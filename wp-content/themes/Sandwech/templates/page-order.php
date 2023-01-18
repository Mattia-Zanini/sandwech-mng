<?php get_header(); ?>

<div class="container-fluid">
    <div class="row">
        <?php
        $current_user_role = wp_get_current_user()->roles[0];
        if ($current_user_role != "administrator")
            require("code_table_list.php");
        else
            require("code_table_list_full.php");
        ?>
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
                                <th>id</th>
                                <th>created</th>
                                <th>pickup</th>
                                <th>break</th>
                                <th>status</th>
                                <th>Name</th>
                                <th>Surname</th>
                                <th>class</th>
                                <th>section</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo get_template_directory_uri() ?>/js/order.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri() ?>/js/setupBtn.js"></script>

<?php get_footer(); ?>