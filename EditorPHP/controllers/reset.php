<?php

include("../lib/DataTables.php");

use
DataTables\Editor,
DataTables\Editor\Field,
DataTables\Editor\Format,
DataTables\Editor\Mjoin,
DataTables\Editor\Options,
DataTables\Editor\Upload,
DataTables\Editor\Validate,
DataTables\Editor\ValidateOptions;
use DeliciousBrains\WPMDB\Container\Dotenv\Validator;
use Illuminate\Support\Str;

// user table
Editor::inst($db, 'reset')
	->fields(
		//Field::inst('user.name','name'),
        //Field::inst('user.surname','surname'),
        Field::inst('reset.id','user'),
		Field::inst('reset.password', 'password'),
        Field::inst('reset.requested','requested'),
		Field::inst('reset.expires', 'expires'),
        Field::inst('reset.completed','completed'),
		
	)
    ->leftJoin( 'user', 'user.id', '=', 'reset.user' )
	->debug(true)
	->process($_POST)
	->json();