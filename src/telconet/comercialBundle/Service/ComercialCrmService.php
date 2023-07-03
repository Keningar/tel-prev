<?php

namespace telconet\comercialBundle\Service;

use Doctrine\ORM\EntityManager;
use telconet\schemaBundle\Entity\ReturnResponse;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoPersonaContacto;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\AdmiProductoCaracteristica;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\AdmiCaracteristica;
use telconet\schemaBundle\Entity\AdmiDepartamento;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use Symfony\Component\HttpFoundation\File\File;
class ComercialCrmService
{

    private $emGeneral;
    private $emComercial;
    private $emFinanciero;
    private $emInfraestructura;
    private $emBiFinanciero;
    private $serviceUtil;
    private $serviceTecnico;
    private $serviceInfoServicio;
    private $serviceUtilidades;
    private $serviceJefesComercial;
    private $servicePunto;
    private $serviceCliente;
    private $servicePersonaFormaContacto;
    private $serviceFoxPremium;
    private $serviceLicenciasKaspersky;
    private $serviceInternetProtegido;
    private $serviceCrearServicio;
    private $serviceContrato;
    
    /**
     * Documentación para la función 'setDependencies'.
     *
     * Función encargada de setear los entities manager de los esquemas de base de datos.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 05-08-2019
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $objContainer - objeto contenedor
     *
     */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer )
    {
        $this->emComercial    = $objContainer->get('doctrine.orm.telconet_entity_manager');
        $this->emGeneral      = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
        $this->emFinanciero   = $objContainer->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->emBiFinanciero = $objContainer->get('doctrine.orm.telconet_bifinanciero_entity_manager');
        $this->serviceUtil    = $objContainer->get('schema.Util');
        $this->serviceTecnico = $objContainer->get('tecnico.InfoServicioTecnico');
        $this->strCrmExecute  = $objContainer->getParameter('telcoCrm_execute');
        $this->urlCrmService  = $objContainer->getParameter('ws_telcoCrm_url');
        $this->serviceSoporte = $objContainer->get('soporte.SoporteService');
        $this->serviceProceso = $objContainer->get('soporte.ProcesoService');
        $this->serviceCorreo  = $objContainer->get('soporte.EnvioPlantilla');
        $this->emInfraestructura     = $objContainer->get('doctrine.orm.telconet_infraestructura_entity_manager');
        $this->serviceInfoServicio   = $objContainer->get('comercial.InfoServicio');
        $this->serviceUtilidades     = $objContainer->get('administracion.Utilidades');
        $this->serviceJefesComercial = $objContainer->get('administracion.JefesComercial');
        $this->servicePunto          = $objContainer->get('comercial.InfoPunto');
        $this->serviceCliente        = $objContainer->get('comercial.Cliente');
        $this->servicePersonaFormaContacto = $objContainer->get('comercial.InfopersonaFormaContacto');
        $this->serviceFoxPremium           = $objContainer->get('tecnico.FoxPremium');
        $this->serviceLicenciasKaspersky   = $objContainer->get('tecnico.LicenciasKaspersky');
        $this->serviceInternetProtegido    = $objContainer->get('tecnico.InternetProtegido');
        $this->serviceCrearServicio        = $objContainer->get('comercial.ComercialCrmFlujo');
        $this->serviceContrato             = $objContainer->get('comercial.InfoContrato');
    }

    /**
     * Documentación para la función 'getBasesyMetasVendedor'.
     *
     * Función que retorna las metas o bases de los vendedores.
     *
     * @param array $arrayParametros [
     *                                  "strPrefijoEmpresa"     => Prefijo de la empresa.
     *                                  "strCodEmpresa"         => Código de la empresa.
     *                                  "strDescripcion"        => Descripción que identifica la meta de un vendedor.
     *                                  "strMes"                => Mes valido para la meta del vendedor.
     *                                  "strAnio"               => Año valido para la meta del vendedor.
     *                                  "strEstado"             => Estado de la meta del vendedor.
     *                                  "strGrupoSubgerente"    => Usuario del Subgerente que se desea retornar la meta por los 
     *                                                             vendedores que reportan al mismo.
     *                                  "strVendedor"           => Usuario del vendeddor que se desea retornar la base o meta.
     *                                  "strAplication"         => Nombre de la aplicación.
     *                               ]
     *
     * @return array $arrayResultado [
     *                                 BaseoMetasVendedor =>  arreglo de las bases o metas del o los vendedores.
     *                                 error              =>  mensaje de error.
     *                               ]
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 25-07-2019
     *
     */
    public function getBasesyMetasVendedor($arrayParametros)
    {
        $arrayDescripcion        = array();
        $arrayDescripcion        = $arrayParametros['arrayDescripcion'] ? $arrayParametros['arrayDescripcion']:"";
        $strUsrCreacion          = ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) )
                                    ? $arrayParametros['strUsrCreacion'] : 'TELCOS +';
        $strIpCreacion           = ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) )
                                    ? $arrayParametros['strIpCreacion'] : '127.0.0.1';
        $arrayBaseoMetasVendedor = array();
        $arrayMetasVendedor      = array();
        $arrayBasesVendedor      = array();
        $strMensaje              = '';
        try
        {
            if( isset($arrayDescripcion['METAS']) && (!empty($arrayDescripcion['METAS']) && $arrayDescripcion['METAS'] == 'METAS POR VENDEDOR') )
            {
                $arrayMetasVendedor = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')->getMetasVendedor($arrayParametros);
                if( isset($arrayMetasVendedor['error']) && !empty($arrayMetasVendedor['error']) )
                {
                    $arrayBaseoMetasVendedor = array();
                    throw new \Exception($arrayMetasVendedor['error']);
                }
                else
                {
                    $arrayBaseoMetasVendedor = $arrayMetasVendedor['MetasVendedor'];
                }
            }
            if( isset($arrayDescripcion['BASES']) && (!empty($arrayDescripcion['BASES']) && $arrayDescripcion['BASES'] == 'BASES POR VENDEDOR') )
            {
                $arrayBasesVendedor = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')->getBasesVendedor($arrayParametros);
                if( isset($arrayBasesVendedor['error']) && !empty($arrayBasesVendedor['error']) )
                {
                    $arrayBaseoMetasVendedor = array();
                    throw new \Exception($arrayBasesVendedor['error']);
                }
                else
                {
                    $arrayBaseoMetasVendedor = $arrayBasesVendedor['BaseVendedor'];
                }
            }
            if( (isset($arrayDescripcion['BASES']) && $arrayDescripcion['BASES'] == 'BASES POR VENDEDOR') && 
                (isset($arrayDescripcion['METAS']) && $arrayDescripcion['METAS'] == 'METAS POR VENDEDOR') )
            {
                $arrayBaseoMetasVendedor = array();
                foreach( $arrayBasesVendedor['BaseVendedor'] as $arrayItemBasesVendedor )
                {
                    foreach( $arrayMetasVendedor[MetasVendedor] as $arrayItemMetasVendedor )
                    {
                        if( ($arrayItemMetasVendedor['mesVigente'] == $arrayItemBasesVendedor['mesVigente']) && 
                            ($arrayItemMetasVendedor['vendedor']   == $arrayItemBasesVendedor['vendedor']) )
                        {
                            $arrayBaseyMetas = array(
                                                     'vendedor'    => $arrayItemBasesVendedor['vendedor'],
                                                     'baseMrc'     => $arrayItemBasesVendedor['baseMrc'],
                                                     'metaMrc'     => $arrayItemMetasVendedor['metaMrc'],
                                                     'metaNrc'     => $arrayItemMetasVendedor['metaNrc'],
                                                     'mesVigente'  => $arrayItemBasesVendedor['mesVigente'],
                                                     'anioVigente' => $arrayItemBasesVendedor['anioVigente']
                                                   );
                        }
                    }
                    array_push($arrayBaseoMetasVendedor,$arrayBaseyMetas);
                }
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje ="Falló al retornar las bases o metas del o los vendedores, intente nuevamente.\n ".$ex->getMessage();
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmService.getBasesyMetasVendedor',
                                            $strMensaje,
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        $arrayResultado = array('BaseoMetasVendedor' => $arrayBaseoMetasVendedor,
                                'error'              => $strMensaje);
        return $arrayResultado;
    }
    /**
     * Documentación para la función 'getContactos'.
     *
     * Función que retorna los contactos de un cliente o pre-cliente.
     *
     * @param array $arrayParametros [
     *                                 strPrefijoEmpresa     => Prefijo de la empresa.
     *                                 strCodEmpresa         => Recibe el código de la empresa.
     *                                 strDescripcionTipoRol => Recibe la descripción del tipo de rol.
     *                                 strIdentificacion     => Identificación del cliente.
     *                               ]
     *
     * @return array $arrayResultado [
     *                                 contactos => arreglo de los contactos
     *                                 error     => mensaje de error
     *                               ]
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 25-07-2019
     *
     */
    public function getContactos($arrayParametros)
    {
        $strPrefijoEmpresa       = $arrayParametros['strPrefijoEmpresa'] ? $arrayParametros['strPrefijoEmpresa']:"";
        $strCodEmpresa           = $arrayParametros['strCodEmpresa'] ? $arrayParametros['strCodEmpresa']:"";
        $strDescripcionTipoRol   = $arrayParametros['strDescripcionTipoRol'] ? $arrayParametros['strDescripcionTipoRol']:"";
        $strIdentificacion       = $arrayParametros['strIdentificacion'] ? $arrayParametros['strIdentificacion']:"";
        $strUsrCreacion          = ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) )
                                    ? $arrayParametros['strUsrCreacion'] : 'TELCOS +';
        $strIpCreacion           = ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) )
                                    ? $arrayParametros['strIpCreacion'] : '127.0.0.1';
        $arrayContactos          = array();
        $arrayParametrosContacto = array();
        $strMensaje              = '';
        $strRolCliente           = 'Cliente';
        $strRolPreCliente        = 'Pre-cliente';
        $strEstadoActivo         = 'Activo';
        $intIdPunto              = -1;
        $intPersonaEmpresaClt    = 0;
        $strTipoConsulta         = 'PERSONA';
        try
        {
            if( $strPrefijoEmpresa != 'TN' )
            {
                throw new \Exception('la consulta de contactos solo aplica para Telconet.');
            }
            $objInfoPersona = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                ->findOneBy( array('identificacionCliente' => $strIdentificacion));
            if( !is_object($objInfoPersona) || empty($objInfoPersona) )
            {
                throw new \Exception('Cliente o pre-cliente no registrado en TelcoS+.');
            }
            $arrayParametrosEmpPersona = array('intCodEmpresa'    => $strCodEmpresa,
                                               'intIdPersona'     => $objInfoPersona->getId(),
                                               'strNombreTipoRol' => $strRolCliente,
                                               'estados'          => array('Activo', 'Modificado', 'Pendiente', 'Cancel', 'Cancelado', 'Eliminado')
                                              );
            $arrayPersonaEmpresaRol    = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                           ->getInfoPersonaByCriterios($arrayParametrosEmpPersona);

            if( (isset($arrayPersonaEmpresaRol['idPersonaEmpresaRol']) && empty($arrayPersonaEmpresaRol['idPersonaEmpresaRol']) )
               && is_array($arrayPersonaEmpresaRol))
            {
                $arrayParametrosEmpPersona['strNombreTipoRol'] = $strRolPreCliente;
                $arrayPersonaEmpresaRol                        = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                                                   ->getInfoPersonaByCriterios($arrayParametrosEmpPersona);
            }
            if( (isset($arrayPersonaEmpresaRol['idPersonaEmpresaRol']) && empty($arrayPersonaEmpresaRol['idPersonaEmpresaRol']))
               && is_array($arrayPersonaEmpresaRol) )
            {
                throw new \Exception('El cliente no tiene un rol relacionado con la empresa.');
            }
            $intPersonaEmpresaClt                                 = $arrayPersonaEmpresaRol['idPersonaEmpresaRol'];
            $arrayParametrosContacto['arrayTipoRol']              = ['arrayDescripcionTipoRol' => [$strDescripcionTipoRol]];
            $arrayParametrosContacto['arrayEmpresaRol']           = ['arrayEmpresaCod'         => [$strCodEmpresa]];
            $arrayParametrosContacto['arrayPersonaPuntoContacto'] = ['arrayPersonaEmpresaRol'  => [$intPersonaEmpresaClt],
                                                                        'arrayPunto'           => [$intIdPunto]];
            $arrayParametrosContacto['strGroupBy']                = 'GROUP';

            $objJsonContactos  = $this->emComercial->getRepository('schemaBundle:InfoPersonaContacto')
                                                   ->getJSONContactoClienteByTipoRol($arrayParametrosContacto);
            $objContactos = json_decode($objJsonContactos);

            if( !is_object($objContactos) || empty($objContactos->encontrados) )
            {
                throw new \Exception('No existen contactos relacionados con el cliente.');
            }

            foreach( $objContactos->encontrados as $objItemContactos )
            {
                $arrayFormaContacto      = array();
                $objJsonFormaContacto    = $this->emComercial->getRepository("schemaBundle:InfoPersonaFormaContacto")
                                                             ->getFormasContactoPorPersona($objItemContactos->intIdPersona);
                $objFormaContactoPersona = json_decode($objJsonFormaContacto);

                if( is_object($objFormaContactoPersona) && !empty($objFormaContactoPersona) )
                {
                    foreach( $objFormaContactoPersona->encontrados as $objItemFormaContacto )
                    {
                        $objFormaContacto = $this->emComercial
                                                 ->getRepository("schemaBundle:AdmiFormaContacto")
                                                 ->findOneBy( array('descripcionFormaContacto' => $objItemFormaContacto->descripcionFormaContacto,
                                                                    'estado'                    => $strEstadoActivo
                                                                   )
                                                            );
                        if( is_object($objFormaContacto) && !empty($objFormaContacto) )
                        {
                            $arrayFormaContacto[] = array('formaContactoId' => $objFormaContacto->getId(),
                                                          'descripcion'     => $objItemFormaContacto->descripcionFormaContacto,
                                                          'valor'           => $objItemFormaContacto->valor);
                        }
                    }
                }
                $arrayParametroRolPersona['arrayEmpresaGrupo']    = ['arrayEmpresaGrupo'      => [$strPrefijoEmpresa]];
                $arrayParametroRolPersona['arrayPersonaContacto'] = ['arrayPersona'           => [$objItemContactos->intIdPersona],
                                                                     'arrayEstado'            => [$strEstadoActivo],
                                                                     'arrayPersonaEmpresaRol' => [$intPersonaEmpresaClt],
                                                                     'arrayPunto'             => [$intIdPunto]];
                $arrayParametroRolPersona['strTipoConsulta']      = $strTipoConsulta;
                $objPersonaRolContacto                            = $this->emComercial->getRepository('schemaBundle:InfoPersonaContacto')
                                                                                      ->getRolesPersonaPunto($arrayParametroRolPersona);
                $arrayPersonaRolContacto                             = array();
                if( is_object($objPersonaRolContacto) && !empty($objPersonaRolContacto) )
                {
                    foreach( $objPersonaRolContacto->getRegistros() as $objItemPersonaRolContacto )
                    {
                        $arrayPersonaRolContacto[] = array('intIdRol'               => $objItemPersonaRolContacto['intIdRol'],
                                                           'intIdPersonaEmpresaRol' => $objItemPersonaRolContacto['intIdPersonaEmpresaRol'],
                                                           'strDescripcionRol'      => $objItemPersonaRolContacto['strDescripcionRol'],
                                                           'strUsrCreacion'         => $objItemPersonaRolContacto['strUsrCreacionIPER'],
                                                           'strEstado'              => $objItemPersonaRolContacto['strEstadoIPER']);
                    }
                }
                $arrayContactos[] = array(  'strNombres'                => $objItemContactos->strNombres,
                                            'strApellidos'              => $objItemContactos->strApellidos,
                                            'strIdentificacionCliente'  => $objItemContactos->strIdentificacionCliente,
                                            'intIdPersona'              => $objItemContactos->intIdPersona,
                                            'dateFeCreacion'            => $objItemContactos->dateFeCreacion->date,
                                            'strUsuarioCreacion'        => $objItemContactos->strUsrCreacion,
                                            'strTipoContacto'           => $objItemContactos->strDescripcionRol,
                                            'strEstado'                 => $objItemContactos->strEstado,
                                            'intIdPersonaEmpresaRol'    => $objItemContactos->intIdPersonaEmpresaRol,
                                            'intIdTitulo'               => $objItemContactos->intIdTitulo,
                                            'strTitulo'                 => $objItemContactos->strDescripcionTitulo,
                                            'arrayFormaContacto'        => $arrayFormaContacto,
                                            'arrayDescripcionContacto'  => $arrayPersonaRolContacto);
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje ="Falló al retornar los contactos, intente nuevamente.\n". $ex->getMessage();
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmService.getContactos',
                                            $strMensaje,
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        $arrayResultado = array('contactos' => $arrayContactos,
                                'error'     => $strMensaje);
        return $arrayResultado;
    }
    /**
     * Documentación para la función 'getPersonaCRM'.
     *
     * Función que retorna los clientes para el ingreso de cuentas en TelcoCRM.
     *
     * @param array $arrayParametros [
     *                                  "strPrefijoEmpresa"     => Prefijo de la empresa.
     *                                  "strCodEmpresa"         => Código de la empresa.
     *                                  "strIdentificacion"     => Identificación del cliente a buscar.
     *                               ]
     *
     * @return array $arrayResultado [
     *                                 "infoCliente"        =>  arreglo de la información del cliente.
     *                                 "infoVendedores"     =>  vendedores asociados al punto.
     *                                 "strCltDistribuidor" =>  información del cliente del distribuidor.
     *                                 "status"             =>  estado de la petición.
     *                                 "error"              =>  mensaje de error.
     *                               ]
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 10-06-2020
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.1 28-05-2021 - Se agrega validación que no permite el ingreso de un cliente de un distribuidor en TelcoCRM.
     *
     */
    public function getPersonaCRM($arrayParametros)
    {
        $strPrefijoEmpresa       = $arrayParametros['strPrefijoEmpresa'] ? $arrayParametros['strPrefijoEmpresa']:"";
        $strCodEmpresa           = $arrayParametros['strCodEmpresa'] ? $arrayParametros['strCodEmpresa']:"";
        $strIdentificacion       = $arrayParametros['strIdentificacion'] ? $arrayParametros['strIdentificacion']:"";
        $strUsrCreacion          = ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) )
                                    ? $arrayParametros['strUsrCreacion'] : 'TELCOS +';
        $strIpCreacion           = ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) )
                                    ? $arrayParametros['strIpCreacion'] : '127.0.0.1';
        $strMensaje              = '';
        $arrayInformacionCliente = array();
        $strStatus               = 200;
        $strCltDistribuidor      = "";
        try
        {
            if(empty($strPrefijoEmpresa) || empty($strCodEmpresa) || empty($strIdentificacion))
            {
                throw new \Exception("El prefijo, código y la identificación son obligatorios para realizar la búsqueda del cliente.");
            }
            if($strPrefijoEmpresa != 'TN')
            {
                throw new \Exception("La consulta de clientes o pre-clientes, solo aplica para Telconet");
            }
            $arrayDistribuidor = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                   ->getDistribuidor(array("strIdentificacion" => $strIdentificacion,
                                                                           "strPrefijoEmpresa" => $strPrefijoEmpresa));
            if(empty($arrayDistribuidor["error"]) && isset($arrayDistribuidor["resultado"]) && 
                !empty($arrayDistribuidor["resultado"]))
            {
                foreach($arrayDistribuidor["resultado"] as $arrayItem)
                {
                    if($arrayItem["intCantidadServ"] > 0 && $arrayItem["intCantidadSolAprobada"] == 0)
                    {
                        $strCltDistribuidor = (!empty($arrayItem["strDistribuidor"]) && isset($arrayItem["strDistribuidor"]))?
                                               $arrayItem["strDistribuidor"]:"";
                    }
                }
            }
            $arrayParametrosCliente  = array('strPrefijoEmpresa' => $strPrefijoEmpresa,
                                             'strCodEmpresa'     => $strCodEmpresa,
                                             'strIdentificacion' => $strIdentificacion);
            $arrayInformacionCliente = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                         ->getPersonaCRM($arrayParametrosCliente);
            if( isset($arrayInformacionCliente['error']) && !empty($arrayInformacionCliente['error']) )
            {
                throw new \Exception($arrayInformacionCliente['error']);
            }
            if(empty($arrayInformacionCliente['infoCliente']) || $arrayInformacionCliente['infoCliente'] == "")
            {
                $strStatus = 206;
            }
        }
        catch(\Exception $ex)
        {
            $strStatus = 400;
            $strMensaje ="Falló al retornar información del cliente, intente nuevamente.\n ".$ex->getMessage();
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmService.getPersonaCRM',
                                            $strMensaje,
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        $arrayResultado = array('infoCliente'        => $arrayInformacionCliente['infoCliente'],
                                'infoVendedores'     => $arrayInformacionCliente['infoVendedores'],
                                'strCltDistribuidor' => $strCltDistribuidor,
                                'status'             => $strStatus,
                                'error'              => $strMensaje);
        return $arrayResultado;
    }

    /**
     * Documentación para la función 'editEstadoServicioProyectoTelcos'.
     *
     * Función encargada de editar el estado de las característica del servicio 
     * asociada al proyecto, en TelcoS+.
     *
     * @param array $arrayParametros [
     *                                  "strPrefijoEmpresa"     => Prefijo de la empresa.
     *                                  "strCodEmpresa"         => Código de la empresa.
     *                                  "arrayValor"            => Identificación de los valores 
     *                                                             de la característica del servicio.
     *                               ]
     *
     * @return array $arrayResultado [
     *                                 "resultado"      =>  mensaje de confirmación.
     *                                 "status"         =>  estado de la petición.
     *                                 "error"          =>  mensaje de error.
     *                               ]
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 17-07-2020
     *
     */
    public function editEstadoServicioProyectoTelcos($arrayParametros)
    {
        $strPrefijoEmpresa = $arrayParametros['strPrefijoEmpresa'] ? $arrayParametros['strPrefijoEmpresa']:"";
        $strCodEmpresa     = $arrayParametros['strCodEmpresa'] ? $arrayParametros['strCodEmpresa']:"";
        $arrayValor        = $arrayParametros['arrayValor'] ? $arrayParametros['arrayValor']:"";
        $strUsrCreacion    = ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) )
                               ? $arrayParametros['strUsrCreacion'] : 'TELCOS +';
        $strIpCreacion     = ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) )
                               ? $arrayParametros['strIpCreacion'] : '127.0.0.1';
        $strMensaje        = "";
        $strStatus         = 200;
        $strObservacion    = "Se canceló el proyecto desde TelcoCRM.";
        try
        {
            if( $strPrefijoEmpresa != "TN" )
            {
                throw new \Exception("La consulta solo aplica para Telconet");
            }

            $objAdmiCaractIdProyecto = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                         ->findOneBy(array("descripcionCaracteristica" => 'NOMBRE_PROYECTO',
                                                                           "estado"                    => "Activo"));
            if( !empty($objAdmiCaractIdProyecto) && is_object($objAdmiCaractIdProyecto) )
            {
                $arrayDescripcionCaracteristica [] = $objAdmiCaractIdProyecto->getDescripcionCaracteristica();
                $arrayIdCaracteristicas[]          = $objAdmiCaractIdProyecto->getId();
            }

            $objAdmiCaractNombreProyecto = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                             ->findOneBy(array("descripcionCaracteristica" => 'ID_PROYECTO',
                                                                               "estado"                    => "Activo"));
            if( !empty($objAdmiCaractNombreProyecto) && is_object($objAdmiCaractNombreProyecto) )
            {
                $arrayDescripcionCaracteristica [] = $objAdmiCaractNombreProyecto->getDescripcionCaracteristica();
                $arrayIdCaracteristicas[]          = $objAdmiCaractNombreProyecto->getId();
            }
            $arrayParametrosServicio  = array('strPrefijoEmpresa'              => $strPrefijoEmpresa,
                                              'strCodEmpresa'                  => $strCodEmpresa,
                                              'arrayValor'                     => $arrayValor,
                                              'arrayDescripcionCaracteristica' => $arrayDescripcionCaracteristica);
            $arrayServicio = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                               ->getServicioPorCaracteristica($arrayParametrosServicio);
            if( isset($arrayServicio['error']) && !empty($arrayServicio['error']) )
            {
                throw new \Exception($arrayParametrosServicio['error']);
            }
            $this->emComercial->getConnection()->beginTransaction();
            if( (!empty($arrayServicio) && is_array($arrayServicio))
                && (!empty($arrayServicio["registros"]) && isset($arrayServicio["registros"])) )
            {
                foreach($arrayServicio["registros"] as $arrayItem)
                {
                    $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                     ->findOneBy(array('id'=>$arrayItem["ID_SERVICIO"]));
                    if( !empty($objServicio) && is_object($objServicio) )
                    {
                        $objServicioHist = new InfoServicioHistorial();
                        $objServicioHist->setServicioId($objServicio);
                        $objServicioHist->setObservacion($strObservacion);
                        $objServicioHist->setIpCreacion($strIpCreacion);
                        $objServicioHist->setUsrCreacion($strUsrCreacion);
                        $objServicioHist->setFeCreacion(new \DateTime('now'));
                        $objServicioHist->setEstado($objServicio->getEstado());
                        $this->emComercial->persist($objServicioHist);
                        $this->emComercial->flush();
                        for($intContador=0;$intContador<count($arrayIdCaracteristicas);$intContador++)
                        {
                            $objProdCaract = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                               ->findOneBy(array("productoId"       => $objServicio->getProductoId(),
                                                                                 "caracteristicaId" => $arrayIdCaracteristicas[$intContador]));
                            if( !empty($objProdCaract) && is_object($objProdCaract) )
                            {
                                $arrayParametrosServicio   = array("servicioId"                => $objServicio->getId(),
                                                                   "productoCaracterisiticaId" => $objProdCaract->getId());
                                $objInfoServicioProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                               ->findOneBy($arrayParametrosServicio);
                            }
                            if( !empty($objInfoServicioProdCaract) && is_object($objInfoServicioProdCaract) )
                            {
                                $objInfoServicioProdCaract->setEstado("Cancelado");
                                $objInfoServicioProdCaract->setFeUltMod(new \DateTime('now'));
                                $objInfoServicioProdCaract->setUsrUltMod($strUsrCreacion);
                                $this->emComercial->persist($objInfoServicioProdCaract);
                                $this->emComercial->flush();
                            }
                        }
                    }
                }
            }
            if( $this->emComercial->getConnection()->isTransactionActive() )
            {
                $strMensaje = "Se actualizó el estado correctamente.";
                $this->emComercial->getConnection()->commit();
                $this->emComercial->getConnection()->close();
            }
        }
        catch(\Exception $ex)
        {
            if( $this->emComercial->getConnection()->isTransactionActive() )
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }
            $strStatus       = 400;
            $strMensajeError ="Falló al actualizar el estado del servicio, intente nuevamente.\n ".$ex->getMessage();
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmService.editEstadoServicioProyectoTelcos',
                                            $strMensajeError,
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        $arrayResultado = array('resultado' => $strMensaje,
                                'status'    => $strStatus,
                                'error'     => $strMensajeError);
        return $arrayResultado;
    }

    /**
     * Documentación para la función 'getRequestCRM'.
     *
     * Función que retorna las metas o bases de los vendedores.
     *
     * @param array $arrayParametros [
     *                                  "strPrefijoEmpresa"     => Prefijo de la empresa.
     *                                  "strCodEmpresa"         => Código de la empresa.
     *                                  "strFuncion"            => Nombre de la función a ejecutar en TelcoCRM.
     *                                  "arrayParametrosCRM"    =>[
     *                                                              Parámetros necesarios para la función de TelcoCRM.
     *                                                            ]
     *                               ]
     *
     * @return array $arrayResultado [
     *                                 resultadoCRM       =>  arreglo de la respuesta de la función de TelcoCRM.
     *                                 error              =>  mensaje de error.
     *                               ]
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 13-04-2020
     *
     */
    public function getRequestCRM($arrayParametros)
    {
        try
        {
            $strUsrCreacion      = ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) )
                                   ? $arrayParametros['strUsrCreacion'] : 'TELCOS +';
            $strIpCreacion       = ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) )
                                   ? $arrayParametros['strIpCreacion'] : '127.0.0.1';
            $strFuncion          = $arrayParametros['strFuncion'] ? $arrayParametros['strFuncion']:"";
            $strOp               = $arrayParametros['strOp'] ? $arrayParametros['strOp']:"";
            $arrayParametrosRest = $arrayParametros['arrayParametrosCRM'] ? $arrayParametros['arrayParametrosCRM']:"";
            $arrayResultadoCRM   = array();
            if((!is_array($arrayParametrosRest) || empty($arrayParametrosRest)) ||empty($strFuncion))
            {
                throw new \Exception('Parámetros incompletos.');
            }
            if($this->strCrmExecute === "S")
            {
                $objCurl = curl_init($this->urlCrmService);
                curl_setopt($objCurl, CURLOPT_RETURNTRANSFER, 1);

                $arrayParametros = array("strOp"              => $strOp,
                                         "arrayParametrosCRM" => $arrayParametrosRest);
                $arrayParametrosCRM = array(
                                            "method" => $strFuncion,
                                            "input_type" => "JSON",
                                            "response_type" => "JSON",
                                            "rest_data" => json_encode(array("arrayParametros"=>$arrayParametros)),
                                        );

                curl_setopt($objCurl, CURLOPT_POSTFIELDS, $arrayParametrosCRM);
                curl_setopt($objCurl, CURLOPT_CONNECTTIMEOUT, 0); 
                curl_setopt($objCurl, CURLOPT_TIMEOUT, 0);

                $arrayResultado = curl_exec($objCurl);
                curl_close($objCurl);

                $objResultado  = json_decode($arrayResultado);
                if((!empty($objResultado)&&is_object($objResultado)) &&
                    ($objResultado->status == "200" && !empty($objResultado->message)))
                {
                    $arrayResultadoCRM = $objResultado->message;
                }
                else
                {
                    throw new \Exception($objResultado->message);
                }
            }
        }
        catch(\Exception $ex)

        {
            $strMensaje ="Falló la comunicación entre TelcoS+ y TelcoCRM.\n ".$ex->getMessage();
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmService.getRequestCRM',
                                            $strMensaje,
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        $arrayResultado = array('resultado' => $arrayResultadoCRM,
                                'error'     => $strMensaje);
        return $arrayResultado;
    }
    
    /**
     * Documentación para la función getHolding, permite obtener el historial de un servicio.
     *
     * @param array $arrayParametros [
     *                                 $strUsrCreacion     => Usuario de consulta.
     *                                 $strIpCreacion      => Ip de consulta.
     *                                 $strEmpresa         => Empresa a consultar.
     *                               ]
     * @return array $arrayResultado
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 25-10-2020
     */
    public function getHolding($arrayParametros)
    {
        try
        {
            $strUsrCreacion      = ( isset($arrayParametros['strLogin']) && !empty($arrayParametros['strLogin']) )
                                   ? $arrayParametros['strLogin'] : 'TELCOS +';
            $strIpCreacion       = ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) )
                                   ? $arrayParametros['strIpCreacion'] : '127.0.0.1';
            $strEmpresa          = ( isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']) )
                                   ? $arrayParametros['strCodEmpresa'] : '10';
            $strDescripcion      = 'HOLDING DE EMPRESAS';
            $strMensajeError     = "";
            $strStatus           = 200;
            $arrayHoldings       = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                 ->get($strDescripcion,
                                                                          '',
                                                                          '',
                                                                          '',
                                                                          '',
                                                                          '',
                                                                          $strUsrCreacion,
                                                                          '',
                                                                          '',
                                                                          $strEmpresa);
            if(is_array($arrayHoldings) && !empty($arrayHoldings))
            {
                foreach($arrayHoldings as $arrayItem)
                {
                    if(isset($arrayItem['id']) && !empty($arrayItem['id']) 
                        && isset($arrayItem['valor1']) && !empty($arrayItem['valor1'])
                            && isset($arrayItem['estado']) && !empty($arrayItem['estado']) && $arrayItem['estado']== 'Activo')
                    {
                        $arrayHolding[] = array('intId'     => $arrayItem['id'],
                                                'strNombre' => $arrayItem['valor1']);
                    }
                }
            }
            
        }
        catch (\Exception $ex) 
        {
            $strMensajeError = "Falló la comunicación entre TelcoS+ y TelcoCRM.\n ".$ex->getMessage();
            $strStatus       = 400;
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmService.getHolding',
                                            $strMensajeError,
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        $arrayResultado = array('resultado' => $arrayHolding,
                                'status'    => $strStatus,
                                'error'     => $strMensajeError);
        return $arrayResultado;
    }

   /**
     * Documentación para la función 'putCrearTareaTelcoCRM'.
     *
     * Función que invoca al web services de crear tarea.
     *
     * @param array $arrayParametros [
     *                                "prefijo_empresa"      => Prefijo de la empresa,
     *                                "cod_empresa"          => Código de la empresa,
     *                                "loginAsignado"        => Login de la persona a quién se le va a crear la tarea,
     *                                "punto"                => Coordenada del punto,
     *                                "usrCreacion"          => Usuario quien crea la tarea,
     *                                "nombreProceso"        => Nombre del proceso,
     *                                "nombreTarea"          => Nombre de la tarea,
     *                                "fechaSolicitada"      => Fecha solicitada para la ejecución de la tarea,
     *                                "horaSolicitada"       => Hora de la tarea,
     *                                "prefijo_empresa"      => Prefijo de la empresa,
     *                                "observacion"          => Observación de la tarea a crear,
     *                                "esAutomatico"         => Valor boleano para iniciar la tarea
     *                                "file"                 => Archivo en base64,
     *                                "fileName"             => Nombre del archivo,
     *                                "fileExtension"        => Extensión del archivo,
     *                               ]
     *
     * @return Array $arrayRespuesta.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 25-12-2020
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 15-07-2021 - Se modifica el método para obtener el correo de las
     *                           asistentes del vendedor y el correo del vendedor.
     */
    public function putCrearTareaTelcoCRM($arrayParametros)
    {
        try
        {
            $strMensajeError   = "";
            $strUsrCreacion    = "TelcoCRM+";
            $strIpCreacion     = "127.0.0.1";
            $arrayTarea        = array();
            $arrayRespuesta    = array();
            $arrayItemTarea    = array();
            $strStatus         = 200;

            if(empty($arrayParametros) || !is_array($arrayParametros))
            {
                throw new \Exception("Datos incompletos para realizar la tarea.");
            }

            foreach($arrayParametros as $arrayItem)
            {
                if(!isset($arrayItem['prefijo_empresa']) || empty($arrayItem['prefijo_empresa']))
                {
                    throw new \Exception("La variable prefijo_empresa es un campo obligatorio.");
                }
                if(!isset($arrayItem['cod_empresa']) || empty($arrayItem['cod_empresa']))
                {
                    throw new \Exception("La variable cod_empresa es un campo obligatorio.");
                }
                if(!isset($arrayItem['loginAsignado']) || empty($arrayItem['loginAsignado']))
                {
                    throw new \Exception("La variable loginAsignado es un campo obligatorio.");
                }
                if(!isset($arrayItem['punto']) || empty($arrayItem['punto']))
                {
                    throw new \Exception("La variable punto es un campo obligatorio.");
                }
                if(!isset($arrayItem['usrCreacion']) || empty($arrayItem['usrCreacion']))
                {
                    throw new \Exception("La variable UsrCreacion es un campo obligatorio.");
                }
                $strUsrCreacion = $arrayItem["usrCreacion"];
                if(!isset($arrayItem['nombreProceso']) || empty($arrayItem['nombreProceso']))
                {
                    throw new \Exception("El nombre del proceso es un campo obligatorio.");
                }
                if(!isset($arrayItem['nombreTarea']) || empty($arrayItem['nombreTarea']))
                {
                    throw new \Exception("El nombre de la tarea es un campo obligatorio.");
                }
                if(!isset($arrayItem['horaSolicitada'])  || empty($arrayItem['horaSolicitada']) ||
                    !isset($arrayItem['fechaSolicitada']) || empty($arrayItem['fechaSolicitada']))
                {
                    throw new \Exception('La horaSolicitada y(o) fechaSolicitada es un campo obligatorio');
                }
                $arrayFecha = explode('-', $arrayItem['fechaSolicitada']);
                if(count($arrayFecha) !== 3 || !checkdate($arrayFecha[1], $arrayFecha[2], $arrayFecha[0]))
                {
                    throw new \Exception('El Formato de fecha Inválido');
                }
                if(strtotime($arrayItem['horaSolicitada']) === false)
                {
                    throw new \Exception('El Formato de hora Inválido');
                }
                $arrayDepartamentos = $this->emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                        ->getDepartamentosPorLogin(array("strLogin"              => $arrayItem['loginAsignado'],
                                                                                         "intIdEmpresa"          => $arrayItem["cod_empresa"],
                                                                                         "strEstadoDepartamento" => "Activo"));
                if(empty($arrayDepartamentos['registros']) || !is_array($arrayDepartamentos))
                {
                    throw new \Exception('No se encontró el departamento del login Asignado');
                }
                $arrayItemDepartamentos = $arrayDepartamentos['registros'];
                $strDepartamento        = $arrayItemDepartamentos[0]["NOMBRE_DEPARTAMENTO"];
                $objInfoPersona         = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                               ->findOneByLogin($strUsrCreacion);
                if(!is_object($objInfoPersona) || !in_array($objInfoPersona->getEstado(), array('Activo','Pendiente','Modificado')))
                {
                    throw new \Exception('El usuario de creación no existe en telcos o no se encuentra Activo.');
                }
                $strUsuarioAsigna  = $objInfoPersona->getNombres()." ".$objInfoPersona->getApellidos();
                $arrayDatosPersona = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                       ->getInfoDatosPersona(array ('strRol'                     => 'Empleado',
                                                                                    'strPrefijo'                 => $arrayItem['prefijo_empresa'],
                                                                                    'strEstadoPersona'           => array('Activo',
                                                                                                                          'Pendiente',
                                                                                                                          'Modificado'),
                                                                                    'strEstadoPersonaEmpresaRol' => 'Activo',
                                                                                    'strDepartamento'            => $strDepartamento,
                                                                                    'strLogin'                   => $arrayItem['loginAsignado']));
                if($arrayDatosPersona['status'] === 'fail')
                {
                    throw new \Exception('Error al obtener los datos del asignado, por favor comunicar a Sistemas.');
                }
                if($arrayDatosPersona['status'] === 'ok' && empty($arrayDatosPersona['result']))
                {
                    throw new \Exception('Los filtros para encontrar al empleado asignado son incorrectos '.
                                'o el empleado no existe en telcos');
                }
            }
            foreach ($arrayParametros as $arrayItem)
            {
                $arrayTo          = array();
                $strLoginUsuario  = $arrayItem["usrCreacion"];
                $strLoginVendedor = $arrayItem["usrLoginVendedor"];
                $strCodEmpresa    = $arrayItem["cod_empresa"];

                //Obtenemos el correo del usuario creador de la tarea.
                $strCorreo = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                        ->getMailNaf(array('strLogin' => $strLoginUsuario,
                                           'intIdEmp' => $strCodEmpresa));

                if (!empty($strCorreo))
                {
                  $arrayTo[] = $strCorreo;
                }

                //Obtenemos el cargo del usuario creado de la tarea.
                $arrayCargoPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                        ->getCargosPersonas($strLoginUsuario);

                if (!empty($arrayCargoPersona))
                {
                    if ($arrayCargoPersona[0]['STRCARGOPERSONAL'] === "ASISTENTE")
                    {
                        //Obtenemos el correo del usuario vendedor.
                        $strCorreo = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                ->getMailNaf(array('strLogin' => $strLoginVendedor,
                                                   'intIdEmp' => $strCodEmpresa));

                        if (!empty($strCorreo))
                        {
                          $arrayTo[] = $strCorreo;
                        }
                    }

                    if ($arrayCargoPersona[0]['STRCARGOPERSONAL'] === "VENDEDOR")
                    {
                        //Obtenemos el listado de las asistentes del vendedor para posterior obtener el correo.
                        $arrayAsistentes = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                ->getAsistentePorVendedores(array("strLoginUsuario" => $strLoginUsuario,
                                                                  "strCodEmpresa"   => $strCodEmpresa));

                        foreach ($arrayAsistentes["asistente"] as $arrayAsistente)
                        {
                            $strCorreo = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                    ->getMailNaf(array('strLogin' => $arrayAsistente["login"],
                                                       'intIdEmp' => $arrayItem['cod_empresa']));

                            if (!empty($strCorreo))
                            {
                              $arrayTo[] = $strCorreo;
                            }
                        }
                    }
                }

                $strUsrCreacion     = $arrayItem["usrCreacion"] ? $arrayItem["usrCreacion"]:"TelcoCRM";
                $strIpCreacion      = $arrayItem["ipCreacion"]  ? $arrayItem["ipCreacion"]:"127.0.0.1";
                $objInfoPersona     = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                        ->findOneByLogin($strUsrCreacion);
                $strUsuarioAsigna   = $objInfoPersona->getNombres()." ".$objInfoPersona->getApellidos();
                $arrayDepartamentos = $this->emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                        ->getDepartamentosPorLogin(array("strLogin"              => $arrayItem['loginAsignado'],
                                                                                         "intIdEmpresa"          => $arrayItem["cod_empresa"],
                                                                                         "strEstadoDepartamento" => "Activo"));
                $arrayItemDepartamentos = $arrayDepartamentos['registros'];
                $strDepartamento        = $arrayItemDepartamentos[0]["NOMBRE_DEPARTAMENTO"];
                $arrayParametrosPersona = array ('strRol'                     => 'Empleado',
                                                 'strPrefijo'                 => $arrayItem['prefijo_empresa'],
                                                 'strEstadoPersona'           => array('Activo',
                                                                                       'Pendiente',
                                                                                       'Modificado'),
                                                 'strEstadoPersonaEmpresaRol' => 'Activo',
                                                 'strDepartamento'            => $strDepartamento,
                                                 'strLogin'                   => $arrayItem['loginAsignado']);
                $arrayDatosPersona      = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                            ->getInfoDatosPersona($arrayParametrosPersona);
                $arrayParametrosTarea   = array('intIdPersonaEmpresaRol' => $arrayDatosPersona['result'][0]['idPersonaEmpresaRol'],
                                                'intIdEmpresa'           => $arrayDatosPersona['result'][0]['idEmpresa'],
                                                'strPrefijoEmpresa'      => $arrayDatosPersona['result'][0]['prefijoEmpresa'],
                                                'strNombreTarea'         => $arrayItem['nombreTarea'],
                                                'strNombreProceso'       => $arrayItem['nombreProceso'],
                                                'strObservacionTarea'    => $arrayItem['observacion'],
                                                'strMotivoTarea'         => $arrayItem['observacion'],
                                                'strTipoAsignacion'      => 'empleado',
                                                'strIniciarTarea'        => $arrayItem['esAutomatico'],
                                                'strTipoTarea'           => 'T',
                                                'strTareaRapida'         => 'N',
                                                'strFechaHoraSolicitada' => $arrayItem['fechaSolicitada'].' '.$arrayItem['horaSolicitada'],
                                                'boolAsignarTarea'       => true,
                                                'arrayTo'                => $arrayTo,
                                                'strAplicacion'          => 'TelcoCRM',
                                                'strUsuarioAsigna'       => $strUsuarioAsigna,
                                                'strUserCreacion'        => $strUsrCreacion,
                                                'strIpCreacion'          => $strIpCreacion);
                $arrayRespuestaTarea = $this->serviceSoporte->crearTareaCasoSoporte($arrayParametrosTarea);
                if($arrayRespuestaTarea['mensaje'] === 'fail')
                {
                    throw new \Exception('Error al crear la tarea, por favor comunicar a Sistemas.');
                }
                if(!empty($arrayRespuestaTarea['numeroTarea']) && isset($arrayRespuestaTarea['numeroTarea']))
                {
                    $arrayItemTarea = array("punto"         => $arrayItem['punto'],
                                            "numeroTarea"   => $arrayRespuestaTarea['numeroTarea'],
                                            "numeroDetalle" => $arrayRespuestaTarea['numeroDetalle']);
                    if(!empty($arrayItem['documentoAdjunto'])&&isset($arrayItem['documentoAdjunto'])
                       && $arrayItem['documentoAdjunto'] == "S")
                    {
                        $this->serviceProceso->putFile(array('strFileBase64'     => $arrayItem["file"],
                                                             'strFileName'       => $arrayItem["fileName"],
                                                             'strFileExtension'  => $arrayItem["fileExtension"],
                                                             'intNumeroTarea'    => $arrayRespuestaTarea['numeroTarea'],
                                                             'strOrigen'         => "t",
                                                             'strPrefijoEmpresa' => $arrayItem['prefijo_empresa'],
                                                             'strUsuario'        => $strUsrCreacion,
                                                             'strIp'             => $strIpCreacion));
                    }
                    array_push($arrayTarea,$arrayItemTarea);
                }
            }
        }
        catch(\Exception $e)
        {
            $strMensajeError = "Ocurrió un error al crear la tarea, por favor comuníquese con el departamento de Sistemas.";
            $strStatus       = 400;
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmService.putCrearTareaTelcoCRM',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        $arrayRespuesta['status']            = $strStatus;
        $arrayRespuesta['arrayTarea']        = $arrayTarea;
        $arrayRespuesta['strMensajeError']   = $strMensajeError;
        return $arrayRespuesta;
    }

   /**
     * Documentación para la función 'putNotificacionCotizacionTelcoCRM'.
     *
     * Función que envía notificación cuando se crea una cotización desde TelcoCRM.
     *
     * @param array $arrayParametros [
     *                                  "strUsrCreacion"       => Usuario quien crea la tarea,
     *                                  "strIpCreacion"        => Ip del usuario en sesión,
     *                                  "strVendedor"          => Login del vendedor,
     *                                  "strNombreClt"         => Nombre del cliente,
     *                                  "strIdentificacion"    => Identificación del cliente,
     *                                  "strNombreCot"         => Nombre de la cotización,
     *                                  "strCodigo"            => Código de la cotización,
     *                                  "strNombrePro"         => Nombre de la propuesta,
     *                                  "strCodEmpresa"        => Código de la empresa,
     *                                  "arrayCategoria"       => Arreglo de las líneas de negocio,
     *                                  "strLoginGP"           => Login del Gerente de Producto,
     *                               ]
     *
     * @return Array $arrayRespuesta.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 25-12-2020
     *
     */
    public function putNotificacionCotizacionTelcoCRM($arrayParametros)
    {
        try
        {
            $strUsrCreacion      = ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) )
                                   ? $arrayParametros['strUsrCreacion'] : 'TELCOS +';
            $strIpCreacion       = ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) )
                                   ? $arrayParametros['strIpCreacion'] : '127.0.0.1';
            $strVendedor         = ( isset($arrayParametros['strVendedor']) && !empty($arrayParametros['strVendedor']) )
                                   ? $arrayParametros['strVendedor'] : '';
            $strNombreClt        = ( isset($arrayParametros['strNombreClt']) && !empty($arrayParametros['strNombreClt']) )
                                   ? $arrayParametros['strNombreClt'] : '';
            $strIdentificacion   = ( isset($arrayParametros['strIdentificacion']) && !empty($arrayParametros['strIdentificacion']) )
                                   ? $arrayParametros['strIdentificacion'] : '';
            $strNombreCot        = ( isset($arrayParametros['strNombreCot']) && !empty($arrayParametros['strNombreCot']) )
                                   ? $arrayParametros['strNombreCot'] : '';
            $strCodigo           = ( isset($arrayParametros['strCodigo']) && !empty($arrayParametros['strCodigo']) )
                                   ? $arrayParametros['strCodigo'] : '';
            $strNombrePro        = ( isset($arrayParametros['strNombrePro']) && !empty($arrayParametros['strNombrePro']) )
                                   ? $arrayParametros['strNombrePro'] : '';
            $strCodEmpresa       = ( isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']) )
                                   ? $arrayParametros['strCodEmpresa'] : '';
            $arrayCategoria      = ( isset($arrayParametros['arrayCategoria']) && !empty($arrayParametros['arrayCategoria']) )
                                   ? $arrayParametros['arrayCategoria'] : '';
            $strLoginGP          = ( isset($arrayParametros['strLoginGP']) && !empty($arrayParametros['strLoginGP']) )
                                   ? $arrayParametros['strLoginGP'] : '';
            $objPersonaGP        = $this->emComercial->getRepository('schemaBundle:InfoPersona')->findOneBy(array('login'=>$strLoginGP));
            $strJefeProducto     = (is_object($objPersonaGP) && !empty($objPersonaGP)) 
                                   ? $objPersonaGP->getNombres().' '.$objPersonaGP->getApellidos() :'';
            $objPersonaVendedor  = $this->emComercial->getRepository('schemaBundle:InfoPersona')->findOneBy(array('login'=>$strVendedor));
            $strNombreVendedor   = (is_object($objPersonaVendedor) && !empty($objPersonaVendedor)) 
                                   ? $objPersonaVendedor->getNombres().' '.$objPersonaVendedor->getApellidos() :'';
            $strStatus           = 200;
            $strMensajeError     = "";
            $arrayCorreos        = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                     ->getContactosByLoginPersonaAndFormaContacto($strLoginGP,
                                                                                                 'Correo Electronico');
            if(!empty($arrayCorreos) && is_array($arrayCorreos))
            {
                foreach($arrayCorreos as $arrayItem)
                {
                    if(!empty($arrayItem['valor']) && isset($arrayItem['valor']))
                    {
                        $arrayDestinatarios[] = $arrayItem['valor'];
                    }
                }
            }
            if(!empty($arrayCategoria) && is_array($arrayCategoria))
            {
                foreach($arrayCategoria as $arrayItemCategoria)
                {
                    $arrayCorreoLineaNegocio = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                 ->get('PARAMETROS_TELCOCRM',
                                                                       'COMERCIAL', 
                                                                       '', 
                                                                       'CORREO_POR_LINEA_NEGOCIO',
                                                                       strtolower($arrayItemCategoria),
                                                                       '', 
                                                                       '', 
                                                                       '', 
                                                                       '',
                                                                       $strCodEmpresa);
                    if(!empty($arrayCorreoLineaNegocio) && is_array($arrayCorreoLineaNegocio))
                    {
                        foreach($arrayCorreoLineaNegocio as $arrayItem)
                        {
                            if(!empty($arrayItem['valor2']) && isset($arrayItem['valor2']))
                            {
                                $arrayDestinatarios[] = $arrayItem['valor2'];
                            }
                        }
                    }
                }
            }
            $arrayResultadoCorreo    = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                         ->getSubgerentePorLoginVendedor(array("strLogin"=>$strVendedor));
            if(!empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]))
            {
                $arrayRegistrosCorreo = $arrayResultadoCorreo['registros'];
                $arrayCorreos         = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                             ->getContactosByLoginPersonaAndFormaContacto($arrayRegistrosCorreo[0]["LOGIN_SUBGERENTE"],
                                                                                          'Correo Electronico');
                if(!empty($arrayCorreos) && is_array($arrayCorreos))
                {
                    foreach($arrayCorreos as $arrayItem)
                    {
                        if(!empty($arrayItem['valor']) && isset($arrayItem['valor']))
                        {
                            $arrayDestinatarios[] = $arrayItem['valor'];
                        }
                    }
                }
                $arrayCorreos = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                     ->getContactosByLoginPersonaAndFormaContacto($arrayRegistrosCorreo[0]["LOGIN_VENDEDOR"],
                                                                                  'Correo Electronico');
                if(!empty($arrayCorreos) && is_array($arrayCorreos))
                {
                    foreach($arrayCorreos as $arrayItem)
                    {
                        if(!empty($arrayItem['valor']) && isset($arrayItem['valor']))
                        {
                            $arrayDestinatarios[] = $arrayItem['valor'];
                        }
                    }
                }
            }
            $arrayParametrosMail  = array("strNombreCot"             => ucwords(strtolower($strNombreCot)),
                                          "strCodigo"                => ucwords(strtolower($strCodigo)),
                                          "strJefeProducto"          => ucwords(strtolower($strJefeProducto)),
                                          "strNombrePro"             => ucwords(strtolower($strNombrePro)),
                                          "strNombreVendedor"        => ucwords(strtolower($strNombreVendedor)),
                                          "strNombreCliente"         => ucwords(strtolower($strNombreClt)),
                                          "strIdentificacionCliente" => ucwords(strtolower($strIdentificacion)));
            $this->serviceCorreo->generarEnvioPlantilla("NUEVA COTIZACION TELCOCRM", 
                                                        'mdleon@telconet.ec',//array_unique($arrayDestinatarios), 
                                                        'NUEVACOTIZACION', 
                                                        $arrayParametrosMail,
                                                        $strCodEmpresa,
                                                        '',
                                                        '',
                                                        null, 
                                                        true,
                                                        'notificaciones_telcos@telconet.ec');
        }
        catch(\Exception $e)
        {
            $strMensajeError = "Ocurrió un error al enviar notificación, por favor comuníquese con el departamento de Sistemas.";
            $strStatus       = 400;
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmService.putNotificacionCotizacionTelcoCRM',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        $arrayRespuesta['status']            = $strStatus;
        $arrayRespuesta['strMensajeError']   = $strMensajeError;
        return $arrayRespuesta;
    }
    
    /**
     * Documentación para la función getDatosPuntoServicio, permite obtener la relacion del producto entre Crm y Telcos y retorna las caracteristicas.
     *
     * @param array $arrayParametros [
     *                                 opConsulta         => Acción a realizar.
     *                                 $arrayParametrosWs => Datos para tratamiento.
     *                               ]
     * @return array $arrayResultado
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 08-04-2021
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.1 08-04-2022 - Se agrega funcionalidad para pedidos automaticos.
     */
    public function getDatosPuntoServicio($arrayParametrosWs)
    {
        $strOperacion   = ( isset($arrayParametrosWs['opConsulta']) && !empty($arrayParametrosWs['opConsulta']) )
                                   ? $arrayParametrosWs['opConsulta'] : '';
        try
        {
            if(!empty($strOperacion))
            {
               switch($strOperacion)
               {
                    case 'ConsultaNegocioUbicacion':
                        $arrayResponse["PuntosCob"]    = $this->servicePunto->obtenerPuntosCobertura($arrayParametrosWs['strCodEmpresa']);
                        $arrayResponse['negocio']      = $this->servicePunto->obtenerTiposNegocio($arrayParametrosWs['strCodEmpresa']);
                        $arrayResponse['ubicacion']    = $this->servicePunto->obtenerTiposUbicacion();
                        $arrayResponse['FormaContacto']= $this->serviceCrearServicio->getFormasContacto();
                        $arrayResponse['vendedores']   = $this->serviceCrearServicio->getComboVendedores($arrayParametrosWs);
                        $arrayResponse['Puntos']       = $this->serviceCrearServicio->getPuntosByCliente($arrayParametrosWs);
                        $arrayResponse['ciudades'] = $this->serviceCrearServicio->listaCiudades($arrayParametrosWs);
                        break;
                    case 'ConsultaPuntoCobertura':
                        $arrayResponse["arrayDatos"] = $this->servicePunto->obtenerPuntosCobertura($arrayParametrosWs['strCodEmpresa']);
                        break;
                    case 'ConsultaCanton':
                        $arrayResponse['canton'] = $this->servicePunto->obtenerCantonesJurisdiccion($arrayParametrosWs['intIdJurisdicion']);
                        break;
                    case 'ConsultaParroquia':
                        $arrayResponse['parroquia'] = $this->serviceCliente->obtenerParroquiasCanton($arrayParametrosWs['intIdCanton']);
                        $arrayResponse['login'] = $this->serviceCrearServicio->getLoginPunto($arrayParametrosWs);
                        break;
                    case 'ConsultaSector':
                        $arrayResponse['sector'] = $this->serviceCliente->obtenerSectoresParroquia($arrayParametrosWs['strCodEmpresa'],
                                                                                         $arrayParametrosWs['intIdParroquia']);
                        break;
                    case 'ValidaContacto':
                        $arrayResponse['ValidaContacto'] = $this->servicePersonaFormaContacto->validarFormasContactos($arrayParametrosWs);
                        break;  
                    case 'ListadoPuntos':
                        $arrayResponse['Puntos']       = $this->serviceCrearServicio->getPuntosByCliente($arrayParametrosWs);
                        break;
                    case 'ListadoProductos':
                        $arrayResponse['Servicios'] = $this->serviceCrearServicio->getListaServicio($arrayParametrosWs);
                        break;
                    case 'ValidaLogin':
                        $arrayResponse['login'] = $this->servicePunto->validarLogin($arrayParametrosWs['strLogin']);
                        break;
                    case 'CreaPunto':
                        $arrayResponse['Punto'] = $this->serviceCrearServicio->creaPunto($arrayParametrosWs);
                        break;
                    case 'CaracteristicasProd':
                        $arrayResponse['caracteristicas'] = $this->serviceCrearServicio->getCaracteristicasProducto($arrayParametrosWs);
                        $arrayResponse['listadoCaract']   = $this->serviceInfoServicio->listarCaracteristicasPorProducto($arrayParametrosWs);
                        break;
                    case 'CrearServicio':
                        $arrayResponse['servicios'] = $this->serviceCrearServicio->crearServicio($arrayParametrosWs);
                        $arrayResponse['Contrato'] = $this->serviceContrato->validaContrato($arrayParametrosWs);
                        break;
                    case 'ListarCiudades':
                        $arrayResponse['ciudades'] = $this->serviceCrearServicio->listaCiudades($arrayParametrosWs);
                        break;
                    case 'ListarParroquias':
                        $arrayResponse['parroquias'] = $this->serviceCrearServicio->listaParroquias($arrayParametrosWs);
                        break;
                    case 'ListarSectores':
                        $arrayResponse['sectores'] = $this->serviceCrearServicio->listaSectores($arrayParametrosWs);
                        break;
                    case 'NodosEdificios':
                        $arrayResponse['Edificios'] = $this->serviceCrearServicio->nodosClientes($arrayParametrosWs);
                        break;
                    case 'ValidaContrato':
                        $arrayResponse['Contrato'] = $this->serviceContrato->validaContrato($arrayParametrosWs);
                        break;
                    case 'VerificaPedido':
                        $arrayResponse['Pedidos'] = $this->pedidoByServicio($arrayParametrosWs);
                        break;
                    default:
                        $arrayResponse['status']  = $this->status['METODO'];
                        $arrayResponse['message'] = $this->mensaje['METODO'];
               }
            }
        }
        catch (\Exception $ex) 
        {
            $strMensajeError = "Falló la comunicación entre TelcoS+ y TelcoCRM.\n ".$ex->getMessage();
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmService.getCaracteristicasProducto',
                                            $strMensajeError,
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        $arrayResultado = $arrayResponse;
        return $arrayResultado;
    }
    
    /**
     * Documentación para el método pedidoByServicio
     *
     * Funcion que devuelve los id pedidos de las caracteristicas de los servicios.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 28-05-2022
     *
     * @param Array $arrayDatosWs[
     *                                  "strNombre"         => nombre de la parroquia.
     *                                  "intIdParroquia"    => Id de la parroquia.
     *                                  "intCodEmpresa"     => codigo de la empresa
     *                               ]
     *
     * @return array $arrayResultado 
     */
    public function pedidoByServicio($arrayDatosWs)
    {
        $arrayServicios           = ( isset($arrayDatosWs['arrayServicios']) && !empty($arrayDatosWs['arrayServicios']) )
                                   ? $arrayDatosWs['arrayServicios'] : '';
        $strCaracteristica        = ( isset($arrayDatosWs['strCaracteristica']) && !empty($arrayDatosWs['strCaracteristica']) )
                                   ? $arrayDatosWs['strCaracteristica'] : 'PEDIDO_ID';
        $arrayPedidos             = array();
        if(!empty($arrayServicios))
        {
           foreach($arrayServicios as $arrayServicio)
           {
               $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                     ->findOneBy(array('id'=>$arrayServicio["ID_SERVICIO"]));
               if( !empty($objServicio) && is_object($objServicio) )
               {
                    $objServicioProdCarct = $this->serviceInfoServicio->getValorCaracteristicaServicio(array('objServicio' => $objServicio,
                                                                                 'strNombreCaracteristica' => $strCaracteristica));
                    if(is_object($objServicioProdCarct))
                    {
                        $arrayPedido = array('IdServicio' => $objServicioProdCarct->getServicioId(),
                                              'PedidoId'   => $objServicioProdCarct->getValor());
                        array_push($arrayPedidos,$arrayPedido);
                    }
               }
           } 
        }
        return $arrayPedidos;	
    }
    
}
