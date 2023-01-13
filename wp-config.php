<?php

/**
 * Il file base di configurazione di WordPress.
 *
 * Questo file viene utilizzato, durante l’installazione, dallo script
 * di creazione di wp-config.php. Non è necessario utilizzarlo solo via web
 * puoi copiare questo file in «wp-config.php» e riempire i valori corretti.
 *
 * Questo file definisce le seguenti configurazioni:
 *
 * * Impostazioni del database
 * * Chiavi segrete
 * * Prefisso della tabella
 * * ABSPATH
 *
 * * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Impostazioni database - È possibile ottenere queste informazioni dal proprio fornitore di hosting ** //
/** Il nome del database di WordPress */
define('DB_NAME', 'wordpress');

/** Nome utente del database */
define('DB_USER', 'root');

/** Password del database */
define('DB_PASSWORD', '');

/** Hostname del database */
define('DB_HOST', 'localhost');

/** Charset del Database da utilizzare nella creazione delle tabelle. */
define('DB_CHARSET', 'utf8mb4');

/** Il tipo di collazione del database. Da non modificare se non si ha idea di cosa sia. */
define('DB_COLLATE', '');

/**#@+
 * Chiavi univoche di autenticazione e di sicurezza.
 *
 * Modificarle con frasi univoche differenti!
 * È possibile generare tali chiavi utilizzando {@link https://api.wordpress.org/secret-key/1.1/salt/ servizio di chiavi-segrete di WordPress.org}
 *
 * È possibile cambiare queste chiavi in qualsiasi momento, per invalidare tutti i cookie esistenti.
 * Ciò forzerà tutti gli utenti a effettuare nuovamente l'accesso.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'ad/7o/9Fz9a1v9wtC*#tw9|$k~Y>FHT1o$&HY<jF|2/XAiPmoT^SVedU9ZIjW|is');
define('SECURE_AUTH_KEY',  'jBn,GI>wp}rfkHAA]C*H0Xb.Wv%;L}lBi<T o JgEEe!!fN~ N(9shBeT,Mrtn#8');
define('LOGGED_IN_KEY',    'C2`eVjK,%c`eUF-hnSu%PHtL(q *QKneiw~d{U85c>8Y_C!_W%S4OmK%u~deNXCR');
define('NONCE_KEY',        'xnr>pJt^t|@G`Hng+#Nk7@&,x7a+:/xSdVn7E4%gtek7l3i5|&o i!u..)/|?QgY');
define('AUTH_SALT',        'j`:.Jp&XGxCb?_!`U` vw0K`Qd}x|W[Z{SNut<RwC;uifBPifpB_vRH^gaic=sL4');
define('SECURE_AUTH_SALT', '@a{4ot:KU!7s!56*|:5EyqxAzrtWVaQaWx5}5*j)u+eis+f,K]lzHfQs0JhKz~C{');
define('LOGGED_IN_SALT',   '[6JD{,G^DZ=$@K2hLkXr{@JOJOh11pVr(yhKpe4*t9bQBNo0-Q~Ue:/rAf6]yV_G');
define('NONCE_SALT',       'Wc`2.]#r x9AyQ6-b%gpO7G oIq],H1exyJtEM*6V)h(w~<RJ+XHj!8^^JQ[+@Kf');

/**#@-*/

/**
 * Prefisso tabella del database WordPress.
 *
 * È possibile avere installazioni multiple su di un unico database
 * fornendo a ciascuna installazione un prefisso univoco. Solo numeri, lettere e trattini bassi!
 */
$table_prefix = 'wp_';

/**
 * Per gli sviluppatori: modalità di debug di WordPress.
 *
 * Modificare questa voce a TRUE per abilitare la visualizzazione degli avvisi durante lo sviluppo
 * È fortemente raccomandato agli svilupaptori di temi e plugin di utilizare
 * WP_DEBUG all’interno dei loro ambienti di sviluppo.
 *
 * Per informazioni sulle altre costanti che possono essere utilizzate per il debug,
 * leggi la documentazione
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define('WP_DEBUG', false);

/* Aggiungere qualsiasi valore personalizzato tra questa riga e la riga "Finito, interrompere le modifiche". */



/* Finito, interrompere le modifiche! Buona pubblicazione. */

/** Path assoluto alla directory di WordPress. */
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

/** Imposta le variabili di WordPress ed include i file. */
require_once ABSPATH . 'wp-settings.php';
