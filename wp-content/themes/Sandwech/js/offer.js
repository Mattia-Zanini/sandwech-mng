var $ = jQuery;

$(window).on('load', function () {
    // fields dell'editor
    var editor = new $.fn.dataTable.Editor({
        ajax: "../EditorPHP/controllers/offer.php",
        table: "#table",
        fields: [
            {
                label: "Name:",
                name: "name"
            },
            {
                label: "Price:",
                name: "price"
            },
            {
                label: "Start:",
                name: "start"
            },
            {
                label: "Expiry:",
                name: "expiry"
            },
            {
                label: "Description:",
                name: "description"
            }
        ]
    });

    // fields della tabella
    var table = $('#table').DataTable({
        lengthChange: false,
        ajax: "../EditorPHP/controllers/offer.php",
        columns: [
            {
                data: "name"
            },
            {
                data: "price"
            },
            {
                data: "start"
            },
            {
                data: "expiry"
            },
            {
                data: "description"
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