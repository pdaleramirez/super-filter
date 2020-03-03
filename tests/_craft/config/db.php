<?php

/**
 * Database Configuration
 *
 * All of your system's database configuration settings go in here.
 * You can see a list of the default settings in craft/app/config/defaults/db.php
 */

return [

	// The datzzabase server name or IP address. Usually this is 'localhost' or '127.0.0.1'.
	'server' =>  getenv('DB_SERVER'),

	// The database username to connect with.
    'user' => getenv('DB_USER'),

	// The database password to connect with.
    'password' => getenv('DB_PASSWORD'),

	// The name of the database to select.
    'database' => getenv('DB_DATABASE'),

	// The prefix to use when naming tables. This can be no more than 5 characters.
	'tablePrefix' => getenv('DB_PREFIX'),

];
