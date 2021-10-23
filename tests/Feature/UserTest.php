<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class UserTest extends TestCase
{

    // use RefreshDatabase;

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
            ->post(route('users.change_role'), [
                "id" => $anotherUser->id,
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
        // certify that user is an normal user
        $anotherUser = User::factory()->create();
        $anotherUser->role = '';
        $anotherUser->save();

        $request = $this->actingAs($normalUser)->withSession(['banned' => false])
            ->post(route('users.change_role'), [
                "id" => $anotherUser->id,
                "role" => "admin",

            ])
            ->assertStatus(403);

        $anotherUser = User::find($anotherUser->id);
        assertEquals('', $anotherUser->role);
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

        $request = $this->post(route('users.change_role'), [
            "id" => $anotherUser->id,
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
        //TODO
        assertEquals(true, false);
    }
}
