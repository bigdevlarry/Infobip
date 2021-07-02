<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use App\Enums\StatusCodeEnum;
use App\Exceptions\CustomException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                throw new CustomException(StatusCodeEnum::BAD_REQUEST, null, 'Invalid token');
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                throw new CustomException(StatusCodeEnum::BAD_REQUEST, null, 'Expired token');
            }else{
                throw new CustomException(StatusCodeEnum::BAD_REQUEST, null, 'Authorization Token not found');
            }
        }
        return $next($request);
    }
}