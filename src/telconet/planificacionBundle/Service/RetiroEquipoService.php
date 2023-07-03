<?php

namespace telconet\planificacionBundle\Service;

use Doctrine\ORM\EntityManager;
use telconet\schemaBundle\Entity\InfoOrdenTrabajo;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoDetalleHistorial;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Finder\Finder;
 
/**
* Documentación para la clase 'RetiroEquipoService'.
*
 * Clase utilizada para manejar operaciones relacionadas con los retiros de equipos
*
* @author Jesus Bozada <jbozada@telconet.ec>
* @version 1.0 27-12-2016
*/
class RetiroEquipoService
{
    private $container;
    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $procesarImagenesService;
    private $strPathTelcos;
    /**
     *
     * @var telconet\schemaBundle\Service\UtilService
     */
    private $serviceUtil;
    /**
     *
     * @var telconet\tecnicoBundle\Service\InfoServicioTecnicoService
     */
    private $serviceServicioTecnico;
    /**
     *
     * @var telconet\soporteBundle\Service\EnvioPlantillaService
     */
    private $serviceEnvioPlantilla;
    /**
     *
    * @var telconet\soporteBundle\Service\SoporteService
     */   
    private $serviceSoporte;
   
    public function setDependencies(Container $container)
    {
        $this->container               = $container;
        $this->emComercial             = $container->get('doctrine')->getManager('telconet');
        $this->emInfraestructura       = $container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSoporte               = $container->get('doctrine')->getManager('telconet_soporte');
        $this->emNaf                   = $container->get('doctrine')->getManager('telconet_naf');
        $this->emComunicacion          = $container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emGen                   = $container->get('doctrine')->getManager('telconet_general');
        $this->serviceUtil             = $container->get('schema.Util');
        $this->serviceServicioTecnico  = $container->get('tecnico.InfoServicioTecnico');
        $this->procesarImagenesService = $container->get('tecnico.ProcesarImagenes');
        $this->strPathTelcos           = $container->getParameter('path_telcos');
        $this->serviceEnvioPlantilla   = $container->get('soporte.EnvioPlantilla');
        $this->serviceSoporte          = $container->get('soporte.SoporteService');
    }
 
    /**
     * Metodo utilizado para recuperar información de retiros de equipos asignados a un empleado
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0         27-12-2016
     * @since 1.0
     *
     * @param Array $arrayParametros [ strCodEmpresa      Empresa a la que pertenece el cliente consultado
     *                                 strLoginAsignado   Login del empleado al cual estan asignados los retiros de equipos
     *                               ]
     *
     * @return Array $arrayRespuesta
     * [
     *  - strStatus   Estado de la transaccion ejecutada
     *  - strMensaje  Mensaje de la transaccion ejecutada
     *  - arrayData      [ - idDetalleSolicitud       Identificador de solicitud de retiro de equipos   
     *                     - idServicio               Identificador de servicio a procesar
     *                     - fechaActivacionServicio  Fecha de Activacion de servicio
     *                     - buscaCpeNaf              Bandera que indica si un servicio debe ser consultado en el naf
     *                                                para el procesamiento de la solicitud
     *                     - idPunto                  Identificador del punto del servicio a procesar    
     *                     - cliente                  Nombre del cliente del cual se procesara la solicitud de retiro
     *                                                de equipo
     *                     - esRecontratacion         Cadena de caracteres que indica si un servicio es recontratacion
     *                     - tercerizadora            Cadena de caracteres que indica el nombre de tercerizadora segun el
     *                                                servicio procesado        
     *                     - login                    Cadena de caracteres que indica el login del servicio a procesar         
     *                     - tipoOrden                Cadena de caracteres que indica el tipo de orden del servicio a procesar
     *                     - producto                 Cadena de caracteres que indica el nombre de producto o plan del
     *                                                servicio a procesar           
     *                     - coordenadas              Cadena de caracteres que indica la coordenada del servicio a procesar
     *                     - direccion                Cadena de caracteres que indica la direccion del login a procesar
     *                     - ciudad                   Cadena de caracteres que indica la ciudad del login a procesar
     *                     - nombreSector             Cadena de caracteres que indica el nombre del sector del login a procesar 
     *                     - idDetalleSolHistorial    Identificador de historial de servicio
     *                     - feIniPlan                Cadena de caracteres que indican la fecha de inicio de planificacion
     *                     - feFinPlan                Cadena de caracteres que indican la fecha de fin de planificacion
     *                     - latitud                  Cadena de caracteres que indica la latitud del login a procesar
     *                     - longitud                 Cadena de caracteres que indica la longitud del login a procesar
     *                     - elementosPorSolicitud    [  - idSolCaract      Identificador de caracteristica de solicitud
     *                                                   - tipoElemento     Cadena de caracteres que indica el tipo de elemento a retirar
     *                                                   - nombreElemento   Cadena de caracteres que indica el nombre del elemento a retirar
     *                                                   - idElemento       Identificador del elemento a retirar
     *                                                ]
     *                   ]
     * ]
     */
    public function generarSolicitudesRetirarEquipoWs($arrayParametros)
    {
        $arrayRespuesta               = "";
        $arrayRespuesta['strStatus']  = "ERROR";
        $arrayRespuesta['strMensaje'] = "Problemas al recuperar Información de retiros de equipos";
        $arrayRespuesta['arrayData']  = "";
        try
        {       
            $arrayRespuestaSolicitudes = $this->emComercial
                                              ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                              ->generarArraySolicitudesRetirarEquipoWs($arrayParametros);
           
            
            $arrayRespuesta['strStatus']  = $arrayRespuestaSolicitudes['strStatus'];
            $arrayRespuesta['strMensaje'] = $arrayRespuestaSolicitudes['strMensaje'];
            $arrayRespuesta['arrayData']  = $arrayRespuestaSolicitudes['arrayRetiros'];
 
           
        }
        catch(\Exception $ex)
        {            
            $this->serviceUtil->insertError('Telcos+',
                                            'RetiroEquipoService.generarArraySolicitudesRetirarEquipoWs',
                                            $ex->getMessage(),
                                            $arrayParametros['strUsuarioCreacion'],
                                            $arrayParametros['strIpCreacion']
                                           );
        }
        return $arrayRespuesta;
    }
   
    /**
     * Funcion que sirve para buscar CPE en el NAF y en TELCOS
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 28-12-2016
     * @since 1.0
     *
     * @param Array $arrayParametros [
     *                                  - strPrefijoEmpresa    Cadena de caracteres que indica el prefijo de la empresa
     *                                  - intIdEmpresa         Cadena de caracteres que indica el id de la empresa
     *                                  - intIdServicio        Identificador de servicio
     *                                  - strModeloCpe         Cadena de caracteres que indica el modelo de cpe
     *                                  - strEstadoCpe         Cadena de caracteres que indica el estado de cpe
     *                                  - strBandera           Cadena de caracteres que indica bandera usada en proceso de consulta de información
     *                                  - strSerieCpe          Cadena de caracteres que indica la serie del cpe
     *                                  - intIdElementoCpe     Identificador de elemento cpe
     *                               ]        
     * @return Array $arrayRespuesta [
     *                                  - strMensaje           Cadena de caracteres que indica el mensaje de la transaccion ejecutada
     *                                  - strStatus            Cadena de caracteres que indica el estado de la transaccion ejecutada
     *                                  - strMacCpe            Cadena de caracteres que indica la mac del elemento consultado
     *                                  - strModoCpe           Cadena de caracteres que indica el modo del elemento consultado
     *                                  - strNombreCpe         Cadena de caracteres que indica el nombre del elemento consultado
     *                                  - strDescripcionCpe    Cadena de caracteres que indica la descripcion del elemento consultado
     *                               ]
     * ]
     */
    public function buscarCpeNaf($arrayParametros)
    {
        $strPrefijoEmpresa                   = $arrayParametros['strPrefijoEmpresa'];
        $intIdEmpresa                        = $arrayParametros['intIdEmpresa'];
        $intIdServicio                       = $arrayParametros['intIdServicio'];
        $strModeloCpe                        = $arrayParametros['strModeloCpe'];
        $strEstadoCpe                        = $arrayParametros['strEstadoCpe'];
        $strBandera                          = $arrayParametros['strBandera'];
        $strSerieCpe                         = $arrayParametros['strSerieCpe'];
        $intIdElementoCpe                    = $arrayParametros['intIdElementoCpe'];
        $arrayRespuesta                      = array();
        $arrayRespuesta['strMensaje']        = "";
        $arrayRespuesta['strStatus']         = "ERROR";
        $arrayRespuesta['strMacCpe']         = "Sin Informacion";
        $arrayRespuesta['strModoCpe']        = "Sin Informacion";
        $arrayRespuesta['strNombreCpe']      = "Sin Informacion";
        $arrayRespuesta['strDescripcionCpe'] = "Sin Informacion";
        $strCaracteristica                   = 'MAC'; 
        $strDescripcionProducto              = "";
        $strSerieFisica                      = "";
        try
        {
            $strSerieCpe = ($strSerieCpe ? trim($strSerieCpe) : "");
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            if($strPrefijoEmpresa == 'TN')
            {
                if (is_object($objServicio))
                {
                    $strDescripcionProducto = $objServicio->getProductoId()->getNombreTecnico();
                }
                if($strDescripcionProducto == 'INTERNET WIFI')
                {
                    $strCaracteristica = 'MAC WIFI';
                }
 
            }
            else
            {
                $strDescripcionProducto = "INTERNET DEDICADO";  
            }
 
            $objProductoInternetDedicado   = $this->emComercial
                                                  ->getRepository('schemaBundle:AdmiProducto')
                                                  ->findOneBy(array("descripcionProducto" => $strDescripcionProducto,
                                                                    "empresaCod"          => $intIdEmpresa,
                                                                    "estado"              => "Activo"));
 
            $objCaracteristicaModo         = $this->emComercial
                                                  ->getRepository('schemaBundle:AdmiCaracteristica')
                                                  ->findOneBy(array("descripcionCaracteristica" => "MODO OPERACION",
                                                                    "estado"                    => "Activo"));
            $objCaracteristicaMac          = $this->emComercial
                                                  ->getRepository('schemaBundle:AdmiCaracteristica')
                                                  ->findOneBy(array( "descripcionCaracteristica" => $strCaracteristica,
                                                                     "estado"                    => "Activo"));
            if(is_object($objCaracteristicaModo) && is_object($objCaracteristicaMac) && is_object($objProductoInternetDedicado))
            {
 
                $objProductoCaracteristicaMac  = $this->emComercial
                                                      ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                      ->findOneBy(array( "productoId"       => $objProductoInternetDedicado->getId(),
                                                                         "caracteristicaId" => $objCaracteristicaMac->getId()));
 
                $objProductoCaracteristicaModo = $this->emComercial
                                                      ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                      ->findOneBy(array("productoId"       => $objProductoInternetDedicado->getId(),
                                                                        "caracteristicaId" => $objCaracteristicaModo->getId()));
 
                if(is_object($objProductoCaracteristicaMac))
                {
                    $objMac  = $this->emComercial
                                    ->getRepository('schemaBundle:InfoServicioProdCaract')
                                    ->findOneBy(array("servicioId"                => $intIdServicio,
                                                      "productoCaracterisiticaId" => $objProductoCaracteristicaMac->getId(),
                                                      "estado"                    => "Activo"));
                }
                if(is_object($objProductoCaracteristicaModo))
                {
                    $objModo = $this->emComercial
                                    ->getRepository('schemaBundle:InfoServicioProdCaract')
                                    ->findOneBy(array("servicioId"                => $intIdServicio,
                                                      "productoCaracterisiticaId" => $objProductoCaracteristicaModo->getId(),
                                                      "estado"                    => "Activo"));
                }
            }
 
            //LOGICA DE NEGOCIO - CAPA DE SERVICIO
            $arrayRespuestaService = $this->serviceServicioTecnico->buscarElementoEnNaf($strSerieCpe,$strModeloCpe,$strEstadoCpe,$strBandera);
            $strStatus             = $arrayRespuestaService[0]['status'];
            $strMensaje            = $arrayRespuestaService[0]['mensaje'];
 
            //se valida serie no sensitiva a Mayusculas ni Minusculas y que exista en las bases de datos de Telcos
            $objElementoCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneBy(array("id" => $intIdElementoCpe));
            if (is_object($objElementoCliente))
            {
                $strSerieFisica = ($objElementoCliente->getSerieFisica() ? $objElementoCliente->getSerieFisica() : "");
            }
            if(strtoupper(trim($strSerieFisica)) != strtoupper($strSerieCpe))
            {
                $arrayRespuesta['strMensaje'] = "La serie ingresada no coincide con la registrada en la activacion";       
                if($strStatus == "OK")
                {
                    $objCpeTelcos       = $this->emInfraestructura
                                               ->getRepository('schemaBundle:InfoElemento')
                                               ->findOneBy(array( "serieFisica" => $strSerieCpe,
                                                                  "estado"      => "Activo"));
 
                    $objServicioTecnico = $this->emComercial
                                               ->getRepository('schemaBundle:InfoServicioTecnico')
                                               ->findOneBy(array( "servicioId" => $intIdServicio));
 
                    //validacion abierta temporalmente por vrodriguez
                    $objCpeTelcos = 1;
                    if($objCpeTelcos)
                    {
                        //validacion abierta temporalmente por vrodriguez
                        if($objCpeTelcos)
                        {
                            if(is_object($objMac))
                            {
                                $arrayRespuesta['strMacCpe']  = $objMac->getValor();
                            }
 
                            if(is_object($objModo))
                            {
                                $arrayRespuesta['strModoCpe'] = $objModo->getValor();
                            }
                            //validacion abierta temporalmente por vrodriguez         
                            //$arrayRespuesta['nombreCpe'] = $objCpeTelcos->getNombreElemento();
                            $arrayRespuesta['strDescripcionCpe'] = $strMensaje;
                            //se limpia bandera de error
                            $arrayRespuesta['strMensaje']        = "";
                            $arrayRespuesta['strStatus']         = "OK";
 
                        }
                        else
                        {
                            $arrayRespuesta['strMensaje'] = "SERIAL DE CPE NO ASIGNADO A ESTE SERVICIO. FAVOR REVISAR";
                        }
                    }
                    else
                    {
                        $arrayRespuesta['strMensaje'] = "SERIAL DE CPE NO EXISTE EN EL SISTEMA";
                    }
                }
                else
                {
                    $arrayRespuesta['strMensaje'] = $strMensaje;
                }
            }
            else
            {
                //se agrega codigo para realizar validacion de modelo contra el naf segun sea el caso
                $arrayRespuesta['strDescripcionCpe'] = "SERIAL REGISTRADO EN TELCOS";
 
                if ($strStatus == "OK")
                {
                    $objCpeTelcos       = $this->emInfraestructura
                                               ->getRepository('schemaBundle:InfoElemento')
                                               ->findOneBy(array( "serieFisica" => $strSerieCpe,
                                                                  "estado"      => "Activo"));
                    $objServicioTecnico = $this->emComercial
                                               ->getRepository('schemaBundle:InfoServicioTecnico')
                                               ->findOneBy(array( "servicioId"  => $intIdServicio));
                    //validacion abierta temporalmente por vrodriguez
                    $objCpeTelcos = 1;
 
                    if($objCpeTelcos)
                    {
                        //validacion abierta temporalmente por vrodriguez
                        //if($objServicioTecnico->getElementoClienteId()==$objCpeTelcos->getId()){
                        if($objCpeTelcos)
                        {
                            if(is_object($objMac))
                            {
                                $arrayRespuesta['strMacCpe'] = $objMac->getValor();
                            }
                            if(is_object($objModo))
                            {
                                $arrayRespuesta['strModoCpe'] = $objModo->getValor();
                            }
                            //validacion abierta temporalmente por vrodriguez         
                            //$arrayRespuesta['nombreCpe']    = $objCpeTelcos->getNombreElemento();
                            $arrayRespuesta['strDescripcionCpe'] = $strMensaje;
                            //se limpia bandera de error
                            $arrayRespuesta['strMensaje']     = "";
                            $arrayRespuesta['strStatus']      = "OK";
 
                        }
                        else
                        {
                            $arrayRespuesta['strMensaje'] = "SERIAL DE CPE NO ASIGNADO A ESTE SERVICIO. FAVOR REVISAR";
                        }
                    }
                    else
                    {
                        $arrayRespuesta['strMensaje'] = "SERIAL DE CPE NO EXISTE EN EL SISTEMA";
                    } 
                }
                else
                {
                    $arrayRespuesta['strMensaje'] = $strMensaje;
                }
            }
        }
        catch(\Exception $ex)
        {                   
            $arrayRespuesta['strMensaje'] = "EXISTIO UN ERROR AL CONSULTAR INFORMACION";
            $arrayRespuesta['strStatus']  = "ERROR";
            $this->serviceUtil->insertError('Telcos+',
                                            'RetiroEquipoService.buscarCpeNaf',
                                            $ex->getMessage(),
                                            $arrayParametros['strUsuarioCreacion'],
                                            $arrayParametros['strIpCreacion']
                                           );
        }
        return $arrayRespuesta;
    }
   
    /**
     * Metodo para realizar la finalizacion de las solicitudes de retiro de equipo
     * Retorna response
     * @return response
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 29-12-2016
     * @since 1.0
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 09-01-2017    Se cambia metodo de capa controller a capa service para ser reutilizada en WS utilizado por app Movil
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 09-08-2017 - En la tabla INFO_TAREA_SEGUIMIENTO y INFO_DETALLE_HISTORIAL, se regulariza el departamento creador de la accion y
     *                            se adicionan los campos de persona empresa rol id para identificar el responsable actual.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.3 24-11-2017 - Se solicita eliminar la lógica que finalizaba la tarea de retiro de equipo cuando se finalizaba la solicitud de
     *                           retiro de equipo.
     * @author Sofia Fernandez <sfernandez.ec>
     * @version 1.4 24-11-2017 - Se elimina la insercion de la caracteristica del estado del elemento y empleado que retira el equipo debido a que
     *                           se lo maneja desde NAF.
     * @author Sofia Fernandez <sfernandez.ec>
     * @version 1.5 03-02-2018 - Se agrega finalziacion de la tarea.
     * 
     * @author Sofia Fernandez <sfernandez.ec>
     * @version 1.6 18-03-2019 - Se agrega validacion para finalziacion de la tarea.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.7 29-03-2018 - No se finaliza la tarea de retiro de equipo. Unicamente la solicitud.
     *
     * @author Byron Anton <banton@telconet.ec>
     * @version 1.8 04-07-2019 - Se modifica validación para ver si existe tarea en el historial como finalizada.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.9 15-01-2019 - Se agrega una comprobacion para generar correo de notificacion a personal de RADIO
     *                           sobre equipos Wifi devueltos en bodega.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 2.0 30-03-2023 - Se agrega prefijo empresa EN en validaciones que mencionan a MD para permitir flujo de retiro de equipo para ECUANET.
     * 
     * @param Array $arrayParametros [
     *                                  - strIpCreacion       Cadena de caracteres que indica la ip de donde se ejecuta la transaccion
     *                                  - strCodEmpresa       Cadena de caracteres que indica el id de la empresa
     *                                  - intIdSolicitud      Identificador de solicitud de retiro de equipo
     *                                  - strBuscarCpeNaf     Cadena de caracteres que indica si debe ser consultado en el naf los equipos procesados
     *                                  - intIdResponsable    Identificador de responsable de retiro de equipo
     *                                  - strDatosElementos   Cadena de caracteres que indica los elementos a retirar
     *                                  - strPrefijoEmpresa   Cadena de caracteres que indica el prefijo de la empresa a procesar
     *                                  - strUsuarioCreacion  Cadena de caracteres que indica el usuario que ejecuta el retiro
     *                                  - intIdDepartamento   Id del departamento en session
     *                               ]        
     * @return Array $arrayRespuesta [
     *                                  - strMensaje          Cadena de caracteres que indica el mensaje de la transaccion ejecutada
     *                                  - strStatus           Cadena de caracteres que indica el estado de la transaccion ejecutada
     *                               ]
     *
     */
    public function finalizarRetiroEquipo($arrayParametros)
    {
        $strIpCreacion         = $arrayParametros['strIpCreacion'];
        $strCodEmpresa         = $arrayParametros['strCodEmpresa'];
        $intIdSolicitud        = $arrayParametros['intIdSolicitud'];
        $strBuscarCpeNaf       = $arrayParametros['strBuscarCpeNaf'];
        $intIdResponsable      = $arrayParametros['intIdResponsable'];
        $arrayDatosElementos   = $arrayParametros['arrayDatosElementos'];
        $strPrefijoEmpresa     = $arrayParametros['strPrefijoEmpresa'];
        $strUsuarioCreacion    = $arrayParametros['strUsuarioCreacion'];
        $intCantidad           = 1;
        $strEstadoRe           = 'RE';
        $strTipoArticulo       = 'AF';
        $boolEstadoRetiro      = false;
        $strCodEmpresaNaf      = $strCodEmpresa;
        $boolGuardoElementos   = true;
        $strCadenaElementosNoEntregados               = "";
        $strMsjErrorFinalVerificacionActualizacionNaf = "";
 
        $this->emNaf->beginTransaction();
        $this->emSoporte->beginTransaction();
        $this->emComercial->beginTransaction();
        $this->emInfraestructura->beginTransaction();
        
        try
        {
            if($arrayDatosElementos)
            {
                $arrayElementos = $arrayDatosElementos['elementos'];
            }
 
            if($strPrefijoEmpresa == "MD" || $strPrefijoEmpresa == "EN")
            {
                $objEmpresaTN     = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo("TN");
                $strCodEmpresaNaf = $objEmpresaTN->getId();
            }
 
            $objDetalleSolicitud  = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($intIdSolicitud);
 
 
            //se valida si existe entidad detalle solicitud
            if(is_object($objDetalleSolicitud))
            {
                $objEmpleadoResponsable = $this->emComercial->getRepository('schemaBundle:InfoPersona')->findOneById($intIdResponsable);
                //se valida si existe custodo asignado
                if(is_object($objEmpleadoResponsable))
                {
 
 
                    //bucle para recorrer elementos a retirar
                    if(count($arrayElementos) == 0)
                    {
                        $strMensajeResponse  = "Solicitud sin Equipos para Retirar. Favor notificar a Sistemas.";
                        $boolGuardoElementos = false;
                    }
                    else
                    {
                        for($i = 0; $i < count($arrayElementos); $i++)
                        {
                            $strBuscarCpeNafTmp                          = $strBuscarCpeNaf;
                            $intIdSolCaract                              = $arrayElementos[$i]['idSolCaract'];
                            $strSerieCpe                                 = $arrayElementos[$i]['serieElemento'] ?
                                                                           trim($arrayElementos[$i]['serieElemento']):
                                                                           "";
                            $strEstadoCpe                                = $arrayElementos[$i]['estadoElemento'];
                            $strEntregadoCpe                             = $arrayElementos[$i]['entregado'];
                            $strTipoElemento                             = $arrayElementos[$i]['tipoElemento'];
                            $strCodigoArticulo                           = $arrayElementos[$i]['modeloElemento']; //modelo del elemento cliente
                            $strNombreElemento                           = $arrayElementos[$i]['nombreElemento'];
                            $intIdArticuloNaf                            = $arrayElementos[$i]['idArticuloNaf'];
                            $strMsjErrorTelcosNaf                        = '';
                            $strMsjErrorLowerTelcosNaf                   = '';
                            $boolBanderaElementosSinSerie                = false;
                            $boolGuardarCaracteristicasElemento          = false;
                            $strMsjErrorVerificarActualizacionEnNaf      = '';
                            $strMsjErrorVerificarActualizacionEnNafLower = '';
                            //si es roseta no se debe buscar en el naf porque no tiene serie
                            if($strTipoElemento == 'ROSETA')
                            {
                                $strBuscarCpeNafTmp = 'NO';
                            }
                           
                            // se valida si el idSolCaract es mayor a cero
                            if($intIdSolCaract > 0)
                            {
                                /*
                                 * Si el elemento tiene como estado 'NO ENTREGADO', no debe obtener la serie física registrada en el telcos,
                                 * sino directamente proceder a finalizar los equipos.
                                 */
                                if($strEntregadoCpe == "no")
                                {
                                    $strMsjErrorTelcosNaf           = '';
                                    $boolBanderaElementosSinSerie   = true;
                                    $strCadenaElementosNoEntregados = $strCadenaElementosNoEntregados . " " . $strNombreElemento;
                                }
                                else
                                {
                                    /*
                                     * Se obliga a que la serie debería estar en NAF. Si esto no ocurriera, no se puede realizar el retiro del equipo
                                     */
                                    if($strBuscarCpeNafTmp=="SI" && isset($intIdArticuloNaf) && !empty($intIdArticuloNaf))
                                    {
                                        /*
                                         * Si el elemento se encuentra en estado 'NO ENTREGADO', no se procede a realizar validación de la serie
                                         * en el Naf, puesto que no se ingresa ni la serie ni el modelo del elemento.
                                         * Esta validación ha sido solicitada por el usuario
                                         */
                                        $strMsjErrorTelcosNaf   = str_repeat(' ', 1000);
                                        $strSql                 = "BEGIN AFK_PROCESOS.IN_P_RETIRA_INSTALACION(:codigoEmpresaNaf, ".
                                                                  ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, ".
                                                                  ":cantidad, :estado, :pv_mensajeerror); END;";
                                        $objStmt                = $this->emNaf->getConnection()->prepare($strSql);
                                        $objStmt->bindParam('codigoEmpresaNaf'      , $strCodEmpresaNaf);
                                        $objStmt->bindParam('codigoArticulo'        , $strCodigoArticulo);
                                        $objStmt->bindParam('tipoArticulo'          , $strTipoArticulo);
                                        $objStmt->bindParam('identificacionCliente' , $objEmpleadoResponsable->getIdentificacionCliente());
                                        $objStmt->bindParam('serieCpe'              , strtoupper($strSerieCpe));
                                        $objStmt->bindParam('cantidad'              , $intCantidad);
                                        $objStmt->bindParam('estado'                , $strEstadoRe);
                                        $objStmt->bindParam('pv_mensajeerror'       , $strMsjErrorTelcosNaf);
                                        $objStmt->execute();
                                        if(trim($strMsjErrorTelcosNaf))
                                        {
                                            $strMsjErrorLowerTelcosNaf  = str_repeat(' ', 1000);
                                            $strSql                     = "BEGIN AFK_PROCESOS.IN_P_RETIRA_INSTALACION(:codigoEmpresaNaf, ".
                                                                          ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, ".
                                                                          ":cantidad, :estado, :pv_mensajeerror); END;";
                                            $objStmt                    = $this->emNaf->getConnection()->prepare($strSql);
                                            $objStmt->bindParam('codigoEmpresaNaf'      , $strCodEmpresaNaf);
                                            $objStmt->bindParam('codigoArticulo'        , $strCodigoArticulo);
                                            $objStmt->bindParam('tipoArticulo'          , $strTipoArticulo);
                                            $objStmt->bindParam('identificacionCliente' , $objEmpleadoResponsable->getIdentificacionCliente());
                                            $objStmt->bindParam('serieCpe'              , strtolower($strSerieCpe));
                                            $objStmt->bindParam('cantidad'              , $intCantidad);
                                            $objStmt->bindParam('estado'                , $strEstadoRe);
                                            $objStmt->bindParam('pv_mensajeerror'       , $strMsjErrorLowerTelcosNaf);
                                            $objStmt->execute();
 
                                            if(!trim($strMsjErrorLowerTelcosNaf))
                                            {
                                                $strMsjErrorTelcosNaf                   = '';
                                                $strMsjErrorVerificarActualizacionEnNaf = '';
                                            }
                                        }
                                    }
                                    /*
                                     * Así no sea obligatorio buscarlo en el NAF, se realiza la consulta de igual manera, con la diferencia que
                                     * ésta no formará parte de un mensaje de error, sino que aparecerá como un mensaje de advertencia y permitirá
                                     * realizar el retiro
                                     */
                                    else
                                    {
                                        /*
                                         * Sólo si el elemento no es una roseta debería ir a NAF a verificar si existe la serie para mostrarlo
                                         * como parte del mensaje de advertencia al usuario
                                         */
                                        if($strTipoElemento != 'ROSETA' && isset($intIdArticuloNaf) &&   !empty($intIdArticuloNaf))
                                        {
                                            /*
                                             * Si $strEntregadoCpe=si significa que el estado es diferente de NO ENTREGADO, por ende el usuario
                                             * puede ingresar la serie y el modelo del elemento y además si $strBuscarCpeNafTmp=NO, implica que el
                                             * servicio tiene fecha de activación menor al 2016-07-01, por lo que se asume que el elemento
                                             * no existe en NAF.
                                             * Sin embargo puede darse que el elemento si se encuentre en NAF, por lo que es necesario
                                             * que se permita finalizar el equipo en telcos y verificar si el elemento existe en NAF, para su respectivo
                                             * retiro, es decir no habra problemas de retiro en Telcos y se le mostrará al usuario si ha ocurrido
                                             * algún problema en NAF para que verifique y realice la gestión manualmente.
                                             */
                                            $strMsjErrorTelcosNaf                   = "";
                                            $boolGuardarCaracteristicasElemento     = true;
                                            $strMsjErrorVerificarActualizacionEnNaf = str_repeat(' ', 1000);
                                            $strSql                                 = "BEGIN AFK_PROCESOS.IN_P_RETIRA_INSTALACION(:codigoEmpresaNaf, ".
                                                                                      ":codigoArticulo, :tipoArticulo, :identificacionCliente, ".
                                                                                      ":serieCpe, :cantidad, :estado, :pv_mensajeerror); END;";
                                            $objStmt                                = $this->emNaf->getConnection()->prepare($strSql);
                                            $objStmt->bindParam('codigoEmpresaNaf'      , $strCodEmpresaNaf);
                                            $objStmt->bindParam('codigoArticulo'        , $strCodigoArticulo);
                                            $objStmt->bindParam('tipoArticulo'          , $strTipoArticulo);
                                            $objStmt->bindParam('identificacionCliente' , $objEmpleadoResponsable->getIdentificacionCliente());
                                            $objStmt->bindParam('serieCpe'              , strtoupper($strSerieCpe));
                                            $objStmt->bindParam('cantidad'              , $intCantidad);
                                            $objStmt->bindParam('estado'                , $strEstadoRe);
                                            $objStmt->bindParam('pv_mensajeerror'       , $strMsjErrorVerificarActualizacionEnNaf);
                                            $objStmt->execute();
                                            if(trim($strMsjErrorVerificarActualizacionEnNaf))
                                            {
                                                $strMsjErrorVerificarActualizacionEnNafLower    = str_repeat(' ', 1000);
                                                $strSql                                         = "BEGIN AFK_PROCESOS.IN_P_RETIRA_INSTALACION( ".
                                                                                                  ":codigoEmpresaNaf,:codigoArticulo, :tipoArticulo, ".
                                                                                                  ":identificacionCliente, :serieCpe, :cantidad, ".
                                                                                                  ":estado, :pv_mensajeerror); END;";
 
                                                $objStmt                                        = $this->emNaf->getConnection()->prepare($strSql);
                                                $objStmt->bindParam('codigoEmpresaNaf'      , $strCodEmpresaNaf);
                                                $objStmt->bindParam('codigoArticulo'        , $strCodigoArticulo);
                                                $objStmt->bindParam('tipoArticulo'          , $strTipoArticulo);
                                                $objStmt->bindParam('identificacionCliente' , $objEmpleadoResponsable->getIdentificacionCliente());
                                                $objStmt->bindParam('serieCpe'              , strtolower($strSerieCpe));
                                                $objStmt->bindParam('cantidad'              , $intCantidad);
                                                $objStmt->bindParam('estado'                , $strEstadoRe);
                                                $objStmt->bindParam('pv_mensajeerror'       , $strMsjErrorVerificarActualizacionEnNafLower);
                                                $objStmt->execute();
 
                                                if(!trim($strMsjErrorVerificarActualizacionEnNafLower))
                                                {
                                                    $strMsjErrorVerificarActualizacionEnNaf = '';
                                                }
                                            }
                                        }
                                    }
                                }
 
                                if(trim($strMsjErrorTelcosNaf))
                                {
                                    $strMensajeResponse  = "Error naf: " . $strMsjErrorTelcosNaf;
                                    $boolGuardoElementos = false;
                                    break;
                                }
 
                                $objCaractSolElemento   = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')->find($intIdSolCaract);
                                if(is_object($objCaractSolElemento))
                                {
                                    $objCaractSolElemento->setEstado("Finalizada");
                                    $this->emComercial->persist($objCaractSolElemento);
                                    /*
                                     * Se valida si se deben guardar la serie y el modelo como caracteristica de la solicitud
                                     * haciendo referencia a la otra característica que contiene el elemento de la solicitud y además
                                     * validando que se haya ingresado la serie y el modelo del elemento
                                     */
                                    if($boolGuardarCaracteristicasElemento && !$boolBanderaElementosSinSerie)
                                    {
                                        //Guarda la serie y el id del modelo ingresados como caracteristicas del detalle solicitud
                                        $strNombreCaractSerie   = 'RETIRO_SERIE_ELEMENTO';
 
                                        $objCaracteristicaSerie = $this->emComercial
                                                                       ->getRepository('schemaBundle:AdmiCaracteristica')
                                                                       ->findOneBy(array(
                                                                                         "descripcionCaracteristica" => $strNombreCaractSerie,
                                                                                         "estado"                    => "Activo"
                                                                                        )
                                                                                  );
                                        if(is_object($objCaracteristicaSerie))
                                        {
                                            $objInfoDetalleSolCaractSerie   = new InfoDetalleSolCaract();
                                            $objInfoDetalleSolCaractSerie->setCaracteristicaId($objCaracteristicaSerie);
                                            $objInfoDetalleSolCaractSerie->setValor($strSerieCpe);
                                            $objInfoDetalleSolCaractSerie->setDetalleSolicitudId($objDetalleSolicitud);
                                            $objInfoDetalleSolCaractSerie->setEstado("Finalizada");
                                            $objInfoDetalleSolCaractSerie->setFeCreacion(new \DateTime('now'));
                                            $objInfoDetalleSolCaractSerie->setUsrCreacion($strUsuarioCreacion);
                                            $objInfoDetalleSolCaractSerie->setDetalleSolCaractId($intIdSolCaract);
                                            $this->emComercial->persist($objInfoDetalleSolCaractSerie);
                                        }
 
                                        $strNombreCaractModelo   = 'RETIRO_MODELO_ELEMENTO';
                                        $objCaracteristicaModelo = $this->emComercial
                                                                        ->getRepository('schemaBundle:AdmiCaracteristica')
                                                                        ->findOneBy(array(
                                                                                          "descripcionCaracteristica"=>$strNombreCaractModelo,
                                                                                          "estado"                   =>"Activo"
                                                                                         )
                                                                                   );
                                        if(is_object($objCaracteristicaModelo))
                                        {
                                            $objInfoDetalleSolCaractModelo   = new InfoDetalleSolCaract();
                                            $objInfoDetalleSolCaractModelo->setCaracteristicaId($objCaracteristicaModelo);
                                            $objInfoDetalleSolCaractModelo->setValor($strCodigoArticulo);
                                            $objInfoDetalleSolCaractModelo->setDetalleSolicitudId($objDetalleSolicitud);
                                            $objInfoDetalleSolCaractModelo->setEstado("Finalizada");
                                            $objInfoDetalleSolCaractModelo->setFeCreacion(new \DateTime('now'));
                                            $objInfoDetalleSolCaractModelo->setUsrCreacion($strUsuarioCreacion);
                                            $objInfoDetalleSolCaractModelo->setDetalleSolCaractId($intIdSolCaract);
                                            $this->emComercial->persist($objInfoDetalleSolCaractModelo);
                                        }
                                    }
                                }
                                // cierre de if - se valida si el idSolCaract es mayor a cero
                            }
                            else
                            {
                                $strMensajeResponse  = "Solicitud sin Equipos para Retirar. Favor notificar a Sistemas.";
                                $boolGuardoElementos = false;
                                break;
                            }
 
                            /*
                             * Obteniendo Mensaje de Verificacion y Actualizacion en Naf
                             */
                            if(trim($strMsjErrorVerificarActualizacionEnNaf))
                            {
                                $strMsjErrorFinalVerificacionActualizacionNaf .= $strMsjErrorVerificarActualizacionEnNaf."<br/>";
                            }
                            //cierre de bucle - bucle para recorrer elementos a retirar       
                        }//cierre for
                    }
 
                    // se obtiene destinatarios para notificaciones siguientes
                    $objServicio         = $this->emComercial
                                                ->getRepository('schemaBundle:InfoServicio')
                                                ->findOneById($objDetalleSolicitud->getServicioId());
                    $arrayFormasContacto = $this->emComercial
                                                ->getRepository('schemaBundle:InfoPersona')
                                                ->getContactosByLoginPersonaAndFormaContacto($objServicio->getPuntoId()->getUsrVendedor(),
                                                                                             'Correo Electronico');
                    $arrayTo        = array();
 
                    if($arrayFormasContacto)
                    {
                        foreach($arrayFormasContacto as $objFormaContacto)
                        {
                            $arrayTo[] = $objFormaContacto['valor'];
                        }
                    }
                    //valida si la bandera guardoElementos se encuentra en true
                    if($boolGuardoElementos)
                    {
                        $strEstadoAnteriorDetalleSolicitud = $objDetalleSolicitud->getEstado();
                        if($strEstadoAnteriorDetalleSolicitud!="Finalizada")
                        {
                            $objDetalleSolicitud->setEstado("Finalizada");
                            $this->emComercial->persist($objDetalleSolicitud);
                            $this->emComercial->flush();
                            $strObservacion = $objDetalleSolicitud->getObservacion();

                            //Finalizacion de la tarea
                            if(is_object($objDetalleSolicitud))
                            {
                                //FINALIZACIÓN DE TAREA
                                $objDetalle = $this->emSoporte
                                                   ->getRepository('schemaBundle:InfoDetalle')
                                                   ->findOneByDetalleSolicitudId($objDetalleSolicitud->getId());

                                if(is_object($objDetalle))
                                {
                                    $arrayDetalleHistorial = $this->emSoporte
                                                                  ->getRepository('schemaBundle:InfoDetalleHistorial')
                                                                  ->findBy(array('detalleId' => $objDetalle->getId(),
                                                                                 'estado'    => "Finalizada"));
                                    if(count($arrayDetalleHistorial) > 0) 
                                    {
                                       error_log("Tarea en el historial ya se encuentra Finalizada : ".$objDetalle->getId());
                                    } 
                                    else 
                                    {
                                        //Se ingresa el historial de la info_detalle
                                        $arrayParametrosHist["intDetalleId"]         = $objDetalle->getId();
                                        $arrayParametrosHist["strObservacion"]       = $objDetalleSolicitud->getObservacion();
                                        $arrayParametrosHist["strAccion"]            = "Finalizada";
                                        $arrayParametrosHist["strEnviaDepartamento"] = "N";
                                        $arrayParametrosHist["strCodEmpresa"]        = $strCodEmpresa;
                                        $arrayParametrosHist["strUsrCreacion"]       = $strUsuarioCreacion;
                                        $arrayParametrosHist["strEstadoActual"]      = "Finalizada";
                                        $arrayParametrosHist["strOpcion"]            = "Historial";
                                        $arrayParametrosHist["strIpCreacion"]        = $strIpCreacion;

                                        $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

                                        //Se ingresa el seguimiento de la tarea
                                        $arrayParametrosHist["strObservacion"] = "Tarea fue Finalizada. Obs : " . $objDetalleSolicitud->getObservacion();
                                        $arrayParametrosHist["strOpcion"]      = "Seguimiento";

                                        $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                                    }
                                }
                                else
                                {
                                    $strMensajeResponse = "No existe Tarea";
                                }
                            }
                            // cierre de if - se valida si existe entidad detalle solicitud
                            else
                            {
                                $strMensajeResponse = "No existe Solicitud";
                            }
                        }
                        // se valida si existieron elementos sin serie valida
                        if($strCadenaElementosNoEntregados!="")
                        {
                            //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                            $objDetalleSolHist = new InfoDetalleSolHist();
                            $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                            $objDetalleSolHist->setIpCreacion($strIpCreacion);
                            $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                            $objDetalleSolHist->setUsrCreacion($strUsuarioCreacion);
                            $objDetalleSolHist->setObservacion('Existieron equipos no entregados y que no tienen '.
                                                                  'serie, Elementos:' . $strCadenaElementosNoEntregados .
                                                                  ', favor verificar.');
                            $objDetalleSolHist->setEstado($strEstadoAnteriorDetalleSolicitud);
                            $this->emComercial->persist($objDetalleSolHist);
                            $objDetalleSolicitudSinSerie = $objDetalleSolicitud;
                            // notificaciones en caso de existir elementos sin serie valida en Telcos
                            $strAsunto = "Solicitud de Retiro de Equipo con elementos sin serie valida #" . $objDetalleSolicitud->getId();
                            $objDetalleSolicitudSinSerie->setObservacion('Existieron equipos no entregados y que no tienen serie,'.
                                                                         ' Elementos:' . $strCadenaElementosNoEntregados . ', favor verificar.');
                           
                            $arrayParametrosMail   = array('detalleSolicitud'     => $objDetalleSolicitudSinSerie,
                                                           'detalleSolicitudHist' => null,
                                                           'motivo'               => null);
                            if($strPrefijoEmpresa == "MD" || $strPrefijoEmpresa == "EN")
                            {
                                $objServicioMail = $this->emComercial
                                                        ->getRepository('schemaBundle:InfoServicio')
                                                        ->findOneById($objDetalleSolicitud->getServicioId());
                                $this->serviceEnvioPlantilla->generarEnvioPlantilla( $strAsunto,
                                                                                     $arrayTo,
                                                                                     'RETIRO_TTCO_MD',
                                                                                     $arrayParametrosMail,
                                                                                     $strCodEmpresa,
                                                                                     $objServicioMail->getPuntoId()
                                                                                                     ->getPersonaEmpresaRolId()
                                                                                                     ->getOficinaId()
                                                                                                     ->getCantonId(),
                                                                                     ''
                                                                                   );
                            }
                            else
                            {
                                $this->serviceEnvioPlantilla->generarEnvioPlantilla( $strAsunto,
                                                                                     $arrayTo,
                                                                                     'RETIRO_TTCO_MD',
                                                                                     $arrayParametrosMail,
                                                                                     $strCodEmpresa,
                                                                                     '',
                                                                                     ''
                                                                                   );
                            }
                        }
                        //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $objDetalleSolHist = new InfoDetalleSolHist();
                        $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                        $objDetalleSolHist->setIpCreacion($strIpCreacion);
                        $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $objDetalleSolHist->setUsrCreacion($strUsuarioCreacion);
                        $objDetalleSolHist->setObservacion('Se finalizo solicitud de retiro de equipo');
                        $objDetalleSolHist->setEstado('Finalizada');
                        $this->emComercial->persist($objDetalleSolHist);
                        $objDetalleSolicitud->setObservacion($strObservacion);
                        // notificaciones en caso de finalizar retiro de equipo
                        $strAsunto = "Solicitud de Retiro de Equipo Finalizada #" . $objDetalleSolicitud->getId();
                       
                        $arrayParametrosMail   = array('detalleSolicitud' => $objDetalleSolicitud, 'detalleSolicitudHist' => null, 'motivo' => null);
                        if($strPrefijoEmpresa == "MD" || $strPrefijoEmpresa == "EN")
                        {
                            $objServicioMail = $this->emComercial
                                                    ->getRepository('schemaBundle:InfoServicio')
                                                    ->findOneById($objDetalleSolicitud->getServicioId());
                            $this->serviceEnvioPlantilla->generarEnvioPlantilla( $strAsunto,
                                                                                 $arrayTo,
                                                                                 'RETIRO_TTCO_MD',
                                                                                 $arrayParametrosMail,
                                                                                 $strCodEmpresa,
                                                                                 $objServicioMail->getPuntoId()
                                                                                                 ->getPersonaEmpresaRolId()
                                                                                                 ->getOficinaId()
                                                                                                 ->getCantonId(),
                                                                                 ''
                                                                               );
                        }
                        else
                        {
                            $this->serviceEnvioPlantilla->generarEnvioPlantilla( $strAsunto,
                                                                                 $arrayTo,
                                                                                 'RETIRO_TTCO_MD',
                                                                                 $arrayParametrosMail,
                                                                                 $strCodEmpresa,
                                                                                 '',
                                                                                 ''
                                                                               );
                        }
 
                        $objAdmiMotivo = $this->emComercial
                                              ->getRepository('schemaBundle:AdmiMotivo')
                                              ->findOneBy(array('nombreMotivo' => 'CANCELACION AUTOMATICA',
                                                                'estado'       => 'Activo'));   
 
                        if (is_object($objAdmiMotivo))
                        {
                            $objServicioHistorialCancel = $this->emComercial
                                                               ->getRepository('schemaBundle:InfoServicioHistorial')
                                                               ->findOneBy(array('servicioId'  => $objServicio,
                                                                                 'estado'      => 'Cancel',
                                                                                 'motivoId'    => $objAdmiMotivo->getId()
                                                                                )
                                                                          );
                        }
                        /* Si el servicio fue cancelado automaticamente por procesos masivos se envía notificación
                           adicional a las jefaturas provinciales */
                        if (is_object($objServicioHistorialCancel))
                        {
                            $arrayTo    = array();
                            $strAsunto  = "Solicitud de Retiro de Equipo #".$objDetalleSolicitud->getId().
                                          " Finalizada generada por Cancelación Automática";
                            $this->serviceEnvioPlantilla->generarEnvioPlantilla( $strAsunto,
                                                                                 $arrayTo,
                                                                                 'RETIRO_MD_TN_CA',
                                                                                 $arrayParametrosMail,
                                                                                 '',
                                                                                 '',
                                                                                 ''
                                                                               );
                        }
 
                       $this->emComercial->flush();
                       $this->emInfraestructura->flush();
                       $this->emSoporte->flush();
                       //se de commit a la transaccion utilizada en actualizaciones de articulos en el naf
                       $this->emNaf->commit();
                       $this->emComercial->commit();
                       $this->emInfraestructura->commit();
                       $this->emSoporte->commit();
                        $strMensajeResponse  = "Se finalizó el Retiro del Equipo con éxito<br/><br/><b>Observación en NAF</b><br/>";
                        $boolEstadoRetiro    = true;
                        
                        if(trim($strMsjErrorFinalVerificacionActualizacionNaf))
                        {
                            $strMensajeResponse  = $strMsjErrorFinalVerificacionActualizacionNaf;
                        }
                        else
                        {
                            $strMensajeResponse .= "El Retiro del Equipo fue realizado con éxito";
                        }

                        //Proceso que graba tarea en INFO_TAREA
                        if(is_object($objDetalle))
                        {
                            $arrayParametrosInfoTarea['intDetalleId']   = $objDetalle->getId();
                            $arrayParametrosInfoTarea['strUsrCreacion'] = $strUsuarioCreacion;
                            $this->serviceSoporte->crearInfoTarea($arrayParametrosInfoTarea);
                        }

                    }// cierre de if - valida si la bandera guardoElementos se encuentra en true                
                }
                else
                {
                    $strMensajeResponse = "No existe el custodio asignado";
                }
            }// cierre de if - se valida si existe entidad detalle solicitud
            else
            {
                $strMensajeResponse = "No existe el detalle de solicitud";
            }
        }
        catch(\Exception $ex)
        {
            if($this->emComercial->getConnection()->isTransactionActive())
            {
               $this->emComercial->rollback();
            }
            $this->emComercial->close();
            if($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->rollback();
            }
            $this->emSoporte->close();
            if($this->emNaf->getConnection()->isTransactionActive())
            {
                $this->emNaf->rollback();
            }
            $this->emNaf->close();
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->rollback();
            }
            $this->emInfraestructura->close();
            $boolEstadoRetiro   = false;
            $strMensajeResponse = "Error: Ha ocurrido un problema al procesar el retiro de equipos por favor notificar a Sistemas! ";
            $this->serviceUtil->insertError('Telcos+',
                                            'RetiroEquipoService.finalizarRetiroEquipo',
                                            $ex->getMessage(),
                                            $arrayParametros['strUsuarioCreacion'],
                                            $arrayParametros['strIpCreacion']
                                           );
        }
        $arrayRespuesta = array('strStatus' => $boolEstadoRetiro? "OK":"ERROR", 'strMensaje' => $strMensajeResponse);

        /*Se valida si la respuesta es OK para solicitar correo para RADIO.*/
        if ($boolEstadoRetiro)
        {

            $this->serviceServicioTecnico->validaNotificacionRadio($objServicio,
                $arrayElementos[0],
                is_object($objDetalle) ? $objDetalle : null);

        }

        return $arrayRespuesta;
    }
   
    /**
     * grabarActaRetiroEquipo
     *
     * Funcion que sirve para grabar el Acta de Retiro de Equipos, generar la firma,
     * generar el pdf y enviar el pdf por mail.
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 04-01-2017
     * @since 1.0
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.1 24-11-2017 - Déspues de generar el acta se debe finalizar la tarea de retiro de equipo.
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.2 02-06-2020 - Se modifica código para crear nueva estructura de archivos.
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 17-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.4 11-11-2020 -Almacenar pdf en el servidor NFS remoto.
     *
     * @author Wilmer Vera  <wvera@telconet.ec>
     * @version 1.5 29-10-2021 - Se realiza cambio al momento de llamar al acta de retiro
     * para piloto se añadio -PLT.
     * 
     * @param Array $arrayParametros [
     *                                  - strCodEmpresa       Cadena de caracteres que indica la empresa a procesar
     *                                  - strPrefijoEmpresa   Cadena de caracteres que indica el prefijo de la empresa
     *                                  - intIdSolicitud      Identificador de solicitud de retiro de equipo
     *                                  - strFirmaCliente64   Cadena de caracteres que indica la firma codificada del cliente
     *                                  - strFirmaEmpleado64  Cadena de caracteres que indica la firma codificada del empleado
     *                                  - strDatosElementos   Cadena de caracteres que indica los elementos a retirar
     *                                  - strUsrCreacion      Cadena de caracteres que indica el usuario que ejecuta el retiro
     *                                  - strIpCreacion       Cadena de caracteres que indica la ip desde donde se ejecuta el retiro
     *                                  - strFeCreacion       Cadena de caracteres que indica la fecha de ejecución del retiro
     *                                  - strNombreEmpleado   Cadena de caracteres que indica nombre del empleado que gestiona retiro
     *                                  - strCedulaEmpleado   Cadena de caracteres que indica cedula del empleado que gestiona retiro
     *                                  - strNombreCliente    Cadena de caracteres que indica nombre del cliente que gestiona retiro
     *                                  - strCedulaCliente    Cadena de caracteres que indica cedula del cliente que gestiona retiro
     *                                  - strObservaciones    Cadena de caracteres que indica observaciones del cliente que gestiona retiro
     *                               ]        
     * @return Array $arrayRespuesta [
     *                                  - strMensaje          Cadena de caracteres que indica el mensaje de la transaccion ejecutada
     *                                  - strStatus           Cadena de caracteres que indica el estado de la transaccion ejecutada
     *                               ]
     */
    public function grabarActaRetiroEquipo($arrayParametros)
    {
        $strCodEmpresa                                  = $arrayParametros['strCodEmpresa'];
        $strPrefijoEmpresa                              = $arrayParametros['strPrefijoEmpresa'];
        $intIdSolicitud                                 = $arrayParametros['intIdSolicitud'];
        $strFirmaCliente64                              = $arrayParametros['strFirmaCliente64'];
        $strFirmaEmpleado64                             = $arrayParametros['strFirmaEmpleado64'];
        $arrayDatosElementos                            = $arrayParametros['arrayDatosElementos'];
        $strUsrCreacion                                 = $arrayParametros['strUsrCreacion'];
        $strIpCreacion                                  = $arrayParametros['strIpCreacion'];
        $dateFeCreacion                                 = $arrayParametros['strFeCreacion'];
        $strHora                                        = date('Y-m-d-His');
        $strFecha                                       = date('Y-m-d');
        $strStatus                                      = "ERROR";
        $strPathSource                                  = "";
        $strMensaje                                     = "";
        $arrayRespuestaFinal                            = array();
        $strServerRoot                                  = $_SERVER['DOCUMENT_ROOT'];
 
        $arrayParametrosHist                            = array();
 
        $arrayParametrosHist["strCodEmpresa"]           = $strCodEmpresa;
        $arrayParametrosHist["strUsrCreacion"]          = $strUsrCreacion;
        $arrayParametrosHist["strEstadoActual"]         = "Finalizada";
        $arrayParametrosHist["strOpcion"]               = "Historial";
        $arrayParametrosHist["strIpCreacion"]           = $strIpCreacion;
        $boolFirmaDefault                               = false;
       
        $this->emComunicacion->getConnection()->beginTransaction();
 
        try
        {
            $arrayObjFinder = new Finder();
            $arrayObjFinder->files()->in(__DIR__);
            foreach($arrayObjFinder as $objFile)
            {
                if(strpos($objFile->getRealpath(), "RetiroEquipo") !== false)
                {
                    $strPathSource = explode("/Service/RetiroEquipoService.php", $objFile->getRealpath())[0];
                    $strPathSource = explode("\Service\RetiroEquipoService.php", $strPathSource)[0];
                }
            }
 
            $objDetalleSolicitud       = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolicitud);
            $objInfoServicio           = $objDetalleSolicitud->getServicioId();
            $intIdServicio             = $objInfoServicio->getId();
            $objInfoPunto              = $objInfoServicio->getPuntoId();
            $strExtensionArchivo       = 'png';
            $strNombreArchivoCliente   = $intIdServicio.'_cliente';
            $strNombreArchivoEmpleado  = $intIdServicio.'_empleado';

            if(isset($arrayParametros['strRutaFisicaCompleta']) && !empty($arrayParametros['strRutaFisicaCompleta']))
            {
                $strRutaFisicaCompleta  = $arrayParametros['strRutaFisicaCompleta'];
                $strRutaFisicaArchivo   = substr($strRutaFisicaCompleta, 0, strrpos($strRutaFisicaCompleta, '/')+1) . 'firmas/';

                $arrayFirma['strPath']      = $strRutaFisicaArchivo;
                $arrayDocumento['strPath']  = $strRutaFisicaCompleta;
                // Si el directorio se crea exitosamente retornara valor 100.
                if("100" != $this->serviceUtil->creaDirectorio($arrayFirma)->getStrStatus() ||
                   "100" != $this->serviceUtil->creaDirectorio($arrayDocumento)->getStrStatus()
                   )
                {
                    throw new \Exception("Problemas al crear los directorios, intenta nuevamente");
                }
            }
            else
            {
                $strRutaFisicaCompleta  = 'public/uploads/documentos';                
                $strRutaFisicaArchivo   = 'public/uploads/firmas/';
            }
           
            if($strFirmaCliente64!="")
            {
                $this->procesarImagenesService->grabarImagenBase64( $strFirmaCliente64,
                                                                    $strNombreArchivoCliente,
                                                                    $strRutaFisicaArchivo,
                                                                    $strExtensionArchivo );
            }
           
            if($strFirmaEmpleado64!="")
            {
                $this->procesarImagenesService->grabarImagenBase64( $strFirmaEmpleado64,
                                                                    $strNombreArchivoEmpleado,
                                                                    $strRutaFisicaArchivo,
                                                                    $strExtensionArchivo );
            }
            else
            {
                $boolFirmaDefault = true;
            }
           
            //obtener datos cliente, servicio, punto, contactos
            $arrayParametrosInfo = array (
                                          'intIdServicio'   => $intIdServicio,
                                          'intFiltroInicio' => 0,
                                          'intFiltroFin'    => 2
                                         );
            $arrayResultado = $this->getInfoClienteRetiroEquipo($arrayParametrosInfo);
            if ($arrayResultado['strStatus'] == "OK")
            {
                $arrayInformacionServicio = $arrayResultado['arrayData'];
            }
            else
            {
                throw new \Exception("Error al recuperar información del servicio");
            }
           
            $strImagen = $this->strPathTelcos;
           
            if ($strPrefijoEmpresa == "MD")
            {
                $strCodigoEncuesta = "ACT-RET-MD";
                $strImagen        .= 'telcos/web/public/images/logo_netlife_big.jpg';
            }
            else
            {
                $strCodigoEncuesta = "ACT-RET-TN";
                $strImagen        .= 'telcos/web/public/images/logo_telconet_plantilla.jpg';
            }
           
            $objPlantilla = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')->findOneByCodigo($strCodigoEncuesta);
            $strHtml      = $objPlantilla->getPlantilla();
           
            $fileArchivo  = fopen($strPathSource . '/Resources/views/Default/actaRetiroEquipo.html.twig', "w");
 
            if($fileArchivo)
            {
                fwrite($fileArchivo, $strHtml);
                fclose($fileArchivo);
 
                //generar PDF
                $arrayPdf = array(
                                  'strServerRoot'             => $this->strPathTelcos,
                                  'strFecha'                  => $strFecha,
                                  'intIdServicio'             => $intIdServicio,
                                  'strPrefijoEmpresa'         => $strPrefijoEmpresa,
                                  'intCodigo'                 => $intIdServicio,
                                  'strHora'                   => $strHora,
                                  'objServicio'               => $arrayInformacionServicio['objServicio'],
                                  'arrayDatosCliente'         => $arrayInformacionServicio['arrayDatosCliente'],
                                  'arrayFormaContactoPunto'   => $arrayInformacionServicio['arrayFormaContactoPunto'],
                                  'arrayFormaContactoCliente' => $arrayInformacionServicio['arrayFormaContactoCliente'],
                                  'arrayContactoCliente'      => $arrayInformacionServicio['arrayContactoCliente'],
                                  'arrayElementosRetiro'      => $arrayDatosElementos,
                                  'objUltimaMilla'            => $arrayInformacionServicio['objUltimaMilla'],
                                  'strModulo'                 => "planificacion",
                                  'strImagen'                 => $strImagen,
                                  'strObservaciones'          => $arrayParametros['strObservaciones'],
                                  'strNombreEmpleado'         => $arrayParametros['strNombreEmpleado'],
                                  'strCedulaEmpleado'         => $arrayParametros['strCedulaEmpleado'],
                                  'strNombreCliente'          => $arrayParametros['strNombreCliente'],
                                  'strCedulaCliente'          => $arrayParametros['strCedulaCliente'],
                                  'boolFirmaDefault'          => $boolFirmaDefault,
                                  'strRutaFisicaCompleta'     => $strRutaFisicaCompleta,
                                  'strRutaFisicaArchivo'      => $strRutaFisicaArchivo,
                                  'bandNfs'                   => $arrayParametros['bandNfs'],
                                  'strApp'                    => $arrayParametros['strAplicacion'],
                                  'strSubModulo'              => $arrayParametros['strOrigenAccion'],
                                  'idComunicacion'            => $arrayParametros['intIdDetalle'],
                                  'strUsrCreacion'            => $strUsrCreacion
                                 );

                $arrayRespuestaActa = $this->generarPdf($arrayPdf);
               
                //eliminar los archivos de firmas
                unlink($strRutaFisicaArchivo.$strNombreArchivoCliente.'.'.$strExtensionArchivo);
                unlink($strRutaFisicaArchivo.$strNombreArchivoEmpleado.'.'.$strExtensionArchivo);

                if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
                {
                    $strRutaFisicaCompleta = $arrayRespuestaActa['strSrc'];
                }
                //enviar por mail plantilla
                $arrayPlantilla = array(
                                            'strServerRoot'         => $strServerRoot,
                                            'strCodigo'             => $intIdServicio,
                                            'strHora'               => $strHora,
                                            'objPunto'              => $objInfoPunto,
                                            'intIdEmpresa'          => $strCodEmpresa,
                                            'strRutaFisicaCompleta' => $strRutaFisicaCompleta,
                                            'bandNfs'               => $arrayParametros['bandNfs']
                                        );
                $this->enviarPlantilla($arrayPlantilla);
               
                $objTipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')->findOneByExtensionTipoDocumento('PDF');

                $objDocumento = new InfoDocumento();
                $objDocumento->setTipoDocumentoId($objTipoDocumento);
                $objDocumento->setTipoDocumentoGeneralId(8);
                $objDocumento->setNombreDocumento('Retiro Equipo Codigo : ' . $intIdServicio);
                $objDocumento->setUbicacionLogicaDocumento('Retiro_Equipo_RET-EQ-' . $intIdServicio . '-' . $strHora . '.pdf');
                if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
                {
                    $objDocumento->setUbicacionFisicaDocumento($arrayRespuestaActa['strSrc']);
                }
                else
                {
                    $objDocumento->setUbicacionFisicaDocumento($serverRoot . '/'
                                                                . $strRutaFisicaCompleta . '/Acta_EPP_' .
                                                                $intIdComunicacion . '-' . $hora . '.pdf');
                }
                $objDocumento->setEstado('Activo');
                $objDocumento->setEmpresaCod($strCodEmpresa);
                $objDocumento->setFechaDocumento($dateFeCreacion);
                $objDocumento->setUsrCreacion($strUsrCreacion);
                $objDocumento->setFeCreacion($dateFeCreacion);
                $objDocumento->setIpCreacion($strIpCreacion);
                $this->emComunicacion->persist($objDocumento);
                $this->emComunicacion->flush();

                $objDocumentoRelacion = new InfoDocumentoRelacion();
                $objDocumentoRelacion->setDocumentoId($objDocumento->getId());
                $objDocumentoRelacion->setModulo('TECNICO');
                $objDocumentoRelacion->setServicioId($intIdServicio);

                if(is_object($objInfoServicio) && is_object($objInfoPunto))
                {
                    $objDocumentoRelacion->setPuntoId($objInfoPunto->getId());
                    $objPersonaEmpresRol = $objInfoPunto->getPersonaEmpresaRolId();
                    if (is_object($objPersonaEmpresRol))
                    {
                        $objDocumentoRelacion->setPersonaEmpresaRolId($objPersonaEmpresRol->getId());
                    }
                }

                if(isset($arrayParametros['intIdDetalle']) && !empty($arrayParametros['intIdDetalle']))
                {
                    $objDocumentoRelacion->setDetalleId($arrayParametros['intIdDetalle']);
                }

                $objDocumentoRelacion->setEstado('Activo');
                $objDocumentoRelacion->setFeCreacion($dateFeCreacion);
                $objDocumentoRelacion->setUsrCreacion($strUsrCreacion);
                $this->emComunicacion->persist($objDocumentoRelacion);
                $this->emComunicacion->flush();

                $this->emComunicacion->getConnection()->commit();
 
                $strMensaje           = 'Acta de Retiro de equipo procesada correctamente';
                $strStatus            = "OK";
 
                $objDetalleSolicitud  = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($intIdSolicitud);
 
                //se valida si existe entidad detalle solicitud
                if(is_object($objDetalleSolicitud))
                {
                    //FINALIZACIÓN DE TAREA
                    if(isset($arrayParametros['intIdDetalle']) && !empty($arrayParametros['intIdDetalle']))
                    {
                        //Se ingresa el historial de la info_detalle
                        $arrayParametrosHist["intDetalleId"]         = $arrayParametros['intIdDetalle'];
                        $arrayParametrosHist["strObservacion"]       = $objDetalleSolicitud->getObservacion();
                        $arrayParametrosHist["strAccion"]            = "Finalizada";
                        $arrayParametrosHist["strEnviaDepartamento"] = "N";

                        $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

                        //Se ingresa el seguimiento de la tarea
                        $arrayParametrosHist["strObservacion"] = "Tarea fue Finalizada. Obs : " . $objDetalleSolicitud->getObservacion();
                        $arrayParametrosHist["strOpcion"]      = "Seguimiento";

                        $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                    }
                    else
                    {
                        $objDetalle = $this->emSoporte
                                       ->getRepository('schemaBundle:InfoDetalle')
                                       ->findOneByDetalleSolicitudId($objDetalleSolicitud->getId());
                        
                        if(is_object($objDetalle))
                        {
                            //Se ingresa el historial de la info_detalle
                            $arrayParametrosHist["intDetalleId"]         = $objDetalle->getId();
                            $arrayParametrosHist["strObservacion"]       = $objDetalleSolicitud->getObservacion();
                            $arrayParametrosHist["strAccion"]            = "Finalizada";
                            $arrayParametrosHist["strEnviaDepartamento"] = "N";

                            $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

                            //Se ingresa el seguimiento de la tarea
                            $arrayParametrosHist["strObservacion"] = "Tarea fue Finalizada. Obs : " . $objDetalleSolicitud->getObservacion();
                            $arrayParametrosHist["strOpcion"]      = "Seguimiento";

                            $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                        }
                    }

                    //Proceso que graba tarea en INFO_TAREA
                    $arrayParametrosInfoTarea['intDetalleId'] = null;
                    if (isset($arrayParametros['intIdDetalle']))
                    {
                        $arrayParametrosInfoTarea['intDetalleId'] = $arrayParametros['intIdDetalle'];
                    }
                    elseif (is_object($objDetalle))
                    {
                        $arrayParametrosInfoTarea['intDetalleId'] = $objDetalle->getId();
                    }
                    $arrayParametrosInfoTarea['strUsrCreacion'] = $strUsrCreacion;
                    $this->serviceSoporte->crearInfoTarea($arrayParametrosInfoTarea);
                }
                // cierre de if - se valida si existe entidad detalle solicitud
                else
                {
                    $strStatus = "ERROR";
                    $strMensaje = "No existe el detalle de solicitud";
                }
                $arrayRespuestaFinal = array('strStatus' => $strStatus, 'strMensaje' => $strMensaje);
            }
            else
            {
                throw new \Exception("Problema al procesar el archivo, intenta nuevamente");
            }
        }
        catch(\Exception $e)
        {
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }
            $this->emComunicacion->close();
 
            $strStatus = "ERROR";
            $this->serviceUtil->insertError('Telcos+',
                                            'RetiroEquipoService.grabarActaRetiroEquipo',
                                            $e->getMessage(),
                                            $arrayParametros['strUsrCreacion'],
                                            $arrayParametros['strIpCreacion']
                                           );
            $strMensaje          = "Error: Ocurrio un error al generar el acta de retiro de equipos, favor notificar a sistemas!";
            $arrayRespuestaFinal = array('strStatus' => $strStatus, 'strMensaje' => $strMensaje);
        }
        return $arrayRespuestaFinal;
    }
   
    /**
     * getInfoClienteRetiroEquipo
     *
     * Funcion que sirve para obtener los datos necesarios para cargar el acta de entrega del servicio
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 06-01-2017
     
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.1 21-10-2022 - Se agrega validación de ultima milla para los servicios sin ultima milla.
     *
     * @param Array $arrayParametros [
     *                                  - intIdServicio     Identificador de servicio a recuperar información
     *                                  - intFiltroInicio   Numero entero usado como filtro inicial para recuperar información
     *                                  - intFiltroFin      Numero entero usado como filtro final para recuperar información
     *                               ]        
     * @return Array $arrayRespuesta [
     *                                  - arrayData     Array con información del servicio
     *                                               [
     *                                                  - objServicio                 Objeto con información de servicio       
     *                                                  - objPlanCab                  Objeto con información de plan de servicio
     *                                                  - objUltimaMilla              Objeto con información de ultima milla de servicio
     *                                                  - arrayDatosCliente           Array con datos del cliente
     *                                                  - arrayFormaContactoPunto     Array con formas de contacto del punto
     *                                                  - arrayFormaContactoCliente   Array con formas de contacto del cliente
     *                                                  - arrayContactoCliente        Array con contacto del cliente
     *                                               ]
     *                                  - strStatus     Cadena de caracteres que indica el estado de la transaccion ejecutada
     *                               ]
     */
    public function getInfoClienteRetiroEquipo($arrayParametros)
    {
        //inicializar variables de los parametros
        $intIdServicio   = $arrayParametros['intIdServicio'];
        $intFiltroInicio = $arrayParametros['intFiltroInicio'];
        $intFiltroFin    = $arrayParametros['intFiltroFin'];
        $arrayRespuesta  = array();
        try
        {
            $objServicio        = $this->emComercial->find('schemaBundle:InfoServicio', $intIdServicio);
            if (!is_object($objServicio))
            {
                throw new \Exception("Error al recuperar información del servicio");
            }
            $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->findOneBy(array("servicioId" => $intIdServicio));
            if (!is_object($objServicioTecnico))
            {
                throw new \Exception("Error al recuperar información técnica del servicio");
            }
            if($objServicioTecnico->getUltimaMillaId())
            {
                $objUltimaMilla = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                        ->find($objServicioTecnico->getUltimaMillaId());
            }
            else
            {
                $objUltimaMilla = (object) ['nombreTipoMedio' => ''];
            }
            $objPlanCab         = $objServicio->getPlanId();
 
            //obtener datos del cliente-----------------------------------------------------------------------------------------------------------
            $boolEsProducto    = $objServicio->getProductoId()?true:false;
            $arrayDatosCliente = $this->emComercial
                                      ->getRepository('schemaBundle:InfoPersona')
                                      ->getDatosClientePorIdServicio($intIdServicio,$boolEsProducto);
            $intIdPersona      = $arrayDatosCliente['ID_PERSONA'];
 
            //obtener formas contactos del punto--------------------------------------------------------------------------------------------------
            $arrayFormaContactosPunto = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                          ->getFormaContactoPorPunto( $objServicio->getPuntoId()->getId(),
                                                                                      $intFiltroInicio,
                                                                                      $intFiltroFin );
 
            //obtener formas contactos del cliente------------------------------------------------------------------------------------------------
            $arrayFormaContactoCliente = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                           ->getFormaContactoPorCliente($intIdPersona, $intFiltroInicio, $intFiltroFin);
 
            //obtener contacto del cliente--------------------------------------------------------------------------------------------------------
            $arrayContactoCliente = $this->emComercial->getRepository('schemaBundle:InfoPersona')->getContactosPorCliente($intIdPersona);
 
            $arrayInfoActa = array(
                                    'objServicio'               => $objServicio,
                                    'objPlanCab'                => $objPlanCab,
                                    'objUltimaMilla'            => $objUltimaMilla,
                                    'arrayDatosCliente'         => $arrayDatosCliente,
                                    'arrayFormaContactoPunto'   => $arrayFormaContactosPunto,
                                    'arrayFormaContactoCliente' => $arrayFormaContactoCliente,
                                    'arrayContactoCliente'      => $arrayContactoCliente,
                                  );
            $strStatus = "OK";
            $arrayRespuesta = array('strStatus' => $strStatus, 'arrayData' => $arrayInfoActa);
        }
        catch(\Exception $e)
        {
            $strStatus = "ERROR";
            $this->serviceUtil->insertError('Telcos+',
                                            'RetiroEquipoService.getInfoClienteRetiroEquipo',
                                            $e->getMessage(),
                                            $arrayParametros['strUsrCreacion'],
                                            $arrayParametros['strIpCreacion']
                                           );
            $arrayRespuesta = array('strStatus' => $strStatus, 'arrayData' => null);
        }
        return $arrayRespuesta;
    }
   
    /**
     * generarPdf
     *
     * Funcion que sirve para generar pdf de la encuesta llenada por el cliente
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 06-01-2017
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.1 02-06-2020 - Se modifica código para crear nueva estructura de archivos.
     *
     * @author Walther Joao Gaibor <jnazareno@telconet.ec>
     * @version 1.1 12-11-2020 - Almacenar el pdf en el servidor NFS.
     *
     * @param Array $arrayParametros [
     *                                  - strServerRoot              Cadena de caracteres que indica ruta de servidor
     *                                  - strHora                    Cadena de caracteres que indica hora de procesamiento
     *                                  - intIdServicio              Numero entero que indica el identificador del servicio
     *                                  - strModulo                  Cadena de caracteres que indica modulo donde se recuperara plantilla
     *                                  - strImagen                  Cadena de caracteres que indica ruta de imagen usada en pdf
     *                                  - strFecha                   Cadena de caracteres que indica fecha de procesamiento de solicitud
     *                                  - objServicio                Objeto con información del servicio
     *                                  - arrayDatosCliente          Array con datos del cliente
     *                                  - arrayFormaContactoPunto    Array con formas de contacto del punto
     *                                  - arrayFormaContactoCliente  Array con formas de contacto del cliente
     *                                  - arrayContactoCliente       Array con contacto de cliente
     *                                  - objUltimaMilla             Objeto con información de ultima milla del servicio
     *                                  - strNombreEmpleado          Cadena de caracteres que indica nombre del empleado que gestiona retiro
     *                                  - strCedulaEmpleado          Cadena de caracteres que indica cedula del empleado que gestiona retiro
     *                                  - strNombreCliente           Cadena de caracteres que indica nombre del cliente que gestiona retiro
     *                                  - strCedulaCliente           Cadena de caracteres que indica cedula del cliente que gestiona retiro
     *                                  - strObservaciones           Cadena de caracteres que indica observaciones del cliente que gestiona retiro
     *                               ]
     */
    public function generarPdf($arrayParametros)
    {
        $strServerRoot    = $arrayParametros['strServerRoot'];
        $strHora          = $arrayParametros['strHora'];
        $intIdServicio    = $arrayParametros['intIdServicio'];
        $strModulo        = $arrayParametros['strModulo'];
        $strImagen        = $arrayParametros['strImagen'];
        $strDirFirmas     = $strServerRoot . 'telcos/web/' . $arrayParametros['strRutaFisicaArchivo'];
        $strDirDocumentos = $strServerRoot . 'telcos/web/' . $arrayParametros['strRutaFisicaCompleta'] . '/';
        $strFirmaCliente  = $strDirFirmas . $intIdServicio . '_cliente.png';
        $strFirmaEmpleado = $strDirFirmas . $intIdServicio . '_empleado.png';
       
        if(isset($arrayParametros['boolFirmaDefault']) && $arrayParametros['boolFirmaDefault'])
        {
            $strFirmaEmpleado = $strServerRoot . 'telcos/web/public/images/firma_hp.jpg';
            $arrayParametros['strNombreEmpleado'] = "";
            $arrayParametros['strCedulaEmpleado'] = "";
        }
        $arrayPDFCorreo = array(
                                'cuerpo'                => null,
                                'materiales'            => null,
                                'totalMateriales'       => null,
                                'firmaCliente'          => $strFirmaCliente,
                                'firmaEmpleado'         => $strFirmaEmpleado,
                                'fecha'                 => $arrayParametros['strFecha'],
                                'servicio'              => $arrayParametros['objServicio'],
                                'datosCliente'          => $arrayParametros['arrayDatosCliente'],
                                'formaContactoPunto'    => $arrayParametros['arrayFormaContactoPunto'],
                                'formaContactoCliente'  => $arrayParametros['arrayFormaContactoCliente'],
                                'contactoCliente'       => $arrayParametros['arrayContactoCliente'],
                                'elementosRetiro'       => $arrayParametros['arrayElementosRetiro'],
                                'ultimaMilla'           => $arrayParametros['objUltimaMilla'],
                                'observaciones'         => $arrayParametros['strObservaciones'],
                                'elementoCpe'           => null,
                                'elementoOnt'           => null,
                                'elementoWifi'          => null,
                                'macCpe'                => null,
                                'macOnt'                => null,
                                'macWifi'               => null,
                                'imagenCabecera'        => $strImagen,
                                'nombreEmpleado'        => $arrayParametros['strNombreEmpleado'],
                                'cedulaEmpleado'        => $arrayParametros['strCedulaEmpleado'],
                                'nombreCliente'         => $arrayParametros['strNombreCliente'],
                                'cedulaCliente'         => $arrayParametros['strCedulaCliente'],
                                'firmaEmpresa'          => $arrayParametros['boolFirmaDefault']
                               );
 
        $objHtmlPdf = $this->container->get('templating')->render($strModulo.'Bundle:Default:actaRetiroEquipo.html.twig', $arrayPDFCorreo);
        
        if($arrayParametros['bandNfs'])
        {
            $objFile                = $this->container->get('knp_snappy.pdf')->getOutputFromHtml($objHtmlPdf);
            $arrayPathAdicional     = null;
            $strKey                 = isset($arrayParametros['idComunicacion']) ? $arrayParametros['idComunicacion'] : 'SinTarea';
            $arrayPathAdicional[]   = array('key' => $strKey);
            $strNombreArchivo       = 'Retiro_Equipo_RET-EQ-' . $intIdServicio . '-' . $strHora . '.pdf';
            $arrayParamNfs          = array(
                                            'prefijoEmpresa'       => $arrayParametros['strPrefijoEmpresa'],
                                            'strApp'               => $arrayParametros['strApp'],
                                            'strSubModulo'         => $arrayParametros['strSubModulo'],
                                            'arrayPathAdicional'   => $arrayPathAdicional,
                                            'strBase64'            => base64_encode($objFile),
                                            'strNombreArchivo'     => $strNombreArchivo,
                                            'strUsrCreacion'       => $arrayParametros['strUsrCreacion']);
            $arrayRespNfsPdf        = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
            if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
            {
                $arrayParametrosDoc['strSrc']           = $arrayRespNfsPdf['strUrlArchivo'];
            }
            else
            {
                throw new \Exception($arrayRespNfsPdf['strMensaje'].'  -> generarPdf()');
            }
        }
        else
        {
            $this->container->get('knp_snappy.pdf')->generateFromHtml($objHtmlPdf, $strDirDocumentos .
                                                                  'Retiro_Equipo_RET-EQ-' . $intIdServicio .
                                                                  '-' . $strHora . '.pdf');
        }
        return $arrayParametrosDoc;
    }
   
    /**
     * enviarPlantilla
     *
     * Funcion que sirve para enviar la plantilla de la encuesta por mail al cliente
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 10-01-2017
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.1 02-06-2020 - Se modifica código para crear nueva estructura de archivos.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.2 11-11-2020 - Se envia la url del archivo almacenado en el servidor NFS remoto.
     *
     * @param $arrayParametros
     */
    public function enviarPlantilla($arrayParametros)
    {
        $strServerRoot    = $arrayParametros['strServerRoot'];
        $strCodigo        = $arrayParametros['strCodigo'];
        $strHora          = $arrayParametros['strHora'];
        $objPunto         = $arrayParametros['objPunto'];
        $intIdEmpresa     = $arrayParametros['intIdEmpresa'];   
        $strNombreEmpresa = '';
        $strCorreoEmpresa = '';
        $strDirDocumentos = $strServerRoot . '/' . $arrayParametros['strRutaFisicaCompleta'] . '/';
       
        if (!is_object($objPunto))
        {
            throw new \Exception("Error al recuperar información del punto del servicio");
        }
       
        $strCorreos = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')->findCorreosPorPunto($objPunto->getId());
 
        if($strCorreos != '')
        {
            $arrayCorreos          = explode(",", $strCorreos);                  
            $strRutaArchivoAdjunto = $strDirDocumentos . 'Retiro_Equipo_RET-EQ-' . $strCodigo . '-' . $strHora . '.pdf';
            if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
            {
                $strRutaArchivoAdjunto = $arrayParametros['strRutaFisicaCompleta'];
            }
            $objPersona            = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                       ->find($objPunto->getPersonaEmpresaRolId()->getPersonaId()->getId());
 
            if(!is_object($objPersona))
            {
                $arrayParametros['cliente'] = sprintf($objPersona);                   
            }
            else
            {
                $arrayParametros['cliente'] = '';
            }
            if ($intIdEmpresa == '18')
            {
                $strNombreEmpresa = 'NETLIFE';
                $strCorreoEmpresa = 'notificaciones@netlife.net.ec';
            }
            else
            {
                $strNombreEmpresa = 'TELCONET';
                $strCorreoEmpresa = 'notificaciones@telconet.ec';
            }
            $this->serviceEnvioPlantilla->generarEnvioPlantilla($strNombreEmpresa.' te confirma sobre tu requerimiento de finalización de retiro de equipo. '
                                                                . 'Adjunto Acta de Retiro de equipo.',
                                                                $arrayCorreos,
                                                                'RET-EQ-CORREO',
                                                                $arrayParametros,
                                                                $intIdEmpresa,
                                                                '',
                                                                '',
                                                                $strRutaArchivoAdjunto,
                                                                false,
                                                                $strCorreoEmpresa);
        }
    }
}
  

