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
                'manufacturer_id' => function () {
                    return Manufacturer::where('name', 'Check Point')->first() ?? Manufacturer::factory()->checkpoint();
                },
                'category_id' => function () {
                    return Category::where('name', 'Firewall')->first() ?? Category::factory()->assetFirewallCategory();
                },
                'name' => '1550 Appliance',
                'notes' => 'Check Point 1550 SMB Appliance',
                'model_number' => 'CPAP-SG1550',
            ];
        });
    }

    public function ckp1555Model()
    {
        return $this->state(function () {
            return [
                'manufacturer_id' => function () {
                    return Manufacturer::where('name', 'Check Point')->first() ?? Manufacturer::factory()->checkpoint();
                },
                'category_id' => function () {
                    return Category::where('name', 'Firewall')->first() ?? Category::factory()->assetFirewallCategory();
                },
                'name' => '1555 Appliance',
                'notes' => 'Check Point 1555 SMB Appliance',
                'model_number' => 'CPAP-SG1555',
            ];
        });
    }

    public function ckp1570Model()
    {
        return $this->state(function () {
            return [
                'manufacturer_id' => function () {
                    return Manufacturer::where('name', 'Check Point')->first() ?? Manufacturer::factory()->checkpoint();
                },
                'category_id' => function () {
                    return Category::where('name', 'Firewall')->first() ?? Category::factory()->assetFirewallCategory();
                },
                'name' => '1550 Appliance',
                'notes' => 'Check Point 1570 SMB Appliance',
                'model_number' => 'CPAP-SG1570',
            ];
        });
    }

    public function ckp1590Model()
    {
        return $this->state(function () {
            return [
                'manufacturer_id' => function () {
                    return Manufacturer::where('name', 'Check Point')->first() ?? Manufacturer::factory()->checkpoint();
                },
                'category_id' => function () {
                    return Category::where('name', 'Firewall')->first() ?? Category::factory()->assetFirewallCategory();
                },
                'name' => '1590 Appliance',
                'notes' => 'Check Point 1590 SMB Appliance',
                'model_number' => 'CPAP-SG1590',
            ];
        });
    }

    public function ckpCloudGuardModel()
    {
        return $this->state(function () {
            return [
                'manufacturer_id' => function () {
                    return Manufacturer::where('name', 'Check Point')->first() ?? Manufacturer::factory()->checkpoint();
                },
                'category_id' => function () {
                    return Category::where('name', 'Firewall')->first() ?? Category::factory()->assetFirewallCategory();
                },
                'name' => 'CloudGuard VM',
                'notes' => 'Check Point 1550 Appliance',
            ];
        });
    }
}
