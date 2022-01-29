<?php

namespace App\Http\Controllers\Api\V1;

use App\Facade\AppUtils;
use Illuminate\Http\Request;
use App\Enums\StatusCodeEnum;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepository;
use App\Repositories\Tournament\TournamentRepository;


class TournamentController extends Controller
{
    private $tournament, $user;

    public function __construct(UserRepository $user, TournamentRepository $tournament)
    {
        $this->user = $user;
        $this->tournament = $tournament;
    }

    public function create(Request $request)
    {
        $requestBody = [
            'name' => 'required|unique:tournaments', 
            'point' => 'nullable|integer',  
        ];

        AppUtils::validation($request->all(), $requestBody);

        $request['creator'] = $this->user->getAuthenticatedUser()['id'];

        $tournament = $this->tournament->createTournament($request->all());

        return AppUtils::setResponse(StatusCodeEnum::CREATED, $tournament, "Tournament created");
    }


    public function sendInvite (Request $request)
    {
        $requestBody = [
            'username' => 'required|string|exists:users,username',
            'tournament_id' => 'required|integer|exists:tournaments,id'
        ];

        AppUtils::validation($request->all(), $requestBody);

        $request['current_user'] = $this->user->getAuthenticatedUser()['id'];

        $user = $this->tournament->inviteFriend($request->all(), $request['current_user']);

        return AppUtils::setResponse(StatusCodeEnum::OK, null, "Invitation sent");
    }

    public function submitResult(Request $request)
    {
       $requestBody = [
            'tournament_id' => 'required|integer|exists:tournaments,id',
            'first_player_score' => 'required|integer',
            'second_player_score' => 'required|integer',
            'status' => 'required|string',
            'second_player_id' => 'required|integer|exists:users,id'
        ];

        AppUtils::validation($request->all(), $requestBody);

        $request['current_user'] = $this->user->getAuthenticatedUser()['id'];

        $result = $this->tournament->submitResult($request->all());

        return AppUtils::setResponse(StatusCodeEnum::OK, $result, "Result submitted");
    }
}
