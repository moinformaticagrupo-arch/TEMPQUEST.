<?php
header('Content-Type: application/json; charset=UTF-8');
try { require_once __DIR__.'/conexion.php'; $conexion->query('SELECT 1'); http_response_code(200); echo json_encode(['ok'=>true]); }
catch(Throwable $e) { http_response_code(503); echo json_encode(['ok'=>false]); }
?>