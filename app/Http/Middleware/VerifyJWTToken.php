<?php

namespace App\Http\Middleware;

use Closure;

use JWTAuth;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;


class VerifyJWTToken
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
		//$test = '123';
		//header('test: Bearer ' . $test);
        try{
            $token = JWTAuth::getToken();
            if ($token) {
                if (! $user = JWTAuth::parseToken()->authenticate() ) {
                    return response()->json(['code' => 400, 'massage' => 'user not found','data' => null]);
                }
            }else{
                return response()->json(['code' => 400, 'massage' => 'Token not provided','data' => null]);
            }

        } catch (TokenExpiredException $e) {
            // If the token is expired, then it will be refreshed and added to the headers

            try {
                $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                $user = JWTAuth::setToken($refreshed)->toUser();
                header('Authorization: Bearer ' . $refreshed);
            } catch (JWTException $e) {
                return response()->json(['code' => 400, 'massage' => 'Something went wrong','data' => null]);
            }catch (TokenInvalidException $e) {
                return response()->json(['code' => 400, 'massage' => 'Token invalid','data' => null]);
            }
        }catch (JWTException $e) {
            return response()->json(['code' => 400, 'massage' => 'Something went wrong','data' => null]);
        }catch (TokenInvalidException $e) {
            return response()->json(['code' => 400, 'massage' => 'token invalid','data' => null]);
        }
        return $next($request);
    }
}
