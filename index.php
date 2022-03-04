<!DOCTYPE html>
<html>
<head>
<?php
include "inc.php";
if (isset($_GET["url"]) && ($_GET["url"] != "")) {

if (!url_exists($_GET["url"])) {
  die("404. Feed not found");
}

/* Check if URL contents a text */
file_put_contents(".env", file_get_contents($_GET["url"]));
$mime = mime_content_type(".env");
unlink(".env");
if ($mime != "text/plain") {
  die("Invalid document. It isn't a text".$mime);
}


// á[ ]á => á\[\s\]á
// Feed parser
$feed = file_get_contents($_GET["url"]);
$feed = explode("\n", $feed);
$dates = [];
$posts = [];
foreach($feed as $line) {
  if (str_starts_with($line, "#")) {
    // Link pattern
    $link_pattern = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";

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
      if (strtolower(parse_url($_GET["url"])["host"]) != strtolower($user["host"])) {
        die("sus feed");
      }
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
      $description = preg_replace("/\t/", " ", $description);
      $description = explode(" ", $description);
      foreach($description as $num => $section) {
        if ($section == "") {
          unset($description[$num]);
        }
        if (preg_match($link_pattern, $section)) {
          $description[$num] = preg_replace($link_pattern, "<a href='$1'>$1</a>", $section);
        } else {
          $description[$num] = htmlentities($section);
        }
      }
      $description = implode(" ", $description);
    }
  } elseif($line == "") {
  } else {
    $entry = explode("\t", $line, 2);
    array_push($dates, $entry[0]);
    $entry[1] = preg_replace("/\t/", " ", $entry[1]);
    $entry[1] = explode(" ", $entry[1]);
    foreach($entry[1] as $num => $section) {
      if ($section == "") {
        unset($entry[1][$num]);
      }
      if (preg_match($link_pattern, $section)) {
        $entry[1][$num] = preg_replace($link_pattern, "<a href='$1'>$1</a>", $section);
      } else {
        $entry[1][$num] = htmlentities($section);
      }
    }
    $entry[1] = implode(" ", $entry[1]);
    array_push($posts, $entry[1]);
    //print_r($dates);
  }
}

if ($user == "") {
  $user = $_GET["url"];
}

// Checkmark
$check = explode("\n", file_get_contents("https://raw.githubusercontent.com/luqaska/twtxt-verified/main/list.txt"));
$checkmark = "";
foreach($check as $u) {
  if (($u == $user)) {
    $checkmark = '<span style="margin-left:5px" title="Verified by devs">☑️</span>';
  }
} ?>
<title><?= $user ?> | twtxtExplorer</title>
<style>
body {
  text-align:center;
}
a#button {
  padding:1px;
  background:black;
  color:white;
  font-decoration:none;
}
form input[type=url] {
  width:50%;
}
form input[type=submit] {
  width:50px;
}
.post {
  border:solid 1px black;
  margin:5px 0;
  padding:8px;
  text-align:initial;
}
.top {
  font-size:small;
  font-family:sans-serif;
}
.content {
  font-size:medium;
}
</style>
</head>
<body>
<div><form method="GET">
  <a href="?" id="button" title="twtxtExplorer v1.2.1">twtxtE</a>
  <input type="url" name="url" placeholder="URL" value="<?= $_GET["url"] ?>">
  <input type="submit" value="Go!">
</form></div>
<div id="header" style="text-align:center">
  <div style="display:flex;align-items:center;justify-content:center">
    <?php if (isset($avatar)) { ?>
    <div><a href="<?= $avatar ?>" target="_blank"><img style="width:50px;height:50px;margin-right:10px;border:3px solid black;border-radius:100%" src="<?= $avatar ?>"></a></div>
    <?php } ?>
    <h2><?= $user ?><?= $checkmark ?></h2>
  </div>
  <p><?php if (isset($description)) {
    echo $description . " ";
  } ?><a href="<?= $_GET["url"] ?>"><button>Follow</button></a></p>
  <?php if (isset($followers) && isset($following)) { ?>
  <div><b><?= $followers ?></b> followers - <b><?= $following ?></b> following</div>
  <?php } ?>
</div>
<div style="margin:10px 25%">
<?php
$dates = array_reverse($dates);
$posts = array_reverse($posts);
foreach($posts as $num => $post) {
  $post = htmlentities($post);
  echo '<div class="post"><div class="top">' . date(DATE_RSS, strtotime($dates[$num])) . '</div><div class="content">' . $post . '</div></div>';
} ?>
</div>
</div>
<div style="margin-top:10px">&copy;2022 Luqaska<br><a href="https://github.com/luqaska/twtxtExplorer">GitHub</a></div>
<?php } else { ?>
<title>twtxtExplorer</title>
</head>
<body>
<h1 style="font-weight:normal"><b>twtxtE</b>xplorer</h1>
<div><form method="GET"><input type="url" name="url" placeholder="URL"><input type="submit"></form></div>
<p>&copy;2022 <a href="https://lucas.koyu.space">Luqaska</a> - <a href="https://github.com/luqaska/twtxtExplorer">GitHub</a></p>
<?php } ?>
</body>
</html>
