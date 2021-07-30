<?php

namespace App\Repositories\Tournament;

interface TournamentInterface
{
    public function createTournament(array $request);

    public function inviteFriend (array $request, int $currentUser);

    public function submitResult (array $request);

    public function updateResult (array $request);

    public function viewLeaderBoard();
}
