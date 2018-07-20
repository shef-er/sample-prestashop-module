<?php

class AdminTestController extends ModuleAdminController
{
    /*
    public function __construct()
    {
        $this->table         = 'admintest';
        $this->className     = 'AdminTest';

        parent::__construct();
    }*/

    public function initContent()
    {
        parent::initContent();

        $this->show_toolbar = false;
        $this->context->smarty->assign(array(
            'content' => $this->renderSettings() . $this->renderForm() . $this->displayFields(),
        ));
    }

}