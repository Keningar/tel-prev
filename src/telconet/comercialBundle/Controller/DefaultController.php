<?php

namespace telconet\comercialBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller implements TokenAuthenticatedController
{
    
    public function indexAction($name)
    {
        return $this->render('comercialBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function menuAction($opcion_menu)
    {
		/*
		if (true === $this->get('security.context')->isGranted('ROLE_6-1'))
        {
			return $this->render('comercialBundle:PreCliente:index.html.twig');
		}	
		*/	
        if (true === $this->get('security.context')->isGranted('ROLE_19-1'))
        {
            return $this->forward('seguridadBundle:Default:dashboard', array('modulo' =>'comercial','opcion_menu' =>$opcion_menu));
        }
		
        return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                        'mensaje' => 'No tiene permisos para usar la aplicacion.'));
    }
    
	public function ajaxServiciosProductoMesAction()
	{
		$request = $this->getRequest();
		$session  = $request->getSession();
        $em = $this->get('doctrine')->getManager('telconet');				
		$codigoEstado=$request->query->get('est');
		
		$fechaActual=date('l Y');
		$fechaActual="1 ".$fechaActual;
		//$fechaActual="1 Jan 2012";
		$fechaComparacion = strtotime($fechaActual);
		$calculo= strtotime("31 days", $fechaComparacion); //Le aumentamos 31 dias
		$fechaFin= date("Y-m-d", $calculo);
		$fechaIni= date('Y-m')."-01";

		$Productos= $em->getRepository('schemaBundle:AdmiProducto')->findProductosActivadosPorRangoFechas(new \DateTime($fechaIni),new \DateTime($fechaFin));
		//	
			
		//print_r (new \DateTime($fechaIni));die();
		//echo $fechaIni." ".$fechaFin;die();
		//print_r($Productos);die();
				
		foreach($Productos as $dato){	
			//echo $dato['producto']." ".$dato['total']." -+- ";
			$arreglo[]= array(
				'name'=> sprintf("%s",$dato['producto']),
				'data1'=> sprintf("%s",$dato['total'])
				);  
		}	
		if (empty($arreglo)){
			$arreglo[]= array(
				'name'=> "",
				'data1'=> ""
				);  
		}
		//die();
		$response = new Response(json_encode(array('productos'=>$arreglo)));
		$response->headers->set('Content-type', 'text/json');
		return $response;	
	}


    /**
     * Documentación para el método 'getInformacionGraficosDashboardComercialAction'.
     *
     * Método que retorna la información correspondiente para ser presentada en el Dashboard Comercial.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 10-06-2017
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 12-06-2017 - Se obtiene la información comercial dependiendo del usuario en sessión
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 26-03-2018 - Se agregan parametros para consultar datos de facturación al esquema BI_FINANCIERO
     */
    public function getInformacionGraficosDashboardComercialAction()
    {
        $objJsonResponse         = new JsonResponse();
        $objRequest              = $this->get('request');
        $objSession              = $objRequest->getSession();
        $strPrefijoEmpresa       = $objSession->get('prefijoEmpresa');
        $strUsrCreacion          = $objSession->get('user');
        $intIdPersonEmpresaRol   = $objSession->get('idPersonaEmpresaRol');
        $strIpCreacion           = $objRequest->getClientIp();
        $strCategoria            = $objRequest->request->get('strCategoria') ? $objRequest->request->get('strCategoria') : '';
        $strGrupo                = $objRequest->request->get('strGrupo') ? $objRequest->request->get('strGrupo') : '';
        $strSubgrupo             = $objRequest->request->get('strSubgrupo') ? $objRequest->request->get('strSubgrupo') : '';
        $strFechaInicio          = $objRequest->request->get('strFechaInicio') ? $objRequest->request->get('strFechaInicio') : "";
        $strFechaFin             = $objRequest->request->get('strFechaFin') ? $objRequest->request->get('strFechaFin') : '';
        $serviceComercial        = $this->get('comercial.Comercial');
        $strUserComercial        = $this->container->getParameter('user_comercial');
        $strPasswordComercial    = $this->container->getParameter('passwd_comercial');
        $strUserBiFinanciero     = $this->container->getParameter('user_bifinanciero');
        $strPasswordBiFinanciero = $this->container->getParameter('passwd_bifinanciero');
        $strDatabaseDsn          = $this->container->getParameter('database_dsn');
        $serviceUtil             = $this->get('schema.Util');
        $strTipoPersonal         = "";
        $arrayResponse           = array('boolSuccess'     => false, 
                                         'strMensajeError' => 'No se ha encontrado información actualizada de las ventas para la categoría o grupo '.
                                                              'seleccionado.');
        try
        {
            /**
             * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL' ASOCIADA A EL USUARIO LOGONEADO 
             */
            $arrayPersonalEmpresaRol       = array('intIdPersonEmpresaRol' => $intIdPersonEmpresaRol,
                                                   'strUsrCreacion'        => $strUsrCreacion,
                                                   'strIpCreacion'         => $strIpCreacion);            
            $arrayResultadoCaracteristicas = $serviceComercial->getCaracteristicasPersonalEmpresaRol($arrayPersonalEmpresaRol);
            
            if( !empty($arrayResultadoCaracteristicas) )
            {
                if( isset($arrayResultadoCaracteristicas['strCargoPersonal']) && !empty($arrayResultadoCaracteristicas['strCargoPersonal']) )
                {
                    $strTipoPersonal = $arrayResultadoCaracteristicas['strCargoPersonal'];
                }//( isset($arrayResultadoCaracteristicas['strCargoPersonal']) && !empty($arrayResultadoCaracteristicas['strCargoPersonal']) )
            }//( !empty($arrayResultadoCaracteristicas) )            
            if( !empty($strGrupo) )
            {
                if(strpos($strGrupo,'Trimestre'))
                {
                    $strGrupo = trim(str_replace('(Trimestre)','',$strGrupo));
                }
                elseif(strpos($strGrupo,'Mensual'))
                {
                    $strGrupo = trim(str_replace('(Mensual)','',$strGrupo));                                
                }
            }            
            $arrayParametrosServiceComercial = array('strPrefijoEmpresa'       => $strPrefijoEmpresa,
                                                     'strFechaInicio'          => $strFechaInicio,
                                                     'strFechaFin'             => $strFechaFin,
                                                     'strUsrCreacion'          => $strUsrCreacion,
                                                     'strIpCreacion'           => $strIpCreacion,
                                                     'strDatabaseDsn'          => $strDatabaseDsn,
                                                     'strUserComercial'        => $strUserComercial,
                                                     'strPasswordComercial'    => $strPasswordComercial,
                                                     'strUserBiFinanciero'     => $strUserBiFinanciero,
                                                     'strPasswordBiFinanciero' => $strPasswordBiFinanciero,
                                                     'strCategoria'            => $strCategoria,
                                                     'strTipoPersonal'         => $strTipoPersonal,
                                                     'intIdPersonEmpresaRol'   => $intIdPersonEmpresaRol,
                                                     'strGrupo'                => $strGrupo,
                                                     'strSubgrupo'             => $strSubgrupo);

            $arrayResultadoService = $serviceComercial->getInformacionDashboard($arrayParametrosServiceComercial);
            
            if( !empty($arrayResultadoService) )
            {
                $arrayResponse['boolSuccess']     = true;
                $arrayResponse['strMensajeError'] = '';
                $arrayResponse                    = array_merge($arrayResponse, $arrayResultadoService);
            }//( !empty($arrayResultadoService) )
        }
        catch( \Exception $e )
        {
            $serviceUtil->insertError('TELCOS+',
                                      'SeguridadBundle.DefaultController.getInformacionGraficosDashboardComercialAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        
        $objJsonResponse->setData($arrayResponse);
        
        return $objJsonResponse;
    }


    /**
     * Documentación para el método 'getDetalleVentasAction'.
     *
     * Método que retorna la información detallada de las ventas según el tipo enviado por el usuario
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 15-06-2017
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 12-07-2017 - Se agrega la variable '$strAccion' para validar si va a consultar los detalles o si va a generar el excel para 
     *                           exportar el detallado de los servicios que se han considerado en cada uno de los cuadros presentados en el DASHBOARD
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 18-10-2017 - Se añade clase 'vertical-align' para que las columnas de los detalles esten alineadas verticalmente.
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.3 17-08-2017 - Se añade logica para retornar información detallada para los subgerentes y vendedores.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.4 21-09-2020 - Se añade lógica para retornar información relacionada con las propuestas de TelcoCRM.
     *
     */
    public function getDetalleVentasAction()
    {
        $objJsonResponse       = new JsonResponse();
        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $strEmpresaCod         = $objSession->get('idEmpresa');
        $strPrefijoEmpresa     = $objSession->get('prefijoEmpresa');
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $strUsrCreacion        = $objSession->get('user');
        $strIpCreacion         = $objRequest->getClientIp();
        $strCategoria          = $objRequest->request->get('strCategoria') ? $objRequest->request->get('strCategoria') : '';
        $strGrupo              = $objRequest->request->get('strGrupo') ? $objRequest->request->get('strGrupo') : '';
        $strSubgrupo           = $objRequest->request->get('strSubgrupo') ? $objRequest->request->get('strSubgrupo') : '';
        $strTipo               = $objRequest->request->get('strTipo') ? $objRequest->request->get('strTipo') : '';
        $strFechaInicio        = $objRequest->request->get('strFechaInicio') ? $objRequest->request->get('strFechaInicio') : "";
        $strFechaFin           = $objRequest->request->get('strFechaFin') ? $objRequest->request->get('strFechaFin') : '';
        $strAccion             = $objRequest->request->get('strAccion') ? $objRequest->request->get('strAccion') : 'CONSULTA';
        $strUserComercial      = $this->container->getParameter('user_comercial');
        $strPasswordComercial  = $this->container->getParameter('passwd_comercial');
        $strDatabaseDsn        = $this->container->getParameter('database_dsn');
        $serviceUtil           = $this->get('schema.Util');
        $serviceComercial      = $this->get('comercial.Comercial');        
        $emComercial           = $this->getDoctrine()->getManager('telconet');
        $emGeneral             = $this->getDoctrine()->getManager('telconet_general');
        $strTipoPersonal       = "";
        $arrayResponse         = array('boolSuccess'         => false, 
                                       'strMensajeRespuesta' => 'No se ha encontrado información solicitada.',
                                       'strBodyModal'        => '<div class="row">'.
                                                                '  <div class="col-lg-2">&nbsp;</div>'.
                                                                '  <div class="col-lg-8">No se han encontrado '.$strTipo.'.</div>'.
                                                                '  <div class="col-lg-2">&nbsp;</div>'.
                                                                '</div>');

        try
        {
            /**
             * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL' ASOCIADA A EL USUARIO LOGONEADO 
             */
            $arrayPersonalEmpresaRol       = array('intIdPersonEmpresaRol' => $intIdPersonEmpresaRol,
                                                   'strUsrCreacion'        => $strUsrCreacion,
                                                   'strIpCreacion'         => $strIpCreacion);
            $arrayResultadoCaracteristicas = $serviceComercial->getCaracteristicasPersonalEmpresaRol($arrayPersonalEmpresaRol);
            
            if( !empty($arrayResultadoCaracteristicas) )
            {
                if( isset($arrayResultadoCaracteristicas['strCargoPersonal']) && !empty($arrayResultadoCaracteristicas['strCargoPersonal']) )
                {
                    $strTipoPersonal = $arrayResultadoCaracteristicas['strCargoPersonal'];
                    
                }//( isset($arrayResultadoCaracteristicas['strCargoPersonal']) && !empty($arrayResultadoCaracteristicas['strCargoPersonal']) )
            }//( !empty($arrayResultadoCaracteristicas) )
            
            if( !empty($strTipo) )
            {
                if( !empty($strGrupo) )
                {
                    if(strpos($strGrupo,'Trimestre'))
                    {
                        $strGrupo = trim(str_replace('(Trimestre)','',$strGrupo));
                    }
                    elseif(strpos($strGrupo,'Mensual'))
                    {
                        $strGrupo = trim(str_replace('(Mensual)','',$strGrupo));
                    }
                }                
                $arrayParametrosGenerales = array('strPrefijoEmpresa'    => $strPrefijoEmpresa,
                                                  'strFechaInicio'       => $strFechaInicio,
                                                  'strFechaFin'          => $strFechaFin,
                                                  'strUsrCreacion'       => $strUsrCreacion,
                                                  'strIpCreacion'        => $strIpCreacion,
                                                  'strDatabaseDsn'       => $strDatabaseDsn,
                                                  'strUserComercial'     => $strUserComercial,
                                                  'strPasswordComercial' => $strPasswordComercial,
                                                  'strCategoria'         => $strCategoria,
                                                  'strTipoPersonal'      => $strTipoPersonal,
                                                  'intIdPersonEmpresaRol'=> $intIdPersonEmpresaRol,
                                                  'strGrupo'             => $strGrupo,
                                                  'strSubgrupo'          => $strSubgrupo);           
            
                if ( $strAccion == "CONSULTA" )
                {
                    //CONSULTO LOS ESTADOS PERMITIDOS PARA OBTENER EL DETALLE DE LAS VENTAS SEGUN LO SELECCIONADO POR EL USUARIO
                    $intContadorPrimeraFila               = 0;
                    $intTotalVentasAcumuladas             = 0;
                    $floatTotalVentasAcumuladas           = 0;
                    $intTotalDescuentosVentasAcumuladas   = 0;
                    $floatTotalDescuentosVentasAcumulados = 0;
                    $arrayParametrosDet                   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                      ->get("DASHBOARD_COMERCIAL",
                                                                            "COMERCIAL", 
                                                                            "REPORTES", 
                                                                            strtoupper($strTipo), 
                                                                            "", 
                                                                            "", 
                                                                            "", 
                                                                            "",
                                                                            "",
                                                                            $strEmpresaCod,
                                                                            "valor5" );

                    if( !empty($arrayParametrosDet) )
                    {
                        $boolSuccess = false;
                        foreach($arrayParametrosDet as $arrayParametroDet)
                        {
                            if( isset($arrayParametroDet['valor2']) && !empty($arrayParametroDet['valor2']) )
                            {
                                $arrayParametros                    = $arrayParametrosGenerales;
                                $arrayParametros['strTipoOrdenes']  = $arrayParametroDet['valor2'];
                                $arrayParametros['strOpcionSelect'] = $arrayParametroDet['valor4']=='DESCUENTO'?$arrayParametroDet['valor4']:'NULL';
                                $arrayResultados                    = $serviceComercial->getInformacionVentasTelcosCRM($arrayParametros);
                                if((is_array($arrayResultados) && !empty($arrayResultados))
                                    && (isset($arrayResultados["arrayOrdenes"]) && !empty($arrayResultados["arrayOrdenes"])))
                                {
                                    $intValorColumna = 2;
                                    if(isset($arrayParametroDet['valor4']) && $arrayParametroDet['valor4'] == 'DESCUENTO')
                                    {
                                        $intValorColumna = 1;
                                        $strCabModalDesc ='<div class="col-lg-1 modal-header" style="text-align: center;">
                                                             <h6><b>Descuento</b></h6>
                                                           </div>';
                                    }
                                    $strCabModal = '<div class="row" style="margin-top:5px; padding: 5px 5px;">
                                                        <div class="col-lg-2 modal-header" style="text-align: center;">
                                                            <h6><b>Cant. Propuesta</b></h6>
                                                        </div>
                                                        <div class="col-lg-2 modal-header" style="text-align: center;">
                                                            <h6><b>Cant. &Oacute;rd no CRM</b></h6>
                                                        </div>
                                                        <div class="col-lg-2 modal-header" style="text-align: center;">
                                                            <h6><b>Cant. &Oacute;rd CRM</b></h6>
                                                        </div>
                                                        <div class="col-lg-'.$intValorColumna.' modal-header" style="text-align: center;">
                                                            <h6><b>Vendedor</b></h6>
                                                        </div>
                                                        '.$strCabModalDesc.'
                                                        <div class="col-lg-2 modal-header" style="text-align: center;">
                                                            <h6><b>MRC</b></h6>
                                                        </div>
                                                        <div class="col-lg-2 modal-header" style="text-align: center;">
                                                            <h6><b>NRC</b></h6>
                                                        </div>
                                                    </div>';

                                    $strBodyModal .= '<div class="row" style="margin-top:1px; padding: 10px 10px;">
                                                        <div class="col-lg-12 modal-header" style="text-align: center;">
                                                            <h4><b>'.( (isset($arrayParametroDet['valor1']) && !empty($arrayParametroDet['valor1']))
                                                                ? $arrayParametroDet['valor1'] : '' ).'</b></h4>
                                                        </div>
                                                        </div>';

                                    $strBodyModal .= $strCabModal;
                                    foreach($arrayResultados["arrayOrdenes"] as $arrayItemResultados)
                                    {
                                        $boolSuccess = true;
                                        if( isset($arrayParametroDet['valor4']) && $arrayParametroDet['valor4'] == 'DESCUENTO' )
                                        {
                                            $strBodyModalDesc ='<div class="col-lg-1" style="text-align: center;">
                                                                 <h6>
                                                                  <b>$ '.number_format($arrayItemResultados['intTotalDescuento'], 2, '.', ',').'</b>
                                                                 </h6>
                                                                </div>';
                                        }
                                        $strBodyModal  .= '<div class="row" style="margin-top:5px; padding: 10px 10px;">
                                                                <div class="col-lg-2 " style="text-align: center;">
                                                                <h6><b>'.$arrayItemResultados['intCantPropuestas'].'</b></h6>
                                                                </div>
                                                                <div class="col-lg-2 " style="text-align: center;">
                                                                    <h6><b>'.$arrayItemResultados['intCantOrdenes'].'</b></h6>
                                                                </div>
                                                                <div class="col-lg-2 " style="text-align: center;">
                                                                <h6><b>'.$arrayItemResultados['intCantOrdenesCrm'].'</b></h6>
                                                                </div>
                                                                <div class="col-lg-'.$intValorColumna.' " style="text-align: center;">
                                                                    <h6><b>'.$arrayItemResultados['strUsrVendedor'].'</b></h6>
                                                                </div>
                                                                '.$strBodyModalDesc.'
                                                                <div class="col-lg-2" style="text-align: center;">
                                                                 <h6>
                                                                  <b>$ '.number_format($arrayItemResultados['intTotalVentasMrc'], 2, '.', ',').'</b>
                                                                 </h6>
                                                                </div>
                                                                <div class="col-lg-2" style="text-align: center;">
                                                                 <h6>
                                                                  <b>$ '.number_format($arrayItemResultados['intTotalVentasNrc'], 2, '.', ',').'</b>
                                                                  </h6>
                                                                </div>
                                                            </div>';
                                    }
                                }
                            }
                        }
                        if($strTipo == "ORDENES_CANCELADAS" && ($strTipoPersonal === 'SUBGERENTE' || $strTipoPersonal === 'GERENTE_VENTAS'))
                        {
                            $arrayListaOrdenDowgrade  = $serviceComercial->getListaOrdenes($arrayParametrosGenerales);
                            if(!empty($arrayListaOrdenDowgrade) && is_array($arrayListaOrdenDowgrade))
                            {
                                $arrayDowgradeTotal = array();
                                $strCabDowModal    .= '<div class="row" style="margin-top:1px; padding: 10px 10px;">
                                                            <div class="col-lg-12 modal-header" style="text-align: center;">
                                                                <h4><b>&Oacute;rdenes Dowgrade</b></h4>
                                                            </div>
                                                       </div>
                                                       <div class="row" style="margin-top:5px; padding: 5px 5px;">
                                                           <div class="col-lg-4 modal-header" style="text-align: center;">
                                                               <h4><b>Cant. &Oacute;rd</b></h4>
                                                           </div>
                                                           <div class="col-lg-4 modal-header" style="text-align: center;">
                                                               <h4><b>Vendedor</b></h4>
                                                           </div>
                                                           <div class="col-lg-4 modal-header" style="text-align: center;">
                                                               <h4><b>Precio de Venta</b></h4>
                                                           </div>
                                                       </div>';
                                foreach($arrayListaOrdenDowgrade as $arrayItemListOrden)
                                {
                                    $arrayResDetOrdenDow = $serviceComercial->getDatosOrdenes($strFechaInicio,
                                                                                              $strFechaFin,
                                                                                              $arrayItemListOrden['ID_SERVICIO']);
                                    if(!empty($arrayResDetOrdenDow) && is_array($arrayResDetOrdenDow))
                                    {
                                        $intContDow      = 0;
                                        $floatPrecioDow  = 0;
                                        foreach($arrayResDetOrdenDow as $arrayItemDetOrdenDow)
                                        {
                                            $strObservacion  = $arrayItemDetOrdenDow['OBSERVACION'];
                                            $strObsIniDowAnt = substr($strObservacion,strpos($strObservacion,'Velocidad Down anterior:')+24);
                                            $strObsFinDowAnt = substr($strObsIniDowAnt,0,strpos($strObsIniDowAnt,'<br> Velocidad'));
                                            $strObsIniDowNue = substr($strObservacion,strpos($strObservacion,'Velocidad Down Nuevo:')+21);
                                            $strObsFinDowNue = substr($strObsIniDowNue,0,strpos($strObsIniDowNue,'<br>'));
                                            if( $strObsFinDowAnt < $strObsFinDowNue )
                                            {
                                                $intContDow ++;
                                            }
                                            $strObsIniPreAnt = substr($strObservacion,strpos($strObservacion,'Precio anterior: ')+17);
                                            $strObsFinPreAnt = substr($strObsIniPreAnt,0,strpos($strObsIniPreAnt,'<br> Precio Nuevo'));
                                            $strObsIniPreNue = substr($strObservacion,strpos($strObservacion,'Precio Nuevo   : ')+17);
                                            $strObsFinPreNue = substr($strObsIniPreNue,0,strpos($strObsIniPreNue,'<br>'));
                                            if( $strObsFinPreNue > $strObsFinPreAnt )
                                            {
                                                $floatPrecioDow = $floatPrecioDow+($strObsFinPreNue-$strObsFinPreAnt);
                                            }
                                        }
                                        $arrayVendedorDowgrade=array('USR_VENDEDOR' => $arrayItemListOrden['USR_VENDEDOR'],
                                                                     'CANT'         => $intContDow,
                                                                     'TOTAL'        => $floatPrecioDow );
                                        array_push($arrayDowgradeTotal,$arrayVendedorDowgrade);
                                    }
                                }

                                $arrayVendedor = $serviceComercial->getVendedor($arrayParametrosGenerales['intIdPersonEmpresaRol'],
                                                                                $arrayParametrosGenerales['strPrefijoEmpresa']);
                                if(!empty($arrayVendedor) && is_array($arrayVendedor) && 
                                    (!empty($arrayDowgradeTotal) && is_array($arrayDowgradeTotal)))
                                {
                                    sort($arrayDowgradeTotal);
                                    $strBodyModalDow .=$strCabDowModal;
                                    foreach( $arrayVendedor as $arrayItemFiltroVendedor )
                                    {
                                        $intContDow     = 0;
                                        $floatPrecioDow = 0;
                                        foreach( $arrayDowgradeTotal as $arrayItemDow )
                                        {
                                            if( $arrayItemDow['USR_VENDEDOR'] == $arrayItemFiltroVendedor['LOGIN'] )
                                            {
                                                $intContDow     = $intContDow+$arrayItemDow['CANT'];
                                                $floatPrecioDow = $floatPrecioDow+$arrayItemDow['TOTAL'];
                                            }
                                        }
                                        $strBodyModalDow .= '<div class="row" style="margin-top:5px; padding: 10px 30px;">
                                                                <div class="col-lg-4" style="text-align: center;">
                                                                    <h5><b>'.$intContDow.'</b></h5>
                                                                </div>
                                                                <div class="col-lg-4" style="text-align: center;">
                                                                    <h5><b>'.$arrayItemFiltroVendedor['LOGIN'].'</b></h5>
                                                                </div>
                                                                <div class="col-lg-4" style="text-align: center;">
                                                                    <h5><b>$'.number_format($floatPrecioDow, 2, '.', ',').'</b></h5>
                                                                </div>
                                                            </div>';
                                    }
                                }
                            }
                        }
                    }
                    if($boolSuccess)
                    {
                        $arrayResponse['boolSuccess']         = $boolSuccess;
                        $arrayResponse['strBodyModal']        = $strBodyModal.$strBodyModalDow;
                        $arrayResponse['strMensajeRespuesta'] = '';
                    }
                }// Fin Accion Consultar
                else
                {
                    $arrayParametrosGenerales['strTipoOrdenes']     = $strTipo;
                    $arrayParametrosGenerales['strOpcionSelect']    = 'DETALLE';
                    $arrayParametrosGenerales['strEmailUsrSession'] = $strUsrCreacion.'@telconet.ec';
                    $strValorFormaContacto                          = ""; 

                    $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonEmpresaRol);

                    if ( is_object($objInfoPersonaEmpresaRol) )
                    {
                        $objInfoPersona = $objInfoPersonaEmpresaRol->getPersonaId();

                        if ( is_object($objInfoPersona) )
                        {
                            $strValorFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                                 ->getValorFormaContactoPorCodigo($objInfoPersona,'MAIL');

                            if ( !is_null($strValorFormaContacto))
                            {
                                $arrayParametrosGenerales['strEmailUsrSession'] = strtolower($strValorFormaContacto);
                            }
                        }
                    }
                    if(!empty($strTipo) && $strTipo == "VENTAS_NO_CONCRETADAS")
                    {
                        $arrayParametrosGenerales['strFechaInicio'] = "01-Jan-".date("Y");
                    }
                    $arrayResultados = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                   ->getInformacionVentasTelcosCRM($arrayParametrosGenerales);

                    if ( isset($arrayResultados['strMensajeRespuesta']) && !empty($arrayResultados['strMensajeRespuesta']) )
                    {
                        $arrayResponse['boolSuccess']         = true;
                        $arrayResponse['strBodyModal']        = '';
                        $arrayResponse['strMensajeRespuesta'] = $arrayResultados['strMensajeRespuesta'];
                    }
                }// Fin Accion Exportar
            }//( !empty($strTipo) )
            else
            {
                throw new \Exception('No se ha encontrado el tipo con el cual se va a realizar la consulta respectiva.');
            }            
        }
        catch( \Exception $e )
        {
            $serviceUtil->insertError('TELCOS+',
                                      'SeguridadBundle.DefaultController.getDetalleVentasAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        
        $objJsonResponse->setData($arrayResponse);
        
        return $objJsonResponse;
    }


    /**
     * Documentación para el método 'getInformacionDestacadosAction'.
     *
     * Método que retorna la información de los productos o vendedores destacados.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 10-06-2017
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 12-06-2017 - Se obtiene la información de los vendores o productos destacados dependiendo del usuario en sessión
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 15-06-2017 - Se modifica la función para que retorne la información en formato HTML de los productos o vendedores destacados
     */
    public function getInformacionDestacadosAction()
    {
        $objJsonResponse       = new JsonResponse();
        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $strPrefijoEmpresa     = $objSession->get('prefijoEmpresa');
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $strUsrCreacion        = $objSession->get('user');
        $strIpCreacion         = $objRequest->getClientIp();
        $strCategoria          = $objRequest->request->get('strCategoria') ? $objRequest->request->get('strCategoria') : '';
        $strGrupo              = $objRequest->request->get('strGrupo') ? $objRequest->request->get('strGrupo') : '';
        $strSubgrupo           = $objRequest->request->get('strSubgrupo') ? $objRequest->request->get('strSubgrupo') : '';
        $strTipo               = $objRequest->request->get('strTipo') ? $objRequest->request->get('strTipo') : '';
        $strFechaInicio        = $objRequest->request->get('strFechaInicio') ? $objRequest->request->get('strFechaInicio') :  "";
        $strFechaFin           = $objRequest->request->get('strFechaFin') ? $objRequest->request->get('strFechaFin') : '';
        $strUserComercial      = $this->container->getParameter('user_comercial');
        $strPasswordComercial  = $this->container->getParameter('passwd_comercial');
        $strDatabaseDsn        = $this->container->getParameter('database_dsn');
        $serviceUtil           = $this->get('schema.Util');
        $serviceComercial      = $this->get('comercial.Comercial');  
        $emComercial           = $this->getDoctrine()->getManager('telconet');
        $strTipoPersonal       = "";
        $arrayResponse         = array('boolSuccess'     => false, 
                                       'strMensajeError' => 'No se ha encontrado información solicitada.',
                                       'strBodyModal'    => '<div class="row">'.
                                                            '  <div class="col-lg-2">&nbsp;</div>'.
                                                            '  <div class="col-lg-8">No se han encontrado '.$strTipo.' destacados.</div>'.
                                                            '  <div class="col-lg-2">&nbsp;</div>'.
                                                            '</div>');

        try
        {
            /**
             * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL' ASOCIADA A EL USUARIO LOGONEADO 
             */
            $arrayPersonalEmpresaRol       = array('intIdPersonEmpresaRol' => $intIdPersonEmpresaRol,
                                                   'strUsrCreacion'        => $strUsrCreacion,
                                                   'strIpCreacion'         => $strIpCreacion);
            $arrayResultadoCaracteristicas = $serviceComercial->getCaracteristicasPersonalEmpresaRol($arrayPersonalEmpresaRol);
            
            if( !empty($arrayResultadoCaracteristicas) )
            {
                if( isset($arrayResultadoCaracteristicas['strCargoPersonal']) && !empty($arrayResultadoCaracteristicas['strCargoPersonal']) )
                {
                    $strTipoPersonal = $arrayResultadoCaracteristicas['strCargoPersonal'];
                }//( isset($arrayResultadoCaracteristicas['strCargoPersonal']) && !empty($arrayResultadoCaracteristicas['strCargoPersonal']) )
            }//( !empty($arrayResultadoCaracteristicas) )

            if( !empty($strGrupo) )
            {
                if(strpos($strGrupo,'Trimestre'))
                {
                    $strGrupo = trim(str_replace('(Trimestre)','',$strGrupo));
                }
                elseif(strpos($strGrupo,'Mensual'))
                {
                    $strGrupo = trim(str_replace('(Mensual)','',$strGrupo));                                
                }
            }
            $arrayParametros = array('strPrefijoEmpresa'    => $strPrefijoEmpresa,
                                     'strFechaInicio'       => $strFechaInicio,
                                     'strFechaFin'          => $strFechaFin,
                                     'strUsrCreacion'       => $strUsrCreacion,
                                     'strIpCreacion'        => $strIpCreacion,
                                     'strCategoria'         => $strCategoria,
                                     'strGrupo'             => $strGrupo,
                                     'strSubgrupo'          => $strSubgrupo,
                                     'strTipoPersonal'      => $strTipoPersonal,
                                     'intIdPersonEmpresaRol'=> $intIdPersonEmpresaRol,
                                     'strDatabaseDsn'       => $strDatabaseDsn,
                                     'strUserComercial'     => $strUserComercial,
                                     'strPasswordComercial' => $strPasswordComercial,
                                     'intRownum'            => 1000);

            if( !empty($strTipo) )
            {
                $cursorDestacados  = null;
                $strLabelDestacado = "";

                if( $strTipo == "Vendedores" )
                {
                    $strLabelDestacado = 'VENDEDOR';
                    $cursorDestacados  = $emComercial->getRepository('schemaBundle:InfoServicio')->getListadoVendedoresDestacados($arrayParametros);
                }
                elseif( $strTipo == "Productos" )
                {
                    $strLabelDestacado = 'DESCRIPCION_PRODUCTO';
                    $cursorDestacados  = $emComercial->getRepository('schemaBundle:InfoServicio')->getListadoProductosDestacados($arrayParametros);
                }
                
                if( !empty($cursorDestacados) )
                {
                    $strBodyModal = null;

                    while( ($arrayResultadoCursor = oci_fetch_array($cursorDestacados, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                    {
                        $strDestacado = ( isset($arrayResultadoCursor[$strLabelDestacado]) && !empty($arrayResultadoCursor[$strLabelDestacado]) )
                                        ? ucwords(strtolower(htmlentities($arrayResultadoCursor[$strLabelDestacado]))) : '';
                        $floatValor   = ( isset($arrayResultadoCursor['VALOR_VENTA']) && !empty($arrayResultadoCursor['VALOR_VENTA']) )
                                        ? number_format($arrayResultadoCursor['VALOR_VENTA'], 2, '.', ',') : 0;

                        $strBodyModal .= '<div class="row" style="padding-bottom: 5px;">'.
                                            '<div class="col-lg-1">&nbsp;</div>'.
                                            '<div class="col-lg-5">'.
                                                '<span class="progress-description">'.$strDestacado.'</span> '.
                                            '</div>'.
                                            '<div class="col-lg-5">'.
                                                '<span class="info-box-number text-right">$ '.$floatValor.'</span>'.
                                            '</div>'.
                                            '<div class="col-lg-1">&nbsp;</div>'.
                                         '</div>';
                    }//while( ($arrayResultadoCursor = oci_fetch_array($cursorDestacados, OCI_ASSOC + OCI_RETURN_NULLS)) != false )

                    if( !empty($strBodyModal) )
                    {
                        $arrayResponse['boolSuccess']     = true;
                        $arrayResponse['strBodyModal']    = $strBodyModal;
                        $arrayResponse['strMensajeError'] = '';
                    }//( !empty($strBodyModal) )
                }//( !empty($cursorDestacados) )
            }
            else
            {
                throw new \Exception('No se ha enviado el tipo con el cual se requiere obtener la información destacada.');
            }
        }
        catch( \Exception $e )
        {
            $serviceUtil->insertError('TELCOS+',
                                      'SeguridadBundle.DefaultController.getInformacionDestacadosAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        
        $objJsonResponse->setData($arrayResponse);
        
        return $objJsonResponse;
    }


    /**
     * Documentación para el método 'getVentasPorVendedorAction'.
     *
     * Método que retorna la información de las ventas por vendedor
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 11-06-2017
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 12-06-2017 - Se modifica para que se puedan traer las ventas dependiendo del usuario en sessión
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 19-06-2017 - Se modifica la función para retornar lo vendido en el trimestre para las categorías 2 y 3 de los productos.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 23-10-2017 - Se añade validación 'PROVINCIAS_AGRUPADAS' para obtener la acumulación de las ventas por provincias, para desglosar
     *                           las ventas por asesor de provincias.
     */
    public function getVentasPorVendedorAction()
    {
        $objJsonResponse       = new JsonResponse();
        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $strPrefijoEmpresa     = $objSession->get('prefijoEmpresa');
        $strEmpresaCod         = $objSession->get('idEmpresa');
        $strUsrCreacion        = $objSession->get('user');
        $strIpCreacion         = $objRequest->getClientIp();
        $strCategoria          = $objRequest->request->get('strCategoria') ? $objRequest->request->get('strCategoria') : '';
        $strGrupo              = $objRequest->request->get('strGrupo') ? $objRequest->request->get('strGrupo') : '';
        $strSubgrupo           = $objRequest->request->get('strSubgrupo') ? $objRequest->request->get('strSubgrupo') : '';
        $strTipoVendedor       = $objRequest->request->get('strTipoVendedor') ? $objRequest->request->get('strTipoVendedor') : '';
        $strFechaInicio        = $objRequest->request->get('strFechaInicio') ? $objRequest->request->get('strFechaInicio') : "";
        $strFechaFin           = $objRequest->request->get('strFechaFin') ? $objRequest->request->get('strFechaFin') : '';
        $strUserComercial      = $this->container->getParameter('user_comercial');
        $strPasswordComercial  = $this->container->getParameter('passwd_comercial');
        $strDatabaseDsn        = $this->container->getParameter('database_dsn');
        $strTipoPersonal       = "";
        $serviceUtil           = $this->get('schema.Util');
        $serviceComercial      = $this->get('comercial.Comercial');
        $emComercial           = $this->getDoctrine()->getManager('telconet');
        $emGeneral             = $this->getDoctrine()->getManager('telconet_general');
        $arrayResponse         = array('boolSuccess'     => false, 
                                       'strMensajeError' => 'No se ha encontrado información solicitada.',
                                       'arrayVendedores' => array(),
                                       'arrayVendido'    => array(),
                                       'arrayMetas'      => array(),
                                       'arrayVentasCat'  => array());

        try
        {
            /**
             * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL' ASOCIADA A EL USUARIO LOGONEADO 
             */
            $arrayPersonalEmpresaRol       = array('intIdPersonEmpresaRol' => $intIdPersonEmpresaRol,
                                                   'strUsrCreacion'        => $strUsrCreacion,
                                                   'strIpCreacion'         => $strIpCreacion);
            $arrayResultadoCaracteristicas = $serviceComercial->getCaracteristicasPersonalEmpresaRol($arrayPersonalEmpresaRol);
            
            if( !empty($arrayResultadoCaracteristicas) )
            {
                if( isset($arrayResultadoCaracteristicas['strCargoPersonal']) && !empty($arrayResultadoCaracteristicas['strCargoPersonal']) )
                {
                    $strTipoPersonal = $arrayResultadoCaracteristicas['strCargoPersonal'];
                }//( isset($arrayResultadoCaracteristicas['strCargoPersonal']) && !empty($arrayResultadoCaracteristicas['strCargoPersonal']) )
            }//( !empty($arrayResultadoCaracteristicas) )


            $arrayParametros = array('strPrefijoEmpresa'    => $strPrefijoEmpresa,
                                     'strFechaInicio'       => $strFechaInicio,
                                     'strFechaFin'          => $strFechaFin,
                                     'strUsrCreacion'       => $strUsrCreacion,
                                     'strIpCreacion'        => $strIpCreacion,
                                     'strCategoria'         => $strCategoria,
                                     'strGrupo'             => $strGrupo,
                                     'strSubgrupo'          => $strSubgrupo,
                                     'strTipoPersonal'      => $strTipoPersonal,
                                     'intIdPersonEmpresaRol'=> $intIdPersonEmpresaRol,
                                     'strDatabaseDsn'       => $strDatabaseDsn,
                                     'strUserComercial'     => $strUserComercial,
                                     'strPasswordComercial' => $strPasswordComercial,
                                     'strTipoVendedor'      => $strTipoVendedor);
            
            if( !empty($strTipoVendedor) )
            {
                if( $strTipoVendedor == "PROVINCIAS" || $strTipoVendedor == "PROVINCIAS_AGRUPADAS" )
                {
                    $cursorVendedores = $emComercial->getRepository('schemaBundle:InfoServicio')->getVentasPorVendedor($arrayParametros);
                    
                    if( !empty($cursorVendedores) )
                    {
                        $arrayVendedores = array();
                        $arrayMetas      = array();
                        $arrayVendido    = array();
                        
                        while( ($arrayResultadoCursor = oci_fetch_array($cursorVendedores, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                        {
                            $strCanton   = ( isset($arrayResultadoCursor['CANTON']) && !empty($arrayResultadoCursor['CANTON']) )
                                           ? ucwords(strtolower($arrayResultadoCursor['CANTON'])) : '';
                            $strVendedor = ( isset($arrayResultadoCursor['VENDEDOR']) && !empty($arrayResultadoCursor['VENDEDOR']) )
                                           ? ucwords(strtolower($arrayResultadoCursor['VENDEDOR'])) : '';
                            $floatVenta  = ( isset($arrayResultadoCursor['TOTAL_VENTA']) && !empty($arrayResultadoCursor['TOTAL_VENTA']) )
                                           ? floatval($arrayResultadoCursor['TOTAL_VENTA']) : 0;
                            $floatMeta   = 0;

                            if ( $strTipoVendedor == "PROVINCIAS_AGRUPADAS" )
                            {
                                $strCanton = $strVendedor;
                            }

                            //CONSULTO LA META POR CADA VENDEDOR OBTENIDO
                            $arrayParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                           ->getOne("DASHBOARD_COMERCIAL",
                                                                    "COMERCIAL", 
                                                                    "REPORTES", 
                                                                    "METAS POR TIPO VENDEDOR", 
                                                                    strtoupper($strCanton), 
                                                                    "", 
                                                                    "", 
                                                                    "",
                                                                    "",
                                                                    $strEmpresaCod );

                            if( isset($arrayParametroDet["valor3"]) && !empty($arrayParametroDet["valor3"]) )
                            {
                                $floatMeta = $arrayParametroDet["valor3"];
                            }//( isset($arrayParametroDet["valor3"]) && !empty($arrayParametroDet["valor3"]) )
                            
                            if( !empty($strVendedor) )
                            {
                                $arrayVendedores[] = $strVendedor;
                                $arrayVendido[]    = $floatVenta;
                                $arrayMetas[]      = floatval($floatMeta);
                            }//( !empty($strVendedor) && floatval($floatVenta) > 0 && floatval($floatMeta) > 0 )
                        }//while( ($arrayResultadoCursor = oci_fetch_array($cursorVendedores, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                        
                        if( !empty($arrayVendedores) && !empty($arrayVendido) && !empty($arrayMetas) )
                        {
                            $arrayResponse['arrayVendedores'] = $arrayVendedores;
                            $arrayResponse['arrayVendido']    = $arrayVendido;
                            $arrayResponse['arrayMetas']      = $arrayMetas;
                            $arrayResponse['boolSuccess']     = true;
                            $arrayResponse['strMensajeError'] = '';
                        }//( !empty($arrayVendedores) && !empty($arrayVendido) && !empty($arrayMetas) )
                    }//( !empty($cursorVendedores) )
                }//( $strTipoVendedor == "PROVINCIAS" )
                else
                {  
                    //CONSULTO LA META POR CADA CATEGORIA OBTENIDA
                    $arrayMetasPorCategorias = array('CATEGORIA_1' => 0, 'CATEGORIA_2' => 0, 'CATEGORIA_3' => 0);
                    $arrayMetasParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                         ->get("DASHBOARD_COMERCIAL",
                                                               "COMERCIAL", 
                                                               "REPORTES", 
                                                               "METAS POR TIPO VENDEDOR", 
                                                               $strTipoVendedor, 
                                                               "", 
                                                               "", 
                                                               "",
                                                               "",
                                                               $strEmpresaCod );

                    if( !empty($arrayMetasParametrosDet) )
                    {
                        foreach($arrayMetasParametrosDet as $arrayMetaParametroDet)
                        {
                            if( isset($arrayMetaParametroDet["valor3"]) && !empty($arrayMetaParametroDet["valor3"])
                                && isset($arrayMetaParametroDet["valor2"]) && !empty($arrayMetaParametroDet["valor2"]) )
                            {
                                $strCategoriaMeta                           = str_replace(" ", "_", $arrayMetaParametroDet["valor2"]);
                                $arrayMetasPorCategorias[$strCategoriaMeta] = floatval($arrayMetaParametroDet["valor3"]);
                            }//( isset($arrayParametroDet["valor3"]) && !empty($arrayParametroDet["valor3"])...
                        }//foreach($arrayMetasParametrosDet as $arrayMetaParametroDet)
                    }//( !empty($arrayMetasParametrosDet) )

                    
                    //CONSULTO LAS CATEGORIAS EXISTENTES
                    $arrayParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                   ->get("DASHBOARD_COMERCIAL",
                                                         "COMERCIAL", 
                                                         "REPORTES", 
                                                         "", 
                                                         "CATEGORIAS_PRODUCTOS", 
                                                         "", 
                                                         "", 
                                                         "",
                                                         "",
                                                         $strEmpresaCod );

                    foreach( $arrayParametroDet as $arrayCategoria )
                    {
                        if( isset($arrayCategoria['descripcion']) && !empty($arrayCategoria['descripcion']) )
                        {
                            if( isset($arrayCategoria['valor2']) &&  $arrayCategoria['valor2'] == 'TRIMESTRE' )
                            {
                                $arrayParametrosTrimestre = array('strFecha'       => $strFechaInicio,
                                                                  'strUsrCreacion' => $strUsrCreacion,
                                                                  'strIpCreacion'  => $strIpCreacion);
                                $arrayResultadoTrimestre  = $serviceUtil->getSegmentacionFecha($arrayParametrosTrimestre);
                                
                                if( isset($arrayResultadoTrimestre['intTrimestre']) && !empty($arrayResultadoTrimestre['intTrimestre'])
                                    && isset($arrayResultadoTrimestre['intAnioActual']) && !empty($arrayResultadoTrimestre['intAnioActual']) )
                                {
                                    $strTrimestre = $arrayResultadoTrimestre['intTrimestre']." ".$arrayCategoria['valor2'];
                                    
                                    //CONSULTO EL MES DE INICIO DEL TRIMESTRE ENCONTRADO
                                    $arrayParametroDetTrimestre = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                            ->getOne("DASHBOARD_COMERCIAL",
                                                                                     "COMERCIAL", 
                                                                                     "REPORTES", 
                                                                                     "", 
                                                                                     "INICIO_TRIMESTRES", 
                                                                                     $strTrimestre, 
                                                                                     "", 
                                                                                     "",
                                                                                     "",
                                                                                     $strEmpresaCod );
                                    
                                    if( isset($arrayParametroDetTrimestre['valor3']) && !empty($arrayParametroDetTrimestre['valor3']) )
                                    {
                                        $arrayParametros['strFechaInicio'] = '01-'.$arrayParametroDetTrimestre['valor3'].'-'.
                                                                             $arrayResultadoTrimestre['intAnioActual'];
                                        $dateFechaFin                      = new \DateTime($arrayParametros['strFechaInicio']);
                                        $dateFechaFin                      = $dateFechaFin->add(new \DateInterval('P3M'));
                                        $arrayParametros['strFechaFin']    = $dateFechaFin->format('d-M-Y');
                                    }//( isset($arrayParametroDetTrimestre['valor3']) && !empty($arrayParametroDetTrimestre['valor3']) )
                                }//( isset($arrayResultadoTrimestre['intTrimestre']) && !empty($arrayResultadoTrimestre['intTrimestre']) )
                            }
                            else
                            {
                                $arrayParametros['strFechaInicio'] = $strFechaInicio;
                                $arrayParametros['strFechaFin']    = $strFechaFin;
                            }//( isset($arrayCategoria['valor2']) &&  $arrayCategoria['valor2'] == 'TRIMESTRE' )

                            $strCategoriaProducto = $arrayCategoria['descripcion'];
                            $floatMeta            = 0;
                            
                            $arrayParametros['strCategoria'] = $strCategoriaProducto;
                            $cursorVendedores                = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                           ->getVentasPorVendedor($arrayParametros);

                            if( !empty($cursorVendedores) )
                            {
                                while( ($arrayResultadoCursor = oci_fetch_array($cursorVendedores, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                                {
                                    $strVendedor = ( isset($arrayResultadoCursor['VENDEDOR']) && !empty($arrayResultadoCursor['VENDEDOR']) )
                                                   ? ucwords(strtolower($arrayResultadoCursor['VENDEDOR'])) : '';
                                    
                                    if( !empty($strVendedor) )
                                    {
                                        if( isset($arrayResponse['arrayVentasCat']) )
                                        {
                                            $arrayVentasCat = $arrayResponse['arrayVentasCat'];
                                            
                                            if( !isset($arrayVentasCat[$strVendedor]) )
                                            {
                                                $arrayVendedorCat = $arrayVentasCat[$strVendedor];
                                                
                                                if( !isset($arrayVendedorCat['CATEGORIA_1']) )
                                                {
                                                    $arrayResponse['arrayVentasCat'][$strVendedor]
                                                                  ['CATEGORIA_1']['floatVendido'] = 0;
                                                    $arrayResponse['arrayVentasCat'][$strVendedor]
                                                                  ['CATEGORIA_1']['floatMeta']    = $arrayMetasPorCategorias['CATEGORIA_1'];
                                                }//( !isset($arrayVendedorCat['CATEGORIA_1']) )
                                                
                                                if( !isset($arrayVendedorCat['CATEGORIA_2']) )
                                                {
                                                    $arrayResponse['arrayVentasCat'][$strVendedor]
                                                                  ['CATEGORIA_2']['floatVendido'] = 0;
                                                    $arrayResponse['arrayVentasCat'][$strVendedor]
                                                                  ['CATEGORIA_2']['floatMeta']    = $arrayMetasPorCategorias['CATEGORIA_2'];
                                                }//( !isset($arrayVendedorCat['CATEGORIA_1']) )
                                                
                                                if( !isset($arrayVendedorCat['CATEGORIA_3']) )
                                                {
                                                    $arrayResponse['arrayVentasCat'][$strVendedor]
                                                                  ['CATEGORIA_3']['floatVendido'] = 0;
                                                    $arrayResponse['arrayVentasCat'][$strVendedor]
                                                                  ['CATEGORIA_3']['floatMeta']    = $arrayMetasPorCategorias['CATEGORIA_3'];
                                                }//( !isset($arrayVendedorCat['CATEGORIA_1']) )
                                            }//( !isset($arrayVentasCat[$strVendedor]) )
                                        }//( isset($arrayResponse['arrayVentasCat']) )
                                        else
                                        {
                                            $arrayResponse['arrayVentasCat'][$strVendedor]['CATEGORIA_1']['floatVendido'] = 0;
                                            $arrayResponse['arrayVentasCat'][$strVendedor]['CATEGORIA_1']['floatMeta']    = 0;
                                            $arrayResponse['arrayVentasCat'][$strVendedor]['CATEGORIA_2']['floatVendido'] = 0;
                                            $arrayResponse['arrayVentasCat'][$strVendedor]['CATEGORIA_2']['floatMeta']    = 0;
                                            $arrayResponse['arrayVentasCat'][$strVendedor]['CATEGORIA_3']['floatVendido'] = 0;
                                            $arrayResponse['arrayVentasCat'][$strVendedor]['CATEGORIA_3']['floatMeta']    = 0;
                                        }

                                        $floatVenta  = ( isset($arrayResultadoCursor['TOTAL_VENTA']) && !empty($arrayResultadoCursor['TOTAL_VENTA']) )
                                                       ? floatval($arrayResultadoCursor['TOTAL_VENTA']) : 0;

                                        if( !empty($strVendedor) && floatval($floatVenta) > 0 )
                                        {
                                            $strCategoriaProducto = str_replace(' ', '_', $strCategoriaProducto);

                                            $arrayResponse['arrayVentasCat'][$strVendedor][$strCategoriaProducto]['floatVendido'] = $floatVenta;

                                            $arrayResponse['boolSuccess']     = true;
                                            $arrayResponse['strMensajeError'] = '';
                                        }//( !empty($strVendedor) && floatval($floatVenta) > 0 )
                                    }//( !empty($strVendedor) )
                                }//while( ($arrayResultadoCursor = oci_fetch_array($cursorVendedores, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                            }//( !empty($cursorVendedores) )
                        }//( isset($arrayCategoria['descripcion']) && !empty($arrayCategoria['descripcion']) )
                    }//foreach( $arrayParametroDet as $arrayCategoria )
                }////( $strTipoVendedor == "PYMES" || $strTipoVendedor == "KAM" || $strTipoVendedor == "CORPORATIVO" )
            }
            else
            {
                throw new \Exception('No se ha enviado el tipo con el cual se requiere obtener la información.');
            }
        }
        catch( \Exception $e )
        {
            $serviceUtil->insertError('TELCOS+',
                                      'SeguridadBundle.DefaultController.getVentasPorVendedorAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        
        $objJsonResponse->setData($arrayResponse);
        
        return $objJsonResponse;
    }
    /**
     * Documentación para el método 'getDetalleFacturacionAsesorAction'.
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.4 11-12-2021 - Se modifica para mostrar los datos de internet/datos y business solutions en la facturación Mrc.
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.3 23-08-2018 - Se agrega estilos css para visualizar todos los clientes por cada vendedor
     * 
     * Actualización: Se agrega retorno de data para exportar a excel el detalle de facturación MRC o NRC
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 24-04-2018
     * 
     * Método que retorna la información correspondiente de facturación Mrc y Nrc del asesor.
     *
     * @return Response
     *
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 26-03-2018
     * 
     */
    public function getDetalleFacturacionAsesorAction()
    {
        $objJsonResponse         = new JsonResponse();
        $objRequest              = $this->get('request');
        $objSession              = $objRequest->getSession();
        $strPrefijoEmpresa       = $objSession->get('prefijoEmpresa');
        $strUsrCreacion          = $objSession->get('user');
        $intIdPersonEmpresaRol   = $objSession->get('idPersonaEmpresaRol');
        $strIpCreacion           = $objRequest->getClientIp();
        $strFechaInicio          = $objRequest->request->get('strFechaInicio') ? $objRequest->request->get('strFechaInicio') : "";
        $strFechaFin             = $objRequest->request->get('strFechaFin') ? $objRequest->request->get('strFechaFin') : '';
        $strTipo                 = $objRequest->request->get('strTipo') ? $objRequest->request->get('strTipo') : '';
        $strTipoConsulta         = $objRequest->request->get('strTipoConsulta') ? $objRequest->request->get('strTipoConsulta') : '';
        $serviceComercial        = $this->get('comercial.Comercial');
        $strUserBiFinanciero     = $this->container->getParameter('user_bifinanciero');
        $strPasswordBiFinanciero = $this->container->getParameter('passwd_bifinanciero');
        $strDatabaseDsn          = $this->container->getParameter('database_dsn');
        $serviceUtil             = $this->get('schema.Util');
        $strTipoPersonal         = "";
        $arrayResponse           = array('boolSuccess'     => false, 
                                         'strMensajeError' => 'No se ha encontrado información de facturación solicitada');
        try
        {
            /**
             * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL' ASOCIADA A EL USUARIO LOGONEADO 
             */                
            $arrayPersonalEmpresaRol       = array('intIdPersonEmpresaRol' => $intIdPersonEmpresaRol,
                                                   'strUsrCreacion'        => $strUsrCreacion,
                                                   'strIpCreacion'         => $strIpCreacion);
            $arrayResultadoCaracteristicas = $serviceComercial->getCaracteristicasPersonalEmpresaRol($arrayPersonalEmpresaRol);

            if( !empty($arrayResultadoCaracteristicas) )
            {
                if( isset($arrayResultadoCaracteristicas['strCargoPersonal']) && !empty($arrayResultadoCaracteristicas['strCargoPersonal']) )
                {
                    $strTipoPersonal = $arrayResultadoCaracteristicas['strCargoPersonal'];
                }//( isset($arrayResultadoCaracteristicas['strCargoPersonal']) && !empty($arrayResultadoCaracteristicas['strCargoPersonal']) )
            }//( !empty($arrayResultadoCaracteristicas) )
            $arrayParametrosServiceComercial = array('strPrefijoEmpresa'       => $strPrefijoEmpresa,
                                                     'strFechaInicio'          => $strFechaInicio,
                                                     'strFechaFin'             => $strFechaFin,
                                                     'strUsrCreacion'          => $strUsrCreacion,
                                                     'strIpCreacion'           => $strIpCreacion,
                                                     'strDatabaseDsn'          => $strDatabaseDsn,
                                                     'strTipo'                 => $strTipo,
                                                     'strTipoConsulta'         => $strTipoConsulta,
                                                     'strUserBiFinanciero'     => $strUserBiFinanciero,
                                                     'strPasswordBiFinanciero' => $strPasswordBiFinanciero,
                                                     'strTipoPersonal'         => $strTipoPersonal,
                                                     'intIdPersonEmpresaRol'   => $intIdPersonEmpresaRol);            
            $arrayResultadoService = $serviceComercial->getDetalleFacturacionAsesor($arrayParametrosServiceComercial);            
            $strClaseCliente  = 'col-lg-3';
            $strClaseProducto = 'col-lg-4';
            //Se valida el tipo de la consulta
            if( $strTipoConsulta =='DETALLADO' || $strTipoConsulta =='DETALLADO_TRIMESTRAL')
            {
                if( $strTipoPersonal === 'SUBGERENTE' || $strTipoPersonal === 'GERENTE_VENTAS' )
                {
                    if( $strTipo === 'ORDENES_MRC' )
                    {
                        $arrayResultadoServiceOrden = $serviceComercial->getOrdenesNuevas($arrayParametrosServiceComercial);
                        
                        $strBodyModal  = '<div class="row" style="margin-top:5px; padding: 10px 10px;">
                                            <div class="col-lg-12 modal-header" style="text-align: center;">
                                                <h5><b>&Oacute;rdenes Nuevas</b></h5>
                                            </div>                            
                                          </div>';
                        $strCabModal   = '<div class="row" style="margin-top:5px; padding: 10px 30px;">
                                            <div class="col-lg-4  modal-header" style="text-align: center;">
                                                <h5><b>Cant. &Oacute;rd</b></h5>
                                            </div>
                                            <div class="col-lg-4  modal-header" style="text-align: center;">
                                                <h5><b>Vendedor</b></h5>
                                            </div>
                                            <div class="col-lg-4 modal-header" style="text-align: center;">
                                                <h5><b>Precio de Venta</b></h5>
                                            </div>
                                          </div>';
                        $strBodyModal .= $strCabModal;
                        
                        if( !empty($arrayResultadoServiceOrden) )
                        {
                            foreach($arrayResultadoServiceOrden as $arrayItemOrdenesNuevas)
                            {
                                $strBodyModal .= '<div class="row" style="margin-top:5px; padding: 10px 10px;">
                                                    <div class="col-lg-4" style="text-align: center;">
                                                        <h5><b>'.$arrayItemOrdenesNuevas['TOTAL'].'</b></h5>
                                                    </div>
                                                    <div class="col-lg-4" style="text-align: center;">
                                                        <h5><b>'.$arrayItemOrdenesNuevas['VENDEDOR'].'</b></h5>
                                                    </div>
                                                    <div class="col-lg-4" style="text-align: center;">
                                                        <h5><b>$'.number_format($arrayItemOrdenesNuevas['SUMATOTAL'], 2, '.', ',').'</b></h5>
                                                    </div>
                                                  </div>';
                            }    
                        }
                        
                        $strBodyModal .= '<div class="row" style="margin-top:1px; padding: 10px 10px;">
                                            <div class="col-lg-12 modal-header" style="text-align: center;">
                                                <h5><b>&Oacute;rdenes Upgrade</b></h5>
                                            </div>                            
                                          </div>';
                        $strBodyModal .= $strCabModal;
                        
                        $arrayListaOrdenUp = $serviceComercial->getListaOrdenes($arrayParametrosServiceComercial);
                        if( !empty($arrayListaOrdenUp) )
                        {
                            $arrayUpgradeTotal=array();
                            
                            foreach( $arrayListaOrdenUp as $arrayItemListOrden )
                            {
                                $arrayDetOrdenUp = $serviceComercial->getDatosOrdenes($strFechaInicio,$strFechaFin,$arrayItemListOrden['ID_SERVICIO']);
                                
                                foreach( $arrayDetOrdenUp as $arrayItemDetOrdenUp )
                                {
                                    $intContUp      = 0;
                                    $floatPrecioUp  = 0;
                                    $strObservacion = $arrayItemDetOrdenUp['OBSERVACION'];                                    
                                    $strObsIniUpAnt = substr($strObservacion,strpos($strObservacion,'Velocidad Up anterior  :')+24);                        
                                    $strObsFinUpAnt = substr($strObsIniUpAnt,0,strpos($strObsIniUpAnt,'<br> Velocidad'));

                                    $strObsIniUpNue = substr($strObservacion,strpos($strObservacion,'Velocidad Up Nuevo  :')+21);                        
                                    $strObsFinUpNue = substr($strObsIniUpNue,0,strpos($strObsIniUpNue,'<br> Velocidad'));

                                    if( $strObsFinUpAnt < $strObsFinUpNue )
                                    {
                                        $intContUp ++;
                                    }
                                    
                                    $strObsIniPreAnt = substr($strObservacion,strpos($strObservacion,'Precio anterior: ')+17);
                                    $strObsFinPreAnt = substr($strObsIniPreAnt,0,strpos($strObsIniPreAnt,'<br> Precio Nuevo'));
                                    
                                    $strObsIniPreNue = substr($strObservacion,strpos($strObservacion,'Precio Nuevo   : ')+17);
                                    $strObsFinPreNue = substr($strObsIniPreNue,0,strpos($strObsIniPreNue,'<br>'));
                                    
                                    if( $strObsFinPreNue > $strObsFinPreAnt )
                                    {
                                        $floatPrecioUp = $floatPrecioUp+($strObsFinPreNue-$strObsFinPreAnt);
                                    }
                                }
                                $arrayVendedorUpgrade=array('USR_VENDEDOR' => $arrayItemListOrden['USR_VENDEDOR'],
                                                            'CANT'   => $intContUp,
                                                            'TOTAL'  => $floatPrecioUp );
                                array_push($arrayUpgradeTotal,$arrayVendedorUpgrade);                                
                            }
                            sort($arrayUpgradeTotal);
                            $arrayVendedor = $serviceComercial->getVendedor($arrayParametrosServiceComercial['intIdPersonEmpresaRol'],$arrayParametrosServiceComercial['strPrefijoEmpresa']);
                            foreach( $arrayVendedor as $arrayItemVendedor )
                            {
                                $intContUp     = 0;
                                $floatPrecioUp = 0;                                
                                foreach( $arrayUpgradeTotal as $arrayItemUp )
                                {
                                    if( $arrayItemUp['USR_VENDEDOR'] == $arrayItemVendedor['LOGIN'] )
                                    {
                                        $intContUp     = $intContUp+$arrayItemUp['CANT'];
                                        $floatPrecioUp = $floatPrecioUp+$arrayItemUp['TOTAL'];
                                    }
                                }
                                $strBodyModal .= '<div class="row" style="margin-top:5px; padding: 10px 10px;">
                                                            <div class="col-lg-4" style="text-align: center;">
                                                                <h5><b>'.$intContUp.'</b></h5>
                                                            </div>
                                                            <div class="col-lg-4" style="text-align: center;">
                                                                <h5><b>'.$arrayItemVendedor['LOGIN'].'</b></h5>
                                                            </div>
                                                            <div class="col-lg-4" style="text-align: center;">
                                                                <h5><b>$'.number_format($floatPrecioUp, 2, '.', ',').'</b></h5>
                                                            </div>
                                                          </div>';
                            }
                        }
                        $arrayResponse['boolSuccess']     = true;
                        $arrayResponse['strBodyModal']    = $strBodyModal;
                        $arrayResponse['strMensajeError'] = '';

                    }//($strTipo === 'ORDENES_MRC')
                    else
                    {
                        $strBodyModal = '<div class="row" style="margin-top:5px; padding: 5px 5px;">
                                            <div class="col-xs-0-5  modal-header" style="text-align: justify;">
                                                <h5><b>#</b></h5>
                                            </div>
                                            <div class="col-lg-4  modal-header" style="text-align: center;">
                                                <h5><b>Vendedor</b></h5>
                                            </div>
                                            <div class="col-lg-3  modal-header" style="text-align: center;">
                                                <h5><b>Facturación</b></h5>
                                            </div>
                                            <div class="col-lg-2  modal-header" style="text-align: center;">
                                                <h5><b>Notas de crédito</b></h5>
                                            </div>
                                            <div class="col-lg-2.5  modal-header" style="text-align: center;">
                                                <h5><b>Total</b></h5>
                                            </div>
                                         </div>';
                        if( !empty($arrayResultadoService) )
                        {
                            $arrayFilter        = array();                            
                            foreach( $arrayResultadoService as $arrayItem_vendedor )
                            {
                                array_push($arrayFilter,$arrayItem_vendedor['strUsrVendedor']);                            
                            }
                            $arrayVendedor = array_unique($arrayFilter);   
                            sort($arrayVendedor);
                            $intContadorVend    = 0;
                            $intContadorRes     = 0;
                            $floatAcumFac       = 0;
                            $floatAcumNc        = 0;
                            $floatAcumFacID     = 0;
                            $floatAcumNcID      = 0;
                            $floatAcumFacBS     = 0;
                            $floatAcumNcBS      = 0;
                            $arrayVendedorTotal = array();

                            foreach( $arrayVendedor as $arrayItemFiltroVendedor )
                            {
                                $floatValorFacVend = 0;
                                $floatValorNcVend  = 0;

                                foreach( $arrayResultadoService as $arrayItem )
                                {                               
                                    if( $arrayItem['strUsrVendedor'] == $arrayItemFiltroVendedor )
                                    {                                       
                                        if ( $strTipo === 'MRC' )
                                        {                                        
                                            $floatValorFacVend += $arrayItem['floatFacMrc'];
                                            $floatValorNcVend  += $arrayItem['floatNcMrc'];
                                        }
                                        elseif( $strTipo === 'NRC' )
                                        {
                                            $floatValorFacVend += $arrayItem['floatFacNrc'];
                                            $floatValorNcVend  += $arrayItem['floatNcNrc'];
                                        }                                                                        
                                    }
                                }

                                $arrayVendedorValor=array('strUsrVendedor' => $arrayItemFiltroVendedor,
                                                            'floatFacMrc'  => $floatValorFacVend,
                                                            'floatFacNrc'  => $floatValorNcVend,
                                                            'total'        => $floatValorFacVend+$floatValorNcVend );

                                array_push($arrayVendedorTotal,$arrayVendedorValor);
                            }

                            sort($arrayVendedorTotal);

                            foreach($arrayVendedorTotal as $arrayItemFiltroVendedor)
                            {
                                $intContadorVend++;
                                $strBodyModal .= '<ul>
                                                    <li>
                                                        <div class="row" style="margin-top:5px; padding: 10px 10px;">
                                                            <div class="col-xs-0-5" style="text-align: center;"><h5><b>'.$intContadorVend.'</b></h5></div>
                                                            <div class="col-lg-4" style="text-align: center;"><h5><b>'.$arrayItemFiltroVendedor['strUsrVendedor'].'</b></h5></div>
                                                            <div class="col-lg-3" style="text-align: center;"><h5><b>$'.number_format($arrayItemFiltroVendedor['floatFacMrc'], 2, '.', ',').'</b></h5></div>
                                                            <div class="col-lg-2" style="text-align: center;"><h5><b>$'.number_format($arrayItemFiltroVendedor['floatFacNrc'], 2, '.', ',').'</b></h5></div>
                                                            <div class="col-lg-2.5" style="text-align: center;"><h5><b>$'.number_format($arrayItemFiltroVendedor['total'], 2, '.', ',').'</b></h5></div>
                                                        </div>';
                                $strBodyModal .= '<ul><li>
                                                    <div class="row" style="margin-top:5px; padding: 10px 15px;">
                                                        <div class="col-xs-0-5  modal-header" style="text-align: center;">
                                                            <h5><b>#</b></h5>
                                                        </div>
                                                        <div class="col-lg-2  modal-header" style="text-align: center;">
                                                            <h5><b>Asesor</b></h5>
                                                        </div>                                                        
                                                        <div class="col-lg-2 modal-header" style="text-align: center;">
                                                            <h5><b>Cliente</b></h5>
                                                        </div>
                                                        <div class="col-lg-2  modal-header" style="text-align: center;">
                                                            <h5><b>Login</b></h5>
                                                        </div>
                                                        <div class="col-lg-3 modal-header" style="text-align: center;">
                                                            <h5><b>Producto</b></h5>
                                                        </div>
                                                        <div class="col-lg-2  modal-header" style="text-align: center;">
                                                            <h5><b>Fact</b></h5>
                                                        </div>
                                                        <div class="col-xs-0-5  modal-header" style="text-align: center;">
                                                            <h5><b>NC</b></h5>
                                                        </div>
                                                    </div>
                                                  <li>';  

                                $intContadorRes=0;
                                sort($arrayResultadoService);
                                foreach( $arrayResultadoService as $arrayItem )
                                {                                
                                    if( $arrayItem['strUsrVendedor'] == $arrayItemFiltroVendedor['strUsrVendedor'] )
                                    {
                                        $intContadorRes++;
                                        $floatValorFac   = 0;
                                        $floatValorNc    = 0;
                                        $floatValorFacID = 0;
                                        $floatValorNcID  = 0;
                                        $floatValorFacBS = 0;
                                        $floatValorNcBS  = 0;
                                        $strBodyModal .= '<div class="row" style="margin-top:5px; padding: 10px 10px;">
                                                            <div class="col-xs-0-5" style="text-align: center;">
                                                                <h6><b>'.$intContadorRes.'</b></h6>
                                                            </div>
                                                            <div class="col-lg-2" style="text-align: center;">
                                                                <h6>'.$arrayItem['strUsrVendedor'].'</h6>
                                                            </div>
                                                            <div class="col-lg-2" style="text-align: center;">
                                                                <h6>'.$arrayItem['strCliente'].'</h6>
                                                            </div>
                                                            <div class="col-lg-2" style="text-align: center;">
                                                                <h6>'.$arrayItem['strLogin'].'</h6>
                                                         </div>';
                                        if (isset($arrayItem['strObservacion']) && !empty($arrayItem['strObservacion'])) 
                                        {
                                            $strProducto = $arrayItem['strObservacion'];
                                        }
                                        else
                                        {
                                            $strProducto = $arrayItem['strProducto'];
                                        }
                                        $strBodyModal .= '<div class="col-lg-3" style="text-align: center;">
                                                                <h5>'.$strProducto.'</h5>
                                                          </div>';
                                        if ( $strTipo === 'MRC' )
                                        {
                                            $floatValorFac   = $arrayItem['floatFacMrc'];
                                            $floatValorNc    = $arrayItem['floatNcMrc'];
                                            $floatValorFacID = $arrayItem['floatFacMrcID'];
                                            $floatValorNcID  = $arrayItem['floatNcMrcID'];
                                            $floatValorFacBS = $arrayItem['floatFacMrcBS'];
                                            $floatValorNcBS  = $arrayItem['floatNcMrcBS'];
                                        }
                                        elseif( $strTipo === 'NRC' )
                                        {
                                            $floatValorFac = $arrayItem['floatFacNrc'];
                                            $floatValorNc  = $arrayItem['floatNcNrc'];
                                        }
                                        $strBodyModal .=    '<div class="col-lg-2" style="text-align: center;">
                                                                <h5>$'.number_format($floatValorFac, 2, '.', ',').'</h5>
                                                            </div>
                                                            <div class="col-xs-0-5" style="text-align: center;">
                                                                <h5>$'.number_format($floatValorNc, 2, '.', ',').'</h5>
                                                            </div>
                                                         </div>';
                                        $floatAcumFac   += $floatValorFac;
                                        $floatAcumNc    += $floatValorNc;
                                        $floatAcumFacID += $floatValorFacID;
                                        $floatAcumNcID  += $floatValorNcID;
                                        $floatAcumFacBS += $floatValorFacBS;
                                        $floatAcumNcBS  += $floatValorNcBS;
                                    }
                                }
                                $strBodyModal.='    </li>
                                                   </li>
                                                  </ul>
                                                </li>
                                            </ul>';
                            }
                            $strBodyModal .= '<style type="text/css">
                                                li ul{display: none;}
                                                li:hover ul{display:block;}
                                              </style>';
                            $strCabModal .= '<div class="row" style="margin-top:5px; padding: 5px 5px;">
                                                <div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#"  
                                                    style="width:120px">
                                                                    Facturación:
                                                    </button>
                                                </div>';
                            $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                                $'.number_format($floatAcumFac, 2, '.', ',').'
                                                </button>
                                                </div>';
                            $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#" 
                                                    style="width:150px">
                                                                    Notas de crédito:
                                                    </button>
                                                </div>';
                            $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                                    $'.number_format($floatAcumNc, 2, '.', ',').'
                                                    </button>
                                                </div>';
                            $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#" 
                                                    style="width:80px">
                                                                    Total:
                                                    </button>
                                                </div>';
                            $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                                   $'.number_format(($floatAcumFac+$floatAcumNc), 2, '.', ',').'
                                                    </button>
                                                </div>';
                            if ($strTipo === 'MRC' )
                            {
                                $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                    !important;">
                                                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#">
                                                                        Facturación I/D:
                                                        </button>
                                                    </div>';
                                $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                    !important;">
                                                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                                    $'.number_format($floatAcumFacID, 2, '.', ',').'
                                                    </button>
                                                    </div>';
                                $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                    !important;">
                                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#">
                                                                        Notas de crédito I/D:
                                                        </button>
                                                    </div>';
                                $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                    !important;">
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                                        $'.number_format($floatAcumNcID, 2, '.', ',').'
                                                        </button>
                                                    </div>';
                                $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                    !important;">
                                                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#">
                                                                        Total I/D:
                                                        </button>
                                                    </div>';
                                $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                    !important;">
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                                       $'.number_format(($floatAcumFacID+$floatAcumNcID), 2, '.', ',').'
                                                        </button>
                                                    </div>';
                                $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                    !important;">
                                                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#">
                                                                        Facturación BS:
                                                        </button>
                                                    </div>';
                                $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                    !important;">
                                                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                                    $'.number_format($floatAcumFacBS, 2, '.', ',').'
                                                    </button>
                                                    </div>';
                                $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                    !important;">
                                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#">
                                                                        Notas de crédito BS:
                                                        </button>
                                                    </div>';
                                $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                    !important;">
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                                        $'.number_format($floatAcumNcBS, 2, '.', ',').'
                                                        </button>
                                                    </div>';
                                $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                    !important;">
                                                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#">
                                                                        Total B/S:
                                                        </button>
                                                    </div>';
                                $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                    !important;">
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                                       $'.number_format(($floatAcumFacBS+$floatAcumNcBS), 2, '.', ',').'
                                                        </button>
                                                    </div>';
                            }
                            $strCabModal .=  '</div>';
                            $arrayResponse['boolSuccess']     = true;
                            $arrayResponse['strBodyModal']    = $strCabModal.$strBodyModal;
                            $arrayResponse['strMensajeError'] = '';
                        }//( !empty($arrayResultadoService) )
                        else
                        {
                            $arrayResponse['boolSuccess']     = false;
                            $arrayResponse['strBodyModal']    = $strCabModal.$strBodyModal;
                        }
                    }//Ordenes MRC
                }//($strTipoPersonal==='SUBGERENTE' || $strTipoPersonal==='GERENTE_VENTAS')
                else
                {
                    $strBodyModal .= '<div class="row" style="margin-top:5px; padding: 10px 10px;">
                                        <div class="col-xs-0-5  modal-header" style="text-align: center;">
                                            <h5><b>#</b></h5>
                                        </div>
                                        <div class="'.$strClaseCliente.' modal-header" style="text-align: center;">
                                            <h5><b>Cliente</b></h5>
                                        </div>
                                        <div class="col-lg-2  modal-header" style="text-align: center;">
                                            <h5><b>Login</b></h5>
                                        </div>
                                        <div class="'.$strClaseProducto.' modal-header" style="text-align: center;">
                                            <h5><b>Producto</b></h5>
                                        </div>
                                        <div class="col-lg-2  modal-header" style="text-align: center;">
                                            <h5><b>Facturación</b></h5>
                                        </div>
                                        <div class="col-xs-0-5  modal-header" style="text-align: center;">
                                            <h5><b>NC</b></h5>
                                        </div>
                                    </div>';
                    if( !empty($arrayResultadoService) )
                    {
                        $intContadorRes = 1;
                        $floatAcumFac = 0;
                        $floatAcumNc = 0;
                        foreach( $arrayResultadoService as $arrayItem )
                        {
                            $floatValorFac   = 0;
                            $floatValorNc    = 0;
                            $floatValorFacID = 0;
                            $floatValorNcID  = 0;
                            $floatValorFacBS = 0;
                            $floatValorNcBS  = 0;
                            $strBodyModal .= '<div class="row" style="margin-top:5px; padding: 10px 10px;">
                                                <div class="col-xs-0-5" style="text-align: center;">
                                                    <h5>'.$intContadorRes.'</h5>
                                                </div>
                                                <div class="'.$strClaseCliente.'" style="text-align: center;">
                                                    <h5>'.$arrayItem['strCliente'].'</h5>
                                                </div>
                                                <div class="col-lg-2" style="text-align: center;">
                                                    <h5>'.$arrayItem['strLogin'].'</h5>
                                                </div>';
                            if (isset($arrayItem['strObservacion']) && !empty($arrayItem['strObservacion'])) {
                                $strProducto = $arrayItem['strObservacion'];
                            }
                            else
                            {
                                $strProducto = $arrayItem['strProducto'];
                            }
                            $strBodyModal .= '<div class="'.$strClaseProducto.'" style="text-align: center;">
                                                    <h5>'.$strProducto.'</h5>
                                              </div>';
                            if ( $strTipo === 'MRC' )
                            {
                                $floatValorFac   = $arrayItem['floatFacMrc'];
                                $floatValorNc    = $arrayItem['floatNcMrc'];
                                $floatValorFacID = $arrayItem['floatFacMrcID'];
                                $floatValorNcID  = $arrayItem['floatNcMrcID'];
                                $floatValorFacBS = $arrayItem['floatFacMrcBS'];
                                $floatValorNcBS  = $arrayItem['floatNcMrcBS'];
                            }
                            elseif( $strTipo === 'NRC' )
                            {
                                $floatValorFac = $arrayItem['floatFacNrc'];
                                $floatValorNc  = $arrayItem['floatNcNrc'];
                            }
                            $strBodyModal .=    '<div class="col-lg-2" style="text-align: center;">
                                                    <h5>$'.number_format($floatValorFac, 2, '.', ',').'</h5>
                                                </div>
                                                <div class="col-xs-0-5" style="text-align: center;">
                                                    <h5>$'.number_format($floatValorNc, 2, '.', ',').'</h5>
                                                </div>
                                             </div>';
                            $floatAcumFac   += $floatValorFac;
                            $floatAcumNc    += $floatValorNc;
                            $floatAcumFacID += $floatValorFacID;
                            $floatAcumNcID  += $floatValorNcID;
                            $floatAcumFacBS += $floatValorFacBS;
                            $floatAcumNcBS  += $floatValorNcBS;
                            $intContadorRes++;
                        }
                        $strCabModal .= '<div class="row" style="margin-top:5px; padding: 5px 5px;">
                                            <div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#" style="width:120px">
                                                                Facturación:
                                                </button>
                                            </div>';
                        $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                            $'.number_format($floatAcumFac, 2, '.', ',').'
                                            </button>
                                            </div>';
                        $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#" style="width:150px">
                                                                Notas de crédito:
                                                </button>
                                            </div>';
                        $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                                $'.number_format($floatAcumNc, 2, '.', ',').'
                                                </button>
                                            </div>';
                        $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#" style="width:80px">
                                                                Total:
                                                </button>
                                            </div>';
                        $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                               $'.number_format(($floatAcumFac+$floatAcumNc), 2, '.', ',').'
                                                </button>
                                            </div>';
                        if ($strTipo === 'MRC' )
                        {
                            $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                !important;">
                                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#">
                                                                    Facturación I/D:
                                                    </button>
                                                </div>';
                            $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                !important;">
                                                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                                $'.number_format($floatAcumFacID, 2, '.', ',').'
                                                </button>
                                                </div>';
                            $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                !important;">
                                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#">
                                                                    Notas de crédito I/D:
                                                    </button>
                                                </div>';
                            $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                !important;">
                                                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                                    $'.number_format($floatAcumNcID, 2, '.', ',').'
                                                    </button>
                                                </div>';
                            $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                !important;">
                                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#">
                                                                    Total I/D:
                                                    </button>
                                                </div>';
                            $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                !important;">
                                                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                                   $'.number_format(($floatAcumFacID+$floatAcumNcID), 2, '.', ',').'
                                                    </button>
                                                </div>';
                            $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                !important;">
                                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#">
                                                                    Facturación BS:
                                                    </button>
                                                </div>';
                            $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                !important;">
                                                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                                $'.number_format($floatAcumFacBS, 2, '.', ',').'
                                                </button>
                                                </div>';
                            $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                !important;">
                                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#">
                                                                    Notas de crédito BS:
                                                    </button>
                                                </div>';
                            $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                !important;">
                                                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                                    $'.number_format($floatAcumNcBS, 2, '.', ',').'
                                                    </button>
                                                </div>';
                            $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                !important;">
                                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#">
                                                                    Total I/D:
                                                    </button>
                                                </div>';
                            $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px 
                                                !important;">
                                                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                                   $'.number_format(($floatAcumFacBS+$floatAcumNcBS), 2, '.', ',').'
                                                    </button>
                                                </div>';
                        }
                        $strCabModal .=  '</div>';
                        $arrayResponse['boolSuccess']     = true;
                        $arrayResponse['strBodyModal']    = $strCabModal.$strBodyModal;
                        $arrayResponse['strMensajeError'] = '';
                    }//( !empty($arrayResultadoService) )
                    else
                    {
                        $arrayResponse['boolSuccess']     = false;
                        $arrayResponse['strBodyModal']    = $strCabModal.$strBodyModal;
                    }
                }
            }//($strTipoConsulta =='DETALLADO')
            else
            {                
                $arrayResponse['boolSuccess']     = true;
                $arrayResponse['strMensajeError'] = '';
            }
        }
        catch( \Exception $e )
        {
            $serviceUtil->insertError('TELCOS+',
                                      'SeguridadBundle.DefaultController.getDetalleFacturacionAsesorAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        $objJsonResponse->setData($arrayResponse);
        return $objJsonResponse;
    }
    /**
     * Documentación para el método 'getDetalleResultadosVentasAction'.
     *
     * 
     * Método que retorna detalladamente la información correspondiente de facturación Mrc y Nrc del vendedor.
     *
     * @return Response
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 01-09-2018
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.1 11-12-2021 - Se modifica para mostrar los cumplimientos de internet/datos y business solutions.
     * @since 1.1
     */    
    public function getDetalleResultadosVentasAction()
    {
        $objJsonResponse         = new JsonResponse();
        $objRequest              = $this->get('request');
        $objSession              = $objRequest->getSession();
        $strPrefijoEmpresa       = $objSession->get('prefijoEmpresa');
        $strUsrCreacion          = $objSession->get('user');
        $intIdPersonEmpresaRol   = $objSession->get('idPersonaEmpresaRol');
        $strIpCreacion           = $objRequest->getClientIp();
        $strFechaInicio          = $objRequest->request->get('strFechaInicio') ? $objRequest->request->get('strFechaInicio') : "";
        $strFechaFin             = $objRequest->request->get('strFechaFin') ? $objRequest->request->get('strFechaFin') : '';
        $strTipo                 = $objRequest->request->get('strTipo') ? $objRequest->request->get('strTipo') : '';
        $strGrupo                = $objRequest->request->get('strGrupo') ? $objRequest->request->get('strGrupo') : '';
        $strTipoConsulta         = $objRequest->request->get('strTipoConsulta') ? $objRequest->request->get('strTipoConsulta') : '';
        $serviceComercial        = $this->get('comercial.Comercial');
        $strUserBiFinanciero     = $this->container->getParameter('user_bifinanciero');
        $strPasswordBiFinanciero = $this->container->getParameter('passwd_bifinanciero');
        $strDatabaseDsn          = $this->container->getParameter('database_dsn');
        $serviceUtil             = $this->get('schema.Util');
        $strTipoPersonal         = "";
        $arrayResponse           = array('boolSuccess'     => false, 
                                         'strMensajeError' => 'No se ha encontrado información de facturación solicitada');
        try
        {
            /**
             * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL' ASOCIADA A EL USUARIO LOGONEADO 
             */
            $arrayPersonalEmpresaRol       = array('intIdPersonEmpresaRol' => $intIdPersonEmpresaRol,
                                                   'strUsrCreacion'        => $strUsrCreacion,
                                                   'strIpCreacion'         => $strIpCreacion);

            $arrayResultadoCaracteristicas = $serviceComercial->getCaracteristicasPersonalEmpresaRol($arrayPersonalEmpresaRol);
            
            if( isset($arrayResultadoCaracteristicas['strCargoPersonal']) && !empty($arrayResultadoCaracteristicas['strCargoPersonal']) )
            {
                $strTipoPersonal = $arrayResultadoCaracteristicas['strCargoPersonal'];
            }//( isset($arrayResultadoCaracteristicas['strCargoPersonal']) && !empty($arrayResultadoCaracteristicas['strCargoPersonal']) )
            
            $arrayParametrosServiceComercial = array('strPrefijoEmpresa'       => $strPrefijoEmpresa,
                                                     'strFechaInicio'          => $strFechaInicio,
                                                     'strFechaFin'             => $strFechaFin,
                                                     'strUsrCreacion'          => $strUsrCreacion,
                                                     'strIpCreacion'           => $strIpCreacion,
                                                     'strDatabaseDsn'          => $strDatabaseDsn,
                                                     'strTipo'                 => $strTipo,
                                                     'strTipoConsulta'         => $strTipoConsulta,
                                                     'strUserBiFinanciero'     => $strUserBiFinanciero,
                                                     'strPasswordBiFinanciero' => $strPasswordBiFinanciero,
                                                     'strTipoPersonal'         => $strTipoPersonal,
                                                     'intIdPersonEmpresaRol'   => $intIdPersonEmpresaRol);
            //Se valida el tipo de la consulta
            if( $strTipoConsulta == 'EXPORTAR_NRC' || $strTipoConsulta == 'EXPORTAR_MRC' || $strTipoConsulta == 'EXPORTAR_MRCID' || 
                $strTipoConsulta == 'EXPORTAR_MRCBS')
            {
                $serviceComercial->getCumplimiento($arrayParametrosServiceComercial);
                $arrayResponse['boolSuccess']     = true;
                $arrayResponse['strMensajeError'] = 'Se genero el reporte solicitado y fue enviado a su correo';
            }
            else
            {
                $arrayResultadoService = $serviceComercial->getDetalleFacturacionAsesor($arrayParametrosServiceComercial);
            }                        
            
            if( !empty($arrayResultadoService) )
            {
                if( $strTipo === 'MRC' )
                {
                    $strBodyModal = '<div class="row" style="margin-top:5px; padding: 5px 5px;">
                                        <div class="col-lg-1  modal-header" style="text-align: justify;">
                                            <h5><b>#</b></h5>
                                        </div>
                                        <div class="col-lg-2  modal-header" style="text-align: center;">
                                            <h5><b>Vendedor</b></h5>
                                        </div>
                                        <div class="col-lg-2  modal-header" style="text-align: center;">
                                            <h5><b>Base</b></h5>
                                        </div>
                                        <div class="col-lg-1  modal-header" style="text-align: center;">
                                            <h5><b>Meta</b></h5>
                                        </div>                                        
                                        <div class="col-lg-2  modal-header" style="text-align: center;">
                                            <h5><b>Facturación</b></h5>
                                        </div>
                                        <div class="col-lg-2  modal-header" style="text-align: center;">
                                            <h5><b>Dif. vs ppto.</b></h5>
                                        </div>
                                        <div class="col-lg-2  modal-header" style="text-align: center;">
                                            <h5><b>Cump. de meta</b></h5>
                                        </div>
                                     </div>';

                        $intContadorVend    = 0;
                        $floatAcumFac       = 0;
                        $floatPresupuesto   = 0;    
                        $floatTotalPor      = 0;
                        $floatTotalFal      = 0;

                        if ($strGrupo == 'OTROS')
                        {
                           $strBase         = 'BASE';
                           $strMeta         = 'META';
                           $strFact         = 'FACTURACION';
                           $strPresupuesto  = 'DIF_PRESUPUESTO';
                           $strCumplimiento = 'CUMPLIMIENTO_META';
                        }else if($strGrupo == 'ID')
                        {
                            $strBase         = 'BASEID';
                            $strMeta         = 'METAID';
                            $strFact         = 'FACTURACIONID';
                            $strPresupuesto  = 'DIF_PRESUPUESTOID';
                            $strCumplimiento = 'CUMPLIMIENTO_METAID';
                        }else if($strGrupo == 'BS')
                        {
                            $strBase         = 'BASEBS';
                            $strMeta         = 'METABS';
                            $strFact         = 'FACTURACIONBS';
                            $strPresupuesto  = 'DIF_PRESUPUESTOBS';
                            $strCumplimiento = 'CUMPLIMIENTO_METABS';
                        }
                        
                        foreach( $arrayResultadoService as $arrayItemCumplimiento )//vendedores
                        {
                            $intContadorVend++;
                            $strBodyModal .= '<div class="row" style="margin-top:5px; padding: 10px 10px;">
                                                <div class="col-lg-1" style="text-align: justify;"><h5><b>'.$intContadorVend.'</b></h5></div>
                                                <div class="col-lg-2" style="text-align: center;"><h5><b>'.
                                                                        $arrayItemCumplimiento['USR_VENDEDOR'].'</b></h5></div>
                                                <div class="col-lg-2" style="text-align: center;"><h5><b>$'.
                                                                        number_format($arrayItemCumplimiento[$strBase], 2, '.', ',').'</b></h5></div>
                                                <div class="col-lg-1" style="text-align: center;"><h5><b>$'.
                                                                        number_format($arrayItemCumplimiento[$strMeta], 2, '.', ',').'</b></h5></div>
                                                <div class="col-lg-2" style="text-align: center;"><h5><b>$'.
                                                                        number_format($arrayItemCumplimiento[$strFact], 2, '.', ',').'</b></h5></div>
                                                <div class="col-lg-2" style="text-align: center;"><h5><b>$'.
                                                                number_format($arrayItemCumplimiento[$strPresupuesto], 2, '.', ',').'</b></h5></div>
                                                <div class="col-lg-2" style="text-align: center;"><h5><b>$'.
                                                                number_format($arrayItemCumplimiento[$strCumplimiento], 2, '.', ',').'%</b></h5></div>
                                                </div>';
                            $floatAcumFac  += floatval($arrayItemCumplimiento[$strFact]);
                            $floatAcumBas  += floatval($arrayItemCumplimiento[$strBase]);
                            $floatAcumMet  += floatval($arrayItemCumplimiento[$strMeta]); 
                        }
                        $floatPresupuesto=$floatAcumBas+$floatAcumMet;
                        $floatTotalPor = ((floatval($floatAcumFac)-floatval($floatAcumBas))/floatval($floatAcumMet))*100;
                        $floatTotalFal = floatval($floatAcumFac)-floatval($floatAcumBas)-floatval($floatAcumMet);

                        $strCabModal .= '<div class="row" style="margin-top:5px; padding: 5px 5px;">
                                            <div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#">
                                                                Presupuesto (B+M):
                                                </button>
                                            </div>';
                        $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                            $'.number_format($floatPresupuesto, 2, '.', ',').'
                                            </button>
                                            </div>';
                        $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#">
                                                                Dif. vs ppto.:
                                                </button>
                                            </div>';
                        $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                                $'.number_format($floatTotalFal, 2, '.', ',').'
                                                </button>
                                            </div>';

                        $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#">
                                                                Cump. de meta:
                                                </button>
                                            </div>';
                        $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                               '.number_format($floatTotalPor, 2, '.', ',').'%
                                                </button>
                                            </div>
                                         </div>';                        
                        $arrayResponse['boolSuccess']     = true;
                        $arrayResponse['strBodyModal']    = $strCabModal.$strBodyModal;
                        $arrayResponse['strMensajeError'] = '';

                }//if($strTipo === 'MRC')
                elseif( $strTipo === 'NRC' )
                {                               
                    $strBodyModal = '<div class="row" style="margin-top:5px; padding: 5px 5px;">
                                        <div class="col-lg-1  modal-header" style="text-align: justify;">
                                            <h5><b>#</b></h5>
                                        </div>
                                        <div class="col-lg-2  modal-header" style="text-align: center;">
                                            <h5><b>Vendedor</b></h5>
                                        </div>
                                        <div class="col-lg-2  modal-header" style="text-align: center;">
                                            <h5><b>Meta</b></h5>
                                        </div>                                        
                                        <div class="col-lg-3  modal-header" style="text-align: center;">
                                            <h5><b>Facturación</b></h5>
                                        </div>
                                        <div class="col-lg-2  modal-header" style="text-align: center;">
                                            <h5><b>Dif. vs ppto.</b></h5>
                                        </div>
                                        <div class="col-lg-2  modal-header" style="text-align: center;">
                                            <h5><b>Cump. de meta</b></h5>
                                        </div>
                                     </div>';
                    $floatAcumFac       = 0;
                    $intContadorVend    = 0;
                    $floatTotalPor      = 0;
                    $floatTotalFal      = 0;     
                    
                    foreach( $arrayResultadoService as $arrayItemCumplimiento )
                    {
                        $intContadorVend++;
                        $strBodyModal      .= '<div class="row" style="margin-top:5px; padding: 10px 10px;">
                                                <div class="col-lg-1" style="text-align: justify;"><h5><b>'.$intContadorVend.'</b></h5></div>
                                                <div class="col-lg-2" style="text-align: center;"><h5><b>'.$arrayItemCumplimiento['USR_VENDEDOR'].'</b></h5></div>
                                                <div class="col-lg-2" style="text-align: center;"><h5><b>$'.number_format($arrayItemCumplimiento['META']).'</b></h5></div>                                                            
                                                <div class="col-lg-3" style="text-align: center;"><h5><b>$'.number_format($arrayItemCumplimiento['FACTURACION'], 2, '.', ',').'</b></h5></div>
                                                <div class="col-lg-2" style="text-align: center;"><h5><b>$'.number_format($arrayItemCumplimiento['DIF_PRESUPUESTO'], 2, '.', ',').'</b></h5></div>
                                                <div class="col-lg-2" style="text-align: center;"><h5><b>$'.number_format($arrayItemCumplimiento['CUMPLIMIENTO_META'], 2, '.', ',').'%</b></h5></div>
                                                </div>';
                        $floatAcumFac  += floatval($arrayItemCumplimiento['FACTURACION']);
                        $floatAcumMet  += floatval($arrayItemCumplimiento['META']);
                    }
                    $floatTotalPor = (floatval($floatAcumFac)/floatval($floatAcumMet))*100;
                    $floatTotalFal = floatval($floatAcumFac)-floatval($floatAcumMet);                        
                    

                    $strCabModal .= '<div class="row" style="margin-top:5px; padding: 5px 5px;">
                                        <div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#">
                                                            Presupuesto :
                                            </button>
                                        </div>';
                    $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                        $'.number_format($floatAcumMet, 2, '.', ',').'
                                        </button>
                                        </div>';
                    $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#">
                                                            Dif. vs ppto.:
                                            </button>
                                        </div>';
                    $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                            $'.number_format($floatTotalFal, 2, '.', ',').'
                                            </button>
                                        </div>';

                    $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#">
                                                            Cump. de meta:
                                            </button>
                                        </div>';
                    $strCabModal .= '<div class="col-lg-2" style="text-align: center;padding-left:0px !important;padding-right:0px !important;">
                                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#">
                                                           '.number_format($floatTotalPor, 2, '.', ',').'%
                                            </button>
                                        </div>
                                     </div>';                        
                    $arrayResponse['boolSuccess']     = true;
                    $arrayResponse['strBodyModal']    = $strCabModal.$strBodyModal;
                    $arrayResponse['strMensajeError'] = '';

                }//elseif($strTipo === 'NRC')                
            }//( !empty($arrayResultadoService) )
            else
            {
                $arrayResponse['boolSuccess']     = false;
                $arrayResponse['strBodyModal']    = $strCabModal.$strBodyModal;
            }
        }
        catch( \Exception $e )
        {
            $serviceUtil->insertError('TELCOS+',
                                      'SeguridadBundle.DefaultController.getDetalleResultadosVentasAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        $objJsonResponse->setData($arrayResponse);
        return $objJsonResponse;
    }
    /**
     * Documentación para el método 'getDetalleResultadosVentasAction'.
     * 
     * Método que retorna detalladamente la información correspondiente de ordenes de los clientes.
     *
     * @return Response
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 03-09-2018
     * 
     */      
    public function getDetalleResultadosClientesAction()
    {
        $objJsonResponse         = new JsonResponse();
        $objRequest              = $this->get('request');
        $objSession              = $objRequest->getSession();
        $strPrefijoEmpresa       = $objSession->get('prefijoEmpresa');
        $strUsrCreacion          = $objSession->get('user');
        $intIdPersonEmpresaRol   = $objSession->get('idPersonaEmpresaRol');
        $strIpCreacion           = $objRequest->getClientIp();
        $strFechaInicio          = $objRequest->request->get('strFechaInicio') ? $objRequest->request->get('strFechaInicio') : "";
        $strFechaFin             = $objRequest->request->get('strFechaFin') ? $objRequest->request->get('strFechaFin') : '';
        $strTipo                 = $objRequest->request->get('strTipo') ? $objRequest->request->get('strTipo') : '';
        $strTipoConsulta         = $objRequest->request->get('strTipoConsulta') ? $objRequest->request->get('strTipoConsulta') : '';
        $serviceComercial        = $this->get('comercial.Comercial');
        $strUserBiFinanciero     = $this->container->getParameter('user_bifinanciero');
        $strPasswordBiFinanciero = $this->container->getParameter('passwd_bifinanciero');
        $strDatabaseDsn          = $this->container->getParameter('database_dsn');
        $serviceUtil             = $this->get('schema.Util');
        $strTipoPersonal         = "";
        $arrayResponse           = array('boolSuccess'     => false, 
                                         'strMensajeError' => 'No se ha encontrado información de clientes solicitada');
        try
        {
            /**
             * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL' ASOCIADA A EL USUARIO LOGONEADO 
             */
            $arrayPersonalEmpresaRol       = array('intIdPersonEmpresaRol' => $intIdPersonEmpresaRol,
                                                   'strUsrCreacion'        => $strUsrCreacion,
                                                   'strIpCreacion'         => $strIpCreacion);

            $arrayResultadoCaracteristicas = $serviceComercial->getCaracteristicasPersonalEmpresaRol($arrayPersonalEmpresaRol);
            
            if( isset($arrayResultadoCaracteristicas['strCargoPersonal']) && !empty($arrayResultadoCaracteristicas['strCargoPersonal']) )
            {
                $strTipoPersonal = $arrayResultadoCaracteristicas['strCargoPersonal'];
            }//( isset($arrayResultadoCaracteristicas['strCargoPersonal']) && !empty($arrayResultadoCaracteristicas['strCargoPersonal']) )
            
            $arrayParametrosServiceComercial = array('strPrefijoEmpresa'       => $strPrefijoEmpresa,
                                                     'strFechaInicio'          => $strFechaInicio,
                                                     'strFechaFin'             => $strFechaFin,
                                                     'strUsrCreacion'          => $strUsrCreacion,
                                                     'strIpCreacion'           => $strIpCreacion,
                                                     'strDatabaseDsn'          => $strDatabaseDsn,
                                                     'strTipo'                 => $strTipo,
                                                     'strTipoConsulta'         => $strTipoConsulta,
                                                     'strUserBiFinanciero'     => $strUserBiFinanciero,
                                                     'strPasswordBiFinanciero' => $strPasswordBiFinanciero,
                                                     'strTipoPersonal'         => $strTipoPersonal,
                                                     'intIdPersonEmpresaRol'   => $intIdPersonEmpresaRol);
                                                     
            $arrayParametros                    = $arrayParametrosServiceComercial;
            $arrayParametros['strTipo']         = 'MRC';
            $arrayParametros['strTipoConsulta'] = 'AGRUPADO';
            $arrayCltNuevosDet                  = $serviceComercial->getComparacionFacturacionAsesor($arrayParametros);

            $strModalCab       = '<div class="row" style="margin-top:1px; padding: 10px 30px;">
                                    <div class="col-lg-4  modal-header" style="text-align: center;">
                                        <h5><b>Cant. Clientes</b></h5>
                                    </div>
                                    <div class="col-lg-4  modal-header" style="text-align: center;">
                                        <h5><b>Vendedor</b></h5>
                                    </div>
                                    <div class="col-lg-4  modal-header" style="text-align: center;">
                                        <h5><b>Total</b></h5>
                                    </div>
                                  </div>';
            $strPropiedadesCss ='<style type="text/css">
                                    li ul{display: none;}
                                    li:hover ul{display:block;}
                                </style>';
            $strBodyModal      ='<div class="row" style="margin-top:5px; padding: 10px 15px;">
                                    <div class="col-xs-0-5  modal-header" style="text-align: center;">
                                        <h5><b>#</b></h5>
                                    </div>
                                    <div class="col-lg-4  modal-header" style="text-align: center;">
                                        <h5><b>Asesor</b></h5>
                                    </div>
                                    <div class="col-lg-4 modal-header" style="text-align: center;">
                                        <h5><b>Cliente</b></h5>
                                    </div>
                                    <div class="col-xs-3-5  modal-header" style="text-align: center;">
                                        <h5><b>TOTAL</b></h5>
                                    </div>
                                 </div>';
            if( !empty($arrayCltNuevosDet) )
            {
                $strModalCltNuevo   = '<div class="row" style="margin-top:1px; padding: 10px 10px;">
                                            <div class="col-lg-12 modal-header" style="text-align: center;">
                                                <h5><b>Clientes Nuevos</b></h5>
                                            </div>
                                       </div>';                
                if( $strTipoPersonal === 'SUBGERENTE' || $strTipoPersonal === 'GERENTE_VENTAS' )
                {
                    $strModalCltNuevo   .= $strModalCab;
                    $arrayFilter         = array();            
                    $arrayCltNuevosTotal = array();                
                    foreach( $arrayCltNuevosDet as $arrayItemVendedor )
                    {
                        array_push($arrayFilter,$arrayItemVendedor['VENDEDOR']);
                    }
                    $arrayVendedor = array_unique($arrayFilter);        
                    foreach( $arrayVendedor as $arrayItemVendedor)
                    {
                        $intContCltNuevo = 0;
                        $floatTotal      = 0.0;
                        foreach( $arrayCltNuevosDet as $arrayItemVendedorNuevo )
                        {
                            if( $arrayItemVendedor == $arrayItemVendedorNuevo['VENDEDOR'] )
                            {
                                $intContCltNuevo   ++ ;
                                $floatTotal        = $floatTotal+$arrayItemVendedorNuevo['TOTAL'];
                                $arrayCltNuevosAux = array('VENDEDOR'=> $arrayItemVendedorNuevo['VENDEDOR'],
                                                           'CANTIDAD'=> $intContCltNuevo,
                                                           'TOTAL'   => $floatTotal);
                            }                        
                        }
                        array_push($arrayCltNuevosTotal,$arrayCltNuevosAux);                    
                    }
                    foreach( $arrayCltNuevosTotal as $arraItemClientesNuevos )
                    {
                        $strModalCltNuevo .= '<ul><li><div class="row" style="margin-top:5px; padding: 5px 5px;">
                                                    <div class="col-lg-4 " style="text-align: center;">
                                                        <h5><b>'.$arraItemClientesNuevos['CANTIDAD'].'</b></h5>                                            
                                                    </div>
                                                    <div class="col-lg-4 " style="text-align: center;">
                                                        <h5><b>'.$arraItemClientesNuevos['VENDEDOR'].'</b></h5>
                                                    </div>
                                                    <div class="col-lg-4 " style="text-align: center;">
                                                        <h5><b> $'.number_format($arraItemClientesNuevos['TOTAL'], 2, '.', ',').'</b></h5>                                                
                                                    </div>
                                                   </div>';
                        $strModalCltNuevo .= '<ul><li>'.$strBodyModal.'<li>';
                        $intContadorCltNuevos = 0;
                        sort($arrayCltNuevosDet);
                        foreach( $arrayCltNuevosDet as $arraItemClientesNuevosDet )
                        {
                            if( $arraItemClientesNuevos['VENDEDOR'] == $arraItemClientesNuevosDet['VENDEDOR'] )
                            {
                                $intContadorCltNuevos ++;
                                $strModalCltNuevo     .= '<div class="row" style="margin-top:5px; padding: 10px 10px;">
                                                            <div class="col-xs-0-5 " style="text-align: center;">
                                                                <h5><b>'.$intContadorCltNuevos.'</b></h5>
                                                            </div>
                                                            <div class="col-lg-4 " style="text-align: center;">
                                                                <h5><b>'.$arraItemClientesNuevosDet['VENDEDOR'].'</b></h5>
                                                            </div>
                                                            <div class="col-lg-4" style="text-align: center;">
                                                                <h5><b>'.$arraItemClientesNuevosDet['CLIENTE'].'</b></h5>
                                                            </div>
                                                            <div class="col-xs-3-5 " style="text-align: center;">
                                                                <h5><b>$'.$arraItemClientesNuevosDet['TOTAL'].'</b></h5>
                                                            </div>
                                                          </div>';
                            }//if($arraItemClientesNuevos['VENDEDOR'] == $arraItemClientesNuevosDet['VENDEDOR'])
                        }
                        $strModalCltNuevo.= '</li></li></ul></li></ul>';
                    }//foreach( $arrayCltNuevosTotal as $arraItemClientesNuevos )
                    $strModalCltNuevo .= $strPropiedadesCss;
                }
                else
                {
                    $strModalCltNuevo .= $strBodyModal;
                    $intContadorCltNuevos = 0;
                    sort($arrayCltNuevosDet);
                    foreach( $arrayCltNuevosDet as $arraItemClientesNuevosDet )
                    {
                        $intContadorCltNuevos ++;
                        $strModalCltNuevo     .= '<div class="row" style="margin-top:5px; padding: 10px 10px;">
                                                    <div class="col-xs-0-5 " style="text-align: center;">
                                                        <h5><b>'.$intContadorCltNuevos.'</b></h5>
                                                    </div>
                                                    <div class="col-lg-4 " style="text-align: center;">
                                                        <h5><b>'.$arraItemClientesNuevosDet['VENDEDOR'].'</b></h5>
                                                    </div>
                                                    <div class="col-lg-4" style="text-align: center;">
                                                        <h5><b>'.$arraItemClientesNuevosDet['CLIENTE'].'</b></h5>
                                                    </div>
                                                    <div class="col-xs-3-5 " style="text-align: center;">
                                                        <h5><b>$'.$arraItemClientesNuevosDet['TOTAL'].'</b></h5>
                                                    </div>
                                                  </div>';
                    }
                }
            }//if( !empty($arrayCltNuevosDet) )
            $arrayParametrosFact                    =$arrayParametrosServiceComercial;
            $arrayParametrosFact['strTipo']         = 'MRC';
            $arrayParametrosFact['strTipoConsulta'] = 'POR_FACTURAR';
            $arrayCltFacturarDet                    = $serviceComercial-> getComparacionFacturacionAsesor($arrayParametrosFact);

            if( !empty($arrayCltFacturarDet) )
            {
                $strModalCltFact   = '<div class="row" style="margin-top:1px; padding: 10px 10px;">
                                        <div class="col-lg-12 modal-header" style="text-align: center;">
                                            <h5><b>Clientes Por Facturar</b></h5>
                                        </div>
                                      </div>';                                
                if( $strTipoPersonal === 'SUBGERENTE' || $strTipoPersonal === 'GERENTE_VENTAS' )
                {
                    $arrayFilter       = array();            
                    $arrayCltFactTotal = array();
                    $strModalCltFact  .= $strModalCab;
                    foreach( $arrayCltFacturarDet as $arrayItemVendedor )
                    {
                        array_push($arrayFilter,$arrayItemVendedor['VENDEDOR']);
                    }
                    $arrayVendedor = array_unique($arrayFilter);        
                    foreach( $arrayVendedor as $arrayItemVendedor)
                    {
                        $intContCltFact = 0;
                        $floatTotal     = 0.0;
                        foreach( $arrayCltFacturarDet as $arraItemClientesFactDet )
                        {
                            if( $arrayItemVendedor == $arraItemClientesFactDet['VENDEDOR'] )
                            {
                                $intContCltFact ++ ;
                                $floatTotal     = $floatTotal+$arraItemClientesFactDet['TOTAL'];
                                $arrayCltFacAux = array('VENDEDOR'=> $arraItemClientesFactDet['VENDEDOR'],
                                                        'CANTIDAD'=> $intContCltFact,
                                                        'TOTAL'   => $floatTotal);
                            }                        
                        }
                        array_push($arrayCltFactTotal,$arrayCltFacAux);                    
                    }
                    foreach( $arrayCltFactTotal as $arraItemClientesFact )
                    {
                        $strModalCltFact .= '<ul><li><div class="row" style="margin-top:5px; padding: 5px 5px;">
                                                <div class="col-lg-4 " style="text-align: center;">
                                                    <h5><b>'.$arraItemClientesFact['CANTIDAD'].'</b></h5>                                            
                                                </div>
                                                <div class="col-lg-4 " style="text-align: center;">
                                                    <h5><b>'.$arraItemClientesFact['VENDEDOR'].'</b></h5>
                                                </div>
                                                <div class="col-lg-4 " style="text-align: center;">
                                                    <h5><b> $'.number_format($arraItemClientesFact['TOTAL'], 2, '.', ',').'</b></h5>                                                
                                                </div>
                                            </div>';
                        $strModalCltFact .= '<ul><li>'.$strBodyModal.'<li>';
                        $intContadorCltFact=0;
                        sort($arrayCltFacturarDet);
                        foreach( $arrayCltFacturarDet as $arraItemClientesFactDet )
                        {
                            if( $arraItemClientesFact['VENDEDOR'] == $arraItemClientesFactDet['VENDEDOR'] )
                            {
                                $intContadorCltFact++;
                                $strModalCltFact  .= '<div class="row" style="margin-top:5px; padding: 10px 10px;">
                                                        <div class="col-xs-0-5 " style="text-align: center;">
                                                            <h5><b>'.$intContadorCltFact.'</b></h5>
                                                        </div>
                                                        <div class="col-lg-4 " style="text-align: center;">
                                                            <h5><b>'.$arraItemClientesFactDet['VENDEDOR'].'</b></h5>
                                                        </div>
                                                        <div class="col-lg-4" style="text-align: center;">
                                                            <h5><b>'.$arraItemClientesFactDet['CLIENTE'].'</b></h5>
                                                        </div>
                                                        <div class="col-xs-3-5 " style="text-align: center;">
                                                            <h5><b>$'.$arraItemClientesFactDet['TOTAL'].'</b></h5>
                                                        </div>
                                                    </div>';
                            }
                        }//foreach( $arrayCltFacturarDet as $arraItemClientesFactDet )
                        $strModalCltFact .= '</li></li></ul></li></ul>';
                    }//foreach( $arrayCltFactTotal as $arraItemClientesFact )
                    $strModalCltFact .= $strPropiedadesCss;
                }
                else
                {
                    $strModalCltFact .= $strBodyModal;
                    $intContadorCltFact=0;
                    sort($arrayCltFacturarDet);
                    foreach( $arrayCltFacturarDet as $arraItemClientesFactDet )
                    {
                        $intContadorCltFact++;
                        $strModalCltFact  .= '<div class="row" style="margin-top:5px; padding: 10px 10px;">
                                                <div class="col-xs-0-5 " style="text-align: center;">
                                                    <h5><b>'.$intContadorCltFact.'</b></h5>
                                                </div>
                                                <div class="col-lg-4 " style="text-align: center;">
                                                    <h5><b>'.$arraItemClientesFactDet['VENDEDOR'].'</b></h5>
                                                </div>
                                                <div class="col-lg-4" style="text-align: center;">
                                                    <h5><b>'.$arraItemClientesFactDet['CLIENTE'].'</b></h5>
                                                </div>
                                                <div class="col-xs-3-5 " style="text-align: center;">
                                                    <h5><b>$'.$arraItemClientesFactDet['TOTAL'].'</b></h5>
                                                </div>
                                            </div>';
                    }//foreach( $arrayCltFacturarDet as $arraItemClientesFactDet )                    
                }
            }//if( !empty($arrayCltFacturarDet) )
            $arrayCltCancelDet = $serviceComercial->getClientesCancelados($arrayParametrosServiceComercial);            
            if( !empty($arrayCltCancelDet) )
            {
                $strModalCltCancel     = '<div class="row" style="margin-top:1px; padding: 10px 10px;">
                                            <div class="col-lg-12 modal-header" style="text-align: center;">
                                                <h5><b>Clientes cancelados(mes anterior)</b></h5>
                                            </div>                            
                                           </div>';
                $strBodyModalCltCancel ='<div class="row" style="margin-top:5px; padding: 10px 15px;">
                                            <div class="col-xs-0-5  modal-header" style="text-align: center;">
                                                <h5><b>#</b></h5>
                                            </div>
                                            <div class="col-lg-2  modal-header" style="text-align: center;">
                                                <h5><b>Asesor</b></h5>
                                            </div>
                                            <div class="col-lg-2 modal-header" style="text-align: center;">
                                                <h5><b>Cliente</b></h5>
                                            </div>
                                            <div class="col-lg-2  modal-header" style="text-align: center;">
                                                <h5><b>Login</b></h5>
                                            </div>
                                            <div class="col-lg-2 modal-header" style="text-align: center;">
                                                <h5><b>Producto</b></h5>
                                            </div>                                            
                                            <div class="col-lg-2  modal-header" style="text-align: center;">
                                                <h5><b>Motivo</b></h5>
                                            </div>
                                            <div class="col-xs-1-5  modal-header" style="text-align: center;">
                                                <h5><b>TOTAL</b></h5>
                                            </div>
                                          </div>';
                if( $strTipoPersonal === 'SUBGERENTE' || $strTipoPersonal === 'GERENTE_VENTAS' )
                {                                           
                    $strModalCltCancel .= $strModalCab;
                    $arrayFilter        = array();
                    $arrayVendedorTotal = array();
                    foreach( $arrayCltCancelDet as $arrayItemVendedor )
                    {
                        array_push($arrayFilter,$arrayItemVendedor['VENDEDOR']);
                    }
                    $arrayVendedor = array_unique($arrayFilter);
                    foreach($arrayVendedor as $arrayItemVendedor)
                    {
                        $intCltCancel = 0;
                        $floatTotal   = 0.0;                    
                        foreach($arrayCltCancelDet as $arrayItemClientesCancel)
                        {
                            if( $arrayItemVendedor == $arrayItemClientesCancel['VENDEDOR'])
                            {                            
                                $intCltCancel     ++;
                                $floatTotal       = $arrayItemClientesCancel['TOTAL'] + $floatTotal;                            
                                $arrayVendedorAux = array('VENDEDOR'=>$arrayItemVendedor,
                                                        'CANTIDAD'=>$intCltCancel,
                                                        'TOTAL'  =>$floatTotal);                            
                            }                        
                        }
                        array_push($arrayVendedorTotal,$arrayVendedorAux);                    
                    }            
                    foreach($arrayVendedorTotal as $arrayItemCltCancel)
                    {
                        $strModalCltCancel   .= '<ul><li>
                                                    <div class="row" style="margin-top:5px; padding: 5px 5px;">
                                                        <div class="col-lg-4 " style="text-align: center;">
                                                            <h5><b>'.$arrayItemCltCancel['CANTIDAD'].'</b></h5>                                            
                                                        </div>
                                                        <div class="col-lg-4 " style="text-align: center;">
                                                            <h5><b>'.$arrayItemCltCancel['VENDEDOR'].'</b></h5>
                                                        </div>
                                                        <div class="col-lg-4 " style="text-align: center;">
                                                            <h5><b> $'.number_format($arrayItemCltCancel['TOTAL'], 2, '.', ',').'</b></h5>                                                
                                                        </div>
                                                    </div>';
                        $strModalCltCancel   .= '<ul><li>'.$strBodyModalCltCancel.'<li>';
                        $intContadorCltCancel = 0;
                        sort($arrayCltCancelDet);
                        foreach( $arrayCltCancelDet as $arraItemCltCancelDet )
                        {
                            if( $arraItemCltCancelDet['VENDEDOR'] == $arrayItemCltCancel['VENDEDOR'] )
                            {
                                $intContadorCltCancel++;
                                $strModalCltCancel .= '<div class="row" style="margin-top:5px; padding: 10px 10px;">
                                                        <div class="col-xs-0-5 " style="text-align: center;">
                                                            <h6><b>'.$intContadorCltCancel.'</b></h6>
                                                        </div>
                                                        <div class="col-lg-2 " style="text-align: center;">
                                                            <h6><b>'.$arraItemCltCancelDet['VENDEDOR'].'</b></h6>
                                                        </div>
                                                        <div class="col-lg-2" style="text-align: center;">
                                                            <h6><b>'.$arraItemCltCancelDet['CLIENTE'].'</b></h6>
                                                        </div>
                                                        <div class="col-lg-2 " style="text-align: center;">
                                                            <h6><b>'.$arraItemCltCancelDet['LOGIN'].'</b></h6>
                                                        </div>
                                                        <div class="col-lg-2" style="text-align: center;">
                                                            <h6><b>'.$arraItemCltCancelDet['DESCRIPCION_PRODUCTO'].'</b></h6>
                                                        </div>
                                                        <div class="col-lg-2 " style="text-align: center;">
                                                            <h6><b>'.$arraItemCltCancelDet['MOTIVO'].'</b></h6>
                                                        </div>                                                    
                                                        <div class="col-xs-1-5 " style="text-align: center;">
                                                            <h6><b>$'.$arraItemCltCancelDet['TOTAL'].'</b></h6>
                                                        </div>
                                                    </div>';                            
                            }
                        }
                        $strModalCltCancel .= '</li></li></ul></li></ul>';
                    }
                    $strModalCltCancel .= $strPropiedadesCss;
                }
                else
                {
                    $strModalCltCancel   .= $strBodyModalCltCancel;
                    $intContadorCltCancel = 0;
                    sort($arrayCltCancelDet);
                    foreach( $arrayCltCancelDet as $arraItemCltCancelDet )
                    {                       
                        $intContadorCltCancel ++;
                        $strModalCltCancel    .= '<div class="row" style="margin-top:5px; padding: 10px 10px;">
                                                    <div class="col-xs-0-5 " style="text-align: center;">
                                                        <h6><b>'.$intContadorCltCancel.'</b></h6>
                                                    </div>
                                                    <div class="col-lg-2 " style="text-align: center;">
                                                        <h6><b>'.$arraItemCltCancelDet['VENDEDOR'].'</b></h6>
                                                    </div>
                                                    <div class="col-lg-2" style="text-align: center;">
                                                        <h6><b>'.$arraItemCltCancelDet['CLIENTE'].'</b></h6>
                                                    </div>
                                                    <div class="col-lg-2 " style="text-align: center;">
                                                        <h6><b>'.$arraItemCltCancelDet['LOGIN'].'</b></h6>
                                                    </div>
                                                    <div class="col-lg-2" style="text-align: center;">
                                                        <h6><b>'.$arraItemCltCancelDet['DESCRIPCION_PRODUCTO'].'</b></h6>
                                                    </div>
                                                    <div class="col-lg-2 " style="text-align: center;">
                                                        <h6><b>'.$arraItemCltCancelDet['MOTIVO'].'</b></h6>
                                                    </div>                                                    
                                                    <div class="col-xs-1-5 " style="text-align: center;">
                                                        <h6><b>$'.$arraItemCltCancelDet['TOTAL'].'</b></h6>
                                                    </div>
                                                  </div>';
                    }
                }
            }//if( !empty($arrayCltCancelDet) )
            $arrayResponse['boolSuccess']     = true;
            $arrayResponse['strBodyModal']    = $strModalCltNuevo.$strModalCltFact.$strModalCltCancel;
            $arrayResponse['strMensajeError'] = '';            
        }
        catch( \Exception $e )
        {
            $serviceUtil->insertError('TELCOS+',
                                      'SeguridadBundle.DefaultController.getDetalleResultadosClientesAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        $objJsonResponse->setData($arrayResponse);
        return $objJsonResponse;        
    }
}
