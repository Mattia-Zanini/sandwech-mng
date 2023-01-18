var $ = jQuery;

$(window).on('load', function () {
    // fields dell'editor
    var editor = new $.fn.dataTable.Editor({
        ajax: "../EditorPHP/controllers/favourite.php",
        table: "#table",
        fields: [{
            label: "User:",
            name: "username"
        },
        {
            label: "Product:",
            name: "productname"
        },
        {
            label: "Created:",
            name: "created"
        }
        ]
    });

    // fields della tabella
    var table = $('#table').DataTable({
        lengthChange: false,
        ajax: "../EditorPHP/controllers/favourite.php",
        columns: [{
            data: "username"
        },
        {
            data: "productname"
        },
        {
            data: "created"
        }
        ],
        select: true,
    });

    // Display the buttons
    new $.fn.dataTable.Buttons(table, [
        { extend: "create", editor: editor },
        { extend: "edit", editor: editor },
        { extend: "remove", editor: editor }
    ]);

    table.buttons().container()
        .appendTo($('.col-md-6:eq(0)', table.table().container()));
});