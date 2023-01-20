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

// ingredient table
Editor::inst($db, 'offer', 'ID')
	->fields(
		Field::inst('offer.ID', 'id'),
		Field::inst('product.name', 'name'),
		Field::inst('offer.price', 'price'),
		Field::inst('offer.start', 'start'),
		Field::inst('offer.expiry', 'expiry'),
		Field::inst('offer.description', 'description'),
	)
	->leftjoin('product_offer po', 'offer.id', '=', 'po.offer')
	->leftjoin('product', 'product.id', '=', 'po.product')
	->debug(true)
	->process($_POST)
	->json();