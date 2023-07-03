<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use telconet\schemaBundle\Entity\InfoPagoCab;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoPagoCabRepository extends EntityRepository
{
    /**
     * Documentación para getNumeroComprobante
     * 
     * Función que obtiene el número del comprobante asociado al detalle del pago
     * 
     * @param array $arrayParametros['intIdPagoDet'       => 'Id del detalle del pago',
     *                               'strCodigoFormaPago' => 'Código de la forma de pago']
     * @return String $strNumeroComprobante
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 11-01-2017
     */
    public function getNumeroComprobante($arrayParametros)
    {
        $strNumeroComprobante = '';
        
        try
        {
            if( !empty($arrayParametros) )
            {
                $intIdPagoDet       = ( isset($arrayParametros["intIdPagoDet"]) ? ( !empty($arrayParametros["intIdPagoDet"]) 
                                        ? $arrayParametros["intIdPagoDet"] : 0 ) : 0 );
                $strCodigoFormaPago = ( isset($arrayParametros["strCodigoFormaPago"]) ? ( !empty($arrayParametros["strCodigoFormaPago"]) 
                                        ? $arrayParametros["strCodigoFormaPago"] : "" ) : "" );
            
                $strNumeroComprobante = str_pad($strNumeroComprobante, 50, " ");
                
                $strSql = "BEGIN :strNumeroComprobante := DB_FINANCIERO.FNCK_CONSULTS.F_GET_NUMERO_COMPROBANTE(  :intIdPagoDet, ".
                                                                                                                ":strCodigoFormaPago ); END;";
                
                $stmt = $this->_em->getConnection()->prepare($strSql);
                $stmt->bindParam('intIdPagoDet',         $intIdPagoDet);
                $stmt->bindParam('strCodigoFormaPago',   $strCodigoFormaPago);
                $stmt->bindParam('strNumeroComprobante', $strNumeroComprobante);
                $stmt->execute();
            }//( !empty($arrayParametros) )
        }
        catch(\Exception $ex)
        {
           throw($ex);
        }
        
        return $strNumeroComprobante;
    }
    
    
    /* ******************************************************************************* */
    /* *********************  BUSQUEDA AVANZADA FINANCIERA **************************** */
    /* ******************************************************************************* */
    
    /**
     * Documentacion para la funcion findBusquedaAvanzadaFinanciera
     *
     * Función que retorna el listado de documentos financieros según criterios enviados como  parámetros.
     * @param mixed   $arrayVariables => criterios de búsqueda para realizar consulta.
     * @param integer $empresaId      => empresaId  de la empresa que se encuentra en sesión.
     * @param integer $oficinaId      => oficinaId  de la oficina que se encuentra en sesión.
     * @param integer $start          => rango inicial para realizar consulta.     
     * @param integer $limit          => rango final para realizar consulta.    
     *                               
     * @return array resultado
     *
     * @author telcos
     * @version 1.0
     * 
     * @author  Edgar Holguin <eholguin@telconet.ec>
     * @version 1.1 24-10-2016 Se elimina filtro y consulta de usr vendedor.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 19-12-2016 - Se agregan las variables '$strFinPagFechaContabilizacionDesde', '$strFinPagFechaContabilizacionHasta' para 
     *                           realizar la búsqueda por fechas con los cuales se contabilizan los documentos del departamento de cobranzas
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 11-01-2017 - Se agrega al select de los pagos que retorne la columna 'fp.codigoFormaPago' que corresponde al codigo de la forma
     *                           de pago asociada al detalle del pago.
     *
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.4 20-07-2017 - Se agregan el filtro Estado del Punto en el query principal para los doc financieros.
     *                           Costo del query principal es de 56928, utilizando los filtros: FE_CREACION, ID_FORMA_PAGO, ESTADO y EMPRESA_ID
     * @author : Kevin Baque <kbaque@telconet.ec>
     * @version 1.5 31-12-2018 Se agrega en la consulta el usuario en sesion, IdPersonEmpresaRol
     *                         Adicional se agrega logica para retornar la info. de acuerdo
     *                         a la caracteristica de la persona en sesion por medio de las siguiente 
     *                         descripciones de caracteristica:
     *                         'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO'
     *                         Estos cambios solo aplican para Telconet
     */
    public function findBusquedaAvanzadaFinanciera($arrayVariables, $empresaId, $oficinaId, $start, $limit){	
        $whereVar = "";
        $fromAdicional = "";
        $whereAdicional = "";
		
		$tipoDocumento = '';
        $strTipo               = ( isset($arrayVariables['strTipoPersonal']) && !empty($arrayVariables['strTipoPersonal']) )
                                   ? $arrayVariables['strTipoPersonal'] : 'Otros';
        $strPrefijoEmpresa     = ( isset($arrayVariables['strPrefijoEmpresa']) && !empty($arrayVariables['strPrefijoEmpresa']) )
                                   ? $arrayVariables['strPrefijoEmpresa'] : '';
        $intIdPersonEmpresaRol = $arrayVariables['intIdPersonEmpresaRol'] ? intval($arrayVariables['intIdPersonEmpresaRol']) : 0;
        $strSubQuery           = "";
        if( ($strPrefijoEmpresa == 'TN' && $strTipo !== 'Otros' ) && ( $strTipo !=='GERENTE_VENTAS' && !empty($intIdPersonEmpresaRol)) )
        {
            if( $strTipo == 'SUBGERENTE' )
            {
                $strSubQuery = " AND pun.usrVendedor IN
                                (SELECT ipvend.login
                                    FROM schemaBundle:InfoPersona ipvend ,
                                    schemaBundle:InfoPersonaEmpresaRol ipervend
                                WHERE ipervend.estado                        = 'Activo'
                                    AND ipervend.personaId                   = ipvend.id
                                    AND ipvend.estado                        = 'Activo'
                                    AND (ipervend.reportaPersonaEmpresaRolId = ".$intIdPersonEmpresaRol."
                                    OR ipervend.id                           = ".$intIdPersonEmpresaRol."))
                              ";
            }
            elseif( $strTipo == 'ASISTENTE' )
            {
                $strSubQuery = " AND pun.usrVendedor IN
                                (select ipvend.login
                                    from schemaBundle:InfoPersonaEmpresaRolCarac ipercvend ,
                                      schemaBundle:AdmiCaracteristica acvend ,
                                      schemaBundle:InfoPersona ipvend
                                WHERE ipercvend.personaEmpresaRolId          = ".$intIdPersonEmpresaRol."
                                        and acvend.id                        = ipercvend.caracteristicaId
                                        and ipvend.id                        = ipercvend.valor
                                        AND acvend.descripcionCaracteristica = 'ASISTENTE_POR_CARGO'
                                        AND acvend.estado                    = 'Activo'
                                        AND ipercvend.estado                 = 'Activo'
                                        AND ipvend.estado                    = 'Activo')
                              ";
            }
            elseif( $strTipo == 'VENDEDOR' )
            {
                $strSubQuery = " AND pun.usrVendedor IN
                                (select ipvend.login
                                    FROM schemaBundle:InfoPersona ipvend ,
                                    schemaBundle:InfoPersonaEmpresaRol ipervend
                                WHERE ipervend.id          = ".$intIdPersonEmpresaRol."
                                    AND ipervend.personaId = ipvend.id
                                    AND ipervend.estado    = 'Activo'
                                    AND ipvend.estado      = 'Activo')
                              ";
            }
        }
                if($arrayVariables && count($arrayVariables)>0)
        {
            if(isset($arrayVariables["fin_tipoDocumento"]))
            {
                if($arrayVariables["fin_tipoDocumento"] && $arrayVariables["fin_tipoDocumento"]!="" && $arrayVariables["fin_tipoDocumento"]!="0")
                {
					$tipoDocumento = $arrayVariables["fin_tipoDocumento"];
                    $whereVar .= "AND lower(atdf.codigoTipoDocumento) = lower('".trim($arrayVariables["fin_tipoDocumento"])."') ";
                }
            }
			
            if($tipoDocumento == 'PAG' || $tipoDocumento == 'PAGC' || $tipoDocumento == 'ANT' || $tipoDocumento == 'ANTC')
            {
                    if(isset($arrayVariables["login"]))
                    {
                            if($arrayVariables["login"] && $arrayVariables["login"]!="")
                            {
                                    $whereVar .= "AND UPPER(pun.login) like '%".strtoupper(trim($arrayVariables["login"]))."%' ";
                            }
                    }

                    if(isset($arrayVariables["direccion_pto"]))
                    {
                            if($arrayVariables["direccion_pto"] && $arrayVariables["direccion_pto"]!="")
                            {
                                    $whereVar .= "AND UPPER(pun.direccion) like '%".strtoupper(trim($arrayVariables["direccion_pto"]))."%' ";
                            }
                    }

                    if(isset($arrayVariables["descripcion_pto"]))
                    {
                            if($arrayVariables["descripcion_pto"] && $arrayVariables["descripcion_pto"]!="")
                            {
                                    $whereVar .= "AND UPPER(pun.descripcionPunto) like '%".strtoupper(trim($arrayVariables["descripcion_pto"]))."%' ";
                            }
                    }

                    if(isset($arrayVariables["estados_pto"]))
                    {
                            if($arrayVariables["estados_pto"] && $arrayVariables["estados_pto"]!="" && $arrayVariables["estados_pto"]!="0")
                            {
                                    $whereVar .= "AND UPPER(pun.estado) = '".strtoupper(trim($arrayVariables["estados_pto"]))."' ";
                            }
                    }

                    if(isset($arrayVariables["negocios_pto"]))
                    {
                            if($arrayVariables["negocios_pto"] && $arrayVariables["negocios_pto"]!="" && $arrayVariables["negocios_pto"]!="0")
                            {
                                    $whereVar .= "AND pun.tipoNegocioId = '".trim($arrayVariables["negocios_pto"])."' ";
                            }
                    }

                    if(isset($arrayVariables["vendedor"]))
                    {
                            if($arrayVariables["vendedor"] && $arrayVariables["vendedor"]!="")
                            {
                                    $whereVar .= "AND CONCAT(LOWER(peVend.nombres),CONCAT(' ',LOWER(peVend.apellidos))) like '%".strtolower(trim($arrayVariables["vendedor"]))."%' ";
                            }
                    }

                    if(isset($arrayVariables["identificacion"]))
                    {
                            if($arrayVariables["identificacion"] && $arrayVariables["identificacion"]!="")
                            {
                                    $whereVar .= "AND per.identificacionCliente = '".trim($arrayVariables["identificacion"])."' ";
                            }
                    }

                    if(isset($arrayVariables["nombre"]))
                    {
                            if($arrayVariables["nombre"] && $arrayVariables["nombre"]!="")
                            {
                                    $whereVar .= "AND UPPER(per.nombres) like '%".strtoupper(trim($arrayVariables["nombre"]))."%' ";
                            }
                    }

                    if(isset($arrayVariables["apellido"]))
                    {
                            if($arrayVariables["apellido"] && $arrayVariables["apellido"]!="")
                            {
                                    $whereVar .= "AND UPPER(per.apellidos) like '%".strtoupper(trim($arrayVariables["apellido"]))."%' ";
                            }
                    }

                    if(isset($arrayVariables["razon_social"]))
                    {
                            if($arrayVariables["razon_social"] && $arrayVariables["razon_social"]!="")
                            {
                                    $whereVar .= "AND UPPER(per.razonSocial) like '%".strtoupper(trim($arrayVariables["razon_social"]))."%' ";
                            }
                    }

                    if(isset($arrayVariables["direccion_grl"]))
                    {
                            if($arrayVariables["direccion_grl"] && $arrayVariables["direccion_grl"]!="")
                            {
                                    $whereVar .= "AND UPPER(per.direccion) like '%".strtoupper(trim($arrayVariables["direccion_grl"]))."%' ";
                            }
                    }

                    if(isset($arrayVariables["es_edificio"]) || isset($arrayVariables["depende_edificio"]))
                    {
                            $boolPDA = false;
                            if($arrayVariables["es_edificio"] && $arrayVariables["es_edificio"]!="" && $arrayVariables["es_edificio"]!="0")
                            {
                                    $boolPDA = true;
                                    $whereVar .= "AND pda.esEdificio = '".trim($arrayVariables["es_edificio"])."' ";
                            }
                            if($arrayVariables["depende_edificio"] && $arrayVariables["depende_edificio"]!="" && $arrayVariables["depende_edificio"]!="0")
                            {
                                    $boolPDA = true;
                                    $whereVar .= "AND pda.dependeDeEdificio = '".trim($arrayVariables["depende_edificio"])."' ";
                            }

                            if($boolPDA)
                            {
                                    $fromAdicional .= ", schemaBundle:InfoPuntoDatoAdicional pda ";
                                    $whereAdicional .= "AND pun.id = pda.puntoId ";
                            }
                    }
            }//fin if de TIPODOCUMENTO NO ANTS
			
            if($tipoDocumento == 'PAG' || $tipoDocumento == 'ANT' || $tipoDocumento == 'ANTS' || $tipoDocumento == 'ANTC' || $tipoDocumento == 'PAGC')
            {
                    if(isset($arrayVariables["pag_numDocumento"]))
                    {
                            if($arrayVariables["pag_numDocumento"] && $arrayVariables["pag_numDocumento"]!="" && $arrayVariables["pag_numDocumento"]!="0")
                            {
                                    $whereVar .= "AND ipc.numeroPago like '%".trim($arrayVariables["pag_numDocumento"])."%' ";
                            }
                    }
                    if(isset($arrayVariables["pag_numReferencia"]))
                    {
                            if($arrayVariables["pag_numReferencia"] && $arrayVariables["pag_numReferencia"]!="" && $arrayVariables["pag_numReferencia"]!="0")
                            {
                                    $whereVar .= "AND 	(
                                                                                    ipd.numeroCuentaBanco like '%".trim($arrayVariables["pag_numReferencia"])."%' OR
                                                                                    ipd.numeroReferencia like '%".trim($arrayVariables["pag_numReferencia"])."%' 
                                                                            ) ";
                            }
                    }
                    if(isset($arrayVariables["pag_creador"]))
                    {
                            if($arrayVariables["pag_creador"] && $arrayVariables["pag_creador"]!="" && $arrayVariables["pag_creador"]!="0")
                            {
                                    $whereVar .= "AND lower(ipd.usrCreacion) like lower('%".trim($arrayVariables["pag_creador"])."%') ";
                            }
                    }
                    if(isset($arrayVariables["pag_estado"]))
                    {
                            if($arrayVariables["pag_estado"] && $arrayVariables["pag_estado"]!="" && $arrayVariables["pag_estado"]!="0")
                            {
                                    $whereVar .= "AND lower(ipd.estado) like lower('".trim($arrayVariables["pag_estado"])."') ";
                            }
                    }

                    $pag_fechaCreacionDesde = (isset($arrayVariables["pag_fechaCreacionDesde"]) ? $arrayVariables["pag_fechaCreacionDesde"] : 0);
                    $pag_fechaCreacionHasta = (isset($arrayVariables["pag_fechaCreacionHasta"]) ? $arrayVariables["pag_fechaCreacionHasta"] : 0);
                    if($pag_fechaCreacionDesde && $pag_fechaCreacionDesde!="0")
                    {
                            $dateF = explode("-",$pag_fechaCreacionDesde);
                            $pag_fechaCreacionDesde = date("Y/m/d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0]));
                    }
                    if($pag_fechaCreacionHasta && $pag_fechaCreacionHasta!="0")
                    {
                            $dateF = explode("-",$pag_fechaCreacionHasta);
                            $fechaSqlAdd = strtotime(date("Y-m-d", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0])). " +1 day");
                            $pag_fechaCreacionHasta = date("Y/m/d", $fechaSqlAdd);
                    }
                    if($pag_fechaCreacionDesde && $pag_fechaCreacionDesde!="0"){  $whereVar .= "AND ipd.feCreacion >= '".trim($pag_fechaCreacionDesde)."' "; }
                    if($pag_fechaCreacionHasta && $pag_fechaCreacionHasta!="0") { $whereVar .= "AND ipd.feCreacion < '".trim($pag_fechaCreacionHasta)."' ";   }	

                    
                    $strFinPagFechaContabilizacionDesde = ( ( isset($arrayVariables["strFinPagFechaContabilizacionDesde"]) 
                                                              && !empty($arrayVariables["strFinPagFechaContabilizacionDesde"]) ) 
                                                              ? $arrayVariables["strFinPagFechaContabilizacionDesde"] : "" );
                    $strFinPagFechaContabilizacionHasta = ( ( isset($arrayVariables["strFinPagFechaContabilizacionHasta"]) 
                                                              && !empty($arrayVariables["strFinPagFechaContabilizacionHasta"]) ) 
                                                              ? $arrayVariables["strFinPagFechaContabilizacionHasta"] : "" );
                    
                    if( !empty($strFinPagFechaContabilizacionDesde) && !empty($strFinPagFechaContabilizacionHasta) )
                    {
                        $whereVar .= "AND ( ipd.feDeposito >= '".trim($strFinPagFechaContabilizacionDesde)."' ".
                                     "      OR ( ipd.feDeposito IS NULL AND ipd.feCreacion >= '".trim($strFinPagFechaContabilizacionDesde)."' ) ) ".
                                     "AND ( ipd.feDeposito <= '".trim($strFinPagFechaContabilizacionHasta)."' ".
                                     "      OR ( ipd.feDeposito IS NULL AND ipd.feCreacion <= '".trim($strFinPagFechaContabilizacionHasta)."' ) ) ";
                    }
                    
                    
                    if(isset($arrayVariables["pag_formaPago"]))
                    {
                            if($arrayVariables["pag_formaPago"] && $arrayVariables["pag_formaPago"]!="" && $arrayVariables["pag_formaPago"]!="0")
                            {
                                    $whereVar .= "AND fp.id = '".trim($arrayVariables["pag_formaPago"])."' ";
                            }
                    }

                    if(isset($arrayVariables["pag_banco"]))
                    {
                            if($arrayVariables["pag_banco"] && $arrayVariables["pag_banco"]!="" && $arrayVariables["pag_banco"]!="0")
                            {
                                    $whereAdicional .= "
                                    AND ( 
                                                    ( 
                                                            SELECT count(btc)
                                                            FROM schemaBundle:AdmiBancoTipoCuenta btc, schemaBundle:AdmiBanco b   
                                                            WHERE ipd.bancoTipoCuentaId = btc.id 
                                                            AND btc.bancoId = b.id 
                                                            AND LOWER(b.estado) not like LOWER('Eliminado') AND LOWER(b.estado) not like LOWER('Inactivo') 
                                                            AND LOWER(btc.estado) not like LOWER('Eliminado') AND LOWER(btc.estado) not like LOWER('Inactivo') 
                                                            AND b.id = '".trim($arrayVariables["pag_banco"])."'  
                                               ) > 0
                                               OR
                                                    ( 
                                                            SELECT count(btc2)
                                                            FROM schemaBundle:AdmiBancoCtaContable bcc2, schemaBundle:AdmiBancoTipoCuenta btc2, schemaBundle:AdmiBanco b2    
                                                            WHERE bcc2.id = ipd.bancoCtaContableId 
                                                            AND bcc2.bancoTipoCuentaId = btc2.id 
                                                            AND btc2.bancoId = b2.id 
                                                            AND LOWER(b2.estado) not like LOWER('Eliminado') AND LOWER(b2.estado) not like LOWER('Inactivo') 
                                                            AND LOWER(btc2.estado) not like LOWER('Eliminado') AND LOWER(btc2.estado) not like LOWER('Inactivo') 
                                                            AND b2.id = '".trim($arrayVariables["pag_banco"])."'  
                                               ) > 0												   
                                            ) 
                                    ";	
                            }
                    }
                    
                    if(isset($arrayVariables["pag_numDocumentoRef"]))
                    {
                            if($arrayVariables["pag_numDocumentoRef"] && $arrayVariables["pag_numDocumentoRef"]!="")
                            {
                                    $whereAdicional .= "
                                    AND ( 
                                                    ( 
                                                            SELECT count(dfc)
                                                            FROM schemaBundle:InfoDocumentoFinancieroCab dfc  
                                                            WHERE ipd.referenciaId = dfc.id 
                                                            AND dfc.numeroFacturaSri like '%".trim($arrayVariables["pag_numDocumentoRef"])."%'    
                                               ) > 0												   
                                            ) 
                                    ";	
                            }
                    }

                    if (isset($arrayVariables["strEstPunto"]))
                    {
                            if ($arrayVariables["strEstPunto"] && $arrayVariables["strEstPunto"]!="" && $arrayVariables["strEstPunto"]!="0")
                            {
                                if (trim($arrayVariables["strEstPunto"])!='ALL')
                                {
                                    if (preg_match("/^[A-Za-z-]+$/",$arrayVariables["strEstPunto"]))
                                    {
                                        $whereAdicional .= "
                                        AND lower(pun.estado) in ('".trim(strtolower($arrayVariables["strEstPunto"]))."')
                                        ";
                                    }
                                }
                                else
                                {
                                    $arrayEstPunto=array('strParametro' => 'CONF_ESTADO_PUNTO');
                                    $strEstadoPunto=strtolower(trim(str_replace(",","','",$this->obtenerParametroConfig($arrayEstPunto))));
                                    if (preg_match("/^[a-z-]+(','[a-z-]+)*$/",$strEstadoPunto))
                                    {
                                        $whereAdicional .= "
                                        AND lower(pun.estado) in ('".$strEstadoPunto."')
                                        ";
                                    }
                                }
                            }
                    }
            }
        }

        if($tipoDocumento == 'PAG' || $tipoDocumento == 'ANT' || $tipoDocumento == 'PAGC' || $tipoDocumento == 'ANTC')
        {		
                $selectedCont = " count(ipd) as cont ";
                $selectedData = "
                        ipc.id as id_documento, ipd.id as id_documento_detalle, ipc.oficinaId, ipc.numeroPago as numeroDocumento, 
                        ipc.valorTotal as valorTotalGlobal, ipc.estadoPago as estadoDocumentoGlobal, ipc.comentarioPago, 
                        ipd.feCreacion, ipd.valorPago as valorTotal, ipd.depositado, ipd.bancoTipoCuentaId, ipd.bancoCtaContableId, 
                        ipd.referenciaId, ipd.numeroReferencia, ipd.numeroCuentaBanco, ipd.usrCreacion, ipd.comentario as comentarioDetallePago,  
                        ipd.feDeposito,fp.id as id_forma_pago, fp.descripcionFormaPago, fp.esDepositable, 
                        atdf.codigoTipoDocumento, atdf.nombreTipoDocumento, 
                        pun.id as id_punto, pun.login, pun.direccion as direccion_pto, pun.descripcionPunto, pun.estado, pun.usrVendedor, 
                        per.id, per.identificacionCliente, per.nombres, per.apellidos, per.razonSocial, 
                        per.direccion as direccion_grl, per.calificacionCrediticia, 
                        id.feProcesado,id.noComprobanteDeposito ,ipc.feCruce as fechaCruce, ipd.cuentaContableId, fp.codigoFormaPago 
                ";
                $from = "FROM 
                        schemaBundle:InfoPagoCab ipc
                                JOIN schemaBundle:InfoOficinaGrupo iogi with iogi.id=ipc.oficinaId
                                JOIN schemaBundle:InfoEmpresaGrupo iegi with iegi.id=iogi.empresaId and iegi.id='".$empresaId."',
                        schemaBundle:InfoPagoDet ipd left join schemaBundle:InfoDeposito id with id.id=ipd.depositoPagoId,
                        schemaBundle:AdmiFormaPago fp,
                        schemaBundle:AdmiTipoDocumentoFinanciero atdf,
                        schemaBundle:InfoPersona per, 
                        schemaBundle:InfoPersonaEmpresaRol perol 
                        $fromAdicional 
                        , schemaBundle:InfoPunto pun ";

/*                    AND ipc.oficinaId = $oficinaId */				

                $wher = "WHERE 
                        ipc.id = ipd.pagoId 
                        AND ipc.empresaId = $empresaId 
                        AND ipd.formaPagoId = fp.id 
                        AND ipc.tipoDocumentoId = atdf.id 
                        AND perol.id = pun.personaEmpresaRolId 
                        AND per.id = perol.personaId  
                        AND ipc.puntoId=pun.id 
                        $whereAdicional 
                        $whereVar 
                        $strSubQuery
                order by ipd.feCreacion DESC 
                ";	
        }
        else if($tipoDocumento == 'ANTS')
        {		
                $selectedCont = " count(ipd) as cont ";
                $selectedData = "
                        ipc.id as id_documento, ipd.id as id_documento_detalle, ipc.oficinaId, ipc.numeroPago as numeroDocumento, 
                        ipc.valorTotal as valorTotalGlobal, ipc.estadoPago as estadoDocumentoGlobal, ipc.comentarioPago, 
                        ipd.feCreacion, ipd.feDeposito ,ipd.valorPago as valorTotal, ipd.depositado, ipd.bancoTipoCuentaId, ipd.bancoCtaContableId, 
                        ipd.referenciaId, ipd.numeroReferencia, ipd.numeroCuentaBanco, ipd.usrCreacion, ipd.comentario as comentarioDetallePago, 
                        fp.id as id_forma_pago, fp.descripcionFormaPago, fp.esDepositable, 
                        atdf.codigoTipoDocumento, atdf.nombreTipoDocumento, ipc.feCruce as fechaCruce, fp.codigoFormaPago 
                ";
                $from = "FROM 
                schemaBundle:InfoPagoCab ipc,
                schemaBundle:InfoPagoDet ipd,
                schemaBundle:AdmiFormaPago fp,
                schemaBundle:AdmiTipoDocumentoFinanciero atdf  
                $fromAdicional ";

/*                    AND ipc.oficinaId = $oficinaId */				

                $wher = "WHERE 
                        ipc.id = ipd.pagoId 
                        AND ipc.empresaId = $empresaId 
                        AND ipd.formaPagoId = fp.id 
                        AND ipc.tipoDocumentoId = atdf.id 
                        $whereAdicional 
                        $whereVar 
                order by ipd.feCreacion DESC 
                ";	
        }

        $sql = "SELECT $selectedData $from $wher ";
        $sqlC = "SELECT $selectedCont $from $wher ";
				
        $queryC = $this->_em->createQuery($sqlC); 
        $query = $this->_em->createQuery($sql); 

		$resultTotal = $queryC->getOneOrNullResult();
		$total = ($resultTotal ? ($resultTotal["cont"] ? $resultTotal["cont"] : 0) : 0);
		//$total=count($query->getResult());
		
		//echo $query->getSql();
		
        $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        $resultado['registros']=$datos;
        $resultado['total']=$total;
        
        return $resultado;
    }

    /**
     * Busca facturas pendientes segun idPunto
     * @param integer $idPunto
     * @return array (Retorna arreglo con registros encontrados)
     */
    public function findFacturasPendientesPorPunto($idPunto)
    {   
        $query = $this->_em->createQuery("SELECT faCab
		FROM 
                schemaBundle:InfoDocumentoFinancieroCab faCab, 
                schemaBundle:AdmiTipoDocumentoFinanciero td
		WHERE faCab.tipoDocumentoId=td.id AND 
                td.codigoTipoDocumento in (:tiposDocumento) AND
                faCab.puntoId=:puntoId AND 
                faCab.estadoImpresionFact not in (:estados) 
                order by faCab.numeroFacturaSri ASC");
                
        $tiposDocumentos=array('FAC','FACP','ND','NDI');
        $estados=array('Cerrado','Pendiente' , 'Anulado', 'Anulada','Inactivo','Inactiva',
            'Rechazada','Rechazado','null','PendienteError','PendienteSri','Eliminado');
        $query->setParameter('estados',$estados);
        $query->setParameter('tiposDocumento',$tiposDocumentos);
        $query->setParameter('puntoId',$idPunto); 
        $datos = $query->getResult();
        return $datos;
    }
    
    /**
     * Busca los pagos segun criterios: estado, empresa, puntoId,fechaDesde, fechaHasta y numeroPago
     * 
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 16-02-2015 - Se ordena la búsqueda por fecha de creación descendente
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.2 20-12-2016 - Se agrega validación para que cuando no exista punto en sesión se omita criterio de búsqueda por el mismo.
     * 
     * @author Edson Franco <efrancon@telconet.ec>
     * @version 1.3 19-07-2017 - Se agrega el parámetro 'strTipoDocumento' para buscar los pagos asociados al código del documento financiero
     *                           enviado en la consulta
     * 
     * @param string  $estado
     * @param integer $empresaId
     * @param integer $intPuntoId
     * @param string  $strFechaDesde
     * @param string  $strFechaHasta
     * @param integer $limit
     * @param integer $start
     * @param string $strNumeroPago  
     * @return array (Retorna arreglo con el valor total y los registros encontrados)
     */    
    public function findPagosPorCriterios($arrayParametros)
    {
        $strWhere             = '';
        $estado               = $arrayParametros['estado'];
        $empresaId            = $arrayParametros['empresaId'];
        $intPuntoId           = $arrayParametros['puntoId'];
        $strFechaDesde        = $arrayParametros['fechaDesde'];
        $strFechaHasta        = $arrayParametros['fechaHasta'];
        $limit                = $arrayParametros['limit'];
        $start                = $arrayParametros['start'];
        $strNumeroPago        = $arrayParametros['numeroPago'];
        $numeroIdentificacion = $arrayParametros['numeroIdentificacion'];
        $numeroReferencia     = $arrayParametros['numeroReferencia'];
        $strTipoDocumento     = ( isset($arrayParametros['strTipoDocumento']) && !empty($arrayParametros['strTipoDocumento']) )
                                ? $arrayParametros['strTipoDocumento'] : '';
        
        $query                = $this->_em->createQuery();

        $strSql = "SELECT a FROM schemaBundle:InfoPagoCab a ".
                  "LEFT JOIN schemaBundle:InfoRecaudacionDet rdet WITH a.recaudacionDetId = rdet.id ".
                  "JOIN schemaBundle:AdmiTipoDocumentoFinanciero atdf WITH a.tipoDocumentoId = atdf.id WHERE ";
   
        if ( empty($intPuntoId) && empty($strFechaDesde) && empty($strFechaHasta) && empty($strNumeroPago) )
        {
            $resultado['registros'] = array();
            $resultado['total']     = 0;
            return $resultado;               
        }    

        //Agrega criterios al query
        if ( !empty($intPuntoId) )
        {
            $strWhere .= " a.puntoId=:puntoId AND ";
            $query->setParameter('puntoId', $intPuntoId);
        }
        elseif ( empty($intPuntoId) && $strTipoDocumento == 'ANTS' )
        {
            $strWhere .= " (a.puntoId is null OR a.puntoId = 0) AND ";
        }
        else
        {
            $strWhere .= "";
        }
        if ( !empty($strFechaDesde) )
        {                    
            $strFechaDesde = date("Y/m/d", strtotime($strFechaDesde));
            $strFechaDesde = $strFechaDesde.' 00:00:00';
            $strWhere .= " a.feCreacion >= :fechaDesde AND ";
            $query->setParameter('fechaDesde', $strFechaDesde);
        }
        if ( !empty($strFechaHasta) )
        {
            $strFechaHasta = date("Y/m/d", strtotime($strFechaHasta));
            $strFechaHasta = $strFechaHasta.' 23:59:59';
            $strWhere .= " a.feCreacion <= :fechaHasta AND ";
            $query->setParameter('fechaHasta', $strFechaHasta);
        }                
        if ($estado&&($estado!="null"))
        {       
            $strWhere .= " a.estadoPago = :estado AND ";
            $query->setParameter('estado', $estado);            
        }                
        if ( !empty($strNumeroPago) )
        {       
            $strWhere .= " a.numeroPago like :numeroPago AND ";
            $query->setParameter('numeroPago', "%".$strNumeroPago."%");
        }     
        if ($numeroIdentificacion)
        {
            $strWhere .= " rdet.identificacion like :identificacion AND ";
            $query->setParameter('identificacion', "%".$numeroIdentificacion."%");            
        }         
        if ($numeroReferencia)
        {
            $strWhere .= " rdet.numeroReferencia like :referencia AND ";
            $query->setParameter('referencia', "%".$numeroReferencia."%");            
        }

        if ( !empty($strTipoDocumento) )
        {
            $strWhere .= " atdf.codigoTipoDocumento = :strTipoDocumento AND ";
            $query->setParameter('strTipoDocumento', $strTipoDocumento);
        }

        $strSql .= $strWhere." a.empresaId= :empresaId ";
        $strSql .= "ORDER BY a.feCreacion DESC ";
        
        $query->setParameter('empresaId', $empresaId);
        $query->setDQL($strSql);
        $total=count($query->getResult());
        $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        $resultado['registros']=$datos;
        $resultado['total']=$total;
        return $resultado;
    }    
           
	public function findPagosNoDepositados($empresaId,$oficinaId,$fechaDesde,$fechaHasta,$formapago,$limit,$page,$start,$usrCreacion){
                $criterio_formapago='';
                $criterio_fecha_desde='';
                $criterio_fecha_hasta='';
                $criterio_oficina='';
                $criterio_usrcreacion='';
                $formaspago='';
                if ($formapago){
                    $arrformapago=  explode(',', $formapago);
                    for($i=0;$i<count($arrformapago);$i++){
                        $arrformapago[$i]=trim($arrformapago[$i]);
                        if($i==0)
                            $criterio_formapago="(c.descripcionFormaPago = '$arrformapago[$i]'";
                        else
                            $criterio_formapago=$criterio_formapago." OR c.descripcionFormaPago = '$arrformapago[$i]'";
                    }
                    $criterio_formapago=$criterio_formapago.") AND ";
                }
                /*print_r('Fecha Desde: '.$fechaDesde);
                print_r('Fecha Hasta: '.$fechaHasta);
                die;*/
                if ($fechaDesde){
		    //print_r('Entra a if de fecha desde');
                    $fechaD = date("Y/m/d", strtotime($fechaDesde));			
                    $fechaDesde = $fechaD.' 00:00:00' ;
                    $criterio_fecha_desde="a.feCreacion >= '$fechaDesde' AND ";
                }else{
		 //   print_r('Entra a else de fecha desde');
		    $fHoy= date( "Y/m/d");
		    $fechaDesde= date( "Y/m/d", strtotime( "-1 day", strtotime( $fHoy ) ) ); 
		    $criterio_fecha_desde="a.feCreacion >= '$fechaDesde' AND ";
                }
                
                if($fechaHasta){
		    //print_r('Entra a if de fecha hasta');
                    $fechaH = date("Y/m/d", strtotime($fechaHasta));			             
                    $fechaHasta = $fechaH.' 23:59:59';
                    $criterio_fecha_hasta="a.feCreacion <= '$fechaHasta' AND";
                }else{
		   // print_r('Entra a else de fecha hasta');
		    $fechaHasta= date("Y/m/d").' 23:59:59';
		    $criterio_fecha_hasta="a.feCreacion <= '$fechaHasta' AND";
                }
               
               if ($oficinaId){
                   $criterio_oficina=" a.oficinaId= $oficinaId AND ";
               }
               if ($usrCreacion){
                   $criterio_usrcreacion=" a.usrCreacion like '%$usrCreacion%' AND ";
               }
               
		$query = $this->_em->createQuery("SELECT b
		FROM 
                schemaBundle:InfoPagoCab a, schemaBundle:InfoPagoDet b,schemaBundle:AdmiFormaPago c, schemaBundle:AdmiTipoDocumentoFinanciero d
		WHERE 
                a.id=b.pagoId AND
                b.formaPagoId=c.id AND
                $criterio_fecha_desde
                $criterio_fecha_hasta 
                $criterio_formapago 
                $criterio_oficina
                $criterio_usrcreacion                    
                a.empresaId= $empresaId AND
                b.depositado='N' AND
                d.id = a.tipoDocumentoId AND
                d.codigoTipoDocumento in ('PAG','ANT') AND
		c.esDepositable='S'
                AND a.estadoPago<>'Anulado'
                order by b.feCreacion DESC");
                 //echo $query; die;
                //echo $query->getSQL();die;
                $total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
		//$datos = $query->getResult();
		//print_r($datos); die;
                $resultado['registros']=$datos;
                $resultado['total']=$total;
		return $resultado;
	}
    /**
     * 
     * @param integer $idFactura
     * @return float (Retorna el valor total de pagos con estado Cerrado de una factura)
     */
    public function findTotalPagosPorFactura($idFactura)
    {   
        $query = $this->_em->createQuery("SELECT  sum(pd.valorPago) as total_pagos
		FROM 
                schemaBundle:InfoPagoDet pd
		WHERE 
                pd.referenciaId= :referenciaId AND pd.estado=:estado");
        $query->setParameter('referenciaId',$idFactura );
        $query->setParameter('estado', 'Cerrado');
        $datos = $query->getOneOrNullResult();
        return $datos;
    } 
    
    /**Encontrar el detalle de pago que corresponde al valor de la factura, pero se lista si tiene una ND aplicada
	* @param string $idFactura   
	* @return resultado del query generado
	* @author gvillalba
	*/	       
    public function findDetalleDePagosPorFactura($idFactura)
    {   
        $query = $this->_em->createQuery("
				SELECT 
				ipd.id,
				ipc.id as pagoId,
				ipd.valorPago,
				idfd.pagoDetId as refencia_nd,
				idfc.estadoImpresionFact
		FROM 
                schemaBundle:InfoPagoDet ipd
                LEFT JOIN schemaBundle:InfoPagoCab ipc WITH ipc.id=ipd.pagoId
                LEFT JOIN schemaBundle:InfoDocumentoFinancieroDet idfd WITH ipd.id=idfd.pagoDetId
                LEFT JOIN schemaBundle:InfoDocumentoFinancieroCab idfc WITH idfc.id=idfd.documentoId
		WHERE 
                ipd.referenciaId=".$idFactura);
        $datos = $query->getResult();
        return $datos;
    }        
        
    public function findTotalPagosPorFacturaDifAnticipo($idFactura, $anticipo)
    {   
        $query = $this->_em->createQuery("SELECT  sum(pd.valorPago) as total_pagos
		FROM 
                schemaBundle:InfoPagoDet pd
		WHERE 
                pd.referenciaId=$idFactura and pd.pagoId<>$anticipo AND pd.estado='Cerrado'");
//echo $query->getSQL();die;
        $datos = $query->getOneOrNullResult();
        //print_r($datos);die;
        return $datos;
    }

    public function findTotalPagosPorClientePorTipoDocPorEmpresa($idPersonaEmpresaRol,$tipoDoc)
    {   
		if($tipoDoc=='PAG')
			$criterioEstado=" pago.estadoPago in ('Cerrado','Activo') ";
		elseif($tipoDoc=='ANT')
			$criterioEstado=" pago.estadoPago in ('Cerrado','Pendiente') ";
		elseif($tipoDoc=='ANTS')
			$criterioEstado=" pago.estadoPago in ('Cerrado') ";	
        $query = $this->_em->createQuery("SELECT  sum(pago.valorTotal) as valorTotal
		FROM 
                schemaBundle:InfoPagoCab pago, 
				schemaBundle:AdmiTipoDocumentoFinanciero td,
				schemaBundle:InfoPunto pto
		WHERE 
				pago.tipoDocumentoId=td.id AND
				td.codigoTipoDocumento='$tipoDoc' AND
				pago.puntoId=pto.id AND
				pto.personaEmpresaRolId=$idPersonaEmpresaRol AND
                $criterioEstado");
        $datos = $query->getResult();
        //echo($query->getSQL());
        return $datos;
    } 
    
    public function findTotalPagosPorPuntoPorTipoDocPorEmpresa($idPunto,$tipoDoc)
    {   
		if($tipoDoc=='PAG')
			$criterioEstado=" pago.estadoPago in ('Cerrado','Activo') ";
		elseif($tipoDoc=='ANT')
			$criterioEstado=" pago.estadoPago in ('Cerrado','Pendiente') ";
		elseif($tipoDoc=='ANTS')
			$criterioEstado=" pago.estadoPago in ('Cerrado') ";
                
	
        $query = $this->_em->createQuery("SELECT  sum(pago.valorTotal) as valorTotal
		FROM 
                schemaBundle:InfoPagoCab pago, 
				schemaBundle:AdmiTipoDocumentoFinanciero td,
				schemaBundle:InfoPunto pto
		WHERE 
				pago.tipoDocumentoId=td.id AND
				td.codigoTipoDocumento='$tipoDoc' AND
				pago.puntoId=pto.id AND
				pto.id=$idPunto AND
                $criterioEstado");
        $datos = $query->getResult();
        //echo($query->getSQL());
        return $datos;
    } 
 
    public function getDetallePago($id_pago,$codigo)	
    {
		/*
		 *  select * from 
			info_pago_det ipd
			, admi_forma_pago afp
			--, admi_banco_tipo_cuenta abtc
			where ipd.pago_id=69 
			and afp.id_forma_pago=ipd.forma_pago_id 
		 * */
		$string_join="";
		$string_where="";
		$string_select="";
		
		if($codigo=="PAG" || $codigo=="ANT")
		{
			$string_select=",ip.login";
			$string_join=",schemaBundle:InfoPunto ip ";
			$string_where="and ip.id=ipc.puntoId ";
		}
		else
		{
			$string_select="";
			$string_join="";
			$string_where="";
		}
			
		$query = $this->_em->createQuery("SELECT 
				ipd.id,
				ipd.valorPago,
				afp.codigoFormaPago,
				afp.ctaContable,
				ipd.bancoTipoCuentaId,
				ipd.bancoCtaContableId,
				SUBSTRING(ipd.feCreacion,1,10) as feCreacion,
				SUBSTRING(ipd.feDeposito,1,10) as feDeposito,
				ipd.usrCreacion,
				iof.nombreOficina,
				iof.id as oficinaId,
				emp.id as empresaId,
				ipc.numeroPago,
				ipd.numeroCuentaBanco,
				ipd.numeroReferencia,
				SUBSTRING(ipc.feCruce,1,10) as feCruce
				".$string_select."
		FROM 
                schemaBundle:InfoPagoDet ipd, 
                schemaBundle:AdmiFormaPago afp,
                schemaBundle:InfoPagoCab ipc,
                schemaBundle:AdmiTipoDocumentoFinanciero atdf,
                schemaBundle:InfoOficinaGrupo iof,
                schemaBundle:InfoEmpresaGrupo emp 				
                ".$string_join."
		WHERE 
				ipd.pagoId=".$id_pago." 
				and ipc.oficinaId=iof.id 
				and iof.empresaId=emp.id 
				and ipc.id=ipd.pagoId
				".$string_where."
				and atdf.id=ipc.tipoDocumentoId 
				and atdf.codigoTipoDocumento='".$codigo."' 
				and afp.id=ipd.formaPagoId");
        $datos = $query->getResult();
        
//         echo($query->getSQL());
        //die;
        return $datos;
	}
	
	public function getBanco($banco_tipo_cta_id)	
    {
		/*
		select ab.cta_contable
		from admi_banco_tipo_cuenta abtc, admi_banco ab 
		where abtc.id_banco_tipo_cuenta=21 and ab.id_banco=abtc.banco_id;
		* 
		select ab.cta_contable,atc.es_tarjeta,atc.cta_contable as cta_tarjeta
		from admi_banco_tipo_cuenta abtc, admi_banco ab, admi_tipo_cuenta atc 
		where abtc.id_banco_tipo_cuenta=21 and ab.id_banco=abtc.banco_id and abtc.tipo_cuenta_id=atc.id_tipo_cuenta;
		* */
		$query = $this->_em->createQuery("SELECT 
				abcc.ctaContable,
				abcc.noCta,
				atc.esTarjeta,
				atc.ctaContable as ctaTarjeta,
				ab.descripcionBanco
		FROM 
                schemaBundle:AdmiBancoTipoCuenta abtc, 
                schemaBundle:AdmiBanco ab,
                schemaBundle:AdmiTipoCuenta atc,
                schemaBundle:AdmiBancoCtaContable abcc
		WHERE 
				abtc.id=".$banco_tipo_cta_id." 
				and ab.id=abtc.bancoId
				and abtc.tipoCuentaId=atc.id
				and abcc.bancoTipoCuentaId=abtc.id");
        $datos = $query->getSingleResult();
        //print_r($datos);die;
        return $datos;
	}
	
	public function getBancoContable($banco_cta_contable_id)	
    {
		/*
		select ab.cta_contable
		from admi_banco_tipo_cuenta abtc, admi_banco ab 
		where abtc.id_banco_tipo_cuenta=21 and ab.id_banco=abtc.banco_id;
		* 
		select ab.cta_contable,atc.es_tarjeta,atc.cta_contable as cta_tarjeta
		from admi_banco_tipo_cuenta abtc, admi_banco ab, admi_tipo_cuenta atc 
		where abtc.id_banco_tipo_cuenta=21 and ab.id_banco=abtc.banco_id and abtc.tipo_cuenta_id=atc.id_tipo_cuenta;
		* */
		$query = $this->_em->createQuery("SELECT 
				abcc.ctaContable,
				abcc.noCta,
				atc.esTarjeta,
				atc.ctaContable as ctaTarjeta,
				ab.descripcionBanco
		FROM 
                schemaBundle:AdmiBancoCtaContable abcc,
                schemaBundle:AdmiBanco ab,
                schemaBundle:AdmiTipoCuenta atc,
                schemaBundle:AdmiBancoTipoCuenta abtc 
		WHERE 
				abcc.id=".$banco_cta_contable_id." 
				and abcc.bancoTipoCuentaId=abtc.id
				and ab.id=abtc.bancoId
				and abtc.tipoCuentaId=atc.id");
        $datos = $query->getSingleResult();
        //print_r($datos);die;
        return $datos;
	}
	
	public function getNombreBanco($banco_tipo_cta_id)	
    {
		/*
		select ab.cta_contable
		from admi_banco_tipo_cuenta abtc, admi_banco ab 
		where abtc.id_banco_tipo_cuenta=21 and ab.id_banco=abtc.banco_id;
		* 
		select ab.cta_contable,atc.es_tarjeta,atc.cta_contable as cta_tarjeta
		from admi_banco_tipo_cuenta abtc, admi_banco ab, admi_tipo_cuenta atc 
		where abtc.id_banco_tipo_cuenta=21 and ab.id_banco=abtc.banco_id and abtc.tipo_cuenta_id=atc.id_tipo_cuenta;
		* */
		$query = $this->_em->createQuery("SELECT 
				ab.descripcionBanco
		FROM 
                schemaBundle:AdmiBancoTipoCuenta abtc, 
                schemaBundle:AdmiBanco ab,
                schemaBundle:AdmiTipoCuenta atc
		WHERE 
				abtc.id=".$banco_tipo_cta_id." 
				and ab.id=abtc.bancoId
				and abtc.tipoCuentaId=atc.id");
        $datos = $query->getSingleResult();
        //print_r($datos);die;
        return $datos;
	}
	
	public function getNombreBancoContable($banco_cta_contable_id)	
    {
		/*
		select ab.cta_contable
		from admi_banco_tipo_cuenta abtc, admi_banco ab 
		where abtc.id_banco_tipo_cuenta=21 and ab.id_banco=abtc.banco_id;
		* 
		select ab.cta_contable,atc.es_tarjeta,atc.cta_contable as cta_tarjeta
		from admi_banco_tipo_cuenta abtc, admi_banco ab, admi_tipo_cuenta atc 
		where abtc.id_banco_tipo_cuenta=21 and ab.id_banco=abtc.banco_id and abtc.tipo_cuenta_id=atc.id_tipo_cuenta;
		* */
		$query = $this->_em->createQuery("SELECT 
				ab.descripcionBanco
		FROM 
				schemaBundle:AdmiBancoCtaContable abcc,
                schemaBundle:AdmiBancoTipoCuenta abtc, 
                schemaBundle:AdmiBanco ab,
                schemaBundle:AdmiTipoCuenta atc
		WHERE 
				abcc.id=".$banco_cta_contable_id."
				and abcc.bancoTipoCuentaId=abtc.id 
				and ab.id=abtc.bancoId
				and abtc.tipoCuentaId=atc.id");
        $datos = $query->getSingleResult();
        //print_r($datos);die;
        return $datos;
	}
	
	public function getAnticipoSinCliente($banco_tipo_cta_id)	
    {
		/*
		select ab.cta_contable
		from admi_banco_tipo_cuenta abtc, admi_banco ab 
		where abtc.id_banco_tipo_cuenta=21 and ab.id_banco=abtc.banco_id;
		* 
		select ab.cta_contable,atc.es_tarjeta,atc.cta_contable as cta_tarjeta
		from admi_banco_tipo_cuenta abtc, admi_banco ab, admi_tipo_cuenta atc 
		where abtc.id_banco_tipo_cuenta=21 and ab.id_banco=abtc.banco_id and abtc.tipo_cuenta_id=atc.id_tipo_cuenta;
		* */
		$query = $this->_em->createQuery("SELECT 
				abcc.ctaContable,
				abcc.noCta,
				abcc.ctaContableAntSinClientes,
				atc.esTarjeta,
				atc.ctaContable as ctaTarjeta,
				ab.descripcionBanco				
		FROM 
                schemaBundle:AdmiBancoTipoCuenta abtc, 
                schemaBundle:AdmiBanco ab,
                schemaBundle:AdmiTipoCuenta atc,
                schemaBundle:AdmiBancoCtaContable abcc
		WHERE 
				abtc.id=".$banco_tipo_cta_id." 
				and ab.id=abtc.bancoId
				and abtc.tipoCuentaId=atc.id
				and abcc.bancoTipoCuentaId=abtc.id");
        $datos = $query->getSingleResult();
		//echo $query->getSQL();die;
        //print_r($datos);die;
        return $datos;
	}
	
	public function getAnticipoSinClienteContable($banco_tipo_cta_id)	
    {
		/*
		select ab.cta_contable
		from admi_banco_tipo_cuenta abtc, admi_banco ab 
		where abtc.id_banco_tipo_cuenta=21 and ab.id_banco=abtc.banco_id;
		* 
		select ab.cta_contable,atc.es_tarjeta,atc.cta_contable as cta_tarjeta
		from admi_banco_tipo_cuenta abtc, admi_banco ab, admi_tipo_cuenta atc 
		where abtc.id_banco_tipo_cuenta=21 and ab.id_banco=abtc.banco_id and abtc.tipo_cuenta_id=atc.id_tipo_cuenta;
		* */
		$query = $this->_em->createQuery("SELECT 
				abcc.ctaContable,
				abcc.noCta,
				abcc.ctaContableAntSinClientes,
				atc.esTarjeta,
				atc.ctaContable as ctaTarjeta,
				ab.descripcionBanco				
		FROM 
                schemaBundle:AdmiBancoTipoCuenta abtc, 
                schemaBundle:AdmiBanco ab,
                schemaBundle:AdmiTipoCuenta atc,
                schemaBundle:AdmiBancoCtaContable abcc
		WHERE 
				abcc.id=".$banco_cta_contable_id."
				and ab.id=abtc.bancoId
				and abtc.tipoCuentaId=atc.id
				and abcc.bancoTipoCuentaId=abtc.id");
        $datos = $query->getSingleResult();
		//echo $query->getSQL();die;
        //print_r($datos);die;
        return $datos;
	}
	
	public function getCtaCliente($empresa_id,$oficina_id,$tipo)
	{
		$select="";
		if($tipo=='PAG')
			$select="iog.ctaContableClientes,";
		else
			$select="iog.ctaContableAnticipos,";
			
		$query = $this->_em->createQuery("SELECT 
				".$select."
				iog.noCta
		FROM 
                schemaBundle:InfoOficinaGrupo iog
		WHERE 
				iog.empresaId='".$empresa_id."' and iog.id=".$oficina_id);
        //echo($query->getSQL());
        $datos = $query->getSingleResult();
        
        //print_r($datos);
        
        return $datos;
	}
	
	public function getCajaChica($empresa_id, $oficina_id)
	{
		$query = $this->_em->createQuery("SELECT 
				iog.ctaContablePagos,
				iog.noCta
		FROM 
                schemaBundle:InfoOficinaGrupo iog
		WHERE 
				iog.empresaId=".$empresa_id." 
				and iog.id=".$oficina_id);
        $datos = $query->getSingleResult();
        //print_r($datos);die;
        return $datos;
	}
	
	public function findPagosPorDebitoGeneral($estado, $debitoGenId,$fechaDesde,$fechaHasta,$limit,$page,$start){
                $criterio_estado='';
                $criterio_fecha_desde='';
                $criterio_fecha_hasta='';
                if ($fechaDesde){
                    $fechaD = date("Y/m/d", strtotime($fechaDesde));			
                    $fechaDesde = $fechaD ;
                    $criterio_fecha_desde="a.feCreacion >= '$fechaDesde' AND ";
                }
                if($fechaHasta){
                    $fechaH = date("Y/m/d", strtotime($fechaHasta));			             
                    $fechaHasta = $fechaH;
                    $criterio_fecha_hasta="a.feCreacion <= '$fechaHasta' AND";
                }                
                if ($estado){       
                    $criterio_estado="a.estado = '$estado' AND ";
                }                
		$query = $this->_em->createQuery("SELECT a
		FROM 
                schemaBundle:InfoPagoCab a, schemaBundle:InfoDebitoDet b, schemaBundle:InfoDebitoCab c,
				schemaBundle:InfoDebitoGeneral d
		WHERE 
                $criterio_estado
                $criterio_fecha_desde
                $criterio_fecha_hasta
                a.debitoDetId= b.id AND 
                b.debitoCabId= c.id AND
				c.debitoGeneralId=d.id AND
				d.id=$debitoGenId 
                 order by a.feCreacion DESC");
                //echo $query->getSQL();die;
                $total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
                $resultado['registros']=$datos;
                $resultado['total']=$total;
		return $resultado;
	} 

	public function findPagosPorRecaudacion($estado, $recaudacionId,$fechaDesde,$fechaHasta,$limit,$page,$start){
                $criterio_estado='';
                $criterio_fecha_desde='';
                $criterio_fecha_hasta='';
                if ($fechaDesde){
                    $fechaD = date("Y/m/d", strtotime($fechaDesde));			
                    $fechaDesde = $fechaD ;
                    $criterio_fecha_desde="a.feCreacion >= '$fechaDesde' AND ";
                }
                if($fechaHasta){
                    $fechaH = date("Y/m/d", strtotime($fechaHasta));			             
                    $fechaHasta = $fechaH;
                    $criterio_fecha_hasta="a.feCreacion <= '$fechaHasta' AND";
                }                
                if ($estado){       
                    $criterio_estado="a.estado = '$estado' AND ";
                }                
		$query = $this->_em->createQuery("SELECT a
		FROM 
                schemaBundle:InfoPagoCab a
		WHERE 
                $criterio_estado
                $criterio_fecha_desde
                $criterio_fecha_hasta
				a.recaudacionId=$recaudacionId 
                 order by a.feCreacion DESC");
                //echo $query->getSQL();die;
                $total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
                $resultado['registros']=$datos;
                $resultado['total']=$total;
		return $resultado;
	}
	
	public function obtenerPagosParaMigra()
	{
			$query = $this->_em->createQuery("select a 
				from schemaBundle:InfoPagoCab a 
				where 
				a.numPagoMigracion is null 
				and a.usrCreacion <> 'Admin Account'
				and a.valorTotal>0
				and a.recaudacionId is null
				and a.debitoDetId is not null "
				//." and a.feCreacion <='".date('Y/m/d', strtotime('2013-05-15'))."'"
				."	order by a.feCreacion");
			
			$total=count($query->getResult());
			$datos = $query->getResult();
			//echo $query->getSQL();die;
			$resultado['registros']=$datos;
			$resultado['total']=$total;
		
		return $resultado;
	} 		

	public function findTotalRetencionesAts($idEmpresa,$fechaDesde,$fechaHasta)
	{
		$subqueryFechas="";

		if($fechaDesde!=""){
			$subqueryFechas="cab.feCreacion >= '".$fechaDesde."' AND ";
		}

		if($fechaDesde!=""){
			$subqueryFechas.="cab.feCreacion <= '".$fechaHasta."' AND ";
		}

		$query = $this->_em->createQuery("
		SELECT 
		ip.identificacionCliente,
		tdf.codigoTipoDocumento,
		ip.tipoIdentificacion,
		count(det.id) as totalRegistros,
		sum(det.valorPago) as valorPago
		FROM 
		schemaBundle:InfoPagoCab cab, schemaBundle:InfoPagoDet det,
		schemaBundle:InfoPunto pto,
		schemaBundle:InfoPersonaEmpresaRol per,
		schemaBundle:InfoPersona ip,
		schemaBundle:InfoOficinaGrupo iog,
		schemaBundle:AdmiTipoDocumentoFinanciero tdf
		WHERE 
		cab.id=det.pagoId AND
		cab.puntoId=pto.id AND
		cab.oficinaId=iog.id AND
		iog.empresaId=".$idEmpresa." AND
		pto.personaEmpresaRolId=per.id AND
		per.personaId=ip.id								
		".$subqueryFechas." 
		cab.tipoDocumentoId in(2,3) AND 
		det.formaPagoId in (8,9,14)
		GROUP BY 	
		ip.identificacionCliente,
		tdf.codigoTipoDocumento,
		ip.tipoIdentificacion
		ORDER BY 
		ip.identificacionCliente,
		tdf.codigoTipoDocumento,
		ip.tipoIdentificacion								
		");

		$datos = $query->getResult();
		//$datos = $query->setFirstResult(0)->setMaxResults(1)->getResult();		
		//echo $query->getSQL()."\n";		
		return $datos;
	}		


	public function findTotalRetencionesAtsPorPersonaEmpresaRol($idPer,$fechaDesde,$fechaHasta){
	
		$subqueryFechas="";

		if($fechaDesde!=""){
			$subqueryFechas="cab.feCreacion >= '".$fechaDesde."' AND ";
		}

		if($fechaDesde!=""){
			$subqueryFechas.="cab.feCreacion <= '".$fechaHasta."' AND ";
		}

	
		$query= $this->_em->createQuery("SELECT per.id,
		sum(det.valorPago) as valorPago
		FROM 
		schemaBundle:InfoPagoCab cab, schemaBundle:InfoPagoDet det,
		schemaBundle:InfoPunto pto, schemaBundle:InfoPersonaEmpresaRol per
		WHERE 
		cab.id=det.pagoId AND
		cab.puntoId=pto.id AND
		pto.personaEmpresaRolId=per.id AND 
		per.id=$idPer AND 
		$subqueryFechas		
		det.formaPagoId in (8,9,14)
		GROUP BY 	
		per.id
		ORDER BY 
		per.id");
		$datos = $query->getResult();
		//$datos = $query->setFirstResult(0)->setMaxResults(1)->getResult();		
		//echo $query->getSQL();die;		
		return $datos;
	}		
	
	//FUNCION PARA CORRECCIONES DE RESPUESTAS DE DEBITOS
	public function obtenerPagosMalIngresadosEnDebitos($valor,$cuenta,$bcoTipoCuenta)
	{
		$fecha = date("Y/m/d", strtotime('2013-05-20'));
		$query = $this->_em->createQuery("
SELECT det 
FROM 
schemaBundle:InfoPagoCab cab, schemaBundle:InfoPagoDet det
WHERE 
cab.id=det.pagoId AND
det.bancoTipoCuentaId=$bcoTipoCuenta AND
cab.feCreacion >= '$fecha' AND 
cab.tipoDocumentoId=4 AND 
det.formaPagoId=3 AND 
cab.usrCreacion='ncolta' 
AND det.numeroCuentaBanco like '%$cuenta%'
AND det.valorPago='$valor'			
			");
			
			$datos = $query->getResult();
			//echo $query->getSQL();die;		
		return $datos;
	} 		

	
	//FUNCION PARA CORRECCIONES DE RESPUESTAS DE DEBITOS
	public function obtenerPagosMalIngresadosEnDebitosConflicto($valor,$cuenta,$bcoTipoCuenta)
	{
		$fecha = date("Y/m/d", strtotime('2013-05-20'));
		$query = $this->_em->createQuery("
SELECT det 
FROM 
schemaBundle:InfoPagoCab cab, schemaBundle:InfoPagoDet det
WHERE 
cab.id=det.pagoId AND
det.bancoTipoCuentaId=$bcoTipoCuenta AND
cab.feCreacion >= '$fecha' AND 
cab.tipoDocumentoId=4 AND 
det.formaPagoId=3 AND 
cab.usrCreacion='ncolta' 
AND det.numeroCuentaBanco like '%$cuenta%'
AND det.valorPago='$valor'			
			");
			
			//$datos = $query->getResult();
			$datos = $query->setFirstResult(0)->setMaxResults(1)->getResult();		
			//echo $query->getSQL()."\n";		
		return $datos;
	}	
	
	//FUNCION PARA CORRECCIONES DE RESPUESTAS DE DEBITOS
	public function obtenerDebitoProcesado($idDebCab)
	{
		$query = $this->_em->createQuery("SELECT debD.id,
		debD.numeroTarjetaCuenta,
		debD.puntoId,debD.valorTotal,
		debC.bancoTipoCuentaId,
		debD.personaEmpresaRolId
		FROM 
		schemaBundle:InfoDebitoDet debD,
		schemaBundle:InfoDebitoCab debC
		WHERE 
		debC.id=debD.debitoCabId AND
		debD.debitoCabId=$idDebCab AND debD.estado='Procesado' AND debD.numeroTarjetaCuenta='5554186300'");
		$datos = $query->getResult();	
		//echo $query->getSQL()."\n";
		//$datos = $query->setFirstResult(0)->setMaxResults(2)->getResult();		
		return $datos;
	} 

	//FUNCION PARA CORRECCIONES DE RESPUESTAS DE DEBITOS
	public function obtenerDebitoProcesadoSinPago($idDebCab)
	{
		$query = $this->_em->createQuery("SELECT debD.id,
		debD.numeroTarjetaCuenta,
		debD.puntoId,debD.valorTotal,
		debC.bancoTipoCuentaId,
		debD.personaEmpresaRolId
		FROM 
		schemaBundle:InfoDebitoDet debD,
		schemaBundle:InfoDebitoCab debC
		WHERE 
		debC.id=debD.debitoCabId AND
		debD.debitoCabId=$idDebCab AND debD.estado='Procesado' AND 
		debD.id not in(
		SELECT debD1.id 
		FROM schemaBundle:InfoDebitoDet debD1, schemaBundle:InfoPagoCab pagoC
		WHERE debD1.id=pagoC.debitoDetId AND debD1.debitoCabId=$idDebCab
		)");
		$datos = $query->getResult();
		//echo $query->getSQL()."\n";	
		//$datos = $query->setFirstResult(0)->setMaxResults(4)->getResult();		
		return $datos;
	}

    //FUNCION PARA CORRECCIONES DE RESPUESTAS DE DEBITOS
    public function findPrimeraFacturaAbiertaPorPersonaEmpresaRolPorOficinaPorValor($idPersonaEmpresaRol, $idOficina,$valor){
			$criterioValor="";
			if($valor){
				$criterioValor=" idfc.valorTotal=$valor AND ";
			}
			$query = $this->_em->createQuery("SELECT idfc
				FROM 
                                schemaBundle:InfoDocumentoFinancieroCab idfc,
                                schemaBundle:AdmiTipoDocumentoFinanciero atdf,
								schemaBundle:InfoPersonaEmpresaRol per,
								schemaBundle:InfoPunto pto
				WHERE 
								per.id=$idPersonaEmpresaRol AND
								per.id=pto.personaEmpresaRolId AND
								pto.id=idfc.puntoId AND
                                idfc.tipoDocumentoId=atdf.id AND
								atdf.codigoTipoDocumento in ('FAC','FACP') AND  
                                idfc.estadoImpresionFact in ('Activo','Courier') AND
								$criterioValor
                                idfc.oficinaId=$idOficina ORDER BY idfc.feCreacion ASC");
			$resultado=$query->setFirstResult(0)->setMaxResults(1)->getOneOrNullResult();
					//echo $query->getSQL()."\n";
					//die;
			return $resultado;           
        }		
	
	public function obtenerPagosParaMigrarAA()
	{
		$query = $this->_em->createQuery("select a 
			from schemaBundle:InfoPagoCab a 
			where 
			a.feCreacion >= '".date('Y/m/d', strtotime('2013--01'))."'
			and a.feCreacion < '".date('Y/m/d', strtotime('2013--01'))."' 
			and a.anticipoId is null
			and a.numPagoMigracion is null
			and a.valorTotal>0
			order by a.feCreacion,a.id");

		//$total=count($query->setMaxResults(1000)->getResult());
		//$datos = $query->setMaxResults(1000)->getResult();
		$total=count($query->getResult());
		$datos = $query->getResult();
		//echo $query->getSQL();die;
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		
		return $resultado;
	}
	
	public function obtenerPagosCruzadosParaMigrarAA()
	{
		$query = $this->_em->createQuery("select a 
			from schemaBundle:InfoPagoCab a 
			where 
			a.feCruce >= '".date('Y/m/d', strtotime('2013-08-01'))."'
			and a.feCruce < '".date('Y/m/d', strtotime('2013-09-01'))."' 
			and a.anticipoId is null
			and a.numPagoMigracion is null
			and a.valorTotal>0
			order by a.feCreacion,a.id");

		//$total=count($query->setMaxResults(1000)->getResult());
		//$datos = $query->setMaxResults(1000)->getResult();
		$total=count($query->getResult());
		$datos = $query->getResult();
		//echo $query->getSQL();die;
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		
		return $resultado;
	}
	
	public function obtenerPagosAnuladosParaMigrarAA()
	{
		$query = $this->_em->createQuery("select a 
			from schemaBundle:InfoPagoCab a 
			where 
			a.feCreacion >= '".date('Y/m/d', strtotime('2013-08-01'))."'
			and a.feCreacion < '".date('Y/m/d', strtotime('2013-09-01'))."' 
			and a.valorTotal >0
			and a.estadoPago ='Anulado'
			and a.numPagoMigracion is null
			order by a.feCreacion,a.id");


		$total=count($query->getResult());
		$datos = $query->getResult();
		//echo $query->getSQL();die;
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		
		return $resultado;
	}
	
	public function obtenerRecaudacionesParaMigra()
	{
			$query = $this->_em->createQuery("select a 
				from schemaBundle:InfoPagoCab a 
				where 
				a.numPagoMigracion is null 
				and a.usrCreacion <> 'Admin Account'
				and a.valorTotal>0
				and a.recaudacionId is not null"
				//." and a.feCreacion <='".date('Y/m/d', strtotime('2013-05-15'))."'"
				."	order by a.id");
			
			$total=count($query->getResult());
			$datos = $query->getResult();
			//echo $query->getSQL();
			$resultado['registros']=$datos;
			$resultado['total']=$total;
		
		return $resultado;
	} 			
	

	public function findAnticiposPorPunto($idPunto){
			$query = $this->_em->createQuery("select a 
				from schemaBundle:InfoPagoCab a 
				where 
				a.puntoId= $idPunto AND
				a.estadoPago='Pendiente' order by a.valorTotal");

			$datos = $query->getResult();
		return $datos;	
	}

    /**
     * Documentación para funcion 'findAnticiposCruzados'.
     * Obtiene los anticipos que han sido cruzados
     * @version 1.0
     * @author amontero@telconet.ec
     * @since 01-09-2015
     * @param string $idEmpresa
     * @param string $fechaDesde
     * @param string $fechaHasta
     * @return arreglo con resultado del query
     */      
	public function findAnticiposCruzados($idEmpresa,$fechaDesde,$fechaHasta)
    {
        $rsm  = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null,$rsm);
		$sql=
        "SELECT 
            cab.id_pago,
            ofi.nombre_Oficina, 
            cab.numero_Pago, 
            cab.punto_Id,
            cab.fe_Creacion, 
            cab.fe_cruce,
            det.referencia_Id,
            det.valor_Pago,
            cab.estado_Pago,
            td.codigo_tipo_documento,
            ant.fe_creacion as fe_creacion_original,
            ant.fe_cruce as fe_cruce_original,
            ant.numero_pago as numero_pago_original,
            anttd.codigo_tipo_documento as tipo_documento_original
        FROM 
            INFO_PAGO_CAB cab 
            join INFO_PAGO_DET det on (cab.id_pago=det.pago_Id)
            join INFO_OFICINA_GRUPO ofi on  (cab.oficina_Id = ofi.id_oficina)
            join ADMI_TIPO_DOCUMENTO_FINANCIERO td on (cab.tipo_Documento_Id=td.id_tipo_documento)
            left join INFO_PAGO_CAB ant on (cab.anticipo_id=ant.id_pago)
            left join ADMI_TIPO_DOCUMENTO_FINANCIERO anttd on (ant.tipo_documento_id=anttd.id_tipo_documento)
        WHERE 
            CONTAINS(td.codigo_tipo_documento, :tipoPago, 1) > 0 AND
            ofi.empresa_id=:idEmpresa AND
            cab.estado_pago in (:estados)";
        
        $estados=array('Cerrado');
        $query->setParameter('tipoPago',  'ANT%');
        $query->setParameter('estados', $estados);
        $query->setParameter('idEmpresa', $idEmpresa);
        if($fechaDesde != "")
        {
            $fechaDesde           = date("Y/m/d", strtotime($fechaDesde)); 
            $sql.=" AND cab.fe_cruce >= :fe_desde ";
            $query->setParameter('fe_desde', date('Y/m/d', strtotime($fechaDesde)));
        }

        if($fechaHasta != "")
        {
            $fechaHasta           = date("Y/m/d", strtotime($fechaHasta));
            $sql.=" AND cab.fe_cruce <= :fe_hasta ";
            $query->setParameter('fe_hasta', date('Y/m/d', strtotime($fechaHasta)));
        }
        $sql.=" ORDER BY cab.fe_cruce ASC";
        $rsm->addScalarResult('ID_PAGO', 'id','integer');      
        $rsm->addScalarResult('ESTADO_PAGO', 'estadoPago', 'string');
        $rsm->addScalarResult('NOMBRE_OFICINA', 'oficina','string');
        $rsm->addScalarResult('NUMERO_PAGO', 'pago','string');
        $rsm->addScalarResult('PUNTO_ID', 'puntoId','integer');
        $rsm->addScalarResult('FE_CREACION', 'fechaCreacionPago','string');
        $rsm->addScalarResult('FE_CRUCE', 'fechaCruce','string');
        $rsm->addScalarResult('REFERENCIA_ID', 'referenciaId','integer');
        $rsm->addScalarResult('CODIGO_TIPO_DOCUMENTO', 'tipoDocumento','string');
        $rsm->addScalarResult('VALOR_PAGO', 'valorPago','float');     
        $rsm->addScalarResult('FE_CREACION_ORIGINAL', 'feCreacionPagoOriginal','string');
        $rsm->addScalarResult('FE_CRUCE_ORIGINAL', 'feCruceOriginal','string');   
        $rsm->addScalarResult('NUMERO_PAGO_ORIGINAL', 'numeroPagoOriginal','string');
        $rsm->addScalarResult('TIPO_DOCUMENTO_ORIGINAL', 'tipoDocumentoOriginal','string');
        $query->setSQL($sql);
        $resultado=$query->getScalarResult();
		return $resultado;	
	}

	public function findPagosPorAnticipoId($idAnticipo){
	
		$query=$this->_em->createQuery("SELECT cab.id,ofi.nombreOficina AS oficina, 
		cab.numeroPago AS pago, 
		CONCAT(CONCAT(p.nombres,' '),p.apellidos) as nombreCliente,
		p.razonSocial,
		pto.login,cab.feCreacion as fechaCreacionPago, 
		cab.feUltMod AS fechaCruce,
		fac.numeroFacturaSri as factura,
		det.valorPago, cab.estadoPago, td.codigoTipoDocumento as tipoDocumento
		FROM 
		schemaBundle:InfoPagoCab cab,
		schemaBundle:InfoPagoDet det,
		schemaBundle:InfoDocumentoFinancieroCab fac,
		schemaBundle:InfoOficinaGrupo ofi,
		schemaBundle:InfoPunto pto,
		schemaBundle:InfoPersonaEmpresaRol per,
		schemaBundle:InfoPersona p,
                schemaBundle:AdmiTipoDocumentoFinanciero td                
		WHERE 
		cab.anticipoId=$idAnticipo AND
		cab.id=det.pagoId AND
		det.referenciaId = fac.id AND
		cab.oficinaId = ofi.id AND
		cab.puntoId=pto.id AND
                cab.tipoDocumentoId=td.id AND
		pto.personaEmpresaRolId=per.id AND
		per.personaId = p.id ");
		//echo $query->getSQL();
			$resultado=$query->getResult();
			return $resultado;	
	}	
	
	public function findListadoPagosPorFactura($idFactura)
	{
		
		$query=$this->_em->createQuery("
			select sum(idfc.valorTotal) as total from schemaBundle:InfoPagoDet ipd
			join schemaBundle:InfoDocumentoFinancieroCab idfc with idfc.referenciaDocumentoId=ipd.pagoId
			where ipd.referenciaId=".$idFactura." and idfc.estadoImpresionFact='Activo'
			GROUP BY ipd.pagoId
			");
			
		$resultado=$query->getResult();
		return $resultado;	
	}
        
        public function obtieneSaldoPorPunto($idPunto){
            
		$query=$this->_em->createQuery("
			select 
                            sp.saldo 
                        from 
                            schemaBundle:VistaEstadoCuentaResumido sp
			where 
                            sp.id=".$idPunto);
			
		$resultado=$query->setFirstResult(0)->setMaxResults(1)->getResult();
                if(count($resultado)>0){
                    return $resultado;            
                }else
                {
                    $resp= array();    
                    $resp[0]['saldo']=0;
                    return $resp;
                    
                }
        }  
    
    /**
     * Documentación para findByPuntosReactivar
     * 
     * Función que se encarga de obtener los puntos para ser reactivados correspondiente al proceso de Pago por Debitos Pendientes.
     * 
     * @param array $arrayParametros['intDebitoDetId' : Id del Detalle del Débito]
     * 
     * @return array de $arrayPuntos.
     * 
     * @author Hector Lozano<hlozano@telconet.ec>
     * @version 1.0 08-07-2020
     */    
    public function findByPuntosReactivar($arrayParametros)
	{   
        try
       {
            $intDebitoDetId = $arrayParametros["intDebitoDetId"];  
            $objQuery       = $this->_em->createQuery();

            $strQuery = "SELECT IP.id 
                           FROM 
                             schemaBundle:InfoDebitoCab IDC,
                             schemaBundle:InfoDebitoDet IDD,
                             schemaBundle:InfoPunto IP
                           WHERE IDD.debitoCabId         = IDC.id
                             AND IDD.personaEmpresaRolId = IP.personaEmpresaRolId 
                             AND IDD.id                  =:intDebitoDetId 
                             AND IDD.estado              = 'Procesado'
                           GROUP BY IP.id  ";   

            $objQuery->setParameter('intDebitoDetId', $intDebitoDetId);
            $objQuery->setDQL($strQuery);

            $arrayPuntos = $objQuery->getResult();

            return $arrayPuntos;
       } 
       catch (Exception $ex) 
       {
            return null;
       }    
 
	}
        
    /**
     * Documentación para funcion 'obtieneSaldoDelCliente'.
     * Obtiene el saldo total de los puntos enviados por parametro
     * @param string $stringPuntos
     * @return float $floatSaldo : saldo del cliente
     */    
    public function obtieneSaldoDelCliente($stringPuntos) 
    {
        $arrayPuntos=  explode(",", $stringPuntos);
        $floatSaldo=0;
        //Se valida porque en el in no recibe mas de mil valores
        if (count($arrayPuntos)>1000)
        {
            for($i=0; $i<count($arrayPuntos);$i++)
            {
                $query = $this->_em->createQuery("select sp.saldo as saldo
                    from schemaBundle:VistaEstadoCuentaResumido sp
                    where sp.id = :punto");
                $query->setParameter("punto",$arrayPuntos[$i]);
                $resultado=$query->getResult();
                if($resultado)
                {
                    $floatSaldo+=$resultado[0]['saldo'];
                }    
            }            
        }
        else
        {
            $query = $this->_em->createQuery("select sp.saldo as saldo
                from schemaBundle:VistaEstadoCuentaResumido sp
                where sp.id in (:punto)");
            $query->setParameter("punto",$arrayPuntos);
            $resultado=$query->getResult();
            if($resultado)
            {
                $floatSaldo=$resultado[0]['saldo'];
            }    
            
        }    
        return $floatSaldo;
    }

    ///////////////////////////////////taty: Inicio Para reporte cierre de caja////////////////////////////
    
    /* Documentación para el método 'listarCierreCajaXFormaPago'.
     *
     * Actualizacion: Se elimina consulta por campo esDepositable
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 14-07-2014
     * 
     * Retorna los pagos para reporte de cierre de caja segun parametros
     *
     * @param mixed $empresaId Estado del documento.
     * @param mixed $fechaDesde Fecha desde para la consulta.
     * @param mixed $fechaHasta Fecha hasta para la consulta.
     * @param mixed $formapago Pto a consultar.
     * @param mixed $oficina Oficina del usuario en sesion
     *
     * @return resultado Listado de pagos.
     * @author Telcos <sistemas@telconet.ec>
     * @version 1.0
     * 
     */    
    public function listarCierreCajaXFormaPago($empresaId,$feDesde, $feHasta,$formapago,$oficina)
    {
        $whereAdicional = "";
        $query          = $this->_em->createQuery();
        $fechaDesde     = date("Y/m/d", strtotime($feDesde));
        $fechaHasta     = date("Y/m/d", strtotime($feHasta));
        if(isset($fechaDesde) && isset($fechaHasta) )
        {
            if($fechaDesde!="" && $fechaHasta!="" )
            {
                $fechaHasta=$fechaHasta." 23:59:59";
            }
            else
            {
                $fechaDesde="0000/00/00" ;
                $fechaHasta="0000/00/00" ;
            }
        }   
        if(isset($fechaDesde) && isset($fechaHasta))
        {
            if($fechaDesde!="0000/00/00" && $fechaHasta!="0000/00/00" )
            {
                $whereAdicional =  $whereAdicional."AND a.feCreacion > =  :fechaDesde AND a.feCreacion<= :fechaHasta ";
                $query->setParameter("fechaDesde", $fechaDesde);
                $query->setParameter("fechaHasta", $fechaHasta);
            }
            else 
            {
                $fechaHoy       = date("Y/m/d");
                $fechaDesde     = date("Y/m/d", strtotime($fechaHoy));
                $fechaHasta     = $fechaDesde. " 23:59:59";
                $whereAdicional = $whereAdicional."AND a.feCreacion > =  :fechaDesde AND a.feCreacion<= :fechaHasta ";
                $query->setParameter("fechaDesde", $fechaDesde);
                $query->setParameter("fechaHasta", $fechaHasta);
            }
        }
        //si hay forma de pago construyo la sentencia para filtrar mediante forma de pago
        if(isset($formapago))
        {// pregunta si existe la variable
            if($formapago!='')
            {
                $formapago = explode(",", $formapago);  
                $formaPagoIds=array();
                for($i = 0; $i < count($formapago); ++$i) 
                {
                    $objFormaPago= $this->_em->getRepository('schemaBundle:AdmiFormaPago')
                                             ->findOneBy(array('descripcionFormaPago'=>trim($formapago[$i])));  
                    if($objFormaPago)
                    {
                        $formaPagoIds[]= $objFormaPago->getId();
                    }
                }

                if($formaPagoIds)
                {
                    $whereAdicional =  $whereAdicional. "AND  a.formaPagoId in (:formaPagoIds) ";
                    $query->setParameter("formaPagoIds", $formaPagoIds);
                }
            }
        }
       if(isset($oficina))
       {           
           if($oficina!='' )
           {// si no es vacia y no es nul
               $whereAdicional =  $whereAdicional. "AND  b.oficinaId = :oficina ";
               $query->setParameter("oficina", $oficina);
           }
       }
       $query->setDQL("
                 SELECT a.id,a.formaPagoId,a.referenciaId,b.numeroPago,a.usrCreacion,
                   b.puntoId,a.numeroReferencia,a.bancoCtaContableId, b.oficinaId, a.valorPago,a.feCreacion
       FROM 
               schemaBundle:InfoPagoDet a,
               schemaBundle:InfoPagoCab b,
               schemaBundle:AdmiFormaPago c
       WHERE
               a.pagoId=b.id AND c.id=a.formaPagoId AND
               a.estado!= :estado AND b.anticipoId is null AND
               b.empresaId = :empresaId 
               $whereAdicional  
               order by a.formaPagoId DESC  "  );
               $query->setParameter("empresaId", $empresaId);
               $query->setParameter("estado", 'Anulado');
               $datos=$query->getResult();
               $total=count( $datos);
               $resultado['registros']=$datos;
               $resultado['total']=$total;
        return $resultado;
    }    
     
        
        
        
    /* Documentación para el método 'agruparCierreCajaXFormaPago'.
     *
     * Actualizacion: Se elimina consulta por campo esDepositable
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 14-07-2014
     * 
     * Retorna los pagos para reporte de cierre de caja segun parametros
     *
     * @param mixed $empresaId Estado del documento.
     * @param mixed $fechaDesde Fecha desde para la consulta.
     * @param mixed $fechaHasta Fecha hasta para la consulta.
     * @param mixed $formapago Pto a consultar.
     * @param mixed $oficina Oficina del usuario en sesion
     * @param mixed $limit limite de consulta
     * @param mixed $start desde donde empieza la consulta
     *
     * @return resultado Listado de pagos.
     * @author Telcos <sistemas@telconet.ec>
     * @version 1.0
     * 
     */       
    public function agruparCierreCajaXFormaPago($empresaId,$feDesde, $feHasta,$formapago,$oficina, $limit, $start)
    {  
        $strSql      = "";
        $strSqlWhere = "";
        $strSqlPie   = "";
        $fechaDesde  = date("Y/m/d", strtotime($feDesde));
        $fechaHasta  = date("Y/m/d", strtotime($feHasta));
        
        $query       = $this->_em->createQuery($strSql);        
        
        if(isset($fechaDesde) && isset($fechaHasta) )
        {                 
            if($fechaDesde!="" && $fechaHasta!="" )
            {
                $fechaHasta=$fechaHasta." 23:59:59";
            }
            else
            {
                $fechaDesde="0000/00/00" ;
                $fechaHasta="0000/00/00" ;
            }
        }   

        if(isset($fechaDesde) && isset($fechaHasta))
        {
            if($fechaDesde!="0000/00/00" && $fechaHasta!="0000/00/00" )
            {
                $strSqlWhere .=  " AND a.feCreacion > =  :fechaDesde AND a.feCreacion<= :fechaHasta ";
                $query->setParameter("fechaDesde", $fechaDesde);
                $query->setParameter("fechaHasta", $fechaHasta);                
            }
            else 
            { 
                $fechaHoy       = date("Y/m/d");
                $fechaDesde     = date("Y/m/d", strtotime($fechaHoy));
                $fechaHasta     = $fechaDesde. " 23:59:59";
                $strSqlWhere .=  " AND a.feCreacion > =  :fechaDesde AND a.feCreacion<= :fechaHasta ";
                $query->setParameter("fechaDesde", $fechaDesde);
                $query->setParameter("fechaHasta", $fechaHasta);
            }
        }        
        if(isset($formapago))
        {// pregunta si existe la variable
            if($formapago!='')
            {
                $formapago = explode(",", $formapago);  
                $formaPagoIds=array();
                for($i = 0; $i < count($formapago); ++$i) 
                {
                    $objFormaPago= $this->_em->getRepository('schemaBundle:AdmiFormaPago')
                                             ->findOneBy(array('descripcionFormaPago'=>trim($formapago[$i])));  
                    if($objFormaPago)
                    {
                        $formaPagoIds[]= $objFormaPago->getId();
                    }
                }

                if($formaPagoIds)
                {
                    $strSqlWhere .=   " AND  a.formaPagoId in (:formaPagoIds) ";
                    $query->setParameter("formaPagoIds", $formaPagoIds);
                }
            }
        }        
        
        if(isset($oficina))
        {
            if($oficina!='')
            {
                $strSqlWhere .=  "AND  b.oficinaId = :oficina ";
                $query->setParameter("oficina", $oficina);
            }
        }
        
        $query->setParameter("empresa", $empresaId);     
        $query->setParameter("estado", "Anulado");     
        
        $strSqlPie = " GROUP BY a.formaPagoId ORDER BY a.formaPagoId DESC ";
        
        $strSql= "SELECT a.formaPagoId, count(a.formaPagoId) as cantFpagos, SUM(a.valorPago) as sumaPago
            FROM 
                schemaBundle:InfoPagoDet a,
                schemaBundle:InfoPagoCab b,
                schemaBundle:AdmiFormaPago c
            WHERE
                a.pagoId=b.id 
                AND c.id=a.formaPagoId 
                AND a.estado!=:estado
                AND b.empresaId = :empresa
                AND b.anticipoId is null 
            ";        
        
        $strSql = $strSql.$strSqlWhere.$strSqlPie;

        $query->setDQL($strSql);
        
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
		return $datos;
	} 
     
        
      public function valorTotalCierreCaja($empresaId,$feDesde, $feHasta,$formapago,$oficina, $limit, $page, $start)
        {  $whereAdicional = "";
               $fechaDesde = date("Y/m/d", strtotime($feDesde));
            $fechaHasta = date("Y/m/d", strtotime($feHasta));
           
              
          if(isset($fechaDesde) && isset($fechaHasta) ){
                 
             if($fechaDesde!="" && $fechaHasta!="" ){
         
                  $fechaHasta=$fechaHasta." 23:59:59";
              }else{
                $fechaDesde="0000/00/00" ;
                $fechaHasta="0000/00/00" ;
             }
    
              
           }   
            
               
         if(isset($fechaDesde) && isset($fechaHasta)){
             
             if($fechaDesde!="0000/00/00" && $fechaHasta!="0000/00/00" ){
                 
	         
               $whereAdicional =  $whereAdicional."AND a.feCreacion > =  '".$fechaDesde."' AND a.feCreacion<= '".$fechaHasta."' ";
                  
                  
             }else { // si son vacios
                 
               
                  $fechaHoy=date("Y/m/d");
                  
                   $fechaDesde = date("Y/m/d", strtotime($fechaHoy));
                   
                $fechaHasta=$fechaDesde. " 23:59:59";
               
              
                  $whereAdicional =  $whereAdicional."AND a.feCreacion > =  '".$fechaDesde."' AND a.feCreacion<= '".$fechaHasta."' ";
                 
                     
             }
               
         
         }
         if(isset($formapago)){// pregunta si existe la variable
             if($formapago!=''   ){
                
                $FormaPago= $this->_em->getRepository('schemaBundle:AdmiFormaPago')->findOneBy(array('descripcionFormaPago'=>$formapago ));
                
                 
                 if($FormaPago){//entra si no es nula
                     
                      if(count($FormaPago)==1){
                          $whereAdicional =  $whereAdicional. "AND  a.formaPagoId = '".$FormaPago->getId()."' ";
                         
                      }
                 } 
                
             }
             
         }
         if(isset($oficina)){
             if($oficina!='' ){
                 
                 $whereAdicional =  $whereAdicional. "AND  b.oficinaId = '".$oficina."' ";
             }
         }
         
       
		$query = $this->_em->createQuery("
                  SELECT  SUM(a.valorPago) as ValorTotal
		FROM 
                schemaBundle:InfoPagoDet a,
                schemaBundle:InfoPagoCab b,
                schemaBundle:AdmiFormaPago c
		WHERE
                a.pagoId=b.id AND  c.id=a.formaPagoId AND
                a.estado!='Anulado' AND b.anticipoId is null AND
                c.esDepositable='S' AND
                b.empresaId=$empresaId 
                $whereAdicional  
                   "  );
         
            // echo($query->getSQL());die();
               // $total=count($query->getResult());
                
                $datos=$query->getResult();
                $total=count( $datos);
                $resultado['registros']=$datos;
                $resultado['total']=$total;
                   
		return $resultado;
                
      
	}    
     ///traer forma de pagos solo que sean depositables: para cerre de caja
      
        
        
        
///////////////taty:  Fin Para reporte cierre de caja/////////////////
    
    /**
     * Documentación para findPagoExistenteRecaudacionPagoLinea
     * 
     * Función que obtiene pagos existentes filtrando por datos enviados como parámetro.
     * 
     * @param array $arrayParametros['strEmpresaCod'        => Código de la empresa,
     *                               'intIdFormaPago'       => Código de la forma de pago,
     *                               'strNnumeroReferencia' => Número de referencia del documento,
     *                               'intIdBancoTipoCuenta' => Id del tipo de cuenta,
     *                               'intIdBancoCtaContable'=> Id de la cuenta contable,
     *                               'strFechaDesde'        => Rango fecha desde,
     *                               'strFechaHasta'        => Rango fecha hasta]

     * @return array arrayDatos
     * 
     * @author  Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 24-02-2017 Se agrega envio de array de parámetros.
     */
    
    public function findPagoExistenteRecaudacionPagoLinea($arrayParametros)
    {

        $strEmpresaCod         = $arrayParametros['strEmpresaCod'];
        $strFechaDesde         = $arrayParametros['strFechaDesde'];
        $strFechaHasta         = $arrayParametros['strfechaHasta']; 
        $intIdFormaPago        = $arrayParametros['intIdFormaPago']; 
        $strNumeroReferencia   = $arrayParametros['strNumeroReferencia']; 
        $intIdBancoTipoCuenta  = $arrayParametros['intIdBancoTipoCuenta']; 
        $intIdBancoCtaContable = $arrayParametros['intIdBancoCtaContable']; 

        $objQuery = $this->_em->createQuery('
                                                SELECT
                                                    atdf.codigoTipoDocumento,
                                                    ipc.id AS pagoId,
                                                    ir.id  AS recaudacionId,
                                                    ipl.id AS pagoLineaId
                                                FROM
                                                    schemaBundle:InfoPagoDet ipd
                                                    JOIN ipd.pagoId ipc
                                                    JOIN ipc.tipoDocumentoId atdf
                                                    LEFT JOIN ipc.recaudacionId ir
                                                    LEFT JOIN ipc.pagoLinea ipl
                                                WHERE
                                                    ipc.empresaId            = :strEmpresaCod
                                                    AND ipd.formaPagoId      = :intIdFormaPago
                                                    AND ipd.numeroReferencia = :strNumeroReferencia ' .
                                                    ($intIdBancoTipoCuenta ?  ' AND ipd.bancoTipoCuentaId  = :intIdBancoTipoCuenta  ' : '') .
                                                    ($intIdBancoCtaContable ? ' AND ipd.bancoCtaContableId = :intIdBancoCtaContable ' : '') .
                                                    ' AND ipc.feCreacion >=  :strFechaDesde 
                                                      AND ipc.feCreacion <   :strFechaHasta 
                                                      AND ipc.estadoPago IN  (:arrayEstadoPago)
                                                ORDER BY ipc.feCreacion DESC');
        $objQuery->setParameters(array(
                                        'strEmpresaCod'        => $strEmpresaCod,
                                        'intIdFormaPago'       => $intIdFormaPago,
                                        'strNumeroReferencia'  => $strNumeroReferencia,
                                        'arrayEstadoPago'      => array('Cerrado', 'Asignado', 'Activo', 'Pendiente'),
                                        'strFechaDesde'        => date("Y/m/d", strtotime($strFechaDesde)),
                                        'strFechaHasta'        => date("Y/m/d",strtotime(date('Y-m-d', strtotime($strFechaHasta)). " +1 day"))
                                       ));
            
        if ($intIdBancoTipoCuenta)
        {
            $objQuery->setParameter('intIdBancoTipoCuenta', $intIdBancoTipoCuenta);
        }
        if ($intIdBancoCtaContable)
        {
            $objQuery->setParameter('intIdBancoCtaContable', $intIdBancoCtaContable);
        }
        $objQuery->setMaxResults(1);
        $arrayDatos = $objQuery->getOneOrNullResult();
        return $arrayDatos;
    }
    
    /**
     * 
     * @param integer $idFactura
     * @return float (Retorna el valor total de pagos + el valor total de notas de credito de una factura)
     */    
    public function obtieneTotalPagosMasNotasDeCreditoPorFactura($idFactura)
    {   $totalPagos=0;
        $totalNc=0;
        $query_pagos=$this->_em->createQuery("
            SELECT  sum(pd.valorPago) as total_pagos
            FROM 
            schemaBundle:InfoPagoDet pd
            WHERE 
            pd.referenciaId=:referenciaId AND pd.estado=:estado
        ");
        $query_pagos->setParameter('referenciaId',$idFactura);
        $query_pagos->setParameter('estado','Cerrado');        
        $resultado_pagos=$query_pagos->getResult();
        if($resultado_pagos!=null){
            $totalPagos=$resultado_pagos[0]['total_pagos'];
        }
        $query_nc=$this->_em->createQuery("
            SELECT sum(nc.valorTotal) as total_nc
            FROM 
            schemaBundle:InfoDocumentoFinancieroCab fac, 
            schemaBundle:InfoDocumentoFinancieroCab nc
            WHERE
            fac.id=nc.referenciaDocumentoId AND 
            fac.id=:referenciaId AND nc.estadoImpresionFact=:estadoImpresionFact
        ");       
        $query_nc->setParameter('referenciaId',$idFactura);
        $query_nc->setParameter('estadoImpresionFact','Activo');        
        $resultado_nc=$query_nc->getResult();
        if($resultado_nc!=null){
            $totalNc=$resultado_nc[0]['total_nc'];
        } 
        $total=$totalPagos+$totalNc;
        return $total;            
    }
    
    /**
     * Documentación para el método 'findCantidadDetalle'.
     *
     * Me devuelve la cantidad de detalles del pago
     *
     * @param mixed $idPago Pago a buscar.
     *
     * @return datos Numero de detalles en el pago
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 17-07-2014
     */
    public function findCantidadDetalle($idPago)
    {   
        $query = $this->_em->createQuery();
        
        //Totales
        $sql="    
            SELECT  count(ipd.id) as cantidad
            FROM 
                schemaBundle:InfoPagoDet ipd,
                schemaBundle:InfoPagoCab ipc
            WHERE 
                ipd.pagoId=ipc.id
                and ipc.id= :pago_id";
        
        $query->setParameter('pago_id',$idPago);
        $query->setDQL($sql);
        $totalDetalles= $query->getSingleScalarResult();
        
        //Con el estado Cerrado
        $query2 = $this->_em->createQuery();
        
        $sql2="    
            SELECT  count(ipd.id) as cantidad
            FROM 
                schemaBundle:InfoPagoDet ipd,
                schemaBundle:InfoPagoCab ipc
            WHERE 
                ipd.pagoId=ipc.id
                and ipc.id= :pago_id
                and ipd.estado=:estadoPago ";
                
        $query2->setParameter('pago_id',$idPago );
        $query2->setParameter('estadoPago',"Cerrado");
        $query2->setDQL($sql2);
        $totalConEstado= $query2->getSingleScalarResult();
        
        
        $datos["totalDetalles"]=$totalDetalles;
        $datos["totalConEstado"]=$totalConEstado;
        
        return $datos;
    }     

    /* Documentación para el método 'obtenerAnticiposAsignados'.
     *
     * Me devuelve los documentos tales como  'PAG','PAGC','ANT','ANTS','ANTC' que se encuentren en estado Asignado
     *
     * @param mixed $estado Estado del documento.
     * @param mixed $puntos Pto a consultar.
     * @param mixed $fechaDesde Fecha desde para la consulta.
     * @param mixed $fechaHasta Fecha hasta para la consulta.
     *
     * @return resultado Listado de documentos y total de documentos.
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 17-07-2014
     */

    public function obtenerAnticiposAsignados($estado, $puntos, $fechaDesde, $fechaHasta)
    {
        $sub_parte = "";

        if($puntos != "")
        {
            $query = $this->_em->createQuery();

            $dql_cc = "SELECT count(ipc.id) ";

            $dql = "SELECT ipc.id,
					ipc.numeroPago,
					atdf.id as tipoDocumentoId,
					ipc.valorTotal,
					ipc.feCreacion,
					ipc.puntoId,
					ipc.oficinaId,
                    rec.id as recaudacionId  ";

            $cuerpo = " 
                FROM schemaBundle:InfoPagoCab ipc left join schemaBundle:InfoRecaudacion rec with rec.id=ipc.recaudacionId,
					schemaBundle:AdmiTipoDocumentoFinanciero atdf
                WHERE 
                    ipc.tipoDocumentoId=atdf.id
					and atdf.codigoTipoDocumento in (:codigos)
					and ipc.estadoPago=:estado
					and ipc.puntoId in (:puntos)";

            $dql_cc.=$cuerpo;
            $dql.=$cuerpo;

            if($fechaDesde != "")
            {
                $dql.=" and ipc.feCreacion >= :fe_desde";
                $dql_cc.=" and ipc.feCreacion >= :fe_desde";
                $query->setParameter('fe_desde', date('Y/m/d', strtotime($fechaDesde)));
            }

            if($fechaHasta != "")
            {
                $dql.=" and ipc.feCreacion <= :fe_hasta";
                $dql_cc.=" and ipc.feCreacion <= :fe_hasta";
                $query->setParameter('fe_hasta', date('Y/m/d', strtotime($fechaHasta)));
            }

            $codigos = array('PAG', 'PAGC', 'ANT', 'ANTS', 'ANTC');
            $query->setParameter('estado', $estado);
            $query->setParameter('codigos', $codigos);
            $query->setParameter('puntos', $puntos);

            $query->setDQL($dql);
            $datos = $query->getResult();

            if($datos)
            {
                $query->setDQL($dql_cc);
                $total = $query->getSingleScalarResult();
            }
            else
                $total = 0;

            $resultado['registros'] = $datos;
            $resultado['total'] = $total;
        }
        else
        {
            $resultado = '{"registros":"[]","total":0}';
        }

        return $resultado;
    }

    /**
    * obtenerPuntosPorPagoRecaudacion
    *
    * Funcion que obtiene los puntos segun el pago en línea o la recaudacion.
    * @author John Vera <javera@telconet.ec>
    * @version 1.1 08-12-2014
    * @param string $idRecaudacion
    * @param string $idPagoLinea
    * @return array $datos
    */

    public function obtenerPuntosPorPagoRecaudacion($idRecaudacion, $idPagoLinea)
    {
        $sql = '';
        $query = $this->_em->createQuery();
        
        if($idRecaudacion)
        {
            $sql = "SELECT ipc.puntoId
            FROM schemaBundle:InfoPagoCab ipc
            WHERE ipc.recaudacionId= :idRecaudacion
            AND ipc.puntoId IS NOT NULL
            GROUP BY ipc.puntoId";

            $query->setParameter('idRecaudacion', $idRecaudacion);

        }
        if($idPagoLinea)
        {

            $sql = "SELECT  ipc.puntoId
            FROM schemaBundle:InfoPagoCab ipc
            WHERE ipc.pagoLinea= :idPagoLinea
            AND ipc.puntoId IS NOT NULL
            GROUP BY ipc.puntoId";

            $query->setParameter('idPagoLinea', $idPagoLinea);
        }
        if($sql)
        {
            $query->setDQL($sql);
            $datos = $query->getResult();
            return $datos;
        }
    }
    
    
    
    
    /* Documentación para el método 'getPagosPorEmpleado'.
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 02-08-2016
     * 
     * Retorna listado de pagos agrupados por usr de creación
     *
     * @param array arrayParametros Arreglo de parámetros
     *
     * @return resultado Listado de pagos.
     */    
    public function getPagosPorEmpleado($arrayParametros)
    {
        
        $intEmpresaId   = $arrayParametros['intEmpresaId'];
        $datFechaDesde  = $arrayParametros['dateFechaDesde'];
        $datfechaHasta  = $arrayParametros['datefechaHasta']; 
        $strFormaPago   = $arrayParametros['strFormaPago']; 
        $intOficinaId   = $arrayParametros['intOficinaId']; 
        $strUsrCreacion = $arrayParametros['usrCrecacion']; 
        $limit          = $arrayParametros['limit']; 
        $start          = $arrayParametros['start'];   
        
        $whereAdicional = "";
        $query           = $this->_em->createQuery();
        $dateFechaDesde  = date("Y/m/d", strtotime($datFechaDesde));
        $datefechaHasta  = date("Y/m/d", strtotime($datfechaHasta));       
        
        
        
        
        if(isset($dateFechaDesde) && isset($datefechaHasta) )
        {
            if($dateFechaDesde!="" && $datefechaHasta!="" )
            {
                $datefechaHasta=$datefechaHasta." 23:59:59";
            }
            else
            {
                $dateFechaDesde="0000/00/00" ;
                $datefechaHasta="0000/00/00" ;
            }
        }   
        if(isset($dateFechaDesde) && isset($datefechaHasta))
        {
            if($dateFechaDesde!="0000/00/00" && $datefechaHasta!="0000/00/00" )
            {
                $whereAdicional =  $whereAdicional."AND a.feCreacion > =  :fechaDesde AND a.feCreacion<= :fechaHasta ";
                $query->setParameter("fechaDesde", $dateFechaDesde);
                $query->setParameter("fechaHasta", $datefechaHasta);
            }
            else 
            {
                $fechaHoy       = date("Y/m/d");
                $dateFechaDesde     = date("Y/m/d", strtotime($fechaHoy));
                $datefechaHasta     = $dateFechaDesde. " 23:59:59";
                $whereAdicional = $whereAdicional."AND a.feCreacion > =  :fechaDesde AND a.feCreacion<= :fechaHasta ";
                $query->setParameter("fechaDesde", $dateFechaDesde);
                $query->setParameter("fechaHasta", $datefechaHasta);
            }
        }
        //si hay forma de pago construyo la sentencia para filtrar mediante forma de pago
        if(isset($strFormaPago))
        {// pregunta si existe la variable
            if($strFormaPago!='')
            {
                $arrayFormaPago = explode(",", $strFormaPago);  
                $arrayFormaPagoIds=array();
                for($i = 0; $i < count($arrayFormaPago); ++$i) 
                {
                    $objFormaPago= $this->_em->getRepository('schemaBundle:AdmiFormaPago')
                                             ->findOneBy(array('descripcionFormaPago'=>trim($arrayFormaPago[$i])));  
                    if($objFormaPago)
                    {
                        $arrayFormaPagoIds[]= $objFormaPago->getId();
                    }
                }

                if($arrayFormaPagoIds)
                {
                    $whereAdicional =  $whereAdicional. "AND  a.formaPagoId in (:formaPagoIds) ";
                    $query->setParameter("formaPagoIds", $arrayFormaPagoIds);
                }
            }
        }
       if(isset($intOficinaId))
       {           
           if($intOficinaId!='')
           {
               $whereAdicional =  $whereAdicional. "AND  b.oficinaId = :oficina ";
               $query->setParameter("oficina", $intOficinaId);
           }
       }
       $query->setDQL("
                 SELECT a.id,a.formaPagoId,a.referenciaId,b.numeroPago,a.usrCreacion,
                   b.puntoId,a.numeroReferencia,a.bancoCtaContableId, b.oficinaId, a.valorPago,a.feCreacion
       FROM 
               schemaBundle:InfoPagoDet a,
               schemaBundle:InfoPagoCab b,
               schemaBundle:AdmiFormaPago c
       WHERE
               a.pagoId=b.id AND c.id=a.formaPagoId AND
               a.estado!= :estado AND b.anticipoId is null AND
               b.empresaId = :empresaId AND
               b.usrCreacion = :usrCreacion
               $whereAdicional
               order by a.usrCreacion,a.formaPagoId ASC "  );
               $query->setParameter("empresaId", $intEmpresaId);
               $query->setParameter("estado", 'Anulado');
               $query->setParameter("usrCreacion", $strUsrCreacion);
               
               $datos=$query->getResult();
               $total=count( $datos);
               $resultado['registros']=$datos;
               $resultado['total']=$total;
        return $resultado;
    } 
    
    
    /* Documentación para el método 'getTotalesXFormaPagoEmpleado'.
     *
     * @author Edgar  Holguin <eholguin@telconet.ec>
     * @version 1.0 02-08-2016
     * 
     * Retorna los pagos para reporte de cierre de caja segun parametros
     *
     * @param array $arrayParametros Arreglo de parametros.
     *
     * @return resultado Listado de pagos.
     * 
     */       
    public function getTotalesXFormaPagoEmpleado($arrayParametros)
    {  
        $strSql      = "";
        $strSqlWhere = "";
        $strSqlPie   = "";
        
        $intEmpresaId   = $arrayParametros['intEmpresaId'];
        $datFechaDesde  = $arrayParametros['dateFechaDesde'];
        $datfechaHasta  = $arrayParametros['datefechaHasta']; 
        $strFormaPago   = $arrayParametros['strFormaPago']; 
        $intOficinaId   = $arrayParametros['intOficinaId']; 
        $strUsrCreacion = $arrayParametros['usrCrecacion']; 
        $limit          = $arrayParametros['limit']; 
        $start          = $arrayParametros['start'];   
        
        $fechaDesde  = date("Y/m/d", strtotime($datFechaDesde));
        $fechaHasta  = date("Y/m/d", strtotime($datfechaHasta));
        
        $query       = $this->_em->createQuery($strSql);        
        
        if(isset($fechaDesde) && isset($fechaHasta) )
        {                 
            if($fechaDesde!="" && $fechaHasta!="" )
            {
                $fechaHasta=$fechaHasta." 23:59:59";
            }
            else
            {
                $fechaDesde="0000/00/00" ;
                $fechaHasta="0000/00/00" ;
            }
        }   

        if(isset($fechaDesde) && isset($fechaHasta))
        {
            if($fechaDesde!="0000/00/00" && $fechaHasta!="0000/00/00" )
            {
                $strSqlWhere .=  " AND a.feCreacion > =  :fechaDesde AND a.feCreacion<= :fechaHasta ";
                $query->setParameter("fechaDesde", $fechaDesde);
                $query->setParameter("fechaHasta", $fechaHasta);                
            }
            else 
            { 
                $fechaHoy       = date("Y/m/d");
                $fechaDesde     = date("Y/m/d", strtotime($fechaHoy));
                $fechaHasta     = $fechaDesde. " 23:59:59";
                $strSqlWhere .=  " AND a.feCreacion > =  :fechaDesde AND a.feCreacion<= :fechaHasta ";
                $query->setParameter("fechaDesde", $fechaDesde);
                $query->setParameter("fechaHasta", $fechaHasta);
            }
        }        
        if(isset($strFormaPago))
        {
            if($strFormaPago!='')
            {
                $arrayFormaPago = explode(",", $strFormaPago);  
                $formaPagoIds=array();
                for($i = 0; $i < count($arrayFormaPago); ++$i) 
                {
                    $objFormaPago= $this->_em->getRepository('schemaBundle:AdmiFormaPago')
                                             ->findOneBy(array('descripcionFormaPago'=>trim($arrayFormaPago[$i])));  
                    if($objFormaPago)
                    {
                        $arrayformaPagoIds[]= $objFormaPago->getId();
                    }
                }

                if($arrayformaPagoIds)
                {
                    $strSqlWhere .=   " AND  a.formaPagoId in (:formaPagoIds) ";
                    $query->setParameter("formaPagoIds", $arrayformaPagoIds);
                }
            }
        }        
        
        if(isset($intOficinaId))
        {
            if($intOficinaId!='')
            {
                $strSqlWhere .=  "AND  b.oficinaId = :oficina ";
                $query->setParameter("oficina", $intOficinaId);
            }
        }
        
        $query->setParameter("empresa", $intEmpresaId);     
        $query->setParameter("estado", "Anulado");
        $query->setParameter("usrCreacion", $strUsrCreacion);
        
        $strSqlPie = " GROUP BY a.formaPagoId,a.usrCreacion ORDER BY a.formaPagoId DESC ";
        
        $strSql= "SELECT a.formaPagoId, count(a.formaPagoId) as cantFpagos, SUM(a.valorPago) as sumaPago, a.usrCreacion
            FROM 
                schemaBundle:InfoPagoDet a,
                schemaBundle:InfoPagoCab b,
                schemaBundle:AdmiFormaPago c
            WHERE
                a.pagoId=b.id 
                AND c.id=a.formaPagoId 
                AND a.estado!=:estado
                AND b.empresaId = :empresa
                AND b.usrCreacion = :usrCreacion
                AND b.anticipoId is null 
            ";        
        
        $strSql = $strSql.$strSqlWhere.$strSqlPie;

        $query->setDQL($strSql);
        
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        
		return $datos;
	} 
    
    
    /* Documentación para el método 'getUsersPagoPorFecha.
     *
     * @author Edgar  Holguin <eholguin@telconet.ec>
     * @version 1.0 02-08-2016
     * 
     * Retorna Array de usuarios que ingresaron pagos en una fecha determinada
     *
     * @param array $arrayParametros Arreglo de parametros.
     *
     * @return array arrayUsers Arreglo de usuarios 
     * 
     */       
    public function getUsersPagoPorFecha($arrayParametros)
    {  
        $strSql      = "";
        $strSqlWhere = "";
        $strSqlGroup = "";
        
        $intEmpresaId   = $arrayParametros['intEmpresaId'];
        $datFechaDesde  = $arrayParametros['dateFechaDesde'];
        $datFechaHasta  = $arrayParametros['datefechaHasta']; 
        $strFormaPago   = $arrayParametros['strFormaPago']; 
        $intOficinaId   = $arrayParametros['intOficinaId']; 

        
        $fechaDesde  = date("Y/m/d", strtotime($datFechaDesde));
        $fechaHasta  = date("Y/m/d", strtotime($datFechaHasta));
        
        $query       = $this->_em->createQuery($strSql);        
        
        if(isset($fechaDesde) && isset($fechaHasta) )
        {                 
            if($fechaDesde!="" && $fechaHasta!="" )
            {
                $fechaHasta=$fechaHasta." 23:59:59";
            }
            else
            {
                $fechaDesde="0000/00/00" ;
                $fechaHasta="0000/00/00" ;
            }
        }   

        if(isset($fechaDesde) && isset($fechaHasta))
        {
            if($fechaDesde!="0000/00/00" && $fechaHasta!="0000/00/00" )
            {
                $strSqlWhere .=  " AND a.feCreacion > =  :fechaDesde AND a.feCreacion<= :fechaHasta ";
                $query->setParameter("fechaDesde", $fechaDesde);
                $query->setParameter("fechaHasta", $fechaHasta);                
            }
            else 
            { 
                $fechaHoy       = date("Y/m/d");
                $fechaDesde     = date("Y/m/d", strtotime($fechaHoy));
                $fechaHasta     = $fechaDesde. " 23:59:59";
                $strSqlWhere .=  " AND a.feCreacion > =  :fechaDesde AND a.feCreacion<= :fechaHasta ";
                $query->setParameter("fechaDesde", $fechaDesde);
                $query->setParameter("fechaHasta", $fechaHasta);
            }
        }        
        if(isset($strFormaPago))
        {
            if($strFormaPago!='')
            {
                $arrayFormaPago = explode(",", $strFormaPago);  
               
                for($intCont = 0; $intCont < count($arrayFormaPago); ++$intCont) 
                {
                    $objFormaPago= $this->_em->getRepository('schemaBundle:AdmiFormaPago')
                                             ->findOneBy(array('descripcionFormaPago'=>trim($arrayFormaPago[$intCont])));  
                    if($objFormaPago)
                    {
                        $arrayformaPagoIds[]= $objFormaPago->getId();
                    }
                }

                if($arrayformaPagoIds)
                {
                    $strSqlWhere .=   " AND  a.formaPagoId in (:formaPagoIds) ";
                    $query->setParameter("formaPagoIds", $arrayformaPagoIds);
                }
            }
        }        
        
        if(isset($intOficinaId))
        {
            if($intOficinaId!='')
            {
                $strSqlWhere .=  "AND  b.oficinaId = :oficina ";
                $query->setParameter("oficina", $intOficinaId);
            }
        }
        
        $query->setParameter("empresa", $intEmpresaId);     
        $query->setParameter("estado", "Anulado");
        
        $strSqlGroup =  "GROUP BY a.usrCreacion";
        
        $strSql= "  SELECT a.usrCreacion
                    FROM 
                        schemaBundle:InfoPagoDet a,
                        schemaBundle:InfoPagoCab b,
                        schemaBundle:AdmiFormaPago c
                    WHERE
                        a.pagoId=b.id 
                        AND c.id=a.formaPagoId 
                        AND a.estado!=:estado
                        AND b.empresaId = :empresa
                        AND b.anticipoId is null  ";        
        
        $strSql = $strSql.$strSqlWhere.$strSqlGroup;

        $query->setDQL($strSql);
        
		$arrayUsers = $query->getResult();
        
		return $arrayUsers;
	}    
    
    /* Documentación para el método 'getPagosPorReferencia'.
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 16-08-2016
     * 
     * Retorna listado de pagos agrupados por usr de creación y número de papeleta
     *
     * @param  array arrayParametros Arreglo de parámetros
     *
     * @return $arrayResultado Listado de pagos agrupados por numero de referencia.
     */    
    public function getPagosPorReferencia($arrayParametros)
    {       
        $intEmpresaId     = $arrayParametros['intEmpresaId'];
        $dateFechaInicial = $arrayParametros['dateFechaDesde'];
        $dateFechaFinal   = $arrayParametros['datefechaHasta']; 
        $strFormaPago     = $arrayParametros['strFormaPago']; 
        $intOficinaId     = $arrayParametros['intOficinaId']; 
        
        $whereAdicional   = "";
        $query            = $this->_em->createQuery();
        $dateFechaDesde   = date("Y/m/d", strtotime($dateFechaInicial));
        $datefechaHasta   = date("Y/m/d", strtotime($dateFechaFinal));       
        
        
        
        
        if(isset($dateFechaDesde) && isset($datefechaHasta) )
        {
            if($dateFechaDesde!="" && $datefechaHasta!="" )
            {
                $datefechaHasta=$datefechaHasta." 23:59:59";
            }
            else
            {
                $dateFechaDesde="0000/00/00" ;
                $datefechaHasta="0000/00/00" ;
            }
        }   
        if(isset($dateFechaDesde) && isset($datefechaHasta))
        {
            if($dateFechaDesde!="0000/00/00" && $datefechaHasta!="0000/00/00" )
            {
                $whereAdicional =  $whereAdicional."AND a.feCreacion > =  :fechaDesde AND a.feCreacion<= :fechaHasta ";
                $query->setParameter("fechaDesde", $dateFechaDesde);
                $query->setParameter("fechaHasta", $datefechaHasta);
            }
            else 
            {
                $fechaHoy       = date("Y/m/d");
                $dateFechaDesde = date("Y/m/d", strtotime($fechaHoy));
                $datefechaHasta = $dateFechaDesde. " 23:59:59";
                $whereAdicional = $whereAdicional."AND a.feCreacion > =  :fechaDesde AND a.feCreacion<= :fechaHasta ";
                $query->setParameter("fechaDesde", $dateFechaDesde);
                $query->setParameter("fechaHasta", $datefechaHasta);
            }
        }
        //si hay forma de pago construyo la sentencia para filtrar mediante forma de pago
        if(isset($strFormaPago))
        {// pregunta si existe la variable
            if($strFormaPago!='')
            {
                $arrayFormaPago = explode(",", $strFormaPago);  
                $arrayFormaPagoIds=array();
              
                foreach ($arrayFormaPago as $strDescripcionFormaPago)
                {
                    $objFormaPago= $this->_em->getRepository('schemaBundle:AdmiFormaPago')
                                             ->findOneBy(array('descripcionFormaPago'=>trim($strDescripcionFormaPago)));  
                    if($objFormaPago)
                    {
                        $arrayFormaPagoIds[]= $objFormaPago->getId();
                    }
                }

                if($arrayFormaPagoIds)
                {
                    $whereAdicional =  $whereAdicional. "AND  a.formaPagoId in (:formaPagoIds) ";
                    $query->setParameter("formaPagoIds", $arrayFormaPagoIds);
                }
            }
        }
       if(isset($intOficinaId))
       {           
           if($intOficinaId!='')
           {
               $whereAdicional =  $whereAdicional. "AND  b.oficinaId = :oficina ";
               $query->setParameter("oficina", $intOficinaId);
           }
       }
       $query->setDQL("
                        SELECT a.id,a.formaPagoId,a.referenciaId,b.numeroPago,a.usrCreacion,b.puntoId,a.numeroReferencia,
                               a.bancoCtaContableId, b.oficinaId, a.valorPago,a.feCreacion
                        FROM 
                                schemaBundle:InfoPagoDet a,
                                schemaBundle:InfoPagoCab b,
                                schemaBundle:AdmiFormaPago c
                        WHERE
                                a.pagoId=b.id AND c.id=a.formaPagoId AND
                                a.estado!= :estado AND b.anticipoId is null AND
                                b.empresaId = :empresaId 
                                $whereAdicional
                                order by a.numeroReferencia,a.formaPagoId ASC "  );
                                $query->setParameter("empresaId", $intEmpresaId);
                                $query->setParameter("estado", 'Anulado');

               
        $arrayDatos                  = $query->getResult();
        $intTotal                    = count( $arrayDatos);
        $arrayResultado['registros'] = $arrayDatos;
        $arrayResultado['total']     = $intTotal;
        
        return $arrayResultado;
    } 
    
    /* Documentación para el método 'getTotalesXFormaPagoRefrencia'.
     *
     * @author Edgar  Holguin <eholguin@telconet.ec>
     * @version 1.0 16-08-2016
     * 
     * Retorna los pagos para reporte de cierre de caja segun parametros
     *
     * @param array $arrayParametros Arreglo de parametros.
     *
     * @return resultado Listado de pagos.
     * 
     */       
    public function getTotalesXFormaPagoReferencia($arrayParametros)
    {  
        $strSql      = "";
        $strSqlWhere = "";
        $strSqlPie   = "";
        
        $intEmpresaId   = $arrayParametros['intEmpresaId'];
        $dateFechaDesde  = $arrayParametros['dateFechaDesde'];
        $datefechaHasta  = $arrayParametros['datefechaHasta']; 
        $strFormaPago   = $arrayParametros['strFormaPago']; 
        $intOficinaId   = $arrayParametros['intOficinaId']; 
        $strReferencia  = $arrayParametros['numReferencia'];
        $limit          = $arrayParametros['limit']; 
        $start          = $arrayParametros['start'];   
        
        $fechaDesde  = date("Y/m/d", strtotime($dateFechaDesde));
        $fechaHasta  = date("Y/m/d", strtotime($datefechaHasta));
        
        $query       = $this->_em->createQuery($strSql);        
        
        if(isset($fechaDesde) && isset($fechaHasta) )
        {                 
            if($fechaDesde!="" && $fechaHasta!="" )
            {
                $fechaHasta=$fechaHasta." 23:59:59";
            }
            else
            {
                $fechaDesde="0000/00/00" ;
                $fechaHasta="0000/00/00" ;
            }
        }   

        if(isset($fechaDesde) && isset($fechaHasta))
        {
            if($fechaDesde!="0000/00/00" && $fechaHasta!="0000/00/00" )
            {
                $strSqlWhere .=  " AND a.feCreacion > =  :fechaDesde AND a.feCreacion<= :fechaHasta ";
                $query->setParameter("fechaDesde", $fechaDesde);
                $query->setParameter("fechaHasta", $fechaHasta);                
            }
            else 
            { 
                $fechaHoy       = date("Y/m/d");
                $fechaDesde     = date("Y/m/d", strtotime($fechaHoy));
                $fechaHasta     = $fechaDesde. " 23:59:59";
                $strSqlWhere .=  " AND a.feCreacion > =  :fechaDesde AND a.feCreacion<= :fechaHasta ";
                $query->setParameter("fechaDesde", $fechaDesde);
                $query->setParameter("fechaHasta", $fechaHasta);
            }
        }        
        if(isset($strFormaPago))
        {
            if($strFormaPago!='')
            {
                $arrayFormaPago = explode(",", $strFormaPago);  
                $formaPagoIds=array();
               
                foreach($arrayFormaPago as $strDescripcionFormaPago)
                {
                    $objFormaPago= $this->_em->getRepository('schemaBundle:AdmiFormaPago')
                                             ->findOneBy(array('descripcionFormaPago'=>trim($strDescripcionFormaPago)));  
                    if($objFormaPago)
                    {
                        $arrayformaPagoIds[]= $objFormaPago->getId();
                    }
                }

                if($arrayformaPagoIds)
                {
                    $strSqlWhere .=   " AND  a.formaPagoId in (:formaPagoIds) ";
                    $query->setParameter("formaPagoIds", $arrayformaPagoIds);
                }
            }
        }        
        
        if(isset($intOficinaId))
        {
            if($intOficinaId!='')
            {
                $strSqlWhere .=  "AND  b.oficinaId = :oficina ";
                $query->setParameter("oficina", $intOficinaId);
            }
        }
        
        $query->setParameter("empresa", $intEmpresaId);     
        $query->setParameter("estado", "Anulado");
        $query->setParameter("numReferencia", $strReferencia);
        
        $strSqlPie = " GROUP BY a.formaPagoId,a.usrCreacion,a.numeroReferencia ORDER BY a.formaPagoId DESC ";
        
        $strSql= "  SELECT  a.formaPagoId, count(a.formaPagoId) as cantFpagos, SUM(a.valorPago) as sumaPago, a.usrCreacion, a.numeroReferencia
                    FROM 
                            schemaBundle:InfoPagoDet a,
                            schemaBundle:InfoPagoCab b,
                            schemaBundle:AdmiFormaPago c
                    WHERE
                            a.pagoId=b.id 
                            AND c.id=a.formaPagoId 
                            AND a.estado!=:estado
                            AND b.empresaId = :empresa
                            AND a.numeroReferencia = :numReferencia
                            AND b.anticipoId is null 
                    ";        
        
        $strSql = $strSql.$strSqlWhere.$strSqlPie;

        $query->setDQL($strSql);
        
        $arrayDatos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        
		return $arrayDatos;
	}
    
    /* Documentación para el método 'getNumReferenciasPagoPorFecha.
     *
     * @author Edgar  Holguin <eholguin@telconet.ec>
     * @version 1.0 16-08-2016
     * 
     * Retorna Array de numeros de reeferencia de pagos ingresados en una fecha determinada
     *
     * @param array $arrayParametros Arreglo de parametros.
     *
     * @return array arrayReferencias Arreglo de numeros de referencia
     * 
     */       
    public function getNumReferenciasPagoPorFecha($arrayParametros)
    {  
        $strSql      = "";
        $strSqlWhere = "";
        $strSqlGroup = "";
        
        $intEmpresaId   = $arrayParametros['intEmpresaId'];
        $dateFechaDesde = $arrayParametros['dateFechaDesde'];
        $dateFechaHasta = $arrayParametros['datefechaHasta']; 
        $strFormaPago   = $arrayParametros['strFormaPago']; 
        $intOficinaId   = $arrayParametros['intOficinaId']; 

        
        $fechaDesde  = date("Y/m/d", strtotime($dateFechaDesde));
        $fechaHasta  = date("Y/m/d", strtotime($dateFechaHasta));
        
        $query       = $this->_em->createQuery($strSql);        
        
        if(isset($fechaDesde) && isset($fechaHasta) )
        {                 
            if($fechaDesde!="" && $fechaHasta!="" )
            {
                $fechaHasta=$fechaHasta." 23:59:59";
            }
            else
            {
                $fechaDesde="0000/00/00" ;
                $fechaHasta="0000/00/00" ;
            }
        }   

        if(isset($fechaDesde) && isset($fechaHasta))
        {
            if($fechaDesde!="0000/00/00" && $fechaHasta!="0000/00/00" )
            {
                $strSqlWhere .=  " AND a.feCreacion > =  :fechaDesde AND a.feCreacion<= :fechaHasta ";
                $query->setParameter("fechaDesde", $fechaDesde);
                $query->setParameter("fechaHasta", $fechaHasta);                
            }
            else 
            { 
                $fechaHoy       = date("Y/m/d");
                $fechaDesde     = date("Y/m/d", strtotime($fechaHoy));
                $fechaHasta     = $fechaDesde. " 23:59:59";
                $strSqlWhere .=  " AND a.feCreacion > =  :fechaDesde AND a.feCreacion<= :fechaHasta ";
                $query->setParameter("fechaDesde", $fechaDesde);
                $query->setParameter("fechaHasta", $fechaHasta);
            }
        }        
        if(isset($strFormaPago))
        {
            if($strFormaPago!='')
            {
                $arrayFormaPago = explode(",", $strFormaPago);  
               
                for($intCont = 0; $intCont < count($arrayFormaPago); ++$intCont) 
                {
                    $objFormaPago= $this->_em->getRepository('schemaBundle:AdmiFormaPago')
                                             ->findOneBy(array('descripcionFormaPago'=>trim($arrayFormaPago[$intCont])));  
                    if($objFormaPago)
                    {
                        $arrayformaPagoIds[]= $objFormaPago->getId();
                    }
                }

                if($arrayformaPagoIds)
                {
                    $strSqlWhere .=   " AND  a.formaPagoId in (:formaPagoIds) ";
                    $query->setParameter("formaPagoIds", $arrayformaPagoIds);
                }
            }
        }        
        
        if(isset($intOficinaId))
        {
            if($intOficinaId!='')
            {
                $strSqlWhere .=  "AND  b.oficinaId = :oficina ";
                $query->setParameter("oficina", $intOficinaId);
            }
        }
        
        $query->setParameter("empresa", $intEmpresaId);     
        $query->setParameter("estado", "Anulado");
        
        $strSqlGroup =  "GROUP BY a.numeroReferencia, a.formaPagoId ORDER BY a.formaPagoId DESC";
        
        $strSql= "  SELECT  a.numeroReferencia, a.formaPagoId
                    FROM 
                            schemaBundle:InfoPagoDet a,
                            schemaBundle:InfoPagoCab b,
                            schemaBundle:AdmiFormaPago c
                    WHERE
                            a.pagoId=b.id 
                            AND c.id=a.formaPagoId 
                            AND a.estado!=:estado
                            AND b.empresaId = :empresa
                            AND b.anticipoId is null  ";        
        
        $strSql = $strSql.$strSqlWhere.$strSqlGroup;

        $query->setDQL($strSql);
        
        $arrayReferencias = $query->getResult();
        
		return $arrayReferencias;
	}
    

  /**
     * Documentación para el método 'ejecutarEnvioReporteCobranzas'.
     *
     * Ejecuta la generación y envío de reporte de cobranzas según los parámetros indicados.
     *
     * @param mixed $arrayParametros  criterios de busqueda
     *
     * @return array De informacion segun los criterios de busqueda dados
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 18-09-2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 19-12-2016 - Se agregan las variables 'strFinPagFechaContabilizacionDesde', 'strFinPagFechaContabilizacionDesde' para 
     *                           realizar la búsqueda por fechas con las cuales se contabilizan los documentos del departamento de cobranzas
     *
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.2 20-07-2017 - Se agrega el filtro de Estado de Punto, y el envio del mismo al paquete de BD.
     */
    public function ejecutarEnvioReporteCobranzas($arrayParametros)
    {
        $strTipoDocumento             = null;
        $strNumeroDocumento           = null;
        $strNumeroDocumentoAut        = null;
        $strUsrCreacion               = null;
        $strEstadoDocumento           = null;
        $strNumeroReferencia          = null;
        $strEstPunto                  = null;
        $strFormaPago                 = null;
        $dateFechaCreacionDesde       = null;
        $dateFechaCreacionHasta       = null;
        $strEmpresaCod                = null;
        $strUsrSesion                 = null;
        $strPrefijoEmpresa            = null;
        $strEmailUsrSesion            = null;
        $intOficinaId                 = null;
        $strStart                     = null;
        $strLimit                     = null;
        $strFechaContabilizacionDesde = "";
        $strFechaContabilizacionHasta = "";

        if($arrayParametros && count($arrayParametros) > 0)
        {
            $strUsrSesion      = $arrayParametros['usrSesion'];
            $strPrefijoEmpresa = $arrayParametros['prefijoEmpresa'];
            $strEmailUsrSesion = $arrayParametros['emailUsrSesion'];
            
            if( isset($arrayParametros["strFinPagFechaContabilizacionDesde"]) && !empty($arrayParametros["strFinPagFechaContabilizacionDesde"]) )
            {
                $strFechaContabilizacionDesde = trim($arrayParametros["strFinPagFechaContabilizacionDesde"]);
            }

            if( isset($arrayParametros["strFinPagFechaContabilizacionHasta"]) && !empty($arrayParametros["strFinPagFechaContabilizacionHasta"]) )
            {
                $strFechaContabilizacionHasta = trim($arrayParametros["strFinPagFechaContabilizacionHasta"]);
            }
            
            if(isset($arrayParametros["intEmpresaId"]))
            {
                if($arrayParametros["intEmpresaId"] != "" && $arrayParametros["intEmpresaId"] != "0")
                {
                    $strEmpresaCod = trim($arrayParametros["intEmpresaId"]);
                }
            }

            if(isset($arrayParametros["intOficinaId"]))
            {
                if($arrayParametros["intOficinaId"] != "" && $arrayParametros["intOficinaId"] != "0")
                {
                    $intOficinaId = trim($arrayParametros["intOficinaId"]);
                }
            }

            if(isset($arrayParametros["start"]))
            {
                if($arrayParametros["start"] != "")
                {
                    $strStart = trim($arrayParametros["start"]);
                }
            }

            if(isset($arrayParametros["limit"]))
            {
                if($arrayParametros["limit"] != "")
                {
                    $strStart = trim($arrayParametros["limit"]);
                }
            }

            if(isset($arrayParametros["fin_tipoDocumento"]))
            {
                if($arrayParametros["fin_tipoDocumento"] != "" && $arrayParametros["fin_tipoDocumento"] != "0")
                {
                    $strTipoDocumento = trim($arrayParametros["fin_tipoDocumento"]);
                }
            }

            if(isset($arrayParametros["pag_numDocumento"]))
            {
                if($arrayParametros["pag_numDocumento"] != "" && $arrayParametros["pag_numDocumento"] != "0")
                {
                    $strNumeroDocumento = trim($arrayParametros["pag_numDocumento"]);
                }
            }

            if(isset($arrayParametros["pag_numReferencia"]))
            {
                if($arrayParametros["pag_numReferencia"] != "" && $arrayParametros["pag_numReferencia"] != "0")
                {
                    $strNumeroReferencia = trim($arrayParametros["pag_numReferencia"]);
                }
            }

            if(isset($arrayParametros["pag_creador"]))
            {
                if($arrayParametros["pag_creador"] != "" && $arrayParametros["pag_creador"] != "0")
                {
                    $strUsrCreacion = trim($arrayParametros["pag_creador"]);
                }
            }

            if(isset($arrayParametros["pag_estado"]))
            {
                if($arrayParametros["pag_estado"] != "" && $arrayParametros["pag_estado"] != "0")
                {
                    $strEstadoDocumento = trim($arrayParametros["pag_estado"]);
                }
            }

            if (isset($arrayParametros["strEstPunto"]))
            {
                if ($arrayParametros["strEstPunto"] != "" && $arrayParametros["strEstPunto"] != "0")
                {
                    if (trim($arrayParametros["strEstPunto"])!= 'Todos')
                    {
                        if (preg_match("/^[A-Za-z-]+$/",$arrayParametros["strEstPunto"]))
                        {
                            $strEstPunto = trim($arrayParametros["strEstPunto"]);
                        }
                    }
                    else
                    {
                        $arrayEstPunto=array('strParametro' => 'CONF_ESTADO_PUNTO');
                        $strEstPunto = $this->obtenerParametroConfig($arrayEstPunto);
                        if (!preg_match("/^[A-Za-z-]+(,[A-Za-z-]+)*$/",$strEstPunto))
                        {
                            $strEstPunto = null;
                        }
                    }
                }
            }

            $strFechaCreacionDesde = (isset($arrayParametros["pag_fechaCreacionDesde"]) ? $arrayParametros["pag_fechaCreacionDesde"] : 0);
            $strFechaCreacionHasta = (isset($arrayParametros["pag_fechaCreacionHasta"]) ? $arrayParametros["pag_fechaCreacionHasta"] : 0);


            if($strFechaCreacionDesde && $strFechaCreacionDesde != "0")
            {
                $dateF                  = explode("-", $strFechaCreacionDesde);
                $dateFechaCreacionDesde = $dateF[0] . "/" . $dateF[1] . "/" . $dateF[2];
            }
            if($strFechaCreacionHasta && $strFechaCreacionHasta != "0")
            {
                $dateF                  = explode("-", $strFechaCreacionHasta);
                $fechaSqlAdd            = strtotime(date("Y-m-d", strtotime($dateF[0] . "-" . $dateF[1] . "-" . $dateF[2])) . " +1 day");
                $dateFechaCreacionHasta = date("d/m/Y", $fechaSqlAdd);
            }

            if(isset($arrayParametros["pag_formaPago"]))
            {
                if($arrayParametros["pag_formaPago"] != "" && $arrayParametros["pag_formaPago"] != "0")
                {
                    $strFormaPago = trim($arrayParametros["pag_formaPago"]);
                }
            }

            if(isset($arrayParametros["pag_banco"]))
            {
                if($arrayParametros["pag_banco"] != "" && $arrayParametros["pag_banco"] != "0")
                {
                    $strBanco = trim($arrayParametros["pag_banco"]);
                }
            }

            if(isset($arrayParametros["pag_numDocumentoRef"]))
            {
                if($arrayParametros["pag_numDocumentoRef"] != "" && $arrayParametros["pag_numDocumentoRef"] != "0")
                {
                    $strNumeroDocumentoAut = trim($arrayParametros["pag_numDocumentoRef"]);
                }
            }
        }

        $strSql = "BEGIN
                    DB_FINANCIERO.FNKG_REPORTE_FINANCIERO.P_REPORTE_COBRANZAS
                    (
                        :Pv_TipoDocumento,
                        :Pv_NumeroDocumento,
                        :Pv_NumeroDocumentoAut,
                        :Pv_UsrCreacion,
                        :Pv_EstadoDocumento,
                        :Pv_FechaCreacionDesde,
                        :Pv_FechaCreacionHasta,                        
                        :Pv_FormaPago,
                        :Pv_Banco,
                        :Pv_NumeroReferencia,
                        :Pv_EstadoPunto,
                        :Pv_EmpresaCod,
                        :Pv_UsrSesion,
                        :Pv_PrefijoEmpresa,
                        :Pv_EmailUsrSesion,                        
                        :Pv_Start,
                        :Pv_Limit,
                        :Pv_FechaContabilizacionDesde,
                        :Pv_FechaContabilizacionHasta
                    );
                END;";

        try
        {
            $stmt = $this->_em->getConnection()->prepare($strSql);

            $stmt->bindParam('Pv_TipoDocumento', $strTipoDocumento);
            $stmt->bindParam('Pv_NumeroDocumento', $strNumeroDocumento);
            $stmt->bindParam('Pv_NumeroDocumentoAut', $strNumeroDocumentoAut);
            $stmt->bindParam('Pv_UsrCreacion', $strUsrCreacion);
            $stmt->bindParam('Pv_EstadoDocumento', $strEstadoDocumento);
            $stmt->bindParam('Pv_FechaCreacionDesde', trim($dateFechaCreacionDesde));
            $stmt->bindParam('Pv_FechaCreacionHasta', trim($dateFechaCreacionHasta));
            $stmt->bindParam('Pv_FormaPago', $strFormaPago);
            $stmt->bindParam('Pv_Banco', $strBanco);
            $stmt->bindParam('Pv_NumeroReferencia', $strNumeroReferencia);
            $stmt->bindParam('Pv_EstadoPunto', $strEstPunto);
            $stmt->bindParam('Pv_EmpresaCod', $strEmpresaCod);
            $stmt->bindParam('Pv_UsrSesion', $strUsrSesion);
            $stmt->bindParam('Pv_PrefijoEmpresa', $strPrefijoEmpresa);
            $stmt->bindParam('Pv_EmailUsrSesion', $strEmailUsrSesion);
            $stmt->bindParam('Pv_Start', $strStart);
            $stmt->bindParam('Pv_Limit', $strLimit);
            $stmt->bindParam('Pv_FechaContabilizacionDesde', $strFechaContabilizacionDesde);
            $stmt->bindParam('Pv_FechaContabilizacionHasta', $strFechaContabilizacionHasta);
            
            $stmt->execute();
        }
        catch(\Exception $e)
        {
            error_log('Error en InfoPagoCabRepository - ejecutarEnvioReporteCobranzas: '.$e);
            throw($e);
        }
    }
    /**
    * Documentacion para la funcion guardaParametrosReportePagos
    *
    * Metodo que invoca a una funcion de BD llamada P_CONF_REPORT_AUTOM_PAGOS
    * Permite guardar los parametros de la pantalla de configuracion del reporte Automatico
    *
    * @param mixed $arrayParametros[
    *                               'strTipoDocumento' => Tipo de Documento
    *                               'strEstadoPunto'   => Estado del Punto
    *                               'strEstadoPago'    => Estado del Pago
    *                               'strFormaPago'     => Forma de Pago
    *                               'strUsrSesion'     => Usuario de Sesion
    *                               'strIpClient'      => IP del cliente conectado
    *                               'intCodEmpresa'    => identificación del client
    *                              ]
    *
    * @author Jorge Guerrero <jguerrerop@telconet.ec>
    * @version 1.0 20-07-2017
    *
    */
    public function guardaParametrosReportePagos($arrayParametros)
    {
        $strTipoDocumento = null;
        $strEstadoPunto   = null;
        $strEstadoPago    = null;
        $strFormaPago     = null;
        $strUsrSesion     = null;
        $strIpClient      = null;

        if($arrayParametros && count($arrayParametros) > 0)
        {
            $strTipoDocumento = $arrayParametros['strTipoDocumento'];
            $strEstadoPunto   = $arrayParametros['strEstadoPunto'];
            $strEstadoPago    = $arrayParametros['strEstadoPago'];
            $strFormaPago     = $arrayParametros['strFormaPago'];
            $strUsrSesion     = $arrayParametros['strUsrSesion'];
            $strIpClient      = $arrayParametros['strIpClient'];
            $strCodEmpresa    = $arrayParametros['strCodEmpresa'];
        }

        $strSql = "BEGIN
                    DB_FINANCIERO.FNKG_REPORTE_FINANCIERO.P_CONF_REPORT_AUTOM_PAGOS
                    (
                        :Pv_TipoDocumento,
                        :Pv_EstadoPunto,
                        :Pv_EstadoPago,
                        :Pv_FormaPago,
                        :Pv_UsrSesion,
                        :Pv_IpClient,
                        :Pv_CodEmpresa
                    );
                END;";

        try
        {
            $stmt = $this->_em->getConnection()->prepare($strSql);

            $stmt->bindParam('Pv_TipoDocumento', $strTipoDocumento);
            $stmt->bindParam('Pv_EstadoPunto', $strEstadoPunto);
            $stmt->bindParam('Pv_EstadoPago', $strEstadoPago);
            $stmt->bindParam('Pv_FormaPago', $strFormaPago);
            $stmt->bindParam('Pv_UsrSesion', $strUsrSesion);
            $stmt->bindParam('Pv_IpClient', $strIpClient);
            $stmt->bindParam('Pv_CodEmpresa', $strCodEmpresa);

            $stmt->execute();
        }
        catch(\Exception $e)
        {
            error_log('Error en InfoPagoCabRepository - guardaParametrosReportePagos: '.$e);
            throw($e);
        }
    }

    /**
    * Documentacion para la funcion getJsonPagosPorVendedor
    *
    * Función que retorna el listado de pagos por vendedor (usuario en sesión)
    * en formato json
    * @param mixed $arrayParametros[
    *                               'intEmpresaId'           => id empresa en sesion 
    *                               'strPrefijoEmpresa'      => prefijo de la empresa en sesion
    *                               'strUsrSesion'           => usuario en sesion
    *                               'strEmailUsrSesion'      => email usuario en sesion
    *                               'fechaCreacionDesde'     => rango inicial para fecha de creacion
    *                               'fechaCreacionHasta'     => rango final para fecha de creacion
    *                               'strIdentificacion'      => identificación del cliente
    *                               'strRazonSocial'         => razón social del cliente
    *                               'strNombres'             => nombres del cliente
    *                               'strApellidos'           => apellidos del cliente
    *                               'intStart'               => limite de rango de inicio para realizar consulta
    *                               'intLimit'               => limite de rango final para realizar consulta
    *                               ]
    *
    * @return json $objJsonData
    *
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 03-09-2016
    *
    */

    public function getJsonPagosPorVendedor($arrayParametros)
    {
        $intTotal      = 0;
                  
        try
        { 

            if($arrayParametros && count($arrayParametros)>0)
            {

                $objCursor  = $arrayParametros['cursor'];                 

                $strSql = "BEGIN
                            DB_FINANCIERO.FNKG_REPORTE_FINANCIERO.P_GET_PAGOS_VENDEDOR
                            (
                                :Pn_EmpresaId,
                                :Pv_PrefijoEmpresa,
                                :Pv_UsrSesion,
                                :Pv_EmailUsrSesion,
                                :Pv_FechaCreacionDesde,
                                :Pv_FechaCreacionHasta,
                                :Pv_Identificacion,
                                :Pv_RazonSocial,
                                :Pv_Nombres,
                                :Pv_Apellidos,
                                :Pn_Start,
                                :Pn_Limit,
                                :Pn_TotalRegistros,
                                :Pc_Documentos
                            );
                        END;";

                $stmt           = oci_parse($arrayParametros['oci_con'], $strSql);

                oci_bind_by_name($stmt, ":Pn_EmpresaId", $arrayParametros['intEmpresaId']);
                oci_bind_by_name($stmt, ":Pv_PrefijoEmpresa", $arrayParametros['strPrefijoEmpresa']);
                oci_bind_by_name($stmt, ":Pv_UsrSesion", $arrayParametros['strUsrSesion']);
                oci_bind_by_name($stmt, ":Pv_EmailUsrSesion", $arrayParametros['strEmailUsrSesion']);
                oci_bind_by_name($stmt, ":Pv_FechaCreacionDesde", $arrayParametros['strFechaDesde']);
                oci_bind_by_name($stmt, ":Pv_FechaCreacionHasta", $arrayParametros['strFechaHasta']);
                oci_bind_by_name($stmt, ":Pv_Identificacion", $arrayParametros['strIdentificacion']);
                oci_bind_by_name($stmt, ":Pv_RazonSocial", $arrayParametros['strRazonSocial']);
                oci_bind_by_name($stmt, ":Pv_Nombres", $arrayParametros['strNombres']);
                oci_bind_by_name($stmt, ":Pv_Apellidos", $arrayParametros['strApellidos']);
                oci_bind_by_name($stmt, ":Pn_Start", $arrayParametros['intStart']);
                oci_bind_by_name($stmt, ":Pn_Limit", $arrayParametros['intLimit']); 
                oci_bind_by_name($stmt, ":Pn_TotalRegistros", $intTotal, 10);   
                oci_bind_by_name($stmt, ":Pc_Documentos", $objCursor, -1, OCI_B_CURSOR);              

                oci_execute($stmt); 
                oci_execute($objCursor, OCI_DEFAULT);

                while (($row = oci_fetch_array($objCursor)) != false)
                { 
                    $arrayPagos[] = array(
                                           'vendedor'            => trim($row['VENDEDOR']),
                                           'cliente'             => trim($row['CLIENTE']),
                                           'login'               => trim($row['LOGIN']),
                                           'fechaPago'           => trim($row['FECHA_PAGO']),
                                           'numeroPago'          => trim($row['NUMERO_PAGO']),
                                           'formaPago'           => trim($row['FORMA_PAGO']),
                                           'estadoPago'          => trim($row['ESTADO_PAGO']),
                                           'valorPago'           => trim($row['VALOR_PAGO']),
                                           'codigoTipoDocumento' => trim($row['CODIGO_TIPO_DOCUMENTO']),
                                           'factura'             => trim($row['FACTURA']),
                                           'fechaFactura'        => trim($row['FECHA_FACTURA']),
                                           'estado'              => trim($row['ESTADO_FACTURA'])
                                         );                   
                }
            }
            else
            {
                $strMensaje= 'No se enviaron parámetros para generar la consulta.';
            }
            
            $arrayResultado = array('total' => $intTotal, 'encontrados' => $arrayPagos);
            
        }catch (\Exception $e) 
        {
            $strMensaje= 'No se enviaron parámetros para generar la consulta.';
            throw($e);
        }           

        $objJsonData    = json_encode($arrayResultado);
        
        return $objJsonData;
    }
    
   /**
    * Documentación para el método 'generarReportePagosPorVendedor'.
    *
    * Ejecuta la generación y envío de reporte de pagos por vendedor según los parámetros indicados.
    *
    * @param mixed $arrayParametros[
    *                               'intEmpresaId'           => id empresa en sesion 
    *                               'strPrefijoEmpresa'      => prefijo de la empresa en sesion
    *                               'strUsrSesion'           => usuario en sesion
    *                               'strEmailUsrSesion'      => email usuario en sesion
    *                               'fechaCreacionDesde'     => rango inicial para fecha de creacion
    *                               'fechaCreacionHasta'     => rango final para fecha de creacion
    *                               'strIdentificacion'      => identificación del cliente
    *                               'strRazonSocial'         => razón social del cliente
    *                               'strNombres'             => nombres del cliente
    *                               'strApellidos'           => apellidos del cliente
    *                               ]
    *
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 05-10-2016
    */
    public function generarReportePagosPorVendedor($arrayParametros)
    {      
        try
        {
            if($arrayParametros && count($arrayParametros)>0)
            {
                $strSql = "BEGIN
                            DB_FINANCIERO.FNKG_REPORTE_FINANCIERO.P_REPORTE_PAGOS_VENDEDOR
                            (
                                :Pn_EmpresaId,
                                :Pv_PrefijoEmpresa,
                                :Pv_UsrSesion,
                                :Pv_EmailUsrSesion,
                                :Pv_FechaCreacionDesde,
                                :Pv_FechaCreacionHasta,
                                :Pv_Identificacion,
                                :Pv_RazonSocial,
                                :Pv_Nombres,
                                :Pv_Apellidos                                  
                            );
                        END;";

                $stmt = $this->_em->getConnection()->prepare($strSql);

                $stmt->bindParam('Pn_EmpresaId', $arrayParametros['intEmpresaId']);
                $stmt->bindParam('Pv_PrefijoEmpresa', $arrayParametros['strPrefijoEmpresa']);
                $stmt->bindParam('Pv_UsrSesion', $arrayParametros['strUsrSesion']);
                $stmt->bindParam('Pv_EmailUsrSesion', $arrayParametros['strEmailUsrSesion']);
                $stmt->bindParam('Pv_FechaCreacionDesde', $arrayParametros['strFechaDesde']);
                $stmt->bindParam('Pv_FechaCreacionHasta', $arrayParametros['strFechaHasta']);
                $stmt->bindParam('Pv_Identificacion', $arrayParametros['strIdentificacion']);
                $stmt->bindParam('Pv_RazonSocial', $arrayParametros['strRazonSocial']);
                $stmt->bindParam('Pv_Nombres', $arrayParametros['strNombres']);
                $stmt->bindParam('Pv_Apellidos', $arrayParametros['strApellidos']);              

                $stmt->execute();
            }
            else
            {
                $strMensaje= 'No se enviaron parámetros para generar la consulta.';
            }

        }catch (\Exception $e) 
        {
            $strMensaje= 'No se enviaron parámetros para generar la consulta.';
            throw($e);
        }
    }

    /**
    * Documentación para el método 'obtenerParametroConfig'.
    *
    * Obtiene los valores configurados para el reporte automatico de Pagos.
    *
    * @param mixed $arrayParametros[
    *                               'strParametro'           => Parametro de Dato a buscar
    *                               ]
    *
    * @author Jorge Guerrero <jguerrerop@telconet.ec>
    * @version 1.0 14-07-2017
    */
    public function obtenerParametroConfig($arrayParametros)
    {
        $strRespParametro='';

        try
        {
            if (!empty($arrayParametros))
            {
                $strParametro = (isset($arrayParametros["strParametro"]) ? (!empty($arrayParametros["strParametro"])
                                       ? $arrayParametros["strParametro"] : "") : "");

                $strRespParametro = str_pad($strRespParametro, 1000, " ");

                $strSql = "BEGIN :strRespParametro := DB_FINANCIERO.FNKG_REPORTE_FINANCIERO.F_GET_PARM_CONF(:strParametro); END;";
                $stmt = $this->_em->getConnection()->prepare($strSql);
                $stmt->bindParam('strParametro',     $strParametro);
                $stmt->bindParam('strRespParametro', $strRespParametro);
                $stmt->execute();
            }
        }
        catch(\Exception $ex)
        {
            error_log('Error en InfoPagoCabRepository - obtenerParametroConfig: '.$ex);
            throw($ex);
        }

        return $strRespParametro;
    }               
    
    /**
    * Documentación para el método 'findErroresEstadoDeCuenta'.
    *
    * Permite listar los pagos dependientes por punto en el estado de cuenta y 
    * el estado de cuenta por punto.
    * 
    * - Listado de pagos asociados a facturas anuladas
    * 
    * @return listado_errores Listado de errores.
    *
    * @author Ricardo Coello Quezada <rcoello@telconet.ec>
    * @version 1.0 07-08-2017
    * 
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.1 23-11-2018 Se realiza  nueva llamada a procedimiento que obtiene  el listado total de pagos asociados a facturas anuladas.
    */
    public function getListadoDePagosDependientes($arrayParametros)
    {
        try
        { 
            $arrayPagosDep =  array();
            
            if($arrayParametros && count($arrayParametros)>0)
            {
                $objCursor  = $arrayParametros['cursor'];
                $strSql     =  "BEGIN 
                                DB_FINANCIERO.DOCUMENTOS_ERROR.P_PAGOS_ERROR_ESTADO_CTA
                                (
                                    :Pn_IdPunto,
                                    :Pn_EmpresaId,
                                    :Pr_ListadoPagosDep
                                ); 
                                END;";
                
                $objStmt           = oci_parse($arrayParametros['oci_con'], $strSql);
                
                oci_bind_by_name($objStmt, ":Pn_IdPunto",   $arrayParametros['intIdPunto'] );
                oci_bind_by_name($objStmt, ":Pn_EmpresaId", $arrayParametros['intEmpresaId']);
                oci_bind_by_name($objStmt, ":Pr_ListadoPagosDep", $objCursor, -1, OCI_B_CURSOR);
                
                oci_execute($objStmt); 
                oci_execute($objCursor);
                
                while( ($objRow = oci_fetch_array($objCursor, OCI_ASSOC + OCI_RETURN_NULLS)) )
                { 
                    $arrayPagosDep[] = array(
                                           'comentario_error'  => trim($objRow['COMENTARIO_ERROR']),
                                           'login'             => trim($objRow['LOGIN']),
                                           'origen_documento'  => trim($objRow['ORIGEN_DOCUMENTO'])
                                         );
                }
                
                oci_close($arrayParametros['oci_con']);
            }
            else
            {
                $strMensaje= 'No se enviaron parámetros para generar la consulta.';
            }
        }
        catch (\Exception $e) 
        {
            $strMensaje= 'Ocurrio un error al tratar de generar el listado de pagos dependientes.';
            error_log($e->getMessage());
            throw($e);
        }           
            
        return $arrayPagosDep;
    }
    
    /**
    * Documentación para el método 'findPagosPorDependenciaHistorial'.
    *
    * Verifica si el pago es dependiente en 'INFO_PAGO_HISTORIAL' 
    * por motivo 'ANULACION_DEPENCIA'.
    * 
    * @return array (Retorna el historial del pago por dependencia encontrado)
    *
    * @author Ricardo Coello Quezada <rcoello@telconet.ec>
    * @version 1.0 07-08-2017
    */
    public function findPagosPorDependenciaHistorial($arrayParametros)
    {
       $arrayListPagosDepHisto= array();
        
        $objQuery             = $this->_em->createQuery();
        
        $strSelect            = "SELECT iph ";
        $strFrom              = "FROM 
                                    schemaBundle:InfoPagoCab ipc,
                                    schemaBundle:InfoPagoHistorial iph,
                                    schemaBundle:AdmiMotivo am ";
        $strWhere             = "WHERE ipc.id        = iph.pagoId
                                AND iph.motivoId  = am.id
                                AND ipc.id        = :intIdPago
                                AND ipc.puntoId   = :intIdPunto
                                AND ipc.empresaId = :strEmpresaId
                                AND iph.motivoId  = ( SELECT m.id
                                                      FROM schemaBundle:AdmiMotivo m
                                                      WHERE m.nombreMotivo = :strNombreMotivo
                                                      AND   m.estado       = :strEstadoMotivo) ";
        $strOrderBy = " ";
        
        $objQuery->setParameter('intIdPago', $arrayParametros['intIdPago']);
        $objQuery->setParameter('intIdPunto', $arrayParametros['intIdPunto']);
        $objQuery->setParameter('strEmpresaId',$arrayParametros['strEmpresaId']);
        $objQuery->setParameter('strNombreMotivo', $arrayParametros['strNombreMotivo']);
        $objQuery->setParameter('strEstadoMotivo', $arrayParametros['strEstadoMotivo']);
        
        $strSql = $strSelect.$strFrom.$strWhere.$strOrderBy;

        $objQuery->setDQL($strSql);

        $arrayListPagosDepHisto = $objQuery->getResult();
        
        return $arrayListPagosDepHisto;
    }
    
    /**
    * Documentación para el método 'getCanalesRecaudacion'.
    *
    * Función que obtiene los canales de recaudación (ADMI_CANAL_RECAUDACION) según los datos enviados como parámetros
    * 
    * @return array $arrayCanalesRecaudacion
    *
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.0 14-11-2017
    */
    public function getCanalesRecaudacion($arrayParametros)
    {       
        $objQuery             = $this->_em->createQuery();
        
        $strSql               = "SELECT acr.id, acr.nombreCanalRecaudacion, acr.estadoCanalRecaudacion "
                              . "FROM   schemaBundle:AdmiCanalRecaudacion acr "
                              . "WHERE  acr.empresaCod             = :strEmpresaCod "
                              . "AND    acr.estadoCanalRecaudacion = :strEstado order by acr.id desc";
        
        $objQuery->setParameter('strEmpresaCod', $arrayParametros['strEmpresaCod']);
        $objQuery->setParameter('strEstado', $arrayParametros['strEstado']);        

        $objQuery->setDQL($strSql);

        $arrayCanalesRecaudacion = $objQuery->getResult();
        
        return $arrayCanalesRecaudacion;
    }
    
    
    /**
    * Documentación para el método 'getRecaudacionesPorParametros'.
    *
    * Función que obtiene las recaudaciones (INFO_RECAUDACION) según los datos enviados como parámetros
    * 
    * @return array $arrayRecaudaciones
    *
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.0 14-11-2017
    */
    public function getRecaudacionesPorParametros($arrayParametros)
    {       
        $objQuery             = $this->_em->createQuery();
        
        $strSql               = "SELECT ir.id, ir.feCreacion, acr.nombreCanalRecaudacion "
                              . "FROM   schemaBundle:InfoRecaudacion ir "
                              . "JOIN   schemaBundle:AdmiCanalRecaudacion acr with acr.id = ir.canalRecaudacionId "
                              . "WHERE  acr.empresaCod  = :strEmpresaCod "
                              . "AND    ir.estado       = :strEstado order by ir.id desc";
        
        $objQuery->setParameter('strEmpresaCod', $arrayParametros['strEmpresaCod']);
        $objQuery->setParameter('strEstado', $arrayParametros['strEstado']);        

        $objQuery->setDQL($strSql);

        $arrayRecaudaciones = $objQuery->getResult();
        
        return $arrayRecaudaciones;
    }    
    
    /**
    * Documentación para el método 'ejecutarEnvioReporteTributario'.
    *
    * Ejecuta la generación y envío de reporte de tributario según los parámetros indicados.
    * 
    * @param  $arrayParametros
    *
    * @author Alex Arreaga <atarreaga@telconet.ec>
    * @version 1.0 
    * @since 02-04-2020
    */
    public function ejecutarEnvioReporteTributario($arrayParametros)
    {
        $strUsuarioSesion     = null;
        $strEmpresaCod        = null;
        $strClaveDesencripta  = null;
        $strFechaReporteDesde = "";
        $strFechaReporteHasta = "";

        if($arrayParametros && count($arrayParametros) > 0)
        {
            $strUsuarioSesion     = $arrayParametros['strUsuarioSesion'];
            $strClaveDesencripta  = $arrayParametros['strClaveDesencripta'];
            
            if( isset($arrayParametros["strFinFechaReporteDesde"]) && !empty($arrayParametros["strFinFechaReporteDesde"]) )
            {
                $strFechaReporteDesde = trim($arrayParametros["strFinFechaReporteDesde"]);
            }

            if( isset($arrayParametros["strFinFechaReporteHasta"]) && !empty($arrayParametros["strFinFechaReporteHasta"]) )
            {
                $strFechaReporteHasta = trim($arrayParametros["strFinFechaReporteHasta"]);
            }
            
            if(isset($arrayParametros["intEmpresaId"]) && $arrayParametros["intEmpresaId"] != "" && $arrayParametros["intEmpresaId"] != "0")
            {
                $strEmpresaCod = trim($arrayParametros["intEmpresaId"]);
            }
         
            $strSql = "BEGIN
                        DB_FINANCIERO.FNKG_PROCESO_MASIVO_DEB.P_REPORTE_TRIBUTARIO
                        (
                            :Pv_FechaReporteDesde,
                            :Pv_FechaReporteHasta,
                            :Pv_EmpresaCod,
                            :Pv_UsuarioSesion,
                            :Pv_ClaveDesencripta
                        );
                      END;";
        }
        try
        {
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            
            $objStmt->bindParam('Pv_FechaReporteDesde', $strFechaReporteDesde);
            $objStmt->bindParam('Pv_FechaReporteHasta', $strFechaReporteHasta);
            $objStmt->bindParam('Pv_EmpresaCod',        $strEmpresaCod);
            $objStmt->bindParam('Pv_UsuarioSesion',     $strUsuarioSesion);
            $objStmt->bindParam('Pv_ClaveDesencripta',  $strClaveDesencripta);
            
            $objStmt->execute();
        }
        catch(\Exception $ex)
        {
            error_log('Error en InfoPagoCabRepository - ejecutarEnvioReporteTributario: '.$ex);
            throw($ex);
        }
    }
    
   /**
    * Documentación para el método 'getValoresDiferidosPreCancelar'.
    *
    * Función que obtiene los valores diferidos para realizar la cancelación de la deuda diferida.
    * 
    * @return $arrayValoresDiferidos (Retorna Arreglo de valores de deuda diferida.)
    *
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 17-08-2020
    * 
    * Costo Query:36
    */
    public function getValoresDiferidosPreCancelar($arrayParametros)
    {   
        try
        {
            $objRsm = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
            $strSql = " SELECT 
                        DISTINCT
                        NDI.PUNTO_ID AS ID_PUNTO,
                        IP.LOGIN AS LOGIN,
                        DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_CARACT_DOCUMENTO(NCI.ID_DOCUMENTO,'ES_PROCESO_MASIVO') AS ID_PROCESO,
                        DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_CARACT_DOCUMENTO(NCI.ID_DOCUMENTO,'PROCESO_DE_EJECUCION') AS PROCESO_DE_EJECUCION,
                        (DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_CARACT_DOCUMENTO(NCI.ID_DOCUMENTO,'ES_MESES_DIFERIDO') 
                         -
                        DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_CARACT_DOCUMENTO(NCI.ID_DOCUMENTO,'ES_CONT_DIFERIDO') 
                        ) AS CANT_CUOTAS_PRECANCELAR,
                        DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_VALOR_VENCER_PRECAN_DIF(NDI.PUNTO_ID,
                          DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_CARACT_DOCUMENTO(NCI.ID_DOCUMENTO,'ES_PROCESO_MASIVO')) AS VALOR_CUOTA_MENSUAL,  
                        DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_SALDO_DIF_X_PTO_PROC_MASIVO(NDI.PUNTO_ID, 
                          DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_CARACT_DOCUMENTO(NCI.ID_DOCUMENTO,'ES_PROCESO_MASIVO')) AS SALDO_PRECANCELAR
                      FROM DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA IDC,
                        DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB NDI,
                        DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB NCI,
                        DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO TDOC,
                        DB_COMERCIAL.ADMI_CARACTERISTICA AC,
                        DB_COMERCIAL.INFO_PUNTO IP
                      WHERE NDI.PUNTO_ID                =:intIdPunto 
                      AND IP.ID_PUNTO                   = NDI.PUNTO_ID
                      AND NDI.USR_CREACION              =:usrCreacion
                      AND TDOC.ID_TIPO_DOCUMENTO        = NDI.TIPO_DOCUMENTO_ID
                      AND TDOC.CODIGO_TIPO_DOCUMENTO    =:strCodigoTipoDoc
                      AND IDC.DOCUMENTO_ID              = NDI.ID_DOCUMENTO
                      AND AC.ID_CARACTERISTICA          = IDC.CARACTERISTICA_ID
                      AND AC.DESCRIPCION_CARACTERISTICA =:strDescripcionCaract
                      AND NCI.USR_CREACION              =:usrCreacion
                      AND NCI.ESTADO_IMPRESION_FACT     =:strEstado
                      AND NCI.ID_DOCUMENTO              = COALESCE(TO_NUMBER(REGEXP_SUBSTR(IDC.VALOR,'^\d+')),0)
                      AND COALESCE(TO_NUMBER(REGEXP_SUBSTR(
                              DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_CARACT_DOCUMENTO(NCI.ID_DOCUMENTO,'ES_CONT_DIFERIDO'),'^\d+')),0)
                          < COALESCE(TO_NUMBER(REGEXP_SUBSTR(
                              DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_CARACT_DOCUMENTO(NCI.ID_DOCUMENTO,'ES_MESES_DIFERIDO'),'^\d+')),0)
                      GROUP BY 
                        NDI.PUNTO_ID, 
                        IP.LOGIN,
                        DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_CARACT_DOCUMENTO(NCI.ID_DOCUMENTO,'PROCESO_DE_EJECUCION'),
                        DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_CARACT_DOCUMENTO(NCI.ID_DOCUMENTO,'ES_PROCESO_MASIVO'),
                        DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_CARACT_DOCUMENTO(NCI.ID_DOCUMENTO,'ES_CONT_DIFERIDO'),
                        DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.F_GET_CARACT_DOCUMENTO(NCI.ID_DOCUMENTO,'ES_MESES_DIFERIDO')
                      ORDER BY ID_PROCESO ASC";

           $objQuery->setParameter('intIdPunto', $arrayParametros['intIdPunto']);
           $objQuery->setParameter('usrCreacion', 'telcos_diferido');
           $objQuery->setParameter('strCodigoTipoDoc', 'NDI');
           $objQuery->setParameter('strDescripcionCaract', 'ID_REFERENCIA_NCI');
           $objQuery->setParameter('strEstado', 'Activo');
           
           $objRsm->addScalarResult('LOGIN', 'strLogin', 'string');
           $objRsm->addScalarResult('ID_PROCESO', 'intIdProceso', 'integer');  
           $objRsm->addScalarResult('PROCESO_DE_EJECUCION', 'strProcesoEjecucion', 'string');  
           $objRsm->addScalarResult('CANT_CUOTAS_PRECANCELAR', 'intCantCuotas', 'integer');
           $objRsm->addScalarResult('VALOR_CUOTA_MENSUAL', 'floatValorCuotasMensual', 'float');
           $objRsm->addScalarResult('SALDO_PRECANCELAR', 'floatSaldoPreCancelar', 'float');    
           $objQuery->setSQL($strSql);
           $arrayValoresDiferidos = $objQuery->getResult();
        } 
        catch (\Exception $ex)
        {
            throw($ex);
        }
        
        return $arrayValoresDiferidos;
    }
    
    
    /**
     * Documentación para el método 'ejecutarNDIPreCancelacionDiferida'.
     * 
     * Función que invoca al proceso de generación de NDI diferidas por Deuda Diferida.
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0, 14-08-2020
     * 
     */
    public function ejecutarNDIPreCancelacionDiferida($arrayParametros)
    {
        $intIdServicio   = $arrayParametros['intIdServicio'];
        $strEmpresaCod   = $arrayParametros['strEmpresaCod'];
        $strTipoProceso  = $arrayParametros['strTipoProceso'];
        $strMensaje      = str_pad(' ', 30);
        
        try
        {
            $strSql  = "BEGIN DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.P_GENERAR_NDI_CANCELACION( :intIdServicio, "
                                                                                            ." :strEmpresaCod, "
                                                                                            ." :strTipoProceso, "
                                                                                            ." :strMensaje); END;";
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            
            $objStmt->bindParam('intIdServicio' , $intIdServicio);
            $objStmt->bindParam('strEmpresaCod' , $strEmpresaCod);
            $objStmt->bindParam('strTipoProceso' , $strTipoProceso);
            $objStmt->bindParam('strMensaje' , $strMensaje); 
            $objStmt->execute();
            
        } 
        catch (\Exception $ex) 
        {
            error_log("Error al ejecutar las NDI por Pre-Cancelación de Deuda: ". $ex->getMessage());
            $strMensaje = 'Error';
        }

        return $strMensaje;
    }
    
    
    /**
     * Documentación para el método 'getTotalPagosAnt'.
     *
     * Función que retorna el valor total de notas de débito asociadas a pagos y anticipos con el idDetPagAut enviado como parámetro.
     * 
     * Costo: 2
     * 
     * @param $intDetallePagoAutomaticoId Id del detalle de estado de cuenta.
     * 
     * @return $floatTotal
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 22-10-2020 
     */
    public function getTotalPagosAnt($intDetallePagoAutomaticoId)
    {
        
        $strSql =   "SELECT SUM(IPC.VALOR_TOTAL) AS TOTAL
                     FROM   INFO_PAGO_CAB  IPC
                     WHERE  IPC.DETALLE_PAGO_AUTOMATICO_ID   = :intDetallePagoAutomaticoId AND 
                            IPC.ESTADO_PAGO = :estadoPago
                     ORDER BY IPC.FE_CREACION DESC ";  
        
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $objQuery->setParameter("intDetallePagoAutomaticoId", $intDetallePagoAutomaticoId);
        $objQuery->setParameter("estadoPago", 'Anulado');
        $objRsm->addScalarResult('TOTAL', 'total', 'integer');
        $objQuery->setSQL($strSql);
        $arrayRespuesta = $objQuery->getScalarResult();
        $floatTotal = (!empty($arrayRespuesta[0]['total']) ? $arrayRespuesta[0]['total'] : 0 );            
            
        return floatval($floatTotal);
    }   
    
    




    /**
     * Documentación para el método 'getListPage'.
     *
     * Función que retorna lista de pagos por filtros
     * 
     * @param $$arrayRequest[idEmpresa] id de empresa
     * @param $$arrayRequest[ciclo] (Opcional) (Array) ids ciclo de facturacion
     * @param $$arrayRequest[login] (Opcional) login de usuario
     * @param $$arrayRequest[numeroPago] (Opcional) numero de pago
     * @param $$arrayRequest[usuCreacion] (Opcional) usuario de creacion
     * @param $$arrayRequest[tipoDocumento] (Opcional) (Array) ids tipo de documento financiero
     * @param $$arrayRequest[banco] (Opcional) (Array) ids Banco
     * @param $$arrayRequest[tipoPago] (Opcional) (Array) ids forma de pago
     * @param $$arrayRequest[canalPago] (Opcional) (Array) ids canal de pago en linea
     * @param $$arrayRequest[estado] (Opcional) (Array) Estados de pago 
     * @param $$arrayRequest[fechaInicio] (Opcional) fecha incio de creacion, se debe incluir campo fechaFin para su uso
     * @param $$arrayRequest[fechaFin] (Opcional) fecha hasta de creacion, se debe incluir campo fechaInicio para su uso
     * 
     * @return $arrayResultado pagos[id ,puntoId, numeroPago, valorTotal, usrCreacion, feCreacion, login, 
     *      identificacionCliente, estadoPago, nombreCompleto, canal, tipoPago, tipoDocumento]
     * 
     * @author Wilson Quinto <wquinto@telconet.ec>
     * @version 1.0 31-03-2021 
     */
    public function getListPayment($arrayFiltro)
    {
        $arrayResponse = array();
        //Query que obtiene los Datos
        $objRsm = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery = $this->_em->createNativeQuery(null, $objRsm);
        $strBody ="WITH TMP_PAGOS AS ( ";
		$strBody =$strBody."SELECT ipc.ID_PAGO, ipc.PUNTO_ID, ipc.NUMERO_PAGO, ipc.TIPO_DOCUMENTO_ID, ipc.USR_CREACION, ipc.FE_CREACION, ";
		$strBody =$strBody."ipc.ESTADO_PAGO, ipc.PAGO_LINEA_ID, ipd.VALOR_PAGO AS VALOR_TOTAL, ipd.FORMA_PAGO_ID, ipd.BANCO_TIPO_CUENTA_ID, ";
		$strBody =$strBody."atdf.NOMBRE_TIPO_DOCUMENTO ";
		$strBody =$strBody."FROM DB_FINANCIERO.INFO_PAGO_CAB ipc JOIN DB_FINANCIERO.INFO_PAGO_DET ipd ON ipd.PAGO_ID = ipc.ID_PAGO ";
		$strBody =$strBody."JOIN DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO atdf ON atdf.ID_TIPO_DOCUMENTO = ipc.TIPO_DOCUMENTO_ID ";
		$strBody =$strBody."WHERE ipc.EMPRESA_ID = :idEmpresa ";
		
		if(!empty($arrayFiltro['numeroPago']))
        {
            $strBody =$strBody."and ipc.NUMERO_PAGO= :numeroPago ";
        }

        if(!empty($arrayFiltro['usuCreacion']))
        {
            $strBody =$strBody."and ipc.USR_CREACION= :usuCreacion ";
        }
		
		if(!is_null($arrayFiltro['estado']))
        {
            $strBody =$strBody."and ipc.ESTADO_PAGO in (:estado) ";
        }

        if(!is_null($arrayFiltro['tipoDocumento']))
        {
            $strBody =$strBody."and atdf.ID_TIPO_DOCUMENTO in(:tipoDocumento) ";
        }
		
		if(!empty($arrayFiltro['fechaInicio']) && !empty($arrayFiltro['fechaFin']))
        {
            $strBody =$strBody."and ipc.FE_CREACION between TO_TIMESTAMP(:fechaInicio, 'DD-MM-YYYY hh24:mi:ss')"
                ." and TO_TIMESTAMP(:fechaFin, 'DD-MM-YYYY hh24:mi:ss') ";
        }


		$strBody =$strBody.") ";

		$strBody =$strBody."SELECT tip.*,acpn.NOMBRE_CANAL_PAGO_LINEA, ab.DESCRIPCION_BANCO, afp.DESCRIPCION_FORMA_PAGO, ";
		$strBody =$strBody."ipa.LOGIN,ip.IDENTIFICACION_CLIENTE,COALESCE(ip.RAZON_SOCIAL,CONCAT(CONCAT(ip.NOMBRES,' '),ip.APELLIDOS)) as NOMBRE_COMPLETO ";
		$strBody =$strBody."FROM TMP_PAGOS tip ";
		
		$strBody =$strBody."LEFT JOIN DB_FINANCIERO.INFO_PAGO_LINEA ipn ON ipn.ID_PAGO_LINEA=tip.PAGO_LINEA_ID ";
        $strBody =$strBody."LEFT JOIN DB_FINANCIERO.ADMI_CANAL_PAGO_LINEA acpn ON acpn.ID_CANAL_PAGO_LINEA=ipn.CANAL_PAGO_LINEA_ID ";
		
		$strBody =$strBody."LEFT JOIN DB_GENERAL.ADMI_BANCO_TIPO_CUENTA abtc ON abtc.ID_BANCO_TIPO_CUENTA=tip.BANCO_TIPO_CUENTA_ID ";
        $strBody =$strBody."LEFT JOIN DB_GENERAL.ADMI_BANCO ab ON ab.ID_BANCO=abtc.BANCO_ID ";
		
		$strBody =$strBody."LEFT JOIN DB_GENERAL.ADMI_FORMA_PAGO afp on afp.ID_FORMA_PAGO=tip.FORMA_PAGO_ID ";
		
		$strBody =$strBody."LEFT JOIN DB_COMERCIAL.INFO_PUNTO ipa on ipa.ID_PUNTO=tip.PUNTO_ID ";
		$strBody =$strBody."LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper on iper.ID_PERSONA_ROL=ipa.PERSONA_EMPRESA_ROL_ID ";
		$strBody =$strBody."LEFT JOIN DB_COMERCIAL.INFO_PERSONA ip on ip.ID_PERSONA=iper.PERSONA_ID ";
	
		if(!is_null($arrayFiltro['ciclo']))
        {
           
            $strBody = $strBody.",DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC iperc, DB_COMERCIAL.ADMI_CARACTERISTICA ac ";
        }
        $strBody = $strBody."WHERE 1=1 ";
        if(!is_null($arrayFiltro['ciclo']))
        { 
            $strCiclo = implode(", ", $arrayFiltro['ciclo']);
            $strBody = $strBody."and iper.ID_PERSONA_ROL=iperc.PERSONA_EMPRESA_ROL_ID and iperc.CARACTERISTICA_ID=ac.ID_CARACTERISTICA ";
            $strBody = $strBody."and ac.DESCRIPCION_CARACTERISTICA='CICLO_FACTURACION' ";
            $strBody = $strBody."and DECODE( TRANSLATE(iperc.VALOR,'0123456789',' '), NULL, iperc.VALOR,-99) in(".$strCiclo.") ";
        }
		
		 if(!is_null($arrayFiltro['banco']))
        {
            $strBody =$strBody."and ab.ID_BANCO in (:banco) "; 
        }

        if(!is_null($arrayFiltro['tipoPago']))
        {
            $strBody =$strBody."and afp.ID_FORMA_PAGO in (:tipoPago) "; 
        }

        if(!is_null($arrayFiltro['canalPago']))
        {
            $strBody =$strBody."and acpn.ID_CANAL_PAGO_LINEA in (:canalPago) "; 
        }
		
		if(!empty($arrayFiltro['login']))
        {
            $strBody =$strBody."and ipa.LOGIN= :login ";
        }

        $strCompleto = $strBody;

        $objRsm->addScalarResult('ID_PAGO', 'id', 'integer');
        $objRsm->addScalarResult('PUNTO_ID', 'puntoId', 'integer');
        $objRsm->addScalarResult('NUMERO_PAGO', 'numeroPago', 'string');
        $objRsm->addScalarResult('VALOR_TOTAL', 'valorTotal', 'float');
        $objRsm->addScalarResult('USR_CREACION', 'usrCreacion', 'string');
        $objRsm->addScalarResult('FE_CREACION', 'feCreacion', 'string');
        $objRsm->addScalarResult('LOGIN', 'login', 'string');
        $objRsm->addScalarResult('IDENTIFICACION_CLIENTE', 'identificacionCliente', 'string');
        $objRsm->addScalarResult('ESTADO_PAGO', 'estadoPago', 'string');
        $objRsm->addScalarResult('NOMBRE_COMPLETO', 'nombreCompleto', 'string');
        $objRsm->addScalarResult('NOMBRE_CANAL_PAGO_LINEA', 'canal', 'string');
        $objRsm->addScalarResult('DESCRIPCION_FORMA_PAGO', 'tipoPago', 'string');
        $objRsm->addScalarResult('NOMBRE_TIPO_DOCUMENTO', 'tipoDocumento', 'string');
        $objRsm->addScalarResult('DESCRIPCION_BANCO', 'banco', 'string');

        $objNtvQuery->setParameter('idEmpresa', $arrayFiltro['idEmpresa']);

        if(!empty($arrayFiltro['fechaInicio']) && !empty($arrayFiltro['fechaFin']))
        {
            $objNtvQuery->setParameter('fechaInicio', $arrayFiltro['fechaInicio']. ' 00:00:00');
            $objNtvQuery->setParameter('fechaFin', $arrayFiltro['fechaFin']. ' 23:59:00');
        }

        if(!empty($arrayFiltro['login']))
        {
            $objNtvQuery->setParameter('login', $arrayFiltro['login']);
        }

        if(!empty($arrayFiltro['numeroPago']))
        {
            $objNtvQuery->setParameter('numeroPago', $arrayFiltro['numeroPago']);
        }

        if(!empty($arrayFiltro['usuCreacion']))
        {
            $objNtvQuery->setParameter('usuCreacion', $arrayFiltro['usuCreacion']);
        }

        if(!is_null($arrayFiltro['banco']))
        {
            $objNtvQuery->setParameter('banco', $arrayFiltro['banco']);
        }

        if(!is_null($arrayFiltro['tipoPago']))
        {
            $objNtvQuery->setParameter('tipoPago', $arrayFiltro['tipoPago']);
        }

        if(!is_null($arrayFiltro['canalPago']))
        {
            $objNtvQuery->setParameter('canalPago', $arrayFiltro['canalPago']);
        }

        if(!is_null($arrayFiltro['tipoDocumento']))
        {
            $objNtvQuery->setParameter('tipoDocumento', $arrayFiltro['tipoDocumento']);
        }

        if(!is_null($arrayFiltro['estado']))
        {
            $objNtvQuery->setParameter('estado', $arrayFiltro['estado']);
        }
        $objNtvQuery->setSQL($strCompleto);
        $arrayDatos = $objNtvQuery->getResult();
        
        $arrayResponse = array('error' => false,
                    'msg' => 'OK','pagos' => $arrayDatos);
        return $arrayResponse;
    }


    /**
     * Documentación para el método 'getListPaymentExcel'.
     *
     * Función que retorna lista de pagos de un excel de pagos
     * 
     * @param $arrayRequest[urlFile] Url de archivo
     * @param $arrayRequest[idEmpresa] id de empresa.
     * 
     * @return $arrayResultado pagos[id ,puntoId, numeroPago, valorTotal, usrCreacion, feCreacion, login, 
     *      identificacionCliente, estadoPago, nombreCompleto, canal, tipoPago, tipoDocumento]
     *
     * @author Wilson Quinto <wquinto@telconet.ec>
     * @version 1.0 31-03-2021 
     */
    public function getListPaymentExcel($arrayRequest)
    {
        $arrayResponse = array();
        $intCodSalida = 1;
        $strMsjSalida = 'Err';
        try
        {
            if (!empty($arrayRequest))
            {
                $strSql = "BEGIN DB_FINANCIERO.FNCK_ANULAR_PAGO.P_OBTENER_PAGO_EXCEL(:idEmpresa, :urlFile,"
                ." :pagosExcel, :intCodSalida, :strMsjSalida); END;";

                //Obtiene la conexion
                $arrayConnParams = $this->getEntityManager()->getConnection()->getParams();

                $objConn = oci_connect($arrayConnParams['user'],
                    $arrayConnParams['password'],
                    $arrayConnParams['dbname']);

                //Prepara la sentencia
                $objRsm = oci_parse($objConn, $strSql);

                //Declaro variable tipo CURSOR
                $objCursor = oci_new_cursor($objConn); 
                
                //Enlazo las variables enviadas como parametros con las variables de entrada y salida del Procedimiento
                oci_bind_by_name($objRsm, ':idEmpresa', $arrayRequest['idEmpresa']) ;
                oci_bind_by_name($objRsm, ':urlFile', $arrayRequest['urlFile']) ;
                oci_bind_by_name($objRsm, ":pagosExcel",   $objCursor, -1, OCI_B_CURSOR);
                oci_bind_by_name($objRsm, ':intCodSalida', $intCodSalida, 20) ;
                oci_bind_by_name($objRsm, ':strMsjSalida', $strMsjSalida, 2000) ;

                //Ejecutamos la sentencia
                oci_execute($objRsm);
                oci_execute($objCursor);

                $arrayPagos=array();
                while( $arrayResultadoCursor = oci_fetch_array($objCursor, OCI_ASSOC + OCI_RETURN_NULLS) )
                {
                    $intPagoId = ( isset($arrayResultadoCursor['ID_PAGO'])
                    || !empty($arrayResultadoCursor['ID_PAGO']) )
                                           ? $arrayResultadoCursor['ID_PAGO'] : '';
                    $intPuntoId = ( isset($arrayResultadoCursor['PUNTO_ID'])
                    || !empty($arrayResultadoCursor['PUNTO_ID']) )
                                           ? $arrayResultadoCursor['PUNTO_ID'] : '';
                    $strNumeroPago = ( isset($arrayResultadoCursor['NUMERO_PAGO'])
                    || !empty($arrayResultadoCursor['NUMERO_PAGO']) )
                                           ? $arrayResultadoCursor['NUMERO_PAGO'] : '';
                    $floatPagoValor = ( isset($arrayResultadoCursor['VALOR_TOTAL'])
                    || !empty($arrayResultadoCursor['VALOR_TOTAL']) )
                                           ? $arrayResultadoCursor['VALOR_TOTAL'] : '';
                    $strPagoLogin = ( isset($arrayResultadoCursor['LOGIN'])
                    || !empty($arrayResultadoCursor['LOGIN']) )
                                           ? $arrayResultadoCursor['LOGIN'] : '';
                    $strPagoIentificacionCliente = ( isset($arrayResultadoCursor['IDENTIFICACION_CLIENTE'])
                    || !empty($arrayResultadoCursor['IDENTIFICACION_CLIENTE']) )
                                           ? $arrayResultadoCursor['IDENTIFICACION_CLIENTE'] : '';
                    $strPagoNombreCliente = ( isset($arrayResultadoCursor['NOMBRE_COMPLETO'])
                    || !empty($arrayResultadoCursor['NOMBRE_COMPLETO']) )
                                           ? $arrayResultadoCursor['NOMBRE_COMPLETO'] : '';
                    $strPagoUsrCreacion = ( isset($arrayResultadoCursor['USR_CREACION'])
                    || !empty($arrayResultadoCursor['USR_CREACION']) )
                                           ? $arrayResultadoCursor['USR_CREACION'] : '';
                    $strPagoTipo = ( isset($arrayResultadoCursor['DESCRIPCION_FORMA_PAGO'])
                    || !empty($arrayResultadoCursor['DESCRIPCION_FORMA_PAGO']) )
                                           ? $arrayResultadoCursor['DESCRIPCION_FORMA_PAGO'] : '';
                    $strPagoDocumento = ( isset($arrayResultadoCursor['NOMBRE_TIPO_DOCUMENTO'])
                    || !empty($arrayResultadoCursor['NOMBRE_TIPO_DOCUMENTO']) )
                                            ? $arrayResultadoCursor['NOMBRE_TIPO_DOCUMENTO'] : '';
                    $strPagoCanal = ( isset($arrayResultadoCursor['NOMBRE_CANAL_PAGO_LINEA'])
                    || !empty($arrayResultadoCursor['NOMBRE_CANAL_PAGO_LINEA']) )
                                           ? $arrayResultadoCursor['NOMBRE_CANAL_PAGO_LINEA'] : '';
                    $strPagoEstado = ( isset($arrayResultadoCursor['ESTADO_PAGO'])
                    || !empty($arrayResultadoCursor['ESTADO_PAGO']) )
                                           ? $arrayResultadoCursor['ESTADO_PAGO'] : '';
                    $strBanco = ( isset($arrayResultadoCursor['DESCRIPCION_BANCO'])
                    || !empty($arrayResultadoCursor['DESCRIPCION_BANCO']) )
                                           ? $arrayResultadoCursor['DESCRIPCION_BANCO'] : '';
                    $intError = ( isset($arrayResultadoCursor['ERROR'])
                    || !empty($arrayResultadoCursor['ERROR']) )
                                           ? $arrayResultadoCursor['ERROR'] : '';
                    
                    if(isset($arrayResultadoCursor['FE_CREACION'])
                    || !empty($arrayResultadoCursor['FE_CREACION']) )
                    {
                        $strPagoFecha =$arrayResultadoCursor['FE_CREACION'];
                    }else
                    {
                        $strPagoFecha ='';
                    }
                    $arrayPagos[] = array(
                        "id" => $intPagoId ,
                        "puntoId" => $intPuntoId ,
                        "numeroPago" => $strNumeroPago,
                        "valorTotal" => $floatPagoValor,
                        "usrCreacion" => $strPagoUsrCreacion,
                        "feCreacion" => $strPagoFecha,
                        "login" => $strPagoLogin,
                        "identificacionCliente" => $strPagoIentificacionCliente,
                        "estadoPago" => $strPagoEstado,
                        "nombreCompleto" => $strPagoNombreCliente ,
                        "canal" => $strPagoCanal,
                        "tipoPago" => $strPagoTipo,
                        "tipoDocumento" => $strPagoDocumento,
                        "banco" => $strBanco,
                        "error" => $intError);
                    
                    
                }

                $arrayResponse = array('error' => false,
                    'msg' => $strMsjSalida,'pagos' => $arrayPagos);
            }
        }
        catch(\Exception $ex)
        {
            error_log('Error en InfoPagoCabRepository - getListPaymentExcel: '. $ex->getMessage());
            $arrayResponse = array('error' => true,
            'msg'=>'Error en InfoPagoCabRepository - getListPaymentExcel: '. $ex->getMessage());
        }

        return $arrayResponse;
    }
    
    /**
     * Documentación para el método 'getPagosPorDetallePagoAutId'.
     *
     * Función que retorna lista de pagos asociados a detalles de estado de cuenta procesados.
     * 
     * @param intIdPagAutDet[intIdPagAutDet] Id del detalle de estado de cuenta
     * @param intIdPagAutDet[strOpcion]      Opción de donde es invocada conaulta DEP-Depósitos  RET-Retenciones
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 20-05-2022 
     */    
	public function getPagosPorDetallePagoAutId($arrayParametros)
    {
        $intIdPagAutDet   = $arrayParametros['intIdPagAutDet'];
        $strOpcion        = $arrayParametros['strOpcion'];
        error_log('ID DET  '.$intIdPagAutDet.' OPC '.$strOpcion);
        $strQuery         = "SELECT DISTINCT(cab.id)     as idPago,
                                    ofi.nombreOficina    as oficina, 
                                    cab.puntoId,                                    
                                    cab.numeroPago       as numeroPago,
                                    pto.login,
                                    cab.feCreacion       as fechaCreacion, 
                                    cab.usrCreacion      as usrCreacion, 
                                    cab.valorTotal, 
                                    cab.estadoPago, 
                                    td.codigoTipoDocumento as tipoDocumento
                                    FROM 
                                    schemaBundle:InfoPagoCab cab,
                                    schemaBundle:InfoPagoDet det,
                                    schemaBundle:InfoDocumentoFinancieroCab fac,
                                    schemaBundle:InfoOficinaGrupo ofi,
                                    schemaBundle:InfoPunto pto,
                                    schemaBundle:AdmiTipoDocumentoFinanciero td                
                                    WHERE cab.id = det.pagoId AND ";
        
        if($strOpcion === 'DEP')
        {
            $strQuery .=  " cab.detallePagoAutomaticoId = :intIdPagAutDet AND ";
        }
        else if($strOpcion === 'RET')
        {
            $strQuery .=  " det.referenciaDetPagAutId = :intIdPagAutDet AND ";
        }        
		$strQuery .=  " det.referenciaId    = fac.id AND
                        cab.oficinaId       = ofi.id AND
                        cab.puntoId         = pto.id AND
                        cab.tipoDocumentoId = td.id  ";

		$objQuery = $this->_em->createQuery();
        $objQuery->setParameter('intIdPagAutDet', $intIdPagAutDet);
        $objQuery->setDQL($strQuery);
        $arrayListPagos = $objQuery->getResult();
        
        return $arrayListPagos;	
	}  
    
        
    /**
     * Busca facturas pendientes segun idPunto
     * @param integer $idPunto
     * @return array (Retorna arreglo con registros encontrados)
     * 
     * @author Kevin Villegas <kmvillegas@telconet.ec>
     * @version 1.0 03-10-2022 
     */
    public function findFacturasPendientesxPunto($intIdPunto)
    {   
        $strQuery = $this->_em->createQuery("SELECT faCab
        FROM 
                schemaBundle:InfoDocumentoFinancieroCab faCab, 
                schemaBundle:AdmiTipoDocumentoFinanciero td
        WHERE faCab.tipoDocumentoId=td.id AND 
                td.codigoTipoDocumento in (:tiposDocumento) AND
                faCab.puntoId=:puntoId AND 
                faCab.estadoImpresionFact not in (:estados) 
                order by faCab.numeroFacturaSri ASC");
                
        $strTiposDocumentos=array('FAC','FACP','ND','NDI');
        $strEstados=array('Cerrado','Pendiente' , 'Anulado', 'Anulada','Inactivo','Inactiva',
            'Rechazada','Rechazado','null','PendienteError','PendienteSri','Eliminado');
        $strQuery->setParameter('estados',$strEstados);
        $strQuery->setParameter('tiposDocumento',$strTiposDocumentos);
        $strQuery->setParameter('puntoId',$intIdPunto); 
        $arrayDatos = $strQuery->getResult();
        return $arrayDatos;
    }
    
}
