<?php

namespace App\Repositories\User;

use Carbon\Carbon;
use App\Models\User;
use JWTAuth;
use App\Enums\StatusCodeEnum;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\CustomException;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserRepository implements UserInterface
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function register(array $request)
 	{
 		$user = $this->user->create([
            'name' => $request['name'],
            'username' => $request['username'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);

        return $data = [
            'access_token' => JWTAuth::fromUser($user),
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl')
        ];
 	}

    public function login(array $request)
    {
        try {
                if (! $token = JWTAuth::attempt($request)) {
                    throw new CustomException(StatusCodeEnum::BAD_REQUEST, null, 'Invalid credentials');
                }
            } catch (JWTException $e) {
                throw new CustomException(StatusCodeEnum::SERVER_ERROR, null, $e);
            }

        return $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl')
        ];
    }

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                throw new CustomException(StatusCodeEnum::NOT_FOUND, null, 'User not found');
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                throw new CustomException($e->getStatusCode(), null, 'Token expired');

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                throw new CustomException($e->getStatusCode(), null, 'Token invalid');

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
                throw new CustomException($e->getStatusCode(), null, 'Token absent');
        }

        return $user;
    }

    public function viewUser(int $id)
    {
    	$user = $this->user->find($id);
        if(!$user)
            throw new CustomException(StatusCodeEnum::NOT_FOUND, null, 'User not found');

        return $user;
    }   

    public function deactivateUser(int $id)
    {
    	$user = $this->user->find($id);
        if($user){
            $user->deleted_at = Carbon::now();
            return $user->save();
        }
            
        throw new CustomException(StatusCodeEnum::NOT_FOUND, null, 'User not found');        
    }

    public function resetPassword(array $request)
    {
        $user = $this->user->whereEmail($request['email'])->first();
        if($user){
            $user->password = Hash::make($request['password']);
            return $user->save();
        }
        
        throw new CustomException(StatusCodeEnum::NOT_FOUND, null, 'User not found');
    }
}