<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_check_if_user_columns_is_correct()
    {
        $user = User::factory()->make();
        $properties = [
            'name',
            'email',
            'password'
        ];

        $arrayDiff = array_diff($properties, $user->getFillable());
        $sizeDiff =   sizeof($user->getFillable()) -  sizeof($properties);

        $this->assertEquals(0, sizeof($arrayDiff), "some attributes name don't match");
        $this->assertEquals(0, $sizeDiff, "the number of attributes don't match");
    }

    /**
     * @test
     */
    public function check_if_user_is_created()
    {
        User::factory(3)->create();

        $users = User::all();

        assertEquals(4, sizeof($users));
    }
}
