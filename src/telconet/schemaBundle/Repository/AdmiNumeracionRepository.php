<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiNumeracionRepository extends EntityRepository
{
    public function findByEmpresaYOficina($codEmpresa, $idOficina, $codigo)
    {
        $query = $this->_em->createQuery("SELECT a
        FROM 
                schemaBundle:AdmiNumeracion a
        WHERE 
                a.codigo = :codigo AND
                a.empresaId = :codEmpresa AND
                a.estado='Activo' AND
                a.oficinaId = :idOficina");
        $query->setParameter('codigo', $codigo);
        $query->setParameter('codEmpresa', $codEmpresa);
        $query->setParameter('idOficina', $idOficina);
        $datos = $query->getSingleResult();
        return $datos;
    }
    
    /**
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 13-10-2014
     * @since 1.0
     * @param type $intIdEmpresa    Recibe el Id de la empresa
     * @param type $strCodigo       Recibe el codigo de facturacion
     * @return type                 Retorna la entidad AdmiNumeracion
     */
    public function findOficinaMatrizYFacturacion($intIdEmpresa, $strCodigo){
        $query = $this->_em->createQuery("SELECT a
                                            FROM 
                                                    schemaBundle:AdmiNumeracion a,
                                                    schemaBundle:InfoOficinaGrupo iog
                                            WHERE 
                                                    a.codigo                    = :strCodigo AND
                                                    a.empresaId                 = :intIdEmpresa AND
                                                    a.estado                    = :strEstadoAn AND
                                                    a.oficinaId                 = iog.id AND
                                                    iog.estado                  = :strEstadoIOG AND
                                                    iog.esMatriz                = :strEsMatriz AND
                                                    iog.esOficinaFacturacion    = :strEsOficinaFacturacion");
        $query->setParameter('strCodigo', $strCodigo);
        $query->setParameter('intIdEmpresa', $intIdEmpresa);
        $query->setParameter('strEstadoAn', "Activo");
        $query->setParameter('strEstadoIOG', "Activo");
        $query->setParameter('strEsMatriz', "S");
        $query->setParameter('strEsOficinaFacturacion', "S");
        $objDatos = $query->getSingleResult();
        return $objDatos;
    }
    
/**
     * Documentación para la metodo 'findNumeracionXEmpresaYTipo'.
     *
     * Permite obtener las numeraciones activas por empresa y pot tipo
     * 
     * @param $empresa String Parametro de empresa a buscar
     * @param $tipo Tipo de Documentos Parametro de tipos de documentos, cuando el tipo enviado no es FACT
     * por defecto sera utilizado NC.
     * 
     * @return $datos Listado de numeraciones Activas habilitadas para la empresa
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 2.0 29-09-2014
     * @throws java.lang.Exception
     */
    public function findNumeracionXEmpresaYTipo($empresa,$tipo){
       
        $query = $this->_em->createQuery();
        $cuerpo="SELECT distinct a
                FROM 
                schemaBundle:AdmiNumeracion a                
                WHERE a.empresaId = :empresaId
                AND a.estado= :estado
                AND a.codigo in (:tipos)";
	    
        $query->setParameter('empresaId', $empresa);
        $query->setParameter('estado', "Activo");
        
	    if($tipo == 'FACT')
        {
            $tipos = array("FAC", "FACR", "FACE");
            $query->setParameter('tipos', $tipos);
        }
	    else
        {
            $tipos = array("NC");
            $query->setParameter('tipos', $tipos);
        }

        //Datos
        $query->setDQL($cuerpo);
        $datos = $query->getResult();
        
	    return $datos;        
    }	
    
    
    /**
     * Documentación para el método 'generarJson'.
     *
     * Genera el json con las numeraciones encontrada en base a los parámetros enviados por los usuarios
     * 
     * @param array $arrayParametros ['nombre', 'estado', 'inicio', 'limite', 'empresa', 'oficina', 'emGeneral']
     * 
     * @return Json $resultado 
     * 
     * @version 1.0 Version Inicial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 29-12-2015 - Se modifica para enviar como parámetros los datos para la respectiva consulta, y se añade una columna mas a la
     *                           consulta que indica para que tipo de comprobante financiero se usará la numeración
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 27-06-2017 - Se modifica el query para que retorne el numero de autorización de la numeración ingresada
     */
    public function generarJson($arrayParametros)
    {
        $arrayEncontrados = array();
        $arrayRegistros   = $this->getRegistros($arrayParametros);
        $jsonResultado    = null;
 
        if( $arrayRegistros['resultados'] )
        {
            $intTotal = $arrayRegistros['total'];
            
            foreach ($arrayRegistros['resultados'] as $data)
            {
                $strTipoComprobante  = '';
                $objAdmiParametroDet = $arrayParametros['emGeneral']->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->findOneBy( array( 'descripcion' => 'TIPO_COMPROBANTES',
                                                                                        'estado'      => 'Activo',
                                                                                        'valor2'      => trim($data->getCodigo()) ) );

                if( $objAdmiParametroDet )
                {
                    $strTipoComprobante = $objAdmiParametroDet->getValor1();
                }
        
                $arrayEncontrados[] = array('id_numeracion'         => $data->getId(),
                                            'descripcion'           => trim($data->getDescripcion()),
                                            'codigo'                => trim($data->getCodigo()),
                                            'tipoComprobante'       => ucwords(strtolower($strTipoComprobante)),
                                            'numeracion_uno'        => trim($data->getNumeracionUno()),
                                            'numeracion_uno'        => trim($data->getNumeracionUno()),
                                            'strNumeroAutorizacion' => trim($data->getNumeroAutorizacion()),
                                            'secuencia'             => trim($data->getSecuencia()),
                                            'tabla'                 => trim($data->getTabla()),
                                            'estado'                => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') 
                                                                       ? 'Eliminado':'Activo'),
                                            'action1'               => 'button-grid-show',
                                            'action2'               => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') 
                                                                       ? 'icon-invisible':'button-grid-edit'),
                                            'action3'               => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') 
                                                                       ? 'icon-invisible':'button-grid-delete'));
            }

            if($intTotal == 0)
            {
                $arrayResultados = array('total' => 1 ,
                                         'encontrados' => array('id_numeracion'     => 0 , 
                                                                'descripcion'       => 'Ninguno', 
                                                                'codigo'            => 'Ninguno', 
                                                                'tipoComprobante'   => 'Ninguno', 
                                                                'tabla'             => 'Ninguno', 
                                                                'numeracion_uno'    => 0 , 
                                                                'numeracion_dos'    => 0 , 
                                                                'secuencia'         => 0 ,
                                                                'numeracion_id'     => 0 , 
                                                                'numeracion_nombre' => 'Ninguno', 
                                                                'estado'            => 'Ninguno') );
                
                $jsonResultado = json_encode( $arrayResultados);
            }
            else
            {
                $jsonDataFinal = json_encode($arrayEncontrados);
                $jsonResultado = '{"total":"'.$intTotal.'","encontrados":'.$jsonDataFinal.'}';
            }
        }
        else
        {
            $jsonResultado = '{"total":"0","encontrados":[]}';
        }//( $arrayRegistros['resultados'] )
            
        return $jsonResultado;
    }
    
    
    /**
     * Documentación para el método 'getRegistros'.
     *
     * Obtiene los registros de la numeraciones desde la base de acuerdo a los parámetros enviados por los usuarios
     * 
     * @param array $arrayParametros ['nombre', 'estado', 'inicio', 'limite', 'empresa', 'oficina']
     * 
     * @return array $arrayResultado ['resultados', 'total']
     * 
     * @version 1.0 Version Inicial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 29-12-2015 - Se modifica para enviar como parámetros los datos para la respectiva consulta
     */
    public function getRegistros($arrayParametros)
    {
        $arrayResultado = array();
        
        $qb      = $this->_em->createQueryBuilder();
        $qbCount = $this->_em->createQueryBuilder();
        
        $qb->select('sim')->add('from', 'schemaBundle:AdmiNumeracion sim, schemaBundle:InfoOficinaGrupo iog');
        $qbCount->select('COUNT(sim.id)')->add('from', 'schemaBundle:AdmiNumeracion sim, schemaBundle:InfoOficinaGrupo iog');
        
        $qb->where("iog.id = sim.oficinaId");
        $qb->andWhere("iog.esOficinaFacturacion = ?1");
        $qb->setParameter(1, 'S');
        
        $qbCount->where("iog.id = sim.oficinaId");
        $qbCount->andWhere("iog.esOficinaFacturacion = ?1");
        $qbCount->setParameter(1, 'S');

        
        if( $arrayParametros['estado'] != "Todos" )
        {
            if( $arrayParametros['estado'] == "Activo" )
            {
                $qb->andWhere("sim.estado not like ?2");
                $qb->setParameter(2, 'Eliminado');
                
                $qbCount->andWhere("sim.estado not like ?2");
                $qbCount->setParameter(2, 'Eliminado');
            }
            else
            {
                $qb->andWhere('sim.estado = ?2');
                $qb->setParameter(2, $arrayParametros['estado']);
                
                $qbCount ->andWhere('sim.estado = ?2');
                $qbCount->setParameter(2, $arrayParametros['estado']);
            }
        }
        
        
        if( $arrayParametros['nombre'] != "" )
        {
            $qb->andWhere( 'sim.descripcion like ?3' );
            $qb->setParameter(3, '%'.$arrayParametros['nombre'].'%');
            
            $qbCount->andWhere( 'sim.descripcion like ?3' );
            $qbCount->setParameter(3, '%'.$arrayParametros['nombre'].'%');
        }
        
        
        if( $arrayParametros['empresa'] )
        {
            $qb->andWhere( 'sim.empresaId = ?4' );
            $qb->setParameter(4, $arrayParametros['empresa']);
            
            $qbCount->andWhere( 'sim.empresaId = ?4' );
            $qbCount->setParameter(4, $arrayParametros['empresa']);
        }
        
        
        if( $arrayParametros['oficina'] )
        {
            $qb->andWhere( 'sim.oficinaId = ?5' );
            $qb->setParameter(5, ''.$arrayParametros['oficina']);
            
            $qbCount->andWhere( 'sim.oficinaId = ?5' );
            $qbCount->setParameter(5, ''.$arrayParametros['oficina']);
        }
        
        $qb->orderBy('sim.feCreacion', 'DESC');
        
        
        if( $arrayParametros['inicio'] != '' )
        {
            $qb->setFirstResult($arrayParametros['inicio']); 
        }
        
        if( $arrayParametros['limite'] != '' )
        {
            $qb->setMaxResults($arrayParametros['limite']);
        }
        
        $query      = $qb->getQuery();
        $queryCount = $qbCount->getQuery();
        
        $arrayResultado['resultados'] = $query->getResult();
        $arrayResultado['total']      = $queryCount->getSingleScalarResult();
        
        return $arrayResultado;
    }
    
    
    /**
     * Documentación para funcion 'findFactNumeracionXEmpresaXNumeracionUnoXNumeracionDos'.
     * busca numeracion por empresaCod, numeroUno de factura y numeroDos de factura
     * @param string $empresaCod
     * @param string $numeroUno
     * @param string $numeroDos
     * @return array $datos numeracion encontrada
     */      
    public function findFactNumeracionXEmpresaXNumeracionUnoXNumeracionDos($empresaCod, $numeroUno, $numeroDos)
    {
        $query = $this->_em->createQuery("SELECT a
            FROM 
            schemaBundle:AdmiNumeracion a                
            WHERE 
            a.empresaId = :empresaCod AND
            a.codigo in(:codigos) AND
            a.numeracionUno=:numeroUno AND
            a.numeracionDos=:numeroDos    
            ");
        $codigos=array('FAC','FACR','FACE');
        $query->setParameter('empresaCod',$empresaCod);
        $query->setParameter('codigos',$codigos);
        $query->setParameter('numeroUno',$numeroUno);
        $query->setParameter('numeroDos',$numeroDos);
        $datos = $query->getResult();
        return $datos;
    }
    
    
    /**
     * Documentación para funcion 'getNumeracionContratoVehiculo'.
     * busca numeracion por empresaCod, numeroUno de factura y numeroDos de factura
     * @param string $intIdEmpresa
     * @param string $intIdOficina
     * @param string $strCodigo
     * @return array $objResult numeración encontrada
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec> 
     * @version 1.1 10-08-2016 - Se corrige para que el parámetro de entrado sea el id de la oficina
     */  
    public function getNumeracionContratoVehiculo($intIdEmpresa, $intIdOficina,$strCodigo)
    {
        $objResultado = "";
        try
        {
            $query  = $this->_em->createQuery();
            
            $sql    = "SELECT a
                                            FROM 
                                                    schemaBundle:AdmiNumeracion a,
                                                    schemaBundle:InfoOficinaGrupo iog
                                            WHERE 
                                                    a.codigo                    = :strCodigo AND
                                                    a.empresaId                 = :intIdEmpresa AND
                                                    a.estado                    = :estado AND
                                                    a.oficinaId                 = iog.id AND
                                                    a.oficinaId                 = :intIdOficina";
            
            $query->setParameter('strCodigo', $strCodigo);
            $query->setParameter('intIdEmpresa', $intIdEmpresa);
            $query->setParameter('intIdOficina', $intIdOficina);
            $query->setParameter('estado', 'Activo');
            $query->setDQL($sql);
            $objResultado = $query->getSingleResult();
        } 
        catch (Exception $ex) {
            error_log($ex->getMessage());
        }
        return $objResultado;
    }

}
