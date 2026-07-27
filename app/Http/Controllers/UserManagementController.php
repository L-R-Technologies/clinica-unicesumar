<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\UserManagementService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class UserManagementController extends Controller
{
    private const USERS_PER_PAGE = 20;

    private const ROLE_OPTIONS = [
        'teacher' => 'Professor',
        'student' => 'Estudante',
    ];

    private const STATUS_OPTIONS = [
        'active' => 'Ativo',
        'inactive' => 'Inativo',
    ];

    protected $userManagementService;

    public function __construct(UserManagementService $userManagementService)
    {
        $this->middleware('auth');
        $this->middleware('role:teacher');
        $this->userManagementService = $userManagementService;
    }

    public function index(Request $request): Response
    {
        $filters = [
            'search' => $request->input('search', ''),
            'role' => $request->input('role', ''),
            'status' => $request->input('status', ''),
        ];

        $users = $this->userManagementService->getFilteredUsers($filters, self::USERS_PER_PAGE);

        return Inertia::render('users/index', [
            'users' => $users,
            'filters' => $filters,
            'roleOptions' => self::ROLE_OPTIONS,
            'statusOptions' => self::STATUS_OPTIONS,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('users/create');
    }

    public function store(Request $request): RedirectResponse
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
                    'professional_license' => $validatedData['professional_license'] ?? null,
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
                    'semester' => $validatedData['semester'],
                ];

                $this->userManagementService->createStudent($userData, $studentData);
            } else {
                return back()->withErrors(['user_type' => 'Tipo de usuário inválido.'])->withInput();
            }

            return redirect()
                ->route('user-management.index')
                ->with('success', 'Usuário criado com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            Log::error('Erro ao criar usuário', ['exception' => $e]);

            return back()
                ->with('error', 'Não foi possível criar o usuário. Tente novamente.')
                ->withInput();
        }
    }

    public function show($id): Response
    {
        $user = User::with(['teacher', 'student'])->findOrFail($id);

        return Inertia::render('users/show', [
            'user' => $user,
        ]);
    }

    public function edit($id): Response
    {
        $user = User::with(['teacher', 'student'])->findOrFail($id);

        return Inertia::render('users/edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
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
                    'professional_license' => $validatedData['professional_license'] ?? null,
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
                    'semester' => $validatedData['semester'],
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
            Log::error('Erro ao atualizar usuário', ['user_id' => $id, 'exception' => $e]);

            return back()
                ->with('error', 'Não foi possível atualizar o usuário. Tente novamente.')
                ->withInput();
        }
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $user = User::findOrFail($id);

            $this->userManagementService->deleteUser($user);

            return redirect()
                ->route('user-management.index')
                ->with('success', 'Usuário removido com sucesso!');
        } catch (Exception $e) {
            Log::error('Erro ao remover usuário', ['user_id' => $id, 'exception' => $e]);

            return back()->with('error', 'Não foi possível remover o usuário. Tente novamente.');
        }
    }

    public function toggleStatus($id): RedirectResponse
    {
        try {
            $user = User::findOrFail($id);

            $updatedUser = $this->userManagementService->toggleUserStatus($user);

            $message = $updatedUser->active ? 'Usuário ativado com sucesso!' : 'Usuário desativado com sucesso!';

            return back()->with('success', $message);
        } catch (Exception $e) {
            Log::error('Erro ao alterar status do usuário', ['user_id' => $id, 'exception' => $e]);

            return back()->with('error', 'Não foi possível alterar o status do usuário. Tente novamente.');
        }
    }
}
