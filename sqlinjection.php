<html>
<h1>Challenge</h1>
<p>Your challenge is to find out the total number of users by injecting SQL into the 'Username' field below.</p>

<form>
    <label for="username">Username:</label>
    <input type="text" name="username" size="25" placeholder="inject your SQL here!" />
    <input type="submit" value="Submit" />
</form>

<?php

$host   = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "demo";

try {
    
    // MySQL setup
    $pdo  = new PDO("mysql:host=$host", $dbUser, $dbPass);
    $stmt = $pdo->prepare("CREATE DATABASE IF NOT EXISTS $dbName");
    $stmt->execute();
    $pdo = new PDO("mysql:host=$host;dbname=$dbName", $dbUser, $dbPass);
    
    
    $userTable = "CREATE TABLE IF NOT EXISTS `users` (
 `username` varchar(32) NOT NULL default '',
 `password` varchar(32) default NULL,
 PRIMARY KEY  (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
    
    
    $pdo->exec($userTable);
    $q        = $pdo->query("SELECT * FROM users;");
    $rowCount = $q->rowCount();
    
    if ($rowCount == 0) {
        $pdo->exec("INSERT INTO `users` VALUES ('Joe', 'Bloggs')");
        $pdo->exec("INSERT INTO `users` VALUES ('Fred', 'Bloggs')");
        $pdo->exec("INSERT INTO `users` VALUES ('Admin', 'User');");
    }
}
catch (PDOException $e) {
    echo $e->getMessage();
}

// Set GBK character set
$pdo->query('SET NAMES gbk');

// PDO prepared statement
$stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
$stmt->execute(array(
    $_GET['username']
));

$result = $stmt->fetchAll();
echo "Result: " . json_encode($result);

$q        = $pdo->query("SELECT * FROM users;");
$qResult = $q->fetchAll();

if ($result == $qResult){
    echo "<h1 style='color: green;'>Challenge Completed!</h1>";
}

echo "</br></br><p>PHP version: " . PHP_VERSION . "</p></br>";
?>

</html>
