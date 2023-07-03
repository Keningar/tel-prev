<?php
namespace telconet\comunicacionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Imagenes controller.
 *
 * Controlador que se encargará de administrar las funcionalidades
 * respecto a la opción de Imagenes
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 27-07-2015
 */
class ImagenesController extends Controller
{
    /**
     * @Secure(roles="ROLE_292-1")
     *
     * Documentación para el método 'indexAction'.
     *
     * Muestra el panel inicial con los criterios de búsqueda disponibles
     * a los usuarios para realizar la consulta de las imágenes.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 27-07-2015
     */
    public function indexAction()
    {
        return $this->render( 'comunicacionesBundle:Imagenes:index.html.twig' );
    }
    
    
    /**
     * @Secure(roles="ROLE_292-2757")
     *
     * Documentación para el método 'ajaxBuscarImagenesAction'.
     *
     * Realizará la búsqueda de las imágenes que correspondan a los
     * criterios ingresados por los usuarios.
     * 
     * @param string $objRequest Criterios ingresados por el usuario.
     *
     * @return JsonResponse $response
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 27-07-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 19-07-2017 Se verifica la url de la imagen para que independiente desde donde haya sido subida se muestre en el visor de imágenes
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 09-11-2017 Se agrega la lógica para permitir la auditoría de elementos
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 10-11-2017 Se agrega el estado de la evaluación como parámetros para filtrar los elementos
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 16-11-2017 Se realizan modificaciones de la información que debe aparecer en el visor de imágenes y se habilita
     *                         la opción de evaluar cuando la imagen está asociada a un caso
     * 
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.5 24-06-2019 Se agregan parametros de Longitud y Latitud en array de respuesta.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 16-07-2019 Se verifica si el usuario en sesión tiene el perfil 'Consultar Imagenes Sin Filtro Por Region', 
     *                          para quitar el filtro por región al consultar las imágenes y se elimina código repetido
     * 
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.7 07-11-2019 Se agrega lógica para validar fotos antes y despues.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.8 11-11-2020 Los archivos que se encuentren en el servidor NFS remoto deben poder ser visualizados en telcos.
     * 
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.9 02-03-2022 Para el módulo de soporte, se actualiza el proceso de búsqueda de las imágenes, con el objetivo
     * de mejorar el tiempo de respuesta.
     * 
     */
    public function ajaxBuscarImagenesAction(Request $objRequest)
    {
        $response               = new JsonResponse();
        $arrayParametros        = array();
        $arrayResultados        = array();
        $arrayImagenes          = array();
        $idEmpresa              = 0;
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComunicacion         = $this->getDoctrine()->getManager('telconet_comunicacion');
        $emTelconet             = $this->getDoctrine()->getManager("telconet");
        $serviceUtil            = $this->get('schema.Util');
        $strControlador         = 'ImagenesController.ajaxBuscarImagenesAction';

        $objSession             = $objRequest->getSession();
        $strUserSession         = $objSession->get('user');
        $strIpClient            = $objRequest->getClientIp();

        
        $strCodEmpresaSession   = $objSession->get('idEmpresa');
        $strRegionSession       = '';
        $intIdOficinaSession    = $objSession->get('idOficina') ? $objSession->get('idOficina') : 0;
        $objOficinaSession      = $emTelconet->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficinaSession);
        if(is_object($objOficinaSession))
        {
            $objCantonSession   = $emGeneral->getRepository("schemaBundle:AdmiCanton")->find($objOficinaSession->getCantonId());
            if(is_object($objCantonSession))
            {
                $strRegionSession = $objCantonSession->getRegion();
            }
        }
        
        $strEstadoEvaluacion    = $objRequest->query->get('strEstadoEvaluacion');
        $strFechaDesde          = $objRequest->query->get('strFechaDesde');
        $arrayFechaDesde        = explode('T', $strFechaDesde);
        $strFechaHasta          = $objRequest->query->get('strFechaHasta');
        $arrayFechaHasta        = explode('T', $strFechaHasta);
        $intElemento            = $objRequest->query->get('intElemento');
        $intIdentificacion      = $objRequest->query->get('intIdentificacion');
        $intModeloElemento      = $objRequest->query->get('intModeloElemento');
        $intTipoElemento        = $objRequest->query->get('intTipoElemento');
        $strNombres             = $objRequest->query->get('strNombres');
        $strApellidos           = $objRequest->query->get('strApellidos');
        $strLogin               = $objRequest->query->get('strLogin');
        $strRazonSocial         = $objRequest->query->get('strRazonSocial');
        $strTipoSoporte         = $objRequest->query->get('strTipoSoporte');
        $intNumeroSoporte       = $objRequest->query->get('intNumeroSoporte');
        $strFechaDesdeSoporte   = $objRequest->query->get('strFechaDesdeSoporte');
        $arrayFechaDesdeSoporte = explode('T', $strFechaDesdeSoporte);
        $strFechaHastaSoporte   = $objRequest->query->get('strFechaHastaSoporte');
        $arrayFechaHastaSoporte = explode('T', $strFechaHastaSoporte);
        $strEmpresa             = $objRequest->query->get('strEmpresa');
        $intDepartamento        = $objRequest->query->get('intDepartamento');
        $intLimite              = $objRequest->query->get("limit");
        $intPagina              = $objRequest->query->get("page");
        $intInicio              = $objRequest->query->get("start");
        
        $strParamPathTelcos     = $this->container->getParameter('path_telcos');
        $strPathTelcos          = $strParamPathTelcos."telcos/web";
        
        if( $strEmpresa != "" )
        {
            $objEmpresa = $emTelconet->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($strEmpresa);
            $idEmpresa  = $objEmpresa->getId();
        }
        
        $arrayParametros['limite']              = $intLimite;
        $arrayParametros['pagina']              = $intPagina;
        $arrayParametros['inicio']              = $intInicio;
        $arrayParametros['fechaDesde']          = $arrayFechaDesde[0];
        $arrayParametros['fechaHasta']          = $arrayFechaHasta[0];
        $arrayParametros['elemento']            = $intElemento;
        $arrayParametros['modeloElemento']      = $intModeloElemento;
        $arrayParametros['tipoElemento']        = $intTipoElemento;
        $arrayParametros['identificacion']      = $intIdentificacion;
        $arrayParametros['nombres']             = $strNombres;
        $arrayParametros['apellidos']           = $strApellidos;
        $arrayParametros['login']               = $strLogin;
        $arrayParametros['razonSocial']         = $strRazonSocial;
        $arrayParametros['tipoSoporte']         = $strTipoSoporte;
        $arrayParametros['numeroSoporte']       = $intNumeroSoporte;
        $arrayParametros['fechaDesdeSoporte']   = $arrayFechaDesdeSoporte[0];
        $arrayParametros['fechaHastaSoporte']   = $arrayFechaHastaSoporte[0];
        $arrayParametros['empresa']             = $idEmpresa;
        $arrayParametros['departamento']        = $intDepartamento;
        $arrayParametros['strRegionSession']    = $strRegionSession;
        $arrayParametros['strCodEmpresaSession']= $strCodEmpresaSession;
        $arrayParametros['strEstadoEvaluacion'] = $strEstadoEvaluacion;
                
        if(true === $this->get('security.context')->isGranted('ROLE_292-6557'))
        {
            $arrayParametros['strRegionSession'] = '';
        }
        
        if( $arrayParametros['tipoSoporte'] )
        {
            $arrayParametros['role292_5557'] = $this->get('security.context')->isGranted('ROLE_292-5557');
            $arrayParametros['usuarioSesion'] = $strUserSession;
            $arrayParametros['objContainer'] = $this->container;
            $objImagenes = $emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                                    ->getImagenesPorCriterios($arrayParametros);
            $response->setData($objImagenes);                                        
        } 
        else 
        {
            $arrayImagenes  = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->getImagenesByCriterios( $arrayParametros );
        
            if( $arrayImagenes['total'] > 0 )
            {
                $arrayTiposElementos            = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
                                                                    ->findBy(array('estado' => "Activo", 'esDe' => "BACKBONE"), 
                                                                            array('nombreTipoElemento' => 'ASC'));
                
                
                $arrayValoresEvaluacionTrabajo  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->get(
                                                                                                                    'VALORES_EVALUACION_TRABAJO', 
                                                                                                                    '', 
                                                                                                                    '', 
                                                                                                                    '', 
                                                                                                                    '', 
                                                                                                                    '',
                                                                                                                    '', 
                                                                                                                    '',
                                                                                                                    '',
                                                                                                                    '',
                                                                                                                    'valor3'
                                                                                                                );

                $arrayCronologiaFotos = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne('CRONOLOGIA_FOTOS_OBLIGATORIAS', 
                '', 
                '', 
                '', 
                '', 
                '', 
                '', 
                ''
                );
                if (is_array($arrayCronologiaFotos))
                {
                    $strCronologiaFotoAntes     = !empty($arrayCronologiaFotos['valor1']) ? $arrayCronologiaFotos['valor1'] : "ANTES";
                    $strCronologiaFotoDespues   = !empty($arrayCronologiaFotos['valor2']) ? $arrayCronologiaFotos['valor2'] : "DESPUES";
                }                                                                                                  

                foreach($arrayImagenes['registros'] as $arrayItemImagen)
                {
                    $strNombrePersona = ucwords(strtolower($arrayItemImagen['nombres'].' '.$arrayItemImagen['apellidos']));
                    
                    $arrayItemResultado = array();
                    $arrayItemResultado['intIdImagen']         = $arrayItemImagen['id'];
                    $arrayItemResultado['strPersonaNombre']    = $strNombrePersona;
                    $arrayItemResultado['strNombreImagen']     = $arrayItemImagen['nombreDocumento']."_".$arrayItemImagen['idDocumentoRelacion'];

                    $strContenidoAdicImagen = "";
                    if(isset($arrayItemImagen['numeroCaso']) && !empty($arrayItemImagen['numeroCaso']))
                    {
                        $strContenidoAdicImagen .= "<div class='fila'>
                                                        <div class='label'> Número Caso:</div>
                                                        <div class='descripcion'>".$arrayItemImagen['numeroCaso']."</div>
                                                    </div>";
                    }

                    if( isset($arrayItemImagen['numeroTarea']) && !empty($arrayItemImagen['numeroTarea']))
                    {
                        $strContenidoAdicImagen .= "<div class='fila'>
                                                    <div class='label'> Número Tarea:</div>
                                                    <div class='descripcion'>".$arrayItemImagen['numeroTarea']."</div>
                                                </div>";
                    }
                    $arrayItemResultado['strContenidoAdicImagen']   = $strContenidoAdicImagen;
                    
                    
                    $strInfoEvaluacionImg   = "";
                    if(isset($arrayItemImagen['idElemento']) && !empty($arrayItemImagen['idElemento'])
                        || (isset($arrayItemImagen['numeroCaso']) && !empty($arrayItemImagen['numeroCaso']))
                        || ((isset($arrayItemImagen['numeroTarea']) && !empty($arrayItemImagen['numeroTarea']))
                                && (isset($arrayItemImagen['estadoEvaluacion']) && !empty($arrayItemImagen['estadoEvaluacion']))))
                    {
                        $strEstadoEvaluacion    = "Pendiente";
                        $intIdTipoElemento      = $arrayItemImagen['idTipoElemento'] ? $arrayItemImagen['idTipoElemento'] : 0;
                        if(isset($arrayItemImagen['estadoEvaluacion']) && !empty($arrayItemImagen['estadoEvaluacion']))
                        {
                            $strEstadoEvaluacion = $arrayItemImagen['estadoEvaluacion'];
                        }
                        $arrayItemResultado['strEstadoEvaluacion']  = $strEstadoEvaluacion;

                        $strInfoEvaluacionImg .= "<div class='fila'>
                                                    <div class='label'>Estado de Auditoría:</div>
                                                    <div class='descripcion' id='estadoEvaluacion'>".$strEstadoEvaluacion."</div>
                                                </div>";

                        if($strEstadoEvaluacion === 'Auditada')
                        {
                            if(isset($arrayItemImagen['tipoElementoId']) && !empty($arrayItemImagen['tipoElementoId']))
                            {
                                $objTipoElementoEvaluacion  = $emComunicacion->getRepository('schemaBundle:AdmiTipoElemento')
                                                                            ->find($arrayItemImagen['tipoElementoId']);
                                if(is_object($objTipoElementoEvaluacion))
                                {
                                    $strInfoEvaluacionImg .= "<div class='fila'>
                                                                <div class='label'>Tipo Elemento:</div>
                                                                <div class='descripcion'>".$objTipoElementoEvaluacion->getNombreTipoElemento()."</div>
                                                            </div>";
                                    
                                }
                            }

                            if(isset($arrayItemImagen['porcentajeEvaluado']))
                            {
                                $strInfoEvaluacionImg .= "<div class='fila'>
                                                            <div class='label'>Porcentaje Base:</div>
                                                            <div class='descripcion' id='porcentajeBase'>"
                                                            .$arrayItemImagen['porcentajeEvaluacionBase']."%</div>
                                                        </div>";

                                $strInfoEvaluacionImg .= "<div class='fila'>
                                                            <div class='label'>Porcentaje Obtenido:</div>
                                                            <div class='descripcion' id='porcentajeObtenido'>"
                                                            .$arrayItemImagen['porcentajeEvaluado']."%</div>
                                                        </div>";
                            }
                            
                            if(isset($arrayItemImagen['evaluacionTrabajo']) && !empty($arrayItemImagen['evaluacionTrabajo']))
                            {
                                $strInfoEvaluacionImg .= "<div class='fila'>
                                                            <div class='label'>Evaluación Trabajo:</div>
                                                            <div class='descripcion'>".$arrayItemImagen['evaluacionTrabajo']."</div>
                                                        </div>";
                            }
                            
                            if(isset($arrayItemImagen['nombreEvaluador']) && !empty($arrayItemImagen['nombreEvaluador']))
                            {
                                $strInfoEvaluacionImg .= "<div class='fila'>
                                                          <div class='label'>Evaluador:</div>
                                                          <div class='descripcion'>".ucwords(strtolower($arrayItemImagen['nombreEvaluador']))."</div>
                                                        </div>";
                            }
                        }
                        else if((true === $this->get('security.context')->isGranted('ROLE_292-5557')) 
                                && ($strEstadoEvaluacion === 'Pendiente' 
                                    || ($strEstadoEvaluacion === 'En Proceso' && $arrayItemImagen["usrEvaluacion"] === $strUserSession)))
                        {
                            $strStyleForm = "";
                            if($strEstadoEvaluacion === 'Pendiente')
                            {
                                $strInfoEvaluacionImg   .= "<div align='center'>".
                                                            "<span class='height20px'>".                                               
                                                                "<a id='btnEvaluar' href='javascript:void(0);' 
                                                                onclick='iniciarEvaluacion(".$arrayItemImagen['idDocumentoRelacion'].")' 
                                                                class='button-crud'>Evaluar".
                                                                "</a>".
                                                            "</span>".
                                                        "</div>";
                                $strStyleForm = "style='display:none'";
                            }
                            else
                            {
                                $strStyleForm = "style='display:block'";
                            }


                            $strInfoEvaluacionImg   .= "<form id='formularioEvaluacion_".$arrayItemImagen['idDocumentoRelacion']."' 
                                                        name='formularioEvaluacion_".$arrayItemImagen['idDocumentoRelacion']."' ".$strStyleForm." >";

                            if(strpos($arrayItemImagen['nombreDocumento'], $strCronologiaFotoDespues))
                            {
                                if(isset($arrayItemImagen['porcentajeEvaluado']))
                                {
                                    $strInfoEvaluacionImg .= "<div class='fila'>
                                                                <div class='label'>Porcentaje Base:</div>
                                                                <div class='descripcion' id='porcentajeBase'>"
                                                                .$arrayItemImagen['porcentajeEvaluacionBase']."%</div>
                                                            </div>";

                                    $strInfoEvaluacionImg .= "<div class='fila'>
                                                                <div class='label'>Porcentaje Obtenido:</div>
                                                                <div class='descripcion' id='porcentajeObtenido'>"
                                                                .$arrayItemImagen['porcentajeEvaluado']."%</div>
                                                            </div>";
                                }  
                            }
                            else
                            {
                                $strInfoEvaluacionImg .= "<div class='fila'>
                                <div class='label'>Tipo Elemento:</div>
                                <div class='descripcion'>";

                                if(!empty($arrayTiposElementos))
                                {
                                    $strInfoEvaluacionImg .= "<select name='tipoElementoEvaluacion' required><option value=''>Seleccione...</option>";
                                    foreach ($arrayTiposElementos as $objTipoElemento)
                                    {
                                        $intIdElementoFor = $objTipoElemento->getId();
                                        $strInfoEvaluacionImg  .="<option value='" . $intIdElementoFor . "' " 
                                                                . ($intIdElementoFor === $intIdTipoElemento  ? "selected='selected' " : "" ) ." > "
                                                                . ucwords(strtolower(sprintf("%s",$objTipoElemento->getNombreTipoElemento())))
                                                                . "</option>";
                                    }
                                    $strInfoEvaluacionImg .= "</select>";
                                }
                                $strInfoEvaluacionImg .= "</div></div>";
                            }

                            $strInfoEvaluacionImg .= "<div class='fila'>
                                                        <div class='label'>Evaluación Trabajo:</div>
                                                        <div class='descripcion'>";
                            $strInfoEvaluacionImg .= "<select name='evaluacionTrabajo'><option value=''>Seleccione...</option>";
                            foreach($arrayValoresEvaluacionTrabajo as $arrayValorEvaluacionTrabajo)
                            {
                                $strInfoEvaluacionImg  .="<option value='" . $arrayValorEvaluacionTrabajo['valor1'] . "' > "
                                                        . $arrayValorEvaluacionTrabajo['valor2'];
                            }
                            $strInfoEvaluacionImg .= "</select></div></div><br>";
                            $strInfoEvaluacionImg .= "<div align='center'>".
                                                        "<span class='height20px'>".                                               
                                                            "<a href='javascript:void(0);' 
                                                            onclick='evaluarTrabajo(".$arrayItemImagen['idDocumentoRelacion'].")' 
                                                            class='button-crud'>Guardar Evaluación".
                                                            "</a>".
                                                        "</span>".
                                                    "</div>";
                            $strInfoEvaluacionImg .= "</form>";
                        }
                        else if($strEstadoEvaluacion === 'En Proceso' && $arrayItemImagen["usrEvaluacion"] !== $strUserSession)
                        {
                            if(isset($arrayItemImagen['nombreEvaluador']) && !empty($arrayItemImagen['nombreEvaluador']))
                            {
                                $strInfoEvaluacionImg .= "<div class='fila'>
                                                          <div class='label'>Evaluador:</div>
                                                          <div class='descripcion'>".ucwords(strtolower($arrayItemImagen['nombreEvaluador']))."</div>
                                                        </div>";
                            }
                        }
                        else
                        {
                            $strInfoEvaluacionImg .= "";
                        }
                    }
                    $arrayItemResultado['strInfoEvaluacionImg']     = $strInfoEvaluacionImg;
                    
                    $strUrlVisorImg                                 = "";
                    $mixPosUrlVisorImg                              = strrpos($arrayItemImagen['urlImagen'], $strPathTelcos);
                    if($mixPosUrlVisorImg === false) 
                    {
                        $strUrlVisorImg = '/'.$arrayItemImagen['urlImagen'];
                    }
                    else
                    {
                        $strUrlVisorImg = substr($arrayItemImagen['urlImagen'], strlen($strPathTelcos));
                    }

                    $strNFS  = substr($arrayItemImagen['urlImagen'],0, strrpos($arrayItemImagen['urlImagen'], '/'));
                    $boolNfs = (filter_var($strNFS, FILTER_VALIDATE_URL) !== false);
                    if($boolNfs)
                    {
                        $strUrlVisorImg = $arrayItemImagen['urlImagen'];
                    }
                    $strNombreFotoAntes = "";

                    if(strpos($arrayItemImagen['nombreDocumento'], $strCronologiaFotoDespues))
                    {
                        $strNombreFotoAntes = trim(str_replace($strCronologiaFotoDespues, 
                                                                $strCronologiaFotoAntes,
                                                                $arrayItemImagen['nombreDocumento']));

                        if (!empty($strNombreFotoAntes))
                        {
                            $strNombreFotoAntes = substr($strNombreFotoAntes, 
                                                            0, 
                                                            strpos($strNombreFotoAntes, 
                                                                    "_", 
                                                                    (strpos($strNombreFotoAntes, "_")+1)
                                                                )
                                                        );
                        }
                    }

                    $arrayResultadoFotos = $emComunicacion->getRepository('schemaBundle:InfoDocumento')
                    ->getResultadoFotosAntesDespues(
                        [
                            "nombreDocumento" => $strNombreFotoAntes,
                            "usuario"         => $arrayItemImagen['login']
                        ]
                    );

                    $strUrlImagenAntes = "";

                    if($arrayResultadoFotos['status'] == 'OK')
                    {
                        if($arrayResultadoFotos['total'] > 0)
                        {
                            $strUrlImagenAntes = $arrayResultadoFotos['resultado'][0]['ubicacionFisicaDocumento'] 
                                                ? $arrayResultadoFotos['resultado'][0]['ubicacionFisicaDocumento'] : "";
                            $strNFS  = substr($strUrlImagenAntes,0, strrpos($strUrlImagenAntes, '/'));
                            if(!(filter_var($strNFS, FILTER_VALIDATE_URL)) )
                            {
                                $strUrlImagenAntes = '/'.$strUrlImagenAntes;
                            }
                        }
                    }
                    else
                    {
                        $serviceUtil->insertError(
                                                    'Telcos+',
                                                    $strControlador,
                                                    $arrayResultadoFotos['resultado'],
                                                    $strUserSession,
                                                    $strIpClient
                                                );
                    }


                    $arrayItemResultado['strUrlImagen']        = $strUrlVisorImg;
                    $arrayItemResultado['strUrlImagenAntes']   = $strUrlImagenAntes;
                    $arrayItemResultado['strPersonaLogin']     = $arrayItemImagen['login'];
                    $arrayItemResultado['strFechaCreacion']    = $arrayItemImagen['feCreacion']->format('d M Y');
                    $arrayItemResultado['strLongitud']         = $arrayItemImagen['floatLongitud'];
                    $arrayItemResultado['strLatitud']          = $arrayItemImagen['floatLatitud'];
                    $arrayResultados[] = $arrayItemResultado;
                }
            }
            
            $response->setData(
                                array(
                                        'intTotal'      => $arrayImagenes['total'],
                                        'arrayImagenes' => $arrayResultados
                                    )
                            );
        }
        
        return $response;
    }
    
    /**
     * @Secure(roles="ROLE_292-2758")
     *
     * Documentación para el método 'descargarImagenesAction'.
     *
     * Descarga la imagen seleccionada por el usuario.
     * 
     * @param string $objRequest Dato enviado por el usuario.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 29-07-2015
     */
    public function descargarImagenesAction(Request $objRequest)
    {
        $intIdImagen    = $objRequest->query->get('intIdImagen');
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $entityImagen   = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find( $intIdImagen );
        
        if (!$entityImagen)
        {
            throw $this->createNotFoundException('No se encontro la imagen solicitada.');
        }
        
        $strRutaImagen = $entityImagen->getUbicacionFisicaDocumento();

        $fileImagen     = fopen($strRutaImagen, "rb");
        $contentImagen  = stream_get_contents($fileImagen);
        fclose($fileImagen);
        
        $response = new Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.basename($strRutaImagen).'";');
        $response->setContent($contentImagen);
        
        return $response;
    }
    
    
    /**
     * @Secure(roles="ROLE_292-5557")
     *
     * Documentación para el método 'iniciarEvaluacionImagenesAction'.
     *
     * Actualiza la información del estado y la fecha de inicio de la evaluación relacionada a la imagen
     * 
     * @return json con resultado del proceso
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-11-2017
     */
    public function iniciarEvaluacionImagenesAction()
    {
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $strUserSession = $objSession->get('user');
        $strMensaje     = "";
        $strStatus      = "ERROR";
        $arrayEstados   = array("En Proceso" => 'iniciada',
                                "Auditada"   => 'auditada');
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $objResponse    = new JsonResponse();
        $objResponse->headers->set('Content-Type', 'text/html');
        $intIdDocumentoRelacion = $objRequest->get('intIdDocumentoRelacion');
        $emComunicacion->getConnection()->beginTransaction();
        try
        {
            if(isset($intIdDocumentoRelacion) && !empty($intIdDocumentoRelacion))
            {
                $objDocRelacion = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')->find($intIdDocumentoRelacion);
                if(is_object($objDocRelacion))
                {
                    $strEstadoDocRelacionUltimo = $objDocRelacion->getEstadoEvaluacion();                    
                    if($strEstadoDocRelacionUltimo === 'En Proceso' || $strEstadoDocRelacionUltimo === 'Auditada')
                    {
                        $strMensaje         = "La evaluación ya ha sido ".$arrayEstados[$strEstadoDocRelacionUltimo];
                        $strUsrEvaluacion   = $objDocRelacion->getUsrEvaluacion();
                        if(!empty($strUsrEvaluacion))
                        {
                            $objEvaluador       = $emComercial->getRepository('schemaBundle:InfoPersona')->findOneByLogin($strUsrEvaluacion);
                            if(is_object($objEvaluador))
                            {
                                $strMensaje .= " por ". $objEvaluador->getNombres()." ".$objEvaluador->getApellidos();
                            }
                        }
                    }
                    else
                    {
                        $objDocRelacion->setEstadoEvaluacion('En Proceso');
                        $objDocRelacion->setFeInicioEvaluacion(new \DateTime('now'));
                        $objDocRelacion->setUsrEvaluacion($strUserSession);
                        $emComunicacion->persist($objDocRelacion);
                        $emComunicacion->flush();
                        $emComunicacion->commit();
                        $strStatus  = "OK";
                        $strMensaje = "Se inició la evaluación correctamente";
                    }
                }
            }
            else
            {
                $strMensaje = "No se ha enviado la información requerida para guardar la evaluación";
            }
        } 
        catch (\Exception $e) 
        {
            if($emComunicacion->getConnection()->isTransactionActive())
            {
                $emComunicacion->rollback();
                $emComunicacion->close();
            }
            error_log($e->getMessage());
            $strMensaje .= "Ha ocurrido un problema. <br/>Por favor informe a Sistemas.";
        }
        
        $objResponse->setData(array('strStatus' => $strStatus, 'strMensaje' => $strMensaje));
        return $objResponse;
    }
    
    
    /**
     * @Secure(roles="ROLE_292-5557")
     *
     * Documentación para el método 'guardarEvaluacionImagenesAction'.
     *
     * Guarda la información ingresada de la evaluación relacionada a la imagen
     * 
     * @return json con resultado del proceso
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-11-2017
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.1 09-11-2019 - Se agrega lógica para creación de tarea automatica sobre elementos fiscalizados.
     * 
     */
    public function guardarEvaluacionImagenesAction()
    {
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strUserSession         = $objSession->get('user');
        $strIpClient            = $objRequest->getClientIp();
        $strCodEmpresaSession   = $objSession->get('idEmpresa');
        $strMensaje             = "";
        $strStatus              = "ERROR";
        $emComunicacion         = $this->getDoctrine()->getManager('telconet_comunicacion');
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $emSoporte              = $this->getDoctrine()->getManager('telconet_soporte');
        $serviceSoporte         = $this->get('soporte.SoporteService');	
        $objResponse            = new JsonResponse();
        $objResponse->headers->set('Content-Type', 'text/html');
        $intIdDocumentoRelacion = $objRequest->get('intIdDocumentoRelacion');
        $intIdTipoElemento      = $objRequest->get('intIdTipoElemento');
        $strEvaluacionTrabajo   = $objRequest->get('strEvaluacionTrabajo');
        $serviceUtil            = $this->get('schema.Util');
        $strControlador         = 'ImagenesController.guardarEvaluacionImagenesAction';
        $strObservaciones       = "";
        $emComunicacion->getConnection()->beginTransaction();
        try
        {
            if((isset($intIdDocumentoRelacion) && !empty($intIdDocumentoRelacion))
               && ((isset($intIdTipoElemento) && !empty($intIdTipoElemento))
                    || (isset($strEvaluacionTrabajo) && !empty($strEvaluacionTrabajo))))
            {
                $objDocRelacion = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')->find($intIdDocumentoRelacion);
                
                if(is_object($objDocRelacion))
                {
                    $arrayEstadoCrearTarea = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('ESTADO_CREACION_TAREA_FOTOS_OBLIGATORIAS', 
                             '', 
                             '', 
                             '', 
                             '', 
                             '', 
                             '', 
                             ''
                            );

                    if (is_array($arrayEstadoCrearTarea))
                    {
                        $strEstadoCrearTarea = !empty($arrayEstadoCrearTarea['valor1']) ? $arrayEstadoCrearTarea['valor1'] : "DAÑO";
                    }

                    if(empty($intIdTipoElemento) && $strEvaluacionTrabajo == $strEstadoCrearTarea)
                    {
                        if($objDocRelacion->getDocumentoId() > 0)
                        {
                            $objInfoDocumento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')
                            ->find($objDocRelacion->getDocumentoId());
                        }

                        $objInfoComunicacion = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                                            ->findOneBy(array('detalleId' => $objDocRelacion->getDetalleId(),
                                                                'claseComunicacion' => 'Recibido'));

                        $arrayIdTareaAutomatica = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('TAREA_AUTOMATICA_FOTOS_OBLIGATORIAS', 
                                                         '', 
                                                         '', 
                                                         '', 
                                                         '', 
                                                         '', 
                                                         '', 
                                                         ''
                                                        );

                        if (is_array($arrayIdTareaAutomatica))
                        {
                            $intIdtarea = !empty($arrayIdTareaAutomatica['valor1']) ? $arrayIdTareaAutomatica['valor1'] : 1346;
                        }

                        if($intIdtarea > 0)
                        {
                            $objTarea       = $emSoporte->find('schemaBundle:AdmiTarea', $intIdtarea);
                        }

                        if(is_object($objInfoComunicacion))
                        {
                        $strObservaciones   = "Tarea Origen: ".$objInfoComunicacion->getId()
                                            .", Login Origen: ".$objInfoComunicacion->getRemitenteNombre();
                        }

                        if(is_object($objTarea) && is_object($objInfoDocumento))
                        {
                            $strObservaciones   = $strObservaciones 
                            .", Elemento Origen: ".$objInfoDocumento->getStrEtiquetaDocumento();

                            $arrayParametros = array(
                                'tarea'                 => $objTarea,
                                'empresaCod'            => $strCodEmpresaSession,
                                'longitud'              => $objInfoDocumento->getLongitud(),
                                'latitud'               => $objInfoDocumento->getLatitud(),
                                'observaciones'         => $strObservaciones,
                                'flagTarea'             => "S",
                                'usrCreacion'           => $strUserSession,
                                'ipCreacion'            => $strIpClient,
                                'feCreacion'            => new \DateTime('now'),
                                'tipoAsignado'          => "EMPLEADO"
                            );
    
                            //crear Tarea
                            $arrayResultado = $serviceSoporte->crearTareaIncidenciaElemento($arrayParametros);
    
                            if( $arrayResultado['status'] != "OK" )
                            {
                                $serviceUtil->insertError('Telcos+',
                                $strControlador,
                                $arrayResultado['mensaje'],
                                $strUserSession,
                                $strIpClient);

                                $strMensaje = "Ocurrió un problema al crear la tarea automática, por favor contactar a Soporte Sistemas";
                                $objResponse->setData(array('strStatus' => $arrayResultado['status'], 'strMensaje' => $strMensaje));
                                return $objResponse;
                            }
                        }
                    }

                    $objDocRelacion->setTipoElementoId($intIdTipoElemento);
                    $objDocRelacion->setEstadoEvaluacion('Auditada');
                    $objDocRelacion->setEvaluacionTrabajo($strEvaluacionTrabajo);
                    $objDocRelacion->setUsrEvaluacion($strUserSession);
                    $emComunicacion->persist($objDocRelacion);
                    $emComunicacion->flush();
                    $emComunicacion->commit();
                    $strStatus  = "OK";
                    $strMensaje = "Se guardó la evaluación correctamente";
                }
            }
            else
            {
                $strMensaje = "No se ha enviado la información requerida para guardar la evaluación";
            }
        } 
        catch (\Exception $e) 
        {
            if($emComunicacion->getConnection()->isTransactionActive())
            {
                $emComunicacion->rollback();
                $emComunicacion->close();
            }
            error_log($e->getMessage());
            $strMensaje .= "Ha ocurrido un problema. <br/>Por favor informe a Sistemas.";
        }
        
        $objResponse->setData(array('strStatus' => $strStatus, 'strMensaje' => $strMensaje));
        return $objResponse;
    }
}
