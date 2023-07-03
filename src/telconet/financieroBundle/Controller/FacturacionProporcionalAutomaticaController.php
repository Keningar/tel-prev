<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\schemaBundle\Entity\AdmiNumeracion;

class FacturacionProporcionalAutomaticaController extends Controller
{
    /**
     * Permite la visualizacion de los documentos pendientes de aprobación correspondiente a los procesos
     * de facturacion proporcional de activacion y reactivacion automatica
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 28-06-2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 01-09-2016 - Se agrega el rol para que se puedan visualizar el filtro por oficina al consultar las facturas.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.2 19-09-2016
     * Se envían los roles: ROLE_198-4758 - Aprobar  Facturación Proporcional Automática.
     *                      ROLE_198-4759 - Rechazar Facturación Proporcional Automática.
     *                      ROLE_198-4760 - Exportar Facturación Proporcional Automática.
     */
    public function listarFacturasAction()
    {
        $objRequest    = $this->getRequest();
        $objSession    = $objRequest->getSession();
        $objCliente    = $objSession->get('cliente');
        $intIdOficina  = $objSession->get('idOficina') ? $objSession->get('idOficina') : 0;

        if($objCliente)
        {
            $arrayParametro = array('cliente' => "S");
        }
        else
        {
            $arrayParametro = array('cliente' => "N");
        }
        
        //Se agrega control de roles permitidos
        $rolesPermitidos = array();
        
        if(true === $this->get('security.context')->isGranted('ROLE_198-165'))
        {
            $rolesPermitidos[] = 'ROLE_198-165'; //COMBO OFICINAS PARA FILTRAR LAS FACTURAS
        }
        
        if(true === $this->get('security.context')->isGranted('ROLE_198-4758'))
        {
            $rolesPermitidos[] = 'ROLE_198-4758'; // FACTURACION PROPORCIONAL AUTOMATICA APROBAR
        }
        
        if(true === $this->get('security.context')->isGranted('ROLE_198-4759'))
        {
            $rolesPermitidos[] = 'ROLE_198-4759'; // FACTURACION PROPORCIONAL AUTOMATICA RECHAZAR
        }
        
        if(true === $this->get('security.context')->isGranted('ROLE_198-4760'))
        {
            $rolesPermitidos[] = 'ROLE_198-4760'; // FACTURACION PROPORCIONAL AUTOMATICA EXPORTAR
        }
        
        $arrayParametro['rolesPermitidos']     = $rolesPermitidos;
        $arrayParametro['intIdOficinaSession'] = $intIdOficina;
        
        return $this->render('financieroBundle:FacturacionProporcionalAutomatica:listarFacturas.html.twig', $arrayParametro);
    }
    
    /**
     * Permite obtener el listado de los clientes segun el parametro de busqueda ingresado
     * se utilizan para la busqueda 4 digitos
     * 
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 28-06-2016
    */
    public function listarClientesAction()
    {
        $request        = $this->getRequest();
        $session        = $request->getSession();
        $idEmpresa      = $session->get('idEmpresa');
        $estado         = 'Eliminado';
        $em             = $this->get('doctrine')->getManager('telconet');
        $filter         = $request->get("query");
        $parte_cliente  = "";
        $datos          = $em->getRepository('schemaBundle:InfoPersona')->findListadoClientesPorEmpresaPorEstado($estado,$idEmpresa,$filter);
        $i              =1;
        
        foreach ($datos as $persona):
            if($persona->getNombres()!="" && $persona->getApellidos()!="")
                $informacion_cliente = $persona->getNombres()." ".$persona->getApellidos();

            if($persona->getRazonSocial()!="")
                $informacion_cliente = $persona->getRazonSocial();


            $arreglo[]= array(
                'idcliente'     => $persona->getId(),
                'descripcion'   => $informacion_cliente,
            );              

            $i++;     
        endforeach;

        if (!empty($arreglo))
            $response = new Response(json_encode(array('clientes' => $arreglo)));
        else
        {
            $arreglo[]= array(
                'idcliente'     => "",
                'descripcion'   => "",
            );
            $response = new Response(json_encode(array('clientes' => $arreglo)));
        }		
        $response->headers->set('Content-type', 'text/json');
        return $response;
	}
	
    public function listarPtosClientesAction()
    {
        $estado     = 'Pendiente';
        $request    = $this->getRequest();
        $idcliente  = $request->get("idcliente");
        $session    = $request->getSession();
        $idEmpresa  = $session->get('idEmpresa');
        $em         = $this->get('doctrine')->getManager('telconet');       
        $resultado  = $em->getRepository('schemaBundle:InfoPunto')->findListarTodosPtosClientes($idcliente);
        $datos      = $resultado['registros'];
        $total      = $resultado['total'];
        $i          = 1;
        
        if($datos)
        {
            foreach ($datos as $pto):
                $arreglo[]= array(
                    'id_pto_cliente'    => $pto->getId(),
                    'descripcion_pto'   => $pto->getDescripcionPunto(),
                );              

                $i++;     
            endforeach;
        }

        if (!empty($arreglo))
                $response = new Response(json_encode(array('listado' => $arreglo)));
        else
        {
                $arreglo[]= array(
                        'id_pto_cliente'    => "",
                        'descripcion_pto'   => "",
                );
                $response = new Response(json_encode(array('listado' => $arreglo)));
        }	
        $response->headers->set('Content-type', 'text/json');
        return $response;
	}
	
    
    /**
     * Permite obtener la data que sera presentada en el grid para el respectivo rechazo o aprobacion de las facturas proporcionales
     * 
     * @version 1.0 Versión Inicial
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 01-09-2016 - Se modifica para que ahora muestre las facturas dependiendo de la oficina enviada como parámetro.
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.3 22-01-2021 - Se modifica la fecha Inicial de Busqueda
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.4 28-04-2022 - Se agrega verificacion para mostrar boton de clonacion
     * @return $objResponse Response
     */
	public function listarFacturasGridAction()
	{
		$objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        
        $objCliente         = $objSession->get('cliente');
        $intEmpresaId       = $objRequest->getSession()->get('idEmpresa');
        $intIdOficina       = $objRequest->get("intIdOficina") ? $objRequest->get("intIdOficina") : 0;
        $strNombreOficina   = $objSession->get('nombreOficina');
        $intLimit           = $objRequest->get("limit");
        $intPage            = $objRequest->get("page");
        $intStart           = $objRequest->get("start");
        $strUsrCreacion     = $objRequest->get('usrCreacion'); 
        
        $strfechaDesde      = explode('T', $objRequest->get("fechaDesde"));
        $strfechaHasta      = explode('T', $objRequest->get("fechaHasta"));
        
        $intIdCliente       = $objRequest->get("idCliente");
        $intPtoCliente      = $objRequest->get('idPtoCliente');
        $emFinanciero       = $this->get('doctrine')->getManager('telconet_financiero');
        $strTipoDoc         = 'FACP';
        $i                  = 1;
        
        //Si el de la session existe los sobreescribe al actual
        if($objCliente)
        {
            $intIdCliente = $objCliente['id'];
        }
        
        //Si el campo se encuentra nulo
        if(!$strfechaDesde[0])
        {
            $strfechaDesde     = date("Y")."-".date("m")."-01";
        }
        else
        {
            $strfechaDesde     = $strfechaDesde[0];
        }
            
        //Si el campo se encuentra nulo
        if (!$strfechaHasta[0])
        {
            $strfechaHasta     = "";
        }
        else
        {
            $strfechaHasta     = $strfechaHasta[0];
        }
        
        $arrayParametros                    = array();
        $arrayParametros['intIdOficina']    = $intIdOficina;          
        $arrayParametros['strfechaDesde']   = $strfechaDesde;
        $arrayParametros['strfechaHasta']   = $strfechaHasta;
        $arrayParametros['intIdCliente']    = $intIdCliente; 
        $arrayParametros['intPtoCliente']   = $intPtoCliente;    
        $arrayParametros['intEmpresaId']    = $intEmpresaId;  
        $arrayParametros['intLimit']        = $intLimit;  
        $arrayParametros['intStart']        = $intStart;  
        $arrayParametros['strTipoDoc']      = $strTipoDoc;  
        $arrayParametros['strUsrCreacion']  = $strUsrCreacion;  
        $arrayParametros['objContainer']    = $this->container;  
        
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $intIdEmpresa       = $objSession->get('idEmpresa');

        $arrayParametrosVerificacion = array();
        $arrayParametrosVerificacion["intIdPersonEmpresaRol"] = $intIdPersonEmpresaRol;
        $arrayParametrosVerificacion["intIdEmpresa"] = $intIdEmpresa;
        $arrayParametrosVerificacion["objInfoDocumentoFinancieroCab"] = null;//Entidad Cabecera-Factura

        //VIENE DE PREFACTURA (PROCESO AUTOMATICO)
        $objInfoDocumentoFinancieroCabService   = $this->get('financiero.InfoDocumentoFinancieroCab');
        $strDescripcionEmpresasPermitidas   = "CHEQUEO_EMPRESA_CLON_PREFACTURAS";
        $strDescripcionDetEstados           = "ESTADOS_CLONACION_PREFACTURAS";

        $arrayParametrosVerificacion["strDescripcionEmpresasPermitidas"] = $strDescripcionEmpresasPermitidas;
        $arrayParametrosVerificacion["strDescripcionDetEstados"] = $strDescripcionDetEstados;
        $arrayParametrosVerificacion["boolPerfilClonacion"] = $this->get('security.context')->isGranted('ROLE_185-6877');

        $arrayDatosVerificacionService = $objInfoDocumentoFinancieroCabService->verificarPermisosClonacion($arrayParametrosVerificacion);
        
        $strPintarBoton = "N";
        if($arrayDatosVerificacionService['boolPermisoPersonaClonar'] && $arrayDatosVerificacionService['boolPermisoEmpresaClonar'])
        {
            $strPintarBoton = "S";
        }
        $arrayParametros['strPintarBoton']    = $strPintarBoton;

        $objFacturas   = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                      ->getJsonFacturasPendientes($arrayParametros);        
        
        $objResponse = new Response($objFacturas);
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
		
        
        
	}
	
	public function listarFacturasProcesadasAction()
    {
        $request = $this->getRequest();
        $session=$request->getSession();
        $cliente=$session->get('cliente');
        $ptocliente=$session->get('ptoCliente');

        if($cliente)
            $parametro=array('cliente' => "S");
        else
            $parametro=array('cliente' => "N");
        return $this->render('financieroBundle:FacturacionProporcionalAutomatica:listarFacturasProcesadas.html.twig', $parametro);
    }
    
	public function facturasProcesadasAction()
	{
        $request        = $this->getRequest();
        $session        = $request->getSession();
        $cliente        = $session->get('cliente');
        $ptocliente     = $session->get('ptoCliente');
        $idOficina      = $session->get('idOficina');
        $nombreOficina  = $session->get('nombreOficina');

        $fechaDesde = explode('T',$request->get("fechaDesde"));
        $fechaHasta = explode('T',$request->get("fechaHasta"));
        $idcliente  = $request->get("idcliente");
        $idestado   = $request->get("idestado");
        
        //Si el de la session existe los sobreescribe al actual
        if($cliente)
            $idcliente  = $cliente['id'];

        if($idestado=="")
            $idestado   = 'Courier';
       
		$i=1;
		if ((!$fechaDesde[0])&&(!$fechaHasta[0]))
		{
            $em_financiero  = $this->get('doctrine')->getManager('telconet_financiero');  
            $fechaDesde     = "";
            $fechaHasta     = "";
            $resultado      = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                            ->findListadoFacturasProporcionalesProcesadas($idOficina,$fechaDesde,$fechaHasta,$idestado);
		}
		else
		{
            $em_financiero  = $this->get('doctrine')->getManager('telconet_financiero'); 
            $fechaDesde     = $fechaDesde[0];
            $fechaHasta     = $fechaHasta[0];    
            $resultado      = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                            ->findListadoFacturasProporcionalesProcesadas($idOficina,$fechaDesde,$fechaHasta,$idestado);
		}
		
        $sumatoria=0;
        $listadoEstadoCuenta=$resultado;
		
        if($listadoEstadoCuenta)
        {
            foreach($listadoEstadoCuenta as $listado)
            {
                $cliente="";

                if($listado["nombres"]!="" )
                    $cliente=$listado["nombres"];

                if($listado["apellidos"]!="" )
                    $cliente.=" ".$listado["apellidos"];

                if($listado["razonSocial"]!="" )
                    $cliente.=" - ".$listado["razonSocial"];

                $arreglo[]= array(
                    'id'        => $listado["id"],
                    'documento' => $listado["numeroFacturaSri"],
                    'FeEmision' => $listado["feEmision"],
                    'Fecreacion'=> $listado["feCreacion"],
                    'oficina'   => $listado["nombreOficina"],
                    'subtotal'  => $listado["subtotal"],
                    'impuestos' => $listado["subtotalConImpuesto"],
                    'descuento' => $listado["subtotalDescuento"],
                    'total'     => $listado["valorTotal"],
                    'punto'     => $listado["login"],
                    'cliente'   => $cliente,
                );              
            }
        }

        if (empty($arreglo))
            $arreglo[]= array(
                'id'        => "",
                'documento' => "",
                'FeEmision' => "",
                'Fecreacion'=> "",
                'oficina'   => "",
                'subtotal'  => "",
                'impuestos' => "",
                'descuento' => "",
                'total'     => "",
                'punto'     => "",
                'cliente'   => "",
            );

            $response = new Response(json_encode(array('documentos'=>$arreglo)));
            $response->headers->set('Content-type', 'text/json');
            return $response;		
	}
    
    /**
     * Permite obtener los usuarios de creación parametrizados para la facturación automática según la empresa en sesión.
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 13-09-2017
     * @return $jsonResponse
     */
    public function getUsersCreacionFactAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        
        $arrayParametros                          = array();
        $arrayParametros['strEmpresaCod']         = $objSession->get('idEmpresa');
        $arrayParametros['strNombreParametroCab'] = 'FILTROS DE FACTURACION AUTOMATICA';
        $arrayParametros['valor1']                = 'FACP'; 
        $arrayParametros['estado']                = 'Activo'; 

        
        $objUsers = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getJSONParametrosByCriterios($arrayParametros);  
        
        $objJsonResponse      = new JsonResponse();
        $objJsonResponse->setContent($objUsers);
        return $objJsonResponse;
    }      
	
}
