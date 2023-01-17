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
/*Editor::inst($db, 'user', 'ID')
	->fields(
		Field::inst('name'),
		Field::inst('surname'),
		Field::inst('email'),
		Field::inst('password'),
		Field::inst('active')
	)
	->debug(true)
	->process($_POST)
	->json();*/

	Editor::inst( $db, 'user' )
    ->field(
        Field::inst( 'user.name' ,'name'),
        Field::inst( 'user.surname' ,'surname' ),
		Field::inst( 'user.email'  ,'email'),
		Field::inst( 'user.active'  ,'active'),
        Field::inst( 'class.year' ,'year' ),
		Field::inst( 'class.section' ,'section' )
    )
    ->leftJoin( 'user_class as uc', 'uc.user', '=', 'user.id' )
    ->leftJoin( 'class', 'class.id', '=', 'uc.class' )
    ->process($_POST)
    ->json();