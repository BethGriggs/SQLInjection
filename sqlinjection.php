<html>
<form>
    Username:
    <input type="text" name="username" size="15" />
    <br /> Password:
    <input type="password" name="password" size="15" />
    <br />
    <p>
        <input type="submit" value="Login" />
    </p>
</form>

<h1>Challenge</h1>
<p>Your challenge is to find out the total number of users by injecting SQL into the 'Username' field above.</p>

<?php

## Default MySQL
mysql_connect("localhost", "root", "");


## Database/Table Setup
mysql_query("CREATE DATABASE IF NOT EXISTS demo;");            
mysql_select_db("demo");

mysql_query("CREATE TABLE IF NOT EXISTS `users` (
  `username` varchar(32) NOT NULL default '',
  `password` varchar(32) default NULL,
  PRIMARY KEY  (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
mysql_query("INSERT INTO `users` VALUES ('Joe', 'Bloggs')");
mysql_query("INSERT INTO `users` VALUES ('Fred', 'Bloggs')");
mysql_query("INSERT INTO `users` VALUES ('Admin', 'User');");

## Set GBK Character Set
mysql_query("SET NAMES GBK");

#$_GET['username'] = chr(0xbf).chr(0x27).' OR username = username /*';

## addslashes() example

echo "<h2>1. addslashes() </h2>";
$username = addslashes($_GET['username']);
$password = addslashes($_GET['password']);
$sql = "SELECT * FROM  users WHERE  username = '$username' AND password = '$password'";
$result = mysql_query($sql) or trigger_error(mysql_error().$sql);

echo("Username:  ". $username ."</br>");
echo("Number of Rows: ". mysql_num_rows($result). "</br>");
echo("Client Encoding: ". mysql_client_encoding(). "</br>");

## mysql_real_escape_string() 
echo "<h2>2. mysql_real_escape_string()</h2>";
$username = mysql_real_escape_string($_GET['username']);
$password = mysql_real_escape_string($_GET['password']);
$sql = "SELECT * FROM  users WHERE  username = '$username' AND password = '$password'";
$result = mysql_query($sql) or trigger_error(mysql_error().$sql);

echo ("Username: ".$username. "</br>");
echo ("Number of Rows: " .mysql_num_rows($result)."</br>");
echo ("Client Encoding: " .mysql_client_encoding(). "</br>");

echo "</br></br>PHP version: ".PHP_VERSION."</br>";

?>

</html>