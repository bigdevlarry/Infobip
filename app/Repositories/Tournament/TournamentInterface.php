<?php

namespace App\Repositories\Tournament;

interface TournamentInterface
{
    public function createTournament(array $request);

    public function inviteFriend (string $username, int $currentUser);

    public function submitResult (array $request);

    public function updateResult (array $request);

    public function viewLeaderBoard();
}
