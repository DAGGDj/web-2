<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Todos podem ver
    }

    public function view(User $user, Category $category): bool
    {
        return true; // Todos podem ver
    }

    public function create(User $user): bool
    {
        // Admin e bibliotecario podem criar
        return $user->role === 'admin' || $user->role === 'bibliotecario';
    }

    public function update(User $user, Category $category): bool
    {
        // Admin e bibliotecario podem editar
        return $user->role === 'admin' || $user->role === 'bibliotecario';
    }

    public function delete(User $user, Category $category): bool
    {
        // APENAS admin pode deletar
        return $user->role === 'admin';
    }
}