<?php

namespace App;

use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'factories' => [
        Controller\IndexController::class                                       => InvokableFactory::class,
        Controller\FileController::class                                        => InvokableFactory::class,
        Controller\ProfileController::class                                     => InvokableFactory::class,
    ],
];
