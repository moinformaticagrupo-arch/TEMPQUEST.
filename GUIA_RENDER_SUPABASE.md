# Publicación: GitHub + Render + Supabase

## Arquitectura

- GitHub guarda el código del proyecto.
- Render ejecuta PHP como servicio web Docker.
- Supabase aloja PostgreSQL.
- El dashboard y la app/control hablan con PHP en Render; PHP lee/escribe la misma base Supabase.

Supabase usa PostgreSQL, no MySQL. El archivo schema_postgres.sql convierte el esquema que compartiste: dispositivos y mediciones, incluye sus 113 lecturas de ejemplo y agrega simulacion_estado. No contiene instrucciones DROP.

## 1. Importar la base en Supabase

1. En Supabase, abrí el proyecto destino y luego SQL Editor > New query.
2. Antes de ejecutar, confirmá en Table Editor si ya existen public.dispositivos o public.mediciones. Si existen y contienen datos valiosos, hacé backup y no dupliques filas a ciegas. El SQL usa ON CONFLICT DO NOTHING, pero una tabla existente con columnas diferentes requiere adaptación.
3. Si es un proyecto limpio, pegá todo el contenido de schema_postgres.sql en el editor y ejecutalo.
4. Verificá que aparezcan public.dispositivos, public.mediciones y public.simulacion_estado. El dump original tenía 2 dispositivos y 113 mediciones.
5. Para el endpoint PHP, la tabla de lecturas debe tener las columnas id, id_dispositivo, temperatura, humedad, presion y fecha_hora, y dispositivos debe tener id y nombre.

## 2. Subir a GitHub

1. Descomprimí el ZIP.
2. Creá un repositorio nuevo en GitHub.
3. Subí el contenido de la carpeta nexus_github_render_supabase a la raíz del repo, incluido Dockerfile, render.yaml y docker-entrypoint.sh.
4. No subas contraseñas, tokens ni la URL de conexión completa. Este paquete no contiene esas credenciales.

## 3. Crear servicio en Render

1. En Render elegí New > Blueprint y conectá el repositorio; alternativamente creá un Web Service, elegí Docker y apuntá al Dockerfile de la raíz.
2. Configurá las variables privadas:
   - DB_HOST: host PostgreSQL mostrado por Supabase.
   - DB_PORT: puerto de la conexión elegida.
   - DB_NAME: normalmente postgres.
   - DB_USER: usuario que muestra la cadena de conexión (el pooler puede tener un nombre con el identificador del proyecto).
   - DB_PASSWORD: contraseña PostgreSQL de Supabase.
   - DB_SSLMODE: require.
   - CONTROL_TOKEN: inventá un token largo y único para autorizar controles.
3. Para DB_HOST/PORT/USER/PASSWORD, usá la conexión Postgres recomendada en Supabase > Connect. Si Render no alcanza la conexión directa, probá el pooler de Supabase y usá su host, puerto y usuario exactos.
4. Esperá el deploy.

## 4. Comprobar el servicio

Reemplazá SERVICIO por el nombre asignado por Render:

- /health.php debe responder {"ok":true}.
- /obtener.php debe devolver las últimas mediciones importadas (JSON).
- /simulacion_control.php debe devolver el estado actual.
- /control.html permite activar el escenario desde el teléfono.
- / abre el dashboard.

En control.html ingresá el mismo CONTROL_TOKEN configurado en Render. Abrí el dashboard y tocá “Activar alertas en este celular” para permitir notificación/sonido mientras la página está abierta.

## 5. Para que la app de tu amigo use el mismo backend

La app debe enviar sus solicitudes al URL de Render, no al localhost de XAMPP. Si su app ya usa PHP, desplegá también ese backend en Render y configura sus credenciales DB_* para la misma Supabase. Si la app solo consume API, cambiale la base URL a la API Render.

Si la app sigue escribiendo a una base MySQL de XAMPP, no compartirá datos con Supabase. Hay que migrar las lecturas o reconfigurar su backend para usar PostgreSQL. No pongas la contraseña de Supabase en el código móvil. Conectá por PHP/API.

El endpoint obtener.php espera public.mediciones y public.dispositivos. Las lecturas nuevas deben insertarse en mediciones con id_dispositivo válido. Una vez compartas el repo o el código de conexión de tu amigo sin secretos, se pueden ajustar los endpoints a su app.

## Alertas

Los valores se consultan cada 2 segundos. Se muestra alerta si temperatura supera 27°C (aviso), llega a 32°C, CPU es >=90%, o pve-02 está desconectado. La alarma sonora y notificación en navegador se habilitan con un toque en el dashboard. Eso funciona con la página abierta. Push garantizado cuando está cerrada requiere Firebase Cloud Messaging u otra integración, no incluida en esta versión.

## Seguridad y límites

- Configurá credenciales solo en Environment de Render. No las pegues en GitHub ni en el chat.
- El token de control es una protección demo, no autenticación con usuarios.
- El plan gratuito de Render puede suspender servicios inactivos; abrí la página antes de presentar y contemplá que puede tardar en despertar.
- La tabla simulacion_estado está separada de mediciones. Mientras activa, el dashboard muestra valores simulados; al desactivarla, vuelve al dato más nuevo.


## Abrir control sin enlace visible
En el dashboard, toca cinco veces seguidas el título NEXUS NOC en menos de 2,5 segundos. Se abrirá control.html en el mismo sitio. Luego ingresá CONTROL_TOKEN. Este gesto solo oculta el enlace en la interfaz; la autorización real la aplica el token.
