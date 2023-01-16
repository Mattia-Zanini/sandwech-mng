<?php

include("../lib/DataTables.php");

/*use
	DataTables\Editor,
	DataTables\Editor\Field,
	DataTables\Editor\Format,
	DataTables\Editor\Mjoin,
	DataTables\Editor\Options,
	DataTables\Editor\Upload,
	DataTables\Editor\Validate,
	DataTables\Editor\ValidateOptions;
use DeliciousBrains\WPMDB\Container\Dotenv\Validator;
use Illuminate\Support\Str;*/

// Alias Editor classes so they are easy to use
use
DataTables\Editor,
DataTables\Editor\Field,
DataTables\Editor\Format,
DataTables\Editor\Mjoin,
DataTables\Editor\Options,
DataTables\Editor\Upload,
DataTables\Editor\Validate,
DataTables\Editor\ValidateOptions;

// user table
/*Editor::inst($db, 'nutritional_value', 'id')
	->fields(
		Field::inst('kcal'),
		Field::inst('fats')
	)
	->debug(true)
	->process($_POST)
	->json();
*/

Editor::inst( $db, 'nutritional_value' )
    ->field(
        Field::inst( 'nutritional_value.kcal' ),
        Field::inst( 'nutritional_value.fats' ),
        Field::inst( 'product.name' )
    )
    ->leftJoin( 'product', 'product.nutritional_value', '=', 'nutritional_value.id' )
	->debug(true)
    ->process($_POST)
    ->json();