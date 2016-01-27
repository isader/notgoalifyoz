<?php

    include_once("logic/login.php");

    $isLogin = (isset($isLogin)) ? $isLogin : $_POST['login_flag'];
    $isSignup = (isset($isSignup)) ? $isSignup : $_POST['signup_flag'];

    if ($isLogin) {
        $email = (isset($email)) ? $email : $_POST['email'];
        $password = (isset($password)) ? $password : $_POST['password'];

        $login_result = login($email, $password);

        if (mysql_num_rows($login_result) == 1) {
            while($user = mysql_fetch_array($login_result)) {
                $user_id = $user['user_id'];
                $first_name = $user['first_name'];
                $last_name = $user['last_name'];
                $age = $user['age'];
            }
            $user_cookie = '{"user_id":'.$user_id.', "first_name":"'.$first_name.'", "last_name": "'.$last_name.'", "age": "'.$age.'"}';
            setcookie("ud", $user_cookie);
            header('Location: index.php');
        }
        else {
            $login_failed = 1;
        }
    }
    else if ($isSignup) {
        $first_name = (isset($first_name)) ? $first_name : $_POST['first_name'];
        $last_name = (isset($last_name)) ? $last_name : $_POST['last_name'];
        $age = (isset($age)) ? $age : $_POST['age'];
        $email = (isset($email)) ? $email : $_POST['email'];
        $password = (isset($password)) ? $password : $_POST['password'];

        $signup_result = signup($first_name, $last_name, $age, $email, $password);
    }
?>

<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>SettleIn</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <!-- Place favicon.ico in the root directory -->

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="bower_components/magnific-popup/dist/magnific-popup.css">
        <link rel="stylesheet" href="css/main.css">
        <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    </head>
    <body id="login">
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <div class="login-wrapper">
            <div class="login-inner-wrapper">
                <form id="login-form" class="login-form <?php if($signup_result === "exists") {echo 'hidden';} else{echo '';} ?>" method="post" action="login.php">
                    <h1>SettleIn</h1>
                    <?php
                    if ($signup_result == 1) {
                    ?>
                    <h2>Sign up complete!</h2>
                    <p class="signup-success">Congratulations! Your account has been successfully set up. You can now login as a registered user.</p>
                    <?php
                    }

                    if ($login_failed == 1) {
                    ?>
                    <h2>Login failed!</h2>
                    <p class="signup-success">Unfortunately, the login credentials provided are incorrect. Please try again.</p>
                    <?php
                    }
                    ?>
                    <input type="text" name="email" class="email" placeholder="Email" />
                    <input type="password" name="password" class="password" placeholder="Password" />
                    <!--
                    <p class="forgot-password">
                        <a href="#">Forgot password?</a>
                    </p>
                    -->
                    <input type="hidden" name="login_flag" value="1"/>
                    <input type="submit" value="Sign In" />
                    <a href="#" class="login-bottom-link j-switch-to-signup">Sign up as a new user</a>
                </form>

                <form id="signup-form" class="login-form <?php if($signup_result === "exists") {echo '';} else{echo 'hidden';} ?>" method="post" action="login.php">
                    <h1>SettleIn</h1>
                    <?php
                    if ($signup_result === "exists") {
                    ?>
                    <p class="signup-success">It appears the email address you used already exists. Please try to login using your existing credentials.</p>
                    <?php
                    }
                    ?>
                    <h2>Sign up</h2>
                    <input type="text" name="first_name" class="first-name" placeholder="First name" />
                    <input type="text" name="last_name" class="last-name" placeholder="Last name" />
                    <input type="text" name="age" class="age" placeholder="Age" />
                    <input type="text" name="email" class="email" placeholder="Email" />
                    <input type="password" name="password" class="password" placeholder="Password" />
                    <input type="hidden" name="signup_flag" value="1"/>
                    <input type="submit" value="Sign Up" />
                    <a href="#" class="login-bottom-link j-switch-to-login">Existing user? Sign in now</a>
                </form>
            </div>
        </div>


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
        <script src="bower_components/magnific-popup/dist/jquery.magnific-popup.min.js"></script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='https://www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X','auto');ga('send','pageview');
        </script>
    </body>
</html>
