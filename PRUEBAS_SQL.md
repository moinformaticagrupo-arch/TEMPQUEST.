# Pruebas desde Supabase SQL Editor

El dashboard consulta obtener.php cada 2 segundos. En modo normal lee la medición más reciente de public.mediciones con el nombre de public.dispositivos. Con la simulación activa, el estado de public.simulacion_estado reemplaza la vista sin modificar las mediciones.

## Probar un valor de sensor y la alerta

Primero asegurate de que la simulación esté apagada. Luego editá la última lectura:

UPDATE public.simulacion_estado
SET activa=FALSE, escenario='ninguno', nodo_offline=FALSE, actualizado=NOW(), ultimo_tick=NOW()
WHERE id=1;

UPDATE public.mediciones
SET temperatura=36.50
WHERE id=(SELECT id FROM public.mediciones ORDER BY fecha_hora DESC LIMIT 1);

El dashboard debe recibir el cambio en el siguiente sondeo y mostrar alerta térmica. Para devolver la lectura importada #113 al valor original del dump, usá:

UPDATE public.mediciones SET temperatura=36.54 WHERE id=113;

## Simular temperatura sin tocar lecturas reales

UPDATE public.simulacion_estado
SET activa=TRUE, escenario='manual',
    temperatura=36, temp_base=36,
    humedad=48, hum_base=48,
    cpu=38, cpu_base=38,
    nodo_offline=FALSE, actualizado=NOW(), ultimo_tick=NOW()
WHERE id=1;

El servidor hará pequeñas variaciones alrededor del valor base cada cinco segundos mientras el dashboard consulta.

## Simular nodo desconectado

UPDATE public.simulacion_estado
SET activa=TRUE, escenario='nodo', nodo_offline=TRUE,
    actualizado=NOW(), ultimo_tick=NOW()
WHERE id=1;

## Volver a las lecturas reales

UPDATE public.simulacion_estado
SET activa=FALSE, escenario='ninguno', nodo_offline=FALSE,
    actualizado=NOW(), ultimo_tick=NOW()
WHERE id=1;

No edites la fila de medición real para simular un nodo caído. La simulación está aislada en su propia tabla.

## Guardar manualmente 80 °C desde SQL Editor

La página de control ahora acepta 80 °C. También podés establecerlo desde Supabase así; los valores base hacen que la simulación fluctúe levemente alrededor de 80 °C:

UPDATE public.simulacion_estado SET activa=TRUE, escenario='manual', temperatura=80, temp_base=80, humedad=48, hum_base=48, cpu=38, cpu_base=38, actualizado=NOW(), ultimo_tick=NOW() WHERE id=1;
