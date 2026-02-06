<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BorrowingController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('categories', CategoryController::class);

Route::resource('authors', AuthorController::class);

Route::resource('publishers', PublisherController::class);



// Rotas para criação de livros
Route::get('/books/create-id-number', [BookController::class, 'createWithId'])->name('books.create.id');
Route::post('/books/create-id-number', [BookController::class, 'storeWithId'])->name('books.store.id');

Route::get('/books/create-select', [BookController::class, 'createWithSelect'])->name('books.create.select');
Route::post('/books/create-select', [BookController::class, 'storeWithSelect'])->name('books.store.select');

// Rotas RESTful para index, show, edit, update, delete (tem que ficar depois das rotas /books/create-id-number e /books/create-select)
Route::resource('books', BookController::class)->except(['create', 'store']);


Route::resource('users', UserController::class)->except(['create', 'store', 'destroy']);



// Rota para registrar um empréstimo
Route::post('/books/{book}/borrow', [BorrowingController::class, 'store'])->name('books.borrow');

// Rota para listar o histórico de empréstimos de um usuário
Route::get('/users/{user}/borrowings', [BorrowingController::class, 'userBorrowings'])->name('users.borrowings');

// Rota para registrar a devolução
Route::patch('/borrowings/{borrowing}/return', [BorrowingController::class, 'returnBook'])->name('borrowings.return');

// Rotas para gestão de débitos
Route::middleware(['auth'])->group(function () {
    // Lista de usuários com débito
    Route::get('/users-with-debt', [BorrowingController::class, 'usersWithDebt'])
        ->name('users.with.debt');
    
    // Detalhes do débito de um usuário
    Route::get('/users/{user}/debt-details', [BorrowingController::class, 'userDebtDetails'])
        ->name('user.debt.details');
    
    // Registrar pagamento (zerar débito)
    Route::post('/users/{user}/pay-debt', [BorrowingController::class, 'payDebt'])
        ->name('user.pay.debt');
    
    // Ajustar débito manualmente
    Route::post('/users/{user}/adjust-debt', [BorrowingController::class, 'adjustDebt'])
        ->name('user.adjust.debt'); // ESTA É A ROTA QUE ESTÁ FALTANDO
});