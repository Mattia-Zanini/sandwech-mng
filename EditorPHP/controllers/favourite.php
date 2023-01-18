<?php

include("../lib/DataTables.php");

use
    DataTables\Editor,
    DataTables\Editor\Field;
// 	DataTables\Editor\Format,
// 	DataTables\Editor\Mjoin,
// 	DataTables\Editor\Options,
// 	DataTables\Editor\Upload,
// 	DataTables\Editor\Validate,
// 	DataTables\Editor\ValidateOptions;
// 	use DeliciousBrains\WPMDB\Container\Dotenv\Validator;
// 	use Illuminate\Support\Str;

// ingredient table
Editor::inst($db, 'favourite','user','product')
    ->fields(
        Field::inst('product.name', 'productname'),
        Field::inst('user.name','username'),
        Field::inst('favourite.created','created')
    )
    ->leftjoin('product','product.id','=','favourite.product')
    ->leftjoin('user','user.id','=','favourite.user')
    ->debug(true)
    ->process($_POST)
    ->json();
