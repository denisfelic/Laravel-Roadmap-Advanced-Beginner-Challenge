<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{

    use RefreshDatabase;

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


        $request = $this->actingAs($userAdmin)->withSession(['banned' => false])->get('/users/create')
            ->assertStatus(200);
    }
}
