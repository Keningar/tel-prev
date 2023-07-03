<?php

namespace telconet\soporteBundle\Service;


use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class GestionPaqueteSoporteService
{

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer) 
    {
        $this->emSoporte = $objContainer->get('doctrine')->getManager('telconet_soporte');
        $this->emComercial = $objContainer->get('doctrine')->getManager('telconet');
    }    
    
    /**
     * Función que crea el paquete de horas de soporte.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     */
    public function crearPaqueteSoporte($arrayParametros)
    {
        $objGestionPaquete = $this->emSoporte->getRepository('schemaBundle:GestionPaqueteSoporte');


        $arrayParametros['meses'] = $objGestionPaquete
        ->obtenerValorCaracteristica($arrayParametros['servicioPaqueteId'],'Cantidad de meses', $arrayParametros['tipoProducto'])[0]['valor'];
        $arrayParametros['horas'] = $objGestionPaquete
        ->obtenerValorCaracteristica($arrayParametros['servicioPaqueteId'],'Cantidad de horas', $arrayParametros['tipoProducto'])[0]['valor'];
        $arrayParametros['uuidPaquete'] = $arrayParametros['strUuidPaquete'] ? : exec('uuidgen -r');
        $arrayParametros['uuidDetalle'] = exec('uuidgen -r');
        $arrayParametros['tipo']        = $arrayParametros['tipo'] ? :"nuevo";
        $arrayParametros['minutosContratados'] = intval($arrayParametros['horas'])*60;
        $arrayParametros['fechaInicio'] = new \DateTime('now');

        
        $objFechaFin = new \DateTime('now');


        $arrayParametros['fechaFin'] = $objFechaFin->add(new \DateInterval('P'.$arrayParametros['meses'].'M'));
        

        return $objGestionPaquete->crearPaqueteSoporte($arrayParametros);
    }
    
    /**
     * Función que obtiene los puntos por cliente.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     */
    public function getPuntosCliente($arrayParametros)
    {
        $strCodEmpresa = $arrayParametros['codEmpresa'];
        $intIdCliente = $arrayParametros['idCliente'];
        $strNombre = $arrayParametros['nombre'];
        $strRol = $arrayParametros['rol'];
        $intStart = $arrayParametros['start'];
        $intLimit = $arrayParametros['limit'];
        $intPage = $arrayParametros['page'];
        $strEsPadre = $arrayParametros['esPadre'];

        return $this->emComercial->getRepository('schemaBundle:InfoPunto')->findPtosPorEmpresaPorGrupo($strCodEmpresa, $strNombre);
    }

    public function getServiciosByPunto($arrayParametros)
    {

        $strIdPunto = $arrayParametros['idPunto'];
        $strCodEmpresa = $arrayParametros['codEmpresa'];
        $strLogin = $arrayParametros['login'];
        $strLogin2 = $arrayParametros['login2'];
        $strEstado = $arrayParametros['estado'];
        $arrayTraslados = $arrayParametros['arrayTraslados'];

        return $this->emComercial->getRepository('schemaBundle:InfoServicio')->findServiciosByLoginAndLogin($strLogin, $strLogin2, $strCodEmpresa);
    }

    public function putServiciosPaqueteSoporte($arrayParametros)
    {
        return $this->emSoporte->getRepository('schemaBundle:InfoPaqueteSoporteServ')->putServiciosPaqueteSoporte($arrayParametros);
    }

    //Consultar soportes paquete soporte
    public function getSoportesPaqueteSoporte($arrayParametros)
    {
        return $this->emSoporte->getRepository('schemaBundle:GestionPaqueteSoporte')->getSoportesPaqueteSoporte($arrayParametros);
    }

    //Ajustar tiempo de soporte
    public function putSolAjusteTiempoSoporte($arrayParametros)
    {
        return $this->emSoporte->getRepository('schemaBundle:GestionPaqueteSoporte')->putSolAjusteTiempoSoporte($arrayParametros);
    }

    //Gestionar solicitud por ajuste de tiempo
    public function getSolicitudesPuntoTipo($arrayParametros)
    {

        $strTipo = $arrayParametros['tipo'];
        $strLogin = $arrayParametros['login'];

        return $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->getSolicitudesPuntoTipo($strTipo, $strLogin);
    }

    public function putAprobarSolAjstTiempoSoporte($arrayParametros)
    {
        return $this->emSoporte->getRepository('schemaBundle:GestionPaqueteSoporte')->putAprobarSolAjstTiempoSoporte($arrayParametros);
    }

}