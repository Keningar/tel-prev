<?php

namespace telconet\tecnicoBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
/**
 * Clase Service InfoInterfaceElemento
 * 
 * Clase donde se implementa servicio de liberacion de Interfaces
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 18-12-2014
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.1 10-05-2021 Se modifica el __construct por el uso de setDependencies
 * 
 */
class InfoInterfaceElementoService
{
    private $objContainer;
    private $emComercial;
    
    public function setDependencies(Container $objContainer)
    {
        $this->objContainer = $objContainer;
        $this->emComercial  = $objContainer->get('doctrine')->getManager('telconet');
    }
    
        
    /**
     * Documentación para el método 'liberarInterfaceSplitter'.
     *
     * Libera los recursos de red de un servicio
     * @return response.
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 18-12-2014
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 10-05-2021 Se agregan validaciones y se setea a null los campos de la INFO_SERVICIO_TECNICO, tal cual como lo hace la opción 
     *                         automática, por errores al anular luego de rechazar una solicitud de servicio de Internet
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 26-05-2021 Liberación para servicios con tipo de red GPON y con productos técnicos DATOS SAFECITY, L3MPLS, INTERNET y INTMPLS
     *
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 1.3 02-03-2023 Se agrega validacion por Prefijo de Empresa para Ecuanet.
     * 
     */
    public function liberarInterfaceSplitter($arrayParametros)
    {
        $strMensaje             = "";
        $objServicio            = $arrayParametros["objServicio"];
        $strUsrCreacion         = $arrayParametros["strUsrCreacion"] ? $arrayParametros["strUsrCreacion"] : "liberainterface";
        $strIpCreacion          = $arrayParametros["strIpCreacion"] ? $arrayParametros["strIpCreacion"] : "127.0.0.1";
        $strProcesoLibera       = $arrayParametros["strProcesoLibera"];
        $strVerificaLiberacion  = $arrayParametros["strVerificaLiberacion"] ? $arrayParametros["strVerificaLiberacion"] : "NO";
        $strPrefijoEmpresa      = $arrayParametros["strPrefijoEmpresa"];
        $booleanTipoRedGpon     = isset($arrayParametros["booleanTipoRedGpon"]) ? $arrayParametros["booleanTipoRedGpon"] : false;
        $strAplicaLiberacion    = "NO";
        try
        {
            if(!is_object($objServicio))
            {
                throw new \Exception("No se ha enviado correctamente el objeto asociado al servicio para liberar el puerto");
            }
            
            if($strVerificaLiberacion === "SI")
            {
                if(!isset($strPrefijoEmpresa) || empty($strPrefijoEmpresa))
                {
                    throw new \Exception("No se ha enviado correctamente el prefijo de la empresa para liberar el puerto");
                }
                $booleanProductoGpon = ($booleanTipoRedGpon && is_object($objServicio->getProductoId()) &&
                                        ($objServicio->getProductoId()->getNombreTecnico() === "DATOS SAFECITY" ||
                                        $objServicio->getProductoId()->getNombreTecnico() === "L3MPLS" ||
                                        $objServicio->getProductoId()->getNombreTecnico() === "INTERNET" ||
                                        $objServicio->getProductoId()->getNombreTecnico() === "INTMPLS"));
                if ((($strPrefijoEmpresa == "MD" || $strPrefijoEmpresa == "EN") && is_object($objServicio->getPlanId()))
                    || (is_object($objServicio->getProductoId()) 
                        && ($objServicio->getProductoId()->getNombreTecnico() === "INTERNET SMALL BUSINESS"
                            || $objServicio->getProductoId()->getNombreTecnico() === "TELCOHOME"))
                    || ($strPrefijoEmpresa == "TNP" && is_object($objServicio->getPlanId()))
                    || $booleanProductoGpon
                   )
                {
                    $strAplicaLiberacion = "SI";
                }
            }
            else
            {
                $strAplicaLiberacion = "SI";
            }
            
            if($strAplicaLiberacion === "SI")
            {
                $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                        ->findOneByServicioId($objServicio->getId());
                //se agrega validacion de que se recupere objeto servicio tecnico
                if(is_object($objServicioTecnico))
                {
                    $intIdInterfaceElementoConector = $objServicioTecnico->getInterfaceElementoConectorId();
                    if(isset($intIdInterfaceElementoConector) && !empty($intIdInterfaceElementoConector))
                    {
                    	$strObservacionServicio = "Se libera la interface elemento conector ".$strProcesoLibera
                    			                  . " con las siguientes características:<br>"
            			          	              ."ID: ".$intIdInterfaceElementoConector;
                        $objInterfaceSplitter = $this->emComercial->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                     ->find($intIdInterfaceElementoConector);
                        if(is_object($objInterfaceSplitter))
                        {
                            $strObservacionServicio .= "<br>Nombre de la interface: ".$objInterfaceSplitter->getNombreInterfaceElemento();
                            //libero interface anterior
                            $objInterfaceSplitter->setEstado("not connect");
                            $objInterfaceSplitter->setFeUltMod(new \DateTime('now'));
                            $objInterfaceSplitter->setUsrUltMod($strUsrCreacion);
                            $this->emComercial->persist($objInterfaceSplitter);
                            $this->emComercial->flush();
                        }
                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objServicio);
                        $objServicioHistorial->setEstado($objServicio->getEstado());
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setIpCreacion($strIpCreacion);
                        $objServicioHistorial->setObservacion($strObservacionServicio);
                        $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                        $this->emComercial->persist($objServicioHistorial);
                        $this->emComercial->flush();
                    }
                    $objServicioTecnico->setElementoId(null);
                    $objServicioTecnico->setInterfaceElementoId(null);
                    $objServicioTecnico->setElementoContenedorId(null);
                    $objServicioTecnico->setElementoConectorId(null);
                    $objServicioTecnico->setInterfaceElementoConectorId(null);
                    $objServicioTecnico->setElementoClienteId(null);
                    $objServicioTecnico->setInterfaceElementoClienteId(null);
                    $this->emComercial->persist($objServicioTecnico);
                    $this->emComercial->flush();
                }
            }
            $strStatus = "OK";
        }
        catch(\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = "Error: <br>" . $e->getMessage().". Favor notificar a Sistemas.";
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }
}
