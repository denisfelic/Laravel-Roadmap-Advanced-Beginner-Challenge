<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectTest extends TestCase
{

    public function test_admins_can_create_a_project()
    {
        $admin = User::factory()->create();
        $project = Project::factory()->make();


        $this->actingAs($admin)->post(route('project.store'), $project->toArray())
            ->assertOk()
            ->assertJson($project->toArray());

        $this->assertEquals($project->toArray(), Project::first()->toArray());
    }

    public function test_normal_user_cannot_create_a_project()
    {
    }

    public function test_unauthenticated_user_cannot_create_a_project()
    {
    }

    public function test_admins_can_update_a_project()
    {
    }

    public function test_normal_user_cannot_update_a_project()
    {
    }

    public function test_unauthenticated_user_cannot_update_a_project()
    {
    }

    public function test_admins_can_delete_a_project()
    {
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
