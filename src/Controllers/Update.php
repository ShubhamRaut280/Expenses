<?php
namespace Simcify\Controllers;

use PDO;
use Simcify\Auth;
use Simcify\Database;
use DotEnvWriter\DotEnvWriter;

class Update {

    /**
     * Get settings view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user = Auth::user();
        if ($user->role != "admin") {
            return view('errors/404');   
        }
        $versions = self::versions(); 
        $currentVersion = env("APP_VERSION");
        $latest = $versions[$currentVersion];
        return view('update', compact("user","latest"));
    }

    /**
     * Get settings view
     * 
     * @return Json
     */
    public function scan() {
        header('Content-type: application/json');
        $currentVersion = env("APP_VERSION");
        $versions = self::versions(); 
        $updateTo = $versions[$currentVersion];
        if (is_null($updateTo)) {
            exit(json_encode(responder("warning", "Hmm", "You are running on the latest Version ".$updateTo)));
        }
        self::update($updateTo);
        exit(json_encode(responder("success", "Complete!", "Update successfully completed","reload()")));
    }

    /**
     * App versions
     * 
     * @return Array
     */
    public function versions() {
        return array(
                    "1.0" => "1.1",
                    "1.1" => "1.2",
                    "1.2" => "1.3",
                    "1.3" => NULL
                    );
    }

    /**
     * Update Signer
     * 
     * @return Json
     */
    public function update($version) {
        if (strpos(dirname(__FILE__),'\\') > 0)
		{
			$envPath = str_replace("src\Controllers", ".env", dirname(__FILE__));
		} else {
			$envPath = str_replace("src/Controllers", ".env", dirname(__FILE__));
		}
        $env = new DotEnvWriter($envPath);
        $env->castBooleans();
        $versionUpdates = file_get_contents(config("app.storage")."updates/".$version.".json");
        $updates = explode("@hellaplus", $versionUpdates);

        foreach ($updates as $update) {
            Database::table("users")->command($update);
        }
        
        // if($version == "1.3"){
        //     $useraccounts = Database::table('users')->get();
        //     foreach ($useraccounts as $useraccount) {
                
        //         $data = array(
        //             'name' => 'Salary',
        //             'type' => 'Income',
        //             'user' => $useraccount->id
        //         );
        //         Database::table('categories')->insert($data);
        //         $data = array(
        //             'name' => 'Investments',
        //             'type' => 'Income',
        //             'user' => $useraccount->id
        //         );
        //         Database::table('categories')->insert($data);
        //         $data = array(
        //             'name' => 'Donations',
        //             'type' => 'Income',
        //             'user' => $useraccount->id
        //         );
        //         Database::table('categories')->insert($data);

        //         $categoryid = Database::table('categories')->where('name', 'Salary')->where('type', 'income')->where('user', $useraccount->id)->first();
        //         $update = "update `income` set `category` = ".$categoryid." where `income_group` = 'Salary' and `user` = ".$useraccount->id.";";
        //         Database::table("income")->command($update);
        //         $categoryid = Database::table('categories')->where('name', 'Investments')->where('type', 'income')->where('user', $useraccount->id)->first();
        //         $update = "update `income` set `category` = ".$categoryid." where `income_group` = 'Investments' and `user` = ".$useraccount->id.";";
        //         Database::table("income")->command($update);
        //         $categoryid = Database::table('categories')->where('name', 'Doncations')->where('type', 'income')->where('user', $useraccount->id)->first();
        //         $update = "update `income` set `category` = ".$categoryid." where `income_group` = 'Donations' and `user` = ".$useraccount->id.";";
        //         Database::table("income")->command($update);
        //         $categoryid = 0;
        //         $update = "update `income` set `category` =  0 where `income_group` = 'Other' and `user` = ".$useraccount->id.";";
        //         Database::table("users")->command($update);
        //     }
        
        // }

        $env->set("APP_VERSION", $version);
        $env->save();
    }


}
