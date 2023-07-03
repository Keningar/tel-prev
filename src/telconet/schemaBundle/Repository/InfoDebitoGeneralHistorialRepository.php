<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use telconet\schemaBundle\Entity\InfoDebitoGeneral;
use telconet\schemaBundle\Entity\InfoDebitoGeneralHistorial;

class InfoDebitoGeneralHistorialRepository extends EntityRepository
{

    
    /**
    * Documentación para el método 'contabilizarDebitosProcesoManual'.
    * Este metodo contabiliza los debitos que son procesados en forma manual
    * @param  String $stringEmpresaCod   id de la empresa
    * @param  Integer $intDebitoGeneralHistorialId   id del historial del debito general 
    * @return Integer $out_Resultado Retorna un ok si la actualizacion fue correcta y un Error en caso contrario.
    *
    * @author Andres Montero <amontero@telconet.ec>
    * @version 1.0 13-04-2016
    *
    * Se recibe parámetro $objParametros para poder reutilizar la función insertError() en el repositorio
    * y se cambia de nombre la variable $stringEmpresaCod por $strEmpresaCod por problemas con SONAR.
    * @author Douglas Natha <dnatha@telconet.ec>
    * @version 1.1 07-11-2019
    * @since 1.0
    */
    public function contabilizarDebitosProcesoManual($strEmpresaCod,$intDebitoGeneralHistorialId, $objParametros)
    {
            $serviceUtil   = $objParametros['serviceUtil'];
            $out_msn_Error = null;
            $out_msn_Error = str_pad($out_msn_Error, 1000, " ");
            $out_Resultado = '[Proceso contable OK]';
            $tipo = 'MANUAL';

            $serviceUtil->insertError( 'Telcos+', 
                        'LOG DE EJECUCION DE PAGOS', 
                        'InfoDebitoGeneralHistorialRepository/contabilizarDebitosProcesoManual -  ' .
                        'FNKG_CONTABILIZAR_DEBITOS.PROCESAR_DEBITOS '.
                        'con los sgtes parametros... Codigo de empresa: ' . $strEmpresaCod .
                        ', debitoGeneralHistorialId: '. $intDebitoGeneralHistorialId . 
                        ', tipo: ' . $tipo . ', msnError: ' . $out_msn_Error ,
                        'telcos', 
                        '127.0.0.1' );

            //llama al metodo que verifica si el pago o anticipo se puede anular, caso contrario Devuelve como  mensaje "Error"
            $sql = "BEGIN FNKG_CONTABILIZAR_DEBITOS.PROCESAR_DEBITOS(:empresaCod, :debitoGeneralHistorialId, :tipo, :msnError); END;";
            $stmt = $this->_em->getConnection()->prepare($sql);
            $stmt->bindParam('empresaCod', $strEmpresaCod);
            $stmt->bindParam('debitoGeneralHistorialId', $intDebitoGeneralHistorialId);
            $stmt->bindParam('tipo', $tipo);
            $stmt->bindParam('msnError', $out_msn_Error);
            $stmt->execute();

            $serviceUtil->insertError( 'Telcos+', 
                        'LOG DE EJECUCION DE PAGOS', 
                        'InfoDebitoGeneralHistorialRepository/contabilizarDebitosProcesoManual - DESPUES DE EJECUTAR: ' .
                        'FNKG_CONTABILIZAR_DEBITOS.PROCESAR_DEBITOS '.
                        'con los sgtes parametros... Codigo de empresa: ' . $strEmpresaCod .
                        ', debitoGeneralHistorialId: '. $intDebitoGeneralHistorialId . 
                        ', tipo: ' . $tipo . ', msnError: ' . $out_msn_Error ,
                        'telcos', 
                        '127.0.0.1' ); 

            if(strtoupper($out_msn_Error)!='PROCESO OK')
            {
                $out_Resultado = '[Error al intentar crear asientos contables]';
            }
        return $out_Resultado;
    }     
    
    
}
