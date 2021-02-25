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
        $this->paymentMethodsCategoriesID=Balance::getPaymentMethods();
        
        $this->incomeCategoriesNames=[];
        foreach($this->incomeCategoriesID as $incomeCategory){
            $this->incomeCategoriesNames[]=FinancialMovement::getCategoryOrMethodNameById($incomeCategory, static::getUserTableWithIncomesCategory());
        }

        $this->expenseCategoriesNames=[];
        foreach($this->expenseCategoriesID as $expenseCategory){
            $this->expenseCategoriesNames[]=FinancialMovement::getCategoryOrMethodNameById($expenseCategory, static::getUserTableWithExpensesCategory());
        }

        $this->paymentMethodsNames=[];
        foreach($this->paymentMethodsCategoriesID as $paymentMethod){
            $this->paymentMethodsNames[]=FinancialMovement::getCategoryOrMethodNameById($paymentMethod, static::getUserTableWithPaymentMethods());
        }
    }

    public static function changeCategoryOrPaymentMethod()
    {
        echo $_POST['newName']."<br>";
        echo $_POST['whatToChange']."<br>";
        echo $_POST['categoryOrPaymentMethodName'];
        
        $userId=$_SESSION['user_id'];
        $newName=$_POST['newName'];
        
        $nameToChange=$_POST['categoryOrPaymentMethodName'];
        if($_POST['whatToChange']=="incomeCategory"){
            $tableWithCategories=static::getUserTableWithIncomesCategory();
        }elseif($_POST['whatToChange']=='paymentMethod'){
            $tableWithCategories=static::getUserTableWithPaymentMethods();
        }elseif($_POST['whatToChange']=="expenseCategory"){
            $tableWithCategories=static::getUserTableWithExpensesCategory();
            if(isset($_POST['limit'])){
                static::addLimit(FinancialMovement::getCategoryOrMethodIdByName($nameToChange, $tableWithCategories),$_POST['limit'],$tableWithCategories);
            }
        }
        if(!empty($_POST['newName'])){
            $id=FinancialMovement::getCategoryOrMethodIdByName($nameToChange, $tableWithCategories);
            $sql="UPDATE ".$tableWithCategories." SET name=:name WHERE id=:id ";
            $db=static::getDB();
            $stmt=$db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':name', $newName, PDO::PARAM_STR);
            $stmt->execute();
        }
    }
    protected static function addLimit($id,$limit,$tableWithCategories){
        $sql="UPDATE ".$tableWithCategories." SET expense_limit=:expense_limit  WHERE id=:id ";
        $db=static::getDB();
        $stmt=$db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':expense_limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
    }
}