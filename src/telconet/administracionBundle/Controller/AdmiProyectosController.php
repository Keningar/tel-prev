<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use telconet\schemaBundle\Entity\admiProyectos;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Service\UtilService;


class AdmiProyectosController extends Controller implements TokenAuthenticatedController
{ 
   /**
     * @Secure(roles="ROLE_466-1")
     *
     * Documentación para la función 'indexAction'.
     *
     * Función que carga la pantalla de Administración de proyectos.
     *
     * @return render Redirecciona al index de la opción.
     *
     * @author Byron Anton <banton@telconet.ec>
     * @version 1.0 11-05-2021
     *
     */
    public function indexAction()
    {
       try
       {
           $objRequest           = $this->getRequest();
           $strUsrCreacion       = $objRequest->getSession()->get('user');
           $strIpCreacion        = $objRequest->getClientIp();
           $serviceUtil          = $this->get('schema.Util');
           $arrayRolesPermitidos = array();

           if( $this->get('security.context')->isGranted('ROLE_466-1') )
           {
               $arrayRolesPermitidos[] = 'ROLE_466-1';
           }
           if( $this->get('security.context')->isGranted('ROLE_466-6') )
           {
               $arrayRolesPermitidos[] = 'ROLE_466-6';
           }
           if( $this->get('security.context')->isGranted('ROLE_466-4') )
           {
               $arrayRolesPermitidos[] = 'ROLE_466-4';
           }
           if( $this->get('security.context')->isGranted('ROLE_466-8') )
           {
               $arrayRolesPermitidos[] = 'ROLE_466-8';
           }
           if( $this->get('security.context')->isGranted('ROLE_466-9') )
           {
               $arrayRolesPermitidos[] = 'ROLE_466-9';
           }
       }
       catch(\Exception $e)
       {
           $serviceUtil->insertError('TelcoS+', 'AdmiProyectosController.indexAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
       }
       return $this->render('administracionBundle:AdmiProyectos:index.html.twig', array('rolesPermitidos'  => $arrayRolesPermitidos));
    }

   /**
     * @Secure(roles="ROLE_466-7")
     *
     * Documentación para la función 'gridAction'.
     *
     * Función que retorna el listado de proyectos.
     *
     * @return $objResponse - Listado de proyectos.
     *
     * @author Byron Anton <banton@telconet.ec>
     * @version 1.0 11-05-2021
     *
     */
    public function gridAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strNombreProyecto      = $objRequest->get("strNombreProyecto")   ? $objRequest->get("strNombreProyecto"):"";
        $strFechaInicio         = $objRequest->get("strFechaInicio")      ? $objRequest->get("strFechaInicio"):"";
        $strFechaFin            = $objRequest->get("strFechaFin")         ? $objRequest->get("strFechaFin"):"";
        $strEstado              = $objRequest->get("strEstado")           ? $objRequest->get("strEstado"):"";
        $intIdEmpresa           = $objSession->get('idEmpresa')           ? $objSession->get('idEmpresa'):"";
        $strUsrCreacion         = $objSession->get('user')                ? $objSession->get('user'):"";
        $strIpCreacion          = $objRequest->getClientIp()              ? $objRequest->getClientIp():'127.0.0.1';
        $serviceUtil            = $this->get('schema.Util');
        $intTotal               = 0;
        $arrayProyecto         = array();
        try
        {

            $arrayParametros                      = array();
            $arrayParametros['strNombreProyecto'] = $strNombreProyecto;
            $arrayParametros['strFechaInicio']    = $strFechaInicio;
            $arrayParametros['strFechaFin']       = $strFechaFin;
            $arrayParametros['strEstado']         = $strEstado;
            $arrayParametros['intIdEmpresa']      = $intIdEmpresa;
            $arrayResultado                       = $this->getDoctrine()->getManager("telconet_naf")
                                                         ->getRepository('schemaBundle:admiProyectos')
                                                         ->getProyectos($arrayParametros);

            if(isset($arrayResultado['error']) && !empty($arrayResultado['error']))
            {
                throw new \Exception($arrayResultado['error']);
            }

            if(!empty($arrayResultado["registros"]) && isset($arrayResultado["registros"]))
            {
                $arrayRegistros   = $arrayResultado['registros'];
                $intTotal         = $arrayResultado['total'];
                foreach($arrayRegistros as $arrayDatos)
                {
                    $arrayDataLink    = array('intIdProyecto'  => $arrayDatos["ID_PROYECTO"],
                                              'strEstado'      => $arrayDatos["ESTADO"]);
                    $arrayDataLinkVer = array('intIdProyecto'  => $arrayDatos["ID_PROYECTO"]);
                    $strLinkVer       = array('linkVer'=> $this->generateUrl('com_admiProyectos_show',$arrayDataLink),
                                              'linkEditar'=> $this->generateUrl('com_admiProyectos_edit',$arrayDataLinkVer));
                    
                    $strLinkEditar    = array('linkEditar'=> $this->generateUrl('com_admiProyectos_edit',$arrayDataLinkVer));
                    $arrayProyecto[] = array('intIdProyecto'       => $arrayDatos["ID_PROYECTO"],
                                              'strNombre'           => $arrayDatos["NOMBRE"],
                                              'intIdResponsable'    => $arrayDatos["RESPONSABLE_ID"],
                                              'strTipoContabilidad' => $arrayDatos["TIPO_CONTABILIDAD"],
                                              'intIdCuenta'         => $arrayDatos["CUENTA_ID"],
                                              'strEstado'           => $arrayDatos["ESTADO"],
                                              'strFeInicio'           => $arrayDatos["FE_INICIO"],
                                              'strFeFin'           => $arrayDatos["FE_FIN"],
                                              'strNombrePer'           => $arrayDatos["NOMBRE_PERSONA"],
                                              'strAcciones'         => $strLinkVer);
                }
            }
        }
        catch(\Exception $e)
        {
            echo $e->getMessage();
            $serviceUtil->insertError('TelcoS+', 'AdmiProyectosController.gridAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        $objResponse = new Response(json_encode(array('intTotal' => $intTotal, 'data' => $arrayProyecto)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

   /**
     * @Secure(roles="ROLE_466-6")
     *
     * Documentación para la función 'showAction'.
     *
     * Función que renderiza la página de Ver detalle.
     *
     * @param int $intIdProyecto  => id del proyecto.
     * @param string $strEstado    => estado del proyecto.
     *
     * @return render - Página de Ver Proyecto.
     *
     * @author Byron Anton <banton@telconet.ec>
     * @version 1.0 11-05-2021
     *
     */
    public function showAction($intIdProyecto,$strEstado)
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strUsrCreacion         = $objSession->get('user')           ? $objSession->get('user'):"";
        $strIpCreacion          = $objRequest->getClientIp()         ? $objRequest->getClientIp():'127.0.0.1';
        $intIdEmpresa           = $objSession->get('idEmpresa')      ? $objSession->get('idEmpresa'):"";
        $serviceUtil            = $this->get('schema.Util');
        try
        {
            if(empty($intIdProyecto))
            {
                throw new \Exception("El identificador es un parámetro obligatorio.");
            }
            $arrayProyecto                    = array();
            $arrayParametros                  = array();
            $arrayParametros['intIdProyecto'] = $intIdProyecto;
            $arrayParametros['strEstado']     = $strEstado;
            $arrayParametros['intIdEmpresa']  = $intIdEmpresa;
            $arrayResultado                   = $this->getDoctrine()->getManager("telconet_naf")
                                                     ->getRepository('schemaBundle:admiProyectos')
                                                     ->getProyectos($arrayParametros);
            if(isset($arrayResultado['error']) && !empty($arrayResultado['error']))
            {
                throw new \Exception($arrayResultado['error']);
            }
            if(!empty($arrayResultado["registros"])&&isset($arrayResultado["registros"]))
            {
                $arrayRegistros = $arrayResultado['registros'];
                foreach($arrayRegistros as $arrayDatos)
                {
                    $arrayProyecto = array('intIdProyecto'       => $arrayDatos["ID_PROYECTO"],
                                           'strNombre'           => $arrayDatos["NOMBRE"],
                                           'intIdResponsable'    => $arrayDatos["RESPONSABLE_ID"],
                                           'strTipoContabilidad' => $arrayDatos["TIPO_CONTABILIDAD"],
                                           'intIdCuenta'         => $arrayDatos["CUENTA_ID"],
                                           'strEstado'           => $arrayDatos["ESTADO"],
                                           'strFeInicio'           => $arrayDatos["FE_INICIO"],
                                           'strFeFin'           => $arrayDatos["FE_FIN"],
                                           'strUsrCreacion'      => $arrayDatos["USR_CREACION"],
                                           'strFeCreacion'       => $arrayDatos["FE_CREACION"],
                                           'strNombrePer'           => $arrayDatos["NOMBRE_PERSONA"]);
                }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('TelcoS+', 'AdmiProyectosController.showAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        return $this->render('administracionBundle:AdmiProyectos:show.html.twig',array('arrayProyectoDet' => $arrayProyecto));
    }

    /**
     * Documentación para la función 'newAction'.
     *
     * Función que crea los proyectos.
     *
     * @return Response - Mensaje de exito.
     *
     * @author Byron Anton <banton@telconet.ec>
     * @version 1.0 11-05-2021
     *
     */
    public function newAction()
    {
        try
        {
            $objRequest             = $this->getRequest();
            $objSession             = $objRequest->getSession();
            $strUsrCreacion         = $objSession->get('user')                ? $objSession->get('user'):"";
            $strIpCreacion          = $objRequest->getClientIp()              ? $objRequest->getClientIp():'127.0.0.1';
            $strTipoContabilidad    = $objRequest->get("strTipoContabilidad") ? $objRequest->get("strTipoContabilidad"):"";
            $strNombreProyecto      = $objRequest->get("strNombreProyecto")   ? $objRequest->get("strNombreProyecto"):"";
            $strResponsable         = $objRequest->get("strResponsable")      ? $objRequest->get("strResponsable"):"";
            $strFechaInicioCrear    = $objRequest->get("strFechaInicioCrear")      ? $objRequest->get("strFechaInicioCrear"):"";
            $strFechaFinCrear       = $objRequest->get("strFechaFinCrear")      ? $objRequest->get("strFechaFinCrear"):"";
            $intIdEmpresa           = $objSession->get('idEmpresa')           ? $objSession->get('idEmpresa'):"";
            $serviceUtil            = $this->get('schema.Util');
            $emNaf                  = $this->getDoctrine()->getManager("telconet_naf");
            $strCodigoError         = "";
            $strEstado              = 'Activo';
            if(empty($strTipoContabilidad) || empty($strNombreProyecto) || empty($strResponsable))
            {
                $strCodigoError = "204";
                throw new \Exception("Estimado usuario, debe llenar todos los campos para crear la solicitud.");
            }
            
            if($strFechaFinCrear < $strFechaInicioCrear)
            {
                $strCodigoError = "204";
                throw new \Exception("Estimado usuario, la fecha inicio debe ser menor a la fecha fin");
            }

            $emNaf->getConnection()->beginTransaction();
            $objProyecto= new AdmiProyectos();
            $objProyecto->setNombre($strNombreProyecto);
            $objProyecto->setResponsableId($strResponsable);
            $objProyecto->setTipoContabilidad($strTipoContabilidad);
            $objProyecto->setNoCia($intIdEmpresa);
            $objProyecto->setFeCreacion(new \DateTime('now'));
            $objProyecto->setFeInicio(new \DateTime($strFechaInicioCrear));
            $objProyecto->setFeFin(new \DateTime($strFechaFinCrear));
            $objProyecto->setUsr_creacion($strUsrCreacion);
            $objProyecto->setEstado($strEstado);
            $emNaf->persist($objProyecto);
            $emNaf->flush();
            if($emNaf->getConnection()->isTransactionActive())
            {
                $emNaf->getConnection()->commit();
                $emNaf->getConnection()->close();
            }
            $strResponse = "Acción ejecutada correctamente.";
        }
        catch(\Exception $e)
        {
            if( $emNaf->getConnection()->isTransactionActive() )
            {
                $emNaf->getConnection()->rollback();
                $emNaf->getConnection()->close();
            }
            $strResponse = "Ocurrió un error al ejecutar la acción, por favor comuniquese con Sistemas";
            if($strCodigoError == "204")
            {
                $strResponse = $e->getMessage();
            }
            $serviceUtil->insertError('TelcoS+', 
                                      'AdmiProyectosController.newAction', 
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        return new Response($strResponse);
    }
    
    /**
     * @Secure(roles="ROLE_466-4")
     *
     * Documentación para la función 'showAction'.
     *
     * Función que renderiza la página de Ver detalle.
     *
     * @param int $intIdProyecto  => id del proyecto.
     * @param string $strEstado    => estado del proyecto.
     *
     * @return render - Página de Ver Proyecto.
     *
     * @author Byron Anton <banton@telconet.ec>
     * @version 1.0 11-05-2021
     *
     */
    
    
    public function editAction($intIdProyecto)
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strUsrCreacion         = $objSession->get('user')           ? $objSession->get('user'):"";
        $strIpCreacion          = $objRequest->getClientIp()         ? $objRequest->getClientIp():'127.0.0.1';
        $intIdEmpresa           = $objSession->get('idEmpresa')      ? $objSession->get('idEmpresa'):"";
        $serviceUtil            = $this->get('schema.Util');
        try
        {
            if(empty($intIdProyecto))
            {
                throw new \Exception("El identificador es un parámetro obligatorio.");
            }
            $arrayProyecto                    = array();
            $arrayParametros                  = array();
            $arrayParametros['intIdProyecto'] = $intIdProyecto;
            $arrayParametros['strEstado']     = '';
            $arrayParametros['intIdEmpresa']  = $intIdEmpresa;
            $arrayResultado                   = $this->getDoctrine()->getManager("telconet_naf")
                                                     ->getRepository('schemaBundle:admiProyectos')
                                                     ->getProyectos($arrayParametros);
            if(isset($arrayResultado['error']) && !empty($arrayResultado['error']))
            {
                throw new \Exception($arrayResultado['error']);
            }
            if(!empty($arrayResultado["registros"])&&isset($arrayResultado["registros"]))
            {
                $arrayRegistros = $arrayResultado['registros'];
                foreach($arrayRegistros as $arrayDatos)
                {
                    $arrayProyecto = array('intIdProyecto'       => $arrayDatos["ID_PROYECTO"],
                                           'strNombre'           => $arrayDatos["NOMBRE"],
                                           'intIdResponsable'    => $arrayDatos["RESPONSABLE_ID"],
                                           'strTipoContabilidad' => $arrayDatos["TIPO_CONTABILIDAD"],
                                           'intIdCuenta'         => $arrayDatos["CUENTA_ID"],
                                           'strEstado'           => $arrayDatos["ESTADO"],
                                           'strFeInicio'           => $arrayDatos["FE_INICIO"],
                                           'strFeFin'           => $arrayDatos["FE_FIN"],
                                           'strUsrCreacion'      => $arrayDatos["USR_CREACION"],
                                           'strFeCreacion'       => $arrayDatos["FE_CREACION"],
                                           'strNombrePer'        => $arrayDatos["NOMBRE_PERSONA"]);
                }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('TelcoS+', 'AdmiProyectosController.editAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        return $this->render('administracionBundle:AdmiProyectos:edit.html.twig',array('arrayProyectoDet' => $arrayProyecto));
    }
    
    /**
    * @Secure(roles="ROLE_466-4")
    
     * Documentación para la función 'newAction'.
     *
     * Función que crea los proyectos.
     *
     * @return Response - Mensaje de exito.
     *
     * @author Byron Anton <banton@telconet.ec>
     * @version 1.0 11-05-2021
     *
     */
    public function updateAction()
    {
        try
        {
            
            $objRequest             = $this->getRequest();
            $objSession             = $objRequest->getSession();
            $strUsrCreacion         = $objSession->get('user')                ? $objSession->get('user'):"";
            $strIpCreacion          = $objRequest->getClientIp()              ? $objRequest->getClientIp():'127.0.0.1';
            $strNombreProyecto      = $objRequest->get("strNombreProyecto")   ? $objRequest->get("strNombreProyecto"):"";
            $strResponsable         = $objRequest->get("strResponsable")      ? $objRequest->get("strResponsable"):"";
            $intIdProyecto          = $objRequest->get('intIdProyecto')           ? $objRequest->get('intIdProyecto'):"";
            $strTipoContabilidad    = $objRequest->get("strTipoContabilidad") ? $objRequest->get("strTipoContabilidad"):"";
            $strFechaInicioEditar    = $objRequest->get("strFechaInicioEditar")      ? $objRequest->get("strFechaInicioEditar"):"";
            $strFechaFinEditar      = $objRequest->get("strFechaFinEditar")      ? $objRequest->get("strFechaFinEditar"):"";
            $strEstado              = $objRequest->get("strEstadoEditar")      ? $objRequest->get("strEstadoEditar"):"";
            $serviceUtil            = $this->get('schema.Util');
            $emNaf                  = $this->getDoctrine()->getManager("telconet_naf");
            $strCodigoError         = "";
            if(empty($intIdProyecto) || empty($strNombreProyecto) || empty($strResponsable))
            {
                $strCodigoError = "204";
                throw new \Exception("Estimado usuario, debe llenar todos los campos para crear la solicitud.".$intIdProyecto.$strNombreProyecto);
            }
            
             if($strFechaFinEditar < $strFechaInicioEditar)
            {
                $strCodigoError = "204";
                throw new \Exception("Estimado usuario, la fecha inicio debe ser menor a la fecha fin");
            }

            $emNaf->getConnection()->beginTransaction();
            $objProyecto = $emNaf->getRepository('schemaBundle:admiProyectos')->find($intIdProyecto);
            $objProyecto->setNombre($strNombreProyecto);
            $objProyecto->setResponsableId($strResponsable);
            $objProyecto->setTipoContabilidad($strTipoContabilidad);
            $objProyecto->setFeInicio(new \DateTime($strFechaInicioEditar));
            $objProyecto->setFeFin(new \DateTime($strFechaFinEditar));
            $objProyecto->setEstado($strEstado);
            $objProyecto->setFeUltMod(new \DateTime('now'));
            $objProyecto->setUsrUltMod($strUsrCreacion);
            $emNaf->persist($objProyecto);
            $emNaf->flush();
            if($emNaf->getConnection()->isTransactionActive())
            {
                $emNaf->getConnection()->commit();
                $emNaf->getConnection()->close();
            }
            $strResponse = "Se actualiza resgistro correctamente.";
        }
        catch(\Exception $e)
        {
            if( $emNaf->getConnection()->isTransactionActive() )
            {
                $emNaf->getConnection()->rollback();
                $emNaf->getConnection()->close();
            }
            $strResponse = "Ocurrió un error al ejecutar la acción, por favor comuniquese con Sistemas";
            if($strCodigoError == "204")
            {
                $strResponse = $e->getMessage();
            }
            $serviceUtil->insertError('TelcoS+', 
                                      'AdmiProyectosController.newAction', 
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        return new Response($strResponse);
    }
    
    
    /**
     *
     * Documentación para la función 'getResponsablesAction'.
     *
     * Función que renderiza la página de Ver detalle.
     *
     * @param int $intIdProyecto  => id del proyecto.
     * @param string $strEstado    => estado del proyecto.
     *
     * @return render - Página de Ver Proyecto.
     *
     * @author Byron Anton <banton@telconet.ec>
     * @version 1.0 11-05-2021
     *
     */
    
    public function getResponsablesAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strUsrCreacion         = $objSession->get('user')           ? $objSession->get('user'):"";
        $strIpCreacion          = $objRequest->getClientIp()         ? $objRequest->getClientIp():'127.0.0.1';
        $intIdEmpresa           = $objSession->get('idEmpresa')      ? $objSession->get('idEmpresa'):"";
        $serviceUtil            = $this->get('schema.Util');
        try
        {
            $arrayParametros                  = array();
            $arrayResponsable                 = array();
            $arrayParametros['intIdEmpresa']  = $intIdEmpresa;
            $arrayResultado                   = $this->getDoctrine()->getManager("telconet_naf")
                                                     ->getRepository('schemaBundle:admiProyectos')
                                                     ->getListaResponsables($arrayParametros);
            if(isset($arrayResultado['error']) && !empty($arrayResultado['error']))
            {
                throw new \Exception($arrayResultado['error']);
            }
            if(!empty($arrayResultado["registros"])&&isset($arrayResultado["registros"]))
            {
                $arrayRegistros = $arrayResultado['registros'];
                
                foreach($arrayRegistros as $arrayDatos)
                {
                    $arrayResponsable[] = array('idRes'       => $arrayDatos["ID_PERSONA_ROL"],
                                                'nombre'      => $arrayDatos["NOMBRES"]);
                    
                }
                
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('TelcoS+', 'AdmiProyectosController.getResponsablesAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
         try 
        {
            $objResponse = new Response(json_encode(array('arrayResponsable' => $arrayResponsable)));
            $objResponse->headers->set('Content-type', 'text/json');
        }
        catch (Exception $e)
        {
            $serviceUtil->insertError('TelcoS+', 'AdmiProyectosController.getResponsablesAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
         return $objResponse;

    }

}
