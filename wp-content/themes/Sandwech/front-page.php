<?php get_header(); ?>

<div class="container-fluid">
    <!--
    <div class="row">
        <div class="col-12">
            <h1 class="title text-center"><?php //echo get_the_title(); ?></h1>
            <hr />
        </div>
    </div>
    -->

    <div class="row">
        <div class="col-2">
            <!--
            [
                "allergen",
                "break",
                "cart",
                "class",
                "favourite",
                "ingredient",
                "nutritional_value",
                "offer",
                "order",
                "pickup",
                "pickup_break",
                "product",
                "product_allergen",
                "product_ingredient",
                "product_offer",
                "product_order",
                "product_tag",
                "reset",
                "status",
                "tag",
                "user",
                "user_class"
            ]
            -->
            <ul id="tables_list">
                <li><a href="http://localhost/sandwech-mng">Coffee</a></li>
                <li class="list-table">allergen</li>
                <li class="list-table">break</li>
                <li class="list-table">cart</li>
                <li class="list-table">class</li>
                <li class="list-table">favourite</li>
                <li class="list-table">ingredient</li>
                <li class="list-table">nutritional_value</li>
                <li class="list-table">offer</li>
                <li class="list-table">order</li>
                <li class="list-table">pickup</li>
                <li class="list-table">pickup_break</li>
                <li class="list-table">product</li>
                <li class="list-table">product_allergen</li>
                <li class="list-table">product_ingredient</li>
                <li class="list-table">product_offer</li>
                <li class="list-table">product_order</li>
                <li class="list-table">product_tag</li>
                <li class="list-table">reset</li>
                <li class="list-table">status</li>
                <li class="list-table">tag</li>
                <li class="list-table">user</li>
                <li class="list-table">user_class</li>
            </ul>
        </div>
        <div class="col-10">
            <div class="row">
                <h1 class="title text-center" id="title_table"></h1>
            </div>
            <div class="row">
                <div class="col-12">
                    <table id="table" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Description</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('li.list-table').click(function() {
        var newTableText = $(this).text();
        var oldTableText = $('#table_name').text();
        //console.log(newTableText);
        $('#title_table').text(newTableText);
        $('#table_name').text(newTableText);

        $('html').find('script').filter(function() {
            return $(this).attr('src') === "<?php echo get_template_directory_uri() ?>/js/" +
                oldTableText + ".js"
        }).remove();

        //$('#table').dataTable().fnClearTable();

        $('#table').DataTable().destroy();
        $("tbody").remove();

        $.getScript("<?php echo get_template_directory_uri() ?>/js/user.js").then(
            function() {
                SetupButtons();
                console.log("js caricato");

                $('html').find('script').filter(function() {
                    console.log($(this).attr('src') ===
                        "<?php echo get_template_directory_uri() ?>/js/product.js");
                });
            });
    });

    var tableName = $('#table_name').text();
    //console.log(tableName);
    $('#title_table').text(tableName);

    $.getScript("<?php echo get_template_directory_uri() ?>/js/" + tableName + ".js").then(function() {
        SetupButtons();
    });

    /* per togliere uno script
    $('html').find('script').filter(function() {
        return $(this).attr('src') === 'http://firstScript.com'
    }).remove();
    */
});
</script>
<!-- <script type="text/javascript" src="<?php //echo get_template_directory_uri() ?>/js/table_Example.js"></script> -->
<script type="text/javascript" src="<?php echo get_template_directory_uri() ?>/js/setupBtn.js"></script>

<?php get_footer(); ?>