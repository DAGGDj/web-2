<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Book;

class BookPolicy
{
    // Ver todos livros
    public function viewAny(User $user): bool
    {
        return true; // Todos podem ver
    }
    
    // Ver um livro
    public function view(User $user, Book $book): bool
    {
        return true; // Todos podem ver
    }
    
    // Criar livro
    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'bibliotecario';
    }
    
    // Atualizar livro
    public function update(User $user, Book $book): bool
    {
        return $user->role === 'admin' || $user->role === 'bibliotecario';
    }
    
    // Deletar livro
    public function delete(User $user, Book $book): bool
    {
        return $user->role === 'admin';
    }
}