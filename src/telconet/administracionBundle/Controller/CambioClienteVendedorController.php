<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use telconet\schemaBundle\Entity\InfoLog;
use telconet\schemaBundle\Entity\ReturnResponse;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;

/**
 * CambioClienteVendedorController.
 *
 * Controlador que contiene las funciones correspondientes al cambio masivo de clientes vendedor
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.0 25-11-2019
 */

class CambioClienteVendedorController extends Controller implements TokenAuthenticatedController
{
    /**
     * indexAction, accede a la página principal de cambio de vendedor masivo.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 25-11-2019
     * @since 1.0
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_442-1")
     */
    public function indexAction()
    {
        return $this->render('administracionBundle:CambioClienteVendedor:index.html.twig');
    }

    /**
     * getClientesPorVendedorAjaxAction
     *
     * Obtiene los clientes del vendedor
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 26-11-2019
     * @since 1.0
     * @throws $objException
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     */
    public function getClientesPorVendedorAjaxAction()
    {
        $objJsonResponse   = new JsonResponse();
        $objReturnResponse = new ReturnResponse();

        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $serviceUtil       = $this->get('schema.Util');
        $strLoginVendedor  = $objRequest->get('strLoginVendedor', 'N');
        $arrayParametros   = array();

        $emComercial->getConnection()->beginTransaction();

        try
        {
            //Termina el metodo si no se envía login del vendedor
            if(!isset($strLoginVendedor) || empty($strLoginVendedor))
            {
                throw new \Exception(" No se está enviando login del vendedor a consultar.");
            }

            $arrayParametros['strLoginVendedor'] = $strLoginVendedor;
            $arrayRespuesta = $emComercial->getRepository('schemaBundle:InfoPersona')
                ->getClientesPorVendedor($arrayParametros);

            if(is_array($arrayRespuesta) && is_array($arrayRespuesta['registros']) && count($arrayRespuesta['registros']) > 0)
            {
                $objReturnResponse->setRegistros($arrayRespuesta['registros']);
                $objReturnResponse->setTotal($arrayRespuesta['total']);
                $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
            }
            else
            {
                $objReturnResponse->setRegistros(array());
                $objReturnResponse->setTotal(0);
                $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT .
                    ' El vendedor no tiene clientes asignados');
            }
        }
        catch(\Exception $objException)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $objException->getMessage());
            $serviceUtil->insertError('Telcos+',
                __METHOD__,
                $objReturnResponse->getStrMessageStatus(),
                $objSession->get('user'),
                $objRequest->getClientIp());
        }

        $emComercial->getConnection()->close();
        $objJsonResponse->setData($objReturnResponse);

        return $objJsonResponse;
    }

    /**
     * crearSolicitudCambioVendedorAjaxAction
     *
     * Se crea la solicitud para cambio masivo de clientes del vendedor
     * el cual deberá ser aprobada o rechazada por el gerente comercial.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 27-11-2019
     * @since 1.0
     * @throws $objException
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     */

    public function crearSolicitudCambioVendedorAjaxAction()
    {
        $objResponse                   = new JsonResponse();
        $objReturnResponse             = new ReturnResponse();

        $objRequest                    = $this->getRequest();
        $objSession                    = $objRequest->getSession();
        $emComercial                   = $this->getDoctrine()->getManager();
        $emGeneral                     = $this->getDoctrine()->getManager("telconet_general");
        $serviceUtil                   = $this->get('schema.Util');
        $strClientIp                   = $objRequest->getClientIp();
        $strEmpresaId                  = $objSession->get('idEmpresa');
        $strUsrCreacion                = $objSession->get('user');
        $strLoginVendedorOrigen        = $objRequest->get('strLoginVendedorOrigen', '');
        $strLoginVendedorDestino       = $objRequest->get('strLoginVendedorDestino', '');
        $intIdPersonaEmpresaRolOrigen  = $objRequest->get('intIdPersonaEmpresaRolOrigen', 0);
        $intIdPersonaEmpresaRolDestino = $objRequest->get('intIdPersonaEmpresaRolDestino', 0);
        $strIdClientes                 = $objRequest->get('strIdClientes', '');


        //Termina el metodo si no envia los datos adecuados
        if(empty($strLoginVendedorOrigen) || empty($strLoginVendedorDestino) || empty($intIdPersonaEmpresaRolOrigen) ||
            empty($intIdPersonaEmpresaRolDestino) || empty($strIdClientes))
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " No se pudo obtener información requerida");
            $serviceUtil->insertError('Telcos+', __METHOD__, $objReturnResponse->getStrMessageStatus(), $strUsrCreacion, $strClientIp);
            $objResponse->setData($objReturnResponse);
            return $objResponse;
        }

        $emComercial->getConnection()->beginTransaction();
        $emGeneral->getConnection()->beginTransaction();

        try
        {
            //Se obtiene la solicitud por cambio de vendedor
            $objCambioVendedorSolicitud = $emComercial->getRepository("schemaBundle:AdmiTipoSolicitud")
                ->findOneBy(array('estado'               => 'Activo',
                                  'descripcionSolicitud' => 'SOLICITUD CAMBIO MASIVO CLIENTES VENDEDOR'));

            if(!is_object($objCambioVendedorSolicitud))
            {
                throw new \Exception(' No se encontró el tipo de solicitud por cambio masivo de vendedor.');
            }

            //Se obtiene solicitudes pendientes de aprobacion / rechazo
            $arraySolicitudesPendientes = $emGeneral->getRepository("schemaBundle:InfoLog")
                ->findBy(array('accion' => $objCambioVendedorSolicitud->getId(),
                               'clase'  => $intIdPersonaEmpresaRolOrigen,
                               'estado' => 'Pendiente'));

            if(!is_null($arraySolicitudesPendientes) && is_array($arraySolicitudesPendientes) && count($arraySolicitudesPendientes) <= 0)
            {
                //Se obtiene la caracteristica por 'solicitud cambio masivo vendedor origen'.
                $objSolVendedorOrigenCarac = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                    ->findOneBy(array('estado'                    => 'Activo',
                                      'descripcionCaracteristica' => 'SOLICITUD_CAMBIO_MASIVO_VENDEDOR_ORIGEN'));

                if(!is_object($objSolVendedorOrigenCarac))
                {
                    throw new \Exception('No se encontró la característica por solicitud cambio masivo vendedor origen');
                }

                //Se obtiene vendedor origen
                $objVendedor = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                    ->find($intIdPersonaEmpresaRolOrigen);


                if(!is_object($objVendedor))
                {
                    throw new \Exception('No se encontró información del vendedor origen');
                }

                $strVendedorOrigenNombres = ucwords(strtolower(trim($objVendedor->getPersonaId()->__toString())));

                //Se obtiene vendedor destino
                $objVendedor = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                    ->find($intIdPersonaEmpresaRolDestino);

                if(!is_object($objVendedor))
                {
                    throw new \Exception('No se encontró información del vendedor destino');
                }

                $strVendedorDestinoNombres = ucwords(strtolower(trim($objVendedor->getPersonaId()->__toString())));

                $arrayIdClientes = explode(',', $strIdClientes);
                $arrayParametrosTotales['arrayIdClientes'] = $arrayIdClientes;

                $arrayRespuestaTotales = $emComercial->getRepository('schemaBundle:InfoPunto')
                    ->getTotalPuntosPorCliente($arrayParametrosTotales);

                if(is_null($arrayRespuestaTotales) || !is_array($arrayRespuestaTotales) || empty($arrayRespuestaTotales))
                {
                    throw new \Exception(' Error al calcular totales de puntos y servicios');
                }

                $strObservacion = '<b>Cambio de vendedor masivo:</b><br/>' .
                    '&emsp;<b>Vendedor Origen:</b>&ensp;&ensp;' . $strVendedorOrigenNombres . '<br/>' .
                    '&emsp;<b>Vendedor Destino:</b>&ensp;' . $strVendedorDestinoNombres . '<br/>' .
                    '&emsp;<b>Clientes Totales:</b>&ensp;&ensp;&ensp;' . $arrayRespuestaTotales['intCantidadClientes'] . '<br/>' .
                    '&emsp;<b>Puntos Totales:</b>&ensp;&ensp;&ensp;&ensp;' . $arrayRespuestaTotales['intCantidadPuntos'] . '<br/>' .
                    '&emsp;<b>Servicios Totales:</b>&ensp;&ensp;' . $arrayRespuestaTotales['intCantidadServicios'];


                //Se guarda la solicitud de cambio de vendedor
                $objDetalleSolicitud = new InfoDetalleSolicitud();
                $objDetalleSolicitud->setTipoSolicitudId($objCambioVendedorSolicitud);
                $objDetalleSolicitud->setObservacion($strObservacion);
                $objDetalleSolicitud->setUsrCreacion($strUsrCreacion);
                $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitud->setEstado('Pendiente');
                $emComercial->persist($objDetalleSolicitud);

                //Se guarda la solicitud caracteristica de cambio de vendedor
                $objDetalleSolCarac = new InfoDetalleSolCaract();
                $objDetalleSolCarac->setCaracteristicaId($objSolVendedorOrigenCarac);
                $objDetalleSolCarac->setValor($strLoginVendedorOrigen);
                $objDetalleSolCarac->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolCarac->setEstado('Activo');
                $objDetalleSolCarac->setUsrCreacion($strUsrCreacion);
                $objDetalleSolCarac->setUsrUltMod($strUsrCreacion);
                $objDetalleSolCarac->setFeCreacion(new \DateTime('now'));
                $objDetalleSolCarac->setFeUltMod(new \DateTime('now'));
                $emComercial->persist($objDetalleSolCarac);

                //Se guarda el historial de la solicitud por cambio de vendedor masivo
                $objDetalleSolHist = new InfoDetalleSolHist();
                $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolHist->setIpCreacion($strClientIp);
                $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHist->setUsrCreacion($strUsrCreacion);
                $objDetalleSolHist->setEstado('Pendiente');
                $objDetalleSolHist->setObservacion($strObservacion);
                $emComercial->persist($objDetalleSolHist);

                //Se guarda los id clientes que cambiarían de vendedor
                $objInfoLog = new InfoLog();
                $objInfoLog->setEmpresaCod($strEmpresaId);
                $objInfoLog->setTipoLog(0);
                $objInfoLog->setOrigenLog('TELCOS');

                //Id de la solicitud
                $objInfoLog->setAplicacion($objDetalleSolicitud->getId());

                //IdPersonaEmpresaRol Vendedor Origen
                $objInfoLog->setClase($intIdPersonaEmpresaRolOrigen);

                //IdPersonaEmpresaRol Vendedor Destino
                $objInfoLog->setMetodo($intIdPersonaEmpresaRolDestino);

                //Estado Solicitud: Pendiente, Aprobado, Rechazado
                $objInfoLog->setEstado('Pendiente');

                //Id del tipo de solicitud
                $objInfoLog->setAccion($objCambioVendedorSolicitud->getId());

                //IdPersonaEmpresaRol concatenados de clientes a reasignar
                $objInfoLog->setDescripcion($strIdClientes);
                $objInfoLog->setUsrCreacion($strUsrCreacion);
                $objInfoLog->setUsrUltMod($strUsrCreacion);
                $objInfoLog->setFeCreacion(new \DateTime('now'));
                $objInfoLog->setFeUltMod(new \DateTime('now'));
                $emGeneral->persist($objInfoLog);

                $emComercial->flush();
                $emGeneral->flush();

                $emComercial->getConnection()->commit();
                $emGeneral->getConnection()->commit();

                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
            }
            else
            {
                $strVendedorNombres = '';
                $objVendedor = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                    ->find($intIdPersonaEmpresaRolOrigen);

                if(is_object($objVendedor))
                {
                    $strVendedorNombres = ucwords(strtolower(trim($objVendedor->getPersonaId()->__toString())));
                }

                $objReturnResponse->setStrMessageStatus('Existen solicitudes pendientes de cambio masivo
                del vendedor<b> ' . $strVendedorNombres . '</b>.<br/><br/>No se podrá proceder mientras dichas solicitudes
                no sean autorizadas y/o rechazadas por el Gerente Comercial.');
            }
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        }
        catch(\Exception $objException)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' ' . $objException->getMessage());
            $serviceUtil->insertError('Telcos+',
                __METHOD__,
                $objReturnResponse->getStrMessageStatus(),
                $strUsrCreacion,
                $strClientIp);
            $emComercial->getConnection()->rollback();
        }

        $emComercial->getConnection()->close();
        $emGeneral->getConnection()->close();


        $objResponse->setData($objReturnResponse);

        return $objResponse;
    }
}
