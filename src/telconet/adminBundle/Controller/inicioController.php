<?php

namespace telconet\adminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;

/**
 * ClieCliente controller.
 *
 */
class inicioController extends Controller implements TokenAuthenticatedController
{
    /**
     * Metodo dashboard
     * 
     * @author Version Original
     * @version 1.0
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 - Incluir llamada a cambio de clve cuando lo amerite
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.2 2016-10-05 - Se cargan las actividades recientes para presentar en pantalla inicial
     * 
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.3 
     * @since 2022-03-15 - Se actualiza para incluir en sesión, el valor de minutos parametrizados, para consultar
     * la cantidad de Casos que se han aperturado en Extranet 
     * 
     */
    public function dashboardAction()
    {   
        $request = $this->getRequest();
        $session = $request->getSession();
        if($session->get('user'))
        {
            if($session->get('requiereCambioPass') !== null && $session->get('requiereCambioPass'))
            {
                $objForm = $this->creaFormularioActualizaClave();
                return $this->render('adminBundle:Default:cambiar_clave.html.twig', array('objForm' => $objForm->createView()));
            }
            else
            {
                $start = '';
                $limit = '';
                $descripcion = "";
                $estado = 'Activo';
                $empresa = "";
                $respuestaListaPlantillas= "";
                $em_comunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");
                $emGeneral       = $this->getDoctrine()->getManager('telconet_general');

                $entityClaseDocumento = $em_comunicacion->getRepository('schemaBundle:AdmiClaseDocumento')->findOneBy(
                        array('nombreClaseDocumento' => 'Notificacion Interna Noticia'));
                if ($entityClaseDocumento){
                    // Obtener listado de Noticias servicio PlantillaService
                    /* @var servicioPlantilla PlantillaService */
                    $servicioPlantilla = $this->get('soporte.ListaPlantilla');
                    $respuestaListaPlantillas = $servicioPlantilla
                                              ->listarPlantillas($entityClaseDocumento->getId(), $descripcion, $estado,
                                                                 $empresa, $start, $limit, "SI");
                    $respuestaListaPlantillas=json_decode($respuestaListaPlantillas,true);
                }
                //Se cargan las actividades recientes
                $emSeguridad = $this->getDoctrine()->getManager("telconet_seguridad");
                $arrayActividadesPersona = $emSeguridad->getRepository('schemaBundle:SeguBitacoraPersona')
                                                       ->getUltimasActividades($session->get('id_empleado'));
                if (true === $this->get('security.context')->isGranted('ROLE_78-8517'))
                {
                    $arrayTiempoMinutosCasosExtranet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                 ->getOne('MS_CORE_SOPORTE',
                                                                        'SOPORTE',
                                                                        '',
                                                                        'TIEMPO_MIN_CONSULTAR_CASOS_EXTRANET',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        $session->get('idEmpresa'));
                    $intMinutosConsultaCasosExtranet = $arrayTiempoMinutosCasosExtranet['valor1'];
                    $session->set('minutosConsultaCasosExtranet',$intMinutosConsultaCasosExtranet);
                }
                
                return $this->render('adminBundle:Inicio:dashboard.html.twig',
                                     array('listaNoticias'    => $respuestaListaPlantillas["encontrados"],
                                           'listaActividades' => $arrayActividadesPersona)
                                    );
            }
        }
        else
        {
            return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                        'mensaje' => 'No tiene Usuario y Credenciales para utilizar el Telcos +. Favor Solicitarlos a sistemas@telconet.ec'));
        }
    }      
public function menuAction($opcion_menu)
    {
		//=== $this->get('security.context')->isGranted('ROLE_147-1')
        if (true === $this->get('security.context')->isGranted('ROLE_147-1'))
        {
            return $this->forward('seguridadBundle:Default:dashboard', array('modulo' =>'inicio','opcion_menu' =>$opcion_menu));
        }
        return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                        'mensaje' => 'No tiene permisos para usar la aplicacion.'));
    }
    
    /**
    * @Secure(roles="ROLE_147-117")
    */
    public function inicioBusquedaAvanzadaAction()
    {
        $request = $this->get('request');
        $loginSearch = $request->query->get('LoginSearch');
        $rolesPermitidos = array();
if (true === $this->get('security.context')->isGranted('ROLE_147-124'))
        {
$rolesPermitidos[] = 'ROLE_147-124';
}
        $session = $this->get('request')->getSession();
        $modulo_activo = $session->get("modulo_activo");
        $em = $this->get('doctrine')->getManager('telconet');
        $em_general = $this->get('doctrine')->getManager('telconet_general');
        
        $arrayEstados = array(  
                                array("idEstado"=>"0", "descripcionEstado"=>"-- Seleccione --"),
                                array("idEstado"=>"Pendiente", "descripcionEstado"=>"Pendiente"),
                                array("idEstado"=>"Activo", "descripcionEstado"=>"Activo"),
                                array("idEstado"=>"Inactivo", "descripcionEstado"=>"Inactivo"),
                                array("idEstado"=>"Cancelado", "descripcionEstado"=>"Cancelado"),
                                array("idEstado"=>"Anulado", "descripcionEstado"=>"Anulado")
                             );
        $arrayEstadosContrato = array(  
                                        array("idEstado"=>"0", "descripcionEstado"=>"-- Seleccione --"),
                                        array("idEstado"=>"Pendiente", "descripcionEstado"=>"Pendiente"),
                                        array("idEstado"=>"Activo", "descripcionEstado"=>"Activo"),
                                        array("idEstado"=>"Inactivo", "descripcionEstado"=>"Inactivo"),
                                        array("idEstado"=>"Cancelado", "descripcionEstado"=>"Cancelado")
                                    );
        
        $tipos_negocio = $em->getRepository('schemaBundle:AdmiTipoNegocio')->findByEstado("Activo");
        $ArrayTipoNegocio[]=array('idTipo'=>'0', 'descripcionTipo'=>'-- Seleccione --');
        foreach ($tipos_negocio as $tipo):
            $ArrayTipoNegocio[]=array( 'idTipo'=>$tipo->getId(), 'descripcionTipo'=>$tipo->getNombreTipoNegocio() );
        endforeach;

        $forma_pago = $em_general->getRepository('schemaBundle:AdmiFormaPago')->findByEstado("Activo");
        $ArrayFormaPago[]=array('idFormaPago'=>'0', 'descripcionFormaPago'=>'-- Seleccione --');
        foreach ($forma_pago as $forma):
            $ArrayFormaPago[]=array( 'idFormaPago'=>$forma->getId(), 'descripcionFormaPago'=>$forma->getDescripcionFormaPago() );
        endforeach;
        
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");  
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("147", "1");		
		$session->set('modulo_activo', $entityItemMenu->getNombreItemMenu());
		$session->set('modulo_activo_html', $entityItemMenu->getTitleHtml());
		$session->set('id_modulo_activo', $entityItemMenu->getId());
		$session->set('imagen_modulo_activo', $entityItemMenu->getUrlImagen());

        return $this->render('adminBundle:Default:inicio.html.twig', array(
                                'loginSearch'=>$loginSearch,
                                'estados'=>$arrayEstados,
                                'tipo_negocio'=>$ArrayTipoNegocio,
                                'estados_contrato'=>$arrayEstadosContrato,
                                'formas_pago'=>$ArrayFormaPago,
                                'rolesPermitidos'=>$rolesPermitidos
                            ));
    }   
    
    /**
	* @Secure(roles="ROLE_147-244")
	*/
    public function getFormasPagoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $request = $this->get('request');  
		
        $start = $request->query->get('start');
        $limit = $request->query->get('limit');
		
        $forma_pago = $this->getDoctrine()
			            ->getManager("telconet_general")
			            ->getRepository('schemaBundle:AdmiFormaPago')
			            ->getRegistros("","Activo",$start,$limit);
						
		$ArrayFormaPago[]=array('id_forma_pago'=>'0', 'descripcion_forma_pago'=>'-- Seleccione --');
        foreach ($forma_pago as $forma):
            $ArrayFormaPago[]=array( 'id_forma_pago'=>$forma->getId(), 'descripcion_forma_pago'=>$forma->getDescripcionFormaPago() );
        endforeach;
		
		$num = count($ArrayFormaPago);		
		$dataF =json_encode($ArrayFormaPago);
		$objJson= '{"total":"'.$num.'","encontrados":'.$dataF.'}';		
        $respuesta->setContent($objJson);   
			
        return $respuesta;
    }
	
    public function getBancosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $request = $this->get('request');  
		
        $start = $request->query->get('start');
        $limit = $request->query->get('limit');
		
        $bancos = $this->getDoctrine()
			            ->getManager("telconet_general")
			            ->getRepository('schemaBundle:AdmiBanco')
			            ->getRegistros("","Activo",$start,$limit);
						
		$ArrayBancos[]=array('id_banco'=>'0', 'descripcion_banco'=>'-- Seleccione --');
        foreach ($bancos as $banco):
            $ArrayBancos[]=array( 'id_banco'=>$banco->getId(), 'descripcion_banco'=>$banco->getDescripcionBanco() );
        endforeach;
		
		$num = count($ArrayBancos);		
		$dataF =json_encode($ArrayBancos);
		$objJson= '{"total":"'.$num.'","encontrados":'.$dataF.'}';		
        $respuesta->setContent($objJson);   
			
        return $respuesta;
    }
	
	/**
	* #@Secure(roles="ROLE_147-244")
	*/
    public function getTiposDocumentoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $request = $this->get('request');  
		
        $start = $request->query->get('start');
        $limit = $request->query->get('limit');
		
        $tipo_documentos = $this->getDoctrine()
			            ->getManager("telconet_financiero")
			            ->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
			            ->getRegistros("","Activo",$start,$limit);
						
		$ArrayTipoDocumento[]=array('codigo_tipo_documento'=>'0', 'nombre_tipo_documento'=>'-- Seleccione --');
        foreach ($tipo_documentos as $tipo_documento):
            $ArrayTipoDocumento[]=array( 'codigo_tipo_documento'=>$tipo_documento->getCodigoTipoDocumento(), 'nombre_tipo_documento'=>$tipo_documento->getNombreTipoDocumento() );
        endforeach;
		
		$num = count($ArrayTipoDocumento);		
		$dataF =json_encode($ArrayTipoDocumento);
		$objJson= '{"total":"'.$num.'","encontrados":'.$dataF.'}';		
        $respuesta->setContent($objJson);   
			
        return $respuesta;
    }

    /*combo EMPLEADOS llenado ajax*/
    /**
	* @Secure(roles="ROLE_147-108")
	*/
    public function getEmpleadosAction()
    {        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');        
        $nombre = $peticion->query->get('query');
        
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        
        $objData = $this->getDoctrine()
                        ->getManager("telconet")
                        ->getRepository('schemaBundle:InfoPersona')
                        ->findPersonasXTipoRol("Empleado", $nombre, $codEmpresa);
        
        $arreglo = array();
        $num = count($objData); 
        if($objData && count($objData)>0)
        {
            foreach($objData as $key => $entityPersona)
            {
                $arreglo[]= array('id_empleado'=>$entityPersona->getId(),'nombre_empleado'=> $entityPersona->getNombres() . " " . $entityPersona->getApellidos());
            }
            
            $dataF =json_encode($arreglo);
            $objJson= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
        } 
        else
        {
            $objJson= '{"total":"0","encontrados":[]}';
        }        
        
        $respuesta->setContent($objJson);
        return $respuesta;		
    }
	
    /*combo EMPRESAS EXTERNAS llenado ajax*/
    /**
	* @Secure(roles="ROLE_147-110")
	*/
    public function getEmpresasExternasAction()
    {        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');        
        $nombre = $peticion->query->get('query');
        
        $objData = $this->getDoctrine()
                        ->getManager("telconet")
                        ->getRepository('schemaBundle:InfoPersona')
                        ->findPersonasXTipoRol("Proveedor", $nombre, "");
        
        $arreglo = array();
        $num = count($objData); 
        if($objData && count($objData)>0)
        {
            foreach($objData as $key => $entityPersona)
            {
                $arreglo[]= array('id_empresa_externa'=>$entityPersona->getId(),'nombre_empresa_externa'=> $entityPersona->getRazonSocial());
            }
            
            $dataF =json_encode($arreglo);
            $objJson= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
        } 
        else
        {
            $objJson= '{"total":"0","encontrados":[]}';
        }        
        
        $respuesta->setContent($objJson);
        return $respuesta;		
    }    
    

    /*
    * getCuadrillasAction
    * Esta funcion retorna el listado de cuadrillas
    *
    * @version 1.0 Version Incial
    *
    * @author modificado Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 09-04-2018 Se agrega condicion para cuadrillas asignadas a Hal
    *
	* @Secure(roles="ROLE_147-109")
	*/
    public function getCuadrillasAction()
    {        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');        
        $nombre = $peticion->query->get('query');

        $arrayParametros["strNombreCuadrilla"] = $nombre;
        $arrayParametros["strHal"]             = "N";

        $objData = $this->getDoctrine()
                        ->getManager("telconet")
                        ->getRepository('schemaBundle:AdmiCuadrilla')
                        ->findCuadrillas($arrayParametros);
        
        $arreglo = array();
        $num = count($objData); 
        if($objData && count($objData)>0)
        {
            foreach($objData as $key => $entityPersona)
            {
                $arreglo[]= array('id_cuadrilla'=>$entityPersona->getId(),'nombre_cuadrilla'=> $entityPersona->getNombreCuadrilla());
            }
            
            $dataF =json_encode($arreglo);
            $objJson= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
        } 
        else
        {
            $objJson= '{"total":"0","encontrados":[]}';
        }        
        
        $respuesta->setContent($objJson);
        return $respuesta;		
    } 
	
    /**
    * @Secure(roles="ROLE_147-120")
    */
    public function datosComercialBusquedaAction()
    {
        /*se obtiene los datos basicos del formulario para la busqueda
            * - Segun los campos llenos se hace el filtro de los datos 
            * y el mismo se lo va agregando en el query*/
        $em = $this->getDoctrine()->getManager('telconet');
			
        $em->getConnection()->beginTransaction();
		try
		{		
			$clientes="";
			$request = $this->get('request');
			
			$start = $request->query->get('start');
			$limit = $request->query->get('limit');
			
			$login = $request->query->get("login") ? $request->query->get("login") : "" ;        
			$descripcion_pto = $request->query->get("descripcion_pto") ? $request->query->get("descripcion_pto") : "" ;
			$direccion_pto = $request->query->get("direccion_pto") ? $request->query->get("direccion_pto") : "" ;
			$estados_pto = $request->query->get("estados_pto") ? $request->query->get("estados_pto") : "" ;
			$negocios_pto = $request->query->get("negocios_pto") ? $request->query->get("negocios_pto") : "" ;
			$vendedor = $request->query->get("vendedor") ? $request->query->get("vendedor") : "" ;
			$identificacion = $request->query->get("identificacion") ? $request->query->get("identificacion") : "" ;
			$nombre = $request->query->get("nombre") ? $request->query->get("nombre") : "" ;
			$apellido = $request->query->get("apellido") ? $request->query->get("apellido") : "" ;
			$razon_social = $request->query->get("razon_social") ? $request->query->get("razon_social") : "" ;
			$direccion_grl = $request->query->get("direccion_grl") ? $request->query->get("direccion_grl") : "" ;
			$depende_edificio = $request->query->get("depende_edificio") ? $request->query->get("depende_edificio") : 0 ;
			$es_edificio = $request->query->get("es_edificio") ? $request->query->get("es_edificio") : 0 ;
			
			$estados_contrato = $request->query->get("con_estado") ? $request->query->get("con_estado") : 0 ;
			$formas_pago = $request->query->get("con_forma_pago") ? $request->query->get("con_forma_pago") : 0 ;
			$num_contrato = $request->query->get("con_num_contrato") ? $request->query->get("con_num_contrato") : "" ;
			$es_vip = $request->query->get("con_es_vip") ? $request->query->get("con_es_vip") : 0 ;
		   
			$tipo_documento = $request->query->get("doc_tipo") ? $request->query->get("doc_tipo") : 0 ;
			$creador_documento = $request->query->get("doc_creado") ? $request->query->get("doc_creado") : "" ;
			$num_documento = $request->query->get("doc_numero") ? $request->query->get("doc_numero") : "" ;
			$tipo_orden = $request->query->get("doc_tipo_orden") ? $request->query->get("doc_tipo_orden") : 0 ;
			
			$fechaDesdeCreacion = explode('T',$request->query->get('doc_desde_creacion'));
			$fechaHastaCreacion = explode('T',$request->query->get('doc_hasta_creacion'));
			$desde_creacion = $fechaDesdeCreacion ? $fechaDesdeCreacion[0] : 0 ;
			$hasta_creacion = $fechaHastaCreacion ? $fechaHastaCreacion[0] : 0 ;
			
			$respuesta = new Response();
			$respuesta->headers->set('Content-Type', 'text/json');
			
			//PROCESO DE BUSQUEDA 
			$arrayVariables = array (   "login" => $login,
										"descripcion_pto" => $descripcion_pto,
										"direccion_pto" => $direccion_pto,
										"estados_pto" => $estados_pto,
										"negocios_pto" => $negocios_pto,
										"vendedor" => $vendedor,
										"identificacion" => $identificacion,
										"nombre" => $nombre,
										"apellido" => $apellido,
										"razon_social" => $razon_social,
										"direccion_grl" => $direccion_grl,
										"depende_edificio" => $depende_edificio,
										"es_edificio" => $es_edificio,
										"estados_contrato" => $estados_contrato,
										"formas_pago" => $formas_pago,
										"num_contrato" => $num_contrato,
										"es_vip" => $es_vip,
										"tipo_documento" => $tipo_documento,
										"creador_documento" => $creador_documento,
										"num_documento" => $num_documento,
										"tipo_orden" => $tipo_orden,
										"desde_creacion" => $desde_creacion,
										"hasta_creacion" => $hasta_creacion
									);

			$resultado = $em->getRepository('schemaBundle:InfoPersona')->findBusquedaAvanzadaComercial($arrayVariables, $start, $limit);
			$numTotal = $resultado['total'];

			$i=0;
			if($resultado['registros'] && count($resultado['registros']) > 0)
			{
				foreach ($resultado['registros'] as $dat):
					if($i % 2==0)
						$clase='';
					else
						$clase='k-alt';

					$fechaAprobacion="";

					if($dat['razonSocial'])
						$cliente = $dat['razonSocial'];
					else
						$cliente = $dat['nombres']." ".$dat['apellidos'];
					
					$nombreVendedor = (isset($dat["nombreVendedor"]) ? ($dat["nombreVendedor"] ? ucwords(mb_strtolower($dat["nombreVendedor"], 'UTF-8')) : "") : "");
                
					$clientes[]= array(
										'id'=> $i,
										'idClienteSucursal'=> "",
										'idPunto'=> ($dat['id_punto'] ? $dat['id_punto'] : 0),
										'numeroContrato'=> $dat['numeroContrato'],
										'identificacion'=> $dat['identificacionCliente'],
										'cliente'=> ucwords(mb_strtolower(trim($cliente), 'UTF-8')),
										'calificacion'=> $dat['calificacionCrediticia'],
										'fechaAprobacion'=> "",
										'login1'=>$dat['login'],
										'descripcionClienteSucursal'=>$dat['descripcionPunto'],
										'direccionClienteSucursal'=>$dat['direccion_pto'],
										'idEstadoPtoCliente'=>$dat['estado'],
										'estadoContrato'=>$dat['estadoContrato'],
										'opt_enlace_servicio'=>"",
										'opt_cliente_sucursal'=>"",
										'vendedor'=> trim($nombreVendedor),
										'clase'=>"",
										'action1' => (!$dat['id_punto'] ? 'icon-invisible':'button-grid-puntoCliente')
									);
					$i++;
				endforeach;
			}
				
			if (!empty($clientes))
			{
				$dataF =json_encode($clientes);
				$objJson= '{"total":"'.$numTotal.'","encontrados":'.$dataF.'}';
			}
			else
			{        
				$objJson= '{"total":"0","encontrados":[]}';
				/*
				$clientes[]= array(
									'id'=> "0",
									'idClienteSucursal'=> "",
									'numeroContrato'=> "",
									'identificacion'=> "",
									'cliente'=> "",
									'calificacion'=> "",
									'fechaAprobacion'=> "",
									'login1'=>"",
									'descripcionClienteSucursal'=>"",
									'idEstadoPtoCliente'=>"",
									'opt_enlace_servicio'=>"",
									'opt_cliente_sucursal'=>"",
									'vendedor'=>"",
									'clase'=>"",
									'action1' => (true ? 'icon-invisible':'button-grid-puntoCliente')
								);


				$dataF =json_encode($clientes[0]);
				$objJson= '{"total":"1","encontrados":'.$dataF.'}';        */     
			}
		}catch (Exception $e) {
            $em->getConnection()->close();
			$objJson= '{"total":"0","encontrados":[]}';
            //$resultado = json_encode(array('success'=>false,'mensaje'=>$e));
        }
		
        $respuesta->setContent($objJson);
        return $respuesta;
    }
    
	 /**
	* @Secure(roles="ROLE_147-121")
	*/
    public function datosPlanificacionBusquedaAction()
    {
        /*se obtiene los datos basicos del formulario para la busqueda
            * - Segun los campos llenos se hace el filtro de los datos 
            * y el mismo se lo va agregando en el query*/
        $em = $this->getDoctrine()->getManager('telconet');
        $em_general = $this->getDoctrine()->getManager("telconet_general");
        
        $clientes="";
        $request = $this->get('request');
        $peticion = $this->get('request');
        
        $start = $request->query->get('start');
        $limit = $request->query->get('limit');
		
        $fechaDesdeSolPlanif = explode('T',$peticion->query->get('plan_desde_solPlanif'));
        $fechaHastaSolPlanif = explode('T',$peticion->query->get('plan_hasta_solPlanif'));
        $fechaDesdePlanif = explode('T',$peticion->query->get('plan_desde_planif'));
        $fechaHastaPlanif = explode('T',$peticion->query->get('plan_hasta_planif'));
        $fechaDesdeAsignacion = explode('T',$peticion->query->get('plan_desde_asig'));
        $fechaHastaAsignacion = explode('T',$peticion->query->get('plan_hasta_asig'));	
        $estado =$peticion->query->get('plan_estado') ? $peticion->query->get('plan_estado') : "Todos";
		
        $parametros = array();		
        $parametros["login2"]=$peticion->query->get('login') ? $peticion->query->get('login') : "";	
        $parametros["descripcionPunto"]=$peticion->query->get('descripcion_pto') ? $peticion->query->get('descripcion_pto') : "";
        $parametros["vendedor"]=$peticion->query->get('vendedor') ? $peticion->query->get('vendedor') : "";
		
        $parametros["direccion_pto"]=$peticion->query->get('direccion_pto') ? $peticion->query->get('direccion_pto') : "";
        $parametros["estados_pto"]=$peticion->query->get('estados_pto') ? $peticion->query->get('estados_pto') : "";
        $parametros["negocios_pto"]=$peticion->query->get('negocios_pto') ? $peticion->query->get('negocios_pto') : "";
        $parametros["identificacion"]=$peticion->query->get('identificacion') ? $peticion->query->get('identificacion') : "";
        $parametros["nombre"]=$peticion->query->get('nombre') ? $peticion->query->get('nombre') : "";
        $parametros["apellido"]=$peticion->query->get('apellido') ? $peticion->query->get('apellido') : "";
        $parametros["razon_social"]=$peticion->query->get('razon_social') ? $peticion->query->get('razon_social') : "";
        $parametros["direccion_grl"]=$peticion->query->get('direccion_grl') ? $peticion->query->get('direccion_grl') : "";
        $parametros["depende_edificio"]=$peticion->query->get('depende_edificio') ? $peticion->query->get('depende_edificio') : "";
        $parametros["es_edificio"]=$peticion->query->get('es_edificio') ? $peticion->query->get('es_edificio') : "";
		
        $parametros["fechaDesdeSolPlanif"]= $fechaDesdeSolPlanif ? $fechaDesdeSolPlanif[0] : "";
        $parametros["fechaHastaSolPlanif"]= $fechaHastaSolPlanif ? $fechaHastaSolPlanif[0] : "";
        $parametros["fechaDesdePlanif"]= $fechaDesdePlanif ? $fechaDesdePlanif[0] : "";
        $parametros["fechaHastaPlanif"]= $fechaHastaPlanif ? $fechaHastaPlanif[0] : "";
        $parametros["fechaDesdeAsignacion"]= $fechaDesdeAsignacion ? $fechaDesdeAsignacion[0] : "";
        $parametros["fechaHastaAsignacion"]= $fechaHastaAsignacion ? $fechaHastaAsignacion[0] : "";
        $parametros["ciudad"]=$peticion->query->get('plan_ciudad') ? $peticion->query->get('plan_ciudad') : "";
        $parametros["numOrdenServicio"]=$peticion->query->get('plan_numOrdenServicio') ? $peticion->query->get('plan_numOrdenServicio') : "";
        $parametros["estado"]=$peticion->query->get('plan_estado') ? $peticion->query->get('plan_estado') : "Todos";
        $parametros["tipoResponsable"]=$peticion->query->get('plan_tipoResponsable') ? $peticion->query->get('plan_tipoResponsable') : "todos";
        $parametros["codigoResponsable"]=$peticion->query->get('plan_codigoResponsable') ? $peticion->query->get('plan_codigoResponsable') : 0;
	
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
		
		//BUSQUEDA EN SI
        $estado = ($estado ? $estado : "Todos");
        if($estado == "Todos")
        {
            $registrosTotal = array();
            $registros = array();
            
            $num1 = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoDetalleSolicitud')
						 ->getRegistrosReporteGeneral('count', '', '', "TODOS-Planifica", $parametros);        
            $registros1 = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoDetalleSolicitud')
							   ->getRegistrosReporteGeneral('datos', $start, $limit, "TODOS-Planifica", $parametros);        
            if($registros1 && count($registros1)>0)
            {
                foreach($registros1 as $data1)
                {
                    $registros[] = $data1;
                }
            }
            
            $num2 = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoDetalleSolicitud')
						 ->getRegistrosReporteGeneral('count', '', '', "Planificada", $parametros);
            $registros2 = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoDetalleSolicitud')
							   ->getRegistrosReporteGeneral('datos', $start, $limit, "Planificada", $parametros);        
            if($registros2 && count($registros2)>0)
            {
                foreach($registros2 as $data2)
                {
                    $registros[] = $data2;
                }
            }
            
            $num3 = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoDetalleSolicitud')
						 ->getRegistrosReporteAsignadas('count', '', '', "", $parametros);
            $registros3 = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoDetalleSolicitud')
							   ->getRegistrosReporteAsignadas('datos', $start, $limit, "", $parametros);        
            if($registros3 && count($registros3)>0)
            {
                foreach($registros3 as $data3)
                {
                    $registros[] = $data3;
                }
            }
			
            $num = ($num1 ? $num1 : 0) + ($num2 ? $num2 : 0) + ($num3 ? $num3 : 0);
        }
        else if($estado == "PrePlanificada" || $estado == "Planificada" || $estado == "Detenido" || $estado == "Anulado" || $estado == "Rechazada")
        {							
            $num = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoDetalleSolicitud')
						->getRegistrosReporteGeneral('count', '', '', $estado, $parametros);
            $registros = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoDetalleSolicitud')
							  ->getRegistrosReporteGeneral('datos', $start, $limit, $estado, $parametros);        
        }      
        else if($estado == "Asignada" || $estado == "Finalizada")
        {
			$num = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoDetalleSolicitud')
						->getRegistrosReporteAsignadas('count', '', '', $estado, $parametros);
			$registros = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoDetalleSolicitud')
						      ->getRegistrosReporteAsignadas('datos', $start, $limit, $estado, $parametros);           
		}
		
		
		
        if ($registros) {            
            foreach ($registros as $data)
            {       
				$nombreProductoPlan = "";
				if(isset($data["idServicio"]))
				{
					if($data["idServicio"])
					{
		                $Servicio = $em->getRepository('schemaBundle:InfoServicio')->findOneById($data["idServicio"]); 
		                $nombreProducto =  ($Servicio->getProductoId() ? $Servicio->getProductoId()->getDescripcionProducto() : "");  
		                $nombrePlan =  ($Servicio->getPlanId() ? $Servicio->getPlanId()->getNombrePlan() : "");  
		                $nombreProductoPlan = $nombreProducto . $nombrePlan;
					}
				}
				
                $nombreVendedor = (isset($data["nombreVendedor"]) ?  ($data["nombreVendedor"] ? $data["nombreVendedor"] : "") : "");
                
                $nombreSector =  (isset($data["nombreSector"]) ? ($data["nombreSector"] ? $data["nombreSector"]  : "") : "");
                $parroquia =  (isset($data["nombreParroquia"]) ? ($data["nombreParroquia"] ? $data["nombreParroquia"]  : "") : "");
                $ciudad =  (isset($data["nombreCanton"]) ? ($data["nombreCanton"] ? $data["nombreCanton"]  : "") : "");
                $cliente = (isset($data["razonSocial"]) ||  isset($data["nombres"]) ? ($data["razonSocial"] ? $data["razonSocial"] : $data["nombres"] . " " . $data["apellidos"]) : "");
                $coordenadas = (isset($data["longitud"]) && isset($data["latitud"]) ? $data["longitud"] . ", ". $data["latitud"] : "");				          
                $latitud =  (isset($data["latitud"]) ? ($data["latitud"] ? $data["latitud"]  : "") : "");
                $longitud =  (isset($data["longitud"]) ? ($data["longitud"] ? $data["longitud"]  : ""): "");
				
                $feSolicitaPlanificacion = (isset($data["feCreacion"]) ? ($data["feCreacion"] ? strval(date_format($data["feCreacion"],"d/m/Y G:i")) : "" ) : "");    
                $feAsignada = (isset($data["feAsignada"]) ? ($data["feAsignada"] ? strval(date_format($data["feAsignada"],"d/m/Y G:i")) : "") : "");  
                $feAsignadaDetalle = (isset($data["feAsignadaDetalle"]) ? ($data["feAsignadaDetalle"] ? strval(date_format($data["feAsignadaDetalle"],"d/m/Y G:i")) : "") : "");    
				
                $fechaPlanificacionReal = "";
                $fePlanificada = "";
                $HoraIniPlanificada = "";
                $HoraFinPlanificada = "";
                $nombrePlanifica = "";
                if( strtoupper($data["estado"])==strtoupper("Planificada") || strtoupper($data["estado"])==strtoupper("Replanificada")  ||  
					strtoupper($data["estado"])==strtoupper("Asignada") || strtoupper($data["estado"])==strtoupper("Finalizada")
				  )
                {
					$fePlanificada = (isset($data["feIniPlan"]) ? ($data["feIniPlan"] ? strval(date_format($data["feIniPlan"],"d/m/Y")) : "") : ""); 
					$HoraIniPlanificada = (isset($data["feIniPlan"]) ? ($data["feIniPlan"] ? strval(date_format($data["feIniPlan"],"h:i")) : "") : "");  
					$HoraFinPlanificada = (isset($data["feFinPlan"]) ? ($data["feFinPlan"] ? strval(date_format($data["feFinPlan"],"h:i")) : "") : ""); 
                    
                    //$usrPlanifica =  ($data["usrPlanifica"] ? $data["usrPlanifica"] : "");
					if(isset($data["usrPlanifica"]))
					{
	                    if($data["usrPlanifica"] && $data["usrPlanifica"]!="")
	                    {
	                        $entityPersona = $em->getRepository('schemaBundle:InfoPersona')->findOneByLogin($data["usrPlanifica"]); 
	                        $nombrePlanifica = ($entityPersona ? $entityPersona->getNombres() . " " . $entityPersona->getApellidos() : "");
	                    }
					}
                
                    $fechaPlanificacionReal = $fePlanificada . " (" . $HoraIniPlanificada . " - " . $HoraFinPlanificada . ")";
                }  
					
                $nombreAsigna = ""; $asignadoNombre= ""; $refAsignadoNombre= ""; $Asignado=""; $nombreTarea ="";
                if( strtoupper($data["estado"])==strtoupper("Asignada") || strtoupper($data["estado"])==strtoupper("Finalizada") )
                {
					if(isset($data["usrAsigna"]))
					{
						if($data["usrAsigna"] && $data["usrAsigna"]!="")
						{
							$entityPersona = $em->getRepository('schemaBundle:InfoPersona')->findOneByLogin($data["usrAsigna"]); 
							$nombreAsigna = ($entityPersona ? $entityPersona->getNombres() . " " . $entityPersona->getApellidos() : "");
						}
					}
						
	                $asignadoNombre =  (isset($data["asignadoNombre"]) ? ($data["asignadoNombre"] ? $data["asignadoNombre"]  : "") : "");  
	                $refAsignadoNombre =  (isset($data["refAsignadoNombre"]) ? ($data["refAsignadoNombre"] ? $data["refAsignadoNombre"]  : "") : "");  
					$Asignado = ($asignadoNombre ? $asignadoNombre . ($refAsignadoNombre ? " - " . $refAsignadoNombre: "") : "" . ($refAsignadoNombre ? $refAsignadoNombre: "") );
					
	                $nombreTarea =  (isset($data["nombreTarea"]) ? ($data["nombreTarea"] ? $data["nombreTarea"]  : "") : ""); 
				}
                
                $nombreMotivo = "";
				if(isset($data["motivoId"]))
				{
					if($data["motivoId"] && $data["motivoId"]!="")
	                {
	                    $EntityMotivo = $em_general->getRepository('schemaBundle:AdmiMotivo')->findOneById($data["motivoId"]);
	                    $nombreMotivo =  ($EntityMotivo ? ($EntityMotivo->getNombreMotivo() ? $EntityMotivo->getNombreMotivo() : "") : "");  
	                }
				}
					
				$idDetalleSolicitud =  (isset($data["idDetalleSolicitud"]) ? ($data["idDetalleSolicitud"] ? $data["idDetalleSolicitud"]  : 0) : 0); 
				$idServicio =  (isset($data["idServicio"]) ? ($data["idServicio"] ? $data["idServicio"]  : 0) : 0); 
				$idPunto =  (isset($data["idPunto"]) ? ($data["idPunto"] ? $data["idPunto"]  : 0) : 0); 
				$idOrdenTrabajo =  (isset($data["idOrdenTrabajo"]) ? ($data["idOrdenTrabajo"] ? $data["idOrdenTrabajo"]  : 0) : 0); 
				$idDetalle =  (isset($data["idDetalle"]) ? ($data["idDetalle"] ? $data["idDetalle"]  : 0) : 0); 
				$idDetalleAsignacion =  (isset($data["idDetalleAsignacion"]) ? ($data["idDetalleAsignacion"] ? $data["idDetalleAsignacion"]  : 0) : 0);				
				$numeroOrdenTrabajo =  (isset($data["numeroOrdenTrabajo"]) ? ($data["numeroOrdenTrabajo"] ? $data["numeroOrdenTrabajo"]  : "") : "");
				
                $arr_encontrados[]=array(
                                         'id_factibilidad' =>$idDetalleSolicitud,
                                         'id_servicio' =>$idServicio,
                                         'id_punto' =>$idPunto,
                                         'id_orden_trabajo' =>$idOrdenTrabajo,
                                         'id_detalle' => $idDetalle,
                                         'id_detalle_asignacion' =>$idDetalleAsignacion, 
                                         'num_orden_trabajo' =>$numeroOrdenTrabajo, 
                                         'cliente' =>ucwords(mb_strtolower(trim($cliente), 'UTF-8')),
                                         'vendedor' =>ucwords(mb_strtolower(trim($nombreVendedor), 'UTF-8')),
                                         'login2' =>trim($data["login"]),
                                         'producto' =>trim($nombreProductoPlan),
                                         'coordenadas' =>trim($coordenadas),
                                         'direccion' =>trim($data["direccion"]),
                                         'ciudad' =>ucwords(mb_strtolower(trim($ciudad), 'UTF-8')),
                                         'nombreSector' =>ucwords(mb_strtolower(trim($nombreSector), 'UTF-8')),
                                         'latitud' =>trim($latitud),
                                         'longitud' =>trim($longitud),
                                         'feSolicitaPlanificacion' =>trim($feSolicitaPlanificacion),
                                         'fechaPlanificacionReal' =>trim($fechaPlanificacionReal),
                                         'fePlanificada' =>trim($fePlanificada),
                                         'HoraIniPlanificada' =>trim($HoraIniPlanificada),
                                         'HoraFinPlanificada' =>trim($HoraFinPlanificada),
                                         'feAsignada' =>trim($feAsignada),
                                         'usrPlanifica' =>trim($nombrePlanifica),
                                         'usrAsigna' =>trim($nombreAsigna),
                                         'nombreTarea' =>trim($nombreTarea),
                                         'nombreAsignado' =>trim($Asignado),
                                         'motivo' =>trim($nombreMotivo), 
                                         'estado' =>trim($data["estado"]),
										 'action1' => (!$idPunto ? 'icon-invisible':'button-grid-puntoCliente'),
										 'action2' => (!$idOrdenTrabajo ? 'icon-invisible':'button-grid-puntoCliente')
                                        );
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_factibilidad' => 0 , 'nombre_factibilidad' => 'Ninguno', 'factibilidad_id' => 0 , 'factibilidad_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $objJson = json_encode( $resultado);
            }
            else
            {
                $dataF =json_encode($arr_encontrados);
                $objJson= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
            }
        }
        else
        {
            $objJson= '{"total":"0","encontrados":[]}';        
		}
		
		
        $respuesta->setContent($objJson);
        return $respuesta;
	}

	/**
	* @Secure(roles="ROLE_147-122")
	*/
    public function datosFinancieroBusquedaAction()
    {
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $em = $this->getDoctrine()->getManager('telconet_financiero');
			
        $em->getConnection()->beginTransaction();
		try
		{	
			$respuesta = new Response();
			$respuesta->headers->set('Content-Type', 'text/json');
			
			$peticion = $this->get('request');
			$session = $peticion->getSession();
			
			$login = $peticion->query->get("login") ? $peticion->query->get("login") : "" ;        
			$descripcion_pto = $peticion->query->get("descripcion_pto") ? $peticion->query->get("descripcion_pto") : "" ;
			$direccion_pto = $peticion->query->get("direccion_pto") ? $peticion->query->get("direccion_pto") : "" ;
			$estados_pto = $peticion->query->get("estados_pto") ? $peticion->query->get("estados_pto") : "" ;
			$negocios_pto = $peticion->query->get("negocios_pto") ? $peticion->query->get("negocios_pto") : "" ;
			$vendedor = $peticion->query->get("vendedor") ? $peticion->query->get("vendedor") : "" ;
			$identificacion = $peticion->query->get("identificacion") ? $peticion->query->get("identificacion") : "" ;
			$nombre = $peticion->query->get("nombre") ? $peticion->query->get("nombre") : "" ;
			$apellido = $peticion->query->get("apellido") ? $peticion->query->get("apellido") : "" ;
			$razon_social = $peticion->query->get("razon_social") ? $peticion->query->get("razon_social") : "" ;
			$direccion_grl = $peticion->query->get("direccion_grl") ? $peticion->query->get("direccion_grl") : "" ;
			$depende_edificio = $peticion->query->get("depende_edificio") ? $peticion->query->get("depende_edificio") : 0 ;
			$es_edificio = $peticion->query->get("es_edificio") ? $peticion->query->get("es_edificio") : 0 ;
			
			$parametros = array (   "login" => $login,
									"descripcion_pto" => $descripcion_pto,
									"direccion_pto" => $direccion_pto,
									"estados_pto" => $estados_pto,
									"negocios_pto" => $negocios_pto,
									"vendedor" => $vendedor,
									"identificacion" => $identificacion,
									"nombre" => $nombre,
									"apellido" => $apellido,
									"razon_social" => $razon_social,
									"direccion_grl" => $direccion_grl,
									"depende_edificio" => $depende_edificio,
									"es_edificio" => $es_edificio
								);

		
			$parametros['fin_tipoDocumento'] = $fin_tipoDocumento = $peticion->query->get('fin_doc_tipoDocumento') ? $peticion->query->get('fin_doc_tipoDocumento') : '';
			
			$parametros['doc_numDocumento'] = $peticion->query->get('fin_doc_numDocumento') ? $peticion->query->get('fin_doc_numDocumento') : '';
			$parametros['doc_creador'] = $peticion->query->get('fin_doc_creador') ? $peticion->query->get('fin_doc_creador') : '';
			$parametros['doc_estado'] = $peticion->query->get('fin_doc_estado') ? $peticion->query->get('fin_doc_estado') : '';
			$parametros['doc_monto'] = $peticion->query->get('fin_doc_monto') ? $peticion->query->get('fin_doc_monto') : 0.00 ;
			$parametros['doc_montoFiltro'] = $peticion->query->get('fin_doc_montoFiltro') ? $peticion->query->get('fin_doc_montoFiltro') : 'i';
			$doc_fechaCreacionDesde = explode('T',$peticion->query->get('fin_doc_fechaCreacionDesde'));
			$doc_fechaCreacionHasta = explode('T',$peticion->query->get('fin_doc_fechaCreacionHasta'));
			$doc_fechaEmisionDesde = explode('T',$peticion->query->get('fin_doc_fechaEmisionDesde'));
			$doc_fechaEmisionHasta = explode('T',$peticion->query->get('fin_doc_fechaEmisionHasta'));		
			$parametros['doc_fechaCreacionDesde'] = $doc_fechaCreacionDesde ? $doc_fechaCreacionDesde[0] : 0 ;
			$parametros['doc_fechaCreacionHasta'] = $doc_fechaCreacionHasta ? $doc_fechaCreacionHasta[0] : 0 ;
			$parametros['doc_fechaEmisionDesde'] = $doc_fechaEmisionDesde ? $doc_fechaEmisionDesde[0] : 0 ;
			$parametros['doc_fechaEmisionHasta'] = $doc_fechaEmisionHasta ? $doc_fechaEmisionHasta[0] : 0 ;
			
			$parametros['pag_numDocumento'] = $peticion->query->get('fin_pag_numDocumento') ? $peticion->query->get('fin_pag_numDocumento') : '';
			$parametros['pag_numReferencia'] = $peticion->query->get('fin_pag_numReferencia') ? $peticion->query->get('fin_pag_numReferencia') : '';
			$parametros['pag_numDocumentoRef'] = $peticion->query->get('fin_pag_numDocumentoRef') ? $peticion->query->get('fin_pag_numDocumentoRef') : '';
			$parametros['pag_creador'] = $peticion->query->get('fin_pag_creador') ? $peticion->query->get('fin_pag_creador') : '';
			$parametros['pag_formaPago'] = $peticion->query->get('fin_pag_formaPago') ? $peticion->query->get('fin_pag_formaPago') : '';
			$parametros['pag_banco'] = $peticion->query->get('fin_pag_banco') ? $peticion->query->get('fin_pag_banco') : '';
			$parametros['pag_estado'] = $peticion->query->get('fin_pag_estado') ? $peticion->query->get('fin_pag_estado') : '';
			$pag_fechaCreacionDesde = explode('T',$peticion->query->get('fin_pag_fechaCreacionDesde'));
			$pag_fechaCreacionHasta = explode('T',$peticion->query->get('fin_pag_fechaCreacionHasta'));
			$parametros['pag_fechaCreacionDesde'] = $pag_fechaCreacionDesde ? $pag_fechaCreacionDesde[0] : 0 ;
			$parametros['pag_fechaCreacionHasta'] = $pag_fechaCreacionHasta ? $pag_fechaCreacionHasta[0] : 0 ;
		
			$start = $peticion->query->get('start');
			$limit = $peticion->query->get('limit');
			
			$oficinaId = $peticion->getSession()->get('idOficina');
			$empresaId = $peticion->getSession()->get('idEmpresa'); 
			if($fin_tipoDocumento == 'FAC' || $fin_tipoDocumento == 'FACP' || $fin_tipoDocumento == 'NC' || $fin_tipoDocumento == 'ND')
			{
				$resultado = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->findBusquedaAvanzadaFinanciera($parametros, $empresaId, $oficinaId, $start, $limit);
			}
			else if($fin_tipoDocumento == 'PAG' || $fin_tipoDocumento == 'ANT' || $fin_tipoDocumento == 'ANTS')
			{
				$resultado = $em->getRepository('schemaBundle:InfoPagoCab')->findBusquedaAvanzadaFinanciera($parametros, $empresaId, $oficinaId, $start, $limit);
			}
			$numTotal = $resultado['total'];

			/*
								ipc.id as id_documento, ipd.id as id_documento_detalle, ipc.oficinaId, ipc.numeroPago as numeroDocumento, 
								ipc.valorTotal as valorTotalGlobal, ipc.estadoPago as estadoDocumentoGlobal, 
								ipd.feCreacion, ipd.valorPago as valorTotal, ipd.depositado, ipd.bancoTipoCuentaId, ipd.bancoCtaContableId, 
								ipd.referenciaId, ipd.numeroReferencia, ipd.numeroCuentaBanco, ipd.usrCreacion, 
								fp.id as id_forma_pago, fp.descripcionFormaPago, fp.esDepositable, 
								atdf.codigoTipoDocumento, atdf.nombreTipoDocumento, 
								pun.id as id_punto, pun.login, pun.direccion as direccion_pto, pun.descripcionPunto, pun.estado, pun.usrVendedor, 
								per.id, per.identificacionCliente, per.nombres, per.apellidos, per.razonSocial, 
								per.direccion as direccion_grl, per.calificacionCrediticia, 
								CONCAT(peVend.nombres,CONCAT(' ',peVend.apellidos)) as nombreVendedor 
								
								
								ipc.id as id_documento, ipd.id as id_documento_detalle, ipc.oficinaId, ipc.numeroPago as numeroDocumento, 
								ipc.valorTotal as valorTotalGlobal, ipc.estadoPago as estadoDocumentoGlobal, 
								ipd.feCreacion, ipd.valorPago as valorTotal, ipd.depositado, ipd.bancoTipoCuentaId, ipd.bancoCtaContableId, 
								ipd.referenciaId, ipd.numeroReferencia, ipd.numeroCuentaBanco, ipd.usrCreacion, 
								fp.id as id_forma_pago, fp.descripcionFormaPago, fp.esDepositable, 
								atdf.codigoTipoDocumento, atdf.nombreTipoDocumento 
								
								
							idfc.id as id_documento, idfc.oficinaId, idfc.numeroFacturaSri as numeroDocumento, idfc.valorTotal, idfc.estadoImpresionFact as estadoDocumentoGlobal,
							idfc.esAutomatica, idfc.feCreacion, idfc.usrCreacion, atdf.codigoTipoDocumento, atdf.nombreTipoDocumento, 
							pun.id as id_punto, 
							pun.login, pun.direccion as direccion_pto, pun.descripcionPunto, pun.estado, pun.usrVendedor, 
							per.id, per.identificacionCliente, per.nombres, per.apellidos, per.razonSocial, 
							per.direccion as direccion_grl, per.calificacionCrediticia, 
							CONCAT(peVend.nombres,CONCAT(' ',peVend.apellidos)) as nombreVendedor 
							*/
								
								
			$i=0;
			if($resultado['registros'] && count($resultado['registros']) > 0)
			{
				foreach ($resultado['registros'] as $dat):
					if($i % 2==0)
						$clase='';
					else
						$clase='k-alt';
						
					$valorTotal= ($dat['valorTotal'] ? $dat['valorTotal'] : 0.00);	
					$valorTotal = number_format($valorTotal, 2, '.', '');
					setlocale(LC_MONETARY, 'en_US');
					// $valorTotal = money_format('%#10n', $valorTotal);

					$razonSocial = (isset($dat['razonSocial']) ? ($dat['razonSocial'] ? $dat['razonSocial'] : "") : "");
					$nombres = (isset($dat['nombres']) || isset($dat['apellidos']) ? ($dat['nombres'] ? $dat['nombres'] . " " . $dat['apellidos'] : "") : "");
					$informacion_cliente = ($razonSocial && $razonSocial != "" ? $razonSocial : $nombres);
					
					$automatica = isset($dat['esAutomatica']) ? ($dat['esAutomatica']=="S" ? "Si" : "No") : '';
					$nombreVendedor = (isset($dat["nombreVendedor"]) ? ($dat["nombreVendedor"] ? ucwords(mb_strtolower($dat["nombreVendedor"], 'UTF-8')) : "") : "");
               
					$referencia1 = ''; $referencia2 = ''; $referencia='';
					if(isset($dat["numeroCuentaBanco"]))
						$referencia1 = $dat["numeroCuentaBanco"];
					if(isset($dat["numeroReferencia"]))
						$referencia2 = $dat["numeroReferencia"];
					
					$referencia = ($referencia1 ? $referencia1 . " " : "") . ($referencia2 ? $referencia2 . " " : ""); 
					
					$nombreBanco  = "";
					if(isset($dat["bancoTipoCuentaId"]))
					{
						$bancoTipoCuentaId = $dat["bancoTipoCuentaId"];
						$entityBancoTipoCuenta = $emComercial->getRepository('schemaBundle:AdmiBancoTipoCuenta')->findOneById($bancoTipoCuentaId);
						if($entityBancoTipoCuenta && count($entityBancoTipoCuenta)>0)
						{
							$entityBanco = $entityBancoTipoCuenta->getBancoId();
							$nombreBanco = ($entityBanco ? ($entityBanco->getDescripcionBanco() ? $entityBanco->getDescripcionBanco() : "") : "");
						}
					}
					if(isset($dat["bancoCtaContableId"]))
					{
						$bancoCtaContableId = $dat["bancoCtaContableId"];
						$entityBancoCtaContable = $em->getRepository('schemaBundle:AdmiBancoCtaContable')->findOneById($bancoCtaContableId);						
						if($entityBancoCtaContable && count($entityBancoCtaContable)>0)
						{							
							$entityBancoTipoCuenta = $entityBancoCtaContable->getBancoTipoCuentaId();
							if($entityBancoTipoCuenta && count($entityBancoTipoCuenta)>0)
							{
								$entityBanco = $entityBancoTipoCuenta->getBancoId();
								$nombreBanco = ($entityBanco ? ($entityBanco->getDescripcionBanco() ? $entityBanco->getDescripcionBanco() : "") : "");
							}
						}
					}					
					$nombreBanco = ucwords(mb_strtolower(trim($nombreBanco), 'UTF-8'));					
				
					$referenciaId = ""; $noDocumentoReferencia  = ""; $codigoDocumentoReferencia  = ""; $nombreDocumentoReferencia  = "";
					if(isset($dat["referenciaId"]))
					{
						if($dat["referenciaId"] && $dat["referenciaId"]!="")
						{
							$referenciaId = $dat["referenciaId"];
							$entityReferencia = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->findOneById($referenciaId);
							if($entityReferencia && count($entityReferencia)>0)
							{
								$noDocumentoReferencia = ($entityReferencia ? ($entityReferencia->getNumeroFacturaSri() ? $entityReferencia->getNumeroFacturaSri() : "") : "");
								
								$tipoDocumentoReferenciaId = ($entityReferencia ? ($entityReferencia->getTipoDocumentoId() ? $entityReferencia->getTipoDocumentoId() : "") : "");
								$entityTipoDocumento = $em->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')->findOneById($tipoDocumentoReferenciaId);								
								if($entityTipoDocumento && count($entityTipoDocumento)>0)
								{
									$codigoDocumentoReferencia = ($entityTipoDocumento ? ($entityTipoDocumento->getCodigoTipoDocumento() ? $entityTipoDocumento->getCodigoTipoDocumento() : "") : "");
									$nombreDocumentoReferencia = ($entityTipoDocumento ? ($entityTipoDocumento->getNombreTipoDocumento() ? $entityTipoDocumento->getNombreTipoDocumento() : "") : "");
								}
							}//fin entityReferencia
						}//fin referenciaId
					}
					
					$nombreCreador = "Migracion";
					$empleado = $emComercial->getRepository('schemaBundle:InfoPersona')->getPersonaPorLogin($dat["usrCreacion"]);
					if($empleado && count($empleado)>0)
					{
						$nombreCreador = $empleado->getNombres().' '.$empleado->getApellidos();
					}
					$nombreCreador = ucwords(mb_strtolower(trim($nombreCreador), 'UTF-8'));
					$nombreVendedor = ucwords(mb_strtolower(trim($nombreVendedor), 'UTF-8'));
			
					if(isset($dat['feDeposito']))
					{
						if($dat['feDeposito']!="")
							$fecha_deposito=strval(date_format($dat['feDeposito'],"d/m/Y G:i"));
						else
							$fecha_deposito="";
					}
					else
						$fecha_deposito="";
						
					if(isset($dat['feProcesado']))
					{
						if($dat['feProcesado']!="")
							$fecha_procesado=strval(date_format($dat['feProcesado'],"d/m/Y G:i"));
						else
							$fecha_procesado="";
					}
					else
						$fecha_procesado="";
						
					if(isset($dat['noComprobanteDeposito']))
					{
						if($dat['noComprobanteDeposito']!="")
							$no_comprobante_deposito=$dat['noComprobanteDeposito'];
						else
							$no_comprobante_deposito="";
					}
					else
						$no_comprobante_deposito="";
						
					//Entidad oficina - para la presentacion en el pago
					$oficinaId=$emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($dat['oficinaId']);
					
					//Para la presentacion de la oficina segun pago o facturas
					$oficina_presentar="";
					
					if($fin_tipoDocumento == 'PAG' || $fin_tipoDocumento == 'ANT' || $fin_tipoDocumento == 'ANTS')
						$oficina_presentar=$oficinaId->getNombreOficina();
					else
						$oficina_presentar=$dat['nombreOficina'];
						
					if(isset($dat['feEmision']))
					{
						if($dat['feEmision']!="")
							$fecha_emision=strval(date_format($dat['feEmision'],"d/m/Y G:i"));
						else
							$fecha_emision="";
					}
					else
						$fecha_emision="";
						
					$referencia_nd="";
					$comentario_nd="";
						
					if($fin_tipoDocumento=='ND')
					{
						//saco con el id_documento el det y el pago_det_id
						//obtengo el numero_pago y lo pongo en la referencia
						$referencia_nd="";
						$comentario_nd="";
						$nd_det = $em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($dat['id_documento']);
						foreach ($nd_det as $nd):
							$pago_det=$em->getRepository('schemaBundle:InfoPagoDet')->find($nd->getPagoDetId());
							$referencia_nd.="|".$pago_det->getPagoId()->getNumeroPago();
							$comentario_nd.="|".$nd->getObservacionesFacturaDetalle();
						endforeach;
						
						$referencia=$referencia_nd;
						
						//'comentarioDetallePago'=>(isset($dat['comentarioDetallePago']) ? $dat['comentarioDetallePago'] : ""),
					}
					
					
					$documentos[]= array(
						'id'=> $i,
						'idClienteSucursal'=> "",
						'oficinaId'=>$oficina_presentar,
						'idPunto'=> (isset($dat['id_punto']) ? ($dat['id_punto'] ? $dat['id_punto'] : 0) : 0),
						'identificacion'=> (isset($dat['identificacionCliente']) ? $dat['identificacionCliente'] : ""),
						'calificacion'=> (isset($dat['calificacionCrediticia']) ? $dat['calificacionCrediticia'] : ""),
						'fechaAprobacion'=> "",
						'login1'=> (isset($dat['login']) ? $dat['login'] : ""),
						'Punto'=> (isset($dat['descripcionPunto']) ? ucwords(mb_strtolower(trim($dat['descripcionPunto']), 'UTF-8')) : ""),
						'idEstadoPtoCliente'=> (isset($dat['estado']) ? $dat['estado'] : ""),
						'opt_enlace_servicio'=>"",
						'opt_cliente_sucursal'=>"",
						'vendedor'=> trim($nombreVendedor),
						'idDocumento'=>$dat['id_documento'],
						'idDocumentoDetalle'=>(isset($dat['id_documento_detalle']) ? $dat['id_documento_detalle'] : ""),
						'comentarioPago'=>(isset($dat['comentarioPago']) ? $dat['comentarioPago'] : ""),	
						'comentarioDetallePago'=>(isset($dat['comentarioDetallePago']) ? $dat['comentarioDetallePago'] : $comentario_nd),		
						'codigoTipoDocumento'=>$dat['codigoTipoDocumento'],
						'nombreTipoDocumento'=>$dat['nombreTipoDocumento'],
						'NumeroDocumento'=>$dat['numeroDocumento'],
						'descripcionFormaPago'=>(isset($dat['descripcionFormaPago']) ? ucwords(mb_strtolower($dat['descripcionFormaPago'], 'UTF-8')) : ""),
						'Cliente'=>ucwords(mb_strtolower(trim($informacion_cliente), 'UTF-8')),
						'Esautomatica'=>$automatica,
						'ValorTotal'=>$valorTotal,
						'referencia'=> $referencia,
						'nombreBanco'=> trim($nombreBanco),
						'referenciaId'=>$referenciaId,
						'NumeroDocumentoRef'=>$noDocumentoReferencia,
						'NombreDocumentoRef'=>trim($nombreDocumentoReferencia),
						'CodigoDocumentoRef'=>trim($codigoDocumentoReferencia),
						'nombreCreador'=> $nombreCreador,
						'Estado'=>$dat['estadoDocumentoGlobal']?$dat['estadoDocumentoGlobal']:'',
						'Fecreacion'=> strval(date_format($dat['feCreacion'],"d/m/Y G:i")),
						'Feemision'=> $fecha_emision,
						'Fedeposito'=> $fecha_deposito,
						'Feprocesado'=>$fecha_procesado,
						'NoComprobanteDeposito'=>$no_comprobante_deposito,
						'clase'=>$clase,
						'action1'=>'button-grid-show',
						'boton'=>"",
						'tipoNegocio'=>$dat['codigoTipoNegocio']
					);
					$i++;
				endforeach;
			}
			
			
			if (!empty($documentos))
			{
				$dataF =json_encode($documentos);
				$objJson= '{"total":"'.$numTotal.'","encontrados":'.$dataF.'}';
			}
			else
			{        
				$objJson= '{"total":"0","encontrados":[]}';
			}	
			
		}catch (Exception $e) {
            $em->getConnection()->close();
			$objJson= '{"total":"0","encontrados":[]}';
            //$resultado = json_encode(array('success'=>false,'mensaje'=>$e));
        }
		
        $respuesta->setContent($objJson);
        return $respuesta;	
	}
	
	/**
	* @Secure(roles="ROLE_147-123")
	*/
    public function datosTecnicosBusquedaAction()
    {
	}
	
	/**
	* @Secure(roles="ROLE_147-364")
	*/
    public function datosSoporteBusquedaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $session = $peticion->getSession();
		
		$login = $peticion->query->get("login") ? $peticion->query->get("login") : "" ;        
		$descripcion_pto = $peticion->query->get("descripcion_pto") ? $peticion->query->get("descripcion_pto") : "" ;
		$direccion_pto = $peticion->query->get("direccion_pto") ? $peticion->query->get("direccion_pto") : "" ;
		$estados_pto = $peticion->query->get("estados_pto") ? $peticion->query->get("estados_pto") : "" ;
		$negocios_pto = $peticion->query->get("negocios_pto") ? $peticion->query->get("negocios_pto") : "" ;
		$vendedor = $peticion->query->get("vendedor") ? $peticion->query->get("vendedor") : "" ;
		$identificacion = $peticion->query->get("identificacion") ? $peticion->query->get("identificacion") : "" ;
		$nombre = $peticion->query->get("nombre") ? $peticion->query->get("nombre") : "" ;
		$apellido = $peticion->query->get("apellido") ? $peticion->query->get("apellido") : "" ;
		$razon_social = $peticion->query->get("razon_social") ? $peticion->query->get("razon_social") : "" ;
		$direccion_grl = $peticion->query->get("direccion_grl") ? $peticion->query->get("direccion_grl") : "" ;
		$depende_edificio = $peticion->query->get("depende_edificio") ? $peticion->query->get("depende_edificio") : 0 ;
		$es_edificio = $peticion->query->get("es_edificio") ? $peticion->query->get("es_edificio") : 0 ;
		
		$parametros = array (   "login" => $login,
								"descripcion_pto" => $descripcion_pto,
								"direccion_pto" => $direccion_pto,
								"estados_pto" => $estados_pto,
								"negocios_pto" => $negocios_pto,
								"vendedor" => $vendedor,
								"identificacion" => $identificacion,
								"nombre" => $nombre,
								"apellido" => $apellido,
								"razon_social" => $razon_social,
								"direccion_grl" => $direccion_grl,
								"depende_edificio" => $depende_edificio,
								"es_edificio" => $es_edificio
							);
		
        $parametros['numero'] = $peticion->query->get('sop_numero');
        $parametros['estado'] = $peticion->query->get('sop_estado');
        $parametros['tituloInicial'] = $peticion->query->get('sop_tituloInicial');
        $parametros['versionInicial'] = $peticion->query->get('sop_versionInicial');
        $parametros['tituloFinal'] = $peticion->query->get('sop_tituloFinal');
        $parametros['tituloFinalHip'] = $peticion->query->get('sop_tituloFinalHip');
        $parametros['versionFinal'] = $peticion->query->get('sop_versionFinal');
        $parametros['nivelCriticidad'] = $peticion->query->get('sop_nivelCriticidad');
        $parametros['tipoCaso'] = $peticion->query->get('sop_tipoCaso');
        $parametros['usrApertura'] = $peticion->query->get('sop_usrApertura');
        $parametros['usrCierre'] = $peticion->query->get('sop_usrCierre');
        
		$varSessionCliente = ($session->get('cliente') ? $session->get('cliente') : "");
		$varSessionPtoCliente = ($session->get('ptoCliente') ? $session->get('ptoCliente') : "");
		$nombreClienteAfectado = ($varSessionCliente ? ($varSessionCliente['razon_social'] ? $varSessionCliente['razon_social'] : $varSessionCliente['nombres'] . " " . $varSessionCliente['apellidos']) : "");  
		$loginPuntoCliente = ($varSessionPtoCliente ? ($varSessionPtoCliente['login'] ? $varSessionPtoCliente['login'] : "") : ""); 
		
        $parametros['clienteAfectado'] = ($nombreClienteAfectado ? $nombreClienteAfectado : $peticion->query->get('sop_clienteAfectado'));
        $parametros['loginAfectado'] = ($loginPuntoCliente ? $loginPuntoCliente : $peticion->query->get('sop_loginAfectado'));
		
		$feAperturaDesde = explode('T',$peticion->query->get('sop_feAperturaDesde'));
		$feAperturaHasta = explode('T',$peticion->query->get('sop_feAperturaHasta'));
		$feCierreDesde = explode('T',$peticion->query->get('sop_feCierreDesde'));
		$feCierreHasta = explode('T',$peticion->query->get('sop_feCierreHasta'));		
		$parametros['feAperturaDesde'] = $feAperturaDesde ? $feAperturaDesde[0] : 0 ;
		$parametros['feAperturaHasta'] = $feAperturaHasta ? $feAperturaHasta[0] : 0 ;
		$parametros['feCierreDesde'] = $feCierreDesde ? $feCierreDesde[0] : 0 ;
		$parametros['feCierreHasta'] = $feCierreHasta ? $feCierreHasta[0] : 0 ;
		
        $parametros['filial_id'] = $peticion->query->get('sop_ca_filial');
        $parametros['area_id'] = $peticion->query->get('sop_ca_area');
        $parametros['departamento_id'] = $peticion->query->get('sop_ca_departamento');
        $parametros['empleado_id'] = $peticion->query->get('sop_ca_empleado');
		
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $em = $this->getDoctrine()->getManager("telconet");
		
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:InfoCaso')
            ->generarJsonCasos($parametros,$start,$limit, $session, $em);
        $respuesta->setContent($objJson);
        
        return $respuesta;
	}
	
	
	//*****************************************************************************************************
	//*************************** ACCIONES SESSION  *****************************************************
	//*****************************************************************************************************
    
    /**
     * @Secure(roles="ROLE_147-124")
     * 
     * Documentación para el método 'cargaSessionAjaxAction'.
     *
     * Método que inicializa los datos del cliente en la sesión.
     *
     * @return Response Resultado de la Operación.
     * 
     * @author Unknow
     * @version 1.0 Unknow
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 04-04-2016
     * Verificación del Cliente VIP para agregarlo a la sesión.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.2 08-06-2016
     * Se inicializan los valores de sesión del cliente
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.3 16-01-2020 - Se inicializa seteo de variables de sesión 'contactosCliente', 'contactosPunto'.
     *
     */
    public function cargaSessionAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        
        // Se inicializan los valores de la sesión.
        $session->set('ptoCliente',  '');
        $session->set('clienteContactos', '');
        $session->set('puntosContactos',  '');
        $session->set('contactosCliente', '');
        $session->set('contactosPunto', '');
        $session->set('esVIP', '');
        $puntoId = $peticion->get('puntoId');
        
        $em = $this->getDoctrine()->getManager("telconet");
        
        $strPrefijoEmpresa = $peticion->getSession()->get('prefijoEmpresa');
        
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
                
        if (null == $puntoCliente = $em->getRepository('schemaBundle:InfoPunto')->getPuntoParaSession($puntoId))
        {
            $respuesta->setContent("No existe la entidad");
        }
        else
        {
            $cliente = $em->getRepository('schemaBundle:InfoPersona')->getPersonaParaSession($codEmpresa, $puntoCliente["id_persona"]);
            $clienteContactos   = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                     ->getFormasContactoParaSession($puntoCliente["id_persona"], "Telefono");
        
            //guardo en session el ptoCliente
            $session->set('ptoCliente', $puntoCliente);
            $session->set('cliente', $cliente);
            $session->set('clienteContactos', $clienteContactos);
            
            $strEsVip = '';
            
            if($strPrefijoEmpresa == 'TN')
            {
                // Buscamos en InfoContratoDatoAdicional para verificar que sea cliente VIP
                $arrayParams        = array('ID_PER'  => $puntoCliente["id_persona_empresa_rol"], 'EMPRESA' =>  $codEmpresa, 'ESTADO'  => 'Activo');
                $entityContratoDato = $this->_em->getRepository('schemaBundle:InfoContratoDatoAdicional')
                                                ->getResultadoDatoAdicionalContrato($arrayParams);
                $strEsVip           = $entityContratoDato && $entityContratoDato->getEsVip() ? 'Sí' : 'No';
            }
            $session->set('esVIP', $strEsVip);

            $respuesta->setContent("Se encontro la entidad");
        }
        return $respuesta;
    }    
	
    /**
     * @Secure(roles="ROLE_147-125")
     * 
     * Documentación para el método 'destruirSesionAjaxAction'.
     *
     * Método que elimina los datos del cliente de la sesión.
     *
     * @return Response Resultado de la Operación.
     * 
     * @author Unknow
     * @version 1.0 Unknow
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 04-04-2016
     * Se limpia el valor esVip de la sesión del Cliente.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.2 18-04-2018 Se inicializa seteo de variable de sesión 'cicloFacturacionCliente'.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.3 14-05-2018 Se inicializa seteo de variables de sesión 'contactosCliente', 'formasContactoCliente' solo para TN.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.4 16-01-2020 - Se inicializa seteo de variables de sesión 'contactosCliente', 'contactosPunto'.
     *
     */
    public function destruirSesionAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
        
		//unset($ptoCliente, $cliente, $clienteContacto);
	
        //guardo en session el ptoCliente
        $session  = $peticion->getSession();
        $strPrefijoEmpresa = $session->get('prefijoEmpresa');
        
        $session->set('ptoCliente', "");
        $session->set('cliente', "");
        $session->set('clienteContactos', "");
        $session->set('puntoContactos', "");
        $session->set('contactosCliente', '');
        $session->set('contactosPunto', '');
        $session->set('datosFinancierosPunto', "");
        $session->set('numServicios', "");
        $session->set('serviciosPunto', "");
        $session->set('esVIP', "");
	$session->set('cicloFacturacionCliente', "");
        $session->set('cicloFacturacionCliente', "");
        $session->set('cicloFacturacionCliente', "");
        $session->set('vistaSoporte', ""); 
        if($strPrefijoEmpresa === 'TN')
        {
            $session->set('contactosCliente', null);
            $session->set('formasContactoCliente', null);
        }
        
        $respuesta->setContent("La sesion del Punto Cliente ha sido eliminada");                      
        return $respuesta;
    }  

    /**
     * @Secure(roles="ROLE_147-145")
     * 
     * Documentación para el método 'guardarSesionEmpresaAjaxAction'.
     *
     * Método que Guarda los datos del cliente en la sesión.
     *
     * @return Response Resultado de la Operación.
     * 
     * @author Unknow
     * @version 1.0 Unknow
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 04-04-2016
     * Se limpia el valor esVip de la sesión del Cliente.
     *
     * @author Richard Cabrera Pereira <rcabrera@telconet.ec>
     * @version 1.2 11-10-2016 Se crea una variable session 'numeroTareasAbiertas', en la cual se va almacenar la
     *                         cantidad de tareas abiertas que existen asignadas al usuario conectado
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 17-04-2017 Se realizan ajustes al momento de llamar a la función getNumeroTareasAbiertas, debido a que ahora se envian los 
     *                         parametros dentro de un array
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 27-06-2017 - Se añade a la sessión del usuario la información geográfica correspondiente
     *
     * @author Richard Cabrera Pereira <rcabrera@telconet.ec>
     * @version 1.5 23-11-2017 Se crea una variable session 'tareasAbiertasDepartamento', en la cual se va almacenar la
     *                         cantidad de tareas abiertas que existen asignadas al departamento del usuario en session
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 31-05-2018 - Se agrega credencial: indicadorTareasNacional, para que la informacion del indicador de tareas departamental,
     *                           sea a nivel nacional
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.7 01-10-2018 Se crean las variables de sesión strLimiteLatitudNorte, strLimiteLatitudSur, strLimiteLongitudEste,
     *                         strLimiteLongitudOeste para almacenar las coordenadas límites de los elementos dependiendo del país en sesión
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.8 31-10-2018 - Se crea una variable session 'tareasAbiertasMovil', en la cual se va almacenar la
     *                         cantidad de casos creado desde la app móvil.
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.9 04-12-2018 -  Se agrega la fecha por defecto para obtener un mejor tiempo de respuesta en los indicadores de tareas.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 2.0 16-01-2020 - Se inicializa seteo de variables de sesión 'contactosCliente', 'contactosPunto'.
     *
     */
    public function guardarSesionEmpresaAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->getRequest();

        $emSoporte              = $this->getDoctrine()->getManager('telconet_soporte');
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $IdPersonaEmpresaRol    = $peticion->get('IdPersonaEmpresaRol');
        $CodEmpresa             = $peticion->get('IdEmpresa');
        $prefijoEmpresa         = $peticion->get('prefijoEmpresa');
        $nombreEmpresa          = $peticion->get('nombreEmpresa');
        $IdOficina              = $peticion->get('IdOficina');
        $nombreOficina          = $peticion->get('nombreOficina');
        $IdDepartamento         = $peticion->get('IdDepartamento');
        $nombreDepartamento     = $peticion->get('nombreDepartamento');
        $intIdPais              = $peticion->get('intIdPais');
        $strNombrePais          = $peticion->get('strNombrePais');
        $intIdRegion            = $peticion->get('intIdRegion');
        $strNombreRegion        = $peticion->get('strNombreRegion');
        $intIdCanton            = $peticion->get('intIdCanton');
        $strNombreCanton        = $peticion->get('strNombreCanton');
        $intIdProvincia         = $peticion->get('intIdProvincia');
        $strNombreProvincia     = $peticion->get('strNombreProvincia');
        $strFacturaElectronico  = $peticion->get('strFacturaElectronico');
        $strNombreEmpresa       = $peticion->get('strNombreEmpresa');
        $arrayParametros        = array();
        $arrayRespuesta         = array();
        
        //guardo en session el ptoCliente
        $session  = $this->get( 'session' );
        $session->set("strBanderaTareasDepartamento","N");
        $session->set('idPersonaEmpresaRol', $IdPersonaEmpresaRol);
        $session->set('idEmpresa', $CodEmpresa);
        $session->set('idOficina', $IdOficina);
        $session->set('idDepartamento', $IdDepartamento);
        $session->set('empresa', $nombreEmpresa);
        $session->set('oficina', $nombreOficina);
        $session->set('departamento', $nombreDepartamento);
        $session->set('intIdPais', $intIdPais);
        $session->set('strNombrePais', $strNombrePais);
        $session->set('intIdRegion', $intIdRegion);
        $session->set('strNombreRegion', $strNombreRegion);
        $session->set('intIdCanton', $intIdCanton);
        $session->set('strNombreCanton', $strNombreCanton);
        $session->set('intIdProvincia', $intIdProvincia);
        $session->set('strNombreProvincia', $strNombreProvincia);
        $session->set('strFacturaElectronico', $strFacturaElectronico);
        $session->set('prefijoEmpresa', $prefijoEmpresa);
        $session->set('strNombreEmpresa', $strNombreEmpresa);
        
        $session->set('ptoCliente', "");
        $session->set('cliente', "");
        $session->set('clienteContactos', "");
        $session->set('puntoContactos', "");
        $session->set('contactosCliente', '');
        $session->set('contactosPunto', '');
        $session->set('esVIP', "");
        $session->set('datosFinancierosPunto', "");
        $session->set('numServicios', "");
        $session->set('serviciosPunto', "");

        //Se calcula el numero de tareas abiertas que tiene asignadas el usuario en session
        $arrayParametros["intPersonaEmpresaRolId"] = $IdPersonaEmpresaRol;
        $arrayParametros["strTipoConsulta"]        = "CantidadTareasAbiertas";
        $arrayParametros["arrayEstados"]           = array('Cancelada','Rechazada','Finalizada','Anulada');
        $arrayParametros["intPersonaEmpresaRol"]   = $IdPersonaEmpresaRol;
        $arrayParametros["strTipoConsulta"]        = "persona";

        $arrayFechaDefecto = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne('TAREAS_FECHA_DEFECTO','SOPORTE','','','','','','','','');

        if (!empty($arrayFechaDefecto) && count($arrayFechaDefecto) > 0 &&
            checkdate($arrayFechaDefecto['valor2'],$arrayFechaDefecto['valor3'],$arrayFechaDefecto['valor1']))
        {
            $strFechaDefecto = $arrayFechaDefecto['valor1'].'-'. //Año
                               $arrayFechaDefecto['valor2'].'-'. //Mes
                               $arrayFechaDefecto['valor3'];     //Día

            $arrayParametros['strFechaDefecto'] = $strFechaDefecto;            
        }

        $arrayRespuesta = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')->getDetalleTareas($arrayParametros);
        $session->set('numeroTareasAbiertas', $arrayRespuesta["intCantidadTareas"]);

        //Se calcula el numero de tareas abiertas por departamento
        $arrayParametros["intIdDepartamento"] = $IdDepartamento;
        $objInfoPersonaEmpresaRol             = $emSoporte->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($IdPersonaEmpresaRol);

        if(is_object($objInfoPersonaEmpresaRol))
        {
            $arrayParametros["intOficinaId"] = $objInfoPersonaEmpresaRol->getOficinaId()->getId();
        }
        $arrayParametros["strEstado"]         = "Activo";
        $arrayParametros["intDepartamentoId"] = $IdDepartamento;
        $arrayParametros["strTipoConsulta"]   = "departamento";

        //Se consulta si la persona en session tiene la credencial: indicadorTareasNacional
        $arrayParametrosPerfil["intIdPersonaRol"] = $IdPersonaEmpresaRol;
        $arrayParametrosPerfil["strNombrePerfil"] = "indicadorTareasNacional";

        $strTienePerfil = $emSoporte->getRepository('schemaBundle:SeguRelacionSistema')->getPerfilPorPersona($arrayParametrosPerfil);

        $arrayParametros["strTieneCredencial"] = $strTienePerfil;

        $arrayTareasPendientes = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')->getDetalleTareas($arrayParametros);
        $session->set('numeroTareasAbiertasDepartamento', $arrayTareasPendientes["intCantidadTareas"]);
        
        $arrayLimitesCoordenadas    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne(   'LIMITES_COORDENADAS_ELEMENTO', 
                                                            '', 
                                                            '', 
                                                            $strNombrePais, 
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '');
        if(empty($arrayLimitesCoordenadas))
        {
            $strLimiteLatitudNorte  = "";
            $strLimiteLatitudSur    = "";
            $strLimiteLongitudEste  = "";
            $strLimiteLongitudOeste = "";
            $strRangoPais           = "";
        }
        else
        {
            $strLimiteLatitudNorte  = $arrayLimitesCoordenadas["valor1"];
            $strLimiteLatitudSur    = $arrayLimitesCoordenadas["valor2"];
            $strLimiteLongitudEste  = $arrayLimitesCoordenadas["valor3"];
            $strLimiteLongitudOeste = $arrayLimitesCoordenadas["valor4"];
            $strRangoPais           = $arrayLimitesCoordenadas["valor5"];
        }
        $session->set("strLimiteLatitudNorte" , $strLimiteLatitudNorte);
        $session->set("strLimiteLatitudSur"   , $strLimiteLatitudSur);
        $session->set("strLimiteLongitudEste" , $strLimiteLongitudEste);
        $session->set("strLimiteLongitudOeste", $strLimiteLongitudOeste);
        $session->set("strRangoPais"          , $strRangoPais);

        $intCantidadCasoMovil  = $emSoporte->getRepository('schemaBundle:InfoCaso')->getCantidadCasoMovil();
        $session->set('numeroTareasAbiertasMovil', $intCantidadCasoMovil);

        $respuesta->setContent("Se seteo los valores de la empresa");
        return $respuesta;
    }
	
	
	//*****************************************************************************************************
	//*************************** CAMBIAR CLAVE **********************************************************
	//*****************************************************************************************************
	
    public static function generateVerifyText()
    {
    	// Create a seed to generate a random number
        srand((double)microtime()*1000000);
	
        // Run the random number seed through the MD5 function
	    $seed_string = md5(rand(0,9999));
	
        // Get the id variable from the <img src> tag ...
        $text = substr($seed_string, 17, 5);
        
        return $text;
    }
	
    /**
     * cambiarClaveAction, metodo que llama al metodo que crea el formulario y lo renderiza al twig para el cambio de clave
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 29-10-2015
     * @since 1.0
     * 
     * @Secure(roles="ROLE_147-264")
     */
    public function cambiarClaveAction()
    {
        $objForm = $this->creaFormularioActualizaClave();
        return $this->render('adminBundle:Default:cambiar_clave.html.twig', array('objForm' => $objForm->createView()));
    }//cambiarClaveAction

    /**
     * actualizarCambioClaveAction, metodo que realiza ejecucion del comando para actualizar la clave en el LDAP
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 29-10-2015
     * @since 1.0
     * 
     * @author Fabricio Bermeo<fbermeo@telconet.ec>
     * @version 1.2 13-07-2016
     * Se modifica el método, añadiendo la actualización de la clave en los aplicativos de networking
     * (AAAA, Tacas, TacacsCpe, NmsBackone, NmsCorp) desde un service
     * 
     * 
     * @author Duval Medina <dmedina@telconet.ec>
     * @version 1.3 2016-09-29 - Se incluye parámetro 'requiereCambioPass' y si es exitoso el cambio se lo setea a false
     * 
     * @Secure(roles="ROLE_147-265")
     */
    public function actualizarCambioClaveAction()
    {
        $objRequest = $this->get('request');
        $objSession = $objRequest->getSession();
        $strMensaje = "";
        $intSalida = 0;
        //Crea el formulario para la actualizacion de la clave
        $objForm    = $this->creaFormularioActualizaClave();
        $objForm->handleRequest($objRequest);
        
        //Valida que el formulario sea valido
        if($objForm->isValid())
        {
            //Verifica que haya clickeado el boton 'actualizar'
            if($objForm->get('actualizar')->isClicked())
            {
                //Recupera el formulario como array
                $arrayData = $objForm->getData();
                $arrParametros = array();
                $arrParametros['strLogin'] = $objSession->get('user');
                $arrParametros['strClave'] = $arrayData['strClave'];
                $arrParametros['strConfirmarClave'] = $arrayData['strConfirmarClave'];
                $arrParametros['boolRequiereCambio'] = $objSession->get('requiereCambioPass');
                $serviceActualizarPassword = $this->get('admin.ActualizarPassword');
                $arrayRespuesta = $serviceActualizarPassword->actualizarPassword($arrParametros);
                $strMensaje = $arrayRespuesta['mensaje'];
                $intSalida = $arrayRespuesta['salida'];
                //Debe renderizar al formulario de actualizar password porque la clave ingresada
                //no coincide con la confirmación de la misma
                if(2 === $intSalida)
                {
                    return $this->render('adminBundle:Default:cambiar_clave.html.twig', array(
                            'objForm'  => $objForm->createView(),
                            'strError' => $strMensaje
                    ));
                }
            }
        }
        else
        {
            //Si el formulario es invalido renderiza la pagina
            return $this->render('adminBundle:Default:cambiar_clave.html.twig', array(
                                 'objForm'  => $objForm->createView()
            ));
        }
        $objSession->set('requiereCambioPass',false);
        return $this->render('adminBundle:Default:cambiarClaveShow.html.twig', array(
                'respuesta' => $intSalida,'mensaje' => $strMensaje
        ));
    }//actualizarCambioClaveAction
    
     /**
     * Documentación para el método 'resetearClave'.
     *
     * Función que resetea clave
     * 
     * @return json    
     * 
     * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 1.0 18-02-2020
     */
    public function resetearClaveAction()
    {
        $objRespuesta       = new Response();
        $objRequest         = $this->get('request');
        $arrayParametros    = array();
        $intIdPersona       = $objRequest->get('id');
        $strPrefijo         = $objRequest->get('prefijo');
        $serviceActualizarPassword = $this->get('admin.ActualizarPassword');
        $emComercial = $this->getDoctrine()->getManager('telconet');
        try
        {
            
            $objPersona = $emComercial->getRepository("schemaBundle:InfoPersona")
                             ->findOneById($intIdPersona);
            if(is_object($objPersona))
            {
                $arrayParametros['strLogin'] =  $objPersona->getLogin();
                $arrayParametros['strPrefijo'] =  $strPrefijo;
                $arrayRespuesta = $serviceActualizarPassword->resetearClave($arrayParametros);        
            }
            $objJson = json_encode($arrayRespuesta);
            $objRespuesta->setContent('{"encontrados":' . $objJson . ',"success":"true"}');
        }
         catch(\Exception $e)
        {
            $objRespuesta['strMensaje'] = 'Hubo un problema al resetear la clave.';
            $objJson = json_encode($arrayRespuesta);
            $objRespuesta->setContent('{"encontrados":' . $objJson . ',"success":"false"}');
        }
        return $objRespuesta;
        
    }
    /**
     * creaFormularioActualizaClave, crea el formulario para la actualizacion de clave
     * @return object Retorna un formulario
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 30-10-2015 Version Inicial
     *
     * @author Luis Tama <ltama@telconet.ec
     * @version 1.1 20-07-2016 Se incluye caraceres especiales faltantes en regex
     */
    private function creaFormularioActualizaClave()
    {
        //Regex para validar claves
        $strRegexPassword = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@%&$!*~+#_={[\/\]\\}|;:.<,>?-])[A-Za-z\d@%&$!*~+#_={[\/\]\\}|;:.<,>?-]{8,15}$/';
        $srtDefaultData   = array('message' => 'Formulario actualiza clave');
        //Crea el formulario para la actualizacion de la clave
        $objForm          = $this->createFormBuilder($srtDefaultData)
                                 ->add('strClave', 'password', array(
                                       'label' => 'Clave',
                                       'attr' => array('class' => 'form-control', 'placeholder' => 'A-z a-z 0-9 $@$!%*?&'),
                                       'constraints' => new NotBlank(),
                                       'constraints' => new Regex(array(
                                       'pattern' => $strRegexPassword,
                                       'message' => 'La clave no cumple con una de las reglas'))))
                                 ->add('strConfirmarClave', 'password', array(
                                       'label' => 'Confirmar Clave',
                                       'attr' => array('class' => 'form-control margenClass', 'placeholder' => 'A-z a-z 0-9 $@$!%*?&'),
                                       'constraints' => new NotBlank(),
                                       'constraints' => new Regex(array(
                                       'pattern' => $strRegexPassword,
                                       'message' => 'La clave no cumple con una de las reglas'))))
                                 ->add('captcha', 'captcha', array(
                                       'label' => 'Ingrese los caracteres que aparecen en la imagen',
                                       'attr' => array('class' => 'form-control margenClass', 
                                       'placeholder' => 'Ingrese los caracteres que aparecen en la imagen'),
                                       'constraints' => new NotBlank(),
                                       'invalid_message' => 'Código invalido'))
                                 ->add('actualizar', 'submit', array(
                                       'attr' => array('class' => 'btn btn-block btn-default btn-flat margenClass')))
                              ->getForm();
        return $objForm;
    }//creaFormularioActualizaClave
	
    /**
    * @Secure(roles="ROLE_147-266")
    */
	public function imageAction($texto)
	{
		// 200 is width / 30 is height
		$TheImage = imagecreate("152", "15");

		// Color image background blue.
		$ColorImage = imagecolorallocate($TheImage, 255,255,255);

		// Color the text white
		$ColorText = imagecolorallocate($TheImage, rand(0,75), rand(0,75), rand(0,75));

		//select font file
		$font="/home/actividades/lib/jpgraph/Fonts/arialbd.ttf";
      
		$trans_color = $ColorImage;//transparent colour
		imagefill($TheImage, 0, 0, $trans_color);
            
		//add text to image
		//imagettftext($TheImage, 10, 90, 16, 196, $ColorText, $font, $texto);      
		imagestring($TheImage, rand(2,3), rand(41,50),   rand(0,1), $texto[0], $ColorText);
		imagestring($TheImage, rand(2,3), rand(56,65),   rand(0,1), $texto[1], $ColorText);
		imagestring($TheImage, rand(2,3), rand(71,80),  rand(0,1), $texto[2], $ColorText);
		imagestring($TheImage, rand(2,3), rand(86,95), rand(0,1), $texto[3], $ColorText);
		imagestring($TheImage, rand(2,3), rand(101,110), rand(0,1), $texto[4], $ColorText);      

		// Let the browser know that it is an PNG image..
		header("Content-Type: image/png");
		imagepng($TheImage); // output to browser.
		exit;
	}
  
	//*****************************************************************************************************
	//*************************** MI AGENDA **************************************************************
	//*****************************************************************************************************
	
    /**
    * @Secure(roles="ROLE_147-126")
    */
    public function miAgendaAction()
    {           
        $em = $this->getDoctrine()->getManager('telconet_general');

		
        return $this->render('adminBundle:Default:mi_agenda.html.twig', array(
        ));
    }
    
    /**
    * @Secure(roles="ROLE_147-127")
    */
    public function getEventosInicioAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $request = $this->get('request');
        $session = $peticion->getSession();
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        $page = $peticion->query->get('page');
        
        $startDate = $peticion->query->get('startDate');
        $endDate = $peticion->query->get('endDate');
        $origen = $peticion->query->get('origen');
        
        $em = $this->getDoctrine()->getManager("telconet");        
        $codEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : "");
		//$arrayDepartamento = $em->getRepository('schemaBundle:InfoPersona')->getDepartamentoByEmpleado($codEmpresa, $session->get('id_empleado'));
	$em_soporte = $this->getDoctrine()->getManager("telconet_soporte");		
	
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:InfoDetalleAsignacion')
            // ->generarJsonCasosYPlanificacionXUsuario($start, $limit, $origen, $startDate, $endDate, $request->getSession()->get('id_empleado'));
            //->generarJsonCasosYPlanificacionXDepartamento($start, $limit, $origen, $startDate, $endDate, $session->get('idDepartamento')); //$arrayDepartamento["id_departamento"]);
           ->generarJsonTareasTodas($start, $limit, $origen, $startDate, $endDate, $request->getSession()->get('id_empleado'),$request->getSession()->get('idDepartamento'),"ByUsuario",$em,$em_soporte);
        
        $respuesta->setContent($objJson);
        return $respuesta;
    } 
       
    /**
    * @Secure(roles="ROLE_147-128")
    */
    public function getColoresAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');       
        
        $arr_encontrados[] = array('id'=> 1, "title"=> "Casos", "color"=> 2);
        $arr_encontrados[] = array('id'=> 2, "title"=> "Planificacion", "color"=> 22);
        $arr_encontrados[] = array('id'=> 3, "title"=> "Soporte", "color"=> 6);
        $arr_encontrados[] = array('id'=> 4, "title"=> "Otros", "color"=> 7);
        $arr_encontrados[] = array('id'=> 5, "title"=> "Otros", "color"=> 26);
        
        $data=json_encode($arr_encontrados);
        $objJson= '{"calendars":'.$data.'}';        
        
        $respuesta->setContent($objJson);
        return $respuesta;
    }

	
	//*****************************************************************************************************
	//*************************** EXPORTAR EXCEL **********************************************************
	//*****************************************************************************************************
	 
    /**
    * #@Secure(roles="ROLE_147-564")
    */
    public function exportarConsulta_BusquedaFinancieraAction()
    {
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $em = $this->getDoctrine()->getManager('telconet_financiero');
			
        $em->getConnection()->beginTransaction();
		try
		{	
			$respuesta = new Response();
			$respuesta->headers->set('Content-Type', 'text/json');
			
			$peticion = $this->get('request');
			$session = $peticion->getSession();
			
			$login = $peticion->query->get("login") ? $peticion->query->get("login") : "" ;        
			$descripcion_pto = $peticion->query->get("descripcion_pto") ? $peticion->query->get("descripcion_pto") : "" ;
			$direccion_pto = $peticion->query->get("direccion_pto") ? $peticion->query->get("direccion_pto") : "" ;
			$estados_pto = $peticion->query->get("estados_pto") ? $peticion->query->get("estados_pto") : "" ;
			$negocios_pto = $peticion->query->get("negocios_pto") ? $peticion->query->get("negocios_pto") : "" ;
			$vendedor = $peticion->query->get("vendedor") ? $peticion->query->get("vendedor") : "" ;
			$identificacion = $peticion->query->get("identificacion") ? $peticion->query->get("identificacion") : "" ;
			$nombre = $peticion->query->get("nombre") ? $peticion->query->get("nombre") : "" ;
			$apellido = $peticion->query->get("apellido") ? $peticion->query->get("apellido") : "" ;
			$razon_social = $peticion->query->get("razon_social") ? $peticion->query->get("razon_social") : "" ;
			$direccion_grl = $peticion->query->get("direccion_grl") ? $peticion->query->get("direccion_grl") : "" ;
			$depende_edificio = $peticion->query->get("depende_edificio") ? $peticion->query->get("depende_edificio") : 0 ;
			$es_edificio = $peticion->query->get("es_edificio") ? $peticion->query->get("es_edificio") : 0 ;
			
			$parametros = array (   "login" => $login,
									"descripcion_pto" => $descripcion_pto,
									"direccion_pto" => $direccion_pto,
									"estados_pto" => $estados_pto,
									"negocios_pto" => $negocios_pto,
									"vendedor" => $vendedor,
									"identificacion" => $identificacion,
									"nombre" => $nombre,
									"apellido" => $apellido,
									"razon_social" => $razon_social,
									"direccion_grl" => $direccion_grl,
									"depende_edificio" => $depende_edificio,
									"es_edificio" => $es_edificio
								);

		
			$parametros['fin_tipoDocumento'] = $fin_tipoDocumento = $peticion->query->get('fin_doc_tipoDocumento') ? $peticion->query->get('fin_doc_tipoDocumento') : '';
			$parametros['fin_tipoDocumento_texto'] = $peticion->query->get('fin_doc_tipoDocumento_texto') ? $peticion->query->get('fin_doc_tipoDocumento_texto') : '';
			
			$parametros['doc_numDocumento'] = $peticion->query->get('fin_doc_numDocumento') ? $peticion->query->get('fin_doc_numDocumento') : '';
			$parametros['doc_creador'] = $peticion->query->get('fin_doc_creador') ? $peticion->query->get('fin_doc_creador') : '';
			$parametros['doc_estado'] = $peticion->query->get('fin_doc_estado') ? $peticion->query->get('fin_doc_estado') : '';
			$parametros['doc_estado_texto'] = $peticion->query->get('fin_doc_estado_texto') ? $peticion->query->get('fin_doc_estado_texto') : '';
			$parametros['doc_monto'] = $peticion->query->get('fin_doc_monto') ? $peticion->query->get('fin_doc_monto') : 0.00 ;
			$parametros['doc_montoFiltro'] = $peticion->query->get('fin_doc_montoFiltro') ? $peticion->query->get('fin_doc_montoFiltro') : 'i';
			$parametros['doc_montoFiltro_texto'] = $peticion->query->get('fin_doc_montoFiltro_texto') ? $peticion->query->get('fin_doc_montoFiltrot_texto') : 'igual que';
			$doc_fechaCreacionDesde = explode('T',$peticion->query->get('fin_doc_fechaCreacionDesde'));
			$doc_fechaCreacionHasta = explode('T',$peticion->query->get('fin_doc_fechaCreacionHasta'));
			$doc_fechaEmisionDesde = explode('T',$peticion->query->get('fin_doc_fechaEmisionDesde'));
			$doc_fechaEmisionHasta = explode('T',$peticion->query->get('fin_doc_fechaEmisionHasta'));		
			$parametros['doc_fechaCreacionDesde'] = $doc_fechaCreacionDesde ? $doc_fechaCreacionDesde[0] : 0 ;
			$parametros['doc_fechaCreacionHasta'] = $doc_fechaCreacionHasta ? $doc_fechaCreacionHasta[0] : 0 ;
			$parametros['doc_fechaEmisionDesde'] = $doc_fechaEmisionDesde ? $doc_fechaEmisionDesde[0] : 0 ;
			$parametros['doc_fechaEmisionHasta'] = $doc_fechaEmisionHasta ? $doc_fechaEmisionHasta[0] : 0 ;

			$parametros['pag_numDocumento'] = $peticion->query->get('fin_pag_numDocumento') ? $peticion->query->get('fin_pag_numDocumento') : '';			
			$parametros['pag_numReferencia'] = $peticion->query->get('fin_pag_numReferencia') ? $peticion->query->get('fin_pag_numReferencia') : '';
			$parametros['pag_numDocumentoRef'] = $peticion->query->get('fin_pag_numDocumentoRef') ? $peticion->query->get('fin_pag_numDocumentoRef') : '';
			$parametros['pag_creador'] = $peticion->query->get('fin_pag_creador') ? $peticion->query->get('fin_pag_creador') : '';
			$parametros['pag_formaPago'] = (($peticion->query->get('fin_pag_formaPago') && $peticion->query->get('fin_pag_formaPago') != "null") ? $peticion->query->get('fin_pag_formaPago') : '');
			$parametros['pag_formaPago_texto'] = $peticion->query->get('fin_pag_formaPago_texto') ? $peticion->query->get('fin_pag_formaPago_texto') : '';
			$parametros['pag_banco'] = (($peticion->query->get('fin_pag_banco') && $peticion->query->get('fin_pag_banco') != "null") ? $peticion->query->get('fin_pag_banco') : '');
			$parametros['pag_banco_texto'] = $peticion->query->get('fin_pag_banco_texto') ? $peticion->query->get('fin_pag_banco_texto') : '';
			$parametros['pag_estado'] = (($peticion->query->get('fin_pag_estado') && $peticion->query->get('fin_pag_estado') != "null") ? $peticion->query->get('fin_pag_estado') : '');
			$parametros['pag_estado_texto'] = $peticion->query->get('fin_pag_estado_texto') ? $peticion->query->get('fin_pag_estado_texto') : '';
			
			$pag_fechaCreacionDesde = $peticion->query->get('fin_pag_fechaCreacionDesde');
			$pag_fechaCreacionHasta = $peticion->query->get('fin_pag_fechaCreacionHasta');
			$parametros['pag_fechaCreacionDesde'] = $pag_fechaCreacionDesde ? $pag_fechaCreacionDesde : 0 ;
			$parametros['pag_fechaCreacionHasta'] = $pag_fechaCreacionHasta ? $pag_fechaCreacionHasta : 0 ;
		
			$start = $peticion->query->get('start');
			$limit = $peticion->query->get('limit');
			
			//var_dump($parametros);  die();
			
			$oficinaId = $peticion->getSession()->get('idOficina');
			$empresaId = $peticion->getSession()->get('idEmpresa'); 
			$resultado = "";
			//echo " hey  -- " .$fin_tipoDocumento;
			
			if($fin_tipoDocumento == 'FAC' || $fin_tipoDocumento == 'FACP' || $fin_tipoDocumento == 'NC' || $fin_tipoDocumento == 'ND')
			{
				$resultado = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->findBusquedaAvanzadaFinanciera($parametros, $empresaId, $oficinaId, $start, $limit);
			}
			else if($fin_tipoDocumento == 'PAG' || $fin_tipoDocumento == 'ANT' || $fin_tipoDocumento == 'ANTS')
			{
				$resultado = $em->getRepository('schemaBundle:InfoPagoCab')->findBusquedaAvanzadaFinanciera($parametros, $empresaId, $oficinaId, $start, $limit);
			}
			
			$numTotal = ($resultado ? ($resultado['total'] ? $resultado['total'] : 0) : 0);
			$registros = ($resultado ? ($resultado['registros'] ? $resultado['registros'] : false) : false);

			$this->generateExcelConsulta_BusquedaFinanciera($registros, $em, $emComercial, $parametros, $peticion->getSession()->get('user'));			

		}catch (Exception $e) {
            $em->getConnection()->close();
			$objJson= '{"total":"0","encontrados":[]}';
            //$resultado = json_encode(array('success'=>false,'mensaje'=>$e));
        }
		
        $respuesta->setContent($objJson);
        return $respuesta;	
	}
	
    public static function generateExcelConsulta_BusquedaFinanciera($datos, $emFinanciero, $em, $parametros, $usuario)
    {
		error_reporting(E_ALL);
                
        $objPHPExcel = new PHPExcel();
       
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array( ' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');

		if($parametros['fin_tipoDocumento'] == 'ANTS')
		{
			$objPHPExcel = $objReader->load(__DIR__."/../Resources/templatesExcel/templateBusquedaFinancieraSC.xls");
		}
		else if($parametros['fin_tipoDocumento'] == 'FAC' || $parametros['fin_tipoDocumento'] == 'FACP' || $parametros['fin_tipoDocumento'] == 'ND' || $parametros['fin_tipoDocumento'] == 'NC')
		{
			$objPHPExcel = $objReader->load(__DIR__."/../Resources/templatesExcel/templateBusquedaFinancieraFCD.xls");		
		}
		else
		{
			$objPHPExcel = $objReader->load(__DIR__."/../Resources/templatesExcel/templateBusquedaFinanciera.xls");		
		}
        
        // Set properties
        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($usuario);
        $objPHPExcel->getProperties()->setTitle("Busqueda Avanzada Financiera");
        $objPHPExcel->getProperties()->setSubject("Busqueda Avanzada Financiera");
        $objPHPExcel->getProperties()->setDescription("Resultado de busqueda avanzada (financiera).");
        $objPHPExcel->getProperties()->setKeywords("Financiero");
        $objPHPExcel->getProperties()->setCategory("Busqueda");

        $objPHPExcel->getActiveSheet()->setCellValue('C3',$usuario);

        $objPHPExcel->getActiveSheet()->setCellValue('C4', PHPExcel_Shared_Date::PHPToExcel( gmmktime(0,0,0,date('m'),date('d'),date('Y')) ));
        $objPHPExcel->getActiveSheet()->getStyle('C4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

		
		$objPHPExcel->getActiveSheet()->setCellValue('B8',''.($parametros['fin_tipoDocumento']=="")?'Todos': $parametros['fin_tipoDocumento_texto']);
		
		
		if($parametros['fin_tipoDocumento'] == 'FAC' || $parametros['fin_tipoDocumento'] == 'FACP' || $parametros['fin_tipoDocumento'] == 'ND' || $parametros['fin_tipoDocumento'] == 'NC')
		{
			$objPHPExcel->getActiveSheet()->setCellValue('B11',''.($parametros['doc_numDocumento']=="")?"Todos":$parametros['doc_numDocumento']);
			$objPHPExcel->getActiveSheet()->setCellValue('B12',''.($parametros['doc_monto']=="")?'Todos': $parametros['doc_monto']);
			$objPHPExcel->getActiveSheet()->setCellValue('B13',''.($parametros['doc_monto']=="")?'Todos': $parametros['doc_montoFiltro_texto']);		
			$objPHPExcel->getActiveSheet()->setCellValue('B14',''.($parametros['doc_estado']=="")?'Todos': $parametros['doc_estado_texto']);
			$objPHPExcel->getActiveSheet()->setCellValue('B15',''.($parametros['doc_creador']=="")?'Todos': $parametros['doc_creador']);		
			$objPHPExcel->getActiveSheet()->setCellValue('C15',''.($parametros['doc_fechaCreacionDesde']=="")?'Todos': $parametros['doc_fechaCreacionDesde']);
			$objPHPExcel->getActiveSheet()->setCellValue('C16',''.($parametros['doc_fechaCreacionHasta']=="")?'Todos': $parametros['doc_fechaCreacionHasta']);
			$objPHPExcel->getActiveSheet()->setCellValue('C17',''.($parametros['doc_fechaEmisionDesde']=="")?'Todos': $parametros['doc_fechaEmisionDesde']);
			$objPHPExcel->getActiveSheet()->setCellValue('C18',''.($parametros['doc_fechaEmisionHasta']=="")?'Todos': $parametros['doc_fechaEmisionHasta']);
		}
		if($parametros['fin_tipoDocumento'] == 'PAG' || $parametros['fin_tipoDocumento'] == 'ANT' || $parametros['fin_tipoDocumento'] == 'ANTS')
		{
			$objPHPExcel->getActiveSheet()->setCellValue('G11',''.($parametros['pag_numDocumento']=="")?"Todos":$parametros['pag_numDocumento'] . " ");
			$objPHPExcel->getActiveSheet()->setCellValue('G12',''.($parametros['pag_numReferencia']=="")?"Todos":$parametros['pag_numReferencia'] . " ");
			$objPHPExcel->getActiveSheet()->setCellValue('G13',''.($parametros['pag_numDocumentoRef']=="")?"Todos":$parametros['pag_numDocumentoRef'] . " ");
			$objPHPExcel->getActiveSheet()->setCellValue('G14',''.($parametros['pag_creador']=="")?'Todos': $parametros['pag_creador']);
			$objPHPExcel->getActiveSheet()->setCellValue('G15',''.($parametros['pag_formaPago']=="")?'Todos': $parametros['pag_formaPago_texto']);	
			$objPHPExcel->getActiveSheet()->setCellValue('G16',''.($parametros['pag_banco']=="")?'Todos': $parametros['pag_banco_texto']);		
			$objPHPExcel->getActiveSheet()->setCellValue('G17',''.($parametros['pag_estado']=="")?'Todos': $parametros['pag_estado_texto']);	
			$objPHPExcel->getActiveSheet()->setCellValue('H18',''.($parametros['pag_fechaCreacionDesde']=="")?'Todos': $parametros['pag_fechaCreacionDesde']);
			$objPHPExcel->getActiveSheet()->setCellValue('H19',''.($parametros['pag_fechaCreacionHasta']=="")?'Todos': $parametros['pag_fechaCreacionHasta']);
		}
		
        $i=23;		
        foreach ($datos as $data):
			$valorTotal= ($data['valorTotal'] ? $data['valorTotal'] : 0.00);	
			$valorTotal = number_format($valorTotal, 2, '.', '');
			//setlocale(LC_MONETARY, 'en_US');
			//$valorTotal = money_format('%#10n', $valorTotal);

			$razonSocial = (isset($data['razonSocial']) ? ($data['razonSocial'] ? $data['razonSocial'] : "") : "");
			$nombres = (isset($data['nombres']) || isset($data['apellidos']) ? ($data['nombres'] ? $data['nombres'] . " " . $data['apellidos'] : "") : "");
			$informacion_cliente = ($razonSocial && $razonSocial != "" ? $razonSocial : $nombres);
			//$informacion_cliente= (isset($data['razonSocial']) ? ($data['razonSocial'] ? $data['razonSocial'] : $data['nombres'] . " " . $data['apellidos']) : "");
			
			$automatica = isset($data['esAutomatica']) ? ($data['esAutomatica']=="S" ? "Si" : "No") : '';
			$nombreVendedor = (isset($data["nombreVendedor"]) ? ($data["nombreVendedor"] ? ucwords(mb_strtolower($data["nombreVendedor"], 'UTF-8')) : "") : "");
	   
			$referencia1 = ''; $referencia2 = ''; $referencia='';
			if(isset($data["numeroCuentaBanco"]))
				$referencia1 = $data["numeroCuentaBanco"];
			if(isset($data["numeroReferencia"]))
				$referencia2 = $data["numeroReferencia"];
			
			$referencia = ($referencia1 ? $referencia1 . " " : "") . ($referencia2 ? $referencia2 . " " : ""); 
					
			$nombreBanco  = "";
			if(isset($data["bancoTipoCuentaId"]))
			{
				$bancoTipoCuentaId = $data["bancoTipoCuentaId"];
				$entityBancoTipoCuenta = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->findOneById($bancoTipoCuentaId);
				if($entityBancoTipoCuenta && count($entityBancoTipoCuenta)>0)
				{
					$entityBanco = $entityBancoTipoCuenta->getBancoId();
					$nombreBanco = ($entityBanco ? ($entityBanco->getDescripcionBanco() ? $entityBanco->getDescripcionBanco() : "") : "");
				}
			}
			if(isset($data["bancoCtaContableId"]))
			{
				$bancoCtaContableId = $data["bancoCtaContableId"];
				$entityBancoCtaContable = $em->getRepository('schemaBundle:AdmiBancoCtaContable')->findOneById($bancoCtaContableId);						
				if($entityBancoCtaContable && count($entityBancoCtaContable)>0)
				{							
					$entityBancoTipoCuenta = $entityBancoCtaContable->getBancoTipoCuentaId();
					if($entityBancoTipoCuenta && count($entityBancoTipoCuenta)>0)
					{
						$entityBanco = $entityBancoTipoCuenta->getBancoId();
						$nombreBanco = ($entityBanco ? ($entityBanco->getDescripcionBanco() ? $entityBanco->getDescripcionBanco() : "") : "");
					}
				}
			}					
			$nombreBanco = ucwords(mb_strtolower(trim($nombreBanco), 'UTF-8'));	
						
			$noDocumentoReferencia  = ""; $codigoDocumentoReferencia  = ""; $nombreDocumentoReferencia  = "";
			if(isset($data["referenciaId"]))
			{
				if($data["referenciaId"] && $data["referenciaId"]!="")
				{
					$referenciaId = $data["referenciaId"];
					$entityReferencia = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->findOneById($referenciaId);
					if($entityReferencia && count($entityReferencia)>0)
					{
						$noDocumentoReferencia = ($entityReferencia ? ($entityReferencia->getNumeroFacturaSri() ? $entityReferencia->getNumeroFacturaSri() : "") : "");
						
						$tipoDocumentoReferenciaId = ($entityReferencia ? ($entityReferencia->getTipoDocumentoId() ? $entityReferencia->getTipoDocumentoId() : "") : "");
						$entityTipoDocumento = $emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')->findOneById($tipoDocumentoReferenciaId);								
						if($entityTipoDocumento && count($entityTipoDocumento)>0)
						{
							$codigoDocumentoReferencia = ($entityTipoDocumento ? ($entityTipoDocumento->getCodigoTipoDocumento() ? $entityTipoDocumento->getCodigoTipoDocumento() : "") : "");
							$nombreDocumentoReferencia = ($entityTipoDocumento ? ($entityTipoDocumento->getNombreTipoDocumento() ? $entityTipoDocumento->getNombreTipoDocumento() : "") : "");
						}
					}//fin entityReferencia
				}//fin referenciaId
			}
					
			$nombreCreador = "Migracion";
			$empleado = $em->getRepository('schemaBundle:InfoPersona')->getPersonaPorLogin($data["usrCreacion"]);
			if($empleado && count($empleado)>0)
			{
				$nombreCreador = $empleado->getNombres().' '.$empleado->getApellidos();
			}
			$nombreCreador = ucwords(mb_strtolower(trim($nombreCreador), 'UTF-8'));
			$nombreVendedor = ucwords(mb_strtolower(trim($nombreVendedor), 'UTF-8'));
					
			$identificacionCliente = (isset($data['identificacionCliente']) ? trim($data['identificacionCliente']) : "");
						
			if($parametros['fin_tipoDocumento'] == 'ANTS')
			{
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, (isset($data['id_documento']) ? $data['id_documento'] : ""));
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, (isset($data['id_documento_detalle']) ? $data['id_documento_detalle'] : ""));
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, trim($data['nombreTipoDocumento']));
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $data['numeroDocumento']);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $valorTotal);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, trim($automatica));
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, (isset($data['descripcionFormaPago']) ? $data['descripcionFormaPago'] : ""));
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, "$referencia ");
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, (isset($data['comentarioDetallePago']) ? $data['comentarioDetallePago'] : ""));
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, trim($nombreBanco));
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, trim($nombreDocumentoReferencia));
				$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, "$noDocumentoReferencia ");				
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, trim($nombreCreador));
				$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, trim($data['estadoDocumentoGlobal']?$data['estadoDocumentoGlobal']:''));
				$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, strval(date_format($data['feCreacion'],"d/m/Y G:i")));
				
				if(isset($data['feDeposito']))
				{
					if($data['feDeposito']!="")
						$fecha_deposito=strval(date_format($data['feDeposito'],"d/m/Y G:i"));
					else
						$fecha_deposito="";
				}
				else
					$fecha_deposito="";
						
				$objPHPExcel->getActiveSheet()->setCellValue('P'.$i, $fecha_deposito);	
			}
			else if($parametros['fin_tipoDocumento'] == 'FAC' || $parametros['fin_tipoDocumento'] == 'FACP' || $parametros['fin_tipoDocumento'] == 'ND' || $parametros['fin_tipoDocumento'] == 'NC')
			{
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, (isset($data['login']) ? trim($data['login']) : "") );
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, (isset($data['estado']) ? trim($data['estado']) : "") );
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, (isset($data['descripcionPunto']) ? trim($data['descripcionPunto']) : "") );
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, (isset($identificacionCliente) ? "$identificacionCliente " : "") );
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, (isset($informacion_cliente) ? trim($informacion_cliente) : "") );
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, (isset($data['id_documento']) ? $data['id_documento'] : ""));
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, trim($data['nombreTipoDocumento']));
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $data['numeroDocumento']);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $valorTotal);
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, trim($automatica));
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, trim($nombreCreador));
				$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, trim($data['estadoDocumentoGlobal']?$data['estadoDocumentoGlobal']:''));
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, strval(date_format($data['feCreacion'],"d/m/Y G:i")));		
				
				if(isset($data['feEmision']))
				{
					if($data['feEmision']!="")
						$fecha_emision=strval(date_format($data['feEmision'],"d/m/Y G:i"));
					else
						$fecha_emision="";
				}
				else
					$fecha_emision="";
						
				$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $fecha_emision);	
				$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $data['codigoTipoNegocio']);		
				$objPHPExcel->getActiveSheet()->setCellValue('P'.$i, $data['nombreOficina']);
				
				$referencia_nd="";
				$comentario_nd="";
					
				if($parametros['fin_tipoDocumento']=='ND')
				{
					//saco con el id_documento el det y el pago_det_id
					//obtengo el numero_pago y lo pongo en la referencia
					$referencia_nd="";
					$comentario_nd="";
					if(isset($data['id_documento']))
					{
						$nd_det = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($data['id_documento']);
						foreach ($nd_det as $nd):
							$pago_det_id=$nd->getPagoDetId();
							if($pago_det_id)
							{
								$pago_det=$emFinanciero->getRepository('schemaBundle:InfoPagoDet')->find($nd->getPagoDetId());
								$referencia_nd.="|".$pago_det->getPagoId()->getNumeroPago();
								$comentario_nd.="|".$nd->getObservacionesFacturaDetalle();
							}
						endforeach;
					}
				}
						
				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $referencia_nd);		
				$objPHPExcel->getActiveSheet()->setCellValue('R'.$i, $comentario_nd);		
			}
			else
			{
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, (isset($data['login']) ? trim($data['login']) : "") );
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, (isset($data['estado']) ? trim($data['estado']) : "") );
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, (isset($data['descripcionPunto']) ? trim($data['descripcionPunto']) : "") );
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, (isset($identificacionCliente) ? "$identificacionCliente " : "") );
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, (isset($informacion_cliente) ? trim($informacion_cliente) : "") );
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, (isset($data['id_documento']) ? $data['id_documento'] : ""));
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, (isset($data['id_documento_detalle']) ? $data['id_documento_detalle'] : ""));
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, trim($data['nombreTipoDocumento']));
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $data['numeroDocumento']);
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $valorTotal);
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, trim($automatica));
				$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, (isset($data['descripcionFormaPago']) ? $data['descripcionFormaPago'] : ""));
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, "$referencia ");
				$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, (isset($data['comentarioDetallePago']) ? $data['comentarioDetallePago'] : ""));
				$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, trim($nombreBanco));
				$objPHPExcel->getActiveSheet()->setCellValue('P'.$i, trim($nombreDocumentoReferencia));
				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, "$noDocumentoReferencia ");
				$objPHPExcel->getActiveSheet()->setCellValue('R'.$i, trim($nombreCreador));
				$objPHPExcel->getActiveSheet()->setCellValue('S'.$i, trim($data['estadoDocumentoGlobal']?$data['estadoDocumentoGlobal']:''));
				$objPHPExcel->getActiveSheet()->setCellValue('T'.$i, strval(date_format($data['feCreacion'],"d/m/Y G:i")));	
				
				if(isset($data['feDeposito']))
				{
					if($data['feDeposito']!="")
						$fecha_deposito=strval(date_format($data['feDeposito'],"d/m/Y G:i"));
					else
						$fecha_deposito="";
				}
				else
					$fecha_deposito="";
					
				$objPHPExcel->getActiveSheet()->setCellValue('U'.$i, $fecha_deposito);
				
				if(isset($data['feProcesado']))
				{
					if($data['feProcesado']!="")
						$fecha_procesado=strval(date_format($data['feProcesado'],"d/m/Y G:i"));
					else
						$fecha_procesado="";
				}
				else
					$fecha_procesado="";
				
				$objPHPExcel->getActiveSheet()->setCellValue('V'.$i, $fecha_procesado);
				
				if(isset($data['noComprobanteDeposito']))
				{
					if($data['noComprobanteDeposito']!="")
						$no_comprobante_deposito=$data['noComprobanteDeposito'];
					else
						$no_comprobante_deposito="";
				}
				else
					$no_comprobante_deposito="";
					
				$objPHPExcel->getActiveSheet()->setCellValue('W'.$i, $no_comprobante_deposito);
				
				if(isset($data['oficinaId']))
				{
					//Entidad oficina - para la presentacion en el pago
					$oficinaId=$em->getRepository('schemaBundle:InfoOficinaGrupo')->find($data['oficinaId']);
					
					if($data['oficinaId']!="")
						$oficina=$oficinaId->getNombreOficina();
					else
						$oficina="";
				}
				else
					$oficina="";
					
				$objPHPExcel->getActiveSheet()->setCellValue('X'.$i, $oficina);
					
			}
			
            $i=$i+1;
        endforeach;
		
		
//        Util::addBorderThinB($objPHPExcel,'A'.($i-1).':I'.($i-1));
        // Merge cells
        // Set document security
        // $objPHPExcel->getSecurity()->setWorkbookPassword("PHPExcel");
        // $objPHPExcel->getSecurity()->setLockWindows(true);
        // $objPHPExcel->getSecurity()->setLockStructure(true);

        // Set sheet security
        // $objPHPExcel->getActiveSheet()->getProtection()->setPassword('PHPExcel');
        // $objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
        // $objPHPExcel->getActiveSheet()->getProtection()->setSheet(true); // This should be enabled in order to enable any of the following!
        // $objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
        // $objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);

        // Set page orientation and size
        //$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //Redirect output to a clients web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_Busqueda_Avanzada_Financiera_'.date('d_M_Y').'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

	
    /**
    * @Secure(roles="ROLE_147-565")
    */
    public function exportarConsulta_BusquedaComercialAction()
    {
	
	}
    /**
    * @Secure(roles="ROLE_147-566")
    */
    public function exportarConsulta_BusquedaPlanificacionAction()
    {
	
	}
    /**
    * @Secure(roles="ROLE_147-567")
    */
    public function exportarConsulta_BusquedaTecnicaAction()
    {
	
	}
    /**
    * @Secure(roles="ROLE_147-568")
    */
    public function exportarConsulta_BusquedaSoporteAction()
    {
	
	}
   
    /**
    * Obtiene listado de noticias a publicar, esta informacion se encuentra registrada
    * en la tabla infoDocumento
    * @return json
    * @author Jesus Bozada P. <jbozada@telconet.ec>
    * @version 1.0 20-08-2014
    */
    public function noticiasAction() {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $start = '';
        $limit = '';
        $strDescripcion = "";
        $strEstado = 'Activo';
        $strEmpresa = "";
        $em_comunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");
        $objClaseDocumento = $em_comunicacion->getRepository('schemaBundle:AdmiClaseDocumento')->findOneBy(
                array('nombreClaseDocumento' => 'Notificacion Interna Noticia'));
        if ($objClaseDocumento){
        // Obtener listado de Noticias servicio PlantillaService
        /* @var servicioPlantilla PlantillaService */
        $servicioPlantilla = $this->get('soporte.ListaPlantilla');
        $respuestaListaPlantillas = $servicioPlantilla
                                  ->listarPlantillas($objClaseDocumento->getId(), $strDescripcion, $strEstado, 
                                                     $strEmpresa, $start, $limit, "SI");
        $respuesta->setContent($respuestaListaPlantillas);
        }
        return $respuesta;
    }

    /**
    * Carga twig con noticia seleccionada por el usuario
    * @param $id identificador de noticia
    * @return json
    * @author Jesus Bozada P. <jbozada@telconet.ec>
    * @version 1.0 20-08-2014
    */
    public function noticiaAction($id) {
       $em = $this->getDoctrine()->getManager('telconet_comunicacion');
       $objInfoDocumento = $em->getRepository('schemaBundle:InfoDocumento')->find($id);
       return $this->render('adminBundle:Inicio:noticia.html.twig', array(
                    'tituloNoticia' => $objInfoDocumento->getNombreDocumento(),
                    'descripcionNoticia' => $objInfoDocumento->getMensaje(),
        ));
    }

    
    /**
     * Documentación para el método 'resumenClienteAction'.
     *
     * Muestra el resumen Comercial, Financiero, Cobranzas, IPPCL y ATC del punto de un
     * cliente.
     *
     * @param integer $intServicio
     * @param integer $intIdPersona
     * @param integer $intPunto
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 21-08-2015
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 10-09-2015 - Se modifica para que se sumen los valores de instalaciones que se le
     *                           realizan al cliente, y además para que se muestre en el campo estado 
     *                           de la información comercial el estado del Servicio, anteriormente se
     *                           mostraba el estado del Cliente.
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 15-09-2015 - Se corrige que a los clientes con estado de servicio 'Cancelado' se
     *                           saque la antiguedad desde el día que se activo el servicio hasta el
     *                           último corte que se le realizó al cliente.
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.3 04-11-2015 - Se corrige que a los clientes con estado de servicio 'Cancelado' se presente la información de forma
     *                           de pago.
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 19-05-2016 - Se realizan ajustes por cambio en los parametros de la funcion generarJsonMisTareas
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.5 08-06-2016
     * Se inicializan los valores de sesión del cliente
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.6 18-06-2016 - Se realiza un ajuste para que en TN busque por productos y retorne la información adecuada.
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 30-06-2016 - Se le envia un parametro mas al generarJsonMisTareas, para que internamente pueda calcular el numero de la tarea
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.8 22-02-2021 - Se agrega la validación para llamar al nuevo proceso de tareas.
     *                         - Se agrega los parámetros de conexión al llamar al proceso de tareas.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.9 13-01-2023 - Se agrega funcionalidad para validar perfil e insertar log asociado.
     */
    public function resumenClienteAction( $intServicio, $intIdPersona, $intPunto )
    {
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        
        $objSession->set('numServicios',   0);
        $objSession->set('serviciosPunto', '');
        $objSession->set('datosFinancierosPunto', '');
        
        $jsonHistorial               = null;
        $arrayClienteContactos       = array();
        $arraySaldoPunto             = array();
        $arrayPromociones            = array();
        $arrayDebitos                = array();
        $arrayCasos                  = array();
        $arrayTareas                 = array();
        $boolError                   = false;
        $strEstado                   = '';
        $strFechaAntiguedad          = '';
        $strAntiguedad               = '';
        $strEsVenta                  = 'NO';
        $strEsPadreFacturacion       = 'NO';
        $strSaldoCliente             = '0,00';
        $intSuspensiones             = 0;
        $strFormaPago                = '';
        $strValorInstalacion         = '';
        $strPagoInstalacion          = 'NO';
        $strPlan                     = '';
        $strLimit                    = '3';
        $strStart                    = '0';
        $intTotalCasos               = 0;
        $intTotalTareas              = 0;
        $strEstadoSolicitud          = 'NO TIENE SOLICITUD RETIRO';
        $intNumeroTareasPresenciales = 0;
        $strEstadoServicio           = '';
        
        $strModuloActivo    = strtolower($objSession->get('modulo_activo'));
        $strNombreModulo    = ucfirst( $objSession->get('nombre_menu_modulo_activo'));
        $strImagenModulo    = $objSession->get('imagen_menu_modulo_activo');
        $strCodEmpresa      = $objSession->get('idEmpresa');
                                                                                                                                                               
        $emComercial       = $this->getDoctrine()->getManager();
        $emComunicacion    = $this->getDoctrine()->getManager("telconet_comunicacion");
        $emSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $emFinanciero      = $this->getDoctrine()->getManager('telconet_financiero');
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $strUsrCreacion     = $objSession->get('user');
        $arrayCliente       = $objSession->get('cliente');
        $arrayPtoCliente    = $objSession->get('ptoCliente'); 
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');  
        $strIpCreacion      = $objRequest->getClientIp();
        $serviceInfoLog     = $this->get('comercial.InfoLog');
        $serviceTokenCas    = $this->get('seguridad.TokenCas');
        $arrayDatosCliente  = array();
        
        
        if($strPrefijoEmpresa == 'MD' &&  (true === $this->get('security.context')->isGranted('ROLE_151-8897')))
        { 

            if(!empty($arrayCliente))
            {
                 $objInfoPersona  = $emComercial->getRepository('schemaBundle:InfoPersona')->findOneById($arrayCliente['id']);

                 if(is_object($objInfoPersona))
                 {
                     $arrayDatosCliente['nombres']            = $objInfoPersona->getNombres();
                     $arrayDatosCliente['apellidos']          = $objInfoPersona->getApellidos();
                     $arrayDatosCliente['razon_social']       = $objInfoPersona->getRazonSocial();
                     $arrayDatosCliente['identificacion']     = $objInfoPersona->getIdentificacionCliente();
                     $arrayDatosCliente['tipoTributario']     = $objInfoPersona->getTipoTributario();
                     $arrayDatosCliente['tipoIdentificacion'] = $objInfoPersona->getTipoIdentificacion();
                     $arrayDatosCliente['login']              = $arrayPtoCliente['login'];
                 }                 
            }
            $strOrigen        = '';
            $strMetodo        = '';
            $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                            ->findOneBy(array('nombreParametro' => 'VISUALIZACION LOGS', 
                                                              'estado'          => 'Activo'));
            if(is_object($objAdmiParametroCab))
            {              
                $objParamDetOrigen = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                   'descripcion' => 'ORIGEN',
                                                                   'empresaCod'  => $strCodEmpresa,
                                                                   'estado'      => 'Activo'));

                $objParamDetMetodo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array('parametroId'     => $objAdmiParametroCab,
                                                                   'observacion'     => 'RESUMEN CLIENTE',
                                                                   'empresaCod'      => $strCodEmpresa,
                                                                   'estado'          => 'Activo'));           
                if(is_object($objParamDetOrigen))
                {
                    $strOrigen  = $objParamDetOrigen->getValor1();
                }

                if(is_object($objParamDetMetodo))
                {
                    $strMetodo  = $objParamDetMetodo->getValor1();
                }             
            }         

            $arrayParametrosLog                   = array();
            $arrayParametrosLog['strOrigen']      = $strOrigen;
            $arrayParametrosLog['strMetodo']      = $strMetodo;
            $arrayParametrosLog['strTipoEvento']  = 'INFO';
            $arrayParametrosLog['strIpUltMod']    = $strIpCreacion;
            $arrayParametrosLog['strUsrUltMod']   = $strUsrCreacion;
            $arrayParametrosLog['dateFechaEvento']= date("Y-m-d h:i:s");
            $arrayParametrosLog['strIdKafka']     = '';
            $arrayParametrosLog['request']        = $arrayDatosCliente;

            $arrayTokenCas               = $serviceTokenCas->generarTokenCas();
            $arrayParametrosLog['token'] = $arrayTokenCas['strToken'];
            $serviceInfoLog->registrarLogsMs($arrayParametrosLog);        
        }        
        
        
        $entityPersona = $emComercial->getRepository( 'schemaBundle:InfoPersona' )->findOneById( $intIdPersona );
        
        if (!$entityPersona)
        {
            throw new NotFoundHttpException('No existe el cliente en nuestra base de datos');
        }	
        
        if( !$boolError )
        {
            $arrayTmpCriterios = array(
                                          'intCodEmpresa'    => $strCodEmpresa,
                                          'intIdPersona'     => $intIdPersona,
                                          'strNombreTipoRol' => 'Cliente',
                                          'estados'          => array('Activo', 'Modificado', 'Cancel', 'Cancelado', 'Eliminado')
                                      );
            
            $arrayPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersona')->getInfoPersonaByCriterios( $arrayTmpCriterios );
            
            $intIdPersonaEmpresaRol = 0;
            $arrayUltimoEstado      = null;
            $intIdUltimoEstado      = 0;
            
            if( $arrayPersonaEmpresaRol['idPersonaEmpresaRol'] )
            {
                $intIdPersonaEmpresaRol = $arrayPersonaEmpresaRol['idPersonaEmpresaRol'];
            }
            
            //Estado Actual del Cliente
            $arrayUltimoEstado = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')
                                             ->findUltimoEstadoPorPersonaEmpresaRol( $intIdPersonaEmpresaRol );        
            $intIdUltimoEstado = $arrayUltimoEstado[0]['ultimo'];
            
            if ($intIdUltimoEstado)
            {
                $objUltimoEstado = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->find( $intIdUltimoEstado );
                
                $strEstado = $objUltimoEstado->getEstado();
            }
            //Fin Estado Actual del Cliente
            
            
            //Para saber si es venta y el nombre del plan. Adicional guardo el servicio en session
            $arrayServicioConsultado = array();
            
            $entityServicio = $emComercial->getRepository( 'schemaBundle:InfoServicio' )->findOneById( $intServicio );
            
            if( $entityServicio )
            {
                $strEstadoServicio = $entityServicio->getEstado();
                
                if( $entityServicio->getEsVenta() == 'S' )
                {
                    $strEsVenta = 'SI';
                }
                
                if( $entityServicio->getPlanId() )
                {
                    $strPlan = $entityServicio->getPlanId()->getNombrePlan();
                }
                elseif($entityServicio->getProductoId())
                {
                    $strPlan = $entityServicio->getProductoId()->getDescripcionProducto();
                }
                
                $arrayServicioConsultado = array( "0" => array('nombre' => $strPlan, 'estado' => $strEstadoServicio) );
            }
            
            $objSession->set('numServicios',   'Servicio Consultado por Resumen');
            $objSession->set('serviciosPunto', $arrayServicioConsultado);
            //Fin Para saber si es venta y el nombre del plan. Adicional guardo el servicio en session
            

            //Conocer la Antiguedad y el número de suspensiones
            $jsonHistorial = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                         ->generarJsonHistorialServicio( $intServicio, 0, 50000, $emGeneral );

            if( $jsonHistorial )
            {
                $objHistorial = null;
                $objHistorial = json_decode($jsonHistorial);
                
                if( $objHistorial->total )
                {
                    if( $objHistorial->total > 0 )
                    {
                        $arrayHistorial = null;
                        $arrayHistorial = $objHistorial->encontrados;
                        
                        $strMensajeConfirmaServicio = trim('se confirmo el servicio');
                        
                        if( $strEstadoServicio == 'Activo' )
                        {
                            $strConfirmaServicio = $strMensajeConfirmaServicio;
                        }
                        else
                        {
                            $strConfirmaServicio = trim('el servicio se corto exitosamente');
                        }
                        
                        foreach( $arrayHistorial as $objItemHistorial )
                        {
                            $strObservacion = strtolower(trim($objItemHistorial->observacion));
                            
                            if( $strObservacion == $strMensajeConfirmaServicio )
                            {
                                $arrayFecha         = explode(' ', $objItemHistorial->feCreacion);
                                $strFechaAntiguedad = $arrayFecha[0];
                            }
                            
                            if( $strConfirmaServicio == $strObservacion )
                            {
                                $dateFechaAntiguedad = \DateTime::createFromFormat('d/m/Y H:i:s', $strFechaAntiguedad. ' 00:00:00');
                                
                                if( $strEstadoServicio == 'Activo' )
                                {
                                    $dateActual = new \DateTime('now');
                                }
                                else
                                {
                                    $arrayFechaActual = explode(' ', $objItemHistorial->feCreacion);
                                    $strFechaActual   = $arrayFechaActual[0];
                                    $dateActual       = \DateTime::createFromFormat('d/m/Y H:i:s', $strFechaActual. ' 00:00:00');
                                }
                                
                                $dateDiferencia = $dateFechaAntiguedad->diff($dateActual);
                                
                                $intAnios = $dateDiferencia->format('%y');
                                $intMeses = $dateDiferencia->format('%m');
                                $intDias  = $dateDiferencia->format('%d'); 
                                
                                $strAntiguedad = $intAnios.' Años '.$intMeses.' Meses '.$intDias.' Días';
                            }//( $strConfirmaServicio == $strObservacion )
                            
                            if( trim($objItemHistorial->estado) == 'In-Corte' )
                            {
                                $intSuspensiones++;
                            }
                            
                        }//foreach( $arrayHistorial as $objItemHistorial )
                    }//( $objHistorial->total > 0 )
                }//( $objHistorial->total )
            }//( $jsonHistorial )
            //Fin Conocer la Antiguedad y el número de suspensiones
            
            
            //Para conocer las formas de contacto
            $arrayClienteContactos = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                 ->getFormasContactoParaSession($entityPersona->getId());

            
            //Para conocer el saldo actual
            $arraySaldoPunto = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                            ->getPuntosFacturacionAndFacturasAbiertasByIdPunto($intPunto, $emComercial, $strCodEmpresa);
            
            if( $arraySaldoPunto )
            {
                $strSaldoCliente = '$'.($arraySaldoPunto['saldoCliente'] ? $arraySaldoPunto['saldoCliente'] : '0,00');
            }
            
            $objSession->set('datosFinancierosPunto', $arraySaldoPunto);
            //Fin Para conocer el saldo actual
            
            
            //Para conocer su forma de pago y la descripción de la cuenta
            $strTmpBanco    = '';
            $entityContrato = $emComercial->getRepository('schemaBundle:InfoContrato')->findOneByPersonaEmpresaRolId($intIdPersonaEmpresaRol);
            
            if( $entityContrato )
            {
                if( $entityContrato->getFormaPagoId() )
                {
                    $intTmpIdFormaPago = $entityContrato->getFormaPagoId();
                            
                    $strFormaPago = $intTmpIdFormaPago->getDescripcionFormaPago();
                    
                    if($entityContrato->getEstado() == 'Activo' || $entityContrato->getEstado() == 'Cancelado')
                    {
                        $entityInfoContratoFormaPago = $emComercial->getRepository('schemaBundle:InfoContratoFormaPago')
                                                                   ->findOneByContratoId($entityContrato->getId());
                        
                        if( $entityInfoContratoFormaPago )
                        {
                            $entityBancoTipoCuenta = $emComercial->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                                                                 ->find($entityInfoContratoFormaPago->getBancoTipoCuentaId());

                            /*
                             * Si el tipo cuenta es tarjeta entonces muestra Descripción de Cuenta
                             * Si no lo es entonces muestra Descripción del Banco
                             */
                            if( $entityBancoTipoCuenta->getTipoCuentaId() )
                            {
                                if($entityBancoTipoCuenta->getTipoCuentaId()->getEsTarjeta() == 'S')
                                {
                                    $strTmpBanco = $entityBancoTipoCuenta->getTipoCuentaId()->getDescripcionCuenta();
                                }
                                else
                                {
                                    $strTmpBanco = $entityBancoTipoCuenta->getBancoId()->getDescripcionBanco();
                                }
                            } 
                        }
                    }
                }//( $entityContrato->getFormaPagoId() )
            }//( $entityContrato )
            //Fin Para conocer su forma de pago y la descripción de la cuenta
            
            
            //Para conocer si es Padre de Facturación
            $entityPuntoDatoAdicional = $emComercial->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($intPunto);
            
            if( $entityPuntoDatoAdicional )
            {
                if( $entityPuntoDatoAdicional->getEsPadreFacturacion() == 'S' )
                {
                    $strEsPadreFacturacion = 'SI';
                }
                else
                {
                    if( $entityServicio )
                    {
                        $strEsPadreFacturacion = $entityServicio->getPuntoFacturacionId()->getLogin();
                    }
                }
            }
            //Fin Para conocer si es Padre de Facturación
            
            
            //Para conocer si canceló algún valor por la instalación y el monto que canceló
            $arrayPagoInstalacion = $emFinanciero->getRepository('schemaBundle:InfoPlanCab')->getValorInstalacion($intPunto);

            if( $arrayPagoInstalacion )
            {
                $strPagoInstalacion  = 'SI';
                $intValorInstalacion = 0;

                foreach( $arrayPagoInstalacion as $arrayInstalacion )
                {
                    $intValorInstalacion += floatval($arrayInstalacion['precioVentaFacproDetalle']);
                }
                
                $strValorInstalacion = '$'.number_format($intValorInstalacion, 2, ',', '.');
            }
            //Fin Para conocer si canceló algún valor por la instalación y el monto que canceló
            
            
            //Para conocer si tiene promociones
            $arrayPromociones = $emComercial->getRepository('schemaBundle:InfoServicio')->getPromocionesDelServicio($intPunto);
            
            
            //Retorno de la información técnica como OLT, Linea PON y SLOT
            $dataTecnica      = $this->get('tecnico.DataTecnica');
            
            $arrayPeticiones  = array(  'idServicio'    => $intServicio,
                                        'idEmpresa'     => $strCodEmpresa   );
            
            $arrayDataTecnica = $dataTecnica->getDataTecnica($arrayPeticiones);
            //Fin Retorno de la información técnica como OLT, Linea PON y SLOT
            
            
            //Estado de Solicitud de Entrega de Equipos
            $strTmpLogin     = $entityServicio->getPuntoId()->getLogin();
            
            $jsonSolicitudes = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                           ->generarJsonSolicitudes( $emInfraestructura,
                                                                     $emGeneral,
                                                                     0, 
                                                                     1000, 
                                                                     '',
                                                                     '',
                                                                     $strTmpLogin, 
                                                                     '',
                                                                     '',
                                                                     '',
                                                                     '',
                                                                     9,//Solicitud de Retiros
                                                                     $strCodEmpresa
                                                                   );
            if( $jsonSolicitudes )
            {
                $arrayTmpSolicitudesResultado = json_decode($jsonSolicitudes);
                
                if( $arrayTmpSolicitudesResultado->total > 0 )
                {
                    $arrayTmpSolicitudes  = $arrayTmpSolicitudesResultado->encontrados;
                    $intTmpUltimoRegistro = $arrayTmpSolicitudesResultado->total -1;
                    $objEstadoSolicitud   = $arrayTmpSolicitudes[$intTmpUltimoRegistro];

                    if( $objEstadoSolicitud->estado == 'Finalizada' )
                    {
                        $strEstadoSolicitud = 'SI';
                    }
                    else
                    {
                        $strEstadoSolicitud = 'PENDIENTE';
                    }
                }
            }
            //Fin Estado de Solicitud de Entrega de Equipos
            
            
            //Historial de Débitos
            $arrayTmpDebitos = $emFinanciero->getRepository('schemaBundle:InfoDebitoDet')
                                            ->findDebitosPorPersonaEmpresaRolId($intIdPersonaEmpresaRol, $strLimit, $strStart);
            
            $arrayTmpDatos = $arrayTmpDebitos['registros'];
            
            foreach( $arrayTmpDatos as $objTmpDato )
            {
                $entityCabeceraDebito = $emFinanciero->getRepository('schemaBundle:InfoDebitoCab')->find($objTmpDato->getDebitoCabId());
                
                $strTmpFechaProceso = '';
                $strTmpUsrProceso   = '';
                
                if(!$objTmpDato->getFeUltMod())
                {
                    if( $entityCabeceraDebito )
                    {
                        $strTmpFechaProceso = strval(date_format($entityCabeceraDebito->getFeUltMod(), "d/m/Y G:i"));
                        $strTmpUsrProceso   = $entityCabeceraDebito->getUsrUltMod();
                    }
                }
                else
                {
                    $strTmpFechaProceso = strval(date_format($objTmpDato->getFeUltMod(), "d/m/Y G:i"));
                    $strTmpUsrProceso   = $objTmpDato->getUsrUltMod();
                }
                
                /*
                 * Si el estado es Procesado entonces obtiene valor debitado porque existe posibilidad 
                 * que no se haya debitado todo lo que se envio al banco.
                 * Si no es Procesado entonces obtiene el valor total que es el valor que se envio a
                 * debitar.
                 */
                $strTmpValorDebito = '';
                
                if($objTmpDato->getEstado()=='Procesado')
                {
                    $strTmpValorDebito = $objTmpDato->getValorDebitado();
                }
                else
                {
                    $strTmpValorDebito = $objTmpDato->getValorTotal();
                } 
                
                $dateTmpFeCreacion = date_format($objTmpDato->getFeCreacion(), "d/m/Y G:i");
                
                $arrayDebitos[] = array(
                                            'id'                 => $objTmpDato->getId(),
                                            'banco'              => $strTmpBanco,
                                            'total'              => $strTmpValorDebito,
                                            'fechaCreacion'      => strval($dateTmpFeCreacion),
                                            'usuarioCreacion'    => $strTmpUsrProceso,
                                            'estado'             => $objTmpDato->getEstado(),
                                            'observacionRechazo' => $objTmpDato->getObservacionRechazo(),
                                            'fechaProceso'       => $strTmpFechaProceso
                                       );
            }//foreach( $arrayTmpDatos as $objTmpDato )
            //Fin Historial de Débitos
            
            
            //Número de Casos
            $arrayTmpParametrosCasos = array();
            
            $strClienteAfectado = ( $entityPersona ? ( $entityPersona->getRazonSocial() ? 
                                    $entityPersona->getRazonSocial() : 
                                    $entityPersona->getNombres()." ".$entityPersona->getApellidos() ) : "" );
            $strLoginPunto      = ( $entityServicio ? ( $entityServicio->getPuntoId() ? 
                                    $entityServicio->getPuntoId()->getLogin() : "" ) : "" );

            $arrayTmpParametrosCasos['clienteAfectado']    = $strClienteAfectado;
            $arrayTmpParametrosCasos['loginAfectado']      = $strLoginPunto;
            $arrayTmpParametrosCasos['idEmpresaSeleccion'] = $strCodEmpresa;

            $jsonCasos = $emSoporte->getRepository('schemaBundle:InfoCaso')
                                   ->generarJsonCasos( $arrayTmpParametrosCasos,
                                                       $strStart,
                                                       $strLimit, 
                                                       $objSession, 
                                                       $emComercial, 
                                                       null,
                                                       $emInfraestructura, 
                                                       $emGeneral,
                                                       $emComunicacion);
            
            if( $jsonCasos )
            {
                $arrayTmpCasosResultado = json_decode($jsonCasos);
                
                if( $arrayTmpCasosResultado->total > 0 )
                {
                    $arrayCasos    = $arrayTmpCasosResultado->encontrados;
                    $intTotalCasos = $arrayTmpCasosResultado->total;
                }
            }
            //Fin Número de Casos
            
            
            //Número de Tareas    
            $arrayTmpParametrosTareas                   = array();
            $arrayTmpParametrosTareas["cliente"]        = $intPunto;
            $arrayTmpParametrosTareas["idDepartamento"] = null;        
            $arrayTmpParametrosTareas["idCuadrilla"]    = null;

            $arrayTmpParametrosTareas["emComercial"]         = $emComercial;
            $arrayTmpParametrosTareas["emComunicacion"]      = $emComunicacion;
            $arrayTmpParametrosTareas["start"]               = $strStart;
            $arrayTmpParametrosTareas["limit"]               = $strLimit;
            $arrayTmpParametrosTareas["isDepartamento"]      = false;
            $arrayTmpParametrosTareas["departamentoSession"] = "";
            $arrayTmpParametrosTareas["existeFiltro"]        = "S";
            $arrayTmpParametrosTareas["ociCon"] = array('userSoporte' => $this->container->getParameter('user_soporte'),
                                                        'passSoporte' => $this->container->getParameter('passwd_soporte'),
                                                        'databaseDsn' => $this->container->getParameter('database_dsn'));

            $arrayNuevoProcesoTarea = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('VALIDACION PARA USAR NUEVA FUNCION GRID TAREAS','SOPORTE','','','','','','','','');

            if (!empty($arrayNuevoProcesoTarea) && $arrayNuevoProcesoTarea['valor1'] == 'S' &&
               ($arrayNuevoProcesoTarea['valor2'] == '' || $arrayNuevoProcesoTarea['valor2'] == $objSession->get('idDepartamento')))
            {
                $strJsonTareas = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                        ->generarJsonInfoTareas($arrayTmpParametrosTareas);
            }
            else
            {
                $strJsonTareas = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                        ->generarJsonMisTareas($arrayTmpParametrosTareas);
            }

            if ($strJsonTareas)
            {
                $arrayTmpTareasResultado = json_decode($strJsonTareas);
                if ($arrayTmpTareasResultado->total > 0)
                {
                    $arrayTareas    = $arrayTmpTareasResultado->encontrados;
                    $intTotalTareas = $arrayTmpTareasResultado->total;
                }
            }
            //Fin Número de Tareas
            
            
            //Número de CAVs (Tareas de tipo Presencial)
            $intFormaContacto           = 85; //Presencial
            $arrayTmpTareasPresenciales = null;
            $arrayTmpTareasPresenciales = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                                                    ->getNumeroTareasByFormaContacto( $intFormaContacto, $intPunto );
            
            if( $arrayTmpTareasPresenciales )
            {
                $intNumeroTareasPresenciales = $arrayTmpTareasPresenciales['contador'];
            }
            //Fin Número de CAVs (Tareas de tipo Presencial)
            
        }//( !$boolError )
       

        return ( $this->render( $strModuloActivo.'Bundle:Default:resumenCliente.html.twig',
                                array( 
                                        'menu_title'     => $strNombreModulo, 
                                        'menu_imagen'    => $strImagenModulo,
                                        'cliente'        => $entityPersona,
                                        'dataComercial'  => array(
                                                                     'estado'          => $strEstadoServicio,
                                                                     'fechaAntiguedad' => $strFechaAntiguedad,
                                                                     'antiguedad'      => $strAntiguedad,
                                                                     'formasContacto'  => $arrayClienteContactos
                                                                 ),
                                        'dataFinanciero' => array(
                                                                     'saldo'              => $strSaldoCliente,
                                                                     'suspensiones'       => $intSuspensiones,
                                                                     'esVenta'            => $strEsVenta,
                                                                     'formaPago'          => $strFormaPago,
                                                                     'esPadreFacturacion' => $strEsPadreFacturacion,
                                                                     'valorInstalacion'   => $strValorInstalacion,
                                                                     'pagoInstalacion'    => $strPagoInstalacion,
                                                                     'promociones'        => $arrayPromociones,
                                                                 ),
                                        'dataIpccAtc'    => array(
                                                                     'cavs'          => $intNumeroTareasPresenciales,
                                                                     'plan'          => $strPlan,
                                                                     'dataTecnica'   => $arrayDataTecnica,
                                                                     'numCasos'      => $intTotalCasos,
                                                                     'retiroEquipos' => $strEstadoSolicitud
                                                                 ),
                                        'debitos'        => $arrayDebitos,
                                        'casos'          => $arrayCasos,
                                        'tareas'         => $arrayTareas,
                                        'persona'        => $intIdPersona, 
                                        'punto'          => $intPunto,
                                        'perEmpresaRol'  => $intIdPersonaEmpresaRol
                                     )
                              )
               );
    }
    
    
    
    /**
     * Documentación para el método 'ventanaSeguimientoTareaAction'.
     *
     * Retorna la tabla de seguimiento de una tarea.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 21-08-2015
     */
    public function ventanaSeguimientoTareaAction( )
    {
        $objRequest = $this->get('request');
        $objSession = $objRequest->getSession();
        
        $jsonRespuesta   = null;
        $strCodEmpresa   = '';
        $arrayResultados = array();
        
        $strCodEmpresa = $objSession->get('idEmpresa');
        
        $jsonRespuesta = $objRequest->query->get('respuestaJson');    
        
        if( $jsonRespuesta )
        {
            $objRespuesta = json_decode($jsonRespuesta);
            
            $arrayResultados = $objRespuesta->encontrados;
        }
        
        return $this->render( 'inicioBundle:Default:tablaSeguimientoTarea.html.twig', 
                              array( 'resultados' => $arrayResultados )
                            );
    }
}