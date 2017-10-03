<?php
include("session.php");
include("db.php");
include("entities.php");
include("displayMatches.php");

if (empty($_SESSION)) {
    session_start();
}

$match = "";
$matchName = "";
$matchID = 0;
$userid = $user_from_db['id'];
$chat = "";
$radius;

if (isset($_SESSION['radius'])) {
    $radius = $_SESSION['radius'];
} else {
    $radius = 0;
}

if (isset($_POST['match'])) {
    $match = mysqli_real_escape_string($connection, toHTMLEntities($_POST['match']));
    $matchID = getMatchID($match);
}

if (isset($_POST['text']) && $_POST['text'] !== "") {
    $text = mysqli_real_escape_string($connection, toHTMLEntities($_POST['text']));
    $name = $user_from_db['name'];
    
    $matchID = getMatchID($match);
    
    $sql = "insert into chat (name, user1, user2, message) values ('$name', $userid, $matchID, '$text');";
    
    if (mysqli_query($connection, $sql)) {
        $chat = displayChat($userid, $matchID);
    } else {
        echo "Error inserting entry";
    }
}

$matchID = getMatchID($match);
$chat = displayChat($userid, $matchID);

function displayChat($userid, $matchID) {
    include("db.php");
    $sql = "select * from chat where (user1='$userid' or user1='$matchID') and (user2='$userid' or user2='$matchID') order by id";
    $result = mysqli_query($connection, $sql);
    
    $ret = "";
    while ($row = mysqli_fetch_assoc($result)) {
        $message = $row['message'];
        $name = $row['name'];
        $text = $row['message'];

        $ret = $ret . "<p><b>$name says:</b> $text</p>";
    }
    
    return $ret;
}

function getMatchID($match) {
    include("db.php");
    $sql = "select id from users where user='$match'";
    if ($query = mysqli_query($connection, $sql)) {
        $row = mysqli_fetch_assoc($query);
        $matchID = $row['id'];
        return $matchID;
    } else {
        return "this failed";
    }
}
?>

<!doctype html>

<html>

<head>
    <meta charset="utf-8;" name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Chat</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="chat.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</head>

<body>
    <header>
        <div class="headerLogo">
            <a href="index.php" id="logo"><img border="0" alt="CinderLogo" src="images/Tinder_logo.png" height="40px"></a>
            <form method="post" class="logOutForm" action="<?php echo htmlspecialchars($_SERVER[" PHP_SELF "]);?>">
                <div class="dropdown pull-right">
                    <button id="username" class="btn btn-success headerButton dropdown-toggle" type="button" id="dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <?= $user_from_db['name'] ?>
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
    </header>
    <h1 class="body">Chat</h1>
    
    <div id="chat-window" onload="scrollBottom()">
        <?= $chat ?>
    </div>
    
    <form method="post" class="form-inline" role="form">
        <div class="form-group">
            <input type="text" name="text" maxlength="255" autocomplete="off">
        </div>
        <div class="form-group">
            <input type="hidden" name="match" value="<?= $match ?>">
        </div>
        <button type="submit" class="btn btn-primary">
            Send
        </button>
        <form method="post">
            <input type="hidden" name="match" value="<?= $match ?>">
            <button type="submit" class="btn btn-primary" style="">
                Refresh chat
            </button>
        </form>
    </form>

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
                        <p><?php echo getChat($user_from_db['id'], $user_from_db['sex'], $user_from_db['user']); ?></p>
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
                        <form method="post">
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
    <script src="scripts/app.js"></script>
    <script>
        setTimeout(function() {
           var chat = <?php displayChat($userid, $matchID); ?>;
           $('#chat-window').html(chat);
        }, 500);
    </script>
</body>

</html>