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
Editor::inst($db, 'product_tag', 'product', 'tag')
	->fields(
		Field::inst('product.name', 'product_name'),
		Field::inst('tag.name', 'tag_name'),
	)
	->leftjoin('product', 'product.id', '=', 'product_tag.product')
	->leftjoin('tag', 'tag.id', '=', 'product_tag.tag')
	->debug(true)
	->process($_POST)
	->json();