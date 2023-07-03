<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class FirmaController extends Controller
{ 
    /**
     * @Secure(roles="ROLE_338-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     * Redirección a la pantalla principal de la administracion de las firmas
     * @return render.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 15-07-2016
     *
     */
    public function indexAction()
    {
        $arrayRolesPermitidos   = array();
        $emSeguridad            = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu         = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("338", "1");

        //MODULO 338 - firma/upload
        if(true === $this->get('security.context')->isGranted('ROLE_338-3'))
        {
            $arrayRolesPermitidos[] = 'ROLE_338-3';
        }
        //MODULO 338 - firma/show
        if (true === $this->get('security.context')->isGranted('ROLE_338-6'))
        {
            $arrayRolesPermitidos[] = 'ROLE_338-6';
        }
        //MODULO 338 - firma/delete
        if (true === $this->get('security.context')->isGranted('ROLE_338-8'))
        {
            $arrayRolesPermitidos[] = 'ROLE_338-8';
        }

        return $this->render('administracionBundle:Firma:index.html.twig', array(
                                                                                    'item'            => $entityItemMenu,
                                                                                    'rolesPermitidos' => $arrayRolesPermitidos
        ));
    }

    /**
     * @Secure(roles="ROLE_338-7")
     * 
     * Documentación para el método 'gridAction'.
     *
     * Llena el grid de consulta de los Talleres
     * @return render.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 17-07-2016
     *
     */
    public function gridAction()
    {
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $objResponse        = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();

        $strNombres         = $objRequest->query->get('nombres') ? trim($objRequest->query->get('nombres')) : "";
        $strApellidos       = $objRequest->query->get('apellidos') ? trim($objRequest->query->get('apellidos')) : "";
        $strIdentificacion  = $objRequest->query->get('identificacion') ? trim($objRequest->query->get('identificacion')) : "";
        $strEstado          = $objRequest->query->get('estado') ? trim($objRequest->query->get('estado')) : "";

        $intIdDepartamento  = $objSession->get('idDepartamento');
        $intIdCanton        = $objRequest->query->get('canton') ? trim($objRequest->query->get('canton')) : '';
        $intStart           = $objRequest->query->get('start') ? trim($objRequest->query->get('start')) : 0;
        $intLimit           = $objRequest->query->get('limit') ? trim($objRequest->query->get('limit')) : 0;;
        $idOficina          = $objSession->get('idOficina') ? $objSession->get('idOficina') : '';
        $objOficina         = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($idOficina);
        $strRegion          = '';
        if($objOficina)
        {
            $objCanton      = $emGeneral->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());
            $strRegion      = $objCanton ? $objCanton->getRegion() : '';
        }

        $session      = $this->get( 'session' ); 
        $intIdEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : ""); 
        $objTipoDocumentoGeneral= $emGeneral->getRepository("schemaBundle:AdmiTipoDocumentoGeneral")->findOneByCodigoTipoDocumento('FIRMA');
        $idTipoDocumentoGeneral = $objTipoDocumentoGeneral->getId();

        $arrayParametros = array(
                                    'empresaCod'                => $intIdEmpresa,
                                    'intStart'                  => $intStart,
                                    'intLimit'                  => $intLimit,
                                    'tipoDocumentoGeneralId'    => $idTipoDocumentoGeneral,
                                    'criterios'                 => array(   'nombres'        => $strNombres, 
                                                                            'apellidos'      => $strApellidos,
                                                                            'identificacion' => $strIdentificacion,
                                                                            'estado'         => $strEstado,
                                                                            'departamento'   => $intIdDepartamento,
                                                                            'canton'         => $intIdCanton,
                                                                            'region'         => $strRegion
                                                                          )
                                );
        $objJson = $this->getDoctrine()->getManager()->getRepository('schemaBundle:InfoPersona')->getJSONDocumentosPersona($arrayParametros);
        $objResponse->setContent($objJson);

        return $objResponse;
    }
	    
    /**
     * @Secure(roles="ROLE_338-3")
     * 
     * Documentación para el método 'uploadAction'.
     *
     * Subir una firma
     * @return render.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 28-07-2016
     *
     */
    public function uploadAction()
    {
        $objResponse                = new Response();
        $objResponse->headers->set('Content-Type', 'text/html');
        $objRequest                 = $this->get('request');
        $intClientIp                = $objRequest->getClientIp();
        $objSession                 = $objRequest->getSession();
        $strUsrCreacion             = $objSession->get('user');
        $idPersonaEmpresaRol        = $objRequest->get('idPersonaEmpresaRol');
        
        $emGeneral                  = $this->getDoctrine()->getManager('telconet_general');
        $objTipoDocumentoGeneral    = $emGeneral->getRepository("schemaBundle:AdmiTipoDocumentoGeneral")->findOneByCodigoTipoDocumento('FIRMA');
        $idTipoDocumentoGeneral     = $objTipoDocumentoGeneral->getId();
        $arrayTipoDocumentos        = array ();
        $arrayFechasHastaDocumentos = array ();
        
        $datos_form_files                   = $objRequest->files;
        $arrayTipoDocumentos['archivo']     = $idTipoDocumentoGeneral;
        
        $datos_form = array(
            'datos_form_files'              => array($datos_form_files),
            'arrayTipoDocumentos'           => $arrayTipoDocumentos,
            'arrayFechasHastaDocumentos'    => $arrayFechasHastaDocumentos
        ); 
        
        try
        {
            /* @var $servicePersonaEmpleado \telconet\administracionBundle\Service\PersonaEmpleadoService */
            $servicePersonaEmpleado = $this->get('administracion.PersonaEmpleado');
            //retorna un objInfoDocumentoRelacion
            $entity                 = $servicePersonaEmpleado->guardarArchivoDigital($idPersonaEmpresaRol,$strUsrCreacion,$intClientIp,$datos_form);
            $boolSuccess            = true;
            $mensaje                = "Su Archivo se subió exitosamente";
            
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());
            $boolSuccess    = false;
            $mensaje        = "Ha ocurrido un error, por favor reporte a Sistemas";
        }
        
        $resultado = json_encode(array('success' => $boolSuccess, 'mensaje' => $mensaje));
        
        $objResponse->setContent($resultado);
        return $objResponse;
        
    }
    
    /**
     * @Secure(roles="ROLE_338-8")
     * 
     * Documentación para el método 'deleteAction'.
     *
     * Elimina una firma
     * @return render.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 28-07-2016
     *
     */
    public function deleteAction()
    {
        $objResponse        = new Response();
        $objResponse->headers->set('Content-Type', 'text/html');
        $objRequest         = $this->get('request');
        $id                 = $objRequest->get('idDocumento');
           
        try
        {
            /* @var $servicePersonaEmpleado \telconet\administracionBundle\Service\PersonaEmpleadoService */
            $servicePersonaEmpleado = $this->get('administracion.PersonaEmpleado');
            $entity                 = $servicePersonaEmpleado->eliminarDocumento($id);
            $boolSuccess            = true;
            $mensaje                = "Se ha eliminado el archivo exitosamente";

        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());
            $boolSuccess    = false;
            $mensaje        = "Ha ocurrido un error, por favor reporte a Sistemas";
            
        }
        
        $resultado = json_encode(array('success' => $boolSuccess, 'mensaje' => $mensaje));
        $objResponse->setContent($resultado);
        return $objResponse;
    }

}