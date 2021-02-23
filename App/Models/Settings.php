<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Models\Balance;
use \App\Models\FinancialMovement;


class Settings extends \Core\Model{

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };

        $this->incomeCategoriesID=Balance::getIncomeCategories();
        $this->expenseCategoriesID=Balance::getExpenseCategories();
        
        $this->incomeCategoriesNames=[];
        foreach($this->incomeCategoriesID as $incomeCategory){
            $this->incomeCategoriesNames[]=FinancialMovement::getCategoryOrMethodNameById($incomeCategory, static::getUserTableWithIncomesCategory());
        }

        $this->expenseCategoriesNames=[];
        foreach($this->expenseCategoriesID as $expenseCategory){
            $this->expenseCategoriesNames[]=FinancialMovement::getCategoryOrMethodNameById($expenseCategory, static::getUserTableWithExpensesCategory());
        }

        //var_dump($this->incomeCategoriesNames);
        
    }
    

}