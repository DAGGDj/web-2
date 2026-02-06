@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Detalhes do Usuário</h1>

    <div class="card">
        <div class="card-header">
            {{ $user->name }}
        </div>
        <div class="card-body">
            <p><strong>Email:</strong> {{ $user->email }}</p>
        </div>
    </div>

    <a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>

@can('viewDebts', App\Models\Borrowing::class)
    <div class="card mt-4 border-danger">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-cash-coin"></i> Gestão de Débitos</h5>
            @if($user->hasDebt())
                <span class="badge bg-warning fs-6">
                    Débito Pendente: R$ {{ number_format($user->debit, 2, ',', '.') }}
                </span>
            @endif
        </div>
        <div class="card-body">
            
            {{-- Resumo do Débito --}}
            <div class="alert {{ $user->hasDebt() ? 'alert-danger' : 'alert-success' }}">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-{{ $user->hasDebt() ? 'exclamation-triangle' : 'check-circle' }}"></i>
                        <strong>
                            @if($user->hasDebt())
                                Usuário possui débito pendente
                            @else
                                Usuário sem débitos
                            @endif
                        </strong>
                    </div>
                    <div>
                        <span class="fs-5">
                            R$ {{ number_format($user->debit, 2, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Botão para quitar débito --}}
            @if($user->hasDebt())
            <div class="mb-4">
                <form action="{{ route('user.pay.debt', $user) }}" 
                      method="PATCH"
                      onsubmit="return confirm('Confirmar quitação do débito de R$ {{ number_format($user->debit, 2, ',', '.') }}?')">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg w-100">
                        <i class="bi bi-cash-stack"></i> Registrar Pagamento e Quitar Débito
                    </button>
                    <small class="text-muted d-block mt-2 text-center">
                        <i class="bi bi-info-circle"></i> Após o pagamento ser realizado presencialmente, clique aqui para zerar o débito no sistema.
                    </small>
                </form>
            </div>
            @endif

            {{-- Histórico de Multas --}}
            @if(isset($borrowingsWithFine) && $borrowingsWithFine->isNotEmpty())
            <h6 class="mt-4 mb-3"><i class="bi bi-clock-history"></i> Histórico de Multas Aplicadas</h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr class="table-secondary">
                            <th>Livro</th>
                            <th>Data Empréstimo</th>
                            <th>Data Devolução</th>
                            <th>Dias Atraso</th>
                            <th>Multa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($borrowingsWithFine as $borrowing)
                        <tr>
                            <td>
                                <a href="{{ route('books.show', $borrowing->book_id) }}">
                                    {{ $borrowing->book->title }}
                                </a>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($borrowing->borrowed_at)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($borrowing->returned_at)->format('d/m/Y') }}</td>
                            <td><span class="badge bg-warning">{{ $borrowing->days_late }} dias</span></td>
                            <td>R$ {{ number_format($borrowing->fine_amount, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    @if($borrowingsWithFine->count() > 1)
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="4" class="text-end"><strong>Total em Multas:</strong></td>
                            <td><strong>R$ {{ number_format($borrowingsWithFine->sum('fine_amount'), 2, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            @endif

            {{-- Livros Atrasados Não Devolvidos --}}
            @if(isset($overdueBorrowings) && $overdueBorrowings->isNotEmpty())
            <h6 class="mt-4 mb-3 text-danger"><i class="bi bi-exclamation-triangle"></i> Livros Atrasados Não Devolvidos</h6>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i>
                Estes livros estão atrasados e acumulando multa diária de R$ 0,50 por dia.
            </div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Livro</th>
                            <th>Data Empréstimo</th>
                            <th>Data Vencimento</th>
                            <th>Dias Atraso</th>
                            <th>Multa Acumulada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($overdueBorrowings as $borrowing)
                        <tr>
                            <td>
                                <a href="{{ route('books.show', $borrowing->book_id) }}">
                                    {{ $borrowing->book->title }}
                                </a>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($borrowing->borrowed_at)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($borrowing->due_date)->format('d/m/Y') }}</td>
                            <td><span class="badge bg-danger">{{ $borrowing->days_late }} dias</span></td>
                            <td>R$ {{ number_format($borrowing->fine_amount, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Ajuste Manual de Débito (apenas admin) --}}
            @can('adjustDebt', App\Models\Borrowing::class)
            <div class="mt-4 p-3 border rounded">
                <h6 class="mb-3"><i class="bi bi-gear"></i> Ajuste Manual de Débito</h6>
                <form action="{{ route('user.adjust.debt', $user) }}" method="PATCH">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Novo Valor</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" 
                                           class="form-control" 
                                           id="amount" 
                                           name="amount" 
                                           step="0.01" 
                                           min="0" 
                                           value="{{ number_format($user->debit, 2, '.', '') }}" 
                                           required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reason" class="form-label">Motivo do Ajuste</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="reason" 
                                       name="reason" 
                                       placeholder="Ex: Pagamento parcial, correção, etc.">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="bi bi-gear"></i> Ajustar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endcan

        </div>
    </div>
    @endcan

<!-- Histórico de Empréstimos -->
<div class="card">
    <div class="card-header">Histórico de Empréstimos</div>
    <div class="card-body">
        @if($user->books->isEmpty())
            <p>Este usuário não possui empréstimos registrados.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Livro</th>
                        <th>Data de Empréstimo</th>
                        <th>Data de Devolução</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
    @foreach($user->books as $book)
        <tr>
            <td>
                <a href="{{ route('books.show', $book->id) }}">
                    {{ $book->title }}
                </a>
            </td>
            <td>{{ $book->pivot->borrowed_at }}</td>
            <td>{{ $book->pivot->returned_at ?? 'Em Aberto' }}</td>
            <td>
                @if(is_null($book->pivot->returned_at))
                    <form action="{{ route('borrowings.return', $book->pivot->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-warning btn-sm">Devolver</button>
                    </form>
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