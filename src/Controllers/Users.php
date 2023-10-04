<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Mail;
use Simcify\Auth;
use Simcify\File;
use Simcify\Str;

class Users{

    /**
     * Get users page view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $title = __('pages.profile-menu.users');
        $user = Auth::user();
        if ($user->role == "user") {
          return view('errors/404');
        }
        $accounts = Database::table('accounts')->where('user', $user->id)->orderBy("id", false)->get();
        $categories = Database::table('categories')->where('user',$user->id)->orderBy("id", false)->get();
        $users = Database::table('users')->where('role', 'user')->get();
        return view('users',compact("user", "title", "users","accounts","categories"));
    }


    /**
     * Create user account
     * 
     * @return Json
     */
    public function create() {
        $password = rand(111111, 999999);
        if (!empty(input('avatar'))) {
            $upload = File::upload(
                input('avatar'), 
                "avatar",
                array(
                    "source" => "base64",
                    "extension" => "png"
                )
            );
            $avatar = $upload['info']['name'];
        }else{
            $avatar = '';
        }
        $signup = Auth::signup(
            array(
                "fname" => escape(input('fname')),
                "lname" => escape(input('lname')),
                "phone" => escape(input('phone')),
                "email" => escape(input('email')),
                "address" => escape(input('address')),
                "avatar" => $avatar,
                "role" => "user",
                "password" => Auth::password($password)
            ), 
            array(
                "uniqueEmail" => input('email')
            )
        );
        if ($signup["status"] == "success") {
            Mail::send(
                input('email'),
                sprintf(__('settings.email-content.new-account-title'), env("APP_NAME")),
                array(
                    "title" => sprintf(__('settings.email-content.new-account-title'), env("APP_NAME")),
                    "subtitle" => sprintf(__('settings.email-content.new-account-subtitle'), env("APP_NAME")),
                    "buttonText" => __('settings.email-content.new-account-button'),
                    "buttonLink" => env("APP_URL"),
                    "message" => sprintf(__('settings.email-content.new-account-message'), input('email'), $password, env("APP_NAME"))
                ),
                "withbutton"
            );
            return response()->json(responder("success", __('settings.messages.account-created'), __('settings.messages.account-created-success'),"reload()"));
        }else{
            if (!empty(($avatar))) {
                File::delete($avatar, "avatar");
            }
            return response()->json(responder("error", __('pages.messages.oops'), $signup["message"]));
        }
    }

    /**
     * Delete user account
     * 
     * @return Json
     */
    public function delete() {
        $account = Database::table("users")->where("id", input("userid"))->first();
        if (!empty($account->avatar)) {
            File::delete($account->avatar, "avatar");
        }
        Database::table("users")->where("id", input("userid"))->delete();
        return response()->json(responder("success", __('settings.messages.account-deleted'), __('settings.messages.account-delete-success'),"reload()"));
    }

    /**
     * User update view
     * 
     * @return Json
     */
    public function updateview() {
      $user = Database::table("users")->where("id", input("userid"))->first();
        return view('includes/ajax/user', compact("user"));
    }

    /**
     * Update user account
     * 
     * @return Json
     */
    public function update() {
        $account = Database::table("users")->where("id", input("userid"))->first();
        foreach (input()->post as $field) {
            if ($field->index == "avatar") {
                if (!empty($field->value)) {
                    $avatar = File::upload(
                        $field->value, 
                        "avatar",
                        array(
                            "source" => "base64",
                            "extension" => "png"
                        )
                    );

                    if ($avatar['status'] == "success") {
                        if (!empty($account->avatar)) {
                            File::delete($account->avatar, "avatar");
                        }
                        Database::table(config('auth.table'))->where("id" , input("userid"))->update(array("avatar" => $avatar['info']['name']));
                    }
                }
                continue;
            }
            if ($field->index == "csrf-token" || $field->index == "userid") {
                continue;
            }
            Database::table(config('auth.table'))->where("id" , input("userid"))->update(array($field->index => escape($field->value)));
        }
        return response()->json(responder("success", __('pages.messages.alright'), __('settings.messages.account-updated-success'),"reload()"));
    }

}
