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
            'name' => $this->faker->unique()->company(),
            'user_id' => User::factory()->superuser(),
            'support_phone' => $this->faker->phoneNumber(),
            'url' => $this->faker->url(),
            'support_email' => $this->faker->safeEmail(),
        ];
    }

    public function checkpoint()
    {
        return $this->state(function () {
            return [
                'name' => 'Check Point',
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
                'url' => 'https://hp.com',
                'support_url' => 'https://support.hp.com',
                'image' => 'hp.png',
            ];
        });
    }
}
