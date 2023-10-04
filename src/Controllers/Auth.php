<?php
namespace Simcify\Controllers;

use Simcify\Auth as Authenticate;
use Pecee\Http\Request;
use Simcify\Database;
use Simcify\Str;

class Auth {
    
    /**
     * Get a sample view or redirect to it
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        if (!isset($_GET['secure'])) {
            redirect(url("Auth@get")."?secure=true");
        }
        return view('auth');
    }
    
    /**
     * User signin
     * 
     * @return Json
     */
    public function signin() {
        $signin = Authenticate::login(input('email'), input('password'), array(
            "rememberme" => true,
            "redirect" => url(''),
            "status" => "Active"
        ));
        
        return response()->json($signin);
    }
    
    /**
     * Create a user account
     * 
     * @return Json
     */
    public function signup() {
        $register = Authenticate::signup(array(
            "fname" => input('fname'),
            "lname" => input('lname'),
            "email" => input('email'),
            "password" => Authenticate::password(input('password')),
            "role" => 'user'
        ), array(
            "authenticate" => true,
            "redirect" => url(""),
            "uniqueEmail" => input('email')
        ));
        
        return response()->json($register);
        
    }
    
    
    /**
     * signout User
     * 
     * @return \Pecee\Http\Response
     */
    public function signout() {
        Authenticate::deauthenticate();
        redirect(url('Auth@get'));
    }
    
    /**
     * Forgot password - send reset password email
     * 
     * @return Json
     */
    public function forgot() {
        $forgot = Authenticate::forgot(input('email'), env('APP_URL') . "/reset/[token]");
        return response()->json($forgot);
    }
    
    
    /**
     * Get reset password page
     * 
     * @return \Pecee\Http\Response
     */
    public function resetpage($token) {
        $title = __('pages.sections.reset-password');
        return view('reset', compact("token", "title"));
    }
    
    /**
     * Reset password
     * 
     * @return Json
     */
    public function reset() {
        $reset = Authenticate::reset(input('token'), input('password'));
        return response()->json($reset);
    }
    
    
}