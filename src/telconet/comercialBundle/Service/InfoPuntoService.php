<?php

namespace telconet\comercialBundle\Service;

use Doctrine\ORM\EntityManager;

use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoPunto;
use telconet\schemaBundle\Entity\InfoPuntoDatoAdicional;
use telconet\schemaBundle\Entity\InfoPuntoFormaContacto;
use telconet\schemaBundle\Entity\InfoPuntoCaracteristica;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Form\InfoPuntoType;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoPuntoHistorial;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\schemaBundle\Entity\AdmiTipoDocumento;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoAdendum;
use telconet\schemaBundle\Entity\AdmiParametroCab;


class InfoPuntoService {
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emcom;

    private $emFinanciero;    
    
    private $emInfraestructura;
    
    private $emComunicacion;
    
    private $emGeneral;
    
    private $strPathTelcos;
    
    private $strFileRoot;
    
    private $strFileCloudRoot;
    
    private $emSoporte;
    /**
     * @var \telconet\schemaBundle\Service\ValidatorService
     */
    private $validator;
    private $serviceInfoPersonaFormaContacto;
    private $serviceUtilidades;
    private $serviceSoporte;
    private $serviceUtil;
    private $serviceSecurity;
    private $serviceInfoCambiarPlan;
    private $serviceTecnico;
    private $serviceComercialMobile;
    private $serviceRestClient;
    private $strMSnfs;
    private $serviceComercialCrm;
    private $strUrlVerificarCatalogoMs; 
    private $strUrlValidacionPuntoAdicionalMs;
    
    private $strUrlgetCreacionPuntoLinks; 
    private $serviceTokenCas;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container) 
    {
        $this->strFileRoot                     = $container->getParameter('ruta_upload_documentos');
        $this->strPathTelcos                   = $container->getParameter('path_telcos');        
        $this->emcom                           = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emFinanciero                    = $container->get('doctrine')->getManager('telconet_financiero');
        $this->emInfraestructura               = $container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emComunicacion                  = $container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emGeneral                       = $container->get('doctrine.orm.telconet_general_entity_manager');        
        $this->emSoporte                       = $container->get('doctrine.orm.telconet_soporte_entity_manager');
        $this->serviceRestClient               = $container->get('schema.RestClient');
        $this->strMSnfs                        = $container->getParameter('ms_nfs');
        $this->validator                       = $container->get('schema.Validator');
        $this->serviceInfoPersonaFormaContacto = $container->get('comercial.InfoPersonaFormaContacto'); 
        $this->serviceUtilidades               = $container->get('administracion.Utilidades'); 
        $this->serviceSoporte                  = $container->get('soporte.SoporteService');
        $this->serviceUtil                     = $container->get('schema.Util');
        $this->serviceSecurity                 = $container->get('security.context');
        $this->serviceInfoCambiarPlan          = $container->get('tecnico.InfoCambiarPlan');
        $this->serviceTecnico                  = $container->get('tecnico.InfoServicioTecnico');
        $this->strFileCloudRoot                = $container->getParameter('cloudforms_ruta_archivos');
        $this->serviceComercialMobile          = $container->get('comercial.ComercialMobile');
        $this->serviceComercialCrm             = $container->get('comercial.ComercialCRM');
        $this->strUrlVerificarCatalogoMs       = $container->getParameter('ws_ms_verificarCatalogo_url');
        $this->strUrlValidacionPuntoAdicionalMs= $container->getParameter('ws_ms_validacionesPuntoAdicional_url');
        $this->strUrlgetCreacionPuntoLinks     = $container->getParameter('ws_ms_creacion_punto_url');
        $this->strUrlMsCompContratoDigital     = $container->getParameter('ws_ms_contrato_digital_url');
        $this->serviceTokenCas                 = $container->get('seguridad.TokenCas');
    }
        
    /**
     * Devuelve los puntos de cobertura activos de la empresa, filtrados por nombre
     * @param string $codEmpresa
     * @param string $nombre (nullable)
     * @param string $idKey key a usar en el array para el id del punto cobertura
     * @param string $nombreKey key a usar en el array para el nombre del punto cobertura
     * @return array de arrays id/nombre
     * @see \telconet\schemaBundle\Entity\AdmiJurisdiccion
     */
    public function obtenerPuntosCobertura($codEmpresa, $nombre = NULL, $idKey = 'id', $nombreKey = 'nombre')
    {
        $list = $this->emcom->getRepository('schemaBundle:AdmiJurisdiccion')->getJurisdiccionesPorNombrePorEmpresa($nombre, $codEmpresa);
        $arreglo = array();
        /* @var $value \telconet\schemaBundle\Entity\AdmiJurisdiccion */
        foreach ($list as $value):
        $arreglo[] = array($idKey => $value->getId(), $nombreKey => $value->getNombreJurisdiccion());
        endforeach;
        return $arreglo;
    }

    /**
     * Devuelve los cantones activos de la jurisdiccion dada, filtrados por nombre
     * 
     * @param integer $idjurisdiccion
     * @param string $strNombre
     * @param string $strIdKey key a usar en el array para el id del canton
     * @param string $strNombreKey key a usar en el array para el nombre del canton
     * 
     * @return array de arrays id/nombre
     * @see \telconet\schemaBundle\Entity\AdmiCantonJurisdiccion
     * @see \telconet\schemaBundle\Entity\AdmiCanton
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.1 11-04-2016
     * Ya no se procesa el listado AdmiCantonJurisdiccion y se retorna directamente el resutado del Repository
     * Se agrega el filtrado por el nombre del Cantón.
     */
    public function obtenerCantonesJurisdiccion($idjurisdiccion, $strNombre = NULL, $strIdKey = 'id', $strNombreKey = 'nombre')
    {
        $arrayParametros['JURISDICCIONID'] = $idjurisdiccion;
        $arrayParametros['NOMBRE']         = strtoupper(trim($strNombre));
        $arrayParametros['ESTADO']         = 'Activo';
        $arrayParametros['VALUE']          = $strIdKey;
        $arrayParametros['DISPLAY']        = $strNombreKey;
        
        return $this->emcom->getRepository('schemaBundle:AdmiCantonJurisdiccion')->getCantonesJurisdicciones($arrayParametros);
    }
    
    /**
     * Devuelve los tipos de negocio activos de la empresa dada
     * @param string $codEmpresa
     * @param string $idKey key a usar en el array para el id del tipo de negocio
     * @param string $nombreKey key a usar en el array para el nombre del tipo de negocio
     * @return array de arrays id/nombre
     * @see \telconet\schemaBundle\Entity\AdmiTipoNegocio
     */
    public function obtenerTiposNegocio($codEmpresa, $idKey = 'id', $nombreKey = 'nombre')
    {
        /* @var $repoAdmiTipoNegocio \telconet\schemaBundle\Repository\AdmiTipoNegocioRepository */
        $repoAdmiTipoNegocio = $this->emcom->getRepository('schemaBundle:AdmiTipoNegocio');
        $list = $repoAdmiTipoNegocio->findTiposNegocioActivosPorEmpresa($codEmpresa)->getQuery()->getResult();
        $arreglo = array();
        /* @var $value \telconet\schemaBundle\Entity\AdmiTipoNegocio */
        foreach ($list as $value):
        $arreglo[] = array($idKey => $value->getId(), $nombreKey => $value->getNombreTipoNegocio());
        endforeach;
        return $arreglo;
    }
    
    /**
     * Devuelve los tipos de ubicacion activos
     * @param string $idKey key a usar en el array para el id del tipo de ubicacion
     * @param string $descripcionKey key a usar en el array para la descripcion del tipo de ubicacion
     * @return array de arrays id/descripcion
     * @see \telconet\schemaBundle\Entity\AdmiTipoUbicacion
     */
    public function obtenerTiposUbicacion($idKey = 'id', $descripcionKey = 'descripcion')
    {
        /* @var $repoAdmiTipoUbicacion \telconet\schemaBundle\Repository\AdmiTipoUbicacionRepository */
        $repoAdmiTipoUbicacion = $this->emcom->getRepository('schemaBundle:AdmiTipoUbicacion');
        $list = $repoAdmiTipoUbicacion->findTiposUbicacionActivos()->getQuery()->getResult();
        $arreglo = array();
        /* @var $value \telconet\schemaBundle\Entity\AdmiTipoUbicacion */
        foreach ($list as $value):
        $arreglo[] = array($idKey => $value->getId(), $descripcionKey => $value->getDescripcionTipoUbicacion());
        endforeach;
        return $arreglo;
    }
    
    /**
     * Devuelve los puntos de la empresa indicada que son edificios, filtrados por nombre
     * @param string $codEmpresa
     * @param string $nombre (nullable)
     * @param integer $limit (nullable)
     * @param integer $page (nullable)
     * @param integer $start (nullable)
     * @param string $order (nullable)
     */
    public function obtenerPuntosEdificios($codEmpresa, $nombre, $limit, $page, $start, $order = NULL)
    {
        $resultado = $this->emcom->getRepository('schemaBundle:InfoPunto')->findPtosEdificiosActivosPorEmpresa($codEmpresa, $nombre, $limit, $page, $start, $order);
        $datos = $resultado['registros'];
        $arreglo = array();
        foreach ($datos as $datos):
        $arreglo[] = array(
                        'idPto' => $datos['id'],
                        'cliente' => ($datos['razonSocial'] ? $datos['razonSocial'] : $datos['nombres'].' '.$datos['apellidos']),
                        'login' => $datos['login'],
                        'descripcionPunto' => $datos['descripcionPunto'],
                        'Direccion' => $datos['direccion'],
                        'nombreEdificio' => $datos['nombreEdificio']
        );
        endforeach;
        return array('total' => $resultado['total'], 'registros' => $arreglo);
    }
    
    /**
     * Genera un login para un nuevo punto del cliente dado     
     * @param string  $strCodEmpresa
     * @param integer $intIdCanton
     * @param integer $intIdPersona
     * @param integer $intIdTipoNegocio
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * Consideraciones: Se agrega Funcion que permite quitar los caracteres especiales del login formado por punto cliente
     * caracteres permitidos [^A-Za-z0-9-]
     * @version 1.1 05-08-2015
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.2 19-07-2017 - Se modifica la validación para generar el prefijo de la empresa.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.3 08-03-2019 - Se agrega funcionalidad para generación de login para puntos de TNG, se renombran variables.
     */
    public function generarLogin($strCodEmpresa, $intIdCanton, $intIdPersona, $intIdTipoNegocio)
    {
        $objEmpresa     = $this->emcom->getRepository('schemaBundle:InfoEmpresaGrupo')->find($strCodEmpresa);
        $objAdmiCanton  = $this->emcom->getRepository('schemaBundle:AdmiCanton')->find($intIdCanton);
        $objCliente     = $this->emcom->getRepository('schemaBundle:InfoPersona')->find($intIdPersona);

        $strLoginSinSecuencia = $this->generarLoginPrv($objCliente, $objAdmiCanton->getSigla());

        //asigna tipo negocio
        if($intIdTipoNegocio)
        {
            $objTipoNegocio = $this->emcom->getRepository('schemaBundle:AdmiTipoNegocio')->find($intIdTipoNegocio);
            if($objTipoNegocio)
            {
                if(strtoupper($objTipoNegocio->getNombreTipoNegocio()) === 'ISP')
                {
                    $strTipoNegocio = 'isp-';
                }
                else
                {
                    $strTipoNegocio = '';
                }
            }
        }

        //asigna prefijo de empresa
        $strPrefijoEmpresa = trim(strtoupper($objEmpresa->getPrefijo()));

        if(isset($strPrefijoEmpresa))
        {
            if($strPrefijoEmpresa === 'TNG')
            {
                $objAdmiNumeracion = $this->emcom->getRepository('schemaBundle:AdmiNumeracion')
                                                 ->findOneBy(array('empresaId'=>$strCodEmpresa ,'codigo'=>'TG', 'estado'=>'Activo' ));
                
                if(is_object($objAdmiNumeracion))
                {
                    
                    
                    $objParamFormatoLogin = $this->emcom->getRepository('schemaBundle:AdmiParametroCab')
                                                        ->findOneBy( array('nombreParametro' => 'GENERA_LOGIN','estado' => 'Activo') );                    
                    
                    if(is_object($objParamFormatoLogin))
                    {
                        $objParamDetFormLoginLong = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                                               ->findOneBy( array('parametroId' => $objParamFormatoLogin,
                                                                                  'valor2'      => 'LongitudLogin',
                                                                                  'estado'      => 'Activo'));
                        if(is_object($objParamDetFormLoginLong))
                        {                        
                            $intLongitudFormato       = intval($objParamDetFormLoginLong->getValor1());
                        }
                        
                        $objParamDetFormLoginPref = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                                               ->findOneBy( array('parametroId' => $objParamFormatoLogin,
                                                                                  'valor2'      => 'PrefijoLogin',
                                                                                  'estado'      => 'Activo'));
                        if(is_object($objParamDetFormLoginPref))
                        {
                            $strPrefijoLogin      = $objParamDetFormLoginPref->getValor1();
                        }
                    }
                     
                    
                    $strSecuenciaAsignada = str_pad($objAdmiNumeracion->getSecuencia(), $intLongitudFormato, "0", STR_PAD_LEFT);                   
                }             
            }
            else
            {
                $strPrefijoEmpresa = strtolower($strPrefijoEmpresa) . '-';
            }
        }else
        {
            $strPrefijoEmpresa = '';
        }
        
        if($strPrefijoEmpresa === 'TNG')
        {
            $strLogin = $strPrefijoLogin . $strSecuenciaAsignada; 
        }
        else
        {
            $strLoginSinSecuencia = $strPrefijoEmpresa . $strTipoNegocio . $strLoginSinSecuencia;
            $strLoginSinSecuencia = $this->escapaCaracteresEspeciales($strLoginSinSecuencia);
            if(!$strLoginSinSecuencia)
            {
                throw new \Exception('No se pudo Eliminar caracteres especiales al Login del Punto Cliente: ' . $strLoginSinSecuencia);
            }            

            $arrayPuntos = $this->emcom->getRepository('schemaBundle:InfoPunto')
                                      ->findPtosPorEmpresaPorCanton($strCodEmpresa, $strLoginSinSecuencia, $intIdCanton, 99999, 1, 0);

            $strLogin         = $strLoginSinSecuencia . ($arrayPuntos['total'] + 1);
            $intContadorLogin = $arrayPuntos['total'] + 1;

            $boolExistePunto = true;
            while($boolExistePunto)
            {
                $objPuntoExistente = $this->emcom->getRepository('schemaBundle:InfoPunto')->findByLogin($strLogin);
                if($objPuntoExistente)
                {
                    $boolExistePunto  = true;
                    $intContadorLogin = $intContadorLogin + 1;
                    $strLogin         = $strLoginSinSecuencia . $intContadorLogin;

                }
                else
                {
                    $boolExistePunto = false;
                }
            }            
        }       

        if(!$strLogin)
        {
            $strResponse = "";
        }
        else
        {           
            $strResponse = $strLogin;
        }

        return $strResponse;
    }

    /**
     * Funcion que permite quitar los caracteres especiales del login formado por punto cliente
     * caracteres permitidos [^A-Za-z0-9-]
     * Consideraciones: Funcion debe recibir la cadena y eliminar los caracteres especiales y retornar la cadena limpia
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 05-08-2015
     * @param string $strCadena    
     * @throws Exception 
     * @return $strCadenaLimpia
     */
     public function escapaCaracteresEspeciales($strCadena)
    {
        if($strCadena)
        {
            $this->emcom->getConnection()->beginTransaction();
            try
            {                  
                $v_cadena_limpia = str_pad($v_cadena_limpia, 1000, " ");
                $sql = 'BEGIN EscapaCaracteresEspeciales(:v_cadena,:v_cadena_limpia); END;';
                $stmt = $this->emcom->getConnection()->prepare($sql);
                $stmt->bindParam('v_cadena', $strCadena);               
                $stmt->bindParam('v_cadena_limpia', $v_cadena_limpia);
                $stmt->execute();                
            }
            catch(\Exception $e)
            {
                $v_cadena_limpia = false;
                error_log($e->getMessage());
            }
            $this->emcom->getConnection()->close();              
            return $v_cadena_limpia;
        }
        else
        {
            return false;
        }
    }
    /**
     * Genera un login en base a los datos del cliente y la sigla del canton
     * @param \telconet\schemaBundle\Entity\InfoPersona $cliente
     * @param string $sigla
     * @return string
     */
    public function generarLoginPrv(InfoPersona $cliente, $sigla)
    {
        $caracter_orig =    array('á','é','í','ó','ú','.',',','-','_','ñ','&','/',' ','Á','É','Í','Ó','Ú','Ñ');
        $caracter_reemp =   array('a','e','i','o','u', '', '', '', '','n', '', '', '','a','e','i','o','u','n');
        $inicialPrimerNombre = "";
        $inicialSegundoNombre = "";
        $inicialPrimerApellido = "";
        $inicialSegundoApellido = "";
        $inicialRazonSocial="";
        //armo el posible login
        if(!$cliente->getRazonSocial()){
            $arr_nombre=explode(' ',$cliente->getNombres());
            if(count($arr_nombre)>1){
                $inicialPrimerNombre=substr($arr_nombre[0],0,1);
                $inicialSegundoNombre=substr($arr_nombre[1],0,1);
            }else{
                $inicialPrimerNombre=substr($arr_nombre[0],0,1);
            }
            $arr_apellido=explode(' ',$cliente->getApellidos());
            if(count($arr_apellido)>1){
                $inicialPrimerApellido=$arr_apellido[0];
                $inicialSegundoApellido=substr($arr_apellido[1],0,1);
            }else{
                $inicialPrimerApellido=$arr_apellido[0];
            }
            $login=$sigla.$inicialPrimerNombre.$inicialSegundoNombre.$inicialPrimerApellido.$inicialSegundoApellido;
        }else{
            $arr_razonSocial = explode(" ",$cliente->getRazonSocial());
            $inicialRazonSocial = $arr_razonSocial[0];
            $login=$sigla.$inicialRazonSocial;
        }
        $login = str_replace($caracter_orig,$caracter_reemp,$login);
        return strtolower($login);
    }
    
    /**
     * Valida que un login no este asignado a ningun punto
     * @param string $login
     * @return string "no" si no existe algun punto con el login dado, "si" si ya existe
     */
    public function validarLogin($login)
    {
        $clienteSucursal = $this->emcom->getRepository('schemaBundle:InfoPunto')->findOneBy(array('login'=> $login));
        if (!$clienteSucursal)
        {
            $response = "no";
        }
        else
        {
            $response = "si";
        }
        return $response;
    }
    
    /**
     * Documentación para el método 'crearPunto'.
     * Crea un nuevo Punto
     * 
     * Consideracion: Se aumenta campo origen WEB ya que se requiere que se identifiquen los Puntos Clientes que han sido ingresados 
     * por la versión Web y los que se ingresaron mediante el Mobil.     
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>       
     * @version 1.1 26-03-2015     
     * 
     * Consideracion: Se Valida el ingreso de las formas de Contacto
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>       
     * @version 1.2 02-09-2015 
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.3 21-12-2015
     * Se incorporó el guardado del Canal y el Punto de venta relacionado al nuevo punto del cliente
     * 
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 1.4 22-03-2016
     * Se valida que se reciban el Canal y el Punto de venta.
     * 
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 1.5 27-07-2016
     * Si la empresa es TN se procesa el parámetro esPadreFacturacion para definirlo como PF o no, caso contrario será siempre será PF.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.6 08-11-2016
     * Se envia array de Parametros $arrayParamFormasContac a la funcion "validarFormasContactos" se agrega strOpcionPermitida, strPrefijoEmpresa. 
     * Prefijo de empresa en sesion para validar que para empresa MD no se obligue el ingreso de al menos 1 correo.
     * 
     * @author John Vera <javera@telconet.ec>       
     * @version 1.6 14-11-2016 Se agrega validación cuando se crea un punto con un pseudo pe existente
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>       
     * @version 1.7 23-05-2017 Se modifica creacion automatica de edificio cuando sea de tipo NODO SATELITAL sin factibilidad de GIS
     *  
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.8 04-07-2017
     * Se agregan las variables strNombrePais e intIdPais para realizar las validaciones por el país de la empresa.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.9
     * @since 28-01-2019
     * Se agrega el tipo de origen del punto para determinar si un punto es de tipo Migración o no.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.10
     * @since 11-03-2019
     * Se agrega validación para obtener el rol, solo si es Origen Web.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.11 26-03-2019
     * Se agrega actualizacion de secuencia para numeración del login generado para un nuevo punto de TNG, registro interno del sector según
     * el parroquiaId ingresado.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.12
     * @since 09-07-2019
     *
     * @author Walther Joao Gaibor C.<wgaibor@telconet.ec>
     * @version 1.13 29-07-2020 - Si el origen es móvil se almacen el croquis en el NFS remoto.
     *
     * @author Walther Joao Gaibor C.<wgaibor@telconet.ec>
     * @version 1.14 20-11-2020 - Implementación de bandera NFS.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 2.0 09-05-2021 - Se agrega lógica para guardar razón social e identificación,
     *                           en caso de que el cliente en sesión es de tipo distribuidor.
     * 
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 2.1 29-09-2021 - Se remplazo el consumo de fecha desde repositorio para obtener fecha actual del servidor de bd 
     * 
     * @param $arrayParametros = array('strCodEmpresa'        => id de la empresa en sesión,
     *                                 'strUsrCreacion'       => usuario de creación,
     *                                 'strClientIp'          => ip local,
     *                                 'arrayDatosForm'       => array de datos del punto,
     *                                 'arrayFormasContacto'  => array de formas de contacto, si es null se obtiene de $arrayDatosForm);
     * 
     *  * 
     * @author Modificado: Jorge Veliz <jlveliz@telconet.ec>
     * @version 2.1 30-06-2021 - Se modifico la subida mediante el ms
     *
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 2.2 05-12-2022 - Se agrega lógica para validaciones en Cliente al momento de crear nuevo punto. Se consume
     *                           al microservicio del método validacionesPuntoAdicionalMs.
     * @author Modificado: Daniel Guzmán <ddguzman@telconet.ec>
     * @version 2.3 16-02-2023 - Se agrega una validación para que cuando se crea un punto mediante el flujo ‘Proceso Traslado’se guardan también 
     *                           las siguientes InfoPuntoCaracteristica ’ES_PROCESO_CONTINUO’, ‘ORIGEN_REQUERIMIENTO’, ‘PUNTO_ATENCION’, 
     *                           ‘PUNTO_ORIGEN_CREACION’, ‘ESTADO_PROCESO_PUNTO’.

     * 
     * @author Henry Pérez Garcia <hrperez@telconet.ec>
     * @version 2.3 25-05-2023 - Se agrega filtro de estado activo para verifica si existe cliente en TelcoS+,
     *                           se valida en TelcoCRM que exista almenos una propuesta abierta.
     * 
     * @throws Exception
     * @return \telconet\schemaBundle\Entity\InfoPunto
     */
    public function crearPunto($arrayParametros)  
    {
        $strCodEmpresa        = $arrayParametros['strCodEmpresa'];
        $strUsrCreacion       = $arrayParametros['strUsrCreacion'];
        $strClientIp          = $arrayParametros['strClientIp'];
        $arrayDatosForm       = $arrayParametros['arrayDatosForm'];     
        $arrayFormasContacto  = $arrayParametros['arrayFormasContacto'];
        $strCanal             = $arrayParametros['strCanal'];
        $boolNfs              = (isset($arrayParametros['bandNfs'])) ? $arrayParametros['bandNfs'] : 0;
        $boolArchivoTmComerc  = false;

        $entityPunto   = new InfoPunto();
        $strNombrePais = $arrayDatosForm['strNombrePais'];
        $intIdPais     = $arrayDatosForm['intIdPais'];
        $objSysDate    = $this->emcom->getRepository('schemaBundle:InfoPersona')->findFechaSistema();
        if (empty($arrayFormasContacto))
        {
            // si no se ha especificado formas de contacto, obtenerlas de $datos_form
            if (is_array($arrayDatosForm['formas_contacto']))
            {
                $arrayFormasContacto = $arrayDatosForm['formas_contacto'];
            }
            else
            {
                $arrayFormasContacto = array();
                if ($arrayDatosForm['formas_contacto'])
                {
                    $arrayFormasContact = explode(',', $arrayDatosForm['formas_contacto']);
                    for ($intCont = 0; $intCont < count($arrayFormasContact); $intCont+=3) 
                    {
                        $arrayFormasContacto[] = array(
                                        'formaContacto' => $arrayFormasContact[$intCont+1],
                                        'valor'         => $arrayFormasContact[$intCont+2]
                        );
                    }
                }
            }
        }
        $strError = '';
        $this->emcom->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();

        try{           
            
            //Validaciones en Cliente para creación de nuevo punto mediante la llamada al microservicio ms-comp-cliente.
            if(($arrayDatosForm['prefijoEmpresa'] == 'MD'|| $arrayDatosForm['prefijoEmpresa'] == 'EN') && $arrayDatosForm['rol'] == 'Cliente')
            {
                $strTipoOrigen = $arrayDatosForm['strTipoOrigen'] == '' ? "Nuevo" : $arrayDatosForm['strTipoOrigen']; 

                $arrayTokenCas   = $this->serviceTokenCas->generarTokenCas();

                $arrayParametros = array();
                $arrayParametros['usrCreacion']       = $strUsrCreacion;
                $arrayParametros['clienteIp']         = $strClientIp;       
                $arrayParametros['token']             = $arrayTokenCas['strToken'];
                $arrayParametros['idEmpresa']         = $strCodEmpresa;
                $arrayParametros['idPersona']         = $arrayDatosForm['personaId']; 
                $arrayParametros['tipoOrigen']        = $strTipoOrigen; 

                $arrayResponse = $this->validacionesPuntoAdicionalMs($arrayParametros);

                if($arrayResponse["strStatus"] == "ERROR")
                {
                    throw new \Exception($arrayResponse["strMensaje"]);
                }
            } 
            
            //Ingresa datos del punto
            $entityPunto->setNombrePunto($arrayDatosForm['nombrepunto']);
            $entityPunto->setDireccion($arrayDatosForm['direccion']);
            $entityPunto->setDescripcionPunto($arrayDatosForm['descripcionpunto']); // referencia
            $entityPunto->setObservacion($arrayDatosForm['observacion']);
            $entityPunto->setFile($arrayDatosForm['file']); // croquis
            $entityPunto->setFileDigital($arrayDatosForm['fileDigital']);
            $entityAdmiJurisdiccion=$this->emcom->getRepository('schemaBundle:AdmiJurisdiccion')->find($arrayDatosForm['ptoCoberturaId']);
            $entityPunto->setPuntoCoberturaId($entityAdmiJurisdiccion);
            $entityAdmiTipoNegocio=$this->emcom->getRepository('schemaBundle:AdmiTipoNegocio')->find($arrayDatosForm['tipoNegocioId']);
            $entityPunto->setTipoNegocioId($entityAdmiTipoNegocio);
            $entityAdmiTipoUbicacion=$this->emcom->getRepository('schemaBundle:AdmiTipoUbicacion')->find($arrayDatosForm['tipoUbicacionId']);
            $entityPunto->setTipoUbicacionId($entityAdmiTipoUbicacion);
            if($strNombrePais==='GUATEMALA')
            {
                $entityAdmiSector=$this->emcom->getRepository('schemaBundle:AdmiSector')
                                              ->findOneBy(array('parroquiaId'=>$arrayDatosForm['parroquiaId'], 'estado'=>'Activo' ));
            }
            else
            {
                $entityAdmiSector=$this->emcom->getRepository('schemaBundle:AdmiSector')->find($arrayDatosForm['sectorId']);
            }
            $entityPunto->setSectorId($entityAdmiSector);               
            $entityPunto->setLatitud($arrayDatosForm['latitudFloat']);
            $entityPunto->setLongitud($arrayDatosForm['longitudFloat']);

            //Se ingresa persona empresa rol del prospecto
            //se graba como Activo porque se ingresa un punto
            try
            {

                $entityPersonaEmpresaRol = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                        ->getPersonaEmpresaRolPorPersonaPorTipoRolParaNew($arrayDatosForm['personaId'], $arrayDatosForm['rol'], $strCodEmpresa);
            }
            catch (\Doctrine\ORM\NonUniqueResultException $e)
            {
                throw new \Exception('No se puede crear el punto, la persona tiene mas de un rol ' . $arrayDatosForm['rol']);
            }
            if (empty($entityPersonaEmpresaRol))
            {
                throw new \Exception('No se puede crear el punto, la persona no tiene rol ' . $arrayDatosForm['rol']);
            }
            $entityPersonaEmpresaRol->setEstado('Activo');
            $this->emcom->persist($entityPersonaEmpresaRol);
            // $this->emcom->flush (); // demasiado flush reduce performance

            $ultimoEstado = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')
                    ->findUltimoEstadoPorPersonaEmpresaRol($entityPersonaEmpresaRol->getId()); 
            $idUltimoEstado=$ultimoEstado[0]['ultimo'];
            $entityUltimoEstado=$this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->find($idUltimoEstado);
            $estado=$entityUltimoEstado->getEstado();
            
            //SI EL ULTIMO ESTADO DEL PROSPECTO ES DIFERENTE DE ACTIVO
            if ($estado!='Activo')
            {
                //REGISTRA EN LA TABLA DE PERSONA_EMPRESA_ROL_HISTO
                $entity_persona_historial = new InfoPersonaEmpresaRolHisto();
                $entity_persona_historial->setEstado($entityPersonaEmpresaRol->getEstado());
                $entity_persona_historial->setFeCreacion($objSysDate);
                $entity_persona_historial->setIpCreacion($strClientIp);
                $entity_persona_historial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                $entity_persona_historial->setUsrCreacion($strUsrCreacion);
                $this->emcom->persist($entity_persona_historial);
                // $this->emcom->flush (); // demasiado flush reduce performance
            }
            $entityPunto->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
            $entityPunto->setLogin($arrayDatosForm['login']);
            $entityPunto->setIpCreacion($strClientIp);
            $entityPunto->setFeCreacion($objSysDate);
            $entityPunto->setUsrCreacion($strUsrCreacion);
            $entityPunto->setUsrVendedor($arrayDatosForm['loginVendedor']);
            
            if($arrayDatosForm['nuevoNodoCliente'] == 'SI')
            {                
                $entityPunto->setEstado('PendienteEdif');                                
            }
            else
            {
                $entityPunto->setEstado('Pendiente');
            } 
            
            //Campo para marcar si el registro de un Punto se origino en la aplicacion WEB Telcos "S"
            if($arrayDatosForm ['origen_web'] && ($arrayDatosForm ['origen_web']=="S" || $arrayDatosForm ['origen_web']=="N"))
            {                
                $entityPunto->setOrigenWeb($arrayDatosForm ['origen_web']);
            }
            if ($entityPunto->getFile())
            {
                $entityPunto->preUpload();
                if(isset($arrayDatosForm['origen']) && $arrayDatosForm['origen'] == 'MOVIL' && $boolNfs)
                {
                    $boolArchivoTmComerc = true;
                }
                else
                {
                    $strPrefijoEmpresa  = isset($arrayDatosForm['prefijoEmpresa']) ? $arrayDatosForm['prefijoEmpresa'] : 'TN';
                    $strNombreApp       = !empty($arrayDatosForm['strApp']) ? $arrayDatosForm['strApp'] : 'TelcosWeb';
                    $arrayPathAdicional = [];
                    $strSubModulo = 'PuntoCroquis';

                    $arrayParamNfs          = array(
                        'prefijoEmpresa'       => $strPrefijoEmpresa,
                        'strApp'               => $strNombreApp,
                        'strSubModulo'         => $strSubModulo,
                        'arrayPathAdicional'   => $arrayPathAdicional,
                        'strBase64'            => base64_encode(file_get_contents($entityPunto->getFile())),
                        'strNombreArchivo'     => $entityPunto->getPath(),
                        'strUsrCreacion'       => $strUsrCreacion);
                    $arrayRespNfs = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);

                    if(isset($arrayRespNfs))
                    {
                        if($arrayRespNfs['intStatus'] == 200)
                        {
                            $entityPunto->setPath($arrayRespNfs['strUrlArchivo']);
                            $entityPunto->setFile(null);
                            $this->emcom->persist($entityPunto);
                        }
                        else
                        {
                            throw new \Exception('No se pudo crear el punto, error al cargar el croquis');
                        }
                    }
                    else
                    {
                        throw new \Exception('Error, no hay respuesta del WS para almacenar el documento');
                    }

                }

            }
            if ($entityPunto->getFileDigital())
            {
                $entityPunto->preUploadDigital();
                $strPrefijoEmpresa  = isset($arrayDatosForm['prefijoEmpresa']) ? $arrayDatosForm['prefijoEmpresa'] : 'TN';
                    $strNombreApp       = !empty($arrayDatosForm['strApp']) ? $arrayDatosForm['strApp'] : 'TelcosWeb';
                    $arrayPathAdicional = [];
                    $strSubModulo = 'PuntoArchivoDigital';

                    $arrayParamNfs          = array(
                        'prefijoEmpresa'       => $strPrefijoEmpresa,
                        'strApp'               => $strNombreApp,
                        'strSubModulo'         => $strSubModulo,
                        'arrayPathAdicional'   => $arrayPathAdicional,
                        'strBase64'            => base64_encode(file_get_contents($entityPunto->getFileDigital())),
                        'strNombreArchivo'     => $entityPunto->getPathDigital(),
                        'strUsrCreacion'       => $strUsrCreacion);
                    $arrayRespNfs = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);

                    if(isset($arrayRespNfs))
                    {
                        if($arrayRespNfs['intStatus'] == 200)
                        {
                            $entityPunto->setPathDigital($arrayRespNfs['strUrlArchivo']);
                            $entityPunto->setFileDigital(null);
                            $this->emcom->persist($entityPunto);
                        }
                        else
                        {
                            throw new \Exception('No se pudo crear el punto, error al cargar el archivo digital');
                        }
                    }
                    else
                    {
                        throw new \Exception('Error, no hay respuesta del WS para almacenar el documento');
                    }

            }   
            $this->emcom->persist($entityPunto);
            
            if($boolArchivoTmComerc)
            {
                $strPrefijoEmpresa  = isset($arrayDatosForm['prefijoEmpresa']) ? $arrayDatosForm['prefijoEmpresa'] : 'MD';
                $strNombreApp       = !empty($arrayDatosForm['strApp']) ? $arrayDatosForm['strApp'] : 'TELCOS';
                $strIdentificacion  = is_object($entityPersonaEmpresaRol->getPersonaId()) ?
                                        $entityPersonaEmpresaRol->getPersonaId()->getIdentificacionCliente() : 'SIN_IDENTIFICACION';
                $arrayPathAdicional   = null;
                $arrayPathAdicional[] = array('key' => $strIdentificacion);

                $objGestionDir      = $this->emcom->getRepository('schemaBundle:AdmiGestionDirectorios')
                                                      ->findOneBy(array('aplicacion'  => $strNombreApp,
                                                                        'empresa'     => $strPrefijoEmpresa));
                if(!is_object($objGestionDir))
                {
                    throw new \Exception('Error, no existe la configuración requerida para almacenar archivos de la aplicación'.$strNombreApp);
                }

                $arrayParamData[]   = array('codigoApp'     => $objGestionDir->getCodigoApp(),
                                            'codigoPath'    => $objGestionDir->getCodigoPath(),
                                            'fileBase64'    => $arrayDatosForm['fileBase64'],
                                            'nombreArchivo' => $entityPunto->getPath(),
                                            'pathAdicional' => $arrayPathAdicional);
                $arrayParamDirectorio = array('data'    => $arrayParamData,
                                              'op'      => 'guardarArchivo',
                                              'user'    => $strUsrCreacion);
                //Llamar al webService
                $arrayOptions      = array(CURLOPT_SSL_VERIFYPEER => false);
                $arrayResponse     = $this->serviceRestClient->postJSON($this->strMSnfs,
                                                                        json_encode($arrayParamDirectorio),
                                                                        $arrayOptions);
                if(isset($arrayResponse))
                {
                    $arrayNFSResp = json_decode($arrayResponse['result'], 1);
                    if($arrayNFSResp['code'] == 200)
                    {
                        $entityPunto->setPath($arrayNFSResp['data'][0]['pathFile']);
                        $entityPunto->setFile(null);
                        $this->emcom->persist($entityPunto);
                    }
                    else
                    {
                        throw new \Exception('No se pudo crear el punto, error al cargar el croquis');
                    }
                }
                else
                {
                    throw new \Exception('Error, no hay respuesta del WS para almacenar el documento');
                }
            }

            if($strNombrePais === 'GUATEMALA')
            {
                $objAdmiNumeracion = $this->emcom->getRepository('schemaBundle:AdmiNumeracion')
                                                 ->findOneBy(array('empresaId'=>$strCodEmpresa ,'codigo'=>'TG', 'estado'=>'Activo' ));
                
                if(is_object($objAdmiNumeracion))
                {
                    $intNuevaSecuencia = ($objAdmiNumeracion->getSecuencia() + 1);
                    $objAdmiNumeracion->setSecuencia($intNuevaSecuencia);
                    $this->emcom->persist($objAdmiNumeracion);                  
                }             
            }            
            
            
            //Se obtiene el rol, validando solo si es Origen Web 
            if($entityPunto->getOrigenWeb() && $entityPunto->getOrigenWeb()=="S")
            {
                $booleanTipoOrigenRol = $this->serviceSecurity->isGranted('ROLE_9-6377');
            }else
            {
                $booleanTipoOrigenRol = true;
            }    
            //Se agrega el origen del punto como característica.
            $strTipoOrigenPunto              = $arrayDatosForm['strTipoOrigen'];
            //Empresa aplica al flujo de origen del punto.
            $arrayParametrosAplicaFact       = array("strProcesoAccion" => "TIPO_ORIGEN_TECNOLOGIA_PUNTO",
                                                     "strEmpresaCod"    => $strCodEmpresa);
            $strAplicaFacturaInstalacion     = $this->serviceUtil->empresaAplicaProceso($arrayParametrosAplicaFact);
            if ($strTipoOrigenPunto && "S" == $strAplicaFacturaInstalacion && $booleanTipoOrigenRol)
            {
                $objInfoCaracteristica   = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                                                       ->findOneBy(array("descripcionCaracteristica" => $strTipoOrigenPunto,
                                                                         "estado"                    => 'Activo'));
                $objCaracteristicaOrigen = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                                                       ->findOneBy(array("descripcionCaracteristica" => 'TIPO_ORIGEN_TECNOLOGIA',
                                                                         "estado"                    => 'Activo'));
                if (!is_null($objInfoCaracteristica) && !is_null($objCaracteristicaOrigen))
                {
                    $objInfoPuntoCaracteristica = new InfoPuntoCaracteristica();
                    $objInfoPuntoCaracteristica->setValor($objInfoCaracteristica->getId());
                    $objInfoPuntoCaracteristica->setCaracteristicaId($objCaracteristicaOrigen);
                    $objInfoPuntoCaracteristica->setPuntoId($entityPunto);
                    $objInfoPuntoCaracteristica->setEstado('Activo');
                    $objInfoPuntoCaracteristica->setFeCreacion($objSysDate);
                    $objInfoPuntoCaracteristica->setUsrCreacion($strUsrCreacion);
                    $objInfoPuntoCaracteristica->setIpCreacion($strClientIp);
                    $this->emcom->persist($objInfoPuntoCaracteristica);

                    //Se inserta el historial por agregar la característica
                    $objInfoPuntoHistorial = new InfoPuntoHistorial();
                    $objInfoPuntoHistorial->setPuntoId($entityPunto);
                    $objInfoPuntoHistorial->setFeCreacion($objSysDate);
                    $objInfoPuntoHistorial->setUsrCreacion($strUsrCreacion);
                    $objInfoPuntoHistorial->setIpCreacion($strClientIp);
                    $objInfoPuntoHistorial->setValor('Se ingresa la característica por el tipo de origen de la tecnología.');
                    $this->emcom->persist($objInfoPuntoHistorial);
                }
                else
                {
                    throw new \Exception("No es posible crear la característica para el tipo de origen del punto.");
                }
            }
            if($arrayDatosForm['nuevoNodoCliente'] == 'SI')
            {
                $objModelo = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                     ->findOneById($arrayDatosForm['tipoedificioid']);
                //verificar que el nombre del elemento no se repita
                $elementoRepetido = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                         ->findOneBy(array("nombreElemento"   => trim(strtoupper($arrayDatosForm['puntoedificio'])),
                                                           "estado"           => array("Activo", "Pendiente", "Factible", "PreFactibilidad"),
                                                           "modeloElementoId" => $objModelo->getId()));
                if($elementoRepetido)
                {
                    throw new \Exception("Ya existe un nodo cliente con el nombre " . $elementoRepetido->getNombreElemento() . " en estado "
                    . $elementoRepetido->getEstado());
                }
                
                $strEstado                 = 'Pendiente';
                $strEstadoSolicitud        = 'PreFactibilidad';
                $boolEsEdifcioConvencional = true;

                if($objModelo->getNombreModeloElemento() == 'NODO SATELITAL')
                {
                    $strEstado                 = 'Activo';
                    $strEstadoSolicitud        = 'FactibilidadEquipos';
                    $boolEsEdifcioConvencional = false;
                }
                
                $objSolicitud = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoSolicitud')->findOneByDescripcionSolicitud('SOLICITUD EDIFICACION');
                $elemento = new InfoElemento();
                $elemento->setDescripcionElemento('Comercial: Creado desde nuevo punto');
                $elemento->setNombreElemento(trim(strtoupper($arrayDatosForm['puntoedificio'])));
                $elemento->setModeloElementoId($objModelo);
                $elemento->setUsrResponsable($strUsrCreacion);
                $elemento->setUsrCreacion($strUsrCreacion);
                $elemento->setFeCreacion($objSysDate);
                $elemento->setIpCreacion($strClientIp);
                $elemento->setEstado($strEstado);

                $this->emInfraestructura->persist($elemento);
                $this->emInfraestructura->flush();

                //historial elemento
                $historialElemento = new InfoHistorialElemento();
                $historialElemento->setElementoId($elemento);
                $historialElemento->setEstadoElemento($strEstado);
                $historialElemento->setObservacion("Creado por nuevo punto " . $arrayDatosForm['login']);
                $historialElemento->setUsrCreacion($strUsrCreacion);
                $historialElemento->setFeCreacion($objSysDate);
                $historialElemento->setIpCreacion($strClientIp);
                $this->emInfraestructura->persist($historialElemento);
                $this->emInfraestructura->flush();

                //info ubicacion
                $parroquia = $this->emInfraestructura->find('schemaBundle:AdmiParroquia', $entityAdmiSector->getParroquiaId());
                $ubicacionElemento = new InfoUbicacion();
                $ubicacionElemento->setLatitudUbicacion($arrayDatosForm['latitudFloat']);
                $ubicacionElemento->setLongitudUbicacion($arrayDatosForm['longitudFloat']);
                $ubicacionElemento->setDireccionUbicacion($arrayDatosForm['direccion']);
                $ubicacionElemento->setAlturaSnm(0);
                $ubicacionElemento->setParroquiaId($parroquia);
                $ubicacionElemento->setUsrCreacion($strUsrCreacion);
                $ubicacionElemento->setFeCreacion($objSysDate);
                $ubicacionElemento->setIpCreacion($strClientIp);
                $this->emInfraestructura->persist($ubicacionElemento);
                $this->emInfraestructura->flush();

                //empresa elemento ubicacion
                $empresaElementoUbica = new InfoEmpresaElementoUbica();
                $empresaElementoUbica->setEmpresaCod($strCodEmpresa);
                $empresaElementoUbica->setElementoId($elemento);
                $empresaElementoUbica->setUbicacionId($ubicacionElemento);
                $empresaElementoUbica->setUsrCreacion($strUsrCreacion);
                $empresaElementoUbica->setFeCreacion($objSysDate);
                $empresaElementoUbica->setIpCreacion($strClientIp);
                $this->emInfraestructura->persist($empresaElementoUbica);
                $this->emInfraestructura->flush();

                //empresa elemento
                $empresaElemento = new InfoEmpresaElemento();
                $empresaElemento->setElementoId($elemento);
                $empresaElemento->setEmpresaCod($strCodEmpresa);
                $empresaElemento->setEstado("Activo");
                $empresaElemento->setUsrCreacion($strUsrCreacion);
                $empresaElemento->setIpCreacion($strClientIp);
                $empresaElemento->setFeCreacion($objSysDate);
                $this->emInfraestructura->persist($empresaElemento);
                $this->emInfraestructura->flush();
                
                $entityDetalleSolicitud = new InfoDetalleSolicitud();
                $entityDetalleSolicitud->setTipoSolicitudId($objSolicitud);
                $entityDetalleSolicitud->setObservacion('Creado por nuevo punto');
                $entityDetalleSolicitud->setUsrCreacion($strUsrCreacion);
                $entityDetalleSolicitud->setFeCreacion($objSysDate);
                $entityDetalleSolicitud->setEstado($strEstadoSolicitud);
                $entityDetalleSolicitud->setElementoId($elemento->getId());
                $this->emInfraestructura->persist($entityDetalleSolicitud);
                $this->emInfraestructura->flush();

                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $entityDetalleSolHist = new InfoDetalleSolHist();
                $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                $entityDetalleSolHist->setIpCreacion($strClientIp);
                $entityDetalleSolHist->setFeCreacion($objSysDate);
                $entityDetalleSolHist->setUsrCreacion($strUsrCreacion);
                $entityDetalleSolHist->setEstado($strEstadoSolicitud);
                $entityDetalleSolHist->setObservacion('Creado por nuevo punto');
                $this->emInfraestructura->persist($entityDetalleSolHist);
                $this->emInfraestructura->flush();
                
                if(!$boolEsEdifcioConvencional)
                {
                    //Si es nodo SATELITAL se crean la referencia PSEUDOPE automaticamente sin pasar por Factibilidad Manual de parte
                    //de GIS
                    $objInfoDetalleElemento = new InfoDetalleElemento();
                    $objInfoDetalleElemento->setEstado('Activo');
                    $objInfoDetalleElemento->setElementoId($elemento->getId());
                    $objInfoDetalleElemento->setDetalleNombre('ADMINISTRA');
                    $objInfoDetalleElemento->setDetalleValor('CLIENTE');
                    $objInfoDetalleElemento->setDetalleDescripcion('ADMINISTRA');
                    $objInfoDetalleElemento->setFeCreacion($objSysDate);
                    $objInfoDetalleElemento->setUsrCreacion($strUsrCreacion);
                    $objInfoDetalleElemento->setIpCreacion($strClientIp);
                    $this->emInfraestructura->persist($objInfoDetalleElemento);
                    $this->emInfraestructura->flush();

                    $objInfoDetalleElemento1 = new InfoDetalleElemento();
                    $objInfoDetalleElemento1->setEstado('Activo');
                    $objInfoDetalleElemento1->setElementoId($elemento->getId());
                    $objInfoDetalleElemento1->setDetalleNombre('TIPO_ELEMENTO_RED');
                    $objInfoDetalleElemento1->setDetalleValor('PSEUDO_PE');
                    $objInfoDetalleElemento1->setDetalleDescripcion('TIPO_ELEMENTO_RED');
                    $objInfoDetalleElemento1->setFeCreacion($objSysDate);
                    $objInfoDetalleElemento1->setUsrCreacion($strUsrCreacion);
                    $objInfoDetalleElemento1->setIpCreacion($strClientIp);
                    $this->emInfraestructura->persist($objInfoDetalleElemento1);
                    $this->emInfraestructura->flush();     

                    $objInfoDetalleElemento = new InfoDetalleElemento();
                    $objInfoDetalleElemento->setEstado('Activo');
                    $objInfoDetalleElemento->setElementoId($elemento->getId());
                    $objInfoDetalleElemento->setDetalleNombre('TIPO_ADMINISTRACION');
                    $objInfoDetalleElemento->setDetalleValor('SATELITAL');
                    $objInfoDetalleElemento->setDetalleDescripcion('TIPO_ADMINISTRACION');
                    $objInfoDetalleElemento->setFeCreacion($objSysDate);
                    $objInfoDetalleElemento->setUsrCreacion($strUsrCreacion);
                    $objInfoDetalleElemento->setIpCreacion($strClientIp);
                    $this->emInfraestructura->persist($objInfoDetalleElemento);
                    $this->emInfraestructura->flush(); 
                }
                
                $this->emInfraestructura->getConnection()->commit();
            }

            // Se verifica que la data del Canal y Punto de venta exitan para crear la característica.
            if(isset($arrayDatosForm['canal']) && isset($arrayDatosForm['punto_venta'])
                 && ($arrayDatosForm['canal'] != '' && $arrayDatosForm['punto_venta'] != ''))
            {
                    // Parámetros para guardar el canal-punto/venta que corresponde al nuevo Punto del cliente
                    $arrayParametros['PUNTO']         = $entityPunto;
                    $arrayParametros['CANAL']         = $arrayDatosForm['canal'];
                    $arrayParametros['PUNTOVENTA']    = $arrayDatosForm['punto_venta'];
                    // Sólo se usa para canales internos
                    $arrayParametros['OFICINAID']     = intval($arrayDatosForm['oficina']);
                    $arrayParametros['CLIENTEIP']     = $strClientIp;
                    $arrayParametros['USRCREACION']   = $strUsrCreacion;
                    $arrayParametros['EMPRESACOD']    = $strCodEmpresa;
                    // Se agrega el canal-punto/venta(Info_Punto_Caracteristica) del punto.
                    $this->guardarCanalPuntoVenta($arrayParametros);
                
            }
            

            // $this->emcom->flush (); // demasiado flush reduce performance

            //Ingresa datos adicionales del punto
            $entityInfoPuntoDatoAdicional  = new InfoPuntoDatoAdicional();
            $entityInfoPuntoDatoAdicional->setDependeDeEdificio($arrayDatosForm['dependedeedificio']);
            if($arrayDatosForm['dependedeedificio'] == 'S')
            {
                if($arrayDatosForm['nuevoNodoCliente'] == 'SI')
                {
                    $entityInfoElemento = $this->emcom->getRepository('schemaBundle:InfoElemento')->find($elemento->getId());
                    $entityInfoPuntoDatoAdicional->setElementoId($entityInfoElemento);
                }
                else
                {
                    $entityInfoElemento = $this->emcom->getRepository('schemaBundle:InfoElemento')->find($arrayDatosForm['puntoedificioid']);
                    
                    if(is_object($entityInfoElemento))
                    {
                        //consulto si es o no un pseudo pe
                        $objDetallePseudo = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy(array('elementoId'   => $entityInfoElemento->getId(),
                                                                                      'detalleNombre'=> 'ADMINISTRA',
                                                                                      'estado'       => 'Activo' ));
                        if (is_object($objDetallePseudo))
                        {                        
                            if ($objDetallePseudo->getDetalleValor() =='EMPRESA')
                            {
                                //aquí valido si es un pseudo Pe y si tiene disponibilidad en el elemento que tiene enlazado
                                $objRelacionPseudo = $this->emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                                             ->findOneBy(array('elementoIdA'   => $arrayDatosForm['puntoedificioid'],
                                                                                               'estado'        => 'Activo' ));

                                if(is_object($objRelacionPseudo))
                                {
                                    $objInterfaceElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                                    ->findOneBy(array('elementoId'   => $objRelacionPseudo->getElementoIdB(),
                                                                                                      'estado'       => 'not connect' ));
                                    if(!is_object($objInterfaceElemento))
                                    {
                                        //no hay interfaces disponibles
                                        throw new \Exception("No se puede crear el punto cliente - El Edificio Pseudo Pe "
                                                             .$entityInfoElemento->getNombreElemento().' no tiene puertos disponibles. ');
                                    }
                                }
                            }
                        }
                    }                    
                    
                    $entityInfoPuntoDatoAdicional->setElementoId($entityInfoElemento);
                }
            }
            
            // Se verifica que exista el parámetro y que sea la empresa TN
            if(isset($arrayDatosForm['prefijoEmpresa']) && $arrayDatosForm['prefijoEmpresa'] == 'TN')
            {
                $entityInfoPuntoDatoAdicional->setEsPadreFacturacion($arrayDatosForm['esPadreFacturacion']);
                
                if($arrayDatosForm['esPadreFacturacion'] == 'S')
                {
                   $entityInfoPuntoDatoAdicional->setEsPadreFacturacion($arrayDatosForm['esPadreFacturacion']);
                   $entityInfoPuntoDatoAdicional->setDatosEnvio('S');
                   $entityInfoPuntoDatoAdicional->setNombreEnvio($arrayDatosForm['nombreDatoEnvio']);
                   
                   if(!empty($arrayDatosForm['sectorDatoEnvio']))
                   {
                       $entityAdmiSector            =  $this->emcom->getRepository('schemaBundle:AdmiSector')
                                                                   ->find($arrayDatosForm['sectorDatoEnvio']);
                       $entityInfoPuntoDatoAdicional->setSectorId($entityAdmiSector);
                   }
                   
                   $entityInfoPuntoDatoAdicional->setDireccionEnvio($arrayDatosForm['direccionDatoEnvio']);
                   $entityInfoPuntoDatoAdicional->setEmailEnvio($arrayDatosForm['correoElectronicoDatoEnvio']);
                   $entityInfoPuntoDatoAdicional->setTelefonoEnvio($arrayDatosForm['telefonoDatoEnvio']);
                }
                if((!empty($arrayDatosForm['razonSocialCltDistribuidor'])    && isset($arrayDatosForm['razonSocialCltDistribuidor'])) &&
                   (!empty($arrayDatosForm['identificacionCltDistribuidor']) && isset($arrayDatosForm['identificacionCltDistribuidor'])))
                {
                    $strRazonSocialCltDistribuidor    = $arrayDatosForm['razonSocialCltDistribuidor'];
                    $strIdentificacionCltDistribuidor = $arrayDatosForm['identificacionCltDistribuidor'];
                    if(!empty($strIdentificacionCltDistribuidor))
                    {
                        $arrayParametros                      = array();
                        $arrayParametros['estado']            = "Activo";
                        $arrayParametros['idEmpresa']         = $strCodEmpresa;
                        $arrayParametros['identificacion']    = $strIdentificacionCltDistribuidor;
                        $arrayParametros['tipo_persona']      = array('cliente','pre-cliente');
                        $arrayParametros['strPrefijoEmpresa'] = isset($arrayDatosForm['prefijoEmpresa']) ? $arrayDatosForm['prefijoEmpresa'] : 'TN';
                        $arrayResultado                       = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                            ->findPersonasPorCriterios($arrayParametros);
                        $arrayRegistros                       = $arrayResultado['registros'];
                        if(!empty($arrayRegistros) && is_array($arrayRegistros))
                        {
                            $strMensaje = "Identificación ingresada, pertenece a un cliente de Telconet, ingresado en TelcoS+.";
                            throw new \Exception($strMensaje);
                        }
                        else
                        {
                            $arrayParametrosCRM   = array("strIdentificacion"  => $strIdentificacionCltDistribuidor);
                            $arrayParametrosWSCrm = array("arrayParametrosCRM" => $arrayParametrosCRM,
                                                          "strOp"              => 'getDatosCliente',
                                                          "strFuncion"         => 'procesar');
                            $arrayRespuestaWSCrm  = $this->serviceComercialCrm->getRequestCRM($arrayParametrosWSCrm);
                            if(!empty($arrayRespuestaWSCrm) && (is_array($arrayRespuestaWSCrm["resultado"]) && 
                               !empty($arrayRespuestaWSCrm["resultado"])))
                            {
                                foreach($arrayRespuestaWSCrm["resultado"] as $arrayItemWSCrm)
                                {
                                    if(!empty($arrayItemWSCrm->strPropuestaCRM) && intval($arrayItemWSCrm->strPropuestaCRM)>0)
                                    {
                                        $strMensaje = "Identificación ingresada, pertenece a un cliente de Telconet, ingresado en TelcoCRM.";
                                        throw new \Exception($strMensaje);
                                    }
                                }
                            }
                        }
                    }
                    $objCaractRazonSocialCltDist      = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                                                             ->findOneBy(array("descripcionCaracteristica" => 'RAZON_SOCIAL_CLT_DISTRIBUIDOR',
                                                                               "estado"                    => 'Activo'));
                    $objCaractIdentificacionCltDist   = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                                                             ->findOneBy(array("descripcionCaracteristica" => 'IDENTIFICACION_CLT_DISTRIBUIDOR',
                                                                               "estado"                    => 'Activo'));
                    if(empty($objCaractRazonSocialCltDist) || !is_object($objCaractRazonSocialCltDist))
                    {
                        throw new \Exception("No se encontró característica RAZON_SOCIAL_CLT_DISTRIBUIDOR, con los parámetros enviados.");
                    }
                    if(empty($objCaractIdentificacionCltDist) || !is_object($objCaractIdentificacionCltDist))
                    {
                        throw new \Exception("No se encontró característica IDENTIFICACION_CLT_DISTRIBUIDOR, con los parámetros enviados.");
                    }
                    $objInfoPuntoCaracteristicaRazonSocial = new InfoPuntoCaracteristica();
                    $objInfoPuntoCaracteristicaRazonSocial->setValor($strRazonSocialCltDistribuidor);
                    $objInfoPuntoCaracteristicaRazonSocial->setCaracteristicaId($objCaractRazonSocialCltDist);
                    $objInfoPuntoCaracteristicaRazonSocial->setPuntoId($entityPunto);
                    $objInfoPuntoCaracteristicaRazonSocial->setEstado('Activo');
                    $objInfoPuntoCaracteristicaRazonSocial->setFeCreacion($objSysDate);
                    $objInfoPuntoCaracteristicaRazonSocial->setUsrCreacion($strUsrCreacion);
                    $objInfoPuntoCaracteristicaRazonSocial->setIpCreacion($strClientIp);
                    $this->emcom->persist($objInfoPuntoCaracteristicaRazonSocial);

                    $objInfoPuntoCaracteristicaIdentificacion = new InfoPuntoCaracteristica();
                    $objInfoPuntoCaracteristicaIdentificacion->setValor($strIdentificacionCltDistribuidor);
                    $objInfoPuntoCaracteristicaIdentificacion->setCaracteristicaId($objCaractIdentificacionCltDist);
                    $objInfoPuntoCaracteristicaIdentificacion->setPuntoId($entityPunto);
                    $objInfoPuntoCaracteristicaIdentificacion->setEstado('Activo');
                    $objInfoPuntoCaracteristicaIdentificacion->setFeCreacion($objSysDate);
                    $objInfoPuntoCaracteristicaIdentificacion->setUsrCreacion($strUsrCreacion);
                    $objInfoPuntoCaracteristicaIdentificacion->setIpCreacion($strClientIp);
                    $this->emcom->persist($objInfoPuntoCaracteristicaIdentificacion);
                }
            }
            else
            {
                $entityInfoPuntoDatoAdicional->setEsPadreFacturacion('S');
            }
            
            $entityInfoPuntoDatoAdicional->setPuntoId($entityPunto);
            $entityInfoPuntoDatoAdicional->setIpCreacion($strClientIp);
            $entityInfoPuntoDatoAdicional->setFeCreacion($objSysDate);
            $entityInfoPuntoDatoAdicional->setUsrCreacion($strUsrCreacion);
            $this->validator->validateAndThrowException($entityInfoPuntoDatoAdicional);
            $this->emcom->persist($entityInfoPuntoDatoAdicional);            

            /* Se envia array de Parametros y se agrega strOpcionPermitida y strPrefijoEmpresa, Prefijo de empresa en sesion para validar
             * que para empresa MD no se obligue el ingreso de al menos 1 correo */
            $arrayParamFormasContac                        = array ();
            $arrayParamFormasContac['strPrefijoEmpresa']   = $arrayDatosForm['prefijoEmpresa'];
            $arrayParamFormasContac['arrayFormasContacto'] = $arrayFormasContacto;
            $arrayParamFormasContac['strOpcionPermitida']  = 'NO';
            $arrayParamFormasContac['intIdPais']           = $intIdPais;
            $arrayParamFormasContac['strNombrePais']       = $strNombrePais;
            $arrayValidaciones   = $this->serviceInfoPersonaFormaContacto->validarFormasContactos($arrayParamFormasContac);
            if($arrayValidaciones)
            {    
                foreach($arrayValidaciones as $key => $mensaje_validaciones)
                {
                    foreach($mensaje_validaciones as $key_msj => $value)
                    {                      
                        $strError = $strError.$value.".\n";                        
                    }
                }
                throw new \Exception("No se puede crear el punto cliente - " . $strError);
            } 
            
            //ReGISTRA LAS FORMAS DE CONTACTO DEL PRE-CLIENTE
            for ($intCont=0;$intCont < count($arrayFormasContacto);$intCont++)
            {
                $entity_persona_forma_contacto = new InfoPuntoFormaContacto();
                $entity_persona_forma_contacto->setValor($arrayFormasContacto[$intCont]['valor']);
                $entity_persona_forma_contacto->setEstado('Activo');
                $entity_persona_forma_contacto->setFeCreacion($objSysDate);
                if (isset($arrayFormasContacto[$intCont]['idFormaContacto']))
                {
                    $entityAdmiFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')
                                                           ->find($arrayFormasContacto[$intCont]['idFormaContacto']);
                }
                else
                {
                    $entityAdmiFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')
                                                           ->findPorDescripcionFormaContacto($arrayFormasContacto[$intCont]['formaContacto']);
                }
                $entity_persona_forma_contacto->setFormaContactoId($entityAdmiFormaContacto);
                $entity_persona_forma_contacto->setIpCreacion($strClientIp);
                $entity_persona_forma_contacto->setPuntoId($entityPunto);
                $entity_persona_forma_contacto->setUsrCreacion($strUsrCreacion);
                $this->emcom->persist($entity_persona_forma_contacto);                
            }                 

            if($arrayDatosForm['strSolInfCli'] && $arrayDatosForm['strSolInfCli'] === 'S')
            {
                $objCaracteristicaSolInfCli = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                                                          ->findOneBy(array("descripcionCaracteristica" => 'linkDatosBancarios',
                                                                            "estado"                    => 'Activo'));
                if (!is_null($objCaracteristicaSolInfCli))
                {
                    $objInfoPuntoCaracteristica = new InfoPuntoCaracteristica();
                    $objInfoPuntoCaracteristica->setValor($arrayDatosForm['strSolInfCli']);
                    $objInfoPuntoCaracteristica->setCaracteristicaId($objCaracteristicaSolInfCli);
                    $objInfoPuntoCaracteristica->setPuntoId($entityPunto);
                    $objInfoPuntoCaracteristica->setEstado('Activo');
                    $objInfoPuntoCaracteristica->setFeCreacion($objSysDate);
                    $objInfoPuntoCaracteristica->setUsrCreacion($strUsrCreacion);
                    $objInfoPuntoCaracteristica->setIpCreacion($strClientIp);
                    $this->emcom->persist($objInfoPuntoCaracteristica);

                    //Se inserta el historial por agregar la característica
                    $objInfoPuntoHistorial = new InfoPuntoHistorial();
                    $objInfoPuntoHistorial->setPuntoId($entityPunto);
                    $objInfoPuntoHistorial->setFeCreacion($objSysDate);
                    $objInfoPuntoHistorial->setUsrCreacion($strUsrCreacion);
                    $objInfoPuntoHistorial->setIpCreacion($strClientIp);
                    $objInfoPuntoHistorial->setValor('Se ingresa la característica para solicitar información al cliente');
                    $this->emcom->persist($objInfoPuntoHistorial);
                }
            }

          
            if(isset($arrayDatosForm['strTipo']) &&
                !empty($arrayDatosForm['strTipo']) &&
                isset($arrayDatosForm['prefijoEmpresa']) &&
                $arrayDatosForm['prefijoEmpresa'] == 'MD')
            {
                $strTipoProceso = $arrayDatosForm['strTipo'] == 'continuo' ? 'S' : 'N';

                $objProcesoCaracteristica = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                    ->findOneBy(
                        array(
                            "descripcionCaracteristica" => 'ES_PROCESO_CONTINUO',
                            "estado"                    => 'Activo'
                        )
                    );

                if(empty($objProcesoCaracteristica) || !is_object($objProcesoCaracteristica))
                {
                    throw new \Exception("No se encontró característica ES_PROCESO_CONTINUO, con los parámetros enviados.");
                }

                $objInfoPuntoCaracteristica = new InfoPuntoCaracteristica();
                $objInfoPuntoCaracteristica->setValor($strTipoProceso);
                $objInfoPuntoCaracteristica->setCaracteristicaId($objProcesoCaracteristica);
                $objInfoPuntoCaracteristica->setPuntoId($entityPunto);
                $objInfoPuntoCaracteristica->setEstado('Activo');
                $objInfoPuntoCaracteristica->setFeCreacion($objSysDate);
                $objInfoPuntoCaracteristica->setUsrCreacion($strUsrCreacion);
                $objInfoPuntoCaracteristica->setIpCreacion($strClientIp);

                $this->emcom->persist($objInfoPuntoCaracteristica);
            }

            if(isset($arrayDatosForm['strNombreOrigen']) &&
                !empty($arrayDatosForm['strNombreOrigen']) &&
                isset($arrayDatosForm['prefijoEmpresa']) &&
                $arrayDatosForm['prefijoEmpresa'] == 'MD' &&
                isset($arrayDatosForm['strTipo']) &&
                $arrayDatosForm['strTipo'] == 'continuo')
            {

                $objOrigenCaracteristica = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                    ->findOneBy(
                        array(
                            "descripcionCaracteristica" => 'ORIGEN_REQUERIMIENTO',
                            "estado"                    => 'Activo'
                        )
                    );

                if(empty($objOrigenCaracteristica) || !is_object($objOrigenCaracteristica))
                {
                    throw new \Exception("No se encontró característica ORIGEN_REQUERIMIENTO, con los parámetros enviados.");
                }

                $objInfoPuntoCaracteristica = new InfoPuntoCaracteristica();
                $objInfoPuntoCaracteristica->setValor($arrayDatosForm['strNombreOrigen']);
                $objInfoPuntoCaracteristica->setCaracteristicaId($objOrigenCaracteristica);
                $objInfoPuntoCaracteristica->setPuntoId($entityPunto);
                $objInfoPuntoCaracteristica->setEstado('Activo');
                $objInfoPuntoCaracteristica->setFeCreacion($objSysDate);
                $objInfoPuntoCaracteristica->setUsrCreacion($strUsrCreacion);
                $objInfoPuntoCaracteristica->setIpCreacion($strClientIp);

                $this->emcom->persist($objInfoPuntoCaracteristica);
            }

            if(isset($arrayDatosForm['strNombrePuntoAtencion']) &&
                !empty($arrayDatosForm['strNombrePuntoAtencion']) &&
                isset($arrayDatosForm['prefijoEmpresa']) &&
                $arrayDatosForm['prefijoEmpresa'] == 'MD' &&
                isset($arrayDatosForm['strTipo']) &&
                $arrayDatosForm['strTipo'] == 'continuo')
            {

                $objPuntoAtencionCaracteristica  = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                    ->findOneBy(
                        array(
                            "descripcionCaracteristica" => 'PUNTO_ATENCION',
                            "estado"                    => 'Activo'
                        )
                    );
            if(empty($objPuntoAtencionCaracteristica) || !is_object($objPuntoAtencionCaracteristica))
                {
                    throw new \Exception("No se encontró característica PUNTO_ATENCION, con los parámetros enviados.");
                }

                $objInfoPuntoCaracteristica = new InfoPuntoCaracteristica();
                $objInfoPuntoCaracteristica->setValor($arrayDatosForm['strNombrePuntoAtencion']);
                $objInfoPuntoCaracteristica->setCaracteristicaId($objPuntoAtencionCaracteristica);
                $objInfoPuntoCaracteristica->setPuntoId($entityPunto);
                $objInfoPuntoCaracteristica->setEstado('Activo');
                $objInfoPuntoCaracteristica->setFeCreacion($objSysDate);
                $objInfoPuntoCaracteristica->setUsrCreacion($strUsrCreacion);
                $objInfoPuntoCaracteristica->setIpCreacion($strClientIp);

                $this->emcom->persist($objInfoPuntoCaracteristica);
            }

            if(isset($arrayDatosForm['intIdPuntoAnterior']) &&
                !empty($arrayDatosForm['intIdPuntoAnterior']) &&
                isset($arrayDatosForm['prefijoEmpresa']) &&
                $arrayDatosForm['prefijoEmpresa'] == 'MD' &&
                isset($arrayDatosForm['strTipo']) &&
                $arrayDatosForm['strTipo'] == 'continuo')
            {

                $objPuntoOrigenCaracteristica  = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                                                                ->findOneBy(
                                                                    array(
                                                                        "descripcionCaracteristica" => 'PUNTO_ORIGEN_CREACION',
                                                                        "estado"                    => 'Activo'
                                                                    )
                                                                );

                if(empty($objPuntoOrigenCaracteristica) || !is_object($objPuntoOrigenCaracteristica))
                {
                    throw new \Exception("No se encontró característica PUNTO_ORIGEN_CREACION, con los parámetros enviados.");
                }

                $objInfoPuntoCaracteristica = new InfoPuntoCaracteristica();
                $objInfoPuntoCaracteristica->setValor($arrayDatosForm['intIdPuntoAnterior']);
                $objInfoPuntoCaracteristica->setCaracteristicaId($objPuntoOrigenCaracteristica);
                $objInfoPuntoCaracteristica->setPuntoId($entityPunto);
                $objInfoPuntoCaracteristica->setEstado('Activo');
                $objInfoPuntoCaracteristica->setFeCreacion($objSysDate);
                $objInfoPuntoCaracteristica->setUsrCreacion($strUsrCreacion);
                $objInfoPuntoCaracteristica->setIpCreacion($strClientIp);

                $this->emcom->persist($objInfoPuntoCaracteristica);

                $objPuntoEstadoProcesoCaracteristica  = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                                                                        ->findOneBy(
                                                                            array(
                                                                                "descripcionCaracteristica" => 'ESTADO_PROCESO_PUNTO',
                                                                                "estado"                    => 'Activo'
                                                                            )
                                                                        );

                if(empty($objPuntoEstadoProcesoCaracteristica) || !is_object($objPuntoEstadoProcesoCaracteristica))
                {
                    throw new \Exception("No se encontró característica ESTADO_PROCESO_PUNTO.");
                }
                $objInfoPuntoCaracteristica = new InfoPuntoCaracteristica();
                $objInfoPuntoCaracteristica->setValor('Pendiente');
                $objInfoPuntoCaracteristica->setCaracteristicaId($objPuntoEstadoProcesoCaracteristica);
                $objInfoPuntoCaracteristica->setPuntoId($entityPunto);
                $objInfoPuntoCaracteristica->setEstado('Activo');
                $objInfoPuntoCaracteristica->setFeCreacion($objSysDate);
                $objInfoPuntoCaracteristica->setUsrCreacion($strUsrCreacion);
                $objInfoPuntoCaracteristica->setIpCreacion($strClientIp);

                $this->emcom->persist($objInfoPuntoCaracteristica);
            }
            $this->emcom->flush (); // demasiado flush reduce performance, es mejor un solo flush luego de un grupo de operaciones
            $this->emcom->getConnection()->commit();

            return $entityPunto;
        }
        catch (\Exception $e) {
            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollBack();
            }
            $this->emcom->getConnection()->close();
            
            if($strCanal=="CANAL_EXTRANET")
            {
               
                $arrayResponse["strStatus"]  = "Error";
                $arrayResponse["strMensaje"] = $e->getMessage();
                
                return $arrayResponse;
            }
            
            
            if($arrayDatosForm ['origen_web'] === 'N')
            {
                
                $strMensaje = 'Error al ingresar Punto: ';
                $arrayParametrosLog['enterpriseCode']   = $strCodEmpresa;
                $arrayParametrosLog['logType']          = "1";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = "InfoPuntoService";
                $arrayParametrosLog['appClass']         = "InfoPuntoService";
                $arrayParametrosLog['appMethod']        = "crearPunto";
                $arrayParametrosLog['appAction']        = "crearPunto";
                $arrayParametrosLog['messageUser']      = "ERROR";
                $arrayParametrosLog['status']           = "Error";
                $arrayParametrosLog['descriptionError'] = $strMensaje.$e->getMessage();
                $arrayParametrosLog['inParameters']     = json_encode($arrayParametros);
                $arrayParametrosLog['creationUser']     = $strUsrCreacion;    

                $this->serviceUtil->insertLog($arrayParametrosLog);
            }            
            throw $e;
        }
    }

    /**
     * Función que obtiene los tipos de orígenes para un punto.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 28-01-2019
     */
    public function getTipoOrigenPunto($arrayParametros)
    {
        $arrayParametrosAplicaTipoOrigen = array("strProcesoAccion" => "TIPO_ORIGEN_TECNOLOGIA_PUNTO",
                                                 "strEmpresaCod"    => $arrayParametros["strEmpresaCod"]);
        $strAplicaTipoOrigenTecnologia   = $this->serviceUtil->empresaAplicaProceso($arrayParametrosAplicaTipoOrigen);
        if ("S" != $strAplicaTipoOrigenTecnologia)
        {
            return array();
        }
        $objDQL            = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->getDql('COMBO_TIPO_ORIGEN_TECNOLOGIA_PUNTO',
                                                      'COMERCIAL',
                                                      'COMBO_TIPO_ORIGEN_TECNOLOGIA_PUNTO',
                                                      null,
                                                      null,
                                                      null,
                                                      null,
                                                      null,
                                                      null,
                                                      strval($arrayParametros['strEmpresaCod']));
        $arrayParamtroDet  = $objDQL->getResult();
        return $arrayParamtroDet;
    }

    /**
     * Función que obtiene el tipo de origen del punto.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 28-01-2019
     */
    public function getCaractTipoOrigenPuntoxIdPunto($arrayParametros)
    {
        $arrayRespuesta = array("total" => 0,
                                "valores" => array("strDescripcion"               => null,
                                                   "intAdmiCaracteristicaId"      => null,
                                                   "strDescripcionCaracteristica" => null,
                                                   "intInfoPuntoCaracteristicaId" => null));
        $arrayParametrosAplicaFact       = array("strProcesoAccion" => "TIPO_ORIGEN_TECNOLOGIA_PUNTO",
                                                 "strEmpresaCod"    => $arrayParametros["strEmpresaCod"]);
        $strAplicaFacturaInstalacion     = $this->serviceUtil->empresaAplicaProceso($arrayParametrosAplicaFact);
        if ("S" != $strAplicaFacturaInstalacion)
        {
            return $arrayRespuesta;
        }
        //Se obtiene la característica
        $objCaractTipoOrigen        = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                                                  ->findOneBy(array("descripcionCaracteristica" => "TIPO_ORIGEN_TECNOLOGIA",
                                                                    "estado"                    => "Activo"));
        $objInfoPuntoCaracteristica = $this->emcom->getRepository("schemaBundle:InfoPuntoCaracteristica")
                                                  ->findOneBy(array("puntoId"          => $arrayParametros["objPuntoId"],
                                                                    "caracteristicaId" => $objCaractTipoOrigen,
                                                                    "estado"           => "Activo"));
        if (!is_null($objInfoPuntoCaracteristica) && is_object($objInfoPuntoCaracteristica))
        {
            $objAdmiCaracteristica = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                                                 ->findOneById($objInfoPuntoCaracteristica->getValor());
            $objDQL            = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->getDql('COMBO_TIPO_ORIGEN_TECNOLOGIA_PUNTO',
                                                          'COMERCIAL',
                                                          'COMBO_TIPO_ORIGEN_TECNOLOGIA_PUNTO',
                                                          null,
                                                          null,
                                                          $objAdmiCaracteristica->getDescripcionCaracteristica(),
                                                          null,
                                                          null,
                                                          null,
                                                          strval($arrayParametros['strEmpresaCod']));
            $arrayParametroDet  = $objDQL->getResult();
        }
        else
        {
            return $arrayRespuesta;
        }
        return array("total" => 1, "valores" => array("strDescripcion"               => $arrayParametroDet[0]["valor1"],
                                                      "intAdmiCaracteristicaId"      => $objAdmiCaracteristica->getId(),
                                                      "strDescripcionCaracteristica" => $objAdmiCaracteristica->getDescripcionCaracteristica(),
                                                      "intInfoPuntoCaracteristicaId" => $objInfoPuntoCaracteristica->getId()));
    }
    /**
     * Documentación para el método 'guardarCanalPuntoVenta'.
     * 
     * Funcion que procesa la inserción de la característica del canal y el punto de venta del punto del cliente
     * Relación Canales vs Puntos de Venta según el registro AdmiParametroDet:
     *
     * @param InfoPunto     $arrayParametros['PUNTO']       Punto al que se asociará la caracteristica PUNTO_DE_VENTA_CANAL
     * @param String        $arrayParametros['CANAL']       Identificador del Canal
     * @param String        $arrayParametros['PUNTOVENTA']  Identificador del Punto de Venta
     * @param Integer       $arrayParametros['OFICINAID']   Id de la Oficina
     * @param String        $arrayParametros['CLIENTEIP']   Dirección IP de donde se origina el guardado de la característica del punto
     * @param String        $arrayParametros['USRCREACION'] Usuario en sesión
     * @param String        $arrayParametros['EMPRESACOD']  Código de la empresa.
     * 
     * @return No
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     * 
     * @author Joel Broncano <jbroncano@telconet.ec> se modifica la conexcion  para guardar el parametro detalle
     * @version 1.0 08-03-2023
     */
    public function guardarCanalPuntoVenta($arrayParametros)
    {
        $entityPunto   = $arrayParametros['PUNTO']; 
        $strCanal      = $arrayParametros['CANAL']; 
        $strPuntoVenta = $arrayParametros['PUNTOVENTA']; 
        $intOficina    = $arrayParametros['OFICINAID']; 
        $clientIp      = $arrayParametros['CLIENTEIP']; 
        $usrCreacion   = $arrayParametros['USRCREACION']; 
        $empresaId     = $arrayParametros['EMPRESACOD'];
        
        // Se obtiene la Característica PUNTO_DE_VENTA_CANAL
        $strCaracteristica    = 'PUNTO_DE_VENTA_CANAL';
        $strEstado            = 'Activo';
        $entityCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, $strEstado);
        if($entityCaracteristica == null)
        {
            throw new \Exception("Característica PUNTO_DE_VENTA_CANAL no existe.");
        }
        // Se crea la característica del punto en base a la oficina en sesión
        if (!($arrayParametros['ESUPDATE']) || (($arrayParametros['ESUPDATE']) && $arrayParametros['ESUPDATE'] == "NO"))
        {
            $entityPuntoCarac = new InfoPuntoCaracteristica();
            $entityPuntoCarac->setPuntoId($entityPunto);
            $entityPuntoCarac->setCaracteristicaId($entityCaracteristica);
            $entityPuntoCarac->setEstado('Activo');
            $entityPuntoCarac->setFeCreacion(new \DateTime('now'));
            $entityPuntoCarac->setIpCreacion($clientIp);
            $entityPuntoCarac->setUsrCreacion($usrCreacion);    
        }
        else
        {
            $arrayParametros2           = array('entityPunto'                  => $entityPunto,
                                                'strDescripcionCaracteristica' => 'PUNTO_DE_VENTA_CANAL',
                                                'strEstado'                    => 'Activo');
            $entityPuntoCarac = $this->emcom->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                            ->getOnePuntoCaracteristica($arrayParametros2);

        }
            
        // Se agrega el valor del punto de venta a la característica del punto
        if($strCanal == 'CANAL_INTERNO')
        {
            // Para el canal interno se ubica el punto de venta basado en la oficina en sesión
            $strParam           = 'CANALES_PUNTO_VENTA';
            $arrayParamCab      = array('nombreParametro' => $strParam, 'modulo' => 'COMERCIAL', 'estado' => 'Activo');
            $entityParametroCab = $this->emcom->getRepository('schemaBundle:AdmiParametroCab')->findOneBy($arrayParamCab);

            if($entityParametroCab == null)
            {
                throw new \Exception("Parámetro CANALES_PUNTO_VENTA no existe.");
            }
            $entityEmpresa = $this->emcom->getRepository('schemaBundle:InfoEmpresaGrupo')->find($empresaId);
            $arrayOficGrup = array('id' => $intOficina, 'empresaId' => $entityEmpresa);
            // Consultar la Oficina por la Empresa en sesión.
            $entityOficina = $this->emcom->getRepository('schemaBundle:InfoOficinaGrupo')->findOneBy($arrayOficGrup);
            $strValor5     = $entityOficina->getNombreOficina();
            // Se obtiene el Punto de Venta Interno que corresponde a la Oficina en sesión.
            $arrayParamDet = array('parametroId' => $entityParametroCab, 'valor3' => 'CANAL_INTERNO', 'valor5' => $strValor5, 'estado' => 'Activo');
            $objPuntoVenta = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')->findOneBy($arrayParamDet);
            
            if($objPuntoVenta)
            {
                // Valor1 => Identificador del Punto de Venta.
                $entityPuntoCarac->setValor($objPuntoVenta->getValor1());
            }
            else
            {
                // Se crea un Punto de Venta referente a la oficina en sesión
                $entityAdmiParametroDet = new AdmiParametroDet();
                $entityAdmiParametroDet->setParametroId($entityParametroCab);
                $entityAdmiParametroDet->setDescripcion('PUNTO DE VENTA ' . $strValor5);
                $entityAdmiParametroDet->setIpCreacion($clientIp);
                
                // Valor1 => Identificador del Punto de Venta.
                // Valor2 => Descriptivo del Punto de Venta.
                // Valor3 => Identificador del Canal.
                // Valor4 => Descriptivo del Canal.
                // Valor5 => Nombre de la oficina asociada al punto de venta.
                $strOficina = str_replace(' - AG ', ' ', $strValor5);
                $entityAdmiParametroDet->setValor1('INTERNO_OFICINA_' . str_replace(' ', '_', $strOficina));
                $entityAdmiParametroDet->setValor2("OFICINA $strOficina");
                $entityAdmiParametroDet->setValor3('CANAL_INTERNO');
                $entityAdmiParametroDet->setValor4('INTERNO');
                $entityAdmiParametroDet->setValor5($strValor5);
                
                $entityAdmiParametroDet->setEstado('Activo');
                $entityAdmiParametroDet->setFeCreacion(new \DateTime('now'));
                $entityAdmiParametroDet->setUsrCreacion($usrCreacion);
                $this->emGeneral->persist($entityAdmiParametroDet);

                $entityPuntoCarac->setValor($entityAdmiParametroDet->getValor1());
            }
        }
        else
        {
            $entityPuntoCarac->setValor($strPuntoVenta);
        }
        $this->emcom->persist($entityPuntoCarac);
    }
    
    public function obtenerDatosPunto($idPunto)
    {
        /* @var $entity \telconet\schemaBundle\Entity\InfoPunto */
        /* @var $entityPuntoDatoAdicional \telconet\schemaBundle\Entity\InfoPuntoDatoAdicional */
        /* @var $cliente \telconet\schemaBundle\Entity\InfoPersona */
        /* @var $entitySector \telconet\schemaBundle\Entity\AdmiSector */
        /* @var $entityParroquia \telconet\schemaBundle\Entity\AdmiParroquia */
        $entity = $this->emcom->getRepository('schemaBundle:InfoPunto')->find($idPunto);
        $entityPuntoDatoAdicional = $this->emcom->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($idPunto);
        $entityCliente = $this->emcom->getRepository('schemaBundle:InfoPersona')->find($entity->getPersonaEmpresaRolId()->getPersonaId());
        $entitySector = $this->emcom->getRepository('schemaBundle:AdmiSector')->find($entity->getSectorId()->getId());
        $entityParroquia = $this->emcom->getRepository('schemaBundle:AdmiParroquia')->find($entitySector->getParroquiaId()->getId());
        return array(
                        'punto' => $entity,
                        'puntoDatoAdicional' => $entityPuntoDatoAdicional,
                        'cliente' => $entityCliente,
                        'sector' => $entitySector,
                        'parroquia' => $entityParroquia,
//                         'cantonId' => $entityParroquia->getCantonId()->getId(),
//                         'parroquiaId' => $entitySector->getParroquiaId()->getId()
        );
    }

    /**
     * Bug:Se corrige el filtro para el listado de tm-comercial aparezcan los puntos pendientes al momento de crear un servicio 
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 18-12-2018
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 16-06-2021 - Se pone consulta de parametro fuera del for
     * 
     */    
    public function obtenerDatosPuntosCliente($codEmpresa, $idPersona, $rol, $isFormasContacto = FALSE, $isIdFormaContacto = FALSE, $isServicios = FALSE)
    {
        /* @var $repoInfoPunto \telconet\schemaBundle\Repository\InfoPuntoRepository */
        /* @var $entity \telconet\schemaBundle\Entity\InfoPunto */
        /* @var $entityPuntoDatoAdicional \telconet\schemaBundle\Entity\InfoPuntoDatoAdicional */
        /* @var $cliente \telconet\schemaBundle\Entity\InfoPersona */
        /* @var $entitySector \telconet\schemaBundle\Entity\AdmiSector */
        /* @var $entityParroquia \telconet\schemaBundle\Entity\AdmiParroquia */
        $repoInfoPunto     = $this->emcom->getRepository('schemaBundle:InfoPunto');
        $puntos            = $repoInfoPunto->findPtosPorEmpresaPorClientePorRol($codEmpresa, $idPersona, null, $rol, null, null, null, null);
        $arreglo           = array();
        $intCont           = 0;
        $intContCancelados = 0;
        $intTotal          = 0;

        $arrayParametroPunto = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAMETROS_TM_COMERCIAL', 
                                                        'COMERCIAL', 
                                                        '',
                                                        '',
                                                        'CANTIDAD DE PUNTOS A MOSTRAR', 
                                                        '',
                                                        '',
                                                        '', 
                                                        '', 
                                                        '18');
        $intCantidadPunto = 20;
        if (($arrayParametroPunto) && ($arrayParametroPunto['valor1']))
        {
            $intCantidadPunto = $arrayParametroPunto['valor1'];
        } 

        foreach ($puntos['registros'] as $punto)
        {                                                           
            $intTotal++;
            $entity = $this->emcom->getRepository('schemaBundle:InfoPunto')->find($punto['id']);
            $entityPuntoDatoAdicional = $this->emcom->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($punto['id']);
            if ($isFormasContacto)
            {
                $formasContacto = $this->obtenerFormasContactoPorPunto($entity->getId(), null, null, $isIdFormaContacto);
            }
            if ($entity->getEstado() == 'Cancelado')
            {
               $intContCancelados++;   
            }
            $arrayEstados = array('Activo', 'In-Corte', 'Pendiente');
            if ( in_array($entity->getEstado(), $arrayEstados) )
            {
                $intCont++;
                
                $arreglo[] = array(
                                'idPto'             => $entity->getId(),
                                'personaId'         => $idPersona,
                                'rol'               => $rol,
                                'login'             => $entity->getLogin(),
                                'ptoCoberturaId'    => $entity->getPuntoCoberturaId()->getId(),
                                'cantonId'          => $entity->getSectorId()->getParroquiaId()->getCantonId()->getId(),
                                'parroquiaId'       => $entity->getSectorId()->getParroquiaId()->getId(),
                                'sectorId'          => $entity->getSectorId()->getId(),
                                'esedificio'        => $entityPuntoDatoAdicional->getEsEdificio(),
                                'nombreEdificio'    => $entityPuntoDatoAdicional->getNombreEdificio(),
                                'dependedeedificio' => $entityPuntoDatoAdicional->getDependeDeEdificio(),
                                'puntoedificioid'   => $entityPuntoDatoAdicional->getPuntoEdificioId(),
                                'tipoNegocioId'     => $entity->getTipoNegocioId()->getId(),
                                'tipoUbicacionId'   => $entity->getTipoUbicacionId()->getId(),
                                'nombrepunto'       => $entity->getNombrePunto(),
                                'direccion'         => $entity->getDireccion(),
                                'descripcionpunto'  => $entity->getDescripcionPunto(), // referencia
                                'observacion'       => $entity->getObservacion(),
                                'file'              => $entity->getFile(), // croquis
                                'fileDigital'       => $entity->getFileDigital(),
                                'latitudFloat'      => $entity->getLatitud(),
                                'longitudFloat'     => $entity->getLongitud(),
                                'loginVendedor'     => $entity->getUsrVendedor(),
                                'estado'            => $entity->getEstado(),
                            ) + ($isFormasContacto ?
                                array('formasContacto' => $formasContacto['registros']) : array())
                            + ($isServicios ?
                                array('servicios'      => $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                          ->findDatosResumenPorPunto($entity->getId())) : array());
                if ($intCont >= $intCantidadPunto)
                {
                    break;
                }

            }
        }
        return $arreglo;
    }
    
    /*public function obtenerPunto($id)
    {
        // TODO: metodo para devolver lista de puntos de un pre-cliente, con datos suficientes para Telcos Mobile
        $entity = $this->emcom->getRepository('schemaBundle:InfoPunto')->find($id);
        $entityPuntoDatoAdicional = $this->emcom->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($id);
        $cliente = $this->emcom->getRepository('schemaBundle:InfoPersona')->find($entity->getPersonaEmpresaRolId()->getPersonaId());
        $entitySector = $this->emcom->getRepository('schemaBundle:AdmiSector')->find($entity->getSectorId()->getId());
        $entityParroquia = $this->emcom->getRepository('schemaBundle:AdmiParroquia')->find($entitySector->getParroquiaId()->getId());
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
        return $this->render('comercialBundle:infopunto:edit.html.twig', array(
                        'entity' => $entity,
                        'entityPuntoDatoAdicional' => $entityPuntoDatoAdicional,
                        'edit_form' => $editForm->createView(),
                        'login' => $entity->getLogin(),
                        'cliente' => $cliente,
                        'rol' => $rol,
                        'cantonId' => $entityParroquia->getCantonId()->getId(),
                        'parroquiaId' => $entitySector->getParroquiaId()->getId()
        ));

        $array['idPto'] = ;
        $array['personaId'] = ;
        $array['rol'] = ;
        $array['login'] = ;
        $array['ptoCoberturaId'] = ;
        $array['sectorId'] = ;
        $array['esedificio'] = ;
        $array['nombreEdificio'] = ;
        $array['dependedeedificio'] = ;
        $array['puntoedificioid'] = ;
        $array['tipoNegocioId'] = ;
        $array['tipoUbicacionId'] = ;
        $array['nombrepunto'] = ;
        $array['direccion'] = ;
        $array['descripcionpunto'] = ; // referencia
        $array['observacion'] = ;
        $array['file'] = ; // croquis
        $array['fileDigital'] = ;
        $array['latitudFloat'] = ;
        $array['longitudFloat'] = ;
        $array['loginVendedor'] = ;
        $array['estado'] = ;
        
    }*/

    /**
     * Devuelve las formas de contacto de un punto
     * @param integer $idPunto
     * @param integer $limit
     * @param integer $start
     * @param boolean $isIdFormaContacto TRUE si se debe agregar al array el id de la forma contacto (default FALSE)
     * @return array con total(integer) y registros(array)
     */
    public function obtenerFormasContactoPorPunto($idPunto, $limit, $start, $isIdFormaContacto = FALSE)
    {
        $resultado = $this->emcom->getRepository('schemaBundle:InfoPuntoFormaContacto')->findPorEstadoPorPunto($idPunto, 'Activo', $limit, $start);
        $datos = $resultado['registros'];
        $arreglo = array();
        /* @var $value \telconet\schemaBundle\Entity\InfoPuntoFormaContacto */
        foreach ($datos as $value):
        $arreglo[] = array(
                        'idPersonaFormaContacto' => $value->getId(),
                        'idPersona' => $value->getPuntoId()->getId(),
                        'formaContacto' => $value->getFormaContactoId()->getDescripcionFormaContacto(),
                        'valor' => $value->getValor()
        ) + ($isIdFormaContacto ?
                array('idFormaContacto' => $value->getFormaContactoId()->getId()) : array());
        endforeach;
        return array('total' => $resultado['total'], 'registros' => $arreglo);
    }
    
    /**
    * Documentación para el método 'actualizaPtoCliente'.
    *
    * Actualiza el tipo de negocio de un punto.
    *
    *
    * @return json_encode $response retorna (succes true|false), (msg error|correcto)
    *
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 05-06-2014
    */
    public function actualizaPtoCliente($puntoInfo) {
	    $success = false;
	    $info_punto = $this->emcom->getRepository('schemaBundle:InfoPunto')->find($puntoInfo['idPunto']);
	   
	    if (!$info_punto) {
		$msg = "No fue encontrado el punto...";
		$success = false;
	    } else {
                $admi_tipo_negocio = $this->emcom->getRepository('schemaBundle:AdmiTipoNegocio')->find($puntoInfo['idTipoNegocio']);
                $info_punto->setTipoNegocioId($admi_tipo_negocio);
		//$info_punto->setComentarioPago(trim($Observacion));
		//$info_punto->setUsrUltMod($usuario);
		$this->emcom->persist($info_punto);
		$this->emcom->flush();
		$msg = "Se Guardo Correctamente.";
		$success = true;		
	    }
            
            $result = array('succes' => $success, 'msg' => $msg);	    
	    
	    return $result;	    
	}

    /**
     * Documentación para el método 'permiteAnularPtoCliente'.
     *
     * Verifica si es posible anular un punto
     *
     * @param integer $idPunto
     * @return string $response retorna 'si' si es posible eliminar punto, 'no' si no es posible anular punto
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 02-10-2014
     */
    public function permiteAnularPtoCliente($idPunto)
    {
        $arrayEstadosServicios = array('Eliminado', 'Anulado', 'Rechazada');
        $arrayServiciosPorPunto = $this->emcom->getRepository('schemaBundle:InfoServicio')->findByPuntoId($idPunto);
        $strPermiteAnularPunto = 'si';
        if($arrayServiciosPorPunto)
        {
            foreach($arrayServiciosPorPunto as $servicio):
                if(!in_array($servicio->getEstado(), $arrayEstadosServicios))
                {
                    $strPermiteAnularPunto = 'no';
                }
            endforeach;
        }
        return $strPermiteAnularPunto;
    }
   
    /**
     * Documentación para el método 'guardaCambioVendedor'.
     *
     * Funcion que guarda formulario de Cambio de Vendedor y Contactos
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 10-03-2015    
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>     
     * @version 1.1 01-09-2015  
     * Se agrega Validacion a nivel de service para el correcto ingreso de las formas de Contacto
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.2 21-10-2015
     * @since 1.1
     * Se separa la lógica de programación, SI se recibe las Formas de Contacto se concluye que se usó el perfil 'Cambio Vendedor Formas Contacto' 
     * por tanto sólo guardará esta información.
     * Si NO se reciben las formas de contacto se deduce que el perfil utilizado es 'Cambio Vendedor Punto' y sólo guardarán los datos del punto 
     * y el cambio de vendedor.
     * El usuario en sesión deberá tener asignado sólo uno de los perfiles, en caso de tener ambos por jerarquía se usará el perfil 
     * 'Cambio Vendedor Punto'
     *  
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.2 08-11-2016
     * Se envia array de Parametros $arrayParamFormasContac a la funcion "validarFormasContactos" se agrega strOpcionPermitida y strPrefijoEmpresa, 
     * Prefijo de empresa en sesion para validar que para empresa MD no se obligue el ingreso de al menos 1 correo.
     *     
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.3 04-07-2017
     * Se agregan las variables strNombrePais e intIdPais para realizar las validaciones de PANAMÁ
     * 
     * @param string  $objInfoPunto           
     * @param string  $strUsrCreacion
     * @param string  $strClientIp
     * @param array   $arrayDatosForm    
     * @throws Exception
     * @return \telconet\schemaBundle\Entity\InfoPunto
     */
    public function guardaCambioVendedor($objInfoPunto, $strUsrCreacion, $strClientIp, $arrayDatosForm)
    {                                
        $objFechaActualiza = new \DateTime('now');
        $strEstadoActivo   = 'Activo';
        $strEstadoInactivo ='Inactivo';
        $strError          = '';
        $this->emcom->getConnection()->beginTransaction();
        $strNombrePais     = $arrayDatosForm['strNombrePais'];
        $intIdPais         = $arrayDatosForm['intIdPais'];
        try
        {
            //Si se reciben las formas de Contacto Sólo se guardarán sólo las formas de contacto.
            //El perfil 'Cambio Vendedor Formas Contacto' renderiza la edición/agreación de formas de contacto, los datos del punto estarán en null.
            //Si tiene el perfil 'Cambio Vendedor Punto' renderizará la edición/agregación de datos del punto, en este escenario estarán en null las
            //Formas de de Contacto.
            if($arrayDatosForm['formas_contacto'])
            {
                if(is_array($arrayDatosForm['formas_contacto']))
                {
                    $arrayFormasContacto = $arrayDatosForm['formas_contacto'];
                }
                else
                {   
                    $arrayFormasContacto = array();
                    if($arrayDatosForm['formas_contacto'])
                    {
                        $arrayExplodeFormasContacto = explode(',', $arrayDatosForm['formas_contacto']);
                        for($i = 0; $i < count($arrayExplodeFormasContacto); $i+=3)
                        {
                            $arrayFormasContacto[] = array('formaContacto' => $arrayExplodeFormasContacto[$i + 1],
                                                           'valor'         => $arrayExplodeFormasContacto[$i + 2]);
                        }
                    }
                }
                
               /* Se envia array de Parametros y se agrega strOpcionPermitida y strPrefijoEmpresa, Prefijo de empresa en sesion para validar
                * que para empresa MD no se obligue el ingreso de al menos 1 correo */
                $arrayParamFormasContac                        = array ();
                $arrayParamFormasContac['strPrefijoEmpresa']   = $arrayDatosForm['strPrefijoEmpresa'];
                $arrayParamFormasContac['arrayFormasContacto'] = $arrayFormasContacto;
                $arrayParamFormasContac['strOpcionPermitida']  = 'NO';
                $arrayParamFormasContac['intIdPais']           = $intIdPais;
                $arrayParamFormasContac['strNombrePais']       = $strNombrePais;
                $arrayValidaciones   = $this->serviceInfoPersonaFormaContacto->validarFormasContactos($arrayParamFormasContac);
                if($arrayValidaciones)
                {    
                    $arrayValidacionesValor = array_values($arrayValidaciones);
                    foreach($arrayValidacionesValor as $mensaje_validaciones)
                    {
                        $arrayMensajesValor = array_values($mensaje_validaciones);
                        foreach($arrayMensajesValor as $value)
                        {                      
                            $strError = $strError.$value.".\n";                        
                        }
                    }
                    throw new \Exception("No se puede guardar información - " . $strError);
                }
                $arrayPersonaFormasContacto = $this->emcom->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                          ->findBy(array('estado'    =>'Activo',
                                                                        'puntoId' => $objInfoPunto->getId()));
                foreach($arrayPersonaFormasContacto as $objFormaContacto)
                {
                    $boolExiste = false;
                        
                    for($i = 0; $i < count($arrayFormasContacto); $i++)
                    {
                        if($objFormaContacto->getValor() == $arrayFormasContacto[$i]["valor"])
                        {
                            $boolExiste = true;
                        }
                    }
                    
                    if(!$boolExiste)
                    {
                        $objFormaContacto->setEstado('Inactivo');
                        $objFormaContacto->setFeCreacion($objFechaActualiza);
                        $objFormaContacto->setUsrCreacion($strUsrCreacion);
                        $this->emcom->persist($objFormaContacto);
                        $this->emcom->flush();
                    }
                }      
                //Ingresa las formas de contacto por Punto
                for($i = 0; $i < count($arrayFormasContacto); $i++)
                {
                    $objAdmiFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')
                                                        ->findPorDescripcionFormaContacto($arrayFormasContacto[$i]["formaContacto"]);
                    
                    if(is_object($objAdmiFormaContacto))
                    {
                        $objFormaContacto = $this->emcom->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                ->findOneBy(array('puntoId'         => $objInfoPunto->getId(), 
                                                                                'valor'           => $arrayFormasContacto[$i]["valor"],
                                                                                'estado'          => 'Activo',
                                                                                'formaContactoId' => $objAdmiFormaContacto->getId()));
                        if (!is_object($objFormaContacto)) 
                        {
                            $objInfoPuntoFormaContacto_n = new InfoPuntoFormaContacto();
                            $objInfoPuntoFormaContacto_n->setValor($arrayFormasContacto[$i]["valor"]);
                            $objInfoPuntoFormaContacto_n->setEstado($strEstadoActivo);
                            $objInfoPuntoFormaContacto_n->setFeCreacion($objFechaActualiza);
                            $objAdmiFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')
                                                                ->findPorDescripcionFormaContacto($arrayFormasContacto[$i]["formaContacto"]);
                            $objInfoPuntoFormaContacto_n->setFormaContactoId($objAdmiFormaContacto);
                            $objInfoPuntoFormaContacto_n->setIpCreacion($strClientIp);
                            $objInfoPuntoFormaContacto_n->setPuntoId($objInfoPunto);
                            $objInfoPuntoFormaContacto_n->setUsrCreacion($strUsrCreacion);
                            $this->emcom->persist($objInfoPuntoFormaContacto_n);
                        }
                    }
                }
                $this->emcom->flush(); 
            }
            else // Si NO vienen las formas de contacto se procede a guardar los datos del punto.
            {
                if( !is_object($objInfoPunto) )
                {
                    throw new \Exception('No se encontró el objecto del punto a modificar');
                }//( !is_object($objInfoPunto) )
                
                $boolCrearHistorial     = false;
                $strValorHistorialPunto = "Se actualiza la siguiente información del punto:<br/>";
                $strLoginVendedorOld    = $objInfoPunto->getUsrVendedor();
                
                //Actualiza Información del Punto
                if( isset($arrayDatosForm['direccion']) && !empty($arrayDatosForm['direccion']) )
                {
                    $strDireccionAntigua = $objInfoPunto->getDireccion();
                    
                    if( $strDireccionAntigua != $arrayDatosForm['direccion'] )
                    {
                        $boolCrearHistorial     = true;
                        $strValorHistorialPunto .= "<b>Dirección Antigua: </b>".$strDireccionAntigua."<br/>";
                        $objInfoPunto->setDireccion($arrayDatosForm['direccion']);
                    }//( $strDireccionAntigua != $arrayDatosForm['direccion'] )
                }//( isset($arrayDatosForm['direccion']) && !empty($arrayDatosForm['direccion']) )
                
                if( isset($arrayDatosForm['descripcionpunto']) && !empty($arrayDatosForm['descripcionpunto']) )
                {
                    $strDescripcionPunto = $objInfoPunto->getDescripcionPunto();
                    
                    if( $strDescripcionPunto != $arrayDatosForm['descripcionpunto'] )
                    {
                        $boolCrearHistorial     = true;
                        $strValorHistorialPunto .= "<b>Referencia Antigua: </b>".$strDescripcionPunto."<br/>";
                        $objInfoPunto->setDescripcionPunto($arrayDatosForm['descripcionpunto']); // referencia
                    }//( $strDescripcionPunto != $arrayDatosForm['descripcionpunto'] )
                }//( isset($arrayDatosForm['descripcionpunto']) && !empty($arrayDatosForm['descripcionpunto']) )
                
                if( isset($arrayDatosForm['observacion']) && !empty($arrayDatosForm['observacion']) )
                {
                    $strObservacion = $objInfoPunto->getObservacion();

                    if( $strObservacion != $arrayDatosForm['observacion'] )
                    {
                        $boolCrearHistorial     = true;
                        $strValorHistorialPunto .= "<b>Observación Antigua: </b>".$strObservacion."<br/>";
                        $objInfoPunto->setObservacion($arrayDatosForm['observacion']);
                    }//( $strObservacion != $arrayDatosForm['observacion'] )
                }//( isset($arrayDatosForm['observacion']) && !empty($arrayDatosForm['observacion']) )
                
                if( isset($arrayDatosForm['loginVendedor']) && !empty($arrayDatosForm['loginVendedor']) )
                {
                    if( $strLoginVendedorOld != $arrayDatosForm['loginVendedor'] )
                    {
                        $boolCrearHistorial     = true;
                        $strValorHistorialPunto .= "<b>Usr Vendedor Antiguo: </b>".$strLoginVendedorOld;
                        $objInfoPunto->setUsrVendedor($arrayDatosForm['loginVendedor']);
                    }//( $strLoginVendedor != $arrayDatosForm['loginVendedor'] )
                }//( isset($arrayDatosForm['loginVendedor']) && !empty($arrayDatosForm['loginVendedor']) )
                
                $objInfoPunto->setFeUltMod($objFechaActualiza);
                $objInfoPunto->setAccion('Cambio de Vendedor');
                $objInfoPunto->setIpUltMod($strClientIp);
                $objInfoPunto->setUsrUltMod($strUsrCreacion);
                $this->emcom->persist($objInfoPunto);
                
                if( $boolCrearHistorial )
                {
                    $arrayParametrosHistorialPunto                       = array();
                    $arrayParametrosHistorialPunto['objInfoPunto']       = $objInfoPunto;
                    $arrayParametrosHistorialPunto['strUsuarioCreacion'] = $strUsrCreacion; 
                    $arrayParametrosHistorialPunto['strIpCreacion']      = $strClientIp; 
                    $arrayParametrosHistorialPunto['strValor']           = $strValorHistorialPunto; 
                    $arrayParametrosHistorialPunto['strAccion']          = "actualizacionInformacion";

                    $this->generarHistorialPunto($arrayParametrosHistorialPunto);
                }//( $boolCrearHistorial )


                /**
                 * Bloque que guarda las solicitudes por cambio de vendedor
                 */
                if( isset($arrayDatosForm['strServiciosSelected']) && !empty($arrayDatosForm['strServiciosSelected']) 
                    && isset($arrayDatosForm['loginVendedor']) && !empty($arrayDatosForm['loginVendedor']) 
                    && $strLoginVendedorOld != $arrayDatosForm['loginVendedor'] )
                {
                    $intIdPersonaEmpresaRol = ( isset($arrayDatosForm['intPersonaEmpresaRol']) && !empty($arrayDatosForm['intPersonaEmpresaRol']) )
                                               ? $arrayDatosForm['intPersonaEmpresaRol'] : 0;
                    $strCodEmpresa          = ( isset($arrayDatosForm['strCodEmpresa']) && !empty($arrayDatosForm['strCodEmpresa']) )
                                               ? $arrayDatosForm['strCodEmpresa'] : null;
                    
                    if( empty($strCodEmpresa) )
                    {
                        throw new \Exception('No se encontró empresa en sessión');
                    }//( empty($strCodEmpresa) )
                    
                    $strLoginNuevoVendedor = $arrayDatosForm['loginVendedor'];//Login del nuevo vendedor seleccionado por el usuario
                    
                    //SE OBTIENE LA CARACTERISTICA POR CAMBIO_VENDEDOR
                    $objCambioVendedorCaracteristica = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                                                            ->findOneBy( array('estado'                    => 'Activo', 
                                                                               'descripcionCaracteristica' => 'CAMBIO_VENDEDOR') );
                    
                    if( !is_object($objCambioVendedorCaracteristica) )
                    {
                        throw new \Exception('No se encontró la característica por cambio de vendedor');
                    }//( !is_object($objCambioVendedorCaracteristica) )
                    
                    
                    //SE OBTIENE LA CARACTERISTCIA POR CAMBIO_SUBGERENTE
                    $objCambioSubgerenteCaracteristica = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                                                              ->findOneBy( array('estado'                    => 'Activo', 
                                                                                 'descripcionCaracteristica' => 'CAMBIO_SUBGERENTE') );
                    
                    if( !is_object($objCambioSubgerenteCaracteristica) )
                    {
                        throw new \Exception('No se encontró la característica por cambio de subgerente');
                    }//( !is_object($objCambioSubgerenteCaracteristica) )


                    //SE OBTIENE LA SOLICITUD POR CAMBIO DE VENDEDOR
                    $objCambioVendedorSolicitud = $this->emcom->getRepository("schemaBundle:AdmiTipoSolicitud")
                                                       ->findOneBy( array('estado'               => 'Activo', 
                                                                          'descripcionSolicitud' => 'SOLICITUD CAMBIO PERSONAL PLANTILLA') );
                    
                    if( !is_object($objCambioVendedorSolicitud) )
                    {
                        throw new \Exception('No se encontró la solicitud por cambio de vendedor');
                    }//( !is_object($objCambioVendedorSolicitud) )
                    
                    
                    //PARÁMETROS USADOS PARA OBTENER LA INFORMACION DEL SUBGERENTE DEL VENDEDOR ACTUAL Y NUEVO
                    $arrayParametros                        = array();
                    $arrayParametros['usuario']             = $intIdPersonaEmpresaRol;
                    $arrayParametros['empresa']             = $strCodEmpresa;
                    $arrayParametros['estadoActivo']        = 'Activo';
                    $arrayParametros['caracteristicaCargo'] = 'CARGO_GRUPO_ROLES_PERSONAL';
                    $arrayParametros['nombreArea']          = 'Comercial';
                    $arrayParametros['strTipoRol']          = array('Empleado', 'Personal Externo');
                    $arrayParametros['limite']              = 1;//Para retornar un sólo registro

                    //BLOQUE QUE BUSCA LOS ROLES NO PERMITIDOS PARA LA BUSQUEDA DEL VENDEDOR NUEVO
                    $arrayRolesNoIncluidos = array();
                    $arrayParametrosRoles  = array( 'strCodEmpresa'     => $strCodEmpresa,
                                                    'strValorRetornar'  => 'descripcion',
                                                    'strNombreProceso'  => 'JEFES',
                                                    'strNombreModulo'   => 'COMERCIAL',
                                                    'strNombreCabecera' => 'ROLES_NO_PERMITIDOS',
                                                    'strUsrCreacion'    => $strUsrCreacion,
                                                    'strIpCreacion'     => $strClientIp );

                    $arrayResultadosRolesNoIncluidos = $this->serviceUtilidades->getDetallesParametrizables($arrayParametrosRoles);

                    if( isset($arrayResultadosRolesNoIncluidos['resultado']) && !empty($arrayResultadosRolesNoIncluidos['resultado']) )
                    {
                        foreach( $arrayResultadosRolesNoIncluidos['resultado'] as $strRolNoIncluido )
                        {
                            $arrayRolesNoIncluidos[] = $strRolNoIncluido;
                        }//foreach( $arrayResultadosRolesNoIncluidos['resultado'] as $strRolNoIncluido )

                        $arrayParametros['rolesNoIncluidos'] = $arrayRolesNoIncluidos;
                    }//( isset($arrayResultadosRolesNoIncluidos['resultado']) && !empty($arrayResultadosRolesNoIncluidos['resultado']) )

                    //BLOQUE QUE BUSCA LOS ROLES PERMITIDOS PARA LA BUSQUEDA DEL VENDEDOR NUEVO
                    $arrayRolesIncluidos                       = array();
                    $arrayParametrosRoles['strNombreCabecera'] = 'ROLES_PERMITIDOS';

                    $arrayResultadosRolesIncluidos = $this->serviceUtilidades->getDetallesParametrizables($arrayParametrosRoles);

                    if( isset($arrayResultadosRolesIncluidos['resultado']) && !empty($arrayResultadosRolesIncluidos['resultado']) )
                    {
                        foreach( $arrayResultadosRolesIncluidos['resultado'] as $strRolIncluido )
                        {
                            $arrayRolesIncluidos[] = $strRolIncluido;
                        }//foreach( $arrayResultadosRolesIncluidos['resultado'] as $strRolIncluido )

                        $arrayParametros['strTipoRol'] = $arrayRolesIncluidos;
                    }//( isset($arrayResultadosRolesIncluidos['resultado']) && !empty($arrayResultadosRolesIncluidos['resultado']) )

                    //SE VALIDA QUE SE CONSIDEREN LOS DEPARTAMENTOS COMERCIALES AGRUPADOS EN EL PARAMETRO 'GRUPO_DEPARTAMENTOS'
                    $arrayParametrosDepartamentos = array('strCodEmpresa'     => $strCodEmpresa,
                                                          'strValorRetornar'  => 'valor1',
                                                          'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                          'strNombreModulo'   => 'COMERCIAL',
                                                          'strNombreCabecera' => 'GRUPO_DEPARTAMENTOS',
                                                          'strValor2Detalle'  => 'COMERCIAL',
                                                          'strUsrCreacion'    => $strUsrCreacion,
                                                          'strIpCreacion'     => $strClientIp);

                    $arrayResultadosDepartamentos = $this->serviceUtilidades->getDetallesParametrizables($arrayParametrosDepartamentos);

                    if( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )
                    {
                        $arrayParametros['departamento'] = $arrayResultadosDepartamentos['resultado'];
                    }//( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )

                    //SE OBTIENE EL CARGO DE VENDEDOR DEL PARAMETRO 'GRUPO_ROLES_PERSONAL'
                    $arrayParametrosCargoVendedor = array('strCodEmpresa'     => $strCodEmpresa,
                                                          'strValorRetornar'  => 'id',
                                                          'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                          'strNombreModulo'   => 'COMERCIAL',
                                                          'strNombreCabecera' => 'GRUPO_ROLES_PERSONAL',
                                                          'strValor3Detalle'  => 'VENDEDOR',
                                                          'strUsrCreacion'    => $strUsrCreacion,
                                                          'strIpCreacion'     => $strClientIp);

                    $arrayResultadosCargoVendedor = $this->serviceUtilidades->getDetallesParametrizables($arrayParametrosCargoVendedor);

                    if( isset($arrayResultadosCargoVendedor['resultado']) && !empty($arrayResultadosCargoVendedor['resultado']) )
                    {
                        foreach( $arrayResultadosCargoVendedor['resultado'] as $intIdCargoVendedor )
                        {
                            $arrayParametros['criterios']['cargo'] = $intIdCargoVendedor;
                        }//foreach( $arrayResultadosCargoVendedor['resultado'] as $intIdDepartamento )
                    }//( isset($arrayResultadosCargoVendedor['resultado']) && !empty($arrayResultadosCargoVendedor['resultado']) )


                    //SE RECORRE EL ARREGLO QUE CONTIENE LOS SERVICIOS SELECCIONADOS POR EL USUARIO
                    $arrayServiciosSelected = explode('|', $arrayDatosForm['strServiciosSelected']);
                    foreach($arrayServiciosSelected as $intIdServicio)
                    {
                        $intIdVendedorNuevo   = 0;
                        $intIdSubgerenteNuevo = 0;
                        $strObservacion       = "Se desea realizar el siguiente cambio de Vendedor y Subgerente:<br/>";
                        $objServicioSelected  = $this->emcom->getRepository("schemaBundle:InfoServicio")->findOneById($intIdServicio);

                        if( !is_object($objServicioSelected) )
                        {
                            throw new \Exception('No se encontró el servicio con id('.$intIdServicio.')');
                        }//( !is_object($objServicioSelected) )
                        
                        $strUsrVendedorServicioActual = $objServicioSelected->getUsrVendedor();
                        
                        /**
                         * SE VERIFICA SI EL SERVICIO SELECCIONADO TIENE PLANTILLA DE COMISIONISTA PARA CREAR LAS SOLICITUDES CORRESPONDIENTES POR
                         * EL CAMBIO DE VENDEDOR
                         */
                        $arrayParametrosComision = array('arrayEstados'       => array('Activo'),
                                                         'intIdServicio'      => $intIdServicio,
                                                         'strRolComisionista' => 'VENDEDOR');
                        $arrayResultadoComision  = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                               ->getServicioComision($arrayParametrosComision);
                        
                        if( isset($arrayResultadoComision['intTotal']) && $arrayResultadoComision['intTotal'] > 0 )
                        {
                            //SE OBTIENE EL VENDEDOR Y SUBGERENTE ACTUAL DEL SERVICIO SELECCIONADO
                            if( !empty($strUsrVendedorServicioActual) )
                            {
                                $arrayParametros['criterios']['login']  = $strUsrVendedorServicioActual;

                                $arrayPersonalVendedorActual = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                    ->findPersonalByCriterios($arrayParametros);

                                if( isset($arrayPersonalVendedorActual['registros']) && !empty($arrayPersonalVendedorActual['registros']) 
                                    && isset($arrayPersonalVendedorActual['total']) && $arrayPersonalVendedorActual['total'] == 1 )
                                {
                                    $arrayVendedorActual     = $arrayPersonalVendedorActual['registros'][0];
                                    $strNombreVendedorActual = ( isset($arrayVendedorActual['nombres']) && !empty($arrayVendedorActual['nombres']) )
                                                                ? ucwords(strtolower($arrayVendedorActual['nombres'])).' ' : '';
                                    $strNombreVendedorActual .= ( isset($arrayVendedorActual['apellidos']) 
                                                                  && !empty($arrayVendedorActual['apellidos']) )
                                                                 ? ucwords(strtolower($arrayVendedorActual['apellidos'])) : '';

                                    if( !empty($strNombreVendedorActual) )
                                    {
                                        $strObservacion .= "<b>Vendedor Actual:</b> ".$strNombreVendedorActual."<br/>";
                                    }
                                    else
                                    {
                                        $strObservacion .= "<b>Vendedor Actual:</b> NO TIENE VENDEDOR ASIGNADO<br/>";
                                    }//( !empty($strNombreVendedorActual) )
                                    
                                    
                                    /**
                                     * SE VERIFICA SI EL SERVICIO TIENE ASOCIADO EN LA INFO_SERVICIO_COMISION EL CARGO DE SUBGERENTE PARA SER
                                     * ACTUALIZADO
                                     */
                                    $boolTieneSubgerente     = false;
                                    $arrayParametrosComision = array('arrayEstados'       => array('Activo'),
                                                                     'intIdServicio'      => $intIdServicio,
                                                                     'strRolComisionista' => 'SUBGERENTE');
                                    $arrayResultadoComision  = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                                           ->getServicioComision($arrayParametrosComision);

                                    if( isset($arrayResultadoComision['intTotal']) && $arrayResultadoComision['intTotal'] == 1
                                        && isset($arrayResultadoComision['arrayRegistros']) 
                                        && !empty($arrayResultadoComision['arrayRegistros']) )
                                    {
                                        $boolTieneSubgerente = true;
                                    }//( isset($arrayResultadoComision['intTotal']) && $arrayResultadoComision['intTotal'] == 1...

                                    if( $boolTieneSubgerente )
                                    {
                                        $intIdReportaPersonaRol = ( isset($arrayVendedorActual['reportaPersonaEmpresaRolId']) 
                                                                    && !empty($arrayVendedorActual['reportaPersonaEmpresaRolId']) )
                                                                   ? $arrayVendedorActual['reportaPersonaEmpresaRolId'] : 0;

                                        if( $intIdReportaPersonaRol > 0 )
                                        {
                                            $objSubgerenteActual = $this->emcom->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                                        ->findOneById($intIdReportaPersonaRol);

                                            if( !is_object($objSubgerenteActual) )
                                            {
                                                throw new \Exception('No se encontró la información del subgerente asignado al servicio('.
                                                                     $intIdServicio.'), idSubgerente('.$intIdReportaPersonaRol.')');
                                            }//( !is_object($objSubgerenteActual) )


                                            $objPersonaSubgerenteActual = $objSubgerenteActual->getPersonaId();

                                            if( !is_object($objPersonaSubgerenteActual) )
                                            {
                                                throw new \Exception('No se encontró la información personal del subgerente asignado al servicio('.
                                                                     $intIdServicio.'), idSubgerente('.$intIdReportaPersonaRol.')');
                                            }//( !is_object($objPersonaSubgerenteActual) )

                                            $strNombresSubgerenteActual        = $objPersonaSubgerenteActual->getNombres();
                                            $strApellidosSubgerenteActual      = $objPersonaSubgerenteActual->getApellidos();
                                            $strNombreCompletoSubgerenteActual = ( !empty($strNombresSubgerenteActual) )
                                                                                  ? ucwords(strtolower($strNombresSubgerenteActual)).' ' : '';
                                            $strNombreCompletoSubgerenteActual .= ( !empty($strApellidosSubgerenteActual) )
                                                                                   ? ucwords(strtolower($strApellidosSubgerenteActual)) : '';

                                            if( !empty($strNombreCompletoSubgerenteActual) )
                                            {
                                                $strObservacion .= "<b>Subgerente Actual:</b> ".$strNombreCompletoSubgerenteActual."<br/>";
                                            }
                                            else
                                            {
                                                $strObservacion .= "<b>Subgerente Actual:</b> NO TIENE SUBGERENTE ASIGNADO<br/>";
                                            }//( !empty($strNombreCompletoSubgerenteActual) )
                                        }
                                        else
                                        {
                                            $strObservacion .= "<b>Subgerente Actual:</b> NO TIENE SUBGERENTE ASIGNADO<br/>";
                                        }//( $intIdReportaPersonaRol > 0 )
                                    }//( $boolTieneSubgerente )
                                }//if( isset($arrayPersonalVendedorActual['registros']) && !empty($arrayPersonalVendedorActual['registros'])...
                            }//( !empty($strUsrVendedorServicioActual) )
                            else
                            {
                                $strObservacion .= "<b>Vendedor Actual:</b> NO TIENE VENDEDOR ASIGNADO<br/>";
                            }


                            /**
                             * BLOQUE QUE OBTIENE LA INFORMACION CORRESPONDIENTE AL VENDEDOR Y SUBGERENTE NUEVO A ASIGNAR
                             */
                            $arrayParametros['criterios']['login'] = $arrayDatosForm['loginVendedor'];

                            $arrayPersonalVendedorNuevo = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                               ->findPersonalByCriterios($arrayParametros);

                            if( isset($arrayPersonalVendedorNuevo['registros']) && !empty($arrayPersonalVendedorNuevo['registros']) 
                                && isset($arrayPersonalVendedorNuevo['total']) && $arrayPersonalVendedorNuevo['total'] == 1 )
                            {
                                $arrayVendedorNuevo     = $arrayPersonalVendedorNuevo['registros'][0];
                                $strNombreVendedorNuevo = ( isset($arrayVendedorNuevo['nombres']) && !empty($arrayVendedorNuevo['nombres']) )
                                                           ? ucwords(strtolower($arrayVendedorNuevo['nombres'])).' ' : '';
                                $strNombreVendedorNuevo .= ( isset($arrayVendedorNuevo['apellidos']) && !empty($arrayVendedorNuevo['apellidos']) )
                                                            ? ucwords(strtolower($arrayVendedorNuevo['apellidos'])) : '';
                                $intIdVendedorNuevo     = ( isset($arrayVendedorNuevo['idPersonaEmpresaRol']) 
                                                            && !empty($arrayVendedorNuevo['idPersonaEmpresaRol']) )
                                                           ? $arrayVendedorNuevo['idPersonaEmpresaRol'] : 0;

                                $strObservacion .= "<b>Nuevo Vendedor:</b> ".$strNombreVendedorNuevo."<br/>";

                                //SE VERIFICA SI TIENE SUBGERENTE ASOCIADO PARA AGREGARLO AL CAMBIO DE VENDEDOR
                                if( $boolTieneSubgerente )
                                {
                                    $intIdSubgerenteNuevo = ( isset($arrayVendedorNuevo['reportaPersonaEmpresaRolId']) 
                                                              && !empty($arrayVendedorNuevo['reportaPersonaEmpresaRolId']) )
                                                             ? $arrayVendedorNuevo['reportaPersonaEmpresaRolId'] : 0;

                                    if( $intIdSubgerenteNuevo > 0 )
                                    {
                                        $objSubgerenteNuevo = $this->emcom->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                                   ->findOneById($intIdSubgerenteNuevo);

                                        if( !is_object($objSubgerenteNuevo) )
                                        {
                                            throw new \Exception('No se encontró la información del nuevo subgerente asignado al servicio('.
                                                                 $intIdServicio.'), idSubgerente('.$intIdSubgerenteNuevo.')');
                                        }//( !is_object($objSubgerenteNuevo) )


                                        $objPersonaSubgerenteNuevo = $objSubgerenteNuevo->getPersonaId();

                                        if( !is_object($objPersonaSubgerenteNuevo) )
                                        {
                                            throw new \Exception('No se encontró la información personal del nuevo subgerente asignado al servicio('.
                                                                 $intIdServicio.'), idSubgerente('.$intIdSubgerenteNuevo.')');
                                        }//( !is_object($objPersonaSubgerenteNuevo) )

                                        $strNombresSubgerenteNuevo        = $objPersonaSubgerenteNuevo->getNombres();
                                        $strApellidosSubgerenteNuevo      = $objPersonaSubgerenteNuevo->getApellidos();
                                        $strNombreCompletoSubgerenteNuevo = ( !empty($strNombresSubgerenteNuevo) )
                                                                              ? ucwords(strtolower($strNombresSubgerenteNuevo)).' ' : '';
                                        $strNombreCompletoSubgerenteNuevo .= ( !empty($strApellidosSubgerenteNuevo) )
                                                                               ? ucwords(strtolower($strApellidosSubgerenteNuevo)) : '';

                                        if( !empty($strNombreCompletoSubgerenteNuevo) )
                                        {
                                            $strObservacion .= "<b>Nuevo Subgerente:</b> ".$strNombreCompletoSubgerenteNuevo."<br/>";
                                        }
                                        else
                                        {
                                            $strObservacion .= "<b>Nuevo Subgerente:</b> NO TIENE SUBGERENTE ASIGNADO<br/>";
                                        }//( !empty($strNombreCompletoSubgerenteActual) )
                                    }
                                    else
                                    {
                                        $strObservacion .= "<b>Nuevo Subgerente:</b> NO TIENE SUBGERENTE ASIGNADO<br/>";
                                    }//( $intIdSubgerenteNuevo > 0 )
                                }//( $boolTieneSubgerente )
                            }//( isset($arrayPersonalVendedorNuevo['registros']) && !empty($arrayPersonalVendedorNuevo['registros'])...

                            if( empty($intIdVendedorNuevo) )
                            {
                                throw new \Exception('No se encontró el nuevo vendedor que será asignado');
                            }//( empty($intIdVendedorNuevo) )


                            //SE VERIFICA SI TIENE SUBGERENTE ASOCIADO PARA AGREGARLO AL CAMBIO DE VENDEDOR
                            if( $boolTieneSubgerente )
                            {
                                if( empty($intIdSubgerenteNuevo) )
                                {
                                    throw new \Exception('No se encontró el nuevo subgerente que será asignado');
                                }//( empty($intIdSubgerenteNuevo) )
                            }//( $boolTieneSubgerente )


                            //SE GUARDA LA SOLICITUD DE CAMBIO DE VENDEDOR
                            $objDetalleSolicitud = new InfoDetalleSolicitud();
                            $objDetalleSolicitud->setServicioId($objServicioSelected);
                            $objDetalleSolicitud->setTipoSolicitudId($objCambioVendedorSolicitud);
                            $objDetalleSolicitud->setObservacion($strObservacion);
                            $objDetalleSolicitud->setUsrCreacion($strUsrCreacion);
                            $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                            $objDetalleSolicitud->setEstado("Pendiente");
                            $this->emcom->persist($objDetalleSolicitud);


                            //SE GUARDA COMO DETALLE DE LA SOLICITUD EL NUEVO VENDEDOR ASIGNADO
                            $objDetalleSolCaracVendedor = new InfoDetalleSolCaract();
                            $objDetalleSolCaracVendedor->setCaracteristicaId($objCambioVendedorCaracteristica);
                            $objDetalleSolCaracVendedor->setDetalleSolicitudId($objDetalleSolicitud);
                            $objDetalleSolCaracVendedor->setEstado('Activo');
                            $objDetalleSolCaracVendedor->setFeCreacion(new \DateTime('now'));
                            $objDetalleSolCaracVendedor->setUsrCreacion($strUsrCreacion);
                            $objDetalleSolCaracVendedor->setValor($intIdVendedorNuevo);
                            $this->emcom->persist($objDetalleSolCaracVendedor);

                            //SE VERIFICA SI TIENE SUBGERENTE ASOCIADO PARA AGREGARLO AL CAMBIO DE VENDEDOR
                            if( $boolTieneSubgerente )
                            {
                                //SE GUARDA COMO DETALLE DE LA SOLICITUD EL NUEVO SUBGERENTE ASIGNADO
                                $objDetalleSolCaracSubgerente = new InfoDetalleSolCaract();
                                $objDetalleSolCaracSubgerente->setCaracteristicaId($objCambioSubgerenteCaracteristica);
                                $objDetalleSolCaracSubgerente->setDetalleSolicitudId($objDetalleSolicitud);
                                $objDetalleSolCaracSubgerente->setEstado('Activo');
                                $objDetalleSolCaracSubgerente->setFeCreacion(new \DateTime('now'));
                                $objDetalleSolCaracSubgerente->setUsrCreacion($strUsrCreacion);
                                $objDetalleSolCaracSubgerente->setValor($intIdSubgerenteNuevo);
                                $this->emcom->persist($objDetalleSolCaracSubgerente);
                            }//( $boolTieneSubgerente )


                            //SE GUARDA EL HISTORIAL DE LA SOLICITUD POR CAMBIO DE VENDEDOR CREADA
                            $objDetalleSolHist = new InfoDetalleSolHist();
                            $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                            $objDetalleSolHist->setIpCreacion($strClientIp);
                            $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                            $objDetalleSolHist->setUsrCreacion($strUsrCreacion);
                            $objDetalleSolHist->setEstado('Pendiente');
                            $objDetalleSolHist->setObservacion($strObservacion);
                            $this->emcom->persist($objDetalleSolHist);
                        }
                        else
                        {
                            $strNombreVendedorAntiguo = "NO TIENE VENDEDOR ASIGNADO";
                            $strNombreVendedorNuevo   = $arrayDatosForm['loginVendedor'];
                            $objInfoVendedorAntiguo   = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                                             ->findOneByLogin($strUsrVendedorServicioActual);
                            
                            if( is_object($objInfoVendedorAntiguo) )
                            {
                                $strNombreVendedorAntiguo = trim($objInfoVendedorAntiguo->__toString());
                                $strNombreVendedorAntiguo = strtolower($strNombreVendedorAntiguo);
                                $strNombreVendedorAntiguo = ucwords($strNombreVendedorAntiguo);
                            }//( is_object($objInfoVendedorAntiguo) )
                            
                            $objInfoVendedorNuevo = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                                         ->findOneByLogin($arrayDatosForm['loginVendedor']);
                            
                            if( is_object($objInfoVendedorNuevo) )
                            {
                                $strNombreVendedorNuevo = trim($objInfoVendedorNuevo->__toString());
                                $strNombreVendedorNuevo = strtolower($strNombreVendedorNuevo);
                                $strNombreVendedorNuevo = ucwords($strNombreVendedorNuevo);
                            }//( is_object($objInfoVendedorNuevo) )

                            $objServicioSelected->setUsrVendedor($arrayDatosForm['loginVendedor']);
                            $this->emcom->persist($objServicioSelected);
                            
                            $strObservacionServicio   = "Se realiza el cambio de <b>VENDEDOR</b><br/>Antiguo: ".$strNombreVendedorAntiguo.
                                                        "<br/>Nuevo: ".$strNombreVendedorNuevo;
                            
                            $objInfoServicioHistorial = new InfoServicioHistorial();
                            $objInfoServicioHistorial->setEstado($objServicioSelected->getEstado());
                            $objInfoServicioHistorial->setUsrCreacion($strUsrCreacion);
                            $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                            $objInfoServicioHistorial->setIpCreacion($strClientIp);
                            $objInfoServicioHistorial->setServicioId($objServicioSelected);
                            $objInfoServicioHistorial->setAccion('cambioVendedor');
                            $objInfoServicioHistorial->setObservacion($strObservacionServicio);
                            $this->emcom->persist($objInfoServicioHistorial);
                        }//( isset($arrayResultadoComision['intTotal']) && $arrayResultadoComision['intTotal'] > 0 )
                    }//foreach($arrayServiciosSelected as $intIdServicio)
                }//( isset($arrayDatosForm['strServiciosSelected']) && !empty($arrayDatosForm['strServiciosSelected'])...
            }
            $this->emcom->flush(); 
            $this->emcom->getConnection()->commit();
            return $objInfoPunto;
        }
        catch(\Exception $e)
        {
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollBack();
            }
            $this->emcom->getConnection()->close();
            throw $e;
        }
    }

     /**    
     * Documentación para el método 'validaCoordenadas'.
     *
     * Descripcion: Metodo encargado de validar las coordenadas 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 11-03-2015     
     * @param array $datos_form     
     * @return array
     */
    /**    
     * Documentación para el método 'validaCoordenadas'.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1 30-06-2017  
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.2 13-03-2019 Se agregan coordenadas para Guatemala.     
     *    
     * @param array $datos_form     
     * @param array $strNombrePais 
     * @return array
     * Se añade el parámetro para validar coordenadas por el nombre del país y se fijan coordenadas panameñas. 
     */
    
    public function validaCoordenadas($datos_form, $strNombrePais)
    {
        $arrayValidaciones    = array();
        $strMensajeValidacion = ''; 
        if(($datos_form['grados_la']!='' && $datos_form['minutos_la']!='' && $datos_form['segundos_la']!=''
            && $datos_form['decimas_segundos_la']!='') && ($datos_form['grados_lo']!='' && $datos_form['minutos_lo']!=''
            && $datos_form['segundos_lo']!='' && $datos_form['decimas_segundos_lo']!=''))
        {   
            //Latitud
            $strMensajeValidacion = $this->validaGradosCoordenadas($datos_form['grados_la']);
            if($strMensajeValidacion!='')
            {
                $arrayValidaciones[] = array('mensaje_validaciones' => 'Grados Latitud- '.$strMensajeValidacion);
            }
           
            $strMensajeValidacion = $this->validaMinutosCoordenadas($datos_form['minutos_la']);
            if($strMensajeValidacion!='')
            {
                $arrayValidaciones[] = array('mensaje_validaciones' => 'Minutos Latitud- '.$strMensajeValidacion);
            }
           
            $strMensajeValidacion = $this->validaSegundosCoordenadas($datos_form['segundos_la']);
            if($strMensajeValidacion!='')
            {
                $arrayValidaciones[] = array('mensaje_validaciones' => 'Segundos Latitud- '.$strMensajeValidacion);
            } 
            
            $strMensajeValidacion = $this->validaDecimasSegundosCoordenadas($datos_form['decimas_segundos_la']);
            if($strMensajeValidacion!='')
            {
                $arrayValidaciones[] = array('mensaje_validaciones' => 'Decimas Segundos Latitud- '.$strMensajeValidacion);
            }                          
           
            //Latitud Norte/Sur
            if($datos_form['latitud'] == 'T')
            {
                $arrayValidaciones[] = array('mensaje_validaciones' => 'Ingrese la latitud (Norte/Sur)');
            }
            
            //Longitud
            $strMensajeValidacion = $this->validaGradosCoordenadas($datos_form['grados_lo']);
            if($strMensajeValidacion!='')
            {
                $arrayValidaciones[] = array('mensaje_validaciones' => 'Grados Longitud- '.$strMensajeValidacion);
            }
           
            $strMensajeValidacion = $this->validaMinutosCoordenadas($datos_form['minutos_lo']);
            if($strMensajeValidacion!='')
            {
                $arrayValidaciones[] = array('mensaje_validaciones' => 'Minutos Longitud- '.$strMensajeValidacion);
            }
           
            $strMensajeValidacion = $this->validaSegundosCoordenadas($datos_form['segundos_lo']);
            if($strMensajeValidacion!='')
            {
                $arrayValidaciones[] = array('mensaje_validaciones' => 'Segundos Longitud- '.$strMensajeValidacion);
            } 
            
            $strMensajeValidacion = $this->validaDecimasSegundosCoordenadas($datos_form['decimas_segundos_lo']);
            if($strMensajeValidacion!='')
            {
                $arrayValidaciones[] = array('mensaje_validaciones' => 'Decimas Segundos Longitud- '.$strMensajeValidacion);
            }  
            //Longitud Este/Oeste
            if($datos_form['longitud'] == 'T')
            {
                $arrayValidaciones[] = array('mensaje_validaciones' => 'Ingrese la longitud (Este/Oeste)');
            }
            
            //Valida Coordenadas Ecuador
            $latitud_1   = -5.036;
            $latitud_2   = 1.40;
            $longitud_1  = -95;
            $longitud_2  = -75.25;
            $paisEmpresa = 'Ecuador';
            if(isset($strNombrePais) && $strNombrePais == 'PANAMA')
            {
                $latitud_1   = 7.15;
                $latitud_2   = 9.66;
                $longitud_1  = -83.06;
                $longitud_2  = -77.15;
                $paisEmpresa = "Panamá";
            }
            else if(isset($strNombrePais) && $strNombrePais == 'GUATEMALA')
            {
                $latitud_1   = 13.428461;
                $latitud_2   = 17.933653;
                $longitud_1  = -92.512969;
                $longitud_2  = -87.471971;
                $paisEmpresa = "Guatemala";
            }            
            if(!($datos_form['latitudFloat'] >= $latitud_1 && $datos_form['latitudFloat'] <= $latitud_2))
            {
                $arrayValidaciones[] = array('mensaje_validaciones' => 'Debe ingresar la latitud en el rango de ' . $paisEmpresa);
            }
            if(!($datos_form['longitudFloat'] >= $longitud_1 && $datos_form['longitudFloat'] <= $longitud_2))
            {
                $arrayValidaciones[] = array('mensaje_validaciones' => 'Debe ingresar la longitud en el rango de ' . $paisEmpresa);
            }
        }
        else
        {
            if (($datos_form['grados_la']!='' || $datos_form['minutos_la']!='' || $datos_form['segundos_la']!='' || 
                   $datos_form['decimas_segundos_la']!='' || $datos_form['latitud']!='T')|| ($datos_form['grados_lo']!='' ||
                   $datos_form['minutos_lo']!='' || $datos_form['segundos_lo']!='' || $datos_form['decimas_segundos_lo']!='' ||
                   $datos_form['longitud']!='T'))
            {
                $arrayValidaciones[] = array('mensaje_validaciones' => 'Si no va a ingresar coordenadas debe dejar todos los campos'
                    . ' de las coordenadas vacios.');
            }           
        }
        return $arrayValidaciones;
    } 
    /**    
     * Documentación para el método 'validaGradosCoordenadas'.
     *
     * Descripcion: Metodo encargado de validar los grados de una coordenada este debe ser valor numerico entero entre 0-360
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 12-03-2015     
     * @param  int $intGrado          
     * @return string
     */
    
    public function validaGradosCoordenadas($intGrado)
    {
        $strMensajeValidacion = '';
        //Grados 
        if(!preg_match('/\d$/i', $intGrado))
        {
            $strMensajeValidacion = 'Ingrese solo numeros';
        }
        else
        {
            //valido que sea numero entre 0-360
            if($intGrado < 0 || $intGrado > 360)
            {                
                $strMensajeValidacion = 'Ingrese solo números entre 0-360';
            }
        }
        return $strMensajeValidacion;
    }

     /**    
     * Documentación para el método 'validaMinutosCoordenadas'.
     *
     * Descripcion: Metodo encargado de validar los minutos de una coordenada este debe ser valor numerico entero entre 0-59
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 12-03-2015     
     * @param  int $intMinuto          
     * @return string
     */
    
    public function validaMinutosCoordenadas($intMinuto)
    {
        $strMensajeValidacion = '';
        //Grados 
        if(!preg_match('/\d$/i', $intMinuto))
        {
            $strMensajeValidacion = 'Ingrese solo numeros';
        }
        else
        {
            //valido que sea numero entre 0-59
            if($intMinuto < 0 || $intMinuto > 59)
            {                
                $strMensajeValidacion = 'Ingrese solo números entre 0-59';
            }
        }
        return $strMensajeValidacion;
    }

    /**    
     * Documentación para el método 'validaSegundosCoordenadas'.
     *
     * Descripcion: Metodo encargado de validar los segundos de una coordenada este debe ser valor numerico entero entre 0-59
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 12-03-2015     
     * @param  int $intSegundo          
     * @return string
     */
    
    public function validaSegundosCoordenadas($intSegundo)
    {
        $strMensajeValidacion = '';
        //Grados 
        if(!preg_match('/\d$/i', $intSegundo))
        {
            $strMensajeValidacion = 'Ingrese solo numeros';
        }
        else
        {
            //valido que sea numero entre 0-59
            if($intSegundo < 0 || $intSegundo > 59)
            {                
                $strMensajeValidacion = 'Ingrese solo números entre 0-59';
            }
        }
        return $strMensajeValidacion;
    }

    /**    
     * Documentación para el método 'validaDecimasSegundosCoordenadas'.
     *
     * Descripcion: Metodo encargado de validar las decimas de segundos de una coordenada este debe ser valor numerico entero entre 0-999
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 12-03-2015     
     * @param  int $intDecimaSegundo          
     * @return string
     */
    
    public function validaDecimasSegundosCoordenadas($intDecimaSegundo)
    {
        $strMensajeValidacion = '';
        //Grados 
        if(!preg_match('/\d$/i', $intDecimaSegundo))
        {
            $strMensajeValidacion = 'Ingrese solo numeros';
        }
        else
        {
            //valido que sea numero entre ]0-999
            if($intDecimaSegundo < 0 || $intDecimaSegundo > 999)
            {                
                $strMensajeValidacion = 'Ingrese solo números entre 0-999';
            }
        }
        return $strMensajeValidacion;
    }   
    
    /**    
     * Documentación para el método 'ingresarPuntoCaracteristica'.
     *
     * Descripcion: Metodo que permite ingresar una caracteristica al punto.
     * 
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.0 22-09-2015     
     * 
     * @param array $params[entityPunto             =>    InfoPunto Entity
     *                      strDescripcionCarac     =>    Descripcion de la Caracteristica
     *                      strValor                =>    Valor de la Caracteristica del punto
     *                      strUsrCreacion          =>    Usuario de creacion 
     *                      strIpCreacion           =>    IP de creacion ]
     * 
     * @return \telconet\schemaBundle\Entity\InfoPuntoCaracteristica;
     */
    public function ingresarPuntoCaracteristica($params)
    {
        $entityPunto        = $params['entityPunto'];
        $descripcionCarac   = $params['strDescripcionCarac'];
        $valor              = $params['strValor'];
        $usrCreacion        = $params['strUsrCreacion'];
        $ipCreacion         = $params['strIpCreacion'];
        
        $this->emcom->getConnection()->beginTransaction();
        try
        {    
            $caracteristica = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                   ->findOneBy(array( "descripcionCaracteristica" => $descripcionCarac, 
                                                      "estado" => "Activo"));
            
            /*==== Validacion si existen caracteristicas para eliminarlas ===============================*/
            $arrayParams    = array('entityPunto'                      => $entityPunto,
                                 'strDescripcionCaracteristica'     => $descripcionCarac,
                                 'strEstado'                        => 'Activo');
            
            $arrayIPC       = $this->emcom->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                          ->getPuntoCaracteristica($arrayParams);
            
            if($arrayIPC)
            {
                foreach($arrayIPC as $ipc){
                    $ipc->setEstado('Eliminado');
                    $this->emcom->persist($ipc);
                    $this->emcom->flush(); 
                }
            }
            
            if($descripcionCarac == 'Ruta Georreferencial'){
                $arrayParamsMinimanga   = array('entityPunto'                      => $entityPunto,
                                                'strDescripcionCaracteristica'     => 'RUTA GEOREFERENCIAL MINIMANGA',
                                                'strEstado'                        => 'Activo');
                
                $arrayMinimanga         = $this->emcom->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                                      ->getPuntoCaracteristica($arrayParamsMinimanga);
                
                if(isset($arrayMinimanga) && !empty($arrayMinimanga))
                {
                    foreach($arrayMinimanga as $objMinimanga){
                        $objMinimanga->setEstado('Eliminado');
                        $this->emcom->persist($objMinimanga);
                        $this->emcom->flush(); 
                    }
                }
            }
            
            /*=======================================================================================*/
            //....
            $puntoCaracteristica = new InfoPuntoCaracteristica();
            $puntoCaracteristica->setPuntoId($entityPunto);
            $puntoCaracteristica->setCaracteristicaId($caracteristica);
            $puntoCaracteristica->setValor($valor);
            $puntoCaracteristica->setEstado("Activo");
            $puntoCaracteristica->setUsrCreacion($usrCreacion);
            $puntoCaracteristica->setIpCreacion($ipCreacion);
            $puntoCaracteristica->setFeCreacion(new \DateTime('now'));
            // ...
            $this->emcom->persist($puntoCaracteristica);
            $this->emcom->flush(); 
            $this->emcom->getConnection()->commit();
            $this->emcom->getConnection()->close();
            // ...
            $status = "OK";
            $mensaje = 'Caracteristica ingresada correctamente';
        }
        catch(\Exception $e)
        {
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollBack();
            }
            $this->emcom->getConnection()->close();
                        
            $status             = "ERROR";
            $mensaje            = "Mensaje: ".$e->getMessage();
        }
        if($this->emcom->getConnection()->isTransactionActive())
        {
            $this->emcom->getConnection()->commit();
        }
        $this->emcom->getConnection()->close();
            
        //*RESPUESTA-------------------------------------------------------------*/
        $respuestaFinal = array('status' => $status, 'mensaje' => $mensaje);
        return $respuestaFinal;
        //*----------------------------------------------------------------------*/
        
    }
    
   /**
     * Documentación para el método 'getCanalPuntoVenta'.
     *
     * Obtiene la información del Canal/Punto de Venta relacionado al Punto del cliente
     *
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 1.0 21-12-2015
     * 
     * @param InfoPunto     $entityPunto   Entidad InfoPunto con punto de venta asociado.
     * @param Integer       $empresaId     Identificador de la empresa en sesión
     */
    public function getCanalPuntoVenta($entityPunto, $empresaId)
    {
        //Criterios de consulta de la característica del punto
        $arrayParametros       = array('entityPunto'                  => $entityPunto,
                                       'strDescripcionCaracteristica' => 'PUNTO_DE_VENTA_CANAL',
                                       'strEstado'                    => 'Activo');
        $entityCaracteristica  = $this->emcom->getRepository('schemaBundle:InfoPuntoCaracteristica')->getOnePuntoCaracteristica($arrayParametros);
        
        $objCanalPuntoVenta['strPuntoVenta']     = '';
        $objCanalPuntoVenta['strPuntoVentaDesc'] = '';
        $objCanalPuntoVenta['strCanal']          = '';
        $objCanalPuntoVenta['strCanalDesc']      = '';

        if($entityCaracteristica != null)
        {
            //Se obtiene el Punto de Venta y el Canal (AdmiParametroDet almacena la data del Punto de venta y también del canal)
            $strCanales         = 'CANALES_PUNTO_VENTA';
            $strModulo          = 'COMERCIAL';
            $strVal1            = $entityCaracteristica->getValor();
            $entCanalPuntoVenta = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                              ->getOne($strCanales, $strModulo, '', '', $strVal1, '', '', '', '', $empresaId);
            // Valor1 => Identificador del Punto de Venta.
            // Valor2 => Descriptivo del Punto de Venta.
            // Valor3 => Identificador del Canal.
            // Valor4 => Descriptivo del Canal.
            $objCanalPuntoVenta['strPuntoVenta']     = $entCanalPuntoVenta['valor1'];
            $objCanalPuntoVenta['strPuntoVentaDesc'] = $entCanalPuntoVenta['valor2'];
            $objCanalPuntoVenta['strCanal']          = $entCanalPuntoVenta['valor3'];
            $objCanalPuntoVenta['strCanalDesc']      = $entCanalPuntoVenta['valor4'];
        }
        return $objCanalPuntoVenta;
    }
    
    
    /**
     * Documentacion para funcion ponerLoginEnSesion
     * Pone informacion del login y servicios del login en sesion
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 17-08-2016
     * @param $objSession    (Objeto de la sesion en sistema)
     * @param $intIdPunto    (id del punto que se pondra en sesion)
     * @param $intCodEmpresa (id de la empresa para obtener datos del punto)
     */    
    public function ponerLoginEnSesion($objSession, $intIdPunto, $intCodEmpresa)
    {
        $arrayPtoCliente = $objSession->get('ptoCliente');
        if($arrayPtoCliente['id'] != $intIdPunto)
        {
            $this->emcom->getRepository('schemaBundle:InfoPunto')->setSessionByIdPunto($intIdPunto, $objSession);
            $arrayPtoCliente                  = $objSession->get('ptoCliente');
            $arraySaldoYFacturasAbiertasPunto = $this->emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                            ->getPuntosFacturacionAndFacturasAbiertasByIdPunto(
                                                                $arrayPtoCliente['id'], 
                                                                $this->emcom, 
                                                                $intCodEmpresa);

            $objSession->set('datosFinancierosPunto', $arraySaldoYFacturasAbiertasPunto);

            //servicios del login para toolbar
            $arrayServiciosSession      = array();
            $arrayParametros['EMPRESA'] = $intCodEmpresa;
            $arrayParametros['PUNTO']   = $arrayPtoCliente['id'];
            $arrayParametros['ESTADOS'] = array('Eliminado','Anulado','Rechazada','Rechazado');
            $arrayServicios             = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                      ->getResultadoServiciosPorEmpresaPorPunto($arrayParametros);
            if( $arrayServicios )
            {
                foreach( $arrayServicios as $objServicio )
                {
                    $arrayServicioSession = array();
                    $objInfoPlan          = $objServicio->getPlanId();
                    $objAdmiProducto      = $objServicio->getProductoId();
                    if( $objInfoPlan )
                    {
                        $arrayServicioSession['nombre'] = $objInfoPlan->getNombrePlan();
                    }
                    if( $objAdmiProducto )
                    {
                        $arrayServicioSession['nombre'] = $objAdmiProducto->getDescripcionProducto();
                    }
                    $arrayServicioSession['estado'] = $objServicio->getEstado();
                    $arrayServiciosSession[]        = $arrayServicioSession;
                }
                $objSession->set('numServicios', count($arrayServiciosSession));
                $objSession->set('serviciosPunto', $arrayServiciosSession);
            }
        }        
    }
    
    
    /**
    * Documentación para el método 'generarHistorialPunto'.
    * Funcion que genera el registro de un nuevo historial según los datos del punto enviados como parámetro.
    * 
    * @param mixed $arrayParametros[
    *                               'objInfoPunto'           => Punto al que se asociará el historial respectivo
    *                               'strUsuarioCreacion'     => Usuario de creación.
    *                               'strIpCreacion'          => Dirección IP de creación.
    *                               'strValor'               => Valor u observación del historial.
    *                               'strAccion'              => Acción relacionada.
    *                               ] 
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 21-11-2016
    */
    public function generarHistorialPunto($arrayParametros)
    {
        $objInfoPunto        = $arrayParametros['objInfoPunto']; 
        $strUsuarioCreacion  = $arrayParametros['strUsuarioCreacion']; 
        $strIpCreacion       = $arrayParametros['strIpCreacion']; 
        $strValor            = $arrayParametros['strValor']; 
        $strAccion           = $arrayParametros['strAccion']; 
        
        $this->emcom->getConnection()->beginTransaction();
        try
        {

            $objInfoPuntoHistorial = new InfoPuntoHistorial();
            $objInfoPuntoHistorial->setPuntoId($objInfoPunto);
            $objInfoPuntoHistorial->setFeCreacion(new \DateTime('now'));
            $objInfoPuntoHistorial->setUsrCreacion($strUsuarioCreacion);
            $objInfoPuntoHistorial->setIpCreacion($strIpCreacion);
            $objInfoPuntoHistorial->setValor($strValor);
            $objInfoPuntoHistorial->setAccion($strAccion);
            $this->emcom->persist($objInfoPuntoHistorial); 
            $this->emcom->flush();  
            $this->emcom->getConnection()->commit();          

        }catch (\Exception $e) 
        {
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollBack();
            }
            $this->emcom->getConnection()->close();
            throw $e;
        }          
    }  
    
    
    /**
     * Documentación para el método 'generarInfoPuntoDatoAdicional'.
     * Funcion que genera registro en info_punto_dato_adicional
     *
     * @param mixed $arrayParamDatoAdic[
     *                                  'objInfoPuntoClonado'     => Punto al cual se registrara la informacion en info_punto_dato_adicional
     *                                  'objInfoPersona'          => Persona de la cual se toma el nombre, sector y direccion de envio
     *                                  'strUsrCreacion'          => Usuario de creacion
     *                                  'intIdPunto'              => Id del Punto del cual se clonara la informacion de Datos adicionales
     *                                 ]
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 14-06-2017
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 12-11-2020 Se elimina uso de transacciones ya que la función es invocada desde otras funciones que ya usan transacciones,
     *                         por ende si se hace el commit, se haría el commit de lo que anteriormente se haya programado y continuaría
     *                         la función principal. Sin embargo, si luego ocurriera un error, el rollback sólo se haría de lo que estaba a
     *                         continuación de esta función
     *
     * return object
     */
    public function generarInfoPuntoDatoAdicional($arrayParamDatoAdic)
    {
        $objInfoPuntoClonado       = $arrayParamDatoAdic['objInfoPuntoClonado'];
        $objInfoPersona            = $arrayParamDatoAdic['objInfoPersona'];
        $strUsrCreacion            = $arrayParamDatoAdic['strUsrCreacion'];
        $intIdPunto                = $arrayParamDatoAdic['intIdPunto'];
        $arrayFormasContacto       = $arrayParamDatoAdic['arrayFormasContacto'];
        $strFormaContactoCorreo    = '';
        $strFormaContactoTelefono  = '';
        $boolExistenCorreos        = false;
        $boolExistenTelefonos      = false;
        
        try
        {
            if(!is_object($objInfoPersona))
            {
                throw new \Exception("No se ha encontrado el Cliente definido");
            }
            if(!is_object($objInfoPuntoClonado))
            {
                throw new \Exception("No se ha encontrado Punto Cliente definido");
            }
            
            $objInfoPuntoDatoAdicionalOrigen = $this->emcom->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                                           ->findOneByPuntoId($intIdPunto);
            // Obtengo la informacion de los Datos Adicionales
            if(is_object($objInfoPuntoDatoAdicionalOrigen))
            {
                $objInfoPuntoDatoAdicionalClonado = new InfoPuntoDatoAdicional();
                $objInfoPuntoDatoAdicionalClonado = clone $objInfoPuntoDatoAdicionalOrigen;

                $objInfoPuntoDatoAdicionalClonado->setPuntoId($objInfoPuntoClonado);
                $objInfoPuntoDatoAdicionalClonado->setFeCreacion(new \DateTime('now'));
                $objInfoPuntoDatoAdicionalClonado->setUsrCreacion($strUsrCreacion);
                    
                if(isset($arrayParamDatoAdic['strTipoCrs']) && !empty($arrayParamDatoAdic['strTipoCrs'])
                    && $arrayParamDatoAdic['strTipoCrs'] == 'Cambio_Razon_Social_Por_Login')
                {
                    // Se marcaran todos los puntos trasladados como padres de Facturacion para el CRS por Punto
                    $objInfoPuntoDatoAdicionalClonado->setEsPadreFacturacion('S');
                }
                
                if($objInfoPuntoDatoAdicionalClonado->getEsPadreFacturacion() == 'S')
                {
                    /* Se seta a NULL para que la funcion getMailTelefonoByPunto obtenga la informacion de correos y telefonos
                       del contacto de Facturacion del Punto o del cliente.
                     * Funcion llama a DB_FINANCIERO.FNCK_COM_ELECTRONICO.GET_ADITIONAL_DATA_BYPUNTO */
                    $objInfoPuntoDatoAdicionalClonado->setEmailEnvio(null);
                    $objInfoPuntoDatoAdicionalClonado->setTelefonoEnvio(null);
                    $this->emcom->persist($objInfoPuntoDatoAdicionalClonado);
                    $this->emcom->flush();
                    
                    // Se marcaran que utilizan Data Adicional
                    $objInfoPuntoDatoAdicionalClonado->setDatosEnvio('S');
                    $objInfoPuntoDatoAdicionalClonado->setNombreEnvio($this->emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                                         ->getFVarcharClean(sprintf("%s", $objInfoPersona)));
                    $objInfoPuntoDatoAdicionalClonado->setSectorId($objInfoPuntoClonado->getSectorId());
                    $objInfoPuntoDatoAdicionalClonado->setDireccionEnvio($this->emFinanciero
                                                                              ->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                              ->getFVarcharClean($objInfoPersona->getDireccionTributaria()));
                    // OBTENGO CORREOS DE ENVIO
                    $arrayParametroGetCorreo                = array();
                    $arrayParametroGetCorreo['strTipoDato'] = 'MAIL';
                    $arrayParametroGetCorreo['intIdPunto']  = $objInfoPuntoClonado->getId();
                    
                    $arrayMailFono = $this->emcom->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                                 ->getMailTelefonoByPunto($arrayParametroGetCorreo);
                    
                    //Entra si tiene correos, sino guardara correos ingresados en el Grid
                    if(isset($arrayMailFono['strMailFono']) && !empty($arrayMailFono['strMailFono']))
                    {
                        $intCantidad         = 0;
                        $arrayMailFono       = explode(";", $arrayMailFono['strMailFono']);
                        $arrayUniqueMailFono = array_unique($arrayMailFono);
                        
                        foreach($arrayUniqueMailFono as $strSingleFormaContacto)
                        {
                            //Si el correo es valido lo concatena
                            if(false === !filter_var($strSingleFormaContacto, FILTER_VALIDATE_EMAIL))
                            {
                                $boolExistenCorreos      = true;
                                $intCantidad             = $intCantidad + 1;
                                $strFormaContactoCorreo .= trim($strSingleFormaContacto). ';';
                            }
                            if($intCantidad == 2)
                            {
                                //Sale del foreach que itera los correos, solo se consideran un maximo de 2 correos
                                break;
                            }
                        }
                    }
                    // OBTENGO TELEFONOS DE ENVIO
                    $arrayParametroGetTelefono                = array();
                    $arrayParametroGetTelefono['strTipoDato'] = 'FONO';
                    $arrayParametroGetTelefono['intIdPunto']  = $objInfoPuntoClonado->getId();
                    
                    $arrayMailFono = $this->emcom->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                                 ->getMailTelefonoByPunto($arrayParametroGetTelefono);

                    //Entra si tiene telefonos, sino guardara telefonos ingresados en el Grid
                    if(isset($arrayMailFono['strMailFono']) && !empty($arrayMailFono['strMailFono']))
                    {
                        $intCantidad         = 0;
                        $arrayMailFono       = explode(";", $arrayMailFono['strMailFono']);
                        $arrayUniqueMailFono = array_unique($arrayMailFono);
                        
                        foreach($arrayUniqueMailFono as $strSingleFormaContacto)
                        {
                            $boolExistenTelefonos      = true;
                            $intCantidad               = $intCantidad + 1;
                            $strFormaContactoTelefono .= trim($strSingleFormaContacto). ';';
                            if($intCantidad == 2)
                            {
                                //Sale del foreach que itera los telefonos, solo se consideran un maximo de 2 telefonos
                                break;
                            }
                        }
                    }
                    // Si no existen correos y telefonos del contacto de Facturacion, Se guardaran los telefonos y correos existentes en el Grid
                    // de formas de Contacto 
                    // Obtengo Array del Grid de Formas de Contactos                    
                    $intCantCorreos  = 0;
                    $intCantTelefono = 0;
                    for($i = 0; $i < count($arrayFormasContacto); $i ++)
                    {
                        $objAdmiFormaContacto = $this->emcom->getRepository('schemaBundle:AdmiFormaContacto')
                                                            ->findPorDescripcionFormaContacto($arrayFormasContacto[$i]['formaContacto']);

                        if(!$boolExistenCorreos && is_object($objAdmiFormaContacto) 
                            && strpos(strtoupper($objAdmiFormaContacto->getDescripcionFormaContacto()), 'CORREO') !== false)
                        {
                            $strSingleFormaContacto = $arrayFormasContacto[$i]['valor'];

                            //Si el correo es valido lo concatena
                            if((false === !filter_var($strSingleFormaContacto, FILTER_VALIDATE_EMAIL)) && $intCantCorreos < 2)
                            {
                                $intCantCorreos          = $intCantCorreos + 1;
                                $strFormaContactoCorreo .= trim($strSingleFormaContacto) . ';';
                            }
                        }
                        if(!$boolExistenTelefonos && is_object($objAdmiFormaContacto)
                            && strpos(strtoupper($objAdmiFormaContacto->getDescripcionFormaContacto()), 'TELEFONO') !== false)
                        {
                            $strSingleFormaContacto = $arrayFormasContacto[$i]['valor'];

                            if($intCantTelefono < 2)
                            {
                                $intCantTelefono           = $intCantTelefono + 1;
                                $strFormaContactoTelefono .= trim($strSingleFormaContacto) . ';';
                            }
                        }
                    }
                    if($strFormaContactoCorreo !='')
                    {
                        $strFormaContactoCorreo   = substr($strFormaContactoCorreo, 0, -1);
                    }
                    if($strFormaContactoTelefono !='')
                    {
                       $strFormaContactoTelefono = substr($strFormaContactoTelefono, 0, -1); 
                    }                    
                    $objInfoPuntoDatoAdicionalClonado->setEmailEnvio($strFormaContactoCorreo);
                    $objInfoPuntoDatoAdicionalClonado->setTelefonoEnvio($strFormaContactoTelefono);
                    $objInfoPuntoDatoAdicionalClonado->setEsElectronica('S');
                    $objInfoPuntoDatoAdicionalClonado->setGastoAdministrativo('N');                   
                    $objInfoPuntoDatoAdicionalClonado->setFeUltMod(null);
                    $objInfoPuntoDatoAdicionalClonado->setUsrUltMod(null);
                    $this->emcom->persist($objInfoPuntoDatoAdicionalClonado);
                    $this->emcom->flush();
                }
                return $objInfoPuntoDatoAdicionalClonado;
            }
        }
        catch(\Exception $e)
        {
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollBack();
            }
            $this->emcom->getConnection()->close();
            throw $e;
        }
    }
   /** 
    * Documentación para el método 'eliminarContratoExternoDigital'.
    * 
    * Descripcion: Metodo encargado de eliminar documentos a partir del id de la referencia enviada
    * 
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 15-02-2017 
    * 
    * @param  array $arrayParametros [
    *                                  "intIdDocumento"        : Id del documento
    *                                  "strUsrSesion"          : usuario de modificacion,     
    *                                ]    
    */
    
    public function eliminarContratoExternoDigital($arrayParametros)
    {          
        $objFechaActualiza = new \DateTime('now');
        $this->emComunicacion->getConnection()->beginTransaction();
        try
        {  
            $objInfoDocumento =  $this->emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($arrayParametros['intIdDocumento']);
            if( is_object($objInfoDocumento) )
            {            
                $strPath = $objInfoDocumento->getUbicacionFisicaDocumento();
                
                if (file_exists($this->strPathTelcos.$strPath))
                {
                    unlink($this->strPathTelcos.$strPath);
                }

                $objInfoDocumento->setEstado("Inactivo");
                $objInfoDocumento->setFeUltMod($objFechaActualiza);
                $objInfoDocumento->setUsrUltMod($arrayParametros['strUsrSesion']);
                $this->emComunicacion->persist($objInfoDocumento);
                $this->emComunicacion->flush();               

                $objInfoDocumentoRelacion = $this->emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')
                                                                 ->findByDocumentoId($arrayParametros['intIdDocumento']);
                
                if(is_object($objInfoDocumentoRelacion))
                {
                    foreach($objInfoDocumentoRelacion as $objDetDocumento)
                    {
                        $objDetDocumento->setEstado("Inactivo");
                        $this->emComunicacion->persist($objDetDocumento);
                        $this->emComunicacion->flush();
                    }
                }
             $this->emComunicacion->getConnection()->commit();
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
    * Documentación para el método 'guardarContratoExternoDigital'.
    * 
    * Descripcion: Metodo encargado de guardar documentos digitales de tipo Venta Externa
    * 
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 19-02-2017 
    * 
    * @author Carlos Julio Pérez Quizhpe <cjperez@telconet.ec>
    * @version 1.1 24-06-2021 
    * Permitir subir más de un documento en el contrato CloudForm.
    * 
    * @author Carlos Julio Pérez Quizhpe <cjperez@telconet.ec>
    * @version 1.2 09-07-2021 
    * Restringir los tipos de archivo a subir.
    *
    * @author Néstor Naula <nnaulal@telconet.ec>
    * @version 1.3 05-05-2021 - Se redirecciona los archivos al NFS
    * @since 1.2
    * 
    * @param  array $arrayParametros [
    *                                  "arrayDatosForm"   : Id del documento
    *                                  "intIdPunto"       : usuario de modificacion,  
    *                                  "strCodEmpresa"    : usuario de modificacion,   
    *                                  "strUsrCreacion"   : usuario de modificacion,   
    *                                  "strIpClient"      : usuario de modificacion,   
    *                                ]     
    */
    public function guardarContratoExternoDigital($arrayParametros)
    {    
        $objFechaCreacion = new \DateTime('now');      
        $this->emComunicacion->getConnection()->beginTransaction();
        try
        {  
            $objInfoPunto = $this->emcom->getRepository('schemaBundle:InfoPunto')->find($arrayParametros['intIdPunto']);
            if( is_object($objInfoPunto ))
            {    
                if($arrayParametros['strPrefijoEmpresa'] == 'TN')
                {
                    $this->emcom->getConnection()->beginTransaction();
                    
                    //Para flujo de empresa TN-Cloudform se valida que el documento ya haya sido ingresado y se encuentre como registro 
                    //en estado Activo para continuar con el flujo
                    $arrayParametrosDocCloud                     = array();
                    $arrayParametrosDocCloud['intIdPunto']       = $arrayParametros['intIdPunto'];
                    $arrayParametrosDocCloud['strCodigoTipoDoc'] = 'CLOUD';
                    $arrayDocumento = $this->emComunicacion->getRepository("schemaBundle:InfoDocumento")
                                                           ->getResultadoContratosExternosDigitales($arrayParametrosDocCloud);

                }
                
                $arrayServicios      = $arrayParametros['arrayServicios'];
                //Guardo files                       
                $arrayDatosForm      = $arrayParametros['arrayDatosForm'];
                $arrayDatosFormFiles = $arrayDatosForm['arrayDatosFormFiles'];
                $arrayTipoDocumentos = $arrayDatosForm['arrayTipoDocumentos'];       
                
                $strNombreArchivo = '';
                $strMensaje       = '';
                
                //Determinar de acuerdo a la empresa en sesión que tipo de documento será almacenado en el Punto
                if($arrayParametros['strPrefijoEmpresa'] == 'MD' || $arrayParametros['strPrefijoEmpresa'] == 'EN')
                {
                    $strNombreArchivo = "documento_digital_vtaexterna";
                    $strMensaje       = "Archivo tipo Venta Externa agregado al Login # ".$objInfoPunto->getLogin();
                }
                else
                {
                    $strNombreArchivo = "documento_digital_cloudform_".$objInfoPunto->getLogin();
                    $strMensaje       = "Archivo tipo Contrato Cloudform agregado al Login # ".$objInfoPunto->getLogin();
                }
                
                // Leer los parámetros
                $strNombreParametro  = "CONTRATO_CLOUDFORM_TIPO_ARCHIVO";
                $strTiposPermitidos  = "";
                $objAdmiParametroCab = $this->emcom->getRepository('schemaBundle:AdmiParametroCab')
                    ->findOneBy(array('nombreParametro' => $strNombreParametro,
                                      'estado'          => 'Activo'));
                    
                if (is_object($objAdmiParametroCab)) 
                {
                    $objAdmiParametroDet = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                        ->findOneBy(array(  "parametroId" => $objAdmiParametroCab->getId(),
                                            "estado"      => "Activo"));
                    if (is_object($objAdmiParametroDet))
                    {
                        $strTiposPermitidos = $objAdmiParametroDet->getValor1();
                    }
                    else
                    {
                        throw new \Exception("Error, no se han definido los tipos de archivo permitidos para subir");
                    }
                }
                else
                {
                    throw new \Exception("Error, no está definido el parámetro [$strNombreParametro]");
                }

                /**
                 * Obtener la extensión de cada uno de los archivos y
                 * determinar si la extensión es permitida.
                 */
                foreach ($arrayDatosFormFiles as $key => $arrayImagenes)                 
                {  
                    foreach ( $arrayImagenes as $keyImagen => $objFile) 
                    {        
                        if( $objFile )
                        {
                            $strExtension      = strtoupper($objFile->getClientOriginalExtension());
                            $strNombreOriginal = $objFile->getClientOriginalName();
                            
                            /**
                             * Verificar si el tipo de archivo está dentro de los tipos
                             * permitidos de acuerdo al parámetro indicado.
                             */
                            $arrayTiposPermitidos = explode (",", $strTiposPermitidos);
                            $boolEsTipoPermitido = false;
                            foreach ($arrayTiposPermitidos as $strTipo) 
                            {
                                if ($strExtension == $strTipo) 
                                {
                                    $boolEsTipoPermitido = true;
                                }
                            }
                            
                            if (!$boolEsTipoPermitido)
                            {
                                $strMensaje1 = "Error, archivo no permitido: [$strNombreOriginal].\n";
                                $strMensaje2 = "Los archivos permitidos deben tener la extensión [$strTiposPermitidos]";
                                throw new \Exception($strMensaje1 . " " . $strMensaje2);
                            }
                        }
                    }
                }

                foreach ($arrayDatosFormFiles as $key => $arrayImagenes)                 
                {  
                    foreach ( $arrayImagenes as $keyImagen => $strValor) 
                    {        
                        if( $strValor )
                        {  
                            
                            $strApp                 = 'TelcosWeb';
                            $strSubModulo           = 'ArchivoExtDigitalPunto';

                            $arrayKey               = array("key" => $objInfoPunto->getLogin());
                            $strArchivo             = file_get_contents($strValor);
                            $strBase64Archivopdf    = base64_encode($strArchivo);
                            $arrayPathAdicional     = array($arrayKey);
                            $strExtension           = $strValor->guessExtension();
                            $strNombreArchivo       = $strNombreArchivo."_".uniqid().".".$strExtension;

                            $arrayParamNfs          = array(
                                'prefijoEmpresa'       => $arrayParametros['strPrefijoEmpresa'],
                                'strApp'               => $strApp,
                                'strSubModulo'         => $strSubModulo,
                                'arrayPathAdicional'   => $arrayPathAdicional,
                                'strBase64'            => $strBase64Archivopdf,
                                'strNombreArchivo'     => $strNombreArchivo,
                                'strUsrCreacion'       => $strUsuario
                            );

                            $arrayRespuestaNfs = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
                            
                            if(empty($arrayRespuestaNfs) || $arrayRespuestaNfs['intStatus'] != 200)
                            {
                                throw new \Exception("Ha ocurrido un error al subir el archivo. Por favor reporte a Sistemas");
                            }

                            $objInfoDocumento = new InfoDocumento();                             
                            $objInfoDocumento->setNombreDocumento($strNombreArchivo);
                            $objInfoDocumento->setUbicacionLogicaDocumento($strNombreArchivo);
                            $objInfoDocumento->setUbicacionFisicaDocumento($arrayRespuestaNfs['strUrlArchivo']);
                            $objInfoDocumento->setFechaDocumento( $objFechaCreacion );                                                                 
                            $objInfoDocumento->setUsrCreacion( $arrayParametros['strUsrCreacion'] );
                            $objInfoDocumento->setFeCreacion( $objFechaCreacion );
                            $objInfoDocumento->setIpCreacion( $arrayParametros['strIpClient'] );
                            $objInfoDocumento->setEstado( 'Activo' );                                                           
                            $objInfoDocumento->setMensaje($strMensaje);                                        
                            $objInfoDocumento->setEmpresaCod( $arrayParametros['strCodEmpresa'] );                        
                            
                            $objTipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                                       ->find($arrayTipoDocumentos[$keyImagen]);                                                                                                                                    
                            if(is_object($objTipoDocumentoGeneral))
                            {            
                                $objInfoDocumento->setTipoDocumentoGeneralId($objTipoDocumentoGeneral->getId());                     
                            }

                            $objTipoDocumento=$this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                                   ->findOneByExtensionTipoDocumento(strtoupper($objInfoDocumento->getExtension()));                                    

                            if(is_object($objTipoDocumento))
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
                                $objAdmiTipoDocumento->setUsrCreacion( $arrayParametros['strUsrCreacion'] );
                                $objAdmiTipoDocumento->setFeCreacion( $objFechaCreacion );                        
                                $this->emComunicacion->persist( $objAdmiTipoDocumento );
                                $this->emComunicacion->flush(); 
                                $objInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);    
                            }                      
                            
                            $this->emComunicacion->persist($objInfoDocumento);
                            $this->emComunicacion->flush();  
                            
                            if($arrayParametros['strPrefijoEmpresa'] == 'MD' || $arrayParametros['strPrefijoEmpresa'] == 'EN')
                            {
                                foreach( $arrayServicios as $intIdServicio )
                                { 
                                    $objInfoServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
                                    if(!is_object($objInfoServicio))
                                    {
                                        throw new \Exception("No se ha encontrado el Servicio");
                                    }
                                    if($objInfoServicio->getEstado() == 'Pre-servicio')
                                    {
                                        $objInfoServicio->setEstado('Factible');
                                        $this->emcom->persist($objInfoServicio);

                                        // Guardo historial para el servicio
                                        $objInfoServicioHistorial = new InfoServicioHistorial();
                                        $objInfoServicioHistorial->setEstado($objInfoServicio->getEstado());
                                        $objInfoServicioHistorial->setFeCreacion($objFechaCreacion);
                                        $objInfoServicioHistorial->setIpCreacion($arrayParametros['strIpClient']);
                                        $objInfoServicioHistorial->setObservacion('Servicio Factible');
                                        $objInfoServicioHistorial->setServicioId($objInfoServicio);
                                        $objInfoServicioHistorial->setUsrCreacion($arrayParametros['strUsrCreacion']);
                                        $this->emcom->persist($objInfoServicioHistorial);
                                        $this->emcom->flush();
                                    }
                                    $objCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                                     ->findOneBy(array('descripcionCaracteristica' => 'Proveedor Venta',
                                                                                       'tipo'                      => 'COMERCIAL'));
                                    if(!is_object($objCaracteristica))
                                    {
                                        throw new \Exception("No se ha definido la característica : Proveedor Venta");
                                    }
                                    if(!$objInfoServicio->getProductoId()->getId())
                                    {   
                                        throw new \Exception("No se ha encontrado Producto Definido en el Servicio");
                                    }                                                                 
                                    $objProdCaract = $this->emcom->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                 ->findOneBy(array( 
                                                                                   "productoId"       => $objInfoServicio->getProductoId()->getId(),
                                                                                   "caracteristicaId" => $objCaracteristica->getId(),
                                                                                   "estado"           => "Activo"
                                                                             )); 
                                   if(!is_object($objProdCaract))
                                   {
                                       throw new \Exception("No se ha definido la característica : Proveedor Venta, para el Producto");
                                   }
                                    $objServProdCaracExis =  $this->emcom->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                         ->findBy(array("servicioId"                => $objInfoServicio->getId(),
                                                                                        "productoCaracterisiticaId" => $objProdCaract->getId(),
                                                                                        "estado"                    => 'Activo'));
                                    if(!is_object($objServProdCaracExis))
                                    {
                                        //Guardo el Proveedor Externo de la Venta asociado al servicio a nivel de Caracteristica del Servicio
                                        $objServicioProdCaract = new InfoServicioProdCaract();
                                        $objServicioProdCaract->setServicioId($objInfoServicio->getId());
                                        $objServicioProdCaract->setProductoCaracterisiticaId($objProdCaract->getId());
                                        $objServicioProdCaract->setValor($arrayParametros['idPersonaRolProveedor']);
                                        $objServicioProdCaract->setEstado('Activo');
                                        $objServicioProdCaract->setUsrCreacion($arrayParametros['strUsrCreacion']);
                                        $objServicioProdCaract->setFeCreacion($objFechaCreacion);

                                        $this->emcom->persist($objServicioProdCaract);
                                        $this->emcom->flush();
                                    }
                                    // Guardo relacion de los documentos digitales con los servicios
                                    $objInfoDocumentoRelacion = new InfoDocumentoRelacion(); 
                                    $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());                    
                                    $objInfoDocumentoRelacion->setModulo('COMERCIAL'); 
                                    $objInfoDocumentoRelacion->setPuntoId($objInfoPunto->getId());        
                                    $objInfoDocumentoRelacion->setServicioId($objInfoServicio->getId());        
                                    $objInfoDocumentoRelacion->setPersonaEmpresaRolId($objInfoPunto->getPersonaEmpresaRolId()->getId());        
                                    $objInfoDocumentoRelacion->setEstado('Activo');                                                                                   
                                    $objInfoDocumentoRelacion->setFeCreacion( $objFechaCreacion );                        
                                    $objInfoDocumentoRelacion->setUsrCreacion( $arrayParametros['strUsrCreacion'] );
                                    $this->emComunicacion->persist( $objInfoDocumentoRelacion );                        
                                    $this->emComunicacion->flush();
                                }
                            }
                            else//Si es TN se agrega los documentos a nivel de Punto
                            {
                                // Guardo relacion de los documentos digitales con el punto
                                $objInfoDocumentoRelacion = new InfoDocumentoRelacion(); 
                                $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());                    
                                $objInfoDocumentoRelacion->setModulo('COMERCIAL'); 
                                $objInfoDocumentoRelacion->setPuntoId($objInfoPunto->getId());
                                $objInfoDocumentoRelacion->setPersonaEmpresaRolId($objInfoPunto->getPersonaEmpresaRolId()->getId());        
                                $objInfoDocumentoRelacion->setEstado('Activo');                                                                                   
                                $objInfoDocumentoRelacion->setFeCreacion( $objFechaCreacion );                        
                                $objInfoDocumentoRelacion->setUsrCreacion( $arrayParametros['strUsrCreacion'] );
                                $this->emComunicacion->persist( $objInfoDocumentoRelacion );                        
                                $this->emComunicacion->flush();
                                
                                //Si la carga es la correcta se generará una solicitud automática para autorización de área de cobranzas
                                //proceso Cloudform
                                $objTipoSolicitud  = $this->emcom->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                 ->findOneByDescripcionSolicitud("SOLICITUD APROBACION CLOUDFORM");
                                
                                if(is_object($objTipoSolicitud))
                                {
                                    $objSolicitud = new InfoDetalleSolicitud();
                                    $objSolicitud->setTipoSolicitudId($objTipoSolicitud);
                                    $objSolicitud->setEstado("PendienteAutorizar");
                                    $objSolicitud->setUsrCreacion($arrayParametros['strUsrCreacion']);
                                    $objSolicitud->setFeCreacion($objFechaCreacion);
                                    $objSolicitud->setObservacion('Se genera Solicitud de autorización de Contrato para Servicios de CloudForm');
                                    $this->emcom->persist($objSolicitud);
                                    $this->emcom->flush();
                                    
                                    $objDetalleSolHist = new InfoDetalleSolHist();
                                    $objDetalleSolHist->setDetalleSolicitudId($objSolicitud);
                                    $objDetalleSolHist->setObservacion('Se genera Solicitud de autorización de Contrato para Servicios de CloudForm');
                                    $objDetalleSolHist->setIpCreacion($arrayParametros['strIpClient']);
                                    $objDetalleSolHist->setFeCreacion($objFechaCreacion);
                                    $objDetalleSolHist->setUsrCreacion($arrayParametros['strUsrCreacion']);
                                    $objDetalleSolHist->setEstado("PendienteAutorizar");
                                    $this->emcom->persist($objDetalleSolHist);
                                    $this->emcom->flush();
                                    
                                    $objCaractPunto = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                                                                  ->findOneBy( array('estado'                    => "Activo",
                                                                                     'descripcionCaracteristica' => "ID_PUNTO") );
                                    if(is_object($objCaractPunto))
                                    {
                                        $objDetalleSolCaracteristica = new InfoDetalleSolCaract();
                                        $objDetalleSolCaracteristica->setCaracteristicaId($objCaractPunto);
                                        $objDetalleSolCaracteristica->setDetalleSolicitudId($objSolicitud);
                                        $objDetalleSolCaracteristica->setEstado('PendienteAutorizar');
                                        $objDetalleSolCaracteristica->setFeCreacion($objFechaCreacion);
                                        $objDetalleSolCaracteristica->setUsrCreacion($arrayParametros['strUsrCreacion']);
                                        $objDetalleSolCaracteristica->setValor($objInfoPunto->getId());
                                        $this->emcom->persist($objDetalleSolCaracteristica);
                                        $this->emcom->flush();
                                    }
                                }
                                
                                //Verificar el canton al cual pertenece el punto
                                $strNombreCanton = '';
                                $intIdCanton     = 0;
                                $intIdOficina    = $objInfoPunto->getPuntoCoberturaId()->getOficinaId();
                                        
                                $objOficina      = $this->emcom->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficina);

                                if(is_object($objOficina))
                                {
                                    $objCanton = $this->emcom->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());

                                    if(is_object($objCanton))
                                    {
                                        $strRegion = $objCanton->getProvinciaId()->getRegionId()->getNombreRegion();
                                    }
                                }

                                $arrayParametroRegion = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('RELACION REGION CON CIUDAD PARA DATACENTER', 
                                                                                   'COMERCIAL', 
                                                                                   '',
                                                                                   $strRegion,
                                                                                   '', 
                                                                                   '',
                                                                                   '',
                                                                                   '', 
                                                                                   '', 
                                                                                   $arrayParametros['strCodEmpresa']);
                                if(!empty($arrayParametroRegion))
                                {
                                    $strNombreCanton = $arrayParametroRegion['valor1'];
                                                                        
                                    $objCanton = $this->emGeneral->getRepository("schemaBundle:AdmiCanton")->findOneByNombreCanton($strNombreCanton);

                                    if(is_object($objCanton))
                                    {
                                        $intIdCanton = $objCanton->getId();
                                    }
                                }
                                
                                //generación de tarea automática para informar al departamento de Cobranzas la acción de debe realizar
                                $arrayInfoEnvio   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->get('CLOUDFORM TAREAS POR DEPARTAMENTO', 
                                                                          'SOPORTE', 
                                                                          '',
                                                                          'AUTORIZACION DE SOLICITUD DE CONTRATO CLOUDFORM',
                                                                          $strNombreCanton, 
                                                                          '',
                                                                          '',
                                                                          '', 
                                                                          '', 
                                                                          $arrayParametros['strCodEmpresa']);
                                
                                $strRightArrow  = '<i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;';
                                $strObservacion = '<b>Tarea Automática : </b><br>Se generó tarea para revisión y autorización de contrato '
                                                . 'subido para órdenes de Servicio nuevas para la plataforma <b>CloudForm</b><br>'
                                                . $strRightArrow.'<b>Login : </b> '.$objInfoPunto->getLogin().'<br>'
                                                . $strRightArrow.'<b>Tipo Solicitud :</b> SOLICITUD APROBACION CLOUDFORM<br>'
                                                . $strRightArrow.'<b>Estado Solicitud :</b> PendienteAutorizar';

                                //Tarea a cobranzas
                                $arrayParametrosEnvioPlantilla                      = array();
                                $arrayParametrosEnvioPlantilla['strObservacion']    = $strObservacion;
                                $arrayParametrosEnvioPlantilla['strUsrCreacion']    = $arrayParametros['strUsrCreacion'];
                                $arrayParametrosEnvioPlantilla['strIpCreacion']     = $arrayParametros['strIpClient'];
                                $arrayParametrosEnvioPlantilla['intDetalleSolId']   = $objSolicitud->getId();
                                $arrayParametrosEnvioPlantilla['strTipoAfectado']   = 'Cliente';
                                $arrayParametrosEnvioPlantilla['objPunto']          = $objInfoPunto;
                                $arrayParametrosEnvioPlantilla['strCantonId']       = $intIdCanton;
                                $arrayParametrosEnvioPlantilla['strEmpresaCod']     = $arrayParametros['strCodEmpresa'];
                                $arrayParametrosEnvioPlantilla['strPrefijoEmpresa'] = $arrayParametros['strPrefijoEmpresa'];

                                $strNumeroTarea = '';
                                
                                foreach($arrayInfoEnvio as $array)
                                {
                                    $objTarea  = $this->emSoporte->getRepository("schemaBundle:AdmiTarea")->findOneByNombreTarea($array['valor3']);

                                    $arrayParametrosEnvioPlantilla['arrayCorreos']   = array($array['valor2']);
                                    $arrayParametrosEnvioPlantilla['intTarea']       = is_object($objTarea)?$objTarea->getId():'';

                                    //Se obtiene el departamento
                                    $objDepartamento = $this->emSoporte->getRepository("schemaBundle:AdmiDepartamento")
                                                                       ->findOneByNombreDepartamento($array['valor4']);

                                    $arrayParametrosEnvioPlantilla['objDepartamento']    = $objDepartamento;
                                    $arrayParametrosEnvioPlantilla["strBanderaTraslado"] = "";
                                    $strNumeroTarea = $this->serviceInfoCambiarPlan->crearTareaRetiroEquipoPorDemo($arrayParametrosEnvioPlantilla);
                                }
                                
                                if(empty($strNumeroTarea))
                                {
                                    throw new \Exception("Error al genera la tarea automática, porfavor notificar a Sistemas");
                                }
                                
                                //genera historial sobre servicios cloud del login gestionado
                                $arrayServicios = $this->emcom->getRepository("schemaBundle:InfoServicio")->findByPuntoId($objInfoPunto->getId());
                                
                                foreach($arrayServicios as $objServicio)
                                {
                                    $objProducto = $objServicio->getProductoId();
                                    
                                    //Verificar si los servicios son de facturación por consumo para escribir historial con el número de tarea
                                    //para seguimiento de la autorización
                                    $boolEsConsumo = $this->serviceTecnico->isContieneCaracteristica($objProducto,'FACTURACION POR CONSUMO');
                                    
                                    if($boolEsConsumo)
                                    {
                                        $objInfoServicioHistorial = new InfoServicioHistorial();
                                        $objInfoServicioHistorial->setEstado($objServicio->getEstado());
                                        $objInfoServicioHistorial->setFeCreacion($objFechaCreacion);
                                        $objInfoServicioHistorial->setIpCreacion($arrayParametros['strIpClient']);
                                        $objInfoServicioHistorial->setObservacion('Se generó solicitud de autorización de contrato '
                                                                                . 'cloudform, la tarea generada es la siguiente:'
                                                                                . ' <b>#'.$strNumeroTarea.'</b>');
                                        $objInfoServicioHistorial->setServicioId($objServicio);
                                        $objInfoServicioHistorial->setUsrCreacion($arrayParametros['strUsrCreacion']);
                                        $this->emcom->persist($objInfoServicioHistorial);
                                        $this->emcom->flush();
                                    }
                                }
                            }
                        }
                    }                       
                }
                $this->emcom->commit();
                $this->emComunicacion->getConnection()->commit();  
                return $objInfoPunto;
            }
            else
            {
                throw new \Exception("No se ha encontrado Punto Cliente definido");
            }
       }
       catch(\Exception $e)
       {                                 
           if ($this->emComunicacion->getConnection()->isTransactionActive())
           {
               $this->emComunicacion->getConnection()->rollback();
           }   
           
           if ($this->emcom->getConnection()->isTransactionActive())
           {
               $this->emcom->getConnection()->rollback();
           }  
           
           $this->emComunicacion->getConnection()->close();  
           $this->emcom->getConnection()->close(); 
           
           throw $e;
       }        
    }
    
    /** 
    * Documentación para el método 'obtenerNumeroMaximoDeCorreosTelefonos'.
    * 
    * Descripcion: Devuelve el numero maximo de correos y el maximo de numeros de telefonos para la ventana de datos de envio.
    * 
    * @author Ricardo Coello Quezada <rcoello@telconet.ec>
    * @version 1.0 09-06-2017 
    * 
    * @param  array $arrayParametros [
    *                                  "strNombreParametro"    : Nombre del parametro,
    *                                  "strModulo"             : Modulo del parametro,
    *                                  "strProceso"            : Proceso del parametro,
    *                                  "strPrefijoEmpresa"     : Prefijo de la empresa,
    *                                  "strIdEmpresa"          : Id de la empresa,   
    *                                ]     
    */
    public function obtenerNumeroMaximoDeCorreosTelefonos( $arrayParametros )
    {
        $intNumeroMaxCorreos    = 0;
        $intNumeroMaxTelefonos  = 0;
        $arrayNumerosMaximos    = array ();
        $arrayAdmiParametroDet  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->get($arrayParametros['strNombreParametro'],
                                                        $arrayParametros['strModulo'],
                                                        $arrayParametros['strProceso'],
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        $arrayParametros['strPrefijoEmpresa'],
                                                        $arrayParametros['strIdEmpresa']
                                                        );
        
        if($arrayAdmiParametroDet && count($arrayAdmiParametroDet) > 0)
        {
                foreach($arrayAdmiParametroDet as $arrayParametroRow)
                {
                    if (!empty($arrayParametroRow['valor2']))
                    {
                        if('MAX_CORREOS'   === $arrayParametroRow['valor1'])
                        {
                            $intNumeroMaxCorreos                            = (int)$arrayParametroRow['valor2'];
                            $arrayNumerosMaximos['intNumeroMaxCorreos']     = $intNumeroMaxCorreos;
                        }

                        if('MAX_TELEFONOS' === $arrayParametroRow['valor1'])
                        {
                            $intNumeroMaxTelefonos                          = (int)$arrayParametroRow['valor2'];
                            $arrayNumerosMaximos['intNumeroMaxTelefonos']   = $intNumeroMaxTelefonos;
                        }
                    }
                }//( $arrayAdmiParametroDet as $arrayParametroRow )
         }//( $arrayAdmiParametroDet )
        
        return $arrayNumerosMaximos;
    }

    /**
    * Documentación para el método 'actualizarCoordenadaDelPunto'.
    *
    * Descripcion: Actualiza las coordenadas del punto
    *
    * @author Walther Joao Gaibor <rcoello@telconet.ec>
    * @version 1.0 26-02-2018
    *
    * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
    * @version 1.1 20-06-2020
    *
    * Se agrega bandera boolNoValidarRegistro para que no valide la cantidad de registros
    * en la info_punto.
    * 
    * 
    * @author: Ronny Morán <rmoranc@telconet.ec>
    * @version: 1.2 25/03/2021
    * Se agrega dirección según nueva ubicación desde el móvil técnico.
    * 
    * 
    * @author: Ronny Morán <rmoranc@telconet.ec>
    * @version: 1.3 22/04/2021
    * Se valida dirección para su actualización
    * 
    * 
    * @param  array $arrayParametros [
    *                                  "intIdPunto"         :integer: id del punto,
    *                                  "strLatitud"         :string:  coordenada de latitud del punto,
    *                                  "strLongitud"        :string:  coordenada de latitud del punto,
    *                                  "strCodEmpresa"      :string:  Codigo de la empresa,
    *                                  "strUsrCreacion"     :string:  Usuario de creación,
    *                                  "strIpCreacion"      :string:  Ip de creación.
    *                                  "boolNoValidarRegistro" :Boolean: Bandera boolNoValidarRegistro,
    *                                  "strDireccion"       :string: Dirección del punto.
    *                                ]
    */
    public function actualizarCoordenadaDelPunto($arrayParametros)
    {
        $arrayRespuesta           = array();
        $strCordenadaHistorial    = '';
        $this->emcom->getConnection()->beginTransaction();
        $boolNoValidarRegistro    = false;
        try
        {
            if(!is_null($arrayParametros['boolNoValidarRegistro']))
            {
                $boolNoValidarRegistro = $arrayParametros['boolNoValidarRegistro'];
            }

            $objInfoPunto = $this->emcom->getRepository('schemaBundle:InfoPunto')
                                        ->findOneById($arrayParametros["intIdPunto"]);
            if(is_object($objInfoPunto))
            {
                $arrayRespuesta['status'] = "OK";
                $strCordenadaHistorial    = "(COORDENADA) Anterior: ".$objInfoPunto->getLatitud().",".$objInfoPunto->getLongitud()."  -  Actual: ".
                                            $arrayParametros["strLatitud"].','.$arrayParametros["strLongitud"];


                $objCaracteristica        = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => "ID_METROS_MAXIMO"));

                $arrayPuntoCaracteristica = $this->emcom->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                                        ->findOneBy(array(
                                                                            'puntoId'           =>  $objInfoPunto->getId(),
                                                                            'caracteristicaId'  =>  $objCaracteristica->getId()
                                                                        ));

                if(count($arrayPuntoCaracteristica) < 1 || $boolNoValidarRegistro)
                {
                    $objInfoPunto->setLatitud($arrayParametros["strLatitud"]);
                    $objInfoPunto->setLongitud($arrayParametros["strLongitud"]);
                    $objInfoPunto->setUsrUltMod($arrayParametros["strUsrCreacion"]);
                    $objInfoPunto->setFeUltMod(new \DateTime('now'));
                    if(isset($arrayParametros["strDireccion"]) && $arrayParametros["strDireccion"] != null 
                       && !empty($arrayParametros["strDireccion"]))
                    {
                        $objInfoPunto->setDireccion($arrayParametros["strDireccion"]);    
                    }
                    $this->emcom->persist($objInfoPunto);
 
                    $objInfoPuntoHistorial = new InfoPuntoHistorial();
                    $objInfoPuntoHistorial->setPuntoId($objInfoPunto);
                    $objInfoPuntoHistorial->setFeCreacion(new \DateTime('now'));
                    $objInfoPuntoHistorial->setUsrCreacion($arrayParametros["strUsrCreacion"]);
                    $objInfoPuntoHistorial->setIpCreacion($arrayParametros["strIpCreacion"]);
                    $objInfoPuntoHistorial->setValor($strCordenadaHistorial);
                    $objInfoPuntoHistorial->setAccion('actualizarCoordenadaPunto');
                    $this->emcom->persist($objInfoPuntoHistorial);
                    $arrayRespuesta['mensaje'] = "Se actualizan las coordenadas geográficas del punto exitosamente";
                }
                else
                {
                    $objTarea = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')
                                                ->findOneBy(array('nombreTarea'    => "ACTUALIZACIÓN DE COORDENADAS"));

                    $arrayParametrosCoordenadas = array(
                                                        'objTarea'      => $objTarea,
                                                        'latitud'       => $arrayParametros["strLatitud"],
                                                        'longitud'      => $arrayParametros["strLongitud"],
                                                        'observaciones' => "Actualización de coordenadas desde el móvil",
                                                        'empresaCod'    => $arrayParametros["strCodEmpresa"],
                                                        'ipCreacion'    => $arrayParametros["strIpCreacion"],
                                                        'usrCreacion'   => $arrayParametros["strUsrCreacion"],
                                                        'intPuntoId'    => $arrayParametros["intIdPunto"]);
                    $this->serviceSoporte->crearTareaActualizaCoordenada($arrayParametrosCoordenadas);
                    $arrayRespuesta['mensaje'] = "Las coordenadas son diferentes a las registradas. Se genera tarea a GIS/Jefe Técnico para su verificación";
                }

                $objAdmiCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                                 ->findOneBy(array('descripcionCaracteristica' => "ID_METROS_MAXIMO",
                                                                                   'estado'                    => 'Activo'));
                if(is_object($objAdmiCaracteristica))
                {
                    $objInfoPuntoCaracteristica = new InfoPuntoCaracteristica();
                    $objInfoPuntoCaracteristica->setValor($arrayParametros["strLatitud"].','.$arrayParametros["strLongitud"]);
                    $objInfoPuntoCaracteristica->setCaracteristicaId($objAdmiCaracteristica);
                    $objInfoPuntoCaracteristica->setPuntoId($objInfoPunto);
                    $objInfoPuntoCaracteristica->setEstado('Activo');
                    $objInfoPuntoCaracteristica->setFeCreacion(new \DateTime('now'));
                    $objInfoPuntoCaracteristica->setUsrCreacion($arrayParametros["strUsrCreacion"]);
                    $objInfoPuntoCaracteristica->setIpCreacion($arrayParametros["strIpCreacion"]);
                    $this->emcom->persist($objInfoPuntoCaracteristica);
                }
                $this->emcom->flush();
                $this->emcom->getConnection()->commit();
            }
        }
        catch(\Exception $e)
        {
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollBack();
            }
            $this->emcom->getConnection()->close();
            throw $e;
        }
        return $arrayRespuesta;
    }

    /**
     * Función para obtener las coordenadas sugeridas desde el movil tecnico
     *
     * @author: Walther Joao Gaibor<wgaibor@telconet.ec>
     * @version: 1.0 26/02/2018
     * @param type $arrayParametros[
     *                              "intIdPunto":           int:    id del punto,
     *                              "intIdCaracteristica    int:    id caracteristica,
     *                              "strEstado"             string: Estado de la coordenada.
     * @return type
     */
    public function obtenerCoordenadaSugerida($arrayParametros)
    {
        $objRespuesta              = array();
        $objCaracteristica         = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                 ->findOneByDescripcionCaracteristica("ID_METROS_MAXIMO");

        $arrayParametrosCoordenada = array(
                                            'intIdPunto'            => $arrayParametros['intIdPunto'],
                                            'intIdCaracteristica'   => $objCaracteristica->getId(),
                                            'strEstado'             => "Activo"
                                          );
        $objRespuesta              = $this->emcom->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                                 ->obtenerCoordenadaSugerida($arrayParametrosCoordenada);
        return $objRespuesta;
    }

    /**
     * Función para actualizar la coordenada sugerida desde el movil tecnico
     *
     * @author: Walther Joao Gaibor<wgaibor@telconet.ec>
     * @version: 1.0 26/02/2018
     *
     * @author: Germán Valenzuela<gvalenzuela@telconet.ec>
     * @version: 1.1 26/08/2019 - Se agrega el parámetro strVerificarTarea encargado de verificar y finalizar la tarea
     *                            en caso que el valor sea diferente de NO.
     *
     * @param type Array $arrayParametros[
     *                                    "intIdPunto":           int:    id del punto,
     *                                    "intIdCaracteristica    int:    id caracteristica,
     *                                    "strEstado"             string: Estado de la coordenada.,
     *                                    "intIdDetalle"          int:    id de la tarea.
     *                                    "strVerificarTarea"     string: Valor Si o No para la verificación y finalización de la tarea.
     *                                   ]
     * @return type
     */
    public function actualizarCoordenadaSugerida($arrayParametros)
    {
        $arrayRespuesta = array();
        $this->emcom->getConnection()->beginTransaction();
        try
        {
            if (!isset($arrayParametros['strVerificarTarea'])
                    || strtoupper($arrayParametros['strVerificarTarea']) !== 'NO')
            {
                //Finalizo la tarea de actualizacion de coordenada
                $objUsuario = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                          ->getPersonaDepartamentoPorUserEmpresa($arrayParametros["strUsrCreacion"], 10);

                $arrayParametrosPunto = array(
                                                'intPuntoId'        => $arrayParametros["intIdPunto"],
                                                'strNombreTarea'    => "ACTUALIZACIÓN DE COORDENADAS"
                                             );

                $arrayDetallePunto = $this->emcom->getRepository('schemaBundle:InfoComunicacion')
                                                 ->getDetalleIdCoordenadaPunto($arrayParametrosPunto);

                $objDetalle        = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                                                     ->find($arrayDetallePunto[0]['detalleId']);

                $arrayParametrosFecha = array(
                                                'fechaInicio'   => $objDetalle->getFeCreacion()->format('d-m-Y'),
                                                'horaInicio'    => $objDetalle->getFeCreacion()->format('H:i')
                                             );
                $arrayTiempoServer    = $this->serviceSoporte->obtenerHoraTiempoTranscurrido($arrayParametrosFecha);

                //Finalizar la tarea automaticamente.
                $arrayParametrosCoordenadas = array(
                                                    'idEmpresa'             => 10,
                                                    'prefijoEmpresa'        => "TN",
                                                    'idDetalle'             => $arrayDetallePunto[0]['detalleId'],
                                                    'tarea'                 => $objDetalle->getTareaId()->getId(),
                                                    'tiempoTotal'           => $arrayTiempoServer['tiempoTotal'],
                                                    'fechaCierre'           => $arrayTiempoServer['fechaFin'],
                                                    'horaCierre'            => $arrayTiempoServer['horaFin'],
                                                    'fechaEjecucion'        => $arrayTiempoServer['fechaInicio'],
                                                    'horaEjecucion'         => $arrayTiempoServer['horaInicio'],
                                                    'esSolucion'            => true,
                                                    'fechaApertura'         => "",
                                                    'horaApertura'          => "",
                                                    'jsonMateriales'        => "",
                                                    'idAsignado'            => $objUsuario['ID_PERSONA'],
                                                    'observacion'           => "Se finaliza automáticamente la tarea de actualización de coordenadas",
                                                    'empleado'              => $objUsuario['NOMBRES']." ".$objUsuario['APELLIDOS'],
                                                    'usrCreacion'           => $arrayParametros["strUsrCreacion"],
                                                    'ipCreacion'            => $arrayParametros["strIpCreacion"],
                                                    'strEnviaDepartamento'  => "N");
                $arrayRespuestaTarea = $this->serviceSoporte->finalizarTarea($arrayParametrosCoordenadas);
            }

            if($arrayRespuestaTarea['status'] == "OK" || strtoupper($arrayParametros['strVerificarTarea']) === 'NO')
            {
                $objInfoPunto = $this->emcom->getRepository('schemaBundle:InfoPunto')
                                            ->findOneById($arrayParametros["intIdPunto"]);
                if(is_object($objInfoPunto))
                {
                    $objInfoPunto->setLatitud($arrayParametros["strLatitud"]);
                    $objInfoPunto->setLongitud($arrayParametros["strLongitud"]);
                    $objInfoPunto->setUsrUltMod($arrayParametros["strUsrCreacion"]);
                    $objInfoPunto->setFeUltMod(new \DateTime('now'));
                    $this->emcom->persist($objInfoPunto);

                    $objInfoPuntoHistorial = new InfoPuntoHistorial();
                    $objInfoPuntoHistorial->setPuntoId($objInfoPunto);
                    $objInfoPuntoHistorial->setFeCreacion(new \DateTime('now'));
                    $objInfoPuntoHistorial->setUsrCreacion($arrayParametros["strUsrCreacion"]);
                    $objInfoPuntoHistorial->setIpCreacion($arrayParametros["strIpCreacion"]);
                    $objInfoPuntoHistorial->setValor($strCordenadaHistorial);
                    $objInfoPuntoHistorial->setAccion('actualizarCoordenadaPunto');
                    $this->emcom->persist($objInfoPuntoHistorial);

                    $this->emcom->flush();
                }


                $objCaracteristica        = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica("ID_METROS_MAXIMO");
                //Recorro la info_punto_caracteristica y cambio todas las sugerencias al estado Procesado
                $arrayPuntoCaracteristica = $this->emcom->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                                        ->findBy(array('puntoId'            => $objInfoPunto->getId(),
                                                                       'caracteristicaId'   => $objCaracteristica->getId()));
                foreach($arrayPuntoCaracteristica as $objInfoPuntoCaracteristica)
                {
                    $objInfoPuntoCaracteristica->setEstado("Procesado");
                    $this->emcom->persist($objInfoPuntoCaracteristica);
                    $this->emcom->flush();
                }
                $this->emcom->getConnection()->commit();
                $arrayRespuesta['strMensaje'] = "Se actualizan las coordenadas geograficas del punto exitosamente";
                $arrayRespuesta['strStatus'] = "OK";
            }
        }
        catch(\Exception $e)
        {
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollBack();
            }
            $this->emcom->getConnection()->close();
            throw $e;
        }
        return $arrayRespuesta;
    }
    
    public function obtenerDatosPuntosClienteFilter($arrayParametros)
    {
        /* @var $srepoInfoPunto \telconet\schemaBundle\Repository\InfoPuntoRepository */
        /* @var $entity \telconet\schemaBundle\Entity\InfoPunto */
        /* @var $entityPuntoDatoAdicional \telconet\schemaBundle\Entity\InfoPuntoDatoAdicional */
        /* @var $cliente \telconet\schemaBundle\Entity\InfoPersona */
        /* @var $entitySector \telconet\schemaBundle\Entity\AdmiSector */
        /* @var $entityParroquia \telconet\schemaBundle\Entity\AdmiParroquia */
        $serviceInfoPunto = $this->emcom->getRepository('schemaBundle:InfoPunto');
        $arrayPuntos = $serviceInfoPunto->findPtosPorEmpresaPorClientePorRolFilter($arrayParametros);
        $arrayRespuesta = array();
        foreach ($arrayPuntos['registros'] as $arrayPunto)
        {
            $entityPunto = $this->emcom->getRepository('schemaBundle:InfoPunto')->find($arrayPunto['id']);
            $entityPuntoDatoAdicional = $this->emcom->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($arrayPunto['id']);
            if ($entityPunto->getEstado() == "Activo")
            {
                
                $arrayRespuesta[] = array(
                                          'idPto'             => $entityPunto->getId(),
                                          'personaId'         => 0,
                                          'rol'               => '',
                                          'login'             => $entityPunto->getLogin(),
                                          'ptoCoberturaId'    => $entityPunto->getPuntoCoberturaId()->getId(),
                                          'cantonId'          => $entityPunto->getSectorId()->getParroquiaId()->getCantonId()->getId(),
                                          'parroquiaId'       => $entityPunto->getSectorId()->getParroquiaId()->getId(),
                                          'sectorId'          => $entityPunto->getSectorId()->getId(),
                                          'esedificio'        => $entityPuntoDatoAdicional->getEsEdificio(),
                                          'nombreEdificio'    => $entityPuntoDatoAdicional->getNombreEdificio(),
                                          'dependedeedificio' => $entityPuntoDatoAdicional->getDependeDeEdificio(),
                                          'puntoedificioid'   => $entityPuntoDatoAdicional->getPuntoEdificioId(),
                                          'tipoNegocioId'     => $entityPunto->getTipoNegocioId()->getId(),
                                          'tipoUbicacionId'   => $entityPunto->getTipoUbicacionId()->getId(),
                                          'nombrepunto'       => $entityPunto->getNombrePunto(),
                                          'direccion'         => $entityPunto->getDireccion(),
                                          'descripcionpunto'  => $entityPunto->getDescripcionPunto(), // referencia
                                          'observacion'       => $entityPunto->getObservacion(),
                                          'file'              => $entityPunto->getFile(), // croquis
                                          'fileDigital'       => $entityPunto->getFileDigital(),
                                          'latitudFloat'      => $entityPunto->getLatitud(),
                                          'longitudFloat'     => $entityPunto->getLongitud(),
                                          'loginVendedor'     => $entityPunto->getUsrVendedor(),
                                          'estado'            => $entityPunto->getEstado()
                                    );
                        }
        }
        return $arrayRespuesta;
    }
    

    public function obtenerDatosPuntosClienteNew($arrayParametros)
    {
        /* @var $serviceInfoPunto \telconet\schemaBundle\Repository\InfoPuntoRepository */
        /* @var $entity \telconet\schemaBundle\Entity\InfoPunto */
        /* @var $entityPuntoDatoAdicional \telconet\schemaBundle\Entity\InfoPuntoDatoAdicional */
        /* @var $cliente \telconet\schemaBundle\Entity\InfoPersona */
        /* @var $entitySector \telconet\schemaBundle\Entity\AdmiSector */
        /* @var $entityParroquia \telconet\schemaBundle\Entity\AdmiParroquia */
        $arrayParametroPunto = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAMETROS_TM_COMERCIAL', 
                                                        'COMERCIAL', 
                                                        '',
                                                        'CANTIDAD DE PUNTOS A MOSTRAR',
                                                        '', 
                                                        '',
                                                        '',
                                                        '', 
                                                        '', 
                                                        '18');
        $intCantidadPunto = 20;
        if (($arrayParametroPunto) && ($arrayParametroPunto['valor1']))
        {
            $intCantidadPunto = $arrayParametroPunto['valor1'];
        }         
        $strCodEmpresa      = $arrayParametros['strCodEmpresa'];
        $intIdPersona       = $arrayParametros['intIdPersona'];
        $intRol             = $arrayParametros['strRol'];
        $boolFormasContacto = $arrayParametros['boolFormasContacto'];
        $intIdFormaContacto = $arrayParametros['intIdFormaContacto'];
        $boolServicios      = $arrayParametros['boolServicios'];
        $serviceInfoPunto     = $this->emcom->getRepository('schemaBundle:InfoPunto');
        $arrayPuntos            = $serviceInfoPunto->findPtosPorEmpresaPorClientePorRol($strCodEmpresa, $intIdPersona, null, $intRol, null, null, null, null);
        $arrayRespuesta           = array();
        $intCont           = 0;
        $intContCancelados = 0;
        $intTotal          = 0;
        foreach ($arrayPuntos['registros'] as $arrayPunto)
        {
            $intTotal++;
            $entityPunto = $this->emcom->getRepository('schemaBundle:InfoPunto')->find($arrayPunto['id']);
            $entityPuntoDatoAdicional = $this->emcom->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($arrayPunto['id']);
            if ($boolFormasContacto)
            {
                $arrayFormasContacto = $this->obtenerFormasContactoPorPunto($entityPunto->getId(), null, null, $intIdFormaContacto);
            }
            if ($entityPunto->getEstado() == 'Cancelado')
            {
               $intContCancelados++;   
            }
            $arrayEstados = array('Activo', 'In-Corte');
            if ( in_array($entityPunto->getEstado(), $arrayEstados) )
            {
                $intCont++;              
                
                $arrayRespuesta[] = array(
                                'idPto'             => $entityPunto->getId(),
                                'personaId'         => $intIdPersona,
                                'rol'               => $intRol,
                                'login'             => $entityPunto->getLogin(),
                                'ptoCoberturaId'    => $entityPunto->getPuntoCoberturaId()->getId(),
                                'cantonId'          => $entityPunto->getSectorId()->getParroquiaId()->getCantonId()->getId(),
                                'parroquiaId'       => $entityPunto->getSectorId()->getParroquiaId()->getId(),
                                'sectorId'          => $entityPunto->getSectorId()->getId(),
                                'esedificio'        => $entityPuntoDatoAdicional->getEsEdificio(),
                                'nombreEdificio'    => $entityPuntoDatoAdicional->getNombreEdificio(),
                                'dependedeedificio' => $entityPuntoDatoAdicional->getDependeDeEdificio(),
                                'puntoedificioid'   => $entityPuntoDatoAdicional->getPuntoEdificioId(),
                                'tipoNegocioId'     => $entityPunto->getTipoNegocioId()->getId(),
                                'tipoUbicacionId'   => $entityPunto->getTipoUbicacionId()->getId(),
                                'nombrepunto'       => $entityPunto->getNombrePunto(),
                                'direccion'         => $entityPunto->getDireccion(),
                                'descripcionpunto'  => $entityPunto->getDescripcionPunto(), // referencia
                                'observacion'       => $entityPunto->getObservacion(),
                                'file'              => $entityPunto->getFile(), // croquis
                                'fileDigital'       => $entityPunto->getFileDigital(),
                                'latitudFloat'      => $entityPunto->getLatitud(),
                                'longitudFloat'     => $entityPunto->getLongitud(),
                                'loginVendedor'     => $entityPunto->getUsrVendedor(),
                                'estado'            => $entityPunto->getEstado(),
                                'cancelado'         => "",
                                'esPadre'           => $entityPuntoDatoAdicional->getEsPadreFacturacion(),
                            ) + ($boolFormasContacto ?
                                array('formasContacto' => $arrayFormasContacto['registros']) : array())
                            + ($boolServicios ?
                                array('servicios'      => $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                          ->findDatosResumenPorPunto($entityPunto->getId())) : array());
                if ($intCont >= $intCantidadPunto)
                {
                    break;
                }

            }
        }
        if ($intTotal == 0)
        {
            return $arrayRespuesta;
        }
        if ($intContCancelados == $intTotal)
        {
            $arrayRespuesta[] = array('cancelado' => "S");
            return $arrayRespuesta; 
        }
        return $arrayRespuesta;
    }
    
    /**
     * Método para obtener información del ademdum
     *
     * @author Initial - 1.0
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 - 14/03/2019 - Se valida el campo FinContrato no sea nulo para poder parsear la fecha.
     * 
     * @author Edgar Pin Villavicencion <epin@telconet.ec>
     * @version 1.2 - 14/02/2019 - Se agrega en el filtro de estados que tambien traiga los que esten en estado Factible
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.3 - 06/08/2019 - Se agrega en el filtro de estados que también traiga los que esten en estado Pendiente.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.4 - 10/11/2019 - Se agrega validación para objectos nulos al obtener datos de clientes TN
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.5 - 13/04/2020 - Se agrega servicios adicionales parametrizados del adendum de servicio al
     *                             consultar puntos del cliente.
     *                             Se reemplaza el nombre original del servicio adicional por el parametrizado
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.6 - 04-02-2021 - Se agrega validación que el objeto que contiene los documentos no sea nulo
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.7 - 05/09/2020 - Se reemplaza método para búsqueda de puntos mediante paginación
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.8 - 18-06-2021 - Se corrige variable recibida en parametro intIdFormaContacto
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.9 - 12-07-2021 - Se mostrará la descripción del producto.
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.9 - 03-12-2021 - Se debe validar que la forma de pago del contrato tenga estado activo.
     * 
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.3 01-08-2022  - Se modifica agrega fecha de autorizacion del contrato
     * 
     * @author Miguel Guzman <mguzman@telconet.ec>
     * @version 1.3 03-03-2023  - Consideracion de empresa
     * 
     * @param array $arrayParametros
     * @return string
     */
    public function obtenerDatosPuntosClienteAdendum($arrayParametros)
    {
        /* @var $serviceInfoPunto \telconet\schemaBundle\Repository\InfoPuntoRepository */
        /* @var $entity \telconet\schemaBundle\Entity\InfoPunto */
        /* @var $entityPuntoDatoAdicional \telconet\schemaBundle\Entity\InfoPuntoDatoAdicional */
        /* @var $cliente \telconet\schemaBundle\Entity\InfoPersona */
        /* @var $entitySector \telconet\schemaBundle\Entity\AdmiSector */
        /* @var $entityParroquia \telconet\schemaBundle\Entity\AdmiParroquia */
        $arrayParametroPunto = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAMETROS_TM_COMERCIAL', 
                                                        'COMERCIAL', 
                                                        '',
                                                        'CANTIDAD DE PUNTOS A MOSTRAR',
                                                        '', 
                                                        '',
                                                        '',
                                                        '', 
                                                        '', 
                                                        $arrayParametros['strCodEmpresa']);
        $intCantidadPunto = 20;
        if (($arrayParametroPunto) && ($arrayParametroPunto['valor1']))
        {
            $intCantidadPunto = $arrayParametroPunto['valor1'];
        }                
        $strCodEmpresa      = $arrayParametros['strCodEmpresa'];
        $intIdPersona       = $arrayParametros['intIdPersona'];
        $intRol             = $arrayParametros['strRol'];
        $boolFormasContacto = $arrayParametros['boolFormasContacto'];
        $intIdFormaContacto = $arrayParametros['intIdFormaContacto'];
        $boolServicios      = $arrayParametros['boolServicios'];
        $arrayEstados       = $arrayParametros['arrayEstadosPuntosTotal'];
        $serviceInfoPunto   = $this->emcom->getRepository('schemaBundle:InfoPunto');

        $arrayRespuesta     = array();
        $intCont            = 0;
        $intContCancelados  = 0;
        $intTotal           = 0;
        try
        {
            $arrayPuntos   = $this->emcom->getRepository('schemaBundle:InfoPunto')->findPuntosPorClientePaginado($arrayParametros);
            $arrayPuntosList = $arrayPuntos['registros'];
            $arrayPuntosTotal = $arrayPuntos['total'];

            foreach ($arrayPuntosList as $arrayPunto)
            {
                $strTipoAdendum   = null;
                $intTotal++;
                $entityPunto = $this->emcom->getRepository('schemaBundle:InfoPunto')->find($arrayPunto['id']);
                $entityPuntoDatoAdicional = $this->emcom->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($arrayPunto['id']);
                //aqui traigo la informacion de adendum, primero traigo los registro de adendum que pertenezcan al punto, 
                //si no hay registros se entiende
                //que el punto es web, si tiene registro se valida si es contrato o adendum de punto o servicio
                
                if ($boolFormasContacto)
                {
                    $arrayFormasContacto = $this->obtenerFormasContactoPorPunto($entityPunto->getId(), null, null, $intIdFormaContacto);
                }
                if ($entityPunto->getEstado() == 'Cancelado')
                {
                   $intContCancelados++;
                }

                if ( in_array($entityPunto->getEstado(), $arrayEstados) )
                {
                    $intCont++;
                    
                        //se agrega recuperacion de contrato en caso de que exista
                        //obtiene rol activos o pendientes de la persona con rol Pre-cliente
                    $arrayEstadoContrato   = array('Pendiente','PorAutorizar');
                    $arrayEstadoRol        = array('Activo','Pendiente');
                    if ($intRol == "Cliente")
                    {
                        $arrayEstadoContrato   = array('Activo');
                        $arrayEstadoRol        = array('Activo');
                    }
                    $objRol = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                   ->findPersonaEmpresaRolByParams($intIdPersona, $strCodEmpresa,
                                                                   $arrayEstadoRol, $intRol);
                    if ($objRol) 
                    {
                        $objContrato = $this->emcom->getRepository('schemaBundle:InfoContrato')
                                                          ->findOneBy(array("personaEmpresaRolId" => $objRol->getId(),
                                                                            "estado"              => $arrayEstadoContrato));
                        if (is_object($objContrato)) 
                        {
                            $arrayContrato = array();
                            $arrayContrato['formaPagoId']          = $objContrato->getFormaPagoId()->getId();
                            $arrayContrato['numeroContratoEmpPub'] = $objContrato->getNumeroContratoEmpPub();
                            $arrayContrato['valorContrato']        = $objContrato->getValorContrato();
                            $arrayContrato['valorAnticipo']        = $objContrato->getValorAnticipo();
                            $arrayContrato['valorGarantia']        = $objContrato->getValorGarantia();
                            $arrayContrato['tipoContratoId']       = $objContrato->getTipoContratoId()->getId();
                            $arrayContrato['personaEmpresaRolId']  = $objRol->getId();
                            $arrayContrato['feFinContratoPost']    = "";
                            $strFeFinContrato                      = $objContrato->getFeFinContrato();
                            if ($strCodEmpresa && !empty($strFeFinContrato))
                            {
                                $arrayContrato['feFinContratoPost'] = $strFeFinContrato->format('d/m/Y');
                            }
                            
                            $arrayContrato['feCreacion']           = "";
                            $strFeCreacion                         = $objContrato->getFeCreacion();
                            if ($strCodEmpresa && !empty($strFeCreacion))
                            {
                                $arrayContrato['feCreacion'] = $strFeCreacion->format('d/m/Y');
                            }

                            $arrayContrato['feAprobacion']           = "";
                            $strFeAprobacion = $objContrato->getFeAprobacion();
                            if ($strCodEmpresa && !empty($strFeAprobacion))
                            {
                                $arrayContrato['feAprobacion'] = $strFeAprobacion->format('d/m/Y');
                            }
    
                            $arrayContrato['origen']               = $objContrato->getOrigen();
                            $arrayContrato['idContrato']  	       = $objContrato->getId();
                            $arrayContrato['estado']               = $objContrato->getEstado();
                            $arrayContrato['numeroContrato']       = $objContrato->getNumeroContrato();
                            $serviceContratoFormaPago              = $this->emcom
                                                                          ->getRepository('schemaBundle:InfoContratoFormaPago');
    
                            /* @var $infoContratoFormaPago \telconet\schemaBundle\Repository\InfoContratoFormaPago */
                            $objContratoFormaPago                  = $serviceContratoFormaPago
                                                                     ->findOneBy(array('contratoId' => $objContrato->getId(),
                                                                                       'estado'     => 'Activo'));
                            $arrayContrato['tipoCuentaId']       = null;
                            $arrayContrato['bancoTipoCuentaId']  = null;
                            $arrayContrato['numeroCtaTarjeta']   = null;
                            $arrayContrato['titularCuenta']      = null;
                            $arrayContrato['mesVencimiento']     = null;
                            $arrayContrato['anioVencimiento']    = null;
                            $arrayContrato['codigoVerificacion'] = null;
    
                            if (is_object($objContratoFormaPago)) 
                            {
                                $arrayContrato['tipoCuentaId']       = $objContratoFormaPago->getTipoCuentaId()->getId();
                                $arrayContrato['bancoTipoCuentaId']  = $objContratoFormaPago->getBancoTipoCuentaId()->getId();
                                $arrayContrato['numeroCtaTarjeta']   = $objContratoFormaPago->getNumeroCtaTarjeta();
                                $arrayContrato['titularCuenta']      = $objContratoFormaPago->getTitularCuenta();
                                $arrayContrato['mesVencimiento']     = $objContratoFormaPago->getMesVencimiento();
                                $arrayContrato['anioVencimiento']    = $objContratoFormaPago->getAnioVencimiento();
                                $arrayContrato['codigoVerificacion'] = $objContratoFormaPago->getCodigoVerificacion();
                            }
                            $entityDocumentoRelacion = $this->emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion');
                            /* @var $entityDocumentoRelacion \telconet\schemaBundle\Repository\InfoDocumentoRelacion */
                            $arrayContrato['numeroFiles'] = 0;
                            $arrayDocumentos = $entityDocumentoRelacion->findBy(array('contratoId' => $objContrato->getId()));
                            if ($arrayDocumentos) 
                            {
                                $arrayContrato['numeroFiles'] = count($arrayDocumentos);
                                
                                $arrayFile = array();
                                foreach ($arrayDocumentos as $arrayDocumento)
                                {   
                                    $objInfoDocumento = $this->emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                                                             ->findOneBy(array('id' => $arrayDocumento->getDocumentoId()));
                                    if ( $objInfoDocumento &&  $objInfoDocumento->getTipoDocumentoId() &&
                                         $objInfoDocumento->getTipoDocumentoId()->getId() == 10)
                                    {
                                        $boolExiste = false;
                                        foreach ($arrayFile as $arrayBusca)
                                        {
                                            if ($arrayBusca["tipoDocumentoGeneralId"] == $objInfoDocumento->getTipoDocumentoGeneralId())
                                            {
                                                $boolExiste = true;
                                            }
                                        }
                                        if (!$boolExiste)
                                        {
                                            $objTipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                                     ->findOneBy(array('id' => $objInfoDocumento->getTipoDocumentoGeneralId()));
                                            $arrayFileDoc = [];
    
                                            $arrayFileDoc["digitalFileUri"]         = $objInfoDocumento->getUbicacionFisicaDocumento(); 
                                            $arrayFileDoc["digitalFileName"]        = $objTipoDocumento->getDescripcionTipoDocumento();
                                            $arrayFileDoc["tipoDocumentoGeneralId"] = $objInfoDocumento->getTipoDocumentoGeneralId();
                                            $arrayFile[] = $arrayFileDoc;
                                        }
                                        
                                    }
                                    
                                }    
                                $arrayContrato['files'] = $arrayFile;
                                
                                
                            } 
                            $objHayAdendums = $this->emcom->getRepository('schemaBundle:InfoAdendum')->findby(array(
                                                                                                           "puntoId" => $entityPunto->getId()
                                                                                 ));
                            $arrayAdendums = array();
                            $strTipoAdendum = null;
                            if ($objHayAdendums)
                            {

                                $arrayTipos = array("C", "AP", "AS");
                                $arrayParametrosEstados = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                ->getOne('PARAMETROS_TM_COMERCIAL', 
                                        'COMERCIAL', 
                                        '',
                                        'ESTADOS ADENDUM VALIDOS MOVIL',
                                        '', 
                                        '',
                                        '',
                                        '', 
                                        '', 
                                        $arrayParametros['strCodEmpresa']);
 
                                $strValor1=$arrayParametrosEstados['valor1'];
                                $arrayEstadosAdendum = explode(",",$strValor1);


                                foreach ($arrayTipos as $arrayTipo)
                                {
                                    $objAdendums = $this->emcom->getRepository('schemaBundle:InfoAdendum')->findby(array(
                                                                                                   "puntoId" => $entityPunto->getId(),
                                                                                                   "tipo"    => $arrayTipo,
                                                                                                   "estado" => $arrayEstadosAdendum
                                                                                                                   ));
                                    if ($objAdendums)
                                    {
                                        $strNumeroAdendum = "";
                                        $strTipo          = "";
                                        
                                        $arrayAdendum     = null;
                                        foreach ($objAdendums as $entityAdendum)
                                        {
                                            $strTipo          = $entityAdendum->getTipo();
                                            $strTipoAdendum   = $entityAdendum->getTipo() == "AS" ? $strTipoAdendum : $entityAdendum->getTipo();

                                            $strNumeroAdendum = $entityAdendum->getNumero();
                                            if ($strTipo !=="C")
                                            {
                                                $arrayFormaPago       = null;
                                                $arrayTipoCuenta      = null;
                                                $arrayBancoTipoCuenta = null;
                                                $arrayFormaPago       = array("k" => $entityAdendum->getFormaPagoId(),
                                                                                "v" => "");
                                                $arrayTipoCuenta      = array("k" => $entityAdendum->getTipoCuentaId(),
                                                                                "v" => "");
                                                $arrayBancoTipoCuenta = array("k" => $entityAdendum->getBancoTipoCuentaId(),
                                                                                "v" => "");

                                                $arrayAdendum = array("id"                 => $entityAdendum->getId(),
                                                                      "numeroAdendum"      => $entityAdendum->getNumero(),
                                                                      "tipo"               => $entityAdendum->getTipo(),
                                                                      "contratoId"         => $objContrato->getId(),
                                                                      "numeroContrato"     => $objContrato->getNumeroContrato(),
                                                                      "formaPagoId"        => $arrayFormaPago,
                                                                      'tipoCuentaId'       => $arrayTipoCuenta,
                                                                      'bancoTipoCuentaId'  => $arrayBancoTipoCuenta,
                                                                      'numeroCtaTarjeta'   => $entityAdendum->getNumeroCtaTarjeta(),
                                                                      'titularCuenta'      => $entityAdendum->getTitularCuenta(),
                                                                      'mesVencimiento'     => $entityAdendum->getMesVencimiento(),
                                                                      'anioVencimiento'    => $entityAdendum->getAnioVencimiento(),
                                                                      'codigoVerificacion' => $entityAdendum->getCodigoVerificacion(),
                                                                      'feCreacion'         => $entityAdendum->getFeCreacion()->format('d/m/Y'),
                                                                      'estado'             => $entityAdendum->getEstado()
                                                                                     );
                                                $entityDocumentoRelacion = $this->emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion');
                                                $arrayDocumentos = $entityDocumentoRelacion->findBy(array('contratoId'    => $objContrato->getId(),
                                                                                                        'numeroAdendum' => $strNumeroAdendum));

                                                if ($arrayDocumentos) 
                                                {
                                                    $arrayContrato['numeroFiles'] = count($arrayDocumentos);
            
                                                    $arrayFile = array();
                                                    foreach ($arrayDocumentos as $arrayDocumento)
                                                    {   
                                                        $objInfoDocumento = $this->emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                                                ->findOneBy(array('id' => $arrayDocumento->getDocumentoId()));
                                                        if ( $objInfoDocumento->getTipoDocumentoId()->getId() == 10)
                                                        {
                                                            $boolExiste = false;
                                                            foreach ($arrayFile as $arrayBusca)
                                                            {
                                                                if ($arrayBusca["tipoDocumentoGeneralId"] == $objInfoDocumento
                                                                                                             ->getTipoDocumentoGeneralId())
                                                                {
                                                                    $boolExiste = true;
                                                                }
                                                            }
                                                            if (!$boolExiste)
                                                            {
                                                                $objTipoDocumento = $this->emComunicacion
                                                                                        ->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                                                        ->findOneBy(array('id' => $objInfoDocumento
                                                                                                                ->getTipoDocumentoGeneralId()));
                                                                $arrayFileDoc = [];
            
                                                                $arrayFileDoc["digitalFileUri"]         = $objInfoDocumento
                                                                                                          ->getUbicacionFisicaDocumento();
                                                                $arrayFileDoc["digitalFileName"]        = $objTipoDocumento
                                                                                                          ->getDescripcionTipoDocumento();
                                                                $arrayFileDoc["tipoDocumentoGeneralId"] = $objInfoDocumento
                                                                                                          ->getTipoDocumentoGeneralId();
                                                                $arrayFile[] = $arrayFileDoc;
                                                            }
            
                                                        }
            
                                                    }
                                             
                                                }

                                                if ($arrayAdendum)
                                                {
                                                    $arrayAdendum['files'] = $arrayFile;
                                                }
                                                $boolExiste = false;
                                                if (count($arrayAdendums) > 0)
                                                {
                                                    foreach ($arrayAdendums as $arrayValida)
                                                    {
                                                        if ($arrayValida['tipo'] == $entityAdendum->getTipo() 
                                                            && $arrayValida['numeroAdendum'] == $entityAdendum->getNumero())
                                                        {
                                                            $boolExiste = true;
                                                        }
                                                    }
    
                                                }
                                                if (!$boolExiste)
                                                {
                                                    $arrayAdendums[] = $arrayAdendum;
                                                }
                                            }
                                        }
                                    } 
                                }
                            } 
                        } 
                    }
              
                    $arrayRespuesta[] = array(
                                    'idPto'             => $entityPunto->getId(),
                                    'personaId'         => $intIdPersona,
                                    'rol'               => $intRol,
                                    'login'             => $entityPunto->getLogin(),
                                    'ptoCoberturaId'    => $entityPunto->getPuntoCoberturaId()->getId(),
                                    'cantonId'          => $entityPunto->getSectorId()->getParroquiaId()->getCantonId()->getId(),
                                    'parroquiaId'       => $entityPunto->getSectorId()->getParroquiaId()->getId(),
                                    'sectorId'          => $entityPunto->getSectorId()->getId(),
                                    'tipoNegocioId'     => $entityPunto->getTipoNegocioId()->getId(),
                                    'tipoUbicacionId'   => $entityPunto->getTipoUbicacionId()->getId(),
                                    'nombrepunto'       => $entityPunto->getNombrePunto(),
                                    'direccion'         => $entityPunto->getDireccion(),
                                    'descripcionpunto'  => $entityPunto->getDescripcionPunto(), // referencia
                                    'observacion'       => $entityPunto->getObservacion(),
                                    'file'              => $entityPunto->getFile(), // croquis
                                    'fileDigital'       => $entityPunto->getFileDigital(),
                                    'latitudFloat'      => $entityPunto->getLatitud(),
                                    'longitudFloat'     => $entityPunto->getLongitud(),
                                    'loginVendedor'     => $entityPunto->getUsrVendedor(),
                                    'estado'            => $entityPunto->getEstado(),
                                    'cancelado'         => "",
                                    'esPadre'           => $entityPuntoDatoAdicional->getEsPadreFacturacion(),
                                    'contrato'          => ($strTipoAdendum == null || $strTipoAdendum == "AP") ? null : $arrayContrato,
                                    'adendums'          => $arrayAdendums,
                                    'origen'            => $entityPunto->getOrigenWeb() == "S" ? "WEB" : "MOVIL",
                                    'feCreacion'        => $entityPunto->getFeCreacion()->format("d/m/Y"),
                                    'strTipo'           => $strTipoAdendum,      
                                    'creacionPunto'      =>$this->getDataLinksContratoCliente(array(
                                        'puntoId'=>$entityPunto->getId(),
                                        'empresaCod'=>$strCodEmpresa ,
                                        'personaEmpresaRolId'=>$intIdPersona ,
                                        'usrCreacion'=> $arrayParametros['strUsrCreacion'] ,
                                    )),
                                ) + (is_object($entityPuntoDatoAdicional)
                                ? array(
                                    'esedificio'        => $entityPuntoDatoAdicional->getEsEdificio(),
                                    'nombreEdificio'    => $entityPuntoDatoAdicional->getNombreEdificio(),
                                    'dependedeedificio' => $entityPuntoDatoAdicional->getDependeDeEdificio(),
                                    'puntoedificioid'   => $entityPuntoDatoAdicional->getPuntoEdificioId(),
                                    'esPadre'           => $entityPuntoDatoAdicional->getEsPadreFacturacion())
                                : array(
                                    'esedificio'        => null,
                                    'nombreEdificio'    => null,
                                    'dependedeedificio' => null,
                                    'puntoedificioid'   => null,
                                    'esPadre'           => null)
                            )
                                + ($boolFormasContacto ?
                                    array('formasContacto' => $arrayFormasContacto['registros']) : array())
                                + ($boolServicios ?
                                    array('servicios'      => $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                              ->findDatosResumenPorPunto($entityPunto->getId())) : array());

                    $strTipoAdendum   = null;                                          
                    if ($intCont >= $intCantidadPunto)
                    {
                        break;
                    }
                }
            }
            
            $arrayProdParams = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                ->get("PRODUCTOS_TM_COMERCIAL", "COMERCIAL", "", "", "", "", "", "", "", $arrayParametros['strCodEmpresa']);

            if(!empty($arrayProdParams))
            {
                $arrayNuevoProdParams = array();
                
                foreach ($arrayProdParams as $intKey => $arrayProdParam)
                {
                    $arrayNuevoProdParams[intval($arrayProdParam['valor4'])] = $arrayProdParam;
                }
                
                foreach ($arrayPuntosList as $intKeyPunto => $arrayPunto)
                {
                    foreach ($arrayRespuesta[$intKeyPunto]['servicios'] as $intKeyServicio => $arrayServicio)
                    {
                        if(is_int($arrayServicio['productoId']))
                        {
                            $arrayRespuesta[$intKeyPunto]['servicios'][$intKeyServicio]['descripcionProducto'] =
                                $arrayRespuesta[$intKeyPunto]['servicios'][$intKeyServicio]['nombre'] =
                                $arrayServicio['descripcionProducto'];
                        }
                    }
                }
            }
        }
        catch (\Exception $objException)
        {
            $arrayParametrosLog['enterpriseCode']   = $arrayParametros['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $objException->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayParametros, 128);
            $arrayParametrosLog['creationUser']     = $arrayParametros['strUsrCreacion'];
            $this->serviceUtil->insertLog($arrayParametrosLog);
            throw $objException;
            
        }

        if ($intTotal == 0)
        {
            return array('puntos' => $arrayRespuesta, 'total' => $arrayPuntosTotal, 'cancelado' => "");
        }
        if ($intContCancelados == $intTotal)
        {
            $arrayRespuesta[] = array('cancelado' => "S");
            return $arrayRespuesta;
        }
        return array('puntos' => $arrayRespuesta, 'total' => $arrayPuntosTotal, 'cancelado' => "");
    }
/**
     * @author Jefferson Alexy Carrillo Anchundia <jacarrillo@telconet.ec> 
     * @version 1.0 07-07-2021
     * #GEO Consumo de ms catalogos , entrega ubicacion en base a las coordenadas
     * @param  array $arrayParametros [
     *                                  "usrCreacion"  :integer:  Usuario de creación, 
     *                                  "token"        :string:  Token cass,
     *                                  "idPersona"    :string: Id de persona,
     *                                  "latitud"      :string: coordenada de latitud del punto,
     *                                  "longitud"     :string: coordenada de latitud del punto,
     * @return JsonResponse $objRespuesta
     */
    public function verificarCatalogoMs($arrayParametros)  
    {
        $arrayResultado  = array();
        $strIpMod               = $arrayParametros['strIpMod'];
        $strUserMod             = $arrayParametros['usrCreacion']; 
        try 
        {
            $objOptions         = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER     => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayParametros['token']
                )
            );
     
            $strJsonData        = json_encode($arrayParametros);
            $strUrl             = $this->strUrlVerificarCatalogoMs;
            $arrayResponseJson  = $this->serviceRestClient->postJSON( $strUrl , $strJsonData, $objOptions);
            
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'], true);

            if (
                isset($strJsonRespuesta['code']) && $strJsonRespuesta['code'] == 0
                && isset($strJsonRespuesta['status'])
                && isset($strJsonRespuesta['message'])
            ) 
            {
                $arrayResponse = array(
                    'strStatus' => 'OK',
                    'strMensaje' => $strJsonRespuesta['message'],
                    'objData' => $strJsonRespuesta['data']
                );
                $arrayResultado = $arrayResponse;
            }
             else 
            {
                $arrayResultado['strStatus']       = "ERROR";  
                
                if (!empty($strJsonRespuesta['message']))
                {   
                    $arrayResultado['strMensaje']  = $strJsonRespuesta['message'];                   
                } 
                else 
                {
                    $arrayResultado['strMensaje']  = "No Existe Conectividad con el WS ms-core-gen-geografia.";
                }
            }
        } catch (\Exception $e) 
        {
            $arrayResultado['strMensaje'] = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas" . $e->getMessage();
  
            $this->serviceUtil->insertError(
                'Telcos+',
                'InfoPuntoService.verificarCatalogoMs',
                'Error InfoPuntoService.verificarCatalogoMs:' . $e->getMessage(),
                $strUserMod,
                $strIpMod
            );
        }
        return $arrayResultado;
    }
    
   /**
    * @author Alex Arreaga <atarreaga@telconet.ec> 
    * @version 1.0 18-11-2021
    * Consumo de ms validacionesPuntoAdicianl.
    * @param  array $arrayParametros ["strUsrCreacion" :string:  Usuario creación, 
    *                                 "token"          :string:  Token cas,
    *                                 "idEmpresa"      :integer: Id empresa,
    *                                 "idPersona"      :integer: Id de persona ]
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec> 
    * @version 1.1 05-12-2022 - Se modifica el nombre de la variable del usuario e ip de creación.
    * 
    * @return $arrayResultado
    */
    public function validacionesPuntoAdicionalMs($arrayParametros)  
    {
        $arrayResultado = array();
        $strUsrCreacion = $arrayParametros['usrCreacion'];
        $strIpCreacion  = $arrayParametros['clienteIp'];
        
        try 
        {
            $objOptions = array(CURLOPT_SSL_VERIFYPEER => false,
                                CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                'tokencas: ' . $arrayParametros['token']
                                                                )
                                );
            
            $strJsonData       = json_encode($arrayParametros);
            $strUrl            = $this->strUrlValidacionPuntoAdicionalMs;
            $arrayResponseJson = $this->serviceRestClient->postJSON( $strUrl , $strJsonData, $objOptions);
            $strJsonRespuesta  = json_decode($arrayResponseJson['result'], true);
            
            if (isset($strJsonRespuesta['status']) && isset($strJsonRespuesta['message'])) 
            {
                $arrayResponse  = array('strStatus'  => $strJsonRespuesta['status'],
                                        'strMensaje' => $strJsonRespuesta['message'],
                                        'objData'    => $strJsonRespuesta['data'] );
                $arrayResultado = $arrayResponse;
            } else 
            {
                $arrayResultado['strStatus']  = "ERROR";
                $arrayResultado['strMensaje'] = empty($strJsonRespuesta['message']) ? 
                                                "No existe conectividad con el WS ms-comp-cliente." : $strJsonRespuesta['message'];
            }
        } catch (\Exception $e) 
        {
            $strRespuesta   = "Error al ejecutar las validaciones de punto en cliente. Favor Notificar a Sistemas" . $e->getMessage();
            $arrayResultado = array('strMensaje' => $strRespuesta);
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoPuntoService.validacionesPuntoAdicionalMs',
                                            'Error InfoPuntoService.validacionesPuntoAdicionalMs: ' . $e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion );
        }
        return $arrayResultado;
    }
      
    /**
     * @author Carlos Caguana <ccaguana@telconet.ec> 
     * @version 1.0 05-04-2022
     * Metodo que me trae las caracterisiticas del punto con respecto a links bancarios
     * @param  array $arrayParametros [
     *                                  "puntoId"  :integer:  Usuario de creación, 
     * @return array $arrayResultado
     */
    public function getDataLinksContratoCliente($arrayParametros)
    {
        $arrayResultado  = array();
        $arrayTokenCas= $this->serviceTokenCas->generarTokenCas();                                
        $arrayParametros['token'] = $arrayTokenCas['strToken'];
       
       
        $strIpMod               = $arrayParametros['127.0.0.1'];
        $strUserMod             = $arrayParametros['strUsrCreacion'];  

        
        try 
        {
            $objOptions         = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER     => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayParametros['token']
                )
            );

           if(!isset($arrayParametros['personaEmpresaRolId']))
           {
                $arrayParPer    = array('strLogin'      => $strUserMod,
                                        'strCodEmpresa' => $arrayParametros['empresaCod']);

                $objPersonaRol  = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                              ->getDatosPerPorLoginEmpresa($arrayParPer);
                if(is_object($objPersonaRol))
                {
                    $arrayParametros['personaEmpresaRolId'] = $objPersonaRol->getId();
                }
                else
                {
                    throw new \Exception("No existe el rol de la persona");
                }
           }    
            $strJsonData        = json_encode($arrayParametros);
            $strUrl             = $this->strUrlgetCreacionPuntoLinks;
            $arrayResponseJson  = $this->serviceRestClient->postJSON( $strUrl , $strJsonData, $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'], true);

            if (
                isset($strJsonRespuesta['code']) && $strJsonRespuesta['code'] == 0
                && isset($strJsonRespuesta['status'])
                && isset($strJsonRespuesta['message'])
            ) 
            {
                $arrayResponse = array(
                    'status' => 'OK',
                    'message' => $strJsonRespuesta['message'],
                    'data' => $strJsonRespuesta['data']
                );
                $arrayResultado = $arrayResponse;
            }
             else 
            {
                $arrayResultado['status']       = "ERROR";  
                
                if (!empty($strJsonRespuesta['message']))
                {   
                    $arrayResultado['message']  = $strJsonRespuesta['message'];                   
                } 
                else 
                {
                    $arrayResultado['message']  = "No Existe Conectividad con el WS ms-comp-contrato-digital.";
                }
            }
        } catch (\Exception $e) 
        {
            $arrayResultado['message'] = "Error al ejecutar el proceso de links bancario. Favor Notificar a Sistemas" . $e->getMessage();
  
            $this->serviceUtil->insertError(
                'Telcos+',
                'InfoPuntoService.getDataLinksContratoCliente',
                'Error InfoPuntoService.getDataLinksContratoCliente:' . $e->getMessage(),
                $strUserMod,
                $strIpMod
            );
        }
      return $arrayResultado;
    }

    /**
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 11-05-2022
     * Metodo que me trae las caracterisiticas del punto con respecto a links bancarios
     * @param  array $arrayParametros [
     *                                  "puntoId"  :integer:  Usuario de creación,
     * @return array $arrayResultado
     */
    public function getObtieneInformacionCliente($arrayParametros)
    {
        $arrayResultado  = array();
        $arrayTokenCas= $this->serviceTokenCas->generarTokenCas();
        $arrayParametros['token'] = $arrayTokenCas['strToken'];

        try 
        {
            $objOptions         = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER     => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayParametros['token']
                )
            );

            $strJsonData        = json_encode($arrayParametros);
            $strUrl             = $this->strUrlMsCompContratoDigital.'obtenerInformacionCliente';
            $arrayResponseJson  = $this->serviceRestClient->postJSON( $strUrl , $strJsonData, $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'], true);

            if (
                isset($strJsonRespuesta['code']) && $strJsonRespuesta['code'] == 0
                && isset($strJsonRespuesta['status'])
                && isset($strJsonRespuesta['message'])
            )
            {
                $arrayResponse = array(
                    'status'    => 'OK',
                    'message'   => $strJsonRespuesta['message'],
                    'data'      => $strJsonRespuesta['data']
                );
                $arrayResultado = $arrayResponse;
            }
             else
            {
                $arrayResultado['status']       = "ERROR";
                
                if (!empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['message']  = $strJsonRespuesta['message'];
                }
                else
                {
                    $arrayResultado['message']  = "No Existe Conectividad con el WS ms-comp-contrato-digital.";
                }
            }
        } catch (\Exception $e)
        {
            $arrayResultado['message'] = "Error al ejecutar el proceso de links bancario. Favor Notificar a Sistemas" . $e->getMessage();

            $this->serviceUtil->insertError(
                'Telcos+',
                'InfoPuntoService.getObtieneInformacionCliente',
                'Error InfoPuntoService.getObtieneInformacionCliente:' . $e->getMessage(),
                $strUserMod,
                $strIpMod
            );
        }
      return $arrayResultado;
    }

    /**
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 11-05-2022
     * Metodo que me trae las caracterisiticas del punto con respecto a links bancarios
     * @param  array $arrayParametros [
     *                                  "puntoId"  :integer:  Usuario de creación, 
     * @return array $arrayResultado
     */
    public function getConsultaDataEncuesta($arrayParametros)
    {
        $arrayResultado  = array();
        $arrayTokenCas= $this->serviceTokenCas->generarTokenCas();
        $arrayParametros['token'] = $arrayTokenCas['strToken'];

        $strIpMod               = $arrayParametros['127.0.0.1'];
        $strUserMod             = $arrayParametros['strUsrCreacion'];  

        
        try
        {
            $objOptions         = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER     => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayParametros['token']
                )
            );

            $strJsonData        = json_encode($arrayParametros);
            $strUrl             = $this->strUrlMsCompContratoDigital.'getClausulas';
            $arrayResponseJson  = $this->serviceRestClient->postJSON( $strUrl , $strJsonData, $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'], true);

            if (
                isset($strJsonRespuesta['code']) && $strJsonRespuesta['code'] == 0
                && isset($strJsonRespuesta['status'])
                && isset($strJsonRespuesta['message'])
            )
            {
                $arrayResponse = array(
                    'status' => 'OK',
                    'message' => $strJsonRespuesta['message'],
                    'data' => $strJsonRespuesta['data']
                );
                $arrayResultado = $arrayResponse;
            }
             else
            {
                $arrayResultado['status']       = "ERROR";

                if (!empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['message']  = $strJsonRespuesta['message'];
                }
                else
                {
                    $arrayResultado['message']  = "No se pudo obtener las Cláusulas. Favor Notificar a Sistemas";
                }
            }
        } catch (\Exception $e)
        {
            $arrayResultado['message'] = "Error al ejecutar el proceso de links bancario. Favor Notificar a Sistemas" . $e->getMessage();

            $this->serviceUtil->insertError(
                'Telcos+',
                'InfoPuntoService.getDataLinksContratoCliente',
                'Error InfoPuntoService.getDataLinksContratoCliente:' . $e->getMessage(),
                $strUserMod,
                $strIpMod
            );
        }
      return $arrayResultado;
    }

    /**
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 11-05-2022
     * Metodo que permite desincriptar la tarjeta de credito.
     * @param  array $arrayParametros [
     *                                  "puntoId"  :integer:  Usuario de creación, 
     * @return array $arrayResultado
     */
    public function getDescifrarTarjeta($arrayParametros)
    {
        $arrayResultado  = array();
        $arrayTokenCas= $this->serviceTokenCas->generarTokenCas();
        $arrayParametros['token'] = $arrayTokenCas['strToken'];

        $strIpMod               = $arrayParametros['127.0.0.1'];
        $strUserMod             = $arrayParametros['strUsrCreacion'];  

        
        try
        {
            $objOptions         = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER     => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayParametros['token']
                )
            );

            $strJsonData        = json_encode($arrayParametros);
            $strUrl             = $this->strUrlMsCompContratoDigital.'decifrarValor';
            $arrayResponseJson  = $this->serviceRestClient->postJSON( $strUrl , $strJsonData, $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'], true);

            if (
                isset($strJsonRespuesta['code']) && $strJsonRespuesta['code'] == 0
                && isset($strJsonRespuesta['status'])
                && isset($strJsonRespuesta['message'])
            )
            {
                $arrayResponse = array(
                    'status' => 'OK',
                    'message' => $strJsonRespuesta['message'],
                    'data' => $strJsonRespuesta['data']
                );
                $arrayResultado = $arrayResponse;
            }
             else
            {
                $arrayResultado['status']       = "ERROR";

                if (!empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['message']  = $strJsonRespuesta['message'];
                }
                else
                {
                    $arrayResultado['message']  = "No Existe Conectividad con el WS ms-comp-contrato-digital.";
                }
            }
        } catch (\Exception $e)
        {
            $arrayResultado['message'] = "Error al ejecutar el proceso de links bancario. Favor Notificar a Sistemas" . $e->getMessage();

            $this->serviceUtil->insertError(
                'Telcos+',
                'InfoPuntoService.getDataLinksContratoCliente',
                'Error InfoPuntoService.getDataLinksContratoCliente:' . $e->getMessage(),
                $strUserMod,
                $strIpMod
            );
        }
      return $arrayResultado;
    }
}

