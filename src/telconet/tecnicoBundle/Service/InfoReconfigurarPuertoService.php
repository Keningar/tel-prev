<?php

namespace telconet\tecnicoBundle\Service;

use Doctrine\ORM\EntityManager;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoIpElemento;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion; 
use telconet\schemaBundle\Entity\InfoDetalleInterface;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoPuntoDatoAdicional;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\AdmiMotivo;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class InfoReconfigurarPuertoService {
    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $emSeguridad;
    private $servicioGeneral;
    private $activarService;
    private $container;
    private $host;
    private $ejecutaComando;
    
    public function __construct(Container $container) 
    {
        $this->container = $container;
        $this->emSoporte = $this->container->get('doctrine')->getManager('telconet_soporte');
        $this->emInfraestructura = $this->container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSeguridad = $this->container->get('doctrine')->getManager('telconet_seguridad');
        $this->emComercial = $this->container->get('doctrine')->getManager('telconet');
        $this->emComunicacion = $this->container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emNaf = $this->container->get('doctrine')->getManager('telconet_naf');
        $this->host = $this->container->getParameter('host');
        $this->ejecutaComando = $this->container->getParameter('ws_rda_ejecuta_scripts');
    }    
    
    public function setDependencies(InfoServicioTecnicoService $servicioGeneral,InfoActivarPuertoService $activarService) 
    {
        $this->servicioGeneral = $servicioGeneral;
        $this->activarService = $activarService;
    }
    
    /**
     * Funcion que selecciona las variables necesarias para luego
     * llamar a las funciones correspondientes por cada empresa
     * 
     * @author Creado: Francisco Adum <fadum@telconet.ec>
     * @version 1.0 21-07-2014
     * 
     * @param array                    $arrayPeticiones
     * @return array con dos valores: String 'status' indica OK/ERROR, String 'mensaje' indica el mensaje a presentar en caso de ERROR
     */
    public function reconfigurarPuerto($arrayPeticiones)
    {
        //*DECLARACION DE VARIABLES----------------------------------------------*/
        $idEmpresa = $arrayPeticiones[0]['idEmpresa'];
        $prefijoEmpresa = $arrayPeticiones[0]['prefijoEmpresa'];
        $idServicio = $arrayPeticiones[0]['idServicio'];
        $usrCreacion = $arrayPeticiones[0]['usrCreacion'];
        $ipCreacion = $arrayPeticiones[0]['ipCreacion'];
                
        //migracion_ttco_md
        $arrayEmpresaMigra = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
            ->getEmpresaEquivalente($idServicio, $prefijoEmpresa);

        if($arrayEmpresaMigra)
        { 
            if ($arrayEmpresaMigra['prefijo']=='TTCO')
            {
                 $idEmpresa= $arrayEmpresaMigra['id'];
                 $prefijoEmpresa= $arrayEmpresaMigra['prefijo'];
            }
        }
        
        $servicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
        $servicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneBy(array( "servicioId" => $servicio->getId()));
        $interfaceElemento= $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($servicioTecnico->getInterfaceElementoId());
        $elemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($interfaceElemento->getElementoId());
        $modeloElemento = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')->find($elemento->getModeloElementoId());
        //*----------------------------------------------------------------------*/
        
        $arrayRespuesta = null;
        
        if($prefijoEmpresa=="TTCO")
        {
            
        }
        else if($prefijoEmpresa=="MD")
        {
            $arrayRespuesta = $this->reconfigurarPuertoMd($servicio, $servicioTecnico, $interfaceElemento, 
                                                          $modeloElemento, $idEmpresa, $usrCreacion, $ipCreacion);
        }
        
        return $arrayRespuesta;
    }
    
    /**
     * Funcion que reconfigura el puerto del servicio,
     * para clientes de MD, solo internet
     * 
     * @author Creado: Francisco Adum <fadum@telconet.ec>
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec> 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec> 
     * @version 1.0 21-07-2014
     * @version 1.1 03-09-2015
     * @version 1.2 03-09-2015
     * @version 1.3 03-09-2015
     * @version 1.4 03-09-2015
     * 
     * @param InfoServicio              $servicio
     * @param InfoServicioTecnico       $servicioTecnico
     * @param InfoInterfaceElemento     $interfaceElemento
     * @param AdmiModeloElemento        $modeloElemento
     * @param int                       $idEmpresa
     * @param String                    $usrCreacion
     * @return array con dos valores: String 'status' indica OK/ERROR, String 'mensaje' indica el mensaje a presentar en caso de ERROR
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.5 04-03-2022 Se modifica el metodo para poder realizar las llamadas a los ws del servidor LC
     */
    public function reconfigurarPuertoMd($servicio, $servicioTecnico, $interfaceElemento, $modeloElemento, $idEmpresa,
                                         $usrCreacion,$ipCreacion)
    {
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        $respuestaFinal      = null;
        $status              = "NA";
        $mensaje             = "NA";
        $strMensajeHistorial = "";
        $strSpid             = "";
        $spcLineProfileName  = "";
        $spcServiceProfile   = "";
        $serieOnt            = "";
        $spcVlan             = "";
        $spcGemPort          = "";
        $spcTrafficTable     = "";
        $strModeloElementoOlt   = "";
        $strIpElementoOlt       = "";
        $serviceMiddleware      = $this->container->get('tecnico.RedAccesoMiddleware');
        try{
            $producto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                          ->findOneBy(array("nombreTecnico" => "INTERNET",
                                                            "empresaCod"=>$idEmpresa, 
                                                            "estado" => "Activo"));
            
            $servProdCaractIndiceCliente = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $producto);
            $indiceClienteValor = "";
            if($servProdCaractIndiceCliente)
            {
                $indiceClienteValor = $servProdCaractIndiceCliente->getValor();
                $servProdCaractIndiceCliente->setEstado("Eliminado");
                $this->emComercial->persist($servProdCaractIndiceCliente);
                $this->emComercial->flush();
            }
            
            if($modeloElemento->getNombreModeloElemento()=="EP-3116")
            {
                $servProdCaractPerfil = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "PERFIL", $producto);
                $servProdCaractMacOnt = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $producto);

                if(!$servProdCaractPerfil)
                {
                    $respuestaFinal[] = array('status'=>"ERROR", 'mensaje'=>"NO EXISTE PERFIL");
                    return $respuestaFinal;
                }

                if(!$servProdCaractMacOnt)
                {
                    $respuestaFinal[] = array('status'=>"ERROR", 'mensaje'=>"NO EXISTE MAC ONT");
                    return $respuestaFinal;
                }
            }
            
            if($modeloElemento->getNombreModeloElemento()=="MA5608T")
            {
                $spcLineProfileName = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "LINE-PROFILE-NAME", $producto);
                if(!$spcLineProfileName)
                {
                    $respuestaFinal[] = array('status'=>"ERROR", 'mensaje'=>"NO EXISTE LINE PROFILE");
                    return $respuestaFinal;
                }
                $spcGemPort         = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "GEM-PORT", $producto);
                if(!$spcGemPort)
                {
                    $respuestaFinal[] = array('status'=>"ERROR", 'mensaje'=>"NO EXISTE GEM PORT");
                    return $respuestaFinal;
                }
                $spcVlan            = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "VLAN", $producto);
                if(!$spcVlan)
                {
                    $respuestaFinal[] = array('status'=>"ERROR", 'mensaje'=>"NO EXISTE VLAN");
                    return $respuestaFinal;
                }
                $spcTrafficTable    = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "TRAFFIC-TABLE", $producto);
                if(!$spcTrafficTable)
                {
                    $respuestaFinal[] = array('status'=>"ERROR", 'mensaje'=>"NO EXISTE TRAFFIC TABLE");
                    return $respuestaFinal;
                }
                $servProdCaractSpid = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $producto);

                $spcServiceProfile  = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SERVICE-PROFILE", $producto);
                if(!$spcServiceProfile)
                {
                    $respuestaFinal[] = array('status'=>"ERROR", 'mensaje'=>"NO EXISTE SERVICE PROFILE");
                    return $respuestaFinal;
                }
                $entityElementoOnt  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($servicioTecnico->getElementoClienteId());
                if ($entityElementoOnt)
                {
                    $serieOnt = $entityElementoOnt->getSerieFisica();
                }
                else
                {
                    $respuestaFinal[] = array('status'=>"ERROR", 'mensaje'=>"NO EXISTE SERIE");
                    return $respuestaFinal;
                }
                $servProdCaractMacOnt = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $producto);

                $intIdElementoOlt       = $servicioTecnico->getElementoId();
                $objElementoOlt         = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoOlt);
                if(is_object($objElementoOlt))
                {
                    $strModeloElementoOlt   = $objElementoOlt->getNombreElemento();
                }
                $objIpElementoOlt   = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                              ->findOneBy(array("elementoId" => $intIdElementoOlt));
                if(is_object($objIpElementoOlt))
                {
                    $strIpElementoOlt = $objIpElementoOlt->getIp();
                }
            }
            
            
            $arrayParametros=array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'macOnt'            => ($servProdCaractMacOnt)?$servProdCaractMacOnt->getValor():"",
                                        'perfil'            => ($servProdCaractPerfil)?$servProdCaractPerfil->getValor():"",
                                        'login'             => $servicio->getPuntoId()->getLogin(),
                                        'ontLineProfile'    => ($spcLineProfileName) ? $spcLineProfileName->getValor() : "",
                                        'serviceProfile'    => ($spcServiceProfile)  ? $spcServiceProfile->getValor()  : "",
                                        'serieOnt'          => $serieOnt,
                                        'vlan'              => ($spcVlan)            ? $spcVlan->getValor()            : "",
                                        'gemPort'           => ($spcGemPort)         ? $spcGemPort->getValor()         : "",
                                        'trafficTable'      => ($spcTrafficTable)    ? $spcTrafficTable->getValor()    : "",
                                        'ontId'             => $indiceClienteValor,
                                        'usrCreacion'       => $usrCreacion,
                                        'ipCreacion'        => $ipCreacion,
                                        'strModeloElementoOlt'  => $strModeloElementoOlt,
                                        'strIpElementoOlt'      => $strIpElementoOlt,
                                        'service_port'          => ($servProdCaractSpid)?$servProdCaractSpid->getValor(): "",
                                  );
            
            //se ejecuta la activacion del servicio
            $respuestaFinal         = $this->activarService->activarClienteMdSinIp($arrayParametros);
            $status                 = $respuestaFinal[0]['status'];
            $mensaje                = $respuestaFinal[0]['mensaje'];
            $intIdClienteElemento   = $respuestaFinal[0]['ont_id'];
            
            if($status=="OK")
            {
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, $producto, "INDICE CLIENTE", 
                                                                               $intIdClienteElemento, $usrCreacion);
                if($modeloElemento->getNombreModeloElemento()=="MA5608T")
                {
                    //*OBTENER SCRIPT SPID --------------------------------------------------------*/
                    $scriptArraySpid   = $this->servicioGeneral->obtenerArregloScript("obtenerSpid",$modeloElemento);
                    $idDocumentoSpid   = $scriptArraySpid[0]->idDocumento;
                    $usuario           = $scriptArraySpid[0]->usuario;
                    //*----------------------------------------------------------------------*/

                    //dividir interface para obtener tarjeta y puerto pon
                    list($tarjeta, $puertoPon) = split('/',$interfaceElemento->getNombreInterfaceElemento());

                    //variables datos
                    //$datos = $tarjeta.",".$puertoPon.",".$intIdClienteElemento;
                    //OBTENER NOMBRE CLIENTE
                    $objPersona         = $servicioTecnico->getServicioId()->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
                    $strNombreCliente   = $objPersona->__toString();
                    //OBTENER IDENTIFICACION
                    $strIdentificacion      = $objPersona->getIdentificacionCliente();
                    // cambiar a llamada WS RECONFIGURAR_PUERTO_LC
                    $arrayDatosONT        = array(
                                                'serial_ont'       => $arrayParametros["serieOnt"],
                                                'mac_ont'          => ($servProdCaractMacOnt)?$servProdCaractMacOnt->getValor():"",
                                                'nombre_olt'       => $strModeloElementoOlt,
                                                'ip_olt'           => $strIpElementoOlt,
                                                'puerto_olt'       => $interfaceElemento->getNombreInterfaceElemento(),
                                                'modelo_olt'       => $modeloElemento->getNombreModeloElemento(),
                                                'gemport'          => $arrayParametros["gemPort"],
                                                'estado_servicio'  => $servicioTecnico->getServicioId()->getEstado(),
                                                'service_profile'  => $arrayParametros["serviceProfile"],
                                                'line_profile'     => $arrayParametros["ontLineProfile"],
                                                'traffic_table'    => $arrayParametros["trafficTable"],
                                                'ont_id'           => $intIdClienteElemento,
                                              );
                    $arrayDatosMiddleware   = array(
                                                'nombre_cliente'        => $strNombreCliente,
                                                'login'                 => $arrayParametros["login"],
                                                'identificacion'        => $strIdentificacion,
                                                'datos'                 => $arrayDatosONT,
                                                'opcion'                => "RECONFIGURAR_PUERTO_LC",
                                                'ejecutaComando'        => $this->ejecutaComando,
                                                'usrCreacion'           => $usrCreacion,
                                                'ipCreacion'            => $ipCreacion,
                                              );
                    $objJsonSpid          = $serviceMiddleware->middleware(json_encode($arrayDatosMiddleware));
                    $objJsonSpid          = json_decode(json_encode($objJsonSpid));
                    $strStatusSpid        = $objJsonSpid->status;
                  
                    if($strStatusSpid!="OK")
                    {
                        //reversar indice cliente
                        if($servProdCaractIndiceCliente)
                        {
                            $servProdCaractIndiceCliente->setEstado("Activo");
                            $this->emComercial->persist($servProdCaractIndiceCliente);
                            $this->emComercial->flush();
                        }
                        $respuestaFinal     = null;
                        $respuestaFinal[]   = array('status'=>$strStatusSpid, 'mensaje'=>$objJsonSpid->mensaje);
                        return $respuestaFinal;
                    }
                    $strSpid = $objJsonSpid->spid;
                  
                    if($strSpid!="")
                    {
                        if($servProdCaractSpid)
                        {
                            $servProdCaractSpid->setEstado("Eliminado");
                            $this->emComercial->persist($servProdCaractSpid);
                            $this->emComercial->flush();
                        }
                        //servicio prod caract ssid
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, $producto, "SPID", $strSpid, $usrCreacion);
                    }
                  
                }
                
                
            }
            else
            {
                //reversar indice cliente
                if($servProdCaractIndiceCliente)
                {
                    $indiceClienteValor = $servProdCaractIndiceCliente->getValor();
                    $servProdCaractIndiceCliente->setEstado("Activo");
                    $this->emComercial->persist($servProdCaractIndiceCliente);
                    $this->emComercial->flush();
                }
                
                $respuestaFinal[] = array('status'=>"ERROR", 'mensaje'=>"Error, ".$mensaje);
                return $respuestaFinal;
            }
            
            //historial del servicio
            $servicioHistorial = new InfoServicioHistorial();
            $servicioHistorial->setServicioId($servicio);
            if($status=="OK")
            {
                
                if($modeloElemento->getNombreModeloElemento()=="MA5608T")
                {
                    $strMensajeHistorial = "<br>Service Port ID:".$strSpid;
                }
                
                $servicioHistorial->setObservacion("Se reconfiguro el puerto,"
                                                  ."<br><b>Datos Anteriores:</b>"
                                                  ."<br>Olt:".$interfaceElemento->getElementoId()->getNombreElemento()
                                                  ."<br>Puerto:".$interfaceElemento->getNombreInterfaceElemento()
                                                  ."<br>Indice:".$indiceClienteValor
                                                  . $strMensajeHistorial
                                                  ."<br><b>Datos Nuevos:</b>"
                                                  ."<br>Olt:".$interfaceElemento->getElementoId()->getNombreElemento()
                                                  ."<br>Puerto:".$interfaceElemento->getNombreInterfaceElemento()
                                                  ."<br>Indice:".$intIdClienteElemento);
            }
            else
            {
                $servicioHistorial->setObservacion("No se reconfiguro el puerto");
            }
            $servicioHistorial->setEstado($servicio->getEstado());
            $servicioHistorial->setUsrCreacion($usrCreacion);
            $servicioHistorial->setFeCreacion(new \DateTime('now'));
            $servicioHistorial->setIpCreacion($ipCreacion);
            $this->emComercial->persist($servicioHistorial);
            $this->emComercial->flush();
        }
        catch (\Exception $e) {
            if ($this->emComercial->getConnection()->isTransactionActive()){
                $this->emComercial->getConnection()->rollback();
            }
            $respuestaFinal[] = array('status'=>"ERROR", 'mensaje'=>"Error en la Logica de Negocio!, ".$e->getMessage());
            return $respuestaFinal;
        }
        
        //*DECLARACION DE COMMITS*/
        if ($this->emComercial->getConnection()->isTransactionActive()){
            $this->emComercial->getConnection()->commit();
        }
        
        $this->emComercial->getConnection()->close();
        //*----------------------------------------------------------------------*/
        
        return $respuestaFinal;
    }
    
    /**
     * Funcion que ejecuta el script de activacion.
     * 
     * @author Creado: Francisco Adum <fadum@telconet.ec>
     * @version 1.0 21-07-2014
     * 
     * @param InfoInterfaceElemento     $interfaceElemento
     * @param int                       $idClienteElemento
     * @param String                    $macOnt
     * @param String                    $perfil
     * @param String                    $login
     * @param int                       $idDocumento
     * @param String                    $usuario
     * @param String                    $protocolo
     * @param InfoServicioTecnico       $servicioTecnico
     * @return json con dos valores: String 'status' indica OK/ERROR, String 'mensaje' indica el mensaje a presentar en caso de ERROR
     */
    public function activarClienteOlt($interfaceElemento, $idClienteElemento, $macOnt, $perfil, $login,
                                      $idDocumento, $usuario, $protocolo, $servicioTecnico)
    {
        $loginTrunk = substr($login, 0, 17);
        $datos = $interfaceElemento->getNombreInterfaceElemento().",".$idClienteElemento.",".$idClienteElemento.",".$macOnt.",".$idClienteElemento.",".$perfil.",".$idClienteElemento.",".$loginTrunk;
        $comando = "java -jar -Djava.security.egd=file:/dev/./urandom /home/telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '".$this->host."' '".$idDocumento."' '".$usuario."' '".$protocolo."' '".$servicioTecnico->getElementoId()."' '".$datos."'";
        $salida= shell_exec($comando);
        $pos = strpos($salida, "{"); 
        $jsonObj= substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);
        
        return $resultadJson;
    }
}