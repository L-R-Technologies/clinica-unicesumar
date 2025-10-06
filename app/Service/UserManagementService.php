<?php

namespace App\Service;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserManagementService
{
    public function getFilteredUsers(array $filters = [])
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

        return $query->orderBy('name')->get();
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
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'role' => 'teacher',
                'email_verified_at' => now(),
                'active' => true,
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'registration_number' => $teacherData['registration_number'],
                'crbm' => $teacherData['crbm'] ?? null,
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
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'role' => 'student',
                'email_verified_at' => now(),
                'active' => true,
            ]);

            Student::create([
                'user_id' => $user->id,
                'ra' => $studentData['ra'],
                'course' => $studentData['course'],
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
                        'crbm' => $specificData['crbm'] ?? $teacher->getAttribute('crbm'),
                    ]);
                }
            } elseif ($user->role === 'student' && ! empty($specificData)) {
                $student = $user->student;
                if ($student) {
                    $student->update([
                        'ra' => $specificData['ra'],
                        'course' => $specificData['course'] ?? $student->getAttribute('course'),
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
        DB::beginTransaction();

        try {
            if ($user->teacher) {
                $user->teacher->delete();
            }

            if ($user->student) {
                $user->student->delete();
            }

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
            $user->update(['active' => ! $user->active]);

            DB::commit();

            return $user->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function generateTemporaryPassword(): string
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 12);
    }

    public function validateTeacherData(array $data, $userId = null): array
    {
        $rules = [
            'name' => 'required|string|regex:/^[\pL\s]+$/u|max:255',
            'email' => 'required|string|email|max:255|unique:users'.($userId ? ",email,{$userId}" : ''),
            'password' => $userId ? 'nullable|string|min:8' : 'required|string|min:8',
            'registration_number' => 'required|string|max:10|unique:teachers,registration_number'.($userId ? ",{$userId},user_id" : ''),
            'crbm' => 'nullable|string|max:10',
        ];

        return validator($data, $rules)->validate();
    }

    public function validateStudentData(array $data, $userId = null): array
    {
        $rules = [
            'name' => 'required|string|regex:/^[\pL\s]+$/u|max:255',
            'email' => 'required|string|email|max:255|unique:users'.($userId ? ",email,{$userId}" : ''),
            'password' => $userId ? 'nullable|string|min:8' : 'required|string|min:8',
            'ra' => 'required|string|max:9|unique:students,ra'.($userId ? ",{$userId},user_id" : ''),
            'course' => 'required|string|max:255',
        ];

        return validator($data, $rules)->validate();
    }
}
