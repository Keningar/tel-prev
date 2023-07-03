<?php

namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\ReturnResponse;
use \telconet\schemaBundle\Entity\InfoProcesoMasivoCab;
use \telconet\schemaBundle\Entity\InfoProcesoMasivoDet;
use telconet\schemaBundle\Entity\InfoServicioHistorial;

/**
 * Ipv4PublicoMasivoController, controlador que contiene los metodos para asignar mediante un archivo csv
 * a los clientes una ipv4 publica
 * 
 * @author Francisco Adum <fadum@netlife.net.ec>
 * @version 22-09-2017
 * @since 1.0
 */
class Ipv4PublicoMasivoController extends Controller
{

    /**
     * indexAction, index de la opcion
     * 
     * @author Francisco Adum <fadum@netlife.net.ec>
     * @version 22-09-2017
     * @since 1.0
     * 
     * @return render Redirecciona al index de la opcion
     * 
     * @Secure(roles="ROLE_399-1")
     */
    public function indexAction()
    {
        $arrayRolesPermitidos = array();
        if(true === $this->get('security.context')->isGranted('ROLE_355-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_399-1'; //Carga informacion GIS
        }
        return $this->render('tecnicoBundle:Ipv4PublicoMasivo:index.html.twig', array('rolesPermitidos' => $arrayRolesPermitidos));
    } //indexAction

    /**
     * upLoadFileAction, sube el archivo que contiene la informacion de GIS
     * 
     * @author Francisco Adum <fadum@netlife.net.ec>
     * @version 22-09-2017
     * @since 1.0
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @Secure(roles="ROLE_399-1")
     */
    public function leerProcesarArchivoAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $emComercial        = $this->get('doctrine')->getManager('telconet');
        $emInfraestructura  = $this->get('doctrine')->getManager('telconet_infraestructura');
        $intIdEmpresa       = $objSession->get('idEmpresa');
        
        $strIdSevicio       = "";
        $arrayClientes      = array();
        $serviceGeneral     = $this->get('tecnico.InfoServicioTecnico');
        $serviceRDA         = $this->get('tecnico.RedAccesoMiddleware');
        
        $emComercial->getConnection()->beginTransaction();
        $emInfraestructura->getConnection()->beginTransaction();
        try 
        {
            $objProducto    = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                ->findOneBy(array(  "nombreTecnico" => "INTERNET",
                                                                    "empresaCod"    => $intIdEmpresa, 
                                                                    "estado"        => "Activo"));
            
            //grabar cabecera del proceso masivo
            $objProcesoMasivoCab = new InfoProcesoMasivoCab();
            $objProcesoMasivoCab->setTipoProceso("AsignaIpv4Publico");
            $objProcesoMasivoCab->setEmpresaCod($intIdEmpresa);
            $objProcesoMasivoCab->setEstado("Pendiente");
            $objProcesoMasivoCab->setUsrCreacion($objSession->get('user'));
            $objProcesoMasivoCab->setFeCreacion(new \DateTime('now'));
            $objProcesoMasivoCab->setIpCreacion($objRequest->getClientIp());
            $emInfraestructura->persist($objProcesoMasivoCab);
            $emInfraestructura->flush();
            
            //leer el archivo
            $objArchivo = fopen($_FILES['archivo']['tmp_name'], "r");
            
            if($objArchivo === false)
            {
                throw new \Exception("No se pudo abrir el archivo");
            }
            
            while(!feof($objArchivo))
            {
                //leer cada linea del archivo
                $strLinea   = explode(",", trim(fgets($objArchivo)));
                $strLogin   = $strLinea[0];
                $intElemento= $strLinea[1];
                $strPuerto  = $strLinea[2];
                $strIndice  = $strLinea[3];
                $strMac     = $strLinea[4];
                
                if($strLogin)
                {
                    //obtener el id_servicio del cliente
                    $objPunto       = $emComercial->getRepository('schemaBundle:InfoPunto')->findOneByLogin(trim($strLogin));
                    if(!is_object($objPunto))
                    {
                        throw new \Exception("No existe el punto del login:". $strLogin . "\n FAVOR REVISAR EL ARCHIVO!");
                    }

                    $objServicio    = $emComercial->getRepository('schemaBundle:InfoServicio')->obtieneProductoInternetxPunto($objPunto->getId());
                    if(!is_object($objServicio))
                    {
                        throw new \Exception("No existe el Servicio Internet para el login:". $strLogin . "\n FAVOR REVISAR EL ARCHIVO!");
                    }

                    //grabar proceso masivo det
                    $objProcesoMasivoDet = new InfoProcesoMasivoDet();
                    $objProcesoMasivoDet->setProcesoMasivoCabId($objProcesoMasivoCab);
                    $objProcesoMasivoDet->setPuntoId($objPunto->getId());
                    $objProcesoMasivoDet->setServicioId($objServicio->getId());
                    $objProcesoMasivoDet->setEstado("Pendiente");
                    $objProcesoMasivoDet->setUsrCreacion($objSession->get('user'));
                    $objProcesoMasivoDet->setFeCreacion(new \DateTime('now'));
                    $objProcesoMasivoDet->setIpCreacion($objRequest->getClientIp());
                    $emInfraestructura->persist($objProcesoMasivoDet);
                    $emInfraestructura->flush();

                    //grabar el historial del servicio
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicio);
                    $objServicioHistorial->setObservacion("Empezó el proceso de asignación de Ipv4 Pública");
                    $objServicioHistorial->setEstado($objServicio->getEstado());
                    $objServicioHistorial->setUsrCreacion($objSession->get('user'));
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($objRequest->getClientIp());
                    $emComercial->persist($objServicioHistorial);
                    $emComercial->flush();

                    //grabar caracteristica ipv4 en el servicio
                    $serviceGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "IPV4", "PUBLICO", $objSession->get('user'));

                    //concatenar con | los id_servicio para enviar a actualizar el arbol ldap
                    $strIdSevicio = $strIdSevicio . $objServicio->getId() . '|' ;

                    //armar arreglo para Middleware RDA
                    $arrayClientes[] = array(
                                                'login'                 => $strLogin,
                                                'elemento'              => $intElemento,
                                                'puerto'                => $strPuerto,
                                                'ont_id'                => $strIndice,
                                                'mac'                   => $strMac,
                                                'procesoMasivoDetId'    => $objProcesoMasivoDet->getId(),
                                                'servicioId'            => $objServicio->getId()
                                            );
                }
            }
            fclose($objArchivo);
        }
        catch(\Exception $e)
        {
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }
            
            if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
            }
            
            $this->get('session')->getFlashBag()->add('notice', 'Ocurrio un error: \n ' . $e->getMessage() );
            
            return $this->redirect($this->generateUrl('procesomasivo_asignarIpv4Publico'));
        }
        
        //*DECLARACION DE COMMITS*/
        if($emComercial->getConnection()->isTransactionActive())
        {
            $emComercial->getConnection()->commit();
        }
        
        if($emInfraestructura->getConnection()->isTransactionActive())
        {
            $emInfraestructura->getConnection()->commit();
        }
        
        try
        {
            //eliminar ultimo caracter |
            $strIdSevicio = rtrim($strIdSevicio,"|");

            //enviar a ldap los clientes.
            $objResultadoJsonLdap = $serviceGeneral->ejecutarComandoLdap("X", $strIdSevicio);
            if($objResultadoJsonLdap->status!="OK")
            {
                $strMensaje = $strMensaje . "<br>" . $objResultadoJsonLdap->mensaje;
            }

            //enviar a middleware los clientes
            $arrayDatos = array(
                                'datos'         => $arrayClientes,
                                'opcion'        => "ASIGNAR_IPV4_PUBLICO_MASIVO",
                                'ejecutaComando'=> "SI",
                                'usrCreacion'   => $objSession->get('user'),
                                'ipCreacion'    => $objRequest->getClientIp()
                               );

            $serviceRDA->middleware(json_encode($arrayDatos));
            
            $this->get('session')->getFlashBag()->add('success', 'Archivo Cargado correctamente, se empezó la asignación pública de ipv4');
        }
        catch(\Exception $e)
        {
            $objProcesoMasivoCab->setEstado("Eliminado");
            $emInfraestructura->persist($objProcesoMasivoCab);
            $emInfraestructura->flush();
            
            $arrayRespuestaRollback = $emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                        ->setEstadoProcesoMasivoDetByProcesoMasivoCabId($objProcesoMasivoCab->getId(), "Eliminado");
            
            
            $this->get('session')->getFlashBag()->add('notice', 'Ocurrio un error: \n ' . $e->getMessage() . 
                                                                ' \n' . $arrayRespuestaRollback['mensaje'] );
            
            return $this->redirect($this->generateUrl('procesomasivo_asignarIpv4Publico'));
        }
        
        return $this->redirect($this->generateUrl('procesomasivo_asignarIpv4Publico'));
    }
}
