<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiPlantillaRepository extends EntityRepository
{
    public function generarJson($nombre,$estado,$modulo,$codigo,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($nombre, $estado,$modulo,$codigo, '', '');
        $registros = $this->getRegistros($nombre, $estado,$modulo,$codigo, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {					  
                $arr_encontrados[]=array('id_plantilla' =>$data->getId(),
                                         'nombre_plantilla' =>trim($data->getNombrePlantilla()),
                                         'codigo' =>trim($data->getCodigo()),
                                         'modulo' =>trim($data->getModulo()),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_plantilla' => 0 , 'nombre_plantilla' => 'Ninguno', 'codigo' => 'Ninguno' ,'estado' => 'Ninguno'));
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
    
    public function getRegistros($nombre,$estado,$modulo,$codigo,$start,$limit){
        	     
    
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:AdmiPlantilla','sim');
            
        $boolBusqueda = false; 
        
        if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( "LOWER(sim.nombrePlantilla) like LOWER('%".$nombre."%')");
            //$qb->setParameter(1, '%'.$nombre.'%');
        }
        
         if($codigo!=""){
            $boolBusqueda = true;
            $qb ->andWhere( "LOWER(sim.codigo) like LOWER('%".$codigo."%')");
            //$qb->setParameter(2, '%'.$codigo.'%');
        }
        
         if($modulo!=""){
            $boolBusqueda = true;
            $qb ->andWhere( "LOWER(sim.modulo) like LOWER('%".$modulo."%')");
            //$qb->setParameter(3, '%'.$modulo.'%');
        }
        
        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
                $qb ->andWhere("LOWER(sim.estado) not like LOWER('Eliminado')");
            }
            else{
                $qb ->andWhere("LOWER(sim.estado) = LOWER('%".$estado."%')");
               // $qb->setParameter(4, $estado);
            }
        }
        
        if($start!='' && !$boolBusqueda)
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();              
        
        return $query->getResult();
    }
    
    
    /**
     * getPlantillaXCodigoYEmpresa
     * 
     * Método que obtiene la plantilla con la que se enviará el mail
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 - Se considera el codigo de la empresa para obtener la plantilla. 
     * @since 17-06-2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 - Se quita el codigo de la empresa porque las plantillas son genericas. 
     * @since 20-06-2016
     * @param String $codigo     Código de la plantilla que se quiere consultar
     * @param String $empresaCod Código de la empresa del usuario en sesión.
     * @return queryResult
     */
    public function getPlantillaXCodigoYEmpresa($codigo)
    {
        $qb = $this->_em->createQueryBuilder();
        
        $qb->select('sim')->from('schemaBundle:AdmiPlantilla','sim');                                    

        if($codigo!="")
        {            
            $qb->where( 'LOWER(sim.codigo) = LOWER(?1)');
            $qb->setParameter(1, $codigo);
        }

        $qb->andWhere("sim.estado <> 'Eliminado'");               

        $query = $qb->getQuery();                

        return $query->getResult();
    }
    
    /**
     * getAliasXPlantilla
     *
     * Metodo encargado de obtener todos los alias relacionados a una plantilla
     *     
     * @param integer $idPlantilla        
     * @param integer $empresaCod       
     * @param integer $idCiudad       
     * @param integer $idDepartamento       
     * @param string  $strEsCopia      
     * 
     * @return array con los alias de la plantilla
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.5 - 14-11-2014  (Se agrega la busqueda si el alias esCopia o no)
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 - Version Inicial
     */  
    public function getAliasXPlantilla($idPlantilla , $empresaCod="" , $idCiudad="" , $idDepartamento="" , $strEsCopia )
    {                
        $query = $this->_em->createQuery();
        $sql   =
                "SELECT 
                alias                
                FROM
                schemaBundle:AdmiAlias alias,
                schemaBundle:InfoAliasPlantilla info                
                WHERE
                alias.id         =   info.aliasId and 
                info.plantillaId =   :plantilla and
                info.esCopia     =   :esCopia ";   
        
        $query->setParameter("plantilla", $idPlantilla);
        $query->setParameter("esCopia", $strEsCopia);    

        if($empresaCod != "")
        {
            $sql .= " and alias.empresaCod = :empresa ";            
            $query->setParameter("empresa", $empresaCod);
        }

        if($idCiudad != "")
        {            
            $sql .= " and alias.cantonId = :canton ";
            $query->setParameter("canton", $idCiudad);
        }

        if($idDepartamento != "")
        {            
            $sql .= " and alias.departamentoId = :departamento ";
            $query->setParameter("departamento", $idDepartamento);
        }               

        $sql .= " and alias.estado <> :estadoAlias ";            
        $query->setParameter("estadoAlias", 'Eliminado');
        
        $sql .= " and info.estado  <> :estadoInfoPlantilla ";            
        
        $query->setParameter("estadoInfoPlantilla", 'Eliminado');     
        
        $query->setDQL($sql);                

        $resultado = $query->getResult();

        $to = array();

        foreach($resultado as $data):

            $to[] = $data->getValor();

        endforeach;

        return $to;
    }

}