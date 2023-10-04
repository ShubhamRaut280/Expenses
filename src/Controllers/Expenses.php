<?php
namespace Simcify\Controllers;
use Simcify\Auth;
use Simcify\Database;

class Expenses{

    /**
     * Get a sample view or redirect to it
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $stats = array();
        $title = __('pages.sections.expenses');
        $user = Auth::user();
        $accounts = Database::table('accounts')->where('user', $user->id)->orderBy("id", false)->get();
        $categories = Database::table('categories')->where('user',$user->id)->where('type','expense')->orderBy("id", false)->get();
        $incomecategories = Database::table('categories')->where('user',$user->id)->where('type','income')->orderBy("id", false)->get();
        $expenses = Database::table("expenses")->where("expenses`.`user", $user->id)->leftJoin("accounts", "expenses.account","accounts.id")->leftJoin("categories", "expenses.category","categories.id")->orderBy("expenses.id", false)->get("`expenses.id`", "`expenses.expense_date`", "`expenses.amount`", "`expenses.title`", "`accounts.name`", "`categories.name` as category");
        $stats['spent'] = Database::table('expenses')->where('user', $user->id)->where('MONTH(`expense_date`)', date("m"))->sum('amount','total')[0]->total;
        if ($user->monthly_spending > 0) {
          $stats['percentage'] = round(($stats['spent'] / $user->monthly_spending) * 100);
        }else{
          $stats['percentage'] = 0;
        }
        return view('expenses',compact("user", "title", "stats", "accounts","expenses","stats","categories", "incomecategories"));
    }

    /**
     * Add expense
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
          'expense_date'=>date('Y-m-d',strtotime(input('expense_date')))
      );
      Database::table('expenses')->insert($data);
        if (input('account') != "00") {
          self::balance(input('account'), input('amount'), "minus");
        }
      return response()->json(responder("success", __('pages.messages.alright'), __('expenses.messages.add-success'), "reload()"));
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
     * Expense update modal
     * 
     * @return \Pecee\Http\Response
     */
    public function updateview() {
        $user = Auth::user();
        $categories = Database::table('categories')->where('user',$user->id)->where('type','expense')->get();
        $accounts = Database::table('accounts')->where('user', $user->id)->orderBy("id", false)->get();
        $expense = Database::table('expenses')->where('id', input("expenseid"))->first();
        return view('includes/ajax/expense',compact("expense","accounts","categories"));
    }

    /**
     * Update expense
     * 
     * @return Json
     */
    public function update(){
      $expense = Database::table('expenses')->where('id', input("expenseid"))->first();
      $user = Auth::user();
      $data = array(
          'title'=>escape(input('title')),
          'amount'=>input('amount'),
          'account'=>input('account'),
          'category'=>input('category'),
          'expense_date'=>date('Y-m-d',strtotime(input('expense_date')))
      );
      if (input('amount') != $expense->amount && $expense->account > 0) {
        self::balance($expense->account, $expense->amount, "minus");
        self::balance($expense->account, input('amount'), "plus");
      }
      Database::table('expenses')->where('id',input('expenseid'))->update($data);
      return response()->json(responder("success", __('pages.messages.alright'), __('expenses.messages.edit-success'), "reload()"));
    }

    /**
     * Delete expense record
     * 
     * @return Json
     */
    public function delete(){
      $expense = Database::table('expenses')->where('id', input("expenseid"))->first();
      if (!empty($expense->account)) {
        self::balance($expense->account, $expense->amount, "plus");
      }
      Database::table('expenses')->where('id',input('expenseid'))->delete();
      return response()->json(responder("success", __('pages.messages.alright'), __('expenses.messages.delete-success'), "reload()"));
    }

}
