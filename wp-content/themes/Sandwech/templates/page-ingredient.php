<?php get_header(); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-2">
            <?php require("code_table_list.php"); ?>
        </div>
        <div class="col-10">
            <div class="row">
                <h1 class="title text-center" id="title_table">
                    <?php echo get_the_title() ?>
                </h1>
            </div>
            <div class="row">
                <div <?php echo TABLE_SIZE_CLASS; ?>>
                    <table id="table" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Descrizione</th>
                                <th>Prezzo</th>
                                <th>Extra</th>
                                <th>Quantità</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo get_template_directory_uri() ?>/js/ingredient.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri() ?>/js/setupBtn.js"></script>

<?php get_footer(); ?>