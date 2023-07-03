<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiAlias;
use telconet\schemaBundle\Entity\InfoEmpresaGrupo;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\InfoDocumento;

use telconet\schemaBundle\Form\AdmiAliasType;
use telconet\schemaBundle\Form\EmpresasType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use \PHPExcel_Cell;
use DOMDocument;

class ConfiguracionRutasBackboneController extends Controller implements TokenAuthenticatedController
{ 
    public function indexAction()
    {          
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();        
        $strLogin       = $objSession->get('user');        

        return $this->render('administracionBundle:ConfiguracionRutasBackbone:index.html.twig', array());
    }
    public function agregarElementoAction()
    {          
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();        
        $strLogin       = $objSession->get('user');        

        return $this->render('administracionBundle:ConfiguracionRutasBackbone:agregarElemento.html.twig', array());
    }
    public function cargarInformacionAction()
    {          
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();        
        $strLogin       = $objSession->get('user');        

        return $this->render('administracionBundle:ConfiguracionRutasBackbone:cargarInformacion.html.twig', array());
    }

    public function factibilidadAction()
    {          
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();        
        $strLogin       = $objSession->get('user');        

        return $this->render('administracionBundle:ConfiguracionRutasBackbone:factibilidad.html.twig', array());
    }

    public function agregarMangaAction()
    {   
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");       
        $objPeticion = $this->get('request');
        $intIdItrFin = $objPeticion->query->get('itrFin');   

        $objInterface   = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($intIdItrFin);

        $objInfoElemento    = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
        ->find($objInterface->getElementoId());

        if(is_object($objInfoElemento))
        {
            $intElementoId = $objInfoElemento->getId();
        }

        $objTramo  = $emInfraestructura->getRepository('schemaBundle:InfoTramo')
                        ->findOneBy(array('elementoBId' => $intElementoId));

        if(is_object($objTramo))
        {
            $objElementoRuta    = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                     ->findOneBy( array('id' => $objTramo->getRutaId(), 'estado' => 'Activo') );
            
            if(is_object($objElementoRuta))
            {
                $strNombreRuta = $objElementoRuta->getNombreElemento();
            }
        }

        return $this->render('administracionBundle:ConfiguracionRutasBackbone:agregarManga.html.twig', array(
            'nombreRuta'  => str_replace(" ", "_",$strNombreRuta),
            'idElemento'  => $objElementoRuta->getId()
        ));
    }
    public function exportarInformacionAction()
    {          
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();        
        $strLogin       = $objSession->get('user');        

        return $this->render('administracionBundle:ConfiguracionRutasBackbone:exportarInformacion.html.twig', array());
    }

    /**
     * showEnlaceAction
     * funcion que permite mostrar el objEnlace creado con su detalle
     * 
     * @author  Anthony Santillan <asantillany@telconet.ec>
     *      
     * @version 1.0 15-02-2023 Version Inicial
     * @return view
     */
    public function showEnlaceAction()
    {
        $objPeticion = $this->get('request');
        $intIdIni = $objPeticion->query->get('idIni');
        $intIdFin = $objPeticion->query->get('idFin');
        $intIdItrFin = $objPeticion->query->get('itrFin');

        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");

        if($intIdIni != '')
        {
            $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                ->find($intIdIni);

            $intOrden = 1;
            $arrayRespuestas =array();
        
            while($objEnlace !== null)
            {
    
                $objInterfaceInicio   = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                ->find($objEnlace->getInterfaceElementoIniId());
                $intElementoInicio = $objInterfaceInicio->getElementoId()->getId();
    
                $objElementoInicio = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                    ->findOneBy( array('id' => $intElementoInicio, 'estado' => 'Activo') );
                if(is_object($objElementoInicio))
                {
                    $strNombreElementoInicio = $objElementoInicio->getNombreElemento();
                }
    
                $objInterfaceFin      = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                ->find($objEnlace->getInterfaceElementoFinId());
    
                $intElementoFin       = $objInterfaceFin->getElementoId()->getId();
    
                $objElementoFinal = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                    ->findOneBy( array('id' => $intElementoFin, 'estado' => 'Activo') );
    
                if(is_object($objElementoFinal))
                {                        
                    $strNombreElementoFinal = $objElementoFinal->getNombreElemento();
                }
    
                $strHilo   = '-';
                $strColorHilo = '-';
    
                if($objEnlace->getBufferHiloId())
                {
                    $objBufferHilo = $emInfraestructura->getRepository("schemaBundle:InfoBufferHilo")->find($objEnlace->getBufferHiloId()->getId());
    
                    if($objBufferHilo)
                    {
                        $objHilo = $emInfraestructura->getRepository("schemaBundle:AdmiHilo")->find($objBufferHilo->getHiloId()->getId());
                        if($objHilo)
                        {
                            $intHiloId = $objHilo->getId();
                            $strHilo = $objHilo->getNumeroHilo();
                            $strColorHilo = $objHilo->getColorHilo();
                        }
                    }
                }
    
                $arrayRespuesta =array(
                                        'orden'             => $intOrden,
                                        'idEnlace'          => $objEnlace->getId(),
                                        'elementoInicioId'  => $intElementoInicio,
                                        'elementoFinId'     => $intElementoFin,
                                        'elementoInicio'    => $strNombreElementoInicio,
                                        'elementoFin'       => $strNombreElementoFinal,
                                        'interfaceInicioId' => $objInterfaceInicio->getId(),
                                        'interfaceFinId'    => $objInterfaceFin->getId(),
                                        'interfaceInicio'   => $objInterfaceInicio->getNombreInterfaceElemento(),
                                        'interfaceFin'      => $objInterfaceFin->getNombreInterfaceElemento(),
                                        'hilo'              => $strHilo,
                                        'color'             => $strColorHilo,
                                        );
    
                array_push($arrayRespuestas,$arrayRespuesta);

                if($intIdItrFin == $objInterfaceFin->getId())
                {
                    break;
                }
    
                $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                        ->findOneBy( array('interfaceElementoIniId' => $objEnlace->getInterfaceElementoFinId()));
                $intOrden++;
            }
            
            $objElementoInicio = $arrayRespuestas[0];
            $objElementoFinal  = $arrayRespuestas[count($arrayRespuestas)-1];
        }
        if($intIdFin != '')
        {
            $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                ->find($intIdFin);

            $intOrden = 1;
            $arrayRespuestas =array();
        
            while($objEnlace !== null)
            {
    
                $objInterfaceInicio   = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                ->find($objEnlace->getInterfaceElementoIniId());
                $intElementoInicio = $objInterfaceInicio->getElementoId()->getId();
    
                $objElementoInicio = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                    ->findOneBy( array('id' => $intElementoInicio, 'estado' => 'Activo') );
                if(is_object($objElementoInicio))
                {
                    $strNombreElementoInicio = $objElementoInicio->getNombreElemento();
                }
    
                $objInterfaceFin      = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                ->find($objEnlace->getInterfaceElementoFinId());
    
                $intElementoFin       = $objInterfaceFin->getElementoId()->getId();
    
                $objElementoFinal = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                    ->findOneBy( array('id' => $intElementoFin, 'estado' => 'Activo') );
    
                if(is_object($objElementoFinal))
                {                        
                    $strNombreElementoFinal = $objElementoFinal->getNombreElemento();
                }
    
                $strHilo   = '-';
                $strColorHilo = '-';
    
                if($objEnlace->getBufferHiloId())
                {
                    $objBufferHilo = $emInfraestructura->getRepository("schemaBundle:InfoBufferHilo")
                    ->find($objEnlace->getBufferHiloId()->getId());
    
                    if($objBufferHilo)
                    {
                        $objHilo = $emInfraestructura->getRepository("schemaBundle:AdmiHilo")
                        ->find($objBufferHilo->getHiloId()->getId());
                        if($objHilo)
                        {
                            $intHiloId = $objHilo->getId();
                            $strHilo = $objHilo->getNumeroHilo();
                            $strColorHilo = $objHilo->getColorHilo();
                        }
                    }
                }
    
                $arrayRespuesta =array(
                                        'orden'             => $intOrden,
                                        'idEnlace'          => $objEnlace->getId(),
                                        'elementoInicioId'  => $intElementoInicio,
                                        'elementoFinId'     => $intElementoFin,
                                        'elementoInicio'    => $strNombreElementoInicio,
                                        'elementoFin'       => $strNombreElementoFinal,
                                        'interfaceInicioId' => $objInterfaceInicio->getId(),
                                        'interfaceFinId'    => $objInterfaceFin->getId(),
                                        'interfaceInicio'   => $objInterfaceInicio->getNombreInterfaceElemento(),
                                        'interfaceFin'      => $objInterfaceFin->getNombreInterfaceElemento(),
                                        'hilo'              => $strHilo,
                                        'color'             => $strColorHilo,
                                        );
    
                array_push($arrayRespuestas,$arrayRespuesta);
    
                $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                        ->findOneBy( array('interfaceElementoFinId' => $objEnlace->getInterfaceElementoIniId()));


                if($intIdItrFin == $objInterfaceInicio->getId())
                {
                    break;
                }
                $intOrden++;
            }
            
            $objElementoInicio = $arrayRespuestas[0];
            $objElementoFinal  = $arrayRespuestas[count($arrayRespuestas)-1];

            return $this->render('administracionBundle:ConfiguracionRutasBackbone:equipo.html.twig', array(
                'elementoInicioId'  => $objElementoFinal['elementoInicioId'],
                'elementoFinId'     => $objElementoInicio['elementoFinId'],
                'elementoInicio'    => str_replace(" ", "_",$objElementoFinal['elementoInicio']),
                'elementoFin'       => str_replace(" ", "_",$objElementoInicio['elementoFin']),
                'interfaceInicioId' => $objElementoFinal['interfaceInicioId'],
                'interfaceFinId'    => $objElementoInicio['interfaceFinId'],
                'interfaceInicio'   => str_replace(" ", "_", $objElementoFinal['interfaceInicio']),
                'interfaceFin'      => str_replace(" ", "_", $objElementoInicio['interfaceFin']),
                'hiloId'            => $objElementoInicio['hiloId'],
                'hilo'              => $objElementoInicio['hilo'],
                'color'             => $objElementoInicio['color'],
                'idInicio'          => $intIdIni,
                'idFin'             => $intIdFin
         ));
        }
            
        return $this->render('administracionBundle:ConfiguracionRutasBackbone:equipo.html.twig', array(
                'elementoInicioId'  => $objElementoInicio['elementoInicioId'],
                'elementoFinId'     => $objElementoFinal['elementoFinId'],
                'elementoInicio'    => str_replace(" ", "_",$objElementoInicio['elementoInicio']),
                'elementoFin'       => str_replace(" ", "_",$objElementoFinal['elementoFin']),
                'interfaceInicioId' => $objElementoInicio['interfaceInicioId'],
                'interfaceFinId'    => $objElementoFinal['interfaceFinId'],
                'interfaceInicio'   => str_replace(" ", "_", $objElementoInicio['interfaceInicio']),
                'interfaceFin'      => str_replace(" ", "_", $objElementoFinal['interfaceFin']),
                'hiloId'            => $objElementoInicio['hiloId'],
                'hilo'              => $objElementoInicio['hilo'],
                'color'             => $objElementoInicio['color'],
                'idInicio'          => $intIdIni,
                'idFin'             => $intIdFin
        ));
    }

    /**
     * 
     * procesarArchivoAction     
     * Metodo encargado de procesar el archivo de hilos en formato excel.
     *    
     * @author Anthony Santillan <asantillany@telconet.ec>
     * @version 1.0 15-02-2023 Version Inicial
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
        $objRespuesta        = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/html');
        $emGeneral           = $this->getDoctrine()->getManager('telconet_general');
        
        try
        {
           
            if($arrayInfoFile && count($arrayInfoFile) > 0)
            {
                $arrayArchivo     = explode('.', $strArchivo);
                $intCountArray    = count($arrayArchivo);
                $strNombreArchivo = $arrayArchivo[0];
                $strExtArchivo    = $arrayArchivo[$intCountArray - 1];
                $strPrefijo       = substr(md5(uniqid(rand())), 0, 6);                                      
                $strNuevoNombre   = $strNombreArchivo.'_'. $strPrefijo . "." . $strExtArchivo;

                $arrayParametros                           = array();                                
                $arrayParametros['strNuevoNombre']         = $strNuevoNombre;                
                $arrayParametros['strUser']                = $strUser;
                $arrayParametros['strCodEmpresa']          = $strCodEmpresa;
                $arrayParametros['strPrefijoEmpresa']      = $strPrefijoEmpresa;
                $arrayParametros['strClientIp']            = $objRequest->getClientIp();
                $arrayParametros['strExtension']           = $strExtArchivo;

                $strResultado = $this->isSubirArchivoNfs($arrayParametros);                                    
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
     * 
     * isSubirArchivoNfs
     * Metodo encargado de subir el archivo al NFS y crear el proceso masivo
     *    
     * @author Anthony Santillan <asantillany@telconet.ec>
     * @version 1.0 15-02-2023 Version Inicial
     *              
     * @return boolean         
     */ 
    public function isSubirArchivoNfs($arrayParametros)
    {        
        $strNuevoNombre        = $arrayParametros['strNuevoNombre'];               
        $strUser               = $arrayParametros['strUser'];       
        $strCodEmpresa         = $arrayParametros['strCodEmpresa'];
        $strPrefijoEmpresa     = $arrayParametros['strPrefijoEmpresa'];        
        $strClientIp           = $arrayParametros['strClientIp']; 
        $strDestinatario       = $arrayParametros['strDestinatario']; 
        $strExtension          = $arrayParametros['strExtension'];        
        $strInputFile          = $_FILES['archivo_abu']['tmp_name'];        
        $emGeneral             = $this->getDoctrine()->getManager('telconet_general');        
        $emFinanciero          = $this->getDoctrine()->getManager("telconet_financiero");
        $emComunicacion        = $this->getDoctrine()->getManager('telconet_comunicacion'); 
        $objServiceUtil        = $this->get('schema.Util');
        $strApp                = '';
        $strSubModulo          = '';
        $objPeticion              = $this->get('request');
               
        $objAdmiParametroCab   = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                        ->findOneBy(array('nombreParametro' => 'AUTOMATIZACION DE CARGA DE HILOS', 
                                                          'estado'          => 'Activo'));

        // motivo de la carga del archivo
        $arrayMotivoCargaArchivoHilos = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->findOneBy(array('parametroId' => $objAdmiParametroCab->getId(),
                                                            'descripcion' => 'MOTIVO_ARCHIVO_HILOS',
                                                            'empresaCod'  => $strCodEmpresa,
                                                            'estado'      => 'Activo'));

        $strMotivoCargaArchivoHilos = $arrayMotivoCargaArchivoHilos->getValor1();
        
        $objMotivo   = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo($strMotivoCargaArchivoHilos);
        $intIdMotivo = null;
        if( $objMotivo )
        {
            $intIdMotivo = $objMotivo->getId();
        }
                         
        if(is_object($objAdmiParametroCab))
        {              
            $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                               'descripcion' => 'CONFIGURACION NFS',
                                                               'empresaCod'  => $strCodEmpresa,
                                                               'estado'      => 'Activo'));
            if(is_object($objAdmiParametroDet))
            {
    
                $strPathAdicional  = $objAdmiParametroDet->getValor1(); // 'configuiracionDeHilo',
                $strApp            = $objAdmiParametroDet->getValor2(); // 'TelcosWeb',
                $strSubModulo      = $objAdmiParametroDet->getValor3(); // 'ArchivoBackbone',                     

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
            'strBase64'            => base64_encode($strData),
            'strNombreArchivo'     => $strNuevoNombre,
            'strUsrCreacion'       => $strUser);
        // Guardar el Archivo en el Nfs
        $arrayResponseNfs = $objServiceUtil->guardarArchivosNfs($arrayParamNfs);

        if($arrayResponseNfs['intStatus']=='500')
        {
            throw new \Exception($arrayResponseNfs['strMensaje']);    
        }
        // Ruta donde se almacena el archivo de hiloos
        $strTargetPath = $arrayResponseNfs['strUrlArchivo'];      
        
        try
        {
            $emComunicacion->getConnection()->beginTransaction();
            if($arrayResponseNfs['intStatus']==200)
            {  
                $strMensaje = 'archivo de configuracion de hilos backbone';

                $objInfoDocumento = new InfoDocumento();
                $objInfoDocumento->setMensaje($strMensaje);
                $objInfoDocumento->setEstado('Activo');
                $objInfoDocumento->setNombreDocumento($arrayParametros['strNuevoNombre']);
                $objInfoDocumento->setUbicacionFisicaDocumento($strTargetPath);
                $objInfoDocumento->setUbicacionLogicaDocumento($arrayParametros['strNuevoNombre']);
                $objInfoDocumento->setFeCreacion(new \DateTime('now'));
                $objInfoDocumento->setUsrCreacion($strUser);
                $objInfoDocumento->setIpCreacion($objPeticion->getClientIp());
                $emComunicacion->persist($objInfoDocumento);
                $emComunicacion->flush();
            }
            else
            {
                $strResultado  = 'No se puede almacenar archivo, verifique configuracion';
            }     
            $emComunicacion->getConnection()->commit();         
            $emComunicacion->getConnection()->close(); 
            
            $objArchivo = $emComunicacion->getRepository('schemaBundle:InfoDocumento')
            ->findOneBy(array('nombreDocumento' => $arrayParametros['strNuevoNombre']));

            if(is_object($objArchivo))
            {
                $arrayParametros['intIdDocumento']   = $objArchivo->getId();
                $strUrlSubida = $this->getDescargaDocumentosHilos($arrayParametros);
            }
            if(!empty($strUrlSubida))
            {
                //llamar al procedimiento en la base para realizar la subida de hilos
                $arrayParamsSubidaCsv   = array(
                    "intIdDocumento"             => $arrayParametros['intIdDocumento'],
                    "strNombreArchivoHilo"       => $arrayParametros['strNuevoNombre'],
                    "strExtensionArchivoHilo"    => $strExtension,
                    "strUsrCreacion"             => $strUser
                    );

                $arrayRespuestaSubidaExcel    = $this->ejecutaSubidaCsvHilos($arrayParamsSubidaCsv);
                $strStatusSubidaExcel         = $arrayRespuestaSubidaExcel["status"];
                $strMensajeSubidaExcel        = $arrayRespuestaSubidaExcel["mensaje"];
                
                if($strStatusSubidaExcel !== "OK")
                {
                    $strResultadoJson = '{"success":false,"objRespuesta":"' . $strMensajeSubidaExcel .'"}';
                }
            }
        } 
        catch (Exception $ex) 
        {
            $strResultado  = 'Error al procesar archivo de hilos.'; 
            if($emComunicacion->getConnection()->isTransactionActive())
            {
                $emComunicacion->getConnection()->rollback();
                $emComunicacion->getConnection()->close();
            }
            $objServiceUtil->insertError('Telcos+',
                                         'ConfiguracionRutasBackboneController.isSubirArchivoNfs',
                                         'Error ConfiguracionRutasBackboneController.isSubirArchivoNfs: No se pudo ejecutar el proceso - '
                                          .$ex->getMessage(),
                                          $strUser,
                                          $strClientIp);
           
        }
        $strResultado  = $strMensajeSubidaExcel ;
        return $strResultado;        
    }

    /**
     * Documentación para el método 'getDescargaDocumentosHilos'.
     * Este metodo obtiene los documentos a partir de la url
     *
     * @author Anthony Santillan <asantillany@telconet.ec>
     * @version 1.0 15-02-2023 Version Inicial 
     * 
     */
    public function getDescargaDocumentosHilos($arrayParametros)
    {
        $intIdDocumento = $arrayParametros["intIdDocumento"];
        //Buscar el documento
        $emComunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");
        $objArchivo = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($intIdDocumento);
        if($objArchivo)
        {
            $strUrl = $objArchivo->getUbicacionFisicaDocumento();
            return $strUrl;
        }
        else
        {
            throw new \Exception('Archivo no encontrado');  
        }
    }

    /**
     * Función que sirve para ejecutar el procedimiento de Base de Datos que ejecuta la validación del archivo csv y creación de hilos 
     * masivas
     * 
     * @author Anthony Santillan <asantillany@telconet.ec>
     * @version 1.0 15-02-2023 Version Inicial 
     * 
     */
    public function ejecutaSubidaCsvHilos($arrayParametros)
    {
        $strDatabaseDsn             = $this->container->getParameter('database_dsn');
        $strUserInfraestructura     = $this->container->getParameter('user_infraestructura');
        $strPasswordInfraestructura = $this->container->getParameter('passwd_infraestructura');
        $strNombreArchivoHilo       = $arrayParametros["strNombreArchivoHilo"];
        $strExtensionArchivoHilo    = $arrayParametros["strExtensionArchivoHilo"];
        $strUsrCreacion             = $arrayParametros["strUsrCreacion"];
        $intIdDocumento             = $arrayParametros["intIdDocumento"];
        $strMuestraErrorUsuario     = 'NO';
        $strStatus                  = '';
        $strMensaje                 = '';

        try
        {
            if(!isset($strNombreArchivoHilo) || empty($strNombreArchivoHilo))
            {
                $strMuestraErrorUsuario = 'SI';
                throw new \Exception ('No se ha podido obtener el nombre del archivo subido');
            }
            
            if(!isset($strExtensionArchivoHilo) || empty($strExtensionArchivoHilo))
            {
                $strMuestraErrorUsuario = 'SI';
                throw new \Exception ('No se ha podido obtener la extensión del archivo subido');
            }
            
            if(!isset($strUsrCreacion) || empty($strUsrCreacion))
            {
                $strMuestraErrorUsuario = 'SI';
                throw new \Exception ('No se ha podido obtener el usuario en sesión');
            }
            
            //procedimiento para leer el excel en la base 
            $strSql         = " BEGIN DB_INFRAESTRUCTURA.INFRKG_RUTASBACKBONE.P_UPLOAD_CSV_HILOS(
                                    :Pn_IdArchivoCsvPsm,
                                    :Pv_NombreArchivoPsm,
                                    :Pv_ExtensionArchivoPsm,
                                    :Pv_UsrCreacion,
                                    :Pv_Status,
                                    :Pv_Mensaje); 
                                END;";
            $objConn = oci_connect($strUserInfraestructura, $strPasswordInfraestructura, $strDatabaseDsn);
            $objStmt = oci_parse($objConn, $strSql);
            
            oci_bind_by_name($objStmt, ':Pn_IdArchivoCsvPsm', $intIdDocumento);
            oci_bind_by_name($objStmt, ':Pv_NombreArchivoPsm', $strNombreArchivoHilo);
            oci_bind_by_name($objStmt, ':Pv_ExtensionArchivoPsm', $strExtensionArchivoHilo);
            oci_bind_by_name($objStmt, ':Pv_UsrCreacion', $strUsrCreacion);
            oci_bind_by_name($objStmt, ':Pv_Status', $strStatus, 5);
            oci_bind_by_name($objStmt, ':Pv_Mensaje', $strMensaje, 2000);
            oci_execute($objStmt);
        }
        catch (\Exception $e)
        {
            if($strMuestraErrorUsuario === "SI")
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "Ha ocurrido un problema al intentar ejecutar la creación de scopes/policy. Por favor comuníquese con Sistemas!";
            }
            $strStatus  = "ERROR";
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;

    }
    /**
     * Funcion que sirve para obtener las interfaces de un elemento por su nombre
     * @author Anthony Santillan <asantillany@telconet.ec>
     * @version 1.0 15-02-2023 Version Inicial
     *
     * @return json
     */
    public function buscarInterfacesPorElementoAction()
    {
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');

        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion = $this->get('request');
        
        $strNombreElemento = $objPeticion->get('nombreElemento');
        
        $objElemento     = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                    ->findOneBy( array('nombreElemento' => $strNombreElemento, 'estado' => 'Activo') );

        if(is_object($objElemento))
        {
            $intIdElemento = $objElemento->getId(); 
        }
        $intStart = $objPeticion->query->get('start');
        $intLimit = $objPeticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonInterfacesPorElemento($intIdElemento,"Todos",$intStart,$intLimit);
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }

     /**
     * Metodo utilizado para obtener información de los tramos
     * 
     * @param Object  $objResponse con informacion del elemento de acuerdo a la petición.
     * 
     * @author Anthony Santillan <asantillany@telconet.ec>        
     * @version 1.0 15-02-2023 Version Inicial
     * 
     */
    public function getTramosAction()
    {
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objPeticion        = $this->get('request'); 
        $arrayResultados     = array();
        $arrayResultado     = array();
        
        $strNombreElemento = $objPeticion->get('nombreElemento');
        
        $objElemento     = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                    ->findOneBy( array('nombreElemento' => $strNombreElemento, 'estado' => 'Activo') );

        if(is_object($objElemento))
        {
            $intIdElemento = $objElemento->getId(); 
        }

        $arrayParametros                     = array();
        $arrayParametros['intElemento']      = $intIdElemento;
        
        $arrayResultados = $this->getArrayTramosAction($arrayParametros);
        foreach($arrayResultados['encontrados'] as $resultado)
        {
            if($resultado['tipoElementoA'] == 'ODF')
            {
                $objElemento     = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                    ->findOneBy( array('nombreElemento' => $resultado['nombreElementoA'], 'estado' => 'Activo') );

                array_push($arrayResultado,array('nombreElemento' => $objElemento->getNombreElemento()));
            }
            if($resultado['tipoElementoB'] == 'SWITCH' || $resultado['tipoElementoB'] == 'OLT' 
            || $resultado['tipoElementoB'] == 'ROUTER' || $resultado['tipoElementoB'] == 'CPE')
            {
                $objElemento     = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                    ->findOneBy( array('nombreElemento' => $resultado['nombreElementoB'], 'estado' => 'Activo') );

                array_push($arrayResultado,array('nombreElemento' => $objElemento->getNombreElemento()));
                continue;
            }
            if($resultado['tipoElementoA'] == 'SWITCH' || $resultado['tipoElementoA'] == 'OLT' 
            || $resultado['tipoElementoA'] == 'ROUTER' || $resultado['tipoElementoA'] == 'CPE')
            {
                $objElemento     = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                    ->findOneBy( array('nombreElemento' => $resultado['nombreElementoA'], 'estado' => 'Activo') );

                array_push($arrayResultado,array('nombreElemento' => $objElemento->getNombreElemento()));
                continue;
            }
            $objElemento     = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                    ->findOneBy( array('nombreElemento' => $resultado['nombreElementoA'], 'estado' => 'Activo') );

            if(is_object($objElemento ))
            {
                $objInfoRelacionElemento  = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                ->findOneBy( array('elementoIdA' => $objElemento->getId(), 'estado' => 'Activo') );
            }
            if(is_object($objInfoRelacionElemento))
            {
                $objElementoContenido     = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                ->find($objInfoRelacionElemento->getElementoIdB());

                array_push($arrayResultado,array('nombreElemento' => $objElementoContenido->getNombreElemento()));
            }
        }
        $objResponse = new Response(json_encode(array('intTotal' => 1, 'data'  => $arrayResultado)));
        $objResponse->headers->set('Content-type', 'text/json'); 
        return $objResponse;
    }  


    public function getArrayTramosAction($arrayParametros)
    {
        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $strCodEmpresa         = $objSession->get('idEmpresa');
        $arrayResultado = array();
        
        $objTramoInicial= $this->getDoctrine()->getManager("telconet_infraestructura")
                               ->getRepository('schemaBundle:InfoTramo')
                               ->findOneBy(array('rutaId'    => $arrayParametros['intElemento'],
                                                 'estado'    => 'Activo',
                                                 'tipoTramo' => 'INICIAL'));  
        
        if(is_object($objTramoInicial))
        {
            $arrayParametros['intElementoInicial'] = $objTramoInicial->getElementoAId();

            $objElemento    = $this->getDoctrine()->getManager("telconet_infraestructura")
                                   ->getRepository('schemaBundle:InfoElemento')
                                   ->find($arrayParametros['intElemento']);
            if(is_object($objElemento))
            {
                $arrayParametros['strEstado']        = $objElemento->getEstado();
                $arrayParametros["strCodEmpresa"]    = $strCodEmpresa;

                $arrayResultado = $this->getDoctrine()->getManager("telconet_infraestructura")
                                       ->getRepository('schemaBundle:InfoElemento')
                                       ->getTramosPorRutas($arrayParametros);       
            }
        }

        return $arrayResultado;
    }    


    /**
     * buscarElementoPorNombreAction
     *
     * Funcion para busqueda de elementos por nombre
     *
     * @return $objJsonResponse JSON
     *
     * @author Anthony Santillan <asantillany@telconet.ec>
     * @version 1.0 15-02-2023 Version Inicial
     *
     */
    public function buscarElementoPorNombreAction()
    {
        
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objRespuesta       = new Response();
        $objPeticion        = $this->get('request');

        $objJson            = array();
        $arrayParametros                      = array();
        $arrayParametros['strNombreElemento'] = $objPeticion->get('nombreElemento');
        $arrayParametros['strEstadoElemento'] = $objPeticion->get('estado');
        $arrayParametros['intStart']          = $objPeticion->get('start');
        $arrayParametros['intLimit']          = $objPeticion->get('limit');
        
        $arrayRespuestas = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->getElementos($arrayParametros);

        foreach($arrayRespuestas as $arrayRespuesta)
        {
            array_push($objJson,$arrayRespuesta);
        }

        $objResponse = new Response(json_encode(array('intTotal' => 1, 'data'  => $objJson)));
        $objResponse->headers->set('Content-type', 'text/json');            
        return $objResponse;
    }

     /**
     * buscarTramoSiguienteAction
     *
     * Funcion para busqueda del tramo siguiente de un elemento de una ruta
     *
     * @author Anthony Santillan <asantillany@telconet.ec>
     * @version 1.0 15-02-2023 Version Inicial
     *
     */
    public function buscarTramoSiguienteAction()
    {
        $emInfraestructura                 = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objRespuesta       = new Response();
        $objRespuesta->headers->set('Content-type', 'text/json'); 
        $objPeticion        = $this->get('request');

        $objJson            = array();
        $arrayRespuesta    = array();
        $arrayRespuestas    = array();

        $intElementoPrevio        = $objPeticion->get('intElementoPrevio');
        $intElementoSiguiente     = $objPeticion->get('intElementoSiguiente');

        if($intElementoPrevio > 0)
        {
            $objInfoElemento    = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
            ->find($intElementoPrevio);
        }
        if($intElementoSiguiente > 0)
        {
            $objInfoElemento    = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
            ->find($intElementoSiguiente);
        }

        if(is_object($objInfoElemento))
        {
            if($intElementoPrevio > 0)
            {
                $objTramo  = $emInfraestructura->getRepository('schemaBundle:InfoTramo')
                        ->findOneBy(array('elementoBId' => $objInfoElemento->getId()));
            }
            if($intElementoSiguiente > 0)
            {
                $objTramo  = $emInfraestructura->getRepository('schemaBundle:InfoTramo')
                        ->findOneBy(array('elementoAId' => $objInfoElemento->getId()));
            }

            if(!empty($objTramo))
            {
                $intTotal = 0;
                $intTotal++;
                $strLogin ='';
                $strHilo ='';
                $strNombreElementoInicio ='';
                $strNombreElementoFinal ='';

                $objElementoIni   = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                    ->find($objTramo->getElementoAId());

                $objInfoRelacionElemento  = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                ->findOneBy( array('elementoIdA' => $objElementoIni->getId(), 'estado' => 'Activo') );
                                    
                $objJsonIni = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                ->findBy( array('elementoId' => $objInfoRelacionElemento->getElementoIdB()) );
                
                foreach($objJsonIni as $InterfaceInicio)
                {
                    if($intElementoPrevio !== '')
                    {
                        $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                        ->findOneBy( array('interfaceElementoFinId' => $InterfaceInicio->getId()));
                    }
                    if($intElementoSiguiente !== '')
                    {
                        $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                        ->findOneBy( array('interfaceElementoIniId' => $InterfaceInicio->getId()));
                    }
                   
                    if($objEnlace == '')
                    {
                        continue;
                    }

                    $objInterfaceInicio   = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                    ->find($objEnlace->getInterfaceElementoIniId());

                    $intElementoInicio    = $objInterfaceInicio->getElementoId()->getId();
        
                    $objElementoInicio = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                        ->findOneBy( array('id' => $intElementoInicio, 'estado' => 'Activo') );
                    if(is_object($objElementoInicio))
                    {
                        $strNombreElementoInicio = $objElementoInicio->getNombreElemento();

                        $objInfoRelacionElementoIni  = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                        ->findOneBy( array('elementoIdB' => $objElementoInicio->getId(), 'estado' => 'Activo') );

                       
                        
                        if(is_object($objInfoRelacionElementoIni))
                        {
                            $intIdElementoIni = $objInfoRelacionElementoIni->getElementoIdA();

                            $objInfoElementoIni = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                            ->findOneBy( array('id' => $intIdElementoIni, 'estado' => 'Activo') );

                        }
                        if(is_object($objInfoElementoIni))
                        {
                            $strNombreElementoIni = $objInfoElementoIni->getNombreElemento();
                        }
                    }
        
                    $objInterfaceFin     = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                    ->find($objEnlace->getInterfaceElementoFinId());
        
                    $intElementoFin   = $objInterfaceFin->getElementoId()->getId();
        
                    $objElementoFinal = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                        ->findOneBy( array('id' => $intElementoFin, 'estado' => 'Activo') );
        
                    if(is_object($objElementoFinal))
                    {                        
                        $strNombreElementoFinal = $objElementoFinal->getNombreElemento();

                        $objInfoRelacionElementoFin  = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                        ->findOneBy( array('elementoIdB' => $objElementoFinal->getId(), 'estado' => 'Activo') );
                        
                        if(is_object($objInfoRelacionElementoFin))
                        {
                            $intIdElemento = $objInfoRelacionElementoFin->getElementoIdA();

                            $objInfoElementoFin = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                            ->findOneBy( array('id' => $objInfoRelacionElementoFin->getElementoIdA(), 
                                                'estado' => 'Activo') );

                        }
                        if(is_object($objInfoElementoFin))
                        {
                            $strNombreElementoFin = $objInfoElementoFin->getNombreElemento();
                        }
                        
                    }
                        

                    if($objEnlace->getBufferHiloId())
                    {
                        $objBufferHilo = $emInfraestructura->getRepository("schemaBundle:InfoBufferHilo")
                        ->find($objEnlace->getBufferHiloId()->getId());
        
                        if($objBufferHilo)
                        {

                            $objHilo = $emInfraestructura->getRepository("schemaBundle:AdmiHilo")
                            ->find($objBufferHilo->getHiloId()->getId());
                            if($objHilo)
                            {
                                $intHiloId = $objHilo->getId();
                                $strHilo = $objHilo->getNumeroHilo();
                                $strColorHilo = $objHilo->getColorHilo();
                            }
                        }
                    }
                    $objInfoEnlaceServicio =  $emInfraestructura->getRepository("schemaBundle:InfoEnlaceServicioBackbone")
                                                    ->findOneBy(array('enlaceId' => $objEnlace->getId()));

                    if(is_object($objInfoEnlaceServicio))
                    {
                        $strLogin = $objInfoEnlaceServicio->getLoginAux();
                    }

                    if($strNombreElementoInicio != $strNombreElementoFinal)
                    {
                        $arrayRespuesta =array(
                            'mangaInicio'       => $strNombreElementoIni,
                            'mangaFin'          => $objInfoElementoFin->getNombreElemento(),
                            'elementoInicio'    => $strNombreElementoInicio,
                            'interfaceIni'      => $objInterfaceInicio->getNombreInterfaceElemento(),
                            'interfaceFin'      => $objInterfaceFin->getNombreInterfaceElemento(),
                            'elementoFin'       => $strNombreElementoFinal,
                            'hilo'              => $strHilo,
                            'login'             => $strLogin,
                            );
                    }
                    else
                    {
                        continue;
                    }
                   
                        array_push($arrayRespuestas,$arrayRespuesta);
                }
                array_push($objJson,$arrayRespuestas);
            }
        }

        $objResponse = new Response(json_encode(array('intTotal' => $intTotal, 'data'  => $objJson[0])));
        $objResponse->headers->set('Content-type', 'text/json');            
        return $objResponse;
    }

    /**
     * getTipoRutaAction
     *
     * Funcion que retorna los tipos de elementos Ruta
     *
     * @author Anthony Santillan <asantillany@telconet.ec>
     * @version 1.0 15-02-2023 Version Inicial
     *
     */
    public function getTipoRutaAction()
    {
        $objJsonResponse  = new Response();
        $objJsonResponse->headers->set('Content-Type', 'text/json');
        $arrayParametros = array();
        $arrayParametros["descripcion"] = "RUTA";
        $arrayParametros["estado"]      = "Activo";


        $arrayRespuestas = array();
        $arrayRespuesta = array();

        $emGeneral       = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');

        $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->findBy(array('descripcion' => 'Listado de rutas disponibles'));
        
        foreach($objAdmiParametroDet as $objTipoRuta)
        {
            $strTipoRuta = $objTipoRuta->getValor1();

            $objTipoElemento = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
            ->findOneBy(array( "nombreTipoElemento" => $strTipoRuta));
            
            if(is_object($objTipoElemento))
            {
                $strTipo = $objTipoElemento->getNombreTipoElemento();

                $arrayRespuesta = array('id' => $objTipoElemento->getId(),'tipoRuta' => $strTipo);
            }
            array_push($arrayRespuestas,$arrayRespuesta);
        }
        
        $objResponse = new Response(json_encode(array('intTotal' => 1, 'data'  => $arrayRespuestas)));
        $objResponse->headers->set('Content-type', 'text/json');            
        return $objResponse;
    }

    /**
     * getTipoRutaAction
     *
     * Funcion que actualiza los enlaces entre elementos
     *
     * @author Anthony Santillan <asantillany@telconet.ec>
     * @version 1.0 15-02-2023 Version Inicial
     *
     */
    public function actualizarHilosAction() 
    { 
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objRequest          = $this->get('request');
        $objPeticion         = $this->get('request');
        $objSession          = $objRequest->getSession();
        $strUser             = $objSession->get('user');
        $intElementoInicio   = $objPeticion->get('strElementoInicio');
        $intElementoFin      = $objPeticion->get('strElementoFin');
        $intElementoInsertar = $objPeticion->get('strElementoInsertar');
        $strStatus          = 200;

        $objElementoIni   = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                ->find($intElementoInicio);

        if(is_object($objElementoIni))
        {
            $strElementoInicio = $objElementoIni->getNombreElemento();
        }
        $objElementoFin   = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                ->find($intElementoFin);

        if(is_object($objElementoFin))
        {
            $strElementoFin = $objElementoFin->getNombreElemento();
        }
            
        $strElementoInsertar = $intElementoInsertar;

        try
        {
            $strQuery = "BEGIN DB_INFRAESTRUCTURA.INFRKG_RUTASBACKBONE.P_UPDATE_RUTAS(".
                        ":Pv_NombreElementoIni,".
                        ":Pv_NombreElementoFin,".
                        ":Pv_NombreElementoInsertar,".
                        ":Pv_UsrCreacion,".
                        ":Pv_Status,".
                        ":Pv_Mensaje);END;";

            $objStmt = $emInfraestructura->getConnection()->prepare($strQuery);
            $objStmt->bindParam('Pv_NombreElementoIni', $strElementoInicio);
            $objStmt->bindParam('Pv_NombreElementoFin', $strElementoFin);
            $objStmt->bindParam('Pv_NombreElementoInsertar', $strElementoInsertar);
            $objStmt->bindParam('Pv_UsrCreacion', $strUser);
            $objStmt->bindParam('Pv_Status', $strStatus);

            $strMsjResultado = str_pad($strMsjResultado, 5000, " ");
            $objStmt->bindParam('Pv_Mensaje', $strMsjResultado);

            $objStmt->execute();
            

            if(empty($strMsjResultado))
            {
                $strMsjResultado = 'Se ha insertado el nuevo elemento correctamente!';
            }
            $objResponse = new Response(json_encode(array('data'  => $strMsjResultado)));
            $objResponse->headers->set('Content-type', 'text/json');            
            return $objResponse;
        } 
        catch (\Exception $e)
        {
            $objResponse = new Response(json_encode(array( 'data'  => $e->getMessage())));
            $objResponse->headers->set('Content-type', 'text/json');            
            return $objResponse;
        }

    }
     /**
     * Documentación para el método 'ajaxGetEncontradosAction'.
     *
     * Metodo utilizado para obtener información de los s
     * 
     * @param Object  $objRespuesta con informacion del elemento de acuerdo a la petición.
     * 
     * @author Anthony Santillan <asantillany@telconet.ec>          
     * @version 1.0 version inicial 
     * 
     */
    public function ajaxGetEncontradosAction()
    {
        $objRespuesta       = new Response();
        $objPeticion        = $this->get('request');
        $emGeneral       = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');        
        
        $arrayParametros                     = array();
        $arrayParametros['strEstado']        = $objPeticion->query->get('strEstado');
        $arrayParametros['strDetalleNombre'] = 'CLASE';
        $arrayParametros['strTipoElemento']  = $objPeticion->query->get('sltTipoElemento');
        $arrayParametros['intElemento']      = $objPeticion->query->get('intElemento');
        $arrayParametros['START']            = $objPeticion->query->get('start');
        $arrayParametros['LIMIT']            = $objPeticion->query->get('limit');  

        $arrayElementos                     = array();
        $arrayRespuestas                     = array();
        $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
        ->findBy(array('descripcion' => 'Listado de rutas disponibles'));

        foreach($objAdmiParametroDet as $objTipoRuta)
        {
            $strTipoRuta = $objTipoRuta->getValor1();

            $objTipoElemento = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
            ->findOneBy(array( "nombreTipoElemento" => $strTipoRuta));

        if(is_object($objTipoElemento))
        {
            $strTipo = $objTipoElemento->getNombreTipoElemento();

            $arrayRespuesta = array('id' => $objTipoElemento->getId(), 'tipoRuta' => $strTipo );
        }
            array_push($arrayRespuestas,$arrayRespuesta);
        }
        foreach($arrayRespuestas as $objTipo)
        {
            $arrayParametros['strDetalleValor']  = $objTipo['id'];
            $arrayParametros['strTipoElemento']  = $objTipo['tipoRuta'];
            $arrayResultado = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
            ->getArrayElementosByDetalleParam($arrayParametros); 

            array_push($arrayElementos, $arrayResultado);
        }
        
        $objRespuesta = $arrayElementos[0];
        $objResponse = new Response(json_encode(array( 'encontrados'  => $objRespuesta['encontrados'])));
        $objResponse->headers->set('Content-type', 'text/json');            
        return $objResponse;
    }
}

 