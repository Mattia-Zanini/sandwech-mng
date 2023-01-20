<!--
Il file front-page.php è un file presente all'interno di alcuni temi di WordPress e serve per specificare la struttura 
e il contenuto della pagina principale del sito. La pagina principale è quella che viene visualizzata per prima quando si accede al sito.

In alcuni casi, i temi di wordpress utilizzano il file front-page.php per sovrascrivere la pagina principale predefinita 
di wordpress, in modo da creare una pagina personalizzata per la homepage del tuo sito web. In altri casi, 
il file front-page.php è utilizzato per creare una pagina di presentazione, una landing page o una pagina di benvenuto per il tuo sito.

Il file front-page.php può contenere codice HTML, CSS e PHP per creare la struttura e il contenuto della pagina, 
inclusi loop per mostrare i post, le pagine o i contenuti personalizzati. Inoltre, può utilizzare le funzionalità 
e le personalizzazioni create nel file functions.php.

Se il tema in uso non contiene il file front-page.php, WordPress utilizzerà il file index.php per mostrare la pagina principale.
In questo caso, per creare una pagina personalizzata per la homepage del tuo sito web, è possibile utilizzare la funzionalità 
"Pagina iniziale statica" nelle impostazioni di wordpress.

In sintesi, il file front-page.php è un file presente in alcuni temi di wordpress, permette di specificare la struttura e il 
contenuto della pagina principale del sito, può essere utilizzato per sovrascrivere la pagina principale predefinita di wordpress,
può contenere codice HTML, CSS e PHP e può utilizzare le funzionalità e le personalizzazioni create nel file functions.php.
-->


<?php
/*
get_header() è utilizzato per includere il contenuto del file header.php nell'inizio di ogni pagina del tuo sito web. 
In questo modo, è possibile creare un'intestazione coerente per tutte le pagine del tuo sito web senza dover ripetere 
il codice per ogni singola pagina.
*/
get_header();

function MenuBox($text, $tableName)
{
    return
        '<div class="col-2">
        <a class="menu-box-text" href="http://localhost/sandwech-mng/' . $tableName . '">
            <div class="box-nav">
                <h1 class="text-style">' . $text . '</h1>
            </div>
        </a>
</div>';
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <h1 class="title text-center" id="title_table"></h1>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="row d-flex justify-content-around">
                        <?php echo MenuBox("Allergeni", "allergen"); ?>
                        <?php echo MenuBox("Intervalli", "break"); ?>
                        <?php echo MenuBox("Preferiti", "favourite"); ?>
                        <?php echo MenuBox("Ingredienti", "ingredient"); ?>
                        <?php echo MenuBox("Valori Nutrizionali", "nutritional_value"); ?>
                    </div>
                    <div class="row d-flex justify-content-around mt-5">
                        <?php echo MenuBox("Offerte", "offer"); ?>
                        <?php echo MenuBox("Ordini", "order"); ?>
                        <?php echo MenuBox("Punti di Ritiro", "pickup"); ?>
                        <?php echo MenuBox("Prodotti", "product"); ?>
                        <?php echo MenuBox("Categorie", "tag"); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
/*
get_footer() è utilizzato per includere il contenuto del file footer.php alla fine di ogni pagina del tuo sito web. 
In questo modo, è possibile creare un piè di pagina coerente per tutte le pagine del tuo sito web senza dover ripetere 
il codice per ogni singola pagina.
*/
get_footer();
?>