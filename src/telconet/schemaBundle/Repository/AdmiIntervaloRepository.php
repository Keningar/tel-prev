<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiIntervaloRepository extends EntityRepository
{
    /*
    * Costo: 3
    *
    * getIntervalos
    * Obtiene los intervalos
    *
    * @param array $arrayParametros[ "strEstado" => estado del intervalo ]
    *
    * @return array $arrayRespuesta
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 04-04-2018
	*/
    public function getIntervalos($arrayParametros)
    {
        $objQuery  = $this->_em->createQuery();
        $strEstado = $arrayParametros['strEstado'];

        $strSql = " SELECT ain
                        FROM schemaBundle:AdmiIntervalo ain ";

        $strWhere = " WHERE ain.estado = :paramEstado ";

        if($strEstado != "Todos")
        {
            $strSql = $strSql . $strWhere;
            $objQuery->setParameter("paramEstado", $strEstado);
        }

        $objQuery->setDQL($strSql);

        $arrayRespuesta = $objQuery->getResult();

        return $arrayRespuesta;
    }
}
