<?php

require 'db-conn.php';

session_start();
$userid = $_SESSION['userid'];
echo $userid;

$promo = "SELECT promotionname,promotionamount,promotionvalidity FROM freeuser WHERE userid = $userid";
$promo = mysqli_query($conn,$promo);
$promo = mysqli_fetch_assoc($promo);

if(isset($_POST['submit'])){
    $insertpaid = "INSERT into paiduser VALUES ($userid,450,date_add(now(),INTERVAL 3 MONTH),now())";
    if(mysqli_query($conn,$insertpaid)){
        $deletefree = "DELETE FROM freeuser WHERE userid = $userid";
        if(mysqli_query($conn,$deletefree)){
            header('location:dashboard.php');
        }
    }
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
            <h1 class="ml-5 pl-5">Chatting System Fee Payment</h1>
            <p class=" text-s pl-5 ml-5">We Hope You will enjoy our paid membership</p>
            <a class="btn btn-light active px-3 ml-5 float-right">Login</a>
            <a class="btn btn-light px-3  float-right" href="index.php">SignUp</a>
        </div>
    </div>

    <div class="container">
        <h4><?php echo $promo['promotionname'] ?></h4>
        <p><?php echo $promo['promotionamount'] ?></p>
        <p><?php echo $promo['promotionvalidity'] ?></p>

        <form action="promotion.php?amount" class="needs-validation w-50 mx-auto" method="post" novalidate>
            <button type="submit" class="btn btn-light" name="submit">Become Paid Member</button>
        </form>
    </div>
    
    <div height="200px" ></div>

    <script src="cdn/jquery.js"></script>
    <script src="cdn/popper.js"></script>
    <script src="cdn/bootstrap.js"></script>
    
</body>

</html>
