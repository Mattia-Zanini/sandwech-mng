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
Editor::inst($db, 'product_ingredient', 'product', 'ingredient')
	->fields(
		Field::inst('product.name', 'product_name'),
		Field::inst('ingredient.name', 'ingredient_name'),
	)
	->leftjoin('product', 'product.id', '=', 'product_ingredient.product')
	->leftjoin('ingredient', 'ingredient.id', '=', 'product_ingredient.ingredient')
	->debug(true)
	->process($_POST)
	->json();