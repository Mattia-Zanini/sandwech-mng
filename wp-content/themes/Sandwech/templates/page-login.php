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
                <hr class="rounded">
            </div>
            <div class="row">
                <div class="col-4"></div>
                <div class="col-8">
                    <div class="row">
                        <div class="col-sm-6 text-black">

                            <div class="px-5 ms-xl-4">
                                <i class="fas fa-crow fa-2x me-3 pt-5 mt-xl-4" style="color: #709085;"></i>
                                <img class="logo-img-login"
                                    src="<?php echo get_template_directory_uri(); ?>/assets/logo.png" width="140"
                                    height="140" alt="">
                            </div>

                            <div class="d-flex align-items-center h-custom-2 px-5 ms-xl-4 mt-5 pt-5 pt-xl-0 mt-xl-n5">

                                <!--<form style="width: 23rem;">
                                    <div class="form-outline mb-4">
                                        <input type="email" id="form2Example18" class="form-control form-control-lg" />
                                        <label class="form-label" for="form2Example18">Email address</label>
                                    </div>-->

                                <!--<div class="form-outline mb-4" id="show_hide_password">
                                        <input type="password" id="form2Example28" class="form-control form-control-lg" />
                                        <label class="form-label" for="form2Example28">Password</label>
                                    </div>-->
                                <form>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" id="form2Example18" class="form-control form-control-lg" />
                                    </div>
                                    <div class="form-group">
                                        <label>Password</label>
                                        <div class="input-group" id="show_hide_password">
                                            <input class="form-control form-control-lg" id="form2Example18"
                                                type="password">
                                            <div class="input-group-addon">
                                                <a href=" " style="color:black;"><i class="fa fa-eye-slash"
                                                        aria-hidden="true">nascondi</i></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="login-btn">
                                        <button class="btn btn-warning btn-lg btn-block" type="button">Login</button>
                                    </div>
                                </form>



                                <!--<p class="small mb-5 pb-lg-2"><a class="text-muted" href="#!">Forgot password?</a></p>
                                    <p>Don't have an account? <a href="#!" class="link-info">Register here</a></p>-->

                                </form>

                            </div>

                        </div>
                    </div>
                </div>
                <!--<div class="col-2"></div>-->
            </div>
        </div>

    </div>


    <script type="text/javascript" src="<?php echo get_template_directory_uri() ?>/js/password.js"></script>

    <?php get_footer(); ?>