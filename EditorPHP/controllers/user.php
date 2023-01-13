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
Editor::inst($db, 'user', 'ID')
	->fields(
		Field::inst('name'),
		Field::inst('surname'),
		Field::inst('email'),
		Field::inst('password'),
		Field::inst('active')
	)
	->debug(true)
	->process($_POST)
	->json();