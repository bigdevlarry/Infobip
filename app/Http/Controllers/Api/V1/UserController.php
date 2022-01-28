<?php

namespace App\Http\Controllers\Api\V1;

use App\Facade\AppUtils;
use Illuminate\Http\Request;
use App\Enums\StatusCodeEnum;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepository;


class UserController extends Controller
{
    private $user;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    public function login(Request $request)
    {
        $requestBody = [
            'email' => 'required|email', 
            'password' => 'required',  
        ];

        AppUtils::validation($request->all(), $requestBody);

        $user = $this->user->login($request->all());

        return AppUtils::setResponse(StatusCodeEnum::OK, $user, "Login successful");
    }

    public function register(Request $request)
    {
        $requestBody = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'username' => 'required|string|unique:users',
            'password' => 'required|string|min:6|confirmed', 
        ];

        AppUtils::validation($request->all(), $requestBody);

        $user = $this->user->register(array_merge($request->all(), ['token' => $request->header('token')]));

        return AppUtils::setResponse(StatusCodeEnum::CREATED,  $user, "User created");
        
    }

    public function resetPassword(Request $request)
    {
        $requestBody = [
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed', 
        ];

        AppUtils::validation($request->all(), $requestBody);

        $user = $this->user->resetPassword($request->all());

        return AppUtils::setResponse(StatusCodeEnum::OK, null, "Password Reset Successfully");
    }

    public function generatePhoneVerificationPin(Request $request)
    {
        $requestBody = [
            'phone_number' => 'required|unique:users',
        ];

        AppUtils::validation($request->all(), $requestBody);

        $user = $this->user->generateVerificationPin($request->phone_number, $this->user->getAuthenticatedUser()['id']);

        return AppUtils::setResponse(StatusCodeEnum::OK, null, "Pin sent to registered phone number");
    }

    public function verifyPhoneNumber(Request $request)
    {
        $requestBody = [
            'pinCode' => 'required|string'
        ];

        AppUtils::validation($request->all(), $requestBody);
        $user = $this->user->verifySecuredAuthenticationPin($this->user->getAuthenticatedUser()['pin_id'],  $request->pinCode);
        return AppUtils::setResponse(StatusCodeEnum::OK, null, "Phone number verified");
    }
}
