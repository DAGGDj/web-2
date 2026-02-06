<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Book;
use App\Models\Borrowing;

class BorrowingController extends Controller
{
    public function store(Request $request, Book $book)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
    ]);

    if (!$book->isAvailable()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Este livro já está emprestado e não pode ser emprestado novamente.');
        }
        $user = User::find($request->user_id);
        
        if (!$user->canBorrowMoreBooks()) {
            $activeCount = $user->getActiveBorrowingsCount();
            return redirect()->back()
                ->withInput()
                ->with('error', "Este usuário já tem {$activeCount} livro(s) emprestados e atingiu o limite máximo de 5 livros simultâneos.");
        }

        // Verifica se o usuário possui débito pendente
        if ($user->hasDebt()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Este usuário possui débito pendente de R$ ' . 
                       number_format($user->debit, 2, ',', '.') . 
                       '. O empréstimo não pode ser realizado até que o débito seja quitado.');
        }

    Borrowing::create([
        'user_id' => $request->user_id,
        'book_id' => $book->id,
        'borrowed_at' => now(),
    ]);

    return redirect()->route('books.show', $book)->with('success', 'Empréstimo registrado com sucesso.');
}
public function returnBook(Borrowing $borrowing)
{
    $this->authorize('return', $borrowing);
    
    $borrowing->update([
        'returned_at' => now(),
    ]);

    // Verifica se houve atraso na devolução
        if ($borrowing->is_overdue) {
            $fineAmount = $borrowing->fine_amount;
            
            // Adiciona a multa ao débito do usuário
            $borrowing->user->addDebt($fineAmount);
            
            // Mensagem informando sobre a multa
            $message = "Devolução registrada com sucesso. ";
            $message .= "Multa aplicada: R$ " . number_format($fineAmount, 2, ',', '.') . 
                       " (" . $borrowing->days_late . " dia(s) de atraso). ";
            $message .= "Débito total do usuário: R$ " . 
                       number_format($borrowing->user->debit, 2, ',', '.') . ".";
            
            return redirect()->route('books.show', $borrowing->book_id)
                ->with('warning', $message);
        }

    return redirect()->route('books.show', $borrowing->book_id)->with('success', 'Devolução registrada com sucesso.');
}
public function userBorrowings(User $user)
{
    $this->authorize('viewBorrowings', $user);
    
    $borrowings = $user->books()->withPivot('borrowed_at', 'returned_at')->get();

    return view('users.borrowings', compact('user', 'borrowings'));
}

public function usersWithDebt()
    {
        // Apenas bibliotecários e admins podem acessar
        $this->authorize('viewDebts', Borrowing::class);
        
        $users = User::where('debit', '>', 0)
                     ->orderBy('debit', 'desc')
                     ->get();
        
        return view('borrowings.users-with-debt', compact('users'));
    }

    
    public function userDebtDetails(User $user)
    {
        $this->authorize('viewDebts', Borrowing::class);
        
        // Busca todos os empréstimos do usuário que geraram multa
        $borrowingsWithFine = $user->borrowings()
            ->whereNotNull('returned_at')
            ->get()
            ->filter(function($borrowing) {
                return $borrowing->has_fine;
            });
        
        // Empréstimos atrasados não devolvidos
        $overdueBorrowings = $user->overdueBorrowings();
        
        return view('borrowings.user-debt-details', compact('user', 'borrowingsWithFine', 'overdueBorrowings'));
    }

    
    public function payDebt(User $user)
    {
        $this->authorize('payDebt', Borrowing::class);
        
        // Salva o valor antes de zerar para mostrar na mensagem
        $debtAmount = $user->debit;
        
        // Zera o débito do usuário
        $user->clearDebt();
        
        return redirect()->route('users.with.debt')
            ->with('success', 'Débito de R$ ' . number_format($debtAmount, 2, ',', '.') . 
                   ' quitado para o usuário ' . $user->name . '.');
    }

    
    public function adjustDebt(Request $request, User $user)
    {
        $this->authorize('adjustDebt', Borrowing::class);
        
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:255',
        ]);
        
        $oldDebt = $user->debit;
        $user->debit = $request->amount;
        $user->save();
        
        return redirect()->route('user.debt.details', $user)
            ->with('success', 'Débito ajustado de R$ ' . number_format($oldDebt, 2, ',', '.') . 
                   ' para R$ ' . number_format($user->debit, 2, ',', '.') . '.');
    }


}
