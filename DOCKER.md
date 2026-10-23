# tiendaback con Docker (PHP 8.5.12)

El servicio corre en contenedores con PHP 8.5.12 y Laravel 13. No hace falta instalar PHP 8.5 en el servidor.

## Qué levanta `docker-compose.yml`

| Servicio | Qué hace | Puerto (solo 127.0.0.1) |
|---|---|---|
| `app` | php-fpm 8.5.12 con el código | `DOCKER_FPM_PORT` (9385) |
| `web` | nginx que sirve `public/` y pasa PHP a `app` | `DOCKER_HTTP_PORT` (8385) |

- Los dos usan `network_mode: host`, así que la app se conecta a la **MySQL del propio servidor** con las credenciales
  del `.env` de la carpeta donde se hace el pull (no se levanta MySQL en Docker). `DB_HOST=127.0.0.1` sigue funcionando.
- Los puertos 9385 y 8385 evitan chocar con php-fpm 7.4, con redis-laravel (9383/8383) y con cursoback (9384/8384). Se pueden cambiar con
  `DOCKER_FPM_PORT` o `DOCKER_HTTP_PORT` en el `.env`.
- `storage/` se monta desde la carpeta del servidor (logs y videos de productos se conservan). Los videos
  (`storage/app/public/videos`) también se montan en el nginx del contenedor para que `/storage/videos/...` siga sirviéndose.
- La app no usa colas, scheduler ni redis (cache en archivos, `QUEUE_CONNECTION=sync`), por eso no hay servicios extra.

## Despliegue

```bash
cd /ruta/de/tiendaback   # carpeta actual del servicio, con su .env
git pull
docker compose up -d --build
docker compose ps
docker compose logs -f app web
```

Luego, en el nginx del servidor, el sitio de tiendaback pasa de `fastcgi_pass` a php 7.4 a un proxy al contenedor:

```nginx
location / {
    proxy_pass http://127.0.0.1:8385;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    client_max_body_size 50M;
}
```

y se deja de usar el php-fpm 7.4 de este servicio.

## Notas

- Comandos artisan: `docker compose exec app php artisan <comando>`.
- Tras cambiar código: `docker compose up -d --build`.
- Laravel 8 no soporta PHP 8.5, por eso se subió a Laravel 13 (sanctum 4, tinker 3, jwt-auth 2.3, responsecache 8, phpunit 12).
