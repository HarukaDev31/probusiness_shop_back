<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteProductsToScrappingAndEnTiendaProducts extends Migration
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
            Log::info('Tabla products_to_scrapping ha sido truncada completamente');
        }

        // Eliminar productos con estado "EN TIENDA" de la tabla catalogo_producto
        if (Schema::hasTable('catalogo_producto')) {
            $deletedCount = DB::table('catalogo_producto')
                ->where('status', 'EN TIENDA')
                ->delete();

            Log::info("Se eliminaron {$deletedCount} productos con estado 'EN TIENDA' de catalogo_producto");
        }

        // Inserción de productos de ejemplo (puedes cambiar el nombre de la tabla si corresponde)
        // CATEGORÍA 1: BEBES (50 productos)
        DB::statement("INSERT INTO products_to_scrapping (category_id, name, status, created_at, updated_at) VALUES
(1, 'Cuna portátil con mosquitero', 'pending', NOW(), NOW()),
(1, 'Silla de comer alta con bandeja', 'pending', NOW(), NOW()),
(1, 'Cochecito de bebé tres ruedas', 'pending', NOW(), NOW()),
(1, 'Manta térmica para bebé', 'pending', NOW(), NOW()),
(1, 'Termómetro digital infrarrojo', 'pending', NOW(), NOW()),
(1, 'Sonajero musical con luces', 'pending', NOW(), NOW()),
(1, 'Andador con centro de actividades', 'pending', NOW(), NOW()),
(1, 'Canguro ergonómico ajustable', 'pending', NOW(), NOW()),
(1, 'Bañera plegable con indicador temperatura', 'pending', NOW(), NOW()),
(1, 'Monitor de bebé con cámara', 'pending', NOW(), NOW()),
(1, 'Mecedora automática con música', 'pending', NOW(), NOW()),
(1, 'Cambiador portátil impermeable', 'pending', NOW(), NOW()),
(1, 'Saco de dormir para bebé', 'pending', NOW(), NOW()),
(1, 'Esterilizador eléctrico biberones', 'pending', NOW(), NOW()),
(1, 'Humidificador ultrasónico infantil', 'pending', NOW(), NOW()),
(1, 'Almohada anti-reflujo bebé', 'pending', NOW(), NOW()),
(1, 'Peluche interactivo educativo', 'pending', NOW(), NOW()),
(1, 'Corral hexagonal plegable', 'pending', NOW(), NOW()),
(1, 'Luz nocturna proyector estrellas', 'pending', NOW(), NOW()),
(1, 'Intercomunicador digital bidireccional', 'pending', NOW(), NOW()),
(1, 'Asiento de auto convertible', 'pending', NOW(), NOW()),
(1, 'Móvil musical para cuna', 'pending', NOW(), NOW()),
(1, 'Tapete de juego acolchado', 'pending', NOW(), NOW()),
(1, 'Babero impermeable con bolsillo', 'pending', NOW(), NOW()),
(1, 'Calienta toallitas húmedas', 'pending', NOW(), NOW()),
(1, 'Gimnasio de actividades bebé', 'pending', NOW(), NOW()),
(1, 'Almohada de lactancia ergonómica', 'pending', NOW(), NOW()),
(1, 'Organizador pañales y accesorios', 'pending', NOW(), NOW()),
(1, 'Espejo retrovisor auto bebé', 'pending', NOW(), NOW()),
(1, 'Cortaúñas eléctrico seguro', 'pending', NOW(), NOW()),
(1, 'Bolsa maternal con cambiador', 'pending', NOW(), NOW()),
(1, 'Protector solar mineral bebé', 'pending', NOW(), NOW()),
(1, 'Mordedor refrigerante texturizado', 'pending', NOW(), NOW()),
(1, 'Balancín automático con vibración', 'pending', NOW(), NOW()),
(1, 'Plato antideslizante con ventosa', 'pending', NOW(), NOW()),
(1, 'Vaso entrenador con boquilla', 'pending', NOW(), NOW()),
(1, 'Protector cuna malla transpirable', 'pending', NOW(), NOW()),
(1, 'Cepillo dientes silicona suave', 'pending', NOW(), NOW()),
(1, 'Gel limpiador sin lágrimas', 'pending', NOW(), NOW()),
(1, 'Localizador bluetooth bebé', 'pending', NOW(), NOW()),
(1, 'Cojín anti-vuelco seguridad', 'pending', NOW(), NOW()),
(1, 'Dispensador automático pañales', 'pending', NOW(), NOW()),
(1, 'Mochila portabebés montaña', 'pending', NOW(), NOW()),
(1, 'Reductor WC con escalón', 'pending', NOW(), NOW()),
(1, 'Ventilador silencioso cuna', 'pending', NOW(), NOW()),
(1, 'Protector esquinas mesa', 'pending', NOW(), NOW()),
(1, 'Cepillo cabello cerdas naturales', 'pending', NOW(), NOW()),
(1, 'Termómetro baño digital', 'pending', NOW(), NOW()),
(1, 'Organizador juguetes colgante', 'pending', NOW(), NOW()),
(1, 'Alfombra sensorial texturas', 'pending', NOW(), NOW());");

        // CATEGORÍA 2: TECNOLOGIA (50 productos)
        DB::statement("INSERT INTO products_to_scrapping (category_id, name, status, created_at, updated_at) VALUES
(2, 'Auriculares inalámbricos deportivos', 'pending', NOW(), NOW()),
(2, 'Cargador inalámbrico rápido', 'pending', NOW(), NOW()),
(2, 'Smartwatch con GPS integrado', 'pending', NOW(), NOW()),
(2, 'Parlante bluetooth resistente agua', 'pending', NOW(), NOW()),
(2, 'Power bank solar 20000mAh', 'pending', NOW(), NOW()),
(2, 'Cámara web 4K micrófono', 'pending', NOW(), NOW()),
(2, 'Teclado mecánico RGB retroiluminado', 'pending', NOW(), NOW()),
(2, 'Mouse gaming alta precisión', 'pending', NOW(), NOW()),
(2, 'Micrófono condensador profesional', 'pending', NOW(), NOW()),
(2, 'Disco duro externo 2TB', 'pending', NOW(), NOW()),
(2, 'Router wifi 6 dual band', 'pending', NOW(), NOW()),
(2, 'Tablet 10 pulgadas Android', 'pending', NOW(), NOW()),
(2, 'Impresora multifuncional WiFi', 'pending', NOW(), NOW()),
(2, 'Proyector LED portátil', 'pending', NOW(), NOW()),
(2, 'Lentes VR realidad virtual', 'pending', NOW(), NOW()),
(2, 'Drone cámara 4K gimbal', 'pending', NOW(), NOW()),
(2, 'Estación carga múltiple dispositivos', 'pending', NOW(), NOW()),
(2, 'Soporte ajustable notebook', 'pending', NOW(), NOW()),
(2, 'Webcam seguridad nocturna', 'pending', NOW(), NOW()),
(2, 'Adaptador USB-C multipuerto', 'pending', NOW(), NOW()),
(2, 'Lámpara LED escritorio USB', 'pending', NOW(), NOW()),
(2, 'Gamepad inalámbrico universal', 'pending', NOW(), NOW()),
(2, 'Cable HDMI 4K alta velocidad', 'pending', NOW(), NOW()),
(2, 'Ventilador USB silencioso', 'pending', NOW(), NOW()),
(2, 'Convertidor analógico digital', 'pending', NOW(), NOW()),
(2, 'Extensión USB 3.0 activa', 'pending', NOW(), NOW()),
(2, 'Antena WiFi amplificadora', 'pending', NOW(), NOW()),
(2, 'Protector sobretensión inteligente', 'pending', NOW(), NOW()),
(2, 'Grabadora digital portátil', 'pending', NOW(), NOW()),
(2, 'Escáner documentos automático', 'pending', NOW(), NOW()),
(2, 'Brazo articulado monitor', 'pending', NOW(), NOW()),
(2, 'Filtro luz azul monitor', 'pending', NOW(), NOW()),
(2, 'Repetidor señal WiFi', 'pending', NOW(), NOW()),
(2, 'Controlador temperatura inteligente', 'pending', NOW(), NOW()),
(2, 'Sensor movimiento inalámbrico', 'pending', NOW(), NOW()),
(2, 'Lector códigos QR láser', 'pending', NOW(), NOW()),
(2, 'Convertidor señal digital', 'pending', NOW(), NOW()),
(2, 'Multiplexor HDMI automático', 'pending', NOW(), NOW()),
(2, 'Tarjeta captura video', 'pending', NOW(), NOW()),
(2, 'Amplificador WiFi exterior', 'pending', NOW(), NOW()),
(2, 'Interface audio USB profesional', 'pending', NOW(), NOW()),
(2, 'Eliminador ruido activo', 'pending', NOW(), NOW()),
(2, 'Controlador iluminación RGB', 'pending', NOW(), NOW()),
(2, 'Distribuidor señal coaxial', 'pending', NOW(), NOW()),
(2, 'Medidor potencia láser', 'pending', NOW(), NOW()),
(2, 'Analizador espectro portátil', 'pending', NOW(), NOW()),
(2, 'Generador frecuencias digital', 'pending', NOW(), NOW()),
(2, 'Osciloscopio digital mini', 'pending', NOW(), NOW()),
(2, 'Multímetro bluetooth inteligente', 'pending', NOW(), NOW()),
(2, 'Probador cables automático', 'pending', NOW(), NOW());");

        // CATEGORÍA 3: HOGAR (50 productos)
        DB::statement("INSERT INTO products_to_scrapping (category_id, name, status, created_at, updated_at) VALUES
(3, 'Aspiradora robot inteligente', 'pending', NOW(), NOW()),
(3, 'Purificador aire HEPA', 'pending', NOW(), NOW()),
(3, 'Cafetera espresso automática', 'pending', NOW(), NOW()),
(3, 'Freidora aire digital', 'pending', NOW(), NOW()),
(3, 'Batidora alta potencia', 'pending', NOW(), NOW()),
(3, 'Horno microondas inverter', 'pending', NOW(), NOW()),
(3, 'Lavavajillas compacto portátil', 'pending', NOW(), NOW()),
(3, 'Refrigerador mini bar', 'pending', NOW(), NOW()),
(3, 'Plancha vapor vertical', 'pending', NOW(), NOW()),
(3, 'Deshumidificador automático', 'pending', NOW(), NOW()),
(3, 'Cortinas eléctricas motorizadas', 'pending', NOW(), NOW()),
(3, 'Lámpara pie LED regulable', 'pending', NOW(), NOW()),
(3, 'Organizador zapatos giratorio', 'pending', NOW(), NOW()),
(3, 'Espejo baño LED antivaho', 'pending', NOW(), NOW()),
(3, 'Dispensador jabón automático', 'pending', NOW(), NOW()),
(3, 'Calentador agua instantáneo', 'pending', NOW(), NOW()),
(3, 'Ventilador techo control remoto', 'pending', NOW(), NOW()),
(3, 'Alfombra calefacción eléctrica', 'pending', NOW(), NOW()),
(3, 'Cerradura digital huella', 'pending', NOW(), NOW()),
(3, 'Detector humo inteligente', 'pending', NOW(), NOW()),
(3, 'Termostato programable WiFi', 'pending', NOW(), NOW()),
(3, 'Sistema riego automático', 'pending', NOW(), NOW()),
(3, 'Cojines ergonómicos memory foam', 'pending', NOW(), NOW()),
(3, 'Estantería modular ajustable', 'pending', NOW(), NOW()),
(3, 'Mesa centro elevable', 'pending', NOW(), NOW()),
(3, 'Silla oficina ergonómica', 'pending', NOW(), NOW()),
(3, 'Colchón viscoelástico gel', 'pending', NOW(), NOW()),
(3, 'Armario portable tela', 'pending', NOW(), NOW()),
(3, 'Perchero pie madera', 'pending', NOW(), NOW()),
(3, 'Biombo divisor decorativo', 'pending', NOW(), NOW()),
(3, 'Florero cristal soplado', 'pending', NOW(), NOW()),
(3, 'Cuadro canvas abstracto', 'pending', NOW(), NOW()),
(3, 'Reloj pared silencioso', 'pending', NOW(), NOW()),
(3, 'Candelabro metal forjado', 'pending', NOW(), NOW()),
(3, 'Tapete entrada antideslizante', 'pending', NOW(), NOW()),
(3, 'Jardinera vertical interior', 'pending', NOW(), NOW()),
(3, 'Fuente agua relajante', 'pending', NOW(), NOW()),
(3, 'Difusor aromas ultrasónico', 'pending', NOW(), NOW()),
(3, 'Organizador especias giratorio', 'pending', NOW(), NOW()),
(3, 'Tabla cortar bambú', 'pending', NOW(), NOW()),
(3, 'Juego cuchillos cerámicos', 'pending', NOW(), NOW()),
(3, 'Recipientes vidrio herméticos', 'pending', NOW(), NOW()),
(3, 'Moldes silicona repostería', 'pending', NOW(), NOW()),
(3, 'Escurridor platos plegable', 'pending', NOW(), NOW()),
(3, 'Rallador multifuncional acero', 'pending', NOW(), NOW()),
(3, 'Salero pimienta automático', 'pending', NOW(), NOW()),
(3, 'Abrebotellas magnético', 'pending', NOW(), NOW()),
(3, 'Medidores cocina digitales', 'pending', NOW(), NOW()),
(3, 'Filtro agua grifo', 'pending', NOW(), NOW()),
(3, 'Termo café acero inoxidable', 'pending', NOW(), NOW());");

        // CATEGORÍA 4: MASCOTAS (50 productos)
        DB::statement("INSERT INTO products_to_scrapping (category_id, name, status, created_at, updated_at) VALUES
(4, 'Cama ortopédica perro grande', 'pending', NOW(), NOW()),
(4, 'Transportadora avión gato', 'pending', NOW(), NOW()),
(4, 'Bebedero automático circulante', 'pending', NOW(), NOW()),
(4, 'Comedero elevado antideslizante', 'pending', NOW(), NOW()),
(4, 'Juguete interactivo dispensador', 'pending', NOW(), NOW()),
(4, 'Correa extensible reflectante', 'pending', NOW(), NOW()),
(4, 'Collar GPS localizador', 'pending', NOW(), NOW()),
(4, 'Rascador gatos multinivel', 'pending', NOW(), NOW()),
(4, 'Puerta mascota automática', 'pending', NOW(), NOW()),
(4, 'Cepillo autolimpiante pelo', 'pending', NOW(), NOW()),
(4, 'Cortaúñas profesional perros', 'pending', NOW(), NOW()),
(4, 'Bozal ajustable transpirable', 'pending', NOW(), NOW()),
(4, 'Impermeable perro reflectante', 'pending', NOW(), NOW()),
(4, 'Zapatos protectores patas', 'pending', NOW(), NOW()),
(4, 'Arnés no-pull acolchado', 'pending', NOW(), NOW()),
(4, 'Jaula plegable doble puerta', 'pending', NOW(), NOW()),
(4, 'Manta calefacción mascota', 'pending', NOW(), NOW()),
(4, 'Escalera rampa perros', 'pending', NOW(), NOW()),
(4, 'Cámara treat dispenser', 'pending', NOW(), NOW()),
(4, 'Fuente agua cerámica', 'pending', NOW(), NOW()),
(4, 'Limpiador patas automático', 'pending', NOW(), NOW()),
(4, 'Dispensador bolsas residuos', 'pending', NOW(), NOW()),
(4, 'Eliminador olores enzimático', 'pending', NOW(), NOW()),
(4, 'Toallitas húmedas hipoalergénicas', 'pending', NOW(), NOW()),
(4, 'Pelota ejercicio interactiva', 'pending', NOW(), NOW()),
(4, 'Túnel juego gatos', 'pending', NOW(), NOW()),
(4, 'Láser automático ejercicio', 'pending', NOW(), NOW()),
(4, 'Cuerda escalada gatos', 'pending', NOW(), NOW()),
(4, 'Ratón robótico persecución', 'pending', NOW(), NOW()),
(4, 'Puzzle alimentación lenta', 'pending', NOW(), NOW()),
(4, 'Comedero puzzle inteligente', 'pending', NOW(), NOW()),
(4, 'Dispensador medicamentos automático', 'pending', NOW(), NOW()),
(4, 'Termómetro veterinario digital', 'pending', NOW(), NOW()),
(4, 'Kit primeros auxilios', 'pending', NOW(), NOW()),
(4, 'Collar antipulgas natural', 'pending', NOW(), NOW()),
(4, 'Suplemento articulaciones senior', 'pending', NOW(), NOW()),
(4, 'Pasta dental enzimática', 'pending', NOW(), NOW()),
(4, 'Cepillo dientes triple cabeza', 'pending', NOW(), NOW()),
(4, 'Spray entrenamiento casa', 'pending', NOW(), NOW()),
(4, 'Atrapador pelo lavadora', 'pending', NOW(), NOW()),
(4, 'Aspiradora pelo mascotas', 'pending', NOW(), NOW()),
(4, 'Rodillo limpieza tapicería', 'pending', NOW(), NOW()),
(4, 'Desinfectante superficies seguro', 'pending', NOW(), NOW()),
(4, 'Ambientador neutralizador olores', 'pending', NOW(), NOW()),
(4, 'Protector muebles anti-rasguños', 'pending', NOW(), NOW()),
(4, 'Barrera seguridad escaleras', 'pending', NOW(), NOW()),
(4, 'Alfombrilla antideslizante auto', 'pending', NOW(), NOW()),
(4, 'Cinturón seguridad vehicular', 'pending', NOW(), NOW()),
(4, 'Ventilador auto mascotas', 'pending', NOW(), NOW()),
(4, 'Organizador accesorios viaje', 'pending', NOW(), NOW());");

        // CATEGORÍA 5: JOYAS GAFAS Y RELOJES (50 productos)
        DB::statement("INSERT INTO products_to_scrapping (category_id, name, status, created_at, updated_at) VALUES
(5, 'Anillo compromiso oro blanco', 'pending', NOW(), NOW()),
(5, 'Collar perlas cultivadas', 'pending', NOW(), NOW()),
(5, 'Pulsera plata eslabones', 'pending', NOW(), NOW()),
(5, 'Aretes diamantes sintéticos', 'pending', NOW(), NOW()),
(5, 'Cadena oro laminado', 'pending', NOW(), NOW()),
(5, 'Broche vintage cristales', 'pending', NOW(), NOW()),
(5, 'Gemelos acero inoxidable', 'pending', NOW(), NOW()),
(5, 'Dije corazón grabado', 'pending', NOW(), NOW()),
(5, 'Pulsera charm personalizable', 'pending', NOW(), NOW()),
(5, 'Anillo sello familia', 'pending', NOW(), NOW()),
(5, 'Gargantilla terciopelo perla', 'pending', NOW(), NOW()),
(5, 'Brazalete rígido grabado', 'pending', NOW(), NOW()),
(5, 'Pendientes largos bohemios', 'pending', NOW(), NOW()),
(5, 'Anillo ajustable piedras', 'pending', NOW(), NOW()),
(5, 'Collar choker moderno', 'pending', NOW(), NOW()),
(5, 'Reloj pulsera cuarzo', 'pending', NOW(), NOW()),
(5, 'Cronómetro deportivo digital', 'pending', NOW(), NOW()),
(5, 'Reloj pared clásico', 'pending', NOW(), NOW()),
(5, 'Despertador proyección hora', 'pending', NOW(), NOW()),
(5, 'Reloj bolsillo vintage', 'pending', NOW(), NOW()),
(5, 'Cronógrafo acero deportivo', 'pending', NOW(), NOW()),
(5, 'Reloj automático skeleton', 'pending', NOW(), NOW()),
(5, 'Timer cocina magnético', 'pending', NOW(), NOW()),
(5, 'Reloj mesa decorativo', 'pending', NOW(), NOW()),
(5, 'Stopwatch profesional', 'pending', NOW(), NOW()),
(5, 'Gafas sol polarizadas', 'pending', NOW(), NOW()),
(5, 'Lentes lectura bifocales', 'pending', NOW(), NOW()),
(5, 'Gafas seguridad laboratorio', 'pending', NOW(), NOW()),
(5, 'Anteojos computadora filtro', 'pending', NOW(), NOW()),
(5, 'Lentes natación graduados', 'pending', NOW(), NOW()),
(5, 'Gafas esquí antivaho', 'pending', NOW(), NOW()),
(5, 'Lentes realidad aumentada', 'pending', NOW(), NOW()),
(5, 'Anteojos protección láser', 'pending', NOW(), NOW()),
(5, 'Gafas lectura plegables', 'pending', NOW(), NOW()),
(5, 'Lentes contacto coloreados', 'pending', NOW(), NOW()),
(5, 'Joyero organizador giratorio', 'pending', NOW(), NOW()),
(5, 'Estuche gafas rígido', 'pending', NOW(), NOW()),
(5, 'Limpiador ultrasónico joyas', 'pending', NOW(), NOW()),
(5, 'Paño microfibra lentes', 'pending', NOW(), NOW()),
(5, 'Soporte exhibición relojes', 'pending', NOW(), NOW()),
(5, 'Lupa joyero LED', 'pending', NOW(), NOW()),
(5, 'Medidor anillos profesional', 'pending', NOW(), NOW()),
(5, 'Alicates joyería precisión', 'pending', NOW(), NOW()),
(5, 'Lima diamante pulir', 'pending', NOW(), NOW()),
(5, 'Soldador joyas portátil', 'pending', NOW(), NOW()),
(5, 'Yunque mini joyería', 'pending', NOW(), NOW()),
(5, 'Mandril expansor anillos', 'pending', NOW(), NOW()),
(5, 'Cera perdida moldeado', 'pending', NOW(), NOW()),
(5, 'Pulidora rotativa joyas', 'pending', NOW(), NOW()),
(5, 'Prensa hidráulica pequeña', 'pending', NOW(), NOW());");

        // CATEGORÍA 6: MALETAS, BOLSAS Y FUNDAS (50 productos)
        DB::statement("INSERT INTO products_to_scrapping (category_id, name, status, created_at, updated_at) VALUES
(6, 'Maleta cabina 4 ruedas', 'pending', NOW(), NOW()),
(6, 'Mochila viaje antirrobo', 'pending', NOW(), NOW()),
(6, 'Bolsa deportes impermeable', 'pending', NOW(), NOW()),
(6, 'Maletín ejecutivo cuero', 'pending', NOW(), NOW()),
(6, 'Riñonera running reflectante', 'pending', NOW(), NOW()),
(6, 'Bolso mano elegante', 'pending', NOW(), NOW()),
(6, 'Mochila senderismo 40L', 'pending', NOW(), NOW()),
(6, 'Maleta rígida expandible', 'pending', NOW(), NOW()),
(6, 'Bolsa compras reutilizable', 'pending', NOW(), NOW()),
(6, 'Cartera billetera RFID', 'pending', NOW(), NOW()),
(6, 'Mochila laptop acolchada', 'pending', NOW(), NOW()),
(6, 'Bolsa gimnasio ventilada', 'pending', NOW(), NOW()),
(6, 'Maleta vintage retro', 'pending', NOW(), NOW()),
(6, 'Bandolera crossbody ajustable', 'pending', NOW(), NOW()),
(6, 'Mochila hidratación ciclismo', 'pending', NOW(), NOW()),
(6, 'Bolsa pañales organizadora', 'pending', NOW(), NOW()),
(6, 'Maleta aluminio profesional', 'pending', NOW(), NOW()),
(6, 'Clutch fiesta elegante', 'pending', NOW(), NOW()),
(6, 'Mochila escolar ergonómica', 'pending', NOW(), NOW()),
(6, 'Bolsa playa resistente', 'pending', NOW(), NOW()),
(6, 'Neceser viaje colgante', 'pending', NOW(), NOW()),
(6, 'Portadocumentos seguridad', 'pending', NOW(), NOW()),
(6, 'Mochila fotografía profesional', 'pending', NOW(), NOW()),
(6, 'Bolsa herramientas técnico', 'pending', NOW(), NOW()),
(6, 'Maleta médica organizada', 'pending', NOW(), NOW()),
(6, 'Estuche maquillaje profesional', 'pending', NOW(), NOW()),
(6, 'Mochila táctica militar', 'pending', NOW(), NOW()),
(6, 'Bolsa picnic térmica', 'pending', NOW(), NOW()),
(6, 'Maleta musical instrumentos', 'pending', NOW(), NOW()),
(6, 'Cartera tarjetas minimalista', 'pending', NOW(), NOW()),
(6, 'Funda laptop neopreno', 'pending', NOW(), NOW()),
(6, 'Estuche tablet resistente', 'pending', NOW(), NOW()),
(6, 'Protector pantalla cristal', 'pending', NOW(), NOW()),
(6, 'Funda móvil magnética', 'pending', NOW(), NOW()),
(6, 'Case audifonos premium', 'pending', NOW(), NOW()),
(6, 'Funda cámara profesional', 'pending', NOW(), NOW()),
(6, 'Protector teclado transparente', 'pending', NOW(), NOW()),
(6, 'Funda disco duro', 'pending', NOW(), NOW()),
(6, 'Estuche cables organizador', 'pending', NOW(), NOW()),
(6, 'Funda tablet universal', 'pending', NOW(), NOW()),
(6, 'Protector smartwatch deportivo', 'pending', NOW(), NOW()),
(6, 'Funda Nintendo Switch', 'pending', NOW(), NOW()),
(6, 'Case PlayStation portátil', 'pending', NOW(), NOW()),
(6, 'Funda guitarra acolchada', 'pending', NOW(), NOW()),
(6, 'Protector violin profesional', 'pending', NOW(), NOW()),
(6, 'Estuche flauta travesera', 'pending', NOW(), NOW()),
(6, 'Funda trompeta rígida', 'pending', NOW(), NOW()),
(6, 'Case micrófono espuma', 'pending', NOW(), NOW()),
(6, 'Protector amplificador', 'pending', NOW(), NOW()),
(6, 'Funda pedales efectos', 'pending', NOW(), NOW());");
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        Log::info('Migración completada: productos_to_scrapping truncada y productos EN TIENDA eliminados');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Esta migración no se puede revertir ya que elimina datos permanentemente
        Log::warning('No se puede revertir la operación de eliminación de productos');
    }
}
