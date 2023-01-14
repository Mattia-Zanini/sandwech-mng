/*
Questo file javascript utilizza la libreria jQuery e la libreria DataTables per creare una tabella dinamica e interattiva 
per la visualizzazione e la modifica dei dati.

La prima riga del file utilizza la notazione "$" per accedere a jQuery, che è una libreria javascript 
che semplifica la scrittura di codice javascript per la manipolazione del DOM e la gestione degli eventi.

La funzione on('load') di jQuery viene utilizzata per eseguire il codice all'interno della funzione 
quando la pagina è completamente caricata. Dentro questa funzione vengono definite varie proprietà per l'editor e la tabella.

L'editor viene creato utilizzando la funzione Editor() della libreria DataTables, con proprietà come l'URL dell'API, 
l'identificatore della tabella e i campi da visualizzare e modificare. La tabella viene creata utilizzando la 
funzione DataTable() della libreria DataTables, con proprietà come l'URL dell'API, le colonne da visualizzare, 
la possibilità di selezionare righe e l'utilizzo di bottoni per la creazione, modifica e rimozione di dati.

In fine, vengono creati i bottoni per la creazione, modifica e rimozione di dati tramite la funzione Buttons() 
della libreria DataTables e vengono appesi alla tabella, specificando l'editor come parametro.
*/


// Questo codice utilizza la libreria jQuery per creare una tabella dinamica con funzionalità di modifica e cancellazione.
// Assegnamo l'oggetto jQuery alla variabile $
var $ = jQuery;

// Aspettiamo il caricamento completo della pagina prima di eseguire il codice seguente
$(window).on('load', function () {
    // Creiamo un oggetto Editor per la tabella, specificando l'URL del file PHP che gestirà le richieste AJAX
    // fields dell'editor
    var editor = new $.fn.dataTable.Editor({
        ajax: "../EditorPHP/controllers/product.php",
        table: "#table",
        // Definiamo i campi dell'editor e i loro nomi
        fields: [{
            label: "Name:",
            name: "name"
        },
        {
            label: "Price:",
            name: "price"
        },
        {
            label: "Description:",
            name: "description"
        },
        {
            label: "Quantity:",
            name: "quantity"
        },
        {
            label: "Nutritional value:",
            name: "nutritional_value"
        },
        {
            label: "Active:",
            name: "active"
        }
        ]
    });

    // Creiamo l'oggetto DataTable per la tabella, specificando l'URL del file PHP che fornirà i dati della tabella
    // fields della tabella
    var table = $('#table').DataTable({
        // Disabilitiamo la possibilità di cambiare la lunghezza della tabella
        lengthChange: false,
        ajax: "../EditorPHP/controllers/product.php",
        // Definiamo le colonne della tabella e i loro nomi
        columns: [{
            data: "name"
        },
        {
            data: "price"
        },
        {
            data: "description"
        },
        {
            data: "quantity"
        }
        ],
        // Abilitiamo la selezione delle righe
        select: true,
    });

    // Aggiungiamo i pulsanti per la creazione, la modifica e la cancellazione delle righe
    new $.fn.dataTable.Buttons(table, [
        { extend: "create", editor: editor },
        { extend: "edit", editor: editor },
        { extend: "remove", editor: editor }
    ]);

    table.buttons().container()
        .appendTo($('.col-md-6:eq(0)', table.table().container()));
});
