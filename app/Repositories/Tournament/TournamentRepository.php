<?php

namespace App\Repositories\Tournament;

use App\Models\Tournament;
use App\Models\Match;
use App\Models\User;
use App\Models\MatchRound;
use App\Jobs\InviteFriend;
use App\Enums\StatusCodeEnum;
use App\Mail\InviteFriendMail;
use App\Enums\ResultStatusEnum;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Mail;

class TournamentRepository implements TournamentInterface
{
    protected $tournament, $match, $matchRound, $user;

    public function __construct(Tournament $tournament, Match $match, MatchRound $matchRound, User $user)
    {
        $this->tournament = $tournament;
        $this->match = $match;
        $this->user = $user;
        $this->matchRound = $matchRound;
    }

    public function createTournament(array $request)
    {
        return $this->tournament->firstOrCreate($request);
    }

    public function inviteFriend (string $username, int $currentUser)
    {
        $user = $this->user->whereUsername($username)->first();

        if($currentUser == $user->id){
            throw new CustomException(StatusCodeEnum::BAD_REQUEST, null, 'You can\'t invite yourself to a game');
        }

        Mail::to($user->email)->send(new InviteFriendMail($user));

        return true;
    }

    public function submitResult (array $request)
    {   
        $tournament = $this->tournament->find($request['tournament_id']);
       
        if(!$tournament_creator = $tournament->creator == $request['current_user']){
            throw new CustomException(StatusCodeEnum::BAD_REQUEST, null, 'Only tournament creators can submit a result');
        }

        $status = strtolower($request['status']);

        $this->matchResultValidation($status, $request['first_player_score'], $request['second_player_score'], $tournament->point);
        
        if($request['second_player_id'] == $request['current_user']){
            throw new CustomException(StatusCodeEnum::BAD_REQUEST, null, 'First and Second player should be different');
        }

        $match = $this->match->firstOrCreate([
            'tournament_id'=> $request['current_user'],
            'first_player_score' => $request['first_player_score'],
            'second_player_score' => $request['second_player_score'],
            'status' => $status
        ]); 

        $this->matchRound->firstOrCreate([
            'match_id' => $match->id,
            'first_player_id' => $request['current_user'],
            'second_player_id' => $request['second_player_id']
        ]);

        return $match;
    }

    public function updateResult(array $request)
    {   
        $match = Match::with('tournament')->whereId($request['match_id'])->first();

        $status = strtolower($request['status']);

        $this->matchResultValidation($status, $request['first_player_score'], $request['second_player_score'], $match->tournament->point);

        return $match->update([
                'first_player_score' => $request['first_player_score'], 
                'second_player_score' => $request['second_player_score'], 
                'status' => $status
            ]);
    }

    public function viewLeaderboard()
    {
        return true;
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