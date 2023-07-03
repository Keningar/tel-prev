<?php

namespace telconet\soporteBundle\Service;

use Doctrine\ORM\EntityManager;
use telconet\schemaBundle\Entity\InfoDocumento;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

     /**
     * Clase SoporteService
     *
     * Clase que maneja las Transacciones realizadas en el módulo de Soporte - Creacion Casos
     *
     * @author Jesus Bozada P. <jbozada@telconet.ec>
     * @version 1.0 08-08-2014
     */    
class PlantillaService {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emSoporte;
       
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {	    	    	         	    
	    $this->emSoporte         = $container->get('doctrine.orm.telconet_comunicacion_entity_manager');	    
    }
    
     /**
     * listarPlantillas
     *
     * Metodo encargado de generar listado de plantillas de notificaciones
     * @param integer $tipo
     * @param string $nombre
     * @param integer $estado
     * @param integer $empresa
     * @param integer $start
     * @param integer $limit
     * @param string $strLogin Login del usuario creación.
     *
     * @return arreglo con listado de plantillas
     *
     * @author Jesus Bozada P. <jbozada@telconet.ec>
     * @version 1.0 08-08-2014
     *
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-06-15 Corregir nombre de parámetro filtarNoticia
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.2 26-07-2016
     * Se agrega el parámetro $strLogin para filtrar por usuario creación.
     */
    public function listarPlantillas($tipo, $nombre, $estado, $empresa, $start, $limit, $filtrarNoticia = "", $strLogin = null)
    {
        $this->emSoporte->getConnection()->beginTransaction();
        $parametros = array();
       
        $parametros["tipo"] = $tipo; //Parametro que viaja en el request tanto al cargar como al buscar por filtro
        $parametros["nombre"] = $nombre;
        $parametros["estado"] = $estado;
        $parametros["empresa"] = $empresa;
        $parametros["filtrarNoticia"] = $filtrarNoticia;
        $parametros["login"] = $strLogin;

        return $this->emSoporte
                    ->getRepository('schemaBundle:InfoDocumento')
                    ->generarJsonDocumentos($parametros, $start, $limit);
    }

}