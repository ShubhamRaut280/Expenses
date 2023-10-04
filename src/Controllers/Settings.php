<?php
namespace Simcify\Controllers;

use Simcify\File;
use Simcify\Auth;
use Simcify\Database;
use DotEnvWriter\DotEnvWriter;

class Settings {
    
    /**
     * Get settings view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $title      = __('pages.profile-menu.settings');
        $user       = Auth::user();
        $timezones  = Database::table("timezones")->get();
        $currencies = Database::table("currencies")->get();
        $accounts = Database::table('accounts')->where('user', $user->id)->orderBy("id", false)->get();
        $categories = Database::table('categories')->where('user',$user->id)->orderBy("name", true)->get();
        return view('settings', compact("user", "title", "timezones", "currencies","accounts","categories"));
    }
    
    /**
     * Update profile on settings page
     * 
     * @return Json
     */
    public function updateprofile() {
        $account = Database::table(config('auth.table'))->where("email", input("email"))->first();
        if (!empty($account) && $account->id != Auth::user()->id) {
            return response()->json(responder("error", __('pages.messages.oops'), input("email") . " " . __('settings.messages.already-exists')));
        }
        
        foreach (input()->post as $field) {
            if ($field->index == "avatar") {
                if (!empty($field->value)) {
                    $avatar = File::upload($field->value, "avatar", array(
                        "source" => "base64",
                        "extension" => "png"
                    ));
                    
                    if ($avatar['status'] == "success") {
                        if (!empty(Auth::user()->avatar)) {
                            File::delete(Auth::user()->avatar, "avatar");
                        }
                        Database::table(config('auth.table'))->where("id", Auth::user()->id)->update(array(
                            "avatar" => $avatar['info']['name']
                        ));
                    }
                }
                continue;
            }
            
            if ($field->index == "csrf-token") {
                continue;
            }
            
            Database::table(config('auth.table'))->where("id", Auth::user()->id)->update(array(
                $field->index => escape($field->value)
            ));
        }
        
        return response()->json(responder("success", __('pages.messages.alright'), __('settings.messages.profile-edit-success'), "reload()"));
    }
    
    /**
     * Update company on settings page
     * 
     * @return Json
     */
    public function updatecompany() {
        foreach (input()->post as $field) {
            if ($field->index == "csrf-token") {
                continue;
            }
            
            Database::table("companies")->where("id", Auth::user()->company)->update(array(
                $field->index => escape($field->value)
            ));
        }
        exit(json_encode(responder("success", __('pages.messages.alright'), __('settings.messages.company-edit-success'))));
    }
    
    
    /**
     * Update password on settings page
     * 
     * @return Json
     */
    public function updatepassword() {
        $user = Auth::user();
        if (hash_compare($user->password, Auth::password(input("current")))) {
            Database::table(config('auth.table'))->where("id", $user->id)->update(array(
                "password" => Auth::password(input("password"))
            ));
            return response()->json(responder("success", __('pages.messages.alright'), __('settings.messages.password-edit-success'), "reload()"));
        } else {
            return response()->json(responder("error", __('pages.messages.oops'), __('settings.messages.password-incorrect')));
        }
    }
    
    /**
     * Update system settings
     * 
     * @return Json
     */
    public function updatesystem() {
        header('Content-type: application/json');
        if (strpos(dirname(__FILE__),'\\') > 0)
		{
			$envPath = str_replace("src\Controllers", ".env", dirname(__FILE__));
		} else {
			$envPath = str_replace("src/Controllers", ".env", dirname(__FILE__));
		}
        $env = new DotEnvWriter($envPath);
        $env->castBooleans();
        $enableToggle = array(
            "PKI_STATUS",
            "CERTIFICATE_DOWNLOAD",
            "NEW_ACCOUNTS",
            "ALLOW_NON_PDF",
            "USE_CLOUD_CONVERT",
            "SHOW_SAAS"
        );
        foreach ($enableToggle as $key) {
            if (empty(input($key))) {
                $env->set($key, 'Disabled');
            }
        }
        if (empty(input("SMTP_AUTH"))) {
            $env->set("SMTP_AUTH", false);
        }
        $env->set("MAIL_SENDER", input("APP_NAME") . " <" . input("MAIL_FROM") . ">");
        foreach (input()->post as $field) {
            if ($field->index == "APP_LOGO") {
                if (!empty($field->value)) {
                    $upload = File::upload($field->value, "app", array(
                        "source" => "base64",
                        "extension" => "png"
                    ));
                    
                    if ($upload['status'] == "success") {
                        File::delete(env("APP_LOGO"), "app");
                        $env->set("APP_LOGO", $upload['info']['name']);
                        $env->save();
                    }
                }
                continue;
            }
            if ($field->index == "APP_ICON") {
                if (!empty($field->value)) {
                    $upload = File::upload($field->value, "app", array(
                        "source" => "base64",
                        "extension" => "png"
                    ));
                    
                    if ($upload['status'] == "success") {
                        File::delete(env("APP_ICON"), "app");
                        $env->set("APP_ICON", $upload['info']['name']);
                        $env->save();
                    }
                }
                continue;
            }
            
            if ($field->index == "csrf-token") {
                continue;
            }
            
            $env->set($field->index, $field->value);
            $env->save();
        }
        
        exit(json_encode(responder("success", __('pages.messages.alright'), __('settings.messages.settings-edit-success'), "reload()")));
    }
    
    /**
     * Add category
     * 
     * @return Json
     */
    public function addcategory() {
        $data = array(
            'name' => input('category'),
            'type' => input('type'),
            'user' => Auth::user()->id
        );
        Database::table('categories')->insert($data);
        return response()->json(responder("success", __('pages.messages.alright'), __('settings.messages.category-add-success'), "reload()"));
    }
    
    
    /**
     * Category message
     * 
     * @return Json
     */
    public function deletecategory() {
        Database::table("categories")->where("id", input("categoryid"))->delete();
        return response()->json(responder("success", __('settings.messages.category-deleted'), __('settings.messages.category-delete-success'), "reload()"));
    }
    
    /**
     * Update category view
     * 
     * @return Json
     */
    public function updatecategoryview() {
        $category = Database::table('categories')->where('id', input("categoryid"))->first();
        return view('includes/ajax/editcategory', compact("category"));
    }
    
    /**
     * Update category
     * 
     * @return Json
     */
    public function updatecategory() {
        $data = array(
            'name' => input('category'),
            'type' => input('type')
        );
        Database::table('categories')->where('id', input("categoryid"))->update($data);
        return response()->json(responder("success", __('pages.messages.alright'), __('settings.messages.category-edit-success'), "reload()"));
    }
    
}