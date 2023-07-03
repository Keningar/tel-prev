<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiGrupoPromocionReglaRepository extends EntityRepository
{
    /**
     * detenerProcesosPromocion()
     * Detener todos los procesos y reglas de la promocion.
     *
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 10-12-2021
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.1 31-03-2022 - Se cambia el procedimiento para evitar que se demore la respuesta de la ejecuion
     *                  y se caiga el telcos por base demorada
     * 
     * @param Array $arrayParametros["intIdPromocion" => Id de la promocion"]
     * 
     * @return $arrayResultado - Resultado de la ejecucion.
     */
    public function detenerProcesosPromocion($arrayParametros)
    {
        $intIdPromocion = $arrayParametros['intIdPromocion'];
        try
        {
            $strStatus  = null;
            $strStatus  = str_pad($strStatus, 3000, " ");
            $strMensaje = null;
            $strMensaje = str_pad($strMensaje, 3000, " ");
            $strSql = " BEGIN
                        DB_COMERCIAL.CMKG_PROMOCIONES_BW.P_ENVIA_DETENER_PROMOCION_BW(Pn_IdPromocion => :intIdPromocion,
                                                                                    Pv_Status      => :status,
                                                                                    Pv_Mensaje     => :mensaje);
                        END; ";
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindParam('intIdPromocion', $intIdPromocion);
            $objStmt->bindParam('status',         $strStatus);
            $objStmt->bindParam('mensaje',        $strMensaje);
            $objStmt->execute();
            $arrayResultado = array(
                'status'  => $strStatus,
                'mensaje' => $strMensaje
            );
        }
        catch (\Exception $e)
        {
            $arrayResultado = array(
                'status'  => 'ERROR',
                'mensaje' => $e->getMessage()
            );
        }
        return $arrayResultado;
    }

}
