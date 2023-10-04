<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Auth;

class Budget{

    /**
     * Get budget page
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $stats = array();
        $title = __('pages.sections.budget');
        $user = Auth::user();
        $accounts = Database::table('accounts')->where('user', $user->id)->orderBy("id", false)->get();
        $categories = Database::table('categories')->where('user',$user->id)->where('type','expense')->orderBy("id", false)->get();
        $incomecategories = Database::table('categories')->where('user',$user->id)->where('type','income')->orderBy("id", false)->get();
        $stats['allocated'] = Database::table('categories')->where('user', $user->id)->sum("budget", "total")[0]->total;
        $stats['spent'] = Database::table('expenses')->where('user', $user->id)->where('MONTH(`expense_date`)', date("m"))->sum('amount','total')[0]->total;
        if ($user->monthly_spending > 0) {
          $stats['percentage'] = round(($stats['spent'] / $user->monthly_spending) * 100);
        }else{
          $stats['percentage'] = 0;
        }
        $budgets = Database::table('categories')->where("user", $user->id)->where('type','expense')->get();
        $budget = new \StdClass();
        foreach($budgets as $budget){
            $budget->spent = Database::table('expenses')->where('category', $budget->id)->where('MONTH(`expense_date`)', date("m"))->sum('amount','total')[0]->total;
            $budget->lastmonth = Database::table('expenses')->where('category', $budget->id)->where('MONTH(`expense_date`)', date("m") - 1)->sum('amount','total')[0]->total;
            $budget->transactions = Database::table('expenses')->where('category', $budget->id)->where('MONTH(`expense_date`)', date("m"))->count('id','total')[0]->total;
            if ($budget->budget > 0) {
              $budget->percentage = round(($budget->spent / $budget->budget) * 100);
            }else{
              $budget->percentage = 0;
            }

            // Chart data
            $stats['thismonth'][] = "{value:".$budget->spent.", name:'".$budget->name."'}";
            $stats['lastmonth'][] = "{value:".$budget->lastmonth.", name:'".$budget->name."'}";
        }
        
        return view('budget',compact("title","user","stats","accounts","categories","incomecategories",'budgets'));
    }

    /**
     * Adjust budget
     * 
     * @return Js0n
     */
    public function adjust() {
        $user = Auth::user();
        $goals = array(
            'monthly_spending'=>input('monthly_spending'),
            'annual_spending'=>input('annual_spending'),
            'monthly_saving'=>input('monthly_saving'),
            'monthly_earning'=>input('monthly_earning')
        );
        Database::table('users')->where("id", $user->id)->update($goals);
        if (isset($_POST["category"])) {
            foreach ($_POST["category"] as $index => $category) {
                Database::table('categories')->where("id", $category)->update(array("budget" => $_POST["budget"][$index]));
            }
        }
        return response()->json(responder("success",  __('pages.messages.alright'), __('budget.messages.adjust-success'), "reload()"));
    }

}
