<?php

namespace telconet\soporteBundle\Service;

use Doctrine\ORM\EntityManager;
use telconet\schemaBundle\Entity\ReturnResponse;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\schemaBundle\Entity\AdmiTipoDocumento;
use telconet\schemaBundle\Entity\AdmiTipoDocumentoGeneral;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\schemaBundle\Entity\AdmiCaracteristica;
use telconet\schemaBundle\Entity\InfoContrato;
use telconet\schemaBundle\Entity\InfoOficinaGrupo;
use telconet\schemaBundle\Entity\InfoContratoFormaPago;
class SoporteSDService
{
    private $emGeneral;
    private $emComercial;
    private $emFinanciero;
    private $emBiFinanciero;
    private $emSoporte;
    private $emInfraestructura;
    private $emComunicacion;
    private $serviceUtil;
    private $serviceTecnico;
    private $serviceSoporte;
    private $serviceProceso;
    private $serviceCorreo;
    private $serviceContrato;
    private $serviceCrypt;
    private $strUserDocumental;
    private $strPasswordDocumental;
    private $strDatabaseDsn;
    private $strCaracSolicitudSD = 'REFERENCIA_SOLICITUD_SD';
    private $strEstadoActivo     = "Activo";
    private $strEstadoInactivo   = "Inactivo";
    private $strEstadoAprobado   = "Aprobado";
    /**
     * Documentación para la función 'setDependencies'.
     *
     * Función encargada de setear los entities manager de los esquemas de base de datos.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 03-08-2021
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $objContainer - objeto contenedor
     *
     */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer)
    {
        $this->emGeneral             = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
        $this->emComercial           = $objContainer->get('doctrine.orm.telconet_entity_manager');
        $this->emFinanciero          = $objContainer->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->emBiFinanciero        = $objContainer->get('doctrine.orm.telconet_bifinanciero_entity_manager');
        $this->emSoporte             = $objContainer->get('doctrine.orm.telconet_soporte_entity_manager');
        $this->emInfraestructura     = $objContainer->get('doctrine.orm.telconet_infraestructura_entity_manager');
        $this->emComunicacion        = $objContainer->get('doctrine.orm.telconet_comunicacion_entity_manager');
        $this->serviceUtil           = $objContainer->get('schema.Util');
        $this->serviceTecnico        = $objContainer->get('tecnico.InfoServicioTecnico');
        $this->serviceSoporte        = $objContainer->get('soporte.SoporteService');
        $this->serviceProceso        = $objContainer->get('soporte.ProcesoService');
        $this->serviceCorreo         = $objContainer->get('soporte.EnvioPlantilla');
        $this->serviceContrato       = $objContainer->get('comercial.InfoContratoAprob');
        $this->serviceCrypt          = $objContainer->get('seguridad.Crypt');
        $this->strUserDocumental     = $objContainer->getParameter('user_documental');
        $this->strPasswordDocumental = $objContainer->getParameter('passwd_documental');
        $this->strDatabaseDsn        = $objContainer->getParameter('database_dsn');

    }

    /**
     * Documentación para la función 'getPersonaSD'.
     *
     * Función encargada de retornar si existe el cliente o pre-cliente.
     *
     * @param array $arrayParametros [
     *                                "strPrefijoEmpresa"     => Prefijo de la empresa.
     *                                "intCodEmpresa"         => Código de la empresa.
     *                                "intIdUsrCreacion"      => Número de identificación del usuario logueado en el portal SD.
     *                                "strIpCreacion"         => IP del usuario logueado en el portal SD.
     *                                "intIdentificacionClt"  => Identificación del cliente.
     *                               ]
     *
     * @return array $arrayResultado [
     *                                "message"      =>  Mensaje de respuesta.
     *                                "status"       =>  Estado de respuesta.
     *                                "rol"          =>  Rol de la identificación a buscar.
     *                               ]
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 09-10-2021
     *
     */
    public function getPersonaSD($arrayParametros)
    {
        $strIpCreacion           = (isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']))
                                    ? $arrayParametros['strIpCreacion'] : '127.0.0.1';
        $arrayRespuesta          = array();
        $strStatus               = "EXITO";
        $strUsrCreacion          = "TelcoS+";
        $objDatetimeActual       = new \DateTime('now');
        try
        {
            if(empty($arrayParametros) || !is_array($arrayParametros))
            {
                throw new \Exception("Datos incompletos para crear la tarea.");
            }
            if(!isset($arrayParametros['strPrefijoEmpresa']) || empty($arrayParametros['strPrefijoEmpresa']))
            {
                throw new \Exception("La variable strPrefijoEmpresa es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intCodEmpresa']) || empty($arrayParametros['intCodEmpresa']))
            {
                throw new \Exception("La variable intCodEmpresa es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intIdUsrCreacion']) || empty($arrayParametros['intIdUsrCreacion']))
            {
                throw new \Exception("La variable intIdUsrCreacion es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intIdentificacionClt']) || empty($arrayParametros['intIdentificacionClt']))
            {
                throw new \Exception("La variable intIdentificacionClt es un campo obligatorio.");
            }
            //Se obtiene los datos del empleado.
            $objPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                               ->findOneByIdentificacionCliente($arrayParametros['intIdUsrCreacion']);
            if(!is_object($objPersona) || !in_array($objPersona->getEstado(), array('Activo','Pendiente','Modificado')))
            {
                throw new \Exception("No existe usuario en TelcoS+ con la identificación: '".$arrayParametros['intIdUsrCreacion']."'");
            }
            //Se consulta la información con rol cliente.
            $strUsrCreacion                          = $objPersona->getLogin();
            $strRol                                  = "Cliente";
            $arrayParametrosClt                      = array();
            $arrayParametrosClt['estado']            = "Activo";
            $arrayParametrosClt['idEmpresa']         = $arrayParametros['intCodEmpresa'];
            $arrayParametrosClt['identificacion']    = $arrayParametros['intIdentificacionClt'];
            $arrayParametrosClt['tipo_persona']      = array('cliente');
            $arrayParametrosClt['strModulo']         = 'Cliente';
            $arrayParametrosClt['strPrefijoEmpresa'] = $arrayParametros['strPrefijoEmpresa'];
            $arrayResultadoClt                       = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                            ->findPersonasPorCriterios($arrayParametrosClt);
            $arrayRegistrosClt                       = $arrayResultadoClt['registros'];
            $intTotalClt                             = $arrayResultadoClt['total'];
            if(empty($arrayRegistrosClt) || $intTotalClt == 0)
            {
                //Se consulta la información con rol pre-cliente.
                $strRol                              = "Pre-Cliente";
                $arrayParametrosClt['tipo_persona']  = array('pre-cliente');
                $arrayParametrosClt['strModulo']     = 'Pre-Cliente';
                $arrayResultadoClt                   = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                            ->findPersonasPorCriterios($arrayParametrosClt);
                $arrayRegistrosClt                   = $arrayResultadoClt['registros'];
                $intTotalClt                         = $arrayResultadoClt['total'];
                if(empty($arrayRegistrosClt) || $intTotalClt == 0)
                {
                    throw new \Exception("No existe información, con la identifiación: '".$arrayParametros['intIdentificacionClt']."'.");
                }
            }
            $arrayRespuesta['status']       = $strStatus;
            $arrayRespuesta['message']      = "Se consultó la información, correctamente.";
            $arrayRespuesta['rol']          = $strRol;
        }
        catch(\Exception $ex)
        {
            $arrayRespuesta['message'] = $ex->getMessage();
            $arrayRespuesta['status']  = "ERROR";
            $arrayRespuesta['rol']     = "";
            $this->serviceUtil->insertError('TELCOS+',
                                            'SoporteSDService.getPersonaSD',
                                            $ex->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        return $arrayRespuesta;
    }

    /**
     * Documentación para la función 'putCrearTareaSD'.
     *
     * Función encargada de crear tareas.
     *
     * @param array $arrayParametros [
     *                                "strPrefijoEmpresa"     => Prefijo de la empresa.
     *                                "intCodEmpresa"         => Código de la empresa.
     *                                "intIdUsrCreacion"      => Número de identificación del usuario logueado en el portal SD.
     *                                "strIpCreacion"         => IP del usuario logueado en el portal SD.
     *                                "strFechaSolicitada"    => Fecha solicitada para la ejecución de la tarea.
     *                                "strHoraSolicitada"     => Hora de la tarea.
     *                                "strObservacion"        => Observación de la tarea a crear.
     *                                "strEsAutomatico"       => Valor boleano para iniciar la tarea.
     *                                "intIdentificacionClt"  => Identificación del cliente.
     *                                "intIdSolicitudSD"      => Número de solicitud del portal SD.
     *                                "strDocumentoAdjunto"   => Bandera que me permite saber si se va a subir archivos a la tarea.
     *                                "file"                  => Archivo en base64.
     *                                "fileName"              => Nombre del archivo.
     *                                "fileExtension"         => Extensión del archivo.
     *                               ]
     *
     * @return array $arrayResultado [
     *                                "message"      =>  Mensaje de respuesta.
     *                                "status"       =>  Estado de respuesta.
     *                                "intTarea"     =>  Número de tarea.
     *                                "intIdDetalle" =>  Número detalle de la tarea.
     *                               ]
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 17-08-2021
     *
     */
    public function putCrearTareaSD($arrayParametros)
    {
        $strIpCreacion           = (isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']))
                                    ? $arrayParametros['strIpCreacion'] : '127.0.0.1';
        $arrayTarea              = array();
        $arrayRespuesta          = array();
        $strStatus               = "EXITO";
        $strUsrCreacion          = "TelcoS+";
        $objDatetimeActual       = new \DateTime('now');
        try
        {
            if(empty($arrayParametros) || !is_array($arrayParametros))
            {
                throw new \Exception("Datos incompletos para crear la tarea.");
            }
            if(!isset($arrayParametros['strPrefijoEmpresa']) || empty($arrayParametros['strPrefijoEmpresa']))
            {
                throw new \Exception("La variable strPrefijoEmpresa es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intCodEmpresa']) || empty($arrayParametros['intCodEmpresa']))
            {
                throw new \Exception("La variable intCodEmpresa es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intIdUsrCreacion']) || empty($arrayParametros['intIdUsrCreacion']))
            {
                throw new \Exception("La variable intIdUsrCreacion es un campo obligatorio.");
            }
            if(!isset($arrayParametros['strHoraSolicitada'])  || empty($arrayParametros['strHoraSolicitada']) ||
                !isset($arrayParametros['strFechaSolicitada']) || empty($arrayParametros['strFechaSolicitada']))
            {
                throw new \Exception('La strHoraSolicitada y(o) strFechaSolicitada es un campo obligatorio');
            }
            $arrayFecha = explode('-', $arrayParametros['strFechaSolicitada']);
            if(count($arrayFecha) !== 3 || !checkdate($arrayFecha[1], $arrayFecha[2], $arrayFecha[0]))
            {
                throw new \Exception('El Formato de fecha es inválido');
            }
            if(strtotime($arrayParametros['strHoraSolicitada']) === false)
            {
                throw new \Exception('El Formato de hora es inválido');
            }
            if(!isset($arrayParametros['strObservacion']) || empty($arrayParametros['strObservacion']))
            {
                throw new \Exception("La variable strObservacion es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intIdentificacionClt']) || empty($arrayParametros['intIdentificacionClt']))
            {
                throw new \Exception("La variable intIdentificacionClt es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intIdSolicitudSD']) || empty($arrayParametros['intIdSolicitudSD']))
            {
                throw new \Exception("La variable intIdSolicitudSD es un campo obligatorio.");
            }
            if(!isset($arrayParametros['strDocumentoAdjunto']) || empty($arrayParametros['strDocumentoAdjunto']))
            {
                throw new \Exception("La bandera del documneto adjunto es un campo obligatorio.");
            }
            if($arrayParametros['strDocumentoAdjunto'] == "S")
            {
                if(!isset($arrayParametros['file']) || empty($arrayParametros['file']))
                {
                    throw new \Exception("El archivo en Base64 es un campo obligatorio.");
                }
                if(!isset($arrayParametros['fileName']) || empty($arrayParametros['fileName']))
                {
                    throw new \Exception("El nombre del archivo es un campo obligatorio.");
                }
                if(!isset($arrayParametros['fileExtension']) || empty($arrayParametros['fileExtension']))
                {
                    throw new \Exception("El tipo de extension del archivo es un campo obligatorio.");
                }
            }
            //Se obtiene los datos del empleado.
            $objPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                               ->findOneByIdentificacionCliente($arrayParametros['intIdUsrCreacion']);
            if(!is_object($objPersona) || !in_array($objPersona->getEstado(), array('Activo','Pendiente','Modificado')))
            {
                throw new \Exception("No existe usuario en TelcoS+ con la identificación: '".$arrayParametros['intIdUsrCreacion']."'");
            }
            $arrayUsCreador = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                   ->getInfoDatosPersona(array ('strRol'                     => 'Empleado',
                                                                'strPrefijo'                 => $arrayParametros['strPrefijoEmpresa'],
                                                                'strEstadoPersona'           => array('Activo',
                                                                                                      'Pendiente',
                                                                                                      'Modificado'),
                                                                'strEstadoPersonaEmpresaRol' => $this->strEstadoActivo,
                                                                'strLogin'                   => $objPersona->getLogin()));
            $strUsrCreacion    = $objPersona->getLogin();
            $strUsuarioAsigna  = $objPersona->getNombres()." ".$objPersona->getApellidos();
            if($arrayUsCreador['status'] === 'fail')
            {
                throw new \Exception('Error al obtener los datos del empleado, por favor comuníquese con el departamento de Sistemas.');
            }
            if($arrayUsCreador['status'] === 'ok' && empty($arrayUsCreador['result']))
            {
                throw new \Exception('Los filtros para encontrar el empleado son incorrectos o el empleado no existe en TelcoS+');
            }
            //Por medio de la región del empleado, se obtiene el usuario de cobranza.
            $arrayUsCobranza = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->get('PARAMETROS_SECURITY_DATA',
                                          'COMERCIAL',
                                          'CONTRATO_DIGITAL_SD',
                                          'LISTA_USUARIO_COBRANZA',
                                          '',
                                          $arrayUsCreador['result'][0]['region'],
                                          '',
                                          '',
                                          '',
                                          $arrayParametros['intCodEmpresa']);
            if(empty($arrayUsCobranza) || !is_array($arrayUsCobranza))
            {
                throw new \Exception("No existe usuario de cobranza asignado para la región: ".$arrayUsCreador['result'][0]['region']);
            }
            //Se obtiene el nombre y proceso de la tarea.
            $arrayTarea = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                               ->get('PARAMETROS_SECURITY_DATA',
                                     'COMERCIAL',
                                     'CONTRATO_DIGITAL_SD',
                                     'TAREA_PROCESO',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     $arrayParametros['intCodEmpresa']);
            if(empty($arrayTarea) || !is_array($arrayTarea))
            {
                throw new \Exception("No existe tarea y proceso");
            }
            //Se obtiene los datos del Usuario de cobranza.
            $arrayDatosUsCobranza = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                         ->getInfoDatosPersona(array('strRol'                     => 'Empleado',
                                                                     'strPrefijo'                 => $arrayParametros['strPrefijoEmpresa'],
                                                                     'strEstadoPersona'           => array('Activo',
                                                                                                           'Pendiente',
                                                                                                           'Modificado'),
                                                                     'strEstadoPersonaEmpresaRol' => 'Activo',
                                                                     'strLogin'                   => $arrayUsCobranza[0]["valor1"]));
            if($arrayDatosUsCobranza['status'] === 'fail')
            {
                throw new \Exception('Error al obtener los datos del usuario asignado, por favor comuníquese con el departamento de Sistemas.');
            }
            if($arrayDatosUsCobranza['status'] === 'ok' && empty($arrayDatosUsCobranza['result']))
            {
                throw new \Exception('Los filtros para encontrar al empleado asignado son incorrectos o el empleado no existe en TelcoS+');
            }
            //Se obtiene los datos del pre-cliente.
            $objPersonaEmpresaRolClt = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                            ->findByIdentificacionTipoRolEmpresa($arrayParametros['intIdentificacionClt'],
                                                                                 'Pre-cliente',
                                                                                 $arrayParametros['intCodEmpresa']);
            if(empty($objPersonaEmpresaRolClt) || !is_object($objPersonaEmpresaRolClt))
            {
                //En caso de no tener el rol de pre-cliente, se consulta por el rol de cliente.
                $objPersonaEmpresaRolClt = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                ->findByIdentificacionTipoRolEmpresa($arrayParametros['intIdentificacionClt'],
                                                                                     'Cliente',
                                                                                     $arrayParametros['intCodEmpresa']);
                if(empty($objPersonaEmpresaRolClt) || !is_object($objPersonaEmpresaRolClt))
                {
                    throw new \Exception("No existe pre-cliente ingresado en TelcoS+, con la identificación: '".
                                        $arrayParametros['intIdentificacionClt']."'");
                }
            }
            //Se obtiene la características de solicitud SD.
            $objAdmiCaractSolicitudSD = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                             ->findOneBy(array('descripcionCaracteristica' => $this->strCaracSolicitudSD,
                                                               'estado'                    => $this->strEstadoActivo));
            if(empty($objAdmiCaractSolicitudSD) || !is_object($objAdmiCaractSolicitudSD))
            {
                throw new \Exception('No existe la característica: '.$this->strCaracSolicitudSD);
            }
            //Validación => Se valida que no tenga otra tarea asignada con la misma solicitud SD, en estado Activo.
            $arrayPersonaEmpresaRolCarac = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                ->findBy(array('estado'              => $this->strEstadoActivo,
                                                               'personaEmpresaRolId' => $objPersonaEmpresaRolClt,
                                                               'caracteristicaId'    => $objAdmiCaractSolicitudSD));
            if(!empty($arrayPersonaEmpresaRolCarac) && is_array($arrayPersonaEmpresaRolCarac))
            {
                $intNumeroDetalle = 0;
                foreach($arrayPersonaEmpresaRolCarac as $objItem)
                {
                    $objCaracValor    = json_decode($objItem->getValor());
                    $intIdSolicitudSD = $objCaracValor->intIdSolicitudSD;
                    if(!empty($intIdSolicitudSD) && $intIdSolicitudSD == $arrayParametros['intIdSolicitudSD'])
                    {
                        $intNumeroTarea   = $objCaracValor->intNumeroTarea;
                        $intNumeroDetalle = $objCaracValor->intNumeroDetalle;
                    }
                }
                if($intNumeroDetalle != 0)
                {
                    throw new \Exception('La Solicitud #'.$arrayParametros['intIdSolicitudSD'].', ya tiene una tarea ingresada con el #'.
                    $intNumeroTarea);
                }
            }
            //Se consume Service de crear tarea.
            $arrayParametrosTarea   = array('intIdPersonaEmpresaRol' => $arrayUsCreador['result'][0]['idPersonaEmpresaRol'],
                                            'intIdEmpresa'           => $arrayUsCreador['result'][0]['idEmpresa'],
                                            'strPrefijoEmpresa'      => $arrayUsCreador['result'][0]['prefijoEmpresa'],
                                            'strNombreTarea'         => $arrayTarea[0]['valor2'],
                                            'strNombreProceso'       => $arrayTarea[0]['valor1'],
                                            'strObservacionTarea'    => $arrayParametros['strObservacion'],
                                            'strMotivoTarea'         => $arrayParametros['strObservacion'],
                                            'strTipoAsignacion'      => 'empleado',
                                            'strIniciarTarea'        => (!empty($arrayParametros['strEsAutomatico'])) ? 
                                                                                $arrayParametros['strEsAutomatico']:"N",
                                            'strTipoTarea'           => 'T',
                                            'strTareaRapida'         => 'N',
                                            'strFechaHoraSolicitada' => $arrayParametros['strFechaSolicitada'].' '.
                                                                        $arrayParametros['strHoraSolicitada'],
                                            'boolAsignarTarea'       => true,
                                            'strAplicacion'          => 'TelcoS+',
                                            'strUsuarioAsigna'       => $strUsuarioAsigna,
                                            'strUserCreacion'        => $strUsrCreacion,
                                            'strIpCreacion'          => $strIpCreacion);
            $arrayRespuestaTarea = $this->serviceSoporte->crearTareaCasoSoporte($arrayParametrosTarea);
            if($arrayRespuestaTarea['mensaje'] === 'fail')
            {
                throw new \Exception('Error al crear la tarea, por favor comuníquese con el departamento de Sistemas.');
            }
            if((!empty($arrayRespuestaTarea['numeroTarea']) && isset($arrayRespuestaTarea['numeroTarea']))&&
               (!empty($arrayParametros['strDocumentoAdjunto'])&&isset($arrayParametros['strDocumentoAdjunto'])&& 
                       $arrayParametros['strDocumentoAdjunto'] == "S"))
            {
                $this->serviceProceso->putFile(array('strFileBase64'     => $arrayParametros["file"],
                                                     'strFileName'       => $arrayParametros["fileName"],
                                                     'strFileExtension'  => $arrayParametros["fileExtension"],
                                                     'intNumeroTarea'    => $arrayRespuestaTarea['numeroTarea'],
                                                     'strOrigen'         => "t",
                                                     'strPrefijoEmpresa' => $arrayParametros['strPrefijoEmpresa'],
                                                     'strUsuario'        => $strUsrCreacion,
                                                     'strIp'             => $strIpCreacion));

            }
            $this->emComercial->getConnection()->beginTransaction();
            //Se ingresa la característica de la solicitud al pre-cliente.
            $objPersonaEmpresaRolCaracSolSD = new InfoPersonaEmpresaRolCarac();
            $objPersonaEmpresaRolCaracSolSD->setEstado($this->strEstadoActivo);
            $objPersonaEmpresaRolCaracSolSD->setFeCreacion($objDatetimeActual);
            $objPersonaEmpresaRolCaracSolSD->setIpCreacion($strIpCreacion);
            $objPersonaEmpresaRolCaracSolSD->setUsrCreacion($strUsrCreacion);
            $objPersonaEmpresaRolCaracSolSD->setPersonaEmpresaRolId($objPersonaEmpresaRolClt);
            $objPersonaEmpresaRolCaracSolSD->setCaracteristicaId($objAdmiCaractSolicitudSD);
            $arrayValor = array("intIdSolicitudSD" => $arrayParametros['intIdSolicitudSD'],
                                "intNumeroTarea"   => $arrayRespuestaTarea['numeroTarea'],
                                "intNumeroDetalle" => $arrayRespuestaTarea['numeroDetalle']);
            $objPersonaEmpresaRolCaracSolSD->setValor(json_encode($arrayValor));
            $this->emComercial->persist($objPersonaEmpresaRolCaracSolSD);
            $this->emComercial->flush();

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->commit();
                $this->emComercial->getConnection()->close();
            }

            $arrayRespuesta['status']       = $strStatus;
            $arrayRespuesta['message']      = $arrayRespuestaTarea['asignacion'];
            $arrayRespuesta['intTarea']     = $arrayRespuestaTarea['numeroTarea'];
            $arrayRespuesta['intIdDetalle'] = $arrayRespuestaTarea['numeroDetalle'];
        }
        catch(\Exception $ex)
        {
            $arrayRespuesta['message'] = $ex->getMessage();
            $arrayRespuesta['status']  = "ERROR";
            $this->serviceUtil->insertError('TELCOS+',
                                            'SoporteSDService.putCrearTareaSD',
                                            $ex->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }
        }
        return $arrayRespuesta;
    }

    /**
     * Documentación para la función 'putIngresarSeguimientoSD'.
     *
     * Función encargada de ingresar seguimiento a las tareas.
     *
     * @param array $arrayParametros [
     *                                "strPrefijoEmpresa"     => Prefijo de la empresa.
     *                                "intCodEmpresa"         => Código de la empresa.
     *                                "strSeguimiento"        => Descripción del seguimiento a ingresar.
     *                                "intIdentificacionClt"  => Identificación del cliente.
     *                                "intIdSolicitudSD"      => Número de solicitud del portal SD.
     *                                "intIdUsrCreacion"      => Número de identificación del usuario logueado en el portal SD.
     *                                "strIpCreacion"         => IP del usuario logueado en el portal SD.
     *                               ]
     *
     * @return array $arrayResultado [
     *                                "message"      =>  Mensaje de respuesta.
     *                                "status"       =>  Estado de respuesta.
     *                               ]
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 17-08-2021
     *
     */
    public function putIngresarSeguimientoSD($arrayParametros)
    {
        $strIpCreacion           = (isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']))
                                    ? $arrayParametros['strIpCreacion'] : '127.0.0.1';
        $arrayRespuesta          = array();
        $strUsrCreacion          = "TelcoS+";
        $strStatus               = "EXITO";
        try
        {
            if(empty($arrayParametros) || !is_array($arrayParametros))
            {
                throw new \Exception("Datos incompletos para ingresar el seguimiento a la tarea.");
            }
            if(!isset($arrayParametros['strPrefijoEmpresa']) || empty($arrayParametros['strPrefijoEmpresa']))
            {
                throw new \Exception("La variable strPrefijoEmpresa es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intCodEmpresa']) || empty($arrayParametros['intCodEmpresa']))
            {
                throw new \Exception("La variable intCodEmpresa es un campo obligatorio.");
            }
            if(!isset($arrayParametros['strSeguimiento']) || empty($arrayParametros['strSeguimiento']))
            {
                throw new \Exception("La variable strSeguimiento es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intIdentificacionClt']) || empty($arrayParametros['intIdentificacionClt']))
            {
                throw new \Exception("La variable intIdentificacionClt es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intIdSolicitudSD']) || empty($arrayParametros['intIdSolicitudSD']))
            {
                throw new \Exception("La variable intIdSolicitudSD es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intIdUsrCreacion']) || empty($arrayParametros['intIdUsrCreacion']))
            {
                throw new \Exception("La variable intIdUsrCreacion es un campo obligatorio.");
            }
            //Se obtiene los datos del empleado.
            $objPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                               ->findOneByIdentificacionCliente($arrayParametros['intIdUsrCreacion']);
            if(!is_object($objPersona) || !in_array($objPersona->getEstado(), array('Activo','Pendiente','Modificado')))
            {
                throw new \Exception("No existe usuario en TelcoS+ con la identificación: '".$arrayParametros['intIdUsrCreacion']."'");
            }
            $strUsrCreacion          = $objPersona->getLogin();
            //Se obtiene los datos del pre-cliente.
            $objPersonaEmpresaRolClt = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                            ->findByIdentificacionTipoRolEmpresa($arrayParametros['intIdentificacionClt'],
                                                                                 'Pre-cliente',
                                                                                 $arrayParametros['intCodEmpresa']);
            if(empty($objPersonaEmpresaRolClt) || !is_object($objPersonaEmpresaRolClt))
            {
                //En caso de no tener el rol de pre-cliente, se consulta por el rol de cliente.
                $objPersonaEmpresaRolClt = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                ->findByIdentificacionTipoRolEmpresa($arrayParametros['intIdentificacionClt'],
                                                                                     'Cliente',
                                                                                     $arrayParametros['intCodEmpresa']);
                if(empty($objPersonaEmpresaRolClt) || !is_object($objPersonaEmpresaRolClt))
                {
                    throw new \Exception("No existe pre-cliente ingresado en TelcoS+, con la identificación: '".
                                        $arrayParametros['intIdentificacionClt']."'");
                }
            }
            //Se obtiene la características de solicitud SD.
            $objAdmiCaractSolicitudSD = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                             ->findOneBy(array('descripcionCaracteristica' => $this->strCaracSolicitudSD,
                                                               'estado'                    => $this->strEstadoActivo));
            if(empty($objAdmiCaractSolicitudSD) || !is_object($objAdmiCaractSolicitudSD))
            {
                throw new \Exception('No existe la característica: '.$this->strCaracSolicitudSD);
            }
            //Se consulta las características para obtener el idDetalle de la tarea de acuerdo a la solicitud.
            $arrayPersonaEmpresaRolCarac = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                ->findBy(array('estado'              => $this->strEstadoActivo,
                                                               'personaEmpresaRolId' => $objPersonaEmpresaRolClt,
                                                               'caracteristicaId'    => $objAdmiCaractSolicitudSD));
            if(!empty($arrayPersonaEmpresaRolCarac) && is_array($arrayPersonaEmpresaRolCarac))
            {
                $intNumeroDetalle = 0;
                foreach($arrayPersonaEmpresaRolCarac as $objItem)
                {
                    $objCaracValor    = json_decode($objItem->getValor());
                    $intIdSolicitudSD = $objCaracValor->intIdSolicitudSD;
                    if(!empty($intIdSolicitudSD) && $intIdSolicitudSD == $arrayParametros['intIdSolicitudSD'])
                    {
                        $intNumeroDetalle = $objCaracValor->intNumeroDetalle;
                    }
                }
                if($intNumeroDetalle == 0)
                {
                    throw new \Exception("No existe tarea relacionada a la solicitud #".$arrayParametros['intIdSolicitudSD']);
                }
            }
            else
            {
                throw new \Exception("No existe tarea relacionada a la solicitud #".$arrayParametros['intIdSolicitudSD']);
            }
            $strEstadoActualTarea = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')->getUltimoEstado($intNumeroDetalle);
            if(!empty($strEstadoActualTarea) && ($strEstadoActualTarea != "Finalizada" && $strEstadoActualTarea != "Cancelada" &&
               $strEstadoActualTarea != "Rechazada" && $strEstadoActualTarea != "Anulada"))
            {
                //Se consume Service de crear seguimiento a la tarea.
                $arrayParametros = array('idEmpresa'             => $arrayParametros['intCodEmpresa'],
                                         'idDetalle'             => $intNumeroDetalle,
                                         'seguimiento'           => $arrayParametros['strSeguimiento'],
                                         'usrCreacion'           => $strUsrCreacion,
                                         'ipCreacion'            => $strIpCreacion,
                                         'strEnviaDepartamento'  => "N");
                $arrayRespuestaService = $this->serviceSoporte->ingresarSeguimientoTarea($arrayParametros);
                if(!empty($arrayRespuestaService["status"]) && isset($arrayRespuestaService["status"]) &&
                    $arrayRespuestaService["status"] == "ERROR")
                {
                    throw new \Exception($arrayRespuestaService["mensaje"]);
                }
                $arrayRespuesta['status']       = $strStatus;
                $arrayRespuesta['message']      = "Se ingresó el seguimiento, correctamente.";
            }
            else
            {
                throw new \Exception("No es permitido ingresar seguimiento a la tarea en los siguientes estados:".
                                     " 'Finalizada', 'Cancelada', 'Rechazada', 'Anulada'.");
            }
        }
        catch(\Exception $ex)
        {
            $arrayRespuesta['message'] = $ex->getMessage();
            $arrayRespuesta['status']  = "ERROR";
            $this->serviceUtil->insertError('TELCOS+',
                                            'SoporteSDService.putIngresarSeguimientoSD',
                                            $ex->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        return $arrayRespuesta;
    }

    /**
     * Documentación para la función 'putAccionTareaSD'.
     *
     * Función encargada de (iniciar) la tarea.
     *
     * @param array $arrayParametros [
     *                                "strPrefijoEmpresa"     => Prefijo de la empresa.
     *                                "intCodEmpresa"         => Código de la empresa.
     *                                "strAccion"             => Tipo de acción a ejecutar(iniciar).
     *                                "strObservacion"        => Descripción del seguimiento a ingresar.
     *                                "intIdentificacionClt"  => Identificación del cliente.
     *                                "intIdSolicitudSD"      => Número de solicitud del portal SD.
     *                                "intIdUsrCreacion"      => Número de identificación del usuario logueado en el portal SD.
     *                                "strIpCreacion"         => IP del usuario logueado en el portal SD.
     *                               ]
     *
     * @return array $arrayResultado [
     *                                "message"      =>  Mensaje de respuesta.
     *                                "status"       =>  Estado de respuesta.
     *                               ]
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 17-08-2021
     *
     */
    public function putAccionTareaSD($arrayParametros)
    {
        $strIpCreacion           = (isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']))
                                    ? $arrayParametros['strIpCreacion'] : '127.0.0.1';
        $arrayRespuesta          = array();
        $strUsrCreacion          = "TelcoS+";
        $strStatus               = "EXITO";
        try
        {
            if(empty($arrayParametros) || !is_array($arrayParametros))
            {
                throw new \Exception("Datos incompletos.");
            }
            if(!isset($arrayParametros['strPrefijoEmpresa']) || empty($arrayParametros['strPrefijoEmpresa']))
            {
                throw new \Exception("La variable strPrefijoEmpresa es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intCodEmpresa']) || empty($arrayParametros['intCodEmpresa']))
            {
                throw new \Exception("La variable intCodEmpresa es un campo obligatorio.");
            }
            if(!isset($arrayParametros['strAccion']) || empty($arrayParametros['strAccion']))
            {
                throw new \Exception("La variable strAccion es un campo obligatorio.");
            }
            if($arrayParametros['strAccion'] != "iniciar")
            {
                throw new \Exception("Solo está permitido la acción de 'iniciar'.");
            }
            if(!isset($arrayParametros['strObservacion']) || empty($arrayParametros['strObservacion']))
            {
                throw new \Exception("La variable strObservacion es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intIdentificacionClt']) || empty($arrayParametros['intIdentificacionClt']))
            {
                throw new \Exception("La variable intIdentificacionClt es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intIdSolicitudSD']) || empty($arrayParametros['intIdSolicitudSD']))
            {
                throw new \Exception("La variable intIdSolicitudSD es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intIdUsrCreacion']) || empty($arrayParametros['intIdUsrCreacion']))
            {
                throw new \Exception("La variable intIdUsrCreacion es un campo obligatorio.");
            }
            //Se obtiene los datos del empleado.
            $objPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                               ->findOneByIdentificacionCliente($arrayParametros['intIdUsrCreacion']);
            if(!is_object($objPersona) || !in_array($objPersona->getEstado(), array('Activo','Pendiente','Modificado')))
            {
                throw new \Exception("No existe usuario en TelcoS+ con la identificación: '".$arrayParametros['intIdUsrCreacion']."'");
            }
            $strUsrCreacion          = $objPersona->getLogin();
            //Se obtiene los datos del pre-cliente.
            $objPersonaEmpresaRolClt = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                            ->findByIdentificacionTipoRolEmpresa($arrayParametros['intIdentificacionClt'],
                                                                                 'Pre-cliente',
                                                                                 $arrayParametros['intCodEmpresa']);
            if(empty($objPersonaEmpresaRolClt) || !is_object($objPersonaEmpresaRolClt))
            {
                //En caso de no tener el rol de pre-cliente, se consulta por el rol de cliente.
                $objPersonaEmpresaRolClt = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                ->findByIdentificacionTipoRolEmpresa($arrayParametros['intIdentificacionClt'],
                                                                                     'Cliente',
                                                                                     $arrayParametros['intCodEmpresa']);
                if(empty($objPersonaEmpresaRolClt) || !is_object($objPersonaEmpresaRolClt))
                {
                    throw new \Exception("No existe pre-cliente ingresado en TelcoS+, con la identificación: '".
                                        $arrayParametros['intIdentificacionClt']."'");
                }
            }
            $arrayDatosUsCreacion = $this->emComercial
                                         ->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                         ->getInfoDatosPersona(array('strRol'                     => 'Empleado',
                                                                     'strPrefijo'                 => $arrayParametros['strPrefijoEmpresa'],
                                                                     'strEstadoPersona'           => array('Activo',
                                                                                                           'Pendiente',
                                                                                                           'Modificado'),
                                                                     'strEstadoPersonaEmpresaRol' => $this->strEstadoActivo,
                                                                     'strLogin'                   => $strUsrCreacion));
            if($arrayDatosUsCreacion['status'] === 'fail')
            {
                throw new \Exception('Error al obtener los datos del usuario asignado, por favor comuníquese con el departamento de Sistemas.');
            }
            if($arrayDatosUsCreacion['status'] === 'ok' && empty($arrayDatosUsCreacion['result']))
            {
                throw new \Exception('Los filtros para encontrar al empleado asignado son incorrectos o el empleado no existe en TelcoS+');
            }
            //Se obtiene la características de solicitud SD, para (iniciar) la tarea.
            $objAdmiCaractSolicitudSD = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                             ->findOneBy(array('descripcionCaracteristica' => $this->strCaracSolicitudSD,
                                                               'estado'                    => $this->strEstadoActivo));
            if(empty($objAdmiCaractSolicitudSD) || !is_object($objAdmiCaractSolicitudSD))
            {
                throw new \Exception('No existe la característica: '.$this->strCaracSolicitudSD);
            }
            $arrayPersonaEmpresaRolCarac = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                ->findBy(array('estado'              => $this->strEstadoActivo,
                                                               'personaEmpresaRolId' => $objPersonaEmpresaRolClt,
                                                               'caracteristicaId'    => $objAdmiCaractSolicitudSD));
            if(!empty($arrayPersonaEmpresaRolCarac) && is_array($arrayPersonaEmpresaRolCarac))
            {
                $intNumeroDetalle = 0;
                foreach($arrayPersonaEmpresaRolCarac as $objItem)
                {
                    $objCaracValor    = json_decode($objItem->getValor());
                    $intIdSolicitudSD = $objCaracValor->intIdSolicitudSD;
                    if(!empty($intIdSolicitudSD) && $intIdSolicitudSD == $arrayParametros['intIdSolicitudSD'])
                    {
                        $intNumeroDetalle = $objCaracValor->intNumeroDetalle;
                    }
                }
                if($intNumeroDetalle == 0)
                {
                    throw new \Exception("No existe tarea relacionada a la solicitud #".$arrayParametros['intIdSolicitudSD']);
                }
            }
            else
            {
                throw new \Exception("No existe tarea relacionada a la solicitud #".$arrayParametros['intIdSolicitudSD']);
            }
            $objInfoDetalle = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')->find($intNumeroDetalle);
            if(!is_object($objInfoDetalle) || empty($objInfoDetalle))
            {
                throw new \Exception("No existe número de tarea relacionada al pre-cliente");
            }
            $strEstadoActualTarea = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')->getUltimoEstado($intNumeroDetalle);
            if(!empty($strEstadoActualTarea) && ($strEstadoActualTarea == "Asignada" || $strEstadoActualTarea == "Reprogramada"))
            {
                //Se consume Service para (iniciar) la tarea.
                $arrayParametrosAccionTarea["objDetalle"]           = $objInfoDetalle;
                $arrayParametrosAccionTarea["strObservacion"]       = $arrayParametros['strObservacion'];
                $arrayParametrosAccionTarea["strUser"]              = $strUsrCreacion;
                $arrayParametrosAccionTarea["strIpUser"]            = $strIpCreacion;
                $arrayParametrosAccionTarea["intPersonaEmpresaRol"] = $arrayDatosUsCreacion['result'][0]['idPersonaEmpresaRol'];
                $arrayParametrosAccionTarea["strTipo"]              = strtolower($arrayParametros['strAccion']);
                $arrayParametrosAccionTarea["strCodEmpresa"]        = $arrayParametros['intCodEmpresa'];

                $arrayRespuestaService = $this->serviceSoporte->administrarTarea($arrayParametrosAccionTarea);

                if(!empty($arrayRespuestaService["strRespuesta"]) && isset($arrayRespuestaService["strRespuesta"]) &&
                    $arrayRespuestaService["strRespuesta"] == "ERROR")
                {
                    throw new \Exception("Error al ejecutar la acción '".
                                        $arrayParametros['strAccion']."', por favor comuníquese con el departamento de Sistemas.");
                }
                $arrayRespuesta['status']       = $strStatus;
                $arrayRespuesta['message']      = "Se ejecutó la acción '".$arrayParametros['strAccion']."', correctamente.";
            }
            else
            {
                throw new \Exception("Solo es permitido iniciar una tarea en los siguientes estados: 'Asignada', 'Reprogramada'.");
            }
        }
        catch(\Exception $ex)
        {
            $arrayRespuesta['message'] = $ex->getMessage();
            $arrayRespuesta['status']  = "ERROR";
            $this->serviceUtil->insertError('TELCOS+',
                                            'SoporteSDService.putAccionTareaSD',
                                            $ex->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        return $arrayRespuesta;
    }

    /**
     * Documentación para la función 'putAccionSolicitudSD'.
     *
     * Función encargada de aprobar contrato, rechazar.
     *
     * @param array $arrayParametros [
     *                                "strPrefijoEmpresa"     => Prefijo de la empresa.
     *                                "intCodEmpresa"         => Código de la empresa.
     *                                "strAccion"             => Tipo de acción a ejecutar(aprobar,rechazar).
     *                                "intIdentificacionClt"  => Identificación del cliente.
     *                                "intIdSolicitudSD"      => Número de solicitud del portal SD.
     *                                "intIdUsrCreacion"      => Número de identificación del usuario logueado en el portal SD.
     *                                "strIpCreacion"         => IP del usuario logueado en el portal SD.
     *                               ]
     *
     * @return array $arrayResultado [
     *                                "message"      =>  Mensaje de respuesta.
     *                                "status"       =>  Estado de respuesta.
     *                               ]
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 17-08-2021
     *
     */
    public function putAccionSolicitudSD($arrayParametros)
    {
        $strIpCreacion           = (isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']))
                                    ? $arrayParametros['strIpCreacion'] : '127.0.0.1';
        $arrayRespuesta          = array();
        $strUsrCreacion          = "TelcoS+";
        $strStatus               = "EXITO";
        try
        {
            if(empty($arrayParametros) || !is_array($arrayParametros))
            {
                throw new \Exception("Datos incompletos para aprobar/rechazar.");
            }
            if(!isset($arrayParametros['strPrefijoEmpresa']) || empty($arrayParametros['strPrefijoEmpresa']))
            {
                throw new \Exception("La variable strPrefijoEmpresa es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intCodEmpresa']) || empty($arrayParametros['intCodEmpresa']))
            {
                throw new \Exception("La variable intCodEmpresa es un campo obligatorio.");
            }
            if(!isset($arrayParametros['strAccion']) || empty($arrayParametros['strAccion']))
            {
                throw new \Exception("La variable strAccion es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intIdentificacionClt']) || empty($arrayParametros['intIdentificacionClt']))
            {
                throw new \Exception("La variable intIdentificacionClt es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intIdSolicitudSD']) || empty($arrayParametros['intIdSolicitudSD']))
            {
                throw new \Exception("La variable intIdSolicitudSD es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intIdUsrCreacion']) || empty($arrayParametros['intIdUsrCreacion']))
            {
                throw new \Exception("La variable intIdUsrCreacion es un campo obligatorio.");
            }
            //Se obtiene los datos del empleado.
            $objPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                               ->findOneByIdentificacionCliente($arrayParametros['intIdUsrCreacion']);
            if(!is_object($objPersona) || !in_array($objPersona->getEstado(), array('Activo','Pendiente','Modificado')))
            {
                throw new \Exception('El usuario de creación no existe en TelcoS+ o no se encuentra Activo.');
            }
            $strUsrCreacion          = $objPersona->getLogin();
            //Se obtiene la características de solicitud SD.
            $objAdmiCaractSolicitudSD = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                             ->findOneBy(array('descripcionCaracteristica' => $this->strCaracSolicitudSD,
                                                               'estado'                    => $this->strEstadoActivo));
            if(empty($objAdmiCaractSolicitudSD) || !is_object($objAdmiCaractSolicitudSD))
            {
                throw new \Exception('No existe la característica: '.$this->strCaracSolicitudSD);
            }
            //Se obtiene los datos del pre-cliente.
            $objPersonaEmpresaRolClt = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                            ->findByIdentificacionTipoRolEmpresa($arrayParametros['intIdentificacionClt'],
                                                                                 'Pre-cliente',
                                                                                 $arrayParametros['intCodEmpresa']);
            if(empty($objPersonaEmpresaRolClt) || !is_object($objPersonaEmpresaRolClt))
            {
                //En caso de no tener el rol de pre-cliente, se consulta por el rol de cliente.
                $objPersonaEmpresaRolClt = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                ->findByIdentificacionTipoRolEmpresa($arrayParametros['intIdentificacionClt'],
                                                                                     'Cliente',
                                                                                     $arrayParametros['intCodEmpresa']);
                $boolEsCliente = false;
                if(empty($objPersonaEmpresaRolClt) || !is_object($objPersonaEmpresaRolClt))
                {
                    throw new \Exception('No se a ingresado el pre-cliente en TelcoS+.');
                }
                else
                {
                    $boolEsCliente = true;
                }
            }
            //Validación => Se valida que tenga una solicitud Activa para continuar el proceso.
            $boolSolicitudActiva         = false;
            $arrayPersonaEmpresaRolCarac = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                ->findBy(array('estado'              => $this->strEstadoActivo,
                                                               'personaEmpresaRolId' => $objPersonaEmpresaRolClt,
                                                               'caracteristicaId'    => $objAdmiCaractSolicitudSD));
            if(!empty($arrayPersonaEmpresaRolCarac) && is_array($arrayPersonaEmpresaRolCarac))
            {
                foreach($arrayPersonaEmpresaRolCarac as $objItem)
                {
                    $objCaracValor    = json_decode($objItem->getValor());
                    $intIdSolicitudSD = $objCaracValor->intIdSolicitudSD;
                    if(!empty($intIdSolicitudSD) && $intIdSolicitudSD == $arrayParametros['intIdSolicitudSD'])
                    {
                        $boolSolicitudActiva = true;
                    }
                }
            }
            if(!$boolSolicitudActiva)
            {
                throw new \Exception("No existe solicitud en estado 'Activo'.");
            }
            if(strtolower($arrayParametros['strAccion']) == "aprobar")
            {
                if(!$boolEsCliente)
                {
                    $objContrato = $this->emComercial->getRepository('schemaBundle:InfoContrato')
                                        ->findOneByPersonaEmpresaRolId($objPersonaEmpresaRolClt);
                    if(empty($objContrato) || !is_object($objContrato))
                    {
                        throw new \Exception('No existe contrato con los datos del pre-cliente enviados por parámetros.');
                    }
                    if($objContrato->getEstado() != "Pendiente")
                    {
                        throw new \Exception('No existe contrato en estado Pendiente.');
                    }
                    $objDatosClt = $this->serviceContrato
                                        ->getDatosPersonaId($objPersonaEmpresaRolClt->getPersonaId()->getId());
                    if(empty($objDatosClt) || !is_object($objDatosClt))
                    {
                        throw new \Exception("No existe pre-cliente ingresado en TelcoS+, con la identificación: '".
                                             $arrayParametros['intIdentificacionClt']."'");
                    }
                    //Se configura los parámetros necesarios para la aprobación de contrato.
                    $arrayPersona                           = array();
                    $arrayPersona['tipoEmpresa']            = $objDatosClt->getTipoEmpresa();
                    $arrayPersona['tipoIdentificacion']     = $objDatosClt->getTipoIdentificacion();
                    $arrayPersona['tipoTributario']         = $objDatosClt->getTipoTributario();
                    $arrayPersona['nacionalidad']           = $objDatosClt->getNacionalidad();
                    $arrayPersona['origenIngresos']         = $objDatosClt->getOrigenIngresos();
                    $arrayPersona['tieneCarnetConadis']     = "N";
                    if($objDatosClt->getNumeroConadis() !=null && $objDatosClt->getNumeroConadis() !="" )
                    {
                        $arrayPersona['tieneCarnetConadis'] = "S";
                        $arrayPersona['numeroConadis']      = $objDatosClt->getNumeroConadis();
                    }
                    
                    $arrayPersona['identificacionCliente']  = $objDatosClt->getIdentificacionCliente();
                    $arrayPersona['razonSocial']            = $objDatosClt->getRazonSocial();
                    $arrayPersona['representanteLegal']     = $objDatosClt->getRepresentanteLegal();
                    $arrayPersona['genero']                 = $objDatosClt->getGenero();
                    $arrayPersona['tituloId']               = ($objDatosClt->getTituloId() !=null && 
                                                               $objDatosClt->getTituloId() !="" ) ? 
                                                               $objDatosClt->getTituloId()->getId():null;
                    $arrayPersona['nombres']                = $objDatosClt->getNombres();
                    $arrayPersona['apellidos']              = $objDatosClt->getApellidos();
                    $arrayPersona['estadoCivil']            = $objDatosClt->getEstadoCivil();
                    $arrayPersona['fechaNacimiento']        = array("year"  => $objDatosClt->getFechaNacimiento() ? 
                                                                            strval(date_format($objDatosClt->getFechaNacimiento(), "Y")) : '',
                                                                    "month" => $objDatosClt->getFechaNacimiento() ? 
                                                                            strval(date_format($objDatosClt->getFechaNacimiento(), "m")) : '',
                                                                    "day"   => $objDatosClt->getFechaNacimiento() ? 
                                                                            strval(date_format($objDatosClt->getFechaNacimiento(), "d")) : '');
                    $arrayPersona['contribuyenteEspecial']  = $objDatosClt->getContribuyenteEspecial();
                    $arrayPersona['pagaIva']                = $objDatosClt->getPagaIva();
                    $arrayPersona['direccion']              = $objDatosClt->getDireccion();
                    $arrayPersona['direccionTributaria']    = $objDatosClt->getDireccionTributaria();
                    $arrayPersona['calificacionCrediticia'] = $objDatosClt->getCalificacionCrediticia();
                    $arrayPersona['empresaId']              = $arrayParametros['intCodEmpresa'];
                    $arrayPersona['oficinaFacturacion']     = ($objPersonaEmpresaRolClt->getOficinaId() !=null && 
                                                               $objPersonaEmpresaRolClt->getOficinaId() !="" ) ? 
                                                               $this->emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                                                    ->find($objPersonaEmpresaRolClt->getOficinaId()):null;
                    $arrayPersona['esPrepago']              = ($objPersonaEmpresaRolClt->getEsPrepago() !=null && 
                                                               $objPersonaEmpresaRolClt->getEsPrepago() !='' ) ?
                                                               $objPersonaEmpresaRolClt->getEsPrepago() : null;
                    $objCliente = $this->serviceContrato->getClientesPorIdentificacion($objDatosClt->getIdentificacionCliente(),
                                                                                       $arrayParametros['intCodEmpresa']);
                    if(!empty($objCliente) || is_object($objCliente))
                    {
                        throw new \Exception('Ya existe un cliente con la misma identificación, por favor corregir y volver a intentar');
                    }
                    $arrayParametrosContrato = array();
                    $arrayParametrosContrato['intIdContrato']     = $objContrato->getId();
                    $arrayParametrosContrato['arrayPersona']      = $arrayPersona;
                    $arrayParametrosContrato['arrayPersonaExtra'] = array("direccionTributaria" => $objDatosClt->getDireccionTributaria());
                    $intIdTipoCuenta                              = "";
                    $arrayFormaPago                               = array("numeroCtaTarjeta"   => "",
                                                                        "mesVencimiento"     => "",
                                                                        "codigoVerificacion" => "",
                                                                        "bancoTipoCuentaId"  => "",
                                                                        "titularCuenta"      => "",
                                                                        "anioVencimiento"    => "");
                    $objContratoFormaPago                         = $this->emComercial
                                                                         ->getRepository('schemaBundle:InfoContratoFormaPago')
                                                                         ->findOneBy(array("contratoId"   => $objContrato->getId()));
                    if(!empty($objContratoFormaPago) && is_object($objContratoFormaPago))
                    {
                        $arrayFormaPago['numeroCtaTarjeta']   = $this->serviceCrypt->descencriptar($objContratoFormaPago->getNumeroCtaTarjeta());
                        $arrayFormaPago['mesVencimiento']     = $objContratoFormaPago->getMesVencimiento();
                        $arrayFormaPago['codigoVerificacion'] = $objContratoFormaPago->getCodigoVerificacion();
                        $arrayFormaPago['bancoTipoCuentaId']  = ($objContratoFormaPago->getBancoTipoCuentaId() !=null && 
                                                                $objContratoFormaPago->getBancoTipoCuentaId() !='' ) ?
                                                                $objContratoFormaPago->getBancoTipoCuentaId()->getId() : null;
                        $arrayFormaPago['titularCuenta']      = $objContratoFormaPago->getTitularCuenta();
                        $arrayFormaPago['anioVencimiento']    = $objContratoFormaPago->getAnioVencimiento();
                        $intIdTipoCuenta                      = ($objContratoFormaPago->getTipoCuentaId() !=null && 
                                                                $objContratoFormaPago->getTipoCuentaId() !='' ) ?
                                                                $objContratoFormaPago->getTipoCuentaId()->getId() : null;
                    }
                    $arrayParametrosContrato['arrayFormaPago']    = $arrayFormaPago;
                    $arrayParametrosContrato['intIdFormaPago']    = ($objContrato->getFormaPagoId() !=null && 
                                                                     $objContrato->getFormaPagoId() !='' ) ?
                                                                     $objContrato->getFormaPagoId()->getId() : null;
                    $arrayParametrosContrato['intIdTipoCuenta']   = $intIdTipoCuenta;
                    $arrayParametrosServicioEstado                = array('Rechazado','Rechazada','Cancelado','Anulado',
                                                                          'Cancel','Eliminado','Reubicado','Trasladado');
                    $arrayServicios                               = $this->serviceContrato
                                                                         ->getTodosServiciosXEstadoTn($objPersonaEmpresaRolClt->getId(),
                                                                                                      0,
                                                                                                      10000,
                                                                                                      $arrayParametrosServicioEstado);
                    if((!empty($arrayServicios) && is_array($arrayServicios)) &&
                        (isset($arrayServicios["registros"]) && !empty($arrayServicios["registros"])))
                    {
                        foreach($arrayServicios['registros'] as $objItem)
                        {
                            $arrayItemServicios[] = $objItem->getId();
                        }
                    }
                    else
                    {
                        throw new \Exception('No existen servicios.');
                    }
                    $arrayParametrosContrato['arrayServicios']    = $arrayItemServicios;
                    $arrayParametrosContrato['strUsrCreacion']    = $strUsrCreacion;
                    $arrayParametrosContrato['strIpCreacion']     = $strIpCreacion;
                    $arrayParametrosContrato['strPrefijoEmpresa'] = $arrayParametros['strPrefijoEmpresa'];
                    $arrayParametrosContrato['strEmpresaCod']     = $arrayParametros['intCodEmpresa'];
                    $arrayRespuestaAprobarContrato                = $this->serviceContrato->guardarProcesoAprobContrato($arrayParametrosContrato);
                    if((!empty($arrayRespuestaAprobarContrato) && is_array($arrayRespuestaAprobarContrato)) &&
                       (array_key_exists('status',$arrayRespuestaAprobarContrato) && $arrayRespuestaAprobarContrato['status']=='ERROR_SERVICE'))
                    {
                        throw new \Exception($arrayRespuestaAprobarContrato['mensaje']);
                    }
                    else
                    {
                        $this->emComercial->getConnection()->beginTransaction();
                        foreach($arrayItemServicios as $intIdServicio)
                        {
                            $objServicio = $this->serviceContrato->getDatosServicioId($intIdServicio);
                            if(is_object($objServicio) && !empty($objServicio))
                            {
                                $objServCaractTipoProy = $this->serviceTecnico
                                                              ->getServicioProductoCaracteristica($objServicio,
                                                                                                  'TIPO_PROYECTO',
                                                                                                  $objServicio->getProductoId());
                                if(is_object($objServCaractTipoProy) && !empty($objServCaractTipoProy))
                                {
                                    $objTipoSolicitudProyecto = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                                  ->findOneBy(array("descripcionSolicitud" => "SOLICITUD DE PROYECTO",
                                                                                                    "estado"               => "Activo"));
                                    if(is_object($objTipoSolicitudProyecto) && !empty($objTipoSolicitudProyecto))
                                    {
                                        $strObservacionSol     = "Se crea Solicitud para crear un proyecto en TelcoCRM.";
                                        $objDetTipoSolProyecto = new InfoDetalleSolicitud();
                                        $objDetTipoSolProyecto->setServicioId($objServicio);
                                        $objDetTipoSolProyecto->setTipoSolicitudId($objTipoSolicitudProyecto);
                                        $objDetTipoSolProyecto->setObservacion($strObservacionSol);
                                        $objDetTipoSolProyecto->setFeCreacion(new \DateTime('now'));
                                        $objDetTipoSolProyecto->setUsrCreacion($objServicio->getUsrCreacion());
                                        $objDetTipoSolProyecto->setEstado('Pendiente');
                                        $this->emComercial->persist($objDetTipoSolProyecto);
                                        $this->emComercial->flush();
                                        $objDetTipoSolProyectoHist = new InfoDetalleSolHist();
                                        $objDetTipoSolProyectoHist->setDetalleSolicitudId($objDetTipoSolProyecto);
                                        $objDetTipoSolProyectoHist->setEstado($objDetTipoSolProyecto->getEstado());
                                        $objDetTipoSolProyectoHist->setFeCreacion(new \DateTime('now'));
                                        $objDetTipoSolProyectoHist->setUsrCreacion($objServicio->getUsrCreacion());
                                        $objDetTipoSolProyectoHist->setObservacion("Se crea Solicitud de Proyecto");
                                        $objDetTipoSolProyectoHist->setIpCreacion($ipCreacion);
                                        $this->emComercial->persist($objDetTipoSolProyectoHist);
                                        $this->emComercial->flush();
                                    }
                                }
                            }
                        }
                        if($this->emComercial->getConnection()->isTransactionActive())
                        {
                            $this->emComercial->getConnection()->commit();
                            $this->emComercial->getConnection()->close();
                        }
                    }
                }
                $this->emComercial->getConnection()->beginTransaction();
                $arrayPersonaEmpresaRolCarac = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                 ->findBy(array('estado'              => $this->strEstadoActivo,
                                                                                'personaEmpresaRolId' => $objPersonaEmpresaRolClt,
                                                                                'caracteristicaId'    => $objAdmiCaractSolicitudSD));
                if(!empty($arrayPersonaEmpresaRolCarac) && is_array($arrayPersonaEmpresaRolCarac))
                {
                    foreach($arrayPersonaEmpresaRolCarac as $objItem)
                    {
                        $objCaracValor    = json_decode($objItem->getValor());
                        $intIdSolicitudSD = $objCaracValor->intIdSolicitudSD;
                        if(!empty($intIdSolicitudSD) && $intIdSolicitudSD == $arrayParametros['intIdSolicitudSD'])
                        {
                            $objItem->setEstado($this->strEstadoAprobado);
                            $this->emComercial->persist($objItem);
                            $this->emComercial->flush();
                        }
                    }
                }
            }
            elseif(strtolower($arrayParametros['strAccion']) == "rechazar")
            {
                $this->emComercial->getConnection()->beginTransaction();
                $arrayPersonaEmpresaRolCarac = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                 ->findBy(array('estado'              => $this->strEstadoActivo,
                                                                                'personaEmpresaRolId' => $objPersonaEmpresaRolClt,
                                                                                'caracteristicaId'    => $objAdmiCaractSolicitudSD));
                if(!empty($arrayPersonaEmpresaRolCarac) && is_array($arrayPersonaEmpresaRolCarac))
                {
                    foreach($arrayPersonaEmpresaRolCarac as $objItem)
                    {
                        $objCaracValor    = json_decode($objItem->getValor());
                        $intIdSolicitudSD = $objCaracValor->intIdSolicitudSD;
                        if(!empty($intIdSolicitudSD) && $intIdSolicitudSD == $arrayParametros['intIdSolicitudSD'])
                        {
                            $objItem->setEstado($this->strEstadoInactivo);
                            $this->emComercial->persist($objItem);
                            $this->emComercial->flush();
                        }
                    }
                }
            }
            else
            {
                throw new \Exception('Acción a ejecutar, no existe.');
            }
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->commit();
                $this->emComercial->getConnection()->close();
            }
            $arrayRespuesta['status']       = $strStatus;
            $arrayRespuesta['message']      = "Se ejecutó la acción '".$arrayParametros['strAccion']."', correctamente.";
        }
        catch(\Exception $ex)
        {
            $arrayRespuesta['message'] = $ex->getMessage();
            $arrayRespuesta['status']  = "ERROR";
            $this->serviceUtil->insertError('TELCOS+',
                                            'SoporteSDService.putAccionSolicitudSD',
                                            $ex->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }
        }
        return $arrayRespuesta;
    }

    /**
     * Documentación para la función 'putSubirDocumentosSD'.
     *
     * Función encargada de subir los archivos a TelcoS+ y Gestor Documental.
     *
     * @param array $arrayParametros [
     *                                "strPrefijoEmpresa"     => Prefijo de la empresa.
     *                                "intCodEmpresa"         => Código de la empresa.
     *                                "intIdentificacionClt"  => Identificación del cliente.
     *                                "intIdSolicitudSD"      => Número de solicitud del portal SD.
     *                                "intIdUsrCreacion"      => Número de identificación del usuario logueado en el portal SD.
     *                                "strIpCreacion"         => IP del usuario logueado en el portal SD.
     *                                "strTipoDocumento"      => Tipo del documento('ORDEN DE SERVICIO',
     *                                                                              'ADEMDUM',
     *                                                                              'ESCRITURA',
     *                                                                              'CONTRATO',
     *                                                                              'CÉDULA REPRESENTANTE',
     *                                                                              'NOMBRAMIENTO',
     *                                                                              'RUC',
     *                                                                              'CARTA DE COMPROMISO',
     *                                                                              'CÓDIGO DE CONDUCTA' ).
     *                                "fileName"              => Nombre del archivo, sin extensiion.
     *                                "fileExtension"         => Extensión del archivo.
     *                                "file"                  => Archivo en Base64.
     *                               ]
     *
     * @return array $arrayResultado [
     *                                "message"      =>  Mensaje de respuesta.
     *                                "status"       =>  Estado de respuesta.
     *                               ]
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 17-08-2021
     *
     */
    public function putSubirDocumentosSD($arrayParametros)
    {
        $strIpCreacion           = (isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']))
                                    ? $arrayParametros['strIpCreacion'] : '127.0.0.1';
        $arrayRespuesta          = array();
        $arrayParametrosGD       = array();
        $strUsrCreacion          = "TelcoS+";
        $strStatus               = "EXITO";
        $strModulo               = "COMERCIAL";
        $strModuloNfs            = "GestionDocumentosComercial";
        $strApp                  = "TelcosWeb";
        $strMensaje              = "Se creó el documento en TelcoS+, correctamente.";
        try
        {
            if(empty($arrayParametros) || !is_array($arrayParametros))
            {
                throw new \Exception("Datos incompletos para subir los archivos a TelcoS+ y Gestor Documental.");
            }
            if(!isset($arrayParametros['strPrefijoEmpresa']) || empty($arrayParametros['strPrefijoEmpresa']))
            {
                throw new \Exception("La variable strPrefijoEmpresa es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intCodEmpresa']) || empty($arrayParametros['intCodEmpresa']))
            {
                throw new \Exception("La variable intCodEmpresa es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intIdentificacionClt']) || empty($arrayParametros['intIdentificacionClt']))
            {
                throw new \Exception("La variable intIdentificacionClt es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intIdSolicitudSD']) || empty($arrayParametros['intIdSolicitudSD']))
            {
                throw new \Exception("La variable intIdSolicitudSD es un campo obligatorio.");
            }
            if(!isset($arrayParametros['intIdUsrCreacion']) || empty($arrayParametros['intIdUsrCreacion']))
            {
                throw new \Exception("La variable intIdUsrCreacion es un campo obligatorio.");
            }
            //Se obtiene los datos del empleado.
            $objPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                               ->findOneByIdentificacionCliente($arrayParametros['intIdUsrCreacion']);
            if(!is_object($objPersona) || !in_array($objPersona->getEstado(), array('Activo','Pendiente','Modificado')))
            {
                throw new \Exception('El usuario de creación no existe en TelcoS+ o no se encuentra Activo.');
            }
            $arrayUsCreador = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                   ->getInfoDatosPersona(array('strRol'                     => 'Empleado',
                                                               'strPrefijo'                 => $arrayParametros['strPrefijoEmpresa'],
                                                               'strEstadoPersona'           => array('Activo',
                                                                                                     'Pendiente',
                                                                                                     'Modificado'),
                                                               'strEstadoPersonaEmpresaRol' => $this->strEstadoActivo,
                                                               'strLogin'                   => $objPersona->getLogin()));
            if($arrayUsCreador['status'] === 'fail')
            {
                throw new \Exception('Error al obtener los datos del empleado, por favor comuníquese con el departamento de Sistemas.');
            }
            if($arrayUsCreador['status'] === 'ok' && empty($arrayUsCreador['result']))
            {
                throw new \Exception('Los filtros para encontrar el empleado son incorrectos o el empleado no existe en TelcoS+');
            }
            //Por medio de la región del empleado, se obtiene el usuario de cobranza.
            $arrayUsCobranza = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->get('PARAMETROS_SECURITY_DATA',
                                          'COMERCIAL',
                                          'CONTRATO_DIGITAL_SD',
                                          'LISTA_USUARIO_COBRANZA',
                                          '',
                                          $arrayUsCreador['result'][0]['region'],
                                          '',
                                          '',
                                          '',
                                          $arrayParametros['intCodEmpresa']);
            if(empty($arrayUsCobranza) || !is_array($arrayUsCobranza))
            {
                throw new \Exception("No existe usuario de cobranza asignado para la región: ".$arrayUsCreador['result'][0]['region']);
            }
            $strUsrCreacion = $arrayUsCobranza[0]["valor1"];
            //Se obtiene los datos del cliente.
            $objPersonaEmpresaRolClt = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                            ->findByIdentificacionTipoRolEmpresa($arrayParametros['intIdentificacionClt'],
                                                                                 'Cliente',
                                                                                 $arrayParametros['intCodEmpresa']);
            if(empty($objPersonaEmpresaRolClt) || !is_object($objPersonaEmpresaRolClt))
            {
                throw new \Exception('No se a ingresado el pre-cliente en TelcoS+.');
            }
            if(!isset($arrayParametros['strTipoDocumento']) || empty($arrayParametros['strTipoDocumento']))
            {
                throw new \Exception("La variable strTipoDocumento es un campo obligatorio.");
            }
            if(!isset($arrayParametros['file']) || empty($arrayParametros['file']))
            {
                throw new \Exception("La variable file es un campo obligatorio.");
            }
            if(!isset($arrayParametros['fileName']) || empty($arrayParametros['fileName']))
            {
                throw new \Exception("La variable fileName es un campo obligatorio.");
            }
            if(!isset($arrayParametros['fileExtension']) || empty($arrayParametros['fileExtension']))
            {
                throw new \Exception("La variable fileExtension es un campo obligatorio.");
            }
            $this->emComercial->getConnection()->beginTransaction();
            //Se obtiene el contrato del cliente.
            $objContrato = $this->emComercial->getRepository('schemaBundle:InfoContrato')
                                ->findOneByPersonaEmpresaRolId($objPersonaEmpresaRolClt);
            if(empty($objContrato) || !is_object($objContrato))
            {
                throw new \Exception('No existe contrato con los datos del cliente enviados por parámetros.');
            }
            //Se valida que el tipo de extensión del documento exista.
            $objTipoDoc = $this->emComercial->getRepository('schemaBundle:AdmiTipoDocumento')
                                            ->findOneBy(array("extensionTipoDocumento" => strtoupper($arrayParametros['fileExtension'])));
            if(!is_object($objTipoDoc) || empty($objTipoDoc))
            {
                throw new \Exception("No se encontró tipo de extensión, con el parámetro: '".$arrayParametros['fileExtension']."', enviado.");
            }
            //Se valida el tipo de documento a ingresar
            $objTipoDocGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                 ->findOneBy(array("descripcionTipoDocumento" => strtoupper($arrayParametros['strTipoDocumento'])));
            if(!is_object($objTipoDocGeneral) || empty($objTipoDocGeneral))
            {
                throw new \Exception("No se encontró tipo de documento, con el parámetro: '".$arrayParametros['strTipoDocumento']."', enviado.");
            }
            $strPrefijo = substr(md5(uniqid(rand())),0,6);
            $strNuevoNombreTmp    = $arrayParametros['fileName'] . "_" . $strPrefijo . "." . strtolower($objTipoDoc->getExtensionTipoDocumento());
            // Se reemplazan caracteres que no cumplen con el patrón definido para el nombre del archivo.
            $strPatronABuscar     = '/[^a-zA-Z0-9._-]/';
            $strCaracterReemplazo = '_';
            $strNuevoNombre       = preg_replace($strPatronABuscar,$strCaracterReemplazo,$strNuevoNombreTmp);
            $arrayParamNfs        = array('prefijoEmpresa'       => $arrayParametros['strPrefijoEmpresa'],
                                          'strApp'               => $strApp ,
                                          'arrayPathAdicional'   => [],
                                          'strBase64'            => $arrayParametros['file'],
                                          'strNombreArchivo'     => $strNuevoNombre,
                                          'strUsrCreacion'       => $strUsrCreacion,
                                          'strSubModulo'         => $strModuloNfs);
            //Se consume Service de subir archivos al NFS.
            $arrayRespNfs = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
            if(isset($arrayRespNfs) && $arrayRespNfs['intStatus'] == 200)
            {
                $arrayParametrosLog['enterpriseCode']   = $arrayParametros['intCodEmpresa']; 
                $arrayParametrosLog['logType']          = "0";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = "TELCOS";
                $arrayParametrosLog['appClass']         = "SoporteSDService";
                $arrayParametrosLog['appMethod']        = "putSubirDocumentosSD";
                $arrayParametrosLog['messageUser']      = "No aplica.";
                $arrayParametrosLog['status']           = "Exitoso";
                $arrayParametrosLog['descriptionError'] = "Se guarda archivo correctamente atravez de microservicio de Nfs (".
                                                          $arrayRespNfs['strUrlArchivo'].")";
                $arrayParametrosLog['inParameters']     = json_encode($arrayRespNfs);
                $arrayParametrosLog['creationUser']     = "TELCOS";
                $this->serviceUtil->insertLog($arrayParametrosLog);
            }
            else
            {
                throw new \Exception('Error al subir archivo, por favor comuníquese con el departamento de Sistemas.');
            }
            //Se ingresa el documento en TelcoS+
            $objDocumento   = new InfoDocumento();
            $objDocumento->setTipoDocumentoId($objTipoDoc);
            $objDocumento->setTipoDocumentoGeneralId($objTipoDocGeneral->getId());
            $objDocumento->setNombreDocumento($arrayParametros['intIdentificacionClt']."_".$arrayParametros['intIdSolicitudSD']);
            $objDocumento->setUbicacionFisicaDocumento($arrayRespNfs['strUrlArchivo']);
            $objDocumento->setUbicacionLogicaDocumento($strNuevoNombre);
            if($objContrato->getNumeroContrato() != "" && $objContrato->getNumeroContrato() != null)
            {
                $objDocumento->setContratoId($objContrato->getId());
                $objDocumento->setMensaje("Archivo agregado al contrato # ".$objContrato->getNumeroContrato());
            }
            $objDocumento->setEstado($this->strEstadoActivo);
            $objDocumento->setFeCreacion(new \DateTime('now'));
            $objDocumento->setFechaDocumento(new \DateTime('now'));
            $objDocumento->setIpCreacion($strIpCreacion);
            $objDocumento->setUsrCreacion($strUsrCreacion);
            $objDocumento->setEmpresaCod($arrayParametros['intCodEmpresa']);
            $this->emComercial->persist($objDocumento);
            $this->emComercial->flush();
            $objDocRelacion = new InfoDocumentoRelacion();
            $objDocRelacion->setModulo($strModulo);
            $objDocRelacion->setEstado($this->strEstadoActivo);
            $objDocRelacion->setFeCreacion(new \DateTime('now'));
            $objDocRelacion->setUsrCreacion($strUsrCreacion);
            if($objContrato->getNumeroContrato() != "" && $objContrato->getNumeroContrato() != null)
            {
                $objDocRelacion->setContratoId($objContrato->getId());
            }
            $objDocRelacion->setDocumentoId($objDocumento->getId());
            $this->emComercial->persist($objDocRelacion);
            $this->emComercial->flush();
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->commit();
                $this->emComercial->getConnection()->close();
            }
            //Se ingresa el documento en Gestor Documental
            $arrayParametrosGD["strPrefijoEmpresa"]     = $arrayParametros['strPrefijoEmpresa'];
            $arrayParametrosGD["intCodEmpresa"]         = $arrayParametros['intCodEmpresa'];
            $arrayParametrosGD["strNombreDocumento"]    = $arrayParametros['intIdentificacionClt']."_".$arrayParametros['intIdSolicitudSD'];
            $arrayParametrosGD["strLoginUsuario"]       = $strUsrCreacion;
            $arrayParametrosGD["strIpCreacion"]         = $strIpCreacion;
            $arrayParametrosGD["strUbicacionArchivo"]   = $arrayRespNfs['strUrlArchivo'];
            $arrayParametrosGD["strTipoDocumento"]      = (strtoupper($arrayParametros['strTipoDocumento']) == "CONTRATO") ?
                                                           "CONTRATO CLIENTE" : $arrayParametros['strTipoDocumento'];
            $arrayParametrosGD["strRazonSocial"]        = ($objPersonaEmpresaRolClt->getPersonaId()->getRazonSocial() != "") ? 
                                                           $objPersonaEmpresaRolClt->getPersonaId()->getRazonSocial() : 
                                                           $objPersonaEmpresaRolClt->getPersonaId()->getNombres()." ".
                                                           $objPersonaEmpresaRolClt->getPersonaId()->getApellidos();
            $arrayParametrosGD["strRuc"]                = $arrayParametros['intIdentificacionClt'];
            $arrayParametrosGD["strNumContrato"]        = $objContrato->getNumeroContrato();
            $arrayParametrosGD["strDatabaseDsn"]        = $this->strDatabaseDsn;
            $arrayParametrosGD["strUserDocumental"]     = $this->strUserDocumental;
            $arrayParametrosGD["strPasswordDocumental"] = $this->strPasswordDocumental;
            $arrayRespuestaGD                           = $this->emComercial->getRepository('schemaBundle:AdmiTipoDocumento')
                                                               ->putSubirArchivoGD($arrayParametrosGD);
            if(isset($arrayRespuestaGD["status"]) && !empty($arrayRespuestaGD["status"]) && $arrayRespuestaGD["status"] == "EXITO")
            {
                $strMensaje = "Se creó el documento en TelcoS+ y Gestor Documental, correctamente.";
            }
            $arrayRespuesta['message']      = $strMensaje;
            $arrayRespuesta['status']       = $strStatus;
        }
        catch(\Exception $ex)
        {
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }
            $arrayRespuesta['message'] = $ex->getMessage();
            $arrayRespuesta['status']  = "ERROR";
            $this->serviceUtil->insertError('TELCOS+',
                                            'SoporteSDService.putSubirDocumentosSD',
                                            $ex->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        return $arrayRespuesta;
    }
}
