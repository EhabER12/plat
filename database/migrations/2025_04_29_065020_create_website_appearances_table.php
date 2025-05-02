<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('website_appearances', function (Blueprint $table) {
            $table->id();
            $table->string('key')->index();
            $table->json('value')->nullable();
            $table->string('section')->index();
            $table->string('type')->default('text');
            $table->timestamps();
            
            // Make key and section unique together
            $table->unique(['key', 'section']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_appearances');
    }
};
