wp.customize.controlConstructor['o2-range-slider'] = wp.customize.Control.extend({

	ready: function() {
		'use strict';

		var control = this,
			size = 'px',
			slider = control.container.find( '.o2-range-slider-range' ),
			output = control.container.find( '.o2-range-slider-value' ),
			maxattr = control.params.max,
			minattr = control.params.min,
			currentSize = control.setting._value.replace(/[0-9]/g, ''),
			buttonset = '';

		if ( control.params.percent !== false ) {
			buttonset = control.container.find( '.o2-range-slider-buttonset'  );
		}

		if ( control.params.percent !== false ) {
			if ( currentSize === 'px' ) {
				buttonset[0].checked = true;
				slider[0].attributes.min.value = minattr;
				slider[0].attributes.max.value = maxattr;
			} else if ( currentSize === '%' ) {
				buttonset[1].checked = true;
				slider[0].attributes.min.value = 0;
				slider[0].attributes.max.value = 100;
			}
		}

		slider[0].oninput = function() {
			if ( control.params.percent !== false ) {
				buttonset.forEach( function( button ) {
					if ( button.checked ) {
						size = button.value;
					}
				});
			}
			output[0].value = this.value;
			control.setting.set( this.value + size );
		};

		output[0].oninput = function() {
			if ( control.params.percent !== false ) {
				buttonset.forEach( function( button ) {
					if ( button.checked ) {
						size = button.value;
					}
				});
			}
			slider[0].value = this.value;
			control.setting.set( this.value + size );
		};

		if ( control.params.percent !== false ) {
			buttonset = Array.from( buttonset);
		}

		if ( control.params.percent !== false ) {
			buttonset.forEach( function( button ) {
				button.onclick = function() {
					if ( button.value === 'px' ) {
						slider[0].attributes.min.value = minattr;
						slider[0].attributes.max.value = maxattr;
					} else if ( button.value === '%' ) {
						slider[0].attributes.min.value = 0;
						slider[0].attributes.max.value = 100;
					}
					control.setting.set( parseInt( control.setting._value ) + button.value );
				};
			});
		}

		if ( control.params.default !== false ) {
			var reset = control.container.find( '.o2-range-reset-slider' );
			var defaultSize = control.params.default.replace(/[0-9]/g, '');

			reset[0].onclick = function() {
				control.setting.set( control.params.default );
				slider[0].value = parseInt( control.params.default );
				output[0].value = parseInt( control.params.default );
				if ( control.params.percent !== false ) {
					if ( defaultSize === 'px' ) {
						buttonset[0].click();
					} else if ( defaultSize === '%' ) {
						buttonset[1].click();
					}
				}
			};
		} else {
			if ( control.params.percent !== false ) {
				buttonset[0].checked = true;
			}
		}
	}

});