<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        Category::truncate();

        $admin = User::where('permissions->superuser', '1')->first() ?? User::factory()->firstAdmin()->create();

        Category::factory()->count(1)->assetFirewallCategory()->create(['user_id' => $admin->id]);
        Category::factory()->count(1)->assetServerCategory()->create(['user_id' => $admin->id]);
        Category::factory()->count(1)->assetSwitchCategory()->create(['user_id' => $admin->id]);
        Category::factory()->count(1)->assetRouterCategory()->create(['user_id' => $admin->id]);
        Category::factory()->count(1)->assetAccessPointCategory()->create(['user_id' => $admin->id]);
        Category::factory()->count(1)->assetNetScalerCategory()->create(['user_id' => $admin->id]);
    }
}
