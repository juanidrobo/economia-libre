
Esto es un sistema para romper las cadenas de la esclavitud (dinero imaginario),
tu ayuda sera definitiva para lograrlo!

Economía Libre
===============

¿Economía Libre?
Economía basada en monedas de valor real. La creación o emisión de la moneda es pública.
Cada persona o grupo de personas tiene el derecho a crear monedas de valor real,
cambiarlas por productos o servicios, cambiarlas por otras monedas de valor real y/o
recibir pagos en moneda de valor real por productos o servicios prestados.

¿Que es una moneda de valor real?
El valor real es la promesa de un producto o la promesa de un servicio,
una moneda puede tener la promesa de un kilo de sal, la promesa de una una silla,
la promesa de un almuerzo, la promesa de una clase de guitarra o la promesa de
un masaje, etc. La moneda de valor real esta respaldada por una o varias personas.
CREADOR(ES). El dueño de una moneda de valor real al RECLAMAR el valor a su
CREADOR, tendrá a cambio algo tangible o experimentable, producto o servicio
prometido en la moneda. Cada persona puede crear monedas con diferentes valores
o promesas, recibir pagos con moneda de valor real o crear tantas monedas como quiera.

https://www.economia-libre.org/que-es


Código libre:
==============

Para descargar el código libre:
git clone https://github.com/juanidrobo/economia-libre.git

Requerimientos
PHP 5.6

Instalación
============

Economía libre esta construida sobre Symfony. Framework php.
http://symfony.com  NO es necesario instalar directamente,
esto lo hara Composer automáticamente.

Utiliza Composer para instalar dependencias.
https://getcomposer.org/ INSTALA Composer.

En la terminal, en el directorio raiz donde esta el archivo composer.lock

> composer install

Esto instalara las dependencias necesarias para ejecutar Economía Libre, 
esto incluye Symfony framework.



Configura el archivo parameters.yml
=========

/app/config/parameters.yml
Este archivo contiene la información de tu servidor, información de base de datos,
información del servicio de email (smtp) e información de los servicios 
que quieras utilizar. (Recaptcha, fbLogin, GoogleLogin, TwitterLogin, Fb Kit Account)
Sino quieres utilizar uno de estos servicios debes de hacer los cambios tu mismo, 
esto de incluir o no un servicio estará habilitado en el modulo de administración
del sistema de Economía Libre que sera desarrollado mas adelante.

Cambia ??????? por la información requerida.


Crea la base de datos
=====================
En la terminal, en el directorio raíz del proyecto esta el directorio  app/

> chmod +x ./console

> ./console doctrine:schema:create 

Esto creara la base de datos de Economía Libre!
Tienes que verificar los datos de conexión en el archivo parameters.yml



Prueba
=========

http://localhost/xxxxxx/web/app_dev.php

Verifica los permisos de escritura sobre los directorios 
/app/cache
/app/logs


Si utilizas la cola para enviar los correos en vez de enviarlos directamente
debes de configurar en el archivo /app/config/config.yml en la 
sección de # Swiftmailer Configuration
https://symfony.com/doc/current/email/spool.html

    Verifica los permisos
    /app/spool o directorio que hayas configurado para la cola de correos



¿Que sigue en Economía Libre?
Crear monedas de valor real y ponerlas en circulación.
Ofrecer productos y/o servicios y recibir pagos monedas de valor real.

Desarrollar un modulo para manejo del servidor de Economía Libre, 
manejo de usuarios, activación de cuentas, confirmación de datos, 
configuración de variables del sistema. 

Implementar el envío de correos a través de la cola de swiftmailer,
actualmente enviando en vivo.

Implementar una forma de imprimir, en papel, las monedas creadas, con diferentes
diseños. 

Desarrollar un modulo de comunicación entre diferentes servidores de Economía Libre.
En donde exista una red de confianza entre servidores que puede ser modificada
por su administrador, quien decidirá con cual servidor establece comunicación.
Esto generara una red de confianza de servidores que aunque con diferentes
configuraciones mantiene el espíritu de la Economía Libre, de hacer el bien 
procurando que todos nos beneficiemos sin ser cómplices de los que utilizan las 
tecnologías para aprovecharse de otros.
Esto servirá de reputación del servidor y de control social.

Implementar la posibilidad de cambiar de idioma en el sitio por el usuario
y que sea configurable por el administrador del servidor. 

Mejorar la visualización de monedas en la sección "Mis Monedas", tener la opción
de agrupamientos por diferentes criterios (monedas en circulación, 
monedas reclamadas, monedas similares).
Tener la posibilidad de agrupar monedas según el criterio del usuario.

Refactor del código, php, javascript, css.

Un abrazo!
