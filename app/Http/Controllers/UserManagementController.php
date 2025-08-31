<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\UserManagementService;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Exception;

class UserManagementController extends Controller
{
    protected $userManagementService;

    public function __construct(UserManagementService $userManagementService)
    {
        $this->middleware('auth');
        $this->middleware('role:teacher');
        $this->userManagementService = $userManagementService;
    }

    public function index()
    {
        return view('user-management.index-livewire');
    }

    public function create()
    {
        return view('user-management.create-livewire');
    }

    public function store(Request $request)
    {
        try {
            $userType = $request->input('user_type');

            if ($userType === 'teacher') {
                $validatedData = $this->userManagementService->validateTeacherData($request->all());

                $userData = [
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                    'password' => $validatedData['password'],
                ];

                $teacherData = [
                    'registration_number' => $validatedData['registration_number'],
                    'crbm' => $validatedData['crbm'] ?? null,
                ];

                $this->userManagementService->createTeacher($userData, $teacherData);

            } elseif ($userType === 'student') {
                $validatedData = $this->userManagementService->validateStudentData($request->all());

                $userData = [
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                    'password' => $validatedData['password'],
                ];

                $studentData = [
                    'ra' => $validatedData['ra'],
                    'course' => $validatedData['course'],
                ];

                $this->userManagementService->createStudent($userData, $studentData);

            } else {
                return back()->withErrors(['user_type' => 'Tipo de usuário inválido.']);
            }

            return redirect()
                ->route('user-management.index')
                ->with('success', 'Usuário criado com sucesso!');

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao criar usuário: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show($id)
    {
        $user = User::with(['teacher', 'student'])->findOrFail($id);
        return view('user-management.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::with(['teacher', 'student'])->findOrFail($id);
        return view('user-management.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $specificData = [];

            if ($user->role === 'teacher') {
                $validatedData = $this->userManagementService->validateTeacherData($request->all(), $user->id);

                $userData = [
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                ];

                if ($request->filled('password')) {
                    $userData['password'] = $validatedData['password'];
                }

                $specificData = [
                    'registration_number' => $validatedData['registration_number'],
                    'crbm' => $validatedData['crbm'] ?? null,
                ];

            } elseif ($user->role === 'student') {
                $validatedData = $this->userManagementService->validateStudentData($request->all(), $user->id);

                $userData = [
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                ];

                if ($request->filled('password')) {
                    $userData['password'] = $validatedData['password'];
                }

                $specificData = [
                    'ra' => $validatedData['ra'],
                    'course' => $validatedData['course'],
                ];
            } else {
                return back()->withErrors(['error' => 'Tipo de usuário inválido para edição.']);
            }

            $this->userManagementService->updateUser($user, $userData, $specificData);

            return redirect()
                ->route('user-management.index')
                ->with('success', 'Usuário atualizado com sucesso!');

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao atualizar usuário: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            $this->userManagementService->deleteUser($user);

            return redirect()
                ->route('user-management.index')
                ->with('success', 'Usuário removido com sucesso!');

        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao remover usuário: ' . $e->getMessage()]);
        }
    }

    public function generatePassword()
    {
        $password = $this->userManagementService->generateTemporaryPassword();
        return response()->json(['password' => $password]);
    }

    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);

            $updatedUser = $this->userManagementService->toggleUserStatus($user);

            $message = $updatedUser->active ? 'Usuário ativado com sucesso!' : 'Usuário desativado com sucesso!';

            return response()->json([
                'success' => true,
                'message' => $message,
                'active' => $updatedUser->active
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar status do usuário: ' . $e->getMessage()
            ], 500);
        }
    }
}
