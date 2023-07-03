<?php
namespace telconet\comercialBundle\Service;
use telconet\schemaBundle\Entity\AdmiGrupoPromocion;
use telconet\schemaBundle\Entity\AdmiGrupoPromocionRegla;
use telconet\schemaBundle\Entity\AdmiGrupoPromocionHisto;
use telconet\schemaBundle\Entity\AdmiTipoPromocion;
use telconet\schemaBundle\Entity\AdmiTipoPromocionRegla;
use telconet\schemaBundle\Entity\AdmiTipoPlanProdPromocion;
use Symfony\Component\HttpFoundation\Response;
class PromocionInstalacionService 
{ 
    private $emcom;
    private $emInfraestructura;
    private $serviceUtil;
    private $emGeneral;
    private $servicePromocion;
    private $servicePromocionAnchoBanda;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emcom                        = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emInfraestructura            = $container->get('doctrine.orm.telconet_infraestructura_entity_manager');
        $this->emGeneral                    = $container->get('doctrine.orm.telconet_general_entity_manager');
        $this->serviceUtil                  = $container->get('schema.Util');
        $this->servicePromocion             = $container->get('comercial.Promocion');
        $this->servicePromocionAnchoBanda   = $container->get('comercial.PromocionAnchoBanda');
    }
     /**
     * guardarPromoInstalacion, Guarda promoción de instalación.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 01-04-2019     
     * @param array $arrayParametros[]                          
     *              'strNombrePromocion'      => Nombre de la Promoción
     *              'strInicioVigencia'       => Fecha de Inicio de Vigencia de la Promoción
     *              'strFinVigencia'          => Fecha de Fin de Vigencia de la Promoción,
     *              'strIdsTiposNegocio'      => Ids de Tipos de Negocios separados por caracter(,)     
     *              'strIdsFormasPago'        => Ids de las Formas de Pago separadas por caracter(,)
     *              'strIdsUltimaMilla'       => Ids de las últimas millas separadas por caracter(,),
     *              'strIdsEstadoServicio'    => Estados del Servicio separado por caracter(,)
     *              'arrayEmisores'           => Array con Ids de Emisores (ADMI_BANCO_TIPO_CUENTA)
     *              'strPeriodos'             => Contiene el string de los períodos concatenados con su respectivo descuento
     *                                           Ejemplo  Período|%Descuento : 1|20,2|20,3|20
     *              'arraySectorizacion'     => []
     *                                        'intJurisdiccion'         => Jurisdicción,
     *                                        'intCanton'               => Cantón,
     *                                        'intParroquia'            => Parroquia,
     *                                        'strOptSectorOlt'         => Opción de Sector / OLT,
     *                                        'intSectorOlt'            => Sector / OLT,          
     *              'strUsrCreacion'          => Usuario en sesión
     *              'strCodEmpresa'           => Código de empresa en sesión
     *              'strIpCreacion'           => Ip de creación    
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.1 12-10-2020 - Se envía PROM_CODIGO para guardar la característica.     
     * @return $strRespuesta
     */
    public function guardarPromoInstalacion($arrayParametros)
    {
        $strNombrePromocion     = $arrayParametros['strNombrePromocion']; 
        $strInicioVigencia      = $arrayParametros['strInicioVigencia'];
        $strFinVigencia         = $arrayParametros['strFinVigencia'];
        $strIdsTiposNegocio     = $arrayParametros['strIdsTiposNegocio'];        
        $strIdsFormasPago       = $arrayParametros['strIdsFormasPago'];
        $strIdsUltimaMilla      = $arrayParametros['strIdsUltimaMilla'];
        $strIdsEstadoServicio   = $arrayParametros['strIdsEstadoServicio'];
        $arrayEmisores          = $arrayParametros['arrayEmisores'];
        $arraySectorizacion     = $arrayParametros['arraySectorizacion'];
        $strPeriodos            = $arrayParametros['strPeriodos'];        
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $strCodEmpresa          = $arrayParametros['strCodEmpresa'];
        $strIpCreacion          = $arrayParametros['strIpCreacion'];
        $strTipoEdicion         = $arrayParametros['strTipoEdicion'];
        $intIdPromocionOrigen   = $arrayParametros['intIdPromocionOrigen'];
        $strCodigoPromocion     = $arrayParametros['strCodigoPromocion'];
        $strFeCreacion          = new \DateTime('now');
        $this->emcom->beginTransaction();
        try
        {
            $arrayParametroDet   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                   ->getOne(
                                                            'PROM_TIPO_PROMOCIONES', 
                                                            'COMERCIAL', 
                                                            'ADMI_TIPO_PROMOCION',
                                                            '', 
                                                            'Descuento y Diferido de Instalación', 
                                                            '', 
                                                            '', 
                                                            '', 
                                                            '', 
                                                            $strCodEmpresa);           
            $strTipoPromocion    = ( isset($arrayParametroDet["valor1"]) && !empty($arrayParametroDet["valor1"]) )
                                        ? $arrayParametroDet["valor1"] : "";            
            $strCodTipoPromocion = ( isset($arrayParametroDet["valor2"]) && !empty($arrayParametroDet["valor2"]) )
                                        ? $arrayParametroDet["valor2"] : "";
            if($strTipoPromocion == '')
            {
                throw new \Exception("No se pudo crear Promoción, No se encontró el Tipo de Promoción parametizada."); 
            }
            if($strCodTipoPromocion == '')
            {
                throw new \Exception("No se pudo crear Promoción, No se encontró el Código Tipo de Promoción parametizada."); 
            }
            
            $objInfoEmpresaGrupo = $this->emcom->getRepository('schemaBundle:InfoEmpresaGrupo')->find($strCodEmpresa);
            if(!is_object($objInfoEmpresaGrupo))
            {
                throw new \Exception("No se pudo crear Promoción, No se encontró empresa en sesión"); 
            }
            $objAdmiGrupoPromocion = new AdmiGrupoPromocion();            
            $objAdmiGrupoPromocion->setNombreGrupo(substr($strNombrePromocion, 0, 4000));                       
            $objAdmiGrupoPromocion->setFeInicioVigencia(date_create($strInicioVigencia));                       
            $objAdmiGrupoPromocion->setFeFinVigencia(date_create($strFinVigencia));                   
            $objAdmiGrupoPromocion->setFeCreacion($strFeCreacion);
            $objAdmiGrupoPromocion->setUsrCreacion($strUsrCreacion);
            $objAdmiGrupoPromocion->setIpCreacion($strIpCreacion);
            $objAdmiGrupoPromocion->setEmpresaCod($objInfoEmpresaGrupo);
            $objAdmiGrupoPromocion->setEstado('Activo');
            if($strTipoEdicion=='ED')
            {
                $objAdmiGrupoPromocion->setGrupoPromocionId($intIdPromocionOrigen);
         
            }
            $this->emcom->persist($objAdmiGrupoPromocion);
            $this->emcom->flush();     
            
    
            $objAdmiTipoPromocion = new AdmiTipoPromocion();
            $objAdmiTipoPromocion->setGrupoPromocionId($objAdmiGrupoPromocion);   
            $objAdmiTipoPromocion->setCodigoTipoPromocion($strCodTipoPromocion);
            $objAdmiTipoPromocion->setTipo($strTipoPromocion);
            $objAdmiTipoPromocion->setFeCreacion($strFeCreacion);
            $objAdmiTipoPromocion->setUsrCreacion($strUsrCreacion);
            $objAdmiTipoPromocion->setIpCreacion($strIpCreacion);
            $objAdmiTipoPromocion->setEstado('Activo');
            $this->emcom->persist($objAdmiTipoPromocion);
            $this->emcom->flush();
            
            if($strTipoEdicion=='ED')
            {
                 $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                  'strRegla'            => 'ORIGEN_PROMOCION_EDITADA',
                                                  'strValorRegla'       => $intIdPromocionOrigen,
                                                  'strFeCreacion'       => $strFeCreacion,
                                                  'strUsrCreacion'      => $strUsrCreacion,
                                                  'strIpCreacion'       => $strIpCreacion
                                                 );
                  $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas); 
            }
            
            if(isset($strCodigoPromocion) && !empty($strCodigoPromocion))
            {
                $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                  'strRegla'            => 'PROM_CODIGO',
                                                  'strValorRegla'       => $strCodigoPromocion,
                                                  'strFeCreacion'       => $strFeCreacion,
                                                  'strUsrCreacion'      => $strUsrCreacion,
                                                  'strIpCreacion'       => $strIpCreacion
                                                 );
                $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
            }
            
            $arrayParamPromoGrupoHisto = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                               'strFeCreacion'       => $strFeCreacion,
                                               'strUsrCreacion'      => $strUsrCreacion,
                                               'strIpCreacion'       => $strIpCreacion,
                                               'strObservacion'      => 'Se Creó nueva Promoción '.$strTipoPromocion,
                                               'strEstado'           => 'Activo'
                                              );
            $this->servicePromocion->ingresarPromocionGrupoHisto($arrayParamPromoGrupoHisto);
            
            if(isset($arraySectorizacion) && !empty($arraySectorizacion))
            {
                foreach($arraySectorizacion as $objSectorizacion)
                {                    
                    $arraySecuencia     = $this->emcom->getRepository("schemaBundle:AdmiGrupoPromocion")->creaSecuencia();
                    $intNumeroSecuencia = $arraySecuencia['secuencia'];
                    
                    if(isset($objSectorizacion['intJurisdiccion']) && !empty($objSectorizacion['intJurisdiccion'])
                        && $objSectorizacion['intJurisdiccion'] !== "0")
                    {                       
                        $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                  'strRegla'            => 'PROM_JURISDICCION',
                                                  'strValorRegla'       => $objSectorizacion['intJurisdiccion'],
                                                  'strFeCreacion'       => $strFeCreacion,
                                                  'strUsrCreacion'      => $strUsrCreacion,
                                                  'strIpCreacion'       => $strIpCreacion,
                                                  'intNumeroSecuencia'  => $intNumeroSecuencia
                                                 );
                        $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas); 
                    }
                    if(isset($objSectorizacion['intCanton']) && !empty($objSectorizacion['intCanton']) && $objSectorizacion['intCanton'] !== "0")
                    {
                        $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                  'strRegla'            => 'PROM_CANTON',
                                                  'strValorRegla'       => $objSectorizacion['intCanton'],
                                                  'strFeCreacion'       => $strFeCreacion,
                                                  'strUsrCreacion'      => $strUsrCreacion,
                                                  'strIpCreacion'       => $strIpCreacion,
                                                  'intNumeroSecuencia'  => $intNumeroSecuencia
                                                 );
                        $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
                    }
                    if(isset($objSectorizacion['intParroquia']) && !empty($objSectorizacion['intParroquia']) 
                        && $objSectorizacion['intParroquia'] !== "0")
                    {
                        $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                  'strRegla'            => 'PROM_PARROQUIA',
                                                  'strValorRegla'       => $objSectorizacion['intParroquia'],
                                                  'strFeCreacion'       => $strFeCreacion,
                                                  'strUsrCreacion'      => $strUsrCreacion,
                                                  'strIpCreacion'       => $strIpCreacion,
                                                  'intNumeroSecuencia'  => $intNumeroSecuencia
                                                 );
                        $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
                    }                  
                    if(isset($objSectorizacion['strOptSectOltEdif']) && !empty($objSectorizacion['strOptSectOltEdif']) 
                        && $objSectorizacion['strOptSectOltEdif'] !== "TODOS")    
                    {
                        $strReglaPromocion = "";
                        if($objSectorizacion['strOptSectOltEdif'] === 'sector')
                        {
                            $strReglaPromocion = 'PROM_SECTOR';
                        }
                        elseif($objSectorizacion['strOptSectOltEdif'] === 'olt')
                        {
                            $strReglaPromocion = 'PROM_ELEMENTO';
                        }
                        else
                        {
                            $strReglaPromocion = 'PROM_EDIFICIO';
                        }
                        $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                  'strRegla'            => $strReglaPromocion,
                                                  'strValorRegla'       => $objSectorizacion['intSectOltEdif'],
                                                  'strFeCreacion'       => $strFeCreacion,
                                                  'strUsrCreacion'      => $strUsrCreacion,
                                                  'strIpCreacion'       => $strIpCreacion,
                                                  'intNumeroSecuencia'  => $intNumeroSecuencia
                                                 );
                        $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
                    }
                }
            }
            if(isset($strIdsTiposNegocio) && !empty($strIdsTiposNegocio))
            {
                $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                          'strRegla'           => 'PROM_TIPO_NEGOCIO',
                                          'strValorRegla'      => $strIdsTiposNegocio,
                                          'strFeCreacion'      => $strFeCreacion,
                                          'strUsrCreacion'     => $strUsrCreacion,
                                          'strIpCreacion'      => $strIpCreacion,
                                         );
                $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
            }
            if(isset($strIdsFormasPago) && !empty($strIdsFormasPago))
            {
                $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                          'strRegla'           => 'PROM_FORMA_PAGO',
                                          'strValorRegla'      => $strIdsFormasPago,
                                          'strFeCreacion'      => $strFeCreacion,
                                          'strUsrCreacion'     => $strUsrCreacion,
                                          'strIpCreacion'      => $strIpCreacion,
                                         );
                $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
            }
            if(isset($strIdsUltimaMilla) && !empty($strIdsUltimaMilla))
            {
                $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                          'strRegla'           => 'PROM_ULTIMA_MILLA',
                                          'strValorRegla'      => $strIdsUltimaMilla,
                                          'strFeCreacion'      => $strFeCreacion,
                                          'strUsrCreacion'     => $strUsrCreacion,
                                          'strIpCreacion'      => $strIpCreacion,
                                         );
                $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
            }
            if(isset($strIdsEstadoServicio) && !empty($strIdsEstadoServicio))
            {
                $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                          'strRegla'           => 'PROM_ESTADO_SERVICIO',
                                          'strValorRegla'      => $strIdsEstadoServicio,
                                          'strFeCreacion'      => $strFeCreacion,
                                          'strUsrCreacion'     => $strUsrCreacion,
                                          'strIpCreacion'      => $strIpCreacion,
                                         );
                $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
            }                       
            if(isset($arrayEmisores) && !empty($arrayEmisores))
            {
                $arrayParametros           = array();
                $arrayParamBancoTipoCuenta = array();
                $arrayBancoTipoCuenta      = array();
                $arrayIdBancoTipoCuenta    = [];
                for($i = 0; $i < count($arrayEmisores); $i++)
                {
                    $arrayDescomponerEmisor         = ($arrayEmisores[$i]);
                    $arrayDescomponerEmisor         = explode("|", $arrayDescomponerEmisor);
                    $strIdCuenta                    = $arrayDescomponerEmisor[0];
                    $strIdBanco                     = $arrayDescomponerEmisor[1];
                    $arrayParametros['strIdCuenta'] = $strIdCuenta;

                    if($strIdBanco === '0')
                    {
                        $arrayParametros['strIdBanco']   = '';
                        $arrayParametros['strEsTarjeta'] = 'S';
                    }
                    else
                    {
                        $arrayParametros['strIdBanco']   = $strIdBanco;
                        $arrayParametros['strEsTarjeta'] = 'N';
                    }
                    $arrayParamBancoTipoCuenta=$this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getBancoTipoCuenta($arrayParametros);
                    $arrayBancoTipoCuenta     = array_merge($arrayBancoTipoCuenta, $arrayParamBancoTipoCuenta);
                }
                foreach($arrayBancoTipoCuenta as $objBancoTipoCuenta)
                {
                    $arrayIdBancoTipoCuenta[] = $objBancoTipoCuenta['idBancoTipoCuenta'];
                }
                $strIdBancoTipoCuenta = implode(",", $arrayIdBancoTipoCuenta);
                $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                          'strRegla'            => 'PROM_EMISOR',
                                          'strValorRegla'       => $strIdBancoTipoCuenta,
                                          'strFeCreacion'       => $strFeCreacion,
                                          'strUsrCreacion'      => $strUsrCreacion,
                                          'strIpCreacion'       => $strIpCreacion,
                                         );
                $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
            }            
            if(isset($strPeriodos) && !empty($strPeriodos))
            {
                $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                          'strRegla'           => 'PROM_PERIODO',
                                          'strValorRegla'      => $strPeriodos,
                                          'strFeCreacion'      => $strFeCreacion,
                                          'strUsrCreacion'     => $strUsrCreacion,
                                          'strIpCreacion'      => $strIpCreacion,
                                         );
                $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
            }            
            $this->emcom->getConnection()->commit();  
            $strRespuesta = 'OK';
        }
        catch(\Exception $e)
        {
            $this->emcom->rollback();
            $this->emcom->close();           
            $strRespuesta = "No se pudo guardar Promoción de Instalación <br>". $e->getMessage() . ". Favor notificar a Sistemas.";            
            $this->serviceUtil->insertError('Telcos+',
                                            'PromocionInstalacionService.guardarPromoInstalacion',
                                             'Error PromocionInstalacionService.guardarPromoInstalacion:'.$e->getMessage(),
                                             $strUsrCreacion,
                                             $strIpCreacion);
            return $strRespuesta;
        }
        return $strRespuesta;
    }

    /**
     * getEditarPromocionInstalacion()
     * Función que obtiene la información de la Promoción de Instalación
     * para ser presentada en la página de Editar Promoción de Instalación.
     * 
     * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
     * @version 1.0 11-04-2019   
     *        
     * @param array $arrayParametros[]                  
     *              'intIdPromocion'  => Id de Promoción   
     *      
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.1 12-10-2020 - Se obtiene la característica PROM_CODIGO.
     * se guarda Id de la promoción Oirgen.
     *          
     * @return $arrayRespuesta              
     */   
    public function getEditarPromocionInstalacion($arrayParametros)
    {
        $intIdPromocion                        = $arrayParametros['intIdPromocion'];
        $arrayParametros['arraySectorizacion'] = array('PROM_JURISDICCION', 'PROM_CANTON', 'PROM_PARROQUIA', 'PROM_SECTOR', 'PROM_ELEMENTO',
                                                       'PROM_EDIFICIO');
        $objPromocion                          = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                             ->getPromocionInstalacion($arrayParametros);
        $arraySectorizacion                    = array();
        $strFechaEditar                        =  date('Y-m-d', strtotime('+1 days'));
        $strTipoEdicion                        = $arrayParametros['strTipoEdicion'];

        if(!empty($objPromocion['objSectorizacion']))
        {
            foreach($objPromocion['objSectorizacion'] as $sectorización)
            {
                $intSectorizacion = (int) $sectorización['idSectorizacion'];
                $intJurisdiccion  = (int) $sectorización['idJurisdiccion'];
                $strJurisdiccion  = !empty($sectorización['descJurisdiccion']) ? $sectorización['descJurisdiccion'] : "TODOS";
                $intCanton        = (int) $sectorización['idCanton'];
                $strCanton        = !empty($sectorización['descCanton']) ? $sectorización['descCanton'] : "TODOS";
                $intParroquia     = (int) $sectorización['idParroquia'];
                $strParroquia     = !empty($sectorización['descParroquia']) ? $sectorización['descParroquia'] : "TODOS";
                $strSectOltEdif   = "";

                if($sectorización['idSector'] === "0" && $sectorización['idElemento'] === "0" && $sectorización['idEdificio'] === "0")                
                {
                    $strOptSectOltEdif  = "TODOS";
                    $arraySectOltEdif   = 0;
                }
                else
                {
                    if(!empty($sectorización['idSector']) && $sectorización['idSector'] !== "0")
                    {
                        $strOptSectOltEdif = "sector";
                        $arraySector       = explode(",", $sectorización['idSector']);
                        foreach($arraySector as $idSector)
                        {
                            $objSector       = $this->emInfraestructura->getRepository('schemaBundle:AdmiSector')->find((int) $idSector);
                            $strSectOltEdif .= $objSector->getNombreSector() . ', ';
                        }
                        $arraySectOltEdif = $sectorización['idSector'];
                    }

                    if(!empty($sectorización['idElemento']) && $sectorización['idElemento'] !== "0")
                    {
                        $strOptSectOltEdif = "olt";
                        $arrayElemento     = explode(",", $sectorización['idElemento']);
                        
                        foreach($arrayElemento as $idElemento)
                        {
                            $arrayParametrosOlt['intIdElemento']   = (int) $idElemento;
                            $arrayParametrosOlt['intIdEmpresa']    = $arrayParametros['intIdEmpresa'];
                            $arrayParametrosOlt['strTipoElemento'] = 'OLT';
                            
                            $objElemento     = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                           ->getOltEdificioById($arrayParametrosOlt);
                            $strSectOltEdif .= $objElemento[0]['nombre'] . ', ';
                        }
                        $arraySectOltEdif = $sectorización['idElemento'];
                    }
                    
                    if(!empty($sectorización['idEdificio']) && $sectorización['idEdificio'] !== "0")
                    {
                        $strOptSectOltEdif = "edificio";
                        $arrayEdificio     = explode(",", $sectorización['idEdificio']);

                        foreach($arrayEdificio as $idEdificio)
                        {
                            $arrayParametrosEdificio['intIdElemento']   = (int) $idEdificio;
                            $arrayParametrosEdificio['intIdEmpresa']    = $arrayParametros['intIdEmpresa'];
                            $arrayParametrosEdificio['strTipoElemento'] = 'EDIFICACION';

                            $objEdificio     = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                           ->getOltEdificioById($arrayParametrosEdificio);
                            $strSectOltEdif .= $objEdificio[0]['nombre'] . ', ';
                        }
                        $arraySectOltEdif = $sectorización['idEdificio'];
                    }
                }
                $arrayCaractSectorizacion = array("intSectorizacion"     => $intSectorizacion,
                                                  "intJurisdiccion"      => $intJurisdiccion,
                                                  "strJurisdiccion"      => $strJurisdiccion,
                                                  "intCanton"            => $intCanton,
                                                  "strCanton"            => $strCanton,
                                                  "intParroquia"         => $intParroquia,
                                                  "strParroquia"         => $strParroquia,
                                                  "strOptSectOltEdif"    => $strOptSectOltEdif,
                                                  "arraySectOltEdif"     => $arraySectOltEdif,
                                                  "strSectOltEdif"       => trim($strSectOltEdif, ', ')
                                                 );
                array_push($arraySectorizacion, $arrayCaractSectorizacion);
            }
        }    
        
        $arrayCaracteristicas['arraySectorizacion'] = $arraySectorizacion;
        
        foreach($objPromocion['objTipoPromocion'] as $arrayTipoPromocion)
        {
            $arrayCaracteristicas[$arrayTipoPromocion['descCaracteristica']] = $arrayTipoPromocion['valorCaracteristica'];             
        }        
        $objAdmiGrupoPromocion       = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->find($intIdPromocion);
        if(!is_object($objAdmiGrupoPromocion))
        {
             throw new \Exception("No se pudo obtener el detalle de la Promoción. Consulte con el Administrador del Sistema");
        }
        $objAdmiGrupoPromocionOrigenClonado = null;
        if(is_object($objAdmiGrupoPromocion) && $objAdmiGrupoPromocion->getGrupoPromocionId()!=null)
        {
            $objAdmiGrupoPromocionOrigenClonado = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                              ->find($objAdmiGrupoPromocion->getGrupoPromocionId());                
        }
        
        $arrayRespuesta = array('objAdmiGrupoPromocion'              => $objAdmiGrupoPromocion,
                                'arrayCaracteristicas'               => $arrayCaracteristicas,
                                'objAdmiGrupoPromocionOrigenClonado' => $objAdmiGrupoPromocionOrigenClonado,
                                'dateFechaActual'                    => $strFechaEditar,
                                'strTipoEdicion'                     => $strTipoEdicion
        );
        return $arrayRespuesta;        
    }
    
    /**
     * editarPromoInstalacion, Edita promoción de instalación
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 15-04-2019     
     * @param array $arrayParametros[]                  
     *              'intIdPromocion           => Id del Grupo Promoción  ADMI_GRUPO_PROMOCION                  
     *              'strNombrePromocion'      => Nombre de la Promoción
     *              'strInicioVigencia'       => Fecha de Inicio de Vigencia de la Promoción
     *              'strFinVigencia'          => Fecha de Fin de Vigencia de la Promoción,
     *              'strIdsTiposNegocio'      => Ids de Tipos de Negocios separados por caracter(,)     
     *              'strIdsFormasPago'        => Ids de las Formas de Pago separadas por caracter(,)
     *              'strIdsUltimaMilla'       => Ids de las últimas millas separadas por caracter(,),
     *              'strIdsEstadoServicio'    => Estados del Servicio separado por caracter(,)
     *              'arrayEmisores'           => Array con Ids de Emisores (ADMI_BANCO_TIPO_CUENTA)
     *              'strPeriodos'             => Contiene el string de los períodos concatenados con su respectivo descuento
     *                                           Ejemplo  Período|%Descuento : 1|20,2|20,3|20
     *              'arraySectorizacion'      => []
     *                                        'intJurisdiccion'         => Jurisdicción,
     *                                        'intCanton'               => Cantón,
     *                                        'intParroquia'            => Parroquia,
     *                                        'strOptSectorOlt'         => Opción de Sector / OLT,
     *                                        'intSectorOlt'            => Sector / OLT,  
     *                                        'intSectorizacion'        => Secuencia que agrupa la sectorización    
     *              'strUsrUltMod'            => Usuario en sesión
     *              'strCodEmpresa'           => Código de empresa en sesión
     *              'strIpUltMod'             => Ip de creación            
     * @return $strRespuesta
     */
    public function editarPromoInstalacion($arrayParametros)
    {        
        $intIdPromocion         = $arrayParametros['intIdPromocion'];        
        $strNombrePromocion     = $arrayParametros['strNombrePromocion']; 
        $strInicioVigencia      = $arrayParametros['strInicioVigencia'];
        $strFinVigencia         = $arrayParametros['strFinVigencia'];
        $strIdsTiposNegocio     = $arrayParametros['strIdsTiposNegocio'];        
        $strIdsFormasPago       = $arrayParametros['strIdsFormasPago'];
        $strIdsUltimaMilla      = $arrayParametros['strIdsUltimaMilla'];
        $strIdsEstadoServicio   = $arrayParametros['strIdsEstadoServicio'];
        $arrayEmisores          = $arrayParametros['arrayEmisores'];
        $arraySectorizacion     = $arrayParametros['arraySectorizacion'];
        $strPeriodos            = $arrayParametros['strPeriodos'];        
        $strUsrUltMod           = $arrayParametros['strUsrUltMod'];
        $strCodEmpresa          = $arrayParametros['strCodEmpresa'];
        $strIpUltMod            = $arrayParametros['strIpUltMod'];
        $strCodigoPromocion     = $arrayParametros['strCodigoPromocion'];
        $strCodigoPromocionIng = $arrayParametros['strCodigoPromocionIng'];
        $strFeUltMod            = new \DateTime('now');
        $this->emcom->beginTransaction();
        try
        {
            $arrayParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->getOne(
                                                          'PROM_TIPO_PROMOCIONES', 
                                                          'COMERCIAL', 
                                                          'ADMI_TIPO_PROMOCION',
                                                          '', 
                                                          'Descuento y Diferido de Instalación', 
                                                          '', 
                                                          '', 
                                                          '', 
                                                          '', 
                                                          $strCodEmpresa);           
            $strTipoPromocion    = ( isset($arrayParametroDet["valor1"]) && !empty($arrayParametroDet["valor1"]) )
                                        ? $arrayParametroDet["valor1"] : "";            
            $strCodTipoPromocion = ( isset($arrayParametroDet["valor2"]) && !empty($arrayParametroDet["valor2"]) )
                                        ? $arrayParametroDet["valor2"] : "";
            if($strTipoPromocion == '')
            {
                throw new \Exception("No se pudo editar Promoción, No se encontró el Tipo de Promoción parametizada."); 
            }
            if($strCodTipoPromocion == '')
            {
                throw new \Exception("No se pudo editar Promoción, No se encontró el Código Tipo de Promoción parametizada."); 
            }
            
            $objAdmiGrupoPromocion = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->find($intIdPromocion);
            if(!is_object($objAdmiGrupoPromocion))
            {
                throw new \Exception("No se pudo editar la Promoción, No se encontró la Promoción a actualizar"); 
            }                                   
            $objAdmiGrupoPromocion->setNombreGrupo(substr($strNombrePromocion, 0, 4000));                       
            $objAdmiGrupoPromocion->setFeInicioVigencia(new \DateTime($strInicioVigencia));
            $objAdmiGrupoPromocion->setFeFinVigencia(new \DateTime($strFinVigencia));
            $objAdmiGrupoPromocion->setFeUltMod($strFeUltMod);
            $objAdmiGrupoPromocion->setUsrUltMod($strUsrUltMod);
            $objAdmiGrupoPromocion->setIpUltMod($strIpUltMod);
            if($objAdmiGrupoPromocion->getEstado()=='Pendiente')
            {
                $objAdmiGrupoPromocion->setEstado('Activo');
            }
            $this->emcom->persist($objAdmiGrupoPromocion);
            $this->emcom->flush();                                    
            
            $objAdmiTipoPromocion = $this->emcom->getRepository("schemaBundle:AdmiTipoPromocion")
                                                ->findOneBy(array('grupoPromocionId' => $objAdmiGrupoPromocion->getId()));
            if(!is_object($objAdmiTipoPromocion))
            {
                throw new \Exception("No se pudo editar la Promoción, No se encontró el Tipo Promoción a actualizar"); 
            }                        
            $objAdmiTipoPromocion->setFeUltMod($strFeUltMod);
            $objAdmiTipoPromocion->setUsrUltMod($strUsrUltMod);
            $objAdmiTipoPromocion->setIpUltMod($strIpUltMod); 
            if($objAdmiTipoPromocion->getEstado()=='Pendiente')
            {
                $objAdmiTipoPromocion->setEstado('Activo');
            }
            $this->emcom->persist($objAdmiTipoPromocion);
            $this->emcom->flush();
            
            $arrayParamPromoGrupoHisto = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                               'strFeCreacion'       => $strFeUltMod,
                                               'strUsrCreacion'      => $strUsrUltMod,
                                               'strIpCreacion'       => $strIpUltMod,
                                               'strObservacion'      => 'Se Editó la Promoción '.$strTipoPromocion,
                                               'strEstado'           => $objAdmiGrupoPromocion->getEstado()
            );
            $this->servicePromocion->ingresarPromocionGrupoHisto($arrayParamPromoGrupoHisto);
            
            //Sectorización
            $arrayParamSectorizacion['arraySectorizacion'] = array('PROM_JURISDICCION','PROM_CANTON','PROM_PARROQUIA','PROM_SECTOR','PROM_ELEMENTO',
                                                                   'PROM_EDIFICIO');
            $arrayParamSectorizacion['intIdTipoPromocion'] = $objAdmiTipoPromocion->getId();
            
            $arrayReglasSectorizacionByTipo = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                          ->getTipoPromocionReglaSectorizacion($arrayParamSectorizacion);
            
            foreach($arrayReglasSectorizacionByTipo as $objReglasSectorizacionByTipo)
            {
                $objReglasSectorizacionByTipo->setEstado('Eliminado');
                $this->emcom->persist($objReglasSectorizacionByTipo);
            }
            
            if(isset($arraySectorizacion) && !empty($arraySectorizacion))
            {     
                foreach($arraySectorizacion as $objSectorizacion)
                { 
                    if($objSectorizacion['intSectorizacion']==="0")
                    {
                        $arraySecuencia     = $this->emcom->getRepository("schemaBundle:AdmiGrupoPromocion")->creaSecuencia();
                        $intNumeroSecuencia = $arraySecuencia['secuencia'];
                        $strAccion          = 'NUEVO';
                    }
                    else
                    {
                        $intNumeroSecuencia = $objSectorizacion['intSectorizacion'];
                        $strAccion          = 'EDITAR';
                    }
                    if(isset($objSectorizacion['intJurisdiccion']) && !empty($objSectorizacion['intJurisdiccion'])
                        && $objSectorizacion['intJurisdiccion'] !== "0")
                    {
                        $arrayParamReglasJurisdiccion = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                              'strRegla'            => 'PROM_JURISDICCION',
                                                              'strAccion'           => $strAccion,
                                                              'strValorRegla'       => $objSectorizacion['intJurisdiccion'],
                                                              'strFeCreacion'       => $strFeUltMod,
                                                              'strUsrCreacion'      => $strUsrUltMod,
                                                              'strIpCreacion'       => $strIpUltMod,
                                                              'intNumeroSecuencia'  => $intNumeroSecuencia
                                                             );
                        $this->servicePromocion->actualizarTipoPromocionReglaSectorizacion($arrayParamReglasJurisdiccion);
                    }
                    if(isset($objSectorizacion['intCanton']) && !empty($objSectorizacion['intCanton']) && $objSectorizacion['intCanton'] !== "0")
                    {
                        $arrayParamReglasCanton = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                        'strRegla'            => 'PROM_CANTON',
                                                        'strAccion'           => $strAccion,
                                                        'strValorRegla'       => $objSectorizacion['intCanton'],
                                                        'strFeCreacion'       => $strFeUltMod,
                                                        'strUsrCreacion'      => $strUsrUltMod,
                                                        'strIpCreacion'       => $strIpUltMod,
                                                        'intNumeroSecuencia'  => $intNumeroSecuencia
                                                       );
                       
                        $this->servicePromocion->actualizarTipoPromocionReglaSectorizacion($arrayParamReglasCanton);
                    }
                    if(isset($objSectorizacion['intParroquia']) && !empty($objSectorizacion['intParroquia'])
                        && $objSectorizacion['intParroquia'] !== "0")
                    {
                       $arrayParamReglasParroquia = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                          'strRegla'            => 'PROM_PARROQUIA',
                                                          'strAccion'           => $strAccion,
                                                          'strValorRegla'       => $objSectorizacion['intParroquia'],
                                                          'strFeCreacion'       => $strFeUltMod,
                                                          'strUsrCreacion'      => $strUsrUltMod,
                                                          'strIpCreacion'       => $strIpUltMod,
                                                          'intNumeroSecuencia'  => $intNumeroSecuencia 
                                                         );
                        $this->servicePromocion->actualizarTipoPromocionReglaSectorizacion($arrayParamReglasParroquia);
                    }
                    if(isset($objSectorizacion['strOptSectOltEdif']) && !empty($objSectorizacion['strOptSectOltEdif']) 
                       && $objSectorizacion['strOptSectOltEdif'] !== "TODOS")
                    {
                        $strReglaPromocion = "";
                        if($objSectorizacion['strOptSectOltEdif'] === 'sector')
                        {
                            $strReglaPromocion = 'PROM_SECTOR';
                        }
                        elseif($objSectorizacion['strOptSectOltEdif'] === 'olt')
                        {
                            $strReglaPromocion = 'PROM_ELEMENTO';
                        }
                        else
                        {
                            $strReglaPromocion = 'PROM_EDIFICIO';
                        }
                        $arrayParamReglasSectorOlt = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                           'strRegla'            => $strReglaPromocion,
                                                           'strAccion'           => $strAccion,
                                                           'strValorRegla'       => $objSectorizacion['intSectOltEdif'],
                                                           'strFeCreacion'       => $strFeUltMod,
                                                           'strUsrCreacion'      => $strUsrUltMod,
                                                           'strIpCreacion'       => $strIpUltMod,
                                                           'intNumeroSecuencia'  => $intNumeroSecuencia
                                        );
                        $this->servicePromocion->actualizarTipoPromocionReglaSectorizacion($arrayParamReglasSectorOlt);
                    }
                }
            }
            
            if( $strCodigoPromocionIng=='S' &&  (isset($strCodigoPromocion) && !empty($strCodigoPromocion)))
            {
                $strAccion='EDITAR';
                $arrayParamReglaTipoNegocio = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                'strRegla'            => 'PROM_CODIGO',
                                                'strAccion'           => $strAccion,
                                                'strValorRegla'       => $strCodigoPromocion,
                                                'strFeCreacion'       => $strFeUltMod,
                                                'strUsrCreacion'      => $strUsrUltMod,
                                                'strIpCreacion'       => $strIpUltMod);
                $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglaTipoNegocio);
           
            }
            else if ($strCodigoPromocionIng=='N' && (isset($strCodigoPromocion) && !empty($strCodigoPromocion)))
            {
                 
                  $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                  'strRegla'            => 'PROM_CODIGO',
                                                  'strValorRegla'       => $strCodigoPromocion,
                                                  'strFeCreacion'       => $strFeUltMod,
                                                  'strUsrCreacion'      => $strUsrUltMod,
                                                  'strIpCreacion'       => $strIpUltMod
                                                 );
                $this->servicePromocion->ingresarTipoPromocionRegla($arrayParamReglas);
                 
            }
            else if ($strCodigoPromocionIng=='S' && (empty($strCodigoPromocion) || $strCodigoPromocion==''))
            {
                $strAccion='ELIMINAR';
                $arrayParamReglaTipoNegocio = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                                'strRegla'            => 'PROM_CODIGO',
                                                'strAccion'           => $strAccion,
                                                'strFeCreacion'       => $strFeUltMod,
                                                'strUsrCreacion'      => $strUsrUltMod,
                                                'strIpCreacion'       => $strIpUltMod);
                $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglaTipoNegocio);

            }
            
            $strAccion = ( isset($strIdsTiposNegocio) && !empty($strIdsTiposNegocio) ) ? 'EDITAR' : 'ELIMINAR'; 
           
            $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                      'strRegla'           => 'PROM_TIPO_NEGOCIO',
                                      'strAccion'          => $strAccion,
                                      'strValorRegla'      => $strIdsTiposNegocio,
                                      'strFeCreacion'      => $strFeUltMod,
                                      'strUsrCreacion'     => $strUsrUltMod,
                                      'strIpCreacion'      => $strIpUltMod,
                                     );
            $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglas);
            
            $strAccion = ( isset($strIdsFormasPago) && !empty($strIdsFormasPago) ) ? 'EDITAR' : 'ELIMINAR'; 
                      
            $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                      'strRegla'           => 'PROM_FORMA_PAGO',
                                      'strAccion'          => $strAccion,
                                      'strValorRegla'      => $strIdsFormasPago,
                                      'strFeCreacion'      => $strFeUltMod,
                                      'strUsrCreacion'     => $strUsrUltMod,
                                      'strIpCreacion'      => $strIpUltMod,
                                     );
            $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglas);

            $strAccion = ( isset($strIdsUltimaMilla) && !empty($strIdsUltimaMilla) ) ? 'EDITAR' : 'ELIMINAR'; 
                            
            $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                      'strRegla'           => 'PROM_ULTIMA_MILLA',
                                      'strAccion'          => $strAccion,
                                      'strValorRegla'      => $strIdsUltimaMilla,
                                      'strFeCreacion'      => $strFeUltMod,
                                      'strUsrCreacion'     => $strUsrUltMod,
                                      'strIpCreacion'      => $strIpUltMod,
                                     );
            $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglas);
            
            $strAccion = ( isset($strIdsEstadoServicio) && !empty($strIdsEstadoServicio) ) ? 'EDITAR' : 'ELIMINAR'; 
                            
            $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                      'strRegla'           => 'PROM_ESTADO_SERVICIO',
                                      'strAccion'          => $strAccion,
                                      'strValorRegla'      => $strIdsEstadoServicio,
                                      'strFeCreacion'      => $strFeUltMod,
                                      'strUsrCreacion'     => $strUsrUltMod,
                                      'strIpCreacion'      => $strIpUltMod,
                                     );
            $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglas);
                
            if(isset($arrayEmisores) && !empty($arrayEmisores))
            {
                $arrayParametros           = array();
                $arrayParamBancoTipoCuenta = array();
                $arrayBancoTipoCuenta      = array();
                $arrayIdBancoTipoCuenta    = [];
                for($i = 0; $i < count($arrayEmisores); $i++)
                {
                    $arrayDescomponerEmisor         = ($arrayEmisores[$i]);
                    $arrayDescomponerEmisor         = explode("|", $arrayDescomponerEmisor);
                    $strIdCuenta                    = $arrayDescomponerEmisor[0];
                    $strIdBanco                     = $arrayDescomponerEmisor[1];
                    $arrayParametros['strIdCuenta'] = $strIdCuenta;

                    if($strIdBanco === '0')
                    {
                        $arrayParametros['strIdBanco'] = '';
                        $arrayParametros['strEsTarjeta'] = 'S';
                    }
                    else
                    {
                        $arrayParametros['strIdBanco'] = $strIdBanco;
                        $arrayParametros['strEsTarjeta'] = 'N';
                    }
                    $arrayParamBancoTipoCuenta=$this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getBancoTipoCuenta($arrayParametros);
                    $arrayBancoTipoCuenta     = array_merge($arrayBancoTipoCuenta, $arrayParamBancoTipoCuenta);
                }
                foreach($arrayBancoTipoCuenta as $objBancoTipoCuenta)
                {
                    $arrayIdBancoTipoCuenta[] = $objBancoTipoCuenta['idBancoTipoCuenta'];
                }
                $strIdBancoTipoCuenta = implode(",", $arrayIdBancoTipoCuenta);                                
                $strAccion            = 'EDITAR';
            }
            else
            {
                $strAccion            = 'ELIMINAR';
                $strIdBancoTipoCuenta = "";
            }
            $arrayParamReglas = array('intIdTipoPromocion'  => $objAdmiTipoPromocion->getId(),
                                      'strRegla'            => 'PROM_EMISOR',
                                      'strAccion'           => $strAccion,
                                      'strValorRegla'       => $strIdBancoTipoCuenta,
                                      'strFeCreacion'       => $strFeUltMod,
                                      'strUsrCreacion'      => $strUsrUltMod,
                                      'strIpCreacion'       => $strIpUltMod,
                                      );
            $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglas);
            
            $strAccion = ( isset($strPeriodos) && !empty($strPeriodos) ) ? 'EDITAR' : 'ELIMINAR'; 
            
            $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                      'strRegla'           => 'PROM_PERIODO',
                                      'strAccion'          => $strAccion,
                                      'strValorRegla'      => $strPeriodos,
                                      'strFeCreacion'      => $strFeUltMod,
                                      'strUsrCreacion'     => $strUsrUltMod,
                                      'strIpCreacion'      => $strIpUltMod,
                                     );
            $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglas);
            
            $this->emcom->getConnection()->commit();  
            $strRespuesta = 'OK';
        }
        catch(\Exception $e)
        {
            $this->emcom->rollback();
            $this->emcom->close();           
            $strRespuesta = "No se pudo actualizar la Promoción de Instalación <br>". $e->getMessage() . ". Favor notificar a Sistemas.";            
            $this->serviceUtil->insertError('Telcos+',
                                            'PromocionInstalacionService.editarPromoInstalacion',
                                             'Error PromocionInstalacionService.editarPromoInstalacion:'.$e->getMessage(),
                                             $strUsrUltMod,
                                             $strIpUltMod);
            return $strRespuesta;
        }
        return $strRespuesta;
    }    
     /**
     * getPromocionInstalacion()
     * Función que obtiene la información de la Promoción de Instalación,
     * para ser presentada en la página de Ver Detalle.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 04-05-2019          
     * @param array $arrayParametros[]                  
     *              'intIdPromocion'  => Id de Promoción          
     * @return $arrayRespuesta              
     */   
    public function getPromocionInstalacion($arrayParametros)
    {
        $intIdPromocion                        = $arrayParametros['intIdPromocion'];
        $arrayParametrosPromo['intIdPromocion'] = $arrayParametros['intIdPromocion'];
        $arrayParametrosPromo['intIdEmpresa']   = $arrayParametros['intIdEmpresa'];
        $arrayParametrosPromo['strEstado']      = 'Activo';
        
        $arrayParametros['arraySectorizacion'] = array('PROM_JURISDICCION', 'PROM_CANTON', 'PROM_PARROQUIA', 'PROM_SECTOR', 'PROM_ELEMENTO',
                                                       'PROM_EDIFICIO');
        $objPromocion                          = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                             ->getPromocionInstalacion($arrayParametros);
        $arraySectorizacion                    = array();
        $arrayEmisores                         = [];
        
        if(!empty($objPromocion['objSectorizacion']))
        {
            foreach($objPromocion['objSectorizacion'] as $sectorización)
            {
                $strJurisdiccion = !empty($sectorización['descJurisdiccion']) ? $sectorización['descJurisdiccion'] : "TODOS";
                $strCanton       = !empty($sectorización['descCanton']) ? $sectorización['descCanton'] : "TODOS";
                $strParroquia    = !empty($sectorización['descParroquia']) ? $sectorización['descParroquia'] : "TODOS";
                $strSectOltEdif  = "";

                if($sectorización['idSector'] === "0" && $sectorización['idElemento'] === "0" && $sectorización['idEdificio'] === "0")
                {
                    $strOptSectOltEdif = "TODOS";
                }
                else
                {
                    if(!empty($sectorización['idSector']) && $sectorización['idSector'] !== "0")
                    {
                        $strOptSectOltEdif = "Sector";
                        $arraySector       = explode(",", $sectorización['idSector']);
                        foreach($arraySector as $idSector)
                        {
                            $objSector       = $this->emInfraestructura->getRepository('schemaBundle:AdmiSector')->find((int) $idSector);
                            $strSectOltEdif .= $objSector->getNombreSector() . ', ';
                        }
                    }

                    if(!empty($sectorización['idElemento']) && $sectorización['idElemento'] !== "0")
                    {
                        $strOptSectOltEdif = "Olt";
                        $arrayElemento     = explode(",", $sectorización['idElemento']);
                        
                        foreach($arrayElemento as $idElemento)
                        {
                            $arrayParametrosOlt['intIdElemento']   = (int) $idElemento;
                            $arrayParametrosOlt['intIdEmpresa']    = $arrayParametros['intIdEmpresa'];
                            $arrayParametrosOlt['strTipoElemento'] = 'OLT';
                            $objElemento                           = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                                                 ->getOltEdificioById($arrayParametrosOlt);
                            $strSectOltEdif                        .= $objElemento[0]['nombre'] . ', ';
                        }
                    }
                    
                    if(!empty($sectorización['idEdificio']) && $sectorización['idEdificio'] !== "0")
                    {
                        $strOptSectOltEdif = "Edificio";
                        $arrayEdificio     = explode(",", $sectorización['idEdificio']);
                        
                        foreach($arrayEdificio as $idEdificio)
                        {
                            $arrayParametrosEdificio['intIdElemento']    = (int) $idEdificio;
                            $arrayParametrosEdificio['intIdEmpresa']     = $arrayParametros['intIdEmpresa'];
                            $arrayParametrosEdificio['strTipoElemento']  = 'EDIFICACION';
                            
                            $objEdificio                                 = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                                                       ->getOltEdificioById($arrayParametrosEdificio);
                            $strSectOltEdif                             .= $objEdificio[0]['nombre'] . ', ';
                        }
                    }
                }
                $arrayCaractSectorizacion = array("strJurisdiccion"   => $strJurisdiccion,
                                                  "strCanton"         => $strCanton,
                                                  "strParroquia"      => $strParroquia,
                                                  "strOptSectOltEdif" => $strOptSectOltEdif,
                                                  "strSectOltEdif"    => trim($strSectOltEdif, ', ')
                                                 );
                array_push($arraySectorizacion, $arrayCaractSectorizacion);
            }
        }
        else
        {
            $arrayCaractSectorizacion = array("strJurisdiccion"   => "TODOS",
                                              "strCanton"         => "TODOS",
                                              "strParroquia"      => "TODOS",
                                              "strOptSectOltEdif" => "TODOS",
                                              "strSectOltEdif"    => ""
                                             );
            array_push($arraySectorizacion, $arrayCaractSectorizacion);
        }
        
        foreach($objPromocion['objTipoPromocion'] as $tipoPromocion)
        {
            if($tipoPromocion['descCaracteristica'] === "PROM_ESTADO_SERVICIO")
            {
                $strEstadoServicio = $tipoPromocion['valorCaracteristica'];
            }
            if($tipoPromocion['descCaracteristica'] === "PROM_CODIGO")
            {
                $strCodigoPromocion = $tipoPromocion['valorCaracteristica'];
            }
            if($tipoPromocion['descCaracteristica'] === "PROM_PERIODO")
            {                
                $arrayPeriodo                  = array();
                $arrayDescuento                = array();
                $arrayListPeriodosDescuentos   = explode(",", $tipoPromocion['valorCaracteristica']);
                for ($intIndice = 0; $intIndice < count($arrayListPeriodosDescuentos); $intIndice++ )
                {
                    $arrayPeriodoDescuento = explode("|", $arrayListPeriodosDescuentos[$intIndice]);
                    $arrayPeriodo[]        = $arrayPeriodoDescuento[0];
                    $arrayDescuento[]      = $arrayPeriodoDescuento[1];
                }
            }
            if($tipoPromocion['descCaracteristica'] === "PROM_FORMA_PAGO")
            {
                $arrayParametros = array('arrayFormaPago' => explode(",", $tipoPromocion['valorCaracteristica']));
                $objFormasPagos = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getFormasPagos($arrayParametros);
                $strFormaPago = "";
                foreach($objFormasPagos as $objFormaPago)
                {
                    $strFormaPago = $strFormaPago . $objFormaPago['descripcionFormaPago'] . ', ';
                }
            }
            if($tipoPromocion['descCaracteristica'] === "PROM_EMISOR")
            {
                $arrayParametros = array('arrayBancoTipoCuenta' => explode(",", $tipoPromocion['valorCaracteristica']));
                $objEmisores = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getEmisores($arrayParametros);
                $strEmisores = "";
                foreach($objEmisores as $objEmisor)
                {
                    $strEmisores = $strEmisores . $objEmisor['descripcionCuenta'] . '-' . $objEmisor['nombreBanco'] . ', ';
                }
                $arrayParametrosPromo['arrayEstado'] = array('Eliminado');
                $arrayEmisores  = $this->servicePromocionAnchoBanda->getEmisoresPromoAnchoBanda($arrayParametrosPromo);                
            }
            if($tipoPromocion['descCaracteristica'] === "PROM_TIPO_NEGOCIO")
            {
                $arrayParametros = array('arrayTipoNegocio' => explode(",", $tipoPromocion['valorCaracteristica']));
                $objTipoNegocios = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getTipoNegocios($arrayParametros);
                $strTipoNegocio = "";
                foreach($objTipoNegocios as $objTipoNegocio)
                {
                    $strTipoNegocio = $strTipoNegocio . $objTipoNegocio['nombreTipoNegocio'] . ', ';
                }
            }
            if($tipoPromocion['descCaracteristica'] === "PROM_ULTIMA_MILLA")
            {
                $arrayParametros = array('arrayUltimaMilla' => explode(",", $tipoPromocion['valorCaracteristica']));
                $objUltimasMillas = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getUltimasMillas($arrayParametros);
                $strUltimaMilla = "";
                foreach($objUltimasMillas as $objUltimaMilla)
                {
                    $strUltimaMilla = $strUltimaMilla . $objUltimaMilla['nombreTipoMedio'] . ', ';
                }
            }
        }
        $objAdmiGrupoPromocion = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->find($intIdPromocion);
        $arrayCaracteristicas = array('arraySectorizacion' => $arraySectorizacion,
                                      'strEstadoServicio'  => trim($strEstadoServicio, ', '),
                                      'strFormaPago'       => trim($strFormaPago, ', '),
                                      'strEmisores'        => trim($strEmisores, ', '),
                                      'strTipoNegocio'     => trim($strTipoNegocio, ', '),
                                      'strUltimaMilla'     => trim($strUltimaMilla, ', '),
                                      'arrayDescuento'     => $arrayDescuento,
                                      'arrayPeriodo'       => $arrayPeriodo,
                                      'strCodigoPromocion'  => trim($strCodigoPromocion, ', ')
        );
        $arrayRespuesta = array('objAdmiGrupoPromocion' => $objAdmiGrupoPromocion,
                                'arrayCaracteristicas'  => $arrayCaracteristicas,
                                'arrayEmisores'         => $arrayEmisores
        );
        return $arrayRespuesta;        
    }         
      
}
