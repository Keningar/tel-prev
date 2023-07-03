<?php
/**
* Controlador utilizado para las transacciones en la pantalla de reportes de casos cerrados para la Arcotel
*
* @author Richard Cabrera         <rcabrera@telconet.ec>
* @version 1.0 30-10-2017

*/
namespace telconet\soporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;


class ReporteTareasTraceController extends Controller
{
    /**
    * indexAction - Función que llama a la pantalla de generacion de reportes de tareas - trace
    *
    * @return render
    *
    * @author Richard Cabrera   <rcabrera@telconet.ec>
    * @version 1.0 30-10-2017
    *
    */
    public function indexAction()
    {
        $arrayRolesPermitidos = array();
        $emSeguridad          = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu       = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("402", "1");

        //MODULO 378 - descargar archivo arcotel
        if (true === $this->get('security.context')->isGranted('ROLE_402-5537'))
        {
            $arrayRolesPermitidos[] = 'ROLE_402-5537';
        }

        return $this->render('soporteBundle:reportes:indexTrace.html.twig',
                             array('item'            => $entityItemMenu,
                                   'rolesPermitidos' => $arrayRolesPermitidos
        ));
    }


    /**
    * generarAction - Función que se encarga de generar el reporte de tareas - trace
    *
    * @return Object $objResponse
    *
    * @author Richard Cabrera   <rcabrera@telconet.ec>
    * @version 1.0 30-10-2017
    *
    * @Secure(roles="ROLE_402-5537")
    */
    public function generarAction()
    {
        $objRequest         = $this->get('request');
        $objResponse        = new JsonResponse();
        $strFechaInicio     = $objRequest->get("fechaInicio");
        $strFechaFin        = $objRequest->get("fechaFin");
        $serviceSoporte     = $this->get('soporte.SoporteService');


        $strUsuarioCreacion = $objRequest->getSession()->get('user');
        $strIpCreacion      = $objRequest->getClientIp();


        $strFechaInicio   = str_replace("-", "/", $strFechaInicio);
        $arrayFechaInicio = explode("/", $strFechaInicio);
        $strFechaInicio   = $arrayFechaInicio[0].'/'.$arrayFechaInicio[1].'/'.$arrayFechaInicio[2];

        $strFechaFin   = str_replace("-", "/", $strFechaFin);
        $arrayFechaFin = explode("/", $strFechaFin);
        $strFechaFin   = $arrayFechaFin[0].'/'.$arrayFechaFin[1].'/'.$arrayFechaFin[2];

        $arrayParametros["strFechaInicio"]    = $strFechaInicio;
        $arrayParametros["strFechaFin"]       = $strFechaFin;
        $arrayParametros["strUser"]           = $strUsuarioCreacion;
        $arrayParametros["strIpUser"]         = $strIpCreacion;

        $arrayRespuesta = $serviceSoporte->generarReporteTareasTrace($arrayParametros);

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }
}
