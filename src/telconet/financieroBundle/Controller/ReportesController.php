<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Finder\Finder;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use \PHPExcel_Cell;
use \PHPExcel_Cell_DataType;
use \PHPExcel_Style_Fill;
use \PHPExcel_Style_Alignment;
use \PHPExcel_Style_Border;
use \PHPExcel_Worksheet_MemoryDrawing;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use telconet\schemaBundle\Entity\InfoReporteHistorial;

use telconet\financieroBundle\Service\ReportesService;

class ReportesController extends Controller
{
    public function estadoCuentaAction()
    {
		$request = $this->getRequest();
		$session=$request->getSession();
		$cliente=$session->get('cliente');
		$ptocliente=$session->get('ptoCliente');
		
		if($cliente)
			$parametro=array('cliente' => "S");
		else
			$parametro=array('cliente' => "N");
        return $this->render('financieroBundle:reportes:estadoCuenta.html.twig', $parametro);
    }
    
    public function listarClientesAction(){
        $request = $this->getRequest();
        $session=$request->getSession();
        $idEmpresa=$session->get('idEmpresa');
        $em = $this->get('doctrine')->getManager('telconet');
        $filter=$request->get("query");
        $arrayRolesPersona=array('CLIENTE');
        $arrayEstados=array('Eliminado','Inactivo','Pendiente','Anulado');
        $datos = $em->getRepository('schemaBundle:InfoPersona')
                 ->findListadoClientesPorEmpresaPorEstado($arrayEstados,$idEmpresa,$filter,$arrayRolesPersona);
        $i=1;
        foreach ($datos as $persona):            
            if($persona->getNombres()!="" && $persona->getApellidos()!="")
                $informacion_cliente=$persona->getNombres()." ".$persona->getApellidos();

            if($persona->getRazonSocial()!="")
                $informacion_cliente=$persona->getRazonSocial();
		
            $arreglo[]= array(
            'idcliente'=>$persona->getId(),
            'descripcion'=>$informacion_cliente,
            );              

            $i++;     
        endforeach;
        
        if (!empty($arreglo))
            $response = new Response(json_encode(array('clientes' => $arreglo)));
        else
        {
            $arreglo[]= array(
            'idcliente'=> "",
            'descripcion'=> "",
            );
            $response = new Response(json_encode(array('clientes' => $arreglo)));
        }		
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
	
    /**
     * listarPtosClientesAction
     * Metodo que devuelve los puntos de una persona
     * @author Andres Montero <amontero@telconet.ec>
     * @since 18/11/2014
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 05-09-2016 - Se modifica la función para que retorne información sólo si se escribe una cadena de caracteres similar al login que
     *                           del cliente que se desea buscar
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 19-07-2017 - Se corrige la validación para que retorne los logines respectivos cuando se envía el parámetro 'query', caso 
     *                           contrario que verifique si se ha enviado el parámetro 'idcliente'
     * @param string $punto
     * @param string $idEmpresa
     * @return array
     */     
    public function listarPtosClientesAction()
    {
        $objRequest           = $this->getRequest();
        $intIdcliente         = $objRequest->get("idcliente");
        $objSession           = $objRequest->getSession();
        $intIdEmpresa         = $objSession->get('idEmpresa');
        $strPuntoCliente      = $objRequest->get("query");
        $arrayEstadosCruce    = array('Eliminado','Anulado','migracion_ttco','null');
        $emComercial          = $this->get('doctrine')->getManager('telconet'); 
        $arrayResultado       = array();
        $arrayPuntosObtenidos = array();
        $objResponse          = new Response();
        $arrayPuntosClientes  = array();

        if( !empty($intIdcliente) )
        {
            $arrayResultado = $emComercial->getRepository('schemaBundle:InfoPunto')->findListarTodosPtosClientes($intIdcliente, $intIdEmpresa);
        }
        else
        {
            if ( !empty($strPuntoCliente) )
            {
                $arrayResultado = $emComercial->getRepository('schemaBundle:InfoPunto')->findListarPtosClientesCruce( $arrayEstadosCruce,
                                                                                                                      $intIdEmpresa,
                                                                                                                      $strPuntoCliente );
            }
        }

        if ( isset($arrayResultado['registros']) && !empty($arrayResultado['registros']) )
        {
            $arrayPuntosObtenidos = $arrayResultado['registros'];

            if( !empty($arrayPuntosObtenidos) )
            {
                foreach($arrayPuntosObtenidos as $objPunto)
                {
                    $arrayPuntosClientes[] = array( 'id_pto_cliente'  => $objPunto->getId(), 'descripcion_pto' => $objPunto->getLogin() );
                }
            }//( !empty($arrayDatos) )
        }//( !empty($arrayResultado['registros']) )

        if( empty($arrayPuntosClientes) )
        {    
            $arrayPuntosClientes[] = array( 'id_pto_cliente'=> "", 'descripcion_pto'=> "" );
        }
        
        $objResponse->setContent( json_encode( array('listado' => $arrayPuntosClientes) ) );
        $objResponse->headers->set('Content-type', 'text/json');
        
        return $objResponse;
    }
	
    /**
     * estadoCuentaPorClienteAction, obtiene el estado de cuenta del cliente
     * @version 1.1 22-10-2015
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @since 1.0
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 25-05-2016 - Se modifica la función para enviar el cliente y la empresa para buscar los puntos pertenecientes a un cliente
     *                           para calcular el estado de cuenta de un cliente
     * 
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.3 27-07-2016 - Se agrega la presentacion del saldo actual de la factura mediante la variable "saldoActual"
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 03-09-2016 - Se redondea la variable "saldoActual" para que muestre el saldo actual de la factura con dos decimales
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.5 22-09-2016 
     * Se verifica si un Anticipo por cruce se origino de un Anticipo al cual le aplicaron NDI por un valor menor 
     * al valor del Anticipo Original y si este Anticipo Original se encuentra Cerrado no sumarizara al Saldo Total y se marcara en el
     * estado de cuenta en otro color.
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.6 08-08-2017 - Se verifica si el pago tiene dependencia sobre otros documentos en la 'INFO_PAGO_HISTORIAL', se agrega bandera
     *                           $boolDocDependeDePago, en el caso de que sea True cambia el color de fondo de la fila del pago dependiente en el 
     *                           grid, si solo a las empresas que CONTABILIZA. Para ello se verifica los detalles del parámetro cabecera que es 
     *                           'PROCESO CONTABILIZACION EMPRESA' en la tabla 'DB_GENERAL.ADMI_PARAMETRO_DET' y se verifica la columna 'VALOR2' 
     *                           si está seteado con el valor de 'S'.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.5 05-10-2017 - Se agrega llamada a función del repositorio que trae la información necesaria para generar el estado de cuenta 
     *                           usando un procedimiento almacenado en la base de datos.
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.6 25-10-2017 - Se incluye al query del estado de cuenta del cliente la verificación si el pago tiene dependencia sobre otros 
     *                           documentos en la 'INFO_PAGO_HISTORIAL' y la obtención del saldo del documento: FAC o FACP con el objetivo de 
     *                           disminuir el tiempo de procesamiento a la vista.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.7 01-03-2018 - Se cambia Filtro de FeCreacion a FeEmision y se Agregan al Grid FeEmision, FeAutorizacion
     * 
     * @return Response retorna el grid con ele stado de cuenta
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.8 22-04-2020 - Se agrega validación que se consideren las  NDI con la característica de diferido deben 
     * visualizarse como documentos de origen de movimientos.
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.9 02-06-2020 - Se realizan cambios en el reporte por NDI agrupadas por pagos diferidos en emergencia sanitaria.
     */
    public function estadoCuentaPorClienteAction()
    {
        ini_set('max_execution_time', 9999999);
        
        $request    = $this->getRequest();
        $session    = $request->getSession();
        $cliente    = $session->get('cliente');
        $idOficina  = $session->get('idOficina');
        $idEmpresa  = $session->get('idEmpresa');
        $strUsuarioSesion = $session->get('user');
        
        $objDb               = $this->container->getParameter('database_dsn');
        $strUserFinanciero   = $this->container->getParameter('user_financiero');
        $strPasswdFinanciero = $this->container->getParameter('passwd_financiero');  
        $emComercial         = $this->get('doctrine')->getManager('telconet');
        
        $objOciCon      = oci_connect(
                                       $strUserFinanciero,
                                       $strPasswdFinanciero, 
                                       $objDb
                                     );        
        
        $objCursor       = oci_new_cursor($objOciCon);  

        //Para totalizado de anticipos pendientes
        $anticiposPendientes = 0;

        if($cliente)
        {
            $idcliente = $cliente['id'];
        }
        else
        {
            $idcliente = $request->get("idcliente");
        }
        
        $fechaDesde = explode('T', $request->get("fechaDesde"));
        $fechaHasta = explode('T', $request->get("fechaHasta"));
       
        //Se obtiene el listado de puntos
        $em = $this->get('doctrine')->getManager('telconet');
        
        $arrayParametros                  = array();
        $arrayParametros['strEmpresaCod'] = $idEmpresa;  
        $arrayParametros['intIdCliente']  = $idcliente;      
        $arrayParametros['oci_con']       = $objOciCon;
        $arrayParametros['cursor']        = $objCursor; 
        $arrayParametros['strFechaDesde'] = null;        
        $arrayParametros['strFechaHasta'] = null;         
       
        if((!$fechaDesde[0]) && (!$fechaHasta[0]))
        {
            $em_financiero  = $this->get('doctrine')->getManager('telconet_financiero');
            
            $resultado      = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getEstadoCuentaCliente($arrayParametros);
        }
        else
        {
            $em_financiero  = $this->get('doctrine')->getManager('telconet_financiero');
            $fechaDesde     = $fechaDesde[0];
            $fechaHasta     = $fechaHasta[0];

            $arrayFechaDesde                  = explode("-", $fechaDesde);
            $arrayParametros['strFechaDesde'] = $arrayFechaDesde[2] . "/" . $arrayFechaDesde[1] . "/" . $arrayFechaDesde[0];

            $arrayFechaHasta                  = explode("-", $fechaHasta);
            $objFechaAdd                      = strtotime(date("Y-m-d", strtotime($arrayFechaHasta[0] . "-" . $arrayFechaHasta[1] . "-" . 
                                                                                  $arrayFechaHasta[2])) . " +1 day");
            $arrayParametros['strFechaHasta'] = date("d/m/Y", $objFechaAdd);
            
            
            //Como es por fechas debo obtener la informacion previa para la sumatoria del saldo
            $resultado_sumatoria = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                ->findSumatoriaPorFechas($idOficina, $fechaDesde, $idEmpresa, $idcliente);
            
            $resultado      = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getEstadoCuentaCliente($arrayParametros);
        }
        
        $sumatoria           = 0;
        $listadoEstadoCuenta = $resultado['registros'];

        if($listadoEstadoCuenta)
        {

            $arreglo[] = $this->getArregloEnBlanco();
            $movimiento      = 0;
            $sumatoria_desde = 0;

            if(isset($resultado_sumatoria))
            {
                //Se define el arreglo para la sumatoria
                $listadoSumatoria = $resultado_sumatoria;
                foreach($listadoSumatoria as $listado_sumatoria)
                {
                    $movimiento = $listado_sumatoria['movimiento'];
                    if($movimiento == "+")
                    {
                        $sumatoria_desde+=round($listado_sumatoria['valorTotal'], 2);
                    }

                    if($movimiento == "-")
                    {
                        $sumatoria_desde-=round($listado_sumatoria['valorTotal'], 2);
                        if($listado_sumatoria['estadoImpresionFact'] == "Pendiente")
                            $anticiposPendientes+=round($listado_sumatoria['valorTotal'], 2);
                    }
                }

                $arrayTmp = $this->getArregloEnBlanco();
                $arrayTmp['acumulado']  = round($sumatoria_desde, 2);
                $arrayTmp['referencia'] = "Saldo anterior:";

                $arreglo[] = $arrayTmp;
            }

            $sumatoria = $sumatoria_desde;

            foreach($listadoEstadoCuenta as $listado)
            {
                if(!empty($listado))
                {
                    $p_cliente      = $em->getRepository('schemaBundle:InfoPunto')->find($listado['puntoId']);
                    $valor_ingreso  = "";
                    $valor_egreso   = "";
                    $tipoDocumento  = "";

                    $tipo           = $listado['movimiento'];
                    $tipoDocumento  = $listado['codigoTipoDocumento'];
                    $boolDocDependeDePago  = false;
                    $floatSaldoActDocumento= null;

                    /**
                    * Se verifica si un Anticipo por cruce se origino de un Anticipo al cual le aplicaron NDI por un valor menor 
                    * al valor del Anticipo Original y si este se encuentra Cerrado no sumarizara al Saldo Total y se marcara en el
                    * estado de cuenta en otro color.
                    * */                
                    $strEstado               = 'Cerrado';
                    $boolSumatoriaValorTotal = true;
                    $objAnticipoPorCruce     = $em_financiero->getRepository('schemaBundle:InfoPagoDet')
                                                             ->getAnticipoPorCrucePorPagoDetIdPorEstado
                                                               ($listado['id'],$tipoDocumento,$strEstado);
                    if($objAnticipoPorCruce)
                    {
                        $boolSumatoriaValorTotal = false;
                    }

                    if($tipo == "+")
                    {
                        $sumatoria      +=round($listado['valorTotal'], 2);
                        $valor_ingreso  = round($listado['valorTotal'], 2);
                    }

                    if($tipo == "-")
                    {
                        if($boolSumatoriaValorTotal)
                        {
                            $sumatoria -= round($listado['valorTotal'], 2);                       
                        }
                        else
                        {
                            $tipo = "";
                        }
                        $valor_egreso   = round($listado['valorTotal'], 2);
                    }
                    $em_oficina = $this->get('doctrine')->getManager();
                    $oficina    = $em_oficina->getRepository('schemaBundle:InfoOficinaGrupo')->find($listado['oficinaId']);

                    $numero = "";
                    $numero = $listado['numeroReferencia'];

                    if($numero == "")
                    {
                        if(isset($listado['numeroCuentaBanco']))
                        {
                            $numero = $listado['numeroCuentaBanco'];
                        }
                    }
                    $numero_factura_pagada = "";

                    if($tipoDocumento == "ND" || $tipoDocumento == "NDI" || $tipoDocumento == "DEV")
                    {
                        //Se debe buscar la referencia al pago para presentarla en el estado de cuenta
                        $objPago = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                            ->findPagoRelacionado($listado['id']);

                        if(!empty($objPago))
                        {
                            $numero_factura_pagada = $objPago[0]['numeroPago'];
                        }
                        else
                        {   
                            if ($tipoDocumento == "NDI")
                            {
                                $objCaracteristica                = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                                ->findOneBy(array('descripcionCaracteristica' => 'ID_REFERENCIA_NCI',
                                                                                                  'tipo'                      => 'FINANCIERO',
                                                                                                  'estado'                    => 'Activo' ));

                                $arrayInfoDocumentoCaracteristica = $em_financiero->getRepository('schemaBundle:InfoDocumentoCaracteristica')
                                                                                  ->findBy(array("documentoId"      => $listado['id'],
                                                                                                 "caracteristicaId" => $objCaracteristica->getId()));

                                foreach($arrayInfoDocumentoCaracteristica as $objInfoDocumentoCaracteristica)
                                {   
                                    if(is_numeric($objInfoDocumentoCaracteristica->getValor()))
                                    {
                                        $entityFactura  = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                        ->find($objInfoDocumentoCaracteristica->getValor());

                                        if($entityFactura)
                                        {
                                            $numero_factura_pagada = $numero_factura_pagada.$entityFactura->getNumeroFacturaSri()." ";
                                        }
                                    }
                                    
                                }
                                
                                if(empty($numero_factura_pagada))
                                {
                                    $numero_factura_pagada = "";
                                }
                            }
                            else
                            {
                                $numero_factura_pagada = "";
                            }
                        }
                    }
                    
                    if ( $listado['pagoTieneDependencia'] == 'S' )
                    {
                        $boolDocDependeDePago = true;
                    }

                    if(isset($listado['referenciaId']))
                    {
                        if($tipoDocumento == "PAG" || $tipoDocumento == "PAGC" || $tipoDocumento == "ANT" ||
                            $tipoDocumento == "ANTC" || $tipoDocumento == "ANTS" || $tipoDocumento == "NC" || $tipoDocumento == "NCI")
                        {
                            $num_fact = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($listado['referenciaId']);
                            $numero_factura_pagada = $num_fact->getNumeroFacturaSri();
                        }
                        else
                        {
                            $numero_factura_pagada = "";
                        }
                    }
                    else
                    {
                        //Sumatoria de pendiente y aplica para pagos | anticipos | anticipos por cruce
                        if($tipoDocumento == "PAG" || $tipoDocumento == "PAGC" || $tipoDocumento == "ANT" || $tipoDocumento == "ANTC" 
                            || $tipoDocumento == "ANTS")
                        {      
                            //Se debe buscar la referencia al Anticipo para presentarla en el estado de cuenta 
                            $objAnticipoOrig = $em_financiero->getRepository('schemaBundle:InfoPagoCab')->find(intval($listado['refAnticipoId']));

                            if(!empty($objAnticipoOrig))
                            {
                                $numero_factura_pagada = $objAnticipoOrig->getNumeroPago();
                            }
                            else
                            {
                                $numero_factura_pagada = "";
                            }
                            //Se agrega condicion para que solo sumarice en el RESUMEN DEL CLIENTE, Anticipos en estado pendiente
                            if($listado['estadoImpresionFact'] == "Pendiente")
                            {
                                $anticiposPendientes+=round($listado['valorTotal'], 2);
                            }
                        }
                    }
                    
                    $floatSaldoActDocumento = $listado['saldoActualDocumento'];

                    $arreglo[] = array(
                        'documento'               => $listado['numeroFacturaSri'],
                        'valor_ingreso'           => round($valor_ingreso, 2),
                        'valor_egreso'            => round($valor_egreso, 2),
                        'acumulado'               => round($sumatoria, 2),
                        'Fecreacion'              => $listado['feCreacion'],
                        'strFeEmision'            => $listado['strFeEmision'],
                        'strFeAutorizacion'       => $listado['strFeAutorizacion'],
                        'tipoDocumento'           => $tipoDocumento,
                        'punto'                   => $p_cliente->getLogin(),
                        'oficina'                 => $oficina->getNombreOficina(),
                        'referencia'              => $numero_factura_pagada,
                        'formaPago'               => $listado['codigoFormaPago'],
                        'numero'                  => $numero,
                        'movimiento'              => $tipo,
                        'saldoActual'             => $floatSaldoActDocumento,
                        'boolSumatoriaValorTotal' => $boolSumatoriaValorTotal,
                        'boolDocDependeDePago'    => $boolDocDependeDePago,
                    );
                }
            }
        }

        $arreglo[] = $this->getArregloEnBlanco();
        //Termina de escribir todo envio en blanco
        $arrayTmp = $this->getArregloEnBlanco();
        $arrayTmp['documento'] = "RESUMEN DEL CLIENTE:";
        $arreglo[] = $arrayTmp;

        $arrayTmp = $this->getArregloEnBlanco();
        $arrayTmp['documento'] = "Saldo:";
        $arrayTmp['acumulado'] = round($sumatoria, 2);
        $arreglo[] = $arrayTmp;

        $arrayTmp = $this->getArregloEnBlanco();
        $arrayTmp['documento'] = "Anticipos pendientes:";
        $arrayTmp['acumulado'] = round($anticiposPendientes, 2);
        $arreglo[] = $arrayTmp;

        $arrayTmp = $this->getArregloEnBlanco();
        $arrayTmp['documento'] = "Saldo Final:";
        $arrayTmp['acumulado'] = round($sumatoria, 2);
        $arreglo[] = $arrayTmp;
        
        if(empty($arreglo))
        {
            $arrayTmp = $this->getArregloEnBlanco();
        }

        $response = new Response(json_encode(array('documentos' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
	
    /**
     * getArregloEnBlanco, se genera el arreglo en blanco para data especifica
     * @version 1.0 22-10-2015 Gina Villalba <gvillalba@tleconet.ec>
     * @return Response retorna el grid con ele stado de cuenta
     */
    public function getArregloEnBlanco()
    {
        return array(
                'documento'     => "",
                'valor_ingreso' => "",
                'valor_egreso'  => "",
                'acumulado'     => "",
                'Fecreacion'    => "",
                'tipoDocumento' => "",
                'punto'         => "",
                'oficina'       => "",
                'referencia'    => "",
                'formaPago'     => "",
                'numero'        => "",
                'movimiento'    => ""
            );
    }
    
    /**
     * estadoCuentaPorPtoClienteAction, obtiene el estado de cuenta del cliente
     * @version 1.1 17-12-2014 Alexander Samaniego <awsamaniego@telconet.ec>
     * @since 1.0
     * @version 1.2 10-02-2015 Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.3 07-06-2016 Gina Villalba <gvillalba@telconet.ec>
     * Se modifica la presentacion de fecha, numeros de referencia
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 11-09-2016 - Se modifica para que en el estado de cuenta por punto cliente se muestre la NDI en estado 'Activo' que no esté
     *                           asociado a un pago.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 15-09-2016 - Se modifica para que no aparezcan los pagos asociados a una NDI no aparezcan, si la misma se encuentra asociada a un
     *                           movimiento de factura.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.6 21-09-2016 
     * Se verifica si un Anticipo por cruce se origino de un Anticipo al cual le aplicaron NDI por un valor menor 
     * al valor del Anticipo Original y si este Anticipo Original se encuentra Cerrado no sumarizara al Saldo Total y se marcara en el
     * estado de cuenta en otro color.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.7 03-05-2017 - Se agrega validación en seteo de variable que muestra la observación del documento en el estado de cuenta. 
     * @return Response retorna el grid con el estado de cuenta
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.8 01-03-2018 - Se cambia Filtro de FeCreacion a FeEmision y se Agregan al Grid FeEmision, FeAutorizacion     
     *
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.9 22-04-2020 - Se agrega validación que se consideren las  NDI con la característica de diferido deben 
     *  visualizarse como documentos de origen de movimientos.
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 2.0 02-06-2020 - Se realizan cambios en el reporte por NDI agrupadas por pagos diferidos en emergencia sanitaria.
     * 
     * @author Arcángel Farro <lfarro@telconet.ec>  Se realiza modificación para que muestre la observación del detalle del estado de cuenta.
     * @version 2.1 12-05-2023
     * 
     */
    public function estadoCuentaPorPtoClienteAction()
    {
        $objRequest                             = $this->getRequest();
        $objSession                             = $objRequest->getSession();
        // Defino esquema General
        $emGeneral                              = $this->getDoctrine()->getManager("telconet_general");
        // Recupero el ID de la empresa en sesión
        $intIdEmpresa                           = $objSession->get('idEmpresa');
        $arrayCliente                           = $objSession->get('cliente');
        $arrayPtoCliente                        = $objSession->get('ptoCliente');
        $intIdOficina                           = $objSession->get('idOficina');
        $arrayFechaDesde                        = explode('T', $objRequest->get("fechaDesde"));
        $arrayFechaHasta                        = explode('T', $objRequest->get("fechaHasta"));
        $intIdCliente                           = $objRequest->get("idcliente");
        $intIdPtoCliente                        = $objRequest->get("id_pto_cliente");
        $arrayParametros['user_financiero']     = $this->container->getParameter('user_financiero');
        $arrayParametros['passwd_financiero']   = $this->container->getParameter('passwd_financiero');
        $arrayParametros['database_dsn']        = $this->container->getParameter('database_dsn');
        $em                                     = $this->get('doctrine')->getManager('telconet');
        $em_financiero                          = $this->get('doctrine')->getManager('telconet_financiero');
        $em_oficina                             = $this->get('doctrine')->getManager();        
        $intTotalFacturas                       = 0;
        $emComercial                            = $this->getDoctrine()->getManager('telconet');

        // AGREGO PREFIJO DE EMPRESA
        $strPrefijoEmpresa                      = $objSession->get('prefijoEmpresa'); 

        $arrayEmpresas  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('EMPRESA_COD_VALIDACION_EST_CTA_PTO',
                                                            'FINANCIERO',
                                                            null,
                                                            null,
                                                            null,
                                                            null,
                                                            null,
                                                            null,
                                                            null,
                                                            $intIdEmpresa);


        //si el de la session existe los sobreescribe al actual
        if( !empty($arrayCliente) && !empty($arrayPtoCliente) )
        {
            $intIdCliente       = $arrayCliente['id'];
            $intIdPtoCliente    = $arrayPtoCliente['id'];
        }

        if(empty($intIdPtoCliente) || (!$arrayFechaDesde[0]) && (!$arrayFechaHasta[0]))
        {
            if(empty($intIdPtoCliente))
            {
                //debo obtener el listado de puntos
                $entityInfoPunto    = $em->getRepository('schemaBundle:InfoPunto')->findListarPtosClientes($intIdCliente);
                $arrayInfoPunto     = $entityInfoPunto['registros'];
                $intTotalPuntos     = $entityInfoPunto['total'];
                //armando el string de los ptos
                $strPuntosConcatenados = "";
                $intCounter = 1;

                foreach($arrayInfoPunto as $arrayInfoPunto)
                {
                    $strPuntosConcatenados.=$arrayInfoPunto['id'];
                    if($intCounter < $intTotalPuntos)
                    {
                        $strPuntosConcatenados.=",";
                    }
                    $intCounter++;
                }
            }
            else
            {
                $strPuntosConcatenados = $intIdPtoCliente;
            }
        }
        else
        {
            $strPuntosConcatenados = $intIdPtoCliente;
        }

        /* Se obtiene:
         * - Saldo de migracion para el caso de TTCO, que no tienen referencia */

        if((!$arrayFechaDesde[0]) && (!$arrayFechaHasta[0]))
        {
            /*
             * Cuando no hay fecha, se carga por defecto el anio actual
             * Proceso:
             * - Se debe obtener la fecha inicial del anio vigente
             * - Se debe pasar por parametro la fecha para el calculo de saldos
             * - Se debe imprimir los saldos en el json
             * - Se debe mandar la fecha al query de las facturas, para que me liste facturas >= a esa fecha
             * */
            
            $strFechaDesde                      = date("Y") . "-01-01";
            $strFechaHasta                      = "";
            $arrayInfoDocumentoFinancieroCab    = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                ->findEstadoDeCuenta($intIdOficina, 
                                                                                     $strFechaDesde, 
                                                                                     $strFechaHasta, 
                                                                                     $strPuntosConcatenados);
            $arrayInfoDocFinCabAntPgPendientes  = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                ->findAnticiposEstadoDeCuenta($intIdOficina, 
                                                                                              $strFechaDesde, 
                                                                                              $strFechaHasta, 
                                                                                              $strPuntosConcatenados, 
                                                                                              "Pendiente");
            $arrayInfoDocFinCabOG               = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                ->findEstadoDeCuentaOG($intIdOficina, 
                                                                                       $strFechaDesde, 
                                                                                       $strFechaHasta, 
                                                                                       $strPuntosConcatenados);
            $arrayInfoPagoCabAntAsig            = $em_financiero->getRepository('schemaBundle:InfoPagoCab')
                                                                ->obtenerAnticiposAsignados("Asignado", 
                                                                                            $strPuntosConcatenados, 
                                                                                            $strFechaDesde, 
                                                                                            $strFechaHasta);
        }
        else
        {
            $strFechaDesde                      = $arrayFechaDesde[0];
            $strFechaHasta                      = $arrayFechaHasta[0];
            $arrayInfoDocumentoFinancieroCab    = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                ->findEstadoDeCuenta($intIdOficina,
                                                                                     $strFechaDesde,
                                                                                     $strFechaHasta,
                                                                                     $strPuntosConcatenados);
            $arrayInfoDocFinCabAntPgPendientes  = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                ->findAnticiposEstadoDeCuenta($intIdOficina, 
                                                                                              $strFechaDesde, 
                                                                                              $strFechaHasta, 
                                                                                              $strPuntosConcatenados, 
                                                                                              "Pendiente");
            $arrayInfoPagoCabAntAsig            = $em_financiero->getRepository('schemaBundle:InfoPagoCab')
                                                                ->obtenerAnticiposAsignados("Asignado", 
                                                                                            $strPuntosConcatenados, 
                                                                                            $strFechaDesde, 
                                                                                            $strFechaHasta);
            $arrayInfoDocFinCabOG               = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                ->findEstadoDeCuentaOG($intIdOficina,
                                                                                       $strFechaDesde, 
                                                                                       $strFechaHasta, 
                                                                                       $strPuntosConcatenados);
        }
        $intSumatoriaValorTotal     = 0;
        $arrayListadoEstadoCuenta   = $arrayInfoDocumentoFinancieroCab['registros'];
        $arrayAntPgPendientes       = $arrayInfoDocFinCabAntPgPendientes['registros'];
        $arrayAntAsig               = $arrayInfoPagoCabAntAsig['registros'];
        $arrayListadoMigracion      = $arrayInfoDocFinCabOG['registros'];

        if( !empty($arrayListadoMigracion) )
        {
            foreach($arrayListadoMigracion as $arrayListadoMigracion)
            {
                $entityInfoPunto    = $em->getRepository('schemaBundle:InfoPunto')->find($arrayListadoMigracion['puntoId']);
                $intValorIngreso    = "";
                $intValorEgreso     = "";
                $strTipoDocumento   = "";
                if($arrayListadoMigracion['tipoDocumentoId'] != "")
                {
                    $entityAdmiTipoDocumentoFinanciero = $em_financiero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                                       ->find($arrayListadoMigracion['tipoDocumentoId']);
                    $strTipoDocumento = $entityAdmiTipoDocumentoFinanciero->getCodigoTipoDocumento();
                }
                else
                {
                    $strTipoDocumento = "";
                }

                if($entityAdmiTipoDocumentoFinanciero->getMovimiento() == "+")
                {
                    $intSumatoriaValorTotal +=  $arrayListadoMigracion['valorTotal'];
                    $intValorIngreso        = $arrayListadoMigracion['valorTotal'];
                }

                if($entityAdmiTipoDocumentoFinanciero->getMovimiento() == "-")
                {
                    $intSumatoriaValorTotal -=  $arrayListadoMigracion['valorTotal'];
                    $intValorEgreso         = $arrayListadoMigracion['valorTotal'];
                }                
                $entityInfoOficinaGrupo = $em_oficina->getRepository('schemaBundle:InfoOficinaGrupo')->find($arrayListadoMigracion['oficinaId']);

                $strNumeroRefCtaBnco = "";
                $strNumeroRefCtaBnco = $arrayListadoMigracion['numeroReferencia'];

                if($strNumeroRefCtaBnco == "")
                {
                    if(isset($arrayListadoMigracion['numeroCuentaBanco']))
                    {
                        $strNumeroRefCtaBnco = $arrayListadoMigracion['numeroCuentaBanco'];
                    }
                }

                $strNumeroFactPagada = "";

                if(isset($arrayListadoMigracion['referenciaId']))
                {
                    if($strTipoDocumento == "PAG" || $strTipoDocumento == "PAGC" || $strTipoDocumento == "ANT" ||
                        $strTipoDocumento == "ANTC" || $strTipoDocumento == "ANTS" || $strTipoDocumento == "NC")
                    {
                        //echo $arrayListadoMigracion['referenciaId']."-";
                        if(isset($arrayListadoMigracion['referenciaId']))
                        {
                            $entityInfoDocumentoFinancieroCab = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                              ->find($arrayListadoMigracion['referenciaId']);
                            if(isset($entityInfoDocumentoFinancieroCab))
                            {
                                $strNumeroFactPagada = $entityInfoDocumentoFinancieroCab->getNumeroFacturaSri();
                            }
                        }
                        else
                        {
                            $strNumeroFactPagada = "";
                        }
                    }
                    else
                    {
                        $strNumeroFactPagada = "";
                    }
                }


                $arrayContenedorResultados[] = array(
                    'documento'               => $arrayListadoMigracion['numeroFacturaSri'],
                    'valor_ingreso'           => $intValorIngreso,
                    'valor_egreso'            => $intValorEgreso,
                    'acumulado'               => round($intSumatoriaValorTotal, 2),
                    'Fecreacion'              => strval(date_format($arrayListadoMigracion['feCreacion'], "d/m/Y")),
                    'strFeEmision'            => '',
                    'strFeAutorizacion'       => '',
                    'tipoDocumento'           => $strTipoDocumento,
                    'punto'                   => $entityInfoPunto->getLogin(),
                    'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                    'referencia'              => $strNumeroFactPagada,
                    'formaPago'               => $arrayListadoMigracion['codigoFormaPago'],
                    'numero'                  => $strNumeroRefCtaBnco,
                    'observacion'             => $strNumeroRefCtaBnco,
                    'boolSumatoriaValorTotal' => true
                );
            }
        }

        $intSumatoriaTotalMigracion = $intSumatoriaValorTotal;

        $arrayContenedorResultados[] = array(
            'documento'               => "",
            'valor_ingreso'           => "",
            'valor_egreso'            => "",
            'acumulado'               => "",
            'Fecreacion'              => "",
            'strFeEmision'            => "",
            'strFeAutorizacion'       => "",
            'tipoDocumento'           => "",
            'punto'                   => "",
            'oficina'                 => "",
            'referencia'              => "",
            'boolSumatoriaValorTotal' => true
        );

        $arrayContenedorResultados[] = array(
            'documento'               => "MOVIMIENTOS",
            'valor_ingreso'           => "",
            'valor_egreso'            => "",
            'acumulado'               => "",
            'Fecreacion'              => "",
            'strFeEmision'            => "",
            'strFeAutorizacion'       => "",
            'tipoDocumento'           => "",
            'punto'                   => "",
            'oficina'                 => "",
            'referencia'              => "",
            'boolSumatoriaValorTotal' => true
        );

        $intValorIngreso        = 0;
        $intValorEgreso         = 0;
        $intSumatoriaValorTotal = 0;

        if( !empty($arrayListadoEstadoCuenta) )
        {
            foreach($arrayListadoEstadoCuenta as $arrayListadoEstadoCuenta)
            {
                $intValorIngresoDoc = 0;
                $intValorEgresoDoc  = 0;
                $boolContinuarFlujo = true;
                $strObservacionInfoFinDocDet = "";
                
                /**
                 * Bloque que agrega las NDI al estado de cuenta SOLO SI no tiene asociado un PAGO_DET_ID en el detalle de la factura. Es decir,
                 * se verifica que el campo PAGO_DET_ID de la tabla DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET esté en NULL.
                 */
                if( $arrayListadoEstadoCuenta['codigoTipoDocumento'] == "NDI" )
                {
                    $objInfoDocumentoFinancieroDet   = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                                                     ->findOneByDocumentoId($arrayListadoEstadoCuenta['id']);
                    
                    $objCaracteristica              = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                  ->findOneBy(array('descripcionCaracteristica' => 'PROCESO_DIFERIDO',
                                                                                    'tipo'                      => 'FINANCIERO',
                                                                                    'estado'                    => 'Activo' ));

                    $objInfoDocumentoCaracteristica = $em_financiero->getRepository('schemaBundle:InfoDocumentoCaracteristica')
                                                                    ->findOneBy(array("documentoId"      => $arrayListadoEstadoCuenta['id'],
                                                                                      "caracteristicaId" => $objCaracteristica->getId()));
                    if(!is_object($objInfoDocumentoCaracteristica))
                    {
                        if( $objInfoDocumentoFinancieroDet != null )
                        {
                            $intIdPagoDet = $objInfoDocumentoFinancieroDet->getPagoDetId() ? $objInfoDocumentoFinancieroDet->getPagoDetId() : 0;

                            if( $intIdPagoDet > 0 )
                            {
                                $boolContinuarFlujo = false;
                            }//( $intIdPagoDet > 0 )
                        }//( $objInfoDocumentoFinancieroDet != null )
                        else
                        {
                            $boolContinuarFlujo = false;
                        }
                    }
                }//( $arrayListadoEstadoCuenta['codigoTipoDocumento'] == "NDI" )
                
                
                if( $boolContinuarFlujo )
                {
                    $entityInfoPunto    = $em->getRepository('schemaBundle:InfoPunto')->find($arrayListadoEstadoCuenta['puntoId']);
                    $strTipoDocumento   = "";

                    if($arrayListadoEstadoCuenta['tipoDocumentoId'] != "")
                    {
                        $entityAdmiTipoDocumentoFinanciero  = $em_financiero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                                            ->find($arrayListadoEstadoCuenta['tipoDocumentoId']);
                        $strTipoDocumento                   = $entityAdmiTipoDocumentoFinanciero->getCodigoTipoDocumento();
                    }
                    else
                    {
                        $strTipoDocumento = "";
                    }

                    if($entityAdmiTipoDocumentoFinanciero->getMovimiento() == "+")
                    {
                        $intSumatoriaValorTotal     +=  round($arrayListadoEstadoCuenta['valorTotal'], 2);
                        $intValorIngreso            +=  round($arrayListadoEstadoCuenta['valorTotal'], 2);
                        $intTotalFacturas           +=  round($arrayListadoEstadoCuenta['valorTotal'], 2);
                    }
                    
                    $entityInfoOficinaGrupo = $em_oficina->getRepository('schemaBundle:InfoOficinaGrupo')
                                                         ->find($arrayListadoEstadoCuenta['oficinaId']);

                    $strNumeroRefCtaBnco = "";
                    $strNumeroRefCtaBnco = $arrayListadoEstadoCuenta['numeroReferencia'];

                    if($strNumeroRefCtaBnco == "")
                    {
                        if(isset($arrayListadoEstadoCuenta['numeroCuentaBanco']))
                            $strNumeroRefCtaBnco = $arrayListadoEstadoCuenta['numeroCuentaBanco'];
                    }

                    $strNumeroFactPagada = "";

                    // AGREGO VALIDACION PARA PREFIJO EMPRESA
                    foreach( $arrayEmpresas as $codEmpresa )
                    {
                        if ( $strPrefijoEmpresa == $codEmpresa['valor1'] )
                        {
                   
                           $entityInfoDocumentoFinancieroDet = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                                                             ->obtieneDatosDocumento($arrayListadoEstadoCuenta['id']);
                                               
                        } 
                        else
                        {
                           $entityInfoDocumentoFinancieroDet = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                                                             ->findByDocumentoId($arrayListadoEstadoCuenta['id']);
                        }
                    }
                                      
                    foreach( $arrayEmpresas as $codEmpresa )
                    {
                        if ( $strPrefijoEmpresa == $codEmpresa['valor1'] )
                        {
                            foreach($entityInfoDocumentoFinancieroDet as $entityInfoDocumentoFinancieroDet)
                            {
                                $strObservacionDetalleFact = $entityInfoDocumentoFinancieroDet['observacionesFacturaDetalle'];
                                
                                if(!empty($strObservacionDetalleFact))
                                {
                                   $strObservacionInfoFinDocDet = preg_replace( '([^A-Za-z0-9,-./])', 
                                                                                ' ', 
                                                                                $entityInfoDocumentoFinancieroDet['observacionesFacturaDetalle']);
                                    break;
                                }
                            }
                        }
                        else
                        {
                            foreach($entityInfoDocumentoFinancieroDet as $entityInfoDocumentoFinancieroDet)
                            {
                                $strObservacionDetalleFact = $entityInfoDocumentoFinancieroDet->getObservacionesFacturaDetalle();
                                
                                if(!empty($strObservacionDetalleFact))
                                {
                                    $strObservacionInfoFinDocDet = preg_replace('([^A-Za-z0-9,-./])', 
                                                                                ' ', 
                                                                                $entityInfoDocumentoFinancieroDet->getObservacionesFacturaDetalle());
                                    break;
                                }
                            }
                        }
                    }

                    //Por cada factura busco sus pagos
                    $arrayParametros['intIdDocumento'] = $arrayListadoEstadoCuenta['id'];
                    
                    $cursorResult = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                  ->getDocumentosRelacionados($arrayParametros);
                    
                    if( !empty($cursorResult) )
                    {
                        /**
                         * Bloque que agrega las NDI al estado de cuenta SOLO SI no tiene asociado un PAGO_DET_ID en el detalle de la factura. Es 
                         * decir, se verifica que el campo PAGO_DET_ID de la tabla DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET esté en NULL.
                         */
                        if( $arrayListadoEstadoCuenta['codigoTipoDocumento'] == "NDI" )
                        {
                            $strFeCreacion      = strval(date_format($arrayListadoEstadoCuenta['feCreacion'], "d/m/Y"));
                            $intValorIngresoDoc += $arrayListadoEstadoCuenta['valorTotal'];

                            $arrayContenedorResultados[] = array( 'documento'               => $arrayListadoEstadoCuenta['numeroFacturaSri'],
                                                                  'valor_ingreso'           => round($arrayListadoEstadoCuenta['valorTotal'], 2),
                                                                  'valor_egreso'            => "0.00",
                                                                  'acumulado'               => "",
                                                                  'Fecreacion'              => $strFeCreacion,
                                                                  'strFeEmision'            => $arrayListadoEstadoCuenta['fecEmision'],
                                                                  'strFeAutorizacion'       => $arrayListadoEstadoCuenta['fecAutorizacion'],
                                                                  'tipoDocumento'           => $strTipoDocumento,
                                                                  'punto'                   => $entityInfoPunto->getLogin(),
                                                                  'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                                                                  'referencia'              => $strNumeroFactPagada,
                                                                  'formaPago'               => $arrayListadoEstadoCuenta['codigoFormaPago'],
                                                                  'numero'                  => $strNumeroRefCtaBnco,
                                                                  'observacion'             => str_replace('cuota' ,
                                                                                                           '<font color="000000"><b>CUOTA</b>'
                                                                                                           . '</font>',
                                                                                                           $strObservacionInfoFinDocDet),
                                                                  'boolSumatoriaValorTotal' => true);

                        }//( $arrayListadoEstadoCuenta['codigoTipoDocumento'] == "NDI" )
                    
                        if($arrayListadoEstadoCuenta['codigoTipoDocumento'] == "FAC" || $arrayListadoEstadoCuenta['codigoTipoDocumento'] == "FACP")
                        {
                            $intValorIngresoDoc+=$arrayListadoEstadoCuenta['valorTotal'];

                            $arrayContenedorResultados[] = array(
                                'documento'               => $arrayListadoEstadoCuenta['numeroFacturaSri'],
                                'valor_ingreso'           => round($arrayListadoEstadoCuenta['valorTotal'], 2),
                                'valor_egreso'            => "0.00",
                                'acumulado'               => "",
                                'Fecreacion'              => strval(date_format($arrayListadoEstadoCuenta['feCreacion'], "d/m/Y")),
                                'strFeEmision'            => $arrayListadoEstadoCuenta['fecEmision'],
                                'strFeAutorizacion'       => $arrayListadoEstadoCuenta['fecAutorizacion'],
                                'tipoDocumento'           => $strTipoDocumento,
                                'punto'                   => $entityInfoPunto->getLogin(),
                                'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                                'referencia'              => $strNumeroFactPagada,
                                'formaPago'               => $arrayListadoEstadoCuenta['codigoFormaPago'],
                                'numero'                  => $strNumeroRefCtaBnco,
                                'observacion'             => $strObservacionInfoFinDocDet,
                                'boolSumatoriaValorTotal' => true
                            );
                        }

                        while( ($arrayDocumentosRelacionados = oci_fetch_array($cursorResult, OCI_ASSOC + OCI_RETURN_NULLS)) )
                        {

                            if($arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'PAG' || 
                                $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'PAGC' || 
                                $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'ANT' || 
                                $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'ANTC' ||
                                $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'ANTS')
                            {

                                /**
                                 * Se verifica si un Anticipo por cruce se origino de un Anticipo al cual le aplicaron NDI por un valor menor 
                                 * al valor del Anticipo Original y si este se encuentra Cerrado no sumarizara al Saldo Total y se marcara en el
                                 * estado de cuenta en otro color.
                                 * */                
                                 $strEstado               = 'Cerrado';
                                 $boolSumatoriaValorTotal = true;
                                 $objAnticipoPorCruce     = $em_financiero->getRepository('schemaBundle:InfoPagoDet')
                                                                          ->getAnticipoPorCrucePorPagoDetIdPorEstado
                                                                            ($arrayDocumentosRelacionados['ID_PAGO_DET'],
                                                                             $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'],$strEstado);
                                 if( !empty($objAnticipoPorCruce) )
                                 {
                                    $boolSumatoriaValorTotal = false;
                                 }

                                $intValorEgresoDoc      +=  round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                                if($boolSumatoriaValorTotal)
                                {
                                    $intSumatoriaValorTotal -=  round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                                    $intValorEgreso         +=  round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                                }
                                $entityInfoPagoDet = $em_financiero->getRepository('schemaBundle:InfoPagoDet')
                                                                   ->findOneById($arrayDocumentosRelacionados['ID_PAGO_DET']);

                                $arrayContenedorResultados[] = array(
                                    'documento'               => $arrayDocumentosRelacionados['NUMERO_PAGO'],
                                    'valor_ingreso'           => "0.00",
                                    'valor_egreso'            => round($arrayDocumentosRelacionados['VALOR_PAGO'], 2),
                                    'acumulado'               => "",
                                    'Fecreacion'              => $arrayDocumentosRelacionados['FE_CREACION'],
                                    'strFeEmision'            => "",
                                    'strFeAutorizacion'       => "",
                                    'tipoDocumento'           => $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'],
                                    'punto'                   => $entityInfoPunto->getLogin(),
                                    'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                                    'referencia'              => "",
                                    'formaPago'               => $arrayDocumentosRelacionados['CODIGO_FORMA_PAGO'],
                                    'numero'                  => $arrayDocumentosRelacionados['NUMERO_REFERENCIA'],
                                    'observacion'             => $entityInfoPagoDet->getComentario(),
                                    'boolSumatoriaValorTotal' => $boolSumatoriaValorTotal
                                );
                            }

                            //Me devuelve todo el listado de documentos
                            if($arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'ND' || 
                                $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'NDI' ||
                                $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'DEV')
                            {
                                $intValorIngresoDoc     +=  round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                                $intSumatoriaValorTotal +=  round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                                $intValorIngreso        +=  round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);

                                $entityInfoDocFinDet = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                                                     ->findByDocumentoId($arrayDocumentosRelacionados['ID_PAGO_DET']);
                                foreach($entityInfoDocFinDet as $entityInfoDocFinDet)
                                {
                                    $strObservacion = preg_replace('([^A-Za-z0-9])', ' ', $entityInfoDocFinDet->getObservacionesFacturaDetalle());
                                }

                                $arrayContenedorResultados[] = array(
                                    'documento'               => $arrayDocumentosRelacionados['NUMERO_PAGO'],
                                    'valor_ingreso'           => round($arrayDocumentosRelacionados['VALOR_PAGO'], 2),
                                    'valor_egreso'            => "0.00",
                                    'acumulado'               => "",
                                    'Fecreacion'              => $arrayDocumentosRelacionados['FE_CREACION'],
                                    'strFeEmision'            => "",
                                    'strFeAutorizacion'       => "",
                                    'tipoDocumento'           => $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'],
                                    'punto'                   => $entityInfoPunto->getLogin(),
                                    'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                                    'referencia'              => "",
                                    'formaPago'               => "",
                                    'numero'                  => "",
                                    'observacion'             => $strObservacion,
                                    'boolSumatoriaValorTotal' => true
                                );

                                $arrayParametros['intIdDocumento']=$arrayDocumentosRelacionados['ID_PAGO_DET'];

                                $this->obtenerDetalleNotasDebito($arrayContenedorResultados, 
                                                                 $intValorIngresoDoc, 
                                                                 $intValorIngreso, 
                                                                 $intSumatoriaValorTotal, 
                                                                 $intValorEgresoDoc, 
                                                                 $intValorEgreso, 
                                                                 $entityInfoPunto, 
                                                                 $entityInfoOficinaGrupo, 
                                                                 $em_financiero,
                                                                 $arrayParametros
                                                                );
                            }

                            if($arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'NC' ||
                                $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'NCI')
                            {

                                $intSumatoriaValorTotal -= round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                                $intValorEgreso         += round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                                $intValorEgresoDoc      += round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                                $arrayParametrosCab     =  array( "referenciaDocumentoId" => $arrayListadoEstadoCuenta['id'], 
                                                                  "estadoImpresionFact"   => "Activo",
                                                                  "numeroFacturaSri"      => $arrayDocumentosRelacionados['NUMERO_PAGO'] );
                                $entityInfoDocFinCab    =  $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                         ->findOneBy( $arrayParametrosCab );

                                if($entityInfoDocFinCab)
                                    $observacion= $entityInfoDocFinCab->getObservacion();
                                else
                                    $observacion= "";

                                $arrayContenedorResultados[] = array(
                                    'documento'               => $arrayDocumentosRelacionados['NUMERO_PAGO'],
                                    'valor_ingreso'           => "0.00",
                                    'valor_egreso'            => round($arrayDocumentosRelacionados['VALOR_PAGO'], 2),
                                    'acumulado'               => "",
                                    'Fecreacion'              => $arrayDocumentosRelacionados['FE_CREACION'],
                                    'strFeEmision'            => "",
                                    'strFeAutorizacion'       => "",
                                    'tipoDocumento'           => $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'],
                                    'punto'                   => $entityInfoPunto->getLogin(),
                                    'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                                    'referencia'              => "",
                                    'formaPago'               => "",
                                    'numero'                  => "",
                                    'observacion'             => $observacion,
                                    'boolSumatoriaValorTotal' => true
                                );
                            }
                        }
                        //Envio el totalizado
                        $arrayContenedorResultados[] = array(
                            'documento'               => "Total:",
                            'valor_ingreso'           => round($intValorIngresoDoc, 2),
                            'valor_egreso'            => round($intValorEgresoDoc, 2),
                            'acumulado'               => round($intValorIngresoDoc - $intValorEgresoDoc, 2),
                            'Fecreacion'              => "",
                            'strFeEmision'            => "",
                            'strFeAutorizacion'       => "",
                            'tipoDocumento'           => "",
                            'punto'                   => "",
                            'oficina'                 => "",
                            'referencia'              => "",
                            'boolSumatoriaValorTotal' => true
                        );

                        $intValorIngresoDoc = 0;
                        $intValorEgresoDoc  = 0;

                        //Termina de escribir todo envio en blanco
                        $arrayContenedorResultados[] = array(
                            'documento'               => "",
                            'valor_ingreso'           => "",
                            'valor_egreso'            => "",
                            'acumulado'               => "",
                            'Fecreacion'              => "",
                            'strFeEmision'            => "",
                            'strFeAutorizacion'       => "",
                            'tipoDocumento'           => "",
                            'punto'                   => "",
                            'oficina'                 => "",
                            'referencia'              => "",
                            'boolSumatoriaValorTotal' => true
                        );
                    }//($cursorResult)
                }//( $boolContinuarFlujo )
            }
        }

        if( !empty($arrayAntPgPendientes) )
        {
            $intSumAntPgPendiente   = 0;
            $intValorAntPg          = 0;

            //Termina de escribir todo envio en blanco
            $arrayContenedorResultados[] = array(
                'documento'               => "Anticipos no aplicados",
                'valor_ingreso'           => "",
                'valor_egreso'            => "",
                'acumulado'               => "",
                'Fecreacion'              => "",
                'strFeEmision'            => "",
                'strFeAutorizacion'       => "",
                'tipoDocumento'           => "",
                'punto'                   => "",
                'oficina'                 => "",
                'referencia'              => "",
                'boolSumatoriaValorTotal' => true
            );

            $intTotalAntPgPendientes    = 0;
            $intValorIngresoDoc         = 0;
            $intValorEgresoDoc          = 0;

            foreach($arrayAntPgPendientes as $arrayInfoDocFinCabAntPgPendientes)
            {
                $entityInfoPunto    = $em->getRepository('schemaBundle:InfoPunto')->find($arrayInfoDocFinCabAntPgPendientes['puntoId']);
                $strTipoDocumento   = "";


                if($arrayInfoDocFinCabAntPgPendientes['tipoDocumentoId'] != "")
                {
                    $entityAdmiTipoDocumentoFinanciero  = $em_financiero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                                        ->find($arrayInfoDocFinCabAntPgPendientes['tipoDocumentoId']);
                    $strTipoDocumento                   = $entityAdmiTipoDocumentoFinanciero->getCodigoTipoDocumento();
                }
                else
                {
                    $strTipoDocumento = "";
                }
               /**
                * Se verifica si un Anticipo por cruce se origino de un Anticipo al cual le aplicaron NDI por un valor menor 
                * al valor del Anticipo Original y si este se encuentra Cerrado no sumarizara al Saldo Total y se marcara en el
                * estado de cuenta en otro color.
                * */                
                $strEstado               = 'Cerrado';
                $boolSumatoriaValorTotal = true;
                $objAnticipoPorCruce     = $em_financiero->getRepository('schemaBundle:InfoPagoDet')
                                                         ->getAnticipoPorCrucePorPagoDetIdPorEstado
                                                           ($arrayInfoDocFinCabAntPgPendientes['id'],$strTipoDocumento,$strEstado);
                if( !empty($objAnticipoPorCruce) )
                {
                    $boolSumatoriaValorTotal = false;
                }

                if($entityAdmiTipoDocumentoFinanciero->getMovimiento() == "-")
                {
                    $intSumAntPgPendiente       +=  round($arrayInfoDocFinCabAntPgPendientes['valorTotal'], 2);
                    $intValorAntPg              =   round($arrayInfoDocFinCabAntPgPendientes['valorTotal'], 2);
                    $intTotalAntPgPendientes    +=  round($arrayInfoDocFinCabAntPgPendientes['valorTotal'], 2);
                    if($boolSumatoriaValorTotal)
                    {
                        $intSumatoriaValorTotal     -=  round($arrayInfoDocFinCabAntPgPendientes['valorTotal'], 2);
                        $intValorEgreso             +=  round($arrayInfoDocFinCabAntPgPendientes['valorTotal'], 2);
                    }
                }

                $entityInfoOficinaGrupo = $em_oficina->getRepository('schemaBundle:InfoOficinaGrupo')
                                                     ->find($arrayInfoDocFinCabAntPgPendientes['oficinaId']);

                $strNumeroRefCtaBnco    =   "";
                $intValorEgresoDoc      +=  $intValorAntPg;
                
                //Si el anticipo es recaudacion entonces agrega la fecha del anticipo en el comentario
                if ($arrayInfoDocFinCabAntPgPendientes['codigoFormaPago']=='REC')
                {
                    $arrayInfoDocFinCabAntPgPendientes['comentario'].=', fecha: '.
                        strval(date_format($arrayInfoDocFinCabAntPgPendientes['feCreacion'], "Y-m-d H:i:s"));
                }    
                $arrayContenedorResultados[] = array(
                    'documento'               => $arrayInfoDocFinCabAntPgPendientes['numeroFacturaSri'],
                    'valor_ingreso'           => "0.00",
                    'valor_egreso'            => round($intValorAntPg, 2),
                    'acumulado'               => "",
                    'Fecreacion'              => $arrayInfoDocFinCabAntPgPendientes['feCreacion'],
                    'strFeEmision'            => "",
                    'strFeAutorizacion'       => "",
                    'tipoDocumento'           => $strTipoDocumento,
                    'punto'                   => $entityInfoPunto->getLogin(),
                    'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                    'referencia'              => "",
                    'formaPago'               => $arrayInfoDocFinCabAntPgPendientes['codigoFormaPago'],
                    'numero'                  => $arrayInfoDocFinCabAntPgPendientes['numeroReferencia'],
                    'observacion'             => $arrayInfoDocFinCabAntPgPendientes['comentario'],
                    'boolSumatoriaValorTotal' => $boolSumatoriaValorTotal
                );

                $arrayParametros['intIdDocumento']  = $arrayInfoDocFinCabAntPgPendientes['id'];
                $cursorAntPgPendientes              = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                    ->getNotaDebitoAntNoAplicados($arrayParametros);
                if( !empty($cursorAntPgPendientes) )
                {
                    while( ($arrayDocumentosRelacionados = oci_fetch_array($cursorAntPgPendientes, OCI_ASSOC + OCI_RETURN_NULLS)) )
                    {

                        if($arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'ND' || 
                            $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'NDI' || 
                            $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'DEV')
                        {
                            $intValorIngresoDoc     +=  round($arrayDocumentosRelacionados['PRECIO'], 2);
                            $intSumatoriaValorTotal +=  round($arrayDocumentosRelacionados['PRECIO'], 2);
                            $intValorIngreso        +=  round($arrayDocumentosRelacionados['PRECIO'], 2);
                            $entityInfoDocFinDet = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                                                 ->findByDocumentoId($arrayDocumentosRelacionados['ID_PAGO_DET']);
                            foreach($entityInfoDocFinDet as $entityInfoDocFinDet)
                            {
                                $strObservacion = preg_replace('([^A-Za-z0-9])', ' ', $entityInfoDocFinDet->getObservacionesFacturaDetalle());
                            }

                            $arrayContenedorResultados[] = array(
                                'documento'               => $arrayDocumentosRelacionados['NUMERO_FACTURA_SRI'],
                                'valor_ingreso'           => round($arrayDocumentosRelacionados['PRECIO'], 2),
                                'valor_egreso'            => "0.00",
                                'acumulado'               => "",
                                'Fecreacion'              => $arrayDocumentosRelacionados['FE_CREACION'],
                                'strFeEmision'            => "",
                                'strFeAutorizacion'       => "",
                                'tipoDocumento'           => $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'],
                                'punto'                   => $entityInfoPunto->getLogin(),
                                'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                                'referencia'              => "",
                                'formaPago'               => "",
                                'numero'                  => "",
                                'observacion'             => $strObservacion,
                                'boolSumatoriaValorTotal' => true
                            );
                        }

                        if($arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'PAG' || 
                            $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'PAGC' || 
                            $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'ANT' || 
                            $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'ANTC' || 
                            $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'] == 'ANTS')
                        {
                            $intValorEgresoDoc      +=  round($arrayDocumentosRelacionados['PRECIO'], 2);
                            $intSumatoriaValorTotal -=  round($arrayDocumentosRelacionados['PRECIO'], 2);
                            $intValorEgreso         +=  round($arrayDocumentosRelacionados['PRECIO'], 2);
                            $entityInfoPagoDet      = $em_financiero->getRepository('schemaBundle:InfoPagoDet')
                                                                    ->findOneById($arrayDocumentosRelacionados['ID_PAGO_DET']);

                            $arrayContenedorResultados[] = array(
                                'documento'               => "",
                                'valor_ingreso'           => "0.00",
                                'valor_egreso'            => round($arrayDocumentosRelacionados['PRECIO'], 2),
                                'acumulado'               => "",
                                'Fecreacion'              => $arrayDocumentosRelacionados['FE_CREACION'],
                                'strFeEmision'            => "",
                                'strFeAutorizacion'       => "",
                                'tipoDocumento'           => $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'],
                                'punto'                   => $entityInfoPunto->getLogin(),
                                'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                                'referencia'              => "",
                                'formaPago'               => $arrayDocumentosRelacionados['CODIGO_FORMA_PAGO'],
                                'numero'                  => $arrayDocumentosRelacionados['NUMERO_REFERENCIA'],
                                'observacion'             => $entityInfoPagoDet->getComentario(),
                                'boolSumatoriaValorTotal' => true
                            );
                        }
                    }
                }
                //Envio el totalizado
                $arrayContenedorResultados[] = array(
                    'documento'               => "Total:",
                    'valor_ingreso'           => round($intValorIngresoDoc, 2),
                    'valor_egreso'            => round($intValorEgresoDoc, 2),
                    'acumulado'               => round($intValorIngresoDoc - $intValorEgresoDoc, 2),
                    'Fecreacion'              => "",
                    'strFeEmision'            => "",
                    'strFeAutorizacion'       => "",
                    'tipoDocumento'           => "",
                    'punto'                   => "",
                    'oficina'                 => "",
                    'referencia'              => "",
                    'boolSumatoriaValorTotal' => true
                );

                $intValorIngresoDoc     = 0;
                $intValorEgresoDoc      = 0;

                //Termina de escribir todo envio en blanco
                $arrayContenedorResultados[] = array(
                    'documento'               => "",
                    'valor_ingreso'           => "",
                    'valor_egreso'            => "",
                    'acumulado'               => "",
                    'Fecreacion'              => "",
                    'strFeEmision'            => "",
                    'strFeAutorizacion'       => "",
                    'tipoDocumento'           => "",
                    'punto'                   => "",
                    'oficina'                 => "",
                    'referencia'              => "",
                    'boolSumatoriaValorTotal' => true
                );
            }
        }

        if( !empty($arrayAntAsig) )
        {
            $intSumAntPgPendiente   = 0;
            $intValorAntPg          = 0;

            //Termina de escribir todo envio en blanco
            $arrayContenedorResultados[] = array(
                'documento'               => "Historial Anticipos asignados",
                'valor_ingreso'           => "",
                'valor_egreso'            => "",
                'acumulado'               => "",
                'Fecreacion'              => "",
                'strFeEmision'            => "",
                'strFeAutorizacion'       => "",
                'tipoDocumento'           => "",
                'punto'                   => "",
                'oficina'                 => "",
                'referencia'              => "",
                'boolSumatoriaValorTotal' => true
            );

            $intTotalAntPgPendientes    = 0;
            $intValorIngresoDoc         = 0;
            $intValorEgresoDoc          = 0;

            foreach($arrayAntAsig as $arrayAntAsig)
            {
                $entityInfoPunto    = $em->getRepository('schemaBundle:InfoPunto')->find($arrayAntAsig['puntoId']);
                $strTipoDocumento   = "";


                if($arrayAntAsig['tipoDocumentoId'] != "")
                {
                    $entityAdmiTipoDocumentoFinanciero  = $em_financiero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                                        ->find($arrayAntAsig['tipoDocumentoId']);
                    $strTipoDocumento                   = $entityAdmiTipoDocumentoFinanciero->getCodigoTipoDocumento();
                }
                else
                {
                    $strTipoDocumento = "";
                }
                if($entityAdmiTipoDocumentoFinanciero->getMovimiento() == "-")
                {
                    $intSumAntPgPendiente   +=  round($arrayAntAsig['valorTotal'], 2);
                    $intValorAntPg          =   round($arrayAntAsig['valorTotal'], 2);
                    $intTotalAntPgPendientes+=  round($arrayAntAsig['valorTotal'], 2);
                    $intSumatoriaValorTotal -=  round($arrayAntAsig['valorTotal'], 2);
                    $intValorEgreso         +=  round($arrayAntAsig['valorTotal'], 2);
                }

                $entityInfoOficinaGrupo = $em_oficina->getRepository('schemaBundle:InfoOficinaGrupo')->find($arrayAntAsig['oficinaId']);
                $strNumeroRefCtaBnco    = "";
                $intValorEgresoDoc      +=  $intValorAntPg;
                $strObservacionPago = $em_financiero->getRepository('schemaBundle:InfoPagoHistorial')
                   ->obtenerHistorialDePago($arrayAntAsig['id']);
                //Si el anticipo es recaudacion entonces agrega la fecha del anticipo en el comentario
                if ($arrayAntAsig['recaudacionId'])
                {
                    $strObservacionPago['registro']['observacion'].=", fecha:".strval(date_format($arrayAntAsig['feCreacion'], "Y-m-d H:i:s"));
                }
                $arrayContenedorResultados[] = array(
                    'documento'               => $arrayAntAsig['numeroPago'],
                    'valor_ingreso'           => "0.00",
                    'valor_egreso'            => round($intValorAntPg, 2),
                    'acumulado'               => "",
                    'Fecreacion'              => strval(date_format($arrayAntAsig['feCreacion'], "d/m/Y")),
                    'strFeEmision'            => "",
                    'strFeAutorizacion'       => "",
                    'tipoDocumento'           => $strTipoDocumento,
                    'punto'                   => $entityInfoPunto->getLogin(),
                    'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                    'referencia'              => "",
                    'formaPago'               => "",
                    'numero'                  => "",
                    'observacion'             => $strObservacionPago['registro']['observacion'],
                    'boolSumatoriaValorTotal' => true
                );

                $arrayParametros['intIdPago']       = $arrayAntAsig['id'];
                $cursorResult                       = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                    ->getAnticipoGenerados($arrayParametros);
                if( !empty($cursorResult) )
                {
                    while( ($arrayDocumentosRelacionados = oci_fetch_array($cursorResult, OCI_ASSOC + OCI_RETURN_NULLS)) )
                    {
                        $intValorIngresoDoc     +=  round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                        $intSumatoriaValorTotal +=  round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                        $intValorIngreso        +=  round($arrayDocumentosRelacionados['VALOR_PAGO'], 2);
                        $entityInfoPagoDet      =   $em_financiero->getRepository('schemaBundle:InfoPagoDet')->findOneById($arrayDocumentosRelacionados['ID_PAGO_DET']);

                        $arrayContenedorResultados[] = array(
                            'documento'               => $arrayDocumentosRelacionados['NUMERO_PAGO'],
                            'valor_ingreso'           => round($arrayDocumentosRelacionados['VALOR_PAGO'], 2),
                            'valor_egreso'            => "0.00",
                            'acumulado'               => "",
                            'Fecreacion'              => $arrayDocumentosRelacionados['FE_CREACION'],
                            'strFeEmision'            => "",
                            'strFeAutorizacion'       => "",
                            'tipoDocumento'           => $arrayDocumentosRelacionados['CODIGO_TIPO_DOCUMENTO'],
                            'punto'                   => $entityInfoPunto->getLogin(),
                            'oficina'                 => $entityInfoOficinaGrupo->getNombreOficina(),
                            'referencia'              => "",
                            'formaPago'               => $arrayDocumentosRelacionados['CODIGO_FORMA_PAGO'],
                            'numero'                  => $arrayDocumentosRelacionados['NUMERO_REFERENCIA'],
                            'observacion'             => $entityInfoPagoDet->getComentario(),
                            'boolSumatoriaValorTotal' => true
                        );
                    }
                }


                //Envio el totalizado
                $arrayContenedorResultados[] = array(
                    'documento'               => "Total:",
                    'valor_ingreso'           => round($intValorIngresoDoc, 2),
                    'valor_egreso'            => round($intValorEgresoDoc, 2),
                    'acumulado'               => round($intValorIngresoDoc - $intValorEgresoDoc, 2),
                    'Fecreacion'              => "",
                    'strFeEmision'            => "",
                    'strFeAutorizacion'       => "",
                    'tipoDocumento'           => "",
                    'punto'                   => "",
                    'oficina'                 => "",
                    'referencia'              => "",
                    'boolSumatoriaValorTotal' => true
                );

                $intValorIngresoDoc     = 0;
                $intValorEgresoDoc      = 0;

                //Termina de escribir todo envio en blanco
                $arrayContenedorResultados[] = array(
                    'documento'               => "",
                    'valor_ingreso'           => "",
                    'valor_egreso'            => "",
                    'acumulado'               => "",
                    'Fecreacion'              => "",
                    'strFeEmision'            => "",
                    'strFeAutorizacion'       => "",
                    'tipoDocumento'           => "",
                    'punto'                   => "",
                    'oficina'                 => "",
                    'referencia'              => "",
                    'boolSumatoriaValorTotal' => true
                );
            }
        }

        //Termina de escribir todo envio en blanco
        $arrayContenedorResultados[] = array(
            'documento'               => "RESUMEN PTO CLIENTE:",
            'valor_ingreso'           => "",
            'valor_egreso'            => "",
            'acumulado'               => "",
            'Fecreacion'              => "",
            'strFeEmision'            => "",
            'strFeAutorizacion'       => "",
            'tipoDocumento'           => "",
            'punto'                   => "",
            'oficina'                 => "",
            'referencia'              => "",
            'boolSumatoriaValorTotal' => true
        );

        if($intSumatoriaTotalMigracion > 0)
        {
            $arrayContenedorResultados[] = array(
                'documento'               => "Migracion",
                'valor_ingreso'           => round($intSumatoriaTotalMigracion, 2),
                'valor_egreso'            => "",
                'acumulado'               => "",
                'Fecreacion'              => "",
                'strFeEmision'            => "",
                'strFeAutorizacion'       => "",
                'tipoDocumento'           => "",
                'punto'                   => "",
                'oficina'                 => "",
                'referencia'              => "",
                'boolSumatoriaValorTotal' => true
            );
        }
        else
        {
            $arrayContenedorResultados[] = array(
                'documento'               => "Migracion",
                'valor_ingreso'           => "",
                'valor_egreso'            => round(abs($intSumatoriaTotalMigracion), 2),
                'acumulado'               => "",
                'Fecreacion'              => "",
                'strFeEmision'            => "",
                'strFeAutorizacion'       => "",
                'tipoDocumento'           => "",
                'punto'                   => "",
                'oficina'                 => "",
                'referencia'              => "",
                'boolSumatoriaValorTotal' => true
            );
        }

        $arrayContenedorResultados[] = array(
            'documento'               => "Debe",
            'valor_ingreso'           => round($intValorIngreso, 2),
            'valor_egreso'            => "",
            'acumulado'               => "",
            'Fecreacion'              => "",
            'strFeEmision'            => "",
            'strFeAutorizacion'       => "",
            'tipoDocumento'           => "",
            'punto'                   => "",
            'oficina'                 => "",
            'referencia'              => "",
            'boolSumatoriaValorTotal' => true
        );

        $arrayContenedorResultados[] = array(
            'documento'               => "Haber",
            'valor_ingreso'           => "",
            'valor_egreso'            => round($intValorEgreso, 2),
            'acumulado'               => "",
            'Fecreacion'              => "",
            'strFeEmision'            => "",
            'strFeAutorizacion'       => "",
            'tipoDocumento'           => "",
            'punto'                   => "",
            'oficina'                 => "",
            'referencia'              => "",
            'boolSumatoriaValorTotal' => true
        );

        //Para el saldo debo considerar el valor de migracion
        $intSumatoriaValorTotal +=  $intSumatoriaTotalMigracion;

        $arrayContenedorResultados[] = array(
            'documento'               => "SALDO:",
            'valor_ingreso'           => "",
            'valor_egreso'            => "",
            'acumulado'               => round($intSumatoriaValorTotal, 2),
            'Fecreacion'              => "",
            'strFeEmision'            => "",
            'strFeAutorizacion'       => "",
            'tipoDocumento'           => "",
            'numero'                  => "",
            'punto'                   => "",
            'oficina'                 => "",
            'referencia'              => "",
            'boolSumatoriaValorTotal' => true
        );

        if(empty($arrayContenedorResultados))
        {
            $arrayContenedorResultados[] = array(
                'documento'               => "",
                'valor_ingreso'           => "",
                'valor_egreso'            => "",
                'acumulado'               => "",
                'Fecreacion'              => "",
                'strFeEmision'            => "",
                'strFeAutorizacion'       => "",
                'tipoDocumento'           => "",
                'punto'                   => "",
                'oficina'                 => "",
                'referencia'              => "",
                'boolSumatoriaValorTotal' => true
            );
        }

        $objResponse = new Response(json_encode(array('documentos' => $arrayContenedorResultados)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * obtenerDetalleNotasDebito, obtiene el detalle de los documentos ND y NDI
     * @version 1.1 09-04-2015 Gina Villalba <gvillalba@telconet.ec>
     * @since 1.0
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 15-09-2016 - Se quita la validación de substring para que el nombre de la oficina de los detalles asociados a las NDI se vea 
     *                           completo.
     * @return Array de los detalles del documento
     */
    function obtenerDetalleNotasDebito(
                                        &$arreglo, 
                                        &$valor_i, 
                                        &$valor_ingreso, 
                                        &$sumatoria, 
                                        &$valor_e, 
                                        &$valor_egreso, 
                                        $p_cliente, 
                                        $oficina, 
                                        $em_financiero, 
                                        $arrayParametros)
    {
        $cursorResult = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getDocumentosRelacionados($arrayParametros);

        if($cursorResult)
        {
            while(($row = oci_fetch_array($cursorResult, OCI_ASSOC + OCI_RETURN_NULLS)) != false)
            {
                if(
                    $row['CODIGO_TIPO_DOCUMENTO'] == 'PAG' 
                    || $row['CODIGO_TIPO_DOCUMENTO'] == 'PAGC' 
                    || $row['CODIGO_TIPO_DOCUMENTO'] == 'ANT' 
                    || $row['CODIGO_TIPO_DOCUMENTO'] == 'ANTC' 
                    || $row['CODIGO_TIPO_DOCUMENTO'] == 'ANTS')
                {

                    $valor_e+=round($row['VALOR_PAGO'], 2);

                    $sumatoria-=round($row['VALOR_PAGO'], 2);
                    $valor_egreso+=round($row['VALOR_PAGO'], 2);

                    $observacion_int = $em_financiero->getRepository('schemaBundle:InfoPagoDet')->findOneById($row['ID_PAGO_DET']);

                    $arreglo[] = array(
                        'documento'     => $row['NUMERO_PAGO'],
                        'valor_ingreso' => "0.00",
                        'valor_egreso'  => round($row['VALOR_PAGO'], 2),
                        'acumulado'     => "",
                        'Fecreacion'    => $row['FE_CREACION'],
                        'tipoDocumento' => $row['CODIGO_TIPO_DOCUMENTO'],
                        'punto'         => $p_cliente->getLogin(),
                        'oficina'       => $oficina->getNombreOficina(),
                        'referencia'    => "",
                        'formaPago'     => $row['CODIGO_FORMA_PAGO'],
                        'numero'        => $row['NUMERO_REFERENCIA'],
                        'observacion'   => $observacion_int->getComentario(),
                    );
                }

                //Me devuelve todo el listado de documentos
                if(
                    $row['CODIGO_TIPO_DOCUMENTO'] == 'ND' 
                    || $row['CODIGO_TIPO_DOCUMENTO'] == 'NDI' 
                    || $row['CODIGO_TIPO_DOCUMENTO'] == 'DEV')
                {
                    $valor_i+=round($row['VALOR_PAGO'], 2);

                    $sumatoria+=round($row['VALOR_PAGO'], 2);
                    $valor_ingreso+=round($row['VALOR_PAGO'], 2);

                    $observacion_int = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($row['ID_PAGO_DET']);
                    foreach($observacion_int as $obs)
                        $observacion = preg_replace('([^A-Za-z0-9])', ' ', $obs->getObservacionesFacturaDetalle());

                    $arreglo[] = array(
                        'documento'     => $row['NUMERO_PAGO'],
                        'valor_ingreso' => round($row['VALOR_PAGO'], 2),
                        'valor_egreso'  => "0.00",
                        'acumulado'     => "",
                        'Fecreacion'    => $row['FE_CREACION'],
                        'tipoDocumento' => $row['CODIGO_TIPO_DOCUMENTO'],
                        'punto'         => $p_cliente->getLogin(),
                        'oficina'       => $oficina->getNombreOficina(),
                        'referencia'    => "",
                        'formaPago'     => "",
                        'numero'        => "",
                        'observacion'   => $observacion,
                    );

                    $arrayParametros['intIdDocumento'] = $row['ID_PAGO_DET'];
                    $this->obtenerDetalleNotasDebito(
                        $arreglo, 
                        $valor_i, 
                        $valor_ingreso, 
                        $sumatoria, 
                        $valor_e, 
                        $valor_egreso, 
                        $p_cliente, 
                        $oficina, 
                        $em_financiero, 
                        $arrayParametros);
                }

                if($row['CODIGO_TIPO_DOCUMENTO'] == 'NC' || $row['CODIGO_TIPO_DOCUMENTO'] == 'NCI')
                {
                    $sumatoria-=round($row['VALOR_PAGO'], 2);
                    $valor_egreso+=round($row['VALOR_PAGO'], 2);
                    $valor_e+=round($row['VALOR_PAGO'], 2);

                    $observacion_int = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($row['ID_PAGO_DET']);
                    foreach($observacion_int as $obs)
                        $observacion = preg_replace('([^A-Za-z0-9])', ' ', $obs->getObservacionesFacturaDetalle());

                    $arreglo[] = array(
                        'documento'     => $row['NUMERO_PAGO'],
                        'valor_ingreso' => "0.00",
                        'valor_egreso'  => round($row['VALOR_PAGO'], 2),
                        'acumulado'     => "",
                        'Fecreacion'    => $row['FE_CREACION'],
                        'tipoDocumento' => $row['CODIGO_TIPO_DOCUMENTO'],
                        'punto'         => $p_cliente->getLogin(),
                        'oficina'       => $oficina->getNombreOficina(),
                        'referencia'    => "",
                        'formaPago'     => "",
                        'numero'        => "",
                        'observacion'   => $observacion,
                    );
                }
            }
        }
    }
    /**
     * 
     * Documentacion para el metodo estadoCuentaPtoAction
     * 
     * @author Christian Yunga <cyungat@telconet.ec>
     * @version 1.1 14-01-2023 Se agrega validación de perfiles solo los perfiles permitidos pueden ingresar a esta opcion.
     *                         Si pasa la validación entonces se registrara el inicio de sesion  junto con el login del cliente 
     *                         al que se esta consultando  solo para empresa MD.
     *
     * @since 1.0
     */
    public function estadoCuentaPtoAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $arrayCliente           = $objSession->get('cliente');
        $arrayPtoCliente        = $objSession->get('ptoCliente');
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');        
        $strUsrCreacion         = $objSession->get('user');
        $strIpCreacion          = $objRequest->getClientIp();
        $serviceInfoLog         = $this->get('comercial.InfoLog');
        $serviceTokenCas        = $this->get('seguridad.TokenCas');
        $arrayDatosCliente      = array();
        
        if($arrayCliente)
        {
            $arrayParametro=array('cliente' => "S");
        }
        else
        {
            $arrayParametro=array('cliente' => "N");
        }

        if($arrayPtoCliente)
        {
            $arrayParametro['ptocliente']="S";
        }
        else
        {
            $arrayParametro['ptocliente']="N";
        }
        
        if($strPrefijoEmpresa == 'MD' &&  (true === $this->get('security.context')->isGranted('ROLE_91-1')))
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
                                                                   'observacion'     => 'ESTADO CUENTA PUNTO',
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
        else if($strPrefijoEmpresa == 'MD' &&  (false === $this->get('security.context')->isGranted('ROLE_91-1')))
        {
            return $this->render('financieroBundle:reportes:accesoDenegado.html.twig');
        }
        return $this->render('financieroBundle:reportes:estadoCuentaPto.html.twig',$arrayParametro);
    }
	
    /**
     * reporteAnticiposCruzadosAction.
     * Metodo que permite generar un reporte de anticipos cruzados mediante su codigo/prefijo de empresa
     * Se agrega arrayParametros al renderizar twig
     * 
     * @author Javier Hidalgo <jihidalgo@telconet.ec>
     * @version 1.0 09-06-2023
     * 
     */
	public function reporteAnticiposCruzadosAction(){
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $strCodEmpresa          = $objSession->get('strCodEmpresa');
        $arrayParametros                        = array();
        $arrayParametros['strCodEmpresa']       = $strCodEmpresa;
        $arrayParametros['strPrefijoEmpresa']   = $strPrefijoEmpresa;

		return $this->render('financieroBundle:reportes:anticiposCruzados.html.twig',$arrayParametros);
	
	}    
	
    /**
     * genera excel de los anticipos cruzados segun fecha enviado por request
     * @version 1.0
     * @author amontero@telconet.ec
     * @since 01-09-2015
     */    
	public function excelAnticiposCruzadosAction()
    {    
        $objPHPExcel = new PHPExcel();
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array( ' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);		
        // Establecer propiedades
        $objPHPExcel->getProperties()
            ->setCreator("Telcos")
            ->setLastModifiedBy("Telcos")
            ->setTitle("Documento Excel de Anticipos Cruzados")
            ->setSubject("Documento Excel de Anticipos Cruzados")
            ->setDescription("")
            ->setKeywords("Excel Office 2007 openxml php")
            ->setCategory("Excel");
        $request    = $this->getRequest();
        $idEmpresa  = $request->getSession()->get('idEmpresa');
        $mes        = $request->request->get('mes');
        $anio       = $request->request->get('anio');
        $diasmes    = date("d", mktime(0, 0, 0, $mes + 1, 0, $anio));
        $fechaDesde = $anio . "-" . $mes . "-01";
        $fechaHasta = $anio . "-" . $mes . "-" . $diasmes;
        $emfn       = $this->get('doctrine')->getManager('telconet_financiero');
        $anticipos  = $emfn->getRepository('schemaBundle:InfoPagoCab')->findAnticiposCruzados($idEmpresa, $fechaDesde, $fechaHasta);
        $i          = 2;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'OFICINA')
            ->setCellValue('B1', 'PAGO')
            ->setCellValue('C1', 'VALOR')
            ->setCellValue('D1', 'CLIENTE')
            ->setCellValue('E1', 'LOGIN')
            ->setCellValue('F1', 'FECHA_CREACION_PAGO')
            ->setCellValue('G1', 'FECHA_CRUCE')
            ->setCellValue('H1', 'FACTURA')
            ->setCellValue('I1', 'TIPO_PAGO') 
            ->setCellValue('J1', 'ANTICIPO_ORIGINAL')  
            ->setCellValue('K1', 'TIPO_ANTICIPO_ORIGINAL') 
            ->setCellValue('L1', 'ESTADO')          
        ;	
		
        foreach($anticipos as $anticipo)
        {
            // Agregar Informacion
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $anticipo['oficina'])
            ->setCellValue('B'.$i, $anticipo['pago'])
            ->setCellValue('C'.$i, $anticipo['valorPago']);
            if($anticipo['puntoId'])
            {    
                $entityPunto=$emfn->getRepository('schemaBundle:InfoPunto')->find($anticipo['puntoId']);
            }    
            if($anticipo['referenciaId'])
            {    
                $entityFactura=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($anticipo['referenciaId']);
            }    
            if($entityPunto)
            {
                $objPersona=$entityPunto->getPersonaEmpresaRolId()->getPersonaId();
                if($objPersona->getRazonSocial())
                {
                    $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('D'.$i, $objPersona->getRazonSocial());
                }
                else
                {
                    $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('D'.$i, $objPersona->getNombres()
                    ." ".$objPersona->getApellidos());
                }
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, $entityPunto->getLogin());
            }

            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('F'.$i, $anticipo['fechaCreacionPago'])
            ->setCellValue('G'.$i, $anticipo['fechaCruce']);
            if($entityFactura)
            {    
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i, $entityFactura->getNumeroFacturaSri());
            }    
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$i, $anticipo['tipoDocumento']);  
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i,$anticipo['numeroPagoOriginal']);   
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$i,$anticipo['tipoDocumentoOriginal']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i,$anticipo['estadoPago']);   
            $i++;
        }
        // Renombrar Hoja
        $objPHPExcel->getActiveSheet()->setTitle('Anticipos Cruzados'); 
        // Establecer la hoja activa, para que cuando se abra el documento se muestre primero.
        $objPHPExcel->setActiveSheetIndex(0);		
        // Se modifican los encabezados del HTTP para indicar que se envia un archivo de Excel.
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_anticipos_cruzados.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
	
	public function listarFacturasAction()
    {
		$request = $this->getRequest();
		$session=$request->getSession();
		$cliente=$session->get('cliente');
		$ptocliente=$session->get('ptoCliente');
		
		if($cliente)
			$parametro=array('cliente' => "S");
		else
			$parametro=array('cliente' => "N");
			
		
        return $this->render('financieroBundle:reportes:listadoFacturas.html.twig', $parametro);
    }
           
    /**
     * listarReportesCarteraAction
     * Metodo para la presentacion del grid de los reportes de cartera segun la empresa
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @since 18/11/2014
     * @return view
     */
    public function listarReportesCarteraAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $prefijoEmpresa = $session->get('prefijoEmpresa');
        return $this->render('financieroBundle:reportes:listadoReportesCartera.html.twig', array('prefijoEmpresa' => $prefijoEmpresa));
    }

    /**
     * gridReportesCarteraAction
     * Metodo que obtiene los archivos de la cartera de clientes segun la empresa
     * Se modifica:
     *  - Forma de acceso al direcctorio fisico de los archivos
     *  - Forma de pasar los parametros para la descarga unificada de los archivos ZIP
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @since 18/11/2014
     * @version 2.0 18-08-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.1 - 30-06-2016 - Se cambia la ruta a la que apunta el reporte de cartera para TN
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.2 - 28-11-2016 - Se modifica el método para que al consultar los reportes disponibles muestre todos los reportes que comiencen con
     *                             la palabra 'reporte' y contengan en el nombre la palabra 'ZIP'
     * 
     * @author German Valenzuela Franco <gvalenzuela@telconet.ec>
     * @version 2.3 - 12-17-2017 - Se modifica el método aumentando el prefijo TNP que corresponde a la empresa Panama
     * 
     * @author Carlos Caguana  <ccaguana@telconet.ec>
     * @version 2.4 - 19-03-2021 - Se modifica para el consumo de ubicación de los archivos en el servidor NFS
     * 
     * @return json con el listado de archivos
     */
    public function gridReportesCarteraAction()
    {
        try
        {       
            $request = $this->getRequest();
            $mes = $request->get('mes');
            $anio = $request->get('anio');
            $idEmpresa = $request->getSession()->get('idEmpresa');
            $serviceUtils  = $this->get('schema.Util');
            $intTotal=0;
            $arrayParametros= array();
            if($mes && $anio)
            {
                if(strlen($mes) == 1)
                    $mes = "0" . $mes;
                else
                    $mes = $mes;
            }
            

        
            $strDate =$anio."-".$mes."-01";
            $strDiaFin= date("t", strtotime($strDate));
            $arrayParametros['strCodEmpresa']=$idEmpresa;
            $strFecha=array('inicio'=> "01/".$mes."/".$anio ,
                             'fin'=>$strDiaFin."/".$mes."/".$anio);

            

            $em = $this->get('doctrine')->getManager('telconet');
            $entityEmpresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->find($idEmpresa);

            $arrayParamNfs   = array(
                'prefijoEmpresa'       => strtoupper($entityEmpresa->getPrefijo()) ,
                'strApp'               => "TelcosWeb",
                'strCodEmpresa'        => $idEmpresa,
                'strUsrCreacion'       => 'telcos',
                'strFecha'             => $strFecha,
                'strSubModulo'         => "ReporteCartera");

            $arrayRespNfs =$serviceUtils ->buscarArchivosReporteCarteraNfs($arrayParamNfs);

            if ($arrayRespNfs['intStatus'] == 200 )
            {
               $arrayResultadoFiles= $arrayRespNfs['arrayDatosArchivos'];
                if(!empty($arrayResultadoFiles))
               {
                $intTotal=count($arrayResultadoFiles);
                   foreach($arrayResultadoFiles as $arrayFile)
                   {
                       $arreglo[] = array(
                           'linkVer' =>$arrayFile['nombreFile'],
                           'linkFile' =>$arrayFile['pathFile'],
                           'size' => $arrayFile['pesoFile']
                       ); 
                   }  
           
               }

            }
            else
            {
                throw new \Exception('Ocurrio un error al consultar el  archivo al servidor Nfs : '.$arrayRespNfs['strMensaje']);
            }

        }catch(\Exception $objE)
        {
            $strResponse = new Response(json_encode(array('total' =>$intTotal, 'clientes' => $arreglo)));
            $strResponse->headers->set('Content-type', 'text/json');
            return $strResponse;
        }
       
        $strResponse = new Response(json_encode(array('total' => $intTotal, 'clientes' => $arreglo)));
        $strResponse->headers->set('Content-type', 'text/json');
        return $strResponse;
    }


    /**
     * downloadReporteAction
     * Metodo que permite descarga los reportes 
     * Se modifica:
     *  - Se aumenta el parametro de la ruta para la descaraga dinamica de los archivos
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @since 18/11/2014
     * @version 2.0 18-08-2015
     * @return json con el listado de archivos
     */
    public function downloadReporteAction($archivo, $ruta)
    {
        $downloadPath = str_replace("-", "/", $ruta);
        $downloadPath = $downloadPath . $archivo;
        $content = file_get_contents($downloadPath);
        $response = new Response();

        //set headers
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $archivo);

        $response->setContent($content);
        return $response;
    }

    public function listarReportesComparativoMensualAction()
    {
        $request = $this->getRequest();
        $session=$request->getSession();
        $cliente=$session->get('cliente');
        $ptocliente=$session->get('ptoCliente');
        return $this->render('financieroBundle:reportes:listadoReportesComparativoMensual.html.twig', array());        
    }
    
    /**
     * gridReportesComparativoMensualAction
     * Metodo que obtiene los archivos de la facturacion comparativa mensualmente
     * Se modifica:
     *  - Forma de acceso al direcctorio fisico de los archivos
     *  - Forma de pasar los parametros para la descarga unificada de los archivos ZIP
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @since 18/11/2014
     * @version 2.0 18-08-2015
     * @return json con el listado de archivos
     */
    public function gridReportesComparativoMensualAction()
    {

        $request = $this->getRequest();
        $fechaDesde = explode('T', $request->get("fechaDesde"));
        $fechaHasta = explode('T', $request->get("fechaHasta"));
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");

        $mes = $request->get('mes');
        $anio = $request->get('anio');

        //Directorio para buscar el archivo
        $path_telcos = $this->container->getParameter('path_telcos');
        $findPath = $path_telcos . 'telcos/web/public/uploads/reportes/facturacion_comparativa/';

        if($mes && $anio)
        {

            if(strlen($mes) == 1)
                $mes = "0" . $mes;
            else
                $mes = $mes;
            $criterioFecha = $anio . "-" . $mes;
        }
        else
        {
            $criterioFecha = date('Y-m');
        }

        $finder = new Finder();
        $prefijoEmpresa = $request->getSession()->get('prefijoEmpresa');
        $finder->name("reporteComparativoFacturaZip" . $prefijoEmpresa . "*" . $criterioFecha . "*")->files()->in($findPath);
        $finder->sortByChangedTime();

        foreach($finder as $file)
        {

            $pos = strpos($file->getRealpath(), $file->getRelativePathname());
            $ruta = str_replace("/", "-", substr($file->getRealpath(), 0, $pos));
            $urlArchivo = $this->generateUrl('reportes_descarga', array('archivo' => $file->getRelativePathname(), 'ruta' => $ruta));

            $arreglo[] = array(
                'linkVer' => $file->getRelativePathname(),
                'linkFile' => $urlArchivo,
                'size' => (round(filesize($file->getRealpath()) / 1024 / 1024, 2)) . ' Mb'
            );
        }

        $response = new Response(json_encode(array('total' => $total, 'clientes' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    //Descarga de ATS
    public function listarReportesAtsAction()
    {
        $request = $this->getRequest();
        $session=$request->getSession();
        $idEmpresa=$session->get('idEmpresa');        
        return $this->render('financieroBundle:reportes:listadoReportesAts.html.twig', array());        
    }
    
    /**
     * Documentación para el método 'gridReportesAtsAction'.
     *
     * Setea la variables y llama controlador que genera el ATS
     *
     * @return object $objResponse Retorna ('total' => Total de filas consultadas, 'arrayAts' => Los datos de los registros consultados)
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 15-08-2014
     */

    /**
     * @Secure(roles="ROLE_14-1637")
     */
    public function gridReportesAtsAction()
    {

        $intMesActual   = 0;
        $intTotal       = 0;


        $request        = $this->getRequest();
        $session        = $request->getSession();

        $strIdEmpresa   = $session->get('idEmpresa');
        $strAnio        = $request->get('strAnioParam');
        $strMes         = $request->get('strMesParam');

        //Valida que el año este seteado, caso contrario eligira el año actual
        if(!$strAnio)
        {
            $strAnio = date('Y');
        }

        $serviceInfoCompElectronico = $this->get('financiero.InfoCompElectronico');

        /* Verifica que le mes tenga como valor 00, por lo cual se mostrara en el grid
         * los ats desde el primer mes hasta el mes actual.
         */
        if($strMes == '00')
        {

            $intMesActual = date("n");

            //Verifica si el año es diferente del mes actual le setea 13 al mes para que se presenten todos los ats del año consultado
            if($strAnio != date('Y'))
            {
                $intMesActual = 13;
            }

            //recorre desde el primer mes hasta el mese seteado en $intMesActual
            for($intMes = 1; $intMes < $intMesActual; $intMes ++):

                $arrayParamsSend = array('strIdEmpresa' => $strIdEmpresa,
                                         'strMes' => str_pad($intMes, 2, "0", STR_PAD_LEFT),
                                         'strAnio' => $strAnio);

                //llama al metodo del service que genera los ATS
                $arrayGetAts = $serviceInfoCompElectronico->getAts($arrayParamsSend);

                $arrayAts[] = array('strIdEmpresa' => $strIdEmpresa,
                                    'intTamanio' => $arrayGetAts['intTamanio'],
                                    'strAnio' => $strAnio,
                                    'strMes' => str_pad($intMes, 2, "0", STR_PAD_LEFT));
                //suma el total de los registros
                $intTotal ++;
            endfor;
        }else
        {
            $arrayParamsSend = array('strIdEmpresa' => $strIdEmpresa,
                                     'strMes' => str_pad($strMes, 2, "0", STR_PAD_LEFT),
                                     'strAnio' => $strAnio);
            //Por falso, el mes esta seteado en $strMes y retorna el ats del mes $strMes y año $strAnio consultado.
            $arrayGetAts = $serviceInfoCompElectronico->getAts($arrayParamsSend);
            $arrayAts[] = array('strIdEmpresa' => $strIdEmpresa,
                                'intTamanio' => $arrayGetAts['intTamanio'],
                                'strAnio' => $strAnio,
                                'strMes' => $strMes);
            //suma el total de los registros
            $intTotal ++;
        }

        $objResponse = new Response(json_encode(array('total' => $intTotal, 'arrayAts' => $arrayAts)));

        $objResponse->headers->set('Content-type', 'text/json');

        return $objResponse;
    }

    /**
     * Documentación para el método 'descargarDocumentoAction'.
     *
     * Realiza la exportacion del documento XML 
     *
     * @return integer $intNum Contiene por default 0.
     *
     * @return object $objResponse Retorna ('total' => Total de filas consultadas, 'arrayAts' => Los datos de los registros consultados)
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 15-08-2014
     */

    /**
     * @Secure(roles="ROLE_14-1637")
     */
    public function descargarDocumentoAction($intNum)
    {

        $objRequest     = $this->getRequest();
        $strIdEmpresa   = $objRequest->get("strIdEmpresa");
        $strMes         = $objRequest->get("strMes");
        $strAnio        = $objRequest->get("strAnio");
        $strUsuario     = $objRequest->getSession()->get('user');

        $arrayParamsSend = array('strIdEmpresa' => $strIdEmpresa,
                                 'strMes' => str_pad($strMes, 2, "0", STR_PAD_LEFT),
                                 'strAnio' => $strAnio);

        $serviceInfoCompElectronico = $this->get('financiero.InfoCompElectronico');

        $arrayGetAts = $serviceInfoCompElectronico->getAts($arrayParamsSend);

        $arrayParamsInsert = array('strMes' => str_pad($strMes, 2, "0", STR_PAD_LEFT),
                                   'strAnio' => $strAnio,
                                   'clobDocumentoAts' => $arrayGetAts['clobDocumentoAts'],
                                   'strUsuario' => $strUsuario, 'strEmpresaCod' => $strIdEmpresa);

        $serviceInfoCompElectronico->insertsDocumentoAts($arrayParamsInsert);
        
        if($arrayGetAts['boolCheck'] === false){
            $arrayGetAts['clobDocumentoAts'] = "Existio un error: ".$arrayGetAts['strMessage'];
            $arrayGetAts['nameFile'] ="errorAts.txt";
        }
        $objResponse = new Response();

        $objResponse->headers->set('Content-Type', 'mime/type');
        $objResponse->headers->set('Content-Disposition', 'attachment;filename="' . $arrayGetAts['nameFile']);
        $objResponse->setContent($arrayGetAts['clobDocumentoAts']);

        return $objResponse;
    }

    public function downloadReporteAtsAction($archivo)
    {		
		$request = $this->getRequest();
		$session= $request->getSession();
		$idEmpresa = $session->get('idEmpresa');
		$em=$this->getDoctrine('doctrine')->getManager('telconet');        
		$empresa= $em->getRepository('schemaBundle:InfoEmpresaGrupo')->find($idEmpresa);
		$prefijo=$empresa->getPrefijo();
		$ruta="/home/telcos/web/public/uploads/ats/".$prefijo; 
		$path = $ruta."/".$archivo;
		$content = file_get_contents($path);

		$response = new Response();

		//set headers
		$response->headers->set('Content-Type', 'mime/type');
		$response->headers->set('Content-Disposition', 'attachment;filename="'.$archivo);

		$response->setContent($content);
		return $response;
    }
        
    /**
     * SearchDocumentosAction
     * Metodo que permite presentar la busqueda avanzada de documentos
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.1 04-08-2016
     * @since 1.0
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 22-01-2017 - Se agrega perfil para la búsqueda por fecha de contabilización para los reportes de cobranzas.
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.3 19-07-2017 - Se agrega los permisos por empresa para mostrar u ocultar el filtro de Estado del Punto.
     */   
    public function SearchDocumentosAction()
    {
        $request              = $this->getRequest();
        $session              = $request->getSession();
        $emFinanciero         = $this->getDoctrine()->getManager('telconet_financiero');
        $cliente              = $session->get('cliente');
        $ptocliente           = $session->get('ptoCliente');
        $strCodEmpresa        = $session->get('idEmpresa');
        $arrayRolesPermitidos = array();
        
        if (true === $this->get('security.context')->isGranted('ROLE_212-5037'))
        {
            $arrayRolesPermitidos[] = 'ROLE_212-5037'; //Búsqueda por fecha de contabilización en los reporte de cobranzas
        }
        
        $arrayPermisos=array('strParametro' => 'CONF_PERM_EMPRE');
        $strGetParametroEmp = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')->obtenerParametroConfig($arrayPermisos);
        if ($strCodEmpresa == $strGetParametroEmp || is_null($strGetParametroEmp))
        {
            $arrayEmpresaPermitida['PERMISOS_EMPRESA'] = true;
        }
        else
        {
            $arrayEmpresaPermitida['PERMISOS_EMPRESA'] = false;
        }
        
        return $this->render('financieroBundle:reportes:SearchDocumentos.html.twig', array('rolesPermitidos'    => $arrayRolesPermitidos,
                                                                                           'boolPermisoEmpresa' => $arrayEmpresaPermitida));
    }

    /**
     * configAutomaticAction
     * Metodo que permite presentar la configuracion automatica del reporte mensual y diario de Pagos
     *
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.0 Creacion del metodo
     *
     * @Secure(roles="ROLE_391-1")
     */
    public function configAutomaticAction()
    {
        $emFinanciero         = $this->get('doctrine')
                                     ->getManager('telconet_financiero');

        try
        {
            $strEstadoPunto['arrayEstadoPunto']=$emFinanciero ->getRepository('schemaBundle:InfoPagoCab')
                                                              ->obtenerParametroConfig(array('strParametro' => 'ESTADO_PUNTO'));
        }
        catch (\Exception $ex)
        {
            $strEstadoPunto['arrayEstadoPunto']=null;
        }

        try
        {
            $arrayEstadoPago['arrayEstadoPago']=$emFinanciero->getRepository('schemaBundle:InfoPagoCab')
                                                             ->obtenerParametroConfig(array('strParametro' => 'ESTADO_PAGO'));
        }
        catch (\Exception $ex)
        {
            $arrayEstadoPago['arrayEstadoPago']=null;
        }

        try
        {
            $arrayFormaPago['arrayFormaPago']=$emFinanciero->getRepository('schemaBundle:InfoPagoCab')
                                                           ->obtenerParametroConfig(array('strParametro' => 'FORMA_PAGO'));
        }
        catch (\Exception $ex)
        {
            $arrayFormaPago['arrayFormaPago']=null;
        }

        return $this->render('financieroBundle:reportes:ConfigAutomatic.html.twig', array('strEstadoPunto'  => $strEstadoPunto,
                                                                                          'strEstadoPago'   => $arrayEstadoPago,
                                                                                          'strFormaPago'    => $arrayFormaPago));
    }

     /**
     * getTipoDocumentosAction
     * Metodo que permite listar los tipo de documentos financieros existentes
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.1 04-08-2016
     * @since 1.0
     */
    public function getTipoDocumentosAction()
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
            $ArrayTipoDocumento[]=array('codigo_tipo_documento'=>$tipo_documento->getCodigoTipoDocumento(), 
                                        'nombre_tipo_documento'=>$tipo_documento->getNombreTipoDocumento() );
        endforeach;
		
		$num    = count($ArrayTipoDocumento);		
		$dataF  =json_encode($ArrayTipoDocumento);
		$objJson= '{"total":"'.$num.'","encontrados":'.$dataF.'}';		
        $respuesta->setContent($objJson);   
			
        return $respuesta;
    }
    
    /**
     * busquedaDatosFinancieroAction
     * Metodo que permite listar todos los documentos que cumplan los parametros de busqueda
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.1 04-08-2016
     * @since 1.0
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 19-08-2016 - Se agregan las variables '$arrayFinDocFechaAutorizacionDesde', '$arrayFinDocFechaAutorizacionHasta' para realizar la 
     *                           búsqueda por fecha de autorización
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 19-12-2016 - Se agregan las variables 'strFinPagFechaContabilizacionDesde', 'strFinPagFechaContabilizacionHasta' para 
     *                           realizar la búsqueda por fechas con los cuales se contabilizan los documentos del departamento de cobranzas
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 11-01-2017 - Se modifica la función para que la variable 'strReferencia' muestre en el grid el número de referencia o el número
     *                           de la cuenta del banco asociada al detalle del pago. Adicional en la columna de número de comprobante que se muestre
     *                           el número asociado usando la función 'getNumeroComprobante' del repositorio 'InfoPagoCabRepository'
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 03-04-2017 - Se añade el tipo 'NCI' para que se pueda consultar la información respectiva de las notas de crédito internas
     *
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.6 31-07-2017 - Se agrega el filtro por Estado Punto
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.7 09-03-2018 - Se agrega al Grid Fecha de Autorizacion
     * @author : Kevin Baque <kbaque@telconet.ec>
     * @version 1.8 31-12-2018 Se realiza cambio para quela consulta se realice a través de la persona en sesion, solo para Telconet
     *                         en caso de ser asistente aparecerá los datos de los vendedores asignados al asistente
     *                         en caso de ser vendedor aparecerá sus datos
     *                         en caso de ser subgerente aparecerá los datos de los vendedores que reportan al subgerente
     *                         en caso de ser gerente aparecerá todos los datos
     * 
     * @author Angel Reina <areina@telconet.ec>
     * @version 1.9 26-06-2019 - Se agrega columna TIPO CUENTA para los Reporte Financiero - Por tipo de documentos Pago, Pago por Cruce, 
     * Anticipo, Anticipo por Cruce, Anticipo sin Cliente (PAG, PAGC, ANT, ANTC, ANTS) 
     * 
     */
    public function busquedaDatosFinancieroAction()
    {
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $em          = $this->getDoctrine()->getManager('telconet_financiero');			
        $em->getConnection()->beginTransaction();
        try{	
            $objResponse = new Response();
            $objResponse->headers->set('Content-Type', 'text/json');

            $objRequest = $this->get('request');
            $objSession  = $objRequest->getSession();
            $strUsrCreacion     = $objSession->get('user');
            $strLogin           = $objRequest->query->get("login") ? $objRequest->query->get("login") : "" ;        
            $strDescripcionPto  = $objRequest->query->get("descripcion_pto") ? $objRequest->query->get("descripcion_pto") : "" ;
            $strDireccionPto    = $objRequest->query->get("direccion_pto") ? $objRequest->query->get("direccion_pto") : "" ;
            $strEstadosPto      = $objRequest->query->get("estados_pto") ? $objRequest->query->get("estados_pto") : "" ;
            $strNegociosPto    = $objRequest->query->get("negocios_pto") ? $objRequest->query->get("negocios_pto") : "" ;
            $strVendedor        = $objRequest->query->get("vendedor") ? $objRequest->query->get("vendedor") : "" ;
            $strIdentificacion  = $objRequest->query->get("identificacion") ? $objRequest->query->get("identificacion") : "" ;
            $strNombre          = $objRequest->query->get("nombre") ? $objRequest->query->get("nombre") : "" ;
            $strApellido        = $objRequest->query->get("apellido") ? $objRequest->query->get("apellido") : "" ;
            $strRazonSocial    = $objRequest->query->get("razon_social") ? $objRequest->query->get("razon_social") : "" ;
            $strDireccionGrl   = $objRequest->query->get("direccion_grl") ? $objRequest->query->get("direccion_grl") : "" ;
            $strDependeEdificio = $objRequest->query->get("depende_edificio") ? $objRequest->query->get("depende_edificio") : 0 ;
            $strEsEdificio      = $objRequest->query->get("es_edificio") ? $objRequest->query->get("es_edificio") : 0 ;
            $strTipoPersonal    = 'Otros';
            $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
            $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');
            /**
             * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO 
             */
            $arrayResultadoCaracteristicas = $em->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsrCreacion);
            if( !empty($arrayResultadoCaracteristicas) )
            {
                $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
                $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
            }
            $arrayParametros = array (   
                                "login"             => $strLogin,
                                "descripcion_pto"   => $strDescripcionPto,
                                "direccion_pto"     => $strDireccionPto,
                                "estados_pto"       => $strEstadosPto,
                                "negocios_pto"      => $strNegociosPto,
                                "vendedor"          => $strVendedor,
                                "identificacion"    => $strIdentificacion,
                                "nombre"            => $strNombre,
                                "apellido"          => $strApellido,
                                "razon_social"      => $strRazonSocial,
                                "direccion_grl"     => $strDireccionGrl,
                                "depende_edificio"  => $strDependeEdificio,
                                "es_edificio"       => $strEsEdificio
                                );
		
            $strFinTipoDocumento = $objRequest->query->get('fin_doc_tipoDocumento') ? $objRequest->query->get('fin_doc_tipoDocumento') : '';
            $arrayParametros['fin_tipoDocumento']      = $strFinTipoDocumento;
            $arrayParametros['doc_numDocumento']       = $objRequest->query->get('fin_doc_numDocumento') ? $objRequest->query->get('fin_doc_numDocumento'):'';
            $arrayParametros['doc_creador']            = $objRequest->query->get('fin_doc_creador') ? $objRequest->query->get('fin_doc_creador') : '';
            $arrayParametros['doc_estado']             = $objRequest->query->get('fin_doc_estado') ? $objRequest->query->get('fin_doc_estado') : '';
            $arrayParametros['doc_monto']              = $objRequest->query->get('fin_doc_monto') ? $objRequest->query->get('fin_doc_monto') : 0.00 ;
            $arrayParametros['doc_montoFiltro']        = $objRequest->query->get('fin_doc_montoFiltro') ? $objRequest->query->get('fin_doc_montoFiltro'):'i';
            $strDocFechaCreacionDesde                  = explode('T',$objRequest->query->get('fin_doc_fechaCreacionDesde'));
            $strDocFechaCreacionHasta                  = explode('T',$objRequest->query->get('fin_doc_fechaCreacionHasta'));
            $strDocFechaEmisionDesde                   = explode('T',$objRequest->query->get('fin_doc_fechaEmisionDesde'));
            $strDocFechaEmisionHasta                   = explode('T',$objRequest->query->get('fin_doc_fechaEmisionHasta'));	
            $arrayParametros['doc_fechaCreacionDesde'] = $strDocFechaCreacionDesde ? $strDocFechaCreacionDesde[0] : 0 ;
            $arrayParametros['doc_fechaCreacionHasta'] = $strDocFechaCreacionHasta ? $strDocFechaCreacionHasta[0] : 0 ;
            $arrayParametros['doc_fechaEmisionDesde']  = $strDocFechaEmisionDesde ? $strDocFechaEmisionDesde[0] : 0 ;
            $arrayParametros['doc_fechaEmisionHasta']  = $strDocFechaEmisionHasta ? $strDocFechaEmisionHasta[0] : 0 ;
            $arrayParametros['pag_numDocumento']       = $objRequest->query->get('fin_pag_numDocumento') ? 
                                                    $objRequest->query->get('fin_pag_numDocumento') : '';
            $arrayParametros['pag_numReferencia']      = $objRequest->query->get('fin_pag_numReferencia') ? 
                                                    $objRequest->query->get('fin_pag_numReferencia') : '';
            $arrayParametros['pag_numDocumentoRef']    = $objRequest->query->get('fin_pag_numDocumentoRef') ? 
                                                    $objRequest->query->get('fin_pag_numDocumentoRef') : '';
            $arrayParametros['strEstPunto']            = $objRequest->query->get('strEstPunto') ? 
                                                    $objRequest->query->get('strEstPunto') : '';
            $arrayParametros['pag_creador']            = $objRequest->query->get('fin_pag_creador') ? $objRequest->query->get('fin_pag_creador') : '';
            $arrayParametros['pag_formaPago']          = $objRequest->query->get('fin_pag_formaPago') ? $objRequest->query->get('fin_pag_formaPago') : '';
            $arrayParametros['pag_banco']              = $objRequest->query->get('fin_pag_banco') ? $objRequest->query->get('fin_pag_banco') : '';
            $arrayParametros['pag_estado']             = $objRequest->query->get('fin_pag_estado') ? $objRequest->query->get('fin_pag_estado') : '';
            $strPagFechaCreacionDesde               = explode('T',$objRequest->query->get('fin_pag_fechaCreacionDesde'));
            $strPagFechaCreacionHasta               = explode('T',$objRequest->query->get('fin_pag_fechaCreacionHasta'));
            $arrayParametros['pag_fechaCreacionDesde'] = $strPagFechaCreacionDesde ? $strPagFechaCreacionDesde[0] : 0 ;
            $arrayParametros['pag_fechaCreacionHasta'] = $strPagFechaCreacionHasta ? $strPagFechaCreacionHasta[0] : 0 ;
            
            $arrayFinDocFechaAutorizacionDesde          = explode('T',$objRequest->query->get('finDocFechaAutorizacionDesde'));
            $arrayFinDocFechaAutorizacionHasta          = explode('T',$objRequest->query->get('finDocFechaAutorizacionHasta'));		
            $arrayParametros['finDocFechaAutorizacionDesde'] = $arrayFinDocFechaAutorizacionDesde ? $arrayFinDocFechaAutorizacionDesde[0] : 0 ;
            $arrayParametros['finDocFechaAutorizacionHasta'] = $arrayFinDocFechaAutorizacionHasta ? $arrayFinDocFechaAutorizacionHasta[0] : 0 ;
            $arrayParametros['strPrefijoEmpresa']            = $strPrefijoEmpresa;
            $arrayParametros['strTipoPersonal']              = $strTipoPersonal;
            $arrayParametros['intIdPersonEmpresaRol']        = $intIdPersonEmpresaRol;
            
            /**
             * Bloque Fechas de Contabilización Cobranzas
             * 
             * Verifica las fechas de Contabilización para la búsqueda de los documentos del departamento de cobranzas
             */
            $arrayTmpFechasContabilizacion                   = array();
            $arrayTmpFechasContabilizacion['strFechaInicio'] = $objRequest->query->get('strPagFechaContabilizacionDesde') 
                                                               ? $objRequest->query->get('strPagFechaContabilizacionDesde') : '';
            $arrayTmpFechasContabilizacion['strFechaFin']    = $objRequest->query->get('strPagFechaContabilizacionHasta')
                                                               ? $objRequest->query->get('strPagFechaContabilizacionHasta') : '';
            $arrayTmpFechasContabilizacion['strDateFormat']  = 'Y/m/d';
            $arrayTmpFechasContabilizacion['strTimeInicio']  = ' 00:00:00';
            $arrayTmpFechasContabilizacion['strTimeFin']     = ' 23:59:59';
            
            if( !empty($arrayTmpFechasContabilizacion['strFechaInicio']) && !empty($arrayTmpFechasContabilizacion['strFechaFin']) )
            {
                $arrayFechaInicioFin = $this->validadorFechasInicioFin($arrayTmpFechasContabilizacion);
                $arrayParametros['strFinPagFechaContabilizacionDesde'] = $arrayFechaInicioFin['strFechaInicio'];
                $arrayParametros['strFinPagFechaContabilizacionHasta'] = $arrayFechaInicioFin['strFechaFin'];
            }
            else
            {
                $arrayParametros['strFinPagFechaContabilizacionDesde'] = '';
                $arrayParametros['strFinPagFechaContabilizacionHasta'] = '';
            }
            /**
             * Fin del Bloque Fechas de Contabilización Cobranzas
             */
		
            $intStart = $objRequest->query->get('start');
            $intLimit = $objRequest->query->get('limit');

            $intOficinaId = $objRequest->getSession()->get('idOficina');
            $intEmpresaId = $objRequest->getSession()->get('idEmpresa'); 
            
            if( $strFinTipoDocumento == 'FAC'  || 
                $strFinTipoDocumento == 'FACP' || 
                $strFinTipoDocumento == 'NC'   || 
                $strFinTipoDocumento == 'NCI'  || 
                $strFinTipoDocumento == 'ND'   || 
                $strFinTipoDocumento == 'NDI'  || 
                $strFinTipoDocumento == 'DEV'
                )
            {
                    $resultado = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                    ->findBusquedaAvanzadaFinanciera($arrayParametros, $intEmpresaId, $intOficinaId, $intStart, $intLimit);
            }
            else if($strFinTipoDocumento == 'PAG'  || 
                    $strFinTipoDocumento == 'PAGC' || 
                    $strFinTipoDocumento == 'ANT'  || 
                    $strFinTipoDocumento == 'ANTC' || 
                    $strFinTipoDocumento == 'ANTS')
            {
                    $resultado = $em->getRepository('schemaBundle:InfoPagoCab')
                                    ->findBusquedaAvanzadaFinanciera($arrayParametros, $intEmpresaId, $intOficinaId, $intStart, $intLimit);
            }
            
            $numTotal = $resultado['total'];
							
            $i=0;
            if($resultado['registros'] && count($resultado['registros']) > 0)
            {
                foreach ($resultado['registros'] as $dat):
                    if($i % 2==0)
                    {
                        $clase='';
                    }
                    else
                    {
                        $clase='k-alt';
                    }
                    
                    $valorTotal = ($dat['valorTotal'] ? $dat['valorTotal'] : 0.00);	
                    $valorTotal = number_format($valorTotal, 2, '.', '');
                    setlocale(LC_MONETARY, 'en_US');

                    $razonSocial         = (isset($dat['razonSocial']) ? ($dat['razonSocial'] ? $dat['razonSocial'] : "") : "");
                    $nombres             = (isset($dat['nombres']) || isset($dat['apellidos']) ? 
                                            ($dat['nombres'] ? $dat['nombres'] . " " . $dat['apellidos'] : "") : "");
                    $informacion_cliente = ($razonSocial && $razonSocial != "" ? $razonSocial : $nombres);

                    $automatica          = isset($dat['esAutomatica']) ? ($dat['esAutomatica']=="S" ? "Si" : "No") : '';
                    $nombreVendedor      = (isset($dat["nombreVendedor"]) ? 
                                            ($dat["nombreVendedor"] ? 
                                            ucwords(strtolower($dat["nombreVendedor"])) : "") : "");

                    $strNumeroCuentaBanco = '';
                    $strNumeroReferencia  = '';
                    
                    if(isset($dat["numeroCuentaBanco"]))
                    {
                        $strNumeroCuentaBanco = $dat["numeroCuentaBanco"];
                    }
                    
                    if(isset($dat["numeroReferencia"]))
                    {
                        $strNumeroReferencia = $dat["numeroReferencia"];
                    }

                    $strNumeroReferencia = ($strNumeroReferencia ? $strNumeroReferencia : ($strNumeroCuentaBanco ? $strNumeroCuentaBanco : "") ); 

                    $nombreBanco    = "";
                    $strTipoCuenta  = "";
                    if(isset($dat["bancoTipoCuentaId"]))
                    {
                        $bancoTipoCuentaId      = $dat["bancoTipoCuentaId"];
                        $entityBancoTipoCuenta  = $emComercial->getRepository('schemaBundle:AdmiBancoTipoCuenta')->findOneById($bancoTipoCuentaId);
                        
                        if($entityBancoTipoCuenta && count($entityBancoTipoCuenta)>0)
                        {
                            $entityBanco        = $entityBancoTipoCuenta->getBancoId();
                            $nombreBanco        = ($entityBanco ? ($entityBanco->getDescripcionBanco() ? $entityBanco->getDescripcionBanco() : "") : "");
                            
                            $entityTipoCuenta   =  $entityBancoTipoCuenta->getTipoCuentaId();
                            $strTipoCuenta      = ($entityTipoCuenta ? ($entityTipoCuenta->getDescripcionCuenta() ? $entityTipoCuenta->getDescripcionCuenta() : "") : "");
                             
                        }
                    }
                    if(isset($dat["bancoCtaContableId"]))
                    {
                        $bancoCtaContableId     = $dat["bancoCtaContableId"];
                        $entityBancoCtaContable = $em->getRepository('schemaBundle:AdmiBancoCtaContable')->findOneById($bancoCtaContableId);						
                        if($entityBancoCtaContable && count($entityBancoCtaContable)>0)
                        {							
                            $entityBancoTipoCuenta = $entityBancoCtaContable->getBancoTipoCuentaId();
                            if($entityBancoTipoCuenta && count($entityBancoTipoCuenta)>0)
                            {
                                $entityBanco        = $entityBancoTipoCuenta->getBancoId();
                                $nombreBanco        = ($entityBanco ? ($entityBanco->getDescripcionBanco() ? $entityBanco->getDescripcionBanco() : "") : "");
                                
                                $entityTipoCuenta   =  $entityBancoTipoCuenta->getTipoCuentaId();
                                $strTipoCuenta      = ($entityTipoCuenta ? ($entityTipoCuenta->getDescripcionCuenta() ? $entityTipoCuenta->getDescripcionCuenta() : "") : "");
                                
                            }
                        }
                    }
                    $nombreBancoEmpresa="";
                    if(isset($dat["cuentaContableId"]))
                    {
                        $cuentaContableId         = $dat["cuentaContableId"];
                        $entityAdmiCuentaContable = $em->getRepository('schemaBundle:AdmiCuentaContable')->findOneById($cuentaContableId);						
                        if($entityAdmiCuentaContable && count($entityAdmiCuentaContable)>0)
                        {							
                            $nombreBancoEmpresa = $entityAdmiCuentaContable->getDescripcion()." ".$entityAdmiCuentaContable->getNoCta();
                        }
                    }                    
                    $nombreBanco        = ucwords(strtolower(trim($nombreBanco)));					
                    $nombreBancoEmpresa = ucwords(strtolower(trim($nombreBancoEmpresa)));
                    
                    $referenciaId = ""; $noDocumentoReferencia  = ""; $codigoDocumentoReferencia  = ""; $nombreDocumentoReferencia  = "";
                    if(isset($dat["referenciaId"]))
                    {
                        if($dat["referenciaId"] && $dat["referenciaId"]!="")
                        {
                            $referenciaId       = $dat["referenciaId"];
                            $entityReferencia   = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->findOneById($referenciaId);
                            if($entityReferencia && count($entityReferencia)>0)
                            {
                                $noDocumentoReferencia = (  $entityReferencia ?
                                                            ($entityReferencia->getNumeroFacturaSri() ? 
                                                            $entityReferencia->getNumeroFacturaSri() : "") : "");

                                $tipoDocumentoReferenciaId = ($entityReferencia ? 
                                                             ($entityReferencia->getTipoDocumentoId() ? 
                                                              $entityReferencia->getTipoDocumentoId() : "") : "");
                                
                                $entityTipoDocumento = $em->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                          ->findOneById($tipoDocumentoReferenciaId);								
                                
                                if($entityTipoDocumento && count($entityTipoDocumento)>0)
                                {
                                    $codigoDocumentoReferencia = ($entityTipoDocumento ? 
                                                                 ($entityTipoDocumento->getCodigoTipoDocumento() ? 
                                                                  $entityTipoDocumento->getCodigoTipoDocumento() : "") : "");
                                    
                                    $nombreDocumentoReferencia = ($entityTipoDocumento ? 
                                                                 ($entityTipoDocumento->getNombreTipoDocumento() ? 
                                                                  $entityTipoDocumento->getNombreTipoDocumento() : "") : "");
                                }
                            }//fin entityReferencia
                        }//fin referenciaId
                    }

                    $nombreCreador = "Migracion";
                    $empleado      = $emComercial->getRepository('schemaBundle:InfoPersona')->getPersonaPorLogin($dat["usrCreacion"]);
                    
                    if($empleado && count($empleado)>0)
                    {
                        $nombreCreador = $empleado->getNombres().' '.$empleado->getApellidos();
                    }
                    $nombreCreador  = ucwords(strtolower(trim($nombreCreador)));
                    $nombreVendedor = ucwords(strtolower(trim($nombreVendedor)));
                    
                    $strUsrUltModificacion = "";
                    $strFeUltModificacion  = "";
                    
                    if(isset($dat['estadoDocumentoGlobal']) && $dat['estadoDocumentoGlobal']=== 'Eliminado' && $strPrefijoEmpresa==='TN' &&
                        $strFinTipoDocumento !== 'PAG'  && 
                        $strFinTipoDocumento !== 'PAGC' && 
                        $strFinTipoDocumento !== 'ANT'  && 
                        $strFinTipoDocumento !== 'ANTC' && 
                        $strFinTipoDocumento !== 'ANTS')
                    {
                        $arrayReult = $em->getRepository('schemaBundle:InfoDocumentoHistorial')->getMaxIdHistorial($dat['id_documento']);
                        $intMaxIdHistorial = $arrayReult[0]['id'];
                            
                        if(isset($intMaxIdHistorial) && $intMaxIdHistorial > 0)
                        {
                            $objMaxHistorial   = $em->getRepository('schemaBundle:InfoDocumentoHistorial')->find($intMaxIdHistorial);
                            if(is_object($objMaxHistorial))
                            {
                                $strUsrUltModificacion = $objMaxHistorial->getUsrCreacion();
                                $objUsrUltMod          = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                     ->getPersonaPorLogin($strUsrUltModificacion);
                                if(is_object($objUsrUltMod))
                                {
                                    $strUsrUltModificacion = $objUsrUltMod->getNombres().' '.$objUsrUltMod->getApellidos();
                                }
                                $strUsrUltModificacion  = ucwords(strtolower(trim($strUsrUltModificacion)));                            
                                $strFeUltModificacion   = strval(date_format($objMaxHistorial->getFeCreacion(),"d/m/Y G:i"));
                            }
                        }
                    }

                    if(isset($dat['feDeposito']))
                    {
                        if($dat['feDeposito']!="")
                            $fecha_deposito = strval(date_format($dat['feDeposito'],"d/m/Y G:i"));
                        else
                            $fecha_deposito = "";
                    }
                    else
                    {
                        $fecha_deposito = "";
                    }
                    
                    if(isset($dat['feProcesado']))
                    {
                        if($dat['feProcesado']!="")
                            $fecha_procesado = strval(date_format($dat['feProcesado'],"d/m/Y G:i"));
                        else
                            $fecha_procesado = "";
                    }
                    else
                        $fecha_procesado = "";


                     //Fecha de cruce
                    if(isset($dat['fechaCruce']))
                    {
                        if($dat['fechaCruce'])
                        {
                            $fecha_cruce = strval(date_format($dat['fechaCruce'],"d/m/Y G:i"));
                        }
                        else
                        {
                            $fecha_cruce = "";
                        }
                        }
                    else
                    {
                        $fecha_cruce="";     
                    }

                    $strNumeroComprobante = "";
                    
                    //Entidad oficina - para la presentacion en el pago
                    $entityOficina = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($dat['oficinaId']);

                    //Cambio de plan
                    //Para la presentacion de la oficina segun pago o facturas
                    $oficina_presentar = "";

                    if( $strFinTipoDocumento == 'PAG'  || 
                        $strFinTipoDocumento == 'PAGC' || 
                        $strFinTipoDocumento == 'ANT'  || 
                        $strFinTipoDocumento == 'ANTC' || 
                        $strFinTipoDocumento == 'ANTS')
                    {
                        $intIdPagoDet       = ( (isset($dat['id_documento_detalle']) && !empty($dat['id_documento_detalle'])) 
                                                 ? $dat['id_documento_detalle'] : 0 );
                        $strCodigoFormaPago = ( (isset($dat['codigoFormaPago']) && !empty($dat['codigoFormaPago'])) 
                                                 ? $dat['codigoFormaPago'] : '' );

                        $arrayParametrosComprobante = array('intIdPagoDet' => $intIdPagoDet, 'strCodigoFormaPago' => $strCodigoFormaPago);

                        $strNumeroComprobante = $em->getRepository('schemaBundle:InfoPagoCab')->getNumeroComprobante($arrayParametrosComprobante);
                        
                        $oficina_presentar = $entityOficina->getNombreOficina();
                    }
                    else
                    {
                        $oficina_presentar = $dat['nombreOficina'];
                    }
                    
                    if(isset($dat['feEmision']))
                    {
                        if($dat['feEmision']!="")
                        {
                            $fecha_emision = strval(date_format($dat['feEmision'],"d/m/Y G:i"));
                        }
                        else
                        {
                            $fecha_emision = "";
                        }
                    }
                    else
                    {
                        $fecha_emision = "";
                    }
                    
                    if(isset($dat['feAutorizacion']))
                    {
                        if($dat['feAutorizacion']!="")
                        {
                            $strFeAutorizacion = strval(date_format($dat['feAutorizacion'],"d/m/Y G:i"));
                        }
                        else
                        {
                            $strFeAutorizacion = "";
                        }
                    }
                    else
                    {
                        $strFeAutorizacion = "";
                    }
                    
                    $referencia_nd = "";
                    $comentario_nd = "";

                    if( $strFinTipoDocumento=='ND' || 
                        $strFinTipoDocumento=='NDI'||
                        $strFinTipoDocumento=='DEV')
                    {
                        //saco con el id_documento el det y el pago_det_id
                        //obtengo el numero_pago y lo pongo en la referencia
                        $referencia_nd = "";
                        $comentario_nd = "";
                        $nd_det = $em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($dat['id_documento']);
                        foreach ($nd_det as $nd):
                            $pago_det       =$em->getRepository('schemaBundle:InfoPagoDet')->find($nd->getPagoDetId());
                            $referencia_nd .= "|".$pago_det->getPagoId()->getNumeroPago();
                            $comentario_nd .= "|".$nd->getObservacionesFacturaDetalle();
                        endforeach;

                        $referencia = $referencia_nd;
                    }

                    if( (strtoupper($dat['codigoTipoDocumento'])=='ANTS')&&
                        (strtoupper($dat['estadoDocumentoGlobal'])=='CERRADO' || 
                         strtoupper($dat['estadoDocumentoGlobal'])=='ASIGNADO'))
                    {
                        if(strtoupper($dat['estadoDocumentoGlobal'])=='CERRADO')
                        {
                            $pagosPorAnticipo = $em->getRepository('schemaBundle:InfoPagoCab')->findById($dat['id_documento']);
                        }
                        elseif(strtoupper($dat['estadoDocumentoGlobal'])=='ASIGNADO')
                        {
                            $pagosPorAnticipo = $em->getRepository('schemaBundle:InfoPagoCab')->findByAnticipoId($dat['id_documento']);
                        }
                        
                        foreach($pagosPorAnticipo as $objInfoPagoCab)
                        {
                            $objPunto            = $emComercial->getRepository('schemaBundle:InfoPunto')->find($objInfoPagoCab->getPuntoId());
                            $razonSocialTxt      = $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial();
                            $nombresTxt          = $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getNombres();
                            $apellidosTxt        = $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getApellidos();
                            $razonSocial         = (isset($razonSocialTxt) ? ($razonSocialTxt ? $razonSocialTxt : "") : "");
                            $nombres             = (isset($nombresTxt) || isset($apellidosTxt) ? 
                                                   ($nombresTxt ? $nombresTxt . " " . $apellidosTxt : "") : "");
                            $informacion_cliente = ($razonSocial && $razonSocial != "" ? $razonSocial : $nombres);                        
                        }
                    }                    
                    
                    $documentos[]= array(
                                            'id'                    => $i,
                                            'idClienteSucursal'     => "",
                                            'oficinaId'             => $entityOficina->getNombreOficina(),
                                            'idPunto'               => (isset($dat['id_punto']) ? ($dat['id_punto'] ? $dat['id_punto'] : 0) : 0),
                                            'identificacion'        => (isset($dat['identificacionCliente']) ? $dat['identificacionCliente'] : ""),
                                            'calificacion'          => (isset($dat['calificacionCrediticia']) ? $dat['calificacionCrediticia'] : ""),
                                            'fechaAprobacion'       => "",
                                            'login1'                => (isset($dat['login']) ? $dat['login'] : ""),
                                            'Punto'                 => (isset($dat['descripcionPunto']) ? 
                                                                        ucwords(strtolower(trim($dat['descripcionPunto']))) : ""),
                                            'idEstadoPtoCliente'    => (isset($dat['estado']) ? $dat['estado'] : ""),
                                            'opt_enlace_servicio'   => "",
                                            'opt_cliente_sucursal'  => "",
                                            'vendedor'              => trim($nombreVendedor),
                                            'idDocumento'           => $dat['id_documento'],
                                            'idDocumentoDetalle'    => (isset($dat['id_documento_detalle']) ? $dat['id_documento_detalle'] : ""),
                                            'comentarioPago'        => (isset($dat['comentarioPago']) ? $dat['comentarioPago'] : ""),	
                                            'comentarioDetallePago' => (isset($dat['comentarioDetallePago']) ? $dat['comentarioDetallePago'] : ""),		
                                            'codigoTipoDocumento'   => $dat['codigoTipoDocumento'],
                                            'nombreTipoDocumento'   => $dat['nombreTipoDocumento'],
                                            'NumeroDocumento'       => $dat['numeroDocumento'],
                                            'descripcionFormaPago'  => (isset($dat['descripcionFormaPago']) ? 
                                                                        ucwords(strtolower($dat['descripcionFormaPago'])) : ""),
                                            'Cliente'               => ucwords(strtolower(trim($informacion_cliente))),
                                            'Esautomatica'          => $automatica,
                                            'ValorTotal'            => $valorTotal,
                                            'referencia'            => $strNumeroReferencia,
                                            'nombreBanco'           => trim($nombreBanco),
                                            'strTipoCuenta'         => trim($strTipoCuenta),
                                            'referenciaId'          => $referenciaId,
                                            'NumeroDocumentoRef'    => $noDocumentoReferencia,
                                            'NombreDocumentoRef'    => trim($nombreDocumentoReferencia),
                                            'CodigoDocumentoRef'    => trim($codigoDocumentoReferencia),
                                            'nombreCreador'         => $nombreCreador,
                                            'strUsrUltModificacion' => $strUsrUltModificacion,
                                            'Estado'                => $dat['estadoDocumentoGlobal']?$dat['estadoDocumentoGlobal']:'',
                                            'FeEmision'             => $fecha_emision,
                                            'Fecreacion'            => strval(date_format($dat['feCreacion'],"d/m/Y G:i")),
                                            'strFeUltModificacion'  => $strFeUltModificacion,
                                            'strFeAutorizacion'     => $strFeAutorizacion,
                                            'Fedeposito'            => $fecha_deposito,
                                            'Feprocesado'           => $fecha_procesado,
                                            'FechaCruce'            => $fecha_cruce,
                                            'NoComprobanteDeposito' => $strNumeroComprobante,
                                            'nombreBancoEmpresa'    => trim($nombreBancoEmpresa),
                                            'clase'                 => $clase,
                                            'action1'               => 'button-grid-show',
                                            'boton'                 => "",
                                            'tipoNegocio'           => (isset($dat['codigoTipoNegocio']) ? $dat['codigoTipoNegocio'] : "")
                                        );
                    $i++;
                endforeach;//fin foreach ($resultado['registros'] as $dat):
            }
			
            if (!empty($documentos))
            {
                $dataF   = json_encode($documentos);
                $objJson = '{"total":"'.$numTotal.'","encontrados":'.$dataF.'}';
            }
            else
            {        
                $objJson = '{"total":"0","encontrados":[]}';
            }	
			
        }
        catch (Exception $e) 
        {
            $em->getConnection()->close();
            $objJson = '{"total":"0","encontrados":[]}';
        }	
        $objResponse->setContent($objJson);
        return $objResponse;
    }
        
    /**
     * exportarConsulta_BusquedaFinancieraAction
     * Metodo que permite enviar los parametros para la generacion del Excel 
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.1 04-08-2016
     * @since 1.0
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 19-08-2016 - Se agregan las variables '$arrayFinDocFechaAutorizacionDesde', '$arrayFinDocFechaAutorizacionHasta' para exportar el 
     *                           reporte de documentos financieros por fecha de autorización
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.3 16-09-2016 - Se realiza cambio para que se invoque funcion que ejecute procedimiento en la base de datos que genera y envia por 
     *                         - correo el reporte generado
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.4 14-10-2016 - Se agrega control de excepciones, se cambia forma de obtener id de la persona en sesión .
     *                           (por medio de id_empresa_rol en sesión). 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 19-12-2016 - Se agregan las variables 'strFinPagFechaContabilizacionDesde', 'strFinPagFechaContabilizacionHasta' para 
     *                           realizar la búsqueda por fechas con las cuales se contabilizan los documentos del departamento de cobranzas
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.6 03-04-2017 - Se añade el tipo 'NCI' para que se pueda exportar la información respectiva de las notas de crédito internas
     *
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.7 31-07-2017 - Se añade el nuevo filtro de Estao del Punto
     */
    public function exportarConsulta_BusquedaFinancieraAction()
    {
        $respuesta   = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion      = $this->get('request');
        $session       = $peticion->getSession();
        $emComercial   = $this->getDoctrine()->getManager('telconet');
        $em            = $this->getDoctrine()->getManager('telconet_financiero');
        $serviceUtil   = $this->get('schema.Util');
        $strIpClient   = $peticion->getClientIp();
        $strUsrSesion  = $session->get('user');
        $em->getConnection()->beginTransaction();

        $login            = "";
        $descripcion_pto  = "";
        $direccion_pto    = "";
        $estados_pto      = "";
        $negocios_pto     = "";
        $vendedor         = "";
        $identificacion   = "";
        $nombre           = "";
        $apellido         = "";
        $razon_social     = "";
        $direccion_grl    = "";
        $depende_edificio = "";
        $es_edificio      = "";
        $strMensaje       = "No se realizó la consulta.";
		try
		{
			$parametros = array (   "login"             => $login,
                                    "descripcion_pto"   => $descripcion_pto,
                                    "direccion_pto"     => $direccion_pto,
                                    "estados_pto"       => $estados_pto,
                                    "negocios_pto"      => $negocios_pto,
                                    "vendedor"          => $vendedor,
                                    "identificacion"    => $identificacion,
                                    "nombre"            => $nombre,
                                    "apellido"          => $apellido,
                                    "razon_social"      => $razon_social,
                                    "direccion_grl"     => $direccion_grl,
                                    "depende_edificio"  => $depende_edificio,
                                    "es_edificio"       => $es_edificio
								);

		
            $parametros['fin_tipoDocumento']        = $fin_tipoDocumento = $peticion->query->get('fin_doc_tipoDocumento') ? 
                                                                           $peticion->query->get('fin_doc_tipoDocumento') : '';
            $parametros['fin_tipoDocumento_texto']  = $peticion->query->get('fin_doc_tipoDocumento_texto') ? 
                                                      $peticion->query->get('fin_doc_tipoDocumento_texto') : '';
            $parametros['doc_numDocumento']         = $peticion->query->get('fin_doc_numDocumento') ? 
                                                      $peticion->query->get('fin_doc_numDocumento') : '';
            $parametros['doc_creador']              = $peticion->query->get('fin_doc_creador') ? $peticion->query->get('fin_doc_creador') : '';
            $parametros['doc_estado']               = $peticion->query->get('fin_doc_estado') ? $peticion->query->get('fin_doc_estado') : '';
            $parametros['doc_estado_texto']         = $peticion->query->get('fin_doc_estado_texto') ? 
                                                      $peticion->query->get('fin_doc_estado_texto') : '';
            $parametros['doc_monto']                = $peticion->query->get('fin_doc_monto') ? $peticion->query->get('fin_doc_monto') : 0.00 ;
            $parametros['doc_montoFiltro']          = $peticion->query->get('fin_doc_montoFiltro') ? 
                                                      $peticion->query->get('fin_doc_montoFiltro') : 'i';
            $parametros['doc_montoFiltro_texto']    = $peticion->query->get('fin_doc_montoFiltro_texto') ? 
                                                      $peticion->query->get('fin_doc_montoFiltrot_texto') : 'igual que';
            $doc_fechaCreacionDesde                 = explode('T',$peticion->query->get('fin_doc_fechaCreacionDesde'));
            $doc_fechaCreacionHasta                 = explode('T',$peticion->query->get('fin_doc_fechaCreacionHasta'));
            $doc_fechaEmisionDesde                  = explode('T',$peticion->query->get('fin_doc_fechaEmisionDesde'));
            $doc_fechaEmisionHasta                  = explode('T',$peticion->query->get('fin_doc_fechaEmisionHasta'));		
            $parametros['doc_fechaCreacionDesde']   = $doc_fechaCreacionDesde ? $doc_fechaCreacionDesde[0] : 0 ;
            $parametros['doc_fechaCreacionHasta']   = $doc_fechaCreacionHasta ? $doc_fechaCreacionHasta[0] : 0 ;
            $parametros['doc_fechaEmisionDesde']    = $doc_fechaEmisionDesde ? $doc_fechaEmisionDesde[0] : 0 ;
            $parametros['doc_fechaEmisionHasta']    = $doc_fechaEmisionHasta ? $doc_fechaEmisionHasta[0] : 0 ;
            $parametros['pag_numDocumento']         = $peticion->query->get('fin_pag_numDocumento') ? 
                                                      $peticion->query->get('fin_pag_numDocumento') : '';			
            $parametros['pag_numReferencia']        = $peticion->query->get('fin_pag_numReferencia') ? 
                                                      $peticion->query->get('fin_pag_numReferencia') : '';
            $parametros['pag_numDocumentoRef']      = $peticion->query->get('fin_pag_numDocumentoRef') ? 
                                                      $peticion->query->get('fin_pag_numDocumentoRef') : '';
            $parametros['strEstPunto']              = $peticion->query->get('strEstPunto') ? 
                                                      $peticion->query->get('strEstPunto') : '';
            $parametros['pag_creador']              = $peticion->query->get('fin_pag_creador') ? 
                                                      $peticion->query->get('fin_pag_creador') : '';
            $parametros['pag_formaPago']            = (($peticion->query->get('fin_pag_formaPago') && 
                                                        $peticion->query->get('fin_pag_formaPago') != "null") ? 
                                                        $peticion->query->get('fin_pag_formaPago') : '');
            $parametros['pag_formaPago_texto']      = $peticion->query->get('fin_pag_formaPago_texto') ? 
                                                      $peticion->query->get('fin_pag_formaPago_texto') : '';
            $parametros['pag_banco']                = (($peticion->query->get('fin_pag_banco') && 
                                                        $peticion->query->get('fin_pag_banco') != "null") ? 
                                                        $peticion->query->get('fin_pag_banco') : '');
            $parametros['pag_banco_texto']          = $peticion->query->get('fin_pag_banco_texto') ? 
                                                      $peticion->query->get('fin_pag_banco_texto') : '';
            $parametros['pag_estado']               = (($peticion->query->get('fin_pag_estado') && 
                                                        $peticion->query->get('fin_pag_estado') != "null") ? 
                                                        $peticion->query->get('fin_pag_estado') : '');
            $parametros['pag_estado_texto']         = $peticion->query->get('fin_pag_estado_texto') ? 
                                                      $peticion->query->get('fin_pag_estado_texto') : '';

            $pag_fechaCreacionDesde                 = $peticion->query->get('fin_pag_fechaCreacionDesde');
            $pag_fechaCreacionHasta                 = $peticion->query->get('fin_pag_fechaCreacionHasta');
            $parametros['pag_fechaCreacionDesde']   = $pag_fechaCreacionDesde ? $pag_fechaCreacionDesde : 0 ;
            $parametros['pag_fechaCreacionHasta']   = $pag_fechaCreacionHasta ? $pag_fechaCreacionHasta : 0 ;
            
            
            $arrayFinDocFechaAutorizacionDesde          = explode('T',$peticion->query->get('finDocFechaAutorizacionDesde'));
            $arrayFinDocFechaAutorizacionHasta          = explode('T',$peticion->query->get('finDocFechaAutorizacionHasta'));		
            $parametros['finDocFechaAutorizacionDesde'] = $arrayFinDocFechaAutorizacionDesde ? $arrayFinDocFechaAutorizacionDesde[0] : 0 ;
            $parametros['finDocFechaAutorizacionHasta'] = $arrayFinDocFechaAutorizacionHasta ? $arrayFinDocFechaAutorizacionHasta[0] : 0 ;	
            
            
            /**
             * Bloque Fechas de Contabilización Cobranzas
             * 
             * Verifica las fechas de Contabilización para la búsqueda de los documentos del departamento de cobranzas
             */
            $arrayTmpFechasContabilizacion                   = array();
            $arrayTmpFechasContabilizacion['strFechaInicio'] = $peticion->query->get('strPagFechaContabilizacionDesde') 
                                                               ? $peticion->query->get('strPagFechaContabilizacionDesde') : '';
            $arrayTmpFechasContabilizacion['strFechaFin']    = $peticion->query->get('strPagFechaContabilizacionHasta')
                                                               ? $peticion->query->get('strPagFechaContabilizacionHasta') : '';
            $arrayTmpFechasContabilizacion['strDateFormat']  = 'd/m/y';
            $arrayTmpFechasContabilizacion['strTimeInicio']  = '';
            $arrayTmpFechasContabilizacion['strTimeFin']     = '';
            
            if( !empty($arrayTmpFechasContabilizacion['strFechaInicio']) && !empty($arrayTmpFechasContabilizacion['strFechaFin']) )
            {
                $arrayFechaInicioFin = $this->validadorFechasInicioFin($arrayTmpFechasContabilizacion);
                $parametros['strFinPagFechaContabilizacionDesde'] = $arrayFechaInicioFin['strFechaInicio'];
                $parametros['strFinPagFechaContabilizacionHasta'] = $arrayFechaInicioFin['strFechaFin'];
            }
            else
            {
                $parametros['strFinPagFechaContabilizacionDesde'] = '';
                $parametros['strFinPagFechaContabilizacionHasta'] = '';
            }
            /**
             * Fin del Bloque Fechas de Contabilización Cobranzas
             */

            $start      = $peticion->query->get('start');
            $limit      = $peticion->query->get('limit');
            $oficinaId  = $peticion->getSession()->get('idOficina');
            $empresaId  = $peticion->getSession()->get('idEmpresa'); 
            $resultado  = ""; 
            
            $parametros['intEmpresaId']   = $empresaId;
            $parametros['intOficinaId']   = $oficinaId;
            $parametros['start']          = $start;
            $parametros['limit']          = $limit; 
            $parametros['usrSesion']      = $strUsrSesion;
            $parametros['prefijoEmpresa'] = trim($session->get('prefijoEmpresa'));
            $parametros['emailUsrSesion'] = "";
            $strValorFormaContacto        = ""; 
            
            $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                    ->find($session->get('idPersonaEmpresaRol'));

            if(is_object($objInfoPersonaEmpresaRol))
            {
                $strValorFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                     ->getValorFormaContactoPorCodigo($objInfoPersonaEmpresaRol->getPersonaId(),'MAIL');
                
                if(!is_null($strValorFormaContacto))
                {
                    $parametros['emailUsrSesion'] = strtolower($strValorFormaContacto);
                }                
            }         

              
            if( $fin_tipoDocumento == 'FAC'  || 
                $fin_tipoDocumento == 'FACP' || 
                $fin_tipoDocumento == 'NC'   || 
                $fin_tipoDocumento == 'NCI'  || 
                $fin_tipoDocumento == 'ND'   || 
                $fin_tipoDocumento == 'NDI'  ||
                $fin_tipoDocumento == 'DEV')
            {
                $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->ejecutarEnvioReporteFacturacion($parametros);  
            }
            else if($fin_tipoDocumento == 'PAG'  || 
                    $fin_tipoDocumento == 'PAGC' || 
                    $fin_tipoDocumento == 'ANT'  || 
                    $fin_tipoDocumento == 'ANTC' || 
                    $fin_tipoDocumento == 'ANTS')
            { 
                $resultado = $em->getRepository('schemaBundle:InfoPagoCab')->ejecutarEnvioReporteCobranzas($parametros);
            }
          
            // Registro de historial de generación de reporte
            $objInfoReporteHistorial = new InfoReporteHistorial();
            $objInfoReporteHistorial->setEmpresaCod(trim($parametros['prefijoEmpresa']));
            $objInfoReporteHistorial->setCodigoTipoReporte(trim($fin_tipoDocumento));
            $objInfoReporteHistorial->setFeCreacion(new \DateTime('now'));
            $objInfoReporteHistorial->setUsrCreacion($session->get('user'));
            $objInfoReporteHistorial->setEmailUsrCreacion($parametros['emailUsrSesion']);
            $objInfoReporteHistorial->setEstado('Activo');
            $objInfoReporteHistorial->setAplicacion('Telcos'); 
            $em->persist($objInfoReporteHistorial);
            $em->flush();            
            $em->getConnection()->commit();
            
            $strMensaje = 'Reporte generado y enviado exitosamente.';
        }
        catch (\Exception $e) {
            $em->getConnection()->rollback();	
            $em->getConnection()->close();
            $strMensaje= 'Error al generar reporte .'. $e->getMessage();
            $serviceUtil->insertError('Telcos+', 
                                      'ReportesController.exportarConsulta_BusquedaFinancieraAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );
        }
        
        $respuesta->setContent($strMensaje);
        return $respuesta;         
	}

    /**
     * guardarParametros_ReportePagosAction
     * Metodo que permite guardar los parametros seleccionados por el usuario para la generacion
     * del reporte automatico de Pagos diario y mensual
     *
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.0 11-07-2017
     *
     * @return Response
     */
    public function guardarParametros_ReportePagosAction()
    {
        $objRespuesta     = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objPeticion      = $this->get('request');
        $objSession       = $objPeticion->getSession();
        $emFinanciero  = $this->getDoctrine()->getManager('telconet_financiero');
        $serviceUtil   = $this->get('schema.Util');
        $strIpClient   = $objPeticion->getClientIp();
        $strUsrSesion  = $objSession->get('user');
        $strCodEmpresa = $objSession->get('idEmpresa');
        $emFinanciero->getConnection()->beginTransaction();
        $strMensaje    = "No se realizó la consulta.";

        $strTipoDocumento = "";
        $strEstadoPunto   = "";
        $strEstadoPago    = "";
        $strFormaPago     = "";

        try
        {
            $arrayPermisos=array('strParametro' => 'CONF_PERM_EMPRE');
            $strGetParametroEmp = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')->obtenerParametroConfig($arrayPermisos);
            if ($strCodEmpresa == $strGetParametroEmp || is_null($strGetParametroEmp))
            {
                $arrayParametros = array ("strTipoDocumento"  => $strTipoDocumento,
                                          "strEstadoPunto"    => $strEstadoPunto,
                                          "strEstadoPago"     => $strEstadoPago,
                                          "strFormaPago"      => $strFormaPago,
                                          "strUsrSesion"      => $strUsrSesion,
                                          "strIpClient"       => $strIpClient,
                                          "strCodEmpresa"     => $strCodEmpresa);

                $arrayParametros['strTipoDocumento']     = $objPeticion->query->get('strTipoDocumento') ? 
                                                           $objPeticion->query->get('strTipoDocumento') : '';
                $arrayParametros['strEstadoPunto']       = $objPeticion->query->get('strEstadoPunto') ? 
                                                           $objPeticion->query->get('strEstadoPunto') : '';
                $arrayParametros['strEstadoPago']        = $objPeticion->query->get('strEstadoPago') ? 
                                                           $objPeticion->query->get('strEstadoPago') : '';
                $arrayParametros['strFormaPago']         = $objPeticion->query->get('strFormaPago') ? 
                                                           $objPeticion->query->get('strFormaPago') : '';

                $emFinanciero->getRepository('schemaBundle:InfoPagoCab')->guardaParametrosReportePagos($arrayParametros);

                $strMensaje = 'Datos Guardados exitosamente.';
            }
            else
            {
                $strMensaje = 'No tiene acceso a la configuracion de Reporte';
            }
        }
        catch (\Exception $e)
        {
            $emFinanciero->getConnection()->rollback();
            $emFinanciero->getConnection()->close();
            $strMensaje= 'Error al guardar parametros de configuracion.';
            $serviceUtil->insertError('Telcos+',
                                      'ReportesController.guardarParametros_ReportePagosAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }

        $objRespuesta->setContent($strMensaje);
        return $objRespuesta;
    }

    /**
     * Genera archivo xls segun la consulta realizada en la opcion de busqueda financiera
     *
     * @author : telcos       
     * @version 1.0 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.1 2016-08-04 Se corrige seteo de columnas a mostrar en el reporte
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 16-08-2016 - Se corrige que cuando el pago no sea depositado se verifique si tiene guardado una fecha de deposito en el objeto
     *                          '$objInfoPagoDet' en caso contrario mostrar vacío el campo de 'Fecha Deposito' en el reporte.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 19-08-2016 - Se agregan las fechas de autorización seleccionadas por el usuario al reporte.
     * @param array   $datos
     * @param object  $emFinanciero
     * @param object  $em 
     * @param array   $parametros
     * @param String  $usuario
     */        
    public static function generateExcelConsulta_BusquedaFinanciera($datos, $emFinanciero, $em, $parametros, $usuario)
    {
        error_reporting(E_ALL);        
        $objPHPExcel    = new PHPExcel();
        $cacheMethod    = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings  = array( ' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objReader      = PHPExcel_IOFactory::createReader('Excel5');

        if($parametros['fin_tipoDocumento'] == 'ANTS')
        {
            $objPHPExcel = $objReader->load(__DIR__."/../Resources/templatesExcel/templateBusquedaFinancieraSC.xls");
        }
        else if($parametros['fin_tipoDocumento'] == 'FAC'  || 
                $parametros['fin_tipoDocumento'] == 'FACP' || 
                $parametros['fin_tipoDocumento'] == 'ND'   || 
                $parametros['fin_tipoDocumento'] == 'NC'   ||
                $parametros['fin_tipoDocumento'] == 'NDI'  ||
                $parametros['fin_tipoDocumento'] == 'DEV')
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
		
        if( $parametros['fin_tipoDocumento'] == 'FAC'  || 
            $parametros['fin_tipoDocumento'] == 'FACP' || 
            $parametros['fin_tipoDocumento'] == 'ND'   || 
            $parametros['fin_tipoDocumento'] == 'NDI'  || 
            $parametros['fin_tipoDocumento'] == 'DEV'  || 
            $parametros['fin_tipoDocumento'] == 'NC')
        {
            $objPHPExcel->getActiveSheet()->setCellValue('B11',''.($parametros['doc_numDocumento']=="")?"Todos":$parametros['doc_numDocumento']);
            $objPHPExcel->getActiveSheet()->setCellValue('B12',''.($parametros['doc_monto']=="")?'Todos': $parametros['doc_monto']);
            $objPHPExcel->getActiveSheet()->setCellValue('B13',''.($parametros['doc_monto']=="")?'Todos': $parametros['doc_montoFiltro_texto']);    
            $objPHPExcel->getActiveSheet()->setCellValue('B14',''.($parametros['doc_estado']=="")?'Todos': $parametros['doc_estado_texto']);
            $objPHPExcel->getActiveSheet()->setCellValue('B15',''.($parametros['doc_creador']=="")?'Todos': $parametros['doc_creador']);		
            $objPHPExcel->getActiveSheet()->setCellValue('C15',''.($parametros['finDocFechaAutorizacionDesde']=="")?
                                                        'Todos': $parametros['finDocFechaAutorizacionDesde']);
            $objPHPExcel->getActiveSheet()->setCellValue('C16',''.($parametros['finDocFechaAutorizacionHasta']=="")?
                                                        'Todos': $parametros['finDocFechaAutorizacionHasta']);
            $objPHPExcel->getActiveSheet()->setCellValue('C17',''.($parametros['doc_fechaCreacionDesde']=="")?
                                                        'Todos': $parametros['doc_fechaCreacionDesde']);
            $objPHPExcel->getActiveSheet()->setCellValue('C18',''.($parametros['doc_fechaCreacionHasta']=="")?
                                                        'Todos': $parametros['doc_fechaCreacionHasta']);
            $objPHPExcel->getActiveSheet()->setCellValue('C19',''.($parametros['doc_fechaEmisionDesde']=="")?
                                                        'Todos': $parametros['doc_fechaEmisionDesde']);
            $objPHPExcel->getActiveSheet()->setCellValue('C20',''.($parametros['doc_fechaEmisionHasta']=="")?
                                                        'Todos': $parametros['doc_fechaEmisionHasta']);
        }
        if($parametros['fin_tipoDocumento'] == 'PAG'  || 
           $parametros['fin_tipoDocumento'] == 'PAGC' || 
           $parametros['fin_tipoDocumento'] == 'ANT'  || 
           $parametros['fin_tipoDocumento'] == 'ANTC' || 
           $parametros['fin_tipoDocumento'] == 'ANTS')
        {
            $objPHPExcel->getActiveSheet()->setCellValue('G11',''.($parametros['pag_numDocumento']=="")?"Todos":$parametros['pag_numDocumento']." ");
            $objPHPExcel->getActiveSheet()->setCellValue('G12',''.($parametros['pag_numReferencia']=="")?
                                                        "Todos":$parametros['pag_numReferencia']." ");
            $objPHPExcel->getActiveSheet()->setCellValue('G13',''.($parametros['pag_numDocumentoRef']=="")?
                                                        "Todos":$parametros['pag_numDocumentoRef']." ");
            $objPHPExcel->getActiveSheet()->setCellValue('G14',''.($parametros['pag_creador']=="")?'Todos': $parametros['pag_creador']);
            $objPHPExcel->getActiveSheet()->setCellValue('G15',''.($parametros['pag_formaPago']=="")?'Todos': $parametros['pag_formaPago_texto']);	
            $objPHPExcel->getActiveSheet()->setCellValue('G16',''.($parametros['pag_banco']=="")?'Todos': $parametros['pag_banco_texto']);		
            $objPHPExcel->getActiveSheet()->setCellValue('G17',''.($parametros['pag_estado']=="")?'Todos': $parametros['pag_estado_texto']);	
            $objPHPExcel->getActiveSheet()->setCellValue('H18',''.($parametros['pag_fechaCreacionDesde']=="")?
                                                        'Todos': $parametros['pag_fechaCreacionDesde']);
            $objPHPExcel->getActiveSheet()->setCellValue('H19',''.($parametros['pag_fechaCreacionHasta']=="")?
                                                        'Todos': $parametros['pag_fechaCreacionHasta']);
        }
    
        $i=23;    
        foreach ($datos as $data):
            
            $valorTotal          = ($data['valorTotal'] ? $data['valorTotal'] : 0.00);	
            $valorTotal          = number_format($valorTotal, 2, '.', '');
            $razonSocial         = (isset($data['razonSocial']) ? 
                                   ($data['razonSocial'] ? $data['razonSocial'] : "") : "");
            $nombres             = (isset($data['nombres']) || isset($data['apellidos']) ? 
                                   ($data['nombres'] ? $data['nombres'] . " " . $data['apellidos'] : "") : "");
            $informacion_cliente = ($razonSocial && $razonSocial != "" ? $razonSocial : $nombres);
            $automatica          = isset($data['esAutomatica']) ? ($data['esAutomatica']=="S" ? "Si" : "No") : '';
            $nombreVendedor      = (isset($data["nombreVendedor"]) ? ($data["nombreVendedor"] ? 
                                    ucwords(mb_strtolower($data["nombreVendedor"], 'UTF-8')) : "") : "");
            $referencia1         = ''; 
            $referencia2         = ''; 
            $referencia          = '';
            
            if(isset($data["numeroCuentaBanco"]))
            {
                $referencia1 = $data["numeroCuentaBanco"];
            }
            
            if(isset($data["numeroReferencia"]))
            {
                    $referencia2 = $data["numeroReferencia"];
            }
            
            if($referencia1 != $referencia2)
            {
                $referencia = ($referencia1 ? $referencia1 . " " : "") . ($referencia2 ? $referencia2 . " " : ""); 
            }
            else
            {
                $referencia = ($referencia1 ? $referencia1 . " " : "");
            }
                
          
            $nombreBanco  = "";

            if(isset($data["bancoTipoCuentaId"]))
            {
                $bancoTipoCuentaId     = $data["bancoTipoCuentaId"];
                $entityBancoTipoCuenta = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->findOneById($bancoTipoCuentaId);
                
                if($entityBancoTipoCuenta && count($entityBancoTipoCuenta)>0)
                {
                    $entityBanco = $entityBancoTipoCuenta->getBancoId();
                    $nombreBanco = ($entityBanco ? ($entityBanco->getDescripcionBanco() ? $entityBanco->getDescripcionBanco() : "") : "");
                }
            }
            
            if(isset($data["bancoCtaContableId"]))
            {

                $bancoCtaContableId     = $data["bancoCtaContableId"];
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

            $nombreBancoEmpresa  = "";
            $strDescripcionBanco = "";
            $strCtaContable      = "";

            if(isset($data["cuentaContableId"]))
            {
                $cuentaContableId         = $data["cuentaContableId"];
                $entityAdmiCuentaContable = $emFinanciero->getRepository('schemaBundle:AdmiCuentaContable')->findOneById($cuentaContableId);						
                
                if($entityAdmiCuentaContable && count($entityAdmiCuentaContable)>0)
                {             
                    $nombreBancoEmpresa = $entityAdmiCuentaContable->getDescripcion()." ".$entityAdmiCuentaContable->getNoCta();
                }
            }

            else
            {
                if(trim($parametros['fin_tipoDocumento']) == 'PAG')
                {
                    $objInfoPagoDet= $emFinanciero->getRepository('schemaBundle:InfoPagoDet')->findOneById($data['id_documento_detalle']);
                    
                    if($objInfoPagoDet)
                    {
                        if($objInfoPagoDet->getDepositado()=='S')
                        {
                            $objInfoDeposito= $emFinanciero->getRepository('schemaBundle:InfoDeposito')
                                                           ->findOneById($objInfoPagoDet->getDepositoPagoId()); 
                            if($objInfoDeposito)
                            {
                                if(!is_null($objInfoDeposito->getCuentaContableId()))
                                {
                                    $objAdmiCuentaContable= $emFinanciero->getRepository('schemaBundle:AdmiCuentaContable')
                                                                         ->findOneById($objInfoDeposito->getCuentaContableId()); 
                                    if($objAdmiCuentaContable)
                                    {
                                        if(!is_null($objAdmiCuentaContable->getDescripcion()))
                                        {
                                           $strDescripcionBanco = $objAdmiCuentaContable->getDescripcion(); 
                                        }
                                        if(!is_null($objAdmiCuentaContable->getNoCta()))
                                        {
                                           $strCtaContable = $objAdmiCuentaContable->getNoCta(); 
                                        }                                        
                                        
                                        $nombreBancoEmpresa = $strDescripcionBanco." ".$strCtaContable;
                                    }                                                                       
                                }
                                else if(!is_null($objInfoDeposito->getBancoNafId()))
                                {
                                    $objAdmiBancoCtaContable= $emFinanciero->getRepository('schemaBundle:AdmiBancoCtaContable')
                                                                           ->findOneById($objInfoDeposito->getBancoNafId()); 
                                    if($objAdmiBancoCtaContable)
                                    {
                                        if(!is_null($objAdmiBancoCtaContable->getDescripcion()))
                                        {
                                           $strDescripcionBanco = $objAdmiBancoCtaContable->getDescripcion(); 
                                        }                                        
                                        if(!is_null($objInfoDeposito->getNoCtaBancoNaf()))
                                        {
                                           $strCtaContable = $objInfoDeposito->getNoCtaBancoNaf(); 
                                        }                                          
                                        $nombreBancoEmpresa = $objAdmiBancoCtaContable->getDescripcion()." ".$objAdmiBancoCtaContable->getNoCta();
                                    }                                                                        
                                }
                                
                            }
                        }
                    }
                    
                }
            
            }
            
            $nombreBanco        = ucwords(mb_strtolower(trim($nombreBanco), 'UTF-8')); 
            $nombreBancoEmpresa = ucwords(mb_strtolower(trim($nombreBancoEmpresa), 'UTF-8'));

            $noDocumentoReferencia  = ""; $codigoDocumentoReferencia  = ""; $nombreDocumentoReferencia  = "";
            
            if(isset($data["referenciaId"]))
            {
                if($data["referenciaId"] && $data["referenciaId"]!="")
                {
                    $referenciaId     = $data["referenciaId"];
                    $entityReferencia = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->findOneById($referenciaId);
                    
                    if($entityReferencia && count($entityReferencia)>0)
                    {

                        $noDocumentoReferencia     = ($entityReferencia ? 
                                                     ($entityReferencia->getNumeroFacturaSri() ? 
                                                      $entityReferencia->getNumeroFacturaSri() : "") : "");
                        $tipoDocumentoReferenciaId = ($entityReferencia ? 
                                                     ($entityReferencia->getTipoDocumentoId() ? 
                                                      $entityReferencia->getTipoDocumentoId() : "") : "");
                        $entityTipoDocumento       = $emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                                  ->findOneById($tipoDocumentoReferenciaId);
                        
                        if($entityTipoDocumento && count($entityTipoDocumento)>0)
                        {
                            $codigoDocumentoReferencia = ($entityTipoDocumento ? 
                                                         ($entityTipoDocumento->getCodigoTipoDocumento() ? 
                                                          $entityTipoDocumento->getCodigoTipoDocumento() : "") : "");
                            $nombreDocumentoReferencia = ($entityTipoDocumento ? 
                                                         ($entityTipoDocumento->getNombreTipoDocumento() ? 
                                                          $entityTipoDocumento->getNombreTipoDocumento() : "") : "");
                        }
                    }//fin entityReferencia
                }//fin referenciaId
            }
          
            $nombreCreador = "Migracion";
            $empleado      = $em->getRepository('schemaBundle:InfoPersona')->getPersonaPorLogin($data["usrCreacion"]);
            
            if($empleado && count($empleado)>0)
            {
                $nombreCreador = $empleado->getNombres().' '.$empleado->getApellidos();
            }
            
            $nombreCreador         = ucwords(mb_strtolower(trim($nombreCreador), 'UTF-8'));
            $nombreVendedor        = ucwords(mb_strtolower(trim($nombreVendedor), 'UTF-8'));
            $identificacionCliente = (isset($data['identificacionCliente']) ? trim($data['identificacionCliente']) : "");
            
            if($parametros['fin_tipoDocumento'] == 'ANTS')
            { 
                $nombreClienteObtenido          = "";
                $loginPuntoObtenido             = "";
                $estadoPuntoObtenido            = "";
                $nombrePuntoObtenido            = "";
                $identificacionClienteObtenido  = "";
                
                if(strtoupper($data['estadoDocumentoGlobal'])=='CERRADO' || strtoupper($data['estadoDocumentoGlobal'])=='ASIGNADO')
                {
                    if(strtoupper($data['estadoDocumentoGlobal'])=='CERRADO')
                    {
                        $pagos=$emFinanciero->getRepository('schemaBundle:InfoPagoCab')->findById($data['id_documento']);
                    }
                    elseif(strtoupper($data['estadoDocumentoGlobal'])=='ASIGNADO')
                    {
                        $pagos=$emFinanciero->getRepository('schemaBundle:InfoPagoCab')->findByAnticipoId($data['id_documento']);
                    }
                    
                    foreach($pagos as $objInfoPagoCab)
                    {
                        $objPunto                      = $em->getRepository('schemaBundle:InfoPunto')->find($objInfoPagoCab->getPuntoId());
                        $loginPuntoObtenido            = $objPunto->getLogin();
                        $estadoPuntoObtenido           = $objPunto->getEstado();
                        $nombrePuntoObtenido           = $objPunto->getNombrePunto();
                        $identificacionClienteObtenido = $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getIdentificacionCliente();
                        $razonSocialTxt                = $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial();
                        $nombresTxt                    = $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getNombres();
                        $apellidosTxt                  = $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getApellidos();
                        $razonSocial                   = (isset($razonSocialTxt) ? ($razonSocialTxt ? $razonSocialTxt : "") : "");
                        $nombres                       = (isset($nombresTxt) || isset($apellidosTxt) ? 
                                                         ($nombresTxt ? $nombresTxt . " " . $apellidosTxt : "") : "");
                        $nombreClienteObtenido         = ($razonSocial && $razonSocial != "" ? $razonSocial : $nombres);                        
                    }
                }
                
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, (isset($loginPuntoObtenido) ? $loginPuntoObtenido : ""));
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, (isset($estadoPuntoObtenido) ? $estadoPuntoObtenido : ""));
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, (isset($nombrePuntoObtenido) ? $nombrePuntoObtenido : ""));
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, (isset($identificacionClienteObtenido) ? $identificacionClienteObtenido : ""));
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, (isset($nombreClienteObtenido) ? $nombreClienteObtenido : ""));                                
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
                $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, trim($nombreBancoEmpresa));
                $objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, trim($nombreDocumentoReferencia));
                $objPHPExcel->getActiveSheet()->setCellValue('R'.$i, "$noDocumentoReferencia ");        
                $objPHPExcel->getActiveSheet()->setCellValue('S'.$i, trim($nombreCreador));
                $objPHPExcel->getActiveSheet()->setCellValue('T'.$i, trim($data['estadoDocumentoGlobal']?$data['estadoDocumentoGlobal']:''));
                $objPHPExcel->getActiveSheet()->setCellValue('U'.$i, strval(date_format($data['feCreacion'],"d/m/Y G:i")));

                if(isset($data['feDeposito']))
                {
                    if($data['feDeposito']!="")
                    {
                        $fecha_deposito = strval(date_format($data['feDeposito'],"d/m/Y G:i"));
                    }
                    else
                    {
                        $fecha_deposito = "";
                    }
                }
                else
                {
                    $fecha_deposito = "";
                }

                $objPHPExcel->getActiveSheet()->setCellValue('V'.$i, $fecha_deposito);
                //fecha cruce

                if(isset($data['fechaCruce']))
                {
                    if($data['fechaCruce'])
                    {
                        $fecha_cruce = strval(date_format($data['fechaCruce'],"d/m/Y G:i"));
                    }
                    else
                    {
                        $fecha_cruce = ""; 
                    }
                }
                else
                
                $objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $fecha_cruce);

            }
            else if($parametros['fin_tipoDocumento'] == 'FAC'  || 
                    $parametros['fin_tipoDocumento'] == 'FACP' || 
                    $parametros['fin_tipoDocumento'] == 'ND'   ||
                    $parametros['fin_tipoDocumento'] == 'NDI'  ||
                    $parametros['fin_tipoDocumento'] == 'DEV'  ||
                    $parametros['fin_tipoDocumento'] == 'NC')
            {
                $floatSumaPagosPorRetencion = 0;
                $arrayPagosFpRetencion      = $emFinanciero->getRepository('schemaBundle:InfoPagoDet')
                                                           ->findPagoDetRetencionPorPago($data['id_documento']);
                
                foreach($arrayPagosFpRetencion as $objInfoPagoDet)
                {
                    $floatSumaPagosPorRetencion+=$objInfoPagoDet->getValorPago();
                }
                
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, (isset($data['login']) ? trim($data['login']) : ""));
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, (isset($data['estado']) ? trim($data['estado']) : ""));
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, (isset($data['nombrePunto']) ? trim($data['nombrePunto']) : ""));
                
                //descripcionFactura
                if($parametros['fin_tipoDocumento'] == 'FAC' || $parametros['fin_tipoDocumento'] == 'FACP')
                {
                    //Observacion guardada en el historial
                    $strObservacion = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                   ->getHistorialDocumento($data['id_documento']);
                }
                elseif($parametros['fin_tipoDocumento'] == 'NC')
                {
                    //Observacion guardada como caracteristica
                    $strObservacion = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                   ->getInformacionCaracteristica($data['id_documento'],"DESCRIPCION_INTERNA_NC");   
                }
                
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, (isset($strObservacion[0]['informacion']) ? 
                                                            trim($strObservacion[0]['informacion']): ""));
                
                //descripcionFactura->Observacion guardada en el historial
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, (isset($identificacionCliente) ? trim($identificacionCliente) : ""));
                $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, (isset($informacion_cliente) ? trim($informacion_cliente) : ""));
                $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, (isset($data['direccion_grl']) ? $data['direccion_grl'] : ""));
                $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, (isset($data['id_documento']) ? $data['id_documento'] : ""));
                $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, trim($data['nombreTipoDocumento']));
                $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $data['numeroDocumento']);
                $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $data['subtotal']);
                $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $data['subtotalDescuento']);
                
                //valorReal:subtotal-descuento
                $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, (isset($data['valorReal']) ? $data['valorReal'] : 0));
                //valorReal:subtotal-descuento
                
                $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $data['subtotalConImpuesto']);
                
                //iva | ice
                $floatImpuestoIva = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                 ->getValorImpuesto($data['id_documento'],"IVA");
                
                $floatImpuestoIce = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                 ->getValorImpuesto($data['id_documento'],"ICE");
                
                $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, (isset($floatImpuestoIva[0]['totalImpuesto']) 
                                                                        ? $floatImpuestoIva[0]['totalImpuesto'] : "0"));
                $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, (isset($floatImpuestoIce[0]['totalImpuesto']) 
                                                                        ? $floatImpuestoIce[0]['totalImpuesto'] : "0"));
                //iva | ice
                
                $objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $valorTotal);
                $objPHPExcel->getActiveSheet()->setCellValue('R'.$i, trim($automatica));
                $objPHPExcel->getActiveSheet()->setCellValue('S'.$i, trim($nombreCreador));
                $objPHPExcel->getActiveSheet()->setCellValue('T'.$i, trim($data['estadoDocumentoGlobal']?$data['estadoDocumentoGlobal']:''));
                $objPHPExcel->getActiveSheet()->setCellValue('U'.$i, strval(date_format($data['feCreacion'],"d/m/Y G:i")));   

                if(isset($data['feEmision']))
                {
                    if($data['feEmision']!="")
                    {
                        $fecha_emision = strval(date_format($data['feEmision'],"d/m/Y G:i"));
                    }
                    else
                    {
                        $fecha_emision = "";
                    }
                }
                else
                {
                    $fecha_emision = "";
                }

                $objPHPExcel->getActiveSheet()->setCellValue('V'.$i, $fecha_emision); 
                
                //fe_autorizacion
                if(isset($data['feAutorizacion']))
                {
                    if($data['feAutorizacion']!="")
                    {
                        $strFechaAutorizacion=strval(date_format($data['feAutorizacion'],"d/m/Y G:i"));
                    }
                    else
                    {
                        $strFechaAutorizacion="";
                    }
                }
                else
                {
                    $strFechaAutorizacion="";
                }
                
                $objPHPExcel->getActiveSheet()->setCellValue('W'.$i, $strFechaAutorizacion);
                //fe_autorizacion
                
                //forma_pago
                $objContrato  = $em->getRepository('schemaBundle:InfoContrato')->findContratoActivoPorPersonaEmpresaRol($data['idPersonaRol']);
                if(isset($objContrato))
                {
                    $strFormaPago = $objContrato->getFormaPagoId()->getDescripcionFormaPago();
                }
                else
                {
                    $strFormaPago = "";
                }
                
                $objPHPExcel->getActiveSheet()->setCellValue('X'.$i, (isset($strFormaPago) ? trim($strFormaPago) : ""));
                
                //vendedor
                $objPHPExcel->getActiveSheet()->setCellValue('Y'.$i, (isset($data['nombreVendedor']) ? trim($data['nombreVendedor']) : ""));
                $objPHPExcel->getActiveSheet()->setCellValue('Z'.$i, $data['codigoTipoNegocio']);   
                $objPHPExcel->getActiveSheet()->setCellValue('AA'.$i, $data['nombreOficina']);

                $referencia_nd = "";
                $comentario_nd = "";

                if($parametros['fin_tipoDocumento']=='ND'  || 
                   $parametros['fin_tipoDocumento']=='DEV' || 
                   $parametros['fin_tipoDocumento']=='NDI')
                {
                    //saco con el id_documento el det y el pago_det_id
                    //obtengo el numero_pago y lo pongo en la referencia
                    $referencia_nd = "";
                    $comentario_nd = "";
                    if(isset($data['id_documento']))
                    {
                        $nd_det = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                               ->findByDocumentoId($data['id_documento']);
                        foreach ($nd_det as $nd):
                            $pago_det_id = $nd->getPagoDetId();
                            if($pago_det_id)
                            {
                                    $pago_det       = $emFinanciero->getRepository('schemaBundle:InfoPagoDet')->find($nd->getPagoDetId());
                                    $referencia_nd .= "|".$pago_det->getPagoId()->getNumeroPago();
                                    $comentario_nd .= "|".$nd->getObservacionesFacturaDetalle();
                            }
                        endforeach;
                    }
                    
                    //multaChequeProtestado
                    $floatMultaChequeProtestado = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                               ->getInformacionCaracteristica($data['id_documento'],"CHEQUE_PROTESTADO");
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('AH'.$i, (isset($floatMultaChequeProtestado[0]['informacion']) ? 
                                                                            trim($floatMultaChequeProtestado[0]['informacion']) : ""));
                    
                }

                if($parametros['fin_tipoDocumento'] == 'NC')
                {
                    //tipoResponsable
                    $strTipoResponsable = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                       ->getInformacionCaracteristica($data['id_documento'],"TIPO_RESPONSABLE_NC"); 
                    $objPHPExcel->getActiveSheet()->setCellValue('AB'.$i, (isset($strTipoResponsable[0]['informacion']) ? 
                                                                            trim($strTipoResponsable[0]['informacion']) : ""));   
                    
                    //areaResponsable
                    $strResponsable = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                   ->getInformacionCaracteristica($data['id_documento'],"RESPONSABLE_NC");
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('AC'.$i, (isset($strResponsable[0]['informacion']) ? 
                                                                            trim($strResponsable[0]['informacion']) : ""));
                    //#factura que aplica
                    $entityInfoDocFinancieroCabFAC  = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                         ->find($data['referenciaDocumentoId']);
                    $objPHPExcel->getActiveSheet()->setCellValue('AD'.$i, $entityInfoDocFinancieroCabFAC->getNumeroFacturaSri());
                    
                }
                
                //motivoDeDocumento
                $strMotivo = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                          ->getMotivoDocumento($data['id_documento']);
                if($strMotivo)
                {
                    $objPHPExcel->getActiveSheet()->setCellValue('AE'.$i, (isset($strMotivo[0]['nombreMotivo']) ? 
                                                                           trim($strMotivo[0]['nombreMotivo']) : ""));
                }
                else
                {
                    $objPHPExcel->getActiveSheet()->setCellValue('AE'.$i, "");		
                }
                
                $objPHPExcel->getActiveSheet()->setCellValue('AF'.$i, $referencia_nd);    
                $objPHPExcel->getActiveSheet()->setCellValue('AG'.$i, $comentario_nd);
                $objPHPExcel->getActiveSheet()->setCellValue('AI'.$i, $floatSumaPagosPorRetencion);
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
                $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, trim($nombreBancoEmpresa)); 
                $objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, trim($nombreDocumentoReferencia));
                $objPHPExcel->getActiveSheet()->setCellValue('R'.$i, "$noDocumentoReferencia ");
                $objPHPExcel->getActiveSheet()->setCellValue('S'.$i, trim($nombreCreador));
                $objPHPExcel->getActiveSheet()->setCellValue('T'.$i, trim($data['estadoDocumentoGlobal']?$data['estadoDocumentoGlobal']:''));
                $objPHPExcel->getActiveSheet()->setCellValue('U'.$i, strval(date_format($data['feCreacion'],"d/m/Y G:i"))); 

                if(isset($data['feDeposito']))
                {
                    if($data['feDeposito']!="")
                    {
                        $fecha_deposito=strval(date_format($data['feDeposito'],"d/m/Y G:i"));
                    }
                    else
                    {
                        $fecha_deposito="";
                    }
                }
                else
                {
                    $fecha_deposito="";
                }
                
          
                if(trim($parametros['fin_tipoDocumento']) == 'PAG')
                {
                    $objInfoPagoDet= $emFinanciero->getRepository('schemaBundle:InfoPagoDet')->findOneById($data['id_documento_detalle']);

                    if($objInfoPagoDet)
                    {
                        if($objInfoPagoDet->getDepositado()=='S')
                        {
                            $objInfoDeposito = $emFinanciero->getRepository('schemaBundle:InfoDeposito')
                                                           ->findOneById($objInfoPagoDet->getDepositoPagoId()); 
                            if($objInfoDeposito)
                            {
                                $fecha_deposito=strval(date_format($objInfoDeposito->getFeProcesado(),"d/m/Y G:i"));
                            }
                        }
                        else
                        {
                            if( $objInfoPagoDet->getFeDeposito() )
                            {
                                $fecha_deposito = strval(date_format($objInfoPagoDet->getFeDeposito(),"d/m/Y G:i"));
                            }
                            else
                            {
                                $fecha_deposito = "";
                            }
                        }
                    }
                }
                  

                $objPHPExcel->getActiveSheet()->setCellValue('V'.$i, $fecha_deposito);

                if(isset($data['feProcesado']))
                {
                    if($data['feProcesado']!="")
                    {
                        $fecha_procesado=strval(date_format($data['feProcesado'],"d/m/Y G:i"));
                    }
                    else
                    {
                        $fecha_procesado = "";
                    }
                }
                else
                {
                    $fecha_procesado = "";
                }

                $objPHPExcel->getActiveSheet()->setCellValue('W'.$i, $fecha_procesado);

                //Fecha de cruce
                if(isset($data['fechaCruce']))
                {
                    if($data['fechaCruce'])
                    {
                        $fecha_cruce = strval(date_format($data['fechaCruce'],"d/m/Y G:i"));
                    }
                    else
                    {
                        $fecha_cruce = ""; 
                    }
                }else
                    $fecha_cruce="";

                $objPHPExcel->getActiveSheet()->setCellValue('W'.$i, $fecha_cruce);


                if(isset($data['noComprobanteDeposito']))
                {
                    if($data['noComprobanteDeposito']!="")
                    {
                        $no_comprobante_deposito=$data['noComprobanteDeposito'];
                    }
                    else
                    {
                        $no_comprobante_deposito="";
                    }
                }
                else
                {
                    $no_comprobante_deposito="";
                }

                $objPHPExcel->getActiveSheet()->setCellValue('Y'.$i, $no_comprobante_deposito);

                if(isset($data['oficinaId']))
                {
                    //Entidad oficina - para la presentacion en el pago
                    $oficinaId=$em->getRepository('schemaBundle:InfoOficinaGrupo')->find($data['oficinaId']);

                    if($data['oficinaId']!="")
                    {
                        $oficina=$oficinaId->getNombreOficina();
                    }
                    else
                    {
                        $oficina="";
                    }
                }
                else
                {
                    $oficina="";
                }

                $objPHPExcel->getActiveSheet()->setCellValue('Z'.$i, $oficina);
            }
          
            $i=$i+1;
        endforeach;

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');
        $objPHPExcel->setActiveSheetIndex(0);
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_Busqueda_Avanzada_Financiera_'.date('d_M_Y').'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    
    /**
     * getFormasPagoMultiAction
     * Funcion que permite obtener de la base de datos las diferentes forma de pago parametrizados
     * para la multiseleccion
     *
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.0 20-07-2017
     *
     * @return Response
     */
    public function getFormasPagoMultiAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $request = $this->get('request');

        $start = $request->query->get('start');
        $limit = $request->query->get('limit');

        $objFormaPago = $this->getDoctrine()
                             ->getManager("telconet_general")
                             ->getRepository('schemaBundle:AdmiFormaPago')
                             ->getRegistros("","Activo",$start,$limit);

        $arrayFormaPago[]=array('intValue'                => 'ALL',
                                'strDescripcionFormaPago' => 'Todos');

        foreach ($objFormaPago as $objForma)
        {
            $arrayFormaPago[]=array('intValue'                => $objForma->getId(),
                                    'strDescripcionFormaPago' => $objForma->getDescripcionFormaPago());
        }

        $intNum = count($arrayFormaPago);
        $objDataF =json_encode($arrayFormaPago);
        $objJson= '{"intTotal":"'.$intNum.'","objFormaPago":'.$objDataF.'}';
        $objRespuesta->setContent($objJson);

        return $objRespuesta;
    }

    /**
     * getEstadoPuntoAction
     * Funcion que permite obtener de la base de datos los diferentes estados del punto parametrizados
     *
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.0 21-07-2017
     *
     * @return Response
     */
    public function getEstadoPuntoAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $arrayEstPunto=array('strParametro' => 'CONF_ESTADO_PUNTO');
        $strEstadoPunto = $this->getDoctrine()
                          ->getManager("telconet_financiero")
                          ->getRepository('schemaBundle:InfoPagoCab')
                          ->obtenerParametroConfig($arrayEstPunto);

        $arrayPuntos       =explode(",", $strEstadoPunto);
        $arrayEstadoPunto[]=array('intValue'                  => 'ALL',
                                  'strDescripcionEstadoPunto' => 'Todos');

        foreach ($arrayPuntos as $strEstado)
        {
            $arrayEstadoPunto[]=array('intValue'                  => $strEstado,
                                      'strDescripcionEstadoPunto' => $strEstado);
        }

        $intNum = count($arrayEstadoPunto);
        $objDataF =json_encode($arrayEstadoPunto);
        $objJson= '{"intTotal":"'.$intNum.'","objEstadoPunto":'.$objDataF.'}';
        $objRespuesta->setContent($objJson);

        return $objRespuesta;
    }

    /**
     * getEstadoPagoAction
     * Funcion que permite obtener de la base de datos los diferentes estados del pago parametrizados
     *
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.0 21-07-2017
     *
     * @return Response
     */
    public function getEstadoPagoAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $arrayEstPago  =array('strParametro' => 'CONF_ESTADO_PAGO');
        $strEstadoPago = $this->getDoctrine()
                          ->getManager("telconet_financiero")
                          ->getRepository('schemaBundle:InfoPagoCab')
                          ->obtenerParametroConfig($arrayEstPago);

        $arrayPagos       =explode(",",$strEstadoPago);
        $arrayEstadoPago[]=array('intValue'                 =>'ALL',
                                  'strDescripcionEstadoPago'=>'Todos');

        foreach ($arrayPagos as $strPago)
        {
            $arrayEstadoPago[]=array( 'intValue'                 =>$strPago,
                                      'strDescripcionEstadoPago' =>$strPago);
        }

        $intNum = count($arrayEstadoPago);
        $objDataF =json_encode($arrayEstadoPago);
        $objJson= '{"intTotal":"'.$intNum.'","objEstadoPago":'.$objDataF.'}';
        $objRespuesta->setContent($objJson);

        return $objRespuesta;
    }

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
			            ->getRegistros("","Activo","","");
						
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
     * @author German Valenzuela Franco <gvalenzuela@telconet.ec>
     * @version 1.3 12-07-2017
     * Se actualiza el metodo para verificar que sea cliente VIP de la empresa panama - TNP
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.4 16-01-2020 - Se inicializa seteo de variables de sesión 'contactosCliente', 'contactosPunto'.
     *
     */
    public function cargaSessionAjaxAction()
    {
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
            
        $session->set('ptoCliente',  '');
        $session->set('cliente',  '');
        $session->set('clienteContactos', '');
        $session->set('contactosCliente', '');
        $session->set('contactosPunto', '');
        $session->set('esVip', '');

        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $puntoId  = $peticion->get('puntoId');
        
        $codEmpresa        = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $strPrefijoEmpresa = $peticion->getSession()->get('prefijoEmpresa');

        $em = $this->getDoctrine()->getManager("telconet");

        if($puntoId == 0)
        {
            $respuesta->setContent("No existe la entidad");
            return $respuesta;
        }

        if (null == $puntoCliente = $em->getRepository('schemaBundle:InfoPunto')->getPuntoParaSession($puntoId))
        {
            $respuesta->setContent("No existe la entidad");
        }
        else
        {
            $cliente = $em->getRepository('schemaBundle:InfoPersona')->getPersonaParaSession($codEmpresa, $puntoCliente["id_persona"]);
            
            $clienteContactos = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                   ->getFormasContactoParaSession($puntoCliente["id_persona"], "Telefono");
            
            //guardo en session el ptoCliente
            $session->set('ptoCliente', $puntoCliente);
            $session->set('cliente', $cliente);
            $session->set('clienteContactos', $clienteContactos);
                        
            $strEsVip = '';
            
            if($strPrefijoEmpresa == 'TN' || $strPrefijoEmpresa == 'TNP')
            {
                // Buscamos en InfoContratoDatoAdicional para verificar que sea cliente VIP
                $arrayParams        = array('ID_PER'  => $puntoCliente["id_persona_empresa_rol"], 
                                            'EMPRESA' => $codEmpresa, 
                                            'ESTADO'  => 'Activo');
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
     */
    public function destruirSesionAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
        
        //guardo en session el ptoCliente
        $session  = $peticion->getSession();
        $session->set('ptoCliente', "");
        $session->set('cliente', "");
        $session->set('clienteContactos', "");
        $session->set('puntoContactos', "");
        $session->set('esVIP', "");
        
        $respuesta->setContent("Se destruyo la sesion del punto escogido");                      
        return $respuesta;
    } 



    public function guardarSesionEmpresaAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->getRequest();
        
        $IdPersonaEmpresaRol = $peticion->get('IdPersonaEmpresaRol');
        $CodEmpresa = $peticion->get('IdEmpresa');
        $nombreEmpresa = $peticion->get('nombreEmpresa');
        $IdOficina = $peticion->get('IdOficina');
        $nombreOficina = $peticion->get('nombreOficina');
        $IdDepartamento = $peticion->get('IdDepartamento');
        $nombreDepartamento = $peticion->get('nombreDepartamento');
        
        //guardo en session el ptoCliente
        $session  = $this->get( 'session' );
        $session->set('idPersonaEmpresaRol', $IdPersonaEmpresaRol);
        $session->set('idEmpresa', $CodEmpresa);
        $session->set('idOficina', $IdOficina);
        $session->set('idDepartamento', $IdDepartamento);
        $session->set('empresa', $nombreEmpresa);
        $session->set('oficina', $nombreOficina);
        $session->set('departamento', $nombreDepartamento);
        
        $respuesta->setContent("Se seteo los valores de la empresa");                      
        return $respuesta;
    }
	
	/*Archivo del Courier*/
    
    public function listarArchivosCourierAction()
    {
        $request = $this->getRequest();
        $session=$request->getSession();
        $cliente=$session->get('cliente');
        $ptocliente=$session->get('ptoCliente');
        return $this->render('financieroBundle:procesosautomaticos:listadoArchivosCourier.html.twig', array());        
    }
    
     /**
     * gridReportesCarteraAction
     * Metodo que obtiene los archivos de courier segun la empresa
     * @version 2.0 14-08-2022
     * @author Gustavo Narea <gnarea@telconet.ec> Se modifica el metodo para obtener los archivos del servidor nfs
     * */
    public function gridArchivoCourierAction() 
    {
        $objRequest = $this->getRequest();
        $objSession  = $objRequest->getSession();
        $emGeneral = $this->get('doctrine')->getManager('telconet_general');
        $em = $this->get('doctrine')->getManager('telconet');
        $serviceUtil        = $this->get('schema.Util');
        
        $strUsuario  = $objSession->get('user');
        $objFechaDesde = explode('T', $objRequest->get("fechaDesde"));
        $objFechaHasta = explode('T', $objRequest->get("fechaHasta"));		
        $intLimit = $objRequest->get("limit");
        $intPage = $objRequest->get("page");
        $intStart = $objRequest->get("start");

        $strPathAdicional = "Courier";
        
        $intMes=$objRequest->get('mes');
		$intAnio=$objRequest->get('anio');
        
        //Busqueda archivo
        $strPathTelcos        = $this->container->getParameter('path_telcos');
        $strFindPath         = $path_telcos.'telcos/web/public/uploads/archivosCourier/';
        
        if($intMes && $intAnio)
        {
            if (strlen($intMes)==1)
            {
                $intMes="0".$intMes;
            }
            
            $intCriterioFechaInicio = $intAnio."-".$intMes."-01";
            $intCriterioFechaFin = date("Y-m-t", strtotime($intCriterioFechaInicio));
        }        
        else
        {
            $intCriterioFechaInicio=date('Y-m-01');
            $intCriterioFechaFin = date("Y-m-t", strtotime($intCriterioFechaInicio));
        }
        
        $intIdEmpresa = $objRequest->getSession()->get('idEmpresa');        
        
		$entityEmpresa=$em->getRepository('schemaBundle:InfoEmpresaGrupo')->find($intIdEmpresa);
        $strPrefijoEmpresa = strtoupper($entityEmpresa->getPrefijo());
        $objFechaInicio=date_create($intCriterioFechaInicio);
        $objFechaFin=date_create($intCriterioFechaFin);

		$arrayFecha=array('inicio'=> date_format($objFechaInicio,"d/m/Y") ,
                             'fin'=> date_format($objFechaFin,"d/m/Y") );
        
        $objGestionDir    = $emGeneral->getRepository('schemaBundle:AdmiGestionDirectorios')
                                      ->findOneBy(array('aplicacion'  => "TelcosWeb",
                                                         'subModulo'   => "ReporteCourier",
                                                         'empresa'     => $strPrefijoEmpresa));
        if(!is_object($objGestionDir))
        {
            throw new \Exception('Error, no existe la configuración requerida para consultar archivos de la aplicación'.$strNombreApp);
        }

        $arrayData = array('codigoApp'      => $objGestionDir->getCodigoApp(),
                            'codigoPath'    => $objGestionDir->getCodigoPath(),
                            'fecha'         => $arrayFecha,
                            'pathAdicional' => array(array("key" => $strPathAdicional )) );

        $arrayDataBusqueda = array();
        $arrayDataBusqueda['data'] = array($arrayData);
        $arrayDataBusqueda["op"] = "buscarArchivo";
        $arrayDataBusqueda["user"] = $strUsuario;
        
        $arrayResponseWs= $serviceUtil->buscarArchivosNfs($arrayDataBusqueda);

        if ($arrayResponseWs && $arrayResponseWs['intStatus'] == "200")
        {
            foreach($arrayResponseWs['arrayDatosArchivos'] as $arrayArchivo)
            {
                if( $arrayArchivo["nombreFile"] != "" && $arrayArchivo["pathFile"] != "" )
                {
                    $strUrl = $arrayArchivo["pathFile"]; 
                    break;   
                }
            }
        }
	
		if ($arrayResponseWs && $arrayResponseWs['intStatus'] == "200")
		{
			foreach ($arrayResponseWs['arrayDatosArchivos'] as $arrayFilesWs) 
            {    
                $strNombreArchivo = $arrayFilesWs["nombreFile"];
                $strUrlArchivo = $arrayFilesWs["pathFile"];
                $strSizeArchivo = $arrayFilesWs["pesoFile"];
				$arrayArreglo[] = array(
                                    'linkVer' => $strNombreArchivo,
                                    'linkFile' => $strUrlArchivo,
                                    'size' => $strSizeArchivo    
				);                
			}
		}
		
		$objResponse = new Response(json_encode(array('total' => count($arrayArreglo), 'clientes' => $arrayArreglo)));

        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }   	   
    
///////////////////////////////////////////taty: Inicio Reporte CierreCaja/////////////////////////
    
     /**
     * Documentacion para funcion formaPagoDepositableAction
     * Obtiene las formas de pago depositables
     * 
     * Actualizacion: Si el usuario que consulta es de empresa Telconet 
     * adicional se agrega forma de pago "Tarjeta de credito".
     * Tambien Se creo services obtenerFormasPagoParaReporteCierreCaja para obtener 
     * la consulta de las formas de pago para usarla en otras funciones 
     * @author Andrés Montero Holguin
     * @version 1.1 14/07/2016
     * 
     * @author Telcos
     * @version 1.0 
     * 
     * @return $response
     */    
    public function formaPagoDepositableAction()
    {
        $request                = $this->getRequest();
        $strPrefijoEmpresa      = $request->getSession()->get('prefijoEmpresa');
        $serviceReportes        = $this->get('financiero.Reportes');
        $arrRespuestaFormasPago = $serviceReportes->obtenerFormasPagoParaReporteCierreCaja($strPrefijoEmpresa);
        $arrFormasPago          = $arrRespuestaFormasPago['arrFormasPago'];
        $total                  = count($arrFormasPago);
        if (!empty($arrFormasPago))
        {    
            $response = new Response(json_encode(array('total' => $total, 'registros' => $arrFormasPago)));
        } 
        else 
        {
            $arrFormasPago[] = array();
            $response  = new Response(json_encode(array('total' => $total, 'registros' => $arrFormasPago)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    
    ////traer las oficinas de la empresa
    
     public function getOficinasAction(){
         $request = $this->getRequest();
         $session  = $request->getSession();
         
          $em = $this->get('doctrine')->getManager('telconet_financiero');
          $empresaId =$session->get('idEmpresa'); 
          
          $InfoEmpresaGrupo=$em->getRepository('schemaBundle:InfoEmpresaGrupo')->find($empresaId);
          $InfoOficinaGrupo=$em->getRepository('schemaBundle:InfoOficinaGrupo')->findBy(array('empresaId'=>$InfoEmpresaGrupo, 'estado'=>'Activo'));
          
          $total=count($InfoOficinaGrupo);
          
        foreach ($InfoOficinaGrupo as $oficina){  
            $nombreOficina=str_replace(' ', '',$oficina->getNombreOficina());
            $arreglo[] = array(
                'id' => $oficina->getId(),
                'nombre' =>str_replace('TRANSTELCO', 'TRANSTELCO ',$nombreOficina)
                
            );
        }
          
          
           if (!empty($arreglo))
            $response = new Response(json_encode(array('total' => $total, 'registros' => $arreglo)));
           else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('total' => $total, 'registros' => $arreglo)));
          }
        
           $response->headers->set('Content-type', 'text/json');
           return $response;
    }

     /**
     * Muestra pantalla de Reporte de Cierre Fiscal para la empresa Telconet Panamá
     *      
     * @author apenaherrera@telconet.ec
     * @version 1.0 28/01/2019
     */    
	public function mostrarCierreFiscalAction()
    {
	
		return $this->render('financieroBundle:reportes:cierreFiscal.html.twig',array());
	
	}
    
    /**
     * Genera Reporte de Cierre Fiscal X o Cierre Fiscal Z para la empresa Telconet Panamá
     *      
     * @author apenaherrera@telconet.ec
     * @version 1.0 28/01/2019
     */    
	public function generaCierreFiscalAction()
    {
	
		$arrayParametros     = array();
        $objRequest          = $this->getRequest();
        $strTipoCierre       = $objRequest->get('strTipoCierre');
        $objSession          = $objRequest->getSession();
        $strCodEmpresa       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa   = $objSession->get('prefijoEmpresa');
        $strUserSession      = $objSession->get('user');
        $intIdPersRolSession = $objSession->get('idPersonaEmpresaRol');
        $strEmailUsrSesion   = '';
        $objEmComercial      = $this->get('doctrine')->getManager('telconet');
        $objEmFinanciero     = $this->get('doctrine')->getManager('telconet_financiero');
                
        $objInfoPersonaEmpresaRol = $objEmComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                   ->find($intIdPersRolSession);

        if(is_object($objInfoPersonaEmpresaRol))
        {
            $strValorFormaContacto = $objEmComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                    ->getValorFormaContactoPorCodigo($objInfoPersonaEmpresaRol->getPersonaId(),'MAIL');

            if(!is_null($strValorFormaContacto))
            {
                $strEmailUsrSesion = strtolower($strValorFormaContacto);
            }                
        }
        $objResponse     = new Response();
        $arrayParametros['strTipoCierre']     = $strTipoCierre;
        $arrayParametros['strCodEmpresa']     = $strCodEmpresa;
        $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;
        $arrayParametros['strUserSession']    = $strUserSession;
        $arrayParametros['strEmailUsrSesion'] = $strEmailUsrSesion;                        
        
        if( isset($arrayParametros) && !empty($arrayParametros))
        {
            $arrayDatos= $objEmFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                         ->consumeApiInterfazPanamaCierreFiscal($arrayParametros);
            $objResponse->setContent(json_encode(array('strCodError'  => $arrayDatos['strCodError'],
                                                       'strMensaje'   => $arrayDatos['strMensaje'])));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;            
        }
	
	}
     /**
     * Documentacion para funcion mostrarCierreCajaAction
     * Funcion que permite mostrar la opcion de cierre de caja
     * 
     * @author Andrés Montero Holguin <amontero@telconet.ec>
     * @version 1.1 14/07/2016
     * 
     * @author Telcos
     * @version 1.0 
     * 
     * @return $response
     */  
    public function mostrarCierreCajaAction()
    {
        $request           = $this->getRequest();
        $strPrefijoEmpresa = $request->getSession()->get('prefijoEmpresa');        
        
        return $this->render('financieroBundle:reportes:cierreCaja.html.twig', array('strPrefijoEmpresa'=>$strPrefijoEmpresa));        
    }
    
     /**
     * Documentacion para funcion cierreCajaAjaxAction
     * Obtiene los pagos del dia para obtener la consulta de cierre de caja
     * 
     * Actualizacion: Se indenta, se obtiene formas de pago 
     * por defecto si es que la variable $formaspago no tiene datos
     * @author Andrés Montero Holguin <amontero@telconet.ec>
     * @version 1.1 14/07/2016
     * 
     * @author Telcos
     * @version 1.0 
     * 
     * @return $response
     */
    public function cierreCajaAjaxAction()
    {
        ini_set('max_execution_time', 7000000);
        $request           = $this->getRequest();
        $session           = $request->getSession();
        $strPrefijoEmpresa = $session->get('prefijoEmpresa');        
        $empresaId         = $session->get('idEmpresa');         
        $feDesde           = $request->get("fechaDesde");
        $feHasta           = $request->get("fechaHasta");
        $fechaDesde        = date("Y/m/d", strtotime($feDesde));
        $fechaHasta        = date("Y/m/d", strtotime($feHasta));
            
        if($fechaDesde=="" || $fechaHasta=="" )
        {
            $fechaDesde = "0000/00/00";
            $fechaHasta = "0000/00/00";
        }

        $oficina   = $request->get("oficina"); 
        $em        = $this->get('doctrine')->getManager('telconet_financiero');   
        $formapago = $request->get("formapago");

        if (!$formapago)
        {
            $serviceReportes        = $this->get('financiero.Reportes');
            $arrRespuestaFormasPago = $serviceReportes->obtenerFormasPagoParaReporteCierreCaja($strPrefijoEmpresa);
            $formapago              = $arrRespuestaFormasPago['strFormasPago'];
        }   
        
     
        $resultado = $em->getRepository('schemaBundle:InfoPagoCab')
                        ->listarCierreCajaXFormaPago($empresaId,$fechaDesde, $fechaHasta,$formapago,$oficina);     
        ///estos datos son usados para llenar mi array con los parameros necesarios
        $datos     = $resultado['registros'];
        $total     = $resultado['total'];
      
         //////Llenando los parametros////
        foreach ($datos as $dato)
        {
            $id          = $dato['id'];
            $numeroPago  = $dato['numeroPago'];
            $usrCreacion = $dato['usrCreacion'];
            $infoPersona = $em->getRepository('schemaBundle:InfoPersona')->findOneBy(array('login'=> $usrCreacion));
            $empl        = ""   ;
            if($usrCreacion!='Admin Account')  
            {
                if($infoPersona)
                {
                    $empl=$infoPersona->getNombres(). " ". $infoPersona->getApellidos();
                }
            }
            else
            {       
                $empl="migracion"   ;
            }
            $fechaCreacion = $dato['feCreacion'];
            $ptoCiente     = $dato['puntoId']; 
          
            if($ptoCiente!='')
            {
                $infoPunto             = $em->getRepository('schemaBundle:InfoPunto')->find($ptoCiente);
                $infoPersonaEmpresaRol = $infoPunto->getPersonaEmpresaRolId();
                $infoPersona_cliente   = $em->getRepository('schemaBundle:InfoPersona')->find( $infoPersonaEmpresaRol->getPersonaId());
                $razonSocial           = $infoPersona_cliente->getRazonSocial();
                $login                 = $infoPunto->getLogin();
                $cliente               = "";
                 if($razonSocial=="")
                 {
                     $cliente= $infoPersona_cliente->getNombres(). " ". $infoPersona_cliente->getApellidos();
                 }
                 else
                 {
                     $cliente=$razonSocial;
                 }
            }
            else
            {
               $cliente="";
            }      
            $numReferencia=$dato['numeroReferencia'];
         
            if($numReferencia =='')
            { // si el numero de referencia esta vacio
                $numReferencia=$dato['bancoCtaContableId'];
            }
         
            $AdmiFormaPago = $em->getRepository('schemaBundle:AdmiFormaPago')->find($numDoc=$dato['formaPagoId']);
            $formaPago     = $AdmiFormaPago->getDescripcionFormaPago();
            $valor         = $dato['valorPago'];
            $oficinaId     = $dato['oficinaId'];
            $nombreOficina = "";
            if($oficinaId!='')
            {
                $InfoOficinaGrupo = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($oficinaId );
                $nombreOficina    = $InfoOficinaGrupo->getNombreOficina();
            }
            $arreglo[] = 
                array(
                    'id'            => $id,
                    'numeroPago'    => $numeroPago,
                    'empl'          => $empl,
                    'fechaCreacion' => strval(date_format($fechaCreacion, "d/m/Y G:i")),
                    'ptoCiente'     => $ptoCiente,
                    'cliente'       => $cliente,
                    'login'         => $login,
                    'numReferencia' => $numReferencia,
                    'formaPago'     => $formaPago,
                    'nombreOficina' => $nombreOficina,
                    'valor'         => $valor
                );
        }    
      
        if (!empty($arreglo))
        {    
            $response = new Response(json_encode(array('total' => $total, 'pagos' => $arreglo)));
        }    
        else 
        {
            $arreglo[] = array();
            $response  = new Response(json_encode(array('total' => $total, 'pagos' => $arreglo)));
        }
        
        $response->headers->set('Content-type', 'text/json');
        return $response; 
    } 
    
     /**
     * Actualizacion: Se agrega URL de logo de Ecuanet
     * @author Javier Hidalgo<jihidalgo@telconet.ec>
     * @version 1.5 09/06/2023
     * 
     * Actualizacion: Se parametriza url del servidor de imágenes.
     * @author Edgar Holguín<eholguin@telconet.ec>
     * @version 1.4 11/01/2018
     * 
     * Actualizacion: Se añade el prefio TNP de la empresa panama, para que muestre el logo
     * @author Germán Valenzuela Franco <gvalenzuela@telconet.ec>
     * @version 1.3 12/07/2017
     * 
     * Actualizacion: Se indenta, se obtiene formas de pago 
     * por defecto si es que la variable $formaspago no tiene datos
     * @author Andres Montero Holguin <amontero@telconet.ec>
     * @version 1.2 14/07/2016
     * 
     * pdfCierreCajaAction
     * Genera data para generar el reporte de cierre de caja
     * Se realiza cambio para envio de la url del logo que se muestra en el reporte según la empresa en sesion
     * @author Edgar Holguin
     * @version 1.1 25/05/2016
     * 
     * @author Telcos
     * @version 1.0 
     * 
     * @return $response
     */
    public function pdfCierreCajaAction()
    {    
        $request           = $this->getRequest();
        $session           = $request->getSession();
        $strPrefijoEmpresa = $session->get('prefijoEmpresa');
        $empresaId         = $session->get('idEmpresa');     
        $feDesde           = $_GET["fedesde"];
        $feHasta           = $_GET["feHasta"];
        $fechaDesde        = date("Y/m/d", strtotime($feDesde));
        $fechaHasta        = date("Y/m/d", strtotime($feHasta));
      
       
        if($fechaDesde=="" || $fechaHasta=="" )
        {
            $fechaDesde = "0000/00/00";
            $fechaHasta = "0000/00/00";
        }
        $formapago = $_GET["formaPago"] ;
        $oficina   = $_GET["oficina"] ;
        
        if (!$formapago)
        {
            $serviceReportes        = $this->get('financiero.Reportes');
            $arrRespuestaFormasPago = $serviceReportes->obtenerFormasPagoParaReporteCierreCaja($prefijoEmpresa);
            $formapago              = $arrRespuestaFormasPago['strFormasPago'];
        } 
        
        $limit            = $request->get("limit");
        $start            = $request->get("start");
        $em               = $this->get('doctrine')->getManager('telconet_financiero');
        //consultando listado de pagos segun parametros enviados
        $resultado        = $em->getRepository('schemaBundle:InfoPagoCab')
                               ->listarCierreCajaXFormaPago($empresaId,$fechaDesde, $fechaHasta,$formapago,$oficina);
        //consultando los total pagos agrupados por forma de pago y segun parametros enviados
        $datosAgrupados   = $em->getRepository('schemaBundle:InfoPagoCab')
                               ->agruparCierreCajaXFormaPago($empresaId,$fechaDesde, $fechaHasta,$formapago ,$oficina,$limit, $start);
        $datos            = $resultado['registros'];
        $total            = $resultado['total'];
        $direccionOficina = "";
        $pbxOficina       = "";
        $nombreOficina    = "";
        
        if($oficina!='')
        {
            $InfoOficinaGrupo = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($oficina );
            $nombreOficina    = $InfoOficinaGrupo->getNombreOficina();
            //obtengo el pbx y la direccion de oficina
            $direccionOficina = "Dirección: ".$InfoOficinaGrupo-> getDireccionOficina();
            $pbxOficina       = "Teléfono: ". $InfoOficinaGrupo-> getTelefonoFijoOficina();
        }
    
        ///Llenando los parametros /////
        $i            = 0;
        $detalles     = array();
        $detAgrupados = array();
        //// llenando los pagos
        foreach ($datos as $dato)
        {
            $id          = $dato['id'];
            $numeroPago  = $dato['numeroPago'];
            $usrCreacion = $dato['usrCreacion'];
            $infoPersona = $em->getRepository('schemaBundle:InfoPersona')->findOneBy(array('login'=> $usrCreacion));
            $empl        = ""   ;
            if($usrCreacion!='Admin Account')  
            { 
                $empl=$infoPersona->getNombres(). " ". $infoPersona->getApellidos();
            }
            else
            {
                $empl="migracion"   ;
            }
            $fechaCreacion = $dato['feCreacion'];
            $ptoCiente     = $dato['puntoId']; 

            if($ptoCiente!='')
            {
                $infoPunto             = $em->getRepository('schemaBundle:InfoPunto')->find($ptoCiente);
                $infoPersonaEmpresaRol = $infoPunto->getPersonaEmpresaRolId();
                $infoPersona_cliente   = $em->getRepository('schemaBundle:InfoPersona')->find( $infoPersonaEmpresaRol->getPersonaId());
                $razonSocial           = $infoPersona_cliente->getRazonSocial();
                $login                 = $infoPunto->getLogin();
                $cliente               = "";
                if($razonSocial=="")
                {
                    $cliente= $infoPersona_cliente->getNombres(). " ". $infoPersona_cliente->getApellidos();
                }
                else
                {
                    $cliente=$razonSocial;
                }
            }
            else
            {
                $cliente="";
            }

            $numReferencia=$dato['numeroReferencia'];

            if($numReferencia =='')
            { // si el numero de referencia esta vacio
                $numReferencia=$dato['bancoCtaContableId'];
            }

            $AdmiFormaPago=$em->getRepository('schemaBundle:AdmiFormaPago')->find($dato['formaPagoId']);
            $formaPago= $AdmiFormaPago->getDescripcionFormaPago();
            $valor=$dato['valorPago'];

            $detalles[$i]['id']            = $id;
            $detalles[$i]['numeroPago']    = $numeroPago;  
            $detalles[$i]['empl']          = $empl; 
            $detalles[$i]['fechaCreacion'] = strval(date_format($fechaCreacion, "d/m/Y G:i")); 
            $detalles[$i]['ptoCiente']     = $ptoCiente; 
            $detalles[$i]['login']         = $login;
            $detalles[$i]['cliente']       = $cliente; 
            $detalles[$i]['numReferencia'] = $numReferencia; 
            $detalles[$i]['formaPago']     = $formaPago; 
            $detalles[$i]['formaPagoId']   = $dato['formaPagoId'];
            $detalles[$i]['valor']         = $valor; 
            $i++;
        }


        ////llenando los datos los agrupados///
        $j=0;
        foreach ($datosAgrupados as $agrupa)
        {
            $detAgrupados[$j]['idFormaPago'] = $agrupa['formaPagoId'];
            $detAgrupados[$j]['cantidad']    = $agrupa['cantFpagos'];
            if($agrupa['formaPagoId']!='')
            {
                $AdmiFormaPago = $em->getRepository('schemaBundle:AdmiFormaPago')->find($agrupa['formaPagoId']);
                $formaPago     = $AdmiFormaPago->getDescripcionFormaPago();
            }
            else
            {
                $formaPago="";
            }
            $detAgrupados[$j]['descripcionFpago']= $formaPago;
            $detAgrupados[$j]['sumaPago']= $agrupa['sumaPago'];
            $sumaTotalCierre+=$agrupa['sumaPago'];
            $j=$j+1; 
        }
        $fechaConsulta  = date("Y/m/d H:i:s");
        //segun el prefijo indico la imagen que saldrá en el pdf
        $strUrlImagenes =  $this->container->getParameter('imageServer');
        if ($strPrefijoEmpresa == 'MD')
        {
            $strLogoEmpresa =  $strUrlImagenes."/others/telcos/logo_netlife_big.jpg";
        }
        elseif($strPrefijoEmpresa == 'TTCO')
        {
            $strLogoEmpresa = $strUrlImagenes."/others/telcos/logo_transtelco_new.jpg";
        }
        elseif($strPrefijoEmpresa == 'TN' || $strPrefijoEmpresa == 'TNP' )
        {
            $strLogoEmpresa = $strUrlImagenes."/logo_telconet.png";
        }
        elseif($strPrefijoEmpresa == 'EN')
        {
            $strLogoEmpresa = $strUrlImagenes."/others/telcos/logo_ecuanet.png";
        }
        else
        {
            //CONDICIÓN SIN PROGRAMACIÓN
        }

        $html = $this->renderView('financieroBundle:reportes:recibo.html.twig',array(
              'detalles'        => $detalles,
              'detAgrupados'    => $detAgrupados,
              'totalRegistros'  => $total,
              'fechaDesde'      => $fechaDesde,
              'fechaHasta'      => $fechaHasta,
              'sumaTotal'       => $sumaTotalCierre,
              'descripcionPago' => $formapago,
              'fechaConsulta'   => $fechaConsulta,
              'nombreoficina'   => $nombreOficina,
              'oficina'         => $oficina,
              'logoEmpresa'     => $strLogoEmpresa,
              'direccionOficina'=> $direccionOficina,
              'pbxOficina'      => $pbxOficina
           
              ) );
      
        return new Response(
                $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                200,
                array(
                    'Content-Type'          => 'application/pdf',
                    'Content-Disposition'   => 'attachment; filename=cierre-caja-'.trim(date("Y-m-d")).'.pdf',
                )
        );
    }

     
     /**
     * 
     * pdfResumenCierreCajaAction
     * Genera data para forfar el reporte resumen de cierre de caja
     *
     * Actualizacion: Se agrega URL de logo de Ecuanet
     * @author Javier Hidalgo<jihidalgo@telconet.ec>
     * @version 1.5 09/06/2023
     * 
     * Actualizacion: Se parametriza url del servidor de imágenes.
     * @author Edgar Holguín<eholguin@telconet.ec>
     * @version 1.4 11/01/2018
     *  
     * Actualizacion: Se añade el prefio TNP de la empresa panama, para que muestre el logo
     * @author Germán Valenzuela Franco <gvalenzuela@telconet.ec>
     * @version 1.3 12/07/2017
     * 
     * Actualizacion: Se indenta, se obtiene formas de pago 
     * por defecto si es que la variable $formaspago no tiene datos
     * @author Andres Montero Holguin <amontero@telconet.ec>
     * @version 1.2 14/07/2016
     * 
     * Se realiza cambio para envio de la url del logo que se muestra en el reporte según la empresa en sesion
     * @author Edgar Holguin
     * @version 1.1 25/05/2016
     * 
     * @author Telcos
     * @version 1.0 
     * 
     * @return $response
     */
     
    public function pdfResumenCierreCajaAction()
    {
        $request           = $this->getRequest();
        $session           = $request->getSession();
        $strPrefijoEmpresa = $session->get('prefijoEmpresa');
        $empresaId         = $session->get('idEmpresa');
        $feDesde           = $_GET["fedesde"];
        $feHasta           = $_GET["feHasta"];
        $fechaDesde        = date("Y/m/d", strtotime($feDesde));
        $fechaHasta        = date("Y/m/d", strtotime($feHasta));
         
        if($fechaDesde=="" || $fechaHasta=="" )
        {
            $fechaDesde = "0000/00/00";
            $fechaHasta = "0000/00/00";
        }
        $serviceReportes        = $this->get('financiero.Reportes');
        $arrRespuestaFormasPago = $serviceReportes->obtenerFormasPagoParaReporteCierreCaja($prefijoEmpresa);
        $formapago              = $arrRespuestaFormasPago['strFormasPago'];
        $oficina                = $_GET["oficina"] ;
        $limit                  = $request->get("limit");
        $start                  = $request->get("start");
        $em                     = $this->get('doctrine')->getManager('telconet_financiero');
        $datosAgrupados         = $em->getRepository('schemaBundle:InfoPagoCab')
                                     ->agruparCierreCajaXFormaPago($empresaId,$fechaDesde, $fechaHasta,$formapago ,$oficina,$limit, $start);
        
        $direccionOficina = "";
        $pbxOficina       = "";
        $nombreOficina    = "";
        
        if($oficina!='')
        {
            $InfoOficinaGrupo = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($oficina );
            $nombreOficina    = $InfoOficinaGrupo->getNombreOficina();
            //obtengo el pbx y la direccion de oficina
            $direccionOficina = "Dirección: ".$InfoOficinaGrupo-> getDireccionOficina();
            $pbxOficina       = "Teléfono: ". $InfoOficinaGrupo-> getTelefonoFijoOficina();
        }
    
        ////Llenando los parametros /////
        $i=0;
      
        $detAgrupados=array();
        //// llenando los pagos
      
        ////llenando los datos los agrupados///
        $j              = 0;
        $totalregistros = 0;
        foreach ($datosAgrupados as $agrupa)
        {
            $totalregistros                     = $totalregistros + $agrupa['cantFpagos'];
            $detAgrupados[$j]['idFormaPago']    = $agrupa['formaPagoId'];
            $detAgrupados[$j]['cantidad']       = $agrupa['cantFpagos'];
            if($agrupa['formaPagoId']!='')
            {
                $AdmiFormaPago = $em->getRepository('schemaBundle:AdmiFormaPago')->find($agrupa['formaPagoId']);
                $formaPago     = $AdmiFormaPago->getDescripcionFormaPago();
            }
            else
            {
                $formaPago="";
            }
            $detAgrupados[$j]['descripcionFpago'] = $formaPago;    
            $detAgrupados[$j]['sumaPago']         = $agrupa['sumaPago'];
            $detAgrupados[$j]['cantFpagos']       = $agrupa['cantFpagos'];

            $sumaTotalCierre+= $agrupa['sumaPago'];
            $j               = $j+1; 
        }

        ////////////exportar a pDF///   
    
        $feIni          = explode('T',$fechaDesde);
        $feFin          = explode('T',$fechaHasta);

        $fechaConsulta  = date("Y/m/d H:i:s");
        //segun el prefijo indico la imagen que saldrá en el pdf

        $strUrlImagenes = $this->container->getParameter('imageServer');

        if ($strPrefijoEmpresa == 'MD')
        {
            $strLogoEmpresa = $strUrlImagenes."/others/telcos/logo_netlife_big.jpg";
        }
        elseif($strPrefijoEmpresa == 'TTCO')
        {
             $strLogoEmpresa = $strUrlImagenes."/others/telcos/logo_transtelco_new.jpg";
        }
        elseif($strPrefijoEmpresa == 'TN' || $strPrefijoEmpresa == 'TNP')
        {
            $strLogoEmpresa = $strUrlImagenes."/logo_telconet.png";
        }
        elseif($strPrefijoEmpresa == 'EN')
        {
            $strLogoEmpresa = $strUrlImagenes."/others/telcos/logo_ecuanet.png";
        }
        else
        {
            //CONDICIÓN SIN PROGRAMACIÓN
        }
      
        $html = $this->renderView('financieroBundle:reportes:reciboResumen.html.twig',
                array(
                    'detAgrupados'      => $detAgrupados,
                    'fechaDesde'        => $feIni[0],
                    'fechaHasta'        => $feFin[0],
                    'sumaTotal'         => $sumaTotalCierre,
                    'cantidadRegistros' => $totalregistros,
                    'totalregistros'    => $totalregistros,
                    'fechaConsulta'     => $fechaConsulta,
                    'nombreoficina'     => $nombreOficina,
                    'oficina'           => $oficina,
                    'logoEmpresa'       => $strLogoEmpresa,
                    'direccionOficina'  => $direccionOficina,
                    'pbxOficina'        => $pbxOficina
                ) 
        );

        return new Response(
                $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                200,
                array(
                    'Content-Type'          => 'application/pdf',
                    'Content-Disposition'   => 'attachment; filename=cierre-caja-'.trim(date("Y-m-d")).'.pdf',
                )
        );
    }

  
    
////////////////////////////////taty: Fin Reporte CierreCaja////////////////////////
    
    /**
     * Documentación para el método 'getListadoErroresAction'.
     *
     * Permite listar los errores presentes en el estado de cuenta
     * - Listado de pagos asociados a facturas anuladas
     * 
     * @return listado_errores Listado de errores.
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 07-10-2014
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.1 07-08-2017
     *
     */
    public function getListadoErroresAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $arrayPtocliente    = $objSession->get('ptoCliente');
        $intIdptocliente    = $arrayPtocliente["id"];
        $intEmpresaId       = $objSession->get('idEmpresa');
        $strIpClient        = $objRequest->getClientIp();
        $strUsrSesion       = $objSession->get('user');
        $serviceUtil        = $this->get('schema.Util');
        $arrayParametros      = array();
        $arrayListadoPagosDep = array();
        $arrayResultados      = array();
        $objResponse          = new Response();
        
        try
        {
            $em_financiero = $this->get('doctrine')->getManager('telconet_financiero');
            $objDb               = $this->container->getParameter('database_dsn');
            $strUserFinanciero   = $this->container->getParameter('user_financiero');
            $strPasswdFinanciero = $this->container->getParameter('passwd_financiero');  
        
            $objOciCon      = oci_connect(
                                           $strUserFinanciero,
                                           $strPasswdFinanciero, 
                                           $objDb
                                         );
            $objCursor       = oci_new_cursor($objOciCon); 
            /*
             * Errores:
             * - Pago asociado a factura anulada
             * - Pagos negativos
             * - Documentos asociados al pto sin referencias
             * - Recupero los pagos dependientes por punto.
             */
            $arrayParametros['intEmpresaId'] = $intEmpresaId;  
            $arrayParametros['intIdPunto']   = $intIdptocliente;
            $arrayParametros['oci_con']      = $objOciCon;
            $arrayParametros['cursor']       = $objCursor;
            
            $arrayListadoPagosDep            = $em_financiero->getRepository('schemaBundle:InfoPagoCab')
                                                             ->getListadoDePagosDependientes($arrayParametros);
            if($arrayListadoPagosDep && count($arrayListadoPagosDep)>0)
            {
                $arrayResultados = array_merge($arrayResultados, $arrayListadoPagosDep);
            }
        }
        catch (\Exception $e) 
        {   
            error_log($e->getMessage());
            $strMensaje= 'Error al listar los errores presentes en el estado de cuenta .';
            $serviceUtil->insertError('Telcos+', 
                                      'ReportesController.getListadoErroresAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );            
        }
        
        $objResponse = new Response(json_encode(array('listado_errores' => $arrayResultados)));
        $objResponse->headers->set('Content-type', 'text/json');
        
        return $objResponse;
    }
    
    /**
     * resumenFacturasElectronicasAction, realiza el render al index del reporte de facturas electronicas
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 10-11-2014
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 01-04-2015
     * @since 1.0
     */
    public function resumenFacturasElectronicasAction($strTipoDocFinanciero)
    {
        switch($strTipoDocFinanciero)
        {
            case 'FAC':
                return $this->render('financieroBundle:reportes:resumenFacturasElectronicas.html.twig');
            case 'FACP':
                return $this->render('financieroBundle:reportes:resumenFacturasElectronicasFACP.html.twig');
            case 'NC':
                return $this->render('financieroBundle:reportes:resumenFacturasElectronicasNC.html.twig');
                
        }
    }

    /**
     * getTotalResumenFactElectronicasAction, Obtiene el resumen por mes de la facturacion electronica
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 10-11-2014
     */
    public function getTotalResumenFactElectronicasAction()
    {
        $objRequest         = $this->getRequest();
        $objSession                             = $objRequest->getSession();
        // Recupero el ID de la empresa en sesión
        $intIdEmpresa                           = $objSession->get('idEmpresa');
        $strFechaDesde      = $objRequest->get('dateFechaDesde');
        $strFechaHasta      = $objRequest->get('dateFechaHasta');
        $intIdOficina       = $objRequest->get('intIdOficina');
        $arrayParametros    = array();
        $arrayParametros['strTipoDocumento'] = $objRequest->get('strTipoDocumento');
        if($strFechaHasta == null && $strFechaHasta == null)
        {
            $strFeCreacionInicio    = '01-' . date('m') . '-' . date('Y') . ' 00:00:00';
            $strFeCreacionFin       = date('d-m') . '-' . date('Y') . ' 23:59:59';
        }
        else
        {
            $strFeCreacionInicio    = date("d-m-Y", strtotime($strFechaDesde)) . ' 00:00:00';
            $strFeCreacionFin       = date("d-m-Y", strtotime($strFechaHasta)) . ' 23:59:59';
        }
        $em_financiero                  = $this->get('doctrine')->getManager('telconet_financiero');
        $arrayFechaIni                  = explode("-", $strFeCreacionInicio);
        $arrayFechaFin                  = explode("-", $strFeCreacionFin);
        $intDiaInicio                   = $arrayFechaIni[0];
        $intMesInicio                   = $arrayFechaIni[1];
        $arrayAnioInicio                = explode(" ", $arrayFechaIni[2]);
        $intAnioInicio                  = $arrayAnioInicio[0];
        $intDiaFin                      = $arrayFechaFin[0];
        $intMesFin                      = $arrayFechaFin[1];
        $intMesFinConst                 = $intMesFin;
        $arrayAnioFin                   = explode(" ", $arrayFechaFin[2]);
        $intAnioFin                     = $arrayAnioFin[0];
        $intAnioFinConst                = $intAnioFin;
        $intCount                       = 0;
        $jsonArray = array();
        for($intAnio = $intAnioInicio; $intAnio <= $intAnioFin; $intAnio ++)
        {
            if(($intAnioFin - $intAnioInicio) >= 1)
            {
                $intMesFin = 12;
            }
            if($intAnio == $intAnioFin)
            {
                $intMesFin = $intMesFinConst;
            }
            for($intMes = $intMesInicio; $intMes <= $intMesFin; $intMes ++)
            {
                $intFinalDay            = cal_days_in_month(CAL_GREGORIAN, $intMes, $intAnio);
                $strFeCreacionInicio    = '01-' . str_pad($intMes, 2, "0", STR_PAD_LEFT) . '-' . $intAnio . ' 00:00:00';
                $strFeCreacionFin       = $intFinalDay . '-' . $intMes . '-' . $intAnio . ' 23:59:59';
                $arrayParametros['strFeCreacionInicio']     = $strFeCreacionInicio;
                $arrayParametros['strFeCreacionFin']        = $strFeCreacionFin;
                if($intCount == 0)
                {
                    $arrayParametros['strFeCreacionInicio'] = $intDiaInicio . '-' . $intMes . '-' . $intAnio . ' 00:00:00';
                    $intCount ++;
                }
                if($intMesFinConst == $intMes && $intAnioFinConst == $intAnio)
                {
                    $arrayParametros['strFeCreacionFin'] = $intDiaFin . '-' . $intMes . '-' . $intAnio . ' 23:59:59';
                }
                $arrayParametros['intEstado']       = NULL;
                $arrayParametros['intOficinaId']    = $intIdOficina;
                $arrayParametros['strCodEmpresa']   = $intIdEmpresa;
                $arrayResult                        = $em_financiero->getRepository('schemaBundle:InfoComprobanteElectronico')
                                                                    ->getResumenCompElectronicos($arrayParametros);
                $jsonArray                          = array_merge($arrayResult, $jsonArray);
            }
            $intMesInicio   = 1;
            $intAnioInicio  = 1;
        }
        $objResponse = new Response(json_encode(array('arraResult' => $jsonArray)));
        return $objResponse;
    }
    
    public function getDocumentosNoCreadosAction(){
        $emFinanciero   = $this->get('doctrine')->getManager('telconet_financiero');
        $jsonArray      = $emFinanciero->getRepository('schemaBundle:InfoComprobanteElectronico')->getDocumentosNoCreados();
        $objResponse = new Response(json_encode(array('arrayComprobantesNoCreados' => $jsonArray)));
        return $objResponse;
    }

    /**
     * listarReportesDinardapAction
     * Metodo para la presentacion del grid de los reportes de la dinardap por empresa
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @since 24/02/2015
     * @version 1.0
     * @return view
     */
    public function listarReportesDinardapAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $prefijoEmpresa = $session->get('prefijoEmpresa');
        return $this->render('financieroBundle:reportes:listadoReportesDinardap.html.twig', array('prefijoEmpresa' => $prefijoEmpresa));
    }

    /**
     * gridReportesDinardapAction
     * Metodo que obtiene los archivos de la cartera de clientes segun la empresa
     * Se modifica:
     *  - Forma de acceso al direcctorio fisico de los archivos
     *  - Forma de pasar los parametros para la descarga unificada de los archivos ZIP
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @since 24/02/2015
     * @version 2.0 18-08-2015
     * @return json con el listado de archivos
     */
    public function gridReportesDinardapAction()
    {
        $request = $this->getRequest();
        $mes = $request->get('mes');
        $anio = $request->get('anio');

        //Busqueda de archivos
        $path_telcos = $this->container->getParameter('path_telcos');
        $findPath = $path_telcos . 'telcos/web/public/uploads/reportes/dinardap/';

        if($mes && $anio)
        {
            if(strlen($mes) == 1)
                $mes = "0" . $mes;
            else
                $mes = $mes;
            $criterioFecha = $anio . "-" . $mes;
        }
        else
        {
            $criterioFecha = date('Y-m');
        }

        $prefijoEmpresa = $request->getSession()->get('prefijoEmpresa');
        $finder = new Finder();
        $finder->name("reporteDinardapZip" . $prefijoEmpresa . "*" . $criterioFecha . "*")->files()->in($findPath);
        $finder->sortByChangedTime();

        foreach($finder as $file)
        {
            $pos = strpos($file->getRealpath(), $file->getRelativePathname());
            $ruta = str_replace("/", "-", substr($file->getRealpath(), 0, $pos));
            $urlArchivo = $this->generateUrl('reportes_descarga', array('archivo' => $file->getRelativePathname(), 'ruta' => $ruta));

            $arreglo[] = array(
                'linkVer' => $file->getRelativePathname(),
                'linkFile' => $urlArchivo,
                'size' => (round(filesize($file->getRealpath()) / 1024 / 1024, 2)) . ' Mb'
            );
        }

        $response = new Response(json_encode(array('total' => $total, 'archivos' => $arreglo)));

        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /**
     * getReportePagosLineaAction, Muestra la pagina principal del resumen de pagos en linea
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 28-09-2015
     * 
     * @return render Redirecciona al twig del resumen de pagos en linea
     *
     *
     * 
     */
    public function getReportePagosLineaAction()
    {
        $emSeguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("315", "1");
        return $this->render('financieroBundle:reportes:reportePagosLinea.html.twig', array('item' => $entityItemMenu));
    }//getReportePagosLineaAction
    
    /**
     * getGridReportePagosLineaAction, metodo que realiza la llamada al repositorio de la entidad InfoPagoLinea para obtener los datos de la busqueda
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 28-09-2015
     * 
     * @return json Retorna un json obtenido del array de la consulta realiza segun los parametros enviados en el request
     *
     * @author Javier Hidalgo <jihidalgo@telconet.ec>
     * @version 1.1 10-05-2023  Se modifica array de entrada arrayIdsCanalPagoLinea. Si es 0 (todos) lo descartamos y capturamos los demas IDs.
     * 
     */
    public function getGridReportePagosLineaAction()
    {
        
        $objRequest                                = $this->getRequest();
        $objSession                                = $objRequest->getSession();
        $arrayParametros['strCodEmpresa']          = $objSession->get('idEmpresa');
        $arrayParametros['strFechaInicio']         = explode('T', $objRequest->get('dateFechaDesde'))[0];
        $arrayParametros['strFechaFin']            = explode('T', $objRequest->get('dateFechaHasta'))[0];
        $arrayParametros['arrayIdsCanalPagoLinea'] = explode(',', $objRequest->get('strCboCanalPagoLinea'));
        $arrayParametros['strTipoQuery']           = $objRequest->get('strTipoQuery');
        $arrayParametros['strUsrCreacion']         = $objRequest->get('strUsrCreacion');
        
        //Si el valor del combo contiene el 0 (Todos) solo tomamos los canales respectivos a la empresa.
        if(in_array(0, $arrayParametros['arrayIdsCanalPagoLinea']))
        {
            unset($arrayParametros['arrayIdsCanalPagoLinea'][0]);
        }

        $arrayParametros['strDateFormat']         = 'd-m-Y';
        $arrayParametros['strTimeInicio']         = ' 00:00:00';
        $arrayParametros['strTimeFin']            = ' 23:59:59';
        
        //Obtiene fechas por default del dia si es que en el request se han enviado vacias
        $arrayFechaInicioFin = $this->validadorFechasInicioFin($arrayParametros);
        
        $arrayParametros['strFechaInicio'] = $arrayFechaInicioFin['strFechaInicio'];
        $arrayParametros['strFechaFin']    = $arrayFechaInicioFin['strFechaFin'];
        $emFinanciero           = $this->get('doctrine')->getManager('telconet_financiero');
        $arrayResumenPagosLinea = $emFinanciero->getRepository('schemaBundle:InfoPagoLinea')->getResumenPagosLinea($arrayParametros);
        $objResponse = new Response(json_encode(array('jsonResumenPagosLinea' => $arrayResumenPagosLinea['arrayDatos'],
                                                      'strStatus'             => $arrayResumenPagosLinea['strStatus'],
                                                      'strMensaje'            => $arrayResumenPagosLinea['strMensaje'])));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }//getReportePagosLineaAction
    
    /**
     * exportResumenPagosLineaAction, metodo que realiza la exportacion del archivo excel con la data obtenida del repositorio de la entidad 
     * InfoPagoLinea
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 28-09-2015
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 29-06-2016 Se modifica la manera de obtener la imagen para el reporte
     * @since 1.0
     * 
     * @return exit Retorna el archivo excel con la informacion enviada a obtener.
     *
     *
     * 
     */
     public function exportResumenPagosLineaAction()
    {
        try
        {
            error_reporting(E_ALL);
            ini_set('max_execution_time', 3000000);
            $objRequest                               = $this->getRequest();
            $objSession                               = $objRequest->getSession();
            $strUsuario                               = $objSession->get('user');
            $arrayParametros['strFechaInicio']        = $objRequest->get('strFechaInicio');
            $strCodEmpresa                            = $objSession->get('idEmpresa');
            $arrayParametros['arrayStrCodEmpresa']    = !empty($strCodEmpresa) ? [$strCodEmpresa] : '';
            $arrayParametros['strFechaFin']           = $objRequest->get('strFechaFin');
            $intIdCanal                               = $objRequest->get('intIdCanal');
            $arrayParametros['arrayIntCanalPagoLinea']= !empty($intIdCanal) ? [$intIdCanal] : '';
            $arrayParametros['strUsrCreacion']        = $objRequest->get('strUsrCreacion');
            $arrayParametros['strDateFormat']         = 'd-m-Y';
            $arrayParametros['strTimeInicio']         = ' 00:00:00';
            $arrayParametros['strTimeFin']            = ' 23:59:59';
            
            //Obtiene fechas por default en caso que en el request hayan sido enviadas vacias
            $arrayFechaInicioFin = $this->validadorFechasInicioFin($arrayParametros);
            $arrayParametros['strFechaInicio'] = $arrayFechaInicioFin['strFechaInicio'];
            $arrayParametros['strFechaFin']    = $arrayFechaInicioFin['strFechaFin'];

            $emFinanciero                      = $this->get('doctrine')->getManager('telconet_financiero');
            $emGeneral                         = $this->get('doctrine')->getManager('telconet_general');
            $arrayResumenPagosLinea            = $emFinanciero->getRepository('schemaBundle:InfoPagoLinea')
                                                              ->exportResumenPagosLinea($arrayParametros);

            $objPHPExcel = new PHPExcel();
            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '1024MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

            $objPHPExcel->getProperties()->setCreator("TELCOS++");
            $objPHPExcel->getProperties()->setLastModifiedBy($strUsuario);
            $objPHPExcel->getProperties()->setTitle("Resumen Pagos en linea");
            $objPHPExcel->getProperties()->setSubject("Resumen Pagos en linea");
            $objPHPExcel->getProperties()->setDescription("Muestra el resumen detallado de los pagos en linea");
            $objPHPExcel->getProperties()->setKeywords("Asociado");
            $objPHPExcel->getProperties()->setCategory("Reporte");

            //Crea estilo para el titulo del reporte
            $arrayStyleTitulo = array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '006699'),
                    'size' => 12,
                    'name' => 'LKLUG'
                ),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFFFF')
                )
            );

            //Crea estilo para la cabecera del reporte
            $arrayStyleCabecera = array(
                'font' => array(
                    'bold' => false,
                    'color' => array('rgb' => 'FFFFFF'),
                    'size' => 10,
                    'name' => 'LKLUG'
                ),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '888888')
                )
            );

            //Crea estilo para el cuerpo del reporte
            $arrayStyleBodyTable = array(
                'font' => array(
                    'bold' => false,
                    'color' => array('rgb' => '000000'),
                    'size' => 8,
                    'name' => 'LKLUG'
                ),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFFFF')
                )
            );

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(60);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Reporte detallado de pagos en linea Fecha: '. $arrayParametros['strFechaInicio'].
                                                         ' - '.$arrayParametros['strFechaFin']);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($arrayStyleTitulo);
            
            //busca el directorio de la imagen
            $arrayDirImagen = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->getOne('PARAMETROS_PAGOS_LINEA',
                                                 'FINANCIERO',
                                                 'PAGOS_LINEA',
                                                 '',
                                                 'DIR_IMAGEN_PAGOS_LINEA',
                                                 '',
                                                 '',
                                                 '',
                                                 '',
                                                 $strCodEmpresa);
            //Si encuentra la ruta de la imagen procede a agregarla al excel
            if(!empty($arrayDirImagen['valor2']))
            {
                $objImage = imagecreatefromjpeg($arrayDirImagen['valor2']);
                //Si obtiene la imagen la crea en la celda A1
                if($objImage){
                    $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                    $objDrawing->setName('TELCOS++');
                    $objDrawing->setDescription('TELCOS++');
                    $objDrawing->setImageResource($objImage);
                    $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                    $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                    $objDrawing->setHeight(100);
                    $objDrawing->setWidth(138);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                }
            }
            
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A2', 'Fecha Registro Pago')
                ->setCellValue('B2', 'Fecha Transaccion Pago')
                ->setCellValue('C2', 'Empresa')
                ->setCellValue('D2', 'Canal recaudador')
                ->setCellValue('E2', 'Numero Pago')
                ->setCellValue('F2', 'Valor')
                ->setCellValue('G2', 'Nombre Cliente')
                ->setCellValue('H2', 'Identificación cliente')
                ->setCellValue('I2', 'Estado')
                ->setCellValue('J2', 'Usuario Creación')
                ->setCellValue('K2', 'Proceso Masivo');
            $objPHPExcel->getActiveSheet()->getStyle('A2:K2')->applyFromArray($arrayStyleCabecera);
            $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
            $objPHPExcel->getActiveSheet()->getStyle('A2:K2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

            $intCounterRows = 3;
            foreach($arrayResumenPagosLinea['arrayDatos']  as $arrayDatosPagosLinea):
                $objPHPExcel->getActiveSheet()->getStyle('A'. $intCounterRows .':K'. $intCounterRows)->applyFromArray($arrayStyleBodyTable);
                $objPHPExcel->getActiveSheet()->getStyle('A'. $intCounterRows .':K'. $intCounterRows)
                            ->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getRowDimension($intCounterRows)->setRowHeight(20);
                $objPHPExcel->getActiveSheet()->setCellValue('A'. $intCounterRows, $arrayDatosPagosLinea['FE_CREACION']);
                $objPHPExcel->getActiveSheet()->setCellValue('B'. $intCounterRows, $arrayDatosPagosLinea['FE_TRANSACCION']);
                $objPHPExcel->getActiveSheet()->setCellValue('C'. $intCounterRows, $arrayDatosPagosLinea['NOMBRE_EMPRESA']);
                $objPHPExcel->getActiveSheet()->setCellValue('D'. $intCounterRows, $arrayDatosPagosLinea['DESCRIPCION_CANAL_PAGO_LINEA']);
                $objPHPExcel->getActiveSheet()->setCellValue('E'. $intCounterRows, $arrayDatosPagosLinea['NUMERO_REFERENCIA']);
                $objPHPExcel->getActiveSheet()->setCellValue('F'. $intCounterRows, $arrayDatosPagosLinea['VALOR_PAGO_LINEA']);
                $objPHPExcel->getActiveSheet()->getStyle('F'. $intCounterRows)->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $objPHPExcel->getActiveSheet()->setCellValue('G'. $intCounterRows, $arrayDatosPagosLinea['NOMBRE_PERSONA']);
                $objPHPExcel->getActiveSheet()->getStyle('G'. $intCounterRows)->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $objPHPExcel->getActiveSheet()
                            ->setCellValueExplicit('H'. $intCounterRows, 
                                                   $arrayDatosPagosLinea['IDENTIFICACION_CLIENTE'], 
                                                    PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue('I'. $intCounterRows, $arrayDatosPagosLinea['ESTADO_PAGO_LINEA']);
                $objPHPExcel->getActiveSheet()->setCellValue('J'. $intCounterRows, $arrayDatosPagosLinea['USR_CREACION']);
                $objPHPExcel->getActiveSheet()->setCellValue('K'. $intCounterRows, $arrayDatosPagosLinea['PROCESO_MASIVO_ID']);
                $intCounterRows = $intCounterRows + 1;
            endforeach;

            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="ResumenPagosLinea' . date('d_M_Y') . '.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        } catch (\Exception $ex) {
            exit;
        }
    }//exportResumenPagosLineaAction

    /**
     * validadorFechasInicioFin, metodo valida la fecha desde y hasta enviada en el request si las fechas estan vacias retorna el dia actual
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 28-09-2015
     * 
     * @return array Retorna las fechas enviadas en el request o el dia actual en caso de que hayan sido enviadas vacias
     */
    private function validadorFechasInicioFin($arrayParametros)
    {
        //Crea la fecha actual
        if(empty($arrayParametros['strFechaInicio']) && empty($arrayParametros['strFechaFin']))
        {
            $arrayResponse['strFechaInicio'] = date($arrayParametros['strDateFormat']).$arrayParametros['strTimeInicio'];
            $arrayResponse['strFechaFin']    = date($arrayParametros['strDateFormat']).$arrayParametros['strTimeFin'];
        } //Le da formato a la fecha enviada en el request
        else if(!empty($arrayParametros['strFechaInicio']) && !empty($arrayParametros['strFechaFin']))
        {
            $arrayResponse['strFechaInicio'] = date_format(date_create($arrayParametros['strFechaInicio']), 
                                                                       $arrayParametros['strDateFormat']).$arrayParametros['strTimeInicio'];
            $arrayResponse['strFechaFin']    = date_format(date_create($arrayParametros['strFechaFin']), 
                                                                       $arrayParametros['strDateFormat']).$arrayParametros['strTimeFin'];
        }
        //Crea la fecha de incio y le da formato a la fecha fin
        if(!empty($arrayParametros['strFechaInicio']) && empty($arrayParametros['strFechaFin']))
        {
            $arrayResponse['strFechaInicio']  = date($arrayParametros['strDateFormat']).$arrayParametros['strTimeFin'];
            $arrayResponse['strFechaFin']     = date_format(date_create($arrayParametros['strFechaFin']), 
                                                                        $arrayParametros['strDateFormat']).$arrayParametros['strTimeFin'];
        }
        //Le da formato a la fecha inicio y crea la fecha fin
        if(empty($arrayParametros['strFechaInicio']) && !empty($arrayParametros['strFechaFin']))
        {
            $arrayResponse['strFechaInicio'] = date_format(date_create($arrayParametros['strFechaInicio']), 
                                                                       $arrayParametros['strDateFormat']).$arrayParametros['strTimeInicio'];
            $arrayResponse['strFechaFin']    = date($arrayParametros['strDateFormat']).$arrayParametros['strTimeFin'];
        }
        return $arrayResponse;
    }//validadorFechasInicioFin
     
    
    /**
     * @Secure(roles="ROLE_339-1")
     * 
     * Documentación para 'reportesBuroAction'
     * 
     * Método que muestra la vista que contiene los reportes para el buró de crédito generados por los usuarios.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 15-03-2016
     * 
     * @return view
     */
    public function reportesBuroAction()
    {        
        if (true === $this->get('security.context')->isGranted('ROLE_339-3577'))
        {
            $rolesPermitidos[] = 'ROLE_339-3577'; //Descargar reporte de buro
        }
        
        if (true === $this->get('security.context')->isGranted('ROLE_339-9'))
        {
            $rolesPermitidos[] = 'ROLE_339-9'; //Eliminar Reporte de Buro
        }
        
        return $this->render('financieroBundle:reportes:reportesBuro.html.twig', array('rolesPermitidos' => $rolesPermitidos));
    }
    
    
    /**
     * @Secure(roles="ROLE_339-7")
     * 
     * Documentación para 'gridReportesBuroAction'
     * 
     * Método que retorna los reportes para el buró de crédito generados por los usuarios.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 15-03-2016
     * 
     * @return view
     */
    public function gridReportesBuroAction()
    {
        $objResponse              = new Response();
        $objRequest               = $this->getRequest();
        $objSession               = $objRequest->getSession();
        $intIdEmpresaSession      = $objSession->get('idEmpresa');
        $emSeguridad              = $this->get('doctrine')->getManager('telconet_seguridad');
        $intStart                 = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
        $intLimit                 = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;
        $strFechaBusqueda         = $objRequest->query->get('fecha') ? $objRequest->query->get('fecha') : '';
        $strTipo                  = $objRequest->query->get('tipo') ? $objRequest->query->get('tipo') : '';
        $arrayTmpFechaInicio      = $strFechaBusqueda ? explode("T", $strFechaBusqueda): array();
        $arrayResultados          = array();
        $dateFechaInicio          = null;
        $dateFechaFinal           = null;
        $serviceInfoTransacciones = $this->get('seguridad.InfoTransacciones');
        
        /*
         * Bloque if( $arrayTmpFechaInicio )
         * 
         * Genera la fecha en el formato necesario usado por el doctrine para luego ser convertido al formato usado por la base de datos
         */
        if( $arrayTmpFechaInicio )
        {
            $arrayFechaInicio = explode("-", $arrayTmpFechaInicio[0]);
            $timeFechaInicio  = strtotime("01-".$arrayFechaInicio[1]."-".$arrayFechaInicio[0]);
            $dateFechaInicio  = date("Y/m/d", $timeFechaInicio);

            $dateFechaFinal  = strtotime(date("d-m-Y", $timeFechaInicio)." +1 month");
            $dateFechaFinal  = date("Y/m/d", $dateFechaFinal);
        }
        /*
         * Fin Bloque if( $arrayTmpFechaInicio )
         */
        
        
        /*
         * Bloque que retorna los reportes enviados al buró de crédito generados por los usuarios
         */
        $arrayParametros = array(
                                    'intStart'         => $intStart,
                                    'intLimit'         => $intLimit,
                                    'route'            => $this->get('router'),
                                    'strNombreReporte' => 'reportesBuro',
                                    'criterios'        => array(
                                                                    'tipoTransaccion'      => 'Generar',
                                                                    'empresa'              => $intIdEmpresaSession,
                                                                    'estadosTransacciones' => array('Activo', 'Pendiente'),
                                                                    'nombreModulo'         => 'reporte_buro',
                                                                    'nombreAccion'         => 'create',
                                                                    'estadosModulo'        => array('Activo', 'Modificado'),
                                                                    'estadosAcciones'      => array('Activo', 'Modificado'),
                                                                    'feInicial'            => $dateFechaInicio,
                                                                    'feFinal'              => $dateFechaFinal
                                                                )
                                );
        
        $jsonResultados = $emSeguridad->getRepository('schemaBundle:InfoTransacciones')->getJsonTransaccionesByCriterios( $arrayParametros );
            
        $objResponse->setContent( $jsonResultados );
        /*
         * Fin Bloque que retorna los reportes enviados al buró de crédito generados por los usuarios
         */
        
        
        /*
         * Bloque que guarda la transacción ejecutada por el usuario
         */
        if( $strTipo == 'usuario' )
        {
            $arrayTmpParametros = array( 'intStart'          => 0,
                                         'intLimit'          => 1,
                                         'empresaSession'    => $intIdEmpresaSession,
                                         'estadoTransaccion' => 'Activo',
                                         'usuarioSession'    => $objSession->get('user'),
                                         'ipSession'         => $objRequest->getClientIp(),
                                         'nombreTransaccion' => "Consultar reporte de buro",
                                         'tipoTransaccion'   => "Consultar",
                                         'criterios'         => array( 'nombreModulo'    => 'reporte_buro',
                                                                       'nombreAccion'    => 'grid',
                                                                       'estadosModulo'   => array('Activo', 'Modificado'),
                                                                       'estadosAcciones' => array('Activo', 'Modificado') ) );

            $boolGuardado = $serviceInfoTransacciones->guardarTransaccion($arrayTmpParametros);
        }
        /*
         * Fin Bloque que guarda la transacción ejecutada por el usuario
         */
        
        return $objResponse;
    }
    
    
    /**
     * @Secure(roles="ROLE_339-3577")
     * 
     * Documentación para 'descargarReporteBuroAction'
     * 
     * Método que descarga el archivo generado para el buró de crédito.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 15-03-2016
     * 
     * @param String $strNombreArchivo Nombre del archivo a descargar
     * 
     * @return $objResponse
     */
    public function descargarReporteBuroAction($strNombreArchivo)
    {
        $arrayParametros = array( 'nombreCarpeta'     => "reportesBuro",
                                  'nombreArchivo'     => $strNombreArchivo,
                                  'nombreTransaccion' => "Descarga del reporte de buro en formato ZIP",
                                  'nombreModulo'      => 'reporte_buro',
                                  'nombreAccion'      => 'exportarAExcel' );
        
        return $this->downloadArchivoGeneradoAction($arrayParametros);
    }
    
    
    /**
     * Documentación para 'downloadArchivoGeneradoAction'
     * 
     * Método descarga el archivo generado.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 15-03-2016
     * 
     * @param array $arrayParametros ['nombreCarpeta', 'nombreArchivo', 'nombreTransaccion', 'nombreModulo', 'nombreAccion']
     * 
     * @return $objResponse
     */
    private function downloadArchivoGeneradoAction($arrayParametros)
    {
        $objRequest          = $this->getRequest();
        $objSession          = $objRequest->getSession();
        $intIdEmpresaSession = $objSession->get('idEmpresa');
        $strPathTelcos       = $this->container->getParameter('path_telcos');
        
        $strPath                  = $strPathTelcos.'telcos/web/public/uploads/'.$arrayParametros["nombreCarpeta"].'/'.
                                    $arrayParametros["nombreArchivo"];
        $fileContent              = file_get_contents($strPath);
        $objResponse              = new Response();
        $serviceInfoTransacciones = $this->get('seguridad.InfoTransacciones');
        
        /*
         * Bloque que guarda la transacción ejecutada por el usuario
         */        
        $arrayTmpParametros = array( 'intStart'          => 0,
                                     'intLimit'          => 1,
                                     'empresaSession'    => $intIdEmpresaSession,
                                     'estadoTransaccion' => 'Activo',
                                     'usuarioSession'    => $objSession->get('user'),
                                     'ipSession'         => $objRequest->getClientIp(),
                                     'nombreTransaccion' => $arrayParametros["nombreTransaccion"],
                                     'tipoTransaccion'   => "Descargar",
                                     'criterios'         => array( 'nombreModulo'    => $arrayParametros["nombreModulo"],
                                                                   'nombreAccion'    => $arrayParametros["nombreAccion"],
                                                                   'estadosModulo'   => array('Activo', 'Modificado'),
                                                                   'estadosAcciones' => array('Activo', 'Modificado') ) );
        
        $boolGuardado = $serviceInfoTransacciones->guardarTransaccion($arrayTmpParametros);
        /*
         * Fin Bloque que guarda la transacción ejecutada por el usuario
         */
        
        
        //set headers
        $objResponse->headers->set('Content-Type', 'mime/type');
        $objResponse->headers->set('Content-Disposition', 'attachment;filename="'.$arrayParametros["nombreArchivo"]);
        $objResponse->setContent($fileContent);
        
        return $objResponse;
    }
         
    
    /**
     * @Secure(roles="ROLE_339-2")
     * 
     * Documentación para 'generarReporteBuroAction'
     * 
     * Método que muestra la pantalla para generar los reportes para el buró de crédito.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 15-03-2016
     * 
     * @return view
     */
    public function generarReporteBuroAction()
    {
        $objRequest          = $this->getRequest();
        $objSession          = $objRequest->getSession();
        $intIdEmpresaSession = $objSession->get('idEmpresa');
        $emGeneral           = $this->get('doctrine')->getManager('telconet_general');
        
        $arrayParametros                      = array();
        $arrayParametros['strClientesBuenos'] = '50';
        $arrayParametros['strClientesMalos']  = '20';
        
        /*
         * Se utilizan el parámetro de CLIENTES_BUENOS para obtener el valor de deuda permitido con el cual se va a clasificar a los clientes 
         * como Buenos
         */
        $arrayClienteBueno = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                       ->getOne('VALORES_CLIENTES_BURO', '', '', 'CLIENTES_BUENOS', '', '', '', '', '', $intIdEmpresaSession);
        
        if( $arrayClienteBueno )
        {
            $arrayParametros['strClientesBuenos'] = $arrayClienteBueno['valor1'];
        }
        
        
        
        /*
         * Se utilizan el parámetro de CLIENTES_MALOS para obtener el valor de deuda permitido con el cual se va a clasificar a los clientes 
         * como Malos
         */
        $arrayClienteMalo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->getOne('VALORES_CLIENTES_BURO', '', '', 'CLIENTES_MALOS', '', '', '', '', '', $intIdEmpresaSession);
        
        if( $arrayClienteMalo )
        {
            $arrayParametros['strClientesMalos'] = $arrayClienteMalo['valor1'];
        }
        
        return $this->render('financieroBundle:reportes:generarReporteBuro.html.twig', $arrayParametros);
    }
    
    
    /**
     * @Secure(roles="ROLE_339-3")
     * 
     * Documentación para funcion 'crearReporteBuroAction'.
     * 
     * Método que realiza llamada a script que genera el reporte para el buró de crédito
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 15-03-2016
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.1 10-08-2017 Se agrega llamada a función que invoca procedimiento que genera el reporte de buro según los parámetros enviados.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.2 12-09-2017 Se agrega validación para verificar por medio de un historial en estado 'Pendiente' si existe un reporte en ejecución.
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.3 12-09-2017 Se agrega validación para verificar por medio de la transacción existente en estado 'Pendiente' si existe un reporte
     *                         en ejecución, se omite validación con respecto al historial.
     *  
     * @return objeto - render (Renderiza una vista)
     */    
    public function crearReporteBuroAction()
    {
        $strPathTelcos       = $this->container->getParameter('path_telcos');
        $strHostScripts      = $this->container->getParameter('host_scripts');    
        $strUploadPath       = $strPathTelcos.'telcos/web/public/uploads/reportesBuro/';
        $emGeneral           = $this->get('doctrine')->getManager('telconet_general');
        $emFinanciero        = $this->getDoctrine()->getManager('telconet_financiero');
        $emSeguridad         = $this->get('doctrine')->getManager('telconet_seguridad');
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $objRequest          = $this->getRequest();
        $strPrefijoEmpresa   = $objRequest->getSession()->get('prefijoEmpresa');
        $intIdEmpresaSession = $objRequest->getSession()->get('idEmpresa');
        $intIdPersRolSession = $objRequest->getSession()->get('idPersonaEmpresaRol');
        $strIpSession        = $objRequest->getClientIp();
        $strUserSession      = $objRequest->getSession()->get('user');
        $strClientesBuenos   = $objRequest->request->get('clientesBuenos') ? $objRequest->request->get('clientesBuenos') : '';
        $strClientesMalos    = $objRequest->request->get('clientesMalos') ? $objRequest->request->get('clientesMalos') : '';
        $strMensaje          = "El reporte solicitado se está procesando. Llegará un correo notificando cuando termine el proceso.";
        $serviceUtil         = $this->get('schema.Util');
        
        if( !$strClientesBuenos )
        {
            /*
             * Se utilizan el parámetro de CLIENTES_BUENOS para obtener el valor de deuda permitido con el cual se va a clasificar a los clientes 
             * como Buenos
            */
            $arrayClienteBueno = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getOne('VALORES_CLIENTES_BURO', '', '', 'CLIENTES_BUENOS', '', '', '', '', '', $intIdEmpresaSession);
        
            if( $arrayClienteBueno )
            {
                $strClientesBuenos = $arrayClienteBueno['valor1'];
            }
        }
        
        
        if( !$strClientesMalos )
        {
            /*
             * Se utilizan el parámetro de CLIENTES_MALOS para obtener el valor de deuda permitido con el cual se va a clasificar a los clientes 
             * como Malos
            */
            $arrayClienteMalo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                          ->getOne('VALORES_CLIENTES_BURO', '', '', 'CLIENTES_MALOS', '', '', '', '', '', $intIdEmpresaSession);
        
            if( $arrayClienteMalo )
            {
                $strClientesMalos = $arrayClienteMalo['valor1'];
            }
        }

        $strPathFileLogger     = '/home/scripts-telcos/md/financiero/logs/reportes-buro-credito/';
        $strNameFileLogger     = 'reportes-buro-credito';
        $strAmbiente           = 'TELCOS'; 
        $strEmailUsrSesion     = '';
        $strValorFormaContacto = ''; 
            
        $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->find($intIdPersRolSession);

        if(is_object($objInfoPersonaEmpresaRol))
        {
            $strValorFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                 ->getValorFormaContactoPorCodigo($objInfoPersonaEmpresaRol->getPersonaId(),'MAIL');

            if(!is_null($strValorFormaContacto))
            {
                $strEmailUsrSesion = strtolower($strValorFormaContacto);
            }                
        }         
        
        $arrayParametros = array( 'strHost'                => $strHostScripts,
                                  'strPathFileLogger'      => $strPathFileLogger,
                                  'strNameFileLogger'      => $strNameFileLogger,
                                  'strPrefijoEmpresa'      => $strPrefijoEmpresa,
                                  'strIpSession'           => $strIpSession,
                                  'strUsuarioSession'      => $strUserSession,
                                  'strValorClientesBuenos' => $strClientesBuenos,
                                  'strValorClientesMalos'  => $strClientesMalos,
                                  'strDirectorioUpload'    => $strUploadPath,
                                  'strAmbiente'            => $strAmbiente,
                                  'emailUsrSesion'         => $strEmailUsrSesion);
        
        $arrayRptBuroPendientes = $emSeguridad->getRepository('schemaBundle:InfoTransacciones')
                                               ->findBy(array('empresaId'        =>$intIdEmpresaSession,
                                                              'tipoTransaccion'  =>'Generar',
                                                              'estado'           =>'Pendiente'));
        if(count($arrayRptBuroPendientes)==0)
        {

            $emFinanciero->getConnection()->beginTransaction();

            try
            {
                $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->ejecutarReporteBuro($arrayParametros);

                $emFinanciero->getConnection()->commit();

            }
            catch(\Exception $e) 
            {
                $strMensaje = 'Error al generar reporte de buro.';

                $serviceUtil->insertError( 'Telcos+', 
                                           'Reporte de buro', 
                                           'Error al generar reporte de buro . '.$e->getMessage(), 
                                           $strUserSession, 
                                           $strIpSession ); 

                $emFinanciero->getConnection()->rollback();

                $emFinanciero->getConnection()->close();
            }
        }
        else
        {
            $strMensaje = "Actualmente existe un reporte que  se está procesando. Llegará un correo notificando cuando termine el proceso.";
        }

        $this->get('session')->getFlashBag()->add('subida', $strMensaje);        
        
        return $this->redirect($this->generateUrl('reportes_buro', array()));       
    }
    
    
    /**
     * @Secure(roles="ROLE_339-9")
     * 
     * Documentación para funcion 'deleteAjaxAction'.
     * 
     * Método que realiza la actualización del estado de un reporte a 'Eliminado'
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 15-03-2016
     * 
     * @return $jsonResponse
     */    
    public function deleteAjaxAction()
    {
        $objRequest          = $this->getRequest();
        $emSeguridad         = $this->get('doctrine')->getManager('telconet_seguridad');
        $strIpSession        = $objRequest->getClientIp();
        $strUserSession      = $objRequest->getSession()->get('user');
        $intIdTransaccion    = $objRequest->request->get('idTransaccion') ? $objRequest->request->get('idTransaccion') : 0;
        $strResponse         = 'No se encontró reporte';
        
        $objInfoTransacciones = $emSeguridad->getRepository('schemaBundle:InfoTransacciones')->findOneById($intIdTransaccion);  
        
        
        if( $objInfoTransacciones )
        {
            $emSeguridad->getConnection()->beginTransaction();

            try
            {
                $objInfoTransacciones->setEstado("Eliminado");
                $objInfoTransacciones->setFeUltMod(new \DateTime());
                $objInfoTransacciones->setUsrUltMod($strUserSession);
                $objInfoTransacciones->setIpUltMod($strIpSession);
                $emSeguridad->persist($objInfoTransacciones);
                $emSeguridad->flush();	

                $emSeguridad->getConnection()->commit();	
                
                $strResponse = 'OK';	
            }
            catch(Exception $e)
            {
                error_log($e->getMessage());
                $strResponse = 'Hubo un problema al eliminar el registro de la base de datos';

                $emSeguridad->getConnection()->rollback();		
            }
        
            $emSeguridad->getConnection()->close();
        }
            
        return new Response($strResponse);
    }
    
     /**
     * 
     * getRptCierreCajaXEmpleadoAction
     *
     * Actualizacion: Se agrega URL de logo de Ecuanet
     * @author Javier Hidalgo<jihidalgo@telconet.ec>
     * @version 1.5 09/06/2023
     * 
     * Actualizacion: Se parametriza url del servidor de imágenes.
     * @author Edgar Holguín<eholguin@telconet.ec>
     * @version 1.4 11/01/2018 
     * 
     * Genera data para formar el reporte de cierre de caja agrupado por cajero (empleado)
     * @author Edgar Holguin
     * @version 1.0 03/08/2016
     * 
     * @author Edgar Holguin
     * @version 1.1 18/08/2016 Para diferenciar se cambia nombre de reporte generado.
     *
     * Actualizacion: Se añade el prefio TNP de la empresa panama, para que muestre el logo
     * @author Germán Valenzuela Franco <gvalenzuela@telconet.ec>
     * @version 1.2 12/07/2017
     *  
     * @return $response
     */
    public function getRptCierreCajaXEmpleadoAction()
    {      
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        $intEmpresaId      = $objSession->get('idEmpresa');     
        $feDesde           = $_GET["fedesde"];
        $feHasta           = $_GET["feHasta"];
        $dateFechaDesde    = date("Y/m/d", strtotime($feDesde));
        $datefechaHasta    = date("Y/m/d", strtotime($feHasta));
      
       
        if($dateFechaDesde=="" || $datefechaHasta=="" )
        {
            $dateFechaDesde = "0000/00/00";
            $datefechaHasta = "0000/00/00";
        }
        $strFormaPago = $_GET["formaPago"] ;
        $intOficinaId = $_GET["oficina"] ;
       
        if (!$strFormaPago)
        {
            $serviceReportes        = $this->get('financiero.Reportes');
            $arrRespuestaFormasPago = $serviceReportes->obtenerFormasPagoParaReporteCierreCaja($strPrefijoEmpresa);
            $strFormaPago           = $arrRespuestaFormasPago['strFormasPago'];
        } 
        
        $limit            = $objRequest->get("limit");
        $start            = $objRequest->get("start");
        $em               = $this->get('doctrine')->getManager('telconet_financiero');
        
        
        $arrayParametros                    = array();
        $arrayParametros['intEmpresaId']    = $intEmpresaId;
        $arrayParametros['dateFechaDesde']  = $dateFechaDesde;
        $arrayParametros['datefechaHasta']  = $datefechaHasta; 
        $arrayParametros['strFormaPago']    = $strFormaPago; 
        $arrayParametros['intOficinaId']    = $intOficinaId; 
        $arrayParametros['limit']           = $limit; 
        $arrayParametros['start']           = $start; 
        
        
        $arrayUsers = $em->getRepository('schemaBundle:InfoPagoCab')->getUsersPagoPorFecha($arrayParametros);
        $arrayResultados['registros'] = array();
        $arrayResultados['total']     = 0;
        $arrayDatosAgrupados     = array();

        for($intCont=0;$intCont<count($arrayUsers);$intCont++)
        {
            $arrayParametros['usrCrecacion'] = $arrayUsers[$intCont]['usrCreacion']; 
           
            $arrayResultadoParcial[$intCont] = $em->getRepository('schemaBundle:InfoPagoCab')
                                                  ->getPagosPorEmpleado($arrayParametros);

            $arrayResultDatosAgrupados   = $em->getRepository('schemaBundle:InfoPagoCab')
                                              ->getTotalesXFormaPagoEmpleado($arrayParametros);

            $arrayDatosAgrupados = array_merge($arrayDatosAgrupados,$arrayResultDatosAgrupados);            

        } 
        
        $arrayResultados = $arrayResultadoParcial;

        $strDireccionOficina = "";
        $strPbxOficina       = "";
        $strNombreOficina    = "";
        
        if($intOficinaId!='')
        {
            $objInfoOficinaGrupo = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($intOficinaId);
            $strNombreOficina    = $objInfoOficinaGrupo->getNombreOficina();
            $strDireccionOficina = "Dirección: ".$objInfoOficinaGrupo-> getDireccionOficina();
            $strPbxOficina       = "Teléfono: ". $objInfoOficinaGrupo-> getTelefonoFijoOficina();
        }        
        
        $intCont            = 0;
        $arrayDetalles      = array();
        $arrayDetAgrupados  = array();  
        
        foreach ($arrayResultados as $arrayResultado)
        {
            $arrayDatos = $arrayResultado['registros'];
            $intTotal   = $arrayResultado['total'];
            $i          = 0;

            foreach ($arrayDatos as $arrayDato)
            {
                $intIdPago      = $arrayDato['id'];
                $strNumeroPago  = $arrayDato['numeroPago'];
                $usrCreacion    = $arrayDato['usrCreacion'];
                $infoPersona    = $em->getRepository('schemaBundle:InfoPersona')->findOneBy(array('login'=> $usrCreacion));
                $strNombreUser  = ""   ;
                if($usrCreacion!='Admin Account')  
                { 
                    $strNombreUser = $infoPersona->getNombres(). " ". $infoPersona->getApellidos();
                }
                else
                {
                    $strNombreUser = "migracion"   ;
                }
                $dateFechaCreacion  = $arrayDato['feCreacion'];
                $intIdPtoCiente     = $arrayDato['puntoId']; 

                if($intIdPtoCiente!='')
                {
                    $objInfoPunto              = $em->getRepository('schemaBundle:InfoPunto')->find($intIdPtoCiente);
                    if($objInfoPunto)
                    {
                        $objInfoPersonaEmpresaRol  = $objInfoPunto->getPersonaEmpresaRolId();
                        $strLogin                  = $objInfoPunto->getLogin();                           
                        $strCliente                = "";
                        $objInfoPersonaCliente     = $em->getRepository('schemaBundle:InfoPersona')->find( $objInfoPersonaEmpresaRol->getPersonaId());
                        $strRazonSocial            = $objInfoPersonaCliente->getRazonSocial();                    
                        if($strRazonSocial=="")
                        {
                            $strCliente = $objInfoPersonaCliente->getNombres(). " ". $objInfoPersonaCliente->getApellidos();
                        }
                        else
                        {
                            $strCliente = $strRazonSocial;
                        }                                                
                    }
                }
                else
                {
                    $strCliente="";
                }

                $strNumReferencia = $arrayDato['numeroReferencia'];

                if($strNumReferencia == '')
                { 
                    $strNumReferencia = $arrayDato['bancoCtaContableId'];
                }

                $AdmiFormaPago  = $em->getRepository('schemaBundle:AdmiFormaPago')->find($arrayDato['formaPagoId']);
                $strFormaPago   = $AdmiFormaPago->getDescripcionFormaPago();
                $floatValorPago = $arrayDato['valorPago'];

                $arrayDetalles[$intCont][$i]['id']            = $intIdPago;
                $arrayDetalles[$intCont][$i]['numeroPago']    = $strNumeroPago;  
                $arrayDetalles[$intCont][$i]['empl']          = $strNombreUser; 
                $arrayDetalles[$intCont][$i]['fechaCreacion'] = strval(date_format($dateFechaCreacion, "d/m/Y G:i")); 
                $arrayDetalles[$intCont][$i]['ptoCiente']     = $intIdPtoCiente; 
                $arrayDetalles[$intCont][$i]['login']         = $strLogin;
                $arrayDetalles[$intCont][$i]['cliente']       = $strCliente; 
                $arrayDetalles[$intCont][$i]['numReferencia'] = $strNumReferencia; 
                $arrayDetalles[$intCont][$i]['formaPago']     = $strFormaPago; 
                $arrayDetalles[$intCont][$i]['formaPagoId']   = $arrayDato['formaPagoId'];
                $arrayDetalles[$intCont][$i]['valor']         = $floatValorPago; 
                $arrayDetalles[$intCont][$i]['usrCreacion']   = $usrCreacion;
                $i++;
            }
            $intCont++;
        }
           
            $floatTotalCierre=0;
            $j=0;
            foreach ($arrayDatosAgrupados as $arrayDatoAgrupado)
            {
                $arrayDetAgrupados[$j]['idFormaPago'] = $arrayDatoAgrupado['formaPagoId'];
                $arrayDetAgrupados[$j]['cantidad']    = $arrayDatoAgrupado['cantFpagos'];

                if($arrayDatoAgrupado['formaPagoId']!='')
                {
                    $objAdmiFormaPago   = $em->getRepository('schemaBundle:AdmiFormaPago')->find($arrayDatoAgrupado['formaPagoId']);
                    $strDescFormaPago   = $objAdmiFormaPago->getDescripcionFormaPago();
                }
                else
                {
                    $strDescFormaPago = "";
                }
                $arrayDetAgrupados[$j]['descripcionFpago'] = $strDescFormaPago;    
                $arrayDetAgrupados[$j]['sumaPago']         = $arrayDatoAgrupado['sumaPago'];
                $arrayDetAgrupados[$j]['usrCreacion']      = $arrayDatoAgrupado['usrCreacion'];
                $floatTotalCierre+=$arrayDatoAgrupado['sumaPago'];
                $j=$j+1; 
            }
            
        $dateFechaConsulta  = date("Y/m/d H:i:s");
        
        //segun el prefijo de la empresa se indica url de logo respectivo
        $strUrlImagenes = $this->container->getParameter('imageServer');
        
        if ($strPrefijoEmpresa == 'MD')
        {
             $strLogoEmpresa = $strUrlImagenes."/others/telcos/logo_netlife_big.jpg";
        }
        elseif($strPrefijoEmpresa == 'TTCO')
        {
            $strLogoEmpresa = $strUrlImagenes."/others/telcos/logo_transtelco_new.jpg";
        }
        elseif($strPrefijoEmpresa == 'TN' || $strPrefijoEmpresa == 'TNP')
        {
            $strLogoEmpresa = $strUrlImagenes."/logo_telconet.png";
        }
        elseif($strPrefijoEmpresa == 'EN')
        {
            $strLogoEmpresa = $strUrlImagenes."/others/telcos/logo_ecuanet.png";
        }
        else
        {
            //CONDICIÓN SIN PROGRAMACIÓN
        }
           
        $html = $this->renderView('financieroBundle:reportes:rptCierreCajaXCajero.html.twig',
                                   array(
                                            'detalles'        => $arrayDetalles,
                                            'detAgrupados'    => $arrayDetAgrupados,
                                            'totalRegistros'  => $intTotal,
                                            'fechaDesde'      => $dateFechaDesde,
                                            'fechaHasta'      => $datefechaHasta,
                                            'sumaTotal'       => $floatTotalCierre,
                                            'descripcionPago' => $strFormaPago,
                                            'fechaConsulta'   => $dateFechaConsulta,
                                            'nombreoficina'   => $strNombreOficina,
                                            'oficina'         => $intOficinaId,
                                            'logoEmpresa'     => $strLogoEmpresa,
                                            'direccionOficina'=> $strDireccionOficina,
                                            'pbxOficina'      => $strPbxOficina,
                                            'numRegistros'    => $intCont,
           
                                        ) 
                                 );
      
        return new Response($this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                            200,
                            array('Content-Type'          => 'application/pdf',
                                  'Content-Disposition'   => 'attachment; filename=cierre-caja-emp'.trim(date("Y-m-d")).'.pdf',
                                )
                            );
    }    
    
     /**
     * 
     * getRptCierreCajaXPapeletaAction
     * Genera data para formar el reporte de cierre de caja agrupado por número de papeleta
     * @author Edgar Holguin
     * @version 1.0 16/08/2016
     * 
     * Actualizacion: Se añade el prefio TNP de la empresa panama, para que muestre el logo
     * @author Germán Valenzuela Franco <gvalenzuela@telconet.ec>
     * @version 1.1 12/07/2017
     * 
     * Actualizacion: Se agrega URL de logo de Ecuanet
     * @author Javier Hidalgo<jihidalgo@telconet.ec>
     * @version 1.2 09/06/2023
     * 
     * @return $response
     */
    public function getRptCierreCajaXPapeletaAction()
    {      
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        $intEmpresaId      = $objSession->get('idEmpresa');     
        $feDesde           = $_GET["fedesde"];
        $feHasta           = $_GET["feHasta"];
        $dateFechaDesde    = date("Y/m/d", strtotime($feDesde));
        $datefechaHasta    = date("Y/m/d", strtotime($feHasta));
      
       
        if($dateFechaDesde=="" || $datefechaHasta=="" )
        {
            $dateFechaDesde = "0000/00/00";
            $datefechaHasta = "0000/00/00";
        }
        $strFormaPago = $_GET["formaPago"] ;
        $intOficinaId = $_GET["oficina"] ;
       
        if (!$strFormaPago)
        {
            $serviceReportes        = $this->get('financiero.Reportes');
            $arrRespuestaFormasPago = $serviceReportes->obtenerFormasPagoParaReporteCierreCaja($strPrefijoEmpresa);
            $strFormaPago           = $arrRespuestaFormasPago['strFormasPago'];
        } 
        
        $limit            = $objRequest->get("limit");
        $start            = $objRequest->get("start");
        $em               = $this->get('doctrine')->getManager('telconet_financiero');
        
        
        $arrayParametros                    = array();
        $arrayParametros['intEmpresaId']    = $intEmpresaId;
        $arrayParametros['dateFechaDesde']  = $dateFechaDesde;
        $arrayParametros['datefechaHasta']  = $datefechaHasta; 
        $arrayParametros['strFormaPago']    = $strFormaPago; 
        $arrayParametros['intOficinaId']    = $intOficinaId; 
        $arrayParametros['limit']           = $limit; 
        $arrayParametros['start']           = $start; 
        
        
        
        $arrayReferencias = $em->getRepository('schemaBundle:InfoPagoCab')->getNumReferenciasPagoPorFecha($arrayParametros);
        
        $arrayResultados['registros'] = array();
        $arrayResultados['total']     = 0;
        $arrayPagosPorReferencia      = array();
        $arrayPagosAgrupados          = array();


        foreach($arrayReferencias as $arrayReferencia):
            $arrayParametros['numReferencia'] = $arrayReferencia['numeroReferencia'];

            if($arrayParametros['numReferencia']!= null)
            {

                $arrayPagosPorReferencia[0]  = $em->getRepository('schemaBundle:InfoPagoCab')
                                                  ->getPagosPorReferencia($arrayParametros);

                $arrayTotalesPorReferencia   = $em->getRepository('schemaBundle:InfoPagoCab')
                                                  ->getTotalesXFormaPagoReferencia($arrayParametros);

                $arrayPagosAgrupados         = array_merge($arrayPagosAgrupados,$arrayTotalesPorReferencia); 

            }
        endforeach;

        $arrayResultados = $arrayPagosPorReferencia;
        

        $strDireccionOficina = "";
        $strPbxOficina       = "";
        $strNombreOficina    = "";
        
        if($intOficinaId!='')
        {
            $objInfoOficinaGrupo = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($intOficinaId);
            $strNombreOficina    = $objInfoOficinaGrupo->getNombreOficina();
            $strDireccionOficina = "Dirección: ".$objInfoOficinaGrupo-> getDireccionOficina();
            $strPbxOficina       = "Teléfono: ". $objInfoOficinaGrupo-> getTelefonoFijoOficina();
        }        
        
        $intCont               = 0;
        $arrayDetallesPago     = array();
        $arrayDetPagoAgrupado  = array();  
        
        foreach ($arrayResultados as $arrayResultado)
        {
            $arrayDatos = $arrayResultado['registros'];
            $intTotal   = $arrayResultado['total'];
            $i          = 0;
            
            foreach ($arrayDatos as $arrayDato)
            {
                $intIdPago      = $arrayDato['id'];
                $strNumeroPago  = $arrayDato['numeroPago'];
                $usrCreacion    = $arrayDato['usrCreacion'];
                $infoPersona    = $em->getRepository('schemaBundle:InfoPersona')->findOneBy(array('login'=> $usrCreacion));
                $strNombreUser  = ""   ;
                $strCliente     = "";
                
                if($usrCreacion!='Admin Account')  
                { 
                    $strNombreUser = $infoPersona->getNombres(). " ". $infoPersona->getApellidos();
                }
                else
                {
                    $strNombreUser = "migracion"   ;
                }
                $dateFechaCreacion  = $arrayDato['feCreacion'];
                $intIdPtoCiente     = $arrayDato['puntoId']; 

                if($intIdPtoCiente!='')
                {
                    $objInfoPunto              = $em->getRepository('schemaBundle:InfoPunto')->find($intIdPtoCiente);
                    if($objInfoPunto)
                    {
                        $objInfoPersonaEmpresaRol  = $objInfoPunto->getPersonaEmpresaRolId();
                        $strLogin                  = $objInfoPunto->getLogin();                           
                        $objInfoPersonaCliente     = $em->getRepository('schemaBundle:InfoPersona')->find( $objInfoPersonaEmpresaRol->getPersonaId());
                        $strRazonSocial            = $objInfoPersonaCliente->getRazonSocial();                    
                        if($strRazonSocial=="")
                        {
                            $strCliente = $objInfoPersonaCliente->getNombres(). " ". $objInfoPersonaCliente->getApellidos();
                        }
                        else
                        {
                            $strCliente = $strRazonSocial;
                        }                                                
                    }
                }


                if(isset($arrayDato['numeroReferencia']) && ($arrayDato['numeroReferencia'] != ''))
                {
                   $strNumReferencia = $arrayDato['numeroReferencia'];
                }
                else
                {
                   $strNumReferencia = $arrayDato['bancoCtaContableId'];
                }                

                $AdmiFormaPago  = $em->getRepository('schemaBundle:AdmiFormaPago')->find($arrayDato['formaPagoId']);
                $strFormaPago   = $AdmiFormaPago->getDescripcionFormaPago();
                $floatValorPago = (is_null($arrayDato['valorPago'])) ? 0 : $arrayDato['valorPago'];

                $arrayDetallesPago[$intCont][$i]['intIdPago']         = $intIdPago;
                $arrayDetallesPago[$intCont][$i]['strNumeroPago']     = $strNumeroPago;  
                $arrayDetallesPago[$intCont][$i]['strNombreUser']     = $strNombreUser; 
                $arrayDetallesPago[$intCont][$i]['fechaCreacion']     = strval(date_format($dateFechaCreacion, "d/m/Y G:i")); 
                $arrayDetallesPago[$intCont][$i]['intIdPtoCiente']    = $intIdPtoCiente; 
                $arrayDetallesPago[$intCont][$i]['strLogin']          = $strLogin;
                $arrayDetallesPago[$intCont][$i]['strCliente']       = $strCliente; 
                $arrayDetallesPago[$intCont][$i]['strNumReferencia']  = $strNumReferencia; 
                $arrayDetallesPago[$intCont][$i]['strFormaPago']      = $strFormaPago; 
                $arrayDetallesPago[$intCont][$i]['intFormaPagoId']    = $arrayDato['formaPagoId'];
                $arrayDetallesPago[$intCont][$i]['floatValor']        = $floatValorPago; 
                $arrayDetallesPago[$intCont][$i]['strUsrCreacion']    = $usrCreacion;
                $i++;
            }
            $intCont++;
        }
           
            $floatTotalCierre=0;
            $j=0;
            foreach ($arrayPagosAgrupados as $arrayDatosPago)
            {
                $arrayDetPagoAgrupado[$j]['intIdFormaPago']   = $arrayDatosPago['formaPagoId'];
                $arrayDetPagoAgrupado[$j]['floatCantidad']    = $arrayDatosPago['cantFpagos'];

                if($arrayDatosPago['formaPagoId']!='')
                {
                    $objAdmiFormaPago   = $em->getRepository('schemaBundle:AdmiFormaPago')->find($arrayDatosPago['formaPagoId']);
                    $strDescFormaPago   = $objAdmiFormaPago->getDescripcionFormaPago();
                }
                else
                {
                    $strDescFormaPago = "";
                }
                $arrayDetPagoAgrupado[$j]['strDescripcionFpago'] = $strDescFormaPago;    
                $arrayDetPagoAgrupado[$j]['floatSumaPago']       = $arrayDatosPago['sumaPago'];
                $arrayDetPagoAgrupado[$j]['strUsrCreacion']      = $arrayDatosPago['usrCreacion'];
                $arrayDetPagoAgrupado[$j]['strNumReferencia']    = $arrayDatosPago['numeroReferencia'];
                $floatTotalCierre+=$arrayDatosPago['sumaPago'];
                $j=$j+1; 
            }
            
        $dateFechaConsulta  = date("Y/m/d H:i:s");
        
        //segun el prefijo de la empresa se indica url de logo respectivo
        $strUrlImagenes = $this->container->getParameter('imageServer');
        
        if ($strPrefijoEmpresa == 'MD')
        {
            $strLogoEmpresa = $strUrlImagenes."/others/telcos/logo_netlife_big.jpg";
        }
        elseif($strPrefijoEmpresa == 'TTCO')
        {
            $strLogoEmpresa = $strUrlImagenes."/others/telcos/logo_transtelco_new.jpg";
        }
        elseif($strPrefijoEmpresa == 'TN' || $strPrefijoEmpresa == 'TNP')
        {
            $strLogoEmpresa = $strUrlImagenes."/logo_telconet.png";
        }
        elseif($strPrefijoEmpresa == 'EN')
        {
            $strLogoEmpresa = $strUrlImagenes."/others/telcos/logo_ecuanet.png";
        }
        else
        {
            //CONDICIÓN SIN PROGRAMACIÓN
        }
       
        $html = $this->renderView('financieroBundle:reportes:rptCierreCajaXPapeleta.html.twig',
                                   array(
                                            'detalles'        => $arrayDetallesPago,
                                            'detAgrupados'    => $arrayDetPagoAgrupado,
                                            'totalRegistros'  => $intTotal,
                                            'fechaDesde'      => $dateFechaDesde,
                                            'fechaHasta'      => $datefechaHasta,
                                            'sumaTotal'       => $floatTotalCierre,
                                            'descripcionPago' => $strFormaPago,
                                            'fechaConsulta'   => $dateFechaConsulta,
                                            'nombreoficina'   => $strNombreOficina,
                                            'oficina'         => $intOficinaId,
                                            'logoEmpresa'     => $strLogoEmpresa,
                                            'direccionOficina'=> $strDireccionOficina,
                                            'pbxOficina'      => $strPbxOficina,
                                            'numRegistros'    => $intCont,
           
                                        ) 
                                 );
        $options = [
                    'margin-bottom' => 20,
                    ]; 
        return new Response($this->get('knp_snappy.pdf')->getOutputFromHtml($html,$options),
                            200,
                            array('Content-Type'          => 'application/pdf',
                                  'Content-Disposition'   => 'attachment; filename=cierre-caja-papeleta'.trim(date("Y-m-d")).'.pdf',
                                 )
                            );
    }
    
     /**
     * getTipoDocumentosPerfilAction
     * Metodo que permite listar los tipo de documentos financieros existentes según perfil del usuario
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 10-09-2016
     * @return $response
     */ 
    public function getTipoDocumentosPerfilAction()
    {
        $intTotal    = 0;
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $arrayTipoDocumento[]=array('codigo_tipo_documento'=>'0', 'nombre_tipo_documento'=>'-- Seleccione --');
        
        //Verfica si tiene el perfil "GENERAR REPORTE DOCUMENTOS FINANCIEROS".
        if (true === $this->get('security.context')->isGranted('ROLE_14-4659'))
        {
          $arrayCodigosPermitidos = array('FACP','FAC','NC','ND','NCI','NDI','PAG','ANT','ANTS','PAGC','ANTC','DEV');
        }//Verfica si tiene el perfil "GENERAR REPORTE FACTURACION".          
        else if (true === $this->get('security.context')->isGranted('ROLE_14-4657'))
        {
          $arrayCodigosPermitidos = array('FACP','FAC','NC','ND','NCI','NDI');
        }//Verfica si tiene el perfil "GENERAR REPORTE COBRANZAS".
        else if (true === $this->get('security.context')->isGranted('ROLE_14-4658'))
        {
          $arrayCodigosPermitidos = array('PAG','ANT','ANTS','PAGC','ANTC','DEV');
        }
       
		
        $arrayTipoDocumentos = $this->getDoctrine()->getManager("telconet_financiero")
                                                       ->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                       ->findBy(array('codigoTipoDocumento'=>$arrayCodigosPermitidos, 'estado'=>'Activo'));        

        if($arrayTipoDocumentos)
        {
            foreach ($arrayTipoDocumentos as $objTipoDocumento):
                $arrayTipoDocumento[]=array('codigo_tipo_documento'=>$objTipoDocumento->getCodigoTipoDocumento(), 
                                            'nombre_tipo_documento'=>$objTipoDocumento->getNombreTipoDocumento() 
                                           );
                $intTotal++;                        
            endforeach;
        }
        
        $objJson     = json_encode(array('total' => $intTotal, 'encontrados' => (array) $arrayTipoDocumento));
        
        $objResponse->setContent($objJson);   
			
        return $objResponse;
    }
    
    
    /**
     * @Secure(roles="ROLE_366-1")
     * 
     * Documentación para la función 'reportesContabilidadAction'
     * 
     * Método que permite presentar la búsqueda de los reportes para realizar la cuadratura de la contabilidad
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 18-01-2017
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 09-02-2017 - Se modifica la función para añadir la glosa informativa que ayudará al usuario mediante instrucciones como debe
     *                           realizar la cuadratura de la información del TELCOS con el NAF.
     */   
    public function reportesContabilidadAction()
    {
        $emGeneral             = $this->getDoctrine()->getManager("telconet_general");
        $arrayGlosaInformativa = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('GLOSA_REPORTE_CONTABILIDAD',
                                                                                                    'FINANCIERO',
                                                                                                    'REPORTES');
        $strGlosaInformativa   = ( isset($arrayGlosaInformativa['valor1']) && !empty($arrayGlosaInformativa['valor1']) ) ? 
                                 $arrayGlosaInformativa['valor1'] : '';
            
            
        return $this->render('financieroBundle:reportes:reportesContabilidad.html.twig', array( 'strGlosaInformativa' => $strGlosaInformativa));        
    }
    
    
    /**
     * Documentación para la función 'getTiposReportesContabilidadAction'
     * 
     * Método que retorna los tipos de reportes contables a los cuales tiene acceso el usuario en sessión
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 18-01-2017
     */   
    public function getTiposReportesContabilidadAction()
    {
        $arrayRolesPermitidos = array();
        $objJsonResponse      = new JsonResponse();
        
        if (true === $this->get('security.context')->isGranted('ROLE_367-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_367-1'; //Reporte de pagos
        }
        
        if (true === $this->get('security.context')->isGranted('ROLE_368-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_368-1'; //Reporte de débitos
        }
        
        if (true === $this->get('security.context')->isGranted('ROLE_369-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_369-1'; //Reporte de depósitos
        }
        
        if (true === $this->get('security.context')->isGranted('ROLE_369-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_370-1'; //Reporte de pagos masivos
        }
        
        if (true === $this->get('security.context')->isGranted('ROLE_369-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_371-1'; //Reporte de anticipos por cruce
        }
        
        $emGeneral                  = $this->getDoctrine()->getManager("telconet_general");
        $arrayParametros            = array('valor1'                => $arrayRolesPermitidos, 
                                            'strNombreParametroCab' => 'REPORTES_CONTABILIDAD', 
                                            'estado'                => 'Activo');
        $arrayTipoReportesContables = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getArrayDetalleParametros($arrayParametros);
        
        $objJsonResponse->setData($arrayTipoReportesContables);
        
        return $objJsonResponse;
    }
    
    
    /**
     * Documentación para la función 'generarReporteContabilidadAction'
     * 
     * Método que permite generar y enviar vía correo el reporte para realizar la respectiva cuadratura de la contabilidad de la empresa del usuario
     * en sessión.
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 19-01-2017
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 31-03-2017 - Se añaden los reportes historicos de documentos financieros 'P_PAG_HISTO' (reporte de pagos) y 'P_ANT_HISTO'
     *                           (reporte de anticipos).
     */ 
    public function generarReporteContabilidadAction()
    {
        $objResponse            = new Response();
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $emFinanciero           = $this->getDoctrine()->getManager('telconet_financiero');
        $serviceUtil            = $this->get('schema.Util');
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strIpCreacion          = $objRequest->getClientIp();
        $strUsuario             = $objSession->get('user');
        $intIdPersonaEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $strMailUsrSession      = "";
        $strMensaje             = 'Reporte generado y enviado exitosamente.';
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : '';
        $strEmpresaCod          = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : '';
        
        $emFinanciero->getConnection()->beginTransaction();
        
        try
        {
            $strTipoReporteContabilidad                      = $objRequest->query->get('strTipoReporteContabilidad') 
                                                               ? $objRequest->query->get('strTipoReporteContabilidad') : '';
            $arrayTmpFechasContabilizacion                   = array();
            $arrayTmpFechasContabilizacion['strFechaInicio'] = $objRequest->query->get('strFechaContabilizacionDesde') 
                                                               ? $objRequest->query->get('strFechaContabilizacionDesde') : '';
            $arrayTmpFechasContabilizacion['strFechaFin']    = $objRequest->query->get('strFechaContabilizacionHasta')
                                                               ? $objRequest->query->get('strFechaContabilizacionHasta') : '';
            $arrayTmpFechasContabilizacion['strDateFormat']  = 'd/m/y';
            $arrayTmpFechasContabilizacion['strTimeInicio']  = '';
            $arrayTmpFechasContabilizacion['strTimeFin']     = '';
            
            if( isset($arrayTmpFechasContabilizacion['strFechaInicio']) && !empty($arrayTmpFechasContabilizacion['strFechaInicio']) 
                && isset($arrayTmpFechasContabilizacion['strFechaFin']) && !empty($arrayTmpFechasContabilizacion['strFechaFin']) )
            {
                $arrayFechaInicioFin          = $this->validadorFechasInicioFin($arrayTmpFechasContabilizacion);
                $strFechaContabilizacionDesde = $arrayFechaInicioFin['strFechaInicio'];
                $strFechaContabilizacionHasta = $arrayFechaInicioFin['strFechaFin'];
            }
            else
            {
                $strFechaContabilizacionDesde = '';
                $strFechaContabilizacionHasta = '';
            }
            
            
            //Bloque que obtiene el mail de la persona en sessión
            $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonaEmpresaRol);

            if( is_object($objInfoPersonaEmpresaRol) )
            {
                $strMailUsrSession = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                 ->getValorFormaContactoPorCodigo($objInfoPersonaEmpresaRol->getPersonaId(), 'MAIL');
                
                if( !empty($strMailUsrSession) )
                {
                    $strMailUsrSession = strtolower($strMailUsrSession);
                }                
            } 
            
            if( !empty($strMailUsrSession) )
            {
                if( $strTipoReporteContabilidad == 'P_PAG'  || $strTipoReporteContabilidad == 'P_PAG_RET' || $strTipoReporteContabilidad == 'P_DEB'
                    || $strTipoReporteContabilidad == 'P_DEP' || $strTipoReporteContabilidad == 'P_ANT_CRUCE' 
                    || $strTipoReporteContabilidad == 'P_PAG_HISTO' || $strTipoReporteContabilidad == 'P_ANT_HISTO' )
                {
                    $arrayParametros = array( 'prefijoEmpresa'                     => $strPrefijoEmpresa,
                                              'fin_tipoDocumento'                  => $strTipoReporteContabilidad,
                                              'strFinPagFechaContabilizacionDesde' => $strFechaContabilizacionDesde,
                                              'strFinPagFechaContabilizacionHasta' => $strFechaContabilizacionHasta,
                                              'emailUsrSesion'                     => strtolower($strMailUsrSession),
                                              'usrSesion'                          => $strUsuario,
                                              'intEmpresaId'                       => $strEmpresaCod);

                    $emFinanciero->getRepository('schemaBundle:InfoPagoCab')->ejecutarEnvioReporteCobranzas($arrayParametros);
                }  

                // Registro de historial de generación de reporte
                $objInfoReporteHistorial = new InfoReporteHistorial();
                $objInfoReporteHistorial->setEmpresaCod($strPrefijoEmpresa);
                $objInfoReporteHistorial->setCodigoTipoReporte(trim($strTipoReporteContabilidad));
                $objInfoReporteHistorial->setFeCreacion(new \DateTime('now'));
                $objInfoReporteHistorial->setUsrCreacion($strUsuario);
                $objInfoReporteHistorial->setEmailUsrCreacion($strMailUsrSession);
                $objInfoReporteHistorial->setEstado('Activo');
                $objInfoReporteHistorial->setAplicacion('Telcos+'); 
                $emFinanciero->persist($objInfoReporteHistorial);
                $emFinanciero->flush();            
                $emFinanciero->getConnection()->commit();
            }//( !empty($strMailUsrSession) )
            else
            {
                throw new \Exception('No se encontró mail registrado de su usuario en sessión para generar y enviar el reporte solicitado. Favor'.
                                     ' comunicarse con el departamento de sistemas para su respectiva gestión.');
            }
        }
        catch(\Exception $e) 
        {
            $strMensaje = 'Error al generar y enviar el reporte solicitado.';
            
            $serviceUtil->insertError( 'Telcos+', 
                                       'Reportes para Contabilidad', 
                                       'Error al generar y enviar el reporte solicitado. '.$e->getMessage(), 
                                       $strUsuario, 
                                       $strIpCreacion );
            
            if ($emFinanciero->getConnection()->isTransactionActive())
            {
                $emFinanciero->getConnection()->rollback();
            }
            
            $emFinanciero->getConnection()->close();
        }
        
        $objResponse->setContent($strMensaje);
        
        return $objResponse;  	
    }
    
    
    /**
     * @Secure(roles="ROLE_379-1")
     * 
     * Documentación para la función 'reportesHistoricoDocumentosFinancierosAction'
     * 
     * Método que permite presentar la búsqueda de los reportes históricos de los documentos financieros usados para la cuadratura de la cartera
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 31-03-2017
     */   
    public function reportesHistoricoDocumentosFinancierosAction()
    {
        $emGeneral             = $this->getDoctrine()->getManager("telconet_general");
        $arrayGlosaInformativa = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('GLOSA_REPORTE_HISTORICO_DOCUMENTO_FINANCIERO',
                                                                                                    'FINANCIERO',
                                                                                                    'REPORTES');
        $strGlosaInformativa   = ( isset($arrayGlosaInformativa['valor1']) && !empty($arrayGlosaInformativa['valor1']) ) ? 
                                 $arrayGlosaInformativa['valor1'] : '';
            
            
        return $this->render( 'financieroBundle:reportes:reportesHistoricoDocumentosFinancieros.html.twig', 
                              array( 'strGlosaInformativa' => $strGlosaInformativa) );        
    }
    
    
    /**
     * Documentación para la función 'getTiposReportesHistoricoDocumentosFinancierosAction'
     * 
     * Método que retorna los tipos de reportes historicos de documentos financieros a los cuales tiene acceso el usuario en sessión
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 31-03-2017
     */   
    public function getTiposReportesHistoricoDocumentosFinancierosAction()
    {
        $arrayRolesPermitidos = array();
        $objJsonResponse      = new JsonResponse();
        
        if (true === $this->get('security.context')->isGranted('ROLE_380-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_380-1'; //Reporte de pagos
        }
        
        if (true === $this->get('security.context')->isGranted('ROLE_381-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_381-1'; //Reporte de anticipos
        }
        
        $emGeneral                  = $this->getDoctrine()->getManager("telconet_general");
        $arrayParametros            = array('valor1'                => $arrayRolesPermitidos, 
                                            'strNombreParametroCab' => 'REPORTES_CONTABILIDAD', 
                                            'estado'                => 'Activo');
        $arrayTipoReportesContables = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getArrayDetalleParametros($arrayParametros);
        
        $objJsonResponse->setData($arrayTipoReportesContables);
        
        return $objJsonResponse;
    }
    
    /**
     * Documentación para la función 'mostrarReporteTributarioAction'
     * 
     * Método que muestra la pantalla de generación reporte tributario para banco Guayaquil.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.0 
     * @since 02-04-2020
     */
    public function mostrarReporteTributarioAction()
    {
        return $this->render('financieroBundle:reportes:reporteTributario.html.twig');
    }
    
    /**
     * Documentación para la función 'generarReporteTriburarioAction'
     * 
     * Método que permite generar y enviar vía correo el reporte por débito de Banco Guayaquil con 
     * cuentas de ahorro y corriente mediante las fechas seleccionadas.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.0 
     * @since 02-04-2020
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * version 1.1 27-10-2021 - Se elimina código donde realizaba llamado a la función validadorFechasInicioFin por motivo que
     *                          se devolvía fecha de año incorrecto.
     * 
     */
    public function generarReporteTriburarioAction()
    {
        $objResponse            = new Response();
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $emFinanciero           = $this->getDoctrine()->getManager('telconet_financiero');
        $serviceUtil            = $this->get('schema.Util');
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strIpCreacion          = $objRequest->getClientIp();
        $strUsuarioSesion       = $objSession->get('user');
        $intIdPersonaEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $strMailUsrSession      = "";
        $strMensaje             = 'Reporte Tributario generado y enviado exitosamente.';
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : '';
        $strEmpresaCod          = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : '';
        $strClaveDesencripta    = $this->container->getParameter('secret');
        
        $emFinanciero->getConnection()->beginTransaction();
        
        try
        {
            $arrayTmpFechasReporte                   = array();
            $arrayTmpFechasReporte['strFechaInicio'] = $objRequest->query->get('strFechaReporteDesde') 
                                                               ? $objRequest->query->get('strFechaReporteDesde') : '';
            $arrayTmpFechasReporte['strFechaFin']    = $objRequest->query->get('strFechaReporteHasta')
                                                               ? $objRequest->query->get('strFechaReporteHasta') : '';

            $strFechaReporteDesde = $arrayTmpFechasReporte['strFechaInicio'];
            $strFechaReporteHasta = $arrayTmpFechasReporte['strFechaFin'];
            
            $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonaEmpresaRol);

            if( is_object($objInfoPersonaEmpresaRol) )
            {
                $strMailUsrSession = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                 ->getValorFormaContactoPorCodigo($objInfoPersonaEmpresaRol->getPersonaId(), 'MAIL');
                
                if( !empty($strMailUsrSession) )
                {
                    $strMailUsrSession = strtolower($strMailUsrSession);
                }                
            } 

            if( !empty($strMailUsrSession) )
            {
                $arrayParametros = array( 'strPrefijoEmpresa'       => $strPrefijoEmpresa,
                                          'strFinFechaReporteDesde' => $strFechaReporteDesde,
                                          'strFinFechaReporteHasta' => $strFechaReporteHasta,
                                          'strMailUsrSession'       => strtolower($strMailUsrSession),
                                          'strUsuarioSesion'        => $strUsuarioSesion,
                                          'intEmpresaId'            => $strEmpresaCod,
                                          'strClaveDesencripta'     => $strClaveDesencripta);

                $emFinanciero->getRepository('schemaBundle:InfoPagoCab')->ejecutarEnvioReporteTributario($arrayParametros);

                // Registro de historial de generación de reporte
                $objInfoReporteHistorial = new InfoReporteHistorial();
                $objInfoReporteHistorial->setEmpresaCod($strPrefijoEmpresa);
                $objInfoReporteHistorial->setCodigoTipoReporte(trim('PAG'));
                $objInfoReporteHistorial->setFeCreacion(new \DateTime('now'));
                $objInfoReporteHistorial->setUsrCreacion($strUsuarioSesion);
                $objInfoReporteHistorial->setEmailUsrCreacion($strMailUsrSession);
                $objInfoReporteHistorial->setEstado('Activo');
                $objInfoReporteHistorial->setAplicacion('Telcos+'); 
                $emFinanciero->persist($objInfoReporteHistorial);
                $emFinanciero->flush();            
                $emFinanciero->getConnection()->commit();
            } 
            else
            {
                throw new \Exception('No se encontró mail registrado de su usuario en sessión para generar y enviar el reporte solicitado. Favor'.
                                     ' comunicarse con el departamento de sistemas para su respectiva gestión.');
            }
            
            $objResponse->setContent($strMensaje);
            return $objResponse;  
            
        }
        catch(\Exception $e) 
        {
            $strMensaje = 'Error al generar y enviar el reporte solicitado.';
            
            $serviceUtil->insertError( 'Telcos+', 
                                       'Reporte Tributario Banco GYE', 
                                       'Error al generar y enviar el reporte solicitado. '.$e->getMessage(), 
                                       $strUsuarioSesion, 
                                       $strIpCreacion );
            
            if ($emFinanciero->getConnection()->isTransactionActive())
            {
                $emFinanciero->getConnection()->rollback();
            }
            
            $emFinanciero->getConnection()->close();
        }
    } 
        
}
