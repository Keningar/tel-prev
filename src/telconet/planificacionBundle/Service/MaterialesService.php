<?php

namespace telconet\planificacionBundle\Service;

use Doctrine\ORM\EntityManager;

use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolMaterial;
use telconet\schemaBundle\Entity\InfoDetalleMaterial;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Clase que sirve para realizar transacciones con los materiales
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 29-08-2015
 */
class MaterialesService 
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $emFinanciero;
    private $emSeguridad;
    private $emNaf;
    private $emGeneral;
    private $container;
    private $servicioGeneral;
    private $mailer;
    private $templating;
    private $host;
    private $pathTelcos;
    private $pathParameters; 
    private $mailerSend;
	
    public function __construct(Container $container) {
        $this->container            = $container;
        $this->emSoporte            = $this->container->get('doctrine')->getManager('telconet_soporte');
        $this->emInfraestructura    = $this->container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSeguridad          = $this->container->get('doctrine')->getManager('telconet_seguridad');
        $this->emComercial          = $this->container->get('doctrine')->getManager('telconet');
        $this->emFinanciero         = $this->container->get('doctrine')->getManager('telconet_financiero');
        $this->emComunicacion       = $this->container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emGeneral            = $this->container->get('doctrine')->getManager('telconet_general');
        $this->emNaf                = $this->container->get('doctrine')->getManager('telconet_naf');
        $this->templating           = $container->get('templating');
        $this->host                 = $this->container->getParameter('host');
        $this->pathTelcos           = $this->container->getParameter('path_telcos');
        $this->pathParameters       = $this->container->getParameter('path_parameters');
        $this->servicioGeneral      = $this->container->get('tecnico.InfoServicioTecnico');
        $this->mailer               = $container->get('mailer');
        $this->mailerSend           = $container->getParameter('mailer_send');
    }
     
    /**
     * Funcion que sirve para finalizar los materiales
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 28-08-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 18-06-2016 Se agrega validación de bandera de envió de correo
     * 
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.2 22-05-2017 Si se registra materiales para la empresa TN no se debe alterar los estados de la solicitud de instalación
     *                         solo se debe registrar los materiales.
     * 
     * @author Wilmer Vera. <wvera@telconet.ec>
     * @version 1.3 09-10-2017 Se agregó la validación para que no se repita los materiales ingresados por solicitud.
     * @since 1.2
     * 
     * @author Ronny Morán. <rmoranc@telconet.ec>
     * @version 1.4 19-12-2018 Completando parametros de Fibra recibida para facturación.
     * @since 1.3
     * 
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.5 28-03-2019 - Se adiciona el parámetro "idDetalle" para realizar
     * cambio de logica al realizar finalizarMateriales, se borra lógica por cambio
     * de flujo de registro de materiales.
     * @since 1.4
     * 
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.6 12-06-2019 - Se adiciona else cuando no se ingresó materiales.
     * @since 1.5
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.7 01-07-2020 - Se adiciona consulta para saber si el producto Requiere Flujo.
     * @since 1.6
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.8 11-02-2022 - Se valida que la variable $vArticulo sea un objeto al registrar los materiales 
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.9 06-10-2022 - Se agrega validación si la última milla esta vacía no obtener el tipo medio.
     * 
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 1.10 22-02-2023 - Se agrega validación para productos EN.
     * 
     * @param array $arrayParametros
     */
    public function finalizarMateriales($arrayParametros)
    {
        $idDetalleSolicitud         = $arrayParametros['id_detalle_solicitud'];
        $intIdDetalle               = $arrayParametros['idDetalle'];
        $idResponsable              = $arrayParametros['id_responsable']; //idPersona
        $codEmpresa                 = $arrayParametros['id_empresa'];
        $prefijoEmpresa             = $arrayParametros['prefijo_empresa'];
        $jsonMateriales             = $arrayParametros['materiales'];
        $usrCreacion                = $arrayParametros['usrCreacion'];
        $ipCreacion                 = $arrayParametros['ipCreacion'];
        $prefijoEmpresaValida       = $prefijoEmpresa;
        $codEmpresaValida           = $codEmpresa;
        $codEmpresaNaf              = $codEmpresa;
        
        $boolUltimaMilla            = false;

        //para codigo empresa MD y EN, se usa el codigo de TN
        if($prefijoEmpresa == "MD" || $prefijoEmpresa == "EN")
        {
            $empresaTN      = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo("TN");
            $codEmpresaNaf  = $empresaTN->getId();
        }
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emSoporte->getConnection()->beginTransaction();
        $this->emNaf->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        
        
        try
        {
            $tipoSolicitud          = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                        ->findOneBy(array('descripcionSolicitud' => 'SOLICITUD MATERIALES EXCEDENTES',
                                                                          'estado'               => 'Activo'));
            
            $entityDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($idDetalleSolicitud);
            $infoServicio           = $entityDetalleSolicitud->getServicioId();
            $codEmpresaTmp          = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getEmpresaEquivalente($infoServicio->getId(), $prefijoEmpresa);
            
            $servicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($infoServicio->getId());
            
            $boolRequiereFlujo = false;
            if(is_object($infoServicio) && is_object($infoServicio->getProductoId()) && $codEmpresa == '10' )
            {
                $intProductoId   = $infoServicio->getProductoId()->getId();
                $objProducto     = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intProductoId);
                if($objProducto)
                {
                    $strDescripcionProducto = $objProducto->getDescripcionProducto();
                }
            
                //Consultamos si el producto requiere flujo ya que antes no lo tenia
                $arrayParametrosRequiereFlujo =   $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->getOne("REQUIERE_FLUJO", 
                                                                             "TECNICO", 
                                                                             "", 
                                                                             "", 
                                                                             $strDescripcionProducto, 
                                                                             "", 
                                                                             "",
                                                                             "",
                                                                             "",
                                                                             10
                                                                             );
                if(!is_array($arrayParametrosRequiereFlujo) && empty($arrayParametrosRequiereFlujo))
                {
                    $boolRequiereFlujo = false;
                }
                else
                {
                    $boolRequiereFlujo = true;
                }
            }
            
            $intIdUltimaMilla = $servicioTecnico->getUltimaMillaId();
            if (!$boolRequiereFlujo && !empty($intIdUltimaMilla))
            {
                $ultimaMilla     = $this->emInfraestructura ->getRepository('schemaBundle:AdmiTipoMedio')->find($servicioTecnico->getUltimaMillaId());
                $boolUltimaMilla = true;
            }
            
            if($codEmpresaTmp)
            {
                $codEmpresaValida       = $codEmpresaTmp['id'];
                $prefijoEmpresaValida   = $codEmpresaTmp['prefijo'];
            }
            
            if($entityDetalleSolicitud)
            {
                $totalMateriales = $jsonMateriales->total;
                $arrayMateriales = $jsonMateriales->materiales;
                
                if($totalMateriales > 0 && $arrayMateriales && count($arrayMateriales) > 0)
                {
                    $boolGuardo                 = false;
                    $mailMateriales             = false;

                    $mensajeResponse            = "";
                    $mensajeMail                = "";
                    $observacionMail            = "";

                    $materialesExcedentes       = array();
                    
                    //obtiene custodio
                    $tareasPlanificacion = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                                                ->generarArrayTareasAsignadas($this->emComercial, "", "", $idDetalleSolicitud);

                    //se asigna la tarea al operativo
                    foreach($tareasPlanificacion as $tareaPlanificacion)
                    {
                        if(!empty($tareaPlanificacion['id_asignacion']) && strpos($tareaPlanificacion['nombre_tarea'], "INSTALACION UM") != false)
                        {
                            if($idResponsable > 0)
                            {
                                $empleadoInst       = $this->emComercial->getRepository('schemaBundle:InfoPersona')->findOneById($idResponsable);
                                $infoAsignaciones   = $this->emSoporte->getRepository("schemaBundle:InfoDetalleAsignacion")
                                                                      ->findOneById($tareaPlanificacion['id_asignacion']);
                                
                                if($tareaPlanificacion['ref_id_asignado'])
                                {
                                    $infoAsignaciones->setRefAsignadoId($idResponsable);
                                    $infoAsignaciones->getRefAsignadoNombre(sprintf("%s", $empleadoInst));
                                }
                                else
                                {
                                    $infoAsignaciones->setAsignadoId($idResponsable);
                                    $infoAsignaciones->getAsignadoNombre(sprintf("%s", $empleadoInst));
                                }
                                $this->emSoporte->persist($infoAsignaciones);
                                $this->emSoporte->flush();
                            }
                            else
                            {
                                $empleadoInst = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                   ->findOneById(($tareaPlanificacion['ref_id_asignado']) ? 
                                                                  $tareaPlanificacion['ref_id_asignado'] : $tareaPlanificacion['id_asignado']);
                            }

                            $cedulaEmpleado = $empleadoInst->getIdentificacionCliente();
                            break;
                        }
                    }
                    //fin custodio
                    //se recorren los materiales de la plantilla
                    foreach($arrayMateriales as $material)
                    {
                        if($material->tipo_articulo == 'Fibra')
                        {
                            
                            $arrayParametroselect['tipoActividad'] = 'Instalacion';
                            $arrayParametroselect['cod_material']  = $material->cod_material ;
                            $arrayParametroselect['codEmpresa']    = $material->empresaId ;
                            
                            $arrayFibraSeleccionada           = $this->emSoporte->getRepository('schemaBundle:InfoDetalleMaterial')
                                                              ->obtenerCaracteristicasFibraSeleccionada($arrayParametroselect);

                                foreach($arrayFibraSeleccionada as $fibra)
                                {
                                    $material->costo_material        = "$ " .number_format($fibra['costo_material'], 2, '.', '');
                                    $material->precio_venta_material = "$ " .number_format($fibra['precio_venta_material'], 2, '.', '');
                                    $material->facturar              = $fibra['facturar'];
                                    
                                }    
                        }
                        
                        $codMaterial    = (isset($material->cod_material) ? $material->cod_material : "");

                        $entityDetalle  = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')->findOneById($intIdDetalle);
                        $vArticulo      = $this->emNaf->getRepository('schemaBundle:VArticulosEmpresas')
                                                      ->getOneArticulobyCodigo($codMaterial);
                        
                        //creamos los detalles de la solicitud material
                        $entityInfoDetalleSolMaterial  = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolMaterial')
                                                                     ->findOneBy([
                                                                            'detalleSolicitudId' => $idDetalleSolicitud,
                                                                            'materialCod'        => $codMaterial
                                                                            ]);
                            
                        //fin vista naf
                        if($entityDetalle && is_object($vArticulo))
                        {
                            if ($boolUltimaMilla)
                            {
                                if($ultimaMilla->getCodigoTipoMedio()=='FO' )
                                {
                                    $prev_costoMaterial         = (isset($material->costo_material) ? 
                                                                  trim(substr($material->costo_material, 1)) : 0);
                                    $costoMaterial              = (($prev_costoMaterial && count($prev_costoMaterial) > 0) ? 
                                                                  number_format($prev_costoMaterial, 2, '.', '') : 0.00);
                                    $prev_precioVentaMaterial   = (isset($material->precio_venta_material) ?
                                                                  trim(substr($material->precio_venta_material, 1)) : 0);
                                    $precioVentaMaterial        = (($prev_precioVentaMaterial && count($prev_precioVentaMaterial) > 0) ?
                                                                  number_format($prev_precioVentaMaterial, 2, '.', '') : 0.00);
                                }
                            
                                if($ultimaMilla->getCodigoTipoMedio()=='CO' ||  $ultimaMilla->getCodigoTipoMedio()=='RAD')
                                {
                                    $prev_costoMaterial         = $vArticulo->getCostoUnitario();
                                    $costoMaterial              = (($prev_costoMaterial && count($prev_costoMaterial) > 0) ? 
                                                                  number_format($prev_costoMaterial, 2, '.', '') : 0.00);
                                    $prev_precioVentaMaterial   = $vArticulo->getPrecioBase();
                                    $precioVentaMaterial        = (($prev_precioVentaMaterial && count($prev_precioVentaMaterial) > 0) ?
                                                                  number_format($prev_precioVentaMaterial, 2, '.', '') : 0.00);
                                }
                            }
                            
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
                            $siFacturar                 = (isset($material->facturar) ? $material->facturar : false);

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

                            //verificar la cantidad usada en la instalacion
                            if($cantidadUsada > 0)
                            {
                                if($cantidadFacturada > 0)
                                {
                                    $mailMateriales = true;
                                }
                                
                                $mensajeResponse    = $mensajeResponse . (isset($material->nombre_material) ? " - " . 
                                                      $material->nombre_material : " - Sin Nombre de Material") . " :";
                                $mensajeMail        = $mensajeMail . "Material: " . $material->nombre_material . " - " . 
                                                      (isset($material->cod_material) ? $material->cod_material : "") . " ";

                                //guardo los valores usados
                                $entityInfoDetalleSolMaterial = new InfoDetalleSolMaterial();
                                $entityInfoDetalleSolMaterial->setDetalleSolicitudId($entityDetalleSolicitud);
                                $entityInfoDetalleSolMaterial->setMaterialCod($codMaterial);
                                $entityInfoDetalleSolMaterial->setCostoMaterial($costoMaterial);
                                $entityInfoDetalleSolMaterial->setPrecioVentaMaterial($precioVentaMaterial);
                                $entityInfoDetalleSolMaterial->setCantidadEstimada($cantidadEstimada);
                                $entityInfoDetalleSolMaterial->setCantidadCliente($cantidadCliente);
                                $entityInfoDetalleSolMaterial->setCantidadUsada($cantidadUsada);
                                $entityInfoDetalleSolMaterial->setCantidadFacturada($cantidadFacturada);
                                $entityInfoDetalleSolMaterial->setValorCobrado($valorCobrado);
                                $entityInfoDetalleSolMaterial->setUsrCreacion($usrCreacion);
                                $entityInfoDetalleSolMaterial->setFeCreacion(new \DateTime('now'));
                                $entityInfoDetalleSolMaterial->setIpCreacion($ipCreacion);
                                $this->emComercial->persist($entityInfoDetalleSolMaterial);
                                $this->emComercial->flush();

                                //GUARDAR INFO DETALLE SOLICICITUD MATERIAL
                                $entityDetalleMaterial = new InfoDetalleMaterial();
                                $entityDetalleMaterial->setDetalleId($entityDetalle);
                                $entityDetalleMaterial->setMaterialCod($codMaterial);
                                $entityDetalleMaterial->setCostoMaterial($costoMaterial);
                                $entityDetalleMaterial->setPrecioVentaMaterial($precioVentaMaterial);
                                $entityDetalleMaterial->setCantidadNoFacturada($cantidadNoFacturada);
                                $entityDetalleMaterial->setCantidadFacturada($cantidadFacturada);
                                $entityDetalleMaterial->setValorCobrado($valorCobrado);
                                $entityDetalleMaterial->setIpCreacion($ipCreacion);
                                $entityDetalleMaterial->setFeCreacion(new \DateTime('now'));
                                $entityDetalleMaterial->setUsrCreacion($usrCreacion);

                                $this->emSoporte->persist($entityDetalleMaterial);
                                $this->emSoporte->flush();

                                //se recopila informacion para envio de correo
                                if($cantidadFacturada > 0 && ($vArticulo->getSubgrupo() != "MODEM" &&
                                                              $vArticulo->getSubgrupo() != "RADIO" && 
                                                              $vArticulo->getSubgrupo() != "DSLAM" && 
                                                              $vArticulo->getSubgrupo() != "UPS")
                                  )
                                {
                                    $excedenteMaterial = array();
                                    $excedenteMaterial['codigo']            = $codMaterial;
                                    $excedenteMaterial['nombre']            = (isset($material->nombre_material) ? " - " . 
                                                                                     $material->nombre_material : " - Sin Nombre de Material");
                                    $excedenteMaterial['cantidadEmpresa']   = $cantidadEmpresa;
                                    $excedenteMaterial['cantidadUsada']     = $cantidadUsada;
                                    $excedenteMaterial['cantidadCliente']   = $cantidadCliente;
                                    $excedenteMaterial['cantidadFacturada'] = $cantidadFacturada;
                                    $excedenteMaterial['precio']            = $precioVentaMaterial;
                                    $excedenteMaterial['valorCobrado']      = $valorCobrado;
                                    $materialesExcedentes[]                 = $excedenteMaterial;
                                    
                                    //se crea solicitud de excedentes de materiales para la facturacion
                                    $detalleSolicitudExcedente = new InfoDetalleSolicitud();
                                    $detalleSolicitudExcedente->setEstado("Aprobado");
                                    $detalleSolicitudExcedente->setTipoSolicitudId($tipoSolicitud);
                                    $detalleSolicitudExcedente->setServicioId($infoServicio);
                                    $detalleSolicitudExcedente->setUsrCreacion($usrCreacion);
                                    $detalleSolicitudExcedente->setFeCreacion(new \DateTime('now'));
                                    $detalleSolicitudExcedente->setObservacion("Se crea solicitud por excedentes de materiales");
                                    $this->emComercial->persist($detalleSolicitudExcedente);
                                    $this->emComercial->flush();
                                    
                                    //se crea historiales de la solicitud de excedentes de materiales por Pendiente y Aprobada
                                    $entityDetalleSolHist = new InfoDetalleSolHist();
                                    $entityDetalleSolHist->setDetalleSolicitudId($detalleSolicitudExcedente);
                                    $entityDetalleSolHist->setIpCreacion($ipCreacion);
                                    $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                                    $entityDetalleSolHist->setUsrCreacion($usrCreacion);
                                    $entityDetalleSolHist->setEstado('Pendiente');
                                    $this->emComercial->persist($entityDetalleSolHist);
                                    $this->emComercial->flush();
                                    
                                    $entityDetalleSolHist1 = new InfoDetalleSolHist();
                                    $entityDetalleSolHist1->setDetalleSolicitudId($detalleSolicitudExcedente);
                                    $entityDetalleSolHist1->setIpCreacion($ipCreacion);
                                    $entityDetalleSolHist1->setFeCreacion(new \DateTime('now'));
                                    $entityDetalleSolHist1->setUsrCreacion($usrCreacion);
                                    $entityDetalleSolHist1->setEstado('Aprobado');
                                    $this->emComercial->persist($entityDetalleSolHist1);
                                    $this->emComercial->flush();
                                }

                                //ACTUALIZA NAF
                                if($vArticulo && $vArticulo->getSubgrupo() != "MODEM")
                                {
                                    //ejecutar procedure para actualizar naf
                                    $idArticulo       = $vArticulo->getId();
                                    $strTipoArticulo  = 'MT';
                                    $strSerieCpe      = '';
                                    $pv_mensajeerror  = str_repeat(' ', 2000);
                                    $sql = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, "
                                           . ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, "
                                           . ":cantidad, :pv_mensajeerror); END;";
                                    $stmt = $this->emNaf->getConnection()->prepare($sql);
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
                    
                    //si los materiales exceden en cantidad, se envia notificacion
                    if($mailMateriales)
                    {
                        //------- COMUNICACIONES --- NOTIFICACIONES 
                        $mail = $this->templating->render('planificacionBundle:IngresarMateriales:notificacion.html.twig', 
                                                     array('detalleSolicitud'       => $entityDetalleSolicitud, 
                                                           'detalleSolicitudHist'   => null, 
                                                           'responsable'            => $empleadoInst, 
                                                           'motivo'                 => null, 
                                                           'materialesExcedentes'   => $materialesExcedentes));

                        $asunto = "Informe de Pre Facturas generadas de Materiales Excedentes del Login:" . $infoServicio->getPuntoId()->getLogin();

                        //DESTINATARIOS.... 
                        $formasContacto = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                               ->getContactosByLoginPersonaAndFormaContacto($infoServicio->getPuntoId()->getUsrVendedor(), 
                                                                                          'Correo Electronico');
                        $to = array();
                        $cc = array();
                        $to[] = 'notificaciones_telcos@telconet.ec';

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
                            ->setBody($mail, 'text/html')
                        ;
                        if($this->mailerSend == "true")
                        {
                            $this->mailer->send($message);
                        }
                    }

                    if($boolGuardo)
                    {
                        $strStatus  = 200;
                        $strMensaje = "Se registró materiales con las siguientes observaciones: <br> " . $mensajeResponse;
                    }
                    else
                    {
                        $strStatus  = 206;
                        $strMensaje = "Valores incorrectos. Los materiales ingresados a descontar deben ser mayores a 0";
                    }
                }
                else
                {
                    throw new \Exception("No existe ningun material asociado");
                }
            }
            else
            {
                throw new \Exception("No existe el detalle de Solicitud");
            }
        }
        catch(\Exception $e)
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            if ($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->getConnection()->rollback();
            }
            
            if ($this->emNaf->getConnection()->isTransactionActive())
            {
                $this->emNaf->getConnection()->rollback();
            }
            
            $strStatus             = "ERROR";
            $strMensaje            = $e->getMessage();
            $strRespuestaFinal     = array('status' => $strStatus, 'mensaje' => $strMensaje);
            return $strRespuestaFinal;
        }
        
        //*DECLARACION DE COMMITS*/
        if ($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }
        
        if ($this->emSoporte->getConnection()->isTransactionActive())
        {
            $this->emSoporte->getConnection()->commit();
        }
        
        if ($this->emNaf->getConnection()->isTransactionActive())
        {
            $this->emNaf->getConnection()->commit();
        }
        
        $this->emComercial->getConnection()->close();
        $this->emSoporte->getConnection()->close();
        $this->emNaf->getConnection()->close();
        //*----------------------------------------------------------------------*/
        
        //*RESPUESTA-------------------------------------------------------------*/
        $strRespuestaFinal = array('status' => $strStatus, 'mensaje' => $strMensaje);
        return $strRespuestaFinal;
        //*----------------------------------------------------------------------*/
    }
    




    /**
     * ingresarSeguimientoTarea - Funcion que sirve para ingresar el seguimiento de una tarea
     * 
     * @author Mario Ayerve <mayerve@telconet.ec>
     * @version 1.0 05-03-2021 - Se envía parámetro tipoAsignado al llamar a generarEnvioPlantilla
     * 
     * @param array $arrayParametros
     * @return array $respuestaFinal
     */
    public function ingresarSeguimientoMaterialesExcedentes($arrayParametros)
    {
        $intIdEmpresa              = $arrayParametros['idEmpresa'];
        $intIdDetalle              = $arrayParametros['idDetalle'];
        $strSeguimiento            = $arrayParametros['seguimiento'];
        $intIdPunto                = $arrayParametros['idPunto'];
        $strLogin                  = $arrayParametros['login'];
        $strVendedor               = $arrayParametros['vendedor'];
        $strProducto               = $arrayParametros['producto'];
        $strUsrCreacion            = $arrayParametros['usrCreacion'];
        $strIpCreacion             = $arrayParametros['ipCreacion'];
        $strPantallaDe             = $arrayParametros['pantallaDe'];
        

        $arrayParametrosHist["strCodEmpresa"]           = $intIdEmpresa;
        $arrayParametrosHist["strUsrCreacion"]          = $strUsrCreacion;
        $arrayParametrosHist["strIpCreacion"]           = $strIpCreacion;
        
        $arrayParametrosHist["strOpcion"]               = "Seguimiento";    

        try
        {
                /*********************************************************/
                //		SE INGRESA SEGUIMIENTO DE SOLICITU
                /*********************************************************/
                $arrayParametrosHist["strObservacion"]        = $strSeguimiento;
                $arrayParametrosHist["intDetalleId"]          = $intIdDetalle;
                $arrayParametrosHist["intPunto"]              = $intIdPunto;
                $arrayParametrosHist["strLogin"]              = $strLogin;
                $arrayParametrosHist["strProducto"]           = $strProducto;
                $arrayParametrosHist["intIdEmpresa"]          = $intIdEmpresa;

                if($strPantallaDe == "Solicitudes")
                {
                    $objParametroCargo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                               ->findOneBy(array("descripcion"=>'Cargo que autoriza excedente de material', 
                                                 "modulo"=>'PLANIFICACIÓN',
                                                 "estado"=>'Activo'));

                    $objCargoAutoriza = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->findOneBy(array("descripcion" => 'Cargo que recibirá solicitud de excedente de material', 
                                      "parametroId" => $objParametroCargo->getId(),
                                      "estado"      => 'Activo'));

                    $objDepartamento = $this->emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                               ->findOneBy(array("nombreDepartamento" =>$objCargoAutoriza->getValor2(),
                                                 "estado"             =>'Activo'));
                    $objRol   = $this->emGeneral->getRepository('schemaBundle:AdmiRol')
                              ->findOneBy(array("descripcionRol" => $objCargoAutoriza->getValor1()));
                    
                    $objEmpresaRol   = $this->emGeneral->getRepository('schemaBundle:InfoEmpresaRol')
                              ->findOneBy(array("rolId"      => $objRol->getId(),
                                                "empresaCod" => $intIdEmpresa,
                                                "estado"     => 'Activo'));
                    
                    $objPersonaEmpresaRol   = $this->emGeneral->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                               ->findOneBy(array("empresaRolId"   => $objEmpresaRol->getId(),
                                                 "departamentoId" => $objDepartamento->getId(),
                                                 "estado"         => 'Activo'));                                                            
                    
                    $arrayParametrosHist["strDestinatario"]          = $objPersonaEmpresaRol->getPersonaId()->getLogin();
                    $arrayParametrosHist["strPlantilla"]             = "NOTIEXCMATGTN";
                }
                else
                {
                    $objSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                    ->find($intIdDetalle);
                    if(is_object($objSolicitud) && !empty($objSolicitud))
                    {
                        $arrayParametrosHist["strDestiAsistente"] = $objSolicitud->getServicioId()->getUsrCreacion();
                    }
                    $arrayParametrosHist["strDestinatario"]          = $strVendedor;
                    $arrayParametrosHist["strPlantilla"]             = "NOTIEXCMATASE";
                }

                $arrayResp = $this->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                
                if($arrayResp && $arrayResp['strStatus']== 'OK')
                {
                    $this->envioCorreo($arrayParametrosHist);
                }
                else
                {
                    throw new \Exception("Ocurrio un error al ingresar el seguimiento - "
                    . $arrayResp['strMensaje']);
                }
            
        }
        catch(\Exception $e)
        {
            $strStatus             = "ERROR";
            $strMensaje            = $e->getMessage();
            $strRespuestaFinal     = array('status' => $strStatus, 'mensaje' => $strMensaje, 'success' => false);
            return $strRespuestaFinal;
        }

        //*RESPUESTA-------------------------------------------------------------*/
        return $arrayResp;
        //*----------------------------------------------------------------------*/
    }


    /**
     * ingresaHistorialYSeguimientoPorTarea - Funcion que ingresa el seguimiento y el historial de la solicitud
     *
     * @author Mario Ayerve <mayerve@telconet.ec>
     * @version 1.0 25-08-2017
     *
     *
     * @param array $arrayParametros [ strOpcion                opción que indica si se inserta un seguimiento o un historial de tarea
     *                                 strFeCreacion            fecha de creacion del registro
     *                                 intDetalleId             id detalle de la tarea
     *                                 strObservacion           observacion a ingresar
     *                                 intAsignadoId            id del asignado
     *                                 strMotivo                motivo del historial de la tarea
     *                                 strCodEmpresa            codigo de la empresa
     *                                 strUsrCreacion           usuario de creacion
     *                                 strEstadoActual          estado actual de la tarea
     *                                 strSeguimientoInterno    se registra si es un seguimiento interno
     *                                 intIdDepartamentoOrigen  departamento origen que creo el registro
     *                                 strIpCreacion            ip de creacion del registro
     *                                 strAccion                accion a realizar
     *                                 strEnviaDepartamento     parametro que indica si se envia o no el departamento origen ]
     *
     * @return array $arrayRespuesta [ objInfoDetalleHistorial  entidad de la tabla INFO_DETALLE_HISTORIAL
     *                                 objInfoTareaSeguimiento  entidad de la tabla INFO_TAREA_SEGUIMIENTO ]
     *
     */
    public function ingresaHistorialYSeguimientoPorTarea($arrayParametros)
    {
        $arrayRespuesta = array();
        $strOpcion                  = isset($arrayParametros["strOpcion"]) ? $arrayParametros["strOpcion"] : "";
        $intDetalleId               = $arrayParametros["intDetalleId"];
        $strObservacion             = isset($arrayParametros["strObservacion"]) ? $arrayParametros["strObservacion"] : "";
        $strUsrCreacion             = isset($arrayParametros["strUsrCreacion"]) ? $arrayParametros["strUsrCreacion"] : "";
        $strIpCreacion              = isset($arrayParametros["strIpCreacion"]) ? $arrayParametros["strIpCreacion"] : "";
        $strEstadoActual            = "Pendiente";

    try
    {
        if($strOpcion == "Seguimiento" || $boolHisSeg)
        {
            $this->emComercial->getConnection()->beginTransaction();
            $objInfoDetalleSolHist = new InfoDetalleSolHist();                    
            $objSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
            ->find($intDetalleId);


            $objInfoDetalleSolHist->setDetalleSolicitudId($objSolicitud);
            $objInfoDetalleSolHist->setObservacion($strObservacion);
            $objInfoDetalleSolHist->setEstado($strEstadoActual);

            $objInfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
            $objInfoDetalleSolHist->setUsrCreacion($strUsrCreacion);
            $objInfoDetalleSolHist->setIpCreacion($strIpCreacion);

            
            $this->emComercial->persist($objInfoDetalleSolHist);
            $this->emComercial->flush();
            $this->emComercial->commit();
            $this->emComercial->close();

            $strStatus  = "OK";
            $strMensaje = "El registro se ingreso correctamente!"; 
        }
    }
        catch(\Exception $e)
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
                $this->emComercial->close();
            }

            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage(); 
        }
        $arrayRespuesta = array("strStatus" => $strStatus, "strMensaje" => $strMensaje ); 
        return $arrayRespuesta;
    }


        /**
     * envioCorreo - envio de notificaciones para la solicitud
     *
     * @author Mario Ayerve <mayerve@telconet.ec>
     * @version 1.0 25-08-2017
     *
     *
     * @param array $arrayParametros [ idDetalle          codigo detalle solicitud
     *                                 idPunto            punto de la solicitud
     *                                 login              login de la solicitud
     *                                 vendedor           usuario del vendedor
     *                                 producto           producto brindado
     *                                 idEmpresa           codigo de empresa]
     *
     */
    public function envioCorreo($arrayParametros)
    {
        $strPlantilla      = $arrayParametros['strPlantilla'];
        $strDestinatario   = $arrayParametros['strDestinatario'];
        $strDestiAsistente = $arrayParametros['strDestiAsistente'];
        $strSeguimiento  = $arrayParametros["strObservacion"];
        $intIdDetalle    = $arrayParametros["intDetalleId"];
        $strLogin        = $arrayParametros["strLogin"];
        $strProducto     = $arrayParametros["strProducto"];
        $intIdEmpresa    = $arrayParametros["intIdEmpresa"];

        try
        {
            $serviceEnvioPlantilla  = $this->container->get('soporte.EnvioPlantilla'); 
            $strMail        = 'Se ingresó un nuevo seguimiento: '. $strSeguimiento;

            $arrayFormasContacto = $this->emComercial->getRepository('schemaBundle:InfoPersona')
            ->getContactosByLoginPersonaAndFormaContacto($strDestinatario,'Correo Electronico');
            $arrayFormasContactoAsis = $this->emComercial->getRepository('schemaBundle:InfoPersona')
            ->getContactosByLoginPersonaAndFormaContacto($strDestiAsistente,'Correo Electronico');
            
            /* Envío de Correo */
            $strAsunto = "Seguimiento de Solicitud de Excedente de Materiales # "
                         . $intIdDetalle ."| login:". $strLogin;
            
            if($arrayFormasContacto)
            {
                    foreach($arrayFormasContacto as $arrayformaContacto)
                    {
                            $arrayDestinatario[] = $arrayformaContacto['valor'];
                    }
            }
            if($arrayFormasContactoAsis)
            {
                    foreach($arrayFormasContactoAsis as $arrayformaContacto)
                    {
                            $arrayDestinatario[] = $arrayformaContacto['valor'];
                    }
            }
            $arrayParametrosMail = array(
                                        "login"                 => $strLogin,
                                        "producto"              => $strProducto,
                                        "mensaje"               => $strMail
                                        );

            $serviceEnvioPlantilla
                ->generarEnvioPlantilla(
                $strAsunto,
                $arrayDestinatario,
                $strPlantilla,
                $arrayParametrosMail,
                $intIdEmpresa,
                '',
                '',
                null,
                false,
                'notificaciones_telcos@telconet.ec'
            );
        }
        catch(\Exception $e)
        {
            $strContent = $e->getMessage();
            return $strContent;
        }
    }

}
