wp.customize.controlConstructor['logincust-padding'] = wp.customize.Control.extend({

	ready: function() {
		'use strict';

		var control = this,
			value = control.setting._value,
			fields = control.container.find( 'input[type=number]' );
		var values = value.split( ' ' );
		fields = Array.from( fields );

		if ( values.length === 1 ) {
			fields.forEach( function( field ) {
				field.value = parseInt( values[0] );
			});
		} else if ( values.length === 2 ) {
			fields[0].value = parseInt( values[0] );
			fields[1].value = parseInt( values[1] );
			fields[2].value = parseInt( values[0] );
			fields[3].value = parseInt( values[1] );
		} else if ( values.length === 3 ) {
			fields[0].value = parseInt( values[0] );
			fields[1].value = parseInt( values[1] );
			fields[2].value = parseInt( values[2] );
			fields[3].value = parseInt( values[1] );
		} else if ( values.length === 4 ) {
			fields[0].value = parseInt( values[0] );
			fields[1].value = parseInt( values[1] );
			fields[2].value = parseInt( values[2] );
			fields[3].value = parseInt( values[3] );
		}

		fields.forEach( function( field ) {
			field.oninput = function() {
				control.setting.set( fields[0].value + 'px ' + fields[1].value + 'px ' + fields[2].value + 'px ' + fields[3].value + 'px' );
			};
		});

	}

});