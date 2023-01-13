var $ = jQuery;

$(window).on('load', function () {
    // fields dell'editor
    var editor = new $.fn.dataTable.Editor({
        ajax: "EditorPHP/controllers/user.php",
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
            label: "Password:",
            name: "password"
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
        ajax: "EditorPHP/controllers/user.php",
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
            data: "password"
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