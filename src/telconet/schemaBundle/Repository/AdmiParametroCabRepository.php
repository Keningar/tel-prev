<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiParametroCabRepository extends EntityRepository
{
     /**
     * findParametrosCab, Crea la sentencia DQL según los parámetros enviados de la entidad AdmiParametroCab
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 09-09-2015
     * @param   array $arrayParametros['strNomreParametro' => Nombre del Parámetro,
     *                                 'strDescripcion'    => Dscripcion del parámetro,
     *                                 'strModulo'         => Nombre del Modulo en el cual se usará el parámetro,
     *                                 'strProceso'        => Proceso para el cual se usa el parámetro,
     *                                 'strEstado'         => Estado de parámetro
     *                                 'strUsrCreacion'    => Usuario que creó el parámetro]
     * @return array Retorna un array con la información devuelta por la entidad AdmiParametroCab según los filtros enviados.
     */
    public function findParametrosCab($arrayParametros)
    {
        $arrayResult['strMensajeError'] = '';
        try
        {
            $objQueryCount = $this->_em->createQuery();
            $strQueryCount = "SELECT count(apc.id) ";
            $objQuery = $this->_em->createQuery();
            $strQuery = "SELECT apc.nombreParametro strNombreParametro, "
                                . "apc.id intIdParametro, "
                                . "apc.descripcion strDescripcion, "
                                . "apc.modulo strModulo, "
                                . "apc.proceso strProceso, "
                                . "apc.estado strEstado, "
                                . "apc.usrCreacion strUsrCreacion, "
                                . "apc.feCreacion strFeCreacion, "
                                . "apc.ipCreacion strIpCreacion, "
                                . "apc.usrUltMod strUltMod, "
                                . "apc.feUltMod strFeUltMod, "
                                . "apc.ipUltMod strIpUltMod ";
                                
            $strFromQuery = "FROM schemaBundle:AdmiParametroCab apc "
                            . " WHERE 1 = 1";

            //Pregunta si $arrayParametros['strNombreParametro'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['strNombreParametro']))
            {
                $strFromQuery .= " AND apc.nombreParametro LIKE :strNombreParametro";
                $objQuery->setParameter('strNombreParametro', '%'.$arrayParametros['strNombreParametro'].'%');
                $objQueryCount->setParameter('strNombreParametro', '%'.$arrayParametros['strNombreParametro'].'%');
            }
            //Pregunta si $arrayParametros['strDescripcion'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['strDescripcion']))
            {
                $strFromQuery .= " AND apc.descripcion LIKE :strDescripcion";
                $objQuery->setParameter('strDescripcion', '%'.$arrayParametros['strDescripcion'].'%');
                $objQueryCount->setParameter('strDescripcion', '%'.$arrayParametros['strDescripcion'].'%');
            }
            //Pregunta si $arrayParametros['strModulo'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['strModulo']))
            {
                $strFromQuery .= " AND apc.modulo LIKE :strModulo";
                $objQuery->setParameter('strModulo', '%'.$arrayParametros['strModulo'].'%');
                $objQueryCount->setParameter('strModulo', '%'.$arrayParametros['strModulo'].'%');
            }
            //Pregunta si $arrayParametros['strProceso'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['strProceso']))
            {
                $strFromQuery .= " AND apc.proceso LIKE :strProceso";
                $objQuery->setParameter('strProceso', '%'.$arrayParametros['strProceso'].'%');
                $objQueryCount->setParameter('strProceso', '%'.$arrayParametros['strProceso'].'%');
            }
            //Pregunta si $arrayParametros['strEstado'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['strEstado']))
            {
                $strFromQuery .= " AND apc.estado IN (:strEstado)";
                $objQuery->setParameter('strEstado', $arrayParametros['strEstado']);
                $objQueryCount->setParameter('strEstado', $arrayParametros['strEstado']);
            }
            //Pregunta si $arrayParametros['strUsrCreacion'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['strUsrCreacion']))
            {
                $strFromQuery .= " AND apc.usrCreacion LIKE :strUsrCreacion";
                $objQuery->setParameter('strUsrCreacion', '%'.$arrayParametros['strUsrCreacion'].'%');
                $objQueryCount->setParameter('strUsrCreacion', '%'.$arrayParametros['strUsrCreacion'].'%');
            }
            $strFromQuery .= " ORDER BY apc.feCreacion";
            $objQuery->setDQL($strQuery . $strFromQuery);
            $arrayResult['arrayResultado'] = $objQuery->setFirstResult($arrayParametros['intStart'])
                                                      ->setMaxResults($arrayParametros['intLimit'])->getResult();
            //Pregunta si $arrayResult['arrayResultado'] es diferente de vacio para realizar el count
            if(!empty($arrayResult['arrayResultado']))
            {
                $strQueryCount = $strQueryCount . $strFromQuery;
                $objQueryCount->setDQL($strQueryCount);
                $arrayResult['intTotal'] = $objQueryCount->getSingleScalarResult();
            }
        }
        catch(\Exception $ex)
        {
            $arrayResult['strMensajeError'] = 'Existion un error en findParametrosCab - ' . $ex->getMessage();
        }
        return $arrayResult;
    }//findParametrosCab
    
    /**
     * getMotivosCambioFormaPago
     *     
     * Método que retorna array de motivos por cambio de forma de pago.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 30-09-2019     
     
     * @return  $arrayMotivoFactura
     *
     */
    public function getMotivosCambioFormaPago()
    {    
        $arrayMotivoFactura     = array();
        try
        {
            $objParametroCab = $this->_em->getRepository('schemaBundle:AdmiParametroCab')
                                         ->findOneBy(array("nombreParametro" => "MOTIVOS_CAMBIO_FORMA_PAGO",
                                                           "estado"          => "Activo"));
            if (is_object($objParametroCab))
            {
                $arrayParametroDet = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                               ->findBy(array("parametroId" => $objParametroCab,
                                                              "estado"      => "Activo"));
                if ($arrayParametroDet)
                {                                         
                    foreach ($arrayParametroDet as $objParametroDet)
                    {
                        if ($objParametroDet->getValor1() === "S")
                        {
                            $arrayMotivoFactura[] = $objParametroDet->getValor2(); 
                        }
                    }
                }
            }
        }
        catch (\Exception $ex) 
        {
            $arrayMotivoFactura[] = array();
        }        
        return $arrayMotivoFactura;
       
    }    
}
