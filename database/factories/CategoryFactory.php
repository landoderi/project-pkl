<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true); // contoh: "Kopi Arabika"

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(8),
            'image' => 'categories/default.jpg', // pastikan file dummy ini ada di storage/public/categories
            'is_active' => true,
        ];
    }
}
