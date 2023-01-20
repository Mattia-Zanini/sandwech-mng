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
Editor::inst($db, 'product_allergen', 'product', 'allergen')
	->fields(
		Field::inst('product.name', 'product_name'),
		Field::inst('allergen.name', 'allergen_name'),
	)
	->leftjoin('product', 'product.id', '=', 'product_allergen.product')
	->leftjoin('allergen', 'allergen.id', '=', 'product_allergen.allergen')
	->debug(true)
	->process($_POST)
	->json();