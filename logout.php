<?php

require 'db-conn.php';

session_unset();
mysqli_close($conn);

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
            <h1 class="ml-5 pl-5">Chatting System LogOut</h1>
            <p class=" text-s pl-5 ml-5">Thank You for Using our services</p>
            <a class="btn btn-light px-3 ml-5 float-right" href="login.php">Login</a>
            <a class="btn btn-light px-3  float-right" href="index.php">SignUp</a>
        </div>
    </div>

    <script src="cdn/jquery.js"></script>
    <script src="cdn/popper.js"></script>
    <script src="cdn/bootstrap.js"></script>
</body>

</html>
