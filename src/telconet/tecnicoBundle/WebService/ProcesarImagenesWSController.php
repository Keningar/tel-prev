<?php

namespace telconet\tecnicoBundle\WebService;

use telconet\schemaBundle\DependencyInjection\BaseWSController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Finder;

/**
 * Clase que sirve para grabar imagenes en la base de datos.
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 30-07-2015
 */
class ProcesarImagenesWSController extends BaseWSController
{
    /**
     * Funcion que sirve para procesar las opciones que vienen desde el mobil
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 30-07-2015
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 24-11-2017 - Se grabaran las imagenes que tenga una tarea de instalación.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.2 02-06-2020 - Se modifica código para crear nueva estructura de archivos.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.3 11/11/2020 - Cambios en el almacenamiento de archivos, ahora se guardarán los mismos
     *                           en el servidor NFS remoto
     *
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.4 12-01-2021 - Se modifica código para crear nueva estructura de envío de imagenes
     * y uso de tokens de seguridad.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.3 12-01-2021 - Se modifica código para crear nueva estructura de envío de imagenes
     * y uso de tokens de seguridad.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.3 12-01-2021 - Se modifica código para crear nueva estructura de envío de imagenes
     * y uso de tokens de seguridad.
     * 
     * @param $request
     * 
     * 
     * @author Carlos Caguana<ccaguana@telconet.ec>
     * @version 1.3 09-11-2020 - Se agrega la validación de Tareas por idDetalle
     * 
     * 
     * @param $request
     * 
     */
    public function procesarAction(Request $request)
    {
        $arrayData = json_decode($request->getContent(),true);

        if(!empty($_POST))
        {
            $arrayData = json_decode($_POST['body'], true);
            $arrayData['archivos'] = $_FILES["archivos"];
        }
        
        $response       = null;
        $token          = "";
        $objResponse    = new Response();
        $strOp             = $arrayData['op'];
        $serviceUtil    = $this->get('schema.Util');
        $emGeneral      = $this->getDoctrine()->getManager('telconet_general');

        //obtener nombre del source para saber que viene de TMO
        $strNameSourceTMO      = "";
        $arrayNameSourceTMO    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
        ->getOne('PARAMETROS_GENERALES_MOVIL', 
                 '', 
                 '', 
                 '', 
                 'NOMBRE_SOURCE_MOVIL', 
                 '', 
                 '', 
                 ''
                 );

        if(is_array($arrayNameSourceTMO))
        {
            $strNameSourceTMO = !empty($arrayNameSourceTMO['valor2']) ? $arrayNameSourceTMO['valor2'] : "ec.telconet.mobile.telcos.operaciones";
        }

        $arrayParametrosDetNFS  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('BANDERA_NFS',
                                                    '',
                                                    '',
                                                    '',
                                                    'S',
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    '');
        if(isset($arrayParametrosDetNFS) && $arrayParametrosDetNFS['valor1'] === 'S')
        {
            $arrayData['bandNfs'] = true;
        }

        if($arrayData['source'])
        {
            if($arrayData['source']['name'] == $strNameSourceTMO)
            {
                $arrayParametroToken = array('token'        => $arrayData['token'],
                                             'source'       => $arrayData['source'],
                                             'user'         => $arrayData['user']);

                $arrayReturnToken   = $this->validateGenerateTokenMobile($arrayParametroToken);

                if($arrayReturnToken['status'] != 200)
                {
                    return new Response(json_encode(array(
                                                            'status'    => $arrayReturnToken['status'],
                                                            'mensaje'   => $arrayReturnToken['mensaje']
                                                        )
                                                    )
                                        );
                }

                $token = $arrayReturnToken['token'];
            }
            else
            {
                $token = $this->validateGenerateToken($arrayData['token'], $arrayData['source'], $arrayData['user']);
                if(!$token)
                {
                    return new Response(json_encode(array(
                            'status' => 403,
                            'mensaje' => "token invalido"
                            )
                        )
                    );
                }
            }
        }

        if($strOp)
        {
            $boolEstadoTareaActual=false;
            $strIdDetalle=$arrayData['data']['idDetalle'];
            $serviceUtils  = $this->get('schema.Util');
            if(isset($strIdDetalle))
            {
                
              $arrayRespuesta=$serviceUtils->estadoTarea($arrayData);
              $boolEstadoTareaActual =  $arrayRespuesta['estadoTarea'];
              $response['valor']= $arrayRespuesta['valorTarea'];
              $response['estado']= $arrayRespuesta['estadoTarea'];

            }

            if($boolEstadoTareaActual)
            {
            $response['mensaje']= "La tarea se encuentra ".$arrayRespuesta['valorTarea'].
            ",por favor verificarlo con su coordinador o jefe departamental";
            $response['status']= 400;
            }else
            {
            switch($strOp)
            {
                case 'activacion':
                    $accion   = "Activar Servicio";
                    $response = $this->putDetalleDocumentoServicio($arrayData, $accion);
                    break;
                case 'soporte':
                    $accion   = "Caso";
                    $response = $this->putDetalleDocumentoCaso($arrayData, $accion);
                    break;
                case 'incidencia':
                    $strFolderApplication = $serviceUtil->getValueByStructure(array('strStructure'  => 'PARAMETROS_ESTRUCTURA_RUTAS_TELCOS', 
                                                                                    'strKey'        => $strNameSourceTMO));
                    $arrayData['strFolderApplication'] = $strFolderApplication;
                    $accion   = "Incidencia Elemento";
                    $response = $this->putDetalleDocumentoElemento($arrayData, $accion);
                    break;
                case 'tarea':
                    $strAccion           = "Tarea";
                    $arrayData['accion'] = $strAccion;
                    $response = $this->putDetalleDocumentoTarea($arrayData);
                    break;
                case 'putFotoFiscalizacion':
                    $strFolderApplication = $serviceUtil->getValueByStructure(array('strStructure'  => 'PARAMETROS_ESTRUCTURA_RUTAS_TELCOS', 
                                                                                    'strKey'        => $strNameSourceTMO));
                    $arrayData['strFolderApplication'] = $strFolderApplication;
                    $accion = "Fiscalizacion";
                    $response = $this->putDetalleDocumentoElemento($arrayData, $accion);
                    break;
                case 'guardarImagenesSincrono':
                    $strFolderApplication = $serviceUtil->getValueByStructure(array('strStructure'  => 'PARAMETROS_ESTRUCTURA_RUTAS_TELCOS', 
                                                                                    'strKey'        => $strNameSourceTMO));
                    $arrayData['strFolderApplication'] = $strFolderApplication;
                    $response = $this->guardarImagenesSincrono($arrayData);
                    break;
                default:
                    $response['status']  = $this->status['METODO'];
                    $response['mensaje'] = $this->mensaje['METODO'];
            }
            }
        }
        if(isset($response))
        {
            $response['token'] = $token;
            $objResponse = new Response();
            $objResponse->headers->set('Content-Type', 'text/json');
            $objResponse->setContent(json_encode($response));
        }
        return $objResponse;
    }
    
    /**
     * Funcion que sirve para grabar las fotos de un caso
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 23-07-2015
     * @param array $arrayData 
     * @param string $strAccion
     */
    private function putDetalleDocumentoCaso($arrayData, $strAccion)
    {
        $mensaje = "";
        
        try
        {
            $strFoto           = $arrayData['data']['foto'];
            $intIdCaso         = $arrayData['data']['idCaso'];
            $strNombreFoto     = $arrayData['data']['nombreFoto'];
            $intIdEmpresa      = $arrayData['data']['codEmpresa'];
            $strIpCreacion     = "127.0.0.1";
            $strFeCreacion     = new \DateTime('now');
            $strUsrCreacion    = $arrayData['user'];
            
            $arrayParametros = array(
                                        'idCaso'        => $intIdCaso,
                                        'foto'          => $strFoto,
                                        'nombreFoto'    => $strNombreFoto,
                                        'usrCreacion'   => $strUsrCreacion,
                                        'ipCreacion'    => $strIpCreacion,
                                        'feCreacion'    => $strFeCreacion,
                                        'idEmpresa'     => $intIdEmpresa,
                                        'accion'        => $strAccion
                                    );
            
            $procesarImagenesService    = $this->get('tecnico.ProcesarImagenes');
            $arrayResultado             = $procesarImagenesService->grabarDetalleDocumentoCaso($arrayParametros);
            
            if($arrayResultado['status']!="OK")
            {
                $mensaje = $arrayResultado['mensaje'];
                if($mensaje == null)
                {
                    throw new \Exception("NULL");
                }
                else
                {
                    throw new \Exception("ERROR_PARCIAL");
                }
            }
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            return $resultado;
        }
        
        $resultado['resultado'] = $arrayResultado;
        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $this->mensaje['OK'];
        return $resultado;
    }
    
    /**
     * funcion que sirve para grabar el detalle elemento (activacion)
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 09-07-2015
     * 
     * @author Jonathan Mazon Sanchez<jmazon@telconet.ec>
     * @version 1.1 10-03-2021  Se Agrega parametros de envio al service para el guardado de imagen al NFS
     * 
     * @param array $arrayData 
     * @param string $strAccion
     */
    private function putDetalleDocumentoServicio($arrayData, $strAccion)
    {
        $mensaje = "";
        
        try
        {
            $strFoto            = $arrayData['data']['foto'];
            $intIdServicio      = $arrayData['data']['idServicio'];
            $strNombreFoto      = $arrayData['data']['nombreFoto'];
            $intIdEmpresa       = $arrayData['data']['codEmpresa'];
            $arrayPathAdicional = $arrayData['data']['pathAdicional'];
            $boolBandera        = $arrayData['bandNfs'];
            $strIpCreacion      = "127.0.0.1";
            $strFeCreacion      = new \DateTime('now');
            $strUsrCreacion     = $arrayData['user'];
            
            $arrayParametros = array(
                                        'idServicio'    => $intIdServicio,
                                        'foto'          => $strFoto,
                                        'nombreFoto'    => $strNombreFoto,
                                        'usrCreacion'   => $strUsrCreacion,
                                        'ipCreacion'    => $strIpCreacion,
                                        'feCreacion'    => $strFeCreacion,
                                        'idEmpresa'     => $intIdEmpresa,
                                        'accion'        => $strAccion,
                                        'bandNfs'       => $boolBandera,
                                        'pathAdicional' =>  $arrayPathAdicional
                                    );
            
            $procesarImagenesService    = $this->get('tecnico.ProcesarImagenes');
            $arrayResultado             = $procesarImagenesService->grabarDetalleDocumentoServicio($arrayParametros);
            
            if($arrayResultado['status']!="OK")
            {
                $mensaje = $arrayResultado['mensaje'];
                
                if($mensaje == null)
                {
                    throw new \Exception("NULL");
                }
                else
                {
                    throw new \Exception("ERROR_PARCIAL");
                }
            }
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            return $resultado;
        }
        
        $resultado['resultado'] = $arrayResultado;
        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $this->mensaje['OK'];
        return $resultado;
    }
    
    /**
     * funcion que sirve para grabar el detalle elemento (incidencia)
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 18-06-2015
     * 
     * @author John Vera R. <javera@telconet.ec>
     * @version 1.1 02-04-2018  Se valida las coordenadas de la imagen
     *
     * @author Walther Joao Gaibor. <javera@telconet.ec>
     * @version 1.2 24-05-2018 Se graba el idCaso en caso de tenerlo.
     * 
     * @author Jean Nazareno. <jnazareno@telconet.ec>
     * @version 1.3 12-06-2019 Se agrega parámetro "strEtiquetaFoto" para guardar la etiqueta de la foto
     *
     *  
     * Se agrega llamado de métodos por medio de la acción seleccionada. 
     * Sí la accion es Fiscalizacion, graba fotos asociadas a una cuadrilla fiscalizada, esta opción 
     * es utilizada inicialmente por TM-Operaciones.
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 26-08-2019
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.1 02-06-2020 - Se modifica código para crear nueva estructura de archivos.
     *
     * @param array $arrayData datos necesarios para guardarla foto.
     * @param string $accion variable que identifica de donde esta siendo llamado.
     * 
     */
    private function putDetalleDocumentoElemento($arrayData, $strAccion)
    {   
        $serviceUtil            = $this->get('schema.Util');
        $mensaje                = "";
        $strAccionFiscalizacion = "Fiscalizacion";
        $arrayResultado         = array();
        $emFinan                = $this->getDoctrine()->getManager("telconet_financiero");
        $emComercial            = $this->getDoctrine()->getManager("telconet");
        $strCodigoPostal        = '593';
        $strOrigenAccion        = 'tareas';
        $strExt                 = 'jpg';

        try
        {
            $intIdDetalle      = $arrayData['data']['idDetalle'];
            $intIdCaso         = $arrayData['data']['idCaso'];
            $strFoto           = $arrayData['data']['foto'];
            $strNombreFoto     = $arrayData['data']['nombreFoto'];
            $intIdEmpresa      = $arrayData['data']['codEmpresa'];
            $intIpCreacion     = "127.0.0.1";
            $strFeCreacion     = new \DateTime('now');
            $strUsrCreacion    = $arrayData['user'];
            
            $strPrefijoEmpresa = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->getPrefijoByCodigo($intIdEmpresa);

            if(isset($arrayData['bandNfs']) && $arrayData['bandNfs'])
            {
                $strAplicacion = $arrayData['strFolderApplication'];
            }
            else
            {
                $arrayParametrosFilePath = array(
                    'strCodigoPostal'       => $strCodigoPostal,
                    'strPrefijoEmpresa'     => $strPrefijoEmpresa,
                    'strFolderApplication'  => $arrayData['strFolderApplication'],
                    'strController'         => 'ProcesarImagenes',
                    'strOrigenAccion'       => $strOrigenAccion,
                    'strExt'                => $strExt
                );

                $strRutaFisicaCompleta = $serviceUtil->createNewFilePath($arrayParametrosFilePath);
            }
         
            $arrayParametros   = array(
                                        'idDetalle'                 => $intIdDetalle,
                                        'idCaso'                    => $intIdCaso,
                                        'foto'                      => $strFoto,
                                        'nombreFoto'                => $strNombreFoto,
                                        'usrCreacion'               => $strUsrCreacion,
                                        'ipCreacion'                => $intIpCreacion,
                                        'feCreacion'                => $strFeCreacion,
                                        'idEmpresa'                 => $intIdEmpresa,
                                        'accion'                    => $strAccion,
                                        'floatLatitud'              => $arrayData['data']['floatLatitud'],
                                        'floatLongitud'             => $arrayData['data']['floatLongitud'],
                                        'strLatitudRef'             => $arrayData['data']['strLatitudRef'],
                                        'strLongitudRef'            => $arrayData['data']['strLongitudRef'],
                                        'strEtiquetaFoto'           => $arrayData['data']['strEtiquetaFoto'],
                                        'intIdCuarillaHistorial'    => $arrayData['data']['idCuarillaHistorial'],
                                        'strRutaFisicaCompleta'     => $strRutaFisicaCompleta,
                                        'strPrefijoEmpresa'         => $strPrefijoEmpresa,
                                        'strApp'                    => $strAplicacion,
                                        'bandNfs'                   => $arrayData['bandNfs'],
                                        'strSubModulo'              => $strOrigenAccion
                                    );
            
            $procesarImagenesService = $this->get('tecnico.ProcesarImagenes');
 
            if($strAccion == $strAccionFiscalizacion)
            {
                $arrayResultado       = $procesarImagenesService->grabarFotoFiscalizacion($arrayParametros);
            }
            else
            {
                $arrayResultado       = $procesarImagenesService->grabarDetalleDocumentoElemento($arrayParametros);
            }

            if($arrayResultado['status']!="OK")
            {
                $mensaje = $arrayResultado['mensaje'];
                if($mensaje == null)
                {
                    throw new \Exception("NULL");
                }
                else
                {
                    throw new \Exception("ERROR_PARCIAL");
                }
            }
        }
        catch(\Exception $e)
        {

            $serviceUtil->insertError('Telcos+',
            'ProcesarImagenesWSController.putDetalleDocumentoElemento',
            $arrayDatos["mensaje"],
            "",
            "127.0.0.1");

            if($e->getMessage() == "NULL")
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            return $resultado;
        }
        
        $resultado['resultado'] = $arrayResultado;
        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $this->mensaje['OK'];
        return $resultado;
    }

    /**
     * funcion que sirve para grabar el detalle de una tarea interdepartamental
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 24-11-2017
     * @param array $arrayData[
     *                          data {
     *                              foto           : String Base64  : Foto que se captura para una tarea en especifico.
     *                              idCaso         : Integer        : idCaso forma de asociar un caso con una foto.
     *                              idDetalle      : Integer        : idDetalle forma de asociar un idDetalle con una foto.
     *                              idServicio     : Integer        : idServicio forma de asociar una instalación con una foto.
     *                              codEmpresa     : Integer        : Codigo de la empresa.
     *                              idDepartamento : Integer        : Id del departamento del operativo.
     *                              idOficina      : Integer        : Id de la oficina del operativo.
     *                              prefijoEmpresa : String         : Prefijo de la empresa del operativo
     *                          }
     *                          op                 : String         : Nombre del método a consumir en telcos.
     *                          source {
     *                              name           : String         : Nombre de la aplicación.
     *                              originID       : Integer        : Id de la aplicación.
     *                              tipoOriginID   : String         : Origen de la aplicación.
     *                          }
     *                          token              : String         : Token Security
     *                          user               : String         : Usuario que hace uso de la aplicación.
     *                         ]
     * @param string $arrayResultado               : array          : Respuesta del consumo.
     */
    private function putDetalleDocumentoTarea($arrayData)
    {
        $strMensaje = "";
        $arrayResultado = array();
        try
        {
            $strFoto           = $arrayData['data']['foto'];
            $intIdDetalle      = $arrayData['data']['idDetalle'];
            $strNombreFoto     = $arrayData['data']['nombreFoto'];
            $intIdEmpresa      = $arrayData['data']['codEmpresa'];
            $strIpCreacion     = "127.0.0.1";
            $strFeCreacion     = new \DateTime('now');
            $strUsrCreacion    = $arrayData['user'];

            $arrayParametros = array(
                                        'idDetalle'     => $intIdDetalle,
                                        'foto'          => $strFoto,
                                        'nombreFoto'    => $strNombreFoto,
                                        'usrCreacion'   => $strUsrCreacion,
                                        'ipCreacion'    => $strIpCreacion,
                                        'feCreacion'    => $strFeCreacion,
                                        'idEmpresa'     => $intIdEmpresa,
                                        'accion'        => $arrayData['accion']
                                    );

            $serviceProcesarImagenes    = $this->get('tecnico.ProcesarImagenes');
            $arrayResultado             = $serviceProcesarImagenes->grabarDetalleDocumentoTarea($arrayParametros);

            if($arrayResultado['status']!="OK")
            {
                $strMensaje = $arrayResultado['mensaje'];
                if($strMensaje == null)
                {
                    throw new \Exception("NULL");
                }
                else
                {
                    throw new \Exception("ERROR_PARCIAL");
                }
            }
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $arrayResultado['status']    = $this->status['NULL'];
                $arrayResultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResultado['status']    = $this->status['ERROR_PARCIAL'];
                $arrayResultado['mensaje']   = $strMensaje;
            }
            else
            {
                $arrayResultado['status']    = $this->status['ERROR'];
                $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
            }
            return $arrayResultado;
        }

        $arrayResultado['resultado'] = $arrayResultado;
        $arrayResultado['status']    = $this->status['OK'];
        $arrayResultado['mensaje']   = $this->mensaje['OK'];
        return $arrayResultado;
    }

    /**
     * funcion que sirve para guardar archivos sincronos
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 07-01-2021
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.1 08-02-2021
     * 
     * Se modifíca lógica para obtener el código de empresa de la tarea
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 06-10-2022 - Se agrega validación para las imagenes del progreso FOTO_DESPUES
     *                           para el servicio con estado Activo del producto SEG_VEHICULO.
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 1.4 12-01-2023 - Se agreag a ala validacion si el tipo de solicitud corresponde a SOLICITUD PLANIFICACION
     *                           para el servicio MOBILE BUS
     * 
     * @param array $arrayData datos necesarios para guardar los archivos.
     * 
     */
    private function guardarImagenesSincrono($arrayData)
    {   
        $strAccionFiscalizacion  = "Fiscalizacion";
        $strAccionTareas         = "Tareas";
        $serviceUtil             = $this->get('schema.Util');
        $strMensaje              = "";
        $strMensajeExitoImagenes = "";
        $arrayResultado          = array();
        $arrayResultadoResponse  = array();
        $emComercial             = $this->getDoctrine()->getManager("telconet");
        $emGeneral               = $this->getDoctrine()->getManager('telconet_general');
        $emComunicacion          = $this->getDoctrine()->getManager("telconet_comunicacion");
        $emSoporte               = $this->getDoctrine()->getManager("telconet_soporte");
        $strCodigoPostal         = '593';
        $strOrigenAccion         = 'tareas';
        $strExt                  = 'jpg';

        try
        {
            $intIdComunicacion = $arrayData['data']['idTarea'];

            if(isset($intIdComunicacion) && !empty($intIdComunicacion))
            {
                $objInfoComunicacion = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                                                      ->find($intIdComunicacion);

                if(is_object($objInfoComunicacion))
                {
                    $arrayData['data']['codEmpresa'] = $objInfoComunicacion->getEmpresaCod();
                }
            }


            $arrayMensajeExitoImagenes    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('MENSAJES_TM_OPERACIONES', 
                    '', 
                    '', 
                    '', 
                    'MSG_OK_GUARDARIMAGENESSINCRONO', 
                    '', 
                    '', 
                    ''
                    );

            if(is_array($arrayMensajeExitoImagenes))
            {
                $strMensajeExitoImagenes = !empty($arrayMensajeExitoImagenes['valor2']) ? 
                $arrayMensajeExitoImagenes['valor2'] : "Éxito al guardar imagenes.";
            }

            $intIdDetalle      = $arrayData['data']['idDetalle'];
            $intIdCaso         = $arrayData['data']['idCaso'];
            $strFoto           = '';
            $strNombreFoto     = '';
            $intIdEmpresa      = $arrayData['data']['codEmpresa'];
            $strAccion         = $arrayData['data']['accion'];
            $intIpCreacion     = "127.0.0.1";
            $strFeCreacion     = new \DateTime('now');
            $strUsrCreacion    = $arrayData['user'];
            
            $strPrefijoEmpresa = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->getPrefijoByCodigo($intIdEmpresa);

            if(isset($arrayData['bandNfs']) && $arrayData['bandNfs'])
            {
                $strAplicacion = $arrayData['strFolderApplication'];
            }
            else
            {
                $arrayParametrosFilePath = array(
                    'strCodigoPostal'       => $strCodigoPostal,
                    'strPrefijoEmpresa'     => $strPrefijoEmpresa,
                    'strFolderApplication'  => $arrayData['strFolderApplication'],
                    'strController'         => 'ProcesarImagenes',
                    'strOrigenAccion'       => $strOrigenAccion,
                    'strExt'                => $strExt
                );

                $strRutaFisicaCompleta = $serviceUtil->createNewFilePath($arrayParametrosFilePath);
            }

            $strCodigoTipoProgreso = 'FOTO';
            if(!empty($arrayData['data']['idServicio']) && !empty($intIdDetalle))
            {
                $objDetalleSolicitud = null;
                $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayData['data']['idServicio']);
                $objDetalle  = $emSoporte->getRepository('schemaBundle:InfoDetalle')->find($intIdDetalle);
                if(is_object($objDetalle))
                {
                    $intDetSolTarea = $objDetalle->getDetalleSolicitudId();
                    if(!empty($intDetSolTarea))
                    {
                        $objDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                                            ->find($intDetSolTarea);
                    }
                }
                if(is_object($objServicio) && is_object($objServicio->getProductoId())
                   && $objServicio->getProductoId()->getNombreTecnico() == 'SEG_VEHICULO'
                   && $objServicio->getEstado() == "Activo"
                   && is_object($objDetalleSolicitud)
                   && $objDetalleSolicitud->getTipoSolicitudId()->getDescripcionSolicitud() == 'SOLICITUD PLANIFICACION')
                {
                    $strCodigoTipoProgreso = 'FOTO_DESPUES';
                }
            }

            foreach ($arrayData['archivos']["error"] as $key => $error) 
            {
                if ($error == 0) 
                {

                    $strPathName    =   $arrayData['data']['dataFiles'][$key]['rutaFoto'];
                    $strFileExt     =   strtolower(end(explode('.',$strPathName)));
                    $strFileTmp     =   $arrayData['archivos']['tmp_name'][$key];
                    $strType        =   pathinfo($strFileTmp, PATHINFO_EXTENSION);
                    $strData        =   file_get_contents($strFileTmp);
                    $strMine        =   '';

                    if(strtoupper($strFileExt) === "JPG" || 
                        strtoupper($strFileExt) === "JPEG" || 
                        strtoupper($strFileExt) === "PNG")
                    {
                        $strMine = 'data:image/';  
                    }

                    $strFoto        = $strMine . $strType . ';base64,' . base64_encode($strData);
                    $strNombreFoto  = $arrayData['data']['dataFiles'][$key]['nombreFoto'];

                    $arrayParametros   = array(
                                                'idDetalle'                 => $intIdDetalle,
                                                'idCaso'                    => $intIdCaso,
                                                'foto'                      => $strFoto,
                                                'nombreFoto'                => $strNombreFoto,
                                                'usrCreacion'               => $strUsrCreacion,
                                                'ipCreacion'                => $intIpCreacion,
                                                'feCreacion'                => $strFeCreacion,
                                                'idEmpresa'                 => $intIdEmpresa,
                                                'accion'                    => $strAccion,
                                                'floatLatitud'              => $arrayData['data']['dataFiles'][$key]['latitud'],
                                                'floatLongitud'             => $arrayData['data']['dataFiles'][$key]['longitud'],
                                                'strLatitudRef'             => $arrayData['data']['dataFiles'][$key]['latitudRef'],
                                                'strLongitudRef'            => $arrayData['data']['dataFiles'][$key]['longitudRef'],
                                                'strEtiquetaFoto'           => $arrayData['data']['dataFiles'][$key]['etiquetaFoto'],
                                                'intIdCuarillaHistorial'    => $arrayData['data']['idCuarillaHistorial'],
                                                'strRutaFisicaCompleta'     => $strRutaFisicaCompleta,
                                                'strPrefijoEmpresa'         => $strPrefijoEmpresa,
                                                'strApp'                    => $strAplicacion,
                                                'bandNfs'                   => $arrayData['bandNfs'],
                                                'strSubModulo'              => $strOrigenAccion
                                            );
                    
                    $objProcesarImagenesService = $this->get('tecnico.ProcesarImagenes');
                            
                    if($strAccion == $strAccionFiscalizacion)
                    {
                        $arrayResultado       = $objProcesarImagenesService->grabarFotoFiscalizacion($arrayParametros);
                    }
                    else
                    {
                        $arrayResultado       = $objProcesarImagenesService->grabarDetalleDocumentoElemento($arrayParametros);
                    }

                    if($arrayResultado['status'] == "OK")
                    {
                        if(($key+1) == count($arrayData['archivos']["error"]) && $strAccion == $strAccionTareas)
                        {
                            $arrayParametros  = array(
                                'strCodEmpresa'        => $arrayData['data']['codEmpresa'],
                                'intIdTarea'           => $arrayData['data']['idTarea'],
                                'intIdDetalle'         => $arrayData['data']['idDetalle'],
                                'strCodigoTipoProgreso'=> $strCodigoTipoProgreso,
                                'intIdServicio'        => $arrayData['data']['idServicio'],
                                'strDescripcionTarea'  => $arrayData['data']['strDescripcionTarea'],
                                'strOrigen'            => $arrayData['data']['strOrigen'],
                                'strUsrCreacion'       => $arrayData['user'],
                                'strIpCreacion'        => '127.0.0.1');
      
                            $objSoporteService = $this->get('soporte.SoporteService');

                            $arrayRespuestaProgreso = $objSoporteService->ingresarProgresoTarea($arrayParametros);
                                                        
                            if($arrayRespuestaProgreso['status'] != "OK" && 
                            strpos($arrayRespuestaProgreso['mensaje'], 'Ya existe un registro del progreso de la tarea') === false)
                            {
                                $strMensajeErrorProgreso      = "";
                                $arrayMensajeErrorProgreso    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                ->getOne('MENSAJES_TM_OPERACIONES', 
                                        '', 
                                        '', 
                                        '', 
                                        'MSG_ERROR_GUARDARPROGRESO', 
                                        '', 
                                        '', 
                                        ''
                                        );
            
                                if(is_array($arrayMensajeErrorProgreso))
                                {
                                    $strMensajeErrorProgreso = !empty($arrayMensajeErrorProgreso['valor2']) 
                                    ? $arrayMensajeErrorProgreso['valor2'] : "Error al guardar el progreso.";
                                }

                                $strMensaje = $strMensajeErrorProgreso;
                                throw new \Exception("ERROR_PARCIAL");
                            }
                        }
                    }
                    else
                    {
                        $strMensaje = $arrayResultado['mensaje'];
                        if($strMensaje == null)
                        {
                            throw new \Exception("NULL");
                        }
                        else
                        {
                            throw new \Exception("ERROR_PARCIAL");
                        }
                    }
                }
                else
                {
                    $strMensajeErrorImagenes      = "";
                    $arrayMensajeErrorImagenes    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('MENSAJES_TM_OPERACIONES', 
                            '', 
                            '', 
                            '', 
                            'MSG_ERROR_GUARDARIMAGENESSINCRONO', 
                            '', 
                            '', 
                            ''
                            );

                    if(is_array($arrayMensajeErrorImagenes))
                    {
                        $strMensajeErrorImagenes = !empty($arrayMensajeErrorImagenes['valor2']) 
                        ? $arrayMensajeErrorImagenes['valor2'] : "Error al guardar imagenes.";
                    }
                    $strMensaje = $strMensajeErrorImagenes;
                    throw new \Exception("ERROR_PARCIAL");
                }
            }
        }
        catch(\Exception $exception)
        {
            if($exception->getMessage() == "NULL")
            {
                $arrayResultadoResponse['status']    = $this->status['NULL'];
                $arrayResultadoResponse['mensaje']   = $this->mensaje['NULL'];
            }
            else if($exception->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResultadoResponse['status']    = $this->status['ERROR_PARCIAL'];
                $arrayResultadoResponse['mensaje']   = $strMensaje;
            }
            else
            {
                $strMensaje                          = $exception->getMessage();
                $arrayResultadoResponse['status']    = $this->status['ERROR'];
                $arrayResultadoResponse['mensaje']   = $this->mensaje['ERROR'];
            }

            $arrayParametrosLog['enterpriseCode']   =  $arrayData['data']['codEmpresa']; 
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = "TELCOS";
            $arrayParametrosLog['appClass']         = "ProcesarImagenesWSController";
            $arrayParametrosLog['appMethod']        = "guardarImagenesSincrono";
            $arrayParametrosLog['messageUser']      = "No aplica.";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $strMensaje;
            $arrayParametrosLog['inParameters']     = json_encode($arrayData);
            $arrayParametrosLog['creationUser']     = $arrayData['user'];

            $serviceUtil->insertLog($arrayParametrosLog);
            
            return $arrayResultadoResponse;
        }
        
        $arrayResultadoResponse['status']    = $this->status['OK'];
        $arrayResultadoResponse['mensaje']   = $strMensajeExitoImagenes;
        return $arrayResultadoResponse;
    }
}