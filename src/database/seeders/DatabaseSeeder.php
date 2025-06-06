<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\ProductsTableSeeder; // ProductsTableSeeder をインポート

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // ProductsTableSeeder を実行
        $this->call(ProductsTableSeeder::class);
    }
}
