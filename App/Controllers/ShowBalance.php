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
        $detailedIncomeTable=$balance->getTableFromDB();

        $testArray=[1,2,3,4,5,6];

        $limits=Balance::getTodaysDate();   
        View::renderTemplate('Balance/showBalance.html',[
            'firstLimit'=>$limits[0],
            'secondLimit'=>$limits[1],
            'detailedIncomeTable'=>$detailedIncomeTable
        ]);
    }


}
