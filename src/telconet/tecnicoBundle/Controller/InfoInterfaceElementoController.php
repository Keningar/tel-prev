<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoInterfaceElemento;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class InfoInterfaceElementoController extends Controller
{

    /**
     * getInterfacesByElementoTipoInterfaceAction, metodo que obtiene interfaces segun su formato 
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 14-04-2016
     * @since 1.0
     */
    public function getInterfacesByElementoTipoInterfaceAction()
    {
        $objRequest         = $this->get('request');
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");

        $arrayParametros    = array();
        $arrayParametros['intIdElemento']                = $objRequest->get('intIdElemento');
        $arrayParametros['arrayEstado']                  = ['strComparador' => $objRequest->get('strComparadorEstado'),
                                                            'strEstado'     => $objRequest->get('strEstado')];
        $arrayParametros['arrayNombreInterfaceElemento'] = ['strComparador'              => $objRequest->get('strComparadorNombreInterfaceElemento'),
                                                            'strNombreInterfaceElemento' => $objRequest->get('strNombreInterfaceElemento')];
        $arrayParametros['arrayDescInterfaceElemento']   = ['strComparador'              => $objRequest->get('strComparadorDescInterfaceElemento'),
                                                            'strDescInterfaceElemento'   => $objRequest->get('strDescInterfaceElemento')];

        $objJson = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                     ->getJSONInterfacesByElementoTipoInterface($arrayParametros);
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent($objJson);
        return $objResponse;
    } //getInterfacesByElementoTipoInterfaceAction

    /**
     * 
     * Documentación para el método 'getInterfacesElementoAction'.
     *
     * Metodo utilizado para recuperar interfaces de elementos
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 02-03-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec> se liberar metodo para recuperar interface de elementos Cassettes 
     * @version 1.1 17-12-2015
     * 
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.2 22-08-2016 Se cambia de nombre al metodo generarJsonInterfacesSplitter por un nombre mas genérico
     *                         debido a que se adiciona logica para que muestre el usuario que ocupa una interfaz OUT
     *                         de un cassete, adicional a esto el metodo se le adiciona un nuevo parametro $tipo por 
     *                         esta razón se modifica la forma de envio de parametro a un array
     */
    public function getInterfacesElementoAction()
    {
        $objRespuesta                   = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $peticion                       = $this->get('request');
        $session                        = $peticion->getSession();
        $prefijoEmpresa                 = $session->get('prefijoEmpresa');
        $idElemento                     = $peticion->query->get('idElemento');
        $tipo                           = $peticion->query->get('tipo');
        $start                          = $peticion->query->get('start');
        $codEmpresa                     = $session->get('idEmpresa');
        $strUser                        = $session->get("user");
        $strIpClient                    = $peticion->getClientIp();
        $arrayParametros                = array();
        $arrayParametros["idElemento"]  = $idElemento;
        $arrayParametros["estado"]      = "Todos";
        $arrayParametros["codEmpresa"]  = $codEmpresa;
        $arrayParametros["start"]       = $start;
        $arrayParametros["tipo"]        = $tipo;
        $serviceUtil                    = $this->get('schema.Util');
        
        try
        {        
            if($prefijoEmpresa == "MD" && $tipo == "DSLAM")
            {
                $objJson = $this->getDoctrine()
                                ->getManager("telconet_infraestructura")
                                ->getRepository('schemaBundle:InfoInterfaceElemento')
                                ->generarJsonInterfaces($idElemento, "Todos", $start, 100);
                $objRespuesta->setContent($objJson);
            }
            else if($prefijoEmpresa == "MD" && $tipo == "SPLITTER")
            {
                $arrayParametros["limit"] = 100;
                $objJson                  = $this->getDoctrine()
                                                 ->getManager("telconet_infraestructura")
                                                 ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                 ->generarJsonInterfacesPorTipo($arrayParametros);
                $objResultado = json_decode($objJson);
                if($objResultado->status != 'OK')
                {
                    throw new \Exception($objResultado->mensaje);
                }
                $objRespuesta->setContent($objJson);
            }
            else 
            {
                $arrayParametros["limit"] = 1000;            
                $objJson                  = $this->getDoctrine()
                                                 ->getManager("telconet_infraestructura")
                                                 ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                 ->generarJsonInterfacesPorTipo($arrayParametros);
                $objResultado = json_decode($objJson);
                if($objResultado->status != 'OK')
                {
                    throw new \Exception($objResultado->mensaje);
                }
                $objRespuesta->setContent($objJson);
            }
        }
        catch (\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 'getInterfacesElementoAction', $ex->getMessage(), $strUser, $strIpClient);
            $objRespuesta->setContent("Se presentaron errores al ejecutar la accion.");
        }

        return $objRespuesta;
    }
    
    
    /**
     * Documentación para el método 'getPuertosAction'
     * 
     * Función que retorna los puertos existentes a un switch en el sistema.
     * 
     * @return jsonResponse $jsonResponse
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 14-01-2015
     */
    public function getPuertosAction()
    {
        $jsonResponse      = new JsonResponse();
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $intIdEmpresa      = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $intIdSwitch       = $objRequest->get('elemento') ? $objRequest->get('elemento') : 0;
        $strAccion         = $objRequest->get('accion') ? $objRequest->get('accion') : '';
        $intIdElemento     = $objRequest->get('idPuerto') ? $objRequest->get('idPuerto') : 0;
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $arrayResultados   = array();
        
        if( $intIdEmpresa != 0 )
        {
            $objInfoElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                 ->findOneBy( array('estado' => 'Activo', 'id' => $intIdSwitch) );
            
            if($objInfoElemento)
            {
                $arrayInterfaceElemento = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                            ->findBy( array( 'estado'     => 'not connect', 
                                                                             'elementoId' => $objInfoElemento ) );
                
                if( $arrayInterfaceElemento )
                {
                    foreach( $arrayInterfaceElemento as $objInterfaceElemento )
                    {
                        $arrayItem           = array();
                        $arrayItem['id']     = $objInterfaceElemento->getId();
                        $arrayItem['puerto'] = $objInterfaceElemento->getNombreInterfaceElemento();
                        
                        $arrayResultados[] = $arrayItem;
                    }//foreach( $arrayInterfaceElemento as $objInterfaceElemento )
                }//( $arrayInterfaceElemento )
                
                    
                if( $strAccion == 'editar' )
                {
                    $objInterfaceElemento = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                              ->findOneBy( array( 'estado'     => 'connected', 
                                                                                  'elementoId' => $objInfoElemento,
                                                                                  'id'         => $intIdElemento ) );
                    
                    if( $objInterfaceElemento )
                    {
                        $arrayItem           = array();
                        $arrayItem['id']     = $objInterfaceElemento->getId();
                        $arrayItem['puerto'] = $objInterfaceElemento->getNombreInterfaceElemento();
                        
                        $arrayResultados[] = $arrayItem;
                    }//( $objInterfaceElemento )
                }//( $strAccion == 'editar' )
            }//($objInfoElemento)
        }//if( $intIdEmpresa != 0 )
            
        $jsonResponse->setData( array('total' => count($arrayResultados), 'encontrados' => $arrayResultados) );
        
        return $jsonResponse;
    }

}