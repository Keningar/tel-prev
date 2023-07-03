<?php

namespace telconet\administracionBundle\Service;

use telconet\administracionBundle\Service\UtilidadesService;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;

/**
 * Clase JefesComercialService
 *
 * Clase que maneja las funcionales necesarias para la administración de los jefes del área comercial
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 22-10-2015
 */    
class JefesComercialService 
{
    const PARAMETRO_GRUPO_ROLES_PERSONAL         = 'GRUPO_ROLES_PERSONAL';
    const CARACTERISTICA_CARGO_GERENTE_PRODUCTO = 'CARGO_GERENTE_PRODUCTO';
    const CARACTERISTICA_CARGO                  = 'CARGO';
    const ESTADO_ACTIVO                         = 'Activo';
    const ESTADO_ELIMINADO                      = 'Eliminado';
    const VALOR_SUPERVISOR                      = 'Supervisor';
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emComercial;
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emGeneral;
    
    /**
     * @var \telconet\administracionBundle\Service\UtilidadesService
     */
    private $serviceUtilidades;
    
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;


    /**
     * setDependencies
     *
     * Método que agrega las dependencias usadas en el service                                   
     *      
     * @param ContainerInterface $container
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 18-04-2017 - Se agrega la variable '$this->emGeneral' que contiene al entity manager del esquema 'DB_GENERAL'
     * @since 1.0
     */
    public function setDependencies( \Symfony\Component\DependencyInjection\ContainerInterface $container )
    {
        $this->container         = $container;
        $this->emComercial       = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emGeneral         = $container->get('doctrine.orm.telconet_general_entity_manager');
        $this->serviceUtilidades = $container->get('administracion.Utilidades');
    }
    
        
    /**
     * getListadoEmpleados
     *
     * Método que retorna el listado del personal del área comercial junto con sus metas Activas y Brutas                                   
     *      
     * @param array $arrayParametros  ['usuario', 'esJefe', 'departamento', 'empresa', 'exceptoUsr', 'noAsignados', 'asignadosA',
     *                                 'jefeConCargo', 'inicio', 'limite', 'sinCuadrilla', 'intIdCuadrilla', 'criterios', 'nombreArea',
     *                                 'rolesNoIncluidos', 'estadoActivo', 'caracteristicaCargo', 'metaBruta', 'metaActiva', 'strPrefijoEmpresa']
     * 
     * @return array $arrayResultados ['total', 'usuarios']
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 22-10-2015
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 27-10-2015 - Se adapta cambia la función para que ahora reciban el valor de cargo, meta bruta y meta activa,
     *                           como un arreglo
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 17-11-2015 - Se modifica para enviar en la consulta el cargo del empleado asignado desde el NAF
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.6 13-03-2017 - Se envía el parámetro 'strPrefijoEmpresa' a la función 'getValorCaracteristica' para que realice las validaciones
     *                           correspondientes dependiendo de la empresa en sessión. Si tiene marcado como Cargo en TELCOS+ de 'Empleado' no lo
     *                           considere como Jefe. 
     *                           Se obtiene mediante la variable '$intIdCargoTelcos' el id del cargo que tiene asignado en TELCOS+.
     *                           Se verifica si el empleado tiene asociado la caracteristica 'CARGO_GERENTE_PRODUCTO' para obtener el nombre del
     *                           grupo del producto al cual ha sido asociado dicho empleado como Gerente de Producto.
     *                           Se verifica si existen más cargos como Jefe que se puedan asignar en TELCOS+ para mostrar la opción de 'asignarJefe'
     *                           para ello se crea un contador '$intTotalCargosJefes' con el cual sabremos si el usuario puede seguir siendo asignado
     *                           como Jefe.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 18-05-2017 - Se quita validación de que el campo sea requerido (SI) cuando se verifica si existen cargos de jefes (ES_JEFE) que
     *                           se pueden habilitar al personal.
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.4 10-12-2018 - Se agrega validación para saber si es asistente en caso de que lo sea se podrá asignar vendedores.
     */
    public function getListadoEmpleados($arrayParametros)
    {
        $objRequest      = $this->container->get('request');
        $objSession      = $objRequest->getSession();
        $strUsrCreacion  = $objSession->get('user') ? $objSession->get('user') : '';
        $strSoloAsis     = $arrayParametros['strSoloAsis'] ? $arrayParametros['strSoloAsis'] : "N";
        $arrayUsuarios   = array();
        $arrayResultados = array();
        
        $arrayTmpPersonas = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                 ->findPersonalByCriterios($arrayParametros);

        $arrayRegistros           = $arrayTmpPersonas['registros'];
        $arrayResultados['total'] = $arrayTmpPersonas['total'];
        
        if( $arrayRegistros )
        {
            $this->emComercial->getConnection()->beginTransaction();
            
            try
            {
                foreach ($arrayRegistros as $arrayDatos)
                {
                    $boolEsJefe       = 'N';
                    $boolEsSupervisor = 'N';
                    $strEsAsistente   = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa'])
                    && $arrayParametros['strPrefijoEmpresa'] == "TN") ? "S" : "N";

                    $idPersonaEmpresaRolEmpleado = $arrayDatos['idPersonaEmpresaRol'];
                    $strUsuario                  = ucwords(strtolower(trim($arrayDatos['nombres']))).' '.
                                                   ucwords(strtolower(trim($arrayDatos['apellidos'])));//Obtener el nombre del empleado
                    $intMetaActivaValor          = 0;
                    $intTotalEmpleadosAsignados  = 0;
                    $objInfoPersonaEmpresaRol    = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                     ->findOneById($idPersonaEmpresaRolEmpleado);

                    //Obtener el nombre del Jefe a quien reporta el Empleado
                    $strIdReportaA = $arrayDatos['reportaPersonaEmpresaRolId'];
                    $strReportaA   = $this->serviceUtilidades
                                          ->getNombreReportaA( array('reportaPersonaEmpresaRolId' => $strIdReportaA) );
                    //Fin Obtener el nombre del Jefe a quien reporta el Empleado


                    $strPrefijoEmpresa = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) ) 
                                         ? $arrayParametros['strPrefijoEmpresa'] : '';
                    $strCodEmpresa     = ( isset($arrayParametros['empresa']) && !empty($arrayParametros['empresa']) ) ? $arrayParametros['empresa']
                                         : '';
                    $strIpCreacion     = ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) )
                                         ? $arrayParametros['strIpCreacion'] : '127.0.0.1';
                        
                    $arrayParametrosCaracteristicas = array(
                                                                'estado'              => $arrayParametros['estadoActivo'],
                                                                'area'                => $arrayParametros['nombreArea'],
                                                                'esJefe'              => $arrayDatos['esJefe'],
                                                                'idPersonaEmpresaRol' => $idPersonaEmpresaRolEmpleado,
                                                                'strPrefijoEmpresa'   => $strPrefijoEmpresa
                                                            );

                    //Obtener el nombre del cargo del Empleado asignado desde el NAF
                    $strCargoNaf = $this->emComercial->getRepository('schemaBundle:AdmiRol')
                                                     ->getRolEmpleadoEmpresa( array('usuario' => $idPersonaEmpresaRolEmpleado) );

                    $intPos = strpos($strCargoNaf, 'Supervi');

                    if( $intPos === 0 )
                    {
                        $boolEsSupervisor = 'S';
                    }
                    //Fin Obtener el nombre del cargo del Empleado  asignado desde el NAF

                    $intPosAsist = strpos(strtolower($strCargoNaf), 'asist');
                    if( $intPosAsist === false )
                    {
                        $strEsAsistente = 'N';
                    }
                    //Obtener el nombre del cargo del Empleado guardado a través del Telcos
                    $strTipoCaracteristicaConsultar                       = ( $strPrefijoEmpresa == 'TN' ) ? 'CargoGrupoRolesPersonal' : 'Cargo';
                    $arrayParametrosCaracteristicas['caracteristica']     = $arrayParametros['caracteristicaCargo'];
                    $arrayParametrosCaracteristicas['tipoCaracteristica'] = $strTipoCaracteristicaConsultar;

                    $arrayTmpResultado = $this->serviceUtilidades->getValorCaracteristica( $arrayParametrosCaracteristicas );
                    $strCargoTelcos    = ( isset($arrayTmpResultado['valor']) ) ? $arrayTmpResultado['valor'] : '';
                    $intIdCargoTelcos  = ( isset($arrayTmpResultado['intIdValor']) && !empty($arrayTmpResultado['intIdValor']) ) 
                                         ? $arrayTmpResultado['intIdValor'] : 0;

                    if( $boolEsSupervisor == 'S' && $strCargoTelcos == 'Jefe' )
                    {
                        $objCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                               ->findOneBy(
                                                                               array(
                                                                                        'descripcionCaracteristica' => self::CARACTERISTICA_CARGO,
                                                                                        'estado'                    => self::ESTADO_ACTIVO
                                                                                    ) 
                                                                           );

                        $arrayParametros = array( 
                                                    'estado'              => self::ESTADO_ACTIVO,
                                                    'personaEmpresaRolId' => $idPersonaEmpresaRolEmpleado,
                                                    'caracteristicaId'    => $objCaracteristica
                                                );

                        $entityPersonaEmpresaRolCarac = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                          ->findOneBy($arrayParametros);

                        if( $entityPersonaEmpresaRolCarac )
                        {
                            $entityPersonaEmpresaRolCarac->setFeUltMod(new \DateTime('now'));
                            $entityPersonaEmpresaRolCarac->setUsrUltMod($strUsrCreacion);
                            $entityPersonaEmpresaRolCarac->setEstado(self::ESTADO_ELIMINADO);

                            $this->emComercial->persist($entityPersonaEmpresaRolCarac);
                            $this->emComercial->flush();
                        }//( $entityPersonaEmpresaRolCarac )
                        
                        
                        $entityPersonaEmpresaRolCaracNew = new InfoPersonaEmpresaRolCarac();
                        $entityPersonaEmpresaRolCaracNew->setEstado(self::ESTADO_ACTIVO);
                        $entityPersonaEmpresaRolCaracNew->setFeCreacion(new \DateTime('now'));
                        $entityPersonaEmpresaRolCaracNew->setIpCreacion($objRequest->getClientIp());
                        $entityPersonaEmpresaRolCaracNew->setUsrCreacion($strUsrCreacion);
                        $entityPersonaEmpresaRolCaracNew->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                        $entityPersonaEmpresaRolCaracNew->setCaracteristicaId($objCaracteristica);
                        $entityPersonaEmpresaRolCaracNew->setValor(self::VALOR_SUPERVISOR);

                        $this->emComercial->persist($entityPersonaEmpresaRolCaracNew);
                        $this->emComercial->flush();
                        
                        $strCargoTelcos = 'Supervisor';
                    }//( $boolEsSupervisor == 'S' && $strCargoTelcos == 'Jefe' )
                    //Fin Obtener el nombre del cargo del Empleado guardado a través del Telcos 
                    
                    
                    //Obtener el valor de la meta bruta asignada a el Empleado
                    $arrayParametrosCaracteristicas['caracteristica']     = isset($arrayParametros['metaBruta'])?$arrayParametros['metaBruta']:'';
                    $arrayParametrosCaracteristicas['tipoCaracteristica'] = 'Meta';

                    $arrayTmpResultado = $this->serviceUtilidades->getValorCaracteristica( $arrayParametrosCaracteristicas );
                    $strMetaBruta      = ( isset($arrayTmpResultado['valor']) ) ? $arrayTmpResultado['valor'] : '';
                    //Fin Obtener el valor de la meta bruta asignada a el Empleado


                    //Obtener el valor de la meta activa asignada a el Empleado
                    $arrayParametrosCaracteristicas['caracteristica'] = isset($arrayParametros['metaActiva'])?$arrayParametros['metaActiva']:'';

                    $arrayTmpResultado = $this->serviceUtilidades->getValorCaracteristica( $arrayParametrosCaracteristicas );
                    $strMetaActiva     = ( isset($arrayTmpResultado['valor']) ) ? $arrayTmpResultado['valor'] : '';

                    if( $strMetaActiva )
                    {
                        $intMetaActivaValor = round( (intval($strMetaBruta) * intval($strMetaActiva)) / 100 );
                    }
                    //Fin Obtener el valor de la meta activa asignada a el Empleado

                    
                    /**
                     * BLOQUE QUE VERIFICA SI EL CARGO EN TELCOS ES DE JEFE
                     */
                    $arrayParametrosRolesNoJefes = array( 'strCodEmpresa'     => $strCodEmpresa,
                                                          'strValorRetornar'  => 'descripcion',
                                                          'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                          'strNombreModulo'   => 'COMERCIAL',
                                                          'strNombreCabecera' => 'ROLES_TELCOS_NO_SON_JEFES',
                                                          'strUsrCreacion'    => $strUsrCreacion,
                                                          'strIpCreacion'     => $strIpCreacion);
                
                    $arrayResultadosRolesNoJefes = $this->serviceUtilidades->getDetallesParametrizables($arrayParametrosRolesNoJefes);

                    if( isset($arrayResultadosRolesNoJefes['resultado']) && !empty($arrayResultadosRolesNoJefes['resultado']) )
                    {
                        $arrayResultadosRolesTelcosNoJefes = $arrayResultadosRolesNoJefes['resultado'];

                        if( !in_array($strCargoTelcos, $arrayResultadosRolesTelcosNoJefes) )
                        {
                            $boolEsJefe = 'S';
                        }//( !in_array($strCargoTelcos, $arrayResultadosRolesTelcosNoJefes) )
                    }//( isset($arrayResultadosRolesNoJefes['resultado']) && !empty($arrayResultadosRolesNoJefes['resultado']) )
                    
                    
                    /**
                     * BLOQUE QUE VERIFICA SI EL CARGO EN TELCOS SE DEBE HABILITAR COMO JEFE
                     */
                    $boolHabilitarComoJefe             = 'N';
                    $arrayParametrosRolesHabilitarJefe = array( 'strCodEmpresa'     => $strCodEmpresa,
                                                                'strValorRetornar'  => 'descripcion',
                                                                'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                                'strNombreModulo'   => 'COMERCIAL',
                                                                'strNombreCabecera' => 'ROL_HABILITAR_COMO_JEFE',
                                                                'strUsrCreacion'    => $strUsrCreacion,
                                                                'strIpCreacion'     => $strIpCreacion);
                
                    $arrayResultadosRolesHabilitarJefe = $this->serviceUtilidades->getDetallesParametrizables($arrayParametrosRolesHabilitarJefe);

                    if( isset($arrayResultadosRolesHabilitarJefe['resultado']) && !empty($arrayResultadosRolesHabilitarJefe['resultado']) )
                    {
                        $arrayResultadosRolesTelcosHabilitarJefe = $arrayResultadosRolesHabilitarJefe['resultado'];

                        if( in_array($strCargoTelcos, $arrayResultadosRolesTelcosHabilitarJefe) )
                        {
                            $boolHabilitarComoJefe = 'S';
                        }//( in_array($strCargoTelcos, $arrayResultadosRolesTelcosHabilitarJefe) )
                    }//( isset($arrayResultadosRolesHabilitarJefe['resultado']) && !empty($arrayResultadosRolesHabilitarJefe['resultado']) )
                    
                    
                    /**
                     * Bloque que verifica si existen más cargos para asignar como JEFE
                     */
                    $intTotalCargosJefes = 0;
                    $objAdmiParametroCab = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                           ->findOneBy( array( 'nombreParametro' => self::PARAMETRO_GRUPO_ROLES_PERSONAL,
                                                                               'estado'          => self::ESTADO_ACTIVO ) );
                    
                    if( is_object($objAdmiParametroCab) )
                    {
                        $intIdParametroCargo = $objAdmiParametroCab->getId();
                        
                        if( $intIdParametroCargo > 0 )
                        {
                            $arrayCargosResultados = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getParametrosByCriterios( array('estado'      => self::ESTADO_ACTIVO,
                                                                                            'parametroId' => $intIdParametroCargo,
                                                                                            'valor4'      => 'ES_JEFE') );

                            if( isset($arrayCargosResultados['registros']) && !empty($arrayCargosResultados['registros']) )
                            {
                                foreach($arrayCargosResultados['registros'] as $arrayCargo)
                                {
                                    $strDescripcionCargo = ucwords(strtolower($arrayCargo['descripcion']));

                                    if( $strDescripcionCargo != $strCargoTelcos )
                                    {
                                        $intTotalCargosJefes++;
                                    }//( $strDescripcionCargo != $strCargoTelcos )
                                }//foreach($arrayResultados['registros'] as $arrayCargo)
                            }//( isset($arrayResultados['registros']) && !empty($arrayResultados['registros']) )
                        }//( $intIdParametroCargo > 0 )
                    }//( is_object($objAdmiParametroCab) )


                    //Saber si tiene empleados asignados
                    if( $boolEsJefe == 'S' )
                    {
                        $arrayTmpParametros = array(
                                                        'usuario'      => $arrayParametros['usuario'],
                                                        'departamento' => $arrayParametros['departamento'],
                                                        'empresa'      => $arrayParametros['empresa'],
                                                        'asignadosA'   => $idPersonaEmpresaRolEmpleado
                                                    );

                        $arrayEmpleadosAsignados = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                     ->findPersonalByCriterios($arrayTmpParametros);

                        $intTotalEmpleadosAsignados = $arrayEmpleadosAsignados['total'];
                    }//( $boolEsJefe == 'S' )
                    //Fin Saber si tiene empleados asignados
                    
                    
                    /**
                     * Bloque que verifica si el empleado tiene asociado la caracteristica 'CARGO_GERENTE_PRODUCTO' para obtener el nombre del grupo
                     * del producto al cual ha sido asociado dicho empleado como Gerente de Producto.
                     */
                    $strGrupoProductoAsociado            = '';
                    $arrayCaracteristicasGerenteProducto = array( 'descripcionCaracteristica' => self::CARACTERISTICA_CARGO_GERENTE_PRODUCTO,
                                                                  'estado'                    => self::ESTADO_ACTIVO );
                    
                    $objCaracteristicaProducto = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                      ->findOneBy( $arrayCaracteristicasGerenteProducto );
                    
                    if( is_object($objCaracteristicaProducto) )
                    {
                        $arrayParametrosGrupoProducto = array( 'estado'              => self::ESTADO_ACTIVO,
                                                               'personaEmpresaRolId' => $idPersonaEmpresaRolEmpleado,
                                                               'caracteristicaId'    => $objCaracteristicaProducto );
                        
                        $objPersonaEmpresaRolCarac = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                       ->findOneBy($arrayParametrosGrupoProducto);
                        
                        if( is_object($objPersonaEmpresaRolCarac) )
                        {
                            $strGrupoProductoAsociado = $objPersonaEmpresaRolCarac->getValor() ? $objPersonaEmpresaRolCarac->getValor() : '';
                        }//( is_object($objPersonaEmpresaRolCarac) )
                    }//( is_object($objCaracteristicaProducto) )

                    $strTiempoLimite = $arrayDatos['strTiempoLimite'] ? $arrayDatos['strTiempoLimite']:'' ;
                    if( !empty($strTiempoLimite) )
                    {
                        $strTiempoLimite = date_format(date_create($strTiempoLimite), 'Y-m-d');
                        $strTiempoLimite = ((strtotime($strTiempoLimite) - strtotime(date('Y-m-d')))/86400);
                    }
                    else
                    {
                        $objAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                   ->findOneBy(array( 'descripcionCaracteristica' => 'ASISTENTE_POR_CARGO',
                                                                               'estado'                    => 'Activo' ));
                        $entityEmpleado = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($idPersonaEmpresaRolEmpleado);
                        $arrayParametrosVend = array( 'estado'           => 'Activo',
                                                      'valor'            => $entityEmpleado->getPersonaIdValor(),
                                                      'caracteristicaId' => $objAdmiCaracteristica);
                        $arrayInfoPersonaEmpresaRolCarac = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                             ->findBy($arrayParametrosVend, 
                                                                                       array('feCreacion'       => 'ASC'));
                        if( count($arrayInfoPersonaEmpresaRolCarac) >1 && $arrayInfoPersonaEmpresaRolCarac[0]->getPersonaEmpresaRolId()->getId() != $arrayParametros['asistentesDe'] )
                        {
                            $strTiempoLimite = 'Asignar N° de días';
                        }
                    }
                    $arrayUsuarios[] = array(
                                                'intIdPersonaEmpresaRol'     => $idPersonaEmpresaRolEmpleado,
                                                'strEmpleado'                => $strUsuario,
                                                'strReportaA'                => $strReportaA,
                                                'strIdReportaA'              => $strIdReportaA,
                                                'strCargo'                   => $strCargoTelcos,
                                                'strMetaActiva'              => $strMetaActiva,
                                                'intMetaActivaValor'         => $intMetaActivaValor,
                                                'strMetaBruta'               => $strMetaBruta,
                                                'intTotalEmpleadosAsignados' => $intTotalEmpleadosAsignados,
                                                'boolEsJefe'                 => $boolEsJefe,
                                                'strCargoNaf'                => $strCargoNaf,
                                                'boolEsSupervisor'           => $boolEsSupervisor,
                                                'intIdCargoTelcos'           => $intIdCargoTelcos,
                                                'strGrupoProductoAsociado'   => $strGrupoProductoAsociado,
                                                'intTotalCargosJefes'        => $intTotalCargosJefes,
                                                'boolHabilitarComoJefe'      => $boolHabilitarComoJefe,
                                                'strEsAsistente'             => $strEsAsistente,
                                                'strTiempoLimite'            => $strTiempoLimite,
                                                'boolSoloAsis'               => $strSoloAsis
                                            );
                }//foreach ($arrayRegistros as $arrayDatos)
                
                $this->emComercial->getConnection()->commit();
            }
            catch(Exception $e)
            {
                error_log($e->getMessage());

                $this->emComercial->getConnection()->rollback();
            }
            $this->emComercial->getConnection()->close();
        }//( $arrayRegistros )
        
        $arrayResultados['usuarios'] = $arrayUsuarios;
        
        return $arrayResultados;
    }
}