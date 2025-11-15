<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Publisher;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    // Formulário com input de ID
    public function createWithId()
    {
        return view('books.create-id');
    }

    // Salvar livro com input de ID
    public function storeWithId(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

         $bookData = $request->except('cover');
        
         if ($request->hasFile('cover')) {
         Storage::disk('public')->makeDirectory('book-covers');
    
         $bookData['cover_path'] = $request->file('cover')->store('book-covers', 'public');
        }

        Book::create($bookData);

        return redirect()->route('books.index')->with('success', 'Livro criado com sucesso.');
    }

    // Formulário com input select
    public function createWithSelect()
    {
        $publishers = Publisher::all();
        $authors = Author::all();
        $categories = Category::all();

        return view('books.create-select', compact('publishers', 'authors', 'categories'));
    }

    // Salvar livro com input select
    public function storeWithSelect(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $bookData = $request->except('cover');

        if ($request->hasFile('cover')) {
         Storage::disk('public')->makeDirectory('book-covers');
    
         $bookData['cover_path'] = $request->file('cover')->store('book-covers', 'public');
        }
        Book::create($bookData);

        return redirect()->route('books.index')->with('success', 'Livro criado com sucesso.');
    }

    // Editar livro
    public function edit(Book $book)
    {
        $publishers = Publisher::all();
        $authors = Author::all();
        $categories = Category::all();

        return view('books.edit', compact('book', 'publishers', 'authors', 'categories'));
    }

    public function update(Request $request, Book $book)
{

    $request->validate([
        'title' => 'required|string|max:255',
        'publisher_id' => 'required|exists:publishers,id',
        'author_id' => 'required|exists:authors,id',
        'category_id' => 'required|exists:categories,id',
        'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $updateData = $request->except('cover','remove_cover');
    $oldCoverPath = $book->cover_path;

    if ($request->has('remove_cover') && $request->remove_cover == '1') {
        $updateData['cover_path'] = null;
    }

    else if ($request->hasFile('cover')) {
         Storage::disk('public')->makeDirectory('book-covers');
    
         $updateData['cover_path'] = $request->file('cover')->store('book-covers', 'public');
          
        }
    $book->update($updateData);
    
    if (($request->hasFile('cover') || $request->has('remove_cover')) && $oldCoverPath && Storage::disk('public')->exists($oldCoverPath)) {
        Storage::disk('public')->delete($oldCoverPath);
    }

    return redirect()->route('books.index')->with('success', 'Livro atualizado com sucesso.');
}

public function index()
{
    // Carregar os livros com autores usando eager loading e paginação
    $books = Book::with('author')->paginate(20);

    return view('books.index', compact('books'));

    

}

public function show(Book $book)
{
    // Carregando autor, editora e categoria do livro com eager loading
    $book->load(['author', 'publisher', 'category']);

    // Carregar todos os usuários para o formulário de empréstimo
    $users = User::all();

    return view('books.show', compact('book','users'));
}

public function destroy(Book $book)
{
    if ($book->cover_path && Storage::disk('public')->exists($book->cover_path)) {
        Storage::disk('public')->delete($book->cover_path);
    }
    
    $book->delete();
    return redirect()->route('books.index')->with('success', 'Livro excluído!');
}

}
