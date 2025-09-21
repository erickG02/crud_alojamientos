<?php
$host = 'localhost';
$port = '3306'; 
$dbname = 'crud_alojamientos';
$user = 'root';    
$password = ''; 

try {
  
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8"; 
    $pdo = new PDO($dsn, $user, $password); 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
   
} catch (PDOException $e) {
    echo "Error de conexión a la base de datos: " . $e->getMessage();
    exit(); 
}
?>