<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            'id'=>'GADGET', 
            'name'=>'Gadget', 
            'created_at'=>'2024-10-10 10:10:10',
            'description'=>'Gadget Category'
        ]);
        DB::table('categories')->insert([
            'id'=>'FOOD', 
            'name'=>'Food', 
            'created_at'=>'2024-10-10 10:10:10',
            'description'=>'Food Category'
        ]);
        DB::table('categories')->insert([
            'id'=>'FASHION', 
            'name'=>'Fashion', 
            'created_at'=>'2024-10-10 10:10:10',
        ]);
        DB::table('categories')->insert([
            'id'=>'SMARTPHONE', 
            'name'=>'Smartphone', 
            'created_at'=>'2024-10-10 10:10:10',
        ]);
    }
}
