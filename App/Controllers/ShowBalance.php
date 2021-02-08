<?php

namespace App\Controllers;

use \Core\View;
use \App\Flash;
use \App\Auth;
use \App\Models\Balance;

class ShowBalance extends Authenticated
{
    public function showBalanceAction()
    {
        $balance= new Balance($_POST);
        if(isset($_POST['customPeriodOfTime'])){
            if(!$balance->validateDates()){
                $balance->setDatesToCurrentMonth();
                Flash::addMessage("Balance limits dates did not change.",'warning');
            }
        }
        $balance->countingHeaderOfBalance();
        View::renderTemplate('Balance/showBalance.html',[
            'balance'=>$balance,
            'detailedIncomeTable'=>$balance->getDetailedIncomes(),
            'detailedExpenseTable'=>$balance->getDetailedExpenses(),
            'dataForIncomesCategoryChart'=>$balance->countAmountDependsOnCategoryOfIncomes(),
            'dataForExpensesCategoryChart'=>$balance->countAmountDependsOnCategoryOfExpenses()
        ]);
        
    }
}
