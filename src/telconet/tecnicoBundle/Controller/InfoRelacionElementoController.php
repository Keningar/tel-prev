<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;

class InfoRelacionElementoController extends Controller
{
    /**
     * Funcion que sirve para cargar la pagina de consulta
     * para la administracion de Relacion Elemento
     *
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 14-10-2014
     */
    public function indexAction()
    {
        $rolesPermitidos = array();

        if(true === $this->get('security.context')->isGranted('ROLE_263-9'))
        {
            $rolesPermitidos[] = 'ROLE_263-9'; //Eliminar Relacion Elemento
        }

        return $this->render('tecnicoBundle:InfoRelacionElemento:index.html.twig', array(
                'rolesPermitidos' => $rolesPermitidos
        ));
    }

    /**
     * Funcion que realiza la llamada al repositorio
     * para obtener los datos necesarios
     *
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 14-10-2014
     */
    public function getEncontradosAction()
    {
        ini_set('max_execution_time', 400000);
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $session = $this->get('session');
        $peticion = $this->get('request');

        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        $idEmpresa = $session->get('idEmpresa');

        $elementoIdA = $peticion->query->get('elementoIdA');
        $elementoIdB = $peticion->query->get('elementoIdB');

        $objJson = $em->getRepository('schemaBundle:InfoRelacionElemento')
            ->generarJsonRelacionElemento($elementoIdA, $elementoIdB, $start, $limit, $idEmpresa);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /**
     * Funcion que realiza la eliminacion de un
     * registro dado.
     *
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 14-10-2014
     *
     * @Secure(roles="ROLE_263-9")
     */
    public function deleteAjaxAction()
    {
        ini_set('max_execution_time', 3000000);
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');

        $peticion = $this->get('request');

        $idRelacionElemento = $peticion->get('param');

        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        $entity = $em->find('schemaBundle:InfoRelacionElemento', $idRelacionElemento);
        if($entity)
        {
            $em->getConnection()->beginTransaction();

            $entity->setEstado("Eliminado");
            $em->persist($entity);
            $em->flush();
            $respuesta->setContent("OK");

            $em->getConnection()->commit();
        }
        else
        {
            $respuesta->setContent("No existe la entidad");
        }

        return $respuesta;
    }
    
    /**
     * Funcion utilizada para obtener unidades de elementos Rack
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 27-02-2015
     * 
     * @Secure(roles="ROLE_273-2137")
     */
    public function ajaxUnidadesElementosByPadreAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion           = $this->get('request');
        $intStart           = $peticion->get('start');
        $intLimit           = $peticion->get('limit');
        $intIdElemento      = $peticion->get('idElemento');
        $intTipoElemento    = 0;
        $em_infraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objTipoElemento    = $em_infraestructura->getRepository('schemaBundle:AdmiTipoElemento')->findOneByNombreTipoElemento("UDRACK");
        if($objTipoElemento)
        {
            $intTipoElemento = $objTipoElemento->getId();
        }
	    $objJson = $this->getDoctrine()
                        ->getManager("telconet_infraestructura")
                        ->getRepository('schemaBundle:InfoRelacionElemento')
                        ->generarJsonUnidadesElementosByPadre($intTipoElemento, $intIdElemento, $intStart, $intLimit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

}