<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Auth;

class Income{

    /**
     * Get income page view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $title = __('pages.sections.income');
        $user = Auth::user();
        $stats = array();
        $accounts = Database::table('accounts')->where('user', $user->id)->orderBy("id", false)->get();
        $categories = Database::table('categories')->where('user',$user->id)->where('type','expense')->orderBy("id", false)->get();
        $incomecategories = Database::table('categories')->where('user',$user->id)->where('type','Income')->orderBy("id", false)->get();
        $income = Database::table("income")->where("income`.`user", $user->id)->leftJoin("accounts", "income.account","accounts.id")->leftJoin("categories", "income.category","categories.id")->orderBy("income.id", false)->get("`income.id`", "`income.income_date`", "`income.category`", "`income.amount`", "`income.title`", "`accounts.name`", "`categories.name` as categoryname");
        $stats['earned'] = Database::table('income')->where('user', $user->id)->where('MONTH(`income_date`)', date("m"))->sum('amount','total')[0]->total;
        if ($user->monthly_earning > 0) {
          $stats['percentage'] = round(($stats['earned'] / $user->monthly_earning) * 100);
        }else{
          $stats['percentage'] = 0;
        }
        return view('income',compact("user","title","accounts","categories","incomecategories","income","stats"));
    }

    /**
     * Add income
     * 
     * @return Json
     */
    public function add() {
      $user = Auth::user();
        $data = array(
            'title'=>escape(input('title')),
            'user'=>$user->id,
            'amount'=>input('amount'),
            'account'=>input('account'),
            'category'=>input('category'),
            'income_date'=>date('Y-m-d',strtotime(input('income_date')))
        );
        Database::table('income')->insert($data);
        if (input('account') != "00") {
          self::balance(input('account'), input('amount'), "plus");
        }
        return response()->json(responder("success", __('pages.messages.alright'), __('income.messages.add-success'), "reload()"));
    }

    /**
     * Account balance
     * 
     * @return true
     */
    public function balance($accountId, $amount, $action) {
      $account = Database::table('accounts')->where('id', $accountId)->first();
      if ($action == "plus") {
        $balance = $account->balance + $amount;
      }elseif ($action == "minus") {
        $balance = $account->balance - $amount;
      }
      Database::table('accounts')->where('id', $accountId)->update(array("balance" => $balance));
      return true;
    }


    /**
     * Income update modal
     * 
     * @return \Pecee\Http\Response
     */
    public function updateview() {
        $user = Auth::user();
        $accounts = Database::table('accounts')->where('user', $user->id)->orderBy("id", false)->get();
        $incomecategories = Database::table('categories')->where('user',$user->id)->where('type','Income')->orderBy("id", false)->get();
        $income = Database::table('income')->where('id', input("incomeid"))->first();
        return view('includes/ajax/income',compact("income","accounts","incomecategories"));
    }

    /**
     * Update income
     * 
     * @return Json
     */
    public function update(){
      $income = Database::table('income')->where('id', input("incomeid"))->first();
      $user = Auth::user();
      $data = array(
          'title'=>escape(input('title')),
          'amount'=>input('amount'),
          'account'=>input('account'),
          'category'=>input('category'),
          'income_date'=>date('Y-m-d',strtotime(input('income_date')))
      );
      if (input('amount') != $income->amount && $income->account > 0) {
        self::balance($income->account, $income->amount, "minus");
        self::balance($income->account, input('amount'), "plus");
      }
      Database::table('income')->where('id',input('incomeid'))->update($data);
      return response()->json(responder("success", __('pages.messages.alright'), __('income.messages.edit-success'), "reload()"));
    }

    /**
     * Delete income record
     * 
     * @return Json
     */
    public function delete(){
      $income = Database::table('income')->where('id', input("incomeid"))->first();
      if (!empty($income->account)) {
        self::balance($income->account, $income->amount, "minus");
      }
      Database::table('income')->where('id',input('incomeid'))->delete();
      return response()->json(responder("success", __('pages.messages.alright'), __('income.messages.delete-success'), "reload()"));
    }

}
