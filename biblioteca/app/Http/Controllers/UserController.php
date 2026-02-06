<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Policies\UserPolicy;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);
        
        $users = \App\Models\User::paginate(10); // Paginação para 10 usuários por página
        return view('users.index', compact('users'));
    }

    public function show(\App\Models\User $user)
    {
         $user->load('books');
         $this->authorize('view', $user);
        
        // Carrega informações de débitos e empréstimos
    $borrowingsWithFine = $user->borrowings()
        ->whereNotNull('returned_at')
        ->get()
        ->filter(function($borrowing) {
            return $borrowing->has_fine;
        });
    
    $overdueBorrowings = $user->getOverdueBorrowings();
    $activeBorrowings = $user->borrowings()->whereNull('returned_at')->get();
    
    return view('users.show', compact('user', 'borrowingsWithFine', 'overdueBorrowings', 'activeBorrowings'));
    }

    public function edit(\App\Models\User $user)
    {
        $this->authorize('update', $user);
        
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user) {
        $this->authorize('update', $user);
        
        
        $data = $request->only('name', 'email');
        
        
        if (auth()->user()->role === 'admin' && $request->has('role')) {
            
            $request->validate([
                'role' => 'required|in:admin,bibliotecario,cliente'
            ]);
            
            
            $this->authorize('changeRole', $user);
            
            $data['role'] = $request->role;
        }
        
        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.');
    }
    
    
    public function updateRole(Request $request, User $user)
    {
        
        $this->authorize('changeRole', $user);
        
        $request->validate([
            'role' => 'required|in:admin,bibliotecario,cliente'
        ]);
        
        $user->update(['role' => $request->role]);
        
        return redirect()->route('users.index')
            ->with('success', 'Papel do usuário atualizado com sucesso.');
    }

}