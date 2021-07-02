<?php

namespace App\Repositories\User;

interface UserInterface
{
    public function register(array $request);

    public function login(array $request);

    public function viewUser(int $id);

    public function getAuthenticatedUser();

    public function deactivateUser(int $id);

    public function resetPassword(array $request);
}
