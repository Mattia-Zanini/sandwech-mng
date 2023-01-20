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
Editor::inst($db, 'user_class','user')
	->fields(
        Field::inst('user.id','id'),
		Field::inst('user_class.class','class'),
		Field::inst('user_class.user','user'),
        Field::inst('user.name','name'),
        Field::inst('user.surname','surname'),
        Field::inst('class.section','section'),
        Field::inst('class.year','year')
	)
    ->leftjoin('class','class.id','=','user_class.class')
    ->leftJoin('user','user.id','=','user_class.user')
	->debug(true)
	->process($_POST)
	->json();