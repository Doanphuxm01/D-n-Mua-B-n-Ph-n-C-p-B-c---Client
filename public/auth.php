<?php
/**
 * Created by PhpStorm.
 * User: sakura
 * Date: 3/13/18
 * Time: 9:35 AM
 */

$valid_passwords = array ("sakura" => "blabla");
$valid_users = array_keys($valid_passwords);

$user = @$_SERVER['PHP_AUTH_USER'];
$pass = @$_SERVER['PHP_AUTH_PW'];

$validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);

if (!$validated) {
    header('WWW-Authenticate: Basic realm="Sakura and Nobita"');
    header('HTTP/1.0 401 1Unauthorized');
    die ("Not authorized");
}