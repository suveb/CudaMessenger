<?php
require 'db-conn.php';

session_start();
$userid = $_SESSION['userid'];

$pageid = $_GET['pageid'];

$ftype = 0;
$sql = "SELECT * FROM follows WHERE userid = $userid AND pageid = $pageid";
$sql = mysqli_query($conn,$sql);
$sql = mysqli_fetch_assoc($sql);
if(!is_null($sql) && count($sql)>0){
    $ftype=1;// I follows
}else{
    $ftype=2;// doesnt follow
}

if(isset($_GET['ftype'])){
    if(($x = $_GET['ftype']) == 1){//change to not follow
        $sql = "DELETE FROM follows WHERE pageid = $pageid AND userid = $userid";
        if(!mysqli_query($conn,$sql)){
            echo 'failed'.mysqli_error($conn);
        }
        $ftype = 2;
    }
    else if(($x = $_GET['ftype']) == 2){//change to follow
        $sql = "INSERT INTO follows VALUES($userid,$pageid)";
        if(!mysqli_query($conn,$sql)){
            echo 'failed'.mysqli_error($conn);
        }
        $ftype = 1;
    }
}

if(isset($_POST['submit'])){
    $content = $_POST['content'];
    $insertpost = "INSERT INTO post(pageid,content) VALUES($pageid,'$content')";

    if(mysqli_query($conn,$insertpost)){
    }else{
        echo 'failed'.mysqli_error($conn);
    }
}

$countfollows = "SELECT COUNT(userid) AS count FROM follows WHERE pageid=$pageid GROUP BY pageid";
$countfollows = mysqli_query($conn,$countfollows);
$countfollows = mysqli_fetch_all($countfollows,MYSQLI_ASSOC);

$pageinfo = "SELECT name,description,adminid FROM pages WHERE pageid = $pageid";
$postlist = "SELECT postid,content,datecreated FROM post WHERE pageid = $pageid ORDER BY datecreated DESC";

$postslikes = "SELECT i.postid,COUNT(i.liked) as likes FROM interacts i,post o WHERE i.postid = o.postid AND o.pageid = $pageid AND i.liked = 1 GROUP BY i.postid";

$postcomments = "SELECT i.postid,COUNT(i.comments) as comments FROM interacts i,post o WHERE i.postid = o.postid AND o.pageid = $pageid AND i.comments != '' GROUP BY i.postid";

$pageinfo = mysqli_query($conn,$pageinfo);
$pageinfo = mysqli_fetch_all($pageinfo,MYSQLI_ASSOC);

$postlist = mysqli_query($conn,$postlist);
$postlist = mysqli_fetch_all($postlist,MYSQLI_ASSOC);

$postslikes = mysqli_query($conn,$postslikes);
$postslikes = mysqli_fetch_all($postslikes,MYSQLI_ASSOC);

$postcomments = mysqli_query($conn,$postcomments);
$postcomments = mysqli_fetch_all($postcomments,MYSQLI_ASSOC);


$userdetails = "SELECT CONCAT(firstname,' ',lastname) AS name,description FROM users WHERE userid = $userid";

$friendslist = "(SELECT CONCAT(firstname,' ',lastname) AS name,userid AS friendid FROM users WHERE userid IN (SELECT friendid FROM friends WHERE userid = $userid))UNION(SELECT CONCAT(firstname,' ',lastname) AS name,userid AS friendid FROM users WHERE userid IN (SELECT userid FROM friends WHERE friendid = $userid))";

$friendreqlist = "SELECT CONCAT(firstname,' ',lastname) AS name,userid as senderid FROM users WHERE userid IN (SELECT senderid FROM friendpending WHERE userid = $userid)";

$likedpages = "SELECT name,p.pageid FROM pages p,follows f WHERE p.pageid = f.pageid AND f.userid = $userid";

$adminpages = "SELECT pageid,name FROM pages p WHERE p.adminid = $userid";

$promotions = "SELECT promotionname,promotionamount,promotionvalidity FROM freeuser WHERE userid = $userid";

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
                            <div class="post-title">
                                <a href="#"><?php echo $pageinfo[0]['name']; ?></a>
                            </div>
                        </div>
                        <p class="lead"><?php echo $pageinfo[0]['description']; ?></p>
                    </div>

                    <div><a class="btn ml-5 mb-3 btn-info" href="pages.php?pageid=<?php echo $pageid ?>&ftype=<?php echo $ftype ?>">
                            <?php 
                                    if(count($countfollows) != 0){
                                            if($ftype == 1){
                                                echo 'UNFOLLOW '.$countfollows[0]['count'];
                                            }else if($ftype == 2)
                                                echo 'FOLLOW '.$countfollows[0]['count'];
                                        }else{
                                        if($ftype == 1){
                                            echo 'UNFOLLOW 0';
                                        }else if($ftype == 2)
                                            echo 'FOLLOW 0';
                            }
                            ?></a>
                    </div>

                    <?php if($userid == $pageinfo[0]['adminid']){ ?>
                    <div class="container">
                        <form action="pages.php?pageid=<?php echo $pageid ?>" class="needs-validation mx-auto" method="post" novalidate>
                            <div class="form-group">
                                <label for="content">Your Post:</label>
                                <input type="textarea" style="height:200px" class="form-control" name="content" required autocomplete="off">
                            </div>
                            <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                        </form>
                    </div>
                    <?php } ?>
                </div>

                <?php if(count($postlist)>0){
    foreach($postlist as $row){ ?>
                <div class="card list my-3 shadow">
                    <div class="card-block p-5">
                        <div class="post card-title">
                            <div class="post-title">
                                <a href="#"><?php echo $pageinfo[0]['name']; ?></a>
                                <p class="text-muted"><?php echo $row['datecreated']; ?></p>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <p class="lead"><?php echo $row['content']; ?></p>
                        <div class="interact mt-3">
                            <a class="d-block align-center py-1 float-left border border-blue rounded-lg" href="posts.php?postid=<?php echo $row['postid'] ?>">
                                <?php 
        $t = 0;
                               foreach($postslikes as $like){
                                   if($like['postid']== $row['postid']){
                                       $t = 1;
                                       echo $like['likes']." ";
                                   }
                               }
                               if($t == 0)
                                   echo '0 ';?> Likes
                            </a>
                            <a class="d-block align-center py-1 float-right border border-blue rounded-lg" href="posts.php?postid=<?php echo $row['postid'] ?>">
                                <?php 
                                       $t = 0;          
                               foreach($postcomments as $comment){
                                   if($comment['postid']== $row['postid']){
                                       $t = 1;
                                       echo $comment['comments']." ";
                                   }
                               }
                               if($t == 0)
                                   echo '0 ';?>Comments
                            </a>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <?php } }  ?>

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
