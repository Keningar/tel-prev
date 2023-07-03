<?php
/*
 * To change this template, choose Tools | Templates and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;

use telconet\schemaBundle\Entity\InfoContrato;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\schemaBundle\Entity\InfoContratoFormaPago;
use telconet\seguridadBundle\Service\CryptService;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use Symfony\Component\HttpFoundation\Response;

use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Clase para crear la Solicitud de Nuevo Nodo
 * 
 * @author Allan Suarez         <arsuarez@telconet.ec> 
 * @version 1.0 13-03-2015
*/
class InfoSolicitudNuevoNodoController extends Controller implements TokenAuthenticatedController {
    
    private $UPLOAD_PATH = 'public/uploads/documentos/';
    /**
    * index
    * 
    * @author Allan Suarez <arsuarez@telconet.ec> 
    * @version 1.0 13-03-2015
   */
    public function indexAction() 
    {   
        $rolesPermitidos = array();
        
        if (true === $this->get('security.context')->isGranted('ROLE_280-2357'))
        {
            $rolesPermitidos[] = 'ROLE_280-2357'; //Autorizar TEC Solicitud
        }
        if (true === $this->get('security.context')->isGranted('ROLE_280-2358'))
        {
            $rolesPermitidos[] = 'ROLE_280-2358'; //Autorizar Legal Solicitud
        }
        if (true === $this->get('security.context')->isGranted('ROLE_280-2359'))
        {
            $rolesPermitidos[] = 'ROLE_280-2359'; //Rechazar Autorizaciones TEC/Legal
        }
        if (true === $this->get('security.context')->isGranted('ROLE_280-2360'))
        {
            $rolesPermitidos[] = 'ROLE_280-2360'; //Reversar 
        }
        if (true === $this->get('security.context')->isGranted('ROLE_280-2361'))
        {
            $rolesPermitidos[] = 'ROLE_280-2361'; //Gestion de contrato de solicitud de Nodo
        }
        if (true === $this->get('security.context')->isGranted('ROLE_280-2362'))
        {
            $rolesPermitidos[] = 'ROLE_280-2362'; //Habilitar Nodo
        }                
        
        return $this->render('tecnicoBundle:InfoSolicitudNuevoNodo:index.html.twig', array(
            'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /**
      * ajaxAutorizarTecAction
      *
      * Método encargado de realizar la autorizacion tecnica de la solicitud de nuevo nodo
      *                                                    
      * @return json con respuesta
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 19-03-2015
      * 
      * @author Antonio Ayala <afayala@telconet.ec>
      * @version 1.1 29-07-2019 - Se agregó ingreso de excepción
      * 
      * @Secure(roles="ROLE_280-2357")
      */
    public function ajaxAutorizarTecAction()
    {
        $respuesta  = new Response();
        $respuesta  ->headers->set('Content-Type', 'text/json');
        
        $em         = $this->get('doctrine')->getManager('telconet');
        
        $em->getConnection()->beginTransaction();
        
        $objPeticion     = $this->get('request');
        
        $objSession      = $objPeticion->getSession();
        $strUserSession  = $objSession->get('user');
        $strIpCreacion   = $objPeticion->getClientIp();
        
        $intSolicitud    = $objPeticion->get('idSolicitud');
        $strObservacion  = $objPeticion->get('observacion');
        $serviceUtil     = $this->get('schema.Util');
        
        try
        {
            
            $objSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intSolicitud);
            
            if($objSolicitud)
            {
                $objSolicitud->setEstado('AutorizadaTecnico');
                $objSolicitud->setObservacion($strObservacion);
                
                $objSolicitudHistorial = new InfoDetalleSolHist();
                $objSolicitudHistorial->setDetalleSolicitudId($objSolicitud);                                
                $objSolicitudHistorial->setMotivoId($objSolicitud->getMotivoId());
                $objSolicitudHistorial->setEstado("AutorizadaTecnico");
                $objSolicitudHistorial->setObservacion("Se Autoriza TEC a solicitud de nuevo Nodo");    
                $objSolicitudHistorial->setUsrCreacion($objPeticion->getSession()->get('user'));
                $objSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                
                $em->persist($objSolicitud);
                $em->persist($objSolicitudHistorial);
                
                $em->flush();
                $em->getConnection()->commit();
                
                $resultado = '{"success":true,"respuesta":"Autorizada Solicitud TEC de nuevo Nodo"}';
            
            }
            else
            {
                $resultado = '{"success":false,"respuesta":"Ha ocurrido un error, por favor reporte a Sistemas"}';
            }
        
        }
        catch(\Exception $objEx)
        {
            if($em->getConnection()->isTransactionActive())
            {                
                $em->getConnection()->rollback();
            }
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoSolicitudNuevoNodoController->ajaxAutorizarTecAction',
                                      $objEx->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);
            
            $resultado = '{"success":false,"respuesta":"Ha ocurrido un error, por favor reporte a Sistemas"}';
            
        }
        
        $respuesta->setContent($resultado);
        return $respuesta;
    }       
    
    /**
      * ajaxAutorizarLegalAction
      *
      * Método encargado de realizar la autorizacion legal de la solicitud de nuevo nodo
      *                                                    
      * @return json con respuesta
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 19-03-2015
      * 
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.1 22-02-2016 - Se guarda informacion de fe de inicio de contrato de nodo
      * 
      * @author Antonio Ayala <afayala@telconet.ec>
      * @version 1.2 20-06-2019 - Se elimina campo de Incremento Anual y se habilita campo No. Contrato
      * Se eliminó valor anticipo
      *
      * @Secure(roles="ROLE_280-2358")
      */
    public function ajaxAutorizarLegalAction()
    {
        $respuesta  = new Response();
        $respuesta  ->headers->set('Content-Type', 'text/json');
        
        $em         = $this->get('doctrine')->getManager('telconet');
                
        $em->getConnection()->beginTransaction();
        
        $objPeticion     = $this->get('request');
        
        $objSession       = $objPeticion->getSession();
        $strUserSession   = $objSession->get('user');
        $strIpCreacion    = $objPeticion->getClientIp();
        
        $strContactoLegal  = $objPeticion->get('contactoLegal');
        $strNumeroContrato = $objPeticion->get('numeroContrato');       
        $strFecFinContrato = $objPeticion->get('fechaFinContrato');
        $strFecIniContrato = $objPeticion->get('fechaIniContrato');
        $strFormaPago      = $objPeticion->get('formaPago');
        $strBanco          = $objPeticion->get('banco');
        $strTipoCuenta     = $objPeticion->get('tipoCuenta');
        $intNumeroCuenta   = $objPeticion->get('numeroCuenta');
        $intValorGarantia  = $objPeticion->get('valorGarantia');
        $strOfiRepLegal    = $objPeticion->get('ofiRepLegal');
        $strRepLegal       = $objPeticion->get('repLegal');        
        $strProvincia      = $objPeticion->get('provincia'); 
        $strValor          = $objPeticion->get('valor'); 
        $intSolicitud      = $objPeticion->get('idSolicitud'); 
        $serviceUtil       = $this->get('schema.Util');
        
        $arrayParametros = array();
        $arrayParametros['numContrato'] = $strNumeroContrato;
                                
        
        try
        {
            $objSolicitud = $em->getRepository("schemaBundle:InfoDetalleSolicitud")->find($intSolicitud);
            
            $objSolicitud->setEstado('AutorizadaLegal');            

            $objSolicitudHistorial = new InfoDetalleSolHist();
            $objSolicitudHistorial->setDetalleSolicitudId($objSolicitud);                                
            $objSolicitudHistorial->setMotivoId($objSolicitud->getMotivoId());
            $objSolicitudHistorial->setEstado("AutorizadaLegal");
            $objSolicitudHistorial->setObservacion("Se Autoriza Legal y genera contrato a solicitud de nuevo Nodo");    
            $objSolicitudHistorial->setUsrCreacion($objPeticion->getSession()->get('user'));
            $objSolicitudHistorial->setFeCreacion(new \DateTime('now'));

            $em->persist($objSolicitud);
            $em->persist($objSolicitudHistorial);
            $em->flush();            
            
            $objContrato = new InfoContrato();
            
            //Se obtiene objeto forma pago
            $objFormaPago = $em->getRepository("schemaBundle:AdmiFormaPago")->find($strFormaPago);
            
            if($objFormaPago)
            {
                $objContrato->setFormaPagoId($objFormaPago);
            }
            
            $objContrato->setEstado("Pendiente");
            $objContrato->setNumeroContrato($strNumeroContrato);
            
            //Se aumenta la numeracion del contrato
            $objAdmiNumeracion = $em->getRepository("schemaBundle:AdmiNumeracion")->findOneBy(array('oficinaId'=>$strProvincia,'codigo'=>'CONNO'));
            if($objAdmiNumeracion)
            {
                $objAdmiNumeracion->setSecuencia($objAdmiNumeracion->getSecuencia() + 1);
                $em->persist($objAdmiNumeracion);
                $em->flush();
            }
            
            $objContrato->setFeCreacion(new \DateTime('now'));
            $objContrato->setUsrCreacion($objPeticion->getSession()->get('user'));
            $objContrato->setIpCreacion($objPeticion->getClientIp());
            
            $objTipoContrato = $em->getRepository("schemaBundle:AdmiTipoContrato")->findOneBy(array('descripcionTipoContrato'=>'NODO'));
            
            if($objTipoContrato)
            {
                $objContrato->setTipoContratoId($objTipoContrato);
            }            
            
            $objContrato->setFeFinContrato(new \DateTime($strFecFinContrato));
            $objContrato->setFeIniContrato(new \DateTime($strFecIniContrato));
            $objContrato->setValorContrato($strValor);
            $objContrato->setValorGarantia($intValorGarantia);
            $objContrato->setUsrRepLegal($strRepLegal);
            $objContrato->setOficinaRepLegal($strOfiRepLegal);
            
            $em->persist($objContrato);
            $em->flush();
            
            //SE GUARDA LA REFERENCIA DEL CONTRATO EN LA SOLICITUD DE NUEVO NODO
            
            $objCaracteristica = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy(array('descripcionCaracteristica'=>'CONTRATO'));
            
            $objSolicitudCaract = new InfoDetalleSolCaract();
            
            $objSolicitudCaract->setDetalleSolicitudId($objSolicitud);
            $objSolicitudCaract->setEstado("Activo");
            $objSolicitudCaract->setCaracteristicaId($objCaracteristica);
            $objSolicitudCaract->setValor($objContrato->getId());
            $objSolicitudCaract->setFeCreacion(new \DateTime('now'));
            $objSolicitudCaract->setUsrCreacion($objPeticion->getSession()->get('user'));
            
            $em->persist($objSolicitudCaract);
            $em->flush();
            
            //Si existe informacion de banco se crea registro en la tabla info contrato forma de pago
            if($strBanco!='')
            {
                $objContratoFormaPago = new InfoContratoFormaPago();
                
                $objTipoCuenta    = $em->getRepository('schemaBundle:AdmiTipoCuenta')->find($strTipoCuenta);
                
                $objBcoTipoCuenta = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                                       ->findOneBy(array('bancoId'=>$strBanco,'tipoCuentaId'=>$strTipoCuenta));
                
                $objContratoFormaPago->setContratoId($objContrato);
                
                if($objBcoTipoCuenta)
                {
                    $objContratoFormaPago->setBancoTipoCuentaId($objBcoTipoCuenta);
                }
                
                $objContratoFormaPago->setTitularCuenta($strContactoLegal);
                $objContratoFormaPago->setEstado("Activo");
                $objContratoFormaPago->setFeCreacion(new \DateTime('now'));
                $objContratoFormaPago->setUsrCreacion($objPeticion->getSession()->get('user'));
                $objContratoFormaPago->setIpCreacion($objPeticion->getClientIp());
                
                /* @var $serviceCrypt \telconet\seguridadBundle\Service\CryptService */
                $serviceCrypt = $this->get('seguridad.Crypt');
                $strNumeroCtaTarjeta = $serviceCrypt->encriptar($intNumeroCuenta);
                  
                $objContratoFormaPago->setNumeroCtaTarjeta($strNumeroCtaTarjeta);
                
                if($objTipoCuenta)
                {
                    $objContratoFormaPago->setTipoCuentaId($objTipoCuenta);
                }
                
                $em->persist($objContratoFormaPago);
                $em->flush();
            }                                    
            
            $em->getConnection()->commit();
            
            $resultado = '{"success":true,"respuesta":"Autorizacion Legal de Nuevo Nodo - Se Genera Contrato"}';
                                  
        }
        catch(\Exception $objEx)
        {
            if($em->getConnection()->isTransactionActive())
            {                
                $em->getConnection()->rollback();
            }
            
            $serviceUtil->insertError('Telcos+',
                                      'InfoSolicitudNuevoNodoController->ajaxAutorizarLegalAction',
                                      $objEx->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);
            
            $resultado = '{"success":false,"respuesta":"Ha ocurrido un error, por favor reporte a Sistemas"}';
        }
        
        $respuesta->setContent($resultado);
        return $respuesta;
    }       
    
    /**
      * ajaxReversarRechazarAction
      *
      * Método encargado de reversar o rechazar las autorizacion legales o tecnicas
      *                                                    
      * @return json con respuesta
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 19-03-2015
      * 
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.1 23-02-2016 - Se agrega validacion de rechazo cuando se realiza renovacion de contrato para que el anterior sea
      *                           anulado cuando el contrato nuevo sea creado.
      * 
      * @Secure(roles="ROLE_280-2359")
      */
    public function ajaxReversarRechazarAction()
    {        
        $respuesta  = new Response();
        $respuesta  ->headers->set('Content-Type', 'text/json');
        
        $em         = $this->get('doctrine')->getManager('telconet');
        
        $em->getConnection()->beginTransaction();
        
        $peticion = $this->get('request');
        $idSolicitud = $peticion->get('idSolicitud');
        $tipoRechazo = $peticion->get('tipoRechazoReverso');
        $tipoAccion  = $peticion->get('tipoAccion');
        $observacion = $peticion->get('observacion');
        $esRenovacion= $peticion->get('esRenovacion');
        
        $estado = '';
        $respuestaSalida = '';
        
        if($tipoAccion == 'rechazar')
        {
            if($tipoRechazo === 'TEC')
            {
                $estado = 'RechazadaTecnico';
                $respuestaSalida = 'Solicitud Rechazada por gestion Tecnica';
            }
            else if($tipoRechazo === 'Legal')
            {
                $estado = 'RechazadaLegal';
                $respuestaSalida = 'Solicitud Rechazada por gestion Legal';
            }
        }
        else //reverso
        {
            if($tipoRechazo === 'TEC')
            {
                $estado = 'Pendiente';
                $respuestaSalida = 'Reverso : Solicitud cambio a estado Pendiente';
            }
            else if($tipoRechazo === 'Legal')
            {
                $estado = 'AutorizadaTecnico';
                $respuestaSalida = 'Reverso '.($esRenovacion=='true'?'por Renovacion ':'').': Solicitud cambio a estado AutorizadaTecnico';
            }
        }
        
        try
        {
            
            $objSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->find($idSolicitud);
            
            if($objSolicitud)
            {
                $objSolicitud->setEstado($estado);
                $objSolicitud->setObservacion($observacion);
                
                if($tipoAccion === 'rechazar')
                {                
                    $objSolicitud->setFeRechazo(new \DateTime('now'));                                        
                }
                
                //Si es renovacion el contrato anterior sera anulado una vez que el nuevo contrato sea creado
                if($tipoAccion === 'reversar' && $tipoRechazo == 'Legal' && $esRenovacion!='true')
                {                
                    //Se anula contrato por reverso de autorizacion legal
                    $objCaract = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                    ->getSolicitudCaractPorTipoCaracteristica($idSolicitud,'CONTRATO');
                    
                    if($objCaract)
                    {
                        $objContrato = $em->getRepository('schemaBundle:InfoContrato')->find($objCaract[0]->getValor());
                                    
                        $objContrato->setEstado('Anulado');
                        $objContrato->setFeRechazo(new \DateTime('now'));
                        $objContrato->setUsrRechazo($peticion->getSession()->get('user'));
                        $em->persist($objContrato);
                        
                        $objCaract[0]->setEstado("Eliminado");
                        $em->persist($objCaract[0]);
                    }
                }
                
                $objSolicitudHistorial = new InfoDetalleSolHist();
                $objSolicitudHistorial->setDetalleSolicitudId($objSolicitud);                                
                $objSolicitudHistorial->setMotivoId($objSolicitud->getMotivoId());
                $objSolicitudHistorial->setEstado($estado);
                $objSolicitudHistorial->setObservacion($observacion!=''?$observacion:$respuestaSalida);    
                $objSolicitudHistorial->setUsrCreacion($peticion->getSession()->get('user'));
                $objSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                
                $em->persist($objSolicitud);
                $em->persist($objSolicitudHistorial);
                
                $em->flush();
                $em->getConnection()->commit();
                
                $resultado = '{"success":true,"respuesta":"'.$respuestaSalida.'"}';
            
            }
            else
            {
                $resultado = '{"success":false,"respuesta":"Ha ocurrido un error, por favor reporte a Sistemas"}';
            }
        
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {                
                $em->getConnection()->rollback();
            }
            error_log($e->getMessage());
            $resultado = '{"success":false,"respuesta":"Ha ocurrido un error, por favor reporte a Sistemas"}';
        }
        
        $respuesta->setContent($resultado);
        return $respuesta;
    }        
    
    /**
      * ajaxHabilitarNodoAction
      *
      * Método encargado de habilitar el nodo despues del tramite de contrato firmado            
      *                                                    
      * @return json con respuesta
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 19-03-2015
      * 
      * @Secure(roles="ROLE_280-2362")
      */
    public function ajaxHabilitarNodoAction()
    {
        $respuesta  = new Response();
        $respuesta  ->headers->set('Content-Type', 'text/json');
        
        $em         = $this->get('doctrine')->getManager('telconet');
        
        $em->getConnection()->beginTransaction();
        
        $peticion = $this->get('request');
        $idSolicitud = $peticion->get('idSolicitud');
        $observacion = $peticion->get('observacion');
        
        try
        {
            
            $objSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->find($idSolicitud);
            
            if($objSolicitud)
            {
                $objSolicitud->setEstado('Finalizada');
                $objSolicitud->setObservacion($observacion);
                
                $objSolicitudHistorial = new InfoDetalleSolHist();
                $objSolicitudHistorial->setDetalleSolicitudId($objSolicitud);                                
                $objSolicitudHistorial->setMotivoId($objSolicitud->getMotivoId());
                $objSolicitudHistorial->setEstado("Finalizada");
                $objSolicitudHistorial->setObservacion($observacion!=""?$observacion:"Nodo Habilitado");    
                $objSolicitudHistorial->setUsrCreacion($peticion->getSession()->get('user'));
                $objSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                
                $em->persist($objSolicitud);
                $em->persist($objSolicitudHistorial);
                
                $em->flush();
                $em->getConnection()->commit();
                
                $resultado = '{"success":true,"respuesta":"Se Habilita Nodo Correctamente"}';
            
            }
            else
            {
                $resultado = '{"success":false,"respuesta":"Ha ocurrido un error, por favor reporte a Sistemas"}';
            }
        
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {                
                $em->getConnection()->rollback();
            }
            
            $resultado = '{"success":false,"respuesta":"Ha ocurrido un error, por favor reporte a Sistemas"}';
        }
        
        $respuesta->setContent($resultado);
        return $respuesta;
    }
    
    /**
     * Funcion que genera el json para el historial de cada solicitud de
     * nuevo nodo
     * 
     * @author Allan Suarez        <arsuarez@telconet.ec>     
     * @version 1.0 19-03-2015
    */
    public function ajaxGetHistorialSolicitudAction() 
    {
        $arr_encontrados = array();
        $respuesta  = new Response();
        $respuesta  ->headers->set('Content-Type', 'text/json');
        $em         = $this->getDoctrine()->getManager('telconet');
        $peticion   = $this->get('request');

        $idSolicitud = $peticion->get('idSolicitud');
        
        //InfoDetalleSolicitud
        $objDetalleSolHist = $em->getRepository('schemaBundle:InfoDetalleSolHist')
                                ->getDetalleSolicitudHistorial($idSolicitud);
        
        foreach ($objDetalleSolHist as $detalleSolHist)
        {
            $arr_encontrados[]=array( 'observacion'  => $detalleSolHist->getObservacion(),
                                      'fechaCrea'    => date_format($detalleSolHist->getFeCreacion(), 'Y-m-d H:i:s'),
                                      'usuarioCrea'  => $detalleSolHist->getUsrCreacion(),
                                      'estado'       => $detalleSolHist->getEstado()
                                     );
        }
        
        $num = count($arr_encontrados);
        $data=json_encode($arr_encontrados);
        $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';
        $respuesta->setContent($resultado);

        return $respuesta;
    }
    
    /**
    * ajaxGetInformacionContrato
    *
    * Método que obtiene la hora y fecha del servidor para gestion de calculo de tiempos en las tareas
    *      
    * @return json
    *
    * @author Allan Suárez <arsuarez@telconet.ec>    
    * @version 1.0 10-02-2015 
    * 
    * @author Allan Suárez <arsuarez@telconet.ec>    
    * @version 1.1 23-02-2016 - Se genera validacion para cuando la informacion de contrato a cargar sea de contrato nuevo o por renovacion
    *
    * @author Antonio Ayala <afayala@telconet.ec>
    * @version 1.2 20-06-2019 - Se elimina campo de incremento anual
    * Se eliminó valor anticipo 
    *
    * @Secure(roles="ROLE_280-2361")      
    */
    public function ajaxGetInformacionContratoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em  = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $peticion   = $this->get('request');
        
        $codEmpresa = $peticion->getSession()->get('idEmpresa');        

        $idProvincia = $peticion->get('idProvincia');
        $idElemento  = $peticion->get('idElemento');
        $tipoAccion  = $peticion->get('tipoAccion');
        
        try
        {                                   
            //Se calcula el nuevo numero de contrato a generar
            $secuencia_asig     = null;
            $numeroDeContrato   = null;            
           
            $datosNumeracion    = $em->getRepository('schemaBundle:AdmiNumeracion')->findOneBy(array('oficinaId'=>$idProvincia,'codigo'=>'CONNO'));
            if( $datosNumeracion )
            {
                $secuencia_asig     = str_pad($datosNumeracion->getSecuencia(),7, "0", STR_PAD_LEFT);
                $numeroDeContrato = $datosNumeracion->getNumeracionUno()."-".$datosNumeracion->getNumeracionDos()."-".$secuencia_asig;                
            }                         
            
            $arrayResultado = $em->getRepository("schemaBundle:InfoContrato")->getRegistroContratoPorElementoNodo($idElemento,$codEmpresa);
            
            //Se obtiene informacion de contacto principal                
            $contactoNodo = $emInfraestructura->getRepository("schemaBundle:InfoContactoNodo")->getContactoPrincipalNodo($idElemento);

            $razonSocial = 'N/A';

            if($contactoNodo && count($contactoNodo)>0)
            {
                $razonSocial = sprintf($contactoNodo[0]);
            }
            
            //Si se requiere renovar el contrato se carga toda la informacion necesaria
            if($tipoAccion == 'renovarContrato' || $arrayResultado)
            {                                
                if($arrayResultado)
                {
                    /* @var $serviceCrypt \telconet\seguridadBundle\Service\CryptService */
                    $serviceCrypt = $this->get('seguridad.Crypt');
                    
                    $arrayEncontrados = Array();

                    foreach($arrayResultado as $data)
                    {
                        $arrayEncontrados = array( 
                                        'success'          => true,
                                        "esRenovacion"     => true,
                                        "contactoNodo"     => $razonSocial,
                                        "solicitud"        => $data['solicitud'],
                                        "idPersona"        => $data['idPersona'],
                                        "idContrato"       => $data['idContrato'],
                                        "numeroContrato"   => $numeroDeContrato,
                                        "numeroContAnt"    => $data['numeroContrato'],
                                        "valor"            => $data['valor'],
                                        "garantia"         => $data['garantia'],
                                        "fechaInicio"      => $data['fechaInicio'],                                            
                                        "fechaFin"         => $data['fechaFin'],
                                        "duracion"         => $data['duracion'],
                                        "oficina"          => $data['oficina'],
                                        "repLegal"         => $data['repLegal'],
                                        "login"            => $data['login'],
                                        "banco"            => $data['banco']!=0?$data['banco']:'',
                                        "tipoCuenta"       => $data['tipoCuenta']!=0?$data['tipoCuenta']:'',
                                        "formaPago"        => $data['formaPago'],
                                        "numeroPago"       => $data['numeroPago']!='N/A'?$serviceCrypt->descencriptar($data['numeroPago']):'',
                                        "nodo"             => $data['nodo'],
                                        "direccion"        => $data['direccion'],
                                        "canton"           => $data['canton'],
                                        "provincia"        => $data['provincia']
                                        );  
                    }                
                }
                
                $response = json_encode($arrayEncontrados);
            }
            else
            {
                $fechaActual =  new \DateTime('now');   
                $fecha       =  $fechaActual->format('Y-m-d');
                $hora        =  $fechaActual->format('H:i');                                 
                
                $response = json_encode(array(  'success'       => true,    
                                                "esRenovacion"  => false,
                                                'fechaActual'   => $fecha,
                                                'horaActual'    => $hora,
                                                'numeroContrato'=> $numeroDeContrato,
                                                'contactoLegal' => $razonSocial));
            }
        }
        catch(\Exception $e)
        {
             $response= json_encode(array('success'=> false , 'error'=>$e->getMessage()));   
        }
                
        $respuesta->setContent($response);
        return $respuesta;
    }           
    
    /**
     * ajaxGetBancoPorTipoCuentaAction
     *
     * Método que obtiene los bancos vinculados a un tipo cuenta enviado como parametro
     *           
     * @author Allan Suárez <arsuarez@telconet.ec>
     * 
     * @version 1.0 22-02-2016       
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * 
     * @author Andrés Montero<amontero@telconet.ec>
     * @version 1.1 29-06-2017
     * Se usa la nueva funcion para obtener bancos findBancosTipoCuentaPorCriterio
     * 
     * @author Steven Ruano <sruano@telconet.ec>
     * @version 1.2 13-01-2023 se implementa obtencion del request para el autocomplete del combobox de banco.
     */
    public function ajaxGetBancoPorTipoCuentaAction()
    {
        $request   = $this->getRequest();
        $objSession   = $request->getSession();
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $arrayParametros                  = array();
        $arrayParametros['strTipoCuenta'] = $this->get('request')->get('tipoCuentaId');
        $arrayParametros['strbanco'] = $this->get('request')->get('query');
        $arrayParametros['arrayEstados']  = array('Activo','Activo-debitos');
        $arrayParametros['intPaisId']     = $objSession->get('intIdPais');
        $emComercial      = $this->getDoctrine()->getManager('telconet');
        $objListadoBancos    = $emComercial->getRepository('schemaBundle:AdmiBancoTipoCuenta')->findBancosTipoCuentaPorCriterio($arrayParametros);
        
        $arrayBancos = Array();
        
        if($objListadoBancos)
        {            
            foreach ($objListadoBancos as $objBancos)
            {
				$arrayBancos[] = array('idBanco'     => $objBancos->getBancoId()->getId(),
                                       'nombreBanco' => $objBancos->getBancoId()->getDescripcionBanco()
                                    );
            }
            
            $arrayResultado = array('total'=>count($arrayBancos),'encontrados'=>$arrayBancos);
        }
        else
        {
            $arrayResultado = array('total'=>'0','encontrados'=>'[]');
        }
        
        $response= json_encode($arrayResultado);
                
        $respuesta->setContent($response);
                
        return $respuesta;
    }
    
    
    /**
     * ajaxGetTipoContactoAction
     * 
     * Metodo que obtiene tanto los roles como los titulos posibles que un contacto de nodo puede tener
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 23-03-2016
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxGetTipoContactoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $tipoInfo  = $this->get('request')->get('tipoInfo'); 
        
        $emGeneral   = $this->get('doctrine')->getManager('telconet_general');
        $emComercial = $this->get('doctrine')->getManager('telconet');
        
        $arrayResultado = Array();
        
        switch($tipoInfo)
        {
            case 'rol':
                $arrayObjTipoRol = $emGeneral->getRepository('schemaBundle:AdmiRol')->getRolesByDescripcionTipoRol('Contacto Nodo');
                foreach($arrayObjTipoRol as $tipoRol)
                {
                    $arrayResultado[] = array('idRol' => $tipoRol->getId(),'nombreRol'=>$tipoRol->getDescripcionRol());
                }               
                break;
            case 'titulo':
                $arrayObjAdmiTitulo = $emComercial->getRepository('schemaBundle:AdmiTitulo')->findBy(array('estado'=>'Activo'));
                foreach($arrayObjAdmiTitulo as $titulo)
                {
                    $arrayResultado[] = array('idTitulo' => $titulo->getId(),'titulo'=>$titulo->getCodigoTitulo());
                }     
                break;
        }
        
        $arrayRespuesta = array('encontrados'=>$arrayResultado);
        
        $response= json_encode($arrayRespuesta);
        
        $respuesta->setContent($response);
                
        return $respuesta;
    }
    
    /**
     * ajaxRenovarContratoAction
     * 
     * Metodo que permite realizar la renovacion o creacion de un nuevo contrato al nodo existente
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 24-02-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - Se modifica para que cuando no venga razon social en la información de contacto, se coloque el nombre y apellidos del 
     *                propietario del local
     * @since 03-08-2016
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.2 20-06-2019 - Se elimina campo incremento anual
     * Se eliminó valor anticipo
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @Secure(roles="ROLE_280-2358")
     */
    public function ajaxRenovarContratoAction()
    {
        $respuesta  = new Response();
        $respuesta  ->headers->set('Content-Type', 'text/json');
        
        $emComercial         = $this->get('doctrine')->getManager('telconet');
        $emInfraestructura   = $this->get('doctrine')->getManager('telconet_infraestructura');
        
        $emComercial->getConnection()->beginTransaction();
        
        $peticion = $this->get('request');        
        
        $empresaCod       = $peticion->getSession()->get('idEmpresa');                
        $idElemento       = $peticion->get('idElemento');       
        $numeroContrato   = $peticion->get('numeroContrato');       
        $fechaFinContrato = $peticion->get('fechaFinContrato');
        $fechaIniContrato = $peticion->get('fechaIniContrato');
        $formaPago        = $peticion->get('formaPago');
        $banco            = $peticion->get('banco');
        $tipoCuenta       = $peticion->get('tipoCuenta');
        $numeroCuenta     = $peticion->get('numeroCuenta');
        $IdContratoAnt    = $peticion->get('idContratoAnt');        
        $valorGarantia    = $peticion->get('valorGarantia');
        $oficinaRepLegal  = $peticion->get('ofiRepLegal');
        $repLegal         = $peticion->get('repLegal');        
        $provincia        = $peticion->get('provincia'); 
        $valor            = $peticion->get('valor'); 
        $idSolicitud      = $peticion->get('idSolicitud'); 
        
        //Info de contacto
        $idPersona            = $peticion->get('idPersona'); 
        $yaExiste             = $peticion->get('yaexiste'); 
        $yaExisteRol          = $peticion->get('yaexisteRol'); 
        $cambioContacto       = $peticion->get('cambioContacto'); 
        $tipoContacto         = $peticion->get('tipoContacto'); 
        $tipoIdentificacion   = $peticion->get('tipoIdentificacion'); 
        $identificacion       = $peticion->get('identificacion'); 
        $tipoTributario       = $peticion->get('tipoTributario'); 
        $tipoGenero           = $peticion->get('tipoGenero'); 
        $tipoNacionalidad     = $peticion->get('tipoNacionalidad'); 
        $tipoTitulo           = $peticion->get('tipoTitulo'); 
        $nombres              = $peticion->get('nombres'); 
        $apellidos            = $peticion->get('apellidos'); 
        $razonSocial          = $peticion->get('razonSocial'); 
        $arrayFormasContacto  = $peticion->get('formasContacto')!=""?json_decode($peticion->get('formasContacto')):""; 
        
        try
        {
            //Tipo contrato NODO
            $objTipoContrato = $emComercial->getRepository("schemaBundle:AdmiTipoContrato")->findOneBy(array('descripcionTipoContrato'=>'NODO'));
            
            //Se anula el contrato anterior
            $objInfoContrato = $emComercial->getRepository("schemaBundle:InfoContrato")->find($IdContratoAnt);  
            
            if($objInfoContrato)
            {
                $objInfoContrato->setEstado('Anulado');
                $objInfoContrato->setFeRechazo(new \DateTime('now'));
                $objInfoContrato->setUsrRechazo($peticion->getSession()->get('user'));
                $emComercial->persist($objInfoContrato);
            }
            
            $objSolicitud = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")->find($idSolicitud);
            
            $objSolicitud->setEstado('AutorizadaLegal');            

            $objSolicitudHistorial = new InfoDetalleSolHist();
            $objSolicitudHistorial->setDetalleSolicitudId($objSolicitud);                                
            $objSolicitudHistorial->setMotivoId($objSolicitud->getMotivoId());
            $objSolicitudHistorial->setEstado("AutorizadaLegal");
            $objSolicitudHistorial->setObservacion("Se Autoriza Legal y genera contrato a solicitud de nuevo Nodo");    
            $objSolicitudHistorial->setUsrCreacion($peticion->getSession()->get('user'));
            $objSolicitudHistorial->setFeCreacion(new \DateTime('now'));

            $emComercial->persist($objSolicitud);
            $emComercial->persist($objSolicitudHistorial);
            $emComercial->flush();            
            
            //Se crea el nuevo contrato ( RENOVADO )
            
            $objContrato = new InfoContrato();
            
            //Se obtiene objeto forma pago
            $objFormaPago = $emComercial->getRepository("schemaBundle:AdmiFormaPago")->find($formaPago);
            
            if($objFormaPago)
            {
                $objContrato->setFormaPagoId($objFormaPago);
            }
            
            $objContrato->setEstado("Pendiente");
            $objContrato->setNumeroContrato($numeroContrato);
            
            //Se aumenta la numeracion del contrato
            $objAdmiNumeracion = $emComercial->getRepository("schemaBundle:AdmiNumeracion")
                                             ->findOneBy(array('oficinaId'=>$provincia,'codigo'=>'CONNO'));
            if($objAdmiNumeracion)
            {
                $objAdmiNumeracion->setSecuencia($objAdmiNumeracion->getSecuencia() + 1);
                $emComercial->persist($objAdmiNumeracion);
                $emComercial->flush();
            }
            
            $objContrato->setFeCreacion(new \DateTime('now'));
            $objContrato->setUsrCreacion($peticion->getSession()->get('user'));
            $objContrato->setIpCreacion($peticion->getClientIp());                        
            
            if($objTipoContrato)
            {
                $objContrato->setTipoContratoId($objTipoContrato);
            }            
            
            $objContrato->setFeFinContrato(new \DateTime($fechaFinContrato));
            $objContrato->setFeIniContrato(new \DateTime($fechaIniContrato));
            $objContrato->setValorContrato($valor);
            $objContrato->setValorGarantia($valorGarantia);
            $objContrato->setUsrRepLegal($repLegal);
            $objContrato->setOficinaRepLegal($oficinaRepLegal);            
            $emComercial->persist($objContrato);
            $emComercial->flush();                             
            
            //Actualizar informacion de espacio para mantener los valores concordantes en todas las dependencias del modulo  
            //Se guardan los valores totales de espacio para generacion posterior de contrato
            $objAdmiCaracteristica = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                 ->findOneBy(array('descripcionCaracteristica'=>'VALOR_NODO'));
            
            $objInfoDetalleSolCaract = $emComercial->getRepository("schemaBundle:InfoDetalleSolCaract")
                                                   ->findOneBy(array('detalleSolicitudId'=> $idSolicitud,
                                                                     'caracteristicaId'  => $objAdmiCaracteristica->getId())
                                                              );                   
            if($objInfoDetalleSolCaract)
            {                
                $objInfoDetalleSolCaract->setValor($valor);                
                $objInfoDetalleSolCaract->setUsrUltMod($peticion->getSession()->get('user'));
                $objInfoDetalleSolCaract->setFeUltMod(new \DateTime('now'));
                $emComercial->persist($objInfoDetalleSolCaract);
                $emComercial->flush();
            }
                                   
            $objCaractAnterior = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                    ->getSolicitudCaractPorTipoCaracteristica($idSolicitud,'CONTRATO');
            //Eliminamos la caracteristica anterior del contrato anulado
            if($objCaractAnterior)
            {
                $objCaractAnterior[0]->setEstado("Eliminado");
                $emComercial->persist($objCaractAnterior[0]);
                $emComercial->flush();
            }
            
            //SE GUARDA LA REFERENCIA DEL CONTRATO EN LA SOLICITUD DE NUEVO NODO
            $objCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                             ->findOneBy(array('descripcionCaracteristica'=>'CONTRATO'));
            
            $objSolicitudCaract = new InfoDetalleSolCaract();            
            $objSolicitudCaract->setDetalleSolicitudId($objSolicitud);
            $objSolicitudCaract->setEstado("Activo");
            $objSolicitudCaract->setCaracteristicaId($objCaracteristica);
            $objSolicitudCaract->setValor($objContrato->getId());
            $objSolicitudCaract->setFeCreacion(new \DateTime('now'));
            $objSolicitudCaract->setUsrCreacion($peticion->getSession()->get('user'));
            
            $emComercial->persist($objSolicitudCaract);
            $emComercial->flush();
            
            //Si existe informacion de banco se crea registro en la tabla info contrato forma de pago
            if($banco!='')
            {
                $objContratoFormaPago = new InfoContratoFormaPago();
                
                $objTipoCuenta    = $emComercial->getRepository('schemaBundle:AdmiTipoCuenta')->find($tipoCuenta);
                
                $objBcoTipoCuenta = $emComercial->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                                       ->findOneBy(array('bancoId'=>$banco,'tipoCuentaId'=>$tipoCuenta));
                
                $objContratoFormaPago->setContratoId($objContrato);
                
                if($objBcoTipoCuenta)
                {
                    $objContratoFormaPago->setBancoTipoCuentaId($objBcoTipoCuenta);
                }
                
                $titularCuenta = $razonSocial;
                
                if($razonSocial == null || $razonSocial != '')
                {
                    $titularCuenta = $nombres.' '.$apellidos;
                }
                
                $objContratoFormaPago->setTitularCuenta($titularCuenta);
                $objContratoFormaPago->setEstado("Activo");
                $objContratoFormaPago->setFeCreacion(new \DateTime('now'));
                $objContratoFormaPago->setUsrCreacion($peticion->getSession()->get('user'));
                $objContratoFormaPago->setIpCreacion($peticion->getClientIp());
                
                /* @var $serviceCrypt \telconet\seguridadBundle\Service\CryptService */
                $serviceCrypt = $this->get('seguridad.Crypt');
                $strNumeroCtaTarjeta = $serviceCrypt->encriptar($numeroCuenta);
                  
                $objContratoFormaPago->setNumeroCtaTarjeta($strNumeroCtaTarjeta);
                
                if($objTipoCuenta)
                {
                    $objContratoFormaPago->setTipoCuentaId($objTipoCuenta);
                }
                
                $emComercial->persist($objContratoFormaPago);
                $emComercial->flush();
            }                                           
                         
            $objPersona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($idPersona);
            
            //Si el contacto del nodo fue cambiado
            if($cambioContacto=='S')
            {                
                $objEmpresaRol = $emComercial->getRepository('schemaBundle:InfoEmpresaRol')->findPorIdRolPorEmpresa($tipoContacto,$empresaCod);
                
                //Referencia de Contacto
                if($yaExiste == 'S')//El contacto existe dentro de la base
                {                                                  
                    if($tipoTributario == 'JUR')
                    {                  
                        if($razonSocial && $razonSocial != '')
                        {
                            $objPersona->setRazonSocial($razonSocial);
                            $emComercial->persist($objPersona);                     
                        }
                    }
                    else
                    {                        
                        $objPersona->setNombres($nombres);
                        $objPersona->setApellidos($apellidos);
                        $emComercial->persist($objPersona);
                    }
                    
                    if($yaExisteRol != 'S')//El contacto existente existe dentro de la base y tiene rol Contacto Nodo
                    {
                        //Solo se guarda la referencia en la info_nodo_contacto                          
                        $objPersonaEmpresaRol = new InfoPersonaEmpresaRol();
                        $objPersonaEmpresaRol->setPersonaId($objPersona);
                        $objPersonaEmpresaRol->setEmpresaRolId($objEmpresaRol);
                        $objPersonaEmpresaRol->setEstado("Activo");
                        $objPersonaEmpresaRol->setUsrCreacion($peticion->getSession()->get('user'));
                        $objPersonaEmpresaRol->setFeCreacion(new \DateTime('now'));
                        $objPersonaEmpresaRol->setIpCreacion($peticion->getClientIp());
                        $emComercial->persist($objPersonaEmpresaRol);
                    }                      
                    
                    //Actualizar informacion de contactos en caso de ser necesario
                    
                    /* @var $servicePersonaFormaContacto \telconet\comercialBundle\Service\InfoPersonaFormaContacto */
                    $servicePersonaFormaContacto = $this->get('comercial.InfoPersonaFormaContacto');

                    //Editar Formas de Contacto en caso de ser necesario
                    $arrayParametros = array(
                                            'idPersona'          => $objPersona->getId(),
                                            'jsonFormasContacto' => $peticion->get('formasContacto'),
                                            'usrCreacion'        => $peticion->getSession()->get('user'),
                                            'ipCreacion'         => $peticion->getClientIp()
                                        );

                    //Actualizar las formas de contacto de la persona
                    $servicePersonaFormaContacto->agregarActualizarEliminarFormasContacto($arrayParametros);
                }
                else //se crea una nueva persona con los roles, formas de contacto y referencia en la info_nodo_contacto
                {
                    $objTitulo = $emComercial->find('schemaBundle:AdmiTitulo', $tipoTitulo);
                    $objPersona = new InfoPersona();
                    $objPersona->setIdentificacionCliente($identificacion);
                    $objPersona->setOrigenProspecto("N");
                    $objPersona->setNombres($nombres);
                    $objPersona->setApellidos($apellidos);
                    $objPersona->setGenero($tipoGenero);
                    $objPersona->setNacionalidad($tipoNacionalidad);
                    $objPersona->setTipoIdentificacion($tipoIdentificacion);
                    $objPersona->setTipoTributario($tipoTributario);
                    $objPersona->setEstado("Activo");
                    $objPersona->setRazonSocial($razonSocial);
                    $objPersona->setTituloId($objTitulo);
                    $objPersona->setUsrCreacion($peticion->getSession()->get('user'));
                    $objPersona->setFeCreacion(new \DateTime('now'));
                    $objPersona->setIpCreacion($peticion->getClientIp());
                    $emComercial->persist($objPersona);                    

                    $personaEmpresaRol = new InfoPersonaEmpresaRol();
                    $personaEmpresaRol->setPersonaId($objPersona);
                    $personaEmpresaRol->setEmpresaRolId($objEmpresaRol);
                    $personaEmpresaRol->setEstado("Activo");
                    $personaEmpresaRol->setUsrCreacion($peticion->getSession()->get('user'));
                    $personaEmpresaRol->setFeCreacion(new \DateTime('now'));
                    $personaEmpresaRol->setIpCreacion($peticion->getClientIp());
                    $emComercial->persist($personaEmpresaRol);                    

                    //Se guardan las formas de Contacto
                    for($i = 0; $i < $arrayFormasContacto->total; $i++)
                    {
                        $objPersonaFormaContacto = new InfoPersonaFormaContacto();
                        $objFormaContacto = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                            ->findOneBy(array('descripcionFormaContacto' => $arrayFormasContacto->data[$i]->formaContacto));

                        $objPersonaFormaContacto->setPersonaId($objPersona);
                        $objPersonaFormaContacto->setFormaContactoId($objFormaContacto);
                        $objPersonaFormaContacto->setValor($arrayFormasContacto->data[$i]->valor);
                        $objPersonaFormaContacto->setEstado("Activo");
                        $objPersonaFormaContacto->setUsrCreacion($peticion->getSession()->get('user'));
                        $objPersonaFormaContacto->setFeCreacion(new \DateTime('now'));
                        $objPersonaFormaContacto->setIpCreacion($peticion->getClientIp());
                        $emComercial->persist($objPersonaFormaContacto);                        
                    }
                }              
            }            
            
            $objInfoElemento = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($idElemento);
            
            $objContactoNodo = $emInfraestructura->getRepository("schemaBundle:InfoContactoNodo")->findOneByNodoId($objInfoElemento);
            
            if($objContactoNodo && $cambioContacto=='S')
            {
                $objContactoNodo->setPersonaId($objPersona->getId());
                $emInfraestructura->persist($objContactoNodo);
                $emInfraestructura->flush();
            }                        
            
            $emComercial->flush();
            
            $emComercial->getConnection()->commit();
            
            $resultado = '{"success":true,"respuesta":"Autorizacion Legal de Nuevo Nodo - Se Genera Contrato por <b>RENOVACION</b>"}';
                                  
        }
        catch(\Exception $e)
        {
            if($emComercial->getConnection()->isTransactionActive())
            {                
                $emComercial->getConnection()->rollback();
            }
            
            error_log($e->getMessage());
            
            $resultado = '{"success":false,"respuesta":"Ha ocurrido un error, por favor reporte a Sistemas"}';
        }
        
        $respuesta->setContent($resultado);
        return $respuesta;
    }
    
    /**
    * ajaxGetInfoRepresentanteLegalAction
    *
    * Método que obtiene el json de las personas que tengan el tipo rol enviado como parametro
    *      
    * @return json
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * 
    * @version 1.0 17-03-2015       
    */
    public function ajaxGetInfoRepresentanteLegalAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
                                     
        $tipoRol        = $this->get('request')->get('tipoRol');                
        
        $objJson = $this->getDoctrine()
                        ->getManager("telconet")
                        ->getRepository('schemaBundle:InfoPersona')
                        ->generarJsonPersonaXTipoRol('','','',$tipoRol);
        
        $respuesta->setContent($objJson);
                
        return $respuesta;
    }
    
     /**
    * ajaxVerContratoNodo
    *
    * Método que obtiene los contratos generados por cada solicitud de nodo
    *      
    * @return json
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * 
    * @version 1.0 23-03-2015     
    * 
    * @Secure(roles="ROLE_280-2361")        
    */
    public function ajaxVerContratoNodoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
                                     
        $idSolicitud        = $this->get('request')->get('idSolicitud');  
        
        $emComercial  = $this->getDoctrine()->getManager('telconet');
               
        $objSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                    ->getSolicitudCaractPorTipoCaracteristica($idSolicitud,'CONTRATO');
        
        if($objSolCaract)
        {
            foreach ($objSolCaract as $caract)
            {
                $objContrato  = $emComercial->getRepository("schemaBundle:InfoContrato")->find($caract->getValor());
                
                $objFormaPago = $emComercial->getRepository("schemaBundle:AdmiFormaPago")->find($objContrato->getFormaPagoId()->getId());
                                
                $arr_encontrados[]=array( 
                                          'idContrato'      => $objContrato->getId(),
                                          'numeroContrato'  => $objContrato->getNumeroContrato(),
                                          'valor'           => $objContrato->getValorContrato(),
                                          'garantia'        => $objContrato->getValorGarantia(),
                                          'estado'          => $objContrato->getEstado(),
                                          'formaPago'       => $objFormaPago->getDescripcionFormaPago(),
                                          'feFinContrato'   => date_format($objContrato->getFeFinContrato(), 'Y-m-d H:i:s')
                                         );
            }

            $num = count($arr_encontrados);
            $data=json_encode($arr_encontrados);
            $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';                        
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';   
        }
        
        $respuesta->setContent($resultado);
                
        return $respuesta;
    }
    
    /**
     * 
     * Método encargado para obtener un resumen de la información del Nodo Creado desde la pantalla de solicitudes de nodo
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 03-08-2016
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxGetResumenNodoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $session  = $peticion->getSession();   
        
        $idElemento = $peticion->get('idElemento');
        
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");        
        
        $arrayParams = array();                

        $arrayParams['id']        = $idElemento;
        $arrayParams['empresa']   = $session->get('idEmpresa');                        
        $arrayParams['region']    = "";                      
        $arrayParams['provincia'] = "";                      
        $arrayParams['canton']    = "";                      
        $arrayParams['parroquia'] = "";                      
        $arrayParams['estado']    = "";                      
        $arrayParams['nombre']    = "";   
            
        $queryResult = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getElementoNodo($arrayParams,'data');                        

        $arrayResultado = $queryResult->getArrayResult();

        $resultado = $arrayResultado[0];       

        $alturaTorre = 'N/A';

        //Busca la altura de la torre en que caso que exista la descripcion cuando el Nodo sea una Torre
        if($resultado['torre']!='N/A' && $resultado['torre']!='NO')
        {
            $objDetalleElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                    ->findOneBy(array('elementoId'=>$idElemento,'detalleNombre'=>'TORRE'));

            if($objDetalleElemento)
            {
                $alturaTorre = $objDetalleElemento->getDetalleDescripcion();
            }
        }     

        $resultado['tieneRenovacion'] = '';
        $resultado['alturaTorre']     = $alturaTorre;

        //Verificar si existe renovacion de contrato

        if($resultado['estadoSolicitud'] != 'Finalizada')
        {
            if($resultado['cantFinalizadas']>0)
            {
                $resultado['tieneRenovacion'] = ' ( Nodo con Renovación de CONTRATO )';
            }
        }
        else //Si ya existe una solcitud Finalizado debe tener al menos 2 estados Finalizados en historial
        {
            if($resultado['cantFinalizadas']>1)
            {
                $resultado['tieneRenovacion'] = ' ( Nodo con Renovación de CONTRATO )';
            }
        }                

        $respuesta->setContent(json_encode($resultado));
                
        return $respuesta;       
    }
    
   /**
    * ajaxDescargarContratoPDFAction
    *
    * Método que lanza el PDF del contrato de Nodo Solicitado previo a la habilitacion del mismo
    *      
    * @return json
    *
    * @author Allan Suárez <arsuarez@telconet.ec>    
    * @version 1.0 19-03-2015     
    * 
    * @author Allan Suárez <arsuarez@telconet.ec>    
    * @version 1.1 19-02-2016 - Mostrar el mes en español en contrato    
    *                         - Mostrar la fecha de Inicio del Contrato y no la fecha de creacion 
    * 
    * @author Allan Suárez <arsuarez@telconet.ec>    
    * @version 1.2 17-08-2016 - Se modifica para que se envie el nombre de los contactos para que siempre la primera letra se mayuscula y el resto
    *                           con minuscula.                       
    * 
    * @Secure(roles="ROLE_280-2361")      
    */
    public function generarContratoPDFAction($id)
    {                        
        $arrayParametros = array();

        $mes = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
        
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');        
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        
        $objSolicitud = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")->find($id);        
        
        //se obtiene la informacion del contrato
        $objSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                    ->getSolicitudCaractPorTipoCaracteristica($id,'CONTRATO');
        
        $objContrato = $emComercial->getRepository("schemaBundle:InfoContrato")->find($objSolCaract[0]->getValor());                
        
        $fechaFinContrato = $objContrato->getFeFinContrato()->format('Y-m-d');        
        $fechaIniContrato = $objContrato->getFeIniContrato()->format('Y-m-d');                        
        
        $arrayFeFin = split("-", $fechaFinContrato);                
        $arrayFeIni = split("-", $fechaIniContrato);                
        
        $arrayParametros['tiempoContrato'] = $this->num2letras($arrayFeFin[0]-$arrayFeIni[0],false); 
        $arrayParametros['incremento']     = $objContrato->getIncrementoAnual();        
        $arrayParametros['anio']           = $this->num2letras($arrayFeIni[0]); 
        $arrayParametros['mes']            = utf8_encode(ucwords($mes[$arrayFeIni[1]-1]));        
        $arrayParametros['dia']            = $this->num2letras($arrayFeIni[2],false);          

        $arrayParametros['ciudad']         = $objContrato->getOficinaRepLegal();
        
        if($objContrato->getOficinaRepLegal() == "Guayaquil")
        {            
            $arrayParametros['direccion'] = "Urbanización Kennedy Norte Manzana 109 Solar 21, Av. Luís Orrantìa y Av. Víctor Hugo Sicouret.";
            $arrayParametros['matriz']    = "matriz";
        }
        else
        {            
            $arrayParametros['direccion'] = "Pedro Gosseal 148 y Mariano Echeverría.";
            $arrayParametros['matriz']    = "filial";
        }

        $array_valor = explode(".", $objContrato->getValorContrato());
        $entero  = $array_valor[0];
        $decimal = $array_valor[1];
        if($decimal != '')
        {
            if($decimal == '1' || $decimal == '2' || $decimal == '3' || $decimal == '4' || $decimal == '5' || $decimal == '6' || 
               $decimal == '7' || $decimal == '8' || $decimal == '9')
            {
                $decimal = $decimal * 10;
                $valor_decimal = ' con ' . $decimal . '/100';
            }
        }
        else
        {
            $valor_decimal = ' 00/100';
            $decimal = '00';
        }

        $arrayParametros['entero']           = $entero;
        $arrayParametros['valorEnteroLetra'] = $this->num2letras($entero);
        $arrayParametros['decimal']          = $decimal;
        $arrayParametros['valorDecimal']     = $valor_decimal;               
        $arrayParametros['numeroContrato']   = $objContrato->getNumeroContrato();
        
        $objPersona = $emComercial->getRepository("schemaBundle:InfoPersona")->findOneByLogin($objContrato->getUsrRepLegal());
        
        $repLegal = 'N/A';
        $titulo   = 'Sr/a.';

        if($objPersona)
        {
            $repLegal = sprintf($objPersona);
            
            $objTitulo = $emComercial->getRepository("schemaBundle:AdmiTitulo")->find($objPersona->getTituloId());
            
            if($objTitulo)
            {
                $titulo = $objTitulo->getCodigoTitulo();
            }
        }
        
        $arrayParametros['repLegal']       = ucwords(strtolower($titulo.' '.$repLegal));
        $arrayParametros['cedulaRepLegal'] = $objPersona->getIdentificacionCliente();
        
        if(strpos(strtoupper($repLegal), "KROCHIN")!== FALSE)
        {
            $arrayParametros['boolRepLegal'] = true;
        }
        else
        {
            $arrayParametros['boolRepLegal'] = false;
        }
        
        //Se obtiene el id del nodo
        $idNodo = $objSolicitud->getElementoId();
        
        $arrayParams['id'] =$idNodo;       
        
        $queryResult = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getElementoNodo($arrayParams,'data');
            
        $resultado = $queryResult->getArrayResult()[0]; //Informacion del nodo
        
        $contactoNodo = $emInfraestructura->getRepository("schemaBundle:InfoContactoNodo")->getContactoPrincipalNodo($idNodo);
        
        $arrayEspacioFisico = $emInfraestructura->getRepository("schemaBundle:InfoEspacioFisico")->findByNodoId($idNodo);
        
        $espacioContrato = '';
        if($arrayEspacioFisico && count($arrayEspacioFisico)>0)
        {
            foreach($arrayEspacioFisico as $espacio):
                $objTipoEspacioFisico = $emInfraestructura->getRepository("schemaBundle:AdmiTipoEspacio")->find($espacio->getTipoEspacioFisicoId());
                
                $espacioContrato.=$objTipoEspacioFisico->getNombreTipoEspacio().' ,';
            endforeach;
        }
        
        $arrayParametros['espacioFisico']    = substr($espacioContrato,0,strlen($espacioContrato)-1);              
        
        $boolTieneTorre = false;
        $alturaMaxTorre = '';
        
        if($resultado['torre'] == 'SI')
        {
            $boolTieneTorre = true;
            
            $objDetalleElemento = $emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                    ->findOneBy(array('elementoId'=>$idNodo,'detalleNombre'=>'TORRE'));
            if($objDetalleElemento)
            {
                $alturaMaxTorre = $objDetalleElemento->getDetalleDescripcion();
            }
        }
        
        $arrayParametros['tieneTorre'] = $boolTieneTorre;
        $arrayParametros['alturaMax']  = $alturaMaxTorre;
        
        $razonSocial = 'N/A';
        $cedContactoNodo = 'N/A';
        $contactos   = '';
        $nombresContacto = '';

        if($contactoNodo && count($contactoNodo)>0)
        {
            $razonSocial = sprintf($contactoNodo[0]);                        
            
            if($contactoNodo[0]->getNombres() && $contactoNodo[0]->getApellidos())
            {
                $nombresContacto  = $contactoNodo[0]->getNombres().' '.$contactoNodo[0]->getApellidos();
            }
            else
            {
                $nombresContacto  = $razonSocial;
            }
            
            $cedContactoNodo = $contactoNodo[0]->getIdentificacionCliente();
            
            //Formas de contacto del contacto legal del nodo
            $arrayFormaContacto = $emComercial->getRepository("schemaBundle:InfoPersonaFormaContacto")->findByPersonaId($contactoNodo[0]->getId());
            
            foreach($arrayFormaContacto as $formaContacto):
                if($formaContacto->getFormaContactoId()==1 || $formaContacto->getFormaContactoId()==25 ||
                   $formaContacto->getFormaContactoId()==26 || $formaContacto->getFormaContactoId()==27 )
                {
                    $contactos = $formaContacto->getValor().", ";
                }
            endforeach;
            
            if($contactoNodo[0]->getGenero()=='M')
            {
                $arrayParametros['articuloInicio'] = 'El';
                $arrayParametros['articuloFin'] = '';
                $arrayParametros['articuloAdicional'] = 'o';
            }
            else
            {
                $arrayParametros['articuloInicio'] = 'La';
                $arrayParametros['articuloFin'] = 'a';
                $arrayParametros['articuloAdicional'] = 'a';
            }
            
            if($contactoNodo[0]->getTipoTributario()=='NAT')
            {
                $arrayParametros['tipoPersona']             = 'NAT';                
                $arrayParametros['infoContactoTres']        = 'de una casa ';
                $arrayParametros['infoContactoCuatro']      = 'terraza';
                $arrayParametros['infoContactoArticulo']    = 'la';
                $arrayParametros['infoContactoComplemento'] = 'a';
                $arrayParametros['infoContactoRazones']     = '';
                $arrayParametros['infoContactoFin']         = '';
                $arrayParametros['infoContactoIva']         = '';
            }
            else
            {
                $arrayParametros['tipoPersona']             = 'TRI';                
                $arrayParametros['infoContactoTres']     = 'de un edificio, predio, centro comercial etc.,';
                $arrayParametros['infoContactoCuatro']   = 'espacio físico';
                $arrayParametros['infoContactoArticulo'] = 'el';
                $arrayParametros['infoContactoComplemento'] = 'o';
                $arrayParametros['infoContactoRazones']  = "p.".$razonSocial;
                $arrayParametros['infoContactoFin']      = 'a'; 
                $arrayParametros['infoContactoIva']      = ' mas iva';                                
            }
        }
               
        $arrayParametros['contactoNodo']    = ucwords(strtolower($razonSocial)); 
        $arrayParametros['cedContactoNodo'] = $cedContactoNodo; 
        $arrayParametros['contactos']       = substr($contactos,0,strlen($contactos)-1);
        $arrayParametros['nombresContacto'] = ucwords(strtolower($nombresContacto));
        
        $garantia = '';
        
        $valorGarantia = $objContrato->getValorGarantia();
        
        if($valorGarantia>0)
        {
            $array_garantia = explode(".", $valorGarantia);
            $entero_garantia = $array_garantia[0];
            $decimal_garantia = $array_garantia[1];
            if($decimal_garantia != '')
            {
                if($decimal_garantia == '1' || $decimal_garantia == '2' || $decimal_garantia == '3' || $decimal_garantia == '4' || 
                   $decimal_garantia == '5' || $decimal_garantia == '6' || $decimal_garantia == '7' || $decimal_garantia == '8' || 
                   $decimal_garantia == '9')
                {
                    $decimal_garantia = $decimal_garantia * 10;
                }
            }
            else
            {
                $decimal_garantia = '00';
            }

            $garantia = " " .$arrayParametros['articuloInicio']." arrendador".$arrayParametros['articuloFin']." declara que recibe de parte de la "
                . "arrendataria al momento de celebrar el presente contrato,  un depósito de $ ".$entero_garantia.".".$decimal_garantia." para "
                . "garantizar el contrato firmado.";
        }
        
        $arrayParametros['garantia']    = $garantia;                    
        
        $html = $this->renderView('tecnicoBundle:InfoSolicitudNuevoNodo:ContratoSolicitudNuevoNodo.html.twig', 
                                   array('informacionNodo' => $resultado,'informacionContrato'=>$arrayParametros));                                        
        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html), 200, array(
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename=Contrato_Nodo_' . str_replace(' ','_',$resultado['nombreElemento']). '.pdf'            
            )
        );
    }
    
    /**
    * ajaxSubirContratoAction
    
    * Método que se encarga de subir el documento del contrato relacionado a la solicitud del nodo
    *      
    * @return json
    *
    * @author Allan Suárez <arsuarez@telconet.ec>    
    * @version 1.0 19-03-2015     
    * 
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.1 21-03-2016 - Se modifica para que lea las rutas definidad para los nodos
    * 
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.2 01-08-2016 - Se modifica para que cuando el nodo tenga renovacion de contrato este no contemple la habilitación del nodo por
    *                           segunda vez
    * 
    * @Secure(roles="ROLE_280-2361")        
    */
    public function ajaxSubirContratoAction()
    {
        $request = $this->getRequest();
        $peticion = $this->get('request');
        
        $empresaCod = $peticion->getSession()->get('idEmpresa');
        
        $idSolicitud     = $peticion->get('idSolicitud');
        $tieneRenovacion = $peticion->get('tieneRenovacion');
        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/html');
        
        $em           = $this->getDoctrine()->getManager('telconet_comunicacion');        
        $emComercial  = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $objSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($idSolicitud);
        
        $objInfoElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objSolicitud->getElementoId());
        
        $objInfoDetalleElemento = $emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                                    ->findOneBy(array('elementoId'   => $objInfoElemento->getId(),
                                                                                      'detalleNombre'=>'IMAGEN NODO')
                                                                                ); 
        
        $objSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                    ->getSolicitudCaractPorTipoCaracteristica($idSolicitud,'CONTRATO');        
        
        $em->getConnection()->beginTransaction(); 
        $emComercial->getConnection()->beginTransaction(); 
        
        $ubicacionFisica = '';

        $boolUploadOk = false;
        
        try
        { 
            if($objSolicitud && $objSolCaract && count($objSolCaract)>0)
            {
                $objContrato = $emComercial->getRepository("schemaBundle:InfoContrato")->find($objSolCaract[0]->getValor());                
                
                $fileRoot = $this->container->getParameter('ruta_upload_documentos');
                
                $path = $this->container->getParameter('path_telcos');               

                $file = $request->files;

                $objArchivo = $file->get('archivo');

                if($file && count($file) > 0)
                {
                    if(isset($objArchivo))
                    {
                        if($objArchivo && count($objArchivo) > 0)
                        {                        	                            
                            $archivo = $objArchivo->getClientOriginalName();                        

                            $arrayArchivo = explode('.', $archivo);
                            $countArray = count($arrayArchivo);
                            $nombreArchivo = $arrayArchivo[0];
                            $extArchivo = $arrayArchivo[$countArray - 1];

                            $prefijo = substr(md5(uniqid(rand())), 0, 6);

                            if($archivo != "")
                            {
                                $nuevoNombre = $nombreArchivo . "_" . $prefijo . "." . $extArchivo;

                                $nuevoNombre = str_replace(" ", "_", $nuevoNombre);     
                                
                                $idNodoRef   = $objInfoElemento->getId();                            
                            
                                //Si es un nodo migrado usamos la misma ruta de imagenes predefinidas
                                if($objInfoDetalleElemento)
                                {
                                    $idNodoRef = $objInfoDetalleElemento->getDetalleValor();                                
                                }                                

                                $filePath = $fileRoot."nodos/nodo_".$idNodoRef."/contrato/";
                                $destino  = $path.$filePath;                                                            

                                $ubicacionFisica = $destino.$nuevoNombre;
                                                                
                                if($objArchivo->move($destino, $nuevoNombre))
                                {
                                    //Guardar imagen
                                    $objTipoDocumento = $em->getRepository('schemaBundle:AdmiTipoDocumento')->find(13);                                                                

                                    $objInfoDocumento = new InfoDocumento();                                    
                                    $objInfoDocumento->setNombreDocumento("Contrato Nodo : ".$objInfoElemento->getNombreElemento()); 
                                    $objInfoDocumento->setUbicacionLogicaDocumento($nuevoNombre);
                                    $objInfoDocumento->setUbicacionFisicaDocumento($this->UPLOAD_PATH."nodos/nodo_".
                                                                                   $idNodoRef."/contrato/".$nuevoNombre); 
                                    $objInfoDocumento->setFechaDocumento(new \DateTime('now'));                                                                 
                                    $objInfoDocumento->setUsrCreacion($request->getSession()->get('user'));
                                    $objInfoDocumento->setFeCreacion(new \DateTime('now'));
                                    $objInfoDocumento->setIpCreacion($request->getClientIp());
                                    $objInfoDocumento->setEstado('Activo');                                                                                   
                                    $objInfoDocumento->setEmpresaCod($empresaCod);                        
                                    $objInfoDocumento->setTipoDocumentoGeneralId(9);//id de tipo contrato
                                    $objInfoDocumento->setTipoDocumentoId($objTipoDocumento);    
                                    $em->persist($objInfoDocumento);      
                                    $em->flush();

                                    $objInfoDocumentoRelacion = new InfoDocumentoRelacion(); 
                                    $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());                    
                                    $objInfoDocumentoRelacion->setModulo('COMERCIAL');                                                           
                                    $objInfoDocumentoRelacion->setContratoId($objContrato->getId());   
                                    $objInfoDocumentoRelacion->setElementoId($objSolicitud->getElementoId());
                                    $objInfoDocumentoRelacion->setEstado('Activo');                                                                                   
                                    $objInfoDocumentoRelacion->setFeCreacion(new \DateTime('now'));                        
                                    $objInfoDocumentoRelacion->setUsrCreacion($request->getSession()->get('user'));
                                    $em->persist($objInfoDocumentoRelacion);      
                                    $em->flush();                                                                                                            
                                                
                                    $objSolicitud->setEstado('FirmadoContrato');
                                    $objSolicitud->setObservacion("Se firma contrato #".$objContrato->getNumeroContrato());
                                    $emComercial->persist($objSolicitud);      

                                    $objSolicitudHistorial = new InfoDetalleSolHist();
                                    $objSolicitudHistorial->setDetalleSolicitudId($objSolicitud);                                
                                    $objSolicitudHistorial->setMotivoId($objSolicitud->getMotivoId());
                                    $objSolicitudHistorial->setEstado("FirmadoContrato");
                                    $objSolicitudHistorial->setObservacion("Se firma contrato #".$objContrato->getNumeroContrato());    
                                    $objSolicitudHistorial->setUsrCreacion($peticion->getSession()->get('user'));
                                    $objSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                                    $emComercial->persist($objSolicitudHistorial);
                                    
                                    if($tieneRenovacion == 'S')
                                    {
                                        $objSolicitud->setEstado('Finalizada');
                                        $objSolicitud->setObservacion("Nodo Habilitado por Subida de Contrato ( Renovado )");
                                        $emComercial->persist($objSolicitud);      
                                        
                                        $objSolicitudHistorial = new InfoDetalleSolHist();
                                        $objSolicitudHistorial->setDetalleSolicitudId($objSolicitud);                                
                                        $objSolicitudHistorial->setMotivoId($objSolicitud->getMotivoId());
                                        $objSolicitudHistorial->setEstado("Finalizada");
                                        $objSolicitudHistorial->setObservacion("Nodo Habilitado por Subida de Contrato ( Renovado )");    
                                        $objSolicitudHistorial->setUsrCreacion($peticion->getSession()->get('user'));
                                        $objSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                                        $emComercial->persist($objSolicitudHistorial);
                                    }
                                    
                                    $objContrato->setEstado('Activo');
                                    $objContrato->setFeAprobacion(new \DateTime('now'));
                                    $objContrato->setUsrAprobacion($peticion->getSession()->get('user'));
                                    $emComercial->persist($objContrato);

                                    $emComercial->flush();
                                                                        
                                    $em->getConnection()->commit(); 
                                    $emComercial->getConnection()->commit(); 

                                    $boolUploadOk = true;                                                               
                                }                                                                                 
                            }                        
                        }//FIN IF ARCHIVO SUBIDO                    
                    }//FIN IF ARCHIVO                
                }//FIN IF FILES            

                if($boolUploadOk)
                {
                    $resultado = '{"success":true,"respuesta":"Contrato Subido Correctamente"}';                                                                                              
                }
                else
                {
                    $resultado = '{"success":false,"respuesta":"Ha ocurrido un error, por favor reporte a Sistemas"}';
                }                           
            }
            else
            {
                $resultado = '{"success":false,"respuesta":"No existe contrato creado para relacionar"}';
            }
            
            $respuesta->setContent($resultado);
            return $respuesta;
        }
        catch(\Exception $e)
        {            
            if($em->getConnection()->isTransactionActive())
            {                
                $em->getConnection()->rollback();
            }
            
            if($emComercial->getConnection()->isTransactionActive())
            {                
                $emComercial->getConnection()->rollback();
            }
            
            unlink($ubicacionFisica);
            
            $resultado = '{"success":false,"respuesta":"' . $e->getMessage() . '"}';
            $respuesta->setContent($resultado);
            return $respuesta;
        }
    }
    
    /**
      * ajaxDescargarContratoFinalAction
      *
      * Método que descarga el contrato generado y firmado
      * 
      * @param id
      *                                                    
      * @return numero en letras
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 19-03-2015
      * 
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.1 21-03-2016 - Se modifica para que lea las rutas definidad para los nodos
      * 
      * @Secure(roles="ROLE_280-2361")      
      */
    public function ajaxDescargarContratoFinalAction($id)
    {
        $pathTelcos = $this->container->getParameter('path_telcos');                        

        $em = $this->getDoctrine()->getManager("telconet_comunicacion");
        $emComercial  = $this->getDoctrine()->getManager('telconet');                
        
        $objSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                    ->getSolicitudCaractPorTipoCaracteristica($id,'CONTRATO');     
        
        $idContratoActivo = '';
        
        foreach($objSolCaract as $caract)
        {            
            $objContrato = $emComercial->getRepository('schemaBundle:InfoContrato')->find($caract->getValor());
            
            if($objContrato->getEstado()=='Activo')
            {
                $idContratoActivo = $objContrato->getId();
            }
        }

        $objDocumentoRelacion = $em->getRepository('schemaBundle:InfoDocumentoRelacion')
                                    ->findOneBy(array('contratoId'=>$idContratoActivo));                
        
        $documento = $em->getRepository('schemaBundle:InfoDocumento')->find($objDocumentoRelacion->getDocumentoId());       
        
        //Todos los contratos son subidos/migrados directamente a la ruta .../.../public/uploads/... para efectos que puedan
        //ser vistos en visor de documentos y visor de imagenes en modulo de comunicacion
        $path = $pathTelcos . "telcos/web/" . $documento->getUbicacionFisicaDocumento();        

        $content = file_get_contents($path);

        $response = new Response();

        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $documento->getUbicacionLogicaDocumento());

        $response->setContent($content);
        return $response;
    }
        
     /**
      * num2letras
      *
      * Método que devuelve el numero en letras
      *                                                    
      * @return numero en letras
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 19-03-2015
      */   
    public static function num2letras($num, $fem = true, $dec = true) 
    {            
       $matuni[2]  = "dos"; 
       $matuni[3]  = "tres"; 
       $matuni[4]  = "cuatro"; 
       $matuni[5]  = "cinco"; 
       $matuni[6]  = "seis"; 
       $matuni[7]  = "siete"; 
       $matuni[8]  = "ocho"; 
       $matuni[9]  = "nueve"; 
       $matuni[10] = "diez"; 
       $matuni[11] = "once"; 
       $matuni[12] = "doce"; 
       $matuni[13] = "trece"; 
       $matuni[14] = "catorce"; 
       $matuni[15] = "quince"; 
       $matuni[16] = "dieciseis"; 
       $matuni[17] = "diecisiete"; 
       $matuni[18] = "dieciocho"; 
       $matuni[19] = "diecinueve"; 
       $matuni[20] = "veinte"; 
       $matunisub[2] = "dos"; 
       $matunisub[3] = "tres"; 
       $matunisub[4] = "cuatro"; 
       $matunisub[5] = "quin"; 
       $matunisub[6] = "seis"; 
       $matunisub[7] = "sete"; 
       $matunisub[8] = "ocho"; 
       $matunisub[9] = "nove"; 

       $matdec[2] = "veint"; 
       $matdec[3] = "treinta"; 
       $matdec[4] = "cuarenta"; 
       $matdec[5] = "cincuenta"; 
       $matdec[6] = "sesenta"; 
       $matdec[7] = "setenta"; 
       $matdec[8] = "ochenta"; 
       $matdec[9] = "noventa"; 
       $matsub[3]  = 'mill'; 
       $matsub[5]  = 'bill'; 
       $matsub[7]  = 'mill'; 
       $matsub[9]  = 'trill'; 
       $matsub[11] = 'mill'; 
       $matsub[13] = 'bill'; 
       $matsub[15] = 'mill'; 
       $matmil[4]  = 'millones'; 
       $matmil[6]  = 'billones'; 
       $matmil[7]  = 'de billones'; 
       $matmil[8]  = 'millones de billones'; 
       $matmil[10] = 'trillones'; 
       $matmil[11] = 'de trillones'; 
       $matmil[12] = 'millones de trillones'; 
       $matmil[13] = 'de trillones'; 
       $matmil[14] = 'billones de trillones'; 
       $matmil[15] = 'de billones de trillones'; 
       $matmil[16] = 'millones de billones de trillones'; 

       $num = trim((string)@$num); 
       if ($num[0] == '-') { 
          $neg = 'menos '; 
          $num = substr($num, 1); 
       }
       else 
          $neg = ''; 
       while ($num[0] == '0') $num = substr($num, 1); 
       if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num; 
       $zeros = true; 
       $punt = false; 
       $ent = ''; 
       $fra = ''; 
       for ($c = 0; $c < strlen($num); $c++) 
       { 
          $n = $num[$c]; 
          if (! (strpos(".,'''", $n) === false)) 
          { 
             if ($punt) break; 
             else{ 
                $punt = true; 
                continue; 
             } 

          }
          elseif (! (strpos('0123456789', $n) === false)) 
          { 
             if ($punt) 
             { 
                if ($n != '0') $zeros = false; 
                $fra .= $n; 
             }
             else 

                $ent .= $n; 
          }
          else 

             break; 

       } 
       $ent = '     ' . $ent; 
       if ($dec and $fra and ! $zeros) 
       { 
          $fin = ' coma'; //' coma'
          for ($n = 0; $n < strlen($fra); $n++) 
          { 
             if (($s = $fra[$n]) == '0') 
                $fin .= ' cero'; 
             elseif ($s == '1') 
                $fin .= $fem ? ' una' : ' un'; 
             else 
                $fin .= ' ' . $matuni[$s]; 
          } 
       }
       else 
          $fin = ''; 
       if ((int)$ent === 0) return 'Cero ' . $fin; 
       $tex = ''; 
       $sub = 0; 
       $mils = 0; 
       $neutro = false; 
       while ( ($num = substr($ent, -3)) != '   ') 
       { 
          $ent = substr($ent, 0, -3); 
          if (++$sub < 3 and $fem) 
          { 
             $matuni[1] = 'una'; 
             $subcent = 'os'; ///////as
          }
          else
          { 
             $matuni[1] = $neutro ? 'un' : 'uno'; 
             $subcent = 'os'; 
          } 
          $t = ''; 
          $n2 = substr($num, 1); 
          if ($n2 == '00') 
          { 
          }
          elseif ($n2 < 21) 
             $t = ' ' . $matuni[(int)$n2]; 
          elseif ($n2 < 30) 
          { 
             $n3 = $num[2]; 
             if ($n3 != 0) $t = 'i' . $matuni[$n3]; 
             $n2 = $num[1]; 
             $t = ' ' . $matdec[$n2] . $t; 
          }
          else
          { 
             $n3 = $num[2]; 
             if ($n3 != 0) $t = ' y ' . $matuni[$n3]; 
             $n2 = $num[1]; 
             $t = ' ' . $matdec[$n2] . $t; 
          } 
          $n = $num[0]; 
          if ($n == 1) 
          { 
             $t = ' ciento' . $t; 
          }
          elseif ($n == 5)
          { 
             $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t; 
          }
          elseif ($n != 0)
          { 
             $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t; 
          } 
          if ($sub == 1) 
          { 
          }
          elseif (! isset($matsub[$sub])) 
          { 
             if ($num == 1) 
             { 
                $t = ' mil'; 
             }
             elseif ($num > 1)
             { 
                $t .= ' mil'; 
             } 
          }
          elseif ($num == 1) 
          { 
             $t .= ' ' . $matsub[$sub] . '?n'; 
          }
          elseif ($num > 1)
          { 
             $t .= ' ' . $matsub[$sub] . 'ones'; 
          }   
          if ($num == '000') $mils ++; 
          elseif ($mils != 0) { 
             if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub]; 
             $mils = 0; 
          } 
          $neutro = true; 
          $tex = $t . $tex; 
       } 
       $tex = $neg . substr($tex, 1) . $fin; 
       return ucfirst($tex); 
    }
}
