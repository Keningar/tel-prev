<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;


class InfoDebitoDetRepository extends EntityRepository
{
	public function findPorCabIdPorIdentificacionClientePorValor($debitoCabId,$identificacion,$valor){	

        $query = $this->_em->createQuery("SELECT dd
		FROM 
                schemaBundle:InfoDebitoDet dd, 
				schemaBundle:InfoPersonaEmpresaRol per, 
				schemaBundle:InfoPersona p
		WHERE 
                dd.debitoCabId=$debitoCabId AND
                dd.personaEmpresaRolId = per.id AND
				per.personaId=p.id AND
				dd.valorTotal=$valor AND
				p.identificacionCliente='$identificacion' AND dd.estado='Pendiente' ORDER BY dd.id DESC"); 
		$datos = $query->setFirstResult(0)->setMaxResults(1)->getResult();
		//echo $query->getSQL()."<br>";die;
	    //print_r($datos);die;
		return $datos;
	}
	public function findPorCabIdPorNombreCliente($debitoCabId,$nombreCliente){	

        $query = $this->_em->createQuery("SELECT dd
		FROM 
                schemaBundle:InfoDebitoDet dd, 
				schemaBundle:InfoPersonaEmpresaRol per, 
				schemaBundle:InfoPersona p
		WHERE 
                dd.debitoCabId=$debitoCabId AND
                dd.personaEmpresaRolId = per.id AND 
				CONCAT(LOWER(p.nombres),CONCAT(' ',LOWER(p.apellidos))) = '".strtolower(trim($nombreCliente))."' AND 
				dd.estado='Pendiente' ORDER BY dd.id DESC"); 
		$datos = $query->setFirstResult(0)->setMaxResults(1)->getResult();
		return $datos;
	}	
	public function findPorCabIdPorNumeroFactura($debitoCabId,$numFactura){	

        $query = $this->_em->createQuery("SELECT dd
		FROM 
                schemaBundle:InfoDebitoDet dd, 
				InfoPunto pto, 
				InfoDocumentoFinancieroCab fact
		WHERE 
                dd.debitoCabId=$debitoCabId AND
                dd.puntoId = pto.id AND
				fact.puntoId = pto.id AND
				fact.numeroFacturaSri='$numFactura'"); 
		$datos = $query->getResult();
		return $datos;
	}
	public function findPorCabIdPorCuentaClientePorValor($debitoCabId,$numeroCuenta,$valor){	

        $query = $this->_em->createQuery("SELECT dd
		FROM 
                schemaBundle:InfoDebitoDet dd		
		WHERE 
                dd.debitoCabId=$debitoCabId AND dd.estado='Pendiente' AND
				dd.valorTotal=$valor AND
				dd.numeroTarjetaCuenta='$numeroCuenta'  ORDER BY dd.id DESC"); 
		$datos = $query->setFirstResult(0)->setMaxResults(1)->getResult();
		//echo $query->getSQL();die;
		//print_r ($datos);
		return $datos;
	}	
	public function findPorCabIdPorReferencia($debitoCabId,$referencia){
		//echo "la referencia es:".$referencia;
		//echo "entro a findPorCabIdPorReferencia";
        $query = $this->_em->createQuery("SELECT dd
		FROM 
                schemaBundle:InfoDebitoDet dd		
		WHERE 
                dd.debitoCabId=".$debitoCabId ." AND
				dd.referencia='".$referencia."' AND dd.estado='Pendiente'"); 
		$datos = $query->getOneOrNullResult();
		//echo $query->getSQL();die;
		return $datos;
	}

	public function findPorCabIdPorReferenciaPorValor($debitoCabId,$referencia,$valor){
		//echo "la referencia es:".$referencia;
		//echo "entro a findPorCabIdPorReferencia";
        $query = $this->_em->createQuery("SELECT dd
		FROM 
                schemaBundle:InfoDebitoDet dd		
		WHERE 
                dd.debitoCabId=".$debitoCabId ." AND
				dd.referencia='".$referencia."' AND 
				dd.valorTotal='".$valor."' AND dd.estado='Pendiente'"); 
		//$datos = $query->getResult();
		$datos = $query->setFirstResult(0)->setMaxResults(1)->getResult();
		//echo $query->getSQL();die;
		return $datos;
	}

    /**
     * Documentación para funcion 'findDetallesDebitoPorDebitoGeneral'.
     * busca detalles de debitos
     * @param parametros (
     *     estado       => (estado del debito), 
     *     debitoGenId  => (id debito general),
     *     fechaDesde   => (fecha inicio), 
     *     fechaHasta   => (fecha fin), 
     *     limit        => (limite de registros en consulta),
     *     page         => (pagina donde se encuentra la consulta),
     *     start        => (registro inicio),
     *     banco        => (id de banco tipo cuenta),
     *     numeroCuenta => (numero de cuenta),
     *     numeroCedula => (numero de cedula),
     *     secret       => (el secret para desencriptar datos))
     * @return array $respuesta debitos encontrados
     */      
    public function findDetallesDebitoPorDebitoGeneral($parametros)
    {
        $estado       = $parametros['estado'];
        $debitoGenId  = $parametros['debitoGenId'];
        $fechaDesde   = $parametros['fechaDesde'];
        $fechaHasta   = $parametros['fechaHasta'];
        $limit        = $parametros['limit'];
        $start        = $parametros['start'];
        $banco        = $parametros['banco'];
        $numeroCuenta = $parametros['numeroCuenta'];
        $numeroCedula = $parametros['numeroCedula'];
        $secret       = $parametros['secret'];
        
        $rsm                    = new ResultSetMappingBuilder($this->_em);
        $criterio_estado        = '';
        $criterio_banco         = '';
        $criterio_numero_cuenta = '';	
        $criterio_numero_cedula = '';	
        $criterio_fecha_desde   = '';
        $criterio_fecha_hasta   = '';      
        $sql="SELECT 
                b.id_debito_det,
                bco.descripcion_banco,
                tc.descripcion_cuenta,
                p.razon_social,
                p.nombres,
                p.apellidos,
                FNKG_CONSULTA_DETALLES_DEBITOS.F_DESENCRIPTA_NUMERO_TARJ_CTA(b.NUMERO_TARJETA_CUENTA, :secretSelect) numero_tarjeta,
                p.identificacion_cliente,
                b.valor_total,
                b.fe_creacion,                
                b.estado,
                b.usr_creacion,
                b.observacion_rechazo,
                b.valor_debitado,
                CASE WHEN b.REFERENCIA IS NOT NULL THEN 
                    FNKG_CONSULTA_DETALLES_DEBITOS.F_DESENCRIPTA_NUMERO_TARJ_CTA(b.REFERENCIA, :secretSelect) 
                ELSE  
                    NULL
                END    
                as referencia                   
              FROM 
                  Info_Debito_Det b, 
                  Info_Debito_Cab c,
                  Info_Debito_General d,
                  Info_Persona_Empresa_Rol per,
                  Info_Persona p,
                  admi_banco_tipo_cuenta btc,
                  admi_banco bco,
                  admi_tipo_cuenta tc
              WHERE 
                  b.persona_Empresa_Rol_Id=per.id_persona_rol 
              AND per.persona_Id=p.id_persona 
              AND b.debito_Cab_Id= c.id_debito_cab 
              AND c.debito_General_Id=d.id_debito_general 
              AND c.banco_tipo_cuenta_id=btc.id_banco_tipo_cuenta
              AND btc.banco_id=bco.id_banco
              AND btc.tipo_cuenta_id=tc.id_tipo_cuenta
              AND ";
        $query = $this->_em->createNativeQuery(null,$rsm);
        $query->setParameter('debito_gen_id',$debitoGenId);
        $query->setParameter('secretSelect',$secret);
        $query->setParameter('secretWhere',$secret);

        if ($fechaDesde)
        {
            $fechaDesde           = date("Y/m/d", strtotime($fechaDesde));	
            $criterio_fecha_desde = " b.fe_Creacion >= :fe_desde AND ";
            $query->setParameter('fe_desde',$fechaDesde);
        }
        if($fechaHasta)
        {
            $fechaHasta           = date("Y/m/d", strtotime($fechaHasta));
            $criterio_fecha_hasta = " b.fe_Creacion <= :fe_hasta AND ";
            $query->setParameter('fe_hasta',$fechaHasta); 
        }                
        if ($estado)
        {       
            $criterio_estado=" b.estado = :estado AND ";
            $query->setParameter('estado',$estado);
        }                
        if($banco)
        {
            $criterio_banco=" c.banco_Tipo_Cuenta_Id= :banco AND ";
            $query->setParameter('banco',$banco);
        }
        if($numeroCuenta)
        {
            $criterio_numero_cuenta=
                " FNKG_CONSULTA_DETALLES_DEBITOS.F_DESENCRIPTA_NUMERO_TARJ_CTA(b.NUMERO_TARJETA_CUENTA, :secretWhere) like :numero_cuenta AND ";
            $query->setParameter('numero_cuenta', '%'.$numeroCuenta.'%');
        }	
        if($numeroCedula)
        {
           $criterio_numero_cedula=" p.identificacion_Cliente like :numero_cedula AND ";
           $query->setParameter('numero_cedula', '%'.$numeroCedula.'%');
        }	
        $sql.=$criterio_numero_cuenta.$criterio_numero_cedula.$criterio_banco.$criterio_estado.$criterio_fecha_desde.$criterio_fecha_hasta;
        $sql.=" d.id_debito_general= :debito_gen_id  order by b.fe_Creacion DESC ";
        $rsm->addScalarResult('ID_DEBITO_DET', 'idDebitoDet','integer');
        $rsm->addScalarResult('ESTADO', 'estado', 'string');
        $rsm->addScalarResult('DESCRIPCION_BANCO', 'descripcionBanco','string');
        $rsm->addScalarResult('DESCRIPCION_CUENTA', 'descripcionCuenta','string');
        $rsm->addScalarResult('RAZON_SOCIAL', 'razonSocial','string');
        $rsm->addScalarResult('NOMBRES', 'nombres','string');
        $rsm->addScalarResult('APELLIDOS', 'apellidos','string');
        $rsm->addScalarResult('NUMERO_TARJETA', 'numeroTarjeta','string');
        $rsm->addScalarResult('IDENTIFICACION_CLIENTE', 'identificacionCliente','string');
        $rsm->addScalarResult('VALOR_TOTAL', 'valorTotal','float');
        $rsm->addScalarResult('FE_CREACION', 'feCreacion','string');            
        $rsm->addScalarResult('OBSERVACION_RECHAZO', 'observacionRechazo','string');   
        $rsm->addScalarResult('USR_CREACION', 'usrCreacion','string');   
        $rsm->addScalarResult('VALOR_DEBITADO', 'valorDebitado','float');               
        $rsm->addScalarResult('REFERENCIA', 'referencia','string');             
        $query->setSQL($sql);
        //Se debe habilitar la oficina para el estado de cta
        $total=count($query->getScalarResult());
        $query->setParameter('start', $start+1);
        $query->setParameter('limit', ($start+$limit)); 
        $sql="SELECT a.*, rownum as intDoctrineRowNum FROM (".$sql.") a WHERE ROWNUM <= :limit";
        if($start>0)
        {
            $sql="SELECT * FROM (".$sql.") WHERE intDoctrineRowNum >= :start";
        }
        $query->setSQL($sql);
        $datos = $query->getScalarResult();
        $resultado['registros'] = $datos;
        $resultado['total']     = $total;	
        return $resultado;
	}    
    
    
    /**
     * Documentación para funcion 'findDebitosPorDebitoGeneral'.
     * busca detalles de debitos
     * @param $estado (estado del debito)
     * @param debitoGenId (id debito general)
     * @param $fechaDesde (fecha inicio)
     * @param $fechaHasta (fecha fin)
     * @param $limit (limite de registros en consulta)
     * @param $page (pagina donde se encuentra la consulta)
     * @param $start (registro inicio)
     * @param $banco (id de banco tipo cuenta)
     * @param $numeroCuenta (numero de cuenta)
     * @param $numeroCedula (numero de cedula)
     * @return array $respuesta debitos encontrados
     */       
	public function findDebitosPorDebitoGeneral($estado, $debitoGenId,$fechaDesde,$fechaHasta,$limit,$page,$start,$banco,$numeroCuenta,$numeroCedula)
    {
        $criterio_estado='';
        $criterio_banco='';
        $criterio_numero_cuenta='';	
        $criterio_numero_cedula='';	
        $criterio_fecha_desde='';
        $criterio_fecha_hasta='';
        $dql="SELECT 
                b
              FROM 
                  schemaBundle:InfoDebitoDet b, 
                  schemaBundle:InfoDebitoCab c,
                  schemaBundle:InfoDebitoGeneral d,
                  schemaBundle:InfoPersonaEmpresaRol per,
                  schemaBundle:InfoPersona p
              WHERE 
                  b.personaEmpresaRolId=per.id 
              AND per.personaId=p.id 
              AND b.debitoCabId= c.id 
              AND c.debitoGeneralId=d.id 
              AND ";

        $query = $this->_em->createQuery();            
        $query->setParameter('debito_gen_id',$debitoGenId);
        if ($fechaDesde)
        {		
            $fechaDesde = date("Y/m/d", strtotime($fechaDesde));	
            $criterio_fecha_desde=" b.feCreacion >= :fe_desde AND ";
            $query->setParameter('fe_desde',$fechaDesde);                
        }
        if($fechaHasta)
        {			             
            $fechaHasta = date("Y/m/d", strtotime($fechaHasta));
            $criterio_fecha_hasta=" b.feCreacion <= :fe_hasta AND ";
            $query->setParameter('fe_hasta',$fechaHasta); 
        }                
        if ($estado)
        {       
            $criterio_estado=" b.estado = :estado AND ";
            $query->setParameter('estado',$estado);
        }                
        if($banco)
        {
            $criterio_banco=" c.bancoTipoCuentaId= :banco AND ";
            $query->setParameter('banco',$banco);
        }
        if($numeroCuenta)
        {
            $criterio_numero_cuenta=" b.numeroTarjetaCuenta like :numero_cuenta AND ";
            $query->setParameter('numero_cuenta', '%'.$numeroCuenta.'%');                    
        }	
        if($numeroCedula)
        {
           $criterio_numero_cedula=" p.identificacionCliente like :numero_cedula AND ";
           $query->setParameter('numero_cedula', '%'.$numeroCedula.'%');
        }	
        $dql.=$criterio_numero_cuenta.$criterio_numero_cedula.$criterio_banco.$criterio_estado.$criterio_fecha_desde.$criterio_fecha_hasta;
        $dql.=" d.id= :debito_gen_id  order by b.feCreacion DESC ";
        $query->setDQL($dql);
        $total=count($query->getResult());
        $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        $resultado['registros']=$datos;
        $resultado['total']=$total;
        return $resultado;
	} 

	public function findPuntosProcesadosPorDebitoCab($idDebitoCab){
		$query = $this->_em->createQuery("SELECT b.puntoId
		FROM 
                schemaBundle:InfoDebitoDet b, 
				schemaBundle:InfoDebitoCab c
		WHERE 
                b.debitoCabId= c.id AND
				c.id=$idDebitoCab  AND
				b.estado='Procesado'
				group by b.puntoId");
//echo $query->getSQL();die;
		$datos = $query->getResult();
		return $datos;
	
	}
        
	public function findDebitosPorPuntoId( $puntoId,$limit,$start){
				
		$query = $this->_em->createQuery("SELECT b
		FROM 
                schemaBundle:InfoDebitoDet b
		WHERE 
                b.estado in ('Procesado','Rechazado')
		AND b.puntoId=$puntoId 
                order by b.feCreacion DESC");
//echo $query->getSQL();die;
                $total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
                $resultado['registros']=$datos;
                $resultado['total']=$total;
		return $resultado;
	}        

    public function findDebitosPorPersonaEmpresaRolId($personaEmpresaRolId,$limit,$start){			
        $query = $this->_em->createQuery("SELECT b
        FROM 
        schemaBundle:InfoDebitoDet b
        WHERE 
        b.estado in ('Procesado','Rechazado')
        AND b.personaEmpresaRolId=$personaEmpresaRolId 
        order by b.feCreacion DESC");
        $total=count($query->getResult());
        $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        $resultado['registros']=$datos;
        $resultado['total']=$total;
        return $resultado;
    }    
    
    
    /**
     * Documentación para funcion 'findDetallesDebitoPorCabecera'.
     * busca clientes por banco o tarjeta, por empresa y que tengan saldo
     * Se usa procedimiento almacenado CLIENTE_DEBITO_PKG.CLIENTES_DEBITO_FACTURA
     * @param $idEmpresa
     * @param $debitoCabId
     * @param $claveDesencripta
     * @param Object $cursa cursor donde se retorna la respuesta del procedimiento
     * @param Object $oci_con - conexion a la bd
     * @return array $respuesta debitos encontrados
     */    
    public function findDetallesDebitoPorCabecera($idEmpresa,$debitoCabId,$claveDesencripta,$cursa,$oci_con)
    {
        $arrayDatos="";                
        $s = oci_parse($oci_con, "BEGIN DB_FINANCIERO.FNKG_CONSULTA_DETALLES_DEBITOS.P_CONSULTA_DETALLE_DEBITO(".
        ":idEmpresa, :idDebitoCab,:claveDesencripta,:debitosRec); END;");
        oci_bind_by_name($s, ":idEmpresa", $idEmpresa);
        oci_bind_by_name($s, ":idDebitoCab", $debitoCabId);
        oci_bind_by_name($s, ":claveDesencripta", $claveDesencripta);
        oci_bind_by_name($s, ":debitosRec", $cursa, -1, OCI_B_CURSOR);
        oci_execute($s);
        oci_execute($cursa);
        $i=0;

        while (($row = oci_fetch_array($cursa)) != false)
        {
            $arrayDatos[$i]['cliente']             = $row['CLIENTE'];            
            $arrayDatos[$i]['nombre_oficina']      = $row['NOMBRE_OFICINA'];
            $arrayDatos[$i]['numero_cta_tarjeta']  = $row['NUMERO_CTA_TARJETA'];
            $arrayDatos[$i]['anio_vencimiento']    = $row['ANIO_VENCIMIENTO'];
            $arrayDatos[$i]['mes_vencimiento']     = $row['MES_VENCIMIENTO'];
            $arrayDatos[$i]['codigo_verificacion'] = $row['CODIGO_VERIFICACION'];
            $arrayDatos[$i]['valor_total']         = $row['VALOR_TOTAL'];
            $arrayDatos[$i]['estado']              = $row['ESTADO'];
            $i++;
        }
        return $arrayDatos;
    }
    
    
    /**
     * getDebitosGeneralesPorPersonaEmpresaRolId
     * 
     * Obtiene los débitos generales asociados a un cliente.
     * 
     * costoQuery: 74
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 20-03-2017
     * 
     * @param  array $arrayParametros [
     *                                  "intPersonaEmpresaRolId" : Id rol del cliente
     *                                  "intStart"    : valor de rango inicial de la consulta,
     *                                  "intLimit"    : valor de rango final de la consulta
     *                                ]     
     * 
     * @return $arrayResultado 
     */
    public function getDebitosGeneralesPorPersonaEmpresaRolId($arrayParametros)
    {
        try
        {
            $arrayResultado = array();
            $objQuery       = $this->_em->createQuery();
            $objQueryCount  = $this->_em->createQuery();
            
            $strSqlCount = "SELECT COUNT(idg.id)" ;
            
            $strSqlDatos = "SELECT 
                              idg.id                 as idDebitoGeneral,
                              idg.feCreacion         as feCreacionDebGral, 
                              idd.estado             as estadoDebitoDet, 
                              idd.feCreacion         as feCreacionDebitoDet, 
                              idd.valorDebitado      as valorDebitadoDet, 
                              idd.valorTotal         as valorTotalDet,
                              idd.feUltMod           as feUltModDet,
                              idd.usrUltMod          as usrUltModDet,
                              idd.observacionRechazo as observacionRechazo,
                              idc.feUltMod           as feUltModCab,
                              idc.usrUltMod          as usrUltModCab,
                              atc.esTarjeta          as esTarjeta,
                              atc.descripcionCuenta  as descripcionCuenta,
                              ab.descripcionBanco    as descripcionBanco ";
             $strSqlFrom = "FROM 
                                  schemaBundle:InfoDebitoDet       idd
                            JOIN  schemaBundle:InfoDebitoCab       idc  WITH idd.debitoCabId        =  idc.id
                            JOIN  schemaBundle:InfoDebitoGeneral   idg  WITH idc.debitoGeneralId    =  idg.id 
                            JOIN  schemaBundle:AdmiBancoTipoCuenta abtc WITH idc.bancoTipoCuentaId  =  abtc.id
                            JOIN  schemaBundle:AdmiBanco           ab   WITH abtc.bancoId           =  ab.id
                            JOIN  schemaBundle:AdmiTipoCuenta      atc  WITH abtc.tipoCuentaId      =  atc.id 
                            WHERE idd.estado in (:arrayEstados)
                            AND   idd.personaEmpresaRolId  = :intPersonaEmpresaRolId
                            ORDER BY idg.id DESC";
             
          $objQueryCount->setParameter('intPersonaEmpresaRolId', $arrayParametros['intPersonaEmpresaRolId']);  
          $objQueryCount->setParameter('arrayEstados', $arrayParametros['arrayEstados']); 
          $objQueryCount->setDQL($strSqlCount.$strSqlFrom);             
            
          $objQuery->setParameter('intPersonaEmpresaRolId', $arrayParametros['intPersonaEmpresaRolId']);  
          $objQuery->setParameter('arrayEstados', $arrayParametros['arrayEstados']); 
          $objQuery->setDQL($strSqlDatos.$strSqlFrom);
          
          $arrayResultado['intTotal']       = $objQueryCount->getSingleScalarResult();
          
          $arrayResultado['arrayRegistros'] = $objQuery->setFirstResult($arrayParametros['intStart'] )
                                                       ->setMaxResults($arrayParametros['intLimit'])->getResult();
        }
        catch(\Exception $e)
        {
            error_log('InfoDebitoDetRepository->getDebitosGeneralesPorPersonaEmpresaRolId '.$e->getMessage());
        }
        
        return $arrayResultado;
    }
    
   /**
    * Documentación para el método 'finalizarSolicitudPorTipo'.
    *
    * Ejecuta procedimiento que envia notificación y finaliza las solicitudes según los valores enviados como parámetro.
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
    * @version 1.0 30-03-2017
    */
    public function finalizarSolicitudPorTipo($arrayParametros)
    {      
        try
        {
            if($arrayParametros && count($arrayParametros)>0)
            {
                $strSql = "BEGIN
                            DB_COMERCIAL.COMEK_TRANSACTION.P_FINALIZA_SOLICITUD_POR_TIPO
                            (
                                :Pn_IdTipoSolicitud,
                                :Pv_EstadoSol,
                                :Pv_EstadoSolActualizar,
                                :Pv_Observacion,
                                :Pv_Usuario,
                                :Pv_Ip,
                                :Pv_Error                                 
                            );
                        END;";

                $stmt = $this->_em->getConnection()->prepare($strSql);

                $stmt->bindParam('Pn_IdTipoSolicitud', $arrayParametros['intTipoSolicitudId']);
                $stmt->bindParam('Pv_EstadoSol', $arrayParametros['strEstadoSol']);
                $stmt->bindParam('Pv_EstadoSolActualizar', $arrayParametros['strEstadoSolActualizar']);
                $stmt->bindParam('Pv_Observacion', $arrayParametros['strObservacion']);
                $stmt->bindParam('Pv_Usuario', $arrayParametros['strUsuarioSesion']);
                $stmt->bindParam('Pv_Ip', $arrayParametros['strIp']);
                $stmt->bindParam('Pv_Error', $arrayParametros['strMsjError']);           

                $stmt->execute();
            }
            else
            {
                $arrayParametros['strMsjError'] = 'No se enviaron parámetros para generar la consulta.';
            }            

        }catch (\Exception $e) 
        {   
            throw($e);
        }           
        
    }        
    

}
