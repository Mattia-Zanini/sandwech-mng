<!--
Il file page.php è un file presente nella maggior parte dei temi di WordPress e serve per specificare 
la struttura e il contenuto delle singole pagine del sito.

In WordPress, le pagine sono utilizzate per creare contenuti statici come la pagina "Chi siamo", 
"Contatti" o "Termini e condizioni" del tuo sito web. Il file page.php è utilizzato per creare la 
struttura e il contenuto di queste pagine.

Il file page.php può contenere codice HTML, CSS e PHP per creare la struttura e il contenuto della pagina, 
inclusi loop per mostrare i contenuti personalizzati. Inoltre, può utilizzare le funzionalità e le personalizzazioni 
create nel file functions.php.

Se il tema in uso non contiene il file page.php, WordPress utilizzerà il file index.php per mostrare la pagina. 
In questo caso, per creare una pagina personalizzata è possibile utilizzare il file single.php o creare un template 
personalizzato per la pagina.

In sintesi, il file page.php è un file presente in molti temi di wordpress, che serve per specificare la struttura 
e il contenuto delle singole pagine del sito, può contenere codice HTML, CSS e PHP, e può utilizzare le funzionalità 
e le personalizzazioni create nel file functions.php. Se non presente, wordpress utilizzerà il file index.php o un 
template personalizzato per mostrare la pagina.
-->

<?php
$current_user_role = wp_get_current_user()->roles[0];

if ($current_user_role != "administrator") {
    if (is_page('allergen')) {
        get_template_part('templates/page', 'allergen');
    } elseif (is_page('break')) {
        get_template_part('templates/page', 'break');
    } elseif (is_page('favourite')) {
        get_template_part('templates/page', 'favourite');
    } elseif (is_page('ingredient')) {
        get_template_part('templates/page', 'ingredient');
    } elseif (is_page('nutritional_value')) {
        get_template_part('templates/page', 'nutritional_value');
    } elseif (is_page('offer')) {
        get_template_part('templates/page', 'offer');
    } elseif (is_page('order')) {
        get_template_part('templates/page', 'order');
    } elseif (is_page('pickup')) {
        get_template_part('templates/page', 'pickup');
    } elseif (is_page('product')) {
        get_template_part('templates/page', 'product');
    } elseif (is_page('tag')) {
        get_template_part('templates/page', 'tag');
    } elseif (is_page('user')) {
        get_template_part('templates/page', 'user');
    } elseif (is_page('login')) {
        get_template_part('templates/page', 'login');
    } else {
        echo "error: unknown page type";
    }
} elseif (($current_user_role == "administrator")) {
    if (is_page('allergen')) {
        get_template_part('templates/page', 'allergen');
    } elseif (is_page('break')) {
        get_template_part('templates/page', 'break');
    } elseif (is_page('favourite')) {
        get_template_part('templates/page', 'favourite');
    } elseif (is_page('ingredient')) {
        get_template_part('templates/page', 'ingredient');
    } elseif (is_page('nutritional_value')) {
        get_template_part('templates/page', 'nutritional_value');
    } elseif (is_page('offer')) {
        get_template_part('templates/page', 'offer');
    } elseif (is_page('order')) {
        get_template_part('templates/page', 'order');
    } elseif (is_page('pickup')) {
        get_template_part('templates/page', 'pickup');
    } elseif (is_page('product')) {
        get_template_part('templates/page', 'product');
    } elseif (is_page('tag')) {
        get_template_part('templates/page', 'tag');
    } elseif (is_page('user')) {
        get_template_part('templates/page', 'user');
    } elseif (is_page('cart')) {
        get_template_part('templates/page', 'cart');
    } elseif (is_page('class')) {
        get_template_part('templates/page', 'class');
    } elseif (is_page('pickup_break')) {
        get_template_part('templates/page', 'pickup_break');
    } elseif (is_page('product_allergen')) {
        get_template_part('templates/page', 'product_allergen');
    } elseif (is_page('product_ingredient')) {
        get_template_part('templates/page', 'product_ingredient');
    } elseif (is_page('product_offer')) {
        get_template_part('templates/page', 'product_offer');
    } elseif (is_page('product_order')) {
        get_template_part('templates/page', 'product_order');
    } elseif (is_page('product_tag')) {
        get_template_part('templates/page', 'product_tag');
    } elseif (is_page('reset')) {
        get_template_part('templates/page', 'reset');
    } elseif (is_page('status')) {
        get_template_part('templates/page', 'status');
    } elseif (is_page('user_class')) {
        get_template_part('templates/page', 'user_class');
    } elseif (is_page('login')) {
        get_template_part('templates/page', 'login');
    } else {
        echo "error: unknown page type";
    }
} else {
    echo "error: unknown page type";
}
?>