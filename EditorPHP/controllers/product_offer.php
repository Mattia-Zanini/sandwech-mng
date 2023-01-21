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
Editor::inst($db, 'product_offer', 'product', 'offer')
	->fields(
		Field::inst('product.name', 'product_name'),
		Field::inst('offer.description', 'offer_description'),
	)
	->leftjoin('product', 'product.id', '=', 'product_offer.product')
	->leftjoin('offer', 'offer.id', '=', 'product_offer.offer')
	->debug(true)
	->process($_POST)
	->json();