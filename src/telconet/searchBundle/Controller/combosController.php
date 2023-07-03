<?php

namespace telconet\searchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class combosController extends Controller
{
    public function ajaxGetEstadosPuntoAction()
    {
	$response = new Response();
        
        $response->headers->set('Content-Type', 'text/json');
	$emComercial = $this->getDoctrine()->getManager();
	$estados = $emComercial->getRepository('schemaBundle:InfoPunto')->getEstados();
	
	if ($estados) {
            $estadoArray = array();
            
            
            foreach ($estados as $estado)
            {
                $estadoArray[] = array('estado_punto' => $estado['estado']);
            }
            
            $data = '{"total":"'.count($estados).'","encontrados":'.json_encode($estadoArray).'}';
            
        }
        else
        {
            $data = '{"total":"0","encontrados":[]}';
        }
        
        $response->setContent($data);
        
        return $response;
        
    }
    
    public function ajaxGetTiposDocumentosComercialesAction()
    {
	$response = new Response();
        
        $response->headers->set('Content-Type', 'text/json');
	
        $tiposDocumentoArray = array();

        $tiposDocumentoArray[] = array('id_tipo_documento'=>'Contrato','tipo_documento' => 'Contrato');
        $tiposDocumentoArray[] = array('id_tipo_documento'=>'Orden Trabajo','tipo_documento' => 'Orden Trabajo');

        $data = '{"total":"'.count($tiposDocumentoArray).'","encontrados":'.json_encode($tiposDocumentoArray).'}';
       
        $response->setContent($data);
        
        return $response;
        
    }
    
    
    /**
     * 
     * Metodo encargado de obtener los parámetros para las validaciones de la licencia
     * 
     * @author José Alava <jialava@telconet.ec>
     * @version 1.0 - 22-06-2019
     * 
     * 
     */
    public function ajaxGetLicenciasAction()
    {
        $response = new Response();
        $arrayLicenciasParametros = [];
        $response->headers->set('Content-Type', 'text/json');
        $emGeneral = $this->getDoctrine()->getManager('telconet_general');
        
        $arrayParametrosCab  =  array ('strNombreParametroCab' => 'SISTEMA OPERATIVO');
        $arrayParametroSO = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findParametrosDet($arrayParametrosCab);
        $arrayLicenciasParametros['SISTEMA OPERATIVO']  = $arrayParametroSO['arrayResultado'];
        
        $arrayParametrosCab  =  array ('strNombreParametroCab' => 'BASE DE DATOS');
        $arrayParametroDB = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findParametrosDet($arrayParametrosCab);
        $arrayLicenciasParametros['BASE DE DATOS']  = $arrayParametroDB['arrayResultado'];
        
        $arrayParametrosCab  =  array ('strNombreParametroCab' => 'APLICACIONES');
        $arrayParametroApp = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findParametrosDet($arrayParametrosCab);
        $arrayLicenciasParametros['APLICACIONES']  = $arrayParametroApp['arrayResultado'];
        
        if(is_array($arrayLicenciasParametros))
        {
              $data = '{"status": "OK","licenciasParametros":'.json_encode($arrayLicenciasParametros).'}';
        }
        else
        {
            $data = '{"status": "Error","licenciasParametros":{}}';
        }        
        $response->setContent($data);
        return $response;        
    }
   

    
     public function ajaxGetFormasPagosContratoAction()
     {
	$response = new Response();
        
        $response->headers->set('Content-Type', 'text/json');
	$emGeneral = $this->getDoctrine()->getManager('telconet_general');
	$formasPago = $emGeneral->getRepository('schemaBundle:AdmiFormaPago')->findBy(array("esPagoParaContrato"=>"S",'estado'=>'Activo'),array('descripcionFormaPago'=>'ASC'));
	
	if ($formasPago) {
            $formasPagoArray = array();
            
            
            foreach ($formasPago as $formaPago)
            {
                $formasPagoArray[] = array('id_forma_pago' => $formaPago->getId(),'forma_pago'=>ucwords(strtolower($formaPago->getDescripcionFormaPago())));
            }
            
            $data = '{"total":"'.count($formasPago).'","encontrados":'.json_encode($formasPagoArray).'}';
            
        }
        else
        {
            $data = '{"total":"0","encontrados":[]}';
        }
        
        $response->setContent($data);
        
        return $response;
        
    }
    
    public function ajaxGetEstadosTiposDocumentosComercialesAction()
    {
	$response = new Response();
        
        $response->headers->set('Content-Type', 'text/json');
	$emComercial = $this->getDoctrine()->getManager();
	$estados = $emComercial->getRepository('schemaBundle:InfoContrato')->getEstados();
	
	if ($estados) {
            $estadoArray = array();
            
            
            foreach ($estados as $estado)
            {
                $estadoArray[] = array('estado_documento' => $estado['estado']);
            }
            
            $data = '{"total":"'.count($estados).'","encontrados":'.json_encode($estadoArray).'}';
            
        }
        else
        {
            $data = '{"total":"0","encontrados":[]}';
        }
        
        $response->setContent($data);
        
        return $response;
        
    }
    
    public function ajaxGetListadoServiciosPorAction()
    {
        $request = $this->getRequest();
        $session=$request->getSession();
        $response = new Response();
        
        $response->headers->set('Content-Type', 'text/json');
        
        $por = $request->query->get("por");
        $codEmpresa = $session->get('idEmpresa');
        $emComercial = $this->getDoctrine()->getManager('telconet');
        
        if(strtolower($por) == "portafolio")
        {
            
            $planes = $emComercial->getRepository('schemaBundle:InfoPlanCab')->findBy(array('empresaCod'=>$codEmpresa,'estado'=>'Activo'),array('nombrePlan'=>'ASC'));

            if ($planes) {
                $planesArray = array();


                foreach ($planes as $plan)
                {
                    $planesArray[] = array('id_servicio' => $plan->getId(),'servicio'=>ucwords(strtolower($plan->getNombrePlan())));
                }

                $data = '{"total":"'.count($planes).'","encontrados":'.json_encode($planesArray).'}';

            }
            else
            {
                $data = '{"total":"0","encontrados":[]}';
            }
        }
        
        if(strtolower($por) == "catalogo")
        {
            $productos = $emComercial->getRepository('schemaBundle:AdmiProducto')->findBy(array('empresaCod'=>$codEmpresa,'estado'=>'Activo'),array('descripcionProducto'=>'ASC'));

            if ($productos) {
                $productosArray = array();


                foreach ($productos as $producto)
                {
                    $productosArray[] = array('id_servicio' => $producto->getId(),'servicio'=>ucwords(strtolower($producto->getDescripcionProducto())));
                }

                $data = '{"total":"'.count($productos).'","encontrados":'.json_encode($productosArray).'}';

            }
            else
            {
                $data = '{"total":"0","encontrados":[]}';
            }
        }
        
         $response->setContent($data);
        
        return $response;
    }
    
    public function ajaxGetEstadoServiciosAction()
    {
	$response = new Response();
        
        $response->headers->set('Content-Type', 'text/json');
	$emComercial = $this->getDoctrine()->getManager();
	$estados = $emComercial->getRepository('schemaBundle:InfoServicio')->getEstados();
	
	if ($estados) {
            $estadoArray = array();
            
            
            foreach ($estados as $estado)
            {
                $estadoArray[] = array('estado_servicio' => $estado['estado']);
            }
            
            $data = '{"total":"'.count($estados).'","encontrados":'.json_encode($estadoArray).'}';
            
        }
        else
        {
            $data = '{"total":"0","encontrados":[]}';
        }
        
        $response->setContent($data);
        
        return $response;
        
    }
    
    public function ajaxGetFormasContactoAction()
     {
	$response = new Response();
        
        $response->headers->set('Content-Type', 'text/json');
	$emComercial = $this->getDoctrine()->getManager();
	$formasContacto = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')->findBy(array('estado'=>'Activo'),array('descripcionFormaContacto'=>'ASC'));
	
	if ($formasContacto) {
            $formasContactoArray = array();
            
            foreach ($formasContacto as $formaContacto)
            {
                $formasContactoArray[] = array('id_forma_contacto' => $formaContacto->getId(),'forma_contacto'=>ucwords(strtolower($formaContacto->getDescripcionFormaContacto())));
            }
            
            $data = '{"total":"'.count($formasContacto).'","encontrados":'.json_encode($formasContactoArray).'}';
            
        }
        else
        {
            $data = '{"total":"0","encontrados":[]}';
        }
        
        $response->setContent($data);
        
        return $response;
        
    }
    
    public function ajaxGetOficinasAction()
     {
	$request = $this->getRequest();
        $session=$request->getSession();
        $response = new Response();
        
        $response->headers->set('Content-Type', 'text/json');
        $codEmpresa = $session->get('idEmpresa');
	$emComercial = $this->getDoctrine()->getManager();
	$oficinas = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->findBy(array('empresaId'=>$codEmpresa,'estado'=>'Activo'),array('nombreOficina' => 'ASC'));
	
	if ($oficinas) {
            $oficinasArray = array();
            
            foreach ($oficinas as $oficina)
            {
                $oficinasArray[] = array('id_oficina' => $oficina->getId(),'oficina'=>ucwords(strtolower($oficina->getNombreOficina())));
            }
            
            $data = '{"total":"'.count($oficinas).'","encontrados":'.json_encode($oficinasArray).'}';
            
        }
        else
        {
            $data = '{"total":"0","encontrados":[]}';
        }
        
        $response->setContent($data);
        
        return $response;
        
    }
    
    public function ajaxGetTiposNegocioAction()
     {
        $response = new Response();
        
        $response->headers->set('Content-Type', 'text/json');
	$emComercial = $this->getDoctrine()->getManager();
	$tiposNegocio = $emComercial->getRepository('schemaBundle:AdmiTipoNegocio')->findBy(array('estado'=>'Activo'),array('nombreTipoNegocio'=>'ASC'));
	
	if ($tiposNegocio) {
            $tiposNegocioArray = array();
            
            foreach ($tiposNegocio as $tipoNegocio)
            {
                $tiposNegocioArray[] = array('id_tipo_negocio' => $tipoNegocio->getId(),'tipo_negocio'=>ucwords(strtolower($tipoNegocio->getNombreTipoNegocio())));
            }
            
            $data = '{"total":"'.count($tiposNegocio).'","encontrados":'.json_encode($tiposNegocioArray).'}';
            
        }
        else
        {
            $data = '{"total":"0","encontrados":[]}';
        }
        
        $response->setContent($data);
        
        return $response;
        
    }
    
    public function ajaxGetTiposUbicacionAction()
     {
        $response = new Response();
        
        $response->headers->set('Content-Type', 'text/json');
	$emComercial = $this->getDoctrine()->getManager();
	$tiposUbicacion = $emComercial->getRepository('schemaBundle:AdmiTipoUbicacion')->findBy(array('estado'=>'Activo'),array('descripcionTipoUbicacion'=>'ASC'));
	
	if ($tiposUbicacion) {
            $tiposUbicacionArray = array();
            
            foreach ($tiposUbicacion as $tipoUbicacion)
            {
                $tiposUbicacionArray[] = array('id_tipo_ubicacion' => $tipoUbicacion->getId(),'tipo_ubicacion'=>ucwords(strtolower($tipoUbicacion->getDescripcionTipoUbicacion())));
            }
            
            $data = '{"total":"'.count($tiposUbicacion).'","encontrados":'.json_encode($tiposUbicacionArray).'}';
            
        }
        else
        {
            $data = '{"total":"0","encontrados":[]}';
        }
        
        $response->setContent($data);
        
        return $response;
        
    }
    
    public function ajaxGetVendedoresAction()
     {
        $objRequest            = $this->getRequest();
        $objSession            = $objRequest->getSession();
        $objResponse           = new Response();
        $emComercial           = $this->getDoctrine()->getManager();
        $strCodEmpresa         = $objSession->get('idEmpresa');
        $strUsrCreacion        = $objSession->get('user');
        $strPrefijoEmpresa     = $objSession->get('prefijoEmpresa');
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $strTipoPersonal       = 'Otros';

        /**
         * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO 
         */
        $arrayResultadoCaracteristicas = $emComercial->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsrCreacion);
        if( !empty($arrayResultadoCaracteristicas) )
        {
            $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
            $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
        }
        $arrayParametros                          = array();
        $arrayParametros['strCodEmpresa']         = $strCodEmpresa;
        $arrayParametros['strPrefijoEmpresa']     = $strPrefijoEmpresa;
        $arrayParametros['strTipoPersonal']       = $strTipoPersonal;
        $arrayParametros['intIdPersonEmpresaRol'] = $intIdPersonEmpresaRol;
        $arrayVendedores = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findVendedoresByEmpresa($arrayParametros);

        if ( $arrayVendedores ) 
        {
            $arrayDatosVendedores = array();

            foreach ($arrayVendedores as $arrayItemVendedores)
            {
                $arrayDatosVendedores[] = array('id_vendedor' => strtolower($arrayItemVendedores->getLogin()),'vendedor'=>  ucwords(strtolower(sprintf("%s",$arrayItemVendedores))));
            }
            $objData = '{"total":"'.count($arrayVendedores).'","encontrados":'.json_encode($arrayDatosVendedores).'}';
        }
        else
        {
            $objData = '{"total":"0","encontrados":[]}';
        }
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent($objData);
        
        return $objResponse;
        
    }
    
    public function ajaxGetTiposElementosAction()
    {
        $response = new Response();
        
        $response->headers->set('Content-Type', 'text/json');
	$emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        
	$tiposElementos = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array('estado'=>"Activo",'esDe'=>"BACKBONE"),array('nombreTipoElemento'=>'ASC'));
	
	if ($tiposElementos) {
            $tiposElementosArray = array();
            
            foreach ($tiposElementos as $tipoElemento)
            {
                $tiposElementosArray[] = array('id_tipo_elemento' =>$tipoElemento->getId(),'tipo_elemento'=>  ucwords(strtolower(sprintf("%s",$tipoElemento->getNombreTipoElemento()))));
            }
            
            $data = '{"total":"'.count($tiposElementos).'","encontrados":'.json_encode($tiposElementosArray).'}';
            
        }
        else
        {
            $data = '{"total":"0","encontrados":[]}';
        }
        
        $response->setContent($data);
        
        return $response;
        
    }
    
    public function ajaxGetModelosElementosAction()
     {
        $request = $this->getRequest();
        $response = new Response();
        
        $response->headers->set('Content-Type', 'text/json');
	$emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $idTipoElemento = $request->query->get('idTipoElemento');
        
	$modelosElementos = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')->findBy(array('tipoElementoId'=>$idTipoElemento,'estado'=>'Activo'),array('descripcionModeloElemento'=>'ASC'));
	
	if ($modelosElementos) {
            $modelosElementosArray = array();
            
            foreach ($modelosElementos as $modeloElemento)
            {
                $modelosElementosArray[] = array('id_modelo_elemento' => $modeloElemento->getId(),'modelo_elemento'=>  ucwords(strtolower(sprintf("%s",$modeloElemento))));
            }
            
            $data = '{"total":"'.count($modelosElementos).'","encontrados":'.json_encode($modelosElementosArray).'}';
            
        }
        else
        {
            $data = '{"total":"0","encontrados":[]}';
        }
        
        $response->setContent($data);
        
        return $response;
        
    }
    
    public function ajaxGetElementosAction()
     {
        $elementoQuery = "";
        $data = '{"total":"0","encontrados":[]}';
        $request = $this->getRequest();
        $response = new Response();
        
        $response->headers->set('Content-Type', 'text/json');
	$emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $idTipoElemento = $request->query->get('idTipoElemento');
        $idModeloElemento = $request->query->get('idModeloElemento');
        
        $elementoQuery = $request->query->get('query');
        
        $elementos = "";

        if($elementoQuery)
        {
            $data = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                      ->getElementoPorNombreModeloTipo($elementoQuery, $idModeloElemento, $idTipoElemento);
        }
        
        
        $response->setContent($data);
        
        return $response;
        
    }
    
    public function ajaxGetTiposMediosAction()
    {
        $response = new Response();
        
        $response->headers->set('Content-Type', 'text/json');
	$emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        
	$tiposMedios = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')->findBy(array('estado'=>'Activo'),array('nombreTipoMedio'=>'ASC'));
	
	if ($tiposMedios) {
            $tiposMediosArray = array();
            
            foreach ($tiposMedios as $tipoMedio)
            {
                $tiposMediosArray[] = array('id_tipo_medio' => $tipoMedio->getId(),'tipo_medio'=>  ucwords(strtolower(sprintf("%s",$tipoMedio->getNombreTipoMedio()))));
            }
            
            $data = '{"total":"'.count($tiposMedios).'","encontrados":'.json_encode($tiposMediosArray).'}';
            
        }
        else
        {
            $data = '{"total":"0","encontrados":[]}';
        }
        
        $response->setContent($data);
        
        return $response;
        
    }
    
    public function ajaxGetInterfacesElementoAction()
     {
        $request = $this->getRequest();
        $response = new Response();
        
        $response->headers->set('Content-Type', 'text/json');
	$emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $idElemento = $request->query->get('idElemento');
        $interfacesElemento = "";
        
        if($idElemento)
	    $interfacesElemento = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->getInterfacesByIdElemento($idElemento);
	
	if ($interfacesElemento) {
            $interfacesElementoArray = array();
            
            foreach ($interfacesElemento as $interfaceElemento)
            {
                $interfacesElementoArray[] = array('id_interface_elemento' => $interfaceElemento->getId(),'interface_elemento'=>  ucwords(strtolower(sprintf("%s",$interfaceElemento))));
            }
            
            $data = '{"total":"'.count($interfacesElemento).'","encontrados":'.json_encode($interfacesElementoArray).'}';
            
        }
        else
        {
            $data = '{"total":"0","encontrados":[]}';
        }
        
        $response->setContent($data);
        
        return $response;
        
    }    
  
}
