<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiSintomaRepository extends EntityRepository
{

    /**
    * generarJson
    *
    * Esta funcion retorna en formato JSON la lista de Sintomas a presentarse en el grid
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 07-12-2015  Se realizan ajustes para presentar las hipotesis por tipo de caso
    *
    * @author Jose Bedon <jobedon@telconet.ec>
    * @version 1.2 19-11-2020 Se agrega filtro por departamento
    *
    * @version 1.0
    *
    * @param array  $parametros
    *
    * @return array $resultado
    *
    */
    public function generarJson($parametros)
    {
        $arr_encontrados = array();
        $arrayRegistros = $this->getRegistrosPorDepartamento($parametros);

        if (count($arrayRegistros) == 0)
        {
            $arrayRegistros = $this->getRegistros($parametros);
        }
        
        if ($arrayRegistros)
        {
            $intNum = count($arrayRegistros);
            
            foreach ($arrayRegistros as $data)
            {                                                        
                
                $arr_encontrados[]=array('id_sintoma'          => $data->getId(),
                                         'nombre_sintoma'      => trim($data->getNombreSintoma()),
                                         'descripcion_sintoma' => trim($data->getDescripcionSintoma()),
                                         'estado'              => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ?
                                                                              'Eliminado':'Activo'),
                                         'action1'             => 'button-grid-show',
                                         'action2'             => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ?
                                                                              'icon-invisible':'button-grid-edit'),
                                         'action3'             => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ?
                                                                              'icon-invisible':'button-grid-delete'));
            }

            if($intNum == 0)
            {
                $resultado = array('total'      => 1 ,
                                  'encontrados' => array('id_sintoma' => 0 , 'nombre_sintoma' => 'Ninguno', 'descripcion_sintoma' => 'Ninguno',
                                                         'sintoma_id' => 0 , 'sintoma_nombre' => 'Ninguno', 'estado'              => 'Ninguno'));
                $resultado = json_encode( $resultado);
                return $resultado;
            }
            else
            {
                $dataF     = json_encode($arr_encontrados);
                $resultado = '{"total":"'.$intNum.'","encontrados":'.$dataF.'}';
                return $resultado;
            }
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';
            return $resultado;
        }
        
    }
    
     /**
     * getRegistros
     *
     * Esta funcion retorna la lista de Sintomas a presentarse en el grid
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 07-12-2015   Se realizan ajustes para presentar las hipotesis por tipo de caso
     * 
     * @author Jose Bedon <jobedon@telconet.ec>
     * @version 1.2 19-11-2020 Se excluyen los sintomas agregados por la categorizacion de sintomas
     *
     * @version 1.0
     *
     * @param array  $parametros
     *
     * @return array $resultado
     *
     */
     public function getRegistros($parametros)
     {
    
        $nombre     = $parametros["nombre"];
        $estado     = $parametros["estado"];
        $start      = $parametros["start"];
        $limit      = $parametros["limit"];
        $codEmpresa = $parametros["codEmpresa"];
        $tipoCaso   = $parametros["tipoCaso"];
        $where      = '';

        if($nombre && $nombre!="")
        {
            $where .= " lower(sim.nombreSintoma) like lower('%$nombre%') AND";
        }
        if($estado && $estado!="Todos")
        {
            if($estado=="Activo")
            {
                $where .= " lower(sim.estado) not like lower('Eliminado') AND";
            }
            else
            {
                $where .= " lower(sim.estado) like lower('%$estado%') AND";
            }
        }
        if($codEmpresa && $codEmpresa!="")
        {
	        $where .= " lower(sim.empresaCod) = '$codEmpresa' ";
        }
        if($tipoCaso && $tipoCaso!="")
        {
            //Se consulta si existen configurados sintomas que esten asociadas unicamente a un tipo de caso y que esten activas,
            //caso contrario deben mostrarse todos los sintomas
            $sintomasXTipoCaso = $this->_em->getRepository('schemaBundle:AdmiSintoma')->findOneBy(array('estado'     => 'Activo',
                                                                                                        'tipoCasoId' => $tipoCaso ));
            $parametro = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                        ->getOne("TIPO CASO POR PROCESOS", "", "", "","Movilizacion", "", "", "");

            //Se agrega validacion solo para Tipo de Caso Movilizacion
            if($sintomasXTipoCaso && $tipoCaso == $parametro['valor2'])
            {
                $where .= " AND ( sim.tipoCasoId = '$tipoCaso' ) ";
            }
            else
            {
                $where .= " AND ( sim.tipoCasoId = '$tipoCaso' OR sim.tipoCasoId is null ) ";
            }
        }

        $where .= " AND NOT EXISTS (
                        SELECT 1 
                        FROM 
                        schemaBundle:AdmiParametroCab apc,
                        schemaBundle:AdmiParametroDet apd
                        WHERE apc.id = apd.parametroId
                        AND apc.nombreParametro = 'CATEGORIA_SINTOMA'
                        AND apc.estado = 'Activo'
                        AND apd.estado = 'Activo'
                        AND apd.valor1 = sim.id
                        AND apd.valor3 = 'Nuevo') ";

        
        $sql = "SELECT sim FROM
                schemaBundle:AdmiSintoma sim
                WHERE $where";

        $query     = $this->_em->createQuery($sql);
               
        $registros = $query->getResult();
	
        return $registros;
    }
    
    
    public function getSintomasXNombre($nombre){
    
	     $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:AdmiSintoma','sim');
            
                    
        
        if($nombre!=""){
        
            $boolBusqueda = true;
            $qb ->where("LOWER(sim.nombreSintoma) like LOWER(?1)");
            $qb->setParameter(1, '%'.$nombre.'%');
                        
        }               		                      
        
        
        $query = $qb->getQuery();               
                
        return $query->getResult();
    
    }

    /**
     * getRegistrosPorDepartamento
     * 
     * Funcion que filtra los sintomas por departamento
     * 
     * @author Jose Bedon <jobedon@telconet.ec>
     * @version 1.0 19-11-2020 
     * 
     */
    public function getRegistrosPorDepartamento($arrayParametros)
    {
        $strNombre       = $arrayParametros["nombre"];
        $strEstado       = $arrayParametros["estado"];
        $strCodEmpresa   = $arrayParametros["codEmpresa"];
        $intTipoCaso     = $arrayParametros["tipoCaso"];
        $intDepartamento = $arrayParametros["depart"];
        $strWhere        = '';

        if($strNombre && $strNombre!="")
        {
            $strWhere .= " AND lower(sim.nombreSintoma) like lower('%$strNombre%') ";
        }
        if($strEstado && $strEstado!="Todos")
        {
            if($strEstado=="Activo")
            {
                $strWhere .= " AND lower(sim.estado) not like lower('Eliminado') ";
            }
            else
            {
                $strWhere .= " AND lower(sim.estado) like lower('%$strEstado%') ";
            }
        }
        if($strCodEmpresa && $strCodEmpresa!="")
        {
	        $strWhere .= " AND lower(sim.empresaCod) = '$strCodEmpresa' ";
        }

        $strSql = "SELECT sim FROM
                schemaBundle:AdmiSintoma sim,
                schemaBundle:AdmiParametroCab apc,
                schemaBundle:AdmiParametroDet apd
                WHERE apc.id = apd.parametroId
                AND apc.nombreParametro = :nombreParametro
                AND apc.estado = :estado
                AND apd.estado = :estado
                AND apd.valor1 = sim.id
                AND sim.tipoCasoId = :tipoCasoId
                AND apd.valor2 = :idDepartamento
                $strWhere
                ";
        $objQuery = $this->_em->createQuery($strSql);

        $objQuery->setParameter('nombreParametro', 'CATEGORIA_SINTOMA');
        $objQuery->setParameter('estado', 'Activo');
        $objQuery->setParameter('tipoCasoId', $intTipoCaso);
        $objQuery->setParameter('idDepartamento', $intDepartamento);
               
        $arrayRegistros = $objQuery->getResult();
	
        return $arrayRegistros;

    }
    
    
    
   /* 
    public function getRegistrosss($nombre,$estado,$start,$limit,$codEmpresa=""){
    
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:AdmiSintoma','sim');
            
        $boolBusqueda = false;              
        
        if($nombre!=""){
        
            $boolBusqueda = true;
            $qb ->where("LOWER(sim.nombreSintoma) like LOWER(?1)");
            $qb->setParameter(1, '%'.$nombre.'%');
            
            echo $nombre.' - ';
        }
        
        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
                $qb ->andWhere("LOWER(sim.estado) not like LOWER('Eliminado')");
            }
            else{
                $qb ->andWhere("LOWER(sim.estado) = LOWER(?2)");
                $qb->setParameter(2, $estado);
            }
        }
        
        if($codEmpresa!=""){
	    $boolBusqueda = true;
            $qb ->where( "sim.empresaCod = ?3 ");
            $qb->setParameter(3, $codEmpresa);        
        }
		
        $qb->orderBy('sim.nombreSintoma','asc');
        
        if($start!='' && !$boolBusqueda)
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();
        
        echo $query->getSQL();
                
        return $query->getResult();
    }*/
}
