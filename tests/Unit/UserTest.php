<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{

    /**
     *
     * @return void
     */
    public function test_check_if_all_user_fillable_properties_are_correct_created()
    {
        $userFillAblePropertiesExpected = [
            "name",
            "email",
            "password",
            "role",
        ];

        $user = new User();

        $userPropertiesIntersect = array_intersect($userFillAblePropertiesExpected, $user->getFillable());

        $this->assertEquals($userPropertiesIntersect, $userFillAblePropertiesExpected);
        $this->assertEquals(sizeof($userFillAblePropertiesExpected), sizeof($user->getFillable()));
    }

    public function test_check_if_an_admin_user_is_correct_returned_in_isAdmin_function()
    {
        $normalUser = User::factory()->create();
        $normalUser->role = '';

        $adminUser = User::factory()->create();
        $adminUser->role = 'admin';


        $this->assertFalse($normalUser->isAdmin());
        $this->assertTrue($adminUser->isAdmin());
    }
}
