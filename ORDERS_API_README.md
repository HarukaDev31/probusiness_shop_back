# API de Órdenes - Documentación

## Descripción
Esta API permite crear órdenes con validación de precios por cantidad y generación automática de números de orden.

## Endpoint

### POST /api/orders/new

**URL:** `POST /api/orders/new`

**Headers requeridos:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Body de ejemplo:**
```json
{
  "customer": {
    "fullName": "Juan Pérez García",
    "dni": "12345678",
    "email": "juan.perez@email.com",
    "phone": "999888777",
    "address": {
      "province": "Lima",
      "city": "Lima",
      "district": "Miraflores"
    }
  },
  "order": {
    "items": [
      {
        "productId": 123,
        "name": "Camisetas de Algodón Premium",
        "price": 25.50,
        "quantity": 100,
        "total": 2550.00,
        "image": "https://s.alicdn.com/@sc04/kf/Hdd21a08cbb3f4703bd4a37193cd8e8eac.jpg_720x720q50.jpg"
      },
      {
        "productId": 456,
        "name": "Pantalones Deportivos",
        "price": 35.00,
        "quantity": 50,
        "total": 1750.00,
        "image": "https://sc01.alicdn.com/kf/HTB1QqQbXQvoK1RjSZFNq6AxMVXa6.jpg"
      }
    ],
    "total": 4300.00,
    "orderNumber": "250624001",
    "orderDate": "2024-06-25T10:30:00.000Z",
    "status": "pending"
  },
  "metadata": {
    "source": "web",
    "userAgent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36",
    "timestamp": 1719307800000
  }
}
```

## Respuesta exitosa (201)
```json
{
  "message": "Orden creada exitosamente",
  "order_id": "24070900001",
  "order_uuid": "550e8400-e29b-41d4-a716-446655440000",
  "total_amount": 4300.00
}
```

## Respuesta de error (422)
```json
{
  "error": "Error en validación de precios",
  "details": [
    "El precio del producto 'Camisetas de Algodón Premium' no coincide. Enviado: 25.50, Correcto: 23.00",
    "El total del item 'Pantalones Deportivos' no coincide. Enviado: 1750.00, Correcto: 1600.00"
  ]
}
```

## Características

### 1. Validación de Token
- Requiere token de autorización en el header `Authorization`
- El token debe existir en la tabla `users` con el campo `api_token`

### 2. Validación de Precios
- Valida que los precios enviados coincidan con los precios calculados según la cantidad
- Usa el campo `prices` (JSON) de la tabla `catalogo_producto` para determinar precios por cantidad
- Ejemplo de estructura de precios:
```json
{
  "1": 30.00,    // 1-9 unidades
  "10": 28.00,   // 10-49 unidades
  "50": 25.50,   // 50-99 unidades
  "100": 23.00   // 100+ unidades
}
```

### 3. Generación de Número de Orden
- Formato: `YYMES00001`
- YY: Año (24)
- M: Mes (07)
- E: Día (09)
- S: Secuencia de 5 dígitos (00001)

### 4. Estructura de Base de Datos

#### Tabla `orders`
- `id`: ID autoincremental
- `order_number`: Número de orden único
- `uuid`: UUID único de la orden
- `customer_full_name`: Nombre completo del cliente
- `customer_dni`: DNI del cliente
- `customer_email`: Email del cliente
- `customer_phone`: Teléfono del cliente
- `customer_province`: Provincia
- `customer_city`: Ciudad
- `customer_district`: Distrito
- `total_amount`: Monto total de la orden
- `status`: Estado de la orden (pending, processing, shipped, delivered, cancelled)
- `order_date`: Fecha de la orden
- `source`: Fuente de la orden (web, mobile, api)
- `user_agent`: User agent del cliente
- `timestamp`: Timestamp de la orden

#### Tabla `order_items`
- `id`: ID autoincremental
- `order_id`: ID de la orden (foreign key)
- `product_id`: ID del producto (foreign key)
- `product_name`: Nombre del producto al momento de la orden
- `unit_price`: Precio unitario al momento de la orden
- `quantity`: Cantidad
- `total_price`: Total del item al momento de la orden
- `product_image`: URL de la imagen del producto

## Instalación

1. Ejecutar la migración:
```bash
php artisan migrate
```

2. Ejecutar el seeder de productos con precios:
```bash
php artisan db:seed --class=ProductPricingSeeder
```

3. Crear un usuario con token de API:
```sql
INSERT INTO users (name, email, password, api_token, created_at, updated_at) 
VALUES ('Test User', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'test-token-123', NOW(), NOW());
```

## Uso

1. Hacer la petición POST a `/api/orders/new`
2. Incluir el header `Authorization: Bearer {token}`
3. Enviar el body con la estructura especificada
4. El sistema validará precios y devolverá el número de orden generado

## Validaciones

- Token de autorización requerido
- Todos los campos del cliente son obligatorios
- Al menos un item en la orden
- Productos deben existir en la base de datos
- Precios y totales deben coincidir con los calculados
- Cantidades deben ser mayores a 0
- Fecha de orden debe ser válida
- Estado debe ser uno de los permitidos 