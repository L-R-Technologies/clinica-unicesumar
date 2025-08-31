<?php

namespace App\Livewire\UserManagement;

use Livewire\Component;
use App\Service\UserManagementService;
use Illuminate\Validation\ValidationException;
use Exception;

class CreateUser extends Component
{
    public $userType = 'teacher';
    public $name = '';
    public $email = '';
    public $password = '';
    public $showPassword = false;

    public $registrationNumber = '';
    public $crbm = '';

    public $ra = '';
    public $course = '';

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ];

        if ($this->userType === 'teacher') {
            $rules['registrationNumber'] = 'required|string|max:10|unique:teachers,registration_number';
            $rules['crbm'] = 'nullable|string|max:10';
        } elseif ($this->userType === 'student') {
            $rules['ra'] = 'required|string|max:9|unique:students,ra';
            $rules['course'] = 'required|string|max:255';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ter um formato válido.',
            'email.unique' => 'Este email já está em uso.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'registrationNumber.required' => 'O número de registro é obrigatório.',
            'registrationNumber.unique' => 'Este número de registro já está em uso.',
            'ra.required' => 'O RA é obrigatório.',
            'ra.unique' => 'Este RA já está em uso.',
            'course.required' => 'O curso é obrigatório.',
        ];
    }

    public function generatePassword()
    {
        $userManagementService = app(UserManagementService::class);
        $this->password = $userManagementService->generateTemporaryPassword();
    }

    public function togglePassword()
    {
        $this->showPassword = !$this->showPassword;
    }

    public function updatedUserType()
    {
        $this->reset(['registrationNumber', 'crbm', 'ra', 'course']);
    }

    public function save()
    {
        $this->validate();

        try {
            $userManagementService = app(UserManagementService::class);

            $userData = [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
            ];

            if ($this->userType === 'teacher') {
                $teacherData = [
                    'registration_number' => $this->registrationNumber,
                    'crbm' => $this->crbm,
                ];

                $userManagementService->createTeacher($userData, $teacherData);
            } elseif ($this->userType === 'student') {
                $studentData = [
                    'ra' => $this->ra,
                    'course' => $this->course,
                ];

                $userManagementService->createStudent($userData, $studentData);
            }

            session()->flash('success', 'Usuário criado com sucesso!');
            return redirect()->route('user-management.index');

        } catch (ValidationException $e) {
            $this->addError('form', 'Erro de validação: ' . implode(', ', $e->errors()));
        } catch (Exception $e) {
            $this->addError('form', 'Erro ao criar usuário: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.user-management.create-user');
    }
}
