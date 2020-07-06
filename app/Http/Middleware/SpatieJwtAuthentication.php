<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Response;
use \Illuminate\Http\Response as Res;
use Illuminate\Support\Facades\Auth;

class SpatieJwtAuthentication extends BaseMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role, $permission) {


        if (!$token = $this->auth->setRequest($request)->getToken()) {
            return Response::json([
                        'status' => 'error',
                        'status_code' => Res::HTTP_BAD_REQUEST,
                        'message' => 'Token Not Provided!',
            ]);
        }

        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
            return Response::json([
                        'status' => 'error',
                        'status_code' => Res::HTTP_BAD_REQUEST,
                        'message' => 'Token Expired!',
            ]);
        } catch (JWTException $e) {
            return Response::json([
                        'status' => 'error',
                        'status_code' => Res::HTTP_BAD_REQUEST,
                        'message' => 'Token Expired!',
            ]);
        }

        if (!$user) {
            return Response::json([
                        'status' => 'error',
                        'status_code' => Res::HTTP_NOT_FOUND,
                        'message' => 'User Not Gound!',
            ]);
        }

        $permissions = is_array($permission) ? $permission : explode('|', $permission);
        $roles = is_array($role) ? $role : explode('|', $role);

        if (!Auth::user()->hasPermissionTo($permission)) {
            return Response::json([
                        'status' => 'error',
                        'status_code' => Res::HTTP_UNAUTHORIZED,
                        'message' => 'Unauthorized Access!',
            ]);
        }

        //$role = auth()->user()->getRoleNames();
        //\Log::info(app('auth')->user()->can($permission));
        //\Log::info(Auth::user()->hasPermissionTo($permission));
        //\Log::info(Auth::user()->hasPermissionTo($permission));
        //return $role->hasPermissionTo($permission);
        //\Log::info($roles);
        //\Log::info($permissions);
        //\Log::info(app('auth')->user());
        //\Log::info(auth()->user()->getRoleNames());



        return $next($request);
    }

}
