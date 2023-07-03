<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Documentación del controller 'ReprocesamientoContableController'
 * 
 * Controlador que contiene las funciones que ayudarán a reprocesar la información para los procesos contables
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 21-02-2017
 */
class ReprocesamientoContableController extends Controller
{
    /**
     * @Secure(roles="ROLE_375-1")
     * 
     * Muestra la pantalla inicial para realizar el reprocesamiento contable de la facturacion
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 21-02-2017
     */
    public function indexFacturacionAction()
    {
        return $this->render('financieroBundle:ReprocesamientoContable:indexFacturacion.html.twig');
    }


    /**
     * Documentación para la función 'getTiposProcesoContableAction'
     * 
     * Función que retorna los tipos de procesos contables para ser reprocesados
     * 
     * @return JsonResponse $objJsonResponse  Contiene la información de los tipos de procesos contables que podrá seleccionar el usuario
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 21-02-2017
     */   
    public function getTiposProcesoContableAction()
    {
        $objJsonResponse = new JsonResponse();
        $objRequest      = $this->get('request');
        $objSession      = $objRequest->getSession();
        $strIpCreacion   = $objRequest->getClientIp();
        $serviceUtil     = $this->get('schema.Util');
        $strUsuario      = $objSession->get('user') ? $objSession->get('user') : '';
        $strEmpresaCod   = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : '';
        $strTipoOpcion   = $objRequest->query->get('strTipoOpcion') ? $objRequest->query->get('strTipoOpcion') : '';
        $emGeneral       = $this->getDoctrine()->getManager("telconet_general");
        
        $arrayTiposProcesoContables = array('total' => 0, 'encontrados' => array());
        
        try
        {
            if( !empty($strEmpresaCod) )
            {
                if( !empty($strTipoOpcion) )
                {
                    $arrayParametros            = array('valor1'                => $strTipoOpcion,
                                                        'strEmpresaCod'         => $strEmpresaCod,
                                                        'strNombreParametroCab' => 'PROCESOS_CONTABLES', 
                                                        'estado'                => 'Activo');
                    $arrayTiposProcesoContables = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getArrayDetalleParametros($arrayParametros);
                }
                else
                {
                    throw new \Exception('No se envía el tipo de opción  que está llamando a la función getTiposProcesoContableAction');
                }//( !empty($strTipoOpcion) )
            }
            else
            {
                throw new \Exception('No tiene una empresa en sessión para llamar a la función getTiposProcesoContableAction');
            }//( !empty($strEmpresaCod) )    
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+', 
                                       'ReprocesamientoContableController:getTiposProcesoContableAction', 
                                       'Error al obtener los tipos de proceso contable. '.$e->getMessage(), 
                                       $strUsuario, 
                                       $strIpCreacion );
        }
        
        $objJsonResponse->setData($arrayTiposProcesoContables);
        
        return $objJsonResponse;
    }
    
    
    /**
     * Documentación para la función 'reprocesarFacturacionAction'
     * 
     * Función que llama al procedure que realiza el re-procesamiento de la información dependiendo de lo seleccionado por el usuario.
     *
     * @return Response $objResponse
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 21-02-2017
     */   
    public function reprocesarFacturacionAction()
    {
        $objResponse                = new Response();
        $objRequest                 = $this->get('request');
        $emGeneral                  = $this->getDoctrine()->getManager("telconet_general");
        $emFinanciero               = $this->getDoctrine()->getManager("telconet_financiero");
        $objSession                 = $objRequest->getSession();
        $strUsuario                 = $objSession->get('user') ? $objSession->get('user') : '';
        $serviceUtil                = $this->get('schema.Util');
        $strIpCreacion              = $objRequest->getClientIp();
        $strEmpresaCod              = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : '';
        $strPrefijoEmpresa          = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : '';
        $strTipoOpcion              = $objRequest->query->get('strTipoOpcion') ? $objRequest->query->get('strTipoOpcion') : '';
        $strTipoReporteContabilidad = $objRequest->query->get('strTipoReporteContabilidad') ? $objRequest->query->get('strTipoReporteContabilidad')
                                      : '';
        $strMensajeProceso          = 'No se pudo realizar el reprocesamiento contable solicitado por el usuario.';
        
        $strFechaReprocesamientoContable = $objRequest->query->get('strFechaReprocesamientoContable') 
                                           ? $objRequest->query->get('strFechaReprocesamientoContable') : '';
        $strFechaReprocesamientoContable = date_format(date_create($strFechaReprocesamientoContable), 'Y-m-d').'';
        
        try
        {
            if( !empty($strEmpresaCod) && !empty($strPrefijoEmpresa) && !empty($strUsuario) )
            {
                if( !empty($strTipoReporteContabilidad) && !empty($strFechaReprocesamientoContable) )
                {
                    $arrayParametros          = array('valor1'                => $strTipoOpcion,
                                                      'strEmpresaCod'         => $strEmpresaCod,
                                                      'strNombreParametroCab' => 'PROCESOS_CONTABLES', 
                                                      'valor2'                => $strTipoReporteContabilidad,
                                                      'estado'                => 'Activo');
                    $arrayResultadoTiposProcesosContable = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                     ->getArrayDetalleParametros($arrayParametros);

                    if( isset($arrayResultadoTiposProcesosContable['encontrados']) && !empty($arrayResultadoTiposProcesosContable['encontrados'])
                        && isset($arrayResultadoTiposProcesosContable['total']) && !empty($arrayResultadoTiposProcesosContable['total'])
                        && $arrayResultadoTiposProcesosContable['total'] == 1 )
                    {
                        $arrayTipoProcesoContable   = $arrayResultadoTiposProcesosContable['encontrados'][0];
                        $strCodigoTipoDocumento     = ( isset($arrayTipoProcesoContable['valor4']) && !empty($arrayTipoProcesoContable['valor4']) ) 
                                                      ? $arrayTipoProcesoContable['valor4'] : '';
                        $strCodigoDiario            = ( isset($arrayTipoProcesoContable['valor2']) && !empty($arrayTipoProcesoContable['valor2']) )
                                                      ? $arrayTipoProcesoContable['valor2'] : '';
                        $strActualizarContabilizado = ( isset($arrayTipoProcesoContable['valor3']) && !empty($arrayTipoProcesoContable['valor3']) )
                                                      ? $arrayTipoProcesoContable['valor3'] : '';
                        $strTipoProceso             = ( isset($arrayTipoProcesoContable['valor5']) && !empty($arrayTipoProcesoContable['valor5']) )
                                                      ? $arrayTipoProcesoContable['valor5'] : '';
                        
                        $arrayParametros = array('strEmpresaCod'                   => $strEmpresaCod,
                                                 'strPrefijoEmpresa'               => $strPrefijoEmpresa,
                                                 'strCodigoTipoDocumento'          => $strCodigoTipoDocumento,
                                                 'strCodigoDiario'                 => $strCodigoDiario,
                                                 'strActualizarContabilizado'      => $strActualizarContabilizado,
                                                 'strFechaReprocesamientoContable' => $strFechaReprocesamientoContable,
                                                 'strUsuario'                      => $strUsuario,
                                                 'strTipoProceso'                  => $strTipoProceso);
                        
                        $strMensajeProceso = $emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                                          ->getReprocesamientoContable($arrayParametros);
                    }//( !empty($arrayTipoProcesoContable) )
                }
                else
                {
                    throw new \Exception('No se envían los paráemtros adecuados para realizar el procesamiento contable requerido por el usuario');
                }//( !empty($strTipoOpcion) )
            }
            else
            {
                throw new \Exception('No tiene una sessión activa para llamar a la función reprocesarFacturacionAction');
            }//( !empty($strEmpresaCod) )  
        }
        catch(\Exception $e)
        {
            $strMensajeProceso = "Error al realizar el reprocesamiento de los documentos electrónicos.";
            
            $serviceUtil->insertError( 'Telcos+', 
                                       'ReprocesamientoContableController:reprocesarFacturacionAction', 
                                       'Error al realizar el reprocesamiento de los documentos electrónicos. '.$e->getMessage(), 
                                       $strUsuario, 
                                       $strIpCreacion );
        }//try
        
        $objResponse->setContent($strMensajeProceso);
        
        return $objResponse;
    }
}
