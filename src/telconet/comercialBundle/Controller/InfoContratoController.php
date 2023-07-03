<?php

namespace telconet\comercialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoAdendum;
use telconet\schemaBundle\Entity\InfoContrato;
use telconet\schemaBundle\Entity\AdmiNumeracion;
use telconet\schemaBundle\Entity\InfoContratoDatoAdicional;
use telconet\schemaBundle\Entity\InfoContratoClausula;
use telconet\schemaBundle\Entity\InfoContratoFormaPago;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Form\InfoContratoType;
use telconet\schemaBundle\Form\InfoContratoDatoAdicionalType;
use telconet\schemaBundle\Form\InfoContratoFormaPagoType;
use telconet\schemaBundle\Form\InfoContratoFormaPagoEditType;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
//
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\schemaBundle\Entity\AdmiTipoSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoOrdenTrabajo;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioTecnico;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\AdmiMotivo;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Entity\AdmiParametroCab;
//
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Form\ProcesoAprobarContratoType;

use telconet\comercialBundle\Service\InfoContratoAprobService;
use telconet\comercialBundle\Service\InfoPreClienteService;
use telconet\seguridadBundle\Service\TokenCasService;
use telconet\schemaBundle\Entity\InfoPersonaEmpFormaPago;
use telconet\schemaBundle\Form\InfoPersonaEmpFormaPagoEditType;
use telconet\schemaBundle\Entity\AdmiFormaPago;
use telconet\schemaBundle\Form\InfoDocumentoType;
use Doctrine\Common\Collections\ArrayCollection;
use telconet\schemaBundle\Entity\InfoPuntoContacto;
use telconet\schemaBundle\Entity\InfoContratoCaracteristica;
use telconet\schemaBundle\Entity\InfoContratoFormaPagoHist;
use telconet\schemaBundle\Entity\InfoContratoFormaPagoLog;
use telconet\financieroBundle\Service\InfoDocumentoFinancieroCabService;
use telconet\comercialBundle\Service\ComercialService;


/**
 * InfoContrato controller.
 *
 */
class InfoContratoController extends Controller implements TokenAuthenticatedController
{

    const NOMBRE_PROCESO = 'LinkDatosBancario';
    const NOMBRE_DOCUMENTO = 'Contrato de adhesión';



    /**
     * Lists all InfoContrato entities.
     *
     * @author : Néstor Naula <nnaulal@telconet.ec>
     * @version 1.1 18-10-2020
     * Se valida el tipo de pantalla adendum o contrato
     */
    public function indexAction()
    {
		$request = $this->getRequest();	
		$session=$request->getSession();
		$idEmpresa=$session->get('idEmpresa');
        $cliente=$session->get('cliente');

        $strTipoContratoAdendum = $request->get('tipo');
        $strNombrePantalla      = "Contrato";

        if(!empty($strTipoContratoAdendum) && $strTipoContratoAdendum == 'adendum')
        {
            $strNombrePantalla = 'Adendum';
        }

        if(!empty($strTipoContratoAdendum) && $strTipoContratoAdendum == 'contratoCRS')
        {
            $strNombrePantalla = 'Contrato-CRS';
        }

		//print_r($cliente); die;
		$em= $this->getDoctrine()->getManager();
                $strTieActPend='N';
		if($cliente['id']){
			$contrato=$em->getRepository('schemaBundle:InfoContrato')
			->findContratosPorEmpresaPorPersonaEmpresaRol($idEmpresa,$cliente['id_persona_empresa_rol']);
                        //echo count($contrato);die;
            if($contrato && empty($strTipoContratoAdendum))
            {
                          
                            foreach($contrato as $con){
                                if ($con->getEstado()=="Activo" || $con->getEstado()=="Pendiente" || $con->getEstado()=="PorAutorizar")
                                {
                                    $strTieActPend='S';
                                }     
                            }    
                            return $this->render('comercialBundle:infocontrato:index.html.twig',
                                                                                                 array('idper' => $cliente['id_persona_empresa_rol']
                                                                                                       ,'tieneActivoPendiente'=>$strTieActPend
                                                                                                       ,'idContrato' => $contrato[0]->getId()));

			}
			else{
                return $this->redirect($this->generateUrl('infocontrato_new', array('idper' => $cliente['id_persona_empresa_rol']
                                                                                   ,'nombrePantalla' => $strNombrePantalla)));
			}
			
		}else{	
			return $this->render('comercialBundle:infocontrato:index.html.twig', array('idper' => $cliente['id_persona_empresa_rol'],
                                                                                        'tieneActivoPendiente'=>$strTieActPend
                                                                                        ,'idContrato' => 0));
		}
    }
    
    /**
     * Documentación para el método 'showDocumentosEntregablesAction'.
     *
     * Retorna listado de documentos entregables que hayan sido entregado o no por el cliente.
     *
     * @return Response Lista de documentos entregables
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 24-03-2016
     */
    public function showDocumentosEntregablesAction($intIdContrato, $strFormaPago)
    {
        $strEmpresaCod      = $this->getRequest()->getSession()->get('idEmpresa');
        $strJsonEntregables = $this->getDoctrine()->getManager('telconet')
                                                  ->getRepository('schemaBundle:InfoContratoCaracteristica')
                                                  ->getJsonResultadoEntregablesContrato($intIdContrato, $strEmpresaCod, $strFormaPago);
        $objResponse = new JsonResponse();       
        $objResponse->setContent($strJsonEntregables);
        return $objResponse;
    }

    /**
     * Documentación para el método 'guardarDocumentoEntregableAction'.
     *
     * Método público que recibe los documentos entregables seleccionados desde la vista para guardarlos.
     *
     * @return Response Respuesta de ejecución del guardado de los entregables.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 24-03-2016
     */
    public function guardarDocumentoEntregableAction() 
    {
        $objRequest         = $this->getRequest();
        $objSesion          = $objRequest->getSession();
        $strUsrCreacion     = $objSesion->get('user');
        $strJsonEntregables = $objRequest->get('jsonEntregables');
        
        /* @var $service InfoContratoAprobService */
        $objContratoAprobService = $this->get('comercial.InfoContratoAprob');
          
        $objRespuesta = new JsonResponse();
        $objRespuesta->setContent(json_encode($objContratoAprobService->guardarDocumentoEntregable($strJsonEntregables, $strUsrCreacion)));
        return $objRespuesta;
    }

     /**
    * Funcion para mostrar la informacion de un contrato existente como forma de pago, numero de tarjeta cuenta, 
    * anio y mes de vencimiento, titular de cuenta, se realiza descencriptado del numero de cta y tarjeta.
    * @author : telcos
    * @author : apenaherrera
    * @version 1.0 19-06-2014      
    * @version 1.1 02-12-2014 
    * 
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.3 01-04-2020 Se agrega enmascaramiento de número tarjeta-cuenta mediante lectura de parámetro.
    * 
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.4 13-01-2023 - Se agrega funcionalidad para validar perfil e insertar log asociado.
    * 
    * @see \telconet\schemaBundle\Entity\InfoContrato
    * @return Renders a view.
    */	
    public function showAction($id)
    {   
        try
        {
            $em               = $this->getDoctrine()->getManager();
            $objRequest       = $this->getRequest();
            $objSession       = $objRequest->getSession();
            $arrayCliente     = $objSession->get('cliente');
            $arrayPtoCliente  = $objSession->get('ptoCliente');
            $strPrefijoEmpresa= $objSession->get('prefijoEmpresa');
            $strRol           = $arrayCliente['nombre_rol'];
            $entity           = $em->getRepository('schemaBundle:InfoContrato')->find($id);
            $clausulas        = $em->getRepository('schemaBundle:InfoContratoClausula')->findByContratoId($id);
            $datosAdicionales = $em->getRepository('schemaBundle:InfoContratoDatoAdicional')->findOneByContratoId($id);

            $strCodEmpresa      = $objSession->get('idEmpresa');
            $emComercial        = $this->getDoctrine()->getManager('telconet');
            $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
            $strUsrCreacion     = $objSession->get('user');
            $strIpCreacion      = $objRequest->getClientIp();
            $serviceInfoLog     = $this->get('comercial.InfoLog');
            $serviceTokenCas    = $this->get('seguridad.TokenCas'); 
            $arrayDatosCliente  = array();
            if( !$entity )
            {
                throw $this->createNotFoundException('Unable to find InfoContrato entity.');
            }

            if($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN')
            {
                $objInfoPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                               ->findOneById($entity->getPersonaEmpresaRolId());
                                            
                $intRolId    = $objInfoPersonaEmpresaRol->getEmpresaRolId()->getId();

                $objInfoEmpresaRol = $em->getRepository('schemaBundle:InfoEmpresaRol')
                                        ->findOneById($intRolId);

                if($objInfoEmpresaRol->getRolId() == 1)
                {
                    $strRol    = 'Cliente';
                }
                else
                {
                    $strRol    = 'Pre-cliente';
                }
            }
            if($strPrefijoEmpresa == 'MD')
            { 
                if(!empty($arrayCliente))
                {
                     $objInfoPersona  = $emComercial->getRepository('schemaBundle:InfoPersona')->findOneById($arrayCliente['id']);

                     if(is_object($objInfoPersona))
                     {
                         $arrayDatosCliente['nombres']            = $objInfoPersona->getNombres();
                         $arrayDatosCliente['apellidos']          = $objInfoPersona->getApellidos();
                         $arrayDatosCliente['razon_social']       = $objInfoPersona->getRazonSocial();
                         $arrayDatosCliente['identificacion']     = $objInfoPersona->getIdentificacionCliente();
                         $arrayDatosCliente['tipoTributario']     = $objInfoPersona->getTipoTributario();
                         $arrayDatosCliente['tipoIdentificacion'] = $objInfoPersona->getTipoIdentificacion();
                         $arrayDatosCliente['login']              = $arrayPtoCliente['login'];
                     }                 
                } 
                $strOrigen        = '';
                $strMetodo        = '';
                $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array('nombreParametro' => 'VISUALIZACION LOGS', 
                                                                  'estado'          => 'Activo'));
                if(is_object($objAdmiParametroCab))
                {              
                    $objParamDetOrigen = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                     ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                       'descripcion' => 'ORIGEN',
                                                                       'empresaCod'  => $strCodEmpresa,
                                                                       'estado'      => 'Activo'));

                    $objParamDetMetodo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                     ->findOneBy(array('parametroId'     => $objAdmiParametroCab,
                                                                       'observacion'     => 'VER CONTRATO',
                                                                       'empresaCod'      => $strCodEmpresa,
                                                                       'estado'          => 'Activo'));           
                    if(is_object($objParamDetOrigen))
                    {
                        $strOrigen  = $objParamDetOrigen->getValor1();
                    }

                    if(is_object($objParamDetMetodo))
                    {
                        $strMetodo  = $objParamDetMetodo->getValor1();
                    }             
                }
                $arrayParametrosLog                   = array();
                $arrayParametrosLog['strOrigen']      = $strOrigen;
                $arrayParametrosLog['strMetodo']      = $strMetodo;
                $arrayParametrosLog['strTipoEvento']  = 'INFO';
                $arrayParametrosLog['strIpUltMod']    = $strIpCreacion;
                $arrayParametrosLog['strUsrUltMod']   = $strUsrCreacion;
                $arrayParametrosLog['dateFechaEvento']= date("Y-m-d h:i:s");
                $arrayParametrosLog['strIdKafka']     = '';
                $arrayParametrosLog['request']        = $arrayDatosCliente;


                $arrayTokenCas                 = $serviceTokenCas->generarTokenCas();
                $arrayParametrosLog['token']   = $arrayTokenCas['strToken'];
                $serviceInfoLog->registrarLogsMs($arrayParametrosLog);
            }
            $deleteForm = $this->createDeleteForm($id);

            $parametros = array(
                'entity'             => $entity,
                'delete_form'        => $deleteForm->createView(),
                'clausulas'          => '',
                'datosAdicionales'   => '',
                'formFormaPago'      => '',
                'strNumeroCtaTarjeta'=> '',
                'descripcion_motivo' => '',
                'strNumCtaTarjDesenc'=> '',
                'prefijoEmpresa'     => $strPrefijoEmpresa,
            );

            if($clausulas)
            {
                $parametros['clausulas'] = $clausulas;
            }
            if($datosAdicionales)
            {
                $parametros['datosAdicionales'] = $datosAdicionales;
            }
            $parametros['rol'] = $strRol;
            //para los datos del forma de pagos que no son efectivo
            if( $entity->getFormaPagoId()->getDescripcionFormaPago() != "Efectivo" )
            {
                //Busco por id y por estado -- falta por estado
                $estado        = "Activo";
                $formFormaPago = $em->getRepository('schemaBundle:InfoContratoFormaPago')->findPorContratoIdYEstado($id, $estado);
                if( $formFormaPago )
                {
                    $parametros['formFormaPago'] = $formFormaPago;

                    //Descencripto el campo Numero_Cta_Tarjeta
                    /* @var $serviceCrypt \telconet\seguridadBundle\Service\CryptService */
                    $serviceCrypt = $this->get('seguridad.Crypt');
                    $strNumCtaTarjDesenc = $serviceCrypt->descencriptar($formFormaPago->getNumeroCtaTarjeta());
                    $serviceInfoContrato = $this->get('comercial.InfoContrato');
        
                    $arrayParametros     = array(
                                                 'strCodEmpresa'        => $strCodEmpresa,
                                                 'strNumeroCtaTarjeta'  => $strNumCtaTarjDesenc,
                                                );       

                    $strNumeroCtaTarjeta  = $serviceInfoContrato->getNumeroTarjetaCtaEnmascarado($arrayParametros); 

                    if( $strNumeroCtaTarjeta )
                    {
                        $parametros['strNumeroCtaTarjeta'] = $strNumeroCtaTarjeta;
                        $parametros['strNumCtaTarjDesenc'] = $strNumCtaTarjDesenc;
                    }
                    else
                    {
                        throw new \Exception('No fue posible mostrar el Numero de Cuenta / Tarjeta - Contactese con el administrador del sistema.');                                       
                    }
                }
            }
            $descripcion_motivo = "";
            if($entity->getEstado() == 'Rechazado')
            {
                $entityMotivo       = $em->getRepository('schemaBundle:AdmiMotivo')->find($entity->getMotivoRechazoId());
                $descripcion_motivo = $entityMotivo->getNombreMotivo();
            }
            $parametros['descripcion_motivo'] = $descripcion_motivo;
            $parametros['grantedVerDocumentoPersonal'] = $this->get('security.context')->isGranted('ROLE_60-8057');
            $parametros['grantedDescargarDocumentoPersonal'] = $this->get('security.context')->isGranted('ROLE_60-8058'); 
            $parametros['strNombrePantalla']  ="Ademdun";         

            $serviceSeguridad     =  $this->get('seguridad.Seguridad');     
            $parametros['grantedAuditorSenior'] =  $serviceSeguridad->isAccesoLoginPerfil($objSession, 'Md_Auditor_Senior'); 
            return $this->render('comercialBundle:infocontrato:show.html.twig', $parametros);            
        }
        catch (\Exception $e)
        {   
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());            
            return $this->render('comercialBundle:infocontrato:show.html.twig', $parametros);            
        }
    }

    /**
    * Funcion para el ingreso de Nuevo Contrato 
    * @author : telcos
    * @version 1.0 19-06-2014  
    * @see \telconet\schemaBundle\Entity\InfoContrato
    * @return Renders a view.
    * 
    * @author : Edgar Holguin <eholguin@telconet.ec>
    * @version 1.1 16-05-2016 Se agrega validacion que verifica contactos ingresados solo para TN      
    * @author : Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.2 20-06-2016 Se aumenta Validacion para el ingreso de Contrato aplicable solo para TN
    * debe existir al menos un contacto Comercial, Facturacion, Cobranzas a nivel de Cliente o a nivel de Punto
    * para poder ingresar el contrato.
    *
    * @author : Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.3 01-07-2016 Se corrige Validaciones para poder generar Contrato.
    * Se controla para permitir evaluar si Cliente posee Contactos de Tipo: Comercial, Cobranzas y Facturacion 
    * registrados a Nivel de Cliente o registrados entre todos sus Logines.
    * 
    * @author : Andrés Montero <amontero@telconet.ec>
    * @version 1.4 06-07-2017
    * Se envia id de pais de la empresa en sesión por parametros al crear formulario de formas de pago con InfoContratoFormaPagoType 
    *
    * @author : Gustavo Narea <gnarea@telconet.ec>
    * @version 1.5 18-09-2020
    * Se parametriza el rango de la fecha de vencimiento de la tarjeta
    *
    * @author : Néstor Naula <nnaulal@telconet.ec>
    * @version 1.6 18-10-2020
    * Se agrega los parámetros obligatorios de imagenes a ingresar para MD
    *
    * @author : Néstor Naula <nnaulal@telconet.ec>
    * @version 1.7 22-06-2022
    * Se corrige los problemas de los número teléfonicos del cliente
    *
    * @author : Walther Joao Gaibor C <wgaibor@telconet.ec>
    * @version 1.8 01-07-2021- Se corrige los problemas al momento de realizar un cambio de razón social.
    *
    * @author : Néstor Naula <nnaulal@telconet.ec>
    * @version 1.9 11-07-2021- Se valida que para el adendum se tenga el estado Pendinte o PorAutorizar
    *                          para procesar.
    *
    * @author : Walther Joao Gaibor C <wgaibor@telconet.ec>
    * @version 1.10 11-09-2021- Se habilita opción para realizar contratos en CRS.
    *
    * @author : Wilson Quinto <wquinto@telconet.ec>
    * @version 1.11 25-02-2022- Se actualiza obtencion de documento requeridos.
    *
    * @author : Walther Joao Gaibor C <wgaibor@telconet.ec>
    * @version 1.12 11-05-2022- Se requiere validar si existe clausulas por responder del usuario.
    *
    * @author Alex Gomez <algomez@telconet.ec>
    * @version 1.13 10-08-2022 Se modifica estado de los puntos y servicios clonados por CRS tradicional y por punto 
    *                          cuando el contrato aun no ha sido autorizado. Aplica para MD y contrato digital.
    *
    * @author Joel Broncano <jbroncano@telconet.ec>
    * @version 1.14 20-04-2023 Soporte EN
    *                           
    *
    */
    public function newAction($idper)
    {
        $request        = $this->getRequest();
        $entityContrato = new InfoContrato();
        $cliente        = $request->getSession()->get('cliente');
        $idEmpresa      = $request->getSession()->get('idEmpresa');
        $prefijoEmpresa = $request->getSession()->get('prefijoEmpresa');
        $intIdPais      = $request->getSession()->get('intIdPais');
        $strUsrCreacion = $request->getSession()->get('user');
        $strClientIp    = $request->getClientIp();
        $strNombrePant  = $request->get('nombrePantalla');
        $strCambioRazonSocial = "N";
        $strNombreProc     = explode("-", $strNombrePant);
        $strNombrePantalla = $strNombreProc[0];
        $arrayPtoCliente= $request->getSession()->get('ptoCliente');
        $serviceInfoContrato = $this->get('comercial.InfoContrato');
        $serviceTokenCas = $this->get('seguridad.TokenCas');
        $servicePreCliente         = $this->get('comercial.PreCliente');
        $arrayCreacionPunto        = array();

        $arrayPuntosCRS    = array();
        $intIdContratoAut  = "";
        $intIdAdendumAut   = "";
        
        $nombreCliente = null;
        $idCliente     = null;
        $tipoRol       = null;
        $estadoCliente = null;
        $em            = $this->getDoctrine()->getManager();		
        $emGeneral     = $this->getDoctrine()->getManager('telconet_general');
        
        $strEstadoServicioPreactivo   = '';

        $arrayTelefonosClienteMs = array();
        $arrayParamContac                           = array();                 
        $arrayParamContac['strEstado']              = "Activo";
        $arrayParamContac['strDescFormaContacto']   = array(
                                                    "Telefono Movil",
                                                    "Telefono Movil Claro",
                                                    "Telefono Movil CNT",
                                                    "Telefono Movil Digicel",
                                                    "Telefono Movil Movistar",
                                                    "Telefono Movil Referencia IPCC",
                                                    "Telefono Movil Tuenti");

        $arrayPuntoCliente         = $request->getSession()->get('ptoCliente');
        $intIdPuntoSession         = 0;
        if(empty($strNombrePantalla))
        {
            $strNombrePantalla         = "Contrato";
        }
        if(!empty($arrayPuntoCliente))
        {
            $intIdPuntoSession         = $arrayPuntoCliente['id'];
        }


        if(!empty($strNombreProc[1]))
        {
          $strCambioRazonSocial = "S";
        }

        //Consulta nuevo estado para servicios creados por Cambio de Razón Social por Login
        //previo a la autorizacion del contrato. Aplica MD
        if($prefijoEmpresa === 'MD' || $prefijoEmpresa === 'EN' )
        {
            $arrayEstadosServicios = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne(
                                                    'ESTADOS_CAMBIO_RAZON_SOCIALXPUNTO',
                                                    'COMERCIAL',
                                                    'CAMBIO_RAZON_SOCIAL_POR_PUNTO',
                                                    '','','','','','',
                                                    $idEmpresa);
            
            if(isset($arrayEstadosServicios) && !empty($arrayEstadosServicios))
            {
                $strEstadoServicioPreactivo = $arrayEstadosServicios["valor1"];
            }
        }


        $boolContactosRequeridos = false;
        
        if($cliente)
        {
            $idCliente     = $cliente['id'];
            $tipoRol       = $cliente['nombre_tipo_rol'];
            $estadoCliente = $cliente['estado'];
            if( $cliente['razon_social'] )
                $nombreCliente = $cliente['razon_social'];
            else 
                $nombreCliente = $cliente['nombres'].' '.$cliente['apellidos'];
	    //Obtiene el historial del prospecto(pre-cliente)			
	    //$personaEmpresaRol=$em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getPersonaEmpresaRolPorPersonaPorTipoRol($idCliente,$tipoRol,$idEmpresa);
        $objPersona              = $em->getRepository('schemaBundle:InfoPersona')->findOneById($idCliente);
        $personaEmpresaRol       = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idper);
	    $historial               = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->findHistorialPorPersonaEmpresaRol($personaEmpresaRol->getId());
	    $ultimoEstado            = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->findUltimoEstadoPorPersonaEmpresaRol($personaEmpresaRol->getId());        
	    $idUltimoEstado          = $ultimoEstado[0]['ultimo'];
	    $entityUltimoEstado      = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->find($idUltimoEstado);
	    $estadoCliente           = $entityUltimoEstado->getEstado();	
	    $tieneServiciosFactibles = false;
        $strTieneServiciosTN     = false;
        $arrayListaDocumentoSubir= array(); 
        
        $arrayAdmiParametroCabAnio  = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                            ->findOneBy(array("nombreParametro" => "ANIO_VIGENCIA_TARJETA",
                                                                              "estado"          => "Activo"));
        if(is_object($arrayAdmiParametroCabAnio))
        {
            $arrayParamDetAnios = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findBy(array("parametroId" => $arrayAdmiParametroCabAnio,
                                                            "estado"      => "Activo"));
            if ($arrayParamDetAnios)
            {
                $intAnioVencimiento = $arrayParamDetAnios[0]->getValor1();
            }
        }

          //Se Verifica si posee contacto Comercial, Facturacion, Cobranzas a nivel de Cliente.
                 
        $intCantidadContactosCom = $em->getRepository('schemaBundle:InfoPersonaContacto')
                                      ->getCantidadPorTipoContactoPorCliente($idper,"Contacto Comercial");
      
        $intCantidadContactosFact= $em->getRepository('schemaBundle:InfoPersonaContacto')
                                      ->getCantidadPorTipoContactoPorCliente($idper,"Contacto Facturacion"); 
                
        $intCantidadContactosCob = $em->getRepository('schemaBundle:InfoPersonaContacto')
                                      ->getCantidadPorTipoContactoPorCliente($idper,"Contacto Cobranzas"); 
                
        $objPuntos               = $em->getRepository('schemaBundle:InfoPunto')->findByPersonaEmpresaRolId($personaEmpresaRol->getId());	    
        foreach( $objPuntos as $punto )
        {
            $arrPunto = $em->getRepository('schemaBundle:InfoServicio')->findServiciosFactiblesPendientes($punto->getId(),0,999999);
                
            if( $arrPunto['total']>0 && $strNombrePantalla == 'Contrato')
            {
                $tieneServiciosFactibles = true;	
            }
	        
            if( $prefijoEmpresa == "TN" )
            {
                $intCantidadServiciosTN = $em->getRepository('schemaBundle:InfoServicio')
                                           ->getCantidadServiciosPorPuntoPorEstados($punto->getId());
                if($intCantidadServiciosTN>0)
                {
                    $strTieneServiciosTN = true;
                }
              
                //Se Verifica si posee contacto Comercial, Facturacion, Cobranzas a nivel de todos sus Punto. 
               
                $intCantidadContactoComercialPto = $em->getRepository('schemaBundle:InfoPuntoContacto')
                                                      ->getCantidadPorTipoContactoPorPunto($punto->getId(),"Contacto Comercial");
                if($intCantidadContactoComercialPto>0)
                { 
                    $intTotalContactoComercial = $intTotalContactoComercial + $intCantidadContactoComercialPto;
                }
                
                $intCantidadContactosFacturacionPto = $em->getRepository('schemaBundle:InfoPuntoContacto')
                                                         ->getCantidadPorTipoContactoPorPunto($punto->getId(),"Contacto Facturacion");
                if($intCantidadContactosFacturacionPto>0)
                {
                    $intTotalContactoFacturacion = $intTotalContactoFacturacion + $intCantidadContactosFacturacionPto;
                } 
                
                $intCantidadContactosCobranzasPto = $em->getRepository('schemaBundle:InfoPuntoContacto')
                                                       ->getCantidadPorTipoContactoPorPunto($punto->getId(),"Contacto Cobranzas");      
                if($intCantidadContactosCobranzasPto>0)
                {
                    $intTotalContactoCobranzas = $intTotalContactoCobranzas + $intCantidadContactosCobranzasPto;
                } 
            }

            if($prefijoEmpresa == "MD" || $prefijoEmpresa == "EN" )
            {
                $arrayServiciosPreactivo = $em->getRepository('schemaBundle:InfoServicio')
                                            ->findBy( 
                                                array(
                                                    'puntoId' => $punto->getId(),
                                                    'estado'  => array($strEstadoServicioPreactivo)
                                                    )
                                                );
                if(isset($arrayServiciosPreactivo) && count($arrayServiciosPreactivo) > 0)
                {
                    array_push($arrayPuntosCRS,$punto->getId());
                }
            }
        }

        $arrayServicios = $em->getRepository('schemaBundle:InfoServicio')
                             ->findBy( 
                                array(
                                    'puntoId' => $intIdPuntoSession,
                                    'estado'  => array(
                                        'PrePlanificada', 'Pendiente','Factible','Activo', $strEstadoServicioPreactivo
                                        )
                                    )
                                ); 
        foreach($arrayServicios as $objServicio)
        {
            if( is_object($objServicio) && ($strNombrePantalla == 'Adendum' || !empty($strNombreProc[1])))
            {
                $objAdendum = $em->getRepository('schemaBundle:InfoAdendum')
                                 ->findOneBy(array("puntoId"    => $intIdPuntoSession,
                                                   "servicioId" => $objServicio->getId(),
                                                   "estado"     => array('Pendiente','PorAutorizar')
                                                )
                                            );
                if(is_object($objAdendum))
                {
                    $tieneServiciosFactibles = true;
                }	
            }
        }

        //Si no posee los contactos requeridos sea a nivel de Cliente o a nivel de todos sus puntos no se permite generar Contrato
        if( ($intCantidadContactosCom  > 0 || $intTotalContactoComercial   > 0) && 
            ($intCantidadContactosFact > 0 || $intTotalContactoFacturacion > 0) && 
            ($intCantidadContactosCob  > 0 || $intTotalContactoCobranzas > 0)
          )
        {
            $boolContactosRequeridos  = true;
        }

        $intIdFormaPago=0;
        $intIdTipoCuenta=0;
        $arrayListaDocumentoSubir = array();
        if($prefijoEmpresa == 'MD' || $prefijoEmpresa == 'EN')
        {
            $objContrato = $em->getRepository('schemaBundle:InfoContrato')
                                        ->findOneBy(array("personaEmpresaRolId" => $idper,
                                                        "estado"              => array('Activo')));
                                                                                        
            if(!empty($strNombreProc[1]) && !is_object($objContrato))
            {
                $strCambioRazonSocial =  "S";
            }

            if(!is_null($objContrato))
            {   
                $entityContratoFormaPagoActivo = $em->getRepository('schemaBundle:InfoContratoFormaPago')
                                                ->findOneBy(array("contratoId" => $objContrato->getId(),
                                                                    "estado"     => 'Activo'));
                if(!is_null($entityContratoFormaPagoActivo))
                {
                    $intIdFormaPago=$objContrato->getFormaPagoId()?$objContrato->getFormaPagoId()->getId():0;
                    $intIdTipoCuenta=$entityContratoFormaPagoActivo->getTipoCuentaId()?$entityContratoFormaPagoActivo->getTipoCuentaId()->getId():0;
                    $intTotalCaracteres = $entityContratoFormaPagoActivo->getBancoTipoCuentaId()?
                                            $entityContratoFormaPagoActivo->getBancoTipoCuentaId()->getTotalCaracteres():0;
                }
               
            }else
            {
                $entityPersonaFormaPago = $servicePreCliente->getDatosPersonaEmpFormaPago($personaEmpresaRol->getPersonaId()->getId(),$idEmpresa);
                if(!is_null($entityPersonaFormaPago))
                {
                    $intIdFormaPago=$entityPersonaFormaPago->getFormaPagoId()?$entityPersonaFormaPago->getFormaPagoId()->getId():0;
                    $intIdTipoCuenta=$entityPersonaFormaPago->getTipoCuentaId()?$entityPersonaFormaPago->getTipoCuentaId()->getId():0;
                    $intTotalCaracteres = $entityPersonaFormaPago->getBancoTipoCuentaId()?
                                            $entityPersonaFormaPago->getBancoTipoCuentaId()->getTotalCaracteres():0;
                }
            }
                                                                    
            $arrayTokenCas = $serviceTokenCas->generarTokenCas();
            
            if(empty($arrayTokenCas['strToken']))
            {
                throw new \Exception($arrayTokenCas['strMensaje']); 
            }
            $objTipoDocumento=array(
                'token' => $arrayTokenCas['strToken'],
                'codEmpresa' => $idEmpresa,
                'prefijoEmpresa' =>  $prefijoEmpresa, 
                'usrCreacion' => $strUsrCreacion, 
                'clientIp' => $strClientIp,
                'idFormaPago'            => $intIdFormaPago,
                'idTipoCuenta'         => $intIdTipoCuenta,
                'tipoTributario'     => $objPersona->getTipoTributario()
            );
            $arrayListaDocumentoSubir=$serviceInfoContrato->verificarDocumentosRequeridosMS($objTipoDocumento);
        }
        
        }	
        $arrayTipoDocumentos = array();
        $objTiposDocumentos  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')->findByEstado("Activo");                   
        foreach ( $objTiposDocumentos as $objTiposDocumentos )
        {   
           $arrayTipoDocumentos[$objTiposDocumentos->getId()] = $objTiposDocumentos->getDescripcionTipoDocumento();
        }
        $em->getRepository('schemaBundle:InfoEmpresaGrupo')->setCurrentIdEmpresa($idEmpresa);
        $form                        = $this->createForm(new InfoContratoType(array('validaFile'=>true)), $entityContrato);
        $entityContratoDatoAdicional = new InfoContratoDatoAdicional();
        $formDatoAdicioanles         = $this->createForm(new InfoContratoDatoAdicionalType(), $entityContratoDatoAdicional);     
        $entityContratoFormaPago     = new InfoContratoFormaPago();
        $entityFormInfoPago                = $this->createForm(new InfoContratoFormaPagoType(
                                                                    array("intIdPais"=>$intIdPais, 
                                                                    "intAnioVencimiento"=>$intAnioVencimiento)), 
                                                        $entityContratoFormaPago);
        $entityFormInfoPago                = $entityFormInfoPago->createView();
        // formInfoPago
        $form = $form->createView();        
        $objFormDocumentos           = $this->createForm(new InfoDocumentoType(array('validaFile'          => true,
                                                                                     'arrayTipoDocumentos' => $arrayTipoDocumentos)),
                                                         new InfoDocumento());
        
        /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
        $serviceInfoContrato    = $this->get('comercial.InfoContrato');
        $entityAdmiTipoContrato = $serviceInfoContrato->obtenerTiposContrato($idEmpresa);

        //Generacion de la fecha
        $fecha_act = date("Y-m-d");
        $fecha     = date('Y-m-d', strtotime("+12 months $fecha_act"));
	
        if( $prefijoEmpresa == "MD" || $prefijoEmpresa == "EN")
        {
            $entityContratoAutorizado = $em->getRepository('schemaBundle:InfoContrato')
                                           ->findOneBy(array("personaEmpresaRolId" => $personaEmpresaRol->getId(),
                                                             "estado"              => 'PorAutorizar'));
            if(is_object($entityContratoAutorizado))
            {
                $intIdContratoAut  = $entityContratoAutorizado->getId();
            }

            if($strCambioRazonSocial=="S")
            {

                $entityAdendumAutorizadoCRS = $em->getRepository('schemaBundle:InfoAdendum')
                ->findOneBy(array("puntoId"     => $intIdPuntoSession,
                                  "estado"      => array("Pendiente","PorAutorizar"),
                                  "tipo"        => array("AS","AP"),
                                ));


                if(is_object($entityAdendumAutorizadoCRS))
                {
                                    
                    $intIdAdendumAut  = $entityAdendumAutorizadoCRS->getId();
                    $strNombrePantalla         = "Adendum";
                }


            }

            $entityAdendumAutorizado = $em->getRepository('schemaBundle:InfoAdendum')
            ->findOneBy(array("puntoId"     => $intIdPuntoSession,
                                "estado"      => 'PorAutorizar',
                                "formaContrato"=>'DIGITAL'));

            if(is_object($entityAdendumAutorizado))
            {
                    $intIdAdendumAut  = $entityAdendumAutorizado->getId();
            }
                                                    
            

          
            
          
             /* @var $servicePreCliente \telconet\comercialBundle\Service\PreClienteService */
            $entityPersonaEmpFormaPago = $servicePreCliente->getDatosPersonaEmpFormaPago($personaEmpresaRol->getPersonaId()->getId(),$idEmpresa);
            
            if($strNombrePantalla == "Adendum")
            {
                $entityContratoActivo = $em->getRepository('schemaBundle:InfoContrato')
                                           ->findOneBy(array("personaEmpresaRolId" => $personaEmpresaRol->getId(),
                                                             "estado"              => 'Activo'));

                if ( is_object($entityContratoActivo) )
                {   
                    $objAdmiFormaPago    = $em->getRepository('schemaBundle:AdmiFormaPago')
                                              ->find($entityContratoActivo->getFormaPagoId()->getId());

                    $entityPersonaEmpFormaPago = new InfoPersonaEmpFormaPago();
                    $entityPersonaEmpFormaPago->setFormaPagoId($objAdmiFormaPago);
                    $entityPersonaEmpFormaPago->setPersonaEmpresaRolId($personaEmpresaRol);

                    $entityContratoFormaPagoActivo = $em->getRepository('schemaBundle:InfoContratoFormaPago')
                                                        ->findOneBy(array("contratoId" => $entityContratoActivo->getId(),
                                                                          "estado"     => 'Activo'));
                
                    if ( is_object($entityContratoFormaPago) )
                    { 
                        $entityPersonaEmpFormaPago->setTipoCuentaId($entityContratoFormaPago->getTipoCuentaId());
                        $entityPersonaEmpFormaPago->setBancoTipoCuentaId($entityContratoFormaPago->getBancoTipoCuentaId());
                    }
                }

            }
            
            if( $entityPersonaEmpFormaPago )
            {
                $objFormFormaPago = $this->createForm(new InfoPersonaEmpFormaPagoEditType(array("intIdPais"=>$intIdPais)),
                                                                                          $entityPersonaEmpFormaPago);
                $objFormFormaPago = $objFormFormaPago->createView();
            
            }
            else
            {            
                $objFormFormaPago = new InfoPersonaEmpFormaPago();
                $objFormFormaPago = $this->createForm(new InfoPersonaEmpFormaPagoEditType(array("intIdPais"=>$intIdPais)), $objFormFormaPago);
                $objFormFormaPago = $objFormFormaPago->createView();           
            }           
        }
        else
        {
            $objFormFormaPago = new InfoPersonaEmpFormaPago();
            $objFormFormaPago = $this->createForm(new InfoPersonaEmpFormaPagoEditType(array("intIdPais"=>$intIdPais)), $objFormFormaPago);
            $objFormFormaPago = $objFormFormaPago->createView();           
        }

        if( $prefijoEmpresa == "MD"  || $prefijoEmpresa == "EN")
        {
            foreach( $objPuntos as $objPunto )
            {
                $intIdPunto = $objPunto->getId();
                if(!empty($intIdPunto))
                {
                    $arrayParamContac['intIdPunto']             = $intIdPunto;
                    
                    $arrayTelefonos = $em->getRepository('schemaBundle:InfoPersonaContacto')
                                                ->getEmailComercialCD($arrayParamContac); 

                    $entityPuntoDatoAdicional = $em->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                                   ->findOneByPuntoId($intIdPuntoSession);

                    if(!empty($arrayTelefonos))
                    {
                        foreach($arrayTelefonos as $strTelefonos)
                        {
                            array_push($arrayTelefonosClienteMs,$strTelefonos['strFormaContacto']);
                        } 
                    }                            
                    if(empty($arrayTelefonos) && is_object($entityPuntoDatoAdicional))
                    {
                        $strContactoTelefono = $entityPuntoDatoAdicional->getTelefonoEnvio();
                        if(!empty($strContactoTelefono))
                        {
                            $arrayTelefonosCliente = preg_split('#(?<!\\\)\;#', $strContactoTelefono);
                            foreach($arrayTelefonosCliente as $strTelefonos)
                            {
                                array_push($arrayTelefonosClienteMs,$strTelefonos);
                            } 
                        }
                    }
                    $arrayParamCorreo = array('intIdPunto'              => $intIdPunto,
                                              'strEstado'               => 'Activo',
                                              'strDescFormaContacto'    => array('Correo Electronico'));

                    $arrayCorreos     = $em->getRepository('schemaBundle:InfoPersonaContacto')
                                    ->getEmailClientePorPunto($arrayParamCorreo);

                    if(!empty($arrayCorreos))
                    {
                        foreach($arrayCorreos as $strCorreos)
                        {
                            $arrayCorreoClienteMs[] = $strCorreos['strFormaContacto'];
                        }
                    }
                }

            }

            if(!empty($idper))
            {
                $arrayParamContac['intIdPersonaEmpresaRol'] = $personaEmpresaRol->getId();
                $arrayResultadosContact     = $em
                                                    ->getRepository('schemaBundle:InfoPersonaContacto')
                                                    ->getEmailCliente($arrayParamContac);

                if(isset($arrayResultadosContact) && !empty($arrayResultadosContact))
                {
                    array_push($arrayTelefonosClienteMs,$arrayResultadosContact[0]['strFormaContacto']);
                }
                $arrayParamCorreo = array('intIdPersonaEmpresaRol'  => $personaEmpresaRol->getId(),
                                          'strEstado'               => 'Activo',
                                          'strDescFormaContacto'    => array('Correo Electronico'));
                $arrayCorreos     = $em->getRepository('schemaBundle:InfoPersonaContacto')
                                       ->getEmailCliente($arrayParamCorreo);
                if(isset($arrayCorreos) && !empty($arrayCorreos))
                {
                    foreach($arrayCorreos as $arrayCorreo)
                    {                      
                        $arrayCorreoClienteMs[] = $arrayCorreo['strFormaContacto'];
                    }
                    
                }

            }
            if( $prefijoEmpresa == "MD"  || $prefijoEmpresa == "EN")
            {
                $arrayParametros    = array(
                    'puntoId'               => $intIdPuntoSession,
                    'usrCreacion'           => $strUsrCreacion,
                    'personaEmpresaRolId'   => $personaEmpresaRol->getId(),
                    'empresaCod'            => $idEmpresa
                    );
                /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
                $serviceInfoPunto   = $this->get('comercial.InfoPunto');
                $arrayRespuesta     = $serviceInfoPunto->getDataLinksContratoCliente($arrayParametros);
                if($arrayRespuesta['status'] == 'OK')
                {
                    $arrayCreacionPunto = $arrayRespuesta['data'];
                }
            }

 
        }
        $arrayCorreoClienteMs = array_unique($arrayCorreoClienteMs, SORT_REGULAR);

        return $this->render('comercialBundle:infocontrato:new.html.twig', 
                                array(
                                        'entity'                  => $entityContrato,
                                        'entityAdmiTipoContrato'  => $entityAdmiTipoContrato,
                                        'form'                    => $form,
                                        'formDatoAdicioanles'     => $formDatoAdicioanles->createView(),
                                        'formInfoPago'            => $entityFormInfoPago,
                                        'idClienteSesion'         => $idCliente,
                                        'nombreClienteSesion'     => $nombreCliente,
                                        'tipoRolClienteSesion'    => $tipoRol,
                                        'estadoCliente'           => $estadoCliente,
                                        'fecha'                   => $fecha,
                                        'tieneServiciosFactibles' => $tieneServiciosFactibles,
                                        'strTieneServiciosTN'     => $strTieneServiciosTN,
                                        'idper'                   => $idper,
                                        'formFormaPago'           => $objFormFormaPago,
                                        'form_documentos'         => $objFormDocumentos->createView(),
                                        'arrayTipoDocumentos'     => $arrayTipoDocumentos,
                                        'boolContactosRequeridos' => $boolContactosRequeridos,
                                        'arrayListaDocumentoSubir'=> $arrayListaDocumentoSubir,
                                        'nombrePantalla'          => $strNombrePantalla,
                                        'entityContrato'          => $intIdContratoAut,
                                        'entityAdendum'           => $intIdAdendumAut,
                                        'identificacion'          => $objPersona->getIdentificacionCliente(),
                                        'arrayTelefonosCliente'   => $arrayTelefonosClienteMs,
                                        'arrayCorreoCliente'      => $arrayCorreoClienteMs,
                                        'idPuntoSession'          => $arrayPtoCliente['id'],
                                        'cambioRazonSocial'       => $strCambioRazonSocial,
                                        'creacionPunto'           => $arrayCreacionPunto,
                                        'totalCaracteres'         => $intTotalCaracteres ? $intTotalCaracteres : 0,
                                        'arrayPuntosCRS'          => $arrayPuntosCRS
                                    )); 

    }
    
    /**
    * Funcion que Guarda Contrato 
    * @author : telcos
    * @param request $request         
    * @version 1.0 23-06-2014          
    * @return a RedirectResponse to the given URL.
    * 
    * 
    * Al crear el contrado debe indicarse el origen de su creacion
    * WEB o Movil. Dado que este metodo es utilizado solo por via WEB
    * entonces se configura 'WEB' como origen del contrato
    * @author Veronica Carrasco <vcarrasco@telconet.ec>
    * @version 1.1 30-08-2016
    * 
    * @author Modificado: Veronica Carrasco <vcarrasco@telconet.ec>
    * @version 1.2 04-09-2016 - Se usa arreglo de parametros $arrayParametrosContrato para funcion $serviceInfoContrato->crearContrato
    * 
    * @author Modificado: Néstor Naula <nnaulal@telconet.ec>
    * @version 1.3 04-11-2020 - Se cambia la de forma de retornar la respuesta por ajax
    * @since 1.2
    *
    * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
    * @version 1.4 - 01-07-2021 - Se controla conecciones de base de datos en la excepción.
    *
    * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
    * @version 1.5 - 11-05-2022 - Se realiza cambios debido a la implementación de Datos Bancario.
    * @author Carlos Caguana <ccaguana@telconet.ec>
    * @version 1.6 17-02-2023 - Se realiza validaciones para el contrato fisico de TN y MD
    *
    *
    * @author Joel Broncano <jbroncano@telconet.ec>
    * @version 1.7 20-04-2023 Soporte EN
    */
    public function createAction(Request $request)
    {
        $check            = $request->get('check');
        $clausula         = $request->get('clausula');
        $clientIp         = $request->getClientIp();
        $session          = $request->getSession();
        $usrCreacion      = $session->get('user');
        $codEmpresa       = $session->get('idEmpresa');
        $prefijoEmpresa   = $session->get('prefijoEmpresa');
        $idOficina        = $session->get('idOficina');                      
        $objDatosFormFiles = $request->files->get('infodocumentotype');               
        $objDatosFormTipos = $request->get('infodocumentotype');
        $intKey=key($objDatosFormTipos);
        $arrayPromMens    = $request->get('codigoMens');
        $arrayPromIns     = $request->get('codigoIns');
        $arrayPromBw      = $request->get('codigoBw');
        $strCodigoCheckMix= $request->get('codigoCheckMix');  
        $strCodigoServMix = $request->get('codigoServMix');
        $intIdTipoPromoMix= $request->get('idTipoPromoMix');
        $strCodigoMix     = $request->get('codigoMixVal');
        $arrayServiciosMix= '';
        $emComercial      = $this->getDoctrine()->getManager("telconet");
        $objPtoCliente    = $session->get('ptoCliente');
        $intIdPunto       = $objPtoCliente['id'];
        $strValorRespuest = '';

        $strCambioRazonSocial   = $request->get('cambioRazonSocial');
        $intIdPersonaEmprRolCRS = $request->get('personaEmpresaRolId');
        $strCambioRazonSocial   = $strCambioRazonSocial != null ? $strCambioRazonSocial : 'N';
        $strClausulaGuardada    = ($request->get('clausulaGuardada')) ? $request->get('clausulaGuardada') : 'N';
        $arrayTipoDocumentos = array ();        
        foreach ($objDatosFormTipos as $intKey => $tipos)
        {                           
            foreach ( $tipos as $key_tipo => $value)
            {                     
                $arrayTipoDocumentos[$key_tipo] = $value;                
            }
        }    
        
        if($strCambioRazonSocial == 'S')
        {
            $objPersonaEmpresaRol   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                  ->find($intIdPersonaEmprRolCRS);
        }

        $objPersona    = $emComercial->getRepository("schemaBundle:InfoPersona")
                                     ->find($objPtoCliente['id_persona']);

        $strIdentificacion =  $objPersona->getIdentificacionCliente();
       
        $datos_form = array_merge(
                $request->get('infocontratodatoadicionaltype'),
                $request->get('infocontratoformapagotype'),
                $request->get('infocontratotype'),
                $strCambioRazonSocial != 'S' ? $request->get('infocontratoextratype'):
                array('personaEmpresaRolId' => $intIdPersonaEmprRolCRS,
                      'idcliente'           => $objPersonaEmpresaRol->getPersonaId()->getId(),
                      'tipoContratoId'      => $request->get('infocontratoextratype')['tipoContratoId']
                     ),
                array('feFinContratoPost'   => $request->get('feFinContratoPost')),                              
                array('datos_form_files'    => $objDatosFormFiles),
                array('arrayTipoDocumentos' => $arrayTipoDocumentos),
                array('strCambioRazonSocial' => $strCambioRazonSocial)
        );
        
        try
        {
           
            /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
            $serviceInfoContrato = $this->get('comercial.InfoContrato');
            $arrayParametrosContrato                   = array();
            $arrayParametrosContrato['codEmpresa']     = $codEmpresa;
            $arrayParametrosContrato['prefijoEmpresa'] = $prefijoEmpresa; 
            $arrayParametrosContrato['idOficina']      = $idOficina; 
            $arrayParametrosContrato['usrCreacion']    = $usrCreacion; 
            $arrayParametrosContrato['clientIp']       = $clientIp; 
            $arrayParametrosContrato['datos_form']     = $datos_form; 
            $arrayParametrosContrato['check']          = $check; 
            $arrayParametrosContrato['clausula']       = $clausula;
            $arrayParametrosContrato['origen']         = 'WEB';
            $entity = $serviceInfoContrato->crearContrato($arrayParametrosContrato);
            
            /*CÓDIGOS PROMOCIONALES*/
            $emComercial->getConnection()->beginTransaction();
                if($strCodigoCheckMix !== '')
                    {
                        $arrayServiciosMix = explode(",", $strCodigoServMix);
                        
                        for ($intValor=0; $intValor<count($arrayServiciosMix); $intValor++)
                        {
                            
                            $intIdServicioMix  = ($arrayServiciosMix[$intValor]);
                            
                            $arrayTipoNombrePromo = explode("-", $intIdTipoPromoMix); 
                            $strIdTipoPromocion   = $arrayTipoNombrePromo[0];
                            $strNombrePromo       = $arrayTipoNombrePromo[1];
                    
                            $arrayParametros                        = array();
                            $arrayParametros['intIdServicio']       = $intIdServicioMix;
                            $arrayParametros['strIpCreacion']       = $clientIp;
                            $arrayParametros['strUsrCreacion']      = $usrCreacion;
                            $arrayParametros['strCodigo']           = $strCodigoMix;
                            $arrayParametros['strTipoPromocion']    = 'NUEVO';
                            $arrayParametros['strPromocion']        = $strNombrePromo;
                            $arrayParametros['strEstado']           = 'Activo';
                            $arrayParametros['strIdTipoPromocion']  = $strIdTipoPromocion;
                            $arrayParametros['strObservacion']      = "Se crea el código promocional {$strCodigoMix} para Mensualidad Nuevos.";
                            
                            $this->guardarCodigoPromocionAction($arrayParametros);
                        }
                        
                    }
                    else
                    {
                        
                        foreach ($arrayPromMens['MENS'] as $strServicioId => $strValorProm) 
                        {
                            
                            foreach ($strValorProm as $strIdTipoNombrePromo => $strCodigo) 
                            {
                            
                                $arrayTipoNombrePromo = explode("-", $strIdTipoNombrePromo); 
                                $strIdTipoPromocion   = $arrayTipoNombrePromo[0];
                                $strNombrePromo       = $arrayTipoNombrePromo[1];
                
                                $arrayParametros                        = array();
                                $arrayParametros['intIdServicio']       = $strServicioId;
                                $arrayParametros['strIpCreacion']       = $clientIp;
                                $arrayParametros['strUsrCreacion']      = $usrCreacion;
                                $arrayParametros['strCodigo']           = $strCodigo;
                                $arrayParametros['strTipoPromocion']    = 'NUEVO';
                                $arrayParametros['strPromocion']        = $strNombrePromo;
                                $arrayParametros['strEstado']           = 'Activo';
                                $arrayParametros['strIdTipoPromocion']  = $strIdTipoPromocion;
                                $arrayParametros['strObservacion']      = "Se crea el código promocional {$strCodigo} para Mensualidad Nuevos.";

                                $this->guardarCodigoPromocionAction($arrayParametros);
                        
                            }
                            
                        }
                                
                    }  
                    foreach ($arrayPromIns['INS'] as $strServicioId => $strValorProm) 
                    {
                         
                        foreach ($strValorProm as $strIdTipoNombrePromo => $strCodigo) 
                        {
                       
                            $arrayTipoNombrePromo = explode("-", $strIdTipoNombrePromo); 
                            $strIdTipoPromocion   = $arrayTipoNombrePromo[0];
                            $strNombrePromo       = $arrayTipoNombrePromo[1];
                            
                            $arrayParametros                        = array();
                            $arrayParametros['intIdServicio']       = $strServicioId;
                            $arrayParametros['strIpCreacion']       = $clientIp;
                            $arrayParametros['strUsrCreacion']      = $usrCreacion;
                            $arrayParametros['strCodigo']           = $strCodigo;
                            $arrayParametros['strTipoPromocion']    = 'INS';
                            $arrayParametros['strPromocion']        = $strNombrePromo;
                            $arrayParametros['strEstado']           = 'Activo';
                            $arrayParametros['strIdTipoPromocion']  = $strIdTipoPromocion;
                            $arrayParametros['strObservacion']      = "Se crea el código promocional {$strCodigo} para Instalación Nuevos.";
                            
                            $this->guardarCodigoPromocionAction($arrayParametros);
        
                        }
                      
                    }  
                    foreach ($arrayPromBw['BW'] as $strServicioId => $strValorProm) 
                    {
                         
                        foreach ($strValorProm as $strIdTipoNombrePromo => $strCodigo) 
                        {
                            
                            $arrayTipoNombrePromo = explode("-", $strIdTipoNombrePromo); 
                            $strIdTipoPromocion   = $arrayTipoNombrePromo[0];
                            $strNombrePromo       = $arrayTipoNombrePromo[1];
        
                            $arrayParametros                        = array();
                            $arrayParametros['intIdServicio']       = $strServicioId;
                            $arrayParametros['strIpCreacion']       = $clientIp;
                            $arrayParametros['strUsrCreacion']      = $usrCreacion;
                            $arrayParametros['strCodigo']           = $strCodigo;
                            $arrayParametros['strTipoPromocion']    = 'BW';
                            $arrayParametros['strPromocion']        = $strNombrePromo;
                            $arrayParametros['strEstado']           = 'Activo';
                            $arrayParametros['strIdTipoPromocion']  = $strIdTipoPromocion;
                            $arrayParametros['strObservacion']      = "Se crea el código promocional {$strCodigo} para Ancho de Banda Nuevos.";
                            
                            $this->guardarCodigoPromocionAction($arrayParametros);
        
                        }
                      
                    }
            
            

            

            $emComercial->getConnection()->commit();

            /***/
            if( $prefijoEmpresa!= 'TN' && $strCambioRazonSocial != 'S' && $strClausulaGuardada != 'S')
            {
                $arrayRespuestaParametro = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                       ->getOne("CONTRATO_FISICO_VALIDACION",
                                                                "COMERCIAL",
                                                                "",
                                                                "",
                                                                "",
                                                                "",
                                                                "",
                                                                "",
                                                                "",
                                                                $codEmpresa);
                $strEnunFirmaDigital        = $arrayRespuestaParametro['valor1'];
                $strRespFirmaDigital        = $arrayRespuestaParametro['valor2'];
                $strEnunTitFacturacion      = $arrayRespuestaParametro['valor3'];
                $strRespTitFacturacion      = $arrayRespuestaParametro['valor4'];
                $strEstadoDefault           = $arrayRespuestaParametro['valor5'];
    
                //Registrar las clausulas como aceptadas para contrato fisico.
                $arrayParamClausula = array(
                                            'nombreProceso'     => self::NOMBRE_PROCESO,
                                            'nombreDocumento'   => self::NOMBRE_DOCUMENTO,
                                            'puntoId'           => $intIdPunto,
                                            'empresaCod'        => $codEmpresa,
                                            'ipCreacion'        => '127.0.0.1',
                                            'usrCreacion'       => $usrCreacion
                                            );
                /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
                $serviceInfoPunto   = $this->get('comercial.InfoPunto');
                $arrayClausulas     = $serviceInfoPunto->getConsultaDataEncuesta($arrayParamClausula);

                if(!empty($arrayClausulas) && $arrayClausulas['status'] == "OK" && $arrayClausulas['data']['enunciados'])
                {

                    $arrayEncChecked = array();
                    foreach($arrayClausulas['data']['enunciados'] as $arrayEnunciados)
                    {
                        if($arrayEnunciados['nombreEnunciado'] == $strEnunFirmaDigital)
                        {
                            $strValorRespuest = $strRespFirmaDigital;
                        }
                        else if($arrayEnunciados['nombreEnunciado'] == $strEnunTitFacturacion)
                        {
                            $strValorRespuest = $strRespTitFacturacion;
                        }
                        else
                        {
                            $strValorRespuest = $strEstadoDefault;
                        }

                        foreach($arrayEnunciados['respuestas'] as $arrayResp)
                        {
                           if(strtoupper($arrayResp['valorRespuesta']) == strtoupper($strValorRespuest))
                            {
                                $arrayValorRespuesta = array(
                                 'idRespuesta'    => $arrayResp['idRespuesta'],
                                 'idEnunciado'    => $arrayEnunciados['idEnunciado'],
                                 'valorRespuesta' => $arrayResp['valorRespuesta'], 
                                 'idDocEnunciadoResp'=> $arrayResp['idDocEnunciadoResp']
                                  ); 

                                array_push($arrayEncChecked, $arrayValorRespuesta ); 
                          }
                        
                        } 

                     
                    }                  
             
                    $arrayClausula      = array(
                        'idDocumento'        => $arrayClausulas['data']['idDocumento'], 
                        'requiereAprobacion' => false,
                        'respuestaCliente'   => $arrayEncChecked,                   
                        'listEstado'         =>array('Reenviado'),
                        'referenciasDocumento'=> array(
                            array( 'nombreReferencia'=> 'PERSONA','valor'=>   $strIdentificacion ),
                            array( 'nombreReferencia'=> 'PUNTO', 'valor'=>   $intIdPunto )
                        ) 
                    
                    );

                    $arrayParamClausula = array( 
                                    'puntoId'           => $intIdPunto,
                                    'clausulas'         => $arrayClausula,
                                    'dataBancario'      => null,
                                    'empresaCod'        => $codEmpresa,
                                    'usrCreacion'       => $usrCreacion,
                                    'ipCreacion'        => '127.0.0.1'
                                    );
                    /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
                    $serviceInfoContrato = $this->get('comercial.InfoContrato');
                    $arraySaveClausulas  =  $serviceInfoContrato->guardarClausulasOrDataBancaria($arrayParamClausula);
                   
                    if ($arraySaveClausulas['status'] != "OK" )
                    {
                        throw new \Exception( $arraySaveClausulas["message"]);      
                    }
            
                }
                else 
                {
                    throw new \Exception( $arrayClausulas["message"]);      
                }
            }

            $strUrlCliente = $this->generateUrl('infocontrato_show', array('id' => $entity->getId()));
            $arrayRespuestaProceso =  array('strMensaje'        => "Proceso realizado",
                                            'strStatus'         => 0,
                                            'strUrl'            => $strUrlCliente,
                                            'strContratoFisico' => 1
                                            ); 

            $objResponse      = new Response(json_encode($arrayRespuestaProceso));

            return $objResponse;
        }
        catch (\Exception $e)
        {
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            $strUrlCliente = $this->generateUrl('infocontrato_new', array('idper'=>$datos_form['personaEmpresaRolId']));
            $objResponse   = new Response(
            json_encode(
                        array('strMensaje' => $e->getMessage(),
                              'strStatus'  => 99,
                              'strUrl'     => $strUrlCliente)
                        ));
            return $objResponse;
        }
    }

   /**
    * Funcion que Guarda Contrato para MD microservicio
    *
    * @author Néstor Naula <nnaulal@telconet.ec>
    * @version 1.0 30-08-2020
    *
    * @author Néstor Naula <nnaulal@telconet.ec>
    * @version 1.1 12-07-2021 - Se valida que los servicios actualizar en el adendum
    *                           esten en estado Pendiente.
    *
    * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
    * @version 1.2 14-04-2022 - Se parametriza los estados de los productos adicionales.
    *
    * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
    * @version 1.2 15-09-2022 - Se migra la inserción de la caracteristica FORMA_REALIZACION_CONTRATO y FORMA_REALIZACION_ADENDUM, esta lógica se
    * 
    * @author Joel Broncano <jbroncano@telconet.ec>
    * @version 1.3 20-04-2023 - Soporte EN
    * 
    * @param Request $objRequest 
    *
    */
    public function createContratoMSAction(Request $objRequest)
    {
        $objSession                = $objRequest->getSession();
        $arrayCheck                = $objRequest->get('check');
        $arrayClausula             = $objRequest->get('clausula');
        $strClientIp               = $objRequest->getClientIp();
        $strUsrCreacion            = $objSession->get('user');
        $intCodEmpresa             = $objSession->get('idEmpresa');
        $strPrefijoEmpresa         = $objSession->get('prefijoEmpresa');
        $intIdOficina              = $objSession->get('idOficina');                      
        $arrayFormFiles            = $objRequest->files->get('infodocumentotype');               
        $arrayFormTipos            = $objRequest->get('infodocumentotype');
        $strNombrePantalla         = $objRequest->get('nombrePantalla');
        $strProcesoContrato        = $objRequest->get('procesoContrato');
        $arrayPuntoCliente         = $objSession->get('ptoCliente');
        $strTelefono               = $objRequest->get('telefonoCliente');
        $intIdPuntoSession         = $objRequest->get('puntoCliente');
        $strCambioRazonSocial      = $objRequest->get('cambioRazonSocial');
        $arrayPromMens             = $objRequest->get('codigoMens');
        $arrayPromIns              = $objRequest->get('codigoIns');
        $arrayPromBw               = $objRequest->get('codigoBw');
        $strCodigoCheckMix         = $objRequest->get('codigoCheckMix');  
        $strCodigoServMix          = $objRequest->get('codigoServMix');
        $intIdTipoPromoMix         = $objRequest->get('idTipoPromoMix');
        $strCodigoMix              = $objRequest->get('codigoMixVal');
        $strEnviarPin              = $objRequest->get('enviarPin');
        $strEnviarPin              = isset($strEnviarPin) ? 'N' : 'S';
        $strDatoBancario           = $objRequest->get('objDatoBancario');
        $arrayDatoBancario         = json_decode($strDatoBancario, true);
        $strCorreoFisicoCliente    = $objRequest->get('correoFisicoCliente');
        $strFormaContrato          = $objRequest->get('formaContrato');
        $strEstadoEliminado        = 'Eliminado';
        $arrayServiciosMix         = '';
        $arrayMens                 = array();
        $arrayMensMix              = array();
        $arrayIns                  = array();
        $arrayBw                   = array();
        $boolIsContratoDigital     = true;
        $strContratoFisico         = 0;
        if($strFormaContrato == 'Contrato Fisico')
        {
            $boolIsContratoDigital = false;
            $strContratoFisico     = 1;
        }
        if($strNombrePantalla=='Adendum' && ($objRequest->get('formaAdemdun')=='Contrato Fisico'))
        {
                $strFormaContrato='FISICO';
                $boolIsContratoDigital=false;
                $strContratoFisico    = 1;           
        }
        
        if(!empty($arrayPuntoCliente) && empty($intIdPuntoSession))
        {
            $intIdPuntoSession         = $arrayPuntoCliente['id'];
        }
  
        $emGeneral                 = $this->getDoctrine()->getManager('telconet_general');
        $emComercial               = $this->getDoctrine()->getManager('telconet');
        $serviceInfoContrato       = $this->get('comercial.InfoContrato');
        $serviceUtil               = $this->get('schema.Util');
        
        $strConvenioPago           = isset($objRequest->get('infocontratodatoadicionaltype')['convenioPago']) ? 'S':'N';
        $strVip                    = isset($objRequest->get('infocontratodatoadicionaltype')['esVip']) ? 'S':'N';
        $strTramiteLegal           = isset($objRequest->get('infocontratodatoadicionaltype')['esTramiteLegal']) ? 'S':'N';
        $strPermiteCorteAutomatico = isset($objRequest->get('infocontratodatoadicionaltype')['permiteCorteAutomatico']) ? 'S':'N';
        $strFideicomiso            = isset($objRequest->get('infocontratodatoadicionaltype')['fideicomiso']) ? 'S':'N';
        $strTiempoEsperaMes        = !empty($objRequest->get('infocontratodatoadicionaltype')['tiempoEsperaMesesCorte']) ?
                                        $objRequest->get('infocontratodatoadicionaltype')['tiempoEsperaMesesCorte'] : 0;
                                    
        $strTiempoEsperaMesesCorte = !empty($strTiempoEsperaMes)? $strTiempoEsperaMes : '1';

        $intFormaPago              = !empty($objRequest->get('infocontratotype')['formaPagoId'])
                                            ? $objRequest->get('infocontratotype')['formaPagoId'] :
                                            ($objRequest->get('infocontratoformapagotype')['formaPagoId'] != null
                                                    ? $objRequest->get('infocontratoformapagotype')['formaPagoId']
                                                    : $objRequest->get('infopersonaempformapagotype')['formaPagoId']);

        $strValorAnticipo          = $objRequest->get('infocontratotype')['valorAnticipo'];
        $strNumContratoEmpPub      = $objRequest->get('infocontratotype')['numeroContratoEmpPub'];

        $intTipoCuentaId           = $objRequest->get('infocontratoformapagotype')['tipoCuentaId'] != null 
                                                ? $objRequest->get('infocontratoformapagotype')['tipoCuentaId']
                                                : $objRequest->get('infopersonaempformapagotype')['tipoCuentaId'];
        $intBancoTipoCuentaId      = $objRequest->get('infocontratoformapagotype')['bancoTipoCuentaId'] != null 
                                                ? $objRequest->get('infocontratoformapagotype')['bancoTipoCuentaId']
                                                : $objRequest->get('infopersonaempformapagotype')['bancoTipoCuentaId'];
        $strNumeroCtaTarjeta       = $objRequest->get('infocontratoformapagotype')['numeroCtaTarjeta'];
        $strTitularCuenta          = $objRequest->get('infocontratoformapagotype')['titularCuenta'];
        $strAnioVencimiento        = $objRequest->get('infocontratoformapagotype')['anioVencimiento'] == 'Seleccione...' ? '' :
                                     $objRequest->get('infocontratoformapagotype')['anioVencimiento'];
        $strMesVencimiento         = $objRequest->get('infocontratoformapagotype')['mesVencimiento'];
        $strCodigoVerificacion     = $objRequest->get('infocontratoformapagotype')['codigoVerificacion'];

        $intTipoContratoId         = $objRequest->get('infocontratoextratype')['tipoContratoId'];
        $intPersonaEmpresaRolId    = $objRequest->get('personaEmpresaRolId') != null ? 
                                        $objRequest->get('personaEmpresaRolId') :
                                        $objRequest->get('infocontratoextratype')['personaEmpresaRolId'];

        $strCambioPago             = $objRequest->get('CambioPago') != null ? 'S' : 'N';

        $strTipoDocumento          = $strNombrePantalla == 'Contrato' ? 'C' : 'AP';

        $strFeFinContratoStr       = $objRequest->get('feFinContratoPost');
        $objFeFinContratoPost      = date_create($strFeFinContratoStr); 
        $strFeFinContratoPost      = date_format($objFeFinContratoPost,"Y-m-d h:i:s");

        $arrayAdendumsRazonSocial  = array();
        $strCambioRazonSocial      = $strCambioRazonSocial != null ? $strCambioRazonSocial : 'N';

        $objFeInicioContrato       = (new \DateTime('now'))->format('Y-m-d H:i:s');
        if(isset($arrayDatoBancario) && !empty($arrayDatoBancario))
        {
            $strNumeroCtaTarjeta    = $arrayDatoBancario['numeroCuenta'];
            $strTitularCuenta       = $arrayDatoBancario['titular'];
            $strAnioVencimiento     = $arrayDatoBancario['anio'];
            $strMesVencimiento      = $arrayDatoBancario['mes'];
        }

        if(!empty($strProcesoContrato))
        {
            $strTipoDocumento          = $strProcesoContrato == 'Contrato' ? 'C' : 'AS';
            $strNombrePantalla         = $strProcesoContrato;
        }
        $arrayServiciosEnv         = array();
        $arrayParametrosContrato   = array();
        $strFlujoLinkBanca         = 'N';

        try
        {

            $arrayParamRegulariza = array('intPersonaEmpresaRolId' => $intPersonaEmpresaRolId,
                'strCodEmpresa' => ($intCodEmpresa . ''),
                'strUsuario' => $strUsrCreacion);
            $arrayRespContratos   = $serviceInfoContrato->regularizarContratoDigital($arrayParamRegulariza);
            if(!empty($arrayRespContratos['arrayValidacionServ']))
            {
                foreach($arrayRespContratos['arrayValidacionServ'] as $arrayValidaciones)
                {
                    $arrayValidacion[] = array("description"  => "contractStatus",
                                                "message"     => $arrayValidaciones['strMensaje'],
                                                "restricted"  => true);
                    $arrayParametrosLog['enterpriseCode']   = $intCodEmpresa;
                    $arrayParametrosLog['logType']          = "1";
                    $arrayParametrosLog['logOrigin']        = "TELCOS";
                    $arrayParametrosLog['application']      = basename(__FILE__);
                    $arrayParametrosLog['appClass']         = basename(__CLASS__);
                    $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                    $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                    $arrayParametrosLog['messageUser']      = "ERROR";
                    $arrayParametrosLog['status']           = "Fallido";
                    $arrayParametrosLog['creationUser']     = $strUsrCreacion;
                    $arrayParametrosLog['descriptionError'] = $arrayValidaciones['strDescripcion'];
                    $arrayParametrosLog['inParameters']     = json_encode($arrayValidaciones, 128);
                
                    $serviceUtil->insertLog($arrayParametrosLog);
                }
                throw new \Exception('Existe inconsistencia en la data. Por favor comuníquese con soporte para su revisión');
            }

            $arrayTipoDocumentos = array ();        

            foreach ($arrayFormTipos as $objTipos)
            {                           
                foreach ( $objTipos as $intKeyTipo => $strValueTipo)
                {                     
                    foreach ($arrayFormFiles as $objImagenes)                 
                    {  
                        foreach ( $objImagenes as $intKeyImagen => $strValueImagen) 
                        {        
                            if($intKeyTipo == $intKeyImagen)
                            {  
                                $strImagenBase64     = base64_encode(file_get_contents($strValueImagen)); 
                                $objTiposDocumentos  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                                ->findOneById($strValueTipo);

                                $arrayTipoDocumentos[$intKeyTipo] = array(
                                                                    "tipoDocumentoGeneralId"     => $strValueTipo,
                                                                    "codigoTipoDocumentoGeneral" => $objTiposDocumentos->getCodigoTipoDocumento(),
                                                                    "documento"                  => $strImagenBase64); 
                            }                   
                        }                          
                    
                    }             
                }
            }

            if (empty($strUsrCreacion))
            {
                throw new \Exception('No se logró obtener el usuario en sesión. Por favor refresque la pantalla.');
            }

            // Validar si existe un contrato activo del muevo personaEmpresaRolId
            $objContratoActivo = $emComercial->getRepository('schemaBundle:InfoContrato')
                                            ->findBy(array("personaEmpresaRolId" => $intPersonaEmpresaRolId,
                                                            "estado"              => array('PorAutorizar', 'Activo')));
            if(!empty($objContratoActivo) && count($objContratoActivo) > 0)
            {
                $objContrato                 = $objContratoActivo[0];
            }
            if($strCambioRazonSocial == "S")
            {
                if(is_object($objContrato))
                {
                    $strTipoDocumento = "AP";
                }

                if(!empty($intPersonaEmpresaRolId))
                {
                    $arrayPuntosCliente = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                    ->findBy(array("personaEmpresaRolId" => $intPersonaEmpresaRolId,
                                                                    "estado"              => 'Activo'));
                } else
                {
                    throw new \Exception("Parámetro personaEmpresaRolId no encontrado.");
                }

                $strTipo = "AP";
                foreach ($arrayPuntosCliente as $objPunto)
                {
                    $arrayServicios = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                ->findBy(array("puntoId" => $objPunto->getId(),
                                                                "estado"  => 'Activo'));

                    //Obtener la numeracion de la tabla Admi_numeracion
                    $strCodigoNumeracion = $strTipo == 'AS' ? 'CONA' : 'CON';
                    $objDatosNumeracion  = $emComercial->getRepository('schemaBundle:AdmiNumeracion')
                                                    ->findByEmpresaYOficina($intCodEmpresa, $intIdOficina, $strCodigoNumeracion);
                    if($objDatosNumeracion)
                    {
                        $intSecuencia                   = str_pad($objDatosNumeracion->getSecuencia(), 7, "0", STR_PAD_LEFT);
                        $boolActualizarSecuenciaAdendum = false;
                    } else
                    {
                        throw new \Exception("No se pudo obtener la numeración", 206);
                    }

                    foreach ($arrayServicios as $objServicio)
                    {
                        $objAdendumRSPendiente = $emComercial->getRepository('schemaBundle:InfoAdendum')
                                                            ->findBy(array("puntoId"    => $objPunto->getId(),
                                                                            "servicioId" => $objServicio->getId(),
                                                                            "estado"     => array('Pendiente')));
                        if(!empty($objAdendumRSPendiente) && count($objAdendumRSPendiente) > 0)
                        {
                            array_push($arrayAdendumsRazonSocial, $objAdendumRSPendiente[0]->getId());
                        }
                    }

                    if($boolActualizarSecuenciaAdendum)
                    {
                        //Actualizo la numeracion en la tabla
                        $intSecuencia = ($objDatosNumeracion->getSecuencia() + 1);
                        $objDatosNumeracion->setSecuencia($intSecuencia);
                        $emComercial->persist($objDatosNumeracion);
                        $emComercial->flush();
                    }
                }
            }
            else
            {
                if(empty($intIdPuntoSession) && !empty($intPersonaEmpresaRolId))
                {
                    $objPunto = $emComercial->getRepository('schemaBundle:InfoPunto')
                                            ->findOneByPersonaEmpresaRolId($intPersonaEmpresaRolId);

                    $intIdPuntoSession = $objPunto->getId();
                }

                if(!empty($intIdPuntoSession))
                {
                    $arrayAdendum = $emComercial->getRepository('schemaBundle:InfoAdendum')
                                                ->findBy(array(
                                                                'puntoId' => $intIdPuntoSession,
                                                                'estado'  => 'Pendiente'
                                                            )
                                                        );                        
                }

                $arrayParametrosEstados = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('ESTADOS_PRODUCTOS_ADICIONALES_CONTRATOS_WEB', 
                                                            'COMERCIAL', 
                                                            '',
                                                            'ESTADOS REQUERIDO PARA PRODUCTOS ADICIONALES DE CONTRATO WEB',
                                                            '', 
                                                            '',
                                                            '',
                                                            '', 
                                                            '', 
                                                            $intCodEmpresa);
                if(!empty($arrayParametrosEstados))
                {
                    $strValor1           = $arrayParametrosEstados['valor1'];
                    $arrayEstadosProdAdic= explode(",",$strValor1);
                }

                foreach($arrayAdendum as $objAdendum)
                {
                    $strKeyServ  = array_search($objAdendum->getServicioId(), $arrayServiciosEnv);
                    $strTipoAdem = $objAdendum->getTipo();
                    $objServicioAdem = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                   ->findOneBy(array(
                                                        'id'     => $objAdendum->getServicioId(),
                                                        'estado' => $arrayEstadosProdAdic
                                                        ));
                    if(empty($strKeyServ) && empty($strTipoAdem) && is_object($objServicioAdem))
                    {
                        array_push($arrayServiciosEnv,$objAdendum->getServicioId());
                    }
                }
            }

            $arrayDatosTarjeta = array( 'numeroCtaTarjeta'        => $strNumeroCtaTarjeta,
                                        'titularCuenta'           => $strTitularCuenta,
                                        'anioVencimiento'         => $strAnioVencimiento,
                                        'mesVencimiento'          => $strMesVencimiento,
                                        'codigoVerificacion'      => $strCodigoVerificacion);
            if($strTipoDocumento == 'C')
            {                                       
                $arrayDatosContrato = array_merge(
                    array('servicioId'              => '1'),
                    array('servicios'               => $arrayServiciosEnv),
                    array('puntoId'                 => $intIdPuntoSession),
                    array('valorAnticipo'           => $strValorAnticipo),
                    array('numContratoEmpPub'       => $strNumContratoEmpPub),
                    array('codNumeracionVE'         => ""),  
                    array('feInicioContrato'        => $objFeInicioContrato),
                    array('feFinContratoPost'       => $strFeFinContratoPost), 
                    array('esConvenioPago'          => $strConvenioPago),
                    array('esTramiteLegal'          => $strTramiteLegal),
                    array('esVip'                   => $strVip),
                    array('permitirCorteAutomatico' => $strPermiteCorteAutomatico),
                    array('fideicomiso'             => $strFideicomiso),
                    array('tiempoEsperaMesesCorte'  => $strTiempoEsperaMesesCorte),
                    array('tipoContratoId'          => $intTipoContratoId),
                    array('tipoCuentaId'            => $intTipoCuentaId),
                    array('bancoTipoCuentaId'       => $intBancoTipoCuentaId),
                    array('formaPagoId'             => $intFormaPago),
                    array('adendumsRazonSocial'     => $arrayAdendumsRazonSocial),
                    $arrayDatosTarjeta
                );
                $arrayParametrosContrato['contrato'] = $arrayDatosContrato;
            }
            else
            {
                if(is_object($objContrato))
                {
                    $intFormaPagoIdC       = $objContrato->getFormaPagoId()->getId();
                    $objContratoFormaPago = $emComercial->getRepository('schemaBundle:InfoContratoFormaPago')
                                                        ->findPorContratoIdYEstado($objContrato->getId(), 'Activo');

                    if(is_object($objContratoFormaPago))
                    {
                        $intTipoCuentaIdC         = $objContratoFormaPago->getTipoCuentaId()->getId();
                        $intBancoTipoCuentaIdC    = $objContratoFormaPago->getBancoTipoCuentaId()->getId();
                    }
                }

                if($strCambioRazonSocial == "N")
                {
                    $entityAdendum = $emComercial->getRepository('schemaBundle:InfoAdendum')
                                                ->findBy(array(
                                                        "puntoId" => $intIdPuntoSession
                                                ));
                    
                    foreach($entityAdendum as $objAdendum)
                    {
                        $strTipoAdendum = $objAdendum->getTipo();
                        if(!empty($strTipoAdendum) && $objAdendum->getEstado() != $strEstadoEliminado)
                        {
                            $strTipoDocumento = 'AS';
                        }
                    }
                }

                $arrayDatosAdendum = array_merge(
                    array('contratoId'              => $objContrato->getId()),
                    array('puntoId'                 => $intIdPuntoSession),
                    array('servicios'               => $arrayServiciosEnv),
                    array('cambioNumeroTarjeta'     => $strCambioPago),
                    array('tipoCuentaId'            => $intTipoCuentaId != null ? $intTipoCuentaId : $intTipoCuentaIdC),
                    array('bancoTipoCuentaId'       => $intBancoTipoCuentaId != null ? $intBancoTipoCuentaId : $intBancoTipoCuentaIdC),
                    array('formaPagoId'             => $intFormaPago != null ? $intFormaPago : $intFormaPagoIdC),
                    array('adendumsRazonSocial'     => $arrayAdendumsRazonSocial),
                    $arrayDatosTarjeta
                );
                $arrayParametrosContrato['adendum'] = $arrayDatosAdendum;
            }

            if($strTipoDocumento == 'C')
            {
            /*Codigos Promocionales*/
                if($strCodigoCheckMix !== '')
                {
                
                    $arrayServiciosMix = explode(",", $strCodigoServMix);
                    
                    for ($intValor=0; $intValor<count($arrayServiciosMix); $intValor++)
                    {
                        $intIdServicioMix  = ($arrayServiciosMix[$intValor]);
                        $arrayTipoNombrePromo = explode("-", $intIdTipoPromoMix); 
                        $strIdTipoPromocion   = $arrayTipoNombrePromo[0];
                        $strNombrePromo       = $arrayTipoNombrePromo[1];
                        
                        if($strIdTipoPromocion!=0 || $strCodigoMix!='')
                        {
                            $arrayMensPromoMix['tipoPromocionId']  = $strIdTipoPromocion;
                            $arrayMensPromoMix['servicioId']       = $intIdServicioMix;
                            $arrayMensPromoMix['codigoPromo']      = $strCodigoMix;
                            $arrayMensPromoMix['tipoPromocion']    = 'PROM_COD_NUEVO';
                            $arrayMensPromoMix['observacion']      = "Se crea el código promocional {$strCodigoMix} para Mensualidad Mix Nuevos,"
                                                                    . " nombre promoción: {$strNombrePromo}.";

                            array_push($arrayMensMix, $arrayMensPromoMix);
                        }
                    }
                }
                else
                {
                    foreach ($arrayPromMens['MENS'] as $strServicioId => $strValorProm) 
                    {
                        foreach ($strValorProm as $strIdTipoNombrePromo => $strCodigo) 
                        {
                            $arrayTipoNombrePromo = explode("-", $strIdTipoNombrePromo); 
                            $strIdTipoPromocion   = $arrayTipoNombrePromo[0];
                            $strNombrePromo       = $arrayTipoNombrePromo[1];

                            if($strIdTipoPromocion!=0 || $strCodigo!='')
                            {
                                $arrayMensPromo['tipoPromocionId']  = $strIdTipoPromocion;
                                $arrayMensPromo['servicioId']       = $strServicioId;
                                $arrayMensPromo['codigoPromo']      = $strCodigo;
                                $arrayMensPromo['tipoPromocion']    = 'PROM_COD_NUEVO';
                                $arrayMensPromo['observacion']      = "Se crea el código promocional {$strCodigo} para Mensualidad Nuevos,"
                                                                    . " nombre promoción: {$strNombrePromo}.";

                                array_push($arrayMens, $arrayMensPromo);
                            
                            }
                        }
                    }
                }    
                
                foreach ($arrayPromIns['INS'] as $strServicioId => $strValorProm) 
                {
                    foreach ($strValorProm as $strIdTipoNombrePromo => $strCodigo) 
                    {
                        $arrayTipoNombrePromo = explode("-", $strIdTipoNombrePromo); 
                        $strIdTipoPromocion   = $arrayTipoNombrePromo[0];
                        $strNombrePromo       = $arrayTipoNombrePromo[1];
                        
                        if($strIdTipoPromocion!=0 || $strCodigo!='')
                        {
                            $arrayInsPromo['tipoPromocionId']  = $strIdTipoPromocion;
                            $arrayInsPromo['servicioId']       = $strServicioId;
                            $arrayInsPromo['codigoPromo']      = $strCodigo;
                            $arrayInsPromo['tipoPromocion']    = 'PROM_COD_INST';
                            $arrayInsPromo['observacion']      = "Se crea el código promocional {$strCodigo} para Instalacion Nuevos"
                                                                . ", nombre promoción: {$strNombrePromo}.";
                            
                            array_push($arrayIns, $arrayInsPromo);
                        }
                        
                        

                    }
                }
                
                foreach ($arrayPromBw['BW'] as $strServicioId => $strValorProm) 
                {
                    
                    foreach ($strValorProm as $strIdTipoNombrePromo => $strCodigo) 
                    {
                        
                        $arrayTipoNombrePromo = explode("-", $strIdTipoNombrePromo); 
                        $strIdTipoPromocion   = $arrayTipoNombrePromo[0];
                        $strNombrePromo       = $arrayTipoNombrePromo[1];
                        
                        if($strIdTipoPromocion!=0 || $strCodigo!='')
                        {
                            $arrayBwPromo['tipoPromocionId']  = $strIdTipoPromocion;
                            $arrayBwPromo['servicioId']       = $strServicioId;
                            $arrayBwPromo['codigoPromo']      = $strCodigo;
                            $arrayBwPromo['tipoPromocion']    = 'PROM_COD_BW';
                            $arrayBwPromo['observacion']      = "Se crea el código promocional {$strCodigo} para Ancho de Banda Nuevos,"
                                                            . " nombre promoción: {$strNombrePromo}.";
                            
                            array_push($arrayBw, $arrayBwPromo);
                        
                        }
                    }
                }
            /**/
            }

            /* @var $serviceTokenCas \telconet\seguridadBundle\Service\TokenCasService */
            $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas = $serviceTokenCas->generarTokenCas();

            if(empty($arrayTokenCas['strToken']))
            {
                throw new \Exception($arrayTokenCas['strMensaje']); 
            }

            $arrayParametrosContrato['ipCreacion']          = $strClientIp; 
            $arrayParametrosContrato['codEmpresa']          = $intCodEmpresa;
            $arrayParametrosContrato['prefijoEmpresa']      = $strPrefijoEmpresa; 
            $arrayParametrosContrato['usrCreacion']         = $strUsrCreacion; 
            $arrayParametrosContrato['origen']              = 'WEB';
            $arrayParametrosContrato['enviarPin']           = $strEnviarPin;
            $arrayParametrosContrato['oficinaId']           = $intIdOficina;
            $arrayParametrosContrato['tipo']                = $strTipoDocumento;
            $arrayParametrosContrato['numeroTelefonico']    = $strTelefono;
            $arrayParametrosContrato['personaEmpresaRolId'] = $intPersonaEmpresaRolId; 
            $arrayParametrosContrato['check']               = $arrayCheck; 
            $arrayParametrosContrato['clausula']            = $arrayClausula;
            $arrayParametrosContrato['token']               = $arrayTokenCas['strToken'];
            $arrayParametrosContrato['cambioRazonSocial']   = $strCambioRazonSocial;
            $arrayParametrosContrato['documentosContrato']  = $arrayTipoDocumentos;

            /*Parámetros para códigos promocionales*/
            $arrayParametrosContrato['promoMix']            = $arrayMensMix;
            $arrayParametrosContrato['promoMens']           = $arrayMens;
            $arrayParametrosContrato['promoIns']            = $arrayIns;
            $arrayParametrosContrato['promoBw']             = $arrayBw;
            $arrayParametrosContrato['formaContrato']      = $strFormaContrato;

            $arrayParametrosContrato['contrato']['correoContrato']     = array($strCorreoFisicoCliente);
            $arrayParametrosContrato['isContratoDigital']              = $boolIsContratoDigital;
            /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
            $serviceInfoContrato = $this->get('comercial.InfoContrato');

            $objInfoPersonaEmpresaRol        = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intPersonaEmpresaRolId);
            //Obtengo Característica de USUARIO
            $objCaracteristicaUsuario = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array("descripcionCaracteristica" => "USUARIO",
                                                                      "estado"                    => "Activo"));

            if(is_object($objCaracteristicaUsuario))
            {
                $arrayParameCaractPersona = array( 
                                                    'estado'              => 'Activo',
                                                    'personaEmpresaRolId' => $objInfoPersonaEmpresaRol,
                                                    'caracteristicaId'    => $objCaracteristicaUsuario
                                                );

                $objPersonaEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                        ->findOneBy( $arrayParameCaractPersona );
                
                //Inserto Caracteristica de USUARIO si no existe
                if(!is_object($objPersonaEmpresaRolCarac))
                {
                    $objPersEmpRolCaracUsuario = new InfoPersonaEmpresaRolCarac();
                    $objPersEmpRolCaracUsuario->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                    $objPersEmpRolCaracUsuario->setCaracteristicaId($objCaracteristicaUsuario);
                    $objPersEmpRolCaracUsuario->setValor($objInfoPersonaEmpresaRol->getPersonaId()->getIdentificacionCliente());
                    $objPersEmpRolCaracUsuario->setFeCreacion(new \DateTime('now'));
                    $objPersEmpRolCaracUsuario->setUsrCreacion($strUsrCreacion);
                    $objPersEmpRolCaracUsuario->setEstado('Activo');
                    $objPersEmpRolCaracUsuario->setIpCreacion($strClientIp);
                    $emComercial->persist($objPersEmpRolCaracUsuario);
                    $emComercial->flush();
                }
            }

            $arrayRespuesta   = $serviceInfoContrato->crearContratoMS($arrayParametrosContrato);
            $objResponse      = new Response(json_encode($arrayRespuesta));

            $intIdContrato = 0;
            if($arrayRespuesta['strStatus'] == 0)
            {
                $objContrato  = $emComercial->getRepository('schemaBundle:InfoContrato')
                                            ->findOneBy(array( "personaEmpresaRolId" => $intPersonaEmpresaRolId,
                                                               "estado"              => "PorAutorizar"));
            
                if(is_object($objContrato))
                {
                    $intIdContrato   = $objContrato->getId();

                    if($strTipoDocumento == 'C')
                    {
                        $objEntityCaract = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(
                                                         array("descripcionCaracteristica"   => 'FORMA_REALIZACION_CONTRATO',
                                                               "estado"                      => 'Activo'));
                    }
                    else
                    {
                        $objEntityCaract = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(
                                                        array("descripcionCaracteristica"   => 'FORMA_REALIZACION_ADENDUM',
                                                              "estado"                      => 'Activo'));
                    }

                    if(is_object($objEntityCaract))
                    {
                        $entityCaractContrato = $emComercial->getRepository('schemaBundle:InfoContratoCaracteristica')
                                                        ->findOneBy(
                                                            array("caracteristicaId"  => $objEntityCaract,
                                                                  "contratoId"        => $objContrato,
                                                                  "estado"            => "Activo"
                                                            ));

                        if(is_object($entityCaractContrato))
                        {
                            $entityCaractContrato->setFeUltMod(new \DateTime('now'));
                            $entityCaractContrato->setUsrUltMod($strUsrCreacion);
                            $entityCaractContrato->setValor2('I');
                        }
                        else
                        {
                            $entityCaractContrato = new InfoContratoCaracteristica();
                            $entityCaractContrato->setCaracteristicaId($objEntityCaract);
                            $entityCaractContrato->setContratoId($objContrato);
                            $entityCaractContrato->setEstado('Activo');
                            $entityCaractContrato->setFeCreacion(new \DateTime('now'));
                            $entityCaractContrato->setUsrCreacion($strUsrCreacion);
                            $entityCaractContrato->setIpCreacion('127.0.0.1');
                            $entityCaractContrato->setValor1('DIGITAL');
                            $entityCaractContrato->setValor2('I');
                        }
                        $emComercial->persist($entityCaractContrato);
                        $emComercial->flush();
                    }
                }
            }
            else if($arrayRespuesta['strStatus'] == "ERROR")
            {
                throw new \Exception($arrayRespuesta['strStatus']);
            }
            if($strContratoFisico == 1)
            {
                $objContrato  = $emComercial->getRepository('schemaBundle:InfoContrato')
                                            ->findOneBy(array( "personaEmpresaRolId" => $intPersonaEmpresaRolId,
                                                               "estado"              => array('Pendiente')  ));
                if($objRequest->get('formaAdemdun')=='Contrato Fisico')
                {   
                    $objContrato  = $emComercial->getRepository('schemaBundle:InfoContrato')
                    ->findOneBy(array( "id" => $arrayParametrosContrato['adendum']['contratoId'])); 
                }
                if(is_object($objContrato))
                {
                    $intIdContrato   = $objContrato->getId();
                }
            }
            $objCaractLinkDataBanc  = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                  ->findOneBy(array("descripcionCaracteristica" => 'linkDatosBancarios',
                                                                    "estado"                    => 'Activo'));
            if(!empty($objCaractLinkDataBanc) && is_object($objCaractLinkDataBanc))
            {
                $objInfoPuntoCaractLinkDataBanc = $emComercial->getRepository("schemaBundle:InfoPuntoCaracteristica")
                                                                ->findOneBy(array("puntoId"          => $intIdPuntoSession,
                                                                                  "caracteristicaId" => $objCaractLinkDataBanc,
                                                                                  "estado"           => "Activo"));
                if($intIdContrato == 0 && $strTipoDocumento != 'AS' && is_object($objInfoPuntoCaractLinkDataBanc))
                {
                    $objPuntoNuevo  = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                ->findOneById($intIdPuntoSession);
                    if(is_object($objPuntoNuevo))
                    {
                        $intPersonaEmpresaRolId = $objPuntoNuevo->getPersonaEmpresaRolId()->getId();
                        $objContrato            = $emComercial->getRepository('schemaBundle:InfoContrato')
                                                            ->findOneBy(array("personaEmpresaRolId" => $intPersonaEmpresaRolId,
                                                                                "estado"              => array("Activo", "Pendiente")));
                        $intIdContrato          = is_object($objContrato) ? $objContrato->getId() : 0;
                        $strFlujoLinkBanca      = 'S';
                    }
                }
            }

            $strUrlCliente = $this->generateUrl('infocontrato_show', array('id'    => $intIdContrato,
                                                                           'idper' => $intPersonaEmpresaRolId));

            $arrayRespuestaProceso =  array('strMensaje'        => $arrayRespuesta['strMensaje'],
                                            'strStatus'         => $arrayRespuesta['strStatus'],
                                            'strUrl'            => $strUrlCliente,
                                            'strContratoFisico' => $strContratoFisico,
                                            'strFlujoLinkBanca' => $strFlujoLinkBanca,
                                            ); 

            $objResponse      = new Response(json_encode($arrayRespuestaProceso));

            return $objResponse;
        }
        catch (\Exception $e)
        {   
            $objResponse = new Response(
                               json_encode(
                                           array('strMensaje' => $e->getMessage(),
                                                 'strStatus'  => 99)
                                          ));
            return $objResponse;
        }
    }

    /**
     * Función encargada de reenviar Pin
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.0 28-10-2020       
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reenvioPinAction()
    {
        $objRequest            = $this->getRequest();
        $objSession            = $objRequest->getSession();

        $strUsrCreacion        = $objSession->get('user');
        $intCodEmpresa         = $objSession->get('idEmpresa');

        $intPersonaEmpresaRol  = $objRequest->get('personaEmpresaRolId');
        $strTelefono           = $objRequest->get('telefonoCliente');
        $strProceso            = "CONTRATODIGITAL";

        try
        {

            /* @var $serviceTokenCas \telconet\seguridadBundle\Service\TokenCasService */
            $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas = $serviceTokenCas->generarTokenCas();

            if(empty($arrayTokenCas['strToken']))
            {
                throw new \Exception($arrayTokenCas['strMensaje']); 
            }

            $arrayParametrosPin = array('telefono'            => $strTelefono,
                                        'token'               => $arrayTokenCas['strToken'],
                                        "codEmpresa"          => $intCodEmpresa,
                                        "usrCreacion"         => $strUsrCreacion,
                                        "proceso"             => $strProceso,
                                        "personaEmpresaRolId" => $intPersonaEmpresaRol);

            /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
            $serviceInfoContrato = $this->get('comercial.InfoContrato');
            $arrayRespuesta   = $serviceInfoContrato->reenviarPinMS($arrayParametrosPin);
            $objResponse      = new Response(json_encode($arrayRespuesta));
            return $objResponse;
        }
        catch (\Exception $e)
        {   
            $objResponse = new Response(
                json_encode(
                            array('strMensaje' => $e->getMessage(),
                                  'strStatus'  => 99)
                           ));
            return $objResponse;     
        }
    }
    
    /**
     * Función encargada de autorizar el contrato
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.0 28-10-2020       
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.1 02-07-2021 - Se corrige al momento de la autorización del contrato no realice la lógica de cambio de razón social.
     *
     * @author Alex Gomez <algomez@telconet.ec>
     * @version 1.2 10-08-2022 - Se recibe nuevo parámetro con array de puntos a activar desde el MS por proceso de CRS.
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.2 15-09-2022 - Se migra la inserción de la caracteristica FORMA_REALIZACION_CONTRATO y FORMA_REALIZACION_ADENDUM, esta lógica se
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function autorizarContratoAction()
    {
        $objRequest            = $this->getRequest();
        $objSession            = $objRequest->getSession();

        $intIdPuntoSession     = $objRequest->get('puntoCliente');
        $intPersonaEmpresaRol  = $objRequest->get('personaEmpresaRolId');

        $arrayPuntosCRS        = json_decode($objRequest->get('arrayPuntosCRS'));

        $strPin                = $objRequest->get('pin');
        $strTipo               = $objRequest->get('tipo');
        $strTipoDocumento      = $strTipo == 'Contrato' ? 'C' : 'AP';
        $strCambioRazonSocial  = $objRequest->get('cambioRazonSocial');
        $strCambiarRazonSocial = !empty($strCambioRazonSocial) ? $strCambioRazonSocial:'N';
        $strCodEmpresa         = $objSession->get('idEmpresa');
        $strPrefijoEmpresa     = $objSession->get('prefijoEmpresa');
        $strUser               = $objSession->get('user'); 
        $strIpCliente          = $objRequest->getClientIp();
        $emComercial           = $this->getDoctrine()->getManager('telconet');
        $intIdContrato         = 0;

        if(empty($intIdPuntoSession))
        {
            $arrayPuntoCliente         = $objSession->get('ptoCliente');
            if(!empty($arrayPuntoCliente))
            {
                $intIdPuntoSession     = $arrayPuntoCliente['id'];
            }
        }

        try
        {

            if (empty($strUser))
            {
                throw new \Exception('No se logró obtener el usuario en sesión. Por favor refresque la pantalla.');
            }

            if(!empty($intIdPuntoSession) && empty($intPersonaEmpresaRol))
            {
                $objPunto = $emComercial->getRepository('schemaBundle:InfoPunto')
                                        ->findOneById($intIdPuntoSession);

                if(is_object($objPunto))
                {
                    $intPersonaEmpresaRol = $objPunto->getPersonaEmpresaRolId()->getId();
                }
            }

            if($strCambiarRazonSocial == "N" && $strTipoDocumento != 'C')
            {
                $entityAdendum = $emComercial->getRepository('schemaBundle:InfoAdendum')
                                            ->findBy(array(
                                                    "puntoId" => $intIdPuntoSession,
                                                    "estado"  => array("PorAutorizar", "Pendiente")
                                            ));
                
                $strTipoAdendumAnt = "AP";
                foreach($entityAdendum as $objAdendum)
                {
                    $strTipoAdendum = $objAdendum->getTipo();
                    if(!empty($strTipoAdendum) && $strTipoAdendum != $strTipoAdendumAnt)
                    {
                        $strTipoDocumento = 'AS';
                    }
                    $strTipoAdendumAnt = $strTipoAdendum;
                }

                if($strTipoDocumento == 'AS' || $strTipoDocumento == 'AP')
                {
                    $entityAdendum = $emComercial->getRepository('schemaBundle:InfoAdendum')
                                                ->findBy(array(
                                                        "puntoId" => $intIdPuntoSession,
                                                        "tipo"    => $strTipoDocumento,
                                                        "estado"  => array("PorAutorizar", "Pendiente")
                                                ),
                                                array('id' => 'desc'));
    
                    if(!empty($entityAdendum))
                    {
                        $strNumeroAdendum = $entityAdendum[0]->getNumero();
                    }
    
                }

            }

            $objContrato  = $emComercial->getRepository('schemaBundle:InfoContrato')
                                            ->findOneBy(array( "personaEmpresaRolId" => $intPersonaEmpresaRol,
                                                               "estado"              => array("PorAutorizar", "Activo")));

            if (!is_object($objContrato))
            {
                throw new \Exception('No se logró encontrar el contrato por estado PorAutorizar o Activo. ('.$intPersonaEmpresaRol.')'); 
            }

            $intIdContrato = $objContrato->getId();

            if($strCambiarRazonSocial == 'S')
            {
                if ($objContrato->getEstado() == "Activo")
                {
                    $strTipoDocumento = "AP";
                }

                $objAdendumRSPendiente = $emComercial->getRepository('schemaBundle:InfoAdendum')
                                                     ->findBy(array("contratoId" => $intIdContrato,
                                                                    "tipo"       => 'AP',
                                                                    "estado"     => array('Pendiente','PorAutorizar')),
                                                             array('id' => 'desc'));
                if(!empty($objAdendumRSPendiente) && count($objAdendumRSPendiente) > 0)
                {
                    $strNumeroAdendum = $objAdendumRSPendiente[0]->getNumero();
                }
            }

            /* @var $serviceTokenCas \telconet\seguridadBundle\Service\TokenCasService */
            $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas = $serviceTokenCas->generarTokenCas();

            $arrayParametrosAutorizar = array(
                                              'ipCreacion'          => $strIpCliente,
                                              'codEmpresa'          => $strCodEmpresa,
                                              'prefijoEmpresa'      => $strPrefijoEmpresa,
                                              'usrCreacion'         => $strUser,
                                              'origen'              => "WEB", 
                                              'tipo'                => $strTipoDocumento,
                                              'personaEmpresaRolId' => $intPersonaEmpresaRol,
                                              'pin'                 => $strPin,
                                              'puntoId'             => $intIdPuntoSession,
                                              'puntosCRS'           => $arrayPuntosCRS,
                                              
                                              'numeroAdendum'       => $strNumeroAdendum,
                                              'cambioRazonSocial'   => $strCambiarRazonSocial,
                                              'token'               => $arrayTokenCas['strToken']);

            /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
            $serviceInfoContrato    = $this->get('comercial.InfoContrato');





            $arrayRespuesta         = $serviceInfoContrato->autorizarContratoMS($arrayParametrosAutorizar);

            $strUrlCliente          = $this->generateUrl('infocontrato_show', array('id'    => $intIdContrato));

            $arrayRespuestaProceso  =  array('strMensaje'       => $arrayRespuesta['strMensaje'],
                                            'strStatus'         => $arrayRespuesta['strStatus'],
                                            'strUrl'            => $strUrlCliente
                                            ); 
         
            if(is_object($objContrato) && $arrayRespuesta['strStatus'] == 0)
            {
                if($strTipoDocumento == 'C')
                {
                    $objEntityCaract = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(
                                                    array("descripcionCaracteristica"   => 'FORMA_REALIZACION_CONTRATO',
                                                          "estado"                      => 'Activo'));
                }
                else
                {
                    $objEntityCaract = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(
                                                    array("descripcionCaracteristica"   => 'FORMA_REALIZACION_ADENDUM',
                                                          "estado"                      => 'Activo'));
                }

                if(is_object($objEntityCaract))
                {
                    $entityCaractContrato = $emComercial->getRepository('schemaBundle:InfoContratoCaracteristica')
                                                        ->findOneBy(
                                                            array("caracteristicaId"  => $objEntityCaract,
                                                                  "contratoId"        => $objContrato,
                                                                  "estado"            => "Activo"
                                                            ));

                    if(is_object($entityCaractContrato))
                    {
                        $entityCaractContrato->setFeUltMod(new \DateTime('now'));
                        $entityCaractContrato->setUsrUltMod($strUser);
                        $entityCaractContrato->setValor2('C');
                    }
                    else
                    {
                        $entityCaractContrato = new InfoContratoCaracteristica();
                        $entityCaractContrato->setCaracteristicaId($objEntityCaract);
                        $entityCaractContrato->setContratoId($objContrato);
                        $entityCaractContrato->setEstado('Activo'); 
                        $entityCaractContrato->setFeCreacion(new \DateTime('now'));
                        $entityCaractContrato->setUsrCreacion($strUser);
                        $entityCaractContrato->setIpCreacion('127.0.0.1');
                        $entityCaractContrato->setValor1('DIGITAL');
                        $entityCaractContrato->setValor2('C');
                    }

                    $emComercial->persist($entityCaractContrato);
                    $emComercial->flush();
                }
            }

            $objResponse            = new Response(json_encode($arrayRespuestaProceso));
            return $objResponse;
        }
        catch (\Exception $e)
        {  
            $arrayRespuestaProceso  =  array('strMensaje'       => $e->getMessage(),
                                             'strStatus'        => 99
                                            );  
            $objResponse = new Response(json_encode($arrayRespuestaProceso));
            return $objResponse;     
        }
    }

    /**
    * @Secure(roles="ROLE_60-1006")
    * Funcion que permite la edicion de la forma de pago del contrato del cliente  
    * Se agrega descencriptado del campo Numero_Cta_tarjeta para que pueda ser editable por el 
    * Administrador de Contratos.  
    * @author : telcos
    * @author : apenaherrera
    * @param integer $id      
    * @version 1.1 02-12-2014
    *
    * @author : Andrés Montero <amontero@telconet.ec>
    * @version 1.2 25-08-2017
    * Actualizacion: Se envia id de pais de la empresa en sesión por parametros 
    * al crear formulario de formas de pago con InfoContratoFormaPagoEditType 
    * 
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.3 01-09-2017
    * - Se agrega el pais al type InfoContratoFormaPagoEditType para poder cargar los tipos de cuenta
    *   en la validacion de si posee forma de pago diferente a efectivo
    *  
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.4 01-04-2020 Se agrega enmascaramiento de número tarjeta-cuenta mediante lectura de parámetro.
    * 
    * @author : Angel Reina <areina@telconet.ec>
    * @version 1.5 27-09-2019 Se agrega funcionalidad para incluir nuevos campos en la edición de CFP.
    * 
    * @author : Gustavo Narea <gnarea@telconet.ec>
    * @version 1.6 01-09-2020 Se agrega seguridad para visualizar y acceder a la funcion
    * 
    * @author : Gustavo Narea <gnarea@telconet.ec>
    * @version 1.7 18-09-2020 Se parametriza el rango de la fecha de vencimiento de la tarjta.
    *
    * @author : Gustavo Narea <gnarea@telconet.ec>
    * @version 1.8 11-08-2021 Se envia el tipoCuentaId para correcta visualizacion inicial de bancos disponibles
    *
    * @author : Walther Joao Gaibor C <wgaibor@telconet.ec>
    * @version 1.9 03-12-2021 Se valida la forma de pago efectivo.
    *
    * @return a RedirectResponse to the given URL.
    */
    public function editAction($id)
    {
        $request   = $this->getRequest();
        $intIdPais = $request->getSession()->get('intIdPais');
        $strCodEmpresa = $request->getSession()->get('idEmpresa');
        $strPrefijoEmpresa = $request->getSession()->get('prefijoEmpresa');
        try
        {
            $em                = $this->getDoctrine()->getManager();
            $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
            $entity            = $em->getRepository('schemaBundle:InfoContrato')->find($id);
            $editForm          = $this->createForm(new InfoContratoType(array('validaFile' => true)), $entity);
            $deleteForm        = $this->createDeleteForm($id);
            $bancoTipoCuentaId = null;

            $arrayAdmiParametroCabAnio  = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
            ->findOneBy(array("nombreParametro" => "ANIO_VIGENCIA_TARJETA",
                              "estado"          => "Activo"));
            if(is_object($arrayAdmiParametroCabAnio))
            {
                $arrayParamDetAnios = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findBy(array("parametroId" => $arrayAdmiParametroCabAnio,
                                                            "estado"      => "Activo"));
                if ($arrayParamDetAnios)
                {
                    $intAnioVencimiento = $arrayParamDetAnios[0]->getValor1();
                }
            }

            $parametros = array(
                'entity'              => $entity,
                'edit_form'           => $editForm->createView(),
                'delete_form'         => $deleteForm->createView(),
                'prefijoEmpresa'      => $strPrefijoEmpresa,
                'clase'               => '',
                'formFormaPago'       => '',
                'strNumeroCtaTarjeta' => '',
                'bancoTipoCuentaId'   => '',
                'idper'               => '',
                'form_documentos'     => '');
        
            //si posee forma de pago dif de efectivo
            $no_existe = 0;
            if( $entity->getFormaPagoId()->getDescripcionFormaPago() != "Efectivo" )
            {
                //Busco por id y por estado -- falta por estado
                //$estado="Activo";
                $parametros['clase'] = "";
                if($strPrefijoEmpresa==='MD' || $strPrefijoEmpresa==='EN')
                {
                    $arrayTipoDocumentos = array();
                    $objTiposDocumentos  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')->findByEstado("Activo"); 
                    foreach ( $objTiposDocumentos as $objTiposDocumentos )
                    {
                        $strTipoDocumento       = $objTiposDocumentos->getDescripcionTipoDocumento();
                        $arrayAdmiParametroCab  = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                            ->findOneBy(array("nombreParametro" => "DOCUMENTOS_FORMAS_PAGO",
                                                                              "estado"          => "Activo"));
                        if(is_object($arrayAdmiParametroCab))
                        {
                            $arrayParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                           ->findBy(array("parametroId" => $arrayAdmiParametroCab,
                                                                          "estado"      => "Activo"));

                            if ($arrayParametroDet)
                            {
                                foreach ($arrayParametroDet as $objParametroDet) 
                                {
                                   if($strTipoDocumento == $objParametroDet->getValor2() )
                                   {
                                       $arrayTipoDocumentos[$objTiposDocumentos->getId()] = $strTipoDocumento;
                                   }
                                }
                            }
                        }
                    }

                    $objFormDocumentos            = $this->createForm(new InfoDocumentoType(array('validaFile'=>true,
                                                    'arrayTipoDocumentos'=>$arrayTipoDocumentos)), new InfoDocumento());
                    $objFormDocumentos            = $objFormDocumentos->createView();

                    $parametros['form_documentos'] = $objFormDocumentos;
                    $parametros['arrayTipoDocumentos' ] = $arrayTipoDocumentos;

                }                

                $objFormFormaPago = $em->getRepository('schemaBundle:InfoContratoFormaPago')
                                    ->findOneBy(array('contratoId' => $id,
                                                      'estado' => 'Activo'));
                if( $objFormFormaPago )
                {
                   
                    $bancoTipoCuentaId             = $objFormFormaPago->getBancoTipoCuentaId()->getId();
                    $intIdTipoCuenta               = $objFormFormaPago->getBancoTipoCuentaId()->getTipoCuentaId()->getId();
                    $parametros['intIdTipoCuenta'] = $intIdTipoCuenta;
                    //Descencripto el campo Numero_Cta_Tarjeta
                    /* @var $serviceCrypt \telconet\seguridadBundle\Service\CryptService */
                    $serviceCrypt        = $this->get('seguridad.Crypt');
                    $strNumeroCtaTarjeta = $serviceCrypt->descencriptar($objFormFormaPago->getNumeroCtaTarjeta());
                    
                    $serviceInfoContrato = $this->get('comercial.InfoContrato');
        
                    $arrayParametros     = array(
                                                 'strCodEmpresa'        => $strCodEmpresa,
                                                 'strNumeroCtaTarjeta'  => $strNumeroCtaTarjeta,
                                                );       
                    $parametros['strNumCtaTarj'] = $strNumeroCtaTarjeta;
                    $strNumeroCtaTarjeta  = $serviceInfoContrato->getNumeroTarjetaCtaEnmascarado($arrayParametros); 

                    if($strNumeroCtaTarjeta)
                    {
                        $parametros['strNumeroCtaTarjeta'] = $strNumeroCtaTarjeta;
                    }
                    else
                    {
                        throw new \Exception('No fue posible mostrar el Numero de Cuenta / Tarjeta - Contactese con el administrador del sistema.'); 
                    }
                    $objFormFormaPago->setNumeroCtaTarjeta($strNumeroCtaTarjeta);
                    $objPagoForm = $this->createForm(new InfoContratoFormaPagoEditType(
                                                                                array("intIdPais"=>$intIdPais,
                                                                                "intAnioVencimiento"=>$intAnioVencimiento)),
                                                                                $objFormFormaPago);
                    $parametros['formFormaPago'] = $objPagoForm->createView();             
                }
                else
                    $no_existe = 1;
            }
            else
            {               
                $parametros['clase'] = "campo-oculto";
                $no_existe = 1;
            }
            $objInfoContratoFormaPagoHist = $em->getRepository('schemaBundle:InfoContratoFormaPago')
                                               ->getUltimoHistorialFormaPago($id);            
            if(is_object($objInfoContratoFormaPagoHist))
            {
                $parametros['numeroActa'] = $objInfoContratoFormaPagoHist->getNumeroActa();
            }
            else
            {
                $parametros['numeroActa'] = '';
            }             
            $parametros['strDecripcionFormaPago'] = $entity->getFormaPagoId()->getDescripcionFormaPago();
            $parametros['bancoTipoCuentaId'] = $bancoTipoCuentaId;
            if($no_existe == 1)
            {
                $entityFormaPago = new InfoContratoFormaPago();
                $formInfoPago = $this->createForm(new InfoContratoFormaPagoEditType(array("intIdPais"=>$intIdPais)), $entityFormaPago);
                $parametros['formFormaPago'] = $formInfoPago->createView();
            }
            $parametros['idper'] = $entity->getPersonaEmpresaRolId()->getId();
            
            return $this->render('comercialBundle:infocontrato:edit.html.twig', $parametros);                 
        }
        catch (\Exception $e)
        {   
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());            
            return $this->render('comercialBundle:infocontrato:edit.html.twig', $parametros);     
        }
    }

   /**
    * Funcion que permite guardar la forma de pago editada del contrato del cliente  
    * Se agrega encriptacion del campo Numero_Cta_tarjeta 
    * Administrador de Contratos.  
    * @author : telcos
    * @author : apenaherrera
    * @author : eholguin
    * @param integer $id      
    * @param Request $request    
    * @version 1.1 02-12-2014
    * @version 1.2 13-02-2015
    * @version 1.3 26-06-2017 Se agrega registro de historial en la edición de las formas de pago del cliente que no son débito bancario.
    * 
    * Actualización: Se modifica envio de parametros en $arrayParametrosValidaCtaTarj a la función validarNumeroTarjetaCta
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.4 07-07-2017
    *
    *
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.5 01-04-2020 Se elimina ingreso de código de verificación para MD.
    *
    * 
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.6 24-04-2020 Se agrega validación para envío de número de cta-tarjeta desenmascarado en caso de no ser editado dicho campo.
    *
    * @author Angel Reina <areina@telconet.ec>
    * @version 1.7  11-05-2020 Se agrega validación para el cambio de forma de pago.
    *                         - Se agrega validación para guardar en la historial de 
    *                           cambio de forma de pago efectivo, cheque, recaudación,
    *                           tranferencia, cartera legal, cartera demanda, fideicomiso.
    *                         - Se agrega validación para guardar documentos digitales.
    * 
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.8  11-05-2020 Se agregan validaciones para empresa MD por ingreso de nuevos campos.
    *                         
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.9  26-06-2020 Se agrega validación al momento de inactivar registro a nivel de tabla info_contrato_forma_pago.
    *
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 2.0  28-07-2020 Se corrige validación para ingreso de cdóigo de verifiación.
    *
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 2.1  14-02-2022 Se agrega envío de parametro.strProcesoFacturacion (S-N) para indicar que proceso invoca la consulta de valores a 
    *                          facturar e insertar un log en la tabla DB_GENERAL.INFO_ERROR.
    * 
    * @return a RedirectResponse to the given URL.
    */	
    public function updateAction(Request $objRequest, $intId)
    {
        $objRequest                = $this->getRequest();
        $objSession                = $objRequest->getSession();
        $strUser                   = $objSession->get('user');
        $strCodEmpresa             = $objSession->get('idEmpresa');
        $intIdPersonEmpRolEmpl     = $objSession->get('idPersonaEmpresaRol');
        $intIdPersonEmpRolClt      = intVal($objRequest->get('personaEmpresaRolId')) ;
        $arrayPuntoSession         = $objSession->get('ptoCliente');
        $intIdPuntoSession         = (!empty($arrayPuntoSession['id'])) ? $arrayPuntoSession['id'] : -1; 
        $serviceInfoContrato       = $this->get('comercial.InfoContrato');
        //obtener el arreglo de los datos de formas de pago
        $arrayFormaPago            = $objRequest->get('infocontratoformapagotype');
        $arrayFormContrato         = $objRequest->get('infocontratotype');
        $strDatoBancario           = $objRequest->get('objDatoBancario');
        $arrayDatoBancario         = json_decode($strDatoBancario, true);
        $strParamBancario          = $objRequest->get('paramFormaPago');
        $boolActualizarBancarioMS  = false;
        $em                        = $this->getDoctrine()->getManager();
        $strError                  ='';        
        $strPrefijoEmpresa         = $objSession->get('prefijoEmpresa');
        $emGeneral                 = $this->getDoctrine()->getManager('telconet_general');
        $emComunicacion            = $this->getDoctrine()->getManager('telconet_comunicacion');
        if($strPrefijoEmpresa === "MD" || $strPrefijoEmpresa === "EN" )
        {    
            $arrayDatosFormFiles       = $objRequest->files->get('infodocumentotype');
            $arrayDatosFormTipos       = $objRequest->get('infodocumentotype'); 
            $intNumeroActa             = $objRequest->get('numeroActa');        
            $intMotivoId               = $objRequest->get('motivoId');
            $strFactura                = 'N';
            $strObservacion            = 'NO POR MOTIVO';
            $boolAplica                = false;
            $strMotivo                 = "";
            
        }        
        $em->getConnection()->beginTransaction();       

        try
        {
            $entity                  = $em->getRepository('schemaBundle:InfoContrato')->find($intId);
            $idFormaPago             = $entity->getFormaPagoId();
            $entityFormaPago         = $em->getRepository('schemaBundle:AdmiFormaPago')->find($arrayFormContrato['formaPagoId']);
            $entityFormaPagoAnterior = $em->getRepository('schemaBundle:AdmiFormaPago')->find($idFormaPago->getId());
            $entity->setFormaPagoId($entityFormaPago);
            $em->persist($entity);
            $em->flush();
            $strFormaPagoActual    =  strtoupper($entityFormaPago->getDescripcionFormaPago());
            $strFormaPagoAnterior  =  strtoupper($entityFormaPagoAnterior->getDescripcionFormaPago());            
            if($strPrefijoEmpresa === "MD" || $strPrefijoEmpresa === "EN" )
            {
                if(strtoupper($entityFormaPago->getDescripcionFormaPago()) == "DEBITO BANCARIO" &&
                    isset($arrayDatoBancario) && !empty($arrayDatoBancario) && $strParamBancario == "S")
                {
                    $arrayFormaPago['tipoCuentaId']         = $arrayDatoBancario['tipoCuentaId'];
                    $arrayFormaPago['formaPagoId']          = $arrayDatoBancario['formaPagoId'];
                    $arrayFormaPago['bancoTipoCuentaId']    = $arrayDatoBancario['bancoTipoCuentaId'];
                    $arrayFormaPago['numeroCtaTarjeta']     = $arrayDatoBancario['numeroCuenta'];
                    $arrayFormaPago['titularCuenta']        = $arrayDatoBancario['titular'];
                    $arrayParamTarjeta                      = array('valor'         =>  $arrayDatoBancario['numeroCuenta'],
                                                                    'usrCreacion'   =>  $strUser,
                                                                    'ipCreacion'    =>  '127.0.0.1',
                                                                    );
                    /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
                    $serviceInfoPunto   = $this->get('comercial.InfoPunto');
                    $arrayConsulta      = $serviceInfoPunto->getDescifrarTarjeta($arrayParamTarjeta);
                    if(isset($arrayConsulta['status']) && !empty($arrayConsulta['status']) && $arrayConsulta['status'] == 'OK')
                    {
                        $arrayFormaPago['numeroCtaTarjeta']     = $arrayConsulta['data'];
                    }
                    else
                    {
                        throw new \Exception("No se pudo desincriptar la tarjeta  - " . $arrayConsulta['message']);
                    }
                    $arrayFormaPago['mesVencimiento']       = intval($arrayDatoBancario['mes']);
                    $arrayFormaPago['anioVencimiento']      = $arrayDatoBancario['anio'];
                    $boolActualizarBancarioMS               = true;
                }
                $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array('nombreParametro' => 'CAMBIO FORMA PAGO', 
                                                                  'estado'          => 'Activo'));
                if(is_object($objAdmiParametroCab))
                {              
                    $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                     ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                       'descripcion' => 'NOTIFICACION SMS',
                                                                       'valor1'      => 'SI',
                                                                       'empresaCod'  => $strCodEmpresa,
                                                                       'estado'      => 'Activo'));
                    if(is_object($objAdmiParametroDet))
                    {
                        $strTextSms = $objAdmiParametroDet->getValor2();
                    }
                    
                    $objAdmiParametroDetTarea = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                            'descripcion' => 'DESCRIPCION TAREA',
                                                                            'empresaCod'  => $strCodEmpresa,
                                                                            'estado'      => 'Activo'));
                    if(is_object($objAdmiParametroDetTarea) && isset($intNumeroActa))
                    {                    
                        $strObservacion        = $objAdmiParametroDetTarea->getValor1()
                                                 .$strFormaPagoActual.$objAdmiParametroDetTarea->getValor2() . $intNumeroActa
                                                 .$objAdmiParametroDetTarea->getValor3();
                    }
                    else
                    {
                        $strObservacion        = $objAdmiParametroDetTarea->getValor1()
                                                 .$strFormaPagoActual
                                                 .$objAdmiParametroDetTarea->getValor3();                        
                    }
                }                
              
                $entityAdmiMotivo = $em->getRepository('schemaBundle:AdmiMotivo')->find($intMotivoId);
                if(is_object($entityAdmiMotivo))
                {
                    $strMotivo = $entityAdmiMotivo->getNombreMotivo();                
                }
                $arrayMotivoFactura = $em->getRepository('schemaBundle:AdmiParametroCab')->getMotivosCambioFormaPago();
                if (in_array($strMotivo, $arrayMotivoFactura))
                {
                    $boolAplica = true;
                    $strFactura = 'S';  
                }                
            }
          
            if(strtoupper($entityFormaPago->getDescripcionFormaPago()) == "DEBITO BANCARIO")
            {
                if(($strPrefijoEmpresa == "MD" || $strPrefijoEmpresa == "EN") && !(is_numeric($arrayFormaPago['numeroCtaTarjeta'])))
                {
                    $serviceCrypt             = $this->get('seguridad.Crypt');
                    $objInfoContratoFormaPago = $em->getRepository('schemaBundle:InfoContratoFormaPago')->findOneByContratoId($intId);
                    if(is_object($objInfoContratoFormaPago))
                    {
                        $arrayFormaPago['numeroCtaTarjeta'] = $serviceCrypt->descencriptar($objInfoContratoFormaPago->getNumeroCtaTarjeta());
                    }
                }                

                $arrayParametrosValidaCtaTarj                          = array();

                $arrayParametrosValidaCtaTarj['intTipoCuentaId']       = $arrayFormaPago['tipoCuentaId'];
                $arrayParametrosValidaCtaTarj['intBancoTipoCuentaId']  = $arrayFormaPago['bancoTipoCuentaId'];
                $arrayParametrosValidaCtaTarj['strNumeroCtaTarjeta']   = $arrayFormaPago['numeroCtaTarjeta'];
                $arrayParametrosValidaCtaTarj['intFormaPagoId']        = $idFormaPago->getId();
                if(($strPrefijoEmpresa !== "MD" && $strPrefijoEmpresa !== "EN" ))
                {
                    $arrayParametrosValidaCtaTarj['strCodigoVerificacion'] = $arrayFormaPago['codigoVerificacion'];
                }

                $arrayParametrosValidaCtaTarj['strCodEmpresa']         = $strCodEmpresa;
                $arrayValidaciones = $serviceInfoContrato->validarNumeroTarjetaCta($arrayParametrosValidaCtaTarj);                
                if($arrayValidaciones)
                {
                    foreach($arrayValidaciones as $key => $mensaje_validaciones)
                    {
                        foreach($mensaje_validaciones as $key_msj => $value)
                        {
                            $strError = $strError . $value . ".\n";
                        }
                    }
                    throw new \Exception("No se pudo Editar la forma de pago   - " . $strError);
                }
            }            
            $entityContratoFormaPago = $em->getRepository('schemaBundle:InfoContratoFormaPago')
                                           ->findOneBy(array("contratoId" => $entity->getId()));
            if(is_object($entityContratoFormaPago))
            {
                $arrayParametrosFormapagoHist                                 = array();
                $arrayParametrosFormapagoHist["entityinfoContratoFormaPago"]  = $entityContratoFormaPago;
                $arrayParametrosFormapagoHist["user"]                         = $strUser;
                $arrayParametrosFormapagoHist["strIp"]                        = $objRequest->getClientIp();
                $arrayParametrosFormapagoHist["entityContrato"]               = $em;
                $arrayParametrosFormapagoHist["idFormaPago"]                  = $idFormaPago;
                $arrayParametrosFormapagoHist["strPrefijoEmpresa"]            = $strPrefijoEmpresa;               
                if($strPrefijoEmpresa === "MD" || $strPrefijoEmpresa === "EN" )
                {
                    $arrayParametrosFormapagoHist["intMotivo"]                = $intMotivoId;
                    if(isset($intNumeroActa))
                    {
                        $arrayParametrosFormapagoHist["intNumeroActa"]            = $intNumeroActa;
                    }
                    $arrayParametrosFormapagoHist["strFactura"]               = $strFactura;
                    $arrayParametrosFormapagoHist['arrayDatosFormFiles']      = $arrayDatosFormFiles;
                    $arrayParametrosFormapagoHist['arrayDatosFormTipos']      = $arrayDatosFormTipos;
                    $arrayParametrosFormapagoHist["strObservacion"]           = $strObservacion;                        
                }
                $arrayParametrosFormapagoHist["intFormaPagoActualId"]         = $arrayFormContrato['formaPagoId'];
                $arrayParametrosFormapagoHist['strCodEmpresa']                = $strCodEmpresa;
                $arrayParametrosFormapagoHist['intNumeroContrato']            = $entity->getId();
                $arrayParametrosFormapagoHist['strUsrCreacion']               = $strUser;
                $arrayParametrosFormapagoHist['strClientIp']                  = $objRequest->getClientIp();
                if($boolAplica && ($strPrefijoEmpresa === "MD" || $strPrefijoEmpresa === "EN"))
                {
                    $arrayParams = array( 'strEmpresaCod'        => $strCodEmpresa,
                                          'strUsrCreacion'       => $strUser,
                                          'intIdContrato'        => $intId,
                                          'intIdMotivo'          => $intMotivoId,
                                          'strIpCliente'         => $objRequest->getClientIp(),
                                          'intFormaPagoId'       => $arrayFormContrato['formaPagoId'],
                                          'intTipoCuentaId'      => $arrayFormaPago['tipoCuentaId'],
                                          'intBancoTipoCuentaId' => $arrayFormaPago['bancoTipoCuentaId'],
                                          'strEmpresaCod'        => $strCodEmpresa
                                       );

                    $intPorcentajeDctoInstalacion  = $serviceInfoContrato->getPorcentajeDctoInstDestino($arrayParams);
                    if(isset($intPorcentajeDctoInstalacion) && $intPorcentajeDctoInstalacion > 0)
                    {
                       $intPorcentajeDctoInstalacion = ($intPorcentajeDctoInstalacion*100);
                    }
                    else
                    {
                        $intPorcentajeDctoInstalacion = 0;
                    }

                    $arrayParametrosFormapagoHist['intPorcentajeDctoInst'] = $intPorcentajeDctoInstalacion;
                }
                $this->guardarContratoFormaPagoHist($arrayParametrosFormapagoHist);
            }            
            else
            {
                $entityInfoContratoFormaPagoHist = new InfoContratoFormaPagoHist();
                $entityInfoContratoFormaPagoHist->setContratoId($entity);
                $entityInfoContratoFormaPagoHist->setEstado('Inactivo');
                $entityInfoContratoFormaPagoHist->setFeCreacion(new \DateTime('now'));
                $entityInfoContratoFormaPagoHist->setFeUltMod(new \DateTime('now'));
                $entityInfoContratoFormaPagoHist->setIpCreacion($objRequest->getClientIp());
                $entityInfoContratoFormaPagoHist->setUsrCreacion($strUser);

                if($idFormaPago != null) 
                {
                    $entityInfoContratoFormaPagoHist->setFormaPago($idFormaPago->getId());
                }
                if($strPrefijoEmpresa === "MD" || $strPrefijoEmpresa === "EN" )
                {
                    $entityInfoContratoFormaPagoHist->setMotivoId($intMotivoId);
                    if(isset($intNumeroActa))
                    {                    
                        $entityInfoContratoFormaPagoHist->setNumeroActa($intNumeroActa);
                    }
                    $entityInfoContratoFormaPagoHist->setFormaPagoActualId($entityFormaPago->getId());
                    $entityInfoContratoFormaPagoHist->setFactura($strFactura);
                    $entityInfoContratoFormaPagoHist->setObservacion($strObservacion);
                }
                $em->persist($entityInfoContratoFormaPagoHist);
                $em->flush();
                if(($strPrefijoEmpresa === "MD"  || $strPrefijoEmpresa === "EN")&& $strFormaPagoActual === 'DEBITO BANCARIO')
                {
                    $arrayParametrosDocCambioFormaPago['strCodEmpresa']        = $strCodEmpresa;
                    $arrayParametrosDocCambioFormaPago['intNumeroContrato']    = $entity->getId();
                    $arrayParametrosDocCambioFormaPago['strUsrCreacion']       = $strUser;
                    $arrayParametrosDocCambioFormaPago['arrayDatosFormFiles']  = $arrayDatosFormFiles;
                    $arrayParametrosDocCambioFormaPago['arrayDatosFormTipos']  = $arrayDatosFormTipos;
                    $arrayParametrosDocCambioFormaPago['strClientIp']          = $objRequest->getClientIp();
                    $arrayParametrosDocCambioFormaPago['intPagoDatosId']       = $entityInfoContratoFormaPagoHist->getId();
                    /*Llama a la función que guarda los documentos digitales por cambio de forma de pago */
                    $objInfoContrato = $serviceInfoContrato->guardarDocumentosDigitales($arrayParametrosDocCambioFormaPago);
                    if( !(is_object($objInfoContrato)) )
                    {
                        throw new \Exception("Hubo un error al guardar documento asociado a la forma de pago del contrato, "
                                . "           verificar con el departamento de sistemas.");
                    }
                }                  
            }
            if(($entityContratoFormaPago) &&
                (strtoupper($entityFormaPago->getDescripcionFormaPago()) == "DEBITO BANCARIO"))
            {

                $entityAdmiBancoTipoCuenta = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                                                                ->find($arrayFormaPago['bancoTipoCuentaId']);
                $entityAdmiBanco = $em->getRepository('schemaBundle:AdmiBanco')->find($entityAdmiBancoTipoCuenta->getBancoId());
                $entityContratoFormaPago->setBancoTipoCuentaId($entityAdmiBancoTipoCuenta);
                $entityAdmiTipoCuenta = $em->getRepository('schemaBundle:AdmiTipoCuenta')->find($arrayFormaPago['tipoCuentaId']);
                $entityContratoFormaPago->setTipoCuentaId($entityAdmiTipoCuenta);
                if(is_object($entityAdmiTipoCuenta) && is_object($entityAdmiBanco))
                {
                    $strFormaPagoActual .= '-'.$entityAdmiTipoCuenta->getDescripcionCuenta().'-'.$entityAdmiBanco->getDescripcionBanco();
                    if(is_object($objAdmiParametroDetTarea))
                    {
                        if(isset($intNumeroActa))
                        {
                            $strObservacion = $objAdmiParametroDetTarea->getValor1().$strFormaPagoActual.$objAdmiParametroDetTarea->getValor2() 
                                            . $intNumeroActa.$objAdmiParametroDetTarea->getValor3();
                        }
                        else
                        {
                            $strObservacion = $objAdmiParametroDetTarea->getValor1().$strFormaPagoActual.$objAdmiParametroDetTarea->getValor3();
                        }
                    }
                }
                
                if(!$arrayFormaPago['numeroCtaTarjeta'])
                {
                    throw new \Exception('No se pudo Editar la forma de pago - El Numero de Cuenta / Tarjeta es un campo obligatorio'); 
                }  
                if($entityAdmiBancoTipoCuenta->getEsTarjeta() == 'S')
                {
                    if(!$arrayFormaPago['mesVencimiento'] || !$arrayFormaPago['anioVencimiento'])
                    {
                        throw new \Exception('No se pudo Editar la forma de pago - El Anio y mes de Vencimiento de la tarjeta son campos obligatorios'); 
                    }  

                    if(!$arrayFormaPago['codigoVerificacion']  && ($strPrefijoEmpresa !== "MD" && $strPrefijoEmpresa !== "EN" ))
                    {
                        throw new \Exception('No se pudo Editar la forma de pago - El codigo de verificacion de la tarjeta es un campo obligatorio'); 
                    }  
                }
                //Llamo a funcion que realiza encriptado del numero de cuenta                
                /* @var $serviceCrypt \telconet\seguridadBundle\Service\CryptService */
                $serviceCrypt        = $this->get('seguridad.Crypt');                
                $strNumeroCtaTarjeta = $serviceCrypt->encriptar($arrayFormaPago['numeroCtaTarjeta']);
                if( $strNumeroCtaTarjeta )
                {
                    $entityContratoFormaPago->setNumeroCtaTarjeta($strNumeroCtaTarjeta);
                }
                else
                {
                    throw new \Exception('No se pudo Editar la forma de pago, no fue posible guardar el numero de cuenta/tarjeta'
                            .$arrayFormaPago['numeroCtaTarjeta'].' - Contactese con el administrador del sistema.'); 
                } 
                $entityContratoFormaPago->setTitularCuenta($arrayFormaPago['titularCuenta']);
                if($entityAdmiBancoTipoCuenta->getEsTarjeta() == 'S')
                {

                    $entityContratoFormaPago->setMesVencimiento($arrayFormaPago['mesVencimiento']);
                    $entityContratoFormaPago->setAnioVencimiento($arrayFormaPago['anioVencimiento']);
                    if(($strPrefijoEmpresa !== "MD"  && $strPrefijoEmpresa !== "EN" )&& isset($arrayFormaPago['codigoVerificacion']))
                    {
                        $entityContratoFormaPago->setCodigoVerificacion($arrayFormaPago['codigoVerificacion']);
                    }
                }
                else
                {
                    $entityContratoFormaPago->setMesVencimiento(null);
                    $entityContratoFormaPago->setAnioVencimiento(null);
                    $entityContratoFormaPago->setCodigoVerificacion(null);
                }
                $entityContratoFormaPago->setEstado("Activo");
                $entityContratoFormaPago->setUsrUltMod($strUser);
                $entityContratoFormaPago->setFeUltMod(new \DateTime('now'));
                $em->persist($entityContratoFormaPago);
                $em->flush();
            }
            elseif(is_object($entityContratoFormaPago) && 
                ((strtoupper($entityFormaPago->getDescripcionFormaPago()) == "EFECTIVO") || 
                (strtoupper($entityFormaPago->getDescripcionFormaPago()) == "CHEQUE") || 
                (strtoupper($entityFormaPago->getDescripcionFormaPago()) == "RECAUDACION")) || 
                (is_object($entityContratoFormaPago) && ($strPrefijoEmpresa == "MD" || $strPrefijoEmpresa == "EN"  )  &&  
                (strtoupper($entityFormaPago->getDescripcionFormaPago()) !== "DEBITO BANCARIO"))
                )
            {
                $entityContratoFormaPago->setEstado("Inactivo");
                $entityContratoFormaPago->setUsrUltMod($strUser);
                $entityContratoFormaPago->setFeUltMod(new \DateTime('now'));
                $em->persist($entityContratoFormaPago);
                $em->flush();
            }
            elseif((!$entityContratoFormaPago) &&
                (strtoupper($entityFormaPago->getDescripcionFormaPago()) == "DEBITO BANCARIO"))
            {
                $entityContratoFormaPago = new InfoContratoFormaPago();
                $entityContratoFormaPago->setContratoId($entity);
                $entityAdmiBancoTipoCuenta = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($arrayFormaPago['bancoTipoCuentaId']);
                $entityContratoFormaPago->setBancoTipoCuentaId($entityAdmiBancoTipoCuenta);
                $entityAdmiTipoCuenta = $em->getRepository('schemaBundle:AdmiTipoCuenta')->find($arrayFormaPago['tipoCuentaId']);
                $entityContratoFormaPago->setTipoCuentaId($entityAdmiTipoCuenta);
                
                if(!$arrayFormaPago['numeroCtaTarjeta'])
                {
                    throw new \Exception('El Numero de Cuenta / Tarjeta es un campo obligatorio - No se pudo Editar la forma de pago'); 
                }
                if($entityAdmiBancoTipoCuenta->getEsTarjeta() == 'S')
                {
                    if(!$arrayFormaPago['mesVencimiento'] || !$arrayFormaPago['anioVencimiento'])
                    {
                        throw new \Exception('No se pudo Editar la forma de pago - El Anio y mes de Vencimiento de la tarjeta es un campo obligatorio'); 
                    }  

                    if(($strPrefijoEmpresa !== "MD" && $strPrefijoEmpresa !== "EN" )&& !$arrayFormaPago['codigoVerificacion'])
                    {
                        throw new \Exception('No se pudo Editar la forma de pago - El codigo de verificacion de la tarjeta es un campo obligatorio'); 
                    }  
                }
                //Llamo a funcion que realiza encriptado del numero de cuenta                
                /* @var $serviceCrypt \telconet\seguridadBundle\Service\CryptService */
                $serviceCrypt        = $this->get('seguridad.Crypt');                
                $strNumeroCtaTarjeta = $serviceCrypt->encriptar($arrayFormaPago['numeroCtaTarjeta']);
                if( $strNumeroCtaTarjeta )
                {
                    $entityContratoFormaPago->setNumeroCtaTarjeta($strNumeroCtaTarjeta);
                }     
                else
                {
                    throw new \Exception('No se pudo Editar la forma de pago, no fue posible guardar el numero de cuenta/tarjeta'
                            .$arrayFormaPago['numeroCtaTarjeta'].' - Contactese con el administrador del sistema.'); 
                } 
                $entityContratoFormaPago->setTitularCuenta($arrayFormaPago['titularCuenta']);
                if($entityAdmiBancoTipoCuenta->getEsTarjeta() == 'S')
                {
                    $entityContratoFormaPago->setMesVencimiento($arrayFormaPago['mesVencimiento']);
                    $entityContratoFormaPago->setAnioVencimiento($arrayFormaPago['anioVencimiento']);
                    if($strPrefijoEmpresa !== "MD" && $strPrefijoEmpresa !== "EN"  )
                    {
                        $entityContratoFormaPago->setCodigoVerificacion($arrayFormaPago['codigoVerificacion']);
                    }
                }
                else
                {
                    $entityContratoFormaPago->setMesVencimiento(null);
                    $entityContratoFormaPago->setAnioVencimiento(null);
                    $entityContratoFormaPago->setCodigoVerificacion(null);
                }
                $entityContratoFormaPago->setEstado("Activo");
                $entityContratoFormaPago->setUsrCreacion($strUser);
                $entityContratoFormaPago->setFeCreacion(new \DateTime('now'));
                $em->persist($entityContratoFormaPago);
                $em->flush();
            }
            if ($strPrefijoEmpresa === "TN")
            {
                $em->getConnection()->commit();
                $em->getConnection()->close();
            }
            
            if ($strPrefijoEmpresa === "MD" || $strPrefijoEmpresa === "EN")
            {
                $arrayDestinatarios     = []; 
                      
                $arrayParamsFacturarion = array( 'strEmpresaCod'        => $strCodEmpresa,
                                                 'strUsrCreacion'       => $strUser,
                                                 'intIdContrato'        => $intId,
                                                 'intIdMotivo'          => $intMotivoId,
                                                 'strIpCliente'         => $objRequest->getClientIp(),
                                                 'intFormaPagoId'       => $arrayFormContrato['formaPagoId'],
                                                 'intTipoCuentaId'      => $arrayFormaPago['tipoCuentaId'],
                                                 'intBancoTipoCuentaId' => $arrayFormaPago['bancoTipoCuentaId']
                                               );
                if ($boolAplica)
                {
                    $arrayParamsFacturarion['strProcesoFacturacion'] = 'S';
                    $strRespuesta = $serviceInfoContrato->ejecutarFacturacionCambioFormaPago($arrayParamsFacturarion);
                    if($strRespuesta === 'OK')
                    {
                        $strObservacion .= $objAdmiParametroDetTarea->getValor4();
                    }
                }
                $objInfoPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                               ->find($objSession->get('idPersonaEmpresaRol'));
                if(is_object($objInfoPersonaEmpresaRol))
                {
                    $strValorFormaContacto = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                ->getValorFormaContactoPorCodigo($objInfoPersonaEmpresaRol
                                                ->getPersonaId(),'MAIL');
                    if(!is_null($strValorFormaContacto))
                    {
                        $strEmailUsrSesion = strtolower($strValorFormaContacto);
                        if(isset($strEmailUsrSesion))
                        {
                            $arrayDestinatarios[] = $strEmailUsrSesion ;
                        }                        
                    }                
                }

                $em->getConnection()->commit();
                $em->getConnection()->close();
                if ($boolAplica)
                {
                    $arrayParametrosSms                           = array();
                    $arrayParametrosSms["intIdPersonaEmpresaRol"] = $intIdPersonEmpRolEmpl;
                    $arrayParametrosSms["intCodEmpresa"]          = intVal($strCodEmpresa);
                    $arrayParametrosSms["strUser"]                = $strUser; 
                    $arrayParametrosSms['strMensaje']             = $strTextSms;
                    $this->envioSMSCambioFormaPago($arrayParametrosSms);                
                    $arrayParametrosSmsCl                           = array();
                    $arrayParametrosSmsCl["intIdPersonaEmpresaRol"] = $intIdPersonEmpRolClt;
                    $arrayParametrosSmsCl["intCodEmpresa"]          = intVal($strCodEmpresa);
                    $arrayParametrosSmsCl["strUser"]                = $strUser; 
                    $arrayParametrosSmsCl['strMensaje']             = $strTextSms;
                    $this->envioSMSCambioFormaPago($arrayParametrosSmsCl);
                    
                           
                    $arrayParametrosMail = array('intIdPuntod'        => $intIdPuntoSession,
                                                 'strCodEmpresa'      => $strCodEmpresa ,
                                                 'strParametro'       => 'CAMB_FORMPAG_HEADERS',
                                                 'strModulo'          => 'FINANCIERO',
                                                 'strCodigoPlantilla' => 'CFP_CLT',
                                                 'strClientIp'        => $objRequest->getClientIp(),
                                                 'strMensaje'         => $strTextSms
                                                );
                    $serviceFinanciero = $this->get('financiero.InfoDocumentoFinancieroCab');
                    
                    $strRespuestaMail  = $serviceFinanciero->sendEmailClienteByParametros($arrayParametrosMail);
                    if($strRespuestaMail == 'OK')
                    {
                        $arrayParametros["strRespuestaMail"] = $strRespuestaMail;
                    }                  
                }             
                $arrayTo        = array();
                $arrayTo[]      = $strEmailUsrSesion;
                $arrayParametros["strNombreTarea"]         = "Cambiar forma de pago";
                $arrayParametros["strNombreProceso"]       = "PROCESOS TAREAS ATC";
                $arrayParametros["intIdCaso"]              = null;
                $arrayParametros["intIdPersonaEmpresaRol"] = $intIdPersonEmpRolEmpl;
                $arrayParametros["arrayTo"]                = $arrayTo;  
                $arrayParametros["strObservacionTarea"]    = $strObservacion;
                $arrayParametros["objDetalleHipotesis"]    = $strObservacion;
                $arrayParametros["strTipoTarea"]           = "T";
                $arrayParametros["strTareaRapida"]         = "S";
                $arrayParametros["boolAsignarTarea"]       = true ;
                $arrayParametros["strUserCreacion"]        = $strUser;
                $arrayParametros["strIpCreacion"]          = $objRequest->getClientIp();
                $arrayParametros["strTipoAsignacion"]      = "empleado";
                $arrayParametros["intAsignarTareaPersona"] = $intIdPersonEmpRolEmpl;
                $arrayParametros["intIdEmpresa"]           = $strCodEmpresa;
                $arrayParametros["intPuntoId"]             = $intIdPuntoSession;
                $arrayParametros['intFormaContacto']       = 5;
                if(is_object($objAdmiParametroDetTarea))
                {  
                    $arrayParametros['strObsAsignaTarea']      = $objAdmiParametroDetTarea->getDescripcion();
                    $arrayParametros['strObsHistorial']        = $objAdmiParametroDetTarea->getValor5();
                    $arrayParametros['strObsSeguimiento']      = $objAdmiParametroDetTarea->getValor6();
                }
                
                $serviceSoporte = $this->get('soporte.SoporteService');
                $arrayRespuesta = $serviceSoporte->crearTareaCasoSoporte($arrayParametros);
                if (!empty($arrayRespuesta))
                {
                    $serviceProceso    = $this->get('soporte.ProcesoService');
                    $arrayDocsContrato = $emComunicacion->getRepository('schemaBundle:InfoDocumento')
		                                                ->findBy(array( "contratoId" => $entity->getId(), "usrCreacion" => $strUser));

                    foreach($arrayDocsContrato as $objInfoDocumento):
                        $strPathTelcos  = $this->container->getParameter('path_telcos');
                        $strRutaFile    = $objInfoDocumento->getUbicacionFisicaDocumento();
                        $strRutaArchivo = $strPathTelcos.$strRutaFile;
                        if(file_exists($strRutaArchivo))
                        {
                            $strFileBase64 = base64_encode(file_get_contents($strRutaArchivo));
                        }           
                        $serviceProceso->putFile(array ('strFileBase64'      => $strFileBase64,
                                                        'strFileName'        => "Tarea_".$arrayRespuesta["numeroTarea"],
                                                        'strFileExtension'   => $objInfoDocumento->getExtension(),
                                                        'intNumeroTarea'     => $arrayRespuesta["numeroTarea"],
                                                        'strNumeroCaso'      => null,
                                                        'strOrigen'          => 'T',
                                                        'strPrefijoEmpresa'  => $strPrefijoEmpresa,
                                                        'boolIsInFileServer' => false,
                                                        'strUsuario'         => $strUser ? $strUser : 'Telcos+',
                                                        'strIp'              => $objRequest->getClientIp()? $objRequest->getClientIp():'127.0.0.1')); 
                    endforeach;
                }
                if($boolActualizarBancarioMS)
                {
                    $arrayParamClausula = array(
                                                'puntoId'           => $intIdPuntoSession,
                                                'estado'            => 'Activo',
                                                'empresaCod'        => $strCodEmpresa,
                                                'usrCreacion'       => $strUser ? $strUser : 'Telcos+',
                                                'ipCreacion'        => $objRequest->getClientIp()? $objRequest->getClientIp():'127.0.0.1'
                                                );

                    /* @var $serviceContrato \telconet\comercialBundle\Service\InfoContratoService */
                    $serviceContrato     = $this->get('comercial.InfoContrato');
                    $arrayResultado      = $serviceContrato->actualizarEstadoClausula($arrayParamClausula);
                }
                $this->get('session')->getFlashBag()->add('success', "Cambio de Forma de pago realizado con éxito. Se crea la tarea: " 
                                                                   . $arrayRespuesta["numeroTarea"]);
            }
            return $this->redirect($this->generateUrl('infocontrato_show', array('id' => $intId)));
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->redirect($this->generateUrl('infocontrato_show', array('id' => $intId)));
        }
    }

    /**   
     * Documentación para el método 'guardarDocumentosCambioFormaPago'.
     *
     * Método que llama a la función en el service para guardar los documentos digitales asociados al cambio de forma de pago.
     * @param array    $arrayParametros 
     * 
     * @author Angel Reina <areina@telconet.ec>
     * @version 1.0 19-07-2019
     * 
     */       
    public function guardarDocumentosCambioFormaPago($arrayParametros)
    {
            /*Llama a la función que guarda los documentos digitales por cambio de forma de pago */
            $serviceInfoContrato = $this->get('comercial.InfoContrato');
            $serviceInfoContrato->guardarDocumentosDigitales($arrayParametros);
        
    }


    /**   
     * Documentación para el método 'envioSMSCambioFormaPago'.
     *
     * Método para envío de SMS por cambio de forma de pago
     * @param array    $arrayParametros 
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 08-09-2019
     * 
     */       
    public function envioSMSCambioFormaPago($arrayParametros)
    {
        $emSoporte                  = $this->getDoctrine()->getManager('telconet_soporte');
        $emComercial                = $this->getDoctrine()->getManager();
        $intIdPersonaEmpresaRol     = $arrayParametros["intIdPersonaEmpresaRol"];
        $intCodEmpresa              = $arrayParametros["intCodEmpresa"];
        $strUser                    = $arrayParametros["strUser"];
        $strMensaje                 = $arrayParametros["strMensaje"];
        $strProceso                 = "CAMBIO FORMA PAGO";      
        /*Llama a la función que guarda los documentos digitales por cambio de forma de pago */
        $serviceInfoContrato      = $this->get('comercial.InfoContrato');           
        $objInfoPersonaEmpresaRol = $emSoporte->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonaEmpresaRol);
        
        if(is_object($objInfoPersonaEmpresaRol))
        {
            $intPersonaId                =  $objInfoPersonaEmpresaRol->getPersonaId()->getId();
            $arrayPersonaFormaContacto   = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                       ->getResultadoFormasContactosPorPersona($intCodEmpresa, $intPersonaId);
            foreach ($arrayPersonaFormaContacto as $entityFormasContacto)
            {
                $strDescripcionFormaContacto = $entityFormasContacto->getFormaContactoId()->getDescripcionFormaContacto();
                $arrayBusqueda = array("Telefono Movil","Telefono Movil Claro", "Telefono Movil Movistar", "Telefono Movil CNT");

                if(in_array($strDescripcionFormaContacto, $arrayBusqueda))
                {
                    //Proceso notificación por sms

                    $arrayParametrosSms['strMensaje']       = $strMensaje;
                    $arrayParametrosSms['strNumeroTlf']     = $entityFormasContacto->getValor();
                    $arrayParametrosSms['strUsername']      = $strUser;
                    $arrayParametrosSms['strCodEmpresa']    = strVal($intCodEmpresa);
                    $arrayParametrosSms['strProceso']       = $strProceso;
                    $arrayResultado = $serviceInfoContrato->envioSMS($arrayParametrosSms) ;
                    return $arrayResultado;
                }
                                
            }
        }
    }
    
    
    
    /**
     * Deletes a InfoContrato entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('schemaBundle:InfoContrato')->find($id);
            $entity->setEstado("Inactivo");
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoContrato entity.');
            }
            //$em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('infocontrato'));
    }
    
    public function deleteAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
        $parametro = $peticion->get('id');
        $arrayValor = explode("|",$parametro);
        //print_r($arrayValor);
        $em = $this->getDoctrine()->getManager();
        foreach($arrayValor as $id):
            //echo $id;
            $entity=$em->getRepository('schemaBundle:InfoContrato')->find($id);
            if($entity){
                $entity->setEstado("Inactivo");
                $em->persist($entity);
                $em->flush();
                $respuesta->setContent("Se elimino la entidad");
            }
            else
                $respuesta->setContent("No existe el registro");
        endforeach;
        return $respuesta;
    }

    private function createDeleteForm($id)
    {
        
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    public function listarClausulasAction()
    {
        $request = $this->getRequest();
        $tipoContratoId=$request->request->get("tipoContratoId");
        $em = $this->getDoctrine()->getManager('telconet');
        $listado_clausulas = $em->getRepository('schemaBundle:AdmiClausulaContrato')->findByTipoContratoId($tipoContratoId);
        $i=0;
        if($listado_clausulas)
        {
            $presentacion_div="<table>";
            foreach ($listado_clausulas as $clausula):
                $i++;
                $presentacion_div.="<tr><td>";
                $presentacion_div.="<input type='checkbox' name='check[]' value='".$clausula->getId()."' checked>";
                $presentacion_div.="</td><td>";
                $presentacion_div.="<textarea name='clausula[]' style='width: 800px; height: 70px;'>".$clausula->getDescripcionClausula()."</textarea>";
                $presentacion_div.="</td></tr>";
            endforeach;
            $presentacion_div.="</table>";
            $arreglo=array('msg'=>'ok','div'=>$presentacion_div);
        }
        else
        {
            $arreglo=array('msg'=>'No existen clausulas para ese tipo de contrato');
        }
        
        $response = new Response(json_encode($arreglo));
        $response->headers->set('Content-type', 'text/json');
        return $response;		
    }
    /**
     * Documentación para el método 'gridAction'.
     * Obtiene informacion de los contratos  segun criterios de busqueda   
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.1 24-03-2016  
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.2 22-06-2021 - Se agrega la opción de poder terminar el proceso
     *                           del contrato si esta autorizado 
     *  
     * @return response       
     */     
    public function gridAction()
    {
        $request        = $this->get('request');
        $session        = $request->getSession();
        $cliente        = $session->get('cliente');
        $ptocliente     = $session->get('ptoCliente');
        $intIdEmpresa   = $session->get('idEmpresa');
        $filter         = $request->request->get("filter");
        $estado_post    = $filter['filters'][0]['value'];
        $strEstado      = '';
        $fechaDesde     = explode('T', $request->get("fechaDesde"));
        $fechaHasta     = explode('T', $request->get("fechaHasta"));
        $intIdOficina   = $request->get("idOficina");
        $strEstado      = $request->get("estado");
        $strNumContrato = $request->get("numContrato");
        $intIdFormaPago = $request->get("formaPago");
        $limit          = $request->get("limit");
        $page           = $request->get("page");
        $start          = $request->get("start");   
        $strEsFisico    = 'N';     
        
        $em = $this->get('doctrine')->getManager('telconet');         
        if($cliente)
        {
            $intClienteSesion = $cliente['id_persona_empresa_rol'];
        }
        else
        {
            $intClienteSesion = "";
        }
        $arrayParametros = array();
        $arrayParametros['estado']      = $strEstado;
        $arrayParametros['idEmpresa']   = $intIdEmpresa;
        $arrayParametros['fechaDesde']  = $fechaDesde[0];
        $arrayParametros['fechaHasta']  = $fechaHasta[0];
        $arrayParametros['idper']       = $intClienteSesion;
        $arrayParametros['idOficina']   = $intIdOficina;
        $arrayParametros['idFormaPago'] = $intIdFormaPago;
        $arrayParametros['numContrato'] = $strNumContrato;
        $arrayParametros['limit']       = $limit;
        $arrayParametros['page']        = $page;
        $arrayParametros['start']       = $start;
        
        if ((!$fechaDesde[0])&&(!$fechaHasta[0]))
        {
            $datos = $em->getRepository('schemaBundle:InfoContrato')->find30ContratosPorEmpresaPorEstado($strEstado,$intIdEmpresa,$intClienteSesion);
        }
        else
        {
            $datos = $em->getRepository('schemaBundle:InfoContrato')->findContratosPorCriterios($arrayParametros);
        }
        
		$i=1;
		foreach ($datos as $datos):
            if($i % 2==0)
            {
                $clase='k-alt';
            }
            else
            {
                $clase='';
            }
					
            $urlVer      = $this->generateUrl('infocontrato_show', array('id' => $datos->getId()));
            $urlEditar   ='';
            $urlEliminar ='';
            $linkVer     = $urlVer;
            $linkEditar  = $urlEditar;
            $linkEliminar= $urlEliminar;

            $objContrato = $em->getRepository('schemaBundle:InfoContrato')
                              ->findOneById($datos->getId());

            if(is_object($objContrato) && $objContrato->getEstado() == 'PorAutorizar')
            {
                $linkVer   = $this->generateUrl('infocontrato_new', 
                             array('idper'          => $objContrato->getPersonaEmpresaRolId()->getId()
                                  ,'nombrePantalla' => 'Contrato'));
            }
            $objContratoFisico = $em->getRepository('schemaBundle:InfoContratoCaracteristica')
                                    ->findOneBy(array('contratoId'          => $datos->getId(),
                                                      'estado'              => 'Activo',
                                                      'valor1'              => 'FISICO'));
            if(is_object($objContratoFisico))
            {
                $strEsFisico = 'S';

            }
            $persona=$em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($datos->getPersonaEmpresaRolId()->getId());
            $fechaFinContrato='';
            if($datos->getFeFinContrato())
            {
                $fechaFinContrato=strval(date_format($datos->getFeFinContrato(),"d/m/Y G:i"));
            }
				
            $arreglo[]= array(
                                'Numerocontrato'        => $datos->getNumeroContrato(),
                                'Numerocontratoemppub'  => $datos->getNumeroContratoEmpPub(),
                                'Valorcontrato'         => $datos->getValorContrato(),
                                'Valoranticipo'         => $datos->getValorAnticipo(),
                                'Valorgarantia'         => $datos->getValorGarantia(),
                                'Fefincontrato'         => $fechaFinContrato,
                                'estado'                => $datos->getEstado(),
                                'linkVer'               => $linkVer,
                                'linkEditar'            => $linkEditar,
                                'linkEliminar'          => $linkEliminar,
                                'clase'                 => $clase,
                                'boton'                 => "",
                                'esFisico'              => $strEsFisico,
                                'cliente'               => $persona->getPersonaId()->getInformacionPersona(),
                                'ptoclienteId'          => $ptocliente['id']
                            );                            
            $i++;     
		endforeach;
        if (!empty($arreglo))
        {
            $response = new Response(json_encode($arreglo));
        }
        else
        {
            $arreglo[]= array(
                                'Numerocontrato'        => "",
                                'Numerocontratoemppub'  => "",
                                'Valorcontrato'         => "",
                                'Valoranticipo'         => "",
                                'Valorgarantia'         => "",
                                'Fefincontrato'         => "",
                                'estado'                => "",
                                'linkVer'               => "",
                                'linkEditar'            => "",
                                'clase'                 => "",
                                'boton'                 => "display:none;",
                                'esFisico'              => "",
                                'cliente'               => "",
                                'ptoclienteId'            =>""
                             );
           $response = new Response(json_encode($arreglo));
        }		
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    /**
     * Documentación para el método 'estadosAction'.
     * Obtiene informacion de los diferentes estados de un contrato para llenado de combo via ajax    
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.1 13-04-2016   
     * @return array $response      
     */ 
    public function estadosAction()
    {    
        $arreglo[]= array('idEstado'=>'Activo','codigo'    => 'ACT','descripcion'=> 'Activo');
        $arreglo[]= array('idEstado'=>'Pendiente','codigo' => 'PEN','descripcion'=> 'Pendiente');
        $arreglo[]= array('idEstado'=>'Inactivo','codigo'  => 'ACT','descripcion'=> 'Inactivo');                
        $arreglo[]= array('idEstado'=>'Convertido','codigo'=> 'ACT','descripcion'=> 'Convertido');
        $arreglo[]= array('idEstado'=>'Rechazado','codigo' => 'REC','descripcion'=> 'Rechazado');
        $arreglo[]= array('idEstado'=>'Cancelado','codigo' => 'CAN','descripcion'=> 'Cancelado');

        $response = new Response(json_encode(array('estados'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;   
    }
    
    /**
    *obtiene listado de bancos asociados a tipo cuenta
    * @return object - response
    * @author Andres Montero <amontero@telconet.ec> 
    * @version 1.1
    * @since 2015-07-30 
    * 
    * @author Andrés Montero<amontero@telconet.ec>
    * @version 1.1 29-06-2017
    * Se usa la nueva funcion para obtener bancos findBancosTipoCuentaPorCriterio 
    */    
    public function listaBancosAsociadosAction()
    {
        $request           = $this->getRequest();
        $session           = $request->getSession();
        $tipoCuenta        = $request->request->get("tipoCuenta");
        $bancoTipoCuentaId = $request->request->get("bcoTipoCtaId");
        $arrayParametros                  = array();
        $arrayParametros['strTipoCuenta'] = $tipoCuenta;
        $arrayParametros['arrayEstados']  = array('Activo','Activo-debitos');
        $arrayParametros['intPaisId']     = $session->get('intIdPais');
        $em                               = $this->getDoctrine()->getManager('telconet');
        $listado_bancos                   = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->findBancosTipoCuentaPorCriterio($arrayParametros);
        $tam                              = 16;
        if($listado_bancos)
        {
            $presentacion_div="<option value=''>Seleccione</option>";
            
            foreach ($listado_bancos as $bancos)
            {
				if($bancoTipoCuentaId)
                {
					if($bancoTipoCuentaId==$bancos->getId())
                    {
						$presentacion_div.="<option value='".$bancos->getId()."' selected>".$bancos->getBancoId()->getDescripcionBanco()."</option>";
                        $tam=$bancos->getTotalCaracteres();
					}
                    else
                    {
						$presentacion_div.="<option value='".$bancos->getId()."'>".$bancos->getBancoId()->getDescripcionBanco()."</option>";
					}
				}
                else
                {	
					$presentacion_div.="<option value='".$bancos->getId()."'>".$bancos->getBancoId()->getDescripcionBanco()."</option>";
				}
            }
            $arreglo=array('msg'=>'ok','tam'=>$tam,'div'=>$presentacion_div);
        }
        else
        {
            $arreglo=array('msg'=>'No existen bancos asociados');
        }
        $response = new Response(json_encode($arreglo));
        $response->headers->set('Content-type', 'text/json');
        return $response;		
    }
    
        //funcion para flujo
	public function ajaxGetContratoClienteAction($id)
	{
		$request = $this->getRequest();
		$request=$this->get('request');
		$session=$request->getSession();
		$idEmpresa=$session->get('idEmpresa');
		
		//$idEmpresa = '10';
		$arreglo=array();
		$request = $this->getRequest();
		$em = $this->get('doctrine')->getManager('telconet');				
		$contrato=$em->getRepository('schemaBundle:InfoContrato')->findContratosPorEmpresaPorPersona($idEmpresa,$id);
		//print_r($contrato);die;
		if($contrato){
	//foreach($contrato as $dato){	
		$arreglo[]= array('tiene'=> 'si','numero'=> $contrato->getNumeroContrato(),
						'estado'=> $contrato->getEstado(),'formaPago' => $contrato->getFormaPagoId()->getDescripcionFormaPago());
	//}
		}
		else
		{
			$arreglo[]= array('tiene'=> 'no','numero'=> '','estado'=> '','formaPago'=>'');
		}                
		$response = new Response(json_encode(array('contrato'=>$arreglo)));
		$response->headers->set('Content-type', 'text/json');
		return $response;
		//print_r($arreglo);
	} 

    /**
     * Lists all InfoContrato entities.
     *
     */
    public function aprobarContratoAction()
    {
     return $this->render('comercialBundle:infocontrato:aprobarContrato.html.twig', array());
    }

    /**  
     * @Secure(roles="ROLE_164-154")
     *  
     * Documentación para el método 'gridAprobarContratoAction'.
     * Metodo Utilizado para obtener los contratos Pendientes de Aprobacion
     * Consideraciones:
     * Se considera que existiran contratos Pendientes de Aprobacion que correspondan a ventas nuevas  y existiran
     * Contratos Pendientes de Aprobacion que correspondan a Cambio de Razon Social por Puntos
     * 
     * @param Request $request
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.1 01-10-2015   
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.2 15-02-2016
     * @since 1.1
     * Se agrega el tipo de contrato(origen: WEB-MOVIL) y la situación de los documentos entregables(documento: PENDIENTES-ENTREGADOS)
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.2
     * @since 26-11-2018
     * Se presenta el botón para procesar el contrato únicamente si no tiene deuda por factura de instalación.
     * Se crea el perfil "Aprobar contrato no pagado" del módulo "contrato" y la acción "aprobarContratoNoPagado"
     *
     * @author Josselhin Moreira Quezada<kjmoreira@telconet.ec>
     * @version 1.3
     * @since 2-04-2019
     * Se crea la validación para saber si la forma de pago del contrato cumple con los parámetros para las promociones en la factura de instalación(100%).
     * 
     * @author Néstor Naula<nnaulal@telconet.ec>
     * @version 1.4
     * @since 11-07-2021
     * Se valida que solo se visualicen los contratos fisicos nuevos.
     *
     * 
     * @return JSON       
     */
    public function gridAprobarContratoAction()
    {
        /* Funcion que saca grid de todos los contratos en estado pendiente de Aprobacion */
        $request                 = $this->get('request');
        $session                 = $request->getSession();
        $idEmpresa               = $session->get('idEmpresa');
        $prefijoEmpresa          = $session->get('prefijoEmpresa');
        $fechaDesde              = explode('T', $request->get("fechaDesde"));
        $fechaHasta              = explode('T', $request->get("fechaHasta"));
        $idOficina               = $request->get("idOficina");
        $estado                  = 'Pendiente';
        $idper                   = "";        
        $strOrigen               = $request->get('origen');
        $strDocumento            = $request->get("documento");
        $nombre                  = $request->get("nombre");
        $limit                   = $request->get("limit");
        $page                    = $request->get("page");
        $start                   = $request->get("start");
        $idTipoContratoAprob     = $request->get("idTipoContratoAprob");
        $arrayResult             = null;
        $arrayContratos          = null;
        $intTotal                = 0;
        $emFinanciero            = $this->getDoctrine()->getManager('telconet_financiero');
        $objRepositoryFinanciero = $emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroCab");
        $emComercial             = $this->getDoctrine()->getManager('telconet');

        $strLinkProcesoAprobar         = "";
        $strLinkAprobCambioRazonSocial = "";
        
        //MODULO 60 - contrato/aprobarContratoNoPagado
        $booleanProcesaNoPagados = $this->get('security.context')->isGranted('ROLE_60-6177');

        $arrayParams = array_merge(array('estado'     => $estado),
                                   array('idEmpresa'  => $idEmpresa),
                                   array('fechaDesde' => $fechaDesde[0]),
                                   array('fechaHasta' => $fechaHasta[0]),
                                   array('idper'      => $idper),
                                   array('idOficina'  => $idOficina),
                                   array('nombre'     => $nombre),
                                   array('limit'      => $limit),
                                   array('page'       => $page),
                                   array('start'      => $start),
                                   array('origen'     => $strOrigen),
                                   array('documento'  => $strDocumento));
        /* @var $service InfoContratoAprobService */
        $service = $this->get('comercial.InfoContratoAprob');
        
        // utilizar lógica única del service
        if($idTipoContratoAprob == "Contrato Nuevo")
        {
            if($prefijoEmpresa == 'MD' || $prefijoEmpresa == 'EN' )
            {
                $arrayRespuesta = $service->listarContratosPorCriterios($arrayParams);
                $arrayContratos = $arrayRespuesta['registros'];
                $intTotal       = $arrayRespuesta['total'];
            }
            else
            {                
                $arrayRespuesta = $service->listarContratosTnPorCriterios($arrayParams);
                $arrayContratos = $arrayRespuesta['registros'];
                $intTotal       = $arrayRespuesta['total'];
            }
        }
        else
        {
            if($idTipoContratoAprob == "Cambio de Razon Social")
            {
                $arrayParams['estado']  = array('Pendiente','PorAutorizar');

                $arrayRespuesta = $service->listarContratosPorCambioRazonSocialCriterios($arrayParams);
                $arrayContratos = $arrayRespuesta['registros'];
                $intTotal       = $arrayRespuesta['total'];
            }
        }
        if(!empty($arrayContratos))
        {
            $arrayResult = array();
            foreach($arrayContratos as $objContrato)
            {
                $strVerContrato      = "S";
                $strUrlVer           = $this->generateUrl('infocontrato_show', array('id' => $objContrato->getId()));
                $strLinkVer          = $strUrlVer;            
                $persona_empresa_rol = $service->getDatosPersonaEmpresaRolId($objContrato->getPersonaEmpresaRolId()->getId());
                $entityPersona       = $service->getDatosPersonaId($persona_empresa_rol->getPersonaId()->getId());

                if(!is_object($entityPersona))
                {
                    throw $this->createNotFoundException('Error No encontró Entidad Info_Persona');
                }

                $arrayAprobar = array('id_persona'  => $entityPersona->getId(),
                                      'id_contrato' => $objContrato->getId());

                if($idTipoContratoAprob == "Contrato Nuevo")
                {
                    $strLinkProcesoAprobar         = $this->generateUrl('aprobacioncontrato_proceso_aprobar', $arrayAprobar); 
                    $strLinkAprobCambioRazonSocial = "";
                    if($prefijoEmpresa == 'MD' || $prefijoEmpresa == 'EN' )
                    {
                        $objAdendum = $emComercial->getRepository('schemaBundle:InfoAdendum')
                                                  ->findOneBy(array("contratoId"    => $objContrato->getId()
                                                    ,"formaContrato" => "DIGITAL"
                                                             ));
                        if(is_object($objAdendum))
                        {
                            $strVerContrato = "N";
                        }
                    }
                }
                else
                {
                    if($idTipoContratoAprob == "Cambio de Razon Social")
                    {

                        $strLinkAprobCambioRazonSocial = $this->generateUrl('aprobacioncontrato_aprobarContratoCambioRazonSocial', $arrayAprobar);
                        $strLinkProcesoAprobar         = "";
                    }
                }
                $arrayContrato         = array('idContrato'    => $objContrato->getId());
                $arrayEntregables      = array('intIdContrato' => $objContrato->getId(), 
                                               'strFormaPago'  => $objContrato->getFormaPagoId()->getCodigoFormaPago());
                $strLinkVerArchivo     = $this->generateUrl('aprobacioncontrato_showDocumentosContrato', $arrayContrato);
                $strLinkVerEntregables = $this->generateUrl('aprobacioncontrato_showDocumentosEntregables', $arrayEntregables);

                if($objContrato->getFeFinContrato() != null)
                {
                    $fecha_fin = strval(date_format($objContrato->getFeFinContrato(), "d/m/Y G:i"));
                }
                else
                {
                    $fecha_fin = "";
                }

                if(!$booleanProcesaNoPagados)
                {
                    //Se verifica si se ha pagado la factura de instalación en la persona empresa rol para poder procesar el contrato.
                    $arrayParametros = array("arrayEstados"         => array("Pendiente","Activo","Cerrado"),
                                             "arrayCaracteristicas" => array("POR_CONTRATO_DIGITAL","POR_CONTRATO_FISICO"),
                                             "intIdContrato"        => $objContrato->getId());
                    $arrayRespuestaPagadas = $objRepositoryFinanciero->getFacturacionInstalacionPagada($arrayParametros);
                    $intContador = array_count_values($arrayRespuestaPagadas);
                    $intFacturasNoPagadas = isset($intContador['N']) ? $intContador['N'] : 0 ;
                    if ($intFacturasNoPagadas > 0)
                    {
                        $strLinkProcesoAprobar = "";
                    }
                    
                    //VALIDACIÓN PARA SABER SI LA FORMA DE PAGO DEL CONTRATO ESTA DENTRO DEL LAS PROMOCIONES PARA EL DESCUENTO DEL 100% EN LA FACTURA DE INSTALACIÓN. 
  
                    $arrayParametroCabecera = array("intIdContrato"      => $objContrato->getId(),
                                                    "strNombreParametro" => 'PORCENTAJE_DESCUENTO_INSTALACION');
                    $arrayRespuestaPagadas  = $objRepositoryFinanciero->getParametroFormaPago($arrayParametroCabecera);
                    
                    $arrayIntegrantes  = json_encode($arrayRespuestaPagadas);
                    $arrayResultados   = json_decode($arrayIntegrantes, true);
                    
                    if($arrayResultados[0]['porcentaje'] === '100')
                    {
                        $strLinkProcesoAprobar = $this->generateUrl('aprobacioncontrato_proceso_aprobar', $arrayAprobar);
                    }
                }
                if($strVerContrato == "S")
                {
                    $arrayResult[] = array( 'id'                            => $objContrato->getId(),
                                            'Numerocontrato'                => $objContrato->getNumeroContrato(),
                                            'Numerocontratoemppub'          => $objContrato->getNumeroContratoEmpPub(),
                                            'Valorcontrato'                 => $objContrato->getValorContrato(),
                                            'Valoranticipo'                 => $objContrato->getValorAnticipo(),
                                            'Valorgarantia'                 => $objContrato->getValorGarantia(),
                                            'Fefincontrato'                 => $fecha_fin,
                                            'estado'                        => $objContrato->getEstado(),
                                            'linkVer'                       => $strLinkVer,
                                            'linkProcesoAprobar'            => $strLinkProcesoAprobar,
                                            'strLinkAprobCambioRazonSocial' => $strLinkAprobCambioRazonSocial,
                                            'linkVerArchivo'                => $strLinkVerArchivo,
                                            'origen'                        => $objContrato->getOrigen(),
                                            'linkVerEntregables'            => $strLinkVerEntregables,
                                            'cliente'                       => $persona_empresa_rol->getPersonaId()->getInformacionPersona(),
                                            'oficina'                       => $persona_empresa_rol->getOficinaId()->getNombreOficina()
                    );
                }
            }
        }

        $response = new Response(json_encode(array('total' => $intTotal, 'arreglo' => $arrayResult)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /**
     * @version 1.0 - No existe
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.0 04-01-2021 - Se agrega la característica del contrato a proceso completado
     * @since 1.1
     * 
     * 
     * @Secure(roles="ROLE_164-155")
     */
    public function aprobarContratoAjaxAction()
    {
        $request=$this->getRequest();        
        $session  = $request->getSession();
		
        $idEmpresa=$request->getSession()->get('idEmpresa');
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');        
        $respuesta->setContent("error del Form");  
        $em = $this->getDoctrine()->getManager('telconet');

        //Obtiene parametros enviados desde el ajax
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
        $array_valor = explode("|",$parametro);       
        
        $em->getConnection()->beginTransaction();
 	try{  
            foreach($array_valor as $id):             
                $entity = $em->getRepository('schemaBundle:InfoContrato')->find($id);
                if (!$entity) {
                        throw $this->createNotFoundException('No se encontro el contrato buscado');
                }            
                //Activa el contrato
                $entity->setEstado('Activo');
				$entity->setUsrAprobacion($request->getSession()->get('user'));
				$entity->setFeAprobacion(new \DateTime('now'));
                $em->persist($entity);
                $em->flush();

                //SE COMPLETA LA CARACTERISTICA DEL PROCESP CONTRATO
                if(is_object($entity))
                {
                    $objEntityCaract = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                          ->findOneBy(
                                            array("descripcionCaracteristica"   => 'FORMA_REALIZACION_CONTRATO',
                                                  "estado"                      => 'Activo'));

                    if(is_object($objEntityCaract))
                    {
                        $entityCaractContrato = $em->getRepository('schemaBundle:InfoContratoCaracteristica')
                                                   ->findOneBy(
                                                    array("caracteristicaId"  => $objEntityCaract,
                                                          "contratoId"        => $entity,
                                                          "estado"            => "Activo"
                                                        ));

                        if(is_object($entityCaractContrato))
                        {
                            $entityCaractContrato->setFeUltMod(new \DateTime('now'));
                            $entityCaractContrato->setUsrUltMod($session->get('user'));
                            $entityCaractContrato->setValor2('C');
                        }
                        else
                        {
                            $entityCaractContrato = new InfoContratoCaracteristica();
                            $entityCaractContrato->setCaracteristicaId($objEntityCaract);
                            $entityCaractContrato->setContratoId($entity);
                            $entityCaractContrato->setEstado('Activo'); 
                            $entityCaractContrato->setFeCreacion(new \DateTime('now'));
                            $entityCaractContrato->setUsrCreacion($session->get('user'));
                            $entityCaractContrato->setIpCreacion('127.0.0.1');
                            $entityCaractContrato->setValor1('FISICO');
                            $entityCaractContrato->setValor2('C');
                        }

                        $em->persist($entityCaractContrato);
                        $em->flush();
                    }
                }
				
				//REGISTRA ESTADO DE PROSPECTO EN PENDIENTE-CONVETIR
				 $entityPersonaEmpresaRol= $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
				->findOneById($entity->getPersonaEmpresaRolId());
				$entityPersonaEmpresaRol->setEstado('Pend-convertir');
                $em->persist($entityPersonaEmpresaRol);
                $em->flush();	
				
				//REGISTRA EN LA TABLA DE PERSONA_EMPRESA_ROL_HISTO
				$entity_persona_historial = new InfoPersonaEmpresaRolHisto();
				$entity_persona_historial->setEstado('Pend-convertir');
				$entity_persona_historial->setFeCreacion(new \DateTime('now'));
				$entity_persona_historial->setIpCreacion($request->getClientIp());
				$entity_persona_historial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
				$entity_persona_historial->setUsrCreacion($session->get('user'));
				$em->persist($entity_persona_historial);
				$em->flush();				
				
           endforeach;
             
           $em->getConnection()->commit();   
           $respuesta->setContent("Se aprobaron contratos con exito.");            
       }       
	catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $respuesta->setContent($e->getMessage());            
	}
       

       return $respuesta;
    }
    
    public function rechazarContratoAjaxAction(){

        $request=$this->getRequest();
        $idEmpresa=$request->getSession()->get('idEmpresa');
        $session  = $request->getSession();         
        $usrCreacion=$session->get('user');		
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');        
        $respuesta->setContent("error del Form");  
        $em = $this->getDoctrine()->getManager('telconet');

        //Obtiene parametros enviados desde el ajax
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
        $motivoId = $peticion->get('motivoId');
        $array_valor = explode("|",$parametro);       
        
        $em->getConnection()->beginTransaction();
 	try{  
            foreach($array_valor as $id){            
                $entity = $em->getRepository('schemaBundle:InfoContrato')->find($id);
                if (!$entity) {
                        throw $this->createNotFoundException('No se encontro la solicitud buscada');
                }
                $entity->setEstado('Rechazado');
                $entity->setUsrRechazo($usrCreacion);
                $entity->setFeRechazo(new \DateTime('now'));                
                $entity->setMotivoRechazoId($motivoId);                
                $em->persist($entity);
                $em->flush();												
            }
             
           $em->getConnection()->commit();   
           $respuesta->setContent("Se aprobaron las solicitudes con exito.");            
       }       
	catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $respuesta->setContent($e->getMessage());            
	}
       return $respuesta;        

    }


    public function getMotivosRechazoContrato_ajaxAction() {
        /* Modificacion a utilizacion de estados por modulos */
        //$session = $this->get('request')->getSession();
        //$modulo_activo=$session->get("modulo_activo");
        $em = $this->get('doctrine')->getManager('telconet');
        $datos = $em->getRepository('schemaBundle:AdmiMotivo')
		->findMotivosPorModuloPorItemMenuPorAccion('aprobacioncontrato','AutorizacionContrato','rechazarContratoAjax');
		$arreglo=array();
    //print_r($datos);die;
    foreach($datos as $valor):
        //print_r($entityAdmiTipoSolicitud[0]->getId());
            $arreglo[] = array(
                'idMotivo' => $valor->getId(),
                'descripcion' => $valor->getNombreMotivo(),
                'idRelacionSistema'=>$valor->getRelacionSistemaId()
            );
    endforeach;
    //die;

        $response = new Response(json_encode(array('motivos' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
        
    public function getOficinas_ajaxAction() {
        /* Modificacion a utilizacion de estados por modulos */

        $request=$this->getRequest();
        $idEmpresa=$request->getSession()->get('idEmpresa');
        $em = $this->get('doctrine')->getManager('telconet');
        $datos = $em->getRepository('schemaBundle:InfoOficinaGrupo')
		->findBy(array( "empresaId" => $idEmpresa, "estado" => "Activo"));
        $arreglo=array();
    //print_r($datos);die;
    foreach($datos as $valor):
        //print_r($entityAdmiTipoSolicitud[0]->getId());
            $arreglo[] = array(
                'idOficina' => $valor->getId(),
                'nombre' => $valor->getNombreOficina()
            );
    endforeach;
    //die;

        $response = new Response(json_encode(array('oficinas' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }      
    
    /**
    * Funcion que ejecuta el proceso de aprobacion del contrato, precargara la informacion de la persona, 
    * Grid con los servicios de todos los puntos de la persona_empresa_rol_id en estado Factible 
    * y la informacion del contrato(forma de Pago) para ser editable previo a su aprobacion del contrato.  
    * Se agrega descencriptado del campo Numero_Cta_tarjeta para que pueda ser editable por el 
    * Administrador de Contratos.  
    * @author : telcos
    * @author : apenaherrera
    * @param integer $id_persona
    * @param integer $id_contrato
    * @version 1.0 23-06-2014          
    * @version 1.1 01-12-2014
    * @version 1.2 15-06-2016
    * Se modifica para que obtenga el ROL del Pre-Cliente sea en estado:  Activo, Pendiente, Pend-convertir
    * 
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.3 01-09-2017
    * - Se agrega variable intIdPais para obtener pais que esta en sesion.
    * - Se agrega el pais al type InfoContratoFormaPagoEditType para poder cargar los tipos de cuenta. 
    * 
    * @return a RedirectResponse to the given URL.
    */	
     public function procesoAprobarContratoAction($id_persona, $id_contrato)
    {
        try
        {  
            /* Funcion que saca grid de todos los contratos en estado pendiente de Aprobacion */
            /* @var $service InfoContratoAprobService */
            $service  = $this->get('comercial.InfoContratoAprob');
            // utilizar logica unica del service        
            $entities = $service->getDatosPersonaId($id_persona);

            $entity = new InfoPersona();
            $options['identificacion']         = $entities->getIdentificacionCliente();
            $options['razonSocial']            = $entities->getRazonSocial();
            $options['nombres']                = $entities->getNombres();
            $options['apellidos']              = $entities->getApellidos();
            $options['direccion']              = $entities->getDireccion();
            $options['tipoEmpresa']            = $entities->getTipoEmpresa();
            $options['tipoIdentificacion']     = $entities->getTipoIdentificacion();
            $options['tipoTributario']         = $entities->getTipoTributario();
            $options['nacionalidad']           = $entities->getNacionalidad();
            $options['direccionTributaria']    = $entities->getDireccionTributaria();
            $options['calificacionCrediticia'] = $entities->getCalificacionCrediticia();
            $options['genero']                 = $entities->getGenero();
            $options['estadoCivil']            = $entities->getEstadoCivil();
            //cambios DINARDARP - se agrega campo origenes de ingresos
            $options['origenIngresos']         = $entities->getOrigenIngresos();
            $options['fechaNacimiento']        = $entities->getFechaNacimiento();
            $options['representanteLegal']     = $entities->getRepresentanteLegal();
            if( $entities->getTituloId() )
            {
                $options['titulo'] = $entities->getTituloId();
            }
            else
            {
                $options['titulo'] = "";
            }
             // Campos Nuevos CONTRIBUYENTE_ESPECIAL,PAGA_IVA, NUMERO_CONADIS
            $request                           = $this->getRequest();
            $session                           = $request->getSession();	
            $strEmpresaId                      = $request->getSession()->get('idEmpresa');
            $strPrefijoEmpresa                 = $request->getSession()->get('prefijoEmpresa');
            $intIdPais                         = $request->getSession()->get('intIdPais');
            $options['empresaId']              = $strEmpresaId;            
            $options['contribuyenteEspecial']  = $entities->getContribuyenteEspecial();
            $options['pagaIva']                = $entities->getPagaIva();
            $options['numeroConadis']          = $entities->getNumeroConadis();
            
            $strTieneNumeroConadis = 'N';
            if($entities->getNumeroConadis()!=null && $entities->getNumeroConadis()!='')
            {
                $strTieneNumeroConadis = 'S';
            }                
	        $options['tieneNumeroConadis'] = $strTieneNumeroConadis;
            
            // Campo OFICINA_FACTURACION , ES_PREPAGO
            $options['oficinaFacturacion'] = null;
            $options['esPrepago']          = null;
            $em                            = $this->getDoctrine()->getManager('telconet');
            $objPersonaEmpresaRol          = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                             ->getPersonaEmpresaRolPorPersonaPorTipoRolActivosPendientes($id_persona,'Pre-cliente', $strEmpresaId);        
            if($strPrefijoEmpresa == 'TN')
            {
                if($objPersonaEmpresaRol)
                {
                    $intOficinaFacturacionId = $objPersonaEmpresaRol->getOficinaId();
                    $intOficinaFacturacion   = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($intOficinaFacturacionId);
                    $options['oficinaFacturacion'] = $intOficinaFacturacion;
                }
            }
            
            if($objPersonaEmpresaRol!= null && $objPersonaEmpresaRol->getEsPrepago()!=null && $objPersonaEmpresaRol->getEsPrepago()!='')
            {
                $strEsPrepago = $objPersonaEmpresaRol->getEsPrepago();
                $options['esPrepago'] = $strEsPrepago;
            }              	        

            //Saco los datos del contrato => forma de Pago para permitir editar datos como numero de tarjeta, banco, anio y mes de vencimiento etc
            // utilizar logica unica del service        
            $entityContrato    = $service->getDatosContratoId($id_contrato);
            $editForm          = $this->createForm(new InfoContratoType(array('validaFile' => true)), $entityContrato);
            $deleteForm        = $this->createDeleteForm($id_contrato);
            $bancoTipoCuentaId = null;

            $parametros = array(
                'entityContrato'      => $entityContrato,
                'edit_form'           => $editForm->createView(),
                'delete_form'         => $deleteForm->createView(),
                'clase'               => '',
                'formFormaPago'       => '',
                'strNumeroCtaTarjeta' => '',
                'bancoTipoCuentaId'   => '',
                'id_per_emp_rol'      => '',
                'form'                => '',
                'direccionTributaria' => '',
                'entity'              => '');
        
             //Construyo el form
            $form                              = $this->createForm(new ProcesoAprobarContratoType($options), $entity);
            $parametros['form']                = $form->createView();
            $parametros['direccionTributaria'] = $entities->getDireccionTributaria();
            $parametros['entity']              = $entities;
            $parametros['prefijoEmpresa']      = $strPrefijoEmpresa;
             
            //si posee forma de pago diferente de efectivo
            $no_existe = 0;
            if( $entityContrato != null && $entityContrato->getFormaPagoId()->getDescripcionFormaPago() != "Efectivo" )
            {
                //Busco datos de la forma de pago por id contrato 
                $estado              = "Pendiente";
                $parametros['clase'] = "";
                // utilizar logica unica del service        
                $formFormaPago = $service->getDatosContratoFormaPagoId($id_contrato);
                $parametros['id_per_emp_rol'] = $entityContrato->getPersonaEmpresaRolId()->getId();
                $parametros['bancoTipoCuentaId'] = $bancoTipoCuentaId;
                if( $formFormaPago )
                {
                    $pagoForm                    = $this->createForm(
                                                                     new InfoContratoFormaPagoEditType(array("intIdPais"=>$intIdPais)), 
                                                                     $formFormaPago
                                                                    );
                    $parametros['formFormaPago'] = $pagoForm->createView();
                    $bancoTipoCuentaId           = $formFormaPago->getBancoTipoCuentaId()->getId();

                    //Descencripto el campo Numero_Cta_Tarjeta
                    /* @var $serviceCrypt \telconet\seguridadBundle\Service\CryptService */
                    $serviceCrypt = $this->get('seguridad.Crypt');
                    $strNumeroCtaTarjeta = $serviceCrypt->descencriptar($formFormaPago->getNumeroCtaTarjeta());

                    if($strNumeroCtaTarjeta)
                    {
                        $parametros['strNumeroCtaTarjeta'] = $strNumeroCtaTarjeta;
                    }
                    else
                    {
                        throw new \Exception('No fue posible mostrar el Numero de Cuenta / Tarjeta - Contactese con el administrador del sistema.'); 
                    }
                }
                else
                {
                    $no_existe           = 1;
                    $parametros['clase'] = "campo-oculto";
                }
            }
            else
            {
                $parametros['clase'] = "campo-oculto";
                $no_existe           = 1;
            }
            $parametros['bancoTipoCuentaId'] = $bancoTipoCuentaId;

            if($no_existe == 1)
            {
                $entityFormaPago             = new InfoContratoFormaPago();
                $formInfoPago                = $this->createForm(new InfoContratoFormaPagoEditType(array("intIdPais"=>$intIdPais)), $entityFormaPago);
                $parametros['formFormaPago'] = $formInfoPago->createView();
            }

            $parametros['id_per_emp_rol'] = $entityContrato->getPersonaEmpresaRolId()->getId();          
                                   
            return $this->render('comercialBundle:infocontrato:procesoAprobarContrato.html.twig', $parametros);
        }
        catch (\Exception $e)
        {   
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());            
            return $this->render('comercialBundle:infocontrato:procesoAprobarContrato.html.twig', $parametros);
        }
    }
    /**Función que saca todos los servicios de todos los puntos clientes 
    *  de una persona_empresa_rol_id en estado Factible 
    *
    * @author Edgar Holguín <eholguin@telconet.ec>       
    * @version 1.1 08-04-2019
    * Se agrega condición para que en consulta de servicios se incluya Telconet Guatemala.
    * 
    * @author Telcos      
    * @version 1.0 
    */    
    public function listadoServiciosAction($id_per_emp_rol){
       /*Para listar:
         * Debo buscar todos los servicios que tenga la persona_empresa_rol
         * Y los servicios con estado Factible
      *  */
        $request   = $this->getRequest();
        $limit     = $request->get("limit");
        $page      = $request->get("page");
        $start     = $request->get("start");
        $nombre    = "";

        /* @var $service InfoContratoAprobService */                                 
        $service = $this->get('comercial.InfoContratoAprob');        
        // utilizar logica unica del service        
        $session         = $request->getSession();            
        $strPrefijoEmpresa  = $session->get('prefijoEmpresa');
        if($strPrefijoEmpresa=='TN' || $strPrefijoEmpresa=='TNP'|| $strPrefijoEmpresa=='TNG')
        {
             $arrayEstado=array('Rechazado', 'Rechazada', 'Cancelado', 'Anulado', 'Cancel', 'Eliminado', 
                              'Reubicado', 'Trasladado');
             $resultado=$service->getTodosServiciosXEstadoTn($id_per_emp_rol,$start,$limit,$arrayEstado);                                                                        
        }
        else
        {
            $estado='Factible';
            $resultado=$service->getTodosServiciosXEstado($id_per_emp_rol,$start,$limit,$estado);                                                                        
        }
        
        $datos = $resultado['registros'];
	$total = $resultado['total'];	
	if($datos)
	{
		foreach ($datos as $datos):
				
		       $descripcion="";
				
			if($datos->getProductoId()!=null)
				$descripcion=$datos->getProductoId()->getDescripcionProducto();
				
			if($datos->getPlanId()!=null)
				$descripcion=$datos->getPlanId()->getNombrePlan();
					
			$arreglo[]= array(
			        'id'=>$datos->getId(),
                                'login'=>$datos->getPuntoId()->getLogin(),
				'descripcion'=>$descripcion,
				'cantidad'=>$datos->getCantidad(),
				'estado'=>$datos->getEstado(),
				'precio'=>$datos->getPrecioVenta(),
				);
		endforeach;
	}
		
	if (!empty($arreglo))                
           $response = new Response(json_encode(array('total' => $total, 'listado' => $arreglo)));
        else
        {
                $arreglo[]= array(
                        'id'=> "",
                        'login'=> "",
                        'descripcion'=> "",
                        'cantidad'=> "",
                        'estado'=> "",
                        'precio'=> "",
                );                
                $response = new Response(json_encode(array('total' => $total, 'listado' => $arreglo)));
        }		
           $response->headers->set('Content-type', 'text/json');
            return $response;
  }
  
  /**
   * Funcion que guarda el proceso de aprobacion de contrato
   * Guarda informacion del prospecto, convierte Prospecto a Cliente
   * Genera Orden de Trabajo de los servicios Factibles que pasarán al proceso de Planificacion
   * Actualiza la informacion de la forma de Pago : numero de cuenta o tarjeta , anio y mes de vencimiento de la tarjeta etc.
   * Actualiza estado de contrato a Aprobado
   * @author modificado Anabelle Peñaherrera <apenaherrera@telconet.ec>
   * @param interger $id // id del contrato   
   * @throws Exception
   * @version 1.1 modificado 20-02-2015    
   * @version 1.2 15-06-2016
   * Se modifica para que obtenga el ROL del Pre-Cliente sea en estado:  Activo, Pendiente, Pend-convertir
   * 
   * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
   * Se modifica para que las Notificaciones se envien solo para servicios que posee Solicitud de Planificacion
   * @version 1.3 30-06-2016
   *
   * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
   * @version 1.4
   * Se valida por estado el Rol de CLIENTE en el metodo getClientesPorIdentificacion
   * considerando solo estado Activo, y se direcciona correctamente el mensaje de error de cliente y existente.
   * 
   * @author Edgar Holguin <eholguin@telconet.ec>
   * Se agrega envio de parámetro strEmpresaCod para la verificar posteriormente si el cliente debe ser marcado como compensado.
   * @version 1.5 14-11-2016
   * 
   * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
   * @version 1.6 15-02-2016
   * Se establece un tiempo máximo holgado para la ejecución del proceso de aprobación.
   * Se agrega guardado de documentos entregables del cliente.
   * 
   * @author David Leon<mdleon@telconet.ec>
   * @version 1.7 16-06-2020 Se realiza llamada del ws de crm de cierre de cotización para productos marcados.
   * 
   * @author Kevin Baque <kbaque@telconet.ec>
   * @version 1.8 03-07-2020 - Se añade nueva lógica la cual se omite el cierre de cotización en TelcoCRM y se reemplaza por una solicitud.
   * 
   * @author David Leon<mdleon@telconet.ec>
   * @version 1.9 22-10-2020 Se agrega validación para generar OT adicional para planes con cableado Ethernet.
   *
   * @author David Leon <mdleon@telconet.ec>
   * @version 2.0 30-10-2020 Se mueve la validación al service para ser consumido por el web y movil.
   * 
   * @author David Leon <mdleon@telconet.ec>
   * @version 2.1 01-06-2022 Se agrega validación para consumir crm para realizar el pedido si es proyecto.
   * 
   * @author Alex Gómez <algomez@telconet.ec
   * @version 2.2 23-09-2022 Se añade invocación a service para verificación y preplanificación de productos CIH (Megadatos).
   * 
   * @since 2.1
   * 
   * @return a RedirectResponse to the given URL.
   */
    public function guardarProcesoAprobContratoAction($id_contrato)
    {
        ini_set('max_execution_time', 9000000);
        try
        {
            /* Procesar prospecto:
             * - verificar por medio de la identificacion si el mismo ya existe
             * - cambiar el estado a procesado
             * - si no existe guardar la informacion del mismo */
            $request            = $this->getRequest();
            $session            = $request->getSession();
            $usrCreacion        = $session->get('user');
            $ipCreacion         = $request->getClientIp();
            $idEmpresa          = $session->get("idEmpresa");           
            $prefijoEmpresa     = $session->get('prefijoEmpresa');
            $estadoI            = 'Inactivo';
            $formas_contacto    = array();
            $intIdPunto         = 0;
            $objPtoCliente      = $session->get('ptoCliente');
            if($objPtoCliente)
            {
                $intIdPunto     = $objPtoCliente['id'];

            }
            $strUsrCreacion     = $session->get('user');
            $strJsonEntregables = $request->get('documentosEntregables');
            $strOrigen          = $request->get('origenContrato');

            /* @var $service InfoContratoAprobService */
            $service = $this->get('comercial.InfoContratoAprob');
            $serviceTecnico     = $this->get('tecnico.InfoServicioTecnico');
            // utilizar logica unica del service        
            $entityContrato      = $service->getDatosContratoId($id_contrato);
            $persona_empresa_rol = $service->getDatosPersonaEmpresaRolId($entityContrato->getPersonaEmpresaRolId()->getId());
            $empresa_rol         = $service->getDatosEmpresaRolId($persona_empresa_rol->getEmpresaRolId()->getId());
            $prospecto           = $service->getDatosPersonaId($persona_empresa_rol->getPersonaId()->getId());
            $arrayRespuesta      = $service->guardarDocumentoEntregable($strJsonEntregables, $strUsrCreacion);
            $emComercial         = $this->get('doctrine')->getManager('telconet');
            if($arrayRespuesta['ESTADO'] !== "OK")
            {
                throw new \Exception('Error: ' . $arrayRespuesta['ERROR']);
            }
            
            if($prospecto != null)
            {
                $options['identificacion']         = $prospecto->getIdentificacionCliente();
                $options['razonSocial']            = $prospecto->getRazonSocial();
                $options['nombres']                = $prospecto->getNombres();
                $options['apellidos']              = $prospecto->getApellidos();
                $options['direccion']              = $prospecto->getDireccion();
                $options['tipoEmpresa']            = $prospecto->getTipoEmpresa();
                $options['tipoIdentificacion']     = $prospecto->getTipoIdentificacion();
                $options['tipoTributario']         = $prospecto->getTipoTributario();
                $options['nacionalidad']           = $prospecto->getNacionalidad();
                $options['direccionTributaria']    = $prospecto->getDireccionTributaria();
                $options['calificacionCrediticia'] = $prospecto->getCalificacionCrediticia();
                $options['genero']                 = $prospecto->getGenero();
                $options['estadoCivil']            = $prospecto->getEstadoCivil();
                //cambios DINARDARP - se agrega campo origenes de ingresos
                $options['origenIngresos']         = $prospecto->getOrigenIngresos();
                $options['fechaNacimiento']        = $prospecto->getFechaNacimiento();
                $options['representanteLegal']     = $prospecto->getRepresentanteLegal();
                if($prospecto->getTituloId())
                    $options['titulo'] = $prospecto->getTituloId()->getId();
                else
                    $options['titulo'] = "";
                
                // Campos Nuevos CONTRIBUYENTE_ESPECIAL,PAGA_IVA, NUMERO_CONADIS                
                $options['empresaId']              = $idEmpresa;            
                $options['contribuyenteEspecial']  = $prospecto->getContribuyenteEspecial();
                $options['pagaIva']                = $prospecto->getPagaIva();
                $options['numeroConadis']          = "";

                $strTieneNumeroConadis = 'N';
                if($prospecto->getNumeroConadis()!=null && $prospecto->getNumeroConadis()!='')
                {
                    $strTieneNumeroConadis    = 'S';
                    $options['numeroConadis'] = $prospecto->getNumeroConadis();
                }                
                $options['tieneNumeroConadis'] = $strTieneNumeroConadis;

                // Campo OFICINA_FACTURACION , ES_PREPAGO
                $options['oficinaFacturacion'] = null;
                $options['esPrepago']          = null;
                $em                            = $this->getDoctrine()->getManager('telconet');
                $objPersonaEmpresaRol          = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                 ->getPersonaEmpresaRolPorPersonaPorTipoRolActivosPendientes($prospecto->getId(),'Pre-cliente', $idEmpresa);        
                if($prefijoEmpresa == 'TN')
                {
                    if($objPersonaEmpresaRol)
                    {
                        $intOficinaFacturacionId = $objPersonaEmpresaRol->getOficinaId();
                        $intOficinaFacturacion   = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($intOficinaFacturacionId);
                        $options['oficinaFacturacion'] = $intOficinaFacturacion;
                    }
                }

                if($objPersonaEmpresaRol!= null && $objPersonaEmpresaRol->getEsPrepago()!=null && $objPersonaEmpresaRol->getEsPrepago()!='')
                {
                    $strEsPrepago = $objPersonaEmpresaRol->getEsPrepago();
                    $options['esPrepago'] = $strEsPrepago;
                }              	        

            }
            //obtener arreglos de datos del prospecto 
            $datos_form       = $request->get("procesoaprobarcontratotype");            
            $datos_form_extra = $request->get("convertirextratype");
            //obtener el arreglo de los datos de formas de pago
            $formaPago     = $request->get('infocontratoformapagotype');
            $form_contrato = $request->get('infocontratotype');

            $id_forma_pago  = $request->get("id_forma_pago");
            $id_tipo_cuenta = $request->get("id_tipo_cuenta");

            $array_listado_servicios = $request->get("array_listado_servicios");
            $array_valor             = explode("|", $array_listado_servicios);
            $array_formas_contacto   = explode(",", $datos_form['formas_contacto']);
            
            $a = 0;
            $x = 0;
            
            for($i = 0; $i < count($array_formas_contacto); $i++)
            {
                if($a == 3)
                {
                    $a = 0;
                    $x++;
                }
                if($a == 1)
                    $formas_contacto[$x]['formaContacto'] = $array_formas_contacto[$i];
                if($a == 2)
                    $formas_contacto[$x]['valor'] = $array_formas_contacto[$i];
                $a++;
            }

            $idProspecto = $persona_empresa_rol->getPersonaId()->getId();
            if($prospecto != null && $prospecto->getIdentificacionCliente())
            {
                $cliente = $service->getClientesPorIdentificacion($prospecto->getIdentificacionCliente(), $empresa_rol->getEmpresaCod()->getId());
            }
            else
            {
                $cliente = "";
            }

            if(empty($cliente))
            {
                //Funcion que guarda todo el proceso
                $arrayParametros = array();
                $arrayParametros['intIdContrato']       = $id_contrato;
                //...
                $arrayParametros['arrayPersona']        = $datos_form;
                $arrayParametros['arrayPersonaExtra']   = $datos_form_extra;
                $arrayParametros['arrayFormasContacto'] = $formas_contacto;
                //...
                $arrayParametros['arrayFormaPago']      = $formaPago;
                $arrayParametros['intIdFormaPago']      = $id_forma_pago;
                $arrayParametros['intIdTipoCuenta']     = $id_tipo_cuenta;
                //...
                $arrayParametros['arrayServicios']      = $array_valor;
                //...
                $arrayParametros['strUsrCreacion']      = $usrCreacion;
                $arrayParametros['strIpCreacion']       = $ipCreacion;
                $arrayParametros['strPrefijoEmpresa']   = $prefijoEmpresa;
                $arrayParametros['strEmpresaCod']       = $idEmpresa;
                $arrayParametros['intIdPunto']          = $intIdPunto;
                $arrayParametros['strOrigenCIH']        = "CONTRATO_FISICO";
                
                $guardarProceso = $service->guardarProcesoAprobContrato($arrayParametros);   
                if($guardarProceso && array_key_exists('status',$guardarProceso) && $guardarProceso['status']=='ERROR_SERVICE'){
                        throw new \Exception($guardarProceso['mensaje']); 
                }
                else
                {
                    foreach($array_valor as $id)
                    {
                        $entityInfoServicio = $service->getDatosServicioId($id);
                        if($entityInfoServicio && count($entityInfoServicio) > 0)
                        {
                            //------- COMUNICACIONES --- NOTIFICACIONES                           
                            $entitySolicitud = $service->getSolicitudPrePlanifId($entityInfoServicio->getId(), "PrePlanificada");
                            if(isset($entitySolicitud))
                            {
                                $mensaje = $this->renderView('planificacionBundle:Coordinar:notificacion.html.twig', 
                                                             array('detalleSolicitud'     => $entitySolicitud, 
                                                                   'detalleSolicitudHist' => null, 
                                                                   'motivo'               => null));
                                
                                $asunto  = "Solicitud de Instalacion #" . $entitySolicitud->getId();
                                //DESTINATARIOS....  
                                $formasContacto = $service->getContactosByLoginPersonaAndFormaContacto($entityInfoServicio->getPuntoId()
                                                                                                                          ->getUsrVendedor(), 
                                                                                                       'Correo Electronico');
                                $to             = array();
                                $cc             = array();
                                $cc[]           = 'notificaciones_telcos@telconet.ec';

                                $to[]           = 'notificaciones_telcos@telconet.ec';
                                if($formasContacto)
                                {
                                    foreach($formasContacto as $formaContacto)
                                    {
                                        $to[] = $formaContacto['valor'];
                                    }
                                }

                                /* @var $envioPlantilla EnvioPlantilla */
                                $envioPlantilla = $this->get('soporte.EnvioPlantilla');
                                $envioPlantilla->enviarCorreo($asunto, $to, $mensaje);

                                $boolGrabo = true;
                            }
                            //Volvemos agregar la validación
                            $objServCaractCotizacion = $serviceTecnico->getServicioProductoCaracteristica($entityInfoServicio,
                                                                                                  'ID_PROPUESTA',
                                                                                                  $entityInfoServicio->getProductoId()
                                                                                                  );

                            if(is_object($objServCaractCotizacion) && !empty($objServCaractCotizacion))
                            {
                                $serviceTelcoCrm = $this->get('comercial.ComercialCrm');
                                $arrayParametros = array("strIdPropuesta"      => $objServCaractCotizacion->getValor(),
                                                         "strVendedor"         => $entityInfoServicio->getUsrCreacion(),
                                                         "strPrefijoEmpresa"    => $prefijoEmpresa, 
                                                         "strCodEmpresa"        => $idEmpresa);
                                //Ejecuta ws de SuiteCrm
                                $arrayParametrosWSCrm = array(
                                                              "arrayParametrosCRM"   => $arrayParametros,
                                                              "strOp"                => 'createPedidos',
                                                              "strFuncion"           => 'procesar'
                                                             );
                                $arrayRespuestaWSCrm = $serviceTelcoCrm->getRequestCRM($arrayParametrosWSCrm);
                            }
                            //fin de validación
                            
                            $objServCaractTipoProy = $serviceTecnico->getServicioProductoCaracteristica($entityInfoServicio,
                                                                                                        'TIPO_PROYECTO',
                                                                                                        $entityInfoServicio->getProductoId());
                            if( is_object($objServCaractTipoProy) && !empty($objServCaractTipoProy) )
                            {
                                $emComercial->getConnection()->beginTransaction();
                                $objTipoSolicitudProyecto = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                        ->findOneBy(array("descripcionSolicitud" => "SOLICITUD DE PROYECTO",
                                                                                          "estado"               => "Activo"));
                                if( !is_object($objTipoSolicitudProyecto) && empty($objTipoSolicitudProyecto) )
                                {
                                    throw new \Exception("No existe Objeto para el tipo de Solicitud de Proyecto");
                                }
                                $strObservacionSol = "Se crea Solicitud para crear un proyecto en TelcoCRM.";
                                $objDetTipoSolProyecto= new InfoDetalleSolicitud();
                                $objDetTipoSolProyecto->setServicioId($entityInfoServicio);
                                $objDetTipoSolProyecto->setTipoSolicitudId($objTipoSolicitudProyecto);
                                $objDetTipoSolProyecto->setObservacion($strObservacionSol);
                                $objDetTipoSolProyecto->setFeCreacion(new \DateTime('now'));
                                $objDetTipoSolProyecto->setUsrCreacion($entityInfoServicio->getUsrCreacion());
                                $objDetTipoSolProyecto->setEstado('Pendiente');
                                $emComercial->persist($objDetTipoSolProyecto);
                                $emComercial->flush();
                                $objDetTipoSolProyectoHist = new InfoDetalleSolHist();
                                $objDetTipoSolProyectoHist->setDetalleSolicitudId($objDetTipoSolProyecto);
                                $objDetTipoSolProyectoHist->setEstado($objDetTipoSolProyecto->getEstado());
                                $objDetTipoSolProyectoHist->setFeCreacion(new \DateTime('now'));
                                $objDetTipoSolProyectoHist->setUsrCreacion($entityInfoServicio->getUsrCreacion());
                                $objDetTipoSolProyectoHist->setObservacion("Se crea Solicitud de Proyecto");
                                $objDetTipoSolProyectoHist->setIpCreacion($ipCreacion);
                                $emComercial->persist($objDetTipoSolProyectoHist);
                                $emComercial->flush();
                                if ($emComercial->getConnection()->isTransactionActive())
                                {
                                    $emComercial->getConnection()->commit();
                                    $emComercial->getConnection()->close();
                                }
                            }

                            if($prefijoEmpresa == "MD")
                            {
                                // Verificación y preplanificación de productos CIH
                                $serviceInfoContrato = $this->get('comercial.InfoContrato');
                                $arrayParamsGeneraOtCIH = array('intIdServicioInternet'  => $entityInfoServicio->getId(),
                                                                'intIdPunto'             => $entityInfoServicio->getPuntoId()->getId(),
                                                                'strUsuarioCreacion'     => $usrCreacion,
                                                                'strIpCreacion'          => $ipCreacion,
                                                                'strOrigen'              => "CONTRATO_FISICO",
                                                                'strPrefijoEmpresa'      => $prefijoEmpresa,
                                                                'strCodEmpresa'          => $idEmpresa);

                                $arrayResponseCIH = $serviceInfoContrato->generacionOtServicioCIH($arrayParamsGeneraOtCIH);

                                if ($arrayResponseCIH['status'] != 'OK')
                                {
                                    throw new \Exception($arrayResponseCIH['mensaje']);
                                }
                            }
                        }
                    }
                    $this->get('session')->getFlashBag()->add('success', 'Aprobacion de Contrato ejecutado con exito');
                    return $this->redirect($this->generateUrl('aprobacioncontrato_aprobar_contrato'));
                }
            }
            else
            {              
                throw new \Exception('Ya existe un cliente con la misma identificación, por favor corregir y volver a intentar');                            
            }
        }
        catch(\Exception $e)
        {
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->redirect($this->generateUrl('aprobacioncontrato_proceso_aprobar', array('id_persona'  => $idProspecto, 
                                                                                                  'id_contrato' => $id_contrato)));
        }
    }

    public function validarTarjetaCtaAction() {
		$respuesta = new Response();
        
        $peticion = $this->get('request');
        $tipoCuentaId = $peticion->get('tipoCuentaId');
		$bancoTipoCuentaId = $peticion->get('bancoTipoCuentaId');
		$numeroCtaTarjeta = $peticion->get('numeroCtaTarjeta');
		$codigoVerificacion = $peticion->get('codigoVerificacion');
		
		$em = $this->getDoctrine()->getManager('telconet');
		$valido = 1;
		$datos = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->findOneById($bancoTipoCuentaId);
		try {

			if ($datos != null)
			//si es tarjeta
			//if ($datos->getEsTarjeta() == 'S') {
				//validamos el numero de caracteres
				if ($datos->getTotalCodSeguridad() != null) {
					if (strlen($codigoVerificacion) != $datos->getTotalCodSeguridad()) {
						$valido = 0; $mensaje = "Código de verificación no valido";
					}
				}
				//validamos el numero de caracteres
				if ($datos->getTotalCaracteres() != null) {
					if (strlen($numeroCtaTarjeta) != $datos->getTotalCaracteres()) {
						$valido = 0; $mensaje = "Total de números invalido";
					}
				}
				//validamos que el numero empieze con los mismos caracteres				
				if ($datos->getCaracterEmpieza() != null) {
					if (substr($numeroCtaTarjeta, 0, strlen($datos->getCaracterEmpieza()) != $datos->getCaracterEmpieza())) {
						$valido = 0; $mensaje = "Número es invalido";
					}
				}
			//}
        }
		catch(\Exception $e) {		
			
		}		

		//Validar siempre!!! quitar despues de normalizar la data
		$valido = 1;
		if ($valido == 1)
			$mensaje = "";
		$respuesta = array('valida'=>$valido, 'msg'=>$mensaje);
		$response = new Response(json_encode($respuesta));
        $response->headers->set('Content-type', 'text/json');
		return $response;
	}

	     
     /**
     * Guarda el registro de Historial de cambio de forma de pago asociado a un contrato.
     * @author  telcos
     * @version 1.0
     * 
     * @author  Angel Reina <areina@telconet.ec>
     * @version 1.1 23-07-2019 - Se modifica función para que reciba array de parámetros. 
     * 
     * @param Entity $infoContratoFormaPago
     * @param String $user
     * 
     * 
     */
    private function guardarContratoFormaPagoHist($arrayParametros) 
    {

        $entityInfoContratoFormaPago = $arrayParametros["entityinfoContratoFormaPago"];
        $strUser                     = $arrayParametros["user"];
        $strIp                       = $arrayParametros["strIp"];
        $emComercial                 = $arrayParametros["entityContrato"];
        $intIdFormaPago              = $arrayParametros["idFormaPago"];
        $intMotivoId                 = $arrayParametros["intMotivo"];
        $intNumeroActa               = $arrayParametros["intNumeroActa"];
        $intFormaPagoActualId        = $arrayParametros["intFormaPagoActualId"];
        $strFactura                  = $arrayParametros["strFactura"];
        $strObservacion              = $arrayParametros["strObservacion"];
        $intPorcentajeDctoInst       = $arrayParametros['intPorcentajeDctoInst'];
        if ($entityInfoContratoFormaPago != null) 
        {
            $entityInfoContratoFormaPagoHist = new \telconet\schemaBundle\Entity\InfoContratoFormaPagoHist;
            $entityInfoContratoFormaPagoHist->setAnioVencimiento($entityInfoContratoFormaPago->getAnioVencimiento());
            $entityInfoContratoFormaPagoHist->setBancoTipoCuentaId($entityInfoContratoFormaPago->getBancoTipoCuentaId());
            $entityInfoContratoFormaPagoHist->setCedulaTitular($entityInfoContratoFormaPago->getCedulaTitular());
            $entityInfoContratoFormaPagoHist->setCodigoVerificacion($entityInfoContratoFormaPago->getCodigoVerificacion());
            $entityInfoContratoFormaPagoHist->setContratoId($entityInfoContratoFormaPago->getContratoId());
            $entityInfoContratoFormaPagoHist->setEstado($entityInfoContratoFormaPago->getEstado());
            $entityInfoContratoFormaPagoHist->setFeCreacion(new \DateTime('now'));
            $entityInfoContratoFormaPagoHist->setFeUltMod($entityInfoContratoFormaPago->getFeUltMod());
            $entityInfoContratoFormaPagoHist->setIpCreacion($strIp);
            $entityInfoContratoFormaPagoHist->setMesVencimiento($entityInfoContratoFormaPago->getMesVencimiento());
            $entityInfoContratoFormaPagoHist->setNumeroCtaTarjeta($entityInfoContratoFormaPago->getNumeroCtaTarjeta());
            $entityInfoContratoFormaPagoHist->setNumeroDebitoBanco($entityInfoContratoFormaPago->getNumeroDebitoBanco());
            $entityInfoContratoFormaPagoHist->setTipoCuentaId($entityInfoContratoFormaPago->getTipoCuentaId()->getId());
            $entityInfoContratoFormaPagoHist->setTitularCuenta($entityInfoContratoFormaPago->getTitularCuenta());
            $entityInfoContratoFormaPagoHist->setUsrCreacion($strUser);
            $entityInfoContratoFormaPagoHist->setUsrUltMod($entityInfoContratoFormaPago->getUsrUltMod());
            if(isset($intPorcentajeDctoInst))
            {
                $entityInfoContratoFormaPagoHist->setDctoAplicado($intPorcentajeDctoInst);
            }
            if ($intIdFormaPago != null) 
            {
                $entityInfoContratoFormaPagoHist->setFormaPago($intIdFormaPago->getId());
            }
            
            $entityInfoContratoFormaPagoHist->setMotivoId($intMotivoId);
            $entityInfoContratoFormaPagoHist->setNumeroActa($intNumeroActa);
            $entityInfoContratoFormaPagoHist->setFormaPagoActualId($intFormaPagoActualId);
            $entityInfoContratoFormaPagoHist->setFactura($strFactura);
            $entityInfoContratoFormaPagoHist->setObservacion($strObservacion);
            
            $emComercial->persist($entityInfoContratoFormaPagoHist);
            $emComercial->flush();


            $arrayParametros['intPagoDatosId']            = $entityInfoContratoFormaPagoHist->getId();
            
            /*Llama a la función que guarda los documentos digitales por cambio de forma de pago */
            $serviceInfoContrato = $this->get('comercial.InfoContrato');

            $objContrato         = $serviceInfoContrato->guardarDocumentosDigitales($arrayParametros);
            if( !(is_object($objContrato)) )
            {
                throw new \Exception("Error al guardar documento asociado a la forma de pago del contrato, "
                        . "           verificar con el departamento de sistemas.");
            }
            
        }
    }
    /**
     * Documentación para el método 'showLogFormaPagoContratoAction'.
     * Descripción: Función que retorna información del historial de las formas de pago de un cliente.
     *
     * @return object $response
     *
     * @author  telcos
     * @version 1.0
     * 
     * @author  Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 26-07-2017 - Se agregan formas de pago a omitir en variables que muestran información de datos bancarios. 
     * 
     * @author  Angel Reina <areina@telconet.ec>
     * @version 1.2 23-07-2019 - Se agregan los campos ( Nueva Forma Pago, Número de Acta, Motivo, enlaces para visualizar archivos digitales ) 
     * 
     */ 
    public function showLogFormaPagoContratoAction($idContrato) {
        $request = $this->getRequest();
        $start = $request->get('start', 0);
        $limit = $request->get('limit', 10);

        $response = new Response();
        $response->headers->set('Content-type', 'text/json');

        $em = $this->get('doctrine')->getManager('telconet');
        $reporsitory = $em->getRepository('schemaBundle:InfoContratoFormaPagoHist');
        $entities = $reporsitory->findBy(array('contratoId' => $idContrato), array('id' => 'DESC'), $limit, $start);
        $total = $reporsitory->findBy(array('contratoId' => $idContrato));

        $arrayResponse = array();
        $arrayResponse['total'] = count($total);
        $arrayResponse['logs'] = array();

        foreach ($entities as $entity) 
        {
            $arrayEntity = array();
            $arrayEntity['id'] = $entity->getId();
            if ($entity->getFormaPago() == 1  || 
                $entity->getFormaPago() == 2  ||
                $entity->getFormaPago() == 4  ||
                $entity->getFormaPago() == 11 ||
                $entity->getFormaPago() == 42 ||
                $entity->getFormaPago() == 101 ) 
            {
                $arrayEntity['titularCuenta'] = "N/A";
                $arrayEntity['bancoTipo'] = "N/A";
                $arrayEntity['bancoTipoCuenta'] = "N/A";
                $arrayEntity['numeroCtaTarjeta'] = "N/A";
            } 
            else 
            {
                $arrayEntity['titularCuenta'] = $entity->getTitularCuenta();
                $arrayEntity['bancoTipo'] = $entity->getBancoTipoCuentaId()->getBancoId()->getDescripcionBanco();
                $arrayEntity['bancoTipoCuenta'] = $entity->getBancoTipoCuentaId()->getTipoCuentaId()->getDescripcionCuenta();
                $arrayEntity['numeroCtaTarjeta'] = $entity->getNumeroCtaTarjeta();
            }

            $entityFormaPago = $em->getRepository('schemaBundle:AdmiFormaPago')->findOneById($entity->getFormaPago());
            $arrayEntity['formaPago'] = '';
            if($entityFormaPago != null) 
            {
                $arrayEntity['formaPago'] = $entityFormaPago->getDescripcionFormaPago();
            }

            
            $objFormaPago = $em->getRepository('schemaBundle:AdmiFormaPago')->findOneById($entity->getFormaPagoActualId());
            $arrayEntity['strFormaPagoActual'] = '';
            
            if($objFormaPago != null) 
            {
                $arrayEntity['strFormaPagoActual'] = $objFormaPago->getDescripcionFormaPago();
            }
            
            $arrayEntity['intNumeroActa'] = $entity->getNumeroActa();
            $arrayEntity['strNombreArchivoAbu'] = $entity->getNombreArchivoAbu();
            $objAdmiMotivo = $em->getRepository('schemaBundle:AdmiMotivo')->findOneById($entity->getMotivoId());
            $arrayEntity['strMotivo'] = $entity->getObservacion();
            if($objAdmiMotivo != null) 
            {
                $arrayEntity['strMotivo'] = $objAdmiMotivo->getNombreMotivo();
            }
            
            
            $arrayEntity['feCreacion'] = date_format($entity->getFeCreacion(), 'd-m-Y H:i:s');
            $arrayEntity['usrCreacion'] = $entity->getUsrCreacion();
            $arrayResponse['logs'][] = $arrayEntity;
        }
        $response->setContent(json_encode($arrayResponse));
        return $response;
    }
        /** 
    * Descripcion: Metodo encargado de eliminar masivamente documentos a partir del id de la referencia enviada
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 29-07-2014   
    * @param integer $id 
    * @return json con resultado del proceso   
    */
    public function eliminarDocumentoAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion  = $this->get('request');
        $parametro = $peticion->get('id');
        if(isset($parametro))
        {
	    $arrayValor = explode("|",$parametro);
        }
        else
	{
	    $parametro  = $peticion->get('param');
	    $arrayValor = explode("|",$parametro);
	} 
        $emComunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");       
        foreach($arrayValor as $id)
        {            
            $objInfoDocumento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($id);                                              
            if( $objInfoDocumento )
            {                                  
                 /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
                $serviceInfoContrato = $this->get('comercial.InfoContrato');
                $entity              = $serviceInfoContrato->eliminarDocumento($id);
                $respuesta->setContent("Se elimino el registro");
             }
             else
                 $respuesta->setContent("No existe el registro");                           
        }   
        return $respuesta;                    
    } 

    /**
    * Funcion para el ingreso de Nuevos Archivos Digitales al Contrato
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 25-07-2014      
    * @see \telconet\schemaBundle\Entity\InfoContrato
    * @return Renders a view.
    * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
    * @version 1.1 26-09-2022 -  Se adiciona el envio del idAdendum para identificar el archivo a subir.
    */	
    public function newArchivoDigitalAction($intId, $intIdAdendum)
    {
        $request                     = $this->getRequest();        
        $arrayCliente                = $request->getSession()->get('cliente');               
        $strNombreCliente            = null;
        $intIdCliente                = null;
        $strTipoRol                  = null;
        $strEstadoCliente            = null;      
        $boolTieneServiciosFactibles = false;
        $em                          = $this->getDoctrine()->getManager();		
        $emGeneral                   = $this->getDoctrine()->getManager('telconet_general');		
        $emComunicacion              = $this->getDoctrine()->getManager('telconet_comunicacion');		        
        if($arrayCliente)
        {            
            $intIdCliente     = $arrayCliente['id'];
            $strTipoRol       = $arrayCliente['nombre_tipo_rol'];
            $strEstadoCliente = $arrayCliente['estado'];
            if( $arrayCliente['razon_social'] )
                $strNombreCliente = $arrayCliente['razon_social'];
            else 
                $strNombreCliente = $arrayCliente['nombres'].' '.$arrayCliente['apellidos'];
        }                 
        $objInfoContrato             = $em->getRepository('schemaBundle:InfoContrato')->find($intId);        
        $objInfoDocumento            = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->findByContratoId($intId);        
        $objPersonaEmpresaRol        = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($objInfoContrato->getPersonaEmpresaRolId()->getId());	    
        $objUltimoEstado             = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->findUltimoEstadoPorPersonaEmpresaRol($objPersonaEmpresaRol->getId());        
        $intIdUltimoEstado           = $objUltimoEstado[0]['ultimo'];
        $objUltimoEstado             = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->find($intIdUltimoEstado);
        $strEstadoCliente            = $objUltimoEstado->getEstado();		    
        $objPuntos                   = $em->getRepository('schemaBundle:InfoPunto')->findByPersonaEmpresaRolId($objPersonaEmpresaRol->getId());
        if($intIdAdendum)
        {
            $strTipoRol       = 'Pre-cliente';  
        }
        foreach( $objPuntos as $punto )
        {
            $arrayPunto = $em->getRepository('schemaBundle:InfoServicio')->findServiciosFactiblesPendientes($punto->getId(),0,999999);
            if( $arrayPunto['total']>0 )
            {
                $boolTieneServiciosFactibles = true;
                break;

            }
        }
        	
        $arrayTipoDocumentos = array();
        $objTiposDocumentos  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')->findByEstado("Activo");                   
        foreach ( $objTiposDocumentos as $objTiposDocumentos )
        {   
           $arrayTipoDocumentos[$objTiposDocumentos->getId()] = $objTiposDocumentos->getDescripcionTipoDocumento();
        }    
        
        
        $form_documentos                           = $this->createForm(new InfoDocumentoType(array('validaFile'=>true,
                                                     'arrayTipoDocumentos'=>$arrayTipoDocumentos)), new InfoDocumento());
        $parametros                                = array('form_documentos' => $form_documentos->createView());   
        $parametros['objInfoContrato']             = $objInfoContrato;
        $parametros['objPersonaEmpresaRol']        = $objPersonaEmpresaRol;
        $parametros['idClienteSesion']             = $intIdCliente;
        $parametros['nombreClienteSesion']         = $strNombreCliente;
        $parametros['tipoRolClienteSesion']        = $strTipoRol;
        $parametros['estadoCliente']               = $strEstadoCliente;
        $parametros['boolTieneServiciosFactibles'] = $boolTieneServiciosFactibles;
        $parametros['objInfoDocumento']            = $objInfoDocumento;
        $parametros['arrayTipoDocumentos']         = $arrayTipoDocumentos;
        $parametros['idAdendum']                   = $intIdAdendum;
        
         return $this->render('comercialBundle:infocontrato:newArchivoDigital.html.twig',$parametros);       
    }
    /**
    * Funcion que Guarda Archivos Digitales agregados al contrato 
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @param request $request  
    * @param interger $id // id del contrato       
    * @version 1.0 26-07-2014          
    * @return a RedirectResponse to the given URL.
    *
    * @author Jorge Luis Veliz <jlveliz@telconet.ec>
    * @since 20-06-2021
    * @version 1.1
    * almacenar los archivos digitales en el microservicio NFS
    *

    */	    
    public function guardarArchivoDigitalAction(Request $objRequest,$intId,$intIdAdendum)
    {       
        $intClientIp         = $objRequest->getClientIp();
        $objSession             = $objRequest->getSession();
        $strUsrCreacion      = $objSession->get('user');
        $strCodEmpresa       = $objSession->get('idEmpresa');                      
        $objDatosFormFiles    = $objRequest->files->get('infodocumentotype');               
        $objDatosFormTipos    = $objRequest->get('infodocumentotype');
        $intKey                 = key($objDatosFormTipos);        
        $arrayTipoDocumentos = array ();        
        foreach ($objDatosFormTipos as $intKey => $tipos)
        {                           
            foreach ( $tipos as $key_tipo => $value)
            {                     
                $arrayTipoDocumentos[$key_tipo]=$value;                
            }
        }             
        $datos_form = array_merge(               
        $objRequest->get('infocontratoextratype'),array('datos_form_files' => $objDatosFormFiles),
                                               array('arrayTipoDocumentos' => $arrayTipoDocumentos));        
        try
        {
            /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
            $serviceInfoContrato = $this->get('comercial.InfoContrato');
            $arrayParametros = array();
            $arrayParametros['id']             = $intId;
            $arrayParametros['idAdendum']      = $intIdAdendum;
            $arrayParametros['strCodEmpresa']  = $strCodEmpresa;
            $arrayParametros['strUsrCreacion'] = $strUsrCreacion;
            $arrayParametros['intClientIp']    = $intClientIp;
            $arrayParametros['datos_form']     = $datos_form;

            $objContrato = $serviceInfoContrato->guardarArchivoDigitalNfs($arrayParametros);
            $arrayParams = array();
            $arrayParams['idContrato']             = $intId;
            $arrayParams['idAdemdun']              = $intIdAdendum;
            $arrayParams['strUsrCreacion']         = $strUsrCreacion;
            $serviceInfoContrato->actualizarDocumentoCaracteristica($arrayParams);
            return $this->redirect($this->generateUrl('infocontrato_show', array('id' => $objContrato->getId())));

        }
        catch (\Exception $e)
        {   
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->redirect($this->generateUrl('infocontrato_show', array('id'=>$intId)));
        }
    }
    /**
    * Funcion en Ajax que lista los archivos digitales asociados al contrato
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 28-07-2014    
    * @param integer $idContrato
    * @param integer $limit
    * @param integer $page
    * @param integer $start
    * @see \telconet\schemaBundle\Entity\InfoDocuemnto
    * @return \Symfony\Component\HttpFoundation\Response
    *
    * bug.- Se corrige para mostrar imagenes de contrato en estructura de adendums, si el punto es nulo no mostrar el login
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.1 14-Ene-2020
    *
    * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
    * @version 1.2 16-03-2020 Se agrega lista con los puntos del contrato para filtrar imágenes digitales.
    *
    * @author Walther Joao Gaibor <wgaibor@telconet.ec>
    * @version 1.3 28-09-2020 En el caso de que el archivo se encuentre almacenado en el servidor NFS, debe
    *                         poder resolver dicho link.
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.4 07-01-2020 Si existe la imagen pero no esta asociada en la info_adendum, no se pone adendum ni login al registro
    * 
    * @author Jorge Veliz <jlveliz@telconet.ec>
    * @version 1.5 07-01-2020 resolver las url por archivo que se encuntren almaceando en el nfs
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.6 23-08-2022 Se agrega columna tipo doc cuando la empresa es MD, se modifica la manera de obtener el origen y el tipo
    * 
    * @author William Anchundia Soza <jwanchundia@telconet.ec>
    * @version 1.7 08-05-2023 Se modifico la tabla de donde se obtiene el tipo de documento -> desde linea 5179
    * 
    * @author William Anchundia Soza <jwanchundia@telconet.ec>
    * @version 1.8 15-05-2023 Se realizaron cambios en la consulta de contrato-> desde linea 5179
    *
    * @author William Anchundia Soza <jwanchundia@telconet.ec>
    * @version 1.9 29-05-2023 No se estaban visualizando todos los documentos -> veririfacion en la tabla infoPunto
    *
    * @author William Anchundia Soza <jwanchundia@telconet.ec>
    * @version 1.10 14-06-2023 No se estaban visualizando todos los documentos -> veririfacion en la tabla infoPunto
    *
    * @author Jefferson Alexy Carrillo <jacarrillotelconet.ec>
    * @version 1.11 22-06-2023 Se corrije visualizacion de documentos para TN
    *
    * @author Jonathan Burgos <jsburgos@telconet.ec>
    * @version 1.12 07-06-2023 No se visualiza las facturas del cliente, se ajusta funcion para poder visualizar los documentos.
    */
    
    /**
    * @Secure(roles="ROLE_60-1917")
    */    
    
    public function showDocumentosContratoAction($idContrato) 
    {
        $objRequest   = $this->getRequest();
        $objSession   = $objRequest->getSession();
        $intStart     = intval($objRequest->get('start', 0));
        $intLimit     = intval($objRequest->get('limit', 10)); 
        $strIdEmpresa = $objRequest->getSession()->get('idEmpresa');
        $strUsuario   = $objRequest->getSession()->get('user');
        $serviceUtil  = $this->get('schema.Util');

        $objResponse  = new Response();
        
        $emGeneral      = $this->getDoctrine()->getManager('telconet_general');		
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');	
        $emComercial    = $this->getDoctrine()->getManager('telconet');	 
     try 
        {
            $serviceSeguridad     =  $this->get('seguridad.Seguridad');            
            $boolAuditorSenior =  $serviceSeguridad->isAccesoLoginPerfil($objSession, 'Md_Auditor_Senior');   
        
            $objInfoDocumento = $emComunicacion->getRepository('schemaBundle:InfoDocumento');

            $arrayParametrosD = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('CONTRATO_ARCHIVOS_NO_VISIBLE',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '');
            $arrayExtensiones = explode(',',$arrayParametrosD['valor1']);

            $arrayResponse          = array();
            $arrayResponse['logs']  = array();
            $arrayResponse['total'] = 0;

            $arrayEstados = array('Activo'); 
            if ($boolAuditorSenior ) 
            {
              array_push( $arrayEstados , 'Invalido');
            }
            $arrayDocumentos = $objInfoDocumento->findBy(array('contratoId' => $idContrato, 
                                                               'estado'     =>   $arrayEstados ), 
                                                         array('id' => 'DESC'), 
                                                         $intLimit, 
                                                         $intStart);
            
            $arrayDocumentosTotal = $objInfoDocumento->findBy(array('contratoId' => $idContrato, 
                                                                    'estado'     =>   $arrayEstados ));
            
            $arrayResponse['total'] = count($arrayDocumentosTotal);

            $objContrato = $emComercial->getRepository('schemaBundle:InfoContrato')->find($idContrato);
            $strCodEmpresa = $objContrato->getPersonaEmpresaRolId()->getEmpresaRolId()->getEmpresaCod()->getId();
            $strLoginPunto     = "";
            foreach ($arrayDocumentos as $objDocumento)
            {
                if(is_object($objDocumento->getTipoDocumentoId()) && 
                   !empty($arrayExtensiones) &&
                  in_array($objDocumento->getTipoDocumentoId()->getExtensionTipoDocumento(), $arrayExtensiones) )
                {
                    $arrayResponse['total'] = count($arrayDocumentosTotal) - 1;
                }
                else
                {
                    $arrayEntity                             = array();
                    $arrayEntity['id']                       = $objDocumento->getId();
                    $arrayEntity['ubicacionLogicaDocumento'] = $objDocumento->getUbicacionLogicaDocumento();

                    $objTipoDocumentoGeneral = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                        ->find($objDocumento->getTipoDocumentoGeneralId());

                    if (is_object($objTipoDocumentoGeneral))
                    {
                        $strUrlUbicacionFisica               = $objDocumento->getUbicacionFisicaDocumento();
                        $intDocumentoId = $objDocumento->getId();

                        if(isset($strUrlUbicacionFisica) && filter_var($strUrlUbicacionFisica, FILTER_VALIDATE_URL))
                        {
                            $strUrlVerDocumento         =  $this->generateUrl('infocontrato_descargarDocumento', array('id' =>$intDocumentoId ));
                            $strUrlEliminarDocumento    =  $this->generateUrl('infocontrato_eliminarDocumento', array('id' => $intDocumentoId ));

                        }
                        else
                        {
                            $strUrlVerDocumento      = $this->generateUrl('infocontrato_descargarDocumento', array('id' => $intDocumentoId));
                            $strUrlEliminarDocumento = $this->generateUrl('infocontrato_eliminarDocumento', array('id' => $intDocumentoId));

                        }
                        $arrayEntity['tipoDocumentoGeneral'] = $objTipoDocumentoGeneral->getDescripcionTipoDocumento();
                    }

                    $strTipoContrato   = "FISICO";
                    $strOrigenContrato = "WEB";
                    $strLoginPunto     = "";
                    $strTipoDoc        = "";

                    if ($strCodEmpresa == "18" || $strCodEmpresa == "33")
                    {
                        $entityDocumentoRelacion = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')
                                                                  ->findOneBy(array('documentoId' => $objDocumento->getId(),
                                                                                    'estado'      =>   $arrayEstados ));
                                                                                    

                 


                        $entityAdendum = $emComercial->getRepository('schemaBundle:InfoAdendum')
                        ->findOneBy(array( 'numero' => $entityDocumentoRelacion->getNumeroAdendum(),
                                          'contratoId'=> $idContrato
                                          )); 
 
                        if (is_object($entityAdendum))
                        {
                            $entityPunto = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                       ->find($entityAdendum->getPuntoId());
                          
                            $objEntityCaract = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(
                                    array("descripcionCaracteristica"   => 'FORMA_REALIZACION_CONTRATO',
                                            "estado"                    => 'Activo'));
                            $objContrato  = $emComercial->getRepository('schemaBundle:InfoContrato')
                                                        ->find($idContrato);

                            $entityCaractContrato = $emComercial->getRepository('schemaBundle:InfoContratoCaracteristica')
                                                            ->findOneBy(
                                                                array("caracteristicaId"  => $objEntityCaract,
                                                                        "contratoId"      => $objContrato,
                                                                        "estado"          => "Activo"
                                                                ));

                            if (is_null($entityAdendum->getFormaContrato()))
                            {                  
                                if(is_object($entityCaractContrato))
                                {
                                    $strTipoDoc = $entityCaractContrato->getValor1();
                                }
                                else
                                {
                                    $strTipoDoc = "DIGITAL";
                                }                                                                             
                            }
                            else
                            {
                                $strTipoDoc = $entityAdendum->getFormaContrato();
                            }                                     
                                   
                        }



                        $strTipoContrato    = "Contrato";
                        $strOrigenContrato  = ($objContrato->getOrigen()) ? $objContrato->getOrigen() : "WEB";

                        if (is_object($entityDocumentoRelacion) && !is_null($entityDocumentoRelacion->getNumeroAdendum()))
                        {
                            $entityAdendum = $emComercial->getRepository('schemaBundle:InfoAdendum')
                                                         ->findOneBy(array('contratoId' => $idContrato,
                                                                           'numero'     => $entityDocumentoRelacion->getNumeroAdendum()));

                            if (is_object($entityAdendum) && is_object($entityPunto))
                            {
                                $entityPunto = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                           ->find($entityAdendum->getPuntoId());
                            }

                            if ($entityAdendum)
                            {
                                $objEntityCaract = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                ->findOneBy(
                                                                array("descripcionCaracteristica"   => 'FORMA_REALIZACION_ADENDUM',
                                                                        "estado"                    => 'Activo'));
                                $objContrato  = $emComercial->getRepository('schemaBundle:InfoContrato')
                                                            ->find($idContrato);
                
                                $entityCaractContrato = $emComercial->getRepository('schemaBundle:InfoContratoCaracteristica')
                                                                ->findOneBy(
                                                                    array("caracteristicaId"  => $objEntityCaract,
                                                                          "contratoId"        => $objContrato,
                                                                          "estado"            => "Activo"
                                                                    ));
                                if (is_null($entityAdendum->getFormaContrato()))
                                {                  
                                    if(is_object($entityCaractContrato))
                                    {
                                        $strTipoDoc = $entityCaractContrato->getValor1();
                                    }
                                    else
                                    {
                                        $strTipoDoc = "DIGITAL";
                                    }                                                                             
                                }
                                else
                                {
                                    $strTipoDoc = $entityAdendum->getFormaContrato();
                                }                                 
                

                                $strTipoContrato = $entityAdendum->getTipo() == "AP" ? "Adendum de Punto" : "Adendum de Servicio";
                            }
                        }

                        if (is_object($entityPunto) && ($entityAdendum))
                        {
                            $strLoginPunto = $entityPunto->getLogin();
                        }
                        else
                        {
                            if(is_object($entityDocumentoRelacion))
                            {
                                $intIdPuntoDoc = $entityDocumentoRelacion->getPuntoId();
                                if(!empty($intIdPuntoDoc))
                                {
                                    $entityPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPuntoDoc);
                                    if(is_object($entityPunto))
                                    {
                                        $strLoginPunto = $entityPunto->getLogin();
                                    }  
                                }
                            }
                            if(empty($strLoginPunto))
                            {
                                $entityPunto = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                           ->findOneBy(array("personaEmpresaRolId" => $objContrato->getPersonaEmpresaRolId(),
                                                                              "estado"              => "Activo"));
                                if(is_object($entityPunto))
                                {
                                    $strLoginPunto = $entityPunto->getLogin();
                                }
                            }
                        }
                        
                    }

                    $arrayEntity['tipoContrato']          = $strTipoContrato;
                    $arrayEntity['origen']                = $strOrigenContrato;
                    $arrayEntity['login']                 = $strLoginPunto;
                    $arrayEntity['feCreacion']            = date_format($objDocumento->getFeCreacion(), 'd-m-Y H:i:s');
                    $arrayEntity['usrCreacion']           = $objDocumento->getUsrCreacion();
                    $arrayEntity['linkVerDocumento']      = $strUrlVerDocumento;
                    $arrayEntity['linkEliminarDocumento'] = $strUrlEliminarDocumento;
                    $arrayEntity['codEmpresa']            = $strCodEmpresa;
                    $arrayEntity['tipoDoc']               = !empty($strTipoDoc) ? $strTipoDoc : "DIGITAL";
                    $arrayEntity['estado']                = $objDocumento->getEstado(); 

                    if (isset($strLoginPunto) || $strCodEmpresa == "10" )
                    {
                        $arrayResponse['logs'][]          = $arrayEntity;
                    }   
                }
            }
        }
        catch(\Exception $objException)
        {
            $serviceUtil->insertLog(array('enterpriseCode'   => $strIdEmpresa,
                                          'logType'          => 1,
                                          'logOrigin'        => 'TELCOS',
                                          'application'      => basename(__FILE__),
                                          'appClass'         => basename(__CLASS__),
                                          'appMethod'        => basename(__FUNCTION__),
                                          'descriptionError' => $objException->getMessage(),
                                          'status'           => 'Fallido',
                                          'inParameters'     => $objException->getTraceAsString(),
                                          'creationUser'     => $strUsuario));
        }
        
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent(json_encode($arrayResponse));
        return $objResponse;
    } 

    /** 
    * Descripcion: Metodo encargado de eliminar documentos a partir del id de la referencia enviada
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 29-07-2014   
    * @param integer $id 
    * @return json con resultado del proceso   
    */
    public function eliminarDocumentoAction($id)
    {   
        $em               = $this->getDoctrine()->getManager("telconet_comunicacion");
        $objInfoDocumento = $em->getRepository('schemaBundle:InfoDocumento')->find($id);           
        if ( !$objInfoDocumento ) 
        {
            throw $this->createNotFoundException('Unable to find InfoDocumento entity.');
        }                        
        if( $objInfoDocumento )
        {            
            try
            {
                /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
                $serviceInfoContrato = $this->get('comercial.InfoContrato');
                $entity              = $serviceInfoContrato->eliminarDocumento($id);                
                return $this->redirect($this->generateUrl('infocontrato_show', array('id'=>$objInfoDocumento->getContratoId())));        
            }
            catch (\Exception $e)
            {   
                $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
                return $this->redirect($this->generateUrl('infocontrato_show', array('id'=>$objInfoDocumento->getContratoId())));        
            }
        }      
    }
    
    /**   
    * Descripcion: Metodo encargado de descargar los documentos a partir del id de la referencia enviada
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 25-07-2014
    *
    * @author  Néstor Naula <nnaulal@telconet.ec>
    * @version 1.1 23-10-2020 - Se modifica para que no coloque la ruta del parámetro 
    *                           cuando la ruta fisica contenga http 
    *
    * @param integer $id
    * @return json con resultado del proceso   
    */
    public function descargarDocumentoAction($id)
    {
        $request           = $this->getRequest();	
        $em                = $this->getDoctrine()->getManager("telconet_comunicacion");
        $objInfoDocumento  = $em->getRepository('schemaBundle:InfoDocumento')->find($id);               
        $path              = $objInfoDocumento->getUbicacionFisicaDocumento();       
        $path_telcos       = $this->container->getParameter('path_telcos');
        $strPathContrato   = $path;
        if(!(strpos($path, 'http') !== false))
        {
            $strPathContrato   = $path_telcos.$path;
        }
        $objContent        = file_get_contents($strPathContrato);        
        $response          = new Response();
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$objInfoDocumento->getUbicacionLogicaDocumento());
        $response->setContent($objContent);
        return $response;       
    }
   
     /**
     * Funcion encargada de validar los numeros de Cuenta o Tarjetas de Credito
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 08-02-2015     
     * @param integer $intTipoCuentaId     
     * @param integer $intBancoTipoCuentaId     
     * @param string $strNumeroCtaTarjeta     
     * @param string $strCodigoVerificacion     
     * 
     * Actualización: Se modifica envio de parametros en $arrayParametrosValidaCtaTarj a la función validarNumeroTarjetaCta
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 07-07-2017
     * 
     * Actualización: Se agregan dos elmentos mas al arreglo que se envía al
     * servicio que valida la tarjeta estos son: token y el id de la forma de pago
     * @author Christian Yunga <cyungat@telconet.ec>
     * @version 1.2 25-11-2022
     * 
     * @see \telconet\schemaBundle\Entity\AdmiBancoTipoCuenta
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function validarNumeroTarjetaCtaAction()
    {
        $objRequest            = $this->getRequest();
        $arrayValidaciones     = array();
        $objSession            = $objRequest->getSession();
        $strCodEmpresa         = $objSession->get('idEmpresa');
        $intTipoCuentaId       = $objRequest->get("tipoCuentaId");
        $intBancoTipoCuentaId  = $objRequest->get("bancoTipoCuentaId");
        $strNumeroCtaTarjeta   = $objRequest->get("numeroCtaTarjeta");
        $strCodigoVerificacion = $objRequest->get("codigoVerificacion");
        $intFormaPagoId        = $objRequest->get("formaPagoId");

        try
        {
            /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
            $serviceInfoContrato = $this->get('comercial.InfoContrato');

            


            $arrayParametrosValidaCtaTarj                          = array();
            $arrayParametrosValidaCtaTarj['intTipoCuentaId']       = $intTipoCuentaId;
            $arrayParametrosValidaCtaTarj['intBancoTipoCuentaId']  = $intBancoTipoCuentaId;
            $arrayParametrosValidaCtaTarj['strNumeroCtaTarjeta']   = $strNumeroCtaTarjeta;
            $arrayParametrosValidaCtaTarj['strCodigoVerificacion'] = $strCodigoVerificacion;
            $arrayParametrosValidaCtaTarj['strCodEmpresa']         = $strCodEmpresa;
            $arrayParametrosValidaCtaTarj['intFormaPagoId']        = $intFormaPagoId;


            $arrayValidaciones   = $serviceInfoContrato->validarNumeroTarjetaCta($arrayParametrosValidaCtaTarj);
            if($arrayValidaciones)
            {
                $response = new Response(json_encode(array('msg' => 'ok', 'validaciones' => $arrayValidaciones)));
            }
            else
            {
                $response = new Response(json_encode(array('msg' => 'No existen datos')));
            }
            $response->headers->set('Content-type', 'text/json');
            return $response;
        }
        catch(\Exception $e)
        {
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            $response = new Response(json_encode(array('msg' => 'No existen datos')));
            $response->headers->set('Content-type', 'text/json');
            return $response;
        }
    }

    /**
     * Funcion para obtener si el registro del bancoTipoCuentaId de un contrato corresponde
     * a una cuenta Bancaria o a una tarjeta de credito, usado para validar el ingreso de campos. 
     * como anio y mes de vencimiento y codigo de verificacion en el caso de tarjetas.    
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 12-02-2015          
     * @param integer $intBancoTipoCuentaId          
     * @see \telconet\schemaBundle\Entity\AdmiBancoTipoCuenta
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function validarPorFormaPagoAction()
    {
        $request                = $this->getRequest();
        $intBancoTipoCuentaId   = $request->get("bancoTipoCuentaId");
        $em                     = $this->getDoctrine()->getManager('telconet');
        $objAdmiBancoTipoCuenta = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($intBancoTipoCuentaId);
       
        if($objAdmiBancoTipoCuenta)
        {
            if($objAdmiBancoTipoCuenta->getEsTarjeta() == 'S')
            {
                $arreglo = array('msg' => 'TARJETA');
            }
            else
            {
                $arreglo = array('msg' => 'BANCO');
            }
        }
        else
        {
            $arreglo = array('msg' => 'No existe banco o tarjeta asociado');
        }

        $response = new Response(json_encode($arreglo));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /**
    * @Secure(roles="ROLE_164-154")
    * 
    * Documentación para el método 'aprobarContratoCambioRazonSocialAction'.
    * Funcion para ejecutar el proceso de aprobacion del contrato para el caso de contratos generados por Cambio de Razon Social por Punto o Login
    * 1) Precargara en pantalla la informacion de la persona, 
    * 2) Precargara la informacion del contrato(forma de Pago) que podra ser editable previo a la aprobacion del contrato en caso de requerirlo.  
    *    Se agrega descencriptado del campo Numero_Cta_tarjeta para que pueda ser editable por el Administrador de Contratos.  
    * 3) Grid con los servicios de todos los puntos Logines origenes del Cambio de Razon social 
    *         
    * @param integer $id_persona
    * @param integer $id_contrato         
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 02-10-2015   
    *
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.1 06-07-2016    
    * Se cambia llamada a metodo que obtiene Rol de Pre-cliente
    * getPersonaEmpresaRolPorPersonaPorTipoRol por getPersonaEmpresaRolPorPersonaPorTipoRolActivosPendientes
    * ya que se requiere la verificacion por estados del ROL para el caso de clientes Reingresados 
    * 
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.2 01-09-2017
    * - Se agrega variable intIdPais para obtener pais que esta en sesion.
    * - Se agrega el pais al type InfoContratoFormaPagoEditType para poder cargar los tipos de cuenta. 
    *
    * @author Néstor Naula <nnaulal@telconet.ec>
    * @version 1.3 25-11-2020 - Se agrega los contactos del cliente
    * @since 1.2
    * 
    * @return a RedirectResponse to the given URL.
    */	
     public function aprobarContratoCambioRazonSocialAction($id_persona, $id_contrato)
    {
        $arrayParametros = array(
                'entityContrato'      => '',
                'edit_form'           => '',
                'delete_form'         => '',
                'clase'               => '',
                'formFormaPago'       => '',
                'strNumeroCtaTarjeta' => '',
                'bancoTipoCuentaId'   => '',
                'id_per_emp_rol'      => '',
                'form'                => '',
                'direccionTributaria' => '',
                'entity'              => '');
         
        try
        {              
            /* @var $service InfoContratoAprobService */
            $service             = $this->get('comercial.InfoContratoAprob');            
            $objInfoPersonaAprob = $service->getDatosPersonaId($id_persona);

            $objInfoPersona                    = new InfoPersona();
            $options['identificacion']         = $objInfoPersonaAprob->getIdentificacionCliente();
            $options['razonSocial']            = $objInfoPersonaAprob->getRazonSocial();
            $options['nombres']                = $objInfoPersonaAprob->getNombres();
            $options['apellidos']              = $objInfoPersonaAprob->getApellidos();
            $options['direccion']              = $objInfoPersonaAprob->getDireccion();
            $options['tipoEmpresa']            = $objInfoPersonaAprob->getTipoEmpresa();
            $options['tipoIdentificacion']     = $objInfoPersonaAprob->getTipoIdentificacion();
            $options['tipoTributario']         = $objInfoPersonaAprob->getTipoTributario();
            $options['nacionalidad']           = $objInfoPersonaAprob->getNacionalidad();
            $options['direccionTributaria']    = $objInfoPersonaAprob->getDireccionTributaria();
            $options['calificacionCrediticia'] = $objInfoPersonaAprob->getCalificacionCrediticia();
            $options['genero']                 = $objInfoPersonaAprob->getGenero();
            $options['estadoCivil']            = $objInfoPersonaAprob->getEstadoCivil();            
            $options['origenIngresos']         = $objInfoPersonaAprob->getOrigenIngresos();
            $options['fechaNacimiento']        = $objInfoPersonaAprob->getFechaNacimiento();
            $options['representanteLegal']     = $objInfoPersonaAprob->getRepresentanteLegal();
            if( $objInfoPersonaAprob->getTituloId() )
            {
                $options['titulo'] = $objInfoPersonaAprob->getTituloId();
            }
            else
            {
                $options['titulo'] = "";
            }
             // Campos Nuevos CONTRIBUYENTE_ESPECIAL,PAGA_IVA, NUMERO_CONADIS
            $request                           = $this->getRequest();
            $session                           = $request->getSession();	
            $strEmpresaId                      = $request->getSession()->get('idEmpresa');
            $strPrefijoEmpresa                 = $request->getSession()->get('prefijoEmpresa');
            $intIdPais                         = $request->getSession()->get('intIdPais');
            $options['empresaId']              = $strEmpresaId;            
            $options['contribuyenteEspecial']  = $objInfoPersonaAprob->getContribuyenteEspecial();
            $options['pagaIva']                = $objInfoPersonaAprob->getPagaIva();
            $options['numeroConadis']          = $objInfoPersonaAprob->getNumeroConadis();
            
            $strTieneNumeroConadis = 'N';
            if($objInfoPersonaAprob->getNumeroConadis()!=null && $objInfoPersonaAprob->getNumeroConadis()!='')
            {
                $strTieneNumeroConadis = 'S';
            }                
	        $options['tieneNumeroConadis'] = $strTieneNumeroConadis;
            
            // Campo OFICINA_FACTURACION , ES_PREPAGO
            $options['oficinaFacturacion'] = null;
            $options['esPrepago']          = null;
            $em                            = $this->getDoctrine()->getManager('telconet');
            $objPersonaEmpresaRol          = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                             ->getPersonaEmpresaRolPorPersonaPorTipoRolActivosPendientes($id_persona,'Pre-cliente', $strEmpresaId);        
            if($strPrefijoEmpresa == 'TN')
            {
                if($objPersonaEmpresaRol)
                {
                    $intOficinaFacturacionId = $objPersonaEmpresaRol->getOficinaId();
                    $intOficinaFacturacion   = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($intOficinaFacturacionId);
                    $options['oficinaFacturacion'] = $intOficinaFacturacion;
                }
            }
            
            if($objPersonaEmpresaRol!= null && $objPersonaEmpresaRol->getEsPrepago()!=null && $objPersonaEmpresaRol->getEsPrepago()!='')
            {
                $strEsPrepago = $objPersonaEmpresaRol->getEsPrepago();
                $options['esPrepago'] = $strEsPrepago;
            }              	        

            // Saco los datos del contrato => forma de Pago para permitir editar datos como numero de tarjeta, banco, anio y mes de vencimiento etc            
            $objInfoContrato       = $service->getDatosContratoId($id_contrato);
            $editForm              = $this->createForm(new InfoContratoType(array('validaFile' => true)), $objInfoContrato);
            $deleteForm            = $this->createDeleteForm($id_contrato);
            $intBancoTipoCuentaId  = null;
           
             //Construyo el form
            $arrayParametros['entityContrato']      = $objInfoContrato;
            $arrayParametros['edit_form']           = $editForm->createView();
            $arrayParametros['delete_form']         = $deleteForm->createView();
            $form                                   = $this->createForm(new ProcesoAprobarContratoType($options), $objInfoPersona);
            $arrayParametros['form']                = $form->createView();
            $arrayParametros['direccionTributaria'] = $objInfoPersonaAprob->getDireccionTributaria();
            $arrayParametros['entity']              = $objInfoPersonaAprob;
            $arrayParametros['prefijoEmpresa']      = $strPrefijoEmpresa;
            
            //si posee forma de pago diferente de efectivo
            $boolNoExiste = 0;
            if( $objInfoContrato != null && $objInfoContrato->getFormaPagoId()->getDescripcionFormaPago() != "Efectivo" )
            {                                
                $arrayParametros['clase'] = "";                
                $objFormFormaPago         = $service->getDatosContratoFormaPagoId($id_contrato);

                if( $objFormFormaPago )
                {
                    $pagoForm                         = $this->createForm(
                                                                          new InfoContratoFormaPagoEditType(array("intIdPais"=>$intIdPais)), 
                                                                          $objFormFormaPago
                                                                         );
                    $arrayParametros['formFormaPago'] = $pagoForm->createView();
                    $intBancoTipoCuentaId             = $objFormFormaPago->getBancoTipoCuentaId()->getId();
                    
                    // Descencripto el campo Numero_Cta_Tarjeta
                    /* @var $serviceCrypt \telconet\seguridadBundle\Service\CryptService */
                    $serviceCrypt        = $this->get('seguridad.Crypt');
                    $strNumeroCtaTarjeta = $serviceCrypt->descencriptar($objFormFormaPago->getNumeroCtaTarjeta());

                    if($strNumeroCtaTarjeta)
                    {
                        $arrayParametros['strNumeroCtaTarjeta'] = $strNumeroCtaTarjeta;
                    }
                    else
                    {
                        throw new \Exception('No fue posible mostrar el Numero de Cuenta / Tarjeta - No puede realizar Aprobacion de Contrato'); 
                    }
                }
                else
                {
                    $boolNoExiste             = 1;
                    $arrayParametros['clase'] = "campo-oculto";
                }
            }
            else
            {
                $arrayParametros['clase'] = "campo-oculto";
                $boolNoExiste             = 1;
            }
            $arrayParametros['bancoTipoCuentaId'] = $intBancoTipoCuentaId;

            if($boolNoExiste == 1)
            {
                $objFormaPago                     = new InfoContratoFormaPago();
                $formInfoPago                     = $this->createForm(
                                                                      new InfoContratoFormaPagoEditType(array("intIdPais"=>$intIdPais)), 
                                                                      $objFormaPago
                                                                     );
                $arrayParametros['formFormaPago'] = $formInfoPago->createView();
            }
            $arrayParametros['id_per_emp_rol'] = $objInfoContrato->getPersonaEmpresaRolId()->getId();  
            $arrayParametros['idper']          = $objInfoContrato->getPersonaEmpresaRolId()->getId(); 
            $arrayParametros['arrayTelefonosCliente'] = $this->obtenerTelefonoCliente(
                array('intPersonaEmpresaRolId' => $objInfoContrato->getPersonaEmpresaRolId()->getId()
            ));        
                                   
            return $this->render('comercialBundle:infocontrato:aprobarContratoCambioRazonSocial.html.twig', $arrayParametros);
        }
        catch (\Exception $e)
        {   
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());            
            return $this->render('comercialBundle:infocontrato:aprobarContratoCambioRazonSocial.html.twig', $arrayParametros);
        }
    }

    /**
    * 
    * Función que obtiene los numeros del cliente
    *
    * @author Néstor Naula <nnaulal@telconet.ec>
    * @version 1.0 30-08-2020
    *
    * @param $arrayParametros
    * @return $strStatus
    */
    public function obtenerTelefonoCliente($arrayParametros)
    {
        $arrayTelefonosClienteMs = array();
        $intPersonaEmpresaRolId  = $arrayParametros['intPersonaEmpresaRolId'];
        $emComercial             = $this->getDoctrine()->getManager('telconet');

        $arrayParamContac                           = array();                 
        $arrayParamContac['strEstado']              = "Activo";
        $arrayParamContac['strDescFormaContacto']   = array(
                                                    "Telefono Movil",
                                                    "Telefono Movil Claro",
                                                    "Telefono Movil CNT",
                                                    "Telefono Movil Digicel",
                                                    "Telefono Movil Movistar",
                                                    "Telefono Movil Referencia IPCC",
                                                    "Telefono Movil Tuenti");

        try
        {
            $objPuntos     = $emComercial->getRepository('schemaBundle:InfoPunto')
                                         ->findByPersonaEmpresaRolId($intPersonaEmpresaRolId);	    

            foreach( $objPuntos as $objPunto )
            {
                $intIdPunto = $objPunto->getId();
                if(!empty($intIdPunto))
                {
                    $arrayParamContac['intIdPunto']             = $intIdPunto;
                    
                    $arrayTelefonos = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
                                                ->getEmailComercialCD($arrayParamContac);

                    $entityPuntoDatoAdicional = $emComercial->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                                ->findOneByPuntoId($intIdPuntoSession);

                    if(!empty($arrayTelefonos))
                    {
                        array_push($arrayTelefonosClienteMs,$arrayTelefonos[0]['strFormaContacto']);
                    }                            
                    if(empty($arrayTelefonos) && is_object($entityPuntoDatoAdicional))
                    {
                        $strContactoTelefono = $entityPuntoDatoAdicional->getTelefonoEnvio();
                        if(!empty($strContactoTelefono))
                        {
                            $arrayTelefonosCliente = preg_split('#(?<!\\\)\;#', $strContactoTelefono);
                            foreach($arrayTelefonosCliente as $strTelefonos)
                            {
                                array_push($arrayTelefonosClienteMs,$strTelefonos);
                            } 
                        }
                    }
                }
            }

            if(!empty($intPersonaEmpresaRolId))
            {
                $arrayParamContac['intIdPersonaEmpresaRol'] = $intPersonaEmpresaRolId;
                $arrayResultadosContact     = $emComercial
                                                    ->getRepository('schemaBundle:InfoPersonaContacto')
                                                    ->getEmailCliente($arrayParamContac);

                if(isset($arrayResultadosContact) && !empty($arrayResultadosContact))
                {
                    array_push($arrayTelefonosClienteMs,$arrayResultadosContact[0]['strFormaContacto']);
                }
            }
            return $arrayTelefonosClienteMs;
        }
        catch (\Exception $e)
        {   
            $arrayRespuesta =  array();
            return $arrayRespuesta;
        }
    }
    
    /**
     * Documentación para el método 'listadoServiciosAprobCambioRazonSocialAction'.
     * Funcion que devuelve consulta de los servicios de todos los puntos Logines origenes del Cambio de Razon social 
     *
     * @param integer $id_per_emp_rol
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 02-10-2015
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 11-11-2020 Se modifica la función para enviar un arreglo de parámetros a la función getServiciosAprobCambioRazonSocial y así
     *                         obtener los servicios W+AP
     *
     * @return JSON
     */
    public function listadoServiciosAprobCambioRazonSocialAction($id_per_emp_rol)
    {
        $objRequest         = $this->getRequest();
        $strPrefijoEmpresa  = $objRequest->getSession()->get('prefijoEmpresa');
        $intLimit           = $objRequest->get("limit");
        $intStart           = $objRequest->get("start");
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $objResponse        = new JsonResponse();
        $arrayResultado     = $emComercial->getRepository('schemaBundle:InfoServicio')
                                          ->getServiciosAprobCambioRazonSocial(array(   "idPersonaEmpresaRol"       => $id_per_emp_rol,
                                                                                        "start"                     => $intStart,
                                                                                        "limit"                     => $intLimit,
                                                                                        "estadoServicio"            => "Activo",
                                                                                        "estadoPerCaract"           => "Activo",
                                                                                        "prefijoEmpresa"            => $strPrefijoEmpresa,
                                                                                        "permiteWdbYAp"             => "SI"));
        $objResponse->setData($arrayResultado);
        return $objResponse;
    }
  
   /**
    * Documentación para el método 'procesaAprobContratoCambioRazonSocialAction'.
    * 
    * Funcion que procesa la aprobacion del contrato para el caso de un contrato generado por un Cambio de Razon
    * Social por Punto o Login.    
    * Consideraciones:
    * 1) Se le asigna nuevo Rol de "Cliente".
    * 2) Se crean los nuevos Logines en base a los Logines Origen del Cambio de Razon Social y sus servicios en estado Activo.
    * 3) Los Logines origen del "Cambio de Razon Social por Punto" pasaran a Cancelados asi como toda la data relacionada a estos en el momento
    *    de la Aprobacion del Nuevo Contrato.
    * 4) Actualiza la informacion de la forma de Pago : numero de cuenta o tarjeta , anio y mes de vencimiento de la tarjeta etc.
    * 5) Actualiza estado de contrato a Aprobado
    *         
    * @param integer $id_contrato    
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 02-10-2015   
    * 
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.1 27-12-2018 - Se agrega strNombrePais y intIdPais en el array de Parametros para validaciones de Telconet Panama
    * 
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.2 09-11-2020 Se modifica la función por cambios en la función invocada procesaAprobContratoCambioRazonSocial del service
    * 
    * @author Néstor Naula <nnaulal@telconet.ec>
    * @version 1.3 09-01-2021 Se agrega proceso de contrato digital MD.
    *
    * @author Alex Gomez <algomez@telconet.ec>
    * @version 3.29 10-08-2022 Se modifica estado de los puntos y servicios clonados por CRS tradicional y por punto 
    *                          cuando el contrato aun no ha sido autorizado. Aplica para MD y contrato digital.
    *
    * @throws Exception
    * @return a RedirectResponse to the given URL.
    */	    
    public function procesaAprobContratoCambioRazonSocialAction($id_contrato)
    {         
        $request               = $this->getRequest();
        $strCodEmpresa         = $request->getSession()->get('idEmpresa');
        $intIdOficina          = $request->getSession()->get('idOficina');
        $strNombrePais         = $request->getSession()->get('strNombrePais');
        $intIdPais             = $request->getSession()->get('intIdPais');
        $strUsrCreacion        = $request->getSession()->get('user');
        $strClientIp           = $request->getClientIp();
        $strPrefijoEmpresa     = $request->getSession()->get('prefijoEmpresa');
        $arrayListadoServicios = $request->get('array_listado_servicios');
        $arrayListadoServicios = explode("|", $arrayListadoServicios);
        $emComercial           = $this->get('doctrine')->getManager('telconet');   
        /* @var $service InfoContratoAprobService */
        $service          = $this->get('comercial.InfoContratoAprob');        
        $objContrato      = $service->getDatosContratoId($id_contrato);
        $objPersonaEmpRol = $service->getDatosPersonaEmpresaRolId($objContrato->getPersonaEmpresaRolId()->getId());
        $intIdPersonEmpRolEmpl  = $request->getSession()->get('idPersonaEmpresaRol');

        $emGeneral             = $this->get('doctrine')->getManager('telconet_general');
        $strEstadoServicioPreactivo   = '';
        $strEstadoPuntoPendiente      = '';

        $arrayParams = array_merge(
            $request->get('procesoaprobarcontratotype'),
            $request->get('convertirextratype'), 
            $request->get('infocontratoformapagotype'),            
            array('id_forma_pago'           => $request->get('id_forma_pago')),
            array('id_tipo_cuenta'          => $request->get('id_tipo_cuenta')),
            array('arrayListadoServicios'   => $arrayListadoServicios),
            array('id_contrato'             => $id_contrato),
            array('strCodEmpresa'           => $strCodEmpresa),
            array('intIdOficina'            => $intIdOficina), 
            array('strNombrePais'           => $strNombrePais),
            array('intIdPais'               => $intIdPais),                
            array('strUsrCreacion'          => $strUsrCreacion), 
            array('strClientIp'             => $strClientIp),
            array('strPrefijoEmpresa'       => $strPrefijoEmpresa),
            array('intIdPersonEmpRolEmpl'   => $intIdPersonEmpRolEmpl));
        
        try 
        {                        

           $arrayRespuesta = $service->procesaAprobContratoCambioRazonSocial($arrayParams);
            
            
            //servicios con nuevo estado preactivo previa confirmacion del contrato

            //Consulta nuevo estado para servicios creados por Cambio de Razón Social por Login
            //previo a la autorizacion del contrato. Aplica MD
            if($strPrefijoEmpresa === 'MD' || $strPrefijoEmpresa === 'EN')
            {
                $arrayEstadosServicios = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne(
                                                        'ESTADOS_CAMBIO_RAZON_SOCIALXPUNTO',
                                                        'COMERCIAL',
                                                        'CAMBIO_RAZON_SOCIAL_POR_PUNTO',
                                                        '','','','','','',
                                                        $strCodEmpresa);
                
                if(isset($arrayEstadosServicios) && !empty($arrayEstadosServicios))
                {
                    $strEstadoServicioPreactivo = $arrayEstadosServicios["valor1"];
                    $strEstadoPuntoPendiente    = 'Pendiente';

                    $arrayParamObservacionHist = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
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

                    foreach ($arrayRespuesta["arrayServiciosPreActivo"] as $strServicioId)
                    {
                        $objInfoServicioClonado  =  $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                ->findOneById($strServicioId);
                        $objInfoServicioClonado->setEstado($strEstadoServicioPreactivo);
                        $emComercial->persist($objInfoServicioClonado);

                        //registro de estado PreActivo en historial
                        $entityServicioHistorial = new InfoServicioHistorial();
                        $entityServicioHistorial->setServicioId($objInfoServicioClonado);
                        $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $entityServicioHistorial->setUsrCreacion($strUsrCreacion);
                        $entityServicioHistorial->setEstado($objInfoServicioClonado->getEstado());
                        $entityServicioHistorial->setObservacion($strMensajeEstadoPreactivo);
                        $emComercial->persist($entityServicioHistorial);

                        $emComercial->flush();
                    }
                }
            }

            if($arrayRespuesta["status"] === "OK")
            {
                $this->get('session')->getFlashBag()->add($arrayRespuesta["tipoMensaje"], $arrayRespuesta["mensaje"]);
            }
            else
            {
                throw new \Exception($arrayRespuesta["mensaje"]);
            }
           
            
            $objInfoPersonaEmpresaRol = $arrayRespuesta["objInfoPersonaEmpresaRol"];

            $arrayPuntos           = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                 ->findBy(array('personaEmpresaRolId' => $objInfoPersonaEmpresaRol->getId(),
                                                                'estado'              => array("Activo",$strEstadoPuntoPendiente)
                                                         ),
                                                          array('id' => 'DESC')
                                                    );

            if($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN' )
            {
                $emComercial->getConnection()->beginTransaction(); 
                //Obtengo Característica de USUARIO
                $objCaracteristicaUsuario = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => "USUARIO",
                                                                        "estado"                    => "Activo"));

                if(is_object($objCaracteristicaUsuario))
                {
                    //Inserto Caracteristica de USUARIO en el nuevo cliente
                    $objPersEmpRolCaracUsuario = new InfoPersonaEmpresaRolCarac();
                    $objPersEmpRolCaracUsuario->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                    $objPersEmpRolCaracUsuario->setCaracteristicaId($objCaracteristicaUsuario);
                    $objPersEmpRolCaracUsuario->setValor($objInfoPersonaEmpresaRol->getPersonaId()->getIdentificacionCliente());
                    $objPersEmpRolCaracUsuario->setFeCreacion(new \DateTime('now'));
                    $objPersEmpRolCaracUsuario->setUsrCreacion($strUsrCreacion);
                    $objPersEmpRolCaracUsuario->setEstado('Activo');
                    $objPersEmpRolCaracUsuario->setIpCreacion($strClientIp);
                    $emComercial->persist($objPersEmpRolCaracUsuario);
                    $emComercial->flush();
                }

                //Obtengo Característica de PUNTO RAZON SOCIAL
                $objCaracteristicaPuntoRS = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => "PUNTO CAMBIO RAZON SOCIAL",
                                                                          "estado"                    => "Activo"));

                if(is_object($objCaracteristicaPuntoRS))
                {
                    //Inserto Caracteristica de USUARIO en el nuevo cliente
                    $objPersEmpRolCaracPuntoRs = new InfoPersonaEmpresaRolCarac();
                    $objPersEmpRolCaracPuntoRs->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                    $objPersEmpRolCaracPuntoRs->setCaracteristicaId($objCaracteristicaPuntoRS);
                    $objPersEmpRolCaracPuntoRs->setValor($objInfoPersonaEmpresaRol->getPersonaId()->getIdentificacionCliente());
                    $objPersEmpRolCaracPuntoRs->setFeCreacion(new \DateTime('now'));
                    $objPersEmpRolCaracPuntoRs->setUsrCreacion($strUsrCreacion);
                    $objPersEmpRolCaracPuntoRs->setEstado('Activo');
                    $objPersEmpRolCaracPuntoRs->setIpCreacion($strClientIp);
                    $emComercial->persist($objPersEmpRolCaracPuntoRs);
                    $emComercial->flush();
                }

                if($emComercial->getConnection()->isTransactionActive())
                {
                    $emComercial->getConnection()->commit();
                }

            }

            if(!empty($arrayPuntos))
            { 
                foreach($arrayPuntos as $objPunto)
                {
                    $intPunto = $objPunto->getId();
                }
            }
 
            $strUrlAprobacion      = $this->generateUrl('aprobacioncontrato_aprobar_contrato');
            $arrayRespuestaProceso =  array('strMensaje'             => 'Proceso realizado',
                                            'strStatus'              => 0,
                                            'intPersonaEmpresaRolId' => $objInfoPersonaEmpresaRol->getId(),
                                            'intPunto'               => $intPunto,
                                            'arrayPuntosCRS'          => $arrayRespuesta["arrayPuntosCRS"] ,     
                                            'strUrl'                 => $strUrlAprobacion
                                            ); 

            $objResponse      = new Response(json_encode($arrayRespuestaProceso));

            return $objResponse;

        }
        catch(\Exception $e)
        {               
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            $strUrlAprobacion      = $this->generateUrl('aprobacioncontrato_aprobarContratoCambioRazonSocial', 
                                        array('id_persona' => $objPersonaEmpRol->getPersonaId()->getId(),'id_contrato' => $id_contrato));
            $arrayRespuestaProceso =  array('strMensaje'        => $e->getMessage(),
                                            'strStatus'         => 99,
                                            'strUrl'            => $strUrlAprobacion
                                            ); 

            $objResponse      = new Response(json_encode($arrayRespuestaProceso));
            
            return $objResponse;
        }
    }
    
    /**
     * Documentación para el método 'formasPagoAction'.
     * Obtiene informacion de las diferentes formas de pago de un contrato para llenado de combo via ajax   
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.1 24-03-2016   
     * @return array       
     */      
    public function formasPagoAction() 
    {
        $emGeneral = $this->get('doctrine')->getManager('telconet');
        $datos     = $emGeneral->getRepository('schemaBundle:AdmiFormaPago')->findFormasPagoParaContrato();
	$arrayFormasPago   = array();
    
        foreach($datos as $valor):
            $arrayFormasPago[] = array(
                                        'idformapago'         => $valor->getId(),
                                        'codigoformapago'     => $valor->getCodigoFormaPago(),
                                        'descripcionformapago'=> $valor->getDescripcionFormaPago()
                                       );
        endforeach;

        $response = new Response(json_encode(array('formas_pago' => $arrayFormasPago)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }  
     /**    
     * @Secure(roles="ROLE_60-4457")
     * Documentación para el método 'editarDatosAdicionalesContratoAction'.
     *
     * Permite presentar en pantalla Datos adicionales del Contrato para su edicion
     * 
     * @param int $id    primary key de la info_contrato del cliente     
     * 
     * @return Render Renderización de la ventana para la edicion de Datos Adicionales del Contrato
     * 
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>       
     * @version 1.0 07-07-2016
     *
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>       
     * @version 1.1 01-10-2018 - Se modifica opcion de "Datos Adicionales del Contrato" para que permita ingresar los Datos Adicionales de Contratos
     * que ingresan por la opción de Cambio de Razón Social. La Pantalla actualmente solo permite la edicion de los Datos Adicionales
     * debido a que es obligatorio el ingreso para contratos por venta nueva pero en Cambio de Razón Social no se solicitan.
     */
    public function editarDatosAdicionalesContratoAction($id)
    {        
        /* @var $service InfoContratoAprobService */
        $service             = $this->get('comercial.InfoContratoAprob');
        $objContrato         = $service->getDatosContratoId($id);
        $objPersonaEmpRol    = $service->getDatosPersonaEmpresaRolId($objContrato->getPersonaEmpresaRolId()->getId());
        $objPersona          = $service->getDatosPersonaId($objPersonaEmpRol->getPersonaId()->getId());
        $em                  = $this->getDoctrine()->getManager();        
        $objContratoDatoAdi  = $em->getRepository('schemaBundle:InfoContratoDatoAdicional')->findOneByContratoId($id);
        if(is_object($objContratoDatoAdi))
        {
            $formContratoDatoAdi = $this->createForm(new InfoContratoDatoAdicionalType(), $objContratoDatoAdi);
        }
        else
        {
            $objContratoDatoAdi  = new InfoContratoDatoAdicional();
            $formContratoDatoAdi = $this->createForm(new InfoContratoDatoAdicionalType(), $objContratoDatoAdi);             
        }
        $formContratoDatoAdi = $formContratoDatoAdi->createView();

        return $this->render('comercialBundle:infocontrato:editarDatosAdicionalesContrato.html.twig', 
                      array('objPersona'          => $objPersona, 
                            'objContrato'         => $objContrato,
                            'objContratoDatoAdi'  => $objContratoDatoAdi,
                            'formContratoDatoAdi' => $formContratoDatoAdi));
    }

     /**    
     * @Secure(roles="ROLE_60-4457")
     * Documentación para el método 'updateDatosAdicionalesContratoAction'.
     *
     * Permite guardar los Datos adicionales del Contrato
     * 
     * @param int $id    primary key de la info_contrato_dato_adicional    
     * 
     * @return a RedirectResponse to the given URL.
     * 
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>       
     * @version 1.0 08-07-2016
     *
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>       
     * @version 1.1 01-10-2018 - Se modifica opcion de "Datos Adicionales del Contrato" para que permita ingresar los Datos Adicionales de Contratos
     * que ingresan por la opción de Cambio de Razón Social. La Pantalla actualmente solo permite la edicion de los Datos Adicionales
     * debido a que es obligatorio el ingreso para contratos por venta nueva pero en Cambio de Razón Social no se solicitan.
     * 
     * @author Edgar Holguin<eholguin@telconet.ec>       
     * @version 1.2 06-06-2022 Se agrega seteo de campo para notificar pago.
     */
    public function updateDatosAdicionalesContratoAction(Request $request, $id)
    {                        
        $serviceInfoContratoAprob  = $this->get('comercial.InfoContratoAprob');
        $formContratoDatoAdi       = $request->get('infocontratodatoadicionaltype');
        $session                   = $request->getSession();
        $strUsuario                = $session->get('user'); 
        $strCodEmpresa             = $session->get('idEmpresa');
        $strClientIp               = $request->getClientIp();
        $em                        = $this->getDoctrine()->getManager();
        $em->getConnection()->beginTransaction();        
        try
        {            
             // Buscamos en InfoContratoDatoAdicional
            $objContrato         = $serviceInfoContratoAprob->getDatosContratoId($id);
            if(!is_object($objContrato))
            {
                throw new \Exception("No se encontro el contrato del cliente");
            }
            $objPersonaEmpRol    = $serviceInfoContratoAprob->getDatosPersonaEmpresaRolId($objContrato->getPersonaEmpresaRolId()->getId());
            if(!is_object($objPersonaEmpRol))
            {
                throw new \Exception("No se encontro información del cliente");
            }
            $arrayParams        = array('ID_PER'  => $objPersonaEmpRol->getId(),
                                        'EMPRESA' => $strCodEmpresa,
                                        'ESTADO'  => $objContrato->getEstado());
            $objContratoDatoAdi = $em->getRepository('schemaBundle:InfoContratoDatoAdicional')
                                     ->getResultadoDatoAdicionalContrato($arrayParams);
            if(is_object($objContratoDatoAdi))
            {
                $objContratoDatoAdi->setFeUltMod(new \DateTime('now'));
                $objContratoDatoAdi->setUsrUltMod($strUsuario);
            }
            else
            {
                $objContratoDatoAdi = new InfoContratoDatoAdicional();
                $objContratoDatoAdi->setContratoId($objContrato);
                $objContratoDatoAdi->setUsrCreacion($strUsuario);
                $objContratoDatoAdi->setFeCreacion(new \DateTime('now'));
                $objContratoDatoAdi->setIpCreacion($strClientIp);
            }

            if(!isset($formContratoDatoAdi['esVip']) || $formContratoDatoAdi['esVip'] == '')
            {
                $objContratoDatoAdi->setEsVip('N');
            }
            else
            {
                $objContratoDatoAdi->setEsVip('S');
            }
            
            if(!isset($formContratoDatoAdi['notificaPago']) || $formContratoDatoAdi['notificaPago'] == '')
            {
                $objContratoDatoAdi->setNotificaPago('N');
            }
            else
            {
                $objContratoDatoAdi->setNotificaPago('S');
            }            
            
            if(!isset($formContratoDatoAdi['esTramiteLegal']) || $formContratoDatoAdi['esTramiteLegal'] == '')
            {
                $objContratoDatoAdi->setEsTramiteLegal('N');
            }
            else
            {
                $objContratoDatoAdi->setEsTramiteLegal('S');
            }    
            
            if(!isset($formContratoDatoAdi['permiteCorteAutomatico']) || $formContratoDatoAdi['permiteCorteAutomatico'] == '')
            {
                $objContratoDatoAdi->setPermiteCorteAutomatico('N');
            }
            else
            {
                $objContratoDatoAdi->setPermiteCorteAutomatico('S');
            }
            
            if(!isset($formContratoDatoAdi['fideicomiso']) || $formContratoDatoAdi['fideicomiso'] == '')
            {
                $objContratoDatoAdi->setFideicomiso('N');
            }
            else
            {
                $objContratoDatoAdi->setFideicomiso('S');
            }
            
            if(!isset($formContratoDatoAdi['convenioPago']) || $formContratoDatoAdi['convenioPago'] == '')
            {
                $objContratoDatoAdi->setConvenioPago('N');
            }
            else
            {
                $objContratoDatoAdi->setConvenioPago('S');
            }                               
           
            if(!isset($formContratoDatoAdi['tiempoEsperaMesesCorte']) || $formContratoDatoAdi['tiempoEsperaMesesCorte']=='')
            {
                $objContratoDatoAdi->setTiempoEsperaMesesCorte(0);
            }
            else
            {
                $objContratoDatoAdi->setTiempoEsperaMesesCorte($formContratoDatoAdi['tiempoEsperaMesesCorte']);
            }           
               
            $em->persist($objContratoDatoAdi);
            $em->flush();
            $em->getConnection()->commit();
            return $this->redirect($this->generateUrl('infocontrato_show', array('id' => $objContratoDatoAdi->getContratoId()->getId())));
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->redirect($this->generateUrl('infocontrato_editarDatosAdicionalesContrato',
                          array('id' => $id)));
        }
    }

    /**
     * obtenerPuntosContratoAction
     *
     * Método encargado de obtener los puntos asociados a un contrato.
     *
     * @return array $arrayPuntosContrato [ 'registros' => 'Información consultada',
     *                                      'total'     => 'Cantidad de registros consultados' ]
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 16-03-2020
     *
     */
    public function obtenerPuntosContratoAction()
    {
        $objResponse = new Response();
        
        $objRequest = $this->getRequest();

        $intIdEmpresa     = intval($objRequest->getSession()->get('idEmpresa'));
        $arrayPuntoSesion = $objRequest->getSession()->get('ptoCliente');
        $strUsuario       = $objRequest->getSession()->get('user');
        
        $intIdContrato    = intval($objRequest->get('intIdContrato', 0));
        
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $serviceUtil = $this->get('schema.Util');

        try
        {
            $objContrato = $emComercial->getRepository('schemaBundle:InfoContrato')
                ->find($intIdContrato);

            $arrayRespuestaParametro = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne("ESTADOS_TM_COMERCIAL",
                         "COMERCIAL",
                         "TM_COMERCIAL",
                         "ESTADOS DEL PUNTO PARA CONSULTA DE DOCUMENTOS DIGITALES",
                         "",
                         "",
                         "",
                         "",
                         "",
                         "18");
            
            $strJsonPuntosContrato = $emComercial->getRepository('schemaBundle:InfoPunto')
                ->getJsonPuntosCliente(array('EMPRESA' => $intIdEmpresa, 
                                             'PERSONA' => $objContrato->getPersonaEmpresaRolId()->getPersonaId()->getId(),
                                             'ESTADOS' => explode(',', $arrayRespuestaParametro['valor1'])));
            
            
        }
        catch (\Exception $objException)
        {
            $serviceUtil->insertLog(array('enterpriseCode'   => $intIdEmpresa,
                                          'logType'          => 1,
                                          'logOrigin'        => 'TELCOS',
                                          'application'      => basename(__FILE__),
                                          'appClass'         => basename(__CLASS__),
                                          'appMethod'        => basename(__FUNCTION__),
                                          'descriptionError' => $objException->getMessage(),
                                          'status'           => 'Fallido',
                                          'inParameters'     => $objException->getTraceAsString(),
                                          'creationUser'     => $strUsuario));
        }
        
        $arrayPuntos                = json_decode($strJsonPuntosContrato, true);
        $arrayPuntos['loginsesion'] = $arrayPuntoSesion['login'];
        
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent(json_encode($arrayPuntos));

        return $objResponse;
    }
    
     /**
     * setLogNumeroCtaTarjetaAction
     *
     * Función que permite realizar el registro de logs de visualización de nmúero cta-tarjeta..
     *
     * @return array $arrayResponse 
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 03-04-2020
     *
     */
    public function setLogNumeroCtaTarjetaAction()
    {
        $objResponse      = new Response();
        $objRequest       = $this->getRequest();
        $objSession       = $objRequest->getSession();
        $strUsrCreacion   = $objSession->get('user');
        $emComercial      = $this->getDoctrine()->getManager('telconet');
        $intIdContrato    = intval($objRequest->get('idContrato', 0));
        $objInfoContrato  = $emComercial->getRepository('schemaBundle:InfoContrato')->find($intIdContrato);
            
        $emComercial->getConnection()->beginTransaction();
        try
        {
            $objInfoContratoFormaPagoLog = new InfoContratoFormaPagoLog();
            $objInfoContratoFormaPagoLog->setEstado('Activo');
            $objInfoContratoFormaPagoLog->setFeCreacion(new \DateTime('now'));
            if(is_object($objInfoContrato))
            {
                $intIdCliente     = $objInfoContrato->getPersonaEmpresaRolId()->getPersonaId();
                $objInfoPersona   = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->find($intIdCliente);                
                $objInfoContratoFormaPagoLog->setContratoId($objInfoContrato);
                $objInfoContratoFormaPagoLog->setPersonaId($objInfoPersona);
            }
            $objInfoContratoFormaPagoLog->setUsrCreacion($strUsrCreacion);
            $emComercial->persist($objInfoContratoFormaPagoLog);
            $emComercial->flush();
             
           $emComercial->getConnection()->commit();   
           $objResponse->setContent("");            
       }       
        catch (\Exception $e) 
       {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
                $objResponse->setContent($e->getMessage());            
        }
       return $objResponse;        
    }

    /**
     * Documentación para el método 'verContratoFormaPagoLog'.
     * Permite visualización de pantalla de los logs registrados por visualización de número-cuenta tarjeta.   
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 06-04-2020   
     * @return response       
     */     
    public function verContratoFormaPagoLogAction()
    {
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');

       return $this->render('comercialBundle:infocontrato:verContratoFormaPagoLog.html.twig', array('strPrefijoEmpresa'   => $strPrefijoEmpresa));
    }

    /**
     * Documentación para el método 'gridInfoContratoLogAction'.
     * Obtiene informacion de los logs registrados por visualización de número-cuenta tarjeta según criterios de busqueda   
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 06-04-2020   
     * @return response       
     */     
    public function gridInfoContratoLogAction()
    {
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $arrayFechaDesde   = explode('T', $objRequest->get("fechaDesde"));
        $arrayFechaHasta   = explode('T', $objRequest->get("fechaHasta"));
        $strNombre         = $objRequest->get("nombre");
        $strApellido       = $objRequest->get("apellido");
        $intLimit          = $objRequest->get("limit");
        $intStart          = $objRequest->get("start");
        $intPage           = $objRequest->get("page");
        $strIdentificacion = $objRequest->get("identificacion");
        $intIdEmpresa      = $objRequest->getSession()->get('idEmpresa');
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        $emComercial       = $this->get('doctrine')->getManager('telconet');        
        
        
        $arrayParametros   = [];
        $arrayParametros['idEmpresa']      = $intIdEmpresa;
        $arrayParametros['fechaDesde']     = $arrayFechaDesde[0];
        $arrayParametros['fechaHasta']     = $arrayFechaHasta[0];
        $arrayParametros['nombre']         = $strNombre;
        $arrayParametros['apellido']       = $strApellido;
        $arrayParametros['identificacion'] = $strIdentificacion;
        $arrayParametros['limit']          = $intLimit;
        $arrayParametros['page']           = $intPage;
        $arrayParametros['start']          = $intStart;
        $arrayParametros['strPrefijoEmpresa']     = $strPrefijoEmpresa;
        
        $arrayResultado  = $emComercial->getRepository('schemaBundle:InfoContrato')->getLogsPorCriterios($arrayParametros);     
        $arrayRegistros  = $arrayResultado['registros'];
        $intTotal        = $arrayResultado['total'];        
        $arrayLogs       = [];
        foreach ($arrayRegistros as $arrayDatos):
            $arrayLogs[]= array(
                                    'id'           => $arrayDatos['id'],
                                    'contratoId'   => $arrayDatos['contratoId'],
                                    'personaId'    => $arrayDatos['personaId'],
                                    'nombre'       => $arrayDatos['nombre'],
                                    'feCreacion'   => strval(date_format($arrayDatos['feCreacion'],"d/m/Y G:i")),
                                    'usrCreacion'  => $arrayDatos['usrCreacion'],
                                    'estado'       => $arrayDatos['estado']                              
                                   );    
        endforeach;

        $objResponse = new Response(json_encode(array('total'=>$intTotal,'infoContratoLogs'=>$arrayLogs)));
        
        $objResponse->headers->set('Content-type', 'text/json');

        return $objResponse;
    }    
    
    /**
     * Documentación para el método 'getMotivosFacturaAction'.
     * Obtiene información de los motivos para la facturación por cambio de forma de pago.    
     * @author Josselhin Moreira Q <kjmoreira@telconet.ec>
     * @version 1.0 17-06-2019   
     * @return array       
     */      
    public function getMotivosFacturaAction() 
    {
        $emSeguridad = $this->getDoctrine()->getManager('telconet_seguridad');
		
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion = $this->get('request');
        
        $intStart = $objPeticion->query->get('start');
        $intLimit = $objPeticion->query->get('limit');
        
        $entitySeguRelacionSistema = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->findOneBy(array("moduloId"=>60, "accionId"=>4));
	    $intRelacionSistemaId      = $entitySeguRelacionSistema->getId() ? $entitySeguRelacionSistema->getId() : 0;         
        
        $objJson = $this->getDoctrine()
                        ->getManager("telconet_general")
                        ->getRepository('schemaBundle:AdmiMotivo')
                        ->generarJson("","Activo",$intStart,$intLimit, $intRelacionSistemaId);
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }      

    /**
     * Documentación para el método 'getValorInsProMensualesAction'.
     * Retorna informacion valores de instalación y promociones mensaules .    
     * @author Angel Reina <areina@telconet.ec>
     * @version 1.0 11-07-2019   
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 13-03-2020 Se agrega envío de parámetros correspondientes a la nueva forma de pago.   
     * 
     * @return array       
     */      
    public function getValorInsProMensualesAction() 
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $emComercial         = $this->getDoctrine()->getManager();
        $objPeticion         = $this->get('request'); 
        $objRequest          = $this->getRequest();
        $objSession          = $objRequest->getSession();
        $strUsuario          = $objSession->get('user'); 
        $strCodEmpresa       = $objSession->get('idEmpresa');
        $arrayPuntoCliente   = $objSession->get('ptoCliente');
        $intIdPuntoSession   = $arrayPuntoCliente['id'];
        
        $objPtoSession = $emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPuntoSession);
        if(is_object($objPtoSession))
        {
            $objPersonaEmpresaRol = $objPtoSession->getPersonaEmpresaRolId();
            $objContrato          = $emComercial->getRepository('schemaBundle:InfoContrato')
                                                ->findOneBy(array( "personaEmpresaRolId" => $objPersonaEmpresaRol->getId(),"estado"=>"Activo"));
            if(is_object($objContrato))
            {
                $intIdContrato  = $objContrato->getId();
            }
        }
        else
        {
            $intIdContrato  = $objPeticion->get('contratoId');
        }

        $objInfoDocumentoFinancieroCabService = $this->get('financiero.InfoDocumentoFinancieroCab');
        $arrayParametros = array();
        $arrayParametros['intIdContrato']         = $intIdContrato;
        $arrayParametros['strEmpresaCod']         = $strCodEmpresa;
        $arrayParametros['strIpCliente']          = $objRequest->getClientIp();
        $arrayParametros['intFormaPagoId']        = $objRequest->get('formaPagoId');
        $arrayParametros['intTipoCuentaId']       = $objRequest->get('tipoCuentaId');
        $arrayParametros['intBancoTipoCuentaId']  = $objRequest->get('bancoTipoCuentaId');
        $arrayParametros['strUsrCreacion']        = $strUsuario;
       
        $arrayValores   = $objInfoDocumentoFinancieroCabService->getPtosValoresFacturarByContratoId($arrayParametros);
        $objResponse = new Response();
        $objResponse->setContent(json_encode(array('total' => 1, 'encontrados' => $arrayValores['arrayPtosValoresFacurar'])));
        $objResponse->headers->set('Content-Type', 'text/json');
        
        return $objResponse;
    }

    /**
     * Documentación para el método 'getDocumentoLogFormaPagoContratoAction'.
     * Retorna los documentos de la forma de pago .    
     * @author Angel Reina <areina@telconet.ec>
     * @version 1.0 11-07-2019   
     * @return array       
     */
    public function getDocumentoLogFormaPagoContratoAction() 
    {
        $arrayResponse          = array();
        $arrayResponse['total'] = 21;
        $arrayResponse['logs']  = array();
        $arrayResponse->setContent(json_encode($arrayResponse));
        return $arrayResponse;
    }

    /**
     * guardarLogContratoAction
     *
     * Método encargado de guardar logs al visualizar documentos digitales.
     *
     * @return array
     *
     * @author Francisco Cueva <facueva@telconet.ec>
     * @version 1.0 25-05-2021
     *
     */
    public function guardarLogContratoAction()
    {
        $serviceUtil          = $this->get('schema.Util');
        $emGeneral            = $this->getDoctrine()->getManager('telconet_general');
        $emComercial          = $this->getDoctrine()->getManager('telconet');
        $serviceComercial     = $this->get('comercial.Comercial');
        $objResponse          = new Response();
        $emComunicacion       = $this->getDoctrine()->getManager('telconet_comunicacion');

        $objRequest           = $this->getRequest();
        $strIdEmpresa         = $objRequest->getSession()->get('idEmpresa');
        $strUsuario           = $objRequest->getSession()->get('user');
        $strClientIp          = $objRequest->getClientIp();
        $strAccion            = $objRequest->get('accion', 0);
        $strIdDoc             = intval($objRequest->get('idDoc', 0));

        $objDocumento         = $emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                ->find($strIdDoc);

        $objDocumentoGeneral  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                ->find($objDocumento->getTipoDocumentoGeneralId());

        $objContrato          = $emComercial->getRepository('schemaBundle:InfoContrato')
                                ->find($objDocumento->getContratoId());

        $objPersonaEmpresaRol = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                ->findOneBy(array('id' => $objContrato->getPersonaEmpresaRolId()));

        $objPersona           = $emComercial->getRepository("schemaBundle:InfoPersona")
                                ->find($objPersonaEmpresaRol->getPersonaId());

        $objPunto             = $emComercial->getRepository('schemaBundle:InfoPunto')
                                ->findOneBy(array('personaEmpresaRolId' => $objPersonaEmpresaRol->getId()));

        $objServicio          = $emComercial->getRepository("schemaBundle:InfoServicio")
                                ->findBy(array('puntoId' => $objPunto->getId()));
        
        $strEstadoServicio = 'SinEstado';

        foreach ($objServicio as $servicio)
        {
            $strPlan   = $servicio->getPlanId();
            $strEstado = $servicio->getEstado();

            if($strPlan !== null  && ($strEstado !== 'Cancel' || $strEstado !== null))
            {
                $strEstadoServicio = $strEstado;
            }
        }

        $arrayParametros['estadoServicio']  = $strEstadoServicio;
        $arrayParametros['tipoDocumento']   = $objDocumentoGeneral->getDescripcionTipoDocumento();
        $arrayParametros['identificacion']  = $objPersona->getIdentificacionCliente();
        $arrayParametros['loginCliente']    = $objPunto->getLogin();
        $arrayParametros['empresaCod']      = $strIdEmpresa;
        $arrayParametros['accion']          = $strAccion;

        if($strAccion == "VISUALIZAR")
        {
            $arrayParametros['observacion'] = "Documento visualizado"; 
        }else if($strAccion == "VISUALIZAR/IMPRIMIR")
        {
            $arrayParametros['observacion'] = "Documento descargado";
        }

        $arrayParametros['usrCreacion']     = $strUsuario;
        $arrayParametros['ipCreacion']      = $strClientIp;

        $objResponse->headers->set('Content-type', 'text/json');
        $arrayRespuesta['respuesta'] = $serviceComercial->insertInfoVisualizacionDoc($arrayParametros);

        $objResponse->setContent(json_encode($arrayRespuesta));
        return $objResponse;
    }
    
    /**
    * guardarCodigoPromocionAction Función que guarda el código promocional ingresado.
    * 
    * @author Katherine Yager <kyager@telconet.ec
    * @version 1.0 01-12-2020
    * 
    * @return array
    */
    public function guardarCodigoPromocionAction($arrayParametros)
    { 
       
        $emComercial            = $this->getDoctrine()->getManager("telconet");
        $strCodigoMens          = $arrayParametros['strCodigo'];
        $strPomocionMens        = $arrayParametros['strPromocion'];
        $intIdServicio          = $arrayParametros['intIdServicio'];
        $strTipoProceso         = $arrayParametros['strTipoPromocion'];
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $strIpCreacion          = $arrayParametros['strIpCreacion'];
        $strIdTipoPromocion     = $arrayParametros['strIdTipoPromocion'];
        $servicePromocion       = $this->get('comercial.Promocion');
      
        try
        {
            
            $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneById( $intIdServicio );
            
            if(!is_object($objServicio))
            {
                throw new \Exception("No se ha encontrado el servicio.");
            }
                 
            if ($strTipoProceso=='EXISTENTE')
            {
              $strDescripcionCaracteristica='PROM_COD_EXISTENTE';
            }
            else if ($strTipoProceso=='NUEVO')
            {
               $strDescripcionCaracteristica='PROM_COD_NUEVO';
            }
            else if ($strTipoProceso=='INS')
            {
               $strDescripcionCaracteristica='PROM_COD_INST';
            }
            else if ($strTipoProceso=='BW')
            {
               $strDescripcionCaracteristica='PROM_COD_BW';
            }
             
            if($strCodigoMens!='')
            {
            
                $objCaracteristicaMens = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array('descripcionCaracteristica' => $strDescripcionCaracteristica,
                                                                        'tipo'                      => 'COMERCIAL'));

                if(!is_object($objCaracteristicaMens))
                {
                    throw new \Exception("No se ha definido la característica");
                }

                $objCaracteristicaMensExist =  $emComercial->getRepository('schemaBundle:InfoServicioCaracteristica')
                                                            ->findOneBy(array("servicioId"       => $objServicio,
                                                                                "caracteristicaId" => $objCaracteristicaMens,
                                                                                "estado"           => 'Activo'));

                if(!is_object($objCaracteristicaMensExist))
                {
                    $arrayParametros                        = array();
                    $arrayParametros['intIdServicio']       = $intIdServicio;
                    $arrayParametros['strIpCreacion']       = $strIpCreacion;
                    $arrayParametros['strUsrCreacion']      = $strUsrCreacion;
                    $arrayParametros['strCodigo']           = $strCodigoMens;
                    $arrayParametros['strPromocion']        = $strPomocionMens;
                    $arrayParametros['strEstado']           = 'Activo';
                    $arrayParametros['strObservacion']      = "Se crea el código promocional {$strCodigoMens}.";
                    $arrayParametros['objCaracteristica']   = $objCaracteristicaMens;
                    $arrayParametros['strIdTipoPromocion']  = $strIdTipoPromocion;
                    $arrayResponseMens                      = $servicePromocion->guardarCodigoServicioCarac($arrayParametros);

                }
               
            } 
            
      
            $arrayResponse = array('strPromMens' => $arrayResponseMens,
                                   'strPromBW'   => $arrayResponseBW,
                                   'strMensaje'  => '');
            
        }
        catch(\Exception $e)
        {
            error_log( 'ERROR: '.$e->getMessage() );
            $strMensaje    = 'Ocurrió un error al guardar el código ingresado promocional.';
            $arrayResponse = array('strPromMens' => '',
                                   'strPromBW'   => '',
                                   'strMensaje'  => $strMensaje);
        }
        
        return $arrayResponse;
    }
    
    /**
     * Datos para el contrato
     *
     * @author : Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.0
     * @since 04-01-2022
     *
     */   
    
    public function datosContratoAction()
    {
		$objRequest     = $this->getRequest();	
		$objSession     = $objRequest->getSession();
		$intIdEmpresa   = $objSession->get('idEmpresa');
        $intPerEmpRol   = $objRequest->get('idPer');
        $intPuntoId     = $objRequest->get('idPto');
    //aqui ver identificacion
        $strIdentificacionCliente    = $objRequest->get('identificacionCliente');
        return $this->render('comercialBundle:infocontrato:datosContrato.html.twig', 
                            array('personaEmpresaRolId' => $intPerEmpRol,
                                  'puntoId'             => $intPuntoId, 
                                  'identificacionCliente'=> $strIdentificacionCliente 
                                
                                ));
    }
   /**
     * getCorreosCliente Función que obtiene los correos del cliente.
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 01-09-2022
     * @return array
     */
    public function getCorreosClienteAction()
    {
        $arrayConsulta       = array();
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $objRequest          = $this->get('request');
        $intIdPunto          = $objRequest->get("idPunto");
        $strNumeroContrato   = $objRequest->get("numeroContrato");
        $objSession          = $objRequest->getSession();
        $strPrefijoEmpresa   = $objSession->get('idEmpresa');
        $strUsrCreacion      = $objSession->get('user');
        if(!isset($intIdPunto) || empty($intIdPunto) || $intIdPunto == "null")
        {
            $objPtoCliente  = $objSession->get('ptoCliente');
            $intIdPunto     = $objPtoCliente['id'];
        }
        $objPunto            = $emComercial->getRepository('schemaBundle:InfoPunto')
                                           ->find($intIdPunto);

        if($objPunto)
        {
            $objFormaContacto      = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                                ->findOneBy(array('descripcionFormaContacto' => 'Correo Electronico',
                                                                    'estado' => 'Activo'
                                                                )
                                                            );

            $arrayAdmiFormContactEmp = $emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                    ->findBy(array('puntoId'       => $objPunto->getId(),
                                                                    'formaContactoId' => $objFormaContacto->getId(),
                                                                    'estado'          => 'Activo'
                                                                    )
                                                                );
            foreach($arrayAdmiFormContactEmp as $objAdmiFormaContactoEmp)
            {
                $arrayFormaContacto = array('valor' => strtolower($objAdmiFormaContactoEmp->getValor()));
                $arrayConsulta[]    = $arrayFormaContacto;
            }
        }

        if($strNumeroContrato)
        {
            $objNumeroContrato      = $emComercial->getRepository('schemaBundle:InfoContrato')
                                                    ->findOneByNumeroContrato($strNumeroContrato);
            if($objNumeroContrato)
            {
                $arrayParamCorreo = array('intIdPersonaEmpresaRol'  => $objNumeroContrato->getPersonaEmpresaRolId()->getId(),
                                            'strEstado'               => 'Activo',
                                            'strDescFormaContacto'    => array('Correo Electronico'));
                $arrayCorreos     = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
                                                ->getEmailCliente($arrayParamCorreo);
                if(isset($arrayCorreos) && !empty($arrayCorreos))
                {
                    foreach($arrayCorreos as $arrayCorreo)
                   {
                        $arrayFormaContacto = array('valor' => strtolower($arrayCorreo['strFormaContacto']));
                        $arrayConsulta[]    = $arrayFormaContacto;
                    }
                    
                }
            }
        }



        $arrayCorreoCliente = array_unique($arrayConsulta, SORT_REGULAR);
        $objResponse        = new Response(json_encode($arrayCorreoCliente));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * notificarAutorizar Función que valida que exista un documento tipo Contrato para notificar o autorizar.
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 01-09-2022
     * @return array
     */
    public function notificarAutorizarAction()
    {
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $emComunicacion      = $this->getDoctrine()->getManager('telconet_comunicacion');
        $objRequest          = $this->get('request');
        $strNumeroContrato   = $objRequest->get("numeroContrato");
        $objSession          = $objRequest->getSession();
        $strPrefijoEmpresa   = $objSession->get('idEmpresa');
        $strUsrCreacion      = $objSession->get('user');
        $strNumeroAdendum  = $objRequest->get("numeroAdendum");
        $objPunto      = $objSession->get('ptoCliente');
        $intIdPunto         = $objPunto['id'];
        try
        {
            if(!empty($strNumeroAdendum))
            {

                $arrayResponse = array('intStatus'  => 200,
                'strStatus'  => 'OK',
                'message'    => 'Existe un documento tipo Contrato para notificar o autorizar.');

                $objAdendum   = $emComercial->getRepository('schemaBundle:InfoAdendum')
                ->findOneBy(array('numero' => $strNumeroAdendum,
                                  'puntoId'       => $intIdPunto,
                                  'estado'         => array('Activo','Pendiente','PorAutorizar')));

                $strTieneContratoAdendum=$emComercial->getRepository('schemaBundle:InfoAdendumCaracteristica')
                                                ->getObtenerCarateristicaDocumento($objAdendum->getId());

                if($strTieneContratoAdendum !='S')  
                {
                    $arrayResponse = array('intStatus'  => 500,
                    'strStatus'  => 'ERROR',
                    'message'    => 'Estimado usuario,debe subir la imagen del adendum.');
                }   
            }else
            {
                $objContrato  = $emComercial->getRepository('schemaBundle:InfoContrato')
                ->findOneBy(array('numeroContrato' => $strNumeroContrato,
                                  'estado'         => array('Activo','Pendiente','PorAutorizar'
                                    ),
                                  'personaEmpresaRolId'=>$objPunto['id_persona_empresa_rol']
                                 ));
                  if($objContrato)
                {
                          $intContratoId = $objContrato->getId();
                }

                $arrayParametros          = array('intContratoId'                 => $intContratoId,
                                'strDescripcionTipoDocumento'   => 'CONTRATO');
                $arrayInfoDocumentoRelaci = $emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                        ->getDocumentosByContrato($arrayParametros);

                 if(isset($arrayInfoDocumentoRelaci) &&
                        !empty($arrayInfoDocumentoRelaci) &&
                        $arrayInfoDocumentoRelaci['total'] > 0)
                {
                $arrayResponse = array('intStatus'  => 200,
                        'strStatus'  => 'OK',
                        'message'    => 'Existe un documento tipo Contrato para notificar o autorizar.');
                }
                else
                {
                        $arrayResponse = array('intStatus'  => 500,
                        'strStatus'  => 'ERROR',
                        'message'    => 'No existe un documento tipo Contrato para notificar o autorizar.');
                }
            }
        }
        catch(\Exception $e)
        {
            $strMensaje    = 'Ocurrió un error al obtener información para autorizar el presente contrato';
            $arrayResponse = array('intStatus'   => 500,
                                   'strStatus'   => 'error',
                                   'strMensaje'  => $strMensaje);
        }
        $objResponse        = new Response(json_encode($arrayResponse));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * correoAutorizar Función que muestra los correos de los departamentos para autorizar el presente contrato.
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 01-09-2022
     * @return array
     */
    public function correoAutorizarAction()
    {
        $emGeneral           = $this->getDoctrine()->getManager('telconet_general');
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $strPrefijoEmpresa   = $objSession->get('idEmpresa');
        $strUsrCreacion      = $objSession->get('user');
        $intIdContrato       = $objRequest->get("idContrato");
        $intIdAdendum        = $objRequest->get("idAdendum");
        $objPunto      = $objSession->get('ptoCliente');
        $intIdPunto         = $objPunto['id']; 
        try
        {

            $arrayResponse            = array('intStatus'   => 500,
                                              'strStatus'   => 'ERROR',
                                              'strMensaje'  => 'Ocurrió un error al obtener los correos de los departamento para autorizar contrato',
                                              'arrayCorreo' => []);
            $objEntityCaract = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->findOneBy(
                                                        array("descripcionCaracteristica"   => 'docFisicoCargado',
                                                                "estado"                    => 'Activo'));
            if(!is_object($objEntityCaract))
            {
                throw new \Exception('No existe la característica docFisicoCargado');
            }
            
            if(!empty($intIdAdendum))
            {    $objAdendum   = $emComercial->getRepository('schemaBundle:InfoAdendum')
                ->findOneBy(array('numero' => $intIdAdendum, 'puntoId'       => $intIdPunto  ));


                $entityCaractContrato = $emComercial->getRepository('schemaBundle:InfoAdendumCaracteristica')
                ->findOneBy(
                    array("caracteristicaId"  => $objEntityCaract->getId(),
                            "adendumId"      => $objAdendum->getId(),
                            "estado"          => "Activo"
                    ));
            }else
            {
                $entityCaractContrato = $emComercial->getRepository('schemaBundle:InfoContratoCaracteristica')
                ->findOneBy(
                    array("caracteristicaId"  => $objEntityCaract->getId(),
                            "contratoId"      => $intIdContrato,
                            "estado"          => "Activo"
                    ));
            }
    
            if(is_object($entityCaractContrato) && $entityCaractContrato->getValor1() == 'N')
            {
                throw new \Exception('No se han cargado los documentos requeridos para realizar un contrato físico.');
            }

            $arrayRespuestaParametro  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->get('CORREOS_ADMINISTRACION_CONTRATOS', 
                                                        'COMERCIAL', 
                                                        '', 
                                                        '',
                                                        '', 
                                                        '', 
                                                        '', 
                                                        '',
                                                        '',
                                                        $strPrefijoEmpresa );
            if(isset($arrayRespuestaParametro) && !empty($arrayRespuestaParametro))
            {
                foreach($arrayRespuestaParametro as $objAdmiFormaContactoEmp)
                {
                    $arrayFormaContacto = array('valor' => strtolower($objAdmiFormaContactoEmp['valor1']));
                    $arrayConsulta[]    = $arrayFormaContacto;
                }
            }
            if(!empty($arrayConsulta))
            {
                $arrayResponse = array('intStatus'   => 200,
                                       'strStatus'   => 'OK',
                                       'strMensaje'  => 'OK',
                                       'arrayCorreo' => $arrayConsulta);
            }
        }
        catch(\Exception $e)
        {
            $arrayResponse = array('intStatus'   => 500,
                                   'strStatus'   => 'ERROR',
                                   'strMensaje'  => $e->getMessage());
        }
        $objResponse       = new Response(json_encode($arrayResponse));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * notificarAutorizar Función que muestra los correos de los departamentos para notificar el presente contrato.
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 01-09-2022
     * @return array
     */
    public function autorizarContratoFisicoAction()
    {  
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $objRequest          = $this->get('request');
        $strCorreo           = $objRequest->get("correo");
        $objSession          = $objRequest->getSession();
        $strPrefijoEmpresa   = $objSession->get('idEmpresa');
        $strNumeroContrato   = $objRequest->get("numeroContrato");
        $strNumeroAdendum    = $objRequest->get("numeroAdendum");
        $serviceEnvio        = $this->get('soporte.EnvioPlantilla');
        $objPunto      = $objSession->get('ptoCliente');
        $intIdPunto         = $objPunto['id'];
        $strNoContrato      = $strNumeroContrato;
        $strFechaCreacion    = 0;
        $strNombreCliente         = "";
        try
        {
           if(!empty($strNumeroAdendum))
            {
                $strNoContrato   = $strNumeroAdendum;
                $objAdendum   = $emComercial->getRepository('schemaBundle:InfoAdendum')
                                            ->findOneBy(array('numero' => $strNumeroAdendum,
                                                              'estado' => array('Activo','Pendiente','PorAutorizar')
                                                            ,'puntoId'       => $intIdPunto));
                $strFechaCreacion = $objAdendum->getFeCreacion();
            } 
            else if(!empty($strNumeroContrato))
            {
                $entityContrato = $emComercial->getRepository('schemaBundle:InfoContrato')
                                              ->findOneBy(array("numeroContrato"     => $strNumeroContrato,
                                                                "estado"             => array('Activo','Pendiente','PorAutorizar')
                                                                ,'personaEmpresaRolId'=>$objPunto['id_persona_empresa_rol']));
                
                $objAdendum     = $emComercial->getRepository('schemaBundle:InfoAdendum')
                                              ->findOneBy(array('contratoId' => $entityContrato->getId(),
                                                                'tipo'       =>'C',
                                                                'estado'     => array('Activo','Pendiente','PorAutorizar')));
                $intIdPunto       = $objAdendum->getPuntoId();
                $strFechaCreacion = $entityContrato->getFeCreacion();
            }
    
            $objPuntoCliente = $emComercial->getRepository('schemaBundle:InfoPunto')
                                        ->find($intIdPunto);
            $arrayCliente = $emComercial->getRepository("schemaBundle:InfoPersona")
                                        ->getDatosClientePorLogin($objPunto['login']);              
            $objVendedor  = $emComercial->getRepository('schemaBundle:InfoPersona')
                                        ->findOneByLogin($objPuntoCliente->getUsrVendedor());
            $strNombreVendedor ="No identificado";
            if($objVendedor)
            {
               $strNombreVendedor = $objVendedor->__toString();
            }
    
            $arrayServicios= $emComercial->getRepository('schemaBundle:InfoServicio')
                                         ->findBy(array("puntoId"    => $objPuntoCliente->getId()));
            $strNombrePlan="";
            foreach($arrayServicios as $serv)
            {
                if($serv->getPlanId())
                {
                    $strNombrePlan = $serv->getPlanId()->getNombrePlan();
                    break;
                }
                
            } 
            $strNombreCliente  = $arrayCliente[0]['razonSocial'];
            if(!empty($arrayCliente[0]['nombres']))
            {
                $strNombreCliente = $arrayCliente[0]['nombres'] . " " . $arrayCliente[0]['apellidos'];
            }
        

            $arrayParametrosMail    = array( 
                                            "noContrato"        => $strNoContrato,
                                            "feContrato"        => strval(date_format($strFechaCreacion, "d/m/Y")) ,
                                            "tipo"              => ($objAdendum->getFormaContrato()) ? $objAdendum->getFormaContrato() : "Físico",
                                            "identificacion"    => $arrayCliente[0]['identificacionCliente'],
                                            "cliente"           => $strNombreCliente,
                                            "vendedor"          => $strNombreVendedor,
                                            "plan"              => $strNombrePlan,
                                            "login"             =>$objPuntoCliente->getLogin());
    
            $arrayDestinatarios[]=$strCorreo;
            $serviceEnvio->generarEnvioPlantilla(  " SOLICITUD DE AUTORIZACION ", 
                                                    array_unique($arrayDestinatarios), 
                                                    'ENV_AUT_CTR', 
                                                    $arrayParametrosMail,
                                                    $strPrefijoEmpresa,
                                                    '',
                                                    '',
                                                    null, 
                                                    true,
                                                    'notificaciones_telcos@telconet.ec');
    
            $arrayResponse     = array('intStatus'   => 200,
                                       'strStatus'   => 'OK',
                                       'strMensaje'  => 'Se ha enviado la notificación de autorización del contrato',
                                       'strNumeroContrato' => $strNumeroContrato,
                                       'strCorreo'   => $strCorreo);
            $objResponse       = new Response(json_encode($arrayResponse));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse; 
        }
        catch(\Exception $e)
        {
            $arrayResponse     = array('intStatus'   => 500,
            'strStatus'   => 'Error',
            'strMensaje'  => 'No ha enviado la notificación de autorización del contrato',
            'strNumeroContrato' => $strNumeroContrato,
            'strCorreo'   => $strCorreo);

        }
        $objResponse       = new Response(json_encode($arrayResponse));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
        
    }

    /**
     * gridAdendumContrato Función que muestra los correos de los departamentos para notificar el presente contrato.
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 01-09-2022
     * @return array
     */
    public function gridAdendumContratoAction()
    {
        $serviceUtil  = $this->get('schema.Util');
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $emComercial  = $this->getDoctrine()->getManager('telconet');

        $objPeticion        = $this->get('request');
        $objSession         = $this->get( 'session' );

        $strUsrCreacion     = $objSession->get('user');
        $strIpClient        = $objPeticion->getClientIp();
        $strContratoId      = $objPeticion->get('contratoId');
        $strDescripcion     = $objPeticion->get('descripcion');
        $strEstado          = $objPeticion->get('estado');

        $arrayDatosBusqueda                     = array();
        $arrayDatosBusqueda['strContratoId']    = $strContratoId;
        $arrayDatosBusqueda['noAdendum']        = $strDescripcion;
        $arrayDatosBusqueda['strEstado']        = $strEstado;
               
        try
        {
            $arrayJson = $emComercial->getRepository('schemaBundle:InfoContrato')
                                     ->generarJsonAdendumContrato($arrayDatosBusqueda);
            if(!empty($arrayJson))
            {
                $arrayAdendum  = $arrayJson['encontrados'];
                foreach($arrayAdendum as $arrayValores)
                {
                    $arrayValorAdendum                  = $arrayValores;
                    $objPunto                           = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                                      ->findOneById($arrayValores['puntoId']);
                    $arrayValorAdendum['login']         = $objPunto->__toString();
                    $arrayValorAdendum['cliente']       = $objPunto->getPersonaEmpresaRolId()->getPersonaId()->__toString();
                    $arrayEntregables                   = array('intId'         => $arrayValores['contratoId'], 
                                                                'intIdAdendum'  => $arrayValores['idAdendum']);
                    $arrayValorAdendum['linkArchivo']   = $this->generateUrl('infocontrato_newArchivoDigital', $arrayEntregables);
                    $arrayRegistrosAdendum[]            = $arrayValorAdendum;
                }
                $arrayResponse = array('intStatus'   => 200,
                                       'strStatus'   => 'OK',
                                       'strMensaje'  => 'OK',
                                       'total'       => $arrayJson['total'],
                                       'encontrados' => $arrayRegistrosAdendum);
            }
            else
            {
                $arrayResponse = array('intStatus'   => 500,
                                       'strStatus'   => 'ERROR',
                                       'strMensaje'  => 'No se encontraron registros',
                                       'total'       => 0,
                                       'encontrados' => []);
            }
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+',
                                      'InfoContratoController->gridAdendumContratoAction',
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strIpClient);
            $arrayResponse  = array('intStatus'   => 500,
                                    'strStatus'   => 'ERROR',
                                    'strMensaje'  => $ex->getMessage(),
                                    'total'       => 0);
        }
        $objResponse       = new Response(json_encode($arrayResponse));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * reenviarDocumentoContrato Función que permite reenviar documento de contrato
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 09-09-2022
     * @return array
     */
    public function reenviarDocumentoContratoAction()
    {
        $serviceUtil        = $this->get('schema.Util');
        $emComercial        = $this->getDoctrine()->getManager('telconet');

        $objRequest         = $this->get('request');
        $objSession         = $this->get('session');

        $strUsrCreacion     = $objSession->get('user');
        $strCodEmpresa      = $objSession->get('idEmpresa');
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $strIpClient        = $objRequest->getClientIp();

        $strCorreo          = $objRequest->get("correoCliente");
        $strNumeroContrato  = $objRequest->get("numeroContrato");
        $strTipoContrato    = $objRequest->get("tipoContrato");
        $strNumeroAdendum   = $objRequest->get("numeroAdendum");

        $objPtoCliente      = $objSession->get('ptoCliente');
        $intIdPunto         = $objPtoCliente['id'];
        try
        {
            if(!$intIdPunto)
            {
                throw new \Exception('No se ha seleccionado un punto de cliente');
            }

            $objContrato     = $emComercial->getRepository('schemaBundle:InfoContrato')
                                           ->findOneBy(array('numeroContrato' => $strNumeroContrato,
                                                              'personaEmpresaRolId'  => $objPtoCliente['id_persona_empresa_rol'],
                                                             'estado'         => array('Activo','Pendiente','PorAutorizar')
                                                            )
                                                        );
            $arrayParametros = array('ipCreacion'           => $strIpClient,
                                     'codEmpresa'           => $strCodEmpresa,
                                     'prefijoEmpresa'       => $strPrefijoEmpresa,
                                     'usrCreacion'          => $strUsrCreacion,
                                     'origen'               => "WEB",
                                     'tipo'                 => $strTipoContrato,
                                     'personaEmpresaRolId'  => $objContrato->getPersonaEmpresaRolId()->getId(),
                                     'pin'                  => "NO-APLICA",
                                     'cambioRazonSocial'    => "N",
                                     'puntoId'              => $intIdPunto,
                                     'numeroAdendum'        => $strNumeroAdendum,
                                     'correoContrato'       => array($strCorreo),
                                     'generarDocumentos'    => array('contratoId'                       => $objContrato->getId(),
                                                                     'recuperarDocumentosDigitales'     => $strTipoContrato,
                                                                     'asunto'                           => "REENVIO DE DOCUMENTO DE CONTRATO",
                                                                     'isMasivo'                         => false
                                                                    )
                                    
                                    );
            /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
            $serviceInfoContrato    = $this->get('comercial.InfoContrato');
            $arrayRespReenviarContra= $serviceInfoContrato->reenviarDocumentoContrato($arrayParametros);
            if($arrayRespReenviarContra['status'] != 'OK')
            {
                throw new \Exception($arrayRespReenviarContra['message']);
            }
            $arrayResponse  = array('intStatus'   => $arrayRespReenviarContra['status'],
                                    'strStatus'   => 'OK',
                                    'strMensaje'  => $arrayRespReenviarContra['message'],
                                    'strData'     => $arrayRespReenviarContra['data']);
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+',
                                      'InfoContratoController->reenviarDocumentoContratoAction',
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strIpClient);
            $arrayResponse  = array('intStatus'   => 500,
                                    'strStatus'   => 'ERROR',
                                    'strMensaje'  => $ex->getMessage());
        }
        $objResponse       = new Response(json_encode($arrayResponse));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
        
    }

}
