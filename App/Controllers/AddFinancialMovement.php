<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\FinancialMovement;
use \App\Flash;

class AddFinancialMovement extends Authenticated
{

    /**
     * Require the user to be authenticated before giving access to all methods in the controller
     *
     * @return void
     */
    /*
    protected function before()
    {
        $this->requireLogin();
    }
    */

    /**
     * Items index
     *
     * @return void
     */
    public function addIncomeFormAction()
    {
        View::renderTemplate('AddFinancialMovement/AddIncome.html',[
            'IncomeCategories'=> FinancialMovement::getIncomeCategories(),
            'userId'=>$_SESSION['user_id']
        ]);
    }

    public function addIncomeAction()
    {
        $financialMovement = new FinancialMovement($_POST);

        if($financialMovement->addIncome()){
            Flash::addMessage('Income added.');
            View::renderTemplate('AddFinancialMovement/AddIncome.html',[
                'IncomeCategories'=> FinancialMovement::getIncomeCategories(),
                'userId'=>$_SESSION['user_id']
            ]);
        }
    }

    /**
     * Show an item
     *
     * @return void
     */
    public function showAction()
    {
        echo "show action";
    }
}
