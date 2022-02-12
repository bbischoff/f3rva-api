<?php

// Environment specific configurations
return [
    // static parameters
    'db.host' => '@@DB_HOST@@',
    'db.name' => '@@DB_NAME@@',
    'db.user' => '@@DB_USER@@',
    'db.password' => '@@DB_PASS@@',
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