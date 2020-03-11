# Prueba Técnica


Esta aplicación ha sido desarrollada como respuesta a la "Prueba Técnica" usando el framework Lumen (6.3.3)

## Diseño y Arquitectura
Durante el desarrollo de esta aplicación se ha tratado de realizar todas las buenas prácticas posibles, aunque por tratarse de una aplicación Demo desarrollada en poco tiempo, puede haber algunos antipatrones. 

Por ejemplo, debido a la poca cantidad de reglas de control de accesos, 
se ha decidido colocar dichas reglas directamente en el archivo `./app/Policies/UserPolicy.php` en un pequeño array de cuatro elementos.
De tratarse de una mayor cantidad de reglas se pudo haber creado una tabla de permisos y roles.

Para la realización de este proyecto solamente se ha usado Lumen y sus dependencias, sin incluir otras librerías de terceros. 
Se ha tratado de usar lo necesario y disponible que tiene Lumen para el funcionamiento correcto de la aplicación.

La arquitectura de de Lumen en sí ayuda a realizar buenas prácticas de desarrollo, se ha seguido los mismo lineamientos, esto se puede ver en diversos archivos por ejemplo el uso de Middlewares, Wrappers, Facades, Gate, Singletons, Traits, etc. 

Existen algunos archivos que vienen predeterminadamente con Lumen, se han dejado esos archivos. aún asi no se estén usando ya que para un futuro sirven como referencia o template. por ejemplo los archivos `Example*.php`  

Algunos archivos que no debería estar incluidos en el repositorio, han sido incluidos intencionalmente para que el despliegue de la aplicación sea mas rápido, por ejemplo los archivos `./.evn` y `./docker/db_password.txt`

## Instalación

Para ejecutar fácilmente el proyecto, se puede correr en docker.

En la carpeta ./docker/ encontrará los archivos necesarios para ejecutar la aplicación

- Verificar que docker se encuentra ejecutándose en su ordenador: `docker ps`.
- Verificar que tiene disponible Docker Compose ejecutando el comando: `docker-compose -v` también puede instalarlo desde [aquí](https://docs.docker.com/compose/install/).
- Ingresar por línea de comandos y dirigirse a la carpeta docker:  `cd ./docker`.
- Ejecutar: `docker-compose up -d`.
- Esperar a que los paquetes se descarguen y se preparen para la ejecución, esto puede tardar unos minutos.
- Abrir en un navegador la url [http://localhost:8202](http://localhost:8202), si logra ver `Lumen (6.3.3) (Laravel Components ^6.0)` entonces el despliegue en docker ha sido correcto.

###### Consideraciones a tener en cuenta:
- Su ordenador debe tener disponible el puerto 8202 que es donde se ha configurado para que se ejecute la aplicación.
- Cada vez que reinicie la instancia en docker, se limpiará la BD y se cargaran nuevos datos de prueba.
- Se está creando un usuario con rol admin, los datos para el login son:
    `email: admin@prueba-tecnica.test` y  
	`password: dummypassword`
- También puede accederse a las apis con este usuario usando el api_token `nLTzEBs664uEALxL8dYf1ao9z4vgl8TMlBaatZ2LUF8jEM9m9t` este token se encuentra definido en el archivo .env
- Este api_token puede enviarse como parámetro GET en la misma URL o como Bearer Token

## Testing

Para realizar las pruebas unitarias se ha desarrollado principalmente en dos archivos:
- `./tests/UserCrudTest.php` para las pruebas sobre la api CRUD
- `./tests/AuthTest.php` para las pruebas de Autenticación y Autorización.

Ejecutar de la siguiente forma:
- `php ./vendor/phpunit/phpunit/phpunit --configuration ./phpunit.xml UserCrudTest .\tests\UserCrudTest.php --teamcity`
- `php ./vendor/phpunit/phpunit/phpunit --configuration ./phpunit.xml AuthTest .\tests\AuthTest.php --teamcity`

###### Consideraciones a tener en cuenta:
- Los test están ejecutándose sobre la misma Base de Datos que usa la aplicación.
- Todas las entidades modificadas por los test van a persistir en la BD que actualmente usa la aplicación.


## Swagger

Existe un archivo donde se encuentra la definición de las apis `./openapi.yaml`
También se puede ingresar a la url [http://localhost:8202/swagger](http://localhost:8202/swagger)
donde podrá ejecutas las apis disponibles en la aplicación.

## Author

Jose Quilca `jose.quilca (at) outlook.com`

