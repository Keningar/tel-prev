<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Clase repositorio para AdmiClaseTipoElemento
 * 
 * 
 */
class AdmiClaseTipoMedioRepository extends EntityRepository
{
    /**
     * Funcion que sirve para generar un json de los objetos obtenidos
     * por medio del filtro
     * 
     * @version 1.0 24-02-2015
     * @author Francisco Adum <fadum@telconet.ec>
     * @param $tipoMedioId              int
     * @param $nombreClaseTipoMedio     String
     * @param $estado                   String
     * @param $start                    int
     * @param $limit                    int
     */
    public function generarJsonClaseTipoMedio($tipoMedioId,$nombreClaseTipoMedio,$estado,$start,$limit){
        $arr_encontrados = array();
                
        $result = $this->getClaseTipoMedio($tipoMedioId, $nombreClaseTipoMedio, $estado, $start, $limit);

        $encontrados = $result['registros'];
        $encontradosTotal = $result['total'];

        if ($encontrados) {
            
            $num = $encontradosTotal;
            
            foreach ($encontrados as $objeto)
            {
                $arr_encontrados[]=array('idClaseTipoMedio'     => $objeto->getId(),
                                         'tipoMedioId'          => trim($objeto->getTipoMedioId()),
                                         'nombreClaseTipoMedio' => trim($objeto->getNombreClaseTipoMedio()),
                                         'estado'               => (trim($objeto->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1'              => 'button-grid-show',
                                         'action2'              => (trim($objeto->getEstado())=='Eliminado' ? 
                                                                    'button-grid-invisible':'button-grid-edit'),
                                         'action3'              => (trim($objeto->getEstado())=='Eliminado' ? 
                                                                    'button-grid-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado = array('total' => 1,
                                   'encontrados' => array('idHilo' => 0, 'numeroHilo' => 'Ninguno'));
                $resultado = json_encode($resultado);

                return $resultado;
            }
            else
            {
                $data = json_encode($arr_encontrados);
                $resultado = '{"total":"' . $num . '","encontrados":' . $data . '}';

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
     * Funcion que sirve para generar y ejecutar un sql de los parametros
     * obtenidos del filtro
     * 
     * @version 1.0 24-02-2015
     * @author Francisco Adum <fadum@telconet.ec>
     * @param $tipoMedioId              int
     * @param $nombreClaseTipoMedio     String
     * @param $estado                   String
     * @param $start                    int
     * @param $limit                    int
     */
    public function getClaseTipoMedio($tipoMedioId, $nombreClaseTipoMedio, $estado, $start, $limit)
    {
        $qb = $this->_em->createQueryBuilder();
        $qbC = $this->_em->createQueryBuilder();
        //query para obtener la informacion
        $qb->select('AdmiClaseTipoMedio')
            ->from('schemaBundle:AdmiClaseTipoMedio', 'AdmiClaseTipoMedio');

        //query para obtener la cantidad de registros
        $qbC->select('count(AdmiClaseTipoMedio.id)')
            ->from('schemaBundle:AdmiClaseTipoMedio', 'AdmiClaseTipoMedio');

        if($nombreClaseTipoMedio != "")
        {
            $qb->where('UPPER(AdmiClaseTipoMedio.nombreClaseTipoMedio) like ?1');
            $qb->setParameter(1, "%" . strtoupper($nombreClaseTipoMedio) . "%");
            $qbC->where('UPPER(AdmiClaseTipoMedio.nombreClaseTipoMedio) like ?1');
            $qbC->setParameter(1, "%" . strtoupper($nombreClaseTipoMedio) . "%");
        }
        if($tipoMedioId != "")
        {
            $qb->andWhere('AdmiClaseTipoMedio.tipoMedioId = ?2');
            $qb->setParameter(2, $tipoMedioId);
            $qbC->andWhere('AdmiClaseTipoMedio.tipoMedioId = ?2');
            $qbC->setParameter(2, $tipoMedioId);
        }
        if($estado != "Todos")
        {
            $qb->andWhere('UPPER(AdmiClaseTipoMedio.estado) = UPPER(?3)');
            $qb->setParameter(3, $estado);
            $qbC->andWhere('UPPER(AdmiClaseTipoMedio.estado) = UPPER(?3)');
            $qbC->setParameter(3, $estado);
        }

        if($start != '')
            $qb->setFirstResult($start);
        if($limit != '')
            $qb->setMaxResults($limit);
        
        //total de objetos
        $total = $qbC->getQuery()->getSingleScalarResult();
        //obtener los objetos
        $query = $qb->getQuery();
        $datos = $query->getResult();
        $resultado['registros'] = $datos;
        $resultado['total'] = $total;

        return $resultado;
    }
}
