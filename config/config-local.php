<?php

// Environment specific configurations
return [
    // static parameters
    'db.host' => 'localhost',
    'db.name' => 'f3',
    'db.user' => 'f3dev',
    'db.password' => 'f3dev',
    'db.charset' => 'utf8',

    // class dependencies
    \F3\Repo\Database::class => DI\create(\F3\Repo\MySqlDatabase::class)
        ->constructor(DI\get('db.host'), 
                      DI\get('db.name'), 
                      DI\get('db.user'), 
                      DI\get('db.password'), 
                      DI\get('db.charset')
    )
];

?>