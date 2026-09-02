<?php

namespace App\Service;

use App\Mail\PatientAccountCreated;
use App\Models\Address;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class PatientService
{
    /**
     * O termo LGPD contém dados pessoais: fica no disco privado e é servido
     * apenas por rota autenticada (nunca pelo disco "public").
     */
    public const LGPD_TERM_DISK = 'local';

    public const LGPD_TERM_DIRECTORY = 'lgpd-terms';

    public const SEX_OPTIONS = [
        'male' => 'Masculino',
        'female' => 'Feminino',
        'other' => 'Outro',
    ];

    private const LGPD_TERM_MIMES = 'jpg,jpeg,png,webp,pdf';

    private const TEMPORARY_PASSWORD_LENGTH = 12;

    private const LGPD_TERM_MAX_KILOBYTES = 5120;

    private const DIGIT_ONLY_FIELDS = ['cpf', 'phone', 'zip_code'];

    private const PATIENT_FIELDS = ['birthday', 'ethnicity', 'sex', 'cpf', 'rg', 'phone'];

    private const ADDRESS_FIELDS = ['street', 'number', 'complement', 'neighborhood', 'city', 'state', 'country', 'zip_code'];

    public function __construct(private readonly AddressService $addressService) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function getFilteredPatients(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = Patient::with('user')->whereHas('user');

        if (! empty($filters['search'])) {
            $search = (string) $filters['search'];
            $searchDigits = preg_replace('/\D/', '', $search);

            $query->where(function ($q) use ($search, $searchDigits) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });

                if ($searchDigits !== '') {
                    $q->orWhere('cpf', 'like', "%{$searchDigits}%");
                }
            });
        }

        return $query
            ->orderBy(User::select('name')->whereColumn('users.id', 'patients.user_id'))
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Normaliza e valida os dados do paciente (conta, dados pessoais, endereço
     * e termo LGPD). A senha não vem do formulário: é gerada no cadastro e
     * enviada por e-mail. O termo é obrigatório no cadastro e opcional na
     * edição (só substitui o atual quando informado).
     *
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public function validatePatientData(array $input, ?Patient $patient = null): array
    {
        $input = $this->stripDigitFields($input);
        $isCreating = $patient === null;

        $rules = array_merge([
            'name' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($patient?->user_id)],
            'birthday' => ['required', 'date', 'before_or_equal:today'],
            'ethnicity' => ['nullable', 'string', 'regex:/^[\pL\s]+$/u', 'max:50'],
            'sex' => ['required', Rule::in(array_keys(self::SEX_OPTIONS))],
            'cpf' => ['required', 'string', 'size:11', 'cpf', Rule::unique(Patient::class)->ignore($patient?->id)],
            'rg' => ['required', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'size:11'],
            'lgpd_term' => [
                $isCreating ? 'required' : 'nullable',
                'file',
                'mimes:'.self::LGPD_TERM_MIMES,
                'max:'.self::LGPD_TERM_MAX_KILOBYTES,
            ],
        ], $this->addressService->rules());

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Cria a conta (role "patient") com senha temporária, o endereço, o
     * paciente e armazena o termo LGPD. A senha é enviada ao paciente por
     * e-mail somente após a transação ser confirmada.
     *
     * @param  array<string, mixed>  $data  Dados já validados.
     */
    public function createPatient(array $data, UploadedFile $lgpdTerm): Patient
    {
        $temporaryPassword = Str::password(self::TEMPORARY_PASSWORD_LENGTH);

        $patient = DB::transaction(function () use ($data, $lgpdTerm, $temporaryPassword): Patient {
            $user = new User([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($temporaryPassword),
                'email_verified_at' => now(),
            ]);
            $user->role = 'patient';
            $user->active = true;
            $user->save();

            $address = Address::create($this->extractFields($data, self::ADDRESS_FIELDS));

            $patient = Patient::create(array_merge(
                $this->extractFields($data, self::PATIENT_FIELDS),
                [
                    'user_id' => $user->id,
                    'address_id' => $address->id,
                    'lgpd_consent_at' => now(),
                ],
            ));

            $patient->update(['lgpd_term_path' => $this->storeLgpdTerm($lgpdTerm)]);

            return $patient->setRelation('user', $user);
        });

        Mail::to($patient->user->email)->send(new PatientAccountCreated($patient->user, $temporaryPassword));

        return $patient;
    }

    /**
     * @param  array<string, mixed>  $data  Dados já validados.
     */
    public function updatePatient(Patient $patient, array $data, ?UploadedFile $lgpdTerm = null): Patient
    {
        return DB::transaction(function () use ($patient, $data, $lgpdTerm): Patient {
            $user = $patient->user;

            if (! $user) {
                throw new RuntimeException("Paciente #{$patient->id} não possui usuário associado.");
            }

            $user->update(['name' => $data['name'], 'email' => $data['email']]);

            $this->addressService->updateForPatient($patient, $data);

            $patientData = $this->extractFields($data, self::PATIENT_FIELDS);

            if ($lgpdTerm) {
                $this->deleteLgpdTerm($patient);
                $patientData['lgpd_term_path'] = $this->storeLgpdTerm($lgpdTerm);
            }

            $patient->update($patientData);

            $patient->refresh();
            $patient->load(['user', 'address']);

            return $patient;
        });
    }

    private function storeLgpdTerm(UploadedFile $lgpdTerm): string
    {
        $path = $lgpdTerm->store(self::LGPD_TERM_DIRECTORY, self::LGPD_TERM_DISK);

        if ($path === false) {
            throw new RuntimeException('Não foi possível armazenar o termo LGPD no disco privado.');
        }

        return $path;
    }

    private function deleteLgpdTerm(Patient $patient): void
    {
        if ($patient->lgpd_term_path) {
            Storage::disk(self::LGPD_TERM_DISK)->delete($patient->lgpd_term_path);
        }
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    private function stripDigitFields(array $input): array
    {
        foreach (self::DIGIT_ONLY_FIELDS as $field) {
            if (isset($input[$field])) {
                $input[$field] = preg_replace('/\D/', '', (string) $input[$field]);
            }
        }

        return $input;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $fields
     * @return array<string, mixed>
     */
    private function extractFields(array $data, array $fields): array
    {
        $extracted = [];

        foreach ($fields as $field) {
            $extracted[$field] = $data[$field] ?? null;
        }

        return $extracted;
    }
}
