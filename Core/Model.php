<?php

namespace Core;

use PDO;
use App\Config;

/**
 * Base model
 *
 * PHP version 7.0
 */
abstract class Model
{

    /**
     * Get the PDO database connection
     *
     * @return mixed
     */
    protected static function getDB()
    {
        static $db = null;

        if ($db === null) {
            $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8';
            $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);

            // Throw an Exception when an error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $db;
    }

    protected static function getDefaultExpensesTable(){
        return Config::DEFAULT_EXPENSES_CATEGORY_TABLE;
    }

    protected static function getUserTableWithExpensesCategory(){
        return Config::USER_TABLE_WITH_EXPENSES_CATEGORY;
    }

    protected static function getDefaultIncomesTable(){
        return Config::DEFAULT_INCOMES_CATEGORY_TABLE;
    }

    protected static function getUserTableWithIncomesCategory(){
        return Config::USER_TABLE_WITH_INCOMES_CATEGORY;
    }

    protected static function getDefaultPaymentMethods(){
        return Config::DEFAULT_PAYMENT_METHOD_TABLE;
    }

    protected static function getUserTableWithPaymentMethods(){
        return Config::USER_TABLE_WITH_PAYMENT_METHODS;
    }

    protected static function getTableWithIncomes(){
        return Config::TABLE_WITH_INCOMES;
    }

    protected static function getTableWithExpenses(){
        return Config::TABLE_WITH_EXPENSES;
    }
}
