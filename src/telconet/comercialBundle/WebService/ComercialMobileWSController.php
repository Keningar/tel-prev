<?php

namespace telconet\comercialBundle\WebService;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use telconet\schemaBundle\DependencyInjection\BaseWSController;
use telconet\comercialBundle\WebService\ComercialMobileWSResponse\PersonaResponse;
use telconet\comercialBundle\WebService\ComercialMobileWSResponse\FormaContactoResponse;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use telconet\comercialBundle\WebService\ComercialMobile\EmpresaPersonaComplexType;
use telconet\comercialBundle\WebService\ComercialMobile\ObtenerEmpresasResponse;
use telconet\comercialBundle\Service\ComercialService;
use telconet\schemaBundle\Entity\AdmiTipoMedio;
use telconet\schemaBundle\Entity\AdmiProducto;
use telconet\schemaBundle\Entity\InfoPlanCab;
use telconet\comercialBundle\WebService\ComercialMobile\PersonaComplexType;
use telconet\comercialBundle\WebService\ComercialMobile\ObtenerPersonaResponse;
use telconet\comercialBundle\WebService\ComercialMobile\ObtenerDatosClienteResponse;
use telconet\comercialBundle\WebService\ComercialMobile\FormaContactoComplexType;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\comercialBundle\WebService\ComercialMobile\CrearPreClienteResponse;
use telconet\comercialBundle\WebService\ComercialMobile\CrearPuntoResponse;

use telconet\seguridadBundle\Service\SeguridadService;
use telconet\comercialBundle\Service\InfoContratoDigitalService;
use telconet\comercialBundle\Service\InfoContratoAprobService;
use telconet\comercialBundle\Service\InfoServicioService;
use telconet\tecnicoBundle\Service\InfoElementoService;
use telconet\schemaBundle\Service\UtilService;

/**
 * Web Service para Telcos Mobile Tecnico
 * @author wsanchez
 * @author ltama
 */
class ComercialMobileWSController extends BaseWSController {

    
    
    /**
     * Metodo que procesa las diferentes opciones del webservice
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.0
     * @Soap\Method("procesar")
     * @Soap\Param("data", phpType = "string")
     * @Soap\Result(phpType = "string")     
     * 
     * 
     * Modificación: Se agrega nuevos procesos generarPinSecurity y autorizarContratoDigital
     * @author Veronica Carrasco <vcarrasco@telconet.ec>
     * @version 1.1 04-09-2016
     * 
     */
    public function procesar($data)
    {        
        // solo hacer decode JSON para trabajar todo con arreglos, requerido por capa service
        $data = $this->decode($data);
        if (!empty($data['op']) && !empty($data['data']))
        {
            //se agregan validaciones de token de seguridad            
            $token = $this->validateGenerateToken($data['token'], $data['source'], $data['user']);
            if (!$token) {
                // si la validacion devuelve false, devolver response con error segun estructura del metodo
                throw new \SoapFault('ws', 'TokenInvalid');
            }
            
            switch ($data['op'])
            {
                case 'notificar':
                    $response = $this->notificar($data['data']);
                    break;
                case 'cargarImagen':
                    $response = $this->cargarImagen($data['data']);
                    break;
                case 'crearContrato':
                    $response = $this->crearContrato($data['data']);
                    break;
                case 'crearPreCliente':
                    $response = $this->crearPreCliente($data['data']);
                    break;
                case 'crearPunto':
                    $response = $this->crearPunto($data['data']);
                    break;
                case 'crearServicio':
                    $response = $this->crearServicio($data['data']);
                    break;
                case 'generarLoginPunto':
                    $response = $this->generarLoginPunto($data['data']);
                    break;
                case 'obtenerCatalogos':
                    $response = $this->obtenerCatalogos($data['data']);
                    break;
                case 'obtenerCatalogosEmpresa':
                    $response = $this->obtenerCatalogosEmpresa($data['data']);
                    break;
                case 'obtenerEmpresas':
                    $response = $this->obtenerEmpresas($data['data']);
                    break;
                case 'obtenerPersona':
                    $response = $this->obtenerPersona($data['data']);
                    break;
                case 'obtenerPlanes':
                    $response = $this->obtenerPlanes($data['data']);
                    break;
                case 'obtenerProductos':
                    $response = $this->obtenerProductos($data['data']);
                    break;
                case 'obtenerPuntosCliente':
                    $response = $this->obtenerPuntosCliente($data['data']);
                    break;
                case 'solicitarFactibilidadServicio':
                    $response = $this->solicitarFactibilidadServicio($data['data']);
                    break;
                case 'generarPinSecurity':
                    $data["data"]["user"] = $data['user'];
                    $response = $this->generarPinSecurity($data['data']);
                    break;
                case 'autorizarContratoDigital':
                    $response = $this->autorizarContratoDigital($data);
                    break;
                case 'obtenerDatosGeneral':
                    $response = $this->obtenerDatosGeneral($data['data']);
                    break;
                case 'planificarOnLine':
                    $response = $this->planificarOnLine($data['data']);
                    break;
            }
        }
        
        //se agrega envio de token de seguridad
        $responseFinal=null;
        if (isset($response))
        {
            $responseFinal['respuesta'] = $response;
            $responseFinal['token'] = $token;
            if (!($response instanceof \BeSimple\SoapBundle\Soap\SoapResponse))
            {
                $responseFinal['respuesta']=json_encode($response);
                
            }
            $responseFinal = $this->soapReturn(json_encode($responseFinal));
            return $responseFinal;
        }
        throw new \SoapFault('ws', 'error');
    }
    
    public function notificar($mensaje)
    {  
        return $this->soapReturn(sprintf('Hello %s!', $mensaje));
    }
    
    public function obtenerEmpresas($data)
    {
        $login = $data['login'];
        
        $repoPersonaEmpresaRol = $this->getManager()->getRepository('schemaBundle:InfoPersonaEmpresaRol');
        /* @var $repoPersonaEmpresaRol \telconet\schemaBundle\Repository\InfoPersonaEmpresaRolRepository */
        $arrayEmpresas = $repoPersonaEmpresaRol->getEmpresasByPersona($login, 'Empleado');
        $arrayResult = array();
        foreach ($arrayEmpresas as $value)
        {
            $arrayResult[] = new EmpresaPersonaComplexType($value);
        }
        return new ObtenerEmpresasResponse($arrayResult);
    }
    
    /**
     * Metodo que obtiene la informacion de un cliente
     * 
     * @param array $data
     * @return ObtenerPersonaResponse
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.0
     * 
     * Modificación: Se agrega nuevos parametros en el arreglo de contrato : origen, idContrato y estado. 
     * Se incluye nuevo estado PorAutorizar en la busqueda de contrato
     * @author Veronica Carrasco <vcarrasco@telconet.ec>
     * @version 1.1 04-09-2016
     * 
     * Modificación: Se agrega el numero de contrato en al array response
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * version 1.2 23-10-2017
     * 
     */
    public function obtenerPersona($data) 
    {
        $datos_persona             = $this->obtenerDatosPersonaPrv($data['codEmpresa'], $data['identificacionCliente'], $data['prefijoEmpresa']);
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto          = $this->get('comercial.InfoPunto');
        $arrayEstadoContrato       = null;
        $arrayEstadoRol            = array();
        $puntos                    = array();
        $planes                    = array();
        //se agregan variables contrato
        $objContrato               = null;
        $arrayContrato             = null;
        $strRol                    = null;
        $boolHayServicio           = false;
        $intJurisdiccionId         = 0;
        $boolCoordinada            = false;
        $objFechaPlanificada       = "";        
        try
        {
            if ($datos_persona) 
            {
                $strRol = "Cliente";
                $puntos = $serviceInfoPunto->obtenerDatosPuntosCliente($data['codEmpresa'], $datos_persona->id, $strRol, true, true, true);
                if(!$puntos)
                {
                    $strRol = "Pre-cliente";
                    $puntos = $serviceInfoPunto->obtenerDatosPuntosCliente($data['codEmpresa'], $datos_persona->id, $strRol, true, true, true);
                }

                foreach ($puntos as $punto) 
                {
                    $key = $punto['tipoNegocioId'] . '|' . $datos_persona->formaPagoId . 
                                                     '|' . $datos_persona->tipoCuentaId . 
                                                     '|' . $datos_persona->bancoTipoCuentaId;
                    if (!isset($planes[$key])) 
                    {
                        $planes[$key] = $this->obtenerPlanesPrv($data['codEmpresa'], $punto['tipoNegocioId'], $datos_persona->formaPagoId, 
                                                                $datos_persona->tipoCuentaId, $datos_persona->bancoTipoCuentaId);
                    }
                    if (isset($punto['servicios']))
                    {
                        foreach ($punto['servicios'] as $arrayServicio)
                        {
                            $objSolicitudPrePlanificacion = $this->getDoctrine()
                                    ->getManager("telconet")
                                    ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                    ->findOneBy(array("servicioId" => $arrayServicio['id'],
                                "tipoSolicitudId" => "8"));
                            if ($objSolicitudPrePlanificacion != null) 
                            {
                                $intJurisdiccionId = $punto['ptoCoberturaId'];
                                $boolHayServicio = true;
                                if (strtoupper($objSolicitudPrePlanificacion->getEstado()) <> strtoupper('PrePlanificada')) 
                                {
                                    $objSolicitudPlanificacion = $this->getDoctrine()
                                    ->getManager("telconet")
                                    ->getRepository('schemaBundle:InfoDetalleSolHist')
                                    ->findOneBy(array("detalleSolicitudId" => $objSolicitudPrePlanificacion->getId(),
                                                      "estado"=>"Planificada"));
                                    $objFechaPlanificada =  $objSolicitudPlanificacion->getFeIniPlan();
                                    $boolCoordinada = true;
                                   
                                }
                            }
                        }
                    }
                }
                //se agrega recuperacion de contrato en caso de que exista
                //obtiene rol activos o pendientes de la persona con rol Pre-cliente
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
                $objRol = $this->getManager()->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                               ->findPersonaEmpresaRolByParams($datos_persona->id, $data['codEmpresa'],
                                                               $arrayEstadoRol, $strRol);
                if ($objRol) 
                {
                    $objContrato = $this->getManager()->getRepository('schemaBundle:InfoContrato')
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
                        $arrayContrato['feFinContratoPost']    = $objContrato->getFeFinContrato()->format('d/m/Y');
                        $arrayContrato['origen']               = $objContrato->getOrigen();
                        $arrayContrato['idContrato']  	       = $objContrato->getId();
                        $arrayContrato['estado']               = $objContrato->getEstado();
                        $arrayContrato['numeroContrato']       = $objContrato->getNumeroContrato();
                        $infoContratoFormaPago                 = $this->getManager()
                                                                      ->getRepository('schemaBundle:InfoContratoFormaPago');

                        /* @var $infoContratoFormaPago \telconet\schemaBundle\Repository\InfoContratoFormaPago */
                        $objContratoFormaPago                  = $infoContratoFormaPago
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
                        $emComunicacion        = $this->getDoctrine()->getManager('telconet_comunicacion');
                        $infoDocumentoRelacion = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion');
                        /* @var $infoDocumentoRelacion \telconet\schemaBundle\Repository\InfoDocumentoRelacion */
                        $arrayDocumentos       = $infoDocumentoRelacion->findBy(array('contratoId' => $objContrato->getId()));
                        if ($arrayDocumentos) 
                        {
                            $arrayContrato['numeroFiles'] = count($arrayDocumentos);
                        } 
                        else 
                        {
                            $arrayContrato['numeroFiles'] = 0;
                        }
                        if ($boolHayServicio) 
                        {
                            $objServicePlanificacion = $this->get('planificacion.Planificar');
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
                    }
                }
            }
        }
        catch(\Exception $ex)
        {
            error_log($ex->getMessage());
        }
        $strFormaContactoSitio = $this->container->getParameter('planificacion.mobile.codFormaContactoSitio');
        return new ObtenerPersonaResponse($datos_persona, $puntos, $planes, $arrayContrato, $arrayPlanificacion, $strFormaContactoSitio);
    }

    /**
     * @return PersonaComplexType
     */
    private function obtenerDatosPersonaPrv($codEmpresa, $identificacionCliente, $prefijoEmpresa)
    {
        /* @var $serviceCliente \telconet\comercialBundle\Service\ClienteService */
        $serviceCliente = $this->get('comercial.Cliente');
        
        $datos_persona = $serviceCliente->obtenerDatosClientePorIdentificacion($codEmpresa, $identificacionCliente, $prefijoEmpresa);
        if (!is_null($datos_persona))
        {
            $datos_persona = new PersonaComplexType($datos_persona);
            $datosFormasContacto = $serviceCliente->obtenerFormasContactoPorPersona($datos_persona->id, null, null, null, true);
            $datos_persona->setFormasContacto($datosFormasContacto['registros']);
        }
        return $datos_persona;
    }
    
    /**
     * Metodo que realiza la creación de un Pre-cliente
     * 
     * @param array $data
     * @return CrearPreClienteResponse
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.0 04-09-2016
     * 
     * @author Andrés Montero <amontero@telconet.ec>  
     * @version 1.1 16-09-2016 
     * Se agrega a la validación de si existe persona, que filtre tambien por rol Cliente
     * 
     */    
    public function crearPreCliente($data)
    {
        $clientIp = '127.0.0.1';
        /* @var $datos_persona PersonaComplexType */
        $datos_persona = $this->obtenerDatosPersonaPrv($data['codEmpresa'], $data['persona']['identificacionCliente'], $data['prefijoEmpresa']);
        if (!is_null($datos_persona) && (in_array('Pre-cliente', $datos_persona->roles) || in_array('Cliente', $datos_persona->roles)) )
        {
            // si la persona existe como pre-cliente, devolver error
            return new CrearPreClienteResponse($datos_persona, 'Persona ya existe como Pre-Cliente o Cliente');
        }
        $persona = new InfoPersona();
        if ($data['persona']['fechaNacimiento'])
        {
            $data['persona']['fechaNacimiento'] = \DateTime::createFromFormat('d/m/Y H:i:s', $data['persona']['fechaNacimiento'] . ' 00:00:00');
        }
        $data['persona']['yaexiste'] = (empty($data['persona']['id']) ? 'N' : 'S');
        /* @var $servicePreCliente \telconet\comercialBundle\Service\PreClienteService */
        $servicePreCliente = $this->get('comercial.PreCliente');
        $servicePreCliente->crearPreCliente($persona, $data['codEmpresa'], $data['idOficina'], $data['usrCreacion'], $clientIp, $data['persona'], $data['prefijoEmpresa'], $data['persona']['formasContacto']);
        $datos_persona = $this->obtenerDatosPersonaPrv($data['codEmpresa'], $persona->getIdentificacionCliente(), $data['prefijoEmpresa']);
        return new CrearPreClienteResponse($datos_persona);
    }

    public function obtenerPlanes($data)
    {
        $array = $this->obtenerPlanesPrv($data['codEmpresa'], $data['idTipoNegocio'], $data['idFormaPago'], $data['idTipoCuenta'], $data['idBancoTipoCuenta']);

        return array('plan' => $array);
    }
    
    private function obtenerPlanesPrv($codEmpresa, $idTipoNegocio, $idFormaPago, $idTipoCuenta, $idBancoTipoCuenta)
    {
        /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
        $serviceInfoServicio = $this->get('comercial.InfoServicio');
        $planes = $serviceInfoServicio->obtenerPlanesAplicables($codEmpresa, $idTipoNegocio, $idFormaPago, $idTipoCuenta, $idBancoTipoCuenta);
        $array = array();
	    for ($i = 0; $i < count($planes); $i++)
	    {
            $planInformacionDetalles = $serviceInfoServicio->obtenerPlanInformacionDetalles($planes[$i]['idPlan'], true, true, 'k', 'v', 'c','t');
	        if (!empty($planInformacionDetalles))
	        {
                $array[] = array (
                            'k' => $planes[$i]['idPlan'],
                            'v' => $planes[$i]['nombrePlan'],
                            'p' => $planInformacionDetalles['precio'],
                            //'d' => $planInformacionDetalles['descuento'],
                            //'o' => $planInformacionDetalles['tipoOrden'],
                            'l' => $planInformacionDetalles['listado'],
                );
	        }
	    }
        return $array;
    }
    
    public function obtenerCatalogos()
    {   
        /* @var $serviceComercial ComercialService */
        $serviceComercial = $this->get('comercial.Comercial');
        /* @var $serviceCliente \telconet\comercialBundle\Service\ClienteService */
        $serviceCliente = $this->get('comercial.Cliente');
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto = $this->get('comercial.InfoPunto');
        /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
        $serviceInfoServicio = $this->get('comercial.InfoServicio');
        $emGeneral = $this->getDoctrine()->getManager('telconet_general');
        $respuesta = array();

        //si-no
        $array = array(
            array('k' => 'S', 'v' => 'Si'),
            array('k' => 'N', 'v' => 'No'));
        $respuesta['siNo'] = $array;

        //tipoEmpresa
        $array = array(
            array('k' => 'Publica', 'v' => 'Publica'),
            array('k' => 'Privada', 'v' => 'Privada'));
        $respuesta['tipoEmpresa'] = $array;

        //se agregan catalogos de origenes de ingreso
        //tipoOrigenIngreso
        $array = array(
            array('k' => 'B', 'v' => 'Empleado Público'),
            array('k' => 'V', 'v' => 'Empleado Privado'),
            array('k' => 'I', 'v' => 'Independiente'),
            array('k' => 'A', 'v' => 'Ama de casa o estudiante'),
            array('k' => 'R', 'v' => 'Rentista'),
            array('k' => 'J', 'v' => 'Jubilado'),
            array('k' => 'M', 'v' => 'Remesas del exterior'));
        $respuesta['tipoOrigenIngreso'] = $array;

        //se agregan catalogos de origenes de ingreso
        //tipoDocumentoGeneral
        $array = array();
        $objTiposDocumentos = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')->findByEstado("Activo");
        foreach($objTiposDocumentos as $objTiposDocumentos)
        {
            $array[] = array('k' => $objTiposDocumentos->getId(), 'v' => $objTiposDocumentos->getDescripcionTipoDocumento());
        }
        $respuesta['tipoDocumentoGeneral'] = $array;

        //tipoIdentificacion
        $array = array(
            array('k' => 'CED', 'v' => 'Cedula'),
            array('k' => 'RUC', 'v' => 'Ruc'),
            array('k' => 'PAS', 'v' => 'Pasaporte'));
        $respuesta['tipoIdentificacion'] = $array;

        //tipoTributario
        $array = array(
            array('k' => 'NAT', 'v' => 'Natural'),
            array('k' => 'JUR', 'v' => 'Juridico'));
        $respuesta['tipoTributario'] = $array;

        //Titulo
        $array = $serviceComercial->obtenerCatalogoTitulos();
        $respuesta['titulo'] = $array;

        //Genero
        $array = array(
            array('k' => 'M', 'v' => 'Masculino'),
            array('k' => 'F', 'v' => 'Femenino'));
        $respuesta['genero'] = $array;

        //EstadoCivil
        $array = array(
            array('k' => 'S', 'v' => 'Soltero(a)'),
            array('k' => 'C', 'v' => 'Casado(a)'),
            array('k' => 'U', 'v' => 'Union Libre'),
            array('k' => 'D', 'v' => 'Divorciado(a)'),
            array('k' => 'V', 'v' => 'Viudo(a)'));
        $respuesta['estadoCivil'] = $array;

        //DireccionTributaria
        $array = array(
            array('k' => 'NAC', 'v' => 'Nacional'),
            array('k' => 'EXT', 'v' => 'Extranjera'));
        $respuesta['direccionTributaria'] = $array;

        //formasPago
        $array = $serviceCliente->obtenerFormasPago('k', 'v');
        $respuesta['formasPago'] = $array;

        //tiposCuenta
        $array = $serviceCliente->obtenerTiposCuenta('k', 'v');
        for($i = 0; $i < count($array); $i++)
        {
            $bancos = $serviceCliente->obtenerBancosTipoCuenta($array[$i]['k'], 'k', 'v');
            if(!empty($bancos))
            {
                $array[$i]['items'] = $bancos;
            }
        }
        $respuesta['tiposCuenta'] = $array;

        //formasContacto
        $array = $serviceCliente->obtenerFormasContacto('k', 'v');
        $respuesta['formasContacto'] = $array;

        //tipoMedio
        $array = $serviceInfoServicio->obtenerTiposMedio('k', 'v');
        $respuesta['tipoMedio'] = $array;

        // tipoUbicacion
        $array = $serviceInfoPunto->obtenerTiposUbicacion('k', 'v');
        $respuesta['tipoUbicacion'] = $array;
        return $respuesta;
    }

    /**
     * Devuelve un array de arrays nombrados de arrays k/v/items (clave/valor/items)
     * 
     * @author Telcos
     * @version 1.0 
     * 
     * @author Washington Sanchez <wsanchez@telconet.ec>
     * @version 1.1
     * Se agrega catalogo de Canales y sus respectivos puntos de venta
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2
     * Se agrega catalogo de nombre tecnico a los productos
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.3
     * Se cambia el origen de la lista de puntoedificio
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.4
     * Se utiliza un arreglo de parámetros la llamada del método de la consulta de productos
     *
     * @param array $data
     * 
     * @return array $respuesta
     **/
    public function obtenerCatalogosEmpresa($data)
    {
        $codEmpresa = $data['codEmpresa'];
        $arrayRespuesta = array();
        
        /* @var $serviceCliente \telconet\comercialBundle\Service\ClienteService */
        $serviceCliente = $this->get('comercial.Cliente');
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto = $this->get('comercial.InfoPunto');
        /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
        $serviceInfoServicio = $this->get('comercial.InfoServicio');
        /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
        $serviceInfoContrato = $this->get('comercial.InfoContrato');
        /* @var $serviceInfoContrato \telconet\comercialBundle\tecnico\InfoElementoService */
        $serviceInfoElemento = $this->get('tecnico.InfoElemento');
        
        // CANALES DE VENTA
        $strCanales     = 'CANALES_PUNTO_VENTA';
        $strModulo      = 'COMERCIAL';
        $strVal3        = 'CANAL';
        $emGeneral      = $this->getDoctrine()->getManager('telconet_general');
        $listaCanales   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->get($strCanales, $strModulo, '', '', '', '', $strVal3, '', '', $codEmpresa);
        $arregloCanales = array();

        foreach ($listaCanales as $entityCanal) {
            // PUNTOS DE VENTA POR CANAL
            $listaPuntosVenta   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get($strCanales, $strModulo, '', '', '', '', $entityCanal['valor1'], '', '', $codEmpresa);
            $arregloPuntosVenta = array();

            foreach ($listaPuntosVenta as $entityPuntoVenta) {
                $arregloPuntosVenta[] = array('k' => $entityPuntoVenta['valor1'], 'v' => $entityPuntoVenta['valor2']);
            }

            $arregloCanales[] = array('k' => $entityCanal['valor1'], 'v' => $entityCanal['valor2'], 'items' => $arregloPuntosVenta);
        }
        $arrayRespuesta['canales'] = $arregloCanales;
        
        // tipoNegocio

        $array = $serviceInfoPunto->obtenerTiposNegocio($codEmpresa, 'k', 'v');
        $arrayRespuesta['tipoNegocio'] = $array;
        

        // tipoContrato
        $array = $serviceInfoContrato->obtenerTiposContrato($codEmpresa, 'k', 'v');
        $arrayRespuesta['tipoContrato'] = $array;
        
        // productos
        $arrayParametroListProd = array(
            'strCodEmpresa' => $codEmpresa
        );
        $arrayList = $serviceInfoServicio->obtenerProductos($arrayParametroListProd);
        $arrayData = array();
        /* @var $value \telconet\schemaBundle\Entity\AdmiProducto */
        foreach ($arrayList as $objValue)
        {
            $arrayCaracteristicasComerciales = $serviceInfoServicio->obtenerProductoCaracteristicasComerciales($objValue->getId(), 'k', 'v');
            //$caracteristicasTecnicas = $serviceInfoServicio->obtenerProductoCaracteristicasTecnicas($value->getId(), 'k', 'v');
            $arrayData[] = array(
                            'k' => $objValue->getId(),
                            'v' => $objValue->getDescripcionProducto(),
                            'f' => $objValue->getFuncionPrecio(),
                            'c' => $arrayCaracteristicasComerciales,
                            't' => $objValue->getNombreTecnico(),
                            //'t' => $caracteristicasTecnicas
            );
        }
        $arrayRespuesta['productos'] = $arrayData;

        // puntoEdificio
        $arrayParametros[] = array('nombreElemento' => '',
                                   'ip' => '', 
                                   'tipoElemento' => "EDIFICACION", 
                                   'estado' => 'Todos',
                                   'empresa' => $codEmpresa, 
                                   'start' => "", 
                                   'limit' => "");
        $arrayList = $serviceInfoElemento->getElementosPorEmpresa($arrayParametros);


        $arrayData = array();
        //se agrega el canton para poder filtrar la lista de busqueda de edificios en el APP MobilComercial
        foreach ($arrayList['registros'] as $objValue):
        $arrayData[] = array('k' => $objValue->getId(), 
                             'v' => $objValue->getNombreElemento(),
                             'c' => $objValue->getIdCanton());
        endforeach;

        $arrayRespuesta['puntoEdificio'] = $arrayData;
        
        // puntoCobertura, canton, parroquia, sector
        $array = $serviceInfoPunto->obtenerPuntosCobertura($codEmpresa, null, 'k', 'v');
        for ($i = 0; $i < count($array); $i++)
        {
            $cantones = $serviceInfoPunto->obtenerCantonesJurisdiccion($array[$i]['k'], null, 'k', 'v');
            if (!empty($cantones))
            {
                $array[$i]['items'] = $cantones;
                for ($j = 0; $j < count($array[$i]['items']); $j++)
                {
                    $parroquias = $serviceCliente->obtenerParroquiasCanton($array[$i]['items'][$j]['k'], null, 'k', 'v');
                    if (!empty($parroquias))
                    {
                        $array[$i]['items'][$j]['items'] = $parroquias;
                        for ($k = 0; $k < count($array[$i]['items'][$j]['items']); $k++)
                        {
                            $sectores = $serviceCliente->obtenerSectoresParroquia($codEmpresa,$array[$i]['items'][$j]['items'][$k]['k'], null, 'k', 'v');
                            if (!empty($sectores))
                            {
                                $array[$i]['items'][$j]['items'][$k]['items'] = $sectores;
                            }
                        }
                    }
                }
            }
        }
        $arrayRespuesta['puntoCobertura'] = $array;

        return $arrayRespuesta;
    }
    
    /**
     * Genera un login para un nuevo punto del cliente dado
     */
    public function generarLoginPunto($data)
    {
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto = $this->get('comercial.InfoPunto');
        $login = $serviceInfoPunto->generarLogin($data['codEmpresa'], $data['idCanton'], $data['idPersona'], (empty($data['idTipoNegocio']) ? null : $data['idTipoNegocio']));
        
        return array('login' => $login);
    }
    
    /**
     * Crea un punto en base a los parametros dentro del arreglo recibido
     * 
     * @author Telcos
     * @version 1.0 
     * 
     * @author Washington Sanchez <wsanchez@telconet.ec>
     * @version 1.1
     * Se agregan datos para registrar canales y punto de venta del punto creado
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2
     * Se agregan datos para registrar el origenWeb del punto creado
     *  
     * @param array $data
     * 
     * @return array $response
     **/
    public function crearPunto($data)
    {
        $clientIp = '127.0.0.1';
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto = $this->get('comercial.InfoPunto');
        try
        {
            $data['punto']['file'] = (empty($data['punto']['file']) ? null : $this->writeAndGetFile($data['punto']['file']));
            $data['punto']['fileDigital'] = (empty($data['punto']['fileDigital']) ? null : $this->writeAndGetFile($data['punto']['fileDigital']));
            
            //se agrega codigo para validar informacion y poder crear el punto de manera correcta
            if ($data['punto']['nombrePunto'])
            {
                $data['punto']['nombrepunto'] = $data['punto']['nombrePunto'];
            }
            if ($data['punto']['descripcionPunto'])
            {
                $data['punto']['descripcionpunto'] = $data['punto']['descripcionPunto'];
            }
            if ($data['punto']['dependeDeEdificio'])
            {
                $data['punto']['dependedeedificio'] = $data['punto']['dependeDeEdificio'];
            }
            if ($data['punto']['puntoEdificioId'])
            {
                $data['punto']['puntoedificioid'] = $data['punto']['puntoEdificioId'];
            }
            if ($data['punto']['esEdificio'])
            {
                $data['punto']['esedificio'] = $data['punto']['esEdificio'];
            }
            if ($data['punto']['idCanalVenta'])
            {
                $data['punto']['canal'] = $data['punto']['idCanalVenta'];
            }
            if ($data['punto']['idPtoVenta'])
            {
                $data['punto']['punto_venta'] = $data['punto']['idPtoVenta'];
            }
            if ($data['idOficina'])
            {
                $data['punto']['oficina'] = $data['idOficina'];
            }
            if ($data['punto']['origenWeb'])
            {
                $data['punto']['origen_web'] = $data['punto']['origenWeb'];
            }
            
            $punto = $serviceInfoPunto->crearPunto($data['codEmpresa'], $data['usrCreacion'], $clientIp, $data['punto'], $data['punto']['formasContacto']);
            $personaResponse = $this->obtenerPersona($data);
            $response = array('error' => null, 'personaResponse' => $personaResponse);
        }
        catch (\Exception $e)
        {
            $response = array('error' => $e->getMessage());
        }
        return $response;
    }
    
    
    /**
     * Crea el servicio ingresado por el vendedor a traves del aplicativo movil
     * 
     * @param type $data
     * @return type
     * 
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-06-2017 - Se modifican a un arreglo de parámetros enviados a la función crearServicio en el service InfoServicioService
     */
    public function crearServicio($data)
    {
        $clientIp = '127.0.0.1';
        /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
        $serviceInfoServicio = $this->get('comercial.InfoServicio');
        try
        {
            $arrayParamsServicio = array(   "codEmpresa"        => $data['codEmpresa'],
                                            "idOficina"         => $data['idOficina'],
                                            "entityPunto"       => $data['idPto'],
                                            "entityRol"         => null,
                                            "usrCreacion"       => $data['usrCreacion'],
                                            "clientIp"          => $clientIp,
                                            "tipoOrden"         => $data['tipoOrden'],
                                            "ultimaMillaId"     => $data['ultimaMillaId'],
                                            "servicios"         => $data['servicios'],
                                            "strPrefijoEmpresa" => $data['prefijoEmpresa'],
                                            "session"           => null
                                    );
            $serviceInfoServicio->crearServicio($arrayParamsServicio);
            $personaResponse = $this->obtenerPersona($data);
            $response = array('error' => null, 'personaResponse' => $personaResponse);
        }
        catch (\Exception $e)
        {
            $response = array('error' => $e->getMessage());
        }
        return $response;
    }
    
    public function solicitarFactibilidadServicio($data)
    {
        $clientIp = '127.0.0.1';
        /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
        $serviceInfoServicio = $this->get('comercial.InfoServicio');
        try
        {
            $result = $serviceInfoServicio->solicitarFactibilidadServicio($data['codEmpresa'], $data['prefijoEmpresa'], $data['idServicio'], $data['usrCreacion'], $clientIp);
            $personaResponse = $this->obtenerPersona($data);
            $response = array('result' => $result, 'personaResponse' => $personaResponse);
        }
        catch (\Exception $e)
        {
            $response = array('error' => $e->getMessage());
        }
        return $response;
    }
    
    /**
     * crearContrato, método que crea el contrato para un cliente
     * 
     * @author Juan Carlos La Fuentw <jlafuente@telconet.ec>
     * @version 1.0 29-10-2015
     * @since 1.0
     * 
     * @author Fabricio Bermeo<fbermeo@telconet.ec>
     * @version 1.1 17-07-2016
     * Se modifica el método, llamando al service de contrato digital, para la creación del
     * certificado digital para el cliente
     * 
     * Actualización: Antes de llamar a funcion crearContrato de service InfoContratoService, 
     * se cambia contenido de parametro origen de MOVIL a WEB, para que cree por defecto el contrato como WEB
     * @author Andrés Montero<amontero@telconet.ec>
     * @version 1.2 09-02-2017
     * 
     * 
     * Actualización: 
     * - Se corrige validacion que si enviar pin por SMS da error entonces se escribe un error_log 
     * - Se inserta en bd el error indicando los detalles del mismo.
     * - En el catch se inserta en bd el error.
     * - Se agrega variable strMensajeSms para enviar como respuesta al movil y este sea presentado al usuario.
     * 
     * Para realizar el insert de errores en bd se usa el service schema.Util
     * @author Andrés Montero<amontero@telconet.ec>
     * @version 1.3 07-03-2017
     * 
     * Actualización: 
     * - Se valida que si no crea contrato asigne mensaje de error al response
     * - Se valida que si no se puede crear certificado se rechace el contrato
     * @author Andrés Montero<amontero@telconet.ec>
     * @version 1.4 14-03-2017
     * 
     * Actualización: 
     * - En el catch se retorna en el $response el mensaje retornado por el Exception
     * @author Andres Montero<amontero@telconet.ec>
     * @version 1.5 20-04-2017
     * 
     * Se envia el pin al mail del vendedor y al contacto determinado en parameters.yml
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * version 1.6 16-05-2018
     * 
     * Por motivos de auditoria el mail del pin se envia solo al cliente
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * version 1.7 03-07-2018 
     * 
     * Se registran en el log de errores el envio de sms y mail del pin 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.8 10-09-2018
     * 
     * Se cambia el envio del pin para que se realice después que la obtención del certificado digital haya sido exitosa
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.9 19-09-2018
     * 
     */
    public function crearContrato($data)
    {   
        ini_set('max_execution_time', 16000000);
        $clientIp = '127.0.0.1';
        /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
        $serviceInfoContrato      = $this->get('comercial.InfoContrato');
        /* @var $serviceUtil \telconet\schemaBundle\Service\UtilService */
        $serviceUtil              = $this->get('schema.Util');
        /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoAprobService */
        $serviceInfoContratoAprob = $this->get('comercial.InfoContratoAprob');
        $booleanRechazaCd         = false;
        $objResult                = null;
        try
        {
            if ($data['contrato']['feFinContratoPost'])
            {
                $data['contrato']['feFinContratoPost'] = \DateTime::createFromFormat('d/m/Y H:i:s', 
                                                                                     $data['contrato']['feFinContratoPost'] . ' 00:00:00');
            }
            //se agrega generacion de archivos temporales para subida de archivos de contratos
            $arrayTipoDocumentos    = array (); 
            $datos_form_files       = array (); 
            foreach($data['contrato']['files'] as $file):
                $tipo                  = $file['tipoDocumentoGeneralId'];
                $arrayTipoDocumentos[] = $tipo;
                $archivo               = $this->writeAndGetFile($file['file']);
                $datos_form_files[]    = $archivo; 
            endforeach;
            // Se configuran los parametros iniciales con los que iniciara el contrato
            // Dado que es necesario ingresar un pin para autorizar el contrato este no
            // debe iniciar con un estado PENDINTE sino con uno anterior
            $data['contrato']['arrayTipoDocumentos']    = $arrayTipoDocumentos;
            $data['contrato']['valorEstado']            = 'PorAutorizar'; // Estado Inicial Contrato
            $data['contrato']['datos_form_files']       = array('imagenes',
                                                                $datos_form_files);
            // Al crear el contrato se debe indicar el origen del mismo
            $arrayParametrosContrato                   = array();
            $arrayParametrosContrato['codEmpresa']     = $data['codEmpresa'];
            $arrayParametrosContrato['prefijoEmpresa'] = $data['prefijoEmpresa']; 
            $arrayParametrosContrato['idOficina']      = $data['idOficina']; 
            $arrayParametrosContrato['usrCreacion']    = $data['usrCreacion']; 
            $arrayParametrosContrato['clientIp']       = $clientIp;
            $arrayParametrosContrato['datos_form']     = $data['contrato']; 
            $arrayParametrosContrato['check']          = null; 
            $arrayParametrosContrato['clausula']       = null;
            $arrayParametrosContrato['origen']         = 'WEB';
            

            $objResult     = $serviceInfoContrato->crearContrato($arrayParametrosContrato);

            $strMensaje    = "";
            $strMensajeSms = "SMS no enviado";

            if(is_object($objResult))
            {

                // Se solicita la generacion de un certificado para poder firmar los documentos

                $serviceContratoDigital = $this->get('comercial.InfoContratoDigital');
                $arrayCrearCd           = $serviceContratoDigital->crearCertificado($objResult);

                //Se consulta si fue creado el certificado
                switch($arrayCrearCd['salida'])
                {
                    case '1':
                        // Si el certificado fue generado correctamente entonces procedemos a documentarlo
                        // Se envian todos los documentos de soporte a la Entidad emisora del certificado
                        $arrayDocumentarCd  = $serviceContratoDigital->documentarCertificado($objResult,$data);
                        $strMensaje         = $arrayCrearCd['mensaje'];
                        //Consulta si fue documentado el certificado
                        switch($arrayDocumentarCd['salida'])
                        {
                            case '1':
                                $strMensaje .= " (".$arrayDocumentarCd['mensaje'].")";
                                $response    = array( 
                                                     'idContrato'     => $objResult->getId(), 
                                                     'numeroContrato' => $objResult->getNumeroContrato(),
                                                     'estadoContrato' => $objResult->getEstado(), 
                                                     'pin'            => $arrayResponseService['pin'],
                                                     'mensaje'        => $strMensaje,
                                                     'mensajeSms'     => $strMensajeSms
                                                    );

                                // Generamos el PIN con el cual se confirmara el contrato
                                /* @var $serviceSeguridad SeguridadService */
                                $serviceSeguridad     = $this->get('seguridad.Seguridad');
                                $strMensajeError = "ANTES ENVIO SMS:. Empresa: " . $data['codEmpresa'] . " Telefono: " . $data['contrato']['numeroTelefonico'] . 
                                                   " identificación: " . $data['contrato']['documentoIdentificacion'] . 
                                                   " idContrato =>" .$objResult->getId();
                                $serviceUtil->insertError(
                                                          'Telcos+', 
                                                          'ComercialMobileWSController->crearContrato', 
                                                          $strMensajeError,
                                                          $data['usrCreacion'],
                                                          $clientIp
                                                         );


                                $arrayResponseService = $serviceSeguridad->generarPinSecurity($data['contrato']['documentoIdentificacion'], 
                                                                                              $data['contrato']['numeroTelefonico'], 
                                                                                              $data['codEmpresa']);

                                if (isset($arrayResponseService['status']) && $arrayResponseService['status'] == 'ERROR_SERVICE') 
                                {
                                    $strMensajeError = "ERROR ENVIO SMS: Empresa: " . $data['codEmpresa'] . " Telefono: " . $data['contrato']['numeroTelefonico'] .
                                                       $arrayResponseService['mensaje'] . " identificación: " . $data['contrato']['documentoIdentificacion'] .
                                                       ". idContrato =>".$objResult->getId();
                                    $serviceUtil->insertError(
                                                              'Telcos+', 
                                                              'ComercialMobileWSController->crearContrato', 
                                                              $strMensajeError,
                                                              $data['usrCreacion'],
                                                              $clientIp
                                                             );
                                }
                                else
                                {
                                    $strMensajeError = "DESPUES ENVIO SMS: Empresa: " . $data['codEmpresa'] . " Telefono: " . $data['contrato']['numeroTelefonico'] .
                                                       $arrayResponseService['mensaje'] . " Pin# " . $arrayResponseService['pin'] .
                                                       " identificación: " . $data['contrato']['documentoIdentificacion'] . ". idContrato =>" .$objResult->getId();
                                    $serviceUtil->insertError(
                                                              'Telcos+', 
                                                              'ComercialMobileWSController->crearContrato', 
                                                              $strMensajeError,
                                                              $data['usrCreacion'],
                                                              $clientIp
                                                             );

                                    $strMensajeSms = "SMS enviado";
                                }
                                if (isset($arrayResponseService['pin']))
                                {
                                    $strPin =  $arrayResponseService['pin'];


                                    $strMensaje = $this->get('templating')->render('comercialBundle:infocontrato:notificacionPin.html.twig', 
                                             array('strCedula' => $data['contrato']['documentoIdentificacion'], 
                                                   'strPin' => $strPin));
                                    $strAsunto = "Pin de Instalacion";

                                    //DESTINATARIOS.... 
                                    $emGeneral = $this->getDoctrine()->getManager();
                                    $arrayTo = array();

                                    $arrayParametros = array("intIdPersona"     => $data['contrato']['idcliente'],
                                                             "strFormaContacto" => 'Correo Electronico');
                                    $arrayFormasContactoCliente = $emGeneral->getRepository('schemaBundle:InfoPersona')
                                                             ->getContactosByIdPersonaAndFormaContacto($arrayParametros);
                                    if($arrayFormasContactoCliente)
                                    {
                                        foreach($arrayFormasContactoCliente as $arrayFormaContacto)
                                        {
                                             $arrayTo[] = $arrayFormaContacto['valor'];
                                        }
                                    }

                                    /* @var $envioPlantilla EnvioPlantilla */
                                    $strMensajeError = "ANTES ENVIO MAIL: Empresa: " . $data['codEmpresa'] . " Telefono: " . $data['contrato']['numeroTelefonico'] .
                                                       " Pin# " . $strPin .
                                                       " Asunto: " . $strAsunto .
                                                       " To: " . implode(",", $arrayTo ) . 
                                                       " Mensaje: " . $strMensaje .
                                                       " identificación: " . $data['contrato']['documentoIdentificacion'] . ". idContrato =>" .$objResult->getId();
                                    $serviceUtil->insertError(
                                                              'Telcos+', 
                                                              'ComercialMobileWSController->crearContrato', 
                                                              $strMensajeError,
                                                              $data['usrCreacion'],
                                                              $clientIp
                                                             );

                                    $objEnvioPlantilla = $this->get('soporte.EnvioPlantilla');
                                    $objEnvioPlantilla->enviarCorreo($strAsunto, $arrayTo, $strMensaje);

                                    $strMensajeError = "DESPUES ENVIO MAIL: Empresa: " . $data['codEmpresa'] . " Telefono: " . $data['contrato']['numeroTelefonico'] .
                                                       " Pin# " . $strPin .
                                                       " Asunto: " . $strAsunto .
                                                       " To: " . implode(",", $arrayTo ) . 
                                                       " Mensaje: " . $strMensaje .
                                                       " identificación: " . $data['contrato']['documentoIdentificacion'] . ". idContrato =>" .$objResult->getId();
                                    $serviceUtil->insertError(
                                                              'Telcos+', 
                                                              'ComercialMobileWSController->crearContrato', 
                                                              $strMensajeError,
                                                              $data['usrCreacion'],
                                                              $clientIp
                                                             );                    
                                }
                                break;
                            default:
                                $strMensaje      .= $arrayDocumentarCd['mensaje']?$arrayDocumentarCd['mensaje']:", Error al documentar certificado";
                                $booleanRechazaCd = true;
                                break;
                        }
                        break;
                    default:
                        $strMensaje       = $arrayCrearCd['mensaje']?$arrayCrearCd['mensaje']:"Error al crear certificado";
                        $booleanRechazaCd = true;
                        break;
                }
            }
            else
            {
                $response = array('error' => "Error cuando se intento crear el contrato");
            }
            //Se consulta si se debe rechazar contrato o no
            if($booleanRechazaCd)
            {
                $serviceUtil->insertError(
                                          'Telcos+', 
                                          'ComercialMobileWSController->crearContrato', 
                                          $strMensaje,
                                          $data['usrCreacion'],
                                          $clientIp
                                         );
                $arrayParametrosRechazo['strUsrCreacion'] = $data['usrCreacion'];
                $arrayParametrosRechazo['strIpCreacion']  = $clientIp;
                $arrayParametrosRechazo['strMotivo']      = 'Contrato en Estado Rechazado';
                $arrayParametrosRechazo['objContrato']    = $objResult;
                //Se rechaza automticamente el contrato porque el proceso no se completo
                $serviceInfoContratoAprob->rechazarContratoPorError($arrayParametrosRechazo);
                $response = array('error' => $strMensaje);
            }
        }
        catch (\Exception $e)
        {
            $response = array('error' => $e->getMessage());
            $serviceUtil->insertError(
                                      'Telcos+', 
                                      'ComercialMobileWSController->crearContrato', 
                                      $e->getMessage(),
                                      $data['usrCreacion'],
                                      $clientIp
                                     );
            if(is_object($objResult))
            {
                $arrayParametrosRechazo['strUsrCreacion'] = $data['usrCreacion'];
                $arrayParametrosRechazo['strIpCreacion']  = $clientIp;
                $arrayParametrosRechazo['strMotivo']      = 'Contrato en Estado Rechazado';
                $arrayParametrosRechazo['objContrato']    = $objResult;
                //Se rechaza automticamente el contrato porque el proceso no se completo
                $serviceInfoContratoAprob->rechazarContratoPorError($arrayParametrosRechazo);
            }
        }
        return $response;
    }
    
    public function cargarImagen($data)
    {
        return $this->writeTempFile($data);
    }
    
    /**
     * Escribe un archivo temporal con la data que debe estar encodada en base64,
     * devuelve la ruta del archivo creado en /tmp, con prefijo "telcosws_"
     * @param string $data (encodado en base64)
     * @return string ruta del archivo creado
     */
    private function writeTempFile($data)
    {
        $path = tempnam('/tmp', 'telcosws_');
        $ifp = fopen($path, "wb");
        if (strpos($data,',') !== false)
        {
            $data = explode(',', $data)[1];
        }
        fwrite($ifp, base64_decode($data));
        fclose($ifp);
        return $path;
    }

    /**
     * Devuelve una referencia a un archivo existente en el servidor segun la ruta dada
     * @param string $path
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    private function getFile($path)
    {
        $file = new File($path);
        return $file;
    }

    /**
     * Escribe un archivo con la data base64 y devuelve una referencia al mismo
     * @param string $data (encodado en base64)
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    private function writeAndGetFile($data)
    {
        if (empty($data))
        {
            return null;
        }
        return $this->getFile($this->writeTempFile($data));
    }

    public function obtenerPuntosCliente($data)
    {
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto = $this->get('comercial.InfoPunto');
        $array = $serviceInfoPunto->obtenerDatosPuntosCliente($data['codEmpresa'], $data['idPersona'], $data['rol'], true, true, true);
        
        return array('puntos' => $array);
    }
    
    /**
     * Genera un pin utilizado para autorizar los contratos
     * @param type $data
     * @return type
     * @throws \Exception
     * 
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 29-10-2015
     * 
     * Se envia el pin al mail del vendedor y al contacto determinado en parameters.yml
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * version 1.1 16-05-2018
     * 
     * Por motivos de auditoria el mail del pin se envia solo al cliente
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * version 1.2 03-07-2018
     * 
     * Se registran en el log de errores el envio de sms y mail del pin 
     * @author Edgar Pin Villavicencion <epin@telconet.ec>
     * @version 1.3 10-09-2018
     * 
     */
    private function generarPinSecurity($data) {
        $arrayResponse    = array();
        $strMensaje       = "";         
        $serviceSeguridad = $this->get('seguridad.Seguridad');
        $serviceUtil              = $this->get('schema.Util');        
        /* @var $serviceSeguridad SeguridadService */
        try
        {
            $strMensajeError = "ANTES ENVIO SMS:. Empresa: " . $data['codEmpresa'] . " Telefono: " . $data['numero'] . 
                               " identificación: " . $data['usuario'];
            $serviceUtil->insertError(
                                      'Telcos+', 
                                      'ComercialMobileWSController->crearContrato', 
                                      $strMensajeError,
                                      $data['usrCreacion'],
                                      "127.0.0.1"
                                     );
            
            // Generacion del PIN Security
            $arrayResponseService = $serviceSeguridad->generarPinSecurity($data['usuario'], 
                                                                     $data['numero'], 
                                                                     $data['codEmpresa']);
    
            if (isset($arrayResponseService['status']) && $arrayResponseService['status'] == 'ERROR_SERVICE')
            {
                $strMensaje = $arrayResponseService['mensaje'];
                throw new \Exception('ERROR_PARCIAL');
            }
            if (isset($arrayResponseService['pin']))
            {
                $strMensajeError = "ANTES ENVIO SMS:. Empresa: " . $data['codEmpresa'] . " Telefono: " . $data['numero'] . 
                                   " Mensaje: " . $arrayResponseService['mensaje'] . " Pin# " . $arrayResponseService['pin'] . 
                                   " identificación: " . $data['usuario'];
                $serviceUtil->insertError(
                                          'Telcos+', 
                                          'ComercialMobileWSController->crearContrato', 
                                          $strMensajeError,
                                          $data['usrCreacion'],
                                          "127.0.0.1"
                                         );
                
                $strPin =  $arrayResponseService['pin'];

                $strMensaje = $this->get('templating')->render('comercialBundle:infocontrato:notificacionPin.html.twig', 
                             array('strCedula' => $data['usuario'], 
                                   'strPin' => $strPin));


                $strAsunto = "Pin de Instalacion";

                //DESTINATARIOS.... 
                $emGeneral = $this->getDoctrine()->getManager();
                $arrayParametros = array("intIdPersona"     => $data['idcliente'],
                                         "strFormaContacto" => 'Correo Electronico');
                $arrayTo = array();
                $arrayFormasContactoCliente = $emGeneral->getRepository('schemaBundle:InfoPersona')
                                         ->getContactosByIdPersonaAndFormaContacto($arrayParametros);

                if($arrayFormasContactoCliente)
                {
                    foreach($arrayFormasContactoCliente as $arrayFormaContacto)
                    {
                         $arrayTo[] = $arrayFormaContacto['valor'];
                    }
                }                         

                $strMensajeError = "ANTES ENVIO MAIL: Empresa: " . $data['codEmpresa'] . " Telefono: " . $data['numero'] .
                                   " Pin# " . $strPin .
                                   " Asunto: " . $strAsunto .
                                   " To: " . implode(",", $arrayTo ) . 
                                   " Mensaje: " . $strMensaje .
                                   " identificación: " . $data['usuario'];
                $serviceUtil->insertError(
                                          'Telcos+', 
                                          'ComercialMobileWSController->crearContrato', 
                                          $strMensajeError,
                                          $data['usrCreacion'],
                                          "127.0.0.1"
                                         );
                
                /* @var $envioPlantilla EnvioPlantilla */                
                $objEnvioPlantilla = $this->get('soporte.EnvioPlantilla');
                $objEnvioPlantilla->enviarCorreo($strAsunto, $arrayTo, $strMensaje);

                $strMensajeError = "DESPUES ENVIO MAIL: Empresa: " . $data['codEmpresa'] . " Telefono: " . $data['numero'] .
                                   " Pin# " . $strPin .
                                   " Asunto: " . $strAsunto .
                                   " To: " . implode(",", $arrayTo ) . 
                                   " Mensaje: " . $strMensaje .
                                   " identificación: " . $data['usuario'];
                $serviceUtil->insertError(
                                          'Telcos+', 
                                          'ComercialMobileWSController->crearContrato', 
                                          $strMensajeError,
                                          $data['usrCreacion'],
                                          "127.0.0.1"
                                         );
                
            }
            $arrayResponse['pin'] = $arrayResponseService['pin'];
        }
        catch (\Exception $e)
        {
            if ($e->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResponse['status']     = $this->status['ERROR_PARCIAL'];
                $arrayResponse['mensaje']    = $strMensaje;
            }
            else
            {
                $arrayResponse['status']     = $this->status['ERROR'];
                $arrayResponse['mensaje']    = $this->mensaje['ERROR'];
            }
            return $arrayResponse;
        }
    
        $arrayResponse['status']     = $this->status['OK'];
        $arrayResponse['mensaje']    = $this->mensaje['OK'];
    
        return $arrayResponse;
    }
    
    /**
     * Metodo que realiza la autorizacion de un contrato creado, 
     * firma los documentos asociados al mismo y los guarda en TELCOS
     * @param type $data
     * @return type
     * @throws \Exception
     * 
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @author Veronica Carrasco <vcarrasco@telconet.ec>
     * @version 1.0 29-10-2015
     * @since 1.0
     * 
     * Actualización: Antes de llamar a funcion guardarProcesoAprobContrato de service InfoContratoAprobService 
     * se agrega parametro strOrigen para luego de aprobacion guardar contratoen campo ORIGEN=MOVIL
     * @author Andrés Montero<amontero@telconet.ec>
     * @version 1.1 09-02-2017
     * 
     * Actualización: Se obtiene el user de arreglo $data y se lo usa para
     * grabar el usuario en el historial del servicio
     * @author Andrés Montero<amontero@telconet.ec>
     * @version 1.2 08-03-2017
     * 
     * Actualización: Si existio un error al validar pin entonces se registra el error en base de datos y en el error_log
     * @author Andrés Montero<amontero@telconet.ec>
     * @version 1.3 15-03-2017
     * 
     * Se envía el parámetro strEmpresaCod al service de aprobación de contrato porque es necesario para la asignación del ciclo.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.4
     * @since 24-04-2018
     * 
     * Se verifica que el certificado exista en DBFIRMAELECT.INFO_CERTIFICADO, si no existe se reversa el contrato
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.4
     * @since 09-05-2018
     * 
     */
    private function autorizarContratoDigital($data)
    {
        ini_set('max_execution_time', 400000);
        $strMensaje             = "";
        $arrayResponse          = array();
        $arrayDataRequest       = $data['data']; // idContrato y Pin Security
        $strUsrCreacion         = $data['user'];
        /* @var $serviceUtil \telconet\schemaBundle\Service\UtilService */
        $serviceUtil            = $this->get('schema.Util');
        /* @var $serviceSeguridad SeguridadService */
        $serviceSeguridad       = $this->get('seguridad.Seguridad');
        $objServiceCrypt        = $this->get('seguridad.crypt');
        
        /* @var $serviceContratoAprob InfoContratoAprobService */
        $serviceContratoAprob   = $this->get('comercial.InfoContratoAprob');
        $serviceContrato        = $this->get('comercial.InfoContrato');
        
        try
        {
            $objCertificado = $this->getManager("telconet")->getRepository('schemaBundle:InfoCertificado')
                   ->findOneBy(array("numCedula" => $arrayDataRequest['usuario']));
            if(count($objCertificado) <= 0)
            {
                $objContrato = $this->getManager("telconet")->getRepository('schemaBundle:InfoContrato')
                                                            ->findOneBy(array(
                                                                          "id" => $arrayDataRequest['contrato']
                                                                         ));
                $strMensaje =  'No se encuentra certificado, se reversa el contrato digital. Por favor volver a consultar cliente';           
                $arrayParametros['objContrato']    = $objContrato;
                $arrayParametros['strUsrCreacion'] = $strUsrCreacion;
                $arrayParametros['strIpCreacion']  = "127.0.01";
                $arrayParametros['strMotivo']      = $strMensaje;
                $objPersonaEmpresaRol     = $serviceContratoAprob->rechazarContratoPorCertificado($arrayParametros);
                $arrayResponse['mensaje'] = $strMensaje;
                throw new \Exception('ERROR_PARCIAL');
            }
            // ========================================================================
            // Validacion del PinCode
            // ========================================================================       
            $arrayResponseService    = $serviceSeguridad->validarPinSecurity($arrayDataRequest['pincode'],
                                                                             $arrayDataRequest['usuario'],
                                                                             $arrayDataRequest['codEmpresa']);

            if (isset($arrayResponseService['status']) && $arrayResponseService['status'] == 'ERROR_SERVICE') 
            {
                $strMensaje = $arrayResponseService['mensaje'];
                $serviceUtil->insertError(
                                          'Telcos+',
                                          'ComercialMobileWSController->autorizarContratoDigital', 
                                          $strMensaje,
                                          $strUsrCreacion,
                                          '127.0.0.1'
                                         );
                throw new \Exception('ERROR_PARCIAL');
            }
            // ========================================================================
            // Autorizar el contrato digitalmente
            // ========================================================================          
            // ========================================================================
            // Enviar a firmar los documentos 
            // ========================================================================
            //Obtener datos contrato
            $serviceContratoDigital  = $this->get('comercial.InfoContratoDigital');
            $arrayDocumentosFirmados = $serviceContratoDigital->firmarDocumentos($arrayDataRequest);
            $strMensaje              = $arrayDocumentosFirmados['mensaje'];            
            
            switch($arrayDocumentosFirmados['salida'])
            {
                case '0':
                    $arrayResponse['status']     = $this->status['ERROR_PARCIAL'];
                    $arrayResponse['mensaje']    = $arrayDocumentosFirmados['mensaje'];
                    break;
                case '1':
                    $intIdContrato      = $arrayDataRequest['contrato'];
                    $objContrato        = $arrayDocumentosFirmados['objContrato'];
                    $arrayDocumentos    = $arrayDocumentosFirmados['documentos'];
                    if(is_object($objContrato))
                    {
                        $strPrefixMensaje = 'El contrato: ' . $objContrato->getNumeroContrato();
                        $arrayResponseService['strObservacionHistorial'] = $strPrefixMensaje . ' ' . $arrayResponseService['strObservacionHistorial'];
                        
                        // ================================================================================
                        $objPersonaEmpresaRol   = $serviceContratoAprob->getDatosPersonaEmpresaRolId($objContrato->getPersonaEmpresaRolId()->getId());
                        $arrayUltimaMilla       = $serviceContratoDigital->getDatosUltimaMilla($objPersonaEmpresaRol);

                        // Obtenemos la forma de pago que nos servira para determinar el valor de la instalacion
                        // de los servicios contratados por el cliente
                        $strTipoFormaPago       = $objContrato->getFormaPagoId() ? 
                                                  $objContrato->getFormaPagoId()->getDescripcionFormaPago() : null;

                        
                        if($strTipoFormaPago != "EFECTIVO")
                        {
                            $objContratoFormaPago = $serviceContratoAprob->getDatosContratoFormaPagoId($intIdContrato);
                            if(is_object($objContratoFormaPago))
                            {
                                $strTipoFormaPago = $objContratoFormaPago->getTipoCuentaId()->getDescripcionCuenta();
                            }
                            else
                            {
                                $strTipoFormaPago = null;
                            }
                        }
                        
                        // Verificamos que tengamos una forma de pago y la ultima milla del servicio contratado
                        if($strTipoFormaPago && !empty($arrayUltimaMilla))
                        {
                            // Obtenemos la informacion de la instalacion
                            $objInstalacion = $serviceContratoDigital->getDatosInstalacion($arrayDataRequest["codEmpresa"],
                                                                                           $strTipoFormaPago,
                                                                                           $arrayUltimaMilla["data"]["codigo"],
                                                                                           $objContrato->getPersonaEmpresaRolId());
                            if($objInstalacion["estado"])
                            {
                                // Si el porcentaje de descuesto es el 100% entonces se debe activar el contrato
                                // automaticamente
                                $intPorcentaje = $objInstalacion["porcentaje"];
                                if($intPorcentaje == 100)
                                {
                                    /**
                                    * Obtiene los servicios de la persona empresa rol
                                    */ 
                                    $arrayServiciosEncontrados = array();

                                    if($arrayDataRequest['prefijoEmpresa']=='TN')
                                    {
                                        $arrayEstado        = array( 'Rechazado', 
                                                                     'Rechazada', 
                                                                     'Cancelado', 
                                                                     'Anulado', 
                                                                     'Cancel', 
                                                                     'Eliminado', 
                                                                     'Reubicado', 
                                                                     'Trasladado' );
                                        $arrayResultadoTmp  = $serviceContratoAprob->getTodosServiciosXEstadoTn( $objPersonaEmpresaRol->getId(), 
                                                                                                                 0, 
                                                                                                                 10000, 
                                                                                                                 $arrayEstado );
                                    }
                                    else
                                    {
                                        $strEstadoTmp       = 'Factible';
                                        $arrayResultadoTmp  = $serviceContratoAprob->getTodosServiciosXEstado( $objPersonaEmpresaRol->getId(),
                                                                                                               0, 
                                                                                                               10000, 
                                                                                                               $strEstadoTmp );
                                    }

                                    $arrayRegistrosServicios = $arrayResultadoTmp['registros'];

                                    if( !empty($arrayRegistrosServicios) )
                                    {
                                        foreach($arrayRegistrosServicios as $objServicioTmp)
                                        {
                                            $arrayServiciosEncontrados[] = $objServicioTmp->getId();
                                        }
                                    }
                                    // --------------------------------------------------------------------------
                                    //obtener el arreglo de los datos de formas de pago
                                    $arrayFormaPago       = array();
                                    $intIdTipoCuenta      = 0;
                                    $intIdFormaPago       = $objContrato->getFormaPagoId() ? $objContrato->getFormaPagoId()->getId() : 0;
                                    
                                    if($objContratoFormaPago)
                                    {
                                        $arrayFormaPago['bancoTipoCuentaId']    = $objContratoFormaPago->getBancoTipoCuentaId() ?
                                                                                  $objContratoFormaPago->getBancoTipoCuentaId()->getId() : 0;
                                        $arrayFormaPago['numeroCtaTarjeta']     = $objServiceCrypt
                                                                                        ->descencriptar($objContratoFormaPago->getNumeroCtaTarjeta());
                                        $arrayFormaPago['mesVencimiento']       = $objContratoFormaPago->getMesVencimiento();
                                        $arrayFormaPago['anioVencimiento']      = $objContratoFormaPago->getAnioVencimiento();
                                        $arrayFormaPago['codigoVerificacion']   = $objContratoFormaPago->getCodigoVerificacion();
                                        $arrayFormaPago['titularCuenta']        = $objContratoFormaPago->getTitularCuenta();
                                        $intIdTipoCuenta                        = $objContratoFormaPago->getTipoCuentaId() ? 
                                                                                  $objContratoFormaPago->getTipoCuentaId()->getId() : 0;
                                    }
                                    $arrayParametros = array();
                                    $arrayParametros['intIdContrato']           = $intIdContrato;
                                    $arrayParametros['arrayPersona']            = array();
                                    $arrayParametros['arrayPersonaExtra']       = array();
                                    $arrayParametros['arrayFormasContacto']     = array();
                                    $arrayParametros['arrayFormaPago']          = $arrayFormaPago;
                                    $arrayParametros['intIdFormaPago']          = $intIdFormaPago;
                                    $arrayParametros['intIdTipoCuenta']         = $intIdTipoCuenta;
                                    $arrayParametros['arrayServicios']          = $arrayServiciosEncontrados;
                                    $arrayParametros['strUsrCreacion']          = $strUsrCreacion;
                                    $arrayParametros['strIpCreacion']           = '127.0.0.1';
                                    $arrayParametros['strPrefijoEmpresa']       = $arrayDataRequest['prefijoEmpresa'];
                                    $arrayParametros['strOrigen']               = 'MOVIL';
                                    $arrayParametros['strObservacionHistorial'] = $arrayResponseService['strObservacionHistorial'];
                                    $arrayParametros['strEmpresaCod']           = $arrayDataRequest['codEmpresa'];
                                    
                                    // Aprobacion del contrato
                                    $arrayGuardarProceso = $serviceContratoAprob->guardarProcesoAprobContrato($arrayParametros);
                                    
                                    if($arrayGuardarProceso 
                                       && array_key_exists('status',$arrayGuardarProceso) 
                                       && $arrayGuardarProceso['status']=='ERROR_SERVICE')
                                    {
                                        $strMensaje = $arrayGuardarProceso['mensaje'];
                                        throw new \Exception('ERROR_PARCIAL');
                                    }
                                    $arrayPlanificacion = $arrayGuardarProceso['arrayPlanificacion'];
                                    
                                }
                                else
                                {
                                    $arrayParametrosContrato                            = array();
                                    $arrayParametrosContrato["intIdContrato"]           = $intIdContrato;
                                    $arrayParametrosContrato['strObservacionHistorial'] = $arrayResponseService['strObservacionHistorial'];
                                    $arrayParametrosContrato['strIpCreacion']           = '127.0.0.1';
                                    $arrayParametrosContrato["strOrigen"]               = "MOVIL";
                                    
                                    $serviceContrato->setearDatosContrato($arrayParametrosContrato);
                                }

                            }
                        }

                        // Guardamos los documentos firmados
                        $arrayParametrosDocumentos['codEmpresa']         = $arrayDataRequest['codEmpresa'];
                        $arrayParametrosDocumentos['strUsrCreacion']        = $data['user'];
                        $arrayParametrosDocumentos['strTipoArchivo']        = 'PDF';
                        $arrayParametrosDocumentos['intContratoId']         = $intIdContrato;
                        $arrayParametrosDocumentos['enviaMails']             = $arrayDocumentosFirmados['enviaMails'];
                       
                        $serviceContratoDigital->guardarDocumentos($arrayParametrosDocumentos,$arrayDocumentos);
                        
                        $arrayResponse['status']     = $this->status['OK'];
                        $arrayResponse['mensaje']    = $this->mensaje['OK'];
                        $arrayResponse['fechaAgenda'] = $arrayPlanificacion;
                    }
                    else
                    {
                        $arrayResponse['status']     = $this->status['ERROR_PARCIAL'];
                        $arrayResponse['mensaje']    = "Numero de contrato invalido.";
                    }
                    break;
                default:
                    $arrayResponse['status']     = $this->status['ERROR_PARCIAL'];
                    $arrayResponse['mensaje']    = "Firma de documentos no pudo ser ejecutada. Verificar parametros";
                    $strMensaje                  = $arrayDocumentosFirmados['mensaje'];
                    break;
            }
          
            
        }
        catch (\Exception $e)
        {
            error_log("ERROR:".$e->getMessage());
            if ($e->getMessage() == 'ERROR_PARCIAL')
            {
                $arrayResponse['status']     = $this->status['ERROR_PARCIAL'];
                $arrayResponse['mensaje']    = $strMensaje;
            }
            else
            {
                $arrayResponse['status']     = $this->status['ERROR'];
                $arrayResponse['mensaje']    = $this->mensaje['ERROR'];
            }
            return $arrayResponse;
        }
        return $arrayResponse;
    }
    
     /*
      *  Actualización: Se obtiene el user de arreglo $data y se lo usa para
      * obtener la data de los puntos activos y servicios que sean de grupo internet
      * en el caso que tengan producto netvoice se obtine el numero de ese producto netvoice
      * @author jose vinueza<jdvinueza@telconet.ec>
      * @version 1.2 18-08-2017
      * 
     */
    private function obtenerDatosGeneral($strData)
    {
        $objPersona  = $this->obtenerDatosPersonaPrv($strData['codEmpresa'], $strData['identificacionCliente'], $strData['prefijoEmpresa']);
        $serviceInfoPunto = $this->get('comercial.InfoPunto');
        $arrayPuntos = array();
        try
        {
            if ($objPersona) 
            {
                $strRol = "Cliente";
                $arrayPuntos = $serviceInfoPunto->obtenerDatosPuntosCliente($strData['codEmpresa'], $objPersona->id, $strRol, true, true, true);
                
                if(!$arrayPuntos)
                {
                    $strRol = "Pre-cliente";
                    $arrayPuntos = $serviceInfoPunto->obtenerDatosPuntosCliente($strData['codEmpresa'], $objPersona->id, $strRol, true, true, true);
                }
                
                $arrayPuntosAux = array();
                $boolEsInternet = false;
                foreach ($arrayPuntos as $objPunto)
                {
                   if($objPunto["estado"] == 'Activo')
                    {
                        $arrayServicios = array();
                        foreach($objPunto['servicios'] as $objServicio)
                        {
                            
                            if($objServicio["planId"] != null)
                            {
                                $arrayParametros = array("strCodEmpresa" => $strData['codEmpresa'],
                                                         "strGrupo"      => $strData['grupo'], 
                                                         "idPlan"        => $objServicio["planId"]);

                                $boolObj = $this->getDoctrine()->getManager("telconet")
                                                               ->getRepository('schemaBundle:InfoPlanCab')
                                                               ->isPlanesByGrupo($arrayParametros);

                                if($boolObj)
                                {
                                  $boolEsInternet = true;  
                                }
                            }

                            if(
                                 $objServicio["descripcionProducto"] != null && 
                                 $objServicio["descripcionProducto"] == 'NETVOICE'
                             )
                             {

                                $arrayCarcteristicas = $this->getDoctrine()->getManager("telconet")
                                                           ->getRepository('schemaBundle:InfoServicioProdCaract')
                                                           ->getCaracteristicasServicios($objServicio["id"], $objServicio["estado"]);
                                $arrayCarcteristicasAux = array();
                                foreach($arrayCarcteristicas as $caracteristica)
                                {
                                    if($caracteristica['DESCRIPCION_CARACTERISTICA'] != null && 
                                            $caracteristica['DESCRIPCION_CARACTERISTICA'] == 'NUMERO')
                                    {
                                        $arrayCarcteristicasAux[] = $caracteristica;
                                    }
                                }
                                $objServicio['caracteristicas'] = $arrayCarcteristicasAux ;                           
                             }                        
                             $arrayServicios[] = $objServicio;
                        }
                        $objPunto['servicios'] = $arrayServicios;
                        $arrayPuntosAux[] = $objPunto;   
                    }
                   
                }
                if($boolEsInternet)
                {
                   $arrayPuntos = $arrayPuntosAux; 
                }
                else
                {
                   $arrayPuntos = null;  
                }                                
            }
        }catch(\Exception $ex)
        {
            error_log($ex->getMessage());
        }
        
        return new ObtenerDatosClienteResponse($arrayPuntos, $objPersona);
    }
    /*
     * @author Edgar Pin Villavicencio<epin@telconet.ec>
     * @version 1.0 22-03-2018  -Coordina la solicitud de instalacion desde el mobile comercial
     * 
     * @author Edgar Pin Villavicencio<epin@telconet.ec>
     * @version 1.0 26-06-2018  -Bug - Se corrige la obtencion de cupos moviles se suman los de las agendas que esten en ese horario  
     *
     */

    private function planificarOnLine($arrayData) 
    {
        $objSolicitudPrePlanificacion = $this->getDoctrine() 
                ->getManager("telconet")
                ->getRepository('schemaBundle:InfoDetalleSolicitud')
                ->findOneBy(array("servicioId" => $arrayData['servicioId'],
            "tipoSolicitudId" => "8"));
        $arrayFechaProgramacion = explode("T", $arrayData['fechaDesde']);
        $arrayFecha             = explode("T", $arrayData['fechaDesde']);
        $arrayF                 = explode("-", $arrayFechaProgramacion[0]);
        $arrayHoraF             = explode(":", $arrayFecha[1]);
        
        $arrayFechaInicio       = date("Y/m/d H:i", strtotime($arrayF[2] . "-" . $arrayF[1] . "-" . $arrayF[0] . " " . $arrayFecha[1]));
        
        $arrayFechaI = date_create(date('Y/m/d', strtotime($arrayFecha[0])));
        $arrayFechaI = $arrayFechaI->format("Y-m-d");
        

        $arrayFecha2 = explode("T", $arrayData['fechaHasta']);
        $arrayF2 = explode("-", $arrayFechaProgramacion[0]);
        $arrayHoraF2 = explode(":", $arrayFecha2[1]);
        $arrayFechaInicio2 = date("Y/m/d H:i", strtotime($arrayF2[2] . "-" . $arrayF2[1] . "-" . $arrayF2[0] . " " . $arrayFecha2[1]));
        $arrayFechaSms = date("d/m/Y", strtotime($arrayF2[2] . "-" . $arrayF2[1] . "-" . $arrayF2[0] . " " . $arrayFecha2[1]));

        $strHoraInicioServicio = $arrayHoraF;
        $strHoraFinServicio = $arrayHoraF2;
        $strHoraInicio = $arrayHoraF;
        $strHoraFin = $arrayHoraF2;
        $strNombreContacto = $arrayData['nombreContactoSitio'];
        $strNumeroTelefono = $arrayData['numeroContactoSitio'];

        $arrayParametros['strOrigen'] = "MOVIL";
        $arrayParametros['intIdFactibilidad'] = $objSolicitudPrePlanificacion->getId();
        $arrayParametros['dateF'] = $arrayF;
        $arrayParametros['dateFecha'] = $arrayFecha;
        $arrayParametros['strFechaInicio'] = $arrayFechaInicio;
        $arrayParametros['strFechaFin'] = $arrayFechaInicio2;
        $arrayParametros['strHoraInicioServicio'] = $strHoraInicioServicio;
        $arrayParametros['strHoraFinServicio'] = $strHoraFinServicio;
        $arrayParametros['dateFechaProgramacion'] = $arrayFechaI;
        $arrayParametros['strHoraInicio'] = $strHoraInicio;
        $arrayParametros['strHoraFin'] = $strHoraFin; 
        $arrayParametros['strObservacionServicio'] = "Contacto: " .  $strNombreContacto . " Teléfono Contacto: " . $strNumeroTelefono;
        $arrayParametros['strIpCreacion'] = "127.0.0.1";
        $arrayParametros['strUsrCreacion'] = "MOBILE";
        $arrayParametros['strObservacionSolicitud'] = "Contacto: " .  $strNombreContacto . " Teléfono Contacto: " . $strNumeroTelefono;
        $arrayParametros['strNumeroTelefonico'] = $arrayData['numeroTelefonico'];
        $arrayParametros['arrayFechaSms'] = $arrayFechaSms;
        $arrayParametros['strEmail'] = $arrayData['eMail'];
      
        //valido si hay cupo disponible
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $entityDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                     ->findOneById($arrayParametros['intIdFactibilidad']);
        $strFechaPar = substr($arrayFechaInicio, 0, -1);
        $strFechaPar .= "1";
        $strFechaPar = str_replace("-", "/", $strFechaPar);
        
        $strFechaPar2 = str_replace("-", "/", $arrayFechaInicio);
        

        $intJurisdicionId = $entityDetalleSolicitud->getServicioId()
                        ->getPuntoId()
                        ->getPuntoCoberturaId()->getId();

        $intHoraCierre = $this->container->getParameter('planificacion.mobile.hora_cierre');

        $arrayPar    = array(
            "strFecha" => $strFechaPar,
            "intJurisdiccion" =>  $intJurisdicionId,
            "intHoraCierre" => $intHoraCierre);
        
        $arrayCount  = $emComercial
            ->getRepository('schemaBundle:InfoCupoPlanificacion')
            ->getCountOcupados($arrayPar);
        $intCuantos = $arrayCount['CUANTOS'];        
        
        $arrayParAgenda = array("strFechaDesde" => $strFechaPar2,
                                "intJurisdiccionId" => $intJurisdicionId);
        
        $entityAgendaDetalle  = $emComercial
             ->getRepository('schemaBundle:InfoAgendaCupoDet')
             ->getDetalleAgenda($arrayParAgenda);        

        $intCupoMobile = 0;
        foreach ($entityAgendaDetalle['registros'] as $entityDet)
        {
          $intCupoMobile += $entityDet->getCuposMovil();
        }
        if ($intCuantos >= $intCupoMobile)
        {
            $objServicePlanificacion = $this->get('planificacion.Planificar');
            $arrayPlanificacion = $objServicePlanificacion->getCuposMobil(array("intJurisdiccionId" => $intJurisdicionId));
            $arrayResultado = array();
            $arrayResultado['codigoRespuesta'] = 1; 
            $arrayResultado['listProgramacionInstalacion'] = $arrayPlanificacion;    
            $arrayResultado['mensaje'] = "No hay cupo disponible para este horario, seleccione otro horario por favor!";
           //codigoRespuesta 
            return $arrayResultado;           
        }        
     
        $objServicePlanificacion = $this->get('planificacion.Planificar');
        $arrayResultado = $objServicePlanificacion->coordinarPlanificacion($arrayParametros);

        return $arrayResultado;
    }
}

