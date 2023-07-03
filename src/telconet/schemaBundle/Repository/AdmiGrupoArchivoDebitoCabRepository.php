<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiGrupoArchivoDebitoCabRepository extends EntityRepository
{
    /**
     * Documentación para insertGrupoDet
     * 
     * Función que inserta un nuevo registro en AdmiGrupo
     * 
     * @param array $arrayParametros['intIdPersona'  => 'Id de la Persona'  
     * @return Retorna parametros devueltos por Procedure
     * @author Ivan Romero<icromero@telconet.ec>
     * @version 1.0 16-07-2021
     */
    public function insertGrupoDet($arrayParametros)
    {
            
        try
        {
            if( !empty($arrayParametros) ) 
            {
                $strGrupoDebitoId         = intval($arrayParametros["strGrupoDebitoId"]);
                $strBancoTipoCuentaId         = $arrayParametros["strBancoTipoCuentaId"];
                $strUser    = $arrayParametros["strUser"];
                $strEstado             = $arrayParametros["strEstado"];
                $strMsjError  = "";
                $strMsjError  = str_pad($strMsjError, 50, " ");          
                
                $strSql = "BEGIN DB_FINANCIERO.FNKG_INGRESO_BANCO_GRUPOS.P_INSERT_ADMI_GRUPO_DET (:Pv_GrupoDebitoId,".
                ":Pv_BancoTipoCuentaId,:Pv_UsrCreacion,:Pv_Estado,:Pv_Msn); END;";
                                                               
                $objStmt = $this->_em->getConnection()->prepare($strSql);
                $objStmt->bindParam('Pv_GrupoDebitoId', $strGrupoDebitoId);
                $objStmt->bindParam('Pv_BancoTipoCuentaId', $strBancoTipoCuentaId);
                $objStmt->bindParam('Pv_UsrCreacion', $strUser);
                $objStmt->bindParam('Pv_Estado', $strEstado);
                $objStmt->bindParam('Pv_Msn', $strMsjError);
                $objStmt->execute();
            }
        }
        catch(\Exception $ex)
        {
           throw($ex);
           
        }
        
        return array('strMsjError'     => $strMsjError);
    }

     /**
     * Documentación para insertGrupoDet
     * 
     * Función que inserta un nuevo registro en AdmiGrupoCab y AdmiGrupoDet
     * 
     * @param array $arrayParametros['intIdPersona'  => 'Id de la Persona'  
     * @return Retorna parametros devueltos por Procedure
     * @author Ivan Romero<icromero@telconet.ec>
     * @version 1.0 16-07-2021
     */
    public function insertGrupoCabDet($arrayParametros)
    {
            
        try
        {
            if( !empty($arrayParametros) ) 
            {
                $strNombreGrupo         = $arrayParametros["strNombreGrupo"];
                $strBancoTipoCuentaId         = $arrayParametros["strBancoTipoCuentaId"];
                $strUser    = $arrayParametros["strUser"];
                $strEstado             = $arrayParametros["strEstado"];
                $strEmpresa             = $arrayParametros["strEmpresa"];
                $strMsjError  = "";
                $strMsjError  = str_pad($strMsjError, 50, " ");          
                
                $strSql = "BEGIN DB_FINANCIERO.FNKG_INGRESO_BANCO_GRUPOS.P_INSERT_ADMI_GRUPO_CAB_DET (:Pv_BancoTipoCuentaId,".
                ":Pv_UsrCreacion,:Pv_NombreGrupo,:Pv_Estado,:Pv_Empresa,:Pv_Msn); END;";
                                                               
                $objStmt = $this->_em->getConnection()->prepare($strSql);
                $objStmt->bindParam('Pv_BancoTipoCuentaId', $strBancoTipoCuentaId);
                $objStmt->bindParam('Pv_NombreGrupo', $strNombreGrupo);
                $objStmt->bindParam('Pv_UsrCreacion', $strUser);
                $objStmt->bindParam('Pv_Estado', $strEstado);
                $objStmt->bindParam('Pv_Empresa', $strEmpresa);
                $objStmt->bindParam('Pv_Msn', $strMsjError);
                $objStmt->execute();
            }
        }
        catch(\Exception $ex)
        {
           throw($ex);
           
        }
        
        return array('strMsjError'     => $strMsjError);
    }
}