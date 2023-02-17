<?php

namespace Database\Factories;

use App\Enums\PackageStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'filename' => 'test.zip',
            'hash' => fake()->sha1(),
            'status' => PackageStatus::QUEUED,
            'location' => 's3',
            'path' => '',
            'publisher_email' => fake()->safeEmail(),
            'publisher_name' => fake()->name(),
            'started_processing' => '',
            'finished_processing' => '',
            'request_ip' => fake()->ipv4(),
        ];
    }

    public function queued()
    {
        return $this->state(fn (array $attributes) => [
            'status' => PackageStatus::QUEUED,
        ]);
    }

    public function processing()
    {
        return $this->state(fn (array $attributes) => [
            'status' => PackageStatus::PROCESSING,
            'started_processing' => Carbon::now(),
        ]);
    }

    public function processed()
    {
        return $this->state(fn (array $attributes) => [
            'status' => PackageStatus::FINISHED,
            'started_processing' => Carbon::now()->subMinutes(30),
            'finished_processing' => Carbon::now(),
        ]);
    }

    public function failed()
    {
        return $this->state(fn (array $attributes) => [
            'status' => PackageStatus::FAILED,
            'started_processing' => Carbon::now()->subMinutes(30),
            'finished_processing' => Carbon::now(),
        ]);
    }
}
