<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Settings;

class Setting extends Authenticated
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
    public function indexAction(){
        $setting=new Settings($_POST);
        View::renderTemplate('Setting/index.html',[
            'setting'=>$setting
        ]);
    }

    public function changeCategoryOrPaymentMethodAction(){
        Settings::changeCategoryOrPaymentMethod();
    }

    public function addNewCategoryOrPaymentMethodAction(){
        Settings::addNewCategoryOrPaymentMethod();
    }

    public function deleteCategoryOrPaymentMethodAction(){
        Settings::deleteCategoryOrPaymentMethod();
    }

    /**
     * Add a new item
     *
     * @return void
     */
    public function newAction()
    {
        echo "new action";
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
