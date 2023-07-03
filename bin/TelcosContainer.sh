#!/bin/sh
#####
#####
# Puerto de Debug en Netbeans: 9002
# Examples:
#
# Download image Telcos (Container)
# sh TelcosContainer.sh "DOWNLOAD"
#
# Create and Start Telcos for first time (Image and Container).
# sh TelcosContainer.sh "INIT" "/var/www/telcos"
#
# Start Telcos (Container)
# sh TelcosContainer.sh "START"
#
# Stop Telcos (Container)
# sh TelcosContainer.sh "STOP"
#
# Restart Telcos (Container)
# sh TelcosContainer.sh "RESTART"
#
# Delete Telcos (Container)
# sh TelcosContainer.sh "DELETE"
#
# Update Telcos (Container)
# sh TelcosContainer.sh "UPDATE" "/var/www/telcos"
#####
#####

#####
##
## Status: 0 -> End without error
##         1 -> End with a error controlled
##        -1 -> End with a error no controlled
##
#####

###Global Variables###
codeProcess=$1
nameContainer='telcos'
nameRegistry='registry.gitlab.telconet.ec'
nameImage="/docker/images/telcos"
tag="latest"
fullimage=$nameRegistry$nameImage':'$tag
pathTelcos=$2
homeUser=$(echo $pathTelcos | awk -F\/ '{ print "/"$2"/"$3 }');
functionStatus=-1
KeyIn=""
dirCache='app/cache'
dirLogs='app/logs'
pathParameter=$pathTelcos'/app/config/parameters.yml'
url_telcos="http://dev-telcos-developer.telconet.ec"

###Functions###
f_showError()
{
  if [ $functionStatus -eq -1 ]; then
    echo $1;
    return 1;
  fi
}

f_showWarning()
{
  if [ $functionStatus -eq 1 ]; then
    echo $1;
  fi
}

f_startDocker()
{
  flagDockerContainer=`docker ps | grep -c -w $nameContainer`;
  if [ $flagDockerContainer -eq 0 ]; then
    docker start $nameContainer;
    echo "Contenedor "$nameContainer" iniciado correctamente";
    functionStatus=0;
  else
    echo "El contenedor "$nameContainer" ya se encuentra inciado";
    functionStatus=1;
    exit 1;
  fi
  f_showError "Error en la función f_startDocker";
}

f_restartDocker()
{
  docker stop $nameContainer;
  docker start $nameContainer;
  flagDockerContainer=`docker ps | grep -c -w $nameContainer`;
  if [ $flagDockerContainer -eq 0 ]; then
    echo "Error al reiniciar el contenedor";
    functionStatus=-1;
  else
    echo "El contendor "$nameContainer" iniciado correctamente";
    functionStatus=0;
  fi
  f_showError "Error en la función f_restartDocker";
}

f_dockerConfiguration()
{
  echo "Configurando Docker:";
  docker exec $nameContainer bash -c "service nginx restart >/dev/null 2>&1";
  docker exec $nameContainer bash -c "service php-fpm restart >/dev/null 2>&1";
  docker exec $nameContainer bash -c "php /home/telcos/app/console assets:install --symlink /home/telcos/web/";
  docker exec $nameContainer bash -c "rm -rf /home/telcos/app/cache/*;chmod -R 777 /home/telcos/app/cache/";
  docker exec $nameContainer bash -c "php /home/telcos/app/console cache:clear --env=prod >/dev/null 2>&1; chmod -R 777 /home/telcos/app/cache/ /home/telcos/app/logs/";
  docker exec $nameContainer bash -c "php /home/telcos/app/console cache:clear --env=dev >/dev/null 2>&1; chmod -R 777 /home/telcos/app/cache/ /home/telcos/app/logs/";
}

f_configHosts()
{
  echo "Configurando archivo hosts";
  flagHostConf=`cat /etc/hosts | grep -w "dev-telcos-developer.telconet.ec" | grep -c -w "172.17.0.1"`;
  if [ $flagHostConf -eq 0 ]; then
    echo "Agregando host de desarrollo";
    echo "172.17.0.1      dev-telcos-developer.telconet.ec" >> /etc/hosts;
    functionStatus=0;
  else
    echo "Host dev-telcos-developer.telconet.ec ya se encuentra configurado";
    functionStatus=1;
  fi
  f_showError;
}

f_validDir()
{
  echo "Validando Directorios Cache y Logs";
  if [ ! -d "$pathTelcos/$dirCache" ]; then
    mkdir $pathTelcos/$dirCache;
  fi
  if [ ! -d "$pathTelcos/$dirLogs" ]; then
    mkdir $pathTelcos/$dirLogs;
  fi
}

f_allowFile()
{
  echo "Dando permisos a telcos";
  sudo chmod 777 -R $pathTelcos/web;
  sudo chmod 777 -R $pathTelcos/$dirCache;
  sudo chmod 777 -R $pathTelcos/$dirLogs;
  sudo chmod 777 -R $pathTelcos/src/telconet/tecnicoBundle/batch;
  sudo chmod 777 -R $pathTelcos/src/telconet/soporteBundle/batch;
  cd $pathTelcos;git config core.filemode false;
}

f_createContainer()
{
  echo "Creando contenedor del Docker";
  flagDockerContainer=`docker ps | grep -c -w $nameContainer`;
  if [ $flagDockerContainer -eq 1 ]; then
    echo "Nombre del contenedor '"$nameContainer"' ya se encuentra en uso";
    return 1;
  fi
  docker run -d -p 80:80 -p 443:443 --restart always --add-host=idp-test.telconet.ec:172.24.14.90 --add-host=soc.i.telconet.net:172.24.4.54 --add-host=test-middleware.netlife.net.ec:10.100.105.27 --add-host=dev-telcos-developer.telconet.ec:127.0.0.1 --name $nameContainer -v $pathTelcos:/home/telcos -t $fullimage;
  flagDockerContainer=`docker ps | grep -c -w $nameContainer`;
  if [ $flagDockerContainer -eq 0 ]; then
    echo "No se pudo crear el contenedor "$nameContainer;
    return 1;
  else
    echo "Contenedor "$nameContainer" creado correctamente";
  fi
}

f_validParameter()
{
  echo "Validando Parameters.yml";
  if [ ! -e "$pathParameter" ]; then
    echo "No se encuentra configurado el Parameters.yml en la ruta $pathParameter";
    exit 1;
  fi
}

f_stopContainer()
{
  flagDockerContainer=`docker ps | grep -c -w $nameContainer`;
  if [ $flagDockerContainer -eq 1 ]; then
    docker stop $nameContainer >/dev/null 2>&1;
    flagDockerContainer=`docker ps | grep -c -w $nameContainer`;
      if [ $flagDockerContainer -eq 0 ]; then
        echo "Se detuvo el contenedor: $nameContainer";
      else
        echo "No se pudo detener el contenedor: $nameContainer";
      fi
  else
    echo "No se encuentra el contenedor $nameContainer levantado";
  fi
}

f_downloadImageTelcos()
{
  docker pull $fullimage
  findImage=$(docker images | grep $nameImage | grep -c $tag);
  if [ $findImage -eq 1 ]
  then
    echo "Imagen Descargada: $fullimage";
  else
    echo "Inconveniente al buscar la imagen: $fullimage";
    exit 1;
  fi
}

f_deleteContainerTelcos()
{
  docker rm -f $nameContainer >/dev/null 2>&1;
  echo "Contenedor Borrado: $nameContainer";
}

f_validRegistry()
{
  flagDockerRegistry=$(cat $homeUser/.docker/config.json | grep -c $nameRegistry);
  if [ $flagDockerRegistry -eq 0 ]; then
    echo "No se encuentra registrado el repositorio de telconet";
    exit 1;
  else
    echo "Validación de repositorio Completada";
  fi
}

###Main Program###
echo "***Inicio de la configuracion***";

if [ "START" = $codeProcess ]; then

  f_startDocker;

  f_dockerConfiguration;

fi

if [ "INIT" = $codeProcess ]; then

  f_validParameter;

  f_configHosts;

  f_validDir;

  f_allowFile;

  f_validRegistry;

  f_createContainer;

  f_dockerConfiguration;

fi

if [ "RESTART" = $codeProcess ]; then

  f_restartDocker;

  f_dockerConfiguration;

fi

if [ "STOP" = $codeProcess ]; then

  f_stopContainer;

fi

if [ "DOWNLOAD" = $codeProcess ]; then

  f_validRegistry;

  f_downloadImageTelcos;

fi

if [ "DELETE" = $codeProcess ]; then

  f_deleteContainerTelcos;

fi

if [ "UPDATE" = $codeProcess ]; then

  f_validParameter;

  f_validDir;

  f_stopContainer;

  f_deleteContainerTelcos;

  f_validRegistry;

  f_downloadImageTelcos;

  f_configHosts;

#  f_allowFile;

  f_createContainer;

  f_dockerConfiguration;

fi

echo "===>URL telcos: $url_telcos";
echo "***Fin de la Ejecución***";

return 0;
