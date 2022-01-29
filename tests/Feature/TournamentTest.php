<?php

namespace Tests\Feature;


use Tests\TestCase;
use App\Models\User;
use App\Enums\StatusCodeEnum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TournamentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_generate_pin()
    {
        $payload = [
            "phone_number" => env('SIGNUP_PHONE_NUMBER')
        ];

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' =>  'Bearer ' . $this->generate_token(),
        ])->postJson('api/v1/generate-phone-verification-pin', $payload);

        $response->assertStatus(StatusCodeEnum::CREATED);
    }

    public function test_create_tournament()
    {
        $payload = [
            "name" => "tournament" . rand(10, 10000),
            "point" => 5
        ];
        $response = $this->withHeaders([
            'Authorization' =>  'Bearer ' . $this->generate_token(),
        ])->post('api/v1/create-tournament', $payload);
        $response->assertStatus(StatusCodeEnum::CREATED);
    }

    public function test_invite_friend()
    {
        $payload = [
            "username" => "isaacss",
            "tournament_id" => 1
        ];

        $response = $this->withHeaders([
            'Authorization' =>   'Bearer ' . $this->generate_token(),
        ])->post('api/v1/invite-friend', $payload);

        $response->assertStatus(StatusCodeEnum::OK);
    }

    private function generate_token()
    {
        $user = User::factory()->create();
        return \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);
    }
}
