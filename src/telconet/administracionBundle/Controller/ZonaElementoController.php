<?php

namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

class ZonaElementoController extends Controller implements TokenAuthenticatedController
{
    /**
    * indexAction
    * Esta funcion carga el index de los elementos por zona
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 16-04-2018
    *
    * @Secure(roles="ROLE_411-1")
    *
    */
    public function indexAction()
    {
        $arrayRolesPermitidos = array();
        $emSeguridad          = $this->getDoctrine()->getManager('telconet_seguridad');

        if(true === $this->get('security.context')->isGranted('ROLE_411-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_411-1';// Index
        }
        if(true === $this->get('security.context')->isGranted('ROLE_411-7'))
        {
            $arrayRolesPermitidos[] = 'ROLE_411-7';//grid de zonas
        }
        if(true === $this->get('security.context')->isGranted('ROLE_411-5821'))
        {
            $arrayRolesPermitidos[] = 'ROLE_411-5821';//zonificar elementos
        }

        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("411", "1");

        return $this->render('administracionBundle:ZonaElemento:index.html.twig',
                              array('item'            => $entityItemMenu,
                                    'rolesPermitidos' => $arrayRolesPermitidos));
    }

    /**
    * gridAction
    * Esta funcion llena el grid de la consulta de elementos
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 04-04-2018
    *
    * @Secure(roles="ROLE_411-7")
    *
    */
    public function gridAction()
    {
        $objPeticion        = $this->get('request');
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $strEstadoElemento  = $objPeticion->get('estado');
        $strNombreElemento  = $objPeticion->get('nombreElemento') ? $objPeticion->get('nombreElemento') : "";
        $strModeloElemento  = $objPeticion->get('modeloElemento') ? $objPeticion->get('modeloElemento') : "";
        $intZona            = $objPeticion->get('zona') ? $objPeticion->get('zona') : "";
        $strTipoElemento    = $objPeticion->get('tipoElemento');
        $intStart           = $objPeticion->query->get('start');
        $intLimit           = $objPeticion->query->get('limit');        
        $arrayEncontrados   = array();
        $arrayRespuesta     = array();
        $strZona            = "";
        $objResponse        = new JsonResponse();

        $arrayParametros["intStart"]          = $intStart;
        $arrayParametros["intLimit"]          = $intLimit;
        $arrayParametros["strEstadoElemento"] = $strEstadoElemento;
        $arrayParametros["strNombreElemento"] = $strNombreElemento;
        $arrayParametros["strModeloElemento"] = $strModeloElemento;
        $arrayParametros["strTipoElemento"]   = $strTipoElemento;
        $arrayParametros["intZona"]           = $intZona;
        
        $arrayElementos = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getElementos($arrayParametros);

        if($arrayElementos["intTotal"] > 0)
        {
            foreach($arrayElementos["arrayRegistros"] as $arrayIdxElemento)
            {
                
                //Se obtiene la Zona del elemento
                $arrayParametros["strIdElemento"] = $arrayIdxElemento["id"];
                $strZona = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getZonaPorElemento($arrayParametros);

                //Se obtiene el objeto elemento
                $objInfoElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayIdxElemento["id"]);

                if(is_object($objInfoElemento))
                {
                    //Se obtiene el modelo del elemento
                    $strNombreModelo = "";
                    $strNombreModelo = $objInfoElemento->getModeloElementoId()->getNombreModeloElemento();

                    //Se obtiene el tipo del elemento
                    $strNombreTipo = "";
                    $strNombreTipo = $objInfoElemento->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                }
                
                $arrayEncontrados[] = array('id_elemento'     => $arrayIdxElemento["id"],
                                            'nombre_elemento' => $arrayIdxElemento["nombreElemento"],
                                            'nombre_zona'     => $strZona["nombreZona"] ? $strZona["nombreZona"] : "",
                                            'estado'          => $arrayIdxElemento["estado"],
                                            'nombre_tipo'     => $strNombreTipo,
                                            'nombre_modelo'   => $strNombreModelo);
            }
        }

        $arrayRespuesta["total"]       = $arrayElementos["intTotal"];
        $arrayRespuesta["encontrados"] = $arrayEncontrados;

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }

    /**
    * getZonasAjaxAction
    * Funcion que retorna las zonas
    *
    * @return json $objResponse
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 17-04-2018
    */
    public function getZonasAjaxAction()
    {
        $objPeticion        = $this->get('request');
        $strNombreZona      = $objPeticion->query->get('query') ? $objPeticion->query->get('query') : "";
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        $arrayEncontrados   = array();
        $arrayRespuesta     = array();
        $intNumeroRegistros = 0;
        $objResponse        = new JsonResponse();

        //Se obtiene las zonas
        $objAdmiZonas = $emGeneral->getRepository('schemaBundle:AdmiZona')
            ->getZonas( array('strEstado'     => 'Activo',
                              'strNombreZona' => $strNombreZona,
                              'strOpcion'     => 'consultar'));

        $intNumeroRegistros = count($objAdmiZonas);

        if($intNumeroRegistros > 0)
        {
            foreach($objAdmiZonas as $objAdmiZona)
            {
                $arrayEncontrados[] = array('idZona'      => $objAdmiZona->getId(),
                                            'nombreZona'  => strtoupper($objAdmiZona->getNombreZona()));
            }
        }

        $arrayRespuesta["total"]       = $intNumeroRegistros;
        $arrayRespuesta["encontrados"] = $arrayEncontrados;

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }    

    /**
    * updateZonaAjax
    * Funcion que cambia las zonas de un elemento
    *
    * @return json $arrayRespuesta
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 08-04-2018
    *
    * @Secure(roles="ROLE_411-5821")
    *
    */
    public function updateZonaAjaxAction()
    {
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $objPeticion        = $this->getRequest();
        $objSession         = $objPeticion->getSession();
        $strUserSession     = $objSession->get('user');
        $strIpCreacion      = $objPeticion->getClientIp();
        $strTramaElementos  = $objPeticion->get('tramaElementos');
        $intZona            = $objPeticion->get('zona');
        $arrayElementos     = explode("|", $strTramaElementos);
        $objResponse        = new JsonResponse();
        $arrayRespuesta     = array();
        $serviceUtil        = $this->get('schema.Util');
        $serviceSoporte     = $this->get('soporte.SoporteService');
        $arrayIdElementos   = array();
        $arrayElemtCambio   = array();

        $emInfraestructura->getConnection()->beginTransaction();

        try
        {
            for ($i = 0; $i <= count($arrayElementos); $i++)
            {
                if (!empty($arrayElementos[$i]))
                {
                    $objInfoElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayElementos[$i]);
                    
                    if (is_object($objInfoElemento))
                    {
                        $objDetalleElementos = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                     ->findBy(array("elementoId"    => $arrayElementos[$i],
                                                                                    "detalleNombre" => "ZONA",
                                                                                    'estado'        => 'Activo'));

                        if (empty($objDetalleElementos))
                        {
                            $arrayIdElementos[] = intval($arrayElementos[$i]);
                        }
                        else
                        {
                            foreach ($objDetalleElementos as $objDetalleElemento)
                            {
                                $objDetalleElemento->setEstado("Eliminado");
                                $emInfraestructura->persist($objDetalleElemento);
                                $emInfraestructura->flush();

                                if ($objDetalleElemento->getDetalleValor() != $intZona)
                                {
                                    $arrayElemtCambio[] = array ("intIdElemento"   => $objInfoElemento->getId(),
                                                                 "intZonaAnterior" => $objDetalleElemento->getDetalleValor(),
                                                                 "intZonaNueva"    => $intZona);
                                }
                            }
                        }

                        $objInfoDetalleElemento = new InfoDetalleElemento();                    
                        $objInfoDetalleElemento->setElementoId($arrayElementos[$i]);
                        $objInfoDetalleElemento->setDetalleNombre("ZONA");
                        $objInfoDetalleElemento->setDetalleValor($intZona);
                        $objInfoDetalleElemento->setDetalleDescripcion("Zonificacion de Elemento");
                        $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                        $objInfoDetalleElemento->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleElemento->setIpCreacion($strIpCreacion);
                        $objInfoDetalleElemento->setEstado("Activo");
                        $emInfraestructura->persist($objInfoDetalleElemento);
                        $emInfraestructura->flush();

                    }
                }
            }

            $emInfraestructura->getConnection()->commit();

            if (!empty($arrayIdElementos))
            {
                /*========================= INICIO NOTIFICACION HAL ==========================*/
                $serviceSoporte->notificacionesHal(
                        array ('strModulo' => 'elementosZona',
                               'strUser'   =>  $strUserSession,
                               'strIp'     =>  $strIpCreacion,
                               'arrayJson' =>  array ('op'          => 'agregados',
                                                      'idZona'      => intval($intZona),
                                                      'idElementos' => $arrayIdElementos)));
                /*========================== FIN NOTIFICACION HAL ============================*/
            }

            // Se envia la notificacion de los elementos que cambiaron de zona
            if (!empty($arrayElemtCambio))
            {
                foreach ($arrayElemtCambio as $arrayData)
                {
                    /*========================= INICIO NOTIFICACION HAL ==========================*/
                    $serviceSoporte->notificacionesHal(
                            array ('strModulo' => 'elementosZonaCambio',
                                   'strUser'   =>  $strUserSession,
                                   'strIp'     =>  $strIpCreacion,
                                   'arrayJson' =>  array ('id'         => $arrayData['intIdElemento'],
                                                          'idAnterior' => intval($arrayData['intZonaAnterior']),
                                                          'idNueva'    => intval($arrayData['intZonaNueva']))));
                    /*========================== FIN NOTIFICACION HAL ============================*/
                }
            }

            $arrayRespuesta["estado"]  = "Ok";
            $arrayRespuesta["mensaje"] = "Transaccion Exitosa";
        }
        catch(\Exception $ex)
        {
            if($emInfraestructura->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }

            $serviceUtil->insertError('Telcos+',
                                      'ZonaElementoController->updateZonaAjax',
                                      $ex->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);

            $emInfraestructura->close();

            $arrayRespuesta["estado"]  = "Error";
            $arrayRespuesta["mensaje"] = "Error en la transaccion";
        }

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }
}
