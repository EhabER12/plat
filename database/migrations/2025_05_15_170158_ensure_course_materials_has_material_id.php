<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            // Verificar si la tabla existe
            if (Schema::hasTable('course_materials')) {
                $columns = DB::select("SHOW COLUMNS FROM course_materials");
                $hasMaterialId = false;
                $hasId = false;
                
                foreach ($columns as $column) {
                    if ($column->Field === 'material_id') {
                        $hasMaterialId = true;
                    }
                    if ($column->Field === 'id') {
                        $hasId = true;
                    }
                }
                
                // Si no tiene material_id pero tiene id, renombrar
                if (!$hasMaterialId && $hasId) {
                    // Quitar la clave primaria
                    Schema::table('course_materials', function (Blueprint $table) {
                        $table->dropPrimary();
                    });
                    
                    // Renombrar id a material_id
                    DB::statement('ALTER TABLE course_materials CHANGE id material_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
                    
                    // Establecer material_id como clave primaria
                    Schema::table('course_materials', function (Blueprint $table) {
                        $table->primary('material_id');
                    });
                    
                    // Registrar el cambio
                    DB::statement("INSERT INTO migrations (migration, batch) VALUES ('renamed_id_to_material_id_in_course_materials', 9999)");
                }
                // Si no tiene ninguna de las dos columnas, añadir material_id
                else if (!$hasMaterialId && !$hasId) {
                    Schema::table('course_materials', function (Blueprint $table) {
                        $table->id('material_id')->first();
                    });
                    
                    // Registrar el cambio
                    DB::statement("INSERT INTO migrations (migration, batch) VALUES ('added_material_id_to_course_materials', 9999)");
                }
                
                // Asegurarse de que file_url y file_path son nullables
                if (Schema::hasColumn('course_materials', 'file_url')) {
                    DB::statement('ALTER TABLE course_materials MODIFY file_url VARCHAR(255) NULL');
                } else {
                    Schema::table('course_materials', function (Blueprint $table) {
                        $table->string('file_url')->nullable();
                    });
                }
                
                if (Schema::hasColumn('course_materials', 'file_path')) {
                    DB::statement('ALTER TABLE course_materials MODIFY file_path VARCHAR(255) NULL');
                }
            }
        } catch (\Exception $e) {
            // Registrar el error
            DB::statement("INSERT INTO migrations (migration, batch) VALUES ('error_fixing_course_materials_" . $e->getCode() . "', 9999)");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es recomendable revertir esta migración ya que podría romper la estructura de la base de datos
    }
};
