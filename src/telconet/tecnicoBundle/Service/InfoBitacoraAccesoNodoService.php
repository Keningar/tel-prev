<?php

namespace telconet\tecnicoBundle\Service;

use Error;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use telconet\schemaBundle\Entity\InfoBitacoraAccesoNodo;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;

/**
 * Class Service InfoBitacoraAccesoNodo
 *
 * Clase donde se implementa servicios de acceso a nodos
 *
 */
class InfoBitacoraAccesoNodoService
{
    /**
     *
     * @var \telconet\schemaBundle\Service\MailerService
     */
    private $serviceMailer;

    private $objRequest;
    private $templating;
    private $emCommercial;
    private $emCommunication;
    private $emInfrastructure;
    private $emGeneral;
    private $emSupport;

    private $serviceSupport;
    private $serviceUtil;

    private $strUser;
    private $strUserIp;
    private $strEmpresaCod;

    private $strModeAuthNW;
    private $strUrlAuthNW;
    private $strUserAuthNW;
    private $strPasswordAuthNW;

    public function setDependencies(Container $objContainer)
    {
        $this->objRequest = $objContainer->get('request');
        $this->emCommercial = $objContainer->get('doctrine')->getManager('telconet');
        $this->emCommunication = $objContainer->get('doctrine')->getManager('telconet_comunicacion');
        $this->emInfrastructure = $objContainer->get('doctrine')->getManager('telconet_infraestructura');
        $this->emGeneral = $objContainer->get('doctrine')->getManager('telconet_general');
        $this->emSupport = $objContainer->get('doctrine')->getManager('telconet_soporte');
        $this->serviceSupport = $objContainer->get('soporte.SoporteService');
        $this->serviceUtil = $objContainer->get('schema.Util');
        $this->strUser = $this->objRequest->getSession()->get('user');
        $this->strUserIp = $this->objRequest->getClientIp();
        $this->strEmpresaCod = $this->objRequest->getSession()->get('idEmpresa');
        $this->strModeAuthNW = $objContainer->getParameter('api_networking_mode');
        $this->strUrlAuthNW = $objContainer->getParameter('api_networking_url')[$this->strModeAuthNW];
        $this->strUserAuthNW = $objContainer->getParameter('api_networking_user');
        $this->strPasswordAuthNW = $objContainer->getParameter('api_networking_pass');
        $this->serviceMailer = $objContainer->get('mailer');
        $this->templating  = $objContainer->get('templating');
    }

    /**
     * Obtiene todos los registros aplicando los filtros correspondiente
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0 01-04-2021
     */
    public function getBitacoras($arrayParams)
    {
        try
        {
            $arrayResult = array();
            $objIBANRepository = $this
                ->emInfrastructure
                ->getRepository('schemaBundle:InfoBitacoraAccesoNodo');
            $arrayResult = $objIBANRepository->findByParams($arrayParams);
            $arrayResultCount = $objIBANRepository->findByParamsCount($arrayParams);
            $intCountResult = count($arrayResultCount);
            $arrayNodeAccesLog = array();
            
            if (!empty($arrayParams['elementoRelacionado']))
            {
                foreach($arrayResult as $objNodeAccessLog)
                {
                    if ((strpos($objNodeAccessLog->getElemento(),$arrayParams['elementoRelacionado']))!==false)
                    {
                        $arrayNodeAccesLog[] = array(
                            'id' => $objNodeAccessLog->getId(),
                            'elementoNodoNombre' => $objNodeAccessLog->getElementoNodoNombre(),
                            'departamentoNombre' => $objNodeAccessLog->getDepartamento(),
                            'canton' => $objNodeAccessLog->getCanton(),
                            'motivo' => $objNodeAccessLog->getMotivo(),
                            'observacion' => $objNodeAccessLog->getObservacion(),
                            'tareaId' => $objNodeAccessLog->getTareaId(),
                            'estado' => $objNodeAccessLog->getEstado(),
                            'tecnicoAsignado' => $objNodeAccessLog->getTecnicoAsignado(),
                            'telefono' => $objNodeAccessLog->getTelefono(),
                            'feCreacion' => $objNodeAccessLog->getFeCreacion(),
                            'feUltMod' => $objNodeAccessLog->getFeUltMod(),
                            'codigos' => $objNodeAccessLog->getCodigos(),
                            'elemento' => $objNodeAccessLog->getElemento(),
                            'usrCreacion' => $objNodeAccessLog->getUsrCreacion(),
                            'usrUltMod' => $objNodeAccessLog->getusrUltMod()
                           );
                    }
                    $intCountResult = count($arrayNodeAccesLog);
                }
            }else
            {
                foreach($arrayResult as $objNodeAccessLog)
                {
                 $arrayNodeAccesLog[] = array(
                     'id' => $objNodeAccessLog->getId(),
                     'elementoNodoNombre' => $objNodeAccessLog->getElementoNodoNombre(),
                     'departamentoNombre' => $objNodeAccessLog->getDepartamento(),
                     'canton' => $objNodeAccessLog->getCanton(),
                     'motivo' => $objNodeAccessLog->getMotivo(),
                     'observacion' => $objNodeAccessLog->getObservacion(),
                     'tareaId' => $objNodeAccessLog->getTareaId(),
                     'estado' => $objNodeAccessLog->getEstado(),
                     'tecnicoAsignado' => $objNodeAccessLog->getTecnicoAsignado(),
                     'telefono' => $objNodeAccessLog->getTelefono(),
                     'feCreacion' => $objNodeAccessLog->getFeCreacion(),
                     'feUltMod' => $objNodeAccessLog->getFeUltMod(),
                     'codigos' => $objNodeAccessLog->getCodigos(),
                     'elemento' => $objNodeAccessLog->getElemento(),
                     'usrCreacion' => $objNodeAccessLog->getUsrCreacion(),
                     'usrUltMod' => $objNodeAccessLog->getusrUltMod()
                    );
                }
            }
         
            $objResponse = array(
              'total' => $intCountResult,
              'data' => $arrayNodeAccesLog
            );
        } catch(\Exception $e)
        {
            $this
                ->serviceUtil
                ->insertError('Telcos+', 
                    'InfoBitacoraAccesoNodoService.getBitacoras', 
                    $e->getMessage(), 
                    $this->strUser,
                    $this->strUserIp);

            $objResponse = array(
                'success' => false, 
                'mensaje' => 'Error al consultar bitácoras. Notificar con Sistemas');
        }

        return $objResponse;
    }

    /**
     * Obtiene todos las tareas aplicando los filtros correspondiente
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0 01-04-2021
     */
    public function getTareas($arrayParams)
    {
        $arrayResponse = array();

        try
        {
            $objICRepository = $this
                ->emSupport
                ->getRepository('schemaBundle:InfoComunicacion');

            $arrayResponse = $objICRepository->findByParams($arrayParams);
        } catch(\Exception $e)
        {
            $this
                ->serviceUtil
                ->insertError('Telcos+', 
                    'InfoBitacoraAccesoNodoService.getTareas', 
                    $e->getMessage(), 
                    $this->strUser,
                    $this->strUserIp);

            $arrayResponse = array(
                'success' => false, 
                'mensaje' => 'Error al consultar tareas. Notificar con Sistemas');
        }

        return $arrayResponse;
    }

    /**
     * Obtiene la informacion del nodo buscado
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0 16-02-2023
     */
    public function getElementoBitacora($arrayParams)
    {
        $arrayResponse = array();
        try
        {
            $objIERepository = $this->emInfrastructure->getRepository('schemaBundle:InfoElemento');
            $arrayResponse = $objIERepository->getElementoBitacora($arrayParams);
        } catch(\Exception $e)
        {
            $this
                ->serviceUtil
                ->insertError('Telcos+', 
                    'InfoBitacoraAccesoNodoService.getElementoBitacora', 
                    $e->getMessage(), 
                    $this->strUser,
                    $this->strUserIp);

            $arrayResponse = array(
                'success' => false, 
                'mensaje' => 'Error al consultar tareas. Notificar con Sistemas');
        }

        return $arrayResponse;
    }

    /**
     * Obtiene toda la informacion de la tarea
     * @author Jeampier Carriel <jrealpe@telconet.ec>
     * @version 1.0 30-11-2022
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.1 - 08-02-2023. Se obtiene afectadoId de las tareas de caso
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.2 - 11-04-2023. Se valida que el elementoId exista.
     * 
     */
    public function getTareaDetalle($intIdTarea)
    {
        $arrayResponse = array();

        try
        {
            $objICRepository = $this
                ->emSupport
                ->getRepository('schemaBundle:InfoComunicacion');

            $arrayResponse = $objICRepository->obtenerDatosTareaBitacora($intIdTarea);
            if(count($arrayResponse) == 0)
            {
                $arrayResponse = $objICRepository->obtenerDatosTareaBitacorasinElemento($intIdTarea);
                if(!empty($arrayResponse))
                {
                    $objIS = null;
                    $objIST = null;
                    if (!empty($arrayResponse['result']['detalle_id']))
                    {
                        $objIS = $this->emCommercial->getRepository('schemaBundle:InfoServicio')
                                        ->findServicioByDetalleId(array('detalleId' => $arrayResponse['result']['detalle_id']));
                        if(!empty($objIS))
                        {
                            $objIERepository = null;
                            $strTipo = $objIS['tipoAfectado'];
                            if($strTipo == 'Servicio')
                            {
                                $objIST = $this->emCommercial->getRepository('schemaBundle:InfoServicioTecnico')
                                            ->findOneBy(array('servicioId' => $objIS['afectadoId']));
                                if(!empty($objIST) && $objIST->getElementoId())
                                {
                                    $objIERepository = $this->emInfrastructure->getRepository('schemaBundle:InfoElemento')
                                                                        ->find($objIST->getElementoId());
                                    if (!empty($objIERepository))
                                    {
                                        $arrayResponse['result']['id_elemento'] = $objIERepository->getId();
                                        $arrayResponse['result']['nombre_elemento'] = $objIERepository->getNombreElemento();
                                        $arrayResponse['result']['nombre_tipo_elemento']=$objIERepository->getModeloElementoId()
                                                        ->getTipoElementoId()->getNombreTipoElemento();
                                    }
                                }
                            }elseif($strTipo == 'Elemento' && empty($objIERepository))
                            {
                                $objIERepository = $this->emInfrastructure->getRepository('schemaBundle:InfoElemento')
                                                                        ->findOneById($objIS['afectadoId']);
                                if (!empty($objIERepository))
                                {
                                    $arrayResponse['result']['id_elemento'] = $objIERepository->getId();
                                    $arrayResponse['result']['nombre_elemento'] = $objIERepository->getNombreElemento();
                                    $arrayResponse['result']['nombre_tipo_elemento']=$objIERepository->getModeloElementoId()
                                                    ->getTipoElementoId()->getNombreTipoElemento();
                                }
                            }
                        }
                    }      
                }
            }

            $objDetalleAsignacion = $this->emSupport->getRepository("schemaBundle:InfoDetalleAsignacion")
                                    ->getUltimaAsignacion($arrayResponse['result']['detalle_id']);
            if (!empty($objDetalleAsignacion)) 
            {
                $arrayResponse['result']['telefono'] = $this->emCommercial->getRepository('schemaBundle:InfoPersona')
                                            ->getInfoTelImeiAsignado(
                                                array(
                                                'idPersonaEmpresaRol' => $objDetalleAsignacion->getPersonaEmpresaRolId(),
                                                'detalleNombre' => 'COLABORADOR',
                                                'objUtilService'=> $this->serviceUtil));
            }
            if(!empty($arrayResponse['result']['id_elemento']) && 
                                $arrayResponse['result']['nombre_tipo_elemento']=="NODO")
            {
                $arrayResponse['result']['nodo_id']=$arrayResponse['result']['id_elemento'];
            }else
            {
                if (!empty($arrayResponse['result']['id_elemento']) &&
                                $arrayResponse['result']['nombre_tipo_elemento']!="NODO") 
                {
                    $arrayData['elementoId'] = $arrayResponse['result']['id_elemento'];
                    $arrayData['serviceUtil'] = $this->serviceUtil;
                    $arrayData['emInfrastructure'] = $this->emInfrastructure;
                    $objIBANRepository = $this->emInfrastructure->getRepository('schemaBundle:InfoElemento')
                                                            ->obtenerElementoNodoxTarea($arrayData);
                    if(!empty($objIBANRepository))
                    {
                        $arrayResponse['result']['elemento_id']=$arrayResponse['result']['id_elemento']; 
                        $arrayResponse['result']['nombre_elemento_relacionado'] = $arrayResponse['result']['nombre_elemento'];
                        $arrayResponse['result']['nodo_id']=$objIBANRepository->getId();
                        $arrayResponse['result']['nombre_elemento']=$objIBANRepository->getNombreElemento();
                    }else
                    {
                        $arrayResponse['result']['elemento_id']=$arrayResponse['result']['id_elemento']; 
                        $arrayResponse['result']['nombre_elemento_relacionado'] = $arrayResponse['result']['nombre_elemento'];
                        $arrayResponse['result']['nodo_id']=null;
                        $arrayResponse['result']['nombre_elemento']=null;
                    }
                }else
                {
                    $arrayResponse['result']['nodo_id']=null;
                    $arrayResponse['result']['nombre_elemento']=null;
                    $arrayResponse['result']['elemento_id']=null; 
                    $arrayResponse['result']['nombre_elemento_relacionado'] = null; 
                }
            }
        } catch(\Exception $e)
        {
            $this
                ->serviceUtil
                ->insertError('Telcos+', 
                    'InfoBitacoraAccesoNodoService.getTareas', 
                    $e->getMessage(), 
                    $this->strUser,
                    $this->strUserIp);

            $arrayResponse = array(
                'success' => false, 
                'mensaje' => 'Error al consultar tareas. Notificar con Sistemas');
        }

        return $arrayResponse;
    }

     /**
     * Obtiene una bitácora por parámetros
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0 01-04-2021
     */
    public function getBitacora($arrayParams)
    {
        $objResponse = array(
            'status' => 404,
            'mensaje' => 'Bitácora no encontrada.'
        );

        if (!empty($arrayParams))
        {
            try
            {
                $arrayBParams = array();

                if (array_key_exists('bitacoraId', $arrayParams))
                {
                    $arrayBParams['id'] = $arrayParams['bitacoraId'];
                }

                if (array_key_exists('tareaId', $arrayParams))
                {
                    $arrayBParams['tareaId'] = $arrayParams['tareaId'];
                }

                if (array_key_exists('nodoId', $arrayParams))
                {
                    $arrayBParams['elementoNodo'] = $arrayParams['nodoId'];
                }

                if (array_key_exists('estado', $arrayParams))
                {
                    $arrayBParams['estado'] = $arrayParams['estado'];
                }

                if ($arrayBParams)
                {
                    $objIBANRepository = $this
                        ->emInfrastructure
                        ->getRepository('schemaBundle:InfoBitacoraAccesoNodo');
                    $objIBAN = $objIBANRepository->findOneBy($arrayBParams);
         
                    if (!empty($objIBAN))
                    {
                        $objResponse['status'] = 200;
                        $objResponse['mensaje'] = 'Bitácora encontrada correctamente';
                        $objResponse['data'] = $objIBAN;
                    }
                }
            }
            catch(\Exception $e)
            {
                $this
                    ->serviceUtil
                    ->insertError('Telcos+', 
                        'InfoBitacoraAccesoNodoService.getBitacora', 
                        $e->getMessage(), 
                        $this->strUser,
                        $this->strUserIp);
         
                $objResponse['status'] = 500;
                $objResponse['mensaje'] = 'Error consultando bitácora, notificar a Sistemas';
            }
        }

        return $objResponse;
    }

    /**
     * Crea una nueva bitácora
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0 01-04-2021
     */
    public function createBitacora($arrayParams)
    {
        $objResponse = array(
            'status' => 500,
            'mensaje' => 'Error creando bitácora, notificar a Sistemas.'
        );
        
        $arrayMsg = array();
        $boolError = false;

        try
        {
            $strTareaId        = $arrayParams['tareaId'];
            $strEmpresaId      = $this->strEmpresaCod;
            $strDepartamentoId = $arrayParams['departamentoId'];
            $strLogin          = $arrayParams['tecnicoAsignado'];
            $strCantonNombre   = $arrayParams['cantonNombre'];
            $strUsrCreacion    = $arrayParams['usrCreacion'];
            $intElementoId     = $arrayParams['elementoNodoNombre'];
            $intElementoRelacionadoId = $arrayParams['elemento'];
            $strTelefono       = $arrayParams['telefono'];
            $strObservacion    = $arrayParams['observacion'];
            $strCodigos        = $arrayParams['codigos'];

            $objIC = null;
            
            if (!empty($strTareaId))
            {
                $objICRepository = $this
                    ->emCommunication
                    ->getRepository('schemaBundle:InfoComunicacion');
                $objIC = $objICRepository->findOneBy(array('id' => $strTareaId));
            }

            if (empty($objIC))
            {
                $boolError = true;
                $arrayMsg[] = 'El id de la tarea no es válida.';
            }

            $objDepartment = null;

            if (!empty($strDepartamentoId))
            {
                $objADRepository = $this
                    ->emGeneral
                    ->getRepository('schemaBundle:AdmiDepartamento');
                $objDepartment = $objADRepository->findOneBy(
                    array('id' => (int) $strDepartamentoId));
            }

            if (empty($objDepartment))
            {
                $boolError = true;
                $arrayMsg[] = 'El id del departamento no es válido.';
            }

            $objID = null;

            if (!empty($objIC))
            {
                $objIDRepository = $this->emSupport->getRepository('schemaBundle:InfoDetalle');
                $objID = $objIDRepository->findOneBy(array('id' => $objIC->getDetalleId()));
                if(is_object($objID))
                {
                    $objDetalleAsignacion = $this->emSupport->getRepository("schemaBundle:InfoDetalleAsignacion")
                                    ->getUltimaAsignacion($objID->getId());
                }
            }
            
            if (empty($objID))
            {
                $boolError = true;
                $arrayMsg[] = 'La tarea no tiene detalle asociado.';
            }

            if (empty($strEmpresaId) || $strEmpresaId == 0 || $strEmpresaId == '0')
            {
                $strEmpresaId = 10;
            }

            $strMotivo = null;

            $objAT = null;

            if (!empty($objID))
            {
                $objATRepository = $this->emSupport->getRepository('schemaBundle:AdmiTarea');
                $objAT = $objATRepository->findOneBy(array('id' => $objID->getTareaId()));
            }

            if (empty($objAT))
            {
                $boolError = true;
                $arrayMsg[] = 'La tarea no es una tarea válida.';
            }
            else
            {
                $strMotivo = $objAT->getNombreTarea();
            }

            $objIERepository = $this->emInfrastructure->getRepository('schemaBundle:InfoElemento');

            if (!empty($intElementoId))
            {
                $objIES = $objIERepository->find($intElementoId);
                if (is_object($objIES)) 
                {
                   $strNombreNodo = $objIES->getNombreElemento();
                }
            }

            if (!empty($intElementoRelacionadoId))
            {
                $objIESRelacionado = $objIERepository->find($intElementoRelacionadoId);
            }

            if(!empty($strCodigos))
            {
                $strCodigosGenerado = "Apertura: ".$strCodigos;
            }
            
            if (!$boolError)
            {
                $this->emInfrastructure->getConnection()->beginTransaction();
                $objIBAN = new InfoBitacoraAccesoNodo();
                $objIBAN->setCanton($strCantonNombre);
                $objIBAN->setEmpresaCod((int) $strEmpresaId);
                $objIBAN->setDepartamento($objDepartment->getNombreDepartamento());
                $objIBAN->setTecnicoAsignado($strLogin);
                $objIBAN->setTelefono($strTelefono);
                $objIBAN->setTareaId((int) $strTareaId);
                $objIBAN->setCodigos($strCodigosGenerado);
                $objIBAN->setObservacion($strObservacion);
                $objIBAN->setMotivo($strMotivo);
                if (!empty($objIES)) 
                {
                    $objIBAN->setElementoNodo($objIES);
                    $objIBAN->setNombreNodo($strNombreNodo);
                }
                if (!empty($objIESRelacionado)) 
                {
                    $objIBAN->setElemento($objIESRelacionado);
                }
                $objIBAN->setEstado('Abierta');
                $objIBAN->setUsrCreacion($strUsrCreacion);
                $objIBAN->setFeCreacion(new \DateTime('now'));
                $this->emInfrastructure->persist($objIBAN);
                $this->emInfrastructure->flush();
                $this->emInfrastructure->commit();

                $entityInfoTareaSegui = new InfoTareaSeguimiento();
                $entityInfoTareaSegui->setDetalleId($objIC->getDetalleId());
                $entityInfoTareaSegui->setObservacion('Se ha abierto una bitácora para la tarea');
                $entityInfoTareaSegui->setUsrCreacion($strUsrCreacion);
                $entityInfoTareaSegui->setFeCreacion(new \DateTime('now'));
                $entityInfoTareaSegui->setEmpresaCod((int) $strEmpresaId);
                $entityInfoTareaSegui->setEstadoTarea($objIC->getEstado());
                $entityInfoTareaSegui->setInterno("N");
                $entityInfoTareaSegui->setDepartamentoId($objDepartment->getId());
                $entityInfoTareaSegui->setPersonaEmpresaRolId($objDetalleAsignacion->getPersonaEmpresaRolId());
                $this->emSupport->persist($entityInfoTareaSegui);
                $this->emSupport->flush();

                if(!empty($strCodigos))
                {
                    $entityInfoTareaSegui = new InfoTareaSeguimiento();
                    $entityInfoTareaSegui->setDetalleId($objIC->getDetalleId());
                    $entityInfoTareaSegui->setObservacion("Llave Acsys para apertura de Nodo: ".$strCodigos);
                    $entityInfoTareaSegui->setUsrCreacion($strUsrCreacion);
                    $entityInfoTareaSegui->setFeCreacion(new \DateTime('now'));
                    $entityInfoTareaSegui->setEmpresaCod($objIBAN->getEmpresaCod());
                    $entityInfoTareaSegui->setEstadoTarea($objIC->getEstado());
                    $entityInfoTareaSegui->setInterno("N");
                    $entityInfoTareaSegui->setDepartamentoId($objDetalleAsignacion->getDepartamentoId());
                    $entityInfoTareaSegui->setPersonaEmpresaRolId($objDetalleAsignacion->getPersonaEmpresaRolId());
                    $this->emSupport->persist($entityInfoTareaSegui);
                    $this->emSupport->flush();
                }

                $objResponse['status'] = 200;
                $objResponse['mensaje'] = 'Bitácora creada correctamente';
                $objResponse['data'] = array('id' => $objIBAN->getId());
            }
            else
            {
                $objResponse['status'] = 400;
                $objResponse['mensaje'] = implode(' - ', $arrayMsg);
            }
        }
        catch(\Exception $e)
        {
            if($this->emInfrastructure->getConnection()->isTransactionActive())
            {
                $this->emInfrastructure->getConnection()->rollback();
            }
            $this->emInfrastructure->close();

            $objResponse['mensaje'] = $e->getMessage();
        }

        return $objResponse;
    }

    /**
     * Cierra una nueva bitácora
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0 01-04-2021
     * 
     * Se corrige bug que setea el elemento nulo
     * @author Victor Peña <vpena@telconet.ec> 
     * @version 1.1 21-06-2023
     */
    public function updateBitacora($arrayParams)
    {
        $objResponse = array(
            'status' => 500,
            'mensaje' => 'Error cerrando bitácora, notificar a Sistemas.'
        );

        $arrayMsg = array();
        $boolError = false;

        try
        {
            $strBitacoraId   = $arrayParams['bitacoraId'];
            $strObservacion  = $arrayParams['observacion'];
            $strUsrCreacion  = $arrayParams['usrCreacion'];
            $strCodigos      = $arrayParams['codigos'];


            $objIBAN = null;
            $arrayIBAN = array();

            $arrayIBAN['estado'] = 'Abierta'; 

            if (!empty($strBitacoraId))
            {
                $arrayIBAN['id'] = $strBitacoraId;
            }

            $objIBANRepository = $this->emInfrastructure
                    ->getRepository('schemaBundle:InfoBitacoraAccesoNodo');
            $objIBAN = $objIBANRepository->findOneBy($arrayIBAN);

            if (empty($objIBAN))
            {
                $objResponse['mensaje'] = 'No existe ninguna bitácora abierta.';
                return $objResponse;
            }

            $objICTarea = $this->emCommunication->getRepository('schemaBundle:InfoComunicacion')
                            ->findOneBy(array('id' => $objIBAN->getTareaId()));
            if (is_object($objICTarea)) 
            {
                $objDetalleAsignacion = $this->emSupport->getRepository("schemaBundle:InfoDetalleAsignacion")
                                    ->getUltimaAsignacion($objICTarea->getDetalleId());
            }

            if(!empty($strCodigos))
            {
                if ($objIBAN->getCodigos())
                {
                    $strCodigosGenerado = $objIBAN->getCodigos()."\n\n".'Cierre: '.$strCodigos;
                }else 
                {
                    $strCodigosGenerado = 'Cierre: '.$strCodigos;
                }
                
            }

            if(!empty($strObservacion) && ($objIBAN->getObservacion()))
            {
                $strObservacion = $objIBAN->getObservacion()."\n\n".$strObservacion;
            }

            $objElementoRelacionado = array();
            if($arrayParams['elementoNuevo'])
            {
                $objElementoRelacionado = $this->emInfrastructure->getRepository('schemaBundle:InfoElemento')
                            ->findOneBy(array("nombreElemento" => trim($arrayParams['elementoNuevo']),
                                                "estado"  => "Activo"));
            }

            if (!$boolError)
            {
                $this->emInfrastructure->getConnection()->beginTransaction();
                if (!empty($strObservacion)) 
                {
                    $objIBAN->setObservacion($strObservacion);
                }
                $objIBAN->setEstado('Cerrada');
                if (!empty($strCodigos)) 
                {
                    $objIBAN->setCodigos($strCodigosGenerado);
                }
                if($arrayParams['elementoNuevo'] && !empty($objElementoRelacionado))
                {
                    $objIBAN->setElemento($objElementoRelacionado);
                }
                $objIBAN->setUsrUltMod($strUsrCreacion);
                $objIBAN->setFeUltMod(new \DateTime('now'));

                $this->emInfrastructure->merge($objIBAN);
                $this->emInfrastructure->flush();
                $this->emInfrastructure->commit();
                $this->emInfrastructure->close();

                if(!empty($strCodigos))
                {
                    $entityInfoTareaSegui = new InfoTareaSeguimiento();
                    $entityInfoTareaSegui->setDetalleId($objICTarea->getDetalleId());
                    $entityInfoTareaSegui->setObservacion("Llave Acsys para cierre de Nodo: ".$strCodigos);
                    $entityInfoTareaSegui->setUsrCreacion($strUsrCreacion);
                    $entityInfoTareaSegui->setFeCreacion(new \DateTime('now'));
                    $entityInfoTareaSegui->setEmpresaCod($objIBAN->getEmpresaCod());
                    $entityInfoTareaSegui->setEstadoTarea($objICTarea->getEstado());
                    $entityInfoTareaSegui->setInterno("N");
                    $entityInfoTareaSegui->setDepartamentoId($objDetalleAsignacion->getDepartamentoId());
                    $entityInfoTareaSegui->setPersonaEmpresaRolId($objDetalleAsignacion->getPersonaEmpresaRolId());
                    $this->emSupport->persist($entityInfoTareaSegui);
                    $this->emSupport->flush();
                }

                $entityInfoTareaSegui = new InfoTareaSeguimiento();
                $entityInfoTareaSegui->setDetalleId($objICTarea->getDetalleId());
                $entityInfoTareaSegui->setObservacion('Se ha cerrado una bitácora para la tarea');
                $entityInfoTareaSegui->setUsrCreacion($strUsrCreacion);
                $entityInfoTareaSegui->setFeCreacion(new \DateTime('now'));
                $entityInfoTareaSegui->setEmpresaCod($objIBAN->getEmpresaCod());
                $entityInfoTareaSegui->setEstadoTarea($objICTarea->getEstado());
                $entityInfoTareaSegui->setInterno("N");
                $entityInfoTareaSegui->setDepartamentoId($objDetalleAsignacion->getDepartamentoId());
                $entityInfoTareaSegui->setPersonaEmpresaRolId($objDetalleAsignacion->getPersonaEmpresaRolId());
                $this->emSupport->persist($entityInfoTareaSegui);
                $this->emSupport->flush();


                $objResponse['status'] = 200;
                $objResponse['mensaje'] = 'Bitácora cerrada correctamente';

            }
            else
            {
                $objResponse['mensaje'] = implode(' - ', $arrayMsg);
            }
        }
        catch(\Exception $e)
        {
            if($this->emInfrastructure->getConnection()->isTransactionActive())
            {
                $this->emInfrastructure->getConnection()->rollback();
            }
            $this->emInfrastructure->close();

            $this
                ->serviceUtil
                ->insertError('Telcos+', 
                    'InfoBitacoraAccesoNodoService.getBitacoras', 
                    $e->getMessage(), 
                    $this->strUser,
                    $this->strUserIp);
        }

        return $objResponse;
    }
}