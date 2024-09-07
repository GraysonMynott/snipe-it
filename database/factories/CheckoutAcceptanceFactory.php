<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CheckoutAcceptanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'checkoutable_type' => Asset::class,
            'checkoutable_id' => Asset::factory(),
            'assigned_to_id' => User::factory(),
        ];
    }

    public function pending()
    {
        return $this->state([
            'accepted_at' => null,
            'declined_at' => null,
        ]);
    }
}
