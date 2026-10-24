DIRECTORIO DE CONTACTOS BA

Proyecto web desarrollado utilizando PHP, MySQL, MySQLi, Bootstrap 5 y JavaScript. El sistema permite administrar contactos personales y empresariales mediante una interfaz sencilla y adaptable a diferentes dispositivos.
La aplicación se encuentra publicada en Railway, por lo que puede utilizarse desde internet sin necesidad de instalar XAMPP ni ejecutar servidores locales.

TECNOLOGÍAS UTILIZADAS

•	PHP 8.3
•	MySQL
•	MySQLi para la conexión a la base de datos
•	Bootstrap 5
•	HTML, CSS y JavaScript
•	Apache
•	Docker
•	Railway para el alojamiento de la aplicación y la base de datos
•	GitHub para el almacenamiento del código fuente
•	Postman para pruebas de la API REST

REQUISITOS

Para utilizar la aplicación publicada únicamente se necesita:
•	Conexión a internet.
•	Navegador web actualizado.
•	Credenciales de acceso al sistema.
No es necesario instalar XAMPP, Apache, MySQL ni phpMyAdmin en el equipo del usuario, ya que la aplicación y la base de datos están alojadas en Railway.

CÓMO ACCEDER AL PROYECTO

La aplicación se encuentra disponible en:
https://proyectofinalv2-production-7190.up.railway.app/

Para utilizarla:

1.	Abrir el navegador web.
2.	Ingresar a la dirección de la aplicación.
3.	Iniciar sesión con una cuenta registrada.
4.	Acceder al directorio y utilizar sus diferentes funciones.

BASE DE DATOS
El sistema utiliza MySQL alojado en Railway.
Nombre de la base de datos local: bd_gestion_contactos
Nombre de la base de datos en Railway: railway

La base de datos contiene tres tablas principales:

•	usuarios: almacena la información de los usuarios registrados.
•	contacto: almacena los datos de los contactos personales y empresariales.
•	categorias: permite clasificar los contactos.

La conexión a la base de datos se realiza mediante MySQLi, utilizando variables de entorno configuradas en Railway:

•	DB_HOST
•	DB_PORT
•	DB_USERNAME
•	DB_PASSWORD
•	DB_DATABASE

Estas variables permiten mantener las credenciales fuera del código fuente.

CREDENCIALES DE ACCESO
Para utilizar el sistema es necesario contar con un usuario registrado.
Las contraseñas se almacenan mediante hash y se verifican durante el inicio de sesión.
Las credenciales de prueba pueden proporcionarse de forma privada al evaluador.

FUNCIONAMIENTO DEL SISTEMA
El Directorio de Contactos BA permite:

•	Registrar usuarios.
•	Iniciar sesión.
•	Agregar nuevos contactos.
•	Consultar contactos registrados.
•	Buscar y filtrar contactos.
•	Clasificar contactos por categorías.
•	Editar la información de los contactos.
•	Eliminar contactos.
