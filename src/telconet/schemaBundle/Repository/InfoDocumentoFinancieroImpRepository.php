<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoDocumentoFinancieroImpRepository extends EntityRepository
{
    /* El metodo findValorIvaPorFactura busca el valor del impuesto por factura
     * @param Int    $intIdFactura   Recibe el Id del documento
     * @param String $strEstado      Recibe el estado 
     * modificacion  Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0  22-04-2014
     */

    public function findValorIvaPorFactura($intIdFactura, $strEstado)
    {
        $query = $this->_em->createQuery(
            "SELECT sum(facImp.valorImpuesto) as iva
				FROM schemaBundle:InfoDocumentoFinancieroCab facCab,
				schemaBundle:InfoDocumentoFinancieroDet facDet, schemaBundle:InfoDocumentoFinancieroImp facImp,
				schemaBundle:AdmiImpuesto imp
				WHERE 
				facCab.id=facDet.documentoId AND
				facCab.estadoImpresionFact=:strEstado AND
				facDet.id=facImp.detalleDocId AND
				facImp.impuestoId=imp.id AND
				imp.codigoSri='2' AND
				facCab.id=:intIdFactura");
        $query->setParameter(':strEstado', $strEstado);
        $query->setParameter(':intIdFactura', $intIdFactura);
        $datos = $query->getResult();
        return $datos;
    }

    /* El metodo findValorBaseIvaPorFactura busca la base imponible por factura
     * @param Int    $intIdFactura   Recibe el Id del documento
     * @param String $strEstado      Recibe el estado 
     * modificacion  Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0  22-04-2014
     */

    public function findValorBaseIvaPorFactura($intIdFactura, $strEstado)
    {
        $query = $this->_em->createQuery(
            "SELECT sum(facDet.valorFacproDetalle) as baseIva
				FROM schemaBundle:InfoDocumentoFinancieroCab facCab,
				schemaBundle:InfoDocumentoFinancieroDet facDet, 
				schemaBundle:InfoDocumentoFinancieroImp facImp,
				schemaBundle:AdmiImpuesto imp
				WHERE 
				facCab.id=facDet.documentoId AND				
				facCab.estadoImpresionFact= :strEstado AND
				facDet.id=facImp.detalleDocId AND
				facImp.impuestoId=imp.id AND
				imp.codigoSri='2' AND
				facCab.id=:intIdFactura
				");
        $query->setParameter(':strEstado', $strEstado);
        $query->setParameter(':intIdFactura', $intIdFactura);
        $datos = $query->getResult();
        return $datos;
    }
     /* El metodo findByDetalleDocIdPrioridad busca los impuestos aplicados a un detalle especifico de la Factura
     * @param Int    $intIdDetalleFactura   Recibe el Id del detalle del documento     
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0  07-09-2018
     */
    public function findByDetalleDocIdPrioridad($intIdDetalleFactura)
    {
        $strQuery = $this->_em->createQuery(
            "SELECT facImp
				FROM 
				schemaBundle:InfoDocumentoFinancieroImp facImp,
				schemaBundle:AdmiImpuesto imp
				WHERE 				
				facImp.impuestoId=imp.id 
                AND  facImp.detalleDocId =:intIdDetalleFactura
                order by imp.prioridad asc
				");        
        $strQuery->setParameter('intIdDetalleFactura', $intIdDetalleFactura);
        $objDatos = $strQuery->getResult();
        return $objDatos;
    }
}
