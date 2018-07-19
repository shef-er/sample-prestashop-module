<?php

class TestModuleTestFrontController extends ModuleFrontController {

    public function initContent() {
        parent::initContent();

        $this->setTemplate('testmodule.tpl');
    }

}