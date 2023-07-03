<?php

namespace telconet\comercialBundle\Service;
use Doctrine\ORM\EntityManager;

use telconet\schemaBundle\Entity\AdmiTipoSolicitud;
use telconet\schemaBundle\Entity\InfoContrato;
use telconet\schemaBundle\Entity\InfoContratoClausula;
use telconet\schemaBundle\Entity\InfoContratoDatoAdicional;
use telconet\schemaBundle\Entity\InfoContratoFormaPago;
use telconet\schemaBundle\Entity\InfoServicioTecnico;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoOrdenTrabajo;
use telconet\schemaBundle\Entity\InfoPunto;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\AdmiRol;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\schemaBundle\Entity\AdmiTipoDocumento;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\schemaBundle\Entity\InfoPuntoCaracteristica;
use telconet\schemaBundle\Entity\AdmiCaracteristica;
use telconet\schemaBundle\Entity\InfoPuntoDatoAdicional;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\tecnicoBundle\Service\InfoServicioTecnicoService;
use telconet\schemaBundle\Entity\InfoServicioComision;
use telconet\schemaBundle\Entity\InfoServicioComisionHisto;
use telconet\schemaBundle\Entity\AdmiCiclo;
use telconet\schemaBundle\Entity\InfoSolucionCab;
use telconet\schemaBundle\Entity\InfoSolucionDet;
use telconet\schemaBundle\Entity\InfoServicioRecursoCab;
use telconet\schemaBundle\Entity\InfoServicioRecursoDet;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoContratoCaracteristica;
//use telconet\comercialBundle\Service\ComercialService;
use Imagick;

class InfoContratoService {
    
    private $servicePreCliente;
    private $serviceCliente;
    private $emcom;
    private $emComercial;
    private $emComuni;
    private $emComunicacion;
    private $emGeneral;
    private $path_telcos;
    private $serviceCrypt;
    private $emInfraestructura;
    private $emFinanciero;
    private $serviceInfoPersonaFormaContacto;
    private $fileRoot;
    private $serviceServicioTecnico;
    private $serviceInfoServicio;
    private $serviceInfoPunto;
    private $serviceComercial;
    private $serviceLicenciasOffice365;
    private $utilService;   
    private $serviceSms;
    private $serviceInfoDocFinancieroCab;
    private $serviceLicenciasKaspersky;
    private $serviceFoxPremium;
    private $serviceRestClient;
    private $servicePromocion;
    private $strMSnfs;
    private $objContainer;
    private $strUrsrComercial;
    private $strPassComercial; 
    private $strDnsComercial;
    private $servicePromociones;
    private $strUrlMsCompContratoDigital;
    private $serviceTokenCas; 
    private $serviceRepresentanteLegalMs;
    private $serviceKonibit;
 
    private $strUrlPreplanificaProdCIHMs;
    private $strUrlGeneraOtServicioCIHMs;
    private $strUrlReversaPreplanificaMs;


    /**
     * Documentación para el método 'setDependencies'.
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 06-03-2017 - Se agrega la variable 'serviceInfoServicio'
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.2 15-06-2017 - Se agrega la variable 'serviceInfoPunto'
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.3 19-04-2018 - Se agrega la variable 'serviceComercial'
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.4 26-02-2020 - Se agrega la variable 'serviceInfoDocFinancieroCab'
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.5 11-09-2020 - Se adiciona los services para el consumo de WS tipo REST
     *                           y se lee del parameter.yml el parámetro ms_nfs.
     *
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.6 18-05-2022 - Se anexa servicio de promociones de tecnico para poder realizar validaciones para
     *                           activar o desactivar promociones de frnaja horaria en eventos de CRS.
     * 
     * @author Alex Gómez <algomez@telconet.ec>
     * @version 1.7 11-10-2022 - Se adiciona los services para el consumo de WS tipo REST por
     *                           preplanificación de productos CIH
     * 
     * @since 1.0
     */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container) 
    {
        $this->fileRoot                        = $container->getParameter('ruta_upload_documentos');
        $this->path_telcos                     = $container->getParameter('path_telcos');
        $this->emComuni                        = $container->get('doctrine')->getManager('telconet_comunicacion');
        $this->servicePreCliente               = $container->get('comercial.PreCliente');   
        $this->serviceCliente                  = $container->get('comercial.Cliente');     
        $this->emcom                           = $container->get('doctrine.orm.telconet_entity_manager');     
        $this->emComercial                     = $container->get('doctrine.orm.telconet_entity_manager');     
        $this->emComunicacion                  = $container->get('doctrine.orm.telconet_comunicacion_entity_manager');
        $this->emGeneral                       = $container->get('doctrine.orm.telconet_general_entity_manager');        
        $this->serviceCrypt                    = $container->get('seguridad.Crypt');     
        $this->emInfraestructura               = $container->get('doctrine.orm.telconet_infraestructura_entity_manager');        
        $this->emFinanciero                    = $container->get('doctrine.orm.telconet_financiero_entity_manager');                    
        $this->serviceInfoPersonaFormaContacto = $container->get('comercial.InfoPersonaFormaContacto'); 
        $this->serviceServicioTecnico          = $container->get('tecnico.InfoServicioTecnico');
        $this->serviceInfoServicio             = $container->get('comercial.InfoServicio');
        $this->serviceInfoPunto                = $container->get('comercial.InfoPunto');
        $this->serviceComercial                = $container->get('comercial.Comercial'); 
        $this->serviceLicenciasOffice365       = $container->get('tecnico.LicenciasOffice365');
        $this->utilService                     = $container->get('schema.Util');
        $this->serviceSms                      = $container->get('comunicaciones.SMS');
        $this->serviceInfoDocFinancieroCab     = $container->get('financiero.InfoDocumentoFinancieroCab');
        $this->serviceLicenciasKaspersky       = $container->get('tecnico.LicenciasKaspersky');
        $this->serviceFoxPremium               = $container->get('tecnico.FoxPremium');
        $this->serviceRestClient               = $container->get('schema.RestClient');
        $this->servicePromocion                = $container->get('comercial.Promocion'); 
        $this->servicePromociones              = $container->get('tecnico.Promociones');
        $this->strMSnfs                        = $container->getParameter('ms_nfs');
        $this->strUrlCrearContratoMs           = $container->getParameter('ws_ms_crearContrato_url');
        $this->strUrlReenviarPinMs             = $container->getParameter('ws_ms_reenviarPin_url');
        $this->strUrlAutorizarContratoMs       = $container->getParameter('ws_ms_autorizarContrato_url');
        $this->strUrlDocumentoContratoMs       = $container->getParameter('ws_ms_documentosContrato_url');
        $this->objContainer                    = $container;

        $this->strUrsrComercial             = $container->getParameter('user_comercial');
        $this->strPassComercial             = $container->getParameter('passwd_comercial');
        $this->strDnsComercial              = $container->getParameter('database_dsn');
        $this->strUrlMsCompContratoDigital  = $container->getParameter('ws_ms_contrato_digital_url');
        $this->serviceTokenCas              = $container->get('seguridad.TokenCas');
        $this->serviceRepresentanteLegalMs  = $container->get('comercial.RepresentanteLegalMs');
        $this->strUrlValidarNumeroTarjetaCta   = $container->getParameter('ws_ms_validarNumeroTarjetaCta');
        $this->serviceKonibit               = $container->get('comercial.ConsumoKonibit');

        $this->strUrlPreplanificaProdCIHMs  = $container->getParameter('ws_ms_preplanifica_producto_cih_url');
        $this->strUrlGeneraOtServicioCIHMs  = $container->getParameter('ws_ms_generaot_producto_cih_url');
        $this->strUrlReversaPreplanificaMs  = $container->getParameter('ws_ms_reversa_preplanificacion_cih_url');
    }  
    
    /**
     * Devuelve los Tipos de Contrato activos de la empresa dada
     * @param string $codEmpresa
     * @param string $idKey key a usar en el array para el id del tipo contrato
     * @param string $descripcionKey key a usar en el array para la descripcion del tipo contrato
     * @return array de arrays id/descripcion
     * @see \telconet\schemaBundle\Entity\AdmiTipoContrato
     */
    public function obtenerTiposContrato($codEmpresa, $idKey = 'id', $descripcionKey = 'descripcion') {
        
        $list = $this->emcom->getRepository('schemaBundle:AdmiTipoContrato')->findTipoContratoPorEstadoPorEmpresa('Activo',$codEmpresa);
        $arreglo = array();
        /* @var $value \telconet\schemaBundle\Entity\AdmiTipoContrato */
        foreach ($list as $value):
        $arreglo[] = array($idKey => $value->getId(), $descripcionKey => $value->getDescripcionTipoContrato());
        endforeach;
        return $arreglo;
    }

    /** 
     * Función que crea el contrato por microservicios
     * 
     * @author : Néstor Naula <nnaulal@telconet.ec>
     * @version 1.1 18-10-2020
     * 
     * @param 
     *         string $strTokenCas
     *         array $datosContrato
     * @throws Exception
     * @return $arrayResultado
     * 
     */
    public function crearContratoMS($arrayParametrosContrato)
    {
        try
        {
            $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                        'tokencas: '.$arrayParametrosContrato['token'])
                                       ); 
            $strJsonData        = json_encode($arrayParametrosContrato);
            $arrayResponseJson  = $this->serviceRestClient->postJSON($this->strUrlCrearContratoMs, $strJsonData , $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);

            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 
            && isset($strJsonRespuesta['status'])
            && isset($strJsonRespuesta['message']) )
            {   
                $arrayResponse = array('strStatus' => $strJsonRespuesta['code'],
                                       'strMensaje'=> $strJsonRespuesta['message']);
                $arrayResultado = $arrayResponse;
            }
            else
            {
                $arrayResultado['strStatus']      = "ERROR";
                if(empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['strMensaje']  = "No Existe Conectividad con el WS MS CORE CONTRATO DIGITAL.";
                }
                else
                {
                    $arrayResultado['strMensaje']  = $strJsonRespuesta['message'];
                }
            }

            return $arrayResultado ;
        }
        catch(\Exception $e)
        {
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje'     =>$strRespuesta);
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoContratoService.crearContratoMS',
                                            'Error InfoContratoService.crearContratoMS:'.$e->getMessage(),
                                            $strUserMod,
                                            $strIpMod); 
            return $arrayResultado;
        }
    }

    /** 
     * Función que reenvia el pin al cliente por microservicios
     * 
     * @author : Néstor Naula <nnaulal@telconet.ec>
     * @version 1.1 18-10-2020
     * 
     * @param 
     *         string $strTokenCas
     *         string $personaEmpresaRolId
     * @throws Exception
     * @return $arrayResultado
     * 
     */
    public function reenviarPinMS($arrayParametrosContrato)
    {
        try
        {
            $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                        'tokencas: '.$arrayParametrosContrato['token'])
                                       ); 
            $strJsonData        = json_encode($arrayParametrosContrato);
            $arrayResponseJson  = $this->serviceRestClient->postJSON($this->strUrlReenviarPinMs, $strJsonData , $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);

            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 
            && isset($strJsonRespuesta['status'])
            && isset($strJsonRespuesta['message']) )
            {   
                $arrayResponse = array('strStatus' => $strJsonRespuesta['code'],
                                       'strMensaje'=> $strJsonRespuesta['message']);
                $arrayResultado = $arrayResponse;
            }
            else
            {
                $arrayResultado['strStatus']      = "ERROR";
                if(empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['strMensaje']  = "No Existe Conectividad con el WS MS CORE PIN.";
                }
                else
                {
                    $arrayResultado['strMensaje']  = $strJsonRespuesta['message'];
                }
            }

            return $arrayResultado ;
        }
        catch(\Exception $e)
        {
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje'     =>$strRespuesta);
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoContratoService.reenviarPinMS',
                                            'Error InfoContratoService.reenviarPinMS:'.$e->getMessage(),
                                            $strUserMod,
                                            $strIpMod); 
            return $arrayResultado;
        }
    }

    /** 
     * Función que autoriza el contrato por microservicios
     * 
     * @author : Néstor Naula <nnaulal@telconet.ec>
     * @version 1.1 18-10-2020
     * 
     * @param 
     *         string $strTokenCas
     *         string $strPin
     * @throws Exception
     * @return $arrayResultado
     * 
     */
    public function autorizarContratoMS($arrayParametrosContrato)
    {
        try
        {
            $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                        'tokencas: '.$arrayParametrosContrato['token'])
                                       ); 
            $strJsonData        = json_encode($arrayParametrosContrato);
            $arrayResponseJson  = $this->serviceRestClient->postJSON($this->strUrlAutorizarContratoMs, $strJsonData , $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);

            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 
            && isset($strJsonRespuesta['status'])
            && isset($strJsonRespuesta['message']) )
            {   
                $arrayResponse = array('strStatus' => $strJsonRespuesta['code'],
                                       'strMensaje'=> $strJsonRespuesta['message']);
                $arrayResultado = $arrayResponse;
            }
            else
            {
                $arrayResultado['strStatus']      = "ERROR";
                if(empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['strMensaje']  = "No Existe Conectividad con el WS MS COMP DIGITAL.";
                }
                else
                {
                    $arrayResultado['strMensaje']  = $strJsonRespuesta['message'];
                }
            }

            return $arrayResultado ;
        }
        catch(\Exception $e)
        {
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje'     =>$strRespuesta);
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoContratoService.reenviarPinMS',
                                            'Error InfoContratoService.reenviarPinMS:'.$e->getMessage(),
                                            $strUserMod,
                                            $strIpMod); 
            return $arrayResultado;
        }
    }
    
    /**
     * Crea un contrato para el cliente segun la informacion indicada en el array datos_form
     * Se llama al service que realiza la encriptacion del numero de cuenta tarjeta 
     * @author : apenaherrera          
     * @version 1.1 modificado 13-02-2015
     * @param string $codEmpresa
     * @param string $prefijoEmpresa
     * @param integer $idOficina
     * @param string $usrCreacion
     * @param string $clientIp
     * @param array $datos_form
     * @param array $check (nullable)
     * @param array $clausula (nullable)
     * @throws Exception
     * @return \telconet\schemaBundle\Entity\InfoContrato
     * 
     * @author : Edgar Holguin <eholguin@telconet.ec>
     * @version 1.2 22-07-2016 Se agrega generacion de archivos pdf en el ingreso de imagenes relacionadas a un contrato 
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec> 
     * @version 1.1 07-01-2016 - Se agregan ciertos validaciones para que acepte un contrato de tipo = "VEHICULO"
     * donde sólo se ingresa el contratista guardado en PersonaEmpresaRolId, 
     * la fecha de inicio de contrato guardado en feAprobacion
     * y la fecha de fin de contrato guardado en feFinContrato
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec> 
     * @version 1.2 10-08-2016 - Se corrige la numeración para los contratos de transporte de acuerdo al id de la oficina del empleado
     * 
     * Agregado nuevo parametro origen para idenficar el medio por el cual se ha creado el contrato
     * @param $arrayParametrosContrato 
     *        [
     *              $codEmpresa     => Codigo empresa 
     *              $prefijoEmpresa => Prefijo Empresa
     *              $idOficina      => Id Oficina
     *              $usrCreacion    => Usuario Creacion
     *              $clientIp       => Cliente IP
     *              $datos_form     => Datos del formulario
     *              $check          => NULL, 
     *              $clausula       => Clausulas, puede ser NULL, 
     *              $origen         => Web o Movil o NULL
     *        ]
     * @author Modificado: Veronica Carrasco <vcarrasco@telconet.ec> 
     * @version 1.3 30-08-2016 - Se agrega origen en la creacion de un contrato
     * 
     * 
     * Actualizacion: Se corrige validaciones de ingreso 
     * de contratos con forma de pago debito o tarjeta de credito
     * @author Andres Montero <amontero@telconet.ec> 
     * @version 1.4 19-04-2017
     * 
     * Actualización: Se modifica envio de parametros en $arrayParametrosValidaCtaTarj a la función validarNumeroTarjetaCta
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.5 07-07-2017
     *
     * Actualización: Se agregan códigos para identificar el tipo de excepción que se genera.
     * @author Edgar Pin <epin@telconet.ec>
     * @version 1.6 02-09-2019
     * 
     * Actualización: Se agrega funcionalidad para los adendum, se llena número de contrato en la InfoAdendum
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.7 26-09-2019
     * 
     * Actualización: Se agrega funcionalidad para verificar la fecha de prefactibilidad para la creación de contrato
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.8 29-10-2020
     *
     * Actualización: En el caso que no se puedan generar los documentos del contrato del nfs se realiza un rollback a la
     *                transacción se retorna el mensaje de error al usuario.
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.9 23-04-2021
     * 
     * Actualización: Se agrega caracteristica al contrato si es fisico o digital para la empresa MEGADATOS CRS y normal
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 2.0 29-10-2020
     * 
     * Actualización: Se agrega mejor para la obtención de la numeración del contrato sea primero y no se bloquee ni de una numeración duplicada
     * 
     * 
     * Actualización: consumo de ws para la subida de archivos y la generacion de pdf en imagenes con el metodo boolGenerarArchivoPdfNfs
     * @author Jorge Veliz <jlveliz@telconet.ec>
     * @version 2.1 26-06-2021 
     *     
     * @author Joel Broncano <jbroncano@telconet.ec>
     * @version 2.2 20-04-2023   soporte EN
     */

    public function crearContrato($arrayParametrosContrato)
    {
        $codEmpresa     = $arrayParametrosContrato['codEmpresa'];
        $prefijoEmpresa = $arrayParametrosContrato['prefijoEmpresa']; 
        $idOficina      = $arrayParametrosContrato['idOficina']; 
        $usrCreacion    = $arrayParametrosContrato['usrCreacion']; 
        $clientIp       = $arrayParametrosContrato['clientIp']; 
        $datos_form     = $arrayParametrosContrato['datos_form'];
        $check          = isset($arrayParametrosContrato['check']) ? $arrayParametrosContrato['check'] : NULL; 
        $clausula       = isset($arrayParametrosContrato['clausula']) ? $arrayParametrosContrato['clausula'] : NULL;
        $origen         = isset($arrayParametrosContrato['origen']) ? $arrayParametrosContrato['origen'] : NULL;
        $arrayServicios = $arrayParametrosContrato['servicios'];
        $arrayPromocion = $arrayParametrosContrato['arrayPromocion'];
        // FIXME: se deberia validar que no haya un contrato existente vigente para la persona        
        $i = 0;
        if ($check)
        {
            foreach ($check as $ch):
            $arreglo[$i]=$ch;
            $i++;
            endforeach;
        }
        $i = 0;
        if ($clausula)
        {
            foreach ($clausula as $clau):
            $arregloc[$i]=$clau;
            $i++;
            endforeach;
        }
        $fecha_creacion = new \DateTime('now');      
        $strNumeroCtaTarjeta = "";
        $strError            ='';
        try
        {  

            //Obtener la numeracion de la tabla Admi_numeracion
            $secuencia_asig     = null;
            $numero_de_contrato = null;
            $codigoNumeracion   = 'CON';
            $datosNumeracion    = null;
            if(isset($datos_form['codigoNumeracionVE']))
            {
                $codigoNumeracion       = $datos_form['codigoNumeracionVE'];
                $datosNumeracion        = $this->emcom->getRepository('schemaBundle:AdmiNumeracion')
                                              ->getNumeracionContratoVehiculo($codEmpresa,$idOficina,$codigoNumeracion);
                
            }
            else
            {
                $datosNumeracion    = $this->emcom->getRepository('schemaBundle:AdmiNumeracion')
                                                  ->findByEmpresaYOficina($codEmpresa,$idOficina,$codigoNumeracion);
            }
            if( $datosNumeracion )
            {
                $secuencia_asig     = str_pad($datosNumeracion->getSecuencia(),7, "0", STR_PAD_LEFT);
                $numero_de_contrato = $datosNumeracion->getNumeracionUno()."-".$datosNumeracion->getNumeracionDos()."-".$secuencia_asig;
                $numero_act=($datosNumeracion->getSecuencia()+1);
                $datosNumeracion->setSecuencia($numero_act);
                $this->emcom->persist($datosNumeracion);
                $this->emcom->flush();                
            }
            $this->emcom->getConnection()->beginTransaction();
            $this->emComunicacion->getConnection()->beginTransaction();        
            $entity = new InfoContrato();           
            $entity->setValorAnticipo($datos_form['valorAnticipo']);
            $entity->setNumeroContratoEmpPub($datos_form['numeroContratoEmpPub']); 
            $entity->setNumeroContrato($numero_de_contrato);               
            if ($datos_form['feInicioContrato'])
            {
                if ($datos_form['feInicioContrato'] instanceof \DateTime)
                {
                    $entity->setFeAprobacion($datos_form['feInicioContrato']);
                }
            }
            /*2012-11-09*/
            if ($datos_form['feFinContratoPost'] instanceof \DateTime)
            {
                $entity->setFeFinContrato($datos_form['feFinContratoPost']);
            }
            else
            {
                $start_exp = explode("-",$datos_form['feFinContratoPost']);
                $fechaFin  = date("Y-m-d H:i:s", strtotime($start_exp[0]."-".$start_exp[1]."-".$start_exp[2]));
                $entity->setFeFinContrato(date_create($fechaFin));
            }
            //Ahora es persona empresa rol id
            $personaId           = $datos_form['idcliente'];
            $personaEmpresaRolId = $datos_form['personaEmpresaRolId'];
            //$tiporolpersona=$datos_form['tiporol'];
            $descRol             = 'Pre-cliente';
            if ($prefijoEmpresa == 'MD' || $prefijoEmpresa == 'EN')
            {
                if($datos_form['strCambioRazonSocial'] != 'S')
                {
                    /* @var $entityPersonaEmpFormaPago \telconet\schemaBundle\Entity\InfoPersonaEmpFormaPago */
                    $entityPersonaEmpFormaPago = $this->servicePreCliente->getDatosPersonaEmpFormaPago($personaId, $codEmpresa);
                    $formaPagoId               = null;
                    $tipoCuentaId              = null;
                    $bancoTipoCuentaId         = null;
                    if ( $entityPersonaEmpFormaPago )
                    {
                        $formaPagoId       = $entityPersonaEmpFormaPago->getFormaPagoId()->getId();
                        $tipoCuentaId      = ($entityPersonaEmpFormaPago->getTipoCuentaId() ? $entityPersonaEmpFormaPago->getTipoCuentaId()->getId() : null);
                        $bancoTipoCuentaId = ($entityPersonaEmpFormaPago->getBancoTipoCuentaId() ? $entityPersonaEmpFormaPago->getBancoTipoCuentaId()->getId() : null);
                    }
                    $datos_form['formaPagoId']       = $formaPagoId;
                    $datos_form['tipoCuentaId']      = $tipoCuentaId;
                    $datos_form['bancoTipoCuentaId'] = $bancoTipoCuentaId;
                }

                $objEntityCaract = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                        ->findOneBy(
                                                array("descripcionCaracteristica"   => 'FORMA_REALIZACION_CONTRATO',
                                                      "estado"                      => 'Activo'));

                if(is_object($objEntityCaract))
                {
                    $entityCaractContrato = new InfoContratoCaracteristica();
                    $entityCaractContrato->setCaracteristicaId($objEntityCaract);
                    $entityCaractContrato->setContratoId($entity);
                    $entityCaractContrato->setEstado('Activo');
                    $entityCaractContrato->setFeCreacion(new \DateTime('now'));
                    $entityCaractContrato->setUsrCreacion($usrCreacion);
                    $entityCaractContrato->setIpCreacion('127.0.0.1');
                    $entityCaractContrato->setValor1('FISICO');
                    $entityCaractContrato->setValor2('I');

                    $this->emcom->persist($entityCaractContrato);
                }
            }
            //$persona=$this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getPersonaEmpresaRolPorPersonaPorTipoRol($personaId, $descRol,$codEmpresa);
            $persona = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($personaEmpresaRolId);
            $forma   = $this->emcom->getRepository('schemaBundle:AdmiFormaPago')->find($datos_form['formaPagoId']);
            $tipo    = $this->emcom->getRepository('schemaBundle:AdmiTipoContrato')->find($datos_form['tipoContratoId']);            
            $entity->setPersonaEmpresaRolId($persona);
            $entity->setFormaPagoId($forma);
            $entity->setTipoContratoId($tipo);
            $entity->setFeCreacion($fecha_creacion);
            $arrayAdendum = $this->emcom->getRepository('schemaBundle:InfoAdendum')->findBy(array("puntoId" => $datos_form['puntoId']));
            foreach ($arrayAdendum as $entityAdendum) 
            {
                if ( in_array($entityAdendum->getServicioId(), $arrayServicios))
                {    
                    //valido si el servicio paso por factibilidad manual
                    $entityServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                  ->find($entityAdendum->getServicioId());
                    if ($entityServicio->getPlanId())
                    {
                        $entityServicioHis = $this->emcom->getRepository('schemaBundle:InfoServicioHistorial')
                                                            ->findOneBy(array("servicioId" => $entityAdendum->getServicioId(),
                                                                            "estado" => 'PreFactibilidad'));
                        if ($entityServicioHis)
                        {
                            $objFechaPreFactibilidad = $entityServicioHis->getFeCreacion();

                            //valido que la fecha mas los dias de parametro no sea mayor que la fecha actual
                            $arrayParametrosDet     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                      ->getOne('DIAS_ESPERA_FACTIBILIDAD_MANUAL',
                                     'COMERCIAL', 
                                     '', '', '', '', '', '', '', 
                                    ''); 
                            if ($arrayParametrosDet)
                            {
                                $objFecha      = new \DateTime('now');
                                $objFechaPreFactibilidad->add(new \DateInterval("P".$arrayParametrosDet['valor1']."D"));
                                if ($objFecha < $objFechaPreFactibilidad)
                                {
                                    $objFechaPreFactibilidad->sub(new \DateInterval("P".$arrayParametrosDet['valor1']."D"));
                                    $entity->setFeCreacion($entityServicioHis->getFeCreacion());
                                }
                            }                                   
                        }                                        
                    }   
                }
            }             
            $entity->setUsrCreacion($usrCreacion);
            
            if(isset($datos_form['valorEstado']))
            {
                $entity->setEstado($datos_form['valorEstado']);
            }
            else
            {
                if($datos_form['strCambioRazonSocial'] != 'S')
                {
                    $entity->setEstado('Pendiente');
                }
                else
                {
                    $entity->setEstado('Activo');
                }
                
            }
            $entity->setOrigen($origen);
            $entity->setIpCreacion($clientIp);
                                 
            $this->emcom->persist($entity);
            $this->emcom->flush();
            //aqui estaba la numeracion edg
            if (isset($arreglo) && isset($arregloc))
            {
                for ($i=0;$i<sizeof($arreglo);$i++)
                {
                    $entityClausula  = new InfoContratoClausula();
                    $entityClausula->setContratoId($entity);
                    $entityClausula->setDescripcionClausula($arregloc[$i]);
                    $entityClausula->setClausulaId($arreglo[$i]);
                    $entityClausula->setEstado('Pendiente');
                    $entityClausula->setFeCreacion($fecha_creacion);
                    $entityClausula->setUsrCreacion($usrCreacion);
                    $this->emcom->persist($entityClausula);
                    $this->emcom->flush();
                }
            }
            $entityDatoAdicional  = new InfoContratoDatoAdicional();
            $entityDatoAdicional->setContratoId($entity);
            if(!isset($datos_form['convenioPago']) || $datos_form['convenioPago']=='')
            {
                $entityDatoAdicional->setConvenioPago('N');
            }
            else
            {
                $entityDatoAdicional->setConvenioPago('S');
            }
            
            if(!isset($datos_form['esTramiteLegal']) || $datos_form['esTramiteLegal']=='')
            {
                $entityDatoAdicional->setEsTramiteLegal('N');
            }
            else
            {
                $entityDatoAdicional->setEsTramiteLegal('S');
            }
        
            if(!isset($datos_form['esVip']) || $datos_form['esVip']=='')
            {
                $entityDatoAdicional->setEsVip('N');
            }
            else
            {
                $entityDatoAdicional->setEsVip('S');
            }
            
            if(!isset($datos_form['permiteCorteAutomatico']) || $datos_form['permiteCorteAutomatico']=='')
            {
                $entityDatoAdicional->setPermiteCorteAutomatico('N');
            }
            else
            {
                $entityDatoAdicional->setPermiteCorteAutomatico('S');
            }
        
            if(!isset($datos_form['fideicomiso']) || $datos_form['fideicomiso']=='')
            {
                $entityDatoAdicional->setFideicomiso('N');
            }
            else
            {
                $entityDatoAdicional->setFideicomiso('S');
            }
            if($datos_form['tiempoEsperaMesesCorte']=='' || !isset($datos_form['fideicomiso']))
            {
                $entityDatoAdicional->setTiempoEsperaMesesCorte(1);
            }
            else
            {
                $entityDatoAdicional->setTiempoEsperaMesesCorte($datos_form['tiempoEsperaMesesCorte']);
            }
            $entityDatoAdicional->setFeCreacion($fecha_creacion);
            $entityDatoAdicional->setUsrCreacion($usrCreacion);
            $entityDatoAdicional->setIpCreacion($clientIp);
            $this->emcom->persist($entityDatoAdicional);
            $this->emcom->flush();              
            //Informacion de las formas de pagos con datos adicionales
            if ($forma->getCodigoFormaPago()=='DEB' || $forma->getCodigoFormaPago()=='TARC')
            {
                if (!isset($datos_form['tipoCuentaId']) || empty($datos_form['tipoCuentaId']))
                {
                    throw new \Exception('No se pudo guardar el contrato - El tipo de Cuenta / Tarjeta es un campo obligatorio', 206); 
                }
                if (!isset($datos_form['bancoTipoCuentaId']) || empty($datos_form['bancoTipoCuentaId']))
                {
                    throw new \Exception('No se pudo guardar el contrato - El banco es un campo obligatorio', 206); 
                }
                if (!isset($datos_form['numeroCtaTarjeta']) || empty($datos_form['numeroCtaTarjeta']))
                {
                    throw new \Exception('No se pudo guardar el contrato - El Numero de Cuenta / Tarjeta es un campo obligatorio', 206); 
                }
                 //Llamo a funcion para validar numero de cuenta/tarjeta            
                $arrayParametrosValidaCtaTarj                          = array();
                $arrayParametrosValidaCtaTarj['intTipoCuentaId']       = $datos_form['tipoCuentaId'];
                $arrayParametrosValidaCtaTarj['intBancoTipoCuentaId']  = $datos_form['bancoTipoCuentaId'];
                $arrayParametrosValidaCtaTarj['strNumeroCtaTarjeta']   = $datos_form['numeroCtaTarjeta'];
                $arrayParametrosValidaCtaTarj['intFormaPagoId']          = $datos_form['formaPagoId'];
                if(isset($datos_form['codigoVerificacion']))
                {
                    $arrayParametrosValidaCtaTarj['strCodigoVerificacion'] = $datos_form['codigoVerificacion'];
                }
                $arrayParametrosValidaCtaTarj['strCodEmpresa']         = $codEmpresa;
                $arrayValidaciones   = $this->validarNumeroTarjetaCta($arrayParametrosValidaCtaTarj);
                if($arrayValidaciones)
                {    
                    foreach($arrayValidaciones as $key => $mensaje_validaciones)
                    {
                        foreach($mensaje_validaciones as $key_msj => $value)
                        {                      
                            $strError = $strError.$value.".\n";                        
                        }
                    }
                    throw new \Exception("No se pudo guardar el contrato - " . $strError, 206);
                }
                $bancoTipoCta = $this->emcom->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($datos_form['bancoTipoCuentaId']);
                $tipoCta = $this->emcom->getRepository('schemaBundle:AdmiTipoCuenta')->find($datos_form['tipoCuentaId']);
                if($bancoTipoCta->getEsTarjeta() == 'S')
                {
                    if(!$datos_form['mesVencimiento'] || !$datos_form['anioVencimiento'])
                    {
                        throw new \Exception('No se pudo guardar el contrato - '
                            . 'El Anio y mes de Vencimiento de la tarjeta son campos obligatorios', 206); 
                    }
                }
                $entityFormaPago  = new InfoContratoFormaPago();
                $entityFormaPago->setContratoId($entity);                

                //Llamo a funcion que realiza encriptado del numero de cuenta
                $strNumeroCtaTarjeta = $this->serviceCrypt->encriptar($datos_form['numeroCtaTarjeta']);
                if(!empty($strNumeroCtaTarjeta))
                {
                    $entityFormaPago->setNumeroCtaTarjeta($strNumeroCtaTarjeta);
                }
                else
                {
                    throw new \Exception("No se pudo guardar el contrato - Numero de cuenta/tarjeta es incorrecto", 206);
                }

                if (!isset($datos_form['titularCuenta']) || empty($datos_form['titularCuenta']))
                {
                    throw new \Exception('No se pudo guardar el contrato - El titular de cuenta es un campo obligatorio', 206);
                }
                $entityFormaPago->setTitularCuenta($datos_form['titularCuenta']);
                if($bancoTipoCta->getEsTarjeta() == 'S')
                {
                    $entityFormaPago->setAnioVencimiento($datos_form['anioVencimiento']);
                    $entityFormaPago->setMesVencimiento($datos_form['mesVencimiento']);
                    $entityFormaPago->setCodigoVerificacion($datos_form['codigoVerificacion']);
                }
                $entityFormaPago->setTipoCuentaId($tipoCta);
                $entityFormaPago->setBancoTipoCuentaId($bancoTipoCta);
                $entityFormaPago->setUsrCreacion($usrCreacion);
                $entityFormaPago->setFeCreacion($fecha_creacion);
                $entityFormaPago->setUsrCreacion($usrCreacion);
                $entityFormaPago->setIpCreacion($clientIp);
                $entityFormaPago->setEstado('Activo');
                $this->emcom->persist($entityFormaPago);
                $this->emcom->flush();
            } 
            //Hago el update a InfoAdendum
            $arrayAdendum = $this->emcom->getRepository('schemaBundle:InfoAdendum')->findBy(array("puntoId" => $datos_form['puntoId']));
            foreach ($arrayAdendum as $entityAdendum) 
            {
                if ( in_array($entityAdendum->getServicioId(), $arrayServicios))
                {    
                    $entityAdendum->setContratoId($entity->getId());
                    $entityAdendum->setFormaPagoId($datos_form['formaPagoId']);
                    $entityAdendum->setTipoCuentaId($datos_form['tipoCuentaId']);
                    $entityAdendum->setBancoTipoCuentaId($datos_form['bancoTipoCuentaId']);
                    $entityAdendum->setNumeroCtaTarjeta($strNumeroCtaTarjeta);
                    $entityAdendum->setTitularCuenta($datos_form['titularCuenta']);
                    $entityAdendum->setMesVencimiento($datos_form['mesVencimiento']);
                    $entityAdendum->setAnioVencimiento($datos_form['anioVencimiento']);
                    $entityAdendum->setCodigoVerificacion($datos_form['codigoVerificacion']);
                    $entityAdendum->setFeCreacion($entity->getFeCreacion());

                    $entityAdendum->setTipo("C");
                    $this->emcom->persist($entityAdendum); 
 
                }
            }                
            $this->emcom->flush();
            $boolBandNfs = isset($arrayParametrosContrato['bandNfs']) ? $arrayParametrosContrato['bandNfs'] : 0;
            if (!$boolBandNfs && $this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->commit();
            }
            //Guardo files asociados al contrato                      
            $datos_form_files    = $datos_form['datos_form_files'];
            $arrayTipoDocumentos = $datos_form['arrayTipoDocumentos']; 
            if ($prefijoEmpresa == 'EN')
            { 
                foreach ($datos_form_files as $arrayImagenes)
                {
                    if($arrayImagenes[0]==null ||  empty($arrayImagenes[0]))
                    {
                        throw new \Exception('Por favor suba el contrato es obligatorio', 206); 
                    }
                }
            }                                   
            $i=0;
            if(isset($arrayParametrosContrato['bandNfs']) && $arrayParametrosContrato['bandNfs'])
            {
                $arrayParmGuardarNFS = array('arrayDatosFormFiles' => $datos_form_files,
                                             'arrayFileBase64'     => $datos_form['files'],
                                             'arrayTipoDocumentos' => $arrayTipoDocumentos,
                                             'strUsuario'          => $usrCreacion,
                                             'prefijoEmpresa'      => $prefijoEmpresa,
                                             'strApp'              => $arrayParametrosContrato['strApp'],
                                             'bandNfs'             => $arrayParametrosContrato['bandNfs'],
                                             'objPerEmpRol'        => $persona
                                            );
                $arrayRespuestaNfs = $this->guardarArchivoNFS($arrayParmGuardarNFS);
                if(isset($arrayRespuestaNfs) && !empty($arrayRespuestaNfs))
                {
                    if ($this->emcom->getConnection()->isTransactionActive())
                    {
                        $this->emcom->getConnection()->commit();
                    }
                    foreach($arrayRespuestaNfs as $arrayValor)
                    {
                        $objInfoDocumento = new InfoDocumento();
                        $objInfoDocumento->setNombreDocumento($arrayValor['strNombreArchivo']);
                        $objInfoDocumento->setUbicacionLogicaDocumento($arrayValor['strNombreArchivo']);
                        $objInfoDocumento->setFechaDocumento(new \DateTime('now'));
                        $objInfoDocumento->setUsrCreacion($usrCreacion);
                        $objInfoDocumento->setFeCreacion(new \DateTime('now'));
                        $objInfoDocumento->setIpCreacion($clientIp);
                        $objInfoDocumento->setEstado('Activo');
                        $objInfoDocumento->setMensaje("Archivo agregado al contrato # "
                                                        . $numero_de_contrato );
                        $objInfoDocumento->setEmpresaCod($codEmpresa);
                        $objInfoDocumento->setPath($arrayValor['strUrl']);
                        $objInfoDocumento->setFile(null);
                        $objInfoDocumento->setUbicacionFisicaDocumento($arrayValor['strUrl']);


                        if(isset($arrayValor['strTipoDocGeneral']))
                        {
                            $objTipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                                       ->find($arrayValor['strTipoDocGeneral']);
                            if( $objTipoDocumentoGeneral != null )
                            {
                                $objInfoDocumento->setTipoDocumentoGeneralId( $objTipoDocumentoGeneral->getId() );
                            }
                        }

                        $objTipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                                 ->findOneByExtensionTipoDocumento(strtoupper($arrayValor['strTipoDocumento']));
                        if( $objTipoDocumento != null)
                        {
                            $objInfoDocumento->setTipoDocumentoId($objTipoDocumento);
                        }
                        else
                        {
                            $objAdmiTipoDocumento = new AdmiTipoDocumento();
                            $objAdmiTipoDocumento->setExtensionTipoDocumento(strtoupper($arrayValor['strTipoDocumento']));
                            $objAdmiTipoDocumento->setTipoMime(strtoupper($arrayValor['strTipoDocumento']));
                            $objAdmiTipoDocumento->setDescripcionTipoDocumento('ARCHIVO FORMATO '.strtoupper($arrayValor['strTipoDocumento']));
                            $objAdmiTipoDocumento->setEstado('Activo');
                            $objAdmiTipoDocumento->setUsrCreacion($usrCreacion);
                            $objAdmiTipoDocumento->setFeCreacion(new \DateTime('now'));
                            $this->emComunicacion->persist($objAdmiTipoDocumento);
                            $this->emComunicacion->flush();
                            $objInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);
                        }
                        $objInfoDocumento->setContratoId($entity->getId());
                        $this->emComunicacion->persist($objInfoDocumento);
                        $this->emComunicacion->flush();

                        $objInfoDocumentoRelacion = new InfoDocumentoRelacion();
                        $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());
                        $objInfoDocumentoRelacion->setModulo('COMERCIAL');
                        $objInfoDocumentoRelacion->setContratoId($entity->getId());
                        $objInfoDocumentoRelacion->setEstado('Activo');
                        $objInfoDocumentoRelacion->setFeCreacion(new \DateTime('now'));
                        $objInfoDocumentoRelacion->setUsrCreacion($usrCreacion);

                        $this->emComunicacion->persist($objInfoDocumentoRelacion);
                        $this->emComunicacion->flush();
                    }
                }
                else
                {
                    throw new \Exception('No se pudo guardar el contrato - '
                            . 'Error al momento de crear los archivos del contrato', 206);
                }
            }
            else
            {
                foreach ($datos_form_files as $key => $imagenes)
                {
                    foreach ( $imagenes as $key_imagen => $value)
                    {
                        if($value)
                        {

                            $objInfoDocumento = new InfoDocumento();
                            $objInfoDocumento->setFile( $value );
                            $objInfoDocumento->setNombreDocumento("documento_digital");
                            $objInfoDocumento->setFechaDocumento( $fecha_creacion );
                            $objInfoDocumento->setUsrCreacion( $usrCreacion );
                            $objInfoDocumento->setFeCreacion( $fecha_creacion );
                            $objInfoDocumento->setIpCreacion( $clientIp );
                            $objInfoDocumento->setEstado( 'Activo' );
                            $objInfoDocumento->setMensaje( "Archivo agregado al contrato # ".$numero_de_contrato );
                            $objInfoDocumento->setEmpresaCod( $codEmpresa );

                            $objTipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                                       ->find($arrayTipoDocumentos[$key_imagen]);
                            if( $objTipoDocumentoGeneral != null )
                            {
                                $objInfoDocumento->setTipoDocumentoGeneralId( $objTipoDocumentoGeneral->getId() );
                            }
                            $i++;
                            if ( $objInfoDocumento->getFile() )
                            {
                                $objInfoDocumento->preUpload();
                                $strNombreArchivo = $objInfoDocumento->getUbicacionLogicaDocumento();
                                $strNombreApp       = !empty($arrayParametrosContrato['strApp']) ? $arrayParametrosContrato['strApp'] : 'TelcosWeb';
                                $arrayPathAdicional = [];
                                $strSubModulo = "ContratoDocumentoDigital";
                                
                                $arrayParamNfs          = array(
                                    'prefijoEmpresa'       => $prefijoEmpresa,
                                    'strApp'               => $strNombreApp,
                                    'strSubModulo'         => $strSubModulo,
                                    'arrayPathAdicional'   => $arrayPathAdicional,
                                    'strBase64'            => base64_encode(file_get_contents($objInfoDocumento->getFile())),
                                    'strNombreArchivo'     => $strNombreArchivo,
                                    'strUsrCreacion'       => $usrCreacion);
                                $arrayRespNfs = $this->utilService->guardarArchivosNfs($arrayParamNfs);
                
                                if(isset($arrayRespNfs))
                                {
                                    if($arrayRespNfs['intStatus'] == 200)
                                    {
                                        $strNuevoNombreArchivo = $objInfoDocumento->getNombreDocumento().'.'.$objInfoDocumento->getExtension();
                                        $objInfoDocumento->setNombreDocumento($strNuevoNombreArchivo);
                                        $objInfoDocumento->setFile(null);
                                        $objInfoDocumento->setUbicacionFisicaDocumento($arrayRespNfs['strUrlArchivo']);

                                    }
                                    else
                                    {
                                        throw new \Exception('No se pudo crear el contrato, error al cargar el archivo digital');
                                    }
                                }
                                else
                                {
                                    throw new \Exception('Error, no hay respuesta del WS para almacenar el documento');
                                }

                            }

                            $objTipoDocumento = $this->emComunicacion
                                                    ->getRepository('schemaBundle:AdmiTipoDocumento')
                                                    ->findOneByExtensionTipoDocumento(strtoupper($objInfoDocumento->getExtension()));

                            if( $objTipoDocumento != null)
                            {
                                $objInfoDocumento->setTipoDocumentoId($objTipoDocumento);
                            }
                            else
                            {   //Inserto registro con la extension del archivo a subirse
                                $objAdmiTipoDocumento = new AdmiTipoDocumento();
                                $objAdmiTipoDocumento->setExtensionTipoDocumento(strtoupper($objInfoDocumento->getExtension()));
                                $objAdmiTipoDocumento->setTipoMime(strtoupper($objInfoDocumento->getExtension()));
                                $objAdmiTipoDocumento->setDescripcionTipoDocumento('ARCHIVO FORMATO '.strtoupper($objInfoDocumento->getExtension()));
                                $objAdmiTipoDocumento->setEstado('Activo');
                                $objAdmiTipoDocumento->setUsrCreacion( $usrCreacion );
                                $objAdmiTipoDocumento->setFeCreacion( $fecha_creacion );
                                $this->emComunicacion->persist( $objAdmiTipoDocumento );
                                $this->emComunicacion->flush();
                                $objInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);
                            }
                            $objInfoDocumento->setContratoId( $entity->getId() );
                            $this->emComunicacion->persist($objInfoDocumento);
                            $this->emComunicacion->flush();
                            $objInfoDocumentoRelacion = new InfoDocumentoRelacion();
                            $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());
                            $objInfoDocumentoRelacion->setModulo('COMERCIAL');
                            $objInfoDocumentoRelacion->setContratoId($entity->getId());
                            $objInfoDocumentoRelacion->setEstado('Activo');
                            $objInfoDocumentoRelacion->setFeCreacion($fecha_creacion);
                            $objInfoDocumentoRelacion->setUsrCreacion($usrCreacion);
                            $this->emComunicacion->persist($objInfoDocumentoRelacion);
                            $this->emComunicacion->flush();


                            if($objTipoDocumento)
                            {
                                if("ARCHIVO DE IMAGEN" === trim($objTipoDocumento->getDescripcionTipoDocumento()))
                                {
                                    $arrayNombreDocumento = explode(".",$objInfoDocumento->getPath());
                                    $strNombreDocumento   = $arrayNombreDocumento[0].".pdf";

                                    $arrayParametros = array ();
                                    $arrayParametros['strNombreDocuemnto']        = $strNombreDocumento;
                                    $arrayParametros['strUbicacionFisicaDoc']     = $objInfoDocumento->getUbicacionFisicaDocumento();
                                    $arrayParametros['strUsrCreacion']            = $usrCreacion;
                                    $arrayParametros['strClienteIp']              = $clientIp;
                                    $arrayParametros['strMensaje']                = "Archivo agregado al contrato # ".$numero_de_contrato;
                                    $arrayParametros['strCodEmpresa']             = $codEmpresa;
                                    $arrayParametros['intTipoDocumentoGeneralId'] = $objTipoDocumentoGeneral->getId();
                                    $arrayParametros['intContratoId']             = $entity->getId();
                                    $arrayParametros['emComunicacion']            = $this->emComunicacion;
                                    $arrayParametros['strPrefijoEmpresa']         = $prefijoEmpresa;
                                    $arrayParametros['strSubModulo']              = $strSubModulo;
                                    $arrayParametros['strNombreApp']              = $strNombreApp;


                                    if(!($this->boolGenerarArchivoPdfNfs($arrayParametros)))
                                    {
                                        throw new \Exception('No se pudo guardar el contrato - Error al generar archivo pdf.', 206);
                                    }

                                }
                            }
                        }
                    }
                }
            }
            $arrayResponseProm = $this->servicePromocion->guardarCodigoPromocional($arrayPromocion);
            if ($this->emComunicacion->getConnection()->isTransactionActive()){
                $this->emComunicacion->getConnection()->commit();
            }
            $this->emcom->getConnection()->close();
            $this->emComunicacion->getConnection()->close();  
            
            return $entity;
        }
        catch(\Exception $e)
        {
            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
            }                        
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }            
            $this->emcom->getConnection()->close();
            $this->emComunicacion->getConnection()->close();  
            throw $e;
        }
    }
   /**
     * Funcion que Guarda Archivos Digitales agregados al contrato 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @param interger $id // id del contrato 
     * @param string $codEmpresa     
     * @param integer $idOficina
     * @param string $usrCreacion
     * @param string $clientIp
     * @param array $datos_form
     * @throws Exception
     * @version 1.0 28-07-2014 
     * @author  Edgar Holguin <eholguin@telconet.ec>
     * @version 1.1 27-07-2016 Se agrega generacion de archivos pdf en el ingreso de imagenes relacionadas a un contrato 
     * @return \telconet\schemaBundle\Entity\InfoContrato
     */
    public function guardarArchivoDigital($id,$codEmpresa, $usrCreacion, $clientIp, $datos_form)
    {    
        
        $fecha_creacion = new \DateTime('now');      
        
        $this->emComunicacion->getConnection()->beginTransaction();
        try
        {  
            $objInfoContrato = $this->emcom->getRepository('schemaBundle:InfoContrato')->find($id);
            if( $objInfoContrato )
            {
                //Guardo files asociados al contrato                      
                $datos_form_files    = $datos_form['datos_form_files'];
                $arrayTipoDocumentos = $datos_form['arrayTipoDocumentos'];
                $i=0;
                foreach ($datos_form_files as $key => $imagenes)                 
                {  
                    foreach ( $imagenes as $key_imagen => $value) 
                    {        
                        if( $value )
                        {                            
                            $objInfoDocumento = new InfoDocumento();                             
                            $objInfoDocumento->setNombreDocumento("documento_digital");
                            $objInfoDocumento->setUploadDir(substr($this->fileRoot, 0, -1));
                            $objInfoDocumento->setFile( $value );   
                            $objInfoDocumento->setFechaDocumento( $fecha_creacion );                                                                 
                            $objInfoDocumento->setUsrCreacion( $usrCreacion );
                            $objInfoDocumento->setFeCreacion( $fecha_creacion );
                            $objInfoDocumento->setIpCreacion( $clientIp );
                            $objInfoDocumento->setEstado( 'Activo' );                                                           
                            $objInfoDocumento->setMensaje( "Archivo agregado al contrato # ".$objInfoContrato->getNumeroContrato() );                                        
                            $objInfoDocumento->setEmpresaCod( $codEmpresa );                        

                            $objTipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')->find($arrayTipoDocumentos[$key_imagen]);                                                                                                                                    
                            if( $objTipoDocumentoGeneral != null )
                            {            
                                $objInfoDocumento->setTipoDocumentoGeneralId( $objTipoDocumentoGeneral->getId() );                            
                            }                                                    
                            $i++;                        
                            if ( $objInfoDocumento->getFile() )
                            {
                                $objInfoDocumento->preUpload();
                                $objInfoDocumento->upload();
                            }                                                                           
                            $objTipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')->findOneByExtensionTipoDocumento(strtoupper($objInfoDocumento->getExtension()));                                    

                            if( $objTipoDocumento != null)
                            {
                                $objInfoDocumento->setTipoDocumentoId($objTipoDocumento);                                
                            }
                            else
                            {   //Inserto registro con la extension del archivo a subirse
                                $objAdmiTipoDocumento = new AdmiTipoDocumento(); 
                                $objAdmiTipoDocumento->setExtensionTipoDocumento(strtoupper($objInfoDocumento->getExtension()));
                                $objAdmiTipoDocumento->setTipoMime(strtoupper($objInfoDocumento->getExtension()));                            
                                $objAdmiTipoDocumento->setDescripcionTipoDocumento('ARCHIVO FORMATO '.strtoupper($objInfoDocumento->getExtension()));
                                $objAdmiTipoDocumento->setEstado('Activo');
                                $objAdmiTipoDocumento->setUsrCreacion( $usrCreacion );
                                $objAdmiTipoDocumento->setFeCreacion( $fecha_creacion );                        
                                $this->emComunicacion->persist( $objAdmiTipoDocumento );
                                $this->emComunicacion->flush(); 
                                $objInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);    
                            }                      
                            $objInfoDocumento->setContratoId( $objInfoContrato->getId() );
                            $this->emComunicacion->persist($objInfoDocumento);
                            $this->emComunicacion->flush();   

                            $objInfoDocumentoRelacion = new InfoDocumentoRelacion(); 
                            $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());                    
                            $objInfoDocumentoRelacion->setModulo('COMERCIAL'); 
                            $objInfoDocumentoRelacion->setContratoId($objInfoContrato->getId());        
                            $objInfoDocumentoRelacion->setEstado('Activo');                                                                                   
                            $objInfoDocumentoRelacion->setFeCreacion($fecha_creacion);                        
                            $objInfoDocumentoRelacion->setUsrCreacion($usrCreacion);
                            $this->emComunicacion->persist($objInfoDocumentoRelacion);                        
                            $this->emComunicacion->flush();
                            
                            
                            
                            if($objTipoDocumento)
                            {
                                if("ARCHIVO DE IMAGEN" === trim($objTipoDocumento->getDescripcionTipoDocumento()))
                                {
                                    $arrayNombreDocumento = explode(".",$objInfoDocumento->getPath());
                                    $strNombreDocumento   = $arrayNombreDocumento[0].".pdf"; 
                                    $strNumeroContrato    = $objInfoContrato->getNumeroContrato();
                                    
                                    $arrayParametros = array ();
                                    $arrayParametros['strNombreDocuemnto']        = $strNombreDocumento;
                                    $arrayParametros['strUbicacionFisicaDoc']     = $this->fileRoot.$strNombreDocumento;  
                                    $arrayParametros['strDirArchivo']             = $this->path_telcos.$this->fileRoot.$objInfoDocumento->getPath();  
                                    $arrayParametros['strUsrCreacion']            = $usrCreacion;   
                                    $arrayParametros['strClienteIp']              = $clientIp;  
                                    $arrayParametros['strMensaje']                = "Archivo agregado al contrato # ".$strNumeroContrato; 
                                    $arrayParametros['strCodEmpresa']             = $codEmpresa;
                                    $arrayParametros['intTipoDocumentoGeneralId'] = $objTipoDocumentoGeneral->getId();
                                    $arrayParametros['intContratoId']             = $objInfoContrato->getId(); 
                                    $arrayParametros['emComunicacion']            = $this->emComunicacion;  
                                    
                                    if(!($this->boolGenerarArchivoPdf($arrayParametros))) 
                                    {
                                        throw new \Exception('Error al generar archivo pdf.');
                                    }

                                }
                            }                            
                        }
                    }                       
                }
                if ($this->emComunicacion->getConnection()->isTransactionActive()){
                    $this->emComunicacion->getConnection()->commit();
                }                
                $this->emComunicacion->getConnection()->close();  
                return $objInfoContrato;
            }
       }
       catch(\Exception $e)
       {                                 
           if ($this->emComunicacion->getConnection()->isTransactionActive())
           {
               $this->emComunicacion->getConnection()->rollback();
           }                            
           $this->emComunicacion->getConnection()->close();  
           throw $e;
       }        
    }

    /**
     * Funcion que Guarda Archivos Digitales agregados al contrato 
     *
     * @author Kevin Mosquera Coronel <kmosquera@telconet.ec>
     * @param array    $arrayParametros
     * @throws Exception
     * @version 1.0 01-07-2021 Se agrega subida por el microservicio
     * @return \telconet\schemaBundle\Entity\InfoContrato
     */
    
    public function guardarArchivoDigitalNfs($arrayParametros)
    {   
        $intId                 = (int) ($arrayParametros['id']);
        $strCodEmpresa         = $arrayParametros['strCodEmpresa'];
        $strUsrCreacion        = $arrayParametros['strUsrCreacion'];
        $intClientIp           = (int) ($arrayParametros['intClientIp']);
        $arrayDatosform        = $arrayParametros['datos_form'];
        $intIdAdendum                = (int) ($arrayParametros['idAdendum']);
        
        $objEmpresa     = $this->emcom->getRepository('schemaBundle:InfoEmpresaGrupo')->find($strCodEmpresa);
        $strPrefijoEmpresa = trim(strtoupper($objEmpresa->getPrefijo()));
        
        $objFechaCreacion = new \DateTime('now');      
        
        $this->emComunicacion->getConnection()->beginTransaction();
        try
        {  
            $objInfoContrato = $this->emcom->getRepository('schemaBundle:InfoContrato')->find($intId);
            $objInfoAdendum = $this->emcom->getRepository('schemaBundle:InfoAdendum')->find($intIdAdendum);
            if( $objInfoContrato )
            {                    
                $arrayDatosformFiles    = $arrayDatosform['datos_form_files'];
                $arrayTipoDocumentos = $arrayDatosform['arrayTipoDocumentos'];
                
                foreach ($arrayDatosformFiles as $intKey => $arrayImagenes)                 
                {  
                    foreach ( $arrayImagenes as $arrayKeyImagen => $objvalue) 
                    {        
                        if( $objvalue )
                        {                            
                            $objInfoDocumento = new InfoDocumento();                             
                            $objInfoDocumento->setNombreDocumento("documento_digital");
                            $objInfoDocumento->setFile( $objvalue );   
                            $objInfoDocumento->setFechaDocumento( $objFechaCreacion );   
                            $objInfoDocumento->setUsrCreacion( $strUsrCreacion );
                            $objInfoDocumento->setFeCreacion( $objFechaCreacion );
                            $objInfoDocumento->setIpCreacion( $intClientIp );
                            $objInfoDocumento->setEstado( 'Activo' );    
                            $objInfoDocumento->setMensaje( "Archivo agregado al contrato # ".
                            $objInfoContrato->getNumeroContrato() );                       
                            $objInfoDocumento->setEmpresaCod( $strCodEmpresa );           

                            $objTipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                                       ->find($arrayTipoDocumentos[$arrayKeyImagen]);
                            if( $objTipoDocumentoGeneral != null )
                            {            
                                $objInfoDocumento->setTipoDocumentoGeneralId( $objTipoDocumentoGeneral->getId() );  
                            }                                                    
                                                   
                            if ( $objInfoDocumento->getFile() )
                            {
                                $objInfoDocumento->preUpload();
                                $strNombreArchivo = $objInfoDocumento->getUbicacionLogicaDocumento();
                                $strNombreApp     = 'TelcosWeb';
                                $arrayPathAdicional = [];
                                $strSubModulo = "ContratoDocumentoDigital";
                                
                                $arrayParamNfs          = array(
                                    'prefijoEmpresa'       => $strPrefijoEmpresa,
                                    'strApp'               => $strNombreApp,
                                    'strSubModulo'         => $strSubModulo,
                                    'arrayPathAdicional'   => $arrayPathAdicional,
                                    'strBase64'            => base64_encode(file_get_contents($objInfoDocumento->getFile())),
                                    'strNombreArchivo'     => $strNombreArchivo,
                                    'strUsrCreacion'       => $strUsrCreacion);
                                $arrayRespNfs = $this->utilService->guardarArchivosNfs($arrayParamNfs);
                
                                if(isset($arrayRespNfs))
                                {
                                    if($arrayRespNfs['intStatus'] == 200)
                                    {
                                        $strNuevoNombreArchivo = $objInfoDocumento->getNombreDocumento().'.'.$objInfoDocumento->getExtension();
                                        $objInfoDocumento->setNombreDocumento($strNuevoNombreArchivo);
                                        $objInfoDocumento->setFile(null);
                                        $objInfoDocumento->setUbicacionFisicaDocumento($arrayRespNfs['strUrlArchivo']);

                                    }
                                    else
                                    {
                                        throw new \Exception('No se pudo crear el contrato, error al cargar el archivo digital');
                                    }
                                }
                                else
                                {
                                    throw new \Exception('Error, no hay respuesta del WS para almacenar el documento');
                                }
                            }                                                                           
                            $objTipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                     ->findOneByExtensionTipoDocumento(strtoupper($objInfoDocumento->getExtension()));

                            if( $objTipoDocumento != null)
                            {
                                $objInfoDocumento->setTipoDocumentoId($objTipoDocumento);          
                            }
                            else
                            {
                                $objAdmiTipoDocumento = new AdmiTipoDocumento(); 
                                $objAdmiTipoDocumento->setExtensionTipoDocumento(strtoupper($objInfoDocumento->getExtension()));
                                $objAdmiTipoDocumento->setTipoMime(strtoupper($objInfoDocumento->getExtension()));   
                                $objAdmiTipoDocumento->setDescripcionTipoDocumento('ARCHIVO FORMATO '.strtoupper($objInfoDocumento->getExtension()));
                                $objAdmiTipoDocumento->setEstado('Activo');
                                $objAdmiTipoDocumento->setUsrCreacion( $strUsrCreacion );
                                $objAdmiTipoDocumento->setFeCreacion( $objFechaCreacion );          
                                $this->emComunicacion->persist( $objAdmiTipoDocumento );
                                $this->emComunicacion->flush(); 
                                $objInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);    
                            }                      
                            $objInfoDocumento->setContratoId( $objInfoContrato->getId() );
                            $this->emComunicacion->persist($objInfoDocumento);
                            $this->emComunicacion->flush();   

                            $objInfoDocumentoRelacion = new InfoDocumentoRelacion(); 
                            $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());      
                            $objInfoDocumentoRelacion->setModulo('COMERCIAL'); 
                            $objInfoDocumentoRelacion->setContratoId($objInfoContrato->getId()); 
                            $objInfoDocumentoRelacion->setEstado('Activo');
                            $objInfoDocumentoRelacion->setFeCreacion($objFechaCreacion);       
                            $objInfoDocumentoRelacion->setUsrCreacion($strUsrCreacion);
                            if($objInfoAdendum)
                            {
                                $objInfoDocumentoRelacion->setNumeroAdendum($objInfoAdendum->getNumero()); 
                            }
                            $this->emComunicacion->persist($objInfoDocumentoRelacion);         
                            $this->emComunicacion->flush();
                            
                            
                            
                            if($objTipoDocumento && "ARCHIVO DE IMAGEN" === trim($objTipoDocumento->getDescripcionTipoDocumento()))
                            {
                                $arrayNombreDocumento = explode(".",$objInfoDocumento->getPath());
                                $strNombreDocumento   = $arrayNombreDocumento[0].".pdf"; 
                                $strNumeroContrato    = $objInfoContrato->getNumeroContrato();
                                    
                                $arrayParametros = array ();
                                $arrayParametros['strNombreDocuemnto']        = $strNombreDocumento;
                                $arrayParametros['strUbicacionFisicaDoc']     = $objInfoDocumento->getUbicacionFisicaDocumento();  
                                $arrayParametros['strUsrCreacion']            = $strUsrCreacion;   
                                $arrayParametros['strClienteIp']              = $intClientIp;  
                                $arrayParametros['strMensaje']                = "Archivo agregado al contrato # ".$strNumeroContrato; 
                                $arrayParametros['strCodEmpresa']             = $strCodEmpresa;
                                $arrayParametros['intTipoDocumentoGeneralId'] = $objTipoDocumentoGeneral->getId();
                                $arrayParametros['intContratoId']             = $objInfoContrato->getId(); 
                                $arrayParametros['emComunicacion']            = $this->emComunicacion;  
                                $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;
                                $arrayParametros['strSubModulo'] = $strSubModulo;
                                $arrayParametros['strNombreApp'] = $strNombreApp;

                                if(!($this->boolGenerarArchivoPdfNfs($arrayParametros))) 
                                {
                                    throw new \Exception('Error al generar archivo pdf.');
                                }
                                
                            }                            
                        }
                    }                       
                }
                if ($this->emComunicacion->getConnection()->isTransactionActive())
                {
                    $this->emComunicacion->getConnection()->commit();
                }                
                $this->emComunicacion->getConnection()->close();  
                return $objInfoContrato;
            }
        }
        catch(\Exception $e)
        {                                 
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }                            
            $this->emComunicacion->getConnection()->close();  
            throw $e;
        }        
    }

    

    /**
     * Método para almacenar los archivos digitales en el microservicio NFS
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @since 14-09-2020
     * @version 1.0
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 - 23-04-2020 - En el caso que no se puedan generar los documentos del contrato del nfs se realiza un rollback a la
     *                             transacción se retorna el mensaje de error al usuario
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.2 03-05-2021 - Se agrega al $arrayParametros el strNombreEtiqueta para definir el nombre de los archivos que se agregarán al 
     *                           contrato de los Clientes con "Beneficio 3era Edad / Adulto Mayor"
     * 
     * @param   $arrayParametros
     * @return  bool generaPdf
     */
    public function guardarArchivoNFS($arrayParametros)
    {
        $arrayTipoDocumentos = $arrayParametros['arrayTipoDocumentos'];
        $intIndxImg = 0;
        foreach ($arrayParametros['arrayDatosFormFiles'] as $arrayImagenes)
        {
            foreach ( $arrayImagenes as $intkeyImagen => $strValue)
            {
                if($strValue)
                {
                    $strNombreEtiqueta      = isset($arrayParametros['strNombreEtiqueta']) ? $arrayParametros['strNombreEtiqueta'] : '';
                    $strPrefijoEmpresa      = isset($arrayParametros['prefijoEmpresa']) ? $arrayParametros['prefijoEmpresa'] : 'MD';
                    $strNombreApp           = !empty($arrayParametros['strApp']) ? $arrayParametros['strApp'] : 'TELCOS';
                    $strIdentificacion      = is_object($arrayParametros['objPerEmpRol']->getPersonaId()) ?
                                                $arrayParametros['objPerEmpRol']->getPersonaId()->getIdentificacionCliente()
                                                : 'SIN_IDENTIFICACION';

                    $arrayPathAdicional     = null;
                    $arrayParamData         = null;
                    $arrayNFSResp           = null;
                    
                    if($strNombreEtiqueta !='')
                    {
                        $objTipoDocumentoGeneral = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                                        ->find($arrayTipoDocumentos[$intkeyImagen]);
                        $strTipoArchivo = '';
                        if($objTipoDocumentoGeneral != null)
                        {
                            $strTipoArchivo = $objTipoDocumentoGeneral->getDescripcionTipoDocumento();
                        }
                        $strNombreArchivo  =  $strNombreEtiqueta.'_'.$strTipoArchivo.'_'.uniqid();
                        $strExtension      =  $arrayParametros['arrayFileBase64'][$intIndxImg]['extension'];
                        $strNombreImagen   =  $strNombreArchivo . ".". $strExtension;                       
                    }
                    else
                    {
                        $strNombreArchivo   = 'documento_digital_'.uniqid();   
                        $strNombreImagen    = $strNombreArchivo.'.png';
                        $strExtension       = 'PNG';
                    }
                    
                    $arrayPathAdicional[]   = array('key' => $strIdentificacion);
                    $objGestionDir          = $this->emcom->getRepository('schemaBundle:AdmiGestionDirectorios')
                                                    ->findOneBy(array('aplicacion'  => $strNombreApp,
                                                                      'empresa'     => $strPrefijoEmpresa));
                    if(!is_object($objGestionDir))
                    {
                        throw new \Exception('Error, no existe la configuración requerida para almacenar archivos de la app '.$strNombreApp);
                    }
                    $arrayParamData[]       = array('codigoApp'     => $objGestionDir->getCodigoApp(),
                                                    'codigoPath'    => $objGestionDir->getCodigoPath(),
                                                    'fileBase64'    => $arrayParametros['arrayFileBase64'][$intIndxImg]['file'],
                                                    'nombreArchivo' => $strNombreImagen,
                                                    'pathAdicional' => $arrayPathAdicional);
                    $arrayParamDirectorio   = array('data'    => $arrayParamData,
                                                    'op'      => 'guardarArchivo',
                                                    'user'    => $arrayParametros['strUsuario']);
                    //Llamar al webService
                    $arrayOptions      = array(CURLOPT_SSL_VERIFYPEER => false);
                    $arrayResponse     = $this->serviceRestClient->postJSON($this->strMSnfs,
                                                                            json_encode($arrayParamDirectorio),
                                                                            $arrayOptions);

                    if(isset($arrayResponse))
                    {
                        $arrayNFSResp = json_decode($arrayResponse['result'], 1);
                        if($arrayNFSResp['code'] == 200)
                        {
                            $strUrl       = $arrayNFSResp['data'][0]['pathFile'];
                            $arrayArchivo = array('strUrl'              => $strUrl,
                                                  'strNombreArchivo'    => $strNombreImagen,
                                                  'strTipoDocGeneral'   => $arrayTipoDocumentos[$intkeyImagen],
                                                  'strTipoDocumento'    => $strExtension);
                            $arrayFile[]  = $arrayArchivo;
                        }
                        else
                        {
                            throw new \Exception('No se pudo guardar el contrato - '
                                    . 'Error al momento de crear los archivos del contrato', 206);
                        }
                    }
                    else
                    {
                        throw new \Exception('Error, no hay respuesta del WS para almacenar el documento');
                    }

                    $objTipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                             ->findOneByExtensionTipoDocumento(strtoupper($strExtension));
                                       
                    if(is_object($objTipoDocumento) && "ARCHIVO DE IMAGEN" === trim($objTipoDocumento->getDescripcionTipoDocumento()))
                    {
                        $objImagick = new Imagick($strUrl);
                        $objImagick->setImageFormat('pdf');
                        $objImgBlob             = $objImagick->getimageblob();
                        $arrayParamData         = null;
                        $arrayParamDirectorio   = null;
                        $strNombrePdf           = $strNombreArchivo.'.pdf';
                        $arrayParamData[]       = array('codigoApp'     => $objGestionDir->getCodigoApp(),
                                                        'codigoPath'    => $objGestionDir->getCodigoPath(),
                                                        'fileBase64'    => base64_encode($objImgBlob),
                                                        'nombreArchivo' => $strNombrePdf,
                                                        'pathAdicional' => $arrayPathAdicional);
                        $arrayParamDirectorio   = array('data'    => $arrayParamData,
                                                        'op'      => 'guardarArchivo',
                                                        'user'    => $arrayParametros['strUsuario']);
                        $arrayResponse          = $this->serviceRestClient->postJSON($this->strMSnfs,
                                                                                     json_encode($arrayParamDirectorio),
                                                                                     $arrayOptions);
                        if(isset($arrayResponse))
                        {
                            $arrayNFSResp = json_decode($arrayResponse['result'], 1);
                            if($arrayNFSResp['code'] == 200)
                            {
                                $strUrl = $arrayNFSResp['data'][0]['pathFile'];
                                $arrayArchivo = array('strUrl'              => $strUrl,
                                                      'strNombreArchivo'    => $strNombrePdf,
                                                      'strTipoDocGeneral'   => $arrayTipoDocumentos[$intkeyImagen],
                                                      'strTipoDocumento'    => 'PDF');
                                $arrayFile[]  = $arrayArchivo;
                            }
                            else
                            {
                                throw new \Exception('No se pudo crear el contrato, error al crear el archivo '.$strNombrePdf);
                            }
                        }
                        else
                        {
                            throw new \Exception('Error, no hay respuesta del WS para almacenar el documento');
                        }                    
                    }
                    
                    $intIndxImg++;                                                                               
                }
            }
        }
        return $arrayFile;
    }

    /** 
    * Descripcion: Metodo que genera un archivo pdf a partir de la ruta de uno existente
    * @author  Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 29-07-2016   
    * 
    * @author  Edgar Holguin <eholguin@telconet.ec>
    * @version 1.1 15-08-2016   Se corrige seteo de campo documentoId que debe hacer referencia a la tabla info_docuemnto.
    * 
    * @param   $arrayParametros 
    * @return  bool generaPdf   
    */
    
    public function boolGenerarArchivoPdf($arrayParametros)
    {

        $strNombreDocumento   = $arrayParametros['strNombreDocuemnto'];
        $strUbiFisicaDoc      = $arrayParametros['strUbicacionFisicaDoc'];
        $strDirArchivo        = $arrayParametros['strDirArchivo']; 
        $strUsrCreacion       = $arrayParametros['strUsrCreacion'];
        $strClientIp          = $arrayParametros['strClienteIp'];
        $strMensaje           = $arrayParametros['strMensaje'];
        $strCodEmpresa        = $arrayParametros['strCodEmpresa'];
        $intDocGeneralId      = (int) ($arrayParametros['intTipoDocumentoGeneralId']);
        $intContratoId        = (int) ($arrayParametros['intContratoId']);
        $strNumeroAdendum     = $arrayParametros['strNumeroAdendum'];
        $emComunicacion       = $arrayParametros['emComunicacion'];
        $arrayDir             = explode(".",$strDirArchivo);
        $strRutaArchivoPdf    = $arrayDir[0].".pdf";
        $dateFechaCreacion    = new \DateTime('now');  
                              
        $objImagick = new Imagick($strDirArchivo);
        if($objImagick)
        {
            $objImagick->setImageFormat('pdf');
            $objImagick->writeImage($strRutaArchivoPdf);

            $objInfDocumento = new InfoDocumento();    
            $objInfDocumento->setNombreDocumento($strNombreDocumento);
            $objInfDocumento->setUbicacionLogicaDocumento($strNombreDocumento);
            $objInfDocumento->setUbicacionFisicaDocumento($strUbiFisicaDoc);                                
            $objInfDocumento->setUploadDir(substr($this->fileRoot, 0, -1));
            $objInfDocumento->setFechaDocumento( $dateFechaCreacion );                                                                 
            $objInfDocumento->setUsrCreacion( $strUsrCreacion );
            $objInfDocumento->setFeCreacion( $dateFechaCreacion );
            $objInfDocumento->setIpCreacion( $strClientIp );
            $objInfDocumento->setEstado( 'Activo' );                                                           
            $objInfDocumento->setMensaje( $strMensaje );                                        
            $objInfDocumento->setEmpresaCod( $strCodEmpresa );
            $objInfDocumento->setTipoDocumentoGeneralId($intDocGeneralId); 

            $objTipoDoc = $emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')->findOneByExtensionTipoDocumento('PDF');                                    

            if($objTipoDoc)
            {
                $objInfDocumento->setTipoDocumentoId($objTipoDoc);  
            }

            $objInfDocumento->setContratoId($intContratoId);
            $emComunicacion->persist($objInfDocumento);
            $emComunicacion->flush();   

            $objInfoDocRelacion = new InfoDocumentoRelacion(); 
            $objInfoDocRelacion->setDocumentoId($objInfDocumento->getId());                    
            $objInfoDocRelacion->setModulo('COMERCIAL'); 
            $objInfoDocRelacion->setContratoId($intContratoId);        
            $objInfoDocRelacion->setEstado('Activo');                                                                                   
            $objInfoDocRelacion->setFeCreacion($dateFechaCreacion);                        
            $objInfoDocRelacion->setUsrCreacion($strUsrCreacion);
            $objInfoDocRelacion->setNumeroAdendum($strNumeroAdendum);
            $emComunicacion->persist($objInfoDocRelacion);                        
            $emComunicacion->flush();
            
            return true;
        }
        return false;
    
    }
    
    
     /** 
    * Descripcion: Metodo que genera un archivo pdf a partir de la ruta de uno existente
    * @author  Kevin Mosquera <kmosqueran@telconet.ec>
    * @version 1.2 20-06-2021   Subida de archivos al ms.
    * 
    * @param   $arrayParametros 
    * @return  bool generaPdf   
    */
    
    public function boolGenerarArchivoPdfNfs($arrayParametros)
    {

        $strNombreDocumento   = $arrayParametros['strNombreDocuemnto'];
        $strUbiFisicaDoc      = $arrayParametros['strUbicacionFisicaDoc']; 
        $strUsrCreacion       = $arrayParametros['strUsrCreacion'];
        $strClientIp          = $arrayParametros['strClienteIp'];
        $strMensaje           = $arrayParametros['strMensaje'];
        $strCodEmpresa        = $arrayParametros['strCodEmpresa'];
        $intDocGeneralId      = (int) ($arrayParametros['intTipoDocumentoGeneralId']);
        $intContratoId        = (int) ($arrayParametros['intContratoId']);
        $strNumeroAdendum     = $arrayParametros['strNumeroAdendum'];
        $emThisComunicacion   = $arrayParametros['emComunicacion'];
        $strPrefijoEmpresa       = $arrayParametros['strPrefijoEmpresa'];
        $strSubModulo         = $arrayParametros['strSubModulo'];
        $strNombreApp         = $arrayParametros['strNombreApp'];
        $objFechaCreacion    = new \DateTime('now');  
                              
        
        $objImagick = new Imagick($strUbiFisicaDoc);
        if($objImagick)
        {
            $objImagick->setImageFormat('pdf');
            
            $objImgBlob = $objImagick->getimageblob();

            $objInfDocumento = new InfoDocumento();    
            $objInfDocumento->setNombreDocumento($strNombreDocumento);
            $objInfDocumento->setUbicacionLogicaDocumento($strNombreDocumento);
            $objInfDocumento->setFechaDocumento( $objFechaCreacion );
            $objInfDocumento->setUsrCreacion( $strUsrCreacion );
            $objInfDocumento->setFeCreacion( $objFechaCreacion );
            $objInfDocumento->setIpCreacion( $strClientIp );
            $objInfDocumento->setEstado( 'Activo' );             
            $objInfDocumento->setMensaje( $strMensaje );          
            $objInfDocumento->setEmpresaCod( $strCodEmpresa );
            $objInfDocumento->setTipoDocumentoGeneralId($intDocGeneralId);
            
            $arrayPathAdicional = [];     
            
            $arrayParamNfs          = array(
                'prefijoEmpresa'       => $strPrefijoEmpresa,
                'strApp'               => $strNombreApp,
                'strSubModulo'         => $strSubModulo,
                'arrayPathAdicional'   => $arrayPathAdicional,
                'strBase64'            => base64_encode($objImgBlob),
                'strNombreArchivo'     => $strNombreDocumento,
                'strUsrCreacion'       => $strUsrCreacion);
            $arrayRespNfs = $this->utilService->guardarArchivosNfs($arrayParamNfs);

            if(isset($arrayRespNfs))
            {
                if($arrayRespNfs['intStatus'] == 200)
                {
                    $objInfDocumento->setUbicacionFisicaDocumento($arrayRespNfs['strUrlArchivo']);

                }
                else
                {
                    throw new \Exception('No se pudo crear el contrato, error al cargar el archivo digital');
                }
            }
            else
            {
                throw new \Exception('Error, no hay respuesta del WS para almacenar el documento');
            }

            $objTipoDoc = $emThisComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')->findOneByExtensionTipoDocumento('PDF');

            if($objTipoDoc)
            {
                $objInfDocumento->setTipoDocumentoId($objTipoDoc);  
            }

            $objInfDocumento->setContratoId($intContratoId);
            $emThisComunicacion->persist($objInfDocumento);
            $emThisComunicacion->flush();   

            $objInfoDocRelacion = new InfoDocumentoRelacion(); 
            $objInfoDocRelacion->setDocumentoId($objInfDocumento->getId());
            $objInfoDocRelacion->setModulo('COMERCIAL'); 
            $objInfoDocRelacion->setContratoId($intContratoId);        
            $objInfoDocRelacion->setEstado('Activo');
            $objInfoDocRelacion->setFeCreacion($objFechaCreacion);
            $objInfoDocRelacion->setUsrCreacion($strUsrCreacion);
            $objInfoDocRelacion->setNumeroAdendum($strNumeroAdendum);
            $emThisComunicacion->persist($objInfoDocRelacion);
            $emThisComunicacion->flush();
            
            return true;
        }
        return false;
    
    }   


    /** 
    * Descripcion: Metodo encargado de eliminar documentos a partir del id de la referencia enviada
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 29-07-2014   
    * @param integer $id // id del documento
    * @return json con resultado del proceso   
    */
    
    public function eliminarDocumento($id)
    {                
        $this->emComunicacion->getConnection()->beginTransaction();
        try
        {  
            $objInfoDocumento =  $this->emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($id);                                              
            if( $objInfoDocumento )
            {            
                $path = $objInfoDocumento->getUbicacionFisicaDocumento();
                //$path      = 'C:/wamp/www/telcos/web/public/uploads/documentos/'.$objInfoDocumento->getUbicacionLogicaDocumento();
                if (file_exists($this->path_telcos.$path))
                unlink($this->path_telcos.$path);

                $objInfoDocumento->setEstado("Inactivo");
                $this->emComunicacion->persist($objInfoDocumento);
                $this->emComunicacion->flush();               

                $objInfoDocumentoRelacion = $this->emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')->findByDocumentoId($id);
                if(isset($objInfoDocumentoRelacion))
                {
                    foreach($objInfoDocumentoRelacion as $det)
                    {
                        $det->setEstado("Inactivo");
                        $this->emComunicacion->persist($det);
                        $this->emComunicacion->flush();
                    }
                }
             if ($this->emComunicacion->getConnection()->isTransactionActive())
             {
                 $this->emComunicacion->getConnection()->commit();
             }                
             $this->emComunicacion->getConnection()->close();  
             return $objInfoDocumento;    
             
            }     
        }
        catch(\Exception $e)
        {                                 
           if ($this->emComunicacion->getConnection()->isTransactionActive())
           {
               $this->emComunicacion->getConnection()->rollback();
           }                            
           $this->emComunicacion->getConnection()->close();  
           throw $e;
        }   
    }        
    
    /** 
     * Descripcion: Metodo encargado de validar los numeros de Cuenta o Tarjetas de Credito
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @param interger $intTipoCuentaId
     * @param integer $intBancoTipoCuentaId     
     * @param string  $strNumeroCtaTarjeta
     * @param string  $strCodigoVerificacion
     * @throws Exception
     * @version 1.0 09-02-2015 
     * @return array
     * 
     * 
     * Actualización: 
     * - Se modifica el metodo para que reciba los parametros por arreglo 
     * - Se agrega validación para permitir condicionar por empresa la validación de bines
     * arrayParametros[
     *     intTipoCuentaId       => Id de tipo cuenta
     *     intBancoTipoCuentaId  => Id de banco tipo cuenta
     *     strNumeroCtaTarjeta   => Numero de tarjeta
     *     strCodigoVerificacion => codigo de verificacion de tarjeta
     *     strCodEmpresa         => codigo de empresa
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 07-07-2017
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 12-11-2020 Se elimina uso de transacciones ya que la función es sólo de consulta y en estos casos nunca se usa,
     *                         ya que al colocar transacciones y hacer el close se cierra la misma conexión desde la función que la invoca y por ende
     *                         no se guarda nada de lo anterior que se haya realizado ya que no se hizo commit y así mismo lo que esté programado
     *                         después hará commit automático(sin necesidad de escribirlo) ya que ya no estará abierta la transacción.
     *                         Este error ha sido detectado desde la opción de aprobación de contrato por cambio de razón social por punto.
     *
     * Se elimina todo el código anterior ya que de ahora en adelante se usará un procedimiento almacenado
     * este procedimiento almacenado es DB_FINANCIERO.FNCK_ACTUALIZA_TARJETAS_ABU.P_VALIDAR_TARJETA_ABU
     * 
     * arrayparametros[
     *                  strCodEmpresa           => Código de empresa
     *                  strNumeroCtaTarjeta     => Número de cuenta de la tarjeta
     *                  intBancoTipoCuentaId    => Id del banco tipo cuenta
     *                  intTipoCuentaId         => Id tipo de cuenta
     *                  intFormaPagoId          => Id de la forma de pago
     *                ]
     * @author  Christian Yunga <cyungat@telconet.ec>
     * @version 2.0 15-02-2023
     */
    public function validarNumeroTarjetaCta($arrayParametros)
    {
        $arrayTokenCas = array();
        $arrayValidaciones = array(); 
        $arrayRespuesta = array();
        $strJsonRespuesta = '';
        $arrayDatosValidarTarjeta = array();
        $arrayDatosValidarTarjeta['codEmpresa']         = $arrayParametros['strCodEmpresa'];
        $arrayDatosValidarTarjeta['numeroCtaTarjeta']   = $arrayParametros['strNumeroCtaTarjeta'];
        $arrayDatosValidarTarjeta['bancoTipoCuentaId']  = $arrayParametros['intBancoTipoCuentaId'];
        $arrayDatosValidarTarjeta['tipoCuentaId']       = $arrayParametros['intTipoCuentaId'];
        $arrayDatosValidarTarjeta['formaPagoId']        = $arrayParametros['intFormaPagoId'];
        $arrayDatosValidarTarjeta['codigoVerificacion'] = '';
        $arrayDatosValidarTarjeta['ipCreacion']         = '';
        $arrayTokenCas   = $this->serviceTokenCas->generarTokenCas(); 
        
        try
        {
            
            $objOptions   = array(CURLOPT_SSL_VERIFYPEER => false,
                                  CURLOPT_HTTPHEADER     => array('Content-Type: appliction/json',
                                                                  'tokencas: '.$arrayTokenCas['strToken']));
        
            $strJsonData        = json_encode($arrayDatosValidarTarjeta);
            $arrayResponseJson  = $this->serviceRestClient->postJSON($this->strUrlValidarNumeroTarjetaCta,$strJsonData,$objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);

            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0
            && isset($strJsonRespuesta['status'])
            && isset($strJsonRespuesta['message']))
            {
                $arrayResponse = array('strStatus'  => $strJsonRespuesta['code'],
                                       'strMensaje' => $strJsonRespuesta['message']);
                $arrayValidaciones = $arrayResponse;
            }
            else
            {
                $arrayValidaciones['strStatus']   = "ERROR";
                if(empty($strJsonRespuesta['message']))
                {
                    $arrayRespuesta[] = array('mensaje_validaciones' => "No Existe Conectividad con el WS MS CORE CONTRATO.");
                }
                else
                {
                    $arrayRespuesta[] = array('mensaje_validaciones' => $strJsonRespuesta['message']);
                }
            }
            return $arrayRespuesta;
        } catch (\Exception $e) 
        {
            $arrayRespuesta[] = array('mensaje_validaciones' =>$e->getMessage());
            return $arrayRespuesta;
        }
    }

    /**
     * Funcion que usa el crontab para aprobar contratos de manera masiva
     * 
     * arrayparametros[
     *                  strCodEmpresa           => Código de empresa
     *                  strNumeroCtaTarjeta     => Número de cuenta de la tarjeta
     *                  intBancoTipoCuentaId    => Id del banco tipo cuenta
     *                  intTipoCuentaId         => Id tipo de cuenta
     *                  intFormaPagoId          => Id de la forma de pago
     *                ]
     * 
     * @author  Christian Yunga <cyungat@telconet.ec>
     * @version 1.0 15-02-2023
     */

    public function validarNumeroTarjetaCtaAprobarContrato($arrayParametros)
    {
        $strMensajeValidacion ='';
        $arrayRespuesta       = array();
        try 
        {
            
            $arrayDatosValidarTarjeta = array();
            $arrayDatosValidarTarjeta['codEmpresa']         = $arrayParametros['strCodEmpresa'];
            $arrayDatosValidarTarjeta['numeroCtaTarjeta']   = $arrayParametros['strNumeroCtaTarjeta'];
            $arrayDatosValidarTarjeta['bancoTipoCuentaId']  = $arrayParametros['intBancoTipoCuentaId'];
            $arrayDatosValidarTarjeta['tipoCuentaId']       = $arrayParametros['intTipoCuentaId'];
            $arrayDatosValidarTarjeta['formaPagoId']        = $arrayParametros['intFormaPagoId'];
            $arrayDatosValidarTarjeta['codigoVerificacion'] = '';
            $arrayDatosValidarTarjeta['ipCreacion']         = '';

            
            $strMensajeValidacion = $this->emComercial->getRepository('schemaBundle:InfoContrato')
                                                                    ->getValidarNumeroTarjetaCta($arrayParametros);
     
            if(!empty($strMensajeValidacion))
            {
                $arrayRespuesta[] = array('mensaje_validaciones' => $strMensajeValidacion);
            }

            return $arrayRespuesta;
            
        }
            catch (\Exception $ex)
        {
            error_log($ex->getMessage());
           
           throw($ex);
        }
    }
    /**
     * 
     * Documentación para el método 'ejecutaCambioRazonSocialPorPunto'.
     *
     * Metodo utilizado para ejecutar el cambio de razon social por Punto o Login
     * Consideraciones:
     * En caso de que el "Cambio de Razon Social por Punto" se realice sobre un cliente ya existente:  
     * 1) Se crearan los nuevos logines en el cliente ya existente, bajo el contrato ya existente, se guardara informacion de los
     *    Nuevos Puntos asi como toda la data relacionada a estos.
     * 2) Los Logines origen del "Cambio de Razon Social por Punto" quedaran en estado Cancelado asi como toda la data relacionada a estos.
     *     
     * En caso de que el "Cambio de Razon Social por Punto" se realice sobre un cliente Nuevo:
     * 1) Se procedera a crear el nuevo Cliente con ROL de "Pre-cliente".
     * 2) Se generaran las nuevas Formas de Contacto del nuevo PreCliente.
     * 3) Se generara un nuevo Contrato en estado "Pendiente" para el PreCliente.
     * 4) Se subiran los archivos digitales adjuntos al nuevo Contrato pendiente.
     * 5) Los Logines nuevos que se trasladan se crearan en el momento de la Aprobacion del Nuevo contrato
     * 6) Los Logines origen del "Cambio de Razon Social por Punto" pasaran a Cancelados asi como toda la data relacionada a estos en el momento
     *    de la Aprobacion del Nuevo Contrato.
     * 
     * Validaciones:
     * 1) No se permite realizar "Cambio de Razon Social por Punto" a todos los Logines de un Cliente esto debera realizarse desde la opcion de 
     *    "Cambio de Razon Social" tradicional    
     * 2) No se permite Cambio de Razon Social Por Login, si el Login es Punto Padre de Facturacion de otros Logines.
     * 3) No se permite Cambio de Razon Social Por Login si el Login no posee servicio Activo
     * 4) No se permite realizar "Cambio de Razon Social por Punto" si el cliente posee deuda pendiente.
     *
     * @param array  $arrayParams 
     * @throws Exception
     * @return \telconet\schemaBundle\Entity\InfoPersonaEmpresaRol
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 13-09-2015     
     * 
     * Se actualiza que no valide si cliente tiene deuda en caso de tratarse de  CLIENTE CANAL
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.2 13-01-2016  
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 27-06-2015    Se corrige cancelación de LDAP de servicios antiguos y creación de LDAP nuevos servicios
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 18-02-2019 Se agrega actualización de ldap para servicios Small Business y TelcoHome al ejecutar el cambio de razón social
     * 
     * Se agrega informacion de Contactos a Clonarse en el Proceso de Cambio de Razon Social:
     * -Clono Contactos a nivel de Cliente hacia la nueva razon social
     * -Clono Contactos a nivel de Punto hacia los Logines de la nueva razon social 
     * -Se modifica Validacion que verifica deuda del Cliente solo validara en Caso de ser MD, si se trata de TN no debe validar deuda.     
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.4 20-06-2016  
     *
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.5 20-06-2019  Se modifica operador en validación que compara números de identificación.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 12-11-2020 Se modifica orden de ejecución de funciones que se ejecutaban internamente debido a mal uso de transacciones
     *                         que afectan a la ejecución del proceso principal, para lo cual se obtiene la respuesta de la ejecución de la
     *                         función cambioRazonSocialClienteExistente
     * 
     *
     * @author Marlon Pluas <mpluas@telconet.ec>
     * @version 1.7 03-12-2020  Se agrega bandera $strRetornarRSPuntos para devolver los servicios clonados al nuevo punto, ademas
     *                          el obj persona empresa rol.
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.8 22-11-2021  Se valida el nuevo rol creado.
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.9 12-05-2022 - Se verifica servicios con planes para enviarlos al metodo principal y valide si tienen
     *                            promocion por franja horaria
     * 
     * @author Alex Gomez <algomez@telconet.ec>
     * @version 2.0 10-08-2022 Se modifica estado de los puntos y servicios clonados por CRS tradicional y por punto 
     *                          cuando el contrato aun no ha sido autorizado. Aplica para MD y contrato digital.
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 2.1 26-08-2022 - Se agrega consumo a MS por productos Konibit.
     * 
     * @author Luis Farro <lfarro@telconet.ec>
     * @version 3.31 23-02-2023 - Se modifica el envio de productos. Se envia un solo array con todos los productos Konibit.
     */
    public function ejecutaCambioRazonSocialPorPunto($arrayParams) 
    {        
        $strNumeroCtaTarjeta                = "";
        $strError                           = "";
        $arrayServiciosLdap                 = null;
        $arrayRoles                         = array();  
        $boolExisPerConContrato             = true;// Persona Existe con Contrato
        $boolExistePersona                  = true;// Persona Existe
        $objInfoPersonaEmpresaRol           = null;
        $strMensaje                         = "";
        $strTipoMensaje                     = "";
        $arrayRespuestaCRSClienteExistente  = array();
        $this->emcom->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();                
        $strRetornarRSPuntos          = $arrayParams['strRetornarRSPuntos'] != null ? $arrayParams['strRetornarRSPuntos'] : "N";
        $arrayAdendumsExcluirRS       = array();
        $arrayServiciosRSContrato     = array();
        $intContratoFisico            = $arrayParams['intContratoFisico'];
        $arrayServiciosPromociones    = array();
        $arrayPuntosCRSActivar        = array();
        $boolAsignaEstadoPreactivo    = false;
        $strEstadoServicioPreactivo   = '';
        $strMensajeEstadoPreactivo    = '';
        $arrayKonibit                 = array();

        try
        {  
            // Valido que exista la Caracteristica necesaria que relaciona los logines origen y destino del cambio de razon social 
            // para ejecutar el Proceso
            $objAdmiCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                     ->findOneBy(array("descripcionCaracteristica" => "PUNTO CAMBIO RAZON SOCIAL", "estado"=>"Activo"));            
            if(!$objAdmiCaracteristica)
            {
                throw new \Exception('No se pudo generar el Cambio de Razon Social por Login - '
                . 'No se encontro registro de la Caracteristica [PUNTO CAMBIO RAZON SOCIAL] requerida para ejecutar el proceso.');
            }                        
            // Valido que no se pueda realizar cambio de razon social hacia el mismo cliente
            $objPersonaOrigen = $this->emcom->getRepository('schemaBundle:InfoPersona')->find($arrayParams['antiguoIdCliente']);
            
            if($objPersonaOrigen && ($objPersonaOrigen->getIdentificacionCliente() === $arrayParams['identificacionCliente']))
            {
                throw new \Exception('Error en el Numero de Identificacion, - '
                                     . 'No puede realizar Cambio de Razon Social hacia el mismo Cliente'
                                     . ' ['.$arrayParams['identificacionCliente'].']');
            }
                          
            // Valido que la Persona Empresa Rol Origen posea Rol de "Cliente" para ejecutar Cambio de Razon Social por login
            $objPersonaEmpresaRolOrigen = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                          ->getPersonaEmpresaRolPorPersonaPorTipoRolActivosPendientes($arrayParams['antiguoIdCliente'], 'Cliente', 
                                                                                                      $arrayParams['strCodEmpresa']);            
            if(!$objPersonaEmpresaRolOrigen)
            { 
                throw new \Exception('No se encontro informacion del Cliente Origen o no posee Rol de [Cliente], - '
                                     . 'No puede realizar Cambio de Razon Social por Punto');
            }                        
            
            // Valido que la Persona Empresa Rol Origen posea Logines con servicios Activos que no sean Padres de Facturacion de otros Logines 
            // para ejecutar el Cambio de Razon Social por login             
            $objInfoPuntoActivo  = $this->emcom->getRepository('schemaBundle:InfoPunto')
                                   ->getPuntosCambioRazonSocialPorLogin($objPersonaEmpresaRolOrigen->getId(),'','','','','');
            if(!$objInfoPuntoActivo)
            {                
                throw new \Exception('El cliente Origen no posee Puntos (Logines) en estado Activo que no sean Punto Facturacion de otros Logines, - '
                                     . 'No puede realizar Cambio de Razon Social por Punto');
            } 
            
            // Obtengo Puntos (Logines) seleccionados a Trasladar por Cambio de Razon Social
            $arrayPuntosSeleccionados = array();    
            $json_asignaciones        = json_decode($arrayParams['puntos_asignados']);
            $array_asignaciones       = $json_asignaciones->asignaciones;
            $intCantidadAsignaciones  = $json_asignaciones->total;
            foreach($array_asignaciones as $asignacion)
            {
                $arrayPuntosSeleccionados[] = $asignacion->idPto;
            }
            
            // Valido que se haya escogido al menos 1 login para ejecutar el Cambio de Razon Social por Login
            if(count($arrayPuntosSeleccionados)==0)
            {
                throw new \Exception('No se han escogido Logines debe escoger al menos 1 Punto o Login, - '
                                     . 'No puede realizar Cambio de Razon Social por Punto');
            }
            
            // Obtengo el Total de Logines que posean servicios Activos o In-Corte por Persona Empresa Rol
            $intTotalPuntos  = $this->emcom->getRepository('schemaBundle:InfoPunto')
                               ->getTotalPuntosConServicioActivoCortado($objPersonaEmpresaRolOrigen->getId());
                        
            // Valido que no se permita realizar "Cambio de Razon Social por Punto" a todos los Logines de un Cliente esto debera realizarse  
            // desde la opcion de Cambio de Razon Social" tradicional    
            if($intCantidadAsignaciones == $intTotalPuntos)
             {
                  throw new \Exception('No se permite realizar Cambio de Razon Social a todos los Logines de un Cliente esto debera realizarse '
                                       . 'desde la opcion de Cambio de Razon Social tradicional, - No puede realizar Cambio de Razon'
                                       . ' Social por Punto'); 
             } 
            
            // Valido que el Login a trasladarse no sea Padre de Facturacion de otros Logines 
            $arrayPuntosTotal   = $this->emcom->getRepository('schemaBundle:InfoPunto')
                                ->getPuntosCambioRazonSocialPorLogin($objPersonaEmpresaRolOrigen->getId(),'','','','','');                                         
            $objPuntosTotal     = $arrayPuntosTotal['registros'];
            $intTotal           = $arrayPuntosTotal['total'];
            if($objPuntosTotal && $intTotal > 0)
            {
               foreach($objPuntosTotal as $objPuntos)
                {                    
                    $arrayPuntosTotal[] = $objPuntos['id'];               
                }
            }
            
            foreach($arrayPuntosSeleccionados as $value)
            {   
                if(!$arrayPuntosTotal || ($arrayPuntosTotal && !in_array($value, $arrayPuntosTotal)))
                {
                    $objLogin  = $this->emcom->getRepository('schemaBundle:InfoPunto')->findOneById($value);
                    throw new \Exception('El Punto Login ['.$objLogin->getLogin().'] es Punto Facturacion de otros Logines, - '
                                         . 'No puede realizar Cambio de Razon Social por Punto');    
                }    
            }     
            
            // Verifica deuda del cliente Origen del Cambio de Razon Social por login, no se permite realizar si cliente tiene deuda.                      
            $fltValor   = 0;
            // Saco saldo deudor del Cliente , No se considera validacion si el CLIENTE es Canal
            $objAdmiRol = $this->emcom->getRepository('schemaBundle:AdmiRol')->find($objPersonaEmpresaRolOrigen->getEmpresaRolId()->getRolId());
            if(($arrayParams['strPrefijoEmpresa']=='MD' 
                    || $arrayParams['strPrefijoEmpresa']=='EN')
                    && $objAdmiRol->getDescripcionRol() != 'Cliente Canal')
            {
                    $objInfoPuntoDeuda = $this->emcom->getRepository('schemaBundle:InfoPunto')
                    ->findByPersonaEmpresaRolId($objPersonaEmpresaRolOrigen->getId());
                    foreach($objInfoPuntoDeuda as $pto)
                    {
                        $arraySaldoarr = $this->emFinanciero->getRepository('schemaBundle:InfoPagoCab')->obtieneSaldoPorPunto($pto->getId());
                        $fltValor      = $fltValor + $arraySaldoarr[0]['saldo'];
                    }
                    if(round($fltValor, 2) > 0)
                    {
                        throw new \Exception('El cliente Origen tiene deuda con la empresa [Deuda] : ' . round($fltValor, 2) . ', - '
                                            . 'No puede realizar Cambio de Razon Social por Punto');
                    }
            

            }

            // Busco si existe informacion del cliente Destino del Cambio de Razon Social por login
            $arrayDatosCliente = $this->serviceCliente->obtenerDatosClientePorIdentificacion($arrayParams['strCodEmpresa'], 
                                 $arrayParams['identificacionCliente'], $arrayParams['strPrefijoEmpresa']);        
            if ($arrayDatosCliente)
            {                   
                $strRoles   = $arrayDatosCliente['roles'];                
                $arrayRoles = explode('|', $strRoles);   
                
                if(count($arrayRoles) == 0)
                {
                    // Identificacion ya existente pero no posee contrato, Debe completar datos del Contrato y subir Archivo Digital
                    $boolExisPerConContrato = false;
                }                
                for($i = 0; $i < count($arrayRoles); $i++)
                {
                    if($arrayRoles[$i])
                    {
                        // Busco si posee rol de Cliente Busco si posee contrato Activo                        
                        if($arrayRoles[$i] == 'Cliente')
                        {
                            $objInfoPersonaEmpresaRol = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                        ->findByIdentificacionTipoRolEmpresa($arrayParams['identificacionCliente'], 'Cliente', 
                                                                                             $arrayParams['strCodEmpresa']);
                            if($objInfoPersonaEmpresaRol)
                            {
                                $objContrato = $this->emcom->getRepository('schemaBundle:InfoContrato')
                                               ->findContratoActivoPorPersonaEmpresaRol($objInfoPersonaEmpresaRol->getId());
                                if(!$objContrato)
                                {
                                    throw new \Exception('Cliente Destino con Identificacion [' . $arrayParams['identificacionCliente'] . '] '
                                                        . ' existente posee ROL [Cliente] pero no posee contrato Activo - '
                                                        . 'No puede realizar Cambio de Razon Social por Punto');
                                }
                                else
                                {
                                    $boolExisPerConContrato = true;
                                    break;    
                                }
                            }
                        }
                        else
                        {
                            if($arrayRoles[$i] == 'Pre-cliente')
                            {
                                throw new \Exception('Cliente Destino con Identificacion [' . $arrayParams['identificacionCliente'] . '] existente '
                                                   . ' posee rol de Pre-cliente, No puede realizar Cambio de Razon Social por Punto');
                            }
                            else
                            {
                                // Identificacion ya existente pero no posee contrato, Debe completar datos del Contrato y subir Archivo Digital
                                $boolExisPerConContrato = false;
                            }
                        }
                    }  
                    else
                    {
                         // Identificacion ya existente pero no posee contrato, Debe completar datos del Contrato y subir Archivo Digital
                        $boolExisPerConContrato = false;
                    }
                }
            }
            else
            {
                // Identificacion No existe registrada, se registra desde nuevo Pre-cliente, datos del Contrato y subir Archivo Digital
                $boolExistePersona = false;
            }
            
            /* En caso de que el "Cambio de Razon Social por Punto" se realice sobre un cliente ya existente con rol de "Cliente" 
             * y con Contrato Activo:  
             * 1) Se crearan los nuevos logines en el cliente ya existente, bajo el contrato ya existente, se guardara informacion de los
             *    Nuevos Puntos asi como toda la data relacionada a estos.
             * 2) Los Logines origen del "Cambio de Razon Social por Punto" quedaran en estado Cancelado asi como toda la data relacionada a estos,
             *    no podra reversarse.
             */                  
            if($arrayParams['yaexiste'] == 'S' && $boolExisPerConContrato)
            {
                //Consulto los puntos actuales del cliente nuevo
                $arrayPuntosClienteNuevo = $this->emcom->getRepository('schemaBundle:InfoPunto')
                                                       ->findBy(array("personaEmpresaRolId" => $objInfoPersonaEmpresaRol->getId(),
                                                                      "estado"              => 'Activo'));
                foreach ($arrayPuntosClienteNuevo as $objPunto)
                {
                    //Consulto si tiene adendums con estado Pendiente, arma un array de adendums que se deben excluir en el proceso de creacion
                    //y autorizacion de RS
                    $arrayAdendumPendiente = $this->emcom->getRepository('schemaBundle:InfoAdendum')
                                                         ->findBy(array("puntoId" => $objPunto->getId(),
                                                                        "estado"  => array('Pendiente', 'Trasladado','Activo')));
                    foreach ($arrayAdendumPendiente as $objAdendumPendiente)
                    {
                        array_push($arrayAdendumsExcluirRS, $objAdendumPendiente->getId());
                    }
                }

                $arrayRespuestaCRSClienteExistente  = $this->cambioRazonSocialClienteExistente( $arrayParams, 
                                                                                $arrayPuntosSeleccionados, 
                                                                                $objInfoPersonaEmpresaRol, 
                                                                                $objAdmiCaracteristica,
                                                                                $objPersonaEmpresaRolOrigen);

                $arrayServiciosRSContrato  = $arrayRespuestaCRSClienteExistente['arrayServiciosLdap'];
                $arrayServiciosPromociones = $arrayRespuestaCRSClienteExistente['arrayServiciosPromociones'];
                $arrayPuntosCRSActivar     = $arrayRespuestaCRSClienteExistente['arrayPuntosCRS'];
                $arrayKonibit              = $arrayRespuestaCRSClienteExistente['arrayEnvKon'];
            }
            /* En caso de que el "Cambio de Razon Social por Punto" se realice sobre un cliente Nuevo:
             * 1) Se procedera a crear el nuevo Cliente con ROL de "Pre-cliente".
             * 2) Se generaran las nuevas Formas de Contacto del nuevo PreCliente.
             * 3) Se generara un nuevo Contrato en estado "Pendiente" para el PreCliente.
             * 4) Se subiran los archivos digitales adjuntos al nuevo Contrato Pendiente. 
             * 5) Se actualizan nuevos Representante legal            
             */
            else
            {
                
                //Consulta nuevo estado para servicios creados por Cambio de Razón Social por Login
                //previo a la autorizacion del contrato. Solo aplica para MD y contrato Digital
                if(($arrayParams['strPrefijoEmpresa'] === 'MD' || $arrayParams['strPrefijoEmpresa'] === 'EN') 
                    && $intContratoFisico != 1)
                {
                    $boolAsignaEstadoPreactivo = true;

                    $arrayEstadosServicios = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne(
                                                                'ESTADOS_CAMBIO_RAZON_SOCIALXPUNTO',
                                                                'COMERCIAL',
                                                                'CAMBIO_RAZON_SOCIAL_POR_PUNTO',
                                                                '','','','','','',
                                                                $arrayParams['strCodEmpresa']);
                    
                    if(isset($arrayEstadosServicios) && !empty($arrayEstadosServicios))
                    {
                        $strEstadoServicioPreactivo = $arrayEstadosServicios["valor1"];
                    }
                    else
                    {
                        $boolAsignaEstadoPreactivo = false;
                    }
                }


                   //valida que exista represenatnte legal para persona juridica  
                   if (($arrayParams['strPrefijoEmpresa'] === 'MD' || $arrayParams['strPrefijoEmpresa'] === 'EN')
                   && (!$boolExistePersona || !$boolExisPerConContrato) 
                   && ($arrayParams['tipoTributario']=== 'JUR'))
                   {
                       $arrayDatosRL = json_decode($arrayParams['strDatosRepresentanteLegal'],true); 
                       if (empty($arrayDatosRL) || count($arrayDatosRL) < 1)
                       {
                       throw new \Exception("Debe registrar los datos del representante legal.");
                       }
                   }
                                    
                                        
                $objResponseCrsNew  =   $this->cambioRazonSocialClienteNuevo($arrayParams,$arrayPuntosSeleccionados,$boolExistePersona, 
                                                                                 $boolExisPerConContrato, $objAdmiCaracteristica);
                $strError = $objResponseCrsNew ['strError'] ;                                            
               if(!empty($strError))
               {       
                throw new \Exception($strError);                                                   
               }

                $objInfoPersonaEmpresaRol=  $objResponseCrsNew ['objInfoPersonaEmpresaRol'] ; 
                if(!is_object($objInfoPersonaEmpresaRol))
                {
                    throw new \Exception('No se encontro los nuevos roles del cliente');
                }

             
                                                           
                if($strRetornarRSPuntos == 'S' && $intContratoFisico != 1)
                {
                    $arrayEnvKon         = array();
                    // Obtengo Los Puntos Origenes del Cambio de Razon Social que seran Clonados con sus servicios
                    $strFechaCreacion    = new \DateTime('now');
                    $arrayResultado      = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                        ->getPuntosAprobCambioRazonSocial($objInfoPersonaEmpresaRol->getId(),
                                                                  0, 9999999, 'Activo'); 

                    // Recorro arreglo, Clono los nuevos puntos y sus servicios                   
                    $objDatosPuntos = $arrayResultado['registros'];
                    // Valido que existan las referencias de los logines que ejecutaran el cambio de Razon social
                    if(count($objDatosPuntos)==0)
                    {
                    throw new \Exception('No se encontro los Logines Origen del Cambio de Razon Social, - '
                                    . 'No se pudo Aprobar el contrato');
                    }                 
                    foreach($objDatosPuntos as $objDatosPuntos)
                    {
                        $intContKonibit      = 0;
                        $objInfoPuntoOrigen  = $this->emcom->getRepository('schemaBundle:InfoPunto')->find($objDatosPuntos['id']);
                        $strLoginOrigen      = $objInfoPuntoOrigen->getLogin();
                        $intIdPuntoOrigen    = $objInfoPuntoOrigen->getId();
                        $objInfoPuntoClonado = new InfoPunto();
                        $objInfoPuntoClonado = clone $objInfoPuntoOrigen;
                        $objInfoPuntoClonado->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                        $objInfoPuntoClonado->setFeCreacion($strFechaCreacion);
                        $objInfoPuntoClonado->setUsrCreacion($arrayParams['strUsrCreacion']);
                        $objInfoPuntoClonado->setObservacion($objDatosPuntos['id']);
                        // Obtengo Login con secuencia
                        $arrayPuntos = $this->emcom->getRepository('schemaBundle:InfoPunto')
                                        ->findPtosPorEmpresaPorCanton($arrayParams['strCodEmpresa'], 
                                                                $objInfoPuntoClonado->getLogin(),
                                                                $objInfoPuntoClonado->getSectorId()->getParroquiaId()->getCantonId()->getId(), 
                                                                9999999, 1, 0);

                        $strLogin    = $objInfoPuntoClonado->getLogin() . ($arrayPuntos['total'] + 1);
                        $objInfoPuntoClonado->setLogin($strLogin);

                        if($boolAsignaEstadoPreactivo && $objInfoPuntoClonado->getEstado() == "Activo")
                        {
                            //Cambia a estado Pendiente el punto hasta la autorizacion del contrato
                            $objInfoPuntoClonado->setEstado("Pendiente");
                        }

                        $this->emcom->persist($objInfoPuntoClonado);
                        $this->emcom->flush();

                        // Obtengo los servicios Ligados al Punto que seran trasladados
                        $arrayInfoServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                    ->findServiciosPorEmpresaPorPunto($arrayParams['strCodEmpresa'], 
                                                                                    $objDatosPuntos['id'], 
                                                                                    99999999, 1, 0);
                        
                        $objInfoServicio   = $arrayInfoServicio['registros'];

                        foreach($objInfoServicio as $objServ)
                        {                   
                            if($objServ->getEstado() == 'Activo')
                            {
                                $objInfoServicioClonado = new InfoServicio();
                                $objInfoServicioClonado = clone $objServ;
                                $objInfoServicioClonado->setFeCreacion($strFechaCreacion);
                                $objInfoServicioClonado->setUsrCreacion($arrayParams['strUsrCreacion']);
                                $objInfoServicioClonado->setObservacion($objServ->getId());
                                $objInfoServicioClonado->setPuntoId($objInfoPuntoClonado);
                                $objInfoServicioClonado->setPuntoFacturacionId($objInfoPuntoClonado);
                                if( $arrayParams['strPrefijoEmpresa'] != 'TN')
                                {

                                    $objInfoServicioClonado->setPorcentajeDescuento(0);
                                    $objInfoServicioClonado->setValorDescuento(null);
                                    $objInfoServicioClonado->setDescuentoUnitario(null);
                                }

                                $this->emcom->persist($objInfoServicioClonado);
                                $this->emcom->flush();

                                // Obtenemos los servicios con planes para validar si tiene promociones
                                if($arrayParams['strPrefijoEmpresa'] == 'MD'&& is_object($objInfoServicioClonado->getPlanId()))
                                {
                                    array_push($arrayServiciosPromociones, array('destino' => $objInfoServicioClonado->getId(),
                                                                                 'origen' => $objServ->getId()));
                                }
                                array_push($arrayServiciosRSContrato, array('servicioNuevo'    => $objInfoServicioClonado)); 

                                $objInfoServicioTecnico = $this->emcom->getRepository('schemaBundle:InfoServicioTecnico')
                                                  ->findByServicioId($objServ);       
                       
                                foreach($objInfoServicioTecnico as $objServT)
                                {                                                          
                                    $objInfoServicioTecnicoClonado = new InfoServicioTecnico();
                                    $objInfoServicioTecnicoClonado = clone $objServT;
                                    $objInfoServicioTecnicoClonado->setServicioId($objInfoServicioClonado);                            
                                    $this->emcom->persist($objInfoServicioTecnicoClonado);
                                    $this->emcom->flush();                            
                                }

                                /*Cambio estado de servicios a Preactivo previa autorizacion del contrato*/
                                if($boolAsignaEstadoPreactivo && $objInfoServicioClonado->getEstado() == "Activo")
                                {
                                    $objInfoServicioClonado->setEstado($strEstadoServicioPreactivo);
                                    $this->emcom->persist($objInfoServicioClonado);
                                    $this->emcom->flush();
                                }

                                //INI VALIDACIONES KONIBIT
                                $strTelefono           = "";
                                $strCorreo             = "";
                                $arrayListadoServicios = array();
                                $arrayTokenCas         = array();
                                $arrayPdts             = array();
                                $arrayKonibit          = array();
                                $intIdProdKon          = 0;
                                //ALMACENA PRODUCTOS KONIBIT
                                $arrayListProdKon      = array();
                                if($arrayParams['strPrefijoEmpresa'] == 'MD')
                                {
                                    $arrayFormasContacto = array();
                                    if($arrayParams['formas_contacto'])
                                    {
                                        $arrayFormasContactos = explode(',', $arrayParams['formas_contacto']);
                                        for($intI = 0; $intI < count($arrayFormasContactos); $intI+=3)
                                        {
                                            $arrayFormasContacto[] = array('formaContacto' => $arrayFormasContactos[$intI + 1],
                                                                           'valor'         => $arrayFormasContactos[$intI + 2]);
                                        }
                                    }
                                    for($i = 0; $i < count($arrayFormasContacto); $i++)
                                    {
                                        if ($arrayFormasContacto[$i]["formaContacto"] == "Correo Electronico")
                                        {
                                            $strCorreo = $arrayFormasContacto[$i]["valor"];
                                            break;
                                        }
                                    }
                                    for($i = 0; $i < count($arrayFormasContacto); $i++)
                                    {
                                        if ($arrayFormasContacto[$i]["formaContacto"] == "Telefono Movil")
                                        {
                                            $strTelefono = $arrayFormasContacto[$i]["valor"];
                                            break;
                                        }
                                        if ($arrayFormasContacto[$i]["formaContacto"] == "Telefono Movil Claro")
                                        {
                                            $strTelefono = $arrayFormasContacto[$i]["valor"];
                                            break;
                                        }
                                        if ($arrayFormasContacto[$i]["formaContacto"] == "Telefono Movil CNT")
                                        {
                                            $strTelefono = $arrayFormasContacto[$i]["valor"];
                                            break;
                                        }
                                        if ($arrayFormasContacto[$i]["formaContacto"] == "Telefono Movil Digicel")
                                        {
                                            $strTelefono = $arrayFormasContacto[$i]["valor"];
                                            break;
                                        }
                                        if ($arrayFormasContacto[$i]["formaContacto"] == "Telefono Movil Movistar")
                                        {
                                            $strTelefono = $arrayFormasContacto[$i]["valor"];
                                            break;
                                        }
                                        if ($arrayFormasContacto[$i]["formaContacto"] == "Telefono Movil Referencia IPCC")
                                        {
                                            $strTelefono = $arrayFormasContacto[$i]["valor"];
                                            break;
                                        }
                                        if ($arrayFormasContacto[$i]["formaContacto"] == "Telefono Movil Tuenti")
                                        {
                                            $strTelefono = $arrayFormasContacto[$i]["valor"];
                                            break;
                                        }
                                    }
                                    $arrayParametroKnb     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                   ->getOne('INVOCACION_KONIBIT_ACTUALIZACION', 
                                                                                            'TECNICO', 
                                                                                            'DEBITOS',
                                                                                            'WS_KONIBIT', 
                                                                                            '', 
                                                                                            '', 
                                                                                            '', 
                                                                                            '', 
                                                                                            '', 
                                                                                            $arrayParams['strCodEmpresa']);
                                    
                                    $arrayListadoServicios = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                             ->get('PRODUCTOS ADICIONALES AUTOMATICOS','COMERCIAL','',
                                                                                   'Lista de productos adicionales automaticos',
                                                                                   '','','','','',$arrayParams['strCodEmpresa']);
                                    
                                    if (is_object($objServ->getProductoId()))
                                    {
                                        $intIdProdKon = $objServ->getProductoId()->getId();
                                        foreach($arrayListadoServicios as $objListado)
                                        {
                                            // Si encuentra un producto konibit procede pasar la caracteristica
                                            if ($intIdProdKon == $objListado['valor1'] && $objListado['valor3'] == "SI")
                                            {   //DATA
                                                $intContKonibit = $intContKonibit + 1;
                                                if ($intContKonibit > 1)
                                                {
                                                    $strLoginOrigen   = $objInfoPuntoClonado->getLogin();
                                                    $intIdPuntoOrigen = $objInfoPuntoClonado->getId();
                                                }
                                                
                                                //PRODUCTOS
                                                $objProductos   = array('orderID'      => $objServ->getId(),
                                                                        'productSKU'   => $objInfoServicioClonado->getProductoId()
                                                                                                                 ->getCodigoProducto(),
                                                                        'productName'  => $objInfoServicioClonado->getProductoId()
                                                                                                                 ->getDescripcionProducto(),
                                                                        'quantity'     => '1',
                                                                        'included'     => false,
                                                                        'productoId'   => $intIdProdKon,
                                                                        'migrateTo'    => $objInfoServicioClonado->getId(),
                                                                        'status'       => 'active'
                                                                       );

                                                $arrayPdts[]    = $objProductos;
                                                array_push( $arrayListProdKon,$objProductos );
                                                
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $objPlanDet = $this->emcom->getRepository('schemaBundle:InfoPlanDet')
                                                                  ->findBy(array('planId' => $objServ->getPlanId(),
                                                                                 'estado' => "Activo"));


                                        $strPlanInternet = $objServ->getPlanId()->getNombrePlan();
                                        if(($objPlanDet))
                                        {
                                            foreach($objPlanDet as $idxPlanDet)
                                            {
                                                $objProducto = $this->emcom->getRepository('schemaBundle:AdmiProducto')
                                                                           ->find($idxPlanDet->getProductoId());

                                                if(is_object($objProducto))
                                                {
                                                    $intIdProdKon = $idxPlanDet->getProductoId();
                                                    foreach($arrayListadoServicios as $objListado)
                                                    {
                                                        // Si encuentra un producto konibit procede pasar la caracteristica
                                                        if ($intIdProdKon == $objListado['valor1'] && $objListado['valor3'] == "SI")
                                                        {   
                                                            //DATA
                                                            $intContKonibit = $intContKonibit + 1;
                                                            if ($intContKonibit > 1)
                                                            {
                                                                $strLoginOrigen   = $objInfoPuntoClonado->getLogin();
                                                                $intIdPuntoOrigen = $objInfoPuntoClonado->getId();
                                                            }
                                                            $arrayPdts      = array();
                                                            
                                                            //PRODUCTOS
                                                            $objProductos   = array('orderID'      => $objServ->getId(),
                                                                                    'productSKU'   => $objProducto->getCodigoProducto(),
                                                                                    'productName'  => $objProducto->getDescripcionProducto(),
                                                                                    'quantity'     => '1',
                                                                                    'included'     => true,
                                                                                    'productoId'   => $intIdProdKon,
                                                                                    'migrateTo'    => $objInfoServicioClonado->getId(),
                                                                                    'status'       => 'active'
                                                                                   );

                                                            $arrayPdts[]    = $objProductos;
                                                            array_push( $arrayListProdKon,$objProductos );
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    // Armo mi Array de envio a Konibit
            
                                    $arrayTokenCas  = $this->serviceTokenCas->generarTokenCas();

                                    $objDataProd    = array(
                                        'companyName'   => $objInfoPersonaEmpresaRol->getPersonaId()
                                            ->getRazonSocial() ?
                                            $objInfoPersonaEmpresaRol->getPersonaId()
                                            ->getRazonSocial() :
                                            $objInfoPersonaEmpresaRol->getPersonaId()
                                            ->getNombres() . ''
                                            . $objInfoPersonaEmpresaRol->getPersonaId()
                                            ->getApellidos(),
                                        'companyCode'   => $objInfoPuntoClonado->getId(),
                                        'companyID'     => $arrayParams['identificacionCliente'],
                                        'contactName'   => $objInfoPersonaEmpresaRol->getPersonaId()
                                            ->getRazonSocial() ?
                                            $objInfoPersonaEmpresaRol->getPersonaId()
                                            ->getRazonSocial() :
                                            $objInfoPersonaEmpresaRol->getPersonaId()
                                            ->getNombres() . ''
                                            . $objInfoPersonaEmpresaRol->getPersonaId()
                                            ->getApellidos(),
                                        'email'         => $arrayParams["correoElectronico"],
                                        'phone'         => $arrayParams["telefono"],
                                        'login'         => $objInfoPuntoClonado->getLogin(),
                                        'plan'          => $strPlanInternet,
                                        'address'       => $objInfoPuntoClonado->getDireccion(),
                                        'city'          => $objInfoPuntoClonado->getPuntoCoberturaId()
                                            ->getNombreJurisdiccion(),
                                        'sector'        => $objInfoPuntoClonado->getSectorId()
                                            ->getNombreSector(),
                                        'status'        => 'active',
                                        'products'      => $arrayPdts
                                    );
                                    //DATA
                                    $arrayData      = array(
                                        'action'        => (isset($arrayParametroKnb["valor5"]) &&
                                            !empty($arrayParametroKnb["valor5"]))
                                            ? $arrayParametroKnb["valor5"] : "",
                                        'partnerID'     => (isset($arrayParametroKnb["valor7"]) &&
                                            !empty($arrayParametroKnb["valor7"]))
                                            ? $arrayParametroKnb["valor7"] : "001",
                                        'companyCode'   => $intIdPuntoOrigen,
                                        'companyID'     => $objPersonaOrigen->getIdentificacionCliente(),
                                        'contactName'   => $objPersonaOrigen->getRazonSocial() ?
                                            $objPersonaOrigen->getRazonSocial() :
                                            $objPersonaOrigen->getNombres() . " " .
                                            $objPersonaOrigen->getApellidos(),
                                        'login'         => $strLoginOrigen,
                                        'data'          => $objDataProd,
                                        'requestNumber' => '1',
                                        'timestamp'     => ''
                                    );

                                    $arrayKonibit   = array(
                                        'identifier'    => $objInfoServicioClonado->getId(),
                                        'type'          => (isset($arrayParametroKnb["valor4"]) &&
                                            !empty($arrayParametroKnb["valor4"]))
                                            ? $arrayParametroKnb["valor4"] : "",
                                        'retryRequered' => true,
                                        'process'       => (isset($arrayParametroKnb["valor6"]) &&
                                            !empty($arrayParametroKnb["valor6"]))
                                            ? $arrayParametroKnb["valor6"] : "",
                                        'origin'        => (isset($arrayParametroKnb["valor3"]) &&
                                            !empty($arrayParametroKnb["valor3"]))
                                            ? $arrayParametroKnb["valor3"] : "",
                                        'user'          => $arrayParams['strUsrCreacion'],
                                        'uri'           => (isset($arrayParametroKnb["valor1"]) &&
                                            !empty($arrayParametroKnb["valor1"]))
                                            ? $arrayParametroKnb["valor1"] : "",
                                        'executionIp'   => $arrayParams['strClientIp'],
                                        'data'          => $arrayData
                                    );


                                    $arrayEnvKon[] = array(
                                        'strToken'         => $arrayTokenCas['strToken'],
                                        'strUser'          => $arrayParams['strUsrCreacion'],
                                        'strIp'            => $arrayParams['strClientIp'],
                                        'arrayPropiedades' => $arrayKonibit
                                    );
                                }
                            } 
                        }
                       
                    }
                    
                }

            }
            
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->commit();
            }
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->commit();
            }
            if($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->commit();
            }
            $this->emcom->getConnection()->close();
            $this->emInfraestructura->getConnection()->close();
            $this->emComunicacion->getConnection()->close();

            
            $arrayKonibit[0]['arrayPropiedades']['data']['data']['products'] = $arrayListProdKon;
            $arrayEnvKonibit = array();
            array_push( $arrayEnvKonibit, $arrayKonibit[0] );
            
            $strStatus = "OK";
            if($arrayParams['yaexiste'] == 'S' && $boolExisPerConContrato)
            {
                if (!empty($arrayKonibit) && $arrayParams['strPrefijoEmpresa'] == 'MD') 
                {
                    foreach($arrayEnvKonibit as $envkon)
                    {
                        $this->serviceKonibit->envioAKonibit($envkon);
                    }
                }
            }
            else
            {
                if (!empty($arrayEnvKon) && $arrayParams['strPrefijoEmpresa'] == 'MD') 
                {
                    foreach($arrayEnvKonibit as $envkon)
                    {
                        $this->serviceKonibit->envioAKonibit($envkon);
                    }
                }
            }

        }
         catch(\Exception $e)
        {
            $strStatus     = "ERROR";
            $strMensaje    = $e->getMessage();
            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
            }                        
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            } 
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }  
            $this->emcom->getConnection()->close();
            $this->emComunicacion->getConnection()->close();  
            $this->emInfraestructura->getConnection()->close();
        }


   

      

       //valida que exista represenatnte legal para persona juridica  
       if (($strStatus === "OK")
       && ($arrayParams['strPrefijoEmpresa'] === 'MD' || $arrayParams['strPrefijoEmpresa'] === 'EN' )
       && (!$boolExistePersona || !$boolExisPerConContrato) 
       && ($arrayParams['tipoTributario']=== 'JUR'))
       {
 
         $arrayDatosRL = json_decode($arrayParams['strDatosRepresentanteLegal'],true); 
         if (count($arrayDatosRL) != 0)
          {
               $arrayTokenCas = $this->serviceTokenCas->generarTokenCas();  
               $arrayParamsRepresent = array(
               'token'                                => $arrayTokenCas['strToken'],
               'esCambioRazonSocial'                  => true,
               'codEmpresa'                           => $arrayParams['strCodEmpresa'] ,   
               'prefijoEmpresa'                       => $arrayParams['strPrefijoEmpresa'] ,        
               'oficinaId'                            => $arrayParams['intIdOficina'] ,   
               'origenWeb'                            => $arrayParams['origen_web'] ,  
               'clientIp'                             => $arrayParams['strClientIp'] ,        
               'usrCreacion'                          => $arrayParams['strUsrCreacion'] ,  
               'idPais'                               => $arrayParams['intIdPais'] ,          
               'tipoIdentificacion'                   => $arrayParams['tipoIdentificacion'],
               'identificacion'                       => $arrayParams['identificacionCliente'],
               'representanteLegal'                   => $arrayDatosRL
               );   
                                                                                           
               $objResponseRepresent     =  $this->serviceRepresentanteLegalMs->wsActualizarRepresentanteLegal($arrayParamsRepresent);
               if ($objResponseRepresent['strStatus']!='OK' ) 
               {     
                   $strStatus  = 403; 
                   $strMensaje = "Fallo en representante legal: " .$objResponseRepresent['strMensaje'];
               }   

          }
      
       } 

       
        if($strStatus === "OK")
        {
            $strMuestraErrorAdicionalCRS    = "NO";
            $strMsjUsrErrorAdicionalCRS     = "";
            $strMensajeCorreoECDF           = "";
            if(isset($arrayRespuestaCRSClienteExistente) && !empty($arrayRespuestaCRSClienteExistente))
            {
                $strMensajeCorreoECDF = $arrayRespuestaCRSClienteExistente["strMensajeCorreoECDF"];
                $arrayServiciosLdap = $arrayRespuestaCRSClienteExistente["arrayServiciosLdap"];
                //eliminación de Ldap de antiguo servicio y creación de Ldap de nuevo servicio
                if(isset($arrayServiciosLdap) && !empty($arrayServiciosLdap))
                {
                    foreach ($arrayServiciosLdap as $arrayServicioLdap)
                    {
                        $arrayRespuestaLdap = $this->serviceServicioTecnico
                                                   ->configurarLdapCambioRazonSocial(array( "servicioAnterior"  => 
                                                                                            $arrayServicioLdap['servicioAnterior'],
                                                                                            "servicioNuevo"     => 
                                                                                            $arrayServicioLdap['servicioNuevo'],
                                                                                            "usrCreacion"       => $arrayParams['strUsrCreacion'],
                                                                                            "ipCreacion"        => $arrayParams['strClientIp'],
                                                                                            "prefijoEmpresa"    => $arrayParams['strPrefijoEmpresa']
                                                                                      ));
                        if($arrayRespuestaLdap["status"] === "ERROR" || !empty($arrayRespuestaLdap["mensaje"]))
                        {
                            $strMuestraErrorAdicionalCRS    = "SI";
                            $strMsjUsrErrorAdicionalCRS     .= $arrayRespuestaLdap["mensaje"] . ". ";
                        }
                    }
                }
                
                $arrayServiciosNetlifeCloud = $arrayRespuestaCRSClienteExistente["arrayServiciosNetlifeCloud"];
                if(isset($arrayServiciosNetlifeCloud) && !empty($arrayServiciosNetlifeCloud))
                {
                    //FACTURACIÓN DE LOS SERVICIOS CANCELADOS NETLIFECLOUD   
                    foreach ($arrayServiciosNetlifeCloud as $arrayServicioNetlifeCloud)
                    {
                        //Se invoca a la función generarFacturaServicioCancelado para generar factura a los servicios NetlifeCloud cancelados
                        $arrayRespuestaFacturaNetlifeCloud  = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                                          ->generarFacturaServicioCancelado(
                                                                                array(
                                                                                        'strPrefijoEmpresa' => $arrayParams['strPrefijoEmpresa'],
                                                                                        'strEmpresaCod'     => $arrayParams['strCodEmpresa'],
                                                                                        'strIp'             => $arrayParams['strClientIp'], 
                                                                                        'intServicioId'     => 
                                                                                        $arrayServicioNetlifeCloud["intIdServicio"]
                                                                                ));
                        if($arrayRespuestaFacturaNetlifeCloud["status"] == 'ERROR')
                        {
                            $strMuestraErrorAdicionalCRS    = "SI";
                            $strMsjUsrErrorAdicionalCRS     .= $arrayRespuestaFacturaNetlifeCloud["mensaje"] . ". ";
                        }
                    }
                }
            }

            // Procesamos los servicios con promociones
            if (!empty($arrayServiciosPromociones))
            {
                foreach ($arrayServiciosPromociones as $arrayServPromo)
                {
                    //EJECUTAR PROMOCIONES DE SERVICIOS POR CAMBIO DE RAZON SOCIAL
                    $arrayParametrosInfoBw = array();
                    $arrayParametrosInfoBw['intIdServicio']     = $arrayServPromo['destino'];
                    $arrayParametrosInfoBw['intIdEmpresa']      = $arrayParams["strCodEmpresa"];
                    $arrayParametrosInfoBw['strTipoProceso']    = "CAMBIO_RAZON_SOCIAL";
                    $arrayParametrosInfoBw['strValor']          = $arrayServPromo['origen'];
                    $arrayParametrosInfoBw['strUsrCreacion']    = $arrayParams['strUsrCreacion'];
                    $arrayParametrosInfoBw['strIpCreacion']     = $arrayParams['strClientIp'];
                    $arrayParametrosInfoBw['strPrefijoEmpresa'] = $arrayParams['strPrefijoEmpresa'];
                    $this->servicePromociones->configurarPromocionesBW($arrayParametrosInfoBw);
                }
            }
            
            if($strMuestraErrorAdicionalCRS === "SI")
            {
                $strMensaje =   'Se ha realizado de manera correcta el proceso principal de la ejecución por cambio de razón social. '.
                                'Sin embargo, se tuvieron los siguientes inconvenientes: '.$strMsjUsrErrorAdicionalCRS.
                                'Por favor verificar con el departamento de Sistemas!';
                $this->utilService->insertError('Telcos+', 
                                                'InfoContratoAprobService->ejecutaCambioRazonSocialPorPunto',
                                                $strMsjUsrErrorAdicionalCRS, 
                                                $arrayParams['strUsrCreacion'], 
                                                $arrayParams['strClientIp']
                                               );
                $strTipoMensaje = "warning";
            }
        }
        
        if($strRetornarRSPuntos == 'S')
        {
            $arrayRespuestaProceso = array( "status"                    => $strStatus,
                                            "mensaje"                   => $strMensaje,
                                            "tipoMensaje"               => $strTipoMensaje,
                                            "objInfoPersonaEmpresaRol"  => $objInfoPersonaEmpresaRol,
                                            "arrayServMigrados"         => $arrayServiciosRSContrato,
                                            "arrayAdendumsExcluirRS"    => $arrayAdendumsExcluirRS,
                                            "arrayPuntosCRS"            => $arrayPuntosCRSActivar,
                                            "strMensajeCorreoECDF"      => $strMensajeCorreoECDF);
        }
        else
        {
            $arrayRespuestaProceso = array( "status"                    => $strStatus,
                                            "mensaje"                   => $strMensaje,
                                            "tipoMensaje"               => $strTipoMensaje,
                                            "objInfoPersonaEmpresaRol"  => $objInfoPersonaEmpresaRol,
                                            "arrayPuntosCRS"            => $arrayPuntosCRSActivar,
                                            "strMensajeCorreoECDF"      => $strMensajeCorreoECDF);
        }

        return $arrayRespuestaProceso;
    }
 
     /**
     * Funcion que segun el tipo de caracteristica  ENLACE_DATOS o ES_BACKUP obtendra los enlaces (Extremo-Concentrador) o (Backup- Principal) 
     * existentes para un ID_SERVICIO Concentrador o Principal.  
     * Obtiene todos los enlaces extremos de un servicio concentrador o todos los enlaces backups de un servicio Principal
     * Clona la informacion del enlace para asignar el ID_SERVICIO del nuevo CONCENTRADOR  o nuevo PRINCIPAL que fue generado por el cambio de 
     * Razón Social tradicional o por Login segun el caso.
     * Se genera Historial en el servicio Extremo o en servicio BACKUP indicando que se actualiza el enlace por el cambio de Razon Social 
     * tradicional o por Login.
     * Se cancela la caracteristica ENLACE_DATOS o ES_BACKUP que contiene la referencia al servicio CONCENTRADOR  o PRINCIPAL que fue Cancelado
     * por Cambio de Razon Social Tradicional o por Login.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 18-01-2019
     * 
     * @param  Array $arrayParametros 
     *                               [
     *                                  strFechaCreacion       => Fecha de Crecaión 
     *                                  strUsrCreacion         => Usuario de Creacion
     *                                  strIpCreacion          => Ip de creacion
     *                                  objInfoServicioOrigen  => Objeto del Servicio origen del Cambio de Razon Social     
     *                                  objInfoServicioDestino => Objeto del Servicio Destino del cambio de Razon Social
     *                                  strTipoCaracteristica  => Tipo de Caracteristica a buscar ENLACE_DATOS, ES_BACKUP 
     *                               ]
     * @throws \Exception
     * @return string $strMensajeError
     * 
     */
    public function actualizaConcentradorEnExtremos($arrayParametros)
    {              
        $strEstadoCancelado      = 'Cancelado';        
        $strFechaCreacion        = $arrayParametros['strFechaCreacion'];
        $strUsrCreacion          = $arrayParametros['strUsrCreacion'];
        $strIpCreacion           = $arrayParametros['strIpCreacion'];        
        $objInfoServicioOrigen   = $arrayParametros['objInfoServicioOrigen'];
        $objInfoServicioDestino  = $arrayParametros['objInfoServicioDestino'];  
        $strTipoCaracteristica   = $arrayParametros['strTipoCaracteristica'];          
        $strObservTipoCaract     = "";
        $strMensajeError         = "";        
        try
        {
            if($strTipoCaracteristica=='ENLACE_DATOS')
            {
                $strObservTipoCaract = "Se definió nuevo Concentrador por proceso de Cambio de Razon Social por Login";
            }
            else
            {                            
                $strObservTipoCaract = "Se definió nuevo enlace Principal por proceso de Cambio de Razon Social por Login";
            }
            //Obtengo todos los enlaces (Extremo-Concentrador) o (Backup- Principal) existentes para un servicio concentrador/Principal especifico
            $arrayParametrosExtremos                          = array();
            $arrayParametrosExtremos['intIdServicio']         = $objInfoServicioOrigen->getId();
            $arrayParametrosExtremos['strTipoCaracteristica'] = $strTipoCaracteristica;
            
            $arrayEnlacesExtremosPorConcentrador = $this->emcom->getRepository('schemaBundle:infoServicio')
                                                               ->getEnlacesExtremosPorConcentrador($arrayParametrosExtremos);            
            
            $objEnlacesExtremosPorConcentrador = $arrayEnlacesExtremosPorConcentrador['objRegistros'];
            
           //Clona la informacion del enlace (Extremo o Backup) para asignar el ID_SERVICIO del nuevo (Concentrador o Principal)
            foreach($objEnlacesExtremosPorConcentrador as $arrayEnlaceExtremo)
            {               
                $objInfoServicioProdCaractEnlaceDatos = $this->emcom->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                    ->find($arrayEnlaceExtremo['intIdServicioProdCaract']);

                if(!is_object($objInfoServicioProdCaractEnlaceDatos))
                {
                    throw new \Exception('No encontro la caracteristica del extremo del enlace.');
                }
                $objInfoServProdCaractEnlaceDatosClonado = new InfoServicioProdCaract();
                $objInfoServProdCaractEnlaceDatosClonado = clone $objInfoServicioProdCaractEnlaceDatos;
                $objInfoServProdCaractEnlaceDatosClonado->setValor($objInfoServicioDestino->getId());
                $objInfoServProdCaractEnlaceDatosClonado->setFeCreacion($strFechaCreacion);
                $objInfoServProdCaractEnlaceDatosClonado->setUsrCreacion($strUsrCreacion);
                $this->emcom->persist($objInfoServProdCaractEnlaceDatosClonado);
                $this->emcom->flush();

                //Se genera Historial en el servicio (Extremo o Backup) indicando que se actualiza el enlace por el cambio de Razon Social
                $objInfoServicioExtremo = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                      ->find($arrayEnlaceExtremo['intIdServicioExtremo']);
                if(!is_object($objInfoServicioExtremo))
                {
                    throw new \Exception('No encontro el Servicio extremo del Enlace de Datos');
                }
                //Obtengo el Punto y la Persona del (Concentrador o Principal) Anterior para registro del Historial
                $objInfoPuntoConcentradorAnterior = $this->emcom->getRepository('schemaBundle:InfoPunto')
                                                                ->find($arrayEnlaceExtremo['intIdPuntoConcentrador']);
                if(!is_object($objInfoPuntoConcentradorAnterior))
                {
                    throw new \Exception('No encontro el Punto Concentrador del Enlace de Datos');
                }
                $objInfoPersonaConcentradorAnterior = $objInfoPuntoConcentradorAnterior->getPersonaEmpresaRolId()->getPersonaId();
                $strClienteConcentradorAnterior     = sprintf("%s", $objInfoPersonaConcentradorAnterior);
                
                $objInfoPersonaConcentradorNuevo = $objInfoServicioDestino->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();                
                $strClienteConcentradorNuevo     = sprintf("%s", $objInfoPersonaConcentradorNuevo);

                $strDatosAnteriores = "<b>Datos Anteriores:</b><br>                       
                                    Cliente: " . $strClienteConcentradorAnterior . "<br>
                                    Login: " . $arrayEnlaceExtremo['strLoginConcentrador'] . "<br>
                                    LoginAux: " . $arrayEnlaceExtremo['strLoginAuxConcentrador'] . "<br>";

                $strDatosNuevos = "<b>Datos Nuevos:</b><br>
                                    Cliente: " . $strClienteConcentradorNuevo . "<br>                                    
                                    Login: " . $objInfoServicioDestino->getPuntoId()->getLogin() . "<br>
                                    LoginAux: " . $objInfoServicioDestino->getLoginAux() . "<br>";

                $strObservacionDestino = $strObservTipoCaract. ".<br><br>" . $strDatosNuevos . "<br>" . $strDatosAnteriores;

                $objInfoServicioHistorialEnlace = new InfoServicioHistorial();
                $objInfoServicioHistorialEnlace->setServicioId($objInfoServicioExtremo);
                $objInfoServicioHistorialEnlace->setFeCreacion($strFechaCreacion);
                $objInfoServicioHistorialEnlace->setUsrCreacion($strUsrCreacion);
                $objInfoServicioHistorialEnlace->setEstado($objInfoServicioExtremo->getEstado());
                $objInfoServicioHistorialEnlace->setObservacion($strObservacionDestino);
                $this->emcom->persist($objInfoServicioHistorialEnlace);
                $this->emcom->flush();

                //Se cancela la caracteristica ENLACE_DATOS o ES_BACKUP que contiene la referencia al servicio CONCENTRADOR  o PRINCIPAL que fue 
                //Cancelado por Cambio de Razon Social Tradicional o por Login.
                $objInfoServicioProdCaractEnlaceDatos->setEstado($strEstadoCancelado);
                $objInfoServicioProdCaractEnlaceDatos->setFeUltMod($strFechaCreacion);
                $objInfoServicioProdCaractEnlaceDatos->setUsrUltMod($strUsrCreacion);
                $this->emcom->persist($objInfoServicioProdCaractEnlaceDatos);
                $this->emcom->flush();
            }
        }
        catch(\Exception $e)
        {
            $strMensajeError = "No se pudo definir el nuevo servicio Concentrador o Principal en los enlaces (Extremo-Concentrador) o"
                . " (Backup- Principal)"
                . " por proceso de Cambio de Razon Social <br>"
                . $e->getMessage() . ". Favor notificar a Sistemas.";
            $this->utilService->insertError('Telcos+', 'actualizaConcentradorEnExtremos', $e->getMessage(), $strUsrCreacion, $strIpCreacion
            );
        }
        return $strMensajeError;
    }
    
    /**   
     * Documentación para el método 'cambioRazonSocialClienteExistente'.
     *
     * Metodo que se encarga de ejecutar el "Cambio de Razon Social por Punto" cuando se realice sobre un cliente ya existente con rol de "Cliente" 
     * y con Contrato Activo:  
     * 1) Se crearan los nuevos logines en el cliente ya existente, bajo el contrato ya existente, se guardara informacion de los
     *    Nuevos Puntos asi como toda la data relacionada a estos.
     * 2) Los Logines origen del "Cambio de Razon Social por Punto" quedaran en estado Cancelado asi como toda la data relacionada a estos,
     *    no podra reversarse.                              
     *              
     * @param array    $arrayParams 
     * @param array    $arrayPuntosSeleccionados
     * @param object   $objInfoPersonaEmpresaRol
     * @param object   $objAdmiCaracteristica
     * @param object   $objPersonaEmpresaRolOrigen
     * 
     * @throws Exception     
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 23-10-2015  
     * 
     * Se agregan campos Nuevos CarnetConadis, EsPrepago, PagaIva, ContribuyenteEspecial y Combo de Oficinas de Facturacion
     * para el caso de ser empresa Telconet se deben pedir dichos campos, en el caso de empresa Megadatos se deben setear los
     * valores por dafault
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.1 06-06-2016   
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 21-06-2015    Se agrega cancelación de LDAP de servicios antiguos y creación de LDAP nuevos servicios
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 27-06-2015    Se corrige cancelación de LDAP de servicios antiguos y creación de LDAP nuevos servicios
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.4 06-07-2016    Se agrega registro de motivo en cancelacion de servicios
     *
     * Se agrega informacion de Contactos a Clonarse en el Proceso de Cambio de Razon Social:
     * -Clono Contactos a nivel de Cliente hacia la nueva razon social
     * -Clono Contactos a nivel de Punto hacia los Logines de la nueva razon social    
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.4 20-06-2016  
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 06-03-2017 - Se agrega historial a nivel del servicio marcando como fecha de creación la fecha con la cual se realiza el cálculo
     *                           de los meses restantes para facturar el servicio.
     * 
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>
     * @version 1.6 04-05-2017 -Se guarda la Plantilla de Comisionistas asociada a la antigua Razon social en la nueva razon social.
     * Se Cancela Plantilla de comisionistas en la antigua Razon Social
     * Se guarda Historial con fecha y observacion 'Se Confirmo el Servicio' en los servicios de la nueva Razon Social en base a la Fecha 
     * de Activacion o Confirmacion de Servicio de los servicios antiguos. 
     * Se Cancelan las Plantillas de Comisionistas asociadas a los servicios del cliente origen del Cambio de Razon Social
     * Se genera Historial de Cancelacion de Plantilla.     
     * 
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>
     * @version 1.7 15-06-2017 - Se modifica el ingreso de Datos de Envio por Punto.
     * Se debe obtener la informacion de correos y telefonos del contacto de Facturacion del Punto o del cliente en ese orden. 
     * Funcion a llamar es la existente para la generacion del XML (DB_FINANCIERO.FNCK_COM_ELECTRONICO.GET_ADITIONAL_DATA_BYPUNTO)
     * Se debe considerar eliminar duplicidad de registros y solo se registrara un maximo de 2 correos y 2 telefonos separados por ;
     * La informacion del nombre_envio, direccion_envio sera tomados de la nueva Razon Social.
     * La informacion del Sector_id será tomado del Punto Clonado.   
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.5
     * @since 16-05-2018
     * Cambio por ciclos de facturación:
     * Se inserta en la InfoPersonaEmpresaRolHisto cuando el ciclo del cliente origen es diferente al ciclo del cliente destino.
     *
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>
     * @version 1.6 22-06-2018- Se agrega que se generen las caracteristicas de los servicios en estado activo, y se considera para el Producto
     *                          Fox_Primium que al clonar dichas caracteristicas se marque la caracteristica 'MIGRADO_FOX' en S.
     * 
     * @author Hector Lozano<hlozano@telconet.ec>
     * @version 1.7
     * @since  13-07-2018
     * Se agrega validación para que no clone las características del servicio Netlifecloud cuando se realiza 
     * el Cambio de Razón Social por login. En lugar de clonar se guardan nuevas caracteristicas del servicio
     * Netlifecloud invocando al WebService de Intcomex. 
     * 
     * @author Hector Lozano<hlozano@telconet.ec>
     * @version 1.8
     * @since  25-07-2018
     * Se agrega validación para facturar a los servicios NetlifeCloud cancelados cuando se realiza cambio de Razón Social por login. 
     * 
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.9
     * @since 08-06-2018
     * Se agrega la característica al servicio para facturarlo posteriormente.
     * 
     * @author Hector Lozano<hlozano@telconet.ec>
     * @version 1.10
     * @since  31-08-2018
     * Se agrega validación para no clonar los descuentos al nuevo cliente cuando se realiza el Cambio de Razón Social.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 2.1
     * @since 27-09-2018
     * Se obtiene el ciclo de origen antes de validar si el cliente destino es diferente.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 2.2
     * @since 21-01-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.3 17-02-2019 Se agrega la eliminación y creación del ldap en servicios Internet Small Business y TelcoHome al cambiar razón social
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.4 19-06-2019 Se agrega clonación de solicitudes de Agregar Equipo y Cambio de equipo por soporte en proceso de cambio
     *                         de razón social de puntos de un cliente
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 2.5 17-01-2020 Se agrega registro de historial con fecha de activación mínima del servicio origen.
     * 
     * @since 2.3
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.6 14-10-2020 - Para productos Paramount y Noggin al momento de realizar un cambio se generan un nuevo usuario y password.
     * @since 2.5
     * 
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 2.7 28-10-2020 - Se agrega bandera para realizar proceso a los productos Paramount y Noggin 
     *                            dentro de un plan o como producto adicional.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.8 09-11-2020 Se modifica la función para obtener los servicios netlifecloud además de los servicios que requieren ldap y se elimina
     *                         la invocación a la función generarFacturaServicioCancelado, ya que ésta invoca a un paquete que es el que da commit
     *                         o rollback, provocando así varios errores de la función principal
     *
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 2.9 07-1-2021 - Se agrega codigo para acceder a la plantilla de envio de sms para productos Paramount y Noggin.
     *                           Se agrega validacion para que busque las caracteristicas del correo del producto, se envia el ID servicio.
     *
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 3.0 07-01-2021 - Se agrega en la observacion de servicio nuevo el numero del servicio anterior 
     *                           para proceso de contrato digital.
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.10 06-04-2021 - Se agrega logica que permita clonar en servicios Extender Dual Band solicitudes de AGREGAR EQUIPO
     *                            del punto origen al destino.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.10 15-04-2021 - Se usan parámetros ya usados para W+AP
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 2.11 09-08-2021 - Se parametriza validaciones para el Producto ECDF.
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 2.12 12-05-2022 - Se verifica servicios con planes para enviarlos al metodo principal y valide si tienen
     *                            promocion por franja horaria
     *
     * Se obtiene segun el tipo de caracteristica  ENLACE_DATOS o ES_BACKUP  los enlaces (Extremo-Concentrador) o (Backup- Principal) 
     * existentes para un ID_SERVICIO Concentrador o Principal.       
     * Clona la informacion del enlace para asignar el ID_SERVICIO del nuevo CONCENTRADOR  o nuevo PRINCIPAL que fue generado por el cambio de 
     * Razón Social tradicional o por Login segun el caso.
     * Se genera Historial en el servicio Extremo o en servicio BACKUP indicando que se actualiza el enlace por el cambio de Razon Social    
     * Se cancela la caracteristica ENLACE_DATOS o ES_BACKUP que contiene la referencia al servicio CONCENTRADOR  o PRINCIPAL que fue Cancelado
     * por Cambio de Razon Social.
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 2.12 04-12-2021 - Se parametriza para configurar las caracteristicas del correo electronico 
     * en el Producto ECDF.
     * 
     * @author Alex Gomez <algomez@telconet.ec>
     * @version 2.13 10-08-2022 Se modifica estado de los puntos y servicios clonados por CRS tradicional y por punto 
     *                          cuando el contrato aun no ha sido autorizado. Aplica para MD y contrato digital.
     * 
     * @author Jessenia Piloso <jpiloso@telconet.ec>
     * @version 2.13 23-10-2022 - Se actualiza el proceso para agregar validaciones para el flujo de cambio de razon social 
     *                             de NetlifeCam Outdoor
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 2.14 26-08-2022 - Se agrega consumo a MS por productos Konibit.
     *
     */
    
    public function cambioRazonSocialClienteExistente($arrayParams, $arrayPuntosSeleccionados, $objInfoPersonaEmpresaRol,
                                                      $objAdmiCaracteristica,$objPersonaEmpresaRolOrigen)
    {
        $strFechaCreacion       = new \DateTime('now');
        $strEstadoCancelado     = 'Cancelado';
        $strEstadoActivo        = 'Activo';
        $objAdmiCicloOrigen     = new AdmiCiclo();
        $strAsuntoNuevoServicio = "";
        $strPlantillaSms        = "";
        $arrayServiciosPromociones = array();
        $arrayPuntosCRSActivar     = array();
        $boolAsignaEstadoPreactivo    = false;
        $strEstadoServicioPreactivo   = '';
        $strMensajeEstadoPreactivo    = '';

        // ALAMACENA DE PRODUCTOS KONIBIT
        $arrayListProdKon      = array();
        $arrayListProdKon      = [];

        //ALMACENA EL LOGIN ORIGEN
        $strLoginOrigenKon     = '';

        //ALMACENA EL COMPANY CODE ORIGEN
        $intCompCodeKon        = 0;

        //Consulta nuevo estado para servicios creados por Cambio de Razón Social por Login
        //previo a la autorizacion del contrato. Solo aplica para MD y contrato Digital
        if(($arrayParams['strPrefijoEmpresa'] === 'MD' || $arrayParams['strPrefijoEmpresa'] === 'EN') 
            && $arrayParams['intContratoFisico'] != 1)
        {
            $boolAsignaEstadoPreactivo = true;

            $arrayEstadosServicios = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne(
                                                        'ESTADOS_CAMBIO_RAZON_SOCIALXPUNTO',
                                                        'COMERCIAL',
                                                        'CAMBIO_RAZON_SOCIAL_POR_PUNTO',
                                                        '','','','','','',
                                                        $arrayParams['strCodEmpresa']);
            
            if(isset($arrayEstadosServicios) && !empty($arrayEstadosServicios))
            {
                $strEstadoServicioPreactivo = $arrayEstadosServicios["valor1"];
            }
            else
            {
                $boolAsignaEstadoPreactivo = false;
            }

            $arrayParamObservacionHist = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne(
                                                            'OBSERVACION_CAMBIO_ESTADO_PREACTIVO',
                                                            'COMERCIAL',
                                                            'CAMBIO_RAZON_SOCIAL_POR_PUNTO',
                                                            'OBSERVACION_HIST_SERVICIO_PREACTIVO',
                                                            '','','','','',
                                                            $arrayParams['strCodEmpresa']);
            
            if(isset($arrayParamObservacionHist) && !empty($arrayParamObservacionHist))
            {
                $strMensajeEstadoPreactivo = $arrayParamObservacionHist["valor1"];
            }
            else
            {
                throw new \Exception('No se encontro parametro por mensaje de confirmacion CRS');
            }
        }


        //se recupera motivo de cancelacion de servicios
        $objMotivoCambioRs     = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo('Cambio de Razon Social');
        
        $objInfoPersona        = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                      ->findOneByIdentificacionCliente($arrayParams['identificacionCliente']);
        $arrayServiciosLdap         = array();
        $arrayServiciosNetlifeCloud = array();
        $objInfoPersona->setDireccionTributaria($arrayParams['direccionTributaria']);
        $this->emcom->persist($objInfoPersona);
        $this->emcom->flush();
         //Clono Contactos a nivel de Cliente hacia la nueva razon social
        $arrayContactos = $this->emcom->getRepository('schemaBundle:InfoPersonaContacto')
                               ->findByPersonaEmpresaRolIdYEstado($objPersonaEmpresaRolOrigen->getId(),$strEstadoActivo);
        
        $strMensajeCorreoECDF       = "";
        $strTieneCorreoElectronico  = "NO";
        if(isset($arrayParams["tieneCorreoElectronico"]) && !empty($arrayParams["tieneCorreoElectronico"]))
        {
          $strTieneCorreoElectronico = $arrayParams["tieneCorreoElectronico"];
        }
        if( $arrayContactos )
        {
            foreach( $arrayContactos as $contacto )
            {
                $objContactoClonado = clone $contacto;
                $objContactoClonado->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                $objContactoClonado->setFeCreacion($strFechaCreacion);
                $objContactoClonado->setIpCreacion($arrayParams['strClientIp']);   
                $objContactoClonado->setUsrCreacion($arrayParams['strUsrCreacion']);
                $this->emcom->persist($objContactoClonado);
                $this->emcom->flush();
            }
        }
        $arrayFormasContacto = array();
        if($arrayParams['formas_contacto'])
        {
            $arrayExplodeFormasContacto = explode(',', $arrayParams['formas_contacto']);
            for($i = 0; $i < count($arrayExplodeFormasContacto); $i+=3)
            {
                $arrayFormasContacto[] = array('formaContacto' => $arrayExplodeFormasContacto[$i + 1],
                                               'valor'         => $arrayExplodeFormasContacto[$i + 2]);
            }
        }
        //Mantiene el ciclo en caso que pertenezcan a ciclos diferentes.
        $strAplicaCiclosFac = $this->serviceComercial->aplicaCicloFacturacion(array("strEmpresaCod"     => $arrayParams["strCodEmpresa"],
                                                                                    "strPrefijoEmpresa" => $arrayParams["strPrefijoEmpresa"]));
        if ('S' == $strAplicaCiclosFac)
        {
            //Obtengo Característica de CICLO_FACTURACION
            $objCaracteristicaCiclo = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                  ->findOneBy(array("descripcionCaracteristica" => "CICLO_FACTURACION",
                                                                    "estado" => "Activo"));
            if (!is_object($objCaracteristicaCiclo))
            {
                throw new \Exception('No existe Caracteristica CICLO_FACTURACION - No se pudo generar el Cambio de Razón Social por Login');
            }

            $objRepositoryEmpRolCarac = $this->emcom->getRepository("schemaBundle:InfoPersonaEmpresaRolCarac");
            $objPerEmpRolCaracOrigen = $objRepositoryEmpRolCarac->findOneBy(array("personaEmpresaRolId" => $objPersonaEmpresaRolOrigen->getId(),
                                                                                  "estado"              => "Activo",
                                                                                  "caracteristicaId"    => $objCaracteristicaCiclo->getId()));
            $objPerEmpRolCaracDestino = $objRepositoryEmpRolCarac->findOneBy(array("personaEmpresaRolId" => $objInfoPersonaEmpresaRol->getId(),
                                                                                   "estado"              => "Activo",
                                                                                   "caracteristicaId"    => $objCaracteristicaCiclo->getId()));
            //Si el ciclo es diferente, se inserta un historial para identificar el caso.
            $objAdmiCicloOrigen  = $this->emcom->getRepository("schemaBundle:AdmiCiclo")
                                               ->find($objPerEmpRolCaracOrigen->getValor());
            if ($objPerEmpRolCaracOrigen->getValor() != $objPerEmpRolCaracDestino->getValor())
            {
                $objAdmiCicloDestino = $this->emcom->getRepository("schemaBundle:AdmiCiclo")
                                                   ->find($objPerEmpRolCaracDestino->getValor());
                $objInfoPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
                $objInfoPersonaEmpresaRolHisto->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                $objInfoPersonaEmpresaRolHisto->setEstado($objInfoPersonaEmpresaRol->getEstado());
                $objInfoPersonaEmpresaRolHisto->setFeCreacion(new \DateTime('now'));
                $objInfoPersonaEmpresaRolHisto->setIpCreacion($arrayParams['strClientIp']);
                $objInfoPersonaEmpresaRolHisto->setUsrCreacion('cicloFactCRSxP');
                $objInfoPersonaEmpresaRolHisto->setObservacion("Se realiza el CRS a un cliente que se encuentra en un ciclo distinto al origen:" .
                                "Origen: " . $objAdmiCicloOrigen->getNombreCiclo() . " |Destino: " . $objAdmiCicloDestino->getNombreCiclo());
                $this->emcom->persist($objInfoPersonaEmpresaRolHisto);
            }
        }
        
        $arrayEnvKon  = array();
        // Recorro arreglo de Logines a trasladar, Clono los nuevos puntos y sus servicios             
        foreach($arrayPuntosSeleccionados as $key => $value)
        {
            $intContKonibit      = 0;
            $strLogin            = '';
            $objInfoPuntoOrigen  = $this->emcom->getRepository('schemaBundle:InfoPunto')->find($value);
            $strLoginOrigen      = $objInfoPuntoOrigen->getLogin();
            $intIdPuntoOrigen    = $objInfoPuntoOrigen->getId();
            $strLoginOrigenKon   = $objInfoPuntoOrigen->getLogin();
            $intCompCodeKon      = $objInfoPuntoOrigen->getId();
            $objInfoPuntoClonado = new InfoPunto();
            $objInfoPuntoClonado = clone $objInfoPuntoOrigen;
            $objInfoPuntoClonado->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
            $objInfoPuntoClonado->setFeCreacion($strFechaCreacion);
            $objInfoPuntoClonado->setUsrCreacion($arrayParams['strUsrCreacion']);
            $objInfoPuntoClonado->setObservacion('');
            // Obtengo Login con secuencia
            $arrayPuntos = $this->emcom->getRepository('schemaBundle:InfoPunto')
                           ->findPtosPorEmpresaPorCanton($arrayParams['strCodEmpresa'], $objInfoPuntoClonado->getLogin(), 
                                                         $objInfoPuntoClonado->getSectorId()->getParroquiaId()->getCantonId()->getId(), 
                                                         9999999, 1, 0);

            $strLogin    = $objInfoPuntoClonado->getLogin() . ($arrayPuntos['total'] + 1);
            $objInfoPuntoClonado->setLogin($strLogin);

            //Setea estado pendiente a punto hasta la autorizacion del contrato
            if($boolAsignaEstadoPreactivo && $objInfoPuntoClonado->getEstado() == "Activo")
            {
                $objInfoPuntoClonado->setEstado('Pendiente');
            }

            $this->emcom->persist($objInfoPuntoClonado);
            $this->emcom->flush();
       
            //Llena array de puntos a activar por el ms
            if($boolAsignaEstadoPreactivo && $objInfoPuntoClonado->getEstado() == "Pendiente")
            {
                array_push($arrayPuntosCRSActivar,$objInfoPuntoClonado->getId());
            }

            //Clono Contactos a nivel de Punto hacia los Logines de la nueva razon social
            $arrayPuntoContactos = $this->emcom->getRepository('schemaBundle:InfoPuntoContacto')
                                        ->findByPuntoIdYEstado($value,$strEstadoActivo);
            if($arrayPuntoContactos)
            {
                foreach($arrayPuntoContactos as $contactoPto)
                {
                    $objContactoPtoClonado = clone $contactoPto;
                    $objContactoPtoClonado->setPuntoId($objInfoPuntoClonado);
                    $objContactoPtoClonado->setFeCreacion($strFechaCreacion);
                    $objContactoPtoClonado->setIpCreacion($arrayParams['strClientIp']);
                    $objContactoPtoClonado->setUsrCreacion($arrayParams['strUsrCreacion']);
                    $this->emcom->persist($objContactoPtoClonado);
                    $this->emcom->flush();
                }
            }

            $arrayParamDatoAdic                        = array();
            $arrayParamDatoAdic['objInfoPuntoClonado'] = $objInfoPuntoClonado;                 
            $arrayParamDatoAdic['objInfoPersona']      = $objInfoPersona;                  
            $arrayParamDatoAdic['strUsrCreacion']      = $arrayParams['strUsrCreacion'];  
            $arrayParamDatoAdic['intIdPunto']          = $value;  
            $arrayParamDatoAdic['strTipoCrs']          = 'Cambio_Razon_Social_Por_Login';  
            $arrayParamDatoAdic['arrayFormasContacto'] = $arrayFormasContacto;
            
            $objInfoPuntoDatoAdicionalClonado = $this->serviceInfoPunto->generarInfoPuntoDatoAdicional($arrayParamDatoAdic);
            
            
            $objSolucionesCab  = $this->emcom->getRepository('schemaBundle:InfoSolucionCab')
                                                    ->findBy(array('puntoId' => $value));

            if(isset($objSolucionesCab) && !empty($objSolucionesCab))
            {
                $intPuntoIdNuevo = $objInfoPuntoClonado->getId();
                
                foreach($objSolucionesCab as $objSolucionCab)
                { 
                    $objSolucionCab->setPuntoId($intPuntoIdNuevo);
                    $objSolucionCab->setFecCreacion($strFechaCreacion);
                    $objSolucionCab->setUsrCreacion($arrayParams['strUsrCreacion']);
                    $objSolucionCab->setUsrUltMod($arrayParams['strUsrCreacion']);
                    $objSolucionCab->setFecUltMod($strFechaCreacion);

                    $this->emcom->persist($objSolucionCab);
                    $this->emcom->flush();
                } 
            }
            
            // Obtengo los servicios Ligados al Punto que seran trasladados
            $arrayInfoServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                 ->findServiciosPorEmpresaPorPunto($arrayParams['strCodEmpresa'], $value, 99999999, 1, 0);
            $objInfoServicio = $arrayInfoServicio['registros'];
            foreach($objInfoServicio as $serv)
            {
                $strEstadoServicioAnterior      = "Cancel";
                $strObservacionServicioAnterior = "Cancelado por cambio de razon social por login";
                $strEstadoSpcAnterior           = "Cancelado";
                $strContinuaFlujoWyAp           = "NO";
                $strContinuaFlujoCAM            = "NO";
                $strEjecutaCreacionSolWyAp      = "NO";
                $strContinuaFlujoEdb            = "NO";
                $strEjecutaCreacionSolEdb       = "NO";
                $strEjecutaFlujoNormal          = "SI";
                $strEjecutaCreacionSolPlan      = "NO";
                $boolProductoNetlifeCam         = false;
                if(isset($arrayParams ['strPrefijoEmpresa']) && !empty($arrayParams ['strPrefijoEmpresa'])
                && ($arrayParams ['strPrefijoEmpresa'] == 'MD' || $arrayParams ['strPrefijoEmpresa'] == 'EN') && is_object($serv->getProductoId()))
                {
                    if($serv->getProductoId()->getNombreTecnico() === "WDB_Y_EDB")
                    {
                        $strContinuaFlujoWyAp           = "SI";
                        $arrayEstadoPermitidoCRSWdbyEdb = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                          ->getOne( 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD', 
                                                                                    '', 
                                                                                    '', 
                                                                                    '',
                                                                                    'CAMBIO_RAZON_SOCIAL',
                                                                                    'ESTADOS_SERVICIOS_X_PROD_FLUJO_PERSONALIZADO',
                                                                                    'WDB_Y_EDB',
                                                                                    $serv->getEstado(),
                                                                                    '',
                                                                                    $arrayParams['strCodEmpresa']);
                        if(isset($arrayEstadoPermitidoCRSWdbyEdb) && !empty($arrayEstadoPermitidoCRSWdbyEdb))
                        {
                            $strEjecutaFlujoNormal          = "NO";
                            $strEjecutaCreacionSolWyAp      = "SI";
                            $strEstadoServicioPorCRS        = $arrayEstadoPermitidoCRSWdbyEdb['valor5'];
                            $strEstadoServicioAnterior      = "Eliminado";
                            $strObservacionServicioAnterior = "Eliminado por cambio de razón social";
                            $strEstadoSpcAnterior           = "Eliminado";
                        }
                        else
                        {
                            $strEjecutaFlujoNormal      = "SI";
                            $strEstadoServicioPorCRS    = $serv->getEstado();
                        }
                    }
                    else if($serv->getProductoId()->getNombreTecnico() === "EXTENDER_DUAL_BAND")
                    {
                        $strContinuaFlujoEdb            = "SI";
                        $arrayEstadoPermitidoCRSEdb     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                          ->getOne( 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD', 
                                                                                    '', 
                                                                                    '', 
                                                                                    '',
                                                                                    'CAMBIO_RAZON_SOCIAL',
                                                                                    'ESTADOS_SERVICIOS_X_PROD_FLUJO_PERSONALIZADO',
                                                                                    'EXTENDER_DUAL_BAND',
                                                                                    $serv->getEstado(),
                                                                                    '',
                                                                                    $arrayParams['strCodEmpresa']);
                        if(isset($arrayEstadoPermitidoCRSEdb) && !empty($arrayEstadoPermitidoCRSEdb))
                        {
                            $strEjecutaFlujoNormal          = "NO";
                            $strEjecutaCreacionSolEdb       = "SI";
                            $strEstadoServicioPorCRS        = $arrayEstadoPermitidoCRSEdb['valor5'];
                            $strEstadoServicioAnterior      = "Eliminado";
                            $strObservacionServicioAnterior = "Eliminado por cambio de razón social";
                            $strEstadoSpcAnterior           = "Eliminado";
                        }
                        else
                        {
                            $strEjecutaFlujoNormal      = "SI";
                            $strEstadoServicioPorCRS    = $serv->getEstado();
                        }
                    }
                    else if($serv->getProductoId()->getNombreTecnico() === "ECDF")
                    {
                        $strEjecutaFlujoNormal      = "SI";
                        $strEstadoServicioPorCRS    = $serv->getEstado();
                        if($strTieneCorreoElectronico === "NO")
                        {
                          $strEstadoServicioPorCRS  = "Pendiente";
                        }
                        else
                        {
                            $objServProdCaractCorreo = $this->serviceServicioTecnico->getServicioProductoCaracteristica($serv,
                                                                                                'CORREO ELECTRONICO',
                                                                                                $serv->getProductoId());
                            if(is_object($objServProdCaractCorreo))
                            {
                                $strCorreoAnterior = $objServProdCaractCorreo->getValor();
                                if((!isset($strCorreoAnterior) || empty($strCorreoAnterior)))
                                {
                                    throw new \Exception("El cliente anterior no cuenta un correo eléctrónico de suscripción");
                                }
                                // Ejecutar WS del canal del futbol para actualizar el correo antiguo
                                $objInfoPersonaAsigna = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                ->findOneByLogin($arrayParams["strUsrCreacion"]);

                                if(!is_object($objInfoPersonaAsigna) 
                                || !in_array($objInfoPersonaAsigna->getEstado(), array('Activo','Pendiente','Modificado')))
                                 {
                                      throw new \Exception('El usuario de creación no existe en telcos o no se encuentra Activo.');
                                 }
                                $strUsuarioAsigna  = $objInfoPersonaAsigna->getNombres()." ".$objInfoPersonaAsigna->getApellidos();

                                $arrayParametrosECDF["email_old"]              = $strCorreoAnterior;
                                $arrayParametrosECDF["email_new"]              = $arrayParams["correoElectronico"];
                                $arrayParametrosECDF["usrCreacion"]            = $arrayParams["strUsrCreacion"];
                                $arrayParametrosECDF["ipCreacion"]             = $arrayParams["strClientIp"];
                                $arrayParametrosECDF['strLoginOrigen']         = $objInfoPuntoOrigen->getLogin();
                                $arrayParametrosECDF['strLoginDestino']        = $strLogin;
                                $arrayParametrosECDF['intIdEmpresa']           = $arrayParams["strCodEmpresa"];
                                $arrayParametrosECDF['strPrefijoEmpresa']      = $arrayParams['strPrefijoEmpresa'];
                                $arrayParametrosECDF['strUsuarioAsigna']       = $strUsuarioAsigna;
                                $arrayParametrosECDF['intIdPersonaEmpresaRol'] = $arrayParams['intIdPersonEmpRolEmpl'];
                                $arrayParametrosECDF['intPuntoId']             = $objInfoPuntoClonado->getId();
                                $arrayParametrosECDF['boolCrearTarea']         = true;
                                $arrayParametrosECDF['objServicio']            = $serv;
                                $arrayParametrosECDF['identificacionCliente']  = $arrayParams['identificacionCliente'];

                                $arrayResultado  = $this->serviceFoxPremium->actualizarCorreoECDF($arrayParametrosECDF);
                                if($arrayResultado['mensaje'] != 'ok')
                                {
                                      $strMensajeCorreoECDF     = "<br />".$arrayResultado['mensaje'];
                                      $strEstadoServicioPorCRS  = "Pendiente";
                                }
                            }
                            else 
                            {
                                throw new \Exception("El cliente anterior no cuenta un correo eléctrónico de suscripción");
                            }
                        }
                    }
                    else if($serv->getProductoId()->getNombreTecnico() === "NETLIFECAM OUTDOOR")
                    {   
                        $boolProductoNetlifeCam = true;
                        $strContinuaFlujoCAM = "SI";
                        $arrayEstadoPermitidoCRSCAM = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->getOne(   'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD',
                                                                                '','','',
                                                                                'CAMBIO_RAZON_SOCIAL',
                                                                                'ESTADOS_SERVICIOS_X_PROD_FLUJO_PERSONALIZADO',
                                                                                'NETLIFECAM OUTDOOR',
                                                                                $serv->getEstado(),'',
                                                                                $arrayParams['strCodEmpresa']);

                        if(isset($arrayEstadoPermitidoCRSCAM) && !empty($arrayEstadoPermitidoCRSCAM)) 
                        {
                            $strEjecutaFlujoNormal      = "NO";
                            $strEjecutaCreacionSolPlan  = "SI";
                            $strEstadoServicioPorCRS    = $arrayEstadoPermitidoCRSCAM['valor5'];
                            $strEstadoServicioAnterior      = "Eliminado";
                            $strObservacionServicioAnterior = "Eliminado por cambio de razón social";
                            $strEstadoSpcAnterior           = "Eliminado";
                        }
                        else
                        {
                            $strEjecutaFlujoNormal  = "SI";
                            $strEstadoServicioPorCRS = $serv->getEstado();
                        }
                    }
                    else
                    {
                        $strEjecutaFlujoNormal      = "SI";
                        $strEstadoServicioPorCRS    = $serv->getEstado();
                    }
                }
                else
                {
                    $strEjecutaFlujoNormal      = "SI";
                    $strEstadoServicioPorCRS    = $serv->getEstado();
                }
                
                if($serv->getEstado() == 'Activo'  || $strContinuaFlujoWyAp === "SI" || $strContinuaFlujoEdb === "SI"
                   || $strContinuaFlujoCAM === "SI")
                {
                    $objInfoServicioClonado = new InfoServicio();
                    $objInfoServicioClonado = clone $serv;
                    $objInfoServicioClonado->setFeCreacion($strFechaCreacion);
                    $objInfoServicioClonado->setUsrCreacion($arrayParams['strUsrCreacion']);
                    $objInfoServicioClonado->setPuntoId($objInfoPuntoClonado);
                    $objInfoServicioClonado->setPuntoFacturacionId($objInfoPuntoClonado);
                    $objInfoServicioClonado->setPorcentajeDescuento(0);
                    $objInfoServicioClonado->setValorDescuento(null);
                    $objInfoServicioClonado->setDescuentoUnitario(null);
                    $objInfoServicioClonado->setObservacion($serv->getId());
                    $objInfoServicioClonado->setEstado($strEstadoServicioPorCRS);
                    $this->emcom->persist($objInfoServicioClonado);
                    $this->emcom->flush();
                    
                    
                    
                   
                    
                    $intIdServicio= $objInfoServicioClonado->getId();
                    
                    $objInfoRecursosCab = $this->emcom->getRepository('schemaBundle:InfoServicioRecursoCab')
                                                            ->findBy(array('servicioId' => $serv->getId()));
                    
                    if(isset($objInfoRecursosCab) && !empty($objInfoRecursosCab))
                    {
                      foreach($objInfoRecursosCab as $objInfoRecursoCab)
                        {
                            $objInfoRecursoCab->setServicioId($intIdServicio);
                            $objInfoRecursoCab->setFecCreacion($strFechaCreacion);
                            $objInfoRecursoCab->setUsrCreacion($arrayParams['strUsrCreacion']);
                            $objInfoRecursoCab->setUsrUltMod($arrayParams['strUsrCreacion']);
                            $objInfoRecursoCab->setFecUltMod($strFechaCreacion);


                            $this->emcom->persist($objInfoRecursoCab);
                            $this->emcom->flush();
                        }   
                    }
                    
                    $objSolucionesDet  = $this->emcom->getRepository('schemaBundle:InfoSolucionDet')
                                                            ->findBy(array('servicioId' => $serv->getId()));
                    
                    $intServcioId = $objInfoServicioClonado->getId();
                    
                    if(isset($objSolucionesDet) && !empty($objSolucionesDet))
                    {
                        foreach($objSolucionesDet as $obSolucionDet)
                        {
                            $objSolucionDetClonado = new InfoSolucionDet();
                            $objSolucionDetClonado = clone $obSolucionDet;
                            $objSolucionDetClonado->setServicioId($intServcioId);
                            $objSolucionDetClonado->setFecCreacion($strFechaCreacion);
                            $objSolucionDetClonado->setUsrCreacion($arrayParams['strUsrCreacion']);
                            $objSolucionDetClonado->setUsrUltMod($arrayParams['strUsrCreacion']);
                            $objSolucionDetClonado->setFecUltMod($strFechaCreacion);

                            $this->emcom->persist($objSolucionDetClonado);
                        
                            $obSolucionDet->setEstado('Cancelado');
                            $this->emcom->persist($obSolucionDet);
                            
                            $this->emcom->flush();  
                        }
                       
                    }
                   
                    
                    //Clona los tipos documentos de cortesia aprobadas
                    //Agrega Còdigo para replicar Cortesías aprobadas
                    if( $arrayParams['strPrefijoEmpresa'] == 'TN')
                    {
                        $arrayActualCortesias = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                ->findCortesiasAprobadas($serv->getId());
                        
                        if(count( $arrayActualCortesias) > 0) 
                        {
                            foreach ($arrayActualCortesias as $cortesia) 
                            {
                                $objNewCortesia = new InfoDetalleSolicitud();
                                $objNewCortesia = clone $cortesia;
                                $objNewCortesia->setServicioId($objInfoServicioClonado);
                                $objNewCortesia->setFeCreacion(new \DateTime('now'));
                                $this->emcom->persist($objNewCortesia);
                                
                                //Anula la cortesìa del anterior servicio
                                $cortesia->setEstado('Anulado');
                                $this->emcom->persist($cortesia);
                                
                                
                                //agregar historial a la solicitud nueva
                                $strNewUsrLogin = $objInfoServicioClonado->getPuntoId()->getLogin();
                                $objHistorialNuevaCortesia = new InfoDetalleSolHist();
                                $objHistorialNuevaCortesia->setDetalleSolicitudId($objNewCortesia);
                                $objHistorialNuevaCortesia->setEstado($objNewCortesia->getEstado());
                                $objHistorialNuevaCortesia->setFeCreacion(new \DateTime('now'));
                                $objHistorialNuevaCortesia->setIpCreacion($arrayParams['strClientIp']);
                                $objHistorialNuevaCortesia->setObservacion("Generado por Cambio de Razón social a nombre de  $strNewUsrLogin");
                                $objHistorialNuevaCortesia->setUsrCreacion($arrayParams['strClientIp']);
                                $this->emcom->persist($objHistorialNuevaCortesia);
                                

                                //Agrega al historial la anterior cortesia cancelada
                                $objHistoriaViejaCortesia = new InfoDetalleSolHist();
                                $objHistoriaViejaCortesia->setDetalleSolicitudId($cortesia);
                                $objHistoriaViejaCortesia->setEstado($cortesia->getEstado());
                                $objHistoriaViejaCortesia->setFeCreacion(new \DateTime('now'));
                                $objHistoriaViejaCortesia->setIpCreacion($arrayParams['strClientIp']);
                                $objHistoriaViejaCortesia->setObservacion("Se genera por Cambio de Razón social a nombre de  $strNewUsrLogin");
                                $objHistoriaViejaCortesia->setUsrCreacion($arrayParams['strUsrCreacion']);
                                $this->emcom->persist($objHistoriaViejaCortesia);


                                
                                $this->emcom->flush();
                            }
                        }                    
                               
                    }

                    
                    if($strEjecutaFlujoNormal === "SI")
                    {
                        $arrayParametros = array("strAplicaCiclosFac" => $strAplicaCiclosFac,
                                                 "objServicioOrigen"  => $serv,
                                                 "objServicioDestino" => $objInfoServicioClonado,
                                                 "objAdmiCicloOrigen" => $objAdmiCicloOrigen,
                                                 "strUsrCreacion"     => $arrayParams['strUsrCreacion'],
                                                 "strIpCreacion"      => $arrayParams['strClientIp']);
                        $arrayRespuesta = $this->serviceInfoServicio
                                          ->crearServicioCaracteristicaPorCRS($arrayParametros);
                        if ($arrayRespuesta["strEstado"] != "OK")
                        {
                            throw new \Exception("Error al procesar el Cambio de Razón Social: " . $arrayRespuesta["strMensaje"]);
                        }
                    }

                    /**
                     * Bloque que genera un historial en el servicio con la fecha con la cual se realiza el cálculo de meses restantes
                     */
                    $intFrecuenciaProducto = $serv->getFrecuenciaProducto() ? $serv->getFrecuenciaProducto() : 0;

                    if( isset($arrayParams['strPrefijoEmpresa']) && $arrayParams['strPrefijoEmpresa'] == 'TN' && $intFrecuenciaProducto > 1 
                        && is_object($objInfoServicioClonado) )
                    {
                        $intIdServicioAntiguo = $serv->getId() ? $serv->getId() : 0;
                        $intMesesRestantes    = $serv->getMesesRestantes() ? $serv->getMesesRestantes() : 0;

                        $arrayParametrosGenerarHistorialReinicioConteo = array('intIdServicioAntiguo' => $intIdServicioAntiguo,
                                                                               'objServicioNuevo'     => $objInfoServicioClonado,
                                                                               'strPrefijoEmpresa'    => $arrayParams['strPrefijoEmpresa'],
                                                                               'strUsrCreacion'       => $arrayParams['strUsrCreacion'],
                                                                               'intMesesRestantes'    => $intMesesRestantes);

                        $this->serviceInfoServicio->generarHistorialReinicioConteo($arrayParametrosGenerarHistorialReinicioConteo);
                    }
                    
                    if($strEjecutaFlujoNormal === "SI")
                    {
                        $arrayParametrosFechaAct = array('emFinanciero'  => $this->emFinanciero,
                                                         'intIdServicio' => $serv->getId()
                        );
                        // Registro de historial con feActivacion de servicio origen
                        $objAdmiParametroCab = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                               ->findOneBy(array('nombreParametro' => 'CAMBIO FORMA PAGO', 
                                                                                 'estado'          => 'Activo'));
                        if(is_object($objAdmiParametroCab))
                        {                 
                            $objAdmiParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                   ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                                     'descripcion' => 'FECHA ACTIVACION ORIGEN',
                                                                                     'valor1'      => 'CAMBIO RAZON SOCIAL',
                                                                                     'empresaCod'  => $arrayParams["strCodEmpresa"],
                                                                                     'estado'      => 'Activo'));
                            $strFechaActivacionOrigen = $this->emcom->getRepository('schemaBundle:InfoServicioTecnico')
                                                                    ->getFechaActivacionServicioOrigen($arrayParametrosFechaAct); 

                            if(is_object($objAdmiParametroDet))
                            {
                                $strAccionHistOrigen = $objAdmiParametroDet->getValor2();

                                if(isset($strAccionHistOrigen) && !empty($strFechaActivacionOrigen))
                                {
                                    // Guardo Historial con fecha y observacion 'Se Confirmo el Servicio' en los servicios origenes del Cambio de 
                                    // Razon Social
                                    $objServicioHistorial = new InfoServicioHistorial();
                                    $objServicioHistorial->setServicioId($objInfoServicioClonado);
                                    $objServicioHistorial->setFeCreacion(new \DateTime($strFechaActivacionOrigen));
                                    $objServicioHistorial->setUsrCreacion($arrayParams['strUsrCreacion']);
                                    $objServicioHistorial->setEstado($objInfoServicioClonado->getEstado());
  
                                    if ($boolProductoNetlifeCam)
                                    {   
                                        $objAdmiParametroCab = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                        ->findOneBy(array('nombreParametro' => 'PROYECTO NETLIFECAM', 
                                                                        'estado'          => 'Activo'));
                                        if(is_object($objAdmiParametroCab))
                                        {
                                            $objAdmiParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                            'descripcion' => 'PARAMETROS NETLIFECAM OUTDOOR',
                                                                            'valor1'      => 'CAMBIO RAZON SOCIAL',
                                                                            'empresaCod'  => $arrayParams["strCodEmpresa"],
                                                                            'estado'      => 'Activo'));
                                            $strAccionHistOrigen = $objAdmiParametroDet->getValor2();
                                            $strObserHistOrigen = $objAdmiParametroDet->getValor3();   
                                        }
                                        $objServicioHistorial->setAccion($strAccionHistOrigen);
                                        $objServicioHistorial->setObservacion($strObserHistOrigen); 
                                    }else
                                    {    
                                        $strObserHistOrigen = 'Fecha inicial de servicio por Cambio de razón social.';
                                        $objServicioHistorial->setAccion($strAccionHistOrigen);
                                        $objServicioHistorial->setObservacion($strObserHistOrigen);
                                    }    
                                    $this->emcom->persist($objServicioHistorial);
                                }                          
                            }
                        }
                        // Obtengo la fecha de confirmacion del servicio del cliente origen del cambio de razon social
                        $strFechaActivacion = $this->emcom->getRepository('schemaBundle:InfoServicioTecnico')
                                                          ->getFechaActivacionServicio($arrayParametrosFechaAct);

                        if(isset($strFechaActivacion) && !empty($strFechaActivacion))
                        {
                            // Guardo Historial con fecha y observacion 'Se Confirmo el Servicio' en los servicios origenes del Cambio de 
                            // Razon Social
                            $objServicioHistorial = new InfoServicioHistorial();
                            $objServicioHistorial->setServicioId($objInfoServicioClonado);
                            $objServicioHistorial->setFeCreacion(new \DateTime($strFechaActivacion));
                            $objServicioHistorial->setUsrCreacion($arrayParams['strUsrCreacion']);
                            $objServicioHistorial->setEstado($objInfoServicioClonado->getEstado());
                            $objServicioHistorial->setAccion('confirmarServicio');
                            $objServicioHistorial->setObservacion('Se Confirmó el Servicio por Cambio de razón social por login');
                            $this->emcom->persist($objServicioHistorial);
                        }
                    }

                    $objInfoServicioHistorial = new InfoServicioHistorial();
                    $objInfoServicioHistorial->setServicioId($objInfoServicioClonado);
                    $objInfoServicioHistorial->setFeCreacion($strFechaCreacion);
                    $objInfoServicioHistorial->setUsrCreacion($arrayParams['strUsrCreacion']);
                    $objInfoServicioHistorial->setEstado($objInfoServicioClonado->getEstado());
                    $objInfoServicioHistorial->setObservacion('Creado por Cambio de razon social por login, Login Origen:' .
                                                               $objInfoPuntoOrigen->getLogin());
                    $this->emcom->persist($objInfoServicioHistorial);
                    $this->emcom->flush(); 

                    if ($boolProductoNetlifeCam && $strEjecutaFlujoNormal === "NO")
                    {   
                        $objAdmiParametroCab = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                        ->findOneBy(array('nombreParametro' => 'PROYECTO NETLIFECAM', 'estado' => 'Activo'));
                        if(is_object($objAdmiParametroCab))
                        {
                            $objAdmiParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                            'descripcion' => 'PARAMETROS NETLIFECAM OUTDOOR',
                                                            'valor1'      => 'CAMBIO RAZON SOCIAL',
                                                            'empresaCod'  => $arrayParams["strCodEmpresa"],
                                                            'estado'      => 'Activo'));
                            $strAccionHistOrigen = $objAdmiParametroDet->getValor2();
                            $strObserHistOrigen = $objAdmiParametroDet->getValor3();   
                        }

                        $arrayParametrosFechaAct = array('emFinanciero'  => $this->emFinanciero,
                                                         'intIdServicio' => $serv->getId());
                        $strFechaActivacionOrigen = $this->emcom->getRepository('schemaBundle:InfoServicioTecnico')
                                                  ->getFechaActivacionServicioOrigen($arrayParametrosFechaAct);
                        $objInfoServicioHistorial = new InfoServicioHistorial();
                        $objInfoServicioHistorial->setServicioId($objInfoServicioClonado);
                        $objInfoServicioHistorial->setFeCreacion(new \DateTime($strFechaActivacionOrigen));
                        $objInfoServicioHistorial->setUsrCreacion($arrayParams['strUsrCreacion']);
                        $objInfoServicioHistorial->setEstado($objInfoServicioClonado->getEstado()); 
                        $objInfoServicioHistorial->setAccion($strAccionHistOrigen);
                        $objInfoServicioHistorial->setObservacion($strObserHistOrigen); 
                        $this->emcom->persist($objInfoServicioHistorial);
                        $this->emcom->flush();
                    }   
                    

                    // Validamos si el nuevo servicio posee un plany guardamos para verificar si es con promociones
                    if($arrayParams['strPrefijoEmpresa'] == 'MD'&& is_object($objInfoServicioClonado->getPlanId()))
                    {
                        array_push($arrayServiciosPromociones, array('destino' => $objInfoServicioClonado->getId(),
                                                                     'origen' => $serv->getId()));
                    }

                    // Funcion que verifica si existen servicios extremos para un servicio concentrador, actualiza a todos los enlaces extremos
                    // existentes (servicios con caracteristica ENLACE_DATOS) el nuevo servicio Concentrador generado en el cambio de razon Social
                    $arrayParametroEnlaceDatos = array ('strFechaCreacion'       => $strFechaCreacion,
                                                        'strUsrCreacion'         => $arrayParams['strUsrCreacion'],
                                                        'strIpCreacion'          => $arrayParams['strClientIp'],                                                                                                              
                                                        'objInfoServicioOrigen'  => $serv,
                                                        'objInfoServicioDestino' => $objInfoServicioClonado,
                                                        'strTipoCaracteristica'  => 'ENLACE_DATOS'); 
                    
                    $strMsjActualizaConcentradorEnExtremos = $this->actualizaConcentradorEnExtremos($arrayParametroEnlaceDatos);
                    if($strMsjActualizaConcentradorEnExtremos)
                    {
                        throw new \Exception($strMsjActualizaConcentradorEnExtremos);
                    }
                    
                    // Funcion que verifica si existen servicios BACKUP para un servicio PRINCIPAL, actualiza a todos los enlaces BACKUPS
                    // existentes (servicios con caracteristica ES_BACKUP) el nuevo ID servicio PRINCIPAL generado en el cambio de razon Social
                    $arrayParametroEnlacesBackup = array ('strFechaCreacion'       => $strFechaCreacion,
                                                          'strUsrCreacion'         => $arrayParams['strUsrCreacion'],
                                                          'strIpCreacion'          => $arrayParams['strClientIp'],                                                                                                              
                                                          'objInfoServicioOrigen'  => $serv,
                                                          'objInfoServicioDestino' => $objInfoServicioClonado,
                                                          'strTipoCaracteristica'  => 'ES_BACKUP'); 
                    
                    $strMsjActualizaPrincipalEnBackups = $this->actualizaConcentradorEnExtremos($arrayParametroEnlacesBackup);
                    if($strMsjActualizaPrincipalEnBackups)
                    {
                        throw new \Exception($strMsjActualizaPrincipalEnBackups);
                    }
                    // Se obtienen el producto IPMP
                    $objProductoIPMP = $this->emcom->getRepository('schemaBundle:AdmiProducto')
                                                        ->findOneBy(array("descripcionProducto"
                                                                            => 'I. PROTEGIDO MULTI PAID',
                                                                                "estado" => "Activo"));

                    $arrayProCaract  = array( "objServicio"       => $serv,
                                                "objProducto"       => $objProductoIPMP,
                                                "strUsrCreacion"    => $arrayParams['strUsrCreacion'],
                                                "strCaracteristica" => "ANTIVIRUS");

                    $strRespuestaCaract = $this->serviceLicenciasKaspersky->obtenerValorServicioProductoCaracteristica($arrayProCaract);
                    
                    
                    if(is_object($strRespuestaCaract['objServicioProdCaract']) &&
                        $strRespuestaCaract['objServicioProdCaract']->getValor() == "KASPERSKY")
                    {
                        $objInfoServicioProdCaract = $this->emcom->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                    ->findBy(array("servicioId" => $serv->getId()));

                        $arrayEstadosCaract = array('Activo','Pendiente','Suspendido');
                    } 
                    else
                    {
                        $objInfoServicioProdCaract = $this->emcom->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                        ->findBy(array("servicioId" => $serv->getId(),
                                                                        "estado"     => 'Activo'));

                        
                        $arrayEstadosCaract = array('Activo');
                    }                                     
                    

                    //Seteamos la caracteristica a buscar
                    $strCaractProducto='NETLIFECLOUD';
                    
                    //Seteamos los parametros para enviar a la función getInfoCaractProducto
                    $arrayParamProdCaract = array(
                                                    'intServicioId'        => $serv->getId(),
                                                    'strCaracteristica'    => $strCaractProducto
                                                );
    
                    // Se obtienen las características del servicio asociado
                    $objCaractProducto = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                    ->getInfoCaractProducto($arrayParamProdCaract);

                    if(is_object($strRespuestaCaract['objServicioProdCaract']) && 
                        $strRespuestaCaract['objServicioProdCaract']->getValor() == "KASPERSKY")
                    {
                        $arrayProCaractAntivirus   = array( "objServicio"       => $serv,
                                                            "objProducto"       => $objProductoIPMP, 
                                                            "strUsrCreacion"    => $arrayParams['strUsrCreacion']);


                        $arrayProCaractAntivirus["strCaracteristica"] = "SUSCRIBER_ID";
                        $arrayRespuestaCaract = $this->serviceLicenciasKaspersky
                                            ->obtenerValorServicioProductoCaracteristica($arrayProCaractAntivirus);

                        if(is_object($arrayRespuestaCaract['objServicioProdCaract']))
                        {
                            $intSuscriberId  = $arrayRespuestaCaract["objServicioProdCaract"]->getValor();
                        }
                        else if($arrayRespuestaCaract["status"] == 'ERROR')
                        {  
                            throw new \Exception('No se obtuvo suscriber ID');
                        }
                        
                    
                        $arrayProCaractAntivirus["strCaracteristica"] = 'CORREO ELECTRONICO';
                        $arrayRespuestaGetSpc  = $this->serviceLicenciasKaspersky
                                                ->obtenerValorServicioProductoCaracteristica($arrayProCaractAntivirus);
                        
                        if(is_object($arrayRespuestaGetSpc['objServicioProdCaract']))
                        {
                            $strCorreoSuscripcion  = $arrayRespuestaGetSpc["objServicioProdCaract"]->getValor();
                        }
                        else if($arrayRespuestaGetSpc["status"] == 'ERROR')
                        {  
                            throw new \Exception('No se obtuvo correo electrónico del cliente');
                        }

                        $strMsjErrorAdicHtml        = "No se pudo Realizar la cancelacion del suscriberID";
                    
                        $arrayParamsLicencias       = array("strProceso"                => "CANCELACION_ANTIVIRUS",
                                                            "strEscenario"              => "CANCELACION_POR_CAMBIO_RAZON_SOCIAL_LOGIN_EXIST",
                                                            "objServicio"               => $serv,
                                                            "objPunto"                  => $serv->getPuntoId(),
                                                            "strCodEmpresa"             => $arrayParams['strCodEmpresa'],
                                                            "objProductoIPMP"           => $objProductoIPMP,
                                                            "strUsrCreacion"            => $arrayParams['strUsrCreacion'],
                                                            "strIpCreacion"             => $arrayParametros['strIpCreacion'],
                                                            "strEstadoServicioInicial"  => $serv->getEstado(),
                                                            "intSuscriberId"            => $intSuscriberId,
                                                            "strCorreoSuscripcion"      => $strCorreoSuscripcion,
                                                            "strMsjErrorAdicHtml"       => $strMsjErrorAdicHtml
                                                            );

                        $arrayRespuestaGestionLicencias = $this->serviceLicenciasKaspersky->gestionarLicencias($arrayParamsLicencias);
                        $strStatusGestionLicencias      = $arrayRespuestaGestionLicencias["status"];
                        $strStatusGestionLicencias      = "OK";
                        $strMensajeGestionLicencias     = $arrayRespuestaGestionLicencias["mensaje"];
                        $arrayRespuestaWs               = $arrayRespuestaGestionLicencias["arrayRespuestaWs"];

                        if($strStatusGestionLicencias === "ERROR")
                        {
                            $strMostrarError = "SI";
                            throw new \Exception('Fallo del envió de solicitud Cancelación de licencia kaspersky');
                        }
                    }

                    $strDescripcionProducto = "";
                    $objProductoServicio    = null;
                    $arrayProducto          = array();
                    //NOMBRE TECNICO DE PRODUCTOS DE TVS PERMITIDOS
                    $arrayNombreTecnicoPermitido = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('NOMBRE_TECNICO_PRODUCTOSTV_CRS',//nombre parametro cab
                                                        'COMERCIAL', //modulo cab
                                                        'OBTENER_NOMBRE_PRODUCTO',//proceso cab
                                                        'FLUJO_CRS', //descripcion det
                                                        '','','','','',
                                                        '18'); //empresa
                    foreach($arrayNombreTecnicoPermitido as $arrayNombreTecnico)
                    {
                        $arrayProdTvNombreTecnico[]   =   $arrayNombreTecnico['valor1'];
                    }
                    if(is_object($objInfoServicioClonado->getProductoId()))
                    {
                        $strDescripcionProducto = $objInfoServicioClonado->getProductoId()->getDescripcionProducto();
                        $objProductoServicio    = $objInfoServicioClonado->getProductoId();
                        $strNombreTecnicoProdTv = $objInfoServicioClonado->getProductoId()->getNombreTecnico();
                        if(in_array($strNombreTecnicoProdTv,$arrayProdTvNombreTecnico ))
                        {
                            $arrayProducto  = $this->serviceFoxPremium->determinarProducto(
                                array('strNombreTecnico'=>$strNombreTecnicoProdTv));
                        }
                    }
                    else if(is_object($objInfoServicioClonado->getPlanId()))
                    {
                        $objPlanDet = $this->emcom->getRepository('schemaBundle:InfoPlanDet')
                                                  ->findBy(array('planId' => $objInfoServicioClonado->getPlanId(),
                                                                 'estado' => "Activo"));
                        if(($objPlanDet))
                        {
                            foreach($objPlanDet as $idxPlanDet)
                            {
                                $objProducto = $this->emcom->getRepository('schemaBundle:AdmiProducto')
                                                           ->find($idxPlanDet->getProductoId());

                                if(is_object($objProducto) && in_array($objProducto->getNombreTecnico(), $arrayProdTvNombreTecnico))
                                {
                                    $objProductoServicio    = $objProducto;
                                    $strDescripcionProducto = $objProducto->getDescripcionProducto();
                                    $arrayProducto          = $this->serviceFoxPremium->determinarProducto(
                                                                array('strNombreTecnico'=>$objProducto->getNombreTecnico()));
                                    break;
                                }
                            }
                        }
                    }

                    $strBanderaCredenciales    = "N";
                    $strBanderaNotifica        = "N";
                    //Se obtiene el nombre de las caracteristicas: usuario y password para los productos configurados
                    if(isset($arrayProducto) && !empty($arrayProducto))
                    {
                        //se agrega Parametro para validar si se generan o no credenciales de productos
                        $arrayNombreTecnicoGeneraCredenciales = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                            ->get('NO_GENERA_CREDENCIALES_CRS',//nombre parametro cab
                                                                                'COMERCIAL', //modulo cab
                                                                                'NO_GENERA_CREDENCIALES',//proceso cab
                                                                                'PRODUCTO_TV', //descripcion det
                                                                                '','','','','',
                                                                                '18'); //empresa
                        foreach($arrayNombreTecnicoGeneraCredenciales as $arrayNTGeneraCredenciales)
                        {
                            $arrayProdTvNombreTecGeneraCred[]   =   $arrayNTGeneraCredenciales['valor1'];
                        }
                        if(in_array($arrayProducto["strNombreTecnico"],$arrayProdTvNombreTecGeneraCred))
                        {
                            $strBanderaCredenciales    = "N";
                            $strBanderaNotifica        = "S";
                            $strNombreTecnico          = $arrayProducto["strNombreTecnico"];
                            $strPlantillaCorreo        = $arrayProducto["strCodPlantNuevo"];
                            $strAsuntoNuevoServicio    = $arrayProducto['strAsuntoNuevo'];
                            $strPlantillaSms           = $arrayProducto['strSmsNuevo'];

                            // EN CASO DE QUE EXISTA EL PRODUCTO Y SI TENGA CORREO
                            if($arrayProducto["strNombreTecnico"] === "ECDF")
                            {
                                $strCaracteristicaUsuario  = $arrayProducto["strUser"];
                                $strCaracteristicaPassword = $arrayProducto["strPass"];

                                if($strTieneCorreoElectronico === "SI"
                                && $objInfoServicioClonado->getEstado() === "Activo")
                                {
                                  $strBanderaCredenciales    = "S";
                                }
                                else
                                {
                                  $strBanderaNotifica        = "N";
                                }
                            }
                        }
                        else
                        {
                            $strCaracteristicaUsuario  = $arrayProducto["strUser"];
                            $strCaracteristicaPassword = $arrayProducto["strPass"];
                            $strNombreTecnico          = $arrayProducto["strNombreTecnico"];
                            $strPlantillaCorreo        = $arrayProducto["strCodPlantNuevo"];
                            $strAsuntoNuevoServicio    = $arrayProducto['strAsuntoNuevo'];
                            $strPlantillaSms           = $arrayProducto['strSmsNuevo'];
                            $strBanderaCredenciales    = "S";
                            $strBanderaNotifica        = "S";
                        }
                    }

                    // Si la caracteristica del servicio es diferente de Office, se clonan sus caracteristicas al nuevo punto.            
                    if($objCaractProducto['caracteristica']!='NETLIFECLOUD')
                    {
                        //cancelacion de Caracteristicas del servicio
                        foreach($objInfoServicioProdCaract as $servpc)
                        {
                            // aqui nuevo código
                            $strClonarCaracteristica   = "S";
                            $strSuscriberIdStatus = '';
                            $intProductoCaracteristica = $servpc->getProductoCaracterisiticaId();
                            $arrayEstadosCaract = array('Activo','Pendiente');
                            if(in_array($servpc->getEstado(),$arrayEstadosCaract))
                            {
                                if(!empty($intProductoCaracteristica))
                                {
                                    $objAdmiProductoCaracteristica = 
                                    $this->emcom->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                ->find($intProductoCaracteristica);

                                    if(is_object($objAdmiProductoCaracteristica))
                                    {
                                        $objAdmiCaracteristica = $objAdmiProductoCaracteristica->getCaracteristicaId();

                                        if(is_object($objAdmiCaracteristica) &&
                                            ($objAdmiCaracteristica->getDescripcionCaracteristica() == $strCaracteristicaUsuario ||
                                                $objAdmiCaracteristica->getDescripcionCaracteristica() == $strCaracteristicaPassword))
                                        {
                                            $strClonarCaracteristica = "N";
                                        }
                                        else if($objAdmiCaracteristica->getDescripcionCaracteristica() === "SUSCRIBER_ID")
                                        {
                                            if(is_object($serv) && $serv->getPlanId() !== null)
                                            {
                                                $strEsenario = "ACTIVACION_PROD_EN_PLAN";
                                            }
                                            else
                                            {
                                                $strEsenario = "ACTIVACION_PROD_ADICIONAL";
                                            }
                                            
                                            $strMsjErrorAdicHtml            = "No se pudo Realizar la activacion suscriberID";

                                            $arrayParamsLicencias           = array("strProceso"                => "ACTIVACION_ANTIVIRUS",
                                                                                    "boolEsCRS"                 => true,
                                                                                    "strEscenario"              => $strEsenario,
                                                                                    "objServicio"               => $objInfoServicioClonado,
                                                                                    "objPunto"               => $objInfoServicioClonado->getPuntoId(),
                                                                                    "strCodEmpresa"             => $arrayParams['strCodEmpresa'],
                                                                                    "objProductoIPMP"           => $objProductoIPMP,
                                                                                    "strUsrCreacion"            => $arrayParams['strUsrCreacion'],
                                                                                    "strIpCreacion"             => $arrayParams['strIpCreacion'],
                                                                                    "strEstadoServicioInicial" =>$objInfoServicioClonado->getEstado(),
                                                                                    "intIdOficina"              => $arrayParams['intIdOficina'],
                                                                                    "strMsjErrorAdicHtml"       => $strMsjErrorAdicHtml);

                                            $arrayRespuestaGestionLicencias = 
                                            $this->serviceLicenciasKaspersky->gestionarLicencias($arrayParamsLicencias);
                                            $strStatusGestionLicencias      = $arrayRespuestaGestionLicencias["status"];
                                            $strStatusGestionLicencias      = "OK";
                                            $strMensajeGestionLicencias     = $arrayRespuestaGestionLicencias["mensaje"];
                                            $arrayRespuestaWs               = $arrayRespuestaGestionLicencias["arrayRespuestaWs"];
                                            
                                            if($strStatusGestionLicencias === "ERROR")
                                            {
                                                $strMostrarError = "SI";
                                                throw new \Exception($strMensajeGestionLicencias);
                                            }
                                            else if(isset($arrayRespuestaWs) && !empty($arrayRespuestaWs)
                                            && $arrayRespuestaWs["status"] === "OK" && $objInfoServicioClonado->getPlanId() !== null)
                                            {
                                                $strClonarCaracteristica = "N";
                                                $strSuscriberId = $arrayRespuestaWs["SuscriberId"];
                                         
                                                //Guardar informacion de la característica del producto
                                                $objServicioProdCaract = new InfoServicioProdCaract();
                                                $objServicioProdCaract->setServicioId($objInfoServicioClonado->getId());
                                                $objServicioProdCaract->setProductoCaracterisiticaId($intProductoCaracteristica);
                                                $objServicioProdCaract->setValor($strSuscriberId);
                                                $objServicioProdCaract->setEstado('Pendiente');
                                                $objServicioProdCaract->setUsrCreacion($arrayParams['strUsrCreacion']);
                                                $objServicioProdCaract->setFeCreacion(new \DateTime('now'));
                                                $this->emcom->persist($objServicioProdCaract);
                                                $this->emcom->flush();

                                            }
                                            else
                                            {
                                                $strClonarCaracteristica = "N";
                                            }
                                        } 
                                        else if($objAdmiCaracteristica->getDescripcionCaracteristica() == "CORREO ELECTRONICO")
                                        {
                                           //ReGISTRA LAS FORMAS DE CONTACTO DEL CLIENTE 
                                           $strCorreoSuscripcion = $servpc->getValor();
                                           if($arrayProducto["strNombreTecnico"] === "ECDF" 
                                           && $strTieneCorreoElectronico === "SI"
                                           && (!isset($strCorreoSuscripcion) || empty($strCorreoSuscripcion)))
                                           {
                                              throw new \Exception("El cliente anterior no cuenta un correo eléctrónico de suscripción");
                                           }

                                           if($arrayProducto["strNombreTecnico"] === "ECDF"
                                           && $strTieneCorreoElectronico === "SI")
                                           {
                                              if(!isset($arrayParams["correoElectronico"]) 
                                              || empty($arrayParams["correoElectronico"]))
                                              {
                                                throw new \Exception("Debes ingresar un correo válido");
                                              }

                                              $objServicioProdCaract = new InfoServicioProdCaract();
                                              $objServicioProdCaract = clone $servpc;
                                              $objServicioProdCaract->setServicioId($objInfoServicioClonado->getId());
                                              $objServicioProdCaract->setFeCreacion(new \DateTime('now'));
                                              $objServicioProdCaract->setUsrCreacion($arrayParams['strUsrCreacion']);
                                              $objServicioProdCaract->setValor($arrayParams["correoElectronico"]);
                                              $this->emcom->persist($objServicioProdCaract);
                                              $this->emcom->flush();

                                              if($strEstadoServicioPorCRS == "Activo")
                                              {
                                                  $servpc->setEstado("Eliminado");
                                              }
                                              else 
                                              {
                                                  $servpc->setEstado("Cancel");
                                              }
                                           }
                                           else if($arrayProducto["strNombreTecnico"] === "ECDF"
                                           && $strTieneCorreoElectronico === "NO")
                                           {
                                              $servpc->setEstado("Cancel");
                                           }
                                           $strNuevoCorreoElectronico  = $strCorreoSuscripcion;

                                           if(!empty($strNuevoCorreoElectronico))
                                           {
                                            $servpc->setValor($strNuevoCorreoElectronico);
                                            $strClonarCaracteristica = "S";
                                           }
                                        }
                                        else if($objAdmiCaracteristica->getDescripcionCaracteristica() == "CODIGO_PRODUCTO")
                                        {
                                            $strClonarCaracteristica = "N";
                                        }
                                    }
                                }
                                
                                if($strClonarCaracteristica == "S")
                                {
                                    $objInfoServicioProdCaractClonado = new InfoServicioProdCaract();
                                    $objInfoServicioProdCaractClonado = clone $servpc;
                                    $objInfoServicioProdCaractClonado->setServicioId($objInfoServicioClonado->getId());
                                    $objInfoServicioProdCaractClonado->setFeCreacion($strFechaCreacion);
                                    $objInfoServicioProdCaractClonado->setUsrCreacion($arrayParams['strUsrCreacion']);
                                    $this->emcom->persist($objInfoServicioProdCaractClonado);
                                    $this->emcom->flush();
                                }

                                $strClonarCaracteristica = "S";
                            }       
                        
                            // paso el valor de la caracteristica 'MIGRADO_FOX' a S, ya que el servicio fue clonado o migrado 
                            // por el cambio de razon social
                            $arrayParametrosFox = array();
                            $objRespuestaValidacion = null;
                            $arrayParametrosFox["strDescripcionCaracteristica"] = "MIGRADO_FOX";
                            $arrayParametrosFox["strNombreTecnico"]             = "FOXPREMIUM";
                            $arrayParametrosFox["intIdServicio"]                = $servpc->getId();
                            $arrayParametrosFox["intIdServProdCaract"]          = $servpc->getId();
                            $arrayParametrosFox["strEstadoSpc"]                 = 'Activo';

                            $objRespuestaServProdCarac = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                                    ->getCaracteristicaServicio($arrayParametrosFox);

                            if (is_object($objRespuestaServProdCarac))
                            {
                                $servpc->setValor('S');
                            }
                            // Se procede a Cancelar las caracteristicas de los Servicios Origen del Cambio de Razon Social
                            $servpc->setEstado($strEstadoSpcAnterior);
                            $servpc->setFeUltMod($strFechaCreacion);
                            $servpc->setUsrUltMod($arrayParams['strUsrCreacion']);
                            $this->emcom->persist($servpc);
                            $this->emcom->flush();
                            
                        } 
                    }
                    else
                    {
                        // Se crean nuevas caracteristicas para el servicio si su caracteristica anterior es Office    
                        $strAccion = 'cambioRazonSocial';
                        $arrayParametrosWs = array(
                                                  'strPrefijoEmpresa'    => $arrayParams['strPrefijoEmpresa'],
                                                  'strEmpresaCod'        => $arrayParams['strCodEmpresa'],
                                                  'strUsuarioCreacion'   => $arrayParams['strUsrCreacion'],
                                                  'strIp'                => $arrayParams['strClientIp'], 
                                                  'intServicioId'        => $objInfoServicioClonado->getId(),
                                                  'strAccion'            => $strAccion
                                                );

                        $arrayRespuestaLicencia=$this->serviceLicenciasOffice365->renovarLicenciaOffice365($arrayParametrosWs);

                        if($arrayRespuestaLicencia["status"] == 'ERROR')
                        {  
                            throw new \Exception($arrayRespuestaLicencia["mensaje"]);
                        }
                            
                         foreach($objInfoServicioProdCaract as $servpc)
                        {
                            // Se procede a Cancelar las caracteristicas de los Servicios Origen del Cambio de Razon Social
                            $servpc->setEstado('Cancelado');
                            $servpc->setFeUltMod($strFechaCreacion);
                            $servpc->setUsrUltMod($arrayParams['strUsrCreacion']);
                            $this->emcom->persist($servpc);
                            $this->emcom->flush();                                
                        }
                    }

                    if($strBanderaCredenciales === "S" && is_object($objInfoPersona))
                    {
                        //Para servicios Paramount y Noggin se generan nuevo usuario y contrasenia
                        $arrayParametrosGenerarUsuario["intIdPersona"]     = $objInfoPersona->getId();
                        $arrayParametrosGenerarUsuario["strCaracUsuario"]  = $strCaracteristicaUsuario;
                        $arrayParametrosGenerarUsuario["strNombreTecnico"] = $strNombreTecnico;

                        $strUsuario  = $this->serviceFoxPremium->generaUsuarioFox($arrayParametrosGenerarUsuario);

                        if(empty($strUsuario))
                        {
                            throw new \Exception("No se pudo obtener Usuario para el servicio ".$strDescripcionProducto);
                        }

                        $strPassword           = $this->serviceFoxPremium->generaContraseniaFox();
                        $strPasswordEncriptado = $this->serviceCrypt->encriptar($strPassword);
                        if(empty($strPassword))
                        {
                            throw new \Exception("No se pudo generar Password para el servicio ".$strDescripcionProducto);
                        }

                        //Insertar nuevas caracteristicas: usuario y password
                        $this->serviceServicioTecnico->ingresarServicioProductoCaracteristica($objInfoServicioClonado,
                                                                                              $objProductoServicio,
                                                                                              $strCaracteristicaUsuario,
                                                                                              $strUsuario,
                                                                                              $arrayParams['strUsrCreacion']);

                        $this->serviceServicioTecnico->ingresarServicioProductoCaracteristica($objInfoServicioClonado,
                                                                                              $objProductoServicio,
                                                                                              $strCaracteristicaPassword,
                                                                                              $strPasswordEncriptado,
                                                                                              $arrayParams['strUsrCreacion']);
                        //Cambiar estado ELiminado de la caracteristica del correo del producto
                        $arrayNombreTecnicoEliminaCaracCorreo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->get('NOMBRE_PRODUCTOSTV_ELIMINA_CARAC_CORREO',//nombre parametro cab
                                                                        'COMERCIAL', //modulo cab
                                                                        'ELIMINA_CARAC_CORREO',//proceso cab
                                                                        'CRS_ELIMINA_CARAC_CORREO', //descripcion det
                                                                        '','','','','',
                                                                        '18'); //empresa
                        foreach($arrayNombreTecnicoEliminaCaracCorreo as $arrayNombreTecnicoProd)
                        {
                            $arrayProdTvPermitido[]   =   $arrayNombreTecnicoProd['valor1'];
                        }
                        if(in_array($strNombreTecnico,$arrayProdTvPermitido))
                        {
                            $arrayParameter =   array(
                                                        "strNombreTecnico"  =>  $strNombreTecnico,
                                                        "strUsrCreacion"    =>  $arrayParams['strUsrCreacion'],
                                                        "intIdServicio"     =>  $objInfoServicioClonado->getId()
                                                     );
                            $this->serviceFoxPremium->eliminarCaractCorreo($arrayParameter);
                        }
                    }
                    //Se valida si se notifica por correo y sms productos de tv
                    if ($strBanderaNotifica === "S")
                    {
                        //Coger las credenciales de la info_servicio_pro_caract clonadas
                        if(empty($strPassword) && empty($strUsuario))
                        {
                            $arrayParamServProdCarac= array('intIdServicio' =>  $objInfoServicioClonado->getId());
                            $arrayCaracteristicasTv  =   $this->serviceFoxPremium->obtieneArrayCaracteristicas($arrayParamServProdCarac);

                            if(is_array($arrayCaracteristicasTv) && !empty($arrayCaracteristicasTv))
                            {
                                $objServProdCaracContrasenia = $arrayCaracteristicasTv[$arrayProducto['strPass']];
                                $objServProdCaracUsuario     = $arrayCaracteristicasTv[$arrayProducto['strUser']];
                                $strUsuario                  = $objServProdCaracUsuario->getValor();
                                $strPassword                 = $this->serviceCrypt->descencriptar($objServProdCaracContrasenia->getValor());
                            }
                            else
                            {
                                throw new \Exception('No se encontraron características del Servicio '. $arrayProducto['strMensaje']);
                            }
                        }
                        //Guarda Historial de Notificacion de correo y sms
                        $arrayParamHistorial        = array('strUsrCreacion'  => $arrayParams['strUsrCreacion'], 
                                                            'strClientIp'     => $arrayParams['strClientIp'], 
                                                            'objInfoServicio' => $objInfoServicioClonado,
                                                            'strTipoAccion'   => $arrayProducto['strAccionActivo'],
                                                            'strMensaje'      => $arrayProducto['strMensaje']);

                        //Notifico al cliente por Correo y SMS
                        $this->serviceFoxPremium->notificaCorreoServicioFox(
                                                array("strDescripcionAsunto"   => $strAsuntoNuevoServicio,
                                                      "strCodigoPlantilla"     => $strPlantillaCorreo,
                                                      "strEmpresaCod"          => $arrayParams["strCodEmpresa"],
                                                      "intPuntoId"             => $objInfoPuntoClonado->getId(),
                                                      "intIdServicio"          => $objInfoServicioClonado->getId(),
                                                      "strNombreTecnico"       => $strNombreTecnico,
                                                      "intPersonaEmpresaRolId" => $objInfoPuntoClonado->getPersonaEmpresaRolId()
                                                                                                     ->getId(),
                                                      "arrayParametros"        => array("contrasenia" => $strPassword,
                                                                                        "usuario"     => $strUsuario),
                                                      "arrayParamHistorial"    => $arrayParamHistorial
                                                     ));

                        //Se reemplaza la contraseña del mensaje del parámetro
                        $strMensajeSMS = str_replace("{{USUARIO}}",
                                                     $strUsuario,
                                                     str_replace("{{CONTRASENIA}}",
                                                                 $strPassword,
                                                                 $strPlantillaSms));

                        $this->serviceFoxPremium->notificaSMSServicioFox(
                                array("strMensaje"             => $strMensajeSMS,
                                      "strTipoEvento"          => "enviar_infobip",
                                      "strEmpresaCod"          => $arrayParams["strCodEmpresa"],
                                      "intPuntoId"             => $objInfoPuntoClonado->getId(),
                                      "intPersonaEmpresaRolId" => $objInfoPuntoClonado->getPersonaEmpresaRolId()->getId(),
                                      "strNombreTecnico"       => $strNombreTecnico,
                                      "arrayParamHistorial"    => $arrayParamHistorial
                                     )
                               );
                    }

                    $objInfoServicioTecnico = $this->emcom->getRepository('schemaBundle:InfoServicioTecnico')
                                              ->findByServicioId($serv->getId());
                    foreach($objInfoServicioTecnico as $servT)
                    {
                        $objInfoServicioTecnicoClonado = new InfoServicioTecnico();
                        $objInfoServicioTecnicoClonado = clone $servT;
                        $objInfoServicioTecnicoClonado->setServicioId($objInfoServicioClonado);
                        $this->emcom->persist($objInfoServicioTecnicoClonado);
                        $this->emcom->flush();
                    }
                    $objInfoIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')->findByServicioId($serv->getId());
                    foreach($objInfoIp as $ip)
                    {
                        $objInfoIpClonado = new InfoIp();
                        $objInfoIpClonado = clone $ip;
                        $objInfoIpClonado->setServicioId($objInfoServicioClonado->getId());
                        $this->emInfraestructura->persist($objInfoIpClonado);
                        $this->emInfraestructura->flush();

                        // Se procede a Cancelar la informacion de las IPS asociadas al servicio origen del Cambio de Razon Social
                        $ip->setEstado($strEstadoCancelado);
                        $this->emInfraestructura->persist($ip);
                        $this->emInfraestructura->flush();
                    }
                    
                    if($strEjecutaCreacionSolWyAp === "SI")
                    {
                        $arrayParamsWyApTrasladoyCRS    = array("objServicioOrigen"     => $serv,
                                                                "objServicioDestino"    => $objInfoServicioClonado,
                                                                "strCodEmpresa"         => $arrayParams["strCodEmpresa"],
                                                                "strUsrCreacion"        => $arrayParams['strUsrCreacion'],
                                                                "strIpCreacion"         => $arrayParams['strClientIp'],
                                                                "strOpcion"             => " cambio de razón social con cliente existente");
                        $arrayRespuestaWyApTrasladoyCrs = $this->serviceInfoServicio->creaSolicitudWyApTrasladoyCRS($arrayParamsWyApTrasladoyCRS);
                        if($arrayRespuestaWyApTrasladoyCrs["status"] === "ERROR")
                        {
                            throw new \Exception($arrayRespuestaWyApTrasladoyCrs["mensaje"]);
                        }
                    }
                    else if($strEjecutaCreacionSolEdb === "SI")
                    {
                        $arrayParamsEdbTrasladoyCRS    = array( "objServicioOrigen"     => $serv,
                                                                "objServicioDestino"    => $objInfoServicioClonado,
                                                                "strCodEmpresa"         => $arrayParams["strCodEmpresa"],
                                                                "strUsrCreacion"        => $arrayParams['strUsrCreacion'],
                                                                "strIpCreacion"         => $arrayParams['strClientIp'],
                                                                "strOpcion"             => " cambio de razón social con cliente existente");
                        $arrayRespuestaEdbTrasladoyCrs = $this->serviceInfoServicio->creaSolicitudEdbTrasladoyCRS($arrayParamsEdbTrasladoyCRS);
                        if($arrayRespuestaEdbTrasladoyCrs["status"] === "ERROR")
                        {
                            throw new \Exception($arrayRespuestaEdbTrasladoyCrs["mensaje"]);
                        }
                    }else if ($strEjecutaCreacionSolPlan === "SI")
                    {   
                        $arrayParamsCamCRS = array("objServicioOrigen"      => $serv,
                                                    "objServicioDestino"    => $objInfoServicioClonado,
                                                    "strCodEmpresa"         => $arrayParams["strCodEmpresa"],
                                                    "strUsrCreacion"        => $arrayParams['strUsrCreacion'],
                                                    "strIpCreacion"         => $arrayParams['strClientIp'],
                                                    "strOpcion"             => "cambio de razón social con cliente existente");
                        $arrayRespuestaCamCrs  = $this->serviceInfoServicio->creaSolicitudNetLifeCAM($arrayParamsCamCRS);
                        if($arrayRespuestaCamCrs["status"] === "ERROR")
                        {
                            throw new \Exception($arrayRespuestaCamCrs["mensaje"]);
                        }
                    }
                    
                    // Se procede a Cancelar los Servicios Origen del Cambio de Razon Social
                    $serv->setEstado($strEstadoServicioAnterior);
                    $this->emcom->persist($serv);
                    $this->emcom->flush();
                                       
                    // Creo registro en el Historial del Servicio Origen del Cambio de Razon Social
                    $objInfoServicioHistorialOrigen = new InfoServicioHistorial();
                    $objInfoServicioHistorialOrigen->setServicioId($serv);
                    $objInfoServicioHistorialOrigen->setFeCreacion(new \DateTime('now'));
                    $objInfoServicioHistorialOrigen->setUsrCreacion($arrayParams['strUsrCreacion']);
                    if ($objMotivoCambioRs)
                    {
                        $objInfoServicioHistorialOrigen->setMotivoId($objMotivoCambioRs->getId());
                    }
                    $objInfoServicioHistorialOrigen->setEstado($strEstadoServicioAnterior);
                    $objInfoServicioHistorialOrigen->setObservacion($strObservacionServicioAnterior);
                    $this->emcom->persist($objInfoServicioHistorialOrigen);
                    $this->emcom->flush();
                    
                    if(( $arrayParams['strPrefijoEmpresa'] == 'MD' || $arrayParams['strPrefijoEmpresa'] == 'EN')
                        || ($arrayParams['strPrefijoEmpresa'] === 'TN' && is_object($serv->getProductoId()) 
                            && ($serv->getProductoId()->getNombreTecnico() === "INTERNET SMALL BUSINESS"
                                || $serv->getProductoId()->getNombreTecnico() === "TELCOHOME")))
                    {
                        $arrayServiciosLdap[] = array(
                                                        'servicioAnterior' => $serv,
                                                        'servicioNuevo'    => $objInfoServicioClonado
                                                       );
                    }
                    
                    // Se guarda la Plantilla de Comisionistas a la nueva Razon social
                    $arrayServicioComision = $this->emcom->getRepository('schemaBundle:InfoServicioComision')
                                                  ->findBy(array("servicioId" => $serv->getId(), "estado" => "Activo"));

                    foreach($arrayServicioComision as $objServicioComision)
                    {
                        $objInfoServicioComision = clone $objServicioComision;
                        $objInfoServicioComision->setServicioId($objInfoServicioClonado);
                        $objInfoServicioComision->setFeCreacion($strFechaCreacion);
                        $objInfoServicioComision->setIpCreacion($arrayParams['strClientIp']);
                        $objInfoServicioComision->setUsrCreacion($arrayParams['strUsrCreacion']);
                        $this->emcom->persist($objInfoServicioComision);

                        //Cancelo estado de la plantilla del cliente origen del cambio de razon social, guardo usuario, ip y fecha.
                        $objServicioComision->setEstado($strEstadoCancelado);
                        $objServicioComision->setFeUltMod($strFechaCreacion);
                        $objServicioComision->setIpUltMod($arrayParams['strClientIp']);
                        $objServicioComision->setUsrUltMod($arrayParams['strUsrCreacion']);
                        $this->emcom->persist($objServicioComision);

                        /* Guardo un registro en el Historico en la plantilla del cliente origen del cambio de razon social 
                          que se Cancela */
                        $objInfoServicioComisionHisto = new InfoServicioComisionHisto();
                        $objInfoServicioComisionHisto->setServicioComisionId($objServicioComision);
                        $objInfoServicioComisionHisto->setServicioId($objServicioComision->getServicioId());
                        $objInfoServicioComisionHisto->setComisionDetId($objServicioComision->getComisionDetId());
                        $objInfoServicioComisionHisto->setPersonaEmpresaRolId($objServicioComision->getPersonaEmpresaRolId());
                        $objInfoServicioComisionHisto->setComisionVenta($objServicioComision->getComisionVenta());
                        $objInfoServicioComisionHisto->setComisionMantenimiento($objServicioComision->getComisionMantenimiento());
                        $objInfoServicioComisionHisto->setEstado($objServicioComision->getEstado());
                        $objInfoServicioComisionHisto->setObservacion('Plantilla de Comisionistas cancelada por cambio de razón social por login');
                        $objInfoServicioComisionHisto->setFeCreacion($strFechaCreacion);
                        $objInfoServicioComisionHisto->setIpCreacion($arrayParams['strClientIp']);
                        $objInfoServicioComisionHisto->setUsrCreacion($arrayParams['strUsrCreacion']);
                        $this->emcom->persist($objInfoServicioComisionHisto);
                    }

                    //se clonan las solicitudes de agregar equipo y cambio de equipo por soporte que se encuentren en estado permitidos
                        if((is_object($objInfoServicioClonado->getPlanId()) &&
                        ($objInfoServicioClonado->getEstado() == 'Activo' || $objInfoServicioClonado->getEstado() == 'In-Corte')) ||
                         $strTieneEstadoPermitidoServicioEDB == "S"
                         && ($arrayParams['strPrefijoEmpresa'] == 'MD' 
                          || $arrayParams['strPrefijoEmpresa'] == 'EN'))
                     {
                         $arrayParametrosClonarSolCrs = array(
                                                              'objServicioOrigen'  => $serv,
                                                              'objServicioDestino' => $objInfoServicioClonado,
                                                              'strUsrCreacion'     => $arrayParams['strUsrCreacion'],
                                                              'strIpCreacion'      => $arrayParams['strClientIp'],
                                                              'strEmpresaCod'      => $arrayParams["strCodEmpresa"]
                                                             );
                         $this->serviceInfoServicio->clonarSolicitudesPorCrs($arrayParametrosClonarSolCrs);
                     }
                    


                    /*Cambio estado de servicios a Preactivo antes de la autorizacion del contrato*/
                    if($boolAsignaEstadoPreactivo && $objInfoServicioClonado->getEstado() == "Activo")
                    {
                        $objInfoServicioClonado->setEstado($strEstadoServicioPreactivo);
                        $this->emcom->persist($objInfoServicioClonado);
                        
                        //registro de estado PreActivo en historial
                        $entityServicioHistorial = new InfoServicioHistorial();
                        $entityServicioHistorial->setServicioId($objInfoServicioClonado);
                        $entityServicioHistorial->setFeCreacion($strFechaCreacion);
                        $entityServicioHistorial->setUsrCreacion($arrayParams['strUsrCreacion']);
                        $entityServicioHistorial->setEstado($objInfoServicioClonado->getEstado());
                        $entityServicioHistorial->setObservacion($strMensajeEstadoPreactivo);
                        $this->emcom->persist($entityServicioHistorial);

                        $this->emcom->flush();
                    }
                
                    //INI VALIDACIONES KONIBIT
                    $strTelefono           = "";
                    $strCorreo             = "";
                    $arrayListadoServicios = array();
                    $arrayTokenCas         = array();
                    $arrayPdts             = array();
                    $arrayKonibit          = array();
                    $intIdProdKon          = 0;
                    if($arrayParams['strPrefijoEmpresa'] == 'MD')
                    {
                        $arrayParamEmial                           = array();                 
                        $arrayParamEmial['strEstado']              = "Activo";
                        $arrayParamEmial['strDescFormaContacto']   = array("Correo Electronico");
                        $arrayParamEmial['intIdPersonaEmpresaRol'] = $objInfoPersonaEmpresaRol->getId();
                        $arrayCorreoCli                            = $this->emcom
                                                                     ->getRepository('schemaBundle:InfoPersonaContacto')
                                                                     ->getEmailCliente($arrayParamEmial);

                        foreach ($arrayCorreoCli as $arrayCorreo) 
                        {
                            $strCorreo = $arrayCorreo['strFormaContacto'];
                            break;
                        }
                        $arrayParamTelf                           = array();                 
                        $arrayParamTelf['strEstado']              = "Activo";
                        $arrayParamTelf['strDescFormaContacto']   = array("Telefono Movil",
                                                                          "Telefono Movil Claro",
                                                                          "Telefono Movil CNT",
                                                                          "Telefono Movil Digicel",
                                                                          "Telefono Movil Movistar",
                                                                          "Telefono Movil Referencia IPCC",
                                                                          "Telefono Movil Tuenti");
                        $arrayParamTelf['intIdPersonaEmpresaRol'] = $objInfoPersonaEmpresaRol->getId();
                        $arrayContactosTelf                       = $this->emcom
                                                                    ->getRepository('schemaBundle:InfoPersonaContacto')
                                                                    ->getEmailCliente($arrayParamTelf);
                        foreach ($arrayContactosTelf as $arrayContactoT) 
                        {
                            $strTelefono = $arrayContactoT['strFormaContacto'];
                            break;
                        }
                        $arrayParametroKnb     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                       ->getOne('INVOCACION_KONIBIT_ACTUALIZACION', 
                                                                                'TECNICO', 
                                                                                'DEBITOS',
                                                                                'WS_KONIBIT', 
                                                                                '', 
                                                                                '', 
                                                                                '', 
                                                                                '', 
                                                                                '', 
                                                                                $arrayParams['strCodEmpresa']);
                        
                        $arrayListadoServicios = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                 ->get('PRODUCTOS ADICIONALES AUTOMATICOS','COMERCIAL','',
                                                                       'Lista de productos adicionales automaticos',
                                                                       '','','','','',$arrayParams['strCodEmpresa']);
                        if (is_object($serv->getProductoId()))
                        {
                            $intIdProdKon = $serv->getProductoId()->getId();
                            foreach($arrayListadoServicios as $objListado)
                            {
                                // Si encuentra un producto konibit procede pasar la caracteristica
                                if ($intIdProdKon == $objListado['valor1'] && $objListado['valor3'] == "SI")
                                {   //DATA
                                    $intContKonibit = $intContKonibit + 1;
                                    if ($intContKonibit > 1)
                                    {
                                        $strLoginOrigen   = $objInfoPuntoClonado->getLogin();
                                        $intIdPuntoOrigen = $objInfoPuntoClonado->getId();
                                    }
                                    $arrayTokenCas  = $this->serviceTokenCas->generarTokenCas();
                                    //PRODUCTOS
                                    $objProductos   = array('orderID'      => $serv->getId(),
                                                            'productSKU'   => $objInfoServicioClonado->getProductoId()->getCodigoProducto(),
                                                            'productName'  => $objInfoServicioClonado->getProductoId()->getDescripcionProducto(),
                                                            'quantity'     => '1',
                                                            'included'     => false,
                                                            'productoId'   => $intIdProdKon,
                                                            'migrateTo'    => $objInfoServicioClonado->getId(),
                                                            'status'       => 'active'
                                                           );

                                    $arrayPdts[]    = $objProductos;
                                    array_push( $arrayListProdKon,$objProductos );
                                    //DATA
                                    $objDataProd    = array('companyName'   => $objInfoPersona->getRazonSocial() ? 
                                                                               $objInfoPersona->getRazonSocial() :
                                                                               $objInfoPersona->getNombres().' '.
                                                                               $objInfoPersona->getApellidos(),
                                                            'companyCode'   => $objInfoPuntoClonado->getId(),
                                                            'companyID'     => $arrayParams['identificacionCliente'],
                                                            'contactName'   => $objInfoPersona->getRazonSocial() ? 
                                                                               $objInfoPersona->getRazonSocial() :
                                                                               $objInfoPersona->getNombres().' '.
                                                                               $objInfoPersona->getApellidos(),
                                                            'email'         => $strCorreo,
                                                            'phone'         => $strTelefono,
                                                            'login'         => $objInfoPuntoClonado->getLogin(),
                                                            'plan'          => $serv->getProductoId()->getDescripcionProducto(),
                                                            'address'       => $objInfoPuntoClonado->getDireccion(),
                                                            'city'          => $objInfoPuntoClonado->getPuntoCoberturaId()->getNombreJurisdiccion(),
                                                            'sector'        => $objInfoPuntoClonado->getSectorId()->getNombreSector(),
                                                            'status'        => 'active',
                                                            'products'      => $arrayPdts
                                                           );
                                    //DATA
                                    $arrayData      = array('action'        => (isset($arrayParametroKnb["valor5"]) && 
                                                                                !empty($arrayParametroKnb["valor5"]) )
                                                                                ? $arrayParametroKnb["valor5"] : "",
                                                            'partnerID'     => (isset($arrayParametroKnb["valor7"]) &&
                                                                                !empty($arrayParametroKnb["valor7"]) )
                                                                                ? $arrayParametroKnb["valor7"] : "001",
                                                            'companyCode'   => $intCompCodeKon,
                                                            'companyID'     => $objPersonaEmpresaRolOrigen->getPersonaId()
                                                                                                          ->getIdentificacionCliente(),
                                                            'contactName'   => $objPersonaEmpresaRolOrigen->getPersonaId()->getRazonSocial() ? 
                                                                               $objPersonaEmpresaRolOrigen->getPersonaId()->getRazonSocial() :
                                                                               $objPersonaEmpresaRolOrigen->getPersonaId()->getNombres() . " " .
                                                                               $objPersonaEmpresaRolOrigen->getPersonaId()->getApellidos(),
                                                            'login'         => $strLoginOrigenKon,
                                                            'data'          => $objDataProd,
                                                            'requestNumber' => '1',
                                                            'timestamp'     => ''
                                                            );

                                    $arrayKonibit   = array('identifier'    => $objInfoServicioClonado->getId(),
                                                            'type'          => ( isset($arrayParametroKnb["valor4"]) && 
                                                                                 !empty($arrayParametroKnb["valor4"]) )
                                                                                 ? $arrayParametroKnb["valor4"] : "",
                                                            'retryRequered' => true,
                                                            'process'       => ( isset($arrayParametroKnb["valor6"]) && 
                                                                                 !empty($arrayParametroKnb["valor6"]) )
                                                                                 ? $arrayParametroKnb["valor6"] : "",
                                                            'origin'        => ( isset($arrayParametroKnb["valor3"]) && 
                                                                                 !empty($arrayParametroKnb["valor3"]) )
                                                                                 ? $arrayParametroKnb["valor3"] : "",
                                                            'user'          => $arrayParams['strUsrCreacion'],
                                                            'uri'           => ( isset($arrayParametroKnb["valor1"]) && 
                                                                                 !empty($arrayParametroKnb["valor1"]) )
                                                                                 ? $arrayParametroKnb["valor1"] : "",
                                                            'executionIp'   => $arrayParams['strClientIp'],
                                                            'data'          => $arrayData
                                                            );


                                    $arrayEnvKon [] = array('strToken'         => $arrayTokenCas['strToken'],
                                                            'strUser'          => $arrayParams['strUsrCreacion'],
                                                            'strIp'            => $arrayParams['strClientIp'],
                                                            'arrayPropiedades' => $arrayKonibit);
                                }
                            }
                        }
                        else
                        {
                            $objPlanDet = $this->emcom->getRepository('schemaBundle:InfoPlanDet')
                                                      ->findBy(array('planId' => $serv->getPlanId(),
                                                                     'estado' => "Activo"));
                            if(($objPlanDet))
                            {
                                foreach($objPlanDet as $idxPlanDet)
                                {
                                    $objProducto = $this->emcom->getRepository('schemaBundle:AdmiProducto')
                                                               ->find($idxPlanDet->getProductoId());

                                    if(is_object($objProducto))
                                    {
                                        $intIdProdKon = $idxPlanDet->getProductoId();
                                        foreach($arrayListadoServicios as $objListado)
                                        {
                                            // Si encuentra un producto konibit procede pasar la caracteristica
                                            if ($intIdProdKon == $objListado['valor1'] && $objListado['valor3'] == "SI")
                                            {   //DATA
                                                $intContKonibit = $intContKonibit + 1;
                                                if ($intContKonibit > 1)
                                                {
                                                    $strLoginOrigen   = $objInfoPuntoClonado->getLogin();
                                                    $intIdPuntoOrigen = $objInfoPuntoClonado->getId();
                                                }
                                                $arrayPdts      = array();
                                                $arrayTokenCas  = $this->serviceTokenCas->generarTokenCas();
                                                //PRODUCTOS
                                                $objProductos   = array('orderID'      => $serv->getId(),
                                                                        'productSKU'   => $objProducto->getCodigoProducto(),
                                                                        'productName'  => $objProducto->getDescripcionProducto(),
                                                                        'quantity'     => '1',
                                                                        'included'     => true,
                                                                        'productoId'   => $intIdProdKon,
                                                                        'migrateTo'    => $objInfoServicioClonado->getId(),
                                                                        'status'       => 'active'
                                                                       );

                                                $arrayPdts[]    = $objProductos;
                                                array_push( $arrayListProdKon,$objProductos );
                                                //DATA
                                                $objDataProd    = array('companyName'   => $objInfoPersona->getRazonSocial() ? 
                                                                                           $objInfoPersona->getRazonSocial() :
                                                                                           $objInfoPersona->getNombres().' '.
                                                                                           $objInfoPersona->getApellidos(),
                                                                        'companyCode'   => $objInfoPuntoClonado->getId(),
                                                                        'companyID'     => $arrayParams['identificacionCliente'],
                                                                        'contactName'   => $objInfoPersona->getRazonSocial() ? 
                                                                                           $objInfoPersona->getRazonSocial() :
                                                                                           $objInfoPersona->getNombres().' '.
                                                                                           $objInfoPersona->getApellidos(),
                                                                        'email'         => $strCorreo,
                                                                        'phone'         => $strTelefono,
                                                                        'login'         => $objInfoPuntoClonado->getLogin(),
                                                                        'plan'          => $serv->getPlanId()->getNombrePlan(),
                                                                        'address'       => $objInfoPuntoClonado->getDireccion(),
                                                                        'city'          => $objInfoPuntoClonado->getPuntoCoberturaId()
                                                                                                               ->getNombreJurisdiccion(),
                                                                        'sector'        => $objInfoPuntoClonado->getSectorId()->getNombreSector(),
                                                                        'status'        => 'active',
                                                                        'products'      => $arrayPdts
                                                                       );
                                                //DATA
                                                $arrayData      = array('action'        => (isset($arrayParametroKnb["valor5"]) && 
                                                                                            !empty($arrayParametroKnb["valor5"]) )
                                                                                            ? $arrayParametroKnb["valor5"] : "",
                                                                        'partnerID'     => (isset($arrayParametroKnb["valor7"]) &&
                                                                                            !empty($arrayParametroKnb["valor7"]) )
                                                                                            ? $arrayParametroKnb["valor7"] : "001",
                                                                        'companyCode'   => $intCompCodeKon,
                                                                        'companyID'     => $objPersonaEmpresaRolOrigen->getPersonaId()
                                                                                                                      ->getIdentificacionCliente(),
                                                                        'contactName'   => $objPersonaEmpresaRolOrigen->getPersonaId()
                                                                                                                      ->getRazonSocial() ? 
                                                                                           $objPersonaEmpresaRolOrigen->getPersonaId()
                                                                                                                      ->getRazonSocial() :
                                                                                           $objPersonaEmpresaRolOrigen->getPersonaId()
                                                                                                                      ->getNombres() . " " .
                                                                                           $objPersonaEmpresaRolOrigen->getPersonaId()
                                                                                                                      ->getApellidos(),
                                                                        'login'         => $strLoginOrigenKon,
                                                                        'data'          => $objDataProd,
                                                                        'requestNumber' => '1',
                                                                        'timestamp'     => ''
                                                                        );

                                                $arrayKonibit   = array('identifier'    => $objInfoServicioClonado->getId(),
                                                                        'type'          => ( isset($arrayParametroKnb["valor4"]) && 
                                                                                             !empty($arrayParametroKnb["valor4"]) )
                                                                                             ? $arrayParametroKnb["valor4"] : "",
                                                                        'retryRequered' => true,
                                                                        'process'       => ( isset($arrayParametroKnb["valor6"]) && 
                                                                                             !empty($arrayParametroKnb["valor6"]) )
                                                                                             ? $arrayParametroKnb["valor6"] : "",
                                                                        'origin'        => ( isset($arrayParametroKnb["valor3"]) && 
                                                                                             !empty($arrayParametroKnb["valor3"]) )
                                                                                             ? $arrayParametroKnb["valor3"] : "",
                                                                        'user'          => $arrayParams['strUsrCreacion'],
                                                                        'uri'           => ( isset($arrayParametroKnb["valor1"]) && 
                                                                                             !empty($arrayParametroKnb["valor1"]) )
                                                                                             ? $arrayParametroKnb["valor1"] : "",
                                                                        'executionIp'   => $arrayParams['strClientIp'],
                                                                        'data'          => $arrayData
                                                                        );


                                                $arrayEnvKon [] = array('strToken'         => $arrayTokenCas['strToken'],
                                                                        'strUser'          => $arrayParams['strUsrCreacion'],
                                                                        'strIp'            => $arrayParams['strClientIp'],
                                                                        'arrayPropiedades' => $arrayKonibit);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
                //Se verifica si servicio está atado a un producto con la característica NETLIFECLOUD
                $arrayCaractProdNetlifeCloud    = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                              ->getInfoCaractProducto(array(
                                                                                                'intServicioId'        => $serv->getId(),
                                                                                                'strCaracteristica'    => 'NETLIFECLOUD'
                                                                                            ));
                if($arrayCaractProdNetlifeCloud['caracteristica'] === 'NETLIFECLOUD')
                {
                    $arrayServiciosNetlifeCloud[]   = array('intIdServicio' => $serv->getId());
                }
            }
            
            //Guardo relacion de los Puntos Logines destinos del Cambio de Razon Social con sus Puntos Logines origen
            $objInfoPuntoCaracteristica = new InfoPuntoCaracteristica();
            $objInfoPuntoCaracteristica->setPuntoId($objInfoPuntoClonado);
            $objInfoPuntoCaracteristica->setCaracteristicaId($objAdmiCaracteristica);
            $objInfoPuntoCaracteristica->setValor($objInfoPuntoOrigen->getId());
            $objInfoPuntoCaracteristica->setFeCreacion($strFechaCreacion);
            $objInfoPuntoCaracteristica->setUsrCreacion($arrayParams['strUsrCreacion']);
            $objInfoPuntoCaracteristica->setIpCreacion($arrayParams['strClientIp']);
            $objInfoPuntoCaracteristica->setEstado($strEstadoActivo);
            $this->emcom->persist($objInfoPuntoCaracteristica);
            $this->emcom->flush();

            // Se procede a Cancelar los Logines Origen del Cambio de Razon Social
            $objInfoPuntoOrigen->setEstado($strEstadoCancelado);
            $this->emcom->persist($objInfoPuntoOrigen);
            $this->emcom->flush();
            
        }// fin foreach($arrayPuntosSeleccionados as $key => $value)     
        
        if (!empty($arrayEnvKon)) 
        {
            $arrayRespuestaProceso  = array("arrayServiciosLdap"            => $arrayServiciosLdap,
                                            "arrayServiciosNetlifeCloud"    => $arrayServiciosNetlifeCloud,
                                            "arrayPuntosCRS"                => $arrayPuntosCRSActivar,
                                            "strMensajeCorreoECDF"          => $strMensajeCorreoECDF,
                                            "arrayEnvKon"                   => $arrayEnvKon);
        }
        else
        {
            $arrayRespuestaProceso  = array("arrayServiciosLdap"            => $arrayServiciosLdap,
                                            "arrayServiciosNetlifeCloud"    => $arrayServiciosNetlifeCloud,
                                            "arrayPuntosCRS"                => $arrayPuntosCRSActivar,
                                            "strMensajeCorreoECDF"          => $strMensajeCorreoECDF);
        }
        
        return $arrayRespuestaProceso;
    }

    /**   
     * Documentación para el método 'cambioRazonSocialClienteNuevo'.
     *
     * Metodo usado para ejecutar el "Cambio de Razon Social por Punto" en el Caso que se realice hacia un cliente Nuevo.     
     * 1) Se procedera a crear el nuevo Cliente con ROL de "Pre-cliente".
     * 2) Se generaran las nuevas Formas de Contacto del nuevo PreCliente.
     * 3) Se generara un nuevo Contrato en estado "Pendiente" para el PreCliente.
     * 4) Se subiran los archivos digitales adjuntos al nuevo Contrato Pendiente.             
     *              
     * @param array    $arrayParams 
     * @param array    $arrayPuntosSeleccionados
     * @param boolean  $boolExistePersona
     * @param boolean  $boolExisPerConContrato
     * @param object   $objAdmiCaracteristica
     * 
     * @throws Exception
     * @return \telconet\schemaBundle\Entity\InfoPersonaEmpresaRol
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 23-10-2015     
     * 
     * Se agregan campos Nuevos CarnetConadis, EsPrepago, PagaIva, ContribuyenteEspecial y Combo de Oficinas de Facturacion
     * para el caso de ser empresa Telconet se deben pedir dichos campos, en el caso de empresa Megadatos se deben setear los
     * valores por dafault
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.1 06-06-2016  
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.2 08-11-2016
     * Se envia array de Parametros $arrayParamFormasContac a la funcion "validarFormasContactos" se agrega strOpcionPermitida y strPrefijoEmpresa, 
     * Prefijo de empresa en sesion para validar que para empresa MD no se obligue el ingreso de al menos 1 correo.
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 07-07-2017
     * Se modifica envio de parametros en $arrayParametrosValidaCtaTarj a la función validarNumeroTarjetaCta
     * 
     * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
     * @version 1.4 03-10-2017
     * Se agrega que cuando se realice CRS por Login hacia cliente nuevo se asigne la caracteristrica CICLO_FACTURACION en el nuevo Cliente o
     * cliente destino del cambio de razón social
     * Si se trata de un CRS por Login hacia un Cliente ya existente, este deberá mantener su ciclo de facturación ya existente.
     * 
     * @author Jorge Guerrero<jguerrerop@telconet.ec>
     * @version 1.5 01-12-2017
     * Se agrega el parametro por empresa configurado en la admi_parametro
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.6 19-04-2018 - Se agrega la variable 'serviceComercial'
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.7
     * @since 17-05-2018
     * Cambio por ciclos de facturación:
     * Se hereda el ciclo del cliente origen.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.8 27-12-2018 - Se realizan correcciones para Telconet Panama, no esta tomando la oficina en sesion cuando se trata de Panama.
     *                           Se envia al array que valida las formas de contacto el Nombre y Id Pais.
     * 
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.9 09-12-2020 - Se registra el representante legal para nuevos clientes con tipo tributario Juridico.
     *
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 2.0 03-12-2020 - Se agrega validación para generar contrato fisico o digital MD.
     *
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 2.1 22-06-2021 - Se arregla bug de la info_documento relación apuntando al esquema COMERCIAL.
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 2.2 22-11-2021 - Se agrega try catch y se valida el ingreso del representnte legal para MD.
     * 
     *  
     * @author Jorge Veliz <jlveliz@telconet.ec>
     * @version 3.0 22-06-2021 - ENvian a  guardar los documentos agregados al nuevo NFS server.
     * 
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 3.1 10-01-2022 - Se agrega busqueda de codEmpresa al momento de validar la identificación
     *
     */       
    public function cambioRazonSocialClienteNuevo($arrayParams, $arrayPuntosSeleccionados, $boolExistePersona, $boolExisPerConContrato,
                                                  $objAdmiCaracteristica)
    {

        $objResponse = array(); 

        $strFechaCreacion     = new \DateTime('now');
        $strEstadoPendiente   = "Pendiente";
        $strEstadoActivo      = "Activo";
        $strError             = "";
        $strNumeroCtaTarjeta  = "";

        $arrayParametros['strPrefijoEmpresa'] = $arrayParams['strPrefijoEmpresa'];
        $arrayParametros['strEmpresaCod']     = $arrayParams['strCodEmpresa'];
        $intContratoFisico                    = $arrayParams['intContratoFisico'];
        $strMensajeCorreoECDF       = "";
        $strTieneCorreoElectronico  = "";
        if(isset($arrayParams["tieneCorreoElectronico"]) 
        && !empty($arrayParams["tieneCorreoElectronico"]))
        {
            $strTieneCorreoElectronico = $arrayParams["tieneCorreoElectronico"];
        }

        try
        {
        
        $strAplicaCiclosFac = $this->serviceComercial->aplicaCicloFacturacion($arrayParametros);
        // Verifico, Si no existe registro de la Persona la Ingreso
        if(!$boolExistePersona)
        {   
            $objInfoPersona = new InfoPersona();
            $objInfoPersona->setTipoIdentificacion($arrayParams['tipoIdentificacion']);
            $objInfoPersona->setIdentificacionCliente($arrayParams['identificacionCliente']);
            $objInfoPersona->setFeCreacion($strFechaCreacion);
            $objInfoPersona->setUsrCreacion($arrayParams['strUsrCreacion']);
            $objInfoPersona->setIpCreacion($arrayParams['strClientIp']);
            $objInfoPersona->setEstado($strEstadoPendiente);
            // Campo para marcar si el registro de una persona se origino en la aplicacion WEB Telcos "S"
            if($arrayParams ['origen_web'])
            {
                if($arrayParams ['origen_web'] == "S" || $arrayParams ['origen_web'] == "N")
                {
                    $objInfoPersona->setOrigenWeb($arrayParams ['origen_web']);
                }
            }
        }
        else
        {
            if(!$boolExisPerConContrato)
            {
                $objInfoPersona = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                  ->findOneByIdentificacionCliente($arrayParams['identificacionCliente']);
            }
        }

        if(!isset($arrayParams ['tipoEmpresa']))
        {
            $arrayParams ['tipoEmpresa'] = null;
        }
        $objInfoPersona->setTipoEmpresa($arrayParams['tipoEmpresa']);
        $objInfoPersona->setTipoTributario($arrayParams['tipoTributario']);
        $objInfoPersona->setRazonSocial($arrayParams['razonSocial']);
        $objInfoPersona->setRepresentanteLegal($arrayParams['representanteLegal']);
        $objInfoPersona->setNacionalidad($arrayParams['nacionalidad']);
        $objInfoPersona->setDireccionTributaria($arrayParams['direccionTributaria']);
        if($arrayParams['strPrefijoEmpresa'] == 'TN')
        {
            $objInfoPersona->setContribuyenteEspecial($arrayParams ['contribuyenteEspecial']);
            $objInfoPersona->setPagaIva($arrayParams ['pagaIva']);
            $objInfoPersona->setNumeroConadis($arrayParams ['numeroConadis']);
        }
        else
        {
            $objInfoPersona->setPagaIva('S');
        }
        if(!$arrayParams['tipoEmpresa'])
        {
            $objInfoPersona->setOrigenIngresos($arrayParams ['origenIngresos']);
            $objInfoPersona->setGenero($arrayParams['genero']);
            $objInfoPersona->setEstadoCivil($arrayParams['estadoCivil']);
            $objInfoPersona->setNombres($arrayParams['nombres']);
            $objInfoPersona->setApellidos($arrayParams['apellidos']);

            // Conversion desde el mobile DATE -> ARRAY
            if(!is_array($arrayParams ['fechaNacimiento']))
            {
                $fechaNacimientoObj   = $arrayParams['fechaNacimiento']->format('Y-m-d');
                $fechaNacimientoArray = explode('-', $fechaNacimientoObj);
                $arrayParams['fechaNacimiento'] = array('year' => $fechaNacimientoArray[0],
                                                        'month' => $fechaNacimientoArray[1],
                                                        'day' => $fechaNacimientoArray[2]);
            }

            if(!$arrayParams['fechaNacimiento'] ||
              (!$arrayParams ['fechaNacimiento'] ['year'] &&
               !$arrayParams ['fechaNacimiento'] ['month'] &&
               !$arrayParams ['fechaNacimiento'] ['day']))
            {
                throw new \Exception('La Fecha de Nacimiento es un campo obligatorio - '
                                     . 'No se pudo generar el Cambio de Razon Social por Login');
            }
            else
            {
                if(is_array($arrayParams ['fechaNacimiento']) &&
                  ($arrayParams ['fechaNacimiento'] ['year'] &&
                   $arrayParams ['fechaNacimiento'] ['month'] &&
                   $arrayParams ['fechaNacimiento'] ['day']))
                {
                    $intEdad = $this->servicePreCliente->devuelveEdadPorFecha($arrayParams ['fechaNacimiento'] ['year'] .
                                '-' . $arrayParams ['fechaNacimiento'] ['month'] .
                                '-' . $arrayParams ['fechaNacimiento'] ['day']);
                    if($intEdad < 18)
                    {
                        throw new \Exception('La Fecha de Nacimiento ingresada corresponde a un menor de edad - '
                                            . 'No se pudo generar el Cambio de Razon Social por Login : '
                                            . $arrayParams ['fechaNacimiento'] ['year'] . '-'
                                            . $arrayParams ['fechaNacimiento'] ['month'] . '-'
                                            . $arrayParams ['fechaNacimiento'] ['day']);
                    }
                }
            }
            if($arrayParams ['fechaNacimiento'] instanceof \DateTime)
            {
                $objInfoPersona->setFechaNacimiento($arrayParams ['fechaNacimiento']);
            }
            else if(is_array($arrayParams ['fechaNacimiento']))
            {
                if($arrayParams ['fechaNacimiento'] ['year'] &&
                   $arrayParams ['fechaNacimiento'] ['month'] &&
                   $arrayParams ['fechaNacimiento'] ['day'])
                {
                    $objInfoPersona->setFechaNacimiento(date_create($arrayParams ['fechaNacimiento'] ['year'] . '-' .
                                                                    $arrayParams ['fechaNacimiento'] ['month'] . '-' .
                                                                    $arrayParams ['fechaNacimiento'] ['day']));
                }
            }
            $objAdmiTitulo = $this->emcom->getRepository('schemaBundle:AdmiTitulo')->find($arrayParams['tituloId']);
            if($objAdmiTitulo)
            {
                $objInfoPersona->setTituloId($objAdmiTitulo);
            }
        }
        $objInfoPersona->setOrigenProspecto('N');
        $this->emcom->persist($objInfoPersona);
        
        // Creo registro de la persona con Rol de Pre-cliente
        $objInfoPersonaEmpresaRol = new InfoPersonaEmpresaRol();
        $objInfoEmpresaRol        = $this->emcom->getRepository('schemaBundle:InfoEmpresaRol')
                                    ->findPorNombreTipoRolPorEmpresa('Pre-cliente', $arrayParams['strCodEmpresa']);
        if(!$objInfoEmpresaRol)
        {
            throw new \Exception('No encontro Rol de Pre-cliente, para la empresa [' . $arrayParams['strPrefijoEmpresa'] . '] - '
                               . 'No se pudo generar el Cambio de Razon Social por Login');
        }
        $objInfoPersonaEmpresaRol->setPersonaId($objInfoPersona);
        $objInfoPersonaEmpresaRol->setEmpresaRolId($objInfoEmpresaRol);
        
        if($arrayParams['strPrefijoEmpresa'] == 'TN')
        {
            $objInfoOficinaGrupoTN = $this->emcom->getRepository('schemaBundle:InfoOficinaGrupo')->find((int) $arrayParams['idOficinaFacturacion']);
            if(!$objInfoOficinaGrupoTN)
            {
                throw new \Exception('No encontro Oficina [' . $arrayParams['idOficinaFacturacion'] . '] para la creacion del Cliente - '
                               . 'No se pudo generar el Cambio de Razon Social por Login');
            }
            $objInfoPersonaEmpresaRol->setOficinaId($objInfoOficinaGrupoTN);
            $objInfoPersonaEmpresaRol->setEsPrepago($arrayParams ['esPrepago']);
        }
        else
        {
            if($arrayParams['strPrefijoEmpresa'] == 'TNP')
            {
                $objInfoOficinaGrupo = $this->emcom->getRepository('schemaBundle:InfoOficinaGrupo')->find($arrayParams['intIdOficina']);
                if(!$objInfoOficinaGrupo)
                {
                    throw new \Exception('No encontro Oficina [' . $arrayParams['intIdOficina'] . '] para la creacion del Cliente - '
                    . 'No se pudo generar el Cambio de Razon Social por Login');
                }
                $objInfoPersonaEmpresaRol->setOficinaId($objInfoOficinaGrupo);
                $objInfoPersonaEmpresaRol->setEsPrepago('S');
            }
        }
        if ('S' == $strAplicaCiclosFac)
        {
            $objInfoOficinaGrupo = $this->emcom->getRepository('schemaBundle:InfoOficinaGrupo')->find($arrayParams['intIdOficina']);
            if(!$objInfoOficinaGrupo)
            {
                throw new \Exception('No encontro Oficina [' . $arrayParams['intIdOficina'] . '] para la creacion del Cliente - '
                               . 'No se pudo generar el Cambio de Razon Social por Login');
            }
            $objInfoPersonaEmpresaRol->setOficinaId($objInfoOficinaGrupo);
            $objInfoPersonaEmpresaRol->setEsPrepago('S');
            
            //Obtengo Caracteristica de CICLO_FACTURACION
            $objCaracteristicaCiclo = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                  ->findOneBy(array("descripcionCaracteristica" => "CICLO_FACTURACION",
                                                                    "estado" => "Activo"));
            if(!is_object($objCaracteristicaCiclo))
            {
                throw new \Exception('No existe Caracteristica CICLO_FACTURACION - No se pudo generar el Cambio de Razón Social por Login');
            }

            $objPerEmpRolCaracOrigen = $this->emcom->getRepository("schemaBundle:InfoPersonaEmpresaRolCarac")
                                                   ->findOneBy(array("personaEmpresaRolId" => $arrayParams["personaEmpresaRolId"],
                                                                     "estado"              => "Activo",
                                                                     "caracteristicaId"    => $objCaracteristicaCiclo->getId()));

            $objAdmiCiclo            = $this->emcom->getRepository("schemaBundle:AdmiCiclo")
                                                   ->find($objPerEmpRolCaracOrigen->getValor());

            //Inserto Caracteristica de CICLO_FACTURACION en el nuevo cliente destino del CRS
            $objPersEmpRolCaracCiclo = new InfoPersonaEmpresaRolCarac();
            $objPersEmpRolCaracCiclo->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
            $objPersEmpRolCaracCiclo->setCaracteristicaId($objCaracteristicaCiclo);
            $objPersEmpRolCaracCiclo->setValor($objPerEmpRolCaracOrigen->getValor());
            $objPersEmpRolCaracCiclo->setFeCreacion($strFechaCreacion);
            $objPersEmpRolCaracCiclo->setUsrCreacion($arrayParams['strUsrCreacion']);
            $objPersEmpRolCaracCiclo->setEstado($strEstadoActivo);
            $objPersEmpRolCaracCiclo->setIpCreacion($arrayParams['strClientIp']);
            $this->emcom->persist($objPersEmpRolCaracCiclo);

            //Inserto Historial de creacion de caracteristica de CICLO_FACTURACION en el Pre_cliente                
            $objPersEmpRolCaracCicloHisto = new InfoPersonaEmpresaRolHisto();
            $objPersEmpRolCaracCicloHisto->setUsrCreacion($arrayParams['strUsrCreacion']);
            $objPersEmpRolCaracCicloHisto->setFeCreacion($strFechaCreacion);
            $objPersEmpRolCaracCicloHisto->setIpCreacion($arrayParams['strClientIp']);
            $objPersEmpRolCaracCicloHisto->setEstado($strEstadoActivo);
            $objPersEmpRolCaracCicloHisto->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
            $objPersEmpRolCaracCicloHisto->setObservacion('Se generó cambio de Razón Social por Login y se asignó Ciclo de Facturación: '
                . $objAdmiCiclo->getNombreCiclo());
            $this->emcom->persist($objPersEmpRolCaracCicloHisto);
        }        
        $objInfoPersonaEmpresaRol->setFeCreacion($strFechaCreacion);
        $objInfoPersonaEmpresaRol->setUsrCreacion($arrayParams['strUsrCreacion']);
        $objInfoPersonaEmpresaRol->setEstado($strEstadoActivo);
        $objInfoPersonaEmpresaRol->setIpCreacion($arrayParams['strClientIp']);
        $this->emcom->persist($objInfoPersonaEmpresaRol);

        // Creo registro del Historial de la Persona Empresa Rol Activo
        $objInfoPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
        $objInfoPersonaEmpresaRolHisto->setEstado($strEstadoActivo);
        $objInfoPersonaEmpresaRolHisto->setFeCreacion($strFechaCreacion);
        $objInfoPersonaEmpresaRolHisto->setIpCreacion($arrayParams['strClientIp']);
        $objInfoPersonaEmpresaRolHisto->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
        $objInfoPersonaEmpresaRolHisto->setUsrCreacion($arrayParams['strUsrCreacion']);
        $this->emcom->persist($objInfoPersonaEmpresaRolHisto);

        // Valido Formas de Contacto Ingresadas                
        $arrayFormasContacto = array();
        if($arrayParams['formas_contacto'])
        {
            $array_formas_contacto = explode(',', $arrayParams['formas_contacto']);
            for($intI = 0; $intI < count($array_formas_contacto); $intI+=3)
            {
                $arrayFormasContacto[] = array('formaContacto' => $array_formas_contacto[$intI + 1],
                                               'valor'         => $array_formas_contacto[$intI + 2]);
            }
        }
        /* Se envia array de Parametros y se agrega strOpcionPermitida y strPrefijoEmpresa, Prefijo de empresa en sesion para validar
         * que para empresa MD no se obligue el ingreso de al menos 1 correo */
        $arrayParamFormasContac                        = array ();
        $arrayParamFormasContac['strPrefijoEmpresa']   = $arrayParams['strPrefijoEmpresa'];
        $arrayParamFormasContac['arrayFormasContacto'] = $arrayFormasContacto;
        $arrayParamFormasContac['strOpcionPermitida']  = 'NO';
        $arrayParamFormasContac['strNombrePais']       = $arrayParams['strNombrePais'];
        $arrayParamFormasContac['intIdPais']           = $arrayParams['intIdPais'];
        
        $arrayValidaciones = $this->serviceInfoPersonaFormaContacto->validarFormasContactos($arrayParamFormasContac);
        if($arrayValidaciones)
        {
            foreach($arrayValidaciones as $key => $mensaje_validaciones)
            {
                foreach($mensaje_validaciones as $key_msj => $value)
                {
                    $strError = $strError . $value . ".\n";
                }
            }
            throw new \Exception("No se pudo generar el Cambio de Razon Social por Login - " . $strError);
        }
        // Pone en estado Inactivo a todas las formas de Contacto de la Persona que tenga en estado Activo                
        $this->serviceInfoPersonaFormaContacto->inactivarPersonaFormaContactoActivasPorPersona($objInfoPersona->getId(),
                                                                                               $arrayParams['strUsrCreacion']);
        // Registra las formas de Contacto del Cliente
        for($i = 0; $i < count($arrayFormasContacto); $i++)
        {
            $objInfoPersonaFormaContacto = new InfoPersonaFormaContacto();
            $objInfoPersonaFormaContacto->setValor($arrayFormasContacto[$i]["valor"]);
            $objInfoPersonaFormaContacto->setEstado($strEstadoActivo);
            $objInfoPersonaFormaContacto->setFeCreacion($strFechaCreacion);

            if(isset($arrayFormasContacto[$i]['idFormaContacto']))
            {
                $objAdmiFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')
                                        ->find($arrayFormasContacto[$i]['idFormaContacto']);
            }
            else
            {
                $objAdmiFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')
                                        ->findPorDescripcionFormaContacto($arrayFormasContacto [$i] ['formaContacto']);
            }

            $objInfoPersonaFormaContacto->setFormaContactoId($objAdmiFormaContacto);
            $objInfoPersonaFormaContacto->setIpCreacion($arrayParams['strClientIp']);
            $objInfoPersonaFormaContacto->setPersonaId($objInfoPersona);
            $objInfoPersonaFormaContacto->setUsrCreacion($arrayParams['strUsrCreacion']);
            $this->emcom->persist($objInfoPersonaFormaContacto);
        }

        if(($arrayParams["strCodEmpresa"] != 18 && $arrayParams["strCodEmpresa"] != 33)|| $intContratoFisico == 1)
        {
            // Ingreso Contrato para el nuevo Pre-cliente
            $objInfoContrato = new InfoContrato();
            $objInfoContrato->setValorAnticipo($arrayParams['valorAnticipo']);
            $objInfoContrato->setNumeroContratoEmpPub($arrayParams['numeroContratoEmpPub']);

            // Obtener la numeracion de la tabla admi_numeracion
            $strSecuenciaAsig  = null;
            $strNumeroContrato = null;
            $objAdmiNumeracion = $this->emcom->getRepository('schemaBundle:AdmiNumeracion')
                                ->findByEmpresaYOficina($arrayParams['strCodEmpresa'], $arrayParams['intIdOficina'], 'CON');
            if($objAdmiNumeracion)
            {
                $strSecuenciaAsig  = str_pad($objAdmiNumeracion->getSecuencia(), 7, "0", STR_PAD_LEFT);
                $strNumeroContrato = $objAdmiNumeracion->getNumeracionUno() . "-" . $objAdmiNumeracion->getNumeracionDos() .
                                    "-" . $strSecuenciaAsig;
                $objInfoContrato->setNumeroContrato($strNumeroContrato);
            }
            if($arrayParams['feFinContratoPost'] instanceof \DateTime)
            {
                $objInfoContrato->setFeFinContrato($arrayParams['feFinContratoPost']);
            }
            else
            {
                $arrayStartExp = explode("-", $arrayParams['feFinContratoPost']);
                $strFechaFin   = date("Y-m-d H:i:s", strtotime($arrayStartExp[0] . "-" . $arrayStartExp[1] . "-" . $arrayStartExp[2]));
                $objInfoContrato->setFeFinContrato(date_create($strFechaFin));
            }

            $objAdmiFormaPago    = $this->emcom->getRepository('schemaBundle:AdmiFormaPago')->find($arrayParams['formaPagoId']);
            $objAdmiTipoContrato = $this->emcom->getRepository('schemaBundle:AdmiTipoContrato')->find($arrayParams['tipoContratoId']);

            $objInfoContrato->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
            $objInfoContrato->setFormaPagoId($objAdmiFormaPago);
            $objInfoContrato->setTipoContratoId($objAdmiTipoContrato);
            $objInfoContrato->setFeCreacion($strFechaCreacion);
            $objInfoContrato->setUsrCreacion($arrayParams['strUsrCreacion']);
            $objInfoContrato->setEstado($strEstadoPendiente);
            $objInfoContrato->setIpCreacion($arrayParams['strClientIp']);
            $this->emcom->persist($objInfoContrato);

            if($objInfoContrato)
            {
                // Actualizo la numeracion en la tabla
                $intNumeroAct = ($objAdmiNumeracion->getSecuencia() + 1);
                $objAdmiNumeracion->setSecuencia($intNumeroAct);
                $this->emcom->persist($objAdmiNumeracion);
            }
            // Informacion de las formas de pagos con datos adicionales
            if(($arrayParams['tipoCuentaId'] != '') && ($arrayParams['bancoTipoCuentaId'] != '') &&
            ($arrayParams['numeroCtaTarjeta'] != '') && ($arrayParams['titularCuenta'] != ''))
            {
                // Llamo a funcion para validar numero de cuenta/tarjeta            
                $arrayParametrosValidaCtaTarj                          = array();
                $arrayParametrosValidaCtaTarj['intTipoCuentaId']       = $arrayParams['tipoCuentaId'];
                $arrayParametrosValidaCtaTarj['intBancoTipoCuentaId']  = $arrayParams['bancoTipoCuentaId'];
                $arrayParametrosValidaCtaTarj['strNumeroCtaTarjeta']   = $arrayParams['numeroCtaTarjeta'];
                $arrayParametrosValidaCtaTarj['strCodigoVerificacion'] = $arrayParams['codigoVerificacion'];
                $arrayParametrosValidaCtaTarj['strCodEmpresa']         = $arrayParams['strCodEmpresa'];
                $arrayParametrosValidaCtaTarj['intFormaPagoId']        = $arrayParams['formaPagoId'];

                $arrayValidaciones = $this->validarNumeroTarjetaCta($arrayParametrosValidaCtaTarj);
                if($arrayValidaciones)
                {
                    foreach($arrayValidaciones as $key => $mensaje_validaciones)
                    {
                        foreach($mensaje_validaciones as $key_msj => $value)
                        {
                            $strError = $strError . $value . ".\n";
                        }
                    }
                    throw new \Exception("No se pudo generar el Cambio de Razon Social por Login - " . $strError);
                }

                $objAdmiBancoTipoCuenta = $this->emcom->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                                        ->find($arrayParams['bancoTipoCuentaId']);
                $objAdmiTipoCuenta      = $this->emcom->getRepository('schemaBundle:AdmiTipoCuenta')->find($arrayParams['tipoCuentaId']);
                if($objAdmiBancoTipoCuenta->getEsTarjeta() == 'S')
                {
                    if(!$arrayParams['mesVencimiento'] || !$arrayParams['anioVencimiento'])
                    {
                        throw new \Exception('No se pudo generar el Cambio de Razon Social por Login - '
                                        . 'El Anio y mes de Vencimiento de la tarjeta son campos obligatorios');
                    }
                    if(!$arrayParams['codigoVerificacion'])
                    {
                        throw new \Exception('No se pudo generar el Cambio de Razon Social por Login - '
                                        . 'El codigo de verificacion de la tarjeta es un campo obligatorio');

                    }
                }
                $objInfoContratoFormaPago = new InfoContratoFormaPago();
                $objInfoContratoFormaPago->setContratoId($objInfoContrato);

                if(!$arrayParams['numeroCtaTarjeta'])
                {
                    throw new \Exception('No se pudo generar el Cambio de Razon Social por Login - '
                                    . 'El Numero de Cuenta / Tarjeta es un campo obligatorio ');
                }
                // Llamo a funcion que realiza encriptado del numero de cuenta
                $strNumeroCtaTarjeta = $this->serviceCrypt->encriptar($arrayParams['numeroCtaTarjeta']);
                if($strNumeroCtaTarjeta)
                {
                    $objInfoContratoFormaPago->setNumeroCtaTarjeta($strNumeroCtaTarjeta);
                }
                else
                {
                    throw new \Exception('No se pudo generar el Cambio de Razon Social por Login - '
                                    . 'No fue posible guardar el numero de cuenta/tarjeta ' . $arrayParams['numeroCtaTarjeta']);
                }
                if($objAdmiBancoTipoCuenta->getEsTarjeta() == 'S')
                {
                    $objInfoContratoFormaPago->setAnioVencimiento($arrayParams['anioVencimiento']);
                    $objInfoContratoFormaPago->setMesVencimiento($arrayParams['mesVencimiento']);
                    $objInfoContratoFormaPago->setCodigoVerificacion($arrayParams['codigoVerificacion']);
                }
                $objInfoContratoFormaPago->setTitularCuenta($arrayParams['titularCuenta']);
                $objInfoContratoFormaPago->setTipoCuentaId($objAdmiTipoCuenta);
                $objInfoContratoFormaPago->setBancoTipoCuentaId($objAdmiBancoTipoCuenta);
                $objInfoContratoFormaPago->setFeCreacion($strFechaCreacion);
                $objInfoContratoFormaPago->setUsrCreacion($arrayParams['strUsrCreacion']);
                $objInfoContratoFormaPago->setIpCreacion($arrayParams['strClientIp']);
                $objInfoContratoFormaPago->setEstado($strEstadoActivo);
                $this->emcom->persist($objInfoContratoFormaPago);
            }   
        }     
        //Guardo relacion de los Puntos Origen del Cambio de Razon Social con la PERSONA EMPRESA ROL destino.                               
        foreach($arrayPuntosSeleccionados as $key => $value)
        {
            $objInfoPuntoOrigen = $this->emcom->getRepository('schemaBundle:InfoPunto')->find($value);
            if($objInfoPuntoOrigen)
            {
                $objInfoPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                $objInfoPersonaEmpresaRolCarac->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                $objInfoPersonaEmpresaRolCarac->setCaracteristicaId($objAdmiCaracteristica);
                $objInfoPersonaEmpresaRolCarac->setValor($objInfoPuntoOrigen->getId());
                $objInfoPersonaEmpresaRolCarac->setFeCreacion($strFechaCreacion);
                $objInfoPersonaEmpresaRolCarac->setUsrCreacion($arrayParams['strUsrCreacion']);
                $objInfoPersonaEmpresaRolCarac->setIpCreacion($arrayParams['strClientIp']);
                $objInfoPersonaEmpresaRolCarac->setEstado($strEstadoActivo);
                $this->emcom->persist($objInfoPersonaEmpresaRolCarac);

                // LOGICA ECDF
                if($strTieneCorreoElectronico !== "")
                {
                    // Obtengo los servicios Ligados al Punto que seran trasladados
                    $arrayInfoServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                    ->findServiciosPorEmpresaPorPunto($arrayParams['strCodEmpresa'], 
                                                                                    $objInfoPuntoOrigen->getId(), 
                                                                                    99999999, 1, 0);
                        
                    $objInfoServicio   = $arrayInfoServicio['registros'];

                    foreach($objInfoServicio as $objServ)
                    {                   
                        if($objServ->getEstado() == 'Activo')
                        {
                          $objProductoVeri = $objServ->getProductoId();
                          if(isset($arrayParams['strPrefijoEmpresa']) && !empty($arrayParams['strPrefijoEmpresa'])
                          && ($arrayParams['strPrefijoEmpresa'] == 'MD' || $arrayParams['strPrefijoEmpresa'] == 'EN' ) && is_object($objProductoVeri))
                          {
                              $strNombreTecnico = $objProductoVeri->getNombreTecnico();
                              if($strNombreTecnico === "ECDF")
                              {
                                  $objServProdCaractCorreoPendienteCRS = $this->serviceServicioTecnico
                                                                              ->getServicioProductoCaracteristica(
                                                                                  $objServ,
                                                                                  'CORREO ELECTRONICO',
                                                                                  $objServ->getProductoId(),
                                                                                  array('strEstadoSpc' => 'tmpPendienteCRS'));

                                  if(is_object($objServProdCaractCorreoPendienteCRS))
                                  { 
                                      $objServProdCaractCorreoPendienteCRS->setEstado("Eliminado");
                                      $objServProdCaractCorreoPendienteCRS->setFeUltMod(new \DateTime('now'));
                                      $objServProdCaractCorreoPendienteCRS->setUsrUltMod($arrayParams['strUsrCreacion']);
                                      $this->emcom->persist($objServProdCaractCorreoPendienteCRS);
                                      $this->emcom->flush();
                                  }
                                  if($strTieneCorreoElectronico === "SI")
                                  {
                                      if(!isset($arrayParams["correoElectronico"]) 
                                      || empty($arrayParams["correoElectronico"]))
                                      {
                                        throw new \Exception("Debes ingresar un correo válido");
                                      }
                                      $objServProdCaractCorreo = $this->serviceServicioTecnico
                                                                      ->getServicioProductoCaracteristica(
                                                                          $objServ,
                                                                          'CORREO ELECTRONICO',
                                                                          $objServ->getProductoId());

                                      if(is_object($objServProdCaractCorreo))
                                      {
                                          $objServicioProdCaract = new InfoServicioProdCaract();
                                          $objServicioProdCaract = clone $objServProdCaractCorreo;
                                          $objServicioProdCaract->setServicioId($objServ->getId());
                                          $objServicioProdCaract->setFeCreacion(new \DateTime('now'));
                                          $objServicioProdCaract->setUsrCreacion($arrayParams['strUsrCreacion']);
                                          $objServicioProdCaract->setValor($arrayParams["correoElectronico"]);
                                          $objServicioProdCaract->setEstado("tmpPendienteCRS");
                                          $this->emcom->persist($objServicioProdCaract);
                                          $this->emcom->flush();
                                      }
                                  }
                              }
                          }
                        }
                    }
                }
            }
        }
        $this->emcom->flush();
 

        //Guardo files asociados al contrato
        if(($arrayParams["strCodEmpresa"] != 18 && $arrayParams["strCodEmpresa"] != 33) || $intContratoFisico == 1)
        {                      
            $arrayDatosFormFiles = $arrayParams['arrayDatosFormFiles'];
            $arrayTipoDocumentos = $arrayParams['arrayTipoDocumentos'];
            $i = 0;

            foreach($arrayDatosFormFiles as $key => $imagenes)
            {
                foreach($imagenes as $key_imagen => $value)
                {
                    if($value)
                    {
                        $objInfoDocumento = new InfoDocumento();
                        $objInfoDocumento->setFile($value);
                        $objInfoDocumento->setNombreDocumento("documento_digital");
                        $objInfoDocumento->setUploadDir(substr($this->fileRoot, 0, -1));
                        $objInfoDocumento->setFechaDocumento($strFechaCreacion);
                        $objInfoDocumento->setUsrCreacion($arrayParams['strUsrCreacion']);
                        $objInfoDocumento->setFeCreacion($strFechaCreacion);
                        $objInfoDocumento->setIpCreacion($arrayParams['strClientIp']);
                        $objInfoDocumento->setEstado($strEstadoActivo);
                        $objInfoDocumento->setMensaje("Archivo agregado al contrato # " . $strNumeroContrato);
                        $objInfoDocumento->setEmpresaCod($arrayParams['strCodEmpresa']);

                        $objTipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                ->find($arrayTipoDocumentos[$key_imagen]);
                        if($objTipoDocumentoGeneral != null)
                        {
                            $objInfoDocumento->setTipoDocumentoGeneralId($objTipoDocumentoGeneral->getId());
                        }
                        $i++;
                        if($objInfoDocumento->getFile())
                        {
                            $objInfoDocumento->preUpload();
                            $strNombreArchivo = $objInfoDocumento->getUbicacionLogicaDocumento();
                                $strNombreApp     = 'TelcosWeb';
                                $arrayPathAdicional = [];
                                $strSubModulo = "ContratoDocumentoDigital";
                                
                                $arrayParamNfs          = array(
                                    'prefijoEmpresa'       => $arrayParams['strPrefijoEmpresa'],
                                    'strApp'               => $strNombreApp,
                                    'strSubModulo'         => $strSubModulo,
                                    'arrayPathAdicional'   => $arrayPathAdicional,
                                    'strBase64'            => base64_encode(file_get_contents($objInfoDocumento->getFile())),
                                    'strNombreArchivo'     => $strNombreArchivo,
                                    'strUsrCreacion'       => $arrayParams['strUsrCreacion']);
                                $arrayRespNfs = $this->utilService->guardarArchivosNfs($arrayParamNfs);
                
                                if(isset($arrayRespNfs))
                                {
                                    if($arrayRespNfs['intStatus'] == 200)
                                    {
                                        $strNuevoNombreArchivo = $objInfoDocumento->getNombreDocumento().'.'.$objInfoDocumento->getExtension();
                                        $objInfoDocumento->setNombreDocumento($strNuevoNombreArchivo);
                                        $objInfoDocumento->setFile(null);
                                        $objInfoDocumento->setUbicacionFisicaDocumento($arrayRespNfs['strUrlArchivo']);

                                    }
                                    else
                                    {
                                        throw new \Exception('No se pudo crear el contrato, error al cargar el archivo digital');
                                    }
                                }
                                else
                                {
                                    throw new \Exception('Error, no hay respuesta del WS para almacenar el documento');
                                }
                        }
                        if($objInfoDocumento->getExtension() != null && $objInfoDocumento->getExtension() != '')
                        {
                            $objTipoDocumento = $this->emcom->getRepository('schemaBundle:AdmiTipoDocumento')
                                                ->findOneByExtensionTipoDocumento(strtoupper($objInfoDocumento->getExtension()));

                            if($objTipoDocumento != null)
                            {
                                $objInfoDocumento->setTipoDocumentoId($objTipoDocumento);
                            }
                            else
                            {
                                if($objInfoDocumento->getExtension() != null)
                                {
                                    // Inserto registro con la extension del archivo a subirse
                                    $objAdmiTipoDocumento = new AdmiTipoDocumento();
                                    $objAdmiTipoDocumento->setExtensionTipoDocumento(strtoupper($objInfoDocumento->getExtension()));
                                    $objAdmiTipoDocumento->setTipoMime(strtoupper($objInfoDocumento->getExtension()));
                                    $objAdmiTipoDocumento->setDescripcionTipoDocumento('ARCHIVO FORMATO '
                                                                                    . strtoupper($objInfoDocumento->getExtension()));
                                    $objAdmiTipoDocumento->setEstado($strEstadoActivo);
                                    $objAdmiTipoDocumento->setUsrCreacion($arrayParams['strUsrCreacion']);
                                    $objAdmiTipoDocumento->setFeCreacion($strFechaCreacion);
                                    $this->emcom->persist($objAdmiTipoDocumento);
                                    $this->emcom->flush();
                                    $objInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);
                                }
                            }
                        }
                        $objInfoDocumento->setContratoId($objInfoContrato->getId());
                        $this->emcom->persist($objInfoDocumento);
                        $this->emcom->flush();

                        $objInfoDocumentoRelacion = new InfoDocumentoRelacion();
                        $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());
                        $objInfoDocumentoRelacion->setModulo('COMERCIAL');
                        $objInfoDocumentoRelacion->setContratoId($objInfoContrato->getId());
                        $objInfoDocumentoRelacion->setEstado($strEstadoActivo);
                        $objInfoDocumentoRelacion->setFeCreacion($strFechaCreacion);
                        $objInfoDocumentoRelacion->setUsrCreacion($arrayParams['strUsrCreacion']);
                        $this->emcom->persist($objInfoDocumentoRelacion);
                        $this->emcom->flush();
                        if($objTipoDocumento)
                        {
                            $strDescripcionTipoDocumentoTrim = trim($objTipoDocumento->getDescripcionTipoDocumento());
                            if($strDescripcionTipoDocumentoTrim  === "ARCHIVO DE IMAGEN")
                            {
                                $arrayNombreDocumento       = explode(".",$objInfoDocumento->getPath());
                                $strUsrCreacion             = $arrayParams['strUsrCreacion'];
                                $strNombreDocumento         = $arrayNombreDocumento[0].".pdf";
                                $strNumeroContrato          = $objInfoContrato->getNumeroContrato();
                                $strUbiFisicaDoc            = $objInfoDocumento->getUbicacionFisicaDocumento();
                                $objFechaCreacion           = new \DateTime('now');
                                $strClientIp                = $arrayParams['strClientIp'];
                                $strMensaje                 = "Archivo agregado al contrato # ".$strNumeroContrato;
                                $strCodEmpresa              = $arrayParams['strCodEmpresa'];
                                $strPrefijoEmpresa          = $arrayParams['strPrefijoEmpresa'];
                                $intTipoDocumentoGeneralId  = $objTipoDocumentoGeneral->getId();
                                $strNumeroAdendum           = isset($arrayParams['strNumeroAdendum'] ) ? $arrayParams['strNumeroAdendum'] : '';

                                $objImagick = new Imagick($strUbiFisicaDoc);
                                if($objImagick)
                                {
                                    $objImagick->setImageFormat('pdf');
                                    
                                    $objImgBlob = $objImagick->getimageblob();

                                    $objInfDocumento = new InfoDocumento();    
                                    $objInfDocumento->setNombreDocumento($strNombreDocumento);
                                    $objInfDocumento->setUbicacionLogicaDocumento($strNombreDocumento);
                                    $objInfDocumento->setFechaDocumento( $objFechaCreacion );
                                    $objInfDocumento->setUsrCreacion( $strUsrCreacion );
                                    $objInfDocumento->setFeCreacion( $objFechaCreacion );
                                    $objInfDocumento->setIpCreacion( $strClientIp );
                                    $objInfDocumento->setEstado( 'Activo' );             
                                    $objInfDocumento->setMensaje( $strMensaje );          
                                    $objInfDocumento->setEmpresaCod( $strCodEmpresa );
                                    $objInfDocumento->setTipoDocumentoGeneralId($intTipoDocumentoGeneralId);
                                    
                                    $arrayPathAdicional = [];     
                                    
                                    $arrayParamNfs          = array(
                                        'prefijoEmpresa'       => $strPrefijoEmpresa,
                                        'strApp'               => $strNombreApp,
                                        'strSubModulo'         => $strSubModulo,
                                        'arrayPathAdicional'   => $arrayPathAdicional,
                                        'strBase64'            => base64_encode($objImgBlob),
                                        'strNombreArchivo'     => $strNombreDocumento,
                                        'strUsrCreacion'       => $strUsrCreacion);
                                    $arrayRespNfs = $this->utilService->guardarArchivosNfs($arrayParamNfs);

                                    if(isset($arrayRespNfs))
                                    {
                                        if($arrayRespNfs['intStatus'] == 200)
                                        {
                                            $objInfDocumento->setUbicacionFisicaDocumento($arrayRespNfs['strUrlArchivo']);

                                        }
                                        else
                                        {
                                            throw new \Exception('No se pudo crear el contrato, error al cargar el archivo digital');
                                        }
                                    }
                                    else
                                    {
                                        throw new \Exception('Error, no hay respuesta del WS para almacenar el documento');
                                    }

                                    $objTipoDoc=$this->emcom->getRepository('schemaBundle:AdmiTipoDocumento')->findOneByExtensionTipoDocumento('PDF');

                                    if($objTipoDoc)
                                    {
                                        $objInfDocumento->setTipoDocumentoId($objTipoDoc);  
                                    }

                                    $objInfDocumento->setContratoId($objInfoContrato->getId());
                                    $this->emcom->persist($objInfDocumento);
                                    $this->emcom->flush();   

                                    $objInfoDocRelacion = new InfoDocumentoRelacion(); 
                                    $objInfoDocRelacion->setDocumentoId($objInfDocumento->getId());
                                    $objInfoDocRelacion->setModulo('COMERCIAL'); 
                                    $objInfoDocRelacion->setContratoId($objInfoContrato->getId());        
                                    $objInfoDocRelacion->setEstado('Activo');
                                    $objInfoDocRelacion->setFeCreacion($objFechaCreacion);
                                    $objInfoDocRelacion->setUsrCreacion($strUsrCreacion);
                                    $objInfoDocRelacion->setNumeroAdendum($strNumeroAdendum);
                                    $this->emcom->persist($objInfoDocRelacion);
                                    $this->emcom->flush();

                                }
                            }                                                
                        }

                    }
                }
            } // fin foreach($datos_form_files as $key => $imagenes)  
        }  
        $objResponse['objInfoPersonaEmpresaRol'] = $objInfoPersonaEmpresaRol; 
        }
        catch(\Exception $e)
        { 
            $this->utilService->insertError('Telcos+', 
                                            'cambioRazonSocialClienteNuevo', 
                                            $e->getMessage(), 
                                            $arrayParams['strUsrCreacion'], 
                                            $arrayParams['strClientIp']
            );

            $objResponse['strError'] = $e->getMessage(); 
            $objResponse['objInfoPersonaEmpresaRol'] =  null;    
        }
 

        return $objResponse; 
    }
    
    
    /** 
     * Descripcion: Metodo que modifica campos de InfoContrato según datos enviados por parametros.
     * @author Andrés Montero <amontero@telconet.ec>
     * @param arrayParametros[
     *     intIdContrato => id del contrato que se desea setear
     *     strOrigen     => origen del contrato puede ser WEB o MOVIL
     * ]
     * @throws Exception
     * @version 1.0 10-02-2017
     * @return $objContrato object
     */
    public function setearDatosContrato($arrayParametros)
    {
        $this->emcom->getConnection()->beginTransaction();
        try
        {
            $objContrato = $this->emcom->getRepository('schemaBundle:InfoContrato')->findOneById($arrayParametros['intIdContrato']);
            if(is_object($objContrato))
            {
                if(isset($arrayParametros['strOrigen']) && !empty($arrayParametros['strOrigen']))
                {
                    $objContrato->setOrigen($arrayParametros['strOrigen']);
                }
                $this->emcom->persist($objContrato);
                $this->emcom->flush();
                
                $objPersonaEmpresaRolCliente = $objContrato->getPersonaEmpresaRolId();
                if(is_object($objPersonaEmpresaRolCliente) && isset($arrayParametros['strOrigen']) && !empty($arrayParametros['strOrigen']) &&
                   'MOVIL' === strtoupper($arrayParametros['strOrigen']))
                {
                    // =================================================================================
                    // [CREATE] - Se crea el historial del Info Persona Empresa Rol Cliente con el pin y
                    // el telefono de autorizacion con su numero de contrato
                    // =================================================================================
                    $objPersonaEmpresaRolHistCliente = new InfoPersonaEmpresaRolHisto();
                    $objPersonaEmpresaRolHistCliente->setEstado($objPersonaEmpresaRolCliente->getEstado());
                    $objPersonaEmpresaRolHistCliente->setFeCreacion(new \DateTime('now'));
                    $objPersonaEmpresaRolHistCliente->setIpCreacion($arrayParametros['strIpCreacion']);
                    $objPersonaEmpresaRolHistCliente->setPersonaEmpresaRolId($objPersonaEmpresaRolCliente);
                    $objPersonaEmpresaRolHistCliente->setUsrCreacion('telcos_contrato');
                    $objPersonaEmpresaRolHistCliente->setObservacion($arrayParametros['strObservacionHistorial']);
                    $this->emcom->persist($objPersonaEmpresaRolHistCliente);
                    $this->emcom->flush();
                }
            }
            
            $this->emcom->commit();
            
        }
        catch(\Exception $e)
        {
            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
            }
            $this->emcom->getConnection()->close();
        }
        return $objContrato;
    }

   /* Documentación crearAdendum.- Permite guardar un Adendum de contrato sea por punto o por servicio
    * @parameters $arrayParametrosContrato[
    *                                      'strCodEmpresa'     => 'Código de la Empresa',
    *                                      'strPrefijoEmpresa' => 'Prefijo de la Empresa',
    *                                      'strIdOficina'      => 'id de la oficina donde se origina el adendum',
    *                                      'strUsrCreacion'    => 'Usuario de creación del adendum',
    *                                      'strClientIp'       => 'ip del usuario',
    *                                      'intIdContrato'     => 'id del contrato',
    *                                      'intIdPunto'        => 'id del punto al que se va a realizar el adendum', 
    *                                      'strOrigen'         => 'Origen del Adendum WEB o MOVIL',
    *                                      'arrayServicios'    => ['id de los servicios que pertenecen al adendum'],
    *                                      'strTipo'           => 'Tipo de adendum AS) Adendum de Servicio, AP) Adendum de Punto' 
    *                                     ]
    * 
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.0 24-10-2019
    *
    * @author Walther Joao Gaibor <wgaibor@telconet.ec>
    * @version 1.1 - 21-10-2020 - Almacenar los archivos generados por adendum en el nfs remoto.
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.1 10-06-2021 - Se corrige para que la numeración no genere bloqueo y para que no se duplique el numero de adendum
    */
    public function crearAdendum($arrayParametrosContrato)
    {
        $strCodEmpresa     = $arrayParametrosContrato['strCodEmpresa'];
        $strPrefijoEmpresa = $arrayParametrosContrato['strPrefijoEmpresa'];
        $intIdOficina      = $arrayParametrosContrato['intIdOficina']; 
        $strUsuario        = $arrayParametrosContrato['strUsrCreacion'];
        $strClientIp       = $arrayParametrosContrato['strClientIp']; 
        $intIdContrato     = $arrayParametrosContrato['intIdContrato'];
        $arrayServicios    = $arrayParametrosContrato['arrayServicios'];
        $strTipo           = $arrayParametrosContrato['strTipo'];
        $strCambiaFP       = $arrayParametrosContrato['strCambioNumeroTarjeta'];
        $arrayDatosForm    = $arrayParametrosContrato['arrayDatosForm'];
        $intPersEmprRol    = $arrayParametrosContrato['intPersEmprRol'];
        $arrayPromocion    = $arrayParametrosContrato['arrayPromocion'];

        try
        {  
             //Obtener la numeracion de la tabla Admi_numeracion
             $strCodigoNumeracion   = $strTipo == 'AS' ? 'CONA' : 'CON';
             $objDatosNumeracion    = $this->emComercial->getRepository('schemaBundle:AdmiNumeracion')
                                                   ->findByEmpresaYOficina($strCodEmpresa,$intIdOficina,$strCodigoNumeracion);
             $strNumeroAdendum = "";
             if( $objDatosNumeracion )
             {
                 $intSecuencia     = str_pad($objDatosNumeracion->getSecuencia(),7, "0", STR_PAD_LEFT);
                 $strNumeroAdendum = $objDatosNumeracion->getNumeracionUno()."-".$objDatosNumeracion->getNumeracionDos()."-".$intSecuencia;
                 //Actualizo la numeracion en la tabla
                 $intSecuencia = ($objDatosNumeracion->getSecuencia()+1);
                 $objDatosNumeracion->setSecuencia($intSecuencia);
                 $this->emComercial->persist($objDatosNumeracion);
                 $this->emComercial->flush();
             } 
             else 
             {
                 throw new Exception("No se pudo obtener la numeración", 206);
                 
             }
             $this->emComercial->getConnection()->beginTransaction();
             $this->emComunicacion->getConnection()->beginTransaction();             
             //valido la forma de pago
             $objForma   = $this->emcom->getRepository('schemaBundle:AdmiFormaPago')->find($arrayDatosForm['formaPagoId']);
             if (!$objForma)
             {
                 throw new \Exception('No se pudo guardar el Adendum - No existe forma de pago');
             }

             if ($objForma->getCodigoFormaPago()=='DEB' || $objForma->getCodigoFormaPago()=='TARC')
             {
                 if (!isset($arrayDatosForm['tipoCuentaId']) || empty($arrayDatosForm['tipoCuentaId']))
                 {
                     throw new \Exception('No se pudo guardar el Adendum - El tipo de Cuenta / Tarjeta es un campo obligatorio', 206); 
                 }
                 if (!isset($arrayDatosForm['bancoTipoCuentaId']) || empty($arrayDatosForm['bancoTipoCuentaId']))
                 {
                     throw new \Exception('No se pudo guardar el Adendum - El banco es un campo obligatorio', 206); 
                 }
                 if (!isset($arrayDatosForm['numeroCtaTarjeta']) || empty($arrayDatosForm['numeroCtaTarjeta']))
                 {
                     throw new \Exception('No se pudo guardar el Adendum - El Numero de Cuenta / Tarjeta es un campo obligatorio', 206); 
                 }
                  //Llamo a funcion para validar numero de cuenta/tarjeta            
                 if ($strCambiaFP == "S")
                 {
                    $strKeyError = "";
                    $arrayParametrosValidaCtaTarj                          = array();
                    $arrayParametrosValidaCtaTarj['intTipoCuentaId']       = $arrayDatosForm['tipoCuentaId'];
                    $arrayParametrosValidaCtaTarj['intBancoTipoCuentaId']  = $arrayDatosForm['bancoTipoCuentaId'];
                    $arrayParametrosValidaCtaTarj['strNumeroCtaTarjeta']   = $arrayDatosForm['numeroCtaTarjeta'];
                    $arrayParametrosValidaCtaTarj['strCodigoVerificacion'] = $arrayDatosForm['codigoVerificacion'];
                    $arrayParametrosValidaCtaTarj['intFormaPagoId']        = $arrayDatosForm['formaPagoId'];
                    $arrayParametrosValidaCtaTarj['strCodEmpresa']         = $strCodEmpresa;
                    $arrayParametrosValidaCtaTarj['strKeyError']           = $strKeyError;
                    $arrayValidaciones   = $this->validarNumeroTarjetaCta($arrayParametrosValidaCtaTarj);
                    $strError = "";
                    if($arrayValidaciones)
                    {    
                        foreach($arrayValidaciones as $strKey => $mensaje_validaciones)
                        {
                            $strKeyError = $strKey;
                            foreach($mensaje_validaciones as $strKeyMsj => $value)
                            {
                                $strKeyError = $strKeyMsj;
                                $strError = $strError.$value.".\n";                        
                            }
                        }
                        throw new \Exception("No se pudo guardar el Adendum - " . $strError, 206);
                    }
   
                 }
                 $objBancoTipoCta = $this->emcom->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($arrayDatosForm['bancoTipoCuentaId']);
                 if($objBancoTipoCta->getEsTarjeta() == 'S' && (!$arrayDatosForm['mesVencimiento'] || !$arrayDatosForm['anioVencimiento']))
                 {
                    throw new \Exception('No se pudo guardar el Adendum - '
                        . 'El Anio y mes de Vencimiento de la tarjeta son campos obligatorios', 206); 
                 } 
                 //Llamo a funcion que realiza encriptado del numero de cuenta
                 $strNumeroCtaTarjeta = $arrayDatosForm['numeroCtaTarjeta'];
                 
                 if ($strCambiaFP == "S")
                 {
                    $strNumeroCtaTarjeta = $this->serviceCrypt->encriptar($arrayDatosForm['numeroCtaTarjeta']);
                    if(empty($strNumeroCtaTarjeta))
                    {
                        throw new \Exception("No se pudo guardar el Adendum - Numero de cuenta/tarjeta es incorrecto", 206);
                    }   
                 }
                 
 
                 if (!isset($arrayDatosForm['titularCuenta']) || empty($arrayDatosForm['titularCuenta']))
                 {
                     throw new \Exception('No se pudo guardar el Adenudm - El titular de cuenta es un campo obligatorio', 206);
                 }
             }              

             foreach ($arrayServicios as $intServicio)
             {
                 $entityAdendum = $this->emComercial->getRepository('schemaBundle:InfoAdendum')
                                               ->findOneBy(array(
                                                                 "contratoId" => null,
                                                                 "puntoId"    => $arrayDatosForm['puntoId'],
                                                                 "servicioId" => $intServicio
                                                           ));
                 if ($entityAdendum)
                 {

                    $entityAdendum->setContratoId($intIdContrato);
                    $entityAdendum->setNumero($strNumeroAdendum);
                    $entityAdendum->setTipo($strTipo);
                    $entityAdendum->setFormaPagoId($arrayDatosForm['formaPagoId']);
                    $entityAdendum->setTipoCuentaId($arrayDatosForm['tipoCuentaId']);
                    $entityAdendum->setBancoTipoCuentaId($arrayDatosForm['bancoTipoCuentaId']);
                    $entityAdendum->setNumeroCtaTarjeta($strNumeroCtaTarjeta);
                    $entityAdendum->setTitularCuenta($arrayDatosForm['titularCuenta']);
                    $entityAdendum->setMesVencimiento($arrayDatosForm['mesVencimiento']);
                    $entityAdendum->setAnioVencimiento($arrayDatosForm['anioVencimiento']);
                    $entityAdendum->setCodigoVerificacion($arrayDatosForm['codigoVerificacion']);
                    $entityAdendum->setUsrModifica($strUsuario);
                    $entityAdendum->setFeModifica(new \DateTime('now'));
                    $entityAdendum->setFeCreacion(new \DateTime('now'));
                    $entityAdendum->setEstado("PorAutorizar");

                    //valido si el servicio paso por factibilidad manual
                    $entityServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                    ->find($entityAdendum->getServicioId());
                    if ($entityServicio->getPlanId())
                    {
                        $entityServicioHis = $this->emcom->getRepository('schemaBundle:InfoServicioHistorial')
                                                            ->findOneBy(array("servicioId" => $entityAdendum->getServicioId(),
                                                                            "estado" => 'PreFactibilidad'));
                        if ($entityServicioHis)
                        {
                            $objFechaPreFactibilidad = $entityServicioHis->getFeCreacion();

                            //valido que la fecha mas los dias de parametro no sea mayor que la fecha actual
                            $arrayParametrosDet     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('DIAS_ESPERA_FACTIBILIDAD_MANUAL',
                                        'COMERCIAL', 
                                        '', '', '', '', '', '', '', 
                                    ''); 
                            if ($arrayParametrosDet)
                            {
                                $objFecha      = new \DateTime('now');
                                $objFechaPreFactibilidad->add(new \DateInterval("P".$arrayParametrosDet['valor1']."D"));
                                if ($objFecha < $objFechaPreFactibilidad)
                                {
                                    $objFechaPreFactibilidad->sub(new \DateInterval("P".$arrayParametrosDet['valor1']."D"));
                                    $entityAdendum->setFeCreacion($entityServicioHis->getFeCreacion());
                                }
                            }                                   
                        }                                        
                    }                          
                    $this->emComercial->persist($entityAdendum);
 
                 }
            }
            $this->emComercial->flush();
            $boolBandNfs = isset($arrayParametrosContrato['bandNfs']) ? $arrayParametrosContrato['bandNfs'] : 0;
            if(!$boolBandNfs && $this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->commit();
            }

            //Guardo files asociados al contrato                      
            $arrayDatosFormFiles = $arrayDatosForm['datos_form_files'];
            $arrayTipoDocumentos = $arrayDatosForm['arrayTipoDocumentos'];                                    
            $intI = 0;
            if(isset($arrayParametrosContrato['bandNfs']) && $arrayParametrosContrato['bandNfs'])
            {
                $objPerEmpRol        = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                   ->find($intPersEmprRol);
                $arrayParmGuardarNFS = array('arrayDatosFormFiles' => $arrayDatosFormFiles,
                                             'arrayFileBase64'     => $arrayDatosForm['files'],
                                             'arrayTipoDocumentos' => $arrayTipoDocumentos,
                                             'strUsuario'          => $strUsuario,
                                             'prefijoEmpresa'      => $strPrefijoEmpresa,
                                             'strApp'              => $arrayParametrosContrato['strApp'],
                                             'bandNfs'             => $arrayParametrosContrato['bandNfs'],
                                             'objPerEmpRol'        => $objPerEmpRol
                                            );
                $arrayRespuestaNfs = $this->guardarArchivoNFS($arrayParmGuardarNFS);
                if(isset($arrayRespuestaNfs) && !empty($arrayRespuestaNfs))
                {
                    if ($this->emcom->getConnection()->isTransactionActive())
                    {
                        $this->emcom->getConnection()->commit();
                    }
                    foreach($arrayRespuestaNfs as $arrayValor)
                    {
                        $objInfoDocumento = new InfoDocumento();
                        $objInfoDocumento->setNombreDocumento($arrayValor['strNombreArchivo']);
                        $objInfoDocumento->setUbicacionLogicaDocumento($arrayValor['strNombreArchivo']);
                        $objInfoDocumento->setFechaDocumento(new \DateTime('now'));
                        $objInfoDocumento->setUsrCreacion($strUsuario);
                        $objInfoDocumento->setFeCreacion(new \DateTime('now'));
                        $objInfoDocumento->setIpCreacion($strClientIp);
                        $objInfoDocumento->setEstado('Activo');
                        $objInfoDocumento->setMensaje("Archivo agregado al Adendum # "
                                                        . $strNumeroAdendum );
                        $objInfoDocumento->setEmpresaCod($strCodEmpresa);
                        $objInfoDocumento->setPath($arrayValor['strUrl']);
                        $objInfoDocumento->setFile(null);
                        $objInfoDocumento->setUbicacionFisicaDocumento($arrayValor['strUrl']);


                        if(isset($arrayValor['strTipoDocGeneral']))
                        {
                            $objTipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                                       ->find($arrayValor['strTipoDocGeneral']);
                            if( $objTipoDocumentoGeneral != null )
                            {
                                $objInfoDocumento->setTipoDocumentoGeneralId( $objTipoDocumentoGeneral->getId() );
                            }
                        }

                        $objTipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                                 ->findOneByExtensionTipoDocumento(strtoupper($arrayValor['strTipoDocumento']));
                        if( $objTipoDocumento != null)
                        {
                            $objInfoDocumento->setTipoDocumentoId($objTipoDocumento);
                        }
                        else
                        {
                            $objAdmiTipoDocumento = new AdmiTipoDocumento();
                            $objAdmiTipoDocumento->setExtensionTipoDocumento(strtoupper($arrayValor['strTipoDocumento']));
                            $objAdmiTipoDocumento->setTipoMime(strtoupper($arrayValor['strTipoDocumento']));
                            $objAdmiTipoDocumento->setDescripcionTipoDocumento('ARCHIVO FORMATO '.strtoupper($arrayValor['strTipoDocumento']));
                            $objAdmiTipoDocumento->setEstado('Activo');
                            $objAdmiTipoDocumento->setUsrCreacion($usrCreacion);
                            $objAdmiTipoDocumento->setFeCreacion(new \DateTime('now'));
                            $this->emComunicacion->persist($objAdmiTipoDocumento);
                            $this->emComunicacion->flush();
                            $objInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);
                        }
                        $objInfoDocumento->setContratoId($intIdContrato);
                        $this->emComunicacion->persist($objInfoDocumento);
                        $this->emComunicacion->flush();

                        $objInfoDocumentoRelacion = new InfoDocumentoRelacion();
                        $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());
                        $objInfoDocumentoRelacion->setModulo('COMERCIAL');
                        $objInfoDocumentoRelacion->setContratoId($intIdContrato);
                        $objInfoDocumentoRelacion->setEstado('Activo');
                        $objInfoDocumentoRelacion->setFeCreacion(new \DateTime('now'));
                        $objInfoDocumentoRelacion->setUsrCreacion($strUsuario);
                        $objInfoDocumentoRelacion->setNumeroAdendum($strNumeroAdendum);
                        $this->emComunicacion->persist($objInfoDocumentoRelacion);
                        $this->emComunicacion->flush();
                    }
                }
            }
            else
            {
                foreach ($arrayDatosFormFiles as $arrayImagenes)
                {
                    foreach ( $arrayImagenes as $intkeyImagen => $strValue)
                    {
                        if($strValue)
                        {
                            $objInfoDocumento = new InfoDocumento();
                            $objInfoDocumento->setFile( $strValue );
                            $objInfoDocumento->setNombreDocumento("documento_digital");
                            $objInfoDocumento->setUploadDir(substr($this->fileRoot, 0, -1));
                            $objInfoDocumento->setFechaDocumento( new \DateTime('now') );
                            $objInfoDocumento->setUsrCreacion( $strUsuario );
                            $objInfoDocumento->setFeCreacion( new \DateTime('now') );
                            $objInfoDocumento->setIpCreacion( $strClientIp );
                            $objInfoDocumento->setEstado( 'Activo' );
                            $objInfoDocumento->setMensaje( "Archivo agregado al Adendum # " . $strNumeroAdendum );
                            $objInfoDocumento->setEmpresaCod( $strCodEmpresa );

                            $objTipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                                    ->find($arrayTipoDocumentos[$intkeyImagen]);
                            if( $objTipoDocumentoGeneral != null )
                            {
                                $objInfoDocumento->setTipoDocumentoGeneralId( $objTipoDocumentoGeneral->getId() );
                            }
                            $intI++;
                            if ( $objInfoDocumento->getFile() )
                            {
                                $objInfoDocumento->preUpload();
                                $objInfoDocumento->upload();
                            }

                            $objTipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                                    ->findOneByExtensionTipoDocumento(strtoupper($objInfoDocumento->getExtension()));

                            if( $objTipoDocumento != null)
                            {
                                $objInfoDocumento->setTipoDocumentoId($objTipoDocumento);
                            }
                            else
                            {
                                $objAdmiTipoDocumento = new AdmiTipoDocumento();
                                $objAdmiTipoDocumento->setExtensionTipoDocumento(strtoupper($objInfoDocumento->getExtension()));
                                $objAdmiTipoDocumento->setTipoMime(strtoupper($objInfoDocumento->getExtension()));
                                $objAdmiTipoDocumento->setDescripcionTipoDocumento('ARCHIVO FORMATO '.strtoupper($objInfoDocumento->getExtension()));
                                $objAdmiTipoDocumento->setEstado('Activo');
                                $objAdmiTipoDocumento->setUsrCreacion( $strUsuario );
                                $objAdmiTipoDocumento->setFeCreacion( new \DateTime('now') );
                                $this->emComunicacion->persist( $objAdmiTipoDocumento );
                                $this->emComunicacion->flush();
                                $objInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);
                            }
                            $objInfoDocumento->setContratoId( $intIdContrato );
                            $this->emComunicacion->persist($objInfoDocumento);
                            $this->emComunicacion->flush();
                            $objInfoDocumentoRelacion = new InfoDocumentoRelacion();
                            $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());
                            $objInfoDocumentoRelacion->setModulo('COMERCIAL');
                            $objInfoDocumentoRelacion->setContratoId($intIdContrato);
                            $objInfoDocumentoRelacion->setEstado('Activo');
                            $objInfoDocumentoRelacion->setFeCreacion(new \DateTime('now'));
                            $objInfoDocumentoRelacion->setUsrCreacion($strUsuario);
                            $objInfoDocumentoRelacion->setNumeroAdendum($strNumeroAdendum);
                            $this->emComunicacion->persist($objInfoDocumentoRelacion);
                            $this->emComunicacion->flush();

                            if($objTipoDocumento && "ARCHIVO DE IMAGEN" === trim($objTipoDocumento->getDescripcionTipoDocumento()))
                            {
                                    $arrayNombreDocumento = explode(".",$objInfoDocumento->getPath());
                                    $strNombreDocumento   = $arrayNombreDocumento[0].".pdf";

                                    $arrayParametros = array ();
                                    $arrayParametros['strNombreDocuemnto']        = $strNombreDocumento;
                                    $arrayParametros['strUbicacionFisicaDoc']     = $this->fileRoot.$strNombreDocumento;
                                    $arrayParametros['strDirArchivo']             = $this->path_telcos.$this->fileRoot.$objInfoDocumento->getPath();
                                    $arrayParametros['strUsrCreacion']            = $strUsuario;
                                    $arrayParametros['strClienteIp']              = $strClientIp;
                                    $arrayParametros['strMensaje']                = "Archivo agregado al Adendum # " . $strNumeroAdendum;
                                    $arrayParametros['strCodEmpresa']             = $strCodEmpresa;
                                    $arrayParametros['intTipoDocumentoGeneralId'] = $objTipoDocumentoGeneral->getId();
                                    $arrayParametros['intContratoId']             = $intIdContrato;
                                    $arrayParametros['strNumeroAdendum']          = $strNumeroAdendum;
                                    $arrayParametros['emComunicacion']            = $this->emComunicacion;

                                    if(!($this->boolGenerarArchivoPdf($arrayParametros)))
                                    {
                                        throw new \Exception('No se pudo guardar el adendum - Error al generar archivo pdf.', 206);
                                    }

                            }
                        }
                    }
                }
            }
            $arrayResponseProm = $this->servicePromocion->guardarCodigoPromocional($arrayPromocion);
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->commit();
            }
            $this->emComercial->getConnection()->close();
            $this->emComunicacion->getConnection()->close();  
            return true;
        }
        catch(\Exception $e)
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }                        
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }            
            $this->emComercial->getConnection()->close();
            $this->emComunicacion->getConnection()->close();  
            throw $e;
        }
    }
    
    /** 
     * Descripcion: Método que retorna el número de cuenta-tarjeta enviado como parámetro enmascarado.
     * arrayParametros[
     *     strNumeroCtaTarjeta   => Numero de tarjeta
     *     strCodEmpresa         => codigo de empresa
     * ]
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 01-04-2020
     * 
     */
    
    public function getNumeroTarjetaCtaEnmascarado($arrayParametros)
    {
        $strNumeroCtaTarjeta = $arrayParametros['strNumeroCtaTarjeta'];
        
        $arrayParametrosDet     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->getOne('ENMASCARA TARJETA CUENTA',
                                                           'FINANCIERO', 
                                                           '', '', '', '', '', '', '', 
                                                           $arrayParametros['strCodEmpresa']);
        if (!empty($arrayParametrosDet))
        {
            $intDigLeft  = intval($arrayParametrosDet['valor1']);
            $intDigRigth = intval($arrayParametrosDet['valor2']);
            $strCaracter = $arrayParametrosDet['valor3'];
            
            
            for($intIndice = $intDigLeft;$intIndice < (strlen($strNumeroCtaTarjeta)-$intDigRigth); $intIndice++)
            {
                $strNumeroCtaTarjeta = substr_replace($strNumeroCtaTarjeta,$strCaracter,$intIndice,1);
            }
        }

        return $strNumeroCtaTarjeta;
    }    
    
     
    
    /**     
     * Función que ejecuta facturación por cambio de forma de pago.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0  27-08-2018
     * 
     * @return $strRespuesta
     */
    public function ejecutarFacturacionCambioFormaPago($arrayParametros)
    {
        $intIdMotivo           = $arrayParametros['intIdMotivo'];
        $strIpCliente          = $arrayParametros['strIpCliente'];
        $strEmpresaCod         = $arrayParametros['strEmpresaCod']; 
        $strUsrCreacion        = $arrayParametros['strUsrCreacion'];
        $intCont               = 0;
        $strRespuesta          ='NO';
        $arrayParamUsrCreacion = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->getOne('FACTURACION_SOLICITUDES', 
                                                          'FINANCIERO',
                                                          'FACTURACION_SOLICITUDES', 
                                                          'Cambio de Forma de Pago', 
                                                          '','','','','',$strEmpresaCod);
        
        
        if (!empty($arrayParamUsrCreacion)  && isset($arrayParamUsrCreacion['valor1']) && isset($arrayParamUsrCreacion['valor5']))
        {
            $strTipoSolicitud      = $arrayParamUsrCreacion['valor1'];
            $strUsrCreacionFactura = $arrayParamUsrCreacion['valor5'];
        }        

        $arrayPtosValoresFacturar = $this->serviceInfoDocFinancieroCab->getPtosValoresFacturarByContratoId($arrayParametros);        
     
        $arrayParametrosFact                           = array();
        $arrayParametrosFact['intMotivoId']            = null;
        $arrayParametrosFact['strMsnError']            = str_pad(' ', 30); 
        $arrayParametrosFact['strEmpresaCod']          = $strEmpresaCod;
        $arrayParametrosFact['strEstadoSolicitud']     = 'Pendiente';   
        $arrayParametrosFact['strUsrCreacion']         = $strUsrCreacionFactura;
        $arrayParametrosFact['strDescTipoSolicitud']   = $strTipoSolicitud;

        try
        {           
            $objMotivoCambioFormaPago   =   $this->emcom->getRepository('schemaBundle:AdmiMotivo')
                                                        ->find($intIdMotivo); 
            
            if(is_object($objMotivoCambioFormaPago))
            {
                $arrayParametrosFact['intMotivoId'] = $objMotivoCambioFormaPago->getId();                    
            }
            
            foreach($arrayPtosValoresFacturar['arrayPtosValoresFacurar'] as $arrayPtoValorFacturar)
            {
                $floatInstalacion = $arrayPtoValorFacturar['floatValorInst'];                
                $floatSubtotal    = $arrayPtoValorFacturar['floatSubtotal'];
                $intIdPto         = $arrayPtoValorFacturar['intIdPto'];
                $strOrigen        = $arrayPtoValorFacturar['strOrigen'];
                if($strOrigen === 'CRS' || $strOrigen === 'Traslado')
                {
                    $floatInstalacion+= $arrayPtosValoresFacturar['arrayPtosValoresFacurar'][$intCont+1]['floatValorInst'];
                    $floatSubtotal   += $arrayPtosValoresFacturar['arrayPtosValoresFacurar'][$intCont+1]['floatSubtotal'];
                    unset($arrayPtosValoresFacturar['arrayPtosValoresFacurar'][$intCont+1]);
                    
                }
                $arrayServicioPreferencial = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                         ->getServicioPreferenciaByPunto(['intIdPunto' => $arrayPtoValorFacturar['intIdPto']]);
                $intIdServicioInternet = 0;
                
                if(!empty($arrayServicioPreferencial))
                {
                    $intIdServicioInternet  = $arrayServicioPreferencial[0]['ID_SERVICIO'];
                }
                $objInfoServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                               ->find($intIdServicioInternet);

                if(is_object($objInfoServicio))
                {
                    $arrayParametrosFact['strEstadoServicio'] = $objInfoServicio->getEstado();
                    $objTipoSolicitud = $this->emcom->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                    ->findOneBy(array("descripcionSolicitud" => "SOLICITUD CAMBIO FORMA PAGO",
                                                                      "estado"               => "Activo"));            


                    if(is_object($objTipoSolicitud))
                    {
                        $objDetalleSolCambFormaPago = new InfoDetalleSolicitud();
                        $objDetalleSolCambFormaPago->setServicioId($objInfoServicio);
                        $objDetalleSolCambFormaPago->setTipoSolicitudId($objTipoSolicitud);
                        $objDetalleSolCambFormaPago->setObservacion("Se crea Solicitud por Cambio de Forma de Pago.");
                        $objDetalleSolCambFormaPago->setPrecioDescuento($floatSubtotal);
                        if(is_object($objMotivoCambioFormaPago))
                        {
                            $objDetalleSolCambFormaPago->setMotivoId($objMotivoCambioFormaPago->getId());                    
                        }
                        $objDetalleSolCambFormaPago->setFeCreacion(new \DateTime('now'));
                        $objDetalleSolCambFormaPago->setUsrCreacion($strUsrCreacion);
                        $objDetalleSolCambFormaPago->setEstado('Pendiente');
                        $this->emcom->persist($objDetalleSolCambFormaPago);
                        $this->emcom->flush(); 


                        $objInfoDetalleSolFactHistorial = new InfoDetalleSolHist();
                        $objInfoDetalleSolFactHistorial->setDetalleSolicitudId($objDetalleSolCambFormaPago);
                        $objInfoDetalleSolFactHistorial->setEstado($objDetalleSolCambFormaPago->getEstado());
                        $objInfoDetalleSolFactHistorial->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolFactHistorial->setUsrCreacion($strUsrCreacion);
                        $objInfoDetalleSolFactHistorial->setObservacion("Se crea Solicitud por Cambio de Forma de Pago.");
                        $objInfoDetalleSolFactHistorial->setIpCreacion($strIpCliente);
                        $this->emcom->persist($objInfoDetalleSolFactHistorial);
                        $this->emcom->flush(); 

                        if($floatInstalacion > 0)
                        {
                            $strRespuesta = 'OK';
                            $objAdmiCaractInstalacion = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                                                                    ->findOneBy(array("descripcionCaracteristica" => "INSTALACION",
                                                                                      "estado"                    => "Activo"));

                            if(is_object($objAdmiCaractInstalacion))
                            {
                                $objSolCaractInstalacion = new InfoDetalleSolCaract();
                                $objSolCaractInstalacion->setCaracteristicaId($objAdmiCaractInstalacion);
                                $objSolCaractInstalacion->setDetalleSolicitudId($objDetalleSolCambFormaPago);
                                $objSolCaractInstalacion->setValor($floatInstalacion);
                                $objSolCaractInstalacion->setEstado("Facturable");
                                $objSolCaractInstalacion->setUsrCreacion($strUsrCreacion);
                                $objSolCaractInstalacion->setFeCreacion(new \DateTime('now'));
                                $this->emcom->persist($objSolCaractInstalacion);
                                $this->emcom->flush(); 
                            }

                        }
                        // Se agrega característica por cambio de forma de pago.
                        $objAdmiCaractCambioFormaPago = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                                                             ->findOneBy(array("descripcionCaracteristica" => "CAMBIO_FORMA_PAGO",
                                                                               "estado"                    => "Activo"));
                        if(is_object($objAdmiCaractCambioFormaPago))
                        {
                            $objSolCaractCambioFormaPago = new InfoDetalleSolCaract();
                            $objSolCaractCambioFormaPago->setCaracteristicaId($objAdmiCaractCambioFormaPago);
                            $objSolCaractCambioFormaPago->setDetalleSolicitudId($objDetalleSolCambFormaPago);
                            $objSolCaractCambioFormaPago->setValor('');
                            $objSolCaractCambioFormaPago->setEstado("NoFacturable");
                            $objSolCaractCambioFormaPago->setUsrCreacion($strUsrCreacion);
                            $objSolCaractCambioFormaPago->setFeCreacion(new \DateTime('now'));
                            $this->emcom->persist($objSolCaractCambioFormaPago);
                            $this->emcom->flush();
                        }
                        // Se agrega característica FACTURACION DETALLADA
                        $objAdmiCaractFactDet = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                                                            ->findOneBy(array("descripcionCaracteristica" => "FACTURACION DETALLADA",
                                                                              "estado"                    => "Activo"));
                        $objSolCaractFactDet = new InfoDetalleSolCaract();
                        $objSolCaractFactDet->setCaracteristicaId($objAdmiCaractFactDet);
                        $objSolCaractFactDet->setDetalleSolicitudId($objDetalleSolCambFormaPago);
                        $objSolCaractFactDet->setValor('S');
                        $objSolCaractFactDet->setEstado("Activo");
                        $objSolCaractFactDet->setUsrCreacion($strUsrCreacion);
                        $objSolCaractFactDet->setFeCreacion(new \DateTime('now'));
                        $this->emcom->persist($objSolCaractFactDet);
                        $this->emcom->flush();                    

                    }
                }
                $strRespuestaFact   = $this->emcom->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                  ->generarFacturacionSolicitud($arrayParametrosFact);
                
                if($strRespuestaFact ==='OK')
                {
                    $arrayParamFact                        = array();
                    $arrayParamFact['intPuntoId']          = $intIdPto; 
                    $arrayParamFact['strUsrCreacion']      = $strUsrCreacion;
                    $arrayParamFact['intCaracteristicaId'] = $objAdmiCaractCambioFormaPago->getId();
                    $arrayParamFact['strMsnError']         = str_pad(' ', 30); 
                    $this->emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                       ->marcarFacturasCaracteristicaPtoId($arrayParamFact);
                }
                $intCont ++;
            }
          
        }
        catch(\Exception $objEx)
        {
            $this->utilService->insertError('Telcos+','InfoContratoService->ejecutarFacturacionCambioFormaPago',
                                            $objEx->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCliente);
        }
        
        return $strRespuesta;
    }    

    
    /**   
     * Documentación para el método 'guardarDocumentosDigitales'.
     *
     * Método usado para guardar documentos digitales por cambio de forma de pago.
     * @param array    $arrayParams 
     * 
     * @author Angel Reina <areina@telconet.ec>
     * @version 1.0 19-07-2019
     * 
     */       
    public function guardarDocumentosDigitales($arrayParams)
    {
        $strFechaCreacion           = new \DateTime('now');
        //Guardo files asociados a la forma de de pago del contrato 
        $arrayDatosFormFiles        = $arrayParams['arrayDatosFormFiles'];
        $arrayTipoDocumentos        = $arrayParams['arrayDatosFormTipos'];
        $intNumeroContrato          = $arrayParams['intNumeroContrato'];
        $strCodEmpresa              = $arrayParams['strCodEmpresa'];
        $strUsrCreacion             = $arrayParams['strUsrCreacion'];
        $strClientIp                = $arrayParams['strClientIp'];
        $intPagoDatosId             = $arrayParams['intPagoDatosId'];
        $strEstadoActivo            = "Activo";
        $this->emComunicacion->getConnection()->beginTransaction();
        try
        {  
            $objInfoContrato = $this->emcom->getRepository('schemaBundle:InfoContrato')->find($intNumeroContrato);
            if( $objInfoContrato )
            {              
                foreach($arrayDatosFormFiles as $imagenes)
                {
                    foreach($imagenes as $key_imagen => $value)
                    {
                        if($value)
                        {
                            $objInfoDocumento = new InfoDocumento();
                            $objInfoDocumento->setFile($value);
                            $objInfoDocumento->setNombreDocumento("documento_digital");
                            $objInfoDocumento->setUploadDir(substr($this->fileRoot, 0, -1));
                            $objInfoDocumento->setFechaDocumento($strFechaCreacion);
                            $objInfoDocumento->setUsrCreacion($strUsrCreacion);
                            $objInfoDocumento->setFeCreacion($strFechaCreacion);
                            $objInfoDocumento->setIpCreacion($strClientIp);
                            $objInfoDocumento->setEstado($strEstadoActivo);
                            $objInfoDocumento->setMensaje("Archivo agregado a la forma de pago con el contrato # " . $intNumeroContrato);
                            $objInfoDocumento->setEmpresaCod($strCodEmpresa);

                            $intIdTipoDocumentoGeneral = $arrayTipoDocumentos["tipos"][$key_imagen];

                            $objTipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                       ->find($intIdTipoDocumentoGeneral);
                            
                            if($objTipoDocumentoGeneral != null)
                            {
                                $objInfoDocumento->setTipoDocumentoGeneralId($objTipoDocumentoGeneral->getId());
                            }

                            if($objInfoDocumento->getFile())
                            {
                                $objInfoDocumento->preUpload();
                                $objInfoDocumento->upload();
                            }
                            if($objInfoDocumento->getExtension() != null && $objInfoDocumento->getExtension() != '')
                            {
                                $objTipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                    ->findOneByExtensionTipoDocumento(strtoupper($objInfoDocumento->getExtension()));

                                if($objTipoDocumento != null)
                                {
                                    $objInfoDocumento->setTipoDocumentoId($objTipoDocumento);
                                }
                                else
                                {
                                    if($objInfoDocumento->getExtension() != null)
                                    {
                                        // Inserto registro con la extension del archivo a subirse
                                        $objAdmiTipoDocumento = new AdmiTipoDocumento();
                                        $objAdmiTipoDocumento->setExtensionTipoDocumento(strtoupper($objInfoDocumento->getExtension()));
                                        $objAdmiTipoDocumento->setTipoMime(strtoupper($objInfoDocumento->getExtension()));
                                        $objAdmiTipoDocumento->setDescripcionTipoDocumento('ARCHIVO FORMATO '
                                                                                          . strtoupper($objInfoDocumento->getExtension()));
                                        $objAdmiTipoDocumento->setEstado($strEstadoActivo);
                                        $objAdmiTipoDocumento->setUsrCreacion($strUsrCreacion);
                                        $objAdmiTipoDocumento->setFeCreacion($strFechaCreacion);
                                        $this->emComunicacion->persist($objAdmiTipoDocumento);
                                        $this->emComunicacion->flush();
                                        $objInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);
                                    }
                                }
                            }
                            $objInfoDocumento->setContratoId($objInfoContrato->getId());
                            $this->emComunicacion->persist($objInfoDocumento);
                            $this->emComunicacion->flush();

                            $objInfoDocumentoRelacion = new InfoDocumentoRelacion();
                            $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());
                            $objInfoDocumentoRelacion->setModulo('COMERCIAL');
                            $objInfoDocumentoRelacion->setContratoId($objInfoContrato->getId());
                            $objInfoDocumentoRelacion->setPagaDatosId($intPagoDatosId);
                            $objInfoDocumentoRelacion->setEstado($strEstadoActivo);
                            $objInfoDocumentoRelacion->setFeCreacion($strFechaCreacion);
                            $objInfoDocumentoRelacion->setUsrCreacion($strUsrCreacion);
                            $this->emComunicacion->persist($objInfoDocumentoRelacion);
                            $this->emComunicacion->flush();
                        }
                    }
                }                       
                if ($this->emComunicacion->getConnection()->isTransactionActive())
                {
                    $this->emComunicacion->getConnection()->commit();
                }                
                $this->emComunicacion->getConnection()->close();  
                return $objInfoContrato;
            }
       }
       catch(\Exception $e)
       {                                 
           if ($this->emComunicacion->getConnection()->isTransactionActive())
           {
               $this->emComunicacion->getConnection()->rollback();
           }                            
           $this->emComunicacion->getConnection()->close();  
           throw $e;
       }        
        
    }    
    
    /**   
     * Documentación para el método 'envioSMS'.
     *
     * Metodo usado para consumir la API SMS
     * @param array    $arrayParams 
     * 
     * @author Angel Reina <areina@telconet.ec>
     * @version 1.0 24-07-2019
     * 
     */       
    public function envioSMS($arrayParams)
    {
        $arrayParametros                = array();
        $arrayData                      = array();
        $arrayParametros['mensaje']     = $arrayParams['strMensaje'];
        $arrayParametros['numero']      = $arrayParams['strNumeroTlf'];
        $arrayParametros['user']        = $arrayParams['strUsername'];
        $arrayParametros['codEmpresa']  = $arrayParams['strCodEmpresa'];
        $arrayParametros['strProceso']  = $arrayParams['strProceso'];
        
        
        if($arrayParametros['numero'])
        {
            $arrayResponseSMS  = (array) $this->serviceSms->sendAPISMS($arrayParametros);
            if ($arrayResponseSMS['salida'] === '200')                         
            {
                $arrayData['status']        = 'OK';
                $arrayData['mensaje']       = 'SMS enviado correctamente';
                
            }
            else
            {
                $arrayData['status']      = 'ERROR_SERVICE';
                $arrayData['mensaje']     = $arrayResponseSMS['detail'];
            }            
        }
        else
        {
            $arrayData['status']        = 'ERROR';
            $arrayData['mensaje']       = 'No existe contacto';
        }
        
        return $arrayData;
    }
    
    
    /**
     * Crea registro en la ADMI_PARAMETRO_DET y ADMI_MOTIVO
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 09-01-2020
     * @return json con un código de estatus y un mensaje de acción realizada
     * 
     * @Secure(roles="ROLE_435-1")
     */
    public function creaMotivoParametroDet($arrayParametros)
    {
        foreach($arrayParametros['arrayParametrosDet']->arrayData as $objParametrosDet):
            $objAdmiParametroDet = new AdmiParametroDet();
            $objAdmiParametroDet->setParametroId($arrayParametros['objAdmiParametroCab']);
            $objAdmiParametroDet->setDescripcion(trim($objParametrosDet->strDescripcion));
            $objAdmiParametroDet->setValor1(trim($objParametrosDet->strValor1));
            $objAdmiParametroDet->setValor2(trim($objParametrosDet->strValor2));
            $objAdmiParametroDet->setEstado("Activo");
            $objAdmiParametroDet->setUsrCreacion($arrayParametros['strUsrCreacion']);
            $objAdmiParametroDet->setFeCreacion(new \DateTime('now'));
            $objAdmiParametroDet->setIpCreacion($arrayParametros['strUsrClientIp']);
            
            $objAdmiMotivo = new AdmiMotivo();
            $objAdmiMotivo->setNombreMotivo($objParametrosDet->strValor2);
            $objAdmiMotivo->setRelacionSistemaId(425);
            $objAdmiMotivo->setEstado('Activo');
            $objAdmiMotivo->setFeCreacion(new \DateTime('now'));
            $objAdmiMotivo->setUsrCreacion($arrayParametros['strUsrCreacion']);
            $objAdmiMotivo->setFeUltMod(new \DateTime('now'));
            $objAdmiMotivo->setUsrUltMod($arrayParametros['strUsrCreacion']);

            $this->emGeneral->persist($objAdmiParametroDet);
            $this->emGeneral->flush();
            $this->emGeneral->persist($objAdmiMotivo);
            $this->emGeneral->flush();                    
        endforeach;       
        
    }

    /**
     * getPorcentajeDctoInstDestino
     *     
     * Método que retorna el porcentaje de descuento de instalación a usar en el cambio de forma de pago.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 28-05-2020     
     
     * @param   $arrayParametros   [intIdContrato, strCodEmpresa,intFormaPagoId,intTipoCuentaId,intBancoTipoCuentaId]
     * @return  $intPorcentajeDcto
     *
     */
    public function getPorcentajeDctoInstDestino($arrayParametros)
    {
        $intIdContrato          = $arrayParametros['intIdContrato'];
        $intFormaPagoId         = $arrayParametros['intFormaPagoId'];
        $intTipoCuentaId        = $arrayParametros['intTipoCuentaId'];
        $intBancoTipoCuentaId   = $arrayParametros['intBancoTipoCuentaId'];
        $strIpCliente           = $arrayParametros['strIpCliente'];
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        
        try
        {        
            $objInfoContrato        = $this->emcom->getRepository('schemaBundle:InfoContrato')->find($intIdContrato);

            if(is_object($objInfoContrato))
            {
                $arrayParametros['idper']      = $objInfoContrato->getPersonaEmpresaRolId()->getId();
                $arrayResultado  = $this->emcom->getRepository('schemaBundle:InfoPunto')->getResultadoFindPtosPorPersonaEmpresaRol($arrayParametros);
                $arrayPuntos     = $arrayResultado['registros'];
                foreach($arrayPuntos as  $arrayPunto)
                {
                    $floatDctoInst = str_pad(' ', 30);
                    
                    $arrayServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                 ->getServicioPreferenciaByPunto(['intIdPunto' => $arrayPunto['id']]);

                    $intIdServicioInternet  = $arrayServicio[0]['ID_SERVICIO'];                  
                    
                    if(!empty($arrayServicio) && $intIdServicioInternet>0)
                    {                      
                        $strSql   = "BEGIN :floatDctoInst := DB_FINANCIERO.FNCK_CAMBIO_FORMA_PAGO.F_GET_PORCENTAJE_DCTO_DEST(:Fv_EmpresaCod,"
                                  . ":Fn_IdPunto,:Fn_IdServicio,:Fn_IdContrato,:Fn_FormaPagoId,:Fn_TipoCuentaId,:Fn_BancoTipoCuentaId); END;";
                        $objStmt = $this->emFinanciero->getConnection()->prepare($strSql);              
                        $objStmt->bindParam('Fv_EmpresaCod' , $arrayParametros['strEmpresaCod']);
                        $objStmt->bindParam('Fn_IdPunto' , $arrayPunto['id']);
                        $objStmt->bindParam('Fn_IdServicio' , $intIdServicioInternet);
                        $objStmt->bindParam('Fn_IdContrato' , $intIdContrato);
                        $objStmt->bindParam('Fn_FormaPagoId' , $intFormaPagoId);
                        $objStmt->bindParam('Fn_TipoCuentaId' , $intTipoCuentaId);
                        $objStmt->bindParam('Fn_BancoTipoCuentaId' , $intBancoTipoCuentaId);                       
                        $objStmt->bindParam('floatDctoInst' , $floatDctoInst);
                        $objStmt->execute();
                       
                        if(floatval($floatDctoInst)>0)
                        {
                            return floatval($floatDctoInst);
                        }
                    }
                }
            }
        }
        catch (\Exception $ex) 
        {
            $this->utilService->insertError('Telcos+',
                                            'InfoContratoService->getPorcentajeDctoInstDestino',
                                            $ex->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCliente);          
            $floatDctoInst = 0;
        }        
        return $floatDctoInst;
       
    }    

    /**
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.2 se corrige para que se cierre la transaccion al final
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1
     * @since 14/09/2020 - Se modifica para que se regularice por servicio y ya no por plan de internet
     * 
     * 
     * Método para regularizar los contratos o servicios adicionales que tengan inconsistencias.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0
     * @since 04/05/2020
     *
     * @param $arrayParametrosContrato[
     *                                      'intPersonaEmpresaRolId':   Integer:    Persona empresa rol id,
     *                                      'strUsuario':               String:     Usuario de session
     *                                     ]
     * @return $arrayRespuesta[
     *                          'boolRegularizar'       => true.- Se regularizo un registro
     *                                                     false.- No se regularizo registro,
     *                          'strMensajeRestricción' => Mensaje a mostrar en la app.
     *                        ]
     */
    public function regularizarContratoDigital($arrayParametros)
    {
        $arrayRespuesta = array();
        $strTipoAdendum = '';
        $this->emComercial->getConnection()->beginTransaction();
        try
        {
            $arrayParametrosDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne('REGULARIZACION_CONTRATO_TM_COMERCIAL',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    $arrayParametros['strCodEmpresa']);
            $arrayEstados           = explode(',',$arrayParametrosDet['valor1']);
            $arrayEstadosPlanes     = explode(',',$arrayParametrosDet['valor3']);

            $arrayContrato          = $this->emComercial->getRepository('schemaBundle:InfoContrato')
                                                        ->findBy(array("personaEmpresaRolId" => $arrayParametros['intPersonaEmpresaRolId']));
            if(empty($arrayContrato) && !isset($arrayContrato))
            {
                $arrayRespuesta = array('boolRegularizar'        => false,
                                        'strMensajeRestricción'  => 'Persona no tiene contrato');
                return $arrayRespuesta;
            }

            foreach($arrayContrato as $objContrato)
            {
                if ( in_array($objContrato->getEstado(), $arrayEstados) )
                {
                    $arrayAdendum   = $this->emComercial->getRepository('schemaBundle:InfoAdendum')
                                                        ->findBy(array('contratoId'     => $objContrato->getId(),
                                                                       'estado'         => $objContrato->getEstado()));
                    if(empty($arrayAdendum))
                    {
                        $arrayValidacionServ[]  = array( 
                                                        'strMensaje'    => $arrayParametrosDet['valor2'],
                                                        'strDescripcion'=> $arrayParametrosDet['valor2'].$objContrato->getId()
                                                        );
                        $arrayRespuesta         = array('boolRegularizar'        => false,
                                                        'strMensajeRestricción'  => '',
                                                        'arrayValidacionServ'    => !empty($arrayValidacionServ) ?
                                                                                             $arrayValidacionServ : null);
                        return $arrayRespuesta;
                    }
                }
            }

            $arrayParametroServicio = array("intPersonaEmpresaRolId" => $arrayParametros['intPersonaEmpresaRolId']);
            $arrayServicio          = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                        ->getServiciosRegularizar($arrayParametroServicio);
            foreach($arrayServicio as $objServicio)
            {
                foreach($arrayContrato as $objContrato)
                {
                    $arrayAdendum   = $this->emComercial->getRepository('schemaBundle:InfoAdendum')
                                                        ->findBy(array('contratoId'     => $objContrato->getId(),
                                                                       'puntoId'        => $objServicio->getPuntoId()->getId(),
                                                                       'servicioId'     => $objServicio->getId()));
                    foreach($arrayAdendum as $objAdendum)
                    {
                        $strEstadoAden = $objAdendum->getEstado();
                        if($objAdendum->getTipo = 'C')
                        {
                            $strEstadoAden = $objContrato->getEstado();
                        }
                        $arrayParametrosDetBuscar  = array('strNombreParametroCab'  => 'REGLAS_REGUDATA_COMERCIAL',
                                                           'strEstado'              => 'Activo',
                                                           'strValor2'              => $strEstadoAden,
                                                           'strValor3'              => $objServicio->getPuntoId()->getEstado(),
                                                           'strValor4'              => $objServicio->getEstado());
                        $arrayContratosRegulariza  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                     ->findParametrosDet($arrayParametrosDetBuscar);
                        if(!empty($arrayContratosRegulariza['arrayResultado']))
                        {
                            $arrayParamServicioReg  = array('intPersonaEmpresaRolId' => $arrayParametros['intPersonaEmpresaRolId'],
                                                            'strEstadoPunto'         => $arrayContratosRegulariza['arrayResultado'][0]['strValor5'],
                                                            'strEstadoServicio'      => $arrayContratosRegulariza['arrayResultado'][0]['strValor6'],
                                                            'objFeCreacionServ'      => $objServicio->getFeCreacion());
                            $arrayServicioRegulari  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                           ->getServiciosRegularizar($arrayParamServicioReg);

                            if($objAdendum->getTipo() == 'C')
                            {
                                $strTipoAdendum = "Contrato";
                            }
                            else
                            {
                                $strTipoAdendum = ($objAdendum->getTipo() == 'AP') ? 'Adendum Punto' : 'Adendum Servicio';
                            }

                            if(empty($arrayServicioRegulari))
                            {
                                $strMensaje =   "No se cumple la ".$arrayContratosRegulariza['arrayResultado'][0]['strValor1']."  ".
                                                $strTipoAdendum." ".$objAdendum->getId()." estado ".$objAdendum->getEstado().", Punto ".
                                                $objServicio->getPuntoId()->getId()." estado ".$objServicio->getPuntoId()->getEstado().
                                                ", servicio ".$objServicio->getId()." estado ".$objServicio->getEstado();
                                if($objAdendum->getTipo() != 'AS')
                                {
                                    $arrayValidacionServ[] = array( 'intAdendum'    => $objAdendum->getId(),
                                                                    'intPunto'      => $objAdendum->getPuntoId(),
                                                                    'intServicio'   => $objAdendum->getServicioId(),
                                                                    'strMensaje'    => $arrayContratosRegulariza['arrayResultado']
                                                                                                                [0]['strValor1'],
                                                                    'strDescripcion'=> $strMensaje
                                                                    );
                                }
                            }
                            foreach($arrayServicioRegulari as $objServRegularizar)
                            {
                                $objAdendumReg = $this->emComercial->getRepository('schemaBundle:InfoAdendum')
                                                                   ->findOneBy(array(
                                                                                       "puntoId"    => $objServRegularizar->getPuntoId()->getId(),
                                                                                       "servicioId" => $objServRegularizar->getId()
                                                                                ));
                                if(is_object($objAdendumReg) &&
                                    ($objAdendumReg->getTipo() != null && $objAdendumReg->getTipo() != $objAdendum->getTipo()))
                                {
                                    continue;
                                }

                                if($objServRegularizar->getTipoOrden() == 'T')
                                {
                                    $strObservacionTraslado = 'Se Creó el servicio por Traslado del login';
                                    $arrayParamHistTrsl     = array('intServicioId'     => $objServRegularizar->getId(),
                                                                    'strObservacion'    => $strObservacionTraslado);
                                    $arrayRespHistTrsl      = $this->findHistorialTrasladoServicio($arrayParamHistTrsl);
                                    if(!empty($arrayRespHistTrsl))
                                    {
                                        if($arrayRespHistTrsl['strLoginPtoOrigen'] != $objServicio->getPuntoId()->getLogin())
                                        {
                                            $strLoginBuscar = $arrayRespHistTrsl['strLoginPtoOrigen'];
                                            //Buscar origen de traslado
                                            $boolExisteTrsl = true;
                                            while($boolExisteTrsl)
                                            {
                                                $arrayParamServReg  = array('intPersonaEmpresaRolId' => $arrayParametros['intPersonaEmpresaRolId'],
                                                                            'strEstadoPunto'         => $objServicio->getPuntoId()->getEstado(),
                                                                            'strEstadoServicio'      => $objServicio->getEstado(),
                                                                            'strLogin'               => $strLoginBuscar);
                                                $arrayServicioTrsl  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                                        ->getServiciosRegularizar($arrayParamServReg);
                                                if(empty($arrayServicioTrsl))
                                                {
                                                    $boolExisteTrsl = false;
                                                }
                                                else
                                                {
                                                    $strLoginBuscar = '';
                                                    foreach($arrayServicioTrsl as $objTraslaServ)
                                                    {
                                                        $arrayParamHistTrsl     = array('intServicioId'     => $objTraslaServ->getId(),
                                                                                        'strObservacion'    => $strObservacionTraslado);
                                                        $arrayRespHistTrsl      = $this->findHistorialTrasladoServicio($arrayParamHistTrsl);
                                                        if(empty($arrayRespHistTrsl))
                                                        {
                                                            $boolExisteTrsl = true;
                                                        }
                                                        else
                                                        {
                                                            if($arrayRespHistTrsl['strLoginPtoOrigen'] == $objServicio->getPuntoId()->getLogin())
                                                            {
                                                                if($objTraslaServ->getPlanId()->getId() != $objServicio->getPlanId()->getId())
                                                                {
                                                                    $objAdendumReg = null;
                                                                }
                                                                else
                                                                {
                                                                    $objAdendumReg = $this->emComercial->getRepository('schemaBundle:InfoAdendum')
                                                                                          ->findOneBy(array(
                                                                                                            "puntoId"    => $objTraslaServ
                                                                                                                            ->getPuntoId()->getId(),
                                                                                                            "servicioId" => $objTraslaServ->getId()
                                                                                                            ));
                                                                }
                                                                break;
                                                            }
                                                            else
                                                            {
                                                                $strLoginBuscar = $arrayRespHistTrsl['strLoginPtoOrigen'];
                                                            }
                                                        }
                                                    }
                                                }
                                                if(empty($strLoginBuscar) || $strLoginBuscar == '')
                                                {
                                                    $boolExisteTrsl = false;
                                                }
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $objAdendumReg = null;
                                    }

                                }


                                if ($objAdendumReg)
                                {
                                    $objAdendumReg->setContratoId($objAdendum->getContratoId());
                                    $objAdendumReg->setNumero($objAdendum->getNumero());
                                    $objAdendumReg->setTipo($objAdendum->getTipo());
                                    $objAdendumReg->setFormaPagoId($objAdendum->getFormaPagoId());
                                    $objAdendumReg->setTipoCuentaId($objAdendum->getTipoCuentaId());
                                    $objAdendumReg->setBancoTipoCuentaId($objAdendum->getBancoTipoCuentaId());
                                    $objAdendumReg->setNumeroCtaTarjeta($objAdendum->getNumeroCtaTarjeta());
                                    $objAdendumReg->setTitularCuenta($objAdendum->getTitularCuenta());
                                    $objAdendumReg->setMesVencimiento($objAdendum->getMesVencimiento());
                                    $objAdendumReg->setAnioVencimiento($objAdendum->getAnioVencimiento());
                                    $objAdendumReg->setCodigoVerificacion($objAdendum->getCodigoVerificacion());
                                    $objAdendumReg->setUsrModifica($arrayParametros['strUsuario']);
                                    $objAdendumReg->setFeModifica(new \DateTime('now'));
                                    $objAdendumReg->setEstado($arrayContratosRegulariza['arrayResultado'][0]['strValor7']);
                                    $this->emComercial->persist($objAdendumReg);

                                    //Desenlazar contratos
                                    $objAdendum->setContratoId(null);
                                    $objAdendum->setNumero(null);
                                    $objAdendum->setTipo(null);
                                    $objAdendum->setFormaPagoId(null);
                                    $objAdendum->setTipoCuentaId(null);
                                    $objAdendum->setBancoTipoCuentaId(null);
                                    $objAdendum->setNumeroCtaTarjeta(null);
                                    $objAdendum->setTitularCuenta(null);
                                    $objAdendum->setMesVencimiento(null);
                                    $objAdendum->setAnioVencimiento(null);
                                    $objAdendum->setCodigoVerificacion(null);
                                    $objAdendum->setUsrModifica($arrayParametros['strUsuario']);
                                    $objAdendum->setFeModifica(new \DateTime('now'));
                                    $objAdendum->setEstado($objServicio->getEstado());
                                    $this->emComercial->persist($objAdendum);

                                    $this->emComercial->flush();
                                    if ($this->emComercial->getConnection()->isTransactionActive())
                                    {
                                        $this->emComercial->getConnection()->commit();
                                    }
                                }
                                else
                                {
                                    $strMensaje =   $arrayContratosRegulariza['arrayResultado'][0]['strValor7']."  ".
                                                    $strTipoAdendum." ".$objAdendum->getId()." estado ".$objAdendum->getEstado().", Punto ".
                                                    $objServicio->getPuntoId()->getId()." estado ".$objServicio->getPuntoId()->getEstado().
                                                    ", servicio ".$objServicio->getId()." estado ".$objServicio->getEstado().
                                                    ", no dispone de servicios para ser regularizados";
                                    if($objAdendum->getTipo() != 'AS')
                                    {
                                        $arrayValidacionServ[] = array( 'intAdendum'    => $objAdendum->getId(),
                                                                        'intPunto'      => $objAdendum->getPuntoId(),
                                                                        'intServicio'   => $objAdendum->getServicioId(),
                                                                        'strMensaje'    => $arrayContratosRegulariza['arrayResultado']
                                                                                                                    [0]['strValor1'],
                                                                        'strDescripcion'=> $strMensaje
                                                                        );
                                    }
                                }
                            }
                        }
                    }

                }
            }

        }
        catch(\Exception $e)
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            $this->emComercial->getConnection()->close();
            throw $e;
        }
        if ($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->close();   
        }
             
        $arrayRespuesta = array('boolRegularizar'        => true,
                                'strMensajeRestricción'  => '',
                                'arrayValidacionServ'    => !empty($arrayValidacionServ) ? $arrayValidacionServ : null);
        return $arrayRespuesta;
    }

    /**
     *
     * Método que retorna el historial de traslado de un servicio
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0
     * @since 07-05-2020
     *
     * @param array $arrayParametros[
     *                                  intServicioId:      integer:   Servicio id
     *                                  strObservacion:     string:    Observación a buscar
     *                              ]
     * @return object $objHistorial
     */
    public function findHistorialTrasladoServicio( $arrayParametros )
    {
        $arrayRespuesta = array();
        try
        {
            $arrayParametrosServHst = array('intServicioId'    => $arrayParametros['intServicioId'],
                                            'strObservacion'   => $arrayParametros['strObservacion']);
            $objServicioHistorial   = $this->emComercial->getRepository('schemaBundle:InfoServicioHistorial')
                                                        ->findHistorialPorObservacion($arrayParametrosServHst);
            if(is_object($objServicioHistorial))
            {
                $strTraslObservacion    = $objServicioHistorial->getObservacion();
                $strLoginBuscarTraslado = substr($strTraslObservacion,strpos($strTraslObservacion,
                                                                             $arrayParametros['strObservacion'])+44);
                $arrayLoginTraslado     = explode(" ",$strLoginBuscarTraslado);
                if(!empty($arrayLoginTraslado) &&
                    isset($arrayLoginTraslado[0]) )
                {
                    $arrayRespuesta     = array('strLoginPtoOrigen' => $arrayLoginTraslado[0],
                                                'strLoginPtoTrasla' => $arrayLoginTraslado[3]);
                }
            }
        }
        catch(\Exception $e)
        {
            throw $e;
        }
        return $arrayRespuesta;
    }


    /**
     * ejecutarReversoCambioRazonSocial
     * Función que  realiza el reverso de cambio rason social
     * 
     * @author Jefferson Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 08-11-2021
     * 
     * @param array $arrayParametros
     * [ 
     *   strPrefijoEmpresa  => prefijo de empresa
     *   strUsrCreacion  => nombre de usuario
     *   strClientIp  => ip de usuario
     *   identificacionDestino  => identificacion de nuevo cliente
     *   idPersonaEmpresaRolOrigen  => idPersonaEmpresaRol de cliente origen
     *   idPersonaOrigen  => idPersona de cliente origen
     *   strMotivoReverso  => razon por la cual se realiza el reverso
     * ]
     * 
     * @return array $arrayRespuesta
     * [
     *      objRespuesta     => Retorna si esta procesando o falló el proceso,
     *      strStatus        => Estado del proceso,
     *      strMensaje       => Retorna si realizó o no el proceso
     * ]
     * 
     */
    public function ejecutarReversoCambioRazonSocial($arrayParametros)
    {

        $strJsonRequest     = json_encode($arrayParametros); 
        $strUsrCreacion     = $arrayParametros['strUsrCreacion'];
        $strIpCreacion      = $arrayParametros['strClientIp']; 
        $strMensaje         = str_repeat('a',  30*1024);
        $strStatus          = str_repeat('a',  30*1024);
        $objRespuesta       = array();
        try
        { 
            $strSql = "BEGIN DB_COMERCIAL.CMKG_CRS_TRANSACCION.P_REVERSAR_CRS(
                                                :Lcl_Request, 
                                                :Lv_Mensaje,
                                                :Lv_Status,
                                                :Pcl_Response); 
                                            END;";
           
            $objConn = oci_connect($this->strUrsrComercial,
                                   $this->strPassComercial,
                                   $this->strDnsComercial);

          
            $objStmt = oci_parse($objConn, $strSql);
            $strRequest = oci_new_descriptor($objConn);
            $strRequest->writetemporary(  $strJsonRequest );
            $objResponse= oci_new_cursor($objConn);
             
            oci_bind_by_name($objStmt, ':Lcl_Request' , $strRequest, -1, OCI_B_CLOB); 
            oci_bind_by_name($objStmt, ':Lv_Mensaje'  , $strMensaje); 
            oci_bind_by_name($objStmt, ':Lv_Status'   , $strStatus); 
            oci_bind_by_name($objStmt, ':Pcl_Response', $objResponse, -1, OCI_B_CURSOR); 
            
            oci_execute($objStmt);
            $strErrorOci = oci_error($objStmt);  
        
            error_log(">>> " . $strMensaje);
          
            if (strpos($strStatus, 'OK') === true )
            {   
               
                oci_execute($objResponse);
                while (($objRow = oci_fetch_array($objResponse, OCI_ASSOC+OCI_RETURN_NULLS)) ) 
                { 
                    array_push( $objRespuesta , $objRow);
                }

                oci_free_statement($objStmt);
                oci_free_statement($objResponse);
                oci_close($conn);
                $strStatus   ='0';             
            }
            else
            {
                $strStatus   = '500';  
                $strMensaje  = $strErrorOci['message']? $strErrorOci['message']:  $strMensaje ; 
                $strMensaje  = $strMensaje . " Por favor notificar a Sistemas."; 
            } 

        }
        catch(\Exception $e)
        {
            $strMensaje   = "Error al procesar la reverso de cambio de razon social. Por favor notificar a Sistemas.";
            $strStatus    = "500";  

            $this->serviceUtil->insertError('Telcos+',
                                            'InfoContratoService.ejecutarReversoCambioRazonSocial',
                                            'Error InfoContratoService.ejecutarReversoCambioRazonSocial:'.$e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion); 
        }

        $arrayRespuesta = array (
            'objRespuesta'   =>  $objRespuesta,
            'strStatus'      =>  $strStatus, 
            'strMensaje'     =>  $strMensaje
           );
           
        return $arrayRespuesta;
    }

    /** 
     * Función que par obtener los documento requeridos en un contrato
     * 
     * @author : Wilson Quinto <wquinto@telconet.ec>
     * @version 1.0 25-02-2022
     * 
     * @param 
     *         array $arrayTipoDocumento
     * @throws Exception
     * @return $arrayResultado
     * 
     */
    public function verificarDocumentosRequeridosMS($arrayTipoDocumento)
    {
        try
        {
            $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                        'tokencas: '.$arrayTipoDocumento['token'])
                                       ); 
            $strJsonData        = json_encode($arrayTipoDocumento);
            $arrayResponseJson  = $this->serviceRestClient->postJSON($this->strUrlDocumentoContratoMs, $strJsonData , $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);

            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 
            && isset($strJsonRespuesta['status'])
            && isset($strJsonRespuesta['message']) )
            {   
                $arrayResultado = $strJsonRespuesta['data'];
            }
            else
            {
                $arrayResultado['strStatus']      = "ERROR";
                if(empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['strMensaje']  = "No Existe Conectividad con el WS MS CORE CONTRATO.";
                }
                else
                {
                    $arrayResultado['strMensaje']  = $strJsonRespuesta['message'];
                }
            }

            return $arrayResultado ;
        }
        catch(\Exception $e)
        {
            $strRespuesta   = "Error al ejecutar el proceso de verificacoin de documento. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje'     =>$strRespuesta);
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoContratoService.verificarDocumentosRequeridosMS',
                                            'Error InfoContratoService.verificarDocumentosRequeridosMS:'.$e->getMessage(),
                                            $arrayTipoDocumento['usrCreacion'],
                                            $arrayTipoDocumento['clientIp']); 
            return $arrayResultado;
        }
    }




    /**
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 11-05-2022
     *
     * Metodo que almacena las clausulas y datos bancarios.
     * @param  array $arrayParametros [
     *                                  "puntoId"  :integer:  Usuario de creación, 
     * @return array $arrayResultado
     */
    public function guardarClausulasOrDataBancaria($arrayParametros)
    {
        $arrayResultado  = array();
        $arrayTokenCas= $this->serviceTokenCas->generarTokenCas();
        $arrayParametros['token'] = $arrayTokenCas['strToken'];

        $strIpMod               = $arrayParametros['127.0.0.1'];
        $strUserMod             = $arrayParametros['strUsrCreacion'];

        try
        {
            $objOptions         = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER     => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayParametros['token']
                )
            );

            $strJsonData        = json_encode($arrayParametros);
            $strUrl             = $this->strUrlMsCompContratoDigital.'guardarClausulasOrDataBancaria';
            $arrayResponseJson  = $this->serviceRestClient->postJSON( $strUrl , $strJsonData, $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'], true);

            if (
                isset($strJsonRespuesta['code']) && $strJsonRespuesta['code'] == 0
                && isset($strJsonRespuesta['status'])
                && isset($strJsonRespuesta['message'])
            )
            {
                $arrayResponse = array(
                    'status' => 'OK',
                    'message' => $strJsonRespuesta['message'],
                    'data' => $strJsonRespuesta['data']
                );
                $arrayResultado = $arrayResponse;
            }
             else
            {
                $arrayResultado['status']       = "ERROR";

                if (!empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['message']  = $strJsonRespuesta['message'];
                }
                else
                {
                    $arrayResultado['message']  = "No Existe Conectividad con el WS ms-comp-contrato-digital.";
                }
            }
        } catch (\Exception $e)
        {
            $arrayResultado['message'] = "Error al ejecutar el proceso de links bancario. Favor Notificar a Sistemas" . $e->getMessage();

            $this->serviceUtil->insertError(
                'Telcos+',
                'InfoPuntoService.getDataLinksContratoCliente',
                'Error InfoPuntoService.getDataLinksContratoCliente:' . $e->getMessage(),
                $strUserMod,
                $strIpMod
            );
        }
      return $arrayResultado;
    }

    /**
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 11-05-2022
     *
     * Metodo que actualiza el estado de las clausulas y datos bancarios.
     * @param  array $arrayParametros [
     *                                  "puntoId"  :integer:  Usuario de creación, 
     * @return array $arrayResultado
     */
    public function actualizarEstadoClausula($arrayParametros)
    {
        $arrayResultado  = array();
        $arrayTokenCas= $this->serviceTokenCas->generarTokenCas();
        $arrayParametros['token'] = $arrayTokenCas['strToken'];

        $strIpMod               = $arrayParametros['127.0.0.1'];
        $strUserMod             = $arrayParametros['strUsrCreacion'];

        try
        {
            $objOptions         = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER     => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayParametros['token']
                )
            );

            $strJsonData        = json_encode($arrayParametros);
            $strUrl             = $this->strUrlMsCompContratoDigital.'actualizarEstadoClausula';
            $arrayResponseJson  = $this->serviceRestClient->postJSON( $strUrl , $strJsonData, $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'], true);

            if (
                isset($strJsonRespuesta['code']) && $strJsonRespuesta['code'] == 0
                && isset($strJsonRespuesta['status'])
                && isset($strJsonRespuesta['message'])
            )
            {
                $arrayResponse = array(
                    'status' => 'OK',
                    'message' => $strJsonRespuesta['message'],
                    'data' => $strJsonRespuesta['data']
                );
                $arrayResultado = $arrayResponse;
            }
             else
            {
                $arrayResultado['status']       = "ERROR";

                if (!empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['message']  = $strJsonRespuesta['message'];
                }
                else
                {
                    $arrayResultado['message']  = "No Existe Conectividad con el WS ms-comp-contrato-digital.";
                }
            }
        } catch (\Exception $e)
        {
            $arrayResultado['message'] = "Error al ejecutar el proceso de links bancario. Favor Notificar a Sistemas" . $e->getMessage();

            $this->serviceUtil->insertError(
                'Telcos+',
                'InfoPuntoService.actualizarEstadoClausula',
                'Error InfoPuntoService.actualizarEstadoClausula:' . $e->getMessage(),
                $strUserMod,
                $strIpMod
            );
        }
        return $arrayResultado;
    }
    
   /** 
     * Función que par obtener los documento requeridos en un contrato
     * 
     * @author : David León <mdleon@telconet.ec>
     * @version 1.0 25-05-2022
     * 
     * @param 
     *         array $arrayDatosWs
     * @throws Exception
     * @return $arrayResultado
     * 
     */
    public function validaContrato($arrayDatosWs)
    {
        $strIdentificacion  = ( isset($arrayDatosWs['strIdentificacion']) && !empty($arrayDatosWs['strIdentificacion']) )
                                   ? $arrayDatosWs['strIdentificacion'] : '';
        $strCodEmpresa      = ( isset($arrayDatosWs['strCodEmpresa']) && !empty($arrayDatosWs['strCodEmpresa']) )
                                   ? $arrayDatosWs['strCodEmpresa'] : 10;
        $strUsuario          = ( isset($arrayDatosWs['strUsuario']) && !empty($arrayDatosWs['strUsuario']) )
                                   ? $arrayDatosWs['strUsuario'] : 'TELCOS +';
        $strStatus          = '200';
        try
        {
            $objPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                ->findByIdentificacionTipoRolEmpresa($strIdentificacion, 'Cliente', $strCodEmpresa);
            
            if(!is_object($objPersonaEmpresaRol))
            {
                $objPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                ->findByIdentificacionTipoRolEmpresa($strIdentificacion, 'Pre-cliente', $strCodEmpresa);
            }
            if(is_object($objPersonaEmpresaRol))
            {
                $objContrato = $this->emcom->getRepository('schemaBundle:InfoContrato')
                                               ->findContratoActivoPorPersonaEmpresaRol($objPersonaEmpresaRol->getId());
                if(!is_object($objContrato))
                {
                    throw new \Exception("No se encontro el contrato, favor verificar");
                }
                $arrayDatos = array('status'    => $strStatus,
                                    'contrato'  => 'OK'
                                        );
            }
        }
        catch(\Exception $e)
        {
            $this->utilService->insertError('Telcos+',
                                            'InfoContratoService->validaContrato',
                                            $e->getMessage(),
                                            $strUsuario,
                                            '172.0.0.1');  
            $arrayDatos = array('status'    => $strStatus,
                                'error'  => $e->getMessage()
                                        );
        }
        return $arrayDatos;
    }

    /**
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 11-05-2022
     *
     * Metodo que actualiza el estado de las clausulas y datos bancarios.
     * @param  array $arrayParametros [
     *                                  "puntoId"  :integer:  Usuario de creación, 
     * @return array $arrayResultado
     */
    public function reenviarDocumentoContrato($arrayParametros)
    {
        $arrayResultado  = array();
        $arrayTokenCas= $this->serviceTokenCas->generarTokenCas();
        $arrayParametros['token'] = $arrayTokenCas['strToken'];

        $strIpMod               = $arrayParametros['127.0.0.1'];
        $strUserMod             = $arrayParametros['strUsrCreacion'];

        try
        {
            $objOptions         = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER     => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayParametros['token']
                )
            );

            $strJsonData        = json_encode($arrayParametros);
            $strUrl             = $this->strUrlMsCompContratoDigital.'generarContratoFisico';
            $arrayResponseJson  = $this->serviceRestClient->postJSON( $strUrl , $strJsonData, $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'], true);

            if (
                isset($strJsonRespuesta['code']) && $strJsonRespuesta['code'] == 0
                && isset($strJsonRespuesta['status'])
                && isset($strJsonRespuesta['message'])
            )
            {
                $arrayResponse = array(
                    'status' => 'OK',
                    'message' => $strJsonRespuesta['message'],
                    'data' => $strJsonRespuesta['data']
                );
                $arrayResultado = $arrayResponse;
            }
             else
            {
                $arrayResultado['status']       = "ERROR";

                if (!empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['message']  = $strJsonRespuesta['message'];
                }
                else
                {
                    $arrayResultado['message']  = "No Existe Conectividad con el WS ms-comp-contrato-digital.";
                }
            }
        } catch (\Exception $e)
        {
            $arrayResultado['message'] = "Error al ejecutar el proceso de links bancario. Favor Notificar a Sistemas" . $e->getMessage();

            $this->utilService->insertError(
                'Telcos+',
                'InfoPuntoService.actualizarEstadoClausula',
                'Error InfoPuntoService.actualizarEstadoClausula:' . $e->getMessage(),
                $strUserMod,
                $strIpMod
            );
        }
      return $arrayResultado;
    }

      /**
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 11-05-2022
     *
     * Metodo que actualiza Documento Caracteristica
     * @param  array $arrayParametros [
     *                                  "strUsrCreacion"  :idContrato: idAdemdun , 
     */
    public function actualizarDocumentoCaracteristica($arrayParams)
    {
        $this->emComunicacion->getConnection()->beginTransaction();
        $strFechaMod = new \DateTime('now');   
        $this->emComercial->getConnection()->beginTransaction();
        $strUsrMod            = $arrayParams['strUsrCreacion'];
        $intIdContrato                 = $arrayParams['idContrato'];
        $intIdAdemdun                  = $arrayParams['idAdemdun'];
        try 
        {
            $objEntityCaract = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
            ->findOneBy(
              array("descripcionCaracteristica"   => 'docFisicoCargado',
                    "estado"                      => 'Activo'));
            if($intIdAdemdun)
            {
                $objInfoAdemdun = $this->emcom->getRepository('schemaBundle:InfoAdendumCaracteristica')
                                        ->findOneBy(array("adendumId" => $intIdAdemdun,
                                        "caracteristicaId"  => $objEntityCaract,
                                        "estado"  => array('Activo')));
                $objInfoAdemdun->setValor1("S");
                $objInfoAdemdun->setUsrUltMod($strUsrMod);
                $objInfoAdemdun->setFeUltMod($strFechaMod);                     
                $this->emComercial->persist($objInfoAdemdun);
                $this->emComercial->flush();
            } else if ($intIdContrato)
            {
                $objInfoContrato = $this->emcom->getRepository('schemaBundle:InfoContratoCaracteristica')
                ->findOneBy(array("contratoId" => $intIdContrato,
                "caracteristicaId"  => $objEntityCaract,
                "estado"  => array('Activo')));
                $objInfoContrato->setValor1("S");
                $objInfoContrato->setUsrUltMod($strUsrMod);
                $objInfoContrato->setFeUltMod($strFechaMod);                     
                $this->emComercial->persist($objInfoContrato);
                $this->emComercial->flush();
            }
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->commit();
            }                
            $this->emComercial->getConnection()->close();    
        } catch (\Throwable $th) 
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
           {
               $this->emComercial->getConnection()->rollback();
           }                            
           $this->emComercial->getConnection()->close();  
           throw $th;
        }
  
    }


    /**
     * procesaAprobacionContrato
     *     
     * Método que procesa la aprobación del contrato post pago de factura.
     *
     * @author Alex Gómez <algomez@telconet.ec>
     * @version 1.0 06-03-2023
     
     * @param   $arrayParametros   [strIpCreacion,strEmpresaCod,strPrefijoEmpresa,strUsrCreacion,strOrigen,
     *                              strTipo,strObservacionHistorial,intPersonaEmpresaRolId,intIdContrato,
     *                              intIdPunto,intIdAdendum,strAplicaTentativa]
     * @return  $arrayResponse     [status, mensaje, seAutorizo]
     *
     */
    public function procesaAprobacionContrato($arrayParametros)
    {
        $arrayParametrosProceso = array();
        $arrayParametrosProceso['ipCreacion']           = $arrayParametros['strIpCreacion'];
        $arrayParametrosProceso['codEmpresa']           = $arrayParametros['strEmpresaCod'];
        $arrayParametrosProceso['prefijoEmpresa']       = $arrayParametros['strPrefijoEmpresa'];
        $arrayParametrosProceso['usrCreacion']          = $arrayParametros['strUsrCreacion'];
        $arrayParametrosProceso['origen']               = $arrayParametros['strOrigen'];
        $arrayParametrosProceso['tipo']                 = $arrayParametros['strTipo'];
        $arrayParametrosProceso['personaEmpresaRolId']  = $arrayParametros['intPersonaEmpresaRolId'];
        $arrayParametrosProceso['contratoId']           = $arrayParametros['intIdContrato'];
        $arrayParametrosProceso['puntoId']              = $arrayParametros['intIdPunto'];
        $arrayParametrosProceso['numeroAdendum']        = $arrayParametros['strNumeroAdendum'];
        $arrayParametrosProceso['aplicaTentativa']      = $arrayParametros['strAplicaTentativa'];
        $arrayParams = json_encode($arrayParametrosProceso);

        $strStatus              = str_repeat(' ', 5);
        $strMsjError            = str_repeat(' ', 1000);
        $intSeAutorizo          = -1;
        $arrayResponse          = array();
        
        try
        {

            //Consulta observación del proceso a guardar en historial
            $objParametro = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne(
                                                'APROBACION_CONTRATO_COMMAND',
                                                'COMERCIAL',
                                                '',
                                                'OBSERVACION_HISTORIAL_APROBACION',
                                                '','','','','',
                                                $arrayParametros['strEmpresaCod']);

            if(empty($objParametro) || empty($objParametro["valor1"]))
            {
                throw new \Exception("No se encontró el parámetro de observación para el historial por la aprobación");
            }

            $arrayParametrosProceso['observacionHistorial'] = $objParametro["valor1"];

            //Invocación a procedimiento
            $strSql = " BEGIN 
                            DB_COMERCIAL.CMKG_CONTRATO_TRANSACCION.P_AUTORIZAR_CONTRATO(:jsonRequest,
                                                                                        :strMsjError,
                                                                                        :strStatus,
                                                                                        :intSeAutorizo); 
                        END;";
            $objStmt = $this->emComercial->getConnection()->prepare($strSql);
            $objStmt->bindParam('jsonRequest', $arrayParams);
            $objStmt->bindParam('strMsjError', $strMsjError);
            $objStmt->bindParam('strStatus', $strStatus);
            $objStmt->bindParam('intSeAutorizo', $intSeAutorizo);
            $objStmt->execute();

            $arrayResponse['status'] = $strStatus;
            $arrayResponse['mensaje'] = $strMsjError;
            $arrayResponse['seAutorizo'] = $intSeAutorizo;
        }
        catch (\Exception $ex) 
        {
            $arrayResponse['status'] = 'ERROR';
            $arrayResponse['mensaje'] = $ex->getMessage();

            $this->utilService->insertError('Telcos+',
                                            'InfoContratoService->procesaAprobacionContrato',
                                            $ex->getMessage(),
                                            $arrayParametros['strUsrCreacion'],
                                            $arrayParametros['strIpCreacion']);
        }        
        return $arrayResponse;
       
    }

    /**
     * Función que verifica y genera las solicitudes de preplanificación para 
     * productos identificados como CIH en estado pendiente
     *
     * @param $arrayParametros [
     *                              "intIdServicioInternet"  => Id del servicio de Internet
     *                              "intIdPunto"             => Id del punto
     *                              "strUsuarioCreacion"     => Usuario de creación
     *                              "strIpCreacion"          => Ip de creación
     *                              "strOrigen"              => Origen de la ejecución
     *                              "strPrefijoEmpresa"      => Prefijo de la empresa
     *                              "strCodEmpresa"          => Código de la empresa
     *                         ]
     *
     * @return array $arrayRespuesta [
     *                                  "status"                => 'OK' o 'ERROR'
     *                                  "mensaje"               => Mensaje de error
     *                               ]
     *
     * @author Alex Gómez <algomez@telconet.ec>
     * @version 1.0 21/09/2022
     *
     */
    public function preplanificaProductosCIH($arrayParametros)
    {
        $arrayParametrosWs = array("prefijoEmpresa"     => $arrayParametros["strPrefijoEmpresa"],
                                   "ipCreacion"         => $arrayParametros["strIpCreacion"],
                                   "usrCreacion"        => $arrayParametros["strUsuarioCreacion"],
                                   "codEmpresa"         => $arrayParametros["strCodEmpresa"],
                                   "origen"             => $arrayParametros["strOrigen"],
                                   "puntoId"            => $arrayParametros["intIdPunto"],
                                   "servicioInternetId" => $arrayParametros["intIdServicioInternet"]);
        $strMensajeError = "";

        try
        {
            $arrayTokenCas= $this->serviceTokenCas->generarTokenCas();

            if(empty($arrayTokenCas['strToken']))
            {
                throw new \Exception('No se genero el token, por favor reintente.');
            }

            $objOptions         = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER     => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayTokenCas['strToken']
                )
            );

            $strJsonData        = json_encode($arrayParametrosWs);
            $strUrl             = $this->strUrlPreplanificaProdCIHMs;
            $arrayResponseJson  = $this->serviceRestClient->postJSON( $strUrl , $strJsonData, $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'], true);

            if (
                isset($strJsonRespuesta['code']) && $strJsonRespuesta['code'] == 0
                && isset($strJsonRespuesta['status'])
                && isset($strJsonRespuesta['message'])
            ) 
            {
                $strStatusRespuesta = $strJsonRespuesta['data']['status'];
                $strMensajeRespuesta = $strJsonRespuesta['data']['mensaje'];
            }
            else 
            {
                $strStatusRespuesta       = "ERROR";  
                
                if (!empty($strJsonRespuesta['message']))
                {   
                    $strMensajeRespuesta  = $strJsonRespuesta['message'];                   
                } 
                else 
                {
                    $strMensajeRespuesta  = "No Existe Conectividad con el WS ms-comp-contrato-digital.";
                }
            }
        }
        catch (\Exception $e)
        {
            $strStatusRespuesta     = "ERROR";
            $strMensajeRespuesta    = 'Ha ocurrido un error inesperado y no se ha podido verificar y generar las solicitudes por '
                                      .'Preplanificación de productos CIH. Por favor comuníquese con Sistemas!';
            $strMensajeError        = $e->getMessage();
            error_log("Error al preplanificar los productos CIH ".$strMensajeError);
        }

        $this->utilService->insertError(
            'Telcos+',
            basename(__CLASS__) . '.' . basename(__FUNCTION__),
            '[Status: '. $strStatusRespuesta. '] [Mensaje: '. 
                (($strMensajeError == "")?$strMensajeRespuesta:$strMensajeError) . ']',
            $arrayParametros["strIpCreacion"],
            $arrayParametros["strUsuarioCreacion"]
        );
        
        $arrayRespuesta = array("status"   => $strStatusRespuesta,
                                "mensaje"  => $strMensajeRespuesta);
        return $arrayRespuesta;
    }


    /**
     * Función que verifica y genera las ordenes de trabajo por solicitudes de preplanificación para 
     * productos identificados como CIH en estado pendiente
     *
     * @param $arrayParametros [
     *                              "intIdServicioInternet"  => Id del servicio de Internet
     *                              "intIdPunto"             => Id del punto
     *                              "strUsuarioCreacion"     => Usuario de creación
     *                              "strIpCreacion"          => Ip de creación
     *                              "strOrigen"              => Origen de la ejecución
     *                              "strPrefijoEmpresa"      => Prefijo de la empresa
     *                              "strCodEmpresa"          => Código de la empresa
     *                         ]
     *
     * @return array $arrayRespuesta [
     *                                  "status"                => 'OK' o 'ERROR'
     *                                  "mensaje"               => Mensaje de error
     *                               ]
     *
     * @author Alex Gómez <algomez@telconet.ec>
     * @version 1.0 21/09/2022
     *
     */
    public function generacionOtServicioCIH($arrayParametros)
    {
        $arrayParametrosWs = array("prefijoEmpresa"     => $arrayParametros["strPrefijoEmpresa"],
                                   "ipCreacion"         => $arrayParametros["strIpCreacion"],
                                   "usrCreacion"        => $arrayParametros["strUsuarioCreacion"],
                                   "codEmpresa"         => $arrayParametros["strCodEmpresa"],
                                   "origen"             => $arrayParametros["strOrigen"],
                                   "puntoId"            => $arrayParametros["intIdPunto"],
                                   "servicioInternetId" => $arrayParametros["intIdServicioInternet"]);
        $strMensajeError   = "";

        try
        {

            $arrayTokenCas= $this->serviceTokenCas->generarTokenCas();
        
            if(empty($arrayTokenCas['strToken']))
            {
                throw new \Exception('No se genero el token, por favor reintente.');
            }

            $objOptions         = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER     => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayTokenCas['strToken']
                )
            );

            $strJsonData        = json_encode($arrayParametrosWs);
            $strUrl             = $this->strUrlGeneraOtServicioCIHMs;
            $arrayResponseJson  = $this->serviceRestClient->postJSON( $strUrl , $strJsonData, $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'], true);

            if (
                isset($strJsonRespuesta['code']) && $strJsonRespuesta['code'] == 0
                && isset($strJsonRespuesta['status'])
                && isset($strJsonRespuesta['message'])
            ) 
            {
                $strStatusRespuesta = $strJsonRespuesta['data']['status'];
                $strMensajeRespuesta = $strJsonRespuesta['data']['mensaje'];
            }
            else 
            {
                $strStatusRespuesta       = "ERROR";  
                
                if (!empty($strJsonRespuesta['message']))
                {   
                    $strMensajeRespuesta  = $strJsonRespuesta['message'];                   
                } 
                else 
                {
                    $strMensajeRespuesta  = "No Existe Conectividad con el WS ms-comp-contrato-digital.";
                }
            }
        }
        catch (\Exception $e)
        {
            $strStatusRespuesta     = "ERROR";
            $strMensajeRespuesta    = 'Ha ocurrido un error inesperado y no se ha podido verificar y generar las OT de productos CIH. '
                                      .'Por favor comuníquese con Sistemas!';
            $strMensajeError        = $e->getMessage();
            error_log("Error al generar OT por productos CIH ".$strMensajeError);
        }

        $this->utilService->insertError(
            'Telcos+',
            basename(__CLASS__) . '.' . basename(__FUNCTION__),
            '[Status: '. $strStatusRespuesta. '] [Mensaje: '. 
                (($strMensajeError == "")?$strMensajeRespuesta:$strMensajeError) . ']',
            $arrayParametros["strIpCreacion"],
            $arrayParametros["strUsuarioCreacion"]
        );
        
        $arrayRespuesta = array("status"   => $strStatusRespuesta,
                                "mensaje"  => $strMensajeRespuesta);
        return $arrayRespuesta;
    }


    /**
     * Función que verifica y reversa las solicitudes de preplanificación para 
     * productos identificados como CIH en estado pendiente
     *
     * @param $arrayParametros [
     *                              "intIdServicioInternet"  => Id del servicio de Internet
     *                              "intIdPunto"             => Id del punto
     *                              "strUsuarioCreacion"     => Usuario de creación
     *                              "strIpCreacion"          => Ip de creación
     *                              "strOrigen"              => Origen de la ejecución
     *                              "strPrefijoEmpresa"      => Prefijo de la empresa
     *                              "strCodEmpresa"          => Código de la empresa
     *                         ]
     *
     * @return array $arrayRespuesta [
     *                                  "status"                => 'OK' o 'ERROR'
     *                                  "mensaje"               => Mensaje de error
     *                               ]
     *
     * @author Alex Gómez <algomez@telconet.ec>
     * @version 1.0 21/09/2022
     *
     */
    public function reversaPreplanificacionCIH($arrayParametros)
    {
        $arrayParametrosWs = array("prefijoEmpresa"     => $arrayParametros["strPrefijoEmpresa"],
                                   "ipCreacion"         => $arrayParametros["strIpCreacion"],
                                   "usrCreacion"        => $arrayParametros["strUsuarioCreacion"],
                                   "codEmpresa"         => $arrayParametros["strCodEmpresa"],
                                   "origen"             => $arrayParametros["strOrigen"],
                                   "puntoId"            => $arrayParametros["intIdPunto"],
                                   "servicioInternetId" => $arrayParametros["intIdServicioInternet"]);
        $strMensajeError = "";

        try
        {
            
            $arrayTokenCas= $this->serviceTokenCas->generarTokenCas();
        
            if(empty($arrayTokenCas['strToken']))
            {
                throw new \Exception('No se genero el token, por favor reintente.');
            }

            $objOptions         = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER     => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayTokenCas['strToken']
                )
            );

            $strJsonData        = json_encode($arrayParametrosWs);
            $strUrl             = $this->strUrlReversaPreplanificaMs;
            $arrayResponseJson  = $this->serviceRestClient->postJSON( $strUrl , $strJsonData, $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'], true);

            if (
                isset($strJsonRespuesta['code']) && $strJsonRespuesta['code'] == 0
                && isset($strJsonRespuesta['status'])
                && isset($strJsonRespuesta['message'])
            ) 
            {
                $strStatusRespuesta = $strJsonRespuesta['data']['status'];
                $strMensajeRespuesta = $strJsonRespuesta['data']['mensaje'];
            }
            else 
            {
                $strStatusRespuesta       = "ERROR";  
                
                if (!empty($strJsonRespuesta['message']))
                {   
                    $strMensajeRespuesta  = $strJsonRespuesta['message'];                   
                } 
                else 
                {
                    $strMensajeRespuesta  = "No Existe Conectividad con el WS ms-comp-contrato-digital.";
                }
            }
        }
        catch (\Exception $e)
        {
            $strStatusRespuesta     = "ERROR";
            $strMensajeRespuesta    = 'Ha ocurrido un error inesperado y no se ha podido verificar y reversar las solicitudes '
                                      .'de preplanificación por productos CIH. Por favor comuníquese con Sistemas!';

            $strMensajeError        = $e->getMessage();
            error_log("Error al reversar preplanificacion de productos CIH ".$strMensajeError);
        }

        $this->utilService->insertError(
            'Telcos+',
            basename(__CLASS__) . '.' . basename(__FUNCTION__),
            '[Status: '. $strStatusRespuesta. '] [Mensaje: '. 
                (($strMensajeError == "")?$strMensajeRespuesta:$strMensajeError) . ']',
            $arrayParametros["strIpCreacion"],
            $arrayParametros["strUsuarioCreacion"]
        );
        
        $arrayRespuesta = array("status"   => $strStatusRespuesta,
                                "mensaje"  => $strMensajeRespuesta);
        return $arrayRespuesta;
    }

}
