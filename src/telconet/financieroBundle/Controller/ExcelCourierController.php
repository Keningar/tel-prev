<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use \PHPExcel_Cell;

class ExcelCourierController extends Controller
{
    /**
     * Documentación para el método 'indexAction'.
     *
     * Permite carga la interfaz grafica de la opcion
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 11-08-2014
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        $session=$request->getSession();
        $prefijoEmpresa = $session->get('prefijoEmpresa'); 
        
        if($prefijoEmpresa!="MD")
            $parametro['mensaje']="Acceso no Permitido, su rol no permite la accion";
            
        $parametro['fechaCancelar']=date('Y-m-d');
        
        return $this->render('financieroBundle:ExcelCourier:listarFacturasImprimir.html.twig', $parametro);
    }
    
    /**
     * Documentación para el método 'getNumeracionAction'.
     *
     * Permite obtener las numeraciones asociadas a las empresas
     *
     * @return respuesta Json con el listado de numeraciones
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 11-08-2014
     */
    public function getNumeracionAction()
    {
        $request = $this->getRequest();
        $session=$request->getSession();
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        //Obtengo las numeracion por empresa
        $codEmpresa = $session->get('idEmpresa');                   
        $emComercial = $this->getDoctrine()->getManager();
        $facturas = $emComercial->getRepository('schemaBundle:AdmiNumeracion')->findNumeracionXEmpresaYTipo($codEmpresa,"FACT");
        if ($facturas) 
        {
            $facturasArray = array();

            foreach ($facturas as $factura)
            {
                $array[] = array(
                    'id_numeracion' => $factura->getId(),
                    'numeracion'=>$factura->getNumeracionUno()."-".$factura->getNumeracionDos()
                );
            }
            $data = json_encode($array);
        }
     
        $resultado= '{"total":"0","encontrados":'.$data.'}';
        $respuesta->setContent($resultado);
        return $respuesta;
    }
    
    /**
     * Documentación para el método 'getCodigoTipoDocumentoAction'.
     *
     * Permite obtener los tipos de documentos requeridos para el proceso FAC y FACP
     *
     * @return respuesta Json con el listado de tipos de documentos
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 11-08-2014
     */
    public function getCodigoTipoDocumentoAction()
    {
        $request = $this->getRequest();
        $session=$request->getSession();
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        //Obtengo las numeracion por empresa
        $codEmpresa = $session->get('idEmpresa');           
        $emComercial = $this->getDoctrine()->getManager();
        $tipo1 = $emComercial->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')->findOneByCodigoTipoDocumento('FAC');
        $tipo2 = $emComercial->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')->findOneByCodigoTipoDocumento('FACP');
        $array[] = array(
            'codigoTipoDocumento' => $tipo1->getCodigoTipoDocumento(),
            'descripcion'=>$tipo1->getNombreTipoDocumento()
        );
        
        $array[] = array(
            'codigoTipoDocumento' => $tipo2->getCodigoTipoDocumento(),
            'descripcion'=>$tipo2->getNombreTipoDocumento()
        );
        
        $data = json_encode($array);
        $resultado= '{"total":"0","encontrados":'.$data.'}';
        $respuesta->setContent($resultado);
        return $respuesta;
    }
    
    /**
     * Documentación para el método 'getProcesarAction'.
     *
     * Permite hacer la llamada al script de Gestion de Scripts, para que el mismo invoque al script que genera el archivo
     * de excel para el courier.
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 11-08-2014
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 01-08-2016 - Se modifica a que el método llame al JAR de TelcosComunicaciónScript
     */
    public function getProcesarAction()
    {
        set_time_limit(0);
        $respuesta = new Response();
        $request = $this->getRequest();
        
        $fechaDesde=$request->get("fechaDesde");
        $fechaHasta=$request->get("fechaHasta");
        $cmbNumeracion=$request->get("cmbNumeracion");
        $cmbTipos=$request->get("cmbTipos");
        $nuno=$request->get("nuno");
        $ndos=$request->get("ndos");
        
        if($fechaDesde!="")
        {
            $fechaDesde_o=(explode('T',$fechaDesde));
            $fechaDesde=$fechaDesde_o[0];
        }
        else
            $fechaDesde="";
            
        if($fechaHasta!="")
        {
            $fechaHasta_o=(explode('T',$fechaHasta));
            $fechaHasta=$fechaHasta_o[0];
        }
        else
            $fechaHasta="";
            
        if($nuno=="")
            $nuno="-";
            
        if($ndos=="")
            $ndos="-";
            
        if($cmbTipos=="")
            $cmbTipos="";
    
        
        //Variables para la llamada del script
        $path_telcos    = $this->container->getParameter('path_telcos');
        $host_scripts   = $this->container->getParameter('host_scripts');
        $strPathJava = $this->container->getParameter('path_java_soporte');
        $strScriptPathJava = $this->container->getParameter('path_script_java_soporte');
        
        //Declara script que genera el reporte en excel para el courier
        $strScript = '/home/scripts-telcos/md/financiero/sources/generacion-excel-courier-md/dist/archivoImpresionMD.jar';
        
        //Se declaran los parametros del script
        $strNombreArchivo = "archivoCourierMD".date("Y-m-dH:i:s",time()).".xls";
        $strNombreArchivo = str_replace(':','-',$strNombreArchivo);
        
        $strParametros = $host_scripts."|".$fechaDesde."|".$fechaHasta."|".$cmbNumeracion."|".$cmbTipos."|".$strNombreArchivo."|".$nuno."|".$ndos;

        //Se declara el comando que se encarga de ejecutar el script
        $strComando = "nohup ".$strPathJava." -Xmx1500m -Xmx1500m -DentityExpansionLimit=1000000 -jar -Djava.security.egd=file:/dev/./urandom "
                      .$path_telcos."telcos/app/Resources/scripts/TelcosComunicacionScripts.jar '".$strScript."' '".$strParametros."' 'NO' '"
                      .$host_scripts."' '".$strScriptPathJava."' >> ".$path_telcos."telcos/app/Resources/scripts/log/log.txt &";
        
        $salida = shell_exec($strComando);
        
        $response = new Response();
        $response->setContent($strNombreArchivo);
        $response->headers->set('Content-type', 'text/plain');
        
        return $response;
    }
}
