<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Facade\AppUtils;
use Illuminate\Http\Request;
use App\Enums\StatusCodeEnum;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
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

        $user = $this->user->register($request->all());

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

    public function deactivate (Request $request)
    {
        $this->validateUserId($request);

        $user = $this->user->deactivateUser($request->id);

        return AppUtils::setResponse(StatusCodeEnum::OK, null, "User deactivated");
    }

    public function view(Request $request)
    {
        $this->validateUserId($request);

        $user = $this->user->viewUser($request->id);

        return AppUtils::setResponse(StatusCodeEnum::OK, $user, "Success");
    }

    private function validateUserId ($request)
    {
        $requestBody = [
            'id' => ['required'],
        ];
        return AppUtils::validation($request->all(), $requestBody);
    }
}
