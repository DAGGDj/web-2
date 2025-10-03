<html>
<head></head>
<body>
    
    <h1>Lista de Instrumentos</h1>        
       
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Valor</th>
            <th>Ações</th>
        </tr>
        @foreach($instruments as $instrument)
        <tr>
            <td>{{ $instrument->id }}</td>
            <td>{{ $instrument->name }}</td>
            <td>{{ $instrument->value }}</td>
            <td>
                <!-- Botão de Visualizar -->
                <a href="{{ route('instruments.show', $instrument) }}">
                    Visualizar
                </a>

                <!-- Botão de Editar -->
                <a href="{{ route('instruments.edit', $instrument) }}">
                    Editar
                </a>

                <form action="{{ route('instruments.destroy', $instrument) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button onclick="return confirm('Deseja excluir este instrumento?')">
                        Excluir
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>   

</body>
</html>
