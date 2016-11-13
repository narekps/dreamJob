<?php

namespace App\Controller;

use App\Entity\User;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends BaseController
{

    public function indexAction()
    {
        return new ViewModel();
    }
}
