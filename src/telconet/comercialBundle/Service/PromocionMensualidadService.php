<?php

namespace telconet\comercialBundle\Service;

use telconet\schemaBundle\Entity\AdmiGrupoPromocion;
use telconet\schemaBundle\Entity\AdmiTipoPromocion;

class PromocionMensualidadService
{
    private $emcom;
    private $emInfraestructura;
    private $serviceUtil;
    private $emGeneral;
    private $servicePromocion;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emcom             = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emInfraestructura = $container->get('doctrine.orm.telconet_infraestructura_entity_manager');
        $this->emGeneral         = $container->get('doctrine.orm.telconet_general_entity_manager');
        $this->serviceUtil       = $container->get('schema.Util');
        $this->servicePromocion  = $container->get('comercial.Promocion');
    }

    /**
     * guardarPromoMensualidad()
     * Función que guarda nueva Promoción de Mensualidad.
     * 
     * @author Hector Lozano hlozano@telconet.ec>
     * @version 1.0 01-04-2019         
     * @param array $arrayParametros[                 
     *                                 'arraySectorizacion'    => Arreglo de Sectorización(Jurisdicción,Cantón,Parroquia,Sector/Olt),
     *                                 'strNombrePromocion'    => Nombre de Promoción,
     *                                 'strIdsEstadoServicio'  => Estados del Servicio separado por caracter(,),
     *                                 'strInicioVigencia'     => Fecha de Inicio de Vigencia,
     *                                 'strFinVigencia'        => Fecha de Fi de Vigencia,
     *                                 'strIdsFormasPago'      => Id's de formas de pago separado por caracter(,)
     *                                 'arrayEmisores'         => Arreglo de Emisores,
     *                                 'strUsrCreacion'        => Usuario de Creación,
     *                                 'strCodEmpresa'         => Código de Empresa,
     *                                 'strIpCreacion'         => Ip de Creación,
     *                                 'arrayPromoMix'         => Arreglo de Promoción Mix,
     *                                 'arrayPromoPlanes'      => Arreglo de Promoción Planes,
     *                                 'arrayPromoProductos'   => Arreglo de Promoción Productos,
     *                                 'arrayPromoDescTotal'   => Arreglo de Promoción Descuento Total
     *                              ]                          
     * @return $strResponse - Mensaje de estado de la transacción.
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.1 12-10-2020 - Se envía el tipo de promoción origen para guardar la característica, 
     * se guarda Id de la promoción Oirgen.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.2 27-09-2022 - Se agrega regla PROM_PERM_MIN_CV, utilizada para la Cancelación Voluntaria.
     * 
     */
    public function guardarPromoMensualidad($arrayParametros)
    {
        $arraySectorizacion     = $arrayParametros['arraySectorizacion'];
        $strNombrePromocion     = $arrayParametros['strNombrePromocion'];
        $strIdsEstadoServicio   = $arrayParametros['strIdsEstadoServicio'];
        $strInicioVigencia      = $arrayParametros['strInicioVigencia'];
        $strFinVigencia         = $arrayParametros['strFinVigencia'];
        $strIdsFormasPago       = $arrayParametros['strIdsFormasPago'];
        $arrayEmisores          = $arrayParametros['arrayEmisores'];
        $strTipoCliente         = $arrayParametros['strTipoCliente'];
        $strPermMinimaCancelVol = $arrayParametros['strPermMinimaCancelVol'];
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $strCodEmpresa          = $arrayParametros['strCodEmpresa'];
        $strIpCreacion          = $arrayParametros['strIpCreacion'];
        $strFeCreacion          = new \DateTime('now');
        $arrayPromoMix          = $arrayParametros['arrayPromoMix'];
        $arrayPromoPlanes       = $arrayParametros['arrayPromoPlanes'];
        $arrayPromoProductos    = $arrayParametros['arrayPromoProductos'];
        $arrayPromoDescTotal    = $arrayParametros['arrayPromoDescTotal'];
        $strTipoEdicion         = $arrayParametros['strTipoEdicion'];
        $intIdPromocionOrigen   = $arrayParametros['intIdPromocionOrigen'];
        $strCodigoPromocion     = $arrayParametros['strCodigoPromocion'];
        $this->emcom->beginTransaction();
        try
        {
            $objInfoEmpresaGrupo = $this->emcom->getRepository('schemaBundle:InfoEmpresaGrupo')->find($strCodEmpresa);
            if(!is_object($objInfoEmpresaGrupo))
            {
                throw new \Exception("No se pudo crear la Promoción, No se encontró empresa en sesión.");
            }
            $objAdmiGrupoPromocion = new AdmiGrupoPromocion();
            $objAdmiGrupoPromocion->setNombreGrupo(substr($strNombrePromocion, 0, 4000));
            $objAdmiGrupoPromocion->setFeInicioVigencia(new \DateTime($strInicioVigencia));
            $objAdmiGrupoPromocion->setFeFinVigencia(new \DateTime($strFinVigencia));
            $objAdmiGrupoPromocion->setFeCreacion($strFeCreacion);
            $objAdmiGrupoPromocion->setUsrCreacion($strUsrCreacion);
            $objAdmiGrupoPromocion->setIpCreacion($strIpCreacion);
            $objAdmiGrupoPromocion->setEmpresaCod($objInfoEmpresaGrupo);
            if($strTipoEdicion=='ED')
            {
                $objAdmiGrupoPromocion->setGrupoPromocionId($intIdPromocionOrigen);
            }
            $objAdmiGrupoPromocion->setEstado('Activo');
            $this->emcom->persist($objAdmiGrupoPromocion);
            $this->emcom->flush();
            
             if($strTipoEdicion=='ED')
            {
                $arrayParamReglas = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                               'strRegla'            => 'ORIGEN_PROMOCION_EDITADA',
                                               'strValorRegla'       => $intIdPromocionOrigen,
                                               'strFeCreacion'       => $strFeCreacion,
                                               'strUsrCreacion'      => $strUsrCreacion,
                                               'strIpCreacion'       => $strIpCreacion
                                              );
                $this->servicePromocion->ingresarGrupoPromocionRegla($arrayParamReglas);
            }

            if(isset($strCodigoPromocion) && !empty($strCodigoPromocion))
            {
                $arrayParamReglas = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                               'strRegla'            => 'PROM_CODIGO',
                                               'strValorRegla'       => $strCodigoPromocion,
                                               'strFeCreacion'       => $strFeCreacion,
                                               'strUsrCreacion'      => $strUsrCreacion,
                                               'strIpCreacion'       => $strIpCreacion
                                              );
                $this->servicePromocion->ingresarGrupoPromocionRegla($arrayParamReglas);
            }
           
            if(isset($arraySectorizacion) && !empty($arraySectorizacion))
            {
                foreach($arraySectorizacion as $objSectorizacion)
                {
                    $arraySecuencia     = $this->emcom->getRepository("schemaBundle:AdmiGrupoPromocion")->creaSecuencia();
                    $intNumeroSecuencia = $arraySecuencia['secuencia'];

                    if(isset($objSectorizacion['intJurisdiccion']) && !empty($objSectorizacion['intJurisdiccion'])
                       && $objSectorizacion['intJurisdiccion'] !== "0")
                    {
                        $arrayParamReglas = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                                  'strRegla'            => 'PROM_JURISDICCION',
                                                  'strValorRegla'       => $objSectorizacion['intJurisdiccion'],
                                                  'strFeCreacion'       => $strFeCreacion,
                                                  'strUsrCreacion'      => $strUsrCreacion,
                                                  'strIpCreacion'       => $strIpCreacion,
                                                  'intNumeroSecuencia'  => $intNumeroSecuencia
                                                 );
                        $this->servicePromocion->ingresarGrupoPromocionRegla($arrayParamReglas);
                    }
                    
                    if(isset($objSectorizacion['intCanton']) && !empty($objSectorizacion['intCanton']) 
                       && $objSectorizacion['intCanton'] !== "0")
                    {
                        $arrayParamReglas = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                                  'strRegla'            => 'PROM_CANTON',
                                                  'strValorRegla'       => $objSectorizacion['intCanton'],
                                                  'strFeCreacion'       => $strFeCreacion,
                                                  'strUsrCreacion'      => $strUsrCreacion,
                                                  'strIpCreacion'       => $strIpCreacion,
                                                  'intNumeroSecuencia'  => $intNumeroSecuencia
                                                 );
                        $this->servicePromocion->ingresarGrupoPromocionRegla($arrayParamReglas);
                    }
                    
                    if(isset($objSectorizacion['intParroquia']) && !empty($objSectorizacion['intParroquia']) 
                       && $objSectorizacion['intParroquia'] !== "0")
                    {
                        $arrayParamReglas = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                                  'strRegla'            => 'PROM_PARROQUIA',
                                                  'strValorRegla'       => $objSectorizacion['intParroquia'],
                                                  'strFeCreacion'       => $strFeCreacion,
                                                  'strUsrCreacion'      => $strUsrCreacion,
                                                  'strIpCreacion'       => $strIpCreacion,
                                                  'intNumeroSecuencia'  => $intNumeroSecuencia
                                                 );
                        $this->servicePromocion->ingresarGrupoPromocionRegla($arrayParamReglas);
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
                        $arrayParamReglas = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                                  'strRegla'            => $strReglaPromocion,
                                                  'strValorRegla'       => $objSectorizacion['intSectOltEdif'],
                                                  'strFeCreacion'       => $strFeCreacion,
                                                  'strUsrCreacion'      => $strUsrCreacion,
                                                  'strIpCreacion'       => $strIpCreacion,
                                                  'intNumeroSecuencia'  => $intNumeroSecuencia
                                                 );
                        $this->servicePromocion->ingresarGrupoPromocionRegla($arrayParamReglas);
                    }
                }
            }

            if(isset($strIdsEstadoServicio) && !empty($strIdsEstadoServicio))
            {
                $arrayParamReglas = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                          'strRegla'            => 'PROM_ESTADO_SERVICIO',
                                          'strValorRegla'       => $strIdsEstadoServicio,
                                          'strFeCreacion'       => $strFeCreacion,
                                          'strUsrCreacion'      => $strUsrCreacion,
                                          'strIpCreacion'       => $strIpCreacion,
                                         );
                $this->servicePromocion->ingresarGrupoPromocionRegla($arrayParamReglas);
            }
            if(isset($strIdsFormasPago) && !empty($strIdsFormasPago))
            {
                $arrayParamReglas = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                          'strRegla'            => 'PROM_FORMA_PAGO',
                                          'strValorRegla'       => $strIdsFormasPago,
                                          'strFeCreacion'       => $strFeCreacion,
                                          'strUsrCreacion'      => $strUsrCreacion,
                                          'strIpCreacion'       => $strIpCreacion,
                                         );
                $this->servicePromocion->ingresarGrupoPromocionRegla($arrayParamReglas);
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

                    $arrayParamBancoTipoCuenta = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getBancoTipoCuenta($arrayParametros);
                    $arrayBancoTipoCuenta      = array_merge($arrayBancoTipoCuenta, $arrayParamBancoTipoCuenta);
                }
                foreach($arrayBancoTipoCuenta as $objBancoTipoCuenta)
                {
                    $arrayIdBancoTipoCuenta[] = $objBancoTipoCuenta['idBancoTipoCuenta'];
                }
                $strIdBancoTipoCuenta = implode(",", $arrayIdBancoTipoCuenta);
                $arrayParamReglas     = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                              'strRegla'            => 'PROM_EMISOR',
                                              'strValorRegla'       => $strIdBancoTipoCuenta,
                                              'strFeCreacion'       => $strFeCreacion,
                                              'strUsrCreacion'      => $strUsrCreacion,
                                              'strIpCreacion'       => $strIpCreacion,
                                             );
                $this->servicePromocion->ingresarGrupoPromocionRegla($arrayParamReglas);
            }       
            if(isset($strTipoCliente) && !empty($strTipoCliente))
            {
                $arrayParamReglas = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                          'strRegla'            => 'PROM_TIPO_CLIENTE',
                                          'strValorRegla'       => $strTipoCliente,
                                          'strFeCreacion'       => $strFeCreacion,
                                          'strUsrCreacion'      => $strUsrCreacion,
                                          'strIpCreacion'       => $strIpCreacion,
                                         );
                $this->servicePromocion->ingresarGrupoPromocionRegla($arrayParamReglas);
            }
            if(isset($strPermMinimaCancelVol) && !empty($strPermMinimaCancelVol))
            {
                $arrayParamReglas = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                          'strRegla'            => 'PROM_PERM_MIN_CV',
                                          'strValorRegla'       => $strPermMinimaCancelVol,
                                          'strFeCreacion'       => $strFeCreacion,
                                          'strUsrCreacion'      => $strUsrCreacion,
                                          'strIpCreacion'       => $strIpCreacion,
                                         );
                $this->servicePromocion->ingresarGrupoPromocionRegla($arrayParamReglas);
            }

            if(!empty($arrayPromoMix))
            {
                $arrayParamTipoPromocion = array('strTipoPromocion'      => 'Descuento en Mensualidad Mix de Planes y Productos',
                                                 'strCodEmpresa'         => $strCodEmpresa,
                                                 'objAdmiGrupoPromocion' => $objAdmiGrupoPromocion,
                                                 'strFeCreacion'         => $strFeCreacion,
                                                 'strUsrCreacion'        => $strUsrCreacion,
                                                 'strIpCreacion'         => $strIpCreacion
                                                );
                if(isset($arrayPromoMix['strPermanenciaMinima']) && !empty($arrayPromoMix['strPermanenciaMinima']))
                {
                    $arrayParamTipoPromocion ['strPermanenciaMinima'] = $arrayPromoMix['strPermanenciaMinima'];
                }
                if(isset($arrayPromoMix['strMora']) && !empty($arrayPromoMix['strMora']))
                {
                    $arrayParamTipoPromocion ['strMora'] = $arrayPromoMix['strMora'];
                }
                if(isset($arrayPromoMix['intValMora']) && !empty($arrayPromoMix['intValMora']))
                {
                    $arrayParamTipoPromocion ['intValMora'] = $arrayPromoMix['intValMora'];
                }
                if(isset($arrayPromoMix['strIndefinida']) && !empty($arrayPromoMix['strIndefinida']))
                {
                    $arrayParamTipoPromocion ['strIndefinida'] = $arrayPromoMix['strIndefinida'];
                }
                if(isset($arrayPromoMix['fltDescIndefinido']) && !empty($arrayPromoMix['fltDescIndefinido']))
                {
                    $arrayParamTipoPromocion ['fltDescIndefinido'] = $arrayPromoMix['fltDescIndefinido'];
                }
                if(isset($arrayPromoMix['strTipoPeriodo']) && !empty($arrayPromoMix['strTipoPeriodo']))
                {
                    $arrayParamTipoPromocion ['strTipoPeriodo'] = $arrayPromoMix['strTipoPeriodo'];
                }
                if(isset($arrayPromoMix['arrayDescuentoPeriodo']) && !empty($arrayPromoMix['arrayDescuentoPeriodo']))
                {
                    $arrayParamTipoPromocion ['arrayDescuentoPeriodo'] = $arrayPromoMix['arrayDescuentoPeriodo'];
                }
                if(isset($arrayPromoMix['arrayPlanes']) && !empty($arrayPromoMix['arrayPlanes']))
                {
                    $arrayParamTipoPromocion ['arrayPlanes'] = $arrayPromoMix['arrayPlanes'];
                }
                if(isset($arrayPromoMix['arrayProductos']) && !empty($arrayPromoMix['arrayProductos']))
                {
                    $arrayParamTipoPromocion ['arrayProductos'] = $arrayPromoMix['arrayProductos'];
                }

                $this->servicePromocion->guardarTipoPromocion($arrayParamTipoPromocion);
            }

            if(!empty($arrayPromoPlanes))
            {
                $arrayParamTipoPromocion = array('strTipoPromocion'      => 'Descuento en Mensualidad de Planes',
                                                 'strCodEmpresa'         => $strCodEmpresa,
                                                 'objAdmiGrupoPromocion' => $objAdmiGrupoPromocion,
                                                 'strFeCreacion'         => $strFeCreacion,
                                                 'strUsrCreacion'        => $strUsrCreacion,
                                                 'strIpCreacion'         => $strIpCreacion
                                                );
                if(isset($arrayPromoPlanes['strPermanenciaMinima']) && !empty($arrayPromoPlanes['strPermanenciaMinima']))
                {
                    $arrayParamTipoPromocion ['strPermanenciaMinima'] = $arrayPromoPlanes['strPermanenciaMinima'];
                }
                if(isset($arrayPromoPlanes['strMora']) && !empty($arrayPromoPlanes['strMora']))
                {
                    $arrayParamTipoPromocion ['strMora'] = $arrayPromoPlanes['strMora'];
                }
                if(isset($arrayPromoPlanes['intValMora']) && !empty($arrayPromoPlanes['intValMora']))
                {
                    $arrayParamTipoPromocion ['intValMora'] = $arrayPromoPlanes['intValMora'];
                }
                if(isset($arrayPromoPlanes['strIndefinida']) && !empty($arrayPromoPlanes['strIndefinida']))
                {
                    $arrayParamTipoPromocion ['strIndefinida'] = $arrayPromoPlanes['strIndefinida'];
                }
                if(isset($arrayPromoPlanes['fltDescIndefinido']) && !empty($arrayPromoPlanes['fltDescIndefinido']))
                {
                    $arrayParamTipoPromocion ['fltDescIndefinido'] = $arrayPromoPlanes['fltDescIndefinido'];
                }
                if(isset($arrayPromoPlanes['strTipoPeriodo']) && !empty($arrayPromoPlanes['strTipoPeriodo']))
                {
                    $arrayParamTipoPromocion ['strTipoPeriodo'] = $arrayPromoPlanes['strTipoPeriodo'];
                }
                if(isset($arrayPromoPlanes['arrayDescuentoPeriodo']) && !empty($arrayPromoPlanes['arrayDescuentoPeriodo']))
                {
                    $arrayParamTipoPromocion ['arrayDescuentoPeriodo'] = $arrayPromoPlanes['arrayDescuentoPeriodo'];
                }
                if(isset($arrayPromoPlanes['arrayPlanes']) && !empty($arrayPromoPlanes['arrayPlanes']))
                {
                    $arrayParamTipoPromocion ['arrayPlanes'] = $arrayPromoPlanes['arrayPlanes'];
                }

                $this->servicePromocion->guardarTipoPromocion($arrayParamTipoPromocion);
            }

            if(!empty($arrayPromoProductos))
            {
                $arrayParamTipoPromocion = array('strTipoPromocion'      => 'Descuento en Mensualidad de Productos',
                                                 'strCodEmpresa'         => $strCodEmpresa,
                                                 'objAdmiGrupoPromocion' => $objAdmiGrupoPromocion,
                                                 'strFeCreacion'         => $strFeCreacion,
                                                 'strUsrCreacion'        => $strUsrCreacion,
                                                 'strIpCreacion'         => $strIpCreacion
                                                );
                if(isset($arrayPromoProductos['strPermanenciaMinima']) && !empty($arrayPromoProductos['strPermanenciaMinima']))
                {
                    $arrayParamTipoPromocion ['strPermanenciaMinima'] = $arrayPromoProductos['strPermanenciaMinima'];
                }
                if(isset($arrayPromoProductos['strMora']) && !empty($arrayPromoProductos['strMora']))
                {
                    $arrayParamTipoPromocion ['strMora'] = $arrayPromoProductos['strMora'];
                }
                if(isset($arrayPromoProductos['intValMora']) && !empty($arrayPromoProductos['intValMora']))
                {
                    $arrayParamTipoPromocion ['intValMora'] = $arrayPromoProductos['intValMora'];
                }
                if(isset($arrayPromoProductos['strIndefinida']) && !empty($arrayPromoProductos['strIndefinida']))
                {
                    $arrayParamTipoPromocion ['strIndefinida'] = $arrayPromoProductos['strIndefinida'];
                }
                if(isset($arrayPromoProductos['fltDescIndefinido']) && !empty($arrayPromoProductos['fltDescIndefinido']))
                {
                    $arrayParamTipoPromocion ['fltDescIndefinido'] = $arrayPromoProductos['fltDescIndefinido'];
                }
                if(isset($arrayPromoProductos['strTipoPeriodo']) && !empty($arrayPromoProductos['strTipoPeriodo']))
                {
                    $arrayParamTipoPromocion ['strTipoPeriodo'] = $arrayPromoProductos['strTipoPeriodo'];
                }
                if(isset($arrayPromoProductos['arrayDescuentoPeriodo']) && !empty($arrayPromoProductos['arrayDescuentoPeriodo']))
                {
                    $arrayParamTipoPromocion ['arrayDescuentoPeriodo'] = $arrayPromoProductos['arrayDescuentoPeriodo'];
                }
                if(isset($arrayPromoProductos['arrayProductos']) && !empty($arrayPromoProductos['arrayProductos']))
                {
                    $arrayParamTipoPromocion ['arrayProductos'] = $arrayPromoProductos['arrayProductos'];
                }

                $this->servicePromocion->guardarTipoPromocion($arrayParamTipoPromocion);
            }
            if(!empty($arrayPromoDescTotal))
            {
                $arrayParamTipoPromocion = array('strTipoPromocion'      => 'Descuento Total en Mensualidad',
                                                 'strCodEmpresa'         => $strCodEmpresa,
                                                 'objAdmiGrupoPromocion' => $objAdmiGrupoPromocion,
                                                 'strFeCreacion'         => $strFeCreacion,
                                                 'strUsrCreacion'        => $strUsrCreacion,
                                                 'strIpCreacion'         => $strIpCreacion
                                                );
                if(isset($arrayPromoDescTotal['strTipoPeriodo']) && !empty($arrayPromoDescTotal['strTipoPeriodo']))
                {
                    $arrayParamTipoPromocion ['strTipoPeriodo'] = $arrayPromoDescTotal['strTipoPeriodo'];
                }
                if(isset($arrayPromoDescTotal['arrayDescuentoPeriodo']) && !empty($arrayPromoDescTotal['arrayDescuentoPeriodo']))
                {
                    $arrayParamTipoPromocion ['arrayDescuentoPeriodo'] = $arrayPromoDescTotal['arrayDescuentoPeriodo'];
                }

                $this->servicePromocion->guardarTipoPromocion($arrayParamTipoPromocion);
            }
            $arrayParamPromoGrupoHisto = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                               'strFeCreacion'       => $strFeCreacion,
                                               'strUsrCreacion'      => $strUsrCreacion,
                                               'strIpCreacion'       => $strIpCreacion,
                                               'strObservacion'      => 'Se Creo nueva Promoción Descuento en Mensualidad',
                                               'strEstado'           => 'Activo'
                                              );
            $this->servicePromocion->ingresarPromocionGrupoHisto($arrayParamPromoGrupoHisto);
            $this->emcom->getConnection()->commit();
            $strRespuesta = 'OK';
        }
        catch(\Exception $e)
        {
            $this->emcom->rollback();
            $this->emcom->close();
            $strRespuesta = "No se pudo guardar Promoción de Mensualidad. Favor notificar a Sistemas.";
            $this->serviceUtil->insertError('Telcos+', 
                                            'PromocionService.guardarPromoMensualidad', 
                                            'Error PromocionService.guardarPromoMensualidad:'. $e->getMessage(),
                                             $strUsrCreacion, 
                                             $strIpCreacion
                                           );
            return $strRespuesta;
        }
        return $strRespuesta;
    }

    /**
     * getPromocionMensual()
     * Función que obtiene la información de la Promoción de Mensualidad,
     * para ser presentada en la página de Ver Detalle.
     * 
     * @author Hector Lozano hlozano@telconet.ec>
     * @version 1.0 04-04-2019         
     * @param array $arrayParametros[                 
     *                                'intIdPromocion'  => Id de Promoción
     *                                'intIdEmpresa'    => Id de Empresa       
     *                              ]  
     * @author José Candelario jcanderlario@telconet.ec>
     * @version 1.1 03-09-2020 Se agrega el id de los planes y productos para que sean visualizados en las interfaces
     *                         de promociones
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.2 12-10-2020 - Se agrega característica PROM_CODIGO.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.2 27-09-2022 - Se agrega que retorne regla PROM_PERM_MIN_CV, utilizada para la Cancelación Voluntaria.
     *  
     * @return $arrayRespuesta              
     */
    public function getPromocionMensual($arrayParametros)
    {
        $arrayTipoPromoMix       = [];
        $arrayTipoPromoPlanes    = [];
        $arrayTipoPromoProductos = [];
        $arrayTipoPromoDescTotal = [];
        $intIdPromocion = $arrayParametros['intIdPromocion'];

        $arrayParametros['arrayEstados']         = array('Eliminado');
        $arrayParametros['arrayCaracteristicas'] = array('PROM_ESTADO_SERVICIO', 'PROM_FORMA_PAGO','PROM_PERM_MIN_CV',
                                                         'PROM_CODIGO', 'PROM_EMISOR', 'PROM_TIPO_CLIENTE');
        $arrayParametros['arraySectorizacion']   = array('PROM_JURISDICCION', 'PROM_CANTON', 'PROM_PARROQUIA',
                                                         'PROM_SECTOR', 'PROM_ELEMENTO', 'PROM_EDIFICIO');
        $objPromocion                            = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                               ->getPromocionMensual($arrayParametros);
        $arraySectorizacion                      = array();
        $arrayEmisores                           = [];

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
                            
                            $objElemento     = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                           ->getOltEdificioById($arrayParametrosOlt);
                            $strSectOltEdif .= $objElemento[0]['nombre'] . ', ';
                        }
                    }
                    
                    if(!empty($sectorización['idEdificio']) && $sectorización['idEdificio'] !== "0")
                    {
                        $strOptSectOltEdif = "Edificio";
                        $arrayEdificio     = explode(",", $sectorización['idEdificio']);
                        
                        foreach($arrayEdificio as $idEdificio)
                        {
                            $arrayParametrosEdificio['intIdElemento'] = (int) $idEdificio;
                            $arrayParametrosEdificio['intIdEmpresa']  = $arrayParametros['intIdEmpresa'];
                            $arrayParametrosEdificio['strTipoElemento']    = 'EDIFICACION';
                            
                            $objEdificio     = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                           ->getOltEdificioById($arrayParametrosEdificio);
                            $strSectOltEdif .= $objEdificio[0]['nombre'] . ', ';
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

        foreach($objPromocion['objCaractGenerales'] as $caracteristica)
        {
            if($caracteristica['caracteristica'] === "PROM_ESTADO_SERVICIO")
            {
                $strEstadoServicio = $caracteristica['valor'];
            }
            if($caracteristica['caracteristica'] === "PROM_CODIGO")
            {
                $strCodigoPromocion = $caracteristica['valor'];
            }
            if($caracteristica['caracteristica'] === "PROM_FORMA_PAGO")
            {
                $arrayParametrosFormaPago = array('arrayFormaPago' => explode(",", $caracteristica['valor']));
                
                $objFormasPagos = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getFormasPagos($arrayParametrosFormaPago);
                $strFormaPago   = "";
                foreach($objFormasPagos as $objFormaPago)
                {
                    $strFormaPago .= $objFormaPago['descripcionFormaPago'] . ', ';
                }
            }
            if($caracteristica['caracteristica'] === "PROM_EMISOR")
            {
                $arrayParametrosEmisor = array('arrayBancoTipoCuenta' => explode(",", $caracteristica['valor']));
                
                $objEmisores = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getEmisores($arrayParametrosEmisor);
                $strEmisores = "";
                foreach($objEmisores as $objEmisor)
                {
                    $strEmisores .= $objEmisor['descripcionCuenta'] . '-' . $objEmisor['nombreBanco'] . ', ';
                }
                $arrayParametrosPromo['intIdPromocion'] = $arrayParametros['intIdPromocion'];
                $arrayParametrosPromo['arrayEstado']    = array('Eliminado');
                $arrayEmisores                          = $this->getEmisoresPromoMensualidad($arrayParametrosPromo);
            }
            if($caracteristica['caracteristica'] === "PROM_TIPO_CLIENTE")
            {
                $strTipoCliente = $caracteristica['valor'];
            }
            if($caracteristica['caracteristica'] === "PROM_PERM_MIN_CV")
            {
                $strPermMinimaCancelVol = $caracteristica['valor'];
            }
        }
        $objAdmiGrupoPromocion = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->find($intIdPromocion);
        $arrayCaracteristicas  = array('arraySectorizacion'     => $arraySectorizacion,
                                       'strEstadoServicio'      => $strEstadoServicio,
                                       'strFormaPago'           => trim($strFormaPago, ', '),
                                       'strEmisores'            => trim($strEmisores, ', '),
                                       'strTipoCliente'         => $strTipoCliente,
                                       'strCodigoPromocion'     => $strCodigoPromocion,
                                       'strPermMinimaCancelVol' => $strPermMinimaCancelVol
                                      );

        foreach($objPromocion['objTipoPromocion'] as $tipoPromocion)
        {
            if($tipoPromocion['descCaracteristica'] === "PROM_PERMANENCIA_MINIMA")
            {
                $strDescCaracteristica = "Permanencia Mínima(Meses)";
            }
            if($tipoPromocion['descCaracteristica'] === "PROM_PIERDE_PROMOCION_MORA")
            {
                $strDescCaracteristica = "Mora";
            }
            if($tipoPromocion['descCaracteristica'] === "PROM_DIAS_MORA")
            {
                $strDescCaracteristica = "Días de Mora";
            }
            if($tipoPromocion['descCaracteristica'] === "PROM_PROMOCION_INDEFINIDA")
            {
                $strDescCaracteristica = "Promoción Indefinida";
            }
            if($tipoPromocion['descCaracteristica'] === "PROM_DESCUENTO")
            {
                $strDescCaracteristica = "Descuento Indefinido(%)";
            }
            if($tipoPromocion['descCaracteristica'] === "PROM_TIPO_PERIODO")
            {
                $strDescCaracteristica = "Tipo Periodo";
            }
            if($tipoPromocion['descCaracteristica'] === "PROM_PERIODO")
            {
                $strDescCaracteristica = "DescUnicoVariable";
            }
            if($tipoPromocion['codTipoPromo'] === "PROM_MIX")
            {
                $arrayTipoPromoMix[] = array('intIdTipoPromo'       => $tipoPromocion['idTipoPromo'],
                                             'strcodTipoPromo'      => $tipoPromocion['codTipoPromo'],
                                             'strDescripcionCaract' => $strDescCaracteristica,
                                             'strValor'             => $tipoPromocion['valorCaracteristica'],
                                            );
            }
            if($tipoPromocion['codTipoPromo'] === "PROM_MPLA")
            {
                $arrayTipoPromoPlanes[] = array('intIdTipoPromo'       => $tipoPromocion['idTipoPromo'],
                                                'strcodTipoPromo'      => $tipoPromocion['codTipoPromo'],
                                                'strDescripcionCaract' => $strDescCaracteristica,
                                                'strValor'             => $tipoPromocion['valorCaracteristica'],
                                               );
            }
            if($tipoPromocion['codTipoPromo'] === "PROM_MPRO")
            {
                $arrayTipoPromoProductos[] = array('intIdTipoPromo'       => $tipoPromocion['idTipoPromo'],
                                                   'strcodTipoPromo'      => $tipoPromocion['codTipoPromo'],
                                                   'strDescripcionCaract' => $strDescCaracteristica,
                                                   'strValor'             => $tipoPromocion['valorCaracteristica'],
                                                  );
            }
            if($tipoPromocion['codTipoPromo'] === "PROM_TOT")
            {
                $arrayTipoPromoDescTotal[] = array('strcodTipoPromo'      => $tipoPromocion['codTipoPromo'],
                                                   'strDescripcionCaract' => $strDescCaracteristica,
                                                   'strValor'             => $tipoPromocion['valorCaracteristica'],
                                                  );
            }
        }
        if(!empty($arrayTipoPromoMix))
        {
            $objTipoPlanProdPromocion = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                             ->getTipoPlanProdPromoNotEliminado(array("intTipoPromocionId" => $arrayTipoPromoMix[0]['intIdTipoPromo'],
                                                                                      "arrayEstados"       => array('Eliminado')));
            $strNombrePlanes    = "";
            $strNombreProductos = "";

            foreach($objTipoPlanProdPromocion as $objPlanProducto)
            {
                $objPlan     = $objPlanProducto->getPlanId();
                $objProducto = $objPlanProducto->getProductoId();

                if($objPlan !== null)
                {
                    $strNombrePlanes .= $objPlan->getNombrePlan() . ' - [' . $objPlan->getId() . '], ';
                }
                if($objProducto !== null)
                {
                    $strNombreProductos .= $objProducto->getDescripcionProducto() . ' - [' . $objProducto->getId() . '] , ';
                }
            }
            $arrayTipoPromoMix[] = array('strDescripcionCaract' => "Planes", 'strValor' => trim($strNombrePlanes, ', '));
            $arrayTipoPromoMix[] = array('strDescripcionCaract' => "Productos", 'strValor' => trim($strNombreProductos, ', '));
        }
        if(!empty($arrayTipoPromoPlanes))
        {
            $objTipoPlanProdPromocion = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                        ->getTipoPlanProdPromoNotEliminado(array("intTipoPromocionId" => $arrayTipoPromoPlanes[0]['intIdTipoPromo'],
                                                                                 "arrayEstados"       => array('Eliminado')));
            $strNombrePlanes = "";

            foreach($objTipoPlanProdPromocion as $objPlanProducto)
            {
                $objPlan = $objPlanProducto->getPlanId();
                if($objPlan !== null)
                {
                    $strNombrePlanes .= $objPlan->getNombrePlan() . ' - [' . $objPlan->getId() . '], ';
                }
            }
            $arrayTipoPromoPlanes[] = array('strDescripcionCaract' => "Planes", 'strValor' => trim($strNombrePlanes, ', '));
        }
        if(!empty($arrayTipoPromoProductos))
        {
            $objTipoPlanProdPromocion = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                       ->getTipoPlanProdPromoNotEliminado(array("intTipoPromocionId" => $arrayTipoPromoProductos[0]['intIdTipoPromo'],
                                                                                "arrayEstados"       => array('Eliminado')));
            $strNombreProductos = "";

            foreach($objTipoPlanProdPromocion as $objPlanProducto)
            {
                $objProducto = $objPlanProducto->getProductoId();
                if($objProducto !== null)
                {
                    $strNombreProductos .= $objProducto->getDescripcionProducto() . ' - [' . $objProducto->getId() . '] , ';
                }
            }
            $arrayTipoPromoProductos[] = array('strDescripcionCaract' => "Productos", 'strValor' => trim($strNombreProductos, ', '));
        }
        $arrayRespuesta = array('objAdmiGrupoPromocion'   => $objAdmiGrupoPromocion,
                                'arrayCaracteristicas'    => $arrayCaracteristicas,
                                'arrayTipoPromoMix'       => $arrayTipoPromoMix,
                                'arrayTipoPromoPlanes'    => $arrayTipoPromoPlanes,
                                'arrayTipoPromoProductos' => $arrayTipoPromoProductos,
                                'arrayTipoPromoDescTotal' => $arrayTipoPromoDescTotal,
                                'arrayEmisores'           => $arrayEmisores
                               );
        return $arrayRespuesta;
    }

    /**
     * getPromocionMensualEditar()
     * Función que obtiene la información de la Promoción de Mensualidad, para editar.
     * 
     * @author Hector Lozano hlozano@telconet.ec>
     * @version 1.0 04-05-2019         
     * @param array $arrayParametros[                 
     *                                'intIdPromocion'  => Id de Promoción
     *                                'intIdEmpresa'    => Id de Empresa         
     *                              ]
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.1 12-10-2020 - Se agrega que retorne promoción de Origen y el código promocional.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.2 27-09-2022 - Se agrega que retorne regla PROM_PERM_MIN_CV, utilizada para la Cancelación Voluntaria.
     * 
     * @return $arrayRespuesta              
     */
    public function getPromocionMensualEditar($arrayParametros)
    {
        $arrayCaracteristicas    = [];
        $arrayTipoPromoMix       = [];
        $arrayTipoPromoPlanes    = [];
        $arrayTipoPromoProductos = [];
        $arrayTipoPromoDescTotal = [];
        $intIdPromocion          = $arrayParametros['intIdPromocion'];
        $strTipoEdicion          = $arrayParametros['strTipoEdicion'];

        $arrayParametros['arrayEstados']         = array('Eliminado');
        $arrayParametros['arrayCaracteristicas'] = array('PROM_ESTADO_SERVICIO', 'PROM_FORMA_PAGO', 'PROM_EMISOR', 'PROM_TIPO_CLIENTE',
                                                         'PROM_CODIGO','PROM_PERM_MIN_CV');
        $arrayParametros['arraySectorizacion']   = array('PROM_JURISDICCION', 'PROM_CANTON', 'PROM_PARROQUIA',
                                                         'PROM_SECTOR', 'PROM_ELEMENTO', 'PROM_EDIFICIO');        
        $objPromocion                            = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                               ->getPromocionMensual($arrayParametros);
        $arraySectorizacion                      = array();

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
                    $strOptSectOltEdif = "TODOS";
                    $arraySectOltEdif  = 0;
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
                $arrayCaractSectorizacion = array("intSectorizacion"   => $intSectorizacion,
                                                  "intJurisdiccion"    => $intJurisdiccion,
                                                  "strJurisdiccion"    => $strJurisdiccion,
                                                  "intCanton"          => $intCanton,
                                                  "strCanton"          => $strCanton,
                                                  "intParroquia"       => $intParroquia,
                                                  "strParroquia"       => $strParroquia,
                                                  "strOptSectOltEdif"  => $strOptSectOltEdif,
                                                  "arraySectOltEdif"   => $arraySectOltEdif,
                                                  "strSectOltEdif"     => trim($strSectOltEdif, ', ')
                                                 );
                array_push($arraySectorizacion, $arrayCaractSectorizacion);
            }
        }

        foreach($objPromocion['objCaractGenerales'] as $caracteristica)
        {
            if($caracteristica['caracteristica'] === "PROM_ESTADO_SERVICIO")
            {
                $strEstadoServicio = $caracteristica['valor'];
                $arrayCaracteristicas['strEstadoServicio'] = $strEstadoServicio;
            }
            if($caracteristica['caracteristica'] === "PROM_FORMA_PAGO")
            {
                $strIdFormasPagos = $caracteristica['valor'];
                $arrayCaracteristicas['strIdFormasPagos'] = $strIdFormasPagos;
            }
            if($caracteristica['caracteristica'] === "PROM_EMISOR")
            {
                $strIdEmisores = $caracteristica['valor'];
                $arrayCaracteristicas['strIdEmisores'] = $strIdEmisores;
            }
            if($caracteristica['caracteristica'] === "PROM_TIPO_CLIENTE")
            {
                $strTipoCliente = $caracteristica['valor'];
                $arrayCaracteristicas['strTipoCliente'] = $strTipoCliente;
            }
            if($caracteristica['caracteristica'] === "PROM_CODIGO")
            {
                $strCodPromocion = $caracteristica['valor'];
                $arrayCaracteristicas['strCodPromocion'] = $strCodPromocion;
            }
            if($caracteristica['caracteristica'] === "PROM_PERM_MIN_CV")
            {
                $strPermMinimaCancelVol = $caracteristica['valor'];
                $arrayCaracteristicas['strPermMinimaCancelVol'] = $strPermMinimaCancelVol;
            }
        }
        $objAdmiGrupoPromocion = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->find($intIdPromocion);
        $arrayCaracteristicas['arraySectorizacion'] = $arraySectorizacion;

        foreach($objPromocion['objTipoPromocion'] as $tipoPromocion)
        {
            if($tipoPromocion['codTipoPromo'] === "PROM_MIX")
            {
                $arrayTipoPromoMix[] = array('intIdTipoPromo'       => $tipoPromocion['idTipoPromo'],
                                             'strcodTipoPromo'      => $tipoPromocion['codTipoPromo'],
                                             'strDescripcionCaract' => $tipoPromocion['descCaracteristica'],
                                             'strValor'             => $tipoPromocion['valorCaracteristica'],
                                            );
            }
            if($tipoPromocion['codTipoPromo'] === "PROM_MPLA")
            {
                $arrayTipoPromoPlanes[] = array('intIdTipoPromo'       => $tipoPromocion['idTipoPromo'],
                                                'strcodTipoPromo'      => $tipoPromocion['codTipoPromo'],
                                                'strDescripcionCaract' => $tipoPromocion['descCaracteristica'],
                                                'strValor'             => $tipoPromocion['valorCaracteristica'],
                                               );
            }
            if($tipoPromocion['codTipoPromo'] === "PROM_MPRO")
            {
                $arrayTipoPromoProductos[] = array('intIdTipoPromo'       => $tipoPromocion['idTipoPromo'],
                                                   'strcodTipoPromo'      => $tipoPromocion['codTipoPromo'],
                                                   'strDescripcionCaract' => $tipoPromocion['descCaracteristica'],
                                                   'strValor'             => $tipoPromocion['valorCaracteristica'],
                                                  );
            }
            if($tipoPromocion['codTipoPromo'] === "PROM_TOT")
            {
                $arrayTipoPromoDescTotal[] = array('strcodTipoPromo'      => $tipoPromocion['codTipoPromo'],
                                                   'strDescripcionCaract' => $tipoPromocion['descCaracteristica'],
                                                   'strValor'             => $tipoPromocion['valorCaracteristica'],
                                                  );
            }
        }
        if(!empty($arrayTipoPromoMix))
        {
            $objTipoPlanProdPromocion = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                             ->getTipoPlanProdPromoNotEliminado(array("intTipoPromocionId" => $arrayTipoPromoMix[0]['intIdTipoPromo'],
                                                                                      "arrayEstados"       => array('Eliminado')));
            $arrayIdPlanes    = [];
            $arrayIdProductos = [];

            foreach($objTipoPlanProdPromocion as $objPlanProducto)
            {
                $objPlan     = $objPlanProducto->getPlanId();
                $objProducto = $objPlanProducto->getProductoId();

                if($objPlan !== null)
                {
                    array_push($arrayIdPlanes, $objPlan->getId());
                }
                if($objProducto !== null)
                {
                    array_push($arrayIdProductos, $objProducto->getId());
                }
            }
            $arrayTipoPromoMix[] = array('strDescripcionCaract' => "Planes", 'strValor' => implode(",", $arrayIdPlanes));
            $arrayTipoPromoMix[] = array('strDescripcionCaract' => "Productos", 'strValor' => implode(",", $arrayIdProductos));
        }
        if(!empty($arrayTipoPromoPlanes))
        {
            $objTipoPlanProdPromocion = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                        ->getTipoPlanProdPromoNotEliminado(array("intTipoPromocionId" => $arrayTipoPromoPlanes[0]['intIdTipoPromo'],
                                                                                 "arrayEstados"       => array('Eliminado')));
            $arrayIdPlanes = [];

            foreach($objTipoPlanProdPromocion as $objPlanProducto)
            {
                $objPlan = $objPlanProducto->getPlanId();
                if($objPlan !== null)
                {
                    array_push($arrayIdPlanes, $objPlan->getId());
                }
            }
            $arrayTipoPromoPlanes[] = array('strDescripcionCaract' => "Planes", 'strValor' => implode(",", $arrayIdPlanes));
        }
        if(!empty($arrayTipoPromoProductos))
        {
            $objTipoPlanProdPromocion = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                       ->getTipoPlanProdPromoNotEliminado(array("intTipoPromocionId" => $arrayTipoPromoProductos[0]['intIdTipoPromo'],
                                                                                "arrayEstados"       => array('Eliminado')));
            $arrayIdProductos = [];

            foreach($objTipoPlanProdPromocion as $objPlanProducto)
            {
                $objProducto = $objPlanProducto->getProductoId();
                if($objProducto !== null)
                {
                    array_push($arrayIdProductos, $objProducto->getId());
                }
            }
            $arrayTipoPromoProductos[] = array('strDescripcionCaract' => "Productos", 'strValor' => implode(",", $arrayIdProductos));
        }
        
        $objAdmiGrupoPromocionOrigenClonado = null;
        if(is_object($objAdmiGrupoPromocion) && $objAdmiGrupoPromocion->getGrupoPromocionId()!=null)
        {
            $objAdmiGrupoPromocionOrigenClonado = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                              ->find($objAdmiGrupoPromocion->getGrupoPromocionId());                
        }

        $arrayRespuesta = array('objAdmiGrupoPromocion'              => $objAdmiGrupoPromocion,
                                'arrayCaracteristicas'               => $arrayCaracteristicas,
                                'arrayTipoPromoMix'                  => $arrayTipoPromoMix,
                                'arrayTipoPromoPlanes'               => $arrayTipoPromoPlanes,
                                'arrayTipoPromoProductos'            => $arrayTipoPromoProductos,
                                'arrayTipoPromoDescTotal'            => $arrayTipoPromoDescTotal,
                                'objAdmiGrupoPromocionOrigenClonado' => $objAdmiGrupoPromocionOrigenClonado,
                                'dateFechaActual'                    => $dateFechaEditar,
                                'strTipoEdicion'                     => $strTipoEdicion
                               );

        return $arrayRespuesta;
    }

    /**
     * editarPromoMensualidad()
     * Función que Edita promoción de Mensualidad
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 15-04-2019    
     * @param array $arrayParametros[               
     *                                'intIdPromocion         => Id del Grupo Promoción  ADMI_GRUPO_PROMOCION
     *                                'arraySectorizacion'    => Arreglo de Sectorización(Jurisdicción,Cantón,Parroquia,Sector/Olt),
     *                                'strNombrePromocion'    => Nombre de Promoción,
     *                                'strIdsEstadoServicio'  => Estados del Servicio separado por caracter(,),
     *                                'strInicioVigencia'     => Fecha de Inicio de Vigencia,
     *                                'strFinVigencia'        => Fecha de Fi de Vigencia,
     *                                'strIdsFormasPago'      => Id's de formas de pago separado por caracter(,)
     *                                'arrayEmisores'         => Arreglo de Emisores,
     *                                'strUsrCreacion'        => Usuario de Creación,
     *                                'strCodEmpresa'         => Código de Empresa,
     *                                'strIpCreacion'         => Ip de Creación,
     *                                'arrayPromoMix'         => Arreglo de Promoción Mix,
     *                                'arrayPromoPlanes'      => Arreglo de Promoción Planes,
     *                                'arrayPromoProductos'   => Arreglo de Promoción Productos,
     *                                'arrayPromoDescTotal'   => Arreglo de Promoción Descuento Total    
     *                              ]  
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.1 27-09-2022 - Se agrega regla PROM_PERM_MIN_CV, utilizada para la Cancelación Voluntaria.
     * 
     * @return $strRespuesta
     */
    public function editarPromoMensualidad($arrayParametros)
    {
        $arraySectorizacion     = $arrayParametros['arraySectorizacion'];
        $intIdPromocion         = $arrayParametros['intIdPromocion'];
        $strNombrePromocion     = $arrayParametros['strNombrePromocion'];
        $strIdsEstadoServicio   = $arrayParametros['strIdsEstadoServicio'];
        $strInicioVigencia      = $arrayParametros['strInicioVigencia'];
        $strFinVigencia         = $arrayParametros['strFinVigencia'];
        $strIdsFormasPago       = $arrayParametros['strIdsFormasPago'];
        $arrayEmisores          = $arrayParametros['arrayEmisores'];
        $strTipoCliente         = $arrayParametros['strTipoCliente'];
        $strPermMinimaCancelVol = $arrayParametros['strPermMinimaCancelVol'];
        $strCodEmpresa          = $arrayParametros['strCodEmpresa'];
        $strUsrUltMod           = $arrayParametros['strUsrUltMod'];
        $strIpUltMod            = $arrayParametros['strIpUltMod'];
        $strFeUltMod            = new \DateTime('now');
        $arrayPromoMix          = $arrayParametros['arrayPromoMix'];
        $arrayPromoPlanes       = $arrayParametros['arrayPromoPlanes'];
        $arrayPromoProductos    = $arrayParametros['arrayPromoProductos'];
        $arrayPromoDescTotal    = $arrayParametros['arrayPromoDescTotal'];
        $strCodigoPromocion     = $arrayParametros['strCodigoPromocion'];
        $strCodigoPromocionIng  = $arrayParametros['strCodigoPromocionIng'];
        

        $this->emcom->beginTransaction();
        try
        {
            $objInfoEmpresaGrupo = $this->emcom->getRepository('schemaBundle:InfoEmpresaGrupo')->find($strCodEmpresa);
            if(!is_object($objInfoEmpresaGrupo))
            {
                throw new \Exception("No se pudo editar Promoción, No se encontró empresa en sesion");
            }

            $objAdmiGrupoPromocion = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->find($intIdPromocion);
            if(!is_object($objAdmiGrupoPromocion))
            {
                throw new \Exception("No se pudo editar la Promoción, No se encontró la Promoción a editar");
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

            $arrayParamSectorizacion['arraySectorizacion'] = array('PROM_JURISDICCION', 
                                                                   'PROM_CANTON',
                                                                   'PROM_PARROQUIA',
                                                                   'PROM_SECTOR',
                                                                   'PROM_ELEMENTO',
                                                                   'PROM_EDIFICIO'
                                                                  );
            $arrayParamSectorizacion['intIdGrupoPromocion'] = $objAdmiGrupoPromocion->getId();
            
            $arrayReglasSectorizacionByGrupo = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                           ->getGrupoPromocionReglaSectorizacion($arrayParamSectorizacion);

            foreach($arrayReglasSectorizacionByGrupo as $objReglasSectorizacionByGrupo)
            {
                $objReglasSectorizacionByGrupo->setEstado('Eliminado');
                $this->emcom->persist($objReglasSectorizacionByGrupo);
            }

            if(isset($arraySectorizacion) && !empty($arraySectorizacion))
            {
                foreach($arraySectorizacion as $objSectorizacion)
                {
                    if($objSectorizacion['intSectorizacion'] === "0")
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
                        $arrayParamReglasJurisdiccion = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                                              'strRegla'            => 'PROM_JURISDICCION',
                                                              'strAccion'           => $strAccion,
                                                              'strValorRegla'       => $objSectorizacion['intJurisdiccion'],
                                                              'strFeCreacion'       => $strFeUltMod,
                                                              'strUsrCreacion'      => $strUsrUltMod,
                                                              'strIpCreacion'       => $strIpUltMod,
                                                              'intNumeroSecuencia'  => $intNumeroSecuencia
                                                             );
                        $this->servicePromocion->actualizarGrupoPromocionReglaSectorizacion($arrayParamReglasJurisdiccion);
                    }

                    if(isset($objSectorizacion['intCanton']) && !empty($objSectorizacion['intCanton'])
                       && $objSectorizacion['intCanton'] !== "0")
                    {
                        $arrayParamReglasCanton = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                                        'strRegla'            => 'PROM_CANTON',
                                                        'strAccion'           => $strAccion,
                                                        'strValorRegla'       => $objSectorizacion['intCanton'],
                                                        'strFeCreacion'       => $strFeUltMod,
                                                        'strUsrCreacion'      => $strUsrUltMod,
                                                        'strIpCreacion'       => $strIpUltMod,
                                                        'intNumeroSecuencia'  => $intNumeroSecuencia
                                                       );
                        $this->servicePromocion->actualizarGrupoPromocionReglaSectorizacion($arrayParamReglasCanton);
                    }

                    if(isset($objSectorizacion['intParroquia']) && !empty($objSectorizacion['intParroquia'])
                       && $objSectorizacion['intParroquia'] !== "0")
                    {
                        $arrayParamReglasParroquia = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                                           'strRegla'            => 'PROM_PARROQUIA',
                                                           'strAccion'           => $strAccion,
                                                           'strValorRegla'       => $objSectorizacion['intParroquia'],
                                                           'strFeCreacion'       => $strFeUltMod,
                                                           'strUsrCreacion'      => $strUsrUltMod,
                                                           'strIpCreacion'       => $strIpUltMod,
                                                           'intNumeroSecuencia'  => $intNumeroSecuencia
                                                          );
                        $this->servicePromocion->actualizarGrupoPromocionReglaSectorizacion($arrayParamReglasParroquia);
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
                        $arrayParamReglasSectorOlt = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                                           'strRegla'            => $strReglaPromocion,
                                                           'strAccion'           => $strAccion,
                                                           'strValorRegla'       => $objSectorizacion['intSectOltEdif'],
                                                           'strFeCreacion'       => $strFeUltMod,
                                                           'strUsrCreacion'      => $strUsrUltMod,
                                                           'strIpCreacion'       => $strIpUltMod,
                                                           'intNumeroSecuencia'  => $intNumeroSecuencia
                                                          );
                        $this->servicePromocion->actualizarGrupoPromocionReglaSectorizacion($arrayParamReglasSectorOlt);
                    }
                }
            }

            $strAccionEstadoServicio        = isset($strIdsEstadoServicio) && !empty($strIdsEstadoServicio) ? 'EDITAR' : 'ELIMINAR';
            $arrayParamReglasEstadoServicio = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                                    'strRegla'            => 'PROM_ESTADO_SERVICIO',
                                                    'strAccion'           => $strAccionEstadoServicio,
                                                    'strValorRegla'       => $strIdsEstadoServicio,
                                                    'strFeCreacion'       => $strFeUltMod,
                                                    'strUsrCreacion'      => $strUsrUltMod,
                                                    'strIpCreacion'       => $strIpUltMod,
                                                   );
            $this->servicePromocion->actualizarGrupoPromocionRegla($arrayParamReglasEstadoServicio);

            $strAccionFormaPago        = isset($strIdsFormasPago) && !empty($strIdsFormasPago) ? 'EDITAR' : 'ELIMINAR';
            $arrayParamReglasFormaPago = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                               'strRegla'            => 'PROM_FORMA_PAGO',
                                               'strAccion'           => $strAccionFormaPago,
                                               'strValorRegla'       => $strIdsFormasPago,
                                               'strFeCreacion'       => $strFeUltMod,
                                               'strUsrCreacion'      => $strUsrUltMod,
                                               'strIpCreacion'       => $strIpUltMod,
                                              );
            $this->servicePromocion->actualizarGrupoPromocionRegla($arrayParamReglasFormaPago);
            
            
            $strAccionPermMinCancelVol        = isset($strPermMinimaCancelVol) && !empty($strPermMinimaCancelVol) ? 'EDITAR' : 'ELIMINAR';
            $arrayParamReglasPermMinCancelVol = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                               'strRegla'            => 'PROM_PERM_MIN_CV',
                                               'strAccion'           => $strAccionPermMinCancelVol,
                                               'strValorRegla'       => $strPermMinimaCancelVol,
                                               'strFeCreacion'       => $strFeUltMod,
                                               'strUsrCreacion'      => $strUsrUltMod,
                                               'strIpCreacion'       => $strIpUltMod,
                                              );
            $this->servicePromocion->actualizarGrupoPromocionRegla($arrayParamReglasPermMinCancelVol);
            
            
            if($strCodigoPromocionIng=='S' && (isset($strCodigoPromocion) && !empty($strCodigoPromocion)))
            {
                $strAccion='EDITAR';
                $arrayParamReglaTipoNegocio = array('intIdGrupoPromocion'  => $objAdmiGrupoPromocion->getId(),
                                                'strRegla'            => 'PROM_CODIGO',
                                                'strAccion'           => $strAccion,
                                                'strValorRegla'       => $strCodigoPromocion,
                                                'strFeCreacion'       => $strFeUltMod,
                                                'strUsrCreacion'      => $strUsrUltMod,
                                                'strIpCreacion'       => $strIpUltMod);
                $this->servicePromocion->actualizarGrupoPromocionRegla($arrayParamReglaTipoNegocio);
           
            }
            else if ($strCodigoPromocionIng=='N' && (isset($strCodigoPromocion) && !empty($strCodigoPromocion)))
            {
                 $arrayParamReglas = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                               'strRegla'            => 'PROM_CODIGO',
                                               'strValorRegla'       => $strCodigoPromocion,
                                               'strFeCreacion'       => $strFeUltMod,
                                               'strUsrCreacion'      => $strUsrUltMod,
                                               'strIpCreacion'       => $strIpUltMod
                                              );
                 $this->servicePromocion->ingresarGrupoPromocionRegla($arrayParamReglas);
            }
            else if ($strCodigoPromocionIng=='S' && empty($strCodigoPromocion))
            {
                $strAccion='ELIMINAR';
                $arrayParamReglaTipoNegocio = array('intIdGrupoPromocion'  => $objAdmiGrupoPromocion->getId(),
                                                'strRegla'            => 'PROM_CODIGO',
                                                'strAccion'           => $strAccion,
                                                'strFeCreacion'       => $strFeUltMod,
                                                'strUsrCreacion'      => $strUsrUltMod,
                                                'strIpCreacion'       => $strIpUltMod);
                $this->servicePromocion->actualizarGrupoPromocionRegla($arrayParamReglaTipoNegocio);
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

                    $arrayParamBancoTipoCuenta = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getBancoTipoCuenta($arrayParametros);
                    $arrayBancoTipoCuenta      = array_merge($arrayBancoTipoCuenta, $arrayParamBancoTipoCuenta);
                }
                foreach($arrayBancoTipoCuenta as $objBancoTipoCuenta)
                {
                    $arrayIdBancoTipoCuenta[] = $objBancoTipoCuenta['idBancoTipoCuenta'];
                }
                $strIdBancoTipoCuenta = implode(",", $arrayIdBancoTipoCuenta);
                $strAccionEmisores    = 'EDITAR';
            }
            else
            {
                $strAccionEmisores    = 'ELIMINAR';
                $strIdBancoTipoCuenta = "";
            }
            $arrayParamReglasEmisores = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                              'strRegla'            => 'PROM_EMISOR',
                                              'strAccion'           => $strAccionEmisores,
                                              'strValorRegla'       => $strIdBancoTipoCuenta,
                                              'strFeCreacion'       => $strFeUltMod,
                                              'strUsrCreacion'      => $strUsrUltMod,
                                              'strIpCreacion'       => $strIpUltMod,
                                             );
            $this->servicePromocion->actualizarGrupoPromocionRegla($arrayParamReglasEmisores);
            
            $strAccionTipoCliente        = isset($strTipoCliente) && !empty($strTipoCliente) ? 'EDITAR' : 'ELIMINAR';
            $arrayParamReglasTipoCliente = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                                 'strRegla'            => 'PROM_TIPO_CLIENTE',
                                                 'strAccion'           => $strAccionTipoCliente,
                                                 'strValorRegla'       => $strTipoCliente,
                                                 'strFeCreacion'       => $strFeUltMod,
                                                 'strUsrCreacion'      => $strUsrUltMod,
                                                 'strIpCreacion'       => $strIpUltMod,
                                                );
            $this->servicePromocion->actualizarGrupoPromocionRegla($arrayParamReglasTipoCliente);

            if(!empty($arrayPromoMix))
            {
                $arrayParamTipoPromocion = array('strTipoPromocion'      => 'Descuento en Mensualidad Mix de Planes y Productos',
                                                 'strCodEmpresa'         => $strCodEmpresa,
                                                 'objAdmiGrupoPromocion' => $objAdmiGrupoPromocion,
                                                 'strFeUltMod'           => $strFeUltMod,
                                                 'strUsrUltMod'          => $strUsrUltMod,
                                                 'strIpUltMod'           => $strIpUltMod
                                                );
                $strAccionPermanenciaMinima = isset($arrayPromoMix['strPermanenciaMinima']) && !empty($arrayPromoMix['strPermanenciaMinima']) ?
                                              'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayPermanenciaMinima'] = array('strPermanenciaMinima'       => $arrayPromoMix['strPermanenciaMinima'],
                                                                            'strAccionPermanenciaMinima' => $strAccionPermanenciaMinima
                                                                           );

                $strAccionTieneMora = isset($arrayPromoMix['strMora']) && !empty($arrayPromoMix['strMora']) ? 'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayTieneMora'] = array('strTieneMora'       => $arrayPromoMix['strMora'],
                                                                    'strAccionTieneMora' => $strAccionTieneMora
                                                                   );
                
                $strAccionValorMora = isset($arrayPromoMix['intValMora']) && !empty($arrayPromoMix['intValMora']) ? 'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayValorMora'] = array('intValMora'         => $arrayPromoMix['intValMora'],
                                                                    'strAccionValorMora' => $strAccionValorMora
                                                                   );

                $strAccionIndefinida = isset($arrayPromoMix['strIndefinida']) && !empty($arrayPromoMix['strIndefinida']) ? 'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayIndefinida'] = array('strIndefinida'       => $arrayPromoMix['strIndefinida'],
                                                                     'strAccionIndefinida' => $strAccionIndefinida
                                                                    );

                $strAccionDescIndefinido = isset($arrayPromoMix['fltDescIndefinido']) && !empty($arrayPromoMix['fltDescIndefinido']) ?
                                           'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayDescIndefinido'] = array('fltDescIndefinido'       => $arrayPromoMix['fltDescIndefinido'],
                                                                         'strAccionDescIndefinido' => $strAccionDescIndefinido
                                                                        );

                $strAccionTipoPeriodo = isset($arrayPromoMix['strTipoPeriodo']) && !empty($arrayPromoMix['strTipoPeriodo']) ? 'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayTipoPeriodo'] = array('strTipoPeriodo'       => $arrayPromoMix['strTipoPeriodo'],
                                                                      'strAccionTipoPeriodo' => $strAccionTipoPeriodo
                                                                     );

                $strAccionDescuentoPeriodo = isset($arrayPromoMix['arrayDescuentoPeriodo']) && !empty($arrayPromoMix['arrayDescuentoPeriodo']) ?
                                             'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayDescuentoPeriodo'] = array('arrayDescuentoPeriodo'     => $arrayPromoMix['arrayDescuentoPeriodo'],
                                                                           'strAccionDescuentoPeriodo' => $strAccionDescuentoPeriodo
                                                                          );

                $arrayParamTipoPromocion ['arrayPlanes'] = array('arrayPlanes' => $arrayPromoMix['arrayPlanes']);

                $arrayParamTipoPromocion ['arrayProductos'] = array('arrayProductos' => $arrayPromoMix['arrayProductos']);

                $this->editarTipoPromocion($arrayParamTipoPromocion);
            }
            else
            {
                $arrayParamTipoPromocion = array('strTipoPromocion'      => 'Descuento en Mensualidad Mix de Planes y Productos',
                                                 'strCodEmpresa'         => $strCodEmpresa,
                                                 'objAdmiGrupoPromocion' => $objAdmiGrupoPromocion,
                                                 'strFeUltMod'           => $strFeUltMod,
                                                 'strUsrUltMod'          => $strUsrUltMod,
                                                 'strIpUltMod'           => $strIpUltMod
                                                );
                $this->servicePromocion->eliminarTipoPromocion($arrayParamTipoPromocion);
            }

            if(!empty($arrayPromoPlanes))
            {
                $arrayParamTipoPromocion = array('strTipoPromocion'      => 'Descuento en Mensualidad de Planes',
                                                 'strCodEmpresa'         => $strCodEmpresa,
                                                 'objAdmiGrupoPromocion' => $objAdmiGrupoPromocion,
                                                 'strFeUltMod'           => $strFeUltMod,
                                                 'strUsrUltMod'          => $strUsrUltMod,
                                                 'strIpUltMod'           => $strIpUltMod
                                                );
                $strAccionPermanenciaMinima = isset($arrayPromoPlanes['strPermanenciaMinima']) && !empty($arrayPromoPlanes['strPermanenciaMinima']) ?
                                              'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayPermanenciaMinima'] = array('strPermanenciaMinima'       => $arrayPromoPlanes['strPermanenciaMinima'],
                                                                            'strAccionPermanenciaMinima' => $strAccionPermanenciaMinima
                                                                           );

                $strAccionTieneMora = isset($arrayPromoPlanes['strMora']) && !empty($arrayPromoPlanes['strMora']) ? 'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayTieneMora'] = array('strTieneMora'       => $arrayPromoPlanes['strMora'],
                                                                    'strAccionTieneMora' => $strAccionTieneMora
                                                                   );
                
                $strAccionValorMora = isset($arrayPromoPlanes['intValMora']) && !empty($arrayPromoPlanes['intValMora']) ? 'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayValorMora'] = array('intValMora'         => $arrayPromoPlanes['intValMora'],
                                                                    'strAccionValorMora' => $strAccionValorMora
                                                                   );

                $strAccionIndefinida = isset($arrayPromoPlanes['strIndefinida']) && !empty($arrayPromoPlanes['strIndefinida']) ? 
                                       'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayIndefinida'] = array('strIndefinida'       => $arrayPromoPlanes['strIndefinida'],
                                                                     'strAccionIndefinida' => $strAccionIndefinida
                                                                    );

                $strAccionDescIndefinido = isset($arrayPromoPlanes['fltDescIndefinido']) && !empty($arrayPromoPlanes['fltDescIndefinido']) ?
                                           'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayDescIndefinido'] = array('fltDescIndefinido'       => $arrayPromoPlanes['fltDescIndefinido'],
                                                                         'strAccionDescIndefinido' => $strAccionDescIndefinido
                                                                        );

                $strAccionTipoPeriodo = isset($arrayPromoPlanes['strTipoPeriodo']) && !empty($arrayPromoPlanes['strTipoPeriodo']) ?
                                        'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayTipoPeriodo'] = array('strTipoPeriodo'       => $arrayPromoPlanes['strTipoPeriodo'],
                                                                      'strAccionTipoPeriodo' => $strAccionTipoPeriodo
                                                                     );

                $strAccionDescuentoPeriodo = isset($arrayPromoPlanes['arrayDescuentoPeriodo']) && !empty($arrayPromoPlanes['arrayDescuentoPeriodo']) ?
                                             'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayDescuentoPeriodo'] = array('arrayDescuentoPeriodo'     => $arrayPromoPlanes['arrayDescuentoPeriodo'],
                                                                           'strAccionDescuentoPeriodo' => $strAccionDescuentoPeriodo
                                                                          );

                $arrayParamTipoPromocion ['arrayPlanes'] = array('arrayPlanes' => $arrayPromoPlanes['arrayPlanes']);

                $this->editarTipoPromocion($arrayParamTipoPromocion);
            }
            else
            {
                $arrayParamTipoPromocion = array('strTipoPromocion'      => 'Descuento en Mensualidad de Planes',
                                                 'strCodEmpresa'         => $strCodEmpresa,
                                                 'objAdmiGrupoPromocion' => $objAdmiGrupoPromocion,
                                                 'strFeUltMod'           => $strFeUltMod,
                                                 'strUsrUltMod'          => $strUsrUltMod,
                                                 'strIpUltMod'           => $strIpUltMod
                                                );

                $this->servicePromocion->eliminarTipoPromocion($arrayParamTipoPromocion);
            }

            if(!empty($arrayPromoProductos))
            {
                $arrayParamTipoPromocion = array('strTipoPromocion'      => 'Descuento en Mensualidad de Productos',
                                                 'strCodEmpresa'         => $strCodEmpresa,
                                                 'objAdmiGrupoPromocion' => $objAdmiGrupoPromocion,
                                                 'strFeUltMod'           => $strFeUltMod,
                                                 'strUsrUltMod'          => $strUsrUltMod,
                                                 'strIpUltMod'           => $strIpUltMod
                                                );
                $strAccionPermanenciaMinima = isset($arrayPromoProductos['strPermanenciaMinima']) &&
                                              !empty($arrayPromoProductos['strPermanenciaMinima']) ? 'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayPermanenciaMinima'] = array('strPermanenciaMinima'   => $arrayPromoProductos['strPermanenciaMinima'],
                                                                            'strAccionPermanenciaMinima' => $strAccionPermanenciaMinima
                                                                           );

                $strAccionTieneMora = isset($arrayPromoProductos['strMora']) && !empty($arrayPromoProductos['strMora']) ? 'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayTieneMora'] = array('strTieneMora'       => $arrayPromoProductos['strMora'],
                                                                    'strAccionTieneMora' => $strAccionTieneMora
                                                                   );
                
                $strAccionValorMora = isset($arrayPromoProductos['intValMora']) && !empty($arrayPromoProductos['intValMora']) ? 'EDITAR':'ELIMINAR';
                $arrayParamTipoPromocion ['arrayValorMora'] = array('intValMora'         => $arrayPromoProductos['intValMora'],
                                                                    'strAccionValorMora' => $strAccionValorMora
                                                                   );

                $strAccionIndefinida = isset($arrayPromoProductos['strIndefinida']) && !empty($arrayPromoProductos['strIndefinida']) ?
                                       'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayIndefinida'] = array('strIndefinida'       => $arrayPromoProductos['strIndefinida'],
                                                                     'strAccionIndefinida' => $strAccionIndefinida
                                                                    );

                $strAccionDescIndefinido = isset($arrayPromoProductos['fltDescIndefinido']) && !empty($arrayPromoProductos['fltDescIndefinido']) ?
                                           'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayDescIndefinido'] = array('fltDescIndefinido'       => $arrayPromoProductos['fltDescIndefinido'],
                                                                         'strAccionDescIndefinido' => $strAccionDescIndefinido
                                                                        );

                $strAccionTipoPeriodo = isset($arrayPromoProductos['strTipoPeriodo']) && !empty($arrayPromoProductos['strTipoPeriodo']) ?
                                        'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayTipoPeriodo'] = array('strTipoPeriodo'       => $arrayPromoProductos['strTipoPeriodo'],
                                                                      'strAccionTipoPeriodo' => $strAccionTipoPeriodo
                                                                     );

                $strAccionDescuentoPeriodo = isset($arrayPromoProductos['arrayDescuentoPeriodo']) &&
                                             !empty($arrayPromoProductos['arrayDescuentoPeriodo']) ? 'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayDescuentoPeriodo'] = array('arrayDescuentoPeriodo' => $arrayPromoProductos['arrayDescuentoPeriodo'],
                                                                           'strAccionDescuentoPeriodo' => $strAccionDescuentoPeriodo
                                                                          );

                $arrayParamTipoPromocion ['arrayProductos'] = array('arrayProductos' => $arrayPromoProductos['arrayProductos']);

                $this->editarTipoPromocion($arrayParamTipoPromocion);
            }
            else
            {
                $arrayParamTipoPromocion = array('strTipoPromocion'      => 'Descuento en Mensualidad de Productos',
                                                 'strCodEmpresa'         => $strCodEmpresa,
                                                 'objAdmiGrupoPromocion' => $objAdmiGrupoPromocion,
                                                 'strFeUltMod'           => $strFeUltMod,
                                                 'strUsrUltMod'          => $strUsrUltMod,
                                                 'strIpUltMod'           => $strIpUltMod
                                                );
                $this->servicePromocion->eliminarTipoPromocion($arrayParamTipoPromocion);
            }

            if(!empty($arrayPromoDescTotal))
            {
                $arrayParamTipoPromocion = array('strTipoPromocion'      => 'Descuento Total en Mensualidad',
                                                 'strCodEmpresa'         => $strCodEmpresa,
                                                 'objAdmiGrupoPromocion' => $objAdmiGrupoPromocion,
                                                 'strFeUltMod'           => $strFeUltMod,
                                                 'strUsrUltMod'          => $strUsrUltMod,
                                                 'strIpUltMod'           => $strIpUltMod
                                                );


                $strAccionTipoPeriodo = isset($arrayPromoDescTotal['strTipoPeriodo']) && !empty($arrayPromoDescTotal['strTipoPeriodo']) ?
                                        'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayTipoPeriodo'] = array('strTipoPeriodo'       => $arrayPromoDescTotal['strTipoPeriodo'],
                                                                      'strAccionTipoPeriodo' => $strAccionTipoPeriodo
                );

                $strAccionDescuentoPeriodo = isset($arrayPromoDescTotal['arrayDescuentoPeriodo']) &&
                                             !empty($arrayPromoDescTotal['arrayDescuentoPeriodo']) ? 'EDITAR' : 'ELIMINAR';
                $arrayParamTipoPromocion ['arrayDescuentoPeriodo'] = array('arrayDescuentoPeriodo' => $arrayPromoDescTotal['arrayDescuentoPeriodo'],
                                                                           'strAccionDescuentoPeriodo' => $strAccionDescuentoPeriodo
                                                                          );
                $this->editarTipoPromocion($arrayParamTipoPromocion);
            }
            else
            {
                $arrayParamTipoPromocion = array('strTipoPromocion'      => 'Descuento Total en Mensualidad',
                                                 'strCodEmpresa'         => $strCodEmpresa,
                                                 'objAdmiGrupoPromocion' => $objAdmiGrupoPromocion,
                                                 'strFeUltMod'           => $strFeUltMod,
                                                 'strUsrUltMod'          => $strUsrUltMod,
                                                 'strIpUltMod'           => $strIpUltMod
                                                );
                $this->servicePromocion->eliminarTipoPromocion($arrayParamTipoPromocion);
            }

            $arrayParamPromoGrupoHisto = array('intIdGrupoPromocion' => $objAdmiGrupoPromocion->getId(),
                                               'strFeCreacion'       => $strFeUltMod,
                                               'strUsrCreacion'      => $strUsrUltMod,
                                               'strIpCreacion'       => $strIpUltMod,
                                               'strObservacion'      => 'Se Editó Promoción Descuento en Mensualidad',
                                               'strEstado'           => $objAdmiGrupoPromocion->getEstado()
                                              );
            $this->servicePromocion->ingresarPromocionGrupoHisto($arrayParamPromoGrupoHisto);
            $this->emcom->getConnection()->commit();
            $strRespuesta = 'OK';
        }
        catch(\Exception $e)
        {
            $this->emcom->rollback();
            $this->emcom->close();
            $strRespuesta = "No se pudo editar la Promoción de Mensualidad. Favor notificar a Sistemas.";
            $this->serviceUtil->insertError('Telcos+', 
                                            'PromocionMensualidasService.editarPromoMensualidad',
                                            'Error PromocionMensualidasService.guardarPromoMensualidad:' . $e->getMessage(),
                                            $strUsrUltMod, 
                                            $strIpUltMod
                                           );
            return $strRespuesta;
        }
        return $strRespuesta;
    }
    
    /**
     * editarTipoPromocion()
     * Función que edita las reglas para cada Tipo de Promoción(Promo Mix, Planes, Productos, Desc.Total)
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 18-04-2019    
     * @param array $arrayParametros[               
     *                                'objAdmiGrupoPromocion   => Objeto del Grupo Promoción  ADMI_GRUPO_PROMOCION
     *                                'strTipoPromocion'       => Tipo de Promoción(Mix, Planes, Productos, Desc.Total),
     *                                'strCodEmpresa'          => Código de la Empresa,
     *                                'strFeUltMod'            => Fecha de Edición,
     *                                'strUsrUltMod'           => Usuario de Edición,
     *                                'strIpUltMod'            => Ip de Edición,
     *                                'arrayPermanenciaMinima' => Arreglo con detalle de Regla Permanencia Mínima
     *                                'arrayTieneMora'         => Arreglo con detalle de Regla de Tiene Mora
     *                                'arrayIndefinida'        => Arreglo con detalle de Regla Promoción Indefinida
     *                                'arrayDescIndefinido'    => Arreglo con detalle de Regla Descuento Indefinido
     *                                'arrayTipoPeriodo'       => Arreglo con detalle de Regla Tipo de Período
     *                                'arrayDescuentoPeriodo'  => Arreglo con detalle de Regla Descuento Período
     *                                'arrayPlanes'            => Arreglo con detalle de Planes 
     *                                'arrayProductos'         => Arreglo con detalle de Productos
     *                              ]  
     */
    public function editarTipoPromocion($arrayParamTipoPromocion)
    {
        
        $arrayParamTipoPromo = array('intGrupoPromocionId' => $arrayParamTipoPromocion['objAdmiGrupoPromocion']->getId(),
                                     'strTipoPromocion'    => $arrayParamTipoPromocion['strTipoPromocion'],
                                     'arrayEstados'        => array('Eliminado')
                                    );
        $objAdmiTipoPromocion = $this->emcom->getRepository("schemaBundle:AdmiGrupoPromocion")->getTipoPromocionEstado($arrayParamTipoPromo);  
       
        if(!is_object($objAdmiTipoPromocion))
        {
            $arrayParametros = array('strTipoPromocion' => $arrayParamTipoPromocion['strTipoPromocion'],
                                     'strCodEmpresa'    => $arrayParamTipoPromocion['strCodEmpresa']
                                    );
            $arrayParametrosPromocion = $this->servicePromocion->obtenerParametrosPromocion($arrayParametros);
            $strTipoPromocion         = $arrayParametrosPromocion['strTipoPromocion'];
            $strCodTipoPromocion      = $arrayParametrosPromocion['strCodTipoPromocion'];

            $objAdmiTipoPromocion = new AdmiTipoPromocion();
            $objAdmiTipoPromocion->setGrupoPromocionId($arrayParamTipoPromocion['objAdmiGrupoPromocion']);
            $objAdmiTipoPromocion->setCodigoTipoPromocion($strCodTipoPromocion);
            $objAdmiTipoPromocion->setTipo($strTipoPromocion);
            $objAdmiTipoPromocion->setFeCreacion($arrayParamTipoPromocion['strFeUltMod']);
            $objAdmiTipoPromocion->setUsrCreacion($arrayParamTipoPromocion['strUsrUltMod']);
            $objAdmiTipoPromocion->setIpCreacion($arrayParamTipoPromocion['strIpUltMod']);
            $objAdmiTipoPromocion->setEstado('Activo');
            $this->emcom->persist($objAdmiTipoPromocion);
            $this->emcom->flush();
        }else
        {
            $objAdmiTipoPromocion->setFeUltMod($arrayParamTipoPromocion['strFeUltMod']);
            $objAdmiTipoPromocion->setUsrUltMod($arrayParamTipoPromocion['strUsrUltMod']);
            $objAdmiTipoPromocion->setIpUltMod($arrayParamTipoPromocion['strIpUltMod']);
            if($objAdmiTipoPromocion->getEstado()=='Pendiente')
            {
                $objAdmiTipoPromocion->setEstado('Activo');
            }
        }

        if(isset($arrayParamTipoPromocion['arrayPermanenciaMinima']) && !empty($arrayParamTipoPromocion['arrayPermanenciaMinima']))
        {
            $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                      'strRegla'           => 'PROM_PERMANENCIA_MINIMA',
                                      'strAccion'          => $arrayParamTipoPromocion['arrayPermanenciaMinima']['strAccionPermanenciaMinima'],
                                      'strValorRegla'      => $arrayParamTipoPromocion['arrayPermanenciaMinima']['strPermanenciaMinima'],
                                      'strFeCreacion'      => $arrayParamTipoPromocion['strFeUltMod'],
                                      'strUsrCreacion'     => $arrayParamTipoPromocion['strUsrUltMod'],
                                      'strIpCreacion'      => $arrayParamTipoPromocion['strIpUltMod']
                                     );
            $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglas);
        }

        if(isset($arrayParamTipoPromocion['arrayTieneMora']) && !empty($arrayParamTipoPromocion['arrayTieneMora']))
        {
            $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                      'strRegla'           => 'PROM_PIERDE_PROMOCION_MORA',
                                      'strAccion'          => $arrayParamTipoPromocion['arrayTieneMora']['strAccionTieneMora'],
                                      'strValorRegla'      => $arrayParamTipoPromocion['arrayTieneMora']['strTieneMora'],
                                      'strFeCreacion'      => $arrayParamTipoPromocion['strFeUltMod'],
                                      'strUsrCreacion'     => $arrayParamTipoPromocion['strUsrUltMod'],
                                      'strIpCreacion'      => $arrayParamTipoPromocion['strIpUltMod']
                                     );
            $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglas);
        }
        
        if(isset($arrayParamTipoPromocion['arrayValorMora']) && !empty($arrayParamTipoPromocion['arrayValorMora']))
        {
            $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                      'strRegla'           => 'PROM_DIAS_MORA',
                                      'strAccion'          => $arrayParamTipoPromocion['arrayValorMora']['strAccionValorMora'],
                                      'strValorRegla'      => $arrayParamTipoPromocion['arrayValorMora']['intValMora'],
                                      'strFeCreacion'      => $arrayParamTipoPromocion['strFeUltMod'],
                                      'strUsrCreacion'     => $arrayParamTipoPromocion['strUsrUltMod'],
                                      'strIpCreacion'      => $arrayParamTipoPromocion['strIpUltMod']
                                     );
            $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglas);
        }

        if(isset($arrayParamTipoPromocion['arrayIndefinida']) && !empty($arrayParamTipoPromocion['arrayIndefinida']))
        {
            $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                      'strRegla'           => 'PROM_PROMOCION_INDEFINIDA',
                                      'strAccion'          => $arrayParamTipoPromocion['arrayIndefinida']['strAccionIndefinida'],
                                      'strValorRegla'      => $arrayParamTipoPromocion['arrayIndefinida']['strIndefinida'],
                                      'strFeCreacion'      => $arrayParamTipoPromocion['strFeUltMod'],
                                      'strUsrCreacion'     => $arrayParamTipoPromocion['strUsrUltMod'],
                                      'strIpCreacion'      => $arrayParamTipoPromocion['strIpUltMod']
                                     );
            $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglas);
        }

        if(isset($arrayParamTipoPromocion['arrayDescIndefinido']) && !empty($arrayParamTipoPromocion['arrayDescIndefinido']))
        {
            $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                      'strRegla'           => 'PROM_DESCUENTO',
                                      'strAccion'          => $arrayParamTipoPromocion['arrayDescIndefinido']['strAccionDescIndefinido'],
                                      'strValorRegla'      => $arrayParamTipoPromocion['arrayDescIndefinido']['fltDescIndefinido'],
                                      'strFeCreacion'      => $arrayParamTipoPromocion['strFeUltMod'],
                                      'strUsrCreacion'     => $arrayParamTipoPromocion['strUsrUltMod'],
                                      'strIpCreacion'      => $arrayParamTipoPromocion['strIpUltMod']
                                     );
            $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglas);
        }

        if(isset($arrayParamTipoPromocion['arrayTipoPeriodo']) && !empty($arrayParamTipoPromocion['arrayTipoPeriodo']))
        {
            $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                      'strRegla'           => 'PROM_TIPO_PERIODO',
                                      'strAccion'          => $arrayParamTipoPromocion['arrayTipoPeriodo']['strAccionTipoPeriodo'],
                                      'strValorRegla'      => $arrayParamTipoPromocion['arrayTipoPeriodo']['strTipoPeriodo'],
                                      'strFeCreacion'      => $arrayParamTipoPromocion['strFeUltMod'],
                                      'strUsrCreacion'     => $arrayParamTipoPromocion['strUsrUltMod'],
                                      'strIpCreacion'      => $arrayParamTipoPromocion['strIpUltMod']
                                     );
            $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglas);
        }

        if(isset($arrayParamTipoPromocion['arrayDescuentoPeriodo']) && !empty($arrayParamTipoPromocion['arrayDescuentoPeriodo']))
        {
            $strDescuentoPeriodo = "";
            if(isset($arrayParamTipoPromocion['arrayDescuentoPeriodo']['arrayDescuentoPeriodo']) &&
              !empty($arrayParamTipoPromocion['arrayDescuentoPeriodo']['arrayDescuentoPeriodo']))
            {
                $strDescuentoPeriodo = implode(",", $arrayParamTipoPromocion['arrayDescuentoPeriodo']['arrayDescuentoPeriodo']);
            }
            
            $arrayParamReglas = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                      'strRegla'           => 'PROM_PERIODO',
                                      'strAccion'          => $arrayParamTipoPromocion['arrayDescuentoPeriodo']['strAccionDescuentoPeriodo'],
                                      'strValorRegla'      => $strDescuentoPeriodo,
                                      'strFeCreacion'      => $arrayParamTipoPromocion['strFeUltMod'],
                                      'strUsrCreacion'     => $arrayParamTipoPromocion['strUsrUltMod'],
                                      'strIpCreacion'      => $arrayParamTipoPromocion['strIpUltMod']
                                     );
            $this->servicePromocion->actualizarTipoPromocionRegla($arrayParamReglas);
        }

        if(isset($arrayParamTipoPromocion['arrayPlanes']) && !empty($arrayParamTipoPromocion['arrayPlanes']))
        {
            $arrayAdmiTipoPlanProdPromoPlanes = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                     ->getTipoPlanProdPromoNotNull(array('intTipoPromocionId' => $objAdmiTipoPromocion->getId(),
                                                                                         'strTipoPlanProd'    => 'PLAN'));

            foreach($arrayAdmiTipoPlanProdPromoPlanes as $objAdmiTipoPlanProdPromoPlanesEliminar)
            {
                $objAdmiTipoPlanProdPromoPlanesEliminar->setEstado('Eliminado');
                $this->emcom->persist($objAdmiTipoPlanProdPromoPlanesEliminar);
            }
            foreach($arrayParamTipoPromocion['arrayPlanes']['arrayPlanes'] as $plan)
            {
                $arrayParametros = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                         'intIdPlan'          => $plan,
                                         'strFeUltMod'        => $arrayParamTipoPromocion['strFeUltMod'],
                                         'strUsrUltMod'       => $arrayParamTipoPromocion['strUsrUltMod'],
                                         'strIpUltMod'        => $arrayParamTipoPromocion['strIpUltMod']
                                        );
                $this->servicePromocion->editarPlanProd($arrayParametros);
            }
        }

        if(isset($arrayParamTipoPromocion['arrayProductos']) && !empty($arrayParamTipoPromocion['arrayProductos']))
        {
            $arrayAdmiTipoPlanProdPromoProd = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                          ->getTipoPlanProdPromoNotNull(array('intTipoPromocionId' => $objAdmiTipoPromocion->getId(),
                                                                                              'strTipoPlanProd'    => 'PROD'));

            foreach($arrayAdmiTipoPlanProdPromoProd as $objAdmiTipoPlanProdPromoProdEliminar)
            {
                $objAdmiTipoPlanProdPromoProdEliminar->setEstado('Eliminado');
                $this->emcom->persist($objAdmiTipoPlanProdPromoProdEliminar);
                $this->emcom->flush();
            }

            foreach($arrayParamTipoPromocion['arrayProductos']['arrayProductos'] as $producto)
            {
                $arrayParametros = array('intIdTipoPromocion' => $objAdmiTipoPromocion->getId(),
                                         'intIdProducto'      => $producto,
                                         'strFeUltMod'        => $arrayParamTipoPromocion['strFeUltMod'],
                                         'strUsrUltMod'       => $arrayParamTipoPromocion['strUsrUltMod'],
                                         'strIpUltMod'        => $arrayParamTipoPromocion['strIpUltMod']
                                        );
                $this->servicePromocion->editarPlanProd($arrayParametros);
            }
        }
    }
    
    /**
     * getEmisoresPromoMensualidad()
     * Función que obtiene los emisores de la Promoción de Mensualidad
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 17-04-2019    
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.1 27-09-2021 - Se agrega función para obtener las reglas promocionales de Emisores. 
     * 
     * @param array $arrayParametros
     */
    public function getEmisoresPromoMensualidad($arrayParametros)
    {
        $objEmisorReglaPromo   = $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getEmisorReglaPromoMensualidad($arrayParametros);
        $arrayEmisorReglaPromo = explode(",",$objEmisorReglaPromo[0]['valorRegla']);
        
        $arrayParametros ['arrayEmisorReglaPromo'] = $arrayEmisorReglaPromo;
        
        return $this->emcom->getRepository('schemaBundle:AdmiGrupoPromocion')->getEmisoresPromoMensualidad($arrayParametros);
    }

}
