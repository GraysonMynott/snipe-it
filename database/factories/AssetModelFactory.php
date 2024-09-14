<?php

namespace Database\Factories;

use App\Models\AssetModel;
use App\Models\CustomField;
use App\Models\CustomFieldset;
use App\Models\Manufacturer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

class AssetModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AssetModel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => "1",
            'name' => Null,
            'category_id' => Category::factory(),
            'model_number' => Null,
            'notes' => Null,

        ];
    }

    public function ckp1550Model()
    {
        return $this->state(function () {
            return [
                'name' => '1550 Appliance',
                'category_id' => function () {
                    return Category::where('name', 'Firewall')->first() ?? Category::factory()->assetFirewallCategory();
                },
                'manufacturer_id' => function () {
                    return Manufacturer::where('name', 'Check Point')->first() ?? Manufacturer::factory()->checkpoint();
                },
                'notes' => 'Check Point 1550 Appliance'
            ];
        });
    }

    public function ckp1555Model()
    {
        return $this->state(function () {
            return [
                'name' => '1555 Appliance',
                'category_id' => function () {
                    return Category::where('name', 'Firewall')->first() ?? Category::factory()->assetFirewallCategory();
                },
                'manufacturer_id' => function () {
                    return Manufacturer::where('name', 'Check Point')->first() ?? Manufacturer::factory()->checkpoint();
                },
                'notes' => 'Check Point 1555 Appliance'
            ];
        });
    }

    public function ckp1570Model()
    {
        return $this->state(function () {
            return [
                'name' => '1570 Appliance',
                'category_id' => function () {
                    return Category::where('name', 'Firewall')->first() ?? Category::factory()->assetFirewallCategory();
                },
                'manufacturer_id' => function () {
                    return Manufacturer::where('name', 'Check Point')->first() ?? Manufacturer::factory()->checkpoint();
                },
                'notes' => 'Check Point 1570 Appliance'
            ];
        });
    }

    public function ckp1590Model()
    {
        return $this->state(function () {
            return [
                'name' => '1590 Appliance',
                'category_id' => function () {
                    return Category::where('name', 'Firewall')->first() ?? Category::factory()->assetFirewallCategory();
                },
                'manufacturer_id' => function () {
                    return Manufacturer::where('name', 'Check Point')->first() ?? Manufacturer::factory()->checkpoint();
                },
                'notes' => 'Check Point 1590 Appliance'
            ];
        });
    }

    public function ckpCloudguardModel()
    {
        return $this->state(function () {
            return [
                'name' => 'Cloudguard VM',
                'category_id' => function () {
                    return Category::where('name', 'Firewall')->first() ?? Category::factory()->assetFirewallCategory();
                },
                'manufacturer_id' => function () {
                    return Manufacturer::where('name', 'Check Point')->first() ?? Manufacturer::factory()->checkpoint();
                },
                'notes' => 'Check Point Cloudguard Virtual Machine'
            ];
        });
    }
}
