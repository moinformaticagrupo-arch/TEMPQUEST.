<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';
function reply(array $data,int $status=200): never { http_response_code($status); echo json_encode($data,JSON_UNESCAPED_UNICODE); exit; }
try {
  $method=$_SERVER['REQUEST_METHOD'] ?? 'GET';
  if($method==='POST') {
    $expected=getenv('CONTROL_TOKEN') ?: ''; $token=$_SERVER['HTTP_X_CONTROL_TOKEN'] ?? '';
    if($expected==='' || !hash_equals($expected,$token)) reply(['ok'=>false,'error'=>'Clave incorrecta o no configurada.'],401);
    $in=json_decode(file_get_contents('php://input'),true);
    if(!is_array($in)) reply(['ok'=>false,'error'=>'JSON inválido.'],400);
    $action=$in['accion'] ?? '';
    if($action==='restablecer') {
      $conexion->exec("UPDATE public.simulacion_estado SET activa=FALSE, escenario='ninguno', temperatura=22, humedad=48, cpu=38, temp_base=22, hum_base=48, cpu_base=38, nodo_offline=FALSE, actualizado=NOW(), ultimo_tick=NOW() WHERE id=1");
    } elseif($action==='escenario') {
      $scenario=$in['escenario'] ?? ''; $presets=['temperatura'=>[38,48,38,false],'nodo'=>[22,48,38,true],'cpu'=>[22,48,96,false]];
      if(!isset($presets[$scenario])) reply(['ok'=>false,'error'=>'Escenario desconocido.'],400);
      [$t,$h,$c,$off]=$presets[$scenario];
      $q=$conexion->prepare("UPDATE public.simulacion_estado SET activa=TRUE, escenario=?, temperatura=?, humedad=?, cpu=?, temp_base=?, hum_base=?, cpu_base=?, nodo_offline=?, actualizado=NOW(), ultimo_tick=NOW() WHERE id=1");
      $q->execute([$scenario,$t,$h,$c,$t,$h,$c,$off ? 'true' : 'false']);
    } elseif($action==='personalizado') {
      $t=filter_var($in['temperatura']??null,FILTER_VALIDATE_INT); $h=filter_var($in['humedad']??null,FILTER_VALIDATE_INT); $c=filter_var($in['cpu']??null,FILTER_VALIDATE_INT);
      if($t===false||$t<15||$t>80||$h===false||$h<20||$h>90||$c===false||$c<5||$c>100) reply(['ok'=>false,'error'=>'Valores fuera de rango.'],400);
      $q=$conexion->prepare("UPDATE public.simulacion_estado SET activa=TRUE, escenario='personalizado', temperatura=?, humedad=?, cpu=?, temp_base=?, hum_base=?, cpu_base=?, nodo_offline=FALSE, actualizado=NOW(), ultimo_tick=NOW() WHERE id=1");
      $q->execute([$t,$h,$c,$t,$h,$c]);
    } else reply(['ok'=>false,'error'=>'Acción desconocida.'],400);
  } elseif($method!=='GET') reply(['ok'=>false,'error'=>'Método no permitido.'],405);
  $r=$conexion->query("SELECT activa,escenario,temperatura,humedad,cpu,nodo_offline,actualizado FROM public.simulacion_estado WHERE id=1")->fetch();
  if(!$r) reply(['ok'=>false,'error'=>'Ejecuta schema_postgres.sql en Supabase.'],500);
  $active=in_array($r['activa'],[true,1,'1','t','true'],true); $offline=in_array($r['nodo_offline'],[true,1,'1','t','true'],true);
  reply(['ok'=>true,'active'=>$active,'escenario'=>$r['escenario'],'temperatura'=>(float)$r['temperatura'],'humedad'=>(float)$r['humedad'],'cpu'=>(int)$r['cpu'],'nodo_offline'=>$offline?1:0,'actualizado'=>$r['actualizado']]);
} catch(Throwable $e) { reply(['ok'=>false,'error'=>'Error del servidor procesando la simulación.'],500); }
?>
