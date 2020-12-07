<?php

namespace Database\Seeders;

use App\Models\CategoryMagazine;
use Illuminate\Database\Seeder;

class CategoryMagazineTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i=1; $i<=3; $i++) {
            $category = new CategoryMagazine();
            $category->name = "Category $i";
            $category->save();
        }
    }
}
