<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use App\Models\Animal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();//取消外鍵約束
        Animal::truncate();//請空資料表 ID歸零
        User::truncate();//請空資料表 ID歸零

        User::factory(5)->create();
        Animal::factory(10000)->create();
        Schema::enableForeignKeyConstraints();//開啟外鍵約束
    }
}
