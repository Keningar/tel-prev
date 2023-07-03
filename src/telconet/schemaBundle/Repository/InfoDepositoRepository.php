<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoDepositoRepository extends EntityRepository
{
    
    
    /**
    * Documentación para el método 'getResultadoDepositos'.
    * Esta funcion obtiene los depositos creados segun los parametros recibidos
    *
    * @param  Array $parametros   tiene los parametros para la funcion que son los siguientes:
    *                             fechaDesde : fecha inicio de creacion o proceso del deposito
    *                             fechaHasta : fecha fin de creacion o proceso del deposito
    *                             comprobante: numero de comprobante del deposito
    *                             tipoFecha  : define si la fecha es de creacion o de proceso
    *                             limit      : indica cantidad de registros del query
    *                             start      : indica desde que registro inicia el query
    * @return Integer $out_Resultado Retorna arreglo con la informacion
    * @author Andres Montero <amontero@telconet.ec>
    * @version 1.0 20-01-2016
    * costoQuery =7
    */    
    public function getResultadoDepositos($arrayParametros)
    {
        $fechaDesde           = $arrayParametros['fechaDesde'];
        $fechaHasta           = $arrayParametros['fechaHasta'];
        $comprobante          = $arrayParametros['comprobante'];
        $tipoFecha            = $arrayParametros['tipoFecha'];
        $estado               = $arrayParametros['estado'];
        $limit                = $arrayParametros['limit'];
        $start                = $arrayParametros['start'];  
        $empresaId            = $arrayParametros['empresaId'];
        $oficinaId            = $arrayParametros['oficinaId'];
        $criterio_estado      = '';
        $criterio_fecha_desde = '';
        $criterio_fecha_hasta = '';
        $criterio_comprobante = '';
        $sql                  = "SELECT a FROM schemaBundle:InfoDeposito a WHERE ";
        $sqlCount             = "SELECT COUNT(a.id) FROM schemaBundle:InfoDeposito a WHERE ";
        $query                = $this->_em->createQuery();
        $queryCount           = $this->_em->createQuery();
        $criterio_empresa=" a.empresaId=:empresaId AND ";
        $query->setParameter('empresaId',$empresaId);
        $queryCount->setParameter('empresaId',$empresaId);
        if ($estado)
        {
            $criterio_estado=" a.estado=:estado AND ";
            $query->setParameter('estado',$estado);
            $queryCount->setParameter('estado',$estado);
        }
        if ($oficinaId)
        {
            $criterio_estado=" a.oficinaId=:oficinaId AND ";
            $query->setParameter('oficinaId',$oficinaId);
            $queryCount->setParameter('oficinaId',$oficinaId);
        }        
        if($tipoFecha=="c")
        {
            if ($fechaDesde)
            {
                $fechaD               = date("Y/m/d H:i:s", strtotime($fechaDesde." 00:00:00"));			
                $fechaDesde           = $fechaD ;
                $criterio_fecha_desde = " a.feCreacion >= :feDesde AND ";
                $query->setParameter('feDesde',$fechaDesde);
                $queryCount->setParameter('feDesde',$fechaDesde);
            }
            if($fechaHasta)
            {
                $fechaH               = date("Y/m/d H:i:s", strtotime($fechaHasta." 23:59:59"));			             
                $fechaHasta           = $fechaH;
                $criterio_fecha_hasta = " a.feCreacion <= :feHasta AND ";
                $query->setParameter('feHasta',$fechaHasta);   
                $queryCount->setParameter('feHasta',$fechaHasta);   
            }
        }
        elseif($tipoFecha=="p")
        {
            if ($fechaDesde)
            {
                $fechaD               = date("Y/m/d", strtotime($fechaDesde));			
                $fechaDesde           = $fechaD ;
                $criterio_fecha_desde = " a.feProcesado >= :feDesde AND ";
                $query->setParameter('feDesde',$fechaDesde);
                $queryCount->setParameter('feDesde',$fechaDesde); 
            }
            if($fechaHasta)
            {
                $fechaH               = date("Y/m/d", strtotime($fechaHasta));			             
                $fechaHasta           = $fechaH;
                $criterio_fecha_hasta = " a.feProcesado <= :feHasta AND ";
                $query->setParameter('feHasta',$fechaHasta);
                $queryCount->setParameter('feHasta',$fechaHasta);
            }
        }
        if ($comprobante)
        {
            $criterio_comprobante = " a.noComprobanteDeposito like :comprobante AND ";
            $query->setParameter('comprobante', '%'.$comprobante.'%');
            $queryCount->setParameter('comprobante', '%'.$comprobante.'%');
        }

        $sql      = $sql.$criterio_fecha_desde.$criterio_fecha_hasta.$criterio_comprobante.$criterio_estado.
            $criterio_empresa." 1=1 order by a.feCreacion DESC";
        $sqlCount = $sqlCount.$criterio_fecha_desde.$criterio_fecha_hasta.$criterio_comprobante.$criterio_estado.$criterio_empresa." 1=1 ";
        $query->setDQL($sql);
        $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        if($datos)
        {
            $queryCount->setDQL($sqlCount);
            $total = $queryCount->getSingleScalarResult();
        }
        $resultado['registros'] = $datos;
        $resultado['total']     = $total;
        return $resultado;
    }
    
    /**
     * Documentación para el método 'getJSONDepositos'.
     * 
     * Funcion que retorna los depositos creados   
     * 
     * @param array $arrayParametros ['fechaDesde' :       Fecha inicio de creacion o proceso del deposito,
     *                                'fechaHasta' :       Fecha fin de creacion o proceso del deposito,
     *                                'comprobante':       Numero de comprobante del deposito,
     *                                'tipoFecha'  :       Define si la fecha es de creacion o de proceso,
     *                                'limit'      :       Indica cantidad de registros del query,
     *                                'start'      :       Indica desde que registro inicia el query,
     *                                'empresaId'  :       Empresa del usuario en session,
     *                                'oficinaId'  :       Oficina del usuario en session,
     *                                'estado'     :       Estado de los depositos consultados,
     *                                'strPrefijoEmpresa': Prefijo de la empresa en sessión
     * 
     * @return string $strJson
     *   
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.0
     * @since 20-01-2016  
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 16-03-2017 - Se modifica la función para validar si un depósito en estado 'Pendiente' puede ser procesado. Adicional se añade el
     *                           control y manejo de excepciones.
     * 
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 04-07-2017
     * Se modifica la función para validar que la fecha de creación no sea nula si el estado es diferente de 'Pendiente' 
     */      
    public function getJSONDepositos($arrayParametros, $container = null)
    {
        $strJson = json_encode( array('total' => 0, 'depositos' => array()) );
        
        try
        {
            $arrayResultado = $this->getResultadoDepositos($arrayParametros);
            $datos          = $arrayResultado['registros'];
            $total          = $arrayResultado['total'];
            $oficina        = null;
            
            foreach ($datos as $datos)
            {
                $urlPagos     = $container->get('router')->generate('infodeposito_excelpagos_por_deposito', array('intIdDeposito' => $datos->getId()));
                $fechaProcesa = '';
                if($datos->getFeProcesado())
                {
                    $fechaProcesa=date_format($datos->getFeProcesado(), "d/m/Y G:i");
                }    
                $nombreBanco = "";
                $cuenta      = "";
                if ($datos->getBancoNafId()!=null)
                {
                    $objBancoCtaContable=$this->_em->getRepository('schemaBundle:AdmiBancoCtaContable')->find($datos->getBancoNafId());
                    $objBancoTipoCuenta=$this->_em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($objBancoCtaContable->getBancoTipoCuentaId());
                    $nombreBanco=$objBancoTipoCuenta->getBancoId()->getDescripcionBanco();
                    $cuenta=$objBancoCtaContable->getNoCta();
                }
                elseif ($datos->getCuentaContableId())
                {
                    $objCtaContable = $this->_em->getRepository('schemaBundle:AdmiCuentaContable')->find($datos->getCuentaContableId());
                    $nombreBanco    = $objCtaContable->getDescripcion();
                    $cuenta         = $objCtaContable->getNoCta();
                }
                if($datos->getOficinaId())
                {    
                    $objOficina = $this->_em->getRepository('schemaBundle:InfoOficinaGrupo')->find($datos->getOficinaId());
                    $oficinaArr = explode("-",$objOficina->getNombreOficina());
                    $oficina    = $oficinaArr[1]; 
                }

                /**
                 * Bloque que valida si el depósito creado puede ser procesado
                 */
                $objFechaCreacion  = $datos->getFeCreacion();
                $strEstadoDeposito = $datos->getEstado();
                $strFechaCreacion = '';
                if($datos->getFeCreacion())
                {
                    $strFechaCreacion  = strval(date_format($objFechaCreacion, "d/m/Y G:i"));
                }
                $strFechaValidar   = '';
                $strPuedeProcesar  = 'N';
                
                if( is_object($objFechaCreacion) && $strEstadoDeposito == 'Pendiente' )
                {
                    $strFechaCreacion  = strval(date_format($objFechaCreacion, "d/m/Y G:i"));
                    $strFechaValidar   = $objFechaCreacion->format('d-m-Y');
                    $strPrefijoEmpresa = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) ) 
                                         ? $arrayParametros['strPrefijoEmpresa'] : '';
                    
                    $arrayParametrosFechaDeposito = array('strFechaValidar'     => $strFechaValidar,
                                                          'strPrefijoEmpresa'   => $strPrefijoEmpresa,
                                                          'strParametroValidar' => 'PROCESAR_DEPOSITO');
                    $arrayRespuestaValidacion     = $this->validarFechaDeposito($arrayParametrosFechaDeposito);

                    if( isset($arrayRespuestaValidacion['strMensajeError']) && !empty($arrayRespuestaValidacion['strMensajeError']) )
                    {
                        throw new \Exception($arrayRespuestaValidacion['strMensajeError']);
                    }
                    else
                    {
                        $strPuedeProcesar = ( isset($arrayRespuestaValidacion['strRespuestaValidacion'])
                                              && !empty($arrayRespuestaValidacion['strRespuestaValidacion']) )
                                            ? $arrayRespuestaValidacion['strRespuestaValidacion'] : 'N';
                    }//( isset($arrayRespuestaValidacion['strMensajeError']) && !empty($arrayRespuestaValidacion['strMensajeError']) )
                }//( is_object($objFechaCreacion) )

                $arreglo[] = array( 'id'               => $datos->getId(),
                                    'valor'            => $datos->getValor(),
                                    'realizadoPor'     => $datos->getUsrCreacion(),
                                    'nombreBanco'      => $nombreBanco,
                                    'banco'            => $datos->getBancoNafId(),
                                    'cuenta'           => $cuenta,
                                    'comprobante'      => $datos->getNoComprobanteDeposito(),
                                    'fechaProcesa'     => strval($fechaProcesa),
                                    'fechaCreacion'    => $strFechaCreacion,
                                    'usuarioCreacion'  => $datos->getUsrCreacion(),
                                    'estado'           => $strEstadoDeposito,
                                    'oficina'          => $oficina,  
                                    'linkPagos'        => $urlPagos,
                                    'strPuedeProcesar' => $strPuedeProcesar );
            }
            
            if (!empty($arreglo))
            {    
                $strJson = json_encode(array('total' => $total, 'depositos' => $arreglo));
            }    
            else 
            {
                $arreglo[] = array();
                $strJson = json_encode(array('total' => $total, 'depositos' => $arreglo));
            }
        }
        catch(\Exception $e)
        {
            error_log('[ERROR InfoDepositoRepository.getJSONDepositos]: '.$e->getMessage());
            
            throw ($e);
        }
            
        return $strJson;
	}


    /**
     * Documentación para el método 'validarFechaDeposito'.
     * 
     * Método que invoca el procedimiento 'DB_FINANCIERO.FNCK_CONSULTS.P_VALIDAR_FECHA_DEPOSITO' para validar hasta que fecha se podrá ingresar
     * pagos, anticipos y/o procesar depósitos
     *
     * @param  array  $arrayParametros ['strFechaValidar'     => 'Fecha contra la cual se va a validar la información',
     *                                  'strParametroValidar' => 'Valor1 del detalle del parámetro 'VALIDACIONES_PROCESOS_CONTABLES'',
     *                                  'strPrefijoEmpresa'   => 'Prefijo de la empresa en sessión']
     * 
     * @return array $arrayResultados ['strRespuestaValidacion' => 'Respuesta de la validacion realizada. Los valores son: 'S' puesto continuar con
     *                                                              el proceso, caso contrario 'N'',
     *                                 'strMensajeError'        => 'Mensaje de error en caso de existir']
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 16-03-2017
     */
    public function validarFechaDeposito($arrayParametros)
    {
        $arrayResultados = array('strRespuestaValidacion' => '', 'strMensajeError' => '');
        
        try
        {
            $strRespuestaValidacion = '';//Se inicializa variable para evitar el NOTICE en el log de error
            $strRespuestaValidacion = str_pad($strRespuestaValidacion, 2, " ");
            $strMensajeError        = '';//Se inicializa variable para evitar el NOTICE en el log de error
            $strMensajeError        = str_pad($strMensajeError, 4000, " ");
            
            $strFechaValidar     = ( isset($arrayParametros['strFechaValidar']) && !empty($arrayParametros['strFechaValidar']) ) 
                                   ? $arrayParametros['strFechaValidar'] : '';
            $strParametroValidar = ( isset($arrayParametros['strParametroValidar']) && !empty($arrayParametros['strParametroValidar']) ) 
                                   ? $arrayParametros['strParametroValidar'] : '';
            $strPrefijoEmpresa   = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) ) 
                                   ? $arrayParametros['strPrefijoEmpresa'] : '';
            
            if( !empty($strFechaValidar) && !empty($strParametroValidar) && !empty($strPrefijoEmpresa) )
            {
                $strSql = "BEGIN DB_FINANCIERO.FNCK_CONSULTS.P_VALIDAR_FECHA_DEPOSITO( :strFechaValidar, ".
                                                                                      ":strParametroValidar, ".
                                                                                      ":strPrefijoEmpresa, ".
                                                                                      ":strRespuestaValidacion, ".
                                                                                      ":strMensajeError ); END;";
                $objStmt = $this->_em->getConnection()->prepare($strSql);
                $objStmt->bindParam('strFechaValidar',        $strFechaValidar);
                $objStmt->bindParam('strParametroValidar',    $strParametroValidar);
                $objStmt->bindParam('strPrefijoEmpresa',      $strPrefijoEmpresa);
                $objStmt->bindParam('strRespuestaValidacion', $strRespuestaValidacion);
                $objStmt->bindParam('strMensajeError',        $strMensajeError);
                $objStmt->execute();
                
                $arrayResultados['strRespuestaValidacion'] = $strRespuestaValidacion;
                $arrayResultados['strMensajeError']        = $strMensajeError;
            }//( !empty($strFechaValidar) && !empty($strParametroValidar) && !empty($strPrefijoEmpresa) )
            else
            {
                throw new \Exception('Todos los parámetros son obligatorios para poder validar la fecha de depósito.');
            }
        }
        catch(\Exception $e)
        {
            error_log('[ERROR InfoDepositoRepository.validarFechaDeposito]: '.$e->getMessage());
            
            $arrayResultados['strRespuestaValidacion'] = 'N';
            $arrayResultados['strMensajeError']        = 'Hubo un problema al validar la fecha de depósito';
            
            throw ($e);
        }
            
        return $arrayResultados;
    }
	
	public function getDeposito($id_deposito){
		
		/*
		 * select id.id_deposito,id.no_cuenta_banco_naf,id.no_cuenta_contable_naf,id.no_comprobante_deposito,id.valor,id.usr_creacion,ab.descripcion_banco,usr_procesa,id.fe_deposito,id.fe_procesado,iper.oficina_id,iog.nombre_oficina
			from info_deposito id
			join admi_banco_cta_contable abcc on abcc.id_banco_cta_contable=id.banco_naf_id
			join admi_banco_tipo_cuenta abtc on abtc.id_banco_tipo_cuenta=abcc.banco_tipo_cuenta_id
			join admi_banco ab on ab.id_banco=abtc.banco_id
			join info_persona ip on ip.login=id.usr_creacion
			join info_persona_empresa_rol iper on iper.persona_id=ip.id_persona
			left join info_oficina_grupo iog on iog.id_oficina=iper.oficina_id
			where id.id_deposito=87;
		 * */
		 
		$query = $this->_em->createQuery("SELECT 
				ide.id,
				ide.noCuentaBancoNaf,
				ide.noCuentaContableNaf,
				ide.noComprobanteDeposito,
				ide.valor,
				ide.feProcesado as feDeposito,
				ide.usrProcesa,
				ab.descripcionBanco,
				iog.id as oficinaId,
				iog.nombreOficina
			FROM 
				schemaBundle:InfoDeposito ide,
				schemaBundle:AdmiBancoCtaContable abcc,
				schemaBundle:AdmiBancoTipoCuenta abtc,
				schemaBundle:AdmiBanco ab,
				schemaBundle:InfoPersona ip,
				schemaBundle:InfoPersonaEmpresaRol iper,
				schemaBundle:InfoOficinaGrupo iog
			WHERE 
				ide.bancoNafId=abcc.id
				and abtc.id=abcc.bancoTipoCuentaId
				and ab.id=abtc.bancoId
				and upper(trim(ip.login))=upper(trim(ide.usrProcesa))
				and iper.personaId=ip.id
				and iper.oficinaId=iog.id
				and ide.id=".$id_deposito);
		
		echo $query->getSQL();		
		$datos = $query->getResult();
		return $datos;
	}
	
	public function obtenerDepositosParaMigrarAA()
	{
		/*select * 
			from info_deposito id
			where id.fe_procesado between '01/04/13' and '30/04/13' and id.estado='Procesado';*/
		
		//Abril: 1/30
		//Mayo: 1/31
		
		$query = $this->_em->createQuery("select a 
			from schemaBundle:InfoDeposito a 
			where 
			a.feProcesado >= '".date('Y/m/d', strtotime('2013-08-01'))."'
			and a.feProcesado < '".date('Y/m/d', strtotime('2013-09-01'))."' 
			and a.estado='Procesado'
			order by a.feProcesado,a.id");


		$total=count($query->getResult());
		$datos = $query->getResult();
		//echo $query->getSQL();die;
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		
		return $resultado;
	}
    
    
    
    /**
    * Documentación para el método 'contabilizarDeposito'.
    * Este metodo anula el pago o anticipo
    *
    * @param  Integer $empresaCod     Obtiene el Id de la empresa
    * @param  Array   $arrDepositosId Obtiene los depositos que seran contabilizados
    * @return Integer $out_Resultado Retorna un ok si la actualizacion fue correcta y un Error en caso contrario.
    *
    * @author Andres Montero <amontero@telconet.ec>
    * @version 1.0 26-12-2015
    * 
    * Se recibe parámetro $objParametros para poder reutilizar la función insertError() en el repositorio
    * se cambia de nombre la variable $empresaCod por $intEmpresaCod y $arrDepositosId por $arrayDepositosId 
    * por problemas con SONAR.
    * @author Douglas Natha <dnatha@telconet.ec>
    * @version 1.1 07-11-2019
    * @since 1.0
    */
    public function contabilizarDeposito($intEmpresaCod, $arrayDepositosId, $objParametros)
    {
        $serviceUtil = $objParametros['serviceUtil'];

        for ($intI=0;$intI<count($arrayDepositosId);$intI++)
        {
            $out_msn_Error = null;
            $out_msn_Error = str_pad($out_msn_Error, 1000, " ");
            $out_Resultado = '[Proceso contable OK]';
            //llama al metodo que verifica si el pago o anticipo se puede anular, caso contrario Devuelve como  mensaje "Error"
            if($arrayDepositosId[$intI] != null)
            {
                $intIdDeposito=$arrayDepositosId[$intI]['idDeposito'];
                $intIdOficina =$arrayDepositosId[$intI]['idOficina'];

                $serviceUtil->insertError( 'Telcos+', 
                        'LOG DE EJECUCION DE PAGOS', 
                        'InfoDepositoRepository/contabilizarDeposito ' .
                        '- FNKG_CONTABILIZAR_DEPOSITOS.PROCESAR_DEPOSITO '.
                        'con los sgtes parametros... Codigo de empresa: ' . 
                         $intEmpresaCod . ', depositoId: '. $intIdDeposito . 
                         ', oficinaId: ' . $intIdOficina . ', msnError: ' . 
                         $out_msn_Error , 
                        'telcos', 
                        '127.0.0.1' );
                        
                $sql = "BEGIN FNKG_CONTABILIZAR_DEPOSITOS.PROCESAR_DEPOSITO(:empresaCod, :depositoId, :oficinaId, :msnError); END;";
                $stmt = $this->_em->getConnection()->prepare($sql);
                $stmt->bindParam('empresaCod', $intEmpresaCod );
                $stmt->bindParam('depositoId', $intIdDeposito);
                $stmt->bindParam('oficinaId', $intIdOficina);
                $stmt->bindParam('msnError', $out_msn_Error);
                $stmt->execute();

                $serviceUtil->insertError( 'Telcos+', 
                        'LOG DE EJECUCION DE PAGOS', 
                        'InfoDepositoRepository/contabilizarDeposito - DESPUES DE EJECUTAR: ' .
                        'FNKG_CONTABILIZAR_DEPOSITOS.PROCESAR_DEPOSITO '.
                        'con los sgtes parametros... Codigo de empresa: ' . 
                         $intEmpresaCod . ', depositoId: '. $intIdDeposito . 
                         ', oficinaId: ' . $intIdOficina . ', msnError: ' . 
                         $out_msn_Error , 
                        'telcos', 
                        '127.0.0.1' ); 
            }
            if(strtoupper($out_msn_Error)!='PROCESO OK')
            {
                $out_Resultado = '[Error en proceso contable:'.$out_msn_Error.']';
            }
        }
        return $out_Resultado;
    }   
    
    
    /**
    * Documentación para el método 'findPagosPorDebitoGeneral'.
    * Este obtiene los pagos que son parte de un deposito
    *
    * @param  Integer $intIdDeposito     Obtiene el Id del deposito
    * @return Array $datos               Retorna los pagos consultados en un arreglo
    *
    * @author Andres Montero <amontero@telconet.ec>
    * @version 1.0 25-05-2016
    * @author Edson Franco <efranco@telconet.ec>
    * @version 1.1 13-12-2016 - Se corrige para que retorne como 'valorTotal' el valor del detalle del pago del deposito consultado.
    * @author Edson Franco <efranco@telconet.ec>
    * @version 1.2 15-03-2017 - Se corrige método para retorna los pagos asociados a un depósito.
    */    
    public function findPagosPorDeposito($intIdDeposito)
    {
        $query = $this->_em->createQuery(
            "SELECT 
            tdf.codigoTipoDocumento, pcab.puntoId, 
            pcab.numeroPago, pdet.valorPago as valorTotal, pdet.referenciaId, pcab.estadoPago
            FROM 
            schemaBundle:InfoPagoCab pcab,  
            schemaBundle:InfoPagoDet pdet,
            schemaBundle:AdmiTipoDocumentoFinanciero tdf
            WHERE
            pcab.id=pdet.pagoId 
            AND pcab.tipoDocumentoId = tdf.id
            AND pdet.depositoPagoId  = :depositoPagoId ");
        
        $query->setParameter('depositoPagoId',$intIdDeposito);
        $datos = $query->getResult();
        return $datos;
    }    
}
