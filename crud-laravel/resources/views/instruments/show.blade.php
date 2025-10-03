<html>
<head></head>
<body>
    
    <h1>Detalhes do Instrumento</h1>        
    <div>
        <p><strong>ID:</strong> {{ $instrument->id }}</p>
        <p><strong>Nome:</strong> {{ $instrument->name }}</p>
        <p><strong>Descrição:</strong> {{ $instrument->description }}</p>
        <p><strong>Valor:</strong> {{ $instrument->value }}</p>
        <p><strong>Validade:</strong> {{ $instrument->expiration_date }}</p>
        <p><strong>Estoque:</strong> {{ $instrument->stock }}</p>
    </div>    

</body>
</html>
