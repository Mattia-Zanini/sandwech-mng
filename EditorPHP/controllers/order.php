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

// product table
Editor::inst($db, 'order', 'ID')
	->fields(
        Field::inst('order.id', 'id'),
		Field::inst('user.name','username'),
        Field::inst( 'user.surname' ,'surname' ),
		Field::inst('order.created','created'),
		Field::inst('pickup.name','pickupname'),
		Field::inst('break.time','time'),
		Field::inst('status.description', 'description'),
        Field::inst('class.year', 'year'),
        Field::inst('class.section', 'section'),
		Field::inst('order.pickup', 'pickup')
		
	)
	->leftJoin( 'user', 'user.id', '=', 'order.user' )
    ->leftJoin( 'break', 'break.id', '=', 'order.break' )
    ->leftJoin( 'pickup', 'pickup.id', '=', 'order.pickup' )
    ->leftJoin( 'status', 'status.id', '=', 'order.status' )
    ->leftJoin( 'user_class', 'user_class.user', '=', 'user.id' )
    ->leftJoin( 'class', 'user_class.class', '=', 'class.id' )
    ->process($_POST)
	->json();

	 /* vecchi fields utili per i vari controlli
    Field::inst('objects.first_name')
			->validator(
				Validate::notEmpty(
					ValidateOptions::inst()
						->message('A first name is required')
				)
			),
		Field::inst('objects.last_name')
			->validator(
				Validate::notEmpty(
					ValidateOptions::inst()
						->message('A last name is required')
				)
			),
		Field::inst('objects.position'),
		Field::inst('objects.email')
			->validator(
				Validate::email(
					ValidateOptions::inst()
						->message('Please enter an e-mail address')
				)
			),
		Field::inst('objects.office'),
		Field::inst('objects.extn'),
		Field::inst('objects.age')
			->validator(Validate::numeric())
			->setFormatter(Format::ifEmpty(null)),
		Field::inst('objects.salary')
			->validator(Validate::numeric())
			->setFormatter(Format::ifEmpty(null)),
		Field::inst('objects.start_date')
			->validator(Validate::dateFormat('Y-m-d'))
			->getFormatter(Format::dateSqlToFormat('Y-m-d'))
			->setFormatter(Format::dateFormatToSql('Y-m-d')) */