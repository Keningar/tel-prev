<?php

namespace telconet\planificacionBundle\Service;

use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoDetalleInterface;
use telconet\schemaBundle\Entity\InfoServicioTecnico;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\tecnicoBundle\Service\MigracionHuaweiService;
use telconet\tecnicoBundle\Service\InfoServicioTecnicoService;
use telconet\schemaBundle\Service\UtilService;
use Symfony\Component\HttpFoundation\Response;

class FactibilidadCambioUltimaMillaService {

    private $emComercial;
    private $emInfraestructura;
    private $utilService;
    private $sevicioTecnicoService;
	
    /*
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 09-11-2016
     */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emInfraestructura     = $container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emComercial           = $container->get('doctrine')->getManager('telconet');
        $this->utilService           = $container->get('schema.Util');
        $this->sevicioTecnicoService = $container->get('tecnico.InfoServicioTecnico');
    }
    
    /**
     * Metodo que Genera la factibilidad y la data necesaria para asignacion de recursos/ejecucion de servicios
     * ultima milla Radio
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 27-07-2016
     * @since 1.0 24-06-2016
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 23-03-2020 - Cuando sea terciarizada se va insertar la característica del detalle de la solicitud con el valor
     *                           del id tercerizadora anterior del servicio técnico
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 15-04-2021 - Se abre la programacion tambien para productos L3MPLS SDWAN
     *
     * @param Array $arrayParametros [ intIdSolicitud       Identificador de la solicitud de cambion UM
     *                                 intIdSwitchNew       Identificador del Switch a utilizar en la factibilidad
     *                                 intIdInterfaceNew    Identificador de la interface del switch a utilizar en la factibilidad
     *                                 intIdRadioBbNew      Identificador de Radio de backbone a utilizar en la factibilidad
     *                                 strTipoCambio        Cadena de caracteres que indica el tipo de cambio de UM a realizar 
     *                                 strEsTercerizada     Cadena de caracteres que indica si la radio de BackBone es tercerizada
     *                                 intIdTercerizadora   Identificador de la tercerizadora correspondiente
     *                                 strUsrCreacion       Cadena de caracteres que indica el usuario de creación
     *                                 strIpCreacion        Cadena de caracteres que indica la ip de creación 
     *                               ]
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generarFactibilidadUMRadio($arrayParametros)
    {
        $respuesta                  = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $strObsServicioYSolicitud   = "";
        $intIdSolicitud             = $arrayParametros['intIdSolicitud'];
        $intIdSwitchNew             = $arrayParametros['intIdSwitchNew'];
        $intIdInterfaceNew          = $arrayParametros['intIdInterfaceNew'];
        $intIdRadioBbNew            = $arrayParametros['intIdRadioBbNew'];
        $strTipoCambio              = $arrayParametros['strTipoCambio'];
        $strEsTercerizada           = $arrayParametros['strEsTercerizada'];
        $intIdTercerizadora         = $arrayParametros['intIdTercerizadora'];
        $strUsrCreacion             = $arrayParametros['strUsrCreacion'];
        $strIpCreacion              = $arrayParametros['strIpCreacion'];
        $objIdInterfaceOutNew       = null;
        $intIdSwitch                = null;
        $intIdInterface             = null;
        $intIdRadioBb               = null;
        $strEstadoSolicitud         = "";
        $strAccionFactibilidad      = "";
        $strConectorNombre          = "N/A";
        $strConectorNombreNuevo     = "N/A";
        $objSolicitud               = null;
        $objServicio                = null;
        $objProducto                = null;
        $objServicioTecnico         = null;
        $objTipoMedio               = null;
        $objIdInterfaceOutAnt       = null;
        $objIdInterfaceOut          = null;
        $objElementoSwAnt           = null;
        $objInterfaceAnt            = null;
        $objElementoRadioBbAnt      = null;
        $objElementoSwNue           = null;
        $objInterfaceNue            = null;
        $objElementoRadioBbNue      = null;
        $objIdInterfaceOutNew       = null;
        $strAccionFactibilidad      = "";
        $strEstadoSolicitud         = "";
        $objEnlaceAnt               = null;
        $objCaracTipoCambioUM       = null;
        $objCaracElemento           = null;
        $objCaracInterfaceElemento  = null;
        $objCaracElementoConector   = null;
        $objSolCaracVlan            = null;
        $objPerEmpRolCarVlan        = null;
        $objDetalleElementoVlan     = null;
        $objSolCaracVrf             = null;
        $objPerEmpRolCarVrf         = null;
        $objSolCaracProtocolo       = null;
        $objCaracVlan               = null;
        $objCaracVrf                = null;
        $objCaracProtocolo          = null;
        
        try
        {  
            $this->utilService->validaObjeto($intIdSwitchNew,
                                             "No existe información de SW nuevo, favor notificar a Sistemas.");
            $this->utilService->validaObjeto($intIdInterfaceNew, 
                                             "No existe información del puerto del SW nuevo, favor notificar a Sistemas.");
            $this->utilService->validaObjeto($intIdRadioBbNew,
                                             "No existe información de la radio de backbone nueva, favor notificar a Sistemas.");
            $this->utilService->validaObjeto($strTipoCambio,
                                             "No existe información del tipo de cambio de um a realizar, favor notificar a Sistemas.");
            
            //Se recupera información correspondiente a la solicitud de cambio de ultima milaa radio tn
            $objSolicitud = $this->emComercial
                                 ->getRepository("schemaBundle:InfoDetalleSolicitud")
                                 ->find($intIdSolicitud);
            $this->utilService->validaObjeto($objSolicitud,
                                             "No existe solicitud de cambio Um Radio, favor notificar a Sistemas.");
            
            $objServicio = $objSolicitud->getServicioId();
            $this->utilService->validaObjeto($objServicio,
                                             "No existe servicio, favor notificar a Sistemas.");
            
            $objProducto = $objServicio->getProductoId();
            $this->utilService->validaObjeto($objProducto,
                                             "Servicio no tiene un producto registrado, favor notificar a Sistemas.");
            
            $objServicioTecnico = $this->emComercial
                                       ->getRepository("schemaBundle:InfoServicioTecnico")
                                       ->findOneByServicioId($objServicio->getId());
            $this->utilService->validaObjeto($objServicioTecnico,
                                             "No existe información técnica para el servicio, favor notificar a Sistemas.");
            
            $objTipoMedio = $this->emInfraestructura
                                 ->getRepository('schemaBundle:AdmiTipoMedio')
                                 ->find($objServicioTecnico->getUltimaMillaId());
            $this->utilService->validaObjeto($objTipoMedio,
                                             "Servicio no tiene registrado Tipo Medio, favor notificar a Sistemas.");
            
            $intIdRadioBb = $objServicioTecnico->getElementoConectorId();
            $this->utilService->validaObjeto($intIdRadioBb,
                                             "Servicio no tiene elemento conector, favor notificar a Sistemas.");
            
            $intElementoClienteId = $objServicioTecnico->getElementoClienteId();
            $this->utilService->validaObjeto($intElementoClienteId,
                                            "Servicio no tiene registrado elemento cliente, favor notificar a Sistemas.");
            
            $this->utilService->validaObjeto($objServicioTecnico->getElementoId(),
                                             "Servicio no tiene elemento de Backbone asignado, favor notificar a Sistemas.");
            $intIdSwitch = $objServicioTecnico->getElementoId();
            
            $this->utilService->validaObjeto($objServicioTecnico->getInterfaceElementoId(), 
                                             "Servicio no tiene interface de elemento de Backbone asignado, ".
                                             "favor notificar a Sistemas.");
            $intIdInterface = $objServicioTecnico->getInterfaceElementoId();
            
            //recuperar interface esp del elemento conector antiguo
            $objIdInterfaceOutAnt = $this->emInfraestructura
                                         ->getRepository("schemaBundle:InfoInterfaceElemento")
                                         ->findOneBy((array('elementoId'              => $intIdRadioBb,
                                                            'nombreInterfaceElemento' => 'esp1')));
            $this->utilService->validaObjeto($objIdInterfaceOutAnt, 
                                             "Radio de Backbone actual no tiene interfaces, favor notificar a Sistemas.");
            
            //recuperar interface esp del elemento cliente
            $objIdInterfaceOut = $this->emInfraestructura
                                      ->getRepository("schemaBundle:InfoInterfaceElemento")
                                      ->findOneBy((array('elementoId'              => $intElementoClienteId,
                                                         'nombreInterfaceElemento' => 'esp1')));
            $this->utilService->validaObjeto($objIdInterfaceOut,
                                             "Radio de Cliente actual no tiene interfaces, favor notificar a Sistemas.");
            
            //Seteo de variable es terciarizada
            if($strEsTercerizada=='S')
            {
                //valor anterior del id tercerizadora
                $intIdTercerizadoraAnt    = $objServicioTecnico->getTercerizadoraId();
                if(!empty($intIdTercerizadoraAnt))
                {
                    //grabar detalles tecnicos en la solicitud - TERCERIZADORA
                    $objCaracTercerizada    = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                              ->findOneBy(array('descripcionCaracteristica' => 'TERCERIZADORA'));
                    $this->utilService->validaObjeto($objCaracTercerizada,
                                                     "Información incompleta al registrar caracteristica de solicitud TERCERIZADORA, ".
                                                     "favor notificar a Sistemas.");
                    $arrayParTecTercerizada = array(
                                                    'objDetalleSolicitudId' => $objSolicitud,
                                                    'objCaracteristica'     => $objCaracTercerizada,
                                                    'estado'                => "Asignada",
                                                    'valor'                 => $intIdTercerizadoraAnt,
                                                    'usrCreacion'           => $strUsrCreacion
                                                );
                    $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParTecTercerizada);
                }
                $objServicioTecnico->setTercerizadoraId($intIdTercerizadora);
            }
                
            //Obteniendo la informacion de ultima milla anterior
            $objElementoSwAnt      = $this->emInfraestructura
                                          ->getRepository("schemaBundle:InfoElemento")
                                          ->find($intIdSwitch);
            $this->utilService->validaObjeto($objElementoSwAnt,
                                             "No existe elemento de ultima milla anterior, favor notificar a Sistemas.");
            
            $objInterfaceAnt       = $this->emInfraestructura
                                          ->getRepository("schemaBundle:InfoInterfaceElemento")
                                          ->find($intIdInterface);
            $this->utilService->validaObjeto($objInterfaceAnt,
                                             "No existe interface de elemento de ultima milla anterior, favor notificar a Sistemas.");
            
            $objElementoRadioBbAnt = $this->emInfraestructura
                                          ->getRepository("schemaBundle:InfoElemento")
                                          ->find($intIdRadioBb);
            $this->utilService->validaObjeto($objElementoRadioBbAnt,
                                             "No existe radio de backbone de ultima milla anterior, favor notificar a Sistemas.");
            
            $strConectorNombre     = $objElementoRadioBbAnt->getNombreElemento();
            
            //Obteniendo la informacion de ultima milla nueva              
            $objElementoSwNue       = $this->emInfraestructura
                                           ->getRepository("schemaBundle:InfoElemento")
                                           ->find($intIdSwitchNew);
            $this->utilService->validaObjeto($objElementoSwNue,
                                             "No existe elemento de ultima milla nueva, favor notificar a Sistemas.");
            
            $objInterfaceNue        = $this->emInfraestructura
                                           ->getRepository("schemaBundle:InfoInterfaceElemento")
                                           ->find($intIdInterfaceNew);
            $this->utilService->validaObjeto($objInterfaceNue,
                                             "No existe interface de elemento de ultima milla nueva, favor notificar a Sistemas.");
            
            $objElementoRadioBbNue  = $this->emInfraestructura
                                           ->getRepository("schemaBundle:InfoElemento")
                                           ->find($intIdRadioBbNew);
            $this->utilService->validaObjeto($objElementoRadioBbNue,
                                             "No existe radio de backbone de ultima milla nueva, favor notificar a Sistemas.");
            $strConectorNombreNuevo = $objElementoRadioBbNue->getNombreElemento();
            
            $objIdInterfaceOutNew   = $this->emInfraestructura
                                           ->getRepository("schemaBundle:InfoInterfaceElemento")
                                           ->findOneBy((array('elementoId'              => $intIdRadioBbNew,
                                                              'nombreInterfaceElemento' => 'esp1')));
            $this->utilService->validaObjeto($objIdInterfaceOutNew,
                                             "Radio de Backbone nueva no tiene interfaces, favor notificar a Sistemas.");
            
            //Generando nueva factibilidad y asignando estado siguiente de la solicitud de cambio de um radio
            if($strTipoCambio == "MISMO_SWITCH" || $strTipoCambio == "MISMO_PE_MISMO_ANILLO")
            {
                $strAccionFactibilidad = "Actualizó";
                $strEstadoSolicitud    = "Asignada";
            
                $objServicioTecnico->setElementoId($intIdSwitchNew);
                $objServicioTecnico->setInterfaceElementoId($intIdInterfaceNew);
                $objServicioTecnico->setElementoConectorId($intIdRadioBbNew);
                
                $this->emComercial->persist($objServicioTecnico);
                $this->emComercial->flush();
                                             
            }
            else
            {
                $strAccionFactibilidad = "generó";
                $strEstadoSolicitud    = "AsignadoTarea";
                $intIdSwitch           = $intIdSwitchNew;
                $intIdInterface        = $intIdInterfaceNew;
                $intIdRadioBb          = $intIdRadioBbNew;
            }
            /* actualizacion de enlace de servicio con nueva información de radio de backbone
               para tipos de cambio MISMO_SWITCH, MISMO_PE_MISMO_ANILLO */
            if($strTipoCambio == "MISMO_SWITCH" || $strTipoCambio == "MISMO_PE_MISMO_ANILLO")
            {
                //eliminar enlace 
                $objEnlaceAnt = $this->emInfraestructura
                                     ->getRepository('schemaBundle:InfoEnlace')
                                     ->findOneBy(array("interfaceElementoIniId" => $objIdInterfaceOutAnt->getId(),
                                                       "interfaceElementoFinId" => $objIdInterfaceOut->getId(),
                                                       "estado"                 => "Activo"));
                if ($objEnlaceAnt)
                {
                    $objEnlaceAnt->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objEnlaceAnt);
                    $this->emInfraestructura->flush();
                }

                //enlace entre puerto casette - roseta
                $objEnlaceNew = new InfoEnlace();
                $objEnlaceNew->setInterfaceElementoIniId($objIdInterfaceOutNew);
                $objEnlaceNew->setInterfaceElementoFinId($objIdInterfaceOut);
                $objEnlaceNew->setEstado("Activo");
                $objEnlaceNew->setUsrCreacion($strUsrCreacion);
                $objEnlaceNew->setTipoMedioId($objTipoMedio);
                $objEnlaceNew->setTipoEnlace("PRINCIPAL");
                $objEnlaceNew->setFeCreacion(new \DateTime('now'));
                $objEnlaceNew->setIpCreacion($strIpCreacion);
                $this->emInfraestructura->persist($objEnlaceNew);

                $objIdInterfaceOutNew->setEstado("connected");
                $this->emInfraestructura->persist($objIdInterfaceOutNew);
                $this->emInfraestructura->flush();
            }
            
            //grabar detalles tecnicos en la solicitud - TIPO_CAMBIO_ULTIMA_MILLA
            $objCaracTipoCambioUM  = $this->emComercial
                                          ->getRepository('schemaBundle:AdmiCaracteristica')
                                          ->findOneBy(array('descripcionCaracteristica' => 'TIPO_CAMBIO_ULTIMA_MILLA'));
            $this->utilService->validaObjeto($objCaracTipoCambioUM, 
                                             "Información incompleta al registrar caracteristica de solicitud TIPO_CAMBIO_ULTIMA_MILLA, ".
                                             "favor notificar a Sistemas.");
            
            $arrayParametrosTipoCambioUM = array(
                                                 'objDetalleSolicitudId' => $objSolicitud,
                                                 'objCaracteristica'     => $objCaracTipoCambioUM,
                                                 'estado'                => $strEstadoSolicitud,
                                                 'valor'                 => $strTipoCambio,
                                                 'usrCreacion'           => $strUsrCreacion
                                                );
            
            $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTipoCambioUM);
                        
            //grabar detalles tecnicos en la solicitud - ELEMENTO_ID
            $objCaracElemento  = $this->emComercial
                                      ->getRepository('schemaBundle:AdmiCaracteristica')
                                      ->findOneBy(array('descripcionCaracteristica' => 'ELEMENTO_ID'));
            $this->utilService->validaObjeto($objCaracElemento,
                                             "Información incompleta al registrar caracteristica de solicitud ELEMENTO_ID, ".
                                             "favor notificar a Sistemas.");
            
            $arrayParametrosTecElemento = array(
                                                'objDetalleSolicitudId' => $objSolicitud,
                                                'objCaracteristica'     => $objCaracElemento,
                                                'estado'                => $strEstadoSolicitud,
                                                'valor'                 => $intIdSwitch,
                                                'usrCreacion'           => $strUsrCreacion
                                               );  
            $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecElemento);

            //grabar detalles tecnicos en la solicitud - INTERFACE_ELEMENTO_ID
            $objCaracInterfaceElemento  = $this->emComercial
                                               ->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneBy(array('descripcionCaracteristica' => 'INTERFACE_ELEMENTO_ID'));
            $this->utilService->validaObjeto($objCaracInterfaceElemento,
                                             "Información incompleta al registrar caracteristica de solicitud INTERFACE_ELEMENTO_ID, ".
                                             "favor notificar a Sistemas.");
            
            $arrayParametrosTecInterfaceElemento = array(
                                                         'objDetalleSolicitudId' => $objSolicitud,
                                                         'objCaracteristica'     => $objCaracInterfaceElemento,
                                                         'estado'                => $strEstadoSolicitud,
                                                         'valor'                 => $intIdInterface,
                                                         'usrCreacion'           => $strUsrCreacion
                                                        );
            $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecInterfaceElemento);

            //grabar detalles tecnicos en la solicitud - ELEMENTO_CONECTOR_ID
            $objCaracElementoConector  = $this->emComercial
                                              ->getRepository('schemaBundle:AdmiCaracteristica')
                                              ->findOneBy(array('descripcionCaracteristica' => 'ELEMENTO_CONECTOR_ID'));
            $this->utilService->validaObjeto($objCaracElementoConector,
                                             "Información incompleta al registrar caracteristica de solicitud ELEMENTO_CONECTOR_ID, ".
                                             "favor notificar a Sistemas.");
            
            $arrayParametrosTecElementoConector = array(
                                                        'objDetalleSolicitudId' => $objSolicitud,
                                                        'objCaracteristica'     => $objCaracElementoConector,
                                                        'estado'                => $strEstadoSolicitud,
                                                        'valor'                 => $intIdRadioBb,
                                                        'usrCreacion'           => $strUsrCreacion
                                                       );
            $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecElementoConector);
            //almacenamiento de información adicional para servicios con productos L3MPLS
            if($objProducto->getNombreTecnico()=="L3MPLS" || $objProducto->getNombreTecnico()=="L3MPLS SDWAN")
            {
                //Se obtienen datos anteriores de vlan vrf y protocolo
                $objSolCaracVlan        = $this->sevicioTecnicoService
                                               ->getServicioProductoCaracteristica($objServicio, "VLAN",$objProducto );
                $this->utilService->validaObjeto($objSolCaracVlan,
                                                 "El servicio no tiene registrada la caracteristica VLAN, favor notificar a Sistemas.");
                
                $objPerEmpRolCarVlan    = $this->emComercial
                                               ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                               ->find($objSolCaracVlan->getValor());
                $this->utilService->validaObjeto($objPerEmpRolCarVlan,
                                                 "No existe información de la caracteristica de VLAN reservarda para el servicio, ".
                                                 "favor notificar a Sistemas.");
                
                $objDetalleElementoVlan = $this->emInfraestructura
                                               ->getRepository('schemaBundle:InfoDetalleElemento')
                                               ->find($objPerEmpRolCarVlan->getValor());
                $this->utilService->validaObjeto($objDetalleElementoVlan,
                                                 "Información incompleta al registrar caracteristica de solicitud VLAN, ".
                                                 "favor notificar a Sistemas.");
                

                //obtener la vrf 
                $objSolCaracVrf     = $this->sevicioTecnicoService
                                           ->getServicioProductoCaracteristica($objServicio, "VRF", $objProducto);
                $this->utilService->validaObjeto($objSolCaracVrf,
                                                 "El servicio no tiene registrada la caracteristica VRF, favor notificar a Sistemas.");
                
                $objPerEmpRolCarVrf = $this->emComercial
                                           ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                           ->find($objSolCaracVrf->getValor());
                $this->utilService->validaObjeto($objPerEmpRolCarVrf,
                                                 "No existe información de la caracteristica de VRF reservarda para el servicio, ".
                                                 "favor notificar a Sistemas.");
                
                //obtener el protocolo 
                $objSolCaracProtocolo = $this->sevicioTecnicoService
                                             ->getServicioProductoCaracteristica($objServicio, "PROTOCOLO_ENRUTAMIENTO",$objProducto);
                
                
                //grabar detalles tecnicos en la solicitud - VLAN
                $objCaracVlan  = $this->emComercial
                                      ->getRepository('schemaBundle:AdmiCaracteristica')
                                      ->findOneBy(array('descripcionCaracteristica' => 'VLAN'));
                $this->utilService->validaObjeto($objCaracVlan,
                                                 "Información incompleta al registrar caracteristica de solicitud VLAN, favor notificar a Sistemas.");
                
                $arrayParametrosTecVlan = array(
                                                'objDetalleSolicitudId' => $objSolicitud,
                                                'objCaracteristica'     => $objCaracVlan,
                                                'estado'                => $strEstadoSolicitud,
                                                'valor'                 => $objDetalleElementoVlan->getDetalleValor(),
                                                'usrCreacion'           => $strUsrCreacion
                                               );
                $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecVlan);
                
                //grabar detalles tecnicos en la solicitud - VRF
                $objCaracVrf  = $this->emComercial
                                     ->getRepository('schemaBundle:AdmiCaracteristica')
                                     ->findOneBy(array('descripcionCaracteristica' => 'VRF'));
                $this->utilService->validaObjeto($objCaracVrf,
                                                 "Información incompleta al registrar caracteristica de solicitud VRF, favor notificar a Sistemas.");
               
                $arrayParametrosTecVrf = array(
                                               'objDetalleSolicitudId' => $objSolicitud,
                                               'objCaracteristica'     => $objCaracVrf,
                                               'estado'                => $strEstadoSolicitud,
                                               'valor'                 => $objPerEmpRolCarVrf->getValor(),
                                               'usrCreacion'           => $strUsrCreacion
                                              );
                $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecVrf);
                
                //grabar detalles tecnicos en la solicitud - PROTOCOLO_ENRUTAMIENTO
                $objCaracProtocolo  = $this->emComercial
                                           ->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->findOneBy(array('descripcionCaracteristica' => 'PROTOCOLO_ENRUTAMIENTO'));
                $this->utilService->validaObjeto($objCaracProtocolo,
                                                 "Información incompleta al registrar caracteristica de solicitud PROTOCOLO ENRUTAMIENTO, ".
                                                 "favor notificar a Sistemas.");
                
                $arrayParametrosTecProtocolo = array(
                                                     'objDetalleSolicitudId' => $objSolicitud,
                                                     'objCaracteristica'     => $objCaracProtocolo,
                                                     'estado'                => $strEstadoSolicitud,
                                                     'valor'                 => $objSolCaracProtocolo->getValor(),
                                                     'usrCreacion'           => $strUsrCreacion
                                                    );
                $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecProtocolo);
            }
            else
            {
                //Se obtienen datos anteriores de vlan
                $objSolCaracVlan        = $this->sevicioTecnicoService
                                               ->getServicioProductoCaracteristica($objServicio, 
                                                                                   "VLAN",
                                                                                   $objProducto );
                $this->utilService->validaObjeto($objSolCaracVlan,
                                                 "El servicio no tiene registrada la caracteristica VLAN, ".
                                                 "favor notificar a Sistemas.");
               
                $objDetalleElementoVlan = $this->emInfraestructura
                                               ->getRepository('schemaBundle:InfoDetalleElemento')
                                               ->find($objSolCaracVlan->getValor());
                $this->utilService->validaObjeto($objDetalleElementoVlan, 
                                                 "Información incompleta al registrar caracteristica de solicitud VLAN, ".
                                                 "favor notificar a Sistemas.");
                                                    
                //grabar detalles tecnicos en la solicitud - VLAN
                $objCaracVlan  = $this->emComercial
                                      ->getRepository('schemaBundle:AdmiCaracteristica')
                                      ->findOneBy(array('descripcionCaracteristica' => 'VLAN'));
                $this->utilService->validaObjeto($objCaracVlan,
                                                 "Información incompleta al registrar caracteristica de solicitud VLAN, ".
                                                 "favor notificar a Sistemas.");
                
                $arrayParametrosTecVlan = array(
                                                'objDetalleSolicitudId' => $objSolicitud,
                                                'objCaracteristica'     => $objCaracVlan,
                                                'estado'                => $strEstadoSolicitud,
                                                'valor'                 => $objDetalleElementoVlan->getDetalleValor(),
                                                'usrCreacion'           => $strUsrCreacion
                                               );
                $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecVlan);
            
            }
            
            //actualizar estado de la solicitud
            $objSolicitud->setEstado($strEstadoSolicitud);
            $this->emComercial->persist($objSolicitud);
            $this->emComercial->flush();
            
            //agregar historial a la solicitud
            $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
            $objDetalleSolicitudHistorial->setDetalleSolicitudId($objSolicitud);
            $objDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
            $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
            $objDetalleSolicitudHistorial->setUsrCreacion($strUsrCreacion);
            $objDetalleSolicitudHistorial->setEstado($strEstadoSolicitud);
            $this->emComercial->persist($objDetalleSolicitudHistorial);
            $this->emComercial->flush();

            //agregar historial en el servicio
            $strObsServicioYSolicitud .= "Se <b>$strAccionFactibilidad</b> la Factibilidad por Cambio de Ultima Milla Radio<br/>";
            $strObsServicioYSolicitud .= "Tipo de Cambio    : $strTipoCambio<br/>";    
            $strObsServicioYSolicitud .= "<b>Datos Anteriores</b><br/>";
            $strObsServicioYSolicitud .= "Switch Anterior   : ".$objElementoSwAnt->getNombreElemento()."<br/>";
            $strObsServicioYSolicitud .= "Puerto Anterior   : ".$objInterfaceAnt->getNombreInterfaceElemento()."<br/>";
            $strObsServicioYSolicitud .= "Radio BackBone Anterior : ".$strConectorNombre."<br/>";            
            $strObsServicioYSolicitud .= "<b>Datos Nuevos</b><br/>";
            $strObsServicioYSolicitud .= "Switch Nuevo      : ".$objElementoSwNue->getNombreElemento()."<br/>";
            $strObsServicioYSolicitud .= "Puerto Nuevo      : ".$objInterfaceNue->getNombreInterfaceElemento()."<br/>";
            $strObsServicioYSolicitud .= "Radio BackBone Nuevo    : ".$strConectorNombreNuevo."<br/>";                
            
            //Se crea historial de la edicion de la Factibilidad para cambio de UM
            $objInfoHistorial = new InfoServicioHistorial();
            $objInfoHistorial->setServicioId($objServicio);
            $objInfoHistorial->setEstado($objServicio->getEstado());
            $objInfoHistorial->setUsrCreacion($strUsrCreacion);
            $objInfoHistorial->setFeCreacion(new \DateTime('now'));
            $objInfoHistorial->setIpCreacion($strIpCreacion);
            $objInfoHistorial->setObservacion($strObsServicioYSolicitud);
            $this->emComercial->persist($objInfoHistorial);
            $this->emComercial->flush();
            
            $strMensaje = "Factibilidad de Cambio de ultima milla generada Correctamente.";
        } 
        catch (\Exception $ex) 
        {
            $strMensaje = "ERROR : ".$ex->getMessage();
        }
        
        $respuesta->setContent($strMensaje);
        return $respuesta;
    }
    
    /**
     * Metodo que Genera la factibilidad y la data necesaria para asignacion de recursos/ejecucion para UTP y FIBRA DIRECTO
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 27/07/2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - Soportar cambio de Ultima Milla para Servicios que compartan la misma línea de Backbone
     * @since 09/08/2018
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 15-04-2021 - Se abre la programacion tambien para productos L3MPLS SDWAN
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.3 01-04-2022 - Se agrega validacion de objetos nulos en CambioUltimaMilla
     *
     * @param Array $arrayParametros [ intIdSolicitud       Identificador de la solicitud de cambion UM
     *                                 intIdSwitchNew       Identificador del Switch a utilizar en la factibilidad
     *                                 intIdInterfaceNew    Identificador de la interface del switch a utilizar en la factibilidad
     *                                 strTipoCambio        Cadena de caracteres que indica el tipo de cambio de UM a realizar 
     *                                 strUsrCreacion       Cadena de caracteres que indica el usuario de creación
     *                                 strIpCreacion        Cadena de caracteres que indica la ip de creación 
     *                               ]
     *      
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generarFactibilidadUtpFODirecto($arrayParametros)
    {
        $respuesta         = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $intIdSolicitud    = $arrayParametros['intIdSolicitud'];
        $intIdSwitchNew    = $arrayParametros['intIdSwitchNew'];
        $intIdInterfaceNew = $arrayParametros['intIdInterfaceNew'];    
        $strTipoCambio     = $arrayParametros['strTipoCambio'];                           
        $strUsrCreacion    = $arrayParametros['strUsrCreacion'];
        $strIpCreacion     = $arrayParametros['strIpCreacion'];
        
        $strEstadoSolicitud       = "";
        $strAccionFactibilidad    = "";       
        $strObsServicioYSolicitud = "";
        
        try
        {              
            $objSolicitud       = $this->emComercial
                                       ->getRepository("schemaBundle:InfoDetalleSolicitud")
                                       ->find($intIdSolicitud);
            $objServicio        = $objSolicitud->getServicioId();
            $objProducto        = $objServicio->getProductoId();
            $objServicioTecnico = $this->emComercial
                                       ->getRepository("schemaBundle:InfoServicioTecnico")
                                       ->findOneByServicioId($objServicio->getId());
            $objTipoMedio       = $this->emInfraestructura
                                       ->getRepository('schemaBundle:AdmiTipoMedio')
                                       ->find($objServicioTecnico->getUltimaMillaId());            
            
            //Informacion anterior
            $intIdSwitch           = $objServicioTecnico->getElementoId();
            $intIdInterface        = $objServicioTecnico->getInterfaceElementoId();                       

            //Obteniendo la informacion de ultima milla anterior
            $objElementoSwAnt   = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intIdSwitch);
            $objInterfaceAnt    = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")->find($intIdInterface);
            
            //Obteniendo la informacion de ultima milla nueva              
            $objElementoSwNuevo = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intIdSwitchNew);
            $objInterfaceNuevo  = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")->find($intIdInterfaceNew);
                            
            //Generando nueva factibilidad
            if($strTipoCambio == "MISMO_SWITCH" || $strTipoCambio == "MISMO_PE_MISMO_ANILLO")
            {
                $strAccionFactibilidad = "Actualizó";
                $strEstadoSolicitud = "Asignada";
            
                $objServicioTecnico->setElementoId($intIdSwitchNew);
                $objServicioTecnico->setInterfaceElementoId($intIdInterfaceNew);                
                $this->emComercial->persist($objServicioTecnico);
                $this->emComercial->flush();                                             
            }
            else
            {
                $strAccionFactibilidad = "generó";
                $strEstadoSolicitud    = "AsignadoTarea";
                $intIdSwitch           = $intIdSwitchNew;
                $intIdInterface        = $intIdInterfaceNew;
            }
            
            //Reajustando enlaces
            if($strTipoCambio == "MISMO_SWITCH" || $strTipoCambio == "MISMO_PE_MISMO_ANILLO")
            {                
                //eliminar enlace 
                $objEnlaceAnt = $this->emInfraestructura
                                     ->getRepository('schemaBundle:InfoEnlace')
                                     ->findOneBy(array("interfaceElementoIniId" => $intIdInterface,
                                                       "estado"                 => "Activo"));

                if(is_object($objEnlaceAnt))
                {
                    $objEnlaceAnt->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objEnlaceAnt);
                    $this->emInfraestructura->flush();

                    $objInterfaceElementoConectorNew = $this->emInfraestructura
                                                            ->getRepository("schemaBundle:InfoInterfaceElemento")
                                                            ->find($intIdInterfaceNew);


                    //enlace entre puerto SWITCH - ROSETA/CPE
                    $objEnlaceNew = new InfoEnlace();
                    $objEnlaceNew->setInterfaceElementoIniId($objInterfaceElementoConectorNew); //NUEVO PUERTO DEL SW
                    $objEnlaceNew->setInterfaceElementoFinId($objEnlaceAnt->getInterfaceElementoFinId()); //ROSETA CLIENTE
                    $objEnlaceNew->setEstado("Activo");
                    $objEnlaceNew->setUsrCreacion($strUsrCreacion);
                    $objEnlaceNew->setTipoMedioId($objTipoMedio);
                    $objEnlaceNew->setTipoEnlace("PRINCIPAL");
                    $objEnlaceNew->setFeCreacion(new \DateTime('now'));
                    $objEnlaceNew->setIpCreacion($strIpCreacion);
                    $this->emInfraestructura->persist($objEnlaceNew);
                }
                
                //actualizar estado del puerto casette
                $objIdInterfaceOutAnt = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                     ->find($intIdInterface);
                $objIdInterfaceOutAnt->setEstado("not connect");
                $this->emInfraestructura->persist($objIdInterfaceOutAnt);
                $this->emInfraestructura->flush();                               
            }
                        
            //Se guarda el tipo de Cambio de Ultima a Milla a realizar
            $objCaracTipoCambioUM  = $this->emComercial
                                          ->getRepository('schemaBundle:AdmiCaracteristica')
                                          ->findOneBy(array('descripcionCaracteristica' => 'TIPO_CAMBIO_ULTIMA_MILLA'));
            
            $arrayParametrosTipoCambioUM = array(
                                                 'objDetalleSolicitudId' => $objSolicitud,
                                                 'objCaracteristica'     => $objCaracTipoCambioUM,
                                                 'estado'                => $strEstadoSolicitud,
                                                 'valor'                 => $strTipoCambio,
                                                 'usrCreacion'           => $strUsrCreacion
                                                );
            
            $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTipoCambioUM);
                        
            //grabar detalles tecnicos en la solicitud - ELEMENTO_ID
            $objCaracElemento  = $this->emComercial
                                      ->getRepository('schemaBundle:AdmiCaracteristica')
                                      ->findOneBy(array('descripcionCaracteristica' => 'ELEMENTO_ID'));
            $arrayParametrosTecElemento = array(
                                                'objDetalleSolicitudId' => $objSolicitud,
                                                'objCaracteristica'     => $objCaracElemento,
                                                'estado'                => $strEstadoSolicitud,
                                                'valor'                 => $intIdSwitch,
                                                'usrCreacion'           => $strUsrCreacion
                                               );  
            $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecElemento);

            //grabar detalles tecnicos en la solicitud - INTERFACE_ELEMENTO_ID
            $objCaracInterfaceElemento  = $this->emComercial
                                               ->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneBy(array('descripcionCaracteristica' => 'INTERFACE_ELEMENTO_ID'));
            $arrayParametrosTecInterfaceElemento = array(
                                                         'objDetalleSolicitudId' => $objSolicitud,
                                                         'objCaracteristica'     => $objCaracInterfaceElemento,
                                                         'estado'                => $strEstadoSolicitud,
                                                         'valor'                 => $intIdInterface,
                                                         'usrCreacion'           => $strUsrCreacion
                                                        );
            $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecInterfaceElemento);
            
            if($objProducto->getNombreTecnico()=="L3MPLS" || $objProducto->getNombreTecnico()=="L3MPLS SDWAN")
            {
                //Se obtienen datos anteriores de vlan vrf y protocolo
                $objSolCaracVlan = $this->sevicioTecnicoService->getServicioProductoCaracteristica($objServicio, "VLAN",$objProducto );

                if(is_object($objSolCaracVlan)) 
                {
                    $objPerEmpRolCarVlan = $this->emComercial
                                            ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                            ->find($objSolCaracVlan->getValor());

                    $objDetalleElementoVlan = $this->emInfraestructura
                                                ->getRepository('schemaBundle:InfoDetalleElemento')
                                                ->find($objPerEmpRolCarVlan->getValor());
                }
                //obtener la vrf 
                $objSolCaracVrf = $this->sevicioTecnicoService
                                    ->getServicioProductoCaracteristica($objServicio, "VRF", $objProducto);

                if(is_object($objSolCaracVrf)) 
                {
                    $objPerEmpRolCarVrf = $this->emComercial
                                        ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                        ->find($objSolCaracVrf->getValor());
                }

                //obtener el protocolo 
                $objSolCaracProtocolo = $this->sevicioTecnicoService
                                            ->getServicioProductoCaracteristica($objServicio, "PROTOCOLO_ENRUTAMIENTO",$objProducto);
                
                
                //grabar detalles tecnicos en la solicitud - VLAN
                $objCaracVlan  = $this->emComercial
                                    ->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'VLAN'));

                if(is_object($objDetalleElementoVlan)) 
                {
                    $arrayParametrosTecVlan = array(
                                                    'objDetalleSolicitudId' => $objSolicitud,
                                                    'objCaracteristica'     => $objCaracVlan,
                                                    'estado'                => $strEstadoSolicitud,
                                                    'valor'                 => $objDetalleElementoVlan->getDetalleValor(),
                                                    'usrCreacion'           => $strUsrCreacion
                                                );
                    $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecVlan);
                }
                
                //grabar detalles tecnicos en la solicitud - VRF
                $objCaracVrf  = $this->emComercial
                                    ->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'VRF'));

                if(is_object($objPerEmpRolCarVrf)) 
                {
                    $arrayParametrosTecVrf = array(
                        'objDetalleSolicitudId' => $objSolicitud,
                        'objCaracteristica'     => $objCaracVrf,
                        'estado'                => $strEstadoSolicitud,
                        'valor'                 => $objPerEmpRolCarVrf->getValor(),
                        'usrCreacion'           => $strUsrCreacion
                    );
                    $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecVrf);
                }
                
                
                //grabar detalles tecnicos en la solicitud - PROTOCOLO_ENRUTAMIENTO
                $objCaracProtocolo  = $this->emComercial
                                        ->getRepository('schemaBundle:AdmiCaracteristica')
                                        ->findOneBy(array('descripcionCaracteristica' => 'PROTOCOLO_ENRUTAMIENTO'));

                if(is_object($objSolCaracProtocolo)) 
                {
                    $arrayParametrosTecProtocolo = array(
                                                        'objDetalleSolicitudId' => $objSolicitud,
                                                        'objCaracteristica'     => $objCaracProtocolo,
                                                        'estado'                => $strEstadoSolicitud,
                                                        'valor'                 => $objSolCaracProtocolo->getValor(),
                                                        'usrCreacion'           => $strUsrCreacion
                                                        );
                    $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecProtocolo);
                }  
            }
            else
            {
                //Se obtienen datos anteriores de vlan
                $objSolCaracVlan = $this->sevicioTecnicoService->getServicioProductoCaracteristica($objServicio, "VLAN",$objProducto );
                
                $objDetalleElementoVlan = $this->emInfraestructura
                                               ->getRepository('schemaBundle:InfoDetalleElemento')
                                               ->find($objSolCaracVlan->getValor());
                                                    
                //grabar detalles tecnicos en la solicitud - VLAN
                $objCaracVlan  = $this->emComercial
                                      ->getRepository('schemaBundle:AdmiCaracteristica')
                                      ->findOneBy(array('descripcionCaracteristica' => 'VLAN'));
                $arrayParametrosTecVlan = array(
                                                'objDetalleSolicitudId' => $objSolicitud,
                                                'objCaracteristica'     => $objCaracVlan,
                                                'estado'                => $strEstadoSolicitud,
                                                'valor'                 => $objDetalleElementoVlan->getDetalleValor(),
                                                'usrCreacion'           => $strUsrCreacion
                                               );
                $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecVlan);
            
            }
            
            $objInterfaceElemento = $this->emInfraestructura
                                         ->getRepository('schemaBundle:InfoInterfaceElemento')
                                         ->find($intIdInterfaceNew);
            
            //Actualizar estado del nuevo pto del switch
            $objInterfaceElemento->setEstado("Factible");
            $this->emInfraestructura->persist($objInterfaceElemento);
            $this->emInfraestructura->flush();
            
            //actualizar estado de la solicitud
            $objSolicitud->setEstado($strEstadoSolicitud);
            $this->emComercial->persist($objSolicitud);
            $this->emComercial->flush();
            
            //agregar historial a la solicitud
            $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
            $objDetalleSolicitudHistorial->setDetalleSolicitudId($objSolicitud);
            $objDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
            $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
            $objDetalleSolicitudHistorial->setUsrCreacion($strUsrCreacion);
            $objDetalleSolicitudHistorial->setEstado($strEstadoSolicitud);
            $this->emComercial->persist($objDetalleSolicitudHistorial);
            $this->emComercial->flush();

            //agregar historial en el servicio
            $strObsServicioYSolicitud .= "Se <b>$strAccionFactibilidad</b> la Factibilidad por Cambio de Ultima Milla<br/>";
            $strObsServicioYSolicitud .= "Tipo de Cambio    : $strTipoCambio<br/>";    
            $strObsServicioYSolicitud .= "<b>Datos Anteriores</b><br/>";
            $strObsServicioYSolicitud .= "Switch Anterior   : ".$objElementoSwAnt->getNombreElemento()."<br/>";
            $strObsServicioYSolicitud .= "Puerto Anterior   : ".$objInterfaceAnt->getNombreInterfaceElemento()."<br/>";                  
            $strObsServicioYSolicitud .= "<b>Datos Nuevos</b><br/>";
            $strObsServicioYSolicitud .= "Switch Nuevo      : ".$objElementoSwNuevo->getNombreElemento()."<br/>";
            $strObsServicioYSolicitud .= "Puerto Nuevo      : ".$objInterfaceNuevo->getNombreInterfaceElemento()."<br/>";                 
            
            //Se crea historial de la edicion de la Factibilidad para cambio de UM
            $objInfoHistorial = new InfoServicioHistorial();
            $objInfoHistorial->setServicioId($objServicio);
            $objInfoHistorial->setEstado($objServicio->getEstado());
            $objInfoHistorial->setUsrCreacion($strUsrCreacion);
            $objInfoHistorial->setFeCreacion(new \DateTime('now'));
            $objInfoHistorial->setIpCreacion($strIpCreacion);
            $objInfoHistorial->setObservacion($strObsServicioYSolicitud);
            $this->emComercial->persist($objInfoHistorial);
            $this->emComercial->flush();
            
            $strMensaje = "Factibilidad de Cambio de ultima milla generada Correctamente.";                        
        } 
        catch (\Exception $ex) 
        {
            $strMensaje = "ERROR : ".$ex->getMessage();
        }
        
        $respuesta->setContent($strMensaje);
        return $respuesta;
    }
    
    /**
     * 
     * Metodo que Genera la factibilidad y la data necesaria para asignacion de recursos/ejecucion
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 24/06/2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 08/07/2016 - Se agrega historial al servicio cuando se edita Factibilidad con mismo switch y mismo pe mismo anillo
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2
     * @since 08-10-2016    Se realiza cambio para que soporte escenario de servicios migrados sin data GIS que son tomados como RUTA en la
     *                      factibilidad
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3
     * @since 13-10-2016    Se realiza ajuste para que soporte cambio de Ultima Milla para servicios migrados sin data GIS que no pasan
     *                      por asignacion de recursos de red
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.4
     * @since 09-12-2016    Se valida generacion de nuevo enlace de cambio de UM para escenarios que se requiera realizar cambio de puerto
     *                      utilizando el mismo HILO
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.5 23-03-2020 - Se guarda el id del cliente y el id de la interface del cliente del servicio técnico en la
     *                           característica del detalle de la solicitud
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 15-04-2021 - Se abre la programacion tambien para productos L3MPLS SDWAN
     *
     * @param Array $arrayParametros [ intIdSolicitud       Identificador de la solicitud de cambion UM
     *                                 intIdSwitchNew       Identificador del Switch a utilizar en la factibilidad
     *                                 intIdInterfaceNew    Identificador de la interface del switch a utilizar en la factibilidad
     *                                 intIdCajaNew         Identificador de la caja a utilizar en la factibilidad
     *                                 intIdCassetteNew     Identificador del cassette a utilizar en la factibilidad
     *                                 intIdInterfaceOutNew Identificador de la interface del cassette a utilizar en la factibilidad
     *                                 strEmpresaCod        Cadena de caracteres que indica el codigo de la empresa en sesión
     *                                 strTipoCambio        Cadena de caracteres que indica el tipo de cambio de UM a realizar 
     *                                 strUsrCreacion       Cadena de caracteres que indica el usuario de creación
     *                                 strIpCreacion        Cadena de caracteres que indica la ip de creación 
     *                               ]
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generarFactibilidadUM($arrayParametros)
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $intIdSolicitud       = $arrayParametros['intIdSolicitud'];
        $intIdSwitchNew       = $arrayParametros['intIdSwitchNew'];
        $intIdInterfaceNew    = $arrayParametros['intIdInterfaceNew'];
        $intIdCajaNew         = $arrayParametros['intIdCajaNew'];
        $intIdCassetteNew     = $arrayParametros['intIdCassetteNew'];
        $intIdInterfaceOutNew = $arrayParametros['intIdInterfaceOutNew'];
        $strTipoCambio        = $arrayParametros['strTipoCambio'];      
        $strEmpresaCod        = $arrayParametros['strEmpresaCod'];
        $strUsrCreacion       = $arrayParametros['strUsrCreacion'];   
        $strIpCreacion        = $arrayParametros['strIpCreacion'];   
        
        $strEstadoSolicitud       = "";
        $strAccionFactibilidad    = "";
        $strObsServicioYSolicitud = "";
        
        $strContenedorNombre      = "N/A";
        $strConectorNombre        = "N/A";
        $strContenedorNombreNuevo = "N/A";
        $strConectorNombreNuevo   = "N/A";
        
        try
        {  
            $objSolicitud       = $this->emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")->find($intIdSolicitud);
            $objServicio        = $objSolicitud->getServicioId();
            $objProducto        = $objServicio->getProductoId();
            $objServicioTecnico = $this->emComercial->getRepository("schemaBundle:InfoServicioTecnico")->findOneByServicioId($objServicio->getId());
            $objTipoMedio       = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')->find($objServicioTecnico->getUltimaMillaId());
            $strUltimaMilla     = $objTipoMedio->getNombreTipoMedio();                        
            
            $intIdSwitch        = $objServicioTecnico->getElementoId();
            $intIdInterface     = $objServicioTecnico->getInterfaceElementoId();
            $intIdCaja          = $objServicioTecnico->getElementoContenedorId();
            $intIdCassette      = $objServicioTecnico->getElementoConectorId();
            $intIdInterfaceOut  = $objServicioTecnico->getInterfaceElementoConectorId();
            
            //se valida existencia de enlaces actuales
            $intElementoClienteId  = $objServicioTecnico->getElementoClienteId();
            $intInterfaceClienteId = $objServicioTecnico->getInterfaceElementoClienteId();
            $intElementoConectorId = $objServicioTecnico->getElementoConectorId();
            $intIdInterfaceOutAnt  = $objServicioTecnico->getInterfaceElementoConectorId();
                
            //Obteniendo la informacion de ultima milla anterior
            $objElementoSwAnt   = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intIdSwitch);
            $objInterfaceAnt    = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")->find($intIdInterface);
            
            if($intIdCaja>0)
            {    
                $objElementoCajaAnt = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intIdCaja);
                if($objElementoCajaAnt)
                {
                    $strContenedorNombre = $objElementoCajaAnt->getNombreElemento();
                }
            }
            if($intIdCassette>0)
            {
                $objElementoCassetteAnt = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intIdCassette);
                if($objElementoCassetteAnt)
                {
                    $strConectorNombre = $objElementoCassetteAnt->getNombreElemento();
                }
            }
            
            //Obteniendo la informacion de ultima milla nueva              
            $objElementoSwNue = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intIdSwitchNew);
            $objInterfaceNue  = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")->find($intIdInterfaceNew);
                            
            $objElementoCajaNue = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intIdCajaNew);
            if($objElementoCajaNue)
            {
                $strContenedorNombreNuevo = $objElementoCajaNue->getNombreElemento();
            }
            
            $objElementoCassetteNue = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intIdCassetteNew);
            if($objElementoCassetteNue)
            {
                $strConectorNombreNuevo = $objElementoCassetteNue->getNombreElemento();
            }
            
            //Generando nueva factibilidad
            if($strTipoCambio == "MISMO_SWITCH" || $strTipoCambio == "MISMO_PE_MISMO_ANILLO")
            {
                $strAccionFactibilidad = "Actualizó";
                $strEstadoSolicitud = "Asignada";
            
                $objServicioTecnico->setElementoId($intIdSwitchNew);
                $objServicioTecnico->setInterfaceElementoId($intIdInterfaceNew);
                $objServicioTecnico->setElementoContenedorId($intIdCajaNew);
                $objServicioTecnico->setElementoConectorId($intIdCassetteNew);
                $objServicioTecnico->setInterfaceElementoConectorId($intIdInterfaceOutNew);
                
                $this->emComercial->persist($objServicioTecnico);
                $this->emComercial->flush();
                                             
            }
            else
            {
                $strAccionFactibilidad = "generó";
                $strEstadoSolicitud    = "AsignadoTarea";
                $intIdSwitch           = $intIdSwitchNew;
                $intIdInterface        = $intIdInterfaceNew;
                $intIdCaja             = $intIdCajaNew;
                $intIdCassette         = $intIdCassetteNew;
                $intIdInterfaceOut     = $intIdInterfaceOutNew;
            }                       
            
            if($intElementoClienteId)
            {
                $objElemento        = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intElementoClienteId);
                $strTipoElementoCli = $objElemento->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
            }
            
            //Si no existe informacion de GIS ademas es fibra ruta y elemento cliente no es roseta se completa la data de bb de cliente
            if($strUltimaMilla == 'Fibra Optica' && !$intElementoConectorId && ($intElementoClienteId && $strTipoElementoCli!='ROSETA'))
            {                
                $objPunto       = $objServicio->getPuntoId();
                $objUltimaMilla = $objTipoMedio;
                
                //No se asigna de recursos y pasa directamente a realizar el cambio de UM
                if($strTipoCambio == "MISMO_SWITCH" || $strTipoCambio == "MISMO_PE_MISMO_ANILLO")
                {                    
                    $intInterfaceSwRosCas    = $intIdInterfaceOutNew;//Puerto del cassette a enlazar con la roseta ( sin asignacion de recursos )                    
                }
                else
                {                    
                    $intInterfaceSwRosCas    = $intIdInterface; //puerto del switch a enlazar con la roseta ( va a asignacion de recursos )
                }
                                
                //Si no tiene data de GIS y el elemento es CPE se enlaza la roseta directamente con el sw para luego ser regularizado
                //Si es no requiere realizar asignacion de recursos de Red directamente conecta el OUT del cassette a la roseta para poder
                //realizar el cambio de UM directamente
                $objInterfaceElementoConector = $this->emInfraestructura
                                                     ->getRepository("schemaBundle:InfoInterfaceElemento")                                                                
                                                     ->find($intInterfaceSwRosCas); 
                //ingresar elemento roseta
                $arrayParametrosRoseta = array(
                                                'nombreElementoCliente'         => "ros-".$objPunto->getLogin(),
                                                'nombreModeloElementoCliente'   => "ROS-1234",
                                                'serieElementoCliente'          => "00000",
                                                'objInterfaceElementoVecinoOut' => $objInterfaceElementoConector,
                                                'objUltimaMilla'                => $objUltimaMilla,
                                                'objServicio'                   => $objServicio,
                                                'intIdEmpresa'                  => $strEmpresaCod,
                                                'usrCreacion'                   => $strUsrCreacion,
                                                'ipCreacion'                    => $strIpCreacion
                                              );
                $objInterfaceElementoClienteInicio = $this->sevicioTecnicoService->ingresarElementoClienteTN($arrayParametrosRoseta,"ROSETA");

                //ingresar elemento transciever
                $arrayParametrosTransceiver = array(
                                                     'nombreElementoCliente'         => "trans-".$objPunto->getLogin(),
                                                     'nombreModeloElementoCliente'   => "TRANSCEIVER TRANS",
                                                     'serieElementoCliente'          => "00000",
                                                     'objInterfaceElementoVecinoOut' => $objInterfaceElementoClienteInicio,
                                                     'objUltimaMilla'                => $objUltimaMilla,
                                                     'objServicio'                   => $objServicio,
                                                     'intIdEmpresa'                  => $strEmpresaCod,
                                                     'usrCreacion'                   => $strUsrCreacion,
                                                     'ipCreacion'                    => $strIpCreacion
                                                   );
                $objInterfaceElementoClienteTransceiver = $this->sevicioTecnicoService->ingresarElementoClienteTN($arrayParametrosTransceiver,"TRANSCEIVER");

                //Se obtiene el CPE
                $objElementoCpe = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intElementoClienteId);

                $objModeloElementoCpe = $this->emInfraestructura
                                             ->getRepository("schemaBundle:AdmiModeloElemento")
                                             ->find($objElementoCpe->getModeloElementoId()->getId());
                
                //ingresar elemento CPE ( Solo Enlace)
                $arrayParametrosCpe = array(
                                            'nombreElementoCliente'         => $objPunto->getLogin(),
                                            'nombreModeloElementoCliente'   => $objModeloElementoCpe->getNombreModeloElemento(),
                                            'serieElementoCliente'          => "00000",
                                            'objInterfaceElementoVecinoOut' => $objInterfaceElementoClienteTransceiver,
                                            'objUltimaMilla'                => $objUltimaMilla,
                                            'objServicio'                   => $objServicio,
                                            'intIdEmpresa'                  => $strEmpresaCod,
                                            'usrCreacion'                   => $strUsrCreacion,
                                            'ipCreacion'                    => $strIpCreacion,
                                            'esFlujoNormal'                 => "NO",
                                            'objElementoCpe'                => $objElementoCpe
                                           );
                //Solo se crea el enlace con el CPE ya que este ya existe como elemento
                $this->sevicioTecnicoService
                     ->ingresarElementoClienteTN($arrayParametrosCpe,
                                                 $objModeloElementoCpe->getTipoElementoId()
                                                                      ->getNombreTipoElemento());

                //guardar cpe en servicio tecnico
                $objServicioTecnico->setElementoClienteId($objInterfaceElementoClienteInicio->getElementoId()->getId());
                $objServicioTecnico->setInterfaceElementoClienteId($objInterfaceElementoClienteInicio->getId());  
                
                //Eliminado enlace entre el sw y el cpe directamente
                //Cuando no pasa por recursos de red ( queda enlazado CASSETTE con ROSETA )
                //Cuando pasa por recursos de red ( queda regularizado y enlazado SW con ROSETA )
                $objEnlaceAnt = $this->emInfraestructura
                                     ->getRepository('schemaBundle:InfoEnlace')
                                     ->findOneBy(array("interfaceElementoIniId" => $intIdInterface, //Enlace entre Sw y Cpe
                                                       "estado"                 => "Activo"));                                                     
                
                $objInterfaceElementoConectorAnterior = $this->emInfraestructura
                                                             ->getRepository("schemaBundle:InfoInterfaceElemento")                                                                
                                                             ->find($intIdInterface);//Puerto del Sw
  
                if(is_object($objEnlaceAnt))
                {                    
                    $objEnlaceAnt->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objEnlaceAnt);
                    $this->emInfraestructura->flush();
                }
                
                if(is_object($objInterfaceElementoConectorAnterior))
                {
                    $objInterfaceElementoConectorAnterior->setEstado('not connect');
                    $this->emInfraestructura->persist($objInterfaceElementoConectorAnterior);
                    $this->emInfraestructura->flush();
                }                                             
            } //Si existe data de GIS y el escenario no pasa por asignacion de recursos queda creado el enlace entre el cassette y la roseta            
            elseif($intElementoConectorId>0 && ($strTipoCambio == "MISMO_SWITCH" || $strTipoCambio == "MISMO_PE_MISMO_ANILLO"))
            {
                if ( $strUltimaMilla == "Fibra Optica")
                {
                    $objInterfaceElementoConectorNew = $this->emInfraestructura
                                                            ->getRepository("schemaBundle:InfoInterfaceElemento")
                                                            ->find($intIdInterfaceOutNew);
                    
                    if (!is_object($objInterfaceElementoConectorNew))
                    {
                        throw new \Exception("No se logro recupera nueva interface del elemento cliente.");
                    }
                    
                    //eliminar enlace ( si es RUTA -> out del cassette si es DIRECTO -> puerto del SW )
                    $objEnlaceAnt = $this->emInfraestructura
                                         ->getRepository('schemaBundle:InfoEnlace')
                                         ->findOneBy(array("interfaceElementoIniId" => $intIdInterfaceOutAnt,
                                                           "estado"                 => "Activo"));
                    //se consulta enlace a crear
                    $objEnlaceACrear = $this->emInfraestructura
                                            ->getRepository('schemaBundle:InfoEnlace')
                                            ->findOneBy(array("interfaceElementoIniId" => $objInterfaceElementoConectorNew->getId(),
                                                              "estado"                 => "Activo"));
                    /* se agrega validación para solo crear enlace en caso de que exista un enlace anterior activo
                       y que el nuevo enlace a crear no exista */
                    
                    if ((is_object($objEnlaceAnt) && !is_object($objEnlaceACrear)) ||
                        ($intIdInterfaceOutAnt == $objInterfaceElementoConectorNew->getId()))
                    {
                        $objInterfaceFin = null;
                        
                        //Si el enlace no existe Activo ( eliminado por el usuario ) no realiza actualizacion, por tanto se obtiene la
                        //interface de la roseta directamente de la información técnica del cliente
                        if(is_object($objEnlaceAnt))
                        {
                            $objInterfaceFin = $objEnlaceAnt->getInterfaceElementoFinId();
                            
                            if($objEnlaceAnt->getEstado() == 'Activo')
                            {
                                $objEnlaceAnt->setEstado("Eliminado");
                                $this->emInfraestructura->persist($objEnlaceAnt);
                                $this->emInfraestructura->flush();
                            }
                        }
                        else //Si no existe el enlace anterior Activo ( fue eliminado )
                        {
                            //Se obtiene la interface de la Roseta a conectar con el Cassette
                            $objInterfaceCliente = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                           ->find($objServicioTecnico->getInterfaceElementoClienteId());
                            if(is_object($objInterfaceCliente))
                            {
                                $objInterfaceFin = $objInterfaceCliente;
                            }
                        }
                        
                        if(!is_object($objInterfaceFin))
                        {
                            throw new \Exception("No existe interface Cliente a conectar con equipo de Backbone");
                        }
                        
                        //enlace entre puerto casette - roseta
                        $objEnlaceNew = new InfoEnlace();
                        $objEnlaceNew->setInterfaceElementoIniId($objInterfaceElementoConectorNew);
                        $objEnlaceNew->setInterfaceElementoFinId($objInterfaceFin);
                        $objEnlaceNew->setEstado("Activo");
                        $objEnlaceNew->setUsrCreacion($strUsrCreacion);
                        $objEnlaceNew->setTipoMedioId($objTipoMedio);
                        $objEnlaceNew->setTipoEnlace("PRINCIPAL");
                        $objEnlaceNew->setFeCreacion(new \DateTime('now'));
                        $objEnlaceNew->setIpCreacion($strIpCreacion);
                        $this->emInfraestructura->persist($objEnlaceNew);

                        //actualizar estado del puerto casette
                        $objIdInterfaceOutAnt = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                             ->find($intIdInterfaceOutAnt);
                        $objIdInterfaceOutAnt->setEstado("not connect");
                        $this->emInfraestructura->persist($objIdInterfaceOutAnt);
                        $this->emInfraestructura->flush();
                    }
                    /*se mostrara mensaje de error en caso de:
                     *   - Si no existe enlace anterior y no existe enlace nuevo a crear
                     *   - Si existe enlace anterior y existe enlace nuevo a crear
                      */
                    else if ((!is_object($objEnlaceAnt) && !is_object($objEnlaceACrear)) ||
                             (is_object($objEnlaceAnt) && is_object($objEnlaceACrear)) )
                    {
                        throw new \Exception("Ocurrio un problema al crear enlaces entre equipo de backbone y equipo de cliente");
                    }
                }
            }          
            //fin de validacion de existencia de enlaces actuales

            //Se guarda el tipo de Cambio de Ultima a Milla a realizar
            $objCaracTipoCambioUM  = $this->emComercial
                                          ->getRepository('schemaBundle:AdmiCaracteristica')
                                          ->findOneBy(array('descripcionCaracteristica' => 'TIPO_CAMBIO_ULTIMA_MILLA'));
            
            $arrayParametrosTipoCambioUM = array(
                                                 'objDetalleSolicitudId' => $objSolicitud,
                                                 'objCaracteristica'     => $objCaracTipoCambioUM,
                                                 'estado'                => $strEstadoSolicitud,
                                                 'valor'                 => $strTipoCambio,
                                                 'usrCreacion'           => $strUsrCreacion
                                                );
            
            $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTipoCambioUM);
                        
            //grabar detalles tecnicos en la solicitud - ELEMENTO_ID
            $objCaracElemento  = $this->emComercial
                                      ->getRepository('schemaBundle:AdmiCaracteristica')
                                      ->findOneBy(array('descripcionCaracteristica' => 'ELEMENTO_ID'));
            $arrayParametrosTecElemento = array(
                                                'objDetalleSolicitudId' => $objSolicitud,
                                                'objCaracteristica'     => $objCaracElemento,
                                                'estado'                => $strEstadoSolicitud,
                                                'valor'                 => $intIdSwitch,
                                                'usrCreacion'           => $strUsrCreacion
                                               );  
            $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecElemento);

            //grabar detalles tecnicos en la solicitud - INTERFACE_ELEMENTO_ID
            $objCaracInterfaceElemento  = $this->emComercial
                                               ->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneBy(array('descripcionCaracteristica' => 'INTERFACE_ELEMENTO_ID'));
            $arrayParametrosTecInterfaceElemento = array(
                                                         'objDetalleSolicitudId' => $objSolicitud,
                                                         'objCaracteristica'     => $objCaracInterfaceElemento,
                                                         'estado'                => $strEstadoSolicitud,
                                                         'valor'                 => $intIdInterface,
                                                         'usrCreacion'           => $strUsrCreacion
                                                        );
            $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecInterfaceElemento);

            if(!empty($intElementoClienteId))
            {
                //grabar detalles tecnicos en la solicitud - ELEMENTO_CLIENTE_ID
                $objCaracElementoCliente    = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                   ->findOneBy(array('descripcionCaracteristica' => 'ELEMENTO_CLIENTE_ID'));
                $arrayParametrosTecElementoCliente  = array(
                                                            'objDetalleSolicitudId' => $objSolicitud,
                                                            'objCaracteristica'     => $objCaracElementoCliente,
                                                            'estado'                => $strEstadoSolicitud,
                                                            'valor'                 => $intElementoClienteId,
                                                            'usrCreacion'           => $strUsrCreacion
                                                        );
                $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecElementoCliente);
            }

            if(!empty($intInterfaceClienteId))
            {
                //grabar detalles tecnicos en la solicitud - INTERFACE_ELEMENTO_CLIENTE_ID
                $objCaracInterfaceElementoCliente   = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                   ->findOneBy(array('descripcionCaracteristica' => 'INTERFACE_ELEMENTO_CLIENTE_ID'));
                $arrayParametrosTecIntEleCliente    = array(
                                                            'objDetalleSolicitudId' => $objSolicitud,
                                                            'objCaracteristica'     => $objCaracInterfaceElementoCliente,
                                                            'estado'                => $strEstadoSolicitud,
                                                            'valor'                 => $intInterfaceClienteId,
                                                            'usrCreacion'           => $strUsrCreacion
                                                        );
                $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecIntEleCliente);

            }
            if($intIdCassette>0)
            {
                //grabar detalles tecnicos en la solicitud - ELEMENTO_CONECTOR_ID
                $objCaracElementoConector  = $this->emComercial
                                                  ->getRepository('schemaBundle:AdmiCaracteristica')
                                                  ->findOneBy(array('descripcionCaracteristica' => 'ELEMENTO_CONECTOR_ID'));
                $arrayParametrosTecElementoConector = array(
                                                            'objDetalleSolicitudId' => $objSolicitud,
                                                            'objCaracteristica'     => $objCaracElementoConector,
                                                            'estado'                => $strEstadoSolicitud,
                                                            'valor'                 => $intIdCassette,
                                                            'usrCreacion'           => $strUsrCreacion
                                                           );
                $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecElementoConector);
            }
            if($intIdInterfaceOut>0)
            {
                //grabar detalles tecnicos en la solicitud - INTERFACE_ELEMENTO_CONECTOR_ID
                $objCaracInterfaceElementoConector  = $this->emComercial
                                                           ->getRepository('schemaBundle:AdmiCaracteristica')
                                                           ->findOneBy(array('descripcionCaracteristica' => 'INTERFACE_ELEMENTO_CONECTOR_ID'));
                $arrayParametrosTecInterfaceElementoConector = array(
                                                                     'objDetalleSolicitudId' => $objSolicitud,
                                                                     'objCaracteristica'     => $objCaracInterfaceElementoConector,
                                                                     'estado'                => $strEstadoSolicitud,
                                                                     'valor'                 => $intIdInterfaceOut,
                                                                     'usrCreacion'           => $strUsrCreacion
                                                                    );
                $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecInterfaceElementoConector);
            }
            if($intIdCaja>0)
            {
                //grabar detalles tecnicos en la solicitud - ELEMENTO_CONTENEDOR_ID
                $objCaracElementoContenedor  = $this->emComercial
                                                    ->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array('descripcionCaracteristica' => 'ELEMENTO_CONTENEDOR_ID'));
                $arrayParametrosTecElementoContenedor = array(
                                                              'objDetalleSolicitudId' => $objSolicitud,
                                                              'objCaracteristica'     => $objCaracElementoContenedor,
                                                              'estado'                => $strEstadoSolicitud,
                                                              'valor'                 => $intIdCaja,
                                                              'usrCreacion'           => $strUsrCreacion
                                                             );
                $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecElementoContenedor);
            }    
            if($objProducto->getNombreTecnico()=="L3MPLS" || $objProducto->getNombreTecnico()=="L3MPLS SDWAN")
            {
                //Se obtienen datos anteriores de vlan vrf y protocolo
                $objSolCaracVlan = $this->sevicioTecnicoService->getServicioProductoCaracteristica($objServicio, "VLAN",$objProducto );

                $objPerEmpRolCarVlan = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->find($objSolCaracVlan->getValor());

                $objDetalleElementoVlan = $this->emInfraestructura
                                               ->getRepository('schemaBundle:InfoDetalleElemento')
                                               ->find($objPerEmpRolCarVlan->getValor());

                //obtener la vrf 
                $objSolCaracVrf = $this->sevicioTecnicoService->getServicioProductoCaracteristica($objServicio, "VRF", $objProducto);

                $objPerEmpRolCarVrf = $this->emComercial
                                           ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                           ->find($objSolCaracVrf->getValor());

                //obtener el protocolo 
                $objSolCaracProtocolo = $this->sevicioTecnicoService->getServicioProductoCaracteristica($objServicio, "PROTOCOLO_ENRUTAMIENTO",$objProducto);
                
                
                //grabar detalles tecnicos en la solicitud - VLAN
                $objCaracVlan  = $this->emComercial
                                      ->getRepository('schemaBundle:AdmiCaracteristica')
                                      ->findOneBy(array('descripcionCaracteristica' => 'VLAN'));
                $arrayParametrosTecVlan = array(
                                                'objDetalleSolicitudId' => $objSolicitud,
                                                'objCaracteristica'     => $objCaracVlan,
                                                'estado'                => $strEstadoSolicitud,
                                                'valor'                 => $objDetalleElementoVlan->getDetalleValor(),
                                                'usrCreacion'           => $strUsrCreacion
                                               );
                $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecVlan);
                
                //grabar detalles tecnicos en la solicitud - VRF
                $objCaracVrf  = $this->emComercial
                                     ->getRepository('schemaBundle:AdmiCaracteristica')
                                     ->findOneBy(array('descripcionCaracteristica' => 'VRF'));
                $arrayParametrosTecVrf = array(
                                               'objDetalleSolicitudId' => $objSolicitud,
                                               'objCaracteristica'     => $objCaracVrf,
                                               'estado'                => $strEstadoSolicitud,
                                               'valor'                 => $objPerEmpRolCarVrf->getValor(),
                                               'usrCreacion'           => $strUsrCreacion
                                              );
                $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecVrf);
                
                //grabar detalles tecnicos en la solicitud - PROTOCOLO_ENRUTAMIENTO
                $objCaracProtocolo  = $this->emComercial
                                           ->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->findOneBy(array('descripcionCaracteristica' => 'PROTOCOLO_ENRUTAMIENTO'));
                $arrayParametrosTecProtocolo = array(
                                                     'objDetalleSolicitudId' => $objSolicitud,
                                                     'objCaracteristica'     => $objCaracProtocolo,
                                                     'estado'                => $strEstadoSolicitud,
                                                     'valor'                 => $objSolCaracProtocolo->getValor(),
                                                     'usrCreacion'           => $strUsrCreacion
                                                    );
                $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecProtocolo);
            }
            else
            {
                //Se obtienen datos anteriores de vlan
                $objSolCaracVlan = $this->sevicioTecnicoService->getServicioProductoCaracteristica($objServicio, "VLAN",$objProducto );
                
                $objDetalleElementoVlan = $this->emInfraestructura
                                               ->getRepository('schemaBundle:InfoDetalleElemento')
                                               ->find($objSolCaracVlan->getValor());
                                                    
                //grabar detalles tecnicos en la solicitud - VLAN
                $objCaracVlan  = $this->emComercial
                                      ->getRepository('schemaBundle:AdmiCaracteristica')
                                      ->findOneBy(array('descripcionCaracteristica' => 'VLAN'));
                $arrayParametrosTecVlan = array(
                                                'objDetalleSolicitudId' => $objSolicitud,
                                                'objCaracteristica'     => $objCaracVlan,
                                                'estado'                => $strEstadoSolicitud,
                                                'valor'                 => $objDetalleElementoVlan->getDetalleValor(),
                                                'usrCreacion'           => $strUsrCreacion
                                               );
                $this->sevicioTecnicoService->insertarInfoDetalleSolCaract($arrayParametrosTecVlan);
            
            }
            
            $objInterfaceElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($intIdInterfaceOutNew);
            
            //Actualizar estado del nuevo pto del cassette
            $objInterfaceElemento->setEstado("Factible");
            $this->emInfraestructura->persist($objInterfaceElemento);
            $this->emInfraestructura->flush();
            
            //actualizar estado de la solicitud
            $objSolicitud->setEstado($strEstadoSolicitud);
            $this->emComercial->persist($objSolicitud);
            $this->emComercial->flush();
            
            //agregar historial a la solicitud
            $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
            $objDetalleSolicitudHistorial->setDetalleSolicitudId($objSolicitud);
            $objDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
            $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
            $objDetalleSolicitudHistorial->setUsrCreacion($strUsrCreacion);
            $objDetalleSolicitudHistorial->setEstado($strEstadoSolicitud);
            $this->emComercial->persist($objDetalleSolicitudHistorial);
            $this->emComercial->flush();

            //agregar historial en el servicio
            $strObsServicioYSolicitud .= "Se <b>".$strAccionFactibilidad."</b> la Factibilidad por Cambio de Ultima Milla<br/>";
            $strObsServicioYSolicitud .= "Tipo de Cambio    : ".$strTipoCambio."<br/>";    
            $strObsServicioYSolicitud .= "<b>Datos Anteriores</b><br/>";
            $strObsServicioYSolicitud .= "Switch Anterior   : ".$objElementoSwAnt->getNombreElemento()."<br/>";
            $strObsServicioYSolicitud .= "Puerto Anterior   : ".$objInterfaceAnt->getNombreInterfaceElemento()."<br/>";
            $strObsServicioYSolicitud .= "Caja Anterior     : ".$strContenedorNombre."<br/>";
            $strObsServicioYSolicitud .= "Cassette Anterior : ".$strConectorNombre."<br/>";            
            $strObsServicioYSolicitud .= "<b>Datos Nuevos</b><br/>";
            $strObsServicioYSolicitud .= "Switch Nuevo      : ".$objElementoSwNue->getNombreElemento()."<br/>";
            $strObsServicioYSolicitud .= "Puerto Nuevo      : ".$objInterfaceNue->getNombreInterfaceElemento()."<br/>";
            $strObsServicioYSolicitud .= "Caja Nueva        : ".$strContenedorNombreNuevo."<br/>";
            $strObsServicioYSolicitud .= "Cassette Nuevo    : ".$strConectorNombreNuevo."<br/>";                
            
            //Se crea historial de la edicion de la Factibilidad para cambio de UM
            $objInfoHistorial = new InfoServicioHistorial();
            $objInfoHistorial->setServicioId($objServicio);
            $objInfoHistorial->setEstado($objServicio->getEstado());
            $objInfoHistorial->setUsrCreacion($strUsrCreacion);
            $objInfoHistorial->setFeCreacion(new \DateTime('now'));
            $objInfoHistorial->setIpCreacion($strIpCreacion);
            $objInfoHistorial->setObservacion($strObsServicioYSolicitud);
            $this->emComercial->persist($objInfoHistorial);
            $this->emComercial->flush();
            
            $strMensaje = "Factibilidad de Cambio de ultima milla generada Correctamente.";
        } 
        catch (\Exception $ex) 
        {
            $strMensaje = "ERROR : ".$ex->getMessage();
        }
        
        $respuesta->setContent($strMensaje);
        return $respuesta;
    }
   
}
