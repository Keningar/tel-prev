<?php

namespace telconet\comercialBundle\Service;
use http\Exception\BadQueryStringException;
use telconet\schemaBundle\DependencyInjection\BaseWSController;
use telconet\comercialBundle\WebService\ComercialMobile\PersonaComplexTypeNew;
use telconet\comercialBundle\WebService\ComercialMobile\ObtenerPersonaResponseNew;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\schemaBundle\Entity\AdmiTipoDocumento;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoPersonaRepresentante;




class ComercialMobileService  extends BaseWSController
{
    
    private $emGeneral;
    private $emComercial;
    private $emFinanciero;
    private $emBiFinanciero;
    private $emComunicacion;    
    private $serviceUtil;
    private $serviceTecnico;
    private $serviceCliente;
    private $serviceComercial;
    private $serviceInfoPunto;    
    private $serviceInfoServicio;
    private $servicePlanificacion;
    private $strFormContactoSitio;
    private $strTipoFactura;
    private $strTipoFacturaProporcional;
    private $serviceInfoContrato;
    private $fileRoot;
    private $pathTelcos;
    private $serviceContratoAprob;
    private $objPuntoRepository;
    private $objInfoDetalleSolRepository;
    private $objInfoDetalleSolHisRepository;
    private $objAdmiRolRepository;
    private $objInfoPuntoRepository;
    private $objInfoServicioRepository;
    private $objInfoServicioHisRepository;
    private $objInfoDocFinanCabRepository;
    private $objAdmiParamCabRepository;
    private $objAdmiParamDetRepository;
    private $objInfoContratoRepository;
    private $objInfoContratoFormaPagoRepository;
    private $objInfoPagoDetRepository;
    private $objInfoPersonaEmpresaRolRepository;
    private $objInfoDocRelacionRepository;
    private $objInfoDocRepository;
    private $objAdmiTipoDocGeneralRepository;
    private $objAdmiTipoSolicitudRepository;
    private $objInfoAdendumRepository;
    private $serviceInfoPersona;
    private $emSeguridad;
    private $strUrlPersonaRecomendacion;  
    private $serviceInfoPersonaFormaContacto;
    private $serviceTokenCas;
    private $serviceRepresentanteLegalMs;

    /**
     *
     * @var \telconet\schemaBundle\Service\RestClientService
     */
    private $serviceRestClient;

    /**
     * setDependencies
     *
     * Función encargada de setear los entities manager de los esquemas de base de datos
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 24-07-2019
     * 
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 17-12-2020 Se instancia en setDependencies todos los repositorios usados por esta clase
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $objContainer - objeto contenedor
     *
     */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer )
    {
        $this->fileRoot                   = $objContainer->getParameter('ruta_upload_documentos');
        $this->pathTelcos                 = $objContainer->getParameter('path_telcos');
        $this->emComercial                = $objContainer->get('doctrine.orm.telconet_entity_manager');
        $this->emGeneral                  = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
        $this->emFinanciero               = $objContainer->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->emBiFinanciero             = $objContainer->get('doctrine.orm.telconet_bifinanciero_entity_manager');
        $this->emComunicacion             = $objContainer->get('doctrine.orm.telconet_comunicacion_entity_manager');
        $this->serviceUtil                = $objContainer->get('schema.Util');
        $this->serviceTecnico             = $objContainer->get('tecnico.InfoServicioTecnico');
        $this->serviceCliente             = $objContainer->get('comercial.Cliente');
        $this->serviceComercial           = $objContainer->get('comercial.Comercial');
        $this->serviceInfoPunto           = $objContainer->get('comercial.InfoPunto');
        $this->serviceInfoServicio        = $objContainer->get('comercial.InfoServicio');
        $this->serviceInfoContrato        = $objContainer->get('comercial.InfoContrato');
        $this->servicePlanificacion       = $objContainer->get('planificacion.Planificar');
        $this->serviceInfoPerFormContacto = $objContainer->get('comercial.InfoPersonaFormaContacto');
        $this->strFormContactoSitio       = $objContainer->getParameter('planificacion.mobile.codFormaContactoSitio');
        $this->strTipoFactura             = $objContainer->getParameter('financiero_tipo_factura');
        $this->strTipoFacturaProporcional = $objContainer->getParameter('financiero_tipo_factura_proporcional');
        $this->serviceContratoAprob       = $objContainer->get('comercial.InfoContratoAprob');
        $this->objPuntoRepository                 = $this->emComercial->getRepository('schemaBundle:InfoPunto');
        $this->objInfoDetalleSolRepository        = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
        $this->objInfoDetalleSolHisRepository     = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist');
        $this->objAdmiRolRepository               = $this->emComercial->getRepository('schemaBundle:AdmiRol');
        $this->objInfoPuntoRepository             = $this->emComercial->getRepository('schemaBundle:InfoPunto');
        $this->objInfoServicioRepository          = $this->emComercial->getRepository('schemaBundle:InfoServicio');
        $this->objInfoServicioHisRepository       = $this->emComercial->getRepository('schemaBundle:InfoServicioHistorial');
        $this->objInfoContratoFormaPagoRepository = $this->emComercial->getRepository('schemaBundle:InfoContratoFormaPago');
        $this->objInfoPersonaEmpresaRolRepository = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol');
        $this->objInfoContratoRepository          = $this->emComercial->getRepository('schemaBundle:InfoContrato');
        $this->objAdmiTipoSolicitudRepository     = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud');
        $this->objAdmiParamCabRepository          = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab');
        $this->objAdmiParamDetRepository          = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet');
        $this->objInfoDocFinanCabRepository       = $this->emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab');
        $this->objInfoPagoDetRepository           = $this->emFinanciero->getRepository('schemaBundle:InfoPagoDet');
        $this->objInfoDocRelacionRepository       = $this->emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion');
        $this->objInfoDocRepository               = $this->emComunicacion->getRepository('schemaBundle:InfoDocumento');
        $this->objAdmiTipoDocGeneralRepository    = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumentoGeneral');
        $this->objInfoAdendumRepository           = $this->emComercial->getRepository('schemaBundle:InfoAdendum');
        $this->serviceInfoPersona                 = $objContainer->get('comercial.InfoPersona');
        $this->emSeguridad                        = $objContainer->get('doctrine.orm.telconet_seguridad_entity_manager');        
        $this->strUrlPersonaRecomendacion         = $objContainer->getParameter('ws_ms_recomendacionPersona_url');
        $this->serviceRestClient                  = $objContainer->get('schema.RestClient');
        $this->serviceInfoPersonaFormaContacto    = $objContainer->get('comercial.InfoPersonaFormaContacto');
        $this->serviceTokenCas                    = $objContainer->get('seguridad.TokenCas');
        $this->serviceRepresentanteLegalMs        = $objContainer->get('comercial.RepresentanteLegalMs');

    }


    /**
     * @author Edgar Pin Villavicencio  <epin@telconet.ec>
     * @version 1.0 27-08-2018  -Carga los catalogos iniciales de la aplicación tm-comercial
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 10-04-2019  -Se carga los catalogos necesarios para la fase de contrato digital
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.2 23-07-2019  -Se agregan los catálogos restantes necesarios para la fase de contrato digital
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.3 05-07-2020  Refactorización de excepciones
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.4 25-11-2021 - Se agrega validaciones mediante parametrizaciones y se crea función obtenerDatosTipoCtaFormaPago
     *                           para envío de valores los datos requeridos para formas de pago y tipo de cuenta por país.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.5 22-12-2022 - Se modifica valor del nombre de parámetro a 'PARAM_CLIENTE_VALIDACIONES' en consulta para las formas de pago.
     *                           Adicional se agrega el tipo de proceso 'PUNTO_ADICIONAL' para consultar los detalles de parámetros.
     */
    public function getCatalogos()
    {
        
        $arrayRespuesta      = array();     
        $arrayCatalogo = array(
            array('k' => 'S', 'v' => 'Si'),
            array('k' => 'N', 'v' => 'No'));
        $arrayRespuesta['response']['siNo'] = $arrayCatalogo;

        //tipoEmpresa
        $arrayTipoEmpresa = array(
            array('k' => 'Publica', 'v' => 'Publica'),
            array('k' => 'Privada', 'v' => 'Privada'));
        $arrayRespuesta['response']['tipoEmpresa'] = $arrayTipoEmpresa;
    
        //tipoOrigenIngreso
        $arrayTipoOrigenIngreso = array(
            array('k' => 'B', 'v' => 'Empleado Público'),
            array('k' => 'V', 'v' => 'Empleado Privado'),
            array('k' => 'I', 'v' => 'Independiente'),
            array('k' => 'A', 'v' => 'Ama de casa o estudiante'),
            array('k' => 'R', 'v' => 'Rentista'),
            array('k' => 'J', 'v' => 'Jubilado'),
            array('k' => 'M', 'v' => 'Remesas del exterior'));
        $arrayRespuesta['response']['tipoOrigenIngreso'] = $arrayTipoOrigenIngreso;

        //tipoTributario
        $arrayTipoTributario = array(
            array('k' => 'NAT', 'v' => 'Natural'),
            array('k' => 'JUR', 'v' => 'Juridico'));
        $arrayRespuesta['response']['tipoTributario'] = $arrayTipoTributario;

        //Titulo
        $arrayTitulos = $this->serviceComercial->obtenerCatalogoTitulos();
        $arrayRespuesta['response']['titulo'] = $arrayTitulos;

        //Genero
        $arrayGenero = array(
            array('k' => 'M', 'v' => 'Masculino'),
            array('k' => 'F', 'v' => 'Femenino'),
            array('k' => 'O', 'v' => 'Otro'));
        $arrayRespuesta['response']['genero'] = $arrayGenero;

        //EstadoCivil
        $arrayEstadoCivil = array(
            array('k' => 'S', 'v' => 'Soltero(a)'),
            array('k' => 'C', 'v' => 'Casado(a)'),
            array('k' => 'U', 'v' => 'Union Libre'),
            array('k' => 'D', 'v' => 'Divorciado(a)'),
            array('k' => 'V', 'v' => 'Viudo(a)'));
        $arrayRespuesta['response']['estadoCivil'] = $arrayEstadoCivil;

        //DireccionTributaria
        $arrayDireccionTributaria = array(
            array('k' => 'NAC', 'v' => 'Nacional'),
            array('k' => 'EXT', 'v' => 'Extranjera'));
        $arrayRespuesta['response']['direccionTributaria'] = $arrayDireccionTributaria;

        //tipoMedio
        $arrayTipoMedio = $this->serviceInfoServicio->obtenerTiposMedio('k', 'v');
        $arrayRespuesta['response']['tipoMedio'] = $arrayTipoMedio;

        // tipoUbicacion
        $arrayTipoUbicacion = $this->serviceInfoPunto->obtenerTiposUbicacion('k', 'v');
        $arrayRespuesta['response']['tipoUbicacion'] = $arrayTipoUbicacion;
 
   
        //Tipo identificación
        $arrayTipo = array(
            array('k' => 'CED', 'v' => 'Cedula'),
            array('k' => 'RUC', 'v' => 'Ruc'),
            array('k' => 'PAS', 'v' => 'Pasaporte'));
        $arrayRespuesta['response']['tipoIdentificacion'] = $arrayTipo;

        //tiposCuenta
        
        //Se consulta parámetros a validar, los parámetros son para MD
        $arrayValidaTipoCta = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->getOne('PARAM_CLIENTE_VALIDACIONES','FINANCIERO','',
                                             'VALIDACION_TIPO_CUENTA','', 'TIPO_CUENTA', '', '', '', 18,'','','PUNTO_ADICIONAL');

        $strCodigoPais = "";
        //Se valida con el parámetro si aplica proceso.
        if($arrayValidaTipoCta["valor1"] == "S")
        { 
            $arrayParamCodPais = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->getOne('PARAM_CLIENTE_VALIDACIONES','FINANCIERO','',
                                             'CODIGO_PAIS_TIPO_CUENTA_MOVIL','', '', '', '', '', 18,'','','PUNTO_ADICIONAL');
                    
            $strCodigoPais = !empty($arrayParamCodPais["valor1"]) ? $arrayParamCodPais["valor1"] : "";
        } 
        
        if($strCodigoPais != "")
        {
            $arrayParamTipoCuenta = array("strCodigoPais"   => $strCodigoPais,
                                          "strTipoProceso"  => $arrayValidaTipoCta["valor2"],
                                          "strKey"          => "k",
                                          "strValue"        => "v");   
            $arrayTipoCuenta = $this->serviceCliente->obtenerDatosTipoCtaFormaPago($arrayParamTipoCuenta);
        }else
        {
            $arrayTipoCuenta = $this->serviceCliente->obtenerTiposCuenta('k', 'v');
        }
        
        for($intCont = 0; $intCont < count($arrayTipoCuenta); $intCont++)
        {
            $arrayBancos = $this->serviceCliente->obtenerBancosTipoCuenta($arrayTipoCuenta[$intCont]['k'], 'k', 'v');
            if(!empty($arrayBancos))
            {
                $arrayTipoCuenta[$intCont]['items'] = $arrayBancos;
            }
        }
        $arrayRespuesta['response']['tiposCuenta'] = $arrayTipoCuenta;
        //tiposDocumentos
        $arrayTipoDocumento = array();
        $objTiposDocumentos = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                              ->findBy(array("estado"     => "Activo",
                                                             "mostrarApp" => "S"));
        foreach($objTiposDocumentos as $objTiposDocumentos)
        {
            $arrayTipoDocumento[] = array('k' => $objTiposDocumentos->getId(), 'v' => $objTiposDocumentos->getDescripcionTipoDocumento());
        }
        $arrayRespuesta['response']['tipoDocumentoGeneral'] = $arrayTipoDocumento;

        //formasContacto
        $arrayParametros = array("strEstado"     => "Activo",
                                 "strMostrarApp" => "S",
                                 "strKey"        => "k",
                                 "strValue"      => "v");

        $arrayFormaContacto = $this->serviceCliente->getFormasContacto($arrayParametros);
        $arrayRespuesta['response']['formasContacto'] = $arrayFormaContacto;

        //formasPago
        
        //Se consulta parámetros a validar, los parámetros son para MD
        $arrayValidaFormaPago = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->getOne('PARAM_CLIENTE_VALIDACIONES','FINANCIERO','',
                                             'VALIDACION_FORMAS_PAGO','', 'FORMA_PAGO', '', '', '', 18,'','','PUNTO_ADICIONAL'); 
        $arrayFormasPago = array();
        //Se valida con el parámetro si aplica proceso.
        if($arrayValidaFormaPago["valor1"] == "S")
        {
            $arrayParamDetFormaPagos = $this->emGeneral->getRepository("schemaBundle:AdmiParametroDet")
                                            ->get("PARAM_CLIENTE_VALIDACIONES", "FINANCIERO", "", 
                                                  "FORMAS_PAGO_WEB_MOVIL", "", "", "", "", "", 18,"","","PUNTO_ADICIONAL");
            foreach($arrayParamDetFormaPagos as $arrayValorDetFormaPagos)
            { 
                $arrayFormasPago[] = $arrayValorDetFormaPagos["valor1"];
            } 
        }
        
        if(!empty($arrayFormasPago))
        {
            $arrayParamFormaPago = array("arrayFormasPago"    => $arrayFormasPago,
                                         "strTipoProceso"     => $arrayValidaFormaPago["valor2"],
                                         "strKey"             => "k",
                                         "strValue"           => "v");
            $arrayFormaPago = $this->serviceCliente->obtenerDatosTipoCtaFormaPago($arrayParamFormaPago);
        }else
        {
            $arrayFormaPago = $this->serviceCliente->obtenerFormasPago('k', 'v');
        }
        
        $arrayRespuesta['response']['formasPago'] = $arrayFormaPago;

        //tipoTributario
        $arrayTipoTributario = array(
                                     array('k' => 'NAT', 'v' => 'Natural'),
                                     array('k' => 'JUR', 'v' => 'Juridico'));
        $arrayRespuesta['response']['tipoTributario'] = $arrayTipoTributario;

        $arrayRespuesta['response']['documentosObligatorio'] = $arrayTipoDocumento;   
        $arrayRespuesta['status']    = $this->status['OK'];
        $arrayRespuesta['message']   = $this->mensaje['OK'];
        $arrayRespuesta['success']   = true;

        //tipoDocumentoGeneral
        return $arrayRespuesta;
    }


    /**
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 27-08-2018  -Carga los catalogos iniciales de la aplicación tm-comercial de una empresa determinada
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.1 11-02-2020  Corrección de sintaxis en catch
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.2 04-02-2021 Se evalua si existe el objeto caso contrario se envía una excepción
     */
    public function getCatalogosEmpresa($arrayData)
    {
        try
        {
            $strCodEmpresa = $arrayData['strCodEmpresa'];
            $objCatalogo = $this->emComercial->getRepository('schemaBundle:AdmiCatalogos')
                                             ->findOneBy(array("codEmpresa" => $strCodEmpresa,
                                                               "tipo"       => "CATALOGOEMPRESA"));
            if ($objCatalogo)
            {
                $strJsonRespuesta = $objCatalogo->getJsonCatalogo();
            }     
            else
            {
                throw new \Exception("No se obtuvo catálogo de empresas, por favor volver a consultar");
                
            }                                              
            

        }
        catch (\Exception $ex)
        {
            $strJsonRespuesta = '{"status": "500","message": "' . $ex->getMessage() . '", "success": true, "token":false';     
        }
        return $strJsonRespuesta;
    }

    /**
     * @return PersonaComplexTypeNew
     */
    /**
     * Método que obtiene datos de un cliente.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 25-07-2019 Se agrega registro de log.
     * 
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.2 11-02-2020  Corrección de sintaxis en catch
     *
     */     
    public function obtenerDatosPersonaPrv($arrayData)
    {
        $strCodEmpresa     = $arrayData['strCodEmpresa'];
        $strIdentificacion = $arrayData['strIdentificacion'];
        $strPrefijoEmpresa = $arrayData['strPrefijoEmpresa'];
        try
        {
            $objPersona = $this->serviceCliente
                               ->obtenerDatosClientePorIdentificacion($strCodEmpresa, $strIdentificacion, $strPrefijoEmpresa);
            if (!is_null($objPersona))
            {
                $objPersona               = new PersonaComplexTypeNew($objPersona);
                $arrayDatosFormasContacto = $this->serviceCliente
                                                 ->obtenerFormasContactoPorPersona($objPersona->id, null, null, null, true);
                $objPersona->setFormasContacto($arrayDatosFormasContacto['registros']);
            }
        }
        catch (\Exception $objException)
        {
            $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $objException->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
            $arrayParametrosLog['creationUser']     = $arrayData['user'];
            $this->serviceUtil->insertLog($arrayParametrosLog);

            throw $objException;
        }
        return $objPersona;
    }    

    /**
     * Obtiene los datos de los planes aplicables
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 18-07-2019 Se modifica para que reciba array de parámetros, se renombran variables según estándar y se agrega registro log
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.2 25-11-2020 Se agrega tipos de códigos promocionales aplicables para cada plan.
     */
    public function obtenerPlanesPrv($arrayParametros)
    {
        
        $strCodEmpresa        = $arrayParametros['strCodEmpresa'];
        $intIdTipoNegocio     = $arrayParametros['intIdTipoNegocio'];
        $intIdFormaPago       = $arrayParametros['intIdFormaPago'];
        $intIdTipoCuenta      = $arrayParametros['intIdTipoCuenta'];
        $intIdBancoTipoCuenta = $arrayParametros['intIdBancoTipoCuenta'];
        
        try
        {
            $arrayTipoCodigoPlanes = $this->objAdmiParamDetRepository
                ->get('PROMOCIONES_APLICABLES_TM_COMERCIAL', 'COMERCIAL', '', '', '', '', 'PLANES', '', '', '18', 'valor7');

            $arrayPlanes = $this->serviceInfoServicio->obtenerPlanesAplicables($strCodEmpresa,
                                                                               $intIdTipoNegocio,
                                                                               $intIdFormaPago,
                                                                               $intIdTipoCuenta,
                                                                               $intIdBancoTipoCuenta);
            $arrayPlanesRpta = array();
            for ($intCont = 0; $intCont < count($arrayPlanes); $intCont++)
            {
                $arrayPlanInfoDetalles = $this->serviceInfoServicio->obtenerPlanInformacionDetalles($arrayPlanes[$intCont]['idPlan'], 
                                                                                                   true, true, 'k', 'v', 'c','t');

                if (!empty($arrayPlanInfoDetalles))
                {
                    $arrayAplicaCodigos = array();

                    foreach ($arrayTipoCodigoPlanes as $arrayTipoCodigo)
                    {
                        if($arrayTipoCodigo['valor2'] == 'INCLUIR_SOLO')
                        {
                            $arrayAplicaCodigos[] = array('k' => $arrayTipoCodigo['valor4'],
                                                          'v' => in_array(strval($arrayPlanes[$intCont]['idPlan']),
                                                                          explode(';', $arrayTipoCodigo['valor1'])) ? '1' : '0',
                                                          'w' => $arrayTipoCodigo['valor5']);
                        }
                        else if($arrayTipoCodigo['valor2'] == 'EXCLUIR_SOLO')
                        {
                            $arrayAplicaCodigos[] = array('k' => $arrayTipoCodigo['valor4'],
                                                          'v' => !in_array(strval($arrayPlanes[$intCont]['idPlan']),
                                                                           explode(';', $arrayTipoCodigo['valor1'])) ? '1' : '0',
                                                          'w' => $arrayTipoCodigo['valor5']);
                        }
                        else
                        {
                            $arrayAplicaCodigos[] = array('k' => $arrayTipoCodigo['valor4'], 'v' => '1', 'w' => $arrayTipoCodigo['valor5']);
                        }
                    }

                    $arrayPlanesRpta[] = array (
                                'k' => $arrayPlanes[$intCont]['idPlan'],
                                'v' => $arrayPlanes[$intCont]['nombrePlan'],
                                'p' => $arrayPlanInfoDetalles['precio'],
                                'l' => $arrayPlanInfoDetalles['listado'],
                                'c' => $arrayAplicaCodigos
                    );
                }
            }
        }
        catch (\Exception $e)
        {
            $arrayPlanesRpta  = array();
            $arrayData        = array("strCodEmpresa"        => $strCodEmpresa,
                                      "intIdTipoNegocio"     => $intIdTipoNegocio,
                                      "intIdFormaPago"       => $intIdFormaPago,
                                      "intIdTipoCuenta"      => $intIdTipoCuenta,
                                      "intIdBancoTipoCuenta" => $intIdBancoTipoCuenta);
            
            $arrayParametrosLog['enterpriseCode']   = $strCodEmpresa;
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $e->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayData);
            $arrayParametrosLog['creationUser']     = 'telcos_mov';  
            $this->serviceUtil->insertLog($arrayParametrosLog);             
            
        }
        
        return $arrayPlanesRpta;
    }
 
    /**
     * Metodo que obtiene la informacion de un cliente
     * 
     * @param array $arrayData
     * @return ObtenerPersonaResponse
     * @author epin <epin@telconet.ec>
     * @version 1.0
     * 
     * Se modifica el método para quedar preparado para recibir adendum
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 - 12-14-2019 - El CRM necesita recuperar la información del cliente indiferente del Rol
     *                             que posea.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.2 - 19-02-2019 - Se agrega la data para planificación mobile desde la consulta de persona.
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.3 - 10-07-2019 - El TelcoCRM necesita recuperar la información del cliente o pre-cliente así no tenga puntos.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.4 - 25-07-2019 - Se migra función desde WS ComercialMobileWSControllerRest. Se agrega consulta de planes.
     * 
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.5 - 16-09-2019 - Cambio en definición de parámetros al instanciar clase ObtenerPersonaResponseNew 
     *                             para compatibilidad con TelcoCRM.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.6 - 26-09-2019 - Validación de fecha fin contrato al obtener clientes de Telconet para compatibilidad
     *                             con TelcoCRM.
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.7 - 28-09-2019 - Se agrega las validaciones a nivel de punto para los adendums y se envía datos de la persona en 
     *                             clientes cancelados
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.8 - 11-02-2020 - Se válida que retorne la información del cliente aún así no tenga puntos,
     *                             cuando el Web Services sea ejecutado por TelcoCRM.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.9 - 10-02-2020 - Se retorna información de forma de pago para prospectos y clientes.
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.10 - 15-05-2020 - Al momento de consultar la información del cliente, se consulta si se debe regularizar la
     *                              información del contrato o adendum.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.11 - 10-06-2020 - Implementación para persona jurídica.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.12 - 21-07-2020 - Envió del usuario en sesión a la función validaAdendum.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.13 - 16-12-2020 - Se agrega funcionalidad para que si hay cliente con contrato activo pero sin servicio activo
     *                              se reverse el contrato
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.14 - 23-12-202 - se agrega validacion de arrayPuntos cuando es cliente cancelado
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.15 - 29-12-2020 -  se corrige llamado a adendum que sea por contrato y por tipo
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.16 - 01-02-2021 - Se corrige para que se busque el servicio activo en todos los puntos 
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.17 - 18-03-2021 - Se corrige para que se busque en todos los puntos servicios que sean considerados como activos para el caso
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.18 - 26-03-2021 - Se corrige para que se regularice la información de la persona cuando se rol de pre-cliente y cliente
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.19 - 26-11-2021 - Se valida la identificación de la persona.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.20 10-01-2022 - Se agrega busqueda de codEmpresa al momento de validar la identificación
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.21 21-02-2022 - Se enriquece el mensaje de salida en el caso de que existan inconvenientes
     *                            al momento de consultar una identificación.
     * 
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.3 01-08-2022  - Se elimino fecha de fin contrato del contrato
     * 
     * @author  Jefferson Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 1-09-2022 - Se consume representante legal por ms  
     * 
     * @author  Miguel Guzman <mguzman@telconet.ec>
     * @version 1.4 03-03-2023 - Verificacion de filtros por empresa
     */
    public function getPersona($arrayData)
    {
        $strFormaContactoSitio     = $this->strFormContactoSitio;
        $arrayPuntos               = array();
        $arrayPlanes               = array();
        $arrayPlanificacion        = array();
        //se agregan variables contrato
        $arrayContrato             = null;
        $strRol                    = null;
        $boolHayServicio           = false;
        $intJurisdiccionId         = 0;
        $boolCoordinada            = false;
        $objFechaPlanificada       = "";
        $boolCancelado             = false;
        $arrayData['aplication']   = isset($arrayData['aplication']) ? $arrayData['aplication'] : "";
        $arrayServiciosAdendum     = ($arrayData['servicios']) ? $arrayData['servicios'] : null;

        try 
        {
            //Validación de identificación
            $arrayParamValidaIdentifica = array(
                'strTipoIdentificacion'     => $arrayData['strTipoIdentificacion'],
                'strIdentificacionCliente'  => $arrayData['strIdentificacion'],
                'intIdPais'                 => "",
                'strCodEmpresa'             => $arrayData['strCodEmpresa']
            );
            $strValidacionRespuesta = $this->emComercial
                ->getRepository('schemaBundle:InfoPersona')
                ->validarIdentificacionTipo($arrayParamValidaIdentifica);

            if (!empty($strValidacionRespuesta))
            {
                $strValidacionRespuesta .= " Para el tipo identificación ".$arrayData['strTipoIdentificacion']." - "
                                            .$arrayData['strIdentificacion'];
                throw new \Exception($strValidacionRespuesta,1);
            }

            $objPersona = $this->obtenerDatosPersonaPrv($arrayData);

            if ($objPersona)
            {
                if($arrayData['strPrefijoEmpresa'] == 'MD' || $arrayData['strPrefijoEmpresa'] == 'EN')
                {
                    //obtiene roles activos o pendientes de la persona
                    $intIdPersonaRol = null;
                    $objRoles = $this->objInfoPersonaEmpresaRolRepository
                        ->getPersonaEmpresaRolPorPersonaPorEmpresaActivos($objPersona->id, $arrayData['strCodEmpresa']);
                    foreach ($objRoles as $objRol) 
                    {
                        $entityRol = $this->objAdmiRolRepository->find($objRol->getEmpresaRolId()->getRolId());
                        if (($entityRol->getTipoRolId()->getDescripcionTipoRol() == "Cliente" || 
                            $entityRol->getTipoRolId()->getDescripcionTipoRol() == "Pre-cliente") && $objRol->getEstado() == "Activo")
                        {
                            $intIdPersonaRol = $objRol->getId();
                            break;
                        }
                    } 
                    if ($intIdPersonaRol)
                    {
                        $arrayParamRegulariza = array('intPersonaEmpresaRolId'  => $intIdPersonaRol,
                            'strCodEmpresa' => $arrayData['strCodEmpresa'],
                            'strUsuario' => $arrayData['user']);
                        $arrayRespContratos   = $this->serviceInfoContrato->regularizarContratoDigital($arrayParamRegulariza);
                    }

                    $arrayRepresentanteLegal= [] ; 
                    $objRepresentanteLegalNatural =  null;     
                    $strRazonComercial =  null; 
                    $strFechaRegistroMercantil =  null; 
                   
                    if ($objPersona->tipoEmpresa!=null && $objPersona->tipoTributario=="JUR")
                     {         
                      $arrayTokenCas      = $this->serviceTokenCas->generarTokenCas();
                      $arrayParamsRepresent    = array( 
                                                      'token'                 => $arrayTokenCas['strToken'],
                                                      'codEmpresa'            => $arrayData['strCodEmpresa'],
                                                      'prefijoEmpresa'        => $arrayData['strPrefijoEmpresa'],  
                                                      'origenWeb'             => 'S',
                                                      'clientIp'              => '127.0.0.1',
                                                      'usrCreacion'           => $arrayData['user'],
                                                      'idPais'                => 1,
                                                      'tipoIdentificacion'    => $objPersona->tipoIdentificacion,
                                                      'identificacion'        => $objPersona->identificacionCliente
                                                      );
                      $objResponseRepresent    =  $this->serviceRepresentanteLegalMs->wsConsultarRepresentanteLegal( $arrayParamsRepresent);
                   
                          if ($objResponseRepresent ['strStatus']=='OK' )
                          {
                              $arrayRepresentanteLegal = $objResponseRepresent ['objData'];      
                              foreach ($arrayRepresentanteLegal as $key => $value) 
                              {                           
                                if ($value['tipoTributario'] == 'NAT') 
                                {       
                                  $objRepresentanteLegalNatural = $value;                             
                                  $objRepresentanteLegalNatural['rol'] = "Representante Legal Juridico";   
                                  $objRepresentanteLegalNatural['formaContacto'] = array();      
                                  $strRazonComercial            =  $objRepresentanteLegalNatural['razonComercial'] ; 
                                  $strFechaRegistroMercantil    =  $objRepresentanteLegalNatural['fechaRegistroMercantil']; 
                                  $intIndexRep =  count($arrayRepresentanteLegal); 
                                }    
                              }
                          }
                          else
                          {
                            throw new \Exception("Fallo en representante legal: ".$objResponseRepresent ['strMensaje'],1);
                          }       
          
                     }
    
                     $objPersona->arrayRepresentanteLegal     =  $arrayRepresentanteLegal;
                     $objPersona->representanteLegalJuridico  =  $objRepresentanteLegalNatural;
                     $objPersona->razonComercial              =  $strRazonComercial;
                     $objPersona->fechaRegistroMercantil      =  $strFechaRegistroMercantil;    
                }

                $strRol          = "Cliente";
                $arrayParametros = array('strCodEmpresa'           => $arrayData['strCodEmpresa'],
                                         'intIdPersona'            => $objPersona->id,
                                         'strRol'                  => $strRol,
                                         'boolFormasContacto'      => true,
                                         'intIdFormaContacto'      => true,
                                         'boolServicios'           => true,
                                         'activaPaginacionPuntos'  => $arrayData['activaPaginacionPuntos'],
                                         'intPagina'               => $arrayData['intPagina'],
                                         'intLimite'               => $arrayData['intLimite'],
                                         'arrayEstadosPuntosTotal' => array('Activo', 'In-Corte', 'Factible','Pendiente'),
                                         'strUsrCreacion'          => $arrayData['user']
                                        );

                $arrayPuntos = $this->serviceInfoPunto->obtenerDatosPuntosClienteAdendum($arrayParametros);

                if ($arrayPuntos && ($arrayPuntos[0]['idPto']))
                {
                    $objPunto    =  $this->objInfoPuntoRepository
                                         ->find($arrayPuntos[0]['idPto']); 
                    $objContrato = $this->objInfoContratoRepository
                                               ->findOneBy(array("personaEmpresaRolId" => $objPunto->getPersonaEmpresaRolId(),
                                                                 "estado"              => 'Activo'));
                    if ( $objContrato)
                    {
                        $entityAdendum =  $this->objInfoAdendumRepository->findOneBy(array("contratoId" => $objContrato->getId(),
                                                                                           "tipo"       => "C"));
                    }                                             
                    
                    if ($entityAdendum)
                    {
                        $objPunto    =  $this->objInfoPuntoRepository
                            ->find($entityAdendum->getPuntoId());
                                      

                        $boolServicioActivo = false;

                        $objServicios = $this->objInfoServicioRepository
                            ->findBy(array("puntoId" => $objPunto->getId())); 
                        $arrayValorParametros =  $this->objAdmiParamDetRepository
                            ->getOne('PARAMETROS_TM_COMERCIAL',
                                'COMERCIAL',
                                '',
                                'ESTADOS_SERVICIO_FACTIBILIDAD',
                                '',
                                '',
                                '',
                                '',
                                '',
                                $arrayData['strCodEmpresa']);
                        $arrayEstadoServicios = ($arrayValorParametros['valor1']) ? $arrayValorParametros['valor1'] : "Factibilidad";
                        $arrayEstadoServicios = explode(",", $arrayEstadoServicios); 
                        foreach ($objServicios as $objServicio) 
                        {                                
                            if ($objServicio->getPlanId() && !in_array($objServicio->getEstado(),$arrayEstadoServicios))
                            {

                                $boolServicioActivo = true;
                                break;
                            }                                                    
                        }
                        if (!$boolServicioActivo)
                        {

                            $objPuntos    =  $this->objInfoPuntoRepository
                                                   ->findBy(array("personaEmpresaRolId" => $objContrato->getPersonaEmpresaRolId()->getId()));
                            foreach ($objPuntos as $objPunto)
                            {
                                $objServicios = $this->objInfoServicioRepository
                                                      ->findBy(array("puntoId" => $objPunto->getId()));
                                foreach ($objServicios as $objServicio) 
                                {                                
                                    if ($objServicio->getPlanId() && !in_array($objServicio->getEstado(),$arrayEstadoServicios))
                                    {
                                        $boolServicioActivo = true;
                                        break;
                                    }                                                    
                                }                                                      

                            }        
                            if (!$boolServicioActivo)
                            {  
                                $arrayRespuesta['objContrato']  = $objContrato;
                                $arrayRespuesta['boolRechazar'] = true;
                                $arrayRespuesta['status']       = '200';
                                $arrayRespuesta['message']      = "Cliente no tiene Servicios Activos!";
                                $arrayRespuesta['success']      = true;
                                return $arrayRespuesta;
                                
                            }               
                            
                        }

                    }
                }
                $objPersona->totalPuntos = (int)$arrayPuntos['total'];
                $arrayPuntos = $arrayPuntos['puntos'];

                if(!$arrayPuntos)
                {
                    $strRol                    = "Pre-cliente";
                    $arrayParametros['strRol'] = $strRol;
                    $arrayParametros['strCodEmpresa'] = $arrayData['strCodEmpresa'];
                    $arrayPuntos = $this->serviceInfoPunto->obtenerDatosPuntosClienteAdendum($arrayParametros);
                    $objPersona->totalPuntos = (int)$arrayPuntos['total'];
                    $arrayPuntos = $arrayPuntos['puntos'];

                }
                
                if(empty($arrayPuntos) && (isset($arrayData['aplication']) && $arrayData['aplication'] == 'TELCOCRM'))
                {
                    $arrayRespuesta                           = array();
                    
                    $arrayParametros['strFormaContactoSitio'] = $strFormaContactoSitio;
                    $arrayParametros['objPuntos']             = $arrayPuntos;
                    $arrayParametros['objPlanes']             = $arrayPlanes;
                    $arrayParametros['objContrato']           = $arrayContrato;
                    $arrayParametros['arrayFechaCupo']        = $arrayPlanificacion;

                    $arrayRespuesta["response"] = new ObtenerPersonaResponseNew($arrayParametros, $objPersona);
                    
                    $arrayRespuesta['status']   = $this->status['OK'];
                    $arrayRespuesta['message']  = $this->mensaje['OK'];
                    $arrayRespuesta['success']  = true;
                    return $arrayRespuesta;
                }

                foreach ($arrayPuntos as $arrayPunto)
                {
                    if ($arrayPunto['cancelado'] == 'S')
                    {
                        $boolCancelado = true;
                        break;
                    }
                    //Aqui va los de los planes para la 2da parte de cotizador
                    
                    $strKey = $arrayPunto['tipoNegocioId'] . '|' . $objPersona->formaPagoId . 
                                                             '|' . $objPersona->tipoCuentaId . 
                                                             '|' . $objPersona->bancoTipoCuentaId;
                    
                    $arrayParametros['strCodEmpresa']        = $arrayData['strCodEmpresa'];
                    $arrayParametros['intIdTipoNegocio']     = $arrayPunto['tipoNegocioId'];
                    $arrayParametros['intIdFormaPago']       = $objPersona->formaPagoId;
                    $arrayParametros['intIdTipoCuenta']      = $objPersona->tipoCuentaId;
                    $arrayParametros['intIdBancoTipoCuenta'] = $objPersona->bancoTipoCuentaId;  
                                         
                    $objPunto    = $this->objInfoPuntoRepository->find($arrayPunto['idPto']);
                    $objContrato = $this->objInfoContratoRepository
                                                     ->findOneBy(array("personaEmpresaRolId" => $objPunto->getPersonaEmpresaRolId(),
                                                                       "estado"              => array('Activo', 'Rechazado')  ));
                    if ($objContrato)
                    {
                        $arrayParametros['intIdFormaPago']       = $objContrato->getFormaPagoId()->getId();
                        
                        $objContratoFp = $this->objInfoContratoFormaPagoRepository
                                                           ->findOneBy(array("contratoId" => $objContrato->getId()));
                        if ($objContratoFp)
                        {
                            $arrayParametros['intIdTipoCuenta']      = $objContratoFp->getTipoCuentaId()->getId();
                            $arrayParametros['intIdBancoTipoCuenta'] = $objContratoFp->getBancoTipoCuentaId()->getId();      
                        }
                                       
                    }
                    
                    if (!isset($arrayPlanes[$strKey])) 
                    {    
                        $arrayPlanes = $this->obtenerPlanesPrv($arrayParametros);
                    }
                    
                    if (isset($arrayPunto['servicios']))
                    {
                        foreach ($arrayPunto['servicios'] as $arrayServicio)
                        {
                            $objSolicitudPrePlanificacion = $this->objInfoDetalleSolRepository
                                                                 ->findOneBy(array("servicioId"      => $arrayServicio['id'],
                                                                                   "tipoSolicitudId" => "8"));
                            if ($objSolicitudPrePlanificacion != null) 
                            {
                                $intJurisdiccionId = $arrayPunto['ptoCoberturaId'];
                                $boolHayServicio = true;
                                if (strtoupper($objSolicitudPrePlanificacion->getEstado()) <> strtoupper('PrePlanificada'))
                                {
                                    if ($arrayServiciosAdendum)
                                    {
                                        if (in_array($arrayServicio['id'], $arrayServiciosAdendum))
                                        {
                                            $objSolicitudPlanificacion = $this->objInfoDetalleSolHisRepository
                                                ->findOneBy(array("detalleSolicitudId" => $objSolicitudPrePlanificacion->getId(),
                                                              "estado"             => "Planificada"));
                                            $objFechaPlanificada =  new \DateTime('now');
                                            if ($objSolicitudPlanificacion != null 
                                                 && ($arrayData['strCodEmpresa'] == '18' 
                                                || $arrayData['strCodEmpresa'] == '33'))
                                            {
                                                $objFechaPlanificada =  $objSolicitudPlanificacion->getFeIniPlan();
                                            }
                                            $boolCoordinada = true;      
                                        }
                                    }
                                    else
                                    {
                                        $objSolicitudPlanificacion = $this->objInfoDetalleSolHisRepository
                                            ->findOneBy(array("detalleSolicitudId" => $objSolicitudPrePlanificacion->getId(),
                                                              "estado"             => "Planificada"));
                                        $objFechaPlanificada =  new \DateTime('now');
                                        if ($objSolicitudPlanificacion != null
                                            && ($arrayData['strCodEmpresa'] == '18'
                                            || $arrayData['strCodEmpresa'] == '33'))
                                        {
                                            $objFechaPlanificada =  $objSolicitudPlanificacion->getFeIniPlan();
                                        }
                                        $boolCoordinada = true;      
                                    }

                                }
                            }
                        }
                    }
                }

                $arrayValidations = $this->validaAdendum(array("personaId"      => $objPersona->id,
                                                               "enterpriseCode" => ($arrayData['strCodEmpresa']),
                                                               "user"           => $arrayData['user']));

                $objPersona->setValidations($arrayValidations['generalValidations']);
                $objPersona->setPointValidations($arrayValidations['pointValidations']);

                if ($boolCancelado)
                {
                    $strRol                    = "Pre-cliente";
                    $arrayParametros['strRol'] = $strRol;
                    $arrayPuntos = $this->serviceInfoPunto->obtenerDatosPuntosClienteAdendum($arrayParametros);
                    $objPersona->totalPuntos = (int)$arrayPuntos['total'];
                    $arrayPuntos = $arrayPuntos['puntos'];

                    if (!$arrayPuntos)
                    {
                        $objPersona->setValidations($arrayValidations['generalValidations']);
                        $objPersona->setPointValidations($arrayValidations['pointValidations']);

                        $arrayParametros['objPuntos']      = $arrayPuntos;
                        $arrayParametros['objPlanes']      = $arrayPlanes;
                        $arrayParametros['objContrato']    = $arrayContrato;                
                        $arrayParametros['arrayFechaCupo'] = $arrayPlanificacion;
                        
                        $arrayRespuesta["response"] = new ObtenerPersonaResponseNew($arrayParametros, $objPersona);
                        $arrayRespuesta['status']   = '200';
                        $arrayRespuesta['message']  = "Cliente no tiene Servicios Activos!";
                        $arrayRespuesta['success']  = true;
                        return $arrayRespuesta;
                    }
    
                    foreach ($arrayPuntos as $arrayPunto) 
                    {
                        if ($arrayPunto['cancelado'] == 'S')
                        {
                            $boolCancelado = true;
                            break;
                        }
                        //Aqui va los de los planes para la 2da parte de cotizador
                        
                        $strKey = $arrayPunto['tipoNegocioId'] . '|' . $objPersona->formaPagoId . 
                                                                 '|' . $objPersona->tipoCuentaId . 
                                                                 '|' . $objPersona->bancoTipoCuentaId;
                        
                        $arrayParametros['strCodEmpresa']        = $arrayData['strCodEmpresa'];
                        $arrayParametros['intIdTipoNegocio']     = $arrayPunto['tipoNegocioId'];
                        $arrayParametros['intIdFormaPago']       = $objPersona->formaPagoId;
                        $arrayParametros['intIdTipoCuenta']      = $objPersona->tipoCuentaId;
                        $arrayParametros['intIdBancoTipoCuenta'] = $objPersona->bancoTipoCuentaId;
                        $objPunto    = $this->objInfoPuntoRepository->find($arrayPunto['idPto']);
                        $objContrato = $this->objInfoContratoRepository
                                                         ->findOneBy(array("personaEmpresaRolId" => $objPunto->getPersonaEmpresaRolId(),
                                                                           "estado"              => array('Activo', 'Rechazado')  ));
                        if ($objContrato)
                        {
                            $objContratoFp = $this->objInfoContratoFormaPagoRepository
                                                               ->findOneBy(array("contratoId" => $objContrato->getId()));
                            if ($objContratoFp)
                            {
                                $arrayParametros['intIdFormaPago']       = $objContrato->getFormaPagoId()->getId();
                                $arrayParametros['intIdTipoCuenta']      = $objContratoFp->getTipoCuentaId()->getId();
                                $arrayParametros['intIdBancoTipoCuenta'] = $objContratoFp->getBancoTipoCuentaId()->getId();      
                            }
                                           
                        }
    
                        if (!isset($arrayPlanes[$strKey])) 
                        {
                            $arrayPlanes = $this->obtenerPlanesPrv($arrayParametros);
                        }
                        
                        if (isset($arrayPunto['servicios']))
                        {



                            
                            foreach ($arrayPunto['servicios'] as $arrayServicio)
                            {
                                $objSolicitudPrePlanificacion = $this->objInfoDetalleSolRepository
                                                                     ->findOneBy(array("servicioId"      => $arrayServicio['id'],
                                                                                       "tipoSolicitudId" => "8"));
                                if ($objSolicitudPrePlanificacion != null) 
                                {
                                    $intJurisdiccionId = $arrayPunto['ptoCoberturaId'];
                                    $boolHayServicio = true;
                                    if (strtoupper($objSolicitudPrePlanificacion->getEstado()) <> strtoupper('PrePlanificada'))
                                    {
                                        if ($arrayServiciosAdendum)
                                        {
                                            if (in_array($arrayServicio['id'], $arrayServiciosAdendum))
                                            {
                                                $objSolicitudPlanificacion = $this->objInfoDetalleSolHisRepository
                                                ->findOneBy(array("detalleSolicitudId" => $objSolicitudPrePlanificacion->getId(),
                                                                  "estado"             => "Planificada"));
                                                $objFechaPlanificada =  new \DateTime('now');
                                                if ($objSolicitudPlanificacion != null 
                                                    && ($arrayData['strCodEmpresa'] == '18' 
                                                    || $arrayData['strCodEmpresa'] == '33'))
                                                {
                                                    $objFechaPlanificada =  $objSolicitudPlanificacion->getFeIniPlan();
                                                }
                                                $boolCoordinada = true;      
                                            }
                                        }
                                        else
                                        {
                                            $objSolicitudPlanificacion = $this->objInfoDetalleSolHisRepository
                                            ->findOneBy(array("detalleSolicitudId" => $objSolicitudPrePlanificacion->getId(),
                                                              "estado"             => "Planificada"));
                                            $objFechaPlanificada =  new \DateTime('now');
                                            if ($objSolicitudPlanificacion != null 
                                                && ($arrayData['strCodEmpresa'] == '18' 
                                                || $arrayData['strCodEmpresa'] == '33'))
                                            {
                                                $objFechaPlanificada =  $objSolicitudPlanificacion->getFeIniPlan();
                                            }
                                            $boolCoordinada = true;      
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if ($boolCancelado)
                {
                    $objPersona->setValidations($arrayValidations['generalValidations']);
                    $objPersona->setPointValidations($arrayValidations['pointValidations']);
                    $arrayParametros['objPuntos']      = $arrayPuntos;
                    $arrayParametros['objPlanes']      = $arrayPlanes;
                    $arrayParametros['objContrato']    = $arrayContrato;
                    $arrayParametros['arrayFechaCupo'] = $arrayPlanificacion;

                    $arrayRespuesta["response"] = new ObtenerPersonaResponseNew($arrayParametros, $objPersona);
                    $arrayRespuesta['status']   = '200';
                    $arrayRespuesta['message']  = "Cliente no tiene Servicios Activos!";
                    $arrayRespuesta['success']  = true;
                    return $arrayRespuesta;
                }

                if ($boolHayServicio)
                {
                    $objServicePlanificacion = $this->servicePlanificacion;
                    $arrayPlanificacion = array();
                    if ($objFechaPlanificada)
                    {
                        $arrayPlanificacion[] = array("intervaloProgramacion" => null,
                                                      "planificacionHorarios" => null,
                                                      "estadoProgramacion" => "Ya posee una solicitud de instalación planificada \n" . 
                                                      "con Fecha: " . $objFechaPlanificada->format('d/m/Y H:i'));
                    }
                    if (!$boolCoordinada)
                    {
                        $arrayPlanificacion = $objServicePlanificacion->getCuposMobil(array("intJurisdiccionId" => $intJurisdiccionId));
                    }
                }
                if ($strRol == "Cliente")
                {
                    $arrayEstadoContrato   = array('Activo');
                    $arrayEstadoRol        = array('Activo');
                }
                else
                {
                    $arrayEstadoContrato   = array('Pendiente','PorAutorizar');
                    $arrayEstadoRol        = array('Activo','Pendiente');
                }                
                $objRol = $this->objInfoPersonaEmpresaRolRepository
                                            ->findPersonaEmpresaRolByParams($objPersona->id, $arrayData['strCodEmpresa'],
                                                                            $arrayEstadoRol, $strRol);
                if ($objRol) 
                {
                    $objContrato = $this->objInfoContratoRepository
                                                     ->findOneBy(array("personaEmpresaRolId" => $objRol->getId(),
                                                                       "estado"              => $arrayEstadoContrato));
                    if (is_object($objContrato)) 
                    {
                        $arrayContrato = array();
                        $arrayContrato['formaPagoId']          = $objContrato->getFormaPagoId()->getId();
                        $arrayContrato['numeroContratoEmpPub'] = $objContrato->getNumeroContratoEmpPub();
                        $arrayContrato['valorContrato']        = $objContrato->getValorContrato();
                        $arrayContrato['valorAnticipo']        = $objContrato->getValorAnticipo();
                        $arrayContrato['valorGarantia']        = $objContrato->getValorGarantia();
                        $arrayContrato['tipoContratoId']       = $objContrato->getTipoContratoId()->getId();
                        $arrayContrato['personaEmpresaRolId']  = $objRol->getId();
                        $arrayContrato['feFinContratoPost']    = ($objContrato->getFeFinContrato()) ? 
                        $objContrato->getFeFinContrato()->format('d/m/Y')   : "";
                        $arrayContrato['feCreacion']           = ($objContrato->getFeCreacion()) ?  
                        $objContrato->getFeCreacion()->format('d/m/Y')   : "";
                        $arrayContrato['origen']               = $objContrato->getOrigen();
                        $arrayContrato['idContrato']  	       = $objContrato->getId();
                        $arrayContrato['estado']               = $objContrato->getEstado();
                        $arrayContrato['numeroContrato']       = $objContrato->getNumeroContrato();
                        /* @var $objContratoFormaPago \telconet\schemaBundle\Repository\InfoContratoFormaPago */
                        $objContratoFormaPago                  = $this->objInfoContratoFormaPagoRepository
                                                                      ->findOneBy(array('contratoId' => $objContrato->getId()));
                        if (is_object($objContratoFormaPago)) 
                        {
                            $arrayContrato['tipoCuentaId']       = $objContratoFormaPago->getTipoCuentaId()->getId();
                            $arrayContrato['bancoTipoCuentaId']  = $objContratoFormaPago->getBancoTipoCuentaId()->getId();
                            $arrayContrato['numeroCtaTarjeta']   = $objContratoFormaPago->getNumeroCtaTarjeta();
                            $arrayContrato['titularCuenta']      = $objContratoFormaPago->getTitularCuenta();
                            $arrayContrato['mesVencimiento']     = $objContratoFormaPago->getMesVencimiento();
                            $arrayContrato['anioVencimiento']    = $objContratoFormaPago->getAnioVencimiento();
                            $arrayContrato['codigoVerificacion'] = $objContratoFormaPago->getCodigoVerificacion();
                        }
                        else 
                        {
                            $arrayContrato['tipoCuentaId']       = null;
                            $arrayContrato['bancoTipoCuentaId']  = null;
                            $arrayContrato['numeroCtaTarjeta']   = null;
                            $arrayContrato['titularCuenta']      = null;
                            $arrayContrato['mesVencimiento']     = null;
                            $arrayContrato['anioVencimiento']    = null;
                            $arrayContrato['codigoVerificacion'] = null;
                        }

                        $objInfoDocumentoRelacion = $this->objInfoDocRelacionRepository;
                        /* @var $objInfoDocumentoRelacion \telconet\schemaBundle\Repository\InfoDocumentoRelacion */
                        $arrayDocumentos          = $objInfoDocumentoRelacion->findBy(array('contratoId' => $objContrato->getId()));
                        if ($arrayDocumentos) 
                        {
                            $arrayContrato['numeroFiles'] = count($arrayDocumentos);
                            $arrayFile = array();
                            foreach ($arrayDocumentos as $arrayDocumento)
                            {   
                                $objInfoDocumento = $this->objInfoDocRepository
                                                                         ->findOneBy(array('id' => $arrayDocumento->getDocumentoId()));
                                if ( $objInfoDocumento && $objInfoDocumento->getTipoDocumentoId() && 
                                     $objInfoDocumento->getTipoDocumentoId()->getId() == 10)
                                {
                                    $boolExiste = false;
                                    foreach ($arrayFile as $arrayBusca)
                                    {
                                        if ($arrayBusca["tipoDocumentoGeneralId"] == $objInfoDocumento->getTipoDocumentoGeneralId())
                                        {
                                            $boolExiste = true;
                                        }
                                    }
                                    if (!$boolExiste)
                                    {
                                        $objTipoDocumento = $this->objAdmiTipoDocGeneralRepository
                                                                 ->findOneBy(array('id' => $objInfoDocumento->getTipoDocumentoGeneralId()));
                                        $arrayFileDoc = [];
                                        $arrayFileDoc["digitalFileUri"]         = $objInfoDocumento->getUbicacionFisicaDocumento(); 
                                        $arrayFileDoc["digitalFileName"]        = $objTipoDocumento->getDescripcionTipoDocumento();
                                        $arrayFileDoc["tipoDocumentoGeneralId"] = $objInfoDocumento->getTipoDocumentoGeneralId();
                                        $arrayFile[] = $arrayFileDoc;
                                    }
                                    
                                }
                                
                            }    
                            $arrayContrato['files'] = $arrayFile;
                        }
                        else 
                        {
                            $arrayContrato['numeroFiles'] = 0;
                        }
                        if ($boolHayServicio) 
                        {
                            $objServicePlanificacion = $this->servicePlanificacion;
                            $arrayPlanificacion = array();
                            if ($objFechaPlanificada)
                            {
                                $arrayPlanificacion[] = array("intervaloProgramacion" => null,
                                                              "planificacionHorarios" => null,
                                                              "estadoProgramacion"    => "Ya posee una solicitud de instalación planificada \n" . 
                                                              "con Fecha: " . $objFechaPlanificada->format('d/m/Y H:i'));
                            }
                            if (!$boolCoordinada)
                            {
                                $arrayPlanificacion = $objServicePlanificacion->getCuposMobil(array("intJurisdiccionId" => $intJurisdiccionId));
                            }
                        }
                    }
                }
                $arrayRespuesta = array();
                $arrayParametros['objPuntos']                = $arrayPuntos;
                $arrayParametros['objPlanes']                = $arrayPlanes;
                $arrayParametros['objContrato']              = array();
                $arrayParametros['arrayFechaCupo']           = $arrayPlanificacion;
                if(!empty($arrayRespContratos['arrayValidacionServ']))
                {
                    foreach($arrayRespContratos['arrayValidacionServ'] as $arrayValidaciones)
                    {
                        $arrayValidacion[] = array("description"  => "contractStatus",
                                                    "message"     => $arrayValidaciones['strMensaje'],
                                                    "restricted"  => true);
                        $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
                        $arrayParametrosLog['logType']          = "1";
                        $arrayParametrosLog['logOrigin']        = "TELCOS";
                        $arrayParametrosLog['application']      = basename(__FILE__);
                        $arrayParametrosLog['appClass']         = basename(__CLASS__);
                        $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                        $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                        $arrayParametrosLog['messageUser']      = "ERROR";
                        $arrayParametrosLog['status']           = "Fallido";
                        $arrayParametrosLog['creationUser']     = $arrayData['user'];
                        $arrayParametrosLog['descriptionError'] = $arrayValidaciones['strDescripcion'];
                        $arrayParametrosLog['inParameters']     = json_encode($arrayValidaciones, 128);
                        $this->serviceUtil->insertLog($arrayParametrosLog);
                    }
                    $objPersona->setValidations($arrayValidacion);
                }

                if($strRol == 'Cliente')
                {
                    $objPersona->formaPagoId       = $arrayParametros['intIdFormaPago'];
                    $objPersona->tipoCuentaId      = $arrayParametros['intIdTipoCuenta'];
                    $objPersona->bancoTipoCuentaId = $arrayParametros['intIdBancoTipoCuenta'];
                }
                
                $arrayRespuesta["response"]  = new ObtenerPersonaResponseNew($arrayParametros, $objPersona);
                $arrayRespuesta['status']    = $this->status['OK'];
                $arrayRespuesta['message']   = $this->mensaje['OK'];
                $arrayRespuesta['success']   = true;
            }
            else
            {

                $boolIsRecomendacion=false;
                if(empty($arrayData['tokenCas']))
                {
                    $boolIsRecomendacion=false;
                }
                else
                {
                    
 
                $arrayParametros= array("token"                            => $arrayData['tokenCas'],
                                        "strIpMod"                         => $arrayData['strIpMod'],
                                        "strUserMod"                       => $arrayData['strUserMod'],
                                        "opcion"                           => "CONSULTA_DATOS_PERSONA",
                                        "comandoConfiguracion"             => "NO",
                                        "ejecutaComando"                   => "NO",
                                        "actualiza_datos"                  => "NO",
                                        "empresa"                          => $arrayData['strPrefijoEmpresa'],
                                        "ipCreacion"                       => $arrayData['strIpMod'],
                                        "usrCreacion"                      => $arrayData['user'],
                                        "datos"               => array(
                                            "identificacion"               => $arrayData['strIdentificacion'],
                                            "tipoIdentificacion"           => substr($arrayData['strTipoIdentificacion'], 0,1)
                                        ));

                error_log(json_encode($arrayParametros));    

                 $arrayRespuestaMS         = $this->verificarRecomendacion($arrayParametros); 
                error_log(json_encode($arrayRespuestaMS));    
               
                if($arrayRespuestaMS['strStatus'] == "OK")
                {
                    $boolIsRecomendacion=true;
                    $arrayRespuesta['personaRecomendada']  = $arrayRespuestaMS['data'] ; 
                }
                

                }
                $arrayRespuesta['isRecomendacion']    = $boolIsRecomendacion;
                $arrayRespuesta['status']             = $this->status['ERROR_PARCIAL'];
                $arrayRespuesta['message']            = "Cliente no Existe!";
                $arrayRespuesta['success']            = true;
            }
        }
        catch(\Exception $objException)
        {
            $arrayRespuesta['response']= array();
            $arrayRespuesta['success'] = false;
            $arrayRespuesta['status']  = $this->status['ERROR'];
            $arrayRespuesta['message'] = ($objException->getCode() == 1)
                                             ? $objException->getMessage()
                                             : $this->mensaje['ERROR'];

            $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $objException->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
            $arrayParametrosLog['creationUser']     = $arrayData['user'];
            $this->serviceUtil->insertLog($arrayParametrosLog);
        }
        return $arrayRespuesta;
    }
    
    /**
     * Método que obtiene la factibilidad de un punto mediante sus coordenadas
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 22-07-2019 Se agrega registro de log, se modifica formato de respuesta.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.2 20-11-2019 Se agrega planes en el envio de data de persona
     */    
    public function solicitarFactibilidadServicio($arrayData)
    {
        $strClientIp = '127.0.0.1';
        try
        {
            $arrayResult = $this->serviceInfoServicio->solicitarFactibilidadServicio($arrayData['codEmpresa'], 
                                                                                     $arrayData['prefijoEmpresa'], 
                                                                                     $arrayData['idServicio'], 
                                                                                     $arrayData['usrCreacion'], 
                                                                                     $strClientIp);

            $objInfoServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayData['idServicio']);
            
            if(is_object($objInfoServicio))
            {
                $intIdPunto   = $objInfoServicio->getPuntoId();
                
                $objInfoPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);

                if(is_object($objInfoPunto))
                {
                    $strIdentificacion = $objInfoPunto->getPersonaEmpresaRolId()->getPersonaId()->getIdentificacionCliente();
                }
            }
          
            $arrayPersonaResponse = $this->getPersona(array("strCodEmpresa"     => $arrayData['codEmpresa'],
                                                            "strPrefijoEmpresa" => $arrayData['prefijoEmpresa'],
                                                            "strIdentificacion" => $strIdentificacion,
                                                            "user"              => $arrayData['usrCreacion']));
            $objPersona = $arrayPersonaResponse['response']->persona;
            $objPlanes  = $arrayPersonaResponse['response']->planes;


          
            
            $arrayResponse['response'] = array('persona'=> $objPersona,
                                               'planes' => $objPlanes);
            
            $arrayResponse['status']   = $this->status['OK'];
            $arrayResponse['message']  = $arrayResult;
            $arrayResponse['success']  = true;
            $arrayResponse['token']    = null ;
           
        }
        catch (\Exception $e)
        {
            $arrayResponse['response']  = array();
            $arrayResponse['status']    = $this->status['ERROR'];
            $arrayResponse['message']   = $this->mensaje['ERROR'];
            $arrayResponse['success']   = true ;
            $arrayResponse['token']     = null ;
             
            $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $e->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
            $arrayParametrosLog['creationUser']     = $arrayData['usrCreacion'];  
            $this->serviceUtil->insertLog($arrayParametrosLog);             
            
        }
        return $arrayResponse;
    }

    /**
     * Método que genera el login de un punto
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 29-07-2019 Ingreso de log.
     */    
    public function getLoginPunto($arrayData)
    {
        try
        {        
            $strLogin = $this->serviceInfoPunto->generarLogin($arrayData['codEmpresa'], $arrayData['idCanton'], $arrayData['idPersona'], 
                                                       (empty($arrayData['idTipoNegocio']) ? null : $arrayData['idTipoNegocio']));

            $arrayResponse['response'] = array('login' => $strLogin); 
            $arrayResponse['status']   = $this->status['OK'];
            $arrayResponse['message']  = $this->mensaje['OK'];
            $arrayResponse['success']  = true;
            $arrayResponse['token']    = null ;
        
        }
        catch (\Exception $e)
        {
            $arrayResponse['response']  = array();
            $arrayResponse['status']    = $this->status['ERROR'];
            $arrayResponse['message']   = $this->mensaje['ERROR'];
            $arrayResponse['success']   = true ;
            $arrayResponse['token']     = null ;
             
            $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $e->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
            $arrayParametrosLog['creationUser']     = $arrayData['usrCreacion'];  
            $this->serviceUtil->insertLog($arrayParametrosLog);             
            
        }        
            
        return $arrayResponse;
    }    


    /**
     * Método que devuelve el estado de la factura y el saldo del cliente, se mueve lógica de controlador a service
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 26-09-2019
     * 
     * $arrayData[
     *            pointId        => Punto del que se va a obtener el saldo del cliente,
     *            enterpriseCode => Codigo de la empresa,
     *            pointAdendum   => Punto del que se va a consultar el saldo de la factura de instalacion  
     *           ]
     * $arrayResponse[
     *                response[
     *                         estado => Trae el estado de la factura, devuelve "N/A" si no posee factura de instalación,
     *                         saldo  => Devuelve el saldo pendiente de pagar del cliente por el punto
     *                        ],
     *                status  => OK o ERROR,
     *                message => OK o mensaje de error,
     *                success => true
     *               ]
     */        
    public function getEstadoCuentaCliente($arrayData)
    {        
        $intPunto         = $arrayData['pointId'];
        $strCodEmpresa    = isset($arrayData['enterpriseCode']) ? $arrayData['enterpriseCode'] : 18;
        $intPuntoAdendum  = $arrayData['pointAdendum'];
        $intFacturaId     = $this->strTipoFactura;
        $intFacturaPropId = $this->strTipoFacturaProporcional;
        $arrayResponse    = array();
        /* @var $serviceSeguridad SeguridadService */
        try
        {
            $arraySaldoPunto = $this->objInfoDocFinanCabRepository
                                            ->getPuntosFacturacionAndFacturasAbiertasByIdPunto($intPunto, $this->emComercial, $strCodEmpresa);
            $strEstado       = $this->objInfoDocFinanCabRepository
                                            ->getEstadoFacturaInstalacion(array("intPuntoId" => $intPuntoAdendum,
                                                                                "arrayTipo"  => array($intFacturaId, 
                                                                                                      $intFacturaPropId)));
            $arrayResponse['response']['estado'] = $strEstado == "" ? "N/A" : $strEstado;
            $arrayResponse['response']['saldo']  = number_format(floatval($arraySaldoPunto["saldoCliente"]), 2, ".", "");
            $arrayResponse['response']['fecha']  = $arraySaldoPunto['fechaCliente'];
        }
        catch (\Exception $e)
        {
            $arrayParametrosLog['enterpriseCode']   = $strCodEmpresa;
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $e->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
            $arrayParametrosLog['creationUser']     = $arrayData['user'];

            $this->serviceUtil->insertLog($arrayParametrosLog);

            $arrayResponse['status']                  = $this->status['ERROR'];
            $arrayResponse['message']                 = $this->mensaje['ERROR'];
            $arrayResponse['success']                 = true ;
            return $arrayResponse;
        }
        $arrayResponse['status']                  = $this->status['OK'];
        $arrayResponse['message']                 = $this->mensaje['OK'];
        $arrayResponse['success']                 = true ;
        return $arrayResponse;
    }


    /**
     * Método que Valida si se puede guardar un adendum
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 26-09-2019
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.1 10-06-2020 - Se quita validación de solicitud de retiro de equipo pendiente
     * 
     * @author Jefferson Carrillo <jacarrillo@telconet.ec>
     * @version 1.2 14-12-2021 - Se eliminaron las validación de verificar saldo y formas de pago porque fueron migradas a ms.
     *
     * @param $arrayData[
     *                   personaId      => Id de la persona de la que se va a traer la información,
     *                   enterpriseCode => Codigo de la empresa,
     *                  ]
     *
     * @return $arrayResponse[]
     */
    public function validaAdendum($arrayParametros)
    {
        $arrayResponse                   = array();
        $arrayResponseGeneralValidations = array();
        $arrayResponsePointValidations   = array();
        $strMensajeFormaPago             = null;

        $arrayValorParametros = $this->objAdmiParamDetRepository
            ->getOne('PARAMETROS_TM_COMERCIAL', 'COMERCIAL', '', 'DIAS DE VENCIMIENTO DE SALDO', '', '', '', '', '', '18');

        $arrayPersonaEmpresaRol = $this->objInfoPersonaEmpresaRolRepository->findOneByPersonaId($arrayParametros['personaId']);
        $objSolicitudRetiroEquipo = $this->objAdmiTipoSolicitudRepository->findOneBy(array('descripcionSolicitud' => 'SOLICITUD RETIRO EQUIPO',
                                                                                           'estado'               => 'Activo'));

        if(is_object($objSolicitudRetiroEquipo))
        {
            $intSolRetiroEquipoId = $objSolicitudRetiroEquipo->getId();
        }
        else
        {
            throw new \Exception("No se pudo obtener el id de la solicitud de retiro de equipos", 1);
        }

        $arrayPersonaEmpresaRol = $this->objInfoPersonaEmpresaRolRepository
                                                    ->findBy(array('personaId' => $arrayParametros['personaId'] ));
        $objSolicitudRetiroEquipo = $this->objAdmiTipoSolicitudRepository->findOneBy(array('descripcionSolicitud' => 'SOLICITUD RETIRO EQUIPO',
                                                                                           'estado'               => 'Activo'));

        if(is_object($objSolicitudRetiroEquipo))
        {
            $intSolRetiroEquipoId = $objSolicitudRetiroEquipo->getId();
        }
        else
        {
            throw new \Exception("No se pudo obtener el id de la solicitud de retiro de equipos", 1);
        }

        foreach ($arrayPersonaEmpresaRol as $objPersonaEmpresaRol)
        {
            if ( $objPersonaEmpresaRol && $objPersonaEmpresaRol->getEmpresaRolId()->getEmpresaCod()->getId() == 18)
            {
                $objPuntos = $this->objPuntoRepository
                                               ->findBy(array("personaEmpresaRolId" => $objPersonaEmpresaRol->getId(),
                                                              "estado"              => array("Activo", "In-Corte", "Cancelado")));
                foreach ($objPuntos as $objPunto)
                {
                    $arrayParamEstadoCuenta = array("pointId"        => $objPunto->getId(),
                                                    "enterpriseCode" => $arrayParametros["enterpriseCode"],
                                                    "pointAdendum"   => $objPunto->getId(),
                                                    "user"           => $arrayParametros["user"]);

                    $arrayEstadoCuenta = $this->getEstadoCuentaCliente($arrayParamEstadoCuenta);

                    if ($arrayEstadoCuenta['response']['estado'] !== "N/A" || $arrayEstadoCuenta['response']['saldo'] != "0.00")
                    {
                        $arrayValorParametros = $this->objAdmiParamDetRepository
                                                ->getOne('PARAMETROS_TM_COMERCIAL',
                                                        'COMERCIAL',
                                                        '',
                                                        'DIAS DE VENCIMIENTO DE SALDO',
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        '18');
                        $objFecha = new \DateTime($arrayEstadoCuenta['response']['fecha']);
                        $floatValor = 0;

                        if(isset($arrayValorParametros['valor1']))
                        {
                            $strDias = $arrayValorParametros['valor1'];
                            $objFecha->add(new \DateInterval('P' . $strDias . 'D'));
                            $intValor = ($arrayValorParametros['valor2']) ? $arrayValorParametros['valor2'] : 0;
                            $floatValor = floatval($intValor);
                        }
                    }
                    
                    $strJsonSolicitudes = $this->objInfoDetalleSolRepository
                                ->getDetalleSolicitudesByCriterios(array('strLogin'           =>$objPunto->getLogin(),
                                                                        'intIdTipoSolicitud' => $intSolRetiroEquipoId,
                                                                        'strCodEmpresa'      => $arrayParametros['enterpriseCode']));
                                                             
                    if( $strJsonSolicitudes )
                    {
                        $arrayTmpSolicitudesResultado = $strJsonSolicitudes;
                        

                        if((int)$arrayTmpSolicitudesResultado['total'] > 0 )
                        {
                            $arrayTmpSolicitudes  = $arrayTmpSolicitudesResultado['registros'];
                            $objEstadoSolicitud = null;

                            foreach ($arrayTmpSolicitudes as $objSolicitud)
                            {
                                if ($objSolicitud['intIdPunto'] == $objPunto->getId())
                                {
                                    $objEstadoSolicitud = $objSolicitud;
                                    break;
                                }
                            }

                            if ($objEstadoSolicitud)
                            {
                                $entityRol = $this->objAdmiRolRepository
                                                                ->find($objPersonaEmpresaRol->getEmpresaRolId()->getRolId());
                                if (($entityRol && $entityRol->getDescripcionRol() == "Cliente" &&
                                    ($objPersonaEmpresaRol->getEstado() == "Cancelado" || $objPunto->getEstado() == "Cancelado")
                                    && $objEstadoSolicitud['estado'] != 'Finalizada'))
                                {

                                    $arrayResponsePointValidations[] = array("pointId"     => $objPunto->getId(),
                                        "pointLogin"  => $objPunto->getLogin(),
                                        "description" => "deliverEquipment",
                                        "message"     => "Posee una solicitud de retiro de equipos PENDIENTE",
                                        "restricted"  => false);
                                }
                            }
                            else
                            {
                                //valido que la factura no tenga saldo pendiente
                                //Consulto la persona_empresa_rol_id del rol Cliente estado CANCEL - CANCELADO
                                $entityRol = $this->objAdmiRolRepository
                                                                ->find($objPersonaEmpresaRol->getEmpresaRolId()->getRolId());
                                if ($entityRol && $entityRol->getDescripcionRol() == "Cliente" && $objPersonaEmpresaRol->getEstado() == "Cancelado")
                                {
                                    $arrayServicios = $this->objInfoServicioRepository
                                                                        ->findBy(array("puntoId" => $objPunto->getId()));
                                    foreach($arrayServicios as $objServicio)
                                    {
                                        if ($objServicio && $objServicio->getPlanId() != null )
                                        {
                                            $entityServicioHist = $this->objInfoServicioHisRepository
                                                                                    ->findOneBy(array("servicioId" => $objServicio->getId(),
                                                                                                      "estado"     =>  array('Cancel',
                                                                                                                             'Cancelado',
                                                                                                                             'Asignada')));
                                            if ($entityServicioHist)
                                            {
                                                $entityFactura = $this->objInfoDocFinanCabRepository
                                                                                     ->findBy(array("puntoId"             => $objPunto->getId(),
                                                                                                    "estadoImpresionFact" => "Activo",
                                                                                                     "tipoDocumentoId"    => array(1, 5)));
                                                if ($entityFactura)
                                                {
                                                    $arrayResponsePointValidations[] = array("pointId"     => $objPunto->getId(),
                                                                                             "pointLogin"  => $objPunto->getLogin(),
                                                                                             "description" => 'inVoiceEquipment',
                                                                                             "message"     =>
                                                                                                "Tiene factura pendiente de entrega de equipos",
                                                                                             "restricted"  => false);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $arrayResponse = array('generalValidations' => $arrayResponseGeneralValidations,
                               'pointValidations'   => $arrayResponsePointValidations);
        return $arrayResponse;
    }
    
    /**
     * Método que valida las formas de pagos homologadas
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 26-09-2019
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.1 28-01-2020 Homologación y validación de formas de pagos
     * 
     * @param $arrayParametros [
     *                              puntoId             => Id del punto,
     *                              personaEmpresaRolId => Id de la persona empresa rol
     *                         ]
     * 
     * @return $strMensaje
     */    
    public function getUltimasFacturasFormaPago($arrayParametros)
    {
        $strMensaje                 = "";
        $strValidaHomologacion      = 'NO';
        $intCantidadFacturasValidar = 3;
        $arrayFormasPagoValida      = array();
        $arrayFormasPagoHomologadas = array();
        
        $objContrato = $this->objInfoContratoRepository
             ->findOneBy(array("personaEmpresaRolId" => $arrayParametros['personaEmpresaRolId'],
                               "estado"              => "Activo"));
        
        if (is_object($objContrato))
        {
            $objParamCabCategoria = $this->objAdmiParamCabRepository
                ->findOneBy(array('nombreParametro' => 'HOMOLOGACION_FORMAS_DE_PAGO',
                                  'estado'          => 'Activo'));

            if (is_object($objParamCabCategoria))
            {
                $arrayParamDetCategoria = $this->objAdmiParamDetRepository
                    ->findBy(array('parametroId' => $objParamCabCategoria->getId(),
                                   'estado'      => 'Activo'));

                if (!is_null($arrayParamDetCategoria) && !empty($arrayParamDetCategoria))
                {
                    foreach ($arrayParamDetCategoria as $objParamDetCategoria)
                    {
                        $arrayFormasPagoValida["{$objParamDetCategoria->getValor1()}"] =
                            array('valida'            => strtoupper(trim($objParamDetCategoria->getValor3())),
                                  'formas_pago'       => array_filter(explode('|', $objParamDetCategoria->getValor2())),
                                  'cantidad_facturas' => intval($objParamDetCategoria->getValor4()));
                    }

                    $arrayFormaPagoContrato = $arrayFormasPagoValida[$objContrato->getFormaPagoId()->getId()];
                    
                    if(!is_null($arrayFormaPagoContrato) && !empty($arrayFormaPagoContrato))
                    {
                        
                        $strValidaHomologacion      = (isset($arrayFormaPagoContrato['valida']) && 
                                                        !empty($arrayFormaPagoContrato['valida']))
                                                            ? $arrayFormaPagoContrato['valida']
                                                            : 'NO';
                        
                        $arrayFormasPagoHomologadas = (isset($arrayFormaPagoContrato['formas_pago']) && 
                                                        !empty($arrayFormaPagoContrato['formas_pago']))
                                                            ? $arrayFormaPagoContrato['formas_pago']
                                                            : array();

                        $intCantidadFacturasValidar  = (isset($arrayFormaPagoContrato['cantidad_facturas']) &&
                                                        !empty($arrayFormaPagoContrato['cantidad_facturas']))
                                                            ? $arrayFormaPagoContrato['cantidad_facturas']
                                                            : 3;
                    }
                }
            }
            
            if ($strValidaHomologacion == 'SI')
            {
                $arrayDocumentosFinanciero = $this->objInfoDocFinanCabRepository
                    ->findBy(array("puntoId"         => $arrayParametros['puntoId'],
                                   "tipoDocumentoId" => array(1, 5)),
                                   array('feEmision' => 'DESC'));

                if (!is_null($arrayDocumentosFinanciero) && count($arrayDocumentosFinanciero) >= $intCantidadFacturasValidar)
                {
                    $intCount = 0;

                    foreach ($arrayDocumentosFinanciero as $entityDocumentoFinanciero)
                    {
                        $intCount++;

                        $arrayPagoDet = $this->objInfoPagoDetRepository
                            ->findBy(array("referenciaId" => $entityDocumentoFinanciero->getId()));

                        if (!is_null($arrayPagoDet) && !empty($arrayPagoDet))
                        {
                            foreach ($arrayPagoDet as $entityPagoDet)
                            {
                                if (!in_array($entityPagoDet->getFormaPagoId(), $arrayFormasPagoHomologadas))
                                {
                                    $strMensaje = ($intCantidadFacturasValidar > 1) 
                                        ? "Las {$intCantidadFacturasValidar} últimas facturas del cliente no registran"
                                        : "La última factura del cliente no registra";
                                    $strMensaje = "{$strMensaje} la misma forma de pago";
                                    break;
                                }
                            }
                        }

                        if (!empty($strMensaje) || $intCount >= $intCantidadFacturasValidar)
                        {
                            break;
                        }
                    }
                }
            }
        }
        
        return $strMensaje;
    }

    /**
     * Método para obtener los datos del representante legal  de una persona jurídica
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 15-06-2020
     * 
     * @author  Jefferson Carrillo <jacarrillo@telconet.ec> //JAC2021
     * @version 1.0 1-09-2022 - Se eliminó identifica que el consumo esta en metodos desusos
     * @deprecated
     **/
    public function obtenerDatosRepresentanteLegal($arrayData)
    {
        $arrayResponseRepLegal = array();

        try
        {
            $arrayResponseRepLegal = $this->getRepresentanteLegalPersonaJuridica($arrayData);

            if(!is_null($arrayResponseRepLegal) && !is_null($arrayResponseRepLegal['response'])
                && !empty($arrayResponseRepLegal['response']) && $arrayData['strOrigen'] != 'WEB')
            {
                return $arrayResponseRepLegal['response'];
            }
        }
        catch (\Exception $ex)
        {
            $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "Telcos";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $ex->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
            $arrayParametrosLog['creationUser']     = $arrayData['usrCreacion'];
            $this->serviceUtil->insertLog($arrayParametrosLog);
        }

        return $arrayResponseRepLegal;
    }

    /**
     * Método para obtener la respuesta a la consulta de los datos del representante legal de una persona jurídica
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 15-06-2020
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 19-02-2021 - Sé revisa si el cliente está Cancelado y el rep. legal está Activo, Sé inactiva el rep legal
     * 
     * @author  Jefferson Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 1-09-2022 - Se eliminó consumo de este método, se implementó consulta de representante legal desde ms 
     * @deprecated
     *
     **/
    public function getRepresentanteLegalPersonaJuridica($arrayData)
    {
        $arrayRespuesta  = array();
        $arrayRepresentanteLegalJuridico = array();

        $arrayRespuesta['response'] = null;
        $arrayRespuesta['status']   = $this->status['OK'];
        $arrayRespuesta['message']  = "Representante Legal no existe.";
        $arrayRespuesta['success']  = true;

        try
        {
            if($arrayData['strTipoIdentificacion'] == $arrayData['strTipoIdentificacionRepresentanteLegal'] &&
               $arrayData['strIdentificacion'] == $arrayData['strIdentificacionRepresentanteLegal'])
            {
                throw new \Exception("La identificación del representante legal no debe ser igual que la persona jurídica", 1);
            }


            $strIdentificacion = (isset($arrayData['booleanOrigenGetPersona']) && $arrayData['booleanOrigenGetPersona'])
                                     ? $arrayData['strIdentificacion']
                                     : $arrayData['strIdentificacionRepresentanteLegal'];

            $objPersona = $this->obtenerDatosPersonaPrv(array('strCodEmpresa'     => $arrayData['strCodEmpresa'],
                                                              'strPrefijoEmpresa' => $arrayData['strPrefijoEmpresa'],
                                                              'strIdentificacion' => $strIdentificacion));
            if (is_object($objPersona))
            {
                if(isset($arrayData['booleanOrigenGetPersona']) && $arrayData['booleanOrigenGetPersona'])
                {

                    $arrayInfoPersonaEmpresaRolRepLeg = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                        ->buscaClientesPorIdentificacionTipoRolEmpresaEstados(
                            $strIdentificacion,
                            array('Cliente', 'Pre-cliente'),
                            $arrayData['strCodEmpresa'],
                            array('Pendiente', 'Activo'));
                }
                else
                {
                    $arrayInfoPersonaEmpresaRolRepLeg = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                        ->buscaClientesPorIdentificacionTipoRolEmpresaEstados(
                            $strIdentificacion,
                            array('Representante Legal'),
                            $arrayData['strCodEmpresa'],
                            array('Pendiente', 'Activo'));
                }

                if (!is_null($arrayInfoPersonaEmpresaRolRepLeg) && !empty($arrayInfoPersonaEmpresaRolRepLeg))
                {
                    $objInfoPersonaEmpresaRolRepLeg = $arrayInfoPersonaEmpresaRolRepLeg[0];

                    if (is_object($objInfoPersonaEmpresaRolRepLeg))
                    {

                        if(isset($arrayData['booleanOrigenGetPersona']) && $arrayData['booleanOrigenGetPersona'])
                        {
                            $objInfoPersonaRepresentante = $this->emComercial->getRepository('schemaBundle:InfoPersonaRepresentante')
                                ->findOneBy(array('personaEmpresaRolId' => $objInfoPersonaEmpresaRolRepLeg->getId(),
                                                  'estado'              => 'Activo'));
                        }
                        else
                        {
                            $objInfoPersonaRepresentante = $this->emComercial->getRepository('schemaBundle:InfoPersonaRepresentante')
                                ->findOneBy(array('representanteEmpresaRolId' => $objInfoPersonaEmpresaRolRepLeg->getId(),
                                                  'estado'                    => 'Activo'));
                        }
                        
                        if(is_object($objInfoPersonaRepresentante) && 
                            $objInfoPersonaRepresentante->getPersonaEmpresaRolId()->getEstado()== 'Cancelado' && 
                            $objInfoPersonaRepresentante->getEstado() == 'Activo')
                        {
                            $objInfoPersonaRepresentante->setEstado('Inactivo');
                            $this->emComercial->persist($objInfoPersonaRepresentante);
                            $this->emComercial->flush();
                            if(isset($arrayData['booleanOrigenGetPersona']) && $arrayData['booleanOrigenGetPersona'])
                            {
                                $objInfoPersonaRepresentante = $this->emComercial->getRepository('schemaBundle:InfoPersonaRepresentante')
                                    ->findOneBy(array('personaEmpresaRolId' => $objInfoPersonaEmpresaRolRepLeg->getId(), 
                                                        'estado'              => 'Activo'));
                            }
                            else
                            {
                                $objInfoPersonaRepresentante = $this->emComercial->getRepository('schemaBundle:InfoPersonaRepresentante')
                                    ->findOneBy(array('representanteEmpresaRolId' => $objInfoPersonaEmpresaRolRepLeg->getId(), 
                                                        'estado'                    => 'Activo'));
                            }                                
                            
                        }

                        if(is_object($objInfoPersonaRepresentante))
                        {
                            $strRazonSocial =
                                !is_null($objInfoPersonaRepresentante->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial())
                                    ? $objInfoPersonaRepresentante->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial()
                                    : $objInfoPersonaRepresentante->getPersonaEmpresaRolId()->getPersonaId()->getNombres() . ' ' .
                                    $objInfoPersonaRepresentante->getPersonaEmpresaRolId()->getPersonaId()->getApellidos();
                            $strIdentificacionPersonaJuridica =
                                $objInfoPersonaRepresentante->getPersonaEmpresaRolId()->getPersonaId()->getIdentificacionCliente();

                            if(!(isset($arrayData['booleanOrigenGetPersona']) && $arrayData['booleanOrigenGetPersona']))
                            {
                                if(!($objInfoPersonaRepresentante->getPersonaEmpresaRolId()->getPersonaId()->getTipoIdentificacion() ==
                                    $arrayData['strTipoIdentificacion'] &&
                                    $objInfoPersonaRepresentante->getPersonaEmpresaRolId()->getPersonaId()->getIdentificacionCliente() ==
                                    $arrayData['strIdentificacion']))
                                {
                                    throw new \Exception("La persona ya es representante legal de la empresa jurídica '{$strRazonSocial}' 
                                        con identificación '{$strIdentificacionPersonaJuridica}'", 1);
                                }
                                else if($objInfoPersonaRepresentante->getRepresentanteEmpresaRolId()->getPersonaId()->getTipoIdentificacion() ==
                                    $arrayData['strTipoIdentificacionRepresentanteLegal'] &&
                                    $objInfoPersonaRepresentante->getRepresentanteEmpresaRolId()->getPersonaId()->getIdentificacionCliente() ==
                                    $arrayData['strIdentificacionRepresentanteLegal'])
                                {
                                    if($objInfoPersonaRepresentante->getPersonaEmpresaRolId()->getPersonaId()->getTipoIdentificacion() ==
                                        $arrayData['strTipoIdentificacion'] &&
                                        $objInfoPersonaRepresentante->getPersonaEmpresaRolId()->getPersonaId()->getIdentificacionCliente() ==
                                        $arrayData['strIdentificacion'])
                                    {
                                        throw new \Exception($objInfoPersonaRepresentante->getRepresentanteEmpresaRolId()
                                                ->getPersonaId()->getNombres() . ' ' . $objInfoPersonaRepresentante->getRepresentanteEmpresaRolId()
                                                ->getPersonaId()->getApellidos() . " ya se encuentra asignado como representante legal", 1);
                                    }

                                    else
                                    {
                                        throw new \Exception("La persona ya es representante legal de '{$strRazonSocial}'", 1);
                                    }
                                }
                            }

                            $strFechaExpNombramiento = '';

                            if(!is_null($objInfoPersonaRepresentante->getFeRegistroMercantil()))
                            {
                                $strFechaRegistroMercantil = date_format($objInfoPersonaRepresentante->getFeRegistroMercantil(), "d/m/Y G:i");
                            }

                            if(!is_null($objInfoPersonaRepresentante->getFeExpiracionNombramiento()))
                            {
                                $strFechaExpNombramiento =
                                    date_format($objInfoPersonaRepresentante->getFeExpiracionNombramiento(), "d/m/Y G:i");
                            }

                            if(isset($arrayData['booleanOrigenGetPersona']) && $arrayData['booleanOrigenGetPersona'])
                            {
                                $objPersona = $this->obtenerDatosPersonaPrv(
                                                  array('strCodEmpresa'     => $arrayData['strCodEmpresa'],
                                                        'strPrefijoEmpresa' => $arrayData['strPrefijoEmpresa'],
                                                        'strIdentificacion' => $objInfoPersonaRepresentante->getRepresentanteEmpresaRolId()
                                                             ->getPersonaId()->getIdentificacionCliente()));
                            }

                            $arrayRepresentanteLegalJuridico =
                                array('idPersona'                   => $objPersona->id,
                                      'nombres'                     => $objPersona->nombres,
                                      'apellidos'                   => $objPersona->apellidos,
                                      'razonComercial'              => !is_null($objInfoPersonaRepresentante)
                                          ? $objInfoPersonaRepresentante->getRazonComercial()
                                          : '',
                                      'tipoIdentificacion'          => $objPersona->tipoIdentificacion,
                                      'identificacion'              => $objPersona->identificacionCliente,
                                      'cargo'                       => $objPersona->cargo,
                                      'direccion'                   => $objPersona->direccionTributaria,
                                      'nacionalidad'                => $objPersona->nacionalidad,
                                      'tipoTributario'              => $objPersona->tipoTributario,
                                      'rol'                         => !is_null($objInfoPersonaRepresentante)
                                                                           ? 'Representante Legal Juridico'
                                                                           : '',
                                      'fechaRegistroMercantil'      => $strFechaRegistroMercantil,
                                      'fechaExpiracionNombramiento' => $strFechaExpNombramiento,
                                      'estado'                      => !is_null($objInfoPersonaRepresentante)
                                                                           ? $objInfoPersonaRepresentante->getEstado()
                                                                           : '',
                                      'formasContacto'              => $objPersona->formasContacto);


                            $arrayRespuesta['response'] = $arrayRepresentanteLegalJuridico;
                            $arrayRespuesta['status']   = $this->status['OK'];
                            $arrayRespuesta['message']  = $this->mensaje['OK'];
                            $arrayRespuesta['success']  = true;
                            return $arrayRespuesta;
                        }
                    }
                }
            }
            else
            {
                $strIdPais              = "";

                $arrayParamValidaIdentifica = array(
                                                        'strTipoIdentificacion'     => $arrayData['strTipoIdentificacionRepresentanteLegal'],
                                                        'strIdentificacionCliente'  => $arrayData['strIdentificacionRepresentanteLegal'],
                                                        'intIdPais'                 => $strIdPais,
                                                        'strCodEmpresa'             =>  $arrayData['strCodEmpresa']
                                                    );
                $strValidacionRespuesta = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                            ->validarIdentificacionTipo($arrayParamValidaIdentifica);

                if(!empty($strValidacionRespuesta))
                {
                    throw new \Exception($strValidacionRespuesta, 1);
                }
                $arrayRepresentanteLegalJuridico = array('tipoIdentificacion' => $arrayData['strTipoIdentificacionRepresentanteLegal'],
                                                         'identificacion'     => $arrayData['strIdentificacionRepresentanteLegal'],
                                                          'rol'               => 'Disponible');

                $arrayRespuesta['response'] = $arrayRepresentanteLegalJuridico;
                $arrayRespuesta['success']  = true;
                return $arrayRespuesta;

            }

            if(!is_null($objPersona) && !$arrayData['booleanOrigenGetPersona'])
            {
                $arrayRepresentanteLegalJuridico =
                    array('idPersona'                   => $objPersona->id,
                          'nombres'                     => $objPersona->nombres,
                          'apellidos'                   => $objPersona->apellidos,
                          'razonComercial'              => !is_null($objInfoPersonaRepresentante)
                                                               ? $objInfoPersonaRepresentante->getRazonComercial()
                                                               : '',
                          'tipoIdentificacion'          => $objPersona->tipoIdentificacion,
                          'identificacion'              => $objPersona->identificacionCliente,
                          'cargo'                       => $objPersona->cargo,
                          'direccion'                   => $objPersona->direccionTributaria,
                          'nacionalidad'                => $objPersona->nacionalidad,
                          'tipoTributario'              => $objPersona->tipoTributario,
                          'rol'                         => !is_null($objInfoPersonaRepresentante)
                                                               ? 'Representante Legal Juridico'
                                                               : 'Disponible',
                          'fechaRegistroMercantil'      => $strFechaRegistroMercantil,
                          'fechaExpiracionNombramiento' => $strFechaExpNombramiento,
                          'estado'                      => !is_null($objInfoPersonaRepresentante)
                                                               ? $objInfoPersonaRepresentante->getEstado()
                                                               : '',
                          'formasContacto'              => $objPersona->formasContacto);
            }
            $arrayRespuesta['response'] = $arrayRepresentanteLegalJuridico;
            $arrayRespuesta['success']  = true;
        }
        catch(\Exception $objException)
        {
            $arrayRespuesta['status']   = $this->status['ERROR'];
            $arrayRespuesta['success']  = false;

            $arrayRespuesta['message'] = ($objException->getCode() == 1)
                ? $objException->getMessage()
                : 'Ha ocurrido un error inesperado al consultar representante legal';

            $arrayParametrosLog['enterpriseCode']   = $arrayData['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = $arrayRespuesta['message'];
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $objException->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
            $arrayParametrosLog['creationUser']     = $arrayData['usrCreacion'];
            $this->serviceUtil->insertLog($arrayParametrosLog);
        }

        return $arrayRespuesta;
    }

    /**
     * Método que realiza la actualización de información o creación del representante legal de una persona jurídica.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 04-06-2020 para persona juridica
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 09-12-2020 - Se agrega un parámetro adicional 'esCambioRazonSocial', encargado de controlar el commit
     *                           cuando la llamada del método es por cambio de razón social tradicional o por punto.
     * 
     * @author  Jefferson Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 1-09-2022 - Se eliminó consumo de este método, se implementó actualizacion de representante legal desde ms 
     * @deprecated
     */
    public function updateRepresentanteLegalPersonaJuridica($arrayData)
    {
        $arrayResponse['response'] = array();
        $arrayResponse['success']  = false;
        $arrayResponse['message']  = '';
        $arrayResponse['status']   = $this->status['ERROR'];
        $boolEsCambioRazonSocial   = isset($arrayData['esCambioRazonSocial']) && $arrayData['esCambioRazonSocial'];
        $strClientIp = '127.0.0.1';

        if (!$boolEsCambioRazonSocial)
        {
            $this->emComercial->getConnection()->beginTransaction();
        }

        try
        {
            $strTipoTributario  = trim($arrayData['persona']['tipoTributario']);

            if($strTipoTributario != 'JUR')
            {
                throw new \Exception('La persona indicada no es de tipo juridico.', 1);
            }

            if($arrayData['persona']['tipoIdentificacion'] == $arrayData['persona']['representanteLegalJuridico']['tipoIdentificacion'] &&
                $arrayData['persona']['identificacionCliente'] == $arrayData['persona']['representanteLegalJuridico']['identificacion'])
            {
                throw new \Exception("La identificación del representante legal no debe ser igual que la persona jurídica", 1);
            }

            $strIdentificacionCliente    = trim($arrayData['persona']['identificacionCliente']);

            if(!is_null($arrayData['persona']['razonComercial']) && !empty($arrayData['persona']['razonComercial']))
            {
                $strRazonComercial = trim($arrayData['persona']['razonComercial']);
            }

            if(!is_null($arrayData['persona']['fechaRegistroMercantil']) && !empty($arrayData['persona']['fechaRegistroMercantil']))
            {
                $objRegistroMercantilRepLeg =
                    \DateTime::createFromFormat('d/m/Y H:i:s', trim($arrayData['persona']['fechaRegistroMercantil']) . ' 00:00:00');
            }

            $strTipoIdentificacionRepLeg = trim($arrayData['persona']['representanteLegalJuridico']['tipoIdentificacion']);
            $strIdentificacionRepLeg     = trim($arrayData['persona']['representanteLegalJuridico']['identificacion']);
            $strNombresRepLeg            = trim($arrayData['persona']['representanteLegalJuridico']['nombres']);
            $strApellidosRepLeg          = trim($arrayData['persona']['representanteLegalJuridico']['apellidos']);
            $strDireccionRepLeg          = trim($arrayData['persona']['representanteLegalJuridico']['direccion']);
            $strCargoRepLeg              = trim($arrayData['persona']['representanteLegalJuridico']['cargo']);
            $arrayFormasContactoRepLeg   = $arrayData['persona']['representanteLegalJuridico']['formasContacto'];
            $objExpiracionRepLeg         = \DateTime::createFromFormat('d/m/Y H:i:s',
                trim($arrayData['persona']['representanteLegalJuridico']['fechaExpiracionNombramiento']) . ' 00:00:00');

            $arrayInfoPersonaEmpresaRolCliente = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                ->buscaClientesPorIdentificacionTipoRolEmpresaEstados($strIdentificacionCliente,
                    array('Cliente', 'Pre-cliente'),
                    $arrayData['codEmpresa'],
                    array('Pendiente', 'Activo'));
            $objInfoPersonaEmpresaRolCliente = $arrayInfoPersonaEmpresaRolCliente[0];

            if(is_object($objInfoPersonaEmpresaRolCliente) && is_object($objInfoPersonaEmpresaRolCliente->getPersonaId()))
            {
                $objInfoPersonaRepresentante = $this->emComercial->getRepository('schemaBundle:InfoPersonaRepresentante')
                    ->findOneBy(array('personaEmpresaRolId'       => $objInfoPersonaEmpresaRolCliente->getId(),
                                      'estado'                    => 'Activo'));

                if(is_object($objInfoPersonaRepresentante) && is_object($objInfoPersonaRepresentante->getRepresentanteEmpresaRolId()))
                {
                    $objInfoPersonaEmpresaRolRepLeg = $objInfoPersonaRepresentante->getRepresentanteEmpresaRolId();

                    if(is_object($objInfoPersonaEmpresaRolRepLeg) && is_object($objInfoPersonaEmpresaRolRepLeg->getPersonaId()))
                    {
                        if ($objInfoPersonaEmpresaRolCliente->getPersonaId()->getIdentificacionCliente() ==
                            $arrayData['persona']['identificacionCliente'])
                        {
                            $objInfoPersonaRepLeg = $objInfoPersonaRepresentante->getRepresentanteEmpresaRolId()->getPersonaId();

                            if($objInfoPersonaRepLeg->getTipoIdentificacion() == $strTipoIdentificacionRepLeg &&
                                $objInfoPersonaRepLeg->getIdentificacionCliente() == $strIdentificacionRepLeg)
                            {
                                //Actualización representante legal
                                $objInfoPersonaRepLeg->setNombres($strNombresRepLeg);
                                $objInfoPersonaRepLeg->setApellidos($strApellidosRepLeg);
                                $objInfoPersonaRepLeg->setCargo($strCargoRepLeg);
                                $objInfoPersonaRepLeg->setDireccion($strDireccionRepLeg);
                                $objInfoPersonaRepLeg->setDireccionTributaria($strDireccionRepLeg);
                                $objInfoPersonaRepresentante->setFeExpiracionNombramiento($objExpiracionRepLeg);
                                $objInfoPersonaRepresentante->setFeRegistroMercantil($objRegistroMercantilRepLeg);
                                $objInfoPersonaRepresentante->setRazonComercial($strRazonComercial);

                                $objInfoPersonaRepresentante->setObservacion("Actualización de datos del representante legal" .
                                    (($arrayData['origen'] == "WEB") ? ". (Origen WEB)" : ""));

                                $arrayParamFormasContac                        = array ();
                                $arrayParamFormasContac['strPrefijoEmpresa']   = $arrayData['prefijoEmpresa'];
                                $arrayParamFormasContac['arrayFormasContacto'] = $arrayFormasContactoRepLeg;
                                $arrayParamFormasContac['strOpcionPermitida']  = 'NO';
                                $arrayParamFormasContac['strNombrePais']       = 'ECUADOR';
                                $arrayParamFormasContac['intIdPais']           =  1;

                                $arrayValidaciones   = $this->serviceInfoPerFormContacto->validarFormasContactos($arrayParamFormasContac);

                                if($arrayValidaciones)
                                {
                                    foreach($arrayValidaciones as $mensajeValidaciones)
                                    {
                                        foreach($mensajeValidaciones as $value)
                                        {
                                            $strError = $strError.$value.".\n";
                                        }
                                    }
                                    throw new \Exception("No se pudo guardar el representante legal. " . $strError, 1);
                                }

                                $this->serviceInfoPerFormContacto->inactivarPersonaFormaContactoActivasPorPersona($objInfoPersonaRepLeg->getId(),
                                    $arrayData['usrCreacion']);

                                foreach($arrayFormasContactoRepLeg as $arrayValue)
                                {
                                    $objInfoPersonaFormaContacto = new InfoPersonaFormaContacto();
                                    $objInfoPersonaFormaContacto->setValor($arrayValue['valor']);
                                    $objInfoPersonaFormaContacto->setEstado("Activo");
                                    $objInfoPersonaFormaContacto->setFeCreacion(new \DateTime('now'));

                                    if(isset($arrayValue['idFormaContacto']))
                                    {
                                        $objAdmiFormaContacto = $this->emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                            ->find($arrayValue['idFormaContacto']);
                                    }
                                    else
                                    {
                                        $objAdmiFormaContacto = $this->emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                            ->findPorDescripcionFormaContacto($arrayValue ['formaContacto']);
                                    }

                                    $objInfoPersonaFormaContacto->setFormaContactoId($objAdmiFormaContacto);
                                    $objInfoPersonaFormaContacto->setIpCreacion($strClientIp);
                                    $objInfoPersonaFormaContacto->setPersonaId($objInfoPersonaRepLeg);
                                    $objInfoPersonaFormaContacto->setUsrCreacion($arrayData['usrCreacion']);
                                    $this->emComercial->persist($objInfoPersonaFormaContacto);
                                }
                            }
                            else
                            {
                                //Eliminación lógica representante legal, buscar y/o crear una nueva persona y asignarla a la persona juridica
                                $arrayData['persona']['objPersonaEmpresaRol'] = $objInfoPersonaEmpresaRolCliente;

                                $arrayResponseRepLeg = $this->createRepresentanteLegalPersonaJuridica($arrayData);

                                if($arrayResponseRepLeg['success'])
                                {

                                    $objInfoPersonaEmpresaRolRepLeg->setEstado('Eliminado');
                                    $this->emComercial->persist($objInfoPersonaEmpresaRolRepLeg);

                                    $objInfoPersonaRepresentante->setEstado('Eliminado');
                                    $objInfoPersonaRepresentante->setFeUltMod(new \DateTime('now'));
                                    $objInfoPersonaRepresentante->setUsrUltMod($arrayData['usrCreacion']);
                                    $objInfoPersonaRepresentante->setIpUltMod($strClientIp);
                                    $objInfoPersonaRepresentante->setObservacion("Cambio de representante legal");
                                    $this->emComercial->persist($objInfoPersonaRepresentante);
                                }
                                else
                                {
                                    throw new \Exception($arrayResponseRepLeg['message'], 1);
                                }
                            }
                        }
                        else
                        {
                            throw new \Exception('La persona jurídica enviada no coindice con la persona 
                                jurídica del representante legal asignado.', 1);
                        }
                    }
                    else
                    {
                        throw new \Exception('No se ha encontrado a la persona asignada del representante legal.', 1);
                    }
                }
                else
                {
                    $arrayData['persona']['objPersonaEmpresaRol'] = $objInfoPersonaEmpresaRolCliente;

                    $arrayResponseRepLeg = $this->createRepresentanteLegalPersonaJuridica($arrayData);

                    if(!$arrayResponseRepLeg['success'])
                    {
                        throw new \Exception($arrayResponseRepLeg['message'], 1);
                    }
                }
            }
            else
            {
                throw new \Exception('No se ha encontrado representante legal para la identificación especificada. 
                    ('. $strIdentificacionRepLeg .')', 1);
            }

            if($this->emComercial->getConnection()->isTransactionActive() && !$boolEsCambioRazonSocial)
            {
                $this->emComercial->flush();
                $this->emComercial->getConnection()->commit();
                $this->emComercial->getConnection()->close();
            }

            if($arrayData['origen'] != 'WEB')
            {
                $arrayResultado =
                    $this->getPersona(array("strCodEmpresa"         => $arrayData['codEmpresa'],
                                            "strPrefijoEmpresa"     => $arrayData['prefijoEmpresa'],
                                            "strIdentificacion"     => $strIdentificacionCliente,
                                            "strTipoIdentificacion" => $arrayData['persona']['tipoIdentificacion'],
                                            "user"                  => $arrayData['usrCreacion'], "nuevo" => "S"));
                $objPersona                = $arrayResultado['response']->persona;
                $arrayPlanes               = $arrayResultado['response']->planes;
                $arrayResponse['response'] = array('persona' => $objPersona, 'planes' => $arrayPlanes);
            }
            $arrayResponse['status']   = $this->status['OK'];
            $arrayResponse['message']  = 'Representante legal actualizado correctamente.';
            $arrayResponse['success']  = true;
        }
        catch (\Exception $objException)
        {

            if($this->emComercial->getConnection()->isTransactionActive() && !$boolEsCambioRazonSocial)
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }

            $arrayResponse['success']   = false ;
            $arrayResponse['status'] = $this->status['ERROR'];
            $arrayResponse['message'] = ($objException->getCode() == 1)
                ? $objException->getMessage()
                : 'Ha ocurrido un error inesperado, representante legal no ingresado correctamente.';

            $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = $arrayResponse['message'];
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $objException->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
            $arrayParametrosLog['creationUser']     = $arrayData['usrCreacion'];
            $this->serviceUtil->insertLog($arrayParametrosLog);
        }

        return $arrayResponse;
    }

    /**
     * Método que realiza la creación del representante legal de una persona jurídica.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 04-06-2020 para persona juridica
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 09-12-2020 - Se agrega un parámetro adicional 'esCambioRazonSocial', encargado de controlar el commit
     *                           cuando la llamada del método es por cambio de razon social tradicional o por punto.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 25-11-2021 - Problemas al crear el representante legal debido a que el mismo existio como cliente
     *                           pero posee sus registros en un estado diferente de Activo.
     * 
     * @author  Jefferson Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 1-09-2022 - Se eliminó consumo de este método, se implementó creación de representante legal desde ms 
     * @deprecated
     */
    public function createRepresentanteLegalPersonaJuridica($arrayData)
    {
        $arrayResponse           =  array();
        $strClientIp             = '127.0.0.1';
        $boolEsCambioRazonSocial =  isset($arrayData['esCambioRazonSocial']) && $arrayData['esCambioRazonSocial'];
        $arrayEstadosRepresentan = array('Pendiente', 'Activo');

        if(!$this->emComercial->getConnection()->isTransactionActive() && !$boolEsCambioRazonSocial)
        {
            $this->emComercial->getConnection()->beginTransaction();
        }

        try
        {
            $strTipoIdentificacionRepLeg = trim($arrayData['persona']['representanteLegalJuridico']['tipoIdentificacion']);
            $strIdentificacionRepLeg     = trim($arrayData['persona']['representanteLegalJuridico']['identificacion']);

            if($arrayData['persona']['tipoIdentificacion'] == $strTipoIdentificacionRepLeg &&
                $arrayData['persona']['identificacionCliente'] == $strIdentificacionRepLeg)
            {
                throw new \Exception("La identificación del representante legal no debe ser igual que la persona jurídica", 1);
            }

            $objInfoPersonaRepLeg = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                ->findOneBy(array('tipoIdentificacion'    => $strTipoIdentificacionRepLeg,
                                  'identificacionCliente' => $strIdentificacionRepLeg));

            if(is_object($objInfoPersonaRepLeg) && !in_array($objInfoPersonaRepLeg->getEstado(),$arrayEstadosRepresentan))
            {
                $objInfoPersonaRepLeg->setEstado('Activo');
            }

            if(empty($arrayData['persona']['representanteLegalJuridico']['tipoIdentificacion']))
            {
                throw new \Exception('No se ha especificado tipo de identificación del representante legal.', 1);
            }

            if(empty($arrayData['persona']['representanteLegalJuridico']['identificacion']))
            {
                throw new \Exception('No se ha especificado identificación del representante legal.', 1);
            }

            if (empty($arrayData['persona']['representanteLegalJuridico']['direccion']))
            {
                throw new \Exception('No se ha especificado direccion del representante legal.', 1);
            }

            if(empty($arrayData['persona']['representanteLegalJuridico']['cargo']))
            {
                throw new \Exception('No se ha especificado cargo del representante legal.', 1);

            }

            if (!is_null($arrayData['persona']['fechaRegistroMercantil']) && !empty($arrayData['persona']['fechaRegistroMercantil']))
            {
                $arrayData['persona']['fechaRegistroMercantil'] = \DateTime::createFromFormat( 'd/m/Y H:i:s',
                    $arrayData['persona']['fechaRegistroMercantil'] . ' 00:00:00');
            }
            else
            {
                $arrayData['persona']['fechaRegistroMercantil'] = new \DateTime('now');
            }

            if(!is_null($arrayData['persona']['representanteLegalJuridico']['fechaExpiracionNombramiento']) &&
                !empty($arrayData['persona']['representanteLegalJuridico']['fechaExpiracionNombramiento']))
            {
                $arrayData['persona']['representanteLegalJuridico']['fechaExpiracionNombramiento'] = \DateTime::createFromFormat(
                    'd/m/Y H:i:s', $arrayData['persona']['representanteLegalJuridico']['fechaExpiracionNombramiento'] . ' 00:00:00');
            }
            else
            {
                $arrayData['persona']['representanteLegalJuridico']['fechaExpiracionNombramiento'] = null;
                throw new \Exception('La fecha de expiración del nombramiento del representante legal es obligatorio.', 1);
            }

            if (is_null($arrayData['persona']['representanteLegalJuridico']['tituloId']) ||
                empty($arrayData['persona']['representanteLegalJuridico']['tituloId']))
            {
                //140 - Ningun título
                $arrayData['persona']['representanteLegalJuridico']['tituloId'] = 140;
            }

            $objInfoOficinaGrupo = $this->emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                ->find($arrayData['idOficina']);
            $objTitulo           = $this->emComercial->getRepository('schemaBundle:AdmiTitulo')
                ->find($arrayData['persona']['representanteLegalJuridico']['tituloId']);

            $objEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoEmpresaRol')
                ->findPorNombreRolPorNombreTipoRolPorEmpresa('Representante Legal Juridico', 'Representante Legal', $arrayData['codEmpresa']);

            if(!is_object($objTitulo))
            {
                throw new \Exception('No se encuentra el título del representante legal. (' .
                    $arrayData['persona']['representanteLegalJuridico']['tituloId'] . ')', 1);
            }

            if(!is_object($objInfoPersonaRepLeg))
            {
                $objInfoPersonaRepLeg = new InfoPersona();
                $objInfoPersonaRepLeg->setTipoIdentificacion($arrayData['persona']['representanteLegalJuridico']['tipoIdentificacion']);
                $objInfoPersonaRepLeg->setIdentificacionCliente($arrayData['persona']['representanteLegalJuridico']['identificacion']);
                $objInfoPersonaRepLeg->setTituloId($objTitulo);
                $objInfoPersonaRepLeg->setOrigenProspecto('N');
                $objInfoPersonaRepLeg->setTipoTributario('NAT');
                $objInfoPersonaRepLeg->setFeCreacion(new \DateTime('now'));
                $objInfoPersonaRepLeg->setUsrCreacion($arrayData['usrCreacion']);
                $objInfoPersonaRepLeg->setIpCreacion($strClientIp);
                $objInfoPersonaRepLeg->setEstado('Activo');

            }

            $objInfoPersonaRepLeg->setNombres(trim($arrayData['persona']['representanteLegalJuridico']['nombres']));
            $objInfoPersonaRepLeg->setApellidos(trim($arrayData['persona']['representanteLegalJuridico']['apellidos']));
            $objInfoPersonaRepLeg->setCargo(trim($arrayData['persona']['representanteLegalJuridico']['cargo']));
            $objInfoPersonaRepLeg->setDireccion(trim($arrayData['persona']['representanteLegalJuridico']['direccion']));
            $objInfoPersonaRepLeg->setDireccionTributaria(trim($arrayData['persona']['representanteLegalJuridico']['direccion']));

            $this->emComercial->persist($objInfoPersonaRepLeg);
            $this->emComercial->flush();

            $objPersonaEmpresaRolRepLeg = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                            ->findOneBy(array('personaId'       => $objInfoPersonaRepLeg->getId(),
                                                            'empresaRolId'    => $objEmpresaRol->getId(),
                                                            'estado'          => 'Activo'));

            if(!is_object($objPersonaEmpresaRolRepLeg))
            {
                $objPersonaEmpresaRolRepLeg = new InfoPersonaEmpresaRol();
                $objPersonaEmpresaRolRepLeg->setPersonaId($objInfoPersonaRepLeg);
                $objPersonaEmpresaRolRepLeg->setEmpresaRolId($objEmpresaRol);
                $objPersonaEmpresaRolRepLeg->setOficinaId($objInfoOficinaGrupo);
                $objPersonaEmpresaRolRepLeg->setFeCreacion(new \DateTime('now'));
                $objPersonaEmpresaRolRepLeg->setUsrCreacion($arrayData['usrCreacion']);
                $objPersonaEmpresaRolRepLeg->setEstado('Activo');
                $objPersonaEmpresaRolRepLeg->setIpCreacion($strClientIp);

                $this->emComercial->persist($objPersonaEmpresaRolRepLeg);
                $this->emComercial->flush();
            }

            $objInfoPersonaRepresentante = new InfoPersonaRepresentante();
            $objInfoPersonaRepresentante->setPersonaEmpresaRolId($arrayData['persona']['objPersonaEmpresaRol']);
            
            $objInfoPersonaRepresentante->setRepresentanteEmpresaRolId($objPersonaEmpresaRolRepLeg);
            $objInfoPersonaRepresentante->setRazonComercial($arrayData['persona']['razonComercial']);
            $objInfoPersonaRepresentante->setFeRegistroMercantil($arrayData['persona']['fechaRegistroMercantil']);
            $objInfoPersonaRepresentante->setFeExpiracionNombramiento(
                $arrayData['persona']['representanteLegalJuridico']['fechaExpiracionNombramiento']);
            $objInfoPersonaRepresentante->setEstado('Activo');
            $objInfoPersonaRepresentante->setUsrCreacion($arrayData['usrCreacion']);
            $objInfoPersonaRepresentante->setFeCreacion(new \DateTime('now'));
            $objInfoPersonaRepresentante->setIpCreacion($strClientIp);

            $this->emComercial->persist($objInfoPersonaRepresentante);
            $this->emComercial->flush();

            $this->serviceInfoPerFormContacto->inactivarPersonaFormaContactoActivasPorPersona($objInfoPersonaRepLeg->getId(),
                $arrayData['usrCreacion']);

            if(!is_null($arrayData['persona']['representanteLegalJuridico']['formasContacto']) &&
                !empty($arrayData['persona']['representanteLegalJuridico']['formasContacto']))
            {
                foreach($arrayData['persona']['representanteLegalJuridico']['formasContacto'] as $arrayValue)
                {
                    $objInfoPersonaFormaContacto = new InfoPersonaFormaContacto();
                    $objInfoPersonaFormaContacto->setValor($arrayValue['valor']);
                    $objInfoPersonaFormaContacto->setEstado("Activo");
                    $objInfoPersonaFormaContacto->setFeCreacion(new \DateTime('now'));

                    if(isset($arrayValue['idFormaContacto']))
                    {
                        $objAdmiFormaContacto = $this->emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                            ->find($arrayValue['idFormaContacto']);
                    }
                    else
                    {
                        $objAdmiFormaContacto = $this->emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                            ->findPorDescripcionFormaContacto($arrayValue ['formaContacto']);
                    }
                    $objInfoPersonaFormaContacto->setFormaContactoId($objAdmiFormaContacto);
                    $objInfoPersonaFormaContacto->setIpCreacion($strClientIp);
                    $objInfoPersonaFormaContacto->setPersonaId($objInfoPersonaRepLeg);
                    $objInfoPersonaFormaContacto->setUsrCreacion($arrayData['usrCreacion']);
                    $this->emComercial->persist($objInfoPersonaFormaContacto);
                }
                $this->emComercial->flush();

            }

            $arrayResponse['status']   = $this->status['OK'];
            $arrayResponse['message']  = 'Representante legal ingresado correctamente.';
            $arrayResponse['success']  = true;
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->commit();
            }            
            
        }
        catch (\Exception $objException)
        {
            if($this->emComercial->getConnection()->isTransactionActive() && !$boolEsCambioRazonSocial)
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }

            $arrayResponse['status'] = $this->status['ERROR'];
            $arrayResponse['message'] = ($objException->getCode() == 1)
                ? $objException->getMessage() :
                'Ha ocurrido un error inesperado, representante legal no ingresado correctamente.' .$objException->getMessage();
            $arrayResponse['success'] = false;

            $arrayParametrosLog['enterpriseCode']   = $arrayData['codEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = $arrayResponse['message'];
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $objException->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayData, 128);
            $arrayParametrosLog['creationUser']     = $arrayData['usrCreacion'];
            $this->serviceUtil->insertLog($arrayParametrosLog);
        }

        return $arrayResponse;
    }

    /**
     * Método que obtiene puntos de un cliente de forma paginada.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 20-09-2020
     *
     */
    public function getPuntosClientePorPagina($arrayParametros)
    {
        $arrayRespuesta = array();

        $arrayParametros['arrayEstadosPuntosTotal'] = array('Activo', 'In-Corte', 'Factible','Pendiente');

        try
        {
            $arrayRespuestaPuntos = $this->serviceInfoPunto->obtenerDatosPuntosClienteAdendum($arrayParametros);

            $arrayRespuesta['response'] = array('listaPuntos' => $arrayRespuestaPuntos['puntos'],
                                                'total'       => (int)$arrayRespuestaPuntos['total'],
                                                'cancelado'   => ($arrayRespuestaPuntos[0]['cancelado'] == 'S') ? 'S' : '',
                                                'filtrado'    => $arrayParametros['boolFiltrado'],
                                                'nuevoFiltro' => $arrayParametros['boolNuevoFiltro'],
                                                'pagina'      => $arrayParametros['intPagina']);

            $arrayRespuesta['success'] = true;
            $arrayRespuesta['message'] = 'OK';
            $arrayRespuesta['status'] = $this->status['OK'];
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta['success'] = false;
            $arrayRespuesta['status']  = $this->status['ERROR'];
            $arrayRespuesta['message'] = $this->mensaje['ERROR'];

            $arrayParametrosLog['enterpriseCode']   = $arrayParametros['strCodEmpresa'];
            $arrayParametrosLog['logType']          = '1';
            $arrayParametrosLog['logOrigin']        = 'TELCOS';
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = 'ERROR';
            $arrayParametrosLog['status']           = 'Fallido';
            $arrayParametrosLog['descriptionError'] = $objException->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayParametros, 128);
            $arrayParametrosLog['creationUser']     = $arrayParametros['strUsrCreacion'];
            $serviceUtil->insertLog($arrayParametrosLog);
        }
        return $arrayRespuesta;
    }

   /**
    * Método que valida si un usuario tiene acceso a tm-comercial por alguno de sus id de persona empresa rol
    *
    * @author Edgar Pin Villavicencio <epinætelconet.ec>
    * @version 1.0 11-06-2021
    */ 
    public function getAccesoPorRol($arrayData)
    {
        $boolEsCliente = false;
        try 
        {
            foreach ($arrayData['personaRol'] as $arrayPer) 
            {
                $arrayPersonaRol[] = $arrayPer["idPersonaRol"];
            }
            $intIdPersonaEmpresaRol = $arrayPersonaRol[0];
            $objInfoPersonaEmpRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                      ->find($intIdPersonaEmpresaRol);            
            $arrayPersonaLoginApp   = $this->serviceInfoPersona->getInfoUsuarioMobile($objInfoPersonaEmpRol->getPersonaId()->getLogin());
            $intIdPersonaEmpresaRol = 0;
            foreach ($arrayPersonaLoginApp as $arrayPersona) 
            {
               if (in_array($arrayPersona['id_persona_rol'],$arrayPersonaRol))
               {
                   $intIdPersonaEmpresaRol = $arrayPersona['id_persona_rol'];
                   $boolEsCliente = true;
                   break;
               }
            }
            if (!$boolEsCliente)
            {
                $arrayPersonaLoginApp   = $this->serviceInfoPersona->getInfoUsuarioMobile($objInfoPersonaEmpRol->getPersonaId()->getLogin(), 9);
                foreach ($arrayPersonaLoginApp as $arrayPersona) 
                {
                   if (in_array($arrayPersona['id_persona_rol'],$arrayPersonaRol))
                   {
                       $intIdPersonaEmpresaRol = $arrayPersona['id_persona_rol'];
                       break;
                   }
                }                
            }
            if ($intIdPersonaEmpresaRol > 0)
            {
                $strPerfil = "MOBILE COMERCIAL";
                    
                $arrayDatosPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                    ->getPersonaDepartamentoPorUser($objInfoPersonaEmpRol->getPersonaId()->getLogin());

                $arrayRespuestaPerfil = $this->emSeguridad->getRepository('schemaBundle:SeguPerfilPersona')
                    ->getAccesoPorPerfilPersona($strPerfil, $arrayDatosPersona['ID_PERSONA']);

                if(count($arrayRespuestaPerfil) < 1)
                {
                    $arrayMensaje = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                        ->getOne("MENSAJES_TM_COMERCIAL", "COMERCIAL", "TM_COMERCIAL", 
                                 "RESTRICCION_ACCESO", "", "", "", "", "", $strCodEmpresa);
                    
                    throw new \Exception($arrayMensaje['valor1'], 100);
                }     
                $arrayResponse['status'] = $this->status['OK'];
                $arrayResponse['message'] = (string) $intIdPersonaEmpresaRol;
                $arrayResponse['success'] = true;            
            }
            else
            {

                $arrayResponse['status'] = $this->status['ERROR'];
                $arrayResponse['message'] = "No existe Rol de empresa para el usuario";
                $arrayResponse['success'] = false;                
            }
            return $arrayResponse;
        }        
        catch (\Exception $objException) 
        {
            $arrayResponse['status'] = $this->status['ERROR'];
            $arrayResponse['message'] = $objException->getMessage();
            $arrayResponse['success'] = false;  
        }  
    }







    /**
    * Método que valida si un usuario tiene una recomendación por el servicio
    *
    * @author Carlos Caguana <ccaguana@telconet.ec>
    * @version 1.0 06-09-2021
    */ 
    public function verificarRecomendacion($arrayParametros)  
    {
        $arrayResultado  = array();
        $strIpMod               = $arrayParametros['strIpMod'];
        $strUserMod             = $arrayParametros['strUserMod'];
        
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

            $strUrl =  $this->strUrlPersonaRecomendacion;
            $arrayResponseJson  = $this->serviceRestClient->postJSON( $strUrl , $strJsonData, $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'], true);

            if (
                isset($strJsonRespuesta['code']) && $strJsonRespuesta['code'] == 0
                && isset($strJsonRespuesta['status'])
                && isset($strJsonRespuesta['message'])
            ) 
            {
                $arrayResponse = array(
                    'strStatus' => "OK",
                    'strMensaje' => $strJsonRespuesta['message'],
                    'data' => $strJsonRespuesta['data']
                );
                $arrayResultado = $arrayResponse;
            } else 
            {
                $arrayResultado['strStatus']      = "ERROR";
                if (empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['strMensaje']  = "No Existe Conectividad con el WS ms-core-gen-persona.";
                } else 
                {
                    $arrayResultado['strMensaje']  = $strJsonRespuesta['message'];
                }
            }
        } catch (\Exception $e) 
        {
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas" . $e->getMessage();
            $arrayResultado = array('strMensaje'     => $strRespuesta);
            $this->serviceUtil->insertError(
                'Telcos+',
                'ComercialMobileService.verificarRecomendacion',
                'Error ComercialMobileService.verificarRecomendacion:' . $e->getMessage(),
                $strUserMod,
                $strIpMod
            );
        }
        return $arrayResultado;
    }


}
