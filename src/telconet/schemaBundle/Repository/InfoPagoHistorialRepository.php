<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Entity\InfoPagoHistorial;

class InfoPagoHistorialRepository extends EntityRepository
{
    /* Documentación para el método 'obtenerHistorialDePago'.
     *
     * Me devuelve el PRIMER historial del documento 
     *
     * @param mixed $idPago ID Pago a consultar.
     *
     * @return resultado Listado de documentos y total de documentos.
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 19-01-2015
     */

    public function obtenerHistorialDePago($idPago)
    {

        if($idPago > 0)
        {
            $query = $this->_em->createQuery();

            $dql = "SELECT 
                    iph.observacion,
					iph.estado 
                FROM 
                    schemaBundle:InfoPagoCab ipc,
					schemaBundle:InfoPagoHistorial iph
                WHERE 
                    ipc.id=iph.pagoId
                    and ipc.id= :idPago
                ORDER BY iph.id ASC ";

            $query->setParameter('idPago', $idPago);

            $datos = $query->setDQL($dql)->setFirstResult(0)->setMaxResults(1)->getOneOrNullResult();

            $resultado['registro'] = $datos;
        }

        return $resultado;
    }

}
