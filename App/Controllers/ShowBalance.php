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
        var_dump($_POST);
        View::renderTemplate('Balance/showBalance.html',[
            'firstLimit'=>Balance::getTodaysDate()[0],
            'secondLimit'=>Balance::getTodaysDate()[1],
            'detailedIncomeTable'=>$balance->getDetailedIncomes(),
            'detailedExpenseTable'=>$balance->getDetailedExpenses(),
            'dataForIncomesCategoryChart'=>$balance->countAmountDependsOnCategory(),
        ]);
    }
}
