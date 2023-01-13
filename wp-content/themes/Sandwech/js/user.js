var $ = jQuery;

$(window).on('load', function () {
    // fields dell'editor
    var editor = new $.fn.dataTable.Editor({
        ajax: "../EditorPHP/controllers/user.php",
        table: "#user",
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
    var table = $('#user').DataTable({
        dom: "Bfrtip",
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
            data: "password"
        }
        ],
        select: true,
        buttons: [{
            extend: "create",
            editor: editor
        },
        {
            extend: "edit",
            editor: editor
        },
        {
            extend: "remove",
            editor: editor
        }
        ]
    });
});
