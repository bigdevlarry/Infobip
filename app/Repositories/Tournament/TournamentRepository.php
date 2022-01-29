<?php

namespace App\Repositories\Tournament;

use App\Models\User;
use App\Models\Matches;
use App\Models\Tournament;
use App\Jobs\SendInviteJob;
use App\Enums\StatusCodeEnum;
use App\Enums\ResultStatusEnum;
use App\Exceptions\CustomException;

class TournamentRepository implements TournamentInterface
{
    protected $tournament, $user;

    public function __construct(Tournament $tournament, User $user)
    {
        $this->tournament = $tournament;
        $this->user = $user;
    }

    public function createTournament(array $request)
    {
        return $this->tournament->firstOrCreate($request);
    }

    public function inviteFriend (array $request, int $currentUser)
    {
        $user = $this->user->whereUsername($request['username'])->first();
        $tournament = $this->tournament->find($request['tournament_id']);
        if(!$user){
           throw new CustomException(StatusCodeEnum::NOT_FOUND, null, 'User not found'); 
        }

        if($currentUser == $user->id){
            throw new CustomException(StatusCodeEnum::BAD_REQUEST, null, 'You can\'t invite yourself to a game');
        }
        
        SendInviteJob::dispatch($user, $tournament);
    }

    public function submitResult (array $request)
    {   
        $tournament = $this->tournament->find($request['tournament_id']);

        info($request['current_user']);
        if(!$tournament_creator = $tournament->creator == $request['current_user']){
            throw new CustomException(StatusCodeEnum::BAD_REQUEST, null, 'Only tournament creators can submit a result');
        }

        $status = strtolower($request['status']);

        $this->matchResultValidation($status, $request['first_player_score'], $request['second_player_score'], $tournament->point);
        
        if($request['second_player_id'] == $request['current_user']){
            throw new CustomException(StatusCodeEnum::BAD_REQUEST, null, 'First and Second player should be different');
        }
      
        return Matches::create([
            'tournament_id'=> $request['current_user'],
            'first_player_score' => $request['first_player_score'],
            'second_player_score' => $request['second_player_score'],
            'status' => $status
        ]); 
    }

    private function matchResultValidation($status, $firstPlayerScore, $secondPlayerScore, $tournamentPoint)
    {
        if(!in_array($status, [ResultStatusEnum::WIN, ResultStatusEnum::DRAW, ResultStatusEnum::LOSE])){
            throw new CustomException(StatusCodeEnum::BAD_REQUEST, null, 'Status can only be win, draw or lose');
        }

        if($firstPlayerScore > $tournamentPoint){
            throw new CustomException(StatusCodeEnum::BAD_REQUEST, null, 'first player score is greater than tournament point');
        }

        if($secondPlayerScore > $tournamentPoint){
            throw new CustomException(StatusCodeEnum::BAD_REQUEST, null, 'second player score is greater than tournament point');
        }   
    }
}