<?php

namespace telconet\administracionBundle\Service;
use Doctrine\ORM\EntityManager;

use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\schemaBundle\Entity\AdmiTipoDocumento;


class PersonaEmpleadoService {
    
  
    private $emcom;
    private $emComunicacion;
    private $emGeneral;
    private $path_telcos;
    private $emInfraestructura;
    private $emFinanciero;
    private $session;
    private $fileRoot;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container) 
    {
        $this->path_telcos                     = $container->getParameter('path_telcos');   
        $this->emcom                           = $container->get('doctrine.orm.telconet_entity_manager');     
        $this->emComunicacion                  = $container->get('doctrine.orm.telconet_comunicacion_entity_manager');
        $this->emGeneral                       = $container->get('doctrine.orm.telconet_general_entity_manager');     
        $this->emInfraestructura               = $container->get('doctrine.orm.telconet_infraestructura_entity_manager');        
        $this->emFinanciero                    = $container->get('doctrine.orm.telconet_financiero_entity_manager');                    
        $this->session                         = $container->get('session');
        $this->fileRoot                        = $container->getParameter('ruta_upload_documentos');
    }
    
    /** 
    * Descripcion: Metodo encargado de eliminar documentos a partir del id de la referencia enviada
    * @author 
    * @version 1.0 11-12-2015   
    * @param integer $id // id del documento
    * @return json con resultado del proceso   
    */
    
    public function eliminarDocumento($id)
    {                
        $this->emComunicacion->getConnection()->beginTransaction();
        try
        {  
            $objInfoDocumento =  $this->emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($id);                                              
            if( $objInfoDocumento )
            {            
                $path = $objInfoDocumento->getUbicacionFisicaDocumento();
                if (file_exists($this->path_telcos.$path))
                unlink($this->path_telcos.$path);

                $objInfoDocumento->setEstado("Inactivo");
                $this->emComunicacion->persist($objInfoDocumento);
                $this->emComunicacion->flush();               

                $objInfoDocumentoRelacion = $this->emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')->findByDocumentoId($id);
                if(isset($objInfoDocumentoRelacion))
                {
                    foreach($objInfoDocumentoRelacion as $det)
                    {
                        $det->setEstado("Inactivo");
                        $this->emComunicacion->persist($det);
                        $this->emComunicacion->flush();
                    }
                }
             if ($this->emComunicacion->getConnection()->isTransactionActive())
             {
                 $this->emComunicacion->getConnection()->commit();
             }                
             $this->emComunicacion->getConnection()->close();  
             return $objInfoDocumento;    
             
            }     
        }
        catch(\Exception $e)
        {                                 
           if ($this->emComunicacion->getConnection()->isTransactionActive())
           {
               $this->emComunicacion->getConnection()->rollback();
           }                            
           $this->emComunicacion->getConnection()->close();  
           throw $e;
        }   
    }        
    
   /**
     * Funcion que Guarda Archivos Digitales agregados al empleado 
     * @author 
     * @param interger $id // id de InfoPersonaEmpresaRol 
     * @param string $usrCreacion
     * @param string $clientIp
     * @param array $datos_form
     * @throws Exception
     * @version 1.0 11-12-2015 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 21-12-2015 - Se agrega que se envíe el arreglo de las fechas de caducidad.
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 08-04-2016 - Se modifica entidad InfoDocumento ingresando la ruta y nombre de guardado como parametro a la entidad
     * 
     * @return \telconet\schemaBundle\Entity\InfoDocumentoRelacion
     */
    
    public function guardarArchivoDigital($id, $usrCreacion, $clientIp, $datos_form)
    {    
        $fecha_creacion = new \DateTime('now');      
        $this->emComunicacion->getConnection()->beginTransaction();
        try
        {  
            $objInfoPersonaEmpresaRol = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($id);
            if( $objInfoPersonaEmpresaRol )
            {
                //Guardo files asociados al empleado                      
                $datos_form_files           = $datos_form['datos_form_files'];
                $arrayTipoDocumentos        = $datos_form['arrayTipoDocumentos'];
                $arrayFechasHastaDocumentos = $datos_form['arrayFechasHastaDocumentos'];
                $i=0;
                foreach ($datos_form_files as $key => $imagenes)                 
                {  
                    foreach ( $imagenes as $key_imagen => $value) 
                    {        
                        if( $value )
                        {                            
                            $objInfoDocumento = new InfoDocumento(); 
                            $objInfoDocumento->setFile( $value );     
                            $objInfoDocumento->setNombreDocumento("documento_digital");
                            $objInfoDocumento->setUploadDir(substr($this->fileRoot, 0, -1));
                            $objInfoDocumento->setFechaDocumento( $fecha_creacion );                                                                 
                            $objInfoDocumento->setUsrCreacion( $usrCreacion );
                            $objInfoDocumento->setFeCreacion( $fecha_creacion );
                            $objInfoDocumento->setIpCreacion( $clientIp );
                            $objInfoDocumento->setEstado( 'Activo' );                                                           
                            $objInfoDocumento->setMensaje( "Archivo agregado al empleado con id "
                                                            .$objInfoPersonaEmpresaRol->getPersonaId()->getId());                                                             
                            if($arrayFechasHastaDocumentos[$key_imagen])
                            {
                                $objInfoDocumento->setFechaPublicacionHasta($arrayFechasHastaDocumentos[$key_imagen]);   
                            }
                            $objTipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                                        ->find($arrayTipoDocumentos[$key_imagen]);                                                                                                                                    
                            if( $objTipoDocumentoGeneral != null )
                            {            
                                $objInfoDocumento->setTipoDocumentoGeneralId( $objTipoDocumentoGeneral->getId() );                            
                            }                                                    
                            $i++;                        
                            if ( $objInfoDocumento->getFile() )
                            {
                                $objInfoDocumento->preUpload();
                                $objInfoDocumento->upload();
                            }                                                                           
                            $objTipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                                    ->findOneByExtensionTipoDocumento(strtoupper($objInfoDocumento->getExtension()));                                    

                            if( $objTipoDocumento != null)
                            {
                                $objInfoDocumento->setTipoDocumentoId($objTipoDocumento);                                
                            }
                            else
                            {   //Inserto registro con la extension del archivo a subirse
                                $objAdmiTipoDocumento = new AdmiTipoDocumento(); 
                                $objAdmiTipoDocumento->setExtensionTipoDocumento(strtoupper($objInfoDocumento->getExtension()));
                                $objAdmiTipoDocumento->setTipoMime(strtoupper($objInfoDocumento->getExtension()));                            
                                $objAdmiTipoDocumento->setDescripcionTipoDocumento('ARCHIVO FORMATO '.strtoupper($objInfoDocumento->getExtension()));
                                $objAdmiTipoDocumento->setEstado('Activo');
                                $objAdmiTipoDocumento->setUsrCreacion( $usrCreacion );
                                $objAdmiTipoDocumento->setFeCreacion( $fecha_creacion );                        
                                $this->emComunicacion->persist( $objAdmiTipoDocumento );
                                $this->emComunicacion->flush(); 
                                $objInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);    
                            }                      

                            $this->emComunicacion->persist($objInfoDocumento);
                            $this->emComunicacion->flush();   

                            $objInfoDocumentoRelacion = new InfoDocumentoRelacion(); 
                            $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());                    
                            $objInfoDocumentoRelacion->setModulo('PERSONAL'); 
                            $objInfoDocumentoRelacion->setPersonaEmpresaRolId($id);       
                            $objInfoDocumentoRelacion->setEstado('Activo');                                                                                   
                            $objInfoDocumentoRelacion->setFeCreacion($fecha_creacion);                        
                            $objInfoDocumentoRelacion->setUsrCreacion($usrCreacion);
                            $this->emComunicacion->persist($objInfoDocumentoRelacion);                        
                            $this->emComunicacion->flush();
                        }
                    }                       
                }
                if ($this->emComunicacion->getConnection()->isTransactionActive()){
                    $this->emComunicacion->getConnection()->commit();
                }                
                $this->emComunicacion->getConnection()->close();  
                return $objInfoDocumentoRelacion;
            }
       }
       catch(\Exception $e)
       {                                 
           if ($this->emComunicacion->getConnection()->isTransactionActive())
           {
               $this->emComunicacion->getConnection()->rollback();
           }                            
           $this->emComunicacion->getConnection()->close();  
           throw $e;
       }        
    }
    
    
    
    /**
     * 
     * Documentación para el método 'validacionesCreacionActualizacion'.
     * 
     * Valida la información ingresada para la creación o actualización de los Empleados.
     * 
     * @param array $arrayParametros ['tipoIdentificacion', 'identificacion','nombres','apellidos','direccion','genero','titulo']
     * 
     * @return array['boolOk','strMsg'] //si aprueba o no las validaciones con su respectivo mensaje de error si así fuera el caso.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 11-12-2015
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1 21-09-2017 - Se agrega el parámetro del país para validar la identificación.
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.2 10-01-2022 - Se agrega busqueda de codEmpresa al momento de validar la identificación
     */
    public function validacionesCreacionActualizacion($arrayParametros)
    {
        $boolOk    = true;
        $strMsg    = '';
        $intIdPais = $this->session->get('intIdPais');
        if(!isset($arrayParametros['tipoIdentificacion']) || !isset($arrayParametros['identificacion']) 
            || $arrayParametros['tipoIdentificacion'] == 'Seleccione...')
        {
            $boolOk = false;
            $strMsg = 'Seleccione un Tipo de Identificación para el Empleado';
        }
        else if(!isset($arrayParametros['identificacion']) || $arrayParametros['identificacion'] == '')
        {
            $boolOk = false;
            $strMsg = 'Ingrese la Identificación del Empleado';
        }
        else if(!isset($arrayParametros['nombres']) || $arrayParametros['nombres'] == '')
        {
            $boolOk = false;
            $strMsg = 'Ingrese los Nombres del Empleado';
        }
        else if(!isset($arrayParametros['direccion']) || $arrayParametros['direccion'] == '')
        {
            $boolOk = false;
            $strMsg = 'Ingrese la Dirección del Empleado';
        }
        else if(!isset($arrayParametros['apellidos']) || $arrayParametros['apellidos'] == '')
        {
            $boolOk = false;
            $strMsg = 'Ingrese los Apellidos del Empleado';
        }
        else if(!isset($arrayParametros['genero']) || $arrayParametros['genero'] == 'Seleccione...')
        {
            $boolOk = false;
            $strMsg = 'Seleccione el Género del Empleado';
        }
        else if(!isset($arrayParametros['titulo']) || $arrayParametros['titulo'] == 'Seleccione...')
        {
            $boolOk = false;
            $strMsg = 'Seleccione el Título del Empleado';
        }
        else if(!isset($arrayParametros['estadoCivil']) || $arrayParametros['estadoCivil'] == '')
        {
            $boolOk = false;
            $strMsg = 'Seleccione el estado civil del Empleado';
        }
        else
        {
            
            $objRepositorio = $this->emcom->getRepository('schemaBundle:InfoPersona');
            $intIdEmpresa   = $this->session->get('idEmpresa');

            $arrayParamValidaIdentifica = array(
                                                    'strTipoIdentificacion'     => $arrayParametros['tipoIdentificacion'],
                                                    'strIdentificacionCliente'  => $arrayParametros['identificacion'],
                                                    'intIdPais'                 => $intIdPais,
                                                    'strCodEmpresa'             => $intIdEmpresa
                                                );
            $strMensaje     = $objRepositorio->validarIdentificacionTipo($arrayParamValidaIdentifica);
            
            if($strMensaje=='')
            {
                $objListaPersonal = $objRepositorio->findPersonaPorIdentificacion($arrayParametros['tipoIdentificacion'], 
                                                                            $arrayParametros['identificacion']);
                if(!empty($objListaPersonal))
                {
                    
                    $intIdEmpresa           = $this->session->get('idEmpresa');
                    $objListaPersonaEmpresa = $objRepositorio->getEmpresasExternas($intIdEmpresa, 'Todos', $objListaPersonal[0]->getId());
                    if(!empty($objListaPersonaEmpresa))
                    {
                        $strMensaje = 'La identificación corresponde a una Empresa Externa';
                    }
                    else
                    {
                        if($objListaPersonal[0]->getId() != $arrayParametros['idPersona'])
                        {
                            $strMensaje = 'La identificación corresponde a un empleado ya existente';
                        }
                    }
                }
            }
            $boolOk = $strMensaje == '';
            $strMsg = $boolOk ? '' : $strMensaje;
        }
        return array('boolOk' => $boolOk, 'strMsg' => $strMsg);
    }
    
}
