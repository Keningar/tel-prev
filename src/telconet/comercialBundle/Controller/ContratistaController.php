<?php

namespace telconet\comercialBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;


/**
 * InfoPersona controller.
 */
class ContratistaController extends Controller implements TokenAuthenticatedController
{ 
    /**
     * 
     * 
     * Documentación para el método 'gridAction'.
     *
     * Muestra el listado de todos los contratistas que tiene la empresa.
     *
     * @return Response 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 12-01-2016
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 14-07-2016 Se realizan modificaciones para realizar la búsqueda por nombres desde un combobox
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 17-07-2016 Se generaliza la función invocada del repositorio
     * 
     */
    public function gridAction()
    {
        $objRequest     = $this->getRequest();
        $objResponse    = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $identificacion = $objRequest->get("identificacion") ? $objRequest->get("identificacion") : '';
        $nombre         = $objRequest->get("nombre") ? $objRequest->get("nombre") : ($objRequest->get("query") ? $objRequest->get("query"): '');
        $limit          = $objRequest->get("limit");
        $start          = $objRequest->get("start");
        $page           = $objRequest->get("page");
        $idEmpresa      = $objRequest->getSession()->get('idEmpresa');
        $em             = $this->get('doctrine')->getManager('telconet');

        $arrayParametros = array(
            'idEmpresa'     => $idEmpresa,
            'identificacion'=> $identificacion,
            'nombre'        => $nombre,
            'tiposRoles'    => array('Contratista','Proveedor'),
            'limit'         => $limit,
            'page'          => $page,
            'start'         => $start,
            'estado'        => 'Activo'

        );

        $objJson= $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getJSONContratistasoProveedoresVehiculoPorCriterios($arrayParametros);
        $objResponse->setContent($objJson);
        
        return $objResponse;
    }

}
