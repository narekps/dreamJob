<?php

namespace App;

use App\Factory\Repository\User as UserRepositoryFactory;
use App\Factory\Repository\File as FileRepositoryFactory;

return [
    'factories' => [
        // repositories
        UserRepositoryFactory::class                                      => UserRepositoryFactory::class,
        FileRepositoryFactory::class                                      => FileRepositoryFactory::class,
    ],
];
