<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $creator = User::factory()->create();
        $creator->role = 'admin';
        $creator->save();

        $assignedId = (User::factory()->create())->id;
        $clientId = (Client::factory()->create())->id;

        return [
            "title" => $this->faker->unique()->words(3, true),
            "description" => $this->faker->paragraph(),
            "creator_user_id" => $creator->id,
            "assigned_user_id" => $assignedId,
            "assigned_client_id" => $clientId,
            "status" => 'closed',
            "deadline" => Carbon::now()->addDays(3)->toString(),
        ];
    }
}
