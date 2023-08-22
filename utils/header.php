<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>
    <link rel="stylesheet" href="../styles/index.css">
</head>
<body>
<?php

session_start();

if(!isset($_SESSION["user_id"])){
    header("Location: ../login/view.php");
}


?>
    
</body>
</html>