<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\schemaBundle\Entity\AdmiNumeracion;
use Symfony\Component\HttpFoundation\JsonResponse;
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

class FacturacionMensualController extends Controller
{

    /**
     * Permite la visualizacion de los documentos pendientes de aprobación
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 21-12-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 01-09-2016 - Se agrega el rol para que se puedan visualizar el filtro por oficina al consultar las facturas.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.2 19-09-2016
     * Se envían los roles: ROLE_185-4737 - Aprobar  Facturación Mensual Automática.
     *                      ROLE_185-4738 - Rechazar Facturación Mensual Automática.
     *                      ROLE_185-4757 - Exportar Facturación Mensual Automática.
     */
    public function listarFacturasAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $objCliente         = $objSession->get('cliente');
        $emInfraestructura  = $this->get('doctrine')->getManager('telconet_infraestructura');
        $intEmpresaId       = $objRequest->getSession()->get('idEmpresa');
        $intIdOficina       = $objSession->get('idOficina') ? $objSession->get('idOficina') : 0;
        
        $arrayParametro = array();

        if($objCliente)
            $arrayParametro['cliente']= "S";
        else
            $arrayParametro['cliente']= "N";
        
        //Verifica si existe una proceso masivo ejecutandose en estado Activo
        $arrayResultado        = $emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoCab')
                                ->findExisteProcesoMasivoCab($intEmpresaId);
        
        if(!empty($arrayResultado['registro']))
        {
            $arrayParametro['mensaje'] = "Existe un proceso de numeracion ejecutandose, favor esperar";
        }
        else
        {
            $arrayParametro['mensaje'] = "";
        }
        
        //Se agrega control de roles permitidos
        $rolesPermitidos = array();
        
        if(true === $this->get('security.context')->isGranted('ROLE_185-165'))
        {
            $rolesPermitidos[] = 'ROLE_185-165'; //COMBO OFICINAS PARA FILTRAR LAS FACTURAS
        }
        
        if(true === $this->get('security.context')->isGranted('ROLE_185-4737'))
        {
            $rolesPermitidos[] = 'ROLE_185-4737'; // FACTURACION MENSUAL AUTOMATICA APROBAR
        }
        
        if(true === $this->get('security.context')->isGranted('ROLE_185-4738'))
        {
            $rolesPermitidos[] = 'ROLE_185-4738'; // FACTURACION MENSUAL AUTOMATICA RECHAZAR
        }
        
        if(true === $this->get('security.context')->isGranted('ROLE_185-4757'))
        {
            $rolesPermitidos[] = 'ROLE_185-4757'; // FACTURACION MENSUAL AUTOMATICA EXPORTAR
        }
        
        $arrayParametro['rolesPermitidos']     = $rolesPermitidos;
        $arrayParametro['intIdOficinaSession'] = $intIdOficina;

        return $this->render('financieroBundle:FacturacionMensual:listarFacturas.html.twig', $arrayParametro);
    }

    /**
     * Permite obtener el listado de los puntos clientes segun el parametro de busqueda ingresado
     * se utilizan para la busqueda 4 digitos
     * 
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 21-12-2015
    */
    public function listarPtosClientesAction()
    {
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $strEstado         = 'Pendiente';
        $strPtoCliente     = $objRequest->get("query");
        $intIdCliente      = $objRequest->get("idCliente");
        $intIdEmpresa      = $objSession->get("idEmpresa");
        $emComercial       = $this->get('doctrine')->getManager('telconet');
    
        $objPuntosClientes   = $emComercial->getRepository('schemaBundle:InfoPunto')
            ->getJsonPuntosClientes($intIdCliente, $strPtoCliente,$intIdEmpresa);
       
        $objResponse = new Response($objPuntosClientes);
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * Permite obtener la data que sera presentada en el grid para el respectivo rechazo o aprobacion
     * del mismo
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 21-12-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 01-09-2016 - Se modifica para que ahora muestre las facturas dependiendo de la oficina enviada como parámetro.
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.2 14-09-2017 - Se agrega envío de parámetros mediante un arreglo, adicional se agrega envío de parámetro usrCreacion.
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.3 22-01-2021 - Se modifica la fecha Inicial de Busqueda
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.4 28-04-2022 - Se agrega permisos para poder mostrar boton de clonacion
     */
    public function listarFacturasGridAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        
        $objCliente         = $objSession->get('cliente');
        $intEmpresaId       = $objRequest->getSession()->get('idEmpresa');
        $intIdOficina       = $objRequest->get("intIdOficina") ? $objRequest->get("intIdOficina") : 0;
        $intLimit           = $objRequest->get("limit");
        $intStart           = $objRequest->get("start");

        $strfechaDesde      = explode('T', $objRequest->get("fechaDesde"));
        $strfechaHasta      = explode('T', $objRequest->get("fechaHasta"));
               
        $intIdCliente       = $objRequest->get("idCliente");
        $intPtoCliente      = $objRequest->get('idPtoCliente');
        $strUsrCreacion     = $objRequest->get('usrCreacion'); 
        $emFinanciero       = $this->get('doctrine')->getManager('telconet_financiero');
        $strTipoDoc         = 'FAC';

        $objInfoDocumentoFinancieroCabService = $this->get('financiero.InfoDocumentoFinancieroCab');
        //Si el de la session existe los sobreescribe al actual
        if($objCliente)
        {
            $intIdCliente = $objCliente['id'];
        }

        $i = 1;
        
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

    /**
     * @Secure(roles="ROLE_185-4757, ROLE_198-4760")
     * 
     * Permite exportar el listado de informacion pendiente a un archivo excel
     * del mismo
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 21-12-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 04-09-2016 - Se cambia para que se puedan exportar las facturas mostradas en el grid
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.2 19-09-2016
     * Se agrega al reporte de facturas los campos Observación y Vendedor.
     * Se modifica el formato/estilo en la presentación de los datos.
     * Se agregan los roles: ROLE_185-4757 - Exportar Facturación Mensual Automática.
     *                       ROLE_198-4760 - Exportar Facturación Proporcional Automática.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.3 14-09-2017 - Se agrega columna para mostrar campo USR CREACION del documento.
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.4 20-01-2021 - Se cambia el header para descargar el archivo Excel
     *
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.5 22-01-2021 - El reporte muestra los detalles de la factura
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.6 15-06-2022 - Se suprime el formato para la data del reporte por motivos de tiempo de descarga
     */
    public function exportarListadoFacturasAction()
    {
        error_reporting(E_ALL);
        ini_set('max_execution_time', 3000000);
        
        $objPHPExcel = new PHPExcel();
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array( ' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);		
        // Establecer propiedades
        $objPHPExcel->getProperties()
            ->setCreator("Telcos")
            ->setLastModifiedBy("Telcos")
            ->setTitle("Listado de Facturas Pendientes")
            ->setSubject("Listado de Facturas Pendientes")
            ->setDescription("")
            ->setKeywords("Excel Office 2007 openxml php")
            ->setCategory("Excel");
        
        //Crea estilo para el titulo del reporte
        $arrayStyleTitulo = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '006699'),
                'size' => 12
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
                'size' => 10
            ),
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
                'size' => 8
            ),
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FFFFFF')
            )
        );

        $objRequest         = $this->getRequest();  
        $objSession         = $objRequest->getSession();
        
        $objCliente         = $objSession->get('cliente');
        $intEmpresaId       = $objRequest->getSession()->get('idEmpresa');
        $intIdOficina       = $objRequest->get("intIdOficina") ? $objRequest->get("intIdOficina") : 0;
        $strNombreOficina   = $objSession->get('nombreOficina');
        $intLimit           = $objRequest->get("limit");
        $intPage            = $objRequest->get("page");
        $intStart           = $objRequest->get("start");
        
        $strfechaDesde      = $objRequest->get("fechaDesde");  
        $strfechaHasta      = $objRequest->get("fechaHasta");        

        if($strfechaDesde!='')
        {
            $strfechaDesde = date("Y/m/d", strtotime($objRequest->get("fechaDesde")));
        }
        else
        {
            //Busca desde inicio de mes
            //Similar a listarFacturasGrid
            $strfechaDesde     = date("Y")."-".date("m")."-01"; 
        }
        
        if($strfechaHasta!='')
        {
            $strfechaHasta = date("Y/m/d", strtotime($objRequest->get("fechaHasta")));
        }
        else
        {
            $strfechaHasta = "";
        }
        
        $intIdCliente       = $objRequest->get("idCliente");   
        $intPtoCliente      = $objRequest->get('idPtoCliente');
        $strUsrCreacion     = $objRequest->get('usrCreacion'); 
        $emFinanciero       = $this->get('doctrine')->getManager('telconet_financiero');
        
        $strCliente         = "";
        $strTipoDoc         = $objRequest->get("strTipoDoc");   
        
        //Si el de la session existe los sobreescribe al actual
        //Igual comportamiento que listarFacturasGridAction
        if($objCliente)
        {
            $intIdCliente = $objCliente['id'];
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

        $arrayParametrosCopy = $arrayParametros;
        
        $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'LISTADO DE FACTURAS PENDIENTES' . ($strfechaDesde ? " - Desde: $strfechaDesde" : "" ));
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($arrayStyleTitulo);
        
        $objPHPExcel->getActiveSheet()->getRowDimension("1")->setRowHeight(15); // Alto para la fila del título
        $objPHPExcel->getActiveSheet()->getRowDimension("2")->setRowHeight(20); // Alto para la fila de las cabeceras.
        
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceReportes = $this->get('financiero.Reportes');

        $i = 2;
        $arrayParametros = array();
        $arrayParametros['COLUMNAS']     = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N');
        $arrayParametros['ALINEAMIENTO'] = array('L', 'L', 'L', 'L', 'L', 'L', 'L', 'L', 'R', 'R', 'R', 'R', 'R', 'R');
        $arrayParametros['COLS_ANCHO']   = array( 13,  18,  11,  16,  35,  30,  25,  30,  15,  15,  15,  15,  15,  15);
        $arrayParametros['INDICE']       = $i;
        $arrayParametros['INDENT']       = 1;
        $serviceReportes->aplicarEstiloFormatoRegistro($objPHPExcel, $arrayParametros);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", 'FE. CREACION')
                                            ->setCellValue("B$i", 'OFICINA')
                                            ->setCellValue("C$i", 'TIPO DOC.')
                                            ->setCellValue("D$i", 'NO. DOCUMENTO')
                                            ->setCellValue("E$i", 'DETALLE DE FACTURA')
                                            ->setCellValue("F$i", 'USUARIO CREACION')
                                            ->setCellValue("G$i", 'CLIENTE')
                                            ->setCellValue("H$i", 'LOGIN (PUNTO CLIENTE)')
                                            ->setCellValue("I$i", 'VENDEDOR')
                                            ->setCellValue("J$i", 'SUBTOTAL')
                                            ->setCellValue("K$i", 'DESCUENTO') 
                                            ->setCellValue("L$i", 'IVA 12%')
                                            ->setCellValue("M$i", 'ICE')
                                            
                                            ->setCellValue("N$i", 'TOTAL');	
        
		$objPHPExcel->getActiveSheet()->getStyle('A2:N2')->applyFromArray($arrayStyleCabecera);

        $arrayListadoDeDocumento = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                    ->getDetallesFinancierosFacturas($arrayParametrosCopy);                         
        $i++;

        $strTypeStr     = PHPExcel_Cell_DataType::TYPE_NUMERIC;
        $objActiveSheet = $objPHPExcel->setActiveSheetIndex(0);

        $arrayParametros['SUMARIO_INI'] = $i;
        if($arrayListadoDeDocumento)
        {
            unset($arrayParametros['COLS_ANCHO']);

            $arrayParametros['FORMATO_VALOR'] = array('T', 'T', 'T', 'T', 'T', 'T', 'T', 'T', 'T', 'N', 'N', 'N', 'N','N');
            foreach($arrayListadoDeDocumento as $objFacturas)
            {
                $strCliente="";
                
                if($objFacturas["razonSocial"] != "")
                {
                    $strCliente     = $objFacturas["razonSocial"];
                }
                else
                {
                    if($objFacturas["nombres"] != "")
                    {
                        $strCliente = $objFacturas["nombres"];
                    }

                    if($objFacturas["apellidos"] != "")
                    {
                        $strCliente .=" " . $objFacturas["apellidos"];
                    }
                }
                
                $fltSubTotal = number_format(floatval($objFacturas["subtotal"]), 2, '.', '');
                $fltIva = number_format(floatval($objFacturas["iva"]), 2, '.', '');
                $fltIce = number_format(floatval($objFacturas["ice"]), 2, '.', '');
                $fltDescuento = number_format(floatval($objFacturas["descuento"]), 2, '.', '');
                $fltTotal = $fltSubTotal + $fltIva + $fltIce - $fltDescuento;
                $objActiveSheet->setCellValue('A'.$i, $objFacturas["feCreacion"])
                               ->setCellValue('B'.$i, $objFacturas["nombreOficina"])
                               ->setCellValue('C'.$i, $objFacturas["codigoTipoDocumento"])
                               ->setCellValue('D'.$i, $objFacturas["id"])
                               ->setCellValue('E'.$i, $objFacturas["detalleFactura"])
                               ->setCellValue('F'.$i, $objFacturas["usrCreacion"])
                               ->setCellValue('G'.$i, $strCliente)  
                               ->setCellValue('H'.$i, $objFacturas["login"])
                               ->setCellValue('I'.$i, $objFacturas["vendedor"])
                               ->setCellValueExplicit('J'.$i, $fltSubTotal, $strTypeStr)
                               ->setCellValueExplicit('L'.$i, $fltIva, $strTypeStr)
                               ->setCellValueExplicit('M'.$i, $fltIce, $strTypeStr)
                               ->setCellValueExplicit('K'.$i, $fltDescuento, $strTypeStr)
                               ->setCellValueExplicit('N'.$i, $fltTotal, $strTypeStr);
                
                $i++;
            }
            
            $objPHPExcel->getActiveSheet()->mergeCells("A$i:H$i");
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", 'TOTALES');
            $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setIndent(1);
            $objPHPExcel->getActiveSheet()->getRowDimension("$i")->setRowHeight(17);
            
            $arrayParametros['INDICE']  = $i;
            $arrayParametros['SUMARIO'] = true;
            $serviceReportes->aplicarEstiloFormatoRegistro($objPHPExcel, $arrayParametros);
        }
        
        // Renombrar Hoja
        $objPHPExcel->getActiveSheet()->setTitle('Facturas Pendientes'); 
        $objPHPExcel->setActiveSheetIndex(0);	
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_facturas_pendientes.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');

        throw new Exception('Archivo de Generacion de listado de Facturas');
    }
    
    /**
     * @Secure(roles="ROLE_185-4738, ROLE_198-4759")
     * 
     * Permite rechazar (modificar el estado de las mismas a "Eliminado") la facturas seleccionadas
     * 
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 21-12-2015
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 23-09-2016
     * Se agregan los roles: ROLE_185-4738 - Rechazar Facturación Mensual Automática.
     *                       ROLE_198-4759 - Rechazar Facturación Proporcional Automática.
     */
    public function rechazarListadoFacturasAction()
    {
        //Obtiene parametros enviados desde el ajax
        $objRequest         = $this->get('request');
        $strEliminar        = $objRequest->get('param');
        $arrayValorEliminar = explode("|", $strEliminar);

        //informacion del pto cliente
        $objSession         = $objRequest->getSession();
        $intEmpresaId       = $objSession->get('idEmpresa');
        $intOficinaId       = $objSession->get('idOficina');
        $strUser            = $objSession->get('user');
        $emFinanciero       = $this->get('doctrine')->getManager('telconet_financiero');
        
		foreach($arrayValorEliminar as $intId)
        {
            $emFinanciero->getConnection()->beginTransaction();
            try
            {
                $objEntity = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($intId);
                if(!$objEntity)
                {
                    throw $this->createNotFoundException('Unable to find InfoDocumentoFinancieroCab entity.' . $intId);
                }

                $objEntity->setEstadoImpresionFact("Eliminado");
                $emFinanciero->persist($objEntity);
                $emFinanciero->flush();

                if($objEntity)
                {
                    $entityHistorial = new InfoDocumentoHistorial();
                    $entityHistorial->setDocumentoId($objEntity);
                    $entityHistorial->setFeCreacion(new \DateTime('now'));
                    $entityHistorial->setUsrCreacion($strUser);
                    $entityHistorial->setEstado("Eliminado");
                    $entityHistorial->setObservacion("Eliminado mediante proceso de aprobación de facturas pendientes");
                    $emFinanciero->persist($entityHistorial);
                    $emFinanciero->flush();
                }
                $emFinanciero->getConnection()->commit();
            }
            catch(\Exception $e)
            {
                echo $e->getMessage();
                $emFinanciero->getConnection()->rollback();
                $emFinanciero->getConnection()->close();
            }
        }

        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $respuesta->setContent("Se rechazaron las facturas con exito.");
        return $respuesta;
    }
    
    /**
     * @Secure(roles="ROLE_185-4737, ROLE_198-4758")
     * 
     * Documentación para el método 'numerarFacturasJava'.
     * Invoca a método que genera numeracion de facturas
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 21-12-2015
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.1 31-03-2016 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 08-08-2016 - Se envía el parámetro de usuario de creación  en 'null', el estado del documento financiero en 'Pendiente' y si la
     *                           factura es electrónica con el valor de 'S' a la función de 'generarNumeracionFacturas' para que siga numerando las
     *                           facturas como lo hace actualmente.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 03-09-2016 - Se envía el parámetro de 'strIdDocumentos' que contiene los id de los documentos seleccionados por el usuario que
     *                           desea procesar.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.4 23-09-2016
     * Se agregan los roles: ROLE_185-4737 - Aprobar Facturación Mensual Automática.
     *                       ROLE_198-4758 - Aprobar Facturación Proporcional Automática.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.5 20-02-2017 Se agrega envío de usuario de sesión.
     */
    public function numerarFacturasJavaAction()
    {
        $objResponse        = new Response();
        $objRequest         = $this->getRequest();  
        $strPrefijoEmpresa  = $objRequest->getSession()->get('prefijoEmpresa');
        $arrayFechaProcesar = $objRequest->get("fechaEmision") ? explode('T', $objRequest->get("fechaEmision")) : array();        
        $strFechaProcesar   = ( !empty($arrayFechaProcesar) ) ? date("d-m-Y", strtotime($arrayFechaProcesar[0]) ) : '';
        $strTipoDoc         = $objRequest->get("strTipoDoc") ? $objRequest->get("strTipoDoc") : '';
        $arrayIdDocumentos  = $objRequest->get("strIdDocumentos") ? explode('|', $objRequest->get("strIdDocumentos")) : array();
        $strMensaje         = "No se pueden procesar las facturas mensuales, por envío de parámetros incorrectos";
        $serviceUtil        = $this->get('schema.Util');
        $strUsuarioSession  = $objRequest->getSession()->get('user');
        $strIpSession       = $objRequest->getClientIp();
        
        if( !empty($strFechaProcesar) && !empty($strTipoDoc) && !empty($arrayIdDocumentos) )
        {
            try
            {
                $em_financiero                          = $this->get('doctrine')->getManager('telconet_financiero');
                $arrayParametros['fecha_emision']       = $strFechaProcesar;
                $arrayParametros['prefijo_empresa']     = $strPrefijoEmpresa;
                $arrayParametros['strTipoDoc']          = $strTipoDoc;
                $arrayParametros['usrCreacion']         = null;
                $arrayParametros['estadoImpresionFact'] = 'Pendiente';
                $arrayParametros['esElectronica']       = 'S';
                $arrayParametros['arrayIdDocumentos']   = $arrayIdDocumentos;
                $arrayParametros['strUsrSesion']        = $strUsuarioSession;

                if( $strPrefijoEmpresa == "TN" )
                {        
                    $strMensaje = $em_financiero->getRepository('schemaBundle:InfoProcesoMasivoCab')->generarNumeracionFacturas($arrayParametros);
                }
                else
                {
                    $strMensaje = "Opción habilitada para la EMPRESA TELCONET unicamente";     
                }
            }
            catch(\Exception $ex)
            {
                $strMensaje = "No se procesaron las facturas seleccionadas, hubo un problema al numerarlas.";

                $serviceUtil->insertError( 'Telcos+', 
                                           'numerarFacturasJavaAction', 
                                            $strMensaje." - ".$ex->getMessage(),
                                            $strUsuarioSession, 
                                            $strIpSession );
            }
        }//( !empty($strFechaProcesar) && !empty($strTipoDoc) && !empty($arrayIdDocumentos) )
        
        $objResponse->setContent($strMensaje);
        
        return $objResponse;      
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
        $arrayParametros['valor1']                = 'FAC'; 
        $arrayParametros['estado']                = 'Activo';

        
        $objUsers = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getJSONParametrosByCriterios($arrayParametros);  
        
        $objJsonResponse      = new JsonResponse();
        $objJsonResponse->setContent($objUsers);
        return $objJsonResponse;
    }    
    
}
