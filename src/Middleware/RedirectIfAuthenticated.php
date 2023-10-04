<?php
namespace Simcify\Middleware;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use Simcify\Auth;

class RedirectIfAuthenticated implements IMiddleware {

    /**
     * Redirect the user if they are already authenticated
     * 
     * @param   \Pecee\Http\Request $request
     * @return  \Pecee\Http]Request
     */
    public function handle(Request $request) {

        Auth::remember();

        if (Auth::check()) {
            $request->setRewriteUrl(url('Overview@get'));
        } else {
            // Check for a locale set and set the locale
            if (in_array($request->getMethod(), ['get', 'GET']) && input('locale', false)) {
                config('app.locale.default', input('locale'));
            }
        }
        return $request;

    }
}
