<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectTest extends TestCase
{

    public function test_admins_can_create_a_project()
    {
        $admin = User::factory()->create();
        $admin->role = 'admin';
        $admin->save();
        $client = Client::factory()->create();
        $assignedUser = User::factory()->create();
        $projectData = [
            'title' => 'title test',
            'description' => 'description test',
            'status' => 'open',
            'assigned_client_id' => $client->id,
            'assigned_user_id' => $assignedUser->id,
            'deadline' => Carbon::now()->addDays(3)->toString(),

        ];

        $this->actingAs($admin)->post(route('project.store'), $projectData)
            ->assertOk()
            ->assertJsonFragment($projectData);

        $this->assertEquals($client->id, Project::first()->client->id);
    }

    public function test_normal_user_cannot_create_a_project()
    {
        $normalUser = User::factory()->create();
        $normalUser->role = '';
        $normalUser->save();
        $client = Client::factory()->create();
        $assignedUser = User::factory()->create();
        $projectData = [
            'title' => 'title test',
            'description' => 'description test',
            'status' => 'open',
            'assigned_client_id' => $client->id,
            'assigned_user_id' => $assignedUser->id,
            'deadline' => Carbon::now()->addDays(3)->toString(),

        ];

        $this->actingAs($normalUser)->post(route('project.store'), $projectData)
            ->assertForbidden();

        $this->assertNull(Project::first());
    }

    public function test_unauthenticated_user_cannot_create_a_project()
    {
        $client = Client::factory()->create();
        $assignedUser = User::factory()->create();
        $projectData = [
            'title' => 'title test',
            'description' => 'description test',
            'status' => 'open',
            'assigned_client_id' => $client->id,
            'assigned_user_id' => $assignedUser->id,
            'deadline' => Carbon::now()->addDays(3)->toString(),

        ];

        $this->post(route('project.store'), $projectData)
            ->assertRedirect(route('login'));

        $this->assertNull(Project::first());
    }

    public function test_admins_can_update_a_project()
    {

        $project = Project::factory()->create();
        $admin = $project->creator;

        $updateData =  $project->toArray();
        unset($updateData["creator"], $updateData["id"]);
        $updateData["title"] = "Título alterado 123";

        $this->actingAs($admin)->patch(route('project.update', $project->id), $updateData)
            ->assertOk()
            ->assertJson($updateData);

        $expectedProject = Project::first();

        $this->assertEquals($updateData["title"], $expectedProject->title);
    }

    public function test_normal_user_cannot_update_a_project()
    {
        $project = Project::factory()->create();
        $normalUser = User::factory()->create();

        $updateData =  $project->toArray();
        unset($updateData["creator"], $updateData["id"]);
        $updateData["title"] = "Título alterado 123";

        $this->actingAs($normalUser)->patch(route('project.update', $project->id), $updateData)
            ->assertForbidden();

        $expectedProject = Project::first();

        $this->assertEquals($project->toArray(), $expectedProject->toArray());
    }

    public function test_unauthenticated_user_cannot_update_a_project()
    {
        $project = Project::factory()->create();
        $normalUser = User::factory()->create();

        $updateData =  $project->toArray();
        unset($updateData["creator"], $updateData["id"]);
        $updateData["title"] = "Título alterado 123";

        $this->patch(route('project.update', $project->id), $updateData)
            ->assertRedirect(route('login'));

        $expectedProject = Project::first();

        $this->assertEquals($project->toArray(), $expectedProject->toArray());
    }

    public function test_admins_can_delete_a_project()
    {
        $project = Project::factory()->create();
        $admin = $project->creator;

        $projectData = $project->toArray();

        $this->actingAs($admin)->delete(route('project.destroy', $project->id))
            ->assertOk()
            ->assertJson($projectData);

        $expectedProject = Project::first();

        $this->assertNull($expectedProject);
    }

    public function test_normal_user_cannot_delete_a_project()
    {
    }

    public function test_unauthenticated_user_cannot_delete_a_project()
    {
    }

    public function test_admins_can_view_a_project()
    {
    }

    public function test_normal_user_can_view_a_project()
    {
    }

    public function test_unauthenticated_user_cannot_view_a_project()
    {
    }
    public function test_title_field_should_be_required()
    {
    }

    public function test_description_field_should_be_required()
    {
    }

    public function test_deadline_field_should_be_required()
    {
    }

    public function test_assigned_client_field_should_be_required()
    {
    }

    public function test_assigned_user_field_should_be_required()
    {
    }

    public function test_status_field_should_be_required()
    {
    }

    public function test_assigned_user_should_be_active_in_database()
    {
    }

    public function test_assigned_client_should_be_active_in_database()
    {
    }
}
