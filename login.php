<?php
$host = 'localhost'; 
$dbname = 'final'; 
$user = 'root'; 
$pass = 'mysql';
$charset = 'utf8mb4';

// comment for new repository commit

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = 'SELECT * FROM users WHERE username = :username';
    $stmnt = $pdo->prepare($sql);
    $stmnt->execute(['username' => $username]);
    $user = $stmnt->fetch();

    if($user && pasword_verify($password, $user['PasswordHash'])) {
        $_SESSION['user_id'];
        $_SESSION['username'] = $user['Username'];
        $_SESSION['email'] = $user['Email'];
        header("Location: shops.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
    }

?>



<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale = 1.0">
    <link rel="stylesheet" href = "styles2.css">
</head>
<body>
    <div class='nav-bar'>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['Username']); ?>!</p>
    <p>Email <?php echo htmlspecialchars($_SESSION['Email']); ?>!</p>
    <a href ="logout.php">Logout</a>
    <!--comment test-->
    <!--comment test2-->
</div>
<body>
</head>






</html>
