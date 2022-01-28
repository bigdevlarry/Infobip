<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Enums\StatusCodeEnum;
use App\Exceptions\CustomException;
use App\Repositories\User\UserRepository;

class VerifyPhoneNumber
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    private $user;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }


    public function handle(Request $request, Closure $next)
    {
        if(!$this->user->getAuthenticatedUser()['verify_phone_number']){
            throw new CustomException(StatusCodeEnum::BAD_REQUEST, null, 'Verify your phone number');
        }

        return $next($request);
    }
}
