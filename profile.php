<?php
require 'db-conn.php';

session_start();
$userid = $_SESSION['userid'];
$queryid = $_GET['userid'];

if($userid == $queryid){
    if(isset($_POST['submit'])){
        if(($x = $_POST['pname']) !=''){
            $y= $_POST['pdes'];
            $sql = "INSERT INTO pages(name,description,adminid) VALUES('$x','$y',$userid)";
            if(!mysqli_query($conn,$sql)){ 
                echo 'failed'.mysqli_error($conn);
            }
        }
        
        if(($x = $_POST['firstname']) !=''){
            $sql = "UPDATE users SET firstname = '$x' WHERE userid = $userid";
            if(!mysqli_query($conn,$sql)){
                echo 'failed'.mysqli_error($conn);
            }
        }
        if(($x = $_POST['lastname'])!=''){
            $sql = "UPDATE users SET lastname = '$x' WHERE userid = $userid";
            if(!mysqli_query($conn,$sql)){
                echo 'failed'.mysqli_error($conn);
            }
        }
        if(($x = $_POST['description'])!=''){
            $sql = "UPDATE users SET description = '$x' WHERE userid = $userid";
            if(!mysqli_query($conn,$sql)){
                echo 'failed'.mysqli_error($conn);
            }
        }
        if(($x = $_POST['dob'])!=''){
            $sql = "UPDATE users SET dob = '$x' WHERE userid = $userid";
            if(!mysqli_query($conn,$sql)){
                echo 'failed'.mysqli_error($conn);
            }
        }
        if(($x = $_POST['password'])!=''){
            $sql = "UPDATE users SET password = '$x' WHERE userid = $userid";
            if(!mysqli_query($conn,$sql)){
                echo 'failed'.mysqli_error($conn);
            }
        }
        if(($x = $_POST['phoneno'])!=''){
            $sql = "INSERT INTO phoneno VALUES($x,$userid)";
            if(!mysqli_query($conn,$sql)){
                echo 'failed'.mysqli_error($conn);
            }
        }
        if(($x = $_POST['emailid'])!=''){
            $sql = "UPDATE users SET emailid = '$x' WHERE userid = $userid";
            if(!mysqli_query($conn,$sql)){
                echo 'failed'.mysqli_error($conn);
            }
        }
    }    
}else{
    $ftype = 0;
    $sql = "SELECT * FROM friends WHERE userid = $userid AND friendid = $queryid UNION SELECT * FROM friends WHERE userid = $queryid AND friendid = $userid";
    $sql = mysqli_query($conn,$sql);
    $sql = mysqli_fetch_assoc($sql);
    if(!is_null($sql) && count($sql)>0){
        $ftype=1;
    }else{
        $sql = "SELECT * FROM friendpending WHERE senderid = $queryid AND userid =$userid";
        $sql = mysqli_query($conn,$sql);
        $sql = mysqli_fetch_assoc($sql);
        if(!is_null($sql) && count($sql)>0){
            $ftype=2;
        }else{
            $sql = "SELECT * FROM friendpending WHERE senderid = $userid AND userid =$queryid";
            $sql = mysqli_query($conn,$sql);
            $sql = mysqli_fetch_assoc($sql);
            if(!is_null($sql) && count($sql)>0)
                $ftype=3;
            else
                $ftype=4;
        }   
    }

    if(isset($_GET['ftype'])){
        if(($x = $_GET['ftype']) == 1){
            $sql = "DELETE FROM friends WHERE friendid = $queryid AND userid = $userid";
            if(!mysqli_query($conn,$sql)){
                echo 'failed'.mysqli_error($conn);
            }
            $sql = "DELETE FROM friends WHERE userid = $queryid AND friendid = $userid";
            if(!mysqli_query($conn,$sql)){
                echo 'failed'.mysqli_error($conn);
            }
            $ftype = 4;
        }
        else if(($x = $_GET['ftype']) == 2){
            $sql = "INSERT INTO friends VALUES($queryid,$userid)";
            if(!mysqli_query($conn,$sql)){
                echo 'failed'.mysqli_error($conn);
            }
            $sql = "DELETE FROM friendpending WHERE senderid = $queryid AND userid = $userid";
            if(!mysqli_query($conn,$sql)){
                echo 'failed'.mysqli_error($conn);
            }
            $ftype = 1;
        }
        else if(($x = $_GET['ftype']) == 4){
            $sql = "INSERT INTO friendpending VALUES($userid,$queryid)";
            if(!mysqli_query($conn,$sql)){
                echo 'failed'.mysqli_error($conn);
            }
            $ftype = 3;
        }

    }
}


$userdetails = "SELECT CONCAT(firstname,' ',lastname) AS name,description FROM users WHERE userid = $userid";

$friendslist = "(SELECT CONCAT(firstname,' ',lastname) AS name,userid AS friendid FROM users WHERE userid IN (SELECT friendid FROM friends WHERE userid = $userid))UNION(SELECT CONCAT(firstname,' ',lastname) AS name,userid AS friendid FROM users WHERE userid IN (SELECT userid FROM friends WHERE friendid = $userid))";

$friendreqlist = "SELECT CONCAT(firstname,' ',lastname) AS name,userid as senderid FROM users WHERE userid IN (SELECT senderid FROM friendpending WHERE userid = $userid)";

$likedpages = "SELECT name,p.pageid FROM pages p,follows f WHERE p.pageid = f.pageid AND f.userid = $userid";

$adminpages = "SELECT pageid,name FROM pages p WHERE p.adminid = $userid";

$promotions = "SELECT promotionname,promotionamount,promotionvalidity FROM freeuser WHERE userid = $userid";

$posts = "SELECT o.postid,p.name,o.datecreated,o.content FROM pages p, post o WHERE p.pageid = o.pageid AND p.pageid IN (SELECT p1.pageid FROM pages p1,follows f WHERE p.pageid = f.pageid AND f.userid = $userid) ORDER BY o.datecreated DESC";

$postslikes = "SELECT i.postid,COUNT(i.liked) as likes FROM interacts i WHERE i.postid IN(SELECT o.postid FROM post o WHERE o.pageid IN (SELECT p.pageid FROM pages p,follows f WHERE p.pageid = f.pageid AND f.userid = $userid)) AND i.liked = 1 GROUP BY i.postid";

$postcomments = "SELECT i.postid,COUNT(i.comments) as comments FROM interacts i WHERE i.postid IN(SELECT o.postid FROM post o WHERE o.pageid IN (SELECT p.pageid FROM pages p,follows f WHERE p.pageid = f.pageid AND f.userid = $userid)) AND i.comments !='' GROUP BY i.postid";

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

$posts = mysqli_query($conn,$posts);
$posts = mysqli_fetch_all($posts,MYSQLI_ASSOC);

$postslikes = mysqli_query($conn,$postslikes);
$postslikes = mysqli_fetch_all($postslikes,MYSQLI_ASSOC);

$postcomments = mysqli_query($conn,$postcomments);
$postcomments = mysqli_fetch_all($postcomments,MYSQLI_ASSOC);

$userinfo = "SELECT CONCAT(firstname,' ',lastname) AS name,emailid,description,dob FROM users WHERE userid = $queryid";
$userinfo = mysqli_query($conn,$userinfo);
$userinfo = mysqli_fetch_all($userinfo,MYSQLI_ASSOC);

$phoneno = "SELECT pnum FROM phoneno WHERE userid = $queryid";
$phoneno = mysqli_query($conn,$phoneno);
$phoneno = mysqli_fetch_all($phoneno,MYSQLI_ASSOC);

$friendslist2 = "(SELECT CONCAT(firstname,' ',lastname) AS name,userid AS friendid FROM users WHERE userid IN (SELECT friendid FROM friends WHERE userid = $queryid))UNION(SELECT CONCAT(firstname,' ',lastname) AS name,userid AS friendid FROM users WHERE userid IN (SELECT userid FROM friends WHERE friendid = $queryid))";
$friendslist2 = mysqli_query($conn,$friendslist2);
$friendslist2 = mysqli_fetch_all($friendslist2,MYSQLI_ASSOC);

$likedpages2 = "SELECT name,p.pageid FROM pages p,follows f WHERE p.pageid = f.pageid AND f.userid = $queryid";
$likedpages2 = mysqli_query($conn,$likedpages2);
$likedpages2 = mysqli_fetch_all($likedpages2,MYSQLI_ASSOC);
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
                    <a class="nav-link active px-3" href="profile.php?userid=<?php echo $userid ?>">Profile</a>
                </li>
                <li class="nav-item mx-3">
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
                <div class="card list shadow">
                    <div class="card-block p-5">
                        <div class="post card-title">
                            <div class="post-title mr-5 pr-2 text-center">
                                <h3><?php echo $userinfo[0]['name'] ?></h3>
                                <p class="text-muted"><?php echo $userinfo[0]['description'] ?></p>
                                <p><?php echo $userinfo[0]['emailid'] ?></p>
                                <p><?php echo $userinfo[0]['dob'] ?></p>
                                <?php foreach($phoneno as $pnum){ ?>
                                <p><?php echo $pnum['pnum'] ?> </p>
                                <?php } ?>
                            </div>
                            <div class="clearfix"></div>

                        </div>
                        <ul class="float-left ml-5">
                            <h4>Friends</h4>
                            <?php foreach($friendslist2 as $row){ ?>
                            <li><a style="font-size:18px" href="profile.php?userid=<?php echo $row['friendid'] ?>"><?php echo $row['name'] ?></a></li>
                            <?php } ?>
                        </ul>
                        <ul class="float-right mr-5">
                            <h4>Pages Liked</h4>
                            <?php foreach($likedpages2 as $row){ ?>
                            <li><a style="font-size:18px" href="pages.php?pageid=<?php echo $row['pageid'] ?>"><?php echo $row['name'] ?></a></li>
                            <?php } ?>
                        </ul>
                        <div class="clearfix"></div>
                        <?php if($userid == $queryid){ ?>
                        <div>
                            <form action="profile.php?userid=<?php echo $userid; ?>" method="post" class="needs-validation" novalidate>
                                <div class="form-group">
                                    <label for="firstname">Change First Name:</label>
                                    <input type="text" class="form-control" placeholder="Enter First Name" name="firstname" required autocomplete="off">
                                </div>

                                <div class="form-group">
                                    <label for="lastname">Change Last Name:</label>
                                    <input type="text" class="form-control" placeholder="Enter Last Name" name="lastname" required autocomplete="off">
                                </div>

                                <div class="form-group">
                                    <label for="phoneno">Add Phone Number:</label>
                                    <input type="number" class="form-control" placeholder="Enter Phone Number" name="phoneno" required autocomplete="off">
                                </div>

                                <div class="form-group">
                                    <label for="emailid">Change Email ID:</label>
                                    <input type="email" class="form-control" placeholder="Enter Email ID" name="emailid" required autocomplete="off">
                                </div>

                                <div class="form-group">
                                    <label for="description">Change Profile Description:</label>
                                    <input type="text" class="form-control" placeholder="Profile Description" name="description" required autocomplete="off">
                                </div>

                                <div class="form-group">
                                    <label for="dob">Change Date of Birth:</label>
                                    <input type="date" class="form-control" name="dob" required autocomplete="off">
                                </div>

                                <div class="form-group">
                                    <label for="password">Change Password:</label>
                                    <input type="password" class="form-control" placeholder="Enter password" name="password" required>
                                </div>

                                <div class="form-group">
                                    <label for="pname">Create Page Name</label>
                                    <input type="text" class="form-control" name="pname" required autocomplete="off">
                                </div>

                                <div class="form-group">
                                    <label for="pdes">Create page description</label>
                                    <input type="text" class="form-control" name="pdes" required>
                                </div>

                                <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                            </form>
                        </div>
                        <?php }else{ ?>
                        <div>
                            <a class="btn btn-info" href="profile.php?userid=<?php echo $queryid ?>&ftype=<?php echo $ftype ?>"><?php if($ftype == 1){
                                    echo 'UNFRIEND';
                                }else if($ftype == 2)
                                    echo 'ACCEPT FRIEND REQUEST';
                                    else if($ftype == 3){
                                        echo 'WAITING FOR CONFIRMATION';
                                    }else{
                                        echo 'SEND FRIEND REQUEST';
                                    }
                            ?></a>
                            <a class="btn ml-5 btn-info" href="messages.php?friendid=<?php echo $queryid ?>&ftype=<?php echo $ftype ?>"> Send Message</a>

                        </div>

                        <?php } ?>
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

    <script type="application/javascript">
        function likebtn(clicked) {
            alert(clicked);
            return false;
        }

        function commentbtn(clicked) {
            alert(clicked);
            return false;
        }

    </script>

</body>

</html>
