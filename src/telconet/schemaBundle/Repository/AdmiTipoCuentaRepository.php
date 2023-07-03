<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiTipoCuentaRepository extends EntityRepository
{

    /**
     * getTiposCuenta
     * Se obtiene los tipos de cuenta ADMI_TIPO_CUENTA en base al parametro ES_TARJETA (S o N)
     * y por estado Activo
     *      
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 13-09-2017
     * Costo: 3
     * @param array $arrayParametros[
     *                                'strEsTarjeta'       => Recibe parametro (S/N) que define si el Tipo de cuenta
     *                                                        a Consultar es (Tarjeta o Cuenta Bancaria)
     *                                'strEstado'          => Recibe Estado del Tipo de Cuenta a Consultar
     *                                'intIdPais'          => Recibe el codigo del pais a buscar
     *                              ]                                    
     *
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.1 27-02-2018 - Se agrega el filtro del pais en sesion para la busqueda de los Tipos de Cuentas
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 31-08-2021 Se agrega validación de parámetro arrayInValoresEsTarjetaTipoCuenta para los casos en que se elija la opción 
     *                         Select All desde el corte masivo
     *
     * @return $arrayResultado
     */
    public function getTiposCuenta($arrayParametros)
    {
        $objRsm      = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery = $this->_em->createNativeQuery(null, $objRsm);
        
        $objRsmCount      = new ResultSetMappingBuilder($this->_em);
        $objNtvQueryCount = $this->_em->createNativeQuery(null, $objRsmCount);
        
        $strQueryCount = " SELECT COUNT(*) AS CANTIDAD ";
        $strQuery      = " SELECT ID_TIPO_CUENTA, DESCRIPCION_CUENTA  ";
 
        $strFromQuery  = " FROM DB_GENERAL.ADMI_TIPO_CUENTA ATC
                           WHERE ATC.ESTADO     =:strEstado
                           AND ATC.PAIS_ID    =:intIdPais ";
        
        if(isset($arrayParametros["arrayInValoresEsTarjetaTipoCuenta"]) && !empty($arrayParametros["arrayInValoresEsTarjetaTipoCuenta"]))
        {
            $strFromQuery .= "AND ATC.ES_TARJETA IN (:arrayInValoresEsTarjetaTipoCuenta) ";
            $objNtvQuery->setParameter('arrayInValoresEsTarjetaTipoCuenta', array_values($arrayParametros['arrayInValoresEsTarjetaTipoCuenta']));
            $objNtvQueryCount->setParameter('arrayInValoresEsTarjetaTipoCuenta', array_values($arrayParametros['arrayInValoresEsTarjetaTipoCuenta']));
        }
        else
        {
            $strFromQuery .= "AND ATC.ES_TARJETA = :strEsTarjeta ";
            $objNtvQuery->setParameter('strEsTarjeta', $arrayParametros['strEsTarjeta']);
            $objNtvQueryCount->setParameter('strEsTarjeta', $arrayParametros['strEsTarjeta']);
        }
        
        $strOrderByQuery = " ORDER BY DESCRIPCION_CUENTA ASC ";                    

        $objRsmCount->addScalarResult('CANTIDAD', 'Cantidad', 'integer');
        $objRsm->addScalarResult('ID_TIPO_CUENTA', 'intIdTipoCuenta', 'integer');
        $objRsm->addScalarResult('DESCRIPCION_CUENTA', 'strDescripcionCuenta', 'string');
          
        $objNtvQuery->setParameter('strEstado', $arrayParametros['strEstado']);
        $objNtvQuery->setParameter('intIdPais', $arrayParametros['intIdPais']);

        $objNtvQuery->setSQL($strQuery . $strFromQuery . $strOrderByQuery);
        $objDatos = $objNtvQuery->getResult();

        $objNtvQueryCount->setParameter('strEstado', $arrayParametros['strEstado']);
        $objNtvQueryCount->setParameter('intIdPais', $arrayParametros['intIdPais']);

        $strQueryCount = $strQueryCount . $strFromQuery;
        $objNtvQueryCount->setSQL($strQueryCount);
        $intTotal      = $objNtvQueryCount->getSingleScalarResult();

        $arrayResultado['objRegistros'] = $objDatos;
        $arrayResultado['intTotal']     = $intTotal;

        return $arrayResultado;
    }
    
    /**
    * Funcion que devuelve los tipos de tarjetas asociadas a los parametros recibidos
    * @author Andrés Montero <amontero@telconet.ec>
    * @param Array $arrayParametros
    * @version 1.0 23-05-2014
    * @return object
    * 
    * Actualización: Los parametros ahora se reciben por arreglo
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.1 10-07-2017
    * 
    */
    public function generarJson($arrayParametros)
    {
        $arr_encontrados = array();
        
        $arrayParametros['intStart'] = '';
        $arrayParametros['intLimit'] = '';
        $registrosTotal = $this->getRegistros($arrayParametros);
        $registros = $this->getRegistros($arrayParametros);
 
        if ($registros) {
            $num = count($registrosTotal);
            foreach ($registros as $data)
            {
                        
                $arr_encontrados[]=array('id_tipo_cuenta' =>$data->getId(),
                                         'descripcion_tipo_cuenta' =>trim($data->getDescripcionCuenta()),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 
                                                                                             'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 
                                                                                             'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_tipo_cuenta' => 0 , 
                                                        'descripcion_tipo_cuenta' => 'Ninguno', 
                                                        'tipo_cuenta_id' => 0 , 
                                                        'tipo_cuenta_nombre' => 'Ninguno', 
                                                        'estado' => 'Ninguno'
                                                       )
                                 );
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
    * Funcion que devuelve los tipos de tarjetas asociadas a los parametros recibidos
    * @author Andrés Montero <amontero@telconet.ec>
    * @param Array $arrayParametros
    * @version 1.0 23-05-2014
    * @return object
    * 
    * Actualización: Los parametros ahora se reciben por arreglo
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.1 10-07-2017
    * 
    */
    public function getRegistros($arrayParametros)
    {
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:AdmiTipoCuenta','sim');
        $boolBusqueda = false; 
        if(isset($arrayParametros['intIdPais']) && $arrayParametros['intIdPais']!="")
        {
            $boolBusqueda = true;
            $qb ->where( 'sim.paisId = :intIdPais');
            $qb->setParameter('intIdPais', $arrayParametros['intIdPais']);
        }
        if(isset($arrayParametros['strNombre']) && $arrayParametros['strNombre']!="")
        {
            $boolBusqueda = true;
            $qb ->andWhere( 'sim.descripcionCuenta like :strNombre');
            $qb->setParameter('strNombre', '%'.$arrayParametros['strNombre'].'%');
        }
        if(isset($arrayParametros['strEstado']) && $arrayParametros['strEstado']!="Todos")
        {
            $boolBusqueda = true;
            if(isset($arrayParametros['strEstado']) && $arrayParametros['strEstado']=="Activo")
            {
                $qb ->andWhere("LOWER(sim.estado) not like LOWER('Eliminado')");
            }
            else
            {
                $qb ->andWhere('LOWER(sim.estado) = LOWER(:strEstado)');
                $qb->setParameter('strEstado', $arrayParametros['strEstado']);
            }
        }
        
        if(isset($arrayParametros['intStart']) && $arrayParametros['intStart']!='' && !$boolBusqueda)
        {
            $qb->setFirstResult($arrayParametros['intStart']);   
        }
        if(isset($arrayParametros['intLimit']) && $arrayParametros['intLimit']!='')
        {
            $qb->setMaxResults($arrayParametros['intLimit']);
        }
        
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }
    
    
    public function findTiposCuentaPorEsTarjetaActivos($esTarjeta)
    {   
        
        $query = $this->_em->createQuery("SELECT DISTINCT tc.id, tc.descripcionCuenta
		FROM 
                schemaBundle:AdmiTipoCuenta tc
		WHERE 
                UPPER(tc.estado)='ACTIVO' 
                AND tc.esTarjeta='$esTarjeta'
                order by tc.descripcionCuenta ASC");
//$query->getSQL();die;
        $datos = $query->getResult();
        return $datos;
    }

    /**
    * Funcion que devuelve los tipos de tarjetas asociadas en estado activo
    * Consideraciones: Se toma las tarjetas solo en estado Activo 
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @param array $arrayParametros  => ('strTipo'           => S : si es tarjeta o N :si no es tarjeta
    *                                    'intIdPais'         => Id del Pais del Usuario en sesion
    *                                    'strPrefijoEmpresa' => Prefijo de empresa en sesion
    *                                  )     
    * @version 1.0 23-05-2014
    *
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.1 16-11-2018 - Se agrega a la funcion que verifique el tipo de cuenta por Pais, se verfica por prefijo empresa si se obtiene los
    *                           tipos de cuenta por el id del pais.
    * @return object
    */
    public function getTiposCuentaPorEsTarjetaActivos($arrayParametros)
    {   
        $objQuery      = $this->_em->createQuery();
        
        $strSelect      = "SELECT tc 
		                   FROM schemaBundle:AdmiTipoCuenta tc 
		                   WHERE 
                           tc.estado=:strEstado 
                           AND tc.esTarjeta=:strTipo 
                           AND tc.paisId=:intIdPais 
                           ORDER By tc.descripcionCuenta ASC ";
        
        $objQuery->setParameter('strEstado', 'Activo');
        $objQuery->setParameter('strTipo', $arrayParametros['strTipo']); 
        $objQuery->setParameter('intIdPais', $arrayParametros['intIdPais']);            
                        
        $objQuery->setDQL($strSelect);
        $objDatos    = $objQuery->getResult();	
        
	    return $objDatos;
    }

}
