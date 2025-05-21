<?php

// Script para ejecutar migraciones en orden específico
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Primero limpiamos todas las tablas
echo "Eliminando todas las tablas existentes...\n";
Illuminate\Support\Facades\Artisan::call('db:wipe', ['--force' => true]);
echo Illuminate\Support\Facades\Artisan::output();

// Creamos la tabla de migraciones
echo "Creando la tabla de migraciones...\n";
Illuminate\Support\Facades\DB::unprepared('CREATE TABLE migrations (id INT AUTO_INCREMENT PRIMARY KEY, migration VARCHAR(255), batch INT)');

// Obtenemos el orden de migraciones
$migrations = require_once __DIR__.'/migrations_order.php';

// Ejecutamos cada migración en orden
$batch = 1;
foreach ($migrations as $migration) {
    $filename = basename($migration);
    echo "Ejecutando migración: $filename\n";
    
    // Ejecutar la migración
    $migrationClass = require $migration;
    $migrationClass->up();
    
    // Registrar la migración como completada
    Illuminate\Support\Facades\DB::table('migrations')->insert([
        'migration' => pathinfo($filename, PATHINFO_FILENAME),
        'batch' => $batch
    ]);
    
    echo "Migración completada: $filename\n";
}

echo "Todas las migraciones han sido ejecutadas correctamente.\n"; 