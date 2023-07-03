<?php

namespace telconet\comercialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\InfoReporteHistorico;

use \PHPExcel_IOFactory;

/**
 * Documentación para la clase 'TeleventasOutboundController'.
 *
 * Clase utilizada para consulta y generación de reportes televentas outbound.
 *
 * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
 * @version 1.0 03-10-2015
 */
class TeleventasOutboundController extends Controller
{ 
    /**
     *  @Secure(roles="ROLE_309-1")
     * 
     * Documentación para el método 'indexAction'.
     * 
     * Método inicial que consulta el reporte de televentas outbound.
     * 
     * @return Response retorna la renderización del reporte de televentas outbound.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 03-10-2015
     */
    public function indexAction()
    {
        if(true === $this->get('security.context')->isGranted('ROLE_309-1'))
        {
            $arregloRolesPermitidos[] = 'ROLE_309-1'; //INDEX TELEVENTAS OUTBOUND
        }
        if(true === $this->get('security.context')->isGranted('ROLE_309-3037'))
        {
            $arregloRolesPermitidos[] = 'ROLE_309-3037'; //CONSULTAR TELEVENTAS OUTBOUND
        }
        if(true === $this->get('security.context')->isGranted('ROLE_309-3038'))
        {
            $arregloRolesPermitidos[] = 'ROLE_309-3038'; //EXPORTAR TELEVENTAS OUTBOUND
        }
        
        $objSesion = $this->get('request')->getSession();
        
        $strPrefijoEmpresa = $objSesion->get('prefijoEmpresa');
        $intIdOficina      = $objSesion->get('idOficina');
        
        $arrayParametros   = array('oficinaId'  => $intIdOficina);
        $arrayOrdenamiento = array('feCreacion' => 'ASC');
        
        $objRepositorio    = $this->get('doctrine')->getManager('telconet')->getRepository('schemaBundle:AdmiJurisdiccion');
        
        $entityJurisdiccion = $objRepositorio->findOneBy($arrayParametros, $arrayOrdenamiento);
        $intIdJurisdiccion  = '';

        // Obtengo la Jurisdicción por defecto para las consultas.
        if($entityJurisdiccion)
        {
            $intIdJurisdiccion = $entityJurisdiccion->getId();
        }
        
        $objSesion->set('parametrosConsulta', null);
        
        return $this->render('comercialBundle:TeleventasOutbound:index.html.twig', array('rolesPermitidos' => $arregloRolesPermitidos,
                                                                                         'empresa'         => $strPrefijoEmpresa,
                                                                                         'jurisdiccion'    => $intIdJurisdiccion ));
    }

    /**
     * @Secure(roles="ROLE_309-3037")
     * 
     * Documentación para el método 'reporteTeleventasOutboundAction'.
     * 
     * Consulta el listado del reporte de televentas outbound.
     * 
     * @return Response JSON el listado del reporte de televentas outbound paginadas.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 03-10-2015
     */
    public function reporteTeleventasOutboundAction()
    {
        
        $arrayParametros   = $this->getParametrosTeleventasOutbound($this->get('request'));
        
        $this->get('request')->getSession()->set('parametrosConsulta', $arrayParametros);
        
        $strJsonFormaPago = $this->getDoctrine()->getManager()->getRepository('schemaBundle:InfoServicio')
                                                              ->getJsonTeleventasOutboundReport($arrayParametros);
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-type', 'text/json');
        $objRespuesta->setContent($strJsonFormaPago);
        
        return $objRespuesta;
    }

    /**
     * @Secure(roles="ROLE_309-3038")
     * 
     * Documentación para el método 'exportarReporteTeleventasOutboundAction'.
     * 
     * Genera el reporte de Televentas Outbound.
     * 
     * @return Documento Excel.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 03-10-2015
     */
    public function exportarReporteTeleventasOutboundAction()
    {
        $objManager        = $this->getDoctrine()->getManager('telconet');
        $objPeticion       = $this->get('request');
        $arrayParametros   = $objPeticion->getSession()->get('parametrosConsulta');
        $strUsuario        = $objPeticion->getSession()->get('user');
        $strCaracteristica = 'TELEVENTAS_OUTBOUND_REPORTE_HISTORICO';
        $strEstado         = 'Activo';
        
        $entityCaracteristica = $objManager->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, $strEstado);   
        if(!$entityCaracteristica)
        {
            throw new \Exception("No se ha definido la característica $strCaracteristica.");
        }
        
        $arrayResult = $this->getDoctrine()->getManager()->getRepository('schemaBundle:InfoServicio')
                                                         ->getResultadoExportarReporteTeleventasOutbound($arrayParametros);
        if(isset($arrayResult['ERROR']))
        {
            $this->get('session')->getFlashBag()->add('error', $arrayResult['ERROR']);
        }
        else
        {
            if(empty($arrayResult['REGISTROS']))
            {
                $this->get('session')->getFlashBag()->add('error', 'La consulta no generó ningún registro para exportar');
            }
            else
            {
                /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
                $serviceInfoServicio = $this->get('comercial.InfoServicio');
        
                $arrayResult = $serviceInfoServicio->exportarReporteTeleventasOutbound($arrayResult['REGISTROS'], $strUsuario);
                
                if(!isset($arrayResult['ERROR']))
                {
                    $objManager->getConnection()->beginTransaction();
                    try
                    {
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="TeleventasOutbound_' . date('dMY_His') . '.xls"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($arrayResult['PHPExcel'], 'Excel5');
                        $objWriter->save('php://output');

                        // Guardando le la historia de reportes generados

                        // Se remueven valores que no aportan información relevante para el log.
                        unset($arrayParametros['START']);
                        unset($arrayParametros['LIMIT']);

                        $strParametros = json_encode($arrayParametros);
                        // Se crear un nuevo registro histórico de exportación a excel del reporte de televentas.
                        $entityInfoReporteHistorico = new InfoReporteHistorico();
                        $entityInfoReporteHistorico->setCaracteristicaId($entityCaracteristica);
                        $entityInfoReporteHistorico->setParametros($strParametros);
                        $entityInfoReporteHistorico->setCantidadRegistros($arrayResult['INDICE']);
                        $entityInfoReporteHistorico->setEstado('Activo');
                        $entityInfoReporteHistorico->setFeCreacion(new \DateTime('now'));
                        $entityInfoReporteHistorico->setUsrCreacion($strUsuario);
                        $entityInfoReporteHistorico->setIpCreacion($objPeticion->getClientIp());
                        $objManager->persist($entityInfoReporteHistorico);
                        $objManager->flush();
                        
                        if($objManager->getConnection()->isTransactionActive())
                        {
                            $objManager->getConnection()->commit();
                            $objManager->getConnection()->close();
                        }
                        exit;
                    }
                    catch(\Exception $ex)
                    {
                        if($objManager->getConnection()->isTransactionActive())
                        {
                            $objManager->getConnection()->rollback();
                            $objManager->getConnection()->close();
                        }
                        $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
                    }
                }
                else
                {
                    $this->get('session')->getFlashBag()->add('error', $arrayResult['ERROR']);
                }
            }
        }
        return $this->redirect($this->generateUrl('televentasoutbound'));
    }
    
    /**
     * @Secure(roles="ROLE_309-1")
     * 
     * Documentación para el método 'getAjaxComboFormasPagoAction'.
     * 
     * Consulta las formas de pago para contrato activas.
     * 
     * @return Response Lista de formas de pago.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 03-10-2015
     */
    public function getAjaxComboFormasPagoAction()
    {
        $strJsonFormaPago = $this->getDoctrine()->getManager()->getRepository('schemaBundle:AdmiFormaPago')->generarJsonFormaPago('Activo', '', '');
        
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-type', 'text/json');
        $objRespuesta->setContent($strJsonFormaPago);
        
        return $objRespuesta;
    }

    /**
     * @Secure(roles="ROLE_309-1")
     * 
     * Documentación para el método 'getAjaxComboEmpresasAction'.
     * 
     * Consulta las empresas 'MD', 'TTCO', 'TN' para mostrar en el combo.
     * 
     * @return Response Lista de Empresas.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 03-10-2015
     */
    public function getAjaxComboEmpresasAction()
    {
        $arrayParametros['EMPRESASPREF'] = array('MD', 'TTCO', 'TN');
        
        $strJsonEmpresas = $this->getDoctrine()->getManager()->getRepository('schemaBundle:InfoEmpresaGrupo')
                                                             ->generarJsonEmpresasByPrefijo($arrayParametros);
        
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objRespuesta->setContent($strJsonEmpresas);
        
        return $objRespuesta;
    }
    
     /**
     * @Secure(roles="ROLE_309-1")
     * 
     * Documentación para el método 'getAjaxComboJurisdiccionesAction'.
     * 
     * Consulta todas las jurisdicciones activas por empresa en .
     * 
     * @return Response Lista de Empresas.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 03-10-2015
     */
    public function getAjaxComboJurisdiccionesAction()
    {
        $objRespuesta      = new Response();
        $strPrefijoEmpresa = $this->get('request')->get('empresa');
        // Obtengo la empresa basada en el prefijo
        $entityEmpresa     = $this->getDoctrine()->getManager()->getRepository('schemaBundle:InfoEmpresaGrupo')
                                                               ->findOneBy(array('prefijo' => $strPrefijoEmpresa));
        if($entityEmpresa)
        {
            $strJsonJurisdicciones = $this->getDoctrine()->getManager()->getRepository('schemaBundle:AdmiJurisdiccion')
                                                                       ->generarJsonJurisdiccionesPorEmpresa($entityEmpresa->getId());
            $objRespuesta->setContent($strJsonJurisdicciones);
        }
        
        $objRespuesta->headers->set('Content-Type', 'text/json');
        return $objRespuesta;
    }

    /**
     * @Secure(roles="ROLE_309-1")
     * 
     * Documentación para el método 'getParametrosTeleventasOutbound'.
     * 
     * obtiene el arreglo de parámetros para las consultas de televentas outbound.
     * 
     * @param Request $objPeticion
     * 
     * @param Array $arrayParametros['LIMIT']             Int   : Máximo de registros
     *                              ['START']             Int   : Inicio del listado
     *                              ['EMPRESA']           String: Código de la empresa
     *                              ['PLAN']              String: Descripción del plan
     *                              ['ESTADO']            String: Estado del registro
     *                              ['SECTOR']            String: Descripción del sector 
     *                              ['FORMA_CONTACTO_TT'] Array : códigos de teléfono
     *                              ['FORMA_CONTACTO_EM'] Array : código de correo electrónico
     *                              ['FORMA_CONTACTO_TF'] Array : código de teléfono fijo
     *                              ['FORMA_CONTACTO_TM'] Array : códigos de teléfonos móviles
     *                              ['ROLES']             Array : roles de cliente
     *                              ['SERVICIO']          String: estado del servicio 
     *                              ['JURISDICCION']      Int   : id de la jurisdicción 
     *                              ['DIRECCION']         String: Descripción de la dirección del cliente
     *                              ['FORMA_PAGO']        Int   : id de la forma de pago del contrato
     *                              ['NOMBRES']           String: Descripción de los nombres del cliente
     *                              ['APELLIDOS']         String: Descripción de los apellidos del cliente 
     *                              ['RAZON_SOCIAL']      String: Descripción de la razón social del cliente 
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 03-10-2015
     */
    private function getParametrosTeleventasOutbound($objPeticion)
    {
        $arrayParametros['START']    = $objPeticion->get('start');
        $arrayParametros['LIMIT']    = $objPeticion->get('limit');
        $arrayParametros['EMPRESA']  = $objPeticion->get('cbxEmpresa');
        $arrayParametros['PLAN']     = $objPeticion->get('txtPlan');
        $arrayParametros['ESTADO']   = 'Activo';
        $arrayParametros['SECTOR']   = $objPeticion->get('txtSector');
        $arrayParametros['SERVICIO'] = $objPeticion->get('cbxServicio');
        
        $arrayParametros['FORMA_CONTACTO_TT'] = array('TFIJ','MCLA', 'MMOV', 'MCNT');
        $arrayParametros['FORMA_CONTACTO_EM'] = array('MAIL');
        $arrayParametros['FORMA_CONTACTO_TF'] = 'TFIJ';
        $arrayParametros['FORMA_CONTACTO_TM'] = array('MCLA', 'MMOV', 'MCNT');
        $arrayParametros['ROLES']             = array('Cliente', 'Pre-cliente', 'Cliente Canal');
        
        $arrayParametros['JURISDICCION'] = $objPeticion->get('cbxJurisdiccion');
        $arrayParametros['DIRECCION']    = strtolower($objPeticion->get('txtDireccion'));
        $arrayParametros['FORMA_PAGO']   = $objPeticion->get('cbxFormasPago');
        $arrayParametros['NOMBRES']      = trim($objPeticion->get('txtNombres'));
        $arrayParametros['APELLIDOS']    = trim($objPeticion->get('txtApellidos'));
        $arrayParametros['RAZON_SOCIAL'] = trim($objPeticion->get('txtRazonSocial'));
        
        return $arrayParametros;
    }

}
    
