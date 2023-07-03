<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\schemaBundle\Entity\InfoPagoAutomaticoCab;
use telconet\schemaBundle\Entity\InfoPagoAutomaticoDet;
use telconet\schemaBundle\Entity\InfoPagoDet;
use telconet\comercialBundle\Service\InfoContratoDigitalService;
use telconet\financieroBundle\Service\InfoPagoService;
use telconet\financieroBundle\Service\InfoPagoDetService;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Entity\InfoPagoHistorial;
use telconet\schemaBundle\Entity\InfoPagoAutomaticoHist;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use \PHPExcel_Cell;
use DOMDocument;

/**
 * TarjetasAbu controller.
 *
 */
class TarjetasAbuController extends Controller implements TokenAuthenticatedController
{

     /**
     * @Secure(roles="ROLE_483-1")
     * 
     * cargarArchivoAbuAction
     * Función que renderiza la página principal para cargar el archivo de tarjetas Abu mediante un archivo excel.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 12-09-2022    
     * 
     * @return render
     */
    public function cargarArchivoAbuAction()
    {        
        $objRequest       = $this->getRequest();
        $objSession       = $objRequest->getSession();
        $strCodEmpresa    = $objSession->get('idEmpresa');
        $strUsrSession    = $objSession->get('user');
        $emComercial      = $this->getDoctrine()->getManager("telconet");
        $emGeneral        = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtil      = $this->get('schema.Util');
        $objInfoPersona   = $emComercial->getRepository("schemaBundle:InfoPersona")
                                        ->findOneByLogin($strUsrSession);
        /*Se obtiene mensajes de validaciones*/
        $arrayMsjSinArchivo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->getOne('PARAM_TARJETAS_ABU',
                                                  'COMERCIAL','','MENSAJE_OBS_TARJETAS_ABU','COD_SIN_ARCHIVO','',
                                                  '','','',$strCodEmpresa);
                                   
        $strMsjSinArchivo = (isset($arrayMsjSinArchivo["valor2"])
                            && !empty($arrayMsjSinArchivo["valor2"])) ? $arrayMsjSinArchivo["valor2"]
                            : 'No hay archivo que procesar';
        
        $arrayMsjErrorExt = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->getOne('PARAM_TARJETAS_ABU',
                                               'COMERCIAL','','MENSAJE_OBS_TARJETAS_ABU','COD_ERROR_EXT','',
                                               '','','',$strCodEmpresa);
                                   
        $strMsjErrorExt = (isset($arrayMsjErrorExt["valor2"])
                            && !empty($arrayMsjErrorExt["valor2"])) ? $arrayMsjErrorExt["valor2"]
                            : 'Archivo no cumple con la extension xlsx';
        
        $arrayListadoExtension = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->get("PARAM_TARJETAS_ABU", "COMERCIAL", "",
                                                 "EXTENSION_ARCHIVO_ABU", "", "", "", "", "",
                                                 $strCodEmpresa);
        $arrayExtension = array();
        
        foreach($arrayListadoExtension as $objExtension)
        {
            $arrayExtension[] = $objExtension['valor2'];
        }
        
        if(is_object($objInfoPersona) )
        {
            $arrayParametroMail                   = array();
            $arrayParametroMail['strLogin']       = $objInfoPersona->getLogin();           
            $arrayParametroMail['objUtilService'] = $serviceUtil;
            $strDestinatario = $emComercial->getRepository("schemaBundle:InfoPersona")
                                           ->getDestinatarioNaf($arrayParametroMail);
        }               
        return $this->render('financieroBundle:tarjetasAbu:cargarArchivoAbu.html.twig', array('strDestinatario'  => $strDestinatario,
                                                                                              'strMsjSinArchivo' => $strMsjSinArchivo,
                                                                                              'strMsjErrorExt'   => $strMsjErrorExt,  
                                                                                              'arrayExtension'   => $arrayExtension));
    }
    
    /**
     * @Secure(roles="ROLE_483-1")
     * 
     * procesarArchivoAction     
     * Metodo encargado de procesar el archivo Abu en formato excel.
     *    
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 16-09-2022
     * 
     * @return json con resultado del proceso          
     */ 
    public function procesarArchivoAction()
    {
        $objRequest          = $this->getRequest();        
        $objSession          = $objRequest->getSession();
        $strUser             = $objSession->get('user');
        $strCodEmpresa       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa   = $objSession->get('prefijoEmpresa');                      
        $arrayInfoFile       = $_FILES['archivo_abu'];
        $strArchivo          = $arrayInfoFile["name"];   
        $strDestinatario     = $objRequest->get('destinatario');                  
        $objRespuesta        = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/html');
        $emGeneral           = $this->getDoctrine()->getManager('telconet_general');
        
        try
        {
            
            $arrayMsjSinArchivo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('PARAM_TARJETAS_ABU',
                                                     'COMERCIAL','','MENSAJE_OBS_TARJETAS_ABU','COD_SIN_ARCHIVO','',
                                                     '','','',$strCodEmpresa);
                                   
            $strMsjSinArchivo = (isset($arrayMsjSinArchivo["valor2"])
                                 && !empty($arrayMsjSinArchivo["valor2"])) ? $arrayMsjSinArchivo["valor2"]
                                 : 'No hay archivo que procesar';
        
            $arrayMsjErrorExt = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                          ->getOne('PARAM_TARJETAS_ABU',
                                                   'COMERCIAL','','MENSAJE_OBS_TARJETAS_ABU','COD_ERROR_EXT','',
                                                   '','','',$strCodEmpresa);
                                   
            $strMsjErrorExt = (isset($arrayMsjErrorExt["valor2"])
                                && !empty($arrayMsjErrorExt["valor2"])) ? $arrayMsjErrorExt["valor2"]
                                : 'Archivo no cumple con la extension xlsx';
        
            $arrayListadoExtension = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                   ->get("PARAM_TARJETAS_ABU", "COMERCIAL", "",
                                                 "EXTENSION_ARCHIVO_ABU", "", "", "", "", "",
                                                 $strCodEmpresa);                              
            $arrayExtension = array();
        
            foreach($arrayListadoExtension as $objExtension)
            {
                $arrayExtension[] = $objExtension['valor2'];
            }
           
            if($arrayInfoFile && count($arrayInfoFile) > 0)
            {
                $arrayArchivo     = explode('.', $strArchivo);
                $intCountArray    = count($arrayArchivo);
                $strNombreArchivo = $arrayArchivo[0];
                $strExtArchivo    = $arrayArchivo[$intCountArray - 1];
                $strPrefijo       = substr(md5(uniqid(rand())), 0, 6);                
                
                $boolExtension = in_array($strExtArchivo, $arrayExtension);
                
                if(!$boolExtension)
                {
                    throw new \Exception($strMsjErrorExt);     
                }
                                          
                $strNuevoNombre = $strNombreArchivo.'_'. $strPrefijo . "." . $strExtArchivo;

                $arrayParametros                           = array();                                
                $arrayParametros['strNuevoNombre']         = $strNuevoNombre;                
                $arrayParametros['strUser']                = $strUser;
                $arrayParametros['strCodEmpresa']          = $strCodEmpresa;
                $arrayParametros['strPrefijoEmpresa']      = $strPrefijoEmpresa;
                $arrayParametros['strClientIp']            = $objRequest->getClientIp();
                $arrayParametros['strDestinatario']        = $strDestinatario;

                $strResultado = $this->isSubirArchivoAbuNfs($arrayParametros);                                    
            }
            else
            {
                throw new \Exception($strMsjSinArchivo); 
            }
                         
            $objRespuesta->setContent($strResultado);
            
            return $objRespuesta;
        }
        catch(\Exception $e)
        {
            $strResultado = 'Error al procesar el archivo. '.$e->getMessage();
            $objRespuesta->setContent($strResultado);
            
            return $objRespuesta;
        }
    }
    
     /**
     * @Secure(roles="ROLE_483-1")
     * 
     * isSubirArchivoAbuNfs
     * Metodo encargado de subir el archivo al NFS y crear el proceso masivo para la ejecucion del archivo de tarjetas ABU.
     *    
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 19-09-2022
     *              
     * @return boolean         
     */ 
    public function isSubirArchivoAbuNfs($arrayParametros)
    {        
        $strNuevoNombre        = $arrayParametros['strNuevoNombre'];               
        $strUser               = $arrayParametros['strUser'];       
        $strCodEmpresa         = $arrayParametros['strCodEmpresa'];
        $strPrefijoEmpresa     = $arrayParametros['strPrefijoEmpresa'];        
        $strClientIp           = $arrayParametros['strClientIp']; 
        $strDestinatario       = $arrayParametros['strDestinatario'];        
        $strInputFile          = $_FILES['archivo_abu']['tmp_name'];        
        $emGeneral             = $this->getDoctrine()->getManager('telconet_general');        
        $emFinanciero          = $this->getDoctrine()->getManager("telconet_financiero");
        $objServiceUtil        = $this->get('schema.Util');
        $strApp                = '';
        $strSubModulo          = '';
        
        $arrayMotivoActualizaAbu = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->getOne('PARAM_TARJETAS_ABU',
                                                      'COMERCIAL','','MOTIVO_ACTUALIZACION_ABU','',
                                                      '','','','', $strCodEmpresa);
        
        $strMotivoActualizaAbu = (isset($arrayMotivoActualizaAbu["valor1"])
                                 && !empty($arrayMotivoActualizaAbu["valor1"])) ? $arrayMotivoActualizaAbu["valor1"]
                                 : 'Actualización Automática ABU';
        
        $objMotivo = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo($strMotivoActualizaAbu);
        $intIdMotivo = null;
        if( $objMotivo )
        {
            $intIdMotivo = $objMotivo->getId();
        }
                    
        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                        ->findOneBy(array('nombreParametro' => 'PARAM_TARJETAS_ABU', 
                                                          'estado'          => 'Activo'));
        if(is_object($objAdmiParametroCab))
        {              
            $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                               'descripcion' => 'CONFIGURACION NFS',
                                                               'empresaCod'  => $strCodEmpresa,
                                                               'estado'      => 'Activo'));
            if(is_object($objAdmiParametroDet))
            {
                $strPathAdicional  = $objAdmiParametroDet->getValor1();
                $strApp            = $objAdmiParametroDet->getValor2();
                $strSubModulo      = $objAdmiParametroDet->getValor3();                           

            }
            else
            {
                throw new \Exception('Error, no existe la configuración requerida para PATH ADICIONAL ');
            }                          
        }        

        $strData = file_get_contents( $strInputFile );
        $arrayPathAdicional[]   = array('key' => $strPathAdicional);
        
        $arrayParamNfs = array(
            'prefijoEmpresa'       => $strPrefijoEmpresa,
            'strApp'               => $strApp,
            'strSubModulo'         => $strSubModulo,
            'arrayPathAdicional'   => $arrayPathAdicional,
            'strBase64'            =>  base64_encode($strData),
            'strNombreArchivo'     => $strNuevoNombre,
            'strUsrCreacion'       => $strUser);
        $arrayResponseNfs = $objServiceUtil->guardarArchivosNfs($arrayParamNfs);

        if($arrayResponseNfs['intStatus']=='500')
        {
            throw new \Exception($arrayResponseNfs['strMensaje']);    
        }
        // Ruta donde se almacena el archivo de tarjetas Abu
        $strTargetPath = $arrayResponseNfs['strUrlArchivo'];      
        
        $emFinanciero->getConnection()->beginTransaction();
        try
        {
            if($arrayResponseNfs['intStatus']==200)
            {              
                $arrayParamPmaAbu                     = array();
                $arrayParamPmaAbu['strUrlFile']       = $strTargetPath;
                $arrayParamPmaAbu['intIdMotivo']      = $intIdMotivo;
                $arrayParamPmaAbu['strObservacion']   = $strMotivoActualizaAbu;
                $arrayParamPmaAbu['strUsrCreacion']   = $strUser;
                $arrayParamPmaAbu['strCodEmpresa']    = $strCodEmpresa;
                $arrayParamPmaAbu['strIpCreacion']    = $strClientIp;
                $arrayParamPmaAbu['strTipoPma']       = 'ArchivoTarjetasAbu';
                $arrayParamPmaAbu['strDestinatario']  = $strDestinatario;
                $strResultado = $emFinanciero->getRepository('schemaBundle:InfoProcesoMasivoCab')->guardarProcesoMasivo($arrayParamPmaAbu);
            }
            else
            {
                $strResultado  = 'No se puede almacenar archivo, verifique configuracion';
            }              
            
            $emFinanciero->getConnection()->commit();
            $emFinanciero->getConnection()->close();   
                            
        } 
        catch (Exception $ex) 
        {
            $strResultado  = 'Error al procesar archivo Abu.'; 
            if($emFinanciero->getConnection()->isTransactionActive())
            {
                $emFinanciero->getConnection()->rollback();
                $emFinanciero->getConnection()->close(); 
            }  
            $objServiceUtil->insertError('Telcos+',
                                         'TarjetasAbuController.isSubirArchivoAbuNfs',
                                         'Error TarjetasAbuController.isSubirArchivoAbuNfs: No se pudo ejecutar el proceso - '
                                          .$ex->getMessage(),
                                          $strUser,
                                          $strClientIp);
           
        }
        $strResultado  = 'OK';
        return $strResultado;        
    }
}
