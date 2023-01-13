<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sandwech</title>
    <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/default/jquery-3.6.0.min.js">
    </script>
    <link href="<?php echo get_template_directory_uri(); ?>/style.css" rel="stylesheet" type="text/css">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <header class="sticky-top">
        <nav class="navbar navbar-expand-lg navbar-color">
            <div class="container-fluid">
                <a class="navbar-brand mb-0 h1" href="http://localhost/sandwech-mng">Sandwech Management</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!--
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" href="http://localhost/sandwech-mng/user">User</a>
                        </li>
                    </ul>
                </div>
                -->
            </div>
        </nav>
    </header>
    <h1 hidden id="table_name">product</h1> <!-- tabella "product" come predefinita -->