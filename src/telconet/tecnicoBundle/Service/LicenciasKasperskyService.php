<?php

namespace telconet\tecnicoBundle\Service;

use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
/**
 * Clase para invocar a métodos para la activación, supensión, reactivación y cancelación de licencias Kaspersky.
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 25-07-2019
 * 
 * @author Kevin Ortiz <kcortiz@telconet.ec>
 * @version 1.1 22-10-2020 - Se agrego el metodo serviceLicenciasKaspersky 
 */
class LicenciasKasperskyService
{
    private $objContainer;
    private $emComercial;
    private $serviceUtil;
    private $serviceEnvioPlantilla;
    private $serviceLicenciasKaspersky;
    private $serviceLicenciasKasperskyWs;
    
    public function setDependencies(Container $objContainer)
    {
        $this->objContainer                 = $objContainer;
        $this->emComercial                  = $objContainer->get('doctrine')->getManager('telconet');
        $this->emGeneral                    = $objContainer->get('doctrine')->getManager('telconet_general');
        $this->serviceUtil                  = $objContainer->get('schema.Util');
        $this->serviceEnvioPlantilla        = $objContainer->get('soporte.EnvioPlantilla');
        $this->serviceLicenciasKasperskyWs  = $objContainer->get('tecnico.LicenciasKasperskyWs');
        $this->serviceLicenciasKaspersky    = $objContainer->get('tecnico.LicenciasKaspersky');
    }

    /**
     * Función para obtener el valor de la característica asociada al servicio
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 01-08-2019
     * 
     * @author Kevin Ortiz <kcortiz@telconet.ec>
     * @version 1.1 22-10-2020 - $objServicioProdCaract busque caracteristicas en estado Pendiente
     * 
     * @param array $arrayParametros [
     *                                  "objServicio"       => objeto del servicio,
     *                                  "objProducto"       => objeto del producto,
     *                                  "strCaracteristica" => descripción de la característica
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"                => OK o ERROR,
     *                                  "mensaje"               => mensaje de error,
     *                                  "objServicioProdCaract" => objeto con el valor de la característica
     *                                ]
     */
    public function obtenerValorServicioProductoCaracteristica($arrayParametros)
    {
        $strCaracteristica  = $arrayParametros["strCaracteristica"];
        $objServicio        = $arrayParametros["objServicio"];
        try
        {
            $objAdmiCaracteristica  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => $strCaracteristica,
                                                                          "estado"                    => "Activo"
                                                                         )
                                                                   );

            if(is_object($objServicio->getProductoId()))
            {
                $objAdmiProductoCaracteristica  = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                    ->findOneBy(array("productoId"       => $objServicio->getProductoId(),
                                                                                      "caracteristicaId" => $objAdmiCaracteristica->getId(),
                                                                                      "estado"           => "Activo"
                                                                                     )
                                                                                );
            }
            else
            {
                if(isset($arrayParametros["objProducto"]) && is_object($arrayParametros["objProducto"]))
                {
                    $intIdProducto = $arrayParametros["objProducto"]->getId();
                }
                else
                {
                    $objPlanDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                    ->findOneByPlanId($objServicio->getPlanId()->getId());
                    $intIdProducto  = $objPlanDet->getProductoId();
                }
                
                $objAdmiProductoCaracteristica  = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                    ->findOneBy(array("productoId"       => $intIdProducto,
                                                                                      "caracteristicaId" => $objAdmiCaracteristica->getId(),
                                                                                      "estado"           => "Activo"
                                                                                     )
                                                                                );
            }

            if(is_object($objAdmiProductoCaracteristica))
            {
                $objServicioProdCaract  = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                            ->findOneBy(array("productoCaracterisiticaId" => 
                                                                              $objAdmiProductoCaracteristica->getId(),
                                                                              "servicioId"                => $objServicio->getId(),
                                                                              "estado"                    => "Activo"
                                                                             )
                                                                       );
                if(!is_object($objServicioProdCaract))
                {
                    $objServicioProdCaract  = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                ->findOneBy(array("productoCaracterisiticaId" => 
                                                                              $objAdmiProductoCaracteristica->getId(),
                                                                              "servicioId"                => $objServicio->getId(),
                                                                              "estado"                    => "Pendiente"
                                                                             )
                                                                       );
                }
                if(!is_object($objServicioProdCaract))
                {
                    $objServicioProdCaract  = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                ->findOneBy(array("productoCaracterisiticaId" => 
                                                                              $objAdmiProductoCaracteristica->getId(),
                                                                              "servicioId"                => $objServicio->getId(),
                                                                              "estado"                    => "Suspendido"
                                                                             )
                                                                       );
                }
            }
            else
            {
                $objServicioProdCaract = null;
            }
            $strStatus  = "OK";
            $strMensaje = "";
        }
        catch(\Exception $e)
        {
            $strStatus              = "ERROR";
            $strMensaje             = "Problemas al obtener valor de característica.";
            $objServicioProdCaract  = null;
            error_log("error: " . $e->getMessage());
            
        }
        $arrayRespuesta = array("status" => $strStatus, "mensaje" => $strMensaje, "objServicioProdCaract" => $objServicioProdCaract);
        return $arrayRespuesta;
    }
    
    /**
     * Función para actualizar el registro de la característica asociada al servicio
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 01-08-2019
     * 
     * @param array $arrayParametros [
     *                                  "objServicio"       => objeto del servicio,
     *                                  "objProducto"       => objeto del producto,
     *                                  "strCaracteristica" => descripción de la característica,
     *                                  "strEstadoNuevo"    => nuevo estado del objeto,
     *                                  "strValorNuevo"     => nuevo valor del objeto,
     *                                  "strUsrCreacion"    => usuario de creación
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"                => OK o ERROR,
     *                                  "mensaje"               => mensaje para el usuario
     *                                ]
     */
    public function actualizarServicioProductoCaracteristica($arrayParametros)
    {
        try
        {
            $arrayRespuestaGetSpc   = $this->obtenerValorServicioProductoCaracteristica($arrayParametros);
            if($arrayRespuestaGetSpc["status"] === 'OK' && is_object($arrayRespuestaGetSpc["objServicioProdCaract"]))
            {
                $objServicioProdCaract  = $arrayRespuestaGetSpc["objServicioProdCaract"];
                if(isset($arrayParametros['strValorNuevo']) && !empty($arrayParametros['strValorNuevo']))
                {
                    $objServicioProdCaract->setValor($arrayParametros['strValorNuevo']);
                }
                if(isset($arrayParametros['strEstadoNuevo']) && !empty($arrayParametros['strEstadoNuevo']))
                {
                    $objServicioProdCaract->setEstado($arrayParametros['strEstadoNuevo']);
                }
                $objServicioProdCaract->setUsrUltMod($arrayParametros['strUsrCreacion']);
                $objServicioProdCaract->setFeUltMod(new \DateTime('now'));
                $this->emComercial->persist($objServicioProdCaract);
                $this->emComercial->flush();
            }
            $strStatus  = "OK";
            $strMensaje = "Característica actualizada exitosamente";
        }
        catch(\Exception $e)
        {
            error_log("error: " . $e->getMessage());
            $strStatus  = "ERROR";
            $strMensaje = "Problemas al actualizar característica: ".$arrayParametros["strCaracteristica"];
        }
        $arrayRespuesta = array("status" => $strStatus, "mensaje" => $strMensaje);
        return $arrayRespuesta;
    }
    
    /**
     * Función para insertar el registro de la característica asociada al servicio
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 01-08-2019
     * 
     * @param array $arrayParametros [
     *                                  "objServicio"       => objeto del servicio,
     *                                  "objProducto"       => objeto del producto,
     *                                  "strCaracteristica" => descripción de la característica,
     *                                  "strValor"          => valor del objeto a crear,
     *                                  "strUsrCreacion"    => usuario de creación
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"                => OK o ERROR,
     *                                  "mensaje"               => mensaje para el usuario
     *                                ]
     * 
     * @author kevin ortiz <kcortiz@telconet.ec>
     * @version 1.1   15-10-2020 - Se cambio la funcionalidad para que si la caractetristica
     * del servicio incluye suscriber Id se guarde en estado Pendiente.
     * 
     * @author kevin ortiz <kcortiz@telconet.ec>
     * @version 1.2   22-10-2020 - Se agrego el parametro $EstadoAnterior
     * 
     */
    public function guardaServicioProductoCaracteristica($arrayParametros)
    {
        $objServicio        = $arrayParametros["objServicio"];
        $strCaracteristica  = $arrayParametros["strCaracteristica"];
        $strValor           = $arrayParametros["strValor"];
        $strUsrCreacion     = $arrayParametros["strUsrCreacion"];
        $strEstadoAnterior  = $arrayParametros["strEstadoAnterior"] ? $arrayParametros["strEstadoAnterior"] : null;
        $strEstado="Activo";
        try
        {
            $objAdmiCaracteristica  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => $strCaracteristica,
                                                                          "estado"                    => "Activo"
                                                                         )
                                                                   );

            if(is_object($objServicio->getProductoId()))
            {
                $objAdmiProductoCaracteristica  = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                    ->findOneBy(array("productoId"       => $objServicio->getProductoId(),
                                                                                      "caracteristicaId" => $objAdmiCaracteristica->getId(),
                                                                                      "estado"           => "Activo"
                                                                                     )
                                                                               );
            }
            else
            {
                if(isset($arrayParametros["objProducto"]) && is_object($arrayParametros["objProducto"]))
                {
                    $intIdProducto = $arrayParametros["objProducto"]->getId();
                }
                else
                {
                    $objPlanDet  = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                        ->findOneByPlanId($objServicio->getPlanId()->getId());
                    $intIdProducto  = $objPlanDet->getProductoId();
                }
                
                $objAdmiProductoCaracteristica  = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                    ->findOneBy(array("productoId"       => $intIdProducto,
                                                                                      "caracteristicaId" => $objAdmiCaracteristica->getId(),
                                                                                      "estado"           => "Activo"
                                                                                     )
                                                                                );

            }

            if($EstadoAnterior != null && $strCaracteristica === "SUSCRIBER_ID")
            {
                $strEstado = $EstadoAnterior;
            }
            else if($strCaracteristica === "SUSCRIBER_ID")
            {
                $strEstado="Pendiente";
            }

            if(is_object($objAdmiProductoCaracteristica))
            {
                //Guardar informacion de la característica del producto
                $objServicioProdCaract = new InfoServicioProdCaract();
                $objServicioProdCaract->setServicioId($objServicio->getId());
                $objServicioProdCaract->setProductoCaracterisiticaId(
                $objAdmiProductoCaracteristica->getId());
                $objServicioProdCaract->setValor($strValor);
                $objServicioProdCaract->setEstado($strEstado);
                $objServicioProdCaract->setUsrCreacion($strUsrCreacion);
                $objServicioProdCaract->setFeCreacion(new \DateTime('now'));
                $this->emComercial->persist($objServicioProdCaract);
                $this->emComercial->flush();
            }
            $strStatus  = "OK";
            $strMensaje = "Característica Guardada exitosamente";
        }
        catch(\Exception $e)
        {
            error_log("error: " . $e->getMessage());
            $strStatus  = "ERROR";
            $strMensaje = "Problemas al guardar característica: ".$strCaracteristica;
        }
        $arrayRespuesta = array("status" => $strStatus, "mensaje" => $strMensaje);
        return $arrayRespuesta;
    }
    
    /**
     * Función que valida si el servicio I. PROTEGIDO MULTI PAID tiene asociada la tecnología McAfee(ANTERIOR) o Kaspersky(NUEVO)
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 01-08-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 02-09-2019 Se envía por parámetros las variables $strValor1ParamAntivirus y $strValor2LoginesAntivirus, 
     *                          para segmentar la consulta de los parámetros del piloto para procesos individuales y masivos
     * 
     * el valor2 con INDIVIDUAL, para segmentar los logines para el piloto tanto individual como el masivo
     * 
     * @param array $arrayParametros [
     *                                  "intIdPunto"        => id del punto,
     *                                  "strOpcionConsulta" => opción desde la que se realiza la consulta: CREAR_PLAN, CLONAR_PLAN o vacío,
     *                                  "strCodEmpresa"     => código de la empresa
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "strFlujoAntivirus" => NUEVO, ANTERIOR o vacío,
     *                                  "strValorAntivirus" => KASPERSKY o vacío
     *                                ]
     */
    public function validaFlujoAntivirus($arrayParametros) 
    {
        $intIdPunto                 = $arrayParametros["intIdPunto"] ? $arrayParametros["intIdPunto"] : 0;
        $strOpcionConsulta          = $arrayParametros["strOpcionConsulta"] ? $arrayParametros["strOpcionConsulta"] : "";
        $strCodEmpresa              = $arrayParametros["strCodEmpresa"] ? $arrayParametros["strCodEmpresa"] : "";
        $strValor1ParamAntivirus    = $arrayParametros['strValor1ParamAntivirus'] ? $arrayParametros['strValor1ParamAntivirus'] : "NUEVO";
        $strValor2LoginesAntivirus  = $arrayParametros['strValor2LoginesAntivirus'] ? $arrayParametros['strValor2LoginesAntivirus'] : "INDIVIDUAL";
        $strFlujoAntivirus  = "";
        $strValorAntivirus  = "";
        try
        {
            if(!isset($strCodEmpresa) || empty($strCodEmpresa))
            {
                throw new \Exception("No se ha enviado el id de la empresa");
            }
            
            if(intval($strCodEmpresa) === 18)
            {
                $arrayParametroDetAntivirus = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                              ->getOne( 'ANTIVIRUS_PLANES_Y_PRODS_MD',
                                                                        '',
                                                                        '', 
                                                                        '', 
                                                                        $strValor1ParamAntivirus,
                                                                        '',
                                                                        '', 
                                                                        '',
                                                                        '',
                                                                        $strCodEmpresa);

                $strValorAntivirus          = (isset($arrayParametroDetAntivirus["valor2"]) && !empty($arrayParametroDetAntivirus["valor2"]))
                                              ? $arrayParametroDetAntivirus["valor2"] : "";

                if(isset($strOpcionConsulta) && !empty($strOpcionConsulta))
                {
                    if($strOpcionConsulta === "CREAR_PLAN" || $strOpcionConsulta === "CLONAR_PLAN")
                    {
                        $strFlujoAntivirus = "NUEVO";
                    }
                    else
                    {
                        $strFlujoAntivirus = "ANTERIOR";
                    }
                }
                
                if($strFlujoAntivirus !== "NUEVO")
                {
                    if(isset($arrayParametroDetAntivirus["valor6"]) && !empty($arrayParametroDetAntivirus["valor6"]))
                    {
                        if($arrayParametroDetAntivirus["valor6"] === "PILOTO")
                        {
                            if(!empty($intIdPunto))
                            {
                                $objPuntoPilotoKaspersky    = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);
                                if(is_object($objPuntoPilotoKaspersky))
                                {
                                    $strLoginPilotoKaspersky    = $objPuntoPilotoKaspersky->getLogin();
                                    $arrayParamDetPilotoLogin   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                  ->getOne( 'LOGINES_PILOTO_KASPERSKY',
                                                                                            '',
                                                                                            '', 
                                                                                            '', 
                                                                                            $strLoginPilotoKaspersky,
                                                                                            $strValor2LoginesAntivirus,
                                                                                            '', 
                                                                                            '',
                                                                                            '',
                                                                                            $strCodEmpresa);
                                    if(isset($arrayParamDetPilotoLogin) && !empty($arrayParamDetPilotoLogin))
                                    {
                                        $strFlujoAntivirus = "NUEVO";
                                    }
                                    else
                                    {
                                        $strFlujoAntivirus = "ANTERIOR";
                                    }
                                }
                                else
                                {
                                    $strFlujoAntivirus = "ANTERIOR";
                                }
                            }
                            else
                            {
                                $strFlujoAntivirus = "ANTERIOR";
                            }
                        }
                        else if($arrayParametroDetAntivirus["valor6"] === "PRODUCCION")
                        {
                            $strFlujoAntivirus = "NUEVO";
                        }
                        else
                        {
                            $strFlujoAntivirus = "ANTERIOR";
                        }
                    }
                    else
                    {
                        $strFlujoAntivirus = "ANTERIOR";
                    }
                }
            }
        }
        catch (\Exception $e)
        {
            error_log("Error al validar el flujo de antivirus ".$e->getMessage());
        }
        $arrayRespuesta = array("strFlujoAntivirus" => $strFlujoAntivirus,
                                "strValorAntivirus" => $strValorAntivirus);
        return $arrayRespuesta;
    }
    
    /**
     * Función que valida si se puede agregar un servicio adicional I. PROTEGIDO MULTI PAID con tecnología Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 01-08-2019
     * 
     * @author kevin ortiz <kcortiz@telconet.ec>
     * @version 1.1  22-10-2020 - Se agrego  $objServicio & se cambio validacion no se pueden agregar EnVerificacion o In-Corte.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 26-08-2021 Se elimina validación errónea de obtener un servicio con el id del punto que no permite agregar un servicio de
     *                         Internet Protegido
     * 
     * @param array $arrayParametros [
     *                                  "intIdPunto"                    => id del punto,
     *                                  "strCantidadDispositivosIPMP"   => cantidad de dispositivos,
     *                                  "strCodEmpresa"                 => código de la empresa
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"    => OK o ERROR,
     *                                  "mensaje"   => mensaje de error
     *                                ]
     */
    public function validaAgregarIPMP($arrayParametros) 
    {
        $intIdPunto                     = $arrayParametros["intIdPunto"];
        $strCantidadDispositivosIPMP    = $arrayParametros["strCantidadDispositivosIPMP"];
        $strCodEmpresa                  = $arrayParametros["strCodEmpresa"] ? $arrayParametros["strCodEmpresa"] : "18";
        try
        {
            if(!isset($strCodEmpresa) || empty($strCodEmpresa))
            {
                throw new \Exception("No se ha enviado el id de la empresa");
            }
            
            if(!isset($strCantidadDispositivosIPMP) || empty($strCantidadDispositivosIPMP))
            {
                throw new \Exception("No se ha ingresado la cantidad de dispositivos");
            }
            
            $arrayParamDetLicenciasPermitidas = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne(   'ANTIVIRUS_KASPERSKY_LICENCIAS_MD',
                                                                            '',
                                                                            '', 
                                                                            '', 
                                                                            $strCantidadDispositivosIPMP,
                                                                            '',
                                                                            '', 
                                                                            '',
                                                                            '',
                                                                            $strCodEmpresa);
            
            if(!isset($arrayParamDetLicenciasPermitidas) || empty($arrayParamDetLicenciasPermitidas))
            {
                throw new \Exception("La cantidad de dispositivos ingresada no está permitida");
            }
            
            if(!isset($intIdPunto) || empty($intIdPunto))
            {
                throw new \Exception("No se ha enviado el parámetro con el id del punto");
            }
            
            $arrayServiciosMcAfee   = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                        ->getResultadoServiciosMcAfee(array('intIdPunto' => $intIdPunto));
            if(isset($arrayServiciosMcAfee) && !empty($arrayServiciosMcAfee))
            {
                throw new \Exception("El punto tiene servicios McAfee EnVerificacion, Activos o In-Corte, no se pueden agregar ".
                                     "nuevos servicios I. Protegido. Debe realizarse la migración a la nueva tecnología Kaspersky.");
            }
            $strStatus  = "OK";
            $strMensaje = "";
        }
        catch (\Exception $e)
        {
            $strStatus = "ERROR";
            $strMensaje = $e->getMessage();
            error_log("Error al validar el flujo de antivirus ".$e->getMessage());
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }
    
    /**
     * Función que retorna el correo usado para las licencias Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 01-08-2019
     * 
     * @param array $arrayParametros [
     *                                  "intIdPunto"        => id del punto,
     *                                  "strUsrCreacion"    => usuario de creación,
     *                                  "strIpCreacion"     => código de la empresa
     *                                ]
     * 
     * @return string $strCorreoValidado
     */
    public function getCorreoLicencias($arrayParametros)
    {
        $intIdPunto             = $arrayParametros["intIdPunto"];
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];       
        $strIpCreacion          = $arrayParametros['strIpCreacion'];
        $strCorreoValidado      = "";
        try
        {
            $arrayCorreosEnvio = $this->getCorreosSplitLicencias(array( "intIdPunto"        => $intIdPunto,
                                                                        "strUsrCreacion"    => $strUsrCreacion,
                                                                        "strIpCreacion"     => $strIpCreacion));
            if(isset($arrayCorreosEnvio[0]) && !empty($arrayCorreosEnvio[0]))
            {
                $strCorreoValidado = $arrayCorreosEnvio[0];
            }
        }
        catch (\Exception $e)
        {
            error_log("No se ha podido obtener el correo de envío ".$e->getMessage());
        }
        return $strCorreoValidado;
    }
    
    /**
     * Función que devuelve el listado de  correos de un cliente mediante el identificador de uno de sus puntos
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 29-07-2019
     * 
     * @param array  $arrayParametros [
     *                                  "intIdPunto"        => id del punto
     *                                  "strUsrCreacion"    => usuario de creación
     *                                  "strIpCreacion"     => ip de creación
     *                                 ]
     * @return array $arrayCorreosSplit
     * 
     */
    public function getCorreosSplitLicencias($arrayParametros)
    {
        $arrayCorreosSplit      = array();
        $strCorreoARegistrar    = "";
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'] ? $arrayParametros['strUsrCreacion'] : 'telcos';
        $strIpCreacion          = $arrayParametros['strIpCreacion'] ? $arrayParametros['strIpCreacion'] : '127.0.0.1';
        try
        {
            $arrayCorreosEnvio   = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                                     ->getCorreosDatosEnvioMd(array("intIdPunto"  => $arrayParametros['intIdPunto']));
            foreach($arrayCorreosEnvio as $arrayCorreoEnvio)
            {
                if ($arrayCorreoEnvio["intSeparador"] > 0 )
                {
                    $arraySplitComa = explode(',', trim($arrayCorreoEnvio['strCorreo']));
                    foreach($arraySplitComa as $strSplitComa)
                    {
                        $arraySplitPuntoComa = explode(';', trim($strSplitComa));
                        foreach($arraySplitPuntoComa as $strSplitPuntoComa)
                        {
                            $strSplitPuntoComa = trim($strSplitPuntoComa);
                            if(!empty($strSplitPuntoComa) && 
                               false === !filter_var($strSplitPuntoComa, FILTER_VALIDATE_EMAIL))
                            {
                                $arrayCorreosSplit[] = $strSplitPuntoComa;
                            }
                        }
                    }
                }
                else
                {
                    $strCorreoARegistrar = trim($arrayCorreoEnvio["strCorreo"]);
                    if (!empty($strCorreoARegistrar) && false === !filter_var($strCorreoARegistrar, FILTER_VALIDATE_EMAIL))
                    {
                        $arrayCorreosSplit[] = $strCorreoARegistrar;
                    }
                }
            }
        }
        catch (\Exception $e)
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'LicenciasKaspersky->getCorreosSplitLicencias', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
            $arrayCorreosSplit  = array();
        }
        return $arrayCorreosSplit;
    }
    
    /**
     * Función que permite actualizar el correo electrónico de una suscripción activa
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 05-08-2019
     * 
     * @param array $arrayParametros [
     *                                  "intIdServicio"         => id del servicio,
     *                                  "intProductoId"         => id del producto,
     *                                  "strCorreoSuscripcion"  => correo de la suscripción,
     *                                  "strUsrCreacion"        => usuario de creación,
     *                                  "strIpCreacion"         => ip de creación
     *                                  "strCodEmpresa"         => código de la empresa
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"            => OK o ERROR,
     *                                  "mensaje"           => mensaje para el usuario
     *                                ]
     * 
     */
    public function cambiarCorreoEnServicioActivo($arrayParametros)
    {
        $intIdServicio                  = $arrayParametros['intIdServicio'];
        $intIdProducto                  = $arrayParametros['intProductoId'];
        $strUsrCreacion                 = $arrayParametros['strUsrCreacion'];
        $strIpCreacion                  = $arrayParametros['strIpCreacion'];
        $strCodEmpresa                  = $arrayParametros['strCodEmpresa'];
        $strCorreoSuscripcion           = $arrayParametros['strCorreoSuscripcion'];
        $strMostrarError                = "NO";
        $this->emComercial->beginTransaction();
        try
        {
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            if (!is_object($objServicio))
            {
                $strMostrarError = "SI";
                throw new \Exception("No existe información del servicio a procesar.");
            }
            $strEstadoServicioInicial = $objServicio->getEstado();
            if(isset($intIdProducto) && !empty($intIdProducto))
            {
                $objProductoIPMP = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProducto);
                if (!is_object($objProductoIPMP))
                {
                    $strMostrarError = "SI";
                    throw new \Exception("No existe información del producto del servicio a procesar.");
                }
            }
            else
            {
                $objProductoIPMP = null;
            }
            
            
            $arrayParamsGetSpc                      = array("objServicio"   => $objServicio,
                                                            "objProducto"   => $objProductoIPMP);
            $arrayParamsGetSpc["strCaracteristica"] = 'CORREO ELECTRONICO';
            $arrayRespuestaGetSpc                   = $this->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpc);
            if($arrayRespuestaGetSpc["status"] === 'ERROR')
            {
                $strMostrarError = "SI";
                throw new \Exception($arrayRespuestaGetSpc["mensaje"]);
            }
            $objSpcCorreoAnterior   = $arrayRespuestaGetSpc["objServicioProdCaract"];
            if(!is_object($objSpcCorreoAnterior))
            {
                $strMostrarError = "SI";
                throw new \Exception("No existe un correo asociado a este servicio");
            }
            $strCorreoAnterior      = $objSpcCorreoAnterior->getValor();
            
            $strMsjErrorAdicHtml            = "No se pudo actualizar la suscripción al correo ".$strCorreoSuscripcion."<br>";
            
            //Cancelar suscripción de Kaspersky con el correo anterior
            $arrayParamsCancelarLicencias   = array("strProceso"                    => "CANCELACION_ANTIVIRUS",
                                                    "strEscenario"                  => "CANCELACION_X_CAMBIO_CORREO",
                                                    "objServicio"                   => $objServicio,
                                                    "objPunto"                      => $objServicio->getPuntoId(),
                                                    "strCodEmpresa"                 => $strCodEmpresa,
                                                    "objProductoIPMP"               => $objProductoIPMP,
                                                    "strUsrCreacion"                => $strUsrCreacion,
                                                    "strIpCreacion"                 => $strIpCreacion,
                                                    "strMsjErrorAdicHtml"           => $strMsjErrorAdicHtml,
                                                    "strEstadoServicioInicial"      => $strEstadoServicioInicial
                                                    );
            
            $arrayRespuestaCancelarLicencias    = $this->gestionarLicencias($arrayParamsCancelarLicencias);
            $strStatusCancelarLicencias     = $arrayRespuestaCancelarLicencias["status"];
            $strMensajeCancelarLicencias    = $arrayRespuestaCancelarLicencias["mensaje"];
            $arrayRespuestaCancelarWs       = $arrayRespuestaCancelarLicencias["arrayRespuestaWs"];
            
            if($strStatusCancelarLicencias === "ERROR")
            {
                $strMostrarError = "SI";
                throw new \Exception($strMensajeCancelarLicencias);
            }
            else if(isset($arrayRespuestaCancelarWs) && !empty($arrayRespuestaCancelarWs) && $arrayRespuestaCancelarWs["status"] !== "OK")
            {
                $strMostrarError = "SI";
                throw new \Exception($arrayRespuestaCancelarWs["mensaje"]);
            }
            
            //Activar licencias con el nuevo correo
            $arrayParamsGuardarSpc  = array("objServicio"       => $objServicio,
                                            "strUsrCreacion"    => $strUsrCreacion,
                                            "objProducto"       => $objProductoIPMP);
            
            $arrayParamsGuardarSpc["strCaracteristica"] = "CORREO ELECTRONICO";
            $arrayParamsGuardarSpc["strValor"]          = $strCorreoSuscripcion;
            $arrayRespuestaGuardarSpc                   = $this->guardaServicioProductoCaracteristica($arrayParamsGuardarSpc);
            if($arrayRespuestaGuardarSpc["status"] == 'ERROR')
            {
                $strMostrarError = "SI";
                throw new \Exception($arrayRespuestaGuardarSpc["mensaje"]);
            }
            
            $arrayParamsActivarLicencias   = array("strProceso"                 => "ACTIVACION_ANTIVIRUS",
                                                    "strEscenario"              => "ACTIVACION_X_CAMBIO_CORREO",
                                                    "objServicio"               => $objServicio,
                                                    "objPunto"                  => $objServicio->getPuntoId(),
                                                    "strCodEmpresa"             => $strCodEmpresa,
                                                    "objProductoIPMP"           => $objProductoIPMP,
                                                    "strUsrCreacion"            => $strUsrCreacion,
                                                    "strIpCreacion"             => $strIpCreacion,
                                                    "strMsjErrorAdicHtml"       => $strMsjErrorAdicHtml,
                                                    "strEstadoServicioInicial"  => $strEstadoServicioInicial
                                                    );
            
            $arrayRespuestaActivarLicencias = $this->gestionarLicencias($arrayParamsActivarLicencias);
            $strStatusActivarLicencias      = $arrayRespuestaActivarLicencias["status"];
            $strMensajeActivarLicencias     = $arrayRespuestaActivarLicencias["mensaje"];
            $arrayRespuestaActivarWs        = $arrayRespuestaActivarLicencias["arrayRespuestaWs"];
            
            if($strStatusActivarLicencias === "ERROR")
            {
                if($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->rollback();
                }

                $this->emComercial->beginTransaction();
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion($strMsjErrorAdicHtml.$strMensajeActivarLicencias);
                $objServicioHistorial->setEstado($strEstadoServicioInicial);
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
                $this->emComercial->commit();
                
                $strMostrarError = "SI";
                throw new \Exception($strMensajeActivarLicencias);
            }
            else
            {
                if(isset($arrayRespuestaActivarWs) && !empty($arrayRespuestaActivarWs) && $arrayRespuestaActivarWs["status"] === "OK")
                {
                    $arrayParamsActualizaSpc                        = array("objServicio"       => $objServicio,
                                                                            "objProducto"       => $objProductoIPMP,
                                                                            "strCaracteristica" => "PERMITE_CANCELACION_LOGICA",
                                                                            "strEstadoNuevo"    => "Eliminado",
                                                                            "strUsrCreacion"    => $strUsrCreacion);
                    $this->actualizarServicioProductoCaracteristica($arrayParamsActualizaSpc);

                    $objServicioHist = new InfoServicioHistorial();
                    $objServicioHist->setServicioId($objServicio);
                    $objServicioHist->setObservacion('Se actualizó el CORREO ELECTRÓNICO de la suscripción: <br>'.
                                                        'Valor Anterior: <br>'.  
                                                        '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  '.$strCorreoAnterior.'<br>'.
                                                        'Valor Actual: <br>'.  
                                                        '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  '.$strCorreoSuscripcion);
                    $objServicioHist->setIpCreacion($strIpCreacion);
                    $objServicioHist->setFeCreacion(new \DateTime('now'));
                    $objServicioHist->setAccion('actualizaCaracteristica');
                    $objServicioHist->setUsrCreacion($strUsrCreacion);
                    $objServicioHist->setEstado($strEstadoServicioInicial);
                    $this->emComercial->persist($objServicioHist);
                    $this->emComercial->flush();
                }
                else
                {
                    $strMostrarError = "SI";
                    throw new \Exception($strMsjErrorAdicHtml.$arrayRespuestaActivarWs["mensajeHtml"]);
                }
            }
            $this->emComercial->commit();
            $strStatus  = "OK";
            $strMensaje = "Se cambió el correo de la suscripción correctamente.";
        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            if ($strMostrarError == "NO")
            {
                $strMensaje = "No se procesó la actualización del correo de la suscripción, favor comunicarse con Sistemas.";
            }
            else
            {
                $strMensaje = $e->getMessage();
            }
            
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            $this->serviceUtil->insertError('Telcos+', 
                                            'LicenciasKaspersky->cambiarCorreoEnServicioActivo', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion);
        }
        
        $arrayRespuesta = array("status"     => $strStatus,
                                "mensaje"    => $strMensaje);
        return $arrayRespuesta;
        
    }
    
    /**
     * Función que permite actualizar el correo electrónico de una suscripción activa licencia kaspersky
     * 
     * @author kevin ortiz <kcortiz@telconet.ec>
     * @version 1.0 22-10-2020
     * 
     * @param array $arrayParametros [
     *                                  "intIdServicio"              => id del servicio,
     *                                  "intProductoId"              => id del producto,
     *                                  "strUsrCreacion"             => usuario de creación,
     *                                  "strIpCreacion"              => ip de creación
     *                                  "strCodEmpresa"              => código de la empresa
     *                                  "strCorreoSuscripcionNuevo"  => correo Nuevo para la Actulizacion,
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"            => OK o ERROR,
     *                                  "mensaje"           => mensaje para el usuario
     *                                ]
     * 
     */
    public function actualizacionCorreo($arrayParametros)
    {
        $intIdServicio                  = $arrayParametros['intIdServicio'];
        $intIdProducto                  = $arrayParametros['intProductoId'];
        $strUsrCreacion                 = $arrayParametros['strUsrCreacion'];
        $strIpCreacion                  = $arrayParametros['strIpCreacion'];
        $strCodEmpresa                  = $arrayParametros['strCodEmpresa'];
        $strCorreoSuscripcionNuevo      = $arrayParametros['strCorreoSuscripcionNuevo'];
        $strMostrarError                = "NO";
        $this->emComercial->beginTransaction();
        try
        {
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            if (!is_object($objServicio))
            {
                $strMostrarError = "SI";
                throw new \Exception("No existe información del servicio a procesar.");
            }
            $strEstadoServicioInicial = $objServicio->getEstado();
            if(isset($intIdProducto) && !empty($intIdProducto))
            {
                $objProductoIPMP = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProducto);
                if (!is_object($objProductoIPMP))
                {
                    $strMostrarError = "SI";
                    throw new \Exception("No existe información del producto del servicio a procesar.");
                }
            }
            else
            {
                $objProductoIPMP = null;
            }
            
           
           
            $arrayParamsGetSpc                      = array("objServicio"   => $objServicio,
                                                            "objProducto"   => $objProductoIPMP);
            $arrayParamsGetSpc["strCaracteristica"] = 'CORREO ELECTRONICO';
            $arrayRespuestaGetSpc                   = $this->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpc);
            if($arrayRespuestaGetSpc["status"] === 'ERROR')
            {
                $strMostrarError = "SI";
                throw new \Exception($arrayRespuestaGetSpc["mensaje"]);
            }
            $objSpcCorreoAnterior   = $arrayRespuestaGetSpc["objServicioProdCaract"];
            if(!is_object($objSpcCorreoAnterior))
            {
                $strMostrarError = "SI";
                throw new \Exception("No existe un correo asociado a este servicio");
            }

             $strCorreoAnterior  = $objSpcCorreoAnterior->getValor(); 
            
             if($objServicio->getProductoId() != null)
            {
                $strEscenario = "ACTUALIZACION_CORREO_PROD_ADICIONAL";
            } 
            else
            {
                $strEscenario = "ACTUALIZACION_CORREO_PROD_PLAN";
            } 

            
            $arrayParamsCambioCorreo        = array("strProceso"                    => "CAMBIAR_CORREO",
                                                    "strEscenario"                  => $strEscenario, 
                                                    "objServicio"                   => $objServicio,
                                                    "objPunto"                      => $objServicio->getPuntoId(),
                                                    "strCodEmpresa"                 => $strCodEmpresa,
                                                    "objProductoIPMP"               => $objProductoIPMP,
                                                    "strUsrCreacion"                => $strUsrCreacion,
                                                    "strIpCreacion"                 => $strIpCreacion,
                                                    "strEstadoServicioInicial"      => $strEstadoServicioInicial,
                                                    "strCorreoSuscripcionNuevo"     => $strCorreoSuscripcionNuevo
                                                    );
            
            $arrayRespuestaCambioCorreo         = $this->gestionarLicencias($arrayParamsCambioCorreo);
            $strStatusCambioCorreo              = $arrayRespuestaCambioCorreo["status"];
            $strMensajeCambioCorreo             = $arrayRespuestaCambioCorreo["mensaje"];
          
            
            if($strStatusCambioCorreo === "ERROR")
            {
                $strMostrarError = "SI";
                throw new \Exception($strMensajeCambioCorreo);
            }
            
            if($arrayRespuestaCambioCorreo === "ERROR")
            {
                if($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->rollback();
                }

                $this->emComercial->beginTransaction();
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion($strMsjErrorAdicHtml.$strMensajeActivarLicencias);
                $objServicioHistorial->setEstado($strEstadoServicioInicial);
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
                $this->emComercial->commit();
                
                $strMostrarError = "SI";
                throw new \Exception($strMensajeActivarLicencias);
            }
            else
            {
                if(isset($arrayRespuestaCambioCorreo) && !empty($arrayRespuestaCambioCorreo)
                 && $arrayRespuestaCambioCorreo["status"] === "OK" && $arrayRespuestaCambioCorreo["mensaje"] === "EXITOSO")
                {
                    $arrayParamsActualizaSpc                        = array("objServicio"       => $objServicio,
                                                                            "objProducto"       => $objProductoIPMP,
                                                                            "strCaracteristica" => "CORREO ELECTRONICO",
                                                                            "strEstadoNuevo"    => "Eliminado",
                                                                            "strUsrCreacion"    => $strUsrCreacion);
                    $this->actualizarServicioProductoCaracteristica($arrayParamsActualizaSpc);


                    $arrayParamsGuardar                             = array("objServicio"       => $objServicio,
                                                                            "objProducto"       => $objProductoIPMP,
                                                                            "strCaracteristica" => "CORREO ELECTRONICO",
                                                                            "strValor"          => $strCorreoSuscripcionNuevo,
                                                                            "strUsrCreacion"    => $strUsrCreacion);
                    $this->guardaServicioProductoCaracteristica($arrayParamsGuardar);


                    $objServicioHist = new InfoServicioHistorial();
                    $objServicioHist->setServicioId($objServicio);
                    $objServicioHist->setObservacion('Se actualizó el CORREO ELECTRÓNICO de la suscripción: <br>'.
                                                        'Valor Anterior: <br>'.  
                                                        '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  '.$strCorreoAnterior.'<br>'.
                                                        'Valor Actual: <br>'.  
                                                        '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  '.$strCorreoSuscripcionNuevo);
                    $objServicioHist->setIpCreacion($strIpCreacion);
                    $objServicioHist->setFeCreacion(new \DateTime('now'));
                    $objServicioHist->setAccion('actualizaCaracteristica');
                    $objServicioHist->setUsrCreacion($strUsrCreacion);
                    $objServicioHist->setEstado($strEstadoServicioInicial);
                    $this->emComercial->persist($objServicioHist);
                    $this->emComercial->flush();
                }
                else
                {
                    $strMostrarError = "SI";
                    throw new \Exception($strMsjErrorAdicHtml.$arrayRespuestaActivarWs["mensajeHtml"]);
                }
            }
            $this->emComercial->commit();
            $strStatus  = "OK";
            $strMensaje = "Se cambió el correo de la suscripción correctamente.";
        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            if ($strMostrarError == "NO")
            {
                $strMensaje = "No se procesó la actualización del correo de la suscripción, favor comunicarse con Sistemas.";
            }
            else
            {
                $strMensaje = $e->getMessage();
            }
            
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            $this->serviceUtil->insertError('Telcos+', 
                                            'LicenciasKaspersky->actualizacionCorreo', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion);
        }
        
        $arrayRespuesta = array("status"     => $strStatus,
                                "mensaje"    => $strMensaje);
        return $arrayRespuesta;
        
    }

    /**
     * Función que actualiza los reintentos antes de enviar al web service de activación de Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 05-08-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-08-2019 Se agrega la obtención de la variable strMsjTecnologia en dónde se especifica la tecnología del servicio
     *                          y se modifica función invocada desde el cambio de plan masivo
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 15-08-2020 Se agrega validación para el reintento de un producto I. Protegido Multi Paid creando características necesarias 
     *                         para el reintento pero con valores vacíos, ya que este error es por el explorador usado
     * 
     * @param array $arrayParametros [
     *                                  "objServicio"           => objeto del servicio,
     *                                  "objProducto"           => objeto del producto,
     *                                  "objDetallePlanIPMP"    => objeto del detalle de plan en caso de existir,
     *                                  "strTipoProceso"        => INDIVIDUAL o CAMBIO DE PLAN MASIVO,
     *                                  "strOpcion"             => ACTIVACION o REINTENTO,
     *                                  "strValorAntivirus"     => KASPERSKY,
     *                                  "strCodEmpresa"         => id de la empresa en sesión,
     *                                  "strUsrCreacion"        => usuario de creación,
     *                                  "strClientIp"           => ip del cliente
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"            => OK o ERROR,
     *                                  "mensaje"           => mensaje para el usuario,
     *                                  "eliminaReintentos" => SI o NO
     *                                ]
     * 
     */
    public function actualizarReintentosIPMPPrevio($arrayParametros)
    {
        $strCodEmpresa                      = $arrayParametros["strCodEmpresa"];
        $strUsrCreacion                     = $arrayParametros["strUsrCreacion"];
        $strClientIp                        = $arrayParametros["strClientIp"];
        $strOpcion                          = $arrayParametros["strOpcion"];
        $objServicio                        = $arrayParametros["objServicio"];
        $objProductoIPMP                    = $arrayParametros["objProducto"];
        $strTipoProceso                     = $arrayParametros["strTipoProceso"];
        $objDetallePlanIPMP                 = $arrayParametros["objDetallePlanIPMP"] ? $arrayParametros["objDetallePlanIPMP"] : null;
        $strMsjTecnologia                   = $arrayParametros['strMsjTecnologia'] ? $arrayParametros['strMsjTecnologia'] : "";
        $strValorAntivirus                  = $arrayParametros["strValorAntivirus"];
        $strEliminaReintentos               = "NO";
        $strMostrarError                    = "NO";
        $strObservacionIntentosPermitidos = "";
        $this->emComercial->beginTransaction();
        try
        {
            $arrayNumReintentosIPMP = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->getOne( 'NUMERO_MAX_REINTENTOS_MCAFEE', 
                                                                '', 
                                                                '', 
                                                                '', 
                                                                "PLAN", 
                                                                "", 
                                                                '', 
                                                                "", 
                                                                '', 
                                                                $strCodEmpresa);
            if(!empty($arrayNumReintentosIPMP))
            {
                $intNumReintentosPermitidos = intval($arrayNumReintentosIPMP["valor2"]);
            }
            else
            {
                $intNumReintentosPermitidos = 1;
            }
            
            $arrayParamsGuardarSpc  = array("objServicio"       => $objServicio,
                                            "strUsrCreacion"    => $strUsrCreacion,
                                            "objProducto"       => $objProductoIPMP);
            
            $arrayParamsGetSpc      = array("objServicio"       => $objServicio,
                                            "objProducto"       => $objProductoIPMP);
            
            $arrayParamsGetSpc["strCaracteristica"] = 'CORREO ELECTRONICO';
            $arrayRespuestaGetSpc                   = $this->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpc);
            if($arrayRespuestaGetSpc["status"] === 'ERROR')
            {
                $strMostrarError = "SI";
                throw new \Exception($arrayRespuestaGetSpc["mensaje"]);
            }
            $objSpcCorreoElectronico    = $arrayRespuestaGetSpc["objServicioProdCaract"];
            if($strTipoProceso === "CAMBIO DE PLAN MASIVO" || $strTipoProceso === "CAMBIAR_PLAN" || !is_object($objSpcCorreoElectronico))
            {
                if($strTipoProceso === "INDIVIDUAL" && $strOpcion === "REINTENTO" 
                    && isset($arrayParametros['strTipoServicio']) && !empty($arrayParametros['strTipoServicio']) 
                    && $arrayParametros['strTipoServicio'] === "PRODUCTO")
                {
                    $strValorInicialTieneIntenet = "SI";
                    $strCorreoLicencia = "";
                }
                else
                {
                    $strValorInicialTieneIntenet = "PLAN";
                    $strCorreoLicencia  = $this->getCorreoLicencias(array(  "intIdPunto"        =>  
                                                                            $objServicio->getPuntoId()->getId(),
                                                                            "strUsrCreacion"    => 
                                                                            $strUsrCreacion,
                                                                            "strIpCreacion"     =>
                                                                            $strClientIp
                                                                        ));
                }
                //Guardar tiene internet, correo, cantidad de dispositivos y antivirus
                $arrayParamsGuardarSpc["strCaracteristica"] = "CORREO ELECTRONICO";
                $arrayParamsGuardarSpc["strValor"]          = empty($strCorreoLicencia) ? "SIN CORREO" : $strCorreoLicencia;
                $arrayRespuestaGuardarSpc                   = $this->guardaServicioProductoCaracteristica($arrayParamsGuardarSpc);
                if($arrayRespuestaGuardarSpc["status"] == 'ERROR')
                {
                    $strMostrarError = "SI";
                    throw new \Exception($arrayRespuestaGuardarSpc["mensaje"]);
                }

                $arrayParamsGuardarSpc["strCaracteristica"] = "TIENE INTERNET";
                $arrayParamsGuardarSpc["strValor"]          = $strValorInicialTieneIntenet;
                $arrayRespuestaGuardarSpc                   = $this->guardaServicioProductoCaracteristica($arrayParamsGuardarSpc);
                if($arrayRespuestaGuardarSpc["status"] == 'ERROR')
                {
                    $strMostrarError = "SI";
                    throw new \Exception($arrayRespuestaGuardarSpc["mensaje"]);
                }
                
                $strCantidadDispositivos    = "";
                if(is_object($objDetallePlanIPMP))
                {
                    $arrayCaractsPlanProducto   = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                                    ->getCaracteristicas($objDetallePlanIPMP->getId());

                    foreach($arrayCaractsPlanProducto as $arrayCaractsPlanProducto)
                    {
                        if ($arrayCaractsPlanProducto["nombre"] === "CANTIDAD DISPOSITIVOS")
                        {
                            $strCantidadDispositivos = $arrayCaractsPlanProducto["valor"];
                        }
                    }
                }
                $arrayParamsGuardarSpc["strCaracteristica"] = "CANTIDAD DISPOSITIVOS";
                $arrayParamsGuardarSpc["strValor"]          = $strCantidadDispositivos;
                $arrayRespuestaGuardarSpc                   = $this->guardaServicioProductoCaracteristica($arrayParamsGuardarSpc);
                if($arrayRespuestaGuardarSpc["status"] == 'ERROR')
                {
                    $strMostrarError = "SI";
                    throw new \Exception($arrayRespuestaGuardarSpc["mensaje"]);
                }
                
                $arrayParamsGetSpc["strCaracteristica"] = 'ANTIVIRUS';
                $arrayRespuestaGetSpc                   = $this->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpc);
                if(!is_object($arrayRespuestaGetSpc["objServicioProdCaract"]))
                {
                    $arrayParamsGuardarSpc["strCaracteristica"] = "ANTIVIRUS";
                    $arrayParamsGuardarSpc["strValor"]          = $strValorAntivirus;
                    $arrayRespuestaGuardarSpc                   = $this->guardaServicioProductoCaracteristica($arrayParamsGuardarSpc);
                    if($arrayRespuestaGuardarSpc["status"] == 'ERROR')
                    {
                        $strMostrarError = "SI";
                        throw new \Exception($arrayRespuestaGuardarSpc["mensaje"]);
                    }
                }
                
                if(empty($strCorreoLicencia))
                {
                    if ($strTipoProceso === "CAMBIAR_PLAN" || $strTipoProceso === "CAMBIO DE PLAN MASIVO")
                    {
                        $arrayParamsGuardarSpc["strCaracteristica"] = "NUMERO REINTENTOS";
                        $arrayParamsGuardarSpc["strValor"]          = "0";
                        $arrayRespuestaGuardarSpc                   = $this->guardaServicioProductoCaracteristica($arrayParamsGuardarSpc);
                        if($arrayRespuestaGuardarSpc["status"] == 'ERROR')
                        {
                            $strMostrarError = "SI";
                            throw new \Exception($arrayRespuestaGuardarSpc["mensaje"]);
                        }
                        
                        $strObservacionHistorial = "No se activó el producto ".
                                                   $objProductoIPMP->getDescripcionProducto().$strMsjTecnologia.
                                                   " incluido en el plan<br /><b>No se recuperó ningún correo disponible del cliente</b>";
                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objServicio);
                        $objServicioHistorial->setObservacion($strObservacionHistorial);
                        $objServicioHistorial->setEstado($objServicio->getEstado());
                        $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setIpCreacion($strClientIp);
                        $this->emComercial->persist($objServicioHistorial);
                        $this->emComercial->flush();
                        $this->emComercial->commit();
                    }
                    $strMostrarError = "SI";
                    throw new \Exception("No se recuperó ningún correo del cliente.");
                }

                if($strTipoProceso === "CAMBIO DE PLAN MASIVO" && $objServicio->getEstado() === "In-Corte")
                {
                    $arrayParamsGuardarSpc["strCaracteristica"] = "ACTIVACION POR MASIVO";
                    $arrayParamsGuardarSpc["strValor"]          = "SI";
                    $arrayRespuestaGuardarSpc                   = $this->guardaServicioProductoCaracteristica($arrayParamsGuardarSpc);
                    if($arrayRespuestaGuardarSpc["status"] == 'ERROR')
                    {
                        $strMostrarError = "SI";
                        throw new \Exception($arrayRespuestaGuardarSpc["mensaje"]);
                    }
                }
            }
            
            if($strOpcion === "REINTENTO")
            {
                $arrayParamsGetSpc["strCaracteristica"] = 'NUMERO REINTENTOS';
                $arrayRespuestaGetSpc                   = $this->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpc);
                if($arrayRespuestaGetSpc["status"] == 'ERROR')
                {
                    $strMostrarError = "SI";
                    throw new \Exception($arrayRespuestaGetSpc["mensaje"]);
                }
                $objSpcNumReintentos    = $arrayRespuestaGetSpc["objServicioProdCaract"];
                if(is_object($objSpcNumReintentos))
                {
                    $strNumReintentoActual = $objSpcNumReintentos->getValor();

                    if(intval($strNumReintentoActual))
                    {
                        $intNumReintentoActual = intval($strNumReintentoActual);
                    }
                    else
                    {
                        $intNumReintentoActual = 0;
                    }
                    $intNuevoNumReintento = $intNumReintentoActual + 1;
                    if($intNuevoNumReintento == $intNumReintentosPermitidos)
                    {
                        $strEliminaReintentos = "SI";
                        $strObservacionIntentosPermitidos .= "<br>Se ha cumplido con el número máximo de reintentos permitidos.";
                        
                    }
                    $objSpcNumReintentos->setValor($intNuevoNumReintento);
                    $objSpcNumReintentos->setUsrUltMod($strUsrCreacion);
                    $objSpcNumReintentos->setFeUltMod(new \DateTime('now'));
                    $this->emComercial->persist($objSpcNumReintentos);
                    $this->emComercial->flush();
                }
                else
                {
                    $arrayParamsGuardarSpc["strCaracteristica"] = "NUMERO REINTENTOS";
                    $arrayParamsGuardarSpc["strValor"]          = "1";
                    $arrayRespuestaGuardarSpc                   = $this->guardaServicioProductoCaracteristica($arrayParamsGuardarSpc);
                    if($arrayRespuestaGuardarSpc["status"] == 'ERROR')
                    {
                        $strMostrarError = "SI";
                        throw new \Exception($arrayRespuestaGuardarSpc["mensaje"]);
                    }
                }
            }
            $strStatus  = "OK";
            $strMensaje = $strObservacionIntentosPermitidos;
            $this->emComercial->flush();
            $this->emComercial->commit();
        } 
        catch (\Exception $e) 
        {
            $strStatus = "ERROR";
            if ($strMostrarError === "SI")
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "Ha ocurrido un error al reintentar la activación. Por favor notificar a Sistemas";
            }
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
            }
            $this->serviceUtil->insertError('Telcos+',
                                            'LicenciasKasperskyService->actualizarReintentosIPMPPrevio',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strClientIp);
        }
        
        $arrayRespuesta = array("status"            => $strStatus,
                                "mensaje"           => $strMensaje,
                                "eliminaReintentos" => $strEliminaReintentos);
        return $arrayRespuesta;
    }
    
    /**
     * Función que actualiza los reintentos después de enviar al web service de activación de Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 05-08-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-08-2019 Se agrega la obtención de la variable strMsjTecnologia en dónde se especifica la tecnología del servicio
     * 
     * @author adorellana <adorellana@telconet.ec>
     * @version 1.2 04/04/2023 Se agrega seteo de accion confirmarServicio por Reintento
     * 
     * @param array $arrayParametros [
     *                                  "objServicio"           => objeto del servicio,
     *                                  "objProducto"           => objeto del producto,
     *                                  "strTipoServicio"       => PLAN o PRODUCTO,
     *                                  "statusWsIPMP"          => status de la respuesta al web service,
     *                                  "eliminaReintentos"     => parámetro que verifica si el servicio ya cumplió con el 
     *                                                             máximo número de reintentos,
     *                                  "obsIntentosPermitidos" => observación respecto a los intentos permitidos,
     *                                  "strOpcion"             => ACTIVACION o REINTENTO,
     *                                  "strUsrCreacion"        => usuario de creación,
     *                                  "strClientIp"           => ip del cliente
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"    => OK o ERROR,
     *                                  "mensaje"   => mensaje para el usuario
     *                                ]
     * 
     */
    public function actualizarReintentosIPMPPosterior($arrayParametros)
    {
        $strUsrCreacion             = $arrayParametros["strUsrCreacion"];
        $strClientIp                = $arrayParametros["strClientIp"];
        $strOpcion                  = $arrayParametros["strOpcion"];
        $objServicio                = $arrayParametros["objServicio"];
        $objProductoIPMP            = $arrayParametros["objProducto"];
        $strStatusWsIPMP            = $arrayParametros["statusWsIPMP"];
        $strEliminaReintentos       = $arrayParametros["eliminaReintentos"];
        $strObsIntentosPermitidos   = $arrayParametros["obsIntentosPermitidos"];
        $strTipoServicio            = $arrayParametros["strTipoServicio"] ? $arrayParametros["strTipoServicio"] : "PLAN" ;
        $strMsjTecnologia           = $arrayParametros['strMsjTecnologia'] ? $arrayParametros['strMsjTecnologia'] : "";
        $boolConfirmReintento       = false;
        $this->emComercial->beginTransaction();
        try
        {
            if($strTipoServicio === "PRODUCTO")
            {
                $strMsjAdicHisto = "activó el servicio ".$objProductoIPMP->getDescripcionProducto().$strMsjTecnologia;
            }
            else
            {
                $strMsjAdicHisto = "activó el producto ".$objProductoIPMP->getDescripcionProducto().$strMsjTecnologia." incluido en el plan";
            }
            
            
            if($strStatusWsIPMP === "OK")
            {
                $strObservacionHistorial    = "Se ".$strMsjAdicHisto;
                $strEliminaReintentos       = "SI";
                
                if($strTipoServicio === "PRODUCTO" && $strOpcion === "REINTENTO")
                {
                    $boolConfirmReintento = true;
                    $objServicio->setEstado("Activo");
                    $this->emComercial->persist($objServicio);
                    $this->emComercial->flush();
                }
            }
            else
            {
                $strObservacionHistorial = "No se ".$strMsjAdicHisto;
                if($strOpcion === "ACTIVACION" || $strOpcion === "ACTIVACION POR MASIVO")
                {
                    $arrayParamsGuardarSpc      = array("objServicio"       => $objServicio,
                                                        "strUsrCreacion"    => $strUsrCreacion,
                                                        "objProducto"       => $objProductoIPMP,
                                                        "strCaracteristica" => "NUMERO REINTENTOS",
                                                        "strValor"          => "0");
                    $arrayRespuestaGuardarSpc   = $this->guardaServicioProductoCaracteristica($arrayParamsGuardarSpc);
                    if($arrayRespuestaGuardarSpc["status"] == 'ERROR')
                    {
                        throw new \Exception($arrayRespuestaGuardarSpc["mensaje"]);
                    }
                }
            }
            $arrayParamsGetSpc  = array("objServicio"       => $objServicio,
                                        "objProducto"       => $objProductoIPMP);
            if($strOpcion === "ACTIVACION POR MASIVO")
            {
                $arrayParamsGetSpc["strCaracteristica"] = "ACTIVACION POR MASIVO";
                $arrayRespuestaGetSpc                   = $this->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpc);
                if($arrayRespuestaGetSpc["status"] === 'ERROR')
                {
                    throw new \Exception($arrayRespuestaGetSpc["mensaje"]);
                }
                $objSpcActivacionPorMasivo  = $arrayRespuestaGetSpc["objServicioProdCaract"];
                if(is_object($objSpcActivacionPorMasivo))
                {
                    $objSpcActivacionPorMasivo->setEstado("Eliminado");
                    $objSpcActivacionPorMasivo->setUsrUltMod($strUsrCreacion);
                    $objSpcActivacionPorMasivo->setFeUltMod(new \DateTime('now'));
                    $this->emComercial->persist($objSpcActivacionPorMasivo);
                    $this->emComercial->flush();
                }
            }
            
            $arrayParamsGetSpc["strCaracteristica"] = "NUMERO REINTENTOS";
            $arrayRespuestaGetSpc                   = $this->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpc);
            if($arrayRespuestaGetSpc["status"] === 'ERROR')
            {
                throw new \Exception($arrayRespuestaGetSpc["mensaje"]);
            }
            $objSpcNumReintentos    = $arrayRespuestaGetSpc["objServicioProdCaract"];
            if(is_object($objSpcNumReintentos))
            {
                if($strOpcion === "REINTENTO")
                {
                    $strObservacionHistorial .= "<br>Reintento #".$objSpcNumReintentos->getValor().$strObsIntentosPermitidos;
                }
                if($strEliminaReintentos === "SI")
                {
                    $objSpcNumReintentos->setEstado("Eliminado");
                    $objSpcNumReintentos->setUsrUltMod($strUsrCreacion);
                    $objSpcNumReintentos->setFeUltMod(new \DateTime('now'));
                    $this->emComercial->persist($objSpcNumReintentos);
                    $this->emComercial->flush();
                }
                else
                {
                    $objPunto               = $objServicio->getPuntoId();
                    $objPersonaEmpresaRol   = $objPunto->getPersonaEmpresaRolId();
                    $objPersona             = $objPersonaEmpresaRol->getPersonaId();
                    $objJurisdiccion        = $objPunto->getPuntoCoberturaId();
                    $strLogin               = $objPunto->getLogin();
                    $strNombreCliente       = sprintf("%s",$objPersona);
                    if(is_object($objJurisdiccion))
                    {
                        $strNombreJurisdiccion  = $objJurisdiccion->getNombreJurisdiccion();
                    }
                    else
                    {
                        $strNombreJurisdiccion  = "";
                    }
                    
                    if(is_object($objServicio->getPlanId()))
                    {
                        $strTipoServicioCorreo      = "Plan";
                        $strNombreServicioCorreo    = $objServicio->getPlanId()->getNombrePlan();
                        $strDescripcionServicio     = "incluido en el plan";
                    }
                    else
                    {
                        $strTipoServicioCorreo      = "Producto";
                        $strNombreServicioCorreo    = $objProductoIPMP->getDescripcionProducto();
                        $strDescripcionServicio     = "como producto adicional";
                    }
                    if(isset($strMsjTecnologia) && !empty($strMsjTecnologia))
                    {
                        $strDescripcionServicio = $strDescripcionServicio.$strMsjTecnologia;
                    }
                    else
                    {
                        $strDescripcionServicio = $strDescripcionServicio." ";
                    }
                    //Se envía notificación indicando que no se ha podido activar producto I. Protegido Multi Paid incluido en el plan
                    $arrayParametrosErrorIPMP   = array( 
                                                        "nombreProducto"        => $objProductoIPMP->getDescripcionProducto(),
                                                        "descripcionServicio"   => $strDescripcionServicio,
                                                        "cliente"               => $strNombreCliente,
                                                        "login"                 => $strLogin,
                                                        "nombreJurisdiccion"    => $strNombreJurisdiccion,
                                                        "tipoServicio"          => $strTipoServicioCorreo,
                                                        "nombreServicio"        => $strNombreServicioCorreo,
                                                        "observacion"           => $strObservacionHistorial,
                                                        "estadoServicio"        => $objServicio->getEstado()
                                                        );
                    $strAsuntoErrorIPMP         = "Error en Activacion de ".$objProductoIPMP->getDescripcionProducto()." - ".$strLogin;
                    try
                    {
                        $this->serviceEnvioPlantilla->generarEnvioPlantilla($strAsuntoErrorIPMP, 
                                                                            array(), 
                                                                            'ERRORACTIVAIPMP', 
                                                                            $arrayParametrosErrorIPMP, 
                                                                            '', 
                                                                            '', 
                                                                            '',
                                                                            null,
                                                                            false,
                                                                            'notificacionesnetlife@netlife.info.ec');
                    }
                    catch (\Exception $e) 
                    {
                        error_log("No se ha podido enviar el correo con código ERRORACTIVAIPMP ".$e->getMessage());
                    }
                    
                }
            }
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion($strObservacionHistorial);
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strClientIp);
            if($boolConfirmReintento)
            {
                $objServicioHistorial->setAccion('confirmarServicio');
            }
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();
            
            $strStatus  = "OK";
            
            if($strStatusWsIPMP != "OK")
            {
                $strObservacionHistorial .= "<br /><b>Por favor revise el historial del servicio.</b>";
            }
            
            $strMensaje = $strObservacionHistorial;
            $this->emComercial->commit();
        } 
        catch (\Exception $e) 
        {
            $strStatus          = "ERROR";
            $strMensaje         = "Ha ocurrido un error al reintentar la activación. Por favor notificar a Sistemas";
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
            }
            $this->serviceUtil->insertError('Telcos+',
                                            'LicenciasKaspersky->actualizarReintentosIPMPPosterior',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strClientIp);
        }
        
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }
    
    /**
     * Función que crea las características técnicas asociadas a un servicio Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 05-08-2019
     * 
     * @param array $arrayParametros [
     *                                  "objServicio"               => objeto del servicio,
     *                                  "objProducto"               => objeto del producto,
     *                                  "arrayDataIPMP"             => información del suscriber id, código del producto y el correo de la suscripción
     *                                  "strValorAntivirus"         => KASPERSKY,
     *                                  "strHabilitaTransaccion"    => SI o NO,
     *                                  "strUsrCreacion"            => usuario de creación,
     *                                  "strClientIp"               => ip del cliente
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"    => OK o ERROR,
     *                                  "mensaje"   => mensaje de error
     *                                ]
     * 
     */
    public function creaSpcActivacionIPMP($arrayParametros)
    {
        $objServicio            = $arrayParametros["objServicio"];
        $objProductoIPMP        = $arrayParametros["objProducto"] ? $arrayParametros["objProducto"] : null;
        $strUsrCreacion         = $arrayParametros["strUsrCreacion"];
        $strClientIp            = $arrayParametros['strClientIp'];
        $arrayDataIPMP          = $arrayParametros["arrayDataIPMP"];
        $strValorAntivirus      = $arrayParametros["strValorAntivirus"];
        $strHabilitaTransaccion = $arrayParametros["strHabilitaTransaccion"] ? $arrayParametros["strHabilitaTransaccion"] : "NO";
        $strMensaje             = "";
        $strMostrarError        = "NO";
        
        if($strHabilitaTransaccion === "SI")
        {
            $this->emComercial->beginTransaction();
        }
        try
        {
            $arrayParamsGuardarSpc                      = array("objServicio"       => $objServicio,
                                                                "strUsrCreacion"    => $strUsrCreacion,
                                                                "objProducto"       => $objProductoIPMP);
            $arrayParamsGuardarSpc["strCaracteristica"] = "SUSCRIBER_ID";
            $arrayParamsGuardarSpc["strValor"]          = $arrayDataIPMP["intSuscriberId"];
            $arrayRespuestaGuardarSpc                   = $this->guardaServicioProductoCaracteristica($arrayParamsGuardarSpc);
            if($arrayRespuestaGuardarSpc["status"] == 'ERROR')
            {
                $strMostrarError = "SI";
                throw new \Exception($arrayRespuestaGuardarSpc["mensaje"]);
            }
            
            $arrayParamsGuardarSpc["strCaracteristica"] = "CODIGO_PRODUCTO";
            $arrayParamsGuardarSpc["strValor"]          = $arrayDataIPMP["strCodigoProducto"];
            $arrayRespuestaGuardarSpc                   = $this->guardaServicioProductoCaracteristica($arrayParamsGuardarSpc);
            if($arrayRespuestaGuardarSpc["status"] == 'ERROR')
            {
                $strMostrarError = "SI";
                throw new \Exception($arrayRespuestaGuardarSpc["mensaje"]);
            }
            
            $arrayParamsGetSpcAntivirus = array("objServicio"       => $objServicio,
                                                "objProducto"       => $objProductoIPMP,
                                                "strCaracteristica" => "ANTIVIRUS");
            $arrayRespuestaSpcAntivirus = $this->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcAntivirus);
            if($arrayRespuestaSpcAntivirus["status"] == 'ERROR')
            {
                $strMostrarError = "SI";
                throw new \Exception($arrayRespuestaSpcAntivirus["mensaje"]);
            }
            $objSpcAntivirus    = $arrayRespuestaSpcAntivirus["objServicioProdCaract"];
            if(!is_object($objSpcAntivirus) && !empty($strValorAntivirus))
            {
                $arrayParamsGuardarSpc["strCaracteristica"] = "ANTIVIRUS";
                $arrayParamsGuardarSpc["strValor"]          = $strValorAntivirus;
                $arrayRespuestaGuardarSpc                   = $this->guardaServicioProductoCaracteristica($arrayParamsGuardarSpc);
                if($arrayRespuestaGuardarSpc["status"] == 'ERROR')
                {
                    $strMostrarError = "SI";
                    throw new \Exception($arrayRespuestaGuardarSpc["mensaje"]);
                }
            }
            $arrayParamsGetSpcSuscriberIdAnterior = array("objServicio" => $objServicio,
            "objProducto"       => $objProductoIPMP,
            "strCaracteristica" => "SUSCRIBER_ID ESTADO ANTERIOR");
            $arrayRespuestaSpcSuscriberIdAnterior = $this->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcSuscriberIdAnterior);
            
            if($arrayRespuestaSpcSuscriberIdAnterior ["objServicioProdCaract"] == null)
            {
                $arrayParamsGuardarSpc["strCaracteristica"] = "SUSCRIBER_ID ESTADO ANTERIOR";
                $arrayParamsGuardarSpc["strValor"]          = "Pendiente";
                $arrayRespuestaGuardarSpc                   = $this->guardaServicioProductoCaracteristica($arrayParamsGuardarSpc);
                if($arrayRespuestaGuardarSpc["status"] == 'ERROR')
                {
                    $strMostrarError = "SI";
                    throw new \Exception($arrayRespuestaGuardarSpc["mensaje"]);
                }
            }
           
            $strStatus = 'OK';
            if($strHabilitaTransaccion === "SI")
            {
                $this->emComercial->commit();
            }
        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            if ($strMostrarError === "SI")
            {
                $strMensaje = $e->getMessage();    
            }
            else
            {
                $strMensaje = "No se ha podido guardar correctamente las características SUSCRIBER_ID, CODIGO_PRODUCTO y ANTIVIRUS";
            }
            
            if($strHabilitaTransaccion === "SI" && $this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
            }
            $this->serviceUtil->insertError('Telcos+',
                                            'LicenciasKaspersky->creaSpcActivacionIPMP',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strClientIp);
        }
        $arrayRespuestaServicio = array("status"    => $strStatus,
                                        "mensaje"   => $strMensaje);
        return $arrayRespuestaServicio;
    }
    
    /**
     * Función que realiza el reintento de una activación de servicios adicionales I. PROTEGIDO MULTI PAID con tecnología Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-08-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-08-2019 Se agrega el envío de la variable strMsjTecnologia en dónde se especifica la tecnología del servicio
     * 
     * @param array $arrayParametros [
     *                                  "intIdServicio"         => id del servicio,
     *                                  "strTipoProceso"        => INDIVIDUAL,
     *                                  "strOpcion"             => REINTENTO
     *                                  "strCodEmpresa"         => código de la empresa,
     *                                  "strUsrCreacion"        => usuario de creación,
     *                                  "strClientIp"           => ip del cliente
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"    => OK o ERROR,
     *                                  "mensaje"   => mensaje de error
     *                                ]
     * 
     */
    public function reintentarActivacion($arrayParametros)
    {
        $strUsrCreacion     = $arrayParametros["strUsrCreacion"];
        $strClientIp        = $arrayParametros["strClientIp"];
        $intIdServicio      = $arrayParametros["intIdServicio"];
        $strCodEmpresa      = $arrayParametros["strCodEmpresa"];
        $strStatus          = "OK";
        
        try
        {
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            if(!is_object($objServicio))
            {
                $arrayRespuesta = array("status" => "ERROR", "mensaje" => "No se ha podido obtener el objeto servicio");
                return $arrayRespuesta;
            }
            $arrayParametros["objServicio"] = $objServicio;
            
            $arrayValidaFlujoAntivirus  = $this->validaFlujoAntivirus(array(
                                                                            "intIdPunto"        => $objServicio->getPuntoId()->getId(),
                                                                            "strCodEmpresa"     => $strCodEmpresa
                                                                       ));
            $strFlujoAntivirus  = $arrayValidaFlujoAntivirus["strFlujoAntivirus"];
            $strValorAntivirus  = $arrayValidaFlujoAntivirus["strValorAntivirus"];
            
            if($strFlujoAntivirus !== "NUEVO")
            {
                $arrayRespuesta = array("status" => "ERROR", "mensaje" => "No se ha podido obtener correctamente el flujo para el reintento");
                return $arrayRespuesta;
            }
            
            $arrayParametros["strValorAntivirus"]       = $strValorAntivirus;
            $arrayParametros["objServicio"]             = $objServicio;
            $arrayParametros["objProducto"]             = $objServicio->getProductoId();
            $arrayParametros["objDetallePlanIPMP"]      = null;
            if(isset($strValorAntivirus) && !empty($strValorAntivirus))
            {
                $arrayParametros['strMsjTecnologia'] = " con tecnología ".$strValorAntivirus;
            }
            else
            {
                $arrayParametros['strMsjTecnologia'] = "";
            }
            $arrayActualizarReintentos                  = $this->actualizarReintentosIPMPPrevio($arrayParametros);
            $strStatusActualizarReintentos              = $arrayActualizarReintentos["status"];
            $strMensajeActualizarReintentos             = $arrayActualizarReintentos["mensaje"];
            $strEliminaReintentos                       = $arrayActualizarReintentos["eliminaReintentos"];
            if($strStatusActualizarReintentos !== "OK")
            {
                throw new \Exception($strMensajeActualizarReintentos);
            }
            if($objServicio->getEstado() !== "In-Corte")
            {
                $arrayParametros["obsIntentosPermitidos"]   = $strMensajeActualizarReintentos;
                $arrayParametros["eliminaReintentos"]       = $strEliminaReintentos;
                $arrayRespuestaWsLicencias                  = $this->serviceLicenciasKasperskyWs->activacionWsProductoIPMP($arrayParametros);
                $arrayParametros["statusWsIPMP"]            = $arrayRespuestaWsLicencias["status"];
                $arrayParametros["arrayDataIPMP"]           = $arrayRespuestaWsLicencias["arrayDataIPMP"];
                $arrayRespuestaReintentos                   = $this->actualizarReintentosIPMPPosterior($arrayParametros);
                $strStatus                                  = $arrayRespuestaReintentos["status"];
                $strMensaje                                 = $arrayRespuestaReintentos["mensaje"];
                if($strStatus !== "OK")
                {
                    throw new \Exception($strMensaje);
                }
                if($arrayRespuestaWsLicencias["status"] === "OK")
                {
                    $arrayParametros["strHabilitaTransaccion"]  = "SI";
                    $arrayRespuestaSpc  = $this->creaSpcActivacionIPMP($arrayParametros);
                    if($arrayRespuestaSpc["status"] === "ERROR")
                    {
                        $strMensaje .= "<br>Las características no se guardaron correctamente.";
                    }
                }
            }
            else
            {
                $strStatus  = $strStatusActualizarReintentos;
                $strMensaje = "";
            }
        } 
        catch (\Exception $e)
        {
            $strStatus          = "ERROR";
            $strMensaje         = "Ha ocurrido un error al reintentar la activación. Por favor notificar a Sistemas";
            $this->serviceUtil->insertError('Telcos+',
                                            'LicenciasKaspersky->reintentarActivacion',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strClientIp);
        }
        $arrayRespuesta = array("status" => $strStatus, "mensaje" => $strMensaje);
        return $arrayRespuesta;
    }

    /**
     * Función que realiza la activación de licencias de servicios I. PROTEGIDO MULTI PAID con tecnología Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 06-07-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-08-2019 Se agrega la obtención de la variable strMsjTecnologia en dónde se especifica la tecnología del servicio
     * 
     * @param array $arrayParametros [
     *                                  "strProceso"                => ACTIVACION_ANTIVIRUS,
     *                                  "strEscenario"              => ACTIVACION_PROD_EN_PLAN, ACTIVACION_PROD_ADICIONAL, 
     *                                                                 ACTIVACION_PROD_ADICIONAL_X_REINTENTO o ACTIVACION_X_CAMBIO_CORREO,
     *                                  "objServicio"               => objeto del servicio,
     *                                  "objPunto"                  => objeto del punto,
     *                                  "objProductoIPMP"           => objeto del producto I.PROTEGIDO MULTI PAID de servicios adicionales,
     *                                  "intIdOficina"              => id de la oficina para generar la orden de trabajo para servicios adicionales,
     *                                  "strEstadoServicioInicial"  => estado actual del servicio al invocar a esta función,
     *                                  "strMsjErrorAdicHtml"       => mensaje informativo de error que se guardará en el historial del servicio,
     *                                  "strCodEmpresa"             => código de la empresa,
     *                                  "strUsrCreacion"            => usuario de creación,
     *                                  "strIpCreacion"             => ip del creación
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"            => OK o ERROR,
     *                                  "mensaje"           => mensaje de error,
     *                                  "objOrdenTrabajo"   => objeto de la orden de trabajo en caso de existir,
     *                                  "arrayRespuestaWs"  => arreglo con la respuesta del web service
     *                                ]
     * 
     */
    public function activarLicencias($arrayParametros)
    {
        $strPassword                = $arrayParametros["strPassword"];
        $strEscenario               = $arrayParametros['strEscenario'];
        $objServicio                = $arrayParametros['objServicio'];
        $objPunto                   = $arrayParametros['objPunto'];
        $strCodEmpresa              = $arrayParametros['strCodEmpresa'];
        $strValorAntivirus          = $arrayParametros['strValorAntivirus'];
        $strCodigoProducto          = $arrayParametros['strCodigoProducto'];
        $strUsrCreacion             = $arrayParametros['strUsrCreacion'] ? $arrayParametros['strUsrCreacion'] : "telcos";
        $strIpCreacion              = $arrayParametros['strIpCreacion'] ? $arrayParametros['strIpCreacion'] : "127.0.0.1";
        $strMsjErrorAdicHtml        = $arrayParametros['strMsjErrorAdicHtml'] ? $arrayParametros['strMsjErrorAdicHtml'] : "";
        $strMsjTecnologia           = $arrayParametros['strMsjTecnologia'] ? $arrayParametros['strMsjTecnologia'] : "";
        $objProductoIPMP            = is_object($arrayParametros["objProductoIPMP"]) ? $arrayParametros["objProductoIPMP"] : null;
        $strEstadoServicioInicial   = $arrayParametros['strEstadoServicioInicial'] ? $arrayParametros['strEstadoServicioInicial'] : "";
        $objOrdenTrabajo            = null;
        $strTipoTransaccion         = "Activacion";
        try
        {
            if(!isset($strEscenario) || empty($strEscenario))
            {
                throw new \Exception("No se han enviado el escenario que se desea ejecutar para las licencias");
            }
            else if(!is_object($objServicio) || !is_object($objPunto)
                || !isset($strCodEmpresa) || empty($strCodEmpresa))
            {
                throw new \Exception("No se ha enviado el servicio, el punto o el id de la empresa");
            }
            else if(!isset($strCodigoProducto) || empty($strCodigoProducto)
                || !isset($strPassword) || empty($strPassword))
            {
                throw new \Exception("No se ha enviado el código del producto o la password para gestionar las licencias");
            }
            
            if($strEscenario === "ACTIVACION_PROD_ADICIONAL")
            {
                if(is_object($arrayParametros["objOrdenTrabajo"]))
                {
                    $objOrdenTrabajo = $arrayParametros["objOrdenTrabajo"];
                }
                else
                {
                    if(!isset($arrayParametros['intIdOficina']) || empty($arrayParametros['intIdOficina']))
                    {
                        throw new \Exception("No se ha enviado la oficina para generar la orden de trabajo");
                    }

                    $arrayRespuestaGeneraOrden = $this->serviceLicenciasKasperskyWs->generaOrdenDeTrabajo($arrayParametros);
                    if($arrayRespuestaGeneraOrden["status"] === "ERROR")
                    {
                        throw new \Exception($arrayRespuestaGeneraOrden["mensaje"]);
                    }
                    $objOrdenTrabajo    = $arrayRespuestaGeneraOrden["objOrdenTrabajo"];
                }
            }
            
            $arrayParamsProcesaEnvioWs  = array(
                                                "strPassword"           => $strPassword,
                                                "strTipoTransaccion"    => $strTipoTransaccion,
                                                "objServicio"           => $objServicio,
                                                "objProductoIPMP"       => $objProductoIPMP,
                                                "strCodigoProducto"     => $strCodigoProducto,
                                                "strUsrCreacion"        => $strUsrCreacion,
                                                "strIpCreacion"         => $strIpCreacion);
            
            $arrayRespuestaWs           = $this->serviceLicenciasKasperskyWs->procesaEnvioWsLicencias($arrayParamsProcesaEnvioWs);
            

            $boolCRSPorLogin = false;

            if(isset($arrayParametros["boolEsCRS"]) && !empty($arrayParametros["boolEsCRS"]))
            {
                $boolCRSPorLogin = $arrayParametros["boolEsCRS"];
            }
            
            if(!$boolCRSPorLogin && ($strEscenario === "ACTIVACION_PROD_ADICIONAL" || $strEscenario === "ACTIVACION_X_CAMBIO_CORREO"))
            {
                if($arrayRespuestaWs["status"] === "OK")
                {
                    $arrayDataIPMP                  = array("intSuscriberId"    => $arrayRespuestaWs["SuscriberId"],
                                                            "strCodigoProducto" => $strCodigoProducto);
                    $arrayRespCrearSpcActivacion    = $this->creaSpcActivacionIPMP(array(   "objServicio"               => $objServicio,
                                                                                            "objProducto"               => $objProductoIPMP,
                                                                                            "strUsrCreacion"            => $strUsrCreacion,
                                                                                            "strClientIp"               => $strIpCreacion,
                                                                                            "arrayDataIPMP"             => $arrayDataIPMP,
                                                                                            "strValorAntivirus"         => $strValorAntivirus,
                                                                                            "strHabilitaTransaccion"    => "NO"));
                    if($arrayRespCrearSpcActivacion["status"] === "ERROR")
                    {
                        throw new \Exception($arrayRespCrearSpcActivacion["mensaje"]);
                    }
                }
                else
                {
                    if($this->emComercial->getConnection()->isTransactionActive())
                    {
                        $this->emComercial->getConnection()->rollback();
                    }

                    $this->emComercial->beginTransaction();
                    //Se guarda en el historial del servicio el mensaje de error obtenido del web service
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicio);
                    $objServicioHistorial->setObservacion($strMsjErrorAdicHtml.$strMsjTecnologia."<br>".$arrayRespuestaWs["mensajeHtml"]);
                    $objServicioHistorial->setEstado($strEstadoServicioInicial);
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                    
                    if($strEscenario === "ACTIVACION_PROD_ADICIONAL")
                    {
                        $arrayParamsSpcReintentos       = array("objServicio"       => $objServicio,
                                                                "strUsrCreacion"    => $strUsrCreacion,
                                                                "strCaracteristica" => "NUMERO REINTENTOS",
                                                                "strValor"          => "0");
                        $arrayRespuestaSpcReintentos    = $this->guardaServicioProductoCaracteristica($arrayParamsSpcReintentos);
                        if($arrayRespuestaSpcReintentos["status"] == 'ERROR')
                        {
                            throw new \Exception($arrayRespuestaSpcReintentos["mensaje"]);
                        }
                    }
                    else if($strEscenario === "ACTIVACION_X_CAMBIO_CORREO")
                    {
                        $arrayParamsGetSpcCancelXCorreo = array("objServicio"       => $objServicio,
                                                                "objProducto"       => $objProductoIPMP,
                                                                "strCaracteristica" => 'PERMITE_CANCELACION_LOGICA');
                        $arrayRespuestaGetSpc                   = $this->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcCancelXCorreo);
                        if(!is_object($arrayRespuestaGetSpc["objServicioProdCaract"]))
                        {
                            $arrayParamsSpcCancelXCorreo    = array("objServicio"       => $objServicio,
                                                                    "objProducto"       => $objProductoIPMP,
                                                                    "strUsrCreacion"    => $strUsrCreacion,
                                                                    "strCaracteristica" => "PERMITE_CANCELACION_LOGICA",
                                                                    "strValor"          => "SI");
                            $arrayRespuestaSpcCancelXCorreo   = $this->guardaServicioProductoCaracteristica($arrayParamsSpcCancelXCorreo);
                            if($arrayRespuestaSpcCancelXCorreo["status"] == 'ERROR')
                            {
                                throw new \Exception($arrayRespuestaSpcCancelXCorreo["mensaje"]);
                            }
                        }
                    }
                    
                    $this->emComercial->commit();
                }
            }
            $strStatus                  = "OK";
            $strMensaje                 = "";
        }
        catch (\Exception $e)
        {
            $strStatus          = "ERROR";
            $strMensaje         = $e->getMessage();
            $arrayRespuestaWs   = array();
            
            $this->serviceUtil->insertError('Telcos+', 
                                            'LicenciasKaspersky->activarLicencias', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
        }
        $arrayRespuesta = array("status"            => $strStatus,
                                "mensaje"           => $strMensaje,
                                "objOrdenTrabajo"   => $objOrdenTrabajo,
                                "arrayRespuestaWs"  => $arrayRespuestaWs);
        return $arrayRespuesta;
    }
    
    /**
     * Función que realiza el corte de licencias de servicios I. PROTEGIDO MULTI PAID con tecnología Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-07-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-08-2019 Se agrega la obtención de la variable strMsjTecnologia en dónde se especifica la tecnología del servicio
     * 
     * @param array $arrayParametros [
     *                                  "strProceso"                => CORTE_ANTIVIRUS,
     *                                  "strEscenario"              => CORTE_PROD_EN_PLAN o CORTE_PROD_ADICIONAL,
     *                                  "objServicio"               => objeto del servicio,
     *                                  "objPunto"                  => objeto del punto,
     *                                  "objProductoIPMP"           => objeto del producto I.PROTEGIDO MULTI PAID de servicios adicionales,
     *                                  "strEstadoServicioInicial"  => estado actual del servicio al invocar a esta función,
     *                                  "strCodEmpresa"             => código de la empresa,
     *                                  "strUsrCreacion"            => usuario de creación,
     *                                  "strIpCreacion"             => ip del creación
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"            => OK o ERROR,
     *                                  "mensaje"           => mensaje de error,
     *                                  "arrayRespuestaWs"  => arreglo con la respuesta del web service
     *                                ]
     * 
     */
    public function cortarLicencias($arrayParametros)
    {
        $strPassword                    = $arrayParametros["strPassword"];
        $strEscenario                   = $arrayParametros['strEscenario'];
        $objServicio                    = $arrayParametros['objServicio'];
        $objPunto                       = $arrayParametros['objPunto'];
        $strCodEmpresa                  = $arrayParametros['strCodEmpresa'];
        $strCodigoProducto              = $arrayParametros['strCodigoProducto'];
        $strUsrCreacion                 = $arrayParametros['strUsrCreacion'] ? $arrayParametros['strUsrCreacion'] : "telcos";
        $strIpCreacion                  = $arrayParametros['strIpCreacion'] ? $arrayParametros['strIpCreacion'] : "127.0.0.1";
        $objProductoIPMP                = is_object($arrayParametros["objProductoIPMP"]) ? $arrayParametros["objProductoIPMP"] : null;
        $strEstadoServicioInicial       = $arrayParametros['strEstadoServicioInicial'] ? $arrayParametros['strEstadoServicioInicial'] : "";
        $strMsjTecnologia               = $arrayParametros['strMsjTecnologia'] ? $arrayParametros['strMsjTecnologia'] : "";
        $strMsjErrorAdicHtml            = "";
        $strTipoTransaccion             = "Suspension";
        
        if($strEscenario === "CORTE_PROD_EN_PLAN" || $strEscenario === "SUSPENCION_PROD_EN_PLAN")
        {
            $this->emComercial->beginTransaction();
        }
        try
        {
            if(!isset($strEscenario) || empty($strEscenario))
            {
                throw new \Exception("No se han enviado el escenario que se desea ejecutar para las licencias");
            }
            else if(!is_object($objServicio) || !is_object($objPunto)
                || !isset($strCodEmpresa) || empty($strCodEmpresa))
            {
                throw new \Exception("No se ha enviado el servicio, el punto o el id de la empresa");
            }
            else if(!isset($strCodigoProducto) || empty($strCodigoProducto)
                || !isset($strPassword) || empty($strPassword))
            {
                throw new \Exception("No se ha enviado el código del producto o la password para gestionar las licencias");
            }

            $arrayParamsProcesaEnvioWs  = array(
                                            "strPassword"           => $strPassword,
                                            "strTipoTransaccion"    => $strTipoTransaccion,
                                            "objServicio"           => $objServicio,
                                            "objProductoIPMP"       => $objProductoIPMP,
                                            "strCodigoProducto"     => $strCodigoProducto,
                                            "strUsrCreacion"        => $strUsrCreacion,
                                            "strIpCreacion"         => $strIpCreacion);
            
            $arrayRespuestaWs           = $this->serviceLicenciasKasperskyWs->procesaEnvioWsLicencias($arrayParamsProcesaEnvioWs);
            
            if(isset($arrayRespuestaWs) && !empty($arrayRespuestaWs) && $arrayRespuestaWs["status"] === "OK")
            {
                if($strEscenario === "CORTE_PROD_EN_PLAN" || $strEscenario === "SUSPENCION_PROD_EN_PLAN")
                {
                    if($strEscenario === "CORTE_PROD_EN_PLAN")
                    {
                        $strObservacion = "Se cortó el producto ".$objProductoIPMP->getDescripcionProducto()
                        .$strMsjTecnologia." incluido en el plan";
                    }
                    else
                    {
                        $strObservacion = "Se suspendió el producto".$objProductoIPMP->getDescripcionProducto()
                        .$strMsjTecnologia." incluido en el plan";
                    }

                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicio);
                    $objServicioHistorial->setObservacion($strObservacion);
                    $objServicioHistorial->setEstado($objServicio->getEstado());
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                }
            }
            else
            {
                if($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->rollback();
                }
                
                if($strEscenario === "CORTE_PROD_EN_PLAN")
                {
                    $strMsjErrorAdicHtml = "No se ha podido cortar el producto ".$objProductoIPMP->getDescripcionProducto().$strMsjTecnologia
                                           ." incluido en el plan<br>";
                }
                else if($strEscenario === "CORTE_PROD_ADICIONAL")
                {
                    $strMsjErrorAdicHtml = "No se ha podido cortar el servicio ".$objServicio->getProductoId()->getDescripcionProducto()
                                           .$strMsjTecnologia."<br>";
                }
                $this->emComercial->beginTransaction();
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion($strMsjErrorAdicHtml.$arrayRespuestaWs["mensajeHtml"]);
                $objServicioHistorial->setEstado($strEstadoServicioInicial);
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
                $this->emComercial->commit();
                
                if($strEscenario === "CORTE_PROD_EN_PLAN")
                {
                    throw new \Exception($arrayRespuestaWs["mensaje"]);
                }
            }
            if($strEscenario === "CORTE_PROD_EN_PLAN")
            {
                $this->emComercial->commit();
            }
            
            $strStatus  = "OK";
            $strMensaje = "";
        }
        catch (\Exception $e)
        {
            $strStatus          = "ERROR";
            $strMensaje         = $e->getMessage();
            $arrayRespuestaWs   = array();
            
            if($strEscenario === "CORTE_PROD_EN_PLAN" && $this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
            }
            
            $this->serviceUtil->insertError('Telcos+', 
                                            'LicenciasKaspersky->cortarLicencias', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
        }
        $arrayRespuesta = array("status"            => $strStatus,
                                "mensaje"           => $strMensaje,
                                "arrayRespuestaWs"  => $arrayRespuestaWs);
        return $arrayRespuesta;
    }
    
    /**
     * Función que realiza la reactivación de licencias de servicios I. PROTEGIDO MULTI PAID con tecnología Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-07-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-08-2019 Se agrega la obtención de la variable strMsjTecnologia en dónde se especifica la tecnología del servicio
     * 
     * @param array $arrayParametros [
     *                                  "strProceso"                => REACTIVACION_ANTIVIRUS,
     *                                  "strEscenario"              => REACTIVACION_PROD_EN_PLAN o REACTIVACION_ANTIVIRUS,
     *                                  "objServicio"               => objeto del servicio,
     *                                  "objPunto"                  => objeto del punto,
     *                                  "objProductoIPMP"           => objeto del producto I.PROTEGIDO MULTI PAID de servicios adicionales,
     *                                  "strEstadoServicioInicial"  => estado actual del servicio al invocar a esta función,
     *                                  "strCodEmpresa"             => código de la empresa,
     *                                  "strUsrCreacion"            => usuario de creación,
     *                                  "strIpCreacion"             => ip del creación
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"            => OK o ERROR,
     *                                  "mensaje"           => mensaje de error,
     *                                  "arrayRespuestaWs"  => arreglo con la respuesta del web service
     *                                ]
     * 
     */
    public function reactivarLicencias($arrayParametros)
    {
        $strPassword                    = $arrayParametros["strPassword"];
        $strEscenario                   = $arrayParametros['strEscenario'];
        $objServicio                    = $arrayParametros['objServicio'];
        $objPunto                       = $arrayParametros['objPunto'];
        $strCodEmpresa                  = $arrayParametros['strCodEmpresa'];
        $strCodigoProducto              = $arrayParametros['strCodigoProducto'];
        $strUsrCreacion                 = $arrayParametros['strUsrCreacion'] ? $arrayParametros['strUsrCreacion'] : "telcos";
        $strIpCreacion                  = $arrayParametros['strIpCreacion'] ? $arrayParametros['strIpCreacion'] : "127.0.0.1";
        $objProductoIPMP                = is_object($arrayParametros["objProductoIPMP"]) ? $arrayParametros["objProductoIPMP"] : null;
        $strEstadoServicioInicial       = $arrayParametros['strEstadoServicioInicial'] ? $arrayParametros['strEstadoServicioInicial'] : "";
        $strMsjTecnologia               = $arrayParametros['strMsjTecnologia'] ? $arrayParametros['strMsjTecnologia'] : "";
        $strMsjErrorAdicHtml            = "";
        $strTipoTransaccion             = "Reactivacion";
        
        if($strEscenario === "REACTIVACION_PROD_EN_PLAN")
        {
            $this->emComercial->beginTransaction();
        }
        try
        {
            if(!isset($strEscenario) || empty($strEscenario))
            {
                throw new \Exception("No se han enviado el escenario que se desea ejecutar para las licencias");
            }
            else if(!is_object($objServicio) || !is_object($objPunto)
                || !isset($strCodEmpresa) || empty($strCodEmpresa))
            {
                throw new \Exception("No se ha enviado el servicio, el punto o el id de la empresa");
            }
            else if(!isset($strCodigoProducto) || empty($strCodigoProducto)
                || !isset($strPassword) || empty($strPassword))
            {
                throw new \Exception("No se ha enviado el código del producto o la password para gestionar las licencias");
            }

            $arrayParamsProcesaEnvioWs  = array(
                                            "strPassword"           => $strPassword,
                                            "strTipoTransaccion"    => $strTipoTransaccion,
                                            "objServicio"           => $objServicio,
                                            "objProductoIPMP"       => $objProductoIPMP,
                                            "strCodigoProducto"     => $strCodigoProducto,
                                            "strUsrCreacion"        => $strUsrCreacion,
                                            "strIpCreacion"         => $strIpCreacion);
            
            $arrayRespuestaWs           = $this->serviceLicenciasKasperskyWs->procesaEnvioWsLicencias($arrayParamsProcesaEnvioWs);
            
            
            if(isset($arrayRespuestaWs) && !empty($arrayRespuestaWs) && $arrayRespuestaWs["status"] === "OK")
            {
                if($strEscenario === "REACTIVACION_PROD_EN_PLAN")
                {
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicio);
                    $objServicioHistorial->setObservacion("Se reactivo el producto ".$objProductoIPMP->getDescripcionProducto().$strMsjTecnologia
                                                          ." incluido en el plan");
                    $objServicioHistorial->setEstado($objServicio->getEstado());
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                }
            }
            else
            {
                if($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->rollback();
                }
                
                if($strEscenario === "REACTIVACION_PROD_EN_PLAN")
                {
                    $strMsjErrorAdicHtml = "No se ha podido reactivar el producto ".$objProductoIPMP->getDescripcionProducto().$strMsjTecnologia
                                          ." incluido en el plan<br>";
                }
                else if($strEscenario === "REACTIVACION_PROD_ADICIONAL")
                {
                    $strMsjErrorAdicHtml = "No se ha podido reactivar el servicio ".$objServicio->getProductoId()->getDescripcionProducto()
                                           .$strMsjTecnologia."<br>";
                }
                $this->emComercial->beginTransaction();
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion($strMsjErrorAdicHtml.$arrayRespuestaWs["mensajeHtml"]);
                $objServicioHistorial->setEstado($strEstadoServicioInicial);
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
                $this->emComercial->commit();
                if($strEscenario === "REACTIVACION_PROD_EN_PLAN")
                {
                    throw new \Exception($arrayRespuestaWs["mensaje"]);
                }
            }
            if($strEscenario === "REACTIVACION_PROD_EN_PLAN")
            {
                $this->emComercial->commit();
            }
            
            $strStatus  = "OK";
            $strMensaje = "";
        }
        catch (\Exception $e)
        {
            $strStatus          = "ERROR";
            $strMensaje         = $e->getMessage();
            $arrayRespuestaWs   = array();
            
            if($strEscenario === "REACTIVACION_PROD_EN_PLAN" && $this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
            }
            
            $this->serviceUtil->insertError('Telcos+', 
                                            'LicenciasKaspersky->reactivarLicencias', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
        }
        $arrayRespuesta = array("status"            => $strStatus,
                                "mensaje"           => $strMensaje,
                                "arrayRespuestaWs"  => $arrayRespuestaWs);
        return $arrayRespuesta;
    }
   
    /**
     * Función que realiza la cancelación de licencias de servicios I. PROTEGIDO MULTI PAID con tecnología Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-07-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-08-2019 Se agrega la obtención de la variable strMsjTecnologia en dónde se especifica la tecnología del servicio
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.2 28-05-2021 Se agrega Exception para que muestre error del ws debido a que presenta problema el rollback.
     *
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 1.3 20-06-2023 Se agrega bandera para validar el estado de la caracteristica suscriber_id y cancelar
     *                         de manera logica el servicio adicional I. PAID MULTIPAID.
     *
     * @param array $arrayParametros [
     *                                  "strProceso"                => CANCELACION_ANTIVIRUS,
     *                                  "strEscenario"              => CANCELACION_PROD_EN_PLAN, CANCELACION_PROD_ADICIONAL_X_INTERNET,
     *                                                                 CANCELACION_PROD_ADICIONAL o CANCELACION_X_CAMBIO_CORREO
     *                                  "objServicio"               => objeto del servicio,
     *                                  "objPunto"                  => objeto del punto,
     *                                  "objProductoIPMP"           => objeto del producto I.PROTEGIDO MULTI PAID de servicios adicionales,
     *                                  "strEstadoServicioInicial"  => estado actual del servicio al invocar a esta función,
     *                                  "strMsjErrorAdicHtml"       => mensaje informativo de error que se guardará en el historial del servicio,
     *                                  "strCodEmpresa"             => código de la empresa,
     *                                  "strUsrCreacion"            => usuario de creación,
     *                                  "strIpCreacion"             => ip del creación,
     *                                  "strLicenciaActiva"         => estado actual de la caracteristica  suscriber_id,
     *                                                                 para cancelacion logica por CANCELACION_PROD_ADICIONAL_X_INTERNET
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"            => OK o ERROR,
     *                                  "mensaje"           => mensaje de error,
     *                                  "arrayRespuestaWs"  => arreglo con la respuesta del web service
     *                                ]
     * 
     */
    public function cancelarLicencias($arrayParametros)
    {
        $strPassword                    = $arrayParametros["strPassword"];
        $strEscenario                   = $arrayParametros['strEscenario'];
        $objServicio                    = $arrayParametros['objServicio'];
        $objPunto                       = $arrayParametros['objPunto'];
        $strCodEmpresa                  = $arrayParametros['strCodEmpresa'];
        $strCodigoProducto              = $arrayParametros['strCodigoProducto'];
        $strUsrCreacion                 = $arrayParametros['strUsrCreacion'] ? $arrayParametros['strUsrCreacion'] : "telcos";
        $strIpCreacion                  = $arrayParametros['strIpCreacion'] ? $arrayParametros['strIpCreacion'] : "127.0.0.1";
        $objProductoIPMP                = is_object($arrayParametros["objProductoIPMP"]) ? $arrayParametros["objProductoIPMP"] : null;
        $strMsjErrorAdicHtml            = $arrayParametros['strMsjErrorAdicHtml'] ? $arrayParametros['strMsjErrorAdicHtml'] : "";
        $strEstadoServicioInicial       = $arrayParametros['strEstadoServicioInicial'] ? $arrayParametros['strEstadoServicioInicial'] : "";
        $intSuscriberId                 = $arrayParametros['intSuscriberId'] ? $arrayParametros['intSuscriberId'] : 0;
        $strCorreoSuscripcion           = $arrayParametros['strCorreoSuscripcion'] ? $arrayParametros['strCorreoSuscripcion'] : "";
        $strMsjTecnologia               = $arrayParametros['strMsjTecnologia'] ? $arrayParametros['strMsjTecnologia'] : "";
        $strPermiteEnvioCorreoError     = $arrayParametros['strPermiteEnvioCorreoError'] ? $arrayParametros['strPermiteEnvioCorreoError'] : "SI";
        $strLicenciaActiva              = $arrayParametros['strLicenciaActiva'] ? $arrayParametros['strLicenciaActiva'] : "SI";
        $strTipoTransaccion             = "Cancelacion";
        if($strEscenario === "CANCELACION_PROD_EN_PLAN" || $strEscenario === "CANCELACION_POR_CAMBIO_RAZON_SOCIAL_LOGIN_EXIST")
        {
            $this->emComercial->beginTransaction();
        }
        try
        {
            if(!isset($strEscenario) || empty($strEscenario))
            {
                throw new \Exception("No se han enviado el escenario que se desea ejecutar para las licencias");
            }
            else if(!is_object($objServicio) || !is_object($objPunto)
                || !isset($strCodEmpresa) || empty($strCodEmpresa))
            {
                throw new \Exception("No se ha enviado el servicio, el punto o el id de la empresa");
            }
            else if(!isset($strCodigoProducto) || empty($strCodigoProducto)
                || !isset($strPassword) || empty($strPassword))
            {
                throw new \Exception("No se ha enviado el código del producto o la password para gestionar las licencias");
            }
            $arrayParamsGetSpcPermiteCancelLogica       = array("objServicio"       => $objServicio,
                                                                "objProducto"       => $objProductoIPMP,
                                                                "strCaracteristica" => "PERMITE_CANCELACION_LOGICA");
            $arrayRespuestaGetSpcPermiteCancelLogica    = $this->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcPermiteCancelLogica);
            $objSpcPermiteCancelLogica                  = $arrayRespuestaGetSpcPermiteCancelLogica["objServicioProdCaract"];
            if(is_object($objSpcPermiteCancelLogica))
            {
                $strPermiteCancelLogica = $objSpcPermiteCancelLogica->getValor();
            }
            else
            {
                if($strLicenciaActiva == 'NO' && $strEscenario == 'CANCELACION_PROD_ADICIONAL_X_INTERNET')
                {
                    $strPermiteCancelLogica = "SI";
                }
                else
                {
                    $strPermiteCancelLogica = "NO";
                }
            }
            
            
            if($strPermiteCancelLogica === "SI")
            {
                $arrayRespuestaWs           = array("status" => "OK");
            }
            else
            {
                $arrayParamsProcesaEnvioWs  = array(
                                                "strPassword"           => $strPassword,
                                                "strTipoTransaccion"    => $strTipoTransaccion,
                                                "objServicio"           => $objServicio,
                                                "objProductoIPMP"       => $objProductoIPMP,
                                                "strCodigoProducto"     => $strCodigoProducto,
                                                "strUsrCreacion"        => $strUsrCreacion,
                                                "strIpCreacion"         => $strIpCreacion,
                                                "intSuscriberId"        => $intSuscriberId);
            
                $arrayRespuestaWs           = $this->serviceLicenciasKasperskyWs->procesaEnvioWsLicencias($arrayParamsProcesaEnvioWs);
                
            }
            
            if(isset($arrayRespuestaWs) && !empty($arrayRespuestaWs) && $arrayRespuestaWs["status"] === "OK")
            {
                //Se eliminan características aspciadas al servicio
                $arrayParamsActualizaSpc                        = array("objServicio"       => $objServicio,
                                                                        "objProducto"       => $objProductoIPMP,
                                                                        "strEstadoNuevo"    => "Eliminado",
                                                                        "strUsrCreacion"    => $strUsrCreacion);

               if($strEscenario  == 'CANCELACION_POR_CAMBIO_RAZON_SOCIAL_LOGIN' ||
                  $strEscenario  == 'CANCELACION_POR_CAMBIO_RAZON_SOCIAL_LOGIN_EXIST')
               {
                    $objInfoServicioHistorial = new InfoServicioHistorial();
                    $objInfoServicioHistorial->setServicioId($objServicio);
                    $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objInfoServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objInfoServicioHistorial->setEstado($objServicio->getEstado());
                    $objInfoServicioHistorial->setObservacion('Se canceló servicio Internet Protegido por cambio
                    de razón social por login');
                    $this->emComercial->persist($objInfoServicioHistorial);
                    $this->emComercial->flush();
               }
               else if($strEscenario  == 'CANCELACION_POR_CAMBIO_RAZON_SOCIAL')
               {
                    $objInfoServicioHistorial = new InfoServicioHistorial();
                    $objInfoServicioHistorial->setServicioId($objServicio);
                    $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objInfoServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objInfoServicioHistorial->setEstado($objServicio->getEstado());
                    $objInfoServicioHistorial->setObservacion('Se canceló servicio Internet Protegido por cambio
                    de razón social');
                    $this->emComercial->persist($objInfoServicioHistorial);
                    $this->emComercial->flush();
               }
               else
               {
                    $arrayParamsActualizaSpc["strCaracteristica"]   = "CORREO ELECTRONICO";
                    $this->actualizarServicioProductoCaracteristica($arrayParamsActualizaSpc);

                    $arrayParamsActualizaSpc["strCaracteristica"]   = "SUSCRIBER_ID";
                    $this->actualizarServicioProductoCaracteristica($arrayParamsActualizaSpc);
                    
                    $arrayParamsActualizaSpc["strCaracteristica"]   = "ANTIVIRUS";
                    $this->actualizarServicioProductoCaracteristica($arrayParamsActualizaSpc);
                    
                    $arrayParamsActualizaSpc["strCaracteristica"]   = "CODIGO_PRODUCTO";
                    $this->actualizarServicioProductoCaracteristica($arrayParamsActualizaSpc);
                    
                    $arrayParamsActualizaSpc["strCaracteristica"]   = "PERMITE_CANCELACION_LOGICA";
                    $this->actualizarServicioProductoCaracteristica($arrayParamsActualizaSpc);
               }                                                       
            
                
                if($strEscenario === "CANCELACION_PROD_EN_PLAN")
                {
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicio);
                    $objServicioHistorial->setObservacion("Se canceló el producto ".$objProductoIPMP->getDescripcionProducto().$strMsjTecnologia
                                                          ." incluido en el plan");
                    $objServicioHistorial->setEstado($objServicio->getEstado());
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                }
                else if($strEscenario === "CANCELACION_PROD_ADICIONAL_X_INTERNET")
                {
                    $objServicio->setEstado("Cancel");
                    $this->emComercial->persist($objServicio);
                    $this->emComercial->flush();

                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicio);
                    $objServicioHistorial->setObservacion("Se canceló el servicio "
                                                          .$objServicio->getProductoId()->getDescripcionProducto().$strMsjTecnologia);
                    $objServicioHistorial->setMotivoId($arrayParametros["objMotivo"]->getId());
                    $objServicioHistorial->setEstado("Cancel");
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $objServicioHistorial->setAccion($arrayParametros["objAccion"]->getNombreAccion());
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                }
                else if($strEscenario === "CANCELACION_PROD_ADICIONAL")
                {
                    $this->serviceLicenciasKasperskyWs->cancelacionPuntoYCliente(array( "objServicio"       => $objServicio,
                                                                                        "strUsrCreacion"    => $strUsrCreacion,
                                                                                        "strIpCreacion"     => $strIpCreacion));
                }
            }
            else
            {
                if($strEscenario === "CANCELACION_PROD_EN_PLAN")
                {
                    $strMsjErrorAdicHtml = "No se ha podido cancelar el producto ".$objProductoIPMP->getDescripcionProducto()
                                           .$strMsjTecnologia." incluido en el plan<br>";
                }
                else if($strEscenario === "CANCELACION_PROD_ADICIONAL_X_INTERNET" || $strEscenario === "CANCELACION_PROD_ADICIONAL")
                {
                    $strMsjErrorAdicHtml = "No se ha podido cancelar el servicio ".$objServicio->getProductoId()->getDescripcionProducto()
                                           .$strMsjTecnologia."<br>";
                }
                
                if($strEscenario !== "CANCELACION_PROD_ADICIONAL_X_INTERNET")
                {
                    if($this->emComercial->getConnection()->isTransactionActive())
                    {
                        throw new \Exception($arrayRespuestaWs["mensaje"]);
                    }

                    $this->emComercial->beginTransaction();
                }
                $strMsjPosterior = "";
                if($strEscenario !== "CANCELACION_PROD_EN_PLAN")
                {
                    $strMsjPosterior .= "y luego escale una tarea a Sistemas para proceder con la cancelación lógica";
                }
                $strMsjPosterior    = "Comuníquese con el proveedor para realizar una cancelación manual ".$strMsjPosterior;
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion($strMsjErrorAdicHtml.$arrayRespuestaWs["mensajeHtml"]."<br>".$strMsjPosterior);
                $objServicioHistorial->setEstado($strEstadoServicioInicial);
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
                $arrayRespuestaWs["mensaje"] = $arrayRespuestaWs["mensaje"].". ".$strMsjPosterior;
                
                if($strPermiteEnvioCorreoError === "SI")
                {
                    $this->serviceLicenciasKasperskyWs
                         ->envioNotifErrorCancelacionLicencias(array(  
                                                                    "objServicio"               => $objServicio,
                                                                    "objProductoIPMP"           => $objProductoIPMP,
                                                                    "strObservacion"            => $strMsjErrorAdicHtml
                                                                                                   .$arrayRespuestaWs["mensajeHtml"]
                                                                                                   ."<br>".$strMsjPosterior,
                                                                    "strEstadoServicioInicial"  => $strEstadoServicioInicial,
                                                                    "intSuscriberId"            => $intSuscriberId,
                                                                    "strCorreoSuscripcion"      => $strCorreoSuscripcion));
                }
                if($strEscenario === "CANCELACION_PROD_ADICIONAL" || $strEscenario === "CANCELACION_PROD_ADICIONAL_X_INTERNET")
                {
                    $arrayParamsGetSpcCancelLogica      = array("objServicio"       => $objServicio,
                                                                "objProducto"       => $objProductoIPMP,
                                                                "strCaracteristica" => "PERMITE_CANCELACION_LOGICA");
                    $arrayRespuestaGetSpcCancelLogica   = $this->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcCancelLogica);
                    if(!is_object($arrayRespuestaGetSpcCancelLogica["objServicioProdCaract"]))
                    {
                        $arrayParamsSpcCancelLogica     = array("objServicio"       => $objServicio,
                                                                "objProducto"       => $objProductoIPMP,
                                                                "strUsrCreacion"    => $strUsrCreacion,
                                                                "strCaracteristica" => "PERMITE_CANCELACION_LOGICA",
                                                                "strValor"          => "SI");
                        $this->guardaServicioProductoCaracteristica($arrayParamsSpcCancelLogica);
                    }
                }
                
                if($strEscenario === "CANCELACION_PROD_EN_PLAN")
                {
                    throw new \Exception($arrayRespuestaWs["mensaje"]);
                }
            }
            
            //Se hace commit dado el inicio de la transaccion distinto en el cambio de razon social por login cliente existente
            if($strEscenario === "CANCELACION_PROD_EN_PLAN"
            || $strEscenario === "CANCELACION_POR_CAMBIO_RAZON_SOCIAL_LOGIN_EXIST")
            {
                $this->emComercial->commit();
            }
            
            $strStatus  = "OK";
            $strMensaje = "";
        }
        catch (\Exception $e)
        {
            $strStatus          = "ERROR";
            $strMensaje         = $e->getMessage();
            $arrayRespuestaWs   = array();
            
            if($strEscenario === "CANCELACION_PROD_EN_PLAN" && $this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
            }
            
            $this->serviceUtil->insertError('Telcos+', 
                                            'LicenciasKaspersky->cancelarLicencias', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
        }
        $arrayRespuesta = array("status"            => $strStatus,
                                "mensaje"           => $strMensaje,
                                "arrayRespuestaWs"  => $arrayRespuestaWs);
        return $arrayRespuesta;
    }
    
    /**
     * Función que realiza el cambio Correos de licencias de servicios I. PROTEGIDO MULTI PAID con tecnología Kaspersky
     * 
     * @author Kevin Ortiz <kcortiz@telconet.ec>
     * @version 1.0 08-07-2019
     * 
     * @param array $arrayParametros [
     *                                  "strPassword"               => Password de antivirus,
     *                                  "strEscenario"              => ACTUALIZACION_CORREO_PROD_ADICIONAL,ACTUALIZACION_CORREO_PROD_PLAN,
     *                                  "objServicio"               => objeto del servicio,
     *                                  "objPunto"                  => objeto del punto,
     *                                  "strCodEmpresa"             => código de la empresa,
     *                                  "strCodigoProducto"         => código del producto,
     *                                  "strUsrCreacion"            => usuario de creación,
     *                                  "strIpCreacion"             => ip del creación,
     *                                  "objProductoIPMP"           => objeto del producto I.PROTEGIDO MULTI PAID de servicios adicionales,
     *                                  "strEstadoServicioInicial"  => estado actual del servicio al invocar a esta función,
     *                                  "strCorreoSuscripcionNuevo" => correo nuevo para actulizacion,
     *                                  "strMsjTecnologia"          => mensaje informativo de error que se guardará en el historial del servicio,
     * 
     *                                ]
     *                                                              
     * @return array $arrayRespuesta [
     *                                  "status"            => OK o ERROR,
     *                                  "mensaje"           => EXITOSO O ERROR,
     *                                  "arrayRespuestaWs"  => arreglo con la respuesta del web service
     *                                ]
     * 
     */

    public function cambiarCorreo($arrayParametros)
    {
        $strPassword                    = $arrayParametros["strPassword"];
        $strEscenario                   = $arrayParametros['strEscenario'];
        $objServicio                    = $arrayParametros['objServicio'];
        $objPunto                       = $arrayParametros['objPunto'];
        $strCodEmpresa                  = $arrayParametros['strCodEmpresa'];
        $strCodigoProducto              = $arrayParametros['strCodigoProducto'];
        $strUsrCreacion                 = $arrayParametros['strUsrCreacion'] ? $arrayParametros['strUsrCreacion'] : "telcos";
        $strIpCreacion                  = $arrayParametros['strIpCreacion'] ? $arrayParametros['strIpCreacion'] : "127.0.0.1";
        $objProductoIPMP                = is_object($arrayParametros["objProductoIPMP"]) ? $arrayParametros["objProductoIPMP"] : null;
        $strEstadoServicioInicial       = $arrayParametros['strEstadoServicioInicial'] ? $arrayParametros['strEstadoServicioInicial'] : "";
        $intSuscriberId                 = $arrayParametros['intSuscriberId'] ? $arrayParametros['intSuscriberId'] : 0;
        $strCorreoSuscripcionNuevo      = $arrayParametros['strCorreoSuscripcionNuevo'] ? $arrayParametros['strCorreoSuscripcionNuevo'] : "";
        $strMsjTecnologia               = $arrayParametros['strMsjTecnologia'] ? $arrayParametros['strMsjTecnologia'] : "";
        $strTipoTransaccion             = "ActualizacionCorreo";
       
        try
        {
            if(!isset($strEscenario) || empty($strEscenario))
            {
                throw new \Exception("No se han enviado el escenario que se desea ejecutar para las licencias");
            }
            else if(!is_object($objServicio) || !is_object($objPunto)
                || !isset($strCodEmpresa) || empty($strCodEmpresa))
            {
                throw new \Exception("No se ha enviado el servicio, el punto o el id de la empresa");
            }
            else if(!isset($strCodigoProducto) || empty($strCodigoProducto)
                || !isset($strPassword) || empty($strPassword))
            {
                throw new \Exception("No se ha enviado el código del producto o la password para gestionar las licencias");
            }
            
            if($intSuscriberId === 0)
            {
                    $arrayParamsGetSpcSuscriberId   = array("objServicio"       => $objServicio,
                                                            "objProducto"       => $objProductoIPMP,
                                                            "strCaracteristica" => "SUSCRIBER_ID");
                    $arrayRespuestaSpcSuscriberId   = $this->serviceLicenciasKaspersky
                                                           ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcSuscriberId);
                    if($arrayRespuestaSpcSuscriberId["status"] == 'ERROR')
                    {
                        throw new \Exception($arrayRespuestaSpcSuscriberId["mensaje"]);
                    }
                    $objSpcSuscriberId  = $arrayRespuestaSpcSuscriberId["objServicioProdCaract"];
                    if(!is_object($objSpcSuscriberId))
                    {
                        throw new \Exception("No se ha podido obtener el objeto con el SUSCRIBER ID asociada al servicio");
                    }
                    $intSuscriberId = intval($objSpcSuscriberId->getValor());
            }

                $arrayEmail = array('Email' => $strCorreoSuscripcionNuevo);
                

                $arrayRequestWs     = array(
                                            "Password"          => $strPassword,
                                            "Cliente"           => $arrayEmail,
                                            "Producto"          => array(),
                                            "TipoTransaccion"   => $strTipoTransaccion,
                                            "SuscriberId"       => $intSuscriberId);

                $arrayRespuestaWs   = $this->serviceLicenciasKasperskyWs->invocaWs($arrayRequestWs);
            
            //@KrissveraDEv
            $arrayRespuestaWs["status"] =  "OK";
            if(isset($arrayRespuestaWs) && !empty($arrayRespuestaWs) && $arrayRespuestaWs["status"] === "OK")
            {
                $strStatus  = "OK";
                $strMensaje = "EXITOSO";
            }
            else
            {
                $strStatus  = "ERROR";
                $strMensaje = "ERROR";
            }
        }
        catch (\Exception $e)
        {
            $strStatus          = "ERROR";
            $strMensaje         = $e->getMessage();
            $arrayRespuestaWs   = array();
            
            $this->serviceUtil->insertError('Telcos+', 
                                            'LicenciasKaspersky->CambiarCorreo', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
        }
        $arrayRespuesta = array("status"            => $strStatus,
                                "mensaje"           => $strMensaje,
                                "arrayRespuestaWs"  => $arrayRespuestaWs);
        return $arrayRespuesta;
    }
    
    /**
     * Función que realiza la gestión de licencias de servicios I. PROTEGIDO MULTI PAID con tecnología Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-07-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-08-2019 Se agrega el envío de la variable strMsjTecnologia en dónde se especifica la tecnología del servicio
     * 
     * @author kevin ortiz <kcortiz@telconet.ec>
     * @version 1.2 22-10-2020 Se agrego el proceso strProceso === "CAMBIAR_CORREO"
     * 
     * @param array $arrayParametros [
     *                                  "strProceso"                => ACTIVACION_ANTIVIRUS, CORTE_ANTIVIRUS, REACTIVACION_ANTIVIRUS
     *                                                                 o CANCELACION_ANTIVIRUS,
     *                                  "strEscenario"              => Escenario enviado por cada proceso,
     *                                  "objServicio"               => objeto del servicio,
     *                                  "objPunto"                  => objeto del punto,
     *                                  "objProductoIPMP"           => objeto del producto I.PROTEGIDO MULTI PAID de servicios adicionales,
     *                                  "strEstadoServicioInicial"  => estado actual del servicio al invocar a esta función,
     *                                  "strMsjErrorAdicHtml"       => mensaje informativo de error que se guardará en el historial del servicio,
     *                                  "strCodEmpresa"             => código de la empresa,
     *                                  "strUsrCreacion"            => usuario de creación,
     *                                  "strIpCreacion"             => ip del creación
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"            => OK o ERROR,
     *                                  "mensaje"           => mensaje de error,
     *                                  "strCodigoProducto" => código del producto
     *                                ]
     * 
     */
    public function gestionarLicencias($arrayParametros)
    {
        $strUsrCreacion             = $arrayParametros['strUsrCreacion'] ? $arrayParametros['strUsrCreacion'] : "telcos";
        $strIpCreacion              = $arrayParametros['strIpCreacion'] ? $arrayParametros['strIpCreacion'] : "127.0.0.1";
        $strValor1ParamAntivirus    = $arrayParametros['strValor1ParamAntivirus'] ? $arrayParametros['strValor1ParamAntivirus'] : "NUEVO";
        try
        {
            if(isset($arrayParametros['intIdServicio']) && !empty($arrayParametros['intIdServicio']))
            {
                $arrayParametros['objServicio'] = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                    ->find($arrayParametros['intIdServicio']);
            }
            if(isset($arrayParametros['intIdPunto']) && !empty($arrayParametros['intIdPunto']))
            {
                $arrayParametros['objPunto']    = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                                                    ->find($arrayParametros['intIdPunto']);
            }
            if(!isset($arrayParametros['strProceso']) || empty($arrayParametros['strProceso'])
                || !isset($arrayParametros['strEscenario']) || empty($arrayParametros['strEscenario']))
            {
                throw new \Exception("No se han enviado el proceso o el escenario que se desea ejecutar para las licencias");
            }
            else if(!is_object($arrayParametros['objServicio']) || !is_object($arrayParametros['objPunto'])
                    || !isset($arrayParametros['strCodEmpresa']) || empty($arrayParametros['strCodEmpresa']))
            {
                throw new \Exception("No se ha enviado el servicio, el punto o el id de la empresa");
            }
            else if(!isset($arrayParametros['strEstadoServicioInicial']) || empty($arrayParametros['strEstadoServicioInicial']))
            {
                throw new \Exception("No se ha enviado el estado inicial del servicio");
            }
            
            $arrayParametroDetAntivirus = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne( 'ANTIVIRUS_PLANES_Y_PRODS_MD',
                                                                    '',
                                                                    '', 
                                                                    '', 
                                                                    $strValor1ParamAntivirus,
                                                                    '',
                                                                    '', 
                                                                    '',
                                                                    '',
                                                                    $arrayParametros['strCodEmpresa']);
            
            if(!isset($arrayParametroDetAntivirus["valor5"]) || empty($arrayParametroDetAntivirus["valor5"]))
            {
                throw new \Exception("No se ha podido obtener la clave para gestionar las licencias");
            }
            
            if(!isset($arrayParametroDetAntivirus["valor4"]) || empty($arrayParametroDetAntivirus["valor4"]))
            {
                throw new \Exception("No se ha podido obtener el código del producto para gestionar las licencias");
            }
            
            $strPassword        = $arrayParametroDetAntivirus["valor5"];
            $strCodigoProducto  = $arrayParametroDetAntivirus["valor4"];
            $strValorAntivirus  = $arrayParametroDetAntivirus["valor2"];

            $arrayParametros['strPassword']         = $strPassword;
            $arrayParametros['strCodigoProducto']   = $strCodigoProducto;
            $arrayParametros['strValorAntivirus']   = $strValorAntivirus;
            
            if(isset($strValorAntivirus) && !empty($strValorAntivirus))
            {
                $arrayParametros['strMsjTecnologia'] = " con tecnología ".$strValorAntivirus;
            }
            else
            {
                $arrayParametros['strMsjTecnologia'] = "";
            }
            
            $strProceso     = $arrayParametros['strProceso'];
            if($strProceso === "ACTIVACION_ANTIVIRUS")
            {
                $arrayRespuesta = $this->activarLicencias($arrayParametros);
            }
            else if($strProceso === "CORTE_ANTIVIRUS" || $strProceso === "REACTIVACION_ANTIVIRUS" ||
                    $strProceso === "CANCELACION_ANTIVIRUS" || $strProceso === "CAMBIAR_CORREO" )
            {
                if($arrayParametros['strEscenario'] === "CANCELACION_PROD_EN_PLAN" ||
                   $arrayParametros['strEscenario'] === "CANCELACION_POR_CAMBIO_RAZON_SOCIAL_LOGIN" )
                {
                    if(!isset($arrayParametros['intSuscriberId']) || empty($arrayParametros['intSuscriberId']))
                    {
                        throw new \Exception("No se ha enviado el SUSCRIBER ID asociado al servicio");
                    }
                }
                else
                {
                    $arrayParamsGetSpcSuscriberId   = array("objServicio"       => $arrayParametros['objServicio'],
                                                            "objProducto"       => $arrayParametros['objProductoIPMP'],
                                                            "strCaracteristica" => "SUSCRIBER_ID");
                    $arrayRespuestaSpcSuscriberId   = $this->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcSuscriberId);
                    if($arrayRespuestaSpcSuscriberId["status"] == 'ERROR')
                    {
                        throw new \Exception($arrayRespuestaSpcSuscriberId["mensaje"]);
                    }
                    $objSpcSuscriberId  = $arrayRespuestaSpcSuscriberId["objServicioProdCaract"];
                    if(!is_object($objSpcSuscriberId))
                    {
                        throw new \Exception("No se ha podido obtener el objeto con el SUSCRIBER ID asociada al servicio");
                    }
                }
                if($strProceso === "CAMBIAR_CORREO")
                {
                    $arrayRespuesta = $this->CambiarCorreo($arrayParametros);
                }
                if($strProceso === "CORTE_ANTIVIRUS")
                {
                    $arrayRespuesta = $this->cortarLicencias($arrayParametros);
                }
                if($strProceso === "REACTIVACION_ANTIVIRUS")
                {
                    $arrayRespuesta = $this->reactivarLicencias($arrayParametros);
                }
                else if($strProceso === "CANCELACION_ANTIVIRUS")
                {
                    $arrayRespuesta = $this->cancelarLicencias($arrayParametros);
                }
            }
            else
            {
                throw new \Exception("No existe un flujo determinado para el proceso ".$strProceso);
            }
            $arrayRespuesta["strCodigoProducto"] = $strCodigoProducto;
        }
        catch (\Exception $e)
        {
            $arrayRespuesta = array("status"            => "ERROR",
                                    "mensaje"           => $e->getMessage(),
                                    "strCodigoProducto" => "");
            $this->serviceUtil->insertError('Telcos+', 
                                            'LicenciasKaspersky->gestionarLicencias', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
        }
        return $arrayRespuesta;
    }
}
