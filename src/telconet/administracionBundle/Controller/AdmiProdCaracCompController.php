<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiParametroCab;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Entity\InfoPersona;

use telconet\schemaBundle\Form\AdmiHoldingType;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpFoundation\JsonResponse; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiProdCaracCompController extends Controller implements TokenAuthenticatedController
{
    /**
    * @Secure(roles="ROLE_463-1")
    */
    public function indexAction()
    {
    
        $arrayRolesPermitidos = array();

        if (true === $this->get('security.context')->isGranted('ROLE_463-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_463-1';
        }
        
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $emSeguridad         = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu      = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("463", "1");
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $strPrefijoEmpresa   = $objSession->get('prefijoEmpresa');

        return $this->render('administracionBundle:AdmiProdCaracComp:index.html.twig', array(
            'item'              => $entityItemMenu,
            'rolesPermitidos'   => $arrayRolesPermitidos,
            'strPrefijoEmpresa' => $strPrefijoEmpresa
        ));
    }
    
    
    
    /**
    * @Secure(roles="ROLE_463-1")
     * 
     * Documentación para el método 'gridAction'.
     *
     * Método utilizado para llenar el grid de Holding.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 20-05-2021
    */
    public function gridCaracteristicaCompAction()
    {
        $objRespuesta      = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');        
        $objPeticion       = $this->get('request');
        $intIdProducto     = $objPeticion->query->get('idProducto');
 
        $objProductoId                 = $this->getDoctrine()->getManager("telconet")
                                                             ->getRepository('schemaBundle:AdmiProducto')
                                                             ->findOneById($intIdProducto);

        $arrayParamProdCaracComp       = array('productoId'          => $objProductoId->getId(),
                                               'visibleComercial'    => 'SI');

        $arrayProdCaracteristica       = $this->getDoctrine()->getManager("telconet")
                                              ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                              ->findProdCaractComp($arrayParamProdCaracComp);
        foreach($arrayProdCaracteristica as $arrayCaracProd)
        {
            $arrayProdCarac[] = array(
                'idProdCaracComp'           => $arrayCaracProd['idProdCaracComp'],
                'idProductoCaracteristica'  => $arrayCaracProd['idProductoCaracteristica'],
                'caracteristicaId'          => $arrayCaracProd['caracteristicaId'],
                'descripcionCaracteristica' => $arrayCaracProd['descripcionCaracteristica'],
                'tipoIngreso'               => $arrayCaracProd['tipoIngreso'],
                'esVisible'                 => isset($arrayCaracProd['esVisible']) ? $arrayCaracProd['esVisible'] : 0,
                'editable'                  => isset($arrayCaracProd['editable']) ? $arrayCaracProd['editable'] : 0,
                'estado'                    => $arrayCaracProd['estado'],
                'valoresDefault'            => $arrayCaracProd['valoresDefault'],
                'valoresSeleccionable'      => $arrayCaracProd['valoresSeleccionable'],
            );
        }
        $objRespuesta = new Response(json_encode(array('total' => count($arrayProdCaracteristica),
                                                       'caractComp' => $arrayProdCarac)));
        return $objRespuesta;
    }   
    
    /**
     * Documentación para el método 'updateCaracteristicaCompAction'.
     *
     * Método almacenar el comportamiento de las caracteristicas
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 20-05-2021
    */
    public function updateCaracteristicaCompAction()
    {
        $objRequest         = $this->getRequest();
        $objSesion          = $objRequest->getSession();
        $strUsrCreacion     = $objSesion->get('user');
        $objJsonCaractComp  = $objRequest->get('jsonEntregables');
        $strCodEmpresa      = $objSesion->get('idEmpresa'); 
        
        /* @var $service AdmiProductoService */
        $objProductoCaracComp = $this->get('comercial.AdmiProducto');
          
        $objRespuesta   = new JsonResponse();
        $arrayParametro = array('strUsrCreacion'    => $strUsrCreacion,
                                'objJsonCaractComp' => $objJsonCaractComp,
                                'strCodEmpresa'     => $strCodEmpresa);
        $objRespuesta->setContent(json_encode($objProductoCaracComp->actualizarCaracComportamiento($arrayParametro)));
        return $objRespuesta;
    }
    
    /**
     * Documentación para el método 'ajaxListAllAction'.
     * 
     * Método que consulta el listado de productos.
     * 
     * @return Response Lista de Productos.
     * 
     * @author Walther Joao Gaibor C<wgaibor@telconet.ec>
     * @version 1.0 19-05-2021
     */
    public function ajaxListAllAction()
    {

        $objRequest       = $this->getRequest();
        $objEm            = $this->get('doctrine')->getManager('telconet');

        $objSession       = $objRequest->getSession();
        $strEmpresaCod    = $objSession->get('idEmpresa');

        $strFechaDesde    = explode('T', $objRequest->get("fechaDesde"));
        $strFechaHasta    = explode('T', $objRequest->get("fechaHasta"));
        $strEstado        = $objRequest->get("estado");
        $strNombreTecnico = $objRequest->get("nombreTecnico");
        $strDescripcion   = $objRequest->get("descripcion");
        $strGrupo         = $objRequest->get("strGrupo");
        $strLimit         = $objRequest->get("limit");
        $strPage          = $objRequest->get("page");
        $strStart         = $objRequest->get("start");

        if(!$strFechaDesde[0] && !$strFechaHasta[0] && !$strEstado && !$strNombreTecnico && !$strDescripcion && !$strGrupo)
        {//Se presume como la pagina Inicial
            $arrayParametro = array('nombreTecnico' => 'Todos',
                                    'estado'        => 'Activo',
                                    'esEnlace'      => 'NO',
                                    'idEmpresa'     => $strEmpresaCod,
                                    'limit'         => $strLimit,
                                    'page'          => $strPage,
                                    'start'         => $strStart);
            $arrayResultado = $objEm->getRepository('schemaBundle:AdmiProducto')
                                    ->findTodosProductosCaracComp($arrayParametro);
        }
        else
        {
            if(!$strNombreTecnico || $strNombreTecnico=='')
            {
                $strNombreTecnico = "Todos";
            }

            $arrayResultado = $objEm->getRepository('schemaBundle:AdmiProducto')
                                    ->findTodosProductosPorEstadoYEmpresaCriterios(
                                            array('nombreTecnico' => $strNombreTecnico,
                                                'estado'        => $strEstado,
                                                'fechaDesde'    => $strFechaDesde[0],
                                                'fechaHasta'    => $strFechaHasta[0],
                                                'descripcion'   => $strDescripcion,
                                                'strGrupo'      => $strGrupo,
                                                'empresa_cod'   => $strEmpresaCod,
                                                'limit'         => $strLimit,
                                                'page'          => $strPage,
                                                'start'         => $strStart
                                                )
                                        );
        }
        
        $arrayProductos = $arrayResultado['registros'];
        $intTotal = $arrayResultado['total'];

        foreach($arrayProductos as $producto)
        {

            $arrayArreglo[] = array(
                'idproducto'             => $producto->getId(),
                'codigo'                 => $producto->getCodigoProducto(),
                'descripcion'            => $producto->getDescripcionProducto(),
                'nombreTecnico'          => $producto->getNombreTecnico(),
                'tipo'                   => ($producto->getTipo() == null ? null : ($producto->getTipo() == 'S' ? 'Servicio' : 'Bien')),
                'funcionPrecio'          => $producto->getFuncionPrecio(),
                'funcionCosto'           => $producto->getFuncionCosto(),
                'instalacion'            => $producto->getInstalacion(),
                'strGrupo'               => $producto->getGrupo(),
                'strSubgrupo'            => $producto->getSubgrupo(),
                'fechaCreacion'          => strval(date_format($producto->getFeCreacion(), "d/m/Y G:i")),
                'estado'                 => $producto->getEstado(),
                'strRequiereComisionar'  => $producto->getRequiereComisionar()
            );
        }

        if(empty($arrayArreglo))
        {
            $arrayArreglo[] = array(
                               'idproducto'             => "",
                               'codigo'                 => "",
                               'descripcion'            => "",
                               'nombreTecnico'          => "",
                               'tipo'                   => "",
                               'funcionPrecio'          => "",
                               'funcionCosto'           => "",
                               'instalacion'            => "",
                               'strGrupo'               => "",
                               'strSubgrupo'            => "",
                               'fechaCreacion'          => "",
                               'estado'                 => "",
                               'linkVer'                => "",
                               'linkEditar'             => "",
                               'linkEliminar'           => "",
                               'strRequiereComisionar'  => ""
            );
        }
        $arrayResponse = new Response(json_encode(array('total' => $intTotal, 'productos' => $arrayArreglo)));
        $arrayResponse->headers->set('Content-type', 'text/json');
        return $arrayResponse;
    }
}
