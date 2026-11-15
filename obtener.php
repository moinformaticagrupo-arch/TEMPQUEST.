<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/conexion.php';
try {
  // La fila simulada vive aparte; la tabla mediciones conserva las lecturas importadas/reales.
  $conexion->exec("UPDATE public.simulacion_estado SET temperatura=GREATEST(15,LEAST(45,temp_base+(random()-0.5)*2)), humedad=GREATEST(20,LEAST(90,hum_base+(random()-0.5)*4)), cpu=GREATEST(5,LEAST(100,ROUND(cpu_base+(random()-0.5)*8)::smallint)), actualizado=NOW(), ultimo_tick=NOW() WHERE id=1 AND activa=TRUE AND EXTRACT(EPOCH FROM (NOW()-ultimo_tick))>=5");
  $sim=$conexion->query("SELECT activa,escenario,temperatura,humedad,cpu,nodo_offline,actualizado FROM public.simulacion_estado WHERE id=1")->fetch();
  $active=$sim && in_array($sim['activa'],[true,1,'1','t','true'],true);
  if($active) {
    echo json_encode([['id'=>'SIM','temperatura'=>$sim['temperatura'],'humedad'=>$sim['humedad'],'fecha'=>$sim['actualizado'],'modo'=>'SIMULADO','escenario'=>$sim['escenario'],'cpu'=>$sim['cpu'],'nodo_offline'=>in_array($sim['nodo_offline'],[true,1,'1','t','true'],true)?1:0]],JSON_UNESCAPED_UNICODE); exit;
  }
  $q=$conexion->query("SELECT m.id,m.temperatura,m.humedad,m.presion,m.id_dispositivo,m.fecha_hora AS fecha,d.nombre AS dispositivo FROM public.mediciones m JOIN public.dispositivos d ON d.id=m.id_dispositivo ORDER BY m.fecha_hora DESC LIMIT 20");
  echo json_encode($q->fetchAll(),JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
  http_response_code(500);
  echo json_encode(['error'=>'No se pudieron obtener mediciones. Revisa las tablas public.mediciones y public.dispositivos.']);
}
?>
