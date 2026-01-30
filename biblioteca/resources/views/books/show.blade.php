@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Detalhes do Livro</h1>

    <div class="row">
        <div class="col-md-4 mb-4">
            @if($book->cover_path)
                <img src="{{ $book->cover_url }}" alt="Capa do livro" class="img-fluid rounded shadow" style="max-height: 400px; width: auto;">
            @else
                <div class="bg-light rounded shadow d-flex align-items-center justify-content-center" style="height: 300px; border: 2px dashed #ccc;">
                    <div class="text-center text-muted">
                        <i class="bi bi-image" style="font-size: 3rem;"></i>
                        <p class="mt-2">Sem capa</p>
                    </div>
                </div>
            @endif
        </div>
     <div class="col-md-8">

        <div class="card">
        <div class="card-header">
            <strong>Título:</strong> {{ $book->title }}
        </div>
        <div class="card-body">
            <p><strong>Autor:</strong>
                <a href="{{ route('authors.show', $book->author->id) }}">
                    {{ $book->author->name }}
                </a>
            </p>
            <p><strong>Editora:</strong>
                <a href="{{ route('publishers.show', $book->publisher->id) }}">
                    {{ $book->publisher->name }}
                </a>
            </p>
            <p><strong>Categoria:</strong>
                <a href="{{ route('categories.show', $book->category->id) }}">
                    {{ $book->category->name }}
                </a>
            </p>
        </div>
    </div>

    <a href="{{ route('books.index') }}" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
    </div>
</div>


<!-- Formulário para Empréstimos -->
@if($book->isAvailable())
    <div class="card mb-4">
        <div class="card-header">Registrar Empréstimo</div>
        <div class="card-body">
            <form action="{{ route('books.borrow', $book) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="user_id" class="form-label">Usuário</label>
                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                        <option value="" selected>Selecione um usuário</option>
                        @foreach($users as $user)
                            @php
                                $activeCount = $user->getActiveBorrowingsCount();
                                $canBorrow = $user->canBorrowMoreBooks();
                                $statusClass = $canBorrow ? 'text-success' : 'text-danger';
                                $statusIcon = $canBorrow ? '✓' : '✗';
                            @endphp
                            <option value="{{ $user->id }}" 
                                    data-borrowed="{{ $activeCount }}"
                                    data-canborrow="{{ $canBorrow ? '1' : '0' }}"
                                    class="{{ !$canBorrow ? 'text-danger' : '' }}"
                                    {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} 
                                <small class="{{ $statusClass }}">
                                    ({{ $statusIcon }} {{ $activeCount }}/5 livros)
                                </small>
                            </option>
                        @endforeach
                    </select>
                    
                    <!-- Mensagem de status do usuário selecionado -->
                    <div id="userStatus" class="mt-2 small" style="display: none;">
                        <!-- Será preenchido via JavaScript abaixo -->
                    </div>
                    
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-success">Registrar Empréstimo</button>
            </form>
            
            <!-- Adicione este script no final do formulário -->
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const userSelect = document.getElementById('user_id');
                const userStatus = document.getElementById('userStatus');
                
                function updateUserStatus() {
                    const selectedOption = userSelect.options[userSelect.selectedIndex];
                    
                    if (!selectedOption.value) {
                        userStatus.style.display = 'none';
                        return;
                    }
                    
                    const borrowed = parseInt(selectedOption.getAttribute('data-borrowed'));
                    const canBorrow = selectedOption.getAttribute('data-canborrow') === '1';
                    const remaining = 5 - borrowed;
                    
                    if (canBorrow) {
                        userStatus.innerHTML = `
                            <span class="text-success">
                                <i class="bi bi-check-circle"></i> 
                                Este usuário tem <strong>${borrowed}</strong> livro(s) emprestado(s) 
                                e pode pegar mais <strong>${remaining}</strong> livro(s).
                            </span>
                        `;
                        userStatus.className = 'mt-2 small text-success';
                    } else {
                        userStatus.innerHTML = `
                            <span class="text-danger">
                                <i class="bi bi-exclamation-triangle"></i> 
                                Este usuário já tem <strong>${borrowed}</strong> livro(s) emprestado(s) 
                                e <strong>atingiu o limite máximo</strong> de 5 livros.
                            </span>
                        `;
                        userStatus.className = 'mt-2 small text-danger';
                    }
                    
                    userStatus.style.display = 'block';
                }
                
                // Atualiza status quando o usuário muda a seleção
                userSelect.addEventListener('change', updateUserStatus);
                
                // Atualiza status inicial se já tiver um valor selecionado
                if (userSelect.value) {
                    updateUserStatus();
                }
            });
            </script>
        </div>
    </div>
@else
    <div class="alert alert-warning">
        <strong>Atenção!</strong> Este livro já está emprestado e não pode ser emprestado novamente.
    </div>
@endif

<!-- Histórico de Empréstimos -->
<div class="card">
    <div class="card-header">Histórico de Empréstimos</div>
    <div class="card-body">
        @if($book->users->isEmpty())
            <p>Nenhum empréstimo registrado.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Data de Empréstimo</th>
                        <th>Data de Devolução</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
    @foreach($book->users as $user)
    <tr>
        <td>
            <a href="{{ route('users.show', $user->id) }}">
                {{ $user->name }}
            </a>
        </td>
        <td>{{ $user->pivot->borrowed_at }}</td>
        <td>{{ $user->pivot->returned_at ?? 'Em Aberto' }}</td>
        <td>
            @if(is_null($user->pivot->returned_at))
               @php
            $borrowing = App\Models\Borrowing::find($user->pivot->id);
        @endphp
            
            
            @can('return', $borrowing) <form action="{{ route('borrowings.return', $user->pivot->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-warning btn-sm">Devolver</button>
                </form>
                @endcan
            @endif
        </td>
    </tr>
@endforeach
</tbody>
            </table>
        @endif
    </div>
</div>
@endsection