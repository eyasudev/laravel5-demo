<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

/**
 * Class RoleCheck
 * @package App\Http\Middleware
 */
class RoleCheck
{
    /**
     * Creates a new instance of the middleware.
     *
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $roles, $custom_code = 401, $custom_message = "You don't have enough privileges to access this section, please contact your system administrator.")
    {
        if ($custom_code == 401) {
            if (!$request->user()->hasRole(explode('|', $roles))) {
                abort( $custom_code, $custom_message );
            }
        } else {
            return response()->view('errors.common-error', [ 'message' => $custom_message, "error_code" => $custom_code ], $custom_code);
        }

        return $next($request);
    }
}
