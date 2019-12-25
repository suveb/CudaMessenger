<?php
require 'db-conn.php';

session_start();
$userid = $_SESSION['userid'];
$friendid = $_GET['friendid'];

if(isset($_POST['submit'])){
    $msg = $_POST['msg'];
    $insertmsg = "INSERT INTO messages(userid,friendid,content) VALUES($userid,$friendid,'$msg')";
    if(mysqli_query($conn,$insertmsg)){
    }else{
        echo 'failed'.mysqli_error($conn);
    }
}

$messages = "(SELECT * FROM messages WHERE friendid = $friendid AND userid = $userid) UNION (SELECT * FROM messages WHERE friendid = $userid AND userid = $friendid) ORDER BY datesent DESC";

$friendinfo = "SELECT CONCAT(firstname,' ',lastname) as name FROM users WHERE userid = $friendid";

$userdetails = "SELECT CONCAT(firstname,' ',lastname) AS name,description FROM users WHERE userid = $userid";

$friendslist = "(SELECT CONCAT(firstname,' ',lastname) AS name,userid AS friendid FROM users WHERE userid IN (SELECT friendid FROM friends WHERE userid = $userid))UNION(SELECT CONCAT(firstname,' ',lastname) AS name,userid AS friendid FROM users WHERE userid IN (SELECT userid FROM friends WHERE friendid = $userid))";

$friendreqlist = "SELECT CONCAT(firstname,' ',lastname) AS name,userid as senderid FROM users WHERE userid IN (SELECT senderid FROM friendpending WHERE userid = $userid)";

$likedpages = "SELECT name,p.pageid FROM pages p,follows f WHERE p.pageid = f.pageid AND f.userid = $userid";

$adminpages = "SELECT pageid,name FROM pages p WHERE p.adminid = $userid";

$promotions = "SELECT promotionname,promotionamount,promotionvalidity FROM freeuser WHERE userid = $userid";

$friendinfo = mysqli_query($conn,$friendinfo);
$friendinfo = mysqli_fetch_all($friendinfo,MYSQLI_ASSOC);

$messages = mysqli_query($conn,$messages);
$messages = mysqli_fetch_all($messages,MYSQLI_ASSOC);

$userdetails = mysqli_query($conn,$userdetails);
$userdetails = mysqli_fetch_assoc($userdetails);

$friendslist = mysqli_query($conn,$friendslist);
$friendslist = mysqli_fetch_all($friendslist,MYSQLI_ASSOC);

$likedpages = mysqli_query($conn,$likedpages);
$likedpages = mysqli_fetch_all($likedpages,MYSQLI_ASSOC);

$friendreqlist = mysqli_query($conn,$friendreqlist);
$friendreqlist = mysqli_fetch_all($friendreqlist,MYSQLI_ASSOC);

$freeuser = "SELECT userid FROM freeuser WHERE userid = $userid";
$freeuser = mysqli_query($conn,$freeuser);
$freeuser = mysqli_fetch_all($freeuser,MYSQLI_ASSOC);
if(count($freeuser)==0){
    $_SESSION['type'] = "P";
    $adminpages = mysqli_query($conn,$adminpages);
    $adminpages = mysqli_fetch_all($adminpages,MYSQLI_ASSOC);


}else{
    $_SESSION['type'] = "F";
    $promotions = mysqli_query($conn,$promotions);
    $promotions = mysqli_fetch_all($promotions,MYSQLI_ASSOC);
}


?>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" type="text/css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="custom.css" type="text/css">

    <title>Social Media</title>
</head>

<body>
    <!--Navigation-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-info">
        <a class="navbar-brand" href="index.php">
            <img src="images/logo.png" width="140" height="50" class="d-inline-block m-2" alt="logo">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigator" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navigator">
            <form action="search-result.php" method="post" class="form-inline ml-5">
                <input class="form-control round-input shadow-sm" type="search" name="query" size="40" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success px-3 mx-3" type="submit" name="search">Search</button>
            </form>
            <ul class="btn-nav navbar-nav">
                <li class="nav-item mx-3">
                    <a class="nav-link px-3" href="dashboard.php">Home</a>
                </li>
                <li class="nav-item mx-3">
                    <a class="nav-link px-3" href="profile.php?userid=<?php echo $userid ?>">Profile</a>
                </li>
                <li class="nav-item active mx-3">
                    <a class="nav-link px-3" href="messages-list.php">Messages</a>
                </li>
                <li class="nav-item mx-3">
                    <a class="nav-link px-3" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container main p-0" style="max-width:2400px; width: 1170px">
        <!-- Grid Layout -->
        <div class="row">

            <!--Colomn-1-->
            <div class="col-2 m-0 p-0">
                <div class="card shadow overflow-auto" style="height: 510px">
                    <div class="card-block m-3">
                        <h3 class="card-title align-center"><?php
    echo $userdetails['name'];
                                ?></h3>
                        <p class="align-center text-muted"><?php
                                if(!is_null($userdetails['description'])){
                                    echo $userdetails['description'];
                                }
                                ?></p>

                        <h4><?php if(count($friendslist)>0){
                                    echo 'Friends';
                                } ?></h4>
                        <ul><?php foreach($friendslist as $row){ ?>
                            <li><a href=profile.php?userid=<?php echo $row['friendid'] ?>><?php echo $row['name']; ?></a></li>
                            <?php } ?>

                        </ul>

                        <div>
                            <h4><?php if(count($friendreqlist)>0){echo 'Pending Requests';} ?></h4>
                            <ul>
                                <?php foreach($friendreqlist as $row){ ?>
                                <li><a class="btn" href=profile.php?userid=<?php echo $row['senderid'] ?>><?php echo $row['name']; ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!--Colomn-2-->
            <div class="col-8 overflow-auto" style="height: 510px">

                <!--Card-->
                <div class="card list my-3 p-5 shadow">
                    <?php foreach($messages as $row){ ?>
                    <div class="jumbotron jumbotron-fluid rounded">
                        <div class="container">
                            <h3><?php if($row['userid'] == $userid && $row['friendid']=$friendid)
    echo $userdetails['name'];
                                                     else if($row['userid'] == $friendid && $row['friendid']=$userid)
                               echo $friendinfo[0]['name']  ?></h3>
                            <p class="text-muted"><?php echo $row['datesent']; ?></p>
                            <p class="text-s"><?php echo $row['content']; ?></p>
                        </div>
                    </div>
                    <?php } ?>

                    <div class="container">
                        <form action="messages.php?friendid=<?php echo $friendid ?>" class="needs-validation mx-auto" method="post" novalidate>
                            <div class="form-group">
                                <label for="msg">Your Message:</label>
                                <input type="textarea" style="height:200px" class="form-control" name="msg" required autocomplete="off">
                            </div>
                            <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                        </form>
                    </div>

                </div>

            </div>

            <!--Colomn-3-->
            <div class="col-2 m-0 p-0">
                <div class="card shadow overflow-auto" style="height: 510px">
                    <div class="card-block p-3">
                        <div>
                            <h4><?php if($_SESSION['type'] == "F"){
    echo 'Promotions';
}
                                    ?>
                            </h4>
                            <ul><?php if($_SESSION['type'] == "F"){ ?>
                                <li><a href="promotion.php"><?php echo $promotions[0]['promotionname'] ?></a></li>
                                <?php } ?>
                            </ul>
                            <h4><?php if($_SESSION['type'] == "P"){echo 'Admin Pages';}
                                    ?>
                            </h4>
                            <ul><?php if($_SESSION['type'] == "P"){
    foreach($adminpages as $row){ ?>
                                <li><a href="pages.php?pageid=<?php echo $row['pageid'] ?>"><?php echo $row['name'] ?></a></li>
                                <?php }}?>
                            </ul>
                            <h4><?php if(count($likedpages)>0){echo 'Liked Pages';} ?></h4>
                            <ul><?php 
                                    foreach($likedpages as $row){ ?>
                                <li><a href="pages.php?pageid=<?php echo $row['pageid'] ?>"><?php echo $row['name'] ?></a></li>
                                <?php }?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</body>

</html>
