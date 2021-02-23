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
    }

    public static function changeCategoryOrPaymentMethod()
    {
        echo $_POST['newName']."<br>";
        echo $_POST['whatToChange']."<br>";
        $newName=$_POST['newName'];

        $userId=$_SESSION['user_id'];
        echo $userId."<br>";
        
        $name="Nowa nazwa";
        $id=92;
        $sql="UPDATE incomes_category_assigned_to_users SET name=:name WHERE id=:id ";
        $db=static::getDB();
        $stmt=$db->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}