# Curso de Symfony 5. Creando una API desde cero

    https://www.youtube.com/channel/UCMVky0AEACLisBqM6mXxCng
 

## Capítulo 1. Configuración del proyecto
   
    php new nombreProyecto // crea un proyecto desde 0 sin plantillas
    symfony server:start // arrancar el servidor
    composer require annotation // instala anotaciones para definir rutas.
    composer require logger // instalacion del servicio logger

## Capítulo 2. Controllers y rutas

## Capítulo 3. Servicios y container
    
    composer require symfony/orm-pack  // maping objeto relacional
    composer require --dev symfony/maker-bundle  //paquete para mapear las clases de la base de datos

## Capítulo 4. Base de datos e integración con Doctrine

    bin/console doctrine:database:create // crea la base de datos, comprobar en el php.ini las extensiones de la base de datos

## Capítulo 5. FOS Rest Bundle

    composer require friendsofsymfony/rest-bundle // para construir la api requiere un serializer  para json, el de symfony
    composer require symfony/serializer-pack // el serializer de symfony
    composer require symfony/validator twig doctrine/annotations // es requrido para poder continuar

## Capítulo 6. Formularios

    composer require symfony/form // Para crear el tipo de objeto que vamosa a recibir y realizar las validaciones

## Capítulo 7. DTO's y carga de imágenes

    configurara DTO y excuir la carpetra DTO de los servicios en: Config/sevices.yaml
    composer require league/flysystem-bundle // para trabajar con archivos
    serializer de symfony modificamos la salida de los objetos con el serialicer y el normalizer

## -Capítulo 8. Servicios y PHP Unit
    