<?php

namespace telconet\soporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Form\InfoDocumentoGestionType;
use telconet\schemaBundle\Form\AdmiTipoDocumentoType;

use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\AdmiTipoDocumento;
use telconet\schemaBundle\Entity\AdmiTipoDocumentoGeneral;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

use telconet\soporteBundle\Service\SoporteService;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\InfoServicioHistorial;

/**
  * Clase GestionDocumentosController
  *
  * Clase que se encarga del CRUD de la Gestion Documental en el Telcos
  * Subidas y descargas de diferentes tipos de archivos
  *
  * @author Allan Suárez C. <arsuarez@telconet.ec>
  * @version 1.0 10-07-2014
  */    

class GestionDocumentosController extends Controller implements TokenAuthenticatedController
{
     /**
     * indexAction
     *
     * Metodo encargado de dirigir a la pagina principal del CRUD y ver los documentos creados,
     * Recibe el modulo para determinar que index o busqueda mostrar en cada modulo
     *
     * @param string $modulo
     *
     * @return index
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 10-07-2014
     */
       
    public function indexAction($modulo)
    {    	
	$esVisible = false;
        if (true === $this->get('security.context')->isGranted('ROLE_251-1'))
        {
                $esVisible = true;
        }
        
        $request  = $this->get('request');                    

        $em = $this->getDoctrine()->getManager('telconet_comunicacion');   
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");            
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("84", "1");    	             		      
                    	                    	
    	return $this->render('soporteBundle:gestion_documentos:index.html.twig',			
			array(
			      'item'   => $entityItemMenu,
			      'modulo' => $modulo,
			      'esVisible'=>$esVisible
			));			    
    }        
    /**
     * newAction
     *
     * Metodo encargado de dirigir a la pagina que permite guardar nuevo documento         
     *
     * @param string $modulo
     * 
     * @return html con la twig de creacion
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 25-09-2015 - Se agrega el módulo de donde viene la consulta
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 10-07-2014
     */
    public function newAction($modulo)
    {        		      
        $formInfoDocumento = $this->createForm(new InfoDocumentoGestionType());        
        
        return $this->render('soporteBundle:gestion_documentos:new.html.twig', array(            
                                                                                        'formDocumento' => $formInfoDocumento->createView(),
                                                                                        'modulo'        => $modulo
                                                                                    )
                            );
    }
    
    /**
     * createAction
     *
     * Metodo encargado de guardar el documento en la tabla INFO_DOCUMENTO e INFO_DOCUMENTO_RELACION         
     *
     * @return twig que muestra el ingreso realizado
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 25-09-2015 - Se aumenta al retorno del método la variable '$strUrlShow' que indica la url de la vista
     *                           donde se muestra la información del documento creado, y adicional se toma del request la
     *                           variable adicional '$strModuloAdicional' que indica si viene del modulo de comunicaciones
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 10-07-2014
     */
    public function createAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');                
        
        $request = $this->get('request');        
        
        $em           = $this->get('doctrine')->getManager('telconet_comunicacion');    
        $emComercial  = $this->get('doctrine')->getManager('telconet');  
        $emFinanciero = $this->get('doctrine')->getManager('telconet_financiero');
        $emGeneral    = $this->get('doctrine')->getManager('telconet_general');
        $emSoporte    = $this->get('doctrine')->getManager('telconet_soporte');
        
        $nombreDocumento    = $request->get('nombreDocumento')?$request->get('nombreDocumento'):'';  
        $modulo             = $request->get('modulo')?$request->get('modulo'):'';  
        $tipoDocumento      = $request->get('tipoDocumento')?$request->get('tipoDocumento'):'';  
        $extensionDoc       = $request->get('extensionDoc')?$request->get('extensionDoc'):''; 	  
        $elemento           = $request->get('elemento')?$request->get('elemento'):'';  
        $login              = $request->get('login')?$request->get('login'):'';  
        $numeroDocumento    = $request->get('numeroDocumento')?$request->get('numeroDocumento'):'';  
        $tipoElemento       = $request->get('tipoElemento')?$request->get('tipoElemento'):'';  
        $modeloElemento     = $request->get('modeloElemento')?$request->get('modeloElemento'):'';  
        $hiddenFile         = $request->get('hiddenFile')?$request->get('hiddenFile'):'';  
        $nameFile           = $request->get('nameFile')?$request->get('nameFile'):'';  		
        $descripcion        = $request->get('descripcion')?$request->get('descripcion'):'';  
        $tareaCaso          = $request->get('tareaCaso')?$request->get('tareaCaso'):''; 
        $tipoTareaCaso      = $request->get('tipoTareaCaso')?$request->get('tipoTareaCaso'):''; 		
        $encuesta           = $request->get('encuesta')?$request->get('encuesta'):'';   
        $strModuloAdicional = $request->get('strModuloAdicional')?$request->get('strModuloAdicional'):'';  
	
	//Referencia a la Tabla INFO_DOCUMENTO
	$entity  = new InfoDocumento();    
	
	//Referencia a la Tabla INFO_DOCUMENTO_RELACION
	$entityRelacion = new InfoDocumentoRelacion();
	$entityRelacion->setModulo($modulo);  
	$entityRelacion->setEstado('Activo');  
	$entityRelacion->setFeCreacion(new \DateTime('now'));  
	$entityRelacion->setUsrCreacion($request->getSession()->get('user'));  
	
	if($extensionDoc!='')
	{
	      $tipoDocId = $em->getRepository('schemaBundle:AdmiTipoDocumento')->find($extensionDoc);
	      if($tipoDocId) $entity->setTipoDocumentoId($tipoDocId);  	
	}	
	
	if($numeroDocumento!='')
	{
	      if($modulo == 'COMERCIAL')
	      {
		    $contrato = $emComercial->getRepository('schemaBundle:InfoContrato')->findOneByNumeroContrato($numeroDocumento);
		    if($contrato)$entityRelacion->setContratoId($contrato->getId());
	      
	      }
	      
	      if($modulo == 'FINANCIERO')
	      {
		    $docFinan = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->findOneByNumeroFacturaSri($numeroDocumento);
		    if($docFinan)$entityRelacion->setDocumentoFinancieroId($docFinan->getId());
	      
	      }
	}
	
	if($modulo == 'SOPORTE')
	{
	
	      if($tipoTareaCaso != '')
	      {
		    if($tipoTareaCaso == 'C')
		    {
			  $caso = $emSoporte->getRepository('schemaBundle:InfoCaso')->findOneByNumeroCaso($tareaCaso);	      
			  if($caso) $entityRelacion->setCasoId($caso->getId());		    		    		    
		    }
		    else
		    {
			  $actividad = $em->getRepository('schemaBundle:InfoComunicacion')->find($tareaCaso);
			  if($actividad)$entityRelacion->setActividadId($actividad->getId());
		    
		    }	      	      
	      }		
	}		
	
	if($tipoElemento!='')
	{
	      $entityRelacion->setTipoElementoId($tipoElemento);
	}
	if($modeloElemento!='')
	{
	      $entityRelacion->setModeloElementoId($modeloElemento);
	}
	if($elemento!='')
	{
	      $entityRelacion->setElementoId($elemento);
	}
	if($login!='')
	{
	      $entityRelacion->setPuntoId($login);
	      $personaRol = $emComercial->getRepository('schemaBundle:InfoPunto')->find($login);
	      if($personaRol)$entityRelacion->setPersonaEmpresaRolId($personaRol->getPersonaEmpresaRolId()->getId());
	}
                              
        $entity->setTipoDocumentoGeneralId($tipoDocumento);    
        $entity->setNombreDocumento($nombreDocumento);
        $entity->setUbicacionFisicaDocumento(base64_decode($hiddenFile));
        $entity->setUbicacionLogicaDocumento($nameFile);
        $entity->setMensaje($descripcion);
        $entity->setEstado('Activo');
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setFechaDocumento(new \DateTime('now'));
        $entity->setIpCreacion('127.0.0.1');
        $entity->setUsrCreacion($request->getSession()->get('user'));                         
        $entity->setEmpresaCod($request->getSession()->get('idEmpresa'));               
                                
        try
        {        	     	      	   	      
            $em->getConnection()->beginTransaction();

            $em->persist($entity);
            $em->flush();	      

            $entityRelacion->setDocumentoId($entity->getId());	      	     	    
            $em->persist($entityRelacion);
            $em->flush();

            $em->getConnection()->commit();

            if( $strModuloAdicional == 'comunicaciones' )
            {
                $strUrlShow = $this->generateUrl('gestion_documentos_show', array('id' => $entity->getId(), 'modulo' => $strModuloAdicional));
            }
            else
            {
                $strUrlShow = $this->generateUrl('gestion_documentos_show', array('id' => $entity->getId(), 'modulo' => $modulo));
            }
            	
            $resultado = json_encode( array('success'=>true, 'id'=>$entity->getId(), 'strUrlShow' => $strUrlShow) );	             
        
        }catch(Exception $e)
        {
        
	      $em->getConnection()->rollback();
	      $em->getConnection()->close();			
	      $resultado = json_encode(array('success'=>false,'mensaje'=>$e));
        }                               
        
        $respuesta->setContent($resultado);	
        return $respuesta;        
    }    
    
     /**
     * showAction
     *
     * Metodo encargado de mostrar el Documento ingresado 
     *
     * @param integer $id
     * @param string  $modulo
     *
     * @return twig que muestra el ingreso realizado
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 10-07-2014
     */      
    public function showAction($id,$modulo)
    {        		
        $emComunicacion     = $this->getDoctrine()->getManager('telconet_comunicacion');       
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');  
        $emComercial        = $this->getDoctrine()->getManager('telconet');  
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');  
        $emFinanciero       = $this->getDoctrine()->getManager('telconet_financiero'); 
        $emSoporte          = $this->getDoctrine()->getManager('telconet_soporte'); 
        
        $documento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($id);        
        
	if (!$documento) 
	{
            throw $this->createNotFoundException('No se encuentra el documento.');
        }
        
        $documentoRelacion = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')->findOneByDocumentoId($id);
        
        $moduloDocumento = $documentoRelacion->getModulo();
        
        $tipoDocumentoGeneral = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
					  ->find($documento->getTipoDocumentoGeneralId());
	
	if($documentoRelacion->getPuntoId())
	{
	      $puntoCliente         = $emComercial->getRepository('schemaBundle:InfoPunto')
					  ->find($documentoRelacion->getPuntoId());
	}else $puntoCliente = null;
	
	$contrato = ''; $docFinan = '';
					  
        if($documentoRelacion->getContratoId())
        {
	      $contrato       = $emComercial->getRepository('schemaBundle:InfoContrato')
					  ->find($documentoRelacion->getContratoId());        
        }
        
        if($documentoRelacion->getDocumentoFinancieroId())
        {
	      $docFinan      = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
					  ->find($documentoRelacion->getDocumentoFinancieroId());        
        }
        
        if($documentoRelacion->getTipoElementoId())
        {
	      $tipoElemento  = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
					  ->find($documentoRelacion->getTipoElementoId());        
        }else $tipoElemento = null;
        
        if($documentoRelacion->getModeloElementoId())
        {
	      $modeloElemento = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
					  ->find($documentoRelacion->getModeloElementoId());        
        }else $modeloElemento = null;
        
        if($documentoRelacion->getElementoId())
        {
	      $elemento       = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
					  ->find($documentoRelacion->getElementoId());        
        }else $elemento = null; 
        
        $numeroTareaCaso = 'N/A';
        $tipoCasoTarea   = 'Ninguno';
        
        if($documentoRelacion->getCasoId())
        {
	      $caso            = $emSoporte->getRepository('schemaBundle:InfoCaso')
					  ->find($documentoRelacion->getCasoId());  
              if($caso)
              {			
		    $numeroTareaCaso = $caso->getNumeroCaso();					      
		    $tipoCasoTarea   = 'Caso';
	      }
        }
        
        if($documentoRelacion->getActividadId())
        {
	      $actividad       = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')
					  ->find($documentoRelacion->getActividadId()); 
              if($actividad)
              {
		    $numeroTareaCaso = $actividad->getId();					      
		    $tipoCasoTarea   = 'Tarea';
	      }
        }
        
        if($documentoRelacion->getEncuestaId())
        {
	      $encuesta       = $emComunicacion->getRepository('schemaBundle:InfoEncuesta')
					  ->find($documentoRelacion->getEncuestaId());              
        }else $encuesta=null;
        
           
        $parametros=array(            
            'documento'      => $documento,  
            'moduloDocumento'=> $moduloDocumento,
            'tipoDocGeneral' => $tipoDocumentoGeneral,
            'puntoCliente'   => $puntoCliente?$puntoCliente->getLogin():'N/A',
            'contrato'       => $contrato?$contrato:null,
            'docFinan'       => $docFinan?$docFinan:null,
            'tipoElemento'   => $tipoElemento?$tipoElemento->getNombreTipoElemento():'N/A',
            'modeloElemento' => $modeloElemento?$modeloElemento->getNombreModeloElemento():'N/A',
            'elemento'       => $elemento?$elemento->getNombreElemento():'N/A',
            'numeroTareaCaso'=> $numeroTareaCaso,
            'nombreEncuesta' => $encuesta?$encuesta->getNombreEncuesta():'N/A',
            'tipoTareaCaso'  => $tipoCasoTarea,
            'modulo'         => $modulo
        );               

        $parametros['grantedVerDocumentoPersonal'] = $this->get('security.context')->isGranted('ROLE_60-8057');
        $parametros['grantedDescargarDocumentoPersonal'] = $this->get('security.context')->isGranted('ROLE_60-8058');

	return $this->render('soporteBundle:gestion_documentos:show.html.twig', $parametros);
    }
    
       /**
     * editAction
     *
     * Metodo encargado de redirigir a la pagina de edicion del documento 
     *
     * @param integer $id     
     *
     * @return twig donde se pueda editar el documento ingresado
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 10-07-2014
     */    
    public function editAction($id)
    {
        $session = $this->get('request')->getSession();
		
        $em           = $this->get('doctrine')->getManager('telconet_comunicacion');    
        $emComercial  = $this->get('doctrine')->getManager('telconet');  
        $emFinanciero = $this->get('doctrine')->getManager('telconet_financiero');
        $emGeneral    = $this->get('doctrine')->getManager('telconet_general');
        $emInfraestructura    = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSoporte          = $this->getDoctrine()->getManager('telconet_soporte'); 
        
        $documento = $em->getRepository('schemaBundle:InfoDocumento')->find($id);          
                           
        if (!$documento) {
            throw $this->createNotFoundException('Unable to find Documento entity.');
        }
        
        $documentoRelacion = $em->getRepository('schemaBundle:InfoDocumentoRelacion')->findOneByDocumentoId($id);
        
        $formInfoDocumento   = $this->createForm(new InfoDocumentoGestionType(),$documento); 
        
        $tipoDocumentoGeneral = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
					  ->find($documento->getTipoDocumentoGeneralId());
	
	if($documentoRelacion->getPuntoId())
	{
	      $puntoCliente         = $emComercial->getRepository('schemaBundle:InfoPunto')
					  ->find($documentoRelacion->getPuntoId());
        }else $puntoCliente = null;						      
        //*****************************************************************************************************
        //           Informacion relacionada con el Tecnico : Tipo/Modelo/Elemento
        //*****************************************************************************************************   
        
        if($documentoRelacion->getTipoElementoId())
        {
	      $tipoElemento  = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
					  ->find($documentoRelacion->getTipoElementoId());        
        }else $tipoElemento=null;
        
        if($documentoRelacion->getModeloElementoId())
        {
	      $modeloElemento = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
					  ->find($documentoRelacion->getModeloElementoId());        
        }else $modeloElemento=null;
        
        if($documentoRelacion->getElementoId())
        {
	      $elemento       = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
					  ->find($documentoRelacion->getElementoId());        
        }else $elemento = null;   
        
        //*****************************************************************************************************
        //           Informacion relacionada con el Financiero/Comercial : Contratos/Documentos Financieros
        //*****************************************************************************************************                  
        
        $numeroDocumento = '';
        
        if($documentoRelacion->getContratoId())
        {
	      $contrato       = $emComercial->getRepository('schemaBundle:InfoContrato')
					  ->find($documentoRelacion->getContratoId());  
              if($contrato)$numeroDocumento =  $contrato->getNumeroContrato();				    
        }
        
        if($documentoRelacion->getDocumentoFinancieroId())
        {
	      $docFinan      = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
					  ->find($documentoRelacion->getDocumentoFinancieroId()); 
              if($docFinan)$numeroDocumento = $docFinan->getNumeroFacturaSri();					  
        }                
        
        //***********************************************************************************
        //           Informacion relacionada con el Soporte : Tareas/Casos
        //***********************************************************************************
        
        $numeroTareaCaso = '';   $tipoTareaCaso=null;     
        
        if($documentoRelacion->getCasoId())
        {
	      $caso            = $emSoporte->getRepository('schemaBundle:InfoCaso')
					  ->find($documentoRelacion->getCasoId());  
              if($caso){
		    $numeroTareaCaso = $caso->getNumeroCaso();					      
		    $tipoTareaCaso['value']   = 'C';
		    $tipoTareaCaso['opcion']  = 'Caso';
	      }
        }
        
        if($documentoRelacion->getActividadId())
        {
	      $actividad       = $em->getRepository('schemaBundle:InfoComunicacion')
					  ->find($documentoRelacion->getActividadId()); 
              if($actividad){
		    $numeroTareaCaso = $actividad->getId();					      
		    $tipoTareaCaso['value']   = 'T';
		    $tipoTareaCaso['opcion']  = 'Tarea';
              }			
        }
        
        
        return $this->render('soporteBundle:gestion_documentos:edit.html.twig', array(                        
            'formDocumento'  => $formInfoDocumento->createView(),
            'tipoDocGeneral' => $tipoDocumentoGeneral,
            'puntoCliente'   => $puntoCliente?$puntoCliente:null,
            'numeroDoc'      => $numeroDocumento,
            'tipoElemento'   => $tipoElemento?$tipoElemento:null,
            'modeloElemento' => $modeloElemento?$modeloElemento:null,
            'elemento'       => $elemento?$elemento:null,
            'ubicacionFisicaDocumento' => base64_encode($documento->getUbicacionFisicaDocumento()),
            'ubicacionLogicaDocumento' => $documento->getUbicacionLogicaDocumento(),
            'numeroTareaCaso' => $numeroTareaCaso,
            'tipoTareaCaso'   => $tipoTareaCaso?$tipoTareaCaso:null
        ));
    }
       
   /**
     * gridAction
     *
     * Metodo encargado de invocar al Service que se encarag de mostrar los documentos relacionados al modulo que sean invocados          
     *
     * @return json con informacion a mostrar
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 24-09-2015 - Se agregan los parámetros de fecha en la búsqueda de los documentos técnicos
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 10-07-2014
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 21-02-2020 - En caso que los filtros de fechas sean nulos, por defecto se obtendrá los datos de acuerdo
     *                           a los meses que se encuentren parametrizados.
     */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $ssesion  = $peticion->getSession();
        
        $parametros = array();
        $emGeneral  = $this->getDoctrine()->getManager('telconet_general');

        $parametros["modulo"]               = $peticion->query->get('modulo'); 
        $parametros["nombreDocumento"]      = $peticion->query->get('nombre');
        $parametros["tipoDocumentoGeneral"] = $peticion->query->get('tipoDocumento');
        $parametros["tipoDocumento"]        = $peticion->query->get('extensionDoc');      
        $parametros["punto"]                = $peticion->query->get('login');      
        $parametros["tipoElemento"]         = $peticion->query->get('tipoElemento');      
        $parametros["modeloElemento"]       = $peticion->query->get('modeloElemento');      
        $parametros["elemento"]             = $peticion->query->get('elemento');      
        $parametros["empresa"]              = $ssesion->get('idEmpresa');
        $parametros["estado"]               = $peticion->query->get('estado');                 
        $parametros["numeroDocumento"]      = $peticion->query->get('numeroDocumento');  //Contrato/Documento FInanciero
        $parametros["numeroTareaCaso"]      = $peticion->query->get('casoTarea');  //Numero de tareas o Caso
        $parametros["encuesta"]             = $peticion->query->get('encuesta');  //Id de Encuesta
        $parametros["servicio"]             = $peticion->query->get('servicio');  //Id Servicio
        
        $strFechaDesde   = $peticion->query->get('fechaDesde') ? $peticion->query->get('fechaDesde') : '';
        $strFechaHasta   = $peticion->query->get('fechaHasta') ? $peticion->query->get('fechaHasta') : '';
        $arrayFechaDesde = explode('T', $strFechaDesde);
        $arrayFechaHasta = explode('T', $strFechaHasta);
        
        $parametros["fechaDesde"] = $arrayFechaDesde[0]; 
        $parametros["fechaHasta"] = $arrayFechaHasta[0];

        /*
         * En caso que los filtros de fecha sean nulos, por defecto se toma la información desde
         * hace 6 meses en adelante (Valor parametrizable y puede ser cambiado).
         *
         * días (days) ; semanas (week) ; meses (month) ; años (year)
         */
        $arrayParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne('DIAS_CONSULTA_GESTION_DOCUMENTOS','SOPORTE','','','DIAS_PERMITIDOS','','','','','');

        $strValor = !empty($arrayParametrosDet) && isset($arrayParametrosDet['valor3']) &&
                    !empty($arrayParametrosDet['valor3']) ? $arrayParametrosDet['valor3'] : '';

        if (empty($parametros["fechaDesde"]) && empty($parametros["fechaHasta"]) && $strValor)
        {
            $strExpresionRegular      = "/^[+|-]{1} [1-9]{1,99} (days|week|month|year)+$/";
            $intValidar               = preg_match($strExpresionRegular,$strValor);
            $strValor                 = $intValidar === 1 ? $strValor : '- 6 month';
            $parametros["fechaDesde"] = date("Y-m-d",strtotime(date("Y-m-d").$strValor));
        }

        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
	
        /* @var $soporteService SoporteService */
        $soporteService = $this->get('soporte.SoporteService'); 
        $parametros["VerDocumentoPersonal"] = $this->get('security.context')->isGranted('ROLE_302-8057');
        $parametros["DescargarDocumentosPersonales"] = $this->get('security.context')->isGranted('ROLE_302-8058');
        $objJson =  $soporteService->obtenerDocumentos($parametros,$start,$limit);          
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * deleteAction
     *
     * Metodo encargado de eliminar (estado) al registro enviado como parametros
     *
     * @param integer $id     
     *
     * @return redirecciona al index
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 10-07-2014
     */           
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_comunicacion');
        $request = $this->getRequest();

        $entity = $em->getRepository('schemaBundle:InfoDocumento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SistAccion entity.');
        }

        $entity->setEstado("Eliminado");
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setUsrCreacion($request->getSession()->get('user'));
        $em->flush();

        return $this->redirect($this->generateUrl('gestion_documentos'));
    }
    
     /**
     * deleteAjaxAction
     *
     * Metodo encargado de eliminar (estado) a los registros seleccionados de manera masiva    
     *
     * @return redirecciona al index
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 10-07-2014
     */            
    public function deleteAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        
        $parametro = $peticion->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_comunicacion");
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            
            if (null == $entity = $em->find('schemaBundle:InfoDocumento', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
                $entity->setEstado("Eliminado");
                $em->persist($entity);
                
                $em->flush();
                $respuesta->setContent("Se elimino la entidad");
            }
        endforeach;        
        
        return $respuesta;
    }
    
      /**
     * updateAction
     *
     * Metodo encargado realizar el update de los documentos  
     *
     * @param integer $id
     *
     * @return redirecciona al show
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 10-07-2014
     */           
    public function updateAction($id)
    {    
	$respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $request = $this->get('request');
    
        $em           = $this->get('doctrine')->getManager('telconet_comunicacion');    
        $emComercial  = $this->get('doctrine')->getManager('telconet');  
        $emFinanciero = $this->get('doctrine')->getManager('telconet_financiero');
        $emGeneral    = $this->get('doctrine')->getManager('telconet_general');
        $emSoporte    = $this->get('doctrine')->getManager('telconet_soporte');
        
        $nombreDocumento = $request->get('nombreDocumento')?$request->get('nombreDocumento'):'';  
	$modulo          = $request->get('modulo')?$request->get('modulo'):'';  
	$tipoDocumento   = $request->get('tipoDocumento')?$request->get('tipoDocumento'):'';  
	$extensionDoc    = $request->get('extensionDoc')?$request->get('extensionDoc'):''; 	  
	$elemento        = $request->get('elemento')?$request->get('elemento'):'';  
	$login           = $request->get('login')?$request->get('login'):'';  
	$numeroDocumento = $request->get('numeroDocumento')?$request->get('numeroDocumento'):'';  
	$numeroTareaCaso = $request->get('numeroTareaCaso')?$request->get('numeroTareaCaso'):'';
	$tipoElemento    = $request->get('tipoElemento')?$request->get('tipoElemento'):'';  
	$modeloElemento  = $request->get('modeloElemento')?$request->get('modeloElemento'):'';  
	$hiddenFile      = $request->get('hiddenFile')?$request->get('hiddenFile'):'';  
	$nameFile        = $request->get('nameFile')?$request->get('nameFile'):'';  		
	$descripcion     = $request->get('descripcion')?$request->get('descripcion'):'';
	
	//Se obtiene el documento para ser editado
	$entity = $em->getRepository('schemaBundle:InfoDocumento')->find($id);
	$documentoRelacion = $em->getRepository('schemaBundle:InfoDocumentoRelacion')->findOneByDocumentoId($id);
	
	if($extensionDoc!='')
	{
	      $tipoDocId = $em->getRepository('schemaBundle:AdmiTipoDocumento')->find($extensionDoc);
	      if($tipoDocId) $entity->setTipoDocumentoId($tipoDocId);  	
	}	
	
	if($numeroDocumento!='')
	{
	      if($modulo == 'COMERCIAL')
	      {
		    $contrato = $emComercial->getRepository('schemaBundle:InfoContrato')->findOneByNumeroContrato($numeroDocumento);
		    if($contrato)
		    {
			  $documentoRelacion->setContratoId($contrato->getId());			  
                    }			    
	      
	      }
	      
	      if($modulo == 'FINANCIERO')
	      {
		    $docFinan = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->findOneByNumeroFacturaSri($numeroDocumento);
		    if($docFinan)
		    {
			  $documentoRelacion->setDocumentoFinancieroId($docFinan->getId());			  
		    }
	      
	      }
	}
		
	
	if($numeroTareaCaso!='')
	{	      	
	      $caso = $emSoporte->getRepository('schemaBundle:InfoCaso')->findOneByNumeroCaso($numeroTareaCaso);	      
	      if($caso)
	      {
		    $documentoRelacion->setCasoId($caso->getId());
		    $documentoRelacion->setActividadId('');
	      }
	      else
	      {
		    $actividad = $em->getRepository('schemaBundle:InfoComunicacion')->find($numeroTareaCaso);
		    if($actividad)
		    {	
			  $documentoRelacion->setActividadId($actividad->getId());
			  $documentoRelacion->setCasoId('');
		    }
	      }
	}
	else
	{
	      $documentoRelacion->setCasoId('');
	      $documentoRelacion->setActividadId('');	
	}
	
	if($tipoElemento!='')
	{
	      $documentoRelacion->setTipoElementoId($tipoElemento);
	}
	if($modeloElemento!='')
	{
	      $documentoRelacion->setModeloElementoId($modeloElemento);
	}
	if($elemento!='')
	{
	      $documentoRelacion->setElementoId($elemento);
	}
	if($login!='')
	{
	      $documentoRelacion->setPuntoId($login);
	      $personaRol = $emComercial->getRepository('schemaBundle:InfoPunto')->find($login);
	      if($personaRol)$documentoRelacion->setPersonaEmpresaRolId($personaRol->getPersonaEmpresaRolId()->getId());
	}
              
        $documentoRelacion->setModulo($modulo);                 
        $entity->setTipoDocumentoGeneralId($tipoDocumento);    
        $entity->setNombreDocumento($nombreDocumento);
        $entity->setUbicacionFisicaDocumento(base64_decode($hiddenFile));
        $entity->setUbicacionLogicaDocumento($nameFile);
        $entity->setMensaje($descripcion);
        $entity->setEstado('Modificado');               
        
        //Uso de manera local y pruebas
        $comando = "cp ".base64_decode($hiddenFile)." /var/www/telcos/web/public/uploads/documentos/".$nameFile;                                         
	shell_exec($comando);
        
        try
        {        
	      $em->getConnection()->beginTransaction();
	      $em->persist($entity);
	      $em->flush();	      

	      $em->persist($documentoRelacion);
	      $em->flush();	      
	      $em->getConnection()->commit();
			  
	      $resultado = json_encode(array('success'=>true, 'id'=>$entity->getId()));	             
        
        }catch(Exception $e)
        {
        
	      $em->getConnection()->rollback();
	      $em->getConnection()->close();			
	      $resultado = json_encode(array('success'=>false,'mensaje'=>$e));
        }                               
        
        $respuesta->setContent($resultado);	
        return $respuesta;  
	
	
    }

     /**
     * fileUploadAction
     *
     * Metodo encargado de procesar los archvios que se elijan en el formulario y los
     * coloca en el directorio de destino para luego tomar su infirmacion basica y guardarlos en la base
     *
     * @return json con resultado del proceso
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 05-03-2018 Se realizan ajustes para implementar la carga masiva de series de elementos
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 17-05-2016 Se realiza ajustes para que la carga de archivos en tareas soporte tipos WORD y EXCEL
     *
     * @author Edgar Holguin <eholguin@telconet.ec> 
     * @version 1.3 11-04-2016  Se especifica la ruta destino donde se guardan actas de entrega de TN
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 30-03-2016 Se realiza ajustes por requerimiento que permite subir archivos a nivel de tareas
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 18-01-2016 Se realizan ajustes para permitir la carga de adjuntos en la creacion de un caso
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 06-05-2022 Se modifica la función para que los archivos adjuntos se suban al NFS
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 10-07-2014
     * @version 1.0 14-08-2014
     */ 
    public function fileUploadAction()
    {
        $request          = $this->getRequest();
        $strIpCreacion    = $request->getClientIp();
        $peticion         = $this->get('request');
        $objSession  = $request->getSession();
        $strUser     = $objSession->get('user');
        $servicio    = $peticion->get('servicio')?$peticion->get('servicio'):null;
        $idCaso      = $peticion->get('Idcaso')?$peticion->get('Idcaso'):null;
        $idTarea     = $peticion->get('IdTarea')?$peticion->get('IdTarea'):null;
        $origenCaso  = $peticion->get('origenCaso')?$peticion->get('origenCaso'):"N";
        $origenTarea = $peticion->get('origenTarea')?$peticion->get('origenTarea'):"N";
        $origenCarga = $peticion->get('origenCarga')?$peticion->get('origenCarga'):"N/A";
        $strEstado   = $peticion->get('estadoActualizar')?$peticion->get('estadoActualizar'):"";
        $serviceUtil = $this->get('schema.Util');
        $serviceInfoElemento = $this->get('tecnico.InfoElemento');
        $parametros = array();
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $strCodEmpresa      = $objSession->get('idEmpresa');
        $strCodigoDocumento = $peticion->get('codigo')?$peticion->get('codigo'):null;
        $arrayPathAdicional = [];
        $arrayResultadoData = array();
        try{
            $file = $request->files;

            $objArchivo = $file->get('archivo');

            if($file && count($file)>0)
            {
                if(isset($objArchivo))
                {
                    if($objArchivo && count($objArchivo)>0)
                    {
                        $tipo    = $objArchivo->guessExtension(); //extension		
                        $archivo = $objArchivo->getClientOriginalName();
                        $tamano  = $objArchivo->getClientSize();

                        $arrayArchivo  = explode('.', $archivo);
                        $countArray    = count($arrayArchivo);
                        $nombreArchivo = $arrayArchivo[0];
                        $extArchivo    = $arrayArchivo[$countArray-1];

                        $prefijo = substr(md5(uniqid(rand())),0,6);

                        if ($archivo != "") 
                        {
                            $nuevoNombre = $nombreArchivo . "_" . $prefijo . "." . $extArchivo;

                            $nuevoNombre = str_replace(" ","_",$nuevoNombre);

                            if($strPrefijoEmpresa == "TN")
                            {
                                if($origenCarga == "auditoriaElementos")
                                {
                                    $strSubModulo = "AuditoriaElementos";
                                }
                                else
                                {
                                    $nuevoNombre  =  $nombreArchivo.".".$extArchivo;
                                    if($servicio && trim($strCodigoDocumento)=="ACT")
                                    {
                                        $strSubModulo           = "ActasEntregas";
                                    }
                                    else
                                    {
                                        $strSubModulo = "GestionDocumentosSoporte";
                                    }
                                }
                            }
                            else
                            {
                                if($origenCarga == "auditoriaElementos")
                                {
                                    $strSubModulo = "AuditoriaElementos";
                                }
                                else
                                {
                                    if($servicio && trim($strCodigoDocumento)=="ACT")
                                    {
                                        $strSubModulo = "ActasEntregas";
                                    }
                                    else
                                    {
                                        $strSubModulo = "GestionDocumentosSoporte";
                                    }
                                }
                            }
                            
                            $strFileBase64 = base64_encode(file_get_contents($objArchivo->getPathName()));
                            
                            $arrayParamNfs = array( 'prefijoEmpresa'       => $strPrefijoEmpresa,
                                                    'strApp'               => "TelcosWeb",
                                                    'strSubModulo'         => $strSubModulo,
                                                    'arrayPathAdicional'   => $arrayPathAdicional,
                                                    'strBase64'            => $strFileBase64,
                                                    'strNombreArchivo'     => $nuevoNombre,
                                                    'strUsrCreacion'       => $strUser);
                            
                            //Si proviene de subida a partir del servicio del cliente o de la creacion de un caso
                            if($servicio || $origenCaso == 'S' || $origenTarea == 'S' || $origenCarga == "auditoriaElementos")
                            {
                                //PDF o JPG
                                if(($tipo && $tipo=='pdf' && $origenCarga != "auditoriaElementos") ||
                                    ($tipo && $origenCaso == 'S' & ($tipo=='jpg' || $tipo=='jpeg')) ||
                                  ($tipo && $origenTarea == 'S' & ($tipo=='jpg' || $tipo=='jpeg' || $tipo=='pdf'
                                   || $tipo=='doc' || $tipo=='docx' || $tipo=='xlsx')) || ($extArchivo =='xlsx'))
                                {
                                    $arrayRespNfs = $serviceUtil->guardarArchivosNfs($arrayParamNfs);
                                    if(isset($arrayRespNfs) && $arrayRespNfs['intStatus'] == 200)
                                    {
                                        if($origenCaso == 'S')
                                        {
                                            $parametros["idCaso"]      = $idCaso;
                                            $parametros["nuevoNombre"] = $nuevoNombre;
                                            $parametros["destino"]     = $arrayRespNfs['strUrlArchivo'];
                                            $parametros["peticion"]    = $peticion;
                                            $parametros["tipo"]        = $tipo;
                                            $parametros["band"]        = "C";

                                            $bool = $this->guardarAdjuntoCaso($parametros);
                                        }
                                        else if($origenTarea == 'S')
                                        {
                                            $parametros["idTarea"]     = $idTarea;
                                            $parametros["nuevoNombre"] = $nuevoNombre;
                                            $parametros["destino"]     = $arrayRespNfs['strUrlArchivo'];
                                            $parametros["peticion"]    = $peticion;
                                            $parametros["tipo"]        = $tipo;
                                            $parametros["band"]        = "T";

                                            $bool = $this->guardarAdjuntoCaso($parametros);
                                        }
                                        else
                                        {
                                            if($origenCarga == "auditoriaElementos")
                                            {
                                                $arrayParametros["strEstadoActualizar"] = $strEstado;
                                                $arrayParametros["strUsrCreacion"]      = $strUser;
                                                $arrayParametros["strCodEmpresa"]       = $strCodEmpresa;
                                                $arrayParametros["strIpCreacion"]       = $strIpCreacion;
                                                $arrayParametros["strAbsolutePath"]     = $objArchivo->getPathName();
                                                $bool = $serviceInfoElemento->procesarSeriesElementos($arrayParametros);
                                            }
                                            else
                                            {
                                                $bool = $this->guardarActaRecepcion($servicio,
                                                                                    $nuevoNombre,
                                                                                    $arrayRespNfs['strUrlArchivo'],
                                                                                    $peticion);
                                            }
                                        }

                                        if($bool)
                                        {
                                            $arrayResultadoData = array("success"   => true, 
                                                                        "fileName"  => $nuevoNombre,
                                                                        "fileSize"  => "'".$tamano."'",
                                                                        "filePath"  => base64_encode($arrayRespNfs['strUrlArchivo']),
                                                                        "respuesta" => "Archivo Procesado Correctamente");
                                        }
                                        else
                                        {
                                            $arrayResultadoData = array("success"   => false, 
                                                                        "respuesta" => "Ha ocurrido un error, por favor reporte a Sistemas");
                                        }
                                    }
                                    else
                                    {
                                        $arrayResultadoData = array("success"   => false, 
                                                                    "respuesta" => "Ha ocurrido un error, por favor reporte a Sistemas");
                                    }

                                }
                                else
                                {
                                    if($origenCarga == "auditoriaElementos")
                                    {
                                        $arrayResultadoData = array("success"   => false, 
                                                                    "respuesta" => "Debe subir archivo con formato .xlsx");
                                    }
                                    else if($origenCaso == 'S' || $origenTarea == 'S')
                                    {
                                        $arrayResultadoData = array("success"   => false, 
                                                                    "respuesta" => "Debe subir archivo con formato PDF, JPG, WORD o EXCEL");
                                    }
                                    else
                                    {
                                        $arrayResultadoData = array("success"   => false, 
                                                                    "respuesta" => "Debe subir archivo con formato PDF");
                                    }
                                }
                            }
                            else
                            {
                                $arrayRespNfs = $serviceUtil->guardarArchivosNfs($arrayParamNfs);
                                if(isset($arrayRespNfs) && $arrayRespNfs['intStatus'] == 200)
                                {
                                    $arrayResultadoData = array("success"   => true, 
                                                                "fileName"  => $nuevoNombre,
                                                                "fileSize"  => "'".$tamano."'",
                                                                "filePath"  => base64_encode($arrayRespNfs['strUrlArchivo']));
                                }
                                else
                                {
                                    $arrayResultadoData = array("success"   => false, 
                                                                "fileName"  => $nuevoNombre,
                                                                "fileSize"  => "'".$tamano."'",
                                                                "filePath"  => "");
                                }
                            }
                        }
                        else
                        {
                            $arrayResultadoData = array("success"   => false, 
                                                        "fileName"  => "",
                                                        "fileSize"  => "0",
                                                        "filePath"  => "");
                        }
                    }//FIN IF ARCHIVO SUBIDO
                    else
                    {
                        $arrayResultadoData = array("success"   => false, 
                                                    "fileName"  => "",
                                                    "fileSize"  => "0",
                                                    "filePath"  => "");
                    }
                }//FIN IF ARCHIVO
                else
                {
                    $arrayResultadoData = array("success"   => false, 
                                                "fileName"  => "",
                                                "fileSize"  => "0",
                                                "filePath"  => "");
                }
            }//FIN IF FILES
            else
            {
                $arrayResultadoData = array("success"   => false, 
                                            "fileName"  => "",
                                            "fileSize"  => "0",
                                            "filePath"  => "");
                
            }
        }
        catch(\Exception $e)
        {
            $arrayResultadoData = array("success"   => false, 
                                        "respuesta" => $e->getMessage());
        }
        $objResponse = new JsonResponse();
        $objResponse->setData($arrayResultadoData);
	  	return $objResponse;
    }

     /**
     * fileUploadNfsAction
     *
     * Metodo encargado de procesar los archvios que se elijan en el formulario y los
     * sube por medio del microservicio nfs desde la opción: Gestión Documentos > nuevo documento
     *
     * @return json con resultado del proceso
     *
     * @author Andrés Montero Holguin <amontero@telconet.ec>
     * @version 1.0 11-03-2021
     * 
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.1 22-06-2021 Se realizan ajustes para renombrar archivos que tienen caracteres especiales y espacios, antes de ser subidos al NFS
     * 
     */ 
    public function fileUploadNfsAction()
    {
        $objRequest         = $this->getRequest();
        $strIpCreacion      = $objRequest->getClientIp();
        $objPeticion        = $this->get('request');
        $objSession         = $objRequest->getSession();
        $strUser            = $objSession->get('user');
        $objServiceUtil     = $this->get('schema.Util');
        $arrayParametros    = array();
        $objSession         = $objRequest->getSession();
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $strCodEmpresa      = $objSession->get('idEmpresa');
        $strSubModulo       = $objPeticion->get('modulo')?
                              "GestionDocumentos".ucfirst(strtolower($objPeticion->get('modulo'))):"GestionDocumentosSoporte";
        $strApp             = "TelcosWeb";

        try
        {
            $objRespuesta = new Response();
            $objRespuesta->headers->set('Content-Type', 'text/html');

            $objFile = $objRequest->files;

            $objArchivo = $objFile->get('archivo');

            if($objFile && count($objFile)>0)
            {
                if(isset($objArchivo))
                {
                    if($objArchivo && count($objArchivo)>0)
                    {
                        $strTipo    = $objArchivo->guessExtension();		
                        $strArchivo = $objArchivo->getClientOriginalName();
                        $strTamano  = $objArchivo->getClientSize();

                        $arrayArchivo  = explode('.', $strArchivo);
                        $intCountArray    = count($arrayArchivo);
                        $strNombreArchivo = $arrayArchivo[0];
                        $strExtArchivo    = $arrayArchivo[$intCountArray-1];

                        $strPrefijo = substr(md5(uniqid(rand())),0,6);

                        if ($strArchivo != "") 
                        {
                            $strNuevoNombre = $strNombreArchivo . "_" . $strPrefijo . "." . $strExtArchivo;
                            
                            // Se reemplazan caracteres que no cumplen con el patron definido para el nombre del archivo
                            $strPatronABuscar = '/[^a-zA-Z0-9._-]/';
                            $strCaracterReemplazo = '_';
                            $strNuevoNombre = preg_replace($strPatronABuscar,$strCaracterReemplazo,$strNuevoNombre);
                            
                            $strFileBase64 = base64_encode(file_get_contents($objArchivo->getPathName()));

                            //####################################
                            //INICIO DE SUBIR ARCHIVO AL NFS >>>>>
                            //####################################
                            $arrayParamNfs = array(
                                'prefijoEmpresa'       => $strPrefijoEmpresa,
                                'strApp'               => $strApp ,
                                'arrayPathAdicional'   => [],
                                'strBase64'            => $strFileBase64,
                                'strNombreArchivo'     => $strNuevoNombre,
                                'strUsrCreacion'       => $strUser,
                                'strSubModulo'         => $strSubModulo);

                            $arrayRespNfs = $objServiceUtil->guardarArchivosNfs($arrayParamNfs);
                            //##################################
                            //<<<<< FIN DE SUBIR ARCHIVO AL NFS
                            //##################################

                            if(isset($arrayRespNfs) && $arrayRespNfs['intStatus'] == 200)
                            {
                                $strResultado= '{"success":true,"fileName":"'.$strNuevoNombre.'","fileSize":"'.$strTamano.'" ,"filePath":"'
                                                    .base64_encode($arrayRespNfs['strUrlArchivo']).'"}';
                                //REGISTRAMOS EN LOG
                                $arrayParametrosLog['enterpriseCode']   = $strCodEmpresa; 
                                $arrayParametrosLog['logType']          = "0";
                                $arrayParametrosLog['logOrigin']        = "TELCOS";
                                $arrayParametrosLog['application']      = "TELCOS";
                                $arrayParametrosLog['appClass']         = "GestionDocumentosController";
                                $arrayParametrosLog['appMethod']        = "fileUploadNfsAction";
                                $arrayParametrosLog['messageUser']      = "No aplica.";
                                $arrayParametrosLog['status']           = "Exitoso";
                                $arrayParametrosLog['descriptionError'] = "Se guarda archivo correctamente atravez de microservicio de Nfs (".
                                                                          $arrayRespNfs['strUrlArchivo'].")";
                                $arrayParametrosLog['inParameters']     = json_encode($arrayRespNfs);
                                $arrayParametrosLog['creationUser']     = "TELCOS";

                                $objServiceUtil->insertLog($arrayParametrosLog);

                            }
                            else
                            {
                                $strResultado= '{"success":false,"fileName":"'.$strNuevoNombre.'","fileSize":"'.$strTamano.
                                                '" , "filePath":"", "respuesta":'.
                                                '"Se presento un error al intentar guardar el archivo, por favor notificar a Sistemas!"}';
                            }
                        }
                        else
                        {
                            $strResultado= '{"success":false,"fileName":"","fileSize":"'. 0 .'", "filePath":""}';
                        }
                    }//FIN IF ARCHIVO SUBIDO
                    else
                    {
                        $strResultado= '{"success":false,"fileName":"","fileSize":"'. 0 .'", "filePath":""}';
                    }
                }//FIN IF ARCHIVO
                else
                {
                    $strResultado= '{"success":false,"fileName":"","fileSize":"'. 0 .'", "filePath":""}';
                }
            }//FIN IF FILES
            else
            {
                $strResultado= '{"success":false,"fileName":"","fileSize":"'. 0 .'", "filePath":""}';
            }

            $objRespuesta->setContent($strResultado);
            return $objRespuesta;

        }
        catch(\Exception $e)
        {
            $strResultado= '{"success":false,"respuesta":"'.$e->getMessage().'"}';
            $objRespuesta->setContent($strResultado);
            return $objRespuesta;
        }
    }
    
    /**
     * guardarActaRecepcion
     *
     * Metodo encargado de guardar la informacion documental relacionada al ACTA DE RECEPCION del cliente
     * a la cual se le activa el servicio
     *          
     * @return boolean
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 10-07-2014
     * @version 1.0 14-08-2014
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 02-12-2015 - Se realizan ajustes para relacionar el detalleId de la solicitud de planificacion al momento de guardar la 
     *                           acta de recepcion
     *
     */ 
    public function guardarActaRecepcion($idServicio,$nombre,$destino,$request)
    {
        $emComercial        = $this->getDoctrine()->getManager('telconet');  
        $emComunicacion     = $this->getDoctrine()->getManager('telconet_comunicacion'); 
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');

        $servicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);

        if($servicio)
        {
            $idPunto = $servicio->getPuntoId()->getId();				

            if($idPunto)
            {		
                try
                {  		
                    $emComunicacion->getConnection()->beginTransaction();

                    $punto  = $emComercial->getRepository('schemaBundle:InfoPunto')->find($idPunto);
                    //se crea el documento que incurre en esta comunicacion
                    $entity = new InfoDocumento();   

                    $entity->setNombreDocumento('Acta de Recepcion');
                    $entity->setUbicacionFisicaDocumento($destino);
                    $entity->setUbicacionLogicaDocumento($nombre);
                    $entity->setMensaje('Acta de Recepcion de Activacion de Servicio');
                    $entity->setEstado('Activo');
                    $entity->setFeCreacion(new \DateTime('now'));
                    $entity->setFechaDocumento(new \DateTime('now'));
                    $entity->setIpCreacion('127.0.0.1');
                    $entity->setUsrCreacion($request->getSession()->get('user'));                         
                    $entity->setEmpresaCod($request->getSession()->get('idEmpresa'));

                    //'extensionTipoDocumento'=>'PDF' : extension del archivo que se carga desde el Telcos
                    $tipoDocId = $emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                ->findOneByExtensionTipoDocumento(array('extensionTipoDocumento'=>'PDF'));
                    if($tipoDocId)
                    {
                        $entity->setTipoDocumentoId($tipoDocId);
                    }
                    //'codigoTipoDocumento'=>'ACTA': tipo de documento (ACTA ENTREGA RECEPCION)que se carga desde el Telcos
                    $tipoDocumento = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                               ->findOneByCodigoTipoDocumento(array('codigoTipoDocumento'=>'ACTA'));

                    if($tipoDocumento)
                    {
                        $entity->setTipoDocumentoGeneralId($tipoDocumento->getId());
                    }    

                    //Entidad de la tabla INFO_DOCUMENTO_RELACION donde se relaciona el tipo del documento con el servicio y con la
                    //solicitud de PLANIFICACION
                    $entityRelacion = new InfoDocumentoRelacion();

                    $entityRelacion->setModulo('TECNICO');  
                    $entityRelacion->setEstado('Activo');  
                    $entityRelacion->setFeCreacion(new \DateTime('now'));  
                    $entityRelacion->setUsrCreacion($request->getSession()->get('user'));
                    $entityRelacion->setPuntoId($idPunto);
                    $entityRelacion->setServicioId($idServicio);
                    $entityRelacion->setPersonaEmpresaRolId($punto->getPersonaEmpresaRolId()->getId());

                    //Se obtiene el id de la solicitud de planificacion que tiene asociado el servicio, con el objetivo de relacionarlo con el
                    //documento
                    $detalle = $emComercial->getRepository('schemaBundle:InfoDetalle')->getUltimoDetalleSolicitud($idServicio);

                    if($detalle)
                    {
                        $entityRelacion->setDetalleId($detalle['IDDETALLE']);
                    }

                    $emComunicacion->persist($entity);
                    $emComunicacion->flush();	      

                    $entityRelacion->setDocumentoId($entity->getId());	 

                    $emComunicacion->persist($entityRelacion);
                    $emComunicacion->flush();

                    $emComunicacion->getConnection()->commit();	

                    return true;

                }
                catch(\Exception $e)
                {		      
                    $emComunicacion->getConnection()->rollback();
                    $emComunicacion->getConnection()->close();

                    return false;
                }
            }
        }
    }

    /**
     * guardarAdjuntoCaso
     *
     * Metodo encargado de guardar la informacion documental relacionada al documento adjunto relacionado en la
     * creacion del caso
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 15-01-2016
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 30-03-2016 Se realiza ajustes por requerimiento que permite subir archivos a nivel de tareas
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 17-05-2016 Se realiza ajustes para que la carga de archivos en tareas soporte tipos WORD y EXCEL
     *
     * @param array $parametros [integer idCaso, string nuevoNombre, string destino, string peticion, string tipo]
     *
     * @return boolean
     *
     */
    public function guardarAdjuntoCaso($parametros)
    {
        $emComunicacion     = $this->getDoctrine()->getManager('telconet_comunicacion');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $emSoporte          = $this->getDoctrine()->getManager('telconet_soporte');

        $idTarea = $parametros["idTarea"]?$parametros["idTarea"]:"";
        $idCaso  = $parametros["idCaso"]?$parametros["idCaso"]:"";
        $nombre  = $parametros["nuevoNombre"];
        $destino = $parametros["destino"];
        $request = $parametros["peticion"];
        $tipo    = $parametros["tipo"];
        $band    = $parametros["band"];
        $tipoDoc = "PDF";

        if($band == "C")
        {
            $caso = $emSoporte->getRepository('schemaBundle:InfoCaso')->find($idCaso);
        }
        else if($band == "T")
        {
            $tarea = $emSoporte->getRepository('schemaBundle:InfoDetalle')->find($idTarea);
        }
        if($caso || $tarea)
        {
            try
            {
                $emComunicacion->getConnection()->beginTransaction();

                //se crea el documento que incurre en esta comunicacion
                $entity = new InfoDocumento();

                if($band == "C")
                {
                    $entity->setNombreDocumento('Adjunto Caso');
                }
                else if($band == "T")
                {
                    $entity->setNombreDocumento('Adjunto Tarea');
                }
                $entity->setUbicacionFisicaDocumento($destino);
                $entity->setUbicacionLogicaDocumento($nombre);

                if($band == "C")
                {
                    $entity->setMensaje('Documento que se adjunta en la creacion de un Caso');
                }
                else if($band == "T")
                {
                    $entity->setMensaje('Documento que se adjunta a una tarea');
                }
                $entity->setEstado('Activo');
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setFechaDocumento(new \DateTime('now'));
                $entity->setIpCreacion('127.0.0.1');
                $entity->setUsrCreacion($request->getSession()->get('user'));
                $entity->setEmpresaCod($request->getSession()->get('idEmpresa'));

                if($tipo=='pdf')
                {
                   $tipoDoc = "PDF" ;
                }
                else if($tipo=='jpg' || $tipo=='jpeg')
                {
                   $tipoDoc = "JPG" ;
                }
                else if($tipo=='doc')
                {
                   $tipoDoc = "DOC" ;
                }
                else if($tipo=='docx')
                {
                   $tipoDoc = "DOCX" ;
                }
                else if($tipo=='xlsx')
                {
                   $tipoDoc = "XLSX" ;
                }
                //'extensionTipoDocumento'=>'PDF' : extension del archivo que se carga desde el Telcos
                $tipoDocId = $emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                            ->findOneByExtensionTipoDocumento(array('extensionTipoDocumento'=> $tipoDoc));
                if($tipoDocId)
                {
                    $entity->setTipoDocumentoId($tipoDocId);
                }
                //'codigoTipoDocumento'=>'OTR': tipo de documento (OTROS)que se carga desde el Telcos
                $tipoDocumento = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                           ->findOneByCodigoTipoDocumento(array('codigoTipoDocumento'=>'OTR'));

                if($tipoDocumento)
                {
                    $entity->setTipoDocumentoGeneralId($tipoDocumento->getId());
                }

                //Entidad de la tabla INFO_DOCUMENTO_RELACION donde se relaciona el documento cargado con el IdCaso
                $entityRelacion = new InfoDocumentoRelacion();
                $entityRelacion->setModulo('SOPORTE');
                $entityRelacion->setEstado('Activo');
                $entityRelacion->setFeCreacion(new \DateTime('now'));
                $entityRelacion->setUsrCreacion($request->getSession()->get('user'));

                if($band == "C")
                {
                    $entityRelacion->setCasoId($idCaso);
                }
                else if($band == "T")
                {
                    $entityRelacion->setDetalleId($idTarea);
                }
                $emComunicacion->persist($entity);
                $emComunicacion->flush();

                $entityRelacion->setDocumentoId($entity->getId());

                $emComunicacion->persist($entityRelacion);
                $emComunicacion->flush();

                $emComunicacion->getConnection()->commit();

                return true;
            }
            catch(\Exception $e)
            {
                $emComunicacion->getConnection()->rollback();
                $emComunicacion->getConnection()->close();

                return false;
            }
        }
    }

       /**
     * resetFileAction
     *
     * Metodo encargado de eliminar el archivo subido cuando haya sido procesado por error o no se lo requiera
     *          
     * @return json con resultado del proceso
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 10-07-2014
     */ 
    public function resetFileAction()
    {
	  $respuesta = new Response();
	  $respuesta->headers->set('Content-Type', 'text/json');	  	  
	
	  try
	  {
		$peticion = $this->get('request');
	      
		$filePath = base64_decode($peticion->get('filePath')) ;	 
				
		unlink($filePath);		
			      
		$resultado= '{"success":"true"}';				    
		$respuesta->setContent($resultado);
		return $respuesta;
	  }
	  catch(Exception $e)
	  {
		$resultado= '{"success":"false"}';				    
		$respuesta->setContent($resultado);
		return $respuesta;
	  }
    }    	
    
    /**
     * getModeloElementosAction
     *
     * Metodo encargado de obtener los modelos de elementos segun tipo escogido
     *          
     * @return json con resultado del proceso
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 10-07-2014
     */ 
    public function getModeloElementosAction()
    {
	  $respuesta = new Response();
	  $respuesta->headers->set('Content-Type', 'text/json');
	
	  $peticion = $this->get('request');
	  $session  = $peticion->getSession();
	
	  $queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
	  $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));    
	  
	  $tipoElemento = $peticion->query->get('tipoElemento') ? $peticion->query->get('tipoElemento') : "";	      	      
	
	  $objJson = $this->getDoctrine()
			  ->getManager("telconet_infraestructura")
			  ->getRepository('schemaBundle:AdmiModeloElemento')		             
			  ->generarJsonModeloElementosPorTipoEmpresa($nombre, $session->get('idEmpresa') , $tipoElemento , 'Activo');
	  $respuesta->setContent($objJson);
	
	  return $respuesta;
    }
      /**
     * getElementosAction
     *
     * Metodo encargado de obtener los elementos segun tipo/modelo escogido
     *          
     * @return json con resultado del proceso
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 10-07-2014
     */ 
    public function getElementosAction()
    {
	  $respuesta = new Response();
	  $respuesta->headers->set('Content-Type', 'text/json');
	
	  $peticion = $this->get('request');
	
	  $queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
	  $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));    
	  
	  $modeloElemento = $peticion->query->get('modeloElemento') ? $peticion->query->get('modeloElemento') : "";
	  $tipoElemento   = $peticion->query->get('tipoElemento') ? $peticion->query->get('tipoElemento') : "";	      	    
	  	 
	  $objJson = $this->getDoctrine()
			  ->getManager("telconet_infraestructura")
			  ->getRepository('schemaBundle:AdmiModeloElemento')			  
			  ->generarJsonElementosPorModelo($nombre,'Activo',$tipoElemento,$modeloElemento);
	  $respuesta->setContent($objJson);
	
	  return $respuesta;
    }
    
        /**
     * getTipoDocumentoGeneralAction
     *
     * Metodo encargado de obtener los tipos de documentos provenientes del esquema DB_GENERAL
     * que hace referencia a que pueda ser IMAGEN/CONTRATO/DOCUMENTO...
     *          
     * @return json con resultado del proceso
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 10-07-2014
     */ 
    public function getTipoDocumentoGeneralAction()
    {
	  $respuesta = new Response();
	  $respuesta->headers->set('Content-Type', 'text/json');
	
	  $peticion = $this->get('request');	           		             
	
	  $registros = $this->getDoctrine()
			  ->getManager("telconet_general")
			  ->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')->findByEstado('Activo');			
          
          foreach ($registros as $data)
	  {                    			
		  $arr_encontrados[]=array(
		  
			  'idTipo' =>$data->getId(),
			  'descripcionTipoDocumento' => $data->getDescripcionTipoDocumento()				
			);
	  }
	  
	  $data = json_encode($arr_encontrados);
	  $resultado= '{"encontrados":'.$data.',"success":"true"}';
          
	  $respuesta->setContent($resultado);
	
	  return $respuesta;
    }
    
    
    /**
     * getTipoDocumentoAction
     *
     * Metodo encargado de obtener los tipos MIME del documento a subir
     *          
     * @return json con resultado del proceso
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 24-09-2015 - Se cambió la función para que solo retorne las extensiones de los tipos de documentos
     *                           que esten activos y que sean diferentes de null.
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 10-07-2014
     */ 
    public function getTipoDocumentoAction()
    {
        $jsonResponse    = new JsonResponse();
        $arrayResultados = array();
        $boolRespuesta   = false;
        
        $arrayParametros           = array();
        $arrayParametros['estado'] = 'Activo';
	
        $objRegistros = $this->getDoctrine()->getManager("telconet_comunicacion")->getRepository('schemaBundle:AdmiTipoDocumento')
                             ->getAdmiTipoDocumentoByCriterios($arrayParametros);			
        
        if( $objRegistros['registros'] )
        {
            foreach($objRegistros['registros'] as $objItem)
            {                    			
                $arrayResultados[] = array(
                                            'idTipo'                 => $objItem->getId(),
                                            'extensionTipoDocumento' => $objItem->getExtensionTipoDocumento()				
                                          );
            }
            
            $boolRespuesta = true;
        }
                
        $jsonResponse->setData(array('encontrados' => $arrayResultados, 'success' => $boolRespuesta));
	
        return $jsonResponse;
    }
    
    
     /**
     * descargarDocumentoAction
     *
     * Metodo encargado de descargar los documentos a partir del id de la referencia enviada
     *
     * @param integer $id
     *          
     * @return json con resultado del proceso
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 10-07-2014
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.5 11-03-2015 - Se adapata metodo para que pueda mostrarlas sin importar la ruta con la que es guardada  
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.6 21-03-2016 - Se adapata metodo para que pueda mostrarlas sin importar la ruta con la que es guardada 
     *                        - .../telcos/web/...
     *                        - .../public/uploads/...   
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.7 08-04-2016 - Se agrega validacion para que cuando el nombre del archivo contiene la ruta completa continue con la descarga
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.8 29-04-2016 - Se especifica la ruta a leer para realizar descarga de documentos para TN
     *          
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.8 11-03-2021 - Se agrega validación que si la ruta del archivo es una url la descargue directamente.
     *
     */  
    public function descargarDocumentoAction($id)
    {
        $pathUploads = $this->container->getParameter('ruta_upload_documentos');
        $pathTelcos  = $this->container->getParameter('path_telcos');

        $objRequest             = $this->get('request'); 
        $objSession             = $objRequest->getSession(); 
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : '';       

        $emComunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");

        $objDocumento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($id);


        $objRoot = $objDocumento->getUbicacionFisicaDocumento();                
        
        //Si la imagen/archico contiene la ruta completa no concatena ninguna subruta y continua
        if(strpos($objRoot, $pathTelcos) !== false)
        {
            $strPath = $objRoot;
        }
        //Si la imagen es guardada a partir de telcos/web/... se concatena el path_telcos ya que así es como es renderizado el archivo
        //caso contrario continua la ejecucion
        else if(strpos($objRoot, $pathUploads) !== false)
        {
            $strPath = $pathTelcos . $objRoot;
        }        
        else //Cuando la ruta es tomada desde public/uploads/...
        {
            $strPath = $pathTelcos . 'telcos/web/' . $objRoot;
        }                                
        
        if($strPrefijoEmpresa=="TN" || filter_var($objRoot, FILTER_VALIDATE_URL))
        {
            $strPath = $objRoot;
        }
        
        $objContent = file_get_contents($strPath);

        $objResponse = new Response();

        $objResponse->headers->set('Content-Type', 'mime/type');
        $objResponse->headers->set('Content-Disposition', 'attachment;filename="' . $objDocumento->getUbicacionLogicaDocumento());

        $objResponse->setContent($objContent);
        return $objResponse;
    }
    
    
    /**
     * multipleFileUploadAction
     *
     * Metodo encargado de procesar el o los archivos que el usuario desea subir a los casos y tareas
     *
     * @return json con resultado del proceso
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 30-06-2015 
     * 
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 1.1 08-03-2021 - Se recibe parámetro strSubirEnMsNfs que indica si se sube el 
     *                           archivo con el microservicio nfs o en directorios locales
     * 
     */ 
    public function multipleFileUploadAction()
    {
        $objRequest     = $this->get('request');
        $objResponse    = new Response();
        $objResponse->headers->set('Content-Type', 'text/html');
        
        $servicio           = $objRequest->get('servicio') ? $objRequest->get('servicio'): 0;
        $idCaso             = $objRequest->get('Idcaso') ? $objRequest->get('Idcaso'):0;
        $idTarea            = $objRequest->get('IdTarea') ? $objRequest->get('IdTarea'):0;
        $origenCaso         = $objRequest->get('origenCaso') ? $objRequest->get('origenCaso'):"N";
        $origenTarea        = $objRequest->get('origenTarea') ? $objRequest->get('origenTarea'):"N";
        $strCodigoDocumento = $objRequest->get('codigo')? $objRequest->get('codigo'): "";
        $strSubirEnMsNfs    = $objRequest->get('subirEnMsNfs')? $objRequest->get('subirEnMsNfs'): "N";
        
        $objSession         = $objRequest->getSession();
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "";
        $strUser            = $objSession->get('user') ? $objSession->get('user') : "";
        $strIdEmpresa       = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "";
        
        $arrayArchivos      = $this->getRequest()->files->get('archivos');

        $arrayParametros     = array(
            "idCaso"                => $idCaso,
            "idTarea"               => $idTarea,
            "servicio"              => $servicio,
            "origenCaso"            => $origenCaso,
            "origenTarea"           => $origenTarea,
            "strCodigoDocumento"    => $strCodigoDocumento,
            "strPrefijoEmpresa"     => $strPrefijoEmpresa,
            "strUser"               => $strUser,
            "strIdEmpresa"          => $strIdEmpresa,
            "arrayArchivos"         => $arrayArchivos
        );

        $soporteService = $this->get('soporte.SoporteService');
        if ($strSubirEnMsNfs == 'S')
        {
            $arrayRespuesta = $soporteService->guardarMultiplesAdjuntosCasosTareasEnNfs($arrayParametros);
        }
        else
        {
            $arrayRespuesta = $soporteService->guardarMultiplesAdjuntosCasosTareas($arrayParametros);
        }
        $strResultado   = '{"success": '.$arrayRespuesta['success'].', "respuesta":"'.$arrayRespuesta['mensaje'].'"}';
        $objResponse->setContent($strResultado);
        return $objResponse;
    }
    
        
    /**
     * deleteFileAction
     *
     * Metodo encargado de eliminar el registro y archivo subido
     * 
     * @return Response $objResponse
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 22-09-2016
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 08-02-2017 Se mmodifica la función para obtener de manera correcta la ruta de las imágenes que son subidas en las incidencias
     *                         desde el móvil
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 22-09-2020 Se agrega validación para permitir eliminar archivos propios
     * 
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.3 08-12-2021 Se agrega el historial al servicio de eliminación de archivo
     * 
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.4 12-07-2022 Se agrega validación para validar getServicioId
     */
    public function eliminarDocumentoRegistroYArchivoAction()
    {
        $objResponse    = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $emComercial    = $this->getDoctrine()->getManager();
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();
        $strUserSession = $objSession->get('user');
        $strStatus      = "";
        $strMessage     = "";
        $strIpCreacion          = $objRequest->getClientIp()              ? $objRequest->getClientIp() : '127.0.0.1';
        $serviceSolicitud         = $this->get('comercial.Solicitudes');
        $serviceAutorizaciones    = $this->get('comercial.Autorizaciones');
        
        $idDocumento    = $objRequest->get('id');
        
        $emComunicacion->getConnection()->beginTransaction();
        $emComercial->getConnection()->beginTransaction();
        try
        {
            $objDocumento           = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($idDocumento);
            if (!$objDocumento) 
            {
                throw $this->createNotFoundException('No se ha podido encontrar el documento solicitado');
            }
            
            $objDocumentoRelacion   = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')->findOneByDocumentoId($idDocumento);
            if($objDocumentoRelacion->getServicioId())
            {
                $entityServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($objDocumentoRelacion->getServicioId());
            }
            if (!$objDocumentoRelacion) 
            {
                throw $this->createNotFoundException('No se ha podido encontrar la relación del documento con la tarea');
            }
            //Se valida si el usuario del documento es diferente al de sesión
            if ($strUserSession != $objDocumento->getUsrCreacion())
            {
                throw $this->createNotFoundException('No se ha podido eliminar archivo, solo puede eliminar archivos propios');
            }
            $strUbicacionFisica     = $objDocumento->getUbicacionFisicaDocumento();
            $objDocumento->setEstado("Eliminado");
            $objDocumento->setFeCreacion(new \DateTime('now'));
            $objDocumento->setUsrCreacion($strUserSession);
            $emComunicacion->persist($objDocumento);
            $emComunicacion->flush();
            
            $objDocumentoRelacion->setEstado('Eliminado');
            $emComunicacion->persist($objDocumentoRelacion);
            $emComunicacion->flush();
            
            // GUARDAR INFO SERVICIO HISTORIAL
            if(is_object($entityServicio))
            {
                $strSeguimientoDocumento = 'Se ha eliminado correctamente un archivo';
                $strAccion               = 'eliminarEvidencia';
                $arrayParametrosTraServ  = array(
                                            "emComercial"                => $emComercial,
                                            "strClienteIp"               => $strIpCreacion,
                                            "objServicio"                => $entityServicio,
                                            "strSeguimiento"             => $strSeguimientoDocumento,
                                            "strUsrCreacion"             => $strUserSession,
                                            "strAccion"                  => $strAccion );
                $arrayVerificar = $serviceAutorizaciones->registroTrazabilidadDelServicio(
                                $arrayParametrosTraServ);
                if($arrayVerificar['status'] == 'ERROR' )
                {
                throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroTrazabilidadDelServicio
                                        <br> <b>'.$arrayVerificar['mensaje'].'</b>');
                }
            }

            $strPathTelcos  = $this->container->getParameter('path_telcos')."telcos/web";
            
            if(strrpos($strUbicacionFisica, $strPathTelcos) === false) 
            {
                $strUbicacionFisica   = $strPathTelcos."/".$strUbicacionFisica;
            }
            unlink($strUbicacionFisica);
            
            $strMessage .= "Se ha eliminado correctamente el archivo!";
            $strStatus  .= "OK";
            
            $emComunicacion->commit();
            $emComercial->getConnection()->commit();
        } 
        catch (\Exception $e) 
        {
            if($emComunicacion->getConnection()->isTransactionActive())
            {
                $emComunicacion->rollback();
                $emComunicacion->close();

                $emComercial->rollback();
                $emComercial->close();
            }

            $strMessage .= "Ha ocurrido un problema. <br/>Por favor informe a Sistemas.";
            if (strrpos($e->getMessage(),'solo puede eliminar archivos propios'))
            {
                $strMessage = $e->getMessage();
            }
            error_log($strMessage);
        }
        
        $strResultado    = '{"status":"'.$strStatus.'","message":"'.$strMessage.'"}';
        $objResponse->setContent($strResultado); 
        return $objResponse;
    }
    
    

}