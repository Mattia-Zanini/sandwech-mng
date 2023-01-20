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
Editor::inst($db, 'cart', 'user', 'product')
	->fields(
		Field::inst('user.name', 'user_name'),
		Field::inst('user.surname', 'user_surname'),
		Field::inst('product.name', 'product_name'),
		Field::inst('cart.quantity', 'quantity'),
	)
	->leftjoin('user', 'user.id', '=', 'cart.user')
	->leftjoin('product', 'product.id', '=', 'cart.product')
	->debug(true)
	->process($_POST)
	->json();