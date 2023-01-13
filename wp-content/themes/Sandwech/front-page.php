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
                <li>allergen</li>
                <li>break</li>
                <li>cart</li>
                <li>Milk</li>
                <li>Milk</li>
                <li>Milk</li>
                <li>Milk</li>
                <li>Milk</li>
                <li>Milk</li>
                <li>Milk</li>
                <li>Milk</li>
                <li>Milk</li>
                <li>Milk</li>
                <li>Milk</li>
                <li>Milk</li>
                <li>Milk</li>
                <li>Milk</li>
                <li>Milk</li>
                <li>Milk</li>
                <li>Milk</li>
                <li>Milk</li>
                <li>Milk</li>
            </ul>
        </div>
        <div class="col-10">
            <div class="row">
                <h1 class="title text-center" id="title_table"></h1>
                </h1>
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
    var tableName = $('#table_name').text();
    //console.log(tableName);
    $('#title_table').text(tableName);

    $.getScript("<?php echo get_template_directory_uri() ?>/js/" + tableName + ".js").then(function() {
        SetupButtons();
    });
});
</script>
<!-- <script type="text/javascript" src="<?php //echo get_template_directory_uri() ?>/js/table_Example.js"></script> -->
<script type="text/javascript" src="<?php echo get_template_directory_uri() ?>/js/setupBtn.js"></script>

<?php get_footer(); ?>