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
Editor::inst($db, 'product_order', 'product', 'order')
	->fields(
		Field::inst('product.name', 'product_name'),
		Field::inst('order.id', 'order_id'),
       // Field::inst('product_order.quantity', 'quantity')
	)
	->leftjoin('product', 'product.id', '=', 'product_order.product')
	->leftjoin('order', 'order.id', '=', 'product_order.order')
	->debug(true)
	->process($_POST)
	->json();