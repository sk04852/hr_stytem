<?php

namespace Tests\Unit;
use Tests\TestCase;

class UsersTest extends TestCase
{
    private $tempToken_ = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6NDIwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYwMTk5NDM2MiwiZXhwIjoxNjAyMDMwMzYyLCJuYmYiOjE2MDE5OTQzNjIsImp0aSI6IlpkbE1NaWFFa3hGU09vYzYiLCJzdWIiOjEsInBydiI6IjgzN2Y4NTgzN2VkMWFmNjBlNjY5MzBkOWU4ZWFhMWY5ODNjYTA4YTMifQ.UsGeOIE0F4KiUHpgdWGCQEEV_NVmk_K6uHHeCOn7uwA";
    /** @test */
    public function auth_login() {
        $this->withoutExceptionHandling();
        $response = $this->post('/api/auth/login', ["email"=>"admin@crm.com", "password"=> 123456]);
        $jwt = json_decode($response->getContent());
        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'token_type']);
    }


    /** @test */
    public function test_returns_response_with_valid_request()
    {
        $this->withoutExceptionHandling();
        $leadsRequest = 
             $this->withHeader('Authorization', 'Bearer ' . $this->tempToken_)
             ->get('/api/leads');
        $leadsRequest->assertStatus(200);
        $leadsRequest->assertJsonStructure(['leads', 'count']);
        $leads = $leadsRequest->getContent();
    }

    /** @test */
    public function test_fetech_leads_fails_with_401()
    {
        $this->withoutExceptionHandling();
        $leadsRequest = 
                $this->get('/api/leads');
        $leadsRequest->assertJsonStructure(['error', 'token_status']);
        $leadsRequest->assertStatus(401);
    }
}


