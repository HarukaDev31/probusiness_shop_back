<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class TruncateAllProductTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Desactivar verificación de claves foráneas temporalmente
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        
        // Truncar tabla de productos por scrapear
        if (Schema::hasTable('products_to_scrapping')) {
            DB::table('products_to_scrapping')->truncate();
        }
        
        // Truncar tabla de media de productos
        if (Schema::hasTable('catalogo_producto_media')) {
            DB::table('catalogo_producto_media')->truncate();
        }
        
        // Truncar tabla de wishlists (relacionada con productos)
        if (Schema::hasTable('wishlists')) {
            DB::table('wishlists')->truncate();
        }
        
        // Eliminar solo productos con estado "EN TIENDA" de la tabla catalogo_producto
        if (Schema::hasTable('catalogo_producto')) {
            $deletedCount = DB::table('catalogo_producto')
                ->where('status', 'EN TIENDA')
                ->delete();
            
            \Log::info("Se eliminaron {$deletedCount} productos con estado 'EN TIENDA' de catalogo_producto");
        }
        
        // Reactivar verificación de claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        
        // Log de la operación
        \Log::info('Todas las tablas de productos han sido limpiadas exitosamente');
        
        // Insertar datos de productos
        DB::statement("INSERT INTO products_to_scrapping (category_id, name, status, created_at, updated_at) VALUES
(1, 'Pañales desechables talla recién nacido', 'pending', '2025-01-15 10:00:00', '2025-01-15 10:00:00'),
(1, 'Biberón anticólico 250ml', 'pending', '2025-01-15 10:05:00', '2025-01-15 10:05:00'),
(1, 'Chupete ortodóntico silicona', 'pending', '2025-01-15 10:10:00', '2025-01-15 10:10:00'),
(1, 'Cuna convertible en cama', 'pending', '2025-01-15 10:15:00', '2025-01-15 10:15:00'),
(1, 'Silla de coche grupo 0+', 'pending', '2025-01-15 10:20:00', '2025-01-15 10:20:00'),
(1, 'Cochecito de bebé 3 en 1', 'pending', '2025-01-15 10:25:00', '2025-01-15 10:25:00'),
(1, 'Moisés con ruedas', 'pending', '2025-01-15 10:30:00', '2025-01-15 10:30:00'),
(1, 'Cambiador portátil', 'pending', '2025-01-15 10:35:00', '2025-01-15 10:35:00'),
(1, 'Body manga larga algodón', 'pending', '2025-01-15 10:40:00', '2025-01-15 10:40:00'),
(1, 'Pijama bebé estampado', 'pending', '2025-01-15 10:45:00', '2025-01-15 10:45:00'),
(1, 'Babero impermeable', 'pending', '2025-01-15 10:50:00', '2025-01-15 10:50:00'),
(1, 'Toalla con capucha', 'pending', '2025-01-15 10:55:00', '2025-01-15 10:55:00'),
(1, 'Sonajero musical', 'pending', '2025-01-15 11:00:00', '2025-01-15 11:00:00'),
(1, 'Mordedor refrigerante', 'pending', '2025-01-15 11:05:00', '2025-01-15 11:05:00'),
(1, 'Peluche suave antialérgico', 'pending', '2025-01-15 11:10:00', '2025-01-15 11:10:00'),
(1, 'Mantita de arrullo', 'pending', '2025-01-15 11:15:00', '2025-01-15 11:15:00'),
(1, 'Alfombra de juegos', 'pending', '2025-01-15 11:20:00', '2025-01-15 11:20:00'),
(1, 'Hamaca mecedora', 'pending', '2025-01-15 11:25:00', '2025-01-15 11:25:00'),
(1, 'Bañera anatómica', 'pending', '2025-01-15 11:30:00', '2025-01-15 11:30:00'),
(1, 'Termómetro digital', 'pending', '2025-01-15 11:35:00', '2025-01-15 11:35:00'),
(1, 'Aspirador nasal', 'pending', '2025-01-15 11:40:00', '2025-01-15 11:40:00'),
(1, 'Crema protectora', 'pending', '2025-01-15 11:45:00', '2025-01-15 11:45:00'),
(1, 'Champú sin lágrimas', 'pending', '2025-01-15 11:50:00', '2025-01-15 11:50:00'),
(1, 'Loción hidratante', 'pending', '2025-01-15 11:55:00', '2025-01-15 11:55:00'),
(1, 'Monitor de bebé con cámara', 'pending', '2025-01-15 12:00:00', '2025-01-15 12:00:00'),
(1, 'Esterilizador de biberones', 'pending', '2025-01-15 12:05:00', '2025-01-15 12:05:00'),
(1, 'Calienta biberones', 'pending', '2025-01-15 12:10:00', '2025-01-15 12:10:00'),
(1, 'Saco de dormir', 'pending', '2025-01-15 12:15:00', '2025-01-15 12:15:00'),
(1, 'Protector de cuna', 'pending', '2025-01-15 12:20:00', '2025-01-15 12:20:00'),
(1, 'Móvil musical', 'pending', '2025-01-15 12:25:00', '2025-01-15 12:25:00'),
(1, 'Lámpara de noche', 'pending', '2025-01-15 12:30:00', '2025-01-15 12:30:00'),
(1, 'Andador con juguetes', 'pending', '2025-01-15 12:35:00', '2025-01-15 12:35:00'),
(1, 'Trona evolutiva', 'pending', '2025-01-15 12:40:00', '2025-01-15 12:40:00'),
(1, 'Vaso antiderrame', 'pending', '2025-01-15 12:45:00', '2025-01-15 12:45:00'),
(1, 'Cucharas de silicona', 'pending', '2025-01-15 12:50:00', '2025-01-15 12:50:00'),
(1, 'Plato con ventosa', 'pending', '2025-01-15 12:55:00', '2025-01-15 12:55:00'),
(1, 'Portabebés ergonómico', 'pending', '2025-01-15 13:00:00', '2025-01-15 13:00:00'),
(1, 'Bolsa de maternidad', 'pending', '2025-01-15 13:05:00', '2025-01-15 13:05:00'),
(1, 'Intercomunicador', 'pending', '2025-01-15 13:10:00', '2025-01-15 13:10:00'),
(1, 'Humidificador ultrasónico', 'pending', '2025-01-15 13:15:00', '2025-01-15 13:15:00'),
(1, 'Zapatos primeros pasos', 'pending', '2025-01-15 13:20:00', '2025-01-15 13:20:00'),
(1, 'Calcetines antideslizantes', 'pending', '2025-01-15 13:25:00', '2025-01-15 13:25:00'),
(1, 'Gorro de punto', 'pending', '2025-01-15 13:30:00', '2025-01-15 13:30:00'),
(1, 'Manoplas protectoras', 'pending', '2025-01-15 13:35:00', '2025-01-15 13:35:00'),
(1, 'Pulsera de identificación', 'pending', '2025-01-15 13:40:00', '2025-01-15 13:40:00'),
(1, 'Cepillo y peine suave', 'pending', '2025-01-15 13:45:00', '2025-01-15 13:45:00'),
(1, 'Cortauñas especial bebé', 'pending', '2025-01-15 13:50:00', '2025-01-15 13:50:00'),
(1, 'Protector solar bebé', 'pending', '2025-01-15 13:55:00', '2025-01-15 13:55:00'),
(1, 'Parque infantil plegable', 'pending', '2025-01-15 14:00:00', '2025-01-15 14:00:00'),
(1, 'Libro de tela sensorial', 'pending', '2025-01-15 14:05:00', '2025-01-15 14:05:00'),

-- ===== CATEGORÍA: TECNOLOGÍA (category_id: 2) =====
(2, 'Smartphone 5G 128GB', 'pending', '2025-01-15 14:10:00', '2025-01-15 14:10:00'),
(2, 'Laptop 15.6\" Intel i7', 'pending', '2025-01-15 14:15:00', '2025-01-15 14:15:00'),
(2, 'Tablet 10\" con stylus', 'pending', '2025-01-15 14:20:00', '2025-01-15 14:20:00'),
(2, 'Smartwatch deportivo', 'pending', '2025-01-15 14:25:00', '2025-01-15 14:25:00'),
(2, 'Auriculares inalámbricos', 'pending', '2025-01-15 14:30:00', '2025-01-15 14:30:00'),
(2, 'Altavoz Bluetooth portátil', 'pending', '2025-01-15 14:35:00', '2025-01-15 14:35:00'),
(2, 'Cámara digital 4K', 'pending', '2025-01-15 14:40:00', '2025-01-15 14:40:00'),
(2, 'Drone con cámara HD', 'pending', '2025-01-15 14:45:00', '2025-01-15 14:45:00'),
(2, 'Teclado mecánico gaming', 'pending', '2025-01-15 14:50:00', '2025-01-15 14:50:00'),
(2, 'Mouse inalámbrico ergonómico', 'pending', '2025-01-15 14:55:00', '2025-01-15 14:55:00'),
(2, 'Monitor 4K 27 pulgadas', 'pending', '2025-01-15 15:00:00', '2025-01-15 15:00:00'),
(2, 'Impresora multifunción', 'pending', '2025-01-15 15:05:00', '2025-01-15 15:05:00'),
(2, 'Disco duro externo 2TB', 'pending', '2025-01-15 15:10:00', '2025-01-15 15:10:00'),
(2, 'Memoria USB 3.0 64GB', 'pending', '2025-01-15 15:15:00', '2025-01-15 15:15:00'),
(2, 'Cargador inalámbrico rápido', 'pending', '2025-01-15 15:20:00', '2025-01-15 15:20:00'),
(2, 'Power bank 20000mAh', 'pending', '2025-01-15 15:25:00', '2025-01-15 15:25:00'),
(2, 'Cable USB-C 2 metros', 'pending', '2025-01-15 15:30:00', '2025-01-15 15:30:00'),
(2, 'Webcam HD 1080p', 'pending', '2025-01-15 15:35:00', '2025-01-15 15:35:00'),
(2, 'Micrófono condensador USB', 'pending', '2025-01-15 15:40:00', '2025-01-15 15:40:00'),
(2, 'Router WiFi 6 AC3000', 'pending', '2025-01-15 15:45:00', '2025-01-15 15:45:00'),
(2, 'Repetidor WiFi dual band', 'pending', '2025-01-15 15:50:00', '2025-01-15 15:50:00'),
(2, 'Switch ethernet 8 puertos', 'pending', '2025-01-15 15:55:00', '2025-01-15 15:55:00'),
(2, 'Tarjeta gráfica RTX 4060', 'pending', '2025-01-15 16:00:00', '2025-01-15 16:00:00'),
(2, 'Procesador AMD Ryzen 7', 'pending', '2025-01-15 16:05:00', '2025-01-15 16:05:00'),
(2, 'Memoria RAM DDR4 16GB', 'pending', '2025-01-15 16:10:00', '2025-01-15 16:10:00'),
(2, 'SSD NVMe 1TB', 'pending', '2025-01-15 16:15:00', '2025-01-15 16:15:00'),
(2, 'Placa base ATX gaming', 'pending', '2025-01-15 16:20:00', '2025-01-15 16:20:00'),
(2, 'Fuente de alimentación 650W', 'pending', '2025-01-15 16:25:00', '2025-01-15 16:25:00'),
(2, 'Caja PC cristal templado', 'pending', '2025-01-15 16:30:00', '2025-01-15 16:30:00'),
(2, 'Ventilador CPU RGB', 'pending', '2025-01-15 16:35:00', '2025-01-15 16:35:00'),
(2, 'Tarjeta de sonido externa', 'pending', '2025-01-15 16:40:00', '2025-01-15 16:40:00'),
(2, 'Gamepad inalámbrico', 'pending', '2025-01-15 16:45:00', '2025-01-15 16:45:00'),
(2, 'Volante gaming con pedales', 'pending', '2025-01-15 16:50:00', '2025-01-15 16:50:00'),
(2, 'Silla gaming ergonómica', 'pending', '2025-01-15 16:55:00', '2025-01-15 16:55:00'),
(2, 'Soporte para laptop', 'pending', '2025-01-15 17:00:00', '2025-01-15 17:00:00'),
(2, 'Hub USB 3.0 multipuerto', 'pending', '2025-01-15 17:05:00', '2025-01-15 17:05:00'),
(2, 'Adaptador HDMI a USB-C', 'pending', '2025-01-15 17:10:00', '2025-01-15 17:10:00'),
(2, 'Lámpara LED escritorio', 'pending', '2025-01-15 17:15:00', '2025-01-15 17:15:00'),
(2, 'Organizador de cables', 'pending', '2025-01-15 17:20:00', '2025-01-15 17:20:00'),
(2, 'Alfombrilla mouse XXL', 'pending', '2025-01-15 17:25:00', '2025-01-15 17:25:00'),
(2, 'Protector pantalla cristal', 'pending', '2025-01-15 17:30:00', '2025-01-15 17:30:00'),
(2, 'Funda smartphone resistente', 'pending', '2025-01-15 17:35:00', '2025-01-15 17:35:00'),
(2, 'Trípode para cámara', 'pending', '2025-01-15 17:40:00', '2025-01-15 17:40:00'),
(2, 'Gimbal estabilizador', 'pending', '2025-01-15 17:45:00', '2025-01-15 17:45:00'),
(2, 'Luz LED para video', 'pending', '2025-01-15 17:50:00', '2025-01-15 17:50:00'),
(2, 'Lector de tarjetas SD', 'pending', '2025-01-15 17:55:00', '2025-01-15 17:55:00'),
(2, 'Convertidor analógico digital', 'pending', '2025-01-15 18:00:00', '2025-01-15 18:00:00'),
(2, 'Proyector portátil HD', 'pending', '2025-01-15 18:05:00', '2025-01-15 18:05:00'),
(2, 'Pantalla proyección portátil', 'pending', '2025-01-15 18:10:00', '2025-01-15 18:10:00'),
(2, 'Kit limpieza electrónicos', 'pending', '2025-01-15 18:15:00', '2025-01-15 18:15:00'),

-- ===== CATEGORÍA: HOGAR (category_id: 3) =====
(3, 'Sofá 3 plazas gris', 'pending', '2025-01-15 18:20:00', '2025-01-15 18:20:00'),
(3, 'Mesa de centro roble', 'pending', '2025-01-15 18:25:00', '2025-01-15 18:25:00'),
(3, 'Cama matrimonial tapizada', 'pending', '2025-01-15 18:30:00', '2025-01-15 18:30:00'),
(3, 'Colchón viscoelástico', 'pending', '2025-01-15 18:35:00', '2025-01-15 18:35:00'),
(3, 'Almohada cervical', 'pending', '2025-01-15 18:40:00', '2025-01-15 18:40:00'),
(3, 'Juego sábanas algodón', 'pending', '2025-01-15 18:45:00', '2025-01-15 18:45:00'),
(3, 'Edredón nórdico plumas', 'pending', '2025-01-15 18:50:00', '2025-01-15 18:50:00'),
(3, 'Cortinas blackout', 'pending', '2025-01-15 18:55:00', '2025-01-15 18:55:00'),
(3, 'Alfombra persa 2x3m', 'pending', '2025-01-15 19:00:00', '2025-01-15 19:00:00'),
(3, 'Lámpara de pie moderna', 'pending', '2025-01-15 19:05:00', '2025-01-15 19:05:00'),
(3, 'Espejo decorativo redondo', 'pending', '2025-01-15 19:10:00', '2025-01-15 19:10:00'),
(3, 'Cuadro abstracto canvas', 'pending', '2025-01-15 19:15:00', '2025-01-15 19:15:00'),
(3, 'Estantería modular', 'pending', '2025-01-15 19:20:00', '2025-01-15 19:20:00'),
(3, 'Mueble TV blanco', 'pending', '2025-01-15 19:25:00', '2025-01-15 19:25:00'),
(3, 'Sillón reclinable', 'pending', '2025-01-15 19:30:00', '2025-01-15 19:30:00'),
(3, 'Puff almacenaje', 'pending', '2025-01-15 19:35:00', '2025-01-15 19:35:00'),
(3, 'Mesa comedor extensible', 'pending', '2025-01-15 19:40:00', '2025-01-15 19:40:00'),
(3, 'Sillas comedor tapizadas', 'pending', '2025-01-15 19:45:00', '2025-01-15 19:45:00'),
(3, 'Aparador moderno', 'pending', '2025-01-15 19:50:00', '2025-01-15 19:50:00'),
(3, 'Vajilla porcelana 12 servicios', 'pending', '2025-01-15 19:55:00', '2025-01-15 19:55:00'),
(3, 'Cristalería copas vino', 'pending', '2025-01-15 20:00:00', '2025-01-15 20:00:00'),
(3, 'Cubertería acero inoxidable', 'pending', '2025-01-15 20:05:00', '2025-01-15 20:05:00'),
(3, 'Mantel antimanchas', 'pending', '2025-01-15 20:10:00', '2025-01-15 20:10:00'),
(3, 'Servilletas lino', 'pending', '2025-01-15 20:15:00', '2025-01-15 20:15:00'),
(3, 'Cafetera espresso', 'pending', '2025-01-15 20:20:00', '2025-01-15 20:20:00'),
(3, 'Licuadora alta potencia', 'pending', '2025-01-15 20:25:00', '2025-01-15 20:25:00'),
(3, 'Microondas 25L', 'pending', '2025-01-15 20:30:00', '2025-01-15 20:30:00'),
(3, 'Tostadora 4 rebanadas', 'pending', '2025-01-15 20:35:00', '2025-01-15 20:35:00'),
(3, 'Batidora planetaria', 'pending', '2025-01-15 20:40:00', '2025-01-15 20:40:00'),
(3, 'Freidora sin aceite', 'pending', '2025-01-15 20:45:00', '2025-01-15 20:45:00'),
(3, 'Olla programable', 'pending', '2025-01-15 20:50:00', '2025-01-15 20:50:00'),
(3, 'Juego sartenes antiadherentes', 'pending', '2025-01-15 20:55:00', '2025-01-15 20:55:00'),
(3, 'Juego ollas acero inoxidable', 'pending', '2025-01-15 21:00:00', '2025-01-15 21:00:00'),
(3, 'Cuchillos cocina profesional', 'pending', '2025-01-15 21:05:00', '2025-01-15 21:05:00'),
(3, 'Tabla cortar bambú', 'pending', '2025-01-15 21:10:00', '2025-01-15 21:10:00'),
(3, 'Aspiradora robot', 'pending', '2025-01-15 21:15:00', '2025-01-15 21:15:00'),
(3, 'Aspiradora vertical', 'pending', '2025-01-15 21:20:00', '2025-01-15 21:20:00'),
(3, 'Plancha vapor', 'pending', '2025-01-15 21:25:00', '2025-01-15 21:25:00'),
(3, 'Tabla planchar', 'pending', '2025-01-15 21:30:00', '2025-01-15 21:30:00'),
(3, 'Lavadora carga frontal', 'pending', '2025-01-15 21:35:00', '2025-01-15 21:35:00'),
(3, 'Secadora bomba calor', 'pending', '2025-01-15 21:40:00', '2025-01-15 21:40:00'),
(3, 'Lavavajillas 12 servicios', 'pending', '2025-01-15 21:45:00', '2025-01-15 21:45:00'),
(3, 'Refrigeradora 2 puertas', 'pending', '2025-01-15 21:50:00', '2025-01-15 21:50:00'),
(3, 'Congelador vertical', 'pending', '2025-01-15 21:55:00', '2025-01-15 21:55:00'),
(3, 'Aire acondicionado split', 'pending', '2025-01-15 22:00:00', '2025-01-15 22:00:00'),
(3, 'Calefactor eléctrico', 'pending', '2025-01-15 22:05:00', '2025-01-15 22:05:00'),
(3, 'Ventilador techo', 'pending', '2025-01-15 22:10:00', '2025-01-15 22:10:00'),
(3, 'Humidificador ambiente', 'pending', '2025-01-15 22:15:00', '2025-01-15 22:15:00'),
(3, 'Purificador aire HEPA', 'pending', '2025-01-15 22:20:00', '2025-01-15 22:20:00'),
(3, 'Detector humo', 'pending', '2025-01-15 22:25:00', '2025-01-15 22:25:00'),

-- ===== CATEGORÍA: MASCOTAS (category_id: 4) =====
(4, 'Collar ajustable perro', 'pending', '2025-01-15 22:30:00', '2025-01-15 22:30:00'),
(4, 'Correa retráctil 5m', 'pending', '2025-01-15 22:35:00', '2025-01-15 22:35:00'),
(4, 'Arnés acolchado', 'pending', '2025-01-15 22:40:00', '2025-01-15 22:40:00'),
(4, 'Cama ortopédica perro', 'pending', '2025-01-15 22:45:00', '2025-01-15 22:45:00'),
(4, 'Comedero acero inoxidable', 'pending', '2025-01-15 22:50:00', '2025-01-15 22:50:00'),
(4, 'Bebedero automático', 'pending', '2025-01-15 22:55:00', '2025-01-15 22:55:00'),
(4, 'Juguete cuerda dental', 'pending', '2025-01-15 23:00:00', '2025-01-15 23:00:00'),
(4, 'Pelota goma resistente', 'pending', '2025-01-15 23:05:00', '2025-01-15 23:05:00'),
(4, 'Hueso nylon grande', 'pending', '2025-01-15 23:10:00', '2025-01-15 23:10:00'),
(4, 'Ratón catnip gato', 'pending', '2025-01-15 23:15:00', '2025-01-15 23:15:00'),
(4, 'Rascador vertical', 'pending', '2025-01-15 23:20:00', '2025-01-15 23:20:00'),
(4, 'Arena sanitaria gatos', 'pending', '2025-01-15 23:25:00', '2025-01-15 23:25:00'),
(4, 'Bandeja sanitaria', 'pending', '2025-01-15 23:30:00', '2025-01-15 23:30:00'),
(4, 'Pala arena', 'pending', '2025-01-15 23:35:00', '2025-01-15 23:35:00'),
(4, 'Champú antipulgas', 'pending', '2025-01-15 23:40:00', '2025-01-15 23:40:00'),
(4, 'Cepillo desenredante', 'pending', '2025-01-15 23:45:00', '2025-01-15 23:45:00'),
(4, 'Cortauñas mascotas', 'pending', '2025-01-15 23:50:00', '2025-01-15 23:50:00'),
(4, 'Toallitas húmedas', 'pending', '2025-01-15 23:55:00', '2025-01-15 23:55:00'),
(4, 'Vitaminas multivitamínico', 'pending', '2025-01-16 00:00:00', '2025-01-16 00:00:00'),
(4, 'Snacks entrenamiento', 'pending', '2025-01-16 00:05:00', '2025-01-16 00:05:00'),
(4, 'Pienso premium adulto', 'pending', '2025-01-16 00:10:00', '2025-01-16 00:10:00'),
(4, 'Comida húmeda latas', 'pending', '2025-01-16 00:15:00', '2025-01-16 00:15:00'),
(4, 'Transportín viaje', 'pending', '2025-01-16 00:20:00', '2025-01-16 00:20:00'),
(4, 'Asiento coche perro', 'pending', '2025-01-16 00:25:00', '2025-01-16 00:25:00'),
(4, 'Manta térmica', 'pending', '2025-01-16 00:30:00', '2025-01-16 00:30:00'),
(4, 'Caseta exterior', 'pending', '2025-01-16 00:35:00', '2025-01-16 00:35:00'),
(4, 'Puerta para mascotas', 'pending', '2025-01-16 00:40:00', '2025-01-16 00:40:00'),
(4, 'Valla entrenamiento', 'pending', '2025-01-16 00:45:00', '2025-01-16 00:45:00'),
(4, 'Dispensador comida automático', 'pending', '2025-01-16 00:50:00', '2025-01-16 00:50:00'),
(4, 'Fuente agua circulante', 'pending', '2025-01-16 00:55:00', '2025-01-16 00:55:00'),
(4, 'Localizador GPS', 'pending', '2025-01-16 01:00:00', '2025-01-16 01:00:00'),
(4, 'Cámara vigilancia mascotas', 'pending', '2025-01-16 01:05:00', '2025-01-16 01:05:00'),
(4, 'Comedero elevado ajustable', 'pending', '2025-01-16 01:10:00', '2025-01-16 01:10:00'),
(4, 'Escalones cama', 'pending', '2025-01-16 01:15:00', '2025-01-16 01:15:00'),
(4, 'Rampa antideslizante', 'pending', '2025-01-16 01:20:00', '2025-01-16 01:20:00'),
(4, 'Calcetines antideslizantes', 'pending', '2025-01-16 01:25:00', '2025-01-16 01:25:00'),
(4, 'Impermeable lluvia', 'pending', '2025-01-16 01:30:00', '2025-01-16 01:30:00'),
(4, 'Abrigo invierno', 'pending', '2025-01-16 01:35:00', '2025-01-16 01:35:00'),
(4, 'Gafas protectoras sol', 'pending', '2025-01-16 01:40:00', '2025-01-16 01:40:00'),
(4, 'Botiquín primeros auxilios', 'pending', '2025-01-16 01:45:00', '2025-01-16 01:45:00'),
(4, 'Termómetro veterinario', 'pending', '2025-01-16 01:50:00', '2025-01-16 01:50:00'),
(4, 'Vendas elásticas', 'pending', '2025-01-16 01:55:00', '2025-01-16 01:55:00'),
(4, 'Desinfectante heridas', 'pending', '2025-01-16 02:00:00', '2025-01-16 02:00:00'),
(4, 'Juguete interactivo puzzle', 'pending', '2025-01-16 02:05:00', '2025-01-16 02:05:00'),
(4, 'Torre juegos gatos', 'pending', '2025-01-16 02:10:00', '2025-01-16 02:10:00'),
(4, 'Túnel plegable', 'pending', '2025-01-16 02:15:00', '2025-01-16 02:15:00'),
(4, 'Alfombrilla olfativa', 'pending', '2025-01-16 02:20:00', '2025-01-16 02:20:00'),
(4, 'Cepillo eléctrico', 'pending', '2025-01-16 02:25:00', '2025-01-16 02:25:00'),
(4, 'Aspirador pelo mascotas', 'pending', '2025-01-16 02:30:00', '2025-01-16 02:30:00'),

-- ===== CATEGORÍA: JOYAS, GAFAS Y RELOJES (category_id: 5) =====
(5, 'Anillo compromiso oro blanco', 'pending', '2025-01-16 02:35:00', '2025-01-16 02:35:00'),
(5, 'Collar perlas cultivadas', 'pending', '2025-01-16 02:40:00', '2025-01-16 02:40:00'),
(5, 'Pulsera plata esterlina', 'pending', '2025-01-16 02:45:00', '2025-01-16 02:45:00'),
(5, 'Pendientes diamantes', 'pending', '2025-01-16 02:50:00', '2025-01-16 02:50:00'),
(5, 'Reloj automático suizo', 'pending', '2025-01-16 02:55:00', '2025-01-16 02:55:00'),
(5, 'Reloj smartwatch premium', 'pending', '2025-01-16 03:00:00', '2025-01-16 03:00:00'),
(5, 'Gafas sol polarizadas', 'pending', '2025-01-16 03:05:00', '2025-01-16 03:05:00'),
(5, 'Gafas graduadas titanio', 'pending', '2025-01-16 03:10:00', '2025-01-16 03:10:00'),
(5, 'Cadena oro 18k', 'pending', '2025-01-16 03:15:00', '2025-01-16 03:15:00'),
(5, 'Broche vintage', 'pending', '2025-01-16 03:20:00', '2025-01-16 03:20:00'),
(5, 'Gemelos acero inoxidable', 'pending', '2025-01-16 03:25:00', '2025-01-16 03:25:00'),
(5, 'Dije corazón plata', 'pending', '2025-01-16 03:30:00', '2025-01-16 03:30:00'),
(5, 'Piercing titanio', 'pending', '2025-01-16 03:35:00', '2025-01-16 03:35:00'),
(5, 'Tobillera minimalista', 'pending', '2025-01-16 03:40:00', '2025-01-16 03:40:00'),
(5, 'Reloj pared decorativo', 'pending', '2025-01-16 03:45:00', '2025-01-16 03:45:00'),
(5, 'Reloj despertador vintage', 'pending', '2025-01-16 03:50:00', '2025-01-16 03:50:00'),
(5, 'Lupa joyero', 'pending', '2025-01-16 03:55:00', '2025-01-16 03:55:00'),
(5, 'Joyero organizador', 'pending', '2025-01-16 04:00:00', '2025-01-16 04:00:00'),
(5, 'Limpiador joyas ultrasónico', 'pending', '2025-01-16 04:05:00', '2025-01-16 04:05:00'),
(5, 'Paño limpieza gafas', 'pending', '2025-01-16 04:10:00', '2025-01-16 04:10:00'),
(5, 'Cordón gafas deportivo', 'pending', '2025-01-16 04:15:00', '2025-01-16 04:15:00'),
(5, 'Estuche gafas rígido', 'pending', '2025-01-16 04:20:00', '2025-01-16 04:20:00'),
(5, 'Gafas lectura presbicia', 'pending', '2025-01-16 04:25:00', '2025-01-16 04:25:00'),
(5, 'Gafas protección azul', 'pending', '2025-01-16 04:30:00', '2025-01-16 04:30:00'),
(5, 'Monóculo vintage', 'pending', '2025-01-16 04:35:00', '2025-01-16 04:35:00'),
(5, 'Reloj bolsillo clásico', 'pending', '2025-01-16 04:40:00', '2025-01-16 04:40:00'),
(5, 'Cronómetro deportivo', 'pending', '2025-01-16 04:45:00', '2025-01-16 04:45:00'),
(5, 'Pulsera actividad fitness', 'pending', '2025-01-16 04:50:00', '2025-01-16 04:50:00'),
(5, 'Anillo signet personalizado', 'pending', '2025-01-16 04:55:00', '2025-01-16 04:55:00'),
(5, 'Medallón foto', 'pending', '2025-01-16 05:00:00', '2025-01-16 05:00:00'),
(5, 'Pulsera charm plata', 'pending', '2025-01-16 05:05:00', '2025-01-16 05:05:00'),
(5, 'Collar choker moderno', 'pending', '2025-01-16 05:10:00', '2025-01-16 05:10:00'),
(5, 'Pendientes aro grandes', 'pending', '2025-01-16 05:15:00', '2025-01-16 05:15:00'),
(5, 'Alianza matrimonio oro', 'pending', '2025-01-16 05:20:00', '2025-01-16 05:20:00'),
(5, 'Broche corbata', 'pending', '2025-01-16 05:25:00', '2025-01-16 05:25:00'),
(5, 'Pisacorbatas elegante', 'pending', '2025-01-16 05:30:00', '2025-01-16 05:30:00'),
(5, 'Reloj mujer elegante', 'pending', '2025-01-16 05:35:00', '2025-01-16 05:35:00'),
(5, 'Reloj deportivo resistente', 'pending', '2025-01-16 05:40:00', '2025-01-16 05:40:00'),
(5, 'Gafas natación', 'pending', '2025-01-16 05:45:00', '2025-01-16 05:45:00'),
(5, 'Gafas esquí', 'pending', '2025-01-16 05:50:00', '2025-01-16 05:50:00'),
(5, 'Lentes contacto color', 'pending', '2025-01-16 05:55:00', '2025-01-16 05:55:00'),
(5, 'Gafas realidad virtual', 'pending', '2025-01-16 06:00:00', '2025-01-16 06:00:00'),
(5, 'Pendientes magnéticos', 'pending', '2025-01-16 06:05:00', '2025-01-16 06:05:00'),
(5, 'Collar perro identificación', 'pending', '2025-01-16 06:10:00', '2025-01-16 06:10:00'),
(5, 'Reloj ajedrez', 'pending', '2025-01-16 06:15:00', '2025-01-16 06:15:00'),
(5, 'Caja relojes colección', 'pending', '2025-01-16 06:20:00', '2025-01-16 06:20:00'),
(5, 'Lupa relojero', 'pending', '2025-01-16 06:25:00', '2025-01-16 06:25:00'),
(5, 'Kit reparación gafas', 'pending', '2025-01-16 06:30:00', '2025-01-16 06:30:00'),
(5, 'Expositor joyas', 'pending', '2025-01-16 06:35:00', '2025-01-16 06:35:00'),
(5, 'Báscula joyas precisión', 'pending', '2025-01-16 06:40:00', '2025-01-16 06:40:00'),

-- ===== CATEGORÍA: MALETAS, BOLSAS Y FUNDAS (category_id: 6) =====
(6, 'Maleta cabina rígida', 'pending', '2025-01-16 06:45:00', '2025-01-16 06:45:00'),
(6, 'Maleta grande 4 ruedas', 'pending', '2025-01-16 06:50:00', '2025-01-16 06:50:00'),
(6, 'Set 3 maletas expandibles', 'pending', '2025-01-16 06:55:00', '2025-01-16 06:55:00'),
(6, 'Mochila viaje 40L', 'pending', '2025-01-16 07:00:00', '2025-01-16 07:00:00'),
(6, 'Bolsa deporte gimnasio', 'pending', '2025-01-16 07:05:00', '2025-01-16 07:05:00'),
(6, 'Riñonera running', 'pending', '2025-01-16 07:10:00', '2025-01-16 07:10:00'),
(6, 'Neceser toiletry colgante', 'pending', '2025-01-16 07:15:00', '2025-01-16 07:15:00'),
(6, 'Funda laptop 15.6\"', 'pending', '2025-01-16 07:20:00', '2025-01-16 07:20:00'),
(6, 'Mochila portátil ejecutiva', 'pending', '2025-01-16 07:25:00', '2025-01-16 07:25:00'),
(6, 'Bolso mano mujer', 'pending', '2025-01-16 07:30:00', '2025-01-16 07:30:00'),
(6, 'Cartera hombre cuero', 'pending', '2025-01-16 07:35:00', '2025-01-16 07:35:00'),
(6, 'Bandolera crossbody', 'pending', '2025-01-16 07:40:00', '2025-01-16 07:40:00'),
(6, 'Mochila senderismo 60L', 'pending', '2025-01-16 07:45:00', '2025-01-16 07:45:00'),
(6, 'Bolsa playa impermeable', 'pending', '2025-01-16 07:50:00', '2025-01-16 07:50:00'),
(6, 'Funda tablet universal', 'pending', '2025-01-16 07:55:00', '2025-01-16 07:55:00'),
(6, 'Estuche lápices escolar', 'pending', '2025-01-16 08:00:00', '2025-01-16 08:00:00'),
(6, 'Mochila escolar ergonómica', 'pending', '2025-01-16 08:05:00', '2025-01-16 08:05:00'),
(6, 'Portafolios ejecutivo', 'pending', '2025-01-16 08:10:00', '2025-01-16 08:10:00'),
(6, 'Bolsa compras plegable', 'pending', '2025-01-16 08:15:00', '2025-01-16 08:15:00'),
(6, 'Funda smartphone resistente', 'pending', '2025-01-16 08:20:00', '2025-01-16 08:20:00'),
(6, 'Organizador maleta cubos', 'pending', '2025-01-16 08:25:00', '2025-01-16 08:25:00'),
(6, 'Bolsa zapatos viaje', 'pending', '2025-01-16 08:30:00', '2025-01-16 08:30:00'),
(6, 'Funda traje viaje', 'pending', '2025-01-16 08:35:00', '2025-01-16 08:35:00'),
(6, 'Maleta medicinas refrigerada', 'pending', '2025-01-16 08:40:00', '2025-01-16 08:40:00'),
(6, 'Mochila cámara fotográfica', 'pending', '2025-01-16 08:45:00', '2025-01-16 08:45:00'),
(6, 'Bolsa herramientas portátil', 'pending', '2025-01-16 08:50:00', '2025-01-16 08:50:00'),
(6, 'Estuche gafas rígido', 'pending', '2025-01-16 08:55:00', '2025-01-16 08:55:00'),
(6, 'Funda guitarra acolchada', 'pending', '2025-01-16 09:00:00', '2025-01-16 09:00:00'),
(6, 'Bolsa lavandería viaje', 'pending', '2025-01-16 09:05:00', '2025-01-16 09:05:00'),
(6, 'Mochila hidratación', 'pending', '2025-01-16 09:10:00', '2025-01-16 09:10:00'),
(6, 'Billetera tarjetas RFID', 'pending', '2025-01-16 09:15:00', '2025-01-16 09:15:00'),
(6, 'Monedero vintage', 'pending', '2025-01-16 09:20:00', '2025-01-16 09:20:00'),
(6, 'Funda pasaporte', 'pending', '2025-01-16 09:25:00', '2025-01-16 09:25:00'),
(6, 'Organizador documentos', 'pending', '2025-01-16 09:30:00', '2025-01-16 09:30:00'),
(6, 'Bolsa térmica picnic', 'pending', '2025-01-16 09:35:00', '2025-01-16 09:35:00'),
(6, 'Mochila anti-robo', 'pending', '2025-01-16 09:40:00', '2025-01-16 09:40:00'),
(6, 'Funda drone transporte', 'pending', '2025-01-16 09:45:00', '2025-01-16 09:45:00'),
(6, 'Bolsa pañales bebé', 'pending', '2025-01-16 09:50:00', '2025-01-16 09:50:00'),
(6, 'Estuche joyería viaje', 'pending', '2025-01-16 09:55:00', '2025-01-16 09:55:00'),
(6, 'Mochila ruedas trolley', 'pending', '2025-01-16 10:00:00', '2025-01-16 10:00:00'),
(6, 'Bolsa deportes acuáticos', 'pending', '2025-01-16 10:05:00', '2025-01-16 10:05:00'),
(6, 'Funda teclado mecánico', 'pending', '2025-01-16 10:10:00', '2025-01-16 10:10:00'),
(6, 'Portacosméticos organizador', 'pending', '2025-01-16 10:15:00', '2025-01-16 10:15:00'),
(6, 'Maleta piloto profesional', 'pending', '2025-01-16 10:20:00', '2025-01-16 10:20:00'),
(6, 'Funda snowboard', 'pending', '2025-01-16 10:25:00', '2025-01-16 10:25:00'),
(6, 'Bolsa yoga esterilla', 'pending', '2025-01-16 10:30:00', '2025-01-16 10:30:00'),
(6, 'Estuche cables electrónicos', 'pending', '2025-01-16 10:35:00', '2025-01-16 10:35:00'),
(6, 'Mochila táctica militar', 'pending', '2025-01-16 10:40:00', '2025-01-16 10:40:00'),
(6, 'Funda bicicleta transporte', 'pending', '2025-01-16 10:45:00', '2025-01-16 10:45:00'),
(6, 'Organizador coche', 'pending', '2025-01-16 10:50:00', '2025-01-16 10:50:00');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Esta migración no se puede revertir ya que elimina datos permanentemente
        // Se podría implementar un backup antes de eliminar si es necesario
        \Log::warning('No se puede revertir la operación de eliminación de productos');
    }
}
