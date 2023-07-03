<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use telconet\schemaBundle\Entity\InfoOficinaGrupo;
use \telconet\schemaBundle\Entity\ReturnResponse;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoOficinaGrupoRepository extends EntityRepository
{
    
    public function generarJson($em_general, $nombre,$estado,$start,$limit,$codEmpresa){
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($nombre, $estado, '', '',$codEmpresa);
        $registros = $this->getRegistros($nombre, $estado, $start, $limit,$codEmpresa);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                $nombreCanton = "";
                if($data->getCantonId())
                {    
                    $objCanton = $em_general->getRepository('schemaBundle:AdmiCanton')->findOneById($data->getCantonId());
                    $nombreCanton = $objCanton ? $objCanton->getNombreCanton() : "";
                }   
                
                $arr_encontrados[]=array('id_oficina_grupo' =>$data->getId(),
                                         'nombre_canton' =>trim($nombreCanton),
                                         'nombre_empresa' =>trim($data->getEmpresaId()->getNombreEmpresa()),
                                         'nombre_oficina' =>trim($data->getNombreOficina()),
                                         'direccion_oficina' =>trim($data->getDireccionOficina()),
                                         'es_matriz' =>($data->getEsMatriz()=='S'?'SI':'NO'),
                                         'telefono' =>trim($data->getTelefonoFijoOficina()),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0) 
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_oficina_grupo' => 0 , 'nombre_canton' => 'Ninguno', 'nombre_oficina' => 'Ninguno', 'nombre_empresa' => 'Ninguno', 
                                                        'direccion_oficina' => 'Ninguno', 'es_matriz' => 'Ninguno', 'telefono' => 'Ninguno', 
                                                        'oficina_id' => 0 , 'oficina_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
    
    public function getRegistros($nombre,$estado,$start,$limit,$codEmpresa){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:InfoOficinaGrupo','sim');
            
        $boolBusqueda = false;
        if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.nombreOficina) like LOWER(?1)');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        
        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
                $qb ->andWhere("LOWER(sim.estado) not like LOWER('Eliminado')");
            }
            else{
                $qb ->andWhere('LOWER(sim.estado) = LOWER(?2)');
                $qb->setParameter(2, $estado);
            }
        }
        
        if($codEmpresa!=""){
            $qb ->andWhere('sim.empresaId = ?3');
                $qb->setParameter(3, $codEmpresa);
        }
                
		if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }
    
    public function findNombrePorOficinaYEmnpresa($idOficina,$idEmpresa,$estado){
        $query = $this->_em->createQuery("SELECT iog
		FROM 
                schemaBundle:InfoOficinaGrupo iog
		WHERE 
                iog.empresaId='".$idEmpresa."' AND
                iog.id=".$idOficina." AND
                iog.estado='".$estado."'");
                $datos = $query->getSingleResult();
		return $datos;
    }

    /**
     * generarJsonOficinaGrupoPorEmpresa
     *
     * Metodo encargado de obtener las oficinas por las Empresas
     * @param integer $idEmpresa
     * @param string  $estado
     * @param integer $start
     * @param integer $limit
     *
     * @return array $resultado con listado de oficinas
     *
     * @author Desarrollo Inicial
     * @version 1.0
     * 
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-06-15 Validar el explode para el nombreOficina para cuando no contenga '-'
     */
    public function generarJsonOficinaGrupoPorEmpresa($idEmpresa,$estado,$start,$limit){
        $arr_encontrados = array();
        $resultado = '{"total":"0","encontrados":[]}';
        
        $entidadesTotal = $this->getOficinaGrupoPorEmpresa($idEmpresa,$estado,'','');
        
        $entidades = $this->getOficinaGrupoPorEmpresa($idEmpresa,$estado,$start,$limit);
        if ($entidades) {
            
            $num = count($entidadesTotal);
            
            foreach ($entidades as $entidad)
            {
                $nombreOficina = explode("-",$entidad->getNombreOficina());
                $oficina = $nombreOficina[0];
                if(count($nombreOficina) > 1)
                {
                    $oficina = $nombreOficina[1];
                }
                
                $arr_encontrados[]=array('idOficina' =>$entidad->getId(),
                                         'nombreOficina' =>trim($oficina)
                                        );
            }

            if($num == 0)
            {
                $resultado = json_encode( array('total'       => 1 ,
                                                'encontrados' => array('idConectorInterface'     => 0 ,
                                                                       'nombreConectorInterface' => 'Ninguno',
                                                                       'idConectorInterface'     => 0 ,
                                                                       'nombreConectorInterface' => 'Ninguno',
                                                                       'estado'                  => 'Ninguno'))
                                        );
            }
            else
            {
                $data = json_encode($arr_encontrados);
                $resultado = '{"total":"'.$num.'","encontrados":'.$data.'}';
            }
        }
        return $resultado;        
    }
   
    public function getOficinaGrupoPorEmpresa($idEmpresa,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:InfoOficinaGrupo','e');
            $qb ->where( "e.esVirtual = 'N'");
        
        if($idEmpresa!=""){
            $qb ->andWhere( 'e.empresaId = ?1');
            $qb->setParameter(1, $idEmpresa);
        }
        if($estado!="Todos"){
            $qb ->andWhere('e.estado = ?2');
            $qb->setParameter(2, $estado);
        }
        $qb->orderBy('e.nombreOficina', 'ASC');
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query->getResult();
    }
  
    /**
     * Devuelve la oficina matriz de la empresa correspondiente al codigo dado
     * @param string $empresaCod
     * @return InfoOficinaGrupo
     * @author ltama
     */
    public function getOficinaMatrizPorEmpresa($empresaCod)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb ->select('e')
            ->from('schemaBundle:InfoOficinaGrupo','e');
        $qb ->where( "e.esVirtual = 'N'")
            ->andWhere("e.empresaId = :empresaCod")
            ->andWhere("e.estado = 'Activo'")
            ->andWhere("e.esMatriz = 'S'");
        
        $qb->setParameter('empresaCod', $empresaCod);
        $qb->orderBy('e.feCreacion', 'ASC');
        $qb->setFirstResult(0);
        $qb->setMaxResults(1);
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
    }

    /**
     * El metodo getOficinasByPrefijoEmpresa retorna las oficinas por empresa en formato json
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 12-11-2014
     * @param  string   $strPrefijoEmpresa  Recibe el prefijo de la empresa
     * @param  string   $strEsMatriz        Recibe Si es Matriz o No
     * @param  string   $strEsVirtual       Recibe Si es Virtual o No
     * @return json     $jsonOficinas       Retorna el json de las oficinas
     */
    public function getOficinasByPrefijoEmpresaJson($strPrefijoEmpresa, $strEsMatriz, $strEsVirtual)
    {
        $dql = $this->_em->createQuery();
        $strOficnasByPrefijoEmpresa = "SELECT iog "
                                        . "FROM schemaBundle:InfoOficinaGrupo iog, "
                                        . "schemaBundle:InfoEmpresaGrupo ieg "
                                        . "WHERE iog.empresaId = ieg.id "
                                        . "AND ieg.estado = 'Activo' "
                                        . "AND iog.estado = 'Activo' ";
        if(!empty($strEsMatriz))
        {
            $strOficnasByPrefijoEmpresa .= "AND ieg.esMatriz = :strEsMatriz ";
            $dql->setParameter('strEsMatriz', $strEsMatriz);
        }
        if(!empty($strEsVirtual))
        {
            $strOficnasByPrefijoEmpresa .= "AND ieg.esVirtual = :strEsVirtual ";
            $dql->setParameter('strEsVirtual', $strEsVirtual);
        }
        $strOficnasByPrefijoEmpresa .= "AND ieg.prefijo = :strPrefijoEmpresa "
                                        . "ORDER BY iog.nombreOficina, iog.direccionOficina";
        $dql->setParameter('strPrefijoEmpresa', $strPrefijoEmpresa);
        $dql->setDQL($strOficnasByPrefijoEmpresa);
        $arrayOficinas['arrayDatos']    = $dql->getResult();
        $arrayOficinas['intTotalDatos'] = count($arrayOficinas['arrayDatos']);
        foreach($arrayOficinas['arrayDatos'] as $objOficinaGrupo):
            $arrayDatos[] = array('intIdOficina'        => $objOficinaGrupo->getId(),
                                  'strNombreOficina'    => $objOficinaGrupo->getNombreOficina());
        endforeach;
        $objDatos       = json_encode($arrayDatos);
        $jsonOficinas   = '{"intTotalDatos":"' . $arrayOficinas['intTotalDatos'] . '","objDatos":' . $objDatos . '}';
        return $jsonOficinas;
    } //getOficinasByPrefijoEmpresa
    
    
    /**
     * El metodo getOficinasPrincipalesByPrefijoEmpresaJson 
     * retorna las oficinas principales por empresa en formato json
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.0 01-07-2016
     * @param  string   $strPrefijoEmpresa  Recibe el prefijo de la empresa
     * @return json     $jsonOficinas       Retorna el json de las oficinas
     */
    public function getOficinasPrincipalesByPrefijoEmpresaJson($strPrefijoEmpresa)
    {
        $dql = $this->_em->createQuery();
        $strOficnasByPrefijoEmpresa = 
            "SELECT iog "
            . "FROM "
            . "schemaBundle:InfoOficinaGrupo iog, "
            . "schemaBundle:InfoEmpresaGrupo ieg "
            . "WHERE "
            . "iog.empresaId        = ieg.id "
            . "AND ieg.estado       = 'Activo' "
            . "AND iog.estado       = 'Activo' "
            . "AND ieg.prefijo      = :strPrefijoEmpresa "
            . "AND iog.refOficinaId is null "
            . "ORDER BY iog.nombreOficina, iog.direccionOficina";
        $dql->setParameter('strPrefijoEmpresa', $strPrefijoEmpresa);
        $dql->setDQL($strOficnasByPrefijoEmpresa);
        $arrayOficinas['arrayDatos']    = $dql->getResult();
        $arrayOficinas['intTotalDatos'] = count($arrayOficinas['arrayDatos']);
        foreach($arrayOficinas['arrayDatos'] as $objOficinaGrupo):
            $arrayDatos[] = array('intIdOficina'        => $objOficinaGrupo->getId(),
                                  'strNombreOficina'    => $objOficinaGrupo->getNombreOficina());
        endforeach;
        $objDatos       = json_encode($arrayDatos);
        $jsonOficinas   = '{"intTotalDatos":"' . $arrayOficinas['intTotalDatos'] . '","objDatos":' . $objDatos . '}';
        return $jsonOficinas;
    }
    
    
    /**
     * getOficinasByEmpresa, obtiene las oficinas según la empresa en sesión.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 09-12-2016
     * 
     * @param array $arrayParametros[]
     *              'strPrefijoEmpresa'   => Recibe el prefijo de la empresa en sesión.
     *              'strEstadoEmpresa'    => Recibe el estado de la empresa 
     *              'strEstadoOficina'    => Recibe el estado de la oficina
     *                              ]
     * @return \telconet\schemaBundle\Entity\ReturnResponse Retorna un objeto con el resultado de la consulta
     */
    public function getOficinasByEmpresa($arrayParametros)
    {
        $objReturnResponse = new ReturnResponse();
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        try
        {
            $objQueryCount = $this->_em->createQuery();
            $strQueryCount = "SELECT COUNT(iog) ";
            $objQuery = $this->_em->createQuery();
            
            $strQuery = "  SELECT iog.id            intIdObj,    "
                       ."         iog.nombreOficina strDescripcionObj ";
            $strFromQuery = "  FROM "
                               . "schemaBundle:InfoOficinaGrupo iog, "
                               . "schemaBundle:InfoEmpresaGrupo ieg "
                               . "WHERE "
                               . "iog.empresaId        = ieg.id "
                               . "AND ieg.estado       = :strEstadoEmpresa "
                               . "AND iog.estado       = :strEstadoOficina "
                               . "AND ieg.prefijo      = :strPrefijoEmpresa "; 
                    
            $objQuery->setParameter('strEstadoEmpresa' , $arrayParametros['strEstadoEmpresa']);
            $objQuery->setParameter('strEstadoOficina' , $arrayParametros['strEstadoOficina']);
            $objQuery->setParameter('strPrefijoEmpresa', $arrayParametros['strPrefijoEmpresa']);
            $objQuery->setDQL($strQuery . $strFromQuery);
            $objReturnResponse->setRegistros($objQuery->getResult());
            $objReturnResponse->setTotal(0);
            if($objReturnResponse->getRegistros())
            { 
                $strQueryCount = $strQueryCount . $strFromQuery;
                $objQueryCount->setDQL($strQueryCount);
                $objReturnResponse->setTotal($objQueryCount->getSingleScalarResult());
            }
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrMessageStatus('Existio un error en getOficinasByEmpresa - ' . $ex->getMessage());
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
        }
        return $objReturnResponse;
    } 

    /**
     * getOficinaByLogin, obtiene la oficina según el login enviado.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 02-06-2020
     * 
     * @param array $arrayParametros['strLogin'   => Login de la persona a consultar.]
     *                       
     * @return $arrayOficina Retorna un arreglo con el resultado de la consulta
     */
    public function getOficinaByLogin($arrayParametros)
    {
        try
        {
            $objRsm         = new ResultSetMappingBuilder($this->_em);
            $objQuery       = $this->_em->createNativeQuery(null,$objRsm);
            
            $strQuery = "  SELECT * 
            FROM INFO_OFICINA_GRUPO 
            WHERE ID_OFICINA = (SELECT INFOPER2.OFICINA_ID 
                                FROM INFO_PERSONA_EMPRESA_ROL INFOPER2 
                                WHERE ID_PERSONA_ROL = (SELECT MIN(INFOPER.ID_PERSONA_ROL) 
                                                        FROM INFO_PERSONA_EMPRESA_ROL INFOPER
                                                        WHERE INFOPER.PERSONA_ID = (SELECT INFOP.ID_PERSONA
                                                                                    FROM INFO_PERSONA INFOP
                                                                                    WHERE INFOP.LOGIN = :strLogin
                                                                                    ) 
                                                        AND INFOPER.ESTADO = :strEstado)) "; 
                
            $objQuery->setParameter('strEstado' , 'Activo');
            $objQuery->setParameter('strLogin' , $arrayParametros['strLogin']);
            
            $objRsm->addScalarResult('CODIGO_POSTAL_OFI', 'codigoPostalOfi', 'string');

            $objQuery->setSQL($strQuery);
            
            $arrayOficina['resultado']    = $objQuery->getResult();
            $arrayOficina['status']       = 'OK';

        }
        catch(\Exception $ex)
        {
            $arrayOficina['resultado']    = '';
            $arrayOficina['status']       = 'ERROR';
        }
        return $arrayOficina;
    }
    
}
