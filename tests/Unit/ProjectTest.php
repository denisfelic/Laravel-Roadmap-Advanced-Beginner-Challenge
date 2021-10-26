<?php

namespace Tests\Unit;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     * @return void
     */
    public function check_if_the_creator_user_is_not_null()
    {

        $assignedClient = Client::factory()->create();
        $assignedUser = User::factory()->create();
        $title = "Test Project";
        $description = "Test description for project";

        $this->expectException(ModelNotFoundException::class);

        $project = (new ProjectService('undefined', $assignedUser->id, $assignedClient->id))
            ->createProject(Carbon::now()->addDays(4)->toDateString(),  $title, $description, 'closed');

        $expectedProject = Project::where('assigned_user_id', $assignedUser->id)->first();
        $this->assertNull($project);
        $this->assertNull($expectedProject);
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_admin_can_create_an_project()
    {
        $creatorUser = User::factory()->create();
        $assignedClient = Client::factory()->create();
        $assignedUser = User::factory()->create();
        $title = "Test Project";
        $description = "Test description for project";

        $project = (new ProjectService($creatorUser->id, $assignedUser->id, $assignedClient->id))
            ->createProject(Carbon::now()->addDays(4)->toDateString(),  $title, $description, 'closed');

        $expectedProject = Project::where('assigned_user_id', $assignedUser->id)->first();

        $creatorUser->refresh();
        $assignedClient->refresh();
        $assignedUser->refresh();

        $this->assertNotNull($project);
        $this->assertNotNull($expectedProject);
        $this->assertEquals($project, $expectedProject->toArray());
        $this->assertEquals($expectedProject->assignedClient->toArray(), $assignedClient->toArray());
        $this->assertEquals($expectedProject->assignedUser->toArray(), $assignedUser->toArray());
        $this->assertEquals($expectedProject->creatorUser->toArray(), $creatorUser->toArray());
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_normal_user_cannot_create_a_project()
    {
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_the_assigned_user_is_not_null_or_invalid()
    {
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_the_assigned_client_is_not_null_or_invalid()
    {
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_status_is_open_as_default_when_created()
    {
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_status_can_be_changed_to_closed()
    {
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_can_create_project_with_status_closed()
    {
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_project_deadline_is_after_the_current_date()
    {
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_project_title_is_not_null()
    {
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_project_description_is_not_null()
    {
    }
}
