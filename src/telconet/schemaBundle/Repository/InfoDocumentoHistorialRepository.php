<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoDocumentoHistorialRepository extends EntityRepository
{
    
    /* El metodo getMaxIdHistorial busca el id del último historial del documento enviado como parámetro.
     * @param   Int  $intIdDocumento   Recibe el Id del documento
     * modificacion  Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0  01-10-2021
     */

    public function getMaxIdHistorial($intIdDocumento)
    {
        $objQuery = $this->_em->createQuery(
                    "SELECT MAX(idh.id) as id
                        FROM  schemaBundle:InfoDocumentoHistorial idh
                        WHERE idh.documentoId=:intIdDocumento");
        $objQuery->setParameter(':intIdDocumento', $intIdDocumento);
        $arrayReult = $objQuery->getResult();
        return $arrayReult;
    }    
}
