<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota de Cobrança - Hotel Paraíso</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .info {
            margin-bottom: 20px;
        }
        .info table {
            width: 100%;
        }
        .info td {
            padding: 5px;
        }
        .items {
            margin-top: 20px;
        }
        .items table {
            width: 100%;
            border-collapse: collapse;
        }
        .items th, .items td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items th {
            background-color: #f2f2f2;
        }
        .total {
            margin-top: 20px;
            text-align: right;
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Hotel Paraíso</h1>
        <p>Nota de Cobrança</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td><strong>Número:</strong> #{{ str_pad($nota->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td><strong>Data de Emissão:</strong> {{ $nota->data_emissao->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Empresa:</strong> {{ $nota->empresa->nome }}</td>
            </tr>
            <tr>
                <td><strong>Email:</strong> {{ $nota->empresa->email ?? '-' }}</td>
                <td><strong>Telefone:</strong> {{ $nota->empresa->telefone ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>NIF:</strong> {{ $nota->empresa->nif ?? '-' }}</td>
                <td><strong>Endereço:</strong> {{ $nota->empresa->endereco ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="info">
        <h3>Dados da Reserva</h3>
        <table>
            <tr>
                <td><strong>Quarto:</strong> {{ $nota->reserva->quarto->numero }} ({{ $nota->reserva->quarto->tipo }})</td>
                <td><strong>Período:</strong> {{ $nota->reserva->data_entrada->format('d/m/Y') }} até {{ $nota->reserva->data_saida->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="items">
        <h3>Itens</h3>
        <table>
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Quantidade</th>
                    <th>Valor Unitário</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Diárias</td>
                    <td>{{ $nota->reserva->data_entrada->diffInDays($nota->reserva->data_saida) }}</td>
                    <td>MZN {{ number_format($nota->reserva->quarto->preco_diaria, 2, ',', '.') }}</td>
                    <td>MZN {{ number_format($nota->reserva->quarto->preco_diaria * $nota->reserva->data_entrada->diffInDays($nota->reserva->data_saida), 2, ',', '.') }}</td>
                </tr>
                @foreach($nota->reserva->servicos as $rs)
                <tr>
                    <td>{{ $rs->servico->nome }}</td>
                    <td>{{ $rs->quantidade }}</td>
                    <td>MZN {{ number_format($rs->servico->preco, 2, ',', '.') }}</td>
                    <td>MZN {{ number_format($rs->subtotal, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="total">
        <p>Total: MZN {{ number_format($nota->valor_total, 2, ',', '.') }}</p>
    </div>
</body>
</html>


