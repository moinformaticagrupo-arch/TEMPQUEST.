<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/conexion.php';
try {
  // La simulación varía en el servidor cada cinco segundos y no modifica sensores.
  $conexion->exec("UPDATE public.simulacion_estado SET temperatura=GREATEST(15,LEAST(45,temp_base+(random()-0.5)*2)), humedad=GREATEST(20,LEAST(90,hum_base+(random()-0.5)*4)), cpu=GREATEST(5,LEAST(100,ROUND(cpu_base+(random()-0.5)*8)::smallint)), actualizado=NOW(), ultimo_tick=NOW() WHERE id=1 AND activa=TRUE AND EXTRACT(EPOCH FROM (NOW()-ultimo_tick))>=5");
  $sim=$conexion->query("SELECT activa,escenario,temperatura,humedad,cpu,nodo_offline,actualizado FROM public.simulacion_estado WHERE id=1")->fetch();
  $active=$sim && in_array($sim['activa'],[true,1,'1','t','true'],true);
  if($active) {
    echo json_encode([['id'=>'SIM','temperatura'=>$sim['temperatura'],'humedad'=>$sim['humedad'],'fecha'=>$sim['actualizado'],'modo'=>'SIMULADO','escenario'=>$sim['escenario'],'cpu'=>$sim['cpu'],'nodo_offline'=>in_array($sim['nodo_offline'],[true,1,'1','t','true'],true)?1:0]],JSON_UNESCAPED_UNICODE); exit;
  }
  $q=$conexion->query("SELECT id,temperatura,humedad,fecha FROM public.sensores ORDER BY fecha DESC LIMIT 20");
  echo json_encode($q->fetchAll(),JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) { http_response_code(500); echo json_encode(['error'=>'Error consultando Supabase. Revisa el esquema y las variables de Render.']); }
?>