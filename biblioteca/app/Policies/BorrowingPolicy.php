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
    
}