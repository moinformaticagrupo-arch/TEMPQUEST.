# Alertas incluidas en la demo

El dashboard revisa las lecturas cada dos segundos y muestra una banda roja cuando detecta nodo desconectado, temperatura mayor a 32 °C, CPU igual o mayor a 90 %, o aviso térmico por encima de 27 °C. Se genera una alerta nueva cuando el estado cambia a otra condición; no repite sonido en cada consulta mientras siga activa.

En el teléfono, abrí el dashboard por HTTPS y tocá “Activar alertas en este celular” una vez. Ese gesto pide permisos de notificación, registra el service worker y habilita un tono corto mientras la página está abierta. Los navegadores y el sistema operativo controlan permisos, volumen y modo silencio.

Esta versión NO implementa envío push cuando el sitio está cerrado o suspendido. Para ese caso se necesita registrar el dispositivo y configurar Firebase Cloud Messaging (u otro proveedor push), servicio worker de mensajes en segundo plano y una función segura que envíe la notificación. No se puede garantizar que un sitio web haga sonar el celular si la persona revocó permisos, el sistema está en silencio o el navegador suspende la página.
