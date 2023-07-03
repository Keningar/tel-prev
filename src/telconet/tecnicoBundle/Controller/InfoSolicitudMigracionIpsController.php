<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\AdmiModeloElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\tecnicoBundle\Service\InfoServicioTecnicoService;
use telconet\tecnicoBundle\Service\MigracionHuaweiService;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\AdmiTipoDocumento;
use telconet\schemaBundle\Form\InfoElementoServidorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;

/**
 * Clase que sirve para la administracion de los elementos Servidor
 * 
 * @author John Vera <javera@telconet.ec>
 * @version 1.0 19-02-2015
 */
class InfoSolicitudMigracionIpsController extends Controller
{

    /**
     * Funcion que sirve para cargar la pagina inicial
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 21-08-2015
     */
    public function indexAction()
    {
        $rolesPermitidos = array();
        if(true === $this->get('security.context')->isGranted('ROLE_293-2817'))
        {
            $rolesPermitidos[] = 'ROLE_293-2817';
        }

        return $this->render('tecnicoBundle:InfoSolicitudMigracionIp:index.html.twig', array(
                'rolesPermitidos' => $rolesPermitidos
        ));
    }

    /**
     * Funcion que sirve para cargar el formulario de nueva solicitud
     * 
     * @author John Vera  <javera@telconet.ec>
     * @version 1.0 21-08-2015
     * @Secure(roles="ROLE_293-2817")
     */
    public function newAction()
    {
        return $this->render('tecnicoBundle:InfoSolicitudMigracionIp:new.html.twig');
    }
    
    /**
     * Funcion que sirve para cargar el formulario de migracion Logica Tellion
     * 
     * @author Jesus Bozada  <jbozada@telconet.ec>
     * @version 1.0 11-03-2016
     * @Secure(roles="ROLE_293-2817")
     */
    public function newTellionAction()
    {
        return $this->render('tecnicoBundle:InfoSolicitudMigracionIp:newTellion.html.twig');
    }

    /**
     * Funcion que sirve para Crear las solicitudes
     * 
     * @author John Vera <javera@telconet.ec>
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @author Jesus Bozada <jbozada@telconet.ec>
     * 
     * @version 1.0 21-08-2015
     * @version 2.0 16-11-2015 Se modifica proceso de carga de datos y mensaje de notificación
     * @version 2.1 25-01-2016 John Vera Se agrega validaciones 
     * @version 2.2 11-03-2016 Se modifica proceso para soportar migraciones logicas tellion 
     * @version 2.3 04-05-2016 Se corrige proceso de subida de archivos, se recupera modelo de elemento en caso de existir el Elemento 
     * @version 2.4 14-11-2016 Se modifica validación en subida de archivos para permitir el ingreso
     *                         de clientes de OLTs tellion para migración completa(lógica y física) de planes
     * @version 2.5 22-11-2016 Se elimina validación que restringue subir registros de clientes con el mismo plan actual para poder procesar
     *                         migraciones a CNR que estuvieron pausadas por problemas en equipos OLT
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.6 11-12-2018 Se agrega el estado PrePendiente considerado para los clientes que necesiten reproceso
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.7 10-12-2019 Se agrega columna de valor de equipo en la subida del csv y se modifican los estados de validación 
     *                          para la solicitud de cambio de plan masivo
     * 
     */
    public function createAction()
    {
        ini_set('max_execution_time', 3000000);
        $request           = $this->get('request');
        $session           = $request->getSession();
        $em                = $this->get('doctrine')->getManager('telconet');
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emGeneral                  = $this->get('doctrine')->getManager('telconet_general');
        $intContEquiposIncorrectos  = 0;
        $strEquiposIncorrectos      = "";
        $strTipoMigracion  = $request->get('tipoMigracion');
        
        $intContLoginCpm        = 0;
        $contLoginVerificado    = 0;
        $contLoginSinOlt        = 0;
        $contLoginSinInternet   = 0;
        $contLoginNoExiste      = 0;
        $contLoginSinPlan       = 0;
        $contLoginTipo          = 0;
        $contLoginesSinData     = 0;
        $contLoginesExistentes  = 0;        
        $contLoginesMismoPlan   = 0;
        $contLoginesIncorrectos = 0;
        $loginesMismoPlan       = '';
        $loginesExistentes      = '';        
        $loginesSinData         = '';
        $loginesTipo            = '';
        $loginesSinOlt          = '';
        $loginesSinInternet     = '';
        $loginesNoExisten       = '';
        $loginesSinPlan         = '';
        $loginesIncorrectos     = '';
        $strLoginesCpm          = '';
        $strMensajeError        = '';
        $strServiciosPlanes     = '';
        $strServiciosPlanesHtml = '';
        $strHtmlCabeceraTabla   = " </th><tr><th> # </th><th> Elemento </th><th> Login </th><th> Plan Nuevo<br></th></tr>";
        $strHtmlCelda           = "</tr><tr><th colspan=4 style='height:20px; background-color:white;'></th></tr>";
        $cont=0;
        try {

            $objTipoSolicitudTellion = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                          ->findOneBy(array("descripcionSolicitud" => 'SOLICITUD CAMBIO PLAN MASIVO', "estado" => "Activo"));
            $arrayValoresEquipo     = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get( 'VALORES_EQUIPOS_CPM', 
                                                       '', 
                                                       '', 
                                                       '',
                                                       'MASIVO',
                                                       '',
                                                       '',
                                                       '',
                                                       '',
                                                       '');
            $arrayValoresEquipoPermitidos   = array();
            if(is_array($arrayValoresEquipo) && count($arrayValoresEquipo) > 0)
            {
                foreach($arrayValoresEquipo as $arrayValorEquipo)
                {
                    $arrayValoresEquipoPermitidos[] = $arrayValorEquipo['valor2'];
                }
            }


            $file = fopen($_FILES['archivo']['tmp_name'], "r") or exit("Unable to open file!");
            while(!feof($file))
            {
                $boolErrorEquipo = false;
                $linea      = explode(",", trim(fgets($file)));
                $nombreOlt  = $linea[0];                   
                $login      = $linea[1];
                $planNuevo  = $linea[2];
                if(isset($linea[3]) && !empty($linea[3]))
                {
                    $strValorEquipo = $linea[3];
                }
                else
                {
                    $strValorEquipo = '';
                }
                $cont=$cont+1;
                
                if(isset($strValorEquipo) && !empty($strValorEquipo))
                {
                    if(in_array($strValorEquipo, $arrayValoresEquipoPermitidos))
                    {
                        $boolErrorEquipo = false;
                    }
                    else
                    {
                        $boolErrorEquipo = true;
                    }
                }
                else
                {
                    $boolErrorEquipo = false;
                }
                if($boolErrorEquipo)
                {
                    $intContEquiposIncorrectos++;
                    $strEquiposIncorrectos  =   $strEquiposIncorrectos
                                                . '<tr>'
                                                . '<td>'. $intContEquiposIncorrectos. '</td>'
                                                . '<td>'. $nombreOlt                . '</td>'
                                                . '<td>'. $login                    . '</td>'
                                                . '<td>'. $planNuevo                . '</td>'
                                                . '<td>'. $strValorEquipo           . '</td>'
                                                . '</tr>';
                }
                else if ($login)
                {   
                    if($planNuevo && $nombreOlt)
                    { 
                        $objPlan = $em->getRepository('schemaBundle:InfoPlanCab')->findOneById($planNuevo);
                        $objPunto = $em->getRepository('schemaBundle:InfoPunto')->findOneByLogin(trim($login));

                        if($objPlan =='' || $objPlan == null)
                        {
                            $contLoginSinPlan++;
                            $loginesSinPlan = $loginesSinPlan. '<tr>'
                                                             . '<td>'. $contLoginSinPlan  . '</td>'    
                                                             . '<td>'. $nombreOlt . '</td>'
                                                             . '<td>'. $login . '</td>'
                                                             . '<td>'. $planNuevo    . '</td>'
                                                             . '</tr>';
                        }
                        else if($objPunto)
                        {
                            $objServicio = $em->getRepository('schemaBundle:InfoServicio')->getIdsServicioPorIdPunto($objPunto->getId());
                            $objElemento = $em->getRepository('schemaBundle:InfoElemento')->findOneBy(array('nombreElemento' =>trim($nombreOlt),
                                                                                                            'estado'         => 'Activo'));
                            
                            if($objElemento)
                            {
                                $objModeloElemento = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                                   ->find($objElemento->getModeloElementoId());
                                if ($strTipoMigracion == $objModeloElemento->getMarcaElementoId()->getNombreMarcaElemento() ||
                                    $objModeloElemento->getMarcaElementoId()->getNombreMarcaElemento() == "TELLION")
                                {
                                    if($objServicio)
                                    {
                                        //verificamos si ya tiene una solicitud existente
                                        $objSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                           ->findOneBy(array('servicioId'       => $objServicio[0]->getId(),
                                                                             'tipoSolicitudId'  => $objTipoSolicitudTellion->getId(),
                                                                             'estado'           => array('Fallo','Pendiente')));

                                        if(!$objSolicitud)
                                        {
                                            $objServicioTecnico = $em->getRepository('schemaBundle:InfoServicioTecnico')
                                                ->findBy(array('elementoId' => $objElemento->getId(), 'servicioId' => $objServicio[0]->getId()));

                                            $objPlanViejo = $em->getRepository('schemaBundle:InfoPlanCab')
                                                                ->findOneById($objServicio[0]->getPlanId()->getId());
                                            //verifico si son el mismo tipo
                                            if($objPlanViejo->getTipo() == $objPlan->getTipo())
                                            {
                                                $strPlanAplicaCpm     = "";
                                                $objCaracteristicaCpm = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                                           ->findOneBy(array("descripcionCaracteristica" => "APLICA_CPM",
                                                                                             "estado"                    => "Activo"));
                                                if ($objCaracteristicaCpm)
                                                {
                                                   $objPlanCaracCpm = $em->getRepository('schemaBundle:InfoPlanCaracteristica')
                                                                         ->findOneByIdPlanCaracteristica($objPlan->getId() , 
                                                                                                         $objCaracteristicaCpm->getId(),
                                                                                                         'Activo');
                                                   if (is_object($objPlanCaracCpm))
                                                   {
                                                       $strPlanAplicaCpm = $objPlanCaracCpm->getValor();
                                                   }
                                                   else
                                                   {
                                                       $strPlanAplicaCpm = "NO";
                                                   }
                                                }
                                                else
                                                {
                                                    $strPlanAplicaCpm = "NO";
                                                }
                                                if ($strPlanAplicaCpm == "SI")
                                                {
                                                    if($objServicioTecnico)
                                                    {
                                                        $strServiciosPlanes = $objServicio[0]->getId() . "," . $objPlan->getId(). "," .
                                                                              $objPlanViejo->getId().",".$objElemento->getId().",".
                                                                              $strValorEquipo.";" . 
                                                                              $strServiciosPlanes;

                                                        $contLoginVerificado++;
                                                        $strServiciosPlanesHtml = $strServiciosPlanesHtml
                                                                    . '<tr>'
                                                                    . '<td>'. $contLoginVerificado  . '</td>'    
                                                                    . '<td>'. $nombreOlt . '</td>'
                                                                    . '<td>'. $login . '</td>'
                                                                    . '<td>'. $objPlan->getId()     . '</td>'
                                                                    . '</tr>';

                                                    }else
                                                    {
                                                        $contLoginesIncorrectos++;
                                                        $loginesIncorrectos = $loginesIncorrectos
                                                                    . '<tr>'
                                                                    . '<td>'. $contLoginesIncorrectos  . '</td>'
                                                                    . '<td>'. $nombreOlt               . '</td>'
                                                                    . '<td>'. $login                   . '</td>'
                                                                    . '<td>'. $planNuevo               . '</td>'
                                                                    . '</tr>';
                                                    }
                                                }
                                                else
                                                {
                                                    $intContLoginCpm++;
                                                    $strLoginesCpm = $strLoginesCpm
                                                                    . '<tr>'
                                                                    . '<td>'. $intContLoginCpm  . '</td>'
                                                                    . '<td>'. $nombreOlt        . '</td>'
                                                                    . '<td>'. $login            . '</td>'
                                                                    . '<td>'. $planNuevo        . '</td>'
                                                                    . '</tr>';
                                                }
                                            }
                                            else
                                            {                                                        
                                                $contLoginTipo++;
                                                $loginesTipo = $loginesTipo
                                                                . '<tr>'
                                                                . '<td>'. $contLoginTipo  . '</td>'
                                                                . '<td>'. $nombreOlt               . '</td>'
                                                                . '<td>'. $login                   . '</td>'
                                                                . '<td>'. $planNuevo               . '</td>'
                                                                . '</tr>';
                                            }   
                                        }
                                        else
                                        {  
                                            $contLoginesExistentes++;
                                            $loginesExistentes = $loginesExistentes
                                                                    . '<tr>'
                                                                    . '<td>'. $contLoginesExistentes  . '</td>'
                                                                    . '<td>'. $nombreOlt               . '</td>'
                                                                    . '<td>'. $login                   . '</td>'
                                                                    . '<td>'. $planNuevo               . '</td>'
                                                                    . '</tr>';
                                        }                                    
                                    }
                                    else
                                    {
                                        $contLoginSinInternet++;
                                        $loginesSinInternet = $loginesSinInternet
                                                                    . '<tr>'
                                                                    . '<td>'. $contLoginSinInternet  . '</td>'
                                                                    . '<td>'. $nombreOlt               . '</td>'
                                                                    . '<td>'. $login                   . '</td>'
                                                                    . '<td>'. $planNuevo               . '</td>'
                                                                    . '</tr>';
                                    }
                                }
                            }
                            else
                            {
                                 $contLoginSinOlt++;
                                 $loginesSinOlt = $loginesSinOlt
                                                                . '<tr>'
                                                                . '<td>'. $contLoginSinOlt  . '</td>'
                                                                . '<td>'. $nombreOlt               . '</td>'
                                                                . '<td>'. $login                   . '</td>'
                                                                . '<td>'. $planNuevo               . '</td>'
                                                                . '</tr>';
                            }
                        }
                        else
                        {
                            $contLoginNoExiste++;
                            $loginesNoExisten = $loginesNoExisten
                                                                . '<tr>'
                                                                . '<td>'. $contLoginNoExiste  . '</td>'
                                                                . '<td>'. $nombreOlt               . '</td>'
                                                                . '<td>'. $login                   . '</td>'
                                                                . '<td>'. $planNuevo               . '</td>'
                                                                . '</tr>';
                        }
                    }
                    else
                    {
                        $contLoginesSinData++;
                        $loginesSinData = $loginesSinData
                                                                . '<tr>'
                                                                . '<td>'. $contLoginesSinData  . '</td>'
                                                                . '<td>'. $nombreOlt               . '</td>'
                                                                . '<td>'. $login                   . '</td>'
                                                                . '<td>'. $planNuevo               . '</td>'
                                                                . '</tr>';
                    }
                }
            }
            fclose($file);
            if($contLoginesIncorrectos > 0)
            {
                 
                $strMensajeError = "<tr ><th colspan=4> ".$contLoginesIncorrectos . 
                                " clientes no corresponden al olt".$strHtmlCabeceraTabla.$loginesIncorrectos.$strHtmlCelda;
            }
            if($contLoginTipo > 0)
            {
                $strMensajeError = $strMensajeError."<tr ><th colspan=4> ".$contLoginTipo . 
                                " clientes no tienen el mismo tipo de plan".$strHtmlCabeceraTabla.$loginesTipo.$strHtmlCelda;
            }
            if($contLoginesMismoPlan > 0)
            {
                $strMensajeError = $strMensajeError."<tr ><th colspan=4> ".$contLoginesMismoPlan . 
                                " clientes tienen el mismo plan".$strHtmlCabeceraTabla.$loginesMismoPlan.$strHtmlCelda;
            }
            if($contLoginesExistentes > 0)
            {
                $strMensajeError = $strMensajeError."<tr ><th colspan=4> ".$contLoginesExistentes . 
                                " clientes ya tienen una solicitud creada".$strHtmlCabeceraTabla.$loginesExistentes.$strHtmlCelda;
            }
            if($contLoginesSinData > 0)
            {
                $strMensajeError = $strMensajeError."<tr ><th colspan=4> ".$contLoginesSinData . 
                                " clientes no tienen las data completa".$strHtmlCabeceraTabla.$loginesSinData.$strHtmlCelda;
            }
            if($contLoginSinOlt > 0)
            {
                $strMensajeError = $strMensajeError."<tr ><th colspan=4> ".$contLoginSinOlt . 
                                " clientes tienen un olt que no existe".$strHtmlCabeceraTabla.$loginesSinOlt.$strHtmlCelda;
            }
            if($contLoginSinInternet > 0)
            {
                $strMensajeError = $strMensajeError."<tr ><th colspan=4> ".$contLoginSinInternet . 
                                " clientes no tienen un servicio de internet activo".$strHtmlCabeceraTabla.$loginesSinInternet.$strHtmlCelda;
            }
            if($contLoginNoExiste > 0)
            {
                $strMensajeError = $strMensajeError."<tr ><th colspan=4> ".$contLoginNoExiste . 
                                " clientes no existen o estan mal escritos".$strHtmlCabeceraTabla.$loginesNoExisten.$strHtmlCelda;
            }
            if($contLoginSinPlan > 0)
            {
                $strMensajeError = $strMensajeError."<tr ><th colspan=4> ".$contLoginSinPlan . 
                                " clientes no tienen el plan correcto".$strHtmlCabeceraTabla.$loginesSinPlan.$strHtmlCelda;
            }
            if($intContLoginCpm > 0)
            {
                $strMensajeError = $strMensajeError."<tr ><th colspan=4> ".$intContLoginCpm . 
                                   " clientes con planes nuevos que no aplican a procesamiento masivo".
                                   $strHtmlCabeceraTabla.$strLoginesCpm.$strHtmlCelda;
            }
            
            if($intContEquiposIncorrectos > 0)
            {
                $strMensajeError    = $strMensajeError."<tr ><th colspan=5> ".$intContEquiposIncorrectos . 
                                      " clientes con valores de equipos incorrectos".
                                      " </th><tr><th> # </th><th> Elemento </th><th> Login </th><th> Plan Nuevo<br></th><th> Equipo <br></th></tr>"
                                      .$strEquiposIncorrectos."</tr><tr><th colspan=5 style='height:20px; background-color:white;'></th></tr>";
            }
            
            if($strServiciosPlanes)
            {
                /* @var $migracion MigracionHuaweiService */
                $migracion = $this->get('tecnico.MigracionHuawei');
                $mensaje   = $migracion->createSolicitudesCambioPlan($strServiciosPlanes, $session->get('user'),$strTipoMigracion);
            }
            else 
            {
                $mensaje = "No existen clientes para ingresar"; 
            }
            
            if($mensaje)
            {
                $this->get('session')->getFlashBag()->add('notice', 'Ocurrio un error: ' . $mensaje);
                if ($strTipoMigracion == 'HUAWEI')
                {
                    return $this->redirect($this->generateUrl('solicitudMigraIp_new'));
                }
                else
                {
                    return $this->redirect($this->generateUrl('solicitudMigraIp_newTellion'));
                }
            }
            
            if ($strTipoMigracion == 'HUAWEI')
            {
                $asunto          = "Notificación de clientes que cambiarán de plan masivamente";
                $strCuerpoCorreo = "El presente correo es para indicarle que se procedió a subir al telcos los siguientes".
                                   " clientes con los nuevos planes, que serán cambiados masivamente: ";
            }
            else
            {
                $asunto          = "Notificación de clientes que cambiaron de plan masivamente";
                $strCuerpoCorreo = "El presente correo es para indicarle que se procedió a subir al telcos los siguientes".
                                   " clientes con los nuevos planes, que fueron cambiados masivamente: ";
            }
            $parametros = array("registrosClientesOk"      => $strServiciosPlanesHtml,
                                "tieneErrores"             => !empty($strMensajeError) ? "SI" : "NO",
                                "cuerpoCorreo"             => $strCuerpoCorreo,
                                "registrosClientesErrores" => $strMensajeError);
            
            /*Si se crea la entidad hacemos el envio de la notificacion*/
            $envioPlantilla = $this->get('soporte.EnvioPlantilla'); 
            $envioPlantilla->generarEnvioPlantilla($asunto, '', 'CMPUV', $parametros, '', '', '');                

            $this->get('session')->getFlashBag()->add('success', 'Archivo Cargado correctamente, verificar notificación enviada vía '.
                                                                 'correo electronico donde se encuentra el detalle de los Clientes procesados');
            return $this->redirect($this->generateUrl('solicitudMigraIp'));      

        }
        catch(\Exception $e)
        {

            $this->get('session')->getFlashBag()->add('notice', 'Ocurrio un error: ' . $e->getMessage() . '!');
            if ($strTipoMigracion == 'HUAWEI')
            {
                return $this->redirect($this->generateUrl('solicitudMigraIp_new'));
            }
            else
            {
                return $this->redirect($this->generateUrl('solicitudMigraIp_newTellion'));
            }
        }

    }
    
    /**
     * Función que sirve para crear las solicitudes de cambio de plan masivo cuyo método de ejecución se pasará a la base de datos, el cual deberá
     * soportar todas las funcionalidades de la función original createAction
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 26-12-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 17-06-2021 Se modifica la programación para guardar el archivo subido de cambio de plan masivo 
     *                         por medio del microservicio del NFS
     * 
     */
    public function creaSolicitudesCpmAction()
    {
        ini_set('max_execution_time', 3000000);
        $arrayExtensionesPermitidas = array('csv','CSV');
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $strCodEmpresa              = $objSession->get('idEmpresa');
        $strPrefijoEmpresa          = $objSession->get('prefijoEmpresa');
        $strUsrCreacion             = $objSession->get('user');
        $emComunicacion             = $this->get('doctrine')->getManager('telconet_comunicacion');
        $objArchivo                 = $objRequest->files->get('archivo');
        $strMuestraErrorUsuario     = 'NO';
        $boolContinuaSubidaCsvCpm   = false;
        $arrayPathAdicional         = [];
        $serviceUtil                = $this->get('schema.Util');
        
        $emComunicacion->getConnection()->beginTransaction();
        try
        {
            if(isset($objArchivo))
            {
                $strNombreArchivoOriginal       = $objArchivo->getClientOriginalName();
                $arrayPartesNombreArchivo       = explode('.', $strNombreArchivoOriginal);
                $strExtensionArchivo            = array_pop($arrayPartesNombreArchivo);
                $strNombreArchivo               = implode('_', $arrayPartesNombreArchivo);
                
                if (!in_array($strExtensionArchivo, $arrayExtensionesPermitidas))
                {
                    $strMuestraErrorUsuario = 'SI';
                    throw new \Exception ('El archivo no tiene una extensión permitida');
                }
                
                $strCadenaRandom            = substr(md5(uniqid(rand())),0,6);
                $strNuevoNombreArchivo      = $strNombreArchivo . "_" . date('Y-m-d') . "_". $strCadenaRandom;
                $strCaracteresAReemplazar   = "#ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ·";
                $strCaracteresReemplazo     = "_AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn-";
                $strNuevoNombreArchivo      = strtr($strNuevoNombreArchivo, $strCaracteresAReemplazar, $strCaracteresReemplazo);
                $strNuevoNombreArchivoYExt  = $strNuevoNombreArchivo . "." . $strExtensionArchivo;
                
                $strFileBase64                      = base64_encode(file_get_contents($objArchivo->getPathName()));
                $arrayParamsGuardarArchivoNfs       = array(
                                                            'prefijoEmpresa'       => $strPrefijoEmpresa,
                                                            'strApp'               => "TelcosWeb",
                                                            'arrayPathAdicional'   => $arrayPathAdicional,
                                                            'strBase64'            => $strFileBase64,
                                                            'strNombreArchivo'     => $strNuevoNombreArchivoYExt,
                                                            'strUsrCreacion'       => $strUsrCreacion,
                                                            'strSubModulo'         => "CambioPlanMasivo");
                $arrayRespuestaGuardarArchivoNfs    = $serviceUtil->guardarArchivosNfs($arrayParamsGuardarArchivoNfs);
                if(isset($arrayRespuestaGuardarArchivoNfs) && !empty($arrayRespuestaGuardarArchivoNfs) 
                    && $arrayRespuestaGuardarArchivoNfs['intStatus'] == 200)
                {
                    $strUrlArchivoNfs = $arrayRespuestaGuardarArchivoNfs['strUrlArchivo'];
                    if(!isset($strUrlArchivoNfs) || empty($strUrlArchivoNfs))
                    {
                        throw new \Exception('Ocurrió un error al obtener la url del archivo subido al Nfs : '
                                             .$arrayRespuestaGuardarArchivoNfs['strMensaje']);
                    }
                }
                else
                {
                    throw new \Exception('Ocurrió un error al subir archivo al servidor Nfs : '.$arrayRespuestaGuardarArchivoNfs['strMensaje']);
                }
                
                $objDocumentoArchivoCsv = new InfoDocumento();
                $objDocumentoArchivoCsv->setNombreDocumento('Archivo csv subido por cambio de plan masivo '.$strPrefijoEmpresa);
                $objDocumentoArchivoCsv->setMensaje('Documento que se sube para realizar el cambio de plan masivo');
                $objDocumentoArchivoCsv->setUbicacionFisicaDocumento($strUrlArchivoNfs);
                $objDocumentoArchivoCsv->setUbicacionLogicaDocumento($strNuevoNombreArchivoYExt);
                $objDocumentoArchivoCsv->setEstado('Activo');
                $objDocumentoArchivoCsv->setFeCreacion(new \DateTime('now'));
                $objDocumentoArchivoCsv->setFechaDocumento(new \DateTime('now'));
                $objDocumentoArchivoCsv->setIpCreacion('127.0.0.1');
                $objDocumentoArchivoCsv->setUsrCreacion($strUsrCreacion);
                $objDocumentoArchivoCsv->setEmpresaCod($strCodEmpresa);
                            
                $objTipoDocumentoArchivoCsv = $emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                             ->findOneByExtensionTipoDocumento(array(   'extensionTipoDocumento'   => 
                                                                                                        strtoupper($strExtensionArchivo)));
                if(is_object($objTipoDocumentoArchivoCsv))
                {
                    $objDocumentoArchivoCsv->setTipoDocumentoId($objTipoDocumentoArchivoCsv);
                }
                else
                {
                    $objTipoDocumentoArchivoCsv = new AdmiTipoDocumento();
                    $objTipoDocumentoArchivoCsv->setExtensionTipoDocumento(strtoupper($strExtensionArchivo));
                    $objTipoDocumentoArchivoCsv->setTipoMime(strtoupper($strExtensionArchivo));
                    $objTipoDocumentoArchivoCsv->setDescripcionTipoDocumento('ARCHIVO FORMATO '.$strExtensionArchivo);
                    $objTipoDocumentoArchivoCsv->setEstado('Activo');
                    $objTipoDocumentoArchivoCsv->setUsrCreacion($strUsrCreacion);
                    $objTipoDocumentoArchivoCsv->setFeCreacion(new \DateTime('now'));
                    $emComunicacion->persist($objTipoDocumentoArchivoCsv);
                    $emComunicacion->flush();
                    $emComunicacion->setTipoDocumentoId($objTipoDocumentoArchivoCsv);
                }
                
                $emComunicacion->persist($objDocumentoArchivoCsv);
                $emComunicacion->flush();
                $intIdDocumentoCsvSubido = $objDocumentoArchivoCsv->getId();
                $emComunicacion->commit();
                $boolContinuaSubidaCsvCpm = true;
            }
            else
            {
                $strMuestraErrorUsuario = 'SI';
                throw new \Exception('No se ha podido procesar correctamente el archivo');
            }
        }
        catch(\Exception $e)
        {
            if($strMuestraErrorUsuario === "SI")
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "Ha ocurrido un problema inesperado en la subida del archivo al NFS. Por favor comuníquese con Sistemas";
            }
            error_log("Error al subir archivo para cambio de plan masivo ".$e->getMessage());
            if($emComunicacion->getConnection()->isTransactionActive())
            {
                $emComunicacion->getConnection()->rollback();
            }
            $emComunicacion->getConnection()->close();
            $this->get('session')->getFlashBag()->add('notice', $strMensaje);
            return $this->redirect($this->generateUrl('solicitudMigraIp_new'));
        }
        
        try
        {
            if($boolContinuaSubidaCsvCpm && isset($intIdDocumentoCsvSubido) && !empty($intIdDocumentoCsvSubido))
            {
                $arrayParamsSubidaCsv   = array(
                                                "intIdDocumentoCsvSubido"   => $intIdDocumentoCsvSubido,
                                                "strUsrCreacion"            => $strUsrCreacion,
                                                "strCodEmpresa"             => $strCodEmpresa,
                                                "strPrefijoEmpresa"         => $strPrefijoEmpresa,

                                        );
                $arrayRespuestaSubidaCsv    = $this->ejecutaSubidaCsvCpm($arrayParamsSubidaCsv);
                $strStatusSubidaCsv         = $arrayRespuestaSubidaCsv["status"];
                $strMensajeSubidaCsv        = $arrayRespuestaSubidaCsv["mensaje"];
                
                if($strStatusSubidaCsv === "OK")
                {
                    
                    $this->get('session')->getFlashBag()->add('success', 'Archivo Cargado correctamente, verificar notificación enviada '.
                                                                         'vía correo electrónico donde se encuentra el detalle de '.
                                                                         'los clientes procesados ');
                    return $this->redirect($this->generateUrl('solicitudMigraIp'));
                }
                else
                {
                    $strMuestraErrorUsuario = 'SI';
                    throw new \Exception($strMensajeSubidaCsv);
                }
            }
            else
            {
                $strMuestraErrorUsuario = "SI";
                throw new \Exception('Ha ocurrido un error al obtener la información del archivo subido al NFS. Por favor comuníquese con Sistemas');
            }
        }
        catch (\Exception $e)
        {
            if($strMuestraErrorUsuario === "SI")
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "Ha ocurrido un problema inesperado al intentar procesar el csv. Por favor comuníquese con Sistemas";
            }
            $this->get('session')->getFlashBag()->add('notice', $strMensaje);
            return $this->redirect($this->generateUrl('solicitudMigraIp_new'));
        }
    }

    /**
     * Función que sirve para ejecutar el procedimiento de Base de Datos que ejecuta la validación del archivo csv y creación de solicitudes 
     * de cambio de plan masivo
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 27-12-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 21-06-2021 Se modifica la función por cambios en el envío de parámetros al procedimiento P_UPLOAD_CSV_CPM
     * 
     */
    public function ejecutaSubidaCsvCpm($arrayParametros)
    {
        $strDatabaseDsn             = $this->container->getParameter('database_dsn');
        $strUserInfraestructura     = $this->container->getParameter('user_infraestructura');
        $strPasswordInfraestructura = $this->container->getParameter('passwd_infraestructura');
        $intIdDocumentoCsvSubido    = $arrayParametros["intIdDocumentoCsvSubido"];
        $strUsrCreacion             = $arrayParametros["strUsrCreacion"];
        $strCodEmpresa              = $arrayParametros["strCodEmpresa"];
        $strPrefijoEmpresa          = $arrayParametros["strPrefijoEmpresa"];
        $strMuestraErrorUsuario     = 'NO';
        $strStatus                  = '';
        $strMensaje                 = '';

        try
        {
            if(!isset($intIdDocumentoCsvSubido) || empty($intIdDocumentoCsvSubido))
            {
                $strMuestraErrorUsuario = 'SI';
                throw new \Exception ('No se ha podido obtener el archivo subido en la opción de cambio de plan masivo');
            }
            
            if(!isset($strUsrCreacion) || empty($strUsrCreacion))
            {
                $strMuestraErrorUsuario = 'SI';
                throw new \Exception ('No se ha podido obtener el usuario en sesión');
            }
            
            $strSql         = " BEGIN DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.P_UPLOAD_CSV_CPM(
                                    :Pcl_Parametros,
                                    :Pv_Status,
                                    :Pv_Mensaje); 
                                END;";
            $objConn = oci_connect($strUserInfraestructura, $strPasswordInfraestructura, $strDatabaseDsn);
            $objStmt = oci_parse($objConn, $strSql);

            $arrayParams       = array('intIdArchivoCsvCpm'   => $intIdDocumentoCsvSubido,
                                            'strCodEmpresa'        => $strCodEmpresa,
                                            'strPrefijoEmpresa'    => $strPrefijoEmpresa,
                                            'strUsrCreacion'       => $strUsrCreacion);
            
            $objParamsClob = oci_new_descriptor($objConn);
            $objParamsClob->writetemporary(json_encode($arrayParams));
            
            oci_bind_by_name($objStmt,':Pcl_Parametros', $objParamsClob, -1, SQLT_CLOB);
            oci_bind_by_name($objStmt, ':Pv_Status', $strStatus, 5);
            oci_bind_by_name($objStmt, ':Pv_Mensaje', $strMensaje, 2000);
            oci_execute($objStmt);
        }
        catch (\Exception $e)
        {
            if($strMuestraErrorUsuario === "SI")
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "Ha ocurrido un problema al intentar ejecutar la creación de solicitudes. Por favor comuníquese con Sistemas!";
            }
            $strStatus  = "ERROR";
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }
    
    /**
     * Funcion que sirve para ver la solicitud raiz
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 21-08-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 24-12-2019 Se agrega la obtención del id de la solicitud padre
     */
    public function showSolicitudPadreAction()
    {
        $em = $this->getDoctrine()->getManager('telconet');
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');
        $idDetalleSolicitud = $peticion->query->get('idDetalleSolicitud');
        $objCaracteristica = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                ->findOneBy(array("descripcionCaracteristica" => 'REFERENCIA SOLICITUD', "estado" => "Activo"));
        $objdetalleCaract = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                               ->findOneBy(array("detalleSolicitudId" => $idDetalleSolicitud,
                                                 "caracteristicaId" => $objCaracteristica->getId()));
        $solicitudesArray = array();

        if($objdetalleCaract)
        {
            $objSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($objdetalleCaract->getValor());

            $solicitudesArray[] = array(
                                        'idSolPadre'    => $objSolicitud->getId(),
                                        'tipoSolicitud' => $objSolicitud->getTipoSolicitudId()->getDescripcionSolicitud(),
                                        'estado'        => $objSolicitud->getEstado(),
                                        'fechaCrea'     => date_format($objSolicitud->getFeCreacion(), 'Y-m-d H:i:s'),
                                        'usuarioCrea'   => $objSolicitud->getUsrCreacion());
        }

        if($solicitudesArray)
        {
            $data = '{"total":' . count($solicitudesArray) . ',"encontrados":' . json_encode($solicitudesArray) . '}';
        }
        else
        {
            $data = '{"total":"0","encontrados":[]}';
        }

        $respuesta->setContent($data);

        return $respuesta;
    }

    /**
     * Funcion que sirve para realizar la busqueda de solicitudes
     * por medio de un filtro
     * 
     * @author John Vera <javera@telconet.ec>
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 21-08-2015
     * @version 1.1 13-11-2015 Se modifica funcion para traer registros correctos
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 03-01-2020 Se modifica el envío de parámetros a la función getSolicitudesByLoginElementoTipo por observación del Sonar
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.3 17-04-2023 Se filtro por empresa en sesión
     * 
     */
    public function getEncontradosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $peticion = $this->get('request');
        $objSession     = $peticion->getSession();
        $login          = $peticion->query->get('login');
        $elementoId     = $peticion->query->get('elemento');
        $tipoSolicitud  = $peticion->query->get('solicitud');
        $estado         = $peticion->query->get('estado');
        $strCodEmpresa  = $objSession->get('idEmpresa');
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');

        if($tipoSolicitud)
        {
            $objTipoSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                   ->findOneBy(array("descripcionSolicitud" => $tipoSolicitud, "estado" => "Activo"));
            $tipoSolicitudId = $objTipoSolicitud->getId();
        }
        else
        {
            $objTipoSolicitudPlan = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                       ->findOneBy(array("descripcionSolicitud" => 'SOLICITUD CAMBIO PLAN MASIVO', "estado" => "Activo"));
            
            $tipoSolicitudId = $objTipoSolicitudPlan->getId();
        }

        $arrayMigracion = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                             ->getSolicitudesByLoginElementoTipo(array( "login"             => $login, 
                                                                        "idElemento"        => $elementoId, 
                                                                        "idTipoSolicitud"   => $tipoSolicitudId, 
                                                                        "estado"            => $estado, 
                                                                        "start"             => $start, 
                                                                        "limit"             => $limit,
                                                                        "codEmpresa"        => $strCodEmpresa));
        $respuesta->setContent($arrayMigracion);

        return $respuesta;
    }

}