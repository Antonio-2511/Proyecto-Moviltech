#  MovilTech

Aplicación web de ecommerce desarrollada con **Symfony 7** para la gestión y venta de dispositivos móviles y accesorios tecnológicos.

##  Descripción

MovilTech permite a los usuarios navegar por un catálogo de productos organizados por categorías, añadir artículos al carrito, realizar pedidos y dejar reseñas. Los administradores disponen de un panel para gestionar productos, categorías y pedidos.

##  Tecnologías utilizadas

- PHP 8.2
- Symfony 7
- Doctrine ORM
- Twig
- MariaDB 10.4
- Docker
- Bootstrap 5

##  Requisitos previos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) instalado y en ejecución
- [Composer](https://getcomposer.org/) instalado
- [Symfony CLI](https://symfony.com/download) instalada

##  Instalación y puesta en marcha

### 1. Clonar el repositorio

git clone https://github.com/Antonio-2511/Proyecto-Moviltech.git
cd Proyecto-Moviltech

### 2. Instalar dependencias PHP

composer install

### 3. Levantar la base de datos con Docker

docker compose up -d

### 4. Ejecutar las migraciones

php bin/console doctrine:migrations:migrate

### 5. Cargar los datos iniciales

php bin/console doctrine:fixtures:load

### 6. Arrancar el servidor de desarrollo

symfony serve

La aplicación estará disponible en: **http://localhost:8000**

##  Credenciales de prueba

| Rol | Email | Contraseña |
|-----|-------|------------|
| Administrador | admin@moviltech.com | password |
| Usuario normal | user@moviltech.com | password |

> Las cuentas anteriores son creadas automáticamente por los fixtures al ejecutar `doctrine:fixtures:load`.


##  Funcionalidades principales

- **Catálogo público**: listado y detalle de productos con filtrado por categoría
- **Registro y login** de usuarios
- **Carrito de compra**: añadir productos, calcular total, finalizar pedido
- **Historial de pedidos** del usuario autenticado
- **Reseñas**: los usuarios autenticados pueden valorar productos (1-5 estrellas)
- **Panel de administración**:
  - CRUD completo de productos y categorías
  - Gestión y cambio de estado de pedidos (pendiente, pagado, enviado, entregado, cancelado)

##  Roles de usuario

| Rol | Permisos |
|-----|----------|
| `ROLE_USER` | Navegar catálogo, añadir al carrito, realizar pedidos, dejar reseñas |
| `ROLE_ADMIN` | Gestionar productos, categorías y pedidos desde el panel de administración |

