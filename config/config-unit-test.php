<?php

// Environment specific configurations
return [
    // static parameters
    'db.local.path' => 'db/f3sqlite.db',

    // class dependencies
    \F3\Repo\Database::class => DI\create(\F3\Repo\SQLiteDatabase::class)
];

?>