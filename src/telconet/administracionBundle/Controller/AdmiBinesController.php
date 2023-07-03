<?php

namespace telconet\administracionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\AdmiBines;
use telconet\schemaBundle\Form\AdmiBinesType;
use JMS\SecurityExtraBundle\Annotation\Secure;

use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use \PHPExcel_Cell_DataType;

/**
 * Documentación para la clase 'AdmiBines'.
 *
 * Clase utilizada para manejar metodos que permiten realizar la administración de bines del módulo financiero.
 *
 * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
 * @version 1.0 03-08-2015
 */
class AdmiBinesController extends Controller
{
    /**
     *  @Secure(roles="ROLE_294-1")
     * 
     * Documentación para el método 'indexAction'.
     * 
     * Método inicial que consulta la lista de bines
     * 
     * @return Response retorna la renderización de lista de bines
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 03-08-.2015
     */
    public function indexAction()
    {
        //MODULO 294 - ADMI_BINES
        if(true === $this->get('security.context')->isGranted('ROLE_294-1'))
        {
            $rolesPermitidos[] = 'ROLE_294-1'; //INDEX BINES
        }
        if(true === $this->get('security.context')->isGranted('ROLE_294-2797'))
        {
            $rolesPermitidos[] = 'ROLE_294-2797'; //CREAR BIN
        }
        if(true === $this->get('security.context')->isGranted('ROLE_294-2798'))
        {
            $rolesPermitidos[] = 'ROLE_294-2798'; //ELIMINAR BIN
        }
        if(true === $this->get('security.context')->isGranted('ROLE_294-2857'))
        {
            $rolesPermitidos[] = 'ROLE_294-2857'; //EXPORTAR CLIENTES BIN
        }
        return $this->render('administracionBundle:AdmiBines:index.html.twig', array('rolesPermitidos' => $rolesPermitidos));
    }

    /**
     * @Secure(roles="ROLE_294-2797")
     * 
     * Documentación para el método 'createAction'.
     * 
     * Método que ejecuta la acción de crear un nuevo BIN
     * 
     * @param Request $request
     * 
     * @return Response retorna el resultado de la operación
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 03-08-.2015
     */
    public function createAction(Request $request)
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');

        $request = $this->getRequest();

        $strBinNuevo = $request->get("strBinNuevo");
        $strDescripcion = $request->get("strDescripcion");

        $intTipoCuentaId = $request->get("intTipoCuentaId");
        $strTipoCuentaDescripcion = $request->get("strTipoCuentaDescripcion");

        $intBancoiTpoCuentaId = $request->get("intBancoTipoCuentaId");
        $strBancoDescripcion = $request->get("strBancoDescripcion");

        /************************************/
        /* INICIO BLOQUE DE VALIDACIONES    */
        /************************************/
        $boolOk = true;
        $strMsg = '';

        if(!isset($strBinNuevo) || $strBinNuevo == '')
        {
            $boolOk = false;
            $strMsg = 'Debe ingresar un código de BIN nuevo';
            return $respuesta->setContent(json_encode(array('estatus' => $boolOk, 'msg' => $strMsg, 'id' => '0')));
        }

        if(!isset($strDescripcion) || $strDescripcion == '')
        {
            $boolOk = false;
            $strMsg = 'Debe especificar una descripción para el nuevo BIN';
        }
        else if(!isset($intTipoCuentaId) || !isset($strTipoCuentaDescripcion) || $strTipoCuentaDescripcion == 'Escoja una opción')
        {
            $boolOk = false;
            $strMsg = 'Debe seleccionar un tipo de Cuenta';
        }
        else if(!isset($intBancoiTpoCuentaId) || !isset($strBancoDescripcion) || $strBancoDescripcion == 'Seleccione...')
        {
            $boolOk = false;
            $strMsg = 'Debe seleccionar un Banco';
        }

        if(!$boolOk)
        {
            return $respuesta->setContent(json_encode(array('estatus' => $boolOk, 'msg' => $strMsg, 'id' => '0')));
        }
        $arrayEstados= array('Activo');
        $entityAdmiBin = $this->getDoctrine()->getManager('telconet_general')
                                            ->getRepository('schemaBundle:AdmiBines')
                                            ->getBinByCodigo($strBinNuevo, $arrayEstados);
        if(isset($entityAdmiBin))
        {
            return $respuesta->setContent(json_encode(array('estatus' => false,
                                                           'msg'     => 'El BIN ['.$entityAdmiBin->getBinNuevo().'] ya existe.','id' => '0')));
        }

        /************************************/
        /* FIN BLOQUE DE VALIDACIONES    */
        /************************************/
        $entity = new AdmiBines();
        $entity->setBinNuevo($strBinNuevo);
        $entity->setBancoTipoCuentaId($intBancoiTpoCuentaId);
        $entity->setDescripcion($strDescripcion);
        $entity->setBanco($strBancoDescripcion);
        $entity->setTarjeta($strTipoCuentaDescripcion);
        $entity->setEstado('Activo');
        $entity->setUsrCreacion($request->getSession()->get('user'));
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setIpCreacion('127.0.0.1');
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em->persist($entity);
        $em->flush();
        return $respuesta->setContent(json_encode(array('estatus' => true, 'msg' => 'Guardado satisfactoriamente', 'id' => $entity->getId())));
    }

    /**
     * @Secure(roles="ROLE_294-2797")
     * 
     * Documentación para el método 'newAction'.
     * 
     * Renderiza a la vista de creación de un nuevo BIN
     * 
     * @return render
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 03-08-.2015
     *
     * @author : Andrés Montero <amontero@telconet.ec>
     * @version 1.1 10-07-2017
     * Se envia id de pais de la empresa en sesión por parametros al crear formulario de formas de pago con AdmiBinesType 
     */
    public function newAction()
    {
        $request   = $this->getRequest();
        $intIdPais = $request->getSession()->get('intIdPais');
        $objEntity = new AdmiBines();
        $objForm   = $this->createForm(new AdmiBinesType(array("intIdPais"=>$intIdPais)), $objEntity);
        return $this->render('administracionBundle:AdmiBines:new.html.twig', array('entity' => $objEntity,'form' => $objForm->createView()));
    }

    /**
     * Documentación para el método 'showAction'.
     * 
     * Renderiza a la vista de visualización de la información de un BIN
     * 
     * @param mixed $id The entity id
     * 
     * @return render
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 03-08-.2015
     */
    public function showAction($id)
    {
        $entity = $this->getDoctrine()->getManager('telconet_general')->getRepository('schemaBundle:AdmiBines')->find($id);
        if(!$entity)
        {
            throw $this->createNotFoundException('Unable to find AdmiBines entity.');
        }
        return $this->render('administracionBundle:AdmiBines:show.html.twig', array('entity' => $entity));
    }

    /**
     * @Secure(roles="ROLE_294-2798")
     * 
     * Documentación para el método 'deleteAction'.
     * 
     * Método que ejecuta la acción de eliminación lógica de un BIN
     * 
     * @return Response retorna el resultado de la operación
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 03-08-.2015
     */
    public function deleteAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');

        $request = $this->get('request');
        $intIdBin = $request->get('int_IdBin_Ctrl_js');
        $strDescripcion = $request->get('str_descripcion_Ctrl_js');
        $intMotivoId = $request->get('int_motivoId_Ctrl_js');

        if(!isset($intMotivoId) || !isset($intMotivoId) || $intMotivoId == '')
        {
            $strMsg = 'Debe seleccionar un motivo de eliminación';
            return $respuesta->setContent(json_encode(array('estatus' => false, 'msg' => $strMsg, 'id' => '0')));
        }
        else if(!isset($strDescripcion) || $strDescripcion == '')
        {
            $strMsg = 'Debe especificar una descripción para el motivo de eliminación';
            return $respuesta->setContent(json_encode(array('estatus' => false, 'msg' => $strMsg, 'id' => '0')));
        }
        $em = $this->getDoctrine()->getManager('telconet_general');
        $entityBin = $em->getRepository('schemaBundle:AdmiBines')->find($intIdBin);
        if(!$entityBin)
        {
            throw $this->createNotFoundException('Unable to find AdmiBines entity.');
        }
        $entityBin->setEstado('Eliminado');
        $entityBin->setMotivoId($em->getRepository('schemaBundle:AdmiMotivo')->find($intMotivoId));
        $entityBin->setMotivoDescripcion($strDescripcion);
        $entityBin->setUsrUltMod($request->getSession()->get('user'));
        $entityBin->setFeUltMod(new \DateTime('now'));
        $em->persist($entityBin);
        $em->flush();
        return $respuesta->setContent(json_encode(array('estatus' => true,'msg' => 'Bin eliminado satisfactoriamente','id' => $entityBin->getId())));
    }

    /**
     * @Secure(roles="ROLE_294-2798")
     * 
     * Documentación para el método 'showAction'.
     * 
     * Renderiza a la vista de visualización de la información de un BIN
     * 
     * @param mixed $id The entity id
     * 
     * @return render
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 03-08-.2015
     */
    public function deleteViewAction($id)
    {
        $entity = $this->getDoctrine()->getManager('telconet_general')->getRepository('schemaBundle:AdmiBines')->find($id);
        if(!$entity)
        {
            throw $this->createNotFoundException('Unable to find AdmiBines entity.');
        }
        $form = $this->createForm(new AdmiBinesType(), $entity);
        return $this->render('administracionBundle:AdmiBines:delete.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

    /**
     * @Secure(roles="ROLE_294-1")
     * Documentación para el método 'getListaBinesAction'.
     * 
     * Consulta la lista de bines.
     * 
     * @return Response objeto JSON con la lista de bines y el total de registros
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 03-08-.2015
     */
    public function getListaBinesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
       
        $objRequest = $this->get('request');

        $intBin = $objRequest->get('bin');
        $strEstado = $objRequest->get("estado");

        $intLimite = $objRequest->get("limit");
        $intPagina = $objRequest->get("page");
        $intInicio = $objRequest->get("start");

        $arrayParametros['strEstado'] = $strEstado;
        $arrayParametros['strEstCFP'] = 'Activo';
        $arrayParametros['intBin'] = $intBin;

        $arrayParametros['intLimit'] = $intLimite;
        $arrayParametros['intPage'] = $intPagina;
        $arrayParametros['intStart'] = $intInicio;

        $arrayAdmiBines = $this->getDoctrine()->getManager("telconet_general")
                                             ->getRepository('schemaBundle:AdmiBines')
                                             ->getListaBines($arrayParametros);

        $intTotalRegistros = 0;
        $arrayStoreBines = "";
        $strMessageError = "";
        if(empty($arrayAdmiBines['strMensajeError']))
        {
            $arrayStoreBines = $arrayAdmiBines['arrayStoreBines'];
            $intTotalRegistros = $arrayAdmiBines['intTotalRegistros'];
        }
        else
        {
            $strMessageError = $arrayAdmiBines['strMensajeError'];
        }
        $objResponse = new Response(json_encode(array('json_totalbines' => $intTotalRegistros,
                                                      'json_listabines'  => $arrayStoreBines,
                                                      'strMensajeError'     => $strMessageError)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

     /**
     * @Secure(roles="ROLE_294-2797")
     * 
     * Documentación para el método 'getBancosAsociadosPorTipoCuentaAction'.
     * 
     * Consulta los bancos relacionados al tipo de cuenta elegido.
     * 
     * @return Response objeto JSON con la lista de Bancos.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 03-08-.2015
     * 
     * 
     * @author Andrés Montero<amontero@telconet.ec>
     * @version 1.1 29-06-2017
     * Se usa la nueva funcion para obtener bancos findBancosTipoCuentaPorCriterio
     */
    public function getBancosAsociadosPorTipoCuentaAction()
    {
        $request                          = $this->getRequest();
        $objSession                       = $request->getSession();
        $tipoCuenta                       = $request->get("tipoCuenta");
        $arrayParametros                  = array();
        $arrayParametros['strTipoCuenta'] = $tipoCuenta;
        $arrayParametros['arrayEstados']  = array('Activo','Activo-debitos');
        $arrayParametros['intPaisId']     = $objSession->get('intIdPais');
        $listAdmiBancos = $this->getDoctrine()->getManager("telconet")
                                              ->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                                              ->findBancosTipoCuentaPorCriterio($arrayParametros);
        if($listAdmiBancos)
        {
            $presentacion_div = "<option value=''>Seleccione...</option>";
            foreach($listAdmiBancos as $entityBanco)
            {
                $presentacion_div.="<option value='" . $entityBanco->getId() . "'>" .$entityBanco->getBancoId()->getDescripcionBanco(). "</option>";
            }
            $objResponse = new Response(json_encode(array('msg' => 'ok','div' => $presentacion_div)));
        }
        else
        {
            $objResponse = new Response(json_encode(array('msg' => 'No existen bancos asociados')));
        }
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
     /**
     * @Secure(roles="ROLE_294-2798")
     * 
     * Documentación para el método 'getMotivosAnulacionBinesAction'.
     * 
     * Consulta los motivos para la eliminación del BIN.
     * 
     * @return Response objeto JSON con la lista de motivos.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 03-08-.2015
     */
    public function getMotivosAnulacionBinesAction()
    {
        $em = $this->get('doctrine')->getManager('telconet');
        $listaAdmiMotivos = $em->getRepository('schemaBundle:AdmiMotivo')
                               ->findMotivosPorModuloPorItemMenuPorAccion('admi_bines', 'Bines', 'eliminarBin');
        if($listaAdmiMotivos)
        {
            $presentacion_div = "<option value=''>Seleccione...</option>";
            foreach($listaAdmiMotivos as $entityMotivo):
                $presentacion_div.="<option value='". $entityMotivo->getId() . "'>" . $entityMotivo->getNombreMotivo() . "</option>";
            endforeach;
        }
        $objResponse = new Response(json_encode(array('msg'=>'ok','div'=>$presentacion_div)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
        
    /**
     * @Secure(roles="ROLE_294-2857")
     * 
     * Documentación para el método 'exportarAsociadosAction'.
     * 
     * Consulta los clientes asociados y genera el reporte.
     * 
     * @return Documento Excel.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 24-08-2015
     */
    public function exportarAsociadosAction()
    {
        ini_set('max_execution_time', 3000000);
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $request = $this->get('request');
        $session  = $request->getSession();

        $emComercial = $this->getDoctrine()->getManager('telconet');

        $parametros = array();        
        
        $parametros["strBinNuevo"]    = $request->get('strBinNuevo')    ? $request->get('strBinNuevo')    : "";
        $parametros["strDescripcion"] = $request->get('strDescripcion') ? $request->get('strDescripcion') : "";
        $parametros["strBanco"]       = $request->get('strBanco')       ? $request->get('strBanco')       : "";
        $parametros["strTarjeta"]     = $request->get('strTarjeta')     ? $request->get('strTarjeta')     : "";
        $parametros["strEstado"]      = $request->get('strEstado')      ? $request->get('strEstado')      : "";
        $parametros["strServEstado"]  = 'Activo';
        
        $arrayAsociados = $emComercial->getRepository('schemaBundle:AdmiBines')->getClientesAsociados($parametros);
        $this->exportarContratosAsociadosBines($parametros, $arrayAsociados, $session->get('user'));
    }

   
    /**
     * @Secure(roles="ROLE_294-2857")
     * 
     * Documentación para el método 'exportarContratosAsociadosBines'
     *
     * Exportar las asociaciones de clientes-Bines a Excel.
     * 
     * @param array $parametros
     * @param array $asociados
     * @param string $usuario
     *          
     * @return Documento Excel
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 24-08-2015
     */
    private function exportarContratosAsociadosBines($parametros, $asociados , $usuario )
    {         	    
        error_reporting(E_ALL);
        ini_set('max_execution_time', 3000000);

        $objPHPExcel = new PHPExcel();

        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array(' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');

        $objPHPExcel = $objReader->load(__DIR__ . "/../Resources/templatesExcel/templateConsultaClientesPorBin.xls");

        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($usuario);
        $objPHPExcel->getProperties()->setTitle("Consulta de Contratos asociados");
        $objPHPExcel->getProperties()->setSubject("Consulta de Contratos asociados");
        $objPHPExcel->getProperties()->setDescription("Resultado de consulta de contratos asociados por código bin.");
        $objPHPExcel->getProperties()->setKeywords("Asociado");
        $objPHPExcel->getProperties()->setCategory("Reporte");

        $objPHPExcel->getActiveSheet()->setCellValue('C3', $usuario);
        $objPHPExcel->getActiveSheet()->setCellValue('C4', PHPExcel_Shared_Date::PHPToExcel(gmmktime(0, 0, 0, date('m'), date('d'), date('Y'))));
        $objPHPExcel->getActiveSheet()->getStyle('C4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

        $objPHPExcel->getActiveSheet()->setCellValue('B8', '' . $parametros['strBinNuevo']);
        $objPHPExcel->getActiveSheet()->setCellValue('B9', '' . $parametros['strDescripcion']);
        $objPHPExcel->getActiveSheet()->setCellValue('B10','' . $parametros['strTarjeta']);
        $objPHPExcel->getActiveSheet()->setCellValue('B11','' . $parametros['strBanco']);
        $objPHPExcel->getActiveSheet()->setCellValue('B12','' . $parametros['strEstado']);

        $i = 15;
        $type = PHPExcel_Cell_DataType::TYPE_STRING;
        foreach($asociados as $datos):
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $datos['login']);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $datos['idCliente'], $type);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $datos['apellidos']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $datos['nombres']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $datos['servicio']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $datos['nombreOficina']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $datos["telefonos"]);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $datos["direccion"]);
            $i = $i + 1;
        endforeach;
        
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //Redirect output to a clients web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="ClientesAsociados_' . date('d_M_Y') . '.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    
}