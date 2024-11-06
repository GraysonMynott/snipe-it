<?php

namespace Database\Factories;

use App\Models\Manufacturer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ManufacturerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Manufacturer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => Null,
            'notes' => Null,
            'url' => Null,
            'support_email' => Null,
        ];
    }

    public function checkpoint()
    {
        return $this->state(function () {
            return [
                'name' => 'Check Point',
                'notes' => 'Check Point Technologies',
                'url' => 'https://checkpoint.com',
                'support_url' => 'https://usercenter.checkpoint.com',
                'image' => 'checkpoint.png',
            ];
        });
    }

    public function aruba()
    {
        return $this->state(function () {
            return [
                'name' => 'Aruba',
                'notes' => 'Aruba Networking. Subsidiary of HPE',
                'url' => 'https://arubanetworks.com',
                'support_url' => 'https://networkingsupport.hpe.com',
                'image' => 'aruba.png',
            ];
        });
    }

    public function cisco()
    {
        return $this->state(function () {
            return [
                'name' => 'Cisco',
                'url' => 'https://cisco.com',
                'support_url' => 'https://support.cisco.com',
            ];
        });
    }

    public function citrix()
    {
        return $this->state(function () {
            return [
                'name' => 'Citrix',
                'notes' => 'Citrix - purveyors of fine NetScalers since 1892',
                'url' => 'https://citrix.com',
                'support_url' => 'https://support.citrix.com',
                'image' => 'citrix.png',
            ];
        });
    }

    public function apple()
    {
        return $this->state(function () {
            return [
                'name' => 'Apple',
                'notes' => 'Not a Pear',
                'url' => 'https://apple.com',
                'support_url' => 'https://support.apple.com',
                'warranty_lookup_url' => 'https://checkcoverage.apple.com',
                'image' => 'apple.jpg',
            ];
        });
    }

    public function hp()
    {
        return $this->state(function () {
            return [
                'name' => 'HP',
                'notes' => 'Like the sauce.',
                'url' => 'https://hp.com',
                'support_url' => 'https://support.hp.com',
                'image' => 'hp.png',
            ];
        });
    }
}
