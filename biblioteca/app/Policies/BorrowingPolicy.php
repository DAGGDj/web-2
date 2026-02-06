<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Borrowing;
use Illuminate\Auth\Access\HandlesAuthorization;

class BorrowingPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário pode devolver o empréstimo
     */
    public function return(User $user, Borrowing $borrowing): bool
{
    // O usuário pode devolver se:
    // 1. Ele é o dono do empréstimo (cliente), OU
    // 2. Ele é um bibliotecário, OU
    // 3. Ele é um administrador
    return $user->id === $borrowing->user_id || 
           $user->role === 'bibliotecario' ||  // ou 'librarian'
           $user->role === 'admin';
}

    /**
     * Determina se o usuário pode ver o empréstimo
     */
    public function view(User $user, Borrowing $borrowing): bool
{
    return $user->id === $borrowing->user_id || 
           $user->role === 'admin' || 
           $user->role === 'bibliotecario';
}

/**
     * Determina se o usuário pode ver a lista de débitos
     */
    public function viewDebts(User $user): bool
    {
        return $user->role === 'bibliotecario' || $user->role === 'admin';
    }

    /**
     * Determina se o usuário pode quitar débitos
     */
    public function payDebt(User $user): bool
    {
        return $user->role === 'bibliotecario' || $user->role === 'admin';
    }

    /**
     * Determina se o usuário pode ajustar débitos manualmente
     */
    public function adjustDebt(User $user): bool
    {
        return $user->role === 'admin'; // Apenas admin pode ajustar manualmente
    }

    /**
     * Determina se o usuário pode ver qualquer empréstimo (para listagens)
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'bibliotecario' || $user->role === 'admin';
    }

    /**
     * Determina se o usuário pode atualizar/gerenciar empréstimos
     */
    public function update(User $user): bool
    {
        return $user->role === 'bibliotecario' || $user->role === 'admin';
    }
    
}