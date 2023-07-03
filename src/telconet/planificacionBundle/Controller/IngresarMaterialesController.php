<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\planificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroDet;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroImp;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleMaterial;
use telconet\schemaBundle\Entity\InfoDetalleSolMaterial;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\AdmiMotivo;
use telconet\schemaBundle\Entity\InfoServicioHistorial;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class IngresarMaterialesController extends Controller implements TokenAuthenticatedController
{ 
    /**
	* @Secure(roles="ROLE_142-1")
	*/
    public function indexAction()
    { 
    
    $rolesPermitidos = array();
	if (true === $this->get('security.context')->isGranted('ROLE_142-115'))
		{
	$rolesPermitidos[] = 'ROLE_142-115';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_142-116'))
		{
	$rolesPermitidos[] = 'ROLE_142-116';
	}
		
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("142", "1");

        return $this->render('planificacionBundle:IngresarMateriales:index.html.twig', array(
             'item' => $entityItemMenu,
             'rolesPermitidos' => $rolesPermitidos
        ));
    }
        
    /*
	 * Llena el grid de consulta.
	 */
	/**
	* @Secure(roles="ROLE_142-7")
	*/
    public function gridAction()
    {
	$respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');   
        
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $fechaDesdeAsig = explode('T',$peticion->query->get('fechaDesdeAsig'));
        $fechaHastaAsig = explode('T',$peticion->query->get('fechaHastaAsig'));
        
        $login2 = ($peticion->query->get('login2') ? $peticion->query->get('login2') : "");
        $descripcionPunto = ($peticion->query->get('descripcionPunto') ? $peticion->query->get('descripcionPunto') : "");
        $vendedor = ($peticion->query->get('vendedor') ? $peticion->query->get('vendedor') : "");
        $ciudad = ($peticion->query->get('ciudad') ? $peticion->query->get('ciudad') : "");
        $numOrdenServicio = ($peticion->query->get('numOrdenServicio') ? $peticion->query->get('numOrdenServicio') : "");
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $em = $this->getDoctrine()->getManager("telconet");
        $emSoporte = $this->getDoctrine()->getManager("telconet_soporte");
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoDetalleSolicitud')
            ->generarJsonIngresarMateriales($em, $start, $limit, $fechaDesdeAsig[0], $fechaHastaAsig[0], $login2, '',  
                                         $descripcionPunto, $vendedor, $numOrdenServicio, $ciudad,$emSoporte,$codEmpresa);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /*
	 * Llena el gridMaterialesUtilizados de consulta.
	 */
	/**
	* @Secure(roles="ROLE_142-115")
	*/
    /**
     * Documentación para el método 'gridMaterialesUtilizados'.
     *
     * Obtener listado de materiales utilizados
     * @return response.
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 07-11-2014
     */
    public function gridMaterialesUtilizadosAction()
    {
        ini_set('max_execution_time', 2000000);
        $respuesta              = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion               = $this->get('request');
        $id_solicitud           = $peticion->query->get('id_detalle_solicitud');
        $start                  = $peticion->query->get('start');
        $limit                  = $peticion->query->get('limit');
        $codEmpresa             = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $prefijoEmpresa         = $peticion->getSession()->get('prefijoEmpresa');
        $em                     = $this->getDoctrine()->getManager("telconet");
        $em_soporte             = $this->getDoctrine()->getManager("telconet_soporte");
        $em_naf                 = $this->getDoctrine()->getManager("telconet_naf");
        $em_infraestructura     = $this->getDoctrine()->getManager('telconet_infraestructura');
        $entity                 = $em->find('schemaBundle:InfoDetalleSolicitud', $id_solicitud);
        $InfoServicio           = $em->getRepository('schemaBundle:InfoServicio')->findOneById($entity->getServicioId());
        $InfoServicioTecnico    = $em->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($entity->getServicioId());
        $nombrePlan             = $InfoServicio->getPlanId()->getNombrePlan();
        $TipoMedio              = $em_infraestructura->getRepository('schemaBundle:AdmiTipoMedio')->find($InfoServicioTecnico->getUltimaMillaId());

        if($TipoMedio->getNombreTipoMedio() == "Cobre")
        {
            if(strrpos($nombrePlan, "ADSL"))
                $nombreProceso = "SOLICITAR NUEVO SERVICIO COBRE ADSL";
            else
                $nombreProceso = "SOLICITAR NUEVO SERVICIO COBRE VDSL";
        }else if($TipoMedio->getNombreTipoMedio() == "Fibra Optica")
            $nombreProceso = "SOLICITAR NUEVO SERVICIO FIBRA";
        else
            $nombreProceso = "SOLICITAR NUEVO SERVICIO RADIO";

        $entityProceso = $em_soporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso($nombreProceso);
        $codEmpresaTmp = $em->getRepository('schemaBundle:AdmiParametroDet')
            ->getEmpresaEquivalente($entity->getServicioId(), $prefijoEmpresa);
        if($codEmpresaTmp)
        {
            $codEmpresaValida = $codEmpresaTmp['id'];
        }
        else
        {
            $codEmpresaValida = $codEmpresa;
        }

        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:InfoDetalleMaterial')
            ->generarJsonIngresarMateriales($em, $em_naf, $start, $limit, $id_solicitud, $entityProceso->getId(), $codEmpresaValida);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /**
     * @Secure(roles="ROLE_142-116")
     */
    /**
     * Documentación para el método 'finalizarAjax'.
     *
     * Finaliza las ingreso de materiales
     * @return response.
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 07-11-2014
     *
     * @author Emmanuel Martillo<emartillo@telconet.ec>
     * @version 1.1 27-04-2023 - Se agrega validacion por empresa para que Ecuanet siga el flujo.
     *
     */
    public function finalizarAjaxAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion               = $this->get('request');
        $em                     = $this->getDoctrine()->getManager();
        $em_soporte             = $this->getDoctrine()->getManager("telconet_soporte");
        $em_naf                 = $this->getDoctrine()->getManager("telconet_naf");
        $em_financiero          = $this->getDoctrine()->getManager("telconet_financiero");
        $em_general             = $this->getDoctrine()->getManager("telconet_general");
        $id                     = $peticion->get('id');
        $id_responsable         = $peticion->get('id_responsable');
        $codEmpresa             = $peticion->getSession()->get('idEmpresa');
        $codEmpresaValida       = $codEmpresa;
        $prefijoEmpresa         = $peticion->getSession()->get('prefijoEmpresa');
        $prefijoEmpresaValida   = $prefijoEmpresa;
        $codEmpresaNaf          = $codEmpresa;

        if($prefijoEmpresa == "MD" || $prefijoEmpresa == "EN")
        {
            $empresaTN = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo("TN");
            $codEmpresaNaf = $empresaTN->getId();
        }

        $hostNaf    = $this->container->getParameter('database_host_naf');
        $portNaf    = $this->container->getParameter('database_port_naf');
        $sidNaf     = $this->container->getParameter('database_name_naf');
        $userNaf    = $this->container->getParameter('user_naf');
        $passwdNaf  = $this->container->getParameter('passwd_naf');


        $em->getConnection()->beginTransaction();
        $em_soporte->getConnection()->beginTransaction();
        $em_financiero->getConnection()->beginTransaction();

        try
        {
            $oficinaFacturacion = $em->getRepository('schemaBundle:InfoOficinaGrupo')->findOneByNombreOficina('TRANSTELCO - Quito');

            $entityDetalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($id);
            $infoServicio           = $em->getRepository('schemaBundle:InfoServicio')->findOneById($entityDetalleSolicitud->getServicioId());
            $admiProducto           = $em->getRepository('schemaBundle:AdmiProducto')->findOneByDescripcionProducto('MATERIALES');
            $infoTipoDocumento      = $em_financiero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                    ->findOneByNombreTipoDocumento('Factura');
            $admiImpuesto           = $em_general->getRepository('schemaBundle:AdmiImpuesto')
                                                  ->findOneBy(array('porcentajeImpuesto' => '12', 'tipoImpuesto' => 'IVA', 'estado' => 'Activo'));

            $puntoFacturacion       = "";
            $puntoFacturado         = "";
            $codEmpresaTmp          = $em->getRepository('schemaBundle:AdmiParametroDet')
                                         ->getEmpresaEquivalente($entityDetalleSolicitud->getServicioId(), $prefijoEmpresa);
            if($codEmpresaTmp)
            {
                $codEmpresaValida       = $codEmpresaTmp['id'];
                $prefijoEmpresaValida   = $codEmpresaTmp['prefijo'];
            }
            
            if($prefijoEmpresaValida == "TTCO")
            {
                $puntoFacturacion   = $infoServicio->getPuntoFacturacionId()->getId();
                $puntoFacturado     = $infoServicio->getPuntoId()->getId();
            }

            if($entityDetalleSolicitud)
            {
                $json_materiales  = json_decode($peticion->get('materiales'));
                $total_materiales = $json_materiales->total;
                $array_materiales = $json_materiales->materiales;

                if($total_materiales > 0 && $array_materiales && count($array_materiales) > 0)
                {
                    $boolGuardo                 = false;
                    $mailMateriales             = false;
                    $creaCabFacturaMateriales   = true;

                    $mensajeResponse            = "";
                    $mensajeMail                = "";
                    $observacionMail            = "";
                    $subtotalMateriales         = 0;
                    $ivaMateriales              = 0;
                    $totalMateriales            = 0;

                    $materialesExcedentes = array();
                    //obtiene custodio
                    $tareasPlanificacion = $this->getDoctrine()
                        ->getManager("telconet_soporte")
                        ->getRepository('schemaBundle:InfoDetalle')
                        ->generarArrayTareasAsignadas($em, "", "", $id);

                    foreach($tareasPlanificacion as $tareaPlanificacion)
                    {
                        if(strpos($tareaPlanificacion['nombre_tarea'], "INSTALACION UM") != false)
                        {
                            if($id_responsable > 0)
                            {
                                $empleadoInst = $em->getRepository('schemaBundle:InfoPersona')->findOneById($id_responsable);
                                $infoAsignaciones = $em_soporte->getRepository("schemaBundle:InfoDetalleAsignacion")
                                                               ->findOneById($tareaPlanificacion['id_asignacion']);
                                if($tareaPlanificacion['ref_id_asignado'])
                                {
                                    $infoAsignaciones->setRefAsignadoId($id_responsable);
                                    $infoAsignaciones->getRefAsignadoNombre(sprintf("%s", $empleadoInst));
                                }
                                else
                                {
                                    $infoAsignaciones->setAsignadoId($id_responsable);
                                    $infoAsignaciones->getAsignadoNombre(sprintf("%s", $empleadoInst));
                                }
                                $em_soporte->persist($infoAsignaciones);
                                $em_soporte->flush();
                            }
                            else
                            {
                                $empleadoInst = $em->getRepository('schemaBundle:InfoPersona')
                                                   ->findOneById(($tareaPlanificacion['ref_id_asignado']) ? 
                                                                  $tareaPlanificacion['ref_id_asignado'] : $tareaPlanificacion['id_asignado']);
                            }

                            $cedulaEmpleado = $empleadoInst->getIdentificacionCliente();
                            break;
                        }
                    }
                    //fin custodio			
                    foreach($array_materiales as $material)
                    {
                        $id_detalle_sol_material    = (isset($material->id_detalle_sol_material) ? $material->id_detalle_sol_material : 0);
                        $id_detalle                 = (isset($material->id_detalle) ? $material->id_detalle : 0);
                        $codMaterial                = (isset($material->cod_material) ? $material->cod_material : "");

                        $entityDetalle = $em_soporte->getRepository('schemaBundle:InfoDetalle')->findOneById($id_detalle);
                        $infoDetalleSolMaterial = new InfoDetalleSolMaterial();
                        $vArticulo = $em_naf->getRepository('schemaBundle:VArticulosEmpresas')->getOneArticulobyCodigo($codMaterial);
                        //fin vista naf
                        if($entityDetalle)
                        {
                            $prev_costoMaterial = $vArticulo->getCostoUnitario();
                            $costoMaterial              = (($prev_costoMaterial && count($prev_costoMaterial) > 0) ? 
                                                          number_format($prev_costoMaterial, 2, '.', '') : 0.00);
                            $prev_precioVentaMaterial   = $vArticulo->getPrecioBase();
                            $precioVentaMaterial        = (($prev_precioVentaMaterial && count($prev_precioVentaMaterial) > 0) ?
                                                          number_format($prev_precioVentaMaterial, 2, '.', '') : 0.00);
                            $cantidadEmpresa            = (isset($material->cantidad_empresa) ? 
                                                          ($material->cantidad_empresa ? $material->cantidad_empresa : 0) : 0);
                            $cantidadEstimada           = (isset($material->cantidad_estimada) ? 
                                                          ($material->cantidad_estimada ? $material->cantidad_estimada : 0) : 0);
                            $cantidadCliente            = (isset($material->cantidad_cliente) ? 
                                                          ($material->cantidad_cliente ? $material->cantidad_cliente : 0) : 0);
                            $cantidadUsada              = (isset($material->cantidad_usada) ? 
                                                          ($material->cantidad_usada ? $material->cantidad_usada : 0) : 0);
                            $cantidadFacturar           = (isset($material->cantidad_excedente) ? 
                                                          ($material->cantidad_excedente ? $material->cantidad_excedente : 0) : 0);
                            $siFacturar = (isset($material->facturar) ? $material->facturar : false);

                            $cantidadFacturada = ($cantidadFacturar > 0 && $siFacturar ? $cantidadFacturar : 0);

                            if($cantidadFacturada > $cantidadCliente)
                            {
                                $cantidadNoFacturada = $cantidadFacturada > $cantidadCliente;
                            }
                            if($cantidadCliente > $cantidadFacturada)
                            {
                                $cantidadNoFacturada = $cantidadCliente > $cantidadFacturada;
                            }
                            if($cantidadFacturada == $cantidadCliente)
                            {
                                $cantidadNoFacturada = $cantidadFacturada;
                            }

                            $valorCobrado = ($cantidadFacturada > 0 ? number_format(($precioVentaMaterial * $cantidadFacturada), 2, '.', '') : 0.00);

                            if($cantidadUsada > 0)
                            {
                                if($cantidadFacturada > 0)
                                {
                                    $mailMateriales = true;
                                }
                                $mensajeResponse = $mensajeResponse . (isset($material->nombre_material) ? " - " . 
                                                   $material->nombre_material : " - Sin Nombre de Material") . " :";
                                $mensajeMail = $mensajeMail . "Material: " . $material->nombre_material . " - " . 
                                               (isset($material->cod_material) ? $material->cod_material : "") . " ";

                                //guardo los valores usados
                                $infoDetalleSolMaterial->setDetalleSolicitudId($entityDetalleSolicitud);
                                $infoDetalleSolMaterial->setMaterialCod($codMaterial);
                                $infoDetalleSolMaterial->setCostoMaterial($costoMaterial);
                                $infoDetalleSolMaterial->setPrecioVentaMaterial($precioVentaMaterial);
                                $infoDetalleSolMaterial->setCantidadEstimada($cantidadEstimada);
                                $infoDetalleSolMaterial->setCantidadCliente($cantidadCliente);
                                $infoDetalleSolMaterial->setCantidadUsada($cantidadUsada);
                                $infoDetalleSolMaterial->setCantidadFacturada($cantidadFacturada);
                                $infoDetalleSolMaterial->setValorCobrado($valorCobrado);
                                $infoDetalleSolMaterial->setUsrCreacion($peticion->getSession()->get('user'));
                                $infoDetalleSolMaterial->setFeCreacion(new \DateTime('now'));
                                $infoDetalleSolMaterial->setIpCreacion($peticion->getClientIp());
                                $em->persist($infoDetalleSolMaterial);
                                $em->flush();

                                //GUARDAR INFO DETALLE SOLICICITUD MATERIAL
                                $entityDetalleMaterial = new InfoDetalleMaterial();
                                $entityDetalleMaterial->setDetalleId($entityDetalle);
                                $entityDetalleMaterial->setMaterialCod($codMaterial);
                                $entityDetalleMaterial->setCostoMaterial($costoMaterial);
                                $entityDetalleMaterial->setPrecioVentaMaterial($precioVentaMaterial);
                                $entityDetalleMaterial->setCantidadNoFacturada($cantidadNoFacturada);
                                $entityDetalleMaterial->setCantidadFacturada($cantidadFacturada);
                                $entityDetalleMaterial->setValorCobrado($valorCobrado);
                                $entityDetalleMaterial->setIpCreacion($peticion->getClientIp());
                                $entityDetalleMaterial->setFeCreacion(new \DateTime('now'));
                                $entityDetalleMaterial->setUsrCreacion($peticion->getSession()->get('user'));

                                $em_soporte->persist($entityDetalleMaterial);
                                $em_soporte->flush();

                                if($prefijoEmpresaValida == "TTCO" && $cantidadFacturada > 0 && 
                                                                   ($vArticulo->getSubgrupo() != "MODEM" &&
                                                                    $vArticulo->getSubgrupo() != "RADIO" && 
                                                                    $vArticulo->getSubgrupo() != "DSLAM" && 
                                                                    $vArticulo->getSubgrupo() != "UPS"))
                                {
                                    $excedenteMaterial = array();
                                    $excedenteMaterial['codigo'] = $codMaterial;
                                    $excedenteMaterial['nombre'] = (isset($material->nombre_material) ? " - " . 
                                                                   $material->nombre_material : " - Sin Nombre de Material");
                                    $excedenteMaterial['cantidadEmpresa'] = $cantidadEmpresa;
                                    $excedenteMaterial['cantidadUsada'] = $cantidadUsada;
                                    $excedenteMaterial['cantidadCliente'] = $cantidadCliente;
                                    $excedenteMaterial['cantidadFacturada'] = $cantidadFacturada;
                                    $excedenteMaterial['precio'] = $precioVentaMaterial;
                                    $excedenteMaterial['valorCobrado'] = $valorCobrado;
                                    $materialesExcedentes[] = $excedenteMaterial;

                                    //facturacion 
                                    //creacion cabecera factura
                                    if($creaCabFacturaMateriales)
                                    {
                                        $creaCabFacturaMateriales = false;

                                        $infoDocumentoFinancieroCab = new InfoDocumentoFinancieroCab();
                                        $infoDocumentoFinancieroCab->setOficinaId($oficinaFacturacion->getId());
                                        $infoDocumentoFinancieroCab->setPuntoId($puntoFacturacion);
                                        $infoDocumentoFinancieroCab->setTipoDocumentoId($infoTipoDocumento);
                                        $infoDocumentoFinancieroCab->setEntregoRetencionFte('N');
                                        $infoDocumentoFinancieroCab->setEstadoImpresionFact('Pendiente');
                                        $infoDocumentoFinancieroCab->setEsAutomatica('S');
                                        $infoDocumentoFinancieroCab->setProrrateo('N');
                                        $infoDocumentoFinancieroCab->setReactivacion('N');
                                        $infoDocumentoFinancieroCab->setRecurrente('N');
                                        $infoDocumentoFinancieroCab->setComisiona('N');
                                        $infoDocumentoFinancieroCab->setObservacion("Factura de Materiales del login: " . $infoServicio->getPuntoId()
                                                                                                                                        ->getLogin());
                                        $infoDocumentoFinancieroCab->setFeCreacion(new \DateTime('now'));
                                        $infoDocumentoFinancieroCab->setFeEmision(new \DateTime('now'));
                                        $infoDocumentoFinancieroCab->setUsrCreacion($peticion->getSession()->get('user'));

                                        $em_financiero->persist($infoDocumentoFinancieroCab);
                                        $em_financiero->flush();

                                        $infoDocumentoHistorial = new InfoDocumentoHistorial();
                                        $infoDocumentoHistorial->setDocumentoId($infoDocumentoFinancieroCab);
                                        $infoDocumentoHistorial->setFeCreacion(new \DateTime('now'));
                                        $infoDocumentoHistorial->setUsrCreacion($peticion->getSession()->get('user'));
                                        $infoDocumentoHistorial->setEstado('Pendiente');

                                        $em_financiero->persist($infoDocumentoHistorial);
                                        $em_financiero->flush();
                                    }

                                    //creacion detalle factura
                                    $infoDocumentoFinancieroDet = new InfoDocumentoFinancieroDet();
                                    $infoDocumentoFinancieroDet->setDocumentoId($infoDocumentoFinancieroCab);
                                    $infoDocumentoFinancieroDet->setPuntoId($puntoFacturado);
                                    $infoDocumentoFinancieroDet->setCantidad($cantidadFacturada);
                                    $infoDocumentoFinancieroDet->setPrecioVentaFacproDetalle($precioVentaMaterial);
                                    $infoDocumentoFinancieroDet->setValorFacproDetalle($precioVentaMaterial);
                                    $infoDocumentoFinancieroDet->setCostoFacproDetalle($precioVentaMaterial);
                                    $infoDocumentoFinancieroDet->setEmpresaId($codEmpresa);
                                    $infoDocumentoFinancieroDet->setOficinaId($oficinaFacturacion->getId());
                                    $infoDocumentoFinancieroDet->setProductoId($admiProducto->getId());
                                    $infoDocumentoFinancieroDet->setObservacionesFacturaDetalle($excedenteMaterial['nombre']);
                                    $infoDocumentoFinancieroDet->setFeCreacion(new \DateTime('now'));
                                    $infoDocumentoFinancieroDet->setUsrCreacion($peticion->getSession()->get('user'));

                                    $em_financiero->persist($infoDocumentoFinancieroDet);
                                    $em_financiero->flush();

                                    //creacion del impuesto del detalle de la factura
                                    $infoDocumentoFinancieroImp = new InfoDocumentoFinancieroImp();
                                    $infoDocumentoFinancieroImp->setDetalleDocId($infoDocumentoFinancieroDet->getId());
                                    $infoDocumentoFinancieroImp->setImpuestoId($admiImpuesto->getId());
                                    $infoDocumentoFinancieroImp->setPorcentaje(12);
                                    $infoDocumentoFinancieroImp->setFeCreacion(new \DateTime('now'));
                                    $infoDocumentoFinancieroImp->setUsrCreacion($peticion->getSession()->get('user'));

                                    $em_financiero->persist($infoDocumentoFinancieroImp);
                                    $em_financiero->flush();


                                    //subtotal de la factura
                                    $subtotalMateriales = $subtotalMateriales + $valorCobrado;
                                }

                                //ACTUALIZA NAF
                                if($vArticulo && $vArticulo->getSubgrupo() != "MODEM")
                                {
                                    
                                    
                                    $idArticulo       = $vArticulo->getId();
                                    $strTipoArticulo  = 'MT';
                                    $strSerieCpe      = '';
                                    $pv_mensajeerror  = str_repeat(' ', 2000);
                                    $sql = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, "
                                           . ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, "
                                           . ":cantidad, :pv_mensajeerror); END;";
                                    $stmt = $em_naf->getConnection()->prepare($sql);
                                    $stmt->bindParam('codigoEmpresaNaf', $codEmpresaNaf);
                                    $stmt->bindParam('codigoArticulo', $idArticulo);
                                    $stmt->bindParam('tipoArticulo', $strTipoArticulo);
                                    $stmt->bindParam('identificacionCliente', $cedulaEmpleado);
                                    $stmt->bindParam('serieCpe', $strSerieCpe);
                                    $stmt->bindParam('cantidad', intval($cantidadUsada));
                                    $stmt->bindParam('pv_mensajeerror', $pv_mensajeerror);
                                    $stmt->execute();
                                    if(strlen(trim($pv_mensajeerror)) > 0)
                                    {
                                        $mensajeResponse = $mensajeResponse . " Actualizo Registro pero no Naf, " . $pv_mensajeerror . ".<br>";
                                        $observacionMail = " Actualizo Registro pero no Naf, " . $pv_mensajeerror . ".";
                                    }
                                    else
                                    {
                                        $mensajeResponse = $mensajeResponse . " Actualizo Registro y Naf.<br>";
                                        $observacionMail = " Actualizo Registro y Naf.";
                                    }
                                }
                                else
                                {
                                    if($vArticulo->getSubgrupo() == "MODEM")
                                    {
                                        $mensajeResponse = $mensajeResponse . " Actualizo Registro y no Naf ,porque es Activo y no Material.<br>";
                                        $observacionMail = " Actualizo Registro y no Naf porque es Activo y no Material.";
                                    }
                                    else
                                    {
                                        $mensajeResponse = $mensajeResponse . 
                                                           " Actualizo Registro pero no Naf, no existe material en Articulos Instalacion.<br>";
                                        $observacionMail = " Actualizo Registro pero no Naf, no existe material en Articulos Instalacion.";
                                    }
                                }

                                $boolGuardo = true;
                                $mensajeMail = $mensajeMail . "Cant. Empresa:" . $cantidadEmpresa . " Cant. Utilizada:" . $cantidadUsada . 
                                               " Cant. Facturar:" . $cantidadFacturada . " Observacion: " . $observacionMail . "        ";
                            }
                        }
                    }
                    
                    if($mailMateriales)
                    {


                        $subtotalMateriales = round($subtotalMateriales, 2);
                        $ivaMateriales = round(($subtotalMateriales * 12) / 100, 2);
                        $valorTotal = $subtotalMateriales + $ivaMateriales;
                        //calculo final de Factura de Materiales
                        $infoDocumentoFinancieroCab->setSubtotal($subtotalMateriales);
                        $infoDocumentoFinancieroCab->setSubtotalCeroImpuesto($subtotalMateriales);
                        $infoDocumentoFinancieroCab->setSubtotalConImpuesto($ivaMateriales);
                        $infoDocumentoFinancieroCab->setValorTotal($valorTotal);

                        $em_financiero->persist($infoDocumentoFinancieroCab);
                        $em_financiero->flush();

                        //------- COMUNICACIONES --- NOTIFICACIONES 
                        $entityServicio = $em->getRepository('schemaBundle:InfoServicio')->findOneById($entityDetalleSolicitud->getServicioId());
                        $mensaje = $this->renderView('planificacionBundle:IngresarMateriales:notificacion.html.twig', 
                                                     array('detalleSolicitud' => $entityDetalleSolicitud, 
                                                           'detalleSolicitudHist' => null, 
                                                           'responsable' => $empleadoInst, 
                                                           'motivo' => null, 
                                                           'materialesExcedentes' => $materialesExcedentes));

                        $asunto = "Informe de Pre Facturas generadas de Materiales Excedentes del Login:" . $entityServicio->getPuntoId()->getLogin();

                        //DESTINATARIOS.... 
                        $formasContacto = $em->getRepository('schemaBundle:InfoPersona')
                                             ->getContactosByLoginPersonaAndFormaContacto($entityServicio->getPuntoId()->getUsrVendedor(), 
                                                                                          'Correo Electronico');
                        $to = array();
                        $cc = array();
                        $cc[] = 'notificaciones_telcos@telconet.ec';

                        if($prefijoEmpresa == "TTCO")
                        {
                            $to[] = 'facturacionycobranzas@trans-telco.com';
                            $to[] = 'rortega@trans-telco.com';
                        }
                        else if($prefijoEmpresa == "MD" || $prefijoEmpresa == "EN")
                        {
                            $to[] = 'notificaciones_telcos@telconet.ec';
                        }

                        if($formasContacto)
                        {
                            foreach($formasContacto as $formaContacto)
                            {
                                $to[] = $formaContacto['valor'];
                            }
                        }
                        //ENVIO DE MAIL
                        $message = \Swift_Message::newInstance()
                            ->setSubject($asunto)
                            ->setFrom('notificaciones_telcos@telconet.ec')
                            ->setTo($to)
                            ->setCc($cc)
                            ->setBody($mensaje, 'text/html')
                        ;

                        $this->get('mailer')->send($message);
                    }

                    if($boolGuardo)
                    {
                        $entityServicioHistorial = new InfoServicioHistorial();
                        $entityServicioHistorial->setServicioId($infoServicio);
                        $entityServicioHistorial->setIpCreacion($peticion->getClientIp());
                        $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $entityServicioHistorial->setUsrCreacion($peticion->getSession()->get('user'));
                        $entityServicioHistorial->setEstado($infoServicio->getEstado());
                        $entityServicioHistorial->setObservacion("Se finalizo la Instalacion");
                        $em->persist($entityServicioHistorial);
                        $em->flush();

                        $entityDetalleSolicitud->setEstado("Finalizada");
                        $em->persist($entityDetalleSolicitud);
                        $em->flush();

                        //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $lastDetalleSolhist = $em->getRepository('schemaBundle:InfoDetalleSolHist')
                                                 ->findOneDetalleSolicitudHistorial($id, 'Planificada');

                        $entityDetalleSolHist = new InfoDetalleSolHist();
                        $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                        if($lastDetalleSolhist)
                        {
                            $entityDetalleSolHist->setFeIniPlan($lastDetalleSolhist->getFeIniPlan());
                            $entityDetalleSolHist->setFeFinPlan($lastDetalleSolhist->getFeFinPlan());
                            $entityDetalleSolHist->setObservacion($lastDetalleSolhist->getObservacion());
                        }
                        $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
                        $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                        $entityDetalleSolHist->setEstado('Finalizada');

                        $em->persist($entityDetalleSolHist);
                        $em->flush();

                        //------- COMUNICACIONES --- NOTIFICACIONES 
                        $entityServicio = $em->getRepository('schemaBundle:InfoServicio')->findOneById($entityDetalleSolicitud->getServicioId());
                        $mensaje = $this->renderView('planificacionBundle:Coordinar:notificacion.html.twig', 
                                                     array('detalleSolicitud' => $entityDetalleSolicitud, 
                                                           'detalleSolicitudHist' => null, 'motivo' => null));

                        $asunto = "Solicitud de Instalacion Finalizada #" . $entityDetalleSolicitud->getId();

                        //DESTINATARIOS.... 
                        $formasContacto = $em->getRepository('schemaBundle:InfoPersona')
                                             ->getContactosByLoginPersonaAndFormaContacto($entityServicio->getPuntoId()->getUsrVendedor(), 
                                                                                          'Correo Electronico');
                        $to = array();
                        $cc = array();
                        $cc[] = 'notificaciones_telcos@telconet.ec';

                        if($prefijoEmpresa == "TTCO")
                        {
                            $to[] = 'rortega@trans-telco.com';
                            $cc[] = 'sac@trans-telco.com';
                        }
                        else if($prefijoEmpresa == "MD" || $prefijoEmpresa == "EN")
                        {
                            $to[] = 'notificaciones_telcos@telconet.ec';
                        }

                        if($formasContacto)
                        {
                            foreach($formasContacto as $formaContacto)
                            {
                                $to[] = $formaContacto['valor'];
                            }
                        }

                        //ENVIO DE MAIL
                        $message = \Swift_Message::newInstance()
                            ->setSubject($asunto)
                            ->setFrom('notificaciones_telcos@telconet.ec')
                            ->setTo($to)
                            ->setCc($cc)
                            ->setBody($mensaje, 'text/html')
                        ;

                        $this->get('mailer')->send($message);

                        $em->getConnection()->commit();
                        $em_soporte->getConnection()->commit();
                        $em_financiero->getConnection()->commit();
                        $respuesta->setContent("Se finalizo la instalacion, con las siguientes observaciones: <br> " . $mensajeResponse);
                    }
                    else
                    {
                        $em->getConnection()->rollback();
                        $em_soporte->getConnection()->rollback();
                        $em_financiero->getConnection()->rollback();
                        $respuesta->setContent("No se finalizo la instalacion");
                    }
                }
                else
                {
                    $em->getConnection()->rollback();
                    $em_soporte->getConnection()->rollback();
                    $em_financiero->getConnection()->rollback();
                    $respuesta->setContent("No existe ningun material asociado");
                }
            }
            else
            {
                $em->getConnection()->rollback();
                $em_soporte->getConnection()->rollback();
                $em_financiero->getConnection()->rollback();
                $respuesta->setContent("No existe el detalle de solicitud");
            }
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            $em_soporte->getConnection()->rollback();
            $em_financiero->getConnection()->rollback();

            $mensajeError = "Error: " . $e->getMessage();
            error_log($mensajeError);
            $respuesta->setContent($mensajeError);
        }

        return $respuesta;
    }

}