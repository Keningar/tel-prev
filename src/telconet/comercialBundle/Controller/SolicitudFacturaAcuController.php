<?php

namespace telconet\comercialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\AdmiProducto;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Entity\AdmiDepartamento;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\AdmiCaracteristica;
use telconet\schemaBundle\Entity\AdmiProductoCaracteristica;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\AdmiCanton;
use telconet\schemaBundle\Form\InfoDetalleSolicitudType;
use Symfony\Component\HttpFoundation\Response;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Service\UtilService;

class SolicitudFacturaAcuController extends Controller implements TokenAuthenticatedController
{

    /**
     * @Secure(roles="ROLE_456-1")
     *
     * Documentación para la función 'indexAction'.
     *
     * Función que carga la pantalla de solicitud de proyecto.
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 03-01-2020
     *
     * @return render Redirecciona al index de la opción.
     */
    public function indexAction()
    {
        $objRequest     = $this->getRequest();
        $strUsrCreacion = $objRequest->getSession()->get('user');
        $strIpCreacion  = $objRequest->getClientIp();
        $serviceUtil    = $this->get('schema.Util');
        $arrayRolesPermitidos = array();
        try
        {
            if($this->get('security.context')->isGranted('ROLE_456-1'))
            {
                $arrayRolesPermitidos[] = 'ROLE_456-1';
            }
            
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos', 'indexAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        return $this->render('comercialBundle:SolicitudFacturaAcu:index.html.twig', array('rolesPermitidos' => $arrayRolesPermitidos));
    }

    /**
     * @Secure(roles="ROLE_456-1")
     *
     * Documentación para la función 'gridAction'.
     *
     * Función que retorna el listado de solicitudes de proyecto.
     *
     * @return $objResponse - Listado de Solicitudes.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 03-01-2021
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.1 07-04-2021 - Se agrega lógica para listar las solicitudes de R1 y R2.
     *
     */
    public function gridAction()
    {
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $strNombre         = $objRequest->get("strNombre") ? $objRequest->get("strNombre") : "";
        $strLogin          = $objRequest->get("strLogin") ? $objRequest->get("strLogin") : "";
        $strFechaInicio    = $objRequest->get("strFechaInicio") ? $objRequest->get("strFechaInicio") : "";
        $strFechaFin       = $objRequest->get("strFechaFin") ? $objRequest->get("strFechaFin") : "";
        $strEstado         = $objRequest->get("strEstado") ? $objRequest->get("strEstado") : "";
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "";
        $intIdEmpresa      = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "";
        $strUsrCreacion    = $objSession->get('user') ? $objSession->get('user') : "";
        $intIdPersEmpRol   = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : "";
        $intIdCanton       = $objSession->get('intIdCanton') ? $objSession->get('intIdCanton') : "";
        $strIpCreacion     = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $emComercial       = $this->get('doctrine')->getManager('telconet');
        $serviceUtil       = $this->get('schema.Util');
        $serviceComercial  = $this->get('comercial.Comercial');
        $serviceUtilidades = $this->get('administracion.Utilidades');
        $intTotal          = 0;
        $intCantidad       = 0;
        $arraySolicitud    = array();
        $strTipoSolicitud  = "SOLICITUD FACTURACION ACUMULADA";
        $strRegionSesion   = "";
        $boolIgnorarCargo  = false;
        try
        {
            if(empty($intIdCanton))
            {
                throw new \Exception('Error al obtener el cantón del usuario en sesión.');
            }
            $objCanton = $emComercial->getRepository("schemaBundle:AdmiCanton")->find($intIdCanton);
            if(empty($objCanton) || !is_object($objCanton))
            {
                throw new \Exception('Error al obtener el cantón del usuario en sesión.');
            }
            $strRegionSesion  = $objCanton->getRegion();
            $objTipoSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')->findOneBy
                (array('descripcionSolicitud' => $strTipoSolicitud,
                'estado' => 'Activo'));
            if(!is_object($objTipoSolicitud) && empty($objTipoSolicitud))
            {
                throw new \Exception('Error al Obtener el Tipo de Solicitud, favor verificar.');
            }
            $objCaracVendedor = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findOneBy(array('descripcionCaracteristica' => 'VENDEDOR_FACTURA',
                'estado' => 'Activo'));

            if(!is_object($objCaracVendedor) && empty($objCaracVendedor))
            {
                throw new \Exception('Error al Obtener las caracteristicas, favor verificar.');
            }
            $objCaracFactura = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->findOneBy(array('descripcionCaracteristica' => 'NUM_FACTURA_ACUMULADA',
                                                             'estado'                    => 'Activo'));

            if(!is_object($objCaracFactura) && empty($objCaracFactura))
            {
                throw new \Exception('Error al Obtener las características de factura, favor verificar.');
            }
            $arrayRolesNoIncluidos = array();
            $arrayParametrosRoles  = array( 'strCodEmpresa'     => $intIdEmpresa,
                                            'strValorRetornar'  => 'descripcion',
                                            'strNombreProceso'  => 'JEFES',
                                            'strNombreModulo'   => 'COMERCIAL',
                                            'strNombreCabecera' => 'ROLES_NO_PERMITIDOS',
                                            'strUsrCreacion'    => $strUsrCreacion,
                                            'strIpCreacion'     => $strIpCreacion );
            $arrayResultadosRolesNoIncluidos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosRoles);

            if( isset($arrayResultadosRolesNoIncluidos['resultado']) && !empty($arrayResultadosRolesNoIncluidos['resultado']) )
            {
                foreach( $arrayResultadosRolesNoIncluidos['resultado'] as $strRolNoIncluido )
                {
                    $arrayRolesNoIncluidos[] = $strRolNoIncluido;
                }
            }
            $arrayDatos = array('intIdTipoSolicitud'  => $objTipoSolicitud->getId(),
                                'intCaracteristica'   => $objCaracVendedor->getId(),
                                'intCaractFactura'    => $objCaracFactura->getId(),
                                'strUsuario'          => $strUsrCreacion,
                                'strEstado'           => $strEstado,
                                'strLogin'            => $strLogin,
                                'strFechaInicio'      => $strFechaInicio,
                                'strFechaFin'         => $strFechaFin,
                                'arrayRolNoPermitido' => (!empty($arrayRolesNoIncluidos)&&is_array($arrayRolesNoIncluidos))
                                                          ? $arrayRolesNoIncluidos:"",
                                'boolIgnorarCargo'    => $boolIgnorarCargo,
                                'intIdEmpresa'        => $intIdEmpresa,
                                'strPrefijoEmpresa'   => $strPrefijoEmpresa);
            /**
             * Bloque que válida si el usuario en sesión pertenece a la lista 
             * de personas que puede aprobar solicitudes, sin importar el cargo.
             */
            $arrayListaUsuarios = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                              ->get('CAMBIO_FACTURA_COMISION', 
                                                    'COMERCIAL', 
                                                    '', 
                                                    'LISTA_USUARIO_APROBACION', 
                                                    $strUsrCreacion,
                                                    $strRegionSesion,
                                                    '',
                                                    '',
                                                    '',
                                                    $intIdEmpresa);
            if(!empty($arrayListaUsuarios) && is_array($arrayListaUsuarios))
            {
                $boolIgnorarCargo               = true;
                $strEstadoBuscar                = (!empty($strEstado) && $strEstado != "Seleccionar") ? $strEstado: $arrayListaUsuarios[0]["valor3"];
                $strCargoVendedor               = $arrayListaUsuarios[0]["valor4"];
                $arrayDatos['boolIgnorarCargo'] = $boolIgnorarCargo;
                $arrayDatos['strEstado']        = $strEstadoBuscar;
                $arrayDatos['strRegion']        = $strRegionSesion;
                $arrayDatos['strCargo']         = $strCargoVendedor;
            }
            $arrayResultadoCaracteristicas = $emComercial->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsrCreacion);
            if(!empty($arrayResultadoCaracteristicas) && !$boolIgnorarCargo)
            {
                $arrayResultadoCaracteristicas  = $arrayResultadoCaracteristicas[0];
                $strCargoVendedor               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] 
                                                  ? $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
                $arrayDatos['strCargo']         = (!empty($strCargoVendedor) && $strCargoVendedor != "Otros") ? $strCargoVendedor:"";

                if(!empty($strCargoVendedor) && $strCargoVendedor == 'SUBGERENTE')
                {
                    $arrayVendedores               = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                 ->getVendedor($intIdPersEmpRol, $strPrefijoEmpresa);
                    $strEstadoBuscar               = (!empty($strEstado) && $strEstado != "Seleccionar") ? $strEstado: 'Pendiente SubGerente';
                    $arrayDatos['strEstado']       = $strEstadoBuscar;
                    $arrayDatos['arrayVendedores'] = (!empty($arrayVendedores) && is_array($arrayVendedores)) ? $arrayVendedores:"";
                }
                elseif(!empty($strCargoVendedor) && $strCargoVendedor == 'GERENTE_VENTAS')
                {
                    $strEstadoBuscar         = (!empty($strEstado) && $strEstado != "Seleccionar") ? $strEstado: 'Pendiente Gerente';
                    $arrayDatos['strEstado'] = $strEstadoBuscar;
                }
            }
            $arrayFacturas = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->getListadoSolCamFactura($arrayDatos);
            if(!empty($arrayFacturas) && is_array($arrayFacturas))
            {
                foreach($arrayFacturas as $arrayItemFactura):
                    $intCantidad +=1;
                    $arrayTipoFact = array('strCargo'     => $strCargoVendedor,
                                           'strSolicitud' => $arrayItemFactura['idSolicitud']);
                    $arraySolicitud[] = array('intNumero'      => $intCantidad,
                                              'strSolicitud'   => $arrayItemFactura['idSolicitud'],
                                              'strVendedor'    => ucwords(strtolower($arrayItemFactura['vendedor'])),
                                              'strEmision'     => date_format(date_create($arrayItemFactura['fecha']), 'd/m/y'),
                                              'strEstado'      => $arrayItemFactura['estado'],
                                              'strObservacion' => $arrayItemFactura['observacion'],
                                              'valor_total'    => "$  " . number_format($arrayItemFactura['valor_total'], 2),
                                              'strAcciones'    => $arrayTipoFact);
                endforeach;
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos', 'gridAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        $objResponse = new Response(json_encode(array('intTotal' => $intCantidad, 'data' => $arraySolicitud)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * Documentación para la función 'showAction'.
     *
     * Función que renderiza la página de Ver detalle de solicitudes de proyecto.
     *
     * @return render - Página de Ver Solicitud.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 21-01-2021
     *
     */
    public function showAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strSolicitud           = $objRequest->get("strSolicitud") ? $objRequest->get("strSolicitud") : "";
        $intIdEmpresa           = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "";
        $strUsrCreacion         = $objSession->get('user') ? $objSession->get('user') : "";
        $strIpCreacion          = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $serviceUtil            = $this->get('schema.Util');
        $emComercial            = $this->get('doctrine')->getManager('telconet');
        $emFinanciero           = $this->get('doctrine')->getManager('telconet_financiero');
        $strCliente             = '';
        $strLoginCliente        = '';
        $strMotivo              = '';
        $arrayServicio          = array();
        $strDescripcion         = 'N/A';
        try
        {
            if(empty($strSolicitud))
            {
                throw new \Exception("Favor verificar la solicitud.");
            }
            $objInfoDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($strSolicitud);
            if(!is_object($objInfoDetalleSolicitud) && empty($objInfoDetalleSolicitud))
            {
                throw new \Exception("Solicitud no encontrada, Favor verificar");
            }
            
            $objMotivoId = $emComercial->getRepository('schemaBundle:AdmiMotivo')->find($objInfoDetalleSolicitud->getMotivoId());
            if(is_object($objMotivoId) && !empty($objMotivoId))
            {
                $strMotivo = $objMotivoId->getNombreMotivo();
            }
            
            $objCaracNumer = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array('descripcionCaracteristica' => 'NUM_FACTURA_ACUMULADA',
                    'estado' => 'Activo'));
            $objCaracValor = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findOneBy(array('descripcionCaracteristica' => 'VAL_FACTURA_ACUMULADA',
                'estado' => 'Activo'));

            $objCaracVendedor = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findOneBy(array('descripcionCaracteristica' => 'VENDEDOR_FACTURA',
                'estado' => 'Activo'));
            
            if(!is_object($objCaracNumer) && empty($objCaracNumer) && !is_object($objCaracValor) && 
                    empty($objCaracValor) && !is_object($objCaracVendedor) && empty($objCaracVendedor))
            {
                throw new \Exception('Error al Obtener las características, favor verificar.');
            }
            
            $objSolicitudDetCaracFact = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                        ->findOneBy(array('caracteristicaId' => $objCaracNumer->getId(),
                                          'detalleSolicitudId' => $objInfoDetalleSolicitud->getId()));
            
            $objSolicitudDetCaracVal = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                        ->findOneBy(array('caracteristicaId' => $objCaracValor->getId(),
                                          'detalleSolicitudId' => $objInfoDetalleSolicitud->getId()));
            
            $objSolicitudDetCaracVen = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                        ->findOneBy(array('caracteristicaId' => $objCaracVendedor->getId(),
                                          'detalleSolicitudId' => $objInfoDetalleSolicitud->getId()));
            
            if(is_object($objSolicitudDetCaracFact) && !empty($objSolicitudDetCaracFact))
            {
               $objFactura =  $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($objSolicitudDetCaracFact->getValor());
               
               if(is_object($objFactura) && !empty($objFactura))
               {
                   $arrayFacturaDetalle =  $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                                                                ->findBy(array('documentoId' => $objFactura->getId()));
                   
                   if(!empty($arrayFacturaDetalle))
                   {
                       foreach($arrayFacturaDetalle as $objFacturaDetalle):
                           if(is_object($objFacturaDetalle->getServicioId()))
                           {
                               $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($objFacturaDetalle->getServicioId());
                               $strDescripcion = $objServicio->getDescripcionPresentaFactura();
                           }
                           
                       
                           $objProducto = $emComercial->getRepository('schemaBundle:AdmiProducto')->find($objFacturaDetalle->getProductoId());
                           
                           if (is_object($objProducto) && !empty($objProducto))
                           {
                               $arrayServicio [] = array("strDescripcion" => $strDescripcion,
                                                         "strProducto"    => $objProducto->getDescripcionProducto());
                           }
                           
                       endforeach;
                   }
                   $objPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->find($objFactura->getPuntoId());
                   if(is_object($objPunto) && !empty($objPunto))
                   {
                       $objPersonaEmpRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                                            ->find($objPunto->getPersonaEmpresaRolId()->getId());
                       if(is_object($objPersonaEmpRol) && !empty($objPersonaEmpRol))
                       {
                           $objPersona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($objPersonaEmpRol->getPersonaId());
                           if(is_object($objPersona) && !empty($objPersona))
                           {
                               $strCliente = $objPersona->getRazonSocial();
                               if(empty($strCliente))
                               {
                                  $strCliente =  $objPersona->getNombres()+' '+$objPersona->getApellidos();
                               }
                           }
                       }
                       $strLoginCliente = $objPunto->getLogin();
                   }
                   $strFechaConsumo = date_format($objFactura->getFeCreacion(), 'd/m/y');
                   $arraySolicitudDet = array('intSolicitudId' => $objInfoDetalleSolicitud->getId(),
                    'strEstadoSol'      => $objInfoDetalleSolicitud->getEstado(),
                    'strMotivo'         => $strMotivo,
                    'intFacturaId'      => $objFactura->getId(),
                    'strCambioFactura'  =>  $objSolicitudDetCaracVal->getValor(),
                    'strFechaFactura'   => $strFechaConsumo,   
                    'arrayServicio'     => $arrayServicio,
                    'strCliente'        => $strCliente,
                    'strLogin'          => $objPunto->getLogin());
               }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos', 'showAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        return $this->render('comercialBundle:SolicitudFacturaAcu:show.html.twig', array('arraySolicitudDet' => $arraySolicitudDet));
    }

    /**
     * Documentación para la función 'listarFacturas'.
     *
     * Función que renderiza la página de Ver detalle de solicitudes de proyecto.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 11-01-2021
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.1 07-04-2021 - Se agrega valor de la factura.
     *
     */
    public function listarFacturasAction()
    {
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();
        $intIdEmpresa   = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "";
        $strUsrCreacion = $objSession->get('user') ? $objSession->get('user') : "";
        $strIpCreacion  = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $serviceUtil    = $this->get('schema.Util');
        $emFinanciero   = $this->get('doctrine')->getManager('telconet_financiero');
        $emComercial    = $this->get('doctrine')->getManager('telconet');

        $strTipoFacturacion = 'MRC';
        $intCantidad = 0;
        try
        {
            $arrayBusqueda = array('strVendedor' => $strUsrCreacion);
            $arrayFacturas = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getFacturasManuales($arrayBusqueda);

            $objCaracNumer = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findOneBy(array('descripcionCaracteristica' => 'NUM_FACTURA_ACUMULADA',
                'estado' => 'Activo'));
            $objCaracValor = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findOneBy(array('descripcionCaracteristica' => 'VAL_FACTURA_ACUMULADA',
                'estado' => 'Activo'));
            if(!empty($arrayFacturas) && is_array($arrayFacturas))
            {
                foreach($arrayFacturas as $arrayFactura):
                    $strTipoFacturacion = 'MRC';
                    $objSolicitudDetCaracSub = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                        ->findOneBy(array('caracteristicaId' => $objCaracNumer->getId(),
                        'valor' => $arrayFactura['idDocumento'],
                        'estado' => 'Pendiente SubGerente'));

                    $objSolicitudDetCaracGer = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                        ->findOneBy(array('caracteristicaId' => $objCaracNumer->getId(),
                        'valor' => $arrayFactura['idDocumento'],
                        'estado' => 'Pendiente Gerente'));

                    if(!empty($objSolicitudDetCaracSub) || !empty($objSolicitudDetCaracGer))
                    {
                        continue;
                    }

                    $intCantidad +=1;
                    $objSolicitudDetCaracNum = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                        ->findOneBy(array('caracteristicaId' => $objCaracNumer->getId(),
                        'valor' => $arrayFactura['idDocumento'],
                        'estado' => 'Aprobada'));
                    if(is_object($objSolicitudDetCaracNum) && !empty($objSolicitudDetCaracNum))
                    {
                        $objSolicitudDetCaracVal = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                            ->findOneBy(array('caracteristicaId' => $objCaracValor->getId(),
                            'detalleSolicitudId' => $objSolicitudDetCaracNum->getdetalleSolicitudId(),    
                            'estado' => 'Aprobada'));
                        if(is_object($objSolicitudDetCaracVal) && !empty($objSolicitudDetCaracVal) && $objSolicitudDetCaracVal->getValor() == 'NRC')
                        {
                            $strTipoFacturacion = 'NRC';
                        }
                    }
                    $objPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                        ->findOneBy(array('login' => $arrayFactura['vendedor'], 'estado' => 'Activo'));
                    if(is_object($objPersona) && !empty($objPersona))
                    {
                        $strVendedor = $objPersona->getNombres() . ' ' . $objPersona->getApellidos();
                    }
                    else
                    {
                        throw new \Exception('Error al Obtener al vendedor');
                    }

                    $dateFechaConsumo = date_create($arrayFactura['feConsumo']);
                    $strFechaConsumo = date_format($dateFechaConsumo, 'd/m/y');
                    //armar array
                    $arrayTipoFact = array('strFactura' => $arrayFactura['idDocumento'],
                        'strTipo' => $strTipoFacturacion);
                    $arrayDatos[] = array('intNumero' => $intCantidad,
                        'strFactura'    => $arrayFactura['idDocumento'],
                        'strConsumo'    => $strFechaConsumo,
                        'strVendedor'   => $strVendedor,
                        'strLogin'      => $arrayFactura['login'],
                        'strCliente'    => $arrayFactura['cliente'],
                        'valor_total'   => "$  " . number_format($arrayFactura['valor_total'], 2),
                        'strTipoFac'    => $arrayTipoFact,
                        'strTipoFac2'   => $arrayTipoFact,
                        'strAcciones'   => $arrayTipoFact);
                endforeach;
            }
            if(empty($arrayDatos))
            {
                $arrayDatos = '';
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos', 'listarFacturas', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        $objResponse = new Response(json_encode(array('intTotal' => $intCantidad, 'data' => $arrayDatos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * Documentación para la función 'getMotivosAction'.
     *
     * Función que lista los motivos a ser seleccionados para la crear la solicitud.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 11-01-2021
     *
     */
    public function getMotivosAction()
    {
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();
        $intIdEmpresa   = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "";
        $strUsrCreacion = $objSession->get('user') ? $objSession->get('user') : "";
        $strIpCreacion  = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $arrayMotivos   = array();
        $emGeneral      = $this->get('doctrine')->getManager('telconet');
        $serviceUtil    = $this->get('schema.Util');

        try
        {
            $arrayListMotivos = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
                ->findMotivosPorModuloPorItemMenuPorAccion('admiSolicitudFacturaAcu', 'Solicitud Cambio de Facturas', 'index');
            if(is_array($arrayListMotivos) && !empty($arrayListMotivos))
            {
                foreach($arrayListMotivos as $objItem)
                {

                    $arrayMotivos[] = array('id' => $objItem->getId(),
                                            'nombre' => $objItem->getNombreMotivo());
                }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos', 'getMotivosAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        $objResponse = new Response(json_encode(array('motivos' => $arrayMotivos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * Documentación para la función 'getSolicitudFacturaAction'.
     *
     * Función que crea la solicitud para cambio en la comision de la facturación.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 11-01-2021
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 2.0 25-03-2021 - Modificamos la forma de generar solicitudes para vendedores sin jefe asignado. 
     */
    public function getSolicitudFacturaAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $strFactura         = $objRequest->get("strFactura") ? $objRequest->get("strFactura") : "";
        $strObservacionSoli = $objRequest->get("strObservacionSoli") ? $objRequest->get("strObservacionSoli") : "";
        $intIdMotivo        = $objRequest->get("intIdMotivoSoli") ? $objRequest->get("intIdMotivoSoli") : "";
        $strTipoFact        = $objRequest->get("strTipoFact") ? $objRequest->get("strTipoFact") : "";
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "";
        $intIdEmpresa       = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "";
        $strUsrCreacion     = $objSession->get('user') ? $objSession->get('user') : "";
        $strIpCreacion      = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $emComercial        = $this->get('doctrine')->getManager('telconet');
        $emFinanciero       = $this->get('doctrine')->getManager('telconet_financiero');
        $serviceSolicitud   = $this->get('comercial.Solicitudes');
        $serviceUtil        = $this->get('schema.Util');
        $emGeneral          = $this->get('doctrine')->getManager('telconet');
        $objResponse        = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $strEstadoSol       = "Pendiente Gerente";
        $strTipoSolicitud   = "SOLICITUD FACTURACION ACUMULADA";
        try
        {
            if(!empty($strFactura) && !empty($intIdMotivo) && !empty($strTipoFact) && !empty($strObservacionSoli))
            {
                $objMotivoId = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($intIdMotivo);
                if(!is_object($objMotivoId) && empty($objMotivoId))
                {
                    throw new \Exception('Error al Obtener el motivo, favor verificar.');
                }

                $objTipoSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')->findOneBy
                    (array('descripcionSolicitud' => $strTipoSolicitud,
                    'estado' => 'Activo'));
                if(!is_object($objTipoSolicitud) && empty($objTipoSolicitud))
                {
                    throw new \Exception('Error al Obtener el Tipo de Solicitud, favor verificar.');
                }

                $objCaracNumer = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array('descripcionCaracteristica' => 'NUM_FACTURA_ACUMULADA',
                    'estado' => 'Activo'));
                $objCaracValor = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array('descripcionCaracteristica' => 'VAL_FACTURA_ACUMULADA',
                    'estado' => 'Activo'));

                $objCaracVendedor = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array('descripcionCaracteristica' => 'VENDEDOR_FACTURA',
                    'estado' => 'Activo'));

                if(!is_object($objCaracNumer) && empty($objCaracNumer) && !is_object($objCaracValor) && 
                    empty($objCaracValor) && !is_object($objCaracVendedor) && empty($objCaracVendedor))
                {
                    throw new \Exception('Error al Obtener las caracteristicas, favor verificar.');
                }

                $arrayBusqueda = array('intIdFactura' => $strFactura);
                $arrayFacturas = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getFacturasManuales($arrayBusqueda);

                if(!empty($arrayFacturas) && is_array($arrayFacturas))
                {
                    $arrayFactura = $arrayFacturas[0];
                }
                else
                {
                    throw new \Exception('Error al Obtener la factura, favor verificar.');
                }

                $arrayResultadoCaracteristicas = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                                                       ->getCargosPersonas($arrayFactura['vendedor']);
                if(!empty($arrayResultadoCaracteristicas))
                {
                    $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
                    $arrayCargoVendedor = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ?
                        $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
                    if(!empty($arrayCargoVendedor))
                    {
                        foreach($arrayResultadoCaracteristicas as $strVendedor):
                            if($strVendedor !== '')
                            {
                                $strCargoVendedor = $strVendedor;
                            }
                        endforeach;
                    }
                    if($strCargoVendedor == 'VENDEDOR')
                    {
                        $arrayReportaEmpleado = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                                                    ->getCargosReportaEmp($arrayFactura['vendedor']);

                        if(!empty($arrayReportaEmpleado) && is_array($arrayReportaEmpleado))
                        {
                            $arrayLogin = $arrayReportaEmpleado[0];
                            $strLogin = $arrayLogin['LOGIN'];
                            $arrayCargoreportas = $emComercial->getRepository('schemaBundle:InfoPersona')
                                ->getCargosPersonas($strLogin);
                            if(!empty($arrayCargoreportas))
                            {
                                $arrayCargoreporta = $arrayCargoreportas[0];
                                $strCargoReportaVendedor = $arrayCargoreporta['STRCARGOPERSONAL'] ?
                                    $arrayCargoreporta['STRCARGOPERSONAL'] : 'Otros';
                                if($strCargoReportaVendedor == '' || $strCargoReportaVendedor == 'GERENTE_VENTAS')
                                {
                                    $strEstadoSol = 'Pendiente Gerente';
                                }
                                else
                                {
                                    $strEstadoSol = "Pendiente SubGerente";
                                }
                            }
                        }
                    }
                    elseif($strCargoVendedor == 'SUBGERENTE' || $strCargoVendedor == 'GERENTE_VENTAS')
                    {
                        $strEstadoSol = 'Pendiente Gerente';
                    }
                }

                $arrayParametros = array('strFactura'           => $strFactura,
                                         'objMotivo'            => $objMotivoId,
                                         'objTipoSolicitud'     => $objTipoSolicitud,
                                         'objcaracNumeroFac'    => $objCaracNumer,
                                         'objcaracTipoFac'      => $objCaracValor,
                                         'objCaracVendedor'     => $objCaracVendedor,
                                         'strVendedor'          => $arrayFactura['vendedor'],
                                         'strObservacion'       => $strObservacionSoli,
                                         'strTipoFac'           => $strTipoFact,
                                         'strEstado'            => $strEstadoSol,
                                         'strPrefijoEmpresa'    => $strPrefijoEmpresa,
                                         'intEmpresaId'         => $intIdEmpresa,
                                         'strUsrCreacion'       => $strUsrCreacion,
                                         'strIpCreacion'        => $strIpCreacion);

                $arrayRespuesta = $serviceSolicitud->creaSolicitudCambioFacturacion($arrayParametros);
                if($arrayRespuesta["status"] === "ERROR")
                {
                    throw new \Exception($arrayRespuesta["mensaje"]);
                }
                $objResponse->setContent("Solicitud Creada.");
            }
            else
            {
                throw new \Exception('Error al Obtener los datos, favor verificar.');
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos', 'getSolicitudFacturaAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
            $objResponse->setContent("Error al crear la Solicitud, " + $e->getMessage());
        }
        return $objResponse;
    }
    
    /**
     * Documentación para la función 'getGestionaSolicitudAction'.
     *
     * Función que gestiona los diferentes estados en la vida de una solicitud.
     *
     * @return Response - Lista de estados.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 27-01-2021
     *
     */
    public function getGestionaSolicitudAction()
    {
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $intIdSolicitud    = $objRequest->get("strSolicitud") ? $objRequest->get("strSolicitud") : "";
        $strObservacion    = $objRequest->get("strObservacionSoli") ? $objRequest->get("strObservacionSoli") : "Se cambia el estado a ";
        $strAccion         = $objRequest->get("strAccion") ? $objRequest->get("strAccion") : "";
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "";
        $intIdEmpresa      = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "";
        $intIdPersEmpRol   = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : "";
        $strUsrCreacion    = $objSession->get('user') ? $objSession->get('user') : "";
        $strIpCreacion     = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $serviceUtil       = $this->get('schema.Util');
        $objResponse       = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $emComercial       = $this->get('doctrine')->getManager('telconet');
        $serviceSolicitud  = $this->get('comercial.Solicitudes');
        $strMensaje        = 'Se Aprobo la Solicitud';
        try
        {
            if(!empty($intIdSolicitud) && !empty($strAccion))
            {
               if($strAccion == 'Aprobar')
               {
                   $objInfoDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolicitud);
                   
                   if(is_object($objInfoDetalleSolicitud) && !empty($objInfoDetalleSolicitud))
                   {
                       if($objInfoDetalleSolicitud->getEstado()=='Pendiente SubGerente')
                       {
                           $strEstado = 'Pendiente Gerente';
                       }
                       else
                       {
                           $strEstado = 'Aprobada';
                           
                           //verificamos si existe alguna solicitud con dicha factura
                           $objCaracNumer = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                        ->findOneBy(array('descripcionCaracteristica' => 'NUM_FACTURA_ACUMULADA',
                                                                        'estado' => 'Activo'));
                           $objSolicitudAnterior = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                                ->findOneBy(array('caracteristicaId' => $objCaracNumer->getId(),
                                                                                'detalleSolicitudId' => $objInfoDetalleSolicitud->getId()));
                           
                           if(is_object($objSolicitudAnterior) && !empty($objSolicitudAnterior))
                           {
                               $objSolicitudAnteriorAprob = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                                ->findOneBy(array('caracteristicaId' => $objCaracNumer->getId(),
                                                                                'valor' => $objSolicitudAnterior->getValor(),
                                                                                'estado'=> 'Aprobada'));
                               
                               if(is_object($objSolicitudAnteriorAprob) && !empty($objSolicitudAnteriorAprob))
                               {
                                    $arrayParametrosFactAnt = array('intIdSolicitud'  => $objSolicitudAnteriorAprob->getDetalleSolicitudId()->getId(),
                                        'strObservacion'    => 'Se Ingresa una nueva solicitud',
                                        'strEstado'         => 'Anulado',
                                        'strPrefijoEmpresa' => $strPrefijoEmpresa,
                                        'intEmpresaId'      => $intIdEmpresa,
                                        'strUsrCreacion'    => $strUsrCreacion,
                                        'strIpCreacion'     => $strIpCreacion);

                                    $arrayRespuestaFact = $serviceSolicitud->actualizarSolicitudFact($arrayParametrosFactAnt);
                                    if($arrayRespuestaFact["status"] === "ERROR")
                                    {
                                        throw new \Exception($arrayRespuesta["mensaje"]);
                                    }
                               }
                           }
                       }
                       $strObservacion .= $strEstado;
                   }
                   else
                   {
                       throw new \Exception('Error al Obtener la solicitud, favor verificar.');
                   }
               }
               else
               {
                   $strEstado  = 'Rechazado';
                   $strMensaje = 'Se Rechazo la Solicitud';
               }
               $arrayParametros = array('intIdSolicitud'    => $intIdSolicitud,
                                        'strObservacion'    => $strObservacion,
                                        'strEstado'         => $strEstado,
                                        'strPrefijoEmpresa' => $strPrefijoEmpresa,
                                        'intEmpresaId'      => $intIdEmpresa,
                                        'strUsrCreacion'    => $strUsrCreacion,
                                        'strIpCreacion'     => $strIpCreacion);

                $arrayRespuesta = $serviceSolicitud->actualizarSolicitudFact($arrayParametros);
                if($arrayRespuesta["status"] === "ERROR")
                {
                    throw new \Exception($arrayRespuesta["mensaje"]);
                }
                $objResponse->setContent($strMensaje);
            }
            else
            {
                throw new \Exception('Error al aprobar la solicitud, favor verificar.');
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos', 'getSolicitudFacturaAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
            $objResponse->setContent("Error al gestionar la Solicitud, " + $e->getMessage());
        }
        return $objResponse;
    }

    /**
     * Documentación para la función 'getEstadosAction'.
     *
     * Función que obtiene los estados de las solicitudes de proyecto.
     *
     * @return Response - Lista de estados.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 27-01-2021
     *
     */
    public function getEstadosAction()
    {
        try
        {
            $objRequest             = $this->getRequest();
            $objSession             = $objRequest->getSession();
            $intIdEmpresa           = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa'):"";
            $strUsrCreacion         = $objSession->get('user')      ? $objSession->get('user'):"";
            $strIpCreacion          = $objRequest->getClientIp()    ? $objRequest->getClientIp():'127.0.0.1';
            $arrayEstados           = array();
            $emGeneral              = $this->get('doctrine')->getManager('telconet');
            $serviceUtil            = $this->get('schema.Util');
            $arrayListEstados       = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('CAMBIO_FACTURA_COMISION', 
                                                      'COMERCIAL', 
                                                      '', 
                                                      'ESTADO_FACTURA', 
                                                      '', 
                                                      '', 
                                                      '', 
                                                      '', 
                                                      '', 
                                                      $intIdEmpresa);
            foreach($arrayListEstados as $arrayItem)
            {
                $arrayEstados[] = array('id'     => $arrayItem['valor1'], 
                                        'nombre' => $arrayItem['valor1']);
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos', 'getEstadosAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        $objResponse = new Response(json_encode(array('estados' => $arrayEstados)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
}
