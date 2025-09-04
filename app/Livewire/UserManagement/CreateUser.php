<?php

namespace App\Livewire\UserManagement;

use App\Service\UserManagementService;
use Exception;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

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

    public function generatePassword()
    {
        $userManagementService = app(UserManagementService::class);
        $this->password = $userManagementService->generateTemporaryPassword();
    }

    public function togglePassword()
    {
        $this->showPassword = ! $this->showPassword;
    }

    public function updatedUserType()
    {
        $this->reset(['registrationNumber', 'crbm', 'ra', 'course']);
    }

    public function save()
    {
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
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (Exception $e) {
            $this->addError('form', 'Erro ao criar usuário: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.user-management.create-user');
    }
}
