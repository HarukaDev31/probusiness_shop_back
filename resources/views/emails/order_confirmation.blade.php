<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Pedido confirmado!</title>
    <style>
        body { font-family: Arial, sans-serif; background: #fff; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #fff; padding: 24px 0 0 0; text-align: center; }
        .logo { width: 180px; margin-bottom: 10px; }
        .banner { background: #ff6600; color: #fff; font-size: 2rem; font-weight: bold; padding: 18px 0; }
        .content { padding: 24px; }
        .title { font-size: 1.2rem; font-weight: bold; margin-bottom: 8px; text-align: center; }
        .subtitle { font-size: 1rem; margin-bottom: 18px; text-align: center; }
        .info-table { width: 100%; margin-bottom: 24px; border-collapse: collapse; }
        .info-table td { padding: 4px 0; font-size: 0.98rem; }
        .info-label { color: #888; width: 120px; font-weight: bold; }
        .order-summary { margin-top: 24px; }
        .summary-title { background: #666; color: #fff; padding: 8px; font-weight: bold; font-size: 1rem; }
        .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .summary-table th, .summary-table td { padding: 8px; text-align: left; border-bottom: 1px solid #eee; }
        .summary-table th { background: #f5f5f5; color: #444; font-weight: bold; }
        .summary-table td img { width: 48px; height: 48px; object-fit: cover; border-radius: 4px; }
        .footer { text-align: center; color: #888; font-size: 0.95rem; padding: 16px 0 0 0; }
        .footer-logo { width: 120px; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ $message->embed($logo_header) }}" alt="probusiness" class="logo">
        </div>
        <div class="banner" style="text-align:center;">¡Pedido confirmado!</div>
        <div class="content">
            <div class="title">¡Gracias por tu pedido!</div>
            <div class="subtitle">
                Gracias por enviar tu pedido a través de nuestra plataforma. Queremos confirmarte que lo hemos recibido correctamente.<br><br>
                Muy pronto, uno de nuestros asesores se pondrá en contacto contigo para validar los detalles, resolver cualquier duda y ayudarte con el siguiente paso del proceso.
            </div>
            <table class="info-table">
                <tr>
                    <td class="info-label">Información</td>
                    <td>
                        {{ $customer['fullName'] }}<br>
                        {{ $customer['phone'] }}<br>
                        {{ $customer['email'] }}<br>
                        {{ $customer['address']['province'] }}, {{ $customer['address']['city'] }}, {{ $customer['address']['district'] }}
                    </td>
                </tr>
            </table>
            <div class="order-summary">
                <div class="summary-title">Resumen de pedido</div>
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Descripción</th>
                            <th>Cant.</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order['items'] as $item)
                        <tr>
                            <td><img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"></td>
                            <td><strong>{{ $item['name'] }}</strong></td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>s/.{{ number_format($item['price'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="footer">
                Gracias por confiar en Probusiness, donde conectamos tu negocio con los mejores productos y servicios.<br><br>
                Equipo Probusiness
            </div>
            <footer style="background:#111; padding:24px 0; text-align:left;">
                <img src="{{ $message->embed($logo_footer) }}" alt="probusiness" class="footer-logo" style="display:inline-block; margin-left:24px;">
            </footer>
        </div>
    </div>
</body>
</html> 