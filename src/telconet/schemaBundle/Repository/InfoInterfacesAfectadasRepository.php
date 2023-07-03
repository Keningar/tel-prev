<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;

class InfoInterfacesAfectadasRepository extends EntityRepository
{
    /**
    * Documentación para la funcion 'cargaTmpInterfacesAfectadas'.
    * La cual carga la tabla temporal con las interfaces que son consultadas en el momento
    *
    * @param array $arrayParametros['strCadenaInterfaces' => Se envia un array con las interfaces que son consultadas en el momento
    *                                                        de agregar los afectados de forma masiva ]
    *
    * @return integer $intProcesoId
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 27-10-2016
    */
    public function cargaTmpInterfacesAfectadas($arrayParametros)
    {
        $intProcesoId = "";
        $intProcesoId = str_pad($intProcesoId, 20, " ");
        $strSql       = "BEGIN CMKG_TEMP_INTERFACES_AFECT.P_CARGA_TMP_INTERFACES_AFECT(:PA_INTERFACES,:PN_PROCESO_ID); END;";

        $objStmt = $this->_em->getConnection()->prepare($strSql);
        $objStmt->bindParam('PA_INTERFACES', $arrayParametros["strCadenaInterfaces"]);
        $objStmt->bindParam('PN_PROCESO_ID', $intProcesoId);
        $objStmt->execute();

        return $intProcesoId;
    }

    /**
    * Documentación para la funcion 'borraTmpInterfacesAfectadas'.
    * La cual borra los registros asociados al proceso_id enviado como parametro
    *
    * @param array $arrayParametros['intProcesoId' => Id del proceso temporal a eliminar ]
    *
    * @return string $strMensaje
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 27-10-2016
    */
    public function borraTmpInterfacesAfectadas($arrayParametros)
    {
        $strMensaje   = "";
        $strSql = "BEGIN CMKG_TEMP_INTERFACES_AFECT.P_BORRA_TMP_INTERFACES_AFECT(:PN_ID_PROCESO,:PV_MSG); END;";
        $objStmt = $this->_em->getConnection()->prepare($strSql);
        $objStmt->bindParam('PN_ID_PROCESO', $arrayParametros["intProcesoId"]);
        $objStmt->bindParam('PV_MSG', $strMensaje);
        $objStmt->execute();

        return $strMensaje;
    }
}