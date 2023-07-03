<?php

namespace telconet\planificacionBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\soporteBundle\Service\SoporteService;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoPunto;
use telconet\schemaBundle\Entity\AdmiParametroCab;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Entity\InfoDetalleHistorial;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;
use telconet\schemaBundle\Entity\InfoDetalleSolPlanif;
use telconet\schemaBundle\Entity\InfoDetalleSolPlanifHist;
use telconet\schemaBundle\Entity\InfoCriterioAfectado;
use telconet\schemaBundle\Entity\InfoParteAfectada;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoDetalleSolCaractInsp;

/**
 * Clase GestionarInspeccionService
 *
 * Clase que se encarga de realizar acciones para las inspecciones
 *
 * @author Andrés Montero H <amontero@telconet.ec>
 * @version 1.0 28-01-2022
 * 
 * 
 */
class GestionarInspeccionService
{

    private $objContainer;
    private $objEmComunicacion;
    private $objEmGeneral;
    private $objEntManComercial;
    private $objTemplating;
    private $objMailer;
    private $objMailerSend;
    private $objEnvioPlantilla;
    private $objServiceEnvioPlantilla;
    private $objServiceEnvioSms;
    private $objServiceUtil;

    /**
     *  Metodo utilizado para setear dependencia
     * 
     * @author Andrés Montero H <amontero@telconet.ec>
     * @version 1.0 19-01-2022
     * 
     */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer) 
    {
        $this->objContainer                 = $objContainer;
        $this->objEmComunicacion            = $objContainer->get('doctrine')->getManager('telconet_comunicacion');
        $this->objEmGeneral                 = $objContainer->get('doctrine')->getManager('telconet_general');
        $this->objEntManComercial               = $objContainer->get('doctrine')->getManager('telconet');
        $this->objTemplating                = $objContainer->get('templating');
        $this->objMailer                    = $objContainer->get('mailer');
        $this->objMailerSend                = $objContainer->getParameter('mailer_send');    
        $this->objEnvioPlantilla            = $objContainer->get('soporte.EnvioPlantilla');
        $this->objServiceUtil               = $objContainer->get('schema.Util');
        $this->objServiceEnvioSms           = $objContainer->get('comunicaciones.SMS');
        $this->objServiceEnvioPlantilla     = $objContainer->get('soporte.EnvioPlantilla');
        $this->objTemplating                = $objContainer->get('templating');
    }

    /**    
     * Documentación para el método 'crearSolicitud'.
     *
     * Descripcion: Función que sirve para crea una solicitud
     * 
     * @author  Andrés Montero H. <amontero@telconet.ec>
     * @version 1.0 30-09-2021     
     * 
     * @param array $arrayParametros[idservicio    (integer)  =>    id del servicio.
     *                               strTipoSolicitud   (string)   =>    Tipo de solicitud.
     *                               strObservacion     (string)   =>    Observación de la solicitud.
     *                               strEstado          (string)   =>    Estado de inicio.
     *                               strUsrCreacion     (string)   =>    Usuario de creación.
     *                               strIpCreacion      (string)   =>    Ip de creación.
     *                               
     * @return array $arrayRespuesta
     */
    public function crearSolicitudInspeccion($arrayParametros)
    {
        $this->objEntManComercial->getConnection()->beginTransaction();

        try
        {
            $objSavedData;
            $strUsrCreacion = $arrayParametros['usrCreacion'];
            $strIpCreacion  = ( isset($arrayParametros['ipCreacion']) && !empty($arrayParametros['ipCreacion']) )
                              ? $arrayParametros['ipCreacion'] : '127.0.0.1';
            $strEmpresaCod  = $arrayParametros["empresaCod"];

            $arraySolicitudes = $arrayParametros['solicitudes'];

            for ($intI = 0; $intI < count($arraySolicitudes);$intI++)
            {

                if (is_array($arraySolicitudes[$intI]))
                {
                    $strObservacion          = $arraySolicitudes[$intI]["observacion"];
                    $strTipoSolicitud        = $arraySolicitudes[$intI]["tipoSolicitud"];
                    $strEstado               = $arraySolicitudes[$intI]["estado"];
                    $arrayCaracteristicas    = $arraySolicitudes[$intI]["caracteristicas"];
                    $strStatus               = 200;
                    $strRespuesta            = "Se creo con éxito la(s) solicitud(es)";

                    $strContieneLogin        = "N";

                    //Valida que el estado no este vacio
                    if (empty($strEstado))
                    {
                        throw new \Exception("Falta ingresar el estado para la solicitud");
                    }
                    if (empty($strUsrCreacion))
                    {
                        throw new \Exception("Falta ingresar el usuario de creación para la solicitud");
                    }
                    if (empty($strObservacion))
                    {
                        throw new \Exception("Falta ingresar la observación para la solicitud");
                    }

                    $objAdmiTipoSolicitud = $this->objEntManComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                            ->findOneBy(array("descripcionSolicitud" => $strTipoSolicitud,
                                                                            "estado"               => "Activo"));
                    if (!is_object($objAdmiTipoSolicitud))
                    {
                        throw new \Exception("No se encontró el tipo de solicitud: ".$strTipoSolicitud);
                    }
                    $strObservacion    = "Se crea la " . $objAdmiTipoSolicitud->getDescripcionSolicitud() . " | Obs: " .$strObservacion;

                    //Validar que estados son los permitidos para crear la solicitud
                    $arrayEstadosPermitidos = $this->objEmGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get(
                                                                    'ESTADOS_PERMITIDOS_GESTIONAR_INSPECCION','PLANIFICACION',
                                                                    '','','crear','','','','', $strEmpresaCod,''
                                                                    );
                    $arrayEstado = array();
                    foreach($arrayEstadosPermitidos as $arrayItem)
                    {
                        $arrayEstado[] = $arrayItem['valor2'];
                    }

                    if (!in_array($strEstado,$arrayEstado))
                    {
                        throw new \Exception("Este estado no es permitido para crear la solicitud");
                    }

                    //Se inserta la solicitud
                    $objInfoDetalleSolicitud = new InfoDetalleSolicitud();
                    $objInfoDetalleSolicitud->setObservacion($strObservacion);
                    $objInfoDetalleSolicitud->setTipoSolicitudId($objAdmiTipoSolicitud);
                    $objInfoDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                    $objInfoDetalleSolicitud->setUsrCreacion($strUsrCreacion);
                    $objInfoDetalleSolicitud->setEstado($strEstado);
                    $this->objEntManComercial->persist($objInfoDetalleSolicitud);
                    
                    $this->objEntManComercial->flush();
                    $objSavedData = $objInfoDetalleSolicitud;
                    //Se inserta el historial por creación de la solicitud.
                    $objInfoDetalleSolHist = new InfoDetalleSolHist();
                    $objInfoDetalleSolHist->setDetalleSolicitudId($objInfoDetalleSolicitud);
                    $objInfoDetalleSolHist->setEstado($objInfoDetalleSolicitud->getEstado());
                    $objInfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $objInfoDetalleSolHist->setUsrCreacion($strUsrCreacion);
                    $objInfoDetalleSolHist->setObservacion($objInfoDetalleSolicitud->getObservacion());
                    if(!empty($strIpCreacion))
                    {
                        $objInfoDetalleSolHist->setIpCreacion($strIpCreacion);
                    }

                    $this->objEntManComercial->persist($objInfoDetalleSolHist);
                    $this->objEntManComercial->flush();

                    //VALIDA INSPECCIONES
                    $arrayCamposObligatoriosL = $this->objEmGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get(
                                                                    'CARACTERISTICAS_OBLIGATORIAS_EN_WS_SOLICITUD_INSPECCION','PLANIFICACION',
                                                                    '','','','','','','', $strEmpresaCod,''
                                                                    );
                    $arrayCamposObligatoriosLogin = array();
                    $arrayCamposObligatoriosGen = array();
                    $arrayCamposObligatoriosLoginDatosCli = array();

                    foreach($arrayCamposObligatoriosL as $arrayItem)
                    {
                        if ($arrayItem['valor3'] == 'VALIDACION_COORDENADAS_CLIENTE')
                        {
                            $arrayCamposObligatoriosLogin[] = $arrayItem['valor1'];
                        }
                        if ($arrayItem['valor3'] == 'VALIDACION_GENERAL')
                        {
                            $arrayCamposObligatoriosGen[] = $arrayItem['valor1'];
                        }
                        if ($arrayItem['valor3'] == 'VALIDACION_DATOS_CLIENTE')
                        {
                            $arrayCamposObligatoriosLoginDatosCli[] = $arrayItem['valor1'];
                        }
                    }

                    $arrayCaracteristicasEncontradas = array();
                    $arrayProductosEncontrados = array();
                    $arrayCheckListEncontrados = array();
                    $arrayNombreProdEncontrados = array();

                    foreach($arrayCaracteristicas as $strKey => $strValor)
                    {
                        if (('LOGIN_INSPECCION' == $strKey) && !empty($strValor))
                        {
                            $strContieneLogin = "S";
                        }
                        if(!empty($strValor))
                        {
                            $arrayCaracteristicasEncontradas[] =   $strKey;

                            if ($strKey == 'PRODUCTO_INSPECCION')
                            {
                                foreach($strValor as $arrayProducto)
                                {
                                    $arrayProductosEncontrados[] = $arrayProducto;
                                    if (!empty($arrayProducto["checklist"]))
                                    {
                                        $arrayCheckListEncontrados[] = $arrayProducto["checklist"];
                                    }
                                    if (!empty($arrayProducto["nombre"]))
                                    {
                                        $arrayNombreProdEncontrados[] = $arrayProducto["nombre"];
                                    }
                                }

                            }
                        }
                    }
                    //VALIDACION DE PARAMETROS GENERALES
                    $strCaracteristicasFaltantes = "";
                    foreach ($arrayCamposObligatoriosGen as $strCaract) 
                    {
                        if (!in_array($strCaract,$arrayCaracteristicasEncontradas))
                        {
                            $strCaracteristicasFaltantes .= $strCaract.', ';
                        }

                        if ($strCaract == "PRODUCTO_INSPECCION")
                        {
                            if (count($arrayProductosEncontrados) != count($arrayCheckListEncontrados))
                            {
                                $strCaracteristicasFaltantes .= 'checklist de producto'.', ';
                            }
                            elseif(count($arrayProductosEncontrados) != count($arrayNombreProdEncontrados))
                            {
                                $strCaracteristicasFaltantes .= 'nombre de producto'.', ';
                            }
                        }
                    }

                    if (!empty($strCaracteristicasFaltantes))
                    {
                        throw new \Exception("Falta enviar: ".$strCaracteristicasFaltantes);
                    }

                    //VALIDACION DE PARAMETROS OBLIGATORIOS DE COORDENADAS DEL LOGIN
                    $strCaracteristicasFaltantes = "";
                    foreach ($arrayCamposObligatoriosLogin as $strCaract) 
                    {
                        if (!in_array($strCaract,$arrayCaracteristicasEncontradas))
                        {
                            $strCaracteristicasFaltantes .= $strCaract.', ';
                        }
                    }

                    if (!empty($strCaracteristicasFaltantes))
                    {
                        throw new \Exception("Falta enviar coordenadas de: ".$strCaracteristicasFaltantes);
                    }

                    //VALIDACION DE PARAMETROS OBLIGATORIOS DE DATOS DEL CLIENTE
                    $strCaracteristicasFaltantes = "";
                    foreach ($arrayCamposObligatoriosLoginDatosCli as $strCaract) 
                    {
                        if (!in_array($strCaract,$arrayCaracteristicasEncontradas))
                        {
                            $strCaracteristicasFaltantes .= $strCaract.', ';
                        }
                    }

                    if (!empty($strCaracteristicasFaltantes))
                    {
                        throw new \Exception("Falta enviar datos de: ".$strCaracteristicasFaltantes);
                    }


                    foreach($arrayCaracteristicas as $strNombrCaracteristica => $strValor)
                    {
                        $objAdmiCaracteristica = $this->objEntManComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                ->findOneBy(array("descripcionCaracteristica"=>$strNombrCaracteristica,
                                                                                    "estado"                   => "Activo"));
                        if (!is_object($objAdmiCaracteristica))
                        {
                            throw new \Exception("No se encontró la característica: ".$strNombrCaracteristica);
                        }
                        if (empty($strValor))
                        {
                            throw new \Exception("Falta ingresar el valor para la característica:".$strNombrCaracteristica);
                        }
                        //VALIDA INGRESO DE LOGIN
                        if ($strNombrCaracteristica == "LOGIN_INSPECCION")
                        {
                            $objInfoPuntoInspeccion = $this->objEntManComercial->getRepository('schemaBundle:InfoPunto')->findOneByLogin($strValor);
                            if (!is_object($objInfoPuntoInspeccion))
                            {
                                throw new \Exception("No se encontró el login: ".$strValor ." de la característica:".$strNombrCaracteristica);
                            }
                        }

                        //VALIDA EL PRODUCTO
                        if ($strNombrCaracteristica == "PRODUCTO_INSPECCION")
                        {
                            $arrayAdmiProducto = array();
                            $arrayListadoProductos = array();

                            foreach($strValor as $arrayProd)
                            {
                                    $arrayParametrosProd = array(
                                        'strEmpresa' => $strEmpresaCod,
                                        'strQuery'   => $arrayProd['nombre'],
                                        'strEstado'  => 'Activo',
                                        'strStart'   => 0,
                                        'strLimit'   => 1,
                                    );
                                    $objAdmiProductoJson = $this->objEntManComercial->getRepository('schemaBundle:AdmiProducto')
                                                                                    ->generarJsonProductosPorEstado($arrayParametrosProd);
                                    $objAdmiProducto      = json_decode($objAdmiProductoJson);

                                    if(is_object($objAdmiProducto))
                                    {
                                        $arrayAdmiProducto = $objAdmiProducto->encontrados; 
                                    }

                                    if (count($arrayAdmiProducto) <= 0)
                                    {
                                        throw new \Exception("No se encontró el producto con la descripción: ".$arrayProd['nombre'] .
                                                                " de la característica:".$strNombrCaracteristica);
                                    }
                                    else
                                    {
                                        $arrayListadoProductos[] = $arrayProd;
                                    }
                            }
                            $strValor = json_encode($arrayListadoProductos);            
                        }


                        $objInfoDetalleSolCaract = new InfoDetalleSolCaractInsp();
                        $objInfoDetalleSolCaract->setCaracteristicaId($objAdmiCaracteristica);
                        $objInfoDetalleSolCaract->setValor($strValor);
                        $objInfoDetalleSolCaract->setDetalleSolicitudId($objInfoDetalleSolicitud);
                        $objInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaract->setUsrCreacion($strUsrCreacion);
                        $objInfoDetalleSolCaract->setEstado("Activo");
                        $this->objEntManComercial->persist($objInfoDetalleSolCaract);
                        $this->objEntManComercial->flush();
                    }
                }
            }
            $this->objEntManComercial->getConnection()->commit();
        }       
        catch (\Exception $e) 
        {
            $strStatus    = 500;
            $strRespuesta = $e->getMessage()." (Error en la solicitud #".($intI+1).")";
            if ($this->objEntManComercial->getConnection()->isTransactionActive())
            {
                $this->objEntManComercial->getConnection()->rollback();
                $this->objEntManComercial->getConnection()->close();
            }
            $this->objServiceUtil->insertError('TELCOS+',
                                                'GestionarInspeccionService.crearSolicitudInspeccion',
                                                $strRespuesta,
                                                $strUsrCreacion,
                                                $strIpCreacion);


        }

        $arrayRespuesta = array("status"  => $strStatus,
                                "idSolicitud" => $objSavedData->getId(),
                                "mensaje" => $strRespuesta);
        return $arrayRespuesta;     
    }


    /**
     * 
     * Función usada para finalizar una inspección
     * 
     * @param array arrayParametros [
     *                                  strObservacion           => observación de la solicitud
     *                                  strIpCreacion             => ip de creación
     *                                  strUsrCreacion            => usuario de creación
     *                                  idDetalle                 => id detalle de la tarea
     *                                  login                     => Objeto del punto
     *                                ]
     * 
     * @return array arrayRespuesta[
     *                                  "status"                => 'OK' o 'ERROR'
     *                                  "mensaje"               => mensaje de la ejecución de la función
     *                              ]
     * @author Andrés Montero Holguin <amontero@telconet.ec>
     * @version 1.0 28-01-2022
     * 
     */
    public function finalizarInspeccion($arrayParametros)
    {
        $objEmComercial             = $this->objEntManComercial;
        $strObservacion             = $arrayParametros["strObservacion"];
        $strIpCreacion              = $arrayParametros["strIpCreacion"];
        $strUsrCreacion             = $arrayParametros["strUsrCreacion"];
        $strLogin                   = $arrayParametros["login"];
        $objDetalle                 = $arrayParametros["objDetalle"];
        $boolMostrarMsjErrorUsr     = true;
        $strAsuntoNotificacion      = "";
        $objEmComercial->getConnection()->beginTransaction();

        try
        {
            //Obtiene la solicitud
            $objSolicitud          = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                             ->findOneById($objDetalle->getDetalleSolicitudId());

            if (is_object($objSolicitud) && $objSolicitud->getTipoSolicitudId()->getDescripcionSolicitud() == "SOLICITUD INSPECCION")
            {

                //Obtiene el id de la tarea
                $intTareaId = $objEmComercial->getRepository('schemaBundle:InfoComunicacion')
                                                           ->getMinimaComunicacionPorDetalleId($objDetalle->getId());

                //Obtiene la solicitud de la planificación
                $arrayInfoDetalleSolPlanif = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolPlanif')
                                                            ->findBy(array('detalleSolicitudId'=>$objSolicitud->getId(),
                                                                           'tareaId'=>$intTareaId));

                $objInfoDetalleSolPlanif = $arrayInfoDetalleSolPlanif[0];

                if (is_object($objInfoDetalleSolPlanif) )
                {
                    //Crea historial en la planificación de inspección con estado Finalizada
                    $objInfoDetalleSolPlanif->setEstado('Finalizada');
                    $objEmComercial->persist($objInfoDetalleSolPlanif);
                    $objEmComercial->flush();

                    $objInfoDetalleSolPlanifHist = new InfoDetalleSolPlanifHist();
                    $objInfoDetalleSolPlanifHist->setDetalleSolPlanifId($objInfoDetalleSolPlanif);
                    $objInfoDetalleSolPlanifHist->setFeCreacion(new \DateTime('now'));
                    $objInfoDetalleSolPlanifHist->setUsrCreacion($strUsrCreacion);
                    $objInfoDetalleSolPlanifHist->setIpCreacion($strIpCreacion);
                    $objInfoDetalleSolPlanifHist->setObservacion($strObservacion);
                    $objInfoDetalleSolPlanifHist->setEstado('Finalizada');
                    $objEmComercial->persist($objInfoDetalleSolPlanifHist);
                    $objEmComercial->flush();

                    $arrayDetalleSolCaract = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                            ->getSolicitudCaractPorTipoCaracteristica($objSolicitud->getId(), 'LOGIN_INSPECCION');

                    $strLogin = "";
                    $arrayInfoPunto = array();
                    $objInfoPunto = null;
                    if(count($arrayDetalleSolCaract) > 0)
                    {
                        $strLogin = $arrayDetalleSolCaract[0]->getValor();
                    }
                    if (!empty($strLogin))
                    {
                        $arrayInfoPunto = $objEmComercial->getRepository('schemaBundle:InfoPunto')->findByLogin($strLogin);
                        
                        if (count($arrayInfoPunto) > 0)
                        {
                            $objInfoPunto    = $arrayInfoPunto[0];
                            $strUsrNotificar = $objInfoPunto->getUsrVendedor();
                        }
                    }
                    else
                    {
                        //Buscar las caracteristicas de la solicitud de inspección para obtener datos del punto
                        $arrayParametrosCaracteristicas['idSolicitud'] = $objSolicitud->getId();
                        $arrayDatosSinPunto = $this->obtenerCaracteristicas($arrayParametrosCaracteristicas);

                        //SI NO SE OBTUVO EL VENDEDOR SE OBTIENE USR_CREACION DE QUIEN CREA LA SOLICITUD
                        if (empty($arrayDatosSinPunto['strUsrVendedor']))
                        {
                            $arrayDatosSinPunto['strUsrVendedor'] = $objSolicitud->getUsrCreacion();
                            $strUsrNotificar                      = $objSolicitud->getUsrCreacion();
                        }
                        else
                        {
                            $strUsrNotificar = $arrayDatosSinPunto['strUsrVendedor'];
                        }
                    }

                    //Obtiene el último historial de la planificación con estado AsignadoTarea
                    $arrayInfoDetalleSolPlanifHist = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolPlanifHist')
                                                                ->findBy(array('detalleSolPlanifId' => $objInfoDetalleSolPlanif->getId(),
                                                                                'estado'            => 'AsignadoTarea'),
                                                                         array('feCreacion' => 'DESC'));

                    $objAdmiCuadrillaUltAsignada = $objEmComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                                  ->find($arrayInfoDetalleSolPlanifHist[0]->getAsignadoId());

                    //------- COMUNICACIONES --- NOTIFICACIONES     
                    $strContenidoCorreo = $this->objTemplating->render( 'planificacionBundle:Coordinar:notificacionInspeccion.html.twig', 
                                                                        array(
                                                                                    'detalleSolicitud'           => $objSolicitud,
                                                                                    'detalleSolicitudPlanif'     => $objInfoDetalleSolPlanif,
                                                                                    'infoPunto'                  => $objInfoPunto,
                                                                                    'infoNoCliente'              => $arrayDatosSinPunto,
                                                                                    'detalleSolicitudPlanifHist' => $objInfoDetalleSolPlanifHist,
                                                                                    'motivo'                     => null,
                                                                                    'admiCuadrilla'              => $objAdmiCuadrillaUltAsignada));

                    $strAsunto  = "Planificacion de Inspeccion Finalizada  #" .$objSolicitud->getId().
                                  " Cuadrilla: ".$objAdmiCuadrillaUltAsignada->getNombreCuadrilla();

                    //DESTINATARIOS....
                    $arrayFormasContacto = $objEmComercial->getRepository('schemaBundle:InfoPersona')
                                                                ->getContactosByLoginPersonaAndFormaContacto($strUsrNotificar,
                                                                                                            'Correo Electronico');
                    $arrayTo              = array();
                    $arrayTo[]            = 'notificaciones_telcos@telconet.ec';
                    if (isset($arrayFormasContacto) && !empty($arrayFormasContacto))
                    {
                        foreach ($arrayFormasContacto as $arrayFormaContacto)
                        {
                            $arrayTo[] = $arrayFormaContacto['valor'];
                        }
                    }
                    $this->objServiceEnvioPlantilla->enviarCorreo($strAsunto, $arrayTo, $strContenidoCorreo);

                    $strStatus  = "OK";
                    $strMensaje = "Se finalizo la inspección con éxito";
                    $objEmComercial->getConnection()->commit();
                }
                else
                {
                    $boolMostrarMsjErrorUsr = true;
                    throw new \Exception("No existe la inspección");
                }
            }
        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = "Error: " . $e->getMessage();
            error_log($strMensaje);
            if($boolMostrarMsjErrorUsr)
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "No se pudo finalizar la solicitud. Comuníquese con el Dep. de Sistemas!";
            }

            if($objEmComercial->getConnection()->isTransactionActive())
            {
                $objEmComercial->rollback();
                $objEmComercial->close();
            }


            $this->objServiceUtil->insertError('TELCOS+',
                                                'GestionarInspeccionService.finalizarInspeccion',
                                                $strMensaje,
                                                $strUsrCreacion,
                                                $strIpCreacion);
        }


        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);

        return $arrayRespuesta;
    }

    /**
     * 
     * Función usada para finalizar una solicitud de inspección
     * 
     * @param array arrayParametros [
     *                                  strObservacion           => observación de finalización de la solicitud de inspección
     *                                  strIpCreacion             => ip de creación
     *                                  strUsrCreacion            => usuario de creación
     *                                  idSolicitud               => id de la solicitud de inspección
     *                                ]
     * 
     * @return array arrayRespuesta[
     *                                  "status"                => 'OK' o 'ERROR'
     *                                  "mensaje"               => mensaje de la ejecución de la función
     *                              ]
     * @author Andrés Montero Holguin <amontero@telconet.ec>
     * @version 1.0 08-02-2022
     * 
     */
    public function finalizarSolicitudInspeccion($arrayParametros)
    {
        $objEmComercial             = $this->objEntManComercial;
        $strObservacion             = $arrayParametros["strObservacion"];
        $strIpCreacion              = $arrayParametros["strIpCreacion"];
        $strUsrCreacion             = $arrayParametros["strUsrCreacion"];
        $intIdSolicitud             = $arrayParametros["idSolicitud"];
        $strEmpresaCod              = $arrayParametros["empresaCod"];
        $boolMostrarMsjErrorUsr     = false;
        $strAsuntoNotificacion      = "";
        $objEmComercial->getConnection()->beginTransaction();
        $strStatus                 = 200;
        $strMensaje                = "Se finalizo con éxito la solicitud de inspección!";
        try
        {
            //Obtiene la solicitud
            $objInfoDetalleSolicitud = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                      ->findOneById($intIdSolicitud);

            if(is_object($objInfoDetalleSolicitud) &&  $objInfoDetalleSolicitud->getEstado() == "Finalizada")
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception("La solicitud ya fue finalizada anteriormente!");
            }

            if (is_object($objInfoDetalleSolicitud) && 
                $objInfoDetalleSolicitud->getTipoSolicitudId()->getDescripcionSolicitud() == "SOLICITUD INSPECCION" )
            {

                //VALIDA INSPECCIONES
                $arrayEstadosNoPermiteFin = $this->objEmGeneral->getRepository('schemaBundle:AdmiParametroDet')
                ->get(
                        'ESTADOS_PERMITIDOS_GESTIONAR_INSPECCION','PLANIFICACION',
                        '','','validarFinalizar','','','','', $strEmpresaCod,''
                        );
                $arrayEstadoNoFinalizar = array();

                foreach($arrayEstadosNoPermiteFin as $arrayItem)
                {
                    $arrayEstadoNoFinalizar[] = $arrayItem['valor2'];
                }

                $arrayInfoDetalleSolPlanif = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolPlanif')
                                                            ->findBy(array('detalleSolicitudId'=>$objInfoDetalleSolicitud->getId()));

                //SI EL ESTADO DE LA INSPECCION SE ENCUENTRA ABIERTA NO PERMITE FINALIZAR
                foreach($arrayInfoDetalleSolPlanif as $objInspeccion)
                {
                    $strEstadoInspeccion = $objInspeccion->getEstado();

                    if (in_array($strEstadoInspeccion,$arrayEstadoNoFinalizar))
                    {
                        $boolMostrarMsjErrorUsr = true;
                        throw new \Exception("No se puede finalizar porque la solicitud contiene inspecciones".
                                                " que aun estan abiertas!");
                    }

                }

                //ACTUALIZA ESTADO DE LA SOLICITUD
                $objInfoDetalleSolicitud->setEstado('Finalizada');
                $objEmComercial->persist($objInfoDetalleSolicitud);
                $objEmComercial->flush();

                //ACTUALIZA ESTADO DE SOLICITUD A Finalizada
                $objInfoDetalleSolHist = new InfoDetalleSolHist();
                $objInfoDetalleSolHist->setDetalleSolicitudId($objInfoDetalleSolicitud);
                $objInfoDetalleSolHist->setObservacion("Se Finaliza la solicitud de inspección con la Obs:".$strObservacion);
                $objInfoDetalleSolHist->setIpCreacion($strIpCreacion);
                $objInfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleSolHist->setUsrCreacion($strUsrCreacion);
                $objInfoDetalleSolHist->setEstado('Finalizada');

                $objEmComercial->persist($objInfoDetalleSolHist);
                $objEmComercial->flush();

                $objEmComercial->getConnection()->commit();
            }
            else
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception("No existe la solicitud de inspección");
            }
        }
        catch (\Exception $objE)
        {
            $strStatus  = 500;
            $strMensaje = "Error: " . $objE->getMessage();
            error_log($strMensaje);
            if($boolMostrarMsjErrorUsr)
            {
                $strMensaje = $objE->getMessage();
            }
            else
            {
                $strMensaje = "No se pudo finalizar la solicitud. Comuníquese con el Dep. de Sistemas!";
            }

            if($objEmComercial->getConnection()->isTransactionActive())
            {
                $objEmComercial->rollback();
            }
            $objEmComercial->close();
            $this->objServiceUtil->insertError('TELCOS+',
                                                'GestionarInspeccionService.finalizarSolicitudInspeccion',
                                                $objE->getMessage(),
                                                $strUsrCreacion,
                                                $strIpCreacion);
        }

        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }

    /**
     * 
     * Función usada para rechazar una solicitud de inspección
     * 
     * @param array arrayParametros [
     *                                  strObservacion           => observación de finalización de la solicitud de inspección
     *                                  strIpCreacion             => ip de creación
     *                                  strUsrCreacion            => usuario de creación
     *                                  idSolicitud               => id de la solicitud de inspección
     *                                ]
     * 
     * @return array arrayRespuesta[
     *                                  "status"                => 'OK' o 'ERROR'
     *                                  "mensaje"               => mensaje de la ejecución de la función
     *                              ]
     * @author Andrés Montero Holguin <amontero@telconet.ec>
     * @version 1.0 08-02-2022
     * 
     */
    public function rechazarSolicitudInspeccion($arrayParametros)
    {
        $objEmComercial             = $this->objEntManComercial;
        $strObservacion             = $arrayParametros["strObservacion"];
        $strIpCreacion              = $arrayParametros["strIpCreacion"];
        $strUsrCreacion             = $arrayParametros["strUsrCreacion"];
        $intIdSolicitud             = $arrayParametros["intIdSolicitud"];
        $intIdMotivo                = $arrayParametros["intIdMotivo"];
        $strEmpresaCod              = $arrayParametros["strEmpresaCod"];
        $boolMostrarMsjErrorUsr     = false;
        $strAsuntoNotificacion      = "";
        $objEmComercial->getConnection()->beginTransaction();
        $strStatus                 = 200;
        $strMensaje                = "Se creo con exito la solicitud!";
        try
        {
            //Obtiene la solicitud
            $objInfoDetalleSolicitud = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($intIdSolicitud);

            if(!is_object($objInfoDetalleSolicitud))
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception("No existe la solicitud!");
            }

            $objAdmiMotivo = $objEmComercial->getRepository('schemaBundle:AdmiMotivo')->findOneById($intIdMotivo);

            if(!is_object($objAdmiMotivo))
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception("No existe el motivo!");
            }


            if($objInfoDetalleSolicitud->getEstado() == "Rechazada")
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception("La solicitud ya fue rechazada anteriormente!");
            }

            if ($objInfoDetalleSolicitud->getTipoSolicitudId()->getDescripcionSolicitud() == "SOLICITUD INSPECCION" )
            {

                //VALIDA INSPECCIONES
                $arrayEstadosNoPermiteFin = $this->objEmGeneral->getRepository('schemaBundle:AdmiParametroDet')
                ->get(
                        'ESTADOS_PERMITIDOS_GESTIONAR_INSPECCION','PLANIFICACION',
                        '','','validarRechazar','','','','', $strEmpresaCod,''
                        );
                $arrayEstadoNoFinalizar = array();

                foreach($arrayEstadosNoPermiteFin as $arrayItem)
                {
                    $arrayEstadoNoFinalizar[] = $arrayItem['valor2'];
                }

                $arrayInfoDetalleSolPlanif = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolPlanif')
                                                            ->findBy(array('detalleSolicitudId'=>$objInfoDetalleSolicitud->getId()));

                //SI EL ESTADO DE LA INSPECCION SE ENCUENTRA ABIERTA NO PERMITE FINALIZAR
                foreach($arrayInfoDetalleSolPlanif as $objInspeccion)
                {
                    $strEstadoInspeccion = $objInspeccion->getEstado();

                    if (in_array($strEstadoInspeccion,$arrayEstadoNoFinalizar))
                    {
                        $boolMostrarMsjErrorUsr = true;
                        throw new \Exception("No se puede rechazar porque la solicitud contiene inspecciones que aun estan en gestión!");
                    }
                }

                //ACTUALIZA ESTADO DE LA SOLICITUD A Rechazada
                $objInfoDetalleSolicitud->setEstado('Rechazada');
                $objEmComercial->persist($objInfoDetalleSolicitud);
                $objEmComercial->flush();

                //ACTUALIZA HISTORIAL DE LA SOLICITUD A Rechazada
                $objInfoDetalleSolHist = new InfoDetalleSolHist();
                $objInfoDetalleSolHist->setDetalleSolicitudId($objInfoDetalleSolicitud);
                $objInfoDetalleSolHist->setObservacion("Se Rechaza la solicitud de inspección con la Obs:".$strObservacion);
                $objInfoDetalleSolHist->setMotivoId($intIdMotivo);
                $objInfoDetalleSolHist->setIpCreacion($strIpCreacion);
                $objInfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleSolHist->setUsrCreacion($strUsrCreacion);
                $objInfoDetalleSolHist->setEstado('Rechazada');

                $objEmComercial->persist($objInfoDetalleSolHist);
                $objEmComercial->flush();

                $objEmComercial->getConnection()->commit();
            }
            else
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception("No existe la solicitud de inspección");
            }
        }
        catch (\Exception $objE)
        {
            $strStatus  = 500;
            $strMensaje = "Error: " . $objE->getMessage();
            error_log($strMensaje);
            if($boolMostrarMsjErrorUsr)
            {
                $strMensaje = $objE->getMessage();
            }
            else
            {
                $strMensaje = "No se pudo rechazar la solicitud. Comuníquese con el Departamento de Sistemas!";
            }

            if($objEmComercial->getConnection()->isTransactionActive())
            {
                $objEmComercial->rollback();
            }
            $objEmComercial->close();
            $this->objServiceUtil->insertError('TELCOS+',
                                                'GestionarInspeccionService.rechazarSolicitudInspeccion',
                                                $objE->getMessage(),
                                                $strUsrCreacion,
                                                $strIpCreacion);
        }

        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }

    /**
     * 
     * Función que permite obtener las caracteristicas de una solicitud
     * 
     * @param array arrayParametros [
     *                                  idSolicitud    => id de la solicitud de inspección
     *                                ]
     * 
     * @return array arrayCaracteristicas[
     *                                  "strLongitud"  => Longitud del punto
     *                                  "strLatitud"   => Latitud del punto
     *                                  "strDireccion"   => Direccion del punto
     *                                  "stNombresContacto"   => Nombre de contacto para inspección
     *                                  "strCorreoContacto"   => Correo de contacto para inspección
     * *                                "strUsrVendedor"   => usuario vendedor
     * *                                "strCiudad"   => Ciudad Inspección
     * *                                "strNombreCliente"   => Nombre del cliente
     *                              ]
     * @author Andrés Montero Holguin <amontero@telconet.ec>
     * @version 1.0 08-02-2022
     * 
     */
    public function obtenerCaracteristicas($arrayParametros)
    {
        $intIdSolicitud = $arrayParametros['idSolicitud'];

        $arrayCaracteristicas['strLongitud'] = "";
        $arrayCaracteristicas['strLatitud'] = "";
        $arrayCaracteristicas['strDireccion'] = "";
        $arrayCaracteristicas['strNombresContacto'] = "";
        $arrayCaracteristicas['strCorreoContacto'] = "";
        $arrayCaracteristicas['strUsrVendedor'] = "";
        $arrayCaracteristicas['strCiudad'] = "";
        $arrayCaracteristicas['strNombreCliente'] = "";
        $arrayCaracteristicas['arrayProductos'] = "";

        //Buscar las caracteristicas de la solicitud de inspección para obtener datos del punto
        $arrayDetalleSolCaract = $this->objEntManComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findBy(
                                                        array(
                                                            "detalleSolicitudId"=> $intIdSolicitud,
                                                            "estado"            => 'Activo'
                                                            )
                                                        );

        if (count($arrayDetalleSolCaract) == 0)
        {
            $arrayDetalleSolCaract = $this->objEntManComercial->getRepository('schemaBundle:InfoDetalleSolCaractInsp')
                                                    ->findBy(
                                                        array(
                                                            "detalleSolicitudId"=> $intIdSolicitud,
                                                            "estado"            => 'Activo'
                                                            )
                                                        );                           
        }

        foreach($arrayDetalleSolCaract as $objDetalleSolCaract)
                {
                    $objAdmiCaracteristica = $this->objEntManComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                        ->findOneBy(array('id'=>$objDetalleSolCaract->getCaracteristicaId()));
                    if ($objAdmiCaracteristica->getDescripcionCaracteristica() == 'LONGITUD_INSPECCION')
                    {
                        $arrayCaracteristicas['strLongitud'] = $objDetalleSolCaract->getValor();
                    }
                    if ($objAdmiCaracteristica->getDescripcionCaracteristica() == 'LATITUD_INSPECCION')
                    {
                        $arrayCaracteristicas['strLatitud'] = $objDetalleSolCaract->getValor();
                    }
                    if ($objAdmiCaracteristica->getDescripcionCaracteristica() == 'DIRECCION_INSPECCION')
                    {
                        $arrayCaracteristicas['strDireccion'] = $objDetalleSolCaract->getValor();
                    }
                    if ($objAdmiCaracteristica->getDescripcionCaracteristica() == 'NOMBRES_CONTACTO_INSPECCION')
                    {
                        $arrayCaracteristicas['strNombresContacto'] = $objDetalleSolCaract->getValor();
                    }
                    if ($objAdmiCaracteristica->getDescripcionCaracteristica() == 'TELEFONO_CONTACTO_INSPECCION')
                    {
                        $arrayCaracteristicas['strTelefonoContacto'] = $objDetalleSolCaract->getValor();
                    }
                    if ($objAdmiCaracteristica->getDescripcionCaracteristica() == 'USR_VENDEDOR_INSPECCION')
                    {
                        $arrayCaracteristicas['strUsrVendedor'] = $objDetalleSolCaract->getValor();
                    }
                    if ($objAdmiCaracteristica->getDescripcionCaracteristica() == 'CIUDAD_INSPECCION')
                    {
                        $arrayCaracteristicas['strCiudad'] = $objDetalleSolCaract->getValor().", ";
                    }
                    if ($objAdmiCaracteristica->getDescripcionCaracteristica() == 'NOMBRE_CLIENTE_INSPECCION')
                    {
                        $arrayCaracteristicas['strNombreCliente'] = $objDetalleSolCaract->getValor();
                    }

                    if ($objAdmiCaracteristica->getDescripcionCaracteristica() == 'PRODUCTO_INSPECCION')
                    {
                        $arrayCaracteristicas['arrayProductos'] = $objDetalleSolCaract->getValor();
                    }
                    
                }  

        
        return $arrayCaracteristicas;
    }
}
