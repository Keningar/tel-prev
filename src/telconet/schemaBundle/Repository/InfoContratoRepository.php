<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;
use telconet\schemaBundle\DependencyInjection\BaseRepository;

class InfoContratoRepository extends BaseRepository
{
    public function find30ContratosPorEmpresaPorEstado($estado,$idEmpresa,$idper){	
		$subqueryEstado=""; 
		if($idper!="")
			$subquery=" AND iper.id=".$idper;
		else
			$subquery="";
			
                if ($estado){
                    $subqueryEstado=$subqueryEstado." ic.estado = '$estado' AND ";
                }
                
                
		$query = $this->_em->createQuery("SELECT ic
			FROM 
					schemaBundle:InfoPersonaEmpresaRol iper, 
					schemaBundle:InfoEmpresaRol ier,schemaBundle:InfoContrato ic
			WHERE 
					ic.personaEmpresaRolId=iper.id AND
					iper.empresaRolId=ier.id AND
                                        $subqueryEstado
					ier.empresaCod='".$idEmpresa."'".$subquery." ORDER BY ic.feCreacion desc")->setMaxResults(30);
		$datos = $query->getResult();
                //echo $query->getSQL();die;
		return $datos;
	}
        
	public function findContratosPorEmpresaPorEstado($estado,$idEmpresa){	
                $query = $this->_em->createQuery("SELECT ic
		FROM 
                schemaBundle:InfoPersonaEmpresaRol iper, 
                schemaBundle:InfoEmpresaRol ier, schemaBundle:InfoContrato ic
		WHERE 
                ic.personaEmpresaRolId=iper.id AND
                iper.empresaRolId=ier.id AND
                ic.estado = '".$estado."' AND
                ier.empresaCod='".$idEmpresa."'");
		$datos = $query->getResult();
		return $datos;
	} 
        
    
    /**
     * Documentación para el método 'findContratosPorCriteriosConServFact'.
     * 
     * Método que obtienes los contratos nuevos y pendientes de aprobación
     * 
     * @param Array $arrayParams['estado']     String  Estado del contrato.
     *                          ['idEmpresa']  String  Código empresa.
     *                          ['fechaDesde'] String  Fecha mínima de ingreso del contrato.
     *                          ['fechaHasta'] String  Fecha máxima de ingreso del contrato.
     *                          ['idper']      Integer IdPersonaEmpresaRol
     *                          ['oficinaId']  Integer Id de la oficina.
     *                          ['nombre']     String  Nombre del cliente.
     *                          ['intLimit']   Integer Indice final de la paginación
     *                          ['page']       Integer Número de página a obtener
     *                          ['intStart']   Integer Indice incial de la paginación
     *                          ['origen']     String  Origen del contrato: WEB o MOVIL.
     *                          ['documento']  String  Situación de recepción de documentos del contrato (Pendientes/Entregados)
     * 
     * @return $arrayResultado['total']     Integer Cantitad de registros obtenidos.
    *                         ['registros'] Array   Listado de Contratos.
    *                         ['error']     String  Mensaje de error.
     * 
     * CostoQuery: Contratos Físicos = 17, Contratos digitales Pendientes = 22, Contratos Digitales Entregados = 16
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.1 15-02-2016
     * @since 1.0
     * Se condensan los parámetros en el arreglo $arrayParams.
     * Se modifica la lógica de consulta, de createQuery a createNativeQuery, se mantiene el resultado del listado: Array de InfoContrato.
     * Se agrega el parámetro "$strOrigen"    que define el origen del contrato físico(WEB) o digital(MOVIL).
     * Se agrega el parámetro "$strDocumento" indica los contratos con documentos pendiente de recepción o entregados en su totalidad
     */
    public function findContratosPorCriteriosConServFact($arrayParams)
    {
        $strEstado     = $arrayParams['estado'];
        $strCodEmpresa = $arrayParams['idEmpresa'];
        $objFechaDesde = $arrayParams['fechaDesde'];
        $objFechaHasta = $arrayParams['fechaHasta'];
        $intIdPer      = $arrayParams['idper'];
        $intIdOficina  = $arrayParams['idOficina'];
        $strNombre     = $arrayParams['nombre'];
        $intLimit     = $arrayParams['limit'];
        $intStart     = $arrayParams['start'];
        $strOrigen    = isset($arrayParams['origen'])    ? $arrayParams['origen']    : 'WEB';
        $strDocumento = isset($arrayParams['documento']) ? $arrayParams['documento'] : null;
        
        $objRsmBuilderData  = new ResultSetMappingBuilder($this->_em);
        $objRsmBuilderCount = new ResultSetMappingBuilder($this->_em);
        
        // Convierte resultado nativeQuery a Doctrine
        $objRsmBuilderData->addRootEntityFromClassMetadata('telconet\schemaBundle\Entity\InfoContrato', 'IC');
        
        $objNtvQueryData = $this->_em->createNativeQuery(null, $objRsmBuilderData);
        $objNtvQueryCount   = $this->_em->createNativeQuery(null, $objRsmBuilderCount);
        
        $strQueryIni      = '';
        $strQueryFin      = '';
        $strQEntregadoCab = '';
        $strQEntregadoFin = '';

        if($strOrigen !== 'WEB')
        {
            if($strDocumento && $strDocumento == 'E')
            {
                $strQEntregadoCab = " , PENDIENTES AS ( ";
                // Los Contratos Digitales con documentos totalmente ENTREGADOS son la exclusión de los que tienen documentos PENDIENTES
                $strQEntregadoFin = " ) SELECT DISTINCT IC.* 
                                        FROM CONTRATOS_DIGITALES  IC
                                        WHERE IC.ID_CONTRATO NOT IN (SELECT P.ID_CONTRATO FROM PENDIENTES P)";
            }
            $strQueryIni = "WITH CONTRATOS_DIGITALES AS (" ;
            // Query que obtiene los contratos digitales con documentos PENDIENTES de entrega.
            $strQueryFin = ")$strQEntregadoCab
                            SELECT DISTINCT IC.* FROM INFO_CONTRATO IC
                            INNER JOIN CONTRATOS_DIGITALES CD ON CD.ID_CONTRATO = IC.ID_CONTRATO
                            INNER JOIN INFO_CONTRATO_CARACTERISTICA ICC ON ICC.CONTRATO_ID = IC.ID_CONTRATO
                            WHERE ICC.VALOR2 = 'N'
                            UNION
                            SELECT DISTINCT IC.* FROM INFO_CONTRATO IC
                            INNER JOIN CONTRATOS_DIGITALES CD ON CD.ID_CONTRATO = IC.ID_CONTRATO
                            LEFT JOIN INFO_CONTRATO_CARACTERISTICA ICC ON ICC.CONTRATO_ID = IC.ID_CONTRATO
                            WHERE ICC.VALOR2 IS NULL
                            ";
        }
        // Query principal de obtención de contratos físicos o digitales.
        $strQuery = "   SELECT DISTINCT IC.*
                        FROM       INFO_PERSONA_EMPRESA_ROL IPER 
                        INNER JOIN INFO_PERSONA             PERS ON PERS.ID_PERSONA           = IPER.PERSONA_ID
                        INNER JOIN INFO_EMPRESA_ROL         IER  ON IER.ID_EMPRESA_ROL        = IPER.EMPRESA_ROL_ID
                        INNER JOIN INFO_CONTRATO            IC   ON IC.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL 
                        INNER JOIN INFO_PUNTO               P    ON P.PERSONA_EMPRESA_ROL_ID  = IPER.ID_PERSONA_ROL
                        INNER JOIN INFO_SERVICIO            S    ON S.PUNTO_ID                = P.ID_PUNTO 
                        WHERE
                        " . ($intIdOficina       ? " IPER.OFICINA_ID             = :idOficina  AND " : "")
                          . ($intIdPer                 ? " IPER.ID_PERSONA_ROL         = :idper      AND " : "")
                          . (is_object($objFechaDesde) ? " IC.FE_CREACION             >= :fechaDesde AND " : "")
                          . (is_object($objFechaHasta) ? " IC.FE_CREACION             <= :fechaHasta AND " : "")
                          . ($strNombre                ? " PERS.IDENTIFICACION_CLIENTE = :nombre     AND " : "") 
                          . ($strOrigen == 'WEB'       ? " ic.estado  =  :estado  AND s.estado = :estadoServicio AND "
                                                 : " ic.estado in (:estados) AND ") .
                      " IC.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL
                        AND IER.EMPRESA_COD             = :idEmpresa
                        AND IC.ORIGEN                   = :origenContrato
                        ORDER BY IC.FE_CREACION DESC ";

        $strSQLNativo    = $strQueryIni . $strQuery . $strQueryFin . $strQEntregadoFin;
        $arrayEstados    = array('Activo', 'Pendiente');
        $arrayParametros = array('estado'         => $strEstado, 
                                 'estados'        => $arrayEstados, 
                                 'idEmpresa'      => $strCodEmpresa, 
                                 'estadoServicio' => 'Factible', 
                                 'origenContrato' => $strOrigen);
        if($intIdOficina)
        {
            $arrayParametros['idOficina'] = $intIdOficina;
        }

        if($intIdPer)
        {
            $arrayParametros['idper'] = $intIdPer;
        }

        if(is_object($objFechaDesde))
        {
            $arrayParametros['fechaDesde'] = $objFechaDesde;
        }
        if(is_object($objFechaHasta))
        {
            $arrayParametros['fechaHasta'] = $objFechaHasta;
        }
        if($strNombre)
        {
            $arrayParametros['nombre'] = trim($strNombre);
        }

        $objNtvQueryData->setParameters($arrayParametros);
        $objNtvQueryCount->setParameters($arrayParametros);

        $objRsmBuilderCount->addScalarResult('TOTAL', 'total', 'integer');

        $arrayResultado['total']     = $objNtvQueryCount->setSQL("SELECT COUNT(*) AS TOTAL FROM($strSQLNativo)")->getSingleScalarResult();
        $arrayResultado['registros'] = null;

        if(intval($arrayResultado['total']) > 0)
        {
            $objNtvQueryData->setSQL($strSQLNativo);
            $arrayResultado['registros'] = $this->setQueryLimit($objNtvQueryData, $intLimit, $intStart)->getResult();
        }
        
        return $arrayResultado;
    }
    
    /**
     * Documentación para el método 'findContratosPorCriterios'.
     * Obtiene informacion de los contratos  segun criterios de busqueda 
     * @param array     $arrayParams    
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.1 24-03-2016   
     * 
     * Se agrega campo ORIGEN con el fin de identificar si un contrato
     * fue creado via WEB o MOVIL
     * 
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.2 28-08-2016   
     * 
     * @return array $arrayDatos      
     */       
        public function findContratosPorCriterios($arrayParams)
            {                
                $strSqlConsulta = ' SELECT ic
                                    FROM 
                                            schemaBundle:InfoPersonaEmpresaRol iper , 
                                            schemaBundle:InfoEmpresaRol ier, schemaBundle:InfoContrato ic
                                    WHERE   ' . ($arrayParams['idOficina']   ? ' iper.oficinaId  =:idOficina   AND ' : '') .
                                          ' ' . ($arrayParams['fechaDesde']  ? ' ic.feCreacion  >=:fechaDesde  AND ' : '') .
                                          ' ' . ($arrayParams['fechaHasta']  ? ' ic.feCreacion  <=:fechaHasta  AND ' : '') .
                                          '  ic.personaEmpresaRolId=iper.id AND   iper.empresaRolId=ier.id  AND'.
                                          ' ' . ($arrayParams['idFormaPago'] ? ' ic.formaPagoId  =:idFormaPago AND ' : '') . 
                                          ' ' . ($arrayParams['estado']      ? ' ic.estado =:estado  AND ' : '') .
                                          ' ' . ($arrayParams['origen']      ? ' ic.origen =:origen  AND ' : '') .
                                          ' ' . ($arrayParams['numContrato'] ? ' ic.numeroContrato =:numContrato  AND ' : '') .
                                          ' ' . ($arrayParams['idEmpresa']   ? ' ier.empresaCod=:idEmpresa ' : '') .
                                          ' ' . ($arrayParams['idper']       ? ' AND iper.id=:idper  ' : '') .                     
                                          ' ORDER BY ic.feCreacion desc';
            
                $queryDatos = $this->_em->createQuery()->setMaxResults(100);

                if ($arrayParams['idOficina'])
                {
                    $queryDatos->setParameter('idOficina', $arrayParams['idOficina']);
                } 
                if ($arrayParams['idper'])
                {
                    $queryDatos->setParameter('idper', $arrayParams['idper']);
                }                
                if (isset($arrayParams['fechaDesde']))
                {
                    $queryDatos->setParameter('fechaDesde', $arrayParams['fechaDesde']);
                }                        
                if (isset($arrayParams['fechaHasta']))
                {
                    $queryDatos->setParameter('fechaHasta', $arrayParams['fechaHasta']);
                }                       
                if ($arrayParams['idFormaPago'])
                {
                    $queryDatos->setParameter('idFormaPago', $arrayParams['idFormaPago']);
                }
                if ($arrayParams['estado'])
                {
                    $queryDatos->setParameter('estado', trim($arrayParams['estado']));
                }
                if ($arrayParams['origen'])
                {
                    $queryDatos->setParameter('origen', trim($arrayParams['origen']));
                }
                if ($arrayParams['numContrato']!='')
                {
                    $queryDatos->setParameter('numContrato', trim($arrayParams['numContrato']));
                }                
                if (isset($arrayParams['idEmpresa']))
                {
                    $queryDatos->setParameter('idEmpresa', $arrayParams['idEmpresa']);
                }                
                $queryDatos->setDQL($strSqlConsulta);
           
                $arrayDatos = $queryDatos->getResult();

                return $arrayDatos;
            }
	
    
     /**
     * Documentación para el método 'findContratosPorCriterioContrato'.
     * Obtiene informacion del número de contrato para evitar duplicado.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 25-06-2019
     *
     * @return array $arrayDatos
     */
    public function findContratosPorCriterioContrato($arrayParams)
    {
                $strSqlConsulta = ' SELECT ic
                                    FROM   schemaBundle:InfoContrato ic
                                    WHERE   ' . ($arrayParams['numContrato']   ? ' ic.numeroContrato =:numContrato  ' : '') .
                                            ' ORDER BY ic.feCreacion desc';
            
                $objQueryDatos = $this->_em->createQuery()->setMaxResults(100);
                
                if(isset($arrayParams['numContrato']) && !empty($arrayParams['numContrato']))
                {
                    $objQueryDatos->setParameter('numContrato', trim($arrayParams['numContrato']));
                }

                $objQueryDatos->setDQL($strSqlConsulta);
           
                $arrayDatos = $objQueryDatos->getResult();

                return $arrayDatos;
    }
    
    /**
     * Documentación para el método 'findContratosPorEmpresaPorEstadoPorPersona'.
     *
     * Obtiene el contrato del cliente.
     * 
     * @param String $estado    Descripción del estado.
     * @param String $idEmpresa Código de la empresa.
     * @param int    $idPersona Id de la InfoPersona.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>       
     * @version 1.1 18-08-2016
     * @since 1.0
     * Se agrega enlace de variables y filtro de estado IN ('Activo', 'Pendiente', 'Pend-convertir') de la persona_rol.
     */
    public function findContratosPorEmpresaPorEstadoPorPersona($estado, $idEmpresa, $idPersona)
    {
        // Solo el Cliente con estados 'Activo', 'Pendiente' o 'Pend-convertir' puede cambiar de Razón Social.
        $arrayEstados = array('Activo', 'Pendiente', 'Pend-convertir');
        try
        {
            $objQuery = $this->_em->createQuery();

            $strDQL   = "SELECT ic
                         FROM 
                                 schemaBundle:InfoPersona           ip,
                                 schemaBundle:InfoPersonaEmpresaRol iper, 
                                 schemaBundle:InfoEmpresaRol        ier, 
                                 schemaBundle:InfoContrato          ic
                         WHERE 
                                 ip.id                  = :PERSONA          AND
                                 ic.personaEmpresaRolId =  iper.id          AND
                                 ip.id                  =  iper.personaId   AND
                                 iper.empresaRolId      =  ier.id           AND
                                 ic.estado              = :ESTADO           AND
                                 iper.estado          IN (:ESTADO_PER)      AND
                                 ier.empresaCod         = :EMPRESA";

            $objQuery->setParameter("PERSONA",    $idPersona);
            $objQuery->setParameter("ESTADO",     $estado);
            $objQuery->setParameter("ESTADO_PER", $arrayEstados);
            $objQuery->setParameter("EMPRESA",    $idEmpresa);

            return $objQuery->setDQL($strDQL)->getOneOrNullResult();
        }
        catch(\Exception $ex)
        {
            error_log($ex->getMessage());
            return array('error' => $ex->getMessage());
        }
    }

    public function findContratosPorEmpresaPorPersona($idEmpresa,$idPersona){	
                $query = $this->_em->createQuery("SELECT ic
		FROM 
                schemaBundle:InfoPersona ip, schemaBundle:InfoPersonaEmpresaRol iper, 
                schemaBundle:InfoEmpresaRol ier, schemaBundle:AdmiRol rol, 
				schemaBundle:AdmiTipoRol trol, schemaBundle:InfoContrato ic
		WHERE 
                ip.id=$idPersona AND
                ic.personaEmpresaRolId=iper.id AND
                ip.id=iper.personaId AND
                iper.empresaRolId=ier.id AND
				ier.rolId=rol.id AND
				rol.tipoRolId=trol.id AND
				trol.descripcionTipoRol in  ('Cliente','Pre-cliente') AND
				iper.estado in ('Activo','Pendiente','Pend-convertir') AND
                ier.empresaCod='".$idEmpresa."'");
		$datos = $query->getOneOrNullResult();
                //echo $query->getSQL();die;
		return $datos;
	}     
	
		public function findContratosPorEmpresaPorPersonaEmpresaRol($idEmpresa,$idPersonaEmpresaRol){	
                $query = $this->_em->createQuery("SELECT ic
		FROM 
                schemaBundle:InfoPersonaEmpresaRol iper, 
				schemaBundle:InfoContrato ic
		WHERE 
                iper.id=$idPersonaEmpresaRol AND
                ic.personaEmpresaRolId=iper.id AND
				ic.estado in ('Activo','Pendiente','Cancelado','Cancel','Rechazado', 'PorAutorizar')");
		$datos = $query->getResult();
                //echo $query->getSQL();die;
		return $datos;
	}

    public function findContratoActivoPorPersonaEmpresaRol($idPersonaEmpresaRol)
    {
        $query = $this->_em->createQuery("SELECT ic
    		FROM 
                schemaBundle:InfoContrato ic
                JOIN ic.personaEmpresaRolId iper
    		WHERE 
                iper.id = :idPersonaEmpresaRol AND
                ic.estado = 'Activo'");
        $query->setParameter('idPersonaEmpresaRol', $idPersonaEmpresaRol);
        $datos = $query->getOneOrNullResult();
        return $datos;
    }

	public function findContratosPorEmpresaPorPersonaEmpresaRolTodos($idEmpresa,$idPersonaEmpresaRol){	
                $query = $this->_em->createQuery("SELECT ic
		FROM 
                schemaBundle:InfoPersonaEmpresaRol iper, 
		schemaBundle:InfoContrato ic
		WHERE 
                iper.id=$idPersonaEmpresaRol AND
                ic.personaEmpresaRolId=iper.id AND
		ic.estado in ('Activo','Pendiente','Cancelado','Cancel') ORDER BY fe_creacion DESC")->setMaxResults(1);
		$datos = $query->getResult();
                //echo $query->getSQL();die;
		return $datos;
	} 
        
        public function getEstados()
	{
	      $qb = $this->_em->createQueryBuilder('c')
		  ->select('DISTINCT c.estado')
		  ->from('schemaBundle:InfoContrato','c')
                  ->orderBy('c.estado','ASC');
	      
	      $estados = $qb->getQuery()->getResult();
	      return $estados;
	} 

	/**
	 * Obtiene el maximo numeroContrato del cliente correspondiente al
	 * empresaCod, identificacionCliente y estado (opcional) dados.
	 * @author ltama
	 */
	public function findNumeroContratoPorEmpresaPorIdentificacionPorEstado($empresaCod, $identificacionCliente, $estado=NULL)
	{
	    $query = $this->_em->createQuery("
            SELECT
                MAX(con.numeroContrato) AS numeroContrato
            FROM schemaBundle:InfoContrato con
                JOIN schemaBundle:InfoPersonaEmpresaRol rol WITH rol.id=con.personaEmpresaRolId
                JOIN schemaBundle:InfoEmpresaRol emp WITH emp.id=rol.empresaRolId
                JOIN schemaBundle:InfoPersona per WITH per.id=rol.personaId
            WHERE emp.empresaCod=:empresaCod
                AND per.identificacionCliente=:identificacionCliente " .
	            (!is_null($estado) ? " AND con.estado=:estado " : "")
	    );
	    $query->setParameters(array(
	                    'empresaCod' => $empresaCod,
	                    'identificacionCliente' => $identificacionCliente,
	    ));
	    if (!is_null($estado))
	    {
	        $query->setParameter('estado', $estado);
	    }
	
	    return $query->getSingleScalarResult();
	}
     /**
     * Documentación para el método 'listarContratosTnPorCriterios'.
     * Saca informacion de los contratos  segun criterios de busqueda por empresa que se encuentran pendientes de Aprobacion y que 
     * corresponden a contratos nuevos para la empresa TN
     * @param array     $arrayParams    
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 19-06-2016
     * @return array       
     */
    public function listarContratosTnPorCriterios($arrayParams)
    {				                                                			
        $strSqlDatos      = 'SELECT ic ';
        $strSqlCantidad   = 'SELECT count(ic) ';
        $strSqlFrom       = 'FROM 
					schemaBundle:InfoPersonaEmpresaRol iper , 
                    schemaBundle:InfoPersona pers,
					schemaBundle:InfoEmpresaRol ier, 
                    schemaBundle:InfoContrato ic       
                    
                    WHERE   ' . ($arrayParams['idOficina'] ? ' iper.oficinaId=:idOficina AND ' : '') .
            ' ' . ($arrayParams['idper'] ? '  iper.id=:idper AND ' : '') .
            ' ' . ($arrayParams['fechaDesde'] ? ' ic.feCreacion>=:fechaDesde AND ' : '') .
            ' ' . ($arrayParams['fechaHasta'] ? ' ic.feCreacion<=:fechaHasta AND ' : '') .
            ' ' . ($arrayParams['nombre'] ? '  pers.identificacionCliente=:nombre AND ' : '') .
            ' ic.personaEmpresaRolId=iper.id AND
            iper.personaId=pers.id and 
			iper.empresaRolId=ier.id AND
			ic.estado =:estado  AND
			ier.empresaCod=:idEmpresa 
                
            AND EXISTS (SELECT 1
		                 FROM 
                        schemaBundle:InfoPunto pto,
                        schemaBundle:InfoServicio s
		                WHERE 
                        pto.personaEmpresaRolId=iper.id AND
                        s.puntoId=pto.id and s.estado not in (:estadosServicio)
                       ) 
            ORDER BY ic.feCreacion desc';           
        
        $strQueryDatos  = '';
        $strQueryDatos  = $this->_em->createQuery();                      
        $strQueryDatos->setParameters(array('estado'          => $arrayParams['estado'], 
                                            'idEmpresa'       => $arrayParams['idEmpresa'],                                            
                                            'estadosServicio' => array('Rechazado', 'Rechazada', 'Cancelado', 'Anulado', 'Cancel', 'Eliminado', 
                                                                    'Reubicado', 'Trasladado')));
        if($arrayParams['idOficina'])
        {
            $strQueryDatos->setParameter('idOficina', $arrayParams['idOficina']);
        }
        if($arrayParams['idper'])
        {
            $strQueryDatos->setParameter('idper', $arrayParams['idper']);
        }
        if($arrayParams['fechaDesde'] && $arrayParams['fechaHasta'])
        {
            $strQueryDatos->setParameter('fechaDesde', $arrayParams['fechaDesde']);
            $strQueryDatos->setParameter('fechaHasta', $arrayParams['fechaHasta']);
        }
        if($arrayParams['nombre'])
        {
            $strQueryDatos->setParameter('nombre', trim($arrayParams['nombre']));
        }
        $strSqlDatos   .= $strSqlFrom;                
        $strQueryDatos->setDQL($strSqlDatos);
        $objDatos       = $strQueryDatos->setFirstResult($arrayParams['start'])->setMaxResults($arrayParams['limit'])->getResult();
        
        $strQueryCantidad   = '';
        $strQueryCantidad   = $this->_em->createQuery();
        $strQueryCantidad->setParameters(array('estado'       => $arrayParams['estado'], 
                                            'idEmpresa'       => $arrayParams['idEmpresa'],
                                            'estadosServicio' => array('Rechazado', 'Rechazada', 'Cancelado', 'Anulado', 'Cancel', 'Eliminado', 
                                                                    'Reubicado', 'Trasladado')
                                            ));
        if($arrayParams['idOficina'])
        {
            $strQueryCantidad->setParameter('idOficina', $arrayParams['idOficina']);
        }
        if($arrayParams['idper'])
        {
            $strQueryCantidad->setParameter('idper', $arrayParams['idper']);
        }
        if($arrayParams['fechaDesde'] && $arrayParams['fechaHasta'])
        {
            $strQueryCantidad->setParameter('fechaDesde', $arrayParams['fechaDesde']);
            $strQueryCantidad->setParameter('fechaHasta', $arrayParams['fechaHasta']);
        }
        if($arrayParams['nombre'])
        {
            $strQueryCantidad->setParameter('nombre', trim($arrayParams['nombre']));
        }
        
        $strSqlCantidad        .= $strSqlFrom;
        $strQueryCantidad->setDQL($strSqlCantidad);        
        $intTotal                    = $strQueryCantidad->getSingleScalarResult();
        $arrayResultado['registros'] = $objDatos;        
        $arrayResultado['total']     = $intTotal;
        return $arrayResultado;

       }
    
    /**
     * Documentación para el método 'listarContratosPorCambioRazonSocialCriterios'.
     * Saca informacion de los contratos  segun criterios de busqueda por empresa que se encuentran pendientes de Aprobacion y que 
     * corresponden a contratos creados por cambio de Razon Social por Punto.
     * @param array     $arrayParams    
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 02-10-2015   
     * @return array  
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.1 - Se agrega la clausula IN al estado del contrato
     * @since 1.0
     *      
     */
    public function listarContratosPorCambioRazonSocialCriterios($arrayParams)
    {                       
        $strSqlDatos      = 'SELECT ic ';
        $strSqlCantidad   = 'SELECT count(ic) ';
        $strSqlFrom       = 'FROM 
					schemaBundle:InfoPersonaEmpresaRol iper , 
                    schemaBundle:InfoPersona pers,
					schemaBundle:InfoEmpresaRol ier, 
                    schemaBundle:InfoContrato ic                    
			WHERE   ' . ($arrayParams['idOficina'] ? ' iper.oficinaId=:idOficina AND ' : '') .
            ' ' . ($arrayParams['idper'] ? '  iper.id=:idper AND ' : '') .
            ' ' . ($arrayParams['fechaDesde'] ? ' ic.feCreacion>=:fechaDesde AND ' : '') .
            ' ' . ($arrayParams['fechaHasta'] ? ' ic.feCreacion<=:fechaHasta AND ' : '') .
            ' ' . ($arrayParams['nombre'] ? '  pers.identificacionCliente=:nombre AND ' : '') .
            ' ic.personaEmpresaRolId=iper.id AND
            iper.personaId=pers.id and 
			iper.empresaRolId=ier.id AND
			ic.estado IN (:estado)  AND
			ier.empresaCod=:idEmpresa 
            AND EXISTS (
                        SELECT 1
                        FROM schemaBundle:InfoPersonaEmpresaRolCarac ipercar,
                        schemaBundle:AdmiCaracteristica carac,
                        schemaBundle:InfoPunto p 
                        WHERE iper.id = ipercar.personaEmpresaRolId
                         and carac.id = ipercar.caracteristicaId 
                         and carac.descripcionCaracteristica =:nomb_caract
                         and ipercar.estado =:estadoActivo
                         and p.id=ipercar.valor)
            ORDER BY ic.feCreacion desc';
        $strQueryDatos  = '';
        $strQueryDatos  = $this->_em->createQuery();                      
        $strQueryDatos->setParameters(array('estado'       => $arrayParams['estado'], 
                                            'idEmpresa'    => $arrayParams['idEmpresa'],
                                            'nomb_caract'  => 'PUNTO CAMBIO RAZON SOCIAL',
                                            'estadoActivo' => 'Activo'));
        if($arrayParams['idOficina'])
        {
            $strQueryDatos->setParameter('idOficina', $arrayParams['idOficina']);
        }
        if($arrayParams['idper'])
        {
            $strQueryDatos->setParameter('idper', $arrayParams['idper']);
        }
        if($arrayParams['fechaDesde'] && $arrayParams['fechaHasta'])
        {
            $strQueryDatos->setParameter('fechaDesde', $arrayParams['fechaDesde']);
            $strQueryDatos->setParameter('fechaHasta', $arrayParams['fechaHasta']);
        }
        if($arrayParams['nombre'])
        {
            $strQueryDatos->setParameter('nombre', trim($arrayParams['nombre']));
        }
        $strSqlDatos   .= $strSqlFrom;                
        $strQueryDatos->setDQL($strSqlDatos);
        $objDatos       = $strQueryDatos->setFirstResult($arrayParams['start'])->setMaxResults($arrayParams['limit'])->getResult();
        
        $strQueryCantidad   = '';
        $strQueryCantidad   = $this->_em->createQuery();
        $strQueryCantidad->setParameters(array('estado'    => $arrayParams['estado'], 
                                            'idEmpresa'    => $arrayParams['idEmpresa'],
                                            'nomb_caract'  => 'PUNTO CAMBIO RAZON SOCIAL',
                                            'estadoActivo' => 'Activo'));
        if($arrayParams['idOficina'])
        {
            $strQueryCantidad->setParameter('idOficina', $arrayParams['idOficina']);
        }
        if($arrayParams['idper'])
        {
            $strQueryCantidad->setParameter('idper', $arrayParams['idper']);
        }
        if($arrayParams['fechaDesde'] && $arrayParams['fechaHasta'])
        {
            $strQueryCantidad->setParameter('fechaDesde', $arrayParams['fechaDesde']);
            $strQueryCantidad->setParameter('fechaHasta', $arrayParams['fechaHasta']);
        }
        if($arrayParams['nombre'])
        {
            $strQueryCantidad->setParameter('nombre', trim($arrayParams['nombre']));
        }
        
        $strSqlCantidad        .= $strSqlFrom;
        $strQueryCantidad->setDQL($strSqlCantidad);        
        $intTotal               = $strQueryCantidad->getSingleScalarResult();
        $arrayResultadoPuntos['registros'] = $objDatos;        
        $arrayResultadoPuntos['total']     = $intTotal;
        return $arrayResultadoPuntos;

    }
    
    /**
     * 
     * Metodo que devuelve el registro relacionado al contrado creado por un nodo
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 22-02-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - Se obliga que devuelve siempre el nombre y el apellido del representante legal de Telconet
     * @since 08-07-2016
     * 
     * @param integer $intIdElemento
     * @param string  $codEmpresa
     * @return array
     */
    public function getRegistroContratoPorElementoNodo($intIdElemento,$codEmpresa)
    {        
        try
        {
            $rsm   = new ResultSetMappingBuilder($this->_em);	      
            $query = $this->_em->createNativeQuery(null, $rsm);	                        

            $strSelectData = "SELECT 
                                SOL.ID_DETALLE_SOLICITUD,
                                CONT.ID_CONTRATO,
                                CONT.NUMERO_CONTRATO,
                                CONT.VALOR_CONTRATO,
                                CONT.VALOR_ANTICIPO,
                                CONT.VALOR_GARANTIA,
                                TO_CHAR(CONT.FE_INI_CONTRATO,'YYYY-MM-DD') FECHA_INICIO,
                                TO_CHAR(CONT.FE_FIN_CONTRATO,'YYYY-MM-DD') FECHA_FIN,
                                ROUND(months_between(CONT.FE_FIN_CONTRATO,CONT.FE_INI_CONTRATO)/12) DURACION,
                                CONT.INCREMENTO_ANUAL,
                                CONT.OFICINA_REP_LEGAL,
                                (PERSONA.NOMBRES
                                ||' '
                                ||PERSONA.APELLIDOS) REP_LEGAL,
                                PERSONA.LOGIN LOGIN,
                                PERSONA.ID_PERSONA,
                                
                                (SELECT NVL(
                                (SELECT BTC.BANCO_ID
                                FROM ADMI_BANCO_TIPO_CUENTA BTC,
                                  INFO_CONTRATO_FORMA_PAGO PAGO
                                WHERE BTC.ID_BANCO_TIPO_CUENTA = PAGO.BANCO_TIPO_CUENTA_ID
                                AND PAGO.CONTRATO_ID           = CONT.ID_CONTRATO
                                AND PAGO.ESTADO                = :estado),0) FROM DUAL) BANCO,
                                
                                (SELECT NVL(
                                (SELECT BTC.TIPO_CUENTA_ID
                                FROM ADMI_BANCO_TIPO_CUENTA BTC,
                                  INFO_CONTRATO_FORMA_PAGO PAGO
                                WHERE BTC.ID_BANCO_TIPO_CUENTA = PAGO.BANCO_TIPO_CUENTA_ID
                                AND PAGO.CONTRATO_ID           = CONT.ID_CONTRATO
                                AND PAGO.ESTADO                = :estado),0) FROM DUAL) TIPO_CUENTA,
                                                              
                                CONT.FORMA_PAGO_ID FORMA_PAGO,
                                                                
                                (SELECT NVL(
                                  (SELECT NUMERO_CTA_TARJETA
                                  FROM INFO_CONTRATO_FORMA_PAGO
                                  WHERE CONTRATO_ID = CONT.ID_CONTRATO
                                  AND ESTADO        = :estado
                                  ),'N/A')
                                FROM DUAL
                                ) NUMERO_PAGO,
                                
                                ELE.NOMBRE_ELEMENTO NODO,
                                UBICA.DIRECCION_UBICACION DIRECCION,
                                CANTON.NOMBRE_CANTON CANTON,
                                PROV.NOMBRE_PROVINCIA PROVINCIA
                              FROM 
                                INFO_DETALLE_SOLICITUD SOL,
                                INFO_DETALLE_SOL_CARACT SOLC,
                                ADMI_CARACTERISTICA CARACT,
                                INFO_CONTRATO CONT,                               
                                ADMI_TIPO_SOLICITUD TIPO,
                                INFO_PERSONA PERSONA,
                                INFO_ELEMENTO ELE,
                                INFO_EMPRESA_ELEMENTO_UBICA ELEUBICA,
                                INFO_UBICACION UBICA,
                                ADMI_PARROQUIA PARR,
                                ADMI_CANTON CANTON,
                                ADMI_PROVINCIA PROV
                              WHERE TIPO.ID_TIPO_SOLICITUD          = SOL.TIPO_SOLICITUD_ID
                              AND SOL.ID_DETALLE_SOLICITUD          = SOLC.DETALLE_SOLICITUD_ID
                              AND SOLC.VALOR                        = CONT.ID_CONTRATO                              
                              AND CARACT.ID_CARACTERISTICA          = SOLC.CARACTERISTICA_ID
                              AND PERSONA.LOGIN                     = CONT.USR_REP_LEGAL
                              AND SOL.ELEMENTO_ID                   = ELE.ID_ELEMENTO
                              AND ELEUBICA.ELEMENTO_ID              = ELE.ID_ELEMENTO
                              AND ELEUBICA.UBICACION_ID             = UBICA.ID_UBICACION
                              AND UBICA.PARROQUIA_ID                = PARR.ID_PARROQUIA
                              AND PARR.CANTON_ID                    = CANTON.ID_CANTON
                              AND CANTON.PROVINCIA_ID               = PROV.ID_PROVINCIA
                              AND TIPO.DESCRIPCION_SOLICITUD        = :solicitud
                              AND SOLC.CARACTERISTICA_ID            = CARACT.ID_CARACTERISTICA
                              AND CARACT.DESCRIPCION_CARACTERISTICA = :contrato
                              AND CONT.ESTADO                       = :estado
                              AND SOLC.ESTADO                       = :estado
                              AND SOL.ELEMENTO_ID                   = :elemento
                              AND ELEUBICA.EMPRESA_COD              = :empresa";                                                

            $rsm->addScalarResult('ID_DETALLE_SOLICITUD','solicitud','integer');
            $rsm->addScalarResult('ID_CONTRATO','idContrato','integer');
            $rsm->addScalarResult('ID_PERSONA','idPersona','integer');
            $rsm->addScalarResult('NUMERO_CONTRATO','numeroContrato','string');
            $rsm->addScalarResult('VALOR_CONTRATO','valor','float');
            $rsm->addScalarResult('VALOR_ANTICIPO','anticipo','float');
            $rsm->addScalarResult('VALOR_GARANTIA','garantia','float');
            $rsm->addScalarResult('FECHA_INICIO','fechaInicio','string');
            $rsm->addScalarResult('FECHA_FIN','fechaFin','string');
            $rsm->addScalarResult('DURACION','duracion','integer');
            $rsm->addScalarResult('INCREMENTO_ANUAL','incremento','integer');            
            $rsm->addScalarResult('OFICINA_REP_LEGAL','oficina','string');
            $rsm->addScalarResult('REP_LEGAL','repLegal','string');
            $rsm->addScalarResult('LOGIN','login','string');
            $rsm->addScalarResult('BANCO','banco','integer');
            $rsm->addScalarResult('TIPO_CUENTA','tipoCuenta','integer');
            $rsm->addScalarResult('FORMA_PAGO','formaPago','integer');
            $rsm->addScalarResult('NUMERO_PAGO','numeroPago','string');
            $rsm->addScalarResult('NODO','nodo','string');
            $rsm->addScalarResult('DIRECCION','direccion','string');
            $rsm->addScalarResult('CANTON','canton','string');
            $rsm->addScalarResult('PROVINCIA','provincia','string');

            $query->setParameter('solicitud', 'SOLICITUD NUEVO NODO'); 
            $query->setParameter('contrato', 'CONTRATO'); 
            $query->setParameter('estado', 'Activo'); 
            $query->setParameter('elemento', $intIdElemento);             
            $query->setParameter('empresa', $codEmpresa); 
            
            $query->setSQL($strSelectData);	             

            return $query->getArrayResult();        
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }                
    }
    
    /**
     * Documentación para la función getFormaPagoContrato
     * 
     * Función que obtiene la descripción de la forma de pago del cliente enviado como parámetro
     * 
     * @param array $arrayParametros['strTipoInformacion' => 'Tipo de campo a obtener en la informacion del Contrato',
     *                               'intIdPersonaRol'    => 'Id del cliente'
     *                               'strEstado'          => 'Estado del cliente']
     * 
     * @param array $arrayParametros['intIdPagoDet'       => 'Id del detalle del pago',
     *                               'strCodigoFormaPago' => 'Código de la forma de pago']
     * @return String $strDescripcionFormaPago
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 23-03-2018
     */
    public function getFormaPagoContrato($arrayParametros)
    {
        $strDescripcionFormaPago = "";
        
        try
        {
            if( !empty($arrayParametros) )
            {
            
                $strDescripcionFormaPago = str_pad($strDescripcionFormaPago, 50, " ");
                
                $strSql = " BEGIN :strDescripcionFormaPago := DB_COMERCIAL.CMKG_REPORTE_APROB_CONTRATOS.F_INFORMACION_CONTRATO_CLI(:strTipoInformacion, "
                        . ":intIdPersonaRol, :strEstadoCliente); END;";
                
                $objStmt = $this->_em->getConnection()->prepare($strSql);
                $objStmt->bindParam('strTipoInformacion',$arrayParametros["strTipoInformacion"]);
                $objStmt->bindParam('intIdPersonaRol'   ,$arrayParametros["intIdPersonaRol"]);
                $objStmt->bindParam('strEstadoCliente'  ,$arrayParametros["strEstado"]);
                $objStmt->bindParam('strDescripcionFormaPago', $strDescripcionFormaPago);
                $objStmt->execute();
            }
            
        }
        catch(\Exception $ex)
        {
           error_log($ex->getMessage());
           
           throw($ex);
        }
        
        return $strDescripcionFormaPago;
    }    
    
    /**
     * Documentación para el método 'getLogsPorCriterios'.
     * Obtiene información de los logs registrados por visualización de número de cuenta- tarjeta  según criterios de búsqueda.
     * Costo : 79
     * @param array     $arrayParametros    
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 05-04-2020 
     *
     * @return array $arrayDatos      
     */
    public function getLogsPorCriterios($arrayParametros)
    {
        $strFechaDesde     = $arrayParametros['fechaDesde'];
        $strFechaHasta     = $arrayParametros['fechaHasta'];
        $strUsuario        = $arrayParametros['usuario'] ? $arrayParametros['usuario'] : "";
        $strEstado         = $arrayParametros['estado'] ? $arrayParametros['estado'] : '';
        $strNombre         = $arrayParametros['nombre'] ? strtoupper($arrayParametros['nombre']) : "";
        $strApellido       = $arrayParametros['apellido'] ? strtoupper($arrayParametros['apellido']) : "";
        $strIdentificacion = $arrayParametros['identificacion'] ? $arrayParametros['identificacion'] : "";       
        $strLimit          = $arrayParametros['limit'] ? intval($arrayParametros['limit']) : 0;
        $strStart          = $arrayParametros['start'] ? intval($arrayParametros['start']) : 0;
        $strPrefijoEmpresa = $arrayParametros['strPrefijoEmpresa'];

        try
        {
            $objRsmCount    = new ResultSetMappingBuilder($this->_em);
            $objQueryCount  = $this->_em->createNativeQuery(null, $objRsmCount);
            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";
            $objRsmCount->addScalarResult('TOTAL', 'total', 'integer');
            
            $objRsm = new ResultSetMappingBuilder($this->_em);
            $objRsm->addScalarResult('ID_CONTRATO_LOG','id','integer');
            $objRsm->addScalarResult('CONTRATO_ID','contratoId','integer');
            $objRsm->addScalarResult('PERSONA_ID','personaId','integer');
            $objRsm->addScalarResult('NOMBRE', 'nombre','string');
            $objRsm->addScalarResult('FE_CREACION','feCreacion','datetime');
            $objRsm->addScalarResult('USR_CREACION', 'usrCreacion','string');
            $objRsm->addScalarResult('ESTADO', 'estado','string');

            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSelect = "SELECT ILOG.ID_CONTRATO_LOG ,
                                 ILOG.CONTRATO_ID,
                                 ILOG.PERSONA_ID,
                                 ILOG.FE_CREACION,
                                 ILOG.USR_CREACION,
                                 CONCAT(CONCAT(NVL(IPER.NOMBRES,''),' '),NVL(IPER.APELLIDOS,'')) AS NOMBRE,
                                 ILOG.ESTADO";
            $strFrom = " FROM
                              DB_COMERCIAL.INFO_CONTRATO_FORMA_PAGO_LOG ILOG
                         JOIN DB_COMERCIAL.INFO_PERSONA             IPER ON IPER.ID_PERSONA    = ILOG.PERSONA_ID 
                         JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPR  ON IPER.ID_PERSONA    = IPR.PERSONA_ID 
                         JOIN DB_COMERCIAL.INFO_EMPRESA_ROL         IER  ON IER.ID_EMPRESA_ROL = IPR.EMPRESA_ROL_ID
                         JOIN DB_COMERCIAL.INFO_EMPRESA_GRUPO       IEG  ON IEG.COD_EMPRESA    = IER.EMPRESA_COD ";
            $strWhere = " WHERE ILOG.ESTADO = 'Activo'  AND IEG.PREFIJO = :strPrefijoEmpresa ";
            
            $objQuery->setParameter('strPrefijoEmpresa', $strPrefijoEmpresa);
            $objQueryCount->setParameter('strPrefijoEmpresa', $strPrefijoEmpresa);            
        
            if ($strEstado!="")
            {       
                $strWhere .=" AND UPPER(ILOG.ESTADO) = :strEstado";
                $objQuery->setParameter('strEstado', strtoupper($strEstado));
                $objQueryCount->setParameter('strEstado', strtoupper($strEstado));
            }    
            if ($strNombre!="")
            {       
                $strWhere .=" AND UPPER(IPER.NOMBRES) like :strNombre";
                $objQuery->setParameter('strNombre', "%".$strNombre."%");
                $objQueryCount->setParameter('strNombre', "%".$strNombre."%");
            }  
            if ($strApellido!="")
            {       
                $strWhere .=" AND UPPER(IPER.APELLIDOS) like :strApellido";
                $objQuery->setParameter('strApellido', "%".$strApellido."%");
                $objQueryCount->setParameter('strApellido', "%".$strApellido."%");
            } 
       
            if ($strIdentificacion)
            {       
                $strWhere .=" AND IPER.IDENTIFICACION_CLIENTE = :strIdentificacion";
                $objQuery->setParameter('strIdentificacion', $strIdentificacion);
                $objQueryCount->setParameter('strIdentificacion', $strIdentificacion);
            }
            
            if ($strUsuario!="")
            {       
                $strWhere .=" AND UPPER(ILOG.USR_CREACION) = UPPER(:strUsuario)";
                $objQuery->setParameter('strUsuario', $strUsuario);
                $objQueryCount->setParameter('strUsuario', $strUsuario);
            }
            
            if ($strFechaDesde)
            {
                $strFechaD = strtotime($strFechaDesde);
                if($strFechaD)
                {
                    $strWhere .=" AND ILOG.FE_CREACION >= :fechaDesde ";
                    $objQuery->setParameter('fechaDesde', date("Y/m/d", $strFechaD));
                    $objQueryCount->setParameter('fechaDesde', date("Y/m/d", $strFechaD));
                }
            }
            if ($strFechaHasta)
            {
                $strFechaH = strtotime($strFechaHasta);
               
                if($strFechaH)
                {
                    $strWhere .=" AND ILOG.FE_CREACION <= :fechaHasta";
                    $objQuery->setParameter('fechaHasta', date("Y/m/d", $strFechaH));
                    $objQueryCount->setParameter('fechaHasta', date("Y/m/d", $strFechaH));
                }
            }

            $strSql = $strSelect . $strFrom . $strWhere;

            $strSqlCount = $strSelectCount ." FROM (".$strSql.")";
            $objQueryCount->setSQL($strSqlCount);      
            $intTotalPersonas = $objQueryCount->getSingleScalarResult();
            
            $strSql .= " ORDER BY ILOG.FE_CREACION DESC";
            $objQuery->setSQL($strSql);
            $arrayPersonas = $this->setQueryLimit($objQuery,$strLimit,$strStart)->getArrayResult();
            
            $objResultado['total']     = $intTotalPersonas;
            $objResultado['registros'] = $arrayPersonas;
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
            $objResultado = array(
                            'total'     => 0 ,
                            'registros' => array()
                            );
            
        }
        
        return $objResultado;
        
    }
    /**
     * Documentación para la función getValorInstProMensuales
     * 
     * Función que obtiene la descripción de la forma de pago del cliente enviado como parámetro
     * 
     * @author Angel Reina <areina@telconet.ec>
     * @version 1.0 15-07-2019
     * 
     * @author Edgar Holguína <eholguin@telconet.ec>
     * @version 1.1 25-03-2021  Se agrega parámetro tipo de documento en llamada a función que obtiene descuento facturado.
     */
    public function getValorInstProMensuales($arrayParametros)
    {

        $arrayValores      = array();
        $arrayPuntos       = array();
        $strEmpresaCod     = $arrayParametros['idEmpresa'];
        $intIdContrato     = $arrayParametros['contradoId'];
        $intIdMotivo       = $arrayParametros['motivo'];    
        $intIdFormaPago    = $arrayParametros['formaPagoId'];
        $intIdTipoCuenta   = $arrayParametros['tipoCuentaId'];
        $strFormaPago      = str_pad($strFormaPago, 50, " ");         
        $intPromocion      = str_pad(' ', 30);        
                                    
        try
        {
            if( !empty($arrayParametros) )
            {

                $objMotivoFormaPago = $this->_em->getRepository('schemaBundle:AdmiMotivo')->find($intIdMotivo);
                if(!is_object($objMotivoFormaPago))
                { 
                    throw new \Exception('No encontrá el Motivo');
                }

                $objParametroCab = $this->_em->getRepository('schemaBundle:AdmiParametroCab')
                                              ->findOneBy(array("nombreParametro" => "MOTIVOS_CAMBIO_FORMA_PAGO",
                                                                "estado"          => "Activo"));
                if (is_object($objParametroCab))
                {
                    $arrayParametroDet = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->findBy(array("parametroId" => $objParametroCab,
                                                                   "estado"      => "Activo")); 
                    if ($arrayParametroDet)
                    { 
                        foreach ($arrayParametroDet as $parametroDet)
                        {
                            if ($parametroDet->getValor2() === $objMotivoFormaPago->getNombreMotivo() && 
                                $parametroDet->getValor1() === "S" && $parametroDet->getEstado() == "Activo")
                            {                                       
                                $arrayPuntos   = $this->getPuntosxContrato($intIdContrato);
                                   if($arrayPuntos)
                                   {
                                       foreach ($arrayPuntos as $arrayPuntosContrato)
                                       {
                                           if (isset ($arrayPuntosContrato['idpunto'])&& !empty($arrayPuntosContrato['idpunto']))
                                           {            
                                                //Cta Seleccionada.
                                                $strSql  = "BEGIN "
                                                         . ":Lv_TipoFormaPagoSeleccionada := DB_FINANCIERO.FNCK_CANCELACION_VOL."
                                                         . "F_GET_TIPO_CUENTA_SELEC(:Fn_IdFormaPago,:Fn_IdTipoCuenta); END;";
                                                $objStmt = $this->_em->getConnection()->prepare($strSql);              
                                                $objStmt->bindParam('Fn_IdFormaPago'  , $intIdFormaPago);
                                                $objStmt->bindParam('Fn_IdTipoCuenta' , $intIdTipoCuenta);
                                                $objStmt->bindParam('Lv_TipoFormaPagoSeleccionada' , $strFormaPago);
                                                $objStmt->execute();

                                                
                                                //Total promocional.
                                                $strTipoDoc    = 'FAC';
                                                $intPromocion  = str_pad(' ', 30);   
                                                $strSql = "BEGIN "
                                                        . ":Lf_TotalFacturado := DB_FINANCIERO.FNCK_CANCELACION_VOL"
                                                        . ".F_GET_DCTO_FACTURADO(:Fn_IdPunto,:Fv_TioDoc); END;";
                                                
                                                $objStmt = $this->_em->getConnection()->prepare($strSql);  
                                                $objStmt->bindParam('Fn_IdPunto'        , $arrayPuntosContrato['idpunto']);
                                                $objStmt->bindParam('Fv_TioDoc'         , $strTipoDoc);
                                                $objStmt->bindParam('Lf_TotalFacturado' , $intPromocion);
                                                $objStmt->execute();
                                                //Total Instalacion.
                                                $strSql = "BEGIN "
                                                        . ":Ln_TotalFactInstalacion := DB_FINANCIERO.FNCK_CANCELACION_VOL"
                                                        . ".F_GET_CANCEL_VOL_INST_AMORT(:Fv_EmpresaCod,:Fn_IdPunto,:Fn_IdServicio,:Fn_IdContrato);"
                                                        . " END;";
                                                $intIdServicio = null;
                                                $intValorInst  = str_pad(' ', 30); 
                                                $objStmt = $this->_em->getConnection()->prepare($strSql);              
                                                $objStmt->bindParam('Fv_EmpresaCod' , $strEmpresaCod);                                                
                                                $objStmt->bindParam('Fn_IdPunto'  ,$arrayPuntosContrato['idpunto']);
                                                $objStmt->bindParam('Fn_IdServicio' , $intIdServicio);                                                
                                                $objStmt->bindParam('Fn_IdContrato' , $intIdContrato);
                                                $objStmt->bindParam('Ln_TotalFactInstalacion' , $intValorInst);
                                                $objStmt->execute();
                                                
                                                if ($intValorInst < 0 )
                                                {        
                                                    $intValorInst = 0;                                                
                                                }   
                                                else
                                                {
                                                     $intValorInst;
                                                }
                                                

                                                $intSuma = ROUND($intValorInst + $intPromocion,2 );
                                                    $arrayValores[] = array(                                                        
                                                        'idPunto'               => $arrayPuntosContrato['idpunto'],
                                                        'strlogin'              => $arrayPuntosContrato['login'],
                                                        'floatInstalacion'      => $intValorInst,
                                                        'floatPromocional'      => $intPromocion,
                                                        'floatInstProm'         => $intSuma,
                                                        'floatValorInst'         => 0,
                                                        'floatValorInstCambio'   => 0
                                                    );
                                                                                                        
                                                                       
                                           }    
                                       }
                                   }

                            } //if
                        }//foreach
                    } //arrayparametroDet
                }//objparametroCab

        }//arrayparametros
            
        } //try
        catch(\Exception $ex)
        {
           error_log($ex->getMessage());
           
           throw($ex);
        }
        
        return $arrayValores;
    }    
    
/**
* Documentación para la función getPuntosxContrato
* 
* Función que obtiene los puntos activos y login de un contrato.
* Costo: 9
* @author Madeline Haz <mhaz@telconet.ec>
* @version 1.0 19-07-2019
*/  
 public function getPuntosxContrato($intIdContrato)
    {        
        try
        {             
            $objRrsm   = new ResultSetMappingBuilder($this->_em);	      
            $objQquery = $this->_em->createNativeQuery(null, $objRrsm);	                      
            
            $strSelectData =   "SELECT IFP.LOGIN,
                                       IFP.ID_PUNTO
                                FROM   DB_COMERCIAL.INFO_PUNTO IFP
                                INNER  JOIN  DB_COMERCIAL.INFO_PUNTO_DATO_ADICIONAL IPDA ON  IFP.ID_PUNTO = IPDA.PUNTO_ID
                                WHERE  IFP.PERSONA_EMPRESA_ROL_ID  IN (SELECT PERSONA_EMPRESA_ROL_ID 
                                                                       FROM   DB_COMERCIAL.INFO_CONTRATO
                                                                       WHERE  ID_CONTRATO  = :idContrato
                                                                       AND    ESTADO       = :estado )    
                                AND    IPDA.ES_PADRE_FACTURACION = :padrefact
                                AND    IFP.ESTADO                = :estado";   
            
            
            
            $objRrsm->addScalarResult('LOGIN','login','string');
            $objRrsm->addScalarResult('ID_PUNTO','idpunto','integer');
            
            $objQquery->setParameter("padrefact", 'S');
            $objQquery->setParameter("estado", 'Activo');             
            $objQquery->setParameter("idContrato", $intIdContrato);
            $objQquery->setSQL($strSelectData);	             

            return $objQquery->getArrayResult();  
        }
        catch(\Exception $ex)
        {
           error_log($ex->getMessage());
           
           throw($ex);
        }
                    
    }    

/**
* Documentación para la función getDocumentosFormaPago
* 
* Función que obtiene los documentos de la forma de pago por contrato.
* Costo: 26778
* @author Angel Reina <areina@telconet.ec>
* @version 1.0 23-07-2019
*/  
 public function getDocumentosFormaPago($intIdContrato)
    {        
        try
        {             
            $objRsm    = new ResultSetMappingBuilder($this->_em);	      
            $objQquery = $this->_em->createNativeQuery(null, $objRsm);	                      
            
            $strSelectData =   "SELECT ID.*
                                FROM 
                                DB_COMUNICACION.INFO_DOCUMENTO ID
                                INNER JOIN DB_COMUNICACION.INFO_DOCUMENTO_RELACION IDR ON ID.ID_DOCUMENTO = IDR.DOCUMENTO_ID
                                WHERE 
                                IDR.PAGO_DATOS_ID IN(   SELECT ID_DATOS_PAGO
                                                        FROM ( SELECT  * 
                                                               FROM DB_COMERCIAL.INFO_CONTRATO_FORMA_PAGO_HIST 
                                                               WHERE CONTRATO_ID = :idContrato
                                                               ORDER BY FE_CREACION DESC 
                                                              )
                                                        WHERE ROWNUM = 1 
                                )
                                AND ID.ESTADO = 'ACTIVO'";   
            
            $objRsm->addScalarResult('LOGIN','login','string');
            $objRsm->addScalarResult('ID_PUNTO','idpunto','integer');          
            $objQquery->setParameter("padrefact", 'S');
            $objQquery->setParameter("estado", 'Activo');             
            $objQquery->setParameter("idContrato", $intIdContrato);
            $objQquery->setSQL($strSelectData);	             

            return $objQquery->getArrayResult();  
        }
        catch(\Exception $ex)
        {
           error_log($ex->getMessage());
           
           throw($ex);
        }
                    
    }
    
    /**    
     * getContratoPorEstado
     * 
     * Función que obtiene el contrato de la persona.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.0 17-08-2021   
     * 
     * Costo query: 5
     * 
     * @return $objInfoContrato  
     */
    public function getContratoPorEstado($arrayParametros)
    {
        $objQuery        = $this->_em->createQuery();
        $objInfoContrato = null;        
        try
        {
            $strSelect = " select ic ";
            $strFrom   = " from schemaBundle:InfoPersonaEmpresaRol iper,
                           schemaBundle:InfoContrato ic  ";
            $strWhere  = " where iper.id               = :idPersonaEmpresaRol
                           and ic.personaEmpresaRolId  = iper.id  
                           and ic.estado               in (:arrayEstados)
                           ORDER BY ic.feCreacion DESC ";
               
            $objQuery->setParameter("idPersonaEmpresaRol", $arrayParametros['idPersonaEmpresaRol']);
            $objQuery->setParameter("arrayEstados",        $arrayParametros['arrayEstados']);
            
            $strSql = $strSelect . $strFrom . $strWhere;
            
            $objQuery->setDQL($strSql);
            $objQuery->setMaxResults(1);
            $objInfoContrato  = $objQuery->getOneOrNullResult();               
        } 
        catch (\Exception $e) 
        {
            throw($e);
        }
        return $objInfoContrato;
    }

     /**
     * getContratoPorEstado
     * 
     * Función que obtiene el contrato de la persona.
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 17-08-2021
     */
    public function generarJsonAdendumContrato($arrayParametros)
    {
        $arrayEncontrados = array(); 
        $arrayRegistros   = $this->getAdendumContrato($arrayParametros); 
        foreach($arrayRegistros  as $objRegistro)
        {

            $strFecha = (empty($objRegistro['FE_MODIFICA']) ) ?   $objRegistro['FE_CREACION'] :$objRegistro['FE_MODIFICA'] ; 

            $arrayEncontrados[] = array('noAdendum'     => $objRegistro['NUMERO'],
                                        'tipo'          => $objRegistro['FORMA_CONTRATO'],
                                        'puntoId'       => $objRegistro['PUNTO_ID'],
                                        'feFinAdendum'  => $strFecha,
                                        'estado'        => $objRegistro['ESTADO'],
                                        'contratoId'    => $objRegistro['CONTRATO_ID'],
                                        'idAdendum'     => $objRegistro['ID_ADENDUM'],
                                        );
        }
        $intTotal           = count($arrayEncontrados);
        $arrayResultado     = array('total'         => $intTotal,
                                    'encontrados'   => $arrayEncontrados);
        return $arrayResultado;
    }

    /**
     * getContratoPorEstado
     * 
     * Función que obtiene el contrato de la persona.
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 17-08-2021
     */
    public function getAdendumContrato($arrayParametros)
    {
        $strEstado         = $arrayParametros['strEstado'] ? $arrayParametros['strEstado'] : '';
        $strNoAdendum      = $arrayParametros['noAdendum'] ? $arrayParametros['noAdendum'] : '';
        $arrayEstados =    ['Activo','Pendiente','Factible','PreFactibilidad','Planificada','PrePlanificada']; 
        $arrayResult  = array(); 
        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);
            $strSql = " SELECT  
                        AD.NUMERO,
                        AD.FORMA_CONTRATO ,
                        AD.PUNTO_ID,
                        TO_CHAR(AD.FE_CREACION, 'DD/MM/YYYY') FE_CREACION,
                        TO_CHAR(AD.FE_MODIFICA, 'DD/MM/YYYY') FE_MODIFICA,
                        AD.ESTADO, 
                        AD.CONTRATO_ID ,
                        AD.ID_ADENDUM
                        FROM DB_COMERCIAL.INFO_ADENDUM AD
                        INNER JOIN INFO_SERVICIO IS2 
                        ON AD.SERVICIO_ID = IS2.ID_SERVICIO
                        WHERE  IS2.PLAN_ID IS NOT NULL  
                        AND   IS2.ESTADO IN (:arrayEstados)
                        AND   AD.CONTRATO_ID = :idContrato
                        AND   AD.TIPO = :strTipo 
                        ";
        
             $objNativeQuery->setParameter("idContrato", $arrayParametros['strContratoId']);
             $objNativeQuery->setParameter("strTipo", 'AP');
             $objNativeQuery->setParameter("arrayEstados", $arrayEstados );

            if ($strEstado!="")
            {       
                $strSql .="  AND AD.ESTADO = :strEstado ";
                $objNativeQuery->setParameter('strEstado', $strEstado);
            }
            if ($strNoAdendum!="")
            {       
                $strSql .="  AND AD.NUMERO = :strNoAdendum ";
                $objNativeQuery->setParameter('strNoAdendum', $strNoAdendum);
            } 
             
            $objResultSetMap->addScalarResult('NUMERO',        'NUMERO',         'string');
            $objResultSetMap->addScalarResult('CONTRATO_ID',   'CONTRATO_ID',     'integer');
            $objResultSetMap->addScalarResult('ID_ADENDUM',    'ID_ADENDUM',      'string');
            $objResultSetMap->addScalarResult('ESTADO',        'ESTADO',          'string');
            $objResultSetMap->addScalarResult('FORMA_CONTRATO','FORMA_CONTRATO',  'string');
            $objResultSetMap->addScalarResult('PUNTO_ID',      'PUNTO_ID',        'integer');
            $objResultSetMap->addScalarResult('FE_CREACION',   'FE_CREACION',     'string');
            $objResultSetMap->addScalarResult('FE_MODIFICA',   'FE_MODIFICA',     'string'); 
 
            $objNativeQuery->setSQL($strSql);             
            $arrayResult = $objNativeQuery->getArrayResult(); 
        }
        catch (\Exception $ex)
        { 
            error_log("Errors: " . $ex->getMessage());
        }
        
        return $arrayResult; 
    } 

    /**
     *
     * Funcion para validar el numero de tarjeta de cuenta usando el
     * procediiento almacenado  DB_FINANCIERO.FNCK_ACTUALIZA_TARJETAS_ABU.P_VALIDAR_TARJETA_ABU
     *
     * arrayparametros[
     *                  strCodEmpresa           => Código de empresa
     *                  strNumeroCtaTarjeta     => Número de cuenta de la tarjeta
     *                  intBancoTipoCuentaId    => Id del banco tipo cuenta
     *                  intTipoCuentaId         => Id tipo de cuenta
     *                  intFormaPagoId          => Id de la forma de pago
     *                ]
     * @author  Christian Yunga <cyungat@telconet.ec>
     * @version 1.0 15-02-2023
     */
    
    public function getValidarNumeroTarjetaCta($arrayDatosTarjeta)
    {
    $strMsjResultado = '';
    
    try
    {
        if (!empty($arrayDatosTarjeta))
        {
            $strDataType = "BEGIN DB_FINANCIERO.FNCK_ACTUALIZA_TARJETAS_ABU.P_VALIDAR_TARJETA_ABU(".
            ":intTipoCuentaId, ".
            ":intBancoTipoCuentaId, ".
            ":strNumeroCtaTarjeta, ".
            ":strCodigoVerificacion, ".
            ":strCodEmpresaParam, ".
            ":strIpUsr, ".
            ":strMsjResultadoParam); END;";
        
            $objStmt = $this->_em->getConnection()->prepare($strDataType);
            $strCodigoVerificacion ='';
            $strIpUsr ='';
            $strMsjResultado = str_pad($strMsjResultado, 5000, " ");
            $objStmt->bindParam('intTipoCuentaId', $arrayDatosTarjeta['intTipoCuentaId']);
            $objStmt->bindParam('intBancoTipoCuentaId', $arrayDatosTarjeta['intBancoTipoCuentaId']);
            $objStmt->bindParam('strNumeroCtaTarjeta', $arrayDatosTarjeta['strNumeroCtaTarjeta']);
            $objStmt->bindParam('strCodigoVerificacion', $strCodigoVerificacion);
            $objStmt->bindParam('strCodEmpresaParam', $arrayDatosTarjeta['strCodEmpresa']);
            $objStmt->bindParam('strIpUsr', $strIpUsr);
            $objStmt->bindParam('strMsjResultadoParam', $strMsjResultado);
        
            $objStmt->execute();
           
       
        }
    }
    catch (\Exception $ex)
    {
        error_log($ex->getMessage());
       
       throw($ex);
       
    }
    

    return $strMsjResultado;
}

    
}
