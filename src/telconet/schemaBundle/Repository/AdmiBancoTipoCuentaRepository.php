<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiBancoTipoCuentaRepository extends EntityRepository
{
     
    

    /**
     * getBancosPorTiposCuenta
     * Se obtiene los BANCOS asociados al TIPO DE CUENTA en ADMI_BANCO_TIPO_CUENTA en base al parametro ID_TIPO_CUENTA 
     * y estado, Considerando los Bancos Asociados a las Cuentas Bancarias AHORRO y CORRIENTE
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 14-09-2017
     * Costo: 12
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 04-08-2019 Se permite filtrar los registros por el campo ES_TARJETA de la ADMI_TIPO_CUENTA, ya que se permite obtener los bancos,
     *                         aún cuando el tipo de cuenta sea tarjeta, desde la opción de corte masivo
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 01-09-2021 Se modifica la consulta para permitir  para permitir obtener los bancos tanto de tarjeta y cuenta bancaria
     * 
     * @param array $arrayParametros[
     *                                'arrayTiposCuenta'   => Recibe string de Tipos de Cuentas a Consultar
     *                                'strEstado'          => Recibe Estado del Tipo de Cuenta a Consultar
     *                              ]
     * @return $arrayResultado
     */
    public function getBancosPorTiposCuenta($arrayParametros)
    {
        $objRsm      = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery = $this->_em->createNativeQuery(null, $objRsm);
        
        $objRsmCount      = new ResultSetMappingBuilder($this->_em);
        $objNtvQueryCount = $this->_em->createNativeQuery(null, $objRsmCount);
        
        $strQueryCount = " SELECT COUNT(*) AS CANTIDAD FROM (SELECT DISTINCT BCO.ID_BANCO ";
        
        $strQuery      = " SELECT BCO.ID_BANCO, BCO.DESCRIPCION_BANCO  ";
 
        $strFromQuery  = " FROM DB_GENERAL.ADMI_BANCO_TIPO_CUENTA BTC,
                           DB_GENERAL.ADMI_BANCO BCO, 
                           DB_GENERAL.ADMI_TIPO_CUENTA TC
                           WHERE 
                           BTC.TIPO_CUENTA_ID     = TC.ID_TIPO_CUENTA
                           AND BTC.BANCO_ID       = BCO.ID_BANCO
                           AND TC.ESTADO          =:strEstado
                           AND BTC.TIPO_CUENTA_ID IN (:arrayTiposCuenta) ";                    
        
        $strGroupByQuery = " GROUP BY BCO.ID_BANCO,BCO.DESCRIPCION_BANCO ";
        $strOrderByQuery = " ORDER BY BCO.DESCRIPCION_BANCO ASC ";                    

        $objRsmCount->addScalarResult('CANTIDAD', 'Cantidad', 'integer');
        $objRsm->addScalarResult('ID_BANCO', 'intIdBanco', 'integer');
        $objRsm->addScalarResult('DESCRIPCION_BANCO', 'strDescripcionBanco', 'string');
          
        $objNtvQuery->setParameter('strEstado', $arrayParametros['strEstado']);
        $objNtvQuery->setParameter('arrayTiposCuenta', $arrayParametros['arrayTiposCuenta']);
        
        if(isset($arrayParametros["arrayInValoresEsTarjetaTipoCuenta"]) && !empty($arrayParametros["arrayInValoresEsTarjetaTipoCuenta"]))
        {
            $strFromQuery = $strFromQuery." AND TC.ES_TARJETA IN (:arrayInValoresEsTarjetaTipoCuenta) ";
            $objNtvQuery->setParameter('arrayInValoresEsTarjetaTipoCuenta', array_values($arrayParametros["arrayInValoresEsTarjetaTipoCuenta"]));
            $objNtvQueryCount->setParameter('arrayInValoresEsTarjetaTipoCuenta', array_values($arrayParametros["arrayInValoresEsTarjetaTipoCuenta"]));
        }
        else
        {
            $strFromQuery = $strFromQuery." AND TC.ES_TARJETA = :strEsTarjeta ";
            $objNtvQuery->setParameter('strEsTarjeta', 'N');
            $objNtvQueryCount->setParameter('strEsTarjeta', 'N');
        }
        
        if(isset($arrayParametros["strProcesoEjecutante"]) && !empty($arrayParametros["strProcesoEjecutante"])
            && isset($arrayParametros["arrayInValoresCuentaTarjeta"]) && !empty($arrayParametros["arrayInValoresCuentaTarjeta"]))
        {
            $strFromQuery = $strFromQuery." AND BTC.ESTADO <> :strEstadoInactivo ";
            $objNtvQuery->setParameter('strEstadoInactivo', 'Inactivo');
            $objNtvQueryCount->setParameter('strEstadoInactivo', 'Inactivo');
            
            $strFromQuery = $strFromQuery." AND BCO.DESCRIPCION_BANCO NOT IN (
                                            SELECT PARAM_DET.VALOR4
                                            FROM DB_GENERAL.ADMI_PARAMETRO_CAB PARAM_CAB
                                            INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET PARAM_DET
                                            ON PARAM_DET.PARAMETRO_ID = PARAM_CAB.ID_PARAMETRO
                                            WHERE PARAM_CAB.NOMBRE_PARAMETRO = :strParamsServiciosMd
                                            AND PARAM_CAB.ESTADO = :strEstadoActivo
                                            AND PARAM_DET.VALOR1 = :strProcesoEjecutante
                                            AND PARAM_DET.VALOR2 = :strBancosNoPermitidos
                                            AND PARAM_DET.VALOR3 IN (:arrayInValoresCuentaTarjeta)
                                            AND PARAM_DET.ESTADO = :strEstadoActivo
                                            ) ";
            $objNtvQuery->setParameter('strParamsServiciosMd', "PARAMETROS_ASOCIADOS_A_SERVICIOS_MD");
            $objNtvQuery->setParameter('strEstadoActivo', 'Activo');
            $objNtvQuery->setParameter('strProcesoEjecutante', $arrayParametros["strProcesoEjecutante"]);
            $objNtvQuery->setParameter('strBancosNoPermitidos', "BANCOS_NO_PERMITIDOS");
            $objNtvQuery->setParameter('arrayInValoresCuentaTarjeta', array_values($arrayParametros["arrayInValoresCuentaTarjeta"]));
            $objNtvQueryCount->setParameter('strParamsServiciosMd', "PARAMETROS_ASOCIADOS_A_SERVICIOS_MD");
            $objNtvQueryCount->setParameter('strEstadoActivo', 'Activo');
            $objNtvQueryCount->setParameter('strProcesoEjecutante', $arrayParametros["strProcesoEjecutante"]);
            $objNtvQueryCount->setParameter('strBancosNoPermitidos', "BANCOS_NO_PERMITIDOS");
            $objNtvQueryCount->setParameter('arrayInValoresCuentaTarjeta', array_values($arrayParametros["arrayInValoresCuentaTarjeta"]));
        }
        
        $objNtvQuery->setSQL($strQuery . $strFromQuery . $strGroupByQuery . $strOrderByQuery);
        $objDatos = $objNtvQuery->getResult();

        $objNtvQueryCount->setParameter('strEstado', $arrayParametros['strEstado']);
        $objNtvQueryCount->setParameter('arrayTiposCuenta', $arrayParametros['arrayTiposCuenta']);

        $strQueryCount = $strQueryCount . $strFromQuery .')';
        $objNtvQueryCount->setSQL($strQueryCount);
        $intTotal      = $objNtvQueryCount->getSingleScalarResult();

        $arrayResultado['objRegistros'] = $objDatos;
        $arrayResultado['intTotal']     = $intTotal;

        return $arrayResultado;
    }
   
    public function findListarBancos()
    {
        return $qb =$this->createQueryBuilder("t")
        ->select("abtc,ab")
        ->from('schemaBundle:AdmiBancoTipoCuenta abtc, schemaBundle:AdmiBanco ab, schemaBundle:AdmiTipoCuenta atc','')
        ->where("abtc.bancoId=ab.id AND abtc.tipoCuentaId=atc.id");
    }
    
    /**
     * Funcion que devuelve lista de bancos asociados'     
     * @author modificado Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.1  modificado 23-02-2015
     * @since 1.0
     * @param integer $tipoCuenta       
     * @see \telconet\schemaBundle\Entity\AdmiBancoTipoCuenta
     * @return object
     */
    public function findListarBancosSegunTipoCuenta($tipoCuenta)
    {   
        $arr_estados = array('Activo','Activo-debitos');
        $query = $this->_em->createQuery("SELECT abtc
        FROM 
                schemaBundle:AdmiBancoTipoCuenta abtc, schemaBundle:AdmiBanco ab, schemaBundle:AdmiTipoCuenta atc
        WHERE
           abtc.bancoId = ab.id AND
           abtc.tipoCuentaId = atc.id AND
           atc.id = :tipoCuenta AND
                ((abtc.esTarjeta != :strEsTarjeta AND ab.generaDebitoBancario = :strGeneraDebito)
                or abtc.esTarjeta = :strEsTarjeta) AND abtc.estado in(:strEstado) ORDER BY ab.descripcionBanco ASC");
        
        $query->setParameter('tipoCuenta', $tipoCuenta);
        $query->setParameter('strEsTarjeta', 'S');
        $query->setParameter('strGeneraDebito', 'S'); 
        $query->setParameter('strEstado',$arr_estados);
        $datos = $query->getResult();
        return $datos;
    }
    
    /**
     * Funcion que devuelve lista de bancos asociados'
     * @author modificado Andrés Montero <amontero@telconet.ec>
     * @version 1.0 30-06-2017
     * @since 1.0
     * @param integer $arrayParametros[
     *     arrayEstados  => estados para admi_banco_tipo_cuenta
     *     strTipoCuenta => tipo de cuenta
     *     intPaisId     => id del pais
     * ]
     * @see \telconet\schemaBundle\Entity\AdmiBancoTipoCuenta
     * @return object
     * 
     * @author Steven Ruano <sruano@telconet.ec>
     * @version 1.1 13-01-2023 se implementa validacion para obtener el array del banco.
     */
    public function findBancosTipoCuentaPorCriterio($arrayParametros)
    {
        $objQuery = $this->_em->createQuery();
        $strQuery ="
        SELECT
           abtc
        FROM
           schemaBundle:AdmiBancoTipoCuenta abtc,
           schemaBundle:AdmiBanco ab,
           schemaBundle:AdmiTipoCuenta atc
        WHERE
           abtc.bancoId = ab.id AND
           abtc.tipoCuentaId = atc.id AND ";
        if (!empty($arrayParametros['strbanco']) && isset($arrayParametros['strbanco']))
        {
            $strQuery.=' UPPER(ab.descripcionBanco) like UPPER(:strbanco) AND';
            $objQuery->setParameter('strbanco',"%".$arrayParametros['strbanco']."%");
        }   
        if (!empty($arrayParametros['arrayEstados']) && isset($arrayParametros['arrayEstados']))
        {
            $strQuery.=' abtc.estado in(:strEstado) AND ';
            $objQuery->setParameter('strEstado',$arrayParametros['arrayEstados']);
        }

        if (!empty($arrayParametros['strTipoCuenta']) && isset($arrayParametros['strTipoCuenta']))
        {
            $strQuery.=' atc.id = :tipoCuenta AND ';
            $objQuery->setParameter('tipoCuenta', $arrayParametros['strTipoCuenta']);
        }

        if (!empty($arrayParametros['intPaisId']) && isset($arrayParametros['intPaisId']))
        {
            $strQuery.=' ab.paisId = :paisId AND ';
            $objQuery->setParameter('paisId',$arrayParametros['intPaisId']);
        }
        $strQuery.=" ((abtc.esTarjeta != :strEsTarjeta AND ab.generaDebitoBancario = :strGeneraDebito) OR abtc.esTarjeta = :strEsTarjeta) ";
        $strQuery.=" ORDER BY ab.descripcionBanco ASC";

        $objQuery->setParameter('strEsTarjeta', 'S');
        $objQuery->setParameter('strGeneraDebito', 'S');
        $objQuery->setDQL($strQuery);
        $objDatos = $objQuery->getResult();
        return $objDatos;
    }

    /**
     * Funcion que devuelve lista de bancos activos'     
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.0
     * @since 14-07-2015
     * @param integer $visibleEn -> indica donde puede ser visible el bancoTipoCuenta
     * @param string $esTarjeta -> S si es tarjeta de credito y N si no es       
     * @see \telconet\schemaBundle\Entity\AdmiBancoTipoCuenta
     * @return array
     * 
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 05-07-2017
     * Se agrega el parametro $intIdPais para que devuelva los bancos de acuerdo al país
     */        
    public function findBancosActivos($strEsTarjeta,$strVisibleEn,$intIdPais)
    {
        $query = $this->_em->createQuery();
        $strDql = 
        "SELECT DISTINCT 
                ab.id, ab.descripcionBanco
            FROM 
                schemaBundle:AdmiBancoTipoCuenta abtc, 
                schemaBundle:AdmiBanco ab, 
                schemaBundle:AdmiTipoCuenta atc
            WHERE 
                abtc.bancoId=ab.id 
                AND abtc.tipoCuentaId=atc.id AND ";
        if(isset($intIdPais))
        {
            $strDql = $strDql." ab.paisId= :paisId AND ";
            $query->setParameter('paisId', $intIdPais); 
        }
        if ($strEsTarjeta)
        {        
            $strDql = $strDql." abtc.esTarjeta= :esTarjeta AND ";
            $query->setParameter('esTarjeta', $strEsTarjeta);
        }
        if ($strVisibleEn)
        {        
            $strDql = $strDql." abtc.visibleEn like :visibleEn AND ";
            $query->setParameter('visibleEn', "%".$strVisibleEn."%");            
        }
        $strDql=$strDql." abtc.estado='Activo'  order by ab.descripcionBanco ASC ";
        $query->setDQL($strDql);        
        $datos = $query->getResult();
        return $datos;
    }
    
    /**
    * @since 1.0
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.1 05-07-2017
    * Se agrega el parametro $intIdPais para que devuelva los bancos de acuerdo al país
    */ 
    public function findBancosTarjetaActivos($intIdPais)
    {   
        $query = $this->_em->createQuery();
        $strDql = 
        "SELECT ab.id, ab.descripcionBanco
            FROM 
                schemaBundle:AdmiBanco ab
            WHERE 
                UPPER(ab.descripcionBanco)='TARJETAS'";
        if(isset($intIdPais))
        {
            $strDql = $strDql." and ab.paisId= :paisId ";
            $query->setParameter('paisId', $intIdPais); 
        }
        $query->setDQL($strDql); 
        $datos = $query->getResult();
        return $datos;
    }	
	
    public function findBancosParaDebitos()
    {   
        $query = $this->_em->createQuery("SELECT DISTINCT ab.id, ab.descripcionBanco
		FROM 
                schemaBundle:AdmiBancoTipoCuenta abtc, schemaBundle:AdmiBanco ab, schemaBundle:AdmiTipoCuenta atc
		WHERE 
                abtc.bancoId=ab.id AND abtc.tipoCuentaId=atc.id AND abtc.estado in ('Activo','Activo-debitos') 
                order by ab.descripcionBanco ASC");

        $datos = $query->getResult();
        return $datos;
    }

    public function findTiposCuentaPorBancoPorVisibleFormatoParaDebitos($idBanco)
    {
        $query = $this->_em->createQuery("SELECT DISTINCT atc.id, atc.descripcionCuenta
		FROM 
                schemaBundle:AdmiBancoTipoCuenta abtc, schemaBundle:AdmiBanco ab, schemaBundle:AdmiTipoCuenta atc
		WHERE 
                abtc.bancoId=ab.id AND abtc.tipoCuentaId=atc.id AND abtc.estado in ('Activo','Activo-debitos') 
                AND ab.id=$idBanco AND atc.visibleFormato='S'
                order by atc.descripcionCuenta ASC");
//$query->getSQL();die;
        $datos = $query->getResult();
        return $datos;
    }
  	
    public function findTiposCuentaPorBancoActivos($idBanco,$esTarjeta,$estado='Activo')
    {   
        $criterioEsTarjeta='';
        if ($esTarjeta){
            $criterioEsTarjeta=" abtc.esTarjeta='$esTarjeta' AND ";
        }
        $query = $this->_em->createQuery("SELECT DISTINCT atc.id, atc.descripcionCuenta
		FROM 
                schemaBundle:AdmiBancoTipoCuenta abtc, schemaBundle:AdmiBanco ab, schemaBundle:AdmiTipoCuenta atc
		WHERE 
                $criterioEsTarjeta
                abtc.bancoId=ab.id AND abtc.tipoCuentaId=atc.id AND abtc.estado='$estado' 
                AND ab.id=$idBanco
                order by atc.descripcionCuenta ASC");
//echo $query->getSQL();die;
        $datos = $query->getResult();
        return $datos;
    } 
    public function findTiposCuentaPorBancoPorVisibleFormatoActivos($idBanco,$esTarjeta)
    {   
        $criterioEsTarjeta='';
        if ($esTarjeta){
            $criterioEsTarjeta=" abtc.esTarjeta='$esTarjeta' AND ";
        }
        $query = $this->_em->createQuery("SELECT DISTINCT atc.id, atc.descripcionCuenta
		FROM 
                schemaBundle:AdmiBancoTipoCuenta abtc, schemaBundle:AdmiBanco ab, schemaBundle:AdmiTipoCuenta atc
		WHERE 
                $criterioEsTarjeta
                abtc.bancoId=ab.id AND abtc.tipoCuentaId=atc.id AND abtc.estado='Activo' 
                AND ab.id=$idBanco AND atc.visibleFormato='S'
                order by atc.descripcionCuenta ASC");
//$query->getSQL();die;
        $datos = $query->getResult();
        return $datos;
    }     
    public function findBancoTipoCuentaPorBancoPorTipoCuenta($idBanco,$idTipoCuenta)
    {   
        $query = $this->_em->createQuery("SELECT DISTINCT abtc
		FROM 
                schemaBundle:AdmiBancoTipoCuenta abtc
		WHERE  
                abtc.bancoId=$idBanco AND abtc.tipoCuentaId=$idTipoCuenta");
//echo $query->getSQL();die;
        $datos = $query->getOneOrNullResult();
        return $datos;
    }   
    
    public function findBancosTipoCuentaParaGrid($limit,$page,$start){	
        $query = $this->_em->createQuery("SELECT a
                                                        FROM 
                                                        schemaBundle:AdmiBancoTipoCuenta a
                                                        order by a.feCreacion DESC"
                                                        );
        $datos = $query->getResult();
        //echo $query->getSQL();
        $total=count($query->getResult());
        $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        $resultado['registros']=$datos;
        $resultado['total']=$total;
        return $resultado;
    }     

    public function generarJson($nombreBanco, $nombreTipoCuenta, $estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($nombreBanco, $nombreTipoCuenta, $estado, '', '');
        $registros = $this->getRegistros($nombreBanco, $nombreTipoCuenta, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                        
                $arr_encontrados[]=array('id_banco_tipo_cuenta' =>$data->getId(),
                                         'id_banco' =>$data->getBancoId()->getId(),
                                         'id_tipo_cuenta' =>$data->getTipoCuentaId()->getId(),
                                         'descripcion_banco' =>trim($data->getBancoId()->getDescripcionBanco()),
                                         'descripcion_tipo_cuenta' =>trim($data->getTipoCuentaId()->getDescripcionCuenta()),
                                         'total_caracteres' =>($data->getTotalCaracteres()?$data->getTotalCaracteres():0),
                                         'total_codseguridad' =>($data->getTotalCodseguridad()?$data->getTotalCodseguridad():0),
                                         'caracter_empieza' =>($data->getCaracterEmpieza() ? $data->getCaracterEmpieza() : 0),
                                         'es_tarjeta' =>($data->getEsTarjeta()=='S'?'SI':'NO'),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_banco_tipo_cuenta' => 0 , 'id_banco' => 0 , 'id_tipo_cuenta' => 0 , 
                                                        'descripcion_banco' => 'Ninguno', 'descripcion_tipo_cuenta' => 'Ninguno', 
                                                        'total_caracteres'=>0, 'total_codseguridad'=>0, 'caracter_empieza'=>0, 
                                                        'es_tarjeta'=>'NO', 
                                                        'banco_id' => 0 , 'banco_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);
                return $resultado;
            }
            else
            {
                $dataF =json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
        
    }
    
    public function getRegistros($nombreBanco, $nombreTipoCuenta,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
        
        $boolBusqueda = false; 
        $where = "";
        if($nombreBanco!=""){
            $boolBusqueda = true;
            $where .= "AND LOWER(ab.descripcionBanco) like LOWER(%$nombreBanco%) ";
        }
        if($idBanco!=""){
            $boolBusqueda = true;
            $where .= "AND ab.id = $idBanco ";
        }
        
        if($nombreTipoCuenta!=""){
            $boolBusqueda = true;
            $where .= "AND LOWER(atc.descripcionTipoCuenta) like LOWER(%$nombreTipoCuenta%) ";
        }
        if($idTipoCuenta!=""){
            $boolBusqueda = true;
            $where .= "AND atc.id = $idTipoCuenta ";
        }

        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
                $where .= "AND LOWER(sim.estado) not like LOWER('Eliminado') ";
                $qb ->andWhere("");
            }
            else{
                $where .= "AND LOWER(sim.estado) like LOWER($estado) ";
            }
        }
        
        $sql = "SELECT sim 
                FROM schemaBundle:AdmiBancoTipoCuenta sim, schemaBundle:AdmiBanco ab, schemaBundle:AdmiTipoCuenta atc 
                WHERE 
                sim.bancoId=ab.id AND 
                sim.tipoCuentaId=atc.id  
                
                $where
                ";   
        
        $query = $this->_em()->createQuery($sql);
        
        $datos = null;
        if($start!='' && !$boolBusqueda && $limit!='')
            $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        else if($start!='' && !$boolBusqueda && $limit=='')
            $datos = $query->setFirstResult($start)->getResult();
        else if(($start=='' || $boolBusqueda) && $limit!='')
            $datos = $query->setMaxResults($limit)->getResult();
        else
            $datos = $query->getResult();
        
        return $datos;
    }
	

    public function findBancosTipoCuentaPorEstado($estado)
    {   
		$query = $this->_em->createQuery("SELECT abtc
		FROM 
                schemaBundle:AdmiBancoTipoCuenta abtc, schemaBundle:AdmiBanco ab, schemaBundle:AdmiTipoCuenta atc
		WHERE 
                abtc.bancoId=ab.id AND abtc.tipoCuentaId=atc.id AND abtc.estado='$estado' 
                order by ab.descripcionBanco ASC");

        $datos = $query->getResult();
        return $datos;
    }	
    
    /**
     * Funcion que devuelve lista de bancos para debitos'     
     * @author modificado Andres Montero <amontero@telconet.ec>
     * @version 1.1  modificado 26-03-2015
     * @since 1.0  
     * @see \telconet\schemaBundle\Entity\AdmiBancoTipoCuenta
     * @return object
     */    
    public function findBancosTipoCuentaParaDebitos()
    {   
		$query = $this->_em->createQuery(
       "SELECT abtc
		FROM 
                schemaBundle:AdmiBancoTipoCuenta abtc, 
                schemaBundle:AdmiBanco ab, 
                schemaBundle:AdmiTipoCuenta atc
		WHERE 
                abtc.bancoId=ab.id 
                AND abtc.tipoCuentaId=atc.id 
                AND abtc.estado in (:estados)
                order by ab.descripcionBanco ASC");
        $arrEstados=array('Activo','Activo-debitos');
        $query->setParameter('estados',$arrEstados);
        $datos = $query->getResult();
        return $datos;
    }

    public function findBancoTipoCuentaPorNombreBancoPorNombreTipoCuenta($nombreBanco,$nombreTipoCuenta,$empresaCod)
    {   
        $query = $this->_em->createQuery("SELECT DISTINCT bcc
		FROM 
                schemaBundle:AdmiBancoTipoCuenta abtc,
				schemaBundle:AdmiBanco b,
				schemaBundle:AdmiTipoCuenta tc,
                                schemaBundle:AdmiBancoCtaContable bcc
		WHERE  
                                abtc.id=bcc.bancoTipoCuentaId AND
				abtc.bancoId=b.id AND 
				abtc.tipoCuentaId=tc.id AND
                b.descripcionBanco='$nombreBanco' AND tc.descripcionCuenta='$nombreTipoCuenta' AND bcc.empresaCod='".$empresaCod."'");
//echo $query->getSQL();die;
        $datos = $query->getOneOrNullResult();
        return $datos;
    }
	
	//wsanchez
	public function findOneById($idBancoTipoCuenta)
    {   
        $query = $this->_em->createQuery("SELECT DISTINCT abtc
			FROM 
					schemaBundle:AdmiBancoTipoCuenta abtc
			WHERE  
					abtc.id=$idBancoTipoCuenta");
		//echo $query->getSQL();
		//break;
        $datos = $query->getOneOrNullResult();
        return $datos;
    }   
    
    /**
     * Funcion que devuelve lista de tipos de cuenta asociados a un banco'     
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.0
     * @since 13-07-2015
     * @param integer $idBanco -> id del banco del que se busca los tipos de tarjeta
     * @param string $esTarjeta -> S si es tarjeta de credito y N si no es       
     * @see \telconet\schemaBundle\Entity\AdmiBancoTipoCuenta
     * @return array
     */    
    public function findTodosTiposCuentaPorBanco($idBanco,$esTarjeta,$estadoTipoTarjeta,$estadoBancoTipoCuenta,$visibleEn)
    {
        $query = $this->_em->createQuery(
        "SELECT DISTINCT atc.id, atc.descripcionCuenta
		FROM 
            schemaBundle:AdmiBancoTipoCuenta abtc, 
            schemaBundle:AdmiBanco ab, 
            schemaBundle:AdmiTipoCuenta atc
		WHERE 
            abtc.esTarjeta in (:strEsTarjeta) AND
            abtc.bancoId=ab.id 
            AND abtc.tipoCuentaId=atc.id 
            AND ab.id= :strBancoId
            AND atc.estado in (:strEstadoTipoTarjeta)
            AND abtc.estado in (:strEstadoBancoTipoCuenta)
            AND abtc.visibleEn like :visibleEn
        order by 
            atc.descripcionCuenta ASC");
        $query->setParameter('strBancoId', $idBanco);
        $query->setParameter('strEsTarjeta', $esTarjeta);
        $query->setParameter('strEstadoTipoTarjeta', $estadoTipoTarjeta);
        $query->setParameter('strEstadoBancoTipoCuenta',$estadoBancoTipoCuenta);
        $query->setParameter('visibleEn', "%".$visibleEn."%");      
        $datos = $query->getResult();
        return $datos;
    }     
    
    
}