<?php
namespace Database\Factories;

use App\Models\LabOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class LabOrderFactory extends Factory
{
    protected $model = LabOrder::class;

    public function definition(): array
    {
        $priorities = ['normal','urgent'];
        $statuses   = ['created','received','testing','completed','archived'];

        $status = $this->faker->randomElement($statuses);
        $scheduled = $this->faker->dateTimeBetween('+0 days', '+10 days');

        return [
            'patient_name' => $this->faker->name(),
            'test_code'    => $this->faker->randomElement(['CBC','LFT','RFT','CRP']),
            'priority'     => $this->faker->randomElement($priorities),
            'status'       => $status,
            'scheduled_at' => $scheduled,
            'completed_at' => $status === 'completed' ? now() : null,
            'created_by'   => User::factory(),
        ];
    }
}
