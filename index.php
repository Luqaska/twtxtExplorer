<?php
include "inc.php";
if (isset($_GET["url"]) || $_GET["url"] != ""){
if (!url_exists($_GET["url"])) {
  die("404");
}


// á[ ]á => á\[\s\]á
$feed = file_get_contents($_GET["url"]);
$feed = explode("\n", $feed);
$dates = [];
$posts = [];
//$i = 0;
foreach($feed as $line) {
  if (str_starts_with($line, "#")) {
    //$line = preg_replace("/\s/", "", $line);
    $line = preg_replace("/\s+/", "á[ ]á", $line);
    $line = preg_replace("/\t+/", "á[ ]á", $line);


    /* Nick */
    if (str_starts_with($line, "#á[ ]ánická[ ]á=")) {
      $nick = preg_replace("/#á\[\s\]ánická\[\s\]á=á\[\s\]á/", "", $line);
    }

    if (str_starts_with($line, "#á[ ]áurlá[ ]á=")) {
      $url = preg_replace("/#á\[\s\]áurlá\[\s\]á=á\[\s\]á/", "", $line);
    }

    if (isset($nick) && isset($url)) {
      $user = parse_url($url);
      $user = "@" . $nick . "@" . $user["host"];
    }


    /* Avatar */
    if (str_starts_with($line, "#á[ ]áavatará[ ]á=")) {
      $avatar = preg_replace("/\#á\[\s\]áavatará\[\s\]á=á\[\s\]á/", "", $line);
      $avatar = preg_replace("/\#á\[\s\]áavatar=/", "", $avatar);
      $avatar = preg_replace("/\#avatar=/", "", $avatar);
    }

    /* Followers count */
    if (str_starts_with($line, "#á[ ]áfollowersá[ ]á=")) {
      $followers = preg_replace("/\#á\[\s\]áfollowersá\[\s\]á=á\[\s\]á/", "", $line);
      $followers = preg_replace("/\#á\[\s\]áfollowers=/", "", $followers);
      $followers = preg_replace("/\#followers=/", "", $followers);
    }

    /* Following count */
    if (str_starts_with($line, "#á[ ]áfollowingá[ ]á=")) {
      $following = preg_replace("/\#á\[\s\]áfollowingá\[\s\]á=á\[\s\]á/", "", $line);
      $following = preg_replace("/\#á\[\s\]áfollowing=/", "", $following);
      $following = preg_replace("/\#following=/", "", $following);
    }

    /* Avatar */
    if (str_starts_with($line, "#á[ ]áavatará[ ]á=")) {
      $avatar = preg_replace("/\#á\[\s\]áavatará\[\s\]á=á\[\s\]á/", "", $line);
      $avatar = preg_replace("/\#á\[\s\]áavatar=/", "", $avatar);
      $avatar = preg_replace("/\#avatar=/", "", $avatar);
    }


    /* Description */
    if (str_starts_with($line, "#á[ ]ádescriptioná[ ]á=")) {
      $description = preg_replace("/#á\[\s\]ádescriptioná\[\s\]á=/", "", $line);
      $description = preg_replace("/á\[\s\]á/", " ", $description);
      $description = preg_replace("/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/", "<a href='$1'>$1</a>", $description);
    }
  } elseif($line == "") {
  } else {
    $entry = explode("\t", $line, 2);
    array_push($dates, $entry[0]);
    array_push($posts, $entry[1]);
    print_r($dates);
  }
} ?>
<style>
form button{width:5%;background:black;color:white;}
form input[type=url]{width:90%;}
form input[type=submit]{width:5%;}
.post{border:solid 1px black;margin:5px 0;}
.top{font-size:small;font-family:sans-serif;}
.content{font-size:medium;padding:3px;}
</style>
<div><form method="GET"><button title="twtxtExplorer v1.0.0">twtxtE</button><input type="url" name="url" value="<?= $_GET["url"] ?>"><input type="submit" value="Go!"></form></div>
<div id="header" style="width:100%;text-align:center">
  <div style="display:flex;align-items:center;justify-content:center">
    <?php if (isset($avatar)) { ?>
    <div><a href="<?= $avatar ?>" target="_blank"><img style="width:50px;margin-right:10px;border:3px solid black;border-radius:100%" src="<?= $avatar ?>"></a></div>
    <?php } ?>
    <h2><?= $user ?></h2>
  </div>
  <?php if (isset($description)) {
    echo "<p>" . htmlentities($description) . "</p>";
  }
  if (isset($followers) && isset($following)) { ?>
  <div><b><?= $followers ?></b> followers - <b><?= $following ?></b> following</div>
  <?php } ?>
</div>
<div style="margin:10px 25%">
<?php $i=0; foreach($posts as $post) {
    $post = htmlentities($post);
    //$post = preg_replace("/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/", "<a href='$1'>$1</a>", $post);
  echo '<div class="post"><div class="top">' . date(DATE_RSS, strtotime($dates[$i])) . '</div><div class="content">' . $post . '</div></div>';
  $i++;
} ?>
</div>
</div>
<?php } else { ?>
<h1 style="font-weight:normal"><b>twtxtE</b>xplorer</h1>
<div><form method="GET"><input type="url" name="url"><input type="submit"></form></div>
<?php } ?>
