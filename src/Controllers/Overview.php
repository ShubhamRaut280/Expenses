<?php
namespace Simcify\Controllers;
use Simcify\Database;
use Simcify\Auth;

class Overview{

    /**
     * Get a sample view or redirect to it
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $stats = array();
        $title = __('pages.sections.overview');
        $user = Auth::user();
        $accounts = Database::table('accounts')->where('user', $user->id)->orderBy("id", false)->get();
        $categories = Database::table('categories')->where('user',$user->id)->where('type','expense')->orderBy("id", false)->get();
        $incomecategories = Database::table('categories')->where('user',$user->id)->where('type','income')->orderBy("id", false)->get();
        $account = new \StdClass();
        foreach ($accounts as $account) {
            $incomeTransactions = Database::table('income')->where('account', $account->id)->count('id','total')[0]->total;
            $expenseTransactions = Database::table('expenses')->where('account', $account->id)->count('id','total')[0]->total;
            $account->transactions = $incomeTransactions + $expenseTransactions;
        }

        $stats['spent'] = Database::table('expenses')->where('user', $user->id)->where('MONTH(`expense_date`)', date("m"))->sum('amount','total')[0]->total;
        if ($user->monthly_spending > 0) {
          $stats['percentage'] = round(($stats['spent'] / $user->monthly_spending) * 100);
        }else{
          $stats['percentage'] = 0;
        }

        $stats['income'] = Database::table('income')->where('user', $user->id)->where('MONTH(`income_date`)', date("m"))->sum('amount','total')[0]->total;
        $stats['expenses'] = Database::table('expenses')->where('user', $user->id)->where('MONTH(`expense_date`)', date("m"))->sum('amount','total')[0]->total;
        if ($stats['expenses'] > $stats['income']) {
            $stats['savings'] = 0;
        }else{
            $stats['savings'] = $stats['income'] - $stats['expenses'];
        }
        $stats['incomeTransactions'] = Database::table('income')->where('user', $user->id)->where('MONTH(`income_date`)', date("m"))->count('id','total')[0]->total;
        $stats['expenseTransactions'] = Database::table('expenses')->where('user', $user->id)->where('MONTH(`expense_date`)', date("m"))->count('id','total')[0]->total;
        $totalTransactions = $stats['incomeTransactions'] + $stats['expenseTransactions'];
        if ($totalTransactions > 0) {
          $stats['incomePercentage'] = round(($stats['incomeTransactions'] / $totalTransactions) * 100);
          $stats['expensePercentage'] = round(($stats['expenseTransactions'] / $totalTransactions) * 100);
        }else{
          $stats['incomePercentage'] = 0;
          $stats['expensePercentage'] = 0;
        }
        $reports = self::reports(date('Y-m-d', strtotime('today - 30 days')).' 23:59:59', date('Y-m-d').' 00:00:00');

        return view('overview',compact("user","accounts","categories","incomecategories","title","stats","reports"));
    }

    /**
     * Create account
     * 
     * @return Json
     */
    public function createaccount(){
        $data = array(
            'name'=>input('name'),
            'user'=>Auth::user()->id,
            'balance'=>input('balance'),
            'type'=>input('type'),
            'status'=>input('status')
        );
        Database::table('accounts')->insert($data);
        return response()->json(responder("success", __('pages.messages.alright'), __('overview.messages.add-success'), "reload()"));
    }

    /**
     * Update account view
     * 
     * @return Json
     */
    public function updateaccountview() {
        $account = Database::table('accounts')->where('id', input("accountid"))->first();
        return view('includes/ajax/account',compact('account'));
    }

    /**
     * Update account
     * 
     * @return Json
     */
    public function updateaccount(){
        $data = array(
            'name'=>input('name'),
            'balance'=>input('balance'),
            'type'=>input('type'),
            'status'=>input('status')
        );
        Database::table('accounts')->where('id',input('accountid'))->update($data);
        return response()->json(responder("success", __('pages.messages.alright'), __('overview.messages.edit-success'), "reload()"));
    }


    /**
     * Delete account
     * 
     * @return Json
     */
    public function deleteaccount(){
        Database::table('accounts')->where('id',input('accountid'))->delete();
        return response()->json(responder("success", __('pages.messages.alright'), __('overview.messages.delete-success'), "reload()"));
    }

    /**
     * Report
     * 
     * @return array
     */
    public function reports($from, $to){
        $reports = array();
        $user = Auth::user();
        $range = $from."' AND '".$to;
        $reports['income']['total'] = money(Database::table('income')->where("user", $user->id)->where('income_date','BETWEEN',$range)->sum("amount", "total")[0]->total);
        $reports['expenses']['total'] = money(Database::table('expenses')->where("user", $user->id)->where('expense_date','BETWEEN',$range)->sum("amount", "total")[0]->total);
        $reports['income']['count'] = Database::table('income')->where("user", $user->id)->where('income_date','BETWEEN',$range)->count("amount", "total")[0]->total;
        $reports['expenses']['count'] = Database::table('expenses')->where("user", $user->id)->where('expense_date','BETWEEN',$range)->count("amount", "total")[0]->total;
        $reports['expenses']['top'] = Database::table('expenses')->where("user", $user->id)->where('expense_date','BETWEEN',$range)->limit(3)->orderBy("amount", false)->get();


        if (!empty($reports['expenses']['top'])){
            foreach($reports['expenses']['top'] as $topExpense){
                $topExpense->amount = money($topExpense->amount);
            }
        }
 
        $begin = new \DateTime($from);
        $end = new \DateTime($to);
        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($begin, $interval ,$end);
        foreach ( $daterange as $dt ){
            $range = $dt->format( "Y-m-d" )." 00:00:00' AND '".$dt->format( "Y-m-d" )." 23:59:59";
            $reports['chart']['label'][] = $dt->format( "d F" );
            $reports['chart']['income'][] = Database::table('income')->where("user", $user->id)->where('income_date','BETWEEN',$range)->sum("amount", "total")[0]->total;
            $reports['chart']['expenses'][] = (Database::table('expenses')->where("user", $user->id)->where('expense_date','BETWEEN',$range)->sum("amount", "total")[0]->total * -1);
        }

        return $reports;
    }

    /**
     * Get Report
     * 
     * @return array
     */
    public function getreports(){
        $reports = self::reports(input("from").' 00:00:00', input("to").' 23:59:59');
        return response()->json(responder("success", "", "", "reports(".json_encode($reports).")", false));
    }


}
