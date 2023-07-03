<?php

namespace telconet\financieroBundle\Service;

use \PHPExcel;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Cell;
use \PHPExcel_IOFactory;
use \PHPExcel_Settings;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_Fill;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;

use telconet\schemaBundle\Entity\AdmiFormaPago;
use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Form\InfoRecaudacionType;
use telconet\schemaBundle\Entity\InfoRecaudacion;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\Event;
use telconet\schemaBundle\Entity\AdmiBancoCtaContable;
use Symfony\Component\Console\Output\OutputInterface;
use telconet\schemaBundle\DependencyInjection\BatchTransaction;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Response;

class InfoRecaudacionService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emcom;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emfinan;
    /**
     * @var \telconet\schemaBundle\Service\ValidatorService
     */
    private $validator;
    /**
     * @var \telconet\financieroBundle\Service\InfoPagoService
     */
    private $serviceInfoPago;
    /**
     * @var \telconet\tecnicoBundle\Service\ProcesoMasivoService
     */
    private $serviceProcesoMasivo;
    /**
     * @var \telconet\financieroBundle\Service\InfoRecaudacionDetService
     */
    private $serviceInfoRecaudacionDet;    
    /**
     * @var string
     */
    private $home;
    /**
     * @var \telconet\schemaBundle\Service\MailerService
     */
    private $mailer;
    
    /**
     * @var \telconet\schemaBundle\Service\UtilService
     */
    private $util;    

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emGeneral;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emcom = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emfinan = $container->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->validator = $container->get('schema.Validator');
        $this->serviceInfoPago = $container->get('financiero.InfoPago');
        $this->serviceProcesoMasivo = $container->get('tecnico.ProcesoMasivo');
        $this->serviceInfoRecaudacionDet = $container->get('financiero.InfoRecaudacionDet');        
        $this->home = ($container->hasParameter('home_dir') ? $container->getParameter('home_dir') : '/home');
        $this->mailer = $container->get('schema.Mailer');
        $this->util = $container->get('schema.Util');
        $this->emGeneral = $container->get('doctrine.orm.telconet_general_entity_manager');
    }
    
    public function obtenerEstadosRecaudacion()
    {
        $arreglo = array(
                        array('idEstado' => 'Activo', 'codigo' => 'ACT', 'descripcion' => 'Activo'),
                        array('idEstado' => 'Procesando', 'codigo' => 'ACT', 'descripcion' => 'Procesando'),
                        array('idEstado' => 'Pendiente', 'codigo' => 'ACT', 'descripcion' => 'Pendiente'),
                        array('idEstado' => 'Inactivo', 'codigo' => 'ACT', 'descripcion' => 'Inactivo'),
                        array('idEstado' => 'Error', 'codigo' => 'ACT', 'descripcion' => 'Error'),
                        array('idEstado' => 'Anulado', 'codigo' => 'ACT', 'descripcion' => 'Anulado'),
        );
        return $arreglo;
    }
    
    /**
     * Guarda el archivo excel de recaudacion, crea la recaudacion en estado Pendiente,
     * de modo que aun no se procesa el archivo, pero si queda almacenado en el servidor
     * @param string $empresaCod
     * @param integer $oficinaId
     * @param string $usrCreacion
     * @param string $ipCreacion
     * @param array $datos_form_files
     * @return \telconet\schemaBundle\Entity\InfoRecaudacion
     */
    
    /**
     * Documentación para el método 'guardarArchivoRecaudacion'.
     * 
     * Guarda el archivo excel de recaudacion, crea la recaudacion en estado Pendiente,
     * de modo que aun no se procesa el archivo, pero si queda almacenado en el servidor.
     *
     * @param array $arrayParametros [ 'strEmpresaCod'         => 'Código de la empresa en sesión',
     *                                 'intOficinaId'          => 'Id de la oficina en sesión',
     *                                 'strUsrCreacion'        => 'Usuario de creación',
     *                                 'strIpCreacion'         => 'Ip del host',
     *                                 'strNombreArchivo'      => 'Nombre derl archivo',
     *                                 'strEstado'             => 'Estado de la recaudación',
     *                                 'intCanalRecaudacionId' => 'Id del canal de recaudación' ]
     * 
     * @return \telconet\schemaBundle\Entity\InfoRecaudacion  $objRecaudacion
     * 
     * @author  telcos
     * @version 1.0 Versión Inicial
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 15-11-2017 Se realiza cambio para que función reciba arreglo de parámetros, se omite validación al iniciar recaudación .
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.1 10-05-2021 Se realiza cambio para consumir el nuevo NFS.
     *
     */    
    public function guardarArchivoRecaudacion($arrayParametros)
    {
        if(isset($arrayParametros['intRecaudacionId']) && !empty($arrayParametros['intRecaudacionId']))
        {
            $objRecaudacion = $this->emfinan->getRepository('schemaBundle:InfoRecaudacion')->find($arrayParametros['intRecaudacionId']);
        }
        else
        {
            $objRecaudacion = new InfoRecaudacion(); 
            
            $objRecaudacion->setFeCreacion(new \DateTime('now'));
            $objRecaudacion->setEmpresaCod($arrayParametros['strEmpresaCod']);
            $objRecaudacion->setOficinaId($arrayParametros['intOficinaId']);
            $objRecaudacion->setUsrCreacion($arrayParametros['strUsrCreacion']);
            $objRecaudacion->setIpCreacion($arrayParametros['strIpCreacion']);      
            $objRecaudacion->setCanalRecaudacionId($arrayParametros['intCanalRecaudacionId']);            
        }
        
        $objRecaudacion->setFile($arrayParametros['strNombreArchivo']);
        if ($objRecaudacion->getFile())
        {
            $objRecaudacion->preUpload();
            if(' ' !== $objRecaudacion->getFile())
            {
                $objAdmiParametroCab = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                       ->findOneBy(array('nombreParametro' => 'PARAMETROS_RECAUDACION', 
                                                                         'estado'          => 'Activo'));
                if(is_object($objAdmiParametroCab))
                {              
                    $objAdmiParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                           ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                             'descripcion' => 'PATH ADICIONAL',
                                                                             'empresaCod'  => $arrayParametros['strEmpresaCod'],
                                                                             'estado'      => 'Activo'));
                    if(is_object($objAdmiParametroDet))
                    {
                        $strPathAdicional  = $objAdmiParametroDet->getValor1();

                    }
                    else
                    {
                        throw new \Exception('Error, no existe la configuración requerida para PATH ADICIONAL ');
                    }                          
                }
                $arrayPathAdicional[] = array('key' => $strPathAdicional);
                $strApp               = "TelcosWeb";
                $strSubModulo         = "Recaudacion";
                $strArchivo           = base64_encode(file_get_contents($objRecaudacion->getFile())); 
                $arrayParamNfs        = array('prefijoEmpresa'       => $arrayParametros['strPrefijoEmpresa'],
                                              'strApp'               => $strApp,
                                              'strSubModulo'         => $strSubModulo,
                                              'arrayPathAdicional'   => $arrayPathAdicional,
                                              'strBase64'            => $strArchivo,
                                              'strNombreArchivo'     => $objRecaudacion->getPath(),
                    'strUsrCreacion'       => $arrayParametros['strUsrCreacion']);

                $arrayRespuestaNfs    = $this->util->guardarArchivosNfs($arrayParamNfs);
                $objRecaudacion->setPath($arrayRespuestaNfs['strUrlArchivo']);
            }
        }
        if("TN" === $arrayParametros['strPrefijoEmpresa'] && ' ' !== $objRecaudacion->getFile())
        {
            $objContent    = file_get_contents($objRecaudacion->getAbsolutePath());
            $strPath = tempnam(sys_get_temp_dir(), basename($entityRecaudacion->getAbsolutePath()));
            file_put_contents($strPath, $objContent);
            
            $objReader = PHPExcel_IOFactory::createReaderForFile($strPath);

            $objXLS = $objReader->load($strPath);
            $objWorksheet = $objXLS->getSheet(0); 
            $strTituloHoja = trim(strtoupper($objWorksheet->getTitle()));
            if(0 !== strcmp($arrayParametros['strNombreCanalRecaudacion'], $strTituloHoja))
            {
                $objRecaudacion->removeUpload();
                return null;
            }            
        }
        
        
        $objRecaudacion->setArchivoEnvio($arrayParametros['strNombreArchivoEnvio']);       
        
        $objRecaudacion->setEstado($arrayParametros['strEstado']);
         
        if(' ' !== $objRecaudacion->getFile())
        {
            $this->validator->validateAndThrowException($objRecaudacion);
        }
        
        $this->emfinan->persist($objRecaudacion);
        $this->emfinan->flush();
        return $objRecaudacion;
    }
    
    /**
     * Procesa las recaudaciones en estado Pendiente.
     * Si hay alguna en estado Procesando, no inicia el proceso, pues eso indica que hay otro hilo procesando.
     * @return boolean|number
     */
    public function procesarRecaudacionesPendientes(OutputInterface $output)
    {
        $this->emcom->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->emfinan->getConnection()->getConfiguration()->setSQLLogger(null);
        $procesando = $this->emfinan->getRepository('schemaBundle:InfoRecaudacion')->countRecaudacionesProcesando();
        if ($procesando > 0)
        {
            // si hay recaudaciones en estado Procesando, no se debe iniciar otro procesamiento, para respetar el orden
            return 0;
        }
        $entityRecaudacion = null;
        $procesadas = 0;
        $arrayCanalRecaudacionEmpresa = array();
        do
        {
            $entityRecaudacion = $this->emfinan->getRepository('schemaBundle:InfoRecaudacion')->findSiguienteRecaudacionPendiente();
            if ($entityRecaudacion != null)
            {
                if (!isset($entityFormaPago))
                {
                    // obtener forma pago Recaudacion (REC)
                    $entityFormaPago = $this->emcom->getRepository('schemaBundle:AdmiFormaPago')->findOneByCodigoFormaPago('REC');
                }
                $empresaCod = $entityRecaudacion->getEmpresaCod();
                // canales recaudacion por empresa
                if (!isset($arrayCanalRecaudacionEmpresa[$empresaCod]))
                {
                    $listCanalRecaudacion = $this->emfinan->getRepository('schemaBundle:AdmiCanalRecaudacion')->findBy(
                            array('empresaCod' => $empresaCod, 'estadoCanalRecaudacion' => 'Activo'));
                    if (empty($listCanalRecaudacion))
                    {
                        // no hay canales para esta empresa
                        throw new \Exception('No existen Canales de Recaudación configurados para Empresa ' . $empresaCod);
                    }
                    $arrayCanalRecaudacion = array();
                    foreach ($listCanalRecaudacion as $entityCanalRecaudacion)
                    {
                        $arrayCanalRecaudacion[$entityCanalRecaudacion->getTituloHoja()] = $entityCanalRecaudacion;
                    }
                    $arrayCanalRecaudacionEmpresa[$empresaCod] = $arrayCanalRecaudacion;
                }
                try
                {
                    $this->procesarRecaudacion($entityRecaudacion, $entityFormaPago, $arrayCanalRecaudacionEmpresa[$empresaCod], $output);
                    $procesadas++;
                }
                catch (\Exception $e)
                {
                    $output->writeln($e->getMessage());
                    $output->writeln($e->getTraceAsString());
                }
            }
        }
        while ($entityRecaudacion != null);
        return $procesadas;
    }
    
    /**
    *
    * Documentacion para el método 'procesarRecaudacion'
    * 
    * @param InfoRecaudacion $entityRecaudacion
    * @param AdmiFormaPago $entityFormaPago
    * @param array $arrayCanalRecaudacion
    * @param OutputInterface $output
    * @throws \Exception
    * 
    * @version 1.0 Versión inicial
    * 
    * @author  Edgar Holguin <eholguin@telconet.ec>
    * @version 1.2  21-10-2016 Se incluye lectura de campo contrapartida para realizar búsqueda de cliente.
    *
    * @author  Edgar Holguin <eholguin@telconet.ec>
    * @version 1.3  25-10-2017 Se agrega envío de parámetro intIdCanalRecaudación para colsultar la respectiva característica.
    *  
    * @author  Hector Lozano <hlozano@telconet.ec>
    * @version 1.4  30-10-2018 Se actualiza el estado a Error, de la Tabla Info_Recaudacion cuando existe un error al procesar el archivo.
    *  
    * @author  José Candelario <jcandelario@telconet.ec>
    * @version 1.5  10-05-2021 Se agrega la opción de consumir el NSF para la subida de archivos.
     *
    */     
    private function procesarRecaudacion(InfoRecaudacion $entityRecaudacion, AdmiFormaPago $entityFormaPago, array $arrayCanalRecaudacion, OutputInterface $output)
    {
        // datos generales de la recaudacion
        $empresaCod = $entityRecaudacion->getEmpresaCod();
        $prefijoEmpresa = $this->emcom->getRepository('schemaBundle:InfoEmpresaGrupo')->getPrefijoByCodigo($empresaCod);
        $oficinaId = $entityRecaudacion->getOficinaId();
        $usrCreacion = $entityRecaudacion->getUsrCreacion();
        $clientIp = $entityRecaudacion->getIpCreacion();
        $correos = $this->mailer->obtenerCorreosPorLogin($usrCreacion);
        // recaudacion debe pasar a estado Procesando
        if ($entityRecaudacion->getEstado() !== 'Pendiente')
        {
            throw new \Exception("Recaudación {$entityRecaudacion->getId()} no tiene estado Pendiente");
        }
        $entityRecaudacion->setEstado('Procesando');
        $this->emfinan->persist($entityRecaudacion);
        $this->emfinan->flush();
        try
        {
            $output->writeln((new \DateTime('now'))->format('Y-m-d H:i:s') . ' Recaudacion:' . $entityRecaudacion->getId() . ' Archivo:' . $entityRecaudacion->getAbsolutePath());
            //$objPHPExcel = new PHPExcel();
            //$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            //$cacheSettings = array( ' memoryCacheSize ' => '1024MB');
            //PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $objContent    = file_get_contents($entityRecaudacion->getAbsolutePath());
            
            $strPath = tempnam(sys_get_temp_dir(), basename($entityRecaudacion->getAbsolutePath()));
            file_put_contents($strPath, $objContent);
            
            $objReader = PHPExcel_IOFactory::createReaderForFile($strPath);
            /* @var $objXLS PHPExcel */ 
            $objXLS = $objReader->load($strPath); 
            
            for ($numhoja = 0; $numhoja < $objXLS->getSheetCount(); $numhoja++)
            {
                // determinar de que banco es la hoja, obtener canal recaudacion por nombre banco
                $objWorksheet = $objXLS->getSheet($numhoja);
                $title = trim(strtoupper($objWorksheet->getTitle()));
                if (!isset($arrayCanalRecaudacion[$title]))
                {
                    // banco no soportado
                    continue;
                }
                $output->writeln((new \DateTime('now'))->format('Y-m-d H:i:s') . ' Recaudacion:' . $entityRecaudacion->getId() . ' Hoja:' . $title);
                /* @var $entityCanalRecaudacion \telconet\schemaBundle\Entity\AdmiCanalRecaudacion */
                $entityCanalRecaudacion = $arrayCanalRecaudacion[$title];
                // batch
                $batch = new BatchTransaction($entityCanalRecaudacion->getBatchSize(), array($this->emcom, $this->emfinan), $output);
                // una sola logica de barrido de la hoja actual
                $objWorksheet->setRightToLeft(false); // prevenir layout invertido right-to-left
                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                for ($i = $entityCanalRecaudacion->getFilaInicio(); $i <= $highestRow; $i++)
                {
                    if (!$objWorksheet->getCell($entityCanalRecaudacion->getColValidacion() . $i)->getValue())
                    {
                        // si no hay datos en la celda de validacion, ya no seguir leyendo la hoja
                        break;
                    }
                    $respuesta = null;
                    $valorPagado = $objWorksheet->getCell($entityCanalRecaudacion->getColValor() . $i)->getValue();
                    if ($valorPagado) 
                    {
                        // quitar separador de miles (coma)
                        if($prefijoEmpresa === "TN" && (strcmp($entityCanalRecaudacion->getNombreCanalRecaudacion(),'BANCO GUAYAQUIL') === 0))
                        {
                            // BCO GUAYAQUIL
                            $arrayParametros = array('strEmpresaCod'                => $empresaCod,
                                                     'intIdCanalRecaudacion'        => $entityCanalRecaudacion->getId(),
                                                     'strDescricionCaracteristica'  => 'VALOR_RECAUDACION',
                                                     'strEstadoCaracteristica'      => 'Activo');        

                            $objAdmiCanalRecaudacionCaract = $this->emfinan->getRepository('schemaBundle:AdmiCaracteristica')
                                                                           ->getCanalRecaudacionCaracteristica($arrayParametros);

                            if(is_object($objAdmiCanalRecaudacionCaract))
                            {
                                $strFormatoValorRecaudacion = $objAdmiCanalRecaudacionCaract->getValor();

                                if(strpos($strFormatoValorRecaudacion,'|'))
                                {                           
                                    $valorPagado = $this->util->getStringToFloat($strFormatoValorRecaudacion, $valorPagado,'|'); 
                                }
                            }                          
                        }
                        else
                        {
                            $valorPagado = str_replace(',', '', $valorPagado);
                        }                        
                        
                        $strContrapartidaCliente = "";
                        
                        // Si el caráter separador | no se encuentra en la columna COL IDENTIFICACION
                        if(strpos($entityCanalRecaudacion->getColIdentificacion(),'|')===false)
                        {
                            $identificacionCliente = trim($objWorksheet->getCell($entityCanalRecaudacion->getColIdentificacion() . $i)->getValue());
                        }
                        else
                        {
                            $arrayColumnasIdentificacion = explode('|',$entityCanalRecaudacion->getColIdentificacion());
                            
                            // Se obtiene obtiene valor de columna identificación
                            $identificacionCliente = $objWorksheet->getCell($arrayColumnasIdentificacion[0] . $i)->getValue();
                            
                            // Se obtiene obtiene valor de columna contrapartida 
                            $strContrapartidaCliente  = trim($objWorksheet->getCell($arrayColumnasIdentificacion[1] . $i)->getValue());
                            
                        }
                        
                        // validaciones y correcciones en identificacion
                        if ($entityCanalRecaudacion->getSepIdentificacion() !== null)
                        {
                            // quitar todo lo anterior al caracter separador y la identificacion
                            $charPos = strrpos($identificacionCliente, $entityCanalRecaudacion->getSepIdentificacion());
                            if ($charPos !== false)
                            {
                                $identificacionCliente = substr($identificacionCliente, $charPos + 1);
                            }
                        }                       

                        if(is_numeric(trim($identificacionCliente)))
                        {
                            if ($entityCanalRecaudacion->getPadIdentificacion() !== null)
                            {                                                    
                                // agregar cero si es necesario (Cedula 10 caracteres, RUC 13 caracteres)
                                switch(strlen($identificacionCliente))
                                {
                                    case 9:
                                    case 12:
                                        $identificacionCliente = $entityCanalRecaudacion->getPadIdentificacion() . $identificacionCliente;
                                        break;
                                }
                            }
                        }

                        if ($entityCanalRecaudacion->getRemIdentificacion() !== null)
                        {
                            // arreglar RUC que en realidad es Cedula con padding al inicio
                            if (strlen($identificacionCliente) == 13 && 
                                strpos($identificacionCliente, $entityCanalRecaudacion->getRemIdentificacion()) === 0)
                            {
                                $identificacionCliente = substr($identificacionCliente, strlen($entityCanalRecaudacion->getRemIdentificacion()));
                            }
                        }
                        
                        if('N/A' !== $entityCanalRecaudacion->getColFecha())
                        {
                            $fechaObtenida = $objWorksheet->getCell($entityCanalRecaudacion->getColFecha() . $i)->getValue();
                        }
                        
                         
                        if($prefijoEmpresa === "TN" && (strcmp($entityCanalRecaudacion->getNombreCanalRecaudacion(),'BANCO GUAYAQUIL') === 0) )
                        {
                            $arrayParametros = array('strEmpresaCod'                => $empresaCod,
                                                     'intIdCanalRecaudacion'        => $entityCanalRecaudacion->getId(),
                                                     'strDescricionCaracteristica'  => 'FECHA_RECAUDACION',
                                                     'strEstadoCaracteristica'      => 'Activo');        

                            $objAdmiCanalRecaudacionCaract = $this->emfinan->getRepository('schemaBundle:AdmiCaracteristica')
                                                                           ->getCanalRecaudacionCaracteristica($arrayParametros);

                            if(is_object($objAdmiCanalRecaudacionCaract))
                            {
                                $strFormatoFechaProceso = $objAdmiCanalRecaudacionCaract->getValor();
                         
                                $fechaObtenida = $this->util->getStringFechaConFormato($strFormatoFechaProceso, $fechaObtenida);
                            }                          
                        }

                        if ('N/A'=== $entityCanalRecaudacion->getColFecha())
                        {
                            $fechaProceso = new \DateTime('now');
                        }                        
                        else if (strpos($fechaObtenida, '/') === false)
                        {
                            $timestamp = \PHPExcel_Shared_Date::ExcelToPHP($fechaObtenida);
                            $fechaProceso = new \DateTime(gmdate('d-m-Y', $timestamp));
                        }
                        else
                        {
                            $fechaProcesoArr = explode('/', $fechaObtenida);
                            $fechaProceso = new \DateTime($fechaProcesoArr[0] . '-' . $fechaProcesoArr[1] . '-' . $fechaProcesoArr[2]);
                        }
                                                                     
                        $numeroReferencia = $objWorksheet->getCell($entityCanalRecaudacion->getColReferencia() . $i)->getValue();
                        $nombreCliente = $objWorksheet->getCell($entityCanalRecaudacion->getColNombre() . $i)->getValue();
                        // iniciar transaccion                                              
                        $batch->beginTransaction();                                                                
                        //GRABA DETALLE RECAUDACION
                        $entityRecaudacionDet = $this->serviceInfoRecaudacionDet->grabaDetalleRecaudacion($empresaCod, 
                                                                                                      $identificacionCliente, 
                                                                                                      $nombreCliente, 
                                                                                                      $numeroReferencia, 
                                                                                                      $entityRecaudacion);                                                                         
                        // GRABA PAGO pero EN ESTE BANCO SI SE TIENE NUM FACTURA
                        $arrayParametros=array();
                        $arrayParametros['entityFormaPago']        = $entityFormaPago;
                        $arrayParametros['empresaCod']             = $empresaCod;
                        $arrayParametros['oficinaId']              = $oficinaId;
                        $arrayParametros['identificacionCliente']  = $identificacionCliente;
                        $arrayParametros['usrCreacion']            = $usrCreacion;
                        $arrayParametros['valorPagado']            = $valorPagado;
                        $arrayParametros['entityRecaudacion']      = $entityRecaudacion;
                        $arrayParametros['entityRecaudacionDet']   = $entityRecaudacionDet;
                        $arrayParametros['entityPagoLinea']        = null;                        
                        $arrayParametros['bancoTipoCuentaId']      = $entityCanalRecaudacion->getBancoTipoCuentaId();
                        $arrayParametros['numeroReferencia']       = $numeroReferencia;
                        $arrayParametros['origenPago']             = $entityCanalRecaudacion->getNombreCanalRecaudacion();
                        $arrayParametros['fechaProceso']           = $fechaProceso;
                        $arrayParametros['bancoCtaContableId']     = $entityCanalRecaudacion->getBancoCtaContableId();
                        $arrayParametros['entityPersona']          = null;
                        $arrayParametros['strContrapartidaCliente']= $strContrapartidaCliente;
                        $arrayParametros['intIdCanalRecaudacion']  = $entityCanalRecaudacion->getId();                        
                        
                        $respuesta = $this->serviceInfoPago->generarPagoAnticipoRecaudacion($arrayParametros);
                        // guardar y confirmar cambios parciales en la base
                        $batch->commitTransaction();
                        // hacer merge de los entities necesarios para otras transacciones
                        $entityRecaudacion = $this->emfinan->merge($entityRecaudacion);      
                        $entityRecaudacionDet = $this->emfinan->merge($entityRecaudacionDet);      
                    }
                    $color = null;
                    if (empty($respuesta))
                    {
                        // si no hubo respuesta o no hubo valor desde el principio
                        $respuesta = 'NO SE REGISTRO';
                        // resaltar fila con rojo
                        $color = 'FF0000';
                    }
                    else if (is_array($respuesta))
                    {
                        // si el pago ya fue registrado previamente
                        $respuesta = $respuesta['codigoTipoDocumento'] . ' EXISTENTE';
                        // resaltar fila con amarillo
                        $color = 'FFFF66';
                    }
                    else if ($respuesta === 'ANTS')
                    {
                        // si se genero un anticipo sin cliente
                        // resaltar fila con naranja
                        $color = 'FFCC00';
                    }
                    if (!empty($color))
                    {
                        // resaltar con color de fondo
                        $objWorksheet->getStyle('A' . $i . ':' . $entityCanalRecaudacion->getColRespuesta() . $i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color);
                    }
                    // escribir respuesta en ultima celda
                    $objWorksheet->getCell($entityCanalRecaudacion->getColRespuesta() . $i)->setValue($respuesta);
                    // log resultado
                    $output->writeln((new \DateTime('now'))->format('Y-m-d H:i:s') . ' Recaudacion:' . $entityRecaudacion->getId() . ' Fila:' . $i . ' Ident:' . $identificacionCliente . ' Valor:' . $valorPagado . ' Ref:' . $numeroReferencia . ' Resp:' . $respuesta);
                }
                // guardar y confirmar ultimos cambios en la base
                $batch->endTransaction();
                // hacer merge de los entities necesarios para otras transacciones
                $entityRecaudacion = $this->emfinan->merge($entityRecaudacion);
            }
            // recaudacion debe pasar a estado Activo
            if ($entityRecaudacion->getEstado() !== 'Procesando')
            {
                throw new \Exception("Recaudación {$entityRecaudacion->getId()} no tiene estado Procesando");
            }
            $entityRecaudacion->setEstado('Activo');
            $this->emfinan->persist($entityRecaudacion);
            $this->emfinan->flush();              

            // reactivar servicios
            try
            {
                $reactivacion = $this->serviceProcesoMasivo->reactivarServiciosPorRecaudacion($entityRecaudacion, $prefijoEmpresa);
            }
            catch (\Exception $e)
            {
                $reactivacion = array('isReactivado' => false, 'procesoMasivoId' => null);
            }
            $output->writeln((new \DateTime('now'))->format('Y-m-d H:i:s') . ' Recaudacion:' . $entityRecaudacion->getId() . ' Reactivacion:' . $reactivacion['isReactivado'] . '/' . $reactivacion['procesoMasivoId']);
            
            // enviar notificacion
            $correos[] = 'notificaciones_telcos@telconet.ec';
            $this->mailer->sendTwig('Recaudacion Procesada',
                    'notificaciones_telcos@telconet.ec',
                    $correos,
                    'financieroBundle:InfoPagoCab:recaudacionProcesada.html.twig',
                    array('recaudacion' => $entityRecaudacion, 'empresa' => $prefijoEmpresa, 'isReactivado' => $reactivacion['isReactivado'])
            );
            
            // FIXME: puede fallar por falta de memoria si el archivo tiene demasiadas celdas
            // CREA REPORTE DE LOS REGISTROS DE LOS PAGOS
            $objWriter = PHPExcel_IOFactory::createWriter($objXLS, 'Excel2007');
            $objWriter->save(sys_get_temp_dir().'/noencontrados_'.basename($entityRecaudacion->getAbsolutePath()));
            //$objWriter->save($this->home.'/telcos/web/public/uploads/recaudacion_pagos/noencontrados_'.$entityRecaudacion->getPath());
            $objAdmiParametroCab = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                   ->findOneBy(array('nombreParametro' => 'PARAMETROS_RECAUDACION', 
                                                                     'estado'          => 'Activo'));
            if(is_object($objAdmiParametroCab))
            {              
                $objAdmiParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                       ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                         'descripcion' => 'PATH ADICIONAL',
                                                                         'empresaCod'  => $empresaCod,
                                                                         'estado'      => 'Activo'));
                if(is_object($objAdmiParametroDet))
                {
                    $strPathAdicional  = $objAdmiParametroDet->getValor1();

                }
                else
                {
                    throw new \Exception('Error, no existe la configuración requerida para PATH ADICIONAL ');
                }                          
            }
            $arrayPathAdicional[] = array('key' => $strPathAdicional);
            $strApp               = "TelcosWeb";
            $strSubModulo         = "Recaudacion";
            $strArchivo           =  base64_encode(file_get_contents(sys_get_temp_dir().
                                     '/noencontrados_'.basename($entityRecaudacion->getAbsolutePath()))); 

            $strPrefijoEmpresa = $this->emcom->getRepository('schemaBundle:InfoEmpresaGrupo')->
                                                        getPrefijoByCodigo($entityRecaudacion->getEmpresaCod());

            $arrayParamNfs        = array('prefijoEmpresa'       => $strPrefijoEmpresa,
                                          'strApp'               => $strApp,
                                          'strSubModulo'         => $strSubModulo,
                                          'arrayPathAdicional'   => $arrayPathAdicional,
                                          'strBase64'            => $strArchivo, 
                                          'strNombreArchivo'     => "noencontrados_".basename($entityRecaudacion->getAbsolutePath()),
                                          'strUsrCreacion'       => $usrCreacion);

            $arrayRespuestaNfs    = $this->util->guardarArchivosNfs($arrayParamNfs);
            $entityRecaudacion->setPathNoEncontrados($arrayRespuestaNfs['strUrlArchivo']);
            $this->emfinan->persist($entityRecaudacion);
            $this->emfinan->flush();
            
//             $this->get('session')->getFlashBag()->add('exito', $mensaje);
//             return $this->redirect($this->generateUrl('inforecaudacion_list_pagos_recaudacion', array('idRec' => $idRecaudacion)));
            //return $this->redirect($this->generateUrl('inforecaudacion'));
            
            return;
        }catch(\Exception $e){
            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
            }
            $this->emcom->getConnection()->close();
            if ($this->emfinan->getConnection()->isTransactionActive())
            {
                $this->emfinan->getConnection()->rollback();
            }
            $this->emfinan->getConnection()->close();        
            
            // recaudacion debe pasar a estado Error si estaba Procesando, para poder revisar mas tarde
            if ($entityRecaudacion->getEstado() === 'Procesando')
            {
                if (!$this->emfinan->isOpen())
                {
                    $this->emfinan = $this->emfinan->create($this->emfinan->getConnection(),$this->emfinan->getConfiguration());
                }
                
                $entityRecaudacionReset = $this->emfinan->getRepository('schemaBundle:InfoRecaudacion')->find($entityRecaudacion->getId());
                $entityRecaudacionReset->setEstado('Error');
                $this->emfinan->persist($entityRecaudacionReset);
                $this->emfinan->flush();
                
                $this->emfinan->getConnection()->close(); 
            }
  
//             $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
//             return $this->redirect($this->generateUrl('inforecaudacion'));
            throw $e;
        }   
    }  
        
}
