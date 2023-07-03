<?php

namespace telconet\comercialBundle\Service;

use Doctrine\ORM\EntityManager;
use telconet\schemaBundle\Entity\ReturnResponse;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoPersonaContacto;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\AdmiProductoCaracteristica;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\tecnicoBundle\Service\InfoServicioTecnicoService;
use telconet\schemaBundle\Entity\AdmiCaracteristica;
use telconet\schemaBundle\Entity\AdmiDepartamento;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Entity\InfoTareaCaracteristica;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use Symfony\Component\HttpFoundation\File\File;



class OrquestadorService
{
    private $emGeneral;
    private $emComercial;
    private $emComunicacion;
    private $emSoporte;
    private $emFinanciero;
    private $emInfraestructura;
    private $serviceUtil;
    private $serviceTecnico;
    private $serviceInfoServicio;
    private $serviceUtilidades;
    private $serviceJefesComercial;
    private $servicePunto;
    private $serviceSoporte;
    private $serviceCliente;
    private $servicePersonaFormaContacto;
    private $serviceFoxPremium;
    private $serviceLicenciasKaspersky;
    private $serviceInternetProtegido;
    private $restClientPedidos;
    private $serviceTelcoCrm;


    /**
     * Documentación para la función 'setDependencies'.
     *
     * Función encargada de setear los entities manager de los esquemas de base de datos.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 11-05-2021
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $objContainer - objeto contenedor
     *
     */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer )
    {
        $this->emComercial    = $objContainer->get('doctrine.orm.telconet_entity_manager');
        $this->emComunicacion = $objContainer->get('doctrine.orm.telconet_comunicacion_entity_manager');
        $this->emSoporte      = $objContainer->get('doctrine.orm.telconet_soporte_entity_manager');
        $this->emGeneral      = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
        $this->emFinanciero   = $objContainer->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->serviceUtil    = $objContainer->get('schema.Util');
        $this->serviceTecnico = $objContainer->get('tecnico.InfoServicioTecnico');
        $this->emInfraestructura     = $objContainer->get('doctrine.orm.telconet_infraestructura_entity_manager');
        $this->serviceInfoServicio   = $objContainer->get('comercial.InfoServicio');
        $this->serviceUtilidades     = $objContainer->get('administracion.Utilidades');
        $this->serviceSoporte          = $objContainer->get('soporte.SoporteService');
        $this->serviceJefesComercial = $objContainer->get('administracion.JefesComercial');
        $this->servicePunto          = $objContainer->get('comercial.InfoPunto');
        $this->serviceCliente        = $objContainer->get('comercial.Cliente');
        $this->servicePersonaFormaContacto = $objContainer->get('comercial.InfopersonaFormaContacto');
        $this->serviceFoxPremium           = $objContainer->get('tecnico.FoxPremium');
        $this->serviceLicenciasKaspersky   = $objContainer->get('tecnico.LicenciasKaspersky');
        $this->serviceInternetProtegido    = $objContainer->get('tecnico.InternetProtegido');
        $this->restClientPedidos    = $objContainer->get('schema.RestClient');
        $this->strMicroServUrl      = $objContainer->getParameter('orquestador_webservice_url');
        $this->serviceTelcoCrm      = $objContainer->get('comercial.ComercialCrm');        


        
    }
    
    
    /**
     * Documentación para el método crearServicio
     *
     * Funcion que permite agregar los servicios de formas masiva por orquestador.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 02-07-2021
     *
     * @param Array $arrayDatosWs[
     *                                  "strUsuario"         => Usuario que realiza la petición.
     *                                  "strCodEmpresa"      => Código de la empresa.
     *                                  "strPrefijoEmpresa"  => Prefijo de la empresa.
     *                                  "strIpCreacion"      => Ip de creación.
     *                                  "arrayPuntos"        => Listado de puntos a los que se le creara el servicio.
     *                                  "arrayDatos"         => Datos para el nuevo servicio.
     *                                  "strTipoOrden"       => Tipo de Orden (N-> nueva). 
     *                               ]
     *
     * @return array $arrayResultado [
     *                                 Servicios   =>  arreglo de los servicios creados con su respectivo punto.
     *                                 error       =>  mensaje de error.
     *                               ]
     */
    public function crearServicio($arrayDatosWs)
    {
        $strUsuario          = ( isset($arrayDatosWs['strUsuario']) && !empty($arrayDatosWs['strUsuario']) )
                                   ? $arrayDatosWs['strUsuario'] : 'TELCOS +';
        $intCodEmpresa       = ( isset($arrayDatosWs['strCodEmpresa']) && !empty($arrayDatosWs['strCodEmpresa']) )
                                   ? $arrayDatosWs['strCodEmpresa'] : 10;
        $strPrefijoEmpresa   = ( isset($arrayDatosWs['strPrefijoEmpresa']) && !empty($arrayDatosWs['strPrefijoEmpresa']) )
                                   ? $arrayDatosWs['strPrefijoEmpresa'] : 'TN';
        $strIpCreacion       = ( isset($arrayDatosWs['strIpCreacion']) && !empty($arrayDatosWs['strIpCreacion']) )
                                   ? $arrayDatosWs['strIpCreacion'] : '127.0.0.1';
        $strInstancia        = ( isset($arrayDatosWs['instanceId']) && !empty($arrayDatosWs['instanceId']) )
                                   ? $arrayDatosWs['instanceId'] : '';
        
        $arrayDatosOrq       = ( isset($arrayDatosWs['nativeValueParameterList']) && !empty($arrayDatosWs['nativeValueParameterList']) )
                                   ? $arrayDatosWs['nativeValueParameterList'] : '';
        $arrayColum          = $this->arrayColumManual($arrayDatosOrq, 'parameterName');
        $intKeyObs              = array_search('observacion', $arrayColum);
        $strObservacion      = ( isset($arrayDatosOrq[$intKeyObs]['nativeTypeValue']) && !empty($arrayDatosOrq[$intKeyObs]['nativeTypeValue']) )
                                    ? $arrayDatosOrq[$intKeyObs]['nativeTypeValue'] : '';
        $intKeyDoc           = array_search('anexo_tecnico', $arrayColum);
        $strUrlDoc           = ( isset($arrayDatosOrq[$intKeyDoc]['nativeTypeValue']) && !empty($arrayDatosOrq[$intKeyDoc]['nativeTypeValue']) )
                                    ? $arrayDatosOrq[$intKeyDoc]['nativeTypeValue'] : '';
        $intKeyDatos         = array_search('json_datos', $arrayColum);
        $arrayDatosStr       = ( isset($arrayDatosOrq[$intKeyDatos]['nativeTypeValue']) && !empty($arrayDatosOrq[$intKeyDatos]['nativeTypeValue']) )
                                    ? $arrayDatosOrq[$intKeyDatos]['nativeTypeValue'] : '';
        $arrayDatos          = json_decode($arrayDatosStr,true);
        $arrayPuntos         = ( isset($arrayDatos['arrayPuntos']) && !empty($arrayDatos['arrayPuntos']) )? $arrayDatos['arrayPuntos'] : '';
        $arrayServicio       = ( isset($arrayDatos['arrayDatos']) && !empty($arrayDatos['arrayDatos']) )? $arrayDatos['arrayDatos'] : '';
        $strTipoOrden        = ( isset($arrayDatosWs['strTipoOrden']) && !empty($arrayDatosWs['strTipoOrden']) )? $arrayDatosWs['strTipoOrden'] : 'N';
        $strError            = "";
        $strEstado           = "OK";        
        $arrayPuntoServicios = array();
        try
        {
            if(empty($arrayPuntos))
            {
                throw new \Exception("No hay Puntos seleccionados, favor verificar");
            }
            //Buscamos los puntos del array
            foreach($arrayPuntos as $arrayPunto)
            {
                $objPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($arrayPunto);
                
                if(is_object($objPunto) && !empty($objPunto))
                {
                    $objPersEmpRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                                                ->find($objPunto->getPersonaEmpresaRolId()->getId());
                    $objRol = $this->emComercial->getRepository('schemaBundle:AdmiRol')
                                                                           ->find($objPunto->getPersonaEmpresaRolId()->getEmpresaRolId()->getRolId());
                    
                    if(is_object($objPersEmpRol) && !empty($objPersEmpRol))
                    {
                        $intOficinaId  = $objPersEmpRol->getOficinaId()->getId();
                        $intIdProducto = $arrayServicio[0]['codigo'];
                        $intFrecuencia = $arrayServicio[0]['frecuencia'];
                        
                        $arrayParametrosCaracteristicas = array('intIdProducto'         => $intIdProducto,
                                                    'strDescCaracteristica' => 'FACTURACION_UNICA',
                                                    'strEstado'             => 'Activo');
                        $strEsFacturacionUnica = $this->serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaracteristicas);

                        $arrayParametrosCaracteristicas = array('intIdProducto'         => $intIdProducto,
                                                                'strDescCaracteristica' => 'RENTA_MENSUAL',
                                                                'strEstado'             => 'Activo');
                        $strEsRentaMensual = $this->serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaracteristicas);

                        $objAdmiProducto   = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->findOneById($intIdProducto);

                        if((!empty($strEsFacturacionUnica) && $strEsFacturacionUnica == "S" )
                            && (empty($strEsRentaMensual) || (!empty($strEsRentaMensual) && $strEsRentaMensual == "N"))
                          )
                        {
                            if($intFrecuencia != "0")
                            {                                                                               
                                $strError = 'No se puede agregar producto '. $objAdmiProducto->getDescripcionProducto().' ya que es de '
                                    . '[FACTURACION_UNICA] y la Frecuencia que debe escoger es [UNICA]';                                   
                            }
                        } 
                        else
                        {   if(!empty($strEsFacturacionUnica) && $strEsFacturacionUnica == "N"  && $intFrecuencia == "0")
                            {
                                $strError = 'No se puede agregar producto '. $objAdmiProducto->getDescripcionProducto(). ' ya que no es de '.
                                                '[FACTURACION_UNICA] no puede escoger Frecuencia [UNICA]';
                            }
                        }
                        if(empty($strError))
                        {
                            $arrayParamsServicio = array(   "codEmpresa"            => $intCodEmpresa,
                                                        "idOficina"             => $intOficinaId,
                                                        "entityPunto"           => $objPunto,
                                                        "entityRol"             => $objRol,
                                                        "usrCreacion"           => $strUsuario,
                                                        "clientIp"              => $strIpCreacion,
                                                        "tipoOrden"             => $strTipoOrden,
                                                        "ultimaMillaId"         => null,
                                                        "servicios"             => $arrayServicio,
                                                        "strPrefijoEmpresa"     => $strPrefijoEmpresa,
                                                        "session"               => null,
                                                        "intIdSolFlujoPP"       =>  0
                                                );
                        $arrayRespuesta = $this->serviceInfoServicio->crearServicio($arrayParamsServicio);

                        }
                    }
                    else
                    {
                        $strError = "No se encuentra Persona Empresa Rol del Punto seleccionado.";
                    }
                    if((!empty($strError) &&  $strError != "")|| !isset($arrayRespuesta['intIdServicio']))
                    {
                        $strEstado = 'Error';
                    }
                    else
                    {
                        $this->serviceTecnico->generarLoginAuxiliar($arrayRespuesta['intIdServicio']);
                        $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayRespuesta['intIdServicio']);

                        $this->emComercial->getConnection()->beginTransaction();
                        $objInfoServicioHistorial = new InfoServicioHistorial();
                        $objInfoServicioHistorial->setServicioId($objServicio);
                        $objInfoServicioHistorial->setObservacion("Servicio creado desde el TelcoCRM");
                        $objInfoServicioHistorial->setEstado($objServicio->getEstado());
                        $objInfoServicioHistorial->setUsrCreacion($strUsuario);
                        $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objInfoServicioHistorial->setIpCreacion($strIpCreacion);
                        $this->emComercial->persist($objInfoServicioHistorial);
                        $this->emComercial->flush(); 
                        
                        $objInfoServicioHistorial = new InfoServicioHistorial();
                        $objInfoServicioHistorial->setServicioId($objServicio);
                        $objInfoServicioHistorial->setObservacion("Servicio creado desde el Orquestador");
                        $objInfoServicioHistorial->setEstado($objServicio->getEstado());
                        $objInfoServicioHistorial->setUsrCreacion($strUsuario);
                        $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objInfoServicioHistorial->setIpCreacion($strIpCreacion);
                        $this->emComercial->persist($objInfoServicioHistorial);
                        $this->emComercial->flush(); 
                        //Agregamos la caracteristica de instancia de Orquestador
                        $objCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                     ->findOneBy(array('descripcionCaracteristica' => 'INSTANCIA_ID_ORQ', 
                                                                                       'tipo'                      => 'COMERCIAL')
                                                                               );
                        if(is_object($objCaracteristica))
                        {
                            $objProdCaractInstancia = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                ->findOneBy(array( 
                                                                                  "productoId"       => $objAdmiProducto->getId(),
                                                                                  "caracteristicaId" => $objCaracteristica->getId(),
                                                                                  "estado"           => "Activo"
                                                                                 )); 
                            
                            if(is_object($objProdCaractInstancia))
                            {
                                $entityServicioProdCaract  = new InfoServicioProdCaract();
                                $entityServicioProdCaract->setServicioId($objServicio->getId());
                                $entityServicioProdCaract->setProductoCaracterisiticaId($objProdCaractInstancia->getId());
                                $entityServicioProdCaract->setValor($strInstancia);
                                $entityServicioProdCaract->setEstado('Activo');
                                $entityServicioProdCaract->setUsrCreacion($strUsuario);
                                $entityServicioProdCaract->setFeCreacion(new \DateTime('now'));
                                $this->emComercial->persist($entityServicioProdCaract);
                                $this->emComercial->flush(); 

                            }
                        }

                        //Agregamos la caracteristica de documento para la tarea
                        $objCaracteristicaDoc = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                     ->findOneBy(array('descripcionCaracteristica' => 'DOC_ANEXO_TECNICO', 
                                                                                       'tipo'                      => 'COMERCIAL')
                                                                               );
                        if(is_object($objCaracteristicaDoc))
                        {
                            $objProdCaractDocumento = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                ->findOneBy(array( 
                                                                                  "productoId"       => $objAdmiProducto->getId(),
                                                                                  "caracteristicaId" => $objCaracteristicaDoc->getId(),
                                                                                  "estado"           => "Activo"
                                                                                 )); 
                            
                            if(is_object($objProdCaractDocumento))
                            {
                                $entityServicioProdCaract  = new InfoServicioProdCaract();
                                $entityServicioProdCaract->setServicioId($objServicio->getId());
                                $entityServicioProdCaract->setProductoCaracterisiticaId($objProdCaractDocumento->getId());
                                $entityServicioProdCaract->setValor($strUrlDoc);
                                $entityServicioProdCaract->setEstado('Activo');
                                $entityServicioProdCaract->setUsrCreacion($strUsuario);
                                $entityServicioProdCaract->setFeCreacion(new \DateTime('now'));
                                $this->emComercial->persist($entityServicioProdCaract);
                                $this->emComercial->flush(); 

                            }
                        }
                        
                        //Agregamos la caracteristica de observacion
                        $objCaracteristicaObs = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                     ->findOneBy(array('descripcionCaracteristica' => 'OBSERVACION_TAREA_SECURITY', 
                                                                                       'tipo'                      => 'COMERCIAL')
                                                                               );
                        if(is_object($objCaracteristicaObs))
                        {
                            $objProdCaractObservacion = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                ->findOneBy(array( 
                                                                                  "productoId"       => $objAdmiProducto->getId(),
                                                                                  "caracteristicaId" => $objCaracteristicaObs->getId(),
                                                                                  "estado"           => "Activo"
                                                                                 )); 
                            
                            if(is_object($objProdCaractObservacion))
                            {
                                $entityServicioProdCaract  = new InfoServicioProdCaract();
                                $entityServicioProdCaract->setServicioId($objServicio->getId());
                                $entityServicioProdCaract->setProductoCaracterisiticaId($objProdCaractObservacion->getId());
                                $entityServicioProdCaract->setValor($strObservacion);
                                $entityServicioProdCaract->setEstado('Activo');
                                $entityServicioProdCaract->setUsrCreacion($strUsuario);
                                $entityServicioProdCaract->setFeCreacion(new \DateTime('now'));
                                $this->emComercial->persist($entityServicioProdCaract);
                                $this->emComercial->flush(); 

                            }
                        }
                        
                    }
                    if ($this->emComercial->getConnection()->isTransactionActive())
                    {
                        $this->emComercial->getConnection()->commit();
                        $this->emComercial->getConnection()->close();
                    }
                    $objServCaractProp = $this->serviceTecnico->getServicioProductoCaracteristica($objServicio,
                                                                                                  'ID_PROPUESTA',
                                                                                                  $objServicio->getProductoId()
                                                                                                  );
                    $arrayParametros = array("strLogin"      => $objPunto->getLogin(),
                                             "strPuntoId"    => $objPunto->getId(),
                                             "strProducto"   => $objAdmiProducto->getDescripcionProducto(), 
                                             "strServicioId" => $objServicio->getId(),
                                             "strEstado"     => $strEstado,
                                             "strUsuario"    => $strUsuario,
                                             "strError"      => $strError,
                                             "strIdPropuesta"=> $objServCaractProp->getValor());
                    //Ejecuta ws de SuiteCrm
                    $arrayParametrosWSCrm = array(
                                                  "arrayParametrosCRM"   => $arrayParametros,
                                                  "strOp"                => 'registroHistorial',
                                                  "strFuncion"           => 'procesar'
                                                 );
                    $arrayRespuestaWSCrm = $this->serviceTelcoCrm->getRequestCRM($arrayParametrosWSCrm);
                    
                    $arrayParametroList = array(
                        array(
                            "parameterName" => "login_auxiliar",
                            "dataType" => "TEXTO",
                            "nativeTypeValue" => $objServicio->getLoginAux()
                        ),
                        array(
                            "parameterName" => "Punto_id",
                            "dataType" => "TEXTO",
                            "nativeTypeValue" => $objPunto->getId()
                        ),
                        array(
                            "parameterName" => "Producto",
                            "dataType" => "TEXTO",
                            "nativeTypeValue" => $objAdmiProducto->getDescripcionProducto()
                        ),
                        array(
                            "parameterName" => "Servicio_id",
                            "dataType" => "TEXTO",
                            "nativeTypeValue" => $objServicio->getId()
                        ),
                        array(
                            "parameterName" => "Estado",
                            "dataType" => "TEXTO",
                            "nativeTypeValue" => $strEstado
                        ),
                        array(
                            "parameterName" => "Usuario",
                            "dataType" => "TEXTO",
                            "nativeTypeValue" => $strUsuario
                        ),
                        array(
                            "parameterName" => "Punto",
                            "dataType" => "TEXTO",
                            "nativeTypeValue" => $objPunto->getLogin()
                        ),
                        array(
                            "parameterName" => "Error",
                            "dataType" => "TEXTO",
                            "nativeTypeValue" => $strError
                        )
                    );
                    
                    $arrayPuntoServicios = array('resultCode'                => $strEstado,
                                                'productName'               => str_replace(" ", "_", $objAdmiProducto->getDescripcionProducto()),
                                                'instanceId'                => $strInstancia,
                                                'processName'               => 'CREAR_SERVICIO',
                                                'nativeValueParameterList'  => $arrayParametroList,
                                                'Usuario'   => $strUsuario);
                }
            }
        }
        catch (\Exception $ex) 
        {
            $strMensajeError = "Falló la comunicación entre TelcoS+ y TelcoCRM.\n ".$ex->getMessage();
            $arrayPuntoServicios = array('status' => '500',
                                         'error'  => $strMensajeError
                                        );
            $this->serviceUtil->insertError('TELCOS+',
                                            'OrquestadorService.crearServicio',
                                            $strMensajeError,
                                            $strUsuario,
                                            $strIpCreacion);
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }
        }
        return $arrayPuntoServicios;
    }
    
    /**
     * Documentación para la función 'putCrearTareaOrq'.
     *
     * Función encargada de crear tareas.
     *
     * @param array $arrayParametros [
     *                                "instanceId"                  => id de la instancia,
     *                                "nativeValueParameterList"    => Arreglo de datos por parte de orquestador,
     *                                "IdServicio"                  => Login de la persona a quién se le va a crear la tarea,
     *                               ]
     *
     * @return array $arrayResultado 
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 03-08-2022
     *
     * @author Anthony Santillan <asantillany@telconet.ec>
     * @version 1.1 15-05-2023 -- Se agrega la inserción en el historial del servicio
     *                            para visualización de la creación de la tarea
     *
     */
    public function putCrearTareaOrq($arrayDatosWs)
    {
        $strInstancia        = ( isset($arrayDatosWs['instanceId']) && !empty($arrayDatosWs['instanceId']) )
                                   ? $arrayDatosWs['instanceId'] : '';
        
        $arrayDatosOrq       = ( isset($arrayDatosWs['nativeValueParameterList']) && !empty($arrayDatosWs['nativeValueParameterList']) )
                                   ? $arrayDatosWs['nativeValueParameterList'] : '';
        $arrayColum          = $this->arrayColumManual($arrayDatosOrq, 'parameterName');
        $intKeySer           = array_search('idServicio', $arrayColum);
        $intIdServicio       = ( isset($arrayDatosOrq[$intKeySer]['nativeTypeValue']) && !empty($arrayDatosOrq[$intKeySer]['nativeTypeValue']) )
                                    ? $arrayDatosOrq[$intKeySer]['nativeTypeValue'] : '';
        
        $arrayTarea              = array();
        $arrayRespuesta          = array();
        $strEstado               = "error";
        $strUsrCreacion          = "TelcoS+";
        $intIdEmpresa            = 10;
        $strRegionServicio       = "";
        $strObservacion          = "Por favor su ayuda, con la tarea";
        try
        {
            if(empty($intIdServicio))
            {
                throw new \Exception("No se encuentra el servicio.");
            }
            //SACAMOS LOS DATOS DEL SERVICIO
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            if(!is_object($objServicio))
            {
                throw new \Exception("Servicio no encontrado, favor verificar.");
            }
            $objPunto    = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($objServicio->getPuntoId());
            if(!is_object($objPunto))
            {
                throw new \Exception("Punto no encontrado, favor verificar.");
            }
            $arrayParametrosRegion["intPuntoId"] = $objPunto->getId();
            $strRegionServicio = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->getRegionPorPunto($arrayParametrosRegion);
            if(empty($strRegionServicio))
            {
                throw new \Exception("No se encuentra la región del punto.");
            }
            
            $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($objServicio->getProductoId());
            if(!is_object($objProducto))
            {
                throw new \Exception("Producto no encontrado, favor verificar.");
            }
            
            
            //Se obtiene el nombre y proceso de la tarea.
            $arrayTarea = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                               ->get('PARAMETROS_PARA_GENERAR_TAREA_L2',
                                     'COMERCIAL',
                                     'TAREA_PARA_ACTIVACION',
                                     'TAREA_PROCESO',
                                     $objProducto->getDescripcionProducto(),
                                     $strRegionServicio,
                                     '',
                                     '',
                                     '',
                                     $intIdEmpresa);
            if(empty($arrayTarea) || !is_array($arrayTarea))
            {
                throw new \Exception("No existe tarea y proceso");
            }
            $objServCaractObs = $this->serviceTecnico->getServicioProductoCaracteristica($objServicio,
                                                                                                  'OBSERVACION_TAREA_SECURITY',
                                                                                                  $objServicio->getProductoId()
                                                                                                  );
            if(is_object($objServCaractObs))
            {
                $strObservacion = $objServCaractObs->getValor();
            }
            //Se consume Service de crear tarea.
            $arrayParametrosTarea   = array('intIdPersonaEmpresaRol' => $arrayTarea[0]['valor6'],
                                            'intPuntoId'             => $objPunto->getId(),
                                            'intIdEmpresa'           => $intIdEmpresa,
                                            'strPrefijoEmpresa'      => 'TN',
                                            'strNombreTarea'         => $arrayTarea[0]['valor4'],
                                            'strNombreProceso'       => $arrayTarea[0]['valor3'],
                                            'strObservacionTarea'    => $strObservacion,
                                            'strMotivoTarea'         => $strObservacion,
                                            'strTipoAsignacion'      => 'empleado',
                                            'strIniciarTarea'        => 'S',
                                            'strTipoTarea'           => 'T',
                                            'strTareaRapida'         => 'N',
                                            'strFechaHoraSolicitada' => date("Y-m-d").' '.date("H:i:s"),
                                            'boolAsignarTarea'       => true,
                                            'strAplicacion'          => 'TelcoS+',
                                            'strUsuarioAsigna'       => $arrayTarea[0]['valor5'],
                                            'strUserCreacion'        => $objServicio->getUsrCreacion(),
                                            'strIpCreacion'          => $objServicio->getIpCreacion());
            $arrayRespuestaTarea = $this->serviceSoporte->crearTareaCasoSoporte($arrayParametrosTarea);
            if($arrayRespuestaTarea['mensaje'] === 'fail')
            {
                throw new \Exception('Error al crear la tarea, por favor comuníquese con el departamento de Sistemas.');
            }
            if((!empty($arrayRespuestaTarea['numeroTarea']) && isset($arrayRespuestaTarea['numeroTarea'])))
            {
                $this->emComunicacion->getConnection()->beginTransaction();
                $this->emComercial->getConnection()->beginTransaction();
                $this->emSoporte->getConnection()->beginTransaction();

                $objInfoComunicacion = $this->emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                            ->find($arrayRespuestaTarea['numeroTarea']);
                if(!is_object($objInfoComunicacion))
                {
                    throw new \Exception("Tarea en Info Comunicación no encontrada, favor verificar.");
                }
                
                $objCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                     ->findOneBy(array('descripcionCaracteristica' => 'INSTANCIA_ID_ORQ', 
                                                                                       'tipo'                      => 'COMERCIAL')
                                                                               );
                if(is_object($objCaracteristica))
                {
                    //Agregamos la instancia
                    $objInfoTareaCaracteristica = new InfoTareaCaracteristica();
                    $objInfoTareaCaracteristica->setTareaId($objInfoComunicacion->getId());
                    $objInfoTareaCaracteristica->setDetalleId($arrayRespuestaTarea['numeroDetalle']);
                    $objInfoTareaCaracteristica->setCaracteristicaId($objCaracteristica->getId());
                    $objInfoTareaCaracteristica->setValor($strInstancia);
                    $objInfoTareaCaracteristica->setEstado('Activo');
                    $objInfoTareaCaracteristica->setFeCreacion(new \DateTime('now'));
                    $objInfoTareaCaracteristica->setUsrCreacion($objServicio->getUsrCreacion());
                    $objInfoTareaCaracteristica->setIpCreacion($objServicio->getIpCreacion());
                    $this->emSoporte->persist($objInfoTareaCaracteristica);
                    $this->emSoporte->flush();
                }
                $objCaracteristicaServicio = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                     ->findOneBy(array('descripcionCaracteristica' => 'ORQUESTADOR_SERVICIO_ID', 
                                                                                       'tipo'                      => 'COMERCIAL')
                                                                               );
                if(is_object($objCaracteristicaServicio))
                {
                    //Agregamos la instancia
                    $objInfoTareaCaracteristica = new InfoTareaCaracteristica();
                    $objInfoTareaCaracteristica->setTareaId($objInfoComunicacion->getId());
                    $objInfoTareaCaracteristica->setDetalleId($arrayRespuestaTarea['numeroDetalle']);
                    $objInfoTareaCaracteristica->setCaracteristicaId($objCaracteristicaServicio->getId());
                    $objInfoTareaCaracteristica->setValor($objServicio->getId());
                    $objInfoTareaCaracteristica->setEstado('Activo');
                    $objInfoTareaCaracteristica->setFeCreacion(new \DateTime('now'));
                    $objInfoTareaCaracteristica->setUsrCreacion($objServicio->getUsrCreacion());
                    $objInfoTareaCaracteristica->setIpCreacion($objServicio->getIpCreacion());
                    $this->emSoporte->persist($objInfoTareaCaracteristica);
                    $this->emSoporte->flush();
                }
                
                $objCaracteristicaTar = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                             ->findOneBy(array('descripcionCaracteristica' => 'TAREA_ACTIVACION_SECURITY', 
                                                                               'tipo'                      => 'COMERCIAL')
                                                                       );
                if(is_object($objCaracteristicaTar))
                {
                    $objProdCaractTarea = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                        ->findOneBy(array( 
                                                                          "productoId"       => $objProducto->getId(),
                                                                          "caracteristicaId" => $objCaracteristicaTar->getId(),
                                                                          "estado"           => "Activo"
                                                                         )); 

                    if(is_object($objProdCaractTarea))
                    {
                        $entityServicioProdCaract  = new InfoServicioProdCaract();
                        $entityServicioProdCaract->setServicioId($objServicio->getId());
                        $entityServicioProdCaract->setProductoCaracterisiticaId($objProdCaractTarea->getId());
                        $entityServicioProdCaract->setValor($objInfoComunicacion->getId());
                        $entityServicioProdCaract->setEstado('Activo');
                        $entityServicioProdCaract->setUsrCreacion($objServicio->getUsrCreacion());
                        $entityServicioProdCaract->setFeCreacion(new \DateTime('now'));
                        $this->emComercial->persist($entityServicioProdCaract);
                        $this->emComercial->flush(); 

                    }
                    
                    $objInfoServicioHistorial = new InfoServicioHistorial();
                    $objInfoServicioHistorial->setServicioId($objServicio);
                    $objInfoServicioHistorial->setObservacion("Tarea de instalacion de "
                                                            . $objProducto->getDescripcionProducto() .
                                                            " #". $arrayRespuestaTarea['numeroTarea']);
                    $objInfoServicioHistorial->setEstado($objServicio->getEstado());
                    $objInfoServicioHistorial->setUsrCreacion($objServicio->getUsrCreacion());
                    $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objInfoServicioHistorial->setIpCreacion($objServicio->getIpCreacion());
                    $this->emComercial->persist($objInfoServicioHistorial);
                    $this->emComercial->flush(); 

                }
                
                
                $objInfoDetalle = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                            ->find($objInfoComunicacion->getDetalleId());
                if(!is_object($objInfoDetalle))
                {
                    throw new \Exception("Tarea en Info Detalle no encontrada, favor verificar.");
                }
                
                
                $objServCaractDoc = $this->serviceTecnico->getServicioProductoCaracteristica($objServicio,
                                                                                                  'DOC_ANEXO_TECNICO',
                                                                                                  $objServicio->getProductoId()
                                                                                                  );

                if(is_object($objServCaractDoc) && !empty($objServCaractDoc))
                {
                    $strRutaDoc     = $objServCaractDoc->getValor();
                    if($strRutaDoc != "No se adjunto archivo en el CRM")
                    {
                        $arrayArchivo   = explode('/', $strRutaDoc);
                        $arrayCount     = count($arrayArchivo);
                        $strNuevoNombre = $arrayArchivo[$arrayCount - 1];
                        $arrayTipoDoc   = explode('.', $strNuevoNombre);
                        $arrayCountT    = count($arrayTipoDoc);
                        $strTipoDoc     = $arrayTipoDoc[$arrayCountT - 1];

                        $objAdmiTipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                            ->findOneByExtensionTipoDocumento(strtoupper($strTipoDoc));
                        if(!is_object($objAdmiTipoDocumento))
                        {
                            throw new \Exception("No se encuentra el tipo de documento, favor verificar.");
                        }

                        $objInfoDocumento = new InfoDocumento();
                        $objInfoDocumento->setNombreDocumento('Adjunto Tarea');
                        $objInfoDocumento->setMensaje('Documento que se adjunta a una tarea');
                        $objInfoDocumento->setUbicacionFisicaDocumento($strRutaDoc);//url
                        $objInfoDocumento->setUbicacionLogicaDocumento($strNuevoNombre);
                        $objInfoDocumento->setEstado('Activo');
                        $objInfoDocumento->setFeCreacion(new \DateTime('now'));
                        $objInfoDocumento->setFechaDocumento(new \DateTime('now'));
                        $objInfoDocumento->setIpCreacion($objServicio->getIpCreacion());
                        $objInfoDocumento->setUsrCreacion($objServicio->getUsrCreacion());
                        $objInfoDocumento->setEmpresaCod($intIdEmpresa);
                        $objInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);

                        $this->emComunicacion->persist($objInfoDocumento);
                        $this->emComunicacion->flush();

                        //Entidad de la tabla INFO_DOCUMENTO_RELACION donde se relaciona el documento cargado con el IdCaso
                        $objInfoDocumentoRelacion = new InfoDocumentoRelacion();
                        $objInfoDocumentoRelacion->setModulo('SOPORTE');
                        $objInfoDocumentoRelacion->setEstado('Activo');
                        $objInfoDocumentoRelacion->setFeCreacion(new \DateTime('now'));
                        $objInfoDocumentoRelacion->setUsrCreacion($objServicio->getUsrCreacion());
                        $objInfoDocumentoRelacion->setDetalleId($arrayRespuestaTarea['numeroDetalle']);
                        $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());

                        $this->emComunicacion->persist($objInfoDocumentoRelacion);
                        $this->emComunicacion->flush();
                    }
                }
                //sacamos el nombre y tipo de datos de la url
                
                if ($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->getConnection()->commit();
                    $this->emComercial->getConnection()->close();

                }
                if ($this->emComunicacion->getConnection()->isTransactionActive())
                {
                    $this->emComunicacion->getConnection()->commit();
                    $this->emComunicacion->getConnection()->close();

                }
                if ($this->emSoporte->getConnection()->isTransactionActive())
                {
                    $this->emSoporte->getConnection()->commit();
                    $this->emSoporte->getConnection()->close();
                }
                $strEstado = 'pending';

            }
            $arrayRespuesta   = array('resultCode'              => $strEstado,
                                    'productName'               => str_replace(" ", "_", $objProducto->getDescripcionProducto()),
                                    'instanceId'                => $strInstancia,
                                    'processName'               => 'ACTIVACION',
                                    'nativeValueParameterList'  => '',
                                    'Usuario'                   => $strUsrCreacion);
        }
        catch(\Exception $ex)
        {
            $arrayRespuesta['message'] = $ex->getMessage();
            $arrayRespuesta['status']  = "ERROR";
            $this->serviceUtil->insertError('TELCOS+',
                                            'OrquestadorService.putCrearTareaOrq',
                                            $ex->getMessage(),
                                            $strUsrCreacion,
                                            '127.0.0.1');
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
                $this->emComunicacion->getConnection()->close();

            }
            if($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->getConnection()->rollback();
                $this->emSoporte->getConnection()->close();
            }
        }
        return $arrayRespuesta;
    }
    
    /**
     * Documentación para la función 'putProcesoTareaOrq'.
     *
     * Función encargada de crear tareas.
     *
     * @param array $arrayParametros [
     *                                "intIdServicio"      => Servicio para sacar la tarea,
     *                               ]
     *
     * @return array $arrayResultado [
     *                                "message"      =>  Mensaje de respuesta.
     *                                "status"       =>  Estado de respuesta.
     *                               ]
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 12-08-2022
     *
     */
    public function putProcesoTareaOrq($arrayDatosWs)
    {
        $intIdServicio       = ( isset($arrayDatosWs['idServicio']) && !empty($arrayDatosWs['idServicio']) )
                                    ? $arrayDatosWs['idServicio'] : '';
        $intIdEmpresa        = ( isset($arrayDatosWs['idEmpresa']) && !empty($arrayDatosWs['idEmpresa']) )
                                    ? $arrayDatosWs['idEmpresa'] : '10';
        $strEstado           = 'ERROR';
        $strInstancia        = '';
        $intTarea            = '';
        try
        {
            if(empty($intIdServicio))
            {
                throw new \Exception("Id Servicio no puede estar vacio, favor verificar.");
            }
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            if(!is_object($objServicio))
            {
                throw new \Exception("Servicio no encontrado, favor verificar.");
            }
            $objAdmiProducto   = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->findOneById($objServicio->getProductoId());

            $objServCaractTarea = $this->serviceTecnico->getServicioProductoCaracteristica($objServicio,
                                                                                            'TAREA_ACTIVACION_SECURITY',
                                                                                            $objServicio->getProductoId()
                                                                                            );

            $objServCaractInstancia = $this->serviceTecnico->getServicioProductoCaracteristica($objServicio,
                                                                                               'INSTANCIA_ID_ORQ',
                                                                                                $objServicio->getProductoId()
                                                                                               );
            if(is_object($objServCaractTarea) && !empty($objServCaractTarea) && is_object($objServCaractInstancia) && !empty($objServCaractInstancia))
            {
                $intTarea     = $objServCaractTarea->getValor();
                $strInstancia = $objServCaractInstancia->getValor();
                $objInfoComunicacion = $this->emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                            ->find($intTarea);
                if(!is_object($objInfoComunicacion))
                {
                    throw new \Exception("Tarea en Info Comunicación no encontrada, favor verificar.");
                }
                $strEstado = 'OK';
                $arrayParametroList = array(
                        array(
                            "parameterName" => "idTarea",
                            "dataType" => "TEXTO",
                            "nativeTypeValue" => $intTarea
                        ),
                        array(
                            "parameterName" => "Estado",
                            "dataType" => "TEXTO",
                            "nativeTypeValue" => $strEstado
                        ),
                        array(
                            "parameterName" => "Error",
                            "dataType" => "TEXTO",
                            "nativeTypeValue" => ""
                        )
                    );
                $arrayParametros = array('resultCode'               => $strEstado,
                                        'productName'               => str_replace(" ", "_", $objAdmiProducto->getDescripcionProducto()),
                                        'instanceId'                => $strInstancia,
                                        'processName'               => 'ACTIVACION',
                                        'nativeValueParameterList'  => $arrayParametroList,
                                        'Usuario'   => 'Telcos+');
                

                $arrayRespuestaOrq = $this->getOrquestador($arrayParametros);
            }
            
        }
        catch(\Exception $ex)
        {
            $arrayRespuesta['message'] = $ex->getMessage();
            $arrayRespuesta['status']  = "ERROR";
            $this->serviceUtil->insertError('TELCOS+',
                                            'OrquestadorService.putProcesoTareaOrq',
                                            $ex->getMessage(),
                                            'Telcos+',
                                            '127.0.0.1');
        }
        return $arrayRespuesta;
    }
    
    /**
     * Documentación para la función 'putCrearSeguimientoOrq'.
     *
     * Función encargada de crear Seguimiento.
     *
     * @param array $arrayParametros [
     *                                "instanceId"                  => id de la instancia,
     *                                "nativeValueParameterList"    => Arreglo de datos por parte de orquestador,
     *                                "IdServicio"                  => Login de la persona a quién se le va a crear la tarea,
     *                               ]
     *
     * @return array $arrayResultado 
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 12-08-2022
     *
     */
    public function putCrearSeguimientoOrq($arrayDatosWs)
    {
        $intIdEmpresa        = ( isset($arrayDatosWs['idEmpresa']) && !empty($arrayDatosWs['idEmpresa']) )
                                    ? $arrayDatosWs['idEmpresa'] : '10';
        $strInstancia        = ( isset($arrayDatosWs['instanceId']) && !empty($arrayDatosWs['instanceId']) )
                                   ? $arrayDatosWs['instanceId'] : '';
        $arrayDatosOrq       = ( isset($arrayDatosWs['nativeValueParameterList']) && !empty($arrayDatosWs['nativeValueParameterList']) )
                                   ? $arrayDatosWs['nativeValueParameterList'] : '';
        
        $arrayColum          = $this->arrayColumManual($arrayDatosOrq, 'parameterName');
        $intKeySer           = array_search('idTarea', $arrayColum);
        $intTarea            = ( isset($arrayDatosOrq[$intKeySer]['nativeTypeValue']) && !empty($arrayDatosOrq[$intKeySer]['nativeTypeValue']) )
                                    ? $arrayDatosOrq[$intKeySer]['nativeTypeValue'] : '';
        
        $strEstado               = "pending";
        $strUsrCreacion          = "TelcoS+";
        try
        {
            $this->emSoporte->getConnection()->beginTransaction();
            if(empty($intTarea))
            {
                throw new \Exception("Id Tarea no puede estar vacio, favor verificar.");
            }
            $objInfoComunicacion = $this->emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                        ->find($intTarea);
            if(!is_object($objInfoComunicacion))
            {
                throw new \Exception("Tarea en Info Comunicación no encontrada, favor verificar.");
            }

            $objInfoTareaSeg = $this->emSoporte->getRepository('schemaBundle:InfoTareaSeguimiento')
                        ->findOneBy(array("detalleId" => $objInfoComunicacion->getDetalleId()));
            if(!is_object($objInfoTareaSeg))
            {
                throw new \Exception("Tarea en Info Comunicación no encontrada, favor verificar.");
            }
            
            $objAdmiCaracteristicaSer = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array ('descripcionCaracteristica' => 'ORQUESTADOR_SERVICIO_ID',
                                                       'estado'                    => 'Activo'));

            if (is_object($objAdmiCaracteristicaSer) && !empty($objAdmiCaracteristicaSer))
            {
                $objInfoTareaCaracteristicaSer = $this->emSoporte->getRepository('schemaBundle:InfoTareaCaracteristica')
                        ->findOneBy(array ('tareaId'          => $intTarea,
                                           'caracteristicaId' => $objAdmiCaracteristicaSer->getId(),
                                           'estado'           => 'Activo'));

                $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($objInfoTareaCaracteristicaSer->getValor());
                if(!is_object($objServicio))
                {
                    throw new \Exception("Servicio no encontrado, favor verificar.");
                }
                $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($objServicio->getProductoId());
                if(!is_object($objProducto))
                {
                    throw new \Exception("Producto no encontrado, favor verificar.");
                }
                
            }

            //sacamos observacion parametrizada
            $arrayTarea = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                               ->get('OBSERVACION_SEGUIMIENTO_L2',
                                     'COMERCIAL',
                                     'TAREA_SEGUIMIENTO',
                                     'PROCESO_SEGUIMIENTO',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     $intIdEmpresa);
            if(empty($arrayTarea) || !is_array($arrayTarea))
            {
                throw new \Exception("No existe observacion");
            }

            $objInfoTareaSeguimiento = new InfoTareaSeguimiento();
            $objInfoTareaSeguimiento->setDetalleId($objInfoTareaSeg->getDetalleId());
            $objInfoTareaSeguimiento->setObservacion($arrayTarea[0]['valor1']);
            $objInfoTareaSeguimiento->setUsrCreacion('TELCOS+');
            $objInfoTareaSeguimiento->setFeCreacion(new \DateTime('now'));
            $objInfoTareaSeguimiento->setEmpresaCod($intIdEmpresa);
            $objInfoTareaSeguimiento->setEstadoTarea($objInfoComunicacion->getEstado());
            $objInfoTareaSeguimiento->setInterno($objInfoTareaSeg->getInterno());
            $objInfoTareaSeguimiento->setDepartamentoId($objInfoTareaSeg->getDepartamentoId());
            $objInfoTareaSeguimiento->setPersonaEmpresaRolId($objInfoTareaSeg->getPersonaEmpresaRolId());
            $this->emSoporte->persist($objInfoTareaSeguimiento);
            $this->emSoporte->flush();

            if ($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->getConnection()->commit();
                $this->emSoporte->getConnection()->close();
            }
                
            $arrayRespuesta   = array('resultCode'              => $strEstado,
                                    'productName'               => str_replace(" ", "_", $objProducto->getDescripcionProducto()),
                                    'instanceId'                => $strInstancia,
                                    'processName'               => 'CERRAR_INSTALACION',
                                    'nativeValueParameterList'  => '',
                                    'Usuario'                   => $strUsrCreacion);    
            
            
        }
        catch(\Exception $ex)
        {
            $arrayRespuesta['message'] = $ex->getMessage();
            $arrayRespuesta['status']  = "ERROR";
            $this->serviceUtil->insertError('TELCOS+',
                                            'OrquestadorService.putCrearSeguimientoOrq',
                                            $ex->getMessage(),
                                            'Telcos+',
                                            '127.0.0.1');
            if($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->getConnection()->rollback();
                $this->emSoporte->getConnection()->close();
            }
        }
        return $arrayRespuesta;
    }
    
    /**
     * Documentación para la función 'putCerrarTareaOrq'.
     *
     * Función encargada de cerrar la tarea.
     *
     * @param array $arrayParametros [
     *                                "instanceId"      => Instancia de orquestador,
     *                                "idTarea"         => numero de la tarea
     *                               ]
     *
     * @return array $arrayResultado 
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 12-08-2022
     *
     */
    public function putCerrarTareaOrq($arrayDatosWs)
    {
        $intIdEmpresa        = ( isset($arrayDatosWs['idEmpresa']) && !empty($arrayDatosWs['idEmpresa']) )
                                    ? $arrayDatosWs['idEmpresa'] : '10';
        $strInstancia        = ( isset($arrayDatosWs['idInstancia']) && !empty($arrayDatosWs['idInstancia']) )
                                   ? $arrayDatosWs['idInstancia'] : '';
        $intTarea            = ( isset($arrayDatosWs['intTarea']) && !empty($arrayDatosWs['intTarea']) )
                                   ? $arrayDatosWs['intTarea'] : '';
        
        $strEstado               = "error";
        $strUsrCreacion          = "TelcoS+";
        try
        {
            if(empty($intTarea))
            {
                throw new \Exception("Id Tarea no puede estar vacio, favor verificar.");
            }
            $objInfoComunicacion = $this->emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                        ->find($intTarea);
            if(!is_object($objInfoComunicacion))
            {
                throw new \Exception("Tarea en Info Comunicación no encontrada, favor verificar.");
            }

            $objInfoTareaSeg = $this->emSoporte->getRepository('schemaBundle:InfoTareaSeguimiento')
                        ->findOneBy(array("detalleId" => $objInfoComunicacion->getDetalleId()));
            if(!is_object($objInfoTareaSeg))
            {
                throw new \Exception("Tarea en Info Comunicación no encontrada, favor verificar.");
            }
            
            $objAdmiCaracteristicaSer = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array ('descripcionCaracteristica' => 'ORQUESTADOR_SERVICIO_ID',
                                                       'estado'                    => 'Activo'));

            if (is_object($objAdmiCaracteristicaSer) && !empty($objAdmiCaracteristicaSer))
            {
                $objInfoTareaCaracteristicaSer = $this->emSoporte->getRepository('schemaBundle:InfoTareaCaracteristica')
                        ->findOneBy(array ('tareaId'          => $intTarea,
                                           'caracteristicaId' => $objAdmiCaracteristicaSer->getId(),
                                           'estado'           => 'Activo'));

                $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($objInfoTareaCaracteristicaSer->getValor());
                if(!is_object($objServicio))
                {
                    throw new \Exception("Servicio no encontrado, favor verificar.");
                }
                $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($objServicio->getProductoId());
                if(!is_object($objProducto))
                {
                    throw new \Exception("Producto no encontrado, favor verificar.");
                }
                
            }

            //sacamos observacion parametrizada
            $arrayTarea = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                               ->get('OBSERVACION_SEGUIMIENTO_L2',
                                     'COMERCIAL',
                                     'TAREA_SEGUIMIENTO',
                                     'PROCESO_SEGUIMIENTO',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     $intIdEmpresa);
            if(empty($arrayTarea) || !is_array($arrayTarea))
            {
                throw new \Exception("No existe observacion");
            }
            $strEstado = 'OK';
            $arrayParametroList = array(
                        array(
                            "parameterName" => "Estado",
                            "dataType" => "TEXTO",
                            "nativeTypeValue" => $strEstado
                        ),
                        array(
                            "parameterName" => "Error",
                            "dataType" => "TEXTO",
                            "nativeTypeValue" => ""
                        )
                    );    
            $arrayParametros   = array('resultCode'              => $strEstado,
                                    'productName'               => str_replace(" ", "_", $objProducto->getDescripcionProducto()),
                                    'instanceId'                => $strInstancia,
                                    'processName'               => 'CERRAR_INSTALACION',
                                    'nativeValueParameterList'  => $arrayParametroList,
                                    'Usuario'                   => $strUsrCreacion);    
            
            $arrayRespuestaOrq = $this->getOrquestador($arrayParametros);

        }
        catch(\Exception $ex)
        {
            $arrayRespuesta['message'] = $ex->getMessage();
            $arrayRespuesta['status']  = "ERROR";
            $this->serviceUtil->insertError('TELCOS+',
                                            'OrquestadorService.putCerrarTareaOrq',
                                            $ex->getMessage(),
                                            'Telcos+',
                                            '127.0.0.1');
        }
        return $arrayRespuesta;
    }
    
    /**
    * Documentación para la función 'getOrquestador'.
    *
    * Función encargada de llamar al ws para .
    *
    * @author David Leon <mdleon@telconet.ec>
    * @version 1.0 - 13-05-2022
    *
    */
    public function getOrquestador($arrayParametros)
    {
        try
        {
            $intIdEmpresa        = ( isset($arrayParametros['idEmpresa']) && !empty($arrayParametros['idEmpresa']) )
                                    ? $arrayParametros['idEmpresa'] : '10';
            $strMensajeError     = "";
            $strStatus           = "200";
            $arrayParametros2    = array();
            $arrayResultado      = array();
            if(is_array($arrayParametros) && !empty($arrayParametros))
            {
                $arrayParametros2 = array('data' => $arrayParametros,
                                          'user' => 'Telcos+');
                //sacamos observacion parametrizada
                $arrayApikey = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                   ->get('DATOS_ORQUESTADOR_APIKEY',
                                         'COMERCIAL',
                                         'APIKEY',
                                         'APIKEY-ORQ',
                                         '',
                                         '',
                                         '',
                                         '',
                                         '',
                                         $intIdEmpresa);
                if(empty($arrayApikey) || !is_array($arrayApikey))
                {
                    throw new \Exception("No existe apikey");
                }
                $strApiKey = $arrayApikey[0]['valor1'];
                $arrayOptions      = array(CURLOPT_SSL_VERIFYPEER => false,
                                           CURLOPT_HTTPHEADER     => array('apikey: '.$strApiKey));
                $arrayResponse     = $this->restClientPedidos->postJSON($this->strMicroServUrl,json_encode($arrayParametros2) ,$arrayOptions);

                if(!isset($arrayResponse['result']) || $arrayResponse['status']!="200")
                {
                    throw new \Exception('Problemas al obtener información del MicroServicio: '.$arrayResultado["error"]);
                }
                $arrayResultado = json_decode($arrayResponse['result'],true);

                if((empty($arrayResultado) || !is_array($arrayResultado))|| (!isset($arrayResultado['message']) || $arrayResultado["message"] == "") 
                    && !$arrayResultado['status']=='OK')
                {
                    throw new \Exception('Problemas al obtener información del MicroServicio, reintente nuevamente.');
                }

                $arrayDataTemp = $arrayResultado["data"];

                if((empty($arrayDataTemp) || !is_array($arrayDataTemp))|| (!isset($arrayDataTemp["codError"]) || 
                    $arrayDataTemp["codError"] !=$strStatus  || $arrayDataTemp["mensajeUsuario"] !='OK'|| $arrayDataTemp["mensajeTecnico"] !='OK'))
                {
                    throw new \Exception('Problemas al obtener información, reintente nuevamente.');
                }
                $arrayDatosPedidos = $arrayDataTemp["data"];
            }
            else
            {
                throw new \Exception('Problemas al obtener información, reintente nuevamente.');
            }
        }
        catch( \Exception $e )
        {
            $strMensajeError = $e->getMessage();
            $strStatus       = "500";
        }
        $arrayRespuesta = array('error'  => $strMensajeError,
                                'datos'  => $arrayDatosPedidos,
                                'status' => $strStatus);
        return $arrayRespuesta;
    }
    
    /**
    * Documentación para la función 'arrayColumManual'.
    *
    * Ya que no actualizan el php toca estar inventandose cosas.
    *
    * @author David Leon <mdleon@telconet.ec>
    * @version 1.0 - 25-08-2022
    *
    */
    public function arrayColumManual($arrayDatos, $strColumn)
    {
        $arrayNew = array();
        foreach ($arrayDatos as $row) 
        {
            $arrayNew[] = $row[$strColumn];
        }
        return $arrayNew;
    }
}
