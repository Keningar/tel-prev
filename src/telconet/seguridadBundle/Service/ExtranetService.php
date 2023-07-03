<?php

namespace telconet\seguridadBundle\Service;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;

class ExtranetService {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emComercial;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emInfraestructura;
    
    // *** =========================================== ***
    //            INJECCION DE SERVICES
    // *** =========================================== ***
    private $serviceSeguridad;
    /* @var $serviceSeguridad SeguridadService */

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container) 
    {
        $this->emComercial        = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emInfraestructura  = $container->get('doctrine.orm.telconet_infraestructura_entity_manager');
        $this->serviceSeguridad   = $container->get('seguridad.Seguridad');
    }

    /**
     * Registra el dispositivo como un elemento y asociarlo como caracteristica del idPersonaEmpresaRol en InfoPersonaEmpresaRolCarac
     * 
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 29-12-2015
     * 
     * @param string $strUsuario
     * @param string $strCodEmpresa
     * @param array $arrayTelefono
     * @return array con datos del cliente si se encuentra, null caso contrario
     */
    public function asociarDispositivo($strUsuario, $strCodEmpresa, $arrayTelefono) 
    {
        $arrayData = null;

        // Se obtiene la caracteristica [TELEFONO]
        $objRepoAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica');
        $objAdmiCaractTelfefono    = $objRepoAdmiCaracteristica->findOneBy(array('descripcionCaracteristica' => 'TELEFONO_ASOCIADO',
                                                                                 'tipo'                      => 'SEGURIDAD',
                                                                                 'estado'                    => 'Activo'));
        if (!is_object($objAdmiCaractTelfefono)) 
        {
            $arrayData['status']  = 'ERROR_SERVICE';
            $arrayData['mensaje'] = 'Problemas de configuracion';
            return $arrayData;
        }

        if ((array_key_exists('imei', $arrayTelefono) && $arrayTelefono['imei'] != '') &&
            (array_key_exists('iccid', $arrayTelefono) && $arrayTelefono['iccid'] != '') &&
            (array_key_exists('id_device', $arrayTelefono) && $arrayTelefono['id_device'] != '')) 
        {
            $objMarcaElemento = $this->emInfraestructura->getRepository('schemaBundle:AdmiMarcaElemento')
                                                        ->findOneBy(array('nombreMarcaElemento' => 'DISPOSITIVO_ANDROID', 
                                                                          'estado'              => 'Activo'));
            
            $objTipoElemento  = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
                                                        ->findOneBy(array('nombreTipoElemento' => 'SMARTPHONE', 
                                                                          'estado'             => 'Activo'));

            if (is_object($objTipoElemento) && is_object($objMarcaElemento))
            {
                $arrayParametros = array();
                $arrayParametros['strNombre']       = 'ANDROID_MOBILE';
                $arrayParametros['strMarca']        = $objMarcaElemento->getId();
                $arrayParametros['strTipoElemento'] = $objTipoElemento->getId();
                $arrayParametros['strEstado']       = 'Activo';
                $objModeloElemento = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                             ->findModeloElementoPorCriterios($arrayParametros);

                if (is_object($objModeloElemento)) 
                {
                    $this->emInfraestructura->getConnection()->beginTransaction();
                    $this->emComercial->getConnection()->beginTransaction();
                    try 
                    {
                        // Creamos infoElemento
                        $objElemento = new InfoElemento();
                        $objElemento->setModeloElementoId($objModeloElemento);
                        $objElemento->setNombreElemento('Dispositivo Android Asociado por Mobile Netlife ');
                        $objElemento->setSerieFisica($arrayTelefono['imei']);
                        $objElemento->setSerieLogica($arrayTelefono['id_device']);
                        $objElemento->setUsrCreacion('MobileNetlife');
                        $objElemento->setFeCreacion(new \Datetime('now'));
                        $objElemento->setIpCreacion('127.0.0.1');
                        $objElemento->setEstado('Activo');
                        $this->emInfraestructura->persist($objElemento);
                        $this->emInfraestructura->flush();

                        // Creamos los infoDetalleElemento necesarios
                        $this->emInfraestructura
                             ->persist($this->crearDetalleElemento($objElemento->getId(), "imei", $arrayTelefono['imei'], "imei del dispositivo"));
                        $this->emInfraestructura
                            ->persist($this->crearDetalleElemento($objElemento->getId(), "iccid", $arrayTelefono['iccid'], "iccid del dispositivo"));
                        $this->emInfraestructura
                            ->persist($this->crearDetalleElemento($objElemento->getId(),
                                                                  "id_device", 
                                                                  $arrayTelefono['id_device'], 
                                                                  "id_device del dispositivo"));
                        $this->emInfraestructura->flush();

                        // Creamos un infoPersonaEmpresaRolCarac con la caracteristica del dispositivo asociado
                        $strLoginCaract = $this->serviceSeguridad->obtenerIPERCaracLogin($strUsuario, $strCodEmpresa);
                        
                        $objPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                        $objPersonaEmpresaRolCarac->setValor($objElemento->getId());
                        $objPersonaEmpresaRolCarac->setCaracteristicaId($objAdmiCaractTelfefono);
                        $objPersonaEmpresaRolCarac->setPersonaEmpresaRolId($strLoginCaract->getPersonaEmpresaRolId());
                        $objPersonaEmpresaRolCarac->setPersonaEmpresaRolCaracId($strLoginCaract->getId());
                        $objPersonaEmpresaRolCarac->setUsrCreacion('MobileNetlife');
                        $objPersonaEmpresaRolCarac->setFeCreacion(new \Datetime('now'));
                        $objPersonaEmpresaRolCarac->setIpCreacion('127.0.0.1');
                        $objPersonaEmpresaRolCarac->setEstado('Activo');
                        $this->emComercial->persist($objPersonaEmpresaRolCarac);
                        $this->emComercial->flush();

                        $this->emInfraestructura->getConnection()->commit();
                        $this->emComercial->getConnection()->commit();

                    } 
                    catch (\Exception $e) 
                    {
                        error_log($e->getMessage());
                        //...                        
                        if ($this->emInfraestructura->getConnection()->isTransactionActive()) 
                        {
                            $this->emInfraestructura->getConnection()->rollback();
                        }
                        $this->emInfraestructura->getConnection()->close();
                        //...
                        if ($this->emComercial->getConnection()->isTransactionActive()) 
                        {
                            $this->emComercial->getConnection()->rollback();
                        }
                        $this->emComercial->getConnection()->close();
                        //...
                        $arrayData['status']  = 'ERROR_SERVICE';
                        $arrayData['mensaje'] = 'Rollback de la transaccion';
                        return $arrayData;
                    }
                }
            }
        } 
        else 
        {
            $arrayData['status']  = 'ERROR_SERVICE';
            $arrayData['mensaje'] = 'Información incompleta';
            return $arrayData;
        }
        return $arrayData;
    }

    /**
     * Verifica si el dispositivo se encuentra registrado mediante su IMEI e ID_DEVICE
     * 
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 30-12-2015
     * 
     * @param array  $arrayDispositivoParams   -- 'imei'      -> Serie fisica del dispositivo
     *                                    -- 'iccid'     -> Serie del chip del telefono
     *                                    -- 'id_device' -> Serie logica del dispositivo
     * @return array con datos de verificacion del dispositivo
     */
    public function verificarDispositivo($arrayDispositivoParams) 
    {
        $arrayData = null;
       
        // *** =========================================== ***
        //            INJECCION DE REPOSITORIOS
        // *** =========================================== ***
        $objRepoInfoElemento           = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento');
        $objRepoInfoDetalleElemento    = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento');
        
        // Se verifica si existe un elemento con el IMEI e ID_DEVICE los mismos que son la serie Fisica y Logica respectivamente
        $objElemento = $objRepoInfoElemento->findOneBy(array('serieFisica' => $arrayDispositivoParams['imei'],
                                                             'serieLogica' => $arrayDispositivoParams['id_device']));
        if (!isset($objElemento)) 
        {
            $arrayData['status']  = 'ERROR_SERVICE';
            $arrayData['mensaje'] = 'Dispositivo no encontrado';
            return $arrayData;
        }
        
        if ((array_key_exists('imei', $arrayDispositivoParams) && $arrayDispositivoParams['imei'] != '') &&
            (array_key_exists('iccid', $arrayDispositivoParams) && $arrayDispositivoParams['iccid'] != '') &&
            (array_key_exists('id_device', $arrayDispositivoParams) && $arrayDispositivoParams['id_device'] != '')) 
        {
            $booleanMatch = true;
            $objImei   = $objRepoInfoDetalleElemento->findOneBy(array(
                                                                     'elementoId' => $objElemento->getId(), 
                                                                     'detalleNombre' => 'imei'));
            $objIccid  = $objRepoInfoDetalleElemento->findOneBy(array(
                                                                     'elementoId' => $objElemento->getId(), 
                                                                     'detalleNombre' => 'iccid'));
            $objDevice = $objRepoInfoDetalleElemento->findOneBy(array(
                                                                     'elementoId' => $objElemento->getId(), 
                                                                     'detalleNombre' => 'id_device'));

            if (!is_object($objIccid) || $arrayDispositivoParams['iccid'] != $objIccid->getDetalleValor()) 
            {
                $booleanMatch = false;
            }

            if (!is_object($objDevice) || $arrayDispositivoParams['id_device'] != $objDevice->getDetalleValor()) 
            {
                $booleanMatch = false;
            }

            if (!is_object($objImei) || $arrayDispositivoParams['imei'] != $objImei->getDetalleValor()) 
            {
                $booleanMatch = false;
            }

            if ($booleanMatch) 
            {
                $arrayData['dispositivo'] = 'Verificado';
                return $arrayData;
            }
        } 
        else 
        {
            $arrayData["status"] = "ERROR_SERVICE";
            $arrayData['mensaje'] = "informacion incompleta";
        }
        return $arrayData;
    }

    //#########################################
    //###   METODOS PRIVADOS DEL SERVICIO   ###
    //#########################################
    
    /**
     * Genera un InfoDetalleElemento
     * 
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 29-12-2015
     * 
     * @param string $intElementoId
     * @param string $strNombre
     * @param string $strValor
     * @param string $strDescripcion
     * @return $entityDetalleElemento con datos del cliente si se encuentra, null caso contrario
     */
    private function crearDetalleElemento($intElementoId, $strNombre, $strValor, $strDescripcion) 
    {
       
        $objDetalleElemento = new InfoDetalleElemento();

        $objDetalleElemento->setElementoId($intElementoId);
        $objDetalleElemento->setDetalleNombre($strNombre);
        $objDetalleElemento->setDetalleValor($strValor);
        $objDetalleElemento->setDetalleDescripcion($strDescripcion);
        $objDetalleElemento->setUsrCreacion('MobileNetlife');
        $objDetalleElemento->setFeCreacion(new \Datetime('now'));
        $objDetalleElemento->setIpCreacion('127.0.0.1');
        return $objDetalleElemento;
    }

}
