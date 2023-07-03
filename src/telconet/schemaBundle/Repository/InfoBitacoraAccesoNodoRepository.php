<?php

namespace telconet\schemaBundle\Repository;

use telconet\schemaBundle\DependencyInjection\BaseRepository;

class InfoBitacoraAccesoNodoRepository extends BaseRepository
{

    /**
     * queryByParams
     *
     * Método encargado de obtener el query de las bitácoras filtradas según los 
     * parámetro enviados.
     * 
     * @param $arrayParams => filter params
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0 01-04-2021
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.1 18-05-2023 - Se corrigue filtro de fechas para que tome el estado de la bitacora y muestre todas las registradas.
     * 
     */
    private function queryByParams($arrayParams)
    {
        $objQB = $this->createQueryBuilder('iban');
        
        if (array_key_exists('empresaCod', $arrayParams))
        {
            $objQB
                ->andWhere('iban.empresaCod = :empresaCod')
                ->setParameter('empresaCod', $arrayParams['empresaCod']);
        }

        if (array_key_exists('nombreNodo', $arrayParams))
        {
            $objQB
                ->andWhere('LOWER(iban.nombreNodo) LIKE LOWER(:nombreNodo)')
                ->setParameter('nombreNodo', '%'.$arrayParams['nombreNodo'].'%');
        }

        if (array_key_exists('estado', $arrayParams))
        {
            $objQB
                ->andWhere('iban.estado = :status')
                ->setParameter('status', $arrayParams['estado']);
        }

        if (array_key_exists('canton', $arrayParams))
        {
            $objQB
                ->andWhere('LOWER(iban.canton) LIKE LOWER(:canton)')
                ->setParameter('canton', '%'.$arrayParams['canton'].'%');
        }

        if (array_key_exists('tecnicoAsignado', $arrayParams))
        {
            $objQB
                ->andWhere('LOWER(iban.tecnicoAsignado) LIKE LOWER(:login)')
                ->setParameter('login', '%'.$arrayParams['tecnicoAsignado'].'%');
        }

        if (array_key_exists('feCreacion', $arrayParams))
        {
            $strDateFrom = $arrayParams['feCreacion'].' '.'00:00:00';
            $objQB->andWhere('iban.feCreacion >= :dateFrom')
                  ->setParameter('dateFrom', $strDateFrom);
            if (array_key_exists('feMod', $arrayParams)) 
            {
                $strDateTo = $arrayParams['feMod'].' '.'23:59:59';
                $objQB->andWhere('iban.feUltMod <= :dateTo');
                
            }else
            {
                $strDateTo = $arrayParams['feCreacion'].' '.'23:59:59';
                 $objQB->andWhere('iban.feCreacion <= :dateTo')
                ->andWhere('iban.feCreacion <= :dateTo');
            }
            $objQB->setParameter('dateTo', $strDateTo);
        }

        if (array_key_exists('feMod', $arrayParams) && !array_key_exists('feCreacion', $arrayParams))
        {
            $strDateFrom = $arrayParams['feMod'].' '.'00:00:00';
            $strDateTo = $arrayParams['feMod'].' '.'23:59:59';
            $objQB->andWhere('iban.feUltMod >= :dateFrom')
                  ->andWhere('iban.feUltMod <= :dateTo')
                  ->setParameter('dateFrom', $strDateFrom)
                  ->setParameter('dateTo', $strDateTo);
        }


        if (array_key_exists('ciudad', $arrayParams))
        {
            $objQB
                ->andWhere('LOWER(iban.canton) LIKE LOWER(:canton)')
                ->setParameter('canton', '%'.$arrayParams['ciudad'].'%');
        }

        if (array_key_exists('departamento', $arrayParams))
        {
            $objQB
                ->andWhere('LOWER(iban.departamento) LIKE LOWER(:departamento)')
                ->setParameter('departamento', '%'.$arrayParams['departamento'].'%');
        }

        if (array_key_exists('tarea', $arrayParams))
        {
            $objQB
                ->andWhere('LOWER(iban.tareaId) = :tareaId')
                ->setParameter('tareaId', $arrayParams['tarea']);
        }

        if(!array_key_exists('tarea',$arrayParams) && !array_key_exists('nombreNodo',$arrayParams) &&
            ((!$arrayParams['estado']) || $arrayParams['estado'] == 'Todos') && !array_key_exists('canton',$arrayParams) 
            && !array_key_exists('feCreacion',$arrayParams) && !array_key_exists('tecnicoAsignado',$arrayParams) && 
            !array_key_exists('feMod',$arrayParams) &&!array_key_exists('ciudad',$arrayParams) && 
            !array_key_exists('departamento',$arrayParams))
        {
            $strDatePast = date_create(date('d-M-Y', strtotime('-2 day')));
            $strDatePast = date_format($strDatePast,"Y/m/d H:i:s");
            $objQB->andWhere('iban.feCreacion >= :dateFrom')
            ->setParameter('dateFrom', $strDatePast);
        }

        $objQB->orderBy('iban.feCreacion', 'DESC');
        return $objQB->getQuery();
    }

    /**
     * findByParams
     *
     * Método encargado de obtener todas las bitácoras con o sin filtros.
     * 
     * @param $arrayParams => filter params
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0 01-04-2021
     * 
     */
    public function findByParams($arrayParams)
    {
        $objQuery = $this
            ->createQueryBuilder('iban')
            ->getQuery();

        if (!empty($arrayParams))
        {
            $objQuery = $this->queryByParams($arrayParams);
        }

        $intStart = $arrayParams['intStart'];
        $intLimit = $arrayParams['intLimit'];

        $objQuery->setFirstResult($intStart);
        $objQuery->setMaxResults($intLimit);

        return $objQuery->getResult();
    }

    public function findByParamsCount($arrayParams)
    {
        $objQuery = $this
            ->createQueryBuilder('iban')
            ->getQuery();

        if (!empty($arrayParams))
        {
            $objQuery = $this->queryByParams($arrayParams);
        }

        return $objQuery->getResult();
    }

    /**
     * count
     *
     * Método encargado de obtener el número total de bitácoras.
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0 01-04-2021
     * 
     */
    public function count()
    {
        $objQB = $this->createQueryBuilder('iban');
        return $objQB
            ->select('count(iban.id)')
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getSingleScalarResult();
    }
}
