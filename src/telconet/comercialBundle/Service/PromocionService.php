<?php
namespace telconet\comercialBundle\Service;
use telconet\schemaBundle\Entity\AdmiGrupoPromocion;
use telconet\schemaBundle\Entity\AdmiGrupoPromocionRegla;
use telconet\schemaBundle\Entity\AdmiGrupoPromocionHisto;
use telconet\schemaBundle\Entity\AdmiTipoPromocion;
use telconet\schemaBundle\Entity\AdmiTipoPromocionRegla;
use telconet\schemaBundle\Entity\AdmiTipoPlanProdPromocion;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoServicioCaracteristica;
use Symfony\Component\HttpFoundation\Response;
class PromocionService 
{ 
    private $emcom;
    private $emInfraestructura;
    private $serviceUtil;
    private $emGeneral;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emcom              = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emInfraestructura  = $container->get('doctrine.orm.telconet_infraestructura_entity_manager');
        $this->emGeneral          = $container->get('doctrine.orm.telconet_general_entity_manager');
        $this->serviceUtil        = $container->get('schema.Util');
    }
    /**
     * getMotivos, obtiene la información de las motivos para Inactivar o clonar promociones
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 05-04-2019
     * @param array $arrayParametros[]                  
     *              'arrayEstadoMotivos'  => Estado para los motivos 
     *              'strNombreModulo'     => Nombre del Modulo                     
     * @return Response lista de Motivos
     */
    public function getMotivos($arrayParametros)
    {              
        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getMotivos($arrayParametros);
    }
    /**
     * getTiposNegocio, obtiene los tipos de Negocio por empresa en estado activo
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 19-03-2019     
     * @param array $arrayParametros[]                  
     *              'strEstado'          => Estado del tipo de Negocio    
     *              'strEmpresaCod'      => Empresa en sesión                  
     * @return Response lista de Tipos de Negocio
     */
    public function getTiposNegocio($arrayParametros)
    {              
        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getTiposNegocio($arrayParametros);
    }
    /**
    * actualizarTipoPromocionRegla, Función que actualiza Reglas para los Tipos de Promociones
    * En el caso de recibir la acción a procesar -> "ELIMINAR" se procede a pasar a estado  "Eliminado" en  ADMI_TIPO_PROMOCION_REGLA, solo si la
    * promoción tiene la regla.
    * En el caso de recibir la acción a procesar -> "EDITAR" se procede a verificar:
    * - Si la promocion ya posee la Regla se actualiza Valor
    * - Si la promoción no posee la regla se la inserta con el valor enviado. 
    *
    * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
    * @version 1.0 16-04-2019   
    * @param array $arrayParamReglas []
    *              'intIdTipoPromocion'      => Id tipo de la Promoción
    *              'strRegla'                => Tipo de Regla
    *              'strAccion'               => Acción a procesar 'EDITAR' ó 'ELIMINAR'
    *              'strValorRegla'           => Valor de la regla
    *              'strUsrCreacion'          => Usuario en sesión
    *              'strFeCreacion'           => Fecha en sesión
    *              'strIpCreacion'           => Ip de sesión
    */
    
    public function actualizarTipoPromocionRegla($arrayParamReglas)
    {
        $objAdmiTipoPromocion = $this->emcom->getRepository('schemaBundle:AdmiTipoPromocion')->find($arrayParamReglas['intIdTipoPromocion']);
        if(!is_object($objAdmiTipoPromocion))
        {
            throw new \Exception("No se pudo actualizar la Promoción, No se encontró el Tipo de Promoción a ingresar");
        }
        
        $objCaracteristica    = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array('descripcionCaracteristica' => $arrayParamReglas['strRegla'],
                                                              'tipo'                      => 'COMERCIAL',
                                                              'estado'                    => 'Activo'
                                                              )
                                                        );
        if(!is_object($objCaracteristica))
        {
            throw new \Exception("No se pudo actualizar la Promoción, No se encontró la Regla definida para el Tipo de Promoción");

        }
        $arrayParametroTipoRegla = array('intTipoPromocionId'  => $arrayParamReglas['intIdTipoPromocion'],
                                         'intCaracteristicaId' => $objCaracteristica->getId(),
                                         'arrayEstados'        => array('Eliminado')
                               );
        $objAdmiTipoPromocionRegla = $this->emcom->getRepository("schemaBundle:AdmiGrupoPromocion")
                                                 ->getTipoPromocionReglaEstado($arrayParametroTipoRegla);   
        
        if(is_object($objAdmiTipoPromocionRegla))
        {            
            $objAdmiTipoPromocionRegla->setFeUltMod($arrayParamReglas['strFeCreacion']);
            $objAdmiTipoPromocionRegla->setUsrUltMod($arrayParamReglas['strUsrCreacion']);
            $objAdmiTipoPromocionRegla->setIpUltMod($arrayParamReglas['strIpCreacion']);    
            
            if($arrayParamReglas['strAccion'] === 'EDITAR')
            {
                $objAdmiTipoPromocionRegla->setValor($arrayParamReglas['strValorRegla']);                
                if($objAdmiTipoPromocionRegla->getEstado() == 'Pendiente')
                {
                    $objAdmiTipoPromocionRegla->setEstado('Activo');
                }
            }
            elseif($arrayParamReglas['strAccion'] === 'ELIMINAR')                        
            {
                $objAdmiTipoPromocionRegla->setEstado('Eliminado');
            }
            $this->emcom->persist($objAdmiTipoPromocionRegla);
            $this->emcom->flush();
        }
        else
        {
            if($arrayParamReglas['strAccion'] === 'EDITAR')
            {
                $objAdmiTipoPromocionRegla = new AdmiTipoPromocionRegla();
                $objAdmiTipoPromocionRegla->setTipoPromocionId($objAdmiTipoPromocion);
                $objAdmiTipoPromocionRegla->setCaracteristicaId($objCaracteristica);
                $objAdmiTipoPromocionRegla->setValor($arrayParamReglas['strValorRegla']);
                $objAdmiTipoPromocionRegla->setFeCreacion($arrayParamReglas['strFeCreacion']);
                $objAdmiTipoPromocionRegla->setUsrCreacion($arrayParamReglas['strUsrCreacion']);
                $objAdmiTipoPromocionRegla->setIpCreacion($arrayParamReglas['strIpCreacion']);
                $objAdmiTipoPromocionRegla->setEstado($objAdmiTipoPromocion->getEstado());
                $this->emcom->persist($objAdmiTipoPromocionRegla);
                $this->emcom->flush();
            }
        }       
    }
    /**
     * Función que inserta Reglas para los Tipos de Promociones
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 26-04-2019     
     * @param array $arrayParamReglas[]     
     *              'intIdTipoPromocion' => Id del Tipo de Promoción
     *              'strRegla'           => Descripción de las reglas o Características 
     *              'strAccion'          => Acción  NUEVO, EDITAR,
     *              'strValorRegla'      => Valor de Regla
     *              'strFeCreacion'      => Fecha de Creación del Registro
     *              'strUsrCreacion'     => Usuario de Creación del registro
     *              'strIpCreacion'      => Ip de Creación del registro   
     *              'intNumeroSecuencia' => Secuencia SEQ_ADMI_REGLA_SECUENCIA que asocia cada sectorización
     *                                      Jurisdicción, Cantón, Parroquía, Sector, Olt      
     */
    public function actualizarTipoPromocionReglaSectorizacion($arrayParamReglas)
    {
        $objAdmiTipoPromocion = $this->emcom->getRepository('schemaBundle:AdmiTipoPromocion')->find($arrayParamReglas['intIdTipoPromocion']);
        if(!is_object($objAdmiTipoPromocion))
        {
            throw new \Exception("No se pudo editar la Promoción, No se encontró el Tipo de Promoción a ingresar.");
        }       
                
        $objCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                         ->findOneBy(array('descripcionCaracteristica' => $arrayParamReglas['strRegla'],
                                                           'tipo'                      => 'COMERCIAL',
                                                           'estado'                    => 'Activo' ));
        if(!is_object($objCaracteristica))
        {
            throw new \Exception("No se pudo editar la Promoción, No se encontró la Regla definida para el Tipo de Promoción.");
        }
        
        if($arrayParamReglas['strAccion'] === "NUEVO")
        {
            $objAdmiTipoPromocionRegla = new AdmiTipoPromocionRegla();
            $objAdmiTipoPromocionRegla->setTipoPromocionId($objAdmiTipoPromocion);
            $objAdmiTipoPromocionRegla->setCaracteristicaId($objCaracteristica);
            $objAdmiTipoPromocionRegla->setValor($arrayParamReglas['strValorRegla']);
            $objAdmiTipoPromocionRegla->setFeCreacion($arrayParamReglas['strFeCreacion']);
            $objAdmiTipoPromocionRegla->setUsrCreacion($arrayParamReglas['strUsrCreacion']);
            $objAdmiTipoPromocionRegla->setIpCreacion($arrayParamReglas['strIpCreacion']);
            $objAdmiTipoPromocionRegla->setEstado($objAdmiTipoPromocion->getEstado());
            $objAdmiTipoPromocionRegla->setSecuencia($arrayParamReglas['intNumeroSecuencia']);

            $this->emcom->persist($objAdmiTipoPromocionRegla);
            $this->emcom->flush();
        }
        else
        {
            $arrayParametroTipoRegla  = array('intTipoPromocionId'  => $arrayParamReglas['intIdTipoPromocion'],
                                              'intNumeroSecuencia'  => $arrayParamReglas['intNumeroSecuencia'],
                                              'intCaracteristicaId' => $objCaracteristica->getId()
                                            );
            $objAdmiTipoPromocionRegla = $this->emcom->getRepository("schemaBundle:AdmiGrupoPromocion")
                                                     ->getTipoPromocionReglaEstado($arrayParametroTipoRegla);  
            
            if(is_object($objAdmiTipoPromocionRegla))
            {    
                $objAdmiTipoPromocionRegla->setFeUltMod($arrayParamReglas['strFeCreacion']);
                $objAdmiTipoPromocionRegla->setUsrUltMod($arrayParamReglas['strUsrCreacion']);
                $objAdmiTipoPromocionRegla->setIpUltMod($arrayParamReglas['strIpCreacion']);  
                $objAdmiTipoPromocionRegla->setEstado('Activo');
              
                $this->emcom->persist($objAdmiTipoPromocionRegla);
                $this->emcom->flush();
                
            }
        }
    }
     /**
     * ingresarTipoPromocionRegla, Función que inserta Reglas para los Tipos de Promociones.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 02-04-2019     
     * @param array $arrayParamReglas[]     
     *              'intIdTipoPromocion' => Id del Tipo de Promoción.
     *              'strRegla'           => Descripción de las reglas o Características a insertarse.
     *              'strValorRegla'      => Valor de Regla.
     *              'strFeCreacion'      => Fecha de Creación del Registro.
     *              'strUsrCreacion'     => Usuario de Creación del registro.
     *              'strIpCreacion'      => Ip de Creación del registro.   
     *              'intNumeroSecuencia' => Secuencia SEQ_ADMI_REGLA_SECUENCIA que asocia cada sectorización
     *                                      Jurisdicción, Cantón, Parroquía, Sector, Olt.     
     */
    public function ingresarTipoPromocionRegla($arrayParamReglas)
    {
        $objAdmiTipoPromocion = $this->emcom->getRepository('schemaBundle:AdmiTipoPromocion')->find($arrayParamReglas['intIdTipoPromocion']);
        if(!is_object($objAdmiTipoPromocion))
        {
            throw new \Exception("No se pudo crear Promoción, No se encontró el Tipo de Promoción a ingresar");
        }
        $objCaracteristica    = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array('descripcionCaracteristica' => $arrayParamReglas['strRegla'],
                                                              'tipo'                      => 'COMERCIAL',
                                                              'estado'                    => 'Activo'
                                                             ));
        if(!is_object($objCaracteristica))
        {
            throw new \Exception("No se pudo crear Promoción, No se encontró la Regla definida para el Tipo de Promoción");
        }
        $objAdmiTipoPromocionRegla = new AdmiTipoPromocionRegla();
        $objAdmiTipoPromocionRegla->setTipoPromocionId($objAdmiTipoPromocion);
        $objAdmiTipoPromocionRegla->setCaracteristicaId($objCaracteristica);
        $objAdmiTipoPromocionRegla->setValor($arrayParamReglas['strValorRegla']);
        $objAdmiTipoPromocionRegla->setFeCreacion($arrayParamReglas['strFeCreacion']);
        $objAdmiTipoPromocionRegla->setUsrCreacion($arrayParamReglas['strUsrCreacion']);
        $objAdmiTipoPromocionRegla->setIpCreacion($arrayParamReglas['strIpCreacion']);
        $objAdmiTipoPromocionRegla->setEstado('Activo');
        if($arrayParamReglas['strRegla'] === "PROM_JURISDICCION" || $arrayParamReglas['strRegla'] === "PROM_CANTON" ||
            $arrayParamReglas['strRegla'] === "PROM_PARROQUIA" || $arrayParamReglas['strRegla'] === "PROM_SECTOR" ||
            $arrayParamReglas['strRegla'] === "PROM_ELEMENTO" || $arrayParamReglas['strRegla'] === "PROM_EDIFICIO")
        {
            $objAdmiTipoPromocionRegla->setSecuencia($arrayParamReglas['intNumeroSecuencia']);
        }
        $this->emcom->persist($objAdmiTipoPromocionRegla);
        $this->emcom->flush();
    }    
     /**
     * ingresarPromocionGrupoHisto, Función que inserta Historial del grupo de la promoción
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 02-04-2019     
     * @param array $arrayParamPromoGrupoHisto[]     
     *              'intIdGrupoPromocion' => Id del grupo de la Promoción
     *              'intIdMotivo'         => Motivo del registro del Historial 
     *              'strFeCreacion'       => Fecha de Creación del Registro
     *              'strUsrCreacion'      => Usuario de Creación del registro
     *              'strIpCreacion'       => Ip de Creación del registro
     *              'strObservacion'      => Observación del Historial
     *              'strEstado'           => Estado del Historial      
     */
    public function ingresarPromocionGrupoHisto($arrayParamPromoGrupoHisto)
    {
        $objAdmiGrupoPromocion = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                             ->find($arrayParamPromoGrupoHisto['intIdGrupoPromocion']);
        if(!is_object($objAdmiGrupoPromocion))
        {
            throw new \Exception("No se pudo crear Promoción, No se encontró el grupo de la Promoción para ingresar Historial");
        }        
        
        $objAdmiGrupoPromocionHisto = new AdmiGrupoPromocionHisto();                    
        $objAdmiGrupoPromocionHisto->setGrupoPromocionId($objAdmiGrupoPromocion);
        if(isset($arrayParamPromoGrupoHisto['intIdMotivo']) && !empty($arrayParamPromoGrupoHisto['intIdMotivo']))
        {
            $objAdmiMotivo = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($arrayParamPromoGrupoHisto['intIdMotivo']);
            if(!is_object($objAdmiMotivo))
            {
                throw new \Exception("No se pudo crear Promoción, No se encontró registro del motivo para ingresar Historial");
            }

            $objAdmiGrupoPromocionHisto->setMotivoId($objAdmiMotivo->getId());
        }
        $objAdmiGrupoPromocionHisto->setFeCreacion($arrayParamPromoGrupoHisto['strFeCreacion']);
        $objAdmiGrupoPromocionHisto->setUsrCreacion($arrayParamPromoGrupoHisto['strUsrCreacion']);
        $objAdmiGrupoPromocionHisto->setIpCreacion($arrayParamPromoGrupoHisto['strIpCreacion']);
        $objAdmiGrupoPromocionHisto->setObservacion($arrayParamPromoGrupoHisto['strObservacion']);
        $objAdmiGrupoPromocionHisto->setEstado($arrayParamPromoGrupoHisto['strEstado']);
        $this->emcom->persist($objAdmiGrupoPromocionHisto);
        $this->emcom->flush();
    }    
     /**
     * crearProcesoMasivo
     * 
     * Método que genera un Proceso Masivo que puede ser por Inactivación y/o Dado de Baja o Clonación de promociones, 
     * en base a parámetros enviados.
     * El método incluirá en el PMA todas las promociones que hayan sido previamente escogidass o  marcadas en el proceso,
     * guardando el motivo y la observación del proceso sea esta por Inactivación y/o Dada de baja, Clonación.
     *         
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 04-04-2019           
     * @param array $arrayParametros[]                  
     *              'strIdsGrupoPromocion'    => Ids de los grupos de Promociones ADMI_GRUPO_PROMOCION 
     *              'intIdMotivo'             => Motivo del Proceso del PMA
     *              'strObservacion'          => Observación del Proceso del PMA                 
     *              'strUsrCreacion'          => Usuario en sesión
     *              'strCodEmpresa'           => Código de empresa en sesión
     *              'strIpCreacion'           => Ip de creación
     *              'strTipoPma'              => Tipo de Proceso Masivo :InactivarPromo y/o DarBajaPromo, ClonarPromo                         
     * @return $strRespuesta
     */
    public function crearProcesoMasivo($arrayParametros)
    {             
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];        
        $strIpCreacion          = $arrayParametros['strIpCreacion'];
        $this->emcom->beginTransaction();
        try
        {            
            $strRespuesta = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->guardarProcesoMasivo($arrayParametros);            
        }
        catch(\Exception $e)
        {
            $this->emcom->rollback();
            $this->emcom->close();           
            $strRespuesta = "No se pudo crear el Proceso Masivo: ".$arrayParametros['strTipoPma'].", <br> ". 
                             $e->getMessage(). ". Favor notificar a Sistemas.";            
            $this->serviceUtil->insertError('Telcos+',
                                            'PromocionService.crearProcesoMasivo',
                                             'Error PromocionService.crearProcesoMasivo: No se pudo crear el Proceso Masivo: '
                                             .$arrayParametros['strTipoPma'].': '.$e->getMessage(),
                                             $strUsrCreacion,
                                             $strIpCreacion);
            return $strRespuesta;
        }
        return $strRespuesta;
    }      
    /**
     * obtenerParametrosPromocion()
     * Función que obtiene los parámetros de la Promoción
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 04-04-2019          
     * @param array $arrayParametros[                
     *                                'strTipoPromocion'  => Tipo de Promoción,
     *                                'strCodEmpresa'     => Código de Empresa       
     *                              ]  
     * @return $arrayRespuesta              
     */
    public function obtenerParametrosPromocion($arrayParametros)
    {
        $arrayParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->getOne('PROM_TIPO_PROMOCIONES', 'COMERCIAL', 'ADMI_TIPO_PROMOCION', '', 
                                                      $arrayParametros['strTipoPromocion'], '', '', '', '', $arrayParametros['strCodEmpresa']);

        $strTipoPromocion    = ( isset($arrayParametroDet["valor1"]) && !empty($arrayParametroDet["valor1"]) ) ? $arrayParametroDet["valor1"] : "";
        $strCodTipoPromocion = ( isset($arrayParametroDet["valor2"]) && !empty($arrayParametroDet["valor2"]) ) ? $arrayParametroDet["valor2"] : "";
        
        if($strTipoPromocion === '')
        {
            throw new \Exception("No se pudo crear la Promoción, No se encontró el Tipo de Promoción parametizada.");
        }
        if($strCodTipoPromocion === '')
        {
            throw new \Exception("No se pudo crear la Promoción, No se encontró el Código Tipo de Promoción parametizada.");
        }
        $arrayRespuesta = array('strTipoPromocion'    => $strTipoPromocion,
                                'strCodTipoPromocion' => $strCodTipoPromocion
                               );
        return $arrayRespuesta;
    }
    
     /**
     * eliminarTipoPromocion()
     * Función que actualiza el estado a Eliminado de las reglas de Planes y Productos,
     * para el Tipo de Promoción(Promo Mix, Planes, Productos y Desc.Total).
     * Esta función se la utiliza antes de editar la promoción, para setear todas las reglas con estado a eliminado 
     * y posteriormente la regla editada se le cambia a estado Activo.
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 25-04-2019    
     * @param array $arrayParametros[               
     *                                'objAdmiGrupoPromocion  => Objeto del Grupo Promoción  ADMI_GRUPO_PROMOCION
     *                                'strTipoPromocion'      => Tipo de Promoción(Mix, Planes, Productos, Desc.Total),
     *                                'strFeUltMod'           => Fecha de Edición,
     *                                'strUsrUltMod'          => Usuario de Edición,
     *                                'strIpUltMod'           => Ip de Edición,
     *                              ]  
     */
    public function eliminarTipoPromocion($arrayParamTipoPromocion)
    {
        $objAdmiTipoPromocion = $this->emcom->getRepository('schemaBundle:AdmiTipoPromocion')
                                            ->findOneBy(array('grupoPromocionId' => $arrayParamTipoPromocion['objAdmiGrupoPromocion']->getId(),
                                                              'tipo'             => $arrayParamTipoPromocion['strTipoPromocion'],
                                                              'estado'           =>'Activo'));

        if(is_object($objAdmiTipoPromocion))
        {
            $arrayAdmiTipoPromocionRegla = $this->emcom->getRepository('schemaBundle:AdmiTipoPromocionRegla')
                                                       ->findBy(array('tipoPromocionId' => $objAdmiTipoPromocion->getId()));

            foreach($arrayAdmiTipoPromocionRegla as $objAdmiTipoPromocionRegla)
            {
                $objAdmiTipoPromocionRegla->setEstado('Eliminado');
                $objAdmiTipoPromocionRegla->setFeUltMod($arrayParamTipoPromocion['strFeUltMod']);
                $objAdmiTipoPromocionRegla->setUsrUltMod($arrayParamTipoPromocion['strUsrUltMod']);
                $objAdmiTipoPromocionRegla->setIpUltMod($arrayParamTipoPromocion['strIpUltMod']);
                $this->emcom->persist($objAdmiTipoPromocionRegla);
            }
            
            $arrayAdmiTipoPlanProdPromocion = $this->emcom->getRepository('schemaBundle:AdmiTipoPlanProdPromocion')
                                                          ->findBy(array('tipoPromocionId' => $objAdmiTipoPromocion->getId()));
            if(!empty($arrayAdmiTipoPlanProdPromocion))
            {
                foreach($arrayAdmiTipoPlanProdPromocion as $objAdmiTipoPlanProdPromocion)
                {
                    $objAdmiTipoPlanProdPromocion->setEstado('Eliminado');
                    $objAdmiTipoPlanProdPromocion->setFeUltMod($arrayParamTipoPromocion['strFeUltMod']);
                    $objAdmiTipoPlanProdPromocion->setUsrUltMod($arrayParamTipoPromocion['strUsrUltMod']);
                    $objAdmiTipoPlanProdPromocion->setIpUltMod($arrayParamTipoPromocion['strIpUltMod']);
                    $this->emcom->persist($objAdmiTipoPlanProdPromocion);
                }
            }
            
            $objAdmiTipoPromocion->setEstado('Eliminado');
            $objAdmiTipoPromocion->setFeUltMod($arrayParamTipoPromocion['strFeUltMod']);
            $objAdmiTipoPromocion->setUsrUltMod($arrayParamTipoPromocion['strUsrUltMod']);
            $objAdmiTipoPromocion->setIpUltMod($arrayParamTipoPromocion['strIpUltMod']);
            $this->emcom->persist($objAdmiTipoPromocion);
        }
    }
    
    /**
     * ingresarPlanProd()
     * Función que inserta Productos o Planes en AdmiTipoPlanProdPromocion, relacionados con su respectiva Promoción.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 04-04-2019     
     * @param array $arrayParametros[    
     *                                'intIdTipoPromocion' => Id del Tipo de Promoción
     *                                'intIdPlan'          => Id del Plan
     *                                'intIdProducto'      => Id del Producto  
     *                              ]    
     */
    public function ingresarPlanProd($arrayParametros)
    {
        $objAdmiTipoPromocion = $this->emcom->getRepository('schemaBundle:AdmiTipoPromocion')->find($arrayParametros['intIdTipoPromocion']);
        if(!is_object($objAdmiTipoPromocion))
        {
            throw new \Exception("No se pudo crear la Promoción, No se encontró el Tipo de Promoción a ingresar.");
        }
        
        $objAdmiTipoPlanProdPromocion = new AdmiTipoPlanProdPromocion();
        $objAdmiTipoPlanProdPromocion->setTipoPromocionId($objAdmiTipoPromocion);
        
        if(isset($arrayParametros['intIdPlan']) && !empty($arrayParametros['intIdPlan']))
        {
            $objPlan = $this->emcom->getRepository('schemaBundle:InfoPlanCab')->find($arrayParametros['intIdPlan']);
            if(!is_object($objPlan))
            {
                throw new \Exception("No se pudo crear la Promoción, No se encontró el Plan a ingresar.");
            }
            $objAdmiTipoPlanProdPromocion->setPlanId($objPlan);
        }
        if(isset($arrayParametros['intIdProducto']) && !empty($arrayParametros['intIdProducto']))
        {
            $objProducto = $this->emcom->getRepository('schemaBundle:AdmiProducto')->find($arrayParametros['intIdProducto']);
            if(!is_object($objProducto))
            {
                throw new \Exception("No se pudo crear la Promoción, No se encontró el Producto a ingresar.");
            }
            $objAdmiTipoPlanProdPromocion->setProductoId($objProducto);
        }
        $objAdmiTipoPlanProdPromocion->setFeCreacion($arrayParametros['strFeCreacion']);
        $objAdmiTipoPlanProdPromocion->setUsrCreacion($arrayParametros['strUsrCreacion']);
        $objAdmiTipoPlanProdPromocion->setIpCreacion($arrayParametros['strIpCreacion']);
        $objAdmiTipoPlanProdPromocion->setEstado('Activo');
        $this->emcom->persist($objAdmiTipoPlanProdPromocion);
        $this->emcom->flush();
    }

    /**
     * editarPlanProd()
     * Función que edita las reglas para Promoción de Mix, Planes y Productos
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 22-04-2019    
     * @param array $arrayParametros[               
     *                                'intIdTipoPromocion   => Id del Tipo Promoción  ADMI_TIPO_PROMOCION
     *                                'intIdPlan'           => Id del plan a editar,
     *                                'intIdProducto'       => Id del producto a editar,
     *                                'strFeUltMod'         => Fecha de Edición,
     *                                'strUsrUltMod'        => Usuario de Edición,
     *                                'strIpUltMod'         => Ip de Edición,
     *                              ]  
     */
    public function editarPlanProd($arrayParametros)
    {
        $objAdmiTipoPromocion = $this->emcom->getRepository('schemaBundle:AdmiTipoPromocion')->find($arrayParametros['intIdTipoPromocion']);
        if(!is_object($objAdmiTipoPromocion))
        {
            throw new \Exception("No se pudo editar la Promoción, No se encontró el Tipo de Promoción a ingresar.");
        }

        if(isset($arrayParametros['intIdPlan']) && !empty($arrayParametros['intIdPlan']))
        {
            $objPlan = $this->emcom->getRepository('schemaBundle:InfoPlanCab')->find($arrayParametros['intIdPlan']);
            if(!is_object($objPlan))
            {
                throw new \Exception("No se pudo editar la Promoción, No se encontró el Plan a editar.");
            }
            $objAdmiTipoPlanProdPromocion = $this->emcom->getRepository('schemaBundle:AdmiTipoPlanProdPromocion')
                                                        ->findOneBy(array('tipoPromocionId' => $arrayParametros['intIdTipoPromocion'],
                                                                          'planId'          => $objPlan));
            if(is_object($objAdmiTipoPlanProdPromocion))
            {
                $objAdmiTipoPlanProdPromocion->setEstado('Activo');
                $objAdmiTipoPlanProdPromocion->setFeUltMod($arrayParametros['strFeUltMod']);
                $objAdmiTipoPlanProdPromocion->setUsrUltMod($arrayParametros['strUsrUltMod']);
                $objAdmiTipoPlanProdPromocion->setIpUltMod($arrayParametros['strIpUltMod']);

                $this->emcom->persist($objAdmiTipoPlanProdPromocion);
                $this->emcom->flush();
            }
            else
            {
                $objAdmiTipoPlanProdPromocionNuevo = new AdmiTipoPlanProdPromocion();
                $objAdmiTipoPlanProdPromocionNuevo->setTipoPromocionId($objAdmiTipoPromocion);
                $objAdmiTipoPlanProdPromocionNuevo->setPlanId($objPlan);
                $objAdmiTipoPlanProdPromocionNuevo->setFeCreacion($arrayParametros['strFeUltMod']);
                $objAdmiTipoPlanProdPromocionNuevo->setUsrCreacion($arrayParametros['strUsrUltMod']);
                $objAdmiTipoPlanProdPromocionNuevo->setIpCreacion($arrayParametros['strIpUltMod']);
                $objAdmiTipoPlanProdPromocionNuevo->setEstado('Activo');
                $this->emcom->persist($objAdmiTipoPlanProdPromocionNuevo);
                $this->emcom->flush();
            }
        }

        if(isset($arrayParametros['intIdProducto']) && !empty($arrayParametros['intIdProducto']))
        {
            $objProducto = $this->emcom->getRepository('schemaBundle:AdmiProducto')->find($arrayParametros['intIdProducto']);
            if(!is_object($objProducto))
            {
                throw new \Exception("No se pudo editar la Promoción, No se encontró el Producto a editar.");
            }
            $objAdmiTipoPlanProdPromocion = $this->emcom->getRepository('schemaBundle:AdmiTipoPlanProdPromocion')
                                                        ->findOneBy(array('tipoPromocionId' => $arrayParametros['intIdTipoPromocion'],
                                                                          'productoId'      => $objProducto));
            if(is_object($objAdmiTipoPlanProdPromocion))
            {
                $objAdmiTipoPlanProdPromocion->setEstado('Activo');
                $objAdmiTipoPlanProdPromocion->setFeUltMod($arrayParametros['strFeUltMod']);
                $objAdmiTipoPlanProdPromocion->setUsrUltMod($arrayParametros['strUsrUltMod']);
                $objAdmiTipoPlanProdPromocion->setIpUltMod($arrayParametros['strIpUltMod']);

                $this->emcom->persist($objAdmiTipoPlanProdPromocion);
                $this->emcom->flush();
            }
            else
            {
                $objAdmiTipoPlanProdPromocionNuevo = new AdmiTipoPlanProdPromocion();
                $objAdmiTipoPlanProdPromocionNuevo->setTipoPromocionId($objAdmiTipoPromocion);
                $objAdmiTipoPlanProdPromocionNuevo->setProductoId($objProducto);
                $objAdmiTipoPlanProdPromocionNuevo->setFeCreacion($arrayParametros['strFeUltMod']);
                $objAdmiTipoPlanProdPromocionNuevo->setUsrCreacion($arrayParametros['strUsrUltMod']);
                $objAdmiTipoPlanProdPromocionNuevo->setIpCreacion($arrayParametros['strIpUltMod']);
                $objAdmiTipoPlanProdPromocionNuevo->setEstado('Activo');
                $this->emcom->persist($objAdmiTipoPlanProdPromocionNuevo);
                $this->emcom->flush();
            }
        }
    }

    /**
     * ingresarGrupoPromocionRegla()
     * Función que inserta las reglas en AdmiGrupoPromocionRegla, relacionadas con el Grupo Promoción.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 04-04-2019     
     * @param array $arrayParamReglas[    
     *                                 'intIdGrupoPromocion' => Id del Grupo de Promoción
     *                                 'strRegla'            => Descripción de las reglas o Características a insertarse
     *                                 'strValorRegla'       => Valor de Regla
     *                                 'strFeCreacion'       => Fecha de Creación del Registro
     *                                 'strUsrCreacion'      => Usuario de Creación del registro
     *                                 'strIpCreacion'       => Ip de Creación del registro     
     *                               ]
     * 
     */
    public function ingresarGrupoPromocionRegla($arrayParamReglas)
    {
        $objAdmiGrupoPromocion = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->find($arrayParamReglas['intIdGrupoPromocion']);
        if(!is_object($objAdmiGrupoPromocion))
        {
            throw new \Exception("No se pudo crear la Promoción, No se encontró el Grupo de Promoción a ingresar");
        }
        $objCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                         ->findOneBy(array('descripcionCaracteristica' => $arrayParamReglas['strRegla'],
                                                           'tipo'                      => 'COMERCIAL',
                                                           'estado'                    => 'Activo'));
        if(!is_object($objCaracteristica))
        {
            throw new \Exception("No se pudo crear la Promoción, No se encontró la Regla definida para el Grupo de Promoción.");
        }
        $objAdmiGrupoPromocionRegla = new AdmiGrupoPromocionRegla();
        $objAdmiGrupoPromocionRegla->setGrupoPromocionId($objAdmiGrupoPromocion);
        $objAdmiGrupoPromocionRegla->setCaracteristicaId($objCaracteristica);
        $objAdmiGrupoPromocionRegla->setValor($arrayParamReglas['strValorRegla']);
        $objAdmiGrupoPromocionRegla->setFeCreacion($arrayParamReglas['strFeCreacion']);
        $objAdmiGrupoPromocionRegla->setUsrCreacion($arrayParamReglas['strUsrCreacion']);
        $objAdmiGrupoPromocionRegla->setIpCreacion($arrayParamReglas['strIpCreacion']);
        $objAdmiGrupoPromocionRegla->setEstado('Activo');

        if($arrayParamReglas['strRegla'] === "PROM_JURISDICCION" || $arrayParamReglas['strRegla'] === "PROM_CANTON" ||
           $arrayParamReglas['strRegla'] === "PROM_PARROQUIA" || $arrayParamReglas['strRegla'] === "PROM_SECTOR" ||
           $arrayParamReglas['strRegla'] === "PROM_ELEMENTO" || $arrayParamReglas['strRegla'] === "PROM_EDIFICIO")
        {
            $objAdmiGrupoPromocionRegla->setSecuencia($arrayParamReglas['intNumeroSecuencia']);
        }

        $this->emcom->persist($objAdmiGrupoPromocionRegla);
        $this->emcom->flush();
    }
    
    /**
     * actualizarGrupoPromocionRegla()
     * Función que actualiza las reglas del grupo de la promoción.
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 16-04-2019    
     * @param array $arrayParametros[               
     *                                'intIdGrupoPromocion  => Id del Grupo Promoción  ADMI_GRUPO_PROMOCION
     *                                'strRegla'            => Regla del Grupo de Promoción,
     *                                'strFeCreacion'       => Fecha de Edición,
     *                                'strUsrCreacion'      => Usuario de Edición,
     *                                'strIpCreacion'       => Ip de Edición,
     *                                'strAccion'           => Acción para defifinir si se crea, edita o elimina,
     *                                'strValorRegla'       => Valor de Regla del Grupo de Promoción
     *                              ]  
     */
    public function actualizarGrupoPromocionRegla($arrayParamReglas)
    {
        $objAdmiGrupoPromocion = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->find($arrayParamReglas['intIdGrupoPromocion']);
        if(!is_object($objAdmiGrupoPromocion))
        {
            throw new \Exception("No se pudo editar la Promoción, No se encontró el Grupo de Promoción a ingresar.");
        }

        $objCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                         ->findOneBy(array('descripcionCaracteristica' => $arrayParamReglas['strRegla'],
                                                           'tipo'                      => 'COMERCIAL',
                                                           'estado'                    => 'Activo'));
        if(!is_object($objCaracteristica))
        {
            throw new \Exception("No se pudo editar la Promoción, No se encontró la Regla definida para el Grupo de Promoción.");
        }

        $arrayParametroGrupoRegla = array('intIdGrupoPromocion' => $arrayParamReglas['intIdGrupoPromocion'],
                                          'intCaracteristicaId' => $objCaracteristica->getId(),
                                          'arrayEstados'        => array('Eliminado')
                                         );
        $objAdmiGrupoPromocionRegla = $this->emcom->getRepository("schemaBundle:AdmiGrupoPromocion")
                                                  ->getGrupoPromocionReglaEstado($arrayParametroGrupoRegla);

        if(is_object($objAdmiGrupoPromocionRegla))
        {
            $objAdmiGrupoPromocionRegla->setFeUltMod($arrayParamReglas['strFeCreacion']);
            $objAdmiGrupoPromocionRegla->setUsrUltMod($arrayParamReglas['strUsrCreacion']);
            $objAdmiGrupoPromocionRegla->setIpUltMod($arrayParamReglas['strIpCreacion']);

            if($arrayParamReglas['strAccion'] === 'EDITAR')
            {
                $objAdmiGrupoPromocionRegla->setValor($arrayParamReglas['strValorRegla']);
            }
            elseif($arrayParamReglas['strAccion'] === 'ELIMINAR')
            {
                $objAdmiGrupoPromocionRegla->setEstado('Eliminado');
            }
            $this->emcom->persist($objAdmiGrupoPromocionRegla);
            $this->emcom->flush();
        }
        else
        {
            if($arrayParamReglas['strAccion'] === 'EDITAR')
            {
                $objAdmiGrupoPromocionRegla = new AdmiGrupoPromocionRegla();
                $objAdmiGrupoPromocionRegla->setGrupoPromocionId($objAdmiGrupoPromocion);
                $objAdmiGrupoPromocionRegla->setCaracteristicaId($objCaracteristica);
                $objAdmiGrupoPromocionRegla->setValor($arrayParamReglas['strValorRegla']);
                $objAdmiGrupoPromocionRegla->setFeCreacion($arrayParamReglas['strFeCreacion']);
                $objAdmiGrupoPromocionRegla->setUsrCreacion($arrayParamReglas['strUsrCreacion']);
                $objAdmiGrupoPromocionRegla->setIpCreacion($arrayParamReglas['strIpCreacion']);
                $objAdmiGrupoPromocionRegla->setEstado($objAdmiGrupoPromocion->getEstado());

                $this->emcom->persist($objAdmiGrupoPromocionRegla);
                $this->emcom->flush();
            }
        }
    }

    /**
     * actualizarGrupoPromocionReglaSectorizacion()
     * Función que actualiza las reglas del grupo de la promoción(Reglas de Sectorización:Jurisdicción,Cantón, Parroquia, Sector/olt).
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 17-04-2019    
     * @param array $arrayParametros[               
     *                                'intIdGrupoPromocion  => Id del Grupo Promoción  ADMI_GRUPO_PROMOCION
     *                                'strRegla'            => Regla del Grupo de Promoción,
     *                                'strFeCreacion'       => Fecha de Edición,
     *                                'strUsrCreacion'      => Usuario de Edición,
     *                                'strIpCreacion'       => Ip de Edición,
     *                                'strAccion'           => Acción para defifinir si se crea, edita o elimina,
     *                                'strValorRegla'       => Valor de Regla del Grupo de Promoción,
     *                                'intNumeroSecuencia'  => Numero de Secuencia, para agrupar la Sectorización
     *                              ]  
     */
    public function actualizarGrupoPromocionReglaSectorizacion($arrayParamReglas)
    {
        $objAdmiGrupoPromocion = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->find($arrayParamReglas['intIdGrupoPromocion']);
        if(!is_object($objAdmiGrupoPromocion))
        {
            throw new \Exception("No se pudo editar la Promoción, No se encontró el Grupo de Promoción a ingresar.");
        }

        $objCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                         ->findOneBy(array('descripcionCaracteristica' => $arrayParamReglas['strRegla'],
                                                           'tipo'                      => 'COMERCIAL',
                                                           'estado'                    => 'Activo'));
        if(!is_object($objCaracteristica))
        {
            throw new \Exception("No se pudo editar la Promoción, No se encontró la Regla definida para el Grupo de Promoción.");
        }

        if($arrayParamReglas['strAccion'] === "NUEVO")
        {
            $objAdmiGrupoPromocionRegla = new AdmiGrupoPromocionRegla();
            $objAdmiGrupoPromocionRegla->setGrupoPromocionId($objAdmiGrupoPromocion);
            $objAdmiGrupoPromocionRegla->setCaracteristicaId($objCaracteristica);
            $objAdmiGrupoPromocionRegla->setValor($arrayParamReglas['strValorRegla']);
            $objAdmiGrupoPromocionRegla->setFeCreacion($arrayParamReglas['strFeCreacion']);
            $objAdmiGrupoPromocionRegla->setUsrCreacion($arrayParamReglas['strUsrCreacion']);
            $objAdmiGrupoPromocionRegla->setIpCreacion($arrayParamReglas['strIpCreacion']);
            $objAdmiGrupoPromocionRegla->setEstado($objAdmiGrupoPromocion->getEstado());
            $objAdmiGrupoPromocionRegla->setSecuencia($arrayParamReglas['intNumeroSecuencia']);

            $this->emcom->persist($objAdmiGrupoPromocionRegla);
            $this->emcom->flush();
        }
        else
        {
            $arrayParametroGrupoRegla = array('intIdGrupoPromocion' => $arrayParamReglas['intIdGrupoPromocion'],
                                              'intNumeroSecuencia'  => $arrayParamReglas['intNumeroSecuencia'],
                                              'intCaracteristicaId' => $objCaracteristica->getId()
                                             );
            $objAdmiGrupoPromocionRegla = $this->emcom->getRepository("schemaBundle:AdmiGrupoPromocion")
                                                      ->getGrupoPromocionReglaEstado($arrayParametroGrupoRegla);

            if(is_object($objAdmiGrupoPromocionRegla))
            {
                $objAdmiGrupoPromocionRegla->setFeUltMod($arrayParamReglas['strFeCreacion']);
                $objAdmiGrupoPromocionRegla->setUsrUltMod($arrayParamReglas['strUsrCreacion']);
                $objAdmiGrupoPromocionRegla->setIpUltMod($arrayParamReglas['strIpCreacion']);
                $objAdmiGrupoPromocionRegla->setEstado('Activo');

                $this->emcom->persist($objAdmiGrupoPromocionRegla);
                $this->emcom->flush();
            }
        }
    }

    /**
     * Función que obtiene los Olt's, mediante el filtro de idParroquia, estados y idEmpresa
     * 
     * @author Héctor Lozano <hlozano@telconet.ec>
     * @version 1.0 04-03-2019     
     * @param array $arrayParametros[]     
     *              'intIdParroquia'  => Id de la parroquia
     *              'arrayEstados     => Arreglo de estados 
     *              'intIdEmpresa'    => Id de Empresa     
     *              'strTipoElemento' => Tipo de Elemento   
     * @return objeto Olt's
     */
    public function getOlts($arrayParametros)
    {
        $arrayParametrosOlt['intIdParroquia']  = $arrayParametros['intIdParroquia'];
        $arrayParametrosOlt['arrayEstados']    = array('Activo', 'Modificado');
        $arrayParametrosOlt['intIdEmpresa']    = $arrayParametros['intIdEmpresa'];
        $arrayParametrosOlt['strTipoElemento'] = $arrayParametros['strTipoElemento'];

        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getOltsEdificios($arrayParametrosOlt);
    }
    
    /**
     * Función que obtiene los Edificios, mediante el filtro de idParroquia, estados y idEmpresa
     * 
     * @author Héctor Lozano <hlozano@telconet.ec>
     * @version 1.0 08-05-2019     
     * @param array $arrayParametros[]     
     *              'intIdParroquia'  => Id de la parroquia
     *              'arrayEstados     => Arreglo de estados 
     *              'intIdEmpresa'    => Id de Empresa     
     *              'strTipoElemento' => Tipo de Elemento   
     * @return objeto Edificios
     */
    public function getEdificios($arrayParametros)
    {
        $arrayParametrosOlt['intIdParroquia']  = $arrayParametros['intIdParroquia'];
        $arrayParametrosOlt['arrayEstados']    = array('Activo', 'Modificado');
        $arrayParametrosOlt['intIdEmpresa']    = $arrayParametros['intIdEmpresa'];
        $arrayParametrosOlt['strTipoElemento'] = $arrayParametros['strTipoElemento'];

        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getOltsEdificios($arrayParametrosOlt);
    }
    
    /**
     * Función que obtiene los productos, mediante el filtro de nombre estado y idEmpresa
     * 
     * @author Héctor Lozano <hlozano@telconet.ec>
     * @version 1.0 04-03-2019     
     * @param array $arrayParametros[]     
     *              'strNombre'     => Nombre de Producto
     *              'strEstado      => Estado de Producto 
     *              'intIdEmpresa'  => Id de Empresa     
     * @return objeto Productos
     */
    public function getProductos($arrayParametros)
    {
        $arrayParametrosProducto['strEstado']    = 'Activo';
        $arrayParametrosProducto['intIdEmpresa'] = $arrayParametros['intIdEmpresa'];

        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getProductos($arrayParametrosProducto);
    }    
    /**
     * Función que obtiene los planes, mediante el filtro de nombre estado y idEmpresa
     * 
     * @author Héctor Lozano <hlozano@telconet.ec>
     * @version 1.0 04-03-2019  
     * 
     * @author Jessenia Piloso <jpiloso@telconet.ec>
     * @version 1.1 15-09-2022 
     *    
     * @param array $arrayParametros[]     
     *              'strNombre'         => Nombre del Plan
     *              'strEstado          => Estado del Plan 
     *              'intIdEmpresa'      => Id de Empresa  
     *              'strFiltraEstProm'  => variable utilizada para validar 
     *                                     el filtro de los estados en el query que obtiene 
     *                                     los planes destinos para crear promociones de Franja Horaria.
     * @return objeto Planes
     */
    public function getPlanes($arrayParametros)
    {
        $arrayParametrosPlan['strEstado']        = 'Activo';
        $arrayParametrosPlan['intIdEmpresa']     = $arrayParametros['intIdEmpresa'];
        $arrayParametrosPlan['strIdTipoNegocio'] = $arrayParametros['strIdTipoNegocio'];
        $arrayParametrosPlan['strFiltraEstProm'] = $arrayParametros['strFiltraEstProm']
                                                   ? $arrayParametros['strFiltraEstProm']: "" ;

        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getPlanes($arrayParametrosPlan);
    }

    /**
     * Función que obtiene los planes no seleccionados, mediante el filtro de idEmpresa y tipo de promocion
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 26-11-2020
     * @param array $arrayParametros["intIdEmpresa"      => Id Empresa,
     *                               "strIdTipoPromocion => Codigo del tipo de empresa"]
     * @return objeto Planes
    */
    public function getPlanesNoSeleccionados($arrayParametros)
    {
        $intIdEmpresa     = $arrayParametros['intIdEmpresa'];
        $strTipoPromocion = $arrayParametros['strIdTipoPromocion'];
        // Parametrizamos los valores de la consulta
        $arrayDatosConsulta = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                ->getOne('PROMOCION ANCHO BANDA', 'COMERCIAL','PROMO_ANCHO_BANDA',
                                        'Datos para consultas de planes para promocion',
                                        $strTipoPromocion,
                                        '','','','',
                                        $intIdEmpresa);
        if (!empty($arrayDatosConsulta))
        {
            $strProducto       = $arrayDatosConsulta["valor2"];
            $strCaracteristica = $arrayDatosConsulta["valor3"];
            $strEstadosPromo   = $arrayDatosConsulta["valor4"];
            $strEstadosPlanes  = $arrayDatosConsulta["valor5"];
            $strEstado         = 'Activo';

            $arrayParametrosPlan = array (
                'strTipoPromocion'  => $strTipoPromocion,
                'intIdEmpresa'      => $intIdEmpresa,
                'strProducto'       => $strProducto,
                'strCaracteristica' => $strCaracteristica,
                'strEstadosPromo'   => $strEstadosPromo,
                'strEstadosPlanes'  => $strEstadosPlanes,
                'strEstado'         => $strEstado
            );
            return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                ->getPlanesNoSeleccionados($arrayParametrosPlan);
        }
        else
        {
            return null;
        }
    }
    
    /**
     * Función que obtiene las Permanencias Mínimas, mediante el filtro de estado y idEmpresa
     * 
     * @author Héctor Lozano <hlozano@telconet.ec>
     * @version 1.0 04-03-2019    
     * @param array $arrayParametros[]     
     *              'strEstado      => Estado de la permanencia mínima 
     *              'intIdEmpresa'  => Id de Empresa     
     * @return objeto Permanencia mínima
     */
    public function obtenerPermanenciaMinima($arrayParametros)
    {
        $arrayParametrosPermanencia['strEstado']    = 'Activo';
        $arrayParametrosPermanencia['intIdEmpresa'] = $arrayParametros['intIdEmpresa'];

        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->obtenerPermanenciaMinima($arrayParametrosPermanencia);
    }      
    
    
    /**
     * Función que obtiene las Permanencias Mínimas, utilizadas para la cancelaion voluntaria.
     * 
     * @author Héctor Lozano <hlozano@telconet.ec>
     * @version 1.0 28-07-2022    
     * @param array $arrayParametros[]     
     *              'strEstado      => Estado de la permanencia mínima 
     *              'intIdEmpresa'  => Id de Empresa     
     * @return objeto Permanencia mínima
     */
    public function obtenerPermanenciaMinPromoCancelVol($arrayParametros)
    {
        $arrayParametrosPermanencia['strEstado']    = 'Activo';
        $arrayParametrosPermanencia['intIdEmpresa'] = $arrayParametros['intIdEmpresa'];

        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->obtenerPermanenciaMinPromoCancelVol($arrayParametrosPermanencia);
    }      
    
    
    /**
    * getSectorizacion, obtiene las sectorizaciones de una promoción.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 24-04-2019    
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.1 14-10-2019 - Se inicializa la variable "$strElementos" en la validación de edificio por motivos
    *                           que se estaban concadenando los OLT.
    *
    * @param array $arrayParametros []
    *              'intIdPromocion' => Id de la promoción.
    *              'strEstado'      => Estado de la promoción.    
    * @return Response lista de las sectorizaciones de una promoción.
    */
    public function getSectorizacion($arrayParametros)
    {
        $objSectorizacion   = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                          ->getSectorizacion($arrayParametros);

        foreach($objSectorizacion['objSectorizacion'] as $caracteristica)
        {
            $arrayCaracteristicas['intSectorizacion']   = (int)$caracteristica['idSectorizacion'];
            $arrayCaracteristicas['intJurisdiccion']    = (int)$caracteristica['idJurisdiccion'];
            $arrayCaracteristicas['strJurisdiccion']    = $caracteristica['descJurisdiccion'];
            $arrayCaracteristicas['intCanton']          = (int)$caracteristica['idCanton'];
            if($caracteristica['idCanton'] === "0")
            {
                $arrayCaracteristicas['strCanton']  = "TODOS"; 
            }
            else
            {
                $arrayCaracteristicas['strCanton']  = $caracteristica['descCanton'];
            }
            $arrayCaracteristicas['intParroquia']       = (int)$caracteristica['idParroquia'];
            if($caracteristica['idParroquia'] === "0")
            {
                $arrayCaracteristicas['strParroquia']   = "TODOS"; 
            }
            else
            {
                $arrayCaracteristicas['strParroquia']   = $caracteristica['descParroquia'];
            }
            if($caracteristica['idSector'] === "0" && $caracteristica['idElemento'] === "0" && $caracteristica['idEdificio'] === "0")
            {
                $arrayCaracteristicas['strSectOltEdif']     = "TODOS";
                $arrayCaracteristicas['intSectOltEdif']     = (int)"0";
                $arrayCaracteristicas['strSectorOltEdif']   = "";
            }
            else 
            {
                if(!empty($caracteristica['idElemento']) && $caracteristica['idElemento'] !== "0")
                {
                    $arrayElemento                      = explode(",", $caracteristica['idElemento']);
                    $strElementos                       = "";
                    for ($i=0; $i<count($arrayElemento); $i++)
                    {
                        $strIdElemento                          = ($arrayElemento[$i]);
                        $arrayParametrosOlt['intIdElemento']    = (int)$strIdElemento;
                        $arrayParametrosOlt['intIdEmpresa']     = $arrayParametros['intIdEmpresa'];
                        $arrayParametrosOlt['strTipoElemento']  = 'OLT';
                        $objElemento                            = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                                              ->getOltEdificioById($arrayParametrosOlt);

                        foreach($objElemento as $elemento)
                        {
                            $strElemento = $elemento['nombre'];
                        }
                        $strElementos                           = $strElementos.",".$strElemento;
                    }
                    $arrayCaracteristicas['strSectOltEdif']     = "olt";
                    $arrayCaracteristicas['intSectOltEdif']     = $caracteristica['idElemento'];
                    $arrayCaracteristicas['strSectorOltEdif']   = substr($strElementos, 1);
                }
                if(!empty($caracteristica['idSector']) && $caracteristica['idSector'] !== "0")
                {
                    $arraySector                        = explode(",", $caracteristica['idSector']);
                    $strSectores                        = "";

                    for ($i=0; $i<count($arraySector); $i++)
                    {
                        $strIdSector    = ($arraySector[$i]);
                        $objSector      = $this->emInfraestructura->getRepository('schemaBundle:AdmiSector')
                                                                  ->find((int)$strIdSector);
                        $strSector      = $objSector->getNombreSector();
                        $strSectores    = $strSectores.",".$strSector;
                    }
                    $arrayCaracteristicas['strSectOltEdif']     = "sector";
                    $arrayCaracteristicas['intSectOltEdif']     = $caracteristica['idSector'];
                    $arrayCaracteristicas['strSectorOltEdif']   = substr($strSectores, 1);
                }
                if(!empty($caracteristica['idEdificio']) && $caracteristica['idEdificio'] !== "0")
                {
                    $arrayEdificio                      = explode(",", $caracteristica['idEdificio']);                    
                    $strElementos                       = "";
                    for ($i=0; $i<count($arrayEdificio); $i++)
                    {
                        $strIdEdificio                              = ($arrayEdificio[$i]);
                        $arrayParametrosEdificio['intIdElemento']   = (int)$strIdEdificio;
                        $arrayParametrosEdificio['intIdEmpresa']    = $arrayParametros['intIdEmpresa'];
                        $arrayParametrosEdificio['strTipoElemento'] = 'EDIFICACION';
                        $objEdificio                                = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                                                  ->getOltEdificioById($arrayParametrosEdificio);
                        foreach($objEdificio as $elemento)
                        {
                            $strElemento = $elemento['nombre'];
                        }
                        $strElementos                           = $strElementos.",".$strElemento;
                    }
                    $arrayCaracteristicas['strSectOltEdif']     = "edificio";
                    $arrayCaracteristicas['intSectOltEdif']     = $caracteristica['idEdificio'];
                    $arrayCaracteristicas['strSectorOltEdif']   = substr($strElementos, 1);
                }
            }
            $arraySectorizacion [] = $arrayCaracteristicas;
        }
        return $arraySectorizacion;
    }                   
    /**
     * guardarTipoPromocion()
     * Función que guarda el tipo de la Promoción, con sus respectivas reglas.
     * 
     * @author Hector Lozano hlozano@telconet.ec>
     * @version 1.0 01-04-2019           
     * @param array $arrayParamTipoPromocion[                 
     *                                         'strTipoPromocion'       => Tipo de Promoción,
     *                                         'strCodEmpresa'          => Código de Empresa,
     *                                         'objAdmiGrupoPromocion'  => Objeto AdmiGrupoPromocion,
     *                                         'strFeCreacion'          => Fecha de Creación,
     *                                         'strUsrCreacion'         => Usuario de Creación,
     *                                         'strIpCreacion'          => Ip de Creación,
     *                                         'strPermanenciaMinima'   => Permanencia Mínima,
     *                                         'strMora'                => Tiene Mora (SI/NO),
     *                                         'strIndefinida'          => Promoción Indefinida(SI/NO),
     *                                         'fltDescIndefinido'      => Descuento Indefinido
     *                                         'strTipoPeriodo'         => Tipo de Período (Unico/Variable),
     *                                         'arrayDescuentoPeriodo'  => Descuento por período,
     *                                         'arrayPlanes'            => Arreglo de Planes,
     *                                         'arrayProductos'         => Arreglo de Productos
     *                                       ]                       
     */
    public function guardarTipoPromocion($arrayParamTipoPromocion)
    {
        $arrayParametros = array('strTipoPromocion' => $arrayParamTipoPromocion['strTipoPromocion'],
                                 'strCodEmpresa'    => $arrayParamTipoPromocion['strCodEmpresa']
                                );
        $arrayParametrosPromocion = $this->obtenerParametrosPromocion($arrayParametros);
        $strTipoPromocion         = $arrayParametrosPromocion['strTipoPromocion'];
        $strCodTipoPromocion      = $arrayParametrosPromocion['strCodTipoPromocion'];

        $objAdmiTipoPromocion = new AdmiTipoPromocion();
        $objAdmiTipoPromocion->setGrupoPromocionId($arrayParamTipoPromocion['objAdmiGrupoPromocion']);
        $objAdmiTipoPromocion->setCodigoTipoPromocion($strCodTipoPromocion);
        $objAdmiTipoPromocion->setTipo($strTipoPromocion);
        $objAdmiTipoPromocion->setFeCreacion($arrayParamTipoPromocion['strFeCreacion']);
        $objAdmiTipoPromocion->setUsrCreacion($arrayParamTipoPromocion['strUsrCreacion']);
        $objAdmiTipoPromocion->setIpCreacion($arrayParamTipoPromocion['strIpCreacion']);
        $objAdmiTipoPromocion->setEstado('Activo');
        $this->emcom->persist($objAdmiTipoPromocion);
        $this->emcom->flush();

        if(isset($arrayParamTipoPromocion['strPermanenciaMinima']) && !empty($arrayParamTipoPromocion['strPermanenciaMinima']))
        {
            $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                      'strRegla'           => 'PROM_PERMANENCIA_MINIMA',
                                      'strValorRegla'      => $arrayParamTipoPromocion['strPermanenciaMinima'],
                                      'strFeCreacion'      => $arrayParamTipoPromocion['strFeCreacion'],
                                      'strUsrCreacion'     => $arrayParamTipoPromocion['strUsrCreacion'],
                                      'strIpCreacion'      => $arrayParamTipoPromocion['strIpCreacion']
                                     );
            $this->ingresarTipoPromocionRegla($arrayParamReglas);
        }
        if(isset($arrayParamTipoPromocion['strMora']) && !empty($arrayParamTipoPromocion['strMora']))
        {
            $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                      'strRegla'           => 'PROM_PIERDE_PROMOCION_MORA',
                                      'strValorRegla'      => $arrayParamTipoPromocion['strMora'],
                                      'strFeCreacion'      => $arrayParamTipoPromocion['strFeCreacion'],
                                      'strUsrCreacion'     => $arrayParamTipoPromocion['strUsrCreacion'],
                                      'strIpCreacion'      => $arrayParamTipoPromocion['strIpCreacion']
                                     );
            $this->ingresarTipoPromocionRegla($arrayParamReglas);
        }
        if(isset($arrayParamTipoPromocion['intValMora']) && !empty($arrayParamTipoPromocion['intValMora']))
        {
            $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                      'strRegla'           => 'PROM_DIAS_MORA',
                                      'strValorRegla'      => $arrayParamTipoPromocion['intValMora'],
                                      'strFeCreacion'      => $arrayParamTipoPromocion['strFeCreacion'],
                                      'strUsrCreacion'     => $arrayParamTipoPromocion['strUsrCreacion'],
                                      'strIpCreacion'      => $arrayParamTipoPromocion['strIpCreacion']
                                     );
            $this->ingresarTipoPromocionRegla($arrayParamReglas);
        }
        if(isset($arrayParamTipoPromocion['strIndefinida']) && !empty($arrayParamTipoPromocion['strIndefinida']))
        {
            $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                      'strRegla'           => 'PROM_PROMOCION_INDEFINIDA',
                                      'strValorRegla'      => $arrayParamTipoPromocion['strIndefinida'],
                                      'strFeCreacion'      => $arrayParamTipoPromocion['strFeCreacion'],
                                      'strUsrCreacion'     => $arrayParamTipoPromocion['strUsrCreacion'],
                                      'strIpCreacion'      => $arrayParamTipoPromocion['strIpCreacion']
                                     );
            $this->ingresarTipoPromocionRegla($arrayParamReglas);
        }
        if(isset($arrayParamTipoPromocion['fltDescIndefinido']) && !empty($arrayParamTipoPromocion['fltDescIndefinido']))
        {
            $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                      'strRegla'           => 'PROM_DESCUENTO',
                                      'strValorRegla'      => $arrayParamTipoPromocion['fltDescIndefinido'],
                                      'strFeCreacion'      => $arrayParamTipoPromocion['strFeCreacion'],
                                      'strUsrCreacion'     => $arrayParamTipoPromocion['strUsrCreacion'],
                                      'strIpCreacion'      => $arrayParamTipoPromocion['strIpCreacion']
                                     );
            $this->ingresarTipoPromocionRegla($arrayParamReglas);
        }
        if(isset($arrayParamTipoPromocion['strTipoPeriodo']) && !empty($arrayParamTipoPromocion['strTipoPeriodo']))
        {
            $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                      'strRegla'           => 'PROM_TIPO_PERIODO',
                                      'strValorRegla'      => $arrayParamTipoPromocion['strTipoPeriodo'],
                                      'strFeCreacion'      => $arrayParamTipoPromocion['strFeCreacion'],
                                      'strUsrCreacion'     => $arrayParamTipoPromocion['strUsrCreacion'],
                                      'strIpCreacion'      => $arrayParamTipoPromocion['strIpCreacion']
                                     );
            $this->ingresarTipoPromocionRegla($arrayParamReglas);
        }
        if(isset($arrayParamTipoPromocion['arrayDescuentoPeriodo']) && !empty($arrayParamTipoPromocion['arrayDescuentoPeriodo']))
        {
            $strDescuentoPeriodo = implode(",", $arrayParamTipoPromocion['arrayDescuentoPeriodo']);
            $arrayParamReglas    = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                         'strRegla'           => 'PROM_PERIODO',
                                         'strValorRegla'      => $strDescuentoPeriodo,
                                         'strFeCreacion'      => $arrayParamTipoPromocion['strFeCreacion'],
                                         'strUsrCreacion'     => $arrayParamTipoPromocion['strUsrCreacion'],
                                         'strIpCreacion'      => $arrayParamTipoPromocion['strIpCreacion']
                                        );
            $this->ingresarTipoPromocionRegla($arrayParamReglas);
        }
        if(isset($arrayParamTipoPromocion['arrayPlanes']) && !empty($arrayParamTipoPromocion['arrayPlanes']))
        {
            foreach($arrayParamTipoPromocion['arrayPlanes'] as $plan)
            {
                $arrayParametros = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                         'intIdPlan'          => $plan,
                                         'strFeCreacion'      => $arrayParamTipoPromocion['strFeCreacion'],
                                         'strUsrCreacion'     => $arrayParamTipoPromocion['strUsrCreacion'],
                                         'strIpCreacion'      => $arrayParamTipoPromocion['strIpCreacion']
                                        );
                $this->ingresarPlanProd($arrayParametros);
            }
        }
        if(isset($arrayParamTipoPromocion['arrayProductos']) && !empty($arrayParamTipoPromocion['arrayProductos']))
        {
            foreach($arrayParamTipoPromocion['arrayProductos'] as $producto)
            {
                $arrayParametros = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                         'intIdProducto'      => $producto,
                                         'strFeCreacion'      => $arrayParamTipoPromocion['strFeCreacion'],
                                         'strUsrCreacion'     => $arrayParamTipoPromocion['strUsrCreacion'],
                                         'strIpCreacion'      => $arrayParamTipoPromocion['strIpCreacion']
                                        );
                $this->ingresarPlanProd($arrayParametros);
            }
        }
    }

     /**
     * ejecutarProcesoMasivo
     * 
     * Método que ejecuta un proceso masivo de promcioones para actualizar el estado de la misma.
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.0 13-10-2020
     * @param array $arrayParametros[]               
     *              'strTipoPma'       => Ids de los grupos de Promociones ADMI_GRUPO_PROMOCION 
     *              'strOrigenPma'     => Motivo del Proceso del PMA
     *              'strCodEmpresa'    => Observación del Proceso del PMA                 
     *              'strUsrCreacion'   => Usuario en sesión
     *              'strIpCreacion'    => Usuario en sesión
     *              'strEstado'        => Usuario en sesión
     *                     
     * @return $strRespuesta
     */
    public function ejecutarProcesoMasivo($arrayParametros)
    {             
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];        
        $strIpCreacion          = $arrayParametros['strIpCreacion'];

        $this->emcom->beginTransaction();
        try
        {            
            $strRespuesta = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->ejecutarProcesoMasivo($arrayParametros);            
        }
        catch(\Exception $e)
        {
            $this->emcom->rollback();
            $this->emcom->close();           
            $strRespuesta = "No se pudo ejecutar el Proceso Masivo: ".$arrayParametros['strTipoPma'].", <br> ". 
                             $e->getMessage(). ". Favor notificar a Sistemas.";            
            $this->serviceUtil->insertError('Telcos+',
                                            'PromocionService.ejecutarProcesoMasivo',
                                             'Error PromocionService.ejecutarProcesoMasivo: No se pudo ejecutar el Proceso Masivo: '
                                             .$arrayParametros['strTipoPma'].': '.$e->getMessage(),
                                             $strUsrCreacion,
                                             $strIpCreacion);
            return $strRespuesta;
        }
        return $strRespuesta;
    }     
    
    /**
    *
    * validacionPromocionAction Función que valida los código de promociones ingresados por el Usuario.
    * 
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 15-10-2020
    * 
    * @return JsonResponse
    */
    public function validaCodigoPromocion($arrayParametros)
    {
        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->validaCodigoPromocion($arrayParametros);
    }
    
   /**
    * @Secure(roles="ROLE_431-1")
    * validaCodigoPromocionUnico Función que valida si el código promocional ingresado es único.
    * 
    * @author Katherine Yager <kyager@telconet.ec>
    * @version 1.0 05-11-2020
    * 
    * @return JsonResponse
    */
    public function validaCodigoPromocionUnico($arrayParametros)
    {
        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->validaCodigoPromocionUnico($arrayParametros);
    }

        
    /**
    * @Secure(roles="ROLE_431-1")
    * guardarCodigoServicioCaracAction Función que guarda el código promocional en la info servicio característica.
    * 
    * @author Katherine Yager <kyager@telconet.ec
    * @version 1.0 06-11-2020
    * 
    * @return JsonResponse
    */
    public function guardarCodigoServicioCarac($arrayParametros)
    {
        $intServicio       = $arrayParametros['intIdServicio']; 
        $strIpCreacion     = $arrayParametros['strIpCreacion']; 
        $strUsrCreacion    = $arrayParametros['strUsrCreacion']; 
        $strCodigo         = $arrayParametros['strCodigo'];
        $strPromocion      = $arrayParametros['strPromocion']; 
        $strEstado         = $arrayParametros['strEstado']; 
        $strObservacion    = $arrayParametros['strObservacion']; 
        $objCaracteristica = $arrayParametros['objCaracteristica']; 
        $strIdTipoPromocion= $arrayParametros['strIdTipoPromocion']; 
        $strFeCreacion     = new \DateTime('now');

        try
        {  
            $objServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')->findOneById( $intServicio );
          
            if(!is_object($objServicio))
            {
                throw new \Exception("No se ha encontrado el servicio.");
            }
            
            $objCaracteristicaExist =  $this->emcom->getRepository('schemaBundle:InfoServicioCaracteristica')
                                                   ->findOneBy(array("servicioId"       => $objServicio,
                                                                     "caracteristicaId" => $objCaracteristica,
                                                                     "estado"           => 'Activo')); 

            if(is_object($objCaracteristicaExist))
            {
                $objCaracteristicaExist->setFeCreacion($strFeCreacion);
                $objCaracteristicaExist->setUsrCreacion($strUsrCreacion);
                $objCaracteristicaExist->setIpCreacion($strIpCreacion);
                $objCaracteristicaExist->setEstado('Inactivo');
                $this->emcom->persist($objCaracteristicaExist);
                
                //Historial del servicio por ingreso de Código promocional
                $objInfoServicioHistorial= new InfoServicioHistorial();
                $objInfoServicioHistorial->setServicioId($objServicio);
                $objInfoServicioHistorial->setObservacion("Se eliminan los códigos promocionales en estado activo,"
                                                          . " para priorizar códigos promocionales de tipo mix.");
                $objInfoServicioHistorial->setEstado($objServicio->getEstado());
                $objInfoServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objInfoServicioHistorial->setIpCreacion($strIpCreacion);
                $this->emcom->persist($objInfoServicioHistorial);
            }
            
            $objInfoServicioCaracteristica = new InfoServicioCaracteristica();
            $objInfoServicioCaracteristica->setServicioId($objServicio);
            $objInfoServicioCaracteristica->setFeCreacion($strFeCreacion);
            $objInfoServicioCaracteristica->setIpCreacion($strIpCreacion);
            $objInfoServicioCaracteristica->setUsrCreacion($strUsrCreacion);
            $objInfoServicioCaracteristica->setValor($strIdTipoPromocion);
            $objInfoServicioCaracteristica->setEstado($strEstado);
            $objInfoServicioCaracteristica->setObservacion($strObservacion);
            $objInfoServicioCaracteristica->setCaracteristicaId($objCaracteristica);
            $this->emcom->persist($objInfoServicioCaracteristica);

            //Historial del servicio por ingreso de Código promocional
            $objInfoServicioHistorial= new InfoServicioHistorial();
            $objInfoServicioHistorial->setServicioId($objServicio);
            $objInfoServicioHistorial->setObservacion("Se agregó el código promocional: {$strCodigo},de la promoción"
                                                      . " {$strPromocion}");
            $objInfoServicioHistorial->setEstado($objServicio->getEstado());
            $objInfoServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objInfoServicioHistorial->setIpCreacion($strIpCreacion);
            $this->emcom->persist($objInfoServicioHistorial);
            $this->emcom->flush();
            $strRespuesta = 'OK';
        }
        catch(\Exception $e)
        {

            $strRespuesta = "No se pudo guardar la característica del servicio del código de Promoción <br>"
                            . $e->getMessage() . ". Favor notificar a Sistemas.";
            $this->serviceUtil->insertError('Telcos+',
                                            'PromocionService.guardarCodigoServicioCarac',
                                             'Error PromocionService.guardarCodigoServicioCarac:'.$e->getMessage(),
                                             $strUsrCreacion,
                                             $strIpCreacion);
            return $strRespuesta;
        }
        return $strRespuesta;   
    }
    
    /**
    * @Secure(roles="ROLE_431-1")
    * validaCodigoPromocionUnico Función que valida si el código promocional ingresado existe.
    * 
    * @author Katherine Yager <kyager@telconet.ec>
    * @version 1.0 05-11-2020
    * 
    * @return JsonResponse
    */
    public function validaCodigoPromocionExist($arrayParametros)
    {
        $strCodEmpresa        = $arrayParametros['strCodEmpresa'];
        $intServicio          = $arrayParametros['intIdServicio']; 
        $strCaracteristica    = $arrayParametros['strDescripcionCaracteristica']; 
        $strEstado            = $arrayParametros['strEstado']; 
        $strRespuesta         = '';
        $strValorCarac        = '';
      
        $objServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')->findOneById( $intServicio );

        if(!is_object($objServicio))
        {
            throw new \Exception("No se ha encontrado el servicio.");
        }       
               
        $objCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                             ->findOneBy(array('descripcionCaracteristica' => $strCaracteristica,
                                                               'tipo'                      => 'COMERCIAL'));
    
        if(!is_object($objCaracteristica))
        {
            throw new \Exception("No se ha definido la característica : {$strCaracteristica}");
        }     
  
        
        $arrayCaracteristicaServ =  $this->emcom->getRepository('schemaBundle:InfoServicioCaracteristica')
                                                            ->findBy(array("servicioId"               => $objServicio,
                                                                           "caracteristicaId"          => $objCaracteristica,
                                                                           "estado"                    => $strEstado));
        
       
        
           $objCaracteristicaServ = null;
            foreach($arrayCaracteristicaServ as $objCaracteristicaServ)
            {
           
                   $strValorCarac=$objCaracteristicaServ->getValor();
            }
        
        
        
       if($strValorCarac!='')
        {
            $arrayParametrosCodigo['strIdTipoPromocion'] = $strValorCarac;
            $arrayParametrosCodigo['strCodEmpresa']       = $strCodEmpresa;
            $strCodigo    = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                        ->getCodigoPromocion($arrayParametrosCodigo);
           
           
            $strRespuesta = $strCodigo;
        }

        return $strRespuesta;
    }
    
    /**
    * @Secure(roles="ROLE_431-1")
    * validaCodigoPromocionUnico Función que valida si el código promocional tiene mapeo.
    * 
    * @author Katherine Yager <kyager@telconet.ec>
    * @version 1.0 20-11-2020
    * 
    * @return JsonResponse
    */
    public function validaCodigoPromocionEstadoMapeo($arrayParametros)
    {
        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->validaCodigoPromocionEstadoMapeo($arrayParametros);
    }
    
    /**
    *
    * validaCodigoPromocionMasiva Función que valida los código de promociones ingresados por el Usuario.
    * 
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 15-10-2020
    * 
    * @return JsonResponse
    */
    public function validaCodigoPromocionMasiva($arrayParametros)
    {
        $arrayRespuestaPromociones = array();
        $strEsMix                  = 'NO';
        $intIdServicio             = "";
        $strTipoPromocion          = "";
        $strTipoPromo              = "";
        $strIdTipoPromo            = "";
        $intIdPlan                 = "";
        $intIdProducto             = "";
        $strDetalles               = "";
        $strCodigo                 = "";
        $strCodigoMix              = "";
        $strServiciosMix           = "";
        $arrayCodigosPromo         = array();
        $arrayRespuestaServicio    = array();
        try
        {
            $strCodEmpresa             = ( isset($arrayParametros['codEmpresa']) 
                                          && !empty($arrayParametros['codEmpresa']) ) ? $arrayParametros['codEmpresa'] : "";
            $strEsContrato             = ( isset($arrayParametros['strEsContrato']) 
                                          && !empty($arrayParametros['strEsContrato']) ) ? $arrayParametros['strEsContrato'] : "";
            $strFormaPago              = ( isset( $arrayParametros['strFormaPago']) 
                                          && !empty( $arrayParametros['strFormaPago']) ) ?  $arrayParametros['strFormaPago'] : "";
            $strTipoProceso            = ( isset($arrayParametros['strTipoProceso']) 
                                          && !empty($arrayParametros['strTipoProceso']) ) ? $arrayParametros['strTipoProceso'] : "";
            $arrayServicios            = ( isset($arrayParametros['strDatosServicio']) 
                                          && !empty($arrayParametros['strDatosServicio']) ) ? $arrayParametros['strDatosServicio'] : "";
            foreach($arrayServicios as $objServicios)
            {
                $intIdServicio      = ( isset($objServicios['intIdServicio']) 
                                       && !empty($objServicios['intIdServicio']) ) ? $objServicios['intIdServicio'] : "";
                $strTipoPromo       = ( isset($objServicios['strTipoPromo']) 
                                       && !empty($objServicios['strTipoPromo']) ) ? $objServicios['strTipoPromo'] : "";
                $strIdTipoPromo     = ( isset($objServicios['strIdTipoPromo']) 
                                       && !empty($objServicios['strIdTipoPromo']) ) ? $objServicios['strIdTipoPromo'] : "";
                if ($strTipoPromo !== "")
                {
                    if ($strTipoPromo === "package")
                    {
                        $intIdPlan     = $strIdTipoPromo;
                    }
                    else
                    {
                        $intIdProducto = $strIdTipoPromo;
                    }
                }
                else
                {
                    $intIdPlan     = "";
                    $intIdProducto = "";
                }
                $arrayCodigosPromo                         = ( isset($objServicios['codigosPromo']) 
                                                              && !empty($objServicios['codigosPromo']) ) 
                                                              ? $objServicios['codigosPromo'] : "";
                $arrayRespuestaServicio ['strTipoPromo']   = $strTipoPromo; 
                $arrayRespuestaServicio ['strIdTipoPromo'] = $strIdTipoPromo;
                $arrayRespuestaServicio ['intIdServicio']  = $intIdServicio;
                $arrayRespuestaValidacion                  = array();
                $arrayRespuestaServicio['codigosPromo']    = array();
                $strDetalles                               = "";
                $intContadorMix                            = 0;
                $strAplicoMix                              = "";
                
                foreach($arrayCodigosPromo as $objCodigosPromo)
                {
                    if ($strServiciosMix !== "" && isset($strServiciosMix) && $strTipoPromo === "product" 
                        && $intContadorMix === 0 && $objCodigosPromo['strGrupoPromocion'] !== 'PROM_MENS')
                    {   
                        $arrayServiciosMix = explode( ',', $strServiciosMix);
                        for ($intContador = 0; $intContador < count($arrayServiciosMix); $intContador++)
                        {   
                            if ($intIdServicio === $arrayServiciosMix[$intContador])
                            {
                                $arrayDatosMix = array('strGrupoPromocion' => 'PROM_MENS',
                                                       'strTipoPromocion'  => 'PROM_MIX',
                                                       'strTipoProceso'    => $strTipoProceso,
                                                       'strCodEmpresa'     => $strCodEmpresa,
                                                       'strCodigo'         => $strCodigoMix,
                                                       'intIdServicio'     => $intIdServicio,
                                                       'strEsContrato'     => $strEsContrato,
                                                       'strFormaPago'      => $strFormaPago,
                                                       'intIdPlan'         => "",
                                                       'intIdProducto'     => "");

                                $arrayRespuestaMix  = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                           ->validaCodigoPromocion($arrayDatosMix);

                                $arrayRespuestaValidacion ['strGrupoPromocion']  = 'PROM_MENS';
                                $arrayRespuestaValidacion ['strTipoPromocion']   = 'PROM_MPRO'; 
                                $arrayRespuestaValidacion ['strCodigo']          = $strCodigoMix; 
                                $arrayRespuestaValidacion ['strNombrePromocion'] = $arrayRespuestaMix['strNombrePromocion']; 
                                $arrayRespuestaValidacion ['strAplica']          = $arrayRespuestaMix['strAplica'];
                                $arrayRespuestaValidacion ['strMensaje']         = $arrayRespuestaMix['strMensaje'];

                                if ($arrayRespuestaMix['strAplica'] === 'S')
                                {   
                                    $strAplicoMix = $intIdServicio;
                                    $strDetalles  = $strDetalles.' <enter> <enter> '.$arrayRespuestaMix['strDetalle'];
                                }

                                $arrayRespuestaValidacion ['strIdTipoPromocion'] = $arrayRespuestaMix['strIdTipoPromocion'];
                                $arrayRespuestaServicio['codigosPromo'][] = $arrayRespuestaValidacion;
                            }

                        }
                    }
                    $intContadorMix    = 1; 

                    if ($strEsMix === 'OK' && $objCodigosPromo['strGrupoPromocion'] === 'PROM_MENS')
                    {
                        $strTipoPromocion = 'PROM_MIX';
                        $strCodigo        = $strCodigoMix;
                    }
                    else
                    {
                        $strTipoPromocion = $objCodigosPromo['strTipoPromocion'];
                        $strCodigo        = $objCodigosPromo['strCodigo'];
                    }
                    $arrayDatosServicio = array('strGrupoPromocion' => $objCodigosPromo['strGrupoPromocion'],
                                                'strTipoPromocion'  => $strTipoPromocion,
                                                'strTipoProceso'    => $strTipoProceso,
                                                'strCodEmpresa'     => $strCodEmpresa,
                                                'strCodigo'         => $strCodigo,
                                                'intIdServicio'     => $intIdServicio,
                                                'strEsContrato'     => $strEsContrato,
                                                'strFormaPago'      => $strFormaPago,
                                                'intIdPlan'         => $intIdPlan,
                                                'intIdProducto'     => $intIdProducto);

                    $arrayRespuesta     = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                               ->validaCodigoPromocion($arrayDatosServicio);

                    $arrayRespuestaValidacion ['strGrupoPromocion']  = $objCodigosPromo['strGrupoPromocion'];
                    $arrayRespuestaValidacion ['strTipoPromocion']   = $objCodigosPromo['strTipoPromocion']; 
                    $arrayRespuestaValidacion ['strCodigo']          = $strCodigo; 
                    $arrayRespuestaValidacion ['strNombrePromocion'] = $arrayRespuesta['strNombrePromocion']; 
                    $arrayRespuestaValidacion ['strAplica']          = $arrayRespuesta['strAplica'];
                    $arrayRespuestaValidacion ['strMensaje']         = $arrayRespuesta['strMensaje'];

                    if ($arrayRespuesta['strAplica'] === 'S')
                    {
                        $strDetalles = $strDetalles.' <enter> <enter> '.$arrayRespuesta['strDetalle'];
                    }

                    $arrayRespuestaValidacion ['strIdTipoPromocion'] = $arrayRespuesta['strIdTipoPromocion'];

                    if ($arrayRespuesta['strAplica'] === 'S' && $objCodigosPromo['strTipoPromocion'] === 'PROM_MIX'
                        && $strTipoPromo === "package")
                    {
                        $strEsMix           = 'OK';
                        $strCodigoMix       = $objCodigosPromo['strCodigo'];
                        $strServiciosMix    = $arrayRespuesta['strServiciosMix'];
                        $strGrupoPromocion  = $objCodigosPromo['strGrupoPromocion']; 
                        $strNombrePromocion = $arrayRespuesta['strNombrePromocion']; 
                        $strAplica          = $arrayRespuesta['strAplica'];
                        $strMensaje         = $arrayRespuesta['strMensaje'];
                        $strDetalle         = $arrayRespuesta['strDetalle'];
                        $strIdTipoPromocion = $arrayRespuesta['strIdTipoPromocion'];
                        $strAplicoMix       = $intIdServicio;
                    }

                    $arrayRespuestaServicio['codigosPromo'][] = $arrayRespuestaValidacion;
                }
                $arrayRespuestaServicio['strDetalle']         = $strDetalles;
                $arrayRespuestaPromociones['strRespuesta'][]  = $arrayRespuestaServicio;
                $strServiciosMixAplicados                     = $strServiciosMixAplicados.$strAplicoMix.',';
            }
            
            $strServiciosMixAplicados  = substr($strServiciosMixAplicados, 0, -1);
            
            if ($strServiciosMix !== "" && isset($strServiciosMix))
            {
                $arrayServiciosMix          = explode( ',', $strServiciosMix);
                $arrayServiciosMixAplicados = explode( ',', $strServiciosMixAplicados);
                for ($intContador = 0; $intContador < count($arrayServiciosMix); $intContador++)
                {   
                    $strExiste = 'N';
                    if (in_array($arrayServiciosMix[$intContador], $arrayServiciosMixAplicados)) 
                    {
                        $strExiste = 'S';
                    }
                    if ($strExiste === 'N')
                    {
                        $objServicio    = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                               ->find($arrayServiciosMix[$intContador]);
                        if(is_object($objServicio))
                        {
                            $objProducto = $objServicio->getProductoId();
                            if(isset($objProducto))
                            {
                                $arrayRespuestaValidacion                        = array();
                                $arrayRespuestaServicio                          = array();
                                $arrayRespuestaServicio ['strTipoPromo']         = 'product';
                                $arrayRespuestaServicio ['strIdTipoPromo']       = $objProducto->getId();
                                $arrayRespuestaServicio ['intIdServicio']        = ($arrayServiciosMix[$intContador]);
                                $arrayRespuestaValidacion ['strGrupoPromocion']  = $strGrupoPromocion;
                                $arrayRespuestaValidacion ['strTipoPromocion']   = 'PROM_MPRO';
                                $arrayRespuestaValidacion ['strCodigo']          = $strCodigoMix; 
                                $arrayRespuestaValidacion ['strNombrePromocion'] = $strNombrePromocion; 
                                $arrayRespuestaValidacion ['strAplica']          = $strAplica;
                                $arrayRespuestaValidacion ['strMensaje']         = $strMensaje;
                                $arrayRespuestaValidacion ['strIdTipoPromocion'] = $strIdTipoPromocion;
                                $arrayRespuestaServicio['codigosPromo'][]        = $arrayRespuestaValidacion;
                                $arrayRespuestaServicio ['strDetalle']           = $strDetalle;
                                $arrayRespuestaPromociones['strRespuesta'][]     = $arrayRespuestaServicio;
                            }
                        }
                    }
                }
            }
        }
        catch (\Exception $objException)
        {
            $this->serviceUtil->insertError('Telcos+',
                                            'PromocionService->validaCodigoPromocionMasiva',
                                            $objException->getMessage(),
                                            ( isset($arrayParametros['usrCreacion']) 
                                             && !empty($arrayParametros['usrCreacion']) ) ? $arrayParametros['usrCreacion'] : "",
                                            '127.0.0.0');
            
            $arrayResponse =  array('response' => 'Error al consumir el proceso de validación de códigos promocionales.',
                                    'status'   => 500,
                                    'message'  => 'ERROR',
                                    'success'  => false);
            return $arrayResponse;
        }
        $arrayResponse =  array('response' => $arrayRespuestaPromociones,
                                'status'   => 200,
                                'message'  => 'CONSULTA EXITOSA',
                                'success'  => true);
        return $arrayResponse;
    }
    
    /**
     * 
     * Método encargado de guardar los códigos promocionales para móvil. 
     *
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.0 04-01-2020
     *
     * @param  Array $arrayData
     * @return Array $arrayRespuesta
     */
    public function guardarCodigoPromocional($arrayData)
    {
        $strUsuario             = $arrayData['strUsuario'] ? $arrayData['strUsuario'] : 'telcos_prom';
        $strIp                  = $arrayData['strIp'] ? $arrayData['strIp'] : '127.0.0.1';
        
        $this->emcom->beginTransaction();
        try
        {
          
            foreach ($arrayData as $strNombreValor => $strValor) 
            {
     
                    $objServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')->findOneById( $strValor['intIdServicio'] );
            
                    if(!is_object($objServicio))
                    {
                        throw new \Exception("No se ha encontrado el servicio.");
                    }

                    if ($strValor['strTipoProceso']=='EXISTENTE')
                    {
                      $strDescripcionCaracteristica='PROM_COD_EXISTENTE';
                    }
                    if ($strValor['strTipoProceso']=='NUEVO')
                    {
                       $strDescripcionCaracteristica='PROM_COD_NUEVO';
                    }
                    if ($strValor['strTipoPromocion']=='PROM_INS')
                    {
                       $strDescripcionCaracteristica='PROM_COD_INST';
                    }
                    if ($strValor['strTipoPromocion']=='PROM_BW')
                    {
                       $strDescripcionCaracteristica='PROM_COD_BW';
                    }
 
                if($strValor['strCodigo']!='')
                {

                        $objCaracteristicaMens = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                             ->findOneBy(array('descripcionCaracteristica' => $strDescripcionCaracteristica,
                                                                               'tipo'                      => 'COMERCIAL'));

                        if(!is_object($objCaracteristicaMens))
                        {
                            throw new \Exception("No se ha definido la característica");
                        }

                        $objCaracteristicaMensExist =  $this->emcom->getRepository('schemaBundle:InfoServicioCaracteristica')
                                                                   ->findOneBy(array("servicioId"       => $objServicio,
                                                                                     "caracteristicaId" => $objCaracteristicaMens,
                                                                                     "estado"           => 'Activo'));

                        if(!is_object($objCaracteristicaMensExist))
                        {
                            $arrayParametros                        = array();
                            $arrayParametros['intIdServicio']       = $strValor['intIdServicio'];
                            $arrayParametros['strIpCreacion']       = $strIp;
                            $arrayParametros['strUsrCreacion']      = $strUsuario;
                            $arrayParametros['strCodigo']           = $strValor['strCodigo'];
                            $arrayParametros['strPromocion']        = $strValor['strNombnrePromocion'];
                            $arrayParametros['strEstado']           = 'Activo';
                            $arrayParametros['strObservacion']      = $strValor['strObservacion'];
                            $arrayParametros['objCaracteristica']   = $objCaracteristicaMens;
                            $arrayParametros['strIdTipoPromocion']  = $strValor['strIdTipoPromocion'];
                            $arrayResponseMens                      = $this->guardarCodigoServicioCarac($arrayParametros);
                            if($arrayResponseMens!='OK')
                            {
                                 throw new \Exception($arrayResponseMens);
                            }

                        }

                }
            
                
            }
              $this->emcom->getConnection()->commit();
              $arrayRespuesta['strStatus']  = 'OK';
              $arrayRespuesta['strMensaje'] = null;

        }
        catch (\Exception $objException)
        {
            $this->emcom->rollback();
            $this->emcom->close();
            $strMessageControlado = 'Ocurrió un error al guardar los Códigos Promocionales.';

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }

            $this->serviceUtil->insertError('Telcos+',
                                         'ComercialMobileWSControllerRest->putGuardaCodigoPromocional',
                                          $objException->getMessage(),
                                          $strUsuario,
                                          $strIp);

             $arrayRespuesta = array ('strStatus' => 'ERROR', 'strMensaje' => $strMessageControlado);
        }
        return $arrayRespuesta;
    }

    /**
     * Metodo que verifica si promocion puede realizar accion masiva.
     * 
     * @author: Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 21-04-2022
     * 
     * @param array [intIdPromocion => El codigo de la promocion a validar]
     * @return  - Página principal de promociones.
     *
     */
    public function validaPromocionesMasivas($arrayParametros)
    {
        $arrayIdPromociones = $arrayParametros['arrayIdsGrupoPromocion'];
        $intIdEmpresa       = $arrayParametros['strCodEmpresa'];
        $intPromociones     = 0;

        // Buscamos parametro para validar promociones y los tipos permitidos
        $arrayPromosNoMasivas = null;
        $arrayListaParametros = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->get('PROM_TIPO_PROMOCIONES', 'COMERCIAL','ADMI_TIPO_PROMOCION',
                                        'Promociones que no se ejecutaran en masivo',
                                        '','','','','',
                                        $intIdEmpresa);
        if(is_array($arrayListaParametros) && !empty($arrayListaParametros))
        {
            $arrayPromosNoMasivas = $this->serviceUtil->obtenerValoresParametro($arrayListaParametros);
        }
        // Validamos que todas las promociones puedan ejecutarse masiva
        foreach($arrayIdPromociones as $intIdPromocion)
        {
            $objAdmiTipoPromocion = $this->emcom->getRepository('schemaBundle:AdmiTipoPromocion')
                                        ->findOneBy(array("grupoPromocionId" => $intIdPromocion));
                                        
            if (!empty($objAdmiTipoPromocion) &&
                in_array($objAdmiTipoPromocion->getCodigoTipoPromocion(), $arrayPromosNoMasivas))
            {
                $intPromociones++;
            }
        }
        return $intPromociones;
    }

}
