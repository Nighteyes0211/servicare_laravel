<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskCategorySeeder extends Seeder
{
    public function run()
    {
        DB::table('task_categories')->insert([
            ['name' => 'Wartung', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Reparatur', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
