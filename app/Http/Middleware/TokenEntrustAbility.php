<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Log;
use App\Role;

class TokenEntrustAbility extends BaseMiddleware {

    public function handle($request, Closure $next, $roles, $permissions, $validateAll = false) {

        $role = Role::get()->toArray();
        $array = array_column($role, 'role_name');
        $roles = implode("|", $array);

        if (!$token = $this->auth->setRequest($request)->getToken()) {
            return $this->respond('tymon.jwt.absent', 'Token Not Provided', 400);
        }

        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
            return $this->respond('tymon.jwt.expired', 'token_expired', $e->getStatusCode(), [$e]);
        } catch (JWTException $e) {
            return $this->respond('tymon.jwt.invalid', 'token_invalid', 408, [$e]);
        }

        if (!$user) {
            return $this->respond('tymon.jwt.user_not_found', 'User Not Found!', 404);
        }

        if (!$request->user()->ability(explode('|', $roles), explode('|', $permissions), array('validate_all' => $validateAll))) {
            return $this->respond('tymon.jwt.invalid', 'token_invalid', 401, 'Unauthorized');
        }



        $this->events->fire('tymon.jwt.valid', $user);

        return $next($request);
    }

}
