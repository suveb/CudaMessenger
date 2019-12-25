<?php
    
    require 'db-conn.php';
    
    if(isset($_POST['submit'])){
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $emailid = $_POST['emailid'];
        $password = $_POST['password'];
        $dob = $_POST['dob'];
        $phoneno = $_POST['phoneno'];

        $insertuser = "INSERT INTO users(firstname,lastname,emailid,dob,password) VALUES('$firstname','$lastname','$emailid','$dob','$password');";
        $getuserid = "SELECT userid FROM users WHERE emailid='$emailid'";
        
        if(mysqli_query($conn,$insertuser)){
            
            $res = mysqli_query($conn,$getuserid);
            $assoc = mysqli_fetch_all($res,MYSQLI_ASSOC);
            $userid = $assoc[0]['userid'];
            $insertfree = "INSERT INTO freeuser(userid) VALUES($userid);";
            $insertphone = "INSERT INTO phoneno VALUES($phoneno,$userid);";
            mysqli_query($conn,$insertphone);
            mysqli_query($conn,$insertfree);
            
            mysqli_free_result($res);
            mysqli_close($conn);

            header('location:login.php?token=1');
            
        }else{
            echo 'failed'.mysqli_error($conn);
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
            <h1 class="ml-5 pl-5">Chatting System SignUp</h1>
            <p class=" text-s pl-5 ml-5">We are glad that you came here.</p>
            <a class="btn btn-light px-3 ml-5 float-right" href="login.php">Login</a>
            <a class="btn btn-light active px-3 float-right">SignUp</a>
        </div>
    </div>

    <div class="container">
        <form action="index.php" method="post" class="needs-validation w-50 mx-auto" novalidate>
            <div class="form-group">
                <label for="firstname">First Name:</label>
                <input type="text" class="form-control" placeholder="Enter First Name" name="firstname" required autocomplete="off">
                <div class="invalid-feedback">required</div>
            </div>

            <div class="form-group">
                <label for="lastname">Last Name:</label>
                <input type="text" class="form-control" placeholder="Enter Last Name" name="lastname" required autocomplete="off">
                <div class="invalid-feedback">required</div>
            </div>

            <div class="form-group">
                <label for="phoneno">Phone Number:</label>
                <input type="number" class="form-control" placeholder="Enter Phone Number" name="phoneno" required autocomplete="off">
                <div class="invalid-feedback">required</div>
            </div>

            <div class="form-group">
                <label for="emailid">Email ID:</label>
                <input type="email" class="form-control" placeholder="Enter Email ID" name="emailid" required autocomplete="off">
            </div>

            <div class="form-group">
                <label for="dob">Date of Birth:</label>
                <input type="date" class="form-control" name="dob" required autocomplete="off">
                <div class="invalid-feedback">required</div>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" placeholder="Enter password" name="password" required>
                <div class="invalid-feedback">required</div>
            </div>

            <div class="form-group form-check">
                <label class="form-check-label">
                    <input class="form-check-input" type="checkbox" name="tnc" required>I agree on Terms and Conditions
                    <div class="invalid-feedback">Check this checkbox to continue</div>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" name="submit">Submit</button>
        </form>
    </div>

    <div style="height: 100px">

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
