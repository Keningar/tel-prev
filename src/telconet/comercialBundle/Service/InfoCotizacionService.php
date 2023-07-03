<?php
namespace telconet\comercialBundle\Service;
use telconet\schemaBundle\Entity\InfoCotizacionCab;
use telconet\schemaBundle\Entity\InfoCotizacionDet;


class InfoCotizacionService
{
    private $serviceUtil;
    private $emGeneral;
    private $emComercial;
    private $strPathTelcos;
    private $strRutaContrato;
    private $strRutaCotizacion;
    private $strRutaCotTelcos;
    private $strRutaImagenMd;
    private $strTemplate;
    private $objKnp;
    private $serviceEnvioMail;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer)
    {
        $this->serviceUtil       = $objContainer->get('schema.Util');
        $this->emGeneral         = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
        $this->emComercial       = $objContainer->get('doctrine')->getManager('telconet');
        $this->strPathTelcos     = $objContainer->getParameter('path_telcos');
        $this->strRutaContrato   = $objContainer->getParameter('contrato_digital_ruta');
        $this->strRutaCotizacion = $objContainer->getParameter('ruta_cotizaciones_mobile_comercial');
        $this->strRutaCotTelcos  = $objContainer->getParameter('ruta_cotizaciones_telcos');
        $this->strRutaImagenMd   = $objContainer->getParameter('ruta_imagenes_megadatos');
        $this->strTemplate       = $objContainer->get('templating');
        $this->objKnp            = $objContainer->get('knp_snappy.pdf');
        $this->serviceEnvioMail  = $objContainer->get('soporte.EnvioPlantilla');
    }

    public function putCotizacion($arrayData)
    {


        $strCodEmpresa          = $arrayData['data']['enterpriseCode'];
        $intPuntoId             = $arrayData['data']['pointId'];
        $intPersonaEmpresaRolId = $arrayData['data']['enterprisePersonRolId'];
        $strUsrCreacion         = $arrayData['user'];
        $arrayProductos         = $arrayData["data"]['cotizerListProduct'];
        $intNumeroCotizacion    = isset($arrayData['data']['cotizationNumber']) ? $arrayData['data']['cotizationNumber'] : 0;

        $strRutaPdf    = $this->strPathTelcos . $this->strRutaContrato;
        $strRutaAppPdf = $this->strRutaCotizacion;
        $strRutaBase   = $this->strPathTelcos . $this->strRutaCotTelcos;
        $strRutaImagen = $this->strPathTelcos . $this->strRutaImagenMd;
        $this->emComercial->getConnection()->beginTransaction();
        try
        {
            $entityAdmiImpuestoIva = $this->emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                               ->findOneBy( array('tipoImpuesto' => 'IVA', 'estado' => 'Activo') );
            
            //verifico que exista la carpeta cotizaciones
            if ($intNumeroCotizacion == 0)
            {
                $objNumeracion        = $this->emComercial->getRepository('schemaBundle:AdmiNumeracion')
                                                          ->findByEmpresaYOficina($strCodEmpresa,58,"COTI");
                $intSecuencia         = $objNumeracion->getSecuencia() + 1; 
                $entityPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                       ->find($intPersonaEmpresaRolId);
                $entityPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                           ->find($intPuntoId);
                $objFecha = new \DateTime();

                $objCotizacionCab = new InfoCotizacionCab();
                $objCotizacionCab->setNumeroCotizacion($intSecuencia);
                $objCotizacionCab->setFeCreacion($objFecha);
                $objCotizacionCab->setUsrCreacion($strUsrCreacion);
                $objCotizacionCab->setIpCreacion("127.0.0.1");
                $objCotizacionCab->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                $objCotizacionCab->setPuntoId($entityPunto);
                $objCotizacionCab->setEmpresaCod($strCodEmpresa);
                $this->emComercial->persist($objCotizacionCab);
                $this->emComercial->flush();

                //guardo el detalle
                foreach ($arrayProductos as $arrayProducto)
                {
                    $intProducto = $arrayProducto['productId'];
                    $intCantidad = $arrayProducto['productCount'];
                    $intPrecio   = $arrayProducto['productPrice'];
                    $strGravaIva = isset($arrayProducto['gravaIva']) ? $arrayProducto['gravaIva']  : "N";
                    if ($intCantidad > 0)
                    {
                        $objCotizacionDet = new InfoCotizacionDet();
                        $objCotizacionDet->setCotizacionId($objCotizacionCab);
                        $objCotizacionDet->setPlanId(0);
                        $objCotizacionDet->setEsVenta("N");
                        $objCotizacionDet->setCantidad($intCantidad);
                        $objCotizacionDet->setPrecioVenta($intPrecio);
                        $objCotizacionDet->setCosto(0);
                        $objCotizacionDet->setValorDescuento(0);
                        $objCotizacionDet->setDiasGracia(0);
                        $objCotizacionDet->setFrecuenciaProducto(0);
                        $objCotizacionDet->setDescripcionPresentaFactura(" ");
                        $objCotizacionDet->setTieneSolicitudDescuento("N");
                        $objCotizacionDet->setTieneSolicitudCambioDoc("N");
                        $objCotizacionDet->setEstado("Activo");
                        $objCotizacionDet->setEmpresaId($strCodEmpresa);
                        $objCotizacionDet->setProspectoId(0);
                        $objCotizacionDet->setProductoId($intProducto);
                        $objCotizacionDet->setPorcentajeIva(0);
                        if ($strGravaIva === "S")
                        {
                            $objCotizacionDet->setPorcentajeIva($entityAdmiImpuestoIva->getPorcentajeImpuesto());
                        }
                        $this->emComercial->persist($objCotizacionDet);
                        $this->emComercial->flush();
                    }
                } 
            }
            else
            {
                $objCotizacionCab = $this->emComercial->getRepository('schemaBundle:InfoCotizacionCab')
                                                ->findOneBy(array('numeroCotizacion' => $intNumeroCotizacion,
                                                                   'empresaCod'       => $strCodEmpresa));
            }
            $strNombreCliente = $objCotizacionCab->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial();
            if (is_null($strNombreCliente) || $strNombreCliente == "")
            {
                $strNombreCliente  = $objCotizacionCab->getPersonaEmpresaRolId()->getPersonaId()->getApellidos() . " " .
                                     $objCotizacionCab->getPersonaEmpresaRolId()->getPersonaId()->getNombres();
            }
            $strIdentificacion = $objCotizacionCab->getPersonaEmpresaRolId()->getPersonaId()->getIdentificacionCliente();
            $strDireccion      = $objCotizacionCab->getPersonaEmpresaRolId()->getPersonaId()->getDireccionTributaria(); 

            $arrayFormasContactoPunto = $this->emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                    ->findPorEstadoPorPunto($intPuntoId,'Activo',6,0);
            if($arrayFormasContactoPunto['registros'])
            {
                $arrayFormasContactoPunto = $arrayFormasContactoPunto['registros']; 
                $intCont = 0;
                $strTelefonos = "";
                foreach($arrayFormasContactoPunto as $objFormaContactoPunto)
                {
                    $strFormaContacto = $objFormaContactoPunto->getFormaContactoId()->getDescripcionFormaContacto();
                    if (strpos($strFormaContacto, 'Telefono') !== false)
                    {
                        if($intCont==0)
                        {
                            $strTelefonos .= $objFormaContactoPunto->getValor();
                        }
                        else
                        {
                            $strTelefonos .= "-" . $objFormaContactoPunto->getValor();
                        }
                        $intCont++;
                    }
                }
            }
            $objDetalleCot = $this->emComercial->getRepository('schemaBundle:InfoCotizacionDet')
                                         ->findBy(array('cotizacionId' => $objCotizacionCab->getId()));
            $intSubtotal  = 0;
            $intTax       = 0;
            $arrayDetalle = array();
            foreach($objDetalleCot as $objDetalle)
            {
                $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                             ->find($objDetalle->getProductoId());
                $arrayDet = array();
                $arrayDet['productId']       = $objDetalle->getProductoId();
                $arrayDet['productCount']    = $objDetalle->getCantidad();
                $arrayDet['nameProduct']     = $objProducto->getDescripcionProducto();
                $arrayDet['productPrice']    = number_format($objDetalle->getPrecioVenta(), 2);
                $arrayDet['subTotalProduct'] = number_format(round($objDetalle->getCantidad() * $objDetalle->getPrecioVenta(), 2), 2);
                $arrayDetalle[]   = $arrayDet;
                $intSubtotalLinea = round($objDetalle->getCantidad() * $objDetalle->getPrecioVenta(), 2); 
                $intSubtotal     += $intSubtotalLinea ;
                $intTax          +=  round($intSubtotalLinea * ($objDetalle->getPorcentajeIva()/100), 2);
            }
            $strSubtotal      = number_format($intSubtotal, 2);
            $strTax           = number_format($intTax, 2);
            $strTotal         = number_format($intSubtotal + $intTax, 2);
            $strIvaPorcentaje = "";
            if ($intTax > 0)
            {
                $strIvaPorcentaje = number_format($entityAdmiImpuestoIva->getPorcentajeImpuesto(),0);
            }
            $arrayParametrosHtml = array("nombreCliente"         => $strNombreCliente,
                                         "identificacionCliente" => $strIdentificacion,
                                         "fecha"                 => $objCotizacionCab->getFeCreacion()->format('d-m-Y'),
                                         "numero"                => $objCotizacionCab->getNumeroCotizacion(),
                                         "telefonos"             => $strTelefonos,
                                         "direccion"             => $strDireccion,
                                         "subTotal"              => $strSubtotal,
                                         "tax"                   => $strTax,
                                         "total"                 => $strTotal,
                                         "ivaPorcentaje"         => $strIvaPorcentaje,
                                         "imagenNetLife"         => $strRutaImagen . "logo-netlife.png",
                                         "imagenNetHome"         => $strRutaImagen . "logo-nethome.png",
                                         "imagenBanner"          => $strRutaImagen . "bg-nethome.jpg",
                                         "detalleProductos"      => $arrayDetalle
                                        );

            $strNombreDocumento = "Cotizacion_" . $objCotizacionCab->getNumeroCotizacion() . "_" . $strIdentificacion . "_" . 
                    $objCotizacionCab->getFeCreacion()->format('YmdHi') . ".pdf";
            $objHtmlPdf = $this->strTemplate->render('comercialBundle:infocontrato:cotizacion.html.twig',
                                                                    $arrayParametrosHtml);
            
            if ($intNumeroCotizacion == 0)
            {

                $objCotizacionCab->setArchivoDigital($strRutaAppPdf . $objCotizacionCab->getFeCreacion()->format('Ymd') . "/" . $strNombreDocumento);
                $this->emComercial->persist($objCotizacionCab);
                $this->emComercial->flush();

                $objNumeracion->setSecuencia($intSecuencia);
                $objNumeracion->setFeUltMod(new \DateTime());
                $objNumeracion->setUsrUltMod($strUsrCreacion);
                $this->emComercial->persist($objNumeracion);
                $this->emComercial->flush();


                $this->objKnp->generateFromHtml($objHtmlPdf, $strRutaPdf . "cotizaciones/" . 
                                                                          $objCotizacionCab->getFeCreacion()->format('Ymd') . "/" .
                                                                          $strNombreDocumento); 
            }

            $strAsunto = "Cotización Servicios NETHOME";

            $arrayParametros = array("intIdPersona"     => $objCotizacionCab->getPersonaEmpresaRolId()->getPersonaId()->getId(),
                                     "strFormaContacto" => 'Correo Electronico');

            $arrayTo = array();
            $arrayFormasContactoCliente = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                               ->getContactosByIdPersonaAndFormaContacto($arrayParametros);

            if($arrayFormasContactoCliente)
            {
                foreach($arrayFormasContactoCliente as $arrayFormaContacto)
                {
                     $arrayTo[] = $arrayFormaContacto['valor'];
                }
            } 

            $strArchivoAdjunto = $strRutaBase . $objCotizacionCab->getArchivoDigital();
            /* @var $envioPlantilla EnvioPlantilla */                
            
            $this->serviceEnvioMail->enviarCorreo($strAsunto, $arrayTo, $objHtmlPdf, $strArchivoAdjunto);
            
            $arrayRespuesta = array();
            $arrayRespuesta['status']  = 200;
            $arrayRespuesta['message'] = $objCotizacionCab->getNumeroCotizacion();
            $arrayRespuesta['success'] = true;
            $this->emComercial->getConnection()->commit();
        } 
        catch (Exception $ex) 
        {
            $arrayRespuesta['status']  = 500;
            $arrayRespuesta['message'] = "ERROR";
            $arrayRespuesta['success'] = true;
            
            $arrayParametrosLog['enterpriseCode']   = $arrayData['data']['enterpriseCode'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = "ComercialMobileWSControllerRest";
            $arrayParametrosLog['appClass']         = "ComercialMobileWSControllerRest";
            $arrayParametrosLog['appMethod']        = "getListaCotizacion";
            $arrayParametrosLog['appAction']        = "getListaCotizacion";
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $ex->getMessage();
            $arrayParametrosLog['inParameters']     = implode(";", $arrayData);
            $arrayParametrosLog['creationUser']     = $arrayData['data']['filterLoginVendor'];
            $this->serviceUtil->insertLog($arrayParametrosLog);
            $this->emComercial->getConnection()->rollback(); 
            $this->emComercial->getConnection()->close();
            
            
        }
        return $arrayRespuesta;
    }
    /**
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 10-09-2018 - Lista las cotizaciones según el filtro establecido
     * 
     */
    public function getListaCotizacion($arrayData)
    {
        $arrayParametros = array();
        $arrayParametros['strCodEmpresa']            = $arrayData['data']['filterEnterpriseCode'];
        $arrayParametros['intNumeroCotizacion']      = $arrayData['data']['filterCotizationNum'];
        $arrayParametros['strIdentificacionCliente'] = $arrayData['data']['filterCustomerIdentification'];
        $arrayParametros['strFechaDesde']            = isset($arrayData['data']['filterCotizationStartDate']) ? 
                                                       $arrayData['data']['filterCotizationStartDate'] . " 00:00:00" : " 00:00:00" ;
        $arrayParametros['strFechaHasta']            = isset($arrayData['data']['filterCotizationEndDate']) ?
                                                       $arrayData['data']['filterCotizationEndDate'] . " 23:59:59" : " 23:59:59" ;
        $arrayParametros['strLoginVendedor']         = $arrayData['data']['filterLoginVendor'];

        try
        {
            $arrayCotizaciones = $this->emComercial->getRepository('schemaBundle:InfoCotizacionCab')->getCotizacionListado($arrayParametros);

            $arrayResponse['cotizationList'] = array();
            foreach ($arrayCotizaciones as $arrayCotizacion)
            {
                $arrayCab = array();
                $arrayCab['cotizationId']                = $arrayCotizacion['idCotizacion'];
                $arrayCab['cotizationNumber']            = $arrayCotizacion['numeroCotizacion'];
                $arrayCab['cotizerDate']                 = $arrayCotizacion['feCreacion'];
                $arrayCab['cotizerIdentificationOwner']  = $arrayCotizacion['identificacionCliente'];
                $arrayCab['cotizerNameOwner']            = $arrayCotizacion['nombres']; 
                $arrayCab['cotizerStatusOwner']          = $arrayCotizacion['estado']; 
                $arrayCab['enterprisePersonRolId']       = $arrayCotizacion['idPersonaRol']; 
                $arrayCab['enterpriseCode']              = $arrayCotizacion['empresaCod']; 
                $arrayCab['pointId']                     = $arrayCotizacion['puntoId'];
                $arrayCab['pointLogin']                  = $arrayCotizacion['login'];
                $arrayCab['cotizerLoginVendor']          = $arrayCotizacion['usrCreacion'];
                $arrayCab['nameFile']                    = $arrayCotizacion['archivoDigital'];
                $arrayCab['cotizationNumberOs']          = 0;
                $objDetalleCot = $this->emComercial->getRepository('schemaBundle:InfoCotizacionDet')
                                             ->findBy(array('cotizacionId' => $arrayCotizacion['idCotizacion']));
                $intSubtotal = 0;
                $arrayDetalle = array();
                foreach($objDetalleCot as $objDetalle)
                {
                    $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                               ->find($objDetalle->getProductoId());
                    $arrayDet = array();
                    $arrayDet['productId']       = $objDetalle->getProductoId();
                    $arrayDet['productCount']    = $objDetalle->getCantidad();
                    $arrayDet['nameProduct']     = $objProducto->getDescripcionProducto();
                    $arrayDet['productPrice']    = number_format(floatval($objDetalle->getPrecioVenta()), 2, ".", "");
                    $arrayDet['subTotalProduct'] = number_format(round($objDetalle->getCantidad() * 
                                                                       floatval($objDetalle->getPrecioVenta()), 2), 2, ".","");
                    $arrayDetalle[] = $arrayDet;
                    $intSubtotal += round($objDetalle->getCantidad() * $objDetalle->getPrecioVenta(), 2);
                }
                $intTax = round($intSubtotal * .12, 2);
                $arrayCab['cotizerSubTotal']    = number_format($intSubtotal, 2,".","");
                $arrayCab['cotizerTax']         = number_format($intTax, 2,".","");
                $arrayCab['cotizerTotal']       = number_format($intSubtotal + $intTax, 2,".","");
                $arrayCab['cotizerListProduct'] = $arrayDetalle;
                $arrayResponse['cotizationList'][] = $arrayCab;
            }
            $arrayRespuesta['response'] = $arrayResponse;
            $arrayRespuesta['status']  = "200";
            $arrayRespuesta['message'] = "OK";
            $arrayRespuesta['success'] = true;
        }
        catch (Exception $ex) 
        {
            $arrayRespuesta['status']  = "500";
            $arrayRespuesta['message'] = "ERROR";
            $arrayRespuesta['success'] = true;
            $arrayParametrosLog['enterpriseCode']   = $arrayParametros['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = "ComercialMobileWSControllerRest";
            $arrayParametrosLog['appClass']         = "ComercialMobileWSControllerRest";
            $arrayParametrosLog['appMethod']        = "getListaCotizacion";
            $arrayParametrosLog['appAction']        = "getListaCotizacion";
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $ex->getMessage();
            $arrayParametrosLog['inParameters']     = implode(";", $arrayData);
            $arrayParametrosLog['creationUser']     = $arrayData['data']['filterLoginVendor'];
            $this->serviceUtil->insertLog($arrayParametrosLog);
        }
        return $arrayRespuesta;
    }
        
        
}

