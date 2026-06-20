<?php

namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
class AuthTest extends TestCase
{
    use RefreshDatabase;
    protected $seed = true;
    /**
     * A basic test example.
     */
    public function test_login_route_with_empty_data(): void
    {
        $response = $this->postJson(route('api.login'));
        //dd($response->json());
        $response->assertStatus(422);
        $this->assertEquals($response->json()['message'], "The email field is required. (and 1 more error)");
        $this->assertArrayHasKey("email", $response->json()['errors']);
        $this->assertArrayHasKey("password", $response->json()['errors']);
    }

    public function test_user_can_not_login_with_empty_password()
    {
        $data = [
            'email'=>'gaitan.jorgegonzalo959@gmail.com',
            'password'=>''
        ];
        $response = $this->postJson(route('api.login'), $data);
        $response->assertStatus(422);
        $this->assertEquals($response->json()['message'], "The password field is required.");
        $this->assertArrayHasKey("password", $response->json()['errors']);
    }

    public function test_user_can_not_login_with_empty_email()
    {
        $data = [
            'email'=>'',
            'password'=>'Prueba123'
        ];
        $response = $this->postJson(route('api.login'), $data);
        $response->assertStatus(422);
        $this->assertEquals($response->json()['message'], "The email field is required.");
        $this->assertArrayHasKey("email", $response->json()['errors']);
    }

    public function test_user_can_not_login_with_wrong_data(){
        $data = [
            'email' => 'testX@gmail.com.ar',
            'password'=>'Prueba123'
        ];
        $response = $this->postJson(route('api.login'), $data);
        $response->assertStatus(400);
        $this->assertEquals($response->json()['message'], "Invalid credentials");
    }

    public function test_user_can_login_successfull(){
        $data = [
            'email' => 'gaitan.jorgegonzalo959@gmail.com',
            'password'=>'Prueba123'
        ];
        $response = $this->postJson(route('api.login'), $data);
        $response->assertStatus(200);
        $this->assertEquals($response->json()['message'], "access granted");
        //dd($response);
    }
}