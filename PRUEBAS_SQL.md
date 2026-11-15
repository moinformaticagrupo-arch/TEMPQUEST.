# Pruebas manuales desde Supabase SQL Editor

La página consulta el servidor PHP cada 2 segundos. Por eso un cambio confirmado en Supabase aparece normalmente dentro de ese intervalo más el tiempo de red.

La tabla usada como override de laboratorio es public.simulacion_estado. Mientras activa=true, el dashboard muestra esa fila simulada y no la tabla sensores. Para probar una temperatura crítica manual:

```sql
UPDATE public.simulacion_estado
SET activa = TRUE,
    escenario = 'manual',
    temperatura = 36,
    temp_base = 36,
    humedad = 48,
    hum_base = 48,
    cpu = 38,
    cpu_base = 38,
    nodo_offline = FALSE,
    actualizado = NOW(),
    ultimo_tick = NOW()
WHERE id = 1;
```

Para probar nodo fuera de línea:

```sql
UPDATE public.simulacion_estado
SET activa = TRUE, escenario = 'nodo', nodo_offline = TRUE,
    actualizado = NOW(), ultimo_tick = NOW()
WHERE id = 1;
```

Para volver a lecturas reales:

```sql
UPDATE public.simulacion_estado
SET activa = FALSE, escenario = 'ninguno', nodo_offline = FALSE,
    actualizado = NOW(), ultimo_tick = NOW()
WHERE id = 1;
```

No hace falta habilitar acceso público a la base ni guardar la contraseña en el navegador. Render usa las credenciales privadas y la página lee los datos a través de obtener.php.
