<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class InfoDocumentoCompRepository extends EntityRepository
{
   /**
     * Documentación para el método 'getDocumentoFinan'.
     * Obtiene el ID_DOCUMENTO a partir del DOCUMENTO_ID_FINAN 
     *
     * @param  Integer $intIdDocumento Recibe el ID del Documento
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 02-10-2014
     */
    public function getDocumentoFinan($intIdDocumento)
    {
        $objSqlObtieneIdDocumento = $this->_em->createQuery("
                                           SELECT idc.id
                                           FROM schemaBundle:InfoDocumentoComp idc
                                           WHERE idc.documentoIfFinan = :intIdDocumento
                                         ");
        $objSqlObtieneIdDocumento->setParameter('intIdDocumento', $intIdDocumento);
        $arrayDatos = $objSqlObtieneIdDocumento->getResult();
        return $arrayDatos;
    }
}
