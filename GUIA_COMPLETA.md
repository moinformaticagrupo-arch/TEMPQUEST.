# NEXUS NOC · GitHub + Render + Supabase

## Qué contiene
- Dashboard web con estado real/simulado, actualización cada 2 segundos y alertas visibles.
- Control móvil con escenarios y valores personalizados.
- PHP API y conexión PDO PostgreSQL.
- Esquema Supabase y Dockerfile para que Render ejecute PHP.
- Guías de publicación y pruebas manuales SQL.

## Pasos resumidos
1. Ejecuta schema_postgres.sql en Supabase SQL Editor.
2. Sube el contenido de esta carpeta a un repositorio GitHub. No subas secretos.
3. En Render crea un Web Service/Blueprint desde el repo. El runtime usa el Dockerfile.
4. En Render configura DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD, DB_SSLMODE=require y CONTROL_TOKEN como variables privadas. Copia los datos de conexión PostgreSQL desde Supabase; usa el pooler si la conexión directa no funciona desde Render.
5. Espera el deploy y abre /health.php (debe responder ok:true), / para el monitor y /control.html en el teléfono.
6. En el control ingresá CONTROL_TOKEN. Para probar alertas, activa un escenario de temperatura, nodo o CPU.
7. En el celular, abre el dashboard y toca “Activar alertas en este celular”.

La guía detallada está en GUIA_RENDER_SUPABASE.md. Pruebas SQL manuales: PRUEBAS_SQL.md. Alcance de alertas: GUIA_ALERTAS.md.

## Datos de sensores que ya existen
El endpoint espera public.sensores(id,temperatura,humedad,fecha). schema_postgres.sql crea esa tabla si no está, pero no cambia automáticamente el esquema de una tabla existente. Si la tabla real tiene otros nombres de columnas, adapta obtener.php a los nombres correctos.

## Arquitectura
GitHub guarda código; Render ejecuta PHP; Supabase PostgreSQL guarda las tablas. El navegador nunca se conecta con credenciales administrativas. Cambiar valores de public.simulacion_estado en Supabase SQL Editor afecta el NOC en el próximo sondeo. La tabla simulada está separada de las lecturas reales.

## Qué hace el sonido
Con permiso y un toque previo en “Activar alertas”, el dashboard puede emitir un tono breve y mostrar una notificación mientras la página está abierta. La notificación no es un push garantizado si el sitio está cerrado o suspendido; para eso faltaría integrar Firebase Cloud Messaging o el sistema push de la app nativa.
