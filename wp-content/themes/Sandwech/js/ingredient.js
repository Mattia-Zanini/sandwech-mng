var $ = jQuery;

$(window).on('load', function () {
    // fields dell'editor
    var editor = new $.fn.dataTable.Editor({
        ajax: "../EditorPHP/controllers/ingredient.php",
        table: "#table",
        fields: [{
            label: "Name:",
            name: "name"
        },
        {
            label: "Description:",
            name: "description"
        },
        {
            label: "Price:",
            name: "price"
        },
        {
            label: "Extra:",
            name: "extra"
        },
        {
            label: "Quantity:",
            name: "quantity"
        }
        ]
    });

    // fields della tabella
    var table = $('#table').DataTable({
        lengthChange: false,
        ajax: "../EditorPHP/controllers/ingredient.php",
        columns: [{
            data: "name"
        },
        {
            data: "description"
        },
        {
            data: "price"
        },
        {
            data: "extra"
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
       //{ extend: "remove", editor: editor }
    ]);

    table.buttons().container()
        .appendTo($('.col-md-6:eq(0)', table.table().container()));
});