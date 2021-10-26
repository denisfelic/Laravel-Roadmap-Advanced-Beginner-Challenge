<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class UserTest extends TestCase
{


    /**
     * @test
     */
    public function check_if_user_unauthenticated_is_redirect_to_login()
    {
        $response = $this->get('/')->assertRedirect('/login');
    }

    /**
     * @test
     */
    public function check_if_authenticated_user_redirect_to_dashboard()
    {

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->get('/')->assertSee('CRM');
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_comum_user_cannot_access_users_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->get(route('users.index'))
            ->assertRedirect('/')
            ->assertStatus(302);
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_comum_user_cannot_access_edit_user_page()
    {
        $anotherUser = User::factory()->create();

        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->get(route('users.edit', $anotherUser->id));

        $response->assertRedirect('/')
            ->assertStatus(302);
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_comum_user_cannot_access_show_user_page()
    {
        $anotherUser = User::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->get(route('users.show', $anotherUser->id))
            ->assertRedirect('/')
            ->assertStatus(302);
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_comum_user_cannot_access_create_user_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->get(route('users.create'))
            ->assertRedirect('/')
            ->assertStatus(302);
    }



    /**
     * @test
     *
     * @return void
     */
    public function check_if_unauthenticated_user_trying_access_user_routes_redirect_to_login()
    {
        // get resources
        $response = $this->get(route('users.index'))
            ->assertRedirect(route('login'))->assertStatus(302);

        $response = $this->get(route('users.show', 1))
            ->assertRedirect(route('login'))->assertStatus(302);

        $response = $this->get(route('users.edit', 1))
            ->assertRedirect(route('login'))->assertStatus(302)->assertLocation(route('login'));
    }


    /**
     * @test
     */

    public function check_if_user_as_admin_can_access_user_create_page()
    {
        $userAdmin = User::factory()->create();
        $userAdmin->role = 'admin';
        $userAdmin->save();


        $request = $this->actingAs($userAdmin)->withSession(['banned' => false])->get(route('users.create'))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function check_if_user_as_admin_can_access_user_edit_page_of_specific_user()
    {
        $anotherUser = User::factory()->create();

        $userAdmin = User::factory()->create();
        $userAdmin->role = 'admin';
        $userAdmin->save();

        $request = $this->actingAs($userAdmin)->withSession(['banned' => false])
            ->get(route('users.edit', $anotherUser->id))
            ->assertStatus(200);
    }


    /**
     * @test
     *
     */
    public function check_if_admin_user_can_change_user_role()
    {
        $userAdmin = User::factory()->create();
        $userAdmin->role = 'admin';
        // certify that user is an normal user
        $anotherUser = User::factory()->create();
        $anotherUser->role = '';
        $anotherUser->save();

        $request = $this->actingAs($userAdmin)->withSession(['banned' => false])
            ->post(route('users.change_role', $anotherUser->id), [
                "role" => "admin",

            ])
            ->assertStatus(200);

        $anotherUser = User::find($anotherUser->id);
        assertEquals('admin', $anotherUser->role);
    }

    /**
     * @test
     *
     */
    public function check_if_a_normal_user_cannot_change_user_role()
    {
        $normalUser = User::factory()->create();
        $normalUser->role = '';
        $normalUser->save();
        // certify that user is an normal user
        $anotherUser = User::factory()->create();
        $anotherUser->role = '';
        $anotherUser->save();

        $request = $this->actingAs($normalUser)->withSession(['banned' => false])
            ->post(route('users.change_role', $anotherUser->id), [
                "role" => "admin",

            ])
            ->assertStatus(403);

        $anotherUser = User::find($anotherUser->id);
        assertEquals('', $anotherUser->role);
    }

    public function test_check_if_a_normal_user_cannot_change_role_of_admins()
    {
        $normalUser = User::factory()->create();
        $normalUser->role = '';
        $normalUser->save();
        // certify that user is an normal user
        $adminUser = User::factory()->create();
        $adminUser->role = 'admin';
        $adminUser->save();

        $request = $this->actingAs($normalUser)->withSession(['banned' => false])
            ->post(route('users.change_role', $adminUser->id), [
                "role" => "admin",

            ])
            ->assertStatus(403);

        $adminUser = User::find($adminUser->id);
        assertEquals('admin', $adminUser->role);
    }

    /**
     * @test
     *
     * @return void
     */
    public function check_if_a_unauthenticated_user_cannot_change_user_role()
    {
        // certify that user is an normal user
        $anotherUser = User::factory()->create();
        $anotherUser->role = '';
        $anotherUser->save();

        $request = $this->post(route('users.change_role', $anotherUser->id), [
            "role" => "admin",

        ])
            ->assertRedirect(route('login'))
            ->assertStatus(302);

        $anotherUser = User::find($anotherUser->id);
        assertEquals('', $anotherUser->role);
    }


    /**
     * Undocumented function
     *
     * @return void
     */
    public function test_check_if_an_admin_cannot_change_role_of_another_user_admin()
    {
        $userAdmin1 = User::factory()->create();
        $userAdmin2 = User::factory()->create();
        $userAdmin1->role = 'admin';
        $userAdmin2->role = 'admin';

        $userAdmin1->save();
        $userAdmin2->save();

        $this->actingAs($userAdmin1)->post(route('users.change_role', $userAdmin2->id), ["role" => ""])
            ->assertForbidden();

        $userAdmin2->refresh();
        assertEquals('admin', $userAdmin2->role);
    }

    public function test_check_if_admin_can_delete_normal_user()
    {
        $admin1 = User::factory()->create();
        $normalUser = User::factory()->create();

        $admin1->role = 'admin';
        $normalUser->role = '';

        $admin1->save();
        $normalUser->save();

        $this->actingAs($admin1)->delete(route('users.destroy', $normalUser->id))
            ->assertOk()
            ->assertJson($normalUser->toArray());

        $checkUser = User::find($normalUser->id);
        $this->assertNull($checkUser);
    }

    public function test_check_if_admin_cannot_delete_another_admin()
    {
        $admin1 = User::factory()->create();
        $admin2 = User::factory()->create();

        $admin1->role = 'admin';
        $admin2->role = 'admin';

        $admin1->save();
        $admin2->save();

        $this->actingAs($admin1)->delete(route('users.destroy', $admin2->id))
            ->assertForbidden();

        $checkUser = User::find($admin2->id);
        $this->assertNotNull($checkUser);
        $this->assertEquals($admin2->toArray(), $checkUser->toArray());
    }

    public function test_check_if_normal_user_cannot_delete_normal_user()
    {
        $normalUser = User::factory()->create();
        $anotherUser = User::factory()->create();

        $normalUser->role = '';
        $normalUser->save();


        $anotherUser->role = '';
        $anotherUser->save();
        $adminData = $anotherUser->toArray();

        $this->actingAs($normalUser)->withSession(['banned' => false])->delete(route('users.destroy', $anotherUser->id))
            ->assertForbidden();

        $expectedAnotherUser = User::find($anotherUser->id);
        $this->assertEquals($adminData, $expectedAnotherUser->toArray());
        $this->assertNotNull($expectedAnotherUser);
    }

    public function test_check_if_normal_user_cannot_delete_admin()
    {
        $normalUser = User::factory()->create();
        $admin1 = User::factory()->create();

        $normalUser->role = '';
        $normalUser->save();


        $admin1->role = 'admin';
        $admin1->save();
        $adminData = $admin1->toArray();

        $this->actingAs($normalUser)->delete(route('users.destroy', $admin1->id))
            ->assertForbidden();

        $expectedAdminUser = User::find($admin1->id);
        $this->assertEquals($adminData, $expectedAdminUser->toArray());
        $this->assertNotNull($expectedAdminUser);
    }

    public function test_check_if_unauthenticated_user_cannot_delete_anybody()
    {
        $user = User::factory()->create();
        $user->role = "";
        $user->save();
        $userData = $user->toArray();

        $this->delete(route('users.destroy', $user->id))
            ->assertRedirect();

        $expectedUser = User::find($user->id);
        $this->assertNotNull($expectedUser->toArray());
        $this->assertEquals($userData, $expectedUser->toArray());
    }
}
