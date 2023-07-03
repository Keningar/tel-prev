<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiPlantilla;
use telconet\schemaBundle\Entity\InfoAliasPlantilla;

use telconet\schemaBundle\Form\AdmiPlantillaType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

use telconet\administracionBundle\Service\PlantillaService;

class AdmiPlantillaDocumentosController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_475-8437")
    */
    public function indexAction()
    {	

        return $this->render('administracionBundle:AdmiPlantillaDocumentos:index.html.twig');

    }
    
     /**
     * @Secure(roles="ROLE_475-8437")
     * gridPromocionesAction()
     * Función que obtiene un listado de planillas, por medio de los siguientes filtros: Código Plantilla,  Fecha Creación, Usuario Creación.
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 05-12-2021
     * @since 1.0
     *
     * @return $objResponse - Listado de Plantillas
     */
    public function gridPlantillasAction()
    {

        $servicePlantilla          = $this->get('administracion.Plantilla');
        $arrayPlantillas = array();

        $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas = $serviceTokenCas->generarTokenCas();

        $arrayParametrosPlantilla = array('token' => $arrayTokenCas['strToken']);

        $arrayPlantillasResponse = $servicePlantilla->consultarPlantillasMs($arrayParametrosPlantilla);
        
        foreach($arrayPlantillasResponse['objData'] as $objPlantilla)
        {
                $arrayPlantillas[]    = array(  'intIdPlantilla'         => $objPlantilla['id'],
                                                'strCodigoPlantilla'         => $objPlantilla['codigoPlantilla'],
                                                'strdescripcion'     => $objPlantilla['descripcion'],
                                                'strFechaCreacion' => $objPlantilla['fechaCreacion'],
                                                'strUsrCreacion'         => $objPlantilla['usuarioCreacion'],
                                                'strAcciones'            => array('linkVer' => $this->generateUrl('plantilla_detalle', 
                                                                                    array('strCodigoPlantilla' => $objPlantilla['codigoPlantilla'])), 
                                                                                  'linkEditar' => $this->generateUrl('plantilla_editar', 
                                                                                    array('strCodigoPlantilla' => $objPlantilla['codigoPlantilla'])),
                                                                                  'linkEliminar' => $this->generateUrl('plantilla_eliminar', 
                                                                                  array('strCodigoPlantilla' => $objPlantilla['codigoPlantilla'])),
                                                                                  'linkDescargar' => $this->generateUrl('plantilla_descargar', 
                                                                                  array('strCodigoPlantilla' => $objPlantilla['codigoPlantilla'])))
                                                );
        }
        

        $objResponse = new Response(json_encode(array( 'data' => $arrayPlantillas)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
	
    /**
     * @Secure(roles="ROLE_475-8437")
     * detalleAction()
     * Función que obtiene un detalle de planillas, por medio de los siguientes filtros: Código Plantilla,  Fecha Creación, Usuario Creación.
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 05-12-2021
     * @since 1.0
     *
     * @return $objResponse - detalle de Plantillas
     */
    public function detalleAction($strCodigoPlantilla)
    {
        $servicePlantilla          = $this->get('administracion.Plantilla');
        $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas = $serviceTokenCas->generarTokenCas();

        $arrayParametrosPlantilla = array('token' => $arrayTokenCas['strToken'],
                                          'codigoPlantilla' => $strCodigoPlantilla);

        $objPlantillaResponse = $servicePlantilla->consultarPlantillaMs($arrayParametrosPlantilla);
        

        return $this->render('administracionBundle:AdmiPlantillaDocumentos:detallePlantilla.html.twig',
        array('objPlantilla' => $objPlantillaResponse));
    }

    /**
     * @Secure(roles="ROLE_475-8437")
     * editarAction()
     * Función que edita una plantilla
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 05-12-2021
     * @since 1.0
     *
     * @return -modal - plantilla editada
     */
    public function editarAction($strCodigoPlantilla)
    {
        $servicePlantilla          = $this->get('administracion.Plantilla');
        $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas = $serviceTokenCas->generarTokenCas();

        $arrayParametrosPlantilla = array('token' => $arrayTokenCas['strToken'],
                                          'codigoPlantilla' => $strCodigoPlantilla);

        $objPlantillaResponse = $servicePlantilla->consultarPlantillaMs($arrayParametrosPlantilla);
        

        return $this->render('administracionBundle:AdmiPlantillaDocumentos:editarPlantilla.html.twig',
        array('objPlantilla' => $objPlantillaResponse));
    }

    
    /**
     * @Secure(roles="ROLE_475-8437")
     * eliminarAction()
     * Función que elimina una plantilla
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 05-12-2021
     * @since 1.0
     *
     * @return -modal - plantilla eliminada
     */
    public function eliminarAction($strCodigoPlantilla)
    {
        $servicePlantilla          = $this->get('administracion.Plantilla');
        $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas = $serviceTokenCas->generarTokenCas();

        $arrayParametrosPlantilla = array('token' => $arrayTokenCas['strToken'],
                                          'codigoPlantilla' => $strCodigoPlantilla);

        $objPlantillaResponse = $servicePlantilla->consultarPlantillaMs($arrayParametrosPlantilla);
        

        return $this->render('administracionBundle:AdmiPlantillaDocumentos:eliminarPlantilla.html.twig',
        array('objPlantilla' => $objPlantillaResponse));
    }

    /**
     * @Secure(roles="ROLE_475-8437")
     * descargarAction()
     * Función que obtiene descarga de plantilla
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 05-12-2021
     * @since 1.0
     *
     * @return -modal - plantilla a descargar
     */
    public function descargarAction($strCodigoPlantilla)
    {
        $servicePlantilla          = $this->get('administracion.Plantilla');
        $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas = $serviceTokenCas->generarTokenCas();

        $arrayParametrosPlantilla = array('token' => $arrayTokenCas['strToken'],
                                          'codigoPlantilla' => $strCodigoPlantilla);

        $objPlantillaResponse = $servicePlantilla->consultarPlantillaMs($arrayParametrosPlantilla);
        

        return $this->render('administracionBundle:AdmiPlantillaDocumentos:descargarPlantilla.html.twig',
        array('objPlantilla' => $objPlantillaResponse));
    }

    /**
     * @Secure(roles="ROLE_475-8437")
     * crearAction()
     * Función que abre modal para crear plantilla
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 05-12-2021
     * @since 1.0
     
     * @return -modal - plantilla a crear
     */
    public function crearAction()
    {
        
        return $this->render('administracionBundle:AdmiPlantillaDocumentos:crearPlantilla.html.twig');

    }

    /**
     * @Secure(roles="ROLE_475-8437")
     * crearPlantillaAction()
     * Función que crea plantilla
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 05-12-2021
     * @since 1.0
     *
     * @return $objResponse - plantilla creada
     */
    public function crearPlantillaAction()
    {   $objRequest           = $this->getRequest();
        $objSesion    = $objRequest->getSession();
        $servicePlantilla          = $this->get('administracion.Plantilla');
        $objPlantillasResponse;
        $serviceTokenCas = $this->get('seguridad.TokenCas');
        $arrayTokenCas = $serviceTokenCas->generarTokenCas();
        $strPlantilla   = $objRequest->get('strPlantilla'); 
        $strCodigoPlantilla               = $objRequest->get('strCodigoPlantilla'); 
        $strDescripcion                = $objRequest->get('strDescripcion');   
        $strUsrCreacion           = $objSesion->get('user');                    
        $arrayParametros          = array(
                                          'codigoPlantilla'   => $strCodigoPlantilla,
                                          'descripcion'            => $strDescripcion,
                                          'plantilla'         => $strPlantilla,
                                          'usuarioCreacion'         => $strUsrCreacion,
                                          'token'               => $arrayTokenCas['strToken']
                                    ); 
        $objPlantillasResponse = $servicePlantilla->crearPlantillaMs($arrayParametros);
        

        $objResponse = new Response(json_encode(array('objData' => $objPlantillasResponse['objData'],
        'strStatus' => $objPlantillasResponse['strStatus'],
        'strMensaje' => $objPlantillasResponse['strMensaje'])));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_475-8437")
     * editarPlantillaAction()
     * Función que edita plantilla
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 05-12-2021
     * @since 1.0
     *
     * @return $objResponse - plantilla editada
     */
    public function editarPlantillaAction()
    {   $objRequest           = $this->getRequest();
        $objSesion    = $objRequest->getSession();
        $servicePlantilla          = $this->get('administracion.Plantilla');
        $objPlantillasResponse;
        $serviceTokenCas = $this->get('seguridad.TokenCas');
        $arrayTokenCas = $serviceTokenCas->generarTokenCas();

        
        $strPlantilla   = $objRequest->get('strPlantilla'); 
        $strCodigoPlantilla               = $objRequest->get('strCodigoPlantilla'); 
        $strDescripcion                = $objRequest->get('strDescripcion');   
        $intIdPlantilla                = $objRequest->get('intIdPlantilla'); 

        $strUsrCreacion           = $objSesion->get('user');                    
        $arrayParametros          = array('id'=> $intIdPlantilla,
                                          'codigoPlantilla'   => $strCodigoPlantilla,
                                          'descripcion'            => $strDescripcion,
                                          'plantilla'         => $strPlantilla,
                                          'usuarioCreacion'         => $strUsrCreacion,
                                          'token'               => $arrayTokenCas['strToken']
                                    ); 
        $objPlantillasResponse = $servicePlantilla->editarPlantillaMs($arrayParametros);
        

        $objResponse = new Response(json_encode(array('objData' => $objPlantillasResponse['objData'],
        'strStatus' => $objPlantillasResponse['strStatus'],
        'strMensaje' => $objPlantillasResponse['strMensaje'])));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

     /**
     * @Secure(roles="ROLE_475-8437")
     * eliminarPlantillaAction()
     * Función que elimina plantilla
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 05-12-2021
     * @since 1.0
     *
     * @return $objResponse - plantilla eliminada
     */
    public function eliminarPlantillaAction()
    {   $objRequest           = $this->getRequest();
        $objSesion    = $objRequest->getSession();
        $servicePlantilla          = $this->get('administracion.Plantilla');
        $objPlantillasResponse;
        $serviceTokenCas = $this->get('seguridad.TokenCas');
        $arrayTokenCas = $serviceTokenCas->generarTokenCas();

        $strCodigoPlantilla               = $objRequest->get('strCodigoPlantilla'); 
        $strDescripcion                = $objRequest->get('strDescripcion');   

        $strUsrCreacion           = $objSesion->get('user');                    
        $arrayParametros          = array(
                                          'codigoPlantilla'   => $strCodigoPlantilla,
                                          'descripcion'            => $strDescripcion,
                                          'usuarioCreacion'         => $strUsrCreacion,
                                          'token'               => $arrayTokenCas['strToken']
                                    ); 
        $objPlantillasResponse = $servicePlantilla->eliminarPlantillaMs($arrayParametros);
        

        $objResponse = new Response(json_encode(array('objData' => $objPlantillasResponse['objData'],
        'strStatus' => $objPlantillasResponse['strStatus'],
        'strMensaje' => $objPlantillasResponse['strMensaje'])));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

/**
     * @Secure(roles="ROLE_475-8437")
     * descargarPlantillaAction()
     * Función que obtiene un archivo
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 05-12-2021
     * @since 1.0
     *
     * @return $objResponse - objeto a descargar
     */
    public function descargarPlantillaAction()
    {   $objRequest           = $this->getRequest();
        $objSesion    = $objRequest->getSession();
        $servicePlantilla          = $this->get('administracion.Plantilla');
        $objPlantillasResponse;
        $objConvertDocsResponse;
        
        $serviceTokenCas = $this->get('seguridad.TokenCas');
        $arrayTokenCas = $serviceTokenCas->generarTokenCas();

        $arrayParametros = array();

        $strPlantilla   = $objRequest->get('strPlantilla'); 
        $strCodigoPlantilla               = $objRequest->get('strCodigoPlantilla'); 
        $strContrato                = $objRequest->get('strContrato');      
    
        $arrayParametros['codigoPlantilla'] = $strCodigoPlantilla;
        $arrayParametros['token'] = $arrayTokenCas['strToken'];
        if($strContrato != '')
        {
            $arrayParametros['propiedades'] = json_decode($strContrato);
         
        }else
        {
            $arrayParametros['propiedades'] = array();

         
        }
        $objPlantillasResponse = $servicePlantilla->usarPlantillaMs($arrayParametros);
        if($objPlantillasResponse['strStatus']===0)
        {
            $arrayParametros['html'] = $objPlantillasResponse['objData']['resultadoTemplate'];
            $objConvertDocsResponse = $servicePlantilla->convertDocsMs($arrayParametros);
        }else
        {
            $arrayParametros['html'] = $strPlantilla;
            $objConvertDocsResponse = $servicePlantilla->convertDocsMs($arrayParametros);
        }

        $objResponse = new Response(json_encode(array('objData' => $objConvertDocsResponse['objData'],
        'strStatus' => $objConvertDocsResponse['strStatus'],
        'strMensaje' => $objConvertDocsResponse['strMensaje'])));
        $objResponse->headers->set('Content-type', 'text/json');

       
        return $objResponse;
    }
    
}
