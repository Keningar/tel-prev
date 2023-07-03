<?php

namespace telconet\tecnicoBundle\Service;

use \PHPExcel;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Cell;
use \PHPExcel_IOFactory;
use \PHPExcel_Settings;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_Fill;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;

use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\schemaBundle\Entity\AdmiTipoDocumento;
use telconet\schemaBundle\Entity\AdmiCuadrillaHistorial;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoElementoTrazabilidad;
use telconet\schemaBundle\Entity\InfoProcesoMasivoCab;
use telconet\schemaBundle\Entity\InfoProcesoMasivoDet;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoServicioTecnico;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoSubred;

/**
 * Clase InfoElementoService
 *
 * Clase que maneja funcionales necesarias para la Entidad de InfoElemento
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 06-11-2015
 * 
 * @author Daniel Reyes <efranco@telconet.ec>
 * @version 1.1 10-06-2022 - Se agregan variables para los parametros de middleware
 * 
 */    
class InfoElementoService 
{
    const DETALLE_ASOCIADO_ELEMENTO_VEHICULO    = 'CUADRILLA';
    
    const DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO = 'ASIGNACION_VEHICULAR_FECHA_INICIO_CUADRILLA';
    const DETALLE_ASIGNACION_VEHICULAR_FECHA_FIN    = 'ASIGNACION_VEHICULAR_FECHA_FIN_CUADRILLA';

    const DETALLE_ASIGNACION_VEHICULAR_HORA_INICIO	= 'ASIGNACION_VEHICULAR_HORA_INICIO_CUADRILLA';
    const DETALLE_ASIGNACION_VEHICULAR_HORA_FIN		= 'ASIGNACION_VEHICULAR_HORA_FIN_CUADRILLA';
    const DETALLE_ASIGNACION_VEHICULAR_ID_SOL_PRED      = 'ASIGNACION_VEHICULAR_ID_SOL_PREDEF_CUADRILLA';

    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER             = 'ASIGNACION_PROVISIONAL_CHOFER';
    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_INICIO	= 'ASIGNACION_PROVISIONAL_CHOFER_FECHA_INICIO';
    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_FIN	= 'ASIGNACION_PROVISIONAL_CHOFER_FECHA_FIN';
    
    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_ZONA          = 'ASIGNACION_PROVISIONAL_CHOFER_ZONA';
    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_TAREA          = 'ASIGNACION_PROVISIONAL_CHOFER_TAREA';
    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_DEPARTAMENTO          = 'ASIGNACION_PROVISIONAL_CHOFER_DEPARTAMENTO';

    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_INICIO     = 'ASIGNACION_PROVISIONAL_CHOFER_HORA_INICIO';
    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_FIN        = 'ASIGNACION_PROVISIONAL_CHOFER_HORA_FIN'; 
    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_MOTIVO  = 'ASIGNACION_PROVISIONAL_CHOFER_MOTIVO';
    
    const DESCRIPCION_TIPO_SOLICITUD_CHOFER_PROVISIONAL = "SOLICITUD CHOFER PROVISIONAL";
    
    const NOMBRE_CARACTERISTICA_ZONA_CHOFER_PROV        = 'ZONA_ASIGNACION_PROVISIONAL_CHOFER';
    const NOMBRE_CARACTERISTICA_TAREA_CHOFER_PROV       = 'TAREA_ASIGNACION_PROVISIONAL_CHOFER';
    const NOMBRE_CARACTERISTICA_DEPARTAMENTO_CHOFER_PROV= 'DEPARTAMENTO_ASIGNACION_PROVISIONAL_CHOFER';
    const DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PRED           = 'ASIGNACION_PROVISIONAL_ID_SOLICITUD_PREDEF';
    const DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PROV           = 'ASIGNACION_PROVISIONAL_ID_SOLICITUD_PROV'; 
    
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emInfraestructura;

    private $emNaf;

    private $emComunicacion;

    private $emcom;

    private $emSoporte;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emGeneral;
    private $path_telcos;
    private $fileRoot;

    private $utilService;

    private $infoServicioTecnicoService;
    
    private $objSession;
    
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;
    
    private $serviceSolucion;

    private $rdaEjecutaComando;
    private $rdaEjecutaConfiguracion;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->container            = $container;
        $this->emInfraestructura    = $container->get('doctrine.orm.telconet_infraestructura_entity_manager');
        $this->emComunicacion       = $container->get('doctrine.orm.telconet_comunicacion_entity_manager');
        $this->emSoporte            = $container->get('doctrine.orm.telconet_soporte_entity_manager');
        $this->emGeneral            = $container->get('doctrine.orm.telconet_general_entity_manager');
        $this->emcom                = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emNaf                = $container->get('doctrine')->getManager('telconet_naf');
        $this->emSoporte            = $container->get('doctrine')->getManager("telconet_soporte");
        $this->path_telcos          = $container->getParameter('path_telcos');
        $this->fileRoot             = $container->getParameter('ruta_upload_documentos');
        $this->utilService          = $container->get('schema.Util');
        $this->infoServicioTecnicoService  = $container->get('tecnico.infoserviciotecnico');
        $this->objSession           = $container->get('session');
        $this->strUrsrInfraest      = $container->getParameter('user_infraestructura');
        $this->strPassInfraest      = $container->getParameter('passwd_infraestructura');
        $this->strDnsInfraest       = $container->getParameter('database_dsn');
        $this->serviceSolucion      = $container->get('comercial.InfoSolucion');
        $this->restClient           = $container->get('schema.RestClient');
        $this->rdaEjecutaComando       = $container->getParameter('ws_rda_ejecuta_scripts');
        $this->rdaEjecutaConfiguracion = $container->getParameter('ws_rda_ejecuta_config');
    }
    
    
    /**
     * Documentación para el método 'getAlertasMonitoreoUPS'.
     *
     * Muestra el listado de todas las alertas de los UPS que se deben monitorear.
     *
     * @param array $arrayParametros ['intInicio', 'intLimite', 'criterios' => array( 'strNombreNodo' , 'strIpsUps', 'strMarca', 'strRegion', 
     *                                                                                'strProvincia', 'strCiudad', 'arrayEstado', 'arraySeveridad' )]
     * 
     * @return array $arrayResultados ['registros', 'total']
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 23-02-2016
     */
    public function getAlertasMonitoreoUPS($arrayParametros)
    {
        $arrayDispositivos = array();
        $arrayResultados   = $this->emInfraestructura->getRepository('schemaBundle:VistaMonitoreoUps')
                                                     ->getElementosMonitoreoUpsByCriterios($arrayParametros);

        if( $arrayResultados['registros'] )
        {
            foreach( $arrayResultados['registros'] as $objElementoUps )
            {
                $arrayItem                        = array();
                $arrayItem['id']                  = $objElementoUps->getId();
                $arrayItem['idUps']               = $objElementoUps->getIdUps();
                $arrayItem['nombreUps']           = $objElementoUps->getNombreUps();
                $arrayItem['nombreNodo']          = $objElementoUps->getNombreNodo();
                $arrayItem['ipUps']               = $objElementoUps->getIpUps();
                $arrayItem['tipo']                = $objElementoUps->getTipo();
                $arrayItem['generador']           = $objElementoUps->getGenerador();
                $arrayItem['direccion']           = $objElementoUps->getDireccion();
                $arrayItem['latitud']             = $objElementoUps->getLatitud();
                $arrayItem['longitud']            = $objElementoUps->getLongitud();
                $arrayItem['region']              = $objElementoUps->getRegion();
                $arrayItem['provincia']           = $objElementoUps->getProvincia();
                $arrayItem['ciudad']              = $objElementoUps->getCiudad();
                $arrayItem['severidad']           = $objElementoUps->getSeveridad();
                $arrayItem['descripcionAlerta']   = $objElementoUps->getDescripcionAlerta();
                $arrayItem['valor']               = $objElementoUps->getValor();
                $arrayItem['estadoAlerta']        = $objElementoUps->getEstadoAlerta();
                $arrayItem['asignadoTarea']       = 'N';
                $arrayItem['idDetalleTarea']      = 0;
                $arrayItem['fechaModificacion']   = $objElementoUps->getFechaModificacion() 
                                                    ? $objElementoUps->getFechaModificacion()->format('Y-m-d H:i:s') : '-';
                
                $arrayTmpParametros = array( 'criterios' => array(  'strCaracteristica' => "SOLICITUD_MONITOREO_ELEMENTO", 
                                                                    'intIdAlerta'       => $arrayItem['id'], 
                                                                    'intIdElemento'     => $arrayItem['idUps'],
                                                                    'boolAsociadaTarea' => true ) );
                
                $arrayDetalleSolicitud = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                     ->getDetalleSolicitudesByCriterios($arrayTmpParametros);
                
                if( $arrayDetalleSolicitud )
                {
                    if( $arrayDetalleSolicitud['total'] == 1 )
                    {
                        $arrayInfoDetalleSolicitud   = $arrayDetalleSolicitud['registros'][0];
                        $arrayItem['idDetalleTarea'] = $arrayInfoDetalleSolicitud ? $arrayInfoDetalleSolicitud['idDetalleTarea'] : 0;
                        $arrayItem['asignadoTarea']  = 'S';
                    }
                }
                
                $arrayDispositivos[] = $arrayItem;
            }//foreach( $arrayResultados['registros'] as $objElementoUps )
        }//( $arrayResultados['registros'] )

        $arrayResultados['registros'] = $arrayDispositivos;
        
        return $arrayResultados;
    }
    
    
    /**
     * Documentación para el método 'getDispositivosMonitoreoFuentes'.
     *
     * Muestra el listado de todos los dispositivos de las fuentes para monitorear.
     *
     * @param array $arrayParametros
     * 
     * @return array $arrayDispositivos
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 04-02-2016
     */
    public function getDispositivosMonitoreoFuentes($arrayParametros)
    {
        $arrayResultados   = array();
        $arrayDispositivos = array();
        $arrayResultados   = $this->getListadoElementosByCriterios($arrayParametros);
        
        if( $arrayResultados['encontrados'] )
        {
            foreach( $arrayResultados['encontrados'] as $arrayElementoFuente )
            {
                $strNombreSwitch    = '';
                $strPuertoSwitch    = '';
                $strEstadoHistorial = '';
                $objInfoElemento    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                              ->findOneById($arrayElementoFuente['intIdElemento']);
                $objAdmiTipoMedio   = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                              ->findOneBy( array('estado' => 'Activo', 'nombreTipoMedio' => 'ELECTRICO') );
                $objInterfaceFuente = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                              ->findOneBy( array('estado' => 'connected', 'elementoId' => $objInfoElemento) );
                $objInfoEnlace      = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                              ->findOneBy( array( 'estado'                 => 'Activo', 
                                                                                  'interfaceElementoFinId' => $objInterfaceFuente,
                                                                                  'tipoMedioId'            => $objAdmiTipoMedio ) );

                if($objInfoEnlace)
                {
                    $objInterfaceSwitch = $objInfoEnlace->getInterfaceElementoIniId();
                    $strNombreSwitch    = $objInterfaceSwitch->getElementoId()->getNombreElemento();
                    $strPuertoSwitch    = $objInterfaceSwitch->getNombreInterfaceElemento();
                    
                    $strObservacion           = "Se actualiza estado fuente por crontab";
                    $objInfoHistorialElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoHistorialElemento')
                                                     ->getMaxEstadoHistorialElemento($strObservacion);
                    
                    if( $objInfoHistorialElemento )
                    {
                        $strEstadoHistorial = $objInfoHistorialElemento->getEstadoElemento();
                    }//( $objInfoHistorialElemento )
                }//($objInfoEnlace)
                
                if( $strNombreSwitch && $strPuertoSwitch && $strEstadoHistorial)
                {
                    $arrayItem           = array();
                    $arrayItem['switch'] = $strNombreSwitch;
                    $arrayItem['puerto'] = $strPuertoSwitch;
                    $arrayItem['estado'] = $strEstadoHistorial;
                    
                    $arrayDispositivos[] = $arrayItem;
                }//( $strNombreSwitch && $strPuertoSwitch )
            }//foreach( $arrayResultados['encontrados'] as $arrayElementoFuente )
        }//( $arrayResultados['encontrados'] )
        
        return $arrayDispositivos;
    }
    

     /**
     * generarJsonAnillos
     *
     * Esta funcion retorna los anillos configurados en la tabla de parametros
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 18-07-2016
     *
     * @return JSON $resultado
     *
     */
    public function generarJsonAnillos()
    {
        $intCantidadHilos  = 0;

        try
        {
            $arrayParametros   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->get("ANILLOS_MPLS","TECNICO","L3MPLS","","","","","");

            foreach ($arrayParametros as $parametro)
            {
                $intCantidadHilos = $intCantidadHilos + 1;
                $arrayEncontrados[] = array('idAnillo' => $parametro['descripcion'],'numeroAnillo' => $parametro['descripcion']);
            }

            if($intCantidadHilos > 0)
            {
                $arrayEncontrados = array('total'       => $intCantidadHilos,
                                          'encontrados' => $arrayEncontrados);

                $objResultado = json_encode($arrayEncontrados);
            }
            else
            {
                $arrayEncontrados = array('total'       => "0",
                                          'encontrados' => "");

                $objResultado = json_encode($arrayEncontrados);
            }
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $objResultado;
    }


    /**
     * reservarVlanPorPE
     *
     * @author Richard Cabrera  <rcabrera@telconet.ec>
     * @version 1.0 22-08-2019
     *
     * Función encargada de reservar de forma masiva una VLAN en todos los PE de donde sale un servicio de NEDETEL anillo 0
     *
     * @param array $arrayParametros [ intPersonaEmpresaRol => id persona empresa rol del cliente
     *                                 strVlan              => vlan,
     *                                 strUser              => usuario que ejecuta
     *                                 strIpUser            => ip desde donde se ejecuta]
     *
     */
    public function reservarVlanPorPE($arrayParametros)
    {
        try
        {
            $strSql  = "BEGIN
                         DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.P_RESERVAR_VLAN_PE
                         (
                            :PN_PERSONA_EMP_ROL,
                            :PV_VLAN
                          );
                        END;";

            $objStmt = $this->emInfraestructura->getConnection()->prepare($strSql);

            $objStmt->bindParam('PN_PERSONA_EMP_ROL', $arrayParametros["intPersonaEmpresaRol"]);
            $objStmt->bindParam('PV_VLAN', $arrayParametros["strVlan"]);
            $objStmt->execute();
        }
        catch(\Exception $e)
        {
            $this->utilService->insertError('TELCOS+',
                                            'InfoElementoService->reservarVlanPorPE',
                                            $e->getMessage(),
                                            $arrayParametros['strUser'],
                                            $arrayParametros['strIpUser']);
        }
    }


     /**
     * ingresaAuditoriaElementos
     * Funcion que registra la auditoria de un elemento
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 26-02-2018
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 20-04-2018 - Se consulta la serie en telcos, sin descriminar las mayusculas y minisculas
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 29-05-2018 - Se realiza ajustes para cambiar el orden de nombres del responsable
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 04-10-2018 - Se realiza ajustes para guardar una observación en los cambios de Cpe
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.4 21-04-2021 - Se agrega en la validación, la ubicación del elemento que se encuentran en el 'Nodo'.
     *
     * @author Jean Pierre Nazareno Martinez <jnazareno@telconet.ec>
     * @version 1.5 21-09-2021 - Se agrega getConnection() para poder usar 
     * isTransactionActive, rollback, close en catch.
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.6 03-01-2022 - Se agrega tipo de transacción traslado para ingresar auditoría de elemento
     * 
     * @author Geovanny Cudco <acudco@telconet.ec>
     * @version 1.7 29-05-2023 - Se agregan las validaciones para registrar al técnico responsable del cambio de equipo
     *                           en un Nodo Wifi.
     * 
     * @author Geovanny Cudco <acudco@telconet.ec>
     * @version 1.8 09-06-2023 - Se agrega el parámetro strNodoWifi para el registro del responsable del cambio de equipo
     *
     * @param array $arrayParametros [
      *                                strNumeroSerie           numero de serie el elemento
     *                                 strEstadoTelcos          estado del elemento en el Telcos
     *                                 strEstadoNaf             estado del elemento en el NAF
     *                                 strEstadoActivo          estado del elemento para el tracking
     *                                 strUbicacion             ubicacion del elemento
     *                                 strObservacion           campo observación
     *                                 strCodEmpresa            cod empresa
     *                                 strTransaccion           transaccion de donde se origina el registro del tracking
     *                                 strLogin                 login del cliente
     *                                 strResponsable           persona responsable del elemento
     *                                 strUsrCreacion           usuario de creacion
     *                                 strOrigen                origen de donde se llama la funcion
     *                              ]
     */
    public function ingresaAuditoriaElementos($arrayParametros)
    {
        $strIpCreacion      = '127.0.0.1';
        $strResponsable     = '';
        $strCedula          = '';
        $arrayParametrosNaf = array();

        $this->emInfraestructura->getConnection()->beginTransaction();

        try
        {
            //Se obtiene el nombre del cliente cuando la ubicacion es el cliente
            if(($arrayParametros["strLogin"] != "" || $arrayParametros["boolPerteneceElementoNodo"]) &&
               (in_array($arrayParametros["strUbicacion"], array("Cliente","Nodo")) ||
               ($arrayParametros["strUbicacion"] == "EnTransito" &&
                in_array($arrayParametros["strTransaccion"],array('Cambio de Elemento','Cancelacion Servicio','Traslado Servicio')))))
            {
                if ($arrayParametros["boolPerteneceElementoNodo"])
                {
                    $strResponsable = $arrayParametros["strLogin"];
                }
                else
                {
                    //Si la ubicación del dispositivo se encuentra en el cliente o nodo, el responsable es el cliente.
                    if (in_array($arrayParametros["strUbicacion"], array("Cliente","Nodo")))
                    {
                        $strCedula = $arrayParametros["strCedulaCliente"];
                    }

                    $arrayParametrosNaf["strSerie"] = $arrayParametros["strNumeroSerie"];

                    if (empty($strCedula))
                    {
                        $arrayCedula = $this->emNaf->getRepository('schemaBundle:InfoElemento')
                                ->getResponsableArticuloInstalacion($arrayParametrosNaf);
                        $strCedula   = $arrayCedula["cedula"];
                    }

                    if (!empty($strCedula))
                    {
                        $objResponsable = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                ->findOneBy(array("identificacionCliente" => $strCedula));

                        if (is_object($objResponsable))
                        {
                            if ($objResponsable->getRazonSocial() != "")
                            {
                                $strResponsable = $objResponsable->getRazonSocial();
                            }
                            elseif ($objResponsable->getNombres() != "" && $objResponsable->getApellidos() != "")
                            {
                                $strResponsable = $objResponsable->getApellidos() . " " . $objResponsable->getNombres();
                            }
                            elseif ($objResponsable->getRepresentanteLegal() != "")
                            {
                                $strResponsable = $objResponsable->getRepresentanteLegal();
                            }
                            else
                            {
                                $strResponsable = "";
                            }
                        }
                    }
                    else
                    {
                        $arrayResponsable = $this->emNaf->getRepository('schemaBundle:InfoElemento')
                                ->getResponsableFueraBodega($arrayParametrosNaf);
                        $strResponsable   = $arrayResponsable["nombreResponsable"];
                    }


                    // Si es activacion de cliente de MD
                    if ($arrayParametros["strUbicacion"] == "Cliente" && 
                        $arrayParametros["strTransaccion"] == "Activacion Cliente" &&
                        $arrayParametros["strCodEmpresa"] == "18")
                    {
                        $strResponsable = $arrayParametros["strLogin"];
                    }


                    // Si es activacion de cliente de TN y elemento en Nodo
                    if ($arrayParametros["strUbicacion"] == "Nodo" && 
                        $arrayParametros["strTransaccion"] == "Activacion Cliente" &&
                        $arrayParametros["strCodEmpresa"] == "10" &&
                        !empty($arrayParametros["strNombreNodo"]))
                    {
                        $strResponsable = $arrayParametros["strNombreNodo"];
                    }

                    // Si es activacion de cliente de TN, elemento en Nodo y transacción "Traslado a Nodo"
                    if ($arrayParametros["strUbicacion"] == "Nodo" && 
                        $arrayParametros["strTransaccion"] == "Instalacion Nodo" &&
                        $arrayParametros["strCodEmpresa"] == "10" &&
                        !empty($arrayParametros["strResponsable"]))
                    {
                        $strResponsable = $arrayParametros["strResponsable"];
                    }

                }
            }

            //Se obtiene el nombre del resposable cuando la ubicacion sea diferente a cliente
            if ($arrayParametros["strUbicacion"] == "EnOficina" || $arrayParametros["strUbicacion"] == "EnTransito")
            {
                if($arrayParametros["strUbicacion"] == "EnTransito" && $arrayParametros["strEstadoActivo"] == "CambioEquipo" && 
                    $arrayParametros["strTransaccion"] == "Cambio de Elemento" && $arrayParametros["strNodoWifi"]=="SI")
                {
                   $objInfoPersona = $this->emInfraestructura->getRepository('schemaBundle:InfoPersona')
                            ->findOneBy(array("id" => $arrayParametros["intIdPersona"]));                                      
                }
                else
                {
                    $objInfoPersona = $this->emInfraestructura->getRepository('schemaBundle:InfoPersona')
                        ->findOneBy(array("login" => $arrayParametros["strUsrCreacion"]));
                }
            
                if (is_object($objInfoPersona))
                {
                    if ($objInfoPersona->getRazonSocial() != "")
                    {
                        $strResponsable = $objInfoPersona->getRazonSocial();
                    }
                    elseif ($objInfoPersona->getNombres() != "" && $objInfoPersona->getApellidos() != "")
                    {
                        $strResponsable = $objInfoPersona->getApellidos() . " " . $objInfoPersona->getNombres();
                    }
                    elseif ($objInfoPersona->getRepresentanteLegal() != "")
                    {
                        $strResponsable = $objInfoPersona->getRepresentanteLegal();
                    }
                    else
                    {
                        $strResponsable = "";
                    }
                }
            }

            $objInfoElementoTrazabilidad = new InfoElementoTrazabilidad();
            $objInfoElementoTrazabilidad->setNumeroSerie(strtoupper($arrayParametros["strNumeroSerie"]));
            $objInfoElementoTrazabilidad->setEstadoTelcos($arrayParametros["strEstadoTelcos"]);
            $objInfoElementoTrazabilidad->setEstadoNaf($arrayParametros["strEstadoNaf"]);
            $objInfoElementoTrazabilidad->setEstadoActivo($arrayParametros["strEstadoActivo"]);
            $objInfoElementoTrazabilidad->setUbicacion($arrayParametros["strUbicacion"]);
            $objInfoElementoTrazabilidad->setLogin($arrayParametros["strLogin"]);
            $objInfoElementoTrazabilidad->setCodEmpresa($arrayParametros["strCodEmpresa"]);
            $objInfoElementoTrazabilidad->setTransaccion($arrayParametros["strTransaccion"]);
            $objInfoElementoTrazabilidad->setOficinaId($arrayParametros["intOficinaId"]);
            $objInfoElementoTrazabilidad->setResponsable($strResponsable);

            if($arrayParametros["strEstadoActivo"] != "Inventariado" && $arrayParametros["strEstadoActivo"] != "Reingresado"
                && $arrayParametros["strEstadoActivo"] != "Usado" && $arrayParametros["strEstadoActivo"] != "Dañado"
                && $arrayParametros["strEstadoActivo"] != "EnGarantia")
            {
                //Se valida si la serie existe en Telcos
                $arrayParametrosSerie["strNumeroSerie"] = $arrayParametros["strNumeroSerie"];
                $intElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                        ->getExisteSerieTelcos($arrayParametrosSerie);

                if ($arrayParametros["boolPerteneceElementoNodo"])
                {
                    $objInfoElementoTrazabilidad->setObservacion("Elemento pertenece al nodo");
                }
                elseif (!empty($arrayParametros["strObservacion"]))
                {
                    $objInfoElementoTrazabilidad->setObservacion($arrayParametros["strObservacion"]);
                }
                else
                {
                    $objPunto = $this->emInfraestructura->getRepository('schemaBundle:InfoPunto')
                            ->findOneBy(array("login" => $arrayParametros["strLogin"]));

                    if(!is_object($objPunto))
                    {
                        $objInfoElementoTrazabilidad->setObservacion("El login no coincide");
                    }
                }

                if ($intElemento == 0)
                {
                    $objInfoElementoTrazabilidad->setObservacion("La serie no fue encontrada en el Telcos");
                    $objInfoElementoTrazabilidad->setEstadoTelcos("N/A");
                    $objInfoElementoTrazabilidad->setEstadoNaf("N/A");
                }
            }

            //Se registra una observación en el registro de la auditoria del elemento
            if($arrayParametros["strTransaccion"] == "Cambio de Elemento" && $arrayParametros["strEstadoTelcos"] == "Eliminado"
                && $arrayParametros["strCodEmpresa"] == "10")
            {
                $objInfoElementoTrazabilidad->setObservacion($arrayParametros["strObservacion"]);
            }

            $objInfoElementoTrazabilidad->setUsrCreacion($arrayParametros["strUsrCreacion"]);
            $objInfoElementoTrazabilidad->setFeCreacion(new \DateTime('now'));
            $objInfoElementoTrazabilidad->setIpCreacion($strIpCreacion);
            $this->emInfraestructura->persist($objInfoElementoTrazabilidad);
            $this->emInfraestructura->flush();

            $this->emInfraestructura->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }

            $this->utilService->insertError('TELCOS+',
                                            'InfoElementoService->ingresaAuditoriaElementos',
                                            $e->getMessage(),
                                            $arrayParametros["strUsrCreacion"],
                                            $strIpCreacion);

            $this->emInfraestructura->getConnection()->close();
        }
    }

    /**
     * Función que sirve para Actualizar elemento en el naf a estado Instalado
     * 
     * @author Jorge Gomez <jigomez@telconet.ec>
     * @version 1.0 13-09-2016 Version inicial
     * 
    */
    public function ingresaInstalacionNaf($arrayParametros)
    {

        try
        {
            $strArticuloElementoCliente = "";
            $strTipoArticulo            = "AF";
            $strIdentificacionCliente   = "";
    
                //actualizamos registro en el naf cpe
                $strMensajeError = str_repeat(' ', 1000);
                $strSql = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, "
                . ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, "
                . ":cantidad, :pv_mensajeerror); END;";
    
                $objStmt = $this->emNaf->getConnection()->prepare($strSql);
    
                $objStmt->bindParam('codigoEmpresaNaf',        $arrayParametros["intIdEmpresa"]);
                $objStmt->bindParam('codigoArticulo',          $strArticuloElementoCliente);
                $objStmt->bindParam('tipoArticulo',            $strTipoArticulo);
                $objStmt->bindParam('identificacionCliente',   $strIdentificacionCliente);
                $objStmt->bindParam('serieCpe',                $arrayParametros["strSerie"]);
                $objStmt->bindParam('cantidad',                intval(1));
                $objStmt->bindParam('pv_mensajeerror',         $strMensajeError);
                $objStmt->execute();


                if(strlen(trim($strMensajeError)) > 0)
                {
                    $arrayRespuestaFinal = array("status" => "NAF", "mensaje" => "ERROR TRANSCEIVER NAF: ".$strMensajeError);
                    return $arrayRespuestaFinal;
                }
    
                $arrayRespuestaFinal = array("status" => "ok", "mensaje" => "Se instalo correctamente");
                return $arrayRespuestaFinal;
        }
        catch(\Exception $e)
        {
            $this->utilService->insertError('TELCOS+',
                                            'InfoElementoService->ingresaInstalacionNaf',
                                            $e->getMessage(),
                                            $arrayParametros['strUser'],
                                            $arrayParametros['strIpUser']);
        }


    }


    /**
     * Función que sirve para Actualizar elemento en el naf a estado Retirado
     * 
     * @author Jorge Gomez <jigomez@telconet.ec>
     * @version 1.0 13-09-2016 Version inicial
     * 
    */
    public function ingresaRetiroNaf($arrayParametros)
    {

        try
        {

            $emComercial = $this->emcom;

            $objEmpleadoResponsable = $emComercial->getRepository('schemaBundle:InfoPersona')
            ->getPersonaDepartamentoPorUserEmpresa($arrayParametros['strUser'],
            $arrayParametros["intIdEmpresa"]);

            $strTipoArticulo          = "AF";
            $strEstadoRe              = "RE";
    
                //actualizamos registro en el naf cpe
                $strMensajeError = str_repeat(' ', 1000);
                $strSql                     = "BEGIN AFK_PROCESOS.IN_P_RETIRA_INSTALACION(:codigoEmpresaNaf, ".
                ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, ".
                ":cantidad, :estado, :pv_mensajeerror); END;";
                $objStmt                    = $this->emNaf->getConnection()->prepare($strSql);
                $objStmt->bindParam('codigoEmpresaNaf'      , $arrayParametros["intIdEmpresa"]);
                $objStmt->bindParam('codigoArticulo'        , $arrayParametros["strModelo"]);
                $objStmt->bindParam('tipoArticulo'          , $strTipoArticulo);
                $objStmt->bindParam('identificacionCliente' , $objEmpleadoResponsable["IDENTIFICACION_CLIENTE"]);
                $objStmt->bindParam('serieCpe'              , $arrayParametros["strSerie"]);
                $objStmt->bindParam('cantidad'              , intval(1));
                $objStmt->bindParam('estado'                , $strEstadoRe);
                $objStmt->bindParam('pv_mensajeerror'       , $strMensajeError);
                $objStmt->execute();

                if(strlen(trim($strMensajeError)) > 0)
                {
                    $arrayRespuestaFinal = array("status" => "NAF", "mensaje" => "ERROR TRANSCEIVER NAF: ".$strMensajeError);
                    return $arrayRespuestaFinal;
                }

                $arrayRespuestaFinal = array("status" => "ok", "mensaje" => "Se retiro correctamente");
                return $arrayRespuestaFinal;
        }
        catch(\Exception $e)
        {
            $this->utilService->insertError('TELCOS+',
                                            'InfoElementoService->ingresaInstalacionNaf',
                                            $e->getMessage(),
                                            $arrayParametros['strUser'],
                                            $arrayParametros['strIpUser']);
        }


    }

     /**
     * procesarSeriesElementos
     * Funcion que procesa la y actualiza el estado de series cargadas por un archivo excel
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 05-03-2018
     * 
     * @author Jean Pierre Nazareno Martinez <jnazareno@telconet.ec>
     * @version 1.1 21-09-2021 - Se agrega getConnection() para poder usar 
     * isTransactionActive, rollback, close en catch.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 06-05-2022 Se modifica función ya que el directorio del archivo excel ya no estará en un directorio de manera local.
     *
     * @param array $arrayParametros [ strNombreArchivo     nombre del archivo que se carga
     *                                 strEstadoActualizar  estado a actualizar
     *                                 strUsrCreacion       usuario de creacion
     *                                 strIpCreacion        ip de creacion strRutaServer
     *                                 strCodEmpresa        codigo de la empresa
     *                                 strRutaServer        ruta del server ]
     */
    public function procesarSeriesElementos($arrayParametros)
    {
        $arrayParametrosAuditoria = array();

        $this->emInfraestructura->getConnection()->beginTransaction();

        try
        {
            $objReader = PHPExcel_IOFactory::createReaderForFile($arrayParametros["strAbsolutePath"]);
            $objReader->setReadDataOnly(true);

            $objXLS = $objReader->load($arrayParametros["strAbsolutePath"]);
            $objWorksheet = $objXLS->getSheet(0);

            $intMaximaFila    = $objWorksheet->getHighestRow();
            $intMaximaColumna = $objWorksheet->getHighestColumn();
            $arrayParametrosAuditoria["strOrigen"] = "cargaMasiva";

            //Se registra en la INFO_PROCESO_MASIVO_CAB
            $objInfoProcesoMasivoCab = new InfoProcesoMasivoCab();
            $objInfoProcesoMasivoCab->setTipoProceso("CargaMasivaSeries");
            $objInfoProcesoMasivoCab->setEmpresaCod($arrayParametros["strCodEmpresa"]);
            $objInfoProcesoMasivoCab->setEstado("Finalizado");
            $objInfoProcesoMasivoCab->setFeCreacion(new \DateTime('now'));
            $objInfoProcesoMasivoCab->setUsrCreacion($arrayParametros["strUsrCreacion"]);
            $objInfoProcesoMasivoCab->setIpCreacion($arrayParametros["strIpCreacion"]);
            $this->emInfraestructura->persist($objInfoProcesoMasivoCab);

            for ($i = 2; $i <= $intMaximaFila; $i++)
            {
                //SE REGISTRA EL TRACKING DEL ELEMENTO
                $arrayParametrosAuditoria["strNumeroSerie"]  = $objWorksheet->getCell("A".$i)->getValue();
                $arrayParametrosAuditoria["strLogin"]        = $objWorksheet->getCell("B".$i)->getValue();
                $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
                $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';

                if($arrayParametros["strEstadoActualizar"] == 'EnTransito')
                {
                    $arrayParametrosAuditoria["strEstadoActivo"] = 'EnTransito';
                    $arrayParametrosAuditoria["strUbicacion"]    = 'EnTransito';
                }
                else if($arrayParametros["strEstadoActualizar"] == 'EnOficinaMd')
                {
                    $arrayParametrosAuditoria["strEstadoActivo"] = 'EnOficinaMd';
                    $arrayParametrosAuditoria["strUbicacion"]    = 'EnOficina';
                }

                $arrayParametrosAuditoria["strUsrCreacion"] = $arrayParametros["strUsrCreacion"];
                $arrayParametrosAuditoria["strCodEmpresa"]  = $arrayParametros["strCodEmpresa"];
                $arrayParametrosAuditoria["strTransaccion"] = "Carga Masiva Series";
                $arrayParametrosAuditoria["strCodEmpresa"]   = $arrayParametros["strCodEmpresa"];
                $arrayParametrosAuditoria["intOficinaId"]    = 0;

                //Se ingresa el tracking del elemento
                $this->ingresaAuditoriaElementos($arrayParametrosAuditoria);

                //Se registra en la INFO_PROCESO_MASIVO_DET
                $objInfoProcesoMasivoDet = new InfoProcesoMasivoDet();
                $objInfoProcesoMasivoDet->setProcesoMasivoCabId($objInfoProcesoMasivoCab);
                $objInfoProcesoMasivoDet->setPuntoId(50418);
                $objInfoProcesoMasivoDet->setLogin($objWorksheet->getCell("B".$i)->getValue());
                $objInfoProcesoMasivoDet->setSerieFisica($objWorksheet->getCell("A".$i)->getValue());
                $objInfoProcesoMasivoDet->setFeCreacion(new \DateTime('now'));
                $objInfoProcesoMasivoDet->setUsrCreacion($arrayParametros["strUsrCreacion"]);
                $objInfoProcesoMasivoDet->setIpCreacion($arrayParametros["strIpCreacion"]);
                $objInfoProcesoMasivoDet->setEstado("Finalizado");
                $this->emInfraestructura->persist($objInfoProcesoMasivoDet);
                $this->emInfraestructura->flush();
            }

            $this->emInfraestructura->getConnection()->commit();

            return true;
        }
        catch(\Exception $e)
        {
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }

            $this->utilService->insertError('TELCOS+',
                                            'InfoElementoService->procesarSeriesElementos',
                                            $e->getMessage(),
                                            $arrayParametros["strUsrCreacion"],
                                            $arrayParametros["strIpCreacion"]);

            $this->emInfraestructura->getConnection()->close();

            return false;
        }
    }



    /**
     * getListadoElementosByCriterios
     *
     * Método que retorna el listado de los elementos encontrados bajo los criterios enviados por el usuario                      
     *      
     * @param array $arrayParametros  [ 'intStart', 'intLimit', 'intEmpresa', 'strEstado', 'strCategoriaElemento', 'arrayCriterios' ]
     * 
     * @return array $arrayResultados [ 'encontrados', 'total' ]
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 06-11-2015
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 23-11-2015 - Se agrega que se envíe la serie física en la consulta
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 16-12-2015 - Se agrega que se envíe el número de chasis y el motor en la consulta
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 08-04-2016 - Se agrega que se busque la asignación vehicular predefinida
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 14-12-2016 - Se agrega el estado del elemento y se obtiene la persona responsable
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 18-01-2017 - Se agrega la información del departamento, la región y el cantón al que pertenece la persona responsable de una
     *                           tablet
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 1.6 20-04-2020 - Se modifica para que devuelva la información completa de la camioneta ( GPS correcto )
     *
     * @author Modificado: Wilmer Vera <wvera@telconet.ec>
     * @version 1.7 24-09-2020 - Se modifica para poder agregar un criterio mas de busqueda, en este caso el PUBLISHID identificador de tablets
     * usados a partir de la versión 10 del sistema operativo Android.
     * 
     */
    public function getListadoElementosByCriterios($arrayParametros)
    {
        $arrayTmpResultados   = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->getElementosByCriterios($arrayParametros);
        $objRegistros         = $arrayTmpResultados['registros'];
        $arrayElementos       = array();
        $strCategoriaElemento = strtolower($arrayParametros['strCategoriaElemento']);
        
        if($objRegistros)
        {
            foreach($objRegistros as $objElemento)
            {
                $intIdElemento         = $objElemento->getId();
                $strNombreTipoElemento = ucwords( strtolower( $objElemento->getModeloElementoId()
                                                                          ->getTipoElementoId()->getNombreTipoElemento() ) );
                
                    $arrayItem                      = array();
                $arrayItem['intIdElemento']         = $intIdElemento;
                $arrayItem['strSerieFisica']        = $objElemento->getSerieFisica();
                $arrayItem['strNombreElemento']     = $objElemento->getNombreElemento();
                $arrayItem['strSerieLogica']        = $objElemento->getSerieLogica();
                $arrayItem['strModeloElemento']     = $objElemento->getModeloElementoId()
                                                    ? $objElemento->getModeloElementoId()->getNombreModeloElemento() : '';
                $arrayItem['strFechaCreacion']      = $objElemento->getFeCreacion()->format('d M Y');
                $arrayItem['strTipoElemento']       = $strNombreTipoElemento;
                
                $arrayItem['strEstadoElemento']     = $objElemento->getEstado();
                
                $arrayItem['strUrlShow']            = $this->container->get('router')
                                                       ->generate('elemento'.$strCategoriaElemento.'_show', array('id' => $intIdElemento));
                $arrayItem['strUrlEdit']            = $this->container->get('router')
                                                       ->generate('elemento'.$strCategoriaElemento.'_edit', array('id' => $intIdElemento));
                if($strNombreTipoElemento=="Vehiculo")
                {
                    $arrayItem['strUrlShowDocumentosTransporte']  = $this->container->get('router')
                                                       ->generate('elemento'.$strCategoriaElemento.'_showDocumentosTransporte', 
                                                           array('id' => $intIdElemento));
                
                }
                                
                $arrayParametrosDetalleElemento = array('estado' => $arrayParametros['strEstadoActivo'], 'elementoId' => $intIdElemento);
                $objDetallleElementos           = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                       ->findBy( $arrayParametrosDetalleElemento );
                
                if( $objDetallleElementos )
                {
                    foreach( $objDetallleElementos as $objDetallleElemento )
                    {
                        $strNombreDetalleElemento   = $objDetallleElemento->getDetalleNombre();
                        $arrayItem['str'.$strNombreDetalleElemento] = $objDetallleElemento->getDetalleValor();
                        
                        if($strNombreDetalleElemento == 'GPS')
                        {
                            $objElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                   ->find($objDetallleElemento->getDetalleValor());
                            if(is_object($objElemento))
                            {
                                $arrayItem['str'.$strNombreDetalleElemento] = $objElemento->getNombreElemento();
                            }
                        }
                                                
                        if($strNombreDetalleElemento=="RESPONSABLE_TABLET" && isset($arrayItem['strRESPONSABLE_TABLET']) 
                            && !empty($arrayItem['strRESPONSABLE_TABLET']) )
                        {
                            $objPerResponsable = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                             ->find($arrayItem['strRESPONSABLE_TABLET']);
                            if(is_object($objPerResponsable))
                            {
                                $intIdOficinaPerResponsable     = $objPerResponsable->getOficinaId();
                                if($intIdOficinaPerResponsable)
                                {
                                    $objOficinaPerResponsable   = $this->emcom->getRepository('schemaBundle:InfoOficinaGrupo')
                                                                              ->find($intIdOficinaPerResponsable);
                                    if(is_object($objOficinaPerResponsable))
                                    {
                                        $intIdCantonPerResponsable  = $objOficinaPerResponsable->getCantonId();
                                        if($intIdCantonPerResponsable)
                                        {
                                            $objCantonPerResponsable   = $this->emcom->getRepository('schemaBundle:AdmiCanton')
                                                                                     ->find($intIdCantonPerResponsable);
                                            if(is_object($objCantonPerResponsable))
                                            {
                                                $arrayItem['strRegionResponsableTablet']  = $objCantonPerResponsable->getRegion();
                                                $arrayItem['strCantonResponsableTablet']  = sprintf('%s', $objCantonPerResponsable);
                                            }
                                        }
                                    }
                                }
                                
                                $intIdDepartamentoPerResponsable    = $objPerResponsable->getDepartamentoId();
                                if($intIdDepartamentoPerResponsable)
                                {
                                    $objDepartamentoPerResponsable  = $this->emcom->getRepository('schemaBundle:AdmiDepartamento')
                                                                                  ->find($intIdDepartamentoPerResponsable);
                                    if(is_object($objDepartamentoPerResponsable))
                                    {
                                        $arrayItem['strDepartamentoResponsableTablet']  = sprintf('%s', $objDepartamentoPerResponsable);
                                    }
                                }
                                $objPersonaResponsable              = $objPerResponsable->getPersonaId();
                                if(is_object($objPersonaResponsable))
                                {
                                    $arrayItem['strResponsableTablet'] = sprintf('%s', $objPersonaResponsable);
                                }
                            }
                        }
                    }
                }
                $arrayElementos[] = $arrayItem;
            }
        }

        $arrayResultados = array( 'total' => $arrayTmpResultados['total'], 'encontrados' => $arrayElementos );
        
        return $arrayResultados;
    }
    
    /** 
    * Descripcion: Metodo encargado de eliminar documentos a partir del id de la referencia enviada
    * @author 
    * @version 1.0 11-12-2015   
    * @param integer $id // id del documento
    * @return json con resultado del proceso
    * 
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.0 16-12-2015 
    */
    
    public function eliminarDocumento($id)
    {                
        $this->emComunicacion->getConnection()->beginTransaction();
        try
        {  
            $objInfoDocumento =  $this->emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($id);                                              
            if( $objInfoDocumento )
            {            
                $path = $objInfoDocumento->getUbicacionFisicaDocumento();
                if (file_exists($this->path_telcos.$path))
                unlink($this->path_telcos.$path);

                $objInfoDocumento->setEstado("Inactivo");
                $this->emComunicacion->persist($objInfoDocumento);
                $this->emComunicacion->flush();               

                $objInfoDocumentoRelacion = $this->emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')->findByDocumentoId($id);
                if(isset($objInfoDocumentoRelacion))
                {
                    foreach($objInfoDocumentoRelacion as $det)
                    {
                        $det->setEstado("Inactivo");
                        $this->emComunicacion->persist($det);
                        $this->emComunicacion->flush();
                    }
                }
             if ($this->emComunicacion->getConnection()->isTransactionActive())
             {
                 $this->emComunicacion->getConnection()->commit();
             }                
             $this->emComunicacion->getConnection()->close();  
             return $objInfoDocumento;    
             
            }     
        }
        catch(\Exception $e)
        {                                 
           if ($this->emComunicacion->getConnection()->isTransactionActive())
           {
               $this->emComunicacion->getConnection()->rollback();
           }                            
           $this->emComunicacion->getConnection()->close();  
           throw $e;
        }   
    }
    
    /**
     * Funcion que Guarda Archivos Digitales agregados al transporte 
     * @author 
     * @param interger $id // id de InfoElemento 
     * @param string $usrCreacion
     * @param string $clientIp
     * @param array $datos_form
     * @throws Exception
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 22-12-2015 - Se agrega que se envíe el arreglo de las fechas de caducidad.
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 08-04-2016 - Se modifica entidad InfoDocumento ingresando la ruta y nombre de guardado como parametro a la entidad
     * 
     * @return \telconet\schemaBundle\Entity\InfoDocumentoRelacion
     */
    
    public function guardarArchivoDigital($id, $usrCreacion, $clientIp, $datos_form)
    {    
        $fecha_creacion = new \DateTime('now');
        $this->emComunicacion->getConnection()->beginTransaction();
        try
        {  
            $objMedioTransporte = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($id);
            if( $objMedioTransporte )
            {
                //Guardo files asociados al transporte                      
                $datos_form_files    = $datos_form['datos_form_files'];
                $arrayTipoDocumentos = $datos_form['arrayTipoDocumentos'];
                $arrayFechasHastaDocumentos = $datos_form['arrayFechasHastaDocumentos'];
                $i=0;
                foreach ($datos_form_files as $key => $imagenes)                 
                {  
                    foreach ( $imagenes as $key_imagen => $value) 
                    {        
                        if( $value )
                        {                            
                            $objInfoDocumento = new InfoDocumento(); 
                            $objInfoDocumento->setFile( $value );         
                            $objInfoDocumento->setNombreDocumento("documento_digital");
                            $objInfoDocumento->setUploadDir(substr($this->fileRoot, 0, -1));
                            $objInfoDocumento->setFechaDocumento( $fecha_creacion );                                                                 
                            $objInfoDocumento->setUsrCreacion( $usrCreacion );
                            $objInfoDocumento->setFeCreacion( $fecha_creacion );
                            $objInfoDocumento->setIpCreacion( $clientIp );
                            $objInfoDocumento->setEstado( 'Activo' );                                                           
                            $objInfoDocumento->setMensaje( "Archivo agregado al elemento con id ".$objMedioTransporte->getId() );                                                             
                            if($arrayFechasHastaDocumentos[$key_imagen])
                            {
                                $objInfoDocumento->setFechaPublicacionHasta($arrayFechasHastaDocumentos[$key_imagen]);   
                            }
                            $objTipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                                        ->find($arrayTipoDocumentos[$key_imagen]);                                                                                                                                    
                            if( $objTipoDocumentoGeneral != null )
                            {            
                                $objInfoDocumento->setTipoDocumentoGeneralId( $objTipoDocumentoGeneral->getId() );                            
                            }                                                    
                            $i++;                        
                            if ( $objInfoDocumento->getFile() )
                            {
                                $objInfoDocumento->preUpload();
                                $objInfoDocumento->upload();
                            }                                                                           
                            $objTipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                                    ->findOneByExtensionTipoDocumento(strtoupper($objInfoDocumento->getExtension()));                                    

                            if( $objTipoDocumento != null)
                            {
                                $objInfoDocumento->setTipoDocumentoId($objTipoDocumento);                                
                            }
                            else
                            {   //Inserto registro con la extension del archivo a subirse
                                $objAdmiTipoDocumento = new AdmiTipoDocumento(); 
                                $objAdmiTipoDocumento->setExtensionTipoDocumento(strtoupper($objInfoDocumento->getExtension()));
                                $objAdmiTipoDocumento->setTipoMime(strtoupper($objInfoDocumento->getExtension()));                            
                                $objAdmiTipoDocumento->setDescripcionTipoDocumento('ARCHIVO FORMATO '.strtoupper($objInfoDocumento->getExtension()));
                                $objAdmiTipoDocumento->setEstado('Activo');
                                $objAdmiTipoDocumento->setUsrCreacion( $usrCreacion );
                                $objAdmiTipoDocumento->setFeCreacion( $fecha_creacion );                        
                                $this->emComunicacion->persist( $objAdmiTipoDocumento );
                                $this->emComunicacion->flush(); 
                                $objInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);    
                            }                      
                            
                            $this->emComunicacion->persist($objInfoDocumento);
                            $this->emComunicacion->flush();   

                            $objInfoDocumentoRelacion = new InfoDocumentoRelacion(); 
                            $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());                    
                            $objInfoDocumentoRelacion->setModulo('TECNICO'); 
                            $objInfoDocumentoRelacion->setElementoId($id);       
                            $objInfoDocumentoRelacion->setEstado('Activo');                                                                                   
                            $objInfoDocumentoRelacion->setFeCreacion($fecha_creacion);                        
                            $objInfoDocumentoRelacion->setUsrCreacion($usrCreacion);
                            $this->emComunicacion->persist($objInfoDocumentoRelacion);                        
                            $this->emComunicacion->flush();
                        }
                    }                       
                }
                if ($this->emComunicacion->getConnection()->isTransactionActive()){
                    $this->emComunicacion->getConnection()->commit();
                }                
                $this->emComunicacion->getConnection()->close();  
                return $objInfoDocumentoRelacion;
            }
       }
       catch(\Exception $e)
       {                                 
           if ($this->emComunicacion->getConnection()->isTransactionActive())
           {
               $this->emComunicacion->getConnection()->rollback();
           }                            
           $this->emComunicacion->getConnection()->close();  
           throw $e;
       }        
    } 
    
    
    /**
     * Funcion que elimina la asignación vehicular a determinada cuadrilla y, si es que hibiese una asignación provisional de chofer,
     * también la elimina
     * @author 
     * @param array $arrayParametros[
     *                                  'intIdElemento'                     => id del vehículo
     *                                  'intIdCuadrilla'                    => id de la cuadrilla
     *                              ]
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 31-03-2016
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 24-08-2016 Se realizan modificaciones para eliminar el detalle del id de la solicitud predefinida
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 26-09-2018 Se elimina variable objSession y se la obtiene de las dependencias por corrección del SonarQube
     * 
     * @return string $strMensaje
     */
    public function eliminarAsignacionVehicularYProvisionalXCuadrilla($arrayParametros)
    {
        $strFechasHorasAsignacion       = '';
        $strFechasHorasAsignacionProv   = '';
        $histoDetalleProvisional        = false;
        
        $strEstadoActivo    = 'Activo';
        $strEstadoEliminado = 'Eliminado';
        

        $objRequest         = $this->container->get('request');
        $strUserSession     = $this->objSession->get('user');
        $strIpUserSession   = $objRequest->getClientIp();
        $strMensaje         = '';
        
        $datetimeActual     = new \DateTime('now');
        $strFechaFinAV      = $datetimeActual->format('d/m/Y');
        
        $this->emcom->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        try
        {
            $objElemento    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayParametros['intIdElemento']);
        
            $objCuadrilla           = $this->emcom->getRepository('schemaBundle:AdmiCuadrilla')->find($arrayParametros['intIdCuadrilla']);
            
            $objDetalleCuadrilla = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                            ->findOneBy( array( 'estado'        => $strEstadoActivo, 
                                                                                'detalleNombre' => self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO, 
                                                                                'detalleValor'  => $arrayParametros['intIdCuadrilla'] ) 
                                                                        );
            
            
            if($objElemento && $objDetalleCuadrilla)
            {
                $strNombreCuadrilla     = $objCuadrilla->getNombreCuadrilla() ;
                
                $objDetalleCuadrilla->setEstado($strEstadoEliminado);
                $this->emInfraestructura->persist($objDetalleCuadrilla);
                $this->emInfraestructura->flush();



                $arrayDetallesAEliminarAsignacionVehicular = array
                                                                (
                                                                    'Fecha Inicio'   => self::DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO,
                                                                    'Hora Inicio'    => self::DETALLE_ASIGNACION_VEHICULAR_HORA_INICIO,
                                                                    'Hora Fin'       => self::DETALLE_ASIGNACION_VEHICULAR_HORA_FIN,
                                                                    'ID Solicitud'   => self::DETALLE_ASIGNACION_VEHICULAR_ID_SOL_PRED 
                                                                );

                foreach($arrayDetallesAEliminarAsignacionVehicular as $detalleNombreAlias=>$detalleNombre)
                {
                    $objDetalleAEliminar = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                ->findOneBy( array( 'estado'        => $strEstadoActivo, 
                                                                                    'detalleNombre' => $detalleNombre, 
                                                                                    'parent'        => $objDetalleCuadrilla ) 
                                                                           );
                    if($objDetalleAEliminar)
                    {
                        $strFechasHorasAsignacion.= $detalleNombreAlias.": ".$objDetalleAEliminar->getDetalleValor()." ";
                        $objDetalleAEliminar->setEstado($strEstadoEliminado);
                        $this->emInfraestructura->persist($objDetalleAEliminar);
                        $this->emInfraestructura->flush();
                    }

                }
                
                
                /*Crear Detalle Fecha Fin de Asignacion Vehicular*/
                $objInfoDetalleFechaFinAV = new InfoDetalleElemento();
                $objInfoDetalleFechaFinAV->setElementoId($arrayParametros['intIdElemento']);
                $objInfoDetalleFechaFinAV->setDetalleNombre(self::DETALLE_ASIGNACION_VEHICULAR_FECHA_FIN);
                $objInfoDetalleFechaFinAV->setDetalleValor($strFechaFinAV);
                $objInfoDetalleFechaFinAV->setDetalleDescripcion(self::DETALLE_ASIGNACION_VEHICULAR_FECHA_FIN);
                $objInfoDetalleFechaFinAV->setFeCreacion($datetimeActual);
                $objInfoDetalleFechaFinAV->setUsrCreacion($strUserSession);
                $objInfoDetalleFechaFinAV->setIpCreacion($strIpUserSession);
                $objInfoDetalleFechaFinAV->setEstado($strEstadoEliminado);
                $objInfoDetalleFechaFinAV->setParent($objDetalleCuadrilla);
                $this->emInfraestructura->persist($objInfoDetalleFechaFinAV);
                $this->emInfraestructura->flush();
                
                $strFechasHorasAsignacion.= "Fecha Fin: ".$strFechaFinAV;
                
                $strMensajeObservacionEliminacion="Se elimina Veh&iacute;culo a la Cuadrilla<br>";
                
                $strMensajeObservacionElemento  = 'Se elimina asignaci&oacute;n vehicular a cuadrilla ';
                $strMotivoEliminacion           = 'Se elimina asignacion vehicular a cuadrilla';
                $objMotivoEliminacion           = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                                                    ->findOneByNombreMotivo($strMotivoEliminacion);
                $intIdMotivoEliminacion         = $objMotivoEliminacion ? $objMotivoEliminacion->getId() : 0;
                
                
                //Historial Eliminación de vehículo en cuadrilla
                $objCuadrillaHistorialEliminacion = new AdmiCuadrillaHistorial();
                $objCuadrillaHistorialEliminacion->setCuadrillaId($objCuadrilla);
                $objCuadrillaHistorialEliminacion->setEstado($objCuadrilla->getEstado());
                $objCuadrillaHistorialEliminacion->setFeCreacion($datetimeActual);
                $objCuadrillaHistorialEliminacion->setUsrCreacion($strUserSession);
                $objCuadrillaHistorialEliminacion->setObservacion($strMensajeObservacionEliminacion.$strFechasHorasAsignacion);
                $objCuadrillaHistorialEliminacion->setMotivoId($intIdMotivoEliminacion);
                $this->emcom->persist($objCuadrillaHistorialEliminacion);
                $this->emcom->flush();


                $strMensajeObservacionElemento.=" de la cuadrilla ".$strNombreCuadrilla."<br/>";
                $objInfoHistorialElemento = new InfoHistorialElemento();
                $objInfoHistorialElemento->setElementoId($objElemento);
                $objInfoHistorialElemento->setObservacion($strMensajeObservacionElemento.$strFechasHorasAsignacion);
                $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                $objInfoHistorialElemento->setEstadoElemento($strEstadoActivo);
                $this->emInfraestructura->persist($objInfoHistorialElemento);
                $this->emInfraestructura->flush();
                
                
                
                //Se busca si existe una asignación provisional asociada a la cuadrilla y se la elimina       
                $objDetalleChoferProvisional = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                        ->findOneBy( array( 'estado'        => $strEstadoActivo, 
                                                                            'detalleNombre' => self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER, 
                                                                            'parent'        => $objDetalleCuadrilla ) 
                                                                   );

                //Si se elimina una asignación vehicular, se elimina la asignación provisional si es que tuviera alguna
                if($objDetalleChoferProvisional)
                {
                    $objDetalleChoferProvisional->setEstado($strEstadoEliminado);
                    $this->emInfraestructura->persist($objDetalleChoferProvisional);
                    $this->emInfraestructura->flush();
                    
                    $strMensajeObservacionProvisional   = 'Se elimina asignaci&oacute;n provisional del chofer por eliminaci&oacute;n vehicular ';
                    $strMotivoElementoProvisional       = 'Se elimina asignacion provisional del chofer por eliminacion vehicular';
                    $objMotivoChoferProvisional         = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                                                            ->findOneByNombreMotivo($strMotivoElementoProvisional);
                    $intIdMotivoChoferProvisional       = $objMotivoChoferProvisional ? $objMotivoChoferProvisional->getId() : 0;
                    
                    $idPerChofer = $objDetalleChoferProvisional->getDetalleValor();
                    $objPerChofer= $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idPerChofer);

                    if($objPerChofer)
                    {
                        $strMensajeObservacionProvisional.= $objPerChofer->getPersonaId()->getNombres()." ";
                        $strMensajeObservacionProvisional.= $objPerChofer->getPersonaId()->getApellidos()." ";
                    }

                    $histoDetalleProvisional=true;

                    $arrayDetallesAEliminarAsignacionProvisional=array
                                                                        (
                                                                            'Departamento'    =>
                                                                                self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_DEPARTAMENTO,
                                                                            'Zona'              =>
                                                                                self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_ZONA,
                                                                            'Tarea'              =>
                                                                                self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_TAREA,
                                                                            'Fecha Inicio Provisional'   =>
                                                                                self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_INICIO,
                                                                            'Fecha Fin Provisional'    =>
                                                                                self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_FIN,
                                                                            'Hora Inicio Provisional'   =>
                                                                                self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_INICIO,
                                                                            'Hora Fin Provisional'    =>
                                                                                self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_FIN,   
                                                                            'Motivo Provisional'    =>
                                                                                self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_MOTIVO,
                                                                            'Id Solicitud Chofer Predefinido' =>
                                                                                self::DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PRED,
                                                                            'Id Solicitud Chofer Provisional' =>
                                                                                 self::DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PROV
                                                                                    
                                                                        );

                    foreach($arrayDetallesAEliminarAsignacionProvisional as $detalleNombreAliasProvisional=>$detalleNombreProvisional)
                    {
                        $objDetalleAEliminar = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy( array( 'estado'        => $strEstadoActivo, 
                                                                                        'detalleNombre' => $detalleNombreProvisional, 
                                                                                        'parent'        => $objDetalleChoferProvisional ) 
                                                                               );
                        if($objDetalleAEliminar)
                        {
                            if($detalleNombreAliasProvisional=="Fecha Fin Provisional")
                            {
                                $strFechaActualAPChoferEliminarDetalle = $datetimeActual->format('d-M-Y');
                                $objDetalleAEliminar->setDetalleValor($strFechaActualAPChoferEliminarDetalle);
                            }
                            
                            $strFechasHorasAsignacionProv.= $detalleNombreAliasProvisional.": ".$objDetalleAEliminar->getDetalleValor()."<br/>";
                            $objDetalleAEliminar->setEstado($strEstadoEliminado);
                            $this->emInfraestructura->persist($objDetalleAEliminar);
                            $this->emInfraestructura->flush();
                        }
                    }
                    
                    
                    $objTipoSolicitud = $this->emcom->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                            ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_CHOFER_PROVISIONAL);
                
                    $objDetalleSolicitud = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                        ->findOneBy( array( 
                                                                                        'elementoId'        => $arrayParametros['intIdElemento'],
                                                                                        'tipoSolicitudId'   => $objTipoSolicitud,
                                                                                        'estado'            => "Pendiente"
                                                                                    ) 
                                                                               );

                    if($objDetalleSolicitud)
                    {
                        //Se obtiene el detalle solicitud pendiente en el historial
                        $objDetalleSolHistPendienteAPChofer = $this->emcom->getRepository('schemaBundle:InfoDetalleSolHist')
                                               ->findOneBy(
                                                   array(
                                                       "detalleSolicitudId"=> $objDetalleSolicitud,
                                                       "estado"            => "Pendiente"
                                                       )
                                                   );
                        
                        //Se obtiene la fecha de Inicio 
                        $timestampFechaDesdeAPChoferPendiente   = $objDetalleSolHistPendienteAPChofer->getFeIniPlan();

                        $timestampFechaHastaAPChoferPendiente       = $objDetalleSolHistPendienteAPChofer->getFeFinPlan();
                        $strFechaActualAPChoferEliminarSolicitud    = $datetimeActual->format('d/M/Y');
                        $strHoraHastaAPChoferPendiente              = $timestampFechaHastaAPChoferPendiente->format('H:i:s');
                        
                        list($dayHastaAPChoferEliminar,$mesHastaAPChoferEliminar,$yearHastaAPChoferEliminar)=explode('/',$strFechaActualAPChoferEliminarSolicitud);
                        list($horaHastaAPChoferEliminar,$minutosHastaAPChoferEliminar)=explode(':',$strHoraHastaAPChoferPendiente);

                        $datetimeHastaAPChoferEliminar  = new \DateTime();
                        $datetimeHastaAPChoferEliminar->setDate($yearHastaAPChoferEliminar, $mesHastaAPChoferEliminar, $dayHastaAPChoferEliminar);
                        $datetimeHastaAPChoferEliminar->setTime($horaHastaAPChoferEliminar, $minutosHastaAPChoferEliminar, '00');
                        
                        
                        //Crear un Info Detalle Solicitud Historial Finalizado
                        $objInfoDetalleSolHist = new InfoDetalleSolHist();
                        $objInfoDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                        $objInfoDetalleSolHist->setEstado("Finalizado");
                        $objInfoDetalleSolHist->setFeCreacion($datetimeActual);
                        $objInfoDetalleSolHist->setUsrCreacion($strUserSession);
                        $objInfoDetalleSolHist->setIpCreacion($strIpUserSession);
                        $objInfoDetalleSolHist->setMotivoId($intIdMotivoChoferProvisional);
                        $objInfoDetalleSolHist->setFeIniPlan($timestampFechaDesdeAPChoferPendiente);
                        $objInfoDetalleSolHist->setFeFinPlan($datetimeHastaAPChoferEliminar);
                        
                        $this->emcom->persist($objInfoDetalleSolHist);

                        $objDetalleSolicitud->setEstado("Finalizado");
                        $this->emcom->persist($objInfoDetalleSolHist);
                        
                        
                        //Se busca la zona y se la cambia a estado Finalizado
                        $objCaracteristicaZona = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_ZONA_CHOFER_PROV);


                        $objDetalleSolCaracteristicaZona = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(
                                                        array(
                                                            "detalleSolicitudId"=> $objDetalleSolicitud,
                                                            "caracteristicaId"  => $objCaracteristicaZona,
                                                            "estado"            => $strEstadoActivo
                                                            )
                                                        );
                        if($objDetalleSolCaracteristicaZona)
                        {
                            $objDetalleSolCaracteristicaZona->setEstado('Finalizada');
                            $this->emcom->persist($objDetalleSolCaracteristicaZona);
                            $this->emcom->flush();
                        }

                        //Se busca la tarea y se la cambia a estado Finalizado
                        $objCaracteristicaTarea = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_TAREA_CHOFER_PROV);


                        $objDetalleSolCaracteristicaTarea = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(
                                                        array(
                                                            "detalleSolicitudId"=> $objDetalleSolicitud,
                                                            "caracteristicaId"  => $objCaracteristicaTarea,
                                                            "estado"            => $strEstadoActivo
                                                            )
                                                        );
                        if($objDetalleSolCaracteristicaTarea)
                        {
                            $objDetalleSolCaracteristicaTarea->setEstado('Finalizada');
                            $this->emcom->persist($objDetalleSolCaracteristicaTarea);
                            $this->emcom->flush();
                        }


                        $objCaracteristicaDepartamento = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_CHOFER_PROV);


                        $objDetalleSolCaracteristicaDepartamento = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(
                                                        array(
                                                            "detalleSolicitudId"=> $objDetalleSolicitud,
                                                            "caracteristicaId"  => $objCaracteristicaDepartamento,
                                                            "estado"            => $strEstadoActivo
                                                            )
                                                        );
                        if($objDetalleSolCaracteristicaDepartamento)
                        {
                            $objDetalleSolCaracteristicaDepartamento->setEstado('Finalizada');
                            $this->emcom->persist($objDetalleSolCaracteristicaDepartamento);
                            $this->emcom->flush();
                        }
                        
                    }
                    
                    //Crear Historiales de eliminación de Chofer provisional
                    $objCuadrillaHistorialProvisional = new AdmiCuadrillaHistorial();
                    $objCuadrillaHistorialProvisional->setCuadrillaId($objCuadrilla);
                    $objCuadrillaHistorialProvisional->setEstado($objCuadrilla->getEstado());
                    $objCuadrillaHistorialProvisional->setFeCreacion($datetimeActual);
                    $objCuadrillaHistorialProvisional->setUsrCreacion($strUserSession);
                    $objCuadrillaHistorialProvisional->setObservacion($strMensajeObservacionProvisional.$strFechasHorasAsignacionProv);
                    $objCuadrillaHistorialProvisional->setMotivoId($intIdMotivoChoferProvisional);
                    $this->emcom->persist($objCuadrillaHistorialProvisional);
                    $this->emcom->flush();
                    
                    $strMensajeObservacionProvisional.="por la cuadrilla ".$strNombreCuadrilla."<br/>";
                    $objInfoHistorialElemento = new InfoHistorialElemento();
                    $objInfoHistorialElemento->setElementoId($objElemento);
                    $objInfoHistorialElemento->setObservacion($strMensajeObservacionProvisional.$strFechasHorasAsignacionProv);
                    $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                    $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                    $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                    $objInfoHistorialElemento->setEstadoElemento($strEstadoActivo);
                    $this->emInfraestructura->persist($objInfoHistorialElemento);
                    $this->emInfraestructura->flush();
                }

                $strMensaje = 'OK';
                $this->emInfraestructura->getConnection()->commit();
                $this->emcom->getConnection()->commit();
            }
            else
            {
                $strMensaje = 'No existe la asociación';
            }

            $this->emInfraestructura->getConnection()->close(); 
            $this->emcom->getConnection()->close();
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
            
            $strMensaje = 'Hubo un problema de base de datos, por favor contactar con el departamento de sistemas';
            
            $this->emInfraestructura->getConnection()->rollback();
            $this->emInfraestructura->getConnection()->close();

            $this->emcom->getConnection()->rollback();
            $this->emcom->getConnection()->close();
        }
        
        return $strMensaje;
        
    }
    
    /**
     * haversineGreatCircleDistance, Calculates the great-circle distance between two points, with
     * the Haversine formula.
     * 
     * @param array $arrayParametros[
     *                              'floatLatitudFrom' => Latitude of start point in [deg decimal]
     *                              'floatLongitudFrom' => Longitude of start point in [deg decimal]
     *                              'floatLatitudTo' => Latitude of target point in [deg decimal]
     *                              'floatLongitudTo' => Longitude of target point in [deg decimal]
     *                              'intEarthRadius' => Mean earth radius in [m], empty default: 6371000
     *                              ]
     * 
     * @return float Distance between points in [m] (same as earthRadius)
     */
    function haversineGreatCircleDistance($arrayParametros)
    {
        $floatLatitudFrom   = deg2rad($arrayParametros['floatLatitudFrom']);
        $floatLongitudFrom  = deg2rad($arrayParametros['floatLongitudFrom']);
        $floatLatitudTo     = deg2rad($arrayParametros['floatLatitudTo']);
        $floatLongitudTo    = deg2rad($arrayParametros['floatLongitudTo']);
        $intEarthRadius     = $arrayParametros['intEarthRadius'];
        if(empty($arrayParametros['intEarthRadius']))
        {
            $intEarthRadius = 6371000;
        }

        $floatLatitudDelta  = $floatLatitudTo - $floatLatitudFrom;
        $floatLongitudDelta = $floatLongitudTo - $floatLongitudFrom;

        $floatAngle = 2 * asin(sqrt(pow(sin($floatLatitudDelta / 2), 2) + 
                      cos($floatLatitudFrom) * cos($floatLatitudTo) * pow(sin($floatLongitudDelta / 2), 2)));
        return $floatAngle * $intEarthRadius;
    }
    
 
    /**
     * Funcion que sirve para actualizar el estado de una interface de un elemento
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 15-11-2016
     *
     * @param Array $arrayDatos [
     *      usrCreacion              Usuario que lanza la petición
     *      ipCreacion               Ip de donde nace la petición
     *      nombreElemento           Nombre del elemento
     *      nombreInterfaceElemento  Nuevo nombre que actualizará el elemento actual
     *      estadoInterface          estado del elemento a actualizar
     * ]
     * @return Array $arrayRespuesta [ status , mensaje ]
     */
    public function actualizarInterfaceElemento($arrayDatos)
    {
        $strUsrCreacion = $arrayDatos['usrCreacion'];
        $strIpCreacion  = filter_var($arrayDatos['ipCreacion'], FILTER_VALIDATE_IP);
        if(empty($strIpCreacion))
        {
            return array('status' => 'ERROR',
                'mensaje' => $arrayDatos['ipCreacion'] . ' no es un ip valida."'
            );
        }
        $this->emInfraestructura->getConnection()->beginTransaction();
        try
        {
            $strNombreElemento          = $arrayDatos['nombreElemento'];
            $strNombreInterfaceElemento = $arrayDatos['nombreInterfaceElemento'];
            $strEstadoInterface         = $arrayDatos['estadoInterface'];
            
            //solo permito el ingreso de 2 tipos de estados
            $arrayEstado = array("connected", "not connect");
            if (!in_array($strEstadoInterface, $arrayEstado)) {
                throw new \Exception("Solo es permitido estado 'connected' o 'not connect' .");
            }
            if(empty($strNombreElemento))
            {
                throw new \Exception('No se ha recibido el nombre del elemento.');
            }
            if(empty($strNombreInterfaceElemento))
            {
                throw new \Exception('No se ha recibido el nombre de la interface del elemento.');
            }
            
            $objElemento = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->findOneByNombreElemento($strNombreElemento);
            
            if(is_object($objElemento))
            {
                $objInterfaceElemento = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                ->findOneBy(array(  'elementoId'                => $objElemento->getId(),
                                                                                    'nombreInterfaceElemento'   => $strNombreInterfaceElemento));
                if(is_object($objInterfaceElemento))
                {
                    //procedo con la actualización
                    $objInterfaceElemento->setEstado($strEstadoInterface);
                    $objInterfaceElemento->setUsrCreacion($strUsrCreacion);
                    $objInterfaceElemento->setIpCreacion($strIpCreacion);
                    $objInterfaceElemento->setFeCreacion(new \DateTime('now'));
                    
                    $this->emInfraestructura->persist($objInterfaceElemento);
                    $this->emInfraestructura->flush();
                    
                    $arrayRespuesta = array('status'  => 'OK',
                                            'mensaje' => 'El puerto fue actualizado correctamente.'); 
                    
                }
                else
                {
                    throw new \Exception('No existe la interface en el telcos.');        
                }
                
            }
            else
            {
                throw new \Exception('No existe el elemento en el telcos.');            
            }
            
            $this->emInfraestructura->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();                
            }
            $this->emInfraestructura->getConnection()->close();
            $arrayRespuesta = array(
                'status'  => 'ERROR',
                'mensaje' => 'Error: '.$e->getMessage()
            );
        }
        return $arrayRespuesta;
    }
    
    
  
    /**
     * Obtener el estado de la interface
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 15-11-2016
     *
     * @param Array $arrayDatos [
     *      nombreElemento           Nombre del elemento
     *      nombreInterfaceElemento  Nuevo nombre que actualizará el elemento actual
     * ]
     * @return Array $arrayRespuesta [ status , mensaje ]
     */
    public function getEstadoInterfaceElemento($arrayDatos)
    {

        try
        {
            $strNombreElemento          = $arrayDatos['nombreElemento'];
            $strNombreInterfaceElemento = $arrayDatos['nombreInterfaceElemento'];
            
            if(empty($strNombreElemento))
            {
                throw new \Exception('No se ha recibido el nombre del elemento.');
            }
            if(empty($strNombreInterfaceElemento))
            {
                throw new \Exception('No se ha recibido el nombre de la interface del elemento.');
            }
            
            $objElemento = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->findOneByNombreElemento($strNombreElemento);
            
            if(is_object($objElemento))
            {
                $objInterfaceElemento = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                ->findOneBy(array(  'elementoId'                => $objElemento->getId(),
                                                                                    'nombreInterfaceElemento'   => $strNombreInterfaceElemento));
                if(is_object($objInterfaceElemento))
                {
                    
                    $arrayRespuesta = array('status'  => 'OK',
                                            'mensaje' => $objInterfaceElemento->getEstado()); 
                    
                }
                else
                {
                    throw new \Exception('No existe la interface en el telcos.');        
                }
                
            }
            else
            {
                throw new \Exception('No existe el elemento en el telcos.');            
            }            

        }
        catch(\Exception $e)
        {
            $arrayRespuesta = array(
                'status'  => 'ERROR',
                'mensaje' => 'Error: '.$e->getMessage()
            );
        }
        return $arrayRespuesta;
    }    
    
  
    /**
     * Funcion que sirve para realizar el ingreso de un nuevo registro de elemento SWITCH/ROUTER
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 20-09-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 24-04-2017 - Se ajusta creacion de elemento con validacion si un elemento pertenece o será usado en un esquema PseudoPe
     *                         - Se agrega creacion de catalogo de VLANS en creacion de nuevos ROUTERs
     *                         - Se agrega parametro de inicio de subred para subredes creados relacionadas a un determinado ROUTER
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 23-05-2017 - Se agrega detalle que referencia si un switch es Virtual o no, o si es Hibrido o No
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 28-06-2017 - Se parametriza primer y tercer octeto y uso de subred en la generacion de una nueva subred
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 21-06-2018 - Se recibe parametro que determina que tipo de prefijo es la red nueva ingresada ligada a un Pe
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 19-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * 
     * @author Anthony Santillan <asantillany@telconet.ec>
     * @version 1.5 30-09-2022 - Se agrega una validacion para la serie del objeto que se ingreso para crearlo o actualizarlo 
     * 
     * @param  Array $arrayDatos [
     *                              tipoElemento,     Tipo del Elemento SWITCH/ROUTER
     *                              nombre,           Nombre del elemento a ser creado
     *                              ip,               Ip del elemento a ser creado
     *                              modelo,           Modelo del elemento a ser creado
     *                              prefijoRed,       Prefijo de Red ( ROUTER ) 
     *                              numeroFinRed,     numero donde termina la subred dado un prefijo de red
     *                              anillo,           Anillo al cual será relacionado un SWITCH
     *                              tipo,             Tipo de donde proviene un elemento (Backbone o Edificio)
     *                              idNodo,           id del Nodo al cual esta ligado el elemento
     *                              idRack,           id del Rack en el cual estara ubicado el elemento dentro del nodo
     *                              idUdRack,         id de la posicion del rack donde estara ubicado el elemento dentro del rack
     *                              serie,            Serie fisica del elemento
     *                              numeroModulo,     Numero de modulo dado una interfac del elemento a crear
     *                              stacks[ nombreStack , modeloStack , numeroModuloStack , serieStack ],
     *                              usrCreacion,      Usuario de creacion , quien ejecuta la accion
     *                              ipCreacion        Ip de quien ejecuta la accion
     *                           ]
     * @return Array [ status , mensaje ]
     */
    function crearElementoSwitchRouterTN($arrayDatos)        
    {
        $strUsrCreacion = $arrayDatos['usrCreacion'];
        $ipCreacion     = filter_var($arrayDatos['ipCreacion'],FILTER_VALIDATE_IP);   
        $boolEsPseudoPe = $arrayDatos['esEsquemaPseudoPe']=='S'?true:false;
        $boolEsHibrido  = $arrayDatos['esHibrido']=='S'?true:false;
        
        if(empty($ipCreacion))
        {           
            return array(    'status'  => 'ERROR',
                             'mensaje' => $ipCreacion . ' no es un ip valida."'
                            );
        }
                
        $this->emInfraestructura->getConnection()->beginTransaction();
        
        try
        {  
            $objTipoElemento   = $this->emInfraestructura->getRepository("schemaBundle:AdmiTipoElemento")
                                                         ->findOneByNombreTipoElemento($arrayDatos['tipoElemento']);
                        
            if(!$objTipoElemento)
            {                
                return array('status'  => 'ERROR',
                             'mensaje' => 'No existe el tipo de elemento '.$arrayDatos['tipoElemento']
                            );
            }
                       
            $objModeloElemento = $this->emInfraestructura->getRepository("schemaBundle:AdmiModeloElemento")
                                                         ->findOneBy(array('nombreModeloElemento' => $arrayDatos['modelo'],
                                                                           'tipoElementoId'       => $objTipoElemento->getId(),
                                                                           'estado'               => 'Activo')
                                                                     );
            
            $objEmpresa        = $this->emInfraestructura->getRepository("schemaBundle:InfoEmpresaGrupo")->findOneByPrefijo("TN");
            
            if(!$objModeloElemento)
            {                
                return array('status'  => 'ERROR',
                             'mensaje' => 'No existe modelo de Switch a ser creado'
                            );
            }
            
            if(!$boolEsPseudoPe)
            {
                //Se valida que al menos siempre exista el NODO físico donde estará ubicado un elemento de Backbone
                if(empty($arrayDatos['idNodo']) || $arrayDatos['idNodo'] == null)
                {
                    return array('status'  => 'ERROR',
                                 'mensaje' => 'No se puede ubicar el elemento en una Ubicación correcta, ya que no existe NODO de referencia'
                                 );
                }
            }
            
            $objElemento       = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                         ->findOneBy(array(
                                                                        'serieFisica' => $arrayDatos['serie'],
                                                                        'estado'      => 'Activo')
                                                                    );

            //Se valida que el elemento existe o no en la base de datos
            if(empty($objElemento))
            {
                $objInfoElemento   = new InfoElemento();
                $objInfoElemento->setNombreElemento($arrayDatos['nombre']);
                $objInfoElemento->setDescripcionElemento("Dispositivo Telco");
                $objInfoElemento->setModeloElementoId($objModeloElemento);
                $objInfoElemento->setUsrResponsable($strUsrCreacion);
                $objInfoElemento->setUsrCreacion($strUsrCreacion);
                $objInfoElemento->setFeCreacion(new \DateTime('now'));
                $objInfoElemento->setSerieFisica($arrayDatos['serie']);
                $objInfoElemento->setIpCreacion($ipCreacion);
                $objInfoElemento->setEstado("Activo");
                $this->emInfraestructura->persist($objInfoElemento);
                $this->emInfraestructura->flush();
            }
            else 
            {
                //Se actualiza el nombre_elemento en la tabla infoElemento
                $objElemento->setNombreElemento($arrayDatos['nombre']);
                $this->emInfraestructura->persist($objElemento);
                $this->emInfraestructura->flush();
                $objInfoElemento = $objElemento;
            }
                                                                    
            
            //Se genera el catalogo de VLANs para el respectivo PE
            if($arrayDatos['tipoElemento'] == 'ROUTER')
            {
                $this->emInfraestructura->commit();
                $this->emInfraestructura->getConnection()->beginTransaction();
                
                $arrayParametrosVlans                     = array();
                $arrayParametrosVlans['intIdElementoPe']  = $objInfoElemento->getId();
                $arrayParametrosVlans['strUsrCreacion']   = $strUsrCreacion;

                $strMensajeError = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                           ->crearCatalogoVlansPe($arrayParametrosVlans);
                
                if(!empty($strMensajeError) || $strMensajeError!=null)
                {
                    $this->emInfraestructura->remove($objInfoElemento);
                    $this->emInfraestructura->flush();
                    $this->emInfraestructura->commit();
                    $this->emInfraestructura->getConnection()->close();                            
                    return array('status'  => 'ERROR',
                                 'mensaje' => $strMensajeError
                                );
                }
            }
            
            //ip elemento
            $objIp = new InfoIp();
            $objIp->setElementoId($objInfoElemento->getId());
            $objIp->setIp(trim($arrayDatos['ip']));
            $objIp->setVersionIp("IPV4");
            $objIp->setEstado("Activo");
            $objIp->setUsrCreacion($strUsrCreacion);
            $objIp->setFeCreacion(new \DateTime('now'));
            $objIp->setIpCreacion($ipCreacion);
            $this->emInfraestructura->persist($objIp);

            //historial elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objInfoElemento);
            $objHistorialElemento->setEstadoElemento("Activo");
            $objHistorialElemento->setObservacion("Se ingreso un nuevo ".$arrayDatos['tipoElemento']);
            $objHistorialElemento->setUsrCreacion($strUsrCreacion);
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setIpCreacion($ipCreacion);
            $this->emInfraestructura->persist($objHistorialElemento);
                        
            //Se crean las interfaces al elemento
            $arrayInterfaceModelo = $this->emInfraestructura->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                            ->findBy(array("modeloElementoId" => $objModeloElemento->getId()));
            
            foreach($arrayInterfaceModelo as $objInterface)
            {                
                $intCantidadInterfaces = $objInterface->getCantidadInterface();
                $strFormato            = $objInterface->getFormatoInterface();
                
                $objTipoInterface   = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoInterface')
                                                              ->find($objInterface->getTipoInterfaceId());
                
                if(strpos($strFormato, "¿")!==false)
                {                   
                    $strFormato = str_replace("¿", $arrayDatos['numeroModulo'], $strFormato);
                }
                                
                //Se crean las interfaces para el elemento
                for($intSlot = 1 ; $intSlot <= $intCantidadInterfaces ; $intSlot++ )
                {
                    $objInterfaceElemento       = new InfoInterfaceElemento();
                    
                    $format                     = explode("?", $strFormato);
                    $nombreInterfaceElemento    = $format[0] . $intSlot;
                    
                    $objInterfaceElemento->setNombreInterfaceElemento($nombreInterfaceElemento);
                    $objInterfaceElemento->setDescripcionInterfaceElemento($objTipoInterface?$objTipoInterface->getNombreTipoInterface():"");
                    $objInterfaceElemento->setElementoId($objInfoElemento);
                    $objInterfaceElemento->setEstado("not connect");
                    $objInterfaceElemento->setUsrCreacion($strUsrCreacion);
                    $objInterfaceElemento->setFeCreacion(new \DateTime('now'));
                    $objInterfaceElemento->setIpCreacion($ipCreacion);
                    $this->emInfraestructura->persist($objInterfaceElemento);
                }
            }            
                               
            //Si es pseudope NO es necesario ingresar informacion de Ubicacion de elementos
            if(!$boolEsPseudoPe)
            {
                //Si no es hibrido crea el posicionamiento de manera convencional
                if(!$boolEsHibrido)
                {
                    if($arrayDatos['idRack'])
                    {
                        $objElementoRack  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayDatos['idRack']);

                        if($objElementoRack)
                        {
                            $objInterfaceModeloRack = $this->emInfraestructura->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                           ->findOneBy(array("modeloElementoId" => $objElementoRack->getModeloElementoId()));

                            if(!$objInterfaceModeloRack)
                            {
                                $this->emInfraestructura->getConnection()->rollback();
                                $this->emInfraestructura->getConnection()->close();
                                return array('status'  => 'ERROR',
                                             'mensaje' => 'No existe interfaces relacionadas al modelo del Rack '.$objElementoRack->getNombreElemento()
                                            );
                            }

                            $objElementoUnidadRack  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                              ->find($arrayDatos['idUdRack']);

                            $intUnidadMaximaU           = (int) $objElementoUnidadRack->getNombreElemento() + 
                                                          (int) $objModeloElemento->getURack() - 1;                                  

                            if($intUnidadMaximaU > $objInterfaceModeloRack->getCantidadInterface())
                            {
                                $this->emInfraestructura->getConnection()->rollback();
                                $this->emInfraestructura->getConnection()->close();                           
                                return array('status'  => 'ERROR',
                                             'mensaje' => 'No se puede ubicar el Switch en el Rack porque se sobrepasa el tamaño de unidades!');
                            }

                            //obtener todas las unidades del rack
                            $objRelacionesElementoUDRack = $this->emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                                ->findBy(array("elementoIdA" => $arrayDatos['idRack'],
                                                                               "estado"      => "Activo")
                                                                               );
                            $strUnidadesOcupadas = "";                  

                            //se verifica disponibilidad de unidades y se asignan recursos
                            for($t = (int)$objElementoUnidadRack->getNombreElemento(); $t <= $intUnidadMaximaU; $t++)
                            {                        
                                $intElementoUnidadId     = 0;
                                $objRelacionElementoRack = null;
                                foreach($objRelacionesElementoUDRack as $objRelacionElementoUDRack)
                                {
                                    $objElementoUnidadRackDet      = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                          ->find($objRelacionElementoUDRack->getElementoIdB());

                                    if ((int)$objElementoUnidadRackDet->getNombreElemento() == $t)
                                    {
                                        $intElementoUnidadId = $objElementoUnidadRackDet->getId();
                                    }
                                }
                                $objRelacionElementoRack = $this->emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                                ->findOneBy(array("elementoIdA"             => $intElementoUnidadId,
                                                                                  "estado"                  => "Activo"
                                                                                 )
                                                                           );
                                if($objRelacionElementoRack)
                                {
                                    if ($strUnidadesOcupadas == "")
                                    {
                                        $strUnidadesOcupadas = $t;
                                    }
                                    else
                                    {
                                        $strUnidadesOcupadas = $strUnidadesOcupadas . " , " . $t;
                                    }
                                }
                                else
                                {                                
                                    //relacion elemento
                                    $objRelacionElemento = new InfoRelacionElemento();
                                    $objRelacionElemento->setElementoIdA($intElementoUnidadId);
                                    $objRelacionElemento->setElementoIdB($objInfoElemento->getId());
                                    $objRelacionElemento->setTipoRelacion("CONTIENE");
                                    $objRelacionElemento->setObservacion("Rack contiene ".ucwords(strtolower($arrayDatos['tipoElemento'])));
                                    $objRelacionElemento->setEstado("Activo");
                                    $objRelacionElemento->setUsrCreacion($strUsrCreacion);
                                    $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                                    $objRelacionElemento->setIpCreacion($ipCreacion);
                                    $this->emInfraestructura->persist($objRelacionElemento);
                                }
                            }
                            if($strUnidadesOcupadas != "")
                            {
                                $this->emInfraestructura->getConnection()->rollback();
                                $this->emInfraestructura->getConnection()->close();
                                return array('status'  => 'ERROR',
                                             'mensaje' => 'No se puede ubicar el Switch en el Rack porque estan ocupadas las unidades : ' . 
                                                          $strUnidadesOcupadas
                                            );
                            }
                        }
                    }
                    else
                    {                                        
                        $objRelacionElemento = new InfoRelacionElemento();
                        $objRelacionElemento->setElementoIdA($arrayDatos['idNodo']);
                        $objRelacionElemento->setElementoIdB($objInfoElemento->getId());
                        $objRelacionElemento->setTipoRelacion("CONTIENE");
                        $objRelacionElemento->setObservacion("Nodo contiene ".ucwords(strtolower($arrayDatos['tipoElemento'])));
                        $objRelacionElemento->setEstado("Activo");
                        $objRelacionElemento->setUsrCreacion($strUsrCreacion);
                        $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                        $objRelacionElemento->setIpCreacion($ipCreacion);
                        $this->emInfraestructura->persist($objRelacionElemento);
                    }
                }
                else //Si es Hibrido hereda la posicion enviada por parametro del pe referencial
                {
                    if($arrayDatos['idUdRack'])
                    {
                        $objRelacionElemento = new InfoRelacionElemento();
                        $objRelacionElemento->setElementoIdA($arrayDatos['idUdRack']);
                        $objRelacionElemento->setElementoIdB($objInfoElemento->getId());
                        $objRelacionElemento->setTipoRelacion("CONTIENE");
                        $objRelacionElemento->setObservacion("Rack contiene ".ucwords(strtolower($arrayDatos['tipoElemento'])));
                        $objRelacionElemento->setEstado("Activo");
                        $objRelacionElemento->setUsrCreacion($strUsrCreacion);
                        $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                        $objRelacionElemento->setIpCreacion($ipCreacion);
                        $this->emInfraestructura->persist($objRelacionElemento);
                    }
                }

                //tomar datos nodo
                $nodoEmpresaElementoUbicacion = $this->emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                     ->findOneBy(array("elementoId" => $arrayDatos['idNodo']));

                if($nodoEmpresaElementoUbicacion)
                {                
                    $objUbicacion = $this->emInfraestructura->getRepository('schemaBundle:InfoUbicacion')
                                                            ->find($nodoEmpresaElementoUbicacion->getUbicacionId()->getId());

                    if($objUbicacion)
                    {
                        $arrayRespuestaCoordenadas  = $this->validarLimitesCoordenadasElemento(array(
                                                                                                        "latitudElemento"       => 
                                                                                                        $objUbicacion->getLatitudUbicacion(),
                                                                                                        "longitudElemento"      => 
                                                                                                        $objUbicacion->getLongitudUbicacion(),
                                                                                                        "msjTipoElemento"       => "del nodo",
                                                                                                        "msjTipoElementoPadre"  =>
                                                                                                        "que contiene al "
                                                                                                        .$arrayDatos['tipoElemento']." ",
                                                                                                        "msjAdicional"          => 
                                                                                                        "por favor regularizar en la administración"
                                                                                                        ." de Nodos"
                                                                                                     ));
                        if($arrayRespuestaCoordenadas["status"] === "ERROR")
                        {
                            throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                        }
                        //info ubicacion
                        $objParroquia = $this->emInfraestructura->find('schemaBundle:AdmiParroquia', $objUbicacion->getParroquiaId());
                        $objUbicacionElemento = new InfoUbicacion();
                        $objUbicacionElemento->setLatitudUbicacion($objUbicacion->getLatitudUbicacion());
                        $objUbicacionElemento->setLongitudUbicacion($objUbicacion->getLongitudUbicacion());
                        $objUbicacionElemento->setDireccionUbicacion($objUbicacion->getDireccionUbicacion());
                        $objUbicacionElemento->setAlturaSnm($objUbicacion->getAlturaSnm());
                        $objUbicacionElemento->setParroquiaId($objParroquia);
                        $objUbicacionElemento->setUsrCreacion($strUsrCreacion);
                        $objUbicacionElemento->setFeCreacion(new \DateTime('now'));
                        $objUbicacionElemento->setIpCreacion($ipCreacion);
                        $this->emInfraestructura->persist($objUbicacionElemento);

                        $objEmpresaUbica    = $this->emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                                      ->findOneBy(array("elementoId" => $objInfoElemento->getId()));
                        
                        // si el elemento ya existe en la tabla InfoEmpresaElementoUbica no lo vuelve a crear
                        if(empty($objEmpresaUbica))
                        {
                            //empresa elemento ubicacion
                            $objEmpresaElementoUbica = new InfoEmpresaElementoUbica();
                            $objEmpresaElementoUbica->setEmpresaCod($objEmpresa->getId());
                            $objEmpresaElementoUbica->setElementoId($objInfoElemento);
                            $objEmpresaElementoUbica->setUbicacionId($objUbicacionElemento);
                            $objEmpresaElementoUbica->setUsrCreacion($strUsrCreacion);
                            $objEmpresaElementoUbica->setFeCreacion(new \DateTime('now'));
                            $objEmpresaElementoUbica->setIpCreacion($ipCreacion);
                            $this->emInfraestructura->persist($objEmpresaElementoUbica);
                        }

                          
                        $objEmpreElemento        = $this->emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                                                      ->findOneBy(array(
                                                                                        "elementoId" => $objInfoElemento->getId(),
                                                                                        "estado"     => "Activo"
                                                                                ));
                        
                        // si el elemento ya existe en la tabla InfoEmpresaElemento no lo vuelve a crear
                        if(empty($objEmpreElemento))
                        {
                            //empresa elemento
                            $objEmpresaElemento = new InfoEmpresaElemento();
                            $objEmpresaElemento->setElementoId($objInfoElemento);
                            $objEmpresaElemento->setEmpresaCod($objEmpresa->getId());
                            $objEmpresaElemento->setEstado("Activo");
                            $objEmpresaElemento->setUsrCreacion($strUsrCreacion);
                            $objEmpresaElemento->setIpCreacion($ipCreacion);
                            $objEmpresaElemento->setFeCreacion(new \DateTime('now'));
                            $this->emInfraestructura->persist($objEmpresaElemento);
                        }
                    }
                }
            }
                       
            //Se obtiene la información de Stack en caso de existir para crear un nuevo Switch
            if($arrayDatos['tipoElemento'] == 'SWITCH')
            {
                $objDetalleElemento = new InfoDetalleElemento();
                $objDetalleElemento->setElementoId($objInfoElemento->getId());
                $objDetalleElemento->setDetalleNombre("ANILLO");
                $objDetalleElemento->setDetalleValor($arrayDatos['anillo']);
                $objDetalleElemento->setDetalleDescripcion("ANILLO");
                $objDetalleElemento->setUsrCreacion($strUsrCreacion);
                $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                $objDetalleElemento->setIpCreacion($ipCreacion);
                $objDetalleElemento->setEstado("Activo");
                $this->emInfraestructura->persist($objDetalleElemento);
                
                //Verificar si es enviado información de stack nuevo para agregar las nuevas interfaces al switch creado                
                $arrayStacks = $arrayDatos['stacks'];
                
                //Si existen elementos de stack a crear se realiza la agregación de interfaces al switch de acuerdo al modelo del stack requerido
                if($arrayStacks)
                {
                    foreach($arrayStacks as $stack)
                    {
                        $strNombreStack       = $stack['stack']['nombreStack'];
                        $strSerieStack        = $stack['stack']['serieStack'];
                        $strNombreModeloStack = $stack['stack']['modeloStack'];
                        $intModuloStack       = $stack['stack']['numeroModuloStack']; 
                        
                        $objElementoStack = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                                        ->findOneBy(array('nombreElemento' => $strNombreStack,
                                                                                          'serieFisica'    => $strSerieStack,
                                                                                          'estado'         => 'Activo'));
                        
                        //Se valida si existe o no la tarjeta ya ingresada
                        if($objElementoStack)
                        {
                            $this->emInfraestructura->getConnection()->rollback();
                            $this->emInfraestructura->getConnection()->close();                            
                            return array('status'  => 'ERROR',
                                         'mensaje' => 'La Trajeta '.$strNombreStack.' ya existe en ingresada en el Sistema'
                                        );
                        }
                        
                        $objModeloStack = $this->emInfraestructura->getRepository("schemaBundle:AdmiModeloElemento")
                                                                  ->findOneByNombreModeloElemento($strNombreModeloStack);                        
                        if($objModeloStack)
                        {
                            $objElementoStack = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                                        ->findOneBy(array('serieFisica'     => $strSerieStack,
                                                                                          'nombreElemento'  => $strNombreStack,
                                                                                          'estado'          => 'Activo'));
                            //Se valida si existe o no la tarjeta ya ingresada
                            if($objElementoStack)
                            {
                                $this->emInfraestructura->getConnection()->rollback();
                                $this->emInfraestructura->getConnection()->close();                             
                                return array('status'  => 'ERROR',
                                             'mensaje' => 'Ya existe una Tarjeta con la serie '.$strSerieStack.' en el Sistema'
                                            );
                            }
                            
                            $objInfoElementoStack   = new InfoElemento();
                            $objInfoElementoStack->setNombreElemento($strNombreStack);
                            $objInfoElementoStack->setDescripcionElemento("Dispositivo Telco - Tarjeta Switch");
                            $objInfoElementoStack->setModeloElementoId($objModeloStack);
                            $objInfoElementoStack->setUsrResponsable($strUsrCreacion);
                            $objInfoElementoStack->setUsrCreacion($strUsrCreacion);
                            $objInfoElementoStack->setFeCreacion(new \DateTime('now'));
                            $objInfoElementoStack->setSerieFisica($strSerieStack);
                            $objInfoElementoStack->setIpCreacion($ipCreacion);
                            $objInfoElementoStack->setEstado("Activo");
                            $this->emInfraestructura->persist($objInfoElementoStack);
                            $this->emInfraestructura->flush();
                            
                            $objHistorialElementoStack = new InfoHistorialElemento();
                            $objHistorialElementoStack->setElementoId($objInfoElementoStack);
                            $objHistorialElementoStack->setEstadoElemento("Activo");
                            $objHistorialElementoStack->setObservacion("Se ingresó una nueva TARJETA al Switch ".$arrayDatos['nombre']);
                            $objHistorialElementoStack->setUsrCreacion($strUsrCreacion);
                            $objHistorialElementoStack->setFeCreacion(new \DateTime('now'));
                            $objHistorialElementoStack->setIpCreacion($ipCreacion);
                            $this->emInfraestructura->persist($objHistorialElementoStack);
                            
                            $objRelacionElemento = new InfoRelacionElemento();
                            $objRelacionElemento->setElementoIdA($objInfoElemento->getId());
                            $objRelacionElemento->setElementoIdB($objInfoElementoStack->getId());
                            $objRelacionElemento->setTipoRelacion("CONTIENE");
                            $objRelacionElemento->setObservacion("Switch contiene Tarjeta");
                            $objRelacionElemento->setEstado("Activo");
                            $objRelacionElemento->setUsrCreacion($strUsrCreacion);
                            $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                            $objRelacionElemento->setIpCreacion($ipCreacion);
                            $this->emInfraestructura->persist($objRelacionElemento);
                        
                            $arrayInterfaceModeloStack = $this->emInfraestructura->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                              ->findBy(array("modeloElementoId" => $objModeloStack->getId()));
            
                            //Se agregan nuevas interfaces al switch que contiene la nueva tarjeta de red
                            foreach($arrayInterfaceModeloStack as $interfaceStack)
                            {
                                $cantidadInterfaces = $interfaceStack->getCantidadInterface();
                                $strFormato         = $interfaceStack->getFormatoInterface();

                                $objTipoInterfaceStack   = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoInterface')
                                                                                   ->find($interfaceStack->getTipoInterfaceId());

                                $strFormatoModulo        = str_replace("¿", $intModuloStack, $strFormato);
                                
                                //Se crean las interfaces para el elemento
                                for($slot = 1 ; $slot <= $cantidadInterfaces ; $slot++ )
                                {
                                    $objInterfaceElemento       = new InfoInterfaceElemento();

                                    $format                     = explode("?", $strFormatoModulo);
                                    $nombreInterfaceElemento    = $format[0] . $slot;                                                                        

                                    $objInterfaceElemento->setNombreInterfaceElemento($nombreInterfaceElemento);
                                    $objInterfaceElemento->setDescripcionInterfaceElemento($objTipoInterfaceStack?
                                                                                           $objTipoInterfaceStack->getNombreTipoInterface():"");
                                    $objInterfaceElemento->setElementoId($objInfoElemento);
                                    $objInterfaceElemento->setEstado("not connect");
                                    $objInterfaceElemento->setUsrCreacion($strUsrCreacion);
                                    $objInterfaceElemento->setFeCreacion(new \DateTime('now'));
                                    $objInterfaceElemento->setIpCreacion($ipCreacion);
                                    $this->emInfraestructura->persist($objInterfaceElemento);
                                }
                            }
                        }
                        else
                        {
                            $this->emInfraestructura->getConnection()->rollback();
                            $this->emInfraestructura->getConnection()->close();                          
                            return array('status'  => 'ERROR',
                                         'mensaje' => 'No existe el modelo <b>'.$strNombreModeloStack.'</b> '
                                                      . 'de la Tarjeta que desea agregar al Switch'
                                        );
                        }
                    }
                }
                //Marcar Switch como parte de un esquema PseudoPe, switch se marca como Virtual
                if($boolEsPseudoPe)
                {
                    $objDetalleElemento = new InfoDetalleElemento();
                    $objDetalleElemento->setElementoId($objInfoElemento->getId());
                    $objDetalleElemento->setDetalleNombre("ES_SWITCH_VIRTUAL");
                    $objDetalleElemento->setDetalleValor('SI');
                    $objDetalleElemento->setDetalleDescripcion("ES_SWITCH_VIRTUAL");
                    $objDetalleElemento->setUsrCreacion($strUsrCreacion);
                    $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                    $objDetalleElemento->setIpCreacion($ipCreacion);
                    $objDetalleElemento->setEstado("Activo");
                    $this->emInfraestructura->persist($objDetalleElemento);
                }
                
                if($boolEsHibrido)
                {
                    $objDetalleElemento = new InfoDetalleElemento();
                    $objDetalleElemento->setElementoId($objInfoElemento->getId());
                    $objDetalleElemento->setDetalleNombre("ES_HIBRIDO");
                    $objDetalleElemento->setDetalleValor('SI');
                    $objDetalleElemento->setDetalleDescripcion("ES_HIBRIDO");
                    $objDetalleElemento->setUsrCreacion($strUsrCreacion);
                    $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                    $objDetalleElemento->setIpCreacion($ipCreacion);
                    $objDetalleElemento->setEstado("Activo");
                    $this->emInfraestructura->persist($objDetalleElemento);
                }
            }
            else //ROUTER - PE
            {
                $objDetalleElemento = new InfoDetalleElemento();
                $objDetalleElemento->setElementoId($objInfoElemento->getId());
                $objDetalleElemento->setDetalleNombre("PREFIJO_RED");
                $objDetalleElemento->setDetalleValor($arrayDatos['prefijoRed']);
                $objDetalleElemento->setDetalleDescripcion("PREFIJO RED");
                $objDetalleElemento->setUsrCreacion($strUsrCreacion);
                $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                $objDetalleElemento->setIpCreacion($ipCreacion);
                $objDetalleElemento->setEstado("Activo");
                $this->emInfraestructura->persist($objDetalleElemento);
                
                //SE BUSCA QUE SI NO EXISTE LA SUBRED PROCEDE A CREAR LA NUEVA VINCULADA AL PREFIJO DE RED
                $objDetalleElementoPrefijo = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                     ->findOneBy(
                                                                                array('detalleNombre' => 'PREFIJO_RED', 
                                                                                      'detalleValor'  => $arrayDatos['prefijoRed'], 
                                                                                      'estado'        => 'Activo'
                                                                                      )
                                                                                );
                //Si no se crea ejectar el script de creacion de subred
                if(!$objDetalleElementoPrefijo)
                {
                    if(isset($arrayDatos['prefijoRed']) && $arrayDatos['prefijoRed']!='' &&
                       isset($arrayDatos['numeroFinRed']) && $arrayDatos['numeroFinRed']!='' &&
                       isset($arrayDatos['numeroIniRed']) && $arrayDatos['numeroIniRed']!='' &&
                       isset($arrayDatos['tipoPrefijo']) && $arrayDatos['tipoPrefijo']!='' )
                    {
                    //llamar a procedure
                        $arrayParametros['prefijoRed']  = $arrayDatos['prefijoRed'];
                        $arrayParametros['inicioRed']   = $arrayDatos['numeroIniRed'];
                        $arrayParametros['finRed']      = $arrayDatos['numeroFinRed']; //Mascara establecida
                        $arrayParametros['primerOcteto']= '10';
                        $arrayParametros['tercerOcteto']= null;
                        $arrayParametros['uso']         = 'DATOSMPLS';
                        $arrayParametros['tipoPrefijo'] = isset($arrayDatos['tipoPrefijo'])?$arrayDatos['tipoPrefijo']:'';

                        $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->crearRedYSubred($arrayParametros);
                    }
                    else
                    {
                        $this->emInfraestructura->getConnection()->rollback();
                        $this->emInfraestructura->getConnection()->close();                        
                        return array('status'  => 'ERROR',
                                     'mensaje' => 'No existe Informacion de Prefijo de Red y Numero de Fin de Subred a crear o el Tipo del Prefijo '
                                                  . 'para poder asignarle al nuevo ROUTER'
                                        );
                    }
                }
                
                //Ingresar las VLANs segun el numero de anillo Asignado
                
                if($boolEsPseudoPe)
                {
                    //Si el elemento pertenece al esquema pseudoPe
                    $objDetalleElemento = new InfoDetalleElemento();
                    $objDetalleElemento->setElementoId($objInfoElemento->getId());
                    $objDetalleElemento->setDetalleNombre("ES_PSEUDO_PE");
                    $objDetalleElemento->setDetalleValor("SI");
                    $objDetalleElemento->setDetalleDescripcion("ES_PSEUDO_PE");
                    $objDetalleElemento->setUsrCreacion($strUsrCreacion);
                    $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                    $objDetalleElemento->setIpCreacion($ipCreacion);
                    $objDetalleElemento->setEstado("Activo");
                    $this->emInfraestructura->persist($objDetalleElemento);
                }
            } 
            
            //Si el elemento es de Tipo Backbone o Edificio
            $objDetalleElemento = new InfoDetalleElemento();
            $objDetalleElemento->setElementoId($objInfoElemento->getId());
            $objDetalleElemento->setDetalleNombre("DEPENDE_DE");
            $objDetalleElemento->setDetalleValor($arrayDatos['tipo']=='B'?'BACKBONE':'EDIFICIO');
            $objDetalleElemento->setDetalleDescripcion("DEPENDE_DE");
            $objDetalleElemento->setUsrCreacion($strUsrCreacion);
            $objDetalleElemento->setFeCreacion(new \DateTime('now'));
            $objDetalleElemento->setIpCreacion($ipCreacion);
            $objDetalleElemento->setEstado("Activo");
            $this->emInfraestructura->persist($objDetalleElemento);
            
            $arrayRespuesta = array(
                                    'status'  => 'OK',
                                    'mensaje' => 'El '.$arrayDatos['tipoElemento'].' fue creado exitosamente'
            );  
            
            $this->emInfraestructura->flush();
            $this->emInfraestructura->getConnection()->commit();            
        } 
        catch (\Exception $ex) 
        {            
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();  
                $this->emInfraestructura->getConnection()->close();
            }
            
            $this->utilService->insertError("Telcos+",
                                            "crearElementoSwitchRouterTN",
                                            $ex->getMessage(),
                                            $strUsrCreacion,
                                            $ipCreacion
                                    );
            
            $arrayRespuesta = array(
                                    'status'  => 'ERROR',
                                    'mensaje' => 'Error al crear registro de Nuevo Switch, por favor verificar.'
            );
        }                        
        
        return $arrayRespuesta;
    }        
    
    /**
     * Funcion que sirve para actualizar información definida en el SWITCH/ROUTER
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 21-09-2016
     * 
     * @param Array $arrayDatos [
     *                              usrCreacion        Usuario que lanza la petición
     *                              ipCreacion         Ip de donde nace la petición
     *                              nombreAnterior     Nombre del elemento anterior a ser actualizado
     *                              nombreNuevo        Nuevo nombre que actualizará el elemento actual
     *                              ip                 Ip a actualizar del nuevo equipo
     *                              tipo               Si es Tipo Backbone ( B ) o Edificio ( E )
     *                              modelo             Modelo a actualizar del nuevo equipo
     *                              tipoElemento       Tipo del elemento SWITCH/ROUTER
     *                           ]
     * @return Array $arrayRespuesta [ status , mensaje ]
     */
    public function editarElementoSwitchRouterTN($arrayDatos)
    {
        $strUsrCreacion = $arrayDatos['usrCreacion'];
        
        $ipCreacion     = filter_var($arrayDatos['ipCreacion'],FILTER_VALIDATE_IP);   
        
        if(empty($ipCreacion))
        {           
            return array(    'status'  => 'ERROR',
                             'mensaje' => $ipCreacion . ' no es un ip valida."'
                            );
        }
                
        $this->emInfraestructura->getConnection()->beginTransaction();
        
        try
        {
            $strNombreSwitchAnterior = $arrayDatos['nombreAnterior']; //Valor de equipo de BB a modificar
            $strNombreSwitchNuevo    = $arrayDatos['nombreNuevo']; 
            $strIp                   = $arrayDatos['ip'];
            $strModelo               = $arrayDatos['modelo'];
            $strTipoElemento         = $arrayDatos['tipoElemento'];
            $strTipo                 = $arrayDatos['tipo'];
            
            if(empty($strNombreSwitchNuevo))
            {
                $arrayRespuesta = array(
                                'status'  => 'ERROR',
                                'mensaje' => 'No se ha recibido información del nuevo nombre a Actualizar');
                return $arrayRespuesta;
            }
                        
            if($strNombreSwitchAnterior)
            {
                $objElemento = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                       ->findOneByNombreElemento($strNombreSwitchAnterior);
                $flagEditarElemento = false;
                
                if($objElemento)
                {
                    if($strNombreSwitchNuevo)
                    {
                        $objElemento->setNombreElemento($strNombreSwitchNuevo);   
                        $flagEditarElemento = true;
                    }

                    if(!empty($strIp))
                    {
                        $objIp = $this->emInfraestructura->getRepository("schemaBundle:InfoIp")
                                                         ->findOneBy(array('elementoId' => $objElemento->getId(),
                                                                           'estado'     => 'Activo')
                                                                    );                            
                        if($objIp)
                        {
                            $flagEditarElemento = true;
                            $objIp->setIp($strIp);
                            $this->emInfraestructura->persist($objIp);
                        }
                    }

                    if($strTipoElemento == 'ROUTER' && !empty($strModelo))
                    {
                        $objModeloElemento = $this->emInfraestructura->getRepository("schemaBundle:AdmiModeloElemento")
                                                                     ->findOneByNombreModeloElemento($strModelo);

                        if($objModeloElemento)
                        {
                            $objElemento->setModeloElementoId($objModeloElemento);
                            $flagEditarElemento = true;
                        }
                        else
                        {
                            $arrayRespuesta = array(
                                'status'  => 'ERROR',
                                'mensaje' => 'No se ha recibido información del nuevo modelo');
                        }
                    }

                    if($flagEditarElemento)
                    {
                        $this->emInfraestructura->persist($objElemento);

                        //historial elemento
                        $objHistorialElemento = new InfoHistorialElemento();
                        $objHistorialElemento->setElementoId($objElemento);
                        $objHistorialElemento->setEstadoElemento("Activo");
                        $objHistorialElemento->setObservacion("Se actualizó información del ".$strTipoElemento);
                        $objHistorialElemento->setUsrCreacion($strUsrCreacion);
                        $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                        $objHistorialElemento->setIpCreacion($ipCreacion);
                        $this->emInfraestructura->persist($objHistorialElemento);
                    }       
                    
                    if(!empty($strTipo))
                    {
                        $strElementoDepende = $strTipo=='B'?'BACKBONE':'EDIFICIO';
                        $objDetalleElemento = $this->emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                   ->findOneBy(array('elementoId'    =>  $objElemento->getId(),
                                                                     'detalleNombre' => 'DEPENDE_DE',
                                                                     'estado'        => 'Activo'
                                                                    ));
                        
                        if(!is_object($objDetalleElemento))
                        {
                            //Si el elemento es de Tipo Backbone o Edificio
                            $objDetalleElemento = new InfoDetalleElemento();
                            $objDetalleElemento->setElementoId($objElemento->getId());
                            $objDetalleElemento->setDetalleNombre("DEPENDE_DE");
                            $objDetalleElemento->setDetalleValor($strElementoDepende);
                            $objDetalleElemento->setDetalleDescripcion("DEPENDE_DE");
                            $objDetalleElemento->setUsrCreacion($strUsrCreacion);
                            $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                            $objDetalleElemento->setIpCreacion($ipCreacion);
                            $objDetalleElemento->setEstado("Activo");
                            $this->emInfraestructura->persist($objDetalleElemento);
                        }
                        else
                        {
                            $objDetalleElemento->setDetalleValor($strElementoDepende);
                            $this->emInfraestructura->persist($objDetalleElemento);
                        }
                    }
                    
                    $this->emInfraestructura->flush();
                    $this->emInfraestructura->getConnection()->commit();
                    
                    $arrayRespuesta = array(
                                'status'  => 'OK',
                                'mensaje' => $strTipoElemento.' actualizado correctamente'); 
                }
                else
                {
                    $arrayRespuesta = array(
                                'status'  => 'ERROR',
                                'mensaje' => 'No existe el elemento '.$strNombreSwitchAnterior.' enviado a Actualizar');
                }
            }
            else
            {                
                $arrayRespuesta = array(
                                'status'  => 'ERROR',
                                'mensaje' => 'No se ha recibido información del '.$strNombreSwitchAnterior.' a modificar');
            }                            
        } 
        catch (\Exception $ex) 
        {
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
                $this->emInfraestructura->getConnection()->close();
            }
            
            $arrayRespuesta = array(
                                    'status'  => 'ERROR',
                                    'mensaje' => 'Error al realizar la actualización del registro del Switch enviado'
                                   );
        }
                       
        return $arrayRespuesta;
    }
    
        /**
     * Funcion que sirve para listar tipos por empresa
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec
     * @version 1.0 20-11-2017
     * 
     * @param Array $arrayParametros
     * @return Array $arrayResult 
     */
    public function getElementosPorEmpresa($arrayParametros)
    {

        $arrayResult = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                   ->getElementosPorEmpresa($arrayParametros[0]['nombreElemento'], 
                                                            $arrayParametros[0]['ip'], 
                                                            $arrayParametros[0]['tipoElemento'], 
                                                            $arrayParametros[0]['estado'],
                                                            $arrayParametros[0]['empresa'], 
                                                            $arrayParametros[0]['start'], 
                                                            $arrayParametros[0]['limit']);

        return $arrayResult;
    }
    
    /**
     *
     * Metodo encargado de validar si determinado recurso ya existe configurado en una maquina virtual o se realizó algun cambio
     * sobre los mismos. Los resultados posibles son los siguientes:
     * 
     * - 0 -> No existe , significa que es recurso nuevo y debe ser guardado
     * - 1 -> El recurso sufrio un cambio, se elimina el recurso anterior ( trazabilidad ) y se genera el nuevo con el recurso actualizado
     * - 2 -> El recurso no sufrio cambio alguno el flujo continua sin realizar cambio alguno
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 10-04-2018
     * 
     * @param Array $arrayRecursosExistentes
     * @param Object $objJsonEditado
     * @return int
     */
    public function getValidadorRecursoExistenteMaquinaVirtual($arrayRecursosExistentes, $objJsonEditado)
    {
        $intValidador = 0;
        
        foreach($arrayRecursosExistentes as $objRecursoExistente)
        {            
            //Si no existe, se guarda como registro nuevo
            if($objRecursoExistente->idDetalle == $objJsonEditado->idDetalle && 
               $objRecursoExistente->idRecurso != $objJsonEditado->idRecurso)
            {                               
                $intValidador = 1;//Si cambia el recurso
            }   
            else if($objRecursoExistente->idDetalle == $objJsonEditado->idDetalle && 
                    $objRecursoExistente->idRecurso == $objJsonEditado->idRecurso)
            {
                if($objRecursoExistente->usado != $objJsonEditado->asignar)
                {
                    $intValidador = 1;//Si cambia el recurso
                }
                else
                {
                    $intValidador = 2;//Si no cambia
                }
            }
        }
        
        return $intValidador;
    }
    
    /**
     *
     * Metodo encargado de validar si determinado recurso configurado previamente no fue eliminado por el usuario en la herramienta
     * de edición, en ese caso se eliminará el mismo de los detalles de la maquina virtual
     *    
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 10-04-2018
     * 
     * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 1.1 06-07-2020 Se mantiene la lógica pero se adapta a las nuevas estructuras de soluciones.
     * 
     * @param Array $arrayParametros
     * @return int
     */
    public function validarEliminacionRecursosMaquinaVirtual($arrayParametros)
    {              
        $strObservacion    = '';
        
        //si no existe el tipo actualizado continuara el proceso
        foreach($arrayParametros['arrayEditado'] as $objJsonEditados)
        {                
            if($objJsonEditados->tipo != $arrayParametros['strTipo'])
            {
                return '';
            }
        }
        
        foreach($arrayParametros['arrayExistente'] as $objJsonDisco)
        {
            $boolEliminar = true;
                        
            //recorro el array de recursos editados ( edicion de mvs )
            foreach($arrayParametros['arrayEditado'] as $objJsonEditados)
            {                
                if($objJsonEditados->tipo == $arrayParametros['strTipo'])
                {
                    if($objJsonEditados->idRecurso == $objJsonDisco->idRecurso)
                    {
                        $boolEliminar = false;
                        break;
                    }
                }
            }

            //Se requiere eliminar
            if($boolEliminar)
            {
                 $objServicioRecursoDet = $this->emcom->getRepository("schemaBundle:InfoServicioRecursoDet")
                                                      ->find($objJsonDisco->idDetalle);

                if(is_object($objServicioRecursoDet))
                {
                    $objServicioRecursoDet->setEstado('Eliminado');
                    $this->emcom->persist($objServicioRecursoDet);
                    $this->emcom->flush();

                    $objServicioRecursoCab = $objServicioRecursoDet->getServicioRecursoCabId();

                    if(is_object($objServicioRecursoCab))
                    {
                        $strObservacion .= '<tr><td><b> Se Eliminó '.$arrayParametros['strTipo'].'</b></td>'
                                         . '<td class="td-info-resumen">&nbsp;'.
                                            $objServicioRecursoCab->getDescripcionRecurso().'</td><tr>';
                    }
                }
            }
        }
        
        return $strObservacion;
    }
    

    /**
     * Función que valida la latitud y longitud ingresada en un elemento
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 17-09-2018
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 16-10-2018 - Se agrega en el método la validación para detectar las coordenadas del país
     *                           en caso de que un usuario no se encuentre logueado.
     *
     * @param array $arrayParametros [
     *                                  "strLatitudElemento"    => latitud del elemento
     *                                  "strLongitudElemento"   => longitud del elemento
     *                               ]
     * @return array $arrayResultado [
     *                                  "status"    => OK o ERROR
     *                                  "mensaje"   => mensaje de error
     *                               ]
     */
    public function validarLimitesCoordenadasElemento($arrayParametros)
    {
        $strLatitudElemento         = $arrayParametros["latitudElemento"];
        $strLongitudElemento        = $arrayParametros["longitudElemento"];
        $strMsjTipoElemento         = $arrayParametros["msjTipoElemento"];
        $strMsjTipoElementoPadre    = $arrayParametros["msjTipoElementoPadre"] ? $arrayParametros["msjTipoElementoPadre"] : "";
        $strMsjAdicional            = $arrayParametros["msjAdicional"] ? $arrayParametros["msjAdicional"] : "por favor revisar";
        $strLimiteLatitudNorte      = $this->objSession->get("strLimiteLatitudNorte");
        $strLimiteLatitudSur        = $this->objSession->get("strLimiteLatitudSur");
        $strLimiteLongitudEste      = $this->objSession->get("strLimiteLongitudEste");
        $strLimiteLongitudOeste     = $this->objSession->get("strLimiteLongitudOeste");
        $strRangoPais               = $this->objSession->get("strRangoPais");
        $strMensaje                 = "";
        try
        {
            if(empty($strLatitudElemento) || empty($strLongitudElemento))
            {
                throw new \Exception("No se han enviado los valores de latitud y longitud");
            }

            if(empty($strLimiteLatitudNorte) || empty($strLimiteLatitudSur) || empty($strLimiteLongitudEste) || empty($strLimiteLongitudOeste)
                || empty($strRangoPais))
            {
                $arrayLimitesCoordenadas = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('LIMITES_COORDENADAS_ELEMENTO', '', '', 'ECUADOR', '','','','','','');

                if(empty($arrayLimitesCoordenadas))
                {
                    throw new \Exception("Los límites del país en sesión no están ingresados. Favor comunicarse con el Dpto. de Sistemas!");
                }
                else
                {
                    $strLimiteLatitudNorte  = $arrayLimitesCoordenadas["valor1"];
                    $strLimiteLatitudSur    = $arrayLimitesCoordenadas["valor2"];
                    $strLimiteLongitudEste  = $arrayLimitesCoordenadas["valor3"];
                    $strLimiteLongitudOeste = $arrayLimitesCoordenadas["valor4"];
                    $strRangoPais           = $arrayLimitesCoordenadas["valor5"];
                }
            }

            $strMensajeErrorCoordenadas = "";
            
            //longitud >= -95 y longitud <= -75.25
            if (!(floatval($strLongitudElemento) >= (floatval($strLimiteLongitudOeste)) 
                && floatval($strLongitudElemento) <= (floatval($strLimiteLongitudEste))))
            {
                $strMensajeErrorCoordenadas .=  "Longitud ".$strLongitudElemento." ".$strMsjTipoElemento.$strMsjTipoElementoPadre
                                                ."no se encuentra dentro del territorio ".$strRangoPais
                                                .", ".$strMsjAdicional.". ";
            }
            
            //latitud >= -5.036 y latitud <= 1.40
            if (!(floatval($strLatitudElemento) >= floatval($strLimiteLatitudSur) 
                && floatval($strLatitudElemento) <= floatval($strLimiteLatitudNorte)))
            {
                $strMensajeErrorCoordenadas .=  "Latitud ".$strLatitudElemento." ".$strMsjTipoElemento.$strMsjTipoElementoPadre
                                                ."no se encuentra dentro del territorio ".$strRangoPais
                                                .", ".$strMsjAdicional.". ";
            }
            
            if(!empty($strMensajeErrorCoordenadas))
            {
                throw new \Exception($strMensajeErrorCoordenadas);
            }
            
            $strStatus = "OK";
        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
        }
        $arrayResultado = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayResultado;
    }

    /**
     * Servicio para crear  máquinas Virtuales
     *
     * @author José Álava <jialava@telconet.ec>
     * @version 1.0 05-06-2019
     * 
     * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 1.1 06-07-2020 Se mantiene la lógica pero se adapta a las 
     *                         nuevas estructuras de soluciones con el llamado
     *                         a MS.
     *
     * @param Array $arrayParametros
     */
    public function guardarMaquinasVirtuales($arrayParametros)
    {
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $strIpCreacion          = $arrayParametros['strIpCreacion'];
        $intIdEmpresa           = $arrayParametros['intIdEmpresa'];
        $strData                = $arrayParametros['strJson'];
        $intIdServicio          = $arrayParametros['intIdServicio'];
        $arrayMaquinasVirtuales = json_decode($strData);

        //Variables de respuesta.
        $strStatus  = "OK";
        $strMensaje = "Máquinas Virtuales guardadas correctamente.";

        $this->emcom->getConnection()->beginTransaction();

        try
        {
            $objModeloMaquinaVirtual = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                    ->findOneBy(array('nombreModeloElemento' => 'MODELO MAQUINA VIRTUAL DC',
                                      'estado'               => 'Activo'));

            if (!is_object($objModeloMaquinaVirtual))
            {
                throw new \Exception('No se encontró el módelo genérico de Máquina Virtual.');
            }

            //For de maquinas virtuales.
            foreach($arrayMaquinasVirtuales as $objMaquinaVirtual)
            {
                $arrayElemento                        =  array();
                $arrayElemento['nombreElemento']      =  $objMaquinaVirtual->nombre;
                $arrayElemento['descripcionElemento'] = 'Maquina Virtual DC';
                $arrayElemento['modeloElementoId']    =  $objModeloMaquinaVirtual->getId();

                $arrayDetalle                     =  array();
                $arrayDetalle[0]['detalleNombre'] = 'CARPETA';
                $arrayDetalle[0]['detalleValor']  =  $objMaquinaVirtual->carpeta;
                $arrayDetalle[1]['detalleNombre'] = 'TARJETA_RED';
                $arrayDetalle[1]['detalleValor']  =  $objMaquinaVirtual->tarjeta;

                $arrayEmpresaElementoUbica                = array();
                $arrayEmpresaElementoUbica['empresaCod']  = $intIdEmpresa;
                $arrayEmpresaElementoUbica['ubicacionId'] = 0;

                //Obtener la informacion de recursos contratados para la maquina virtual
                $arrayDetalleRecursos = array();
                $arrayRecursosMv      = json_decode($objMaquinaVirtual->arrayRecursos);
                $intCont              = 0;

                //For para obtener los recursos dados a una maquina virtual.
                foreach($arrayRecursosMv as $objRecursoMv)
                {
                     $arrayDetalleRecursos[$intCont]['servicioRecursoCabId'] = $objRecursoMv->idRecurso;
                     $arrayDetalleRecursos[$intCont]['cantidad']             = $objRecursoMv->asignar;
                     $intCont = $intCont +1;
                }

                $arrayMaquinaVirtual                         =  array();
                $arrayMaquinaVirtual['habilitaCommit']       =  true;
                $arrayMaquinaVirtual['estado']               = 'Activo';
                $arrayMaquinaVirtual['usrCreacion']          =  $strUsrCreacion;
                $arrayMaquinaVirtual['ipCreacion']           =  $strIpCreacion;
                $arrayMaquinaVirtual['elemento']             =  $arrayElemento;
                $arrayMaquinaVirtual['detalle']              =  $arrayDetalle;
                $arrayMaquinaVirtual['detalleRecursos']      =  $arrayDetalleRecursos;
                $arrayMaquinaVirtual['empresaElementoUbica'] =  $arrayEmpresaElementoUbica;

                //LLamada al ws que crea las maquinas virtuales.
                $arrayRespuesta = $this->serviceSolucion->WsPostDc(array('strUser'      =>  $strUsrCreacion,
                                                                         'strIp'        =>  $strIpCreacion,
                                                                         'strOpcion'    => 'soluciondc',
                                                                         'strEndPoint'  => 'crearMaquinaVirtual',
                                                                         'arrayRequest' =>  $arrayMaquinaVirtual));

                if (!$arrayRespuesta['status'])
                {
                    throw new \Exception($arrayRespuesta['message']);
                }

                $objServicio = $this->emcom->getRepository("schemaBundle:InfoServicio")->find($intIdServicio);

                $strObservacion  = "Se creó una nueva Máquina Virtual:<br>";
                $strObservacion .= "<table><tr><td><b><i class='fa fa-share' aria-hidden='true'></i>&nbsp;"
                                    . "Nombre</b></td><td>&nbsp;</td><td>".$objMaquinaVirtual->nombre."</td></tr>";
                $strObservacion .= "<tr><td><b><i class='fa fa-share' aria-hidden='true'></i>&nbsp;"
                                    . "Storage:</b></td><td>&nbsp;</td><td>".$objMaquinaVirtual->storage." (GB)</td></tr>";
                $strObservacion .= "<tr><td><b><i class='fa fa-share' aria-hidden='true'></i>&nbsp;"
                                    . "Memoria:</b></td><td>&nbsp;</td><td>".$objMaquinaVirtual->memoria." (GB)</td></tr>";
                $strObservacion .= "<tr><td><b><i class='fa fa-share' aria-hidden='true'></i>&nbsp;"
                                    . "Procesador:</b></td><td>&nbsp;</td><td>".$objMaquinaVirtual->procesador." (Cores)</td></tr></table>";

                //Registramos el historial.
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion($strObservacion);
                $objServicioHistorial->setEstado($objServicio->getEstado());
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $this->emcom->persist($objServicioHistorial);
                $this->emcom->flush();
            }
            $this->emcom->commit();
        }
        catch (\Exception $objException) 
        {
            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }

            $strStatus  = "ERROR";
            $strMensaje = "Error al guardar las Máquinas Virtuales, por favor notificar a Sistemas.";

            $this->utilService->insertError('Telcos+', 
                                            'InfoElementoService.guardarMaquinasVirtualesAction', 
                                             $objException->getMessage(),
                                             $strUsrCreacion,
                                             $strIpCreacion);
        }

        return array('strStatus' => $strStatus,'strMensaje' => $strMensaje);
    }

    /**
     *
     * Función que relaciona el elemento con el nodo wifi cuando se activa
     * servicio tradicional para instalacion simultanea wifi.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.0 14-05-2019 - Versión Inicial.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.1 03-12-2019 - Se modifica lógica para que cuando el servicio tradicional, tenga UM 'Radio', los concentradores
     *                           de administración y navegación tengan la misma UM (Radio).
     *
     */

    public function createRouterExistente($arrayParams)
    {
        $entityEm    = $this->emInfraestructura;
        $emComercial = $this->emcom;

        $arrayResult = array(
            'status' => 'ERROR'
        );

        $entityEm->getConnection()->beginTransaction();
        $emComercial->getConnection()->beginTransaction();

        $objPeticion        = $arrayParams['objRequest'];
        $objSesion          = $objPeticion->getSession();

        $arrayServiciosWifi = $arrayParams['arrayIdServiciosWifi']; /*Array de Servicios Wifi*/
        $intIdServNodWifi   = intval($arrayServiciosWifi[0]); /*Primer Servicio Wifi*/
        $intIdServElemento  = intval($arrayParams['intIdServElemento']); /*idServicio Tradicional*/
        $intIdEmpresa       = intval($arrayParams['intIdEmpresa']);

        $arrayResponse      = $this->getElementosRouterExistente($intIdServNodWifi, $intIdServElemento);
        $intCapacidad       = $arrayResponse['intCapacidad'];
        $intIdElemento      = $arrayResponse['intIdEqClte'];
        $intIdNodoWifi      = $arrayResponse['intIdNodo'];
        $intIdPunto         = $arrayResponse['intIdPunto'];
        $intIdServicio      = $arrayResponse['intIdServicio']; /*IdServicioTradicional*/

        /*Si obtenemos todos los datos necesarios exitosamente se continua el proceso.*/
        if ($arrayResponse['status'] == 'OK')
        {
            try
            {
                $objNodo = $entityEm->getRepository('schemaBundle:InfoElemento')->find($intIdNodoWifi);
                $objElemento = $entityEm->getRepository('schemaBundle:InfoElemento')->find($intIdElemento);
                $entityServicioTecnico = $entityEm->getRepository('schemaBundle:InfoServicioTecnico')
                                            ->findOneByServicioId($intIdServicio);
                $objUMServTrad = $entityEm->getRepository('schemaBundle:AdmiTipoMedio')
                                          ->find($entityServicioTecnico->getUltimaMillaId());

                if(!is_object($entityServicioTecnico))
                {
                    throw new Exception('El servicio no tiene elemento información técnica.');
                }

                if($intIdServicio)
                {
                    $objServicio = $entityEm->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
                    if($objServicio)
                    {
                        $objProductoServicio = $objServicio->getProductoId();
                        $intIdProductoServicio = $objProductoServicio ? $objProductoServicio->getId() : null;
                    }
                }
                //relacion elemento
                $objRelacionElemento = new InfoRelacionElemento();

                if($objNodo)
                {
                    $objRelacionElemento->setElementoIdA($objNodo->getId());
                }
                if($objElemento)
                {
                    $objRelacionElemento->setElementoIdB($objElemento->getId());
                }
                $objRelacionElemento->setTipoRelacion("CONTIENE");
                $objRelacionElemento->setObservacion("Nodo Wifi contiene Router");
                $objRelacionElemento->setEstado("Activo");
                $objRelacionElemento->setUsrCreacion($objSesion->get('user'));
                $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                $objRelacionElemento->setIpCreacion($objPeticion->getClientIp());
                $entityEm->persist($objRelacionElemento);

                //compruebo si tiene ubicacion sino le creo
                $objEmpresaUbicacion = $entityEm->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                    ->findOneByElementoId($intIdElemento);

                if(!$objEmpresaUbicacion)
                {
                    if($objNodo)
                    {
                        //tomar datos nodo
                        $objNodoEmpresaElementoUbicacion = $entityEm->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                            ->findOneBy(array("elementoId" => $objNodo->getId()));
                        if($objNodoEmpresaElementoUbicacion)
                        {
                            $objNodoUbicacion = $entityEm->getRepository('schemaBundle:InfoUbicacion')
                                ->find($objNodoEmpresaElementoUbicacion->getUbicacionId()->getId());
                        }
                    }

                    if($objNodoUbicacion)
                    {
                        $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
                        $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                            "latitudElemento"       =>
                                $objNodoUbicacion->getLatitudUbicacion(),
                            "longitudElemento"      =>
                                $objNodoUbicacion->getLongitudUbicacion(),
                            "msjTipoElemento"       => "del nodo wifi ",
                            "msjTipoElementoPadre"  =>
                                "que contiene al router del cliente ",
                            "msjAdicional"          =>
                                "por favor regularizar en la administración"
                                ." de Nodos Wifi"
                        ));
                        if($arrayRespuestaCoordenadas["status"] === "ERROR")
                        {
                            throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                        }
                        //info ubicacion
                        $objParroquia = $entityEm->find('schemaBundle:AdmiParroquia', $objNodoUbicacion->getParroquiaId());
                        $objUbicacionElemento = new InfoUbicacion();
                        $objUbicacionElemento->setLatitudUbicacion($objNodoUbicacion->getLatitudUbicacion());
                        $objUbicacionElemento->setLongitudUbicacion($objNodoUbicacion->getLongitudUbicacion());
                        $objUbicacionElemento->setDireccionUbicacion($objNodoUbicacion->getDireccionUbicacion());
                        $objUbicacionElemento->setAlturaSnm($objNodoUbicacion->getAlturaSnm());
                        $objUbicacionElemento->setParroquiaId($objParroquia);
                        $objUbicacionElemento->setUsrCreacion($objSesion->get('user'));
                        $objUbicacionElemento->setFeCreacion(new \DateTime('now'));
                        $objUbicacionElemento->setIpCreacion($objPeticion->getClientIp());
                        $entityEm->persist($objUbicacionElemento);
                    }

                    //empresa elemento ubicacion
                    $objEmpresaElementoUbica = new InfoEmpresaElementoUbica();
                    $objEmpresaElementoUbica->setEmpresaCod($objSesion->get('idEmpresa'));
                    $objEmpresaElementoUbica->setElementoId($objElemento);
                    $objEmpresaElementoUbica->setUbicacionId($objUbicacionElemento);
                    $objEmpresaElementoUbica->setUsrCreacion($objSesion->get('user'));
                    $objEmpresaElementoUbica->setFeCreacion(new \DateTime('now'));
                    $objEmpresaElementoUbica->setIpCreacion($objPeticion->getClientIp());
                    $entityEm->persist($objEmpresaElementoUbica);

                    //empresa elemento
                    $objEmpresaElemento = new InfoEmpresaElemento();
                    $objEmpresaElemento->setElementoId($objElemento);
                    $objEmpresaElemento->setEmpresaCod($objSesion->get('idEmpresa'));
                    $objEmpresaElemento->setEstado("Activo");
                    $objEmpresaElemento->setUsrCreacion($objSesion->get('user'));
                    $objEmpresaElemento->setIpCreacion($objPeticion->getClientIp());
                    $objEmpresaElemento->setFeCreacion(new \DateTime('now'));
                    $entityEm->persist($objEmpresaElemento);
                }

                //cambiar estado a solicitud
                $objTipoSolicitud = $entityEm->getRepository('schemaBundle:AdmiTipoSolicitud')
                    ->findOneBy(array(
                        'descripcionSolicitud'  => 'SOLICITUD NODO WIFI',
                        'estado'                => 'Activo'
                    ));

                $arraySolicitudesWifi = $entityEm->getRepository('schemaBundle:InfoDetalleSolicitud')
                    ->findBy(array(
                        'elementoId' => $objNodo->getId(),
                        'tipoSolicitudId' => $objTipoSolicitud->getId()
                    ));

                $strEstadoSolicitud = 'PendientePunto';

                foreach ($arraySolicitudesWifi as $objSolicitudWifi)
                {
                    $intIdElementoA = $objSolicitudWifi->getElementoId();

                    /*Actualizo las solicitudes una por una.*/
                    $objSolicitudWifi->setEstado($strEstadoSolicitud);
                    $entityEm->persist($objSolicitudWifi);
                    $entityEm->flush();

                    //GUARDAR INFO DETALLE SOLICITUD HISTORIAL
                    $objDetalleSolHist = new InfoDetalleSolHist();
                    $objDetalleSolHist->setDetalleSolicitudId($objSolicitudWifi);
                    $objDetalleSolHist->setIpCreacion($objPeticion->getClientIp());
                    $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolHist->setUsrCreacion($objPeticion->getSession()->get('user'));
                    $objDetalleSolHist->setEstado($strEstadoSolicitud);
                    $entityEm->persist($objDetalleSolHist);
                    $entityEm->flush();

                    //actualizo el elemento
                    $objElementoNodoWifi = $entityEm->getRepository('schemaBundle:InfoElemento')
                        ->findOneById($intIdElementoA);
                    $objElementoNodoWifi->setObservacion('Nodo wifi aprobada en la factibilidad por ' . $objSesion->get('user'));
                    $objElementoNodoWifi->setEstado("Activo");
                    $entityEm->persist($objElementoNodoWifi);
                    $entityEm->flush();

                }

                //caracteristica para saber donde esta ubicada la router  (pedestal - edificio)
                $objDetalle = new InfoDetalleElemento();
                $objDetalle->setElementoId($objElemento->getId());
                $objDetalle->setDetalleNombre("TIPO ELEMENTO RED");
                $objDetalle->setDetalleValor("WIFI");
                $objDetalle->setDetalleDescripcion("Caracteristicas para indicar que es un router de uso Wifi");
                $objDetalle->setFeCreacion(new \DateTime('now'));
                $objDetalle->setUsrCreacion($objSesion->get('user'));
                $objDetalle->setIpCreacion($objPeticion->getClientIp());
                $objDetalle->setEstado('Activo');
                $entityEm->persist($objDetalle);
                $entityEm->flush();

                //caracteristica para saber donde esta ubicada la router  (pedestal - edificio)
                $objDetalle1 = new InfoDetalleElemento();
                $objDetalle1->setElementoId($objElemento->getId());
                $objDetalle1->setDetalleNombre("CAPACIDAD");
                $objDetalle1->setDetalleValor($intCapacidad);
                $objDetalle1->setDetalleDescripcion("Capacidad del elemento en Kb ");
                $objDetalle1->setFeCreacion(new \DateTime('now'));
                $objDetalle1->setUsrCreacion($objSesion->get('user'));
                $objDetalle1->setIpCreacion($objPeticion->getClientIp());
                $objDetalle1->setEstado('Activo');
                $entityEm->persist($objDetalle1);
                $entityEm->flush();

                //caracteristica para saber a que punto está relacionado este NODO WIFI
                $objDetalle2 = new InfoDetalleElemento();
                $objDetalle2->setElementoId($objNodo->getId());
                $objDetalle2->setDetalleNombre("ID_PUNTO");
                $objDetalle2->setDetalleValor($intIdPunto);
                $objDetalle2->setDetalleDescripcion("Indica relacion con el punto. ");
                $objDetalle2->setFeCreacion(new \DateTime('now'));
                $objDetalle2->setUsrCreacion($objSesion->get('user'));
                $objDetalle2->setIpCreacion($objPeticion->getClientIp());
                $objDetalle2->setEstado('Activo');
                $entityEm->persist($objDetalle2);
                $entityEm->flush();

                //historial elemento
                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objElemento);
                $objHistorialElemento->setEstadoElemento("Activo");
                $objHistorialElemento->setObservacion('Se asignó al Nodo Wifi '.$objNodo->getNombreElemento());
                $objHistorialElemento->setUsrCreacion($objSesion->get('user'));
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objHistorialElemento->setIpCreacion($objPeticion->getClientIp());
                $entityEm->persist($objHistorialElemento);
                $entityEm->flush();

                $objPunto = $emComercial ->getRepository('schemaBundle:InfoPunto')
                    ->find($intIdPunto);

                $objProducto = $emComercial ->getRepository('schemaBundle:AdmiProducto')
                    ->findOneBy(array('descripcionProducto'=>'L3MPLS',
                                      'nombreTecnico'      =>'L3MPLS'
                    ));

                //ingreso tipo de factibilidad DIRECTA
                $objCaracteristicaFact = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array("descripcionCaracteristica" => 'TIPO_FACTIBILIDAD',
                                      "estado"                    => "Activo"
                    ));

                $objProdCaractFact = null;

                if(is_object($objCaracteristicaFact))
                {
                    if($intIdProductoServicio)
                    {
                        $objProdCaractFact = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                            ->findOneBy(array("caracteristicaId" => $objCaracteristicaFact->getId(),
                                              "productoId" => $intIdProductoServicio
                            ));
                    }
                    $objProdCaractFactNew = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                        ->findOneBy(array("caracteristicaId" => $objCaracteristicaFact->getId(),
                            "productoId" => $objProducto->getId()
                        ));

                    if(is_object($objProdCaractFact))
                    {
                        //servicio prod caract
                        $objSpcFact = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                            ->findOneBy(array("productoCaracterisiticaId" => $objProdCaractFact->getId(),
                                              "servicioId"                => $intIdServicio,
                                              "estado"                    => "Activo"
                            ));
                            $strValor = is_object($objSpcFact) ? $objSpcFact->getValor() : '';
                    }

                    $strTipoElemento = '';
                    //si el servicio no tiene caracteristica factibilidad, verifico la data tecnica para determinar si es RUTA o DIRECTO
                    if($strValor == '')
                    {
                        if($entityServicioTecnico->getElementoClienteId())
                        {
                            $objElementoCliente = $entityEm->getRepository('schemaBundle:InfoElemento')
                                                           ->find($entityServicioTecnico->getElementoClienteId());

                            if(is_object($objElementoCliente) && (is_object($objElementoCliente->getModeloElementoId())))
                            {
                                $objTipoElemento = $objElementoCliente->getModeloElementoId()->getTipoElementoId();
                                $strTipoElemento = is_object($objTipoElemento) ? $objTipoElemento->getNombreTipoElemento() : '';
                            }
                        }
                        else
                        {
                            throw new Exception('El servicio no tiene elemento cliente.');
                        }

                        //si el servicio tecnico no tiene elemento conector y el elemento cliente id es ROUTER o CPE significa que es directo
                        if(!$entityServicioTecnico->getElementoConectorId() && ($strTipoElemento == 'ROUTER' || $strTipoElemento == 'CPE'))
                        {
                            $strValor = 'DIRECTO';
                        }
                        else
                        {
                            $strValor = 'RUTA';
                        }

                        if(is_object($objProdCaractFact) && is_object($objServicio))
                        {
                            //creo al servicio la spc
                            $objSpcFact = new InfoServicioProdCaract();
                            $objSpcFact->setServicioId($objServicio->getId());
                            $objSpcFact->setProductoCaracterisiticaId($objProdCaractFact->getId());
                            $objSpcFact->setValor($strValor);
                            $objSpcFact->setFeCreacion(new \DateTime('now'));
                            $objSpcFact->setUsrCreacion($objSesion->get('user'));
                            $objSpcFact->setEstado("Activo");
                            $emComercial->persist($objSpcFact);
                            $emComercial->flush();
                        }
                    }
                }

                //dependiendo del modelo del nodo verifico la ultima milla
                if($objNodo->getModeloElementoId()->getNombreModeloElemento() == 'BACKBONE')
                {
                    $strUltimaMilla = 'UTP';
                }
                else
                {
                    $strUltimaMilla = 'FO';
                }

                if (is_object($objUMServTrad) && $objUMServTrad->getNombreTipoMedio() == 'Radio')
                {
                    $strUltimaMilla = $objUMServTrad->getCodigoTipoMedio();
                }

                $objTipoMedio = $emComercial ->getRepository('schemaBundle:AdmiTipoMedio')
                    ->findOneByCodigoTipoMedio($strUltimaMilla);

                $objServicio = new InfoServicio();
                $objServicio->setPuntoId($objPunto);
                $objServicio->setProductoId($objProducto);
                $objServicio->setEsVenta('N');
                $objServicio->setPrecioVenta(0);
                $objServicio->setCantidad(1);
                $objServicio->setTipoOrden('N');
                $objServicio->setEstado('AsignadoTarea');
                $objServicio->setFrecuenciaProducto(1);
                $objServicio->setDescripcionPresentaFactura('Concentrador L3MPLS Administracion');
                $objServicio->setUsrCreacion($objSesion->get('user'));
                $objServicio->setFeCreacion(new \DateTime('now'));
                $objServicio->setIpCreacion($objPeticion->getClientIp());
                $emComercial->persist($objServicio);
                $emComercial->flush();

                //si se obtiene los datos del producto característica se inserta en el servicio
                if(is_object($objProdCaractFactNew))
                {
                    //inserto la servicio prod caract
                    $objServicioProdCaract = new InfoServicioProdCaract();
                    $objServicioProdCaract->setServicioId($objServicio->getId());
                    $objServicioProdCaract->setProductoCaracterisiticaId($objProdCaractFactNew->getId());
                    $objServicioProdCaract->setValor($strValor);
                    $objServicioProdCaract->setFeCreacion(new \DateTime('now'));
                    $objServicioProdCaract->setUsrCreacion($objSesion->get('user'));
                    $objServicioProdCaract->setEstado("Activo");
                    $emComercial->persist($objServicioProdCaract);
                    $emComercial->flush();
                }

                //historial del servicio
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion("Se creo el servicio.");
                $objServicioHistorial->setEstado('AsignadoTarea');
                $objServicioHistorial->setUsrCreacion($objSesion->get('user'));
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($objPeticion->getClientIp());
                $emComercial->persist($objServicioHistorial);
                $emComercial->flush();

                $serviceServicioGeneral = $this->infoServicioTecnicoService;

                //se estableció con el usuario que la capacidad sea 14Kb
                $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "CAPACIDAD1", '14',
                    $objSesion->get('user'));
                $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "CAPACIDAD2", '14',
                    $objSesion->get('user'));
                // Se agrega la característica que relaciona el concentrador con los servicios WIFI.
                $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                                $objProducto,
                                                                                "RELACION_INTERNET_WIFI",
                                                                                implode(", ", $arrayServiciosWifi),
                                                                                $objSesion->get('user'));

                //obtener jurisdiccion
                $objParroquia = $emComercial->getRepository('schemaBundle:AdmiParroquia')
                    ->find($objPunto->getSectorId()->getParroquiaId());
                if($objParroquia)
                {
                    /*Si el canton es Guayaquil o Quito, la variable tomará este valor, caso contrario sera Provincias*/
                    $strCanton = $objParroquia->getCantonId()->getNombreCanton() == 'GUAYAQUIL'
                           || $objParroquia->getCantonId()->getNombreCanton() == 'QUITO'
                            ? $objParroquia->getCantonId()->getNombreCanton() : 'PROVINCIAS';

                    $arrayParametros = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                        ->getOne('ENLACE_DATOS_WIFI', 'TECNICO', '', '', $strCanton, '', '', '', '', $intIdEmpresa);

                    $objServicioEnlace = null;
                    if($arrayParametros['valor2'])
                    {
                        $objServicioEnlace = $emComercial->getRepository('schemaBundle:InfoServicio')
                            ->findOneByLoginAux($arrayParametros['valor2']);

                        if($objServicioEnlace)
                        {
                            $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "ENLACE_DATOS",
                                $objServicioEnlace->getId(), $objSesion->get('user'));

                        }
                    }
                }

                $objServicioTecnico  = new InfoServicioTecnico();

                if($entityServicioTecnico)
                {
                    //la tabla servicio tecnico al menos debe tener el id del elemento y la interface
                    if($entityServicioTecnico->getElementoId() && $entityServicioTecnico->getInterfaceElementoId())
                    {
                        $objServicioTecnico->setElementoId($entityServicioTecnico->getElementoId());
                        $objServicioTecnico->setInterfaceElementoId($entityServicioTecnico->getInterfaceElementoId());
                        $objServicioTecnico->setElementoContenedorId($entityServicioTecnico->getElementoContenedorId());
                        $objServicioTecnico->setElementoConectorId($entityServicioTecnico->getElementoConectorId());
                        $objServicioTecnico->setInterfaceElementoConectorId($entityServicioTecnico->getInterfaceElementoConectorId());
                        $objServicioTecnico->setInterfaceElementoClienteId($entityServicioTecnico->getInterfaceElementoClienteId());
                        $objServicioTecnico->setElementoClienteId($entityServicioTecnico->getElementoClienteId());
                    }
                    else
                    {
                        throw new \Exception('El servicio tiene la data técnica incompleta, favor revisar.');
                    }
                }


                $objServicioTecnico->setServicioId($objServicio);
                $objServicioTecnico->setTipoEnlace('PRINCIPAL');
                $objServicioTecnico->setUltimaMillaId($objTipoMedio->getId());
                $emComercial->persist($objServicioTecnico);
                $emComercial->flush();

                //creo la solicitud de planificacion y el historial para que pase directo a asignar recursos de red
                $entityTipoSolicitud =$emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                  ->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");

                $entitySolicitud  = new InfoDetalleSolicitud();
                $entitySolicitud->setServicioId($objServicio);
                $entitySolicitud->setTipoSolicitudId($entityTipoSolicitud);
                $entitySolicitud->setEstado("AsignadoTarea");
                $entitySolicitud->setUsrCreacion($objSesion->get('user'));
                $entitySolicitud->setFeCreacion(new \DateTime('now'));

                $emComercial->persist($entitySolicitud);
                $emComercial->flush();

                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $entityDetalleSolHist = new InfoDetalleSolHist();
                $entityDetalleSolHist->setDetalleSolicitudId($entitySolicitud);
                $entityDetalleSolHist->setIpCreacion($objPeticion->getClientIp());
                $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $entityDetalleSolHist->setUsrCreacion($objSesion->get('user'));
                $entityDetalleSolHist->setEstado('AsignadoTarea');

                $emComercial->persist($entityDetalleSolHist);
                $emComercial->flush();

                //ingreso el segundo servicio
                $objServicio2 = new InfoServicio();
                $objServicio2->setPuntoId($objPunto);
                $objServicio2->setProductoId($objProducto);
                $objServicio2->setEsVenta('N');
                $objServicio2->setPrecioVenta(0);
                $objServicio2->setCantidad(1);
                $objServicio2->setTipoOrden('N');
                $objServicio2->setEstado('AsignadoTarea');
                $objServicio2->setFrecuenciaProducto(1);
                $objServicio2->setDescripcionPresentaFactura('Concentrador L3MPLS Navegacion');
                $objServicio2->setUsrCreacion($objSesion->get('user'));
                $objServicio2->setFeCreacion(new \DateTime('now'));
                $objServicio2->setIpCreacion($objPeticion->getClientIp());
                $emComercial->persist($objServicio2);
                $emComercial->flush();

                //si se obtiene los datos del producto característica se inserta en el servicio
                if(is_object($objProdCaractFactNew))
                {
                    //inserto la servicio prod caract
                    $objServicioProdCaract = new InfoServicioProdCaract();
                    $objServicioProdCaract->setServicioId($objServicio2->getId());
                    $objServicioProdCaract->setProductoCaracterisiticaId($objProdCaractFactNew->getId());
                    $objServicioProdCaract->setValor($strValor);
                    $objServicioProdCaract->setFeCreacion(new \DateTime('now'));
                    $objServicioProdCaract->setUsrCreacion($objSesion->get('user'));
                    $objServicioProdCaract->setEstado("Activo");
                    $emComercial->persist($objServicioProdCaract);
                    $emComercial->flush();
                }

                //historial del servicio
                $objServicioHistorial2 = new InfoServicioHistorial();
                $objServicioHistorial2->setServicioId($objServicio2);
                $objServicioHistorial2->setObservacion("Se creo el servicio.");
                $objServicioHistorial2->setEstado('AsignadoTarea');
                $objServicioHistorial2->setUsrCreacion($objSesion->get('user'));
                $objServicioHistorial2->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial2->setIpCreacion($objPeticion->getClientIp());
                $emComercial->persist($objServicioHistorial2);
                $emComercial->flush();


                $entitySolicitud1  = new InfoDetalleSolicitud();
                $entitySolicitud1->setServicioId($objServicio2);
                $entitySolicitud1->setTipoSolicitudId($entityTipoSolicitud);
                $entitySolicitud1->setEstado("AsignadoTarea");
                $entitySolicitud1->setUsrCreacion($objSesion->get('user'));
                $entitySolicitud1->setFeCreacion(new \DateTime('now'));

                $emComercial->persist($entitySolicitud1);
                $emComercial->flush();

                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $entityDetalleSolHist1 = new InfoDetalleSolHist();
                $entityDetalleSolHist1->setDetalleSolicitudId($entitySolicitud1);
                $entityDetalleSolHist1->setIpCreacion($objPeticion->getClientIp());
                $entityDetalleSolHist1->setFeCreacion(new \DateTime('now'));
                $entityDetalleSolHist1->setUsrCreacion($objSesion->get('user'));
                $entityDetalleSolHist1->setEstado('AsignadoTarea');

                $emComercial->persist($entityDetalleSolHist1);
                $emComercial->flush();

                $objServicioTecnico2  = new InfoServicioTecnico();

                if($entityServicioTecnico)
                {
                    $objServicioTecnico2->setElementoId($entityServicioTecnico->getElementoId());
                    $objServicioTecnico2->setInterfaceElementoId($entityServicioTecnico->getInterfaceElementoId());
                    $objServicioTecnico2->setElementoContenedorId($entityServicioTecnico->getElementoContenedorId());
                    $objServicioTecnico2->setElementoConectorId($entityServicioTecnico->getElementoConectorId());
                    $objServicioTecnico2->setInterfaceElementoConectorId($entityServicioTecnico->getInterfaceElementoConectorId());
                    $objServicioTecnico2->setInterfaceElementoClienteId($entityServicioTecnico->getInterfaceElementoClienteId());
                    $objServicioTecnico2->setElementoClienteId($entityServicioTecnico->getElementoClienteId());

                }

                $objServicioTecnico2->setServicioId($objServicio2);
                $objServicioTecnico2->setTipoEnlace('PRINCIPAL');
                $objServicioTecnico2->setUltimaMillaId($objTipoMedio->getId());
                $emComercial->persist($objServicioTecnico2);
                $emComercial->flush();

                $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio2, $objProducto, "CAPACIDAD1", '14',
                    $objSesion->get('user'));
                $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio2, $objProducto, "CAPACIDAD2", '14',
                    $objSesion->get('user'));
                // Se agrega la característica que relaciona el concentrador con los servicios WIFI.
                $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio2,
                                                                                $objProducto,
                                                                                "RELACION_INTERNET_WIFI",
                                                                                implode(", ", $arrayServiciosWifi),
                                                                                $objSesion->get('user'));

                if($objServicioEnlace)
                {
                    $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio2, $objProducto, "ENLACE_DATOS",
                        $objServicioEnlace->getId(), $objSesion->get('user'));

                }

                $emComercial->commit();
                $entityEm->commit();
                $arrayResult['status'] = 'OK';
                $arrayResult['msg'] = 'Se creó exitosamente el router wifi existente';

            }
            catch(\Exception $e)
            {
                if($emComercial->getConnection()->isTransactionActive())
                {
                    $emComercial->rollback();
                }

                $emComercial->close();

                if($entityEm->getConnection()->isTransactionActive())
                {
                    $entityEm->rollback();
                }
                $entityEm->close();

                $strMensajeError = "Error: " . $e->getMessage();
                $arrayResult['msg'] = $strMensajeError;

                $this->utilService->insertError('Telcos+',
                    'InfoElementoService.createRouterExistente',
                    "Error: <br>" . $e->getMessage(),
                    $objSesion->get('user'),
                    $objPeticion->getClientIp());
            }
        }

        return $arrayResult;

    }


    /**
     * Funcion que permite obtener todos los datos necesarios para crear el elemento router wifi existente.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.0 20-05-2019 - Version Inicial.
     *
     * @params
     *   • $intIdServNodWifi    -> Contiene un int con el id del nodo wifi.
     *   • $intIdServElemento   -> Contiene un int con el id del elemento.
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @return $arrayResponse
     *
     */

    public function getElementosRouterExistente($intIdServNodWifi, $intIdServElemento)
    {
        $arrayResponse              = array();
        $arrayResponse['status']    = 'ERROR';
        $entityEm                   = $this->emInfraestructura;

        /*Obtengo la información necesaria del Nodo Wifi*/
        $objServNodWifi  = $entityEm->getRepository('schemaBundle:InfoServicio')
                              ->find($intIdServNodWifi);

        $objAdmiTipSolNodWifi = $entityEm->getRepository('schemaBundle:AdmiTipoSolicitud')
                                   ->findOneBy(array(
                                        'descripcionSolicitud'=>'SOLICITUD NODO WIFI'
                                    ));

        $objInfoDetSolNodWifi = $entityEm->getRepository('schemaBundle:InfoDetalleSolicitud')
            ->findOneBy(array(
                'servicioId'=>$objServNodWifi->getId(),
                'tipoSolicitudId'=>$objAdmiTipSolNodWifi->getId()
            ));

        $objInfoElementoNodWifi = $entityEm->getRepository('schemaBundle:InfoElemento')
            ->findOneBy(array(
                'id'=>$objInfoDetSolNodWifi->getElementoId()
            ));

        if (is_object($objInfoElementoNodWifi))
        {
            $strNombreElemento = $objInfoElementoNodWifi->getNombreElemento();
            $intIdNodoWifi     = $objInfoElementoNodWifi->getId();
            $intIdPunto        = $objServNodWifi->getPuntoId()->getId();
            $strLogin          = $objServNodWifi->getPuntoId()->getLogin();

            try
            {
                /*Obtengo la información necesaria del elemento*/
                $objServElement = $entityEm->getRepository('schemaBundle:InfoServicio')
                    ->find($intIdServElemento);

                $objServicioTecnico = $entityEm->getRepository('schemaBundle:InfoServicioTecnico')
                    ->findOneByServicioId($objServElement->getId());


                if(is_object($objServicioTecnico) && $objServicioTecnico->getElementoClienteId())
                {
                    $intIdElemClte = $objServicioTecnico->getElementoClienteId();
                    error_log($intIdElemClte);

                    if($intIdElemClte)
                    {
                        $objElemento = $entityEm->getRepository('schemaBundle:InfoElemento')
                            ->find($intIdElemClte);

                        if($objElemento)
                        {
                            $intIdEqClte = '';
                            $strTipoElemento = $objElemento->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                            //si es un cpe o un router es el indicado
                            if($strTipoElemento == 'CPE' || $strTipoElemento == 'ROUTER' )
                            {
                                $intIdEqClte = $objElemento->getId();
                            }
                            else
                            {
                                //sino vamos a buscar los elementos hijos hasta llegar al router
                                $arrayParamRequest = array(
                                    'interfaceElementoConectorId'=> $objServicioTecnico->getInterfaceElementoClienteId(),
                                    'tipoElemento'               => 'ROUTER');

                                $arrayResp = $entityEm->getRepository("schemaBundle:InfoElemento")
                                    ->getElementoClienteByTipoElemento($arrayParamRequest);

                                if($arrayResp['msg'] == 'FOUND')
                                {
                                    $intIdEqClte = $arrayResp['idElemento'];
                                }
                                else
                                {
                                    //sino vamos a buscar los elementos hijos hasta llegar al CPE
                                    $arrayParamRequest = array(
                                        'interfaceElementoConectorId' => $objServicioTecnico->getInterfaceElementoClienteId(),
                                        'tipoElemento'                => 'CPE'
                                    );

                                    $arrayResp = $entityEm->getRepository("schemaBundle:InfoElemento")
                                        ->getElementoClienteByTipoElemento($arrayParamRequest);

                                    if($arrayResp['msg'] == 'FOUND')
                                    {
                                        $intIdEqClte = $arrayResp['idElemento'];
                                    }
                                }
                            }
                            $arrayResponse['intIdEqClte']       = $intIdEqClte;
                            $arrayResponse['intCapacidad']      = 71890;
                            $arrayResponse['strNombreElemento'] = $strNombreElemento;
                            $arrayResponse['intIdNodo']         = $intIdNodoWifi;
                            $arrayResponse['intIdPunto']        = $intIdPunto;
                            $arrayResponse['intIdServicio']     = intval($intIdServElemento);
                            $arrayResponse['strLogin']          = $strLogin;
                            $arrayResponse['status']            = 'OK';
                        }

                    }
                }
            }catch (\Exception $e)
            {
                if ($entityEm->getConnection()->isTransactionActive())
                {
                    $arrayResponse['msn'] = 'Ha ocurrido un error, por favor notificar a Sistemas';
                    $arrayRespuesta['status'] = "ERROR";
                    $this->utilServicio->insertError('Telcos+',
                        'InfoElementoService.getElementosRouterExistente',
                        "Error: <br>" . $e->getMessage(),
                        '', '');
                    $entityEm->getConnection()->rollback();
                    $entityEm->close();
                }
            }
        }
        return $arrayResponse;
    }

    /**
     * validaMantenimientoNodo
     *
     * Método que valida si un nodo posee la característica 'MANTENIMIENTO TORRE', que corresponde al periodo de mantenimiento.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.0 29-01-2020
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.1 05-02-2020 - Se realizan ajustes para que envíe un arreglo con la data correspondiente al
     *                           próximo mantenimiento y si posee el ciclo.
     *
     * @param $intIdElemento
     * @return array
     *
     *
     */
    public function validaMantenimientoNodo($intIdElemento)
    {
        $arrayResponse = array('boolPoseePeriodo' => false, 'strProxMant' => null);

        if (!empty($intIdElemento) && !is_null($intIdElemento))
        {
            try
            {
                $objInfoElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                    ->find($intIdElemento);

                if (is_object($objInfoElemento))
                {
                    $objPeriodoMantenimiento = $this->emInfraestructura
                        ->getRepository('schemaBundle:InfoDetalleElemento')
                        ->findOneBy(array(
                            'elementoId'=> $objInfoElemento->getId(),
                            'detalleNombre'=> 'MANTENIMIENTO TORRE',
                            'estado' => 'Activo'
                        ));

                    $objProximoMantenimiento = $this->emInfraestructura
                        ->getRepository('schemaBundle:InfoDetalleElemento')
                        ->findOneBy(array(
                            'elementoId' => $objInfoElemento->getId(),
                            'detalleNombre' => 'PROXIMO MANTENIMIENTO TORRE',
                            'estado' => 'Activo'
                        ));

                    /*Valido si es un arreglo y si la respuesta esta vacía.*/
                    if (is_object($objPeriodoMantenimiento) && !empty($objPeriodoMantenimiento))
                    {
                        /*En caso de poseer la característica devolveremos 'true'.*/
                        $arrayResponse['boolPoseePeriodo'] = true;
                    }

                    /*Valido si es un arreglo y si la respuesta esta vacía.*/
                    if (is_object($objProximoMantenimiento) && !empty($objProximoMantenimiento))
                    {
                        /*En caso de no poseer la característica devolveremos el valor guardado en ella.*/
                        $arrayResponse['strProxMant'] = $objProximoMantenimiento->getDetalleValor();
                    }

                }

            }
            catch (\Exception $e)
            {
                $objRequest = $this->container->get('request');
                $objSesion = $objRequest->getSession();

                $this->utilService->insertError(
                    'Telcos+',
                    'InfoElementoService.validaMantenimientoNodo',
                    $e->getMessage(),
                    $objSesion->get('user'),
                    $objRequest->getClientIp());
            }
        }

        return $arrayResponse;
    }

    /**
     * ingresaInfoMantenimientoNodo
     *
     * Método que ingresa el periodo de mantenimiento a un nodo radio.
     *
     * @param $arrayParams
     * @return array
     * @secure ("#ROLE_154-7097")
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.0 29-01-2020
     *
     */
    public function ingresaInfoMantenimientoNodo($arrayParams)
    {
        $intIdElemento      = !empty($arrayParams['intIdElemento']) ? $arrayParams['intIdElemento'] : null;
        $intPeriodo         = !empty($arrayParams['intPeriodo']) ? $arrayParams['intPeriodo'] : null;
        $strFechaProxMan    = !empty($arrayParams['strFechaProxMan']) ? $arrayParams['strFechaProxMan'] : null;
        $objRequest         = !empty($arrayParams['objRequest']) ? $arrayParams['objRequest'] : null;

        $emInf             = $this->emInfraestructura;
        $objSesion         = $objRequest->getSession();
        $arrayResponse     = array('status' => 'ERROR');

        if ($intIdElemento && $intPeriodo && $objRequest && $strFechaProxMan)
        {
            $emInf->getConnection()->beginTransaction();

            try
            {
                /*Obtengo un objeto con el elemento ID recibido por parámetro.*/
                $objInfoElemento = $emInf
                    ->getRepository('schemaBundle:InfoElemento')
                    ->find($intIdElemento);

                /*Valido que sea un objeto.*/
                if (is_object($objInfoElemento))
                {
                    /*Busco todas las características de 'MANTENIMIENTO TORRE' que esten vacias.*/
                    $arrayPeriodosMantenimiento = $this->emInfraestructura
                        ->getRepository('schemaBundle:InfoDetalleElemento')
                        ->findBy(array(
                            'elementoId'    => $objInfoElemento->getId(),
                            'detalleNombre' => 'MANTENIMIENTO TORRE',
                            'estado'        => null
                        ));

                    /*Si la búsqueda trae al menos 1 resultado procedo a ponerlo en estado 'Eliminado'.*/
                    if (is_array($arrayPeriodosMantenimiento) && count($arrayPeriodosMantenimiento) >= 1)
                    {
                        foreach ($arrayPeriodosMantenimiento as $objMantenimiento)
                        {
                            $objMantenimiento->setEstado('Eliminado');
                            $this->emInfraestructura->persist($objMantenimiento);
                            $this->emInfraestructura->flush();
                        }
                    }

                    /*Busco todas las características de 'PROXIMO MANTENIMIENTO TORRE' que esten vacias.*/
                    $arrayFechasMantenimiento = $this->emInfraestructura
                        ->getRepository('schemaBundle:InfoDetalleElemento')
                        ->findBy(array(
                            'elementoId'    => $objInfoElemento->getId(),
                            'detalleNombre' => 'PROXIMO MANTENIMIENTO TORRE',
                            'estado'        => null
                        ));

                    /*Si la búsqueda trae al menos 1 resultado procedo a ponerlo en estado 'Eliminado'.*/
                    if (is_array($arrayFechasMantenimiento) && count($arrayFechasMantenimiento) >= 1)
                    {
                        foreach ($arrayFechasMantenimiento as $objFechaProxMan)
                        {
                            $objFechaProxMan->setEstado('Eliminado');
                            $this->emInfraestructura->persist($objFechaProxMan);
                            $this->emInfraestructura->flush();
                        }
                    }

                    /*Busco la característica 'PRÓXIMO MANTENIMIENTO TORRE' en estado 'Activo' .*/
                    $arrayFechaMantenimientoActivo = $this->emInfraestructura
                        ->getRepository('schemaBundle:InfoDetalleElemento')
                        ->findOneBy(array(
                            'elementoId'    => $objInfoElemento->getId(),
                            'detalleNombre' => 'PROXIMO MANTENIMIENTO TORRE',
                            'estado'        => 'Activo'
                        ));

                    /*Validamos que el objeto no este vacío y si el detalle es diferente al que se recibe por parámetro.*/
                    if (is_object($arrayFechaMantenimientoActivo) && !empty($arrayFechaMantenimientoActivo)                        )
                    {
                        /*Si el valor es diferente lo actualizaremos con el que recibimos por parámetro.*/
                        if ($arrayFechaMantenimientoActivo->getDetalleValor() !== $strFechaProxMan)
                        {
                            $arrayFechaMantenimientoActivo->setDetalleValor($strFechaProxMan);
                            $this->emInfraestructura->persist($arrayFechaMantenimientoActivo);
                            $this->emInfraestructura->flush();

                            /*Guardamos un mensaje en el historial del elemento.*/
                            $objInfoHistorialElemento = new InfoHistorialElemento();
                            $objInfoHistorialElemento->setElementoId($objInfoElemento);
                            $objInfoHistorialElemento->setEstadoElemento('Modificado');
                            $objInfoHistorialElemento->setObservacion("Se agrega periodo de mantenimiento: $intPeriodo meses.");
                            $objInfoHistorialElemento->setUsrCreacion($objSesion->get('user'));
                            $objInfoHistorialElemento->setFeCreacion(new \DateTime('now'));
                            $objInfoHistorialElemento->setIpCreacion($objRequest->getClientIp());

                            $this->emInfraestructura->persist($objInfoHistorialElemento);
                            $this->emInfraestructura->flush();
                        }

                    }
                    /*Si el objeto no existe o esta nulo, crearemos uno nuevo.*/
                    else
                    {
                        /*Registramos la característica nueva.*/
                        $objInfoDetalleElemento = new InfoDetalleElemento();
                        $objInfoDetalleElemento->setElementoId($objInfoElemento->getId());
                        $objInfoDetalleElemento->setDetalleNombre('PROXIMO MANTENIMIENTO TORRE');
                        $objInfoDetalleElemento->setDetalleValor($strFechaProxMan);
                        $objInfoDetalleElemento->setDetalleDescripcion('Próxima fecha de mantenimiento de Torre');
                        $objInfoDetalleElemento->setUsrCreacion($objSesion->get('user'));
                        $objInfoDetalleElemento->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleElemento->setIpCreacion($objRequest->getClientIp());
                        $objInfoDetalleElemento->setEstado('Activo');

                        $this->emInfraestructura->persist($objInfoDetalleElemento);
                        $this->emInfraestructura->flush();

                        /*Guardamos un mensaje en el historial del elemento.*/
                        $objInfoHistorialElemento = new InfoHistorialElemento();
                        $objInfoHistorialElemento->setElementoId($objInfoElemento);
                        $objInfoHistorialElemento->setEstadoElemento('Modificado');
                        $objInfoHistorialElemento->setObservacion("Se agrega fecha próximo mantenimiento: $strFechaProxMan.");
                        $objInfoHistorialElemento->setUsrCreacion($objSesion->get('user'));
                        $objInfoHistorialElemento->setFeCreacion(new \DateTime('now'));
                        $objInfoHistorialElemento->setIpCreacion($objRequest->getClientIp());

                        $this->emInfraestructura->persist($objInfoHistorialElemento);
                        $this->emInfraestructura->flush();
                    }

                    /*Registramos la característica nueva.*/
                    $objInfoDetalleElemento = new InfoDetalleElemento();
                    $objInfoDetalleElemento->setElementoId($objInfoElemento->getId());
                    $objInfoDetalleElemento->setDetalleNombre('MANTENIMIENTO TORRE');
                    $objInfoDetalleElemento->setDetalleValor($intPeriodo);
                    $objInfoDetalleElemento->setDetalleDescripcion('Registro de mantenimiento de Torre');
                    $objInfoDetalleElemento->setUsrCreacion($objSesion->get('user'));
                    $objInfoDetalleElemento->setFeCreacion(new \DateTime('now'));
                    $objInfoDetalleElemento->setIpCreacion($objRequest->getClientIp());
                    $objInfoDetalleElemento->setEstado('Activo');

                    $this->emInfraestructura->persist($objInfoDetalleElemento);
                    $this->emInfraestructura->flush();

                    /*Guardamos un mensaje en el historial del elemento.*/
                    $objInfoHistorialElemento = new InfoHistorialElemento();
                    $objInfoHistorialElemento->setElementoId($objInfoElemento);
                    $objInfoHistorialElemento->setEstadoElemento('Modificado');
                    $objInfoHistorialElemento->setObservacion("Se agrega periodo de mantenimiento: $intPeriodo meses.");
                    $objInfoHistorialElemento->setUsrCreacion($objSesion->get('user'));
                    $objInfoHistorialElemento->setFeCreacion(new \DateTime('now'));
                    $objInfoHistorialElemento->setIpCreacion($objRequest->getClientIp());

                    $this->emInfraestructura->persist($objInfoHistorialElemento);
                    $this->emInfraestructura->flush();

                    $arrayResponse['msg']  = "Exito: Se ha guardado la información de mantenimiento correctamente." ;
                    $arrayResponse['status'] = "OK";

                    $this->emInfraestructura->commit();
                }
            }
            catch (\Exception $e)
            {
                if($emInf->getConnection()->isTransactionActive())
                {
                    $emInf->rollback();
                }

                $emInf->close();

                $arrayResponse['msg']  = "Error: " . $e->getMessage();

                $this->utilService->insertError( 
                    'Telcos+',
                    'InfoElementoService.ingresaInfoMantenimientoNodo',
                    $e->getMessage(),
                    $objSesion->get('user'),
                    $objRequest->getClientIp());
            } 
        }

        return $arrayResponse;

    }

    /**
     * getCicloMantenimientoNodo
     *
     * Método que genera los datos en formato JSON del parámetro 'CICLO MANTENIMIENTO'.
     *
     * @param $arrayParams
     * @return array
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.0 11-02-2020
     *
     */
    public function getCicloMantenimientoNodo($arrayParams)
    {
        $objRequest         = !empty($arrayParams['objRequest']) ? $arrayParams['objRequest'] : null;

        $emGen         = $this->emGeneral;
        $objSesion         = $objRequest->getSession();
        $arrayResponse     = array('status' => 'ERROR', 'total' => 0, 'encontrados' => null);

        if ($objRequest)
        {
            try
            {
                $objAdmiParametroCab = $emGen
                    ->getRepository('schemaBundle:AdmiParametroCab')
                    ->findOneBy(array(
                        'nombreParametro'   =>  'MANTENIMIENTO TORRES',
                        'estado'            =>  'Activo'
                    ));

                if (is_object($objAdmiParametroCab) && !empty($objAdmiParametroCab))
                {
                    $arrayAdmiParametroDet = $emGen
                        ->getRepository('schemaBundle:AdmiParametroDet')
                        ->findBy(array(
                            'parametroId'=>$objAdmiParametroCab->getId(),
                            'descripcion'=>'CICLO MANTENIMIENTO',
                            'estado'=>'Activo'
                        ));

                    if (is_array($arrayAdmiParametroDet) && !empty($arrayAdmiParametroDet))
                    {
                        $intTotal = count($arrayAdmiParametroDet);
                        $arrayCiclos = array();

                        foreach ($arrayAdmiParametroDet as $objParamDet)
                        {
                            $arrayCiclos[] = array(
                                'displayField' => $objParamDet->getValor1(),
                                'valueField' => intval($objParamDet->getValor1())
                            );

                        }

                        if (!empty($arrayCiclos))
                        {
                            $arrayResponse['encontrados']   = $arrayCiclos ;
                            $arrayResponse['total']         = $intTotal ;
                            $arrayResponse['status']        = 'OK';

                        }

                    }

                }

            }
            catch (\Exception $e)
            {
                $emGen->close();

                $arrayResponse['msg']  = "Error: " . $e->getMessage();

                $this->utilService->insertError(
                    'Telcos+',
                    'InfoElementoService.getCicloMantenimientoNodo',
                    $e->getMessage(),
                    $objSesion->get('user'),
                    $objRequest->getClientIp());
            }
        }

        return $arrayResponse;

    }

    /*
     * Documentación para el método 'eliminarElementoSwitchStackTN'.
     * 
     * Metodo para eliminar los elementos de tipos SWITCH, STACK o interfaces de SWITCH/STACK
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 02-10-2019
     * 
     * @param Array $arrayDatos [
     *                              tipoElemento,   Tipo del elemento SWITCH/STACK
     *                              nombre,         Nombre del elemento SWITCH/STACK
     *                              interfaces,     Arreglo de las interfaces de los elementos SWITCH/STACK
     *                              usrCreacion,    Usuario de creacion, quien ejecuta la accion
     *                              ipCreacion      Ip de quien ejecuta la accion
     *                          ]
     * @return Array $arrayRespuesta [ status , mensaje ]
     */
    public function eliminarElementoSwitchStackTN($arrayDatos)
    {
        $strUsrCreacion     = $arrayDatos['usrCreacion'];
        $strIpCreacion      = filter_var($arrayDatos['ipCreacion'], FILTER_VALIDATE_IP);
        if( empty($strIpCreacion) )
        {
            return array(
                'status'    => 'ERROR',
                'mensaje'   => $arrayDatos['ipCreacion'] . ' no es una IP válida."'
            );
        }
        
        $strTipoElemento    = $arrayDatos['tipoElemento'];
        //verificar que el tipo de elemento sea SWITCH
        if( $strTipoElemento != 'SWITCH' )
        {
            return array(
                'status'    => 'ERROR',
                'mensaje'   => $strTipoElemento . ' no es un tipo de elemento válido."'
            );
        }
        
        try
        {
            $this->emInfraestructura->getConnection()->beginTransaction();
            $this->emcom->getConnection()->beginTransaction();
        
            $booleanInterfaceEliminada  = false;
            $strNombreElemento          = $arrayDatos['nombre'];
            $arrayInterfacesElemento    = isset($arrayDatos['interfaces']) ? $arrayDatos['interfaces'] : null;
            
            if( empty($strNombreElemento) )
            {
                throw new \Exception('No se ha recibido el nombre del elemento.');
            }
            
            //Obtener el objeto del elemento por su nombre
            $objInfoElemento            = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                    ->findOneBy(array(  'nombreElemento'    => $strNombreElemento,
                                                                        'estado'            => 'Activo'));
            if( is_object($objInfoElemento) )
            {
                //Obtengo el nombre del tipo del elemento
                $strTipoElemento        = $objInfoElemento->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                //verificar que el tipo del elemento no este vacia y que sea SWITCH
                if( empty($strTipoElemento) || $strTipoElemento != 'SWITCH' )
                {
                    throw new \Exception("El tipo del elemento($strTipoElemento) no es valido.");
                }
                
                //Verificar si es un arreglo y que contenga informacion de las interfaces del SWITCH/STACK
                if( is_array($arrayInterfacesElemento) && count($arrayInterfacesElemento) )
                {
                    foreach( $arrayInterfacesElemento as $strNombreInterfaceElemento )
                    {
                        //Obtengo el objeto de la interface del elemento con el primer caracter de la interface en mayuscula o minuscula
                        $strNombreInterfaceUpper    = ucfirst($strNombreInterfaceElemento);
                        $strNombreInterfaceLower    = lcfirst($strNombreInterfaceElemento);
                        $objInterfaceElemento       = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                ->createQueryBuilder('p')
                                                                ->where('p.elementoId = :elementoId')
                                                                ->andWhere("p.nombreInterfaceElemento = :nombreInterfaceElementoUpper OR ".
                                                                           "p.nombreInterfaceElemento = :nombreInterfaceElementoLower")
                                                                ->setParameter('elementoId', $objInfoElemento->getId())
                                                                ->setParameter('nombreInterfaceElementoUpper',$strNombreInterfaceUpper)
                                                                ->setParameter('nombreInterfaceElementoLower',$strNombreInterfaceLower)
                                                                ->getQuery()
                                                                ->getOneOrNullResult();
                        if(is_object($objInterfaceElemento))
                        {
                            $arrayParametros    = array(
                                                        "objInterfaceElemento"      => $objInterfaceElemento,
                                                        "booleanEliminarElemento"   => false,
                                                        "strUsrCreacion"            => $strUsrCreacion,
                                                        "strIpCreacion"             => $strIpCreacion
                                                    );
                            //metodo que verifica y elimina las interfaces y los servicios que no se encuentran en estados Activos ni In-Corte
                            $arrayRespuesta     = $this->eliminarInterfacesSwitchStacksVerificadas($arrayParametros);
                            if( $arrayRespuesta['status'] == 'ERROR' )
                            {
                                throw new \Exception($arrayRespuesta['mensaje']);  
                            }
                            //si booleanInterfaceEliminada es false y mensaje es ELIMINADA
                            if( !$booleanInterfaceEliminada && $arrayRespuesta['mensaje'] == 'ELIMINADA' )
                            {
                                $booleanInterfaceEliminada  = true;
                            }
                        }
                        else
                        {
                            throw new \Exception("Interface($strNombreInterfaceElemento) no existe en ".
                                                 "Telcos para el elemento($strNombreElemento)");  
                        }
                    }

                    //procedo a ingresar el historial si se eliminaron interfaces
                    if( $booleanInterfaceEliminada )
                    {
                        //historial elemento
                        $objHistorialElemento = new InfoHistorialElemento();
                        $objHistorialElemento->setElementoId($objInfoElemento);
                        $objHistorialElemento->setEstadoElemento("Activo");
                        $objHistorialElemento->setObservacion("Se eliminaron una o varias interfaces de este elemento");
                        $objHistorialElemento->setUsrCreacion($strUsrCreacion);
                        $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                        $objHistorialElemento->setIpCreacion($strIpCreacion);
                        $this->emInfraestructura->persist($objHistorialElemento);
                    }
                    
                    //si se eliminaron una o varias interfaces
                    if( $booleanInterfaceEliminada )
                    {
                        $arrayRespuesta = array(
                            'status'  => 'OK',
                            'mensaje' => 'Se eliminaron correctamente las interfaces del elemento.'
                        );
                    }
                    else
                    {
                        $arrayRespuesta = array(
                            'status'  => 'OK',
                            'mensaje' => "Las interfaces se encuentran en estado 'Eliminado'."
                        );
                    }
                }
                else
                {
                    //Obtener las interfaces del elemento SWITCH/STACK
                    $arrayInterfacesElemento    = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                            ->findByElementoId($objInfoElemento->getId());
                    
                    foreach( $arrayInterfacesElemento as $objInterfaceElemento )
                    {
                        $arrayParametros        = array(
                                                        "objInterfaceElemento"      => $objInterfaceElemento,
                                                        "booleanEliminarElemento"   => true,
                                                        "strUsrCreacion"            => $strUsrCreacion,
                                                        "strIpCreacion"             => $strIpCreacion
                                                    );
                        //metodo que verifica y elimina las interfaces y los servicios que no se encuentran en estados Activos ni In-Corte
                        $arrayRespuesta         = $this->eliminarInterfacesSwitchStacksVerificadas($arrayParametros);
                        if( $arrayRespuesta['status'] == 'ERROR' )
                        {
                            throw new \Exception($arrayRespuesta['mensaje']);  
                        }
                    }
                    
                    //procedo con la eliminación del elemento
                    $objInfoElemento->setEstado('Eliminado');
                    $this->emInfraestructura->persist($objInfoElemento);
                    
                    //Obtengo todas las Ip del elemento
                    $arrayInfoIp                = $this->emInfraestructura->getRepository("schemaBundle:InfoIp")
                                                        ->findBy( array('elementoId' => $objInfoElemento->getId(), 'estado' => 'Activo') );
                    //procedo a eliminar las Ip del elemento
                    foreach( $arrayInfoIp as $objInfoIp )
                    {
                        $objInfoIp->setEstado('Eliminado');
                        $this->emInfraestructura->persist($objInfoIp);
                    }
                    
                    //Obtengo todos los detalles del elemento
                    $arrayInfoDetalleElemento   = $this->emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                        ->findBy( array('elementoId' => $objInfoElemento->getId(), 'estado' => 'Activo') );
                    //procedo a eliminar los detalles del elemento
                    foreach( $arrayInfoDetalleElemento as $objInfoDetalleElemento )
                    {
                        $objInfoDetalleElemento->setEstado('Eliminado');
                        $this->emInfraestructura->persist($objInfoDetalleElemento);
                    }

                    //historial elemento
                    $objHistorialElemento       = new InfoHistorialElemento();
                    $objHistorialElemento->setElementoId($objInfoElemento);
                    $objHistorialElemento->setEstadoElemento("Eliminado");
                    $objHistorialElemento->setObservacion("Se eliminó el elemento");
                    $objHistorialElemento->setUsrCreacion($strUsrCreacion);
                    $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                    $objHistorialElemento->setIpCreacion($strIpCreacion);
                    $this->emInfraestructura->persist($objHistorialElemento);
                    
                    //Verificar si es un elemento SWITCH y existen relaciones de STACK
                    $arrayRelacionElemento      = $this->emInfraestructura->getRepository("schemaBundle:InfoRelacionElemento")
                                                                ->findBy( array('elementoIdA' => $objInfoElemento->getId(), 'estado' => 'Activo') );
                    //Si existen Stacks asociados a este SWITCH se realiza la eliminacion de cada uno de esos elementos
                    foreach( $arrayRelacionElemento as $objRelacionElemento )
                    {
                        $objInfoElementoStack   = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                                        ->find($objRelacionElemento->getElementoIdB());
                        
                        if( is_object($objInfoElementoStack) )
                        {
                            //Si el elemento STACK ya esta eliminado no procedo a eliminar
                            $strEstadoElementoStack  = $objInfoElementoStack->getEstado();
                            if( $strEstadoElementoStack != 'Eliminado' )
                            {
                                //Obtener las interfaces del elemento SWITCH/STACKS
                                $arrayInterfacesElemento    = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                    ->findByElementoId($objInfoElementoStack->getId());

                                foreach( $arrayInterfacesElemento as $objInterfaceElemento )
                                {
                                    $arrayParametros        = array(
                                                                    "objInterfaceElemento"      => $objInterfaceElemento,
                                                                    "booleanEliminarElemento"   => true,
                                                                    "strUsrCreacion"            => $strUsrCreacion,
                                                                    "strIpCreacion"             => $strIpCreacion
                                                                );
                                    //metodo que verifica y elimina las interfaces y los servicios que no se encuentran en estados Activos ni In-Corte
                                    $arrayRespuesta         = $this->eliminarInterfacesSwitchStacksVerificadas($arrayParametros);
                                    if( $arrayRespuesta['status'] == 'ERROR' )
                                    {
                                        throw new \Exception($arrayRespuesta['mensaje']);  
                                    }
                                }

                                //procedo con la eliminación del elemento
                                $objInfoElementoStack->setEstado('Eliminado');
                                $this->emInfraestructura->persist($objInfoElementoStack);

                                //procedo con la eliminación de la relación del elemento
                                $objRelacionElemento->setEstado('Eliminado');
                                $this->emInfraestructura->persist($objRelacionElemento);
                                
                                //Obtengo todas las Ip del elemento
                                $arrayInfoIp                = $this->emInfraestructura->getRepository("schemaBundle:InfoIp")
                                                                    ->findBy( array('elementoId' => $objInfoElementoStack->getId(), 
                                                                                    'estado' => 'Activo') );
                                //procedo a eliminar las Ip del elemento
                                foreach( $arrayInfoIp as $objInfoIp )
                                {
                                    $objInfoIp->setEstado('Eliminado');
                                    $this->emInfraestructura->persist($objInfoIp);
                                }

                                //Obtengo todos los detalles del elemento
                                $arrayInfoDetalleElemento   = $this->emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                                    ->findBy( array('elementoId' => $objInfoElementoStack->getId(), 
                                                                                    'estado' => 'Activo') );
                                //procedo a eliminar los detalles del elemento
                                foreach( $arrayInfoDetalleElemento as $objInfoDetalleElemento )
                                {
                                    $objInfoDetalleElemento->setEstado('Eliminado');
                                    $this->emInfraestructura->persist($objInfoDetalleElemento);
                                }
                                
                                //historial elemento
                                $objHistorialElemento       = new InfoHistorialElemento();
                                $objHistorialElemento->setElementoId($objInfoElementoStack);
                                $objHistorialElemento->setEstadoElemento("Eliminado");
                                $objHistorialElemento->setObservacion("Se eliminó el elemento");
                                $objHistorialElemento->setUsrCreacion($strUsrCreacion);
                                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                                $objHistorialElemento->setIpCreacion($strIpCreacion);
                                $this->emInfraestructura->persist($objHistorialElemento);
                            }
                        }
                    }
                    
                    $arrayRespuesta = array(
                        'status'  => 'OK',
                        'mensaje' => 'Se eliminó correctamente el elemento.'
                    );
                }
            }
            else
            {
                //Obtener el objeto del elemento por su nombre
                $objInfoElemento        = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                ->findOneBy(array(  'nombreElemento'    => $strNombreElemento,
                                                                    'estado'            => 'Eliminado'));
                if( is_object($objInfoElemento) )
                {
                    throw new \Exception("$strNombreElemento se encuentra en estado 'Eliminado'.");
                }
                else
                {
                    throw new \Exception("Elemento $strNombreElemento no existe en Telcos");
                }
            }
            
            $this->emcom->flush();
            $this->emInfraestructura->flush();
            
            if( $this->emcom->getConnection()->isTransactionActive() )
            {
                $this->emcom->getConnection()->commit();
                $this->emcom->getConnection()->close();
            }
            if( $this->emInfraestructura->getConnection()->isTransactionActive() )
            {
                $this->emInfraestructura->getConnection()->commit();
                $this->emInfraestructura->getConnection()->close();
            }
        }
        catch(\Exception $e)
        {
            $this->utilService->insertError('Telcos+', 
                                            'InfoElementoService.eliminarElementoSwitchStackTN', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
            
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }
            
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
                $this->emInfraestructura->getConnection()->close();
            }
            
            $arrayRespuesta = array(
                'status'  => 'ERROR',
                'mensaje' => 'Error: '.$e->getMessage()
            );
        }
        return $arrayRespuesta;
    }
    
    /**
     * Documentación para el método 'eliminarInterfacesSwitchStacksVerificadas'.
     * 
     * Metodo para validar y eliminar las interfaces de los elementos SWITCH/STACK que no se encuentre activa 
     * y que no contenga servicios activos o in-corte
     *    
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 02-10-2019
     * 
     * @param Array $arrayParametros [  
     *                                  objInterfaceElemento,       Objeto del modelo InfoInterfaceElemento
     *                                  booleanEliminarElemento,    Boolean que identifica si es eliminación de elemento o interfaces
     *                                  strUsrCreacion,             Usuario de creacion, quien ejecuta la accion
     *                                  strIpCreacion               Ip de quien ejecuta la accion
     *                              ]
     * @return Array $arrayRespuesta [ status , mensaje ]
     */
    public function eliminarInterfacesSwitchStacksVerificadas($arrayParametros)
    {
        try
        {
            $booleanInterfaceEliminada  = false;
            $objInterfaceElemento       = $arrayParametros['objInterfaceElemento'];
            $booleanEliminarElemento    = $arrayParametros['booleanEliminarElemento'];
            $strUsrCreacion             = $arrayParametros['strUsrCreacion'];
            $strIpCreacion              = $arrayParametros['strIpCreacion'];
            
            $strNombreInterface         = $objInterfaceElemento->getNombreInterfaceElemento();
            
            //validar que los servicios de interface no tenga estos tipos de estados que se les agreguen al arreglo.
            $arrayEstadoServicio        = array();
            //obtengo los parametros de los servicios no permitidos para eliminar
            $objAdmiParametroCabServ    = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array(  'nombreParametro'   => 'ESTADOS_SERVICIOS_NO_PERMITIDOS_ACTUALIZACION',
                                                                    'estado'            => 'Activo'));
            if( is_object($objAdmiParametroCabServ) )
            {
                $arrayAdmiParametroDet  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->findBy(array("parametroId"    => $objAdmiParametroCabServ->getId(),
                                                                        "estado"        => "Activo"));
                foreach( $arrayAdmiParametroDet as $objAdmiParametroDet )
                {
                    array_push($arrayEstadoServicio, $objAdmiParametroDet->getValor1());
                }
            }
            //verificpo que el arreglo de estados de interfaces no se encuentre vacia
            if( count($arrayEstadoServicio) == 0 )
            {
                throw new \Exception("No existe estados de verificación para eliminación de servicios de interfaces en Telcos.");
            }

            $strEstadoInterface         = $objInterfaceElemento->getEstado();
            
            //Obtengo todos los servicios de la interface
            $arrayInfoServicioTecnico   = $this->emcom->getRepository('schemaBundle:InfoServicioTecnico')
                                                                    ->findByInterfaceElementoId($objInterfaceElemento->getId());
            foreach( $arrayInfoServicioTecnico as $objInfoServicioTecnico )
            {
                //Obtengo el objeto del servicio
                $objInfoServicio        = $objInfoServicioTecnico->getServicioId();
                if( is_object($objInfoServicio) )
                {
                    //Verifico que el servicio no se encuentre en uso
                    $strEstadoServicio  = $objInfoServicio->getEstado();
                    if( in_array($strEstadoServicio, $arrayEstadoServicio) )
                    {
                        $strNombreElemento      = $objInterfaceElemento->getElementoId()->getNombreElemento();
                        //si se eliminación de elemento
                        if( $booleanEliminarElemento )
                        {
                            throw new \Exception("$strNombreElemento no puede ser eliminado porque tiene servicios asociados");
                        }
                        else
                        {
                            throw new \Exception("No se puede eliminar la interface($strNombreInterface)".
                                                 " contiene un servicio en estado($strEstadoServicio).");
                        }
                    }
                }
            }
            
            //Si el estado de la interface no esta eliminada ingresa a eliminarla
            if( $strEstadoInterface != 'Eliminado' )
            {
                $booleanInterfaceEliminada = true;
                //procedo con la eliminación de la interface
                $objInterfaceElemento->setEstado('Eliminado');
                $objInterfaceElemento->setUsrUltMod($strUsrCreacion);
                $objInterfaceElemento->setFeUltMod(new \DateTime('now'));
                $this->emInfraestructura->persist($objInterfaceElemento);
            }

            //validar si la interface fue eliminada
            if( $booleanInterfaceEliminada )
            {
                $arrayRespuesta = array(  
                    'status'  => 'OK',
                    'mensaje' => "ELIMINADA"
                );
            }
            else
            {
                $arrayRespuesta = array(  
                    'status'  => 'OK',
                    'mensaje' => "NO ELIMINADA"
                );
            }
        }
        catch(\Exception $e)
        {
            $this->utilService->insertError('Telcos+', 
                                            'InfoElementoService.eliminarInterfacesSwitchStacksVerificadas', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
            $arrayRespuesta =   array(  
                'status'  => 'ERROR',
                'mensaje' => $e->getMessage()
            );
        }
        return $arrayRespuesta;
    }

    /**
     * Documentación para el método 'updateInterfacesElementoSwitchTN'.
     * 
     * Metodo para verificar y actualizar los estados de las interfaces del elemento SWITCH
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 02-10-2019
     * 
     * @param Array $arrayDatos [
     *                              tipoElemento,   Tipo del elemento SWITCH
     *                              nombre,         Nombre del elemento SWITCH
     *                              interfaces      Arreglo de las interfaces del elemento SWITCH
     *                              usrCreacion,    Usuario de creacion, quien ejecuta la accion
     *                              ipCreacion      Ip de quien ejecuta la accion
     *                          ]
     * @return Array $arrayRespuesta [ status , mensaje ]
     */
    public function updateInterfacesElementoSwitchTN($arrayDatos)
    {
        $booleanActualizarInterface = false;
        $strUsrCreacion             = $arrayDatos['usrCreacion'];
        $strIpCreacion              = filter_var($arrayDatos['ipCreacion'], FILTER_VALIDATE_IP);
        if( empty($strIpCreacion) )
        {
            return array(
                'status'    => 'ERROR',
                'mensaje'   => $arrayDatos['ipCreacion'] . ' no es una IP válida."'
            );
        }
        
        $strTipoElemento    = $arrayDatos['tipoElemento'];
        //verificar que el tipo de elemento sea SWITCH
        if( $strTipoElemento != 'SWITCH' )
        {
            return array(
                'status'    => 'ERROR',
                'mensaje'   => $strTipoElemento . ' no es un tipo de elemento válido."'
            );
        }
        
        try
        {
            $this->emInfraestructura->getConnection()->beginTransaction();
        
            $strNombreElemento              = $arrayDatos['nombre'];
            $arrayInterfacesElemento        = $arrayDatos['interfaces'];
            
            if( empty($strNombreElemento) )
            {
                throw new \Exception('No se ha recibido el nombre del elemento.');
            }
            
            //Verificar si es un arreglo y que contenga informacion de las interfaces de los SWITCH
            if( empty($arrayInterfacesElemento) || !is_array($arrayInterfacesElemento) || count($arrayInterfacesElemento) == 0 )
            {
                throw new \Exception('No se ha recibido las interfaces del elemento.');
            }
            
            //Obtener el objeto del elemento anterior
            $objInfoElemento                = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                    ->findOneBy(array(  'nombreElemento'    => $strNombreElemento,
                                                                        'estado'            => 'Activo'));
            if( is_object($objInfoElemento) )
            {
                //Obtengo el nombre del tipo del elemento
                $strTipoElemento            = $objInfoElemento->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                //verificar que el tipo del elemento no este vacia y que sea SWITCH
                if( empty($strTipoElemento) || $strTipoElemento != 'SWITCH' )
                {
                    throw new \Exception("El tipo del elemento($strTipoElemento) no es válido.");
                }
                
                //validar que la interface tenga estos tipos de estados que se les agreguen al arreglo.
                $arrayEstadoInterface       = array();
                //validar los servicios de las interfaces que contengan estos estados.
                $arrayEstadoInterfaceServ   = array();
                //obtengo los parametros de las interfaces permitidas
                $objAdmiParametroCabInt     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                                        array(  'nombreParametro'   => 'ESTADOS_INTERFACES_PERMITIDAS',
                                                                                'estado'            => 'Activo' ));
                if( is_object($objAdmiParametroCabInt) )
                {
                    $arrayAdmiParametroDet  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->findBy(array("parametroId" => $objAdmiParametroCabInt->getId(),
                                                                                       "valor3"      => "ESTADOS_INTERFACES",
                                                                                       "estado"      => "Activo"));
                    foreach( $arrayAdmiParametroDet as $objAdmiParametroDet )
                    {
                        array_push($arrayEstadoInterface, $objAdmiParametroDet->getValor1());
                    }
                    
                    $arrayAdmiParametroDet  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->findBy(array("parametroId" => $objAdmiParametroCabInt->getId(),
                                                                                       "valor2"      => "ACTUALIZAR_INTERFACES",
                                                                                       "estado"      => "Activo"));
                    foreach( $arrayAdmiParametroDet as $objAdmiParametroDet )
                    {
                        array_push($arrayEstadoInterfaceServ, $objAdmiParametroDet->getValor1());
                    }
                }
                
                //validar que los servicios de interface no tenga estos tipos de estados que se les agreguen al arreglo.
                $arrayEstadoServicio        = array();
                //obtengo los parametros de los servicios no permitidos para actualización de estado de interface a 'no connect'
                $objAdmiParametroCabServ    = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneBy(array('nombreParametro' => 'ESTADOS_SERVICIOS_NO_PERMITIDOS_ACTUALIZACION',
                                                                      'estado'          => 'Activo'));
                if( is_object($objAdmiParametroCabServ) )
                {
                    $arrayAdmiParametroDet  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->findBy(array("parametroId"    => $objAdmiParametroCabServ->getId(),
                                                                            "estado"        => "Activo"));
                    foreach( $arrayAdmiParametroDet as $objAdmiParametroDet )
                    {
                        array_push($arrayEstadoServicio, $objAdmiParametroDet->getValor1());
                    }
                }
                
                foreach( $arrayInterfacesElemento as $arrayDatosInterface )
                {
                    $strNombreInterface     = $arrayDatosInterface['nombreInterface'];
                    $strEstadoInterface     = $arrayDatosInterface['estado'];
            
                    //verifico que no esten vacias los parametros del arreglo de interfaces
                    if( empty($strNombreInterface) )
                    {
                        throw new \Exception('No se ha recibido el nombre de la interface del elemento.');
                    }
                    if( empty($strEstadoInterface) )
                    {
                        throw new \Exception('No se ha recibido el estado de la interface del elemento.');
                    }
                    
                    //validar los estados permitidos de las interfaces
                    if( !in_array($strEstadoInterface, $arrayEstadoInterface) )
                    {
                        throw new \Exception("El estado($strEstadoInterface) de la interface del elemento no es válido.");  
                    }
                    
                    //Obtengo el objeto de la interface del elemento con el primer caracter de la interface en mayuscula o minuscula
                    $strNombreInterfaceUpper    = ucfirst($strNombreInterface);
                    $strNombreInterfaceLower    = lcfirst($strNombreInterface);
                    $arrayInterfaceElemento     = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                            ->createQueryBuilder('p')
                                                            ->where('p.elementoId = :elementoId')
                                                            ->andWhere("p.nombreInterfaceElemento = :nombreInterfaceElementoUpper OR ".
                                                                       "p.nombreInterfaceElemento = :nombreInterfaceElementoLower")
                                                            ->setParameter('elementoId', $objInfoElemento->getId())
                                                            ->setParameter('nombreInterfaceElementoUpper',$strNombreInterfaceUpper)
                                                            ->setParameter('nombreInterfaceElementoLower',$strNombreInterfaceLower)
                                                            ->getQuery()
                                                            ->getResult();
                    if(count($arrayInterfaceElemento) > 1)
                    {
                        throw new \Exception("Interface($strNombreInterface) se encuentra repetida en Telcos para el elemento($strNombreElemento), ".
                                             "por favor notificar a Sistemas.");
                    }
                    elseif(count($arrayInterfaceElemento) == 0)
                    {
                        throw new \Exception("Interface($strNombreInterface) no existe en ".
                                             "Telcos para el elemento($strNombreElemento)");
                    }
                    //seteo el objeto de la interface
                    $objInterfaceElemento = $arrayInterfaceElemento[0];
                    if( is_object($objInterfaceElemento) )
                    {
                        $strEstadoInterfaceElemento = $objInterfaceElemento->getEstado();
                        //verifico si el estado del elemento es diferente al recibido
                        if( $strEstadoInterface != $strEstadoInterfaceElemento )
                        {
                            //validar los servicios de las interfaces que contengan estos estados.
                            if( in_array($strEstadoInterface, $arrayEstadoInterfaceServ) )
                            {
                                //Obtengo todos los servicios de la interface
                                $arrayInfoServicioTecnico   = $this->emcom->getRepository('schemaBundle:InfoServicioTecnico')
                                                                                        ->findByInterfaceElementoId($objInterfaceElemento->getId());
                                foreach( $arrayInfoServicioTecnico as $objInfoServicioTecnico )
                                {
                                    //Obtengo el objeto del servicio
                                    $objInfoServicio        = $objInfoServicioTecnico->getServicioId();
                                    if( is_object($objInfoServicio) )
                                    {
                                        //Verifico que el servicio no se encuentre en uso
                                        $strEstadoServicio  = $objInfoServicio->getEstado();
                                        if( in_array($strEstadoServicio, $arrayEstadoServicio) )
                                        {
                                            throw new \Exception("No se puede actualizar la interface($strNombreInterface)".
                                                                 " contiene un servicio en estado($strEstadoServicio).");
                                        }
                                    }
                                }
                            }
                            
                            //procedo con la actualización del estado de la interface
                            $objInterfaceElemento->setEstado($strEstadoInterface);
                            $objInterfaceElemento->setUsrUltMod($strUsrCreacion);
                            $objInterfaceElemento->setFeUltMod(new \DateTime('now'));
                            $this->emInfraestructura->persist($objInterfaceElemento);
                            
                            //seteo a true si se actualiza por lo menos una interface
                            if( !$booleanActualizarInterface )
                            {
                                $booleanActualizarInterface = true;
                            }
                        }
                    }
                    else
                    {
                        throw new \Exception("Interface($strNombreInterface) no existe en ".
                                             "Telcos para el elemento($strNombreElemento)");
                    }
                }

                //verifico si se actualizo por lo menos una interface
                if( $booleanActualizarInterface )
                {
                    //historial elemento
                    $objHistorialElemento = new InfoHistorialElemento();
                    $objHistorialElemento->setElementoId($objInfoElemento);
                    $objHistorialElemento->setEstadoElemento("Activo");
                    $objHistorialElemento->setObservacion("Se actualizaron una o varias interfaces de este elemento");
                    $objHistorialElemento->setUsrCreacion($strUsrCreacion);
                    $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                    $objHistorialElemento->setIpCreacion($strIpCreacion);
                    $this->emInfraestructura->persist($objHistorialElemento);
                }

                //verifico si se actualizo por lo menos una interface
                if( $booleanActualizarInterface )
                {
                    $arrayRespuesta = array(
                        'status'  => 'OK',
                        'mensaje' => 'Se actualizaron correctamente las interfaces del elemento.'
                    );
                }
                else
                {
                    $arrayRespuesta = array(
                        'status'  => 'OK',
                        'mensaje' => 'No se actualizaron las interfaces del elemento, ya se encuentran en esos estados.'
                    );
                }
            }
            else
            {
                //Obtener el objeto del elemento por su nombre
                $objInfoElemento        = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                ->findOneBy(array(  'nombreElemento'    => $strNombreElemento,
                                                                    'estado'            => 'Eliminado'));
                if( is_object($objInfoElemento) )
                {
                    throw new \Exception("$strNombreElemento se encuentra en estado 'Eliminado'.");
                }
                else
                {
                    throw new \Exception("Elemento $strNombreElemento no existe en Telcos");
                }
            }
            
            $this->emInfraestructura->flush();
            
            if( $this->emInfraestructura->getConnection()->isTransactionActive() )
            {
                $this->emInfraestructura->getConnection()->commit();
                $this->emInfraestructura->getConnection()->close();
            }
        }
        catch(\Exception $e)
        {
            $this->utilService->insertError('Telcos+', 
                                            'InfoElementoService.updateInterfacesElementoSwitchTN', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
                $this->emInfraestructura->getConnection()->close();
            }
            
            $arrayRespuesta = array(
                'status'  => 'ERROR',
                'mensaje' => 'Error: '.$e->getMessage()
            );
        }
        return $arrayRespuesta;
    }

    /**
     * Documentación para el método 'updateCambioDispositivoElementoSwitchTN'.
     * 
     * Metodo que crear la solicitud del cambio de ultima milla de las interfaces de un mismo elemento SWITCH
     * o a otras interfaces de otro elemento SWITCH (Cambio Ultima Milla Masivo)
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 02-10-2019
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 20-02-2020 - Se agregaron los parámetros 'modeloNuevo', 'serialNuevo' y 'ipNueva' que permitan
     *                           la actualización del modelo, serie y dirección ip del elemento
     *
     * @param Array $arrayDatos [
     *                              tipoElemento,   Tipo del elemento SWITCH
     *                              nombreAnterior, Nombre anterior del elemento SWITCH
     *                              nombreNuevo,    Nombre nuevo del elemento SWITCH
     *                              modeloNuevo,    Nombre del nuevo modelo del elemento SWITCH
     *                              serialNuevo,    Nueva serie del elemento SWITCH
     *                              ipNueva,        Ip nueva del elemento SWITCH
     *                              interfaces      Arreglo de las interfaces que se actualizaran de los dos elementos SWITCH
     *                              usrCreacion,    Usuario de creacion, quien ejecuta la accion
     *                              ipCreacion      Ip de quien ejecuta la accion
     *                          ]
     * @return Array $arrayRespuesta [ status , mensaje ]
     */
    public function updateCambioDispositivoElementoSwitchTN($arrayDatos)
    {
        $strUsrCreacion     = $arrayDatos['usrCreacion'];
        $strIpCreacion      = filter_var($arrayDatos['ipCreacion'], FILTER_VALIDATE_IP);
        if( empty($strIpCreacion) )
        {
            return array(
                'status'    => 'ERROR',
                'mensaje'   => $arrayDatos['ipCreacion'] . ' no es una IP válida."'
            );
        }
        
        $strTipoElemento    = $arrayDatos['tipoElemento'];
        //verificar que el tipo de elemento sea SWITCH
        if( $strTipoElemento != 'SWITCH' )
        {
            return array(
                'status'    => 'ERROR',
                'mensaje'   => $strTipoElemento . ' no es un tipo de elemento válido."'
            );
        }
        
        try
        {
            $this->emInfraestructura->getConnection()->beginTransaction();
            $this->emcom->getConnection()->beginTransaction();
         
            //setear el mensaje del modelo
            $strMensajeModelo = null;
            //setear el mensaje de la serie
            $strMensajeSerial = null;
            //setear el mensaje de la ip
            $strMensajeIp     = null;

            //identificar los errores si pertenece a solicitudes de estos tipos
            $booleanErrorTipoUM             = false;
            $booleanErrorTipoMA             = false;
            
            //identificar si se ingresa minimo una solicitud
            $booleanInsertSolicitudUM       = false;
            
            $strNombreElementoAnterior      = $arrayDatos['nombreAnterior'];
            $strNombreElementoNuevo         = $arrayDatos['nombreNuevo'];
            $arrayInterfacesElemento        = $arrayDatos['interfaces'];
            
            if( empty($strNombreElementoAnterior) )
            {
                throw new \Exception('No se ha recibido el nombre del elemento anterior.');
            }
            
            if( empty($strNombreElementoNuevo) )
            {
                throw new \Exception('No se ha recibido el nombre del elemento nuevo.');
            }
            
            if( empty($arrayInterfacesElemento) )
            {
                throw new \Exception('No se ha recibido las interfaces de los elementos.');
            }
            
            //Verificar si es un arreglo y que contenga informacion de las interfaces de los SWITCH
            if( !is_array($arrayInterfacesElemento) || count($arrayInterfacesElemento) == 0 )
            {
                throw new \Exception('No se ha recibido las interfaces de los elementos.');
            }
            
            $objEmpresa                     = $this->emInfraestructura->getRepository("schemaBundle:InfoEmpresaGrupo")->findOneByPrefijo("TN");
            
            //obtengo el parametro de la cantidad mínima para el CambioUltimaMillaMasivo
            $objAdmiParametroCabMin         = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                                array(  'nombreParametro'   => 'CAMBIO_ULTIMA_MILLA_MASIVO',
                                                                        'estado'            => 'Activo'));
            if( !is_object($objAdmiParametroCabMin) )
            {
                throw new \Exception('No se encontró el número de interfaces mínimas permitidas para Cambio Ultima Milla Masivo en el telcos.');
            }
            $objAdmiParametroDetMin         = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(
                                                                array(  "parametroId"   => $objAdmiParametroCabMin->getId(),
                                                                        "empresaCod"    => $objEmpresa->getId(),
                                                                        "valor1"        => "MinimoCambioUltimaMilla",
                                                                        "estado"        => "Activo"));
            if( !is_object($objAdmiParametroDetMin) )
            {
                throw new \Exception('No se encontró el número de interfaces mínimas permitidas para Cambio Ultima Milla Masivo en el telcos.'); 
            }
            
            //Verificar si el valor recibido del parametro es número caso contrario retorna el error
            $strMinimoInterfacesCambioUM    = $objAdmiParametroDetMin->getValor2();
            if( !is_numeric($strMinimoInterfacesCambioUM) )
            {
                throw new \Exception('No se encontró el número de interfaces mínimas permitidas para Cambio Ultima Milla Masivo en el telcos.');
            }
            //Verificar si corresponde al mínimo de interfaces de cambio de ultima milla caso contrario retorna el error
            $intMinimoInterfacesCambioUM    = intval($strMinimoInterfacesCambioUM);
            if( count($arrayInterfacesElemento) < $intMinimoInterfacesCambioUM )
            {
                throw new \Exception("Cantidad mínima de interfaces permitidas para migración masiva es ".
                                     "$intMinimoInterfacesCambioUM, por favor verificar");
            }
            
            //Obtener el objeto del elemento anterior
            $objInfoElementoAnterior        = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                    ->findOneBy(array(  'nombreElemento'    => $strNombreElementoAnterior,
                                                                        'estado'            => 'Activo'));
            //Obtener el objeto del elemento nuevo
            $objInfoElementoNuevo           = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                    ->findOneBy(array(  'nombreElemento'    => $strNombreElementoNuevo,
                                                                        'estado'            => 'Activo'));
            if( is_object($objInfoElementoAnterior) && is_object($objInfoElementoNuevo) )
            {
                //Obtengo los nombres de los tipos de los elementos
                $strTipoElementoAnterior    = $objInfoElementoAnterior->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                $strTipoElementoNuevo       = $objInfoElementoNuevo->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                //verificar que los tipos de los elementos no esten vacio y que sean SWITCH
                if( empty($strTipoElementoAnterior) || $strTipoElementoAnterior != 'SWITCH' || 
                    empty($strTipoElementoNuevo) || $strTipoElementoNuevo != 'SWITCH' )
                {
                    throw new \Exception("Los tipos de los elementos no son válidos.");
                }
                
                //verificar si el elemento anterior y nuevo son iguales
                if( $objInfoElementoAnterior->getId() == $objInfoElementoNuevo->getId() )
                {
                    //verifico que la variable no este vacía
                    if( isset($arrayDatos['modeloNuevo']) && !empty($arrayDatos['modeloNuevo']) )
                    {
                        //procedo a buscar el modelo del elemento nuevo
                        $strNombreModeloNuevo = $arrayDatos['modeloNuevo'];
                        $objTipoEleNuevoMod   = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
                                                        ->findOneBy(array('nombreTipoElemento'   => 'SWITCH',
                                                                          'estado'               => 'Activo'));
                        $objAdmiModeloNuevo   = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                        ->findOneBy(array('nombreModeloElemento' => $strNombreModeloNuevo,
                                                                          'tipoElementoId'       => $objTipoEleNuevoMod->getId(),
                                                                          'estado'               => 'Activo'));
                        //verifico si el modelo del elemento nuevo existe
                        if( is_object($objAdmiModeloNuevo) )
                        {
                            $objModeloAnterior  = $objInfoElementoNuevo->getModeloElementoId();
                            //verifico si el modelo del elemento nuevo no sea igual al anterior y que sea tipo SWITCH
                            if( $objModeloAnterior->getId() != $objAdmiModeloNuevo->getId() )
                            {
                                $strMensajeModelo = "Modelo actualizado.".
                                                    "<br>Anterior: ".$objModeloAnterior->getNombreModeloElemento().
                                                    "<br>Nuevo: ".$strNombreModeloNuevo;

                                //procedo a actualizar el modelo del elemento nuevo
                                $objInfoElementoNuevo->setModeloElementoId($objAdmiModeloNuevo);
                                $this->emInfraestructura->persist($objInfoElementoNuevo);

                                //agrego historial elemento
                                $objHistorialElemento = new InfoHistorialElemento();
                                $objHistorialElemento->setElementoId($objInfoElementoNuevo);
                                $objHistorialElemento->setEstadoElemento($objInfoElementoNuevo->getEstado());
                                $objHistorialElemento->setObservacion($strMensajeModelo);
                                $objHistorialElemento->setUsrCreacion($strUsrCreacion);
                                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                                $objHistorialElemento->setIpCreacion($strIpCreacion);
                                $this->emInfraestructura->persist($objHistorialElemento);
                            }
                        }
                        else
                        {
                            $objAdmiModeloNuevo = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                            ->findOneBy(array('nombreModeloElemento' => $strNombreModeloNuevo,
                                                                              'estado'               => 'Activo'));
                            if( is_object($objAdmiModeloNuevo) )
                            {
                                $strTipoEleNuevoMod = $objAdmiModeloNuevo->getTipoElementoId()->getNombreTipoElemento();
                                $strMensajeModelo   = "Modelo no actualizado.".
                                                      "<br>El modelo $strNombreModeloNuevo ".
                                                      "es un tipo de elemento $strTipoEleNuevoMod.";
                            }
                            else
                            {
                                $strMensajeModelo = "Modelo no actualizado.".
                                                    "<br>El nombre del modelo ($strNombreModeloNuevo) no existe en telcos.";
                            }
                        }
                    }
                    else
                    {
                        $strMensajeModelo = "Modelo no actualizado.".
                                            "<br>El nombre del modelo del elemento nuevo se encuentra vacío.";
                    }

                    //verifico que la variable no este vacía
                    if( isset($arrayDatos['serialNuevo']) && !empty($arrayDatos['serialNuevo']) )
                    {
                        $strSerieNuevo     = $arrayDatos['serialNuevo'];
                        $strSerieAnterior  = $objInfoElementoNuevo->getSerieFisica();
                        //verifico si la serie nueva es diferente a la actual
                        if( $strSerieAnterior != $strSerieNuevo )
                        {
                            //obtener el objeto del elemento de la serie nueva
                            $objEleSerialNuevo = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                        ->findOneBy(array('serieFisica' => $strSerieNuevo,
                                                                          'estado'      => 'Activo'));
                            //verifico que la nueva serie no este siendo usada
                            if( !is_object($objEleSerialNuevo) )
                            {
                                //procedo a actualizar la serie del elemento nuevo
                                $objInfoElementoNuevo->setSerieFisica($strSerieNuevo);
                                $this->emInfraestructura->persist($objInfoElementoNuevo);

                                $strMensajeSerial = "Serie actualizada.".
                                                    "<br>Anterior: ".$strSerieAnterior.
                                                    "<br>Nueva: ".$strSerieNuevo;

                                //agrego historial elemento
                                $objHistorialElemento = new InfoHistorialElemento();
                                $objHistorialElemento->setElementoId($objInfoElementoNuevo);
                                $objHistorialElemento->setEstadoElemento($objInfoElementoNuevo->getEstado());
                                $objHistorialElemento->setObservacion($strMensajeSerial);
                                $objHistorialElemento->setUsrCreacion($strUsrCreacion);
                                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                                $objHistorialElemento->setIpCreacion($strIpCreacion);
                                $this->emInfraestructura->persist($objHistorialElemento);
                            }
                            else
                            {
                                $strNomEleSerial  = $objEleSerialNuevo->getNombreElemento();
                                $strMensajeSerial = "Serie no actualizada.".
                                                    "<br>La nueva serie $strSerieNuevo ya esta siendo usada en el elemento $strNomEleSerial.";
                            }
                        }
                    }
                    else
                    {
                        $strMensajeSerial = "Serie no actualizada.".
                                            "<br>La serie del elemento nuevo se encuentra vacío.";
                    }

                    //verifico que la variable no este vacía
                    if( isset($arrayDatos['ipNueva']) && !empty($arrayDatos['ipNueva']) )
                    {
                        $strIpNueva = filter_var($arrayDatos['ipNueva'], FILTER_VALIDATE_IP);
                        if( !empty($strIpNueva) )
                        {
                            //obtengo la Ip del elemento anterior
                            $objIpAnterior  = $this->emInfraestructura->getRepository("schemaBundle:InfoIp")
                                                    ->findOneBy(array('elementoId' => $objInfoElementoNuevo->getId(),
                                                                      'estado'     => 'Activo') );
                            //seteo la variable de la ip anterior del elemento
                            $strIpAnterior  = $objIpAnterior->getIp();
                            //verifico si la ip nueva es diferente a la actual
                            if( $strIpAnterior != $strIpNueva )
                            {
                                $objInfoIp  = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                        ->createQueryBuilder('p')
                                                                        ->where('p.ip = :strIp')
                                                                        ->andWhere("p.estado != :estado")
                                                                        ->setParameter('strIp', $strIpNueva)
                                                                        ->setParameter('estado', 'Eliminado')
                                                                        ->getQuery()
                                                                        ->getOneOrNullResult();
                                //verifico que la nueva ip no este siendo usada
                                if( !is_object($objInfoIp) )
                                {
                                    //obtengo todas las Ip del elemento
                                    $arrayInfoIpElemento = $this->emInfraestructura->getRepository("schemaBundle:InfoIp")
                                                            ->findBy(array('elementoId' => $objInfoElementoNuevo->getId(),
                                                                           'estado'     => 'Activo') );
                                    //procedo a eliminar las Ip del elemento
                                    foreach( $arrayInfoIpElemento as $objInfoIpElemento )
                                    {
                                        $objInfoIpElemento->setEstado('Eliminado');
                                        $this->emInfraestructura->persist($objInfoIpElemento);
                                    }

                                    $strMensajeIp = "Ip actualizada.".
                                                    "<br>Anterior: ".$strIpAnterior.
                                                    "<br>Nueva: ".$strIpNueva;

                                    //agrego la nueva ip del elemento
                                    $objIpNueva = new InfoIP();
                                    $objIpNueva->setElementoId($objInfoElementoNuevo->getId());
                                    $objIpNueva->setIp($strIpNueva);
                                    $objIpNueva->setTipoIp($objIpAnterior->getTipoIp());
                                    $objIpNueva->setVersionIp($objIpAnterior->getVersionIp());
                                    $objIpNueva->setEstado("Activo");
                                    $objIpNueva->setUsrCreacion($strUsrCreacion);
                                    $objIpNueva->setFeCreacion(new \DateTime('now'));
                                    $objIpNueva->setIpCreacion($strIpCreacion);
                                    $this->emInfraestructura->persist($objIpNueva);

                                    //agrego historial elemento
                                    $objHistorialElemento = new InfoHistorialElemento();
                                    $objHistorialElemento->setElementoId($objInfoElementoNuevo);
                                    $objHistorialElemento->setEstadoElemento($objInfoElementoNuevo->getEstado());
                                    $objHistorialElemento->setObservacion($strMensajeIp);
                                    $objHistorialElemento->setUsrCreacion($strUsrCreacion);
                                    $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                                    $objHistorialElemento->setIpCreacion($strIpCreacion);
                                    $this->emInfraestructura->persist($objHistorialElemento);
                                }
                                else
                                {
                                    //obtengo el objeto del elemento de la Ip
                                    $objEleIpNueva    = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                                            ->find($objInfoIp->getElementoId());
                                    $strNomEleIpNueva = $objEleIpNueva->getNombreElemento();
                                    $strMensajeIp     = "Ip no actualizada.".
                                                        "<br>La nueva Ip $strIpNueva ya esta siendo usada en el elemento $strNomEleIpNueva.";
                                }
                            }
                        }
                        else
                        {
                            $strMensajeIp = "Ip no actualizada.".
                                            "<br>La nueva Ip ".$arrayDatos['ipNueva']." no es válida.";
                        }
                    }
                    else
                    {
                        $strMensajeIp = "Ip no actualizada.".
                                        "<br>La Ip del elemento nuevo se encuentra vacío.";
                    }
                }

                //validar los estados que deben tener los servicios de las interfaces.
                $arrayEstadoServicio        = array();
                //obtengo los parametros de los servicios permitidos
                $objAdmiParametroCabServ    = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneBy(array(  'nombreParametro'   => 'ESTADOS_SERVICIOS_PERMITIDOS',
                                                                        'estado'            => 'Activo'));
                if( is_object($objAdmiParametroCabServ) )
                {
                    $arrayAdmiParametroDet  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->findBy(array("parametroId"    => $objAdmiParametroCabServ->getId(),
                                                                            "estado"        => "Activo"));
                    foreach( $arrayAdmiParametroDet as $objAdmiParamDet )
                    {
                        array_push($arrayEstadoServicio, $objAdmiParamDet->getValor1());
                    }
                }
                //verificpo que el arreglo de estados de interfaces no se encuentre vacia
                if( count($arrayEstadoServicio) == 0 )
                {
                    throw new \Exception("No existe estados permitidos de servicios de interfaces en el Telcos.");
                }
                
                //validar que la interface de destino no tenga estos tipos de estados que se les agreguen al arreglo.
                $arrayEstadoInterface       = array();
                //obtengo los parametros de las interfaces permitidas
                $objAdmiParaCabEstadosInterfaces    = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                                        array(  'nombreParametro'   => 'ESTADOS_INTERFACES_PERMITIDAS',
                                                                                'estado'            => 'Activo' ));
                if( is_object($objAdmiParaCabEstadosInterfaces) )
                {
                    $arrayAdmiParametroDet      = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->findBy(array("parametroId"   => $objAdmiParaCabEstadosInterfaces->getId(),
                                                                           "valor2"        => "CAMBIO_UM",
                                                                           "estado"        => "Activo"));
                    foreach( $arrayAdmiParametroDet as $objAdmiParametroDet )
                    {
                        array_push($arrayEstadoInterface, $objAdmiParametroDet->getValor1());
                    }
                }
                
                //obtengo los parametro del tipo proceso de CambioUltimaMilla
                $objAdmiParametroCab        = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                                    array(  'nombreParametro'   => 'CAMBIO_ULTIMA_MILLA_MASIVO',
                                                                            'estado'            => 'Activo'));
                if( !is_object($objAdmiParametroCab) )
                {
                    throw new \Exception('No se encontró el tipo de proceso (CambioUltimaMillaMasivo) en el telcos.');  
                }
                $objAdmiParametroDet        = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(
                                                                    array(  "parametroId"   => $objAdmiParametroCab->getId(),
                                                                            "empresaCod"    => $objEmpresa->getId(),
                                                                            "valor1"        => "CambioUltimaMilla",
                                                                            "estado"        => "Activo"));
                if( !is_object($objAdmiParametroDet) )
                {
                    throw new \Exception('No se encontró el tipo de proceso (CambioUltimaMillaMasivo) en el telcos.');  
                }
                
                //Crear Cabecera Procesos Masivos
                $objInfoProcesoMasivoCab    = new InfoProcesoMasivoCab();
                $objInfoProcesoMasivoCab->setTipoProceso($objAdmiParametroDet->getValor2());
                $objInfoProcesoMasivoCab->setEmpresaCod($objEmpresa->getId());
                $objInfoProcesoMasivoCab->setEstado('Pendiente');
                $objInfoProcesoMasivoCab->setFeCreacion(new \DateTime('now'));
                $objInfoProcesoMasivoCab->setUsrCreacion($strUsrCreacion);
                $objInfoProcesoMasivoCab->setIpCreacion($strIpCreacion);
                $this->emInfraestructura->persist($objInfoProcesoMasivoCab);
                
                foreach( $arrayInterfacesElemento as $arrayDatosInterface )
                {
                    $strNombreInterfaceAnterior         = $arrayDatosInterface['nombreInterfaceAnterior'];
                    $strNombreInterfaceNuevo            = $arrayDatosInterface['nombreInterfaceNuevo'];
                    
                    if( empty($strNombreInterfaceAnterior) )
                    {
                        throw new \Exception('No se ha recibido la interface del elemento anterior.');
                    }
                    if( empty($strNombreInterfaceNuevo) )
                    {
                        throw new \Exception('No se ha recibido la interface del elemento nuevo.');
                    }
                    
                    //Obtengo el objeto de la interfaces de los elementos con el primer caracter de la interface en mayuscula o minuscula
                    $strNombreInterfaceAnteriorUpper    = ucfirst($strNombreInterfaceAnterior);
                    $strNombreInterfaceAnteriorLower    = lcfirst($strNombreInterfaceAnterior);
                    $objInterfaceElementoAnterior       = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                ->createQueryBuilder('p')
                                                                ->where('p.elementoId = :elementoId')
                                                                ->andWhere("p.nombreInterfaceElemento = :nombreInterfaceElementoUpper OR ".
                                                                           "p.nombreInterfaceElemento = :nombreInterfaceElementoLower")
                                                                ->setParameter('elementoId', $objInfoElementoAnterior->getId())
                                                                ->setParameter('nombreInterfaceElementoUpper',$strNombreInterfaceAnteriorUpper)
                                                                ->setParameter('nombreInterfaceElementoLower',$strNombreInterfaceAnteriorLower)
                                                                ->getQuery()
                                                                ->getOneOrNullResult();
                    $strNombreInterfaceNuevoUpper       = ucfirst($strNombreInterfaceNuevo);
                    $strNombreInterfaceNuevoLower       = lcfirst($strNombreInterfaceNuevo);
                    $objInterfaceElementoNuevo          = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                ->createQueryBuilder('p')
                                                                ->where('p.elementoId = :elementoId')
                                                                ->andWhere("p.nombreInterfaceElemento = :nombreInterfaceElementoUpper OR ".
                                                                           "p.nombreInterfaceElemento = :nombreInterfaceElementoLower")
                                                                ->setParameter('elementoId', $objInfoElementoNuevo->getId())
                                                                ->setParameter('nombreInterfaceElementoUpper',$strNombreInterfaceNuevoUpper)
                                                                ->setParameter('nombreInterfaceElementoLower',$strNombreInterfaceNuevoLower)
                                                                ->getQuery()
                                                                ->getOneOrNullResult();
                    //verifico que exista la interface anterior
                    if( is_object($objInterfaceElementoAnterior) )
                    {
                        if( is_object($objInterfaceElementoNuevo) )
                        {
                            //verifica que la interface no tenga estos estados
                            $strEstadoInterfaceNuevo = $objInterfaceElementoNuevo->getEstado();
                            if( in_array($strEstadoInterfaceNuevo, $arrayEstadoInterface) )
                            {
                                throw new \Exception("La interface($strNombreInterfaceNuevo) de ".
                                                     "destino se encuentra en estado($strEstadoInterfaceNuevo).");
                            }

                            if( !is_object($objAdmiParametroCab) )
                            {
                            throw new \Exception('No se encontró los estados de validación para los servicios '.
                                            'de cambio de última milla masivo en el telcos.');  
                            }
                            $arrayParametrosEstados = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                array(  "parametroId"   => $objAdmiParametroCab->getId(),
                                        "empresaCod"    => $objEmpresa->getId(),
                                        "valor1"        => "EstadoNoPermitido",
                                        "estado"        => "Activo"));
                                        
                            foreach($arrayParametrosEstados as $objParametroEstados)
                            {
                                $arrayToEstado[] = $objParametroEstados->getValor2();
                            }

                            $arrayInfoServicioTecnicoNuevo = $this->emcom->getRepository('schemaBundle:InfoServicioTecnico')
                                        ->createQueryBuilder('st')
                                        ->innerJoin('schemaBundle:InfoServicio', 's', 'WITH',
                                                's.id = st.servicioId')
                                        ->where('s.estado NOT IN (:ArrayEstados)')
                                        ->andWhere("st.interfaceElementoId = :interfaceElementoId")
                                        ->setParameter('interfaceElementoId', $objInterfaceElementoNuevo->getId())
                                        ->setParameter('ArrayEstados', array_values($arrayToEstado))
                                        ->getQuery()
                                        ->getResult();

                            //verifica que la interface no contenga servicios tecnicos
                            if( count($arrayInfoServicioTecnicoNuevo) > 0 )
                            {
                                throw new \Exception("La interface($strNombreInterfaceNuevo) de destino contiene servicios asociados.");
                            }
                        }
                        
                        //Obtengo los servicios tecnicos de la interface
                        $arrayInfoServicioTecnico       = $this->emcom->getRepository('schemaBundle:InfoServicioTecnico')
                                                                    ->findByInterfaceElementoId($objInterfaceElementoAnterior->getId());
                        if( count($arrayInfoServicioTecnico) == 0 )
                        {
                            throw new \Exception("No contiene servicios la interface($strNombreInterfaceAnterior) del elemento en el telcos.");
                        }
                        foreach( $arrayInfoServicioTecnico as $objInfoServicioTecnico )
                        {
                            //Obtengo el objeto del servicio
                            $objInfoServicio            = $objInfoServicioTecnico->getServicioId();
                            if( is_object($objInfoServicio) )
                            {
                                //verifico que el servicio se encuentre en estado permitido para cambio de ultima milla
                                $strEstadoServicio      = $objInfoServicio->getEstado();
                                if( in_array($strEstadoServicio, $arrayEstadoServicio) )
                                {
                                    $arrayParametros    = array(
                                                            "objInfoServicioTecnico"        => $objInfoServicioTecnico,
                                                            "objInfoServicio"               => $objInfoServicio,
                                                            "objInfoProcesoMasivoCab"       => $objInfoProcesoMasivoCab,
                                                            "objInterfaceElementoAnterior"  => $objInterfaceElementoAnterior,
                                                            "objInterfaceElementoNuevo"     => $objInterfaceElementoNuevo,
                                                            "objInfoElementoNuevo"          => $objInfoElementoNuevo,
                                                            "strInterfaceElementoNuevo"     => $strNombreInterfaceNuevo,
                                                            "strUsrCreacion"                => $strUsrCreacion,
                                                            "strIpCreacion"                 => $strIpCreacion
                                                        );
                                    $arrayRespuesta     = $this->crearSolicitudCambioUMProcesoMasivo($arrayParametros);
                                    if( $arrayRespuesta['status'] == 'ERROR' )
                                    {
                                        throw new \Exception($arrayRespuesta['mensaje']);
                                    }
                                    else if( $arrayRespuesta['mensaje'] == 'EXISTENTE_UM' )
                                    {
                                        $booleanErrorTipoUM = true;
                                    }
                                    else if( $arrayRespuesta['mensaje'] == 'EXISTENTE_MA' )
                                    {
                                        $booleanErrorTipoMA = true;
                                    }
                                    else if( $arrayRespuesta['mensaje'] == 'EXISTENTE_AMBOS' )
                                    {
                                        $booleanErrorTipoUM = true;
                                        $booleanErrorTipoMA = true;
                                    }
                                    else if( $arrayRespuesta['mensaje'] == 'INSERTADO' )
                                    {
                                        $booleanInsertSolicitudUM = true;
                                    }
                                }
                            }
                        }
                    }
                    else
                    {
                        throw new \Exception("No existe la interface($strNombreInterfaceAnterior) del ".
                                             "elemento($strNombreElementoAnterior) en el telcos.");  
                    }
                }
                
                if( !$booleanInsertSolicitudUM && $booleanErrorTipoUM && $booleanErrorTipoMA )
                {
                    throw new \Exception("Ya se encuentra creada una solicitud de 'CAMBIO ULTIMA MILLA' y 'MIGRACION ANILLO', ".
                                         "para estos servicios.");
                }
                else if( !$booleanInsertSolicitudUM && $booleanErrorTipoUM )
                {
                    throw new \Exception("Ya se encuentra creada una solicitud para 'CAMBIO ULTIMA MILLA', para estos servicios.");
                }
                else if( !$booleanInsertSolicitudUM && $booleanErrorTipoMA )
                {
                    throw new \Exception("Ya se encuentra creada una solicitud para 'MIGRACION ANILLO', para estos servicios.");
                }
                
                $this->emcom->flush();
                $this->emInfraestructura->flush();
                
                $objInfoProcesoMasivoDet    = $this->emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                                ->findOneByProcesoMasivoCabId($objInfoProcesoMasivoCab->getId());
                $arrayInfoProcesoMasivoDet  = $this->emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                                ->findByProcesoMasivoCabId($objInfoProcesoMasivoCab->getId());
                //verifico que la solicitudes del cambio de ultima milla fueron creadas
                if( count($arrayInfoProcesoMasivoDet) == 0 )
                {
                    throw new \Exception("No se pudo crear la Solicitud de Cambio de Ultima Milla (ProcesoMasivo).");
                }
                
                //actualizo la cantidad de servicios por el total de detalles de cabecera
                $objInfoProcesoMasivoCab->setCantidadServicios(count($arrayInfoProcesoMasivoDet));
                //actualizo la solicitud id del primer detalle de cabecera
                $objInfoProcesoMasivoCab->setSolicitudId($objInfoProcesoMasivoDet->getSolicitudId());
                $this->emInfraestructura->persist($objInfoProcesoMasivoCab);

                $strMensajeModelo = !empty($strMensajeModelo) ? "<br>$strMensajeModelo" : "";
                $strMensajeSerial = !empty($strMensajeSerial) ? "<br>$strMensajeSerial" : "";
                $strMensajeIp     = !empty($strMensajeIp)     ? "<br>$strMensajeIp"     : "";
                $arrayRespuesta   = array(
                    'status'  => 'OK',
                    'mensaje' => "Se creó la Solicitud de Cambio de Ultima Milla (ProcesoMasivo).".
                                 $strMensajeModelo.
                                 $strMensajeSerial.
                                 $strMensajeIp
                );
            }
            else
            {
                //Obtener el objeto del elemento anterior por su nombre
                $objInfoElementoAnterior    = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                ->findOneBy(array(  'nombreElemento'    => $strNombreElementoAnterior,
                                                                    'estado'            => 'Eliminado'));
                //Obtener el objeto del elemento nuevo por su nombre
                $objInfoElementoNuevo       = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                ->findOneBy(array(  'nombreElemento'    => $strNombreElementoNuevo,
                                                                    'estado'            => 'Eliminado'));
                if( is_object($objInfoElementoAnterior) && is_object($objInfoElementoNuevo) )
                {
                    throw new \Exception("Los elementos $strNombreElementoAnterior y $strNombreElementoNuevo ".
                                         "se encuentran en estado 'Eliminado'.");
                }
                else if( is_object($objInfoElementoAnterior) )
                {
                    throw new \Exception("$strNombreElementoAnterior se encuentra en estado 'Eliminado'.");
                }
                else if( is_object($objInfoElementoNuevo) )
                {
                    throw new \Exception("$strNombreElementoNuevo se encuentra en estado 'Eliminado'.");
                }
                else
                {
                    throw new \Exception("Los elementos no existen en Telcos");
                }          
            }
            
            $this->emcom->flush();
            $this->emInfraestructura->flush();
            
            if( $this->emcom->getConnection()->isTransactionActive() )
            {
                $this->emcom->getConnection()->commit();
                $this->emcom->getConnection()->close();
            }
            if( $this->emInfraestructura->getConnection()->isTransactionActive() )
            {
                $this->emInfraestructura->getConnection()->commit();
                $this->emInfraestructura->getConnection()->close();
            }
        }
        catch(\Exception $e)
        {
            $this->utilService->insertError('Telcos+', 
                                            'InfoElementoService.updateCambioDispositivoElementoSwitchTN', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }
            
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
                $this->emInfraestructura->getConnection()->close();
            }
            
            $arrayRespuesta = array(
                'status'  => 'ERROR',
                'mensaje' => 'Error: '.$e->getMessage()
            );
        }
        return $arrayRespuesta;
    }
    
    /**
     * Documentación para el método 'crearSolicitudCambioUMProcesoMasivo'.
     * 
     * Metodo que crea la solicitud del cambio de ultima milla para todos los servicios de la misma ultima milla con proceso masivo
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 02-10-2019
     * 
     * @param Array $arrayParametros [  
     *                                  objInfoServicioTecnico,         Servicio Tecnico de la interface del elemento
     *                                  objInfoServicio,                Servicio de la interface del elemento
     *                                  objInfoProcesoMasivoCab,        Cabecera del proceso masivo
     *                                  objInterfaceElementoAnterior,   Interface del elemento anterior
     *                                  objInterfaceElementoNuevo,      Interface del elemento nuevo
     *                                  objInfoElementoNuevo,           Elemento nuevo
     *                                  strInterfaceElementoNuevo,      Nombre de la interface del elemento nuevo
     *                                  strUsrCreacion,                 Usuario de creacion, quien ejecuta la accion
     *                                  strIpCreacion                   Ip de quien ejecuta la accion
     *                              ]
     * @return Array $arrayRespuesta [ status , mensaje ]
     */
    public function crearSolicitudCambioUMProcesoMasivo($arrayParametros)
    {
        $objInfoServicioTecnico             = $arrayParametros['objInfoServicioTecnico'];
        $objInfoServicio                    = $arrayParametros['objInfoServicio'];
        $objInfoProcesoMasivoCab            = $arrayParametros['objInfoProcesoMasivoCab'];
        $objInterfaceElementoAnterior       = $arrayParametros['objInterfaceElementoAnterior'];
        $objInterfaceElementoNuevo          = $arrayParametros['objInterfaceElementoNuevo'];
        $objInfoElementoNuevo               = $arrayParametros['objInfoElementoNuevo'];
        $strInterfaceElementoNuevo          = $arrayParametros['strInterfaceElementoNuevo'];
        $strUsrCreacion                     = $arrayParametros['strUsrCreacion'];
        $strIpCreacion                      = $arrayParametros['strIpCreacion'];
                    
        //identificar si se ingresaron solicitudes
        $booleanInsertDetalle               = false;
        //identificar los errores si pertenece a solicitudes de estos tipos
        $booleanErrorTipoUM                 = false;
        $booleanErrorTipoMA                 = false;
        
        $strLoginesAux                      = "";
        
        //variables para conexion a la base de datos mediante conexion OCI
        $arrayOciCon                        = array();
        $arrayOciCon['user_comercial']      = $this->container->getParameter('user_comercial');
        $arrayOciCon['passwd_comercial']    = $this->container->getParameter('passwd_comercial');
        $arrayOciCon['dsn']                 = $this->container->getParameter('database_dsn');
        
        try
        {
            $arrayParametrosMismaUm                                     = array();
            $arrayParametrosMismaUm['intPuntoId']                       = $objInfoServicio->getPuntoId()->getId();
            $arrayParametrosMismaUm['intElementoId']                    = $objInfoServicioTecnico->getElementoId();
            $arrayParametrosMismaUm['intInterfaceElementoId']           = $objInfoServicioTecnico->getInterfaceElementoId();
            $arrayParametrosMismaUm['intElementoClienteId']             = $objInfoServicioTecnico->getElementoClienteId();
            $arrayParametrosMismaUm['intInterfaceElementoClienteId']    = $objInfoServicioTecnico->getInterfaceElementoClienteId();
            $arrayParametrosMismaUm['intUltimaMillaId']                 = $objInfoServicioTecnico->getUltimaMillaId();
            $arrayParametrosMismaUm['intTercerizadoraId']               = $objInfoServicioTecnico->getTercerizadoraId();
            $arrayParametrosMismaUm['intElementoContenedorId']          = $objInfoServicioTecnico->getElementoContenedorId();
            $arrayParametrosMismaUm['intElementoConectorId']            = $objInfoServicioTecnico->getElementoConectorId();
            $arrayParametrosMismaUm['intInterfaceElementoConectorId']   = $objInfoServicioTecnico->getInterfaceElementoConectorId();
            $arrayParametrosMismaUm['strTipoEnlace']                    = $objInfoServicioTecnico->getTipoEnlace();
            $arrayParametrosMismaUm['ociCon']                           = $arrayOciCon;
            
            $arrayParametrosRespuesta       = $this->emcom->getRepository('schemaBundle:InfoServicioTecnico')
                                                                ->getServiciosMismaUm($arrayParametrosMismaUm);
            if( $arrayParametrosRespuesta['strStatus'] == "ERROR" )
            {
                $this->utilServicio->insertError('Telcos+', 
                                                 'InfoElementoService.crearSolicitudCambioUMProcesoMasivo', 
                                                  $arrayParametrosRespuesta['strMensaje'],
                                                  $strUsrCreacion,
                                                  $strIpCreacion);
                throw new \Exception("Error al recuperar servicios con misma UM.");
            }
            
            $objTipoSolicitud               = $this->emcom->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                    ->findOneBy(array(  "descripcionSolicitud" => "SOLICITUD CAMBIO ULTIMA MILLA", 
                                                                        "estado"               => "Activo"));
            $objTipoSolicitudMigracionAni   = $this->emcom->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                    ->findOneBy(array(  "descripcionSolicitud" => "SOLICITUD MIGRACION ANILLO", 
                                                                        "estado"               => "Activo"));
            $objCaracteristicaUm            = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array(  "descripcionCaracteristica" => "SERVICIO_MISMA_ULTIMA_MILLA",
                                                                        "estado"                    => "Activo"));
            $objCaracInterfaceAnterior      = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array(  "descripcionCaracteristica" => "INTERFACE_ELEMENTO_ANTERIOR_ID",
                                                                        "estado"                    => "Activo"));
            $objCaracElementoNuevo          = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array(  "descripcionCaracteristica" => "ELEMENTO_ID",
                                                                        "estado"                    => "Activo"));
            $objCaracInterfaceNueva         = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array(  "descripcionCaracteristica" => "INTERFACE_ELEMENTO_NUEVA_ID",
                                                                        "estado"                    => "Activo"));
            $objCaracInterfaceNuevaNombre   = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array(  "descripcionCaracteristica" => "INTERFACE_ELEMENTO_NUEVA_NOMBRE",
                                                                        "estado"                    => "Activo"));
            
            $arrayRegistrosServicios        = $arrayParametrosRespuesta['arrayRegistros'];
            
            //recupera logines auxiliares de los servicios a crear solicitud para setear historiales de servicios
            foreach( $arrayRegistrosServicios as $strIdServicio )
            {
                $objInfoServicio            = $this->emcom->getRepository('schemaBundle:InfoServicio')->find($strIdServicio);
                if( is_object($objInfoServicio) )
                {
                    $strLoginesAux          = $strLoginesAux . $objInfoServicio->getLoginAux() . ' ';
                }
            
            }
            
            foreach( $arrayRegistrosServicios as $strIdServicio )
            {
                $objInfoServicio            = $this->emcom->getRepository('schemaBundle:InfoServicio')->find($strIdServicio);
                if( is_object($objInfoServicio) )
                {
                    //valido que no se haya creado una solicitud con el mismo servicio con los dos tipos de solicitud
                    $objSolicitudTipoUM     = $this->emcom->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                        ->createQueryBuilder('p')
                                                        ->where('p.servicioId = :servicioId')
                                                        ->andWhere("p.tipoSolicitudId = :tipoSolicitudId")
                                                        ->andWhere("p.estado = 'FactibilidadEnProceso' OR p.estado = 'AsignadoTarea' OR ".
                                                                   "p.estado = 'Asignada'")
                                                        ->setParameter('servicioId', $objInfoServicio->getId())
                                                        ->setParameter('tipoSolicitudId', $objTipoSolicitud->getId())
                                                        ->getQuery()
                                                        ->getOneOrNullResult();
                    $objSolicitudTipoMA     = $this->emcom->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                    ->findOneBy(array("servicioId"      => $objInfoServicio->getId(),
                                                                      "tipoSolicitudId" => $objTipoSolicitudMigracionAni->getId(),
                                                                      "estado"          => "Asignada"));
                    $booleanErrorTipoUM     = $booleanErrorTipoUM || is_object($objSolicitudTipoUM);
                    $booleanErrorTipoMA     = $booleanErrorTipoMA || is_object($objSolicitudTipoMA);
                    
                    if( !is_object($objSolicitudTipoUM) && !is_object($objSolicitudTipoMA) )
                    {
                        //valido que no se haya creado una solicitud caracteristicas con el mismo servicio
                        $objDetalleSolCaracteristica    = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                ->createQueryBuilder('p')
                                                                ->join('p.detalleSolicitudId', 'd')
                                                                ->where('p.valor = :valor')
                                                                ->andWhere("p.caracteristicaId = :caracteristicaId")
                                                                ->andWhere("p.estado = 'FactibilidadEnProceso' OR ".
                                                                           "p.estado = 'Asignada'")
                                                                ->andWhere("d.tipoSolicitudId = :tipoSolicitudId")
                                                                ->andWhere("d.estado = 'FactibilidadEnProceso' OR d.estado = 'AsignadoTarea' OR ".
                                                                           "d.estado = 'Asignada'")
                                                                ->setParameter('valor', $objInfoServicio->getId())
                                                                ->setParameter('caracteristicaId', $objCaracteristicaUm->getId())
                                                                ->setParameter('tipoSolicitudId', $objTipoSolicitud->getId())
                                                                ->getQuery()
                                                                ->getOneOrNullResult();
                        if( !is_object($objDetalleSolCaracteristica) )
                        {
                            //crear solicitud
                            $objDetalleSolicitud    = new InfoDetalleSolicitud();
                            $objDetalleSolicitud->setServicioId($objInfoServicio);
                            $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
                            $objDetalleSolicitud->setUsrCreacion($strUsrCreacion);
                            $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                            $objDetalleSolicitud->setEstado("FactibilidadEnProceso");
                            $this->emcom->persist($objDetalleSolicitud);
                            $this->emcom->flush();
                            
                            //procedo con la creación de las caracteristicas por cada servicio de la solicitud
                            $objDetSolCaracteristicaUm      = new InfoDetalleSolCaract();
                            $objDetSolCaracteristicaUm->setCaracteristicaId($objCaracteristicaUm);
                            $objDetSolCaracteristicaUm->setDetalleSolicitudId($objDetalleSolicitud);
                            $objDetSolCaracteristicaUm->setValor($objInfoServicio->getId());
                            $objDetSolCaracteristicaUm->setEstado("FactibilidadEnProceso");
                            $objDetSolCaracteristicaUm->setUsrCreacion($strUsrCreacion);
                            $objDetSolCaracteristicaUm->setFeCreacion(new \DateTime('now'));
                            $this->emcom->persist($objDetSolCaracteristicaUm);
                            $this->emcom->flush();

                            //agregar historial a la solicitud
                            $objDetalleSolicitudHistorial   = new InfoDetalleSolHist();
                            $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                            $objDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
                            $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                            $objDetalleSolicitudHistorial->setUsrCreacion($strUsrCreacion);
                            $objDetalleSolicitudHistorial->setEstado($objDetalleSolicitud->getEstado());
                            $this->emcom->persist($objDetalleSolicitudHistorial);
                            $this->emcom->flush();

                            //agregar servicio historial
                            $objServicioHistorial           = new InfoServicioHistorial();
                            $objServicioHistorial->setServicioId($objInfoServicio);
                            $objServicioHistorial->setIpCreacion($strIpCreacion);
                            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                            $objServicioHistorial->setEstado($objInfoServicio->getEstado());
                            $objServicioHistorial->setObservacion('Se creo solicitud de cambio de UM (ProcesoMasivo) en estado: '.
                                                                  'FactibilidadEnProceso, para los servicios: '.$strLoginesAux);
                            $this->emcom->persist($objServicioHistorial);
                            $this->emcom->flush();
                            
                            //procedo con la creación de la caracteristica para la interface anterior
                            $objDetSolInterAnterior         = new InfoDetalleSolCaract();
                            $objDetSolInterAnterior->setCaracteristicaId($objCaracInterfaceAnterior);
                            $objDetSolInterAnterior->setDetalleSolicitudId($objDetalleSolicitud);
                            $objDetSolInterAnterior->setValor($objInterfaceElementoAnterior->getId());
                            $objDetSolInterAnterior->setEstado("Activo");
                            $objDetSolInterAnterior->setUsrCreacion($strUsrCreacion);
                            $objDetSolInterAnterior->setFeCreacion(new \DateTime('now'));
                            $this->emcom->persist($objDetSolInterAnterior);
                            $this->emcom->flush();

                            //procedo con la creación de la caracteristica para la interface nueva
                            if( is_object($objInterfaceElementoNuevo) )
                            {
                                $objDetSolInterfaceNueva    = new InfoDetalleSolCaract();
                                $objDetSolInterfaceNueva->setCaracteristicaId($objCaracInterfaceNueva);
                                $objDetSolInterfaceNueva->setDetalleSolicitudId($objDetalleSolicitud);
                                $objDetSolInterfaceNueva->setValor($objInterfaceElementoNuevo->getId());
                                $objDetSolInterfaceNueva->setEstado("Activo");
                                $objDetSolInterfaceNueva->setUsrCreacion($strUsrCreacion);
                                $objDetSolInterfaceNueva->setFeCreacion(new \DateTime('now'));
                                $this->emcom->persist($objDetSolInterfaceNueva);
                                $this->emcom->flush();
                            }
                            else
                            {
                                //procedo con la creación de la caracteristica para el elemento nuevo
                                $objDetSolElementoNuevo     = new InfoDetalleSolCaract();
                                $objDetSolElementoNuevo->setCaracteristicaId($objCaracElementoNuevo);
                                $objDetSolElementoNuevo->setDetalleSolicitudId($objDetalleSolicitud);
                                $objDetSolElementoNuevo->setValor($objInfoElementoNuevo->getId());
                                $objDetSolElementoNuevo->setEstado("Activo");
                                $objDetSolElementoNuevo->setUsrCreacion($strUsrCreacion);
                                $objDetSolElementoNuevo->setFeCreacion(new \DateTime('now'));
                                $this->emcom->persist($objDetSolElementoNuevo);
                                $this->emcom->flush();

                                //procedo con la creación de la caracteristica para la interface nueva
                                $objDetSolInterfaceNueva    = new InfoDetalleSolCaract();
                                $objDetSolInterfaceNueva->setCaracteristicaId($objCaracInterfaceNuevaNombre);
                                $objDetSolInterfaceNueva->setDetalleSolicitudId($objDetalleSolicitud);
                                $objDetSolInterfaceNueva->setValor($strInterfaceElementoNuevo);
                                $objDetSolInterfaceNueva->setEstado("Activo");
                                $objDetSolInterfaceNueva->setUsrCreacion($strUsrCreacion);
                                $objDetSolInterfaceNueva->setFeCreacion(new \DateTime('now'));
                                $this->emcom->persist($objDetSolInterfaceNueva);
                                $this->emcom->flush();
                            }
                            
                            //Crear Detalles Procesos Masivos
                            $objInfoProcesoMasivoDet    = new InfoProcesoMasivoDet();
                            $objInfoProcesoMasivoDet->setProcesoMasivoCabId($objInfoProcesoMasivoCab);
                            $objInfoProcesoMasivoDet->setServicioId($objInfoServicio->getId());
                            $objInfoProcesoMasivoDet->setPuntoId($objInfoServicio->getPuntoId()->getId());
                            $objInfoProcesoMasivoDet->setSolicitudId($objDetalleSolicitud->getId());
                            $objInfoProcesoMasivoDet->setEstado('Pendiente');
                            $objInfoProcesoMasivoDet->setFeCreacion(new \DateTime('now'));
                            $objInfoProcesoMasivoDet->setUsrCreacion($strUsrCreacion);
                            $objInfoProcesoMasivoDet->setIpCreacion($strIpCreacion);
                            $this->emInfraestructura->persist($objInfoProcesoMasivoDet);
                            $this->emInfraestructura->flush();
                            
                            //seteo a true por lo menos si se ingreso una solicitud
                            $booleanInsertDetalle   = true;
                        }
                    }
                }
            }
            
            if( $booleanInsertDetalle )
            {
                $arrayRespuesta =   array(  
                    'status'  => 'OK',
                    'mensaje' => "INSERTADO"
                );
            }
            else
            {
                if( $booleanErrorTipoUM && $booleanErrorTipoMA )
                {
                    $arrayRespuesta =   array(  
                        'status'  => 'OK',
                        'mensaje' => "EXISTENTE_AMBOS"
                    );
                }
                else if( $booleanErrorTipoUM )
                {
                    $arrayRespuesta =   array(  
                        'status'  => 'OK',
                        'mensaje' => "EXISTENTE_UM"
                    );
                }
                else if( $booleanErrorTipoMA )
                {
                    $arrayRespuesta =   array(  
                        'status'  => 'OK',
                        'mensaje' => "EXISTENTE_MA"
                    );
                }
                else
                {
                    $arrayRespuesta =   array(  
                        'status'  => 'OK',
                        'mensaje' => "NULL"
                    );
                }
            }
        }
        catch(\Exception $e)
        {
            $this->utilService->insertError('Telcos+', 
                                            'InfoElementoService.crearSolicitudCambioUM', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
            $arrayRespuesta =   array(  
                'status'  => 'ERROR',
                'mensaje' => $e->getMessage()
            );
        }
        return $arrayRespuesta;
    }
    
    /**
     * Documentación para el método 'realizarCambioUMProcesoMasivo'.
     * 
     * Metodo que realiza el cambio de ultima milla de interfaces de elementos SWITCH por medio de proceso masivo
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 25-10-2019
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 26-02-2020 - Se agrega al cambio de última milla masivo el tipo de medio radio, solo se crea las interfaces nuevas
     *                           no se genera la factibilidad ni se ejecuta cambio de última milla.
     *
     * @author Rafael Vera <rsvera@telconet.ec>
     * @version 1.2 31-05-2023 - Se implementó la validación para la capacidad de los servicios.
     *
     * @param Array $arrayParametros [  
     *                                  servicios,                      Arreglo de los ID de los servicios
     *                                  strUsrCreacion,                 Usuario de creacion, quien ejecuta la accion
     *                                  strIpCreacion                   Ip de quien ejecuta la accion
     *                              ]
     * @return Array $arrayResultado [ status , mensaje , servicio ]
     */
    public function realizarCambioUMProcesoMasivo($arrayParametros)
    {
        $arrayRespuesta     = array();
        //obtengo los id de los servicios recibidos por cada detalle cabecera
        $arrayServicios     = $arrayParametros['servicios'];
        $strUsrCreacion     = $arrayParametros['strUsrCreacion'];
        $strIpCreacion      = filter_var($arrayParametros['strIpCreacion'], FILTER_VALIDATE_IP);
        //se setea el id del servicio que se cae en la ejecucion
        $intIdServicioError = null;
        //se setea el login del servicio que contenga algún error
        $strLoginError      = null;
        //se setea el login auxiliar del servicio que contenga algún error
        $strLoginAuxError   = null;
        //arreglo de los id de las interfaces anterior y nueva
        $arrayIdInteServ    = array();
        
        if( empty($strIpCreacion) )
        {
            foreach( $arrayServicios as $strIdServicio )
            {
                $arrayRespuesta[] = array(
                    'status'    => 500,
                    'mensaje'   => $arrayParametros['strIpCreacion'] . " no es una IP válida.",
                    'servicio'  => intval($strIdServicio)
                );
            }
            $arrayResultado['servicios'] = $arrayRespuesta;
            return $arrayResultado;
        }
        
        $serviceFactibilidadCambioUm    = $this->container->get('planificacion.FactibilidadCambioUltimaMilla');
        $serviceCambioPuerto            = $this->container->get('tecnico.InfoCambiarPuerto');
        $serviceDataTecnica             = $this->container->get('tecnico.DataTecnica');
        $objEmpresa                     = $this->emInfraestructura->getRepository("schemaBundle:InfoEmpresaGrupo")->findOneByPrefijo("TN");
        $objTipoSolicitud               = $this->emcom->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                    ->findOneBy(array(  "descripcionSolicitud" => "SOLICITUD CAMBIO ULTIMA MILLA", 
                                                                        "estado" => "Activo"));
        $objCaracInterfaceAnt           = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array(  "descripcionCaracteristica" => "INTERFACE_ELEMENTO_ANTERIOR_ID",
                                                                            "estado"                    => "Activo"));
        $objCaracElementoNuevo          = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array(  "descripcionCaracteristica" => "ELEMENTO_ID",
                                                                            "estado"                    => "Activo"));
        $objCaracInterfaceNueva         = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array(  "descripcionCaracteristica" => "INTERFACE_ELEMENTO_NUEVA_ID",
                                                                    "estado"                    => "Activo"));
        $objCaracInterfaceNuevaNombre   = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array(  "descripcionCaracteristica" => "INTERFACE_ELEMENTO_NUEVA_NOMBRE",
                                                                    "estado"                    => "Activo"));
        //Se realiza el cambio de ultima milla por cada servicio
        foreach( $arrayServicios as $strIdServicio )
        {
            try
            {
                $this->emInfraestructura->getConnection()->beginTransaction();
                $this->emcom->getConnection()->beginTransaction();

                $intIdServicioError     = $strIdServicio;
                $objInfoServicio        = $this->emcom->getRepository('schemaBundle:InfoServicio')->find($strIdServicio);
                $strLoginAuxError       = $objInfoServicio->getLoginAux();
                $strLoginError          = $objInfoServicio->getPuntoId()->getLogin();
                $objDetalleSolicitud    = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                    ->findOneBy(array(  'servicioId'      => $objInfoServicio->getId(),
                                                                        'tipoSolicitudId' => $objTipoSolicitud->getId(),
                                                                        'estado'          => "FactibilidadEnProceso"));
                if(is_object($objDetalleSolicitud) )
                {
                    /***** INICIO - GENERAR FACTIBILIDAD *****/

                    //verifica si contiene conectores la interface nueva
                    $booleanConectorNueva   = false;

                    $intCajaConector        = null;
                    $intCasseteConector     = null;
                    $intInterfaceConector   = null;
                    $strTipoCambio          = "";
                    $strTipoFactibilidad    = "";
                    $objServicio            = $objDetalleSolicitud->getServicioId();
                    $booleanEjeWsNetworking = true;

                    $objServicioTecnico     = $this->emcom->getRepository("schemaBundle:InfoServicioTecnico")
                                                                        ->findOneByServicioId($objServicio->getId());
                    if( !is_object($objServicioTecnico) )
                    {
                        throw new \Exception("No existe información técnica de servicios.");
                    }

                    $objTipoMedio           = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                        ->find($objServicioTecnico->getUltimaMillaId());
                    if( !is_object($objTipoMedio) )
                    {
                        throw new \Exception("No existe ultima milla en la información técnica de un servicio procesado.");
                    }

                    //obtengo el tipo de la ultima milla
                    $strUltimaMilla         = $objTipoMedio->getNombreTipoMedio();

                    $objAdmiInterfaceAnt    = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array(  "detalleSolicitudId" => $objDetalleSolicitud->getId(), 
                                                                        "caracteristicaId"   => $objCaracInterfaceAnt->getId()));
                    $objAdmiInterfaceNueva  = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array(  "detalleSolicitudId" => $objDetalleSolicitud->getId(), 
                                                                        "caracteristicaId"   => $objCaracInterfaceNueva->getId()));
                    if( !is_object($objAdmiInterfaceAnt) )
                    {
                        throw new \Exception("No existe información de la interface anterior del ".
                                             "cambio de ultima milla (ProcesoMasivo).");
                    }

                    if( !is_object($objAdmiInterfaceNueva) )
                    {
                        $objAdmiElementoNuevo   = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array(  "detalleSolicitudId" => $objDetalleSolicitud->getId(), 
                                                                        "caracteristicaId"   => $objCaracElementoNuevo->getId()));
                        $objAdmiInterfaceNombre = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array(  "detalleSolicitudId" => $objDetalleSolicitud->getId(), 
                                                                        "caracteristicaId"   => $objCaracInterfaceNuevaNombre->getId()));
                        $objInfoElementoNuevo   = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                                        ->find($objAdmiElementoNuevo->getValor());
                        $objInterfaceNueva      = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                    ->findOneBy(array("elementoId"              => $objInfoElementoNuevo->getId(),
                                                                      "nombreInterfaceElemento" => $objAdmiInterfaceNombre->getValor()));
                        if( !is_object($objInterfaceNueva) )
                        {
                            //si no existe la interface nueva procedo con la creación
                            $objInterfaceNueva      = new InfoInterfaceElemento();
                            $objInterfaceNueva->setNombreInterfaceElemento($objAdmiInterfaceNombre->getValor());
                            $objInterfaceNueva->setDescripcionInterfaceElemento($objAdmiInterfaceNombre->getValor());
                            $objInterfaceNueva->setElementoId($objInfoElementoNuevo);
                            $objInterfaceNueva->setEstado('connected');
                            $objInterfaceNueva->setUsrCreacion($strUsrCreacion);
                            $objInterfaceNueva->setFeCreacion(new \DateTime('now'));
                            $objInterfaceNueva->setIpCreacion($strIpCreacion);
                            $this->emInfraestructura->persist($objInterfaceNueva);
                            $this->emInfraestructura->flush();

                            //historial elemento
                            $objHistorialElemento   = new InfoHistorialElemento();
                            $objHistorialElemento->setElementoId($objInfoElementoNuevo);
                            $objHistorialElemento->setEstadoElemento("Activo");
                            $objHistorialElemento->setObservacion("Se ingreso una nueva interface(".$objAdmiInterfaceNombre->getValor().")");
                            $objHistorialElemento->setUsrCreacion($strUsrCreacion);
                            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                            $objHistorialElemento->setIpCreacion($strIpCreacion);
                            $this->emInfraestructura->persist($objHistorialElemento);
                            $this->emInfraestructura->flush();
                        }
                        else if( $strUltimaMilla != "Radio" )
                        {
                            //boolaen para controlar el loop
                            $booleanCasseteNoEncontrado = true;
                            //cantidad maxima de repeticiones del loop
                            $intCantidadLoop            = 50;
                            $intInterfaceElementoIni    = $objInterfaceNueva->getId();
                            //loop para encontrar el CASSETE de la interface del SWICTH
                            while( $booleanCasseteNoEncontrado )
                            {
                                $objInfoEnlace      = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                            ->findOneBy(array('interfaceElementoIniId'  => $intInterfaceElementoIni,
                                                                              'estado'                  => 'Activo'));
                                if( is_object($objInfoEnlace) )
                                {
                                    $objInterfaceFin                = $objInfoEnlace->getInterfaceElementoFinId();
                                    $strNombreTipoElemento          = $objInterfaceFin->getElementoId()->getModeloElementoId()
                                                                                    ->getTipoElementoId()->getNombreTipoElemento();
                                    if( $strNombreTipoElemento == 'CASSETTE' && 
                                        strpos(strtoupper($objInterfaceFin->getNombreInterfaceElemento()),'OUT') !== false )
                                    {
                                        $booleanCasseteNoEncontrado = false;
                                        $intInterfaceConector       = $objInterfaceFin->getId();
                                        $intCasseteConector         = $objInterfaceFin->getElementoId()->getId();
                                        $objRelacionElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                                                ->findOneBy(array(  "elementoIdB"   => $intCasseteConector,
                                                                                                    "estado"        => "Activo"));
                                        $intCajaConector            = $objRelacionElemento->getElementoIdA();
                                        $booleanConectorNueva       = true;
                                    }
                                    else
                                    {
                                        $intInterfaceElementoIni    = $objInterfaceFin->getId();
                                    }
                                }
                                //discrimentando la cantidad repeticiones del loop
                                $intCantidadLoop--;
                                //verificar si intCantidadLoop llega a cero se termina el loop
                                if( $intCantidadLoop == 0 )
                                {
                                    $booleanCasseteNoEncontrado = false;
                                }
                            }
                        }
                    }
                    else
                    {
                        $objInterfaceNueva  = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                        ->find($objAdmiInterfaceNueva->getValor());
                        if( !is_object($objInterfaceNueva) )
                        {
                            throw new \Exception("No existe información de la interface nueva del ".
                                                 "cambio de ultima milla (ProcesoMasivo).");
                        }

                        //procedo con la actualización del estado de la interface
                        $objInterfaceNueva->setEstado('connected');
                        $objInterfaceNueva->setUsrUltMod($strUsrCreacion);
                        $objInterfaceNueva->setFeUltMod(new \DateTime('now'));
                        $this->emInfraestructura->persist($objInterfaceNueva);
                        $this->emInfraestructura->flush();
                        
                        //verifico si la ultima milla es diferente de radio para buscar el cassete
                        if( $strUltimaMilla != "Radio" )
                        {
                            //boolaen para controlar el loop
                            $booleanCasseteNoEncontrado = true;
                            //cantidad maxima de repeticiones del loop
                            $intCantidadLoop            = 50;
                            $intInterfaceElementoIni    = $objInterfaceNueva->getId();
                            //loop para encontrar el CASSETE de la interface del SWICTH
                            while( $booleanCasseteNoEncontrado )
                            {
                                $objInfoEnlace          = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                                       ->findOneBy(array('interfaceElementoIniId'   => $intInterfaceElementoIni,
                                                                                          'estado'                  => 'Activo'));
                                if( is_object($objInfoEnlace) )
                                {
                                    $objInterfaceFin                = $objInfoEnlace->getInterfaceElementoFinId();
                                    $strNombreTipoElemento          = $objInterfaceFin->getElementoId()->getModeloElementoId()
                                                                                    ->getTipoElementoId()->getNombreTipoElemento();
                                    if( $strNombreTipoElemento == 'CASSETTE' && 
                                        strpos(strtoupper($objInterfaceFin->getNombreInterfaceElemento()),'OUT') !== false )
                                    {
                                        $booleanCasseteNoEncontrado = false;
                                        $intInterfaceConector       = $objInterfaceFin->getId();
                                        $intCasseteConector         = $objInterfaceFin->getElementoId()->getId();
                                        $objRelacionElemento        = $this->emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                                                    ->findOneBy(array(  "elementoIdB"   => $intCasseteConector,
                                                                                                        "estado"        => "Activo"));
                                        $intCajaConector            = $objRelacionElemento->getElementoIdA();
                                        $booleanConectorNueva       = true;
                                    }
                                    else
                                    {
                                        $intInterfaceElementoIni    = $objInterfaceFin->getId();
                                    }
                                }
                                //discrimentando la cantidad repeticiones del loop
                                $intCantidadLoop--;
                                //verificar si intCantidadLoop llega a cero se termina el loop
                                if( $intCantidadLoop == 0 )
                                {
                                    $booleanCasseteNoEncontrado = false;
                                }
                            }
                        }
                    }

                    $objInterfaceAnterior   = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                        ->find($objAdmiInterfaceAnt->getValor());
                    if( !is_object($objInterfaceAnterior) )
                    {
                        throw new \Exception("No existe información de la interface anterior del ".
                                             "cambio de ultima milla (ProcesoMasivo).");
                    }

                    //si la ultima milla es Fibra Optica por default es RUTA
                    if( $strUltimaMilla == "Fibra Optica" )
                    {
                       $strTipoFactibilidad     = "RUTA"; 
                    }
                    
                    //verifico si la ultima milla es diferente de radio para generar la factibilidad
                    if( $strUltimaMilla != "Radio" )
                    {
                        $objServProdCaractTipoFact  = $this->infoServicioTecnicoService
                                ->getServicioProductoCaracteristica( $objServicio,
                                                                     'TIPO_FACTIBILIDAD',
                                                                     $objServicio->getProductoId());
                        //Si no existe la caracteristica mencionada se setea por default a Fibra Ruta
                        if( is_object($objServProdCaractTipoFact) )
                        {
                            $strTipoFactibilidad    = $objServProdCaractTipoFact->getValor();
                        }

                        //se verifica si el cambio es en el mismo SWITCH
                        if( $objInterfaceAnterior->getElementoId()->getId() == $objInterfaceNueva->getElementoId()->getId())
                        {
                            $strTipoCambio          = "MISMO_SWITCH";
                            $booleanEjeWsNetworking = false;
                        }
                        else
                        {
                            $strTipoCambio          = "MISMO_PE_MISMO_ANILLO";
                            $booleanEjeWsNetworking = true;
                        }

                        if( $booleanConectorNueva && $strUltimaMilla == "Fibra Optica" && $strTipoFactibilidad == "RUTA" )
                        {
                            $arrayParametrosRequest = array(
                                                            'intIdSolicitud'        => $objDetalleSolicitud->getId(),
                                                            'intIdSwitchNew'        => $objInterfaceNueva->getElementoId()->getId(),
                                                            'intIdInterfaceNew'     => $objInterfaceNueva->getId(),
                                                            'intIdCajaNew'          => $intCajaConector,
                                                            'intIdCassetteNew'      => $intCasseteConector,
                                                            'intIdInterfaceOutNew'  => $intInterfaceConector,
                                                            'strTipoCambio'         => $strTipoCambio,
                                                            'strEmpresaCod'         => $objEmpresa->getId(),
                                                            'strUsrCreacion'        => $strUsrCreacion,
                                                            'strIpCreacion'         => $strIpCreacion
                                                        );
                            $objRespuestaProceso    = $serviceFactibilidadCambioUm->generarFactibilidadUM($arrayParametrosRequest);
                        }
                        else
                        {
                            //Para UTP y FIBRA DIRECTA
                            $arrayParametrosRequest = array(
                                                            'intIdSolicitud'        => $objDetalleSolicitud->getId(),
                                                            'intIdSwitchNew'        => $objInterfaceNueva->getElementoId()->getId(),
                                                            'intIdInterfaceNew'     => $objInterfaceNueva->getId(),
                                                            'strTipoCambio'         => $strTipoCambio,
                                                            'strUsrCreacion'        => $strUsrCreacion,
                                                            'strIpCreacion'         => $strIpCreacion
                                                        );
                            $objRespuestaProceso    = $serviceFactibilidadCambioUm->generarFactibilidadUtpFODirecto($arrayParametrosRequest);
                        }
                        //se valida que la factibilidad si no se proceso correctamente retorna el error
                        if( $objRespuestaProceso->getContent() != "Factibilidad de Cambio de ultima milla generada Correctamente." )
                        {
                            throw new \Exception("Existieron errores al procesar la factibilidad (ProcesoMasivo).");
                        }
                    }

                    //procedo con el cambio de estado de la interface anterior
                    $objInterfaceAnterior->setEstado('not connect');
                    $objInterfaceAnterior->setUsrUltMod($strUsrCreacion);
                    $objInterfaceAnterior->setFeUltMod(new \DateTime('now'));
                    $this->emInfraestructura->persist($objInterfaceAnterior);
                    $this->emInfraestructura->flush();

                    /***** FIN - GENERAR FACTIBILIDAD *****/


                    /***** INICIO - EJECUTAR CAMBIO ULTIMA MILLA *****/

                    //verifico si la ultima milla es diferente de radio para ejecutar el cambio de ultima milla
                    if( $strUltimaMilla != "Radio" )
                    {
                        //obtengo la datos técnico del servicio
                        $arrayPeticionesDataTec = array('idServicio'    => $objInfoServicio->getId(),
                                                        'idEmpresa'     => $objEmpresa->getId(),
                                                        'prefijoEmpresa'=> $objEmpresa->getPrefijo());
                        $arrayRespuestaPeticion = $serviceDataTecnica->getDataTecnica($arrayPeticionesDataTec);

                        $strCapacidad1          = $arrayRespuestaPeticion['capacidad1'];
                        $strCapacidad2          = $arrayRespuestaPeticion['capacidad2'];
                        $strMacCpe              = $arrayRespuestaPeticion['macCpe'];
                        $strVlan                = $arrayRespuestaPeticion['vlan'];

                        $arrayPeticiones        = array(
                                                    'idEmpresa'                     => $objEmpresa->getId(),
                                                    'prefijoEmpresa'                => $objEmpresa->getPrefijo(),
                                                    'idServicio'                    => $objInfoServicio->getId(),
                                                    'mac'                           => $strMacCpe,
                                                    'macRadio'                      => null,
                                                    'vlan'                          => $strVlan,
                                                    'capacidad1'                    => $strCapacidad1,
                                                    'capacidad2'                    => $strCapacidad2,
                                                    'usrCreacion'                   => $strUsrCreacion,
                                                    'ipCreacion'                    => $strIpCreacion,
                                                    'emComercial'                   => $this->emcom,
                                                    'emInfraestructura'             => $this->emInfraestructura,
                                                    'booleanEjeWsNetworking'        => $booleanEjeWsNetworking,
                                                    'booleanSetearConexion'         => true,
                                                    'booleanProcesoMasivo'          => true                                                );
                        /*
                         * Se llama el metodo "cambiarUltimaMillaTn" para realizar el cambio de ultima milla
                         * Se agregen los datos $arrayPeticiones y se envia con la llave 'booleanEstadoBeginTransaction'
                         * con valor de FALSE para deshabilitar la transacionalidad de las conexiones a la BD del metodo
                         */
                        $strResultCambiarUM     = $serviceCambioPuerto->cambiarUltimaMillaTn($arrayPeticiones);
                        if( $strResultCambiarUM == "OK" )
                        {
                            $arrayResultadoServicio['status']   = 200;
                            $arrayResultadoServicio['mensaje']  = "Se realizó correctamente el cambio de ultima milla (ProcesoMasivo).";
                            $arrayResultadoServicio['servicio'] = intval($objInfoServicio->getId());

                            $arrayRespuesta[]   = $arrayResultadoServicio;
                        }
                        else
                        {
                            throw new \Exception($strResultCambiarUM);
                        }
                    }
                    else
                    {
                        //se agrega al arreglo de respuesta el resultado del servicio
                        $arrayRespuesta[] = array(
                            'status'    => 200,
                            'mensaje'   => "Continuar con el proceso manual del cambio de última milla (ProcesoMasivo).",
                            'servicio'  => intval($objInfoServicio->getId())
                        );
                    }

                    /***** FIN - EJECUTAR CAMBIO ULTIMA MILLA *****/

                    //verifico si la ultima milla sea radio
                    if( $strUltimaMilla == "Radio" )
                    {
                        //agrego los id de la interface anterior y nueva
                        $arrayIdInteServ[] = array(
                            'intInterfaceAnterior' => $objInterfaceAnterior->getId(),
                            'intInterfaceNueva'    => $objInterfaceNueva->getId()
                        );
                        //procedo con la actualización del estado de la interface
                        $objInterfaceNueva->setEstado('not connect');
                        $objInterfaceNueva->setUsrUltMod($strUsrCreacion);
                        $objInterfaceNueva->setFeUltMod(new \DateTime('now'));
                        $this->emInfraestructura->persist($objInterfaceNueva);
                        $this->emInfraestructura->flush();
                    }

                    //procedo actualizar el detalle de la cabecera de la solicitud
                    $objInfoProcesoMasivoDet    = $this->emInfraestructura->getRepository("schemaBundle:InfoProcesoMasivoDet")
                                                                        ->findOneBySolicitudId($objDetalleSolicitud->getId());
                    if(is_object($objInfoProcesoMasivoDet) )
                    {
                        $objInfoProcesoMasivoDet->setEstado('Finalizado');
                        $objInfoProcesoMasivoDet->setUsrUltMod($strUsrCreacion);
                        $objInfoProcesoMasivoDet->setFeUltMod(new \DateTime('now'));
                        $objInfoProcesoMasivoDet->setObservacion('Se realizó el cambio de ultima milla del detalle de solicitud');
                        $this->emInfraestructura->persist($objInfoProcesoMasivoDet);
                        $this->emInfraestructura->flush();
                    }

                    $this->emcom->flush();
                    $this->emInfraestructura->flush();

                    //procedo a guardar todos los cambios realizados
                    if( $this->emcom->getConnection()->isTransactionActive() )
                    {
                        $this->emcom->getConnection()->commit();
                        $this->emcom->getConnection()->close();
                    }
                    if( $this->emInfraestructura->getConnection()->isTransactionActive() )
                    {
                        $this->emInfraestructura->getConnection()->commit();
                        $this->emInfraestructura->getConnection()->close();
                    }
                }
                else
                {
                    //se agrega al arreglo de respuesta el resultado del servicio
                    $arrayRespuesta[] = array(
                        'status'    => 200,
                        'mensaje'   => "No hay solicitudes para cambio ultima milla PM en estado 'FactibilidadEnProceso'.",
                        'servicio'  => intval($objInfoServicio->getId())
                    );
                }
            }
            catch (\Exception $e)
            {
                //seteo el mensaje de error del servicio
                $strMensajeError  = $e->getMessage();

                //se agrega al arreglo de respuesta el resultado del servicio
                $arrayRespuesta[] = array(
                    'status'    => 500,
                    'mensaje'   => $strMensajeError,
                    'servicio'  => intval($intIdServicioError)
                );

                //se hace el roll back de las transacciones
                if( $this->emcom->getConnection()->isTransactionActive() )
                {
                    $this->emcom->getConnection()->rollback();
                    $this->emcom->getConnection()->close();
                }
                if( $this->emInfraestructura->getConnection()->isTransactionActive() )
                {
                    $this->emInfraestructura->getConnection()->rollback();
                    $this->emInfraestructura->getConnection()->close();
                }

                //se crea el historial del servicio con el error
                $objInfoServicio   = $this->emcom->getRepository('schemaBundle:InfoServicio')->find($intIdServicioError);
                if( is_object($objInfoServicio) )
                {
                    //agrego el error al historial del servicio
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objInfoServicio);
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setEstado($objInfoServicio->getEstado());
                    $objServicioHistorial->setObservacion('ERROR: '.$strMensajeError);
                    $this->emcom->persist($objServicioHistorial);
                    $this->emcom->flush();
                }

                try
                {
                    //enviar notificación del error por correo electrónico
                    $objMailer      = $this->container->get('schema.Mailer');
                    $strTwigMail    = 'tecnicoBundle:InfoServicioCambioPlan:mailerErrorCambioUltimaMillaMasivo.html.twig';
                    $strAsuntoMail  = "Notificación Error: Cambio Ultima Milla Masivo";
                    $strFromMail    = "notificaciones_telcos@telconet.ec";
                    $arrayToMail    = array();
                    $arrayDatosMail = array(
                        'strLogin'     => $strLoginError,
                        'strLoginAux'  => $strLoginAuxError,
                        'strMensaje'   => $strMensajeError,
                    );

                    //obtengo los correos para el envío de la notificación
                    $objAdmiParametroCab    = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                            array(  'nombreParametro'   => 'CORREOS_RESPUESTA_CAMBIO_UM_MASIVO',
                                                                    'estado'            => 'Activo'));
                    if( !is_object($objAdmiParametroCab) )
                    {
                        throw new \Exception('No se encontró los correos para las notificaciones '.
                                             'de cambio de última milla masivo en el telcos.');  
                    }
                    $arrayParametrosCorreos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                            array(  "parametroId"   => $objAdmiParametroCab->getId(),
                                                                    "empresaCod"    => $objEmpresa->getId(),
                                                                    "estado"        => "Activo"));
                    foreach($arrayParametrosCorreos as $objParametroCorreo)
                    {
                        $arrayToMail[] = $objParametroCorreo->getValor1();
                    }

                    $objMailer->sendTwig($strAsuntoMail,
                                         $strFromMail,
                                         $arrayToMail,
                                         $strTwigMail,
                                         $arrayDatosMail);
                }
                catch(\Exception $ex)
                {
                    $this->utilService->insertError('Telcos+',
                                                    'InfoElementoService.realizarCambioUMProcesoMasivo',
                                                    $ex->getMessage(),
                                                    $strUsrCreacion,
                                                    $strIpCreacion);
                }

                $this->utilService->insertError('Telcos+',
                                                'InfoElementoService.realizarCambioUMProcesoMasivo',
                                                "LoginAux: $strLoginAuxError, ".$strMensajeError,
                                                $strUsrCreacion,
                                                $strIpCreacion);
            }
        }

        try
        {
            //verifico si el arreglo contiene interfaces
            if( count($arrayIdInteServ) > 0 )
            {
                $this->emInfraestructura->getConnection()->beginTransaction();

                //estados permitidos para cambio um que deben tener los servicios de las interfaces.
                $arrayEstadoServicio     = array();
                //obtengo los parametros de los servicios permitidos
                $objAdmiParametroCabServ = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                        ->findOneBy(array(  'nombreParametro'   => 'ESTADOS_SERVICIOS_PERMITIDOS',
                                                                            'estado'            => 'Activo'));
                if( is_object($objAdmiParametroCabServ) )
                {
                    $arrayAdmiParametroDet  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->findBy(array("parametroId"    => $objAdmiParametroCabServ->getId(),
                                                                            "estado"        => "Activo"));
                    foreach( $arrayAdmiParametroDet as $objAdmiParamDet )
                    {
                        array_push($arrayEstadoServicio, $objAdmiParamDet->getValor1());
                    }
                }

                /*
                 * Se recorre todos los id de las interfaces para verificar si tienen servicios con ultima milla tipo radio
                 * para cambiar el estado de la nueva interface a 'not connect'
                 */
                foreach( $arrayIdInteServ as $arrayIdInterface )
                {
                    //obtengo los id de las interfaces anterior y nueva
                    $intIdInterfaceAnterior = $arrayIdInterface['intInterfaceAnterior'];
                    $intIdInterfaceNueva    = $arrayIdInterface['intInterfaceNueva'];
                    //obtengo el objeto de la interface anterior
                    $objInterfaceAnterior   = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                    ->find($intIdInterfaceAnterior);
                    //obtengo el objeto de la interface nueva
                    $objInterfaceNueva      = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                    ->find($intIdInterfaceNueva);
                    if( is_object($objInterfaceAnterior) && is_object($objInterfaceNueva) )
                    {
                        //obtengo los servicios tecnicos de la interface
                        $arrayServTecInterAnt   = $this->emcom->getRepository('schemaBundle:InfoServicioTecnico')
                                                        ->findByInterfaceElementoId($objInterfaceAnterior->getId());
                        foreach( $arrayServTecInterAnt as $objServTecInterface )
                        {
                            //obtengo el tipo de medio
                            $objTipoMedio       = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                                ->find($objServTecInterface->getUltimaMillaId());
                            //obtengo el tipo de medio de la ultima milla
                            $strUltimaMilla     = $objTipoMedio->getNombreTipoMedio();
                            //obtengo el objeto del servicio
                            $objServInterface   = $objServTecInterface->getServicioId();
                            if( is_object($objServInterface) && $strUltimaMilla == "Radio" )
                            {
                                //verifico que el servicio se encuentre en estado permitido
                                $strEstadoServicio = $objServInterface->getEstado();
                                if( in_array($strEstadoServicio, $arrayEstadoServicio) )
                                {
                                    //procedo a cambiar el estado de la nueva interface a 'not connect'
                                    $objInterfaceNueva->setEstado('not connect');
                                    $objInterfaceNueva->setUsrUltMod($strUsrCreacion);
                                    $objInterfaceNueva->setFeUltMod(new \DateTime('now'));
                                    $this->emInfraestructura->persist($objInterfaceNueva);
                                    $this->emInfraestructura->flush();
                                }
                            }
                        }
                    }
                }

                $this->emInfraestructura->flush();
                if( $this->emInfraestructura->getConnection()->isTransactionActive() )
                {
                    $this->emInfraestructura->getConnection()->commit();
                    $this->emInfraestructura->getConnection()->close();
                }
            }
        }
        catch (\Exception $e)
        {
            if( $this->emInfraestructura->getConnection()->isTransactionActive() )
            {
                $this->emInfraestructura->getConnection()->rollback();
                $this->emInfraestructura->getConnection()->close();
            }

            $this->utilService->insertError('Telcos+',
                                            'InfoElementoService.realizarCambioUMProcesoMasivo',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }

        $arrayResultado['servicios'] = $arrayRespuesta;
        return $arrayResultado;
        
    }

    /**
     * Funcion que sirve para obtener equipos de cliente en nodo, pendiente para retirar
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 16-06-2021
     * 
     * @param array $arrayParametros
     * @return array $arrayRespuesta
     */
    public function obtenerDispositivosRetiroClienteNodo($arrayParametros)
    {
        $intDetSolicitudId = $arrayParametros['detSolicitudId'];
        $arrayRespuesta = array();

        try
        {          

            $objDetalleSolicitud    = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')
            ->find($intDetSolicitudId);

            $arrayCaracteristicasSolicitud = $this->emcom
                                                    ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findBy(array( "detalleSolicitudId" => $objDetalleSolicitud->getId(),
                                                                    "estado"             => array('Asignada','AsignadoTarea')
                                                                )
                                                            );

            foreach($arrayCaracteristicasSolicitud as $objCaracteristicaSolicitud)
            {

                //Filtrar equipos del cliente que se encuentren en el nodo
                $objInfoDetalleElemento = $this->emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                        ->findOneBy(array('detalleNombre'   =>  'UBICACION',
                                                            'detalleValor'  =>  'Nodo',
                                                            'elementoId'    =>  $objCaracteristicaSolicitud->getValor()
                                                        ));

                if(is_object($objInfoDetalleElemento))
                {
                    $objElementoCliente     = $this->emInfraestructura
                                                    ->getRepository('schemaBundle:InfoElemento')
                                                    ->find($objCaracteristicaSolicitud->getValor());

                    if (is_object($objElementoCliente))
                    {
                        $arrayRespuesta[] = $objElementoCliente;
                    }  
                }
            }
        } 
        catch (\Exception $exception) 
        {
            $this->serviceUtil->insertLog(array(
                'enterpriseCode'   => '10',
                'logType'          => 1,
                'logOrigin'        => 'TELCOS',
                'application'      => 'TELCOS',
                'appClass'         => 'InfoElementoService',
                'appMethod'        => 'obtenerDispositivosRetiroClienteNodo',
                'descriptionError' => $exception->getMessage(),
                'status'           => 'Fallido',
                'inParameters'     => json_encode($arrayParametros),
                'creationUser'     => 'TELCOS'));

            return array();
        }

        return $arrayRespuesta;
    }
    
    /**
     * Funcion que sirve para generar la solicitud de agregar equipo
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 20-03-2020
     *
     * @param $strJsonSolicitud Json contiene los registros seleccionados para la generación de la solicitudes.
     * 
     * @param $strNumeroRegis Total de registros seleccionados para la generación de solicitudes. 
     */
    public function generarSolAgregarEquipo($strJsonSolicitud,$arrayParametrosVariables)
    {             
        $strMsjError        = str_repeat('a',  30*1024);
        $strRespuesta       = str_repeat('a',  30*1024);
        
        $strNumeroRegis     = $arrayParametrosVariables['strNumeroRegis'];
        $strUsrCreacion     = $arrayParametrosVariables['strUsrCreacion'];
        
        try
        {
            $strSql = "BEGIN INFRK_TRANSACCIONES.P_GEN_SOLICITUD_EQUIPO(
                                                :Pv_JsonSolicitud,
                                                :Pv_NumeroRegistro,
                                                :Pv_UsuarioCreacion,
                                                :Pv_MensajeError,
                                                :Pv_Respuesta); 
                                            END;";
            
            $objConn = oci_connect($this->strUrsrInfraest,
                                $this->strPassInfraest,
                                $this->strDnsInfraest);
            $objStmt = oci_parse($objConn, $strSql);
            $strJsonClob = oci_new_descriptor($objConn);
            $strJsonClob->writetemporary($strJsonSolicitud);
            
            oci_bind_by_name($objStmt, ':Pv_JsonSolicitud', $strJsonClob, -1, OCI_B_CLOB);
            oci_bind_by_name($objStmt, ':Pv_NumeroRegistro', $strNumeroRegis);
            oci_bind_by_name($objStmt, ':Pv_UsuarioCreacion', $strUsrCreacion);
            oci_bind_by_name($objStmt, ':Pv_MensajeError', $strMsjError);
            oci_bind_by_name($objStmt, ':Pv_Respuesta', $strRespuesta);
            
            oci_execute($objStmt);
            $strErrorOci = oci_error($objStmt);
           
            if (strpos($strMsjError, 'ERROR') === false && $strErrorOci==null)
            {
                $arrayRespuesta = array ('strRespuesta'     =>'Procesando..',
                                         'strStatus'        =>'200',
                                         'strMensaje'       =>'PROCESO EXITOSO');
            }
            else
            {
                if (strpos($strMsjError, str_repeat('a',4)) === false)
                {
                    $arrayRespuesta = array ('strRespuesta'     =>$strMsjError,
                                             'strStatus'        =>'500',
                                             'strMensaje'       =>'PROCESO FALLIDO');
                }
                else
                {
                    $arrayRespuesta = array ('strRespuesta'     =>$strErrorOci['message'],
                                             'strStatus'        =>'500',
                                             'strMensaje'       =>'PROCESO FALLIDO');
                }                
            }
        }
        catch(\Exception $e)
        {
            $strRespuesta   = " Error al procesar la generación de solicitud. Favor Notificar a Sistemas";
            $arrayRespuesta = array ('strMensaje'     =>$strRespuesta);
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoElementoService.generarSolAgregarEquipo',
                                            'Error InfoElementoService.generarSolAgregarEquipo:'.$e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
            return $arrayRespuesta;
        }
        return $arrayRespuesta;
    }

    /**
     * Funcion que sirve para activar el olt multiplataforma
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 30-03-2021
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 06-08-2021 - Se ingresa las vlans de cada servicio adicional
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 11-03-2022 - Se envia el usuario de la acción
     * 
     * @author Jenniffer Mujica <jmujica@telconet.ec>
     * @version 1.3 06-03-2023 - Se valida si es el olt ya es multiplataforma y se retorna la respuesta OK
     *
     * @param Array $arrayData [
     *                             nombre_olt,    Nombre del olt
     *                             usrCreacion,   Usuario creación
     *                             ipCreacion,    Ip creación
     *                         ]
     * @return Array $arrayResponse [ status , mensaje ]
     */
    public function activarElementoOltMultiplataformaTN($arrayData)
    {
        $serviceNetworking = $this->container->get('tecnico.NetworkingScripts');
        $strNombreOlt      = $arrayData['nombre_olt'];
        $strUsrCreacion    = $arrayData['usrCreacion'];
        $strIpCreacion     = $arrayData['ipCreacion'];

        try
        {
            //verificar si es multiplataforma
            $strMultiGpon   = "MULTIPLATAFORMA";
            $strEleActivoMulti = "ELEMENTO_ACTIVO_POR_MULTIPLATAFORMA";
            $arrayParametrosDetMulti = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                    'COMERCIAL',
                                                                                                    '',
                                                                                                    'NOMBRES PARAMETROS DETALLES MULTIPLATAFORMA',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '');
            if(isset($arrayParametrosDetMulti) && !empty($arrayParametrosDetMulti))
            {
                $strMultiGpon = isset($arrayParametrosDetMulti['valor1']) && !empty($arrayParametrosDetMulti['valor1']) ?
                                $arrayParametrosDetMulti['valor1'] : $strMultiGpon;
                $strEleActivoMulti = isset($arrayParametrosDetMulti['valor5']) && !empty($arrayParametrosDetMulti['valor5']) ?
                                $arrayParametrosDetMulti['valor5'] : $strEleActivoMulti;
            }
            //consultar olt
            $objOltElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                        ->createQueryBuilder('p')
                                                        ->innerJoin('schemaBundle:InfoElemento', 'e', 'WITH', 'p.elementoId = e.id')
                                                        ->where('e.nombreElemento = :nombreElemento')
                                                        ->andWhere("p.detalleNombre = :detalleNombre")
                                                        ->andWhere("p.detalleValor = :detalleValor")
                                                        ->andWhere("e.estado != :estadoEliminado")
                                                        ->andWhere("p.estado = :estadoActivo")
                                                        ->setParameter('nombreElemento', $strNombreOlt)
                                                        ->setParameter('detalleNombre', $strMultiGpon)
                                                        ->setParameter('detalleValor', 'SI')
                                                        ->setParameter('estadoEliminado', 'Eliminado')
                                                        ->setParameter('estadoActivo', 'Activo')
                                                        ->setMaxResults(1)
                                                        ->getQuery()
                                                        ->getOneOrNullResult();
            if(is_object($objOltElemento))
            {
                return array(
                    'status'   => 'OK',
                    'mensaje'  => "El olt ($strNombreOlt) ya es multiplataforma."
                );
            }

            $this->emInfraestructura->getConnection()->beginTransaction();
            $this->emcom->getConnection()->beginTransaction();

            //seteo el arrreglo de los datos
            $arrayDatosOlt   = array();
            //obtengo los datos del olt multiplataforma
            $arrayParametros = array(
                'strNombreOlt'       => $strNombreOlt,
                'strEstadoSolicitud' => 'Configurado',
                'booleanLikeNombre'  => false
            );
            $arrayResultado = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->getOltsMultiplatafroma($arrayParametros);
            if( $arrayResultado['status'] == 'OK' && is_array($arrayResultado['result']) && count($arrayResultado['result']) == 1 )
            {
                $arrayDatosOlt = $arrayResultado['result'][0];
            }
            else
            {
                throw new \Exception('No se encontró los datos del Olt Multiplataforma, por favor notificar a Sistemas.');
            }
            //obtengo la solicitud
            $objDetalleSolicitud = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')->find($arrayDatosOlt['idSolicitud']);
            if(!is_object($objDetalleSolicitud))
            {
                throw new \Exception("No se encontró la solicitud del Olt Multiplataforma, por favor notificar a Sistemas.");
            }
            //obtengo el elemento olt
            $objOltElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayDatosOlt['idElementoOlt']);
            if(!is_object($objOltElemento))
            {
                throw new \Exception("No se encontró el elemento Olt, por favor notificar a Sistemas.");
            }

            //seteo el arreglo de parametros para asignar los recursos de red en el olt multiplataforma
            $arrayParametrosNet = array(
                'url'        => 'getResources',
                'accion'     => 'Consultar',
                'pe'         => $arrayDatosOlt['nombrePe'],
                'id_olt'     => $objOltElemento->getId(),
                'olt'        => $objOltElemento->getNombreElemento(),
                'servicio'   => 'GENERAL',
                'login_aux'  => '',
                'user_name'  => $strUsrCreacion,
                'user_ip'    => $strIpCreacion
            );
            //se ejecuta script de networking
            $arrayRespuesta = $serviceNetworking->callNetworkingWebService($arrayParametrosNet);
            if($arrayRespuesta['status'] == 'ERROR')
            {
                throw new \Exception($arrayRespuesta['mensaje']);
            }

            //obtengo los datos de la subred
            $arrayDataSubred = $arrayRespuesta['data'];
            if(!isset($arrayDataSubred['vrf_telconet_pri']) || empty($arrayDataSubred['vrf_telconet_pri']))
            {
                throw new \Exception("No se pudo obtener la subred principal desde Networking");
            }
            if(!isset($arrayDataSubred['vrf_telconet_bk']) || empty($arrayDataSubred['vrf_telconet_bk']))
            {
                throw new \Exception("No se pudo obtener la subred backup desde Networking");
            }

            //seteo los parametros de las subredes y vlans
            $strIntPriGpon  = "INTPRIGPON";
            $strIntBkGpon   = "INTBKGPON";
            $strVlanPriGpon = "VLAN INTERNET GPON PRINCIPAL";
            $strVlanBkGpon  = "VLAN INTERNET GPON BACKUP";
            $arrayParametrosSubred = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                    'COMERCIAL',
                                                                                                    '',
                                                                                                    'NOMBRES PARAMETROS SUBREDES Y VLANS',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '');
            if(isset($arrayParametrosSubred) && !empty($arrayParametrosSubred))
            {
                $strIntPriGpon  = isset($arrayParametrosSubred['valor1']) && !empty($arrayParametrosSubred['valor1'])
                                 ? $arrayParametrosSubred['valor1'] : $strIntPriGpon;
                $strIntBkGpon   = isset($arrayParametrosSubred['valor2']) && !empty($arrayParametrosSubred['valor2'])
                                 ? $arrayParametrosSubred['valor2'] : $strIntBkGpon;
                $strVlanPriGpon = isset($arrayParametrosSubred['valor4']) && !empty($arrayParametrosSubred['valor4'])
                                 ? $arrayParametrosSubred['valor4'] : $strVlanPriGpon;
                $strVlanBkGpon  = isset($arrayParametrosSubred['valor5']) && !empty($arrayParametrosSubred['valor5'])
                                 ? $arrayParametrosSubred['valor5'] : $strVlanBkGpon;
            }
            //obtengo el valor de la vlan para servicios safecity
            $arrayParametroVlanSafe = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->get('NUEVA_RED_GPON_TN',
                                                                                            'COMERCIAL',
                                                                                            '',
                                                                                            'PARAMETRO VLAN PARA SERVICIOS ADICIONALES SAFECITY',
                                                                                            '',
                                                                                            '',
                                                                                            '',
                                                                                            '',
                                                                                            '');
            foreach($arrayParametroVlanSafe as $arrayParVlanSafe)
            {
                //se guarda el detalle de la vlan safecity
                $objDetalleElementoVlanPri = new InfoDetalleElemento();
                $objDetalleElementoVlanPri->setElementoId($objOltElemento->getId());
                $objDetalleElementoVlanPri->setDetalleNombre($arrayParVlanSafe['valor1']);
                $objDetalleElementoVlanPri->setDetalleValor($arrayParVlanSafe['valor2']);
                $objDetalleElementoVlanPri->setDetalleDescripcion($arrayParVlanSafe['valor1']);
                $objDetalleElementoVlanPri->setFeCreacion(new \DateTime('now'));
                $objDetalleElementoVlanPri->setUsrCreacion($strUsrCreacion);
                $objDetalleElementoVlanPri->setIpCreacion($strIpCreacion);
                $objDetalleElementoVlanPri->setEstado('Activo');
                $this->emInfraestructura->persist($objDetalleElementoVlanPri);
                $this->emInfraestructura->flush();
            }
            //se genera los datos de subred principal
            $intPosSubPri     = strpos($arrayDataSubred['vrf_telconet_pri']['subred_wan'], "/");
            $strIpSubredPri   = substr($arrayDataSubred['vrf_telconet_pri']['subred_wan'],0,$intPosSubPri);
            $intMaskSubredPri = substr($arrayDataSubred['vrf_telconet_pri']['subred_wan'],$intPosSubPri+1);
            $arrayParGenSubredPri = array(
                'strIdElemento' => $objOltElemento->getId(),
                'strIpSubred'   => $strIpSubredPri,
                'intMaskSubred' => $intMaskSubredPri,
                'strUso'        => $strIntPriGpon,
                'serviceUtil'   => $this->utilService,
                'usrCreacion'   => $strUsrCreacion,
                'ipCreacion'    => $strIpCreacion
            );
            $arrayRespuestaSubredPri = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                    ->generarRecursosSubredes($arrayParGenSubredPri);
            if($arrayRespuestaSubredPri['status'] == 'ERROR')
            {
                throw new \Exception("No se puede generar los datos de subred(".$arrayDataSubred['vrf_telconet_pri']['subred_wan'].
                                     ") para el elemento ".$objOltElemento->getNombreElemento());
            }

            //obtengo el arreglo subred principal
            $arraySubredPrincipal = $this->emInfraestructura->getRepository("schemaBundle:InfoSubred")
                                                    ->createQueryBuilder('s')
                                                    ->where("s.elementoId   = :elementoId")
                                                    ->andWhere("s.uso       = :uso")
                                                    ->andWhere("s.estado    = :estado")
                                                    ->andWhere("s.subredId IS NOT NULL")
                                                    ->setParameter('elementoId', $objOltElemento->getId())
                                                    ->setParameter('uso',        $strIntPriGpon)
                                                    ->setParameter('estado',     "Activo")
                                                    ->getQuery()
                                                    ->getResult();
            foreach($arraySubredPrincipal as $objSubredPrincipal)
            {
                //actualizo el tipo de la subred
                $objSubredPrincipal->setTipo("WAN");
                $objSubredPrincipal->setEstado("Ocupado");
                $this->emInfraestructura->persist($objSubredPrincipal);
                $this->emInfraestructura->flush();
            }
            //setear tipo wan
            $arraySubredPrincipalSub = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                            ->createQueryBuilder('s')
                                                            ->where("s.elementoId   = :elementoId")
                                                            ->andWhere("s.uso       = :uso")
                                                            ->andWhere("s.estado    = :estado")
                                                            ->andWhere("s.subredId IS NULL")
                                                            ->setParameter('elementoId', $objOltElemento->getId())
                                                            ->setParameter('uso',        $strIntPriGpon)
                                                            ->setParameter('estado',     "Activo")
                                                            ->getQuery()
                                                            ->getResult();
            foreach($arraySubredPrincipalSub as $objSubredPrincipal)
            {
                //actualizo el tipo de la subred
                $objSubredPrincipal->setTipo("WAN");
                $this->emInfraestructura->persist($objSubredPrincipal);
                $this->emInfraestructura->flush();
            }

            //se genera los datos de subred backup
            $intPosSubBk     = strpos($arrayDataSubred['vrf_telconet_bk']['subred_wan'], "/");
            $strIpSubredBk   = substr($arrayDataSubred['vrf_telconet_bk']['subred_wan'],0,$intPosSubBk);
            $intMaskSubredBk = substr($arrayDataSubred['vrf_telconet_bk']['subred_wan'],$intPosSubBk+1);
            $arrayParGenSubredBk = array(
                'strIdElemento' => $objOltElemento->getId(),
                'strIpSubred'   => $strIpSubredBk,
                'intMaskSubred' => $intMaskSubredBk,
                'strUso'        => $strIntBkGpon,
                'serviceUtil'   => $this->utilService,
                'usrCreacion'   => $strUsrCreacion,
                'ipCreacion'    => $strIpCreacion
            );
            $arrayRespuestaSubredBk = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                    ->generarRecursosSubredes($arrayParGenSubredBk);
            if($arrayRespuestaSubredBk['status'] == 'ERROR')
            {
                throw new \Exception("No se puede generar los datos de subred(".$arrayDataSubred['vrf_telconet_bk']['subred_wan'].
                                     ") para el elemento ".$objOltElemento->getNombreElemento());
            }

            //obtengo el arreglo subred backup
            $arraySubredBackup = $this->emInfraestructura->getRepository("schemaBundle:InfoSubred")
                                                    ->createQueryBuilder('s')
                                                    ->where("s.elementoId   = :elementoId")
                                                    ->andWhere("s.uso       = :uso")
                                                    ->andWhere("s.estado    = :estado")
                                                    ->andWhere("s.subredId IS NOT NULL")
                                                    ->setParameter('elementoId', $objOltElemento->getId())
                                                    ->setParameter('uso',        $strIntBkGpon)
                                                    ->setParameter('estado',     "Activo")
                                                    ->getQuery()
                                                    ->getResult();
            foreach($arraySubredBackup as $objSubredBackup)
            {
                //actualizo el tipo de la subred
                $objSubredBackup->setTipo("WAN");
                $objSubredBackup->setEstado("Ocupado");
                $this->emInfraestructura->persist($objSubredBackup);
                $this->emInfraestructura->flush();
            }
            //setear tipo wan
            $arraySubredBackupSub = $this->emInfraestructura->getRepository("schemaBundle:InfoSubred")
                                                    ->createQueryBuilder('s')
                                                    ->where("s.elementoId   = :elementoId")
                                                    ->andWhere("s.uso       = :uso")
                                                    ->andWhere("s.estado    = :estado")
                                                    ->andWhere("s.subredId IS NULL")
                                                    ->setParameter('elementoId', $objOltElemento->getId())
                                                    ->setParameter('uso',        $strIntBkGpon)
                                                    ->setParameter('estado',     "Activo")
                                                    ->getQuery()
                                                    ->getResult();
            foreach($arraySubredBackupSub as $objSubredBackup)
            {
                //actualizo el tipo de la subred
                $objSubredBackup->setTipo("WAN");
                $this->emInfraestructura->persist($objSubredBackup);
                $this->emInfraestructura->flush();
            }

            //se guarda el detalle del vlan principal asignado
            $objDetalleElementoVlanPri = new InfoDetalleElemento();
            $objDetalleElementoVlanPri->setElementoId($objOltElemento->getId());
            $objDetalleElementoVlanPri->setDetalleNombre($strVlanPriGpon);
            $objDetalleElementoVlanPri->setDetalleValor($arrayDataSubred['vrf_telconet_pri']['vlan_id']);
            $objDetalleElementoVlanPri->setDetalleDescripcion($strVlanPriGpon);
            $objDetalleElementoVlanPri->setFeCreacion(new \DateTime('now'));
            $objDetalleElementoVlanPri->setUsrCreacion($strUsrCreacion);
            $objDetalleElementoVlanPri->setIpCreacion($strIpCreacion);
            $objDetalleElementoVlanPri->setEstado('Activo');
            $this->emInfraestructura->persist($objDetalleElementoVlanPri);
            $this->emInfraestructura->flush();

            //se guarda el detalle del vlan backup asignado
            $objDetalleElementoVlanBk = new InfoDetalleElemento();
            $objDetalleElementoVlanBk->setElementoId($objOltElemento->getId());
            $objDetalleElementoVlanBk->setDetalleNombre($strVlanBkGpon);
            $objDetalleElementoVlanBk->setDetalleValor($arrayDataSubred['vrf_telconet_bk']['vlan_id']);
            $objDetalleElementoVlanBk->setDetalleDescripcion($strVlanBkGpon);
            $objDetalleElementoVlanBk->setFeCreacion(new \DateTime('now'));
            $objDetalleElementoVlanBk->setUsrCreacion($strUsrCreacion);
            $objDetalleElementoVlanBk->setIpCreacion($strIpCreacion);
            $objDetalleElementoVlanBk->setEstado('Activo');
            $this->emInfraestructura->persist($objDetalleElementoVlanBk);
            $this->emInfraestructura->flush();

            //se guarda el detalle del multiplataforma
            $objDetalleElementoMulti = new InfoDetalleElemento();
            $objDetalleElementoMulti->setElementoId($objOltElemento->getId());
            $objDetalleElementoMulti->setDetalleNombre($strMultiGpon);
            $objDetalleElementoMulti->setDetalleValor("SI");
            $objDetalleElementoMulti->setDetalleDescripcion($strMultiGpon);
            $objDetalleElementoMulti->setFeCreacion(new \DateTime('now'));
            $objDetalleElementoMulti->setUsrCreacion($strUsrCreacion);
            $objDetalleElementoMulti->setIpCreacion($strIpCreacion);
            $objDetalleElementoMulti->setEstado('Activo');
            $this->emInfraestructura->persist($objDetalleElementoMulti);
            $this->emInfraestructura->flush();

            //actualizo el estado de la solicitud
            $objDetalleSolicitud->setEstado("Finalizada");
            $this->emcom->persist($objDetalleSolicitud);
            $this->emcom->flush();

            //agregar historial a la solicitud
            $objDetalleSolHistorial = new InfoDetalleSolHist();
            $objDetalleSolHistorial->setDetalleSolicitudId($objDetalleSolicitud);
            $objDetalleSolHistorial->setIpCreacion($strIpCreacion);
            $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
            $objDetalleSolHistorial->setUsrCreacion($strUsrCreacion);
            $objDetalleSolHistorial->setEstado($objDetalleSolicitud->getEstado());
            $objDetalleSolHistorial->setObservacion("Se activo el olt multiplataforma.");
            $this->emcom->persist($objDetalleSolHistorial);
            $this->emcom->flush();

            if($objOltElemento->getEstado() != 'Activo')
            {
                //actualizo el estado de la solicitud
                $objOltElemento->setEstado("Activo");
                $this->emInfraestructura->persist($objOltElemento);
                $this->emInfraestructura->flush();
                //se guarda el detalle del vlan backup asignado
                $objDetalleElementoActivo = new InfoDetalleElemento();
                $objDetalleElementoActivo->setElementoId($objOltElemento->getId());
                $objDetalleElementoActivo->setDetalleNombre($strEleActivoMulti);
                $objDetalleElementoActivo->setDetalleValor("SI");
                $objDetalleElementoActivo->setDetalleDescripcion($strEleActivoMulti);
                $objDetalleElementoActivo->setFeCreacion(new \DateTime('now'));
                $objDetalleElementoActivo->setUsrCreacion($strUsrCreacion);
                $objDetalleElementoActivo->setIpCreacion($strIpCreacion);
                $objDetalleElementoActivo->setEstado('Activo');
                $this->emInfraestructura->persist($objDetalleElementoActivo);
                $this->emInfraestructura->flush();
            }
            //agregar historial al elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objOltElemento);
            $objHistorialElemento->setObservacion("Se activo el olt multiplataforma.");
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setUsrCreacion($strUsrCreacion);
            $objHistorialElemento->setIpCreacion($strIpCreacion);
            $objHistorialElemento->setEstadoElemento($objOltElemento->getEstado());
            $this->emInfraestructura->persist($objHistorialElemento);
            $this->emInfraestructura->flush();

            $this->emcom->flush();
            $this->emcom->getConnection()->commit();
            $this->emInfraestructura->flush();
            $this->emInfraestructura->getConnection()->commit();

            $arrayResult = array(
                'status'   => 'OK',
                'mensaje'  => "Se activo el olt multiplataforma ($strNombreOlt) correctamente."
            );
        }
        catch(\Exception $e)
        {
            $arrayResult = array(
                'status'  => 'ERROR',
                'mensaje' => $e->getMessage()
            );
            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }
            if( $this->emInfraestructura->getConnection()->isTransactionActive() )
            {
                $this->emInfraestructura->getConnection()->rollback();
                $this->emInfraestructura->getConnection()->close();
            }
            $this->utilService->insertError("Telcos+",
                                      "InfoElementoService.activarElementoOltMultiplataformaTN",
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion
                                    );
        }
        return $arrayResult;
    }

    /**
     * Funcion que sirve para obtener equipos pendiente para retirar en el nodo
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 22-07-2021
     * 
     * @param array $arrayParametros
     * @return array $arrayRespuesta
     */
    public function obtenerDispositivoRetiroNodoPorCaracteristica($arrayParametros)
    {
        $intIdDetalleSolicitud  = $arrayParametros['intIdDetalleSolicitud'];
        $intIdCaracteristica    = $arrayParametros['intIdCaracteristica'];
        $arrayEstados           = $arrayParametros['strEstado'];

        $arrayRespuesta         = null;

        try
        {          
            $arrayCaracteristicasSolicitud = $this->emcom
                                                    ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findBy(array( "detalleSolicitudId"    => $intIdDetalleSolicitud,
                                                                    "estado"                => $arrayEstados,
                                                                    "caracteristicaId"      => $intIdCaracteristica
                                                                )
                                                            );

            foreach($arrayCaracteristicasSolicitud as $objCaracteristicaSolicitud)
            {
                    $objElemento     = $this->emInfraestructura
                                                    ->getRepository('schemaBundle:InfoElemento')
                                                    ->find($objCaracteristicaSolicitud->getValor());

                    if (is_object($objElemento))
                    {
                        $arrayRespuesta = $objElemento;
                    }  
            }
        } 
        catch (\Exception $exception) 
        {
            $this->serviceUtil->insertLog(array(
                'enterpriseCode'   => '10',
                'logType'          => 1,
                'logOrigin'        => 'TELCOS',
                'application'      => 'TELCOS',
                'appClass'         => 'InfoElementoService',
                'appMethod'        => 'obtenerDispositivosRetiroClienteNodo',
                'descriptionError' => $exception->getMessage(),
                'status'           => 'Fallido',
                'inParameters'     => json_encode($arrayParametros),
                'creationUser'     => 'TELCOS'));

            return null;
        }

        return $arrayRespuesta;
    }

    /**
     * Funcion que sirve para reversar la solicitud de olt multiplataforma
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 23-06-2021
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 06-08-2021 - Se eliminan las vlans de cada servicio adicional
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 11-03-2022 - Se envia el usuario de la acción
     *
     * @param Array $arrayData [
     *                             strIdSolicitud,  Id de la solicitud
     *                             intIdOlt,        Id del olt
     *                             intIdNodo,       Id del nodo
     *                             objSolCaractPe,  Objeto de la característica PE
     *                             objSolCaractIp,  Objeto de la característica IP
     *                             strIdEmpresa,    Id de empresa
     *                             strUsrSesion,    Usuario creación
     *                             strIpClient,     Ip creación
     *                         ]
     * @return Array $arrayResponse [ status , mensaje ]
     */
    public function reversarSolicitudOltMultiplataformaTN($arrayData)
    {
        $serviceNetworking = $this->container->get('tecnico.NetworkingScripts');
        $strIdSolicitud    = $arrayData['strIdSolicitud'];
        $intIdOlt          = $arrayData['intIdOlt'];
        $intIdNodo         = $arrayData['intIdNodo'];
        $objSolCaractPe    = $arrayData['objSolCaractPe'];
        $objSolCaractIp    = $arrayData['objSolCaractIp'];
        $strIdEmpresa      = $arrayData['strIdEmpresa'];
        $strUsrSesion      = $arrayData['strUsrSesion'];
        $strIpClient       = $arrayData['strIpClient'];

        try
        {
            $this->emInfraestructura->getConnection()->beginTransaction();
            $this->emcom->getConnection()->beginTransaction();

            //obtengo la solicitud
            $objDetalleSolicitud = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')->find($strIdSolicitud);
            if(!is_object($objDetalleSolicitud))
            {
                throw new \Exception("No se encontró la solicitud del Olt Multiplataforma, por favor notificar a Sistemas.");
            }
            //obtengo el elemento olt
            $objOltElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdOlt);
            if(!is_object($objOltElemento))
            {
                throw new \Exception("No se encontró el elemento Olt, por favor notificar a Sistemas.");
            }
            //obtengo el elemento nodo
            $objNodoElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdNodo);
            if(!is_object($objNodoElemento))
            {
                throw new \Exception("No se encontró el elemento Nodo, por favor notificar a Sistemas.");
            }

            //verificar si el olt ya tiene servicios
            $arrayProductosPermitidos = array();
            $arrayParametrosProductos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                            ->get('NUEVA_RED_GPON_TN',
                                                                                'COMERCIAL',
                                                                                '',
                                                                                'PARAMETRO PARA DEFINIR EL TIPO DE RED GPON DE UN PRODUCTO',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                'S',
                                                                                'RELACION_PRODUCTO_CARACTERISTICA',
                                                                                $strIdEmpresa);
            foreach($arrayParametrosProductos as $arrayDetalles)
            {
                $arrayProductosPermitidos[] = $arrayDetalles['valor1'];
            }
            $arrayEstadosPermitidos = array();
            $arrayParametrosEstados = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->get('NUEVA_RED_GPON_TN',
                                                                                                    'COMERCIAL',
                                                                                                    '',
                                                                                                    '',
                                                                                                    'ESTADOS_SERVICIOS_NO_PERMITIDOS_FLUJO',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '');
            foreach($arrayParametrosEstados as $arrayDetalles)
            {
                $arrayEstadosPermitidos[] = $arrayDetalles['valor2'];
            }
            if(!empty($arrayProductosPermitidos) && !empty($arrayEstadosPermitidos))
            {
                $arrayServiciosElemento = $this->emcom->getRepository("schemaBundle:InfoServicio")
                                                        ->createQueryBuilder('s')
                                                        ->innerJoin('schemaBundle:InfoServicioTecnico', 'tec', 'WITH', 'tec.servicioId = s.id')
                                                        ->where("tec.elementoId = :elementoId")
                                                        ->andWhere("s.estado NOT IN (:estadoNot)")
                                                        ->andWhere("s.productoId IN (:productos)")
                                                        ->setParameter('elementoId', $objOltElemento->getId())
                                                        ->setParameter('estadoNot', array_values($arrayEstadosPermitidos))
                                                        ->setParameter('productos', array_values($arrayProductosPermitidos))
                                                        ->getQuery()
                                                        ->getResult();
                if(!empty($arrayServiciosElemento) && is_array($arrayServiciosElemento) && count($arrayServiciosElemento) > 0)
                {
                    throw new \Exception("No se puede reversar la Solicitud Olt Multiplataforma el elemento contiene servicios, ".
                                         "por favor notificar a Sistemas.");
                }
            }

            //seteo las variables
            $strDetalleMulti         = "MULTIPLATAFORMA";
            $strDetallePeAsignado    = "PE_ASIGNADO";
            $strDetalleNodoAsignado  = "NODO_ASIGNADO";
            $strDetalleIpv6          = "IPV6";
            $strDetalleActivoMult    = "ELEMENTO_ACTIVO_POR_MULTIPLATAFORMA";
            $strDetalleInterfacesPe  = "INTERFACES_PE";
            $arrayParametrosDetMulti = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                    'COMERCIAL',
                                                                                                    '',
                                                                                                    'NOMBRES PARAMETROS DETALLES MULTIPLATAFORMA',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '');
            if(isset($arrayParametrosDetMulti) && !empty($arrayParametrosDetMulti))
            {
                $strDetalleMulti        = isset($arrayParametrosDetMulti['valor1']) && !empty($arrayParametrosDetMulti['valor1'])
                                          ? $arrayParametrosDetMulti['valor1'] : $strDetalleMulti;
                $strDetallePeAsignado   = isset($arrayParametrosDetMulti['valor2']) && !empty($arrayParametrosDetMulti['valor2'])
                                          ? $arrayParametrosDetMulti['valor2'] : $strDetallePeAsignado;
                $strDetalleNodoAsignado = isset($arrayParametrosDetMulti['valor3']) && !empty($arrayParametrosDetMulti['valor3'])
                                          ? $arrayParametrosDetMulti['valor3'] : $strDetalleNodoAsignado;
                $strDetalleIpv6         = isset($arrayParametrosDetMulti['valor4']) && !empty($arrayParametrosDetMulti['valor4'])
                                          ? $arrayParametrosDetMulti['valor4'] : $strDetalleIpv6;
                $strDetalleActivoMult   = isset($arrayParametrosDetMulti['valor5']) && !empty($arrayParametrosDetMulti['valor5'])
                                          ? $arrayParametrosDetMulti['valor5'] : $strDetalleActivoMult;
                $strDetalleInterfacesPe = isset($arrayParametrosDetMulti['valor6']) && !empty($arrayParametrosDetMulti['valor6'])
                                          ? $arrayParametrosDetMulti['valor6'] : $strDetalleInterfacesPe;
            }

            //obtengo el detalle del activo multiplataforma
            $objDetalleInterfaces = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                      ->findOneBy(array("elementoId"    => $objOltElemento->getId(),
                                                                        "detalleNombre" => $strDetalleInterfacesPe,
                                                                        "estado"        => "Activo"));

            if($objDetalleSolicitud->getEstado() == "Configurado" || $objDetalleSolicitud->getEstado() == "Activo")
            {
                //seteo el arreglo de parametros para reversar la configuración en el olt multiplataforma
                $arrayParametros = array(
                    'url'        => 'rollBack',
                    'accion'     => 'Cancelar',
                    'pe'         => $objSolCaractPe->getValor(),
                    'id_olt'     => $objOltElemento->getId(),
                    'olt'        => $objOltElemento->getNombreElemento(),
                    'servicio'   => 'GENERAL',
                    'login_aux'  => '',
                    'user_name'  => $strUsrSesion,
                    'user_ip'    => $strIpClient
                );
                //se ejecuta script de networking
                $arrayRespuestaNetworking = $serviceNetworking->callNetworkingWebService($arrayParametros);
            }
            if($objDetalleSolicitud->getEstado() == "Asignado")
            {
                if(!is_object($objDetalleInterfaces))
                {
                    throw new \Exception("No se encontró el detalle elemento de las interfaces de PE, por favor notificar a Sistemas.");
                }
                //seteo el arreglo de parametros para eliminar los recursos de red en el olt multiplataforma
                $arrayParametros = array(
                    'url'        => 'assignResources',
                    'accion'     => 'eliminar',
                    'pe'         => $objSolCaractPe->getValor(),
                    'id_olt'     => $objOltElemento->getId(),
                    'olt'        => $objOltElemento->getNombreElemento(),
                    'interfaces' => $objDetalleInterfaces->getDetalleValor(),
                    'servicio'   => 'GENERAL',
                    'login_aux'  => '',
                    'user_name'  => $strUsrSesion,
                    'user_ip'    => $strIpClient
                );
                //se ejecuta script de networking
                $arrayRespuestaNetworking = $serviceNetworking->callNetworkingWebService($arrayParametros);
            }
            if($objDetalleSolicitud->getEstado() == "Pendiente")
            {
                $strPosParNodo   = strpos($objNodoElemento->getNombreElemento(),'(');
                $strNodoFormat   = trim(substr($objNodoElemento->getNombreElemento(), 0, $strPosParNodo));
                //seteo el arreglo de parametros para eliminar la ipv6 en networking
                $arrayParametros = array(
                    'url'       => 'manageIPv6',
                    'accion'    => 'eliminar',
                    'pe'        => $objSolCaractPe->getValor(),
                    'id_olt'    => $objOltElemento->getId(),
                    'olt'       => $objOltElemento->getNombreElemento(),
                    'ipv6'      => $objSolCaractIp->getValor(),
                    'id_nodo'   => $objNodoElemento->getId(),
                    'nodo'      => $strNodoFormat,
                    'servicio'  => 'GENERAL',
                    'login_aux' => '',
                    'user_name' => $strUsrSesion,
                    'user_ip'   => $strIpClient
                );
                //se ejecuta script de networking
                $arrayRespuestaNetworking = $serviceNetworking->callNetworkingWebService($arrayParametros);
            }
            if($arrayRespuestaNetworking['status'] == 'ERROR')
            {
                throw new \Exception($arrayRespuestaNetworking['mensaje']);
            }

            //obtengo el detalle del nodo
            $objDetalleNodo = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                      ->findOneBy(array("elementoId"    => $objOltElemento->getId(),
                                                                        "detalleNombre" => $strDetalleNodoAsignado,
                                                                        "estado"        => "Activo"));
            if(is_object($objDetalleNodo))
            {
                //actualizo el estado del nodo
                $objDetalleNodo->setEstado("Eliminado");
                $this->emInfraestructura->persist($objDetalleNodo);
                $this->emInfraestructura->flush();
            }

            //obtengo el detalle de la ipv6
            $objDetalleIpv6 = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                      ->findOneBy(array("elementoId"    => $objOltElemento->getId(),
                                                                        "detalleNombre" => $strDetalleIpv6,
                                                                        "estado"        => "Activo"));
            if(is_object($objDetalleIpv6))
            {
                //actualizo el estado del nodo
                $objDetalleIpv6->setEstado("Eliminado");
                $this->emInfraestructura->persist($objDetalleIpv6);
                $this->emInfraestructura->flush();
            }

            //obtengo el detalle del id del pe
            $objDetalleIdPe = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                      ->findOneBy(array("elementoId"    => $objOltElemento->getId(),
                                                                        "detalleNombre" => $strDetallePeAsignado,
                                                                        "estado"        => "Activo"));
            if(is_object($objDetalleIdPe))
            {
                //actualizo el estado del id del pe
                $objDetalleIdPe->setEstado("Eliminado");
                $this->emInfraestructura->persist($objDetalleIdPe);
                $this->emInfraestructura->flush();
            }

            //obtengo el detalle del multiplataforma
            $objDetalleMulti = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                      ->findOneBy(array("elementoId"    => $objOltElemento->getId(),
                                                                        "detalleNombre" => $strDetalleMulti,
                                                                        "estado"        => "Activo"));
            if(is_object($objDetalleMulti))
            {
                //actualizo el estado del multiplataforma
                $objDetalleMulti->setEstado("Eliminado");
                $this->emInfraestructura->persist($objDetalleMulti);
                $this->emInfraestructura->flush();
            }

            //obtengo el detalle del activo multiplataforma
            $objDetalleActivo = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                      ->findOneBy(array("elementoId"    => $objOltElemento->getId(),
                                                                        "detalleNombre" => $strDetalleActivoMult,
                                                                        "estado"        => "Activo"));
            if(is_object($objDetalleActivo))
            {
                //actualizo el estado del activo multiplataforma
                $objDetalleActivo->setEstado("Eliminado");
                $this->emInfraestructura->persist($objDetalleActivo);
                $this->emInfraestructura->flush();
                //actualizo el estado del elemento
                $objOltElemento->setEstado("Eliminado");
                $this->emInfraestructura->persist($objOltElemento);
                $this->emInfraestructura->flush();
            }

            //verifico si existe las interfaces del pe
            if(is_object($objDetalleInterfaces))
            {
                //actualizo el estado del detalle de interfaces pe multiplataforma
                $objDetalleInterfaces->setEstado("Eliminado");
                $this->emInfraestructura->persist($objDetalleInterfaces);
                $this->emInfraestructura->flush();
            }

            //seteo los parametros de las subredes y vlans
            $strIntPriGpon  = "INTPRIGPON";
            $strIntBkGpon   = "INTBKGPON";
            $strVlanPriGpon = "VLAN INTERNET GPON PRINCIPAL";
            $strVlanBkGpon  = "VLAN INTERNET GPON BACKUP";
            $arrayParametrosSubred = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                    'COMERCIAL',
                                                                                                    '',
                                                                                                    'NOMBRES PARAMETROS SUBREDES Y VLANS',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '');
            if(isset($arrayParametrosSubred) && !empty($arrayParametrosSubred))
            {
                $strIntPriGpon  = isset($arrayParametrosSubred['valor1']) && !empty($arrayParametrosSubred['valor1'])
                                 ? $arrayParametrosSubred['valor1'] : $strIntPriGpon;
                $strIntBkGpon   = isset($arrayParametrosSubred['valor2']) && !empty($arrayParametrosSubred['valor2'])
                                 ? $arrayParametrosSubred['valor2'] : $strIntBkGpon;
                $strVlanPriGpon = isset($arrayParametrosSubred['valor4']) && !empty($arrayParametrosSubred['valor4'])
                                 ? $arrayParametrosSubred['valor4'] : $strVlanPriGpon;
                $strVlanBkGpon  = isset($arrayParametrosSubred['valor5']) && !empty($arrayParametrosSubred['valor5'])
                                 ? $arrayParametrosSubred['valor5'] : $strVlanBkGpon;
            }

            //obtengo el detalle de la vlan principal
            $objDetalleVlanPri = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                      ->findOneBy(array("elementoId"    => $objOltElemento->getId(),
                                                                        "detalleNombre" => $strVlanPriGpon,
                                                                        "estado"        => "Activo"));
            if(is_object($objDetalleVlanPri))
            {
                //actualizo el estado de la vlan principal
                $objDetalleVlanPri->setEstado("Eliminado");
                $this->emInfraestructura->persist($objDetalleVlanPri);
                $this->emInfraestructura->flush();
            }

            //obtengo el detalle de la vlan backup
            $objDetalleVlanBk = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                      ->findOneBy(array("elementoId"    => $objOltElemento->getId(),
                                                                        "detalleNombre" => $strVlanBkGpon,
                                                                        "estado"        => "Activo"));
            if(is_object($objDetalleVlanBk))
            {
                //actualizo el estado de la vlan backup
                $objDetalleVlanBk->setEstado("Eliminado");
                $this->emInfraestructura->persist($objDetalleVlanBk);
                $this->emInfraestructura->flush();
            }

            //obtengo el valor de la vlan para servicios safecity
            $arrayParametroVlanSafe = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->get('NUEVA_RED_GPON_TN',
                                                                                            'COMERCIAL',
                                                                                            '',
                                                                                            'PARAMETRO VLAN PARA SERVICIOS ADICIONALES SAFECITY',
                                                                                            '',
                                                                                            '',
                                                                                            '',
                                                                                            '',
                                                                                            '');
            foreach($arrayParametroVlanSafe as $arrayParVlanSafe)
            {
                //obtengo el detalle de la vlan del servicio adicional safecity
                $objDetalleVlanSafe = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                          ->findOneBy(array("elementoId"    => $objOltElemento->getId(),
                                                                            "detalleNombre" => $arrayParVlanSafe['valor1'],
                                                                            "estado"        => "Activo"));
                if(is_object($objDetalleVlanSafe))
                {
                    //actualizo el estado de la vlan backup
                    $objDetalleVlanSafe->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objDetalleVlanSafe);
                    $this->emInfraestructura->flush();
                }
            }

            //obtengo el arreglo subred principal
            $arraySubredPrincipal = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                      ->findBy(array("elementoId"    => $objOltElemento->getId(),
                                                                     "uso"           => $strIntPriGpon));
            foreach($arraySubredPrincipal as $objSubredPrincipal)
            {
                //actualizo el estado del multiplataforma
                $objSubredPrincipal->setEstado("Eliminado");
                $this->emInfraestructura->persist($objSubredPrincipal);
                $this->emInfraestructura->flush();
            }

            //obtengo el arreglo subred backup
            $arraySubredBackup = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                      ->findBy(array("elementoId"    => $objOltElemento->getId(),
                                                                     "uso"           => $strIntBkGpon));
            foreach($arraySubredBackup as $objSubredBackup)
            {
                //actualizo el estado del multiplataforma
                $objSubredBackup->setEstado("Eliminado");
                $this->emInfraestructura->persist($objSubredBackup);
                $this->emInfraestructura->flush();
            }

            //actualizo el estado de la solicitud
            $objDetalleSolicitud->setEstado("Anulado");
            $this->emcom->persist($objDetalleSolicitud);
            $this->emcom->flush();

            //obtengo el arreglo subred backup
            $arrayDetSolicitudCaract = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                      ->findBy(array("detalleSolicitudId"=> $objDetalleSolicitud->getId()));
            foreach($arrayDetSolicitudCaract as $objDetalleSolicitudCaract)
            {
                //actualizo el estado del multiplataforma
                $objDetalleSolicitudCaract->setEstado($objDetalleSolicitud->getEstado());
                $this->emcom->persist($objDetalleSolicitudCaract);
                $this->emcom->flush();
            }

            //seteo observacion
            $strObservacion = "Se anuló la Solicitud Olt Multiplataforma y se liberaron los recursos de red asignados al ".
                              $objOltElemento->getNombreElemento().".";
            //agregar historial a la solicitud
            $objDetalleSolHistorial = new InfoDetalleSolHist();
            $objDetalleSolHistorial->setDetalleSolicitudId($objDetalleSolicitud);
            $objDetalleSolHistorial->setIpCreacion($strIpClient);
            $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
            $objDetalleSolHistorial->setUsrCreacion($strUsrSesion);
            $objDetalleSolHistorial->setEstado($objDetalleSolicitud->getEstado());
            $objDetalleSolHistorial->setObservacion($strObservacion);
            $this->emcom->persist($objDetalleSolHistorial);
            $this->emcom->flush();

            //agregar historial al elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objOltElemento);
            $objHistorialElemento->setObservacion($strObservacion);
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setUsrCreacion($strUsrSesion);
            $objHistorialElemento->setIpCreacion($strIpClient);
            $objHistorialElemento->setEstadoElemento($objOltElemento->getEstado());
            $this->emInfraestructura->persist($objHistorialElemento);
            $this->emInfraestructura->flush();

            $this->emcom->flush();
            $this->emcom->getConnection()->commit();
            $this->emInfraestructura->flush();
            $this->emInfraestructura->getConnection()->commit();

            $arrayResult = array(
                'status'   => 'OK',
                'mensaje'  => $strObservacion
            );
        }
        catch(\Exception $e)
        {
            $arrayResult = array(
                'status'  => 'ERROR',
                'mensaje' => $e->getMessage()
            );
            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }
            if( $this->emInfraestructura->getConnection()->isTransactionActive() )
            {
                $this->emInfraestructura->getConnection()->rollback();
                $this->emInfraestructura->getConnection()->close();
            }
            $this->utilService->insertError("Telcos+",
                                      "InfoElementoService.reversarSolicitudOltMultiplataformaTN",
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                    );
        }
        return $arrayResult;
    }

    /**
     * Función que obtiene el elemento Nodo que está relacionado con el elemento Olt
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 27-07-2021
     *
     * @param Array $arrayData [
     *                             intIdOlt,        Id del olt
     *                             strUsrSesion,    Usuario creación
     *                             strIpClient,     Ip creación
     *                         ]
     * @return Objecto $objElementoNodo
     */
    public function getElementoNodoPorOlt($arrayData)
    {
        $intIdOlt     = $arrayData['intIdOlt'];
        $strUsrSesion = $arrayData['strUsrSesion'];
        $strIpClient  = $arrayData['strIpClient'];

        try
        {
            $objElementoNodo     = null;
            $objElementoPadreOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                             ->findOneBy(array("elementoIdB" => $intIdOlt,"estado" => "Activo"));
            if(is_object($objElementoPadreOlt))
            {
                $objElementoRelB = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                        ->find($objElementoPadreOlt->getElementoIdA());
                if(!is_object($objElementoRelB))
                {
                    throw new \Exception("No se ha podido encontrar la relación del elemento, por favor notificar a Sistemas.");
                }
                $strNombreModelo = $objElementoRelB->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                if($strNombreModelo == "NODO")
                {
                    $objElementoNodo = $objElementoRelB;
                }
                else
                {
                    $objRelacionElementoRack = $this->emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                ->findOneBy(array("elementoIdB" => $objElementoPadreOlt->getElementoIdA(),
                                                                  "estado"      => "Activo"));

                    if(is_object($objRelacionElementoRack))
                    {
                        $objElementoRelB = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                        ->find($objRelacionElementoRack->getElementoIdA());
                        if(!is_object($objElementoRelB))
                        {
                            throw new \Exception("No se ha podido encontrar la relación del elemento, por favor notificar a Sistemas.");
                        }
                        $strNombreModelo = $objElementoRelB->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                        if($strNombreModelo == "NODO")
                        {
                            $objElementoNodo = $objElementoRelB;
                        }
                        else
                        {
                            $objRelacionElementoNodo = $this->emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                          ->findOneBy(array("elementoIdB" => $objRelacionElementoRack->getElementoIdA(),
                                                                            "estado"      => "Activo"));
                            if(is_object($objRelacionElementoNodo))
                            {
                                $objElementoRelB = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                        ->find($objRelacionElementoNodo->getElementoIdA());
                                if(!is_object($objElementoRelB))
                                {
                                    throw new \Exception("No se ha podido encontrar la relación del elemento, ".
                                                         "por favor notificar a Sistemas.");
                                }
                                $strNombreModelo = $objElementoRelB->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                                if($strNombreModelo == "NODO")
                                {
                                    $objElementoNodo = $objElementoRelB;
                                }
                            }
                        }
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            $objElementoNodo = null;
            $this->utilService->insertError("Telcos+",
                                      "InfoElementoService.getElementoNodoPorOlt",
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                    );
        }
        return $objElementoNodo;
    }

    /*
     * Función para realizar la carga y descarga de los activos en el naf.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 12-05-2021
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 06-06-2022 - Se valida si el servicio es safecity para obtener la información del elemento asignado por contratista y empleado.
     *
     * @param type $arrayParametros [
     *                                 boolRegistrarTraking    : Valor booleano que indica si se debe registrar el traking de los
     *                                                           dipositivos por cancelación del servicio.
     *                                 intNumeroTarea          : Número de tarea de la solicitud de Instalación, Cambio o Retiro.
     *                                 intIdElementoNodo       : Id del elemento nodo.
     *                                 intIdServicio           : Id del servicio del cliente.
     *                                 strTipoRecibe           : Tipo quien recibe el articulo - Cliente/Empleado/Nodo.
     *                                 intIdEmpleado           : Id del empleado encargado.
     *                                 intIdEmpresa            : Id de la empresa.
     *                                 strTipoActividad        : Instalacion, Soporte, Retiro, InstalacionNodo, SoporteNodo, RetiroNodo.
     *                                 strTipoTransaccion      : Tarea,Nuevo, etc.
     *                                 strObservacion          : Observación.
     *                                 arrayEquipos            : [
     *                                     strNumeroSerie  : Número de serie del dispositivo.
     *                                     intIdControl    : Id de la tabla NAF47_TNET.ARAF_CONTROL_CUSTODIO.
     *                                     intCantidadEnt  : Cantidad a entregar.
     *                                     intCantidadRec  : Cantdad a recibir.
     *                                     strTipoArticulo : Tipo de artículo.
     *                                 ]
     *                                 strEstadoSolicitud      : Estado de la solicitud.
     *                                 strDescripcionSolicitud : Descripción de la solicitud.
     *                                 strUsuario              : Login del usuario quien realiza la transacción.
     *                                 strIpUsuario            : Ip del usuario quien realiza la transacción.
     *                              ]
     * @return type $arrayRespuesta
     */
    public function cargaDescargaActivos($arrayParametros)
    {
        $boolRegistrarTraking    = $arrayParametros['boolRegistrarTraking'];
        $intNumeroTarea          = $arrayParametros['intNumeroTarea'];
        $intIdElementoNodo       = $arrayParametros['intIdElementoNodo'];
        $intIdServicio           = $arrayParametros['intIdServicio'];
        $strTipoRecibe           = $arrayParametros['strTipoRecibe'];
        $intIdEmpleado           = $arrayParametros['intIdEmpleado'];
        $intIdEmpresa            = $arrayParametros['intIdEmpresa'];
        $strTipoActividad        = $arrayParametros['strTipoActividad'];
        $strTipoTransaccion      = $arrayParametros['strTipoTransaccion'];
        $strObservacion          = $arrayParametros['strObservacion'];
        $arrayEquipos            = $arrayParametros['arrayEquipos'];
        $strEstadoSolicitud      = $arrayParametros['strEstadoSolicitud'];
        $strDescripcionSolicitud = $arrayParametros['strDescripcionSolicitud'];
        $strUsuario              = $arrayParametros['strUsuario'];
        $strIpUsuario            = $arrayParametros['strIpUsuario'];
        $arrayCargaDescarga      = array();

        try
        {
            //Obtenemos la información del cliente y el empleado encargado.
            if (!empty($intIdServicio))
            {
                //Obtenemos los datos del servicio.
                $objInfoServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
                if (!is_object($objInfoServicio))
                {
                    throw new \Exception('Error : No existe el servicio del cliente con id '.$intIdServicio);
                }

                $objInfoPunto = $objInfoServicio->getPuntoId();
                if (!is_object($objInfoPunto))
                {
                    throw new \Exception('Error : El servicio no esta atado a un punto.');
                }
            }
            elseif (!empty($intIdElementoNodo))
            {
                //Obtenemos los datos del elemento nodo.
                $objInfoElementoNodo = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoNodo);
                if (!is_object($objInfoElementoNodo))
                {
                    throw new \Exception('Error : No existe el nodo con id '.$intIdElementoNodo);
                }
            }
            else
            {
                throw new \Exception("Error : Información incompleta para realizar la carga y descarga de los activos.");
            }

            //Obtenemos los datos del empleado.
            $arrayInfoPersona = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                    ->getInfoDatosPersona(array ('strRol'                     => 'Empleado',
                                                 'strCodEmpresa'              =>  $intIdEmpresa,
                                                 'intIdPersona'               =>  $intIdEmpleado,
                                                 'strEstadoPersona'           =>  array('Activo','Pendiente','Modificado'),
                                                 'strEstadoPersonaEmpresaRol' => 'Activo'));
            $arrayDatosPersona = $arrayInfoPersona['result'][0];
            if( $arrayInfoPersona['status'] === 'fail' && isset($arrayParametros['intIdPerEmpRolCamara'])
               && isset($arrayParametros['booleanServicioSafecity']) && $arrayParametros['booleanServicioSafecity'] )
            {
                $arrayDatosPersona['idPersonaEmpresaRol'] = $arrayParametros['intIdPerEmpRolCamara'];
            }
            elseif($arrayInfoPersona['status'] === 'fail')
            {
                throw new \Exception($arrayInfoPersona['message']);
            }

            //Validamos quien recibe y entrega el activo.
            if ($strTipoRecibe === 'Cliente')
            {
                $intIdPersonaEmpresaRolEntrega = $arrayDatosPersona['idPersonaEmpresaRol'];
                $intIdPersonaEmpresaRolRecibe  = $objInfoPunto->getPersonaEmpresaRolId()->getId();
                $strLoginRecibe                = $objInfoPunto->getLogin();
            }
            elseif ($strTipoRecibe === 'Nodo')
            {
                $intIdPersonaEmpresaRolEntrega = $arrayDatosPersona['idPersonaEmpresaRol'];
                $intIdPersonaEmpresaRolRecibe  = $objInfoElementoNodo->getId();
                $strLoginRecibe                = $objInfoElementoNodo->getNombreElemento();
            }
            else
            {
                $intIdPersonaEmpresaRolRecibe  = $arrayDatosPersona['idPersonaEmpresaRol'];
                $strLoginRecibe                = $arrayDatosPersona['loginEmpleado'];

                if (is_object($objInfoPunto))
                {
                    $intIdPersonaEmpresaRolEntrega = $objInfoPunto->getPersonaEmpresaRolId()->getId();
                }
                elseif (is_object($objInfoElementoNodo))
                {
                    $intIdPersonaEmpresaRolEntrega = $objInfoElementoNodo->getId();
                }
                else
                {
                    throw new \Exception("Error : No se logro obtener la informacion del cliente que entrega el Activo.");
                }
            }

            //Obtenemos la tarea desde el servicio y solicitud.
            if (!empty($intNumeroTarea))
            {
                $intIdComunicacion = $intNumeroTarea;
            }
            else
            {
                $arrayTarea = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                        ->obtenerTareaSolicitudServicio(array('serviceUtil'             => $serviceUtil,
                                                              'strUsuario'              => $strUsuario,
                                                              'strIpUsuario'            => $strIpUsuario,
                                                              'intIdServicio'           => $intIdServicio,
                                                              'strEstadoSolicitud'      => $strEstadoSolicitud,
                                                              'strDescripcionSolicitud' => $strDescripcionSolicitud));
                $intNumeroTarea    = $arrayTarea["result"][0]['idComunicacion'];
                $intIdComunicacion = empty($intNumeroTarea) || $intNumeroTarea === null ? 0 : $intNumeroTarea;
            }

            //Se crea el array de control de los equipos para realizar la carga y descarga del activo.
            foreach ($arrayEquipos as $arrayEquipo)
            {
                if ($boolRegistrarTraking && is_object($objInfoPunto))
                {
                    //Se ingresa el tracking del elemento
                    $arrayParametrosAuditoria = array();
                    $arrayParametrosAuditoria["strLogin"]        =  $objInfoPunto->getLogin();
                    $arrayParametrosAuditoria["strUsrCreacion"]  =  $strUsuario;
                    $arrayParametrosAuditoria["strNumeroSerie"]  =  $arrayEquipo['strNumeroSerie'];
                    $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
                    $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
                    $arrayParametrosAuditoria["strEstadoActivo"] = 'Eliminado';
                    $arrayParametrosAuditoria["strUbicacion"]    = 'EnTransito';
                    $arrayParametrosAuditoria["strCodEmpresa"]   =  $intIdEmpresa;
                    $arrayParametrosAuditoria["strTransaccion"]  = 'Cancelacion Servicio';
                    $arrayParametrosAuditoria["intOficinaId"]    =  0;
                    $this->ingresaAuditoriaElementos($arrayParametrosAuditoria);
                }

                $arrayControlCustodio[] = array('numeroSerie'      => $arrayEquipo['strNumeroSerie'],
                                                'caracteristicaId' => $arrayEquipo['intIdCaracteristica'],
                                                'empresaId'        => $intIdEmpresa,
                                                'cantidadEnt'      => $arrayEquipo['intCantidadEnt'],
                                                'cantidadRec'      => $arrayEquipo['intCantidadRec'],
                                                'tipoTransaccion'  => $strTipoTransaccion,
                                                'transaccionId'    => $intIdComunicacion,
                                                'tareaId'          => $intIdComunicacion,
                                                'login'            => $strLoginRecibe,
                                                'loginEmpleado'    => $strUsuario,
                                                'idControl'        => $arrayEquipo['intIdControl'],
                                                'tipoArticulo'     => $arrayEquipo['strTipoArticulo']);
            }

            //Parámetros para realizar la carga y descarga del Activo.
            $arrayCargaDescarga['intidPersonaEntrega'] = $intIdPersonaEmpresaRolEntrega;
            $arrayCargaDescarga['intidPersonaRecibe']  = $intIdPersonaEmpresaRolRecibe;
            $arrayCargaDescarga['tipoActividad']       = $strTipoActividad;
            $arrayCargaDescarga['observacion']         = $strObservacion;
            $arrayCargaDescarga['arrayControlCusto']   = $arrayControlCustodio;

            $strResultado = $this->emNaf->getRepository('schemaBundle:InfoDetalleMaterial')
                    ->registrarCargaDescargaActivos($arrayCargaDescarga);

            if ($strResultado !== 'Realizado')
            {
                throw new \Exception('Error : '. $strResultado);
            }

            $arrayRespuesta = array ('status' => true,'message' => $strResultado);
        }
        catch (\Exception $objException)
        {
            $strMessage = "Error al realizar la carga y descarga del Activo";
            $strCodigo  = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }

            $this->utilService->insertError('InfoElementoService',
                                            'cargaDescargaActivos',
                                             $strCodigo.'- 1 -'.$objException->getMessage(),
                                             $strUsuario,
                                             $strIpUsuario);

            $this->utilService->insertError('InfoElementoService',
                                            'cargaDescargaActivos',
                                             $strCodigo.'- 2 -'.json_encode($arrayParametros),
                                             $strUsuario,
                                             $strIpUsuario);

            if (!empty($arrayCargaDescarga))
            {
                $this->utilService->insertError('InfoElementoService',
                                          'cargaDescargaActivos',
                                           $strCodigo.'- 3 -'.json_encode($arrayCargaDescarga),
                                           $strUsuario,
                                           $strIpUsuario);
            }

            $arrayRespuesta = array ('status' => false,'message' => $strMessage);
        }
        return $arrayRespuesta;
    }

    /**
     * Función para realizar la carga y descarga de los activos por cambio de equipo en el naf.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 27-05-2021
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 1.1 09-12-2022 - Se obtiene el id el custodio del elemento cliente
     *
     * @param type $arrayParametros [
     *                                 boolPerteneceElementoNodo : Bandera que indica si el elemento pertenece al nodo.
     *                                 intNumeroTarea            : Número de tarea de la solicitud de cambio de equipo.
     *                                 intIdElementoNodo         : Id del elemento nodo.
     *                                 objServicio               : Objeto servicio del cliente.
     *                                 idElementoActual          : Id del elemento actual a cambiar.
     *                                 serieElementoNuevo        : Serie del elemento nuevo a instalar.
     *                                 tipoResponsable           : Tipo de responsable. C = Cuadrilla, E = Empleado
     *                                 idResponsable             : Id del responsable.
     *                                 idEmpresa                 : Id de la empresa.
     *                                 usrCreacion               : Login del usuario quien realiza la transacción.
     *                                 ipCreacion                : Ip del usuario quien realiza la transacción.
     *                              ]
     * @return type $arrayRespuesta
     */
    public function cargaDescargaActivosCambioEquipo($arrayParametros)
    {
        $boolPerteneceElementoNodo = $arrayParametros['boolPerteneceElementoNodo'];
        $intNumeroTarea            = $arrayParametros['intNumeroTarea'];
        $intIdElementoNodo         = $arrayParametros['intIdElementoNodo'];
        $objInfoServicio           = $arrayParametros['objServicio'];
        $intIdElementoActual       = $arrayParametros['idElementoActual'];
        $strSerieElementoNuevo     = $arrayParametros['serieElementoNuevo'];
        $strTipoResponsable        = $arrayParametros['tipoResponsable'];
        $intIdResponsable          = $arrayParametros['idResponsable'];
        $strIdEmpresa              = $arrayParametros['idEmpresa'];
        $strUsuario                = $arrayParametros['usrCreacion'];
        $strIpUsuario              = $arrayParametros['ipCreacion'];
        $strTipoParametro          = $arrayParametros['strTipoParametro'];
        $intIdCustodio             = null;
        $intIdServicio             = null;

        try
        {
            //Obtenemos la información del elemento actual.
            $objInfoElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoActual);
            if (!is_object($objInfoElemento))
            {
                throw new \Exception('Error : No se logro obtener los datos del elemento actual.');
            }

            if (empty($intIdResponsable))
            {
                throw new \Exception('Error : El id del responsable se encuentra vacio.');
            }

            //Obtenemos el custodio del elemento Cliente o Nodo.
            if ($boolPerteneceElementoNodo)
            {
                if (empty($intIdElementoNodo))
                {
                    throw new \Exception('Error : El id del elemento nodo se encuentra vacio.');
                }

                //Obtenemos los datos del elemento nodo.
                $objInfoElementoNodo = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoNodo);
                if (!is_object($objInfoElementoNodo))
                {
                    throw new \Exception('Error : No existe el nodo con id de elemento '.$intIdElementoNodo);
                }

                $intIdCustodio = $objInfoElementoNodo->getId();
            }
            else
            {
                if (!is_object($objInfoServicio))
                {
                    throw new \Exception('Error : No se logro obtener los datos del servicio.');
                }

                $intIdCustodio  = $objInfoServicio->getPuntoId()->getPersonaEmpresaRolId()->getId();
                $intIdServicio  = $objInfoServicio->getId();
            }

            //Obtenemos los datos del empleado responsable del elemento.
            if ($strTipoResponsable === "C")
            {
                $arrayDatos = $this->emcom->getRepository('schemaBundle:AdmiCuadrilla')->findJefeCuadrilla($intIdResponsable);
                if (empty($arrayDatos))
                {
                    throw new \Exception('Error : No se logro obtener el lider de la cuadrilla.');
                }

                $intIdEmpleado           = $arrayDatos['idPersona'];
                $intIdEmpleadoEmpresaRol = $arrayDatos['idPersonaEmpresaRol'];
            }
            else
            {
                $objInfoPersonaEmpresaRol = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdResponsable);
                if (!is_object($objInfoPersonaEmpresaRol))
                {
                    throw new \Exception('Error : No se logro obtener los datos del empleado.');
                }

                $intIdEmpleado           = $objInfoPersonaEmpresaRol->getPersonaId()->getId();
                $intIdEmpleadoEmpresaRol = $objInfoPersonaEmpresaRol->getId();
            }

            //Descarga Cliente/Nodo y Carga Empleado.
            $arrayControlCustosdio = $this->emNaf->getRepository('schemaBundle:InfoDetalleMaterial')
                    ->obtenerControlCustodio(array('strEstadoEquipo' => 'IN',
                                                   'intIdCustodio'   =>  $intIdCustodio,
                                                   'strNumeroSerie'  =>  $objInfoElemento->getSerieFisica(),
                                                   'serviceUtil'     =>  $this->utilService,
                                                   'strTipoParametro'   =>  $strTipoParametro));

            if ($arrayControlCustosdio['status'])
            {
                $intIdControl   = $arrayControlCustosdio['data'][0]['idControl'];
                $arrayEquipos   = array();
                $arrayEquipos[] = array('intIdControl'    =>  $intIdControl,
                                        'strNumeroSerie'  =>  $objInfoElemento->getSerieFisica(),
                                        'intCantidadEnt'  =>  1,
                                        'intCantidadRec'  =>  1,
                                        'strTipoArticulo' => 'Equipos');

                $arrayCargaDescarga = array();
                $arrayCargaDescarga['intNumeroTarea']          =  $intNumeroTarea;
                $arrayCargaDescarga['intIdEmpresa']            =  $strIdEmpresa;
                $arrayCargaDescarga['strTipoRecibe']           = 'Empleado';
                $arrayCargaDescarga['intIdEmpleado']           =  $intIdEmpleado;
                $arrayCargaDescarga['intIdElementoNodo']       =  $intIdElementoNodo;
                $arrayCargaDescarga['intIdServicio']           =  $intIdServicio;
                $arrayCargaDescarga['strTipoTransaccion']      = 'Tarea';
                $arrayCargaDescarga['strTipoActividad']        =  $boolPerteneceElementoNodo ? 'SoporteNodo' : 'Soporte';
                $arrayCargaDescarga['strObservacion']          = 'Retiro por cambio de equipo';
                $arrayCargaDescarga['strEstadoSolicitud']      = 'Asignada';
                $arrayCargaDescarga['strDescripcionSolicitud'] = 'SOLICITUD RETIRO EQUIPO';
                $arrayCargaDescarga['arrayEquipos']            =  $arrayEquipos;
                $arrayCargaDescarga['strUsuario']              =  $strUsuario;
                $arrayCargaDescarga['strIpUsuario']            =  $strIpUsuario;
                $arrayRestCargaDescarga = $this->cargaDescargaActivos($arrayCargaDescarga);
                if (!$arrayRestCargaDescarga['status'])
                {
                    throw new \Exception('Error : '.$arrayRestCargaDescarga['message']);
                }
            }

            //Descarga Empleado y Carga Cliente/Nodo.
            $arrayControlCustosdioEmp = $this->emNaf->getRepository('schemaBundle:InfoDetalleMaterial')
                    ->obtenerControlCustodio(array('strEstadoEquipo' => 'PI',
                                                   'intIdCustodio'   =>  $intIdEmpleadoEmpresaRol,
                                                   'strNumeroSerie'  =>  $strSerieElementoNuevo,
                                                   'serviceUtil'     =>  $this->utilService));

            if (!$arrayControlCustosdioEmp['status'])
            {
                throw new \Exception('Error : El responsable, no tiene cargado el elemento con serie: '.$strSerieElementoNuevo);
            }

            $intIdControl   = $arrayControlCustosdioEmp['data'][0]['idControl'];
            $arrayEquipos   = array();
            $arrayEquipos[] = array('intIdControl'    =>  $intIdControl,
                                    'strNumeroSerie'  =>  $strSerieElementoNuevo,
                                    'intCantidadEnt'  =>  1,
                                    'intCantidadRec'  =>  1,
                                    'strTipoArticulo' => 'Equipos');

            $arrayCargaDescarga = array();
            $arrayCargaDescarga['intNumeroTarea']          =  $intNumeroTarea;
            $arrayCargaDescarga['intIdEmpresa']            =  $strIdEmpresa;
            $arrayCargaDescarga['strTipoRecibe']           =  $boolPerteneceElementoNodo ? 'Nodo' : 'Cliente';
            $arrayCargaDescarga['intIdElementoNodo']       =  $intIdElementoNodo;
            $arrayCargaDescarga['intIdServicio']           =  $intIdServicio;
            $arrayCargaDescarga['intIdEmpleado']           =  $intIdEmpleado;
            $arrayCargaDescarga['strTipoTransaccion']      = 'Tarea';
            $arrayCargaDescarga['strTipoActividad']        =  $boolPerteneceElementoNodo ? 'SoporteNodo' : 'Soporte';
            $arrayCargaDescarga['strObservacion']          = 'Instalacion por cambio de equipo';
            $arrayCargaDescarga['strEstadoSolicitud']      = 'Asignada';
            $arrayCargaDescarga['strDescripcionSolicitud'] = 'SOLICITUD RETIRO EQUIPO';
            $arrayCargaDescarga['arrayEquipos']            =  $arrayEquipos;
            $arrayCargaDescarga['strUsuario']              =  $strUsuario;
            $arrayCargaDescarga['strIpUsuario']            =  $strIpUsuario;
            $arrayRestCargaDescarga = $this->cargaDescargaActivos($arrayCargaDescarga);
            if (!$arrayRestCargaDescarga['status'])
            {
                throw new \Exception('Error : '.$arrayRestCargaDescarga['message']);
            }

            $arrayRespuesta = array ('status' => true,'message' => "Proceso ejecutado.");
        }
        catch (\Exception $objException)
        {
            $strMessage = "Error al realizar el proceso de carga y descarga del Activo";
            $strCodigo  = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }

            $this->utilService->insertError('InfoElementoService',
                                            'cargaDescargaActivosCambioEquipo',
                                             $strCodigo.'- 1 -'.$objException->getMessage(),
                                             $strUsuario,
                                             $strIpUsuario);

            $this->utilService->insertError('InfoElementoService',
                                            'cargaDescargaActivosCambioEquipo',
                                             $strCodigo.'- 2 -'.json_encode($arrayParametros),
                                             $strUsuario,
                                             $strIpUsuario);

            $arrayRespuesta = array ('status' => false,'message' => $strMessage);
        }
        return  $arrayRespuesta;
    }

    /**
     * Función para eliminar el dispositivo del cliente que se encuentra en el nodo.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 27-05-2021
     *
     * @param type $arrayParametros [
     *                                 intIdElemento  : Id del elemento.
     *                                 strObservacion : Observación.
     *                                 strUsuario     : Login del usuario quien realiza la transacción.
     *                                 strIpUsuario   : Ip del usuario quien realiza la transacción.
     *                              ]
     * @return type $arrayRespuesta
     */
    public function eliminarElementoClienteNodo($arrayParametros)
    {
        $strEstado                 = 'Eliminado';
        $boolPerteneceElementoNodo =  $arrayParametros['boolPerteneceElementoNodo'];
        $intIdElemento             =  $arrayParametros['intIdElemento'];
        $strObservacion            =  $arrayParametros['strObservacion'];
        $strUsuario                =  $arrayParametros['strUsuario'];
        $strIpUsuario              =  $arrayParametros['strIpUsuario'];
        $serviceUtil               =  $this->container->get('schema.Util');

        try
        {
            $objInfoElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElemento);

            if (!is_object($objInfoElemento))
            {
                throw new \Exception('Error : (Eliminar) No se logro obtener los datos del dispositivo del cliente.');
            }

            //Eliminación del elemento.
            $objInfoElemento->setEstado($strEstado);
            $this->emInfraestructura->persist($objInfoElemento);
            $this->emInfraestructura->flush();

            //Eliminación de los detalles del elemento
            $arrayInfoDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                    ->findByElementoId($objInfoElemento->getId());

            foreach ($arrayInfoDetalleElemento as $objInfoDetalleElemento)
            {
                if($objInfoDetalleElemento->getDetalleNombre() != 'SERIE_AUTOMATICA')
                {
                    $objInfoDetalleElemento->setEstado($strEstado);
                    $this->emInfraestructura->persist($objInfoDetalleElemento);
                    $this->emInfraestructura->flush();
                }
            }

            //Eliminación de las relaciones del elemento.
            $arrayInfoRelacionElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                    ->findByElementoIdB($objInfoElemento->getId());

            foreach ($arrayInfoRelacionElemento as $objInfoRelacionElemento)
            {
                $objInfoRelacionElemento->setEstado($strEstado);
                $this->emInfraestructura->persist($objInfoRelacionElemento);
                $this->emInfraestructura->flush();
            }

            if ($boolPerteneceElementoNodo)
            {
                //Eliminación de las relaciones del elemento.
                $arrayInfoRelacionElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                        ->findBy(array('elementoIdA' => $objInfoElemento->getId(),'estado' => 'Activo'));

                foreach ($arrayInfoRelacionElemento as $objInfoRelacionElemento)
                {
                    $objInfoRelacionElemento->setEstado($strEstado);
                    $this->emInfraestructura->persist($objInfoRelacionElemento);
                    $this->emInfraestructura->flush();
                }
            }

            //Eliminación de la información de la empresa elemento.
            $arrayInfoEmpresaElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                    ->findByElementoId($objInfoElemento->getId());

            foreach ($arrayInfoEmpresaElemento as $objInfoEmpresaElemento)
            {
                $objInfoEmpresaElemento->setEstado($strEstado);
                $this->emInfraestructura->persist($objInfoEmpresaElemento);
                $this->emInfraestructura->flush();
            }

            //Historial del elemento eliminado
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objInfoElemento);
            $objHistorialElemento->setObservacion($strObservacion);
            $objHistorialElemento->setEstadoElemento($strEstado);
            $objHistorialElemento->setUsrCreacion($strUsuario);
            $objHistorialElemento->setIpCreacion($strIpUsuario);
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $this->emInfraestructura->persist($objHistorialElemento);
            $this->emInfraestructura->flush();

            $arrayRespuesta = array ('status' => true,'message' => "Proceso ejecutado.");
        }
        catch (\Exception $objException)
        {
            $strMessage = "Error al eliminar el dispositivo del cliente.";
            $strCodigo  = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }

            $serviceUtil->insertError('InfoElementoService',
                                      'eliminarElementoClienteNodo',
                                       $strCodigo.'- 1 -'.$objException->getMessage(),
                                       $strUsuario,
                                       $strIpUsuario);

            $serviceUtil->insertError('InfoElementoService',
                                      'eliminarElementoClienteNodo',
                                       $strCodigo.'- 2 -'.json_encode($arrayParametros),
                                       $strUsuario,
                                       $strIpUsuario);

            $arrayRespuesta = array ('status' => false,'message' => $strMessage);
        }

        return  $arrayRespuesta;
    }

    /**
     * Función para realizar el retiro de un elemento que pertenece a un nodo.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 21-06-2021
     *
     * @param type $arrayParametros [
     *                                 strIdEmpresa       : Id de la empresa.
     *                                 intIdDetalle       : Id de la tabla INFO_DETALLE.
     *                                 intIdElementoNodo  : Id del elemento nodo.
     *                                 strTipoResponsable : Tipo de responsable (C = Cuadrilla, E = Empleado).
     *                                 intIdResponsable   : Id del responsable. (Id de la cuadrilla o id de la persona empresa rol).
     *                                 strUsuario         : Login del usuario quien realiza la transacción.
     *                                 strIpUsuario       : Ip del usuario quien realiza la transacción.
     *                                 intIdSolicitud     : Id de la solicitud.
     *                                 intIdElemento      : id de elemento a retirar.
     *                              ]
     * @return type $arrayRespuesta
     */
    public function retirarElementoPerteneceNodo($arrayParametros)
    {
        $strIdEmpresa          = $arrayParametros['strIdEmpresa'];
        $intIdDetalle          = $arrayParametros['intIdDetalle'];
        $intIdElementoNodo     = $arrayParametros['intIdElementoNodo'];
        $strTipoResponsable    = $arrayParametros['strTipoResponsable'];
        $intIdResponsable      = $arrayParametros['intIdResponsable'];
        $strUsuario            = $arrayParametros['strUsuario'];
        $strIpUsuario          = $arrayParametros['strIpUsuario'];
        $intIdSolicitud        = $arrayParametros['intIdSolicitud'];
        $intIdElemento         = $arrayParametros['intIdElemento'];
        $booleanSolRetiroAutomatico   = isset($arrayParametros['solRetiroAutomatico'])?$arrayParametros['solRetiroAutomatico']:false;
        $boolEsPorCambioEquipo = false;

        $this->emInfraestructura->getConnection()->beginTransaction();
        $this->emcom->getConnection()->beginTransaction();
        $this->emNaf->getConnection()->beginTransaction();
        $this->emSoporte->getConnection()->beginTransaction();

        try
        {
            //Obtenemos los datos de la solicitud.
            if (empty($intIdSolicitud))
            {
                throw new \Exception("Error : Id de solicitud Vacio.");
            }

            $objInfoDetalleSolicitud = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolicitud);
            if (!is_object($objInfoDetalleSolicitud))
            {
                throw new \Exception("Error : No existe la solicitud.");
            }

            //Obtenemos los datos del elemento a retirar.
            if (empty($intIdElemento))
            {
                throw new \Exception("Error : Id de elemento Vacio.");
            }

            $objInfoElementoRetirar = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElemento);
            if (!is_object($objInfoElementoRetirar))
            {
                throw new \Exception("Error : No existe el elemento con id $intIdElemento");
            }

            //Obtenemos los datos del detalle de la tarea.
            if (empty($intIdDetalle))
            {
                throw new \Exception("Error : Id de detalle de tarea Vacio.");
            }

            $objInfoDetalle = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')->find($intIdDetalle);
            if (!is_object($objInfoDetalle))
            {
                throw new \Exception("Error : No existe el detalle de la tarea.");
            }

            //Obtenemos los datos del elemento nodo.
            if (empty($intIdElementoNodo))
            {
                throw new \Exception('Error : El id del elemento nodo se encuentra vacio.');
            }

            $objInfoElementoNodo = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoNodo);
            if (!is_object($objInfoElementoNodo))
            {
                throw new \Exception('Error : No existe el nodo con id de elemento '.$intIdElementoNodo);
            }

            //Obtenemos los datos del empleado responsable.
            if (strtoupper($strTipoResponsable) === "C")
            {
                $arrayDatos = $this->emcom->getRepository('schemaBundle:AdmiCuadrilla')->findJefeCuadrilla($intIdResponsable);
                if (empty($arrayDatos))
                {
                    throw new \Exception('Error : No se logro obtener el lider de la cuadrilla.');
                }

                $objInfoPersona = $this->emcom->getRepository('schemaBundle:InfoPersona')->find($arrayDatos['idPersona']);
                if (!is_object($objInfoPersona))
                {
                    throw new \Exception('Error : No se logro obtener los datos del empleado.');
                }

                $intIdEmpleado     = $objInfoPersona->getId();
                $strCedulaEmpleado = $objInfoPersona->getIdentificacionCliente();
            }
            elseif (strtoupper($strTipoResponsable) === "E")
            {
                $objInfoPersonaEmpresaRol = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdResponsable);
                if (!is_object($objInfoPersonaEmpresaRol))
                {
                    throw new \Exception('Error : No se logro obtener los datos del empleado.');
                }

                $intIdEmpleado     = $objInfoPersonaEmpresaRol->getPersonaId()->getId();
                $strCedulaEmpleado = $objInfoPersonaEmpresaRol->getPersonaId()->getIdentificacionCliente();
            }
            else
            {
                throw new \Exception('Error : Tipo de responsable incorrecto.');
            }

            //Característica para identificar si el retiro es por un cambio de elemento.
            $strCaracteristicaCe     = 'CAMBIO ELEMENTO';
            $objAdmiCaracteristicaCe =  $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array("descripcionCaracteristica" =>  $strCaracteristicaCe,
                                      "estado"                    => 'Activo'));

            if (!is_object($objAdmiCaracteristicaCe))
            {
                throw new \Exception("Error : No existe la característica ($strCaracteristicaCe).");
            }

            //Característica para identificar el enlace de la solicitud con la tarea.
            $strCaracteristicaTar     = 'SOLICITUD NODO';
            $objAdmiCaracteristicaTar =  $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array("descripcionCaracteristica" =>  $strCaracteristicaTar,
                                      "estado"                    => 'Activo'));

            if (!is_object($objAdmiCaracteristicaTar))
            {
                throw new \Exception("Error : No existe la característica ($strCaracteristicaTar).");
            }

            //Finalización de la característica de la solicitud que indica que es por un cambio de equipo.
            $objInfoDetalleSolCaract = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                    ->findOneBy(array('detalleSolicitudId' => $objInfoDetalleSolicitud->getId(),
                                      'caracteristicaId'   => $objAdmiCaracteristicaCe->getId(),
                                      'valor'              => $objInfoElementoRetirar->getId()));

            if (is_object($objInfoDetalleSolCaract))
            {
                $boolEsPorCambioEquipo = true;
                $objInfoDetalleSolCaract->setEstado("Finalizada");
                $this->emcom->persist($objInfoDetalleSolCaract);
                $this->emcom->flush();
            }

            //Obtenemos la información de la característica de la tarea que enlaza la solicitud.
            $objInfoTareaCaracteristica = $this->emSoporte->getRepository('schemaBundle:InfoTareaCaracteristica')
                    ->findOneBy(array('detalleId'        => $objInfoDetalle->getId(),
                                      'caracteristicaId' => $objAdmiCaracteristicaTar->getId(),
                                      'valor'            => $objInfoDetalleSolicitud->getId()));

            if (!is_object($objInfoTareaCaracteristica))
            {
                throw new \Exception("Error : No existe la tarea de retiro de equipo.");
            }
            if(!$booleanSolRetiroAutomatico)
            {
                $objInfoTareaCaracteristica->setEstado("Finalizada");
                $objInfoTareaCaracteristica->setFeModificacion(new \DateTime('now'));
                $objInfoTareaCaracteristica->setUsrModificacion($strUsuario);
                $objInfoTareaCaracteristica->setIpModificacion($strIpUsuario);
                $this->emSoporte->persist($objInfoTareaCaracteristica);
                $this->emSoporte->flush();
            }

            //Si el retiro no es por cambio de equipo, se procede a realizar la carga y descarga del elemento.
            if (!$boolEsPorCambioEquipo)
            {
                //Eliminación del elemento.
                $arrayParametrosEliminar = array();
                $arrayParametrosEliminar['boolPerteneceElementoNodo'] =  true;
                $arrayParametrosEliminar['intIdElemento']             =  $objInfoElementoRetirar->getId();
                $arrayParametrosEliminar['strObservacion']            = 'Eliminación por retiro de elemento';
                $arrayParametrosEliminar['strUsuario']                =  $strUsuario;
                $arrayParametrosEliminar['strIpUsuario']              =  $strIpUsuario;
                $arrayResEliminar = $this->eliminarElementoClienteNodo($arrayParametrosEliminar);
                if (!$arrayResEliminar['status'])
                {
                    throw new \Exception("Error : ".$arrayResEliminar['message']);
                }

                $strObservacionHistEle = "Se realizo un retiro de elemento: "
                                        ." - Nombre: ".$objInfoElementoRetirar->getNombreElemento()
                                        ." - Serie:  ".$objInfoElementoRetirar->getSerieFisica()
                                        ." - Modelo: ".$objInfoElementoRetirar->getModeloElementoId()->getNombreModeloElemento();

                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objInfoElementoNodo);
                $objHistorialElemento->setEstadoElemento($objInfoElementoNodo->getEstado());
                $objHistorialElemento->setObservacion($strObservacionHistEle);
                $objHistorialElemento->setUsrCreacion($strUsuario);
                $objHistorialElemento->setIpCreacion($strIpUsuario);
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $this->emInfraestructura->persist($objHistorialElemento);
                $this->emInfraestructura->flush();

                //Descarga Cliente y Carga Empleado.
                $arrayControlCustosdio = $this->emNaf->getRepository('schemaBundle:InfoDetalleMaterial')
                        ->obtenerControlCustodio(array('strEstadoEquipo' => 'IN',
                                                       'intIdCustodio'   =>  $objInfoElementoNodo->getId(),
                                                       'strNumeroSerie'  =>  $objInfoElementoRetirar->getSerieFisica(),
                                                       'serviceUtil'     =>  $this->utilService,
                                                       'strTipoParametro'   =>  'CambioElemento'));

                if ($arrayControlCustosdio['status'])
                {
                    $intIdControl   = $arrayControlCustosdio['data'][0]['idControl'];
                    $arrayEquipos[] = array('strNumeroSerie'  =>  $objInfoElementoRetirar->getSerieFisica(),
                                            'intIdControl'    =>  $intIdControl,
                                            'intCantidadEnt'  =>  1,
                                            'intCantidadRec'  =>  1,
                                            'strTipoArticulo' => 'Equipos');

                    $arrayCargaDescarga = array();
                    $arrayCargaDescarga['intNumeroTarea']          =  $objInfoTareaCaracteristica->getTareaId();
                    $arrayCargaDescarga['intIdEmpresa']            =  $strIdEmpresa;
                    $arrayCargaDescarga['strTipoRecibe']           = 'Empleado';
                    $arrayCargaDescarga['intIdEmpleado']           =  $intIdEmpleado;
                    $arrayCargaDescarga['intIdElementoNodo']       =  $intIdElementoNodo;
                    $arrayCargaDescarga['intIdServicio']           =  null;
                    $arrayCargaDescarga['strTipoTransaccion']      = 'Tarea';
                    $arrayCargaDescarga['strTipoActividad']        = 'RetiroNodo';
                    $arrayCargaDescarga['strObservacion']          = 'Retiro de elemento';
                    $arrayCargaDescarga['arrayEquipos']            =  $arrayEquipos;
                    $arrayCargaDescarga['strUsuario']              =  $strUsuario;
                    $arrayCargaDescarga['strIpUsuario']            =  $strIpUsuario;
                    $arrayRestCargaDescarga = $this->cargaDescargaActivos($arrayCargaDescarga);
                    if (!$arrayRestCargaDescarga['status'])
                    {
                        throw new \Exception('Error : '.$arrayRestCargaDescarga['message']);
                    }
                }
            }

            //Se ingresa el tracking del elemento
            $arrayParametrosAuditoria = array();
            $arrayParametrosAuditoria["intOficinaId"]    =  0;
            $arrayParametrosAuditoria["strLogin"]        =  $objInfoElementoNodo->getNombreElemento();
            $arrayParametrosAuditoria["strUsrCreacion"]  =  $strUsuario;
            $arrayParametrosAuditoria["strCodEmpresa"]   =  $strIdEmpresa;
            $arrayParametrosAuditoria["strNumeroSerie"]  =  $objInfoElementoRetirar->getSerieFisica();
            $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
            $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
            $arrayParametrosAuditoria["strEstadoActivo"] = 'Eliminado';
            $arrayParametrosAuditoria["strUbicacion"]    = 'EnTransito';
            $arrayParametrosAuditoria["strTransaccion"]  = 'Retiro Equipo';
            $arrayParametrosAuditoria["strObservacion"]  = 'Retiro Equipo';
            $this->ingresaAuditoriaElementos($arrayParametrosAuditoria);

            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->commit();
            }

            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->commit();
            }

            if ($this->emNaf->getConnection()->isTransactionActive())
            {
                $this->emNaf->getConnection()->commit();
            }

            if ($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->getConnection()->commit();
            }

            $arrayRespuesta = array ('status'        =>  true,
                                     'message'       => 'Proceso Ejecutado',
                                     'idElemento'    =>  $intIdElemento,
                                     'serieElemento' =>  $objInfoElementoRetirar->getSerieFisica());
        }
        catch (\Exception $objException)
        {
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
                $this->emInfraestructura->getConnection()->close();
            }

            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }

            if ($this->emNaf->getConnection()->isTransactionActive())
            {
                $this->emNaf->getConnection()->rollback();
                $this->emNaf->getConnection()->close();
            }

            if ($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->getConnection()->rollback();
                $this->emSoporte->getConnection()->close();
            }

            $strMessage = "Error al retirar el elemento.";
            $strCodigo  = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }

            $this->utilService->insertError('InfoElementoService',
                                            'retirarElementoPerteneceNodo',
                                             $strCodigo.'- 1 -'.$objException->getMessage(),
                                             $strUsuario,
                                             $strIpUsuario);

            $this->utilService->insertError('InfoElementoService',
                                            'retirarElementoPerteneceNodo',
                                             $strCodigo.'- 2 -'.json_encode($arrayParametros),
                                             $strUsuario,
                                             $strIpUsuario);

            $arrayRespuesta = array ('status'        =>  false,
                                     'message'       =>  $strMessage,
                                     'idElemento'    =>  $intIdElemento,
                                     'serieElemento' =>  null);
        }
        return  $arrayRespuesta;
    }

    /**
     * Documentación para el método 'actualizarCaracteristicasOlt'.
     *
     * Función que sirve para la actualización de las características del Olt por solicitud de proceso masivo
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 26-04-2021
     *
     * @param Array $arrayParametros [
     *          'serviceServicioTecnico' - service Servicio Tecnico
     *          'objElementoOlt'     - elemento
     *          'strIdEmpresa'       - id de empresa
     *          'strUsrSesion'       - usuario creación
     *          'strIpClient'        - ip cliente
     *      ]
     *
     * @return Array $arrayDatos [
     *          'status'              - estado de la operación
     *          'mensaje'             - mensaje de la operación
     *      ]
     * @author Alberto Arias <frias@telconet.ec>
     * @version 1.1 30-03-2022 Se modificaron los parametros de envío para la ejecución del metodo getActualizarCaracteristicasOlt
     */
    public function actualizarCaracteristicasOlt($arrayParametros)
    {
        $serviceServicioTecnico = $arrayParametros['serviceServicioTecnico'];
        $objElementoOlt = $arrayParametros['objElementoOlt'];
        $strIdEmpresa   = $arrayParametros['strIdEmpresa'];
        $strUsrSesion   = $arrayParametros['strUsrSesion'];
        $strIpClient    = $arrayParametros['strIpClient'];
        $strTipoProceso = "";
        $intPuntoId     = "";

        $this->emInfraestructura->getConnection()->beginTransaction();
        try
        {
              $strMensaje = "Se actualizaron las características del Olt correctamente";
 
            //historial elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objElementoOlt);
            $objHistorialElemento->setEstadoElemento($objElementoOlt->getEstado());
            $objHistorialElemento->setObservacion($strMensaje);
            $objHistorialElemento->setUsrCreacion($strUsrSesion);
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setIpCreacion($strIpClient);
            $this->emInfraestructura->persist($objHistorialElemento);
            $this->emInfraestructura->flush();

            //actualizar características
            $arrayParametrosCaract = array(
                "objElementoOlt"             => $objElementoOlt,
                "usrCreacion"                => $strUsrSesion,
                "ipCreacion"                 => $strIpClient, 
            );
            $arrayResult = $serviceServicioTecnico->getActualizarCaracteristicasOlt($arrayParametrosCaract);
            if($arrayResult["status"] !== "OK")
            {
                throw new \Exception($arrayResult["mensaje"]);
            }
            $arrayRespuesta = array(
                "status" => "OK",
                "mensaje" => $strMensaje
            );

            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->commit();
                $this->emInfraestructura->getConnection()->close();
            }
        }
        catch (\Exception $ex)
        {
            $arrayRespuesta = array(
                "status" => "ERROR",
                "mensaje" => $ex->getMessage()
            );
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
                $this->emInfraestructura->getConnection()->close();
            }
            $this->utilService->insertError('Telcos+',
                                            'InfoElementoService.actualizarCaracteristicasOlt',
                                            $ex->getMessage(),
                                            $strUsrSesion,
                                            $strIpClient);
        }
        return $arrayRespuesta;
    }

    /**
     * Documentación para el método 'desconfigurarIpCNR'.
     *
     * Función que sirve para cambiar el estado de la IP en 'Eliminado'
     *
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.0 06-10-2021
     *
     * @param Array $arrayParametros [
     *          'nombre_cliente'        nombre del cliente
     *          'login'                 login del cliente
     *          'identificacion'        identificación del cliente
     *          'serial_ont'            serial del ONT
     *          'mac_ont'               MAC del ONT
     *          'ip'                    IP del servicio
     *          'estado_servicio'       Estado del servicio
     *          'scope'                 Scope del servicio
     *          'opcion'                parametro a ejecutar
     *          'ejecutaComando'        SI
     *          'empresa'               MD
     *          'comandoConfiguracion'  SI
     *          'intIdIp' -             id de la IP a eliminar
     *          'usrCreacion'       -   usuario creación
     *          'ipCreacion'        -   ip cliente
     *      ]
     *
     * @return Array $arrayDatos [
     *          'status'              - estado de la operación
     *          'mensaje'             - mensaje de la operación
     *      ]
     */
    public function desconfigurarIpCNR($arrayParametros)
    {
        $strUsrSesion         = $arrayParametros['usrCreacion'];
        $strIpClient          = $arrayParametros['ipCreacion'];
        $intIdIp              = $arrayParametros['intIdIp'];
        $serviceMiddleware    = $this->container->get('tecnico.RedAccesoMiddleware');
        unset($arrayParametros['intIdIp']);
        $this->emInfraestructura->getConnection()->beginTransaction();
        try
        {
          $arrayEliminarMacIp = $serviceMiddleware->middleware(json_encode($arrayParametros));
          if( $arrayEliminarMacIp['status'] == 'OK' )
          {
              $arrayRespuesta = $arrayEliminarMacIp;
              //obtener ip fija para asignarle el estado "Eliminado"
              $objIpFija = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
              ->findOneBy(array("id" => $intIdIp));

              if(is_object($objIpFija) && $objIpFija->getEstado() != "Eliminado")
              {
                $objIpFija->setEstado("Eliminado");
                $this->emInfraestructura->persist($objIpFija);
                $this->emInfraestructura->flush();
              }
              else
              {
                $arrayRespuesta["mensaje"] .= "<br /><b>En TELCOS no se cambió el estado de la IP: <u>". $objIpFija->getEstado()." </u></b>";
              }
          }
          else
          {
              throw new \Exception($arrayEliminarMacIp['mensaje']);
          }
          if($this->emInfraestructura->getConnection()->isTransactionActive())
          {
              $this->emInfraestructura->getConnection()->commit();
              $this->emInfraestructura->getConnection()->close();
          }
        }
        catch (\Exception $ex)
        {
            $arrayRespuesta = array(
                "status" => "ERROR",
                "mensaje" => $ex->getMessage()
            );
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
                $this->emInfraestructura->getConnection()->close();
            }
            $this->utilService->insertError('Telcos+',
                                            'InfoElementoService.desconfigurarIpCNR',
                                            $ex->getMessage(),
                                            $strUsrSesion,
                                            $strIpClient);
        }
        return $arrayRespuesta;
    }

    /**
     * Documentación para el método 'desconfigurarIpCNR'.
     *
     * Función que sirve para cambiar el estado de la IP en 'Eliminado'
     *
     * @author Daniel Reyes <dreyes@telconet.ec>
     * @version 1.0 -10-2021
     *
     * @param Array $arrayParametros [
     *          'strNombreOlt'   - Nombre del Olt
     *          'strIpOlt'       - Ip asignada al Olt
     *          'strModeloOlt'   - Modelo del Olt
     *          'strUsrSesion'   - Usuario de creacion
     *          'strIpClient'    - Ip de creacion
     *          'intIdEmpresa'   - Codigo de la empresa
     *          'strTipoEmpresa' - Prefijo de la empresa
     *          'strTecnologia'  - Tipo de tecnologia del Olt
     *          'intIdParroquia' - Id de la parroquia
     *      ]
     *
     * @return Array $arrayDatos [
     *          'status'  - estado de la operación
     *          'mensaje' - mensaje de la operación
     *      ]
     */
    public function validarJurisdiccionElementoOlt($arrayParametros)
    {
        $strTienePromo     = 'SI';
        $strNombreOlt      = $arrayParametros['strNombreOlt'];
        $strIpOlt          = $arrayParametros['strIpOlt'];
        $strModeloOlt      = $arrayParametros['strModeloOlt'];
        $strUsrSesion      = $arrayParametros['strUsrSesion'];
        $strIpClient       = $arrayParametros['strIpClient'];
        $intIdEmpresa      = $arrayParametros['intIdEmpresa'];
        $strTipoEmpresa    = $arrayParametros['strTipoEmpresa'];
        $strTecnologia     = $arrayParametros['strTecnologia'];
        $intIdParroquia    = $arrayParametros['intIdParroquia'];
        $strEjecutaComando = $this->rdaEjecutaComando;
        $strEjecutaConfig  = $this->rdaEjecutaConfiguracion;
        $serviceMiddleware = $this->container->get('tecnico.RedAccesoMiddleware');
        try
        {
            $arrayParamJurisdiccion = array(
                "strTipoPromocion" => "PROM_BW",
                "strIdParroquia"   => $intIdParroquia,
                "strEstado"        => "Activo",
                "strTipoBusqueda"  => "PARROQUIA"
            );
            $arrayPromocionVigente = $this->emcom->getRepository('schemaBundle:AdmiTipoPromocionRegla')
                                            ->getValidaPromosActivasOlt($arrayParamJurisdiccion);
            
            if(empty($arrayPromocionVigente))
            {
                $arrayParamJurisdiccion['strTipoBusqueda'] = 'CANTON';
                $arrayPromocionVigente = $this->emcom->getRepository('schemaBundle:AdmiTipoPromocionRegla')
                                            ->getValidaPromosActivasOlt($arrayParamJurisdiccion);
                if(empty($arrayPromocionVigente))
                {
                    $arrayParamJurisdiccion['strTipoBusqueda'] = 'JURISDICCION';
                    $arrayPromocionVigente = $this->emcom->getRepository('schemaBundle:AdmiTipoPromocionRegla')
                                                ->getValidaPromosActivasOlt($arrayParamJurisdiccion);
                    if(empty($arrayPromocionVigente))
                    {
                        $strTienePromo = 'NO';
                    }
                }                              
            }

            if($strTienePromo == 'SI')
            {                
                // Datos del OLT
                $arrayOlt = array(
                    "nombre_olt" => $strNombreOlt,
                    "ip_olt"     => $strIpOlt,
                    "modelo_olt" => $strModeloOlt
                );
                $arrayOlts = array($arrayOlt);

                //Concatenamos toda la informacion para el middleware
                foreach( $arrayPromocionVigente as $objPromoVigente)
                {
                    // Obtenemos los planes con su lineProfile de la promocion
                    $arrayParametrosPlanes = array(
                        "intIdPromocion" => $objPromoVigente['idPromocion'],
                        "intIdEmpresa"   => $intIdEmpresa,
                        "strEstado"      => "Activo"
                    );
                    $arrayPlanesPromocion = $this->emcom->getRepository('schemaBundle:AdmiTipoPlanProdPromocion')
                                                    ->getLinesProfilePromociones($arrayParametrosPlanes);
                    $arrayPlanes = array();
                    if(!empty($arrayPlanesPromocion))
                    {
                        foreach( $arrayPlanesPromocion as $objPlanesPromocion)
                        {
                            $arrayPlanes[] = array("plan" => $objPlanesPromocion['lineAnterior'],
                                                "plan_promo" => $objPlanesPromocion['lineNuevo']);
                        }
                    }

                    //Datos Generales
                    $arrayDatos = array(
                        "plan" => $arrayPlanes,
                        "olt" => $arrayOlts
                    );

                    $arrayParametrosOlt = array();
                    $arrayParametrosOlt['id_promo']     = $objPromoVigente['idPromocion'];
                    $arrayParametrosOlt['nombre_promo'] = $objPromoVigente['nombre'];
                    $arrayParametrosOlt['fecha_inicio'] = $objPromoVigente['fecIni'];
                    $arrayParametrosOlt['fecha_fin']    = $objPromoVigente['fecFin'];
                    $arrayParametrosOlt['hora_inicio']  = $objPromoVigente['horIni'];
                    $arrayParametrosOlt['hora_fin']     = $objPromoVigente['horFin'];
                    $arrayParametrosOlt['promo_diaria'] = 'SI';
                    $arrayParametrosOlt['tecnologia']   = $strTecnologia;

                    $arrayParametrosOlt['datos'] = $arrayDatos;

                    $arrayParametrosOlt['opcion']       = 'NUEVO_OLT_PROMOCIONES';
                    $arrayParametrosOlt['empresa']      = $strTipoEmpresa;
                    $arrayParametrosOlt['usrCreacion']  = $strUsrSesion;
                    $arrayParametrosOlt['ipCreacion']   = $strIpClient;
                    $arrayParametrosOlt['ejecutaComando'] = $strEjecutaComando;
                    $arrayParametrosOlt['comandoConfiguracion'] = $strEjecutaConfig;

                    $arrayActivarOlt = $serviceMiddleware->middleware(json_encode($arrayParametrosOlt));
                    if( $arrayActivarOlt['status'] == 'OK' )
                    {
                        $arrayRespuesta = $arrayActivarOlt;
                    }
                    else
                    {
                        throw new \Exception($arrayActivarOlt['mensaje']);
                    }
                }
            }
        }
        catch (\Exception $ex)
        {
            $arrayRespuesta = array(
                "status" => "ERROR",
                "mensaje" => $ex->getMessage()
            );
            $this->utilService->insertError('Telcos+',
                                            'InfoElementoService.desconfigurarIpCNR',
                                            $ex->getMessage(),
                                            $strUsrSesion,
                                            $strIpClient);
        }
        return $arrayRespuesta;
    }


    /**
     * Documentación para el método 'actualizarNombreElementoNodo'.
     *
     * Función que sirve para actualizar el nombre del un elemento perteneciente al nodo.
     *
     * @author Geovanny Cudco <acudco@telconet.ec>
     * @version 1.0 2023-03-01
     *
     * @param Array $arrayParametros [
     *          'strNombreElemento'     nuevo nombre del elemento
     *          'strSerieElemento'      serie del elemento
     *          'intIdElemento'         id del elemento a actualizar
     *          'strTipoElemento'       tipo del elemento a modificar
     *          'usrCreacion'       -   usuario creación
     *          'ipCreacion'        -   ip cliente
     *      ]
     *
     * @return Array $arrayDatos [
     *          'status'              - estado de la operación
     *          'mensaje'             - mensaje de la operación
     *      ]
     */
    public function actualizarNombreElementoNodo($arrayParametros)
    {        
        $strNombreElemento      = strtoupper($arrayParametros['strNombreElemento']);
        $strSerieElemento       = $arrayParametros['strSerieElemento'];        
        $intIdElemento          = $arrayParametros['intNodoElementoId'];
        $strClase               = $arrayParametros['strClase'];
        $strUserSession         = $arrayParametros['strUserSession'];
        $strIpCreacion          = $arrayParametros['strIpCreacion'];
        $strTipoElemento        = $arrayParametros['strTipoElemento'];

        $this->emcom->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();

        try 
        {
            $objElementoRepetido = $this->emcom->getRepository('schemaBundle:InfoElemento')
                                                      ->findOneBy(array("nombreElemento" => $strNombreElemento)); 

            if(is_object($objElementoRepetido) && !empty($objElementoRepetido))
            {                
                throw new \Exception('Nombre ya existe en otro Elemento, favor revisar!');                       
            }

            $objElemento = $this->emcom->getRepository('schemaBundle:InfoElemento')
                                                                ->findOneById($intIdElemento); 

            if (is_object($objElemento) && !empty($objElemento))
            {                                              
                $objElemento->setNombreElemento($strNombreElemento);               
                $this->emcom->persist($objElemento); 
                $this->emcom->flush();   
                $this->emcom->commit();                        
            }
            else
            {                
                throw new \Exception('No se pudo actualizar el nombre del Elemento, favor revisar!');
            }

            $arrayElementosClase = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedidor')
                                                        ->getElementosConClase(array("strParametro"=> "ELEMENTOS CON CLASE",
                                                                                    "strEstado"   => "Activo"));
            foreach($arrayElementosClase as $elemento)
            {
                if($elemento['tipoElemento']==$strTipoElemento)
                {                    
                    $objDetalleElemento1 = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy(array('elementoId'=> $intIdElemento));
                    if(empty($objDetalleElemento1))
                    {
                        $objDetalleElemento = new InfoDetalleElemento();
                        $objDetalleElemento->setElementoId($intIdElemento);
                        $objDetalleElemento->setDetalleNombre("CLASE");
                        $objDetalleElemento->setDetalleValor($strClase);
                        $objDetalleElemento->setDetalleDescripcion("INSTALACION DE UN ".$strTipoElemento);
                        $objDetalleElemento->setUsrCreacion($strUserSession);
                        $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                        $objDetalleElemento->setIpCreacion($strIpCreacion);
                        $objDetalleElemento->setEstado('Activo');
                        $this->emInfraestructura->persist($objDetalleElemento);
                        $this->emInfraestructura->flush();
                        $this->emInfraestructura->commit();
                    }
                }
            }
                 
            $this->emInfraestructura->getConnection()->close();

            $arrayRespuesta = array(
                "status" => "OK",
                "mensaje" => "Elemento actualizado exitosamente"
            );
        } 
        catch (\Exception $ex) 
        {
            $arrayRespuesta = array(
                "status" => "ERROR",
                "mensaje" => $ex->getMessage()
            );
            
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }

            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
                $this->emInfraestructura->getConnection()->close();
            }

            $this->utilService->insertError('Telcos+',
                                            'InfoElementoService.actualizarNombreElementoNodo',
                                            $ex->getMessage(),
                                            $strUsrSesion,
                                            $strIpClient);        
        }
        return $arrayRespuesta;      
    }
}
