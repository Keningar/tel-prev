<?php

namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoIpElemento;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoServicio;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use telconet\schemaBundle\Repository\InfoServicioRepository;
use Symfony\Component\Validator\Constraints\Length;

class InfoServicioReactivacionController extends Controller implements TokenAuthenticatedController {

    public function reactivacionMasivaClienteAction() {
        $em = $this->getDoctrine()->getManager('telconet');
        $request = $this->getRequest();
        $session = $request->getSession();
        return $this->render('tecnicoBundle:InfoServicioReactivacion:reactivacionMasivaCliente.html.twig', array());
    }

    public function getServiciosAReactivarAction() {
        // ...
        $peticion = $this->get('request');
        $session = $peticion->getSession();
        // ...
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        // ...
        $idEmpresa = $session->get('idEmpresa');       
        // ..
        $fechaCorteDesde = $peticion->query->get('fechaCorteDesde');
        $fechaCorteHasta = $peticion->query->get('fechaCorteHasta');
        $valorMontoDeuda = $peticion->query->get('valorMontoDeuda');
        $idsOficinas     = $peticion->query->get('idsOficinas');
        $strClienteCanal = $peticion->query->get('clienteCanal')?$peticion->query->get('clienteCanal'):'Todos';
        $ultimaMilla     = $peticion->query->get('ultimaMilla');        
        // ...
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $arrayParametros['idEmpresa']       = $idEmpresa;
        $arrayParametros['fechaCorteDesde'] = $fechaCorteDesde;
        $arrayParametros['fechaCorteHasta'] = $fechaCorteHasta;
        $arrayParametros['valorMontoDeuda'] = $valorMontoDeuda;
        $arrayParametros['idsOficinas']     = $idsOficinas;
        $arrayParametros['clienteCanal']    = $strClienteCanal;
        $arrayParametros['ultimaMilla']     = $ultimaMilla;
        
        // ...
        /* @var $serviceProcesoMasivo \telconet\tecnicoBundle\Service\ProcesoMasivoService */
        $serviceProcesoMasivo = $this->get('tecnico.ProcesoMasivo');        
        $objJson = $serviceProcesoMasivo->generarJsonPuntosReactivacion($arrayParametros, $start, $limit);
        $respuesta->setContent($objJson);
        return $respuesta;
    }

    public function getOficinaGrupoConFormaPagoReactivarAction() {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $respuestaFormaPago = new Response();
        $respuestaFormaPago->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $request = $this->getRequest();
        $session = $request->getSession();
        $idEmpresa = $session->get('idEmpresa');
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        $objJson = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoOficinaGrupo')->generarJsonOficinaGrupoPorEmpresa($idEmpresa, "Activo", $start, 100);
        $objJsonFor = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:AdmiFormaPago')->generarJsonFormaPago("Activo", $start, 100);
        return $respuesta->setContent($objJson . "&" . $objJsonFor);
        ;
    }

    /**
     * reactivarClientesMasivoAction
     * 
     * Función para reactivación de clientes masivo llamado desde vista Reactivación Masiva
     * 
     * @version 1.0
     * 
     * @author Javier Hidalgo <jihidalgo@telconet.ec>
     * @version 1.1 10-12-2021 - Se agrega lógica para omitir puntos de clientes en estado InAudit,
     *                           SÓLO si el proceso se realiza desde empresa MEGADATOS. 
     * @since 1.0
     * 
     * @return object $respuesta          
     */
    public function reactivarClientesMasivoAction() {
        $em = $this->get('doctrine')->getManager('telconet');
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        // ...
        $peticion = $this->get('request');
        $session = $peticion->getSession();
        $clientIp = $peticion->getClientIp();
        // ...
        $idEmpresa = $session->get('idEmpresa');
        $prefijoEmpresaSesion = $session->get('prefijoEmpresa');
        $usrCreacion = $session->get('user');
        
        
        $parametro = $peticion->get('param');
        $pagoId = $peticion->get('pagoId');

        if(empty($parametro)){
            // ...
            // $numFacturasAbiertas = $peticion->get('facturas');
            $fechaCorteDesde = $peticion->get('fechaCorteHasta');
            $fechaCorteHasta = $peticion->get('fechaCorteHasta');
            $valorMontoDeuda = $peticion->get('valorMontoDeuda');
            $idsOficinas = $peticion->get('idsOficinas');
            // ...
            $idsPuntos = $peticion->get('idsPuntos');
            $cantidadPuntos = $peticion->get('cantidadPuntos');
        }else{
            $idsServicios = explode("|", $parametro); 
            /* @var $infoPuntoRepository \telconet\schemaBundle\Repository\InfoPuntoRepository */
            $infoPuntoRepository = $em->getRepository('schemaBundle:InfoPunto');
            $idsPuntos = $infoPuntoRepository->findIdsPtosPorIdsServicios($idsServicios, '', '', '');
            $cantidadPuntos = count($idsPuntos);
        }
        
        /*
        * <javera@telconet.ec> - 18/09/2014
        * Se segmentan los puntos por tipo de ultima milla
        * Fibra Optica : Ejecucion VIRGO (MD)
        * Cobre/Radio : Ejecucion Corte Masivo en Telcos (TTCO)
        */
        //Se instancia el Service
        /* @var $serviceProcesoMasivo \telconet\tecnicoBundle\Service\ProcesoMasivoService */
        $serviceProcesoMasivo = $this->get('tecnico.ProcesoMasivo');
       
        //Si el proceso lo realiza SÓLO la empresa Megadatos
        if($idEmpresa == 18)
        {
            //Bandera que indica a nuestro método actualizarEstadoInaudit que es un proceso masivo. 
            $intFlagProcesoMasivo = 1;

            //Verificamos si el usuario posee perfil con permiso para reactivar cliente InAudit
            $intEsPerfilReconectarAbusador = $em->getRepository('schemaBundle:SistPerfil')
                                                                    ->getPerfilesReconexionAbusador($usrCreacion);
            //Obtenemos la característica InAudit
            $objAdmiCaractInaudit = $em->getRepository('schemaBundle:AdmiCaracteristica')
            ->findOneBy(array(
                'descripcionCaracteristica' => 'InAudit',
                'estado' => 'Activo'
            ));

            //Arreglo de puntos en estado In-Audit
            $arrayPuntosInaudit = array();

            //Cadena de string donde se concatenaran los puntos que no estan InAudit
            $strIdsPuntosNoInaudit = '';

            $arrayIdsPuntos = explode("|", $idsPuntos);

            foreach($arrayIdsPuntos as $intIdPunto)
            {
                //Obtengo todos los puntos que se encuentran en estado In-Corte
                $arrayServiciosCortados = $em->getRepository('schemaBundle:InfoServicio')
                                                ->findBy(array('estado'=>'In-Corte','puntoId'=>$intIdPunto));
                                        
                //Obtenemos todos los servicios que se encuentra en estado In-Corte e In-Audit
                foreach($arrayServiciosCortados as $servCortado)
                {
                    $arrayServiciosCortadosInAudit = $em->getRepository("schemaBundle:InfoServicioCaracteristica")
                    ->findBy(array(
                        'servicioId' => $servCortado,
                        'caracteristicaId' => $objAdmiCaractInaudit,
                        'estado' => 'Activo'));
                    
                    if(!empty($arrayServiciosCortadosInAudit))
                    {
                        array_push($arrayPuntosInaudit,$arrayServiciosCortadosInAudit);
                    }
                }

                //Si nuestro arreglo de Servicios InAudit se encuentra vacio implica que ese punto no esta InAudit
                if(empty($arrayPuntosInaudit))
                {
                    //Creamos una cadena con puntos que solo se encuentran en estado In-Corte. 
                    $strIdsPuntosNoInaudit .= $intIdPunto . '|';
                }//Si obtenemos servicios InAudit, se valida si el usuario puede reactivar.
                else if($intEsPerfilReconectarAbusador == 1)
                {
                    $objServiceInfoReconectar = $this->get('tecnico.InfoReconectarServicio');        
                    
                    //Actualizamos el estado de la característica de cada servicio InAudit a Inactivo.
                    foreach($arrayPuntosInaudit as $intKey=>$arrayPunto)
                    {
                        $objServicioInAudit = $arrayPunto[0]->getServicioId();
                        $arrayParametrosInaudit = array(
                                                            'servicio'          => $objServicioInAudit,
                                                            'usrCreacion'       => $usrCreacion,
                                                            'ipCreacion'        => $clientIp,
                                                            'intFlagProcesoMasivo' => $intFlagProcesoMasivo
                                                        );
                        $arrayResActInaudit = $objServiceInfoReconectar->actualizarEstadoInaudit($arrayParametrosInaudit);
                        
                        if($arrayResActInaudit[0]['status'] == 'OK')
                        {
                            //Si el usuario posee perfil para reactivar cliente InAudit, agregamos punto a cadena.
                            $strIdsPuntosNoInaudit .= $intIdPunto . '|';
                        }
                    }
                    
                }

            }
            $arrayPuntossPorUltimaMilla = $serviceProcesoMasivo->obtenerPuntosPorUltimaMilla($strIdsPuntosNoInaudit);

        }
        else
        {
            //Obtenemos puntos por última milla con idsPuntos (Proceso normal)
            $arrayPuntossPorUltimaMilla = $serviceProcesoMasivo->obtenerPuntosPorUltimaMilla($idsPuntos);
        }

        if(!empty($arrayPuntossPorUltimaMilla))
        {
            $strIdsPuntosFO = $arrayPuntossPorUltimaMilla['FO']; //Puntos con Fibra Optica
            $strIdsPuntosCR = $arrayPuntossPorUltimaMilla['CR']; //Puntos con Radio/Cobre

            $intTotalFO = $arrayPuntossPorUltimaMilla['totalFO']; //Total de puntos con Fibra a cortar
            $intTotalCoRa = $arrayPuntossPorUltimaMilla['totalCoRa']; //Total de puntos con Cobre/radio a cortar

            /*             * ***
             * validacion extra para transtelco
             * ***** */
            try
            {
                if($strIdsPuntosCR != '')
                {
                    $strIdsPuntosCR;
                    if(is_array($strIdsPuntosCR))
                    {
                        $puntos = $strIdsPuntosCR;
                        $strIdsPuntosCR = implode('|', $puntos);
                    }
                    else
                    {
                        $puntos = explode('|', $strIdsPuntosCR);
                    }

                    /* @var $infoServicioRepository \telconet\schemaBundle\Repository\InfoServicioRepository */
                    $infoServicioRepository = $em->getRepository('schemaBundle:InfoServicio');
                    $idsServicios = $infoServicioRepository->getIdsServiciosByEstadoAndIdsPuntos('In-Corte', $puntos);

                    $parametro = implode('|', $idsServicios);

                    $fecha = date("Y-m-d");
                    $comando = "nohup java -jar -Djava.security.egd=file:/dev/./urandom /home/telcos/src/telconet/tecnicoBundle/batch/ttco_reactivacionMasiva.jar '" . $parametro . "' '" .
                        $session->get('user') . "' '" . $peticion->getClientIp() .
                        "' >> /home/telcos/src/telconet/tecnicoBundle/batch/reactivacionMasiva-$fecha.txt &";
                    error_log($comando);
                    $salida = shell_exec($comando);
                                        
                    //realizo validacion para obtener la empresa para el flujo
                    $parametros = $em->getRepository('schemaBundle:AdmiParametroDet')
                         ->getOne("EMPRESA_EQUIVALENTE", "", "", "", $prefijoEmpresaSesion, 'CO', "", "");
                    if($parametros)
                    {
                        $prefijoEmpresa = $parametros['valor3'];
                        $objEmpresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($prefijoEmpresa);
                        $idEmpresa = $objEmpresa->getId(); 
                    }
                    
                    $msg = $serviceProcesoMasivo->guardarPuntosPorReactivacionMasivo(null, $prefijoEmpresa, $idEmpresa, $fechaCorteDesde, $fechaCorteHasta, 
                        $valorMontoDeuda, $idsOficinas, $strIdsPuntosCR, $intTotalCoRa, $usrCreacion, $clientIp, $pagoId, null, null, null);
                }

                if($strIdsPuntosFO != '')
                {
                    //realizo validacion para obtener la empresa para el flujo
                    $parametros = $em->getRepository('schemaBundle:AdmiParametroDet')
                         ->getOne("EMPRESA_EQUIVALENTE", "", "", "", $prefijoEmpresaSesion, 'FO', "", "");
                    if($parametros)
                    {
                        $prefijoEmpresa = $parametros['valor3'];
                        $objEmpresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($prefijoEmpresa);
                        $idEmpresa = $objEmpresa->getId(); 
                    }

                    $msg = $serviceProcesoMasivo->guardarPuntosPorReactivacionMasivo(null, $prefijoEmpresa, $idEmpresa, $fechaCorteDesde, $fechaCorteHasta, 
                        $valorMontoDeuda, $idsOficinas, $strIdsPuntosFO, $intTotalFO, $usrCreacion, $clientIp, $pagoId, null, null, null);
                }
                return $respuesta->setContent("Se esta ejecutando el script de: <br> Reactivacion Masiva,"
                    . " favor espere el correo <br> de reporte general");
            }
            catch(\Exception $e)
            {
                return $respuesta->setContent("Disculpe las molestias, existen inconvenientes en el proceso.");
            }
        }
        else
        {
            return $respuesta->setContent("No existen resultados para la clasificacion de puntos.");
        }
    }

}