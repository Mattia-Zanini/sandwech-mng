<?php
/*
Il file functions.php è un file presente all'interno di ogni tema di WordPress e serve a specificare le 
funzionalità e le personalizzazioni del tema. Il file functions.php è un file PHP, quindi può contenere 
codice PHP, ma anche codice HTML, CSS e JavaScript.
|
Il file functions.php è un file importante perché permette di aggiungere funzionalità al tuo tema, senza 
modificare il codice del tema stesso. Ciò significa che, se si decide di cambiare tema in futuro, le personalizzazioni 
create nel file functions.php non verranno perse.
|
Il file functions.php può essere utilizzato per:
Aggiungere funzionalità al tuo tema, come ad esempio la creazione di un menu personalizzato, l'aggiunta di 
widget personalizzati o la creazione di shortcode personalizzati.
Personalizzare il comportamento predefinito di WordPress, come ad esempio la modifica della lunghezza 
dell'estratto dei post o la modifica della lunghezza del titolo della pagina.
|
Caricare risorse esterne, come ad esempio i file CSS o JavaScript.
Eseguire qualsiasi altra operazione che si desidera eseguire prima che il tema venga caricato.
In sintesi, il file functions.php è un file presente in ogni tema di wordpress, permette di aggiungere 
funzionalità e personalizzazioni al tema senza modificare il codice del tema stesso, può contenere diversi 
tipi di codice e permette di personalizzare il comportamento predefinito di wordpress.
*/

define("TABLE_SIZE_CLASS", "class=\"col-11 test-table-size\"");

//carica correttamente i file .css
function load_stylesheets()
{
	wp_enqueue_style('bootstrap', get_template_directory_uri() . "/css/default/bootstrap.min.css", array(), '5.2.3', 'all');
	wp_enqueue_style('datatables', get_template_directory_uri() . "/css/default/dataTables.bootstrap5.min.css", array(), '1.13.1', 'all');
	wp_enqueue_style('buttons', get_template_directory_uri() . "/css/default/buttons.bootstrap5.min.css", array(), '2.3.3', 'all');
	wp_enqueue_style('select', get_template_directory_uri() . "/css/default/select.bootstrap5.min.css", array(), '1.5.0', 'all');
	wp_enqueue_style('dateTime', get_template_directory_uri() . "/css/default/dataTables.dateTime.min.css", array(), '1.2.0', 'all');
	wp_enqueue_style('editor_bootstrap', "/EditorPHP/css/editor.bootstrap5.min.css", array(), '2.0.10', 'all');

	//da non aggiungere insieme a editor_bootstrap_css
	//wp_enqueue_style('editor', "/EditorPHP/css/editor.dataTables.min.css", array(), '2.0.10', 'all');
}
add_action('wp_enqueue_scripts', 'load_stylesheets');

//carica correttamente i file .js
function load_js()
{
	//wp_enqueue_script('jQuery', get_template_directory_uri() . "/js/default/jquery-3.6.0.min.js", array(), '3.6.0', true);
	wp_enqueue_script('jQuery_dataTables', get_template_directory_uri() . "/js/default/jquery.dataTables.min.js", array(), '1.13.1', true);
	wp_enqueue_script('bootstrap', get_template_directory_uri() . "/js/default/bootstrap.bundle.min.js", array(), '5.1.3', true);
	wp_enqueue_script('datatables', get_template_directory_uri() . "/js/default/dataTables.bootstrap5.min.js", array(), '1.13.1', true);
	//wp_enqueue_script('buttons', get_template_directory_uri() . "/js/default/buttons.bootstrap5.min.js", array(), '2.3.3', true);
	wp_enqueue_script('buttons_dt', get_template_directory_uri() . "/js/default/dataTables.buttons.min.js", array(), '2.3.3', true);
	wp_enqueue_script('select', get_template_directory_uri() . "/js/default/dataTables.select.min.js", array(), '1.5.0', true);
	wp_enqueue_script('dateTime', get_template_directory_uri() . "/js/default/dataTables.dateTime.min.js", array(), '1.2.0', true);
	wp_enqueue_script('editor_data', "/EditorPHP/js/dataTables.editor.min.js", array(), '2.0.10', true);
	wp_enqueue_script('editor_bootstrap', "/EditorPHP/js/editor.bootstrap5.min.js", array(), '2.0.10', true);
}
add_action('wp_enqueue_scripts', 'load_js');