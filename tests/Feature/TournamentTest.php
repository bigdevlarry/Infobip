<?php

namespace Tests\Feature;


use Tests\TestCase;
use App\Models\User;
use App\Models\Tournament;
use App\Enums\StatusCodeEnum;

class TournamentTest extends TestCase
{
    public function test_generate_pin()
    {
        $payload = [
            "phone_number" => env('SIGNUP_PHONE_NUMBER')
        ];
       $this->payload_response(route('generate-pin'), $payload, StatusCodeEnum::CREATED);
    }

    public function test_create_tournament()
    {
        $payload = [
            "name" => "tournament-" . rand(10, 10000),
            "point" => 5
        ];
        $this->payload_response(route('create-tournament'), $payload, StatusCodeEnum::CREATED);  
    }

    public function test_invite_friend()
    {
        $payload = [
            "username" => User::latest()->first()->username,
            "tournament_id" => Tournament::latest()->first()->id,
        ];
        $this->payload_response(route('invite-friend'), $payload, StatusCodeEnum::OK);
    }

    private function generate_token() :string
    {
        $user = User::factory()->create();
        return \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);
    }

    private function payload_response(string $route, array $payload, int $status_code) : Object
    {
        $response = $this->withHeaders([
            'Authorization' =>   'Bearer ' . $this->generate_token(),
        ])->postJson($route, $payload);   
        return  $response->assertStatus($status_code);
    }
}
