<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Author;

class AuthorPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Todos podem ver
    }

    public function view(User $user, Author $author): bool
    {
        return true; // Todos podem ver
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'bibliotecario';
    }

    public function update(User $user, Author $author): bool
    {
        return $user->role === 'admin' || $user->role === 'bibliotecario';
    }

    public function delete(User $user, Author $author): bool
    {
        return $user->role === 'admin'; // Só admin pode deletar
    }
}