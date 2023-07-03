<?php

namespace telconet\comercialBundle\Service;
use telconet\schemaBundle\Entity\InfoServicioHistorial;


/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0 27-07-2017 - Servicio que realiza la lógica requerida para InfoServicioHistorial
 */
class InfoServicioHistorialService
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emcom;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emcom = $container->get('doctrine.orm.telconet_entity_manager');
    }

    /**
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0 27-07-2017 - Función que crea el Historial del servicio
     * 
     * @param arrayParametros ['objServicio']     = Objeto del servicio
     *                        ['strUsrCreacion']  = string del usuario
     *                        ['strIpClient']     = string de IP del cliente
     *                        ['strObservacion']  = string de Observación
     *                        ['strAccion']       = string de Acción
     *                        ['intMotivo']       = integer de id del motivo
     *
     * @return objeto InfoServicioHistorial
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.1 06-01-2020 - Se agrega nuevo campo al arreglo de parámetros para guardar el motivo.
     *
     */
    public function crearHistorialServicio($arrayParametros)
    {
        $intMotivo            = ( isset($arrayParametros['intMotivo']) && !empty($arrayParametros['intMotivo']) )
                                ? $arrayParametros['intMotivo'] : null;
        $objServicio          = $arrayParametros['objServicio'];
        $objServicioHistorial = new InfoServicioHistorial();
        $objServicioHistorial->setServicioId($objServicio);
        $objServicioHistorial->setUsrCreacion($arrayParametros['strUsrCreacion']);
        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
        $objServicioHistorial->setIpCreacion($arrayParametros['strIpClient']);
        $objServicioHistorial->setEstado($objServicio->getEstado());
        $objServicioHistorial->setObservacion($arrayParametros['strObservacion']);
        $objServicioHistorial->setAccion($arrayParametros['strAccion']);
        $objServicioHistorial->setMotivoId($intMotivo);
        return $objServicioHistorial;
    }
    
    /**
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 11-12-2019 - Función que crea el Historial de un servicio
     * 
     * @param arrayParametros ['objServicio']     = Objeto del servicio
     *                        ['strUsrCreacion']  = Usuario de creación
     *                        ['strIpClient']     = IP del cliente
     *                        ['strObservacion']  = Observación
     *                        ['strAccion']       = Acción
     *                        ['feCreacion']      = Fecha de creación.
     */
    public function insertInfoServicioHistorial($arrayParametros)
    {
        $objServicio          = $arrayParametros['objServicio'];
        $objServicioHistorial = new InfoServicioHistorial();
        $objServicioHistorial->setServicioId($objServicio);
        $objServicioHistorial->setUsrCreacion($arrayParametros['strUsrCreacion']);
        $objServicioHistorial->setFeCreacion($arrayParametros['feCreacion']);
        $objServicioHistorial->setIpCreacion($arrayParametros['strIpClient']);
        $objServicioHistorial->setEstado($objServicio->getEstado());
        $objServicioHistorial->setObservacion($arrayParametros['strObservacion']);
        $objServicioHistorial->setAccion($arrayParametros['strAccion']);
        return $objServicioHistorial;
    }    

}
