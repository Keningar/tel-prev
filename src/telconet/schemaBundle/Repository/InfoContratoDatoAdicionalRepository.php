<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;

class InfoContratoDatoAdicionalRepository extends EntityRepository
{
    /**
     * Documentación para el método 'getResultadoDatoAdicionalContrato'.
     *
     * Retorna Dato Adicional del Contrato del Cliente
     *
     * @param Array $arrayParametros['EMPRESA'] String: Código de la empresa
     *                              ['ESTADO']  String: Estado de la caracteristica
     *                              ['ID_PER']  Int   : IdPersonaEmpresaRol del Cliente
     * 
     * @return InfoContratoDatoAdicional Dato Adicional del Contrato del Cliente.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     */
    public function getResultadoDatoAdicionalContrato($arrayParametros)
    {
        $query = $this->_em->createQuery("  SELECT icd FROM 
                                                schemaBundle:InfoPersonaEmpresaRol iper, 
                                                schemaBundle:InfoEmpresaRol ier,
                                                schemaBundle:InfoContrato ic,
                                                schemaBundle:InfoContratoDatoAdicional icd
                                            WHERE 
                                                ic.personaEmpresaRolId =  iper.id 
                                            AND iper.empresaRolId      =  ier.id  
                                            AND icd.contratoId         =  ic.id
                                            AND iper.id                = :ID_PER
                                            AND ic.estado              = :ESTADO 
                                            AND ier.empresaCod         = :EMPRESA ");
        $query->setParameter('ID_PER',  $arrayParametros['ID_PER']);
        $query->setParameter('EMPRESA', $arrayParametros['EMPRESA']);
        $query->setParameter('ESTADO',  $arrayParametros['ESTADO']);
        
        return $query->getOneOrNullResult();
    }
}