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
        $balance->getTableFromDB();


        $limits=Balance::getTodaysDate();   
        View::renderTemplate('Balance/showBalance.html',[
            'firstLimit'=>$limits[0],
            'secondLimit'=>$limits[1]
        ]);
    }
}
