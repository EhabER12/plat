<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TestMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('course_materials')->insert([
            'material_id' => 10,
            'course_id' => 5,
            'title' => 'Test Material',
            'description' => 'Test Description',
            'file_url' => 'storage/courses/5/materials/test-material.pdf',
            'file_type' => 'pdf',
            'file_size' => 1024,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
} 