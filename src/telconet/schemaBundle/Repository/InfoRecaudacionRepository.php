<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoRecaudacionRepository extends EntityRepository
{
    
    public function findRecaudacionesPorCriterios($empresaCod, $estado, $fechaDesde, $fechaHasta,$limit,$page,$start)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->from('schemaBundle:InfoRecaudacion', 'ir');
        $qb->where('ir.empresaCod = :empresaCod')->setParameter('empresaCod', $empresaCod);
        $qb->orderBy('ir.feCreacion', 'DESC');
        if ($fechaDesde)
        {
            $fechaDesde = date("Y/m/d", strtotime($fechaDesde));
            $qb->andWhere('ir.feCreacion >= :fechaDesde')->setParameter('fechaDesde', $fechaDesde);
        }
        if ($fechaHasta)
        {
            $fechaHasta = date("Y/m/d", strtotime($fechaHasta));
            $qb->andWhere('ir.feCreacion <= :fechaHasta')->setParameter('fechaHasta', $fechaHasta);
        }
        if ($estado)
        {
            $qb->andWhere('ir.estado = :estado')->setParameter('estado', $estado);
        }
        $qb->select('count(ir)');
        $total = $qb->getQuery()->getSingleScalarResult();
        $qb->select('ir');
        $datos = $qb->getQuery()->setFirstResult($start)->setMaxResults($limit)->getResult();
        $resultado['registros'] = $datos;
        $resultado['total'] = $total;
        return $resultado;
    }
    
    public function countRecaudacionesProcesando()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(ir)');
        $qb->from('schemaBundle:InfoRecaudacion', 'ir');
        $qb->where('ir.estado = :estado')->setParameter('estado', 'Procesando');
        $total = $qb->getQuery()->getSingleScalarResult();
        return $total;
    }
    
    public function findSiguienteRecaudacionPendiente()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('ir');
        $qb->from('schemaBundle:InfoRecaudacion', 'ir');
        $qb->where('ir.estado = :estado')->setParameter('estado', 'Pendiente');
        $qb->orderBy('ir.feCreacion', 'ASC');
        $qb->setMaxResults(1);
        $datos = $qb->getQuery()->getOneOrNullResult();
        return $datos;
    }
    
    /**
     * Documentación para el método 'generarFormatoEnvioRecaudacion'.
     *
     * Ejecuta la generación y envío de reporte de facturación según los parámetros indicados.
     *
     * @param mixed $arrayParametros[
     *                               'strEmpresaCod'                => Código de empresa en sesión.
     *                               'emailUsrSesion'               => Email usuario en sesion.
     *                               'strNombreArchivoEnvio'        => Nombre de archivo con formato de envío a generar.
     *                               'intCanalRecaudacionId'        => Id del canal de recaudacion
     *                               ]
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 17-11-2017
     * 
     * Se recibe parámetro $objParametros para poder reutilizar la función insertError() en el repositorio.
     * @author Douglas Natha <dnatha@telconet.ec>
     * @version 1.1 07-11-2019
     *
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.2 10-05-2021 - Se realizan cambios por el consumo del nuevo NFS.
     * 
     * @since 1.0
     */
    public function generarFormatoEnvioRecaudacion($arrayParametros, $objParametros)
    {
        $serviceUtil          = $objParametros['serviceUtil'];
        $strEmpresaCod        = $arrayParametros['strEmpresaCod'];
        $strEmailUsrSesion    = $arrayParametros['strEmailUsrSesion'];
        $strUsrCreacion       = $arrayParametros['strUsrCreacion'];
        $strNombreArchivo     = $arrayParametros['strNombreArchivoEnvioTem'];
        $strCanalRecaudacion  = $arrayParametros['intCanalRecaudacionId'];
        $strPathNfs           = "";
        $strError             = "";
        
        $serviceUtil->insertError( 'Telcos+', 
                        'LOG DE EJECUCION DE PAGOS', 
                        'InfoRecaudacionRepository/generarFormatoEnvioRecaudacion - '.
                        'FNKG_RECAUDACIONES.P_GEN_FORMATO_ENV_REC con los sgtes parametros... '.
                        'Codigo de empresa: ' . $strEmpresaCod . ', Pv_CanalRecaudacion: ' . 
                        $strCanalRecaudacion . ', Pv_EmailUsrSesion: ' . $strEmailUsrSesion . 
                        ', Pv_UsuarioSession: ' . $Pv_UsuarioSession . 
                        ', Pv_NombreArchivo: ' . $strNombreArchivo ,
                        'telcos', 
                        '127.0.0.1' );

        
        $strSql = "BEGIN
                    DB_FINANCIERO.FNKG_RECAUDACIONES.P_GEN_FORMATO_ENV_REC
                    (
                        :Pv_EmpresaCod,                        
                        :Pv_CanalRecaudacion,
                        :Pv_EmailUsrSesion,
                        :Pv_UsuarioSession,
                        :Pv_NombreArchivo,
                        :Pv_PathNFS,
                        :Pv_Error
                    );
                   END;";

        try
        {
            $objStmt    = $this->_em->getConnection()->prepare($strSql);
            $strPathNfs = str_pad($strPathNfs, 5000, " ");
            $strError   = str_pad($strError, 5000, " ");
            $objStmt->bindParam('Pv_EmpresaCod', $strEmpresaCod);
            $objStmt->bindParam('Pv_CanalRecaudacion', $strCanalRecaudacion);
            $objStmt->bindParam('Pv_EmailUsrSesion', $strEmailUsrSesion);
            $objStmt->bindParam('Pv_UsuarioSession', $strUsrCreacion);
            $objStmt->bindParam('Pv_NombreArchivo', $strNombreArchivo);
            $objStmt->bindParam('Pv_PathNFS', $strPathNfs);
            $objStmt->bindParam('Pv_Error', $strError);

            $objStmt->execute();

            $serviceUtil->insertError( 'Telcos+', 
                        'LOG DE EJECUCION DE PAGOS', 
                        'InfoRecaudacionRepository/generarFormatoEnvioRecaudacion - DESPUES DE EJECUTAR: '.
                        'FNKG_RECAUDACIONES.P_GEN_FORMATO_ENV_REC con los sgtes parametros... '.
                        'Codigo de empresa: ' . $strEmpresaCod . ', Pv_CanalRecaudacion: ' . 
                        $strCanalRecaudacion . ', Pv_EmailUsrSesion: ' . $strEmailUsrSesion . 
                        ', Pv_UsuarioSession: ' . $Pv_UsuarioSession . 
                        ', Pv_NombreArchivo: ' . $strNombreArchivo ,
                        'telcos', 
                        '127.0.0.1' );
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        $arrayRespuesta      = array('strPathNfs' => $strPathNfs,
                                     'strError'   => $strError);

        return $arrayRespuesta; 
    }    
    
}
