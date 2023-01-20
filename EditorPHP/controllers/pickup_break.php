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
Editor::inst($db, 'pickup_break', 'pickup', 'break')
	->fields(
		Field::inst('pickup.name', 'name'),
		Field::inst('break.time', 'time'),
	)
	->leftjoin('pickup', 'pickup.id', '=', 'pickup_break.pickup')
	->leftjoin('break', 'break.id', '=', 'pickup_break.break')
	->debug(true)
	->process($_POST)
	->json();