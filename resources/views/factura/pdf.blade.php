<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura de Pago</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; font-size: 14px;}
        table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 12px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        #ti { text-align: center; font-size: 16px; }
        #im { text-align: right; font-size: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/aishap.png') }}" alt="Logo" style="max-height: 60px; width:100%;">
        <p id="ti">Fundación para el Desarrollo Educativo y Social de la Guajira</p>
        <p id="ti"><strong>NIT:</strong> 901691754-7</p>
        <h2>Factura de Pago</h2>
        <p id="im"><strong>Fecha de impresión:</strong> {{ now()->format('d/m/Y') }}</p>
    </div>

    <div class="info-cliente">
        <p><strong>Estudiante:</strong> {{ $name }}</p>
        <p><strong>Documento:</strong> {{ $documento }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Forma Pago</th>
                <th>Fecha de pago</th>
                <th>Por Pagar</th>
                <th>Valor Cancelado</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Pago de matrícula perteneciente al {{ $semestre }} Semestre</td>
                <td>{{ $formapago }}</td>
                <td>{{ $fecha }}</td>
                <td>${{ number_format($deuda, 0, ',', '.') }}</td>
                <td>${{ number_format($cantidad, 0, ',', '.') }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"></td>
                <th>Total:</th>
                <th>${{ number_format($cantidad, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <br><br>
    <div style="text-align:left;">
        <img src="{{ public_path('images/firma.png') }}" alt="Firma" style="width: 100px;">
        <p><strong>Anyelis Deluque Galván</strong></p>
        <p>Representante Legal</p>
    </div>

    <div style="text-align: center; margin-top:5px;">
        <p style="font-size: 9px;"><strong>RESOLUCIÓN 11287 DEL 26 DE AGOSTO DE 2013</strong></p>
        <p style="font-size: 9px;">Cra. 18 No. 21 - 76 | Cel.: 302 752 1119 | fundescguajira@gmail.com</p>
        <p style="font-size: 9px;">Riohacha, La Guajira</p>
    </div>
</body>
</html>
