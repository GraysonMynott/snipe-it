<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => Null,
            'category_type' => 'asset',
            'user_id' => "1",
        ];
    }

    // usage: Category::factory()->assetFirewallCategory();
    public function assetFirewallCategory()
    {
        return $this->state([
            'name' => 'Firewall',
            'category_type' => 'asset',
        ]);
    }

    // usage: Category::factory()->assetServerCategory();
    public function assetServerCategory()
    {
        return $this->state([
            'name' => 'Server',
            'category_type' => 'asset',
        ]);
    }

    // usage: Category::factory()->assetSwitchCategory();
    public function assetSwitchCategory()
    {
        return $this->state([
            'name' => 'Switch',
            'category_type' => 'asset',
        ]);
    }

    // usage: Category::factory()->assetRouterCategory();
    public function assetRouterCategory()
    {
        return $this->state([
            'name' => 'Router',
            'category_type' => 'asset',
        ]);
    }

     // usage: Category::factory()->assetAccessPointCategory();
     public function assetWAPCategory()
     {
         return $this->state([
             'name' => 'Wireless Access Point',
             'category_type' => 'asset',
         ]);
     }

     // usage: Category::factory()->assetNetScalerCategory();
     public function assetNetScalerCategory()
     {
         return $this->state([
             'name' => 'NetScaler',
             'category_type' => 'asset',
         ]);
     }
}
