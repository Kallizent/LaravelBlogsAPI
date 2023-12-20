<?php

namespace Database\Seeders;

use DateTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('blogs')->insert([
            'uuid' => Str::uuid(),
            'title' => 'Judul Pertama',
            'description' => 'Deskripsi blog pertama',
            'image' => 'example1.jpg',
            'created_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('blogs')->insert([
            'uuid' => Str::uuid(),
            'title' => 'Judul kedua',
            'description' => 'Deskripsi blog kedua',
            'image' => 'example2.jpg',
            'created_at' => date("Y-m-d H:i:s")
        ]);
    }
}
