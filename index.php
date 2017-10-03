<?php
include('db.php');
include("login.php");
include("register.php");
include("entities.php");
include("displayMatches.php");
include("boundingbox.php");
session_start();

$id = "";
$error = "";
$row = "";
$name = "";
$radius;

if (isset($_POST['radius'])) {
    $_SESSION['radius'] = intval($_POST['radius']);
    $radius = $_SESSION['radius'];
} else {
    $radius = 0;
}

if (isset($_POST['username']) && isset($_POST['password'])
   && isset($_POST['email']) && isset($_POST['name']) &&
   isset($_POST['sex'])) {
    echo (register(toHTMLEntities($_POST['username']), toHTMLEntities($_POST['password']), toHTMLEntities($_POST['name']), toHTMLEntities($_POST['email']), toHTMLEntities($_POST['sex'])));
}

if (empty($_POST['user']) || empty($_POST['pass'])) {
    //ta arvab, et need pole täidetud
    //echo ("Both fields have to be filled");
    //echo "$error";
} else {
    echo (logIn(toHTMLEntities($_POST['user']), toHTMLEntities($_POST['pass'])));
}

$loggedIn = isset($_SESSION['id']);

if ($loggedIn) {
    $id = $_SESSION['id'];
}
$hasName = isset($_SESSION['name']);

if ($hasName) {
    $name = $_SESSION['name'];
}

function getMatch($array) {
    $arrayLength = count($array);
    $random = rand(0, $arrayLength - 1);
    return $array[$random];
}
?>

<!doctype html>
<html>

<head>
    <meta charset="utf-8;" name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Kinder</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</head>

<body onload="getLocation()">
    <header>
        <div class="headerLogo"><a href="index.php" id="logo"><img border="0" alt="CinderLogo" src="images/Tinder_logo.png" height="40px"></a>
        
        <?php if ($loggedIn): ?>
            <form method="post" class="logOutForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div class="dropdown pull-right">
                    <button id="username" class="btn btn-success headerButton dropdown-toggle" type="button" id="dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <?= $name ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu">
                        <li><a href="" data-toggle="modal" data-target="#textModal">Write status</a></li>
                        <li><a href="" data-toggle="modal" data-target="#upload">Upload image</a></li>
                        <li><a href="<?=toHTMLEntities("profile.php")?>">Profile</a></li>
                        <li><a href="" data-toggle="modal" data-target="#matches">Matches</a></li>
                        <?php if (isset($_SESSION['lat']) && isset($_SESSION['lon'])): ?>
                        <li><a href="" data-toggle="modal" data-target="#radius">Search radius</a></li>
                        <?php endif; ?>
                        <li><a href="<?=toHTMLEntities("logout.php")?>" name="logout">Log out</a></li>
                    </ul>
                </div>
                
                <?php if (file_exists("uploads/$id.jpg")):?>
                <span class='bounding-box pull-right'>
                    <img src="<?="uploads/$id.jpg"?>" class="profile" alt="">
                </span>
                <?php else: ?>
                    <button type="button" data-toggle="modal" data-target="#upload" class="btn btn-danger pull-right headerButton">Upload image</button>
                <?php endif; ?>
            </form>
            </div>
        
        <?php else: ?>
                <button type="button" class="btn btn-success loginButton headerButton pull-right" data-toggle="modal" data-target="#login">Log In</button>
                <button type="button" class="btn btn-success headerButton pull-right" data-toggle="modal" data-target="#registration">Register</button>
            </div>
        <?php endif; ?>
    </header>
    
    <?php if ($loggedIn):
    $id = $_SESSION['id'];
    $query = mysqli_query($connection, "select * from users where id=$id");
    $user_from_db = mysqli_fetch_assoc($query);
    
    $gender = $user_from_db['sex'];
    $photo = $user_from_db['photo'];
    
    include("getUsers.php");
    
    $users = getUsers();
    $existingMatches = false;
    $match = [];
    
    if (count($users) > 0) {
        $existingMatches = true;
        $filteredUsers = getUsersInBoundingBox($users);
    
        if (empty($_SESSION['previous'])) {
            $previous = [];
            $match = getMatch($filteredUsers);
            array_push($previous, $match);
            if (isset($_SESSION['radius'])) {
                $_SESSION['previous'] = $filteredUsers;
            } else {
                $_SESSION['previous'] = $previous;
            }
        } else {
            $match = getMatch($filteredUsers);
        }

        $matchPhoto = $match['photo'];
    }
    ?>
        <div class="body">
            <?php if (file_exists("uploads/$photo")):
                if ($existingMatches):
            ?>
            <h1>Would you eat this?</h1>
            <h4><?= $match['name'] ?></h4>
            <p><img class="matchImage" src="<?="uploads/$matchPhoto"?>"></p>
            <div>
                <form method="post" action="<?= toHTMLEntities("changeMatch.php"); ?>">
                    <span class="circle nope"><input type="image" name="nope" src="images/nope.png" class="tinderButton nope" value="<?= $match['user'] ?>"></span>
                
                    <span class="info"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne"><img class="infoButton" src="images/info.png"></a></span>

                    <span class="circle like"><input type="image" name="like" value="<?= $match['user'] ?>" src="images/like.png" class="tinderButton like"></span>
                </form>
                
                <!-- accordion-->
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                        <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                            <div class="panel-body">
                                <p><?=$match['text']?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php else:?>
            <h2>Already seen everyone!</h2>
            <?php endif;?>
            <?php else:?>
            <h2>Upload a profile photo to continue</h2>
            <?php endif;?>
        <!-- Set text modal -->
        <div id="textModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Write something about yourself</h4>
                    </div>
                    <div class="modal-body">
                        <!-- siia ka turvavärk -->
                        <form method="post" action="<?=toHTMLEntities("updateText.php")?>">
                            <textarea rows="4" cols="30" name="textArea"></textarea>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
        
        <!-- Upload photo modal -->
        <div id="upload" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Upload photo</h4>
                    </div>
                    <div class="modal-body">
                        <form action="<?=toHTMLEntities("upload.php")?>" method="post" enctype="multipart/form-data" style="text-align: center;">
                            Select image to upload:
                            <input type="file" name="fileToUpload" id="fileToUpload" style="margin-left: 32px;">
                            <input type="submit" value="Upload Image" name="submit" class="btn btn-primary uploadButton">
                        </form>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
            
        <!-- Matches modal -->
        <div id="matches" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Chat with matches</h4>
                    </div>
                    <div class="modal-body">
                        <form action="<?=toHTMLEntities("chat.php")?>" method="post" style="text-align: center;">
                            <?php echo getChat($id, $gender, $_SESSION['user'], $match); ?>
                        </form>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
            
        <!-- Radius modal -->
        <div id="radius" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Insert search radius</h4>
                    </div>
                    <div class="modal-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                            Radius: 
                            <input type="number" name="radius" min="0" max="1000" step="25" value="<?=$radius?>"> km
                            <br><br>
                            <button type="submit" id="log" class="btn btn-primary" autocomplete="off">Submit</button>
                            <form method="post" action="<?php toHTMLEntities("reset.php")?>">
                                <input type="hidden" name="match" value="<?= $match ?>">
                                <button type="submit" class="btn btn-primary" style="">
                                    Reset radius
                                </button>
                            </form>
                        </form>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
        
    <?php else: ?>
        <div class="body">
            <h2>It starts here</h2>
            <h4>Chocolates, chocolates, chocolates and everything in between.</h4>
            <img src="images/chocolate.jpg" class="chocolateImg">
        </div>
        <!-- Registration modal -->
        <div id="registration" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Register</h4>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                            <input id="registerUser" type="text" placeholder="Username" class="form-control input" name="username" autocomplete="off">
                            <input id="registerPass" type="password" placeholder="Password" class="form-control input" name="password" autocomplete="off">
                            <input id="registerName" type="text" placeholder="Name" class="form-control input" name="name" autocomplete="off">
                            <input id="registerEmail" type="email" placeholder="Email" class="form-control input" name="email" autocomplete="off">
                            <input type="radio" value="M" id="male" name="sex">Male
                            <input type="radio" value="F" id="female" name="sex">Female
                            <br><br>
                            <button type="submit" id="register" class="btn btn-primary">Register</button>
                        </form>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>

        <!-- Login modal -->
        <div id="login" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Log In</h4>
                    </div>
                    <div class="modal-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                            <input id="loginUser" type="text" placeholder="Username" class="form-control input" name="user" autofocus="autofocus" autocomplete="off">
                            <input id="loginPass" type="password" placeholder="Password" class="form-control input" name="pass">
                            <button type="submit" id="log" class="btn btn-primary" autocomplete="off">Submit</button>
                        </form>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <script src="scripts/app.js"></script>
</body>

</html>