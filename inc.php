<?php
// https://stackoverflow.com/questions/2280394/how-can-i-check-if-a-url-exists-via-php#2280413
function url_exists($url) {
  if (!$fp = curl_init($url)) return false;
  return true;
}


/* vurltool 1.0 */
$vrl = $_SERVER["REQUEST_URI"];
$vrl = preg_replace("/#(.*)$/", "", $vrl);
$vrl = preg_replace("/\?(.*)$/", "", $vrl);
$v = explode("/", $vrl);
unset($v[0]);
$vi = implode("/", $v);
$v = explode("/", $vi);
