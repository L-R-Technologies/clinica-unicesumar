<?php

namespace App\Service;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserManagementService
{
    public function getFilteredUsers(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = User::with(['teacher', 'student'])
            ->whereIn('role', ['teacher', 'student'])
            ->where('id', '!=', Auth::id());

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if (! empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (! empty($filters['status'])) {
            $active = $filters['status'] === 'active' ? 1 : 0;
            $query->where('active', $active);
        }

        return $query->orderBy('name')->paginate($perPage)->withQueryString();
    }

    public function getUsersByRole(string $role)
    {
        $query = User::where('role', $role)
            ->where('id', '!=', Auth::id());

        if ($role === 'teacher') {
            $query->with('teacher');
        } elseif ($role === 'student') {
            $query->with('student');
        }

        return $query->orderBy('name')->get();
    }

    public function createTeacher(array $userData, array $teacherData)
    {
        $this->validateTeacherData(array_merge($userData, $teacherData));

        DB::beginTransaction();

        try {
            $user = new User([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'email_verified_at' => now(),
            ]);
            $user->role = 'teacher';
            $user->active = true;
            $user->save();

            Teacher::create([
                'user_id' => $user->id,
                'registration_number' => $teacherData['registration_number'],
                'professional_license' => $teacherData['professional_license'] ?? null,
            ]);

            DB::commit();

            return $user->load('teacher');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function createStudent(array $userData, array $studentData)
    {
        $this->validateStudentData(array_merge($userData, $studentData));

        DB::beginTransaction();

        try {
            $user = new User([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'email_verified_at' => now(),
            ]);
            $user->role = 'student';
            $user->active = true;
            $user->save();

            Student::create([
                'user_id' => $user->id,
                'ra' => $studentData['ra'],
                'course' => $studentData['course'],
                'semester' => $studentData['semester'],
            ]);

            DB::commit();

            return $user->load('student');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateUser(User $user, array $userData, array $specificData = [])
    {
        DB::beginTransaction();

        try {
            $updateData = [
                'name' => $userData['name'],
                'email' => $userData['email'],
            ];

            if (! empty($userData['password'])) {
                $updateData['password'] = Hash::make($userData['password']);
            }

            $user->update($updateData);

            if ($user->role === 'teacher' && ! empty($specificData)) {
                $teacher = $user->teacher;
                if ($teacher) {
                    $teacher->update([
                        'registration_number' => $specificData['registration_number'],
                        'professional_license' => $specificData['professional_license'] ?? null,
                    ]);
                }
            } elseif ($user->role === 'student' && ! empty($specificData)) {
                $student = $user->student;
                if ($student) {
                    $student->update([
                        'ra' => $specificData['ra'],
                        'course' => $specificData['course'],
                        'semester' => $specificData['semester'],
                    ]);
                }
            }

            DB::commit();

            return $user->fresh()->load(['teacher', 'student']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteUser(User $user)
    {
        // ERS (UC013): usuários não são excluídos permanentemente, apenas
        // desativados/removidos de forma reversível. Soft delete preserva o
        // registro (e o perfil teacher/student) para eventual restauração.
        DB::beginTransaction();

        try {
            $user->delete();

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function searchUsers(string $search)
    {
        return User::with(['teacher', 'student'])
            ->whereIn('role', ['teacher', 'student'])
            ->where('id', '!=', Auth::id())
            ->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            })
            ->orderBy('name')
            ->get();
    }

    public function toggleUserStatus(User $user)
    {
        DB::beginTransaction();

        try {
            $user->active = ! $user->active;
            $user->save();

            DB::commit();

            return $user->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function generateTemporaryPassword(): string
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $digits = '0123456789';
        $alphabet = $lowercase.$uppercase.$digits;

        // Garante os requisitos de Password::default(): ao menos uma
        // minúscula, uma maiúscula e um número.
        $password = $lowercase[random_int(0, strlen($lowercase) - 1)]
            .$uppercase[random_int(0, strlen($uppercase) - 1)]
            .$digits[random_int(0, strlen($digits) - 1)];

        for ($i = strlen($password); $i < 12; $i++) {
            $password .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return str_shuffle($password);
    }

    public function validateTeacherData(array $data, $userId = null): array
    {
        $rules = [
            'name' => 'required|string|regex:/^[\pL\s]+$/u|max:255',
            'email' => 'required|string|email|max:255|unique:users'.($userId ? ",email,{$userId}" : ''),
            'password' => $userId
                ? ['nullable', 'string', Password::default()]
                : ['required', 'string', Password::default()],
            'registration_number' => 'required|string|max:10|unique:teachers,registration_number'.($userId ? ",{$userId},user_id" : ''),
            'professional_license' => 'nullable|string|max:10',
        ];

        return validator($data, $rules)->validate();
    }

    public function validateStudentData(array $data, $userId = null): array
    {
        $rules = [
            'name' => 'required|string|regex:/^[\pL\s]+$/u|max:255',
            'email' => 'required|string|email|max:255|unique:users'.($userId ? ",email,{$userId}" : ''),
            'password' => $userId
                ? ['nullable', 'string', Password::default()]
                : ['required', 'string', Password::default()],
            'ra' => 'required|string|max:9|unique:students,ra'.($userId ? ",{$userId},user_id" : ''),
            'course' => 'required|string|max:255',
            'semester' => 'required|integer|min:1|max:20',
        ];

        return validator($data, $rules)->validate();
    }
}
