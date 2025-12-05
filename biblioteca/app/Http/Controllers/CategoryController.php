<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Exibe uma lista de categorias
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    // Mostra o formulário para criar uma nova categoria
    public function create()
    {
        $this->authorize('create', Category::class);
        
        return view('categories.create');
    }

    // Armazena uma nova categoria no banco de dados
    public function store(Request $request)
    {
        $this->authorize('create', Category::class);
        
        $request->validate([
            'name' => 'required|string|unique:categories|max:255',
        ]);

        Category::create($request->all());

        return redirect()->route('categories.index')->with('success', 'Categoria criada com sucesso.');
    }

    // Exibe uma categoria específica
    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    // Mostra o formulário para editar uma categoria existente
    public function edit(Category $category)
    {
         $this->authorize('update', $category);
        
        return view('categories.edit', compact('category'));
    }

    // Atualiza uma categoria no banco de dados
    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);
        
        $request->validate([
            'name' => 'required|string|unique:categories,name,' . $category->id . '|max:255',
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index')->with('success', 'Categoria atualizada com sucesso.');
    }

    // Remove uma categoria do banco de dados
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Categoria excluída com sucesso.');
    }
}

