<?php
declare(strict_types=1);
$host=getenv('DB_HOST') ?: ''; $port=getenv('DB_PORT') ?: '5432';
$name=getenv('DB_NAME') ?: 'postgres'; $user=getenv('DB_USER') ?: '';
$pass=getenv('DB_PASSWORD') ?: ''; $sslmode=getenv('DB_SSLMODE') ?: 'require';
if ($host==='' || $user==='') { http_response_code(500); header('Content-Type: application/json; charset=UTF-8'); echo json_encode(['error'=>'Faltan variables DB_HOST o DB_USER en Render.']); exit; }
try {
  $conexion=new PDO("pgsql:host=$host;port=$port;dbname=$name;sslmode=$sslmode",$user,$pass,[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES=>false
  ]);
} catch(PDOException $e) {
  http_response_code(500); header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(['error'=>'No se pudo conectar a Supabase. Revisa las variables privadas DB_* de Render.']); exit;
}
?>