var $ = jQuery;

$(window).on('load', function () {
    // fields dell'editor
    var editor = new $.fn.dataTable.Editor({
        ajax: "../EditorPHP/controllers/user.php",
        table: "#table",
        fields: [{
            label: "Name:",
            name: "user.name"
        },
        {
            label: "Surname:",
            name: "user.surname"
        },
        {
            label: "Email:",
            name: "user.email"
        },
        {
            label: "Active:",
            name: "user.active"
        },
        {
            label: "Year:",
            name: "class.year"
        },
        {
            label: "Section:",
            name: "class.section"
        }
        ]
    });

    // fields della tabella
    var table = $('#table').DataTable({
        lengthChange: false,
        ajax: "../EditorPHP/controllers/user.php",
        columns: [{
            data: "user.name"
        },
        {
            data: "user.surname"
        },
        {
            data: "user.email"
        },
        {
            data: "user.active"
        },
        {
            data: "class.year"
        },
        {
            data: "class.section"
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