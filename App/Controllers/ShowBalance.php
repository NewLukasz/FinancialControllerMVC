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
        if(isset($_POST['fisrtLimitDate'])){
            echo "<br>".$_POST['fisrtLimitDate'];
        }
        View::renderTemplate('Balance/showBalance.html',[
            'balance'=>$balance,
            'detailedIncomeTable'=>$balance->getDetailedIncomes(),
            'detailedExpenseTable'=>$balance->getDetailedExpenses(),
            'dataForIncomesCategoryChart'=>$balance->countAmountDependsOnCategory(),
        ]);
    }
}
