var $ = jQuery;

$(window).on('load', function () {
    // fields dell'editor
    var editor = new $.fn.dataTable.Editor({
        ajax: "../EditorPHP/controllers/user.php",
        table: "#table",
        fields: [{
            label: "Name:",
            name: "name"
        },
        {
            label: "Surname:",
            name: "surname"
        },
        {
            label: "Email:",
            name: "email"
        },
        {
            label: "Active:",
            name: "active"
        },
        {
            label: "Year:",
            name: "year"
        },
        {
            label: "Section:",
            name: "section"
        }
        ]
    });

    // fields della tabella
    var table = $('#table').DataTable({
        lengthChange: false,
        ajax: "../EditorPHP/controllers/user.php",
        columns: [{
            data: "name"
        },
        {
            data: "surname"
        },
        {
            data: "email"
        },
        {
            data: "active"
        },
        {
            data: "year"
        },
        {
            data: "section"
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