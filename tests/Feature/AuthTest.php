<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Test registering user
     * @return void
     */
    public function testRegister()
    {

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/register',
            [
                'first_name' => 'John',
                'last_name'=> 'Doe',
                'email' => 'John' . time() . '@test.com',
                'password' => '123321',
                'password_confirmation' => '123321'
            ]);

        $response->assertStatus(201);

    }
      /**
     * Test registering user with different confirmation password
     * @return void
     */
    public function testRegisterWrongPassword()
    {

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/register',
            [
                'first_name' => 'John',
                'last_name'=> 'Doe',
                'email' => 'John' . time() . '@test.com',
                'password' => '123321',
                'password_confirmation' => '1233dasd212'
            ]);

        $response->assertStatus(422);

    }

      /**
     * Test registering user with existing email
     * @return void
     */
    public function testRegisterExistingEmail()
    {
        $user = User::first();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/register',
            [
                'first_name' => 'John',
                'last_name'=> 'Doe',
                'email' => $user->email,
                'password' => '123321',
                'password_confirmation' => '123321'
            ]);

        $response->assertStatus(422);

    }



   /**
     * Test login user
     * @return void
     */
    public function testLogin()
    {
        $user = User::where('email','like', '%test.com')->first();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/login',
            [
                'email' => $user->email,
                'password' => '123321',
            ]);

        $response->assertStatus(200);

    }

    /**
     * Test login user with incorrect credentials
     * @return void
     */
    public function testLoginIncorrect()
    {
        $user = User::where('email','like', '%test.com')->first();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/login',
            [
                'email' => $user->email,
                'password' => 'ddd',
            ]);

        $response->assertStatus(401);

    }




}
