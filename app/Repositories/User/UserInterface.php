<?php

namespace App\Repositories\User;

interface UserInterface
{
    public function register(array $request);

    public function login(array $request);

    public function generateVerificationPin(string $phoneNumber, string $id);

    public function verifySecuredAuthenticationPin(string $pinId, string $pinCode);

    public function getAuthenticatedUser();

    public function resetPassword(array $request);
}
