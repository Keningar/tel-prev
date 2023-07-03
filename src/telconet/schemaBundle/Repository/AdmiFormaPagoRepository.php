<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiFormaPagoRepository extends EntityRepository
{
    /**
     * getFormasPagoParaContrato
     * Se obtiene las Formas de Pago por Empresa en Sesion y estado, Verificando que la forma de pago exista asignado 
     * al menos a un contrato 
     *      
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 11-09-2017
     * Costo: 15
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 28-09-2021 Se agrega envío de parámetro strProcesoEjecutante para ocultar formas de pago que no deben mostrarse en la pantalla
     *                         de corte masivo
     * 
     * @param array $arrayParametros[
     *                                'strEmpresaCod' => Recibe el codigo de la empresa en sesión.
     *                                'strEstadoFp'   => Recibe Estado de la Forma de Pago a Consultar
     *                              ]                                    
     * @return $arrayResultado
     */
    public function getFormasPagoParaContrato($arrayParametros)
    {
        $objRsm      = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery = $this->_em->createNativeQuery(null, $objRsm);
        
        $objRsmCount      = new ResultSetMappingBuilder($this->_em);
        $objNtvQueryCount = $this->_em->createNativeQuery(null, $objRsmCount);
        
        $strQueryCount = " SELECT COUNT(*) AS CANTIDAD ";
        $strQuery      = " SELECT FP.ID_FORMA_PAGO, FP.DESCRIPCION_FORMA_PAGO ";
 
        $strFromQuery  = " FROM DB_GENERAL.ADMI_FORMA_PAGO FP
                           WHERE ES_PAGO_PARA_CONTRATO = :strEsParaContrato
                           AND ESTADO                  = :strEstadoFp
                           AND EXISTS (SELECT 1
                                       FROM DB_COMERCIAL.INFO_CONTRATO CONT,
                                       DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPEMROL,
                                       DB_COMERCIAL.INFO_EMPRESA_ROL EMPROL
                                       WHERE CONT.PERSONA_EMPRESA_ROL_ID = IPEMROL.ID_PERSONA_ROL
                                       AND IPEMROL.EMPRESA_ROL_ID        = EMPROL.ID_EMPRESA_ROL
                                       AND EMPROL.EMPRESA_COD            = :strEmpresaCod            
                                      )";                    
        
        $strOrderByQuery = " ORDER BY DESCRIPCION_FORMA_PAGO ASC ";
        
        if(isset($arrayParametros["strProcesoEjecutante"]) && !empty($arrayParametros["strProcesoEjecutante"]))
        {
            $strFromQuery = $strFromQuery." AND FP.DESCRIPCION_FORMA_PAGO IN (
                                            SELECT PARAM_DET.VALOR3
                                            FROM DB_GENERAL.ADMI_PARAMETRO_CAB PARAM_CAB
                                            INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET PARAM_DET
                                            ON PARAM_DET.PARAMETRO_ID = PARAM_CAB.ID_PARAMETRO
                                            WHERE PARAM_CAB.NOMBRE_PARAMETRO = :strParamsServiciosMd
                                            AND PARAM_CAB.ESTADO = :strEstadoActivo
                                            AND PARAM_DET.VALOR1 = :strProcesoEjecutante
                                            AND PARAM_DET.VALOR2 = :strFormasPagoNoPermitidas
                                            AND PARAM_DET.ESTADO = :strEstadoActivo
                                            ) ";
            $objNtvQuery->setParameter('strParamsServiciosMd', "PARAMETROS_ASOCIADOS_A_SERVICIOS_MD");
            $objNtvQuery->setParameter('strEstadoActivo', 'Activo');
            $objNtvQuery->setParameter('strProcesoEjecutante', $arrayParametros["strProcesoEjecutante"]);
            $objNtvQuery->setParameter('strFormasPagoNoPermitidas', "FORMAS_PAGO_PERMITIDAS");
            $objNtvQueryCount->setParameter('strParamsServiciosMd', "PARAMETROS_ASOCIADOS_A_SERVICIOS_MD");
            $objNtvQueryCount->setParameter('strEstadoActivo', 'Activo');
            $objNtvQueryCount->setParameter('strProcesoEjecutante', $arrayParametros["strProcesoEjecutante"]);
            $objNtvQueryCount->setParameter('strFormasPagoNoPermitidas', "FORMAS_PAGO_PERMITIDAS");
        }
        
        $objRsmCount->addScalarResult('CANTIDAD', 'Cantidad', 'integer');
        $objRsm->addScalarResult('ID_FORMA_PAGO', 'intIdFormaPago', 'integer');
        $objRsm->addScalarResult('DESCRIPCION_FORMA_PAGO', 'strDescripcionFormaPago', 'string');
          
        $objNtvQuery->setParameter('strEstadoFp', $arrayParametros['strEstadoFp']);
        $objNtvQuery->setParameter('strEmpresaCod', $arrayParametros['strEmpresaCod']);
        $objNtvQuery->setParameter('strEsParaContrato', 'S');        

        $objNtvQuery->setSQL($strQuery . $strFromQuery . $strOrderByQuery);
        $objDatos = $objNtvQuery->getResult();

        $objNtvQueryCount->setParameter('strEstadoFp', $arrayParametros['strEstadoFp']);
        $objNtvQueryCount->setParameter('strEmpresaCod', $arrayParametros['strEmpresaCod']);
        $objNtvQueryCount->setParameter('strEsParaContrato', 'S');        

        $strQueryCount = $strQueryCount . $strFromQuery;
        $objNtvQueryCount->setSQL($strQueryCount);
        $intTotal      = $objNtvQueryCount->getSingleScalarResult();

        $arrayResultado['objRegistros'] = $objDatos;
        $arrayResultado['intTotal']     = $intTotal;

        return $arrayResultado;
    }
    
    public function generarJson($nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($nombre, $estado, '', '');
        $registros = $this->getRegistros($nombre, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                $arr_encontrados[]=array('id_forma_pago' =>$data->getId(),
                                         'descripcion_forma_pago' =>trim($data->getDescripcionFormaPago()),
                                         'codigo_forma_pago' =>trim($data->getCodigoFormaPago()),
                                         'es_depositable' =>($data->getEsDepositable()=='S'?'SI':'NO'),
                                         'es_monetario' =>($data->getEsMonetario()=='S'?'SI':'NO'),
                                         'es_pago_para_contrato' =>($data->getEsPagoParaContrato()=='S'?'SI':'NO'),
                                         'corte_masivo' => ($data->getCorteMasivo() == 'S' ? 'SI' : 'NO'),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_forma_pago' => 0 , 'descripcion_forma_pago' => 'Ninguno', 'codigo_forma_pago' => 'Ninguno', 
                                                        'es_depositable' => 'NO', 'es_monetario' => 'NO', 'es_pago_para_contrato' => 'NO', 
                                                        'forma_pago_id' => 0 , 'forma_pago_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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

     /** 
     * @author Steven Ruano <sruano@telconet.ec>
     * @version 1.0 13-01-2023 Se modfico la consulta a una query native para mayor velocidad de respuesta
     * 
     * @author Steven Ruano <sruano@telconet.ec>
     * @version 1.1 23-01-2023 Se modifico la consulta para que muestre data en estado activo y se muestre
     *                        forma de pago Tarjeta de Credito 
     */
    
    public function getRegistros($strNombre,$strEstado,$intStart,$intLimit)
    {
        
        $boolBusqueda = false;

        $strWhere = ''; 
        
        if($strNombre!="")
        {
            
            $boolBusqueda = true;
            
            $strWhere .= " Where tp.descripcionFormaPago like UPPER('%".$strNombre."%') ";
        }
        
        if($strEstado!="Todos")
        {
            

            if($boolBusqueda)
            {
                $strWhere .= " AND ";
            }
            
            if($strEstado=="Activo" && $boolBusqueda)
            {
               
                $strWhere .= " LOWER(tp.estado) not like LOWER('Eliminado') ";
            }
            
            else
            {
                if(!$boolBusqueda)
                {
                    $strWhere .= " where LOWER(tp.estado) not like LOWER('Eliminado') ";
                } 
                
                else 
                {
                    $strWhere .= " LOWER(tp.estado) = LOWER('".$strEstado."') ";

                }
            }
        }
        
        $strSql ="SELECT tp FROM schemaBundle:AdmiFormaPago tp $strWhere";
        
        $objQuery = $this->_em->createQuery($strSql);
        
        if($intStart!='' && !$boolBusqueda)
        {
            
            $objQuery->setFirstResult($intStart);

        } 

        if($intLimit!='')
        {

            $objQuery->setMaxResults($intLimit);

        } 
        
        $strDatos =$objQuery->getResult();
        return $strDatos;
    }


    /**
     * Documentación para el método 'findFormasPagoContabilizables'.
     * 
     * Método que retorna las formas de pago que tengan asociado una plantilla cuando la empresa en sessión contabiliza
     *
     * @param  array $arrayParametros ['strEmpresaCod'    => 'Código de la empresa a validar',
     *                                 'arrayEstado'      => 'Array de estados permitidos para la consulta de las formas de pago',
     *                                 'strTipoDocumento' => 'Tipo de documento a consultar' ]
     * @return array $arrayFormasPagoContabilizables 'Formas de pagos asociadas a una plantilla contable'
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 04-08-2017
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 06-10-2017 - Se agrega el parámetro 'strTipoDocumento' para validar las plantillas asociadas a plantillas con el tipo de
     *                           documento respectivo.
     */
    public function findFormasPagoContabilizables( $arrayParametros )
    {
        $arrayFormasPagoContabilizables = array();

        try
        {
            $arrayEstado       = ( isset($arrayParametros['arrayEstado']) && !empty($arrayParametros['arrayEstado']) )
                                 ? $arrayParametros['arrayEstado'] : array();
            $strEmpresaCod     = ( isset($arrayParametros['strEmpresaCod']) && !empty($arrayParametros['strEmpresaCod']) )
                                 ? $arrayParametros['strEmpresaCod'] : null;
            $strTipoDocumento  = ( isset($arrayParametros['strTipoDocumento']) && !empty($arrayParametros['strTipoDocumento']) )
                                 ? $arrayParametros['strTipoDocumento'] : null;
            if( !empty($arrayEstado) && !empty($strEmpresaCod) && !empty($strTipoDocumento) )
            {
                $objQuery   = $this->_em->createQuery();
                $strSelect  = "SELECT AFP.id, AFP.descripcionFormaPago, AFP.codigoFormaPago, AFP.tipoFormaPago ";
                $strFrom    = "FROM schemaBundle:AdmiPlantillaContableCab APCC, ".
                              "     schemaBundle:AdmiFormaPago AFP, ".
                              "     schemaBundle:AdmiTipoDocumentoFinanciero ATDF ";
                $strWhere   = "WHERE APCC.formaPagoId         = AFP.id ".
                              "  AND APCC.tipoDocumentoId     = ATDF.id ".
                              "  AND APCC.empresaCod          = :strEmpresaCod ".
                              "  AND ATDF.codigoTipoDocumento = :strTipoDocumento ".
                              "  AND APCC.estado              IN (:arrayEstado) ".
                              "  AND AFP.estado               IN (:arrayEstadoFormaPago) ";
                $strGroupBy = "GROUP BY AFP.id, AFP.descripcionFormaPago, AFP.codigoFormaPago, AFP.tipoFormaPago ";
                $strOrderBy = "ORDER BY AFP.descripcionFormaPago ";

                $objQuery->setParameter('strEmpresaCod',        $strEmpresaCod);
                $objQuery->setParameter('arrayEstado',          array_values($arrayEstado));
                $objQuery->setParameter('strTipoDocumento',     $strTipoDocumento);
                $objQuery->setParameter('arrayEstadoFormaPago', array_values(array('Activo', 'Modificado')));

                $strSql = $strSelect.$strFrom.$strWhere.$strGroupBy.$strOrderBy;

                $objQuery->setDQL($strSql);

                $arrayFormasPagoContabilizables = $objQuery->getResult();
            }
            else
            {
                throw new \Exception("No se han enviado los parámetros adecuados para realizar la consulta");
            }
        }
        catch( \Exception $ex)
        {
            throw ($ex);
        }

        return $arrayFormasPagoContabilizables;
	}

	public function findFormasPagoActivas(){	

                $query = $this->_em->createQuery("SELECT a
		FROM 
                schemaBundle:AdmiFormaPago a
		WHERE 
                a.estado='Activo' AND a.visibleEnPago='S' order by a.descripcionFormaPago ASC
                 ");
		$datos = $query->getResult();
		return $datos;
	}
    
	/**
	 * Devuelve un query builder para obtener las formas de pago para contrato del estado dado
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.1 25-11-2021 - Se agrega para obtener las formas de pago parametrizadas.
     * 
	 * @param string $estado
	 * @return \Doctrine\ORM\QueryBuilder
	 */
    public function findFormasPagoXEstado($strEstado, $arrayParamFormasPago = [])
    {  
        $strQuery =$this->createQueryBuilder("t");
        $strQuery->select("a");
        $strQuery->from('schemaBundle:AdmiFormaPago a','');
        $strQuery->where("a.estado = :estado and a.esPagoParaContrato = 'S'");
        
        if($arrayParamFormasPago!=null || !empty($arrayParamFormasPago))
        {
            $strQuery->andWhere("a.codigoFormaPago in (:arrayCodFormaPago) ");
            $strQuery->setParameter('arrayCodFormaPago', $arrayParamFormasPago);
        }
        
        $strQuery->setParameter('estado', $strEstado);
            
        return $strQuery;
    }
    
    public function generarJsonFormaPago($estado,$start,$limit){
        $arr_encontrados = array();
        
        $entidadesTotal = $this->getFormaPago($estado,'','');
        
        $entidades = $this->getFormaPago($estado,$start,$limit);
//        error_log('entra');
        if ($entidades) {
            
            $num = count($entidadesTotal);
            
            foreach ($entidades as $entidad)
            {
                $arr_encontrados[]=array('idFormaPago' =>$entidad->getId(),
                                         'descripcionFormaPago' =>trim($entidad->getDescripcionFormaPago())
                                        );
            }

            if($num == 0)
            {
               $resultado= array('total' => 1 ,
                                 'encontrados' => array('idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno','idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);

                return $resultado;
            }
            else
            {
                $data=json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';

            return $resultado;
        }
        
    }
   
    public function getFormaPago($estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiFormaPago','e');
            $qb ->where( "e.esPagoParaContrato = 'S'");
        
        if($estado!="Todos"){
            $qb ->andWhere('e.estado = ?2');
            $qb->setParameter(2, $estado);
        }
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query->getResult();
    }
    
    public function getCtaRetenciones()
	{
		$query = $this->_em->createQuery("SELECT 
				afp.ctaContable
		FROM 
                schemaBundle:AdmiFormaPago afp");
        $datos = $query->getSingleResult();
        //print_r($datos);die;
        return $datos;
	}
        
        
    public function generarJsonFormaPagoMonetario($estado,$start,$limit){
        $arr_encontrados = array();
        
        $entidadesTotal = $this->getFormaPagoMonetario($estado,'','');
        
        $entidades = $this->getFormaPagoMonetario($estado,$start,$limit);
//        error_log('entra');
        if ($entidades) {
            
            $num = count($entidadesTotal);
            
            foreach ($entidades as $entidad)
            {
                $arr_encontrados[]=array('idFormaPago' =>$entidad->getId(),
                                         'descripcionFormaPago' =>trim($entidad->getDescripcionFormaPago())
                                        );
            }

            if($num == 0)
            {
               $resultado= array('total' => 1 ,
                                 'encontrados' => array('idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno','idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);

                return $resultado;
            }
            else
            {
                $data=json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';

            return $resultado;
        }
        
    }
   
    public function getFormaPagoMonetario($estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiFormaPago','e');
            $qb ->where( "e.esMonetario = 'S'");
        
        if($estado!="Todos"){
            $qb ->andWhere('e.estado = ?2');
            $qb->setParameter(2, $estado);
        }
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query->getResult();
    }
    
    public function generarJsonFormaGeneral($estado, $start, $limit, $esMonetario, $esPagoParaContrato, $corteMasivo) {
        $arr_encontrados = array();
        //...
        $entidadesTotal = $this->getFormaPagoGeneral($estado, '', '', $esMonetario, $esPagoParaContrato, $corteMasivo);
        $entidades = $this->getFormaPagoGeneral($estado, $start, $limit, $esMonetario, $esPagoParaContrato, $corteMasivo);
        //...
        if ($entidades) {
            $num = count($entidadesTotal);
            foreach ($entidades as $entidad) {
                $arr_encontrados[] = array('idFormaPago' => $entidad->getId(),
                                'descripcionFormaPago' => trim($entidad->getDescripcionFormaPago())
                );
            }
    
            if ($num == 0) {
                $resultado = array('total' => 1, 'encontrados' => array('idConectorInterface' => 0, 'nombreConectorInterface' => 'Ninguno', 'idConectorInterface' => 0, 'nombreConectorInterface' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode($resultado);
                return $resultado;
            } else {
                $data = json_encode($arr_encontrados);
                $resultado = '{"total":"' . $num . '","encontrados":' . $data . '}';
                return $resultado;
            }
        } else {
            $resultado = '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }
    
    public function getFormaPagoGeneral($estado, $start, $limit, $esMonetario, $esPagoParaContrato, $corteMasivo) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e')->from('schemaBundle:AdmiFormaPago', 'e');
        //        $qb->where("'1' = '1'");
    
        if ($estado != "Todos") {
            $qb->andWhere('e.estado = :estado');
            $qb->setParameter('estado', $estado);
        }
    
        if ($esMonetario != "") {
            $qb->andWhere("e.esMonetario = :esMonetario")->setParameter('esMonetario', $esMonetario);
        }
    
        if ($esPagoParaContrato != "") {
            $qb->andWhere("e.esPagoParaContrato = :esPagoParaContrato")->setParameter('esPagoParaContrato', $esPagoParaContrato);
        }
    
        if ($corteMasivo != "") {
            $qb->andWhere("e.corteMasivo = :corteMasivo")->setParameter('corteMasivo', $corteMasivo);
        }
    
        if ($start != '')
            $qb->setFirstResult($start);
        if ($limit != '')
            $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query->getResult();
    }
   /**
    * Funcion que devuelve las formas de pagos validas para el ingreso del contrato del cliente
    * Consideraciones: Se toma las formas de pago en estado Activo y que esten marcadas como formas de pago utilizadas para contratos
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 23-05-2014
    * @return object
    */
    public function findFormasPagoParaContrato(){

       $query = $this->_em->createQuery("		
       select a
       from schemaBundle:AdmiFormaPago a
       where a.estado = :estado and a.esPagoParaContrato =:pago_para_contrato
       ");       
       $query->setParameter('estado', 'Activo');
       $query->setParameter('pago_para_contrato', 'S');
       $datos = $query->getResult();	
	return $datos;
   }
   
   /**
    * Funcion que devuelve las formas de pagos que usan para cierre de caja
    * Consideraciones: Se toma las formas de pago en estado Activo, marcadas como depositables y parametrizadas como cierre caja
    * @author Luis Lindao <llindao@telconet.ec>
    * @version 1.0 26-12-2018
    * @return object
    */
   public function getFormasPagoParametrizadas ($arrayParametros)
   {
       $objQuery   = $this->_em->createQuery();
       $strSelect  = "SELECT AFP.id, AFP.descripcionFormaPago ";
       $strFrom    = "FROM schemaBundle:AdmiFormaPago AFP ";
       $strWhere   = "WHERE AFP.estado = :strEstadoFp ".
                     "AND EXISTS (SELECT 1 ".
                     "            FROM schemaBundle:InfoEmpresaGrupo IEG, ".
                     "                 schemaBundle:AdmiParametroCab APC, ".
                     "                 schemaBundle:AdmiParametroDet APD ".
                     "            WHERE APD.valor1 = AFP.codigoFormaPago ".
                     "            AND APD.estado = :strEstadoParametro ".
                     "            AND APC.nombreParametro = :strNombreParametro ".
                     "            AND APC.estado = :strEstadoParametro ".
                     "            AND IEG.prefijo = :strPrefijoEmpresa ".               
                     "            AND APD.empresaCod = IEG.id ".
                     "            AND APD.parametroId = APC.id) ";
       $strOrderBy = "ORDER BY AFP.descripcionFormaPago ";

       $objQuery->setParameter('strEstadoFp',        $arrayParametros['strEstadoFp']);
       $objQuery->setParameter('strPrefijoEmpresa',  $arrayParametros['strPrefijoEmpresa']);
       $objQuery->setParameter('strNombreParametro', $arrayParametros['strNombreParametro']);
       $objQuery->setParameter('strEstadoParametro', $arrayParametros['strEstadoParametro']);
   
       $strSql = $strSelect.$strFrom.$strWhere.$strOrderBy;

       $objQuery->setDQL($strSql);

       $arrayFormaPagoDepositable = $objQuery->getResult();
       return $arrayFormaPagoDepositable;
       
   }
   
    /**
     * getFormasPagoByParametros
     * Se obtiene las formas de pago según los valores enviados como parámetro.
     *      
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 08-09-2020
     * Costo: 15
     * @param array $arrayParametros[
     *                                'strEmpresaCod'     => Recibe el codigo de la empresa en sesión.
     *                                'strEstadoFp'       => Recibe Estado de la Forma de Pago a Consultar
     *                                'strEsParaContrato' => Parámetro para verificar si es para contrato.
     *                              ]                                    
     * @return $arrayResultado
     */
    public function getFormasPagoByParametros($arrayParametros)
    {
        $objRsm      = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery = $this->_em->createNativeQuery(null, $objRsm);
        
        $objRsmCount      = new ResultSetMappingBuilder($this->_em);
        $objNtvQueryCount = $this->_em->createNativeQuery(null, $objRsmCount);
        
        $strQueryCount = " SELECT COUNT(*) AS CANTIDAD ";
        $strQuery      = " SELECT FP.ID_FORMA_PAGO, FP.DESCRIPCION_FORMA_PAGO ";
 
        $strFromQuery  = " FROM DB_GENERAL.ADMI_FORMA_PAGO FP
                           WHERE ESTADO  = :strEstadoFp AND CODIGO_FORMA_PAGO NOT IN ('TRGM','DPGM')
                           AND EXISTS (SELECT 1
                                       FROM DB_COMERCIAL.INFO_CONTRATO CONT,
                                       DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPEMROL,
                                       DB_COMERCIAL.INFO_EMPRESA_ROL EMPROL
                                       WHERE CONT.PERSONA_EMPRESA_ROL_ID = IPEMROL.ID_PERSONA_ROL
                                       AND IPEMROL.EMPRESA_ROL_ID        = EMPROL.ID_EMPRESA_ROL
                                       AND EMPROL.EMPRESA_COD            = :strEmpresaCod            
                                      )"; 
        
        if(isset($arrayParametros['strVisiblePago']) && $arrayParametros['strVisiblePago']!=='')
        {
            $strFromQuery  .= " AND VISIBLE_EN_PAGO = :strVisiblePago ";
        }
        if(isset($arrayParametros['strTipoFormaPago']) && $arrayParametros['strTipoFormaPago']!=='')
        {
            $strFromQuery  .= " AND TIPO_FORMA_PAGO = :strTipoFormaPago ";
        }
        if(isset($arrayParametros['strEsParaContrato']) && $arrayParametros['strEsParaContrato']!=='')
        {
            $strFromQuery  .= " AND ES_PAGO_PARA_CONTRATO = :strEsParaContrato ";  
        }        
        $strOrderByQuery = " ORDER BY DESCRIPCION_FORMA_PAGO ASC ";                    

        $objRsmCount->addScalarResult('CANTIDAD', 'Cantidad', 'integer');
        $objRsm->addScalarResult('ID_FORMA_PAGO', 'intIdFormaPago', 'integer');
        $objRsm->addScalarResult('DESCRIPCION_FORMA_PAGO', 'strDescripcionFormaPago', 'string');
          
        $objNtvQuery->setParameter('strEstadoFp', $arrayParametros['strEstadoFp']);
        $objNtvQuery->setParameter('strEmpresaCod', $arrayParametros['strEmpresaCod']);
        if(isset($arrayParametros['strVisiblePago']) && $arrayParametros['strVisiblePago']!=='')
        {
            $objNtvQuery->setParameter('strVisiblePago', $arrayParametros['strVisiblePago']);
        }
        if(isset($arrayParametros['strTipoFormaPago']) && $arrayParametros['strTipoFormaPago']!=='')
        {
            $objNtvQuery->setParameter('strTipoFormaPago', $arrayParametros['strTipoFormaPago']); 
        }
        if(isset($arrayParametros['strEsParaContrato']) && $arrayParametros['strEsParaContrato']!=='')
        {
            $objNtvQuery->setParameter('strEsParaContrato', $arrayParametros['strEsParaContrato']);     
        }
     
        $objNtvQuery->setSQL($strQuery . $strFromQuery . $strOrderByQuery);
        $objDatos = $objNtvQuery->getResult();

        $objNtvQueryCount->setParameter('strEstadoFp', $arrayParametros['strEstadoFp']);
        $objNtvQueryCount->setParameter('strEmpresaCod', $arrayParametros['strEmpresaCod']);
        if(isset($arrayParametros['strVisiblePago']) && $arrayParametros['strVisiblePago']!=='')
        {
            $objNtvQueryCount->setParameter('strVisiblePago', $arrayParametros['strVisiblePago']);
        }
        if(isset($arrayParametros['strTipoFormaPago']) && $arrayParametros['strTipoFormaPago']!=='')
        {
            $objNtvQueryCount->setParameter('strTipoFormaPago', $arrayParametros['strTipoFormaPago']); 
        }
        if(isset($arrayParametros['strEsParaContrato']) && $arrayParametros['strEsParaContrato']!=='')
        {
            $objNtvQueryCount->setParameter('strEsParaContrato', $arrayParametros['strEsParaContrato']);     
        }        

        $strQueryCount = $strQueryCount . $strFromQuery;
        $objNtvQueryCount->setSQL($strQueryCount);
        $intTotal      = $objNtvQueryCount->getSingleScalarResult();

        $arrayResultado['objRegistros'] = $objDatos;
        $arrayResultado['intTotal']     = $intTotal;

        return $arrayResultado;
    }   
   
}
