<?php

namespace telconet\planificacionBundle\Service;

use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoDetalleInterface;
use telconet\schemaBundle\Entity\InfoServicioTecnico;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoSubred;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\tecnicoBundle\Service\MigracionHuaweiService;
use telconet\tecnicoBundle\Service\InfoServicioTecnicoService;
use telconet\schemaBundle\Service\UtilService;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;


class RecursosDeRedService {

    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $emSeguridad;
    private $emNaf;
    private $emGeneral;
    private $container;
    private $servicioGeneral;  
    private $utilServicio;
    private $host;
    private $pathTelcos;
    private $pathParameters; 
    private $migracionHuawei;
    private $serviceEnvioPlantilla;
    private $serviceCambioPlan;
    private $serviceCliente;
    private $networkingScripts;
    private $serviceInfoServicioTecnico;
    private $strUrlValidarIpProvinciasWs;
    private $serviceRestClient;
    private $serviceUtil;

    public function __construct(Container $container) {
        $this->container          = $container;
        $this->emSoporte          = $this->container->get('doctrine')->getManager('telconet_soporte');
        $this->emInfraestructura  = $this->container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSeguridad        = $this->container->get('doctrine')->getManager('telconet_seguridad');
        $this->emComercial        = $this->container->get('doctrine')->getManager('telconet');
        $this->emComunicacion     = $this->container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emGeneral          = $this->container->get('doctrine')->getManager('telconet_general');
        $this->emNaf              = $this->container->get('doctrine')->getManager('telconet_naf');
        $this->host               = $this->container->getParameter('host');
        $this->pathTelcos         = $this->container->getParameter('path_telcos');
        $this->pathParameters     = $this->container->getParameter('path_parameters');
        $this->serviceEnvioPlantilla = $this->container->get('soporte.EnvioPlantilla');
        $this->serviceInfoServicioTecnico = $this->container->get('tecnico.InfoServicioTecnico');
        $this->networkingScripts     = $this->container->get('tecnico.NetworkingScripts');
        $this->strUrlValidarIpProvinciasWs  = $this->container->getParameter('url_valida_ip');
        $this->serviceRestClient            = $this->container->get('schema.RestClient');
        $this->serviceUtil                  = $this->container->get('schema.Util');
    }
        
    /*
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 04-05-2016
     */
    public function setDependencies(MigracionHuaweiService $migracionHuawei,
                                    InfoServicioTecnicoService $servicioGeneral,
                                    UtilService $utilServicio,
                                    Container $objContainer)
    {
        $this->migracionHuawei      = $migracionHuawei;
        $this->servicioGeneral      = $servicioGeneral;
        $this->utilServicio         = $utilServicio;
        $this->serviceCambioPlan    = $objContainer->get('tecnico.InfoCambiarPlan');
        $this->serviceCliente       = $objContainer->get('comercial.Cliente');
    }
    
    /**
     * Documentación para el método 'getIpsDisponiblePoolOlt'.
     *
     * Obtiene ip que deban asignarse automaticamente por el sistema
     * 
     * @param integer $nro
     * @param integer $id_elemento
     * @param integer $id_servicio
     * @param integer $id_punto
     * @param string  $esPlan
     * @param integer $id_plan
     * @param string  $esAsignacionRed
     * 
     * @return array.
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 24-03-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 02-05-2015    Se agregan validaciones de perfiles de planes de cliente para 
     *                            realizar la asignacion correcta dependiendo del pool correspondiente
     */
    public function getIpsDisponiblePoolOlt($nro, $id_elemento, $id_servicio, $id_punto, $esPlan, $id_plan, $esAsignacionRed = 'NO')
    {
        $em    = $this->emComercial;
        $emInf = $this->emInfraestructura;

        $arrayResponse        = array();
        $arrayResponse['ips'] = array();
        $IpsFaltantes         = $nro;

        $em->getConnection()->beginTransaction();
        $emInf->getConnection()->beginTransaction();

        try
        {

            $tipoNegocio = $em->getRepository("schemaBundle:InfoPunto")->getTipoNegocioByPuntoId($id_punto);
            $plan        = $tipoNegocio;

            //validacion del plan de edicion limitada con tipo de negocio HOME
            if($id_plan > 0)
            {
                $entityPlanCab      = $em->getRepository('schemaBundle:InfoPlanCab')->find($id_plan);
                $admiCaracteristica = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                         ->findOneBy(array("descripcionCaracteristica" => "EDICION LIMITADA", "estado" => "Activo"));
                $infoPlanCaracteristica = $em->getRepository('schemaBundle:InfoPlanCaracteristica')
                                             ->findOneBy(
                                                         array(
                                                               "planId" => $id_plan,
                                                               "caracteristicaId" => $admiCaracteristica->getId(),
                                                               "valor" => "SI",
                                                               "estado" => $entityPlanCab->getEstado()
                                                              )
                                                        );
                if($infoPlanCaracteristica)
                {
                    $tipoNegocio = "PRO";
                    $plan        = $tipoNegocio;
                }
            }


            if($tipoNegocio == "HOME")
            {
                $arrayResponse['error'] = "Tipo de Negocio <b>HOME</b> no permitido para Ip FIja. Favor notificar a Sistemas";
                return $arrayResponse;
            }

            $perfil = $em->getRepository("schemaBundle:InfoPlanCab")->getPerfilByPlanIdAndPuntoId($esPlan, $id_plan, $id_punto);
            if(strpos($perfil, 'Error') !== false)
            {
                $arrayResponse['error'] = $perfil;
                return $arrayResponse;
            }
            //Se recupera producto internet activo
            $objProductoInternet = $em->getRepository('schemaBundle:AdmiProducto')->findOneBy(array("esPreferencia"  => "SI",
                                                                                                     "nombreTecnico" => "INTERNET",
                                                                                                     "empresaCod"    => '18',
                                                                                                     "estado"        => "Activo"));
            //Se obtiene servicio internet del punto
            $objPunto                    = $em->getRepository("schemaBundle:InfoPunto")->find($id_punto);
            $objServicioInternet         = $this->migracionHuawei->getServicioInternetEnPunto($objPunto, $objProductoInternet);  
            
            /* En caso de tener un servicio de internet activo se procede a realizar la validacion
             * de perfiles del cliente del cual obtener las ips requeridas
             */
            if($objServicioInternet)
            {
                $strEsUav = $this->getCaracteristicaPorPlan($objServicioInternet->getPlanId()->getId());
                
                if ($esAsignacionRed == "SI"  && strtolower($esPlan) == 'no' && $strEsUav == "SI" && $tipoNegocio == "PYME")
                {
                    $objProdCaractIndiceCliente  = $this->servicioGeneral
                                                        ->getServicioProductoCaracteristica($objServicioInternet, "PERFIL", $objProductoInternet);
                    if ($objProdCaractIndiceCliente)
                    {
                        if (substr($perfil,0,strlen($perfil)-2) !=
                            substr($objProdCaractIndiceCliente->getValor(),0,strlen($objProdCaractIndiceCliente->getValor())-2))
                        {
                            $perfil = $objProdCaractIndiceCliente->getValor();
                        }
                    }
                    else
                    {
                        $arrayResponse['error'] = "El servicio de internet Activo no tiene registrado una caracteristica PERFIL,".
                                                  " Favor notificar a Sistemas para realizar la regularización.";
                        return $arrayResponse;
                    }
                }
            }
            if(trim($id_elemento) == "" || $id_elemento == null || $id_elemento == 'null')
            {
                $id_elemento = $em->getRepository("schemaBundle:InfoElemento")->getElementoParaPerfil($id_servicio, $esPlan, $id_punto);
                if(strpos($id_elemento, 'Error') !== false)
                {
                    $arrayResponse['error'] = $id_elemento;
                    return $arrayResponse;
                }
            }


            $perfilIpFija = $emInf->getRepository("schemaBundle:InfoDetalleElemento")
                                  ->getPerfilIpFijaByElementoIdAndPerfilPlan($id_elemento, $tipoNegocio, $perfil);
            if(is_string($perfilIpFija))
            {
                if(strpos($perfilIpFija, 'Error') !== false)
                {
                    $arrayResponse['error'] = $perfilIpFija;
                    return $arrayResponse;
                }
            }

            $infoDetElems = $emInf->getRepository("schemaBundle:InfoDetalleElemento")
                                  ->getPoolIpsByElementoANdTipoNegocioAndPerfilIpfija($id_elemento, $tipoNegocio, $perfilIpFija, $perfil);
            if(is_string($infoDetElems))
            {
                $arrayResponse['error'] = $infoDetElems;
                return $arrayResponse;
            }

            //busco los pools que tengan el perfil y el plan indicado
            foreach($infoDetElems as $infoDetElem)
            {

                $info_subred = $emInf->getRepository('schemaBundle:InfoSubred')->find($infoDetElem->getParent()->getDetalleValor());

                if($info_subred->getEstado() == 'A')
                {
                    $IpsFaltantes = $nro;

                    unset($arrayResponse['ips']);
                    $arrayResponse['ips'] = array();
                    $ipInicial            = $info_subred->getIpInicial();
                    $ipFinal              = $info_subred->getIpFinal();
                    $ipInicialArr         = explode(".", $ipInicial);
                    $ipFinalArr           = explode(".", $ipFinal);

                    if(trim($ipInicialArr[2]) == trim($ipFinalArr[2]))
                    {
                        for($i = $ipInicialArr[3]; $i <= $ipFinalArr[3]; $i++)
                        {
                            $ipPool      = $ipInicialArr[0] . '.' . $ipInicialArr[1] . '.' . $ipInicialArr[2] . '.' . $i;

                            $ipExistente = $emInf->getRepository('schemaBundle:InfoIp')
                                                     ->getIpExistente($ipPool);

                            if(!$ipExistente)
                            {
                                $arrayIp                = array();
                                $arrayIp['ip']          = $ipPool;
                                $arrayIp['tipo']        = 'FIJA';
                                $arrayIp['scope']       = "";
                                $arrayResponse['ips'][] = $arrayIp;

                                $IpsFaltantes -= 1;
                            }

                            if($IpsFaltantes == 0)
                            {
                                break 2;
                            }
                        }
                    }
                }
            }

            $arrayResponse['faltantes'] = $IpsFaltantes;
            $arrayResponse['perfil']    = $perfil;
            $arrayResponse['plan']      = $plan;
            $arrayResponse['elemento']  = $id_elemento;
            if($IpsFaltantes > 0)
                $arrayResponse['error'] = "Ips Faltantes " . $IpsFaltantes . 
                                          ". Favor solicitar a GEPON ingresar un Nuevo Pool de Ips para el paquete <b>" . $perfil . "</b>";

            $em->getConnection()->commit();
            $emInf->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            // Rollback the failed transaction attempt
            $arrayResponse['error'] = $e->getMessage();
            $em->getConnection()->rollback();
            $emInf->getConnection()->rollback();
        }

        return $arrayResponse;
    }
    
    /**
     * Documentación para el método 'getIpsDisponiblePoolOlt'.
     *
     * Obtiene ip que deban asignarse automaticamente por el sistema
     * 
     * @param integer $nro
     * @param integer $id_elemento
     * @param integer $id_servicio
     * @param integer $id_punto
     * @param string  $esPlan
     * @param integer $id_plan
     * 
     * @return array.
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 26-03-2015
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 23-06-2017 - Correccion de acuedo a observaciones dadas por SonarQube
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.2 11-02-2021 - Si el servicio es bajo Red GPON debe generar IP Privadas
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.3 09-11-2021 - Se agregó validación para que se inserte característica de tipo de enrutamiento cuando se realice
     *                           un cambio de puerto a un servicio ISB que tiene Ip Fija pública
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 27-01-2022 Se corrige problema presentado al hacer cambio de línea pon cuando se asignan ips de diferentes scopes en 
     *                         una misma asignación de un servicio. Debido a que se necesita un cambio del lado de middleware para permitir 
     *                         asignación de ips de diferentes scopes, se setea el número de ips faltantes en cada scope, para asegurar que las
     *                         ips  asignadas sean de un mismo scope ya que middleware supone que el scope de una ip fija es el mismo que el de
     *                         las adicionales.
     * 
     */
    public function getIpsDisponibleScopeOlt($nro, $id_elemento, $id_servicio, $id_punto, $esPlan, $id_plan)
    {
        $em                     = $this->emComercial;
        $emInf                  = $this->emInfraestructura;
        $arrayResponse          = array();
        $arrayResponse['ips']   = array();
        $IpsFaltantes           = $nro;
        $arrayResponse['error'] = null;
        $em->getConnection()->beginTransaction();
        $emInf->getConnection()->beginTransaction();
        $intIdServicio = $id_servicio;
        $strUsuarioCreacion = 'telcos+';

        try
        {
            
            $modeloElementoCnr = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                      ->findOneBy(array("nombreModeloElemento"  => "CNR UCS C220",
                                                        "estado"                => "Activo"));

            //obtener elemento cnr
            $objElementoCnr    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                      ->findOneBy(array("modeloElementoId"=>$modeloElementoCnr->getId()));
                   
            $entityElementoOlt = $emInf->getRepository('schemaBundle:InfoElemento')->find($id_elemento);
            $tipoNegocio       = $em->getRepository("schemaBundle:InfoPunto")->getTipoNegocioByPuntoId($id_punto);
            $plan              = $tipoNegocio;

            //validacion del plan de edicion limitada con tipo de negocio HOME
            if($id_plan > 0)
            {
                $entityPlanCab          = $em->getRepository('schemaBundle:InfoPlanCab')->find($id_plan);
                $admiCaracteristica     = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                             ->findOneBy(array("descripcionCaracteristica" => "EDICION LIMITADA", "estado" => "Activo"));
                $infoPlanCaracteristica = $em->getRepository('schemaBundle:InfoPlanCaracteristica')
                                             ->findOneBy(
                                                        array(
                                                            "planId"           => $id_plan,
                                                            "caracteristicaId" => $admiCaracteristica->getId(),
                                                            "valor"            => "SI",
                                                            "estado"           => $entityPlanCab->getEstado()
                                                             )
                                                        );
                if($infoPlanCaracteristica)
                {
                    $tipoNegocio = "PRO";
                    $plan        = $tipoNegocio;
                }
            }


            if($tipoNegocio == "HOME")
            {
                $arrayResponse['faltantes'] = $nro;
                $arrayResponse['error'] = "Tipo de Negocio <b>HOME</b> no permitido para Ip FIja. Favor notificar a Sistemas";
                return $arrayResponse;
            }
            
            //Si el servicio es bajo Red GPON debe aprovisionar la ip privada
            $strIp         = '';
            $boolIpPrivada = false;
            $objServicio = $em->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            if (is_object($objServicio))
            {
                //Si el servicio que se va a trasladar tiene IP fija y el parametro de ese producto está activado para
                //aprovisionamiento con Ip Privada se debe crear la característica para IP Privada
                                                                                    
                $objParametroCabIpPrivada = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                      ->findOneBy(array('nombreParametro' => 'IP_PRIVADA_FIJA_GPON',
                                                                        'estado'            => 'Activo'));
                if (is_object($objParametroCabIpPrivada))
                {
                    $arrayParDetIpPrivada = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->findBy(array('parametroId'   => $objParametroCabIpPrivada->getId(),
                                                                     'estado'        => 'Activo'));
                    if (is_array($arrayParDetIpPrivada) && !empty($arrayParDetIpPrivada))
                    {
                        $arrayIpPrivada = explode(",",$arrayParDetIpPrivada[0]->getValor1());
                    }
                }
                    
                if($id_plan <= 0 && in_array($objServicio->getProductoId()->getId(),$arrayIpPrivada))
                {
                    $objCaracteristicaIpPrivada = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                  ->findOneBy(array( "descripcionCaracteristica" => "TIPO_ENRUTAMIENTO"));
                    if(is_object($objCaracteristicaIpPrivada))
                    {
                        $objProducto = $em->getRepository('schemaBundle:AdmiProducto')->find($objServicio->getProductoId()->getId());

                        $objProdCaracteristicaIpPrivada = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                             ->findOneBy(array( "productoId"       => $objProducto->getId(), 
                                                                                "caracteristicaId" => $objCaracteristicaIpPrivada->getId()
                                                                              )
                                                                        );
                        if(is_object($objProdCaracteristicaIpPrivada))
                        {
                            $objInfoServicioProdCaractIpPrivada = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                             ->findOneBy(array( "servicioId"         => $objServicio->getId(), 
                                                                         "productoCaracterisiticaId" => $objProdCaracteristicaIpPrivada->getId()
                                                                          )
                                                                    );
                            if (is_object($objInfoServicioProdCaractIpPrivada))
                            {
                                $strIp = ($objInfoServicioProdCaractIpPrivada)?$objInfoServicioProdCaractIpPrivada->getValor():"";
                                $boolIpPrivada = true;
                            }
                            else
                            {
                                //agregamos la característica de Ip privada
                                $objServicioProdCaractCopy = new InfoServicioProdCaract();
                                $objServicioProdCaractCopy->setServicioId($intIdServicio);
                                $objServicioProdCaractCopy->setFeCreacion(new \DateTime('now'));
                                $objServicioProdCaractCopy->setUsrCreacion($strUsuarioCreacion);
                                $objServicioProdCaractCopy->setProductoCaracterisiticaId($objProdCaracteristicaIpPrivada->getId());
                                $objServicioProdCaractCopy->setValor('Privada');
                                $objServicioProdCaractCopy->setEstado('Activo');
                                $this->emComercial->persist($objServicioProdCaractCopy);
                                $this->emComercial->flush();
                                $strIp = 'Privada';
                                $boolIpPrivada = true;
                            }
                        }
                    }
                }
            }
            
            //Si esta vacía la variable $strIp por default es Fija
            if(empty($strIp))
            {
                $strIp = 'Fija';
            }
            //Se llena el parametro con los valores para realizar la consulta
            $arrayParametros = array(   "intIdElemento"         => $entityElementoOlt->getId(),
                                         "strIp"                => $strIp
                                     );
            //se debe recuperar el scope activo de tipo fijo para generar ips con respecto al rango que esta descrito en la subred
            $arrayScopeOlt       = $emInf->getRepository('schemaBundle:InfoElemento')
                                         ->getScopeOltIpFija($arrayParametros);
            
            if (!$arrayScopeOlt)
            {   
                if($boolIpPrivada)
                {
                    $arrayResponse['faltantes'] = $nro;
                    $arrayResponse['error'] = "Servicio requiere " . $nro . " Ip Privada" .
                                          ". Favor solicitar a GEPON ingresar un Nuevo Scope para el Olt <b>" .
                                          $entityElementoOlt->getNombreElemento() . "</b>";
                }
                else
                {
                    $arrayResponse['faltantes'] = $nro;
                    $arrayResponse['error'] = "Servicio requiere " . $nro . " Ip Fija" .
                                          ". Favor solicitar a GEPON ingresar un Nuevo Scope para el Olt <b>" .
                                          $entityElementoOlt->getNombreElemento() . "</b>";
                }
                return $arrayResponse;
            }
            else
            {
                $IpsFaltantes = $nro;
                for($j = 0; $j < count($arrayScopeOlt); $j++)
                {
                    //verifica si existe scope en CNR
                    $objJsonScopeCnr = $this->ejecutarScriptCnr($objElementoCnr, 
                                                                $modeloElementoCnr, 
                                                                $arrayScopeOlt[$j]['NOMBRE_SCOPE'], 
                                                                'listarScopes', 
                                                                'verificarExisteScope');
                    
                    if ($objJsonScopeCnr->status != 'OK')
                    {
                        $arrayResponse['faltantes'] = $IpsFaltantes;
                        $arrayResponse['error'] = "Error: ".$objJsonScopeCnr->mensaje;
                        return $arrayResponse;
                    }                    
                    
                    $ipInicial = $arrayScopeOlt[$j]['IP_SCOPE_INI'];
                    $ipFinal   = $arrayScopeOlt[$j]['IP_SCOPE_FIN'];
                    
                    //verifica si existe scope en CNR
                    $objJsonIpsCnr = $this->ejecutarScriptCnr($objElementoCnr, 
                                                              $modeloElementoCnr, 
                                                              $arrayScopeOlt[$j]['NOMBRE_SCOPE'], 
                                                              'obtenerIpsUsadasScope', 
                                                              'getIpsUsadasScope');
                    
                    if ($objJsonIpsCnr->status != 'OK')
                    {
                        $arrayResponse['faltantes'] = $IpsFaltantes;
                        $arrayResponse['error'] = "Error: ".$objJsonIpsCnr->mensaje;
                        return $arrayResponse;
                    }   
                    $strListadoIps = $objJsonIpsCnr->mensaje;
                    
                    unset($arrayResponse['ips']);
                    $arrayResponse['ips'] = array();
                    $IpsFaltantes         = $nro;
                    $ipInicialArr         = explode(".", $ipInicial);
                    $ipFinalArr           = explode(".", $ipFinal);
                    $ipsCnrScope          = explode(",", $strListadoIps);
                    
                    for($k = $ipInicialArr[2]; $k <= $ipFinalArr[2]; $k++)
                    {
                        for($i = $ipInicialArr[3]; $i <= $ipFinalArr[3]; $i++)
                        {
                            $ipPool = $ipInicialArr[0] . '.' . $ipInicialArr[1] . '.' . $k . '.' . $i;

                            if(!in_array($ipPool, $ipsCnrScope))
                            {
                                $ipExistente = $emInf->getRepository('schemaBundle:InfoIp')
                                                     ->getIpExistente($ipPool);
                                if(!$ipExistente)
                                {
                                    $arrayIp                = array();
                                    $arrayIp['ip']          = $ipPool;
                                    $arrayIp['tipo']        = strtoupper($strIp);
                                    $arrayIp['scope']       = $arrayScopeOlt[$j]['NOMBRE_SCOPE'];
                                    $arrayResponse['ips'][] = $arrayIp;
                                    $IpsFaltantes          -= 1;
                                }
                            }
                            if($IpsFaltantes == 0)
                            {
                                break 3;
                            }
                        }
                    }
                }
            }


            $arrayResponse['faltantes'] = $IpsFaltantes;
            $arrayResponse['elemento']  = $id_elemento;
            if($IpsFaltantes > 0)
            {
                $arrayResponse['error'] = "Servicio requiere " . $nro . " Ip Fija" .
                                           ". Favor solicitar a GEPON ingresar un Nuevo Scope para el Olt <b>" .
                                           $entityElementoOlt->getNombreElemento() . "</b>";
            }    

            $em->getConnection()->commit();
            $emInf->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            // Rollback the failed transaction attempt
            $arrayResponse['error'] = $e->getMessage();
            $em->getConnection()->rollback();
            $emInf->getConnection()->rollback();
        }

        return $arrayResponse;
    }
    
    /**
     * Funcion que sirve para obtener ejecutar script de manera generica
     * 
     * @author Creado: Francisco Adum <fadum@telconet.ec>
     * @version 1.0 6-3-2015
     * @param InfoServicioTecnico       $servicioTecnico
     * @param String                    $usuario
     * @param String                    $datos
     * @param int                       $idDocumento
     * @param String                    $accion
     */
    public function ejecutarScriptCnr($elemento, $modeloElementoCnr, $datos, $accionScript, $accionComando)
    {
        //*OBTENER SCRIPT--------------------------------------------------------*/
        $scriptArray = $this->servicioGeneral->obtenerArregloScript($accionScript,$modeloElementoCnr);
        $idDocumento= $scriptArray[0]->idDocumento;
        $usuario= $scriptArray[0]->usuario;
        //*----------------------------------------------------------------------*/
        
        $comando = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_datos.jar '".
            $this->host."' '".$accionComando."' '".$elemento->getId()."' '".$usuario."' 'puerto' "
                       . "'".$idDocumento."' '".$datos."' '".$this->pathParameters."'";
        $salida         = shell_exec($comando);
        $pos            = strpos($salida, "{"); 
        $jsonObj        = substr($salida, $pos);
        $resultadJson   = json_decode($jsonObj);
        
        return $resultadJson;
    }
    
    /**
     * Documentación para el método 'getScopeDisponiblesOlt'.
     *
     * Obtiene scopes disponibles por elemento
     * 
     * @param integer $id_elemento
     * @return array.
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 09-04-2015
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 23-06-2017 - Cambio establecido por observacion de SonarQube
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.2 12-02-2021 - Parametrizar la ip y si es vacía por default tomará el valor de Fija
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 27-01-2022 Se eliminan líneas puestas de manera errónea por desarrollo anterior
     */
    public function getScopeDisponiblesOlt($id_elemento)
    {
        $emInf                = $this->emInfraestructura;
        $arrayResponse        = array();
        $strIp                = '';
        try
        {
            $modeloElementoCnr = $emInf->getRepository('schemaBundle:AdmiModeloElemento')
                                  ->findOneBy(array("nombreModeloElemento"  => "CNR UCS C220",
                                                    "estado"                => "Activo"));
            //obtener elemento cnr
            $objElementoCnr    = $emInf->getRepository('schemaBundle:InfoElemento')
                                       ->findOneBy(array("modeloElementoId"=>$modeloElementoCnr->getId()));
                   
            $entityElementoOlt = $emInf->getRepository('schemaBundle:InfoElemento')->find($id_elemento);
            
            if(empty($strIp))
            {
                $strIp = 'Fija';
            }
            //Se llena el parametro con los valores para realizar la consulta
            $arrayParametros = array(   "intIdElemento"         => $entityElementoOlt->getId(),
                                         "strIp"                => $strIp
                                     );
            
            //se debe recuperar el scope activo de tipo fijo para generar ips con respecto al rango que esta descrito en la subred
            $arrayScopeOlt     = $emInf->getRepository('schemaBundle:InfoElemento')
                                       ->getScopeOltIpFija($arrayParametros);
            
            if (!$arrayScopeOlt)
            {   
                $arrayResponse['error'] = "Servicio requiere " . $nro . " Ip Fija" .
                                          ". Favor solicitar a GEPON ingresar un Nuevo Scope para el Olt <b>" .
                                          $entityElementoOlt->getNombreElemento() . "</b>";
                return $arrayResponse;
            }
            else
            {
                for($j = 0; $j < count($arrayScopeOlt); $j++)
                {
                    //verifica si existe scope en CNR
                    $objJsonScopeCnr = $this->ejecutarScriptCnr($objElementoCnr, 
                                                                $modeloElementoCnr, 
                                                                $arrayScopeOlt[$j]['NOMBRE_SCOPE'], 
                                                                'listarScopes', 
                                                                'verificarExisteScope');
                    
                    if ($objJsonScopeCnr->status != 'OK')
                    {
                        $arrayResponse['error'] = "Error: ".$objJsonScopeCnr->mensaje;
                        return $arrayResponse;
                    }                    
                    
                    $ipInicial = $arrayScopeOlt[$j]['IP_SCOPE_INI'];
                    $ipFinal   = $arrayScopeOlt[$j]['IP_SCOPE_FIN'];
                    
                    //verifica si existe scope en CNR
                    $objJsonIpsCnr = $this->ejecutarScriptCnr($objElementoCnr, 
                                                              $modeloElementoCnr, 
                                                              $arrayScopeOlt[$j]['NOMBRE_SCOPE'], 
                                                              'obtenerIpsUsadasScope', 
                                                              'getIpsUsadasScope');
                    
                    if ($objJsonIpsCnr->status != 'OK')
                    {
                        $arrayResponse['error'] = "Error: ".$objJsonIpsCnr->mensaje;
                        return $arrayResponse;
                    }    
                    $strListadoIps = $objJsonIpsCnr->mensaje;
                    
                    $arrayResponse ['registros'][] = array(
                                                            "ipInicio"   => $ipInicial,
                                                            "ipFin"      => $ipFinal,
                                                            "ipOcupadas" => $strListadoIps,
                                                            "scope"      => $arrayScopeOlt[$j]['NOMBRE_SCOPE']
                                                          );
                    $arrayResponse ['error']       = "";
                }
            }
        }
        catch(\Exception $e)
        {
            // Rollback the failed transaction attempt
            $arrayResponse['error'] = $e->getMessage();
        }

        return $arrayResponse;
    }
    
    /**
     * Documentación para el método 'getIpsDisponiblesPorScopes'.
     *
     * Obtiene ip que deban asignarse automaticamente por el sistema
     * 
     * @param integer $nro
     * @param array $arrayIpsScopes
     * 
     * @return array.
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 09-04-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 27-01-2022 Se corrige problema presentado al hacer cambio de línea pon cuando se asignan ips de diferentes scopes en 
     *                         una misma asignación de un servicio ya que la programación es la misma pero getIpsDisponiblesPorScopes 
     *                         es usada en migración.
     *                         Con la correción se intenta obtener las ips de un mismo scope ya que actualmente al obtener las ips y enviárselas
     *                         a middleware solo se envía mac, ip y id_servicio y no el scope por lo que del lado de middleware siempre se asume 
     *                         que el parámetro enviado como scope nuevo será el mismo tanto para la ip del plan como para adicionales.
     */
    public function getIpsDisponiblesPorScopes( $nro, $arrayIpsScopes )
    {
        $emInf                = $this->emInfraestructura;
        $arrayResponse        = array();
        $arrayResponse['ips'] = array();
        $IpsFaltantes         = $nro;
        
        try
        {
            for($j = 0; $j < count($arrayIpsScopes['registros']); $j++)
            {
                $ipInicial            = $arrayIpsScopes['registros'][$j]['ipInicio'];
                $ipFinal              = $arrayIpsScopes['registros'][$j]['ipFin'];
                $strListadoIps        = $arrayIpsScopes['registros'][$j]['ipOcupadas'];
                unset($arrayResponse['ips']);
                $arrayResponse['ips'] = array();
                $IpsFaltantes         = $nro;
                $ipInicialArr         = explode(".", $ipInicial);
                $ipFinalArr           = explode(".", $ipFinal);
                $ipsCnrScope          = explode(",", $strListadoIps);

                for($k = $ipInicialArr[2]; $k <= $ipFinalArr[2]; $k++)
                {
                    for($i = $ipInicialArr[3]; $i <= $ipFinalArr[3]; $i++)
                    {
                        $ipPool = $ipInicialArr[0] . '.' . $ipInicialArr[1] . '.' . $k . '.' . $i;

                        if(!in_array($ipPool, $ipsCnrScope))
                        {
                            $ipExistente = $emInf->getRepository('schemaBundle:InfoIp')
                                                 ->getIpExistente($ipPool);

                            if(!$ipExistente)
                            {
                                $arrayIp                = array();
                                $arrayIp['ip']          = $ipPool;
                                $arrayIp['tipo']        = 'FIJA';
                                $arrayIp['scope']       = $arrayIpsScopes['registros'][$j]['scope'];
                                $arrayResponse['ips'][] = $arrayIp;
                                $IpsFaltantes          -= 1;
                            }
                        }
                        if($IpsFaltantes == 0)
                        {
                            break 3;
                        }
                    }
                }

            }
            $arrayResponse['faltantes'] = $IpsFaltantes;
        }
        catch(\Exception $e)
        {
            // Rollback the failed transaction attempt
            $arrayResponse['faltantes'] = $IpsFaltantes;
        }

        return $arrayResponse;
    }
    
    /**
     * Funcion que sirve para obtener el objeto servicio tecnico por id punto
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 4-08-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 06-01-2016 Se cambia el metodo a tipo PUBLICO para poder usarlo desde un controller
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 04-02-2018 Se agrega parámetro de nombre técnico para diferenciar servicios Internet Small Business y TelcoHome 
     *                          ya que pueden pertenecer a un mismo login y ambos pertenecen a TN
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 05-05-2020 Se envía un arreglo de parámetros a la función getServicioTecnicoByPuntoId debido a los cambios realizados 
     *                          por la reestucturación de servicios Small Business
     * 
     * @param array $arrayParametros [
     *                                  "intIdPunto"            => id del punto,
     *                                  "intIdProdInternet"     => id del producto Internet
     *                                ]
     * 
     * @return InfoServicioTecnico $mixServicioTecnico
     */
    public function getServicioTecnicoByPuntoId($arrayParametros) 
    {
        $intIdServicio  = $this->emComercial->getRepository('schemaBundle:InfoServicio')->getServicioByPuntoId($arrayParametros);
        try
        {
            if(is_numeric($intIdServicio))
            {
                $mixServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($intIdServicio);
            }
            else
            {
                $mixServicioTecnico = $intIdServicio;
            }
            return $mixServicioTecnico;
        }
        catch (\Doctrine\ORM\NoResultException $e)
        {
            return 'servicio tecnico no encontrado';
        }
    }
    
    /**
     * Funcion que sirve para asignar recursos de red (ips adicionales, 
     * pymes, migracion tellion a huawei, traslados) para los servicios
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 4-08-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 07-12-2015 Se agrega obtencion de nuevos perfil segun el tipo de aprovisionamiento
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 30-12-2015 Se agrega validacion de estado del servicio al momento de ejecutar la accion
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 26-10-2015 Se agrega validacion de detalle elemento para asignar nuevos perfiles a cliente en OLT ya migrados
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.4 22-03-2016 Se setea nuevo olt Hw en data tecnica de cliente para migraciones Tellion -> Huawei y poder 
     *                         recuperar perfil equivalente en caso de ser un Olt hw que trabaje con nuevos planes
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.5 13-04-2016 Se modifica funcion que recupera equivalencia de perfiles de planes para poder
     *                         aprovisionar a clientes con planes UAV
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.6 27-04-2016 Se modifica proceso y se agregan validaciones para migracion de clientes TELLION CNR
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.7 04-05-2016 Se agregan validaciones de perfiles de clientes pyme Tellion Pool con planes UAV al asignar recursos a servicios de 
     *                         Ips adicionales
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.8 20-12-2016 Se agrega validación de caracteristica SCOPE al momento de asignar ip al servicio procesado
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.9 02-07-2018 Se agregan validaciones para asignar recursos de red de servicios con tecnología ZTE
     * @since 1.8
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.10 03-12-2018 Se agregan validaciones para gestionar productos de la empresa TNP
     * @since 1.9
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.11 26-06-2019 Se modifica el envío de parámetros a la función getServiciosIpbyPunto por cambios necesarios en la migración de
     *                           servicios Small Business
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.12 05-05-2020 Se envía un arreglo de parámetros a la función getServicioTecnicoByPuntoId debido a los cambios realizados 
     *                           por la reestucturación de servicios Small Business
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.13 24-02-2023 Se Agrega Bandera con el prefijo de la empresa ECUANET para que siga mismo flujo que MD.
     * 
     * @param array $arrayParametros
     * @return array $respuestaFinal
     */
    public function asignarRecursosRed($arrayParametros)
    {
        $id                     = $arrayParametros['id'];
        $producto               = $arrayParametros['producto'];             //sin usar
        $nombreTecnico          = $arrayParametros['nombreTecnico'];        //solo nombre tecnico para plan que tiene un producto IP
        $idElemento             = $arrayParametros['elementoId'];
        $idInterface            = $arrayParametros['interfaceId'];
        $vci                    = $arrayParametros['vci'];                  //caracteristica para cobre
        $jsonCaracteristicas    = $arrayParametros['datosIps'];             //json de ips
        $strTipoSolicitud       = $arrayParametros['tipoSolicitud'];
        $idSplitter             = $arrayParametros['idSplitter'];
        $idSplitterHuawei       = $arrayParametros['idSplitterHuawei'];     //id elemento conector para migracion
        $idOlt                  = $arrayParametros['idOlt'];                //id elemento para migracion
        $idInterfaceOlt         = $arrayParametros['idInterfaceOlt'];       //id interface elemento para migracion
        $idInterfaceSplitter    = $arrayParametros['interfaceSplitterId'];
        $strMarcaOlt            = $arrayParametros['marcaOlt'];
        $idEmpresa              = $arrayParametros['idEmpresa'];
        $prefijoEmpresa         = $arrayParametros['prefijoEmpresa'];
        $intCantidadIpsBase     = $arrayParametros['cantidadRegistrosIps']; //ips reservadas en migracion
        $strEsPlan              = $arrayParametros['esPlan'];
        $usrCreacion            = $arrayParametros['usrCreacion'];
        $ipCreacion             = $arrayParametros['ipCreacion'];
        
        //Se agregan variable para guardar rastro de ip asignada
        $strListaIps            = '';
        $intContador            = 0;
        $banderaMaxId           = 0;
        $nombreTipoNegocioPlan  = '';
        $sigue                  = true;
        $mensaje                = "Error: ";
        $strPerfilEquivalente   = "";
        $strAprovisionamiento   = "";
        $strEsIp                = "NO";
        $objProductoIp          = null;
        $entityDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($id);
        
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        
        try
        {
            if (!$intCantidadIpsBase || $intCantidadIpsBase == null)
            {
                $intCantidadIpsBase = 0;
            }
            
            if($entityDetalleSolicitud)
            {
                //datos extras
                $entityServicio        = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                           ->findOneById($entityDetalleSolicitud->getServicioId());
                // se agrega validacion del estado del servicio para bloquear operaciones incorrectas
                if( $entityServicio->getEstado() == "Activo" && 
                    $strTipoSolicitud != 'SOLICITUD MIGRACION' )
                {
                    $respuestaFinal = array('status' => "ERROR", 
                                            'mensaje'=> "El servicio Actualmente se encuentra con estado Activo, no es posible Asignar Recursos.");
                    return $respuestaFinal;
                }
                $idServicio            = $entityServicio->getId();
                $entityServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($idServicio);

                $productoInternetDedicado = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                               ->findOneBy(array("empresaCod" => $idEmpresa, "descripcionProducto" => "INTERNET DEDICADO", 
                                                                 "estado" => "Activo"));
                
                //se define el tipo de aprovisionamiento de ips que tiene el olt
                if ($strTipoSolicitud != 'SOLICITUD MIGRACION')
                {
                    if (!$entityServicioTecnico->getElementoId())
                    {
                        $strEsIp                = "SI";
                        $objServicioTecnicoPlan = $this->getServicioTecnicoByPuntoId(array("intIdPunto" => $entityServicio->getPuntoId()->getId()));
                        if (is_object($objServicioTecnicoPlan))
                        {
                            $entityServicioTecnico->setElementoId($objServicioTecnicoPlan->getElementoId());
                        }
                    }
                    else
                    {
                        if ($nombreTecnico == "IP")
                        {
                            if ($entityServicio->getPlanId())
                            {
                                $arrayPlanDetServicio = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                                     ->findBy(array( "planId" => $entityServicio->getPlanId()->getId()));
                                if(count($arrayPlanDetServicio)==1)
                                {
                                    $strEsIp = "SI";
                                }
                            }
                            else
                            {
                                 $strEsIp = "SI";
                            }
                        }
                    }
                    $strAprovisionamiento = $this->geTipoAprovisionamiento($entityServicioTecnico->getElementoId());
                }
                else
                {
                   /* En caso de ser migración, se setea nuevo elemento HW para poder gestionar Nuevos escenarios de migracion
                    * generados por cambio de plan masivo a nuevos planes
                    */
                   $strAprovAnt          = $this->geTipoAprovisionamiento($entityServicioTecnico->getElementoId());
                   if ($strAprovAnt == "CNR")
                   {
                     $intCantidadIpsBase = 1;  
                   }
                   $strAprovisionamiento = "CNR" ;
                   $entityServicioTecnico->setElementoId($idOlt);
                }
                $nombreTipoNegocioPlan = $entityServicio->getPuntoId()->getTipoNegocioId()->getNombreTipoNegocio();
                if ( $nombreTipoNegocioPlan!='HOME' && $nombreTipoNegocioPlan!='PYME' && $nombreTipoNegocioPlan!='PRO' )
                {
                    $nombreTipoNegocioPlan = $entityServicio->getPlanId()->getTipo();
                    if ( $nombreTipoNegocioPlan!='HOME' && $nombreTipoNegocioPlan!='PYME' && $nombreTipoNegocioPlan!='PRO' ) 
                    {
                        $mensaje = "No se puede realizar la Asignacion, el tipo de negocio no es valido!";
                        throw new \Exception($mensaje);
                    }
                }
                
                if($strEsPlan == "si" || (is_object($entityServicio->getPlanId()) && $strTipoSolicitud == 'SOLICITUD MIGRACION'))
                {
                    $arrayProdIp       = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                           ->findBy(array(  "nombreTecnico" => "IP", 
                                                                            "empresaCod"    => $idEmpresa, 
                                                                            "estado"        => "Activo"));

                    $arrayPlanDet     = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                          ->findBy(array("planId" => $entityServicio->getPlanId()->getId()));

                    $indiceProductoIp = $this->servicioGeneral->obtenerIndiceInternetEnPlanDet($arrayPlanDet, $arrayProdIp);

                    if ($indiceProductoIp!=-1)
                    {
                        $objProductoIp = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                           ->find($arrayPlanDet[$indiceProductoIp]->getProductoId());
                    }
                    else
                    {
                        $objProductoIp = null;
                    }
                }//if($strEsPlan == "si")
                else
                {
                    $objProductoIp = $entityServicio->getProductoId();
                }
              
                if($nombreTecnico != "IP")
                {
                    $tipoMedio = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                       ->find($entityServicioTecnico->getUltimaMillaId());

                    if($tipoMedio->getNombreTipoMedio() == "Radio")
                    {
                        //guardo Interface
                        $entityElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                  ->find($entityServicioTecnico->getElementoId());

                        $interfaceElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                     ->findOneBy(array( "elementoId"                => $entityElemento->getId(), 
                                                                                        "nombreInterfaceElemento"   => "wlan1"));

                        if($interfaceElemento)
                        {
                            $entityInterface = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                       ->findOneById($interfaceElemento->getId());
                            $entityServicioTecnico->setInterfaceElementoId($interfaceElemento->getId());
                            $this->emComercial->persist($entityServicioTecnico);
                            $this->emComercial->flush();
                        }
                        else
                        {
                            $sigue = false;
                            $mensaje .= "No existe WLAN1 para el RADIO.";
                            throw new \Exception($mensaje);
                        }
                    }//if($tipoMedio->getNombreTipoMedio() == "Radio")

                    if($tipoMedio->getNombreTipoMedio() == "Cobre")
                    {
                        //guardo Interface
                        $entityInterface = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->findOneById($idInterface);
                        if($idElemento > 0)
                        {
                            $entityServicioTecnico->setElementoId($idElemento);
                        }

                        $entityServicioTecnico->setInterfaceElementoId($idInterface);
                        $this->emComercial->persist($entityServicioTecnico);
                        $this->emComercial->flush();

                        $entityInterface->setEstado('reserved');
                        $this->emInfraestructura->persist($entityInterface);
                        $this->emInfraestructura->flush();

                        $sigue = true;
                    }//if($tipoMedio->getNombreTipoMedio() == "Cobre")

                    if($tipoMedio->getNombreTipoMedio() == "Fibra Optica")
                    {
                        //guardo Interface
                        $entityInterfaceSplitter = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                           ->findOneById($idInterfaceSplitter);
                        if($idSplitter > 0)
                        {
                            $entityServicioTecnico->setElementoConectorId($idSplitter);
                        }
                            
                        //se agrega codigo para liberar puerto de splitter
                        if ($entityServicioTecnico->getInterfaceElementoConectorId()!=$idInterfaceSplitter)
                        {
                            $entityInterfaceSplitterAntes = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                   ->findOneById($entityServicioTecnico->getInterfaceElementoConectorId());
                            $entityInterfaceSplitterAntes->setEstado("not connect");
                            $this->emInfraestructura->persist($entityInterfaceSplitterAntes);
                            $this->emInfraestructura->flush();                            
                        }
                        
                        $entityServicioTecnico->setInterfaceElementoConectorId($idInterfaceSplitter);
                        $this->emComercial->persist($entityServicioTecnico);
                        $this->emComercial->flush();

                        if($strTipoSolicitud != 'SOLICITUD MIGRACION')
                        {
                            $entityInterfaceSplitter->setEstado('reserved');
                        }
                        else
                        {
                            $entityInterfaceSplitter->setEstado('connected');
                        }
                        
                        $this->emInfraestructura->persist($entityInterfaceSplitter);
                        $this->emInfraestructura->flush();

                        $sigue = true;
                    }//if($tipoMedio->getNombreTipoMedio() == "Fibra Optica")

                    if($sigue)
                    {
                        // guardo la CAPACIDAD 1
                        $entityCapacidad1 = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                              ->findOneBy(array("descripcionCaracteristica" => "CAPACIDAD1", "estado" => "Activo"));
                        $prodCaractCapacidad1 = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                  ->findOneBy(array("productoId"        => $productoInternetDedicado->getId(), 
                                                                                    "caracteristicaId"  => $entityCapacidad1->getId(), 
                                                                                    "estado"            => "Activo"));

                        if($prodCaractCapacidad1)
                        {
                            $infoPlanDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                             ->findOneBy(array( "productoId"    => $productoInternetDedicado->getId(), 
                                                                                "planId"        => $entityServicio->getPlanId()));

                            $infoPlanProdCaract1 = $this->emComercial->getRepository('schemaBundle:InfoPlanProductoCaract')
                                                                 ->findOneBy(array("planDetId"                  => $infoPlanDet->getId(), 
                                                                                   "productoCaracterisiticaId"  => $prodCaractCapacidad1->getId()));

                            
                            if($infoPlanProdCaract1)
                            {
                                if($strMarcaOlt == '' || $strMarcaOlt == null )
                                {
                                    $sigue = false;
                                    $strMensaje .= "No existe marca Olt.";
                                    throw new \Exception($strMensaje);                                                                    
                                }

                                
                                if ($strTipoSolicitud == 'SOLICITUD MIGRACION' && $strMarcaOlt == 'ZTE')
                                {
                                    $arrayServProdCarac = $this->emComercial
                                                               ->getRepository('schemaBundle:InfoServicioProdCaract')
                                                               ->findBy(array( "servicioId"                => $entityServicio->getId(),
                                                                                  "productoCaracterisiticaId" => $prodCaractCapacidad1->getId(),
                                                                                  "estado"                    => "Activo"));
                                    foreach($arrayServProdCarac as $objSpcCapacidad1)
                                    {
                                        $objSpcCapacidad1->setEstado("Eliminado");
                                        $this->emComercial->persist($objSpcCapacidad1);
                                        $this->emComercial->flush();
                                    }
                                    $infoServProdCaractCapacidad1 = new InfoServicioProdCaract();
                                    $infoServProdCaractCapacidad1->setServicioId($entityServicio->getId());
                                    $infoServProdCaractCapacidad1->setProductoCaracterisiticaId($prodCaractCapacidad1->getId());
                                    $infoServProdCaractCapacidad1->setValor(trim($infoPlanProdCaract1->getValor()));
                                    $infoServProdCaractCapacidad1->setFeCreacion(new \DateTime('now'));
                                    $infoServProdCaractCapacidad1->setUsrCreacion($usrCreacion);
                                    $infoServProdCaractCapacidad1->setEstado("Activo");
                                    $this->emComercial->persist($infoServProdCaractCapacidad1);
                                    $this->emComercial->flush();
                                }
                                else if ($strTipoSolicitud != 'SOLICITUD MIGRACION' && $strMarcaOlt != 'HUAWEI')
                                {
                                    $infoServProdCaractCapacidad1 = new InfoServicioProdCaract();
                                    $infoServProdCaractCapacidad1->setServicioId($entityServicio->getId());
                                    $infoServProdCaractCapacidad1->setProductoCaracterisiticaId($prodCaractCapacidad1->getId());
                                    $infoServProdCaractCapacidad1->setValor(trim($infoPlanProdCaract1->getValor()));
                                    $infoServProdCaractCapacidad1->setFeCreacion(new \DateTime('now'));
                                    $infoServProdCaractCapacidad1->setUsrCreacion($usrCreacion);
                                    $infoServProdCaractCapacidad1->setEstado("Activo");
                                    $this->emComercial->persist($infoServProdCaractCapacidad1);
                                    $this->emComercial->flush();
                                }
                                $sigue = true;
                            }
                            else
                            {
                                $sigue = false;
                                $mensaje .= "No existe CAPACIDAD1 para el Plan del Servicio.";
                                throw new \Exception($mensaje);
                            }
                        }//if($prodCaractCapacidad1)
                        else
                        {
                            $sigue = false;
                            $mensaje .= "No existe CAPACIDAD1 para el Internet Dedicado.";
                            throw new \Exception($mensaje);
                        }
                    }//if($sigue)

                    if($sigue && $prefijoEmpresa != "TNP")
                    {
                        // guardo la CAPACIDAD 2
                        $entityCapacidad2 = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                              ->findOneBy(array("descripcionCaracteristica" => "CAPACIDAD2", "estado" => "Activo"));
                        $prodCaractCapacidad2 = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                  ->findOneBy(array("productoId"        => $productoInternetDedicado->getId(), 
                                                                                    "caracteristicaId"  => $entityCapacidad2->getId(), 
                                                                                    "estado"            => "Activo"));
                        if($prodCaractCapacidad2)
                        {
                            $infoPlanDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                             ->findOneBy(array("productoId" => $productoInternetDedicado->getId(), 
                                                                               "planId"     => $entityServicio->getPlanId()));
                            $infoPlanProdCaract2 = $this->emComercial->getRepository('schemaBundle:InfoPlanProductoCaract')
                                                                 ->findOneBy(array("planDetId"                  => $infoPlanDet->getId(), 
                                                                                   "productoCaracterisiticaId"  => $prodCaractCapacidad2->getId()));

                            if($infoPlanProdCaract2)
                            {
                                if ($strTipoSolicitud == 'SOLICITUD MIGRACION' && $strMarcaOlt == 'ZTE')
                                {
                                    
                                    $arrayServProdCarac = $this->emComercial
                                                               ->getRepository('schemaBundle:InfoServicioProdCaract')
                                                               ->findBy(array( "servicioId"                => $entityServicio->getId(),
                                                                                  "productoCaracterisiticaId" => $prodCaractCapacidad2->getId(),
                                                                                  "estado"                    => "Activo"));
                                    foreach($arrayServProdCarac as $objSpcCapacidad2)
                                    {
                                        $objSpcCapacidad2->setEstado("Eliminado");
                                        $this->emComercial->persist($objSpcCapacidad2);
                                        $this->emComercial->flush();
                                    }
                                    $infoServProdCaractCapacidad2 = new InfoServicioProdCaract();
                                    $infoServProdCaractCapacidad2->setServicioId($entityServicio->getId());
                                    $infoServProdCaractCapacidad2->setProductoCaracterisiticaId($prodCaractCapacidad2->getId());
                                    $infoServProdCaractCapacidad2->setValor(trim($infoPlanProdCaract2->getValor()));
                                    $infoServProdCaractCapacidad2->setFeCreacion(new \DateTime('now'));
                                    $infoServProdCaractCapacidad2->setUsrCreacion($usrCreacion);
                                    $infoServProdCaractCapacidad2->setEstado("Activo");
                                    $this->emComercial->persist($infoServProdCaractCapacidad2);
                                    $this->emComercial->flush();
                                }
                                else if ($strTipoSolicitud != 'SOLICITUD MIGRACION' && $strMarcaOlt != 'HUAWEI')
                                {
                                    $infoServProdCaractCapacidad2 = new InfoServicioProdCaract();
                                    $infoServProdCaractCapacidad2->setServicioId($entityServicio->getId());
                                    $infoServProdCaractCapacidad2->setProductoCaracterisiticaId($prodCaractCapacidad2->getId());
                                    $infoServProdCaractCapacidad2->setValor(trim($infoPlanProdCaract2->getValor()));
                                    $infoServProdCaractCapacidad2->setFeCreacion(new \DateTime('now'));
                                    $infoServProdCaractCapacidad2->setUsrCreacion($usrCreacion);
                                    $infoServProdCaractCapacidad2->setEstado("Activo");
                                    $this->emComercial->persist($infoServProdCaractCapacidad2);
                                    $this->emComercial->flush();
                                }
                                $sigue = true;
                            }
                            else
                            {
                                $sigue = false;
                                $mensaje .= "No existe CAPACIDAD2 para el Plan del Servicio.";
                                throw new \Exception($mensaje);
                            }
                        }//if($prodCaractCapacidad2)
                        else
                        {
                            $sigue = false;
                            $mensaje .= "No existe CAPACIDAD2 para el Internet Dedicado.";
                            throw new \Exception($mensaje);
                        }
                    }//if($sigue)

                    if($tipoMedio->getNombreTipoMedio() == "Cobre" && $sigue)
                    {
                        //GUARDA PERFIL
                        $entityElemento     = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                      ->find($entityServicioTecnico->getElementoId());
                        $marcaElemento      = $entityElemento->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
                        $detallesElemento   = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                      ->findBy(array("elementoId"    => $entityElemento->getId(), 
                                                                                     "detalleNombre" => "PERFIL"));
                        $encontroPerfil = false;
                        if((strtolower(trim($marcaElemento)) != strtolower("CORECESS")) && 
                           (strtolower(trim($marcaElemento)) != strtolower("NET TO NET")))
                        {
                            if($detallesElemento)
                            {
                                //obtengo las caracteristicas
                                $entityCapacidad1 = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                       ->findOneBy(array("descripcionCaracteristica" => "CAPACIDAD1", "estado" => "Activo"));
                                $entityCapacidad2 = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                       ->findOneBy(array("descripcionCaracteristica" => "CAPACIDAD2", "estado" => "Activo"));
                                
                                //busco el detalle del elemento
                                foreach($detallesElemento as $detalleElemento)
                                {
                                    $entityDetalleCaracteristica1 = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleCaracteristica')
                                                                    ->findOneBy(array("detalleElementoId"           => $detalleElemento->getId(), 
                                                                                      "caracteristicaId"            => $entityCapacidad1->getId(), 
                                                                                      "descripcionCaracteristica"   => trim($infoPlanProdCaract1
                                                                                                                                ->getValor())));
                                    $entityDetalleCaracteristica2 = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleCaracteristica')
                                                                    ->findOneBy(array("detalleElementoId"           => $detalleElemento->getId(), 
                                                                                      "caracteristicaId"            => $entityCapacidad2->getId(), 
                                                                                      "descripcionCaracteristica"   => trim($infoPlanProdCaract2
                                                                                                                                ->getValor())));
                                    if($entityDetalleCaracteristica1 && $entityDetalleCaracteristica2)
                                    {
                                        if($entityDetalleCaracteristica1->getValorCaracteristica() == 
                                           $entityDetalleCaracteristica2->getValorCaracteristica())
                                        {
                                            $encontroPerfil = true;
                                            $bw_up = $entityDetalleCaracteristica1->getValorCaracteristica();
                                            $bw_down = $entityDetalleCaracteristica2->getValorCaracteristica();

                                            $entityDetalleInterface = new InfoDetalleInterface();
                                            $entityDetalleInterface->setInterfaceElementoId($entityInterface);
                                            $entityDetalleInterface->setDetalleNombre('PERFIL');
                                            // $entityDetalleInterface->setDetalleValor($detalleElemento->getDetalleValor());
                                            $entityDetalleInterface->setDetalleValor($bw_up);
                                            $entityDetalleInterface->setIpCreacion($ipCreacion);
                                            $entityDetalleInterface->setFeCreacion(new \DateTime('now'));
                                            $entityDetalleInterface->setUsrCreacion($usrCreacion);
                                            $this->emInfraestructura->persist($entityDetalleInterface);
                                            $this->emInfraestructura->flush();

                                            // guardo Peril en Prod caract					
                                            $entityPerfil = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                               ->findOneBy(array("descripcionCaracteristica" => "PERFIL", "estado" => "Activo"));
                                            $prodCaractPerfil = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                   ->findOneBy(array("productoId" => $productoInternetDedicado->getId(), 
                                                                                     "caracteristicaId" => $entityPerfil->getId(), 
                                                                                     "estado" => "Activo"));

                                            if($prodCaractPerfil)
                                            {
                                                $infoServProdCaractVci = new InfoServicioProdCaract();
                                                $infoServProdCaractVci->setServicioId($entityServicio->getId());
                                                $infoServProdCaractVci->setProductoCaracterisiticaId($prodCaractPerfil->getId());
                                                $infoServProdCaractVci->setValor($bw_up);
                                                $infoServProdCaractVci->setFeCreacion(new \DateTime('now'));
                                                $infoServProdCaractVci->setUsrCreacion($usrCreacion);
                                                $infoServProdCaractVci->setEstado("Activo");

                                                $this->emComercial->persist($infoServProdCaractVci);
                                                $this->emComercial->flush();

                                                $sigue = true;
                                            }
                                            else
                                            {
                                                $sigue = false;
                                                $mensaje .= "No existe el Producto Caracteristica para el Perfil del Elemento.";
                                                throw new \Exception($mensaje);
                                            }

                                            break;
                                        }
                                    }//if($entityDetalleCaracteristica1 && $entityDetalleCaracteristica2)
										    
                                }//foreach($detallesElemento as $detalleElemento)
                                if(!$encontroPerfil)
                                {
                                    $sigue = false;
                                    $mensaje .= "No existe el Detalle Caracteristica para el Perfil del Elemento.";
                                    throw new \Exception($mensaje);
                                }
                            }//if($detallesElemento)
                            else
                            {
                                $sigue = false;
                                $mensaje .= "No existen Perfiles para el Elemento.";
                                throw new \Exception($mensaje);
                            }
                        }//endif marcas

                        if($sigue)
                        {
                            // guardo la VCI					
                            $entityVci = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => "VCI", "estado" => "Activo"));
                            $prodCaractVci = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                ->findOneBy(array("productoId" => $productoInternetDedicado->getId(), 
                                                                  "caracteristicaId" => $entityVci->getId(), 
                                                                  "estado" => "Activo"));

                            if($prodCaractVci)
                            {
                                $infoServProdCaractVci = new InfoServicioProdCaract();
                                $infoServProdCaractVci->setServicioId($entityServicio->getId());
                                $infoServProdCaractVci->setProductoCaracterisiticaId($prodCaractVci->getId());
                                $infoServProdCaractVci->setValor(trim($vci));
                                $infoServProdCaractVci->setFeCreacion(new \DateTime('now'));
                                $infoServProdCaractVci->setUsrCreacion($usrCreacion);
                                $infoServProdCaractVci->setEstado("Activo");
                                $this->emComercial->persist($infoServProdCaractVci);
                                $this->emComercial->flush();

                                $sigue = true;
                            }
                            else
                            {
                                $sigue = false;
                                $mensaje .= "No existe VCI para el Internet Dedicado.";
                                throw new \Exception($mensaje);
                            }
                        }
                    }//if($tipoMedio->getNombreTipoMedio() == "Cobre" && $sigue)

                    if($tipoMedio->getNombreTipoMedio() == "Fibra Optica" && $sigue)
                    {
                        //recuperar perfil
                        $entityPerfil = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->findOneBy(array("descripcionCaracteristica" => "PERFIL", "estado" => "Activo"));
                        $prodCaractPerfil = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                               ->findOneBy(array("productoId" => $productoInternetDedicado->getId(), 
                                                                 "caracteristicaId" => $entityPerfil->getId(), 
                                                                 "estado" => "Activo"));
                        if($prodCaractPerfil)
                        {
                            $infoPlanDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                              ->findOneBy(array("productoId" => $productoInternetDedicado->getId(), 
                                                                "planId"     => $entityServicio->getPlanId(),
                                                                "estado"     => $entityServicio->getPlanId()->getEstado()
                                                               )
                                                         );
                            $infoPlanProdCaractPerfil = $this->emComercial->getRepository('schemaBundle:InfoPlanProductoCaract')
                                                           ->findBy(array("planDetId"                 => $infoPlanDet->getId(), 
                                                                          "productoCaracterisiticaId" => $prodCaractPerfil->getId()
                                                                         )
                                                                   );
                            if($infoPlanProdCaractPerfil)
                            {
                                //se agrega codigo para obtener registro mas reciente de PlanProductoCaracteristica
                                foreach($infoPlanProdCaractPerfil as $PlanProdCaractPerfil)
                                {
                                     if($banderaMaxId==0)
                                     {
                                         $banderaMaxId = $PlanProdCaractPerfil;
                                     }
                                     if($banderaMaxId->getId()<$PlanProdCaractPerfil->getId())
                                     {
                                         $banderaMaxId = $PlanProdCaractPerfil;
                                     }
                                }
                               
                                //Obtiene registro de parametro de perfil equivalente
                                $arrayParametrosFuncion                          = "";
                                $arrayParametrosFuncion['elementoOltId']         = $entityServicioTecnico->getElementoId();
                                $arrayParametrosFuncion['idPlan']                = $entityServicio->getPlanId()->getId();
                                $arrayParametrosFuncion['valorPerfil']           = trim($banderaMaxId->getValor());
                                $arrayParametrosFuncion['tipoAprovisionamiento'] = $strAprovisionamiento;
                                if ($strTipoSolicitud != 'SOLICITUD MIGRACION' )
                                {
                                   $arrayParametrosFuncion['marca'] = $strMarcaOlt; 
                                }
                                else
                                {
                                    $arrayParametrosFuncion['marca']   = 'HUAWEI';
                                }
                                $arrayParametrosFuncion['empresaCod']  = $idEmpresa;

                                $arrayParametrosFuncion['tipoNegocio']   = $nombreTipoNegocioPlan;
                                $arrayParametrosFuncion['tipoEjecucion'] = 'FLUJO';


                                $strPerfilEquivalente     = $this->getPerfilPlanEquivalente($arrayParametrosFuncion);

                                if($strTipoSolicitud != 'SOLICITUD MIGRACION' && 
                                   ($strMarcaOlt != 'HUAWEI' && $strMarcaOlt != 'ZTE'))
                                {
                                    $infoServProdCaractPerfil = new InfoServicioProdCaract();
                                    $infoServProdCaractPerfil->setServicioId($entityServicio->getId());
                                    $infoServProdCaractPerfil->setProductoCaracterisiticaId($prodCaractPerfil->getId());
                                    $infoServProdCaractPerfil->setValor($strPerfilEquivalente);
                                    $infoServProdCaractPerfil->setFeCreacion(new \DateTime('now'));
                                    $infoServProdCaractPerfil->setUsrCreacion($usrCreacion);
                                    $infoServProdCaractPerfil->setEstado("Activo");
                                    $this->emComercial->persist($infoServProdCaractPerfil);
                                    $this->emComercial->flush();
                                }
                                $sigue = true;
                            }
                            else
                            {
                                $sigue = false;
                                $mensaje .= "No existe PERFIL para el Plan " . $entityServicio->getPlanId()->getNombrePlan();
                                throw new \Exception($mensaje);
                            }
                        }
                        else
                        {
                            $sigue    = false;
                            $mensaje .= "No existe Perfil para el Internet Dedicado del Plan " . $entityServicio->getPlanId()->getNombrePlan();
                            throw new \Exception($mensaje);
                        }
                    }//if($tipoMedio->getNombreTipoMedio() == "Fibra Optica" && $sigue)
                    $banderaMaxId = 0;
                    if($sigue)
                    {
                        // guardo la CAPACIDAD-INT1
                        $entityCapacidad1 = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneBy(array("descripcionCaracteristica" => "CAPACIDAD-INT1", "estado" => "Activo"));
                        $prodCaractCapacidad1 = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                   ->findOneBy(array("productoId" => $productoInternetDedicado->getId(), 
                                                                     "caracteristicaId" => $entityCapacidad1->getId(), 
                                                                     "estado" => "Activo"));

                        if($prodCaractCapacidad1)
                        {
                            $infoPlanDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                              ->findOneBy(array("productoId" => $productoInternetDedicado->getId(), 
                                                                "planId" => $entityServicio->getPlanId()));

                            $infoPlanProdCaract1 = $this->emComercial->getRepository('schemaBundle:InfoPlanProductoCaract')
                                                      ->findBy(array("planDetId" => $infoPlanDet->getId(), 
                                                                        "productoCaracterisiticaId" => $prodCaractCapacidad1->getId(), 
                                                                        "estado" => $entityServicio->getPlanId()->getEstado()));

                            if($infoPlanProdCaract1)
                            {
                                //se agrega codigo para obtener registro mas reciente de PlanProductoCaracteristica
                                 foreach($infoPlanProdCaract1 as $PlanProdCaract1)
                                {
                                     if($banderaMaxId==0)
                                     {
                                         $banderaMaxId = $PlanProdCaract1;
                                     }
                                     
                                     if($banderaMaxId->getId()<$PlanProdCaract1->getId())
                                     {
                                         $banderaMaxId = $PlanProdCaract1;
                                     }
                                }
                                if ($strTipoSolicitud != 'SOLICITUD MIGRACION' && 
                                    ($strMarcaOlt != 'HUAWEI' && $strMarcaOlt != 'ZTE' && $strMarcaOlt != 'TELLION'))
                                {
                                    $infoServProdCaractCapacidad1 = new InfoServicioProdCaract();
                                    $infoServProdCaractCapacidad1->setServicioId($entityServicio->getId());
                                    $infoServProdCaractCapacidad1->setProductoCaracterisiticaId($prodCaractCapacidad1->getId());
                                    $infoServProdCaractCapacidad1->setValor(trim($banderaMaxId->getValor()));
                                    $infoServProdCaractCapacidad1->setFeCreacion(new \DateTime('now'));
                                    $infoServProdCaractCapacidad1->setUsrCreacion($usrCreacion);
                                    $infoServProdCaractCapacidad1->setEstado("Activo");
                                    $this->emComercial->persist($infoServProdCaractCapacidad1);
                                    $this->emComercial->flush();
                                }
								    
                            }
                        }
                    }//if($sigue)
                    $banderaMaxId = 0;
                    if($sigue)
                    {
                        // guardo la CAPACIDAD-INT2
                        $entityCapacidad1 = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneBy(array("descripcionCaracteristica" => "CAPACIDAD-INT2", "estado" => "Activo"));
                        $prodCaractCapacidad1 = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                   ->findOneBy(array("productoId" => $productoInternetDedicado->getId(), 
                                                                     "caracteristicaId" => $entityCapacidad1->getId(), 
                                                                     "estado" => "Activo"));

                        if($prodCaractCapacidad1)
                        {
                            $infoPlanDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                              ->findOneBy(array("productoId" => $productoInternetDedicado->getId(), 
                                                                "planId" => $entityServicio->getPlanId()));

                            $infoPlanProdCaract1 = $this->emComercial->getRepository('schemaBundle:InfoPlanProductoCaract')
                                                      ->findBy(array("planDetId" => $infoPlanDet->getId(), 
                                                                        "productoCaracterisiticaId" => $prodCaractCapacidad1->getId(), 
                                                                        "estado" => $entityServicio->getPlanId()->getEstado()));

                            if($infoPlanProdCaract1)
                            {
                                //se agrega codigo para obtener registro mas reciente de PlanProductoCaracteristica
                                 foreach($infoPlanProdCaract1 as $PlanProdCaract1)
                                {
                                     if($banderaMaxId==0)
                                     {
                                         $banderaMaxId = $PlanProdCaract1;
                                     }
                                     if($banderaMaxId->getId()<$PlanProdCaract1->getId())
                                     {
                                         $banderaMaxId = $PlanProdCaract1;
                                     }
                                }
                                if ($strTipoSolicitud != 'SOLICITUD MIGRACION' && 
                                    ($strMarcaOlt != 'HUAWEI' && $strMarcaOlt != 'ZTE' && $strMarcaOlt != 'TELLION'))
                                {
                                    $infoServProdCaractCapacidad1 = new InfoServicioProdCaract();
                                    $infoServProdCaractCapacidad1->setServicioId($entityServicio->getId());
                                    $infoServProdCaractCapacidad1->setProductoCaracterisiticaId($prodCaractCapacidad1->getId());
                                    $infoServProdCaractCapacidad1->setValor(trim($banderaMaxId->getValor()));
                                    $infoServProdCaractCapacidad1->setFeCreacion(new \DateTime('now'));
                                    $infoServProdCaractCapacidad1->setUsrCreacion($usrCreacion);
                                    $infoServProdCaractCapacidad1->setEstado("Activo");
                                    $this->emComercial->persist($infoServProdCaractCapacidad1);
                                    $this->emComercial->flush();
                                }
								    
                            }
                        }
                    }//if($sigue)
                    $banderaMaxId = 0;
                    if($sigue)
                    {
                        // guardo la CAPACIDAD-PROM1
                        $entityCapacidad1 = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneBy(array("descripcionCaracteristica" => 
                                                                 "CAPACIDAD-PROM1",
                                                                 "estado" => "Activo"));
                        $prodCaractCapacidad1 = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                   ->findOneBy(array("productoId" => $productoInternetDedicado->getId(), 
                                                                     "caracteristicaId" => $entityCapacidad1->getId(), 
                                                                     "estado" => "Activo"));

                        if($prodCaractCapacidad1)
                        {
                            $infoPlanDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                              ->findOneBy(array("productoId" => $productoInternetDedicado->getId(), 
                                                                "planId" => $entityServicio->getPlanId()));

                            $infoPlanProdCaract1 = $this->emComercial->getRepository('schemaBundle:InfoPlanProductoCaract')
                                                      ->findBy(array("planDetId" => $infoPlanDet->getId(), 
                                                                        "productoCaracterisiticaId" => $prodCaractCapacidad1->getId(), 
                                                                        "estado" => $entityServicio->getPlanId()->getEstado()));

                            if($infoPlanProdCaract1)
                            {
                                //se agrega codigo para obtener registro mas reciente de PlanProductoCaracteristica
                                 foreach($infoPlanProdCaract1 as $PlanProdCaract1)
                                {
                                     if($banderaMaxId==0)
                                     {
                                         $banderaMaxId = $PlanProdCaract1;
                                     }
                                     if($banderaMaxId->getId()<$PlanProdCaract1->getId())
                                     {
                                         $banderaMaxId = $PlanProdCaract1;
                                     }
                                }
                                if ($strTipoSolicitud != 'SOLICITUD MIGRACION' && 
                                    ($strMarcaOlt != 'HUAWEI' && $strMarcaOlt != 'ZTE' && $strMarcaOlt != 'TELLION'))
                                {
                                    $infoServProdCaractCapacidad1 = new InfoServicioProdCaract();
                                    $infoServProdCaractCapacidad1->setServicioId($entityServicio->getId());
                                    $infoServProdCaractCapacidad1->setProductoCaracterisiticaId($prodCaractCapacidad1->getId());
                                    $infoServProdCaractCapacidad1->setValor(trim($banderaMaxId->getValor()));
                                    $infoServProdCaractCapacidad1->setFeCreacion(new \DateTime('now'));
                                    $infoServProdCaractCapacidad1->setUsrCreacion($usrCreacion);
                                    $infoServProdCaractCapacidad1->setEstado("Activo");
                                    $this->emComercial->persist($infoServProdCaractCapacidad1);
                                    $this->emComercial->flush();
                                }
								    
                            }
                        }
                    }//if($sigue)
                    $banderaMaxId = 0;
                    if($sigue)
                    {
                        // guardo la CAPACIDAD-PROM2
                        $entityCapacidad1 = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneBy(array("descripcionCaracteristica" => "CAPACIDAD-PROM2", 
                                                                 "estado" => "Activo"));
                        $prodCaractCapacidad1 = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                   ->findOneBy(array("productoId" => $productoInternetDedicado->getId(), 
                                                                     "caracteristicaId" => $entityCapacidad1->getId(), 
                                                                     "estado" => "Activo"));

                        if($prodCaractCapacidad1)
                        {
                            $infoPlanDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                              ->findOneBy(array("productoId" => $productoInternetDedicado->getId(), 
                                                                "planId" => $entityServicio->getPlanId()));

                            $infoPlanProdCaract1 = $this->emComercial->getRepository('schemaBundle:InfoPlanProductoCaract')
                                                      ->findBy(array("planDetId" => $infoPlanDet->getId(), 
                                                                        "productoCaracterisiticaId" => $prodCaractCapacidad1->getId(), 
                                                                        "estado" => $entityServicio->getPlanId()->getEstado()));

                            if($infoPlanProdCaract1)
                            {
                                //se agrega codigo para obtener registro mas reciente de PlanProductoCaracteristica
                                foreach($infoPlanProdCaract1 as $PlanProdCaract1)
                                {
                                     if($banderaMaxId==0)
                                     {
                                         $banderaMaxId = $PlanProdCaract1;
                                     }
                                     if($banderaMaxId->getId()<$PlanProdCaract1->getId())
                                     {
                                         $banderaMaxId = $PlanProdCaract1;
                                     }
                                }
                                if ($strTipoSolicitud != 'SOLICITUD MIGRACION' && 
                                    ($strMarcaOlt != 'HUAWEI' && $strMarcaOlt != 'ZTE' && $strMarcaOlt != 'TELLION'))
                                {
                                    $infoServProdCaractCapacidad1 = new InfoServicioProdCaract();
                                    $infoServProdCaractCapacidad1->setServicioId($entityServicio->getId());
                                    $infoServProdCaractCapacidad1->setProductoCaracterisiticaId($prodCaractCapacidad1->getId());
                                    $infoServProdCaractCapacidad1->setValor(trim($banderaMaxId->getValor()));
                                    $infoServProdCaractCapacidad1->setFeCreacion(new \DateTime('now'));
                                    $infoServProdCaractCapacidad1->setUsrCreacion($usrCreacion);
                                    $infoServProdCaractCapacidad1->setEstado("Activo");
                                    $this->emComercial->persist($infoServProdCaractCapacidad1);
                                    $this->emComercial->flush();
                                }
                            }//if($infoPlanProdCaract1)
                        }//if($prodCaractCapacidad1)
                    }//if($sigue)					    
                }//if($nombreTecnico != "IP")
                
                //se agrega linea de codigo para recuperar ultima milla
                $tipoMedio = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')->find($entityServicioTecnico->getUltimaMillaId());
                if($sigue)
                {
                    //Guardo Ips
                    $idProducto = $productoInternetDedicado->getId();
                    //se agrega validacion de ultima milla por motivo de migracion TTCO
                    if(($prefijoEmpresa == "MD" || $prefijoEmpresa == "EN")&& $tipoMedio->getNombreTipoMedio() == "Fibra Optica")
                    {
                        $estadoIp = "Reservada";
                    }
                    else
                    {
                        $estadoIp = "Activo";
                    }

                    $arrayCaracteristicas = "";
                    $json_caracteristicas = json_decode($jsonCaracteristicas);
                    if($json_caracteristicas)
                    {
                        $arrayCaracteristicas = $json_caracteristicas->caracteristicas;
                    }

                    //se agrega validacion de ultima milla por motivo de migracion TTCO
                    if(($prefijoEmpresa == "MD" || $prefijoEmpresa == "EN") &&
                        $nombreTecnico == "IP" && $tipoMedio->getNombreTipoMedio() == "Fibra Optica")
                    {
                        $id_punto = $entityServicio->getPuntoId()->getId();
                        //recuperar perfil
                        $entityPerfil = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->findOneBy(array("descripcionCaracteristica" => "PERFIL", 
                                                             "estado" => "Activo"));
                        $prodCaractPerfil = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                               ->findOneBy(array("productoId" => $productoInternetDedicado->getId(), 
                                                                 "caracteristicaId" => $entityPerfil->getId(), 
                                                                 "estado" => "Activo"));

                        $arrayRespuestaPerfil = $this->emComercial->getRepository("schemaBundle:InfoPlanCab")
                                                     ->getPerfilByPlanIdAndPuntoId("no","",$id_punto,"SI");
                        
                        if (is_string($arrayRespuestaPerfil))
                        {
                            if(strpos($arrayRespuestaPerfil, 'Error') !== false)
                            {
                                $arrayResponse = array('status'=>"ERROR", 'mensaje'=>$arrayRespuestaPerfil);
                                return $arrayResponse;
                                
                            }
                        }

                        if($arrayRespuestaPerfil)
                        {
                            //Obtiene registro de parametro de perfil equivalente
                            $arrayParametrosFuncion                          = "";
                            $arrayParametrosFuncion['elementoOltId']         = $entityServicioTecnico->getElementoId();
                            $arrayParametrosFuncion['idPlan']                = $arrayRespuestaPerfil['strIdPlan'];
                            $arrayParametrosFuncion['valorPerfil']           = $arrayRespuestaPerfil['strPerfil'];
                            $arrayParametrosFuncion['tipoAprovisionamiento'] = $strAprovisionamiento;
                            if (!$strMarcaOlt)
                            {
                                $entityElementoInternet = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                               ->find($entityServicioTecnico->getElementoId());
                                $strMarcaOlt = $entityElementoInternet->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
                            }

                            $arrayParametrosFuncion['marca'] = $strMarcaOlt;

                            if ($strEsIp == "SI")
                            {
                                if ($nombreTipoNegocioPlan=='PRO')
                                {
                                    $arrayParametrosFuncion['tipoNegocio'] = 'PROIP';
                                }
                                else
                                {
                                    $arrayParametrosFuncion['tipoNegocio'] =$nombreTipoNegocioPlan;
                                }
                            }
                            else
                            {
                                $arrayParametrosFuncion['tipoNegocio'] =$nombreTipoNegocioPlan;
                            }

                            $arrayParametrosFuncion['tipoEjecucion'] = 'FLUJO';
                            $arrayParametrosFuncion['empresaCod']    = $idEmpresa;
                            $strPerfilEquivalente                 = $this->getPerfilPlanEquivalente($arrayParametrosFuncion);

                            if ($strTipoSolicitud != 'SOLICITUD MIGRACION' && 
                                ($strMarcaOlt != 'HUAWEI' && $strMarcaOlt != 'ZTE'))
                            {
                                /*se valida perfiles de servicios de Ips adicionales Pyme de clientes Tellion que
                                  aprovisionan con Pool qeu tienen un plan UAV activo*/
                                if (is_object($objServicioTecnicoPlan))
                                {
                                    $strEsUav = $this->getCaracteristicaPorPlan($objServicioTecnicoPlan->getServicioId()->getPlanId()->getId());
                                    if ($strEsUav == "SI" && $nombreTipoNegocioPlan == 'PYME' && $strAprovisionamiento == "POOL")
                                    {
                                        //$strPerfilEquivalente
                                        $objProdCaractIndiceCliente = $this->servicioGeneral
                                                                        ->getServicioProductoCaracteristica($objServicioTecnicoPlan->getServicioId(),
                                                                                                                "PERFIL", 
                                                                                                                $productoInternetDedicado);
                                        if ($objProdCaractIndiceCliente)
                                        {
                                            if (substr($strPerfilEquivalente,0,strlen($strPerfilEquivalente)-2) !=
                                                substr($objProdCaractIndiceCliente->getValor(),0,strlen($objProdCaractIndiceCliente->getValor())-2))
                                            {
                                                $strPerfilEquivalente = $objProdCaractIndiceCliente->getValor();
                                            }
                                        }
                                    }
                                }
                                
                                $infoServProdCaractPerfil = new InfoServicioProdCaract();
                                $infoServProdCaractPerfil->setServicioId($entityServicio->getId());
                                $infoServProdCaractPerfil->setProductoCaracterisiticaId($prodCaractPerfil->getId());
                                $infoServProdCaractPerfil->setValor(trim($strPerfilEquivalente));
                                $infoServProdCaractPerfil->setFeCreacion(new \DateTime('now'));
                                $infoServProdCaractPerfil->setUsrCreacion($usrCreacion);
                                $infoServProdCaractPerfil->setEstado("Activo");
                                $this->emComercial->persist($infoServProdCaractPerfil);
                                $this->emComercial->flush();
                            }
                            $sigue = true;
                        }
                        else
                        {
                            $sigue      = false;                            
                            $mensaje    = "No existe PERFIL";
                            throw new \Exception($mensaje);
                        }

                        if($sigue)
                        {
                            $objServicioTecnicoPlan = $this->getServicioTecnicoByPuntoId(array("intIdPunto" => $id_punto));
                            if(is_object($objServicioTecnicoPlan))
                            {
                                $entityServicioTecnico->setElementoId($objServicioTecnicoPlan->getElementoId());
                                $entityServicioTecnico->setInterfaceElementoId($objServicioTecnicoPlan->getInterfaceElementoId());
                                $entityServicioTecnico->setElementoContenedorId($objServicioTecnicoPlan->getElementoContenedorId());
                                $entityServicioTecnico->setElementoConectorId($objServicioTecnicoPlan->getElementoConectorId());
                                $entityServicioTecnico->setInterfaceElementoConectorId($objServicioTecnicoPlan->getInterfaceElementoConectorId());
                                $this->emComercial->persist($entityServicioTecnico);
                                $this->emComercial->flush();
                            }
                            else
                            {
                                $sigue = false;
                                $mensaje .= $objServicioTecnicoPlan;
                                throw new \Exception($mensaje);
                            }
                        }
					   
                    }

                    if($sigue)
                    {
                        if($arrayCaracteristicas)
                        {
                            for($i = 0; $i < count($arrayCaracteristicas); $i++)
                            {
                                $tipo = $arrayCaracteristicas[$i]->tipo;
                                if($tipo == 'LAN')
                                {
                                    $caracIpLan = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                     ->findOneBy(array("descripcionCaracteristica" => "IP LAN", "estado" => "Activo"));
                                    $caracMascaraLan = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->findOneBy(array("descripcionCaracteristica" => "MASCARA LAN", "estado" => "Activo"));
                                    $caracGatewayLan = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->findOneBy(array("descripcionCaracteristica" => "GATEWAY LAN", "estado" => "Activo"));
                                    $productoCaracteristicaIpLan = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                      ->findOneBy(array("productoId" => $idProducto, 
                                                                                        "caracteristicaId" => $caracIpLan->getId()));
                                    $productoCaracteristicaMascaraLan = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                           ->findOneBy(array("productoId" => $idProducto, 
                                                                                             "caracteristicaId" => $caracMascaraLan->getId()));
                                    $productoCaracteristicaGatewayLan = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                           ->findOneBy(array("productoId" => $idProducto, 
                                                                                             "caracteristicaId" => $caracGatewayLan->getId()));

                                    if($productoCaracteristicaIpLan)
                                    {
                                        $ipLanInterface = $arrayCaracteristicas[$i]->ip;
                                        $mascaraLanInterface = $arrayCaracteristicas[$i]->mascara;
                                        $gatewayLanInterface = $arrayCaracteristicas[$i]->gateway;

                                        $spcIpLan1 = new InfoServicioProdCaract();
                                        $spcIpLan1->setServicioId($idServicio);
                                        $spcIpLan1->setProductoCaracterisiticaId($productoCaracteristicaIpLan->getId());
                                        $spcIpLan1->setValor($ipLanInterface);
                                        $spcIpLan1->setFeCreacion(new \DateTime('now'));
                                        $spcIpLan1->setUsrCreacion($usrCreacion);
                                        $spcIpLan1->setEstado("Activo");
                                        $this->emComercial->persist($spcIpLan1);
                                        $this->emComercial->flush();

                                        $spcmascaraLan1 = new InfoServicioProdCaract();
                                        $spcmascaraLan1->setServicioId($idServicio);
                                        $spcmascaraLan1->setProductoCaracterisiticaId($productoCaracteristicaMascaraLan->getId());
                                        $spcmascaraLan1->setValor($mascaraLanInterface);
                                        $spcmascaraLan1->setFeCreacion(new \DateTime('now'));
                                        $spcmascaraLan1->setUsrCreacion($usrCreacion);
                                        $spcmascaraLan1->setEstado("Activo");
                                        $this->emComercial->persist($spcmascaraLan1);
                                        $this->emComercial->flush();

                                        $spcgatewayLan1 = new InfoServicioProdCaract();
                                        $spcgatewayLan1->setServicioId($idServicio);
                                        $spcgatewayLan1->setProductoCaracterisiticaId($productoCaracteristicaGatewayLan->getId());
                                        $spcgatewayLan1->setValor($gatewayLanInterface);
                                        $spcgatewayLan1->setFeCreacion(new \DateTime('now'));
                                        $spcgatewayLan1->setUsrCreacion($usrCreacion);
                                        $spcgatewayLan1->setEstado("Activo");
                                        $this->emComercial->persist($spcgatewayLan1);
                                        $this->emComercial->flush();
                                    }
                                    else
                                    {
                                        $sigue = false;
                                        $mensaje .= "No existe Producto Caracteristica Ip lan.";
                                        throw new \Exception($mensaje);
                                    }
                                }
                                else
                                {   
                                    if($strTipoSolicitud != 'SOLICITUD MIGRACION' || $intCantidadIpsBase == 0)
                                    {
                                        $infoIp = new InfoIp();
                                        $infoIp->setIp($arrayCaracteristicas[$i]->ip);
                                        $infoIp->setMascara($arrayCaracteristicas[$i]->mascara);
                                        $infoIp->setGateway($arrayCaracteristicas[$i]->gateway);
                                        $infoIp->setTipoIp($tipo);
                                        $infoIp->setVersionIp('IPV4');
                                        $infoIp->setServicioId($idServicio);
                                        $infoIp->setIpCreacion($ipCreacion);
                                        $infoIp->setFeCreacion(new \DateTime('now'));
                                        $infoIp->setUsrCreacion($usrCreacion);
                                        $infoIp->setEstado($estadoIp);
                                        //se agrega codigo para llevar rastro de la ip asignada por el sistema Telcos
                                        $strListaIps = $strListaIps . $arrayCaracteristicas[$i]->ip . ' ';
                                        $intContador = $intContador + 1;
                                        $this->emInfraestructura->persist($infoIp);
                                        $this->emInfraestructura->flush();
                                        
                                        //se agrega validación de ultima milla
                                        if (($prefijoEmpresa == "MD" || $prefijoEmpresa == "EN") && 
                                             $tipoMedio->getNombreTipoMedio() == "Fibra Optica")
                                        {
                                            if ($objProductoIp && $strAprovisionamiento == "CNR")
                                            {
                                                $this->servicioGeneral->ingresarServicioProductoCaracteristica($entityServicio, 
                                                                                                                $objProductoIp, 
                                                                                                                "SCOPE", 
                                                                                                                $arrayCaracteristicas[$i]->scope, 
                                                                                                                $usrCreacion);
                                            }
                                        }
                                    }
                                    else
                                    {
                                        /*
                                         * Se obtienen caracteristicas SCOPE y en caso de existir mas de una caracteristica SCOPE 
                                         * se proceden a eliminar dejando Activa la de mayor secuencial de creacion (ID_SERVICIO_PROD_CARACT)
                                         */
                                        $objCarac = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                      ->findOneBy(array( "descripcionCaracteristica"  => 'SCOPE',
                                                                                         "estado"                     => "Activo"));
                                        
                                        if(is_object($objCarac) && is_object($objProductoIp))
                                        {
                                            $objProdCarac = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                           ->findOneBy(array( "productoId"          => $objProductoIp->getId(),
                                                                                              "caracteristicaId"    => $objCarac->getId(),
                                                                                              "estado"              => "Activo"));
                                            if($objProdCarac)
                                            {
                                                $arrayServProdCarac = $this->emComercial
                                                                           ->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                           ->findBy(array( "servicioId"                  => $entityServicio->getId(),
                                                                                           "productoCaracterisiticaId"   => $objProdCarac->getId(),
                                                                                           "estado"                      => "Activo"),
                                                                                    array('id' => 'DESC'));
                                                
                                                if (count($arrayServProdCarac)>1)
                                                {
                                                    $objProdCaractScopeUlt = $arrayServProdCarac[0];
                                                    foreach($arrayServProdCarac as $objProductoCaracteristicaScope)
                                                    {
                                                        if ($objProdCaractScopeUlt->getId() != $objProductoCaracteristicaScope->getId())
                                                        {
                                                            $objProductoCaracteristicaScope->setEstado("Eliminado");
                                                            $this->emComercial->persist($objProductoCaracteristicaScope);
                                                            $this->emComercial->flush();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                            }
                        }
                    }
                }//if($sigue)

                if($sigue)
                {
                    if($nombreTecnico != "IP")
                    {
                        $estadoServicio = "Asignada";
                        $estadoSolicitud = "Asignada";
                    }
                    else
                    {
                        $estadoServicio = "EnPruebas";
                        if(($prefijoEmpresa == "MD" || $prefijoEmpresa == "EN") &&
                            $nombreTecnico == "IP" && $tipoMedio->getNombreTipoMedio() == "Fibra Optica")
                        {
                            $estadoServicio = "Asignada";
                        }
                        $estadoSolicitud = "Finalizada";
                    }

                    //SE ACTUALIZA EL ESTADO DE LA SOLICITUD	
                    $entityDetalleSolicitud->setEstado($estadoSolicitud);
                    $this->emComercial->persist($entityDetalleSolicitud);
                    $this->emComercial->flush();

                    //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                    $lastDetalleSolhist = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                                            ->findOneDetalleSolicitudHistorial($id, 'Planificada');

                    $entityDetalleSolHist = new InfoDetalleSolHist();
                    $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                    
                    //se agrega codigo para llevar rastro de la ip asignada por el sistema Telcos
                    if($intContador > 0)
                    {
                        if($intContador > 1)
                        {
                            $strListaIps = ($strListaIps ? 'Se asignaron las ips: ' . $strListaIps : "");
                        }
                        else
                        {
                            $strListaIps = ($strListaIps ? 'Se asigno la ip: ' . $strListaIps : "");
                        }
                    }
                    else
                    {
                        $strListaIps = '';
                    }
                    
                    if($lastDetalleSolhist)
                    {
                        $entityDetalleSolHist->setFeIniPlan($lastDetalleSolhist->getFeIniPlan());
                        $entityDetalleSolHist->setFeFinPlan($lastDetalleSolhist->getFeFinPlan());
                        //se agrega codigo para llevar rastro de la ip asignada por el sistema Telcos
                        $entityDetalleSolHist->setObservacion($lastDetalleSolhist->getObservacion() . ' ' . $strListaIps);
                    }
                    $entityDetalleSolHist->setIpCreacion($ipCreacion);
                    $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $entityDetalleSolHist->setUsrCreacion($usrCreacion);
                    $entityDetalleSolHist->setEstado($estadoSolicitud);

                    $this->emComercial->persist($entityDetalleSolHist);
                    $this->emComercial->flush();
                    
                    if ($strTipoSolicitud != 'SOLICITUD MIGRACION')
                    {
                        if ($strMarcaOlt == 'HUAWEI' && $nombreTecnico != "IP")
                        {
                            //SE AGREGAN CARACTERISTICAS PARA ACTIVACION HUAWEI
                            $nombreTipoNegocioPlan = $entityServicio->getPuntoId()->getTipoNegocioId()->getNombreTipoNegocio();
                            if ( $nombreTipoNegocioPlan!='HOME' && $nombreTipoNegocioPlan!='PYME' && $nombreTipoNegocioPlan!='PRO' )
                            {
                                $nombreTipoNegocioPlan = $entityServicio->getPlanId()->getTipo();
                            }
                            if ( $nombreTipoNegocioPlan!='HOME' && $nombreTipoNegocioPlan!='PYME' && $nombreTipoNegocioPlan!='PRO' ) 
                            {
                                $mensaje = "No se puede realizar la Asignacion, el tipo de negocio no es valido!";
                                throw new \Exception($mensaje);
                            }
                            $strPlanCaracUltraV           = $this->getCaracteristicaPorPlan($entityServicio->getPlanId()->getId());
                            $arrayProductoCaracteristicas = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                               ->getProductoCaracteristica($idEmpresa,
                                                                                           $entityServicioTecnico->getElementoId(),
                                                                                           $strPerfilEquivalente,
                                                                                           $nombreTipoNegocioPlan,
                                                                                           $strPlanCaracUltraV
                                                                                          );
                            if (count($arrayProductoCaracteristicas)==4)
                            {
                                foreach($arrayProductoCaracteristicas as $productoCaracteristica)
                                {
                                    $infoServProdCaractPerfil = new InfoServicioProdCaract();
                                    $infoServProdCaractPerfil->setServicioId($entityServicio->getId());
                                    $infoServProdCaractPerfil->setProductoCaracterisiticaId($productoCaracteristica['ID_PRODUCTO_CARACTERISITICA']);
                                    $infoServProdCaractPerfil->setValor($productoCaracteristica['DETALLE_VALOR']);
                                    $infoServProdCaractPerfil->setFeCreacion(new \DateTime('now'));
                                    $infoServProdCaractPerfil->setUsrCreacion($usrCreacion);
                                    $infoServProdCaractPerfil->setEstado("Activo");
                                    $this->emComercial->persist($infoServProdCaractPerfil);
                                    $this->emComercial->flush();
                                }
                            }
                            else
                            {
                                $mensaje = "No se puede realizar la Asignacion, faltan variables de configuracion para el OLT";
                                throw new \Exception($mensaje);
                            }
                            
                            //CARACTERISTICA TRASLADO
                            $traslado = $this->servicioGeneral->getServicioProductoCaracteristica($entityServicio, 
                                                                                                   "TRASLADO", 
                                                                                                   $productoInternetDedicado);

                            if($traslado)
                            {
                                $strServicioAnteriorId  = $traslado->getValor();
                                $entityServicioTraslado = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                            ->find($strServicioAnteriorId);
                                $vlanAnterior           = $this->servicioGeneral->getServicioProductoCaracteristica($entityServicioTraslado, 
                                                                                                                    "VLAN", 
                                                                                                                    $productoInternetDedicado);
                                if ($vlanAnterior)
                                {
                                    /* Cadena '302' corresponde a la VLAN utilizada por planes PRO y PYMES que realizan uso de IPS
                                     * Se realiza esta comparación para actualizar la VLAN del servicio Actual en caso de que el servicio  
                                     * origen del Traslado haya tenido la VLAN 302 como caracteristica, caso contrario la VLAN del servicio Actual
                                     * se mantendra con la correspondiente al Plan segun lo parametrizado
                                     */                                    
                                    if ($vlanAnterior->getValor() == '302')
                                    {
                                        $vlanNueva = $this->servicioGeneral->getServicioProductoCaracteristica($entityServicio, 
                                                                                                                "VLAN", 
                                                                                                                $productoInternetDedicado);
                                        if ($vlanNueva)
                                        {
                                            $vlanNueva->setValor($vlanAnterior->getValor());
                                            $this->emComercial->persist($vlanNueva);
                                            $this->emComercial->flush();                                            
                                        }
                                    }
                                }//if ($vlanAnterior)
                            }//if($traslado)
                        }
                        
                        //SE ACTUALIZA EL ESTADO DEL SERVICIO
                        $entityServicio = $entityDetalleSolicitud->getServicioId();
                        $entityServicio->setEstado($estadoServicio);
                        $this->emComercial->persist($entityServicio);
                        $this->emComercial->flush();

                        //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $entityServicioHist = new InfoServicioHistorial();
                        $entityServicioHist->setServicioId($entityServicio);

                        $entityServicioHist->setIpCreacion($ipCreacion);
                        $entityServicioHist->setFeCreacion(new \DateTime('now'));
                        $entityServicioHist->setUsrCreacion($usrCreacion);
                        //se agrega codigo para llevar rastro de la ip asignada por el sistema Telcos
                        $entityServicioHist->setObservacion($strListaIps);

                        $entityServicioHist->setEstado($estadoServicio);

                        $this->emComercial->persist($entityServicioHist);
                        $this->emComercial->flush();
                    }
                    else //MIGRACION DE TECNOLOGIA TELLION -> HUAWEI
                    {
                        if ($nombreTecnico != "IP")
                        {
                            $entityServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                       ->findOneByServicioId($idServicio);
                            //recuperar perfil
                            $entityPerfil = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneBy(array("descripcionCaracteristica" => "INTERFACE ELEMENTO TELLION", 
                                                                 "estado"                    => "Activo"));
                            $prodCaractPerfil = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                   ->findOneBy(array("productoId"       => $productoInternetDedicado->getId(), 
                                                                     "caracteristicaId" => $entityPerfil->getId(), 
                                                                     "estado"           => "Activo"));

                            $infoServProdCaractPerfil = new InfoServicioProdCaract();
                            $infoServProdCaractPerfil->setServicioId($entityServicio->getId());
                            $infoServProdCaractPerfil->setProductoCaracterisiticaId($prodCaractPerfil->getId());
                            $infoServProdCaractPerfil->setValor($entityServicioTecnico->getInterfaceElementoId());
                            $infoServProdCaractPerfil->setFeCreacion(new \DateTime('now'));
                            $infoServProdCaractPerfil->setUsrCreacion($usrCreacion);
                            $infoServProdCaractPerfil->setEstado("Activo");
                            $this->emComercial->persist($infoServProdCaractPerfil);
                            $this->emComercial->flush();
                            
                            //Crear caracteristicas para servicios de internet del punto
                            $arrayRespuestaServiciosIp  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                            ->getServiciosIpbyPunto(array(  "intIdPunto"        => 
                                                                                                            $entityServicio->getPuntoId()->getId(),
                                                                                                            "intIdServicio"     => 
                                                                                                            $entityServicio->getId())
                                                                                                            );
                            $strStatusServiciosIp       = $arrayRespuestaServiciosIp['status'];
                            if($strStatusServiciosIp !== "OK")
                            {
                                throw new \Exception($arrayRespuestaServiciosIp['mensaje']);
                            }
                                    
                            $arrayServiciosIps = $arrayRespuestaServiciosIp['serviciosIps'];
                            if (count($arrayServiciosIps)>0)
                            {
                                foreach($arrayServiciosIps as $arrayServicioIp)
                                {
                                    
                                    $productoServicio = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                   ->find($arrayServicioIp['PRODUCTO_ID']);
                                    
                                    $entityPerfil = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                       ->findOneBy(array("descripcionCaracteristica" => "INTERFACE ELEMENTO TELLION", 
                                                                         "estado"                    => "Activo"));
                                    $prodCaractPerfil = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                           ->findOneBy(array("productoId"       => $productoServicio->getId(), 
                                                                             "caracteristicaId" => $entityPerfil->getId(), 
                                                                             "estado"           => "Activo"));
                                    
                                    
                                    $infoServProdCaractPerfil = new InfoServicioProdCaract();
                                    $infoServProdCaractPerfil->setServicioId($arrayServicioIp['ID_SERVICIO']);
                                    $infoServProdCaractPerfil->setProductoCaracterisiticaId($prodCaractPerfil->getId());
                                    $infoServProdCaractPerfil->setValor($entityServicioTecnico->getInterfaceElementoId());
                                    $infoServProdCaractPerfil->setFeCreacion(new \DateTime('now'));
                                    $infoServProdCaractPerfil->setUsrCreacion($usrCreacion);
                                    $infoServProdCaractPerfil->setEstado("Activo");
                                    $this->emComercial->persist($infoServProdCaractPerfil);
                                    $this->emComercial->flush();
                                    
                                    $entityPerfil = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                       ->findOneBy(array("descripcionCaracteristica" => "MIGRADO", 
                                                                         "estado"                    => "Activo"));
                                    $prodCaractPerfil = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                           ->findOneBy(array("productoId"       => $productoServicio->getId(), 
                                                                             "caracteristicaId" => $entityPerfil->getId(), 
                                                                             "estado"           => "Activo"));
                                    
                                    $infoServProdCaractPerfil = new InfoServicioProdCaract();
                                    $infoServProdCaractPerfil->setServicioId($arrayServicioIp['ID_SERVICIO']);
                                    $infoServProdCaractPerfil->setProductoCaracterisiticaId($prodCaractPerfil->getId());
                                    $infoServProdCaractPerfil->setValor('NO');
                                    $infoServProdCaractPerfil->setFeCreacion(new \DateTime('now'));
                                    $infoServProdCaractPerfil->setUsrCreacion($usrCreacion);
                                    $infoServProdCaractPerfil->setEstado("Activo");
                                    $this->emComercial->persist($infoServProdCaractPerfil);
                                    $this->emComercial->flush();
                                    
                                    $entityServicioTecnicoIp = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                  ->findOneByServicioId($arrayServicioIp['ID_SERVICIO']);
                                    if ($entityServicioTecnicoIp )
                                    {
                                        $entityServicioTecnicoIp->setInterfaceElementoConectorId($idInterfaceSplitter);
                                        $entityServicioTecnicoIp->setElementoId($idOlt);
                                        $entityServicioTecnicoIp->setInterfaceElementoId($idInterfaceOlt);
                                        $entityServicioTecnicoIp->setElementoConectorId($idSplitterHuawei);
                                        $this->emComercial->persist($entityServicioTecnicoIp);
                                        $this->emComercial->flush();
                                    }
                                    else
                                    {                                        
                                        $entityInfoServicioIp = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                                  ->find($arrayServicioIp['ID_SERVICIO']);
                                        $newInfoServicioTecnicoNew  = new InfoServicioTecnico();
                                        $newInfoServicioTecnicoNew->setUltimaMillaId($entityServicioTecnico->getUltimaMillaId());
                                        $newInfoServicioTecnicoNew->setServicioId($entityInfoServicioIp);
                                        $newInfoServicioTecnicoNew->setInterfaceElementoConectorId($idInterfaceSplitter);
                                        $newInfoServicioTecnicoNew->setElementoId($idOlt);
                                        $newInfoServicioTecnicoNew->setInterfaceElementoId($idInterfaceOlt);
                                        $newInfoServicioTecnicoNew->setElementoConectorId($idSplitterHuawei);
                                        $newInfoServicioTecnicoNew->setElementoContenedorId($entityServicioTecnico->getElementoContenedorId());
                                        $this->emComercial->persist($newInfoServicioTecnicoNew);
                                        $this->emComercial->flush();
                                    }
                                }
                            }//if (count($arrayServiciosIps)>0)
                            //Fin crear caracteristica

                            $entityServicioTecnico->setElementoId($idOlt);
                            $entityServicioTecnico->setInterfaceElementoId($idInterfaceOlt);
                            $entityServicioTecnico->setElementoConectorId($idSplitterHuawei);
                            $this->emComercial->persist($entityServicioTecnico);
                            $this->emComercial->flush();
                            
                            /* se agrega validacion para planes de clientes Tipo PRO que incluyan Ip como producto
                               para poder asignar las caracteristicas necesarias para la activacion */
                            if ( $nombreTipoNegocioPlan == 'PRO' ) 
                            {
                                $entityProductoIP = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                       ->obtenerProductoIp($entityServicio->getPlanId()->getId(),
                                                                           null,
                                                                           'asignacionRed');
                                if (!is_numeric($entityProductoIP))
                                {
                                    $nombreTipoNegocioPlan = 'PYME';
                                }
                            }
                            
                            if ($strMarcaOlt == 'HUAWEI')
                            {
                                $strPlanCaracUltraV           = $this->getCaracteristicaPorPlan($entityServicio->getPlanId()->getId());
                                $arrayProductoCaracteristicas = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                   ->getProductoCaracteristica($idEmpresa,
                                                                                               $idOlt,
                                                                                               $strPerfilEquivalente,
                                                                                               $nombreTipoNegocioPlan,
                                                                                               $strPlanCaracUltraV
                                                                                              );
                                if (count($arrayProductoCaracteristicas)==4)
                                {
                                    foreach($arrayProductoCaracteristicas as $productoCaracteristica)
                                    {
                                        $infoServProdCaractPerfil = new InfoServicioProdCaract();
                                        $infoServProdCaractPerfil->setServicioId($entityServicio->getId());
                                        $infoServProdCaractPerfil->setProductoCaracterisiticaId($productoCaracteristica['ID_PRODUCTO_CARACTERISITICA']);
                                        $infoServProdCaractPerfil->setValor($productoCaracteristica['DETALLE_VALOR']);
                                        $infoServProdCaractPerfil->setFeCreacion(new \DateTime('now'));
                                        $infoServProdCaractPerfil->setUsrCreacion($usrCreacion);
                                        $infoServProdCaractPerfil->setEstado("Activo");
                                        $this->emComercial->persist($infoServProdCaractPerfil);
                                        $this->emComercial->flush();
                                    }
                                }
                                else
                                {
                                    $mensaje = "No se puede realizar la asignación, faltan variables de configuracion para la activación en el OLT";
                                    throw new \Exception($mensaje);
                                }
                            }
                            else if ($strMarcaOlt != 'ZTE')
                            {
                                $mensaje = "No se puede realizar la asignación, tecnología ".$strMarcaOlt." no permitida.";
                                throw new \Exception($mensaje);
                            }
                        }//if ($nombreTecnico != "IP")
                    }
                    
                    $status  = "OK";
                    $mensaje = "Se guardo correctamente los Recursos de Red";
                                    
                }//if($sigue)
                else
                {
                    throw new \Exception($mensaje);
                }
            }
            else
            {
                $mensaje = "No existe el detalle de solicitud";
                throw new \Exception($mensaje);
            }
        }
        catch (\Exception $e)
        {
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
          
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            $status         = "ERROR";
            $mensaje        = "ERROR EN LA LOGICA DE NEGOCIO: <br> ".$e->getMessage();
            $respuestaFinal = array('status'=>$status, 'mensaje'=>$mensaje);

            return $respuestaFinal;
        }
        //*----------------------------------------------------------------------*/
        
        //*DECLARACION DE COMMITS*/
        if ($this->emInfraestructura->getConnection()->isTransactionActive())
        {
            $this->emInfraestructura->getConnection()->commit();
        }

        if ($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }
            
        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        //*----------------------------------------------------------------------*/
        
        //*RESPUESTA-------------------------------------------------------------*/
        $respuestaFinal = array('status'=>$status, 'mensaje'=>$mensaje);
        return $respuestaFinal;
        //*----------------------------------------------------------------------*/
    }
    
    /**
     * Funcion que sirve para grabar los recursos de Red para el producto internet dedicado
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 18-12-2015
     * 
     * Se agrega validación si es que existe el PE enlazado con el Switch
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1 2-05-2016
     * 
     * Se agrega invocacion a metodo que genera login auxiliar al servicios
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 9-05-2016
     *
     * Se recupera elementoPe desde ws networking     
     * @author Eduardo Plua <eplua@telconet.ec>
     * @version 1.3 26-05-2016
     * 
     * Se agrega parametros para soportar servicios con UM Radio
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 23-05-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.4 29-06-2016  Se realiza procesamiento para cambio de ultima milla
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.5 24-08-2016  Se agrega validaciones para poder soportar el proceso de cambio de Um Radio Tn
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.6 20-04-2017  Se soporta esquema de servicios pseudo pe para asignar recursos de red
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 17-07-2018 - Se enviá nuevo parámetro al método getPeBySwitch
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.8 - Por proyecto segmentacion de VLAN se agrega un parametro a la llamada de la funcion: getInfoBackboneByElemento
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.9 03-04-2020 - Se agregó la bandera de no eliminar el protocolo de enrutamiento
     *
     * @param array $arrayParametros [idServicio, hiloDisponible, vlan, ipPublica, mascaraPublica, empresaCod]
     * @return array $respuestaFinal
     */
    public function asignarRecursosRedInternetDedicado($arrayParametros)
    {
        $intIdServicio                  = $arrayParametros['idServicio'];
        $intIdDetalleSolicitud          = $arrayParametros['idDetalleSolicitud'];        
        $intVlan                        = $arrayParametros['vlan'];
        $strIpPublica                   = $arrayParametros['ipPublica'];
        $strMascaraPublica              = $arrayParametros['mascaraPublica'];        
        $intIdElementoPadre             = $arrayParametros['idElementoPadre'];     
        $strUltimaMilla                 = $arrayParametros['ultimaMilla'];
        $strUsrCreacion                 = $arrayParametros['usrCreacion'];
        $strIpCreacion                  = $arrayParametros['ipCreacion'];
        $strTipoSolicitud               = $arrayParametros['tipoSolicitud'];
        $boolEsPseudoPe                 = $arrayParametros['esPseudoPe'];
        $observacionServicioYSolicitud  = "";
        $boolEsCambioUM                 = false;
        $tipoFactibilidad               = "RUTA";
        $arrayParametrosWs              = array();

        if($strTipoSolicitud && $strTipoSolicitud == "SOLICITUD CAMBIO ULTIMA MILLA")
        {
            $boolEsCambioUM = true;
        }
        
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        
        try
        {            
            if($intIdElementoPadre == "No definido")
            {
                throw new \Exception("No Existe PE asociado al Switch, Favor Revisar!");
            }
            
            $objServicio                    = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);                       
            $objDetalleSolicitud            = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                       ->findOneById($intIdDetalleSolicitud);
            $objProducto                    = $objServicio->getProductoId();                    
            
            //Obtener la caracteristica TIPO_FACTIBILIDAD para discriminar que sea FIBRA DIRECTA o RUTA
            $objServProdCaractTipoFact = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,'TIPO_FACTIBILIDAD',$objProducto);                        
            
            if($objServProdCaractTipoFact)
            {
                $tipoFactibilidad = $objServProdCaractTipoFact->getValor();
            }
            else
            {
                if($strUltimaMilla == "Fibra Optica")
                {
                    $tipoFactibilidad = "RUTA";
                }
                else
                {
                    $tipoFactibilidad = "DIRECTO";
                }
            }                       
            
            //Se verifica que el cambio de recursos de red sea de Cambio de Ultima Milla
            if($boolEsCambioUM)            
            {
                $arrayParametros['booleanProtoEnru'] = false;
                $arrayParametros['tipoFactibilidad'] = $tipoFactibilidad;
                //se agrega validacion para ejecutar el proceso correspondiente segun la ultima milla del servicio
                if ($strUltimaMilla == "Radio")
                {
                    $this->asignacionRecursosRedUMRadio($arrayParametros);
                }
                else
                {
                    $this->asignacionRecursosRedUM($arrayParametros);
                }
            } 

            //se graba ip y mascara del tunel
            $objInfoIp = new InfoIp();
            $objInfoIp->setIp($strIpPublica);
            $objInfoIp->setServicioId($objServicio->getId());
            $objInfoIp->setMascara($strMascaraPublica);
            $objInfoIp->setEstado("Activo");
            $objInfoIp->setTipoIp("PUBLICA");
            $objInfoIp->setVersionIp("IPV4");
            $this->emInfraestructura->persist($objInfoIp);
            $this->emInfraestructura->flush();
            
            if(!$boolEsPseudoPe)
            {
                //obtener info detalle elemento
                $objDetalleElementoVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy(array( "elementoId"     => $intIdElementoPadre,
                                                                                       "detalleValor"   => $intVlan,
                                                                                       "detalleNombre"  => "VLAN"));
                //se graba caracteristica vlan
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "VLAN", 
                                                                               $objDetalleElementoVlan->getId(), $strUsrCreacion);
            }
            else
            {
                //se graba caracteristica vlan
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                               $objProducto, 
                                                                               "VLAN_PROVEEDOR", 
                                                                               $intVlan, 
                                                                               $strUsrCreacion);
            }
            
            //se actualiza el estado del servicio
            if(!$boolEsCambioUM)
            {
                $objServicio->setEstado("Asignada");
                $this->emComercial->persist($objServicio);
                $this->emComercial->flush();
            }
            
            $observacionServicioYSolicitud.="Elementos datos por Factibilidad<br/>";
            $arrParametrosFactibilidad      =   array (
                                                    'idServicio'    => $intIdServicio,
                                                    'emComercial'   => $this->emComercial
                                                );
            $arrInfoFactibilidad    = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->getDatosFactibilidad($arrParametrosFactibilidad);
            $arrayInfoFactibilidad  = $arrInfoFactibilidad['data'];

            $arrayParametrosWs["intIdElemento"] = $arrayInfoFactibilidad['idElemento'];
            $arrayParametrosWs["intIdServicio"] = $intIdServicio;

            $objElementoPe          = $this->servicioGeneral->getPeBySwitch($arrayParametrosWs);
            $arrInfoBackbone        = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                        ->getInfoBackboneByElemento($arrayInfoFactibilidad['idElemento'], $objElementoPe,"N");
            if(!$boolEsPseudoPe)
            {
                $arrMergeResultFactibilidad = array_merge($arrayInfoFactibilidad,$arrInfoBackbone);
            
                $observacionServicioYSolicitud.="Nombre Elemento Padre: ".$arrMergeResultFactibilidad["nombreElementoPadre"]."<br/>";
                $observacionServicioYSolicitud.="Nombre Elemento: ".$arrMergeResultFactibilidad["nombreElemento"]."<br/>";
                $observacionServicioYSolicitud.="Nombre Elemento Conector: ".$arrMergeResultFactibilidad["nombreElementoConector"]."<br/>";
                $observacionServicioYSolicitud.="Nombre Elemento Contenedor: ".$arrMergeResultFactibilidad["nombreElementoContenedor"]."<br/>";
                $observacionServicioYSolicitud.="Nombre Interface Elemento: ".$arrMergeResultFactibilidad["nombreElementoContenedor"]."<br/>";
                $observacionServicioYSolicitud.="Hilo: ".$arrMergeResultFactibilidad["numeroColorHilo"]."<br/>";
                $observacionServicioYSolicitud.="Anillo: ".$arrMergeResultFactibilidad["anillo"]."<br/>";
            }
            
            $observacionServicioYSolicitud.="Vlan: ".$intVlan."<br/>";
            
            $observacionServicioYSolicitud.="IP P&uacute;blica: ".$strIpPublica."<br/>";
            $observacionServicioYSolicitud.="M&aacute;scara: ".$strMascaraPublica."<br/>";
            
            if(!$boolEsCambioUM)
            {
                //agregar historial del servicio
                $objInfoHistorial = new InfoServicioHistorial();
                $objInfoHistorial->setServicioId($objServicio);
                $objInfoHistorial->setEstado("Asignada");
                $objInfoHistorial->setUsrCreacion($strUsrCreacion);
                $objInfoHistorial->setFeCreacion(new \DateTime('now'));
                $objInfoHistorial->setIpCreacion($strIpCreacion);
                $objInfoHistorial->setObservacion($observacionServicioYSolicitud);
                $this->emComercial->persist($objInfoHistorial);
                $this->emComercial->flush();                        
            }
            
            if($objDetalleSolicitud)
            {
                //se actualiza estado a la solicitud
                $objDetalleSolicitud->setEstado("Asignada");
                $this->emComercial->persist($objDetalleSolicitud);
                $this->emComercial->flush();

                //agregar historial a la solicitud
                $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
                $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
                $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitudHistorial->setUsrCreacion($strUsrCreacion);
                $objDetalleSolicitudHistorial->setEstado("Asignada");
                $objDetalleSolicitudHistorial->setObservacion($observacionServicioYSolicitud);
                $this->emComercial->persist($objDetalleSolicitudHistorial);
                $this->emComercial->flush();
            }
            else
            {
                throw new \Exception("No Existe Solicitud de Planificación, favor comunicarse con Sistemas!");
            }                        
            
            //Generacion de Login Auxiliar al Servicio            
            $this->servicioGeneral->generarLoginAuxiliar($intIdServicio);
                        
            $status         = "OK";
            $mensaje        = "OK";
        }
        catch (\Exception $e)
        {
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
          
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            $status         = "ERROR";
            $mensaje        = "ERROR EN LA LOGICA DE NEGOCIO: <br> ".$e->getMessage();
        }
        
        //*DECLARACION DE COMMITS*/
        if ($this->emInfraestructura->getConnection()->isTransactionActive())
        {
            $this->emInfraestructura->getConnection()->commit();
        }

        if ($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }
            
        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        //*----------------------------------------------------------------------*/
        
        //*RESPUESTA-------------------------------------------------------------*/
        $respuestaFinal = array('status'=>$status, 'mensaje'=>$mensaje);
        return $respuestaFinal;
        //*----------------------------------------------------------------------*/
    }
    
    /**
     * 
     * Metodo encargado para asignacion de recursos de red para Servicios de Internet DC
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 21-09-2017
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1
     * @since 07/03/2018 - Se ajusta para la solucion muestre el detalle en forma Multi Solucion ( NxN )
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 27-06-2018 - Se agrega parametro al llamado de la funcion crearTareaRetiroEquipoPorDemo
     * @since 1.1
     *
     * @param  Array $arrayParametros [
     *                                  intIdServicio
     *                                  intIdDetalleSolicitud
     *                                  strUltimaMilla
     *                                  strUsrCreacion
     *                                  strIpCreacion
     *                                  strTipoSolicitud
     *                                  jsonData
     *                                  strTipoRecursos
     *                                ]
     * @return Array [ status , mensaje ]
     * @throws \Exception
     */
    public function asignarRecursosRedInternetDC($arrayParametros)
    {
        $intIdServicio                  = $arrayParametros['intIdServicio'];
        $intIdDetalleSolicitud          = $arrayParametros['intIdDetalleSolicitud'];        
        $strUsrCreacion                 = $arrayParametros['strUsrCreacion'];
        $strIpCreacion                  = $arrayParametros['strIpCreacion'];
        $strJsonData                    = $arrayParametros['jsonData'];
        $strTipoRecursos                = $arrayParametros['strTipoRecursos'];
        $intIdEmpresa                   = $arrayParametros['intIdEmpresa'];
        $intElementoPadre               = $arrayParametros['intIdElementoPadre'];
        $strStatus                      = 'OK';
        $strMensaje                     = 'OK';
        $strObservacion                 = '';
        
        //Data de Recursos escogidos
        $arrayJsonData                  = json_decode($strJsonData)[0];
        
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        
        try
        {
            $objServicio         = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);                       
            $objDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdDetalleSolicitud);
            $objElementoPadre    = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intElementoPadre);
            
            $strNombreCanton     = $this->servicioGeneral->getCiudadRelacionadaPorRegion($objServicio,$intIdEmpresa);
            
            if(!is_object($objServicio))
            {
                throw new \Exception("OBSERVACION : No Existe Información de Servicio para asignar recursos de red");
            }
            
            $arrayParametrosEsCloud                = array();
            $arrayParametrosEsCloud['objServicio'] = $objServicio;
            
            $objProducto                    = $objServicio->getProductoId();
            
            //se graba caracteristica de tipo de recurso genericas para cualquier tipo de recurso escogido
            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                           $objProducto, 
                                                                           "TIPO_RECURSO_DC", 
                                                                           $strTipoRecursos, 
                                                                           $strUsrCreacion
                                                                           );
            
            if(!empty($arrayJsonData->vlanLan))
            {
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                               $objProducto, 
                                                                               "VLAN_LAN", 
                                                                               $arrayJsonData->vlanLan, 
                                                                               $strUsrCreacion
                                                                               );
            }
            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                            $objProducto, 
                                                                            "SUBRED_PRIVADA", 
                                                                            $arrayJsonData->redPrivada, 
                                                                            $strUsrCreacion
                                                                           );
            $intIdSubredCliente = 0;
            
            //Si es dedicado se guarda la informacion
            if($strTipoRecursos == 'dedicado')
            {
                $strTipoRecursosDedicado = $arrayJsonData->tipoRecursosDedicado;
                //Validar si la subred publica enviado no existe en otro cliente configurado
                
                if($strTipoRecursosDedicado == 'Nuevos')
                {
                    //Obtener una Ip disponible de la Subred asignada para enganche ( Publica )
                    $arrayParametrosSubred                  = array();
                    $arrayParametrosSubred['tipoAccion']    = "asignar";
                    $arrayParametrosSubred['uso']           = "INTERNETDC-DEDICADO-".$strNombreCanton;
                    $arrayParametrosSubred['mascara']       = trim($arrayJsonData->mascaraPublica);// mascaras /29

                    $arraySubred = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->provisioningSubred($arrayParametrosSubred);

                    if(!empty($arraySubred) && isset($arraySubred['subredId']) && $arraySubred['subredId']!=0)
                    {
                        $intIdSubredCliente = $arraySubred['subredId'];

                        //Obtener la Subred de enganche
                        $objSubredServicio = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->find($intIdSubredCliente);

                        if(is_object($objSubredServicio))
                        {
                            //Verificar si la subred puede ser utilizada, validando la misma con Networking
                            $arrayParametrosValidar['url']      = 'checkSubnet';
                            $arrayParametrosValidar['accion']   = 'checkSubnet';
                            $arrayParametrosValidar['servicio'] = 'INTERNETDC';
                            $arrayParametrosValidar['subred']   = str_replace($arrayJsonData->valorMascaraPublica,"",
                                                                              $objSubredServicio->getSubred());
                            $arrayParametrosValidar['mask']     = str_replace("/","",$arrayJsonData->valorMascaraPublica);
                            $arrayParametrosValidar['login']    = $objServicio->getPuntoId()->getLogin().'_dc';

                            $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayParametrosValidar);

                            if($arrayRespuesta['status'] == 'ERROR')
                            {
                                $arrayParametrosSubred['tipoAccion']    = "liberar";
                                $arrayParametrosSubred['subredId']      = $objSubredServicio->getId();
                                $arrayParametrosSubred['uso']           = "INTERNETDC-DEDICADO-".$strNombreCanton;

                                $arraySubred = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                       ->provisioningSubred($arrayParametrosSubred);

                                throw new \Exception($arrayRespuesta['mensaje']);
                            }
                        }
                    }
                    else
                    {
                        throw new \Exception("OBSERVACION : No existe Subredes Disponibles con la Máscara "
                                           . "( ".$arrayJsonData->valorMascaraPublica." ) "
                                           . "escogida, Notificar a Sistemas");
                    }
                }
                else //Recursos Existentes
                {
                    //SUBRED EXISTENTE
                    $objSubredServicio = $this->emInfraestructura->getRepository("schemaBundle:InfoSubred")->find($arrayJsonData->subredExistente);
                }
                
                if(!empty($arrayJsonData->vlanWanDedicado))
                {
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                                   $objProducto, 
                                                                                   "VLAN_WAN", 
                                                                                   $arrayJsonData->vlanWanDedicado, 
                                                                                   $strUsrCreacion
                                                                               );
                }
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                               $objProducto, 
                                                                               "FIREWALL_DC", 
                                                                               $arrayJsonData->firewallDC, 
                                                                               $strUsrCreacion
                                                                           );
                //Informacion de Subred de Enganche
                $intPrefijoRedDC  = 0;
                $strMascaraSubred = '';
                
                //Obtener una subred /29 disponible segun la ciudad u oficina a la que pertenece el cliente ( GYE/UIO )
                if(!empty($strNombreCanton))
                {
                    $arrayPrefijos =   $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                       ->getOne('PREFIJOS DE RED DATA CENTER', 
                                                                'TECNICO', 
                                                                '',
                                                                '', 
                                                                $strNombreCanton,//GUAYAQUIL/UIO
                                                                '',
                                                                '',
                                                                '', 
                                                                '', 
                                                                $intIdEmpresa);

                    if(!empty($arrayPrefijos))
                    {
                        $intPrefijoRedDC  = $arrayPrefijos['valor2'];
                        $strMascaraSubred = $arrayPrefijos['valor3'];
                    }
                }

                //Obtener la Subred Privada /29
                $arrayParametrosSubred                  = array();
                $arrayParametrosSubred['tipoAccion']    = "asignar";
                $arrayParametrosSubred['uso']           = "DATACENTER";
                $arrayParametrosSubred['mascara']       = trim($strMascaraSubred);// mascaras /29
                $arrayParametrosSubred['subredPrefijo'] = "10.".$intPrefijoRedDC;

                $arraySubred = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->provisioningSubred($arrayParametrosSubred);

                if(!empty($arraySubred))
                {
                    $intIdSubredCliente = $arraySubred['subredId'];
                    
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                    $objProducto, 
                                                                                    "SUBRED_ENGANCHE", 
                                                                                    $intIdSubredCliente, 
                                                                                    $strUsrCreacion
                                                                                   );
                    //Obtener la Subred de enganche
                    $objSubredPrivadaEnganche = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->find($intIdSubredCliente);
                }
                else
                {
                    throw new \Exception("OBSERVACION : No se pudo generar una Subred Máscara ( ".$strMascaraSubred." ) "
                                       . "disponible, Notificar a Sistemas");
                }
            }
            else//Recursos Compartidos
            {
                $intIdSubredCliente = $arrayJsonData->redPublicaCompartida;
                $objSubredServicio  = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->find($intIdSubredCliente);
            }
            
            if(is_object($objSubredServicio))
            {
                $objSubredServicio->setElementoId($objElementoPadre);
                $this->emInfraestructura->persist($objSubredServicio);
                $this->emInfraestructura->flush();
                
                if($strTipoRecursos == 'dedicado')
                {
                    //Obtener la Primera IP disponible
                    $strIpServicio = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                             ->getIpDisponibleBySubred($objSubredServicio->getId());
                }
                else
                {
                    $strIpServicio = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                             ->getIpDisponibleBySubred($intIdSubredCliente);
                }
                
                if($strIpServicio=="NoDisponible")
                {
                     throw new \Exception("OBSERVACION : No Existen IPs disponibles para la Subred Pública requerida, escoja otra Subred");
                }
            }
            else
            {
                throw new \Exception("OBSERVACION : No Existe Información de Subred para generar la IP del Servicio, notificar a Sistemas");
            }
           
            //se graba ip del servicio
            $objInfoIp = new InfoIp();
            $objInfoIp->setIp($strIpServicio);
            $objInfoIp->setSubredId($objSubredServicio->getId());
            $objInfoIp->setServicioId($objServicio->getId());
            $objInfoIp->setMascara($objSubredServicio->getMascara());
            $objInfoIp->setFeCreacion(new \DateTime('now'));
            $objInfoIp->setUsrCreacion($strUsrCreacion);
            $objInfoIp->setIpCreacion($strIpCreacion);
            $objInfoIp->setEstado("Activo");
            $objInfoIp->setTipoIp("WAN");
            $objInfoIp->setVersionIp("IPV4");
            $this->emInfraestructura->persist($objInfoIp);
            $this->emInfraestructura->flush();
            
            $strObservacion .= '<b>Se generaron los Siguientes recursos de Red:</b><br>';
            $strObservacion .= '<b>Tipos de  Recursos:</b>&nbsp;'.strtoupper($strTipoRecursos).'<br>';
            
            if($strTipoRecursos == 'dedicado')
            {
                $strObservacion .= '<b>Firewall DC:</b>&nbsp;'.$arrayJsonData->firewallDC.'<br>';
                $strObservacion .= '<b>Subred Privada Enganche:</b>&nbsp;'.$objSubredPrivadaEnganche->getSubred().'<br>';
                $strObservacion .= '<b>Subred Pública Enrutamiento:</b>&nbsp;'.$objSubredServicio->getSubred().'<br>';
                $strObservacion .= '<b>Vlan Wan:</b>&nbsp;'.$arrayJsonData->valorVlanWan.'<br>';
            }
            else
            {
                $strObservacion .= '<b>Subred Pública Definida:</b>&nbsp;'.$objSubredServicio->getSubred().'<br>';
            }
            
            $strObservacion .= '<b>Vlan Lan:</b>&nbsp;'.$arrayJsonData->valorVlanLan.'<br>';
            $strObservacion .= '<b>Ip del Servicio:</b>&nbsp;'.$strIpServicio.'<br>';
            $strObservacion .= '<b>Subred Lan (Cliente):</b>&nbsp;'.$arrayJsonData->redPrivada.'<br>';
            
            $strEstado        = 'Asignada';
            $boolEsCloud      = false;
            
            //Determinar si el enlace de internet posee un housing se procede a mandar estado Asignada, caso contrario
            //lo pone directamente en Pruebas porque no necesita activacion fisica
            $arrayRespuesta = $this->servicioGeneral->getArrayInformacionTipoSolucionPorPreferencial($objServicio);
            
            //Si no contiene Housing se sobreentiende que es un servicio o flujo hosting completo
            if(!$arrayRespuesta['boolContieneHousing'])
            {
                $strEstado   = 'EnPruebas';
                $boolEsCloud = true;
            }
            
            //se actualiza el estado del servicio
            $objServicio->setEstado($strEstado);
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();
            
            //agregar historial del servicio
            $objInfoHistorial = new InfoServicioHistorial();
            $objInfoHistorial->setServicioId($objServicio);
            $objInfoHistorial->setEstado($strEstado);
            $objInfoHistorial->setUsrCreacion($strUsrCreacion);
            $objInfoHistorial->setFeCreacion(new \DateTime('now'));
            $objInfoHistorial->setIpCreacion($strIpCreacion);
            $objInfoHistorial->setObservacion($strObservacion);
            $this->emComercial->persist($objInfoHistorial);
            $this->emComercial->flush();
            
            if(is_object($objDetalleSolicitud))
            {
                //se actualiza estado a la solicitud
                $objDetalleSolicitud->setEstado("Asignada");
                $this->emComercial->persist($objDetalleSolicitud);
                $this->emComercial->flush();

                //agregar historial a la solicitud
                $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
                $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
                $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitudHistorial->setUsrCreacion($strUsrCreacion);
                $objDetalleSolicitudHistorial->setEstado("Asignada");
                $objDetalleSolicitudHistorial->setObservacion($strObservacion);
                $this->emComercial->persist($objDetalleSolicitudHistorial);
                $this->emComercial->flush();
            }
            else
            {
                throw new \Exception("OBSERVACION : No Existe Solicitud de Planificación, favor comunicarse con Sistemas!");
            }
            
            $this->servicioGeneral->generarLoginAuxiliar($intIdServicio);
            
            //Generar notificacion de los recursos de red generados para procesos de DC
            
            $objCanton = $this->emGeneral->getRepository("schemaBundle:AdmiCanton")
                                         ->findOneByNombreCanton($strNombreCanton);
            
            $strLogin  = $objServicio->getPuntoId()->getLogin();
            
            $arrayParametrosSolucion                  = array();
            $arrayParametrosSolucion['objServicio']   = $objServicio;
            $arrayParametrosSolucion['strCodEmpresa'] = $intIdEmpresa;
            $strSolucion       = $this->servicioGeneral->getNombreGrupoSolucionServicios($arrayParametrosSolucion);
            
            $arrayParametrosEnvioPlantilla                      = array();
            $arrayParametrosEnvioPlantilla['strObservacion']    = 'Tarea Automática: Se solicita realizar Activación de servicios de Internet DC '.
                                                                  '<br><b>Login : </b> '.$strLogin.'<br>'.$strSolucion;
            $arrayParametrosEnvioPlantilla['strUsrCreacion']    = $strUsrCreacion;
            $arrayParametrosEnvioPlantilla['strIpCreacion']     = $strIpCreacion;
            $arrayParametrosEnvioPlantilla['intDetalleSolId']   = $objDetalleSolicitud->getId();
            $arrayParametrosEnvioPlantilla['strTipoAfectado']   = 'Cliente';
            $arrayParametrosEnvioPlantilla['objPunto']          = $objServicio->getPuntoId();
            $arrayParametrosEnvioPlantilla['strCantonId']       = is_object($objCanton)?$objCanton->getId():0;
            $arrayParametrosEnvioPlantilla['strEmpresaCod']     = $intIdEmpresa;
            $arrayParametrosEnvioPlantilla['strPrefijoEmpresa'] = $arrayParametros['prefijoEmpresa'];

            $arrayInfoEnvio   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('HOSTING TAREAS POR DEPARTAMENTO', 
                                                      'SOPORTE', 
                                                      '',
                                                      'ACTIVACION INTERNET HOSTING',
                                                      $strNombreCanton,//GUAYAQUIL/UIO 
                                                      '',
                                                      '',
                                                      '', 
                                                      '', 
                                                      $intIdEmpresa);
            foreach($arrayInfoEnvio as $array)                    
            {
                $objTarea  = $this->emSoporte->getRepository("schemaBundle:AdmiTarea")->findOneByNombreTarea($array['valor3']);

                $arrayParametrosEnvioPlantilla['intTarea']     = is_object($objTarea)?$objTarea->getId():'';
                $arrayParametrosEnvioPlantilla['arrayCorreos'] = array($array['valor2']);

                $objDepartamento = $this->emSoporte->getRepository("schemaBundle:AdmiDepartamento")
                                                   ->findOneByNombreDepartamento($array['valor4']);

                $arrayParametrosEnvioPlantilla['objDepartamento']   = $objDepartamento;
                $arrayParametrosEnvioPlantilla["strBanderaTraslado"] = "";
                $strNumeroTarea = $this->serviceCambioPlan->crearTareaRetiroEquipoPorDemo($arrayParametrosEnvioPlantilla);
            }
            
            $strRazonSocial = '';
            $objDetalleVlan = null;
            
            $objPersonaRol = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")->find($arrayParametros['intIdPersonaRol']);

            if(is_object($objPersonaRol))
            {
                $strRazonSocial = $objPersonaRol->getPersonaId()->getInformacionPersona();
            }

            if(!empty($arrayJsonData->vlanLan))
            {
                //Determinar valor de la Vlan
                $objCaractRol = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRolCarac")->find($arrayJsonData->vlanLan);

                if(is_object($objCaractRol))
                {
                    $objDetalleVlan = $this->emComercial->getRepository("schemaBundle:InfoDetalleElemento")->find($objCaractRol->getValor());
                }
            }

            //Se envia notificacion con los datos necesarios a las diferentes areas involucradas
            $arrayNotificacion                   = array();
            $arrayNotificacion['razonSocial']    = $strRazonSocial;
            $arrayNotificacion['login']          = $strLogin;
            $arrayNotificacion['capacidad1']     = $arrayParametros['intCapacidad1'];
            $arrayNotificacion['capacidad2']     = $arrayParametros['intCapacidad2'];
            $arrayNotificacion['tipoSolucion']   = $arrayParametros['strTipoSolucion'];
            $arrayNotificacion['pe']             = $arrayParametros['strNombrePe'];
            $arrayNotificacion['switch']         = $arrayParametros['strNombreSwitch'];
            $arrayNotificacion['interface']      = $arrayParametros['strNombrePuerto'];
            $arrayNotificacion['tipoRecursos']   = ucwords($strTipoRecursos);
            $arrayNotificacion['vlanLan']        = is_object($objDetalleVlan)?$objDetalleVlan->getDetalleValor():'N/A';
            $arrayNotificacion['subredLanCliente']       = $arrayJsonData->redPrivada;
            $arrayNotificacion['recursosRed']            = $strTipoRecursosDedicado;
            $arrayNotificacion['subredPublica']          = $objSubredServicio->getSubred();
            $arrayNotificacion['mascaraSubredPublica']   = $objSubredServicio->getMascara();
            $arrayNotificacion['ipServicio']             = $strIpServicio;
            $arrayNotificacion['idServicio']             = $intIdServicio;
            $arrayNotificacion['host']                   = $this->host;
            $arrayNotificacion['isCloud']                = $boolEsCloud;
            //...

            //Se obtiene correo del vendedor
            $objVendedor = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                             ->findOneBy(array('login'  => $objServicio->getUsrVendedor()));
            $arrayCorreo = array();

            if(is_object($objVendedor))
            {
                $objFormaContacto = $this->emComercial->getRepository("schemaBundle:AdmiFormaContacto")
                                                      ->findOneBy(array('descripcionFormaContacto' => 'Correo Electronico',
                                                                        'estado'                   => 'Activo'
                                                                 ));
                if(is_object($objFormaContacto))
                {
                    $objInfoPersonaFormaContacto = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                        ->findOneBy(array('personaId'       => $objVendedor->getId(),
                                                                          'formaContactoId' => $objFormaContacto->getId(),
                                                                          'estado'          => "Activo"));
                    //OBTENGO EL CONTACTO DE LA PERSONA QUE ASIGNADA A LA TAREA
                    if($objInfoPersonaFormaContacto)
                    {
                        $arrayCorreo[] = $objInfoPersonaFormaContacto->getValor(); //Correo Persona Asignada
                    }  
                }
            }

            $this->serviceEnvioPlantilla->generarEnvioPlantilla('Asignación de Recursos de Red para Servicio INTERNET DC : '.$strLogin,
                                                                $arrayCorreo,
                                                                'ACTINTERNETDC',
                                                                $arrayNotificacion,
                                                                $intIdEmpresa,
                                                                is_object($objCanton)?$objCanton->getId():0,
                                                                null
                                                               );
            
            $this->emComercial->commit();
            $this->emInfraestructura->commit();
        } 
        catch (\Exception $ex) 
        {
            $strStatus      = 'ERROR';
            $strMensaje     = 'Error al Asignar Recursos de Red para Servicio de INTERNET DC, notificar a Sistemas';
            
            if(strpos($ex->getMessage(),'OBSERVACION')!==false)
            {
                $strMensaje = $ex->getMessage();
            }
            
            $this->utilServicio->insertError('Telcos+', 
                                             'asignarRecursosRedInternetDC', 
                                             $ex->getMessage(), 
                                             $strUsrCreacion, 
                                             $strIpCreacion
                                            );
            
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->rollback();
            }

            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
            }
            
            $this->emComercial->close();
        }
        
        $arrayResultado = array('status' => $strStatus , 'mensaje' => $strMensaje);
        
        return $arrayResultado;
    }
    
    
    /**
     * 
     * Metodo encargado para asignacion de recursos de red para Servicios de Datos DC y Concentradores DC
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 21-09-2017
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 27-06-2018 - Se agrega parametro al llamado de la funcion crearTareaRetiroEquipoPorDemo
     * @since 1.0
     *
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 17-07-2017 - Se realiza ajuste para que al momento de asignar Recursos de Red para DatosDC y estos sean nuevos, se asigne al
     *                           servicio siempre la 4ta Ip de la máscara asignada, dado que las dos primeras utilizables son requeridas para que
     *                           se configuren en los PEs
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.3 04-09-2019 - Se modifica la observación al momento de asignar recursos de red, aparezca la descripción 
     *                           del producto.
     * 
     * @param  Array $arrayParametros [
     *                                  intIdServicio
     *                                  intIdDetalleSolicitud
     *                                  strUltimaMilla
     *                                  strUsrCreacion
     *                                  strIpCreacion
     *                                  strTipoSolicitud
     *                                  jsonData
     *                                  strTipoRecursos
     *                                ]
     * @return Array [ status , mensaje ]
     * @throws \Exception
     */
    public function asignarRecursosRedDatosDC($arrayParametros)
    {
        $intIdServicio                  = $arrayParametros['intIdServicio'];
        $intIdDetalleSolicitud          = $arrayParametros['intIdDetalleSolicitud'];        
        $strUsrCreacion                 = $arrayParametros['strUsrCreacion'];
        $strIpCreacion                  = $arrayParametros['strIpCreacion'];
        $strJsonData                    = $arrayParametros['jsonData'];
        $strTipoRecursos                = $arrayParametros['strTipoRecursos'];
        $intIdEmpresa                   = $arrayParametros['intIdEmpresa'];
        $intElementoPadre               = $arrayParametros['intIdElementoPadre'];
        $personaEmpresaRolId            = $arrayParametros['intIdPersonaRol'];        
        $strStatus                      = 'OK';
        $strMensaje                     = 'OK';
        $strObservacion                 = '';
        
        //Data de Recursos escogidos
        $arrayJsonData      = json_decode($strJsonData)[0];
        
        $intIdSubred        = $arrayJsonData->idSubred;
        $strMascara         = $arrayJsonData->mascara;
        $intVrf             = $arrayJsonData->vrf;
        $strProtocolo       = $arrayJsonData->protocolo;
        $intAsPrivado       = $arrayJsonData->asPrivado;
        $boolDefaultGateway = $arrayJsonData->defaultGateway;
        
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        
        try
        {
            $objServicio         = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);                       
            $objDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdDetalleSolicitud);
            $objElementoPadre    = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intElementoPadre);
            
            $strNombreCanton     = $this->servicioGeneral->getCiudadRelacionadaPorRegion($objServicio,$intIdEmpresa);
            
            if(!is_object($objServicio))
            {
                throw new \Exception("OBSERVACION : No Existe Información de Servicio para asignar recursos de red");
            }
            
            $objProducto = $objServicio->getProductoId();
            
            $arrayParametrosEsCloud                = array();
            $arrayParametrosEsCloud['objServicio'] = $objServicio;
            
            //Vlan Lan
            if(!empty($arrayJsonData->vlanLan))
            {
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                               $objProducto, 
                                                                               "VLAN_LAN", 
                                                                               $arrayJsonData->vlanLan, 
                                                                               $strUsrCreacion
                                                                               );
            }
            
            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                            $objProducto, 
                                                                            "SUBRED_PRIVADA", 
                                                                            $arrayJsonData->redPrivada, 
                                                                            $strUsrCreacion
                                                                           );
            
            //Vlan Wan
            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                           $objProducto, 
                                                                           "VLAN_WAN", 
                                                                           $arrayJsonData->vlanWan, 
                                                                           $strUsrCreacion
                                                                       );
            
            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                           $objProducto, 
                                                                           "FIREWALL_DC", 
                                                                           $arrayJsonData->firewallDC, 
                                                                           $strUsrCreacion
                                                                       );
            $intIdSubredCliente = 0;
            
            //Recursos existentes
            if($strTipoRecursos === "existente")
            {                
                $objSubredServicio  = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->find($intIdSubred);
                
                if(is_object($objSubredServicio))
                {
                    $strMascara     = $objSubredServicio->getMascara();
                    $strIpServicio  = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')->getIpDisponibleBySubred($intIdSubred);
                }
                else
                {
                    throw new \Exception("OBSERVACION : No Existe Información de Subred a heredad para asignar recursos existentes");
                }
            }
            else
            {
                //Informacion de Subred de Enganche
                $intPrefijoRedDC  = 0;                

                //Obtener una subred /29 disponible segun la ciudad u oficina a la que pertenece el cliente ( GYE/UIO )
                if(!empty($strNombreCanton))
                {
                    $arrayPrefijos =   $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                       ->getOne('PREFIJOS DE RED DATA CENTER', 
                                                                'TECNICO', 
                                                                '',
                                                                '', 
                                                                $strNombreCanton,//GUAYAQUIL/UIO
                                                                '',
                                                                '',
                                                                '', 
                                                                '', 
                                                                $intIdEmpresa);

                    if(!empty($arrayPrefijos))
                    {
                        $intPrefijoRedDC  = $arrayPrefijos['valor2'];//Obtiene el prefijo para DC
                    }
                }

                //Obtener la Subred Privada /29
                $arrayParametrosSubred['tipoAccion']    = "asignar";
                $arrayParametrosSubred['uso']           = "DATACENTER";
                $arrayParametrosSubred['mascara']       = trim($strMascara);
                $arrayParametrosSubred['subredPrefijo'] = "10.".$intPrefijoRedDC;

                $arraySubred = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->provisioningSubred($arrayParametrosSubred);

                if(isset($arraySubred['subredId']) && $arraySubred['subredId']>0)
                {                    
                    $intIdSubredCliente = $arraySubred['subredId'];
                    $objSubredServicio  = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->find($arraySubred['subredId']);
                    $objSubredServicio->setElementoId($objElementoPadre);
                    $this->emInfraestructura->persist($objSubredServicio);
                    $this->emInfraestructura->flush();

                    $strIpServicio = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                             ->getIpDisponibleBySubred($arraySubred['subredId']);
                }
                else
                {
                    throw new \Exception("OBSERVACION : No se pudo generar una Subred Máscara ( ".$strMascara." ) "
                                       . "disponible, Notificar a Sistemas");
                }   
            }
            
            if($strIpServicio=="NoDisponible")
            {
                 throw new \Exception("OBSERVACION : No Existen IPs disponibles para la Subred Pública requerida, escoja otra Subred");
            }
            if(!is_object($objSubredServicio))
            {
                throw new \Exception("OBSERVACION : No Existe Información de Subred para generar la IP del Servicio, notificar a Sistemas");
            }
                        
            //Si la asignación de recursos es nueva se asigna siempre la 4ta Ip al servicio del cliente
            if($strTipoRecursos != "existente")
            {
                $arrayOctetos  = explode(".", $strIpServicio);
                $strIpPe1      = $strIpServicio;

                //Se suma 2 octetos para obtener la 4ta IP de la subred generada
                $strOctetoP2   = intval($arrayOctetos[3]) + 1;           
                $strIpPe2      = $arrayOctetos[0].'.'.$arrayOctetos[1].'.'.$arrayOctetos[2].'.'.$strOctetoP2;

                $strOctetoIp   = intval($arrayOctetos[3]) + 2;
                $strIpServicio = $arrayOctetos[0].'.'.$arrayOctetos[1].'.'.$arrayOctetos[2].'.'.$strOctetoIp;

                //Ocupar las Ips para los Pes
                $objInfoIp = new InfoIp();
                $objInfoIp->setIp($strIpPe1);
                $objInfoIp->setSubredId($objSubredServicio->getId());                
                $objInfoIp->setMascara($objSubredServicio->getMascara());
                $objInfoIp->setFeCreacion(new \DateTime('now'));
                $objInfoIp->setUsrCreacion($strUsrCreacion);
                $objInfoIp->setIpCreacion($strIpCreacion);
                $objInfoIp->setEstado("Activo");
                $objInfoIp->setTipoIp("WAN");
                $objInfoIp->setVersionIp("IPV4");
                $this->emInfraestructura->persist($objInfoIp);
                $this->emInfraestructura->flush();

                $objInfoIp = new InfoIp();
                $objInfoIp->setIp($strIpPe2);
                $objInfoIp->setSubredId($objSubredServicio->getId());
                $objInfoIp->setMascara($objSubredServicio->getMascara());
                $objInfoIp->setFeCreacion(new \DateTime('now'));
                $objInfoIp->setUsrCreacion($strUsrCreacion);
                $objInfoIp->setIpCreacion($strIpCreacion);
                $objInfoIp->setEstado("Activo");
                $objInfoIp->setTipoIp("WAN");
                $objInfoIp->setVersionIp("IPV4");
                $this->emInfraestructura->persist($objInfoIp);
                $this->emInfraestructura->flush();
            }
                        
            //se graba ip del servicio
            $objInfoIp = new InfoIp();
            $objInfoIp->setIp($strIpServicio);
            $objInfoIp->setSubredId($objSubredServicio->getId());
            $objInfoIp->setServicioId($objServicio->getId());
            $objInfoIp->setMascara($objSubredServicio->getMascara());
            $objInfoIp->setFeCreacion(new \DateTime('now'));
            $objInfoIp->setUsrCreacion($strUsrCreacion);
            $objInfoIp->setIpCreacion($strIpCreacion);
            $objInfoIp->setEstado("Activo");
            $objInfoIp->setTipoIp("WAN");
            $objInfoIp->setVersionIp("IPV4");
            $this->emInfraestructura->persist($objInfoIp);
            $this->emInfraestructura->flush();
            
            //Guardar valores caracteristicas basicos         
            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                           $objProducto, 
                                                                           "VRF", 
                                                                           $intVrf, 
                                                                           $strUsrCreacion);
            
            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                           $objProducto, 
                                                                           "PROTOCOLO_ENRUTAMIENTO", 
                                                                           $strProtocolo, 
                                                                           $strUsrCreacion);
            
            
            $strObservacion .= '<b>Se generaron los Siguientes recursos de Red:</b><br>';
            $strObservacion .= '<b>Tipos de  Recursos:</b>&nbsp;'.strtoupper($strTipoRecursos).'<br>';
            $strObservacion .= '<b>Firewall DC:</b>&nbsp;'.$arrayJsonData->firewallDC.'<br>';
            $strObservacion .= '<b>Subred Lan (Cliente):</b>&nbsp;'.$arrayJsonData->redPrivada.'<br>';
            $strObservacion .= '<b>Vlan Wan:</b>&nbsp;'.$arrayJsonData->valorVlanWan.'<br>';
            
            if(!empty($arrayJsonData->valorVlanLan))   
            {
                $strObservacion .= '<b>Vlan Lan:</b>&nbsp;'.$arrayJsonData->valorVlanLan.'<br>';
            }
            
            $strObservacion .= '<b>Ip del Servicio:</b>&nbsp;'.$strIpServicio.'<br>';
            
            if($strTipoRecursos != "existente")
            {
                $strObservacion .= '<b>Ip aplicada al Pe1:</b>&nbsp;'.$strIpPe1.'<br>';
                $strObservacion .= '<b>Ip aplicada al Pe2:</b>&nbsp;'.$strIpPe2.'<br>';
            }
                        
            $objVrf   = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->find($intVrf);
            
            if(is_object($objVrf))
            {
                $strNombreVrf    = $objVrf->getValor();
                $strObservacion .= '<b>Vrf:</b>&nbsp;'.$strNombreVrf.'<br>';
            }
            
            $strObservacion .= '<b>Protocolo:</b>&nbsp;'.$strProtocolo.'<br>';
            $strObservacion .= '<b>Máscara:</b>&nbsp;'.$strMascara.'<br>';
            
            //Default Gateway y protcolo de enrutamiento
            if($strProtocolo=="BGP")
            {
                if($boolDefaultGateway=="true")
                {
                    $strDefaultGateway = 'SI';
                }
                else
                {
                    $strDefaultGateway = 'NO';
                }            

                $objDefaultGateway = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 
                                                                                               "DEFAULT_GATEWAY", 
                                                                                               $objProducto);
                if($objDefaultGateway)
                {
                    $objDefaultGateway->setValor($strDefaultGateway);
                    $this->emComercial->persist($objDefaultGateway);
                    $this->emComercial->flush();
                }
                else
                {
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                                   $objProducto, 
                                                                                   "DEFAULT_GATEWAY", 
                                                                                   $strDefaultGateway, 
                                                                                   $strUsrCreacion);
                }
                
                $strObservacion .= '<b>Neighbor Default: :</b>&nbsp;'.$strDefaultGateway.'<br>';
            }
            
            //As Privado
            $objAsPrivado = $this->emComercial
                                 ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                 ->getOneByCaracteristica($personaEmpresaRolId,"AS_PRIVADO");
            if($intAsPrivado>0 && !is_object($objAsPrivado))
            {
                $objPersonaEmpresaRol        = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                 ->find($personaEmpresaRolId);
                
                $objCaracteristicaAsPrivado  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                 ->findOneBy(array("descripcionCaracteristica" =>"AS_PRIVADO", "estado" => "Activo"));
                
                $objInfoPersonaEmpresaRolCaracAsprivado = new InfoPersonaEmpresaRolCarac();
                $objInfoPersonaEmpresaRolCaracAsprivado->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                $objInfoPersonaEmpresaRolCaracAsprivado->setCaracteristicaId($objCaracteristicaAsPrivado);
                $objInfoPersonaEmpresaRolCaracAsprivado->setValor($intAsPrivado);
                $objInfoPersonaEmpresaRolCaracAsprivado->setEstado("Activo");
                $objInfoPersonaEmpresaRolCaracAsprivado->setFeCreacion(new \DateTime('now'));
                $objInfoPersonaEmpresaRolCaracAsprivado->setUsrCreacion($strUsrCreacion);
                $objInfoPersonaEmpresaRolCaracAsprivado->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objInfoPersonaEmpresaRolCaracAsprivado);
                $this->emComercial->flush();
            }
            
            if($intAsPrivado>0)
            {
                $strObservacion .= '<b>As Privado:</b>&nbsp;'.$intAsPrivado.'<br>';
            }
            
            $strEstado        = 'Asignada';
            $boolEsCloud      = false;
            
            //Determinar si el enlace de internet posee un housing se procede a mandar estado Asignada, caso contrario
            //lo pone directamente en Pruebas porque no necesita activacion fisica
            $arrayRespuesta = $this->servicioGeneral->getArrayInformacionTipoSolucionPorPreferencial($objServicio);
            
            //Si no contiene Housing se sobreentiende que es un servicio o flujo hosting completo
            if(!$arrayRespuesta['boolContieneHousing'])
            {
                $strEstado   = 'EnPruebas';
                $boolEsCloud = true;
            }
            
            //se actualiza el estado del servicio
            $objServicio->setEstado($strEstado);
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();
            
            //agregar historial del servicio
            $objInfoHistorial = new InfoServicioHistorial();
            $objInfoHistorial->setServicioId($objServicio);
            $objInfoHistorial->setEstado($strEstado);
            $objInfoHistorial->setUsrCreacion($strUsrCreacion);
            $objInfoHistorial->setFeCreacion(new \DateTime('now'));
            $objInfoHistorial->setIpCreacion($strIpCreacion);
            $objInfoHistorial->setObservacion($strObservacion);
            $this->emComercial->persist($objInfoHistorial);
            $this->emComercial->flush();
            
            if(is_object($objDetalleSolicitud))
            {
                //se actualiza estado a la solicitud
                $objDetalleSolicitud->setEstado("Asignada");
                $this->emComercial->persist($objDetalleSolicitud);
                $this->emComercial->flush();

                //agregar historial a la solicitud
                $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
                $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
                $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitudHistorial->setUsrCreacion($strUsrCreacion);
                $objDetalleSolicitudHistorial->setEstado("Asignada");
                $objDetalleSolicitudHistorial->setObservacion($strObservacion);
                $this->emComercial->persist($objDetalleSolicitudHistorial);
                $this->emComercial->flush();
            }
            else
            {
                throw new \Exception("OBSERVACION : No Existe Solicitud de Planificación, favor comunicarse con Sistemas!");
            }
            
            $this->servicioGeneral->generarLoginAuxiliar($intIdServicio);
            
            //Generar notificacion de los recursos de red generados para procesos de DC
            
            $objCanton = $this->emGeneral->getRepository("schemaBundle:AdmiCanton")
                                         ->findOneByNombreCanton($strNombreCanton);
            
            $strLogin  = $objServicio->getPuntoId()->getLogin();
            
            $arrayParametrosSolucion                  = array();
            $arrayParametrosSolucion['objServicio']   = $objServicio;
            $arrayParametrosSolucion['strCodEmpresa'] = $intIdEmpresa;
            $strSolucion       = $this->servicioGeneral->getNombreGrupoSolucionServicios($arrayParametrosSolucion);
            
            $arrayParametrosEnvioPlantilla                      = array();
            $arrayParametrosEnvioPlantilla['strObservacion']    = 'Tarea Automática: Se solicita realizar activación de servicios de '.
                                                                        $objProducto->getDescripcionProducto().
                                                                        ' <br><b>Login : </b> '.$strLogin.'<br>'.$strSolucion;
            $arrayParametrosEnvioPlantilla['strUsrCreacion']    = $strUsrCreacion;
            $arrayParametrosEnvioPlantilla['strIpCreacion']     = $strIpCreacion;
            $arrayParametrosEnvioPlantilla['intDetalleSolId']   = $objDetalleSolicitud->getId();
            $arrayParametrosEnvioPlantilla['strTipoAfectado']   = 'Cliente';
            $arrayParametrosEnvioPlantilla['objPunto']          = $objServicio->getPuntoId();
            $arrayParametrosEnvioPlantilla['strCantonId']       = is_object($objCanton)?$objCanton->getId():0;
            $arrayParametrosEnvioPlantilla['strEmpresaCod']     = $intIdEmpresa;
            $arrayParametrosEnvioPlantilla['strPrefijoEmpresa'] = $arrayParametros['prefijoEmpresa'];

            $arrayInfoEnvio   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('HOSTING TAREAS POR DEPARTAMENTO', 
                                                      'SOPORTE', 
                                                      '',
                                                      'ACTIVACION INTERNET HOSTING',
                                                      $strNombreCanton,//GUAYAQUIL/UIO 
                                                      '',
                                                      '',
                                                      '', 
                                                      '', 
                                                      $intIdEmpresa);
            foreach($arrayInfoEnvio as $array)                    
            {
                $objTarea  = $this->emSoporte->getRepository("schemaBundle:AdmiTarea")->findOneByNombreTarea($array['valor3']);

                $arrayParametrosEnvioPlantilla['intTarea']     = is_object($objTarea)?$objTarea->getId():'';
                $arrayParametrosEnvioPlantilla['arrayCorreos'] = array($array['valor2']);

                $objDepartamento = $this->emSoporte->getRepository("schemaBundle:AdmiDepartamento")
                                                   ->findOneByNombreDepartamento($array['valor4']);

                $arrayParametrosEnvioPlantilla['objDepartamento']   = $objDepartamento;
                $arrayParametrosEnvioPlantilla["strBanderaTraslado"] = "";
                $strNumeroTarea = $this->serviceCambioPlan->crearTareaRetiroEquipoPorDemo($arrayParametrosEnvioPlantilla);
            }
            
            $strRazonSocial = '';
            $objDetalleVlan = null;
            
            $objPersonaRol = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")->find($arrayParametros['intIdPersonaRol']);

            if(is_object($objPersonaRol))
            {
                $strRazonSocial = $objPersonaRol->getPersonaId()->getInformacionPersona();
            }

            if(!empty($arrayJsonData->vlanLan))
            {
                //Determinar valor de la Vlan
                $objCaractRol = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRolCarac")->find($arrayJsonData->vlanLan);

                if(is_object($objCaractRol))
                {
                    $objDetalleVlan = $this->emComercial->getRepository("schemaBundle:InfoDetalleElemento")->find($objCaractRol->getValor());
                }
            }

            //Se envia notificacion con los datos necesarios a las diferentes areas involucradas
            $arrayNotificacion                   = array();
            $arrayNotificacion['razonSocial']    = $strRazonSocial;
            $arrayNotificacion['login']          = $strLogin;
            $arrayNotificacion['capacidad1']     = $arrayParametros['intCapacidad1'];
            $arrayNotificacion['capacidad2']     = $arrayParametros['intCapacidad2'];
            $arrayNotificacion['tipoSolucion']   = $arrayParametros['strTipoSolucion'];
            $arrayNotificacion['pe']             = $arrayParametros['strNombrePe'];
            $arrayNotificacion['switch']         = $arrayParametros['strNombreSwitch'];
            $arrayNotificacion['interface']      = $arrayParametros['strNombrePuerto'];
            $arrayNotificacion['tipoRecursos']   = ucwords($strTipoRecursos);
            $arrayNotificacion['vlanLan']        = is_object($objDetalleVlan)?$objDetalleVlan->getDetalleValor():'N/A';
            $arrayNotificacion['subredLanCliente']       = $arrayJsonData->redPrivada; 
            $arrayNotificacion['subredPublica']          = $objSubredServicio->getSubred();
            $arrayNotificacion['mascaraSubredPublica']   = $objSubredServicio->getMascara();
            $arrayNotificacion['ipServicio']             = $strIpServicio;
            $arrayNotificacion['idServicio']             = $intIdServicio;
            $arrayNotificacion['host']                   = $this->host;
            $arrayNotificacion['isCloud']                = $boolEsCloud;
            //...

            //Se obtiene correo del vendedor
            $objVendedor = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                             ->findOneBy(array('login'  => $objServicio->getUsrVendedor()));
            $arrayCorreo = array();

            if(is_object($objVendedor))
            {
                $objFormaContacto = $this->emComercial->getRepository("schemaBundle:AdmiFormaContacto")
                                                      ->findOneBy(array('descripcionFormaContacto' => 'Correo Electronico',
                                                                        'estado'                   => 'Activo'
                                                                 ));
                if(is_object($objFormaContacto))
                {
                    $objInfoPersonaFormaContacto = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                        ->findOneBy(array('personaId'       => $objVendedor->getId(),
                                                                          'formaContactoId' => $objFormaContacto->getId(),
                                                                          'estado'          => "Activo"));
                    //OBTENGO EL CONTACTO DE LA PERSONA QUE ASIGNADA A LA TAREA
                    if($objInfoPersonaFormaContacto)
                    {
                        $arrayCorreo[] = $objInfoPersonaFormaContacto->getValor(); //Correo Persona Asignada
                    }  
                }
            }

            $this->serviceEnvioPlantilla->generarEnvioPlantilla('Asignación de Recursos de Red para Servicio DATOS DC : '.$strLogin,
                                                                $arrayCorreo,
                                                                'ACTINTERNETDC',
                                                                $arrayNotificacion,
                                                                $intIdEmpresa,
                                                                is_object($objCanton)?$objCanton->getId():0,
                                                                null
                                                               );
            
            $this->emComercial->commit();
            $this->emInfraestructura->commit();
        } 
        catch (\Exception $ex) 
        {
            $strStatus      = 'ERROR';
            $strMensaje     = 'Error al Asignar Recursos de Red para Servicio de INTERNET DC, notificar a Sistemas';
            
            if(strpos($ex->getMessage(),'OBSERVACION')!==false)
            {
                $strMensaje = $ex->getMessage();
            }
            
            $this->utilServicio->insertError('Telcos+', 
                                             'asignarRecursosRedInternetDC', 
                                             $ex->getMessage(), 
                                             $strUsrCreacion, 
                                             $strIpCreacion
                                            );
            
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->rollback();
            }

            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
            }
            
            $this->emComercial->close();
        }
        
        $arrayResultado = array('status' => $strStatus , 'mensaje' => $strMensaje);
        
        return $arrayResultado;
    }
    
    /**
     * 
     * Metodo encargado de realizar la asignación de recursos de red de manera automatica para servicios L2MPLS
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 11-05-2018
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 27-06-2018 - Se agrega parametro al llamado de la funcion crearTareaRetiroEquipoPorDemo
     * @since 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 17-07-2018 - Se enviá nuevo parámetro al método getPeBySwitch
     * @since 1.1
     *
     * @param Array $arrayParametros
     * @return Array
     * @throws \Exception
     */
    public function asignarRecursosRedL2MPLS($arrayParametros)
    {
        $objServicio    = $arrayParametros['objServicio'];
        $strUsrCreacion = $arrayParametros['strUsrCreacion'];
        $strIpCreacion  = $arrayParametros['strIpCreacion'];
        $intIdEmpresa   = $arrayParametros['intIdEmpresa'];
        $objSolicitud   = $arrayParametros['objSolicitud'];
        $strStatus      = 'OK';
        $strMensaje     = 'OK';
        $strIpLoopBack  = '';
        $strIpLoopBackPe= '';
        $strSubred      = '';
        $intIdInterface = 0;
        $arrayParametrosWs  = array();

        try
        {
            $objServicio->setEstado('Asignada');
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();
            
            $objServicioTecnico = $this->emComercial->getRepository("schemaBundle:InfoServicioTecnico")->findOneByServicioId($objServicio->getId());
            
            if(is_object($objServicioTecnico))
            {
                $intIdInterface = $objServicioTecnico->getInterfaceElementoId();
            }
            
            //Asignacion de una Ip de loopback
            
            $arrayPrefijos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->get( 'SUBREDES RECURSOS DE RED PARA L2MPLS', 
                                                    'PLANIFICACION', 
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    '', 
                                                    '', 
                                                    $intIdEmpresa);
            $boolConfCpe     = false;
            $boolConfPe      = false;
            $arrayValidacion = array();
            
            if(!empty($arrayPrefijos))
            {                
                //arrayPrefijos
                //CPE
                //valor1 -> 10.107.4.X
                //valor2 -> 10.107.4.X1
                //PE
                //valor3 -> 10.107.4.X2
                //valor4 -> 10.107.4.X3
                //SUBRED GENERAL
                //valor5 -> 10.107.4.0/24
                foreach($arrayPrefijos as $array)
                {
                    $strSubred = $array['valor5'];//Subred
                    
                    if(!$boolConfCpe)
                    {
                        $objSubred = $this->emInfraestructura->getRepository("schemaBundle:InfoSubred")->findOneBySubred($strSubred);

                        if(is_object($objSubred))
                        {
                            //Obtener la Ip de loopback para configurar dentro del CPE
                            $strIpLoopBack = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                     ->getIpDisponibleBySubred($objSubred->getId());
                        }
                        else
                        {
                            return array('status'  => 'ERROR',
                                         'mensaje' => 'No existe subred asignada para configuraciones de LoopBack, notificar a Sistemas');
                        }

                        //Validar que aun existan IPs disponibles dentro del rango asignado
                        $arrayOctetos        = explode(".",$strIpLoopBack);
                        $arrayOctetosLimites = explode(".",$array['valor2']);

                        //Si llegamos a la ultima Ip y esta ya no se encuentra dentro del rango establecido, se alerta al usuario
                        if($arrayOctetos[3] > $arrayOctetosLimites[3])
                        {
                            $arrayValidacion = array('status' => 'ERROR', 'mensaje' => 'No existe Ip de LoopBack disponible en asignación '
                                                                                     . 'Automática para configurar en CPE, notificar a Sistemas');
                        }                    
                        else
                        {
                            $boolConfCpe = true;
                        }
                    }
                    
                    //Verificar que el Pe del Extremo ya no tenga configurada una Loopback
                    $strIpLoopBackPe = '';
                    
                    if(!$boolConfPe)
                    {
                        $objServicioTecnicoExtremo = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                          ->getDataTecnicaExtremoPorInterfazL2($objServicioTecnico->getInterfaceElementoId());
                
                        if(is_object($objServicioTecnicoExtremo))
                        {
                            $arrayParametrosWs["intIdElemento"] = $objServicioTecnicoExtremo->getElementoId();
                            $arrayParametrosWs["intIdServicio"] = $objServicio->getId();

                            //Obtener el Pe relacionado al enlace L2 por levantar
                            $objElementoPeExtremoL2 = $this->servicioGeneral->getPeBySwitch($arrayParametrosWs);

                            if(is_object($objElementoPeExtremoL2))
                            {
                                $objDetalle =   $this->emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                                        ->findOneBy(array('elementoId'    => $objElementoPeExtremoL2->getId(),
                                                                                          'detalleNombre' => 'IP LOOPBACK L2',
                                                                                          'estado'        => 'Activo'));
                                if(is_object($objDetalle))
                                {
                                    $strIpLoopBackPe = $objDetalle->getDetalleValor();
                                    $boolConfPe      = true;
                                }
                            }
                        }
                        else
                        {
                            $arrayValidacion = array('status'  => 'ERROR', 
                                                     'mensaje' => 'Extremo L3mpls no se encuentra Activo, por favor culminar activación del mismo');
                            return $arrayValidacion;
                        }
                        
                        //Si no existe, se asignara una nueva IP para ser configurada en el Pe
                        if(empty($strIpLoopBackPe))
                        {
                            //Asignacion
                            $strIpPeInicio = $array['valor3'];
                            $strIpPeFin    = $array['valor4'];
                            
                            $arrayIpPeInicio = explode(".",$strIpPeInicio);
                            $arrayIpPeFin    = explode(".",$strIpPeFin);
                            
                            $strIpConfPe     = $arrayIpPeInicio[0].".".$arrayIpPeInicio[1].".".$arrayIpPeInicio[2];
                            
                            //recorremos el rango asignado
                            for($inti = intval($arrayIpPeInicio[3]); $inti <= intval($arrayIpPeFin[3]) ; $inti ++)
                            {
                                $strIpLoopBackPe = $strIpConfPe.".".$inti;
                                
                                //Verificamos que la Ip ya no este configurado
                                $objInfoIp = $this->emInfraestructura->getRepository("schemaBundle:InfoIp")
                                                                     ->findOneBy(array('ip'     =>  $strIpLoopBackPe,
                                                                                       'estado' => 'Activo'));
                                if(is_object($objInfoIp))
                                {
                                    continue;
                                }
                                else
                                {
                                    if($inti > $arrayIpPeFin[3])
                                    {
                                        $arrayValidacion = array('status'  => 'ERROR', 
                                                                 'mensaje' => 'No existe Ip de LoopBack disponible en asignación '
                                                                            . 'Automática para configurar en <b>Pe</b>, notificar a Sistemas');
                                        $boolConfPe = false;
                                        break;
                                    }
                                    else
                                    {
                                        $this->servicioGeneral->ingresarDetalleElemento($objElementoPeExtremoL2, 
                                                                                        "IP LOOPBACK L2", 
                                                                                        "IP LOOPBACK L2", 
                                                                                        $strIpLoopBackPe, 
                                                                                        $arrayParametros['strUsrCreacion'], 
                                                                                        $arrayParametros['strIpCreacion']
                                                                                       );
            
                                        $boolConfPe = true;
                                        break;
                                    }
                                }
                            }
                                                                               
                            if($boolConfPe)
                            {
                                $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                                $objServicio->getProductoId(), 
                                                                                                "LOOPBACK_L2", 
                                                                                                $strIpLoopBackPe, 
                                                                                                $strUsrCreacion
                                                                                              );  
                            }
                        }
                        else
                        {
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                            $objServicio->getProductoId(), 
                                                                                            "LOOPBACK_L2", 
                                                                                            $strIpLoopBackPe, 
                                                                                            $strUsrCreacion
                                                                                          );
                        }
                    }
                }//endforeach subredes asignadas
                    
                if(!$boolConfPe)
                {
                    return $arrayValidacion;
                }
                
                if(!$boolConfCpe)
                {
                    return $arrayValidacion;
                }
            }
            else
            {
                return array('status' => 'ERROR', 'mensaje' => 'No existe información de Subredes para asignación Automática de IP de Loopback,'
                                                             . ' notificar a Sistemas');
            }
            
            //Se agrega la Ip al Servicio ( loopback cpe )
            $objInfoIp = new InfoIp();
            $objInfoIp->setIp($strIpLoopBack);
            $objInfoIp->setSubredId($objSubred->getId());
            $objInfoIp->setServicioId($objServicio->getId());
            $objInfoIp->setMascara($objSubred->getMascara());
            $objInfoIp->setFeCreacion(new \DateTime('now'));
            $objInfoIp->setUsrCreacion($strUsrCreacion);
            $objInfoIp->setIpCreacion($strIpCreacion);
            $objInfoIp->setEstado("Activo");
            $objInfoIp->setTipoIp("WAN");
            $objInfoIp->setVersionIp("IPV4");
            $this->emInfraestructura->persist($objInfoIp);
            $this->emInfraestructura->flush();
            
            //Asignacion de un virtual connect que no exista en ningun otro cliente
            do
            {
                $boolContinua      = true;
                $arrayVConnect     = $this->emComercial->getRepository("schemaBundle:InfoServicio")->getSecuenciaVirtualConnect();
            
                $intVirtualConnect = $arrayVConnect['secuencia'];
                
                //Se verifica que el VC no exista en otro servicio dentro de la misma Region, en ese caso vuelve a generar una VC nueva y se
                //valida nuevamente
                $arrayCont = $this->emComercial->getRepository("schemaBundle:InfoServicio")->getContVirtualConnect($intVirtualConnect);
                
                if($arrayCont['cont'] == 0)
                {
                    //Se verifica los VC parametrizados como usados ( data anterior )
                    $arrayVCs = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('VIRTUAL CONNECT UTILIZADAS', 
                                                      'PLANIFICACION', 
                                                      '',
                                                      $intVirtualConnect,
                                                      '', 
                                                      '',
                                                      '',
                                                      '', 
                                                      '', 
                                                      $intIdEmpresa);
                    
                    //Si existe dentro de los parametros significa que ya esta configurado
                    if(!empty($arrayVCs))
                    {
                        $boolContinua = false;
                    }
                }
                else
                {
                    $boolContinua = false;
                }
                
                if($boolContinua)
                {
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                    $objServicio->getProductoId(), 
                                                                                    "VIRTUAL_CONNECT", 
                                                                                    $intVirtualConnect, 
                                                                                    $strUsrCreacion);
                    break;
                }
            }
            while(!$boolContinua);
            
            $strObservacion = 'Se generó asignación de Recursos de Red automática con los siguientes Datos:'
                . '<br/><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;<b>Ip LoopBack Cpe:</b>&nbsp;'.$strIpLoopBack
                . '<br/><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;<b>Ip LoopBack Pe:</b>&nbsp;'.$strIpLoopBackPe
                . '<br/><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;<b>VirtualConnect:</b>&nbsp;'.$intVirtualConnect;
            
            $objInfoHistorial = new InfoServicioHistorial();
            $objInfoHistorial->setServicioId($objServicio);
            $objInfoHistorial->setEstado($objServicio->getEstado());
            $objInfoHistorial->setUsrCreacion($strUsrCreacion);
            $objInfoHistorial->setFeCreacion(new \DateTime('now'));
            $objInfoHistorial->setIpCreacion($strIpCreacion);
            $objInfoHistorial->setObservacion($strObservacion);
            $this->emComercial->persist($objInfoHistorial);
            $this->emComercial->flush();
            
            if(is_object($objSolicitud))
            {
                //se actualiza estado a la solicitud
                $objSolicitud->setEstado($objServicio->getEstado());
                $this->emComercial->persist($objSolicitud);
                $this->emComercial->flush();

                //agregar historial a la solicitud
                $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
                $objDetalleSolicitudHistorial->setDetalleSolicitudId($objSolicitud);
                $objDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
                $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitudHistorial->setUsrCreacion($strUsrCreacion);
                $objDetalleSolicitudHistorial->setEstado($objServicio->getEstado());
                $objDetalleSolicitudHistorial->setObservacion($strObservacion);
                $this->emComercial->persist($objDetalleSolicitudHistorial);
                $this->emComercial->flush();
            }
            else
            {
                throw new \Exception("OBSERVACION : No Existe Solicitud de Planificación, favor comunicarse con Sistemas!");
            }
            
            $this->servicioGeneral->generarLoginAuxiliar($objServicio->getId());
            
            $intIdOficina = $objServicio->getPuntoId()->getPuntoCoberturaId()->getOficinaId();
                                        
            $objOficina   = $this->emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficina);
            
            if(is_object($objOficina))
            {
                $objCanton = $this->emComercial->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());
            }
            
            //Notificar a Ipccl2 para que configure los CPEs
            $arrayParametrosEnvioPlantilla                      = array();
            $arrayParametrosEnvioPlantilla['strObservacion']    = 'Tarea Automática: Se solicita realizar Configuración en CPE para Activación de '
                                                                . 'Servicio L2, información disponible en la pantalla técnica del Login. '.
                                                                  '<br><b>Login : </b> '.$objServicio->getPuntoId()->getLogin().'<br>';
            $arrayParametrosEnvioPlantilla['strUsrCreacion']    = $strUsrCreacion;
            $arrayParametrosEnvioPlantilla['strIpCreacion']     = $strIpCreacion;
            $arrayParametrosEnvioPlantilla['intDetalleSolId']   = $objSolicitud->getId();
            $arrayParametrosEnvioPlantilla['strTipoAfectado']   = 'Cliente';
            $arrayParametrosEnvioPlantilla['objPunto']          = $objServicio->getPuntoId();
            $arrayParametrosEnvioPlantilla['strCantonId']       = is_object($objCanton)?$objCanton->getId():0;
            $arrayParametrosEnvioPlantilla['strEmpresaCod']     = $intIdEmpresa;
            $arrayParametrosEnvioPlantilla['strPrefijoEmpresa'] = $arrayParametros['strPrefijo'];
            
            $strNombreCanton  = $this->servicioGeneral->getCiudadRelacionadaPorRegion($objServicio,$intIdEmpresa);

            $arrayInfoEnvio   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('TAREAS POR DEPARTAMENTO PARA L2MPLS', 
                                                      'SOPORTE', 
                                                      '',
                                                      'INSTALACION L2',
                                                      $strNombreCanton,//GUAYAQUIL/UIO 
                                                      '',
                                                      '',
                                                      '', 
                                                      '', 
                                                      $intIdEmpresa);
            foreach($arrayInfoEnvio as $array)                    
            {
                $objTarea  = $this->emSoporte->getRepository("schemaBundle:AdmiTarea")->findOneByNombreTarea($array['valor3']);

                $arrayParametrosEnvioPlantilla['intTarea']     = is_object($objTarea)?$objTarea->getId():'';
                $arrayParametrosEnvioPlantilla['arrayCorreos'] = array($array['valor2']);

                $objDepartamento = $this->emSoporte->getRepository("schemaBundle:AdmiDepartamento")
                                                   ->findOneByNombreDepartamento($array['valor4']);

                $arrayParametrosEnvioPlantilla['objDepartamento']   = $objDepartamento;
                $arrayParametrosEnvioPlantilla["strBanderaTraslado"] = "";
                $strNumeroTarea = $this->serviceCambioPlan->crearTareaRetiroEquipoPorDemo($arrayParametrosEnvioPlantilla);
            }
        } 
        catch (\Exception $ex) 
        {
            $strStatus         = "ERROR";
            $strMensaje        = "Error al Asignar Recursos de Red para Servicio de L2MPLS, notificar a Sistemas";
            
            if(strpos($ex->getMessage(),'OBSERVACION')!==false)
            {
                $strMensaje = $ex->getMessage();
            }
            
            $this->utilServicio->insertError('Telcos+', 
                                             'asignarRecursosRedInternetDC', 
                                             $ex->getMessage(), 
                                             $strUsrCreacion, 
                                             $strIpCreacion
                                            );
        }
        
        $arrayResultado = array('status' => $strStatus , 'mensaje' => $strMensaje);
        
        return $arrayResultado;
    }
    /**
     * Funcion que sirve para grabar los recursos de Red para el producto tunel ip
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 18-12-2015
     * @param array $arrayParametros [idServicio, hiloDisponible, vlan, ipTunel, mascaraTunel, empresaCod]
     * @return array $respuestaFinal
     */
    public function asignarRecursosRedTunelIp($arrayParametros)
    {
        $intIdServicio                  = $arrayParametros['idServicio'];
        $intIdDetalleSolicitud          = $arrayParametros['idDetalleSolicitud'];
        $intIdInterfaceElementoConector = $arrayParametros['hiloDisponible'];
        $intVlan                        = $arrayParametros['vlan'];
        $strIpTunel                     = $arrayParametros['ipTunel'];
        $strMascaraTunel                = $arrayParametros['mascaraTunel'];
        $strEmpresaCod                  = $arrayParametros['empresaCod'];
        $strUsrCreacion                 = $arrayParametros['usrCreacion'];
        $strIpCreacion                  = $arrayParametros['ipCreacion'];
        
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        
        try
        {
            $objServicio                    = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            $objServicioTecnico             = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                ->findOneBy(array( "servicioId" => $objServicio->getId()));
            $objInterfaceElementoConector   = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                      ->find($objServicioTecnico->getInterfaceElementoConectorId());
            $objDetalleSolicitud            = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                       ->findOneById($intIdDetalleSolicitud);
            $objProducto                    = $objServicio->getProductoId();                        
            
            //verificar si el puerto reservado es el mismo que el seleccionado
            if(is_numeric($intIdInterfaceElementoConector))
            {
                //verificar si el puerto reservado es el mismo que el seleccionado
                if($objInterfaceElementoConector->getId() == $intIdInterfaceElementoConector)
                {
                    //se conecta el puerto
                    $objInterfaceElementoConector->setEstado("connected");
                    $this->emInfraestructura->persist($objInterfaceElementoConector);
                    $this->emInfraestructura->flush();
                }
                else
                {
                    $objInterfaceElementoConectorNuevo  = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                      ->find($intIdInterfaceElementoConector);

                    //desconectar viejo puerto
                    $objInterfaceElementoConector->setEstado("not connect");
                    $this->emInfraestructura->persist($objInterfaceElementoConector);
                    $this->emInfraestructura->flush();

                    //conectar nuevo puerto
                    $objInterfaceElementoConectorNuevo->setEstado("connected");
                    $this->emInfraestructura->persist($objInterfaceElementoConectorNuevo);
                    $this->emInfraestructura->flush();
                }
            }
            else
            {
                //se conecta el puerto
                $objInterfaceElementoConector->setEstado("connected");
                $this->emInfraestructura->persist($objInterfaceElementoConector);
                $this->emInfraestructura->flush();
            }
            
            //se graba ip y mascara del tunel
            $objInfoIp = new InfoIp();
            $objInfoIp->setIp($strIpTunel);
            $objInfoIp->setServicioId($objServicio->getId());
            $objInfoIp->setMascara($strMascaraTunel);
            $objInfoIp->setEstado("Activo");
            $objInfoIp->setTipoIp("WAN");
            $objInfoIp->setVersionIp("IPV4");
            $this->emInfraestructura->persist($objInfoIp);
            $this->emInfraestructura->flush();
            
            //se graba caracteristica vlan
            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "VLAN", $intVlan, $strUsrCreacion);
            
            //se actualiza el estado del servicio
            $objServicio->setEstado("Asignada");
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();
            
            //agregar historial del servicio
            $objInfoHistorial = new InfoServicioHistorial();
            $objInfoHistorial->setServicioId($objServicio);
            $objInfoHistorial->setEstado("Asignada");
            $objInfoHistorial->setUsrCreacion($strUsrCreacion);
            $objInfoHistorial->setFeCreacion(new \DateTime('now'));
            $objInfoHistorial->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objInfoHistorial);
            $this->emComercial->flush();
            
            if($objDetalleSolicitud)
            {
                //se actualiza estado a la solicitud
                $objDetalleSolicitud->setEstado("Asignada");
                $this->emComercial->persist($objDetalleSolicitud);
                $this->emComercial->flush();

                //agregar historial a la solicitud
                $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
                $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
                $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitudHistorial->setUsrCreacion($strUsrCreacion);
                $objDetalleSolicitudHistorial->setEstado("Asignada");
                $this->emComercial->persist($objDetalleSolicitudHistorial);
                $this->emComercial->flush();
            }
            else
            {
                throw new \Exception("No Existe Solicitud de Planificación, favor comunicarse con Sistemas!");
            }
                        
            $status         = "OK";
            $mensaje        = "OK";
        }
        catch (\Exception $e)
        {
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
          
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            $status         = "ERROR";
            $mensaje        = "ERROR EN LA LOGICA DE NEGOCIO: <br> ".$e->getMessage();
        }
        
        //*DECLARACION DE COMMITS*/
        if ($this->emInfraestructura->getConnection()->isTransactionActive())
        {
            $this->emInfraestructura->getConnection()->commit();
        }

        if ($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }
            
        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        //*----------------------------------------------------------------------*/
        
        //*RESPUESTA-------------------------------------------------------------*/
        $respuestaFinal = array('status'=>$status, 'mensaje'=>$mensaje);
        return $respuestaFinal;
        //*----------------------------------------------------------------------*/
    }
    
    /**
     * Método para asignar una ip a un producto que lo requiera, antes de la asignación de recursos de red.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 24-09-2019
     *
     * @param array $arrayParametros
     * @return string
     */
    public function asignarIpFWA($arrayParametros)
    {
        $objServicio    = $arrayParametros['objServicio'];
        $strUsrCreacion = $arrayParametros['strUsrCreacion'];
        $strIpCreacion  = $arrayParametros['strIpCreacion'];
        $strNombreParam = $arrayParametros['strNombreParametro'];
        $strValor1      = $arrayParametros['strValor1'];
        $strStatus      = 'OK';
        $arrayData      = array();
        try
        {
            $strMensaje  = "Se asigna la ip LoopBack";
            //Asignación de una Ip de loopback

            if(is_object($objServicio->getProductoId()))
            {
                $arrayPrefijos   =  $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get($strNombreParam,
                                                          'PLANIFICACION',
                                                          '',
                                                          '',
                                                          $strValor1,
                                                          '',
                                                          '',
                                                          '',
                                                          '',
                                                          '10');

               if(!empty($arrayPrefijos))
               {
                   foreach($arrayPrefijos as $arrayRedFwa)
                   {
                       $strSubred = $arrayRedFwa['valor2'];//Subred
                       $objSubred = $this->emInfraestructura->getRepository("schemaBundle:InfoSubred")
                                                            ->findOneBySubred($strSubred);
                       if(is_object($objSubred))
                       {
                           //Obtener la Ip de loopback para configurar dentro del CPE
                           $strIpLoopBack = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                    ->getIpDisponibleBySubred($objSubred->getId());
                       }
                       else
                       {
                           return array('status' => 'ERROR' ,
                                        'data'   => array('strMensaje' => 'No existe subred asignada para configuraciones de LoopBack,'
                                                                          . ' notificar a Sistemas'));
                       }
                       //Validar que aun existan IPs disponibles dentro del rango asignado
                        $arrayOctetos        = explode(".",$strIpLoopBack);
                        $arrayOctetosLimites = explode(".",$arrayRedFwa['valor6']);
                        //Si llegamos a la última Ip y esta ya no se encuentra dentro del rango establecido, se alerta al usuario
                        if($arrayOctetos[3] > $arrayOctetosLimites[3])
                        {
                            return array('status' => 'ERROR',
                                         'data'   => array('strMensaje' => 'No existe Ip de LoopBack disponible en asignación '
                                                                           . 'Automática para configurar en CPE, notificar a Sistemas'));
                        }
                   }
               }
               else
               {
                    return array('status' => 'ERROR',
                                 'data'   => array('strMensaje' => 'No existe información de Subredes para asignación Automática de '
                                                                   . 'IP de Loopback, notificar a Sistemas'));
               }
               //Retorno ip generada.
               $arrayData = array('strIpLoopBack' => $strIpLoopBack,
                                  'strMensaje'    => 'OK');
            }
        }
        catch (\Exception $ex)
        {
            $strStatus         = "ERROR";
            $strMensaje        = "Error al Asignar ip LoopBack, notificar a Sistemas";

            if(strpos($ex->getMessage(),'OBSERVACION')!==false)
            {
                $strMensaje = $ex->getMessage();
            }
            $arrayData = array('strMensaje' => $strMensaje);

            $this->utilServicio->insertError('Telcos+',
                                             'asignarIpFWA',
                                             $ex->getMessage(),
                                             $strUsrCreacion,
                                             $strIpCreacion
                                            );
        }
        $arrayResultado = array('status' => $strStatus , 'data' => $arrayData);

        return $arrayResultado;
    }

    /**
     * Método que graba la ip loopback que fue generada bajo demanda.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 24-09-2019
     *
     * @param array $arrayParametros[
     *                                  'objServicio':      Object Servicio:    Objeto del servicio a instalar,
     *                                  'strIpLoopback':    String:             Ip de loopback generado.
     *                                  'strUsrCreacion':   String:             Usuario de creación.
     *                                  'strIpCreacion':    String:             Ip Session.
     *                              ]
     * @return array $arrayRespuesta
     */
    public function grabarIpFWA($arrayParametros)
    {
        $arrayRespuesta = array();
        $arrayIpGenerada= array();
        $strUsrCreacion = $arrayParametros['strUsrCreacion'];
        $strIpCreacion  = $arrayParametros['strIpCreacion'];
        $objServicio    = $arrayParametros['objServicio'];
        $strIpLoopBack  = $arrayParametros['strIpLoopback'];
        $strNombreParam = $arrayParametros['strNombreParametro'];
        $strValor1      = $arrayParametros['strValor1'];
        $strStatus      = "OK";
        try
        {
            //Consultar si la ip ya fue aprovisionada por otro servicio.
            $objIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                             ->findBy(array(
                                                            'ip'         => $strIpLoopBack,
                                                            'servicioId' => $objServicio->getId(),
                                                            'estado'     => 'Activo'
                                                           ));
            if(is_object($objIp))
            {
                //La ip generada existe y debo volver a generar una nueva ip loopback
                $arrayIpGenerada = $this->asignarIpFWA($arrayParametros);
                if(isset($arrayIpGenerada) && $arrayIpGenerada['status'] == 'OK')
                {
                    $strIpLoopBack = $arrayLoopBack['data']['strIpLoopBack'];
                }
                else
                {
                    throw new \Exception($arrayLoopBack['data']['strMensaje']);
                }
            }
            else
            {
                $arrayPrefijos   =  $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get($strNombreParam,
                                                          'PLANIFICACION',
                                                          '',
                                                          '',
                                                          $strValor1,
                                                          '',
                                                          '',
                                                          '',
                                                          '',
                                                          '10');
                if(!empty($arrayPrefijos))
                {
                    foreach($arrayPrefijos as $arrayRedFwa)
                    {
                        $strSubred = $arrayRedFwa['valor2'];//Subred
                        //Obtengo la subRed del servicio.
                        $objSubred = $this->emInfraestructura->getRepository("schemaBundle:InfoSubred")->findOneBySubred($strSubred);
                        if(!is_object($objSubred))
                        {
                            throw new \Exception('No existe subred asignada para configuraciones de LoopBack, notificar a Sistemas');
                        }
                        //Procedo almacenar la ip de loopback del servicio.
                        $objInfoIp = new InfoIp();
                        $objInfoIp->setIp($strIpLoopBack);
                        $objInfoIp->setSubredId($objSubred->getId());
                        $objInfoIp->setServicioId($objServicio->getId());
                        $objInfoIp->setMascara($objSubred->getMascara());
                        $objInfoIp->setFeCreacion(new \DateTime('now'));
                        $objInfoIp->setUsrCreacion($strUsrCreacion);
                        $objInfoIp->setIpCreacion($strIpCreacion);
                        $objInfoIp->setEstado("Activo");
                        $objInfoIp->setTipoIp("WAN");
                        $objInfoIp->setVersionIp("IPV4");
                        $this->emInfraestructura->persist($objInfoIp);
                        $this->emInfraestructura->flush();

                        $strObservacion = 'Se generó asignación de Recursos de Red automática con los siguientes Datos:'
                        . '<br/><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;<b>Ip LoopBack:</b>&nbsp;'.$strIpLoopBack;
                        $objInfoHistorial = new InfoServicioHistorial();
                        $objInfoHistorial->setServicioId($objServicio);
                        $objInfoHistorial->setEstado($objServicio->getEstado());
                        $objInfoHistorial->setUsrCreacion($strUsrCreacion);
                        $objInfoHistorial->setFeCreacion(new \DateTime('now'));
                        $objInfoHistorial->setIpCreacion($strIpCreacion);
                        $objInfoHistorial->setObservacion($strObservacion);
                        $this->emComercial->persist($objInfoHistorial);
                        $this->emComercial->flush();

                        $arrayRespuesta = array('status'  => $strStatus,
                                                'data'    => array('mensaje'    => 'Se crea la ip de loopback del servicio',
                                                                   'ipLoopBack' => $strIpLoopBack,
                                                                   'objSubred'  => $objSubred));
                    }
                }
            }
        }
        catch (\Exception $e)
        {
            $strStatus      = "ERROR";
            $this->utilServicio->insertError('Telcos+',
                                             'grabarIpFWA',
                                             $e->getMessage(),
                                             $strUsrCreacion,
                                             $strIpCreacion
                                            );
            $arrayRespuesta = array('status'  => $strStatus,
                                    'mensaje' => $e->getMessage());
        }
    return $arrayRespuesta;
    }

    /**
     * Funcion que sirve para grabar los recursos de Red del producto wifi
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 18-12-2015
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 12-10-2016 Se agregó para que se genere el login aux
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 04-09-2020 | Se agrega método para finalizar tarea de factibilidad.
     *
     * @param array $arrayParametros [idServicio, hiloDisponible, vlan, ipTunel, mascaraTunel, empresaCod]
     * @return array $respuestaFinal
     */
    public function asignarRecursosWifi($arrayParametros)
    {
        $intIdServicio                  = $arrayParametros['idServicio'];
        $intIdDetalleSolicitud          = $arrayParametros['idDetalleSolicitud'];
        $intInterfaceConectorId         = $arrayParametros['interfaceConectorId'];
        $strEmpresaCod                  = $arrayParametros['empresaCod'];
        $strUsrCreacion                 = $arrayParametros['usrCreacion'];
        $strIpCreacion                  = $arrayParametros['ipCreacion'];

        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/

        try
        {
            $objServicio                    = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            $objServicioTecnico             = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                ->findOneBy(array( "servicioId" => $objServicio->getId()));
            $objInterfaceElementoConector   = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                      ->find($objServicioTecnico->getInterfaceElementoConectorId());
            $objDetalleSolicitud            = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                       ->findOneById($intIdDetalleSolicitud);


            /*Se envía a finalizar la tarea.*/
            $this->serviceInfoServicioTecnico->establecerEstadoSolicitud(
                array(
                    'idServicio' => $intIdServicio,
                    "opcion" => "128_Fact_Fin"
                ));

            
            //verificar si el puerto reservado es el mismo que el seleccionado
            if(is_numeric($intInterfaceConectorId))
            {
                //verificar si el puerto reservado es el mismo que el seleccionado
                if($objInterfaceElementoConector->getId() == $intInterfaceConectorId)
                {
                    //se conecta el puerto
                    $objInterfaceElementoConector->setEstado("connected");
                    $this->emInfraestructura->persist($objInterfaceElementoConector);
                    $this->emInfraestructura->flush();
                }
                else
                {
                    $objInterfaceElementoConectorNuevo  = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                      ->find($intInterfaceConectorId);

                    //desconectar viejo puerto
                    $objInterfaceElementoConector->setEstado("not connect");
                    $this->emInfraestructura->persist($objInterfaceElementoConector);
                    $this->emInfraestructura->flush();

                    //conectar nuevo puerto
                    $objInterfaceElementoConectorNuevo->setEstado("connected");
                    $this->emInfraestructura->persist($objInterfaceElementoConectorNuevo);
                    $this->emInfraestructura->flush();
                }
            }
            else
            {
                //se conecta el puerto
                $objInterfaceElementoConector->setEstado("connected");
                $this->emInfraestructura->persist($objInterfaceElementoConector);
                $this->emInfraestructura->flush();
            }
                       
            //se actualiza el estado del servicio
            $objServicio->setEstado("Asignada");
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();
            
            //agregar historial del servicio
            $objInfoHistorial = new InfoServicioHistorial();
            $objInfoHistorial->setServicioId($objServicio);
            $objInfoHistorial->setEstado("Asignada");
            $objInfoHistorial->setUsrCreacion($strUsrCreacion);
            $objInfoHistorial->setFeCreacion(new \DateTime('now'));
            $objInfoHistorial->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objInfoHistorial);
            $this->emComercial->flush();
            
            if($objDetalleSolicitud)
            {
                //se actualiza estado a la solicitud
                $objDetalleSolicitud->setEstado("Asignada");
                $this->emComercial->persist($objDetalleSolicitud);
                $this->emComercial->flush();

                //agregar historial a la solicitud
                $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
                $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
                $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitudHistorial->setUsrCreacion($strUsrCreacion);
                $objDetalleSolicitudHistorial->setEstado("Asignada");
                $this->emComercial->persist($objDetalleSolicitudHistorial);
                $this->emComercial->flush();
            }
            else
            {
                throw new \Exception("No Existe Solicitud de Planificación, favor comunicarse con Sistemas!");
            }
            
            //Generacion de Login Auxiliar al Servicio            
            $this->servicioGeneral->generarLoginAuxiliar($intIdServicio);
                        
            $status         = "OK";
            $mensaje        = "OK";
        }
        catch (\Exception $e)
        {
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
          
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            $status         = "ERROR";
            $mensaje        = "ERROR EN LA LOGICA DE NEGOCIO: <br> ".$e->getMessage();
        }
        
        //*DECLARACION DE COMMITS*/
        if ($this->emInfraestructura->getConnection()->isTransactionActive())
        {
            $this->emInfraestructura->getConnection()->commit();
        }

        if ($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }
            
        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        //*----------------------------------------------------------------------*/
        
        //*RESPUESTA-------------------------------------------------------------*/
        $respuestaFinal = array('status'=>$status, 'mensaje'=>$mensaje);
        return $respuestaFinal;
        //*----------------------------------------------------------------------*/
    }
        
    /**
     * obtenerInformacionPromocionesBw
     * 
     * Función que sirve para obtener la información de promociones de BW necesarias para su configuración
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 22-08-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 11-05-2020 Se unifica las validaciones por marca y no por modelo de olt
     * 
     * @param array $arrayParametros [
     *                                intIdServicio  => Identificador del servicio
     *                                intEmpresaCod  => id de la empresa en sesión
     *                                intIdPlan      => id del plan a procesar y recuperar información
     *                                strUsrCreacion => usuario de creación
     *                                strIpCreacion  => ip del cliente
     *                               ]
     */
    public function obtenerInformacionPromocionesBw($arrayParametros)
    {
        $intIdServicio  = ( isset($arrayParametros['intIdServicio']) && !empty($arrayParametros['intIdServicio']) )
                          ? $arrayParametros['intIdServicio'] : null;
        $intEmpresaCod  = ( isset($arrayParametros['intEmpresaCod']) && !empty($arrayParametros['intEmpresaCod']) )
                          ? $arrayParametros['intEmpresaCod'] : null;
        $intIdPlan      = ( isset($arrayParametros['intIdPlan']) && !empty($arrayParametros['intIdPlan']) )
                          ? $arrayParametros['intIdPlan'] : null;
        $strIpCreacion  = ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) )
                          ? $arrayParametros['strIpCreacion'] : null;
        $strUsrCreacion = ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) )
                          ? $arrayParametros['strUsrCreacion'] : null;
        $strStatus    = "ERROR";
        $strMensaje   = "ERROR";
        $strSerieOnt  = "";
        $strMacOnt    = "";
        $strNombreOlt = "";
        $strIpOlt     = "";
        $strPuertoOlt = "";
        $strModeloOlt = "";
        $strMarcaOlt  = "";
        $strOntId     = "";
        $strSpid      = "";
        $strServiceProfile    = "";
        $strEstadoServicio    = "";
        $strTipoNegocioActual = "";
        $strTipoNegocioNuevo  = "";
        $strIpServicio        = "";
        $strScope             = "";
        $intIpFijasActivas    = 0;
        $strMacWifi       = "";
        $strVlan          = "";
        $strGemPort       = "";
        $strLineProfile   = "";
        $strTrafficTable  = "";
        $strCapacidadUp   = "";
        $strCapacidadDown = "";
        $strVlanPromo          = "";
        $strGemPortPromo       = "";
        $strLineProfilePromo   = "";
        $strTrafficTablePromo  = "";
        $strCapacidadUpPromo   = "";
        $strCapacidadDownPromo = "";
        $intKey                = 0;
        $arrayRespuesta        = array();
        $arrayValorCaracteristica     = array();
        $arrayProductoCaracteristicas = array();
        try
        {
            //VALIDAR PARÁMETROS DE ENTRADA
            if(!empty($intIdServicio) && !empty($intIdPlan) &&
               !empty($strIpCreacion) && !empty($strUsrCreacion) &&
               !empty($intEmpresaCod))
            {
                //OBTIENE INFORMACIÓN DEL PRODUCTO INTERNET DEDICADO
                $objProductoInternet = $this->emComercial
                                            ->getRepository('schemaBundle:AdmiProducto')
                                            ->findOneBy(array("descripcionProducto" => "INTERNET DEDICADO", 
                                                             "estado"              => "Activo",
                                                             "empresaCod"          => $intEmpresaCod));
                if(!is_object($objProductoInternet))
                {
                    throw new \Exception("No se logró recuperar información del producto internet protegido.");
                }
                
                //OBTIENE PLAN PROMOCIONAL
                $objPlanPromocional = $this->emComercial
                                           ->getRepository('schemaBundle:InfoPlanCab')
                                           ->find($intIdPlan);
                if(!is_object($objPlanPromocional))
                {
                    throw new \Exception("No se logró recuperar información del plan promocional del servicio a procesar.");
                }
                
                //OBTIENE PERFIL DEL PLAN PROMOCIONAL
                $strPerfil = $this->emComercial
                                  ->getRepository("schemaBundle:InfoPlanCab")
                                  ->getPerfilByPlanIdAndPuntoId("si",$intIdPlan,"","NO");
                if(is_string($strPerfil) && strpos($strPerfil, 'Error') !== false)
                {
                    throw new \Exception($strPerfil);
                }

                //OBTIENE INFORMACIÓN TÉCNICA DEL SERVICIO
                $objServicioTecnico = $this->emComercial
                                           ->getRepository('schemaBundle:InfoServicioTecnico')
                                           ->findOneBy(array( "servicioId" => $intIdServicio));
                if (!is_object($objServicioTecnico))
                {
                    throw new \Exception("No se logró recuperar información técnica del servicio a procesar.");
                }

                $objServicio = $objServicioTecnico->getServicioId();
                
                //OBTENER NOMBRE CLIENTE
                $objPersona       = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
                $strNombreCliente = ($objPersona->getRazonSocial() != "") ? $objPersona->getRazonSocial() : 
                                    $objPersona->getNombres()." ".$objPersona->getApellidos();

                //OBTENER IDENTIFICACIÓN
                $strIdentificacion = $objPersona->getIdentificacionCliente();

                //OBTENER LOGIN
                $strLogin = $objServicio->getPuntoId()->getLogin();
                
                //OBTIENE MARCA DE OLT
                $objElementoOlt = $this->emInfraestructura
                                       ->getRepository('schemaBundle:InfoElemento')
                                       ->find($objServicioTecnico->getElementoId());
                $objModeloElemento = $objElementoOlt->getModeloElementoId();
                $strMarcaOlt       = $objModeloElemento->getMarcaElementoId()->getNombreMarcaElemento();
                
                $objInterfaceElemento = $this->emInfraestructura
                                             ->getRepository("schemaBundle:InfoInterfaceElemento")
                                             ->find($objServicioTecnico->getInterfaceElementoId());
                
                //OBTIENE INFORMACIÓN DEL SERVICIO DEL CLIENTE
                $objElementoCliente = $this->emInfraestructura
                                           ->getRepository('schemaBundle:InfoElemento')
                                           ->find($objServicioTecnico->getElementoClienteId());
                $strSerieOnt  = $objElementoCliente->getSerieFisica();
                $objSpcMacOnt = $this->servicioGeneral
                                     ->getServicioProductoCaracteristica($objServicio,
                                                                         "MAC ONT",
                                                                         $objProductoInternet);
                if(is_object($objSpcMacOnt))
                {
                    $strMacOnt = $objSpcMacOnt->getValor();
                }
                $strNombreOlt  = $objElementoOlt->getNombreElemento();
                $objIpElemento = $this->emInfraestructura
                                      ->getRepository('schemaBundle:InfoIp')
                                      ->findOneBy(array("elementoId" => $objElementoOlt->getId(),
                                                        "estado"     => "Activo"));
                $strIpOlt     = $objIpElemento->getIp();
                $strPuertoOlt = $objInterfaceElemento->getNombreInterfaceElemento();
                $strModeloOlt = $objModeloElemento->getNombreModeloElemento();

                $strEstadoServicio    = $objServicio->getEstado();
                $strTipoNegocioActual = $objPlanPromocional->getTipo();
                $arrayProdIp = $this->emComercial
                                    ->getRepository('schemaBundle:AdmiProducto')
                                    ->findBy(array("nombreTecnico" => "IP", 
                                                   "empresaCod"    => $intEmpresaCod, 
                                                   "estado"        => "Activo"));
                $arrayPlanDet = $this->emComercial
                                     ->getRepository('schemaBundle:InfoPlanDet')
                                     ->findBy(array("planId" => $objPlanPromocional->getId()));

                $intIndiceProductoIp = $this->servicioGeneral
                                            ->obtenerIndiceInternetEnPlanDet($arrayPlanDet, $arrayProdIp);
                if ($intIndiceProductoIp!=-1)
                {
                    $objProductoIp = $this->emComercial
                                          ->getRepository('schemaBundle:AdmiProducto')
                                          ->find($arrayPlanDet[$intIndiceProductoIp]->getProductoId());
                }
                else
                {
                    $objProductoIp = null;
                }
                if (is_object($objProductoIp))
                {
                    $objIpServicio = $this->emInfraestructura
                                          ->getRepository('schemaBundle:InfoIp')
                                          ->findOneBy(array("servicioId" => $objServicio->getId(),
                                                            "estado"     => "Activo"));
                    if(is_object($objIpServicio))
                    {
                      $strIpServicio = $objIpServicio->getIp();
                    }
                    $objSpcScope = $this->servicioGeneral
                                        ->getServicioProductoCaracteristica($objServicio, "SCOPE", $objProductoIp);
                    if(is_object($objSpcScope))
                    {
                        $strScope = $objSpcScope->getValor();
                    }
                }

                $strTipoNegocioNuevo = $objPlanPromocional->getTipo();
                
                //OBTIENE CARACTERÍSTICAS PROMOCIONALES
                $arrayParametrosFuncion           = array();
                $arrayParametrosFuncion['marca']  = $strMarcaOlt;
                $arrayParametrosFuncion['idPlan'] = $intIdPlan;
                $arrayParametrosFuncion['empresaCod']  = $intEmpresaCod;
                $arrayParametrosFuncion['valorPerfil'] = $strPerfil;
                $arrayParametrosFuncion['tipoNegocio'] = $objPlanPromocional->getTipo();
                $arrayParametrosFuncion['tipoEjecucion'] = 'FLUJO';
                $arrayParametrosFuncion['elementoOltId'] = $objElementoOlt->getId();
                $arrayParametrosFuncion['tipoAprovisionamiento'] = "CNR";
                $strPerfilEquivalente = $this->emInfraestructura
                                             ->getRepository('schemaBundle:InfoServicioTecnico')
                                             ->getValorPerfilEquivalente($arrayParametrosFuncion);
                $strLineProfilePromo = $strPerfilEquivalente;
                $objSpcIndiceCliente = $this->servicioGeneral
                                            ->getServicioProductoCaracteristica($objServicio,
                                                                                "INDICE CLIENTE",
                                                                                $objProductoInternet);
                if(is_object($objSpcIndiceCliente))
                {
                    $strOntId = $objSpcIndiceCliente->getValor();
                }

                //OBTIENE VARIABLES POR TECNOLOGÍA
                if($strMarcaOlt == "TELLION")
                {
                    if (!empty($strLineProfilePromo))
                    {
                        $arrayLineProfilePromo = explode("_", $strLineProfilePromo);
                        $strLineProfilePromo   = $arrayLineProfilePromo[0]."_".$arrayLineProfilePromo[1];
                    }
                    $objSpcMacWifi = $this->servicioGeneral
                                          ->getServicioProductoCaracteristica($objServicio,
                                                                              "MAC WIFI",
                                                                              $objProductoInternet);
                    if(is_object($objSpcMacWifi))
                    {
                        $strMacWifi = $objSpcMacWifi->getValor();
                    }

                    //OBTENER PERFIL
                    $objSpcLineProfile = $this->servicioGeneral
                                              ->getServicioProductoCaracteristica($objServicio,
                                                                                  "PERFIL",
                                                                                  $objProductoInternet);
                    if(is_object($objSpcLineProfile))
                    {
                        $strLineProfile     = $objSpcLineProfile->getValor();
                        $arrayPerfil        = explode("_", $strLineProfile);
                        $strLineProfile     = $arrayPerfil[0]."_".$arrayPerfil[1];
                        if($arrayPerfil[2] == 1)
                        {
                            $intIpFijasActivas  = 0;
                        }
                        else
                        {
                            $intIpFijasActivas  = $arrayPerfil[2];
                        }
                    }
                }//if($modeloElemento->getNombreModeloElemento()=="EP-3116")
                else if($strMarcaOlt == "HUAWEI")
                {
                    //OBTENER SERVICE-PORT
                    $objSpcSpid = $this->servicioGeneral
                                       ->getServicioProductoCaracteristica($objServicio,
                                                                           "SPID",
                                                                           $objProductoInternet);
                    if(is_object($objSpcSpid))
                    {
                        $strSpid    = $objSpcSpid->getValor();
                    }

                    //OBTENER SERVICE PROFILE
                    $objSpcServiceProfile = $this->servicioGeneral
                                                 ->getServicioProductoCaracteristica($objServicio,
                                                                                     "SERVICE-PROFILE",
                                                                                     $objProductoInternet);
                    if(is_object($objSpcServiceProfile))
                    {
                        $strServiceProfile = $objSpcServiceProfile->getValor();
                    }

                    //OBTENER LINE PROFILE NAME
                    $objSpcLineProfile = $this->servicioGeneral
                                              ->getServicioProductoCaracteristica($objServicio,
                                                                                  "LINE-PROFILE-NAME",
                                                                                  $objProductoInternet);
                    if(is_object($objSpcLineProfile))
                    {
                        $strLineProfile = $objSpcLineProfile->getValor();
                    }

                    //OBTENER VLAN
                    $objSpcVlan = $this->servicioGeneral
                                       ->getServicioProductoCaracteristica($objServicio,
                                                                           "VLAN",
                                                                           $objProductoInternet);
                    if(is_object($objSpcVlan))
                    {
                        $strVlan = $objSpcVlan->getValor();
                    }

                    //OBTENER GEM-PORT
                    $objSpcGemPort = $this->servicioGeneral
                                          ->getServicioProductoCaracteristica($objServicio,
                                                                              "GEM-PORT",
                                                                              $objProductoInternet);
                    if(is_object($objSpcGemPort))
                    {
                        $strGemPort = $objSpcGemPort->getValor();
                    }

                    //OBTENER TRAFFIC-TABLE
                    $objSpcTraffic = $this->servicioGeneral
                                          ->getServicioProductoCaracteristica($objServicio,
                                                                              "TRAFFIC-TABLE",
                                                                              $objProductoInternet);
                    if(is_object($objSpcTraffic))
                    {
                        $strTrafficTable = $objSpcTraffic->getValor();
                    }
                    
                    $arrayProdIp    = $this->emComercial
                                           ->getRepository('schemaBundle:AdmiProducto')
                                           ->findBy(array( "nombreTecnico" => 'IP', 
                                                           "empresaCod"    => $intEmpresaCod, 
                                                           "estado"        => "Activo"));
                    $arrayServicios = $this->emComercial
                                           ->getRepository('schemaBundle:InfoServicio')
                                           ->findBy(array( "puntoId"   => $objServicio->getPuntoId()->getId(), 
                                                           "estado"    => $objServicio->getEstado()));
                    $arrayDatosIp   = $this->servicioGeneral
                                           ->getInfoIpsFijaPunto($arrayServicios,
                                                                 $arrayProdIp,
                                                                 $objServicio, 
                                                                 $objServicio->getEstado(),
                                                                 'Activo',
                                                                 $objProductoInternet);
                    $intIpFijasActivas = $arrayDatosIp['ip_fijas_activas'];
                    
                    $strPlanCaracUltraV = $this->getCaracteristicaPorPlan($intIdPlan);
                    $arrayProductoCaracteristicas = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                         ->getProductoCaracteristica($intEmpresaCod,
                                                                                     $objServicioTecnico->getElementoId(),
                                                                                     $strPerfilEquivalente,
                                                                                     $objPlanPromocional->getTipo(),
                                                                                     $strPlanCaracUltraV);
                    if(count($arrayProductoCaracteristicas) == 4)
                    {
                        foreach ($arrayProductoCaracteristicas as $arrayRegistro)
                        {
                            foreach($arrayRegistro as $strKeyEstado => $strValueEstado)
                            {
                                if($strKeyEstado=='DESCRIPCION_CARACTERISTICA')
                                {
                                    $arrayDescripcionCaracteristicas[] = $strValueEstado;
                                }
                            }
                        }
                        
                        $intKey                   = array_search('TRAFFIC-TABLE',
                                                                 $arrayDescripcionCaracteristicas);
                        $arrayValorCaracteristica = $arrayProductoCaracteristicas[$intKey];
                        $strTrafficTablePromo     = $arrayValorCaracteristica['DETALLE_VALOR'];
                        
                        $intKey                   = array_search('GEM-PORT',
                                                                 $arrayDescripcionCaracteristicas);
                        $arrayValorCaracteristica = $arrayProductoCaracteristicas[$intKey];
                        $strGemPortPromo          = $arrayValorCaracteristica['DETALLE_VALOR'];
                        
                        $intKey                   = array_search('LINE-PROFILE-NAME',
                                                                 $arrayDescripcionCaracteristicas);
                        $arrayValorCaracteristica = $arrayProductoCaracteristicas[$intKey];
                        $strLineProfilePromo      = $arrayValorCaracteristica['DETALLE_VALOR'];
                        
                        $intKey                   = array_search('VLAN', 
                                                                 $arrayDescripcionCaracteristicas);
                        $arrayValorCaracteristica = $arrayProductoCaracteristicas[$intKey];
                        $strVlanPromo             = $arrayValorCaracteristica['DETALLE_VALOR'];
                    }
                    else
                    {
                        throw new \Exception("No se lograron recuperar correctamente las características promocionales del nuevo plan, parámetros: ".
                                             "intEmpresaCod:".$intEmpresaCod." olt: ".$objServicioTecnico->getElementoId().
                                             " PerfilEquiv: ".$strPerfilEquivalente." tipoPlan: ".$objPlanPromocional->getTipo().
                                             " strPlanCaracUltraV: ".$strPlanCaracUltraV);
                    }
                }
                else if($strMarcaOlt == "ZTE")
                {
                    //OBTENER SERVICE PROFILE
                    $objSpcServiceProfile = $this->servicioGeneral
                                                 ->getServicioProductoCaracteristica($objServicio,
                                                                                     "SERVICE-PROFILE",
                                                                                     $objProductoInternet);
                    if(is_object($objSpcServiceProfile))
                    {
                        $strServiceProfile = $objSpcServiceProfile->getValor();
                    }
                    
                    //OBTENER SERVICE-PORT
                    $objSpcSpid = $this->servicioGeneral
                                       ->getServicioProductoCaracteristica($objServicio,
                                                                           "SPID",
                                                                           $objProductoInternet);
                    if(is_object($objSpcSpid))
                    {
                        $strSpid    = $objSpcSpid->getValor();
                    }
                    
                    //OBTENER CAPACIDAD1
                    $objCapacidadUp = $this->servicioGeneral
                                           ->getServicioProductoCaracteristica($objServicio,
                                                                               "CAPACIDAD1",
                                                                               $objProductoInternet);
                    if(is_object($objCapacidadUp))
                    {
                        $strCapacidadUp = $objCapacidadUp->getValor();
                    }
                    
                    //OBTENER CAPACIDAD2
                    $objCapacidadDown = $this->servicioGeneral
                                             ->getServicioProductoCaracteristica($objServicio,
                                                                                 "CAPACIDAD2",
                                                                                 $objProductoInternet);
                    if(is_object($objCapacidadDown))
                    {
                        $strCapacidadDown = $objCapacidadDown->getValor();
                    }
                    
                    $arrayProdIp    = $this->emComercial
                                           ->getRepository('schemaBundle:AdmiProducto')
                                           ->findBy(array( "nombreTecnico" => 'IP', 
                                                           "empresaCod"    => $intEmpresaCod, 
                                                           "estado"        => "Activo"));

                    $arrayServicios = $this->emComercial
                                           ->getRepository('schemaBundle:InfoServicio')
                                           ->findBy(array( "puntoId"   => $objServicio->getPuntoId()->getId(), 
                                                           "estado"    => $objServicio->getEstado()));
                    $arrayDatosIp   = $this->servicioGeneral
                                           ->getInfoIpsFijaPunto($arrayServicios,
                                                                 $arrayProdIp,
                                                                 $objServicio, 
                                                                 $objServicio->getEstado(),
                                                                 'Activo',
                                                                 $objProductoInternet);
                    $intIpFijasActivas  = $arrayDatosIp['ip_fijas_activas'];
                    
                    // OBTENER CAPACIDAD UP PROMOCIONAL
                    $objCapacidadUp = $this->emComercial
                                           ->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->findOneBy(array("descripcionCaracteristica" => "CAPACIDAD1", "estado" => "Activo"));
                    $objProdCaractCapacidadUp = $this->emComercial
                                                     ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                     ->findOneBy(array("productoId"       => $objProductoInternet->getId(), 
                                                                       "caracteristicaId" => $objCapacidadUp->getId(), 
                                                                       "estado"           => "Activo"));
                    if(is_object($objProdCaractCapacidadUp))
                    {
                        $objInfoPlanDetUp = $this->emComercial
                                                 ->getRepository('schemaBundle:InfoPlanDet')
                                                 ->findOneBy(array("productoId" => $objProductoInternet->getId(), 
                                                                   "planId"     => $objPlanPromocional));

                        $objInfoPlanProdCaractUp = $this->emComercial
                                                        ->getRepository('schemaBundle:InfoPlanProductoCaract')
                                                        ->findOneBy(array("planDetId"                  => $objInfoPlanDetUp->getId(), 
                                                                          "productoCaracterisiticaId"  => $objProdCaractCapacidadUp->getId()));
                        if(is_object($objInfoPlanProdCaractUp))
                        {
                            $strCapacidadUpPromo =  $objInfoPlanProdCaractUp->getValor();
                        }
                        else
                        {
                            throw new \Exception('No se logró recuperar información promocional de capacidad UP');
                        }
                    }
                    // OBTENER CAPACIDAD DOWN PROMOCIONAL
                    $objCapacidadDown = $this->emComercial
                                             ->getRepository('schemaBundle:AdmiCaracteristica')
                                             ->findOneBy(array("descripcionCaracteristica" => "CAPACIDAD2", "estado" => "Activo"));
                    $objProdCaractCapacidadDown = $this->emComercial
                                                       ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                       ->findOneBy(array("productoId"       => $objProductoInternet->getId(), 
                                                                         "caracteristicaId" => $objCapacidadDown->getId(), 
                                                                         "estado"           => "Activo"));

                    if(is_object($objProdCaractCapacidadDown))
                    {
                        $objInfoPlanDetDown = $this->emComercial
                                                   ->getRepository('schemaBundle:InfoPlanDet')
                                                   ->findOneBy(array("productoId" => $objProductoInternet->getId(), 
                                                                     "planId"     => $objPlanPromocional));
                        $objInfoPlanProdCaractDown = $this->emComercial
                                                          ->getRepository('schemaBundle:InfoPlanProductoCaract')
                                                          ->findOneBy(array("planDetId"                  => $objInfoPlanDetDown->getId(), 
                                                                            "productoCaracterisiticaId"  => $objProdCaractCapacidadDown->getId()));
                        if(is_object($objInfoPlanProdCaractDown))
                        {
                            $strCapacidadDownPromo =  $objInfoPlanProdCaractDown->getValor();
                        }
                        else
                        {
                            throw new \Exception('No se logró recuperar información promocional de capacidad Down');
                        }
                    }
                }
                $strStatus  = "OK";
                $strMensaje = "Información recuperada exitosamente.";
            }
            else
            {
                throw new \Exception( 'No se han enviado los parámetros adecuados para procesar la información. - intIdServicio('.
                                      $intIdServicio.'), intIdPlan('.$intIdPlan.'), strIpCreacion('.$strIpCreacion.
                                      '), intEmpresaCod('.$intEmpresaCod.'), strUsrCreacion('.$strUsrCreacion.'))' );
            }
        }
        catch (\Exception $objEx)
        {
            $strStatus  = "ERROR";
            $strMensaje = "Ha ocurrido un error al obtener la información promocional del servicio. Por favor notificar a Sistemas";
            $this->utilServicio->insertError('Telcos+',
                                             'RecursosDeRedService->obtenerInformacionPromocionesBw',
                                             $objEx->getMessage(),
                                             $strUsrCreacion,
                                             $strIpCreacion);
        }
        $arrayRespuesta = array(
                                'strStatus'    => $strStatus,
                                'strMensaje'   => $strMensaje,
                                'strSerieOnt'  => $strSerieOnt,
                                'strMacOnt'    => $strMacOnt,
                                'strNombreOlt' => $strNombreOlt,
                                'strIpOlt'     => $strIpOlt,
                                'strPuertoOlt' => $strPuertoOlt,
                                'strModeloOlt' => $strModeloOlt,
                                'strMarcaOlt'  => $strMarcaOlt,
                                'strOntId'     => $strOntId,
                                'strSpid'      => $strSpid,
                                'strNombreCliente'     => $strNombreCliente,
                                'strIdentificacion'    => $strIdentificacion,
                                'strLogin'             => $strLogin,
                                'strServiceProfile'    => $strServiceProfile,
                                'strEstadoServicio'    => $strEstadoServicio,
                                'strTipoNegocioActual' => $strTipoNegocioActual,
                                'strTipoNegocioNuevo'  => $strTipoNegocioNuevo,
                                'strIpServicio'     => $strIpServicio,
                                'strScope'          => $strScope,
                                'intIpFijasActivas' => $intIpFijasActivas,
                                'strMacWifi'        => $strMacWifi,
                                'strVlan'           => $strVlan,
                                'strGemPort'        => $strGemPort,
                                'strLineProfile'    => $strLineProfile,
                                'strTrafficTable'   => $strTrafficTable,
                                'strCapacidadUp'    => $strCapacidadUp,
                                'strCapacidadDown'  => $strCapacidadDown,
                                'strVlanPromo'    => $strVlanPromo,
                                'strGemPortPromo' => $strGemPortPromo,
                                'strLineProfilePromo'   => $strLineProfilePromo,
                                'strTrafficTablePromo'  => $strTrafficTablePromo,
                                'strCapacidadUpPromo'   => $strCapacidadUpPromo,
                                'strCapacidadDownPromo' => $strCapacidadDownPromo
                               );
        return $arrayRespuesta;
    }
    
     /**
     * getPerfilPlanEquivalente
     *
     * Método que retorna el valor del perfil equivalente para el plan solicitado
     *      
     * @param array $arrayParametros  ['idPlan', 'valorPerfil', 'tipoAprovisionamiento', 'marca','tipoNegocio']
     * 
     * @return array String valorPerfil
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 23-10-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 26-10-2015 Se agrega validacion de detalle elemento para asignar nuevos perfiles a cliente en OLT ya migrados
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 13-04-2016 Se consulta función de repository que obtiene equivalencia de perfiles
     */
    public function getPerfilPlanEquivalente($arrayParametros)
    {
        $strPerfilEquivalente = $this->emInfraestructura->getRepository( 'schemaBundle:InfoServicioTecnico' )
                                     ->getValorPerfilEquivalente($arrayParametros);
        return $strPerfilEquivalente;
    }
    
    /**
     * geTipoAprovisionamiento
     *
     * Método que retorna el valor del tipo de aprovisionamiento del Elemento OLT
     *      
     * @param int $idOlt
     * 
     * @return array String  $strAprovisionamiento
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 07-12-2015
     */
    public function geTipoAprovisionamiento($idOlt)
    {
        $strAprovisionamiento = "";
        //caracteristica para saber donde esta ubicada la caja (pedestal - edificio)
        $entityDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                      ->findOneBy(array( "elementoId"    => $idOlt, 
                                                         "detalleNombre" => "APROVISIONAMIENTO_IP"));
        if ($entityDetalleElemento)
        {
            $strAprovisionamiento = $entityDetalleElemento->getDetalleValor();
        }
        else
        {
            $strAprovisionamiento = "POOL" ;
        }
        return $strAprovisionamiento;
    }
    
    /**
     * getCaracteristicaPorPlan
     *
     * Método que retorna el valor la caracteristica Ultra Velocidad del plan
     *      
     * @param int $idPlan
     * 
     * @return String $strRespuesta Valor de Caracteristica Ultra Velocidad
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 07-12-2015
     */
    public function getCaracteristicaPorPlan($idPlan)
    {
        $strRespuesta = "";
        //Obtenere Caracteristica Plan Ultra Velocidad
        $entityCaracteristicaUltraV = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->findOneBy(array("descripcionCaracteristica" => "ULTRA VELOCIDAD",
                                                             "estado"                    => "Activo"));
        if ($entityCaracteristicaUltraV)
        {
           $entityInfoPlanCaracUltraV = $this->emComercial->getRepository('schemaBundle:InfoPlanCaracteristica')
                                                          ->findOneByIdPlanCaracteristica($idPlan , 
                                                                                          $entityCaracteristicaUltraV->getId(),
                                                                                          'Activo');
           if ($entityInfoPlanCaracUltraV)
           {
               $strRespuesta = $entityInfoPlanCaracUltraV->getValor();
           }
           else
           {
               $strRespuesta = "NO";
           }
        }
        else
        {
            $strRespuesta = "NO";
        }
        return $strRespuesta;
    }
    
         /**
     * Documentacion para el método 'asignarRecursosRedL3mpls'
     * 
     * Método que realiza la asignación de recursos de red para servicios L3MPLS
     * 
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 28-03-2016
     *          
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 9-05-2016 - Se agrega invocacion a metodo que genera login auxiliar al servicios
     *
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 17-05-2016 - Se guarda la info seleccionada por el usuario y se la guarda en el campo observacion para el
     *                           historial del servicio y de la solicitud
     *
     * @author Eduardo Plua <eplua@telconet.ec>
     * @version 1.3 26-05-2016 - * Se recupera elementoPe desde ws networking
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.4 07-06-2016 - Se agrega parametros para soportar servicios con UM Radio
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.5 29-06-2016  Se realiza procesamiento para cambio de ultima milla
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.6 21-07-2016  Se valida flujos de Fibra Optica RUTA o DIRECTO y UTP para generación de enlaces de manera correcta
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.7 24-08-2016  Se agrega validaciones para poder soportar el proceso de cambio de Um Radio Tn
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.8 16-11-2016  Se agrega validacion para cuando se realice asignacion de recursos para pseudope
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.9 22-02-2018  Se agrega bloque para gestionar recursos de red de clientes que hayan realizado migracion de informacion de
     *                          Factibilidad de otro cliente ( manejar subredes definidas y parametrizadas )
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.0 12-04-2018  Se envia la region del Punto y no del usuario en sesion para obtener la Subred para concentradores de Interconexion
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.1 17-04-2018  Se verifica que el login contenga un concentrador de Interconexion asociado para poder generar subredes del pool 
     *                          asignado, caso contrario se le asignara IP del pool generico en el sistema
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.2 07-05-2018  Se verifica que SOLO si es un extremo este pregunte si su concentrador es de interconexion adicional
     *                          si no existe razon social se envie como parametro de consulta el nombre y apellido del cliente
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 12.3 17-07-2018 - Se enviá nuevo parámetro al método getPeBySwitch
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 12.4 09-11-2018 - Se cambia la forma de obtener la región para configurar interconexiones, se obtiene la región general
     *                            a partir de la Provincia en la cual se encuentre registrado el cliente.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 12.5 22-03-2019 - Se asigna la 4ta. IP al cliente cuando son esquema Pe-Hsrp
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 12.6
     * @since 30-08-2019  Por proyecto segmentacion de VLAN se agrega un parametro a la llamada de la funcion: getInfoBackboneByElemento
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 12.7 22-09-2019 - Se agrega lógica para la activacion del concentrador de interconexion FWA, la subred asignada en el concentrador
          *                       R1 y R2 deben ser identicas solo el tercer octeto varia dependiendo la region al que pertenece el concentrador.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 12.8 23-03-2020 - Cuando se crea la característica del As Privado a la persona empresa rol
     *                            se guarda el id en la característica de la solicitud.
     *                            Se ingresa el tipo de recurso en la característica de la solicitud.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 12.9 09-03-2021 - Se agrega soporte para asignar recursos de red a los servicios con tipo de red GPON.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 13.0 19-07-2021 - Se valida tipo red por deafult MPLS
     * 
     * @author Joel Muñoz M <jrmunoz@telconet.ec>
     * @version 13.1 15-03-2023    Se agrega funcionalidad para guardar mismas IP's de servicio principal si es una migración SDWAN
     * 
     */
    public function asignarRecursosRedL3mpls($arrayParametros)
    {
        $intIdServicio                  = $arrayParametros['idServicio'];
        $intIdDetalleSolicitud          = $arrayParametros['idDetalleSolicitud'];
        $intIdElementoPadre             = $arrayParametros['idElementoPadre'];
        $intIdInterfaceElementoConector = $arrayParametros['hilo'];
        $intVlan                        = $arrayParametros['vlan'];
        $intVrf                         = $arrayParametros['vrf'];
        $strProtocolo                   = $arrayParametros['protocolo'];
        $boolDefaultGateway             = $arrayParametros['defaultGateway'];
        $intAsPrivado                   = $arrayParametros['asPrivado'];
        $strMascara                     = $arrayParametros['mascara'];
        $personaEmpresaRolId            = $arrayParametros['personaEmpresaRolId'];
        $intIdSubred                    = $arrayParametros['idSubred'];
        $flagRecursos                   = $arrayParametros['flagRecursos'];
        $strUltimaMilla                 = $arrayParametros['ultimaMilla'];
        $strUsrCreacion                 = $arrayParametros['usrCreacion'];
        $strIpCreacion                  = $arrayParametros['ipCreacion'];
        $boolFlagTransaccion            = $arrayParametros['flagTransaccion'];
        $boolFlagServicio               = $arrayParametros['flagServicio'];
        $strTipoSolicitud               = $arrayParametros['tipoSolicitud'];
        $boolEsPseudoPe                 = $arrayParametros['esPseudoPe'];
        $observacionServicioYSolicitud  = "";
        $tituloRecursos                 = "";
        $arrayParametrosWs              = array();
        $boolEsCambioUM                 = false;
        $strBanderaServProdCaract       = "N";

        
        if($strTipoSolicitud && $strTipoSolicitud == "SOLICITUD CAMBIO ULTIMA MILLA")
        {
            $boolEsCambioUM = true;
        }
        
        if($boolFlagTransaccion)
        {
            $this->emComercial->getConnection()->beginTransaction();
            $this->emInfraestructura->getConnection()->beginTransaction();
        }
        
        try
        {                                     
            $objServicio                    = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            $objProducto                    = $objServicio->getProductoId();
            $objElementoPadre               = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoPadre);
            $objServicioTecnico             = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                ->findOneBy(array( "servicioId" => $objServicio->getId()));
                        
            //Obtener la caracteristica TIPO_FACTIBILIDAD para discriminar que sea FIBRA DIRECTA o RUTA
            $objServProdCaractTipoFact = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,'TIPO_FACTIBILIDAD',$objProducto);                        

            $strTipoRed = $this->serviceInfoServicioTecnico->getValorCaracteristicaServicio($objServicio, 'TIPO_RED', 'Activo');

            //se verifica si el servicio es tipo de red GPON
            $booleanTipoRedGpon = false;
            if(!empty($strTipoRed))
            {
                $arrayParVerTipoRed = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                        'COMERCIAL',
                                                                                                        '',
                                                                                                        'VERIFICAR TIPO RED',
                                                                                                        'VERIFICAR_GPON',
                                                                                                        $strTipoRed,
                                                                                                        '',
                                                                                                        '',
                                                                                                        '');
                if(isset($arrayParVerTipoRed) && !empty($arrayParVerTipoRed))
                {
                    $booleanTipoRedGpon = true;
                }
            }

            if($objServProdCaractTipoFact)
            {
                $tipoFactibilidad = $objServProdCaractTipoFact->getValor();
            }
            else
            {
                if($strUltimaMilla == "Fibra Optica")
                {
                    $tipoFactibilidad = "RUTA";
                }
                elseif ($strUltimaMilla == 'FTTx' && $booleanTipoRedGpon)
                {
                    $tipoFactibilidad = "RUTA";
                }
                else
                {
                    $tipoFactibilidad = "DIRECTO";
                }
            }                       
            
            //Se verifica que el cambio de recursos de red sea de Cambio de Ultima Milla
            if($boolEsCambioUM)            
            {           
                $arrayParametros['tipoFactibilidad'] = $tipoFactibilidad;
                //se agrega validacion para ejecutar el proceso correspondiente segun la ultima milla del servicio
                if ($strUltimaMilla == "Radio")
                {
                    $this->asignacionRecursosRedUMRadio($arrayParametros);
                }
                else
                {
                    $this->asignacionRecursosRedUM($arrayParametros);
                }
            } 
            
            $objInterfaceElementoConector = null;
            
            //Se valida que solo obtenga elemento conector cuando se fibra optica RUTA
            if ( ( ($strUltimaMilla == "Fibra Optica" && $tipoFactibilidad == 'RUTA') || ($strUltimaMilla == 'FTTx' && $booleanTipoRedGpon) )
                 && $boolFlagServicio === true)
            {
                if($objServicioTecnico->getInterfaceElementoConectorId())
                {
                    $objInterfaceElementoConector   = $this->emInfraestructura
                                                           ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                           ->find($objServicioTecnico->getInterfaceElementoConectorId());
                }
            }
            $objDetalleSolicitud            = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                ->findOneById($intIdDetalleSolicitud);
            $ipServicio                     = "NoDisponible";
            $arrayIpServicioPrincipal       = array();
            
            $observacionServicioYSolicitud  .= "Informaci&oacute;n de Backbone<br/>";
            $observacionServicioYSolicitud  .= "Recursos: ".ucfirst($flagRecursos)."<br/>";

            $objAdmiTipoRecurso = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'TIPO_RECURSO'));
            if($flagRecursos==="existentes")
            {
                
                $objSubredServicio  = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->find($intIdSubred);
                $strMascara         = $objSubredServicio->getMascara();

                // SI ES MIGRACIÓN SDWAN CONSERVA LA MISMA IP DEL SERVICIO PRINCIPAL
                if(!empty($arrayParametros['esMigracionSDWAN']) && $arrayParametros['esMigracionSDWAN'] === 'SI')
                {
                    $objServPrincipalSDWAN = $this->serviceInfoServicioTecnico->getServicioProductoCaracteristica(
                        $objServicio,
                        "SERVICIO_MIGRADO_SDWAN",
                        $objServicio->getProductoId()
                    );
                    
                    if(is_object($objServPrincipalSDWAN))
                    {
                        $arrayIpServicioPrincipal = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                ->createQueryBuilder('ip')
                                                ->where('ip.servicioId = :idServicioPrincipal')
                                                ->andWhere("ip.estado in (:estadoActivo)")
                                                ->orderBy('ip.tipoIp', 'DESC')
                                                ->setParameter('idServicioPrincipal', $objServPrincipalSDWAN->getValor())
                                                ->setParameter('estadoActivo', array('Activo', 'Reservada'))
                                                ->getQuery()
                                                ->getResult();

                        if(!empty($arrayIpServicioPrincipal) && is_array($arrayIpServicioPrincipal) && count($arrayIpServicioPrincipal)>0)
                        {
                            $arrayIpServicioPrincipalAux = $arrayIpServicioPrincipal;
                            $ipServicio = array_shift($arrayIpServicioPrincipalAux)->getIp();
                        }
                    }
                }
                else
                {
                    $ipServicio  = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')->getIpDisponibleBySubred($intIdSubred);
                }

                $tituloRecursos  .= "Mismos Recursos<br/>";
                //se ingresa el tipo de recurso en la característica de la solicitud
                if(is_object($objAdmiTipoRecurso))
                {
                    $arrayParametrosTipoRecurso = array(
                                                    'objDetalleSolicitudId' => $objDetalleSolicitud,
                                                    'objCaracteristica'     => $objAdmiTipoRecurso,
                                                    'estado'                => "Asignada",
                                                    'valor'                 => "existentes",
                                                    'usrCreacion'           => $strUsrCreacion
                                                );
                    $this->servicioGeneral->insertarInfoDetalleSolCaract($arrayParametrosTipoRecurso);
                }
            }
            elseif($flagRecursos==="nuevos")
            {                
                $tituloRecursos  .= "Nuevos Recursos<br/>";
                
                $arrayParametrosSubred = array();
                $arrayParametrosSubred['tipoAccion'] = "asignar";
                $arrayParametrosSubred['mascara']    = trim($strMascara);
                
                $objSubredServicio                   = null;
                $boolFlujoInterconexion              = false;

                //se ingresa el tipo de recurso en la característica de la solicitud
                if(is_object($objAdmiTipoRecurso))
                {
                    $arrayParametrosTipoRecurso = array(
                                                    'objDetalleSolicitudId' => $objDetalleSolicitud,
                                                    'objCaracteristica'     => $objAdmiTipoRecurso,
                                                    'estado'                => "Asignada",
                                                    'valor'                 => "nuevos",
                                                    'usrCreacion'           => $strUsrCreacion
                                                );
                    $this->servicioGeneral->insertarInfoDetalleSolCaract($arrayParametrosTipoRecurso);
                }

                //Obtener la razon social
                $strRazonSocialCliente = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial();
                
                //Verificar si el cliente esta configurado como interconexion
                $arrayInfoEnvio   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('RAZON SOCIAL CON OPCION DE INTERCONEXION', 
                                                          'COMERCIAL', 
                                                          '',
                                                          '',
                                                          !empty($strRazonSocialCliente)?$strRazonSocialCliente:
                                                          $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getNombres().' '.
                                                          $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getApellidos(), 
                                                          '',
                                                          '',
                                                          '', 
                                                          '', 
                                                          $arrayParametros['empresaCod']);

                if(!empty($arrayInfoEnvio) && !$booleanTipoRedGpon)
                {
                    //Si es extremo consulto si su concentrador es de tipo CONCINTER ( interconexion )
                    if( $objProducto->getEsConcentrador() == 'NO')
                    {
                        //Obtener el concentrador
                        $objConcentrador = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                    'ENLACE_DATOS',
                                                                                                    $objProducto
                                                                                                   );
                        if(is_object($objConcentrador))
                        {
                            //Verificar si el concentrador tiene nombre tecnico de Interconexion
                            $objServicioConc = $this->emComercial->getRepository("schemaBundle:InfoServicio")->find($objConcentrador->getValor());

                            if(is_object($objServicioConc))
                            {
                                $strNombreTecnico = $objServicioConc->getProductoId()->getNombreTecnico();

                                if($strNombreTecnico == 'CONCINTER')
                                {
                                    $boolFlujoInterconexion = true;
                                }
                            }
                        }
                        else
                        {
                            throw new \Exception("Debe tener enlazado el concentrador de Datos para continuar");
                        }
                    }
                }

                //Verificar que el servicio a asignar recursos de red posea caracteristica INTERCONEXION_CLIENTES donde se tomara
                //recursos especiales designados por el cliente a partir de la razon social
                $objServProdCaractMigracion = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                        'INTERCONEXION_CLIENTES',
                                                                                                        $objProducto
                                                                                                       );
                //Entra a flujo especial para asignacion de subredes
                //Solo entrará aqui si se trata de un flujo de intercinexion o contiene la bandera configurada en el servicio
                //generada por migracion de factibilidad
                if($boolFlujoInterconexion || is_object($objServProdCaractMigracion) && $objServProdCaractMigracion->getValor() == 'S')
                {
                    $strRegion      = '';      
                    $strTipo        = 'CLIENTES';
                    $strRazonSocial = '';
                    
                    $objPersonaRol = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")->find($personaEmpresaRolId);
                    
                    if(is_object($objPersonaRol))
                    {                        
                        $strRS          = $objPersonaRol->getPersonaId()->getRazonSocial();
                        $strRazonSocial = !empty($strRS)?$strRS:$objPersonaRol->getPersonaId()->getNombres().' '.
                                                                $objPersonaRol->getPersonaId()->getApellidos();
                    }
                    
                    if($objProducto->getEsConcentrador()== 'SI')
                    {
                        //Obtener la Region a la cual pertenece el punto
                    $objCaracteristicaEnlaceDatos = $this->emComercial
                                                         ->getRepository('schemaBundle:AdmiCaracteristica')
                                                         ->findOneBy(array(
                                                                           "descripcionCaracteristica" => 'CONCENTRADOR_FWA',
                                                                           "estado"                    => 'Activo'));
                    $objServProdCaractFwa         = $this->emComercial
                                                         ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                         ->findOneBy(array(
                                                                            'valor'               => $objServicio->getId(),
                                                                            'caracteristicaId'    => $objCaracteristicaEnlaceDatos,
                                                                            'estado'              => 'Activo'));
                        if(is_object($objServProdCaractFwa))
                        {
                            $arrayRegion   = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                     ->getRegionPorElemtoId($intIdElementoPadre);
                            if(!empty($arrayRegion))
                            {
                                $strRegion  =   $arrayRegion['registro'];
                            }
                        }
                        else
                        {
                            $intIdOficina = $objServicio->getPuntoId()->getPuntoCoberturaId()->getOficinaId();

                            $objOficina   = $this->emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficina);

                            if(is_object($objOficina))
                            {
                                $objCanton = $this->emComercial->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());

                                if(is_object($objCanton))
                                {
                                    $strRegion = $objCanton->getProvinciaId()->getRegionId()->getNombreRegion();
                                }
                            }
                        }
                                                                        
                        $strTipo = 'CONCENTRADORES';
                    }
                    
                    if(empty($strRazonSocial))
                    {
                        throw new \Exception("No Existen Información de Cliente a asignar recursos de Red, notificar a Sistemas");
                    }
                    
                    $arraySubredesEspeciales   =  $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                  ->get('SUBREDES INTERCONEXION ENTRE CLIENTES', 
                                                                        'TECNICO', 
                                                                        '',
                                                                        $strRazonSocial,
                                                                        $strTipo,
                                                                        '',
                                                                        '',
                                                                        '', 
                                                                        $strRegion, 
                                                                        $arrayParametros['empresaCod']);
                    if(empty($arraySubredesEspeciales))
                    {
                        $objCaracteristicaEnlaceDatos = $this->emComercial
                                                             ->getRepository('schemaBundle:AdmiCaracteristica')
                                                             ->findOneBy(array(
                                                                               "descripcionCaracteristica" => 'CONCENTRADOR_FWA',
                                                                               "estado"                    => 'Activo'));
                        $objConcentradorVirtual       = $this->emComercial
                                                             ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                             ->findOneBy(array(
                                                                                'valor'               => $objServicio->getId(),
                                                                                'caracteristicaId'    => $objCaracteristicaEnlaceDatos,
                                                                                'estado'              => 'Activo'));
                        if(is_object($objConcentradorVirtual))
                        {
                            $strRazonSocial            = "OTECEL S . A.";
                            $arraySubredesEspeciales   =  $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                          ->get('SUBREDES INTERCONEXION ENTRE CLIENTES',
                                                                                'TECNICO',
                                                                                '',
                                                                                $strRazonSocial,
                                                                                $strTipo,
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                $strRegion,
                                                                                $arrayParametros['empresaCod']);
                        }
                    }

                    $arrayParametrosSubred['uso'] = "DATOS-MIGRA-CLIENTES";

                    //Recorrer las subredes
                    foreach($arraySubredesEspeciales as $array)
                    {
                        $arrayParametrosSubred['subredPrefijo'] = $array['valor2'].".".$array['valor3'].".".$array['valor4'];

                        $arraySubred = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                               ->provisioningSubred($arrayParametrosSubred);

                        if($arraySubred['subredId']>0)
                        {
                            $intIdSubred        = $arraySubred['subredId'];
                            $objSubredServicio  = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                       ->find($arraySubred['subredId']);
                            $objSubredServicio->setElementoId($objElementoPadre);
                            $this->emInfraestructura->persist($objSubredServicio);
                            $this->emInfraestructura->flush();

                            $ipServicio = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                  ->getIpDisponibleBySubred($arraySubred['subredId']);
                            break;
                        }
                    }//END FOREACH
                }
                else
                {                    
                    $ArrayPrefijosRed               = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                            ->findBy(array("elementoId"    => $objElementoPadre,
                                                                                            "detalleNombre" => "PREFIJO_RED",
                                                                                            "estado"        => "Activo"));

                    if(!$ArrayPrefijosRed)
                    {
                        throw new \Exception("No Existen Prefijos de red para [ ".$objElementoPadre->getNombreElemento()." ]");
                    }
                                       
                    if ($booleanTipoRedGpon)
                    {
                        //seteo los parametros de las subredes
                        $strDatosGpon = "DATOSGPON";
                        $arrayParametrosSubredGpon = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->getOne('NUEVA_RED_GPON_TN',
                                                                            'COMERCIAL',
                                                                            '',
                                                                            'NOMBRES PARAMETROS SUBREDES Y VLANS',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '');
                        if(isset($arrayParametrosSubredGpon) && !empty($arrayParametrosSubredGpon)
                           && isset($arrayParametrosSubredGpon['valor7']) && !empty($arrayParametrosSubredGpon['valor7']))
                        {
                            $strDatosGpon = $arrayParametrosSubredGpon['valor7'];
                        }
                        $arrayParametrosSubred['uso'] = $strDatosGpon;
                    }
                    else
                    {
                        $arrayParametrosSubred['uso'] = "DATOSMPLS";
                    }

                    //solicitar subred libre
                    foreach($ArrayPrefijosRed as $objPrefijoRed)
                    {
                        $arrayParametrosSubred['subredPrefijo'] = "10.".trim($objPrefijoRed->getDetalleValor());

                        $arraySubred = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                               ->provisioningSubred($arrayParametrosSubred);

                        if($arraySubred['subredId']>0)
                        {
                            $intIdSubred        = $arraySubred['subredId'];
                            $objSubredServicio  = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->find($arraySubred['subredId']);
                            $objSubredServicio->setElementoId($objElementoPadre);
                            $this->emInfraestructura->persist($objSubredServicio);
                            $this->emInfraestructura->flush();

                            $ipServicio = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                  ->getIpDisponibleBySubred($arraySubred['subredId']);

                            break;
                        }
                    }
                }//end flujo normal        
                
                if(!is_object($objSubredServicio))
                {
                    throw new \Exception("No Existen Subredes disponibles");
                }
            }
            else
            {
                throw new \Exception("Opcion no válida para Asignar Recursos de Red");
            }
            
            if($ipServicio=="NoDisponible")
            {
                throw new \Exception("No Existen Ips disponibles");
            }

            //*************Validar si la orden de servicio tiene seteado el esquema de Pe-Hsrp*************//
            $strBanderaServProdCaract = "N";
            $arrayParametrosProdCaract["strCaracteristica"] = "PE-HSRP";
            $arrayParametrosProdCaract["objProducto"]       = $objProducto;
            $arrayParametrosProdCaract["objServicio"]       = $objServicio;

            $strBanderaServProdCaract = $this->serviceCliente->consultaServicioProdCaract($arrayParametrosProdCaract);
            //*************Validar si la orden de servicio tiene seteado el esquema de Pe-Hsrp*************//

            if($strBanderaServProdCaract === "S" && (empty($arrayParametros['esMigracionSDWAN']) ||
                                                     $arrayParametros['esMigracionSDWAN'] != 'SI')) 
            {
                //Si es el esquema Pe-Hsrp se asigna siempre la 4ta Ip al servicio del cliente
                $arrayOctetos = explode(".", $ipServicio);
                $strIpPe1     = $ipServicio;

                //Se suma 2 octetos para obtener la 4ta IP de la subred generada
                $strOctetoP2 = intval($arrayOctetos[3]) + 1;
                $strIpPe2    = $arrayOctetos[0].'.'.$arrayOctetos[1].'.'.$arrayOctetos[2].'.'.$strOctetoP2;

                $strOctetoIp = intval($arrayOctetos[3]) + 2;
                $ipServicio  = $arrayOctetos[0].'.'.$arrayOctetos[1].'.'.$arrayOctetos[2].'.'.$strOctetoIp;

                //Ocupar las Ips para los Pes
                $objInfoIp1 = new InfoIp();
                $objInfoIp1->setIp($strIpPe1);
                $objInfoIp1->setSubredId($objSubredServicio->getId());
                $objInfoIp1->setMascara($objSubredServicio->getMascara());
                $objInfoIp1->setFeCreacion(new \DateTime('now'));
                $objInfoIp1->setUsrCreacion($strUsrCreacion);
                $objInfoIp1->setIpCreacion($strIpCreacion);
                $objInfoIp1->setEstado("Activo");
                $objInfoIp1->setTipoIp("WAN");
                $objInfoIp1->setVersionIp("IPV4");
                $this->emInfraestructura->persist($objInfoIp1);
                $this->emInfraestructura->flush();

                $objInfoIp2 = new InfoIp();
                $objInfoIp2->setIp($strIpPe2);
                $objInfoIp2->setSubredId($objSubredServicio->getId());
                $objInfoIp2->setMascara($objSubredServicio->getMascara());
                $objInfoIp2->setFeCreacion(new \DateTime('now'));
                $objInfoIp2->setUsrCreacion($strUsrCreacion);
                $objInfoIp2->setIpCreacion($strIpCreacion);
                $objInfoIp2->setEstado("Activo");
                $objInfoIp2->setTipoIp("WAN");
                $objInfoIp2->setVersionIp("IPV4");
                $this->emInfraestructura->persist($objInfoIp2);
                $this->emInfraestructura->flush();
            }

            if(empty($arrayParametros['esMigracionSDWAN']) || $arrayParametros['esMigracionSDWAN'] != 'SI')
            {
                //se graba ip del servicio
                $objInfoIp = new InfoIp();
                $objInfoIp->setIp($ipServicio);
                $objInfoIp->setSubredId($intIdSubred);
                $objInfoIp->setServicioId($objServicio->getId());
                $objInfoIp->setMascara($strMascara);
                $objInfoIp->setFeCreacion(new \DateTime('now'));
                $objInfoIp->setUsrCreacion($strUsrCreacion);
                $objInfoIp->setIpCreacion($strIpCreacion);
                $objInfoIp->setEstado("Activo");
                $objInfoIp->setTipoIp("WAN");
                $objInfoIp->setVersionIp("IPV4");
                $this->emInfraestructura->persist($objInfoIp);
                $this->emInfraestructura->flush();
            }

            // SE GUARDAN LAS MISMAS IP AL SERVICIO SDWAN
            foreach($arrayIpServicioPrincipal as $objIpServicio)
            {
                $objInfoIp = new InfoIp();
                $objInfoIp->setIp($objIpServicio->getIp());
                $objInfoIp->setSubredId($objIpServicio->getSubredId());
                $objInfoIp->setServicioId($objServicio->getId());
                $objInfoIp->setMascara($objIpServicio->getMascara());
                $objInfoIp->setFeCreacion(new \DateTime('now'));
                $objInfoIp->setUsrCreacion($strUsrCreacion);
                $objInfoIp->setIpCreacion($strIpCreacion);
                $objInfoIp->setEstado("Activo");
                $objInfoIp->setTipoIp($objIpServicio->getTipoIp());
                $objInfoIp->setVersionIp($objIpServicio->getVersionIp());
                $this->emInfraestructura->persist($objInfoIp);
                $this->emInfraestructura->flush();
            }

            if( ($strUltimaMilla == "Fibra Optica" && $tipoFactibilidad == 'RUTA') || ($strUltimaMilla == 'FTTx' && $booleanTipoRedGpon) )
            {
                //verificar si el puerto reservado es el mismo que el seleccionado
                if(is_numeric($intIdInterfaceElementoConector) && $boolFlagServicio == true)
                {
                    //verificar si el puerto reservado es el mismo que el seleccionado
                    if(is_object($objInterfaceElementoConector) && $objInterfaceElementoConector->getId() === $intIdInterfaceElementoConector)
                    {
                        //se conecta el puerto
                        $objInterfaceElementoConector->setEstado("reserved");
                        $this->emInfraestructura->persist($objInterfaceElementoConector);
                        $this->emInfraestructura->flush();
                    }
                    else
                    {
                        $objInterfaceElementoConectorNuevo  = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                                      ->find($intIdInterfaceElementoConector);

                        if(is_object($objInterfaceElementoConector) && is_object($objInterfaceElementoConectorNuevo))
                        {
                            //desconectar viejo puerto
                            $objInterfaceElementoConector->setEstado("not connect");
                            $this->emInfraestructura->persist($objInterfaceElementoConector);
                            $this->emInfraestructura->flush();

                            //conectar nuevo puerto
                            $objInterfaceElementoConectorNuevo->setEstado("reserved");
                            $this->emInfraestructura->persist($objInterfaceElementoConectorNuevo);
                            $this->emInfraestructura->flush();

                            $objServicioTecnico->setInterfaceElementoConectorId($intIdInterfaceElementoConector);
                            $this->emComercial->persist($objServicioTecnico);
                            $this->emComercial->flush();
                        }
                    }
                }
                else
                {
                    if($boolFlagServicio)
                    {
                        if(is_object($objInterfaceElementoConector))
                        {
                            //se conecta el puerto
                            $objInterfaceElementoConector->setEstado("reserved");
                            $this->emInfraestructura->persist($objInterfaceElementoConector);
                            $this->emInfraestructura->flush();
                        }
                    }
                }
            }
            //se graba caracteristica vlan
            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                           $objProducto, 
                                                                           !$boolEsPseudoPe?"VLAN":"VLAN_PROVEEDOR", 
                                                                           $intVlan, 
                                                                           $strUsrCreacion);
            //se graba caracteristica vrf
            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                           $objProducto, 
                                                                           "VRF", 
                                                                           $intVrf, 
                                                                           $strUsrCreacion);
            //se graba caracteristica protocolo
            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                           $objProducto, 
                                                                           "PROTOCOLO_ENRUTAMIENTO", 
                                                                           $strProtocolo, 
                                                                           $strUsrCreacion);
            $observacionServicioYSolicitud.="</br><b>Datos de Factibilidad</b><br/>";
            $arrParametrosFactibilidad      =   array (
                                                    'idServicio'    => $intIdServicio,
                                                    'emComercial'   => $this->emComercial
                                                );

            $arrParametrosFactibilidad['ultimaMilla'] = $strUltimaMilla;
            $arrParametrosFactibilidad['tipoUM'] = $tipoFactibilidad;
            //Obtener la informacion de factibilidad solo para procesos normales
            if(!$boolEsPseudoPe)
            {
                $arrInfoFactibilidad    = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                                  ->getDatosFactibilidad($arrParametrosFactibilidad);            
           
                $arrayInfoFactibilidad  = $arrInfoFactibilidad['data'];
                $arrayParametrosWs["intIdElemento"] = $arrayInfoFactibilidad['idElemento'];
                $arrayParametrosWs["intIdServicio"] = $intIdServicio;
                if ($booleanTipoRedGpon)
                {
                    $objElementoPe      = $this->servicioGeneral->getPeByOlt($arrayParametrosWs);
                }
                else
                {
                    $objElementoPe      = $this->servicioGeneral->getPeBySwitch($arrayParametrosWs);
                }
                $arrInfoBackbone        = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                               ->getInfoBackboneByElemento($arrayInfoFactibilidad['idElemento'],$objElementoPe,"N");

                $arrMergeResultFactibilidad = array_merge($arrayInfoFactibilidad,$arrInfoBackbone);

                $observacionServicioYSolicitud .= "&#10140; <b>Nombre Elemento Padre:</b> " .
                    $arrMergeResultFactibilidad["nombreElementoPadre"] . "<br/>";
                $observacionServicioYSolicitud .= "&#10140; <b>Nombre Elemento:</b> " .
                    $arrMergeResultFactibilidad["nombreElemento"] . "<br/>";
                $observacionServicioYSolicitud .= "&#10140; <b>Nombre Interfaz Elemento:</b> " .
                    $arrMergeResultFactibilidad["nombreInterfaceElemento"] . "<br/>";


                if ($booleanTipoRedGpon)
                {
                    $observacionServicioYSolicitud .= "&#10140; <b>Nombre Elemento Conector:</b> " .
                        $arrMergeResultFactibilidad["nombreElementoConector"] . "<br/>";
                    $observacionServicioYSolicitud .= "&#10140; <b>Puerto Elemento Conector:</b> " .
                        $arrMergeResultFactibilidad["puertoElementoConector"] . "<br/>";
                }
                else
                {
                    $observacionServicioYSolicitud .= "&#10140; <b>Nombre Elemento Contenedor:</b> " .
                        $arrMergeResultFactibilidad["nombreElementoContenedor"] . "<br/>";
                    $observacionServicioYSolicitud .= "&#10140; <b>Nombre Elemento Conector:</b> " .
                        $arrMergeResultFactibilidad["nombreElementoConector"] . "<br/>";
                    $observacionServicioYSolicitud .= "&#10140; <b>Hilo:</b> " .
                        $arrMergeResultFactibilidad["numeroColorHilo"] . "<br/><br/>";
                    $observacionServicioYSolicitud .= "&#10140; <b>Anillo:</b> " .
                        $arrMergeResultFactibilidad["anillo"] . "<br/>";
                }

            }

            $observacionServicioYSolicitud .= "</br><b>" . $tituloRecursos . "</b>";
            $observacionServicioYSolicitud.="&#10140; <b>Subred del Servicio:</b> ".$objSubredServicio->getSubred()."<br/>";
            $observacionServicioYSolicitud.="&#10140; <b>IP del Servicio:</b> ".$ipServicio."<br/>";
            
            $nombreVLAN="";
            
            if(!$boolEsPseudoPe)
            {
                $objPersonaEmpresaRolCaracVlan = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->find($intVlan);
                
                if($objPersonaEmpresaRolCaracVlan)
                {
                    $idDetalleElementoVlan=$objPersonaEmpresaRolCaracVlan->getValor();
                    $objDetalleElementoVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                      ->find($idDetalleElementoVlan);
                    if($objDetalleElementoVlan)
                    {
                        $nombreVLAN = $objDetalleElementoVlan->getDetalleValor();
                    }
                }
            }
            else
            {
                $nombreVLAN = $intVlan;
            }
            
            $observacionServicioYSolicitud.="&#10140; <b> Vlan: </b>".$nombreVLAN."<br/>";
            $objVrf = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->find($intVrf);
            $nombreVrf="";
            if($objVrf)
            {
                $nombreVrf=$objVrf->getValor();
            }
            $observacionServicioYSolicitud.="&#10140; <b> Vrf:</b> ".$nombreVrf."<br/>";
            $observacionServicioYSolicitud.="&#10140; <b> Protocolo:</b> ".$strProtocolo."<br/>";
            $observacionServicioYSolicitud.="&#10140; <b> Mascara:</b> ".$strMascara."<br/>";
            
            //se graba caracteristica default gateway
            if($boolDefaultGateway=="true")
                $strDefaultGateway = 'SI';
            else
                $strDefaultGateway = 'NO';
            
            $objDefaultGateway = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 
                                                                                           "DEFAULT_GATEWAY", 
                                                                                           $objProducto);
            if($objDefaultGateway)
            {
                $objDefaultGateway->setValor($strDefaultGateway);
                $this->emComercial->persist($objDefaultGateway);
                $this->emComercial->flush();
            }
            else
            {
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                               $objProducto, 
                                                                               "DEFAULT_GATEWAY", 
                                                                               $strDefaultGateway, 
                                                                               $strUsrCreacion);
            }
            
            
            //se graba as privado 
            $objAsPrivado = $this->emComercial
                                 ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                 ->getOneByCaracteristica($personaEmpresaRolId,"AS_PRIVADO");
            if(!$objAsPrivado && $intAsPrivado>0)
            {
                $objPersonaEmpresaRol        = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                 ->find($personaEmpresaRolId);
                
                $objCaracteristicaAsPrivado  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                 ->findOneBy(array("descripcionCaracteristica" =>"AS_PRIVADO", "estado" => "Activo"));
                
                $objInfoPersonaEmpresaRolCaracAsprivado = new InfoPersonaEmpresaRolCarac();
                $objInfoPersonaEmpresaRolCaracAsprivado->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                $objInfoPersonaEmpresaRolCaracAsprivado->setCaracteristicaId($objCaracteristicaAsPrivado);
                $objInfoPersonaEmpresaRolCaracAsprivado->setValor($intAsPrivado);
                $objInfoPersonaEmpresaRolCaracAsprivado->setEstado("Activo");
                $objInfoPersonaEmpresaRolCaracAsprivado->setFeCreacion(new \DateTime('now'));
                $objInfoPersonaEmpresaRolCaracAsprivado->setUsrCreacion($strUsrCreacion);
                $objInfoPersonaEmpresaRolCaracAsprivado->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objInfoPersonaEmpresaRolCaracAsprivado);
                $this->emComercial->flush();

                $objCaracPerEmpAsPrivado    = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array("descripcionCaracteristica" =>"ID_PERSONA_EMPRESA_ROL_CARAC_AS_PRIVADO",
                                                                  "estado" => "Activo"));
                if( is_object($objDetalleSolicitud) && is_object($objCaracPerEmpAsPrivado) && 
                    is_object($objInfoPersonaEmpresaRolCaracAsprivado) )
                {
                    //grabar el id del InfoPersonaEmpresaRolCarac en el detalles en la solicitud
                    $arrayParametrosTecAsPrivado    = array(
                                                            'objDetalleSolicitudId' => $objDetalleSolicitud,
                                                            'objCaracteristica'     => $objCaracPerEmpAsPrivado,
                                                            'estado'                => 'Asignada',
                                                            'valor'                 => $objInfoPersonaEmpresaRolCaracAsprivado->getId(),
                                                            'usrCreacion'           => $strUsrCreacion
                                                        );
                    $this->container->get('tecnico.InfoServicioTecnico')->insertarInfoDetalleSolCaract($arrayParametrosTecAsPrivado);
                }
            }
            
            if($intAsPrivado>0)
            {
                $observacionServicioYSolicitud.="&#10140; <b> As privado:</b> ".$intAsPrivado."<br/>";
            }
            
            if($strProtocolo=="BGP")
            {
                $observacionServicioYSolicitud.="&#10140; <b> Neighbor Default:</b> ".$strDefaultGateway."<br/><br/>";
            }
            if($boolFlagServicio && !$boolEsCambioUM)
            {
        
                //se actualiza el estado del servicio
                $objServicio->setEstado("Asignada");
                $this->emComercial->persist($objServicio);
                $this->emComercial->flush();
                
                //agregar historial del servicio
                $objInfoHistorial = new InfoServicioHistorial();
                $objInfoHistorial->setServicioId($objServicio);
                $objInfoHistorial->setEstado("Asignada");
                $objInfoHistorial->setUsrCreacion($strUsrCreacion);
                $objInfoHistorial->setFeCreacion(new \DateTime('now'));
                $objInfoHistorial->setIpCreacion($strIpCreacion);
                $objInfoHistorial->setObservacion($observacionServicioYSolicitud);
                $this->emComercial->persist($objInfoHistorial);
                $this->emComercial->flush();                                
            }
            
            if($objDetalleSolicitud)
            {
                //se actualiza estado a la solicitud
                $objDetalleSolicitud->setEstado("Asignada");
                $this->emComercial->persist($objDetalleSolicitud);
                $this->emComercial->flush();

                //agregar historial a la solicitud
                $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
                $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
                $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitudHistorial->setUsrCreacion($strUsrCreacion);
                $objDetalleSolicitudHistorial->setEstado("Asignada");
                $objDetalleSolicitudHistorial->setObservacion($observacionServicioYSolicitud);
                $this->emComercial->persist($objDetalleSolicitudHistorial);
                $this->emComercial->flush();
            }
            else
            {
                throw new \Exception("No Existe Solicitud , favor comunicarse con Sistemas!");
            }
            
            //Generacion de Login Auxiliar al Servicio            
            $this->servicioGeneral->generarLoginAuxiliar($intIdServicio);
            
            if($boolFlagTransaccion)
            {
            
                if ($this->emInfraestructura->getConnection()->isTransactionActive())
                {
                    $this->emInfraestructura->getConnection()->commit();
                    $this->emInfraestructura->getConnection()->close();
                }

                if ($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->getConnection()->commit();
                    $this->emComercial->getConnection()->close();
                }
            }
            
            $status         = "OK";
            $mensaje        = "OK";
        }
        catch (\Exception $e)
        {
            if($boolFlagTransaccion)
            {
            
                if ($this->emInfraestructura->getConnection()->isTransactionActive())
                {
                    $this->emInfraestructura->getConnection()->rollback();
                    $this->emInfraestructura->getConnection()->close();
                }
            
                if ($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->getConnection()->rollback();
                    $this->emComercial->getConnection()->close();
                }
            }
            
            $status         = "ERROR";
            $mensaje        = $e->getMessage();
        }
        
        $respuestaFinal = array('status'=>$status, 'mensaje'=>$mensaje);
        
        return $respuestaFinal; 
    }
    
    /**
     * Metodo encargado para la asignacion de recursos de red para servicios VSAT
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 21-06-2017
     * 
     * @param  Array $arrayParametros    [
     *                                      idServicio              Referencia del Servicio
     *                                      idDetalleSolicitud      Referencia de la Solicitud creada
     *                                      idElementoPadre         Referencia del Elemento Pe
     *                                      vlan                    Vlan del cliente
     *                                      vrf                     Vrf del cliente
     *                                      protocolo               Protocolo del cliente
     *                                      defaultGateway          Default Gateway
     *                                      asPrivado               asPrivado del cliente
     *                                      mascara                 Mascara /29 de configuracion a nivel de Backbone
     *                                      mascaraCliente          Mascara /30 de configuracion de las Vsats
     *                                      personaEmpresaRolId     Referencia a la persona empresa rol del cliente
     *                                      idSubred                id subred elegida
     *                                      flagRecursos            si son recursos nuevos o existentes
     *                                      flagTransaccion         Si se transacciona o no
     *                                  ]
     * @return Array $arrayRespuestaFinal
     * @throws \Exception
     */
    public function asignarRecursosL3mplsVsat($arrayParametros)
    {
        $intIdServicio                  = $arrayParametros['idServicio'];
        $intIdDetalleSolicitud          = $arrayParametros['idDetalleSolicitud'];
        $intIdElementoPadre             = $arrayParametros['idElementoPadre'];
        $intVlan                        = $arrayParametros['vlan'];
        $intVrf                         = $arrayParametros['vrf'];
        $strProtocolo                   = $arrayParametros['protocolo'];
        $boolDefaultGateway             = $arrayParametros['defaultGateway'];
        $intAsPrivado                   = $arrayParametros['asPrivado'];
        $strMascara                     = $arrayParametros['mascara'];
        $strMascaraCliente              = $arrayParametros['mascaraCliente'];
        $personaEmpresaRolId            = $arrayParametros['personaEmpresaRolId'];
        $intIdSubred                    = $arrayParametros['idSubred'];
        $strRecursos                    = $arrayParametros['flagRecursos'];        
        $strUsrCreacion                 = $arrayParametros['usrCreacion'];
        $strIpCreacion                  = $arrayParametros['ipCreacion'];
        $boolFlagTransaccion            = $arrayParametros['flagTransaccion'];
        $observacionServicioYSolicitud  = "";
        $strTituloRecursos              = "";    
        $objSubredServicio              = null;
        $objSubredBb                    = null;
        
        if($boolFlagTransaccion)
        {
            $this->emComercial->getConnection()->beginTransaction();
            $this->emInfraestructura->getConnection()->beginTransaction();
        }
        
        try
        {                                     
            $objServicio                    = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            $objProducto                    = $objServicio->getProductoId();
            $objElementoPadre               = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoPadre);
            
            $objDetalleSolicitud            = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                ->findOneById($intIdDetalleSolicitud);
            $ipServicio                     = "NoDisponible";
            
            $observacionServicioYSolicitud  .= "<b>Informaci&oacute;n de Backbone</b><br/>";
            $observacionServicioYSolicitud  .= "<b>Recursos:</b> ".ucfirst($strRecursos)."<br/>";
            
            $arrayPrefijosRed  = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                         ->findBy(array("elementoId"    => $objElementoPadre,
                                                                        "detalleNombre" => "PREFIJO_RED",
                                                                        "estado"        => "Activo"));
            if(empty($arrayPrefijosRed))
            {
                throw new \Exception("No Existen Prefijos de red para [ ".$objElementoPadre->getNombreElemento()." ]");
            }
            
            if($strRecursos==="existentes")
            {
                $strTituloRecursos    .= "<b>Mismos Recursos</b><br/>";
                
                //Se guarda la referencia de la Subred utilizada para Backbone
                $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                $objProducto, 
                                                                                "SUBRED_VSAT",
                                                                                $intIdSubred, 
                                                                                $strUsrCreacion);
                
                $objSubredBb  = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->find($intIdSubred);
                
                if(!is_object($objSubredBb))
                {
                    throw new \Exception("No Existen Subredes configurada en el Cliente para asignar a nuevo Servicio, notificar a Sistemas");
                }
            }
            elseif($strRecursos==="nuevos")
            {
                $strTituloRecursos   .= "<b>Nuevos Recursos</b><br/>";
                
                //-----------------------------------------------------------------------------
                //   Se asigna Red 1 para configuracion del Backbone ( pe con el Hub satelital )
                //-----------------------------------------------------------------------------
                $arrayParametrosSubred = array();
                $arrayParametrosSubred['tipoAccion'] = "asignar";
                $arrayParametrosSubred['uso']        = "DATOSVSAT";
                $arrayParametrosSubred['mascara']    = trim($strMascara);// mascaras /29 ... /24
                
                //solicitar subred libre para configuracion de Backbone ( PE - HUB )
                foreach($arrayPrefijosRed as $objPrefijoRed)
                {
                    $arrayParametrosSubred['subredPrefijo'] = "10.".trim($objPrefijoRed->getDetalleValor());
        
                    $arraySubred = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->provisioningSubred($arrayParametrosSubred);
                    
                    if(isset($arraySubred['subredId']) && $arraySubred['subredId']>0)
                    {
                        $intIdSubred  = $arraySubred['subredId'];
                        $objSubredBb  = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->find($arraySubred['subredId']);
                        
                        if(is_object($objSubredBb))
                        {
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                            $objProducto, 
                                                                                            "SUBRED_VSAT",
                                                                                            $intIdSubred, 
                                                                                            $strUsrCreacion);
                            $objSubredBb->setElementoId($objElementoPadre);
                            $this->emInfraestructura->persist($objSubredBb);
                            $this->emInfraestructura->flush();
                                                        
                        }
                        else
                        {
                            throw new \Exception("No Existen Subredes con máscara <b>".$strMascara."</b> disponibles, notificar a Sistemas");
                        }
                        
                        break;
                    }
                }
            }
            else
            {
                throw new \Exception("Opcion no válida para Asignar Recursos de Red");
            }
            
            //-----------------------------------------------------------------------------
            //   Se asigna Red 2 para configuracion del VSAT en el Cliente
            //-----------------------------------------------------------------------------

            $arrayParametrosSubred['tipoAccion'] = "asignar";
            $arrayParametrosSubred['uso']        = "DATOSVSAT";
            $arrayParametrosSubred['mascara']    = trim($strMascaraCliente);// mascaras /30

            foreach($arrayPrefijosRed as $objPrefijoRed)
            {
                $arrayParametrosSubred['subredPrefijo'] = "10.".trim($objPrefijoRed->getDetalleValor());

                $arraySubred = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->provisioningSubred($arrayParametrosSubred);

                if(isset($arraySubred['subredId']) && $arraySubred['subredId']>0)
                {
                    $intIdSubred        = $arraySubred['subredId'];
                    $objSubredServicio  = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->find($arraySubred['subredId']);

                    if(is_object($objSubredServicio))
                    {
                        $objSubredServicio->setElementoId($objElementoPadre);
                        $this->emInfraestructura->persist($objSubredServicio);
                        $this->emInfraestructura->flush();

                        //Ip a ser utilizada para configurar en el CPE VSAT
                        $ipServicio = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')->getIpDisponibleBySubred($intIdSubred);
                    }
                    else
                    {
                        throw new \Exception("No Existen Subredes con máscara <b>".$strMascara."</b> disponibles, notificar a Sistemas");
                    }                        

                    break;
                }
            }
            
            if($ipServicio=="NoDisponible")
            {
                throw new \Exception("No Existen Ips disponibles");
            }
            
            //se graba ip del servicio
            $objInfoIp = new InfoIp();
            $objInfoIp->setIp($ipServicio);
            $objInfoIp->setSubredId($intIdSubred);
            $objInfoIp->setServicioId($objServicio->getId());
            $objInfoIp->setMascara($strMascaraCliente);
            $objInfoIp->setFeCreacion(new \DateTime('now'));
            $objInfoIp->setUsrCreacion($strUsrCreacion);
            $objInfoIp->setIpCreacion($strIpCreacion);
            $objInfoIp->setEstado("Activo");
            $objInfoIp->setTipoIp("WAN");
            $objInfoIp->setVersionIp("IPV4");
            $this->emInfraestructura->persist($objInfoIp);
            $this->emInfraestructura->flush();
            
            //se graba caracteristica vlan
            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                           $objProducto, 
                                                                           "VLAN_PROVEEDOR",
                                                                           $intVlan, 
                                                                           $strUsrCreacion);
            //se graba caracteristica vrf
            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                           $objProducto, 
                                                                           "VRF", 
                                                                           $intVrf, 
                                                                           $strUsrCreacion);
            //se graba caracteristica protocolo
            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                           $objProducto, 
                                                                           "PROTOCOLO_ENRUTAMIENTO", 
                                                                           $strProtocolo, 
                                                                           $strUsrCreacion);
            $observacionServicioYSolicitud.="<b>Datos de Factibilidad</b><br/>";
            
            $observacionServicioYSolicitud.=$strTituloRecursos;
            $observacionServicioYSolicitud.="<b>Subred Asignada para configuración VSAT-Cliente: </b>".$objSubredServicio->getSubred()."<br/>";
            $observacionServicioYSolicitud.="<b>Subred Asignada para configuración Backbone-HUB: </b>".$objSubredBb->getSubred()."<br/>";
            $observacionServicioYSolicitud.="<b>IP Asignada para Cliente VSAT(Cpe): </b>".$ipServicio."<br/>";
            $observacionServicioYSolicitud.="<b>Vlan: </b>".$intVlan."<br/>";
            
            $objVrf = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->find($intVrf);
            
            $strNombreVrf="";
            
            if(is_object($objVrf))
            {
                $strNombreVrf=$objVrf->getValor();
            }
            $observacionServicioYSolicitud.="<b>Vrf: </b>".$strNombreVrf."<br/>";
            $observacionServicioYSolicitud.="<b>Protocolo: </b>".$strProtocolo."<br/>";
            $observacionServicioYSolicitud.="<b>Mascara de configuración Backbone-HUB: </b>".$strMascara."<br/>";
            $observacionServicioYSolicitud.="<b>Mascara de configuración VSAT-Cliente: </b>".$objSubredServicio->getMascara()."<br/>";
            
            //se graba caracteristica default gateway
            if($boolDefaultGateway=="true")
            {
                $strDefaultGateway = 'SI';
            }
            else
            {
                $strDefaultGateway = 'NO';
            }
            
            $objDefaultGateway = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 
                                                                                           "DEFAULT_GATEWAY", 
                                                                                           $objProducto);
            if($objDefaultGateway)
            {
                $objDefaultGateway->setValor($strDefaultGateway);
                $this->emComercial->persist($objDefaultGateway);
                $this->emComercial->flush();
            }
            else
            {
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                               $objProducto, 
                                                                               "DEFAULT_GATEWAY", 
                                                                               $strDefaultGateway, 
                                                                               $strUsrCreacion);
            }
            
            
            //se graba as privado 
            $objAsPrivado = $this->emComercial
                                 ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                 ->getOneByCaracteristica($personaEmpresaRolId,"AS_PRIVADO");
            
            $objPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($personaEmpresaRolId);
            
            if(!is_object($objAsPrivado) && $intAsPrivado>0)
            {
                $objCaracteristicaAsPrivado  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                 ->findOneBy(array("descripcionCaracteristica" =>"AS_PRIVADO", "estado" => "Activo"));
                
                $objInfoPersonaEmpresaRolCaracAsprivado = new InfoPersonaEmpresaRolCarac();
                $objInfoPersonaEmpresaRolCaracAsprivado->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                $objInfoPersonaEmpresaRolCaracAsprivado->setCaracteristicaId($objCaracteristicaAsPrivado);
                $objInfoPersonaEmpresaRolCaracAsprivado->setValor($intAsPrivado);
                $objInfoPersonaEmpresaRolCaracAsprivado->setEstado("Activo");
                $objInfoPersonaEmpresaRolCaracAsprivado->setFeCreacion(new \DateTime('now'));
                $objInfoPersonaEmpresaRolCaracAsprivado->setUsrCreacion($strUsrCreacion);
                $objInfoPersonaEmpresaRolCaracAsprivado->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objInfoPersonaEmpresaRolCaracAsprivado);
                $this->emComercial->flush();
            } 
            
            if($intAsPrivado>0)
            {
                $observacionServicioYSolicitud.="As privado: ".$intAsPrivado."<br/>";
            }
            
            if($strProtocolo=="BGP")
            {
                $observacionServicioYSolicitud.="Neighbor Default: ".$strDefaultGateway."<br/><br/>";
            }
            
            //se actualiza el estado del servicio
            $objServicio->setEstado("Asignada");
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();

            //agregar historial del servicio
            $objInfoHistorial = new InfoServicioHistorial();
            $objInfoHistorial->setServicioId($objServicio);
            $objInfoHistorial->setEstado("Asignada");
            $objInfoHistorial->setUsrCreacion($strUsrCreacion);
            $objInfoHistorial->setFeCreacion(new \DateTime('now'));
            $objInfoHistorial->setIpCreacion($strIpCreacion);
            $objInfoHistorial->setObservacion($observacionServicioYSolicitud);
            $this->emComercial->persist($objInfoHistorial);
            $this->emComercial->flush();                                            
            
            if($objDetalleSolicitud)
            {
                //se actualiza estado a la solicitud
                $objDetalleSolicitud->setEstado("Asignada");
                $this->emComercial->persist($objDetalleSolicitud);
                $this->emComercial->flush();

                //agregar historial a la solicitud
                $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
                $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
                $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitudHistorial->setUsrCreacion($strUsrCreacion);
                $objDetalleSolicitudHistorial->setEstado("Asignada");
                $objDetalleSolicitudHistorial->setObservacion($observacionServicioYSolicitud);
                $this->emComercial->persist($objDetalleSolicitudHistorial);
                $this->emComercial->flush();
            }
            else
            {
                throw new \Exception("No Existe Solicitud , favor comunicarse con Sistemas!");
            }
            
            //Generacion de Login Auxiliar al Servicio            
            $this->servicioGeneral->generarLoginAuxiliar($intIdServicio);
            
            $objPersona = $this->emComercial->getRepository("schemaBundle:InfoPersona")->find($objPersonaEmpresaRol->getPersonaId());
            
            $strLogin   = $objServicio->getPuntoId()->getLogin();
            
            //Generacion notificacion de subredes generadas para los departamentos pertinentes
            $arrayNotificacion                   = array();
            $arrayNotificacion['nombreTecnico']  = $objProducto->getNombreTecnico();
            $arrayNotificacion['cliente']        = $objPersona->getInformacionPersona();
            $arrayNotificacion['login']          = $strLogin;
            $arrayNotificacion['vlan']           = $intVlan;
            $arrayNotificacion['vrf']            = $strNombreVrf;
            $arrayNotificacion['protocolo']      = $strProtocolo;
            $arrayNotificacion['subredBb']       = $objSubredBb->getSubred();
            $arrayNotificacion['gatewayBb']      = $objSubredBb->getGateway();
            $arrayNotificacion['mascaraBb']      = $objSubredBb->getMascara();
            $arrayNotificacion['subredCliente']  = $objSubredServicio->getSubred();
            $arrayNotificacion['gatewayCliente'] = $objSubredServicio->getGateway();
            $arrayNotificacion['mascaraCliente'] = $objSubredServicio->getMascara();
            $arrayNotificacion['ipCliente']      = $ipServicio;
            
            $this->serviceEnvioPlantilla->generarEnvioPlantilla('Generación de recursos de Red VSAT para punto : '.$strLogin,
                                                                array(),
                                                                'VSAT_RECURSOS',
                                                                $arrayNotificacion,
                                                                $arrayParametros['empresaCod'],
                                                                null,null
                                                               );
            
            if($boolFlagTransaccion)
            {
            
                if ($this->emInfraestructura->getConnection()->isTransactionActive())
                {
                    $this->emInfraestructura->getConnection()->commit();
                    $this->emInfraestructura->getConnection()->close();
                }

                if ($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->getConnection()->commit();
                    $this->emComercial->getConnection()->close();
                }
            }
            
            $status         = "OK";
            $mensaje        = "OK";
        }
        catch (\Exception $e)
        {
            if($boolFlagTransaccion)
            {
            
                if ($this->emInfraestructura->getConnection()->isTransactionActive())
                {
                    $this->emInfraestructura->getConnection()->rollback();
                    $this->emInfraestructura->getConnection()->close();
                }
            
                if ($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->getConnection()->rollback();
                    $this->emComercial->getConnection()->close();
                }
            }
            
            $status         = "ERROR";
            $mensaje        = $e->getMessage();
        }
        
        $arrayRespuestaFinal = array('status'=>$status, 'mensaje'=>$mensaje);
        
        return $arrayRespuestaFinal; 
    }
    /**
    * Funcion que sirve para grabar los recursos de Red para el producto 
    * Internet MPLS
    * 
    * @author Allan Suarez <arsuarez@telconet.ec>
    * @version 1.5 16-12-2016  Se cambia caracteristica de ES_BACKUP a enlace BACKUP segun producto cambiado, si internet dedicado cambia
    *                          a InternetMPLS la caracteristica cambia segun el producto cambiado
    * 
    * @author Jesus Bozada <jbozada@telconet.ec>
    * @version 1.4 04-08-2016  Se realiza procesamiento para cambio de ultima milla Radio
    * 
    * @author Allan Suarez <arsuarez@telconet.ec>
    * @version 1.3 29-06-2016  Se realiza procesamiento para cambio de ultima milla
    * 
    * @author Jesus Bozada <jbozada@telconet.ec>
    * @version 1.2 23-05-2016  Se agrega parametros para soportar servicios con UM Radio
    * 
    * @author Allan Suarez <arsuarez@telconet.ec>
    * @version 1.1 9-05-2016 - Se agrega invocacion a metodo que genera login auxiliar al servicio
    * 
    * @author Juan Lafuente <jlafuente@telconet.ec>
    * @version 1.0 29-03-2016
    * @param array $arrayParametros [idServicio, idDetalleSolicitud, hiloSeleccionado, vlan, subred, tipoSubred, usrCreacion
    *                               ipCreacion, ]
    *
    * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.1 17-05-2016 - Se envían los parámetros numeroColorHiloSeleccionado,nombreElemento,nombreElementoContenedor para guardarlos en el
    *                           historial del servicio y de la solicitud
    * 
    * @author Allan Suarez <arsuarez@telconet.ec>
    * @version 1.2 20-04-2017 - Se ajusta para poder realizar la asignacion de recursos de red para esquema pseudope
    * 
    * @author Allan Suarez <arsuarez@telconet.ec>
    * @version 1.3 28-06-2017 - Se ajusta para poder realizar la asignacion de recursos de red para servicios SATELITALES y asignar una red para
    *                           VSAT de acuerdo a la mascara enviada
    *
    * @author David Leon <mdleon@telconet.ec>
    * @version 1.4 01-06-2019 - Se ingresa el nombre tecnico "INTERNET SDWAN" para que realice el flujo de Internet.
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.5 06-11-2019     Se agrega el concepto de tipo de red GPON
    *
    * @author David Leon <mdleon@telconet.ec>
    * @version 1.5 05-01-2020 - Se replica Ip wan para migración de servicios de productos Internet Sdwan con misma ultima milla.
    *
    * @author Felix Caicedo <facaicedo@telconet.ec>
    * @version 1.6 03-04-2020 - Se agregó la bandera de no eliminar el protocolo de enrutamiento
    *
    * @author Felix Caicedo <facaicedo@telconet.ec>
    * @version 1.7 06-04-2020 - Cuando el nombre técnico del servicio es 'INTERNET' o 'INTERNET SDWAN' y el anillo es mayor a cero
    *                           se guarda el id del producto anterior en la característica de la solicitud.
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.8 22-03-2021 - El producto solo cambia a Internet MPLS cuando es Internet Dedicado
    *
    * @author Felix Caicedo <facaicedo@telconet.ec>
    * @version 1.9 20-05-2021 - Se valida el tipo de red del servicio, si es GPON se debe cambiar el producto Internet Dedicado a Internet MPLS.
    *
    * @return array $respuestaFinal
    *
    * @author Jonathan Montece <jmontece@telconet.ec>
    * @version 2.0 12-08-2021 - Se añade variable login y se valida Ip con WS cuando el anillo sea igual a 0 (Provincias)
    *
    * @author Jean Pierre Nazareno Martinez <jnazareno@telconet.ec>
    * @version 2.1 10-12-2021 Se agrega lógica para guardar datos de Mascara y Gateway
    *
    * @author Joel Muñoz M <jrmunoz@telconet.ec>
    * @version 2.2 15-03-2023 Se agrega funcionalidad para guardar mismas IP's de servicio principal si es una migración SDWAN
    */
    public function asignarRecursosRedInternetMPLS($arrayParametros) 
    {
        $intIdServicio                  = $arrayParametros['idServicio'];
        $intIdDetalleSolicitud          = $arrayParametros['idDetalleSolicitud'];        
        $strNombreInterfaceElemento     = $arrayParametros['nombreInterfaceElemento'];
        $intIdElementoPadre             = $arrayParametros['idElementoPadre'];
        $intVrf                         = $arrayParametros['vrf'];
        $intVlan                        = $arrayParametros['vlan'];
        $strSubred                      = $arrayParametros['subred'];
        $strTipoSubred                  = $arrayParametros['tipoSubred'];
        $strTipoRed                     = $arrayParametros['strTipoRed']?$arrayParametros['strTipoRed']:"MPLS";
        $strUsrCreacion                 = $arrayParametros['usrCreacion'];
        $strIpCreacion                  = $arrayParametros['ipCreacion'];
        $numeroColorHiloSeleccionado    = $arrayParametros['numeroColorHiloSeleccionado'];
        $nombreElemento                 = $arrayParametros['nombreElemento'];
        $anillo                         = $arrayParametros['anillo'];
        $nombreElementoConector         = $arrayParametros['nombreElementoConector'];  
        $strTipoSolicitud               = $arrayParametros['tipoSolicitud'];  
        $strUltimaMilla                 = $arrayParametros['ultimaMilla'];
        $intHiloSeleccionado            = $arrayParametros['hiloSeleccionado'];
        $strLogin                       = $arrayParametros['login'];
        
        // ...
        $boolFlagTransaccion            = $arrayParametros['flagTransaccion'];
        $boolFlagServicio               = $arrayParametros['flagServicio'];
        $boolEsPseudoPe                 = $arrayParametros['esPseudoPe'];
        // ...
        $observacionServicioYSolicitud  = "";
        $boolEsCambioUM                 = false;
        $tipoFactibilidad               = "RUTA";
        $strMascara                     = "";
        $strGateway                     = "";
        $arrayIpServicioPrincipal       = array();
        
        // ==========================================
        //        DECLARACION DE TRANSACCIONES           
        // ==========================================
        
        if($boolFlagTransaccion)
        {
            $this->emComercial->getConnection()->beginTransaction();
            $this->emInfraestructura->getConnection()->beginTransaction();
        }
        
        if($strTipoSolicitud && $strTipoSolicitud == "SOLICITUD CAMBIO ULTIMA MILLA")
        {
            $boolEsCambioUM = true;
        }
        
        try
        {                                     
            $objServicio                    = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            $objServicioTecnico             = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                ->findOneBy(array( "servicioId" => $objServicio->getId()));
            $objDetalleSolicitud            = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                ->findOneById($intIdDetalleSolicitud);            
            $objProducto                    = $objServicio->getProductoId();

            //se verifica si el servicio es tipo de red GPON
            $booleanTipoRedGpon = false;
            $arrayParVerTipoRed = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                    'COMERCIAL',
                                                                                                    '',
                                                                                                    'VERIFICAR TIPO RED',
                                                                                                    'VERIFICAR_GPON',
                                                                                                    $strTipoRed,
                                                                                                    '',
                                                                                                    '',
                                                                                                    '');
            if(isset($arrayParVerTipoRed) && !empty($arrayParVerTipoRed))
            {
                $booleanTipoRedGpon = true;
            }
            //Obtener la caracteristica TIPO_FACTIBILIDAD para discriminar que sea FIBRA DIRECTA o RUTA
            $objServProdCaractTipoFact = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,'TIPO_FACTIBILIDAD',$objProducto);                        
            
            if($objServProdCaractTipoFact)
            {
                $tipoFactibilidad = $objServProdCaractTipoFact->getValor();
            }
            else
            {
                if($strUltimaMilla == "Fibra Optica" || ($strUltimaMilla == 'FTTx' && $booleanTipoRedGpon) )
                {
                    $tipoFactibilidad = "RUTA";
                }
                else
                {
                    $tipoFactibilidad = "DIRECTO";
                }
            }                       
            
            //Se verifica que el cambio de recursos de red sea de Cambio de Ultima Milla
            if($boolEsCambioUM)            
            {
                $arrayParametros['booleanProtoEnru'] = false;
                $arrayParametros['tipoFactibilidad'] = $tipoFactibilidad;
                //se agrega validacion para ejecutar el proceso correspondiente segun la ultima milla del servicio
                if ($strUltimaMilla == "Radio")
                {
                    $this->asignacionRecursosRedUMRadio($arrayParametros);
                }
                else
                {
                    $this->asignacionRecursosRedUM($arrayParametros);
                }
                
            }

            //...
            // ===========================================================================================
            // Validacion del switch, si pertenece a un anillos se cambia a producto Internet MPLS
            // ===========================================================================================
            if($objProducto->getNombreTecnico() == 'INTERNET' || $objProducto->getNombreTecnico() == 'INTERNET SDWAN')
            {
                $strAnillo = '';
                
                if($strUltimaMilla == 'SATELITAL')
                {
                    $arrayParametrosDet =   $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne("VLANS_PERMITIDAS_VSAT", 
                                                                     "TECNICO", 
                                                                     "", 
                                                                     'VLANS INTERNET MPLS', 
                                                                     "", 
                                                                     "", 
                                                                     "",
                                                                     "",
                                                                     "",
                                                                     $arrayParametros['empresaCod']
                                                                   );
                    if(!empty($arrayParametrosDet))
                    {
                        $strAnillo = $arrayParametrosDet['valor3'];
                    }
                }
                else
                {
                    $objElemento = $objServicioTecnico->getElementoId();
            
                    $objDetalleElementoAnillo = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                        ->findONeBy(array("elementoId"    => $objElemento,
                                                                                          "detalleNombre" => "ANILLO",
                                                                                          "estado"        => "Activo"));
                    if(is_object($objDetalleElementoAnillo))
                    {
                        $strAnillo = $objDetalleElementoAnillo->getDetalleValor();
                    }
                }
                
                // Se valida que el anillo asignacion sea mayor a 0
                if( (!empty($strAnillo) && intval($strAnillo) > 0) || $booleanTipoRedGpon )
                {
                    //se guarda el id del producto anterior del servicio
                    $objAdmiCaractProducto  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array("descripcionCaracteristica" =>"PRODUCTO_ID",
                                                                      "estado" => "Activo"));
                    if( is_object($objDetalleSolicitud) && is_object($objAdmiCaractProducto) )
                    {
                        //grabar el id del Producto en el detalle de la solicitud
                        $arrayParametrosTecProductoId   = array(
                                                                'objDetalleSolicitudId' => $objDetalleSolicitud,
                                                                'objCaracteristica'     => $objAdmiCaractProducto,
                                                                'estado'                => 'Asignada',
                                                                'valor'                 => $objProducto->getId(),
                                                                'usrCreacion'           => $strUsrCreacion
                                                            );
                        $this->container->get('tecnico.InfoServicioTecnico')->insertarInfoDetalleSolCaract($arrayParametrosTecProductoId);
                    }
                    // Se cambia a producto Internet MPLS solo para internet dedicado
                    $objProductoINTMPLS = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                            ->findOneBy(array("nombreTecnico" => "INTMPLS",
                                                                              "empresaCod"    => '10',
                                                                              "estado"        => "Inactivo"));

                    //Se obtiene la caracteristica de BACKUP del Servicio antes de ser cambiado a Internet MPLS
                    $objServicioProductoCaract = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                           "ES_BACKUP",
                                                                                                           $objProducto
                                                                                                          );

                    if($objProducto->getNombreTecnico() == 'INTERNET')
                    {
                        $objServicio->setProductoId($objProductoINTMPLS);
                    }
                    else
                    {
                        $objServicio->setProductoId($objProducto);
                    }

                    $this->emComercial->persist($objServicio);
                    $this->emComercial->flush();
                    
                    //Si el servicio backup tiene relacionado su principal y es internet dedicado realiza el cambio segun el producto
                    //internet MPLS en sus caracteristicas
                    
                    if($objServicioTecnico->getTipoEnlace() == 'BACKUP' && is_object($objServicioProductoCaract))
                    {
                        $objCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                               ->findOneBy(array( "descripcionCaracteristica"  => 'ES_BACKUP',
                                                                                  "estado"                     => "Activo"));
                        if(is_object($objCaracteristica))
                        {
                            if($objProducto->getNombreTecnico() == 'INTERNET SDWAN')
                            {
                                $objProductoINTMPLS = $objProducto;
                            }

                            $objProductoCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                              ->findOneBy(array('caracteristicaId' => $objCaracteristica->getId(),
                                                                                'productoId'       => $objProductoINTMPLS->getId(),
                                                                                'estado'           => 'Activo'));
                            if(is_object($objProductoCaracteristica))
                            {
                                //Se cambia la relacion de BACKUP de producto Internet Dedicado a Internet MPLS
                                $objServicioProductoCaract->setProductoCaracterisiticaId($objProductoCaracteristica->getId());
                                $this->emComercial->persist($objServicioProductoCaract);
                                $this->emComercial->flush();
                            }
                        }
                    }
                    //...
                    if($objProducto->getNombreTecnico() == 'INTERNET')
                    {
                        $objProducto = $objProductoINTMPLS;
                    }
                    //...
                    $arrayServicioProductoCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                     ->findBy(array("servicioId" => $objServicio->getId(), 
                                                                                    "estado"     => 'Activo'));
                    if($arrayServicioProductoCaract)
                    {
                        foreach ($arrayServicioProductoCaract as $spc) {
                            $prodCaract = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                               ->find($spc->getProductoCaracterisiticaId());

                            $prodCaractNuevo = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                    ->findOneBy(array("productoId"       => $objProducto->getId(), 
                                                                      "caracteristicaId" => $prodCaract->getCaracteristicaId()->getId(), 
                                                                      "estado"           => "Activo"));
                            $spc->setProductoCaracterisiticaId($prodCaractNuevo->getId());
                            $this->emComercial->persist($spc);
                            $this->emComercial->flush();
                        }
                    }
                }
            }

            // ===========================================================================================
            // Se asigna la ip al servicio basado en la subred seleccionda
            // ===========================================================================================
            
            //Si la Ultima Milla es SATELITAL se obtiene una subred del arbol dada las mascara escogida
            if($strUltimaMilla == 'SATELITAL')
            {
                $objElementoPadre = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoPadre);
                
                if(!is_object($objElementoPadre))
                {
                    throw new \Exception("No Existen Referencia a Elemento Router");
                }
                
                //-----------------------------------------------------------------------------
                //   Se asigna Red 1 para configuracion del Backbone ( pe con el Hub satelital )
                //-----------------------------------------------------------------------------
                $arrayParametrosSubred                  = array();
                $arrayParametrosSubred['tipoAccion']    = "asignar";
                $arrayParametrosSubred['uso']           = "INTMPLSVSAT";
                $arrayParametrosSubred['mascara']       = trim($strSubred);// mascaras /29 ... /30
                $arrayParametrosSubred['elementoId']    = $objElementoPadre->getId();
                $arrayParametrosSubred['subredPrefijo'] = null;

                $arraySubred = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->provisioningSubred($arrayParametrosSubred);

                if(isset($arraySubred['subredId']) && $arraySubred['subredId']>0)
                {
                    $strSubred    = $arraySubred['subredId'];
                    $objSubredBb  = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->find($strSubred);

                    if(is_object($objSubredBb))
                    {
                        $objSubredBb->setElementoId($objElementoPadre);
                        $this->emInfraestructura->persist($objSubredBb);
                        $this->emInfraestructura->flush();

                    }
                    else
                    {
                        throw new \Exception("No Existen Subredes con máscara <b>".$arrayParametrosSubred['mascara']."</b> disponibles, "
                                             . "notificar a Sistemas");
                    }
                }
                else
                {
                    throw new \Exception("No Existen Subredes con máscara <b>".$arrayParametrosSubred['mascara']."</b> disponibles, "
                                             . "notificar a Sistemas");
                }
                
            }
            
            if (is_object($objServicio) && !empty($objServicio))
            {
                $objServProdCaractSDWAN     = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                        'SDWAN',
                                                                                                        $objServicio->getProductoId()
                                                                                                        );
                $objServProdReferencia      = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 
                                                                                                                "SERVICIO_MISMA_ULTIMA_MILLA", 
                                                                                                                $objServicio->getProductoId());
                
            }
            // SI ES MIGRACIÓN SDWAN CONSERVA LA MISMA IP DEL SERVICIO PRINCIPAL
            if(!empty($arrayParametros['esMigracionSDWAN']) && $arrayParametros['esMigracionSDWAN'] === 'SI')
            {
                $objServPrincipalSDWAN = $this->serviceInfoServicioTecnico->getServicioProductoCaracteristica(
                    $objServicio,
                    "SERVICIO_MIGRADO_SDWAN",
                    $objServicio->getProductoId()
                );

                if(is_object($objServPrincipalSDWAN))
                {
                    $arrayIpServicioPrincipal = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                            ->createQueryBuilder('ip')
                                            ->where('ip.servicioId = :idServicioPrincipal')
                                            ->andWhere("ip.estado in (:estadoActivo)")
                                            ->orderBy('ip.tipoIp', 'DESC')
                                            ->setParameter('idServicioPrincipal', $objServPrincipalSDWAN->getValor())
                                            ->setParameter('estadoActivo', array('Activo', 'Reservada'))
                                            ->getQuery()
                                            ->getResult();

                    if(!empty($arrayIpServicioPrincipal) 
                    && is_array($arrayIpServicioPrincipal) 
                    && count($arrayIpServicioPrincipal)>0)
                    {
                        $arrayIpServicioPrincipalAux = $arrayIpServicioPrincipal;
                        $strIp = array_shift($arrayIpServicioPrincipalAux)->getIp();
                    }

                    if(empty($strIp))
                    {
                        throw new \Exception("No Existe IP Asociada a un servicio Internet Mpls, favor comunicarse con Sistemas!");
                    }
                }
            }
            else if(is_object($objServProdCaractSDWAN) && !empty($objServProdCaractSDWAN) && $intHiloSeleccionado!="" && $intHiloSeleccionado!=0 && 
                !is_object($objElemento) && is_object($objServProdReferencia) && !empty($objServProdReferencia))
            {
                $arrayParametrosElemento                        = array();
                $arrayParametrosElemento['elementoId']          = $objElemento;
                $arrayParametrosElemento['interfaceElemento']   = $intHiloSeleccionado;
                $arrayParametrosElemento['servicioId']          = $objServProdReferencia->getValor();
                $arrayServicioTecnico = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                                        ->getElementosPorUltimaMilla($arrayParametrosElemento);
                
                if(!empty($arrayServicioTecnico))
                {
                    $arrayConsultaIp                            = array();
                    $arrayConsultaIp['idServicio']              = $arrayServicioTecnico[0]['servicioId'];
                    $arrayConsultaIp['arrayEstados']            = 'Activo';
                    
                    $arrayIpServicio = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                     ->getIpsPorServicioPorEstados($arrayConsultaIp);
                    
                    if(!empty($arrayIpServicio))
                    {
                        if($arrayIpServicio[0]->getSubredId()==$strSubred)
                        {
                            $strIp  =   $arrayIpServicio[0]->getIp();
                        }
                        else
                        {
                            throw new \Exception("La Subred selecionada no es la correcta, favor verificar");
                        }
                        
                    }
                    else
                    {
                        throw new \Exception("No Existe IP Asociada a un servicio Internet Mpls, favor comunicarse con Sistemas!");
                    }
                }
            }
            if(empty($strIp))
            {
                $strIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                             ->getIpDisponibleBySubred($strSubred);
                
            }
            //Validar Ip con WS cuando el anillo sea igual a 0 (Provincias)
               $strConfirmarProvincias = false;
               $strConfirmarGyeUio = false;
               $strLlamarWs = "N";
               $strNumeroTarea            = "";
               $serviceCambiarPlanService = $this->container->get('tecnico.InfoCambiarPlan');
                $arrayLlamadoProvincias      = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne('CONFIGURACION_WS_PROVINCIAS',//nombre parametro cab
                                                                  'TECNICO', //modulo cab
                                                                  'SUBREDES_PE',//proceso cab
                                                                  'LLAMAR_WS_PROVINCIAS', //descripcion det
                                                                  '','','','','',
                                                                  '10'); //empresa
                $arrayLlamadoGyeUio          = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne('CONFIGURACION_WS_PROVINCIAS',//nombre parametro cab
                                                                  'TECNICO', //modulo cab
                                                                  'SUBREDES_PE',//proceso cab
                                                                  'LLAMAR_WS_GYE_UIO', //descripcion det
                                                                  '','','','','',
                                                                  '10'); //empresa
                if( isset($arrayLlamadoProvincias['valor1']) && !empty($arrayLlamadoProvincias['valor1']) )
                    {
                        $strConfirmarProvincias = $arrayLlamadoProvincias['valor1'];
                    }
                if( isset($arrayLlamadoGyeUio['valor1']) && !empty($arrayLlamadoGyeUio['valor1']) )
                    {
                        $strConfirmarGyeUio = $arrayLlamadoGyeUio['valor1'];
                    }
            
            if($strAnillo > 0 && $strIp != 'NoDisponible' && $strConfirmarGyeUio == "S")
            {
                $strLlamarWs = "S";    
            }
            if($strAnillo == "0" && $strIp != 'NoDisponible' && $strConfirmarProvincias == "S")
            {
                $strLlamarWs = "S";
            }

            if(!empty($arrayParametros['esMigracionSDWAN']) && $arrayParametros['esMigracionSDWAN'] === 'SI')
            {
                $strLlamarWs = "N";
            }

            if($strLlamarWs == "S")
            {
               
                   
                    $objInfoElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                        ->findOneById($intIdElementoPadre);
                    if(!is_object($objInfoElemento))
                    {
                        throw new \Exception("No es posible encontrar el nombre del elemento padre, favor comunicarse con Sistemas!");
                    }

                    $arrayParams['strSubred']           =   $strIp;
                    $arrayParams['strElementoPadre']    =   $objInfoElemento->getNombreElemento();
                    $arrayParams['strServicio']         =   $strTipoRed;
                    $arrayParams['strLogin']            =   $strLogin;
                    $arrayParams['strUsr']              =   $strUsrCreacion;
                    $arrayParams['strClientIp']         =   $strIpCreacion;
                    
                
                    //LOGICA DE VALIDAR IP CON WS

                    $arrayRespuestaWs   =   $this->validacionIpAsignadaProvinciasWs($arrayParams);
                    if($arrayRespuestaWs['mensaje'] == '400')
                    {
                        $strConfirmacion = '¿Desea volver a generar otra ip?';
                        $arrayRespuestaFinal = array('status'=> 'ERROR', 'mensaje'=>$arrayRespuestaWs['msgWs'], 
                                                        'statusWebService'=>$arrayRespuestaWs['statusWs'], 
                                                        'confirmacionWs'=>$strConfirmacion);

                        $objSubred  = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->find($strSubred);

                        if(is_object($objSubred))
                        {
                            $strMascara = $objSubred->getMascara();
                            $strGateway = $objSubred->getGateway();
                        }

                        $objInfoIp = new InfoIp();
                        $objInfoIp->setIp($strIp);
                        $objInfoIp->setEstado("Activo");
                        $objInfoIp->setSubredId($strSubred);
                        $objInfoIp->setMascara($strMascara);
                        $objInfoIp->setGateway($strGateway);
                        $objInfoIp->setTipoIp($strTipoSubred);
                        $objInfoIp->setVersionIp("IPV4");
                        $objInfoIp->setUsrCreacion($strUsrCreacion);
                        $objInfoIp->setFeCreacion(new \DateTime('now'));
                        $objInfoIp->setIpCreacion($strIpCreacion);
                        $this->emInfraestructura->persist($objInfoIp);
                        $this->emInfraestructura->flush();  
                        if ($this->emInfraestructura->getConnection()->isTransactionActive())
                        {
                            $this->emInfraestructura->getConnection()->commit();
                            $this->emInfraestructura->getConnection()->close();
                        }
                        //*****************Se consultan los mensajes configurados para disparar la tarea a L2*************//
                        
                        $arrayValoresMensajes = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->getOne('CONFIGURACION_WS_PROVINCIAS',//nombre parametro cab
                                                                        'TECNICO', //modulo cab
                                                                        'SUBREDES_PE',//proceso cab
                                                                        'NOMBRE_TAREA_L2', //descripcion det
                                                                        '','','','','',
                                                                        '10'); //empresa
                    if(!empty($arrayValoresMensajes))
                    {
                        //*************************Generacion de Tarea Automatica***************************//
                        //1ero.- Se obtiene el nombre de la tarea
                        $arrayValoresParametros = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->getOne('CONFIGURACION_WS_PROVINCIAS',//nombre parametro cab
                                                                        'TECNICO', //modulo cab
                                                                        'SUBREDES_PE',//proceso cab
                                                                        'NOMBRE_TAREA_L2', //descripcion det
                                                                        '','','','','',
                                                                        '10'); //empresa

                        if(isset($arrayValoresParametros["valor1"]) && !empty($arrayValoresParametros["valor1"]))
                        {
                            $strNombreTarea = $arrayValoresParametros["valor1"];
                           


                            if(!empty($strNombreTarea))
                            {
                                $objAdmiTarea = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')
                                                        ->findOneBy(array("nombreTarea" => $strNombreTarea,
                                                                            "estado"      => "Activo"));

                                if(is_object($objAdmiTarea))
                                {
                                    $strTareaId = $objAdmiTarea->getId();
                                }
                            }
                        }

                        //2do.- Se obtiene el punto del servicio
                        $intIdServicio = $objServicio->getId();
                        if(!empty($intIdServicio))
                        {
                            $objInfoServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
                        }

                        if(is_object($objInfoServicio))
                        {
                            $objInfoPunto = $objInfoServicio->getPuntoId();
                        }

                        //3ero.- Se obtiene departamento a asignar
                        $arrayValoresParametros = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('CONFIGURACION_WS_PROVINCIAS',//nombre parametro cab
                                                                            'TECNICO', //modulo cab
                                                                            'SUBREDES_PE',//proceso cab
                                                                            'DEPARTAMENTO_TAREA_RECURSOS_DE_RED', //descripcion det
                                                                            '','','','','',
                                                                            '10'); //empresa

                        if(!empty($arrayValoresParametros["valor1"]))
                        {
                            $strDepartamentoId = $arrayValoresParametros["valor1"];
                            

                        }

                        if(!empty($strDepartamentoId))
                        {
                            $objAdmiDepartamento = $this->emGeneral->getRepository('schemaBundle:AdmiDepartamento')->find($strDepartamentoId);
                        }

                        //4to.- Se obtiene la region del servicio
                        $arrayParametrosRegion["intServicioId"] = $intIdServicio;

                        $strRegionServicio = '';
                        //verifico el punto
                        $objInfoPunto = $objServicio->getPuntoId();
                        if(is_object($objInfoPunto))
                        {
                            //verifico el sector
                            $objSector = $objInfoPunto->getSectorId();
                            if(is_object($objSector))
                            {
                                //verifico la parroquia
                                $objParroquia = $objSector->getParroquiaId();
                                if(is_object($objParroquia))
                                {
                                    //verifico el canton
                                    $objCanton = $objParroquia->getCantonId();
                                    if(is_object($objCanton))
                                    {
                                        $strRegionServicio = $objCanton->getRegion();
                                    }
                                }
                            }
                        }

                        $arrayValoresMensaje = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('CONFIGURACION_WS_PROVINCIAS',//nombre parametro cab
                                                                            'TECNICO', //modulo cab
                                                                            'SUBREDES_PE',//proceso cab
                                                                            'MENSAJE_IP_OCUPADA_UNO', //descripcion det
                                                                            '','','','','',
                                                                            '10'); //empresa

                        if(!empty($arrayValoresMensaje["valor1"]))
                        {
                            $strCadenaUno = $arrayValoresMensaje["valor1"];                            

                        }
                        $arrayValoresMensajeDos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('CONFIGURACION_WS_PROVINCIAS',//nombre parametro cab
                                                                            'TECNICO', //modulo cab
                                                                            'SUBREDES_PE',//proceso cab
                                                                            'MENSAJE_IP_OCUPADA_DOS', //descripcion det
                                                                            '','','','','',
                                                                            '10'); //empresa

                        if(!empty($arrayValoresMensajeDos["valor1"]))
                        {
                            $strCadenaDos = $arrayValoresMensajeDos["valor1"];                            

                        }
                        
                        $arrayParametros["strObservacion"]         = $strCadenaUno." ".$strIp." ".$strCadenaDos;
                        $arrayParametros["intTarea"]               = $strTareaId;
                        $arrayParametros["strTipoAfectado"]        = "Cliente";
                        $arrayParametros["objDepartamento"]        = $objAdmiDepartamento;
                        $arrayParametros["strEmpresaCod"]          = "10";
                        $arrayParametros["strUsrCreacion"]         = $strUsrCreacion;
                        $arrayParametros["strIpCreacion"]          = $strIpCreacion;
                        $arrayParametros["intDetalleSolId"]        = null;
                        $arrayParametros["strBanderaTraslado"]     = "S";
                        $arrayParametros["strRegion"]              = $strRegionServicio;

                        $strNumeroTarea = $serviceCambiarPlanService->crearTareaRetiroEquipoPorDemo($arrayParametros);
                        //*************************Generacion de Tarea Automatica***************************//
                    }
                        return $arrayRespuestaFinal;
                    }
                    else if($arrayRespuestaWs['mensaje'] != 'OK')
                    {
                        throw new \Exception($arrayRespuestaWs['mensaje']); 
                    
                    }
                    

                
            }
            
           
            if($strIp === 'NoDisponible')
            {
                throw new \Exception("No Existe IP Disponible, favor comunicarse con Sistemas!");
            }
            
            $objSubred  = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->find($strSubred);

            if(is_object($objSubred))
            {
                $strMascara = $objSubred->getMascara();
                $strGateway = $objSubred->getGateway();
            }

            if(!empty($arrayParametros['esMigracionSDWAN']) && $arrayParametros['esMigracionSDWAN'] === 'SI')
            {
                // SE GUARDAN LAS MISMAS IP AL SERVICIO SDWAN
                foreach($arrayIpServicioPrincipal as $objIpServicio)
                {
                    // SE CLONAN IP's DE SERVICIO PRINCIPAL A SDWAN
                    $objInfoIp = new InfoIp();
                    $objInfoIp->setIp($objIpServicio->getIp());
                    $objInfoIp->setServicioId($objServicio->getId());
                    $objInfoIp->setEstado($objIpServicio->getEstado());
                    $objInfoIp->setSubredId($objIpServicio->getSubredId());
                    $objInfoIp->setMascara($objIpServicio->getMascara());
                    $objInfoIp->setGateway($objIpServicio->getGateway());
                    $objInfoIp->setTipoIp($objIpServicio->getTipoIp());
                    $objInfoIp->setVersionIp($objIpServicio->getVersionIp());
                    $objInfoIp->setUsrCreacion($strUsrCreacion);
                    $objInfoIp->setFeCreacion(new \DateTime('now'));
                    $objInfoIp->setIpCreacion($strIpCreacion);
                    $this->emInfraestructura->persist($objInfoIp);
                    $this->emInfraestructura->flush();  
                }
            }
            else
            {
                // Se Almacena la IP Disponible para la 
                $objInfoIp = new InfoIp();
                $objInfoIp->setIp($strIp);
                $objInfoIp->setServicioId($objServicio->getId());
                $objInfoIp->setEstado("Activo");
                $objInfoIp->setSubredId($strSubred);
                $objInfoIp->setMascara($strMascara);
                $objInfoIp->setGateway($strGateway);
                $objInfoIp->setTipoIp($strTipoSubred);
                $objInfoIp->setVersionIp("IPV4");
                $objInfoIp->setUsrCreacion($strUsrCreacion);
                $objInfoIp->setFeCreacion(new \DateTime('now'));
                $objInfoIp->setIpCreacion($strIpCreacion);
                $this->emInfraestructura->persist($objInfoIp);
                $this->emInfraestructura->flush();  
            }

         
            
            //se graba caracteristica del ServicioProductoCaracteristica
            /* @var $servicioTecnicoService InfoServicioTecnico */
            $servicioTecnicoService = $this->container->get('tecnico.InfoServicioTecnico');
            
            // Se obtiene el valor del DetalleElemento de la VLAN
            //obtener info detalle elemento
            if(!$boolEsPseudoPe)
            {
                $objDetalleElementoVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                  ->findOneBy(array( "elementoId"     => $intIdElementoPadre,
                                                                                     "detalleValor"   => $intVlan,
                                                                                     "detalleNombre"  => "VLAN"));
                // Se graba caracteristica vlan
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "VLAN", 
                                                                           $objDetalleElementoVlan->getId(), $strUsrCreacion);
            }
            else
            {
                // Se graba caracteristica vlan
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                               $objProducto, 
                                                                               "VLAN_PROVEEDOR", 
                                                                               $intVlan, 
                                                                               $strUsrCreacion);
            }
            
            // Se graba caracteristica VRF
            $servicioTecnicoService->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "VRF", $intVrf, $strUsrCreacion);
            
            $observacionServicioYSolicitud.="<b>Informaci&oacute;n t&eacute;cnica asignada por la factibilidad</b><br/>";
            
            $objVrf = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                             ->find($intVrf);
            $nombreVrf="";
            if($objVrf)
            {
                $nombreVrf=$objVrf->getValor();
            }

            $objSubred = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->find($strSubred);
            
            $objElementoPadre = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoPadre);
            $observacionServicioYSolicitud.="<b>Elemento Padre</b> <br/>";
            $observacionServicioYSolicitud.="<b>Nombre:</b> ".$objElementoPadre->getNombreElemento()."<br/>";

            if(!$booleanTipoRedGpon)
            {
                $observacionServicioYSolicitud.="<b>Anillo:</b> ".$anillo."<br/>";
            }

            $observacionServicioYSolicitud.="<b>Vlan:</b> ".$intVlan."<br/>";
            $observacionServicioYSolicitud.="<b>Vrf:</b> ".$nombreVrf."<br/><br/>";

            $observacionServicioYSolicitud.="<b>Elemento</b> <br/>";
            $observacionServicioYSolicitud.="<b>Nombre:</b> ".$nombreElemento."<br/>";
            $observacionServicioYSolicitud.="<b>Interface:</b> ".$strNombreInterfaceElemento."<br/><br/>";
            
            if(!$boolEsPseudoPe)
            {
                $observacionServicioYSolicitud.="<b>Elemento Conector</b> <br/>";
                $observacionServicioYSolicitud.="<b>Nombre:</b> ".$nombreElementoConector."<br/>";

                if(!$booleanTipoRedGpon)
                {
                    $observacionServicioYSolicitud.="<b>Hilo:</b> ".$numeroColorHiloSeleccionado."<br/><br/>";
                }
                else
                {
                    $observacionServicioYSolicitud.="<b>Interface:</b> ".$numeroColorHiloSeleccionado."<br/><br/>";
                }
            }
            
            $observacionServicioYSolicitud.="<b>Asignaci&oacute;n de Subred</b> <br/>";
            $observacionServicioYSolicitud.="<b>Tipo de Subred:</b> ".$strTipoSubred."<br/>";
            $observacionServicioYSolicitud.="<b>Subred:</b> ".$objSubred->getSubred()."<br/>";
            $observacionServicioYSolicitud.="<b>IP:</b> ".$strIp."<br/><br/>";
            
            if($boolFlagServicio && !$boolEsCambioUM)
            {
                //se actualiza el estado del servicio
                $objServicio->setEstado("Asignada");
                $this->emComercial->persist($objServicio);
                $this->emComercial->flush();

                //agregar historial del servicio
                $objInfoHistorial = new InfoServicioHistorial();
                $objInfoHistorial->setServicioId($objServicio);
                $objInfoHistorial->setEstado("Asignada");
                $objInfoHistorial->setUsrCreacion($strUsrCreacion);
                $objInfoHistorial->setFeCreacion(new \DateTime('now'));
                $objInfoHistorial->setIpCreacion($strIpCreacion);
                $objInfoHistorial->setObservacion($observacionServicioYSolicitud);
                $this->emComercial->persist($objInfoHistorial);
                $this->emComercial->flush();    

                //Generacion de Login Auxiliar al Servicio            
                $this->servicioGeneral->generarLoginAuxiliar($intIdServicio);                    
            }

            if($objDetalleSolicitud)
            {
                //se actualiza estado a la solicitud
                $objDetalleSolicitud->setEstado("Asignada");
                $this->emComercial->persist($objDetalleSolicitud);
                $this->emComercial->flush();

                //agregar historial a la solicitud
                $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
                $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
                $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitudHistorial->setUsrCreacion($strUsrCreacion);
                $objDetalleSolicitudHistorial->setEstado("Asignada");
                $objDetalleSolicitudHistorial->setObservacion($observacionServicioYSolicitud);
                $this->emComercial->persist($objDetalleSolicitudHistorial);
                $this->emComercial->flush();
            }
            else
            {
                throw new \Exception("No Existe Solicitud de Planificación, favor comunicarse con Sistemas!");
            }
                        
            $status         = "OK";
            $mensaje        = "OK";
        }
        catch (\Exception $e)
        {            
            if($boolFlagTransaccion)
            {
            
                if ($this->emInfraestructura->getConnection()->isTransactionActive())
                {
                    $this->emInfraestructura->getConnection()->rollback();
                    $this->emInfraestructura->getConnection()->close();
                }
            
                if ($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->getConnection()->rollback();
                    $this->emComercial->getConnection()->close();
                }
            }
                     
            $status         = "ERROR";
            $mensaje        = "Se presentaron inconvenientes: <br> ".$e->getMessage();
        }
        
        //*DECLARACION DE COMMITS*/        
        if($boolFlagTransaccion)
        {
        
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->commit();
                $this->emInfraestructura->getConnection()->close();
            }

            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->commit();
                $this->emComercial->getConnection()->close();
            }
        }
        
        //*----------------------------------------------------------------------*/
        
        //*RESPUESTA-------------------------------------------------------------*/
        $respuestaFinal = array('status'=>$status, 'mensaje'=>$mensaje);
        return $respuestaFinal;
        //*----------------------------------------------------------------------*/
    }
    
    /**
     * 
     * Metodo utilizado para asignacion de recursos de Red para Cambio de Ultima Milla, para intercambiar informacion Tecnica para ser usado
     * al momento de la Activacion Tecnica
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 29-06-2016
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1
     * @since 16-09-2016    Se corrige variable global de servicio UtilService en proceso de asignación de recursos de red UM
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2
     * @since 08-10-2016    Se realiza cambio para que soporte escenario de servicios migrados sin data GIS que son tomados como RUTA en la
     *                      factibilidad
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.3 26-10-2016  Se valido objeto antes de actualizarlo
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.4
     * @since 26-10-2016    Se agrega validación para verificación de enlaces existentes en servicios 
     *                      previa creación de nuevo enlace entre interface de elemento de backbone y 
     *                      elemento de cliente
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.5
     * @since 12-08-2021    Se agrega validación para asignación de recursos de red cuando se realiza un
     *                      cambio de última milla en una misma ruta
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.6
     * @since 18-08-2021    Se corrige validación para asignación de recursos de red cuando se realiza un
     *                      cambio de última milla en una misma ruta
     * 
     * @param Array $arrayParametros [ usrCreacion , idDetalleSolicitud , idServicio , ipCreacion , ultimaMilla ]
     * @throws \Exception
     */
    public function asignacionRecursosRedUM($arrayParametros)
    {
        $boolEsFibraRuta = false;
        $intIdInterfaceEnlaceElementoClienteNuevo    = null;
        $intIdInterfaceEnlaceElementoClienteAnterior = null;
        
        try
        {                                                
            $strUsrCreacion = $arrayParametros['usrCreacion'];
            $intIdSolicitud = $arrayParametros['idDetalleSolicitud'];
            $intIdServicio  = $arrayParametros['idServicio'];
            $strIpCreacion  = $arrayParametros['ipCreacion'];
            $strUltimaMilla = $arrayParametros['ultimaMilla'];
            $empresaCod     = $arrayParametros['empresaCod'];
            $tipoFact       = $arrayParametros['tipoFactibilidad'];
            
            $arrParametros = array('emInfraestructura' => $this->emInfraestructura, 
                                   'emComercial'       => $this->emComercial,
                                   'idSolicitud'       => $intIdSolicitud,  
                                   'idServicio'        => $intIdServicio,
                                   'ultimaMilla'       => $strUltimaMilla,
                                   'tipoUM'            => $tipoFact
                                  );
            
            $arrInfoFactibilidadUM  = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                              ->getDatosFactibilidadUltimaMilla($arrParametros);
            
            if($strUltimaMilla == "Fibra Optica")
            {       
                if($tipoFact == 'RUTA')
                {
                    $boolEsFibraRuta = true;
                }                                                                 
            }

            if($arrInfoFactibilidadUM['status'] == 'OK')
            {
                $arrInfoFactibilidadUM = $arrInfoFactibilidadUM['data'];
                //Se obtiene la informacion tecnica anterior
                $objInfoServicioTecnico = $this->emComercial->getRepository("schemaBundle:InfoServicioTecnico")
                                                            ->findOneByServicioId($intIdServicio);
                $this->utilServicio->validaObjeto($objInfoServicioTecnico, "No existe información de SW nuevo, favor notificar a Sistemas.");
                $objServicio            = $this->emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServicio);
                $objPunto               = $objServicio->getPuntoId();
                $objUltimaMilla         = $this->emInfraestructura->getRepository("schemaBundle:AdmiTipoMedio")
                                                                  ->findOneByNombreTipoMedio($strUltimaMilla);
                //Valores antiguos                                                                
                $intElementoId                      = $objInfoServicioTecnico->getElementoId();
                $intInterfaceElementoId             = $objInfoServicioTecnico->getInterfaceElementoId();
                $intElementoConectorId              = $objInfoServicioTecnico->getElementoConectorId();
                $intElementoContenedorId            = $objInfoServicioTecnico->getElementoContenedorId();
                $intInterfaceElementoConectorId     = $objInfoServicioTecnico->getInterfaceElementoConectorId();
                $intElementoClienteId               = $objInfoServicioTecnico->getElementoClienteId();
                
                $strElementoNombre   = "";
                $strInterfaceNombre  = "";
                $strContenedorNombre = "";
                $strConectorNombre   = "";
                $strTipoElementoCli  = "";
                
                //Se obtiene la informacion tecnica que exista
                if($intElementoId)
                {
                    $objElemento = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intElementoId);
                    $strElementoNombre = $objElemento->getNombreElemento();
                }
                if($intInterfaceElementoId)
                {
                    $objInterface = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")->find($intInterfaceElementoId);
                    $strInterfaceNombre = $objInterface->getNombreInterfaceElemento();
                }
                if($intElementoConectorId)
                {
                    $objElemento = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intElementoConectorId);
                    $strConectorNombre = $objElemento->getNombreElemento();
                }
                if($intElementoContenedorId)
                {
                    $objElemento = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intElementoContenedorId);
                    $strContenedorNombre = $objElemento->getNombreElemento();
                }                    
                if($intElementoClienteId)
                {
                    $objElemento        = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intElementoClienteId);
                    if(is_object($objElemento))
                    {
                        $strTipoElementoCli = $objElemento->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                    }
                }
                
                //Valores nuevos
                $intElementoIdNuevo                  = $arrInfoFactibilidadUM['idElemento'];
                $intInterfaceElementoIdNuevo         = $arrInfoFactibilidadUM['idInterfaceElemento'];
                $intElementoConectorIdNuevo          = $arrInfoFactibilidadUM['idElementoConector'];
                $intElementoContenedorIdNuevo        = $arrInfoFactibilidadUM['idElementoContenedor'];
                $intInterfaceElementoConectorIdNuevo = $arrInfoFactibilidadUM['idInterfaceElementoConector'];                                
                
                //Se realiza el cambio de informacion de data nueva con la anterior
                $objInfoServicioTecnico->setElementoId($intElementoIdNuevo);
                $objInfoServicioTecnico->setInterfaceElementoId($intInterfaceElementoIdNuevo);
                $objInfoServicioTecnico->setElementoConectorId($intElementoConectorIdNuevo);
                $objInfoServicioTecnico->setElementoContenedorId($intElementoContenedorIdNuevo);
                $objInfoServicioTecnico->setInterfaceElementoConectorId($intInterfaceElementoConectorIdNuevo);                
                
                //Se actualiza la solicitud de la caracteristica con la informacion de Backbone anterior
                $this->actualizarValorYEstadoDetalleSolCarcact($intIdSolicitud,'ELEMENTO_ID',$intElementoId,$intElementoIdNuevo,$strUsrCreacion);
                $this->actualizarValorYEstadoDetalleSolCarcact($intIdSolicitud,'INTERFACE_ELEMENTO_ID',$intInterfaceElementoId,$intInterfaceElementoIdNuevo,$strUsrCreacion);
                $this->actualizarValorYEstadoDetalleSolCarcact($intIdSolicitud,'ELEMENTO_CONECTOR_ID',$intElementoConectorId,$intElementoConectorIdNuevo,$strUsrCreacion);
                $this->actualizarValorYEstadoDetalleSolCarcact($intIdSolicitud,'INTERFACE_ELEMENTO_CONECTOR_ID',$intInterfaceElementoConectorId,$intInterfaceElementoConectorIdNuevo,$strUsrCreacion);
                $this->actualizarValorYEstadoDetalleSolCarcact($intIdSolicitud,'ELEMENTO_CONTENEDOR_ID',$intElementoContenedorId,$intElementoContenedorIdNuevo,$strUsrCreacion);   
                
                if($boolEsFibraRuta) //Para FIBRA RUTA se enlaza al puerto del cassette
                {
                    //Siempre el conector sera el OUT del cassette
                    $intIdInterfaceEnlaceElementoClienteNuevo    = $intInterfaceElementoConectorIdNuevo;
                    
                    //Si tiene data GIS la interface anterior a desenlazar es la del cassette
                    if($intInterfaceElementoConectorId)
                    {                        
                        $intIdInterfaceEnlaceElementoClienteAnterior = $intInterfaceElementoConectorId;
                    }
                    else //Si no tiene data GIS la interfaze a desenlazar es la ROSETA
                    {                        
                        $intIdInterfaceEnlaceElementoClienteAnterior = $intInterfaceElementoId;
                    }                        
                }
                else //Para UTP/FIBRA DIRECTO a puerto del sw y para Radio a puerto de la Radio la interfaz del sw
                {
                    $intIdInterfaceEnlaceElementoClienteNuevo    = $intInterfaceElementoIdNuevo;
                    $intIdInterfaceEnlaceElementoClienteAnterior = $intInterfaceElementoId;
                }
               
                //Si no existe Caja y es FIBRA RUTA y ademas el elemento cliente es CPE o ROUTER directamente
                if($boolEsFibraRuta && !$intElementoConectorId && ($intElementoClienteId && $strTipoElementoCli != 'ROSETA'))
                {                    
                    $objInterfaceElementoConector = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                            ->find($intInterfaceElementoConectorIdNuevo);
                    //ingresar elemento roseta
                    $arrayParametrosRoseta = array(
                                                    'nombreElementoCliente'         => "ros-".$objPunto->getLogin(),
                                                    'nombreModeloElementoCliente'   => "ROS-1234",
                                                    'serieElementoCliente'          => "00000",
                                                    'objInterfaceElementoVecinoOut' => $objInterfaceElementoConector,
                                                    'objUltimaMilla'                => $objUltimaMilla,
                                                    'objServicio'                   => $objServicio,
                                                    'intIdEmpresa'                  => $empresaCod,
                                                    'usrCreacion'                   => $strUsrCreacion,
                                                    'ipCreacion'                    => $strIpCreacion
                                                );
                    $objInterfaceElementoClienteInicio = $this->servicioGeneral->ingresarElementoClienteTN($arrayParametrosRoseta,"ROSETA");

                    //ingresar elemento transciever
                    $arrayParametrosTransceiver = array(
                                                    'nombreElementoCliente'         => "trans-".$objPunto->getLogin(),
                                                    'nombreModeloElementoCliente'   => "TRANSCEIVER TRANS",
                                                    'serieElementoCliente'          => "00000",
                                                    'objInterfaceElementoVecinoOut' => $objInterfaceElementoClienteInicio,
                                                    'objUltimaMilla'                => $objUltimaMilla,
                                                    'objServicio'                   => $objServicio,
                                                    'intIdEmpresa'                  => $empresaCod,
                                                    'usrCreacion'                   => $strUsrCreacion,
                                                    'ipCreacion'                    => $strIpCreacion
                                                );
                    $objInterfaceElementoClienteTransceiver = $this->servicioGeneral->ingresarElementoClienteTN($arrayParametrosTransceiver,"TRANSCEIVER");

                    //Se obtiene el CPE
                    $objElementoCpe = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intElementoClienteId);

                    $objModeloElementoCpe = $this->emInfraestructura->getRepository("schemaBundle:AdmiModeloElemento")
                                                                    ->find($objElementoCpe->getModeloElementoId()->getId());

                    //ingresar elemento CPE ( Solo Enlace)
                    $arrayParametrosCpe = array(
                                                    'nombreElementoCliente'         => $objPunto->getLogin(),
                                                    'nombreModeloElementoCliente'   => $objModeloElementoCpe->getNombreModeloElemento(),
                                                    'serieElementoCliente'          => "00000",
                                                    'objInterfaceElementoVecinoOut' => $objInterfaceElementoClienteTransceiver,
                                                    'objUltimaMilla'                => $objUltimaMilla,
                                                    'objServicio'                   => $objServicio,
                                                    'intIdEmpresa'                  => $empresaCod,
                                                    'usrCreacion'                   => $strUsrCreacion,
                                                    'ipCreacion'                    => $strIpCreacion,
                                                    'esFlujoNormal'                 => "NO",
                                                    'objElementoCpe'                => $objElementoCpe
                                                );
                    //Solo se crea el enlace con el CPE/ROUTER ya que este ya existe como elemento
                    $this->servicioGeneral->ingresarElementoClienteTN($arrayParametrosCpe,
                                                                      $objModeloElementoCpe->getTipoElementoId()->getNombreTipoElemento());

                    //guardar cpe en servicio tecnico
                    $objInfoServicioTecnico->setElementoClienteId($objInterfaceElementoClienteInicio->getElementoId()->getId());
                    $objInfoServicioTecnico->setInterfaceElementoClienteId($objInterfaceElementoClienteInicio->getId());                        
                    
                }
                else
                {          
                    //se recupera la interface de elemento cliente nuevo
                    $objInterfaceElementoConectorNew = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                               ->find($intIdInterfaceEnlaceElementoClienteNuevo);
                    if (!is_object($objInterfaceElementoConectorNew))
                    {
                        throw new \Exception("No se logro recupera nueva interface del elemento cliente.");
                    }
                    
                    //se recupera enlace anterior
                    $objEnlaceAnt = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                            ->findOneBy(array("interfaceElementoIniId"=>$intIdInterfaceEnlaceElementoClienteAnterior,
                                                                              "estado"                =>"Activo"));  
                    
                    //se consulta enlace a crear
                    $objEnlaceACrear = $this->emInfraestructura
                                            ->getRepository('schemaBundle:InfoEnlace')
                                            ->findOneBy(array("interfaceElementoIniId" => $objInterfaceElementoConectorNew->getId(),
                                                              "estado"                 => "Activo"));
                    /* se agrega validación para solo crear enlace en caso de que exista un enlace anterior activo
                       y que el nuevo enlace a crear no exista */
                    if (is_object($objEnlaceAnt) && !is_object($objEnlaceACrear))
                    {
                        $objEnlaceAnt->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objEnlaceAnt);
                        $this->emInfraestructura->flush();

                        $objInterfaceElementoConectorNew = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                                   ->find($intIdInterfaceEnlaceElementoClienteNuevo);

                        //enlace entre puerto casette - roseta / sw - cpe / sw - roseta / sw - radio
                        $objEnlaceNew = new InfoEnlace();
                        $objEnlaceNew->setInterfaceElementoIniId($objInterfaceElementoConectorNew);
                        $objEnlaceNew->setInterfaceElementoFinId($objEnlaceAnt->getInterfaceElementoFinId());
                        $objEnlaceNew->setEstado("Activo");
                        $objEnlaceNew->setUsrCreacion($strUsrCreacion);
                        $objEnlaceNew->setTipoMedioId($objUltimaMilla);
                        $objEnlaceNew->setTipoEnlace("PRINCIPAL");
                        $objEnlaceNew->setFeCreacion(new \DateTime('now'));
                        $objEnlaceNew->setIpCreacion($strIpCreacion);
                        $this->emInfraestructura->persist($objEnlaceNew);

                        //actualizar estado del puerto casette
                        $objIdInterfaceOutAnt = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                        ->find($intIdInterfaceEnlaceElementoClienteAnterior);
                        if (is_object($objIdInterfaceOutAnt))
                        {
                            $objIdInterfaceOutAnt->setEstado("not connect");
                            $this->emInfraestructura->persist($objIdInterfaceOutAnt);
                            $this->emInfraestructura->flush();
                        }
                    }
                    /*se mostrara mensaje de error en caso de:
                     *   - Si no existe enlace anterior y no existe enlace nuevo a crear
                     *   - Si existe enlace anterior y existe enlace nuevo a crear
                      */
                    else if ((!is_object($objEnlaceAnt) && !is_object($objEnlaceACrear)) ||
                             (is_object($objEnlaceAnt) && is_object($objEnlaceACrear)))
                    {
                        $intIdEnlaceAnt    = '';
                        $intIdEnlaceACrear = '';
                        if (is_object($objEnlaceAnt) && is_object($objEnlaceACrear))
                        {
                            $intIdEnlaceAnt    = $objEnlaceAnt->getId();
                            $intIdEnlaceACrear = $objEnlaceACrear->getId();
                            
                            if ($intIdEnlaceAnt !== $intIdEnlaceACrear)
                            {
                                throw new \Exception("Ocurrio un problema al crear enlaces entre equipo de backbone y equipo de cliente");
                            }
                        }
                        if (!is_object($objEnlaceAnt) && !is_object($objEnlaceACrear))
                        {
                            throw new \Exception("Ocurrio un problema al crear enlaces entre equipo de backbone y equipo de cliente");
                        }
                    }
                }
            
                $this->emComercial->persist($objInfoServicioTecnico);
                $this->emComercial->flush();
                
                //Se genera historial de cambio realizado                
                $observacionServicioYSolicitud.="<b>Cambio de Ultima Milla : Informacion de BackBone Anterior</b><br/>";
                $observacionServicioYSolicitud.="Switch Anterior   : ".$strElementoNombre."<br/>";
                $observacionServicioYSolicitud.="Interface Anterior: ".$strInterfaceNombre."<br/>";
                if($boolEsFibraRuta)
                {
                    $observacionServicioYSolicitud.="Caja Anterior     : ".$strContenedorNombre."<br/>";
                    $observacionServicioYSolicitud.="Cassete Anterior  : ".$strConectorNombre."<br/>";            
                }
                $observacionServicioYSolicitud.="<b>Cambio de Ultima Milla : Informacion de BackBone Nueva</b><br/>";
                $observacionServicioYSolicitud.="Switch Nuevo      : ".$arrInfoFactibilidadUM['nombreElemento']."<br/>";
                $observacionServicioYSolicitud.="Interface Nueva   : ".$arrInfoFactibilidadUM['nombreInterfaceElemento']."<br/>";
                if($boolEsFibraRuta)
                {
                    $observacionServicioYSolicitud.="Caja Nueva        : ".$arrInfoFactibilidadUM['nombreElementoContenedor']."<br/>";
                    $observacionServicioYSolicitud.="Cassete Nuevo     : ".$arrInfoFactibilidadUM['nombreElementoConector']."<br/>";
                    $observacionServicioYSolicitud.="Hilo Nuevo        : ".$arrInfoFactibilidadUM['colorHilo']."<br/><br/>";
                }
                
                $objInfoHistorial = new InfoServicioHistorial();
                $objInfoHistorial->setServicioId($objServicio);
                $objInfoHistorial->setEstado($objServicio->getEstado());
                $objInfoHistorial->setUsrCreacion($strUsrCreacion);
                $objInfoHistorial->setFeCreacion(new \DateTime('now'));
                $objInfoHistorial->setIpCreacion($strIpCreacion);
                $objInfoHistorial->setObservacion($observacionServicioYSolicitud);
                $this->emComercial->persist($objInfoHistorial);
                $this->emComercial->flush(); 
                
                $objSolicitud      = $this->emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")->find($intIdSolicitud);
                //Eliminar IP de informacion BB anterior al servicio
                //grabar detalles tecnicos en la solicitud - ID_IP 	                                                                               
                $arrayInfoIp = $this->emInfraestructura->getRepository("schemaBundle:InfoIp")->findBy(array('servicioId'=> $intIdServicio,
                                                                                                            'estado'    => 'Activo')
                                                                                                     );                                                
                if($arrayInfoIp)
                {
                    if(count($arrayInfoIp)>1)
                    {
                        throw new \Exception("Servicio posee más de una ip Activa, Favor Regularizar!");
                    }
                    
                    $arrayInfoIp[0]->setEstado("Eliminada");
                    $this->emInfraestructura->persist($arrayInfoIp[0]);
                    $this->emInfraestructura->flush();                                        
                    
                    $objCaracElemento  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica') 	
                                                           ->findOneBy(array('descripcionCaracteristica' => 'IP_ID')); 
                    
                    $arrayParametrosTecElemento = array( 	
                                                    'objDetalleSolicitudId' => $objSolicitud, 	
                                                    'objCaracteristica'     => $objCaracElemento, 	
                                                    'estado'                => "Asignada", 	
                                                    'valor'                 => $arrayInfoIp[0]->getId(), 	
                                                    'usrCreacion'           => $strUsrCreacion 	
                                                    );  

                    $this->servicioGeneral->insertarInfoDetalleSolCaract($arrayParametrosTecElemento);
                }     
                else
                {
                    throw new \Exception("Servicio no posee IP Activa");
                }
                
                //obtener los servicios prod caract
                $arrServicioProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                           ->findBy(array("servicioId" => $intIdServicio, "estado" => "Activo"));

                //grabar las caracteristicas en la solicitud
                foreach($arrServicioProdCaract as $objServicioProdCaract)
                {
                    $objCaracteristica = $this->servicioGeneral->getCaracteristicaByInfoServicioProdCaract($objServicioProdCaract);

                    $arrayParams = array(
                                                'objDetalleSolicitudId' => $objSolicitud,
                                                'objCaracteristica'     => $objCaracteristica,
                                                'estado'                => "Asignada",
                                                'valor'                 => $objServicioProdCaract->getValor(),
                                                'usrCreacion'           => $strUsrCreacion
                                            );

                    $this->servicioGeneral->insertarInfoDetalleSolCaract($arrayParams);
                }
                
                $this->servicioGeneral->eliminarDatosCaracteristicas($arrayParametros);
                
                //Se deja en Asignado el TIPO_CAMBIO_ULTIMA_MILLA
                $objCaracElemento   = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica') 	
                                                        ->findOneBy(array('descripcionCaracteristica' => 'TIPO_CAMBIO_ULTIMA_MILLA'));
                $objSolCaractTipoUM = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array('caracteristicaId'   => $objCaracElemento->getId(),
                                                                          'estado'             => 'AsignadoTarea',
                                                                          'detalleSolicitudId' => $intIdSolicitud));
                
                if($objSolCaractTipoUM)
                {
                    $objSolCaractTipoUM->setEstado("Asignada");
                    $this->emComercial->persist($objSolCaractTipoUM);
                    $this->emComercial->flush(); 
                }
            }
            else
            {
                throw new \Exception("Informacion insuficiente para Asignar Recursos en Cambio de Ultima Milla");
            }
        } 
        catch (\Exception $ex) 
        {
            error_log($ex->getMessage());
            throw new \Exception("ERROR : ".$ex->getMessage());
        }
    }
    
    /**
     * 
     * Metodo utilizado para asignacion de recursos de Red para Cambio de Ultima Milla
     * para intercambiar informacion Tecnica para ser usado al momento de la Activacion Tecnica
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 31-07-2016
     * @since 1.0 31-07-2016
     * 
     * @param Array $arrayParametros [ usrCreacion            =>  Recibe usuario de creación a utilizar en la actualización de información del metodo
     *                                 idDetalleSolicitud     =>  Recibe identificador de la solicitud de cambio de um de radio
     *                                 idServicio             =>  Recibe identificador del servicio procesado
     *                                 ipCreacion             =>  Recibe ip de creación a utilizar en la actualización de información del metodo
     *                                 ultimaMilla            =>  Recibe ultima milla del servicio procesado
     *                               ]
     * @throws \Exception
     */
    public function asignacionRecursosRedUMRadio($arrayParametros)
    {
        try
        {                                                
            $strUsrCreacion = $arrayParametros['usrCreacion'];
            $intIdSolicitud = $arrayParametros['idDetalleSolicitud'];
            $intIdServicio  = $arrayParametros['idServicio'];
            $strIpCreacion  = $arrayParametros['ipCreacion'];
            $strUltimaMilla = $arrayParametros['ultimaMilla'];
            $this->utilServicio->validaObjeto($strUsrCreacion, "Parametro usuario creación sin información, favor notificar a Sistemas.");
            $this->utilServicio->validaObjeto($strIpCreacion, "Parametro ip creación sin información, favor notificar a Sistemas.");
            
            $arrayParametrosMetodo = array('emInfraestructura' => $this->emInfraestructura, 
                                           'emComercial'       => $this->emComercial,
                                           'idSolicitud'       => $intIdSolicitud,  
                                           'idServicio'        => $intIdServicio,
                                           'ultimaMilla'       => $strUltimaMilla
                                          );
            
            $arrayInfoFactibilidadUM  = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                                ->getDatosFactibilidadUltimaMillaRadio($arrayParametrosMetodo);

            if($arrayInfoFactibilidadUM['status'] == 'OK')
            {
                $arrayInfoFactibilidadUM  = $arrayInfoFactibilidadUM['data'];
                //Se obtiene la informacion tecnica anterior
                $objInfoServicioTecnico = $this->emComercial->getRepository("schemaBundle:InfoServicioTecnico")
                                                            ->findOneByServicioId($intIdServicio);  
                $this->utilServicio->validaObjeto($objInfoServicioTecnico, "El servicio no tiene información técnica, favor notificar a Sistemas.");
                
                $objServicio            = $this->emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServicio);
                $this->utilServicio->validaObjeto($objServicio, "El servicio no existe, favor notificar a Sistemas.");
                
                $objPunto               = $objServicio->getPuntoId();
                $this->utilServicio->validaObjeto($objPunto, "El servicio no tiene asociado un punto, favor notificar a Sistemas.");
                
                //Valores antiguos                                                                
                $intElementoId          = $objInfoServicioTecnico->getElementoId();
                $intInterfaceElementoId = $objInfoServicioTecnico->getInterfaceElementoId();
                $intElementoConectorId  = $objInfoServicioTecnico->getElementoConectorId();
                
                $strElementoNombre   = "";
                $strInterfaceNombre  = "";
                $strConectorNombre   = "";
                
                //Se obtiene la informacion tecnica que exista
                if($intElementoId)
                {
                    $objElemento       = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intElementoId);
                    $this->utilServicio->validaObjeto($objElemento, "El servicio no tiene elemento de backbone registrado, favor notificar a Sistemas.");
                    $strElementoNombre = $objElemento->getNombreElemento();
                }
                if($intInterfaceElementoId)
                {
                    $objInterface       = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")->find($intInterfaceElementoId);
                    $this->utilServicio->validaObjeto($objInterface, "El servicio no tiene una de interface del elemento de backbone registrado, ".
                                                                     "favor notificar a Sistemas.");
                    $strInterfaceNombre = $objInterface->getNombreInterfaceElemento();
                }
                if($intElementoConectorId)
                {
                    $objElemento       = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intElementoConectorId);
                    $this->utilServicio->validaObjeto($objElemento, "El servicio no tiene elemento conector de backbone registrado, favor notificar a Sistemas.");
                    $strConectorNombre = $objElemento->getNombreElemento();
                }
                
                //Valores nuevos
                $intElementoIdNuevo          = $arrayInfoFactibilidadUM['idElemento'];
                $intInterfaceElementoIdNuevo = $arrayInfoFactibilidadUM['idInterfaceElemento'];
                $intElementoConectorIdNuevo  = $arrayInfoFactibilidadUM['idElementoConector'];
                
                //Se realiza el cambio de informacion de data nueva con la anterior
                $objInfoServicioTecnico->setElementoId($intElementoIdNuevo);
                $objInfoServicioTecnico->setInterfaceElementoId($intInterfaceElementoIdNuevo);
                $objInfoServicioTecnico->setElementoConectorId($intElementoConectorIdNuevo);
                
                //Se actualiza la solicitud de la caracteristica con la informacion de Backbone anterior
                $this->actualizarValorYEstadoDetalleSolCarcact($intIdSolicitud,'ELEMENTO_ID',$intElementoId,$intElementoIdNuevo,$strUsrCreacion);
                $this->actualizarValorYEstadoDetalleSolCarcact($intIdSolicitud,
                                                               'INTERFACE_ELEMENTO_ID',
                                                               $intInterfaceElementoId,
                                                               $intInterfaceElementoIdNuevo,
                                                               $strUsrCreacion);
                $this->actualizarValorYEstadoDetalleSolCarcact($intIdSolicitud,
                                                               'ELEMENTO_CONECTOR_ID',
                                                               $intElementoConectorId,
                                                               $intElementoConectorIdNuevo,
                                                               $strUsrCreacion);
                
                $this->emComercial->persist($objInfoServicioTecnico);
                $this->emComercial->flush();
                
                //Se genera historial de cambio realizado                
                $observacionServicioYSolicitud.="<b>Cambio de Ultima Milla Radio : Informacion de BackBone Anterior</b><br/>";
                $observacionServicioYSolicitud.="Switch Anterior   : ".$strElementoNombre."<br/>";
                $observacionServicioYSolicitud.="Interface Anterior: ".$strInterfaceNombre."<br/>";
                $observacionServicioYSolicitud.="Radio Backbone Anterior  : ".$strConectorNombre."<br/>";            
                $observacionServicioYSolicitud.="<b>Cambio de Ultima Milla Radio : Informacion de BackBone Nueva</b><br/>";
                $observacionServicioYSolicitud.="Switch Nuevo      : ".$arrayInfoFactibilidadUM['nombreElemento']."<br/>";
                $observacionServicioYSolicitud.="Interface Nueva   : ".$arrayInfoFactibilidadUM['nombreInterfaceElemento']."<br/>";
                $observacionServicioYSolicitud.="Radio Backbone Nuevo     : ".$arrayInfoFactibilidadUM['nombreElementoConector']."<br/>";
                
                $objInfoHistorial = new InfoServicioHistorial();
                $objInfoHistorial->setServicioId($objServicio);
                $objInfoHistorial->setEstado($objServicio->getEstado());
                $objInfoHistorial->setUsrCreacion($strUsrCreacion);
                $objInfoHistorial->setFeCreacion(new \DateTime('now'));
                $objInfoHistorial->setIpCreacion($strIpCreacion);
                $objInfoHistorial->setObservacion($observacionServicioYSolicitud);
                $this->emComercial->persist($objInfoHistorial);
                $this->emComercial->flush(); 
                
                $objSolicitud      = $this->emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")->find($intIdSolicitud);
                $this->utilServicio->validaObjeto($objSolicitud, "No existe solicitud de cambio de um para el servicio, favor notificar a Sistemas.");
                //Eliminar IP de informacion BB anterior al servicio
                //grabar detalles tecnicos en la solicitud - ID_IP 	                                                                               
                $arrayInfoIp = $this->emInfraestructura->getRepository("schemaBundle:InfoIp")->findBy(array('servicioId'=> $intIdServicio,
                                                                                                            'estado'    => 'Activo')
                                                                                                     );                                                
                if($arrayInfoIp)
                {
                    if(count($arrayInfoIp)>1)
                    {
                        throw new \Exception("Servicio posee más de una ip Activa, Favor Regularizar!");
                    }
                    
                    $arrayInfoIp[0]->setEstado("Eliminada");
                    $this->emInfraestructura->persist($arrayInfoIp[0]);
                    $this->emInfraestructura->flush();                                        
                    
                    $objCaracElemento  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica') 	
                                                           ->findOneBy(array('descripcionCaracteristica' => 'IP_ID')); 
                    $this->utilServicio->validaObjeto($objCaracElemento, "Información incompleta al registrar caracteristica de solicitud IP_ID, ".
                                                                         "favor notificar a Sistemas.");
                    
                    
                    $arrayParametrosTecElemento = array( 	
                                                        'objDetalleSolicitudId' => $objSolicitud, 	
                                                        'objCaracteristica'     => $objCaracElemento, 	
                                                        'estado'                => "Asignada", 	
                                                        'valor'                 => $arrayInfoIp[0]->getId(), 	
                                                        'usrCreacion'           => $strUsrCreacion 	
                                                       );  

                    $this->servicioGeneral->insertarInfoDetalleSolCaract($arrayParametrosTecElemento);
                }     
                else
                {
                    throw new \Exception("Servicio no posee IP Activa");
                }
                
                //obtener los servicios prod caract
                $arrServicioProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                           ->findBy(array("servicioId" => $intIdServicio, "estado" => "Activo"));

                //grabar las caracteristicas en la solicitud
                foreach($arrServicioProdCaract as $objServicioProdCaract)
                {
                    $objCaracteristica = $this->servicioGeneral->getCaracteristicaByInfoServicioProdCaract($objServicioProdCaract);
                    $this->utilServicio->validaObjeto($objCaracteristica, "Información incompleta al registrar caracteristicas de solicitud, ".
                                                                          "favor notificar a Sistemas.");
                    
                    $arrayParams = array(
                                         'objDetalleSolicitudId' => $objSolicitud,
                                         'objCaracteristica'     => $objCaracteristica,
                                         'estado'                => "Asignada",
                                         'valor'                 => $objServicioProdCaract->getValor(),
                                         'usrCreacion'           => $strUsrCreacion
                                        );

                    $this->servicioGeneral->insertarInfoDetalleSolCaract($arrayParams);
                }
                
                $this->servicioGeneral->eliminarDatosCaracteristicas($arrayParametros);
                
                //Se deja en Asignado el TIPO_CAMBIO_ULTIMA_MILLA
                $objCaracElemento   = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica') 	
                                                        ->findOneBy(array('descripcionCaracteristica' => 'TIPO_CAMBIO_ULTIMA_MILLA'));
                $this->utilServicio->validaObjeto($objCaracElemento, "No existe caracteristica de tipo de cambio um en las tablas de parametrización, ".
                                                                     "favor notificar a Sistemas.");
                $objSolCaractTipoUM = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array('caracteristicaId'   => $objCaracElemento->getId(),
                                                                          'estado'             => 'AsignadoTarea',
                                                                          'detalleSolicitudId' => $intIdSolicitud));
                
                if($objSolCaractTipoUM)
                {
                    $objSolCaractTipoUM->setEstado("Asignada");
                    $this->emComercial->persist($objSolCaractTipoUM);
                    $this->emComercial->flush(); 
                }
            }
            else
            {
                throw new \Exception("Informacion insuficiente para Asignar Recursos en Cambio de Ultima Milla");
            }
        } 
        catch (\Exception $ex) 
        {
            error_log($ex->getMessage());
            throw new \Exception("ERROR : ".$ex->getMessage());
        }
    }
    
    /**
     * 
     * Metodo que sirve para actualizar la informacion de detalle de solicitud caracteristica dado la caracteristica y el valor, para efecto de
     * proceso de cambio de Ultima Milla
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 29-06-2016
     * 
     * @param string $strCaracteristica
     * @param integer $idValorAnterior
     * @param integer $idValorNuevo
     * @param string $usrCreacion
     */
    public function actualizarValorYEstadoDetalleSolCarcact($intIdSolicitud,$strCaracteristica,$idValorAnterior,$idValorNuevo,$usrCreacion)
    {
        $objCaracteristica  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica') 	
                                                ->findOneBy(array('descripcionCaracteristica' => $strCaracteristica));
        if($objCaracteristica)
        {
            $objDetSolCaract = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                 ->findOneBy(array('caracteristicaId'   => $objCaracteristica->getId(),
                                                                   'valor'              => $idValorNuevo,
                                                                   'detalleSolicitudId' => $intIdSolicitud,
                                                                   'estado'             => 'AsignadoTarea')
                                                            );            
            if($objDetSolCaract && $idValorAnterior)
            {
                $objDetSolCaract->setValor($idValorAnterior);
                $objDetSolCaract->setEstado("Asignada");
                $objDetSolCaract->setUsrUltMod($usrCreacion);
                $objDetSolCaract->setFeUltMod(new \DateTime('now'));
                $this->emComercial->persist($objDetSolCaract);
                $this->emComercial->flush();
            }                  
            else//Si los valores de bb existentes vienen inconclusos solo se finaliza el resgistro y no se traspasa la informacion
            {                
                if($objDetSolCaract)
                {
                    $objDetSolCaract->setEstado("Finalizada");
                    $objDetSolCaract->setUsrUltMod($usrCreacion);
                    $objDetSolCaract->setFeUltMod(new \DateTime('now'));
                    $this->emComercial->persist($objDetSolCaract);
                    $this->emComercial->flush();
                }
            }
        }
    }
    
    /**
     * 
     * Metodo encaragdo de devolver el resumen de ips dada una subred [ Gateway , Ip Inicial , Ip Final ]
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 05-10-2017
     * 
     * @param Array $arrayParametros [ strSubred , strMascara ]
     * @return Array $arrayResultado
     */
    public function getArrayValoresBySubred($arrayParametros)
    {
        $strSubred  = $arrayParametros['strSubred'];
        $strMascara = $arrayParametros['strMascara'];
        
        $arrayOctetos    = explode(".", $strSubred);
        $intUltimoOcteto = intval($arrayOctetos[3]);
        
        switch($strMascara)
        {
            case '/24':
                $intNumeroIpsTotales = 254;
                break;
            case '/25':
                $intNumeroIpsTotales = 126;
                break;
            case '/26':
                $intNumeroIpsTotales = 62;
                break;
            case '/27':
                $intNumeroIpsTotales = 30;
                break;
            case '/28':
                $intNumeroIpsTotales = 14;
                break;
            case '/29':
                $intNumeroIpsTotales = 6;
                break;
            case '/30':
                $intNumeroIpsTotales = 2;
                break;
            default:
                break;
        }
        
        $arrayResultado                 = array();
        $arrayResultado['strGateway']   = $arrayOctetos[0].'.'.$arrayOctetos[1].'.'.$arrayOctetos[2].'.'.($intUltimoOcteto + 1);
        $arrayResultado['strIpInicial'] = $arrayOctetos[0].'.'.$arrayOctetos[1].'.'.$arrayOctetos[2].'.'.($intUltimoOcteto + 2);
        $arrayResultado['strIpFinal']   = $arrayOctetos[0].'.'.$arrayOctetos[1].'.'.$arrayOctetos[2].'.'.($intUltimoOcteto + $intNumeroIpsTotales);
        
        return $arrayResultado;
    }
    
    /**
     * asignarRecursosRedInternetResidencial
     * 
     * Función que sirve para grabar los recursos de Red para el producto Internet Small Business
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 09-11-2018
     * @since 1.0
     * 
     * @param array $arrayParametros
     * @return array $arrayRespuestaFinal
     */
    public function asignarRecursosRedInternetResidencial($arrayParametros)
    {
        $intIdDetSolPlanif      = $arrayParametros['idDetSolPlanif'];
        $intIdSplitter          = $arrayParametros['idSplitter'];
        $intIdInterfaceSplitter = $arrayParametros['idInterfaceSplitter'];
        $strMarcaOlt            = $arrayParametros['marcaOlt'];
        $strUsrCreacion         = $arrayParametros['usrCreacion'];
        $strIpCreacion          = $arrayParametros['ipCreacion'];
        $strCodEmpresa          = $arrayParametros['idEmpresa'];
        $strPerfilEquivalente   = "";
        $strAprovisionamiento   = "";
        $strStatus              = "ERROR";
        $strMensaje             = "";
        $this->emComercial->beginTransaction();
        $this->emInfraestructura->beginTransaction();

        try
        {
            if( isset($intIdDetSolPlanif) && !empty($intIdDetSolPlanif) && $intIdDetSolPlanif > 0
                && ( isset($intIdSplitter) && !empty($intIdSplitter) && $intIdSplitter > 0
                     && isset($intIdInterfaceSplitter) && !empty($intIdInterfaceSplitter) && $intIdInterfaceSplitter > 0
                    ))
            {
                $objDetalleSolicitud    = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                            ->findOneById($intIdDetSolPlanif);
                if(!is_object($objDetalleSolicitud))
                {
                    $strMensaje = "No se ha podido obtener la solicitud o la interface del splitter que se desea asignar";
                    throw new \Exception($strMensaje);
                }

                $objServicio   = $objDetalleSolicitud->getServicioId();
                if(!is_object($objServicio))
                {
                    $strMensaje = "No se ha podido obtener el servicio asociado";
                    throw new \Exception($strMensaje);
                }
                $intIdServicio = $objServicio->getId();

                $objPunto   = $objServicio->getPuntoId();
                if(!is_object($objPunto))
                {
                    $strMensaje = "No se ha podido obtener el punto asociado al servicio";
                    throw new \Exception($strMensaje);
                }
                
                $objPlan    = $objServicio->getPlanId();
                if(!is_object($objPlan))
                {
                    $strMensaje = "No se ha podido obtener el plan asociado al servicio";
                    throw new \Exception($strMensaje);
                }
                
                $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                        ->findOneByServicioId($intIdServicio);
                if(!is_object($objServicioTecnico))
                {
                    $strMensaje = "No se ha podido obtener el servicio técnico del servicio asociado";
                    throw new \Exception($strMensaje);
                }

                $objProdInternet   = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                            ->findOneBy(array(  "empresaCod"          => $strCodEmpresa, 
                                                                                "nombreTecnico"       => "INTERNET", 
                                                                                "estado"              => "Activo"));
                if(!is_object($objProdInternet))
                {
                    $strMensaje = "No se ha podido obtener el producto principal para la asignación de red";
                    throw new \Exception($strMensaje);
                }
                
                $intIdOlt                   = $objServicioTecnico->getElementoId();
                $intIdInterfaceSplitterAnt  = $objServicioTecnico->getInterfaceElementoConectorId();
                if((empty($intIdOlt) || (!empty($intIdOlt) && is_numeric($intIdOlt) && $intIdOlt <= 0))
                    || (empty($intIdInterfaceSplitterAnt) 
                        || (!empty($intIdInterfaceSplitterAnt) && is_numeric($intIdInterfaceSplitterAnt) && $intIdInterfaceSplitterAnt <= 0)))
                {
                    $strMensaje = "No se han encontrado los recursos de factibilidad asignados";
                    throw new \Exception($strMensaje);
                }

                if (empty($strMarcaOlt))
                {
                    $strMensaje = "No existe un flujo definido para el Internet Small Business sin la marca del OLT";
                    throw new \Exception($strMensaje);
                }

                if ($intIdInterfaceSplitterAnt !== intval($intIdInterfaceSplitter))
                {
                    $objInterfaceSplitterAntes = $this->emInfraestructura
                                                      ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                      ->findOneById($intIdInterfaceSplitterAnt);
                    if(is_object($objInterfaceSplitterAntes))
                    {
                        $objInterfaceSplitterAntes->setEstado("not connect");
                        $this->emInfraestructura->persist($objInterfaceSplitterAntes);
                        $this->emInfraestructura->flush();
                    }                       
                }

                $objInterfaceSplitter   = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                  ->findOneById($intIdInterfaceSplitter);
                if(!is_object($objInterfaceSplitter))
                {
                    $strMensaje = "No se ha podido obtener la interface del splitter que se desea asignar";
                    throw new \Exception($strMensaje);
                }

                $objInterfaceSplitter->setEstado('reserved');
                $this->emInfraestructura->persist($objInterfaceSplitter);
                $this->emInfraestructura->flush();

                $objServicioTecnico->setElementoConectorId($intIdSplitter);
                $objServicioTecnico->setInterfaceElementoConectorId($intIdInterfaceSplitter);
                $this->emComercial->persist($objServicioTecnico);
                $this->emComercial->flush();
                
                
                //recuperar perfil
                $objPerfil           = $this->emComercial
                                            ->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => "PERFIL",
                                                              "estado"                    => "Activo"));
                $objProdCaractPerfil = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                       ->findOneBy(array("productoId"       => $objProdInternet->getId(), 
                                                         "caracteristicaId" => $objPerfil->getId(), 
                                                         "estado"           => "Activo"));
                if(is_object($objProdCaractPerfil))
                {
                    $objPlanDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                       ->findOneBy(array("productoId" => $objProdInternet->getId(), 
                                                         "planId"     => $objServicio->getPlanId(),
                                                         "estado"     => $objServicio->getPlanId()->getEstado()
                                                        )
                                                  );
                    $objPlanProdCaractPerfil = $this->emComercial->getRepository('schemaBundle:InfoPlanProductoCaract')
                                                    ->findOneBy(array("planDetId"                 => $objPlanDet->getId(), 
                                                                      "productoCaracterisiticaId" => $objProdCaractPerfil->getId()
                                                                     )
                                                               );
                    if(is_object($objPlanProdCaractPerfil))
                    {
                        $strPerfilPlan = $objPlanProdCaractPerfil->getValor();
                    }
                }
                
                if(empty($strPerfilPlan))
                {
                    $strMensaje = "No existe un perfil registrado para el Internet Residencial.";
                    throw new \Exception($strMensaje);
                }
                $strAprovisionamiento                             = $this->geTipoAprovisionamiento($intIdOlt);
                $strPerfil                                        = $strPerfilPlan;
                $arrayParamsPerfilEquiv                           = array();
                $arrayParamsPerfilEquiv['elementoOltId']          = $intIdOlt;
                $arrayParamsPerfilEquiv['idPlan']                 = null;
                $arrayParamsPerfilEquiv['valorPerfil']            = $strPerfil;
                $arrayParamsPerfilEquiv['tipoAprovisionamiento']  = $strAprovisionamiento;
                $arrayParamsPerfilEquiv['marca']                  = $strMarcaOlt;
                $arrayParamsPerfilEquiv['empresaCod']             = $strCodEmpresa;
                $arrayParamsPerfilEquiv['tipoNegocio']            = 'HOMETNP';
                $arrayParamsPerfilEquiv['tipoEjecucion']          = 'FLUJO';
                $strPerfilEquivalente   = $this->getPerfilPlanEquivalente($arrayParamsPerfilEquiv);
                if(isset($strPerfilEquivalente) && !empty($strPerfilEquivalente))
                {
                    if($strMarcaOlt === 'HUAWEI')
                    {
                        $arrayProdCaracts   = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                ->getProductoCaracteristica(  $strCodEmpresa,
                                                                                              $intIdOlt,
                                                                                              $strPerfilEquivalente,
                                                                                              "HOMETNP",
                                                                                              "SI");
                        if (count($arrayProdCaracts)==4)
                        {
                            foreach($arrayProdCaracts as $arrayProdCaract)
                            {
                                $intIdProdCaract         = $arrayProdCaract['ID_PRODUCTO_CARACTERISITICA'];
                                $strValor                = $arrayProdCaract['DETALLE_VALOR'];
                                $objInfoServProdCaractPerfil= new InfoServicioProdCaract();
                                $objInfoServProdCaractPerfil->setServicioId($intIdServicio);
                                $objInfoServProdCaractPerfil->setProductoCaracterisiticaId($intIdProdCaract);
                                $objInfoServProdCaractPerfil->setValor($strValor);
                                $objInfoServProdCaractPerfil->setFeCreacion(new \DateTime('now'));
                                $objInfoServProdCaractPerfil->setUsrCreacion($strUsrCreacion);
                                $objInfoServProdCaractPerfil->setEstado("Activo");
                                $this->emComercial->persist($objInfoServProdCaractPerfil);
                                $this->emComercial->flush();
                            }
                        }
                        else
                        {
                            $strMensaje = "No se puede realizar la asignación, debido a que faltan "
                                          ."variables de configuracion para el OLT";
                            throw new \Exception($strMensaje);
                        }                                
                    }
                    else
                    {
                        $strMensaje = "No se puede realizar la asignación, tecnología no permitida.";
                        throw new \Exception($strMensaje);
                    }
                }
                
                $strEstadoSolicitud = "Asignada";
                $strEstadoServicio  = "Asignada";
                
                $objDetalleSolicitud->setEstado($strEstadoSolicitud);
                $this->emComercial->persist($objDetalleSolicitud);
                $this->emComercial->flush();

                $objDetalleSolHist = new InfoDetalleSolHist();
                $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);

                $objUltimoDetalleSolHist = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                                             ->findOneDetalleSolicitudHistorial($intIdDetSolPlanif,
                                                                                                'Planificada');
                if(is_object($objUltimoDetalleSolHist))
                {
                    $objDetalleSolHist->setFeIniPlan($objUltimoDetalleSolHist->getFeIniPlan());
                    $objDetalleSolHist->setFeFinPlan($objUltimoDetalleSolHist->getFeFinPlan());
                    $strObservacionLimite = $objUltimoDetalleSolHist->getObservacion();
                    $strObservacionLimite = substr($strObservacionLimite,0,1499);
                    $objDetalleSolHist->setObservacion($strObservacionLimite);
                }
                $objDetalleSolHist->setIpCreacion($strIpCreacion);
                $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHist->setUsrCreacion($strUsrCreacion);
                $objDetalleSolHist->setEstado($strEstadoSolicitud);

                $this->emComercial->persist($objDetalleSolHist);
                $this->emComercial->flush();

                $objServicio->setEstado($strEstadoServicio);
                $this->emComercial->persist($objServicio);
                $this->emComercial->flush();

                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setObservacion('');
                $objServicioHistorial->setEstado($strEstadoServicio);

                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();

                $this->emInfraestructura->commit();
                $this->emComercial->commit();

                $strStatus  = "OK";
                $strMensaje = "Se guardaron correctamente los Recursos de Red";
            }
            else
            {
                $strMensaje = "No se han enviado todos los parámetros obligatorios para la asignación de red";
                throw new \Exception($strMensaje);
            }
        } 
        catch (\Exception $e) 
        {
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->rollback();
            }
          
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
            }
            
            $this->emInfraestructura->close();
            $this->emComercial->close();
            
            $this->utilServicio->insertError(   "Telcos+",
                                                "RecursosDeRedService->asignarRecursosRedInternetLite",
                                                $e->getMessage(),
                                                $strUsrCreacion,
                                                $strIpCreacion
                                               );
            $strMensaje  = "No se ha podido asignar los recursos de red. Por favor notifique a Sistemas!";

        }
        $arrayRespuestaFinal = array('status' => $strStatus, 'mensaje' => $strMensaje);
        return $arrayRespuestaFinal;
    }
    
    /**
     * Función que sirve para grabar los recursos de Red para el producto Internet Small Business
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 04-12-2017
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 25-04-2018 Se agrega flujo para IPs Adicionales para un servicio Small Business
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 16-05-2018 Se agrega flujo para considerar servicios Small Business con Ips adicionales con OLTs TELLION 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 16-07-2018 Se agrega parámetro strTipoAccion para reutilizar la función al trasladar servicios Small Business sin realizar 
     *                         los respectivos commits
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 08-02-2019 Se agrega la asignación de recursos de red para servicios TelcoHome y sus ips adicionales
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 06-03-2019 Se parametriza por nombre técnico el parámetros MAPEO_VELOCIDAD_PERFIL para distinguir entre configuraciones 
     *                          de servicios TelcoHome y Small Business y se valida nuevo tipo de negocio HOMETN para servicios TelcoHome
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.6 06-03-2019 Se agrega lógica para que siga el flujo correcto del producto small business centros comerciales.
     *
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 27-06-2019 Se agrega la programación para la asignación de recursos para servicios Small Business y TelcoHome cuando 
     *                          sea una SOLICITUD MIGRACION
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.7 17-07-2019 Se adiciona lógica para soportar el flujo de small business razón social.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.8 18-10-2019 - Se agrega el modelo de equipo ZTE a las validaciones para obtener los recursos de red.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 21-01-2020 - Se agrega la programación para asignar recursos de red a un servicio Small Business que será migrado a ZTE,
     *                            así también se corrige las validaciones para que no se guarde la característica perfil para servicios ZTE
     *                            y se procede a guardar los valores de las capacidades como características del servicio
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.9 19-03-2020 -Se agrega lógica para adicionar IP TELEWORKER.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.10 16-04-2020 Se agrega programación para IP TELCOTEACHER en base a programación agregada para IP TELEWORKER
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.11 28-04-2020 Se modifica programación por reestructuración de servicios Small Business y TelcoHome 
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.12 14-12-2020 Se agrea creación de login auxiliar para los servicios TN bajo red Gpon
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.13 20-09-2021 Se agrega validación de velocidad para productos internet safe
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.0 08-07-2022  Se valida si el producto es IP INTERNET VPNoGPON, se obtiene el id del servicio del INTERNET VPNoGPON
     *                          de la característica de la relación del producto principal
     *  
     * @param array $arrayParametros
     * @return array $arrayRespuestaFinal
     */
    public function asignarRecursosRedInternetLite($arrayParametros)
    {
        $intIdDetSolPlanif                  = $arrayParametros['idDetSolPlanif'];
        $strJsonCaracteristicas             = $arrayParametros['datosIps'];
        $intIdSplitter                      = $arrayParametros['idSplitter'];
        $intIdInterfaceSplitter             = $arrayParametros['idInterfaceSplitter'];
        $strMarcaOlt                        = $arrayParametros['marcaOlt'];
        $strUsrCreacion                     = $arrayParametros['usrCreacion'];
        $strIpCreacion                      = $arrayParametros['ipCreacion'];
        $strCodEmpresa                      = $arrayParametros['idEmpresa'];
        $strPrefijoEmpresa                  = $arrayParametros['prefijoEmpresa'];
        $strNombreTecnico                   = $arrayParametros['nombreTecnico'];
        $intIdOltNuevoMigracion             = $arrayParametros['idOltNuevoMigracion'] ? $arrayParametros['idOltNuevoMigracion'] : 0;
        $intIdInterfaceOltNuevoMigracion    = $arrayParametros['idInterfaceOltNuevoMigracion'] ? $arrayParametros['idInterfaceOltNuevoMigracion'] : 0;
        $strTipoAccion                      = $arrayParametros['strTipoAccion'] ? $arrayParametros['strTipoAccion'] : "";
        $intContadorIps                     = 0;
        $strListaIps                        = "";
        $strPerfilEquivalente               = "";
        $strAprovisionamiento               = "";
        $strTipoNegocio                     = "";
        $strStatus                          = "ERROR";
        $strMensaje                         = "";
        $strDescripcionCaractVelocidad      = "";
        
        if($strTipoAccion !== "TRASLADO")
        {
            $this->emComercial->beginTransaction();
            $this->emInfraestructura->beginTransaction();
        }

        try
        {
            if(isset($strNombreTecnico) && !empty($strNombreTecnico) 
                && isset($intIdDetSolPlanif) && !empty($intIdDetSolPlanif) && $intIdDetSolPlanif > 0
                && ((($strNombreTecnico === "INTERNET SMALL BUSINESS" || $strNombreTecnico === "TELCOHOME")
                      && isset($intIdSplitter) && !empty($intIdSplitter) && $intIdSplitter > 0
                      && isset($intIdInterfaceSplitter) && !empty($intIdInterfaceSplitter) && $intIdInterfaceSplitter > 0)
                    || $strNombreTecnico === "IPSB"))
            {
                $objDetalleSolicitud    = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                            ->findOneById($intIdDetSolPlanif);
                if(!is_object($objDetalleSolicitud))
                {
                    $strMensaje = "No se ha podido obtener la solicitud";
                    throw new \Exception($strMensaje);
                }

                $objServicio   = $objDetalleSolicitud->getServicioId();
                if(!is_object($objServicio))
                {
                    $strMensaje = "No se ha podido obtener el servicio asociado";
                    throw new \Exception($strMensaje);
                }
                $intIdServicio = $objServicio->getId();
                
                $objTipoSolicitud = $objDetalleSolicitud->getTipoSolicitudId();
                if(!is_object($objTipoSolicitud))
                {
                    $strMensaje = "No se ha podido obtener el tipo de solicitud";
                    throw new \Exception($strMensaje);
                }
                
                $strTipoSolicitud = $objTipoSolicitud->getDescripcionSolicitud();
                if($strTipoSolicitud === 'SOLICITUD MIGRACION')
                {
                    if(empty($intIdOltNuevoMigracion) || empty($intIdInterfaceOltNuevoMigracion))
                    {
                        $strMensaje = "No se han enviado todos los parámetros obligatorios para la migración";
                        throw new \Exception($strMensaje);
                    }
                    
                    if($strNombreTecnico === "IPSB")
                    {
                        $strMensaje = "No existe un flujo definido para la migración de este servicio";
                        throw new \Exception($strMensaje);
                    }
                }
                if( $objServicio->getEstado() == "Activo" && $strTipoSolicitud !== 'SOLICITUD MIGRACION' )
                {
                    $strMensaje = "El servicio Actualmente se encuentra con estado Activo, no es posible Asignar Recursos.";
                    throw new \Exception($strMensaje);
                }

                $objPunto   = $objServicio->getPuntoId();
                if(!is_object($objPunto))
                {
                    $strMensaje = "No se ha podido obtener el punto asociado al servicio";
                    throw new \Exception($strMensaje);
                }
                $intIdPunto = $objPunto->getId();

                $objProducto    = $objServicio->getProductoId();
                if(!is_object($objProducto))
                {
                    $strMensaje = "No se ha podido obtener el producto asociado al servicio";
                    throw new \Exception($strMensaje);
                }
                
                $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                        ->findOneByServicioId($intIdServicio);
                if(!is_object($objServicioTecnico))
                {
                    $strMensaje = "No se ha podido obtener el servicio técnico del servicio asociado";
                    throw new \Exception($strMensaje);
                }
                
                $strCaractRelProdPrincipal = "";
                if($strNombreTecnico === "IPSB")
                {
                    $arrayParamsInfoProds   = array("strValor1ParamsProdsTnGpon"    => "PRODUCTOS_RELACIONADOS_INTERNET_IP",
                                                    "strCodEmpresa"                 => $strCodEmpresa,
                                                    "intIdProductoIp"               => $objProducto->getId());
                    $arrayInfoMapeoProds    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                ->obtenerParametrosProductosTnGpon($arrayParamsInfoProds);
                    if(isset($arrayInfoMapeoProds) && !empty($arrayInfoMapeoProds))
                    {
                        $intIdProdInternet              = $arrayInfoMapeoProds[0]["intIdProdInternet"];         
                        $strNombreTecnicoProdInternet   = $arrayInfoMapeoProds[0]["strNombreTecnicoProdInternet"];
                        $strDescripcionProdInternet     = $arrayInfoMapeoProds[0]["strDescripcionProdInternet"];
                        $strCaractRelProdPrincipal      = $arrayInfoMapeoProds[0]["strCaractRelProdIp"];
                    }
                    else
                    {
                        $strMensaje = "No se ha podido obtener el producto Internet asociado a este servicio";
                        throw new \Exception($strMensaje);
                    }
                }
                else
                {
                    $intIdProdInternet              = $objProducto->getId();
                    $strNombreTecnicoProdInternet   = $objProducto->getNombreTecnico();
                    $strDescripcionProdInternet     = $objProducto->getDescripcionProducto();
                }

                if(!isset($intIdProdInternet) || empty($intIdProdInternet))
                {
                    $strMensaje = "No se ha podido obtener el id del producto Internet asociado a este servicio";
                    throw new \Exception($strMensaje);
                }
                
                $objProdInternet    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProdInternet);
                if(!is_object($objProdInternet))
                {
                    $strMensaje = "No se ha podido obtener el producto principal para la asignación de red";
                    throw new \Exception($strMensaje);
                }
                
                $boolValidaDescripcion     = false;
                $arrayParamsNombreDescrip  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('LISTA_VELOCIDAD_PRODUCTO_ISB',
                                                                                 '',
                                                                                 '',
                                                                                 '',
                                                                                 $objProdInternet->getDescripcionProducto(),
                                                                                 '',
                                                                                 '',
                                                                                 '',
                                                                                 '',
                                                                                 $strCodEmpresa);
                if(isset($arrayParamsNombreDescrip) && !empty($arrayParamsNombreDescrip))
                {
                    $boolValidaDescripcion = true;
                    $strValor1Descripcion  = $arrayParamsNombreDescrip['valor2'];
                }
                                               
                $arrayParamsCaractsVelocidad    = array("strValor1ParamsProdsTnGpon"    => "DESCRIPCION_CARACT_VELOCIDAD_X_NOMBRE_TECNICO",
                                                        "strCodEmpresa"                 => $strCodEmpresa,
                                                        "strValor2NombreTecnico"        => $strNombreTecnico,
                                                        "boolValidaDescripcion"         => $boolValidaDescripcion,
                                                        "strValor1Descripcion"          => $strValor1Descripcion);
                $arrayInfoCaractsVelocidad      = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                    ->obtenerParametrosProductosTnGpon($arrayParamsCaractsVelocidad);
                if(isset($arrayInfoCaractsVelocidad) && !empty($arrayInfoCaractsVelocidad))
                {
                    $strDescripcionCaractVelocidad = $arrayInfoCaractsVelocidad[0]["strDescripcionCaractVelocidad"];
                    if(!isset($strDescripcionCaractVelocidad) || empty($strDescripcionCaractVelocidad))
                    {
                        $strMensaje = "No se ha podido obtener la descripción de la característica de velocidad asociada al servicio";
                        throw new \Exception($strMensaje);
                    }
                }
                else
                {
                    $strMensaje = "No se ha podido obtener la característica de velocidad asociada al servicio";
                    throw new \Exception($strMensaje);
                }
                
                if($strNombreTecnico === "INTERNET SMALL BUSINESS" || $strNombreTecnico === "TELCOHOME")
                {
                    $intIdOlt                   = $objServicioTecnico->getElementoId();
                    $intIdInterfaceSplitterAnt  = $objServicioTecnico->getInterfaceElementoConectorId();
                    if((empty($intIdOlt) || (!empty($intIdOlt) && is_numeric($intIdOlt) && $intIdOlt <= 0))
                        || (empty($intIdInterfaceSplitterAnt) 
                            || (!empty($intIdInterfaceSplitterAnt) && is_numeric($intIdInterfaceSplitterAnt) && $intIdInterfaceSplitterAnt <= 0)))
                    {
                        $strMensaje = "No se han encontrado los recursos de factibilidad asignados";
                        throw new \Exception($strMensaje);
                    }
                    
                    if (empty($strMarcaOlt))
                    {
                        $strMensaje = "No existe un flujo definido para el ".$strDescripcionProdInternet." sin la marca del OLT";
                        throw new \Exception($strMensaje);
                    }
                    
                    $objServProdCaracTipoNegocio = $this->servicioGeneral->getServicioProductoCaracteristica(   $objServicio,
                                                                                                                "Grupo Negocio",
                                                                                                                $objProducto);
                    if(is_object($objServProdCaracTipoNegocio))
                    {
                        $strTipoNegocio = $objServProdCaracTipoNegocio->getValor();
                        if(($strNombreTecnico === "INTERNET SMALL BUSINESS" && $strTipoNegocio !== "PYMETN")
                            || ($strNombreTecnico === "TELCOHOME" && $strTipoNegocio !== "HOMETN"))
                        {
                            $strMensaje = "No existe un flujo definido de ".$strDescripcionProdInternet." con el grupo de negocio "
                                          .$strTipoNegocio;
                            throw new \Exception($strMensaje);
                        }
                    }
                    else
                    {
                        $strMensaje = "No existe grupo de negocio asociado a este servicio";
                        throw new \Exception($strMensaje);
                    }
                    
                    if ($intIdInterfaceSplitterAnt !== intval($intIdInterfaceSplitter))
                    {
                        $objInterfaceSplitterAntes = $this->emInfraestructura
                                                          ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                          ->findOneById($intIdInterfaceSplitterAnt);
                        if(is_object($objInterfaceSplitterAntes))
                        {
                            $objInterfaceSplitterAntes->setEstado("not connect");
                            $this->emInfraestructura->persist($objInterfaceSplitterAntes);
                            $this->emInfraestructura->flush();
                        }                       
                    }
                    
                    $objInterfaceSplitter   = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                      ->findOneById($intIdInterfaceSplitter);
                    if(!is_object($objInterfaceSplitter))
                    {
                        $strMensaje = "No se ha podido obtener la interface del splitter que se desea asignar";
                        throw new \Exception($strMensaje);
                    }
                    
                    $strEstadoInterfaceSplitter = "";
                    if($strTipoSolicitud === 'SOLICITUD MIGRACION')
                    {
                        $objServicioTecnico->setElementoId($intIdOltNuevoMigracion);
                        $strEstadoInterfaceSplitter = 'connected';
                        $intIdOlt                   = $intIdOltNuevoMigracion;
                        $strAprovisionamiento       = "CNR";
                    }
                    else
                    {
                        $strEstadoInterfaceSplitter = 'reserved';
                        $strAprovisionamiento       = $this->geTipoAprovisionamiento($intIdOlt);
                    }
                    $objInterfaceSplitter->setEstado($strEstadoInterfaceSplitter);
                    $this->emInfraestructura->persist($objInterfaceSplitter);
                    $this->emInfraestructura->flush();

                    $objServicioTecnico->setElementoConectorId($intIdSplitter);
                    $objServicioTecnico->setInterfaceElementoConectorId($intIdInterfaceSplitter);
                    $this->emComercial->persist($objServicioTecnico);
                    $this->emComercial->flush();

                    $objServProdCaracVelocidad = $this->servicioGeneral->getServicioProductoCaracteristica( $objServicio,
                                                                                                            $strDescripcionCaractVelocidad,
                                                                                                            $objProducto);
                    if(!is_object($objServProdCaracVelocidad))
                    {
                        $strMensaje = "El servicio no tiene definido una velocidad";
                        throw new \Exception($strMensaje);
                    }
                    $strVelocidadServicio       = $objServProdCaracVelocidad->getValor();
                    $strValorSpcCapacidad       = "";

                    if(($strMarcaOlt === "HUAWEI" || $strMarcaOlt === "ZTE") 
                        || ($strMarcaOlt === "TELLION" && $strTipoSolicitud !== 'SOLICITUD MIGRACION'))
                    {
                        $arrayMapeoVelocidadPerfil  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                      ->getOne('MAPEO_VELOCIDAD_PERFIL', 
                                                                               '', 
                                                                               '', 
                                                                               '', 
                                                                               $strVelocidadServicio, 
                                                                               '', 
                                                                               $strNombreTecnicoProdInternet, 
                                                                               '', 
                                                                               '', 
                                                                               $strCodEmpresa);
                        if( isset($arrayMapeoVelocidadPerfil['valor2']) 
                           && !empty($arrayMapeoVelocidadPerfil['valor2']) )
                        {
                            if($strMarcaOlt === "ZTE")
                            {
                                $strValorSpcCapacidad = $arrayMapeoVelocidadPerfil['valor4'];
                            }
                            else
                            {
                                $strPerfil                                        = $arrayMapeoVelocidadPerfil['valor2'];
                                $arrayParamsPerfilEquiv                           = array();
                                $arrayParamsPerfilEquiv['elementoOltId']          = $intIdOlt;
                                $arrayParamsPerfilEquiv['idPlan']                 = null;
                                $arrayParamsPerfilEquiv['valorPerfil']            = $strPerfil;
                                $arrayParamsPerfilEquiv['tipoAprovisionamiento']  = $strAprovisionamiento;
                                $arrayParamsPerfilEquiv['marca']                  = $strMarcaOlt;
                                $arrayParamsPerfilEquiv['empresaCod']             = $strCodEmpresa;
                                $arrayParamsPerfilEquiv['tipoNegocio']            = $strTipoNegocio;
                                $arrayParamsPerfilEquiv['tipoEjecucion']          = 'FLUJO';
                                $strPerfilEquivalente   = $this->getPerfilPlanEquivalente($arrayParamsPerfilEquiv);
                                if(empty($strPerfilEquivalente))
                                {
                                    $strMensaje = "No se ha podido obtener el perfil equivalente del perfil ".$strPerfil;
                                    throw new \Exception($strMensaje);
                                }
                            }
                        }
                        else
                        {
                            $strMensaje = "No se ha mapeado un perfil para la velocidad del servicio";
                            throw new \Exception($strMensaje);
                        }  
                    }
                    
                    if($strMarcaOlt === 'HUAWEI')
                    {
                        $strTipoNegocioGetProdCaract    = $strTipoNegocio."|".$strNombreTecnico;
                        $arrayProdCaracts               = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                            ->getProductoCaracteristica(  $strCodEmpresa,
                                                                                                          $intIdOlt,
                                                                                                          $strPerfilEquivalente,
                                                                                                          $strTipoNegocioGetProdCaract,
                                                                                                          "NO");
                        if(is_object($objServicio))
                        {
                            foreach($arrayProdCaracts as $arrayProdCaract)
                            {
                                $objAdmiProductoCaracteristica = $this->emComercial
                                                                      ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                      ->findOneById($arrayProdCaract['ID_PRODUCTO_CARACTERISITICA']);
                                if(is_object($objAdmiProductoCaracteristica) &&
                                    $objAdmiProductoCaracteristica->getProductoId()->getDescripcionProducto() ===
                                    $objServicio->getProductoId()->getDescripcionProducto())
                                {
                                    $arrayAdProCarac[] = array(
                                                            'ID_PRODUCTO_CARACTERISITICA' => $objAdmiProductoCaracteristica->getId(),
                                                            'DESCRIPCION_CARACTERISTICA'  => $arrayProdCaract
                                                                                             ['DESCRIPCION_CARACTERISTICA'],
                                                            'DETALLE_VALOR'               => $arrayProdCaract['DETALLE_VALOR']
                                                          );
                                }
                            }
                            $arrayProdCaracts = array();
                            $arrayProdCaracts = $arrayAdProCarac;
                        }
                        if (count($arrayProdCaracts)==4)
                        {
                            foreach($arrayProdCaracts as $arrayProdCaract)
                            {
                                $intIdProdCaract         = $arrayProdCaract['ID_PRODUCTO_CARACTERISITICA'];
                                $strValor                = $arrayProdCaract['DETALLE_VALOR'];
                                $objInfoServProdCaractPerfil= new InfoServicioProdCaract();
                                $objInfoServProdCaractPerfil->setServicioId($intIdServicio);
                                $objInfoServProdCaractPerfil->setProductoCaracterisiticaId($intIdProdCaract);
                                $objInfoServProdCaractPerfil->setValor($strValor);
                                $objInfoServProdCaractPerfil->setFeCreacion(new \DateTime('now'));
                                $objInfoServProdCaractPerfil->setUsrCreacion($strUsrCreacion);
                                $objInfoServProdCaractPerfil->setEstado("Activo");
                                $this->emComercial->persist($objInfoServProdCaractPerfil);
                                $this->emComercial->flush();
                            }
                        }
                        else
                        {
                            $strMensaje = "No se puede realizar la asignación, debido a que faltan "
                                          ."variables de configuracion para el OLT";
                            throw new \Exception($strMensaje);
                        }                                
                    }
                    else if($strMarcaOlt === 'TELLION')
                    {
                        if($strTipoSolicitud === 'SOLICITUD MIGRACION')
                        {
                            $strMensaje = "No se puede realizar la asignación, tecnología ".$strMarcaOlt." no permitida.";
                            throw new \Exception($strMensaje);
                        }
                        else
                        {
                            $objCaractPerfil    = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                    ->findOneBy(array(  "descripcionCaracteristica" => "PERFIL", 
                                                                                        "estado"                    => "Activo"));
                            if(!is_object($objCaractPerfil))
                            {
                                $strMensaje = "No existe la característica PERFIL";
                                throw new \Exception($strMensaje);
                            }

                            $objProdCaractPerfil    = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                        ->findOneBy(array("productoId"          => 
                                                                                          $objProdInternet->getId(),
                                                                                          "caracteristicaId"    => $objCaractPerfil->getId(),
                                                                                          "estado"              => "Activo"));
                            if(!is_object($objProdCaractPerfil))
                            {
                                $strMensaje = "No existe relación entre el producto ".$objProdInternet->getDescripcionProducto()
                                            ." y la característica PERFIL";
                                throw new \Exception($strMensaje);
                            }

                            $objInfoServProdCaractPerfil = new InfoServicioProdCaract();
                            $objInfoServProdCaractPerfil->setServicioId($intIdServicio);
                            $objInfoServProdCaractPerfil->setProductoCaracterisiticaId($objProdCaractPerfil->getId());
                            $objInfoServProdCaractPerfil->setValor($strPerfilEquivalente);
                            $objInfoServProdCaractPerfil->setFeCreacion(new \DateTime('now'));
                            $objInfoServProdCaractPerfil->setUsrCreacion($strUsrCreacion);
                            $objInfoServProdCaractPerfil->setEstado("Activo");
                            $this->emComercial->persist($objInfoServProdCaractPerfil);
                            $this->emComercial->flush();
                        }
                    }
                    else if($strMarcaOlt === 'ZTE')
                    {
                        if(!isset($strValorSpcCapacidad) || empty($strValorSpcCapacidad) )
                        {
                            $strMensaje = "No se ha podido obtener el valor de las capacidades para la tecnología ZTE";
                            throw new \Exception($strMensaje);
                        }
                        
                        $objCaractCapacidad1    = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                    ->findOneBy(array(  "descripcionCaracteristica" => "CAPACIDAD1", 
                                                                                        "estado"                    => "Activo"));
                        if(!is_object($objCaractCapacidad1))
                        {
                            $strMensaje = "No existe la característica CAPACIDAD1";
                            throw new \Exception($strMensaje);
                        }
                        
                        $objProdCaractCapacidad1    = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                        ->findOneBy(array("productoId"          => 
                                                                                          $objProdInternet->getId(),
                                                                                          "caracteristicaId"    => $objCaractCapacidad1->getId(),
                                                                                          "estado"              => "Activo"));
                        if(!is_object($objProdCaractCapacidad1))
                        {
                            $strMensaje = "No existe relación entre el producto ".$strDescripcionProdInternet
                                        ." y la característica CAPACIDAD1";
                            throw new \Exception($strMensaje);
                        }
                        
                        $objCaractCapacidad2    = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                    ->findOneBy(array(  "descripcionCaracteristica" => "CAPACIDAD2", 
                                                                                        "estado"                    => "Activo"));
                        if(!is_object($objCaractCapacidad2))
                        {
                            $strMensaje = "No existe la característica CAPACIDAD2";
                            throw new \Exception($strMensaje);
                        }
                        
                        $objProdCaractCapacidad2    = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                        ->findOneBy(array("productoId"          => 
                                                                                          $objProdInternet->getId(),
                                                                                          "caracteristicaId"    => $objCaractCapacidad2->getId(),
                                                                                          "estado"              => "Activo"));
                        if(!is_object($objProdCaractCapacidad2))
                        {
                            $strMensaje = "No existe relación entre el producto ".$strDescripcionProdInternet
                                        ." y la característica CAPACIDAD2";
                            throw new \Exception($strMensaje);
                        }
                        
                        $objInfoServProdCaractCapacidad1 = new InfoServicioProdCaract();
                        $objInfoServProdCaractCapacidad1->setServicioId($intIdServicio);
                        $objInfoServProdCaractCapacidad1->setProductoCaracterisiticaId($objProdCaractCapacidad1->getId());
                        $objInfoServProdCaractCapacidad1->setValor($strValorSpcCapacidad);
                        $objInfoServProdCaractCapacidad1->setFeCreacion(new \DateTime('now'));
                        $objInfoServProdCaractCapacidad1->setUsrCreacion($strUsrCreacion);
                        $objInfoServProdCaractCapacidad1->setEstado("Activo");
                        $this->emComercial->persist($objInfoServProdCaractCapacidad1);
                        $this->emComercial->flush();
                            
                        $objInfoServProdCaractCapacidad2 = new InfoServicioProdCaract();
                        $objInfoServProdCaractCapacidad2->setServicioId($intIdServicio);
                        $objInfoServProdCaractCapacidad2->setProductoCaracterisiticaId($objProdCaractCapacidad2->getId());
                        $objInfoServProdCaractCapacidad2->setValor($strValorSpcCapacidad);
                        $objInfoServProdCaractCapacidad2->setFeCreacion(new \DateTime('now'));
                        $objInfoServProdCaractCapacidad2->setUsrCreacion($strUsrCreacion);
                        $objInfoServProdCaractCapacidad2->setEstado("Activo");
                        $this->emComercial->persist($objInfoServProdCaractCapacidad2);
                        $this->emComercial->flush();
                    }
                    else
                    {
                        $strMensaje = "No existe un flujo definido para la tecnología de este servicio";
                        throw new \Exception($strMensaje);
                    }
                    
                    if($strTipoSolicitud === 'SOLICITUD MIGRACION')
                    {
                        $objCaractInterfaceOltTellion   = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                            ->findOneBy(array(  "descripcionCaracteristica" => 
                                                                                                "INTERFACE ELEMENTO TELLION", 
                                                                                                "estado"                    => "Activo"));
                        if(!is_object($objCaractInterfaceOltTellion))
                        {
                            $strMensaje = "No existe la característica INTERFACE ELEMENTO TELLION";
                            throw new \Exception($strMensaje);
                        }
                        $objProdCaractInterfaceOltTellion   = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                                ->findOneBy(array(  "productoId"          => 
                                                                                                    $objProdInternet->getId(),
                                                                                                    "caracteristicaId"    => 
                                                                                                    $objCaractInterfaceOltTellion->getId(),
                                                                                                    "estado"              => "Activo"));
                        if(!is_object($objProdCaractInterfaceOltTellion))
                        {
                            $strMensaje = "No existe relación entre el producto ".$strDescripcionProdInternet
                                        ." y la característica INTERFACE ELEMENTO TELLION";
                            throw new \Exception($strMensaje);
                        }
                        $objSpcInterfaceOltTellion = new InfoServicioProdCaract();
                        $objSpcInterfaceOltTellion->setServicioId($intIdServicio);
                        $objSpcInterfaceOltTellion->setProductoCaracterisiticaId($objProdCaractInterfaceOltTellion->getId());
                        $objSpcInterfaceOltTellion->setValor($objServicioTecnico->getInterfaceElementoId());
                        $objSpcInterfaceOltTellion->setFeCreacion(new \DateTime('now'));
                        $objSpcInterfaceOltTellion->setUsrCreacion($strUsrCreacion);
                        $objSpcInterfaceOltTellion->setEstado("Activo");
                        $this->emComercial->persist($objSpcInterfaceOltTellion);
                        $this->emComercial->flush();

                        if($strNombreTecnico === "INTERNET SMALL BUSINESS")
                        {                            
                            $arrayRespuestaServiciosIp  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                            ->getServiciosIpbyPunto(array(  "intIdPunto"        => $intIdPunto,
                                                                                                            "intIdServicio"     => $intIdServicio,
                                                                                                            "strPrefijoEmpresa" => $strPrefijoEmpresa,
                                                                                                            "strCodEmpresa"     => $strCodEmpresa,
                                                                                                            "intIdProdInternet" => $intIdProdInternet
                                                                                                            )
                                                                                                    );
                            $strStatusServiciosIp       = $arrayRespuestaServiciosIp['status'];
                            if($strStatusServiciosIp !== "OK")
                            {
                                $strMensaje = $arrayRespuestaServiciosIp['mensaje'];
                                throw new \Exception($strMensaje);
                            }

                            $arrayServiciosIps = $arrayRespuestaServiciosIp['serviciosIps'];
                            if(count($arrayServiciosIps) > 0)
                            {
                                $objCaractMigrado   = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                        ->findOneBy(array(  "descripcionCaracteristica" => "MIGRADO",
                                                                                            "estado"                    => "Activo"));
                                if(!is_object($objCaractMigrado))
                                {
                                    $strMensaje = "No existe la característica MIGRADO";
                                    throw new \Exception($strMensaje);
                                }

                                foreach($arrayServiciosIps as $arrayServicioIp)
                                {
                                    $objProductoConIp               = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                                        ->find($arrayServicioIp['PRODUCTO_ID']);
                                    if(!is_object($objProductoConIp))
                                    {
                                        $strMensaje = "No existe producto Ip";
                                        throw new \Exception($strMensaje);
                                    }
                                    $objProdIpCaractInterfaceOlt    = $this->emComercial
                                                                           ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                           ->findOneBy(array(   "productoId"        =>
                                                                                                $objProductoConIp->getId(),
                                                                                                "caracteristicaId"  =>
                                                                                                $objCaractInterfaceOltTellion->getId(),
                                                                                                "estado"            => "Activo"));
                                    if(!is_object($objProdIpCaractInterfaceOlt))
                                    {
                                        $strMensaje = "No existe relación entre el producto ".$objProductoConIp->getDescripcionProducto()
                                                    ." y la característica INTERFACE ELEMENTO TELLION";
                                        throw new \Exception($strMensaje);
                                    }
                                    $objSpcInterfaceOltTellion = new InfoServicioProdCaract();
                                    $objSpcInterfaceOltTellion->setServicioId($arrayServicioIp['ID_SERVICIO']);
                                    $objSpcInterfaceOltTellion->setProductoCaracterisiticaId($objProdIpCaractInterfaceOlt->getId());
                                    $objSpcInterfaceOltTellion->setValor($objServicioTecnico->getInterfaceElementoId());
                                    $objSpcInterfaceOltTellion->setFeCreacion(new \DateTime('now'));
                                    $objSpcInterfaceOltTellion->setUsrCreacion($strUsrCreacion);
                                    $objSpcInterfaceOltTellion->setEstado("Activo");
                                    $this->emComercial->persist($objSpcInterfaceOltTellion);
                                    $this->emComercial->flush();

                                    $objProdIpCaractMigrado = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                                ->findOneBy(array(  "productoId"        =>
                                                                                                    $objProductoConIp->getId(),
                                                                                                    "caracteristicaId"  =>
                                                                                                    $objCaractMigrado->getId(),
                                                                                                    "estado"            => "Activo"));

                                    $objSpcMigrado  = new InfoServicioProdCaract();
                                    $objSpcMigrado->setServicioId($arrayServicioIp['ID_SERVICIO']);
                                    $objSpcMigrado->setProductoCaracterisiticaId($objProdIpCaractMigrado->getId());
                                    $objSpcMigrado->setValor('NO');
                                    $objSpcMigrado->setFeCreacion(new \DateTime('now'));
                                    $objSpcMigrado->setUsrCreacion($strUsrCreacion);
                                    $objSpcMigrado->setEstado("Activo");
                                    $this->emComercial->persist($objSpcMigrado);
                                    $this->emComercial->flush();

                                    $objServicioTecnicoIp   = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                                ->findOneByServicioId($arrayServicioIp['ID_SERVICIO']);
                                    if(is_object($objServicioTecnicoIp))
                                    {
                                        $objServicioTecnicoIp->setElementoId($intIdOltNuevoMigracion);
                                        $objServicioTecnicoIp->setInterfaceElementoId($intIdInterfaceOltNuevoMigracion);
                                        $objServicioTecnicoIp->setElementoConectorId($intIdSplitter);
                                        $objServicioTecnicoIp->setInterfaceElementoConectorId($intIdInterfaceSplitter);
                                        $this->emComercial->persist($objServicioTecnicoIp);
                                        $this->emComercial->flush();
                                    }
                                    else
                                    {
                                        $objServicioIp  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                            ->find($arrayServicioIp['ID_SERVICIO']);
                                        $objServicioTecnicoIp  = new InfoServicioTecnico();
                                        $objServicioTecnicoIp->setUltimaMillaId($objServicioTecnico->getUltimaMillaId());
                                        $objServicioTecnicoIp->setServicioId($objServicioIp);
                                        $objServicioTecnicoIp->setElementoId($intIdOltNuevoMigracion);
                                        $objServicioTecnicoIp->setInterfaceElementoId($intIdInterfaceOltNuevoMigracion);
                                        $objServicioTecnicoIp->setElementoConectorId($intIdSplitter);
                                        $objServicioTecnicoIp->setInterfaceElementoConectorId($intIdInterfaceSplitter);
                                        $objServicioTecnicoIp->setElementoContenedorId($objServicioTecnico->getElementoContenedorId());
                                        $this->emComercial->persist($objServicioTecnicoIp);
                                        $this->emComercial->flush();
                                    }
                                }
                            }
                        }
                        $objServicioTecnico->setElementoId($intIdOltNuevoMigracion);
                        $objServicioTecnico->setInterfaceElementoId($intIdInterfaceOltNuevoMigracion);
                        $objServicioTecnico->setElementoConectorId($intIdSplitter);
                        $this->emComercial->persist($objServicioTecnico);
                        $this->emComercial->flush();
                    }
                    $strEstadoSolicitud = "Asignada";
                }
                else if($strNombreTecnico === "IPSB")
                {
                    //obtener el servicio tecnico del internet por la caracteristica de la relacion
                    $objServicioTecnicoInternet = null;
                    $objSpcRelProdPrincipal     = null;
                    if(isset($strCaractRelProdPrincipal) && !empty($strCaractRelProdPrincipal))
                    {
                        $objSpcRelProdPrincipal = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                            $strCaractRelProdPrincipal,
                                                                                                            $objProducto);
                        if(is_object($objSpcRelProdPrincipal))
                        {
                            $objServicioTecnicoInternet = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                    ->findOneByServicioId($objSpcRelProdPrincipal->getValor());
                        }
                    }
                    else
                    {
                        $objServicioTecnicoInternet = $this->getServicioTecnicoByPuntoId(array( "intIdPunto"        => $intIdPunto,
                                                                                                "intIdProdInternet" => $intIdProdInternet));
                    }
                    if (is_object($objServicioTecnicoInternet))
                    {
                        $intIdOlt               = $objServicioTecnicoInternet->getElementoId();
                        $strAprovisionamiento   = $this->geTipoAprovisionamiento($intIdOlt);
                        $objElementoOlt         = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdOlt);
                        $strMarcaOlt            = $objElementoOlt->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
                        if (empty($strMarcaOlt))
                        {
                            $strMensaje = "No existe un flujo definido para la IP con un ".$strDescripcionProdInternet." sin marca del OLT";
                            throw new \Exception($strMensaje);
                        }
                        $objServicioTecnico->setElementoId($intIdOlt);
                        $objServicioTecnico->setInterfaceElementoId($objServicioTecnicoInternet->getInterfaceElementoId());
                        $objServicioTecnico->setElementoContenedorId($objServicioTecnicoInternet->getElementoContenedorId());
                        $objServicioTecnico->setElementoConectorId($objServicioTecnicoInternet->getElementoConectorId());
                        $objServicioTecnico->setInterfaceElementoConectorId($objServicioTecnicoInternet->getInterfaceElementoConectorId());
                        $this->emComercial->persist($objServicioTecnico);
                        $this->emComercial->flush();
                        if($strMarcaOlt === "TELLION")
                        {
                            //obtener el servicio de internet por la caracteristica de la relacion
                            $objServicioInternet = null;
                            if(is_object($objSpcRelProdPrincipal))
                            {
                                $objServicioInternet = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                            ->find($objSpcRelProdPrincipal->getValor());
                            }
                            else
                            {
                                $objServicioInternet    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                            ->findOneBy(array(  "productoId"    => $objProdInternet,
                                                                                                "puntoId"       => $objPunto,
                                                                                                "estado"        => "Activo"));
                            }
                            if (!is_object($objServicioInternet))
                            {
                                $strMensaje = "No existe un servicio ".$strDescripcionProdInternet." en estado activo para este punto";
                                throw new \Exception($strMensaje);
                            }
                            
                            $objServProdCaracTipoNegocio = $this->servicioGeneral->getServicioProductoCaracteristica(   $objServicioInternet,
                                                                                                                        "Grupo Negocio",
                                                                                                                        $objProdInternet);
                            if(is_object($objServProdCaracTipoNegocio))
                            {
                                $strTipoNegocio = $objServProdCaracTipoNegocio->getValor();
                                if($strTipoNegocio !== "PYMETN")
                                {
                                    $strMensaje = "No existe un flujo definido de ".$strDescripcionProdInternet." con el grupo de negocio "
                                                  .$strTipoNegocio;
                                    throw new \Exception($strMensaje);
                                }
                            }
                            else
                            {
                                $strMensaje = "No existe grupo de negocio asociado al servicio principal ".$strDescripcionProdInternet;
                                throw new \Exception($strMensaje);
                            }
                            
                            $objServProdCaracVelocidad  = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioInternet, 
                                                                                                                    $strDescripcionCaractVelocidad, 
                                                                                                                    $objProdInternet);
                            
                            if(!is_object($objServProdCaracVelocidad))
                            {
                                $strMensaje = "El servicio principal ".$strDescripcionProdInternet." no tiene definido una velocidad";
                                throw new \Exception($strMensaje);
                            }
                            $strVelocidadServicio       = $objServProdCaracVelocidad->getValor();
                            $arrayMapeoVelocidadPerfil  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                          ->getOne( 'MAPEO_VELOCIDAD_PERFIL', 
                                                                                    '', 
                                                                                    '', 
                                                                                    '', 
                                                                                    $strVelocidadServicio, 
                                                                                    '', 
                                                                                    $strNombreTecnicoProdInternet, 
                                                                                    '', 
                                                                                    '', 
                                                                                    $strCodEmpresa);
                            if( isset($arrayMapeoVelocidadPerfil['valor2']) && !empty($arrayMapeoVelocidadPerfil['valor2']) )
                            {
                                $strPerfilMapeo = $arrayMapeoVelocidadPerfil['valor2'];
                            }
                            else
                            {
                                $strMensaje = "No se ha mapeado un perfil para la velocidad del servicio";
                                throw new \Exception($strMensaje);
                            }
                            
                            $strAprovisionamiento                             = $this->geTipoAprovisionamiento($intIdOlt);
                            $arrayParamsPerfilEquiv                           = array();
                            $arrayParamsPerfilEquiv['elementoOltId']          = $intIdOlt;
                            $arrayParamsPerfilEquiv['idPlan']                 = null;
                            $arrayParamsPerfilEquiv['valorPerfil']            = $strPerfilMapeo;
                            $arrayParamsPerfilEquiv['tipoAprovisionamiento']  = $strAprovisionamiento;
                            $arrayParamsPerfilEquiv['marca']                  = $strMarcaOlt;
                            $arrayParamsPerfilEquiv['empresaCod']             = $strCodEmpresa;
                            $arrayParamsPerfilEquiv['tipoNegocio']            = $strTipoNegocio;
                            $arrayParamsPerfilEquiv['tipoEjecucion']          = 'FLUJO';
                            $strPerfilEquivalente   = $this->getPerfilPlanEquivalente($arrayParamsPerfilEquiv);
                            if(isset($strPerfilEquivalente) && !empty($strPerfilEquivalente))
                            {
                                $objCaractPerfil    = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                        ->findOneBy(array(  "descripcionCaracteristica" => "PERFIL", 
                                                                                            "estado"                    => "Activo"));
                                if(!is_object($objCaractPerfil))
                                {
                                    $strMensaje = "No existe la característica PERFIL";
                                    throw new \Exception($strMensaje);
                                }
                                $objProdCaractPerfil    = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                            ->findOneBy(array("productoId"          => $objProdInternet->getId(),
                                                                                              "caracteristicaId"    => $objCaractPerfil->getId(),
                                                                                              "estado"              => "Activo"));
                                if(!is_object($objProdCaractPerfil))
                                {
                                    $strMensaje = "No existe relación entre el producto ".$strDescripcionProdInternet." y la característica PERFIL";
                                    throw new \Exception($strMensaje);
                                }
                                $objInfoServProdCaractPerfil = new InfoServicioProdCaract();
                                $objInfoServProdCaractPerfil->setServicioId($intIdServicio);
                                $objInfoServProdCaractPerfil->setProductoCaracterisiticaId($objProdCaractPerfil->getId());
                                $objInfoServProdCaractPerfil->setValor($strPerfilEquivalente);
                                $objInfoServProdCaractPerfil->setFeCreacion(new \DateTime('now'));
                                $objInfoServProdCaractPerfil->setUsrCreacion($strUsrCreacion);
                                $objInfoServProdCaractPerfil->setEstado("Activo");
                                $this->emComercial->persist($objInfoServProdCaractPerfil);
                                $this->emComercial->flush();
                            }
                            else
                            {
                                $strMensaje = "No se ha podido obtener el perfil equivalente del perfil ".$strPerfilMapeo;
                                throw new \Exception($strMensaje);
                            }
                        }
                    }
                    else
                    {
                        $strMensaje = "No se ha podido obtener el servicio técnico del ".$strDescripcionProdInternet." de la Ip que se desea asignar";
                        throw new \Exception($strMensaje);
                    }
                    
                    $strEstadoSolicitud = "Finalizada";
                }
                else
                {
                    $strMensaje = "No existe un flujo definido para el servicio";
                    throw new \Exception($strMensaje);
                }
                $strEstadoIp        = "Reservada";
                $strEstadoServicio  = "Asignada";
                if($strTipoSolicitud === 'SOLICITUD MIGRACION')
                {
                    /**
                     * Se obtienen características SCOPE y en caso de existir más de una caracteristica SCOPE 
                     * sólo se deja en estado Activo la de mayor secuencial de creación (ID_SERVICIO_PROD_CARACT)
                     * y se eliminan los demás registros
                     */
                    $objCaractScope = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array(  "descripcionCaracteristica" => "SCOPE", 
                                                                            "estado"                    => "Activo"));
                    if(!is_object($objCaractScope))
                    {
                        $strMensaje = "No existe la característica SCOPE";
                        throw new \Exception($strMensaje);
                    }
                    $objProdCaractScope     = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                ->findOneBy(array("productoId"          => $objProdInternet->getId(),
                                                                                  "caracteristicaId"    => $objCaractScope->getId(),
                                                                                  "estado"              => "Activo"));
                    if(is_object($objProdCaractScope))
                    {
                        $arraySpcScopeServicio  = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                    ->findBy(array( "servicioId"                  => $objServicio->getId(),
                                                                                    "productoCaracterisiticaId"   => $objProdCaractScope->getId(),
                                                                                    "estado"                      => "Activo"),
                                                                                    array('id' => 'DESC'));

                        if (count($arraySpcScopeServicio) > 1)
                        {
                            $objProdCaractScopeUlt = $arraySpcScopeServicio[0];
                            foreach($arraySpcScopeServicio as $objProductoCaracteristicaScope)
                            {
                                if ($objProdCaractScopeUlt->getId() != $objProductoCaracteristicaScope->getId())
                                {
                                    $objProductoCaracteristicaScope->setEstado("Eliminado");
                                    $objProductoCaracteristicaScope->setFeUltMod(new \DateTime('now'));
                                    $objProductoCaracteristicaScope->setUsrUltMod($strUsrCreacion);
                                    $this->emComercial->persist($objProductoCaracteristicaScope);
                                    $this->emComercial->flush();
                                }
                            }
                        }
                    }
                }
                else
                {
                    $arrayCaracteristicas   = array();
                    $mixCaracteristicas     = json_decode($strJsonCaracteristicas);
                    if($mixCaracteristicas)
                    {
                        $arrayCaracteristicas = $mixCaracteristicas->caracteristicas;
                        if($arrayCaracteristicas)
                        {
                            for($intIndice = 0; $intIndice < count($arrayCaracteristicas); $intIndice++)
                            {
                                $strTipoIp = $arrayCaracteristicas[$intIndice]->tipo;
                                $objInfoIp = new InfoIp();
                                $objInfoIp->setIp($arrayCaracteristicas[$intIndice]->ip);
                                $objInfoIp->setMascara($arrayCaracteristicas[$intIndice]->mascara);
                                $objInfoIp->setGateway($arrayCaracteristicas[$intIndice]->gateway);
                                $objInfoIp->setTipoIp($strTipoIp);
                                $objInfoIp->setVersionIp('IPV4');
                                $objInfoIp->setServicioId($intIdServicio);
                                $objInfoIp->setIpCreacion($strIpCreacion);
                                $objInfoIp->setFeCreacion(new \DateTime('now'));
                                $objInfoIp->setUsrCreacion($strUsrCreacion);
                                $objInfoIp->setEstado($strEstadoIp);
                                $strListaIps    = $strListaIps . $arrayCaracteristicas[$intIndice]->ip . ' ';
                                $intContadorIps = $intContadorIps + 1;
                                $this->emInfraestructura->persist($objInfoIp);
                                $this->emInfraestructura->flush();

                                if ($strAprovisionamiento === "CNR")
                                {
                                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                                    $objProducto, 
                                                                                                    "SCOPE", 
                                                                                                    $arrayCaracteristicas[$intIndice]->scope, 
                                                                                                    $strUsrCreacion);
                                }
                            }
                        }
                    }
                }

                $objDetalleSolicitud->setEstado($strEstadoSolicitud);
                $this->emComercial->persist($objDetalleSolicitud);
                $this->emComercial->flush();

                $objDetalleSolHist = new InfoDetalleSolHist();
                $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                if($intContadorIps > 1)
                {
                    $strObservacionIps = ($strListaIps ? 'Se asignaron las ips: ' . $strListaIps : "");
                }
                else if($intContadorIps === 1)
                {
                    $strObservacionIps = ($strListaIps ? 'Se asigno la ip: ' . $strListaIps : "");
                }
                else
                {
                    $strObservacionIps = "";
                }

                $objUltimoDetalleSolHist = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                                             ->findOneDetalleSolicitudHistorial($intIdDetSolPlanif,
                                                                                                'Planificada');
                if(is_object($objUltimoDetalleSolHist))
                {
                    $objDetalleSolHist->setFeIniPlan($objUltimoDetalleSolHist->getFeIniPlan());
                    $objDetalleSolHist->setFeFinPlan($objUltimoDetalleSolHist->getFeFinPlan());
                    $strObservacionLimite = $objUltimoDetalleSolHist->getObservacion() . ' ' 
                                                       . $strObservacionIps;
                    $strObservacionLimite = substr($strObservacionLimite,0,1499);
                    $objDetalleSolHist->setObservacion($strObservacionLimite);
                }
                $objDetalleSolHist->setIpCreacion($strIpCreacion);
                $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHist->setUsrCreacion($strUsrCreacion);
                $objDetalleSolHist->setEstado($strEstadoSolicitud);
                $this->emComercial->persist($objDetalleSolHist);
                $this->emComercial->flush();

                if($strTipoSolicitud !== 'SOLICITUD MIGRACION')
                {
                    $objServicio->setEstado($strEstadoServicio);
                    $this->emComercial->persist($objServicio);
                    $this->emComercial->flush();

                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicio);
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setObservacion($strObservacionIps);
                    $objServicioHistorial->setEstado($strEstadoServicio);
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                }
                
                if($strTipoAccion !== "TRASLADO")
                {
                    $this->emInfraestructura->commit();
                    $this->emComercial->commit();
                }

                $strStatus  = "OK";
                $strMensaje = "Se guardaron correctamente los Recursos de Red";
            }
            else
            {
                $strMensaje = "No se han enviado todos los parámetros obligatorios para la asignación de red";
                throw new \Exception($strMensaje);
            }
            
            //Consultamos el nombre tecnico para generar login auxiliar
            $objParametroCabGeneraLoginAux = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                            ->findOneBy(array('nombreParametro' => 'GENERAR_LOGIN_AUXILIAR_GPON_TN',
                                                              'estado'          => 'Activo'));
            if (is_object($objParametroCabGeneraLoginAux))
            {
                $arrayParDetGeneraLoginAux = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->findBy(array('parametroId'   => $objParametroCabGeneraLoginAux->getId(),
                                                               'estado'        => 'Activo'));
                if (is_array($arrayParDetGeneraLoginAux) && !empty($arrayParDetGeneraLoginAux))
                {
                    $arrayGeneraLoginAux = explode(",",$arrayParDetGeneraLoginAux[0]->getValor1());
                }
            }
            
            //Consultamos el nombre tecnico y la empresa para generar login auxiliar para los productos son de TN, pero bajo red Gpon
            if($strPrefijoEmpresa  == "TN" && in_array($strNombreTecnico,$arrayGeneraLoginAux))   
            {
                //Generacion de Login Auxiliar al Servicio            
                $this->servicioGeneral->generarLoginAuxiliar($intIdServicio);
            }
            
        }
        catch (\Exception $e) 
        {
            if($strTipoAccion !== "TRASLADO")
            {
                if ($this->emInfraestructura->getConnection()->isTransactionActive())
                {
                    $this->emInfraestructura->rollback();
                }

                if ($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->rollback();
                }

                $this->emInfraestructura->close();
                $this->emComercial->close();
            }
            
            $this->utilServicio->insertError(   "Telcos+",
                                                "RecursosDeRedService->asignarRecursosRedInternetLite",
                                                $e->getMessage(),
                                                $strUsrCreacion,
                                                $strIpCreacion
                                               );
            $strMensaje  = "No se ha podido asignar los recursos de red. Por favor notifique a Sistemas!";

        }
        $arrayRespuestaFinal = array('status' => $strStatus, 'mensaje' => $strMensaje);
        return $arrayRespuestaFinal;
    }

    /**
     * Función que sirve para grabar los recursos de Red para el producto Datos Safe City
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 26-05-2021
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 16-03-2022 - Se verifica la vrf para los servicios cámara safecity.
     *
     * @param array $arrayParametros
     * @return array $arrayRespuestaFinal
     */
    public function asignarRecursosRedDatosGpon($arrayParametros)
    {
        $intIdDetalleSolicitud  = $arrayParametros['idDetalleSolicitud'];
        $intIdSplitter          = isset($arrayParametros['idSplitter']) ? $arrayParametros['idSplitter'] : null;
        $intIdInterfaceSplitter = isset($arrayParametros['idInterfaceSplitter']) ? $arrayParametros['idInterfaceSplitter'] : null;
        $strEmpresaCod          = $arrayParametros['idEmpresa'];
        $strUsrCreacion         = $arrayParametros['usrCreacion'];
        $strIpCreacion          = $arrayParametros['ipCreacion'];
        $strObservacion         = "";
        $strMensajeAsigCam      = "";

        $this->emComercial->beginTransaction();
        $this->emInfraestructura->beginTransaction();
        try
        {
            $objDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdDetalleSolicitud);
            if(!is_object($objDetalleSolicitud))
            {
                throw new \Exception("No se ha podido obtener la solicitud, por favor notificar a Sistemas.");
            }
            $objServicio         = $objDetalleSolicitud->getServicioId();
            if(!is_object($objServicio))
            {
                throw new \Exception("No se ha podido obtener el servicio, por favor notificar a Sistemas.");
            }
            $objProducto         = $objServicio->getProductoId();
            $objServicioTecnico  = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                ->findOneBy(array( "servicioId" => $objServicio->getId()));
            //obtengo el elemento pe
            $objElementoPe  = $this->servicioGeneral->getPeByOlt(array("intIdElemento" => $objServicioTecnico->getElementoId(),
                                                                       "intIdServicio" => $objServicio->getId()));
            if(!is_object($objElementoPe))
            {
                throw new \Exception("No se ha podido obtener el elemento pe, por favor notificar a Sistemas.");
            }

            /***VERIFICAR VRF CAMARA***/
            $objPunto               = $objServicio->getPuntoId();
            //obtener servicio camara
            $arrayParServCamara = array(
                "objPunto"       => $objPunto,
                "strParametro"   => "PRODUCTO_ADICIONAL_CAMARA",
                "strCodEmpresa"  => $strEmpresaCod,
                "strUsrCreacion" => $strUsrCreacion,
                "strIpCreacion"  => $strIpCreacion
            );
            $arrayResultSerCamara = $this->servicioGeneral->getServicioGponPorProducto($arrayParServCamara);
            if($arrayResultSerCamara["status"] == "OK" && is_object($arrayResultSerCamara["objServicio"]))
            {
                $objServCamara     = $arrayResultSerCamara["objServicio"];
                $objPerEmpCaractVpnGpon = null;
                $objDetalleEleVlanGpon  = null;
                //verificar vrf
                $objCaractVrfVideo = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array("descripcionCaracteristica" => "VRF_VIDEO_SAFECITY",
                                                                      "estado"                    => "Activo"));
                if(!is_object($objCaractVrfVideo))
                {
                    throw new \Exception("No se ha podido obtener la característica VRF VIDEO SAFECITY, ".
                                         "por favor notificar a Sistemas.");
                }
                $arrayPerEmpCaractVrfVideo = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                        ->findBy(array("caracteristicaId"    => $objCaractVrfVideo->getId(),
                                       "personaEmpresaRolId" => $objPunto->getPersonaEmpresaRolId()->getId(),
                                       "estado"              => "Activo"));
                if(!empty($arrayPerEmpCaractVrfVideo) && count($arrayPerEmpCaractVrfVideo) >= 1)
                {
                    $objPerEmpCaractVpnGpon = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                    ->findOneBy(array("id"      => $arrayPerEmpCaractVrfVideo[0]->getValor(),
                                                                      "estado"  => "Activo"));
                    //verificar si hay mas de una vrf
                    if(count($arrayPerEmpCaractVrfVideo) > 1)
                    {
                        $strMensajeAsigCam = "<br>El cliente posee más de una VRF de video para las cámaras.";
                    }
                }
                if(!is_object($objPerEmpCaractVpnGpon))
                {
                    throw new \Exception("No existe la VPN para los servicios ".
                                         $objServCamara->getProductoId()->getDescripcionProducto().
                                         ", por favor de ingresarla.");
                }
                //verificar vlan
                $objCaractTipoRed = $this->servicioGeneral->getServicioProductoCaracteristica($objServCamara,
                                                                                              "TIPO_RED",
                                                                                              $objServCamara->getProductoId());
                if(!is_object($objCaractTipoRed))
                {
                    throw new \Exception("No se ha podido obtener el tipo de red del servicio, ".
                                         "por favor notificar a Sistemas.");
                }
                $arrayParametrosVlan = array('intIdPersonaEmpresaRol' => $objPunto->getPersonaEmpresaRolId()->getId(),
                                             'strEmpresaCod'          => $strEmpresaCod,
                                             'strCaractVlan'          => 'VLAN',
                                             'strNombre'              => $objElementoPe->getNombreElemento(),
                                             'strTipoRed'             => $objCaractTipoRed->getValor());
                $arrayResultadoVlan  = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                    ->getVlansCliente($arrayParametrosVlan);
                if(!empty($arrayResultadoVlan) && isset($arrayResultadoVlan["total"])
                   && isset($arrayResultadoVlan["data"]) && !empty($arrayResultadoVlan["data"])
                   && count($arrayResultadoVlan["data"]) >= 1)
                {
                    $objPerEmpCaractVlanGpon = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                    ->find($arrayResultadoVlan["data"][0]['id']);
                    if(is_object($objPerEmpCaractVlanGpon))
                    {
                        //obtener vlan
                        $objDetalleEleVlanGpon = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                       ->find($objPerEmpCaractVlanGpon->getValor());
                        //verificar si hay mas de una vlan
                        if(count($arrayResultadoVlan["data"]) > 1)
                        {
                            $strMensajeAsigCam = $strMensajeAsigCam."<br>El cliente posee más de una VLAN GPON para los servicios SafeCity.";
                        }
                    }
                }
                if(!is_object($objDetalleEleVlanGpon))
                {
                    throw new \Exception("No existe la VLAN para los servicios ".
                                         $objServCamara->getProductoId()->getDescripcionProducto().
                                         ", por favor de ingresarla.");
                }
            }
            /***FIN VERIFICAR VRF CAMARA***/

            //verifico si esta vacia el id del splitter
            if(empty($intIdSplitter))
            {
                $intIdSplitter = $objServicioTecnico->getElementoConectorId();
            }
            //verifico si esta vacia el id de la interface del splitter
            if(empty($intIdInterfaceSplitter))
            {
                $intIdInterfaceSplitter = $objServicioTecnico->getInterfaceElementoConectorId();
            }

            //obtengo el elemento olt
            $objElemento         = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                            ->find($objServicioTecnico->getElementoId());
            if(!is_object($objElemento))
            {
                throw new \Exception("No se ha podido obtener el elemento, por favor notificar a Sistemas.");
            }
            //obtengo la interface elemento
            $objInterfaceElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                            ->find($objServicioTecnico->getInterfaceElementoId());
            if(!is_object($objInterfaceElemento))
            {
                throw new \Exception("No se ha podido obtener la interface del elemento, por favor notificar a Sistemas.");
            }
            //obtengo el elemento pe
            $objElementoPe       = $this->servicioGeneral->getPeByOlt(array("intIdElemento" => $objElemento->getId(),
                                                                            "intIdServicio" => $objServicio->getId()));
            if(!is_object($objElementoPe))
            {
                throw new \Exception("No se ha podido obtener el elemento pe, por favor notificar a Sistemas.");
            }

            //obtengo el elemento conector
            $objElementoConector = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                            ->find($intIdSplitter);
            if(!is_object($objElementoConector))
            {
                throw new \Exception("No se ha podido obtener el elemento conector, por favor notificar a Sistemas.");
            }
            //obtengo la interface elemento conector
            $objInterfaceElementoConector = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                            ->find($intIdInterfaceSplitter);
            if(!is_object($objInterfaceElementoConector))
            {
                throw new \Exception("No se ha podido obtener la interface elemento conector, por favor notificar a Sistemas.");
            }
            $intIdInterfaceSplitterAnt = $objServicioTecnico->getInterfaceElementoConectorId();
            if ($intIdInterfaceSplitterAnt != intval($intIdInterfaceSplitter))
            {
                $objInterfaceSplitterAntes = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                    ->find($intIdInterfaceSplitterAnt);
                if(is_object($objInterfaceSplitterAntes))
                {
                    $objInterfaceSplitterAntes->setEstado("not connect");
                    $this->emInfraestructura->persist($objInterfaceSplitterAntes);
                    $this->emInfraestructura->flush();
                }
            }
            //se conecta el puerto
            $objInterfaceElementoConector->setEstado("reserved");
            $this->emInfraestructura->persist($objInterfaceElementoConector);
            $this->emInfraestructura->flush();
            //seteo la interface y elemento conector
            $objServicioTecnico->setElementoConectorId($intIdSplitter);
            $objServicioTecnico->setInterfaceElementoConectorId($intIdInterfaceSplitter);
            $this->emComercial->persist($objServicioTecnico);
            $this->emComercial->flush();

            //obtengo la velocidad del servicio
            $objProdCaractVelocidad = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                "VELOCIDAD_GPON",
                                                                                                $objProducto);
            if(!is_object($objProdCaractVelocidad))
            {
                throw new \Exception("No se ha podido obtener la característica de la velocidad gpon del servicio, ".
                                     "por favor notificar a Sistemas.");
            }

            //obtengo la vrf del producto
            $strNombreVrf        = "";
            $arrayParVrfProducto = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                    'COMERCIAL',
                                                                                                    '',
                                                                                                    'VRF PRODUCTO',
                                                                                                    $objProducto->getId(),
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '');
            if(isset($arrayParVrfProducto) && !empty($arrayParVrfProducto)
               && isset($arrayParVrfProducto['valor2']) && !empty($arrayParVrfProducto['valor2']))
            {
                 $strNombreVrf = $arrayParVrfProducto['valor2'];
            }
            else
            {
                throw new \Exception("No se ha podido obtener la vrf del producto, por favor notificar a Sistemas.");
            }
            $objCaractVrf = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array("descripcionCaracteristica" => "VRF",
                                                                  "estado"                    => "Activo"));
            $objPersonaEmpCaractVrf = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                ->findOneBy(array("caracteristicaId"    => $objCaractVrf->getId(),
                                                                  "valor"               => $strNombreVrf,
                                                                  "estado"              => "Activo"));
            if(!is_object($objPersonaEmpCaractVrf))
            {
                throw new \Exception("No se ha podido obtener la vrf del Datos SafeCity, por favor notificar a Sistemas.");
            }
            //se graba característica vrf
            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                           $objProducto,
                                                                           "VRF",
                                                                           $objPersonaEmpCaractVrf->getId(),
                                                                           $strUsrCreacion);

            //obtengo el detalle de la vlan
            $strVlanSafecityGpon = "VLAN SAFECITY GPON";
            $arrayParametrosSubred = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                    'COMERCIAL',
                                                                                                    '',
                                                                                                    'NOMBRES PARAMETROS SUBREDES Y VLANS',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '');
            if(isset($arrayParametrosSubred) && !empty($arrayParametrosSubred)
               && isset($arrayParametrosSubred['valor6']) && !empty($arrayParametrosSubred['valor6']))
            {
                $strVlanSafecityGpon = $arrayParametrosSubred['valor6'];
            }
            //obtengo la vlan
            $objDetalleEleVlan  = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                        ->findONeBy(array("elementoId"    => $objElemento->getId(),
                                                                          "detalleNombre" => $strVlanSafecityGpon,
                                                                          "estado"        => "Activo"));
            if(!is_object($objDetalleEleVlan))
            {
                throw new \Exception("No se ha podido obtener la vlan del elemento, por favor notificar a Sistemas.");
            }
            $strNombreVlan = $objDetalleEleVlan->getDetalleValor();
            //se graba característica vlan
            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                           $objProducto,
                                                                           "VLAN",
                                                                           $objDetalleEleVlan->getId(),
                                                                           $strUsrCreacion);

            //seteo la observación
            $strObservacion .= "Informaci&oacute;n de Backbone<br/>";
            $strObservacion .= "Recursos Asignados<br/>";
            $strObservacion .= "</br><b>Datos de Factibilidad</b><br/>";
            $strObservacion .= "&#10140; <b>Nombre Elemento Padre:</b> ".$objElementoPe->getNombreElemento()."<br/>";
            $strObservacion .= "&#10140; <b>Nombre Elemento:</b> ".$objElemento->getNombreElemento()."<br/>";
            $strObservacion .= "&#10140; <b>Nombre Interfaz Elemento:</b> ".$objInterfaceElemento->getNombreInterfaceElemento()."<br/>";
            $strObservacion .= "&#10140; <b>Nombre Elemento Conector:</b> ".$objElementoConector->getNombreElemento()."<br/>";
            $strObservacion .= "&#10140; <b>Puerto Elemento Conector:</b> ".$objInterfaceElementoConector->getNombreInterfaceElemento()."<br/>";
            $strObservacion .= "&#10140; <b> Vlan: </b>".$strNombreVlan."<br/>";

            //se actualiza el estado del servicio
            $objServicio->setEstado("Asignada");
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();

            //agregar historial del servicio
            $objInfoHistorial = new InfoServicioHistorial();
            $objInfoHistorial->setServicioId($objServicio);
            $objInfoHistorial->setEstado("Asignada");
            $objInfoHistorial->setUsrCreacion($strUsrCreacion);
            $objInfoHistorial->setFeCreacion(new \DateTime('now'));
            $objInfoHistorial->setIpCreacion($strIpCreacion);
            $objInfoHistorial->setObservacion($strObservacion);
            $this->emComercial->persist($objInfoHistorial);
            $this->emComercial->flush();

            //se actualiza estado a la solicitud
            $objDetalleSolicitud->setEstado("Asignada");
            $this->emComercial->persist($objDetalleSolicitud);
            $this->emComercial->flush();

            //agregar historial a la solicitud
            $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
            $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
            $objDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
            $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
            $objDetalleSolicitudHistorial->setUsrCreacion($strUsrCreacion);
            $objDetalleSolicitudHistorial->setEstado("Asignada");
            $objDetalleSolicitudHistorial->setObservacion($strObservacion);
            $this->emComercial->persist($objDetalleSolicitudHistorial);
            $this->emComercial->flush();

            //Generacion de Login Auxiliar al Servicio
            $this->servicioGeneral->generarLoginAuxiliar($objServicio->getId());

            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->commit();
                $this->emInfraestructura->getConnection()->close();
            }
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->commit();
                $this->emComercial->getConnection()->close();
            }
            $strStatus  = "OK";
            $strMensaje = "Se guardaron correctamente los Recursos de Red".$strMensajeAsigCam;
        }
        catch (\Exception $e)
        {
            $this->utilServicio->insertError("Telcos+",
                                            "RecursosDeRedService->asignarRecursosRedDatosGpon",
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
                $this->emInfraestructura->getConnection()->close();
            }
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
        }

        $arrayRespuestaFinal = array('status'=>$strStatus, 'mensaje'=> $strMensaje);
        return $arrayRespuestaFinal;
    }

    /**
     * Función que sirve para grabar los recursos de Red para los servicios adicionales Safe City
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 01-07-2021
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 07-08-2021 - Se obtiene la vlan, vrf y subred por el producto del servicio
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 05-09-2021 - Se valida los recursos de red para servicios WIFI
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 11-05-2022 - Se valida la asignación de la vrf y vlan para servicios CAM SAFECITY
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.4 15-06-2022 - Se pasa el id de la empresa al método para obtener la vrf y vlan.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.5 01-08-2022 - Se agrega el parámetro para validar si es Cámara VPN GPON para no obtener la vrf y vlan.
     *
     * @param array $arrayParametros
     * @return array $arrayRespuestaFinal
     */
    public function guardarRecursosRedServiciosGpon($arrayParametros)
    {
        $serviceSoporte         = $arrayParametros['serviceSoporte'];
        $intIdDetalleSolicitud  = $arrayParametros['idDetalleSolicitud'];
        $booleanCamVpnSafeCity  = isset($arrayParametros['strEsCamVpnGpon']) && $arrayParametros['strEsCamVpnGpon'] === "S";
        $intIdVrf               = $arrayParametros['intIdVrf'];
        $intIdVlan              = $arrayParametros['intIdVlan'];
        $strUsrCreacion         = $arrayParametros['usrCreacion'];
        $strIpCreacion          = $arrayParametros['ipCreacion'];
        $strCodEmpresa          = $arrayParametros['idEmpresa'];

        $this->emComercial->beginTransaction();
        $this->emInfraestructura->beginTransaction();
        try
        {
            $objDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdDetalleSolicitud);
            if(!is_object($objDetalleSolicitud))
            {
                throw new \Exception("No se ha podido obtener la solicitud, por favor notificar a Sistemas.");
            }
            $objServicio         = $objDetalleSolicitud->getServicioId();
            if(!is_object($objServicio))
            {
                throw new \Exception("No se ha podido obtener el servicio, por favor notificar a Sistemas.");
            }
            $objProducto         = $objServicio->getProductoId();
            $objServicioTecnico  = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                ->findOneBy(array( "servicioId" => $objServicio->getId()));

            //obtengo el elemento olt
            $objElemento         = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                            ->find($objServicioTecnico->getElementoId());
            if(!is_object($objElemento))
            {
                throw new \Exception("No se ha podido obtener el elemento, por favor notificar a Sistemas.");
            }
            //obtengo la interface elemento
            $objInterfaceElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                            ->find($objServicioTecnico->getInterfaceElementoId());
            if(!is_object($objInterfaceElemento))
            {
                throw new \Exception("No se ha podido obtener la interface del elemento, por favor notificar a Sistemas.");
            }
            //obtengo el elemento pe
            $objElementoPe       = $this->servicioGeneral->getPeByOlt(array("intIdElemento" => $objElemento->getId(),
                                                                            "intIdServicio" => $objServicio->getId()));
            if(!is_object($objElementoPe))
            {
                throw new \Exception("No se ha podido obtener el elemento pe, por favor notificar a Sistemas.");
            }
            //obtengo servicio principal
            $objCaractServicioPrincipal = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                    'RELACION_SERVICIOS_GPON_SAFECITY',$objProducto);
            if(!is_object($objCaractServicioPrincipal))
            {
                throw new \Exception("No se ha podido obtener la característica del servicio Datos SafeCity, por favor notificar a Sistemas.");
            }
            $objServicioPrincipal = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($objCaractServicioPrincipal->getValor());
            if(!is_object($objServicioPrincipal))
            {
                throw new \Exception("No se ha podido obtener el servicio Datos SafeCity, por favor notificar a Sistemas.");
            }
            //obtner servicio tecnico principal
            $objServicioTecnicoPrincipal = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                ->findOneBy(array( "servicioId" => $objServicioPrincipal->getId()));

            //verificar si el servicio no es WIFI
            if($objProducto->getNombreTecnico() != "SAFECITYWIFI")
            {
                //obtener ip disponible
                $arrayResultadoSubred = $this->servicioGeneral
                                            ->getIpDisponiblePorServicio(array("objServicio"    => $objServicio,
                                                                               "strCodEmpresa"  => $strCodEmpresa,
                                                                               "strUsrCreacion" => $strUsrCreacion,
                                                                               "strIpCreacion"  => $strIpCreacion));
                if($arrayResultadoSubred['status'] != "OK")
                {
                    throw new \Exception($arrayResultadoSubred['mensaje']);
                }
                $objSubredServicioAdd = $arrayResultadoSubred['objSubred'];
                $strIpServicioAdd     = $arrayResultadoSubred['strIpServicio'];

                //se graba ip del servicio adicional
                $objIpSerAdd = new InfoIp();
                $objIpSerAdd->setIp($strIpServicioAdd);
                $objIpSerAdd->setSubredId($objSubredServicioAdd->getId());
                $objIpSerAdd->setServicioId($objServicio->getId());
                $objIpSerAdd->setMascara($objSubredServicioAdd->getMascara());
                $objIpSerAdd->setFeCreacion(new \DateTime('now'));
                $objIpSerAdd->setUsrCreacion($strUsrCreacion);
                $objIpSerAdd->setIpCreacion($strIpCreacion);
                $objIpSerAdd->setEstado("Reservada");
                $objIpSerAdd->setTipoIp("LAN");
                $objIpSerAdd->setVersionIp("IPV4");
                $this->emInfraestructura->persist($objIpSerAdd);
                $this->emInfraestructura->flush();
            }

            //obtengo los datos de vlan y vrf por servicio
            if($objProducto->getNombreTecnico() == "SAFECITYWIFI")
            {
                $arrayResultadoVlanVrf  = $this->servicioGeneral
                                                ->getVlanVrfPorServicio(array("objServicio"   => $objServicio,
                                                                              "strCodEmpresa" => $strCodEmpresa));
                if($arrayResultadoVlanVrf['status'] != "OK")
                {
                    throw new \Exception($arrayResultadoVlanVrf['mensaje']);
                }
                $objDetalleEleVlan      = $arrayResultadoVlanVrf['objDetalleElementoVlan'];
                $objPersonaEmpCaractVrf = $arrayResultadoVlanVrf['objPersonaEmpCaractVrf'];
            }
            elseif($objProducto->getNombreTecnico() == "SAFECITYDATOS")
            {
                if(empty($intIdVlan))
                {
                    throw new \Exception("No se pudo obtener el id de la vlan, por favor notificar a Sistemas.");
                }
                if(empty($intIdVrf))
                {
                    throw new \Exception("No se pudo obtener el id de la vrf, por favor notificar a Sistemas.");
                }
                $objPersonaEmpCaractVlan = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->find($intIdVlan);
                if(!is_object($objPersonaEmpCaractVlan))
                {
                    throw new \Exception("No se ha podido obtener la vlan del cliente, por favor notificar a Sistemas.");
                }
                $objDetalleEleVlan       = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                            ->find($objPersonaEmpCaractVlan->getValor());
                if(!is_object($objDetalleEleVlan))
                {
                    throw new \Exception("No se ha podido obtener la vlan, por favor notificar a Sistemas.");
                }
                $objPersonaEmpCaractVrf = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->find($intIdVrf);
                if(!is_object($objPersonaEmpCaractVrf))
                {
                    throw new \Exception("No se ha podido obtener la vrf del cliente, por favor notificar a Sistemas.");
                }
            }
            //verificar si el servicio es WIFI
            if($objProducto->getNombreTecnico() == "SAFECITYWIFI")
            {
                $objDetalleEleVlanAdmin      = $arrayResultadoVlanVrf['objDetalleElementoVlanAdmin'];
                $objPersonaEmpCaractVrfAdmin = $arrayResultadoVlanVrf['objPersonaEmpCaractVrfAdmin'];
                //se graba vlan del servicio adicional
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,$objProducto,
                                                        "VLAN SSID",$objDetalleEleVlan->getId(),$strUsrCreacion);
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,$objProducto,
                                                        "VLAN ADMIN",$objDetalleEleVlanAdmin->getId(),$strUsrCreacion);
                //se graba vrf del servicio adicional
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,$objProducto,
                                                        "VRF SSID",$objPersonaEmpCaractVrf->getId(),$strUsrCreacion);
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,$objProducto,
                                                        "VRF ADMIN",$objPersonaEmpCaractVrfAdmin->getId(),$strUsrCreacion);
            }
            elseif($objProducto->getNombreTecnico() == "SAFECITYDATOS")
            {
                //se graba vrf del servicio adicional
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,$objProducto,
                                                        "VLAN",$objDetalleEleVlan->getId(),$strUsrCreacion);
                //se graba vlan del servicio adicional
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,$objProducto,
                                                        "VRF",$objPersonaEmpCaractVrf->getId(),$strUsrCreacion);
            }

            //obtengo la velocidad por producto
            $strVelocidadGponDefault   = "";
            $strCapacidadGponDefault   = "";
            $arrayVelocidadServicioAdd = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne('CONFIG_PRODUCTO_DATOS_SAFE_CITY',
                                                                     'COMERCIAL',
                                                                     '',
                                                                     '',
                                                                     $objProducto->getId(),
                                                                     'VELOCIDAD_SERVICIO',
                                                                     '',
                                                                     '',
                                                                     '',
                                                                     $strCodEmpresa);
            if(isset($arrayVelocidadServicioAdd) && isset($arrayVelocidadServicioAdd['valor3'])
                && isset($arrayVelocidadServicioAdd['valor4']))
            {
                $strVelocidadGponDefault = $arrayVelocidadServicioAdd['valor3'];
                $strCapacidadGponDefault = $arrayVelocidadServicioAdd['valor4'];
            }
            //obtengo la capacidad uno del servicio
            $objServCapacidadUnoSerAdd = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,'CAPACIDAD1',$objProducto);
            if(!is_object($objServCapacidadUnoSerAdd))
            {
                //se graba capacidad uno del servicio adicional
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,$objProducto,
                                                        "CAPACIDAD1",$strCapacidadGponDefault,$strUsrCreacion);
            }
            //obtengo la capacidad dos del servicio
            $objServCapacidadDosSerAdd = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,'CAPACIDAD2',$objProducto);
            if(!is_object($objServCapacidadDosSerAdd))
            {
                //se graba capacidad dos del servicio adicional
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,$objProducto,
                                                        "CAPACIDAD2",$strCapacidadGponDefault,$strUsrCreacion);
            }
            //obtengo la velocidad gpon del servicio
            $objServVelocidadGponSerAdd = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                'VELOCIDAD_GPON',$objProducto);
            if(!is_object($objServVelocidadGponSerAdd))
            {
                //se graba velocidad gpon del servicio adicional
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,$objProducto,
                                                        "VELOCIDAD_GPON",$strVelocidadGponDefault,$strUsrCreacion);
            }

            //obtengo el id detalle de la tarea
            $objCaractIdDetalle = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                'ID_DETALLE_TAREA_INSTALACION',$objProducto);
            //verifico si se actualiza el estado de la tarea del servicio por detalles del parametro de la red GPON
            $arrayParDetEstadoTareaGpon = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('NUEVA_RED_GPON_TN',
                                                     'COMERCIAL',
                                                     '',
                                                     'CAMBIAR ESTADO TAREA SERVICIO',
                                                     $objProducto->getId(),
                                                     '',
                                                     '',
                                                     '',
                                                     '',
                                                     $strCodEmpresa);
            if( isset($arrayParDetEstadoTareaGpon) && !empty($arrayParDetEstadoTareaGpon)
                && isset($arrayParDetEstadoTareaGpon['valor4'])&& !empty($arrayParDetEstadoTareaGpon['valor4'])
                && isset($arrayParDetEstadoTareaGpon['valor5'])&& !empty($arrayParDetEstadoTareaGpon['valor5'])
                && is_object($objCaractIdDetalle))
            {
                //obtengo el info detalle
                $objInfoDetalle = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                                                    ->find($objCaractIdDetalle->getValor());
                if(is_object($objInfoDetalle))
                {
                    $arrayParametrosEstado                 = array();
                    $arrayParametrosEstado['cargarTiempo'] = "cliente";
                    $arrayParametrosEstado['esSolucion']   = "N";
                    $arrayParametrosEstado['strCodEmpresa']     = $strCodEmpresa;
                    $arrayParametrosEstado['strUsrCreacion']    = $strUsrCreacion;
                    $arrayParametrosEstado['strIpCreacion']     = $strIpCreacion;
                    $arrayParametrosEstado["intIdDepartamento"] = "";
                    $arrayParametrosEstado['estado']       = $arrayParDetEstadoTareaGpon['valor4'];
                    $arrayParametrosEstado['observacion']  = $arrayParDetEstadoTareaGpon['valor5'];
                    $serviceSoporte->cambiarEstadoTarea($objInfoDetalle,null,$arrayParametros['peticion'],$arrayParametrosEstado);
                }
            }

            //seteo observación
            $strObservacionServicio = "Se asignaron los siguientes recursos de red:<br>".
                                      "<b>Subred del Servicio:</b> ".$objSubredServicioAdd->getSubred()."<br>".
                                      "<b>Mascara:</b> ".$objSubredServicioAdd->getMascara()."<br>".
                                      "<b>Gateway:</b> ".$objSubredServicioAdd->getGateway()."<br>".
                                      "<b>IP del Servicio:</b> ".$strIpServicioAdd."<br>";
            if(!$booleanCamVpnSafeCity)
            {
                $strObservacionServicio .= "<b>VLAN:</b> ".$objDetalleEleVlan->getDetalleValor()."<br>".
                                           "<b>VRF:</b> ".$objPersonaEmpCaractVrf->getValor();
            }
            //ingresar seguimiento a la tarea
            if(is_object($objCaractIdDetalle))
            {
                //obtengo el info detalle
                $objInfoDetalle = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                                                    ->find($objCaractIdDetalle->getValor());
                if(is_object($objInfoDetalle))
                {
                    $arrayParametrosHist                   = array();
                    $arrayParametrosHist["intDetalleId"]   = $objInfoDetalle->getId();
                    $arrayParametrosHist["strCodEmpresa"]  = $strCodEmpresa;
                    $arrayParametrosHist["strUsrCreacion"] = $strUsrCreacion;
                    $arrayParametrosHist["strIpCreacion"]  = $strIpCreacion;
                    $arrayParametrosHist["strOpcion"]      = "Seguimiento";
                    $arrayParametrosHist["strEstadoActual"] = "Asignada";
                    $arrayParametrosHist["strEnviaDepartamento"] = "N";
                    $arrayParametrosHist["strObservacion"] = $strObservacionServicio;
                    $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                }
            }
            //setear estado del servicio
            $objServicio->setEstado("Asignada");
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();
            //seteo la interface y elemento conector
            $objServicioTecnico->setElementoConectorId($objServicioTecnicoPrincipal->getElementoConectorId());
            $objServicioTecnico->setInterfaceElementoConectorId($objServicioTecnicoPrincipal->getInterfaceElementoConectorId());
            $this->emComercial->persist($objServicioTecnico);
            $this->emComercial->flush();
            //ingreso historial
            $this->servicioGeneral->ingresarServicioHistorial($objServicio, "Asignada", $strObservacionServicio,
                                                              $strUsrCreacion, $strIpCreacion);
            //Generacion de Login Auxiliar del Servicio Adicional
            $this->servicioGeneral->generarLoginAuxiliar($objServicio->getId());

            //se actualiza estado a la solicitud
            $objDetalleSolicitud->setEstado("Asignada");
            $this->emComercial->persist($objDetalleSolicitud);
            $this->emComercial->flush();
            //agregar historial a la solicitud
            $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
            $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
            $objDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
            $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
            $objDetalleSolicitudHistorial->setUsrCreacion($strUsrCreacion);
            $objDetalleSolicitudHistorial->setEstado("Asignada");
            $objDetalleSolicitudHistorial->setObservacion($strObservacionServicio);
            $this->emComercial->persist($objDetalleSolicitudHistorial);
            $this->emComercial->flush();

            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->commit();
                $this->emInfraestructura->getConnection()->close();
            }
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->commit();
                $this->emComercial->getConnection()->close();
            }
            $strStatus  = "OK";
            $strMensaje = "Se guardaron correctamente los Recursos de Red";
        }
        catch (\Exception $e)
        {
            $this->utilServicio->insertError("Telcos+",
                                            "RecursosDeRedService->guardarRecursosRedServiciosGpon",
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
                $this->emInfraestructura->getConnection()->close();
            }
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
        }

        $arrayRespuestaFinal = array('status'=>$strStatus, 'mensaje'=> $strMensaje);
        return $arrayRespuestaFinal;
    }
       
    /**
    * Documentación para el método 'validacionIpAsignadaProvinciasWs'.
    *
    * Guarda la información para validar ip enviada a ws de nw
    * @return array $arrayResultado
    *
    * @author Jonathan Montece <jmontece@telconet.ec>
    * @version 1.0 12-08-2021
    */

    public function validacionIpAsignadaProvinciasWs($arrayParametrosIp)  
    {
        $boolConfirm = false;
        $arrayConfirmacion      = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('CONFIGURACION_WS_PROVINCIAS',//nombre parametro cab
                                                          'TECNICO', //modulo cab
                                                          'SUBREDES_PE',//proceso cab
                                                          'RESPUESTA NETWORKING PROVINCIAS', //descripcion det
                                                          '','','','','',
                                                          '10'); //empresa
        if( isset($arrayConfirmacion['valor1']) 
                           && !empty($arrayConfirmacion['valor1']) )
                        {
                            $boolConfirm = $arrayConfirmacion['valor1'];
                        }
        $arrayResultado  = array();
        $strAccion = "validar_subred";
        
        try 
        {
            $arrayDataAuditoria = array
                                    (
                                        "servicio"  => $arrayParametrosIp['strServicio'],
                                        "login_aux" => $arrayParametrosIp['strLogin'],
                                        "user_name" => $arrayParametrosIp['strUsr'],
                                        "user_ip"   => $arrayParametrosIp['strClientIp']
                                    );
            $arrayData = array
                                    (
                                        //"ip"    =>  '8.8.8.8', //CAMBIAR  POR STR IP
                                        "ip"    =>  $arrayParametrosIp['strSubred'], //CAMBIAR 
                                        "pe"    =>  $arrayParametrosIp['strElementoPadre'],
                                        "test"  =>  $boolConfirm
                                    );
            $objJsonArray = array
                                    (
                                        "accion"  => $strAccion,
                                        "data"     => $arrayData,
                                        "data-auditoria" => $arrayDataAuditoria
                                    );

            $arrayOptions = array(CURLOPT_SSL_VERIFYPEER => false, CURLOPT_HTTPHEADER => false);
            $arrayResponse     = $this->serviceRestClient->postJSON($this->strUrlValidarIpProvinciasWs,
                                                                json_encode($objJsonArray),
                                                                $arrayOptions);
 
            if(isset($arrayResponse) && !empty($arrayResponse))
            {
                $arrayResult = json_decode($arrayResponse['result'], 1);
                if($arrayResult['status'] == 200)
                {
                    
                    $arrayResultado    = array('mensaje' => 'OK');

                }
                else if($arrayResult['status'] == 400)
                {
                    $arrayResultado    = array('mensaje' => '400', 'msgWs' => $arrayResult['msg'], 'statusWs' => $arrayResult['status']);
                    
                  
                }
               
                else
                {
                    throw new \Exception($arrayResult['msg']);
                }
            }
            else
            {
                throw new \Exception('Error, no hay respuesta del WS para validar Ip');
            }



        } catch (\Exception $e) 
        {
            
            $strRespuestaUno   =  $arrayParametrosIp['strSubred'];
            $strRespuesta   =  "Con la ip:  $strRespuestaUno " . $e->getMessage();
            $arrayResultado = array('mensaje'     => $strRespuesta);
            $this->serviceUtil->insertError(
                            'Telcos+',
                            'RecursosDeRedService.validacionIpAsignadaProvinciasWs',
                            'Error RecursosDeRedService.validacionIpAsignadaProvinciasWs:' . $e->getMessage(),
                            $arrayParametrosIp['strUsr'],
                            $arrayParametrosIp['strClientIp']
            );
        }
        return $arrayResultado;
    }

    /**
     * Función que sirve para asignar los recursos de Red para los servicios adicionales Safe City
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 11-05-2022
     *
     * @param array $arrayParametros
     * @return array $arrayRespuestaFinal
     */
    public function asignarRecursosRedServiciosGpon($arrayParametros)
    {
        $strUsrCreacion = $arrayParametros['usrCreacion'];
        $strIpCreacion  = $arrayParametros['ipCreacion'];
        try
        {
            $objDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                ->find($arrayParametros['idDetalleSolicitud']);
            if(!is_object($objDetalleSolicitud))
            {
                throw new \Exception("No se ha podido obtener la solicitud, por favor notificar a Sistemas.");
            }
            $objServicio         = $objDetalleSolicitud->getServicioId();
            if(!is_object($objServicio))
            {
                throw new \Exception("No se ha podido obtener el servicio, por favor notificar a Sistemas.");
            }
            //guardar recursos de red
            $arrayRespuestaFinal = $this->guardarRecursosRedServiciosGpon($arrayParametros);
            //ASIGNAR RECURSOS DE RED CAMARAS SIMULTANEO
            if($arrayRespuestaFinal['status'] == 'OK' && $objServicio->getProductoId()->getNombreTecnico() == "SAFECITYDATOS")
            {
                $arrayServicioCamara = $this->emComercial->getRepository("schemaBundle:InfoServicio")
                                            ->findBy(array("puntoId"    => $objServicio->getPuntoId()->getId(),
                                                           "productoId" => $objServicio->getProductoId()->getId(),
                                                           "estado"     => "AsignadoTarea"));
                foreach($arrayServicioCamara as $objServicioCamara)
                {
                    $objTipoSolicitud = $this->emComercial->getRepository("schemaBundle:AdmiTipoSolicitud")
                                                ->findOneBy(array("descripcionSolicitud" => "SOLICITUD PLANIFICACION",
                                                                  "estado"               => "Activo"));
                    $objDetSolicitud  = $this->emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                ->findOneBy(array("tipoSolicitudId" => $objTipoSolicitud->getId(),
                                                                  "servicioId"      => $objServicioCamara->getId(),
                                                                  "estado"          => "AsignadoTarea"));
                    if(is_object($objDetSolicitud))
                    {
                        $arrayParametros['idDetalleSolicitud'] = $objDetSolicitud->getId();
                        //guardar recursos de red
                        $this->guardarRecursosRedServiciosGpon($arrayParametros);
                    }
                }
            }
        }
        catch (\Exception $ex)
        {
            $this->utilServicio->insertError("Telcos+",
                                            "RecursosDeRedService->asignarRecursosRedServiciosGpon",
                                            $ex->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
            $arrayRespuestaFinal = array('status'=>"ERROR", 'mensaje'=> $ex->getMessage());
        }
        return $arrayRespuestaFinal;
    }

    /**
     * Metodo encargado para la asignacion de recursos de red para servicios Clear Channel Punto a Punto
     * 
     * @author Josue Valencia <ajvalencia@telconet.ec>
     * @version 1.0 26-12-2022
     * 
     * @param  Array $arrayParametros    [
     *                                      idServicio              Referencia del Servicio
     *                                      idDetalleSolicitud      Referencia de la Solicitud creada
     *                                      idElementoPadre         Referencia del Elemento Pe
     *                                      vlan                    Vlan del cliente
     *                                      vrf                     Vrf del cliente
     *                                      protocolo               Protocolo del cliente
     *                                      defaultGateway          Default Gateway
     *                                      asPrivado               asPrivado del cliente
     *                                      mascara                 Mascara /29 de configuracion a nivel de Backbone
     *                                      mascaraCliente          Mascara /30 de configuracion de las Vsats
     *                                      personaEmpresaRolId     Referencia a la persona empresa rol del cliente
     *                                      idSubred                id subred elegida
     *                                      flagRecursos            si son recursos nuevos o existentes
     *                                      flagTransaccion         Si se transacciona o no
     *                                  ]
     * @return Array $arrayRespuestaFinal
     * @throws \Exception
     */
    public function asignarRecursosClearChannel($arrayParametros)
    {   
        $intIdServicio                  = $arrayParametros['idServicio'];
        $intIdDetalleSolicitud          = $arrayParametros['idDetalleSolicitud'];
        $strTipoSolicitud               = $arrayParametros['tipoSolicitud'];
        $intIdElementoPadre             = $arrayParametros['idElementoPadre'];
        $intVlan                        = $arrayParametros['vlan'];
        $intVrf                         = $arrayParametros['vrf'];
        $strVrf                         = '';
        $strProtocolo                   = $arrayParametros['protocolo'];
        $boolDefaultGateway             = $arrayParametros['defaultGateway'];//POSIBLE
        $strMascaraPublica              = $arrayParametros['mascaraPublica'];
        $intIdSubred                    = $arrayParametros['idSubred'];        
        $strUsrCreacion                 = $arrayParametros['usrCreacion'];
        $intEmpresaCod                  = $arrayParametros['empresaCod'];
        $intIdOficina                   = $arrayParametros['intIdOficina'];
        $strIpCreacion                  = $arrayParametros['ipCreacion'];
        $strTipoIngreso                = $arrayParametros['tipoIngreso'];

        $strObservacion  = "";
        $boolEsCambioUM                 = false;
        $strTipoFactibilidad            = "RUTA";
        $arrayParametrosWs              = array();

        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        
        try
        {   
            $objServicio         = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);                    
            $objProducto         = $objServicio->getProductoId();
            $objDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdDetalleSolicitud);
                  
            if($strTipoIngreso == 'T')
            {
                if($intIdElementoPadre == "No definido")
                {
                    throw new \Exception("No Existe PE asociado al Switch, Favor Revisar!");
                }
                            
                
                $objElementoPadre    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoPadre);
                $objSubred           = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->find($intIdSubred);
                $strNombreCanton     = $this->servicioGeneral->getCiudadRelacionadaPorRegion($objServicio,$intIdEmpresa);
                
                
                //Obtener la caracteristica TIPO_FACTIBILIDAD para discriminar que sea FIBRA DIRECTA o RUTA
                $objServProdCaractTipoFact = $this->servicioGeneral
                            ->getServicioProductoCaracteristica($objServicio,'TIPO_FACTIBILIDAD',$objProducto);                        
                
                if($objServProdCaractTipoFact)
                {
                    $strTipoFactibilidad = $objServProdCaractTipoFact->getValor();
                }
                else
                {
                    if($strUltimaMilla == "Fibra Optica")
                    {
                        $strTipoFactibilidad = "RUTA";
                    }
                    else
                    {
                        $strTipoFactibilidad = "DIRECTO";
                    }
                }                 
                    
                
                //Obtener el siguiente puerto disponible asignado al Ro
                $objInterfaceElemento =  $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                    ->findOneBy(array('elementoId' => $intIdElementoPadre,
                                                    'estado'     => 'not connect'));

                //Se graba caracteristica nombre pe
                $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                $objProducto, 
                                                                                "NOMBRE PE",
                                                                                $intIdElementoPadre, 
                                                                                $strUsrCreacion);

                //Se graba caracteristica subred wan
                $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                $objProducto, 
                                                                                "SUBRED_PRIVADA",
                                                                                $intIdSubred, 
                                                                                $strUsrCreacion);


                //se graba caracteristica vlan
                $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                $objProducto, 
                                                                                "VLAN",
                                                                                $intVlan, 
                                                                                $strUsrCreacion);
                //se graba caracteristica vrf
                $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                $objProducto, 
                                                                                "VRF", 
                                                                                $intVrf, 
                                                                                $strUsrCreacion);
                //se graba caracteristica protocolo
                $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                $objProducto, 
                                                                                "PROTOCOLO_ENRUTAMIENTO", 
                                                                                $strProtocolo, 
                                                                                $strUsrCreacion);
            
                //se actualiza el estado del servicio
                $objServicio->setEstado("Asignada");
                $this->emComercial->persist($objServicio);
                $this->emComercial->flush();
                
                $objVrf  = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->find($intVrf);
                if(is_object($objVrf))
                {
                    $strVrf = $objVrf->getValor();
                }
                
        

                $strObservacion.="<b>Asignaci&oacute;n Recursos de Red</b><br/>";
                
                $strObservacion.="<b>Nombre Elemento:</b> ".$objElementoPadre->getNombreElemento()."<br/>";
                
                $strObservacion.="<b>VRF:</b> ".$strVrf."<br/>";
                
                $objDetalleElemento  = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')->find($intVlan);
                if($objDetalleElemento)
                {
                                              
                    $objTipoRegion = strpos($objElementoPadre->getNombreElemento(), 'gye');

                    $strRegion = '';
                    if($objTipoRegion)
                    {
                        $strRegion = 'gye';
                    }
                    else
                    {
                        $strRegion = 'uio';
                    }

                    $arrayResponse  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('PE_TELCONET',
                                                            'TECNICO',
                                                            null,
                                                            'PE_TELCONET_ASIGNAR',
                                                            null,
                                                            null,
                                                            null,
                                                            null,
                                                            null,
                                                            $intEmpresaCod);
                    $arrayPeEncontrados = array();
                
                    if(count($arrayResponse)>0)
                    {
                        foreach($arrayResponse as $reg)
                        {
                            $arrayValidaPe = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                            ->getValidarPeTelco($reg['valor1'],
                                                                $reg['valor3'],
                                                                $reg['valor4']);
                            if($arrayValidaPe['status'] === 'OK')
                            {
                                
                                $objTipoRegionPE = strpos($reg['valor1'], $strRegion);
                                if($objTipoRegionPE)
                                {
                                    $arrayPeEncontrados[] = array('nombreElemento'    => $reg['valor1'],
                                                            'valor' => $arrayValidaPe['result']);
                                }
                                
                            }
                        } 
                    }


                    $objDetalleElemento->setEstado('Ocupado');
                    $this->emInfraestructura->persist($objDetalleElemento);
                    $this->emInfraestructura->flush();

                    //Reservar a Nivel Nacional
                    foreach($arrayPeEncontrados as $objPePermitido)
                    {
                       
                        $objVlanReservaNacional = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                ->findOneBy(array("elementoId"    => $objPePermitido['valor'],
                                                "detalleNombre" => "VLAN", 
                                                "detalleValor"  =>  $objDetalleElemento->getDetalleValor()));
                        
                        if(is_object($objVlanReservaNacional) && $objPePermitido['nombreElemento'] != $objElementoPadre->getNombreElemento())
                        {
                            $objVlanReservaNacional->setEstado('Ocupado');
                            $this->emInfraestructura->persist($objVlanReservaNacional);
                            $this->emInfraestructura->flush();
                            
                        }
                    }

                    $strObservacion.="<b>Vlan:</b> ".$objDetalleElemento->getDetalleValor()." Reservada <br/>";
                }


                //Se obtiene la informacion tecnica anterior
                    $objInfoServicioTecnico = $this->emComercial->getRepository("schemaBundle:InfoServicioTecnico")
                    ->findOneByServicioId($intIdServicio);  

                if($objInfoServicioTecnico)
                {
                    $objInfoServicioTecnico-> setElementoId($intIdElementoPadre);
                    $objInfoServicioTecnico-> setInterfaceElementoId($objInterfaceElemento->getId());
                    $this->emComercial->persist($objInfoServicioTecnico);
                    $this->emComercial->flush();
                }

                if($objSubred)
                {
                    //Se obtiene la ip siguiente disponible de la subred
                    $strIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                        ->getIpDisponibleBySubred($intIdSubred);

                    if($strIp != 'NoDisponible')
                    {
                        // Se Almacena la IP Disponible para la 
                        $objInfoIp = new InfoIp();
                        $objInfoIp->setIp($strIp);
                        $objInfoIp->setServicioId($objServicio->getId());
                        $objInfoIp->setEstado("Activo");
                        $objInfoIp->setSubredId($objSubred->getId());
                        $objInfoIp->setMascara($objSubred->getMascara());
                        $objInfoIp->setGateway($objSubred->getGateway());
                        $objInfoIp->setTipoIp($objSubred->getTipo());
                        $objInfoIp->setVersionIp($objSubred->getVersionIp());
                        $objInfoIp->setUsrCreacion($strUsrCreacion);
                        $objInfoIp->setFeCreacion(new \DateTime('now'));
                        $objInfoIp->setIpCreacion($strIpCreacion);
                        $this->emInfraestructura->persist($objInfoIp);
                        $this->emInfraestructura->flush(); 
                        
                        $strObservacion.="<b>IP P&uacute;blica:</b> ".$strIp."<br/>";
                        $strObservacion.="<b>M&aacute;scara:</b> ".$strMascaraPublica."<br/>";

                        //Se verifica que existan IP Disponibles en el rango
                        $strVerificadorIp = $this->emInfraestructura
                            ->getRepository('schemaBundle:InfoIp')
                            ->getIpDisponibleBySubred($intIdSubred);
                        
                        if($strVerificadorIp == 'NoDisponible')
                        {
                            $objSubred->setEstado('Ocupado');
                            $this->emInfraestructura->persist($objSubred);
                            $this->emInfraestructura->flush();
                        }
                    }
                    else
                    {
                        throw new \Exception("OBSERVACION: No Existen IPs disponibles para la Subred Pública requerida, escoja otra Subred");
                    }
                }

                

                //agregar historial del servicio
                $objInfoHistorial = new InfoServicioHistorial();
                $objInfoHistorial->setServicioId($objServicio);
                $objInfoHistorial->setEstado("Asignada");
                $objInfoHistorial->setUsrCreacion($strUsrCreacion);
                $objInfoHistorial->setFeCreacion(new \DateTime('now'));
                $objInfoHistorial->setIpCreacion($strIpCreacion);
                $objInfoHistorial->setObservacion($strObservacion);
                $this->emComercial->persist($objInfoHistorial);
                $this->emComercial->flush();                        
                
                if($objDetalleSolicitud)
                {
                    //se actualiza estado a la solicitud
                    $objDetalleSolicitud->setEstado("Asignada");
                    $this->emComercial->persist($objDetalleSolicitud);
                    $this->emComercial->flush();

                    //agregar historial a la solicitud
                    $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
                    $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                    $objDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
                    $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolicitudHistorial->setUsrCreacion($strUsrCreacion);
                    $objDetalleSolicitudHistorial->setEstado("Asignada");
                    $objDetalleSolicitudHistorial->setObservacion($strObservacion);
                    $this->emComercial->persist($objDetalleSolicitudHistorial);
                    $this->emComercial->flush();
                }

                else
                {
                    throw new \Exception("No Existe Solicitud de Planificación, favor comunicarse con Sistemas!");
                }  
            }
            else
            {
                //Se graba caracteristica nombre pe
                $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                $objProducto, 
                                                                                "NOMBRE PE",
                                                                                $intIdElementoPadre, 
                                                                                $strUsrCreacion);

                //Se graba caracteristica subred wan
                $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                $objProducto, 
                                                                                "SUBRED_PRIVADA",
                                                                                $intIdSubred, 
                                                                                $strUsrCreacion);


                //se graba caracteristica vlan
                $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                $objProducto, 
                                                                                "VLAN",
                                                                                $intVlan, 
                                                                                $strUsrCreacion);
                
                //se graba caracteristica protocolo
                $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                $objProducto, 
                                                                                "PROTOCOLO_ENRUTAMIENTO", 
                                                                                $strProtocolo, 
                                                                                $strUsrCreacion);

                 //se actualiza el estado del servicio
                $objServicio->setEstado("Asignada");
                $this->emComercial->persist($objServicio);
                $this->emComercial->flush();
                
                
        

                $strObservacion.="<b>Asignaci&oacute;n Recursos de Red - Cliente</b><br/>";
                $strObservacion.="<b>Nombre Elemento:</b> ".$intIdElementoPadre."<br/>";
                $strObservacion.="<b>Vlan:</b> ".$intVlan."<br/>";
                $strObservacion.="<b>Subred P&uacute;blica:</b> ".$intIdSubred."<br/>";
                $strObservacion.="<b>M&aacute;scara:</b> ".$strMascaraPublica."<br/>";
                              

                //agregar historial del servicio
                $objInfoHistorial = new InfoServicioHistorial();
                $objInfoHistorial->setServicioId($objServicio);
                $objInfoHistorial->setEstado("Asignada");
                $objInfoHistorial->setUsrCreacion($strUsrCreacion);
                $objInfoHistorial->setFeCreacion(new \DateTime('now'));
                $objInfoHistorial->setIpCreacion($strIpCreacion);
                $objInfoHistorial->setObservacion($strObservacion);
                $this->emComercial->persist($objInfoHistorial);
                $this->emComercial->flush();                        
                
                if($objDetalleSolicitud)
                {
                    //se actualiza estado a la solicitud
                    $objDetalleSolicitud->setEstado("Asignada");
                    $this->emComercial->persist($objDetalleSolicitud);
                    $this->emComercial->flush();

                    //agregar historial a la solicitud
                    $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
                    $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                    $objDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
                    $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolicitudHistorial->setUsrCreacion($strUsrCreacion);
                    $objDetalleSolicitudHistorial->setEstado("Asignada");
                    $objDetalleSolicitudHistorial->setObservacion($strObservacion);
                    $this->emComercial->persist($objDetalleSolicitudHistorial);
                    $this->emComercial->flush();
                }

            }       
                                  
            
            //Generacion de Login Auxiliar al Servicio            
            $this->servicioGeneral->generarLoginAuxiliar($intIdServicio);
            
            $strStatus         = "OK";
            $strMensaje        = "Se Asignaron los Recursos de Red!";
        }
        catch (\Exception $e)
        {
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
          
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            $strStatus         = "ERROR";
            $strMensaje        = "ERROR EN LA LOGICA DE NEGOCIO: <br> ".$e->getMessage();
        }
        
        //*DECLARACION DE COMMITS*/
        if ($this->emInfraestructura->getConnection()->isTransactionActive())
        {
            $this->emInfraestructura->getConnection()->commit();
        }

        if ($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }
            
        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        //*----------------------------------------------------------------------*/
        
        //*RESPUESTA-------------------------------------------------------------*/
        $arrayRespuestaFinal = array('status'=>$strStatus, 
                                'mensaje'=>$strMensaje);
        return $arrayRespuestaFinal;
        //*----------------------------------------------------------------------*/
    }

}
