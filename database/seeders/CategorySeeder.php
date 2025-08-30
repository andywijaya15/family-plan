<?php

namespace Database\Seeders;

use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $insertCategories = [];
        $categories = [
            'MOTOR',
            'BELANJA',
            'LISTRIK',
            'PDAM',
            'JAJAN'
        ];
        foreach ($categories as $category) {
            $insertCategories[] = [
                'id' => Str::uuid7(),
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => 1,
                'updated_by' => 1,
                'name' => $category,
                'type' => CategoryType::EXPENSE
            ];
        }
        if ($insertCategories) {
            Category::query()
                ->insert($insertCategories);
        }
    }
}
