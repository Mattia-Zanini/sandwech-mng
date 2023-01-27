var $ = jQuery;

$(window).on('load', function () {
    // fields dell'editor
    var editor = new $.fn.dataTable.Editor({
        ajax: "../EditorPHP/controllers/product.php",
        table: "#table",
        fields: [{
            label: "Name:",
            name: "name"
        },
        {
            label: "Price:",
            name: "price"
        },
        {
            label: "Description:",
            name: "description"
        },
        {
            label: "Quantity:",
            name: "quantity"
        },
        {
            label: "Nutritional value:",
            name: "nutritional_value"
        },
        {
            label: "Active:",
            name: "active"
        }
        ]
    });

    // fields della tabella
    var table = $('#table').DataTable({
        lengthChange: false,
        ajax: "../EditorPHP/controllers/product.php",
        columns: [{
            data: "name"
        },
        {
            data: "price"
        },
        {
            data: "description"
        },
        {
            data: "quantity"
        }
        ],
        select: true,
    });

    // Display the buttons
    new $.fn.dataTable.Buttons(table, [
        { extend: "create", editor: editor },
        { extend: "edit", editor: editor },
        {
            extend: "selectedSingle",
            text: "Stacce",
            action: function ( e, dt, node, config ) {

                alert( 'Disattivato' );
                // Immediately add `250` to the value of the salary and submit
                editor
                    .edit( table.row( { selected: true } ).index(), false )
                    .set( 'active',0)
                    .submit();
            }
        },
        {
            extend: "selectedSingle",
            text: "Ristabilisci",
            action: function ( e, dt, node, config ) {

                alert( 'riattivato' );
                // Immediately add `250` to the value of the salary and submit
                editor
                    .edit( table.row( { selected: true } ).index(), false )
                    .set( 'active',1)
                    .submit();
            }
        }
        //{ extend: "remove", editor: editor }
    ]);

    table.buttons().container()
        .appendTo($('.col-md-6:eq(0)', table.table().container()));
});