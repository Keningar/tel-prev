<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiTagRepository extends EntityRepository
{

    /**
     * generarJsonTags
     *
     * metodo que genera el json de los tags para el grid principal
     * @param mixed $codigo 
     * @param mixed $nombre 
     * @param mixed $estado 
     * @param mixed $start 
     * @param mixed $limit 
     * @return json
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 05-03-2015
     *
     */
    public function generarJsonTags($codigo, $nombre, $estado, $start, $limit)
    {
        $arr_encontrados = array();

        $tagsTotal = $this->getTags($codigo, $nombre, $estado, '', '');

        $tags = $this->getTags($codigo, $nombre, $estado, $start, $limit);

        if($tags)
        {
            $num = count($tagsTotal);

            foreach($tags as $tag)
            {
                $arr_encontrados[] = array('idTag' => $tag->getId(),
                    'descripcionTag' => trim($tag->getDescripcion()),
                    'observacionTag' => trim($tag->getObservacion()),
                    'estado' => (trim($tag->getEstado()) == 'Eliminado' ? 'Eliminado' : 'Activo'),
                    'action1' => 'button-grid-show',
                    'action3' => (trim($tag->getEstado()) == 'Eliminado' ? 'button-grid-invisible' : 'button-grid-delete'));
            }


            $data = json_encode($arr_encontrados);
            $resultado = '{"total":"' . $num . '","encontrados":' . $data . '}';

            return $resultado;
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';

            return $resultado;
        }
    }

    /**
     * getTags
     *
     * metodo que consulta los tags para el grid principal
     * @param mixed $codigo 
     * @param mixed $nombre 
     * @param mixed $estado 
     * @param mixed $start 
     * @param mixed $limit 
     * @return json
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 05-03-2015
     *
     */
    public function getTags($codigo, $nombre, $estado, $start, $limit)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e')
            ->from('schemaBundle:AdmiTag', 'e');

        if($codigo != "")
        {
            $qb->where('e.id = ?1');
            $qb->setParameter(1, $codigo);
        }
        if($nombre != "")
        {
            $qb->where('e.descripcion = ?3');
            $qb->setParameter(3, '%' . $nombre . '%');
        }
        if($estado != "Todos")
        {
            $qb->andWhere('e.estado = ?2');
            $qb->setParameter(2, $estado);
        }
        if($start != '')
            $qb->setFirstResult($start);
        if($limit != '')
            $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query->getResult();
    }

}
