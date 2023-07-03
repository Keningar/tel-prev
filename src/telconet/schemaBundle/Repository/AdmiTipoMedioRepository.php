<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiTipoMedioRepository extends EntityRepository
{
    public function generarJsonComboTiposMedios($nombre){
        $arr_encontrados = array();
        
        $tiposMedios = $this->getTiposMedios('',$nombre,'Activo','','');

        if ($tiposMedios) {
            
            $num = count($tiposMedios);
            
            foreach ($tiposMedios as $tipoMedio)
            {
                $arr_encontrados[]=array('idTipoMedio' =>$tipoMedio->getId(),
                                         'nombreTipoMedio' =>trim($tipoMedio->getNombreTipoMedio()));
            }

	    $data=json_encode($arr_encontrados);
	    $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

	    return $resultado;
            
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';

            return $resultado;
        }
        
    }
    
    public function generarJsonTiposMedios($codigo,$nombre,$estado,$start,$limit){
        $arr_encontrados = array();
        
        $tiposMediosTotal = $this->getTiposMedios($codigo,$nombre,$estado,'','');
        
        $tiposMedios = $this->getTiposMedios($codigo,$nombre,$estado,$start,$limit);
//        error_log('entra');
        if ($tiposMedios) {
            
            $num = count($tiposMediosTotal);
            
            foreach ($tiposMedios as $tipoMedio)
            {
                $arr_encontrados[]=array('idTipoMedio' =>$tipoMedio->getId(),
                                         'codigoTipoMedio' =>trim($tipoMedio->getCodigoTipoMedio()),
                                         'nombreTipoMedio' =>trim($tipoMedio->getNombreTipoMedio()),
                                         'estado' =>(trim($tipoMedio->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($tipoMedio->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($tipoMedio->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
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
   
    public function getTiposMedios($codigo,$nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiTipoMedio','e');
               
            
        if($codigo!=""){
            $qb ->where( 'e.codigoTipoMedio = ?1');
            $qb->setParameter(1, $codigo);
        }
        if($nombre!=""){
            $qb ->where( 'e.nombreTipoMedio = ?3');
            $qb->setParameter(3, '%'.$nombre.'%');
        }
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
    
    
    public function getJsonTiposMedios()
    {
        return $this->generarJsonComboTiposMedios('');
    }
    
    
    /**
     * getTipoMedioPorDetalleSol
     * 
     * Obtiene el tipo medio de una la solicitud.
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 03-10-2018
     * costoQuery: 6
     * @param  array $arrayParametros [
     *                                  "intIdDetalleSol"     : Id del detalle de la solicitud      
     *                                ]          
     * 
     * @return array $arrayResultado
     */
    public function getTipoMedioPorDetalleSol($arrayParametros)
    {     
        $objRsm           = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery      = $this->_em->createNativeQuery(null, $objRsm);
        $intIdDetalleSol  = $arrayParametros['intIdDetalleSol'];
        
        $strSql           = "   SELECT ATM.CODIGO_TIPO_MEDIO 
                                FROM 
                                    DB_COMERCIAL.INFO_DETALLE_SOLICITUD IDS
                                    INNER JOIN DB_SOPORTE.INFO_SERVICIO_TECNICO IST ON IDS.SERVICIO_ID = IST.SERVICIO_ID 
                                    INNER JOIN DB_SOPORTE.ADMI_TIPO_MEDIO ATM ON IST.ULTIMA_MILLA_ID = ATM.ID_TIPO_MEDIO 
                                WHERE 
                                    IDS.ID_DETALLE_SOLICITUD = :idDetaSolicitud 
                                AND 
                                    ATM.ESTADO = :estado ";
        
        $objRsm->addScalarResult('CODIGO_TIPO_MEDIO','codTipoMedio','string');
                
        $objNtvQuery->setParameter('idDetaSolicitud', $intIdDetalleSol);
        $objNtvQuery->setParameter('estado', 'Activo');
        
        $objNtvQuery->setSQL($strSql);
        $arrayResultado = $objNtvQuery->getOneOrNullResult();
                
        return $arrayResultado;
    }
    
}
