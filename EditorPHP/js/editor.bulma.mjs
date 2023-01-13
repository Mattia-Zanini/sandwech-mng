
/*! Bulma integration for DataTables' Editor
 * © SpryMedia Ltd - datatables.net/license
 */

import $ from 'jquery';
import DataTable from 'datatables.net-bm';
import DataTable from 'datatables.net-editor';


var Editor = DataTable.Editor;

/*
 * Set the default display controller to be our bulma control 
 */
DataTable.Editor.defaults.display = "bulma";


/*
 * Alter the buttons that Editor adds to Buttons so they are suitable for bulma
 */
var i18nDefaults = DataTable.Editor.defaults.i18n;
i18nDefaults.create.title = '<h5 class="modal-title">'+i18nDefaults.create.title+'</h5>';
i18nDefaults.edit.title = '<h5 class="modal-title">'+i18nDefaults.edit.title+'</h5>';
i18nDefaults.remove.title = '<h5 class="modal-title">'+i18nDefaults.remove.title+'</h5>';


/*
 * Change the default classes from Editor to be classes for Bulma
 */
$.extend( true, $.fn.dataTable.Editor.classes, {
	"header": {
		"wrapper": "DTE_Header modal-header"
	},
	"body": {
		"wrapper": "DTE_Body modal-body"
	},
	"footer": {
		"wrapper": "DTE_Footer modal-footer"
	},
	"form": {
		"tag": "form-horizontal",
		"button": "button",
		"buttonInternal": "button",
		"error": "DTE_Form_Error help is-danger"
	},
	"field": {
		"wrapper": "DTE_Field field",
		"label":   "label",
		"error":   "DTE_Field_Error help is-danger",
		"multiValue":    "card multi-value",
		"multiInfo":     "small",
		"multiRestore":  "card multi-restore"
	},
} );

$.extend( true, DataTable.ext.buttons, {
	create: {
		formButtons: {
			className: 'button is-primary'
		}
	},
	edit: {
		formButtons: {
			className: 'button is-primary'
		}
	},
	remove: {
		formButtons: {
			className: 'button is-danger'
		}
	}
} );

DataTable.Editor.fieldTypes.datatable.tableClass = 'table';

/*
 * Bulma display controller - this is effectively a proxy to the Bulma
 * modal control.
 */
let shown = false;
let fullyShown = false;

const dom = {
	content: $(
		'<div class="modal DTED">'+
			'<div class="modal-background"></div>'+
			'<div class="modal-content"></div>'+
			'<button class="modal-close is-large" aria-label="close"></button>'+
		'</div>'
	)
};

DataTable.Editor.display.bulma = $.extend( true, {}, DataTable.Editor.models.displayController, {
	/*
	 * API methods
	 */
	init: function ( dte ) {
		// Add `form-control` to required elements
		dte.on( 'displayOrder.dtebm open.dtebm', function ( e, display, action, form ) {
			$.each( dte.s.fields, function ( key, field ) {
				$('input:not([type=checkbox]):not([type=radio]), select, textarea', field.node() )
					.addClass( 'input' );

				$('input[type=checkbox], input[type=radio]', field.node() )
					.removeClass('input');

				$('select', field.node() )
					.addClass( 'select' )
					.parent().addClass('select');

				$('select[multiple]', field.node() )
					.parent().addClass('is-multiple');
			} );
		} );

		return DataTable.Editor.display.bulma;
	},

	open: function ( dte, append, callback ) {
		$(append).removeClass('is-hidden').addClass('is-active');
		$(append).find('.modal-title').addClass('title');
		dom.content.find('.modal-content').append(append);
		dom.content.addClass('is-active is-clipped');

		dom.content.appendTo("body");
		// Setup events on each show
		$('.modal-close')
			.attr('title', dte.i18n.close)
			.one('click', function () {
				dte.close('icon');
			})
			.appendTo($('div.modal-header', append));

		// This is a bit horrible, but if you mousedown and then drag out of the modal container, we don't
		// want to trigger a background action.
		let allowBackgroundClick = false;
		$(document)
			.off('mousedown.dte-bs5')
			.on('mousedown.dte-bs5', 'div.modal-background', function (e) {
				allowBackgroundClick = $(e.target).hasClass('modal-background');
			} );

		$(document)
			.off('click.dte-bs5')
			.on('click.dte-bs5', 'div.modal-background', function (e) {
				if ( $(e.target).hasClass('modal-background') && allowBackgroundClick ) {
					dte.background();
				}
			} );

		if ( callback ) {
			callback();
		}
		return;
	},

	close: function ( dte, callback ) {
		dom.content
			.find('.is-active')
			.removeClass('is-active')
			.addClass('is-hidden');

		dom.content.removeClass('is-active is-clipped');
		if ( callback ) {
			callback();
		}
	},

	node: function ( dte ) {
		return dom.content[0];
	}
} );


export default Editor;
