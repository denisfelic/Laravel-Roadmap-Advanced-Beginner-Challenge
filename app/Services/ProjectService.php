<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Validation\UnauthorizedException;

class ProjectService
{

    private User $creatorUser;
    private User $assignedUser;
    private Client $assignedClient;

    private array $VALID_STATUSES = ["open", "closed"];

    public function __construct(string $creatorUserId, string $assignedUserId, string $assignedClientId)
    {
        $this->creatorUser = User::findOrFail($creatorUserId);
        $this->assignedUser = User::findOrFail($assignedUserId);
        $this->assignedClient = Client::findOrFail($assignedClientId);
    }

    /**
     * Change the status of an Project
     *
     * @param string $status
     * @return void
     */
    public function changeStatus(string $status, Project $project)
    {
        $this->validateStatus($status);

        $project->status = $status;
        $project->save();
    }

    public function changeDeadline($deadline, Project $project)
    {
        $this->validateDeadLine($deadline);

        $project->deadline = $deadline;
    }

    public function createProject($deadline, string $title, string $description, string $status = 'open'): array
    {
        $this->validateDeadLine($deadline);
        $this->validateStatus($status);
        $this->checkIfUserCanCreateProject();

        $project =  Project::create([
            'deadline' => $deadline,
            'title' => $title,
            'description' => $description,
            "assigned_client_id" => $this->assignedClient->id,
            "assigned_user_id" => $this->assignedUser->id,
            "creator_user_id" => $this->creatorUser->id,
            'status' => $status
        ]);
        return $project->toArray();
    }

    /**
     * Check if user have permissions to create an project
     * @throws UnauthorizedException
     * @return void
     */
    private function checkIfUserCanCreateProject()
    {
        if (!$this->creatorUser->isAdmin()) {
            throw new UnauthorizedException("Only admins can create a project");
        }
    }


    /**
     * Check if the deadline is greater than actual date
     * @throws Exception
     * @param [type] $deadline
     * @return void
     */
    public function validateDeadLine($deadline)
    {

        $currentDate = Carbon::now();
        if (!$currentDate->lessThan($deadline)) {
            // TODO: Refactor to use a specific Exception
            throw new Exception("The date should be greater than actual date");
        }
    }
    /**
     * Check if the status is valid
     * @throws Exception
     * @param [type] $status
     * @return void
     */
    private function validateStatus($status)
    {
        if (!in_array($status, $this->VALID_STATUSES)) {
            // TODO: Refactor to use a specific Exception
            throw new Exception("Invalid status type for the project");
        }
    }
}
