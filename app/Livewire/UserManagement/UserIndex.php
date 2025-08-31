<?php

namespace App\Livewire\UserManagement;

use Livewire\Component;
use App\Service\UserManagementService;
use App\Models\User;
use Exception;

class UserIndex extends Component
{
    public $search = '';
    public $roleFilter = '';

    public function updatedSearch()
    {
        // Livewire automaticamente re-renderiza quando search muda
    }

    public function updatedRoleFilter()
    {
        // Livewire automaticamente re-renderiza quando roleFilter muda
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->roleFilter = '';
    }

    public function deleteUser($userId)
    {
        try {
            $user = User::findOrFail($userId);

            $userManagementService = app(UserManagementService::class);
            $userManagementService->deleteUser($user);
            session()->flash('success', 'Usuário removido com sucesso!');

        } catch (Exception $e) {
            session()->flash('error', 'Erro ao remover usuário: ' . $e->getMessage());
        }
    }

    public function toggleUserStatus($userId)
    {
        try {
            $user = User::findOrFail($userId);

            $userManagementService = app(UserManagementService::class);
            $updatedUser = $userManagementService->toggleUserStatus($user);

            $message = $updatedUser->active ? 'Usuário ativado com sucesso!' : 'Usuário desativado com sucesso!';
            session()->flash('success', $message);

        } catch (Exception $e) {
            session()->flash('error', 'Erro ao alterar status do usuário: ' . $e->getMessage());
        }
    }

    public function getUsers()
    {
        $userManagementService = app(UserManagementService::class);

        if ($this->search) {
            return $userManagementService->searchUsers($this->search);
        } elseif ($this->roleFilter) {
            return $userManagementService->getUsersByRole($this->roleFilter);
        } else {
            return $userManagementService->getAllUsers();
        }
    }

    public function render()
    {
        return view('livewire.user-management.user-index', [
            'users' => $this->getUsers()
        ]);
    }
}
