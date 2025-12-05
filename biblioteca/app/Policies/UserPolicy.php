<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // Ver todos usuários
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }
    
    // Ver um usuário
    public function view(User $user, User $model): bool
    {
        return $user->role === 'admin' || $user->id === $model->id;
    }
    
    // Criar usuário
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }
    
    // Atualizar usuário
    public function update(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }
    
    // Deletar usuário
    public function delete(User $user, User $model): bool
    {
        return $user->role === 'admin' && $user->id !== $model->id;
    }
    
    // Método extra: mudar role
    public function changeRole(User $user, User $model): bool
    {
        return $user->role === 'admin' && $user->id !== $model->id;
    }
}