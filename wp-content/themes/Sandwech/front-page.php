<?php get_header(); ?>

<div class="container-fluid front-page-content">
    <div class="row">
        <div class="col-12">
            <h1 class="title text-center"><?php echo get_the_title() ?></h1>
            <hr />
        </div>
    </div>

    <div class="row">
        <div class="col-2"></div>
        <div class="col-8 text-center">
            <?php echo the_content() ?>
        </div>
        <div class="col-2"></div>
    </div>
</div>

<?php get_footer(); ?>