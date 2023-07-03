<?php

namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiIntervaloController extends Controller implements TokenAuthenticatedController
{

    /**
    * indexAction
    * Esta funcion carga el index de los intervalos
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 04-04-2018
    *
    * @Secure(roles="ROLE_409-1")
    *
    */
    public function indexAction()
    {
        $arrayRolesPermitidos = array();
        $emSeguridad          = $this->getDoctrine()->getManager('telconet_seguridad');

        if(true === $this->get('security.context')->isGranted('ROLE_409-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_409-1';//consultar intervalos
        }
        if(true === $this->get('security.context')->isGranted('ROLE_409-7'))
        {
            $arrayRolesPermitidos[] = 'ROLE_409-7';//grid de intervalos
        }
        if(true === $this->get('security.context')->isGranted('ROLE_409-5817'))
        {
            $arrayRolesPermitidos[] = 'ROLE_409-5817';//eliminar intervalos
        }

        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("409", "1");

        return $this->render('administracionBundle:AdmiIntervalo:index.html.twig',
                              array('item'            => $entityItemMenu,
                                    'rolesPermitidos' => $arrayRolesPermitidos));
    }



    /**
    * gridAction
    * Esta funcion llena el grid de la consulta de Intervalos
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 04-04-2018
    *
    * @Secure(roles="ROLE_409-7")
    *
    */
    public function gridAction()
    {
        $objPeticion        = $this->get('request');
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $strEstadoIntervalo = $objPeticion->get('estado');
        $arrayEncontrados   = array();
        $arrayRespuesta     = array();
        $intNumeroRegistros = 0;
        $objResponse        = new JsonResponse();

        $arrayParametros["strEstado"] = $strEstadoIntervalo;
        $objAdmiIntervalos = $emSoporte->getRepository('schemaBundle:AdmiIntervalo')->getIntervalos($arrayParametros);


        $intNumeroRegistros = count($objAdmiIntervalos);

        if($intNumeroRegistros > 0)
        {
            foreach($objAdmiIntervalos as $objAdmiIntervalo)
            {
                $arrayEncontrados[] = array('id_intervalo' => $objAdmiIntervalo->getId(),
                                            'hora_inicio'  => strval(date_format($objAdmiIntervalo->getHoraIni(), "H:i")),
                                            'hora_fin'     => strval(date_format($objAdmiIntervalo->getHoraFin(), "H:i")),
                                            'estado'       => $objAdmiIntervalo->getEstado());
            }
        }

        $arrayRespuesta["total"]       = $intNumeroRegistros;
        $arrayRespuesta["encontrados"] = $arrayEncontrados;

        $objResponse->setData($arrayRespuesta);

        return $objResponse;

    }
    

    
    /**
    * deleteAjaxAction
    * Funcion que cambia a estado Eliminado los Intervalos
    *
    * @return json $arrayRespuesta
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 08-03-2018
    *
    * @Secure(roles="ROLE_409-5817")
    *
    */
    public function deleteAjaxAction()
    {
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $objPeticion        = $this->getRequest();
        $objSession         = $objPeticion->getSession();
        $strUserSession     = $objSession->get('user');
        $strIpCreacion      = $objPeticion->getClientIp();
        $strTramaIntervalos = $objPeticion->get('tramaIntervalos');
        $arrayIntervalos    = explode("|", $strTramaIntervalos);
        $objResponse        = new JsonResponse();
        $arrayRespuesta     = array();
        $strEstadoIntervalo = "Eliminado";
        $serviceUtil        = $this->get('schema.Util');
        $serviceSoporte     = $this->get('soporte.SoporteService');

        $emSoporte->getConnection()->beginTransaction();

        try
        {
            for ($i = 0; $i <= count($arrayIntervalos); $i++)
            {
                if (!empty($arrayIntervalos[$i]))
                {
                    $objAdmiIntervalo = $emSoporte->getRepository('schemaBundle:AdmiIntervalo')->find($arrayIntervalos[$i]);

                    if (is_object($objAdmiIntervalo))
                    {
                        $objAdmiIntervalo->setEstado($strEstadoIntervalo);
                        $objAdmiIntervalo->setUsrModificacion($strUserSession);
                        $objAdmiIntervalo->setFeModificacion(new \DateTime('now'));
                        $objAdmiIntervalo->setIpModificacion($strIpCreacion);
                        $emSoporte->persist($objAdmiIntervalo);
                        $emSoporte->flush();
                    }
                }
            }
            $emSoporte->getConnection()->commit();

            /*========================= INICIO NOTIFICACION HAL ==========================*/
            for($i = 0; $i <= count($arrayIntervalos); $i++)
            {
                if(!empty($arrayIntervalos[$i]))
                {
                    $serviceSoporte->notificacionesHal(
                            array ('strModulo' => 'intervaloAdmi',
                                   'strUser'   =>  $strUserSession,
                                   'strIp'     =>  $strIpCreacion,
                                   'arrayJson' =>  array ('metodo' => 'elimino',
                                                          'id'     => $arrayIntervalos[$i])));
                }
            }
            /*========================== FIN NOTIFICACION HAL ============================*/

            $arrayRespuesta["estado"]  = "Ok";
            $arrayRespuesta["mensaje"] = "Transaccion Exitosa";
        }
        catch(\Exception $objEx)
        {
            if($emSoporte->isTransactionActive())
            {
                $emSoporte->rollback();
            }

            $serviceUtil->insertError('Telcos+',
                                      'AdmiIntervaloController->deleteAjaxAction',
                                      $objEx->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);
            
            $emSoporte->close();            

            $arrayRespuesta["estado"]  = "Error";
            $arrayRespuesta["mensaje"] = "Error en la transaccion";
        }

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }
}
