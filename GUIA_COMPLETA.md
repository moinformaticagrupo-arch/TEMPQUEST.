# NEXUS NOC: paquete para GitHub, Render y Supabase

La guía principal está en GUIA_RENDER_SUPABASE.md. Ejecutá schema_postgres.sql en Supabase SQL Editor; está adaptado del dump MySQL enviado, conserva las tablas dispositivos y mediciones e importa las 113 filas incluidas. PRUEBAS_SQL.md contiene consultas para editar valores y probar alertas desde Supabase.

El proyecto ofrece:
- index.html: dashboard y alertas mientras permanece abierto.
- control.html: consola móvil.
- PHP con PDO PostgreSQL.
- simulacion_estado separado de mediciones.
- actualización del dashboard cada 2 segundos y variación simulada cada 5.
- Dockerfile y render.yaml para despliegue en Render.

La conexión real y token se cargan en variables privadas de Render. No subas secretos a GitHub. La alarma sonora necesita que la persona habilite alertas y tenga la página abierta; las notificaciones push en segundo plano no están incluidas.


## Acceso discreto al control
Desde el dashboard, toca cinco veces seguidas el título NEXUS NOC en menos de 2,5 segundos para abrir control.html. El enlace no aparece como botón. El token sigue siendo necesario para iniciar o detener simulaciones.
