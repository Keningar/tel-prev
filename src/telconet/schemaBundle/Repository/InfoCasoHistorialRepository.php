<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use telconet\schemaBundle\DependencyInjection\BaseRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoCasoHistorialRepository extends EntityRepository
{
    /**
     * Funcion que sirve para crear y ejecutar sql para obtener el historial
     * de un caso
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1 10-06-2015
     * @since 1.0
     * @param int $idCaso
     * @param int $start
     * @param int $limit
     * @param String $order
     * @return array $resultado (registros, total)
     */
    public function getHistorialCaso($idCaso, $start, $limit, $order='ASC')
    {
        $qb  = $this->_em->createQueryBuilder();
        $qbC = $this->_em->createQueryBuilder();
        
        $qb ->select('e')
            ->from('schemaBundle:InfoCasoHistorial','e');
        $qbC->select('count(e.id)')
            ->from('schemaBundle:InfoCasoHistorial','e');
         
        if($idCaso!="")
        {
            $qb ->where('e.casoId = ?1');
            $qb ->setParameter(1, $idCaso);
            $qb ->orderBy('e.feCreacion',$order);
            
            $qbC->where('e.casoId = ?1');
            $qbC->setParameter(1, $idCaso);
        }
        
        if($start!='')
        {
            $qb->setFirstResult($start);   
        }
            
        if($limit!='')
        {
            $qb->setMaxResults($limit);
        }
        
        //total de objetos
        $total = $qbC->getQuery()->getSingleScalarResult();
        
        //obtener los objetos
        $query = $qb->getQuery();
        $datos = $query->getResult();
        
        $resultado['registros'] = $datos;
        $resultado['total']     = $total;
        
        return $resultado;
    }
    
    /**
     * Método encargado de verificar si existen casos duplicados
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 - 17-11-2018
     *
     * @param array $arrayData
     * @return array
     */
    public function getHistorialCasoPorDuplicidad($arrayData)
    {
        $strCodEmpresa = $arrayData['strCodEmpresa'];
        $intHoras = $arrayData['intHoras'];
        $strLogin = $arrayData['strLogin']; 
        
        $strRol = 'Cliente';
        $arrayResultado = null;
        
        try
        {
            $objRsm     = new ResultSetMappingBuilder($this->_em);
            $objQuery   = $this->_em->createNativeQuery(null, $objRsm);
            
            $strSql     = "SELECT ICH.ID_CASO_HISTORIAL, ICH.CASO_ID, ICH.ESTADO, ICH.FE_CREACION, T_HIST_MAX.NUMERO_CASO
                            FROM DB_SOPORTE.INFO_CASO_HISTORIAL ICH, ( SELECT T_HIST.* FROM (
                                 SELECT ICH2.CASO_ID AS CASO_ID, IC.NUMERO_CASO AS NUMERO_CASO
                                    FROM DB_SOPORTE.INFO_PARTE_AFECTADA IPA 
                                        JOIN DB_SOPORTE.INFO_PUNTO IP 
                                          ON IPA.AFECTADO_NOMBRE = IP.LOGIN, DB_SOPORTE.INFO_CASO IC 
                                        JOIN DB_SOPORTE.INFO_CASO_HISTORIAL ICH2 
                                          ON IC.ID_CASO = ICH2.CASO_ID, DB_SOPORTE.INFO_DETALLE_HIPOTESIS DH 
                                        JOIN DB_SOPORTE.INFO_DETALLE D 
                                          ON DH.ID_DETALLE_HIPOTESIS = D.DETALLE_HIPOTESIS_ID 
                                       WHERE IPA.DETALLE_ID = D.ID_DETALLE 
                                         AND IC.ID_CASO = DH.CASO_ID 
                                         AND IC.EMPRESA_COD = :strCodEmpresa 
                                         AND IPA.TIPO_AFECTADO = :strRol 
                                         AND LOWER(IP.LOGIN) = LOWER(:strLogin) 
                                         AND ICH2.FE_CREACION >= SYSDATE - ((1 / 24) * (:intHoras))
                                         ORDER BY ICH2.CASO_ID DESC
                                        ) T_HIST
                                   WHERE ROWNUM = 1
                                   ) T_HIST_MAX
                              WHERE ICH.CASO_ID = (T_HIST_MAX.CASO_ID)
                              ORDER BY ICH.FE_CREACION ASC" ;
            
            $objRsm->addScalarResult('ID_CASO_HISTORIAL','idCasoHistorial','integer');
            $objRsm->addScalarResult('CASO_ID','idCaso','integer');
            $objRsm->addScalarResult('ESTADO','estado','string');
            $objRsm->addScalarResult('FE_CREACION','fechaCreacion','string');
            $objRsm->addScalarResult('NUMERO_CASO','numeroCaso','string');

            $objQuery->setParameter('strCodEmpresa', $strCodEmpresa);
            $objQuery->setParameter('strRol', $strRol);
            $objQuery->setParameter('strLogin', $strLogin);
            $objQuery->setParameter('intHoras', $intHoras);

            $objQuery->setSQL($strSql);
            $arrayResultado = $objQuery->getResult();
        } catch (\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayResultado;
    }
}
