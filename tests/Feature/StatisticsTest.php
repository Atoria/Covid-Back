<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StatisticsTest extends TestCase
{
    /**
     * Check for unauthorized summary
     *
     * @return void
     */
    public function testUnauthorizedSummary()
    {
        $response = $this->get('/api/summary', ['accept' => 'application/json']);
        $response->assertStatus(401);
    }
     /**
     * Check for unauthorized statistics
     *
     * @return void
     */
    public function testUnauthorizedStatistics()
    {
        $response = $this->get('/api/stats', ['accept' => 'application/json']);
        $response->assertStatus(401);
    }

    /**
     * Check for authorized summary
     *
     * @return void
     */
    public function testAuthorizedSummary()
    {
        $user = User::first();
        $this->actingAs($user);
        $response = $this->get('/api/summary', ['accept' => 'application/json']);
        $response->assertStatus(200);
    }

    /**
     * Check for authorized stats
     *
     * @return void
     */
    public function testAuthorizedStatus()
    {
        $user = User::first();
        $this->actingAs($user);
        $response = $this->get('/api/stats', ['accept' => 'application/json']);
        $response->assertStatus(200);
    }


    /**
     * Check for unauthorized statistics
     *
     * @return void
     */
    public function testNotFound()
    {
        $response = $this->get('/api/test', ['accept' => 'application/json']);
        $response->assertStatus(404);
    }


}
