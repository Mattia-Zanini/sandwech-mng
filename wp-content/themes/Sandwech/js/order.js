var $ = jQuery;

$(window).on('load', function () {
    // fields dell'editor
    var editor = new $.fn.dataTable.Editor({
        ajax: "../EditorPHP/controllers/order.php",
        table: "#table",
        fields: [
        {
            label: "Created:",
            name: "created"
        },
        {
            label: "Pickup:",
            name: "pickup"
        },
        {
            label: "Time:",
            name: "time"
        },
        {
            label: "Description:",
            name: "description"
        },
        {
            label: "Name:",
            name: "username"
        },
        {
            label: "Surname:",
            name: "surname"
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
        ajax: "../EditorPHP/controllers/order.php",
        columns: [ {
            data: "id"
        },
        {
            data: "created"
        },
        {
            data: "pickupname"
        },
        {
            data: "time"
        },
        {
            data: "description"
        },
        {
            data: "username"
        },
        {
            data: "surname"
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