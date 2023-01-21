var $ = jQuery;

$(window).on('load', function () {
    // fields dell'editor
    var editor = new $.fn.dataTable.Editor({
        ajax: "../EditorPHP/controllers/reset.php",
        table: "#table",
        fields: [{
            label: "User:",
            name: "user"
        },
        {
            label: "Password:",
            name: "password"
        },
        {
            label: "Requested:",
            name: "requested"
        },
        {
            label: "Expires:",
            name: "expires"
        },
        {
            label: "Completed:",
            name: "completed"
        }
        ]
    });

    // fields della tabella
    var table = $('#table').DataTable({
        lengthChange: false,
        ajax: "../EditorPHP/controllers/reset.php",
        columns: [
            {
                data: "user"
            },
            {
                data: "password"
            },
            {
                data: "requested"
            },
            {
                data: "expires"
            },
            {
                data: "completed"
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