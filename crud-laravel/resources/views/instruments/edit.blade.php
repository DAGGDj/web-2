<html>
<head></head>
<body>
    Editar Instrumento

    <form action="{{ route('instruments.update', $instrument) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div>
            <label for="name">Nome do instrumento</label>
            <input type="text" id="name" name="name" value="{{ $instrument->name }}" required>            
        </div>
        <div>
            <label for="description">Descrição do instrumento</label>
            <input type="text" id="description" name="description" value="{{ $instrument->description }}" required>            
        </div>

        <div>
            <label for="value">Valor do instrumento</label>
            <input type="number" id="value" name="value" value="{{ $instrument->value }}" required>            
        </div>

        <div>
            <label for="expiration_date">Validade</label>
            <input type="date" id="expiration_date" name="expiration_date" value="{{ $instrument->expiration_date }}" required>            
        </div>

        <div>
            <label for="stock">Estoque</label>
            <input type="number" id="stock" name="stock" value="{{ $instrument->stock }}" required>            
        </div>

        <button type="submit">Atualizar</button>
        
    </form>
</body>
</html>
