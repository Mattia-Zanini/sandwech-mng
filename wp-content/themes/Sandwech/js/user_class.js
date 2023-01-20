var $ = jQuery;

$(window).on('load', function () {
    // fields dell'editor
    var editor = new $.fn.dataTable.Editor({
        ajax: "../EditorPHP/controllers/user_class.php",
        table: "#table",
        fields: [
            {
                label: "Classe:",
                name: "class",
                type:  "select"
            },
        ]
    });

    // fields della tabella
    var table = $('#table').DataTable({
        lengthChange: false,
        ajax: "../EditorPHP/controllers/user_class.php",
        columns: [
            {
                data: "id"
            },
            {
                data: "name"
            },
            {
                data: "surname"
            },
            {
                data: "section",
                edietField:"class"
            },
            {
                data: "year"
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