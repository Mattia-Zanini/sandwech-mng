var $ = jQuery;

$(window).on('load', function () {
    // fields dell'editor
    var editor = new $.fn.dataTable.Editor({
        ajax: "../EditorPHP/controllers/pickup_break.php",
        table: "#table",
        fields: [{
            label: "Name:",
            name: "name"
        },
        {
            label: "Time:",
            name: "time"
        }
        ]
    });

    // fields della tabella
    var table = $('#table').DataTable({
        lengthChange: false,
        ajax: "../EditorPHP/controllers/pickup_break.php",
        columns: [
            {
                data: "name"
            },
            {
                data: "time"
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