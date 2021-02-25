<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\FinancialMovement;
use \App\Flash;
use \App\Auth;

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
        $_SESSION['incomesCategories']=FinancialMovement::getIncomeCategories();
        
        View::renderTemplate('AddFinancialMovement/AddIncome.html',[
            'IncomeCategories'=> $_SESSION['incomesCategories'],
            'userId'=>$_SESSION['user_id']
        ]);
    }

    public function addIncomeAction()
    {
        $income = new FinancialMovement($_POST);
        if(!isset($_SESSION['incomesCategories'])){
            $_SESSION['incomesCategories']=FinancialMovement::getIncomeCategories();
        }
        if($income->addIncome()){
            Flash::addMessage('Income added.');
            $this->redirect('/addFinancialMovement/AddIncomeForm');
        }else{
            Flash::addMessage('Income has not been added.','warning');
            
            
            View::renderTemplate('AddFinancialMovement/AddIncome.html',[
                'IncomeCategories'=>$_SESSION['incomesCategories'],
                'userId'=>$_SESSION['user_id'],
                'income'=>$income
            ]);
        }
    }

    public function addExpenseFormAction(){
        $_SESSION['expenseCategories']=FinancialMovement::getExpenseCategories();
        $_SESSION['paymentMethods']=FinancialMovement::getPaymentMethods();
        
        View::renderTemplate('AddFinancialMovement/AddExpense.html',[
            'expenseCategories'=> $_SESSION['expenseCategories'],
            'paymentMethods'=>$_SESSION['paymentMethods'],
            'userId'=>$_SESSION['user_id']
        ]);
    }

    public function addExpenseAction(){
        $expense = new FinancialMovement($_POST);
        if(!isset($_SESSION['expenseCategories'])||!isset($_SESSION['paymentMethods'])){
            $_SESSION['expenseCategories']=FinancialMovement::getExpenseCategories();
            $_SESSION['paymentMethods']=FinancialMovement::getPaymentMethods();
        }
        if($expense->addExpense()){
            Flash::addMessage('Expense added');
            $this->redirect('/addFinancialMovement/AddExpenseForm');
        }else{
            Flash::addMessage('Expense has not been added','warning');
            View::renderTemplate('AddFinancialMovement/AddExpense.html',[
                'expenseCategories'=>$_SESSION['expenseCategories'],
                'paymentMethods'=>$_SESSION['paymentMethods'],
                'userId'=>$_SESSION['user_id'],
                'expense'=>$expense
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
