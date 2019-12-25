<?php

require 'db-conn.php';

if(isset($_POST['submit'])){
    
    $emailid = $_POST['emailid'];
    $password = $_POST['password'];
    $findbyemail = "SELECT userid,password FROM users WHERE emailid='$emailid'";

    $res = mysqli_query($conn,$findbyemail);
    $assoc = mysqli_fetch_all($res,MYSQLI_ASSOC);
    
    if($assoc != null){
        if($_POST['password'] == $assoc[0]['password']){
            session_start();
            $_SESSION['userid'] = $assoc[0]['userid'];
            header('location:dashboard.php');
        }
    }
    
    mysqli_free_result($res);
    mysqli_close($conn);
}
?>
<html>

<head>
    <title></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="cdn/bootstrap.css">
    <link rel="stylesheet" href="custom.css" type="text/css">
    <title>Social Media</title>
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: 'Josefin Sans', sans-serif;
        }

        body {
            background-color: #fff;
        }

    </style>

</head>

<body>
    <!-- this is just jumbotrone -->
    <div class="jumbotron jumbotron-fluid">
        <div class="container ml-5 pl-5">
            <h1 class="ml-5 pl-5">Chatting System Login</h1>
            <p class=" text-s pl-5 ml-5">We are glad that you came here<br>
                <?php if(isset($_GET['token'])){
                echo 'You Can Login Now';} ?></p>

            <a class="btn btn-light active px-3 ml-5 float-right">Login</a>
            <a class="btn btn-light px-3  float-right" href="index.php">SignUp</a>
        </div>
    </div>

    <div class="container">
        <form action="login.php" class="needs-validation w-50 mx-auto" method="post" novalidate>
            <div class="form-group">
                <label for="emailid">Email ID:</label>
                <input type="email" class="form-control" placeholder="Enter Email ID" name="emailid" required autocomplete="off">
                <div class="invalid-feedback">required</div>
                <div class="text-danger">
                    <?php if(isset($_POST['submit'])){
                        if($assoc == null){
                            echo 'No user exist with this email id';
                        }
                    } ?>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" placeholder="Enter password" name="password" required>
                <div class="invalid-feedback">required</div>
                <div class="text-danger">
                    <?php if(isset($_POST['submit'])){
                        if($assoc != null){
                            if($_POST['password'] != $assoc[0]['password'])
                                echo 'invalid password';
                        }
                    } ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" name="submit">Submit</button>
        </form>
    </div>

    <script src="cdn/jquery.js"></script>
    <script src="cdn/popper.js"></script>
    <script src="cdn/bootstrap.js"></script>
    <script>
        // Disable form submissions if there are invalid fields
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                // Get the forms we want to add validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

    </script>
</body>

</html>
