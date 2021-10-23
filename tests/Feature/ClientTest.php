<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if unauthenticated users cannot access any client route
     * An is redirect to login page
     *
     * @return void
     */
    public function test_check_unauthenticated_cannot_access_any_client_routes()
    {

        $client = Client::factory()->create();

        $this->get(route('client.create'))->assertRedirect(route('login'))->assertStatus(302);
        $this->get(route('client.edit', $client->id))->assertRedirect(route('login'), $client->id)->assertStatus(302);
        $this->get(route('client.show', $client->id))->assertRedirect(route('login'), $client->id)->assertStatus(302);


        $this->post(route('client.store'), [])->assertRedirect(route('login'))->assertStatus(302);
        $this->patch(route('client.update', $client->id), [])->assertRedirect(route('login'))->assertStatus(302);
        $this->delete(route('client.destroy', $client->id))->assertRedirect(route('login'))->assertStatus(302);
    }


    /**
     * Only admins can access create client page
     *
     * @return void
     */
    public function test_check_if_normal_user_cannot_access_client_create_page()
    {
        $normalUser = User::factory()->create();
        $normalUser->role = '';

        $this->actingAs($normalUser)->get(route('client.create'))
            ->assertStatus(403);
    }

    public function test_check_if_normal_user_cannot_access_clients_page()
    {
        $normalUser = User::factory()->create();
        $normalUser->role = '';

        $this->actingAs($normalUser)->get(route('client.index'))
            ->assertStatus(403);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function test_check_if_normal_user_cannot_access_client_edit_page()
    {
        $normalUser = User::factory()->create();
        $normalUser->role = '';
        $client = Client::factory()->create();

        $this->actingAs($normalUser)->get(route('client.edit', $client->id))
            ->assertStatus(403);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function test_check_if_normal_user_cannot_delete_an_client()
    {
        $normalUser = User::factory()->create();
        $client = Client::factory()->create();

        $this->actingAs($normalUser)->delete(route('client.destroy', $client->id))
            ->assertStatus(403);

        assertEquals($client->id, Client::find($client->id)->id);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function test_check_if_normal_user_cannot_edit_an_client()
    {
        $normalUser = User::factory()->create();
        $client = Client::factory()->create();
        $client->VAT = 'test';
        $client->save();

        $this->actingAs($normalUser)->patch(route('client.update', $client->id), [])
            ->assertForbidden();

        $this->assertEquals('test', Client::find($client->id)->VAT);
    }


    /**
     * Undocumented function
     *
     * @return void
     */
    public function test_check_if_normal_user_cannot_create_an_client()
    {
        $normalUser = User::factory()->create();

        $this->actingAs($normalUser)->post(route('client.store'), [
            "company_name" => "TestCompany",
            "VAT" => "11",
            "address" => "Test Road 112"
        ])
            ->assertForbidden();

        $client = Client::where('company_name', "TestCompany")->first();

        $this->assertNull($client);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function test_check_if_normal_user_can_access_client_show_page()
    {
        $normalUser = User::factory()->create();
        $client = Client::factory()->create();

        $this->actingAs($normalUser)->get(route('client.show', $client->id))
            ->assertOk();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function test_check_if_an_admin_user_can_access_client_create_page()
    {
        $adminUser = User::factory()->create();
        $adminUser->role = 'admin';

        $this->actingAs($adminUser)->get(route('client.create'))
            ->assertOk();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function test_check_if_an_admin_user_can_access_client_edit_page()
    {
        $adminUser = User::factory()->create();
        $adminUser->role = 'admin';
        $client = Client::factory()->create();

        $this->actingAs($adminUser)->get(route('client.edit', $client->id))
            ->assertOk();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function test_check_if_an_admin_user_can_access_client_show_page()
    {
        $adminUser = User::factory()->create();
        $adminUser->role = 'admin';
        $client = Client::factory()->create();

        $this->actingAs($adminUser)->get(route('client.show', $client->id))
            ->assertOk();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function test_check_if_an_admin_user_can_update_an_client()
    {
        $adminUser = User::factory()->create();
        $adminUser->role = 'admin';
        $client = Client::factory()->create();
        $clientUpdateData = [
            "company_name" => "name22",
            "VAT" => "VAT22",
            "address" => "Company22 Address 22",
        ];


        $this->actingAs($adminUser)->patch(route('client.update', $client->id), $clientUpdateData)
            ->assertOk()
            ->assertJson($clientUpdateData);


        $client->refresh();
        $clientPropertiesDiff = array_diff_assoc($clientUpdateData, $client->toArray());
        // check that all user properties have been correctly updated
        $this->assertEquals(0, sizeof($clientPropertiesDiff));
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function test_check_if_an_admin_user_can_delete_an_client()
    {

        $adminUser = User::factory()->create();
        $adminUser->role = 'admin';
        $client = Client::factory()->create();
        $deletedClientId = $client->id;

        $this->actingAs($adminUser)->delete(route('client.destroy', $client->id))
            ->assertJson($client->toArray())
            ->assertOk();


        $this->assertNull(Client::find($deletedClientId));
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function test_check_if_an_admin_user_can_create_an_client()
    {
        $adminUser = User::factory()->create();
        $adminUser->role = 'admin';

        $clientStoreData = [
            "company_name" => "TestCompany42",
            "VAT" => "42",
            "address" => "Fake Address Testing42"
        ];

        $this->actingAs($adminUser)->post(route('client.store'), $clientStoreData)
            ->assertCreated();
        //   ->assertJson($clientStoreData)

        $createdClient = Client::where('company_name', $clientStoreData["company_name"])->first();

        $this->assertEquals($clientStoreData["company_name"], $createdClient->company_name);
        $this->assertEquals($clientStoreData["VAT"], $createdClient->VAT);
        $this->assertEquals($clientStoreData["address"], $createdClient->address);
    }
}
