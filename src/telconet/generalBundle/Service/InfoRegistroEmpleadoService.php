<?php

namespace telconet\generalBundle\Service;

use telconet\schemaBundle\Entity\InfoRegistroEmpleado;


/**
 * Documentación para la clase 'InfoRegistroEmpleadoService'.
 *
 * Descripción : Contiene metodos para gestionar los registros de empleados
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 11-11-2016
 */
class InfoRegistroEmpleadoService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emGeneral;
    private $emComercial;
    private $serviceUtil;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emGeneral            = $container->get('doctrine.orm.telconet_general_entity_manager');
        $this->emComercial          = $container->get('doctrine.orm.telconet_entity_manager');
        $this->serviceUtil          = $container->get('schema.Util');
    }
    
    
    /**
     * Documentación: Registrar - Función que permite registrar los tiempos laborables del empleado
     * Por ejemplo puede registrar Inicio de labores, Fin de labores, salidas y llegadas de almuerzo
     * @version 1.0 08-11-2016
     * @since 08-11-2016
     * @author Andrés Montero <amontero@telconet.ec>
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 20-11-2016 Se realiza el respectivo registro para los integrantes que conforman esa cuadrilla sean o no prestados
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.2 16-04-2017 Se registra el imei del dispositivo que realiza la marcación de jornada.
     * 
     * @param type array $arrayParametros (
     *     login        => usuario del empleado,
     *     tipoRegistro => tipo de registro laboral del empleado)
     *     idEmpresa    => id de la empresa a la que pertenece el empleado
     *     latitud      => latitud de las coordenadas de lugar que se realiza el registro
     *     longitud     => longitud de las coordenadas de lugar que se realiza el registro	 
     *     imei         => imei del dispositivo que realiza el registro. 
     * @return Array $arrayResultado
     * [
     *    - status   Estado de la transaccion ejecutada
     *    - mensaje  Mensaje de la transaccion ejecutada
     * ]
     */
    public function registroEmpleado($arrayParametros)
    {
        $arrayResultado            = array();
        $arrayResultado['status']  = "ERROR";
        $arrayResultado['mensaje'] = "Se presentaron problemas al ingresar la información, favor notificar a sistemas."; 
        $strLatitud                = "";
        $strLongitud               = "";
        $strPermiso                = "";
        $strImei                   = "";
        $this->emGeneral->getConnection()->beginTransaction();   
        try
        {
            //Consultamos el idpersonaempresarol del empleado
            $arrayParametrosPersona                   = array();
            $arrayParametrosPersona['login']          = $arrayParametros["strLogin"];
            $arrayParametrosPersona['idEmpresa']      = $arrayParametros['strIdEmpresa'];
            $arrayParametrosPersona['estado']         = 'ACTIVO';            
            $arrayParametrosPersona['tipo_persona']   = 'empleado';
            $arrayParametrosPersona['fechaDesde']     = '';
            $arrayParametrosPersona['fechaHasta']     = '';
            $arrayParametrosPersona['nombre']         = '';
            $arrayParametrosPersona['apellido']       = '';
            $arrayParametrosPersona['razon_social']   = '';
            $arrayParametrosPersona['identificacion'] = '';
            $arrayParametrosPersona['usuario']        = '';
            $arrayParametrosPersona['limit']          = 0;
            $arrayParametrosPersona['start']          = 0;

            $arrayPersonaEmpresaRol = $this->emGeneral->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                      ->findPersonasPorCriterios($arrayParametrosPersona);
            
            if(count($arrayPersonaEmpresaRol['registros']) <= 0)
            {
                throw new \Exception("no existe rol empleado para usuario");
            }
           
            $strLatitud   = (!$arrayParametros["strLatitud"])? "0" : $arrayParametros["strLatitud"];
            $strLongitud  = (!$arrayParametros["strLongitud"])? "0" : $arrayParametros["strLongitud"];
            $strPermiso   = (!$arrayParametros["strPermiso"])? "" : $arrayParametros["strPermiso"];
            $strImei      = (!$arrayParametros["strImei"])? "SIN IMEI" : $arrayParametros["strImei"];
		    
            //Se crea Registro de empleado
            $objRegistroEmpleado = new InfoRegistroEmpleado();
            $objRegistroEmpleado->setPersonaEmpresaRolId($arrayPersonaEmpresaRol['registros'][0]['id']);
            $objRegistroEmpleado->setTipoRegistro($arrayParametros["strTipoRegistro"]);
            $objRegistroEmpleado->setLatitud($strLatitud);
            $objRegistroEmpleado->setLongitud($strLongitud);
            $objRegistroEmpleado->setPermiso($strPermiso);
            $objRegistroEmpleado->setImei($strImei);
            $objRegistroEmpleado->setFeRegistro(new \DateTime('now'));
            $objRegistroEmpleado->setFeCreacion(new \DateTime('now'));
            $objRegistroEmpleado->setUsrCreacion($arrayParametros["strLogin"]);
            $objRegistroEmpleado->setIpCreacion($arrayParametros["strClientIp"]);
            $objRegistroEmpleado->setEstado('Activo');
            $this->emGeneral->persist($objRegistroEmpleado); 
            $this->emGeneral->flush();
            
            /*Verificando si la persona que está marcando es el Líder de una Cuadrilla*/
            $arrayInfoCuadrilla = $this->emComercial->getRepository('schemaBundle:InfoCuadrilla')
                                                    ->getLiderCuadrilla($arrayPersonaEmpresaRol['registros'][0]['persona_id']); 
            
            if($arrayInfoCuadrilla)
            {
                if(isset($arrayPersonaEmpresaRol['registros'][0]['cuadrilla_id']) && !empty($arrayPersonaEmpresaRol['registros'][0]['cuadrilla_id']))
                {
                    $arrayCargos    = array();
                    $objCargos      = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->get('CARGOS AREA TECNICA', 
                                                                          '', 
                                                                          '', 
                                                                          '', 
                                                                          'Personal Tecnico', 
                                                                          '',
                                                                          '', 
                                                                          ''
                                                                          );
                    if(is_object($objCargos) )
                    {
                        foreach($objCargos as $objCargoTecnico)
                        {
                            $arrayCargos[] = $objCargoTecnico['descripcion'];

                        }
                    }

                    $arrayParametrosIntegrantesCuadrilla['criterios']['cargoSimilar']   = $arrayCargos;
                    $arrayParametrosIntegrantesCuadrilla['intIdCuadrilla']              = $arrayPersonaEmpresaRol['registros'][0]['cuadrilla_id'];
                    $arrayParametrosIntegrantesCuadrilla['empresa']                     = $arrayParametros['strIdEmpresa'];
                    
                    $arrayTmpPersonasCuadrilla = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                   ->findPersonalByCriterios($arrayParametrosIntegrantesCuadrilla);


                    $arrayRegistrosPersonasCuadrilla                        = $arrayTmpPersonasCuadrilla['registros'];

                    if( $arrayRegistrosPersonasCuadrilla )
                    {
                        foreach ($arrayRegistrosPersonasCuadrilla as $arrayDatosIntegrante)
                        {
                            $intIdPersonaEmpresaRolIntegrante   = $arrayDatosIntegrante['idPersonaEmpresaRol'];
                            $strLoginIntegrante                 = $arrayDatosIntegrante['login'];
                            
                            if($intIdPersonaEmpresaRolIntegrante != $arrayPersonaEmpresaRol['registros'][0]['id'])
                            {
                                $objRegistroEmpleadoIntegrante = new InfoRegistroEmpleado();
                                $objRegistroEmpleadoIntegrante->setPersonaEmpresaRolId($intIdPersonaEmpresaRolIntegrante);
                                $objRegistroEmpleadoIntegrante->setTipoRegistro($arrayParametros["strTipoRegistro"]);
                                $objRegistroEmpleadoIntegrante->setLatitud($strLatitud);
                                $objRegistroEmpleadoIntegrante->setLongitud($strLongitud);
                                $objRegistroEmpleadoIntegrante->setImei($strImei);
                                $objRegistroEmpleadoIntegrante->setPermiso($strPermiso);
                                $objRegistroEmpleadoIntegrante->setFeRegistro(new \DateTime('now'));
                                $objRegistroEmpleadoIntegrante->setFeCreacion(new \DateTime('now'));
                                $objRegistroEmpleadoIntegrante->setUsrCreacion($strLoginIntegrante);
                                $objRegistroEmpleadoIntegrante->setIpCreacion($arrayParametros["strClientIp"]);
                                $objRegistroEmpleadoIntegrante->setEstado('Activo');
                                $this->emGeneral->persist($objRegistroEmpleadoIntegrante); 
                                $this->emGeneral->flush();
                            }
                        }
                    }
                    else
                    {
                        $this->serviceUtil->insertError('Telcos+', 
                                                        'Registro de empleado', 
                                                        'Se ha registrado el líder de cuadrilla, pero no hay integrantes en esta cuadrilla', 
                                                        $arrayParametros["strLogin"], 
                                                        $arrayParametros["strClientIp"] );
                    }
                }
                else
                {
                    $this->serviceUtil->insertError('Telcos+', 
                                                    'Registro de empleado', 
                                                    'Se ha registrado el líder de cuadrilla, pero no existe el id de la cuadrilla', 
                                                    $arrayParametros["strLogin"], 
                                                    $arrayParametros["strClientIp"] );
                }
            }
            $this->emGeneral->commit();
            //
            $arrayResultado['status']  = "OK";
            $arrayResultado['mensaje'] = "Se registro correctamente.";
        }
        catch(\Exception $ex)
        {             
            if($this->emGeneral->getConnection()->isTransactionActive())
            {
                
                $this->emGeneral->rollback();                
                
                $this->serviceUtil->insertError('Telcos+', 
                                                'Registro de empleado', 
                                                'Error al guardar el registro de empleado. '.$ex->getMessage(), 
                                                $arrayParametros["strLogin"], 
                                                $arrayParametros["strClientIp"] );
                $arrayResultado['status']  = "ERROR";
                $arrayResultado['mensaje'] = "Se presentaron problemas al ingresar la información, favor notificar a sistemas.";                    
                
                $this->emGeneral->close();                                     
            }         
            error_log($ex->getMessage());            
        }
        
        return $arrayResultado;
    }
    
    
    
    
    /**
     * Documentación: Registrar - Función que permite consultar los registros de los tiempos laborables del empleado
     *
     * @version 1.0 09-11-2016
     * @since 09-11-2016
     * @author Andrés Montero <amontero@telconet.ec>
     * @param Array $arrayParametros [ 
     *                                 - strLogin        login del empleado
     *                                 - strIdEmpresa    id empresa del empleado
     *                               ]
     * @return Array $arrayRespuesta 
     * [
     *    - strStatus   Estado de la transaccion ejecutada
     *    - strMensaje  Mensaje de la transaccion ejecutada
     *    - arrayData 
     *                [ - id           id del registro del empleado
     *                  - feRegistro   fecha del registro
     *                  - feCreacion   fecha de creación del registro
     *                  - usrCreacion  usuario que creo el registro
     *                  - estado       estado del registro
     *                ]
     *    - arrayUltimoRegistro
     *                          [ - tipoRegistro        el ultimo tipo de registro registrado por el usuario
     *                            - fechaUltimoRegistro la fecha del ultimo registro ingresado por el usuario
     *                          ]
     * ]
     */
    public function getRegistrosEmpleado($arrayParametros)
    {
        $arrayRespuesta               = array();
        $arrayRespuesta['strStatus']  = "ERROR";
        $arrayRespuesta['strMensaje'] = "Se presentaron problemas al consultar la información, favor notificar a sistemas.";
        $arrayRespuesta['arrayData']  = array();
        try
        {
            //Consultamos el idpersonaempresarol del empleado
            $arrayParametrosPersona                   = array();
            $arrayParametrosPersona['login']          = $arrayParametros["strLogin"];
            $arrayParametrosPersona['idEmpresa']      = $arrayParametros['strIdEmpresa'];
            $arrayParametrosPersona['estado']         = 'ACTIVO';            
            $arrayParametrosPersona['tipo_persona']   = 'empleado';
            $arrayParametrosPersona['fechaDesde']     = '';
            $arrayParametrosPersona['fechaHasta']     = '';
            $arrayParametrosPersona['nombre']         = '';
            $arrayParametrosPersona['apellido']       = '';
            $arrayParametrosPersona['razon_social']   = '';
            $arrayParametrosPersona['identificacion'] = '';
            $arrayParametrosPersona['usuario']        = '';
            $arrayParametrosPersona['limit']          = 0;
            $arrayParametrosPersona['start']          = 0;

            $arrayPersonaEmpresaRol = $this->emGeneral->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                      ->findPersonasPorCriterios($arrayParametrosPersona);
            
            if(count($arrayPersonaEmpresaRol['registros']) <= 0)
            {
                throw new \Exception("no existe rol empleado para usuario");
            }
            
            $arrayParametrosRegistros['intPersonaEmpresaRolId'] = $arrayPersonaEmpresaRol['registros'][0]['id'];
            $arrayParametrosRegistros['dateFechaDesde']         = $arrayParametros['dateFechaDesde'];
            $arrayParametrosRegistros['datefechaHasta']         = $arrayParametros['dateFechaHasta'];
            $arrayParametrosRegistros['arrayEstado']            = $arrayParametros['arrayEstado'];
            $arrayParametrosRegistros['arrayTipoRegistro']      = $arrayParametros['arrayTipoRegistro'];               
            
            $arrayRespuestaRegistros = $this->emGeneral->getRepository('schemaBundle:InfoRegistroEmpleado')
                                                       ->getRegistrosEmpleado($arrayParametrosRegistros);

            //Se consulta el ultimo registro de jornada laboral del empleado
            $arrayParametrosUltimoRegistro['intPersonaEmpresaRolId'] = $arrayPersonaEmpresaRol['registros'][0]['id'];
            $objRespuestaUltRegistro                                 = $this->emGeneral->getRepository('schemaBundle:InfoRegistroEmpleado')
                                                                                       ->getMaxRegistro($arrayParametrosUltimoRegistro);
            
            $arrayRespuesta['arrayUltimoRegistro']                   = is_object($objRespuestaUltRegistro) ?  
                                                                           array(
                                                                                 "tipoRegistro"        => $objRespuestaUltRegistro->getTipoRegistro(),
                                                                                 "fechaUltimoRegistro" => $objRespuestaUltRegistro->getFeRegistro()
                                                                                ) 
                                                                                : 
                                                                           array(
                                                                                 "tipoRegistro"        => "FIN JORNADA",
                                                                                 "fechaUltimoRegistro" => new \DateTime('NOW')
                                                                                );            
            //
            if (count($arrayRespuestaRegistros['arrayRegistros'])>0)
            {
                $arrayRespuesta['arrayData']      = $arrayRespuestaRegistros['arrayRegistros'];
                $arrayRespuesta['strStatus']      = "OK";
                $arrayRespuesta['strMensaje']     = "Se consultaron datos correctamente.";                
            }
            else
            {
                $arrayRespuesta['strStatus']  = "OK";
                $arrayRespuesta['strMensaje'] = "No existe información para la consulta realizada.";
            }
            
        }
        catch(\Exception $ex)
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'Consultar registros de empleado', 
                                            'Error al consultar registros de empleado. '.$ex->getMessage(), 
                                            $arrayParametros["strLogin"], 
                                            $arrayParametros["strClientIp"] );
            $arrayRespuesta['strStatus']  = "ERROR";
            $arrayRespuesta['strMensaje'] = "Se presentaron problemas al consultar la información, favor notificar a sistemas.";            
            error_log($ex->getMessage());
        }
        return $arrayRespuesta;
    } 
    
    
    
}
