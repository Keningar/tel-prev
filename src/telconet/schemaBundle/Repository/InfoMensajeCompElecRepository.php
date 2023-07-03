<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use telconet\schemaBundle\Entity\InfoMensajeCompElec;

class InfoMensajeCompElecRepository extends EntityRepository
{
    /**
     * getMensajesComprobantes, obtiene los cambios de estado y mensajes del SRI
     * @param  array     $arrayParametros  Recibe el id documento, start y limit
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 09-01-2014
     * @since 1.0
     * @return array     $arrayDatos       Retorna el array de mensajes y el count del total de mensajes existentes
     */
     public function getMensajesComprobantes($arrayParametros)
    {   
        $strSelect          = "SELECT imce.tipo, imce.mensaje, imce.informacionAdicional, imce.feCreacion ";
        $strCount           = "SELECT count(imce.id) intTotalMensajes ";
        $strQueryMensajes   = "FROM schemaBundle:InfoMensajeCompElec imce
                                           WHERE imce.documentoId = :intIdDocumento
                                           ORDER BY imce.feCreacion DESC";
        $strObtieneMensajes = $strSelect.$strQueryMensajes;
        $dqlObtieneMensajes = $this->_em->createQuery($strObtieneMensajes);
        $dqlObtieneMensajes->setParameter('intIdDocumento',$arrayParametros['intIdDocumento']);
        $dqlObtieneMensajes->setFirstResult($arrayParametros['intStart'])->setMaxResults($arrayParametros['intLimit']);
        $arrayDatos['arrayMensajes'] = $dqlObtieneMensajes->getResult();
        
        $strObtieneCountMensajes = $strCount.$strQueryMensajes;
        $dqlObtieneCountMensajes = $this->_em->createQuery($strObtieneCountMensajes);
        $dqlObtieneCountMensajes->setParameter('intIdDocumento',$arrayParametros['intIdDocumento']);
        $arrayDatos['intTotalMensajes'] = $dqlObtieneCountMensajes->getSingleResult();
        return $arrayDatos;
    }//getMensajesComprobantes
}

