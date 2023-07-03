<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiNotiPushMktController extends Controller implements
    TokenAuthenticatedController
{
    /**
     * @Secure(roles="ROLE_488-1")
     *
     * Documentación para la función 'indexAction'.
     *
     * Función que carga la pantalla de Administración de Notificaciones.
     *
     * @return render Redirecciona al index de la opción.
     *
     * @author Andrea Orellana <adorellana@telconet.ec>
     * @version 1.0 17-11-2022
     *
     */
    public function indexAction()
    {
        try 
        { 
            $objRequest = $this->getRequest();
            $strUsrCreacion = $objRequest->getSession()->get('user');
            $strIpCreacion = $objRequest->getClientIp();
            $serviceUtil = $this->get('schema.Util');
            $arrayRolesPermitidos = [];

            if ($this->get('security.context')->isGranted('ROLE_488-1')) 
            {
                $arrayRolesPermitidos[] = 'ROLE_488-1';
            }
            if ($this->get('security.context')->isGranted('ROLE_488-2')) 
            {
                $arrayRolesPermitidos[] = 'ROLE_488-2';
            }
            if ($this->get('security.context')->isGranted('ROLE_488-6')) 
            {
                $arrayRolesPermitidos[] = 'ROLE_488-6';
            }
            if ($this->get('security.context')->isGranted('ROLE_488-4')) 
            {
                $arrayRolesPermitidos[] = 'ROLE_488-4';
            }
            if ($this->get('security.context')->isGranted('ROLE_488-8')) 
            {
                $arrayRolesPermitidos[] = 'ROLE_488-8';
            }
            if ($this->get('security.context')->isGranted('ROLE_488-9')) 
            {
                $arrayRolesPermitidos[] = 'ROLE_488-9';
            }
            if ($this->get('security.context')->isGranted('ROLE_488-7')) 
            {
                $arrayRolesPermitidos[] = 'ROLE_488-7';
            }
            if ($this->get('security.context')->isGranted('ROLE_488-8937')) 
            {
                $arrayRolesPermitidos[] = 'ROLE_488-8937';
            }
        } catch (\Exception $e) 
        {
            $serviceUtil->insertError(
                'TelcoS+',
                'AdmiNotiPushMktController.indexAction',
                $e->getMessage(),
                $strUsrCreacion,
                $strIpCreacion
            );
        }
        return $this->render('administracionBundle:AdmiNotiPushMkt:index.html.twig', ['rolesPermitidos' => $arrayRolesPermitidos]);
    }

    /**
     * @Secure(roles="ROLE_488-7")
     *
     * Documentación para la función 'gridAction'.
     *
     * Función que retorna el listado de Plantillas Notificaciones.
     *
     * @return $objResponse - Listado de Notificaciones.
     *
     * @author Andrea Orellana <adorellana@telconet.ec>
     * @version 1.0 20-01-2023
     *
     * @author Andrea Orellana <adorellana@telconet.ec>
     * @version 1.0 17-05-2023 se agrega parametro de estado de la campaña que tendrá en la ejecución de la acción de detener y reiniciar. 
      */
    public function gridAction()
    {
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        $strPrefijoIdEmpresa = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : '';
        $strUsrCreacion = $objSession->get('user') ? $objSession->get('user') : '';
        $strIpCreacion = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $serviceCampaing = $this->get('administracion.CampaingNotiPush');
        $intTotal = 0;
        $serviceUtil = $this->get('schema.Util');
        $arrayResultado = [];
        try 
        {
            $arrayParametros = [];
            $arrayParametros['strUsrCreacion'] = $strUsrCreacion;
            $arrayParametros['strIpCreacion'] = $strIpCreacion;
            $arrayParametros['intIdEmpresa'] = $strPrefijoIdEmpresa;

            $arrayCampaniasResponse = $serviceCampaing->consultarCampaniasMs($arrayParametros);

            if (!empty($arrayCampaniasResponse)) 
            {
                foreach ($arrayCampaniasResponse['objData'] as $arrayDatos) 
                {
                    $arrayDataLink = ['intIdCampania' => $arrayDatos['idCampaing']];
                    $arrayDataDetLink = ['intIdCampania' => $arrayDatos['idCampaing'], 
                                         'strEstado'     => 'Detenida'];

                    foreach ($arrayDatos['configCampaing'] as $arrayConfig) 
                    {
                        $objPropertiesCampania = $arrayConfig['propertyCampain'];
                            if ($objPropertiesCampania['nameProperty'] == 'FechaInicio') 
                            {
                                $strFechaIni = $arrayConfig['value'];
                            }
                    }

                   if ($arrayDatos['status'] == 'Pendiente') 
                   {
                        $strLinkVer = [
                            'linkVer' => $this->generateUrl('com_admiNotiPushMkt_show'),
                            'linkEditar' => $this->generateUrl('com_admiNotiPushMkt_edit'),
                            'linkDetener' => '',
                            'linkEliminar' => '',
                            'linkReini' => ''
                        ];
                    } elseif ($arrayDatos['status'] == 'Programada') 
                    {
                        $strLinkVer = [
                            'linkVer' => $this->generateUrl('com_admiNotiPushMkt_show'),
                            'linkEditar' => $this->generateUrl('com_admiNotiPushMkt_edit'),
                            'linkDetener' => $this->generateUrl('com_admiNotiPushMkt_detener', $arrayDataDetLink),
                            'linkEliminar' => $this->generateUrl('com_admiNotiPushMkt_delete', $arrayDataLink),
                            'linkReini' => ''
                        ];
                    }elseif ($arrayDatos['status']== 'Detenida') 
                    {
                        $arrayLinkPlay = ['intIdCampania' => $arrayDatos['idCampaing'],
                                          'strEstado'     => 'Programada'];
                        $strLinkVer = [
                            'linkVer' => $this->generateUrl('com_admiNotiPushMkt_show'),
                            'linkEditar' => '',
                            'linkDetener' => '',
                            'linkEliminar' => '', 
                            'linkReini' => $this->generateUrl('com_admiNotiPushMkt_reini', $arrayLinkPlay)
                        ];
                    }
                    if ($arrayDatos['status']== 'Finalizada' || $arrayDatos['status'] == 'Publicada' || 
                        $arrayDatos['status'] == 'Eliminada') 
                    {
                        $strLinkVer = [
                            'linkVer' => $this->generateUrl('com_admiNotiPushMkt_show'),
                            'linkEditar' => '',
                            'linkDetener' => '',
                            'linkEliminar' => '',
                            'linkReini' => ''
                        ];
                    }

                    $arrayResultado[$intTotal] = array(
                                                        'idCampania' => $arrayDatos['idCampaing'],
                                                        'strNombreCampania' => $arrayDatos['name'],
                                                        'strEstado' => $arrayDatos['status'],
                                                        'strFechaIni' => $strFechaIni,
                                                        'configuraciones' => $arrayDatos['configCampaing'],
                                                        'strAcciones' => $strLinkVer,
                                                        'strUsrCreacion' => $arrayDatos['creationUser'],
                                                        'strFechaCreacion'=> $arrayDatos['creationDate']);

                    $intTotal = $intTotal +1;
                }
            }
        } catch (\Exception $e) 
        {
            echo $e->getMessage();
            $serviceUtil->insertError(
                'TelcoS+',
                'AdmiNotiPushMktController.gridAction',
                $e->getMessage(),
                $strUsrCreacion,
                $strIpCreacion
            );
        }
        $objResponse = new Response(json_encode(['intTotal' => $intTotal, 'data' => $arrayResultado]));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_488-6")
     *
     * Documentación para la función 'showAction'.
     *
     * Función que renderiza la página de Ver detalle.
     *
     * @return render - Página de Ver Notificacion.
     *
     * @author Andrea Orellana <adorellana@telconet.ec>
     * @version 1.0 14-01-2023
     *
     */
    public function showAction()
    {
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        $strUsrCreacion = $objSession->get('user') ? $objSession->get('user') : '';
        $strIpCreacion = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $serviceUtil = $this->get('schema.Util');
        $objDataCampania = $objRequest->get('data');
        $arrayProyecto = [];
        try 
        {
          
            if (empty($objDataCampania)) 
            {
                throw new \Exception('Ha ocurrido un error durante el proceso');
            }

            foreach($objDataCampania['configuraciones'] as $objConfig)
            {
                if($objConfig['propertyCampain']['nameProperty'] ==	"Titulo")
                {
                    $strTitulo = $objConfig['value'];
                }
                if($objConfig['propertyCampain']['nameProperty'] == "Texto")
                {
                    $strTexto = $objConfig['value'];
                }
            }

            $arrayProyecto = [
                'idCampania' => $objDataCampania['idCampania'],
                'strNombreCampania' => $objDataCampania['strNombreCampania'],
                'strTitulo' => $strTitulo,
                'strDetalle' => $strTexto,
                'strEstado' => $objDataCampania['strEstado'],
                'strFechaIni' => $objDataCampania['strFechaIni'], 
                'strUsrCreacion' => $objDataCampania['strUsrCreacion'],
                'strFeCreacion' => $objDataCampania['strFechaCreacion']
            ];
        } catch (\Exception $e)
         {
            $serviceUtil->insertError(
                'TelcoS+',
                'AdmiNotiPushMktController.showAction',
                $e->getMessage(),
                $strUsrCreacion,
                $strIpCreacion
            );
        }
        return $this->render(
            'administracionBundle:AdmiNotiPushMkt:show.html.twig',
            ['arrayCampaniaDet' => $arrayProyecto]
        );
    }

    /**
     * Documentación para la función 'saveAction'.
     *
     * Función que crea los Notificacions.
     *
     * @return Response - Mensaje de exito.
     *
     * @author Andrea Orellana <adorellana@telconet.ec>
     * @version 1.0 19-01-2023
     *
     */
    public function saveAction()
    {
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        $strUsrCreacion = $objSession->get('user') ? $objSession->get('user') : '';
        $strIpCreacion = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $objPrefEmpresa = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : '';
        $serviceUtil = $this->get('schema.Util');
        $strCodigoError = '';
        $serviceCampaing = $this->get('administracion.CampaingNotiPush');
        $arrayParametros = [];
        $intContador = 0;
        try 
        {
            //Parametros
            $strTitulo = $objRequest->get('strTituloNoti') ? $objRequest->get('strTituloNoti') : '';
            $strTexto = $objRequest->get('strTextoNoti') ? $objRequest->get('strTextoNoti') : '';
            $strImg = $objRequest->get('strImgNoti') ? $objRequest->get('strImgNoti') : '';
            $strNombre = $objRequest->get('strNombreNoti') ? $objRequest->get('strNombreNoti') : '';
            $intHraVencimiento = $objRequest->get('intHraVencimiento') ? $objRequest->get('intHraVencimiento') : '';
            $strPantallaSelec = $objRequest->get('strPantallaSelec') ? $objRequest->get('strPantallaSelec') : '';
            $strFechaInicio = $objRequest->get('strFechaInicio') ? $objRequest->get('strFechaInicio') : '';
            $strHoraInicio = $objRequest->get('strHoraInicio') ? $objRequest->get('strHoraInicio') : '';
            $strEstadoNoti = $objRequest->get('strEstadoNoti') ? $objRequest->get('strEstadoNoti') : '';
            $strEsSegmentado = $objRequest->get('esSegmentado')? $objRequest->get('esSegmentado') : '';
            $strArchivoCsvB64 = $objRequest->get('strArchivoCsvB64') ? $objRequest->get('strArchivoCsvB64') : '';
            $strNombreArchivo = $objRequest->get('strNombreArchivo') ? $objRequest->get('strNombreArchivo') : '';

            //Parametros Asignación de propiedades 
            $arrayParametros['strUsrCreacion'] = $strUsrCreacion;
            $arrayParametros['strIpCreacion'] = $strIpCreacion;
            $arrayParametros['status'] = 'Activo';


            $arrayCampaniasResponse = $serviceCampaing->consultarPropiedadesMs($arrayParametros);

            $arrayConfig = []; 
            if (!empty($arrayCampaniasResponse)) 
            {
                foreach ($arrayCampaniasResponse['objData'] as $arrayDatos) 
                {
                    if ($arrayDatos['nameProperty'] == 'Titulo') 
                    {
                        if($strTitulo != '')
                        {
                            $arrayConfig[$intContador] = array (
                                'idProperty' => $arrayDatos['idProperty'],
                                'value' => $strTitulo
                            );
                        }
                    } else if ($arrayDatos['nameProperty'] == 'Texto') 
                    {
                        if($strTexto != '')
                        {
                            $arrayConfig[$intContador] = array (
                                'idProperty' => $arrayDatos['idProperty'],
                                'value' => $strTexto
                            );
                        }
                    } else if ($arrayDatos['nameProperty'] == 'FechaInicio') 
                    {
                        if( $strFechaInicio != '' && $strHoraInicio != '')
                        {
                            $strDate = $strFechaInicio . " " . $strHoraInicio;
                            $arrayConfig[$intContador] = array (
                                'idProperty' => $arrayDatos['idProperty'],
                                'value' => $strDate
                            );
                        }
                    } else if ($arrayDatos['nameProperty'] == 'Imagen') 
                    {
                        if($strImg != '')
                        {
                            $arrayConfig[$intContador] = array (
                                'idProperty' => $arrayDatos['idProperty'],
                                'value' => $strImg
                             );
                        }
                    } else if ($arrayDatos['nameProperty'] == 'EsSegmentado') 
                    {
                        if($strEsSegmentado != '')
                        {
                            $arrayConfig[$intContador] = array (
                                'idProperty' => $arrayDatos['idProperty'],
                                'value' => $strEsSegmentado
                            );
                        }
                    } else if ($arrayDatos['nameProperty'] == 'Redirige') 
                    {
                        if($strPantallaSelec != '')
                        {
                            $arrayConfig[$intContador] = array (
                                'idProperty' => $arrayDatos['idProperty'],
                                'value' => $strPantallaSelec
                            );
                        }
                    } else if ($arrayDatos['nameProperty'] == 'Archivo') 
                    {
                        if($strArchivoCsvB64 != '' && $strNombreArchivo != '')
                        {
                            $arrayConfig[$intContador] = array (
                                'idProperty' => $arrayDatos['idProperty'],
                                'value' => $strArchivoCsvB64,
                                'detail' => $strNombreArchivo
                            );
                        }
                    } else if ($arrayDatos['nameProperty'] == 'Vencimiento') 
                    {
                        if($intHraVencimiento != '')
                        {
                            $arrayConfig[$intContador] = array (
                                'idProperty' => $arrayDatos['idProperty'],
                                'value' => $intHraVencimiento
                            );
                        }
                    } else if ($arrayDatos['nameProperty'] == 'NombreArchivo' && $strNombreArchivo != '') 
                    {
                        $arrayConfig[$intContador] = array (
                            'idProperty' => $arrayDatos['idProperty'],
                            'value' => $strNombreArchivo
                        );
                    }else if ($arrayDatos['nameProperty'] == 'FechaVencimiento' && $intHraVencimiento != '') 
                    {
                        $arrayConfig[$intContador] = array (
                            'idProperty' => $arrayDatos['idProperty'],
                            'value' =>  $strDate,
                            'detail' => $intHraVencimiento
                        );
                    }
                    $intContador++; 
                }
               
                $arrayCrearParametros = array("nameCampaing"  => $strNombre,
                                            "status"          => $strEstadoNoti,
                                            "propertyVal"     => $arrayConfig,
                                            "strUsrCreacion"  => $strUsrCreacion,
                                            "intIdEmpresa"    => $objPrefEmpresa,
                                            "strIpCreacion"   => $strIpCreacion
                );
               
                $arrayCampaniasResponse = $serviceCampaing->crearCampaniaNotiPushMs($arrayCrearParametros);
            
                if($arrayCampaniasResponse['strStatus'] == "OK")
                {
                    $strResponseMsj = 'Acción ejecutada correctamente.';
                    $strStatus = $arrayCampaniasResponse['strStatus'];

                }else
                {
                    $strResponseMsj = $arrayCampaniasResponse['strMensaje'];
                    $strStatus = $arrayCampaniasResponse['strStatus'];
                }
            }else
            {
                $strResponseMsj = 'No se encontraron propiedades para creación de Notificaciones, por favor comuniquese con Sistemas.';
            }

        } catch (\Exception $e) 
        {
            $strResponseMsj = 'Ocurrió un error al ejecutar la acción, por favor comuniquese con Sistemas';
            if ($strCodigoError == '204') 
            {
                $strResponseMsj = $e->getMessage();
            }
            $serviceUtil->insertError('TelcoS+',
                                      'AdmiNotiPushMktController.saveAction',
                                       $e->getMessage(),
                                       $strUsrCreacion,
                                       $strIpCreacion );
        }
        $objResponse = new Response(json_encode(array('strStatus'  => $strStatus,
                                                      'strResponseMsj'=> $strResponseMsj)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_488-4")
     *
     * Documentación para la función 'editAction'.
     *
     * Función que renderiza la página de Editar Notificación.
     * 
     * @return render - Página de Editar Notificación.
     *
     * @author Andrea Orellana <adorellana@telconet.ec>
     * @version 1.0 11-01-2022
     *
     */
    public function editAction()
    {
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        $strUsrCreacion = $objSession->get('user') ? $objSession->get('user') : '';
        $strIpCreacion = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $serviceUtil = $this->get('schema.Util');
        $objDataVal = $objRequest->get('data') ? $objRequest->get('data') : '';
        $arrayProyecto = [];
        try 
        {
            if(empty($objDataVal))
            {
                throw new \Exception("El identificador es un parámetro obligatorio.");
            }
           
            foreach($objDataVal['configuraciones'] as $objConfig)
            {
                if($objConfig['propertyCampain']['nameProperty'] ==	"Titulo")
                {
                    $strTitulo = $objConfig['value'];
                }
                if($objConfig['propertyCampain']['nameProperty'] == "Texto")
                {
                    $strTexto = $objConfig['value'];
                }
                if($objConfig['propertyCampain']['nameProperty'] == "FechaInicio")
                {
                    $strFechaInicio = $objConfig['value'];
                    $arrayFechaHora = explode(" ", $strFechaInicio);
                    $strFecha = $arrayFechaHora[0];
                    $strHora = $arrayFechaHora[1];
                }
                
                if($objConfig['propertyCampain']['nameProperty'] == "Imagen")
                {
                    $strUrlImagen = $objConfig['value'];
                }
                if($objConfig['propertyCampain']['nameProperty'] == "EsSegmentado")
                {
                    $strEsSegmentado = $objConfig['value'];
                }
                if($objConfig['propertyCampain']['nameProperty'] == "Redirige")
                {
                    $strRedirige = $objConfig['value'];
                }
                if($objConfig['propertyCampain']['nameProperty'] == "Vencimiento")
                {
                    $intVencimiento = $objConfig['value'];
                }
                if($objConfig['propertyCampain']['nameProperty'] == "NombreArchivo")
                {
                    $strNombreArchivo = $objConfig['value'];
                }

            }
            $arrayProyecto = [
                'idCampania' => $objDataVal['idCampania'],
                'strTituloCampania' => $strTitulo,
                'strTextoCampania' =>$strTexto,
                'strNombreCampania' => $objDataVal['strNombreCampania'],
                'strUrlImg' => $strUrlImagen,
                'intVencimiento' => $intVencimiento,
                'idPantalla' => $strRedirige,
                'strDescripcion' => $strRedirige,
                'strEstado' => $objDataVal['strEstado'],
                'strFechaIni' => $strFecha,
                'strHoraIni' => $strHora,
                'envioSegmentado' => $strEsSegmentado,
                'nombreArchivoCsv' => $strNombreArchivo
            ];
        } catch (\Exception $e) 
        {
            $serviceUtil->insertError(
                'TelcoS+',
                'AdmiNotiPushMktController.editAction',
                $e->getMessage(),
                $strUsrCreacion,
                $strIpCreacion
            );
        }
        return $this->render(
            'administracionBundle:AdmiNotiPushMkt:edit.html.twig',
            ['arrayCampaniaDet' => $arrayProyecto]
        );
    }

    /**
    * @Secure(roles="ROLE_488-4")
    
    * Documentación para la función 'updateAction'.
    *
    * Función que actualiza notificación.
    *
    * @return Response - Mensaje de exito.
    *
    * @author Andrea Orellana <adorellana@telconet.ec>
    * @version 1.0 16-01-2023
    *
    */
    public function updateAction()
    {

        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        $strUsrCreacion = $objSession->get('user') ? $objSession->get('user') : '';
        $strIpCreacion = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $objPrefEmpresa = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : '';
        $serviceUtil = $this->get('schema.Util');
        $strCodigoError = '';
        $serviceCampaing = $this->get('administracion.CampaingNotiPush');
        $arrayParametros = [];
        $arrayEditParametros = [];
        $intContador = 0;
        try
        {
   
            //Parametros
            $strTitulo = $objRequest->get('strTituloNoti') ? $objRequest->get('strTituloNoti') : '';
            $strTexto = $objRequest->get('strTextoNoti') ? $objRequest->get('strTextoNoti') : '';
            $strImg = $objRequest->get('strImgNoti') ? $objRequest->get('strImgNoti') : '';
            $strNombre = $objRequest->get('strNombreNoti') ? $objRequest->get('strNombreNoti') : '';
            $intHraVencimiento = $objRequest->get('intHraVencimiento') ? $objRequest->get('intHraVencimiento') : '';
            $strPantallaSelec = $objRequest->get('strPantallaSelec') ? $objRequest->get('strPantallaSelec') : '';
            $strFechaInicio = $objRequest->get('strFechaInicio') ? $objRequest->get('strFechaInicio') : '';
            $strHoraInicio = $objRequest->get('strHoraInicio') ? $objRequest->get('strHoraInicio') : '';
            $strEstadoNoti = $objRequest->get('strEstadoNoti') ? $objRequest->get('strEstadoNoti') : '';
            $strEsSegmentado = $objRequest->get('esSegmentado')? $objRequest->get('esSegmentado') : '';
            $strArchivoCsvB64 = $objRequest->get('strArchivoCsvB64') ? $objRequest->get('strArchivoCsvB64') : '';
            $strNombreArchivo = $objRequest->get('strNombreArchivo') ? $objRequest->get('strNombreArchivo') : '';
            $intIdCampania = $objRequest->get('intIdCampania');

            //Parametros Asignación de propiedades 
            $arrayParametros['strUsrCreacion'] = $strUsrCreacion;
            $arrayParametros['strIpCreacion'] = $strIpCreacion;
            $arrayParametros['status'] = 'Activo';

            $arrayCampaniasResponse = $serviceCampaing->consultarPropiedadesMs($arrayParametros);

            $arrayConfig = []; 
            if (!empty($arrayCampaniasResponse)) 
            {
                foreach ($arrayCampaniasResponse['objData'] as $arrayDatos) 
                {
                    if ($arrayDatos['nameProperty'] == 'Titulo') 
                    {
                        if($strTitulo != '')
                        {
                            $arrayConfig[$intContador] = array (
                                'idProperty' => $arrayDatos['idProperty'],
                                'value' => $strTitulo
                            );
                        }
                    } else if ($arrayDatos['nameProperty'] == 'Texto') 
                    {
                        if($strTexto != '')
                        {
                            $arrayConfig[$intContador] = array (
                                'idProperty' => $arrayDatos['idProperty'],
                                'value' => $strTexto
                            );
                        }
                    } else if ($arrayDatos['nameProperty'] == 'FechaInicio') 
                    {
                        if( $strFechaInicio != '' && $strHoraInicio != '')
                        {
                            $strDate = $strFechaInicio . " " . $strHoraInicio;
                            $arrayConfig[$intContador] = array (
                                'idProperty' => $arrayDatos['idProperty'],
                                'value' => $strDate
                            );
                        }
                    } else if ($arrayDatos['nameProperty'] == 'Imagen') 
                    {
                        if($strImg != '')
                        {
                            $arrayConfig[$intContador] = array (
                                'idProperty' => $arrayDatos['idProperty'],
                                'value' => $strImg
                             );
                        }
                    } else if ($arrayDatos['nameProperty'] == 'EsSegmentado') 
                    {
                        if($strEsSegmentado != '')
                        {
                            $arrayConfig[$intContador] = array (
                                'idProperty' => $arrayDatos['idProperty'],
                                'value' => $strEsSegmentado
                            );
                        }
                    } else if ($arrayDatos['nameProperty'] == 'Redirige')
                     {
                        if($strPantallaSelec != '')
                        {
                            $arrayConfig[$intContador] = array (
                                'idProperty' => $arrayDatos['idProperty'],
                                'value' => $strPantallaSelec
                            );
                        }
                    } else if ($arrayDatos['nameProperty'] == 'Archivo') 
                    {
                        if($strArchivoCsvB64 != '' && $strNombreArchivo != '')
                        {
                            $arrayConfig[$intContador] = array (
                                'idProperty' => $arrayDatos['idProperty'],
                                'value' => $strArchivoCsvB64,
                                'detail' => $strNombreArchivo
                            );
                        }
                    } else if ($arrayDatos['nameProperty'] == 'Vencimiento')
                     {
                        if($intHraVencimiento != '')
                        {
                            $arrayConfig[$intContador] = array (
                                'idProperty' => $arrayDatos['idProperty'],
                                'value' => $intHraVencimiento
                            );
                        }
                    } else if ($arrayDatos['nameProperty'] == 'NombreArchivo' && $strNombreArchivo != '') 
                    {
                        $arrayConfig[$intContador] = array (
                            'idProperty' => $arrayDatos['idProperty'],
                            'value' => $strNombreArchivo
                        );
                    }else if ($arrayDatos['nameProperty'] == 'FechaVencimiento' && $intHraVencimiento != '') 
                    {
                        $arrayConfig[$intContador] = array (
                            'idProperty' => $arrayDatos['idProperty'],
                            'value' =>  $strDate,
                            'detail' => $intHraVencimiento
                        );
                    }
                    $intContador++; 
                }
               
                $arrayEditParametros = [ 
                    "idCampaing" => $intIdCampania,
                    "nameCampaing" => $strNombre,
                    "status" => $strEstadoNoti,
                    "propertyVal" => $arrayConfig,
                    "strUsrCreacion" => $strUsrCreacion,
                    "strPrefijoEmpresa" => $objPrefEmpresa,
                    "strIpCreacion" => $strIpCreacion
                ];
                
                $arrayCampaniasResponse = $serviceCampaing->editarCampaniaNotiPushMs($arrayEditParametros);
            
                if($arrayCampaniasResponse['strStatus'] == "OK")
                {
                    $strResponseMsj = 'Acción ejecutada correctamente.';
                    $strStatus = $arrayCampaniasResponse['strStatus'];
                }else
                {
                    $strResponseMsj = $arrayCampaniasResponse['strMensaje'];
                    $strStatus = $arrayCampaniasResponse['strStatus'];
                }
            }else
            {
                $strResponseMsj = 'No se encontraron propiedades para creación de Notificaciones, por favor comuniquese con Sistemas.';
            }

        } catch (\Exception $e) 
        {
           
            $strResponseMsj = 'Ocurrió un error al ejecutar la acción, por favor comuniquese con Sistemas';
            if ($strCodigoError == '204')
            {
                $strResponseMsj = $e->getMessage();
            }
            $serviceUtil->insertError('TelcoS+',
                                      'AdmiNotiPushMktController.updateAction',
                                       $e->getMessage(),
                                       $strUsrCreacion,
                                       $strIpCreacion );
        }

        $objResponse = new Response(json_encode(array('strStatus'  => $strStatus,
                                                      'strResponseMsj'=> $strResponseMsj)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
       
    }

    /**
     *
     * Documentación para la función 'getPantallasAppAction'.
     *
     * Función que obtiene lista de pantallas en el App móvil al que se redigirá la notificacion.
     *
     * @return render - Página de Nueva Pantalla
     *
     * @author Andrea Orellana<adorellana@telconet.ec>
     * @version 1.0 06-01-2023
     *
     */

    public function getPantallasAppAction()
    {
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        $strUsrCreacion = $objSession->get('user') ? $objSession->get('user') : '';
        $strIpCreacion = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $intIdEmpresa = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : '';
        $serviceUtil = $this->get('schema.Util');
        $serviceCampaing = $this->get('administracion.CampaingNotiPush');
        $arrayParametros = [];
        $intContador = 0;
        try 
        {

            $arrayParametros = [
                "user" => $strUsrCreacion,
                "idEmpresa" => $intIdEmpresa,
                "nombreParamCab" => "NETLIFEACCESS",
                "estado" => "Activo"
            ];
            $arrayPantallasRes = $serviceCampaing->consultarPantallasAppMs($arrayParametros);

            if ($arrayPantallasRes)
            {
                foreach ($arrayPantallasRes['objData'] as $arrayDatos)
                {
                    $arrayRedirige[$intContador] = array(
                        'idPantalla' => $arrayDatos['idParameterDet'],
                        'descripcion' => $arrayDatos['value1'],
                    );
                    $intContador++;
                }
            }

        } catch (\Exception $e) 
        {
            $serviceUtil->insertError(
                'TelcoS+',
                'AdmiNotiPushMktController.getPantallasAppAction',
                $e->getMessage(),
                $strUsrCreacion,
                $strIpCreacion
            );
        }
        try 
        {
            $objResponse = new Response(json_encode(['arrayPantallas' => $arrayRedirige]));
            $objResponse->headers->set('Content-type', 'text/json');
        } catch (Exception $e) 
        {
            $serviceUtil->insertError(
                'TelcoS+',
                'AdmiNotiPushMktController.getPantallasAppAction',
                $e->getMessage(),
                $strUsrCreacion,
                $strIpCreacion
            );
        }
        return $objResponse;
    }

    public function deleteAjaxAction()
    {
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        $emGeneral = $this->getDoctrine()->getManager("telconet_general");
        $strUsrCreacion = $objSession->get('user') ? $objSession->get('user') : '';
        $strIpCreacion = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $intIdEmpresa = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : '';
        $serviceUtil = $this->get('schema.Util');
        $arrayIdsCampanias = $objRequest->get('arrayIdsCampanias') ? $objRequest->get('arrayIdsCampanias') : '';
        $serviceCampaing = $this->get('administracion.CampaingNotiPush');
        $arrayParametros = [];

        //se consulta Parametro para enviar mensaje por pantalla
        $arrayMensaje = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne(
                'MENSAJES_ADMIN_NOTIF_PUSH',
                'ADMINISTRACION',
                '',
                'NOTI_PUSH_MENSAJE_ELIMINAR',
                '',
                '',
                '',
                '',
                '',
                $intIdEmpresa
            );

        if (isset($arrayMensaje) && !empty($arrayMensaje)) 
        {
            $strMensajeProceso = $arrayMensaje["valor1"];
        }

        $arrayParametros = [
            'arrayIdsCampanias' => $arrayIdsCampanias,
            'strEstado' => "Eliminada",
            'strUsrCreacion' => $strUsrCreacion,
            'strCodEmpresa' => $intIdEmpresa,
            'strIpCreacion' => $strIpCreacion
        ];
        try 
        {

            $arrayResponse = $serviceCampaing->deleteCampaniasMs($arrayParametros);

            if ($arrayResponse['strStatus'] == "OK") 
            {
                return new Response($strMensajeProceso);
            }else
            {
                return new Response($arrayResponse['strMensaje']);
            }

        } catch (\Exception $e) 
        {
            $emGeneral->getConnection()->rollback();
            $emGeneral->getConnection()->close();
            $serviceUtil->insertError('TelcoS+', 'AdmiNotiPushMktController.deleteAjaxAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
            $strResponse = "Ocurrió un error al Eliminar la(s) Notificacion(es), por favor consulte con el Administrador.";
        }
        return $strResponse;
    }


    public function ajaxClonarCampaniasAction()
    {
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        $emGeneral = $this->getDoctrine()->getManager("telconet_general");
        $strUsrCreacion = $objSession->get('user') ? $objSession->get('user') : '';
        $strIpCreacion = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $intIdEmpresa = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : '';
        $objPrefEmpresa = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : '';
        $serviceUtil = $this->get('schema.Util');
        $arrayIdsCampanias = $objRequest->get('arrayIdsCampanias') ? $objRequest->get('arrayIdsCampanias') : '';
        $serviceCampaing = $this->get('administracion.CampaingNotiPush');
        $arrayParametros = [];

        //se consulta Parametro para enviar mensaje por pantalla
        $arrayMensaje = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne(
                'MENSAJES_ADMIN_NOTIF_PUSH',
                'ADMINISTRACION',
                '',
                'NOTI_PUSH_MENSAJE_CLONAR',
                '',
                '',
                '',
                '',
                '',
                $intIdEmpresa
            );

        if (isset($arrayMensaje) && !empty($arrayMensaje)) 
        {
            $strMensajeProceso = $arrayMensaje["valor1"];
        }

        $arrayParametros = [
            'arrayIdsCampanias' => $arrayIdsCampanias,
            'strEstado' => "Pendiente",
            'strUsrCreacion' => $strUsrCreacion,
            'strCodEmpresa' => $objPrefEmpresa,
            'strIpCreacion' => $strIpCreacion
        ];
        try 
        {

            $arrayResponse = $serviceCampaing->clonarCampaniasMs($arrayParametros);

            if ($arrayResponse['strStatus'] == "OK") 
            {
                return new Response($strMensajeProceso);
            }else
            {
                return new Response($arrayResponse['strMensaje']);
            }

        } catch (\Exception $e)
        {
            $emGeneral->getConnection()->rollback();
            $emGeneral->getConnection()->close();
            $serviceUtil->insertError('TelcoS+', 'AdmiNotiPushMktController.ajaxClonarCampaniasAction', $e->getMessage(), 
                                       $strUsrCreacion, $strIpCreacion);
            $strResponse = "Ocurrió un error al Eliminar la(s) Notificacion(es), por favor consulte con el Administrador.";
        }
        return $strResponse;
    }

    public function deleteItemAction($intIdCampania)
    {
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        $emGeneral = $this->getDoctrine()->getManager("telconet_general");
        $strUsrCreacion = $objSession->get('user') ? $objSession->get('user') : '';
        $strIpCreacion = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $intIdEmpresa = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : '';
        $strPrefijoIdEmpresa = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : '';
        $serviceUtil = $this->get('schema.Util');
        $serviceCampaing = $this->get('administracion.CampaingNotiPush');
        $arrayParametros = [];

        //se consulta Parametro para enviar mensaje por pantalla
        $arrayMensaje = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne(
                'MENSAJES_ADMIN_NOTIF_PUSH',
                'ADMINISTRACION',
                '',
                'NOTI_PUSH_MENSAJE_ELIMINAR',
                '',
                '',
                '',
                '',
                '',
                $intIdEmpresa
            );

        if (isset($arrayMensaje) && !empty($arrayMensaje))
        {
            $strMensajeProceso = $arrayMensaje["valor1"];
        }

        $arrayIdsCampanias = array($intIdCampania);

        $arrayParametros = [
            'arrayIdsCampanias' => $arrayIdsCampanias,
            'strEstado' => "Eliminada",
            'strUsrCreacion' => $strUsrCreacion,
            'strCodEmpresa' => $strPrefijoIdEmpresa,
            'strIpCreacion' => $strIpCreacion
        ];
        try 
        {

            $arrayResponse = $serviceCampaing->deleteCampaniasMs($arrayParametros);

            if ($arrayResponse['strStatus'] == "OK") 
            {
                return new Response($strMensajeProceso);
            }else
            {
                return new Response($arrayResponse['strMensaje']);
            }

        } catch (\Exception $e) 
        {
            $emGeneral->getConnection()->rollback();
            $emGeneral->getConnection()->close();
            $serviceUtil->insertError('TelcoS+', 'AdmiNotiPushMktController.deleteItemAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
            $strResponse = "Ocurrió un error al Eliminar la(s) Notificacion(es), por favor consulte con el Administrador.";
        }
        return $strResponse;
    }
   
    /**
     * Documentación para la función 'actualizaEstadoItemAction'.
     *
     * Función que permite actualizar el estado de una campania
     *
     * @author Andrea Orellana <adorellana@telconet.ec>
     * @version 1.0 12-01-2023
     *
     * @author Andrea Orellana <adorellana@telconet.ec>
     * @version 1.0 18-05-2023 se agrega parámetro estadoNuevo que tendrá la campania.
     */
    public function actualizaEstadoItemAction($intIdCampania, $strEstado)
    {
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
      
        $strUsrCreacion = $objSession->get('user') ? $objSession->get('user') : '';
        $strIpCreacion = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $strPrefijoIdEmpresa = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : '';
        $serviceUtil = $this->get('schema.Util');
        $serviceCampaing = $this->get('administracion.CampaingNotiPush');
        $arrayParametros = [];

        $arrayIdsCampanias = array($intIdCampania);

        $arrayParametros = [
            'arrayIdsCampanias' => $arrayIdsCampanias,
            'strEstado' => $strEstado,
            'strUsrCreacion' => $strUsrCreacion,
            'strCodEmpresa' => $strPrefijoIdEmpresa,
            'strIpCreacion' => $strIpCreacion
        ];
        try 
        {

            $arrayResponse = $serviceCampaing->deleteCampaniasMs($arrayParametros);

            if ($arrayResponse['strStatus'] == "OK") 
            {
                return new Response($arrayResponse['strMensaje']);
            }else
            {
                return new Response($arrayResponse['strMensaje']);
            }

        } catch (\Exception $e) 
        {
            $serviceUtil->insertError('TelcoS+', 'AdmiNotiPushMktController.detenerItemAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
            $strResponse = "Ocurrió un error al Eliminar la(s) Notificacion(es), por favor consulte con el Administrador.";
        }
        return $strResponse;
    }

        /**
     * @Secure(roles="ROLE_488-2")
     *
     * Documentación para la función 'newAction'.
     *
     * Función que renderiza la página de Crear Notificación.
     * 
     * @return render - Página de Crear Notificación.
     *
     * @author Andrea Orellana <adorellana@telconet.ec>
     * @version 1.0 12-01-2023
     *
     */
    public function newAction()
    {
        return $this->render('administracionBundle:AdmiNotiPushMkt:new.html.twig', array());
    }

}