<?php

namespace Tests\Unit;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\UnauthorizedException;
use Tests\TestCase;

class ProjectServiceTest extends TestCase
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
        $creatorUser->role = 'admin';
        $creatorUser->save();
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
        $this->assertEquals($expectedProject->client->toArray(), $assignedClient->toArray());
        $this->assertEquals($expectedProject->user->toArray(), $assignedUser->toArray());
        $this->assertEquals($expectedProject->creator->toArray(), $creatorUser->toArray());
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_normal_user_cannot_create_a_project()
    {
        $this->expectException(UnauthorizedException::class);
        $creatorUser = User::factory()->create();
        $creatorUser->role = "";
        $creatorUser->save();

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

        $this->assertNull($project);
        $this->assertNull($expectedProject);
        $this->assertEquals($assignedUser->projects, null);
        $this->assertEquals($assignedClient->project->toArray(), null);
        $this->assertEquals($creatorUser->createdProjects, null);
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_the_assigned_user_is_not_null_or_invalid()
    {
        $this->expectException(ModelNotFoundException::class);
        $creatorUser = User::factory()->create();
        $creatorUser->role = "admin";
        $creatorUser->save();

        $assignedClient = Client::factory()->create();
        $title = "Test Project";
        $description = "Test description for project";

        $project = (new ProjectService($creatorUser->id, '', $assignedClient->id))
            ->createProject(Carbon::now()->addDays(4)->toDateString(),  $title, $description, 'closed');
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_the_assigned_client_is_not_null_or_invalid()
    {
        $this->expectException(ModelNotFoundException::class);
        $creatorUser = User::factory()->create();
        $assignedUser = User::factory()->create();
        $creatorUser->role = "admin";
        $creatorUser->save();

        $title = "Test Project";
        $description = "Test description for project";

        $project = (new ProjectService($creatorUser->id, $assignedUser->id, ''))
            ->createProject(Carbon::now()->addDays(4)->toDateString(),  $title, $description, 'closed');
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_status_is_open_as_default_when_created()
    {
        $creatorUser = User::factory()->create();
        $creatorUser->role = 'admin';
        $creatorUser->save();
        $assignedClient = Client::factory()->create();
        $assignedUser = User::factory()->create();
        $title = "Test Project";
        $description = "Test description for project";

        (new ProjectService($creatorUser->id, $assignedUser->id, $assignedClient->id))
            ->createProject(Carbon::now()->addDays(4)->toDateString(),  $title, $description);

        $expectedProject = Project::where('assigned_user_id', $assignedUser->id)->first();


        $this->assertEquals('open', $expectedProject->status);
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_status_can_be_changed_to_closed()
    {
        $creatorUser = User::factory()->create();
        $creatorUser->role = 'admin';
        $creatorUser->save();

        $assignedClient = Client::factory()->create();
        $assignedUser = User::factory()->create();
        $title = "Test Project";
        $description = "Test description for project";

        $projectService = new ProjectService($creatorUser->id, $assignedUser->id, $assignedClient->id);
        $projectService->createProject(Carbon::now()->addDays(4)->toDateString(),  $title, $description);
        $projectService->changeStatus("closed", Project::first());

        $expectedProject = Project::where('assigned_user_id', $assignedUser->id)->first();
        $this->assertEquals("closed", $expectedProject->status);
    }


    /**
     * @test
     *
     * @return void
     */
    public function check_if_project_cannot_be_created_if_deadline_is_before_or_current_date()
    {
        $creatorUser = User::factory()->create();
        $creatorUser->role = 'admin';
        $creatorUser->save();

        $assignedClient = Client::factory()->create();
        $assignedUser = User::factory()->create();
        $title = "Test Project";
        $description = "Test description for project";
        $this->expectExceptionMessage('The date should be greater than actual date');

        $projectService = new ProjectService($creatorUser->id, $assignedUser->id, $assignedClient->id);
        $projectService->createProject(Carbon::now()->subMinutes(1)->toDateString(),  $title, $description);

        $expectedProject = Project::where('assigned_user_id', $assignedUser->id)->first();
        $this->assertEquals(null, $expectedProject);
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_project_deadline_cannot_be_changed_to_before_or_equal_current_date()
    {
        $creatorUser = User::factory()->create();
        $creatorUser->role = 'admin';
        $creatorUser->save();

        $assignedClient = Client::factory()->create();
        $assignedUser = User::factory()->create();
        $title = "Test Project";
        $description = "Test description for project";
        $this->expectExceptionMessage('The date should be greater than actual date');

        $expectedDate = Carbon::now()->addDay(2)->toDateString();
        $projectService = new ProjectService($creatorUser->id, $assignedUser->id, $assignedClient->id);
        $projectService->createProject($expectedDate,  $title, $description);

        $project = Project::first();
        $projectService->changeDeadline(Carbon::now()->subDay()->toDateString(), $project);


        $expectedProject = Project::where('assigned_user_id', $assignedUser->id)->first();
        $this->assertEquals($expectedDate, $expectedProject->deadline);
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_project_deadline_can_be_changed_to_after_or_equal_current_date()
    {
        $creatorUser = User::factory()->create();
        $creatorUser->role = 'admin';
        $creatorUser->save();

        $assignedClient = Client::factory()->create();
        $assignedUser = User::factory()->create();
        $title = "Test Project";
        $description = "Test description for project";
        $this->expectExceptionMessage('The date should be greater than actual date');

        $projectService = new ProjectService($creatorUser->id, $assignedUser->id, $assignedClient->id);
        $projectService->createProject(Carbon::now()->toDateString(),  $title, $description);

        $project = Project::first();
        $expectedDate = Carbon::now()->addDay(2)->toDateString();
        $projectService->changeDeadline($expectedDate, $project);


        $expectedProject = Project::where('assigned_user_id', $assignedUser->id)->first();
        $this->assertEquals($expectedDate, $expectedProject->deadline);
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
