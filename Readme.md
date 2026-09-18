<!-- Primero usaremos starkit de lviwir -->

# Instalar paquetes y ejecutar migracion

php artisan tenancy:install
php artisan migrate

### Registar en Providers

agregar en proviers:
TenancyServiceProvider::class

### Crear modelos para los tenant

php artisan make:model Tenant
