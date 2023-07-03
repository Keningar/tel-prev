<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoDetalleElementoRepository extends EntityRepository
{
    /**
     * Documentación para el método 'getDetallesElementoByNombre'.
     *
     * Método utilizado para obtener una vlan libre en un Router
     *
     * @param string idElemento Id del Elemento al cual pertenece el detalle
     * @param string nombreDetalle Nombre del detalle a buscar
     *
     * @return array arrayDetalles
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
    */
    public function getDetallesElementoByNombre($idElemento,$nombreDetalle)
    {
        $arrayDetalles= array();
        
        if($idElemento>0 && $nombreDetalle)
        {
        
            $query = $this->_em->createQuery(null);

            $dql = "SELECT 
                        ide.detalleNombre,
                        ide.detalleValor
                    FROM
                        schemaBundle:InfoDetalleElemento ide
                    WHERE 
                        ide.estado = :estado 
                    AND ide.detalleNombre = :nombreDetalle
                    AND ide.elementoId = :idElemento";

            $query->setParameter('idElemento', $idElemento);
            $query->setParameter('nombreDetalle', $nombreDetalle);
            $query->setParameter('estado', "Activo");

            $query->setDQL($dql);              
            $arrayDetalles = $query->getResult();
        }
        
        return $arrayDetalles;
    }
    
    /**
     * Documentación para el método 'findVlanLibre'.
     *
     * Método utilizado para obtener una vlan libre en un Router
     *
     * @param string idElemento Id del Elemento en donde buscar una vlan libre
     * @param string numAnillo Numero del Anillo para buscar del rango requerido
     * @param string strReservaVlan Nombre de operacion para crear VLAN
     *
     * @return array objVlanLibre
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     *
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-05-18 Convetir la busqueda de las VLAN a Native Query porque los valores son
     *                         numericos pero en la base son VARCHAR, se realiza el order by para obtener
     *                         la mas pequeña
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 05-10-2017 - Se agrega bloque para consultar y reservar vlans para flujos de DC
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 27-02-2018 - Se agrega a la condicional de detalle valor un convertidor para que soporte datos 
     *                           varchar2 que generen errores en la consulta
     * 
     * @author Luis Farro <lfarro@telconet.ec>
     * @version 1.4 23-12-2022 - Se modifica la funcion para validar que las VLAN no se dupliquen.
     *                           Se agrega validacion para validar VLAN de UIO y GYE.
     * 
     * * @author Josue Valencia <ajvalencia@telconet.ec>
     * @version 1.5 25-01-2023 - Se agregar Filtro para que consulte VLAN en el rango aprobado para
     *                           el producto CLEAR CHANNEL PUNTO A PUNTO.
    */
    public function findVlanLibre($intIdElemento,$strNumAnillo,$strTipoVlan = '',$intIdEmpresa = '', $strReservaVlan = '')
    {
        $objVlanLibre    = array();
        $min             = 0;
        $max             = 0;
        
        if(empty($strTipoVlan))
        {
            $arrayParametros = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                         ->getOne("ANILLOS_MPLS", 
                                                 "TECNICO", 
                                                 "L3MPLS", 
                                                 $strNumAnillo, 
                                                 "", 
                                                 "", 
                                                 "", 
                                                 ""
                                               );

            if($arrayParametros)
            {
                $min = $arrayParametros['valor1'];
                $max = $arrayParametros['valor2'];
            }
        }
        else
        {
            if ($strReservaVlan == 'VLAN_CCPP')
            {
                $arrayRangosPermitidos =  $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('RANGO_VLANS_CH',
                                                        'TECNICO',
                                                        null,
                                                        'RANGO_VLANS_CLEAR_CHANNEL',
                                                        null,
                                                        null,
                                                        null,
                                                        null,
                                                        null, 
                                                        $intIdEmpresa);
            }
            else
            {
                $arrayRangosPermitidos =  $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('RANGO_VLANS_DC', 
                                                        'TECNICO', 
                                                        '',
                                                        '',
                                                        $strTipoVlan,
                                                        '',
                                                        '',
                                                        '', 
                                                        '', 
                                                        $intIdEmpresa);
            }
            
            if(!empty($arrayRangosPermitidos))
            {
                $min = $arrayRangosPermitidos['valor2'];
                $max = $arrayRangosPermitidos['valor3'];
            }
        }
        
        if($intIdElemento>0)
        {
            $rsm = new ResultSetMappingBuilder($this->_em);	      
            $query = $this->_em->createNativeQuery(null, $rsm);
            $arrayParametros= null;
            
            if ($strReservaVlan == 'OP_VLAN')
            {
             
                $strCondicion= " IN (SELECT VALOR2 FROM ADMI_PARAMETRO_DET APD
                INNER JOIN ADMI_PARAMETRO_CAB APC ON APD.PARAMETRO_ID= APC.ID_PARAMETRO
                 WHERE APC.NOMBRE_PARAMETRO='PE_DATACENTER' AND APC.ESTADO='Activo') ";
                
            }
            else
            {
                $strCondicion="= ?";
            }
            
            $Sql = "SELECT *
                        FROM
                          (SELECT ID_DETALLE_ELEMENTO,
                            ELEMENTO_ID,
                            DETALLE_NOMBRE,
                            DETALLE_VALOR,
                            DETALLE_DESCRIPCION,
                            ESTADO
                          FROM INFO_DETALLE_ELEMENTO
                          WHERE ESTADO                  = ?
                          AND DETALLE_NOMBRE            = ?
                        AND ELEMENTO_ID                  ".$strCondicion."   
                          AND (COALESCE(TO_NUMBER(REGEXP_SUBSTR(DETALLE_VALOR,'^\d+')),0)) >= ?
                          AND (COALESCE(TO_NUMBER(REGEXP_SUBSTR(DETALLE_VALOR,'^\d+')),0)) <= ?
                          ORDER BY DETALLE_VALOR
                          )
                        WHERE ROWNUM = 1";

            $rsm->addEntityResult('telconet\schemaBundle\Entity\InfoDetalleElemento', 'de');
            $rsm->addFieldResult('de', 'ID_DETALLE_ELEMENTO', 'id');
            $rsm->addFieldResult('de', 'ELEMENTO_ID', 'elementoId');
            $rsm->addFieldResult('de', 'DETALLE_NOMBRE', 'detalleNombre');
            $rsm->addFieldResult('de', 'DETALLE_VALOR', 'detalleValor');
            $rsm->addFieldResult('de', 'DETALLE_DESCRIPCION', 'detalleDescripcion');
            $rsm->addFieldResult('de', 'ESTADO', 'estado');
            
            $query->setParameter(1, 'Activo');
            $query->setParameter(2, 'VLAN');

            if ($strReservaVlan == 'OP_VLAN')
            {
                $query->setParameter(3, $min);
                $query->setParameter(4, $max); 
            }
            else
            {
              $query->setParameter(3, $intIdElemento); 
              $query->setParameter(4, $min);
              $query->setParameter(5, $max);
            }
            
            $query->setSQL($Sql);
            $detalles = $query->getResult();
            if(count($detalles) != 0)
            {
                $objVlanLibre = $detalles[0];
            }
        }
        
        return $objVlanLibre;
    }

    /**
     * Documentación para el método 'findVlanLibreDC'.
     *
     * Método utilizado para obtener una vlan libre en un Router
     *
     * @author Josué Valencia <ajvalencia@telconet.ec>
     * @version 1.0 25-04-2024 - Adaptar el query de la extracción de VLAN automática evitando la duplicidad
     *                           para los productos de DC.
    */
    public function findVlanLibreDC($intIdElemento,$strNumAnillo,$strTipoVlan = '',$intIdEmpresa = '', $strReservaVlan = '')
    {
        $objVlanLibre    = array();
        $intMin          = 0;
        $intMax          = 0;
        
        if(empty($strTipoVlan))
        {
            $arrayParametros = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                         ->getOne("ANILLOS_MPLS", 
                                                 "TECNICO", 
                                                 "L3MPLS", 
                                                 $strNumAnillo, 
                                                 "", 
                                                 "", 
                                                 "", 
                                                 ""
                                               );

            if($arrayParametros)
            {
                $intMin = $arrayParametros['valor1'];
                $intMax = $arrayParametros['valor2'];
            }
        }
        else
        {
            
            $arrayRangosPermitidos =  $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('RANGO_VLANS_DC', 
                                                    'TECNICO', 
                                                    '',
                                                    '',
                                                    $strTipoVlan,
                                                    '',
                                                    '',
                                                    '', 
                                                    '', 
                                                    $intIdEmpresa);
            
            
            if(!empty($arrayRangosPermitidos))
            {
                $intMin = $arrayRangosPermitidos['valor2'];
                $intMax = $arrayRangosPermitidos['valor3'];
            }
        }
        
        if($intIdElemento>0)
        {
            $objRsm = new ResultSetMappingBuilder($this->_em);	      
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
            $arrayParametros= null;
            
            if ($strReservaVlan == 'OP_VLAN')
            {
             
                $strSql= "SELECT *
                FROM
                  (SELECT IDE.ID_DETALLE_ELEMENTO,
                    IDE.ELEMENTO_ID,
                    IDE.DETALLE_NOMBRE,
                    IDE.DETALLE_VALOR,
                    IDE.DETALLE_DESCRIPCION,
                    IDE.ESTADO
                  FROM DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO IDE
                  INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET APD
                  ON IDE.ELEMENTO_ID = APD.VALOR2
                  INNER JOIN DB_GENERAL.ADMI_PARAMETRO_CAB APC
                  ON APD.PARAMETRO_ID      = APC.ID_PARAMETRO
                  INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO IDET
                  ON IDET.DETALLE_VALOR = IDE.DETALLE_VALOR AND IDET.ELEMENTO_ID IN APD.VALOR2
                  WHERE IDE.ESTADO         = :strEstadoActivo
                  AND IDE.DETALLE_NOMBRE   = :strVlan
                  AND APC.NOMBRE_PARAMETRO = :strParametro
                  AND APC.ESTADO           = :strEstadoActivo
                  AND (SELECT COUNT(*) FROM ADMI_PARAMETRO_DET APDET
                            INNER JOIN ADMI_PARAMETRO_CAB APCAB ON APDET.PARAMETRO_ID= APCAB.ID_PARAMETRO
                            WHERE APCAB.NOMBRE_PARAMETRO = :strParametro AND APCAB.ESTADO = :strEstadoActivo) 
                      = (SELECT COUNT(*) AS CANTIDAD FROM DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO IDET WHERE IDET.ELEMENTO_ID  
                           IN (SELECT VALOR2 FROM ADMI_PARAMETRO_DET APDT
                            INNER JOIN ADMI_PARAMETRO_CAB APCA ON APDT.PARAMETRO_ID= APCA.ID_PARAMETRO
                            WHERE APCA.NOMBRE_PARAMETRO = :strParametro AND APCA.ESTADO = :strEstadoActivo) 
                  AND IDET.DETALLE_VALOR = IDE.DETALLE_VALOR  AND IDET.ESTADO =  :strEstadoActivo GROUP BY IDET.DETALLE_VALOR )
                  AND COALESCE(TO_NUMBER(REGEXP_SUBSTR(IDE.DETALLE_VALOR, '^\d+')), 0) BETWEEN :intMinValor AND :intMaxValor 
                  ORDER BY IDE.DETALLE_VALOR
                  )
                WHERE ROWNUM = 1";
                
            
                $objRsm->addScalarResult('ID_DETALLE_ELEMENTO', 'intIdDetalleElemento'    , 'integer');
                $objRsm->addScalarResult('ELEMENTO_ID', 'intElementoId'    , 'integer');
                $objRsm->addScalarResult('DETALLE_NOMBRE', 'strDetalleNombre'    , 'string');
                $objRsm->addScalarResult('DETALLE_VALOR','strDetalleValor', 'string');
                $objRsm->addScalarResult('DETALLE_DESCRIPCION', 'strDetalleDescripcion'    , 'string');
                $objRsm->addScalarResult('ESTADO','strEstado', 'string');

                $objQuery->setParameter('strEstadoActivo', 'Activo');
                $objQuery->setParameter('strVlan', 'VLAN');
                $objQuery->setParameter('strParametro', 'PE_DATACENTER');
                $objQuery->setParameter('intMinValor', $intMin);
                $objQuery->setParameter('intMaxValor', $intMax);
                
                $objQuery->setSQL($strSql);
                $arrayDetallesELemento = $objQuery->getArrayResult();
                if(count($arrayDetallesELemento) != 0)
                {
                    $objVlanLibre = $arrayDetallesELemento[0];
                }
            }
        }
        
        return $objVlanLibre;
    }

    /**
     * Documentación para el función 'getVlanLibreGpon'.
     *
     * Función utilizado para obtener una vlan libre para el tipo de red Gpon.
     *
     * @param array $arrayParametros[
     *                               'intIdElemento' => id elemento del Olt.
     *                               'strCodEmpresa' => código de la empresa.
     *                              ]
     * @return array objVlanLibre
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 20-05-2021
     */
    public function getVlanLibreGpon($arrayParametros)
    {
        $intIdElemento   = $arrayParametros['intIdElemento'];
        $strCodEmpresa   = $arrayParametros['strCodEmpresa'];
        $arrayResultado  = array();
        try
        {
            if(!empty($intIdElemento))
            {
                $arrayParametros = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('NUEVA_RED_GPON_TN',
                                                            'COMERCIAL',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            'CATALOGO_VLANS_DATOS',
                                                            $strCodEmpresa);
                if(isset($arrayParametros) && !empty($arrayParametros) && is_array($arrayParametros))
                {
                    $strMinimo = $arrayParametros[0]['valor1'];
                    $strMaximo = $arrayParametros[0]['valor2'];
                    $objRsm    = new ResultSetMappingBuilder($this->_em);
                    $objQuery  = $this->_em->createNativeQuery(null, $objRsm);

                    $strSql = "SELECT *
                                FROM
                                  (SELECT ID_DETALLE_ELEMENTO,
                                    ELEMENTO_ID,
                                    DETALLE_NOMBRE,
                                    DETALLE_VALOR,
                                    DETALLE_DESCRIPCION,
                                    ESTADO
                                  FROM INFO_DETALLE_ELEMENTO
                                  WHERE ESTADO                  = ?
                                  AND DETALLE_NOMBRE            = ?
                                  AND ELEMENTO_ID               = ?
                                  AND (COALESCE(TO_NUMBER(REGEXP_SUBSTR(DETALLE_VALOR,'^\d+')),0)) >= ?
                                  AND (COALESCE(TO_NUMBER(REGEXP_SUBSTR(DETALLE_VALOR,'^\d+')),0)) <= ?
                                  ORDER BY DETALLE_VALOR
                                  )
                                WHERE ROWNUM = 1";

                    $objRsm->addEntityResult('telconet\schemaBundle\Entity\InfoDetalleElemento', 'de');
                    $objRsm->addFieldResult('de', 'ID_DETALLE_ELEMENTO', 'id');
                    $objRsm->addFieldResult('de', 'ELEMENTO_ID', 'elementoId');
                    $objRsm->addFieldResult('de', 'DETALLE_NOMBRE', 'detalleNombre');
                    $objRsm->addFieldResult('de', 'DETALLE_VALOR', 'detalleValor');
                    $objRsm->addFieldResult('de', 'DETALLE_DESCRIPCION', 'detalleDescripcion');
                    $objRsm->addFieldResult('de', 'ESTADO', 'estado');

                    $objQuery->setParameter(1, 'Activo');
                    $objQuery->setParameter(2, 'VLAN GPON');
                    $objQuery->setParameter(3, $intIdElemento);
                    $objQuery->setParameter(4, $strMinimo);
                    $objQuery->setParameter(5, $strMaximo);

                    $objQuery->setSQL($strSql);
                    $arrayResultadoTemp = $objQuery->getResult();
                    if(!empty($arrayResultadoTemp) && is_array($arrayResultadoTemp))
                    {
                        $arrayResultado['resultado'] = $arrayResultadoTemp[0];
                    }
                }
            }
            else
            {
                throw new \Exception("Los parámetros proporcionados para la búsqueda de vlans disponibles del olt no son válidos.");
            }
        }
        catch(\Exception $ex)
        {
            $arrayResultado['error'] = $ex->getMessage();
        }
        return $arrayResultado;
    }
     /**
     * Obtiene el ultimo registro ingresado segun los filtros indicados
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 27-11-2018
     * 
     * @param array $arrayParametro[strDetalleNombre, intElemento, strEstado]
     * 
     * @return array $arrayDatos
     **/
    
    public function getUltimoDetalleElemento($arrayParametro)
    {
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);
        
        $strDetalle     = $arrayParametro['strDetalleNombre'];
        $intElemento    = $arrayParametro['intElemento'];
        $strEstado      = $arrayParametro['strEstado'];


        $strSql = " SELECT DETALLE_VALOR
                    FROM INFO_DETALLE_ELEMENTO
                    WHERE ID_DETALLE_ELEMENTO =
                      (SELECT MAX(ID_DETALLE_ELEMENTO)
                      FROM info_detalle_elemento
                      WHERE elemento_id  = :elementoId
                      AND DETALLE_NOMBRE = :detalleNombre
                      AND estado         = :estado) ";

        $objQuery->setParameter("estado", $strEstado);
        $objQuery->setParameter("detalleNombre", $strDetalle);
        $objQuery->setParameter("elementoId", $intElemento);

        $objRsm->addScalarResult('DETALLE_VALOR', 'detalleValor', 'string');
        
        $objQuery->setSQL($strSql);
        $arrayDatos = $objQuery->getScalarResult();
        
        return $arrayDatos;
    }    

 

    public function getJsonIpsPorElemento($idElemento)
    {
        $encontrados = $this->getIpsPorElemento($idElemento);
        $total = count($encontrados);

        $registrosArray = array();
        if($encontrados)
        {
            foreach($encontrados as $registro)
            {
                $registrosArray[] = array('idDetalleElemento' => $registro['idDetalleElemento'],
                                          'idIp'              => $registro['idIp'],
                                          'ip'                => $registro['ip'],
                                          'vlan'              => $registro['vlan'],
                                          'estado'            => $registro['estado']);
            }
        }
        if($registrosArray)
        {
            $data = '{"total":"' . $total . '","encontrados":' . json_encode($registrosArray) . '}';
        }
        else
        {
            $data = '{"total":"0","encontrados":[]}';
        }

        return $data;
    }
    
    
    public function getIpsPorElemento($idElemento)
    {
        
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);

        $sql = "SELECT DE.ID_DETALLE_ELEMENTO,
                I.ID_IP,
                I.IP,
                DE.ESTADO,
                (select DE1.DETALLE_VALOR
                from INFO_DETALLE_ELEMENTO DE1
                where DE1.DETALLE_NOMBRE    = :detalleVlan
                and DE1.ESTADO              = :estado
                and REF_DETALLE_ELEMENTO_ID = DE.ID_DETALLE_ELEMENTO) VLAN
              from INFO_DETALLE_ELEMENTO DE,
                INFO_IP I
              where I.ID_IP         = DE.DETALLE_VALOR
              and DE.ELEMENTO_ID    = :elementoId
              and DE.DETALLE_NOMBRE = :detalleIp
              AND DE.ESTADO = :estado
              AND I.ESTADO = :estado ";

        $query->setParameter("estado", 'Activo');
        $query->setParameter("detalleIp", 'IP');
        $query->setParameter("detalleVlan", 'VLAN');
        $query->setParameter("elementoId", $idElemento);
        
        $rsm->addScalarResult(strtoupper('ID_DETALLE_ELEMENTO'), 'idDetalleElemento', 'string');
        $rsm->addScalarResult(strtoupper('ID_IP'), 'idIp', 'string');
        $rsm->addScalarResult(strtoupper('IP'), 'ip', 'string');
        $rsm->addScalarResult(strtoupper('VLAN'), 'vlan', 'string');
        $rsm->addScalarResult(strtoupper('ESTADO'), 'estado', 'string');

        $query->setSQL($sql);

        $encontrados = $query->getResult();
        
        return $encontrados;        
    }
    
    public function getPerfilIpFijaByElementoIdAndPerfilPlan($id_elemento, $tipoNegocio, $perfil) {
            $olt = $this->_em->getRepository('schemaBundle:InfoElemento')->find($id_elemento);
            $detalleElementoPerfil = $this->_em->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy(array("elementoId" => $id_elemento, "detalleNombre" => "PERFIL", "detalleValor" => $perfil, "parent" => null));

            if ($detalleElementoPerfil) 
            {
                $query = $this->_em->createQuery("select p from schemaBundle:InfoDetalleElemento p
                                                  where p.parent = :detalleElementoIdPerfil 
                                                  and p.detalleNombre = :cadenaPool
                                                  and p.feCreacion = (select max(x.feCreacion) from schemaBundle:InfoDetalleElemento x
                                                                      where x.parent =  :detalleElementoIdPerfil2
                                                                      and x.detalleNombre = :cadenaPool2)");
        
                $query->setParameter("cadenaPool", 'POOL IP FIJA');
                $query->setParameter("detalleElementoIdPerfil",  $detalleElementoPerfil->getId() );
                $query->setParameter("cadenaPool2", 'POOL IP FIJA');
                $query->setParameter("detalleElementoIdPerfil2",  $detalleElementoPerfil->getId() );
                
                try {
                    $infoDetElems = $query->getOneOrNullResult();

                    if ($infoDetElems)
                        return $infoDetElems;
                    else {

                        return "Error: </b><br>No existen Perfiles para Pool de Ips. <br>Favor solicitar a Sistemas la actualizacion para:<br><b>Olt:    </b>" . $olt->getNombreElemento();
                    }
                } catch (\Exception $e) {
                    return 'Error: ' . $e->getMessage();
                }
            } 
            else 
            {
                return "Error: </b><br>No existen Perfiles en el Olt. <br>Favor solicitar a Sistemas la actualizacion para:<br><b>Olt:    </b>" . $olt->getNombreElemento();
            }
    }
	
	public function getPoolIpsByElementoANdTipoNegocioAndPerfilIpfija($id_elemento,$tipoNegocio,$perfilIpFija,$perfil){
		$perfilesIn = "";
		
		$query = $this->_em->createQuery("select p from schemaBundle:InfoDetalleElemento p
                    where p.elementoId = $id_elemento
                    and p.detalleValor = '".$perfilIpFija->getDetalleValor()."'
                    and p.detalleNombre = 'POOL IP FIJA'");
		
		$perfilesIpFija = $query->getResult();
		
		foreach($perfilesIpFija as $perfilIpFija){
			$perfilesIn = $perfilesIn."'".$perfilIpFija->getParent()->getDetalleValor()."',";
		}
		
		if($perfilesIn){
			$perfilesIn = substr($perfilesIn,0,strlen($perfilesIn)-1);
		}
	
		$query = "select a from schemaBundle:InfoDetalleElemento a, schemaBundle:InfoDetalleElemento b
                    where a.elementoId = $id_elemento
                    and a.parent = b.parent
                    and a.detalleNombre = 'PLAN' AND a.detalleValor = '".$tipoNegocio."'
                    and b.detalleNombre = 'PERFIL' 
                    and b.detalleValor in ($perfilesIn)";
		
		$infoDetElemHQuery = $this->_em->createQuery($query);
		
        $infoDetElems = $infoDetElemHQuery->getResult();	
        
        if($infoDetElems)
			return $infoDetElems;
		else{
			$olt = $this->_em->getRepository('schemaBundle:InfoElemento')->find($id_elemento);
			return "Error: No existen Pools libres para Ip Fija. <br>Favor solicitar a GEPON ingresar para los siguientes datos:<br><b>Olt:    </b>".$olt->getNombreElemento()."<br><b>    Paquete:</b>".$tipoNegocio."<br><b>Perfil:    </b>".$perfil."<br><b>Pool:    </b>".$perfilIpFija->getDetalleValor();
		}
			
	}

	public function getDetallesElementoByElementoANdTipoNegocioAndPerfil($id_elemento,$tipoNegocio,$perfil){
// 		$perfil = "pyme_2:6_1";
		$sufijo = substr($perfil,strlen($perfil)-2,strlen($perfil)-1);
		
		if($sufijo=="_1"){
			$perfil2 = substr($perfil,0,strlen($perfil)-2)."_5";
			$query = "select a from schemaBundle:InfoDetalleElemento a, schemaBundle:InfoDetalleElemento b
                    where a.elementoId = $id_elemento
                    and a.parent = b.parent
                    and a.detalleNombre = 'PLAN' AND a.detalleValor = '".$tipoNegocio."'
                    and b.detalleNombre = 'PERFIL' AND ( b.detalleValor = '".$perfil."' or  b.detalleValor = '".$perfil2."')";
		}elseif($sufijo=="_5"){
			$perfil2 = substr($perfil,0,strlen($perfil)-2)."_1";
			$query = "select a from schemaBundle:InfoDetalleElemento a, schemaBundle:InfoDetalleElemento b
                    where a.elementoId = $id_elemento
                    and a.parent = b.parent
                    and a.detalleNombre = 'PLAN' AND a.detalleValor = '".$tipoNegocio."'
                    and b.detalleNombre = 'PERFIL' AND ( b.detalleValor = '".$perfil."' or  b.detalleValor = '".$perfil2."')";
		}else{
			$query = "select a from schemaBundle:InfoDetalleElemento a, schemaBundle:InfoDetalleElemento b
                    where a.elementoId = $id_elemento
                    and a.parent = b.parent
                    and a.detalleNombre = 'PLAN' AND a.detalleValor = '".$tipoNegocio."'
                    and b.detalleNombre = 'PERFIL' AND b.detalleValor = '".$perfil."'";
		}
		
		$infoDetElemHQuery = $this->_em->createQuery($query);
        $infoDetElems = $infoDetElemHQuery->getResult();	
        
        if($infoDetElems)
			return $infoDetElems;
		else{
			$olt = $this->_em->getRepository('schemaBundle:InfoElemento')->find($id_elemento);
			return "Error: <b>".$olt->getNombreElemento()."</b><br>No existe Pool de Ip para los datos del cliente. <br>Favor solicitar a GEPON el ingreso de un Pool de Ips con los siguientes datos:<br><b>".$tipoNegocio."</b><br><b>".$perfil."</b>";
		}
			
	}
	
	public function tieneControlIpFIja($perfil){
		// Ej: pyme_2:6_1
		$result = false;
		
		$sufijo = substr($perfil,strlen($perfil)-2,strlen($perfil)-1);
		
		if($sufijo=="_1"){
			$result = true;
		}
		
		return $result;
	
	}
    
    /**
    * getSubredFromDetalleElemento, Obtiene la subred desde la infodetalleelemento
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 08-03-2015
    * @return json retorna la consulta de los scopes
    */
    public function getSubredFromDetalleElemento($arrayParametros)
    {
        $arrayResult['strMensajeError'] = '';
        try
        {
            $objQueryCount = $this->_em->createQuery();
            $strQueryCount = "SELECT count(isd.id) ";
            $objQuery = $this->_em->createQuery();
            $strQuery = "SELECT isd ";
            $strFromQuery = "FROM schemaBundle:InfoSubred isd, "
                . "schemaBundle:InfoDetalleElemento ide "
                . "WHERE ide.elementoId = :intIdElemento "
                . "AND ide.detalleNombre IN (:arrayDetalleNombre) "
                . "AND ide.detalleValor = isd.id";
            $objQuery->setParameter('intIdElemento', $arrayParametros['intIdElemento']);
            $objQuery->setParameter('arrayDetalleNombre', $arrayParametros['arrayDetalleNombre']);
            $objQuery->setDQL($strQuery . $strFromQuery);
            $arrayResult['arrayResultado'] = $objQuery->setFirstResult($arrayParametros['intStart'])->setMaxResults($arrayParametros['intLimit'])->getResult();
            if(!empty($arrayResult['arrayResultado']))
            {
                $strQueryCount = $strQueryCount . $strFromQuery;
                $objQueryCount->setParameter('intIdElemento', $arrayParametros['intIdElemento']);
                $objQueryCount->setParameter('arrayDetalleNombre', $arrayParametros['arrayDetalleNombre']);
                $objQueryCount->setDQL($strQueryCount);
                $arrayResult['intTotal'] = $objQueryCount->getSingleScalarResult();
            }
        }
        catch(\Exception $ex)
        {
            $arrayResult['strMensajeError'] = 'Existion un error en getSubredFromDetalleElemento - ' . $ex->getMessage();
        }

        return $arrayResult;
    }//getSubredFromDetalleElemento


    /**
     * findScopesByNombreElemento, Obtiene los scopes por nombre o por elemento.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 02-09-2015
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 06-06-2016 Se modifica la validacion del arrayResultado
     * @since 1.0
     *  
     * @param   array $arrayParametros['intIdElemento'      => Id del elemento,
     *                                 'strNombreScope'     => Nombre de Scope,
     *                                 'arrayDetalleNombre' => Nombre en la InfoDetalleElemento,
     *                                 'intStart'           => Inicio del Rownum,
     *                                 'intLimit'           => Fin del Rownum]
     * @return array $arrayResult[arrayResultado    => Retorna en un array los datos obtenidos de la consulta
     *                            intTotal          => Retorna el total de los registros obtenidos por la consulta
     *                            strMensajeError   => Retorna el mensaje de error
     *                           ]
     */
    public function findScopesByNombreElemento($arrayParametros)
    {
        $arrayResult['strMensajeError'] = '';
        $arrayResult['arrayResultado']  = '';
        $arrayResult['intTotal']        = '';
        try
        {
            $objQueryCount = $this->_em->createQuery();
            $strQueryCount = "SELECT count(ide.id) ";
            $objQuery = $this->_em->createQuery();
            $strQuery = "SELECT idesr.detalleNombre strDetalleNombre, "
                                . "isr.estado strEstado, "
                                . "isr.subred strSubred, "
                                . "isr.mascara strMascara, "
                                . "isr.ipInicial strIpInicial, "
                                . "isr.ipFinal strIpFinal, "
                                . "(SELECT ap.nombrePolicy from schemaBundle:AdmiPolicy ap where ap.id = isr.notificacion) intNotificacion, "
                                . "ide.detalleValor strDetalleValor, "
                                . "isr.id intIdSubred ";
            $strFromQuery = "FROM schemaBundle:InfoDetalleElemento ide "
                . " LEFT JOIN schemaBundle:InfoDetalleElemento idesr WITH ide.parent = idesr.id "
                . " LEFT JOIN schemaBundle:InfoSubred isr WITH isr.id = idesr.detalleValor "
                . "WHERE ide.detalleNombre IN (:arrayDetalleNombre) ";

            //Pregunta si $arrayParametros['intIdElemento'] es diferente de vacio
            if(!empty($arrayParametros['intIdElemento']))
            {
                $strFromQuery .= " AND ide.elementoId = :intIdElemento";
                $objQuery->setParameter('intIdElemento', $arrayParametros['intIdElemento']);
            }

            //Pregunta si $arrayParametros['strEstado'] es diferente de vacio
            if(!empty($arrayParametros['strEstado']))
            {
                $strFromQuery .= " AND isr.estado = :strEstado";
                $objQuery->setParameter('strEstado', $arrayParametros['strEstado']);
            }

            //Pregunta si $arrayParametros['strNombreScope'] es diferente de vacio
            if(!empty($arrayParametros['strNombreScope']))
            {
                $strFromQuery .= " AND ide.detalleValor LIKE :strNombreScope";
                $objQuery->setParameter('strNombreScope', '%'.$arrayParametros['strNombreScope'].'%');
            }

            $objQuery->setParameter('arrayDetalleNombre', $arrayParametros['arrayDetalleNombre']);
            $objQuery->setDQL($strQuery . $strFromQuery);

            //Pregunta si $arrayParametros['intStart'] es diferente de vacio
            if(!empty($arrayParametros['intStart']))
            {
                $objQuery->setFirstResult($arrayParametros['intStart']);
            }

            //Pregunta si $arrayParametros['intStart'] es diferente de vacio
            if(!empty($arrayParametros['intLimit']))
            {
                $objQuery->setMaxResults($arrayParametros['intLimit']);
            }

            $arrayResult['arrayResultado'] = $objQuery->getResult();

            //Pregunta si $arrayResult['arrayResultado'] es diferente de vacio para realizar el count
            if($arrayResult['arrayResultado'])
            {
                $strQueryCount = $strQueryCount . $strFromQuery;
                $objQueryCount->setParameter('arrayDetalleNombre', $arrayParametros['arrayDetalleNombre']);

                //Pregunta si $arrayParametros['intIdElemento'] es diferente de vacio
                if(!empty($arrayParametros['intIdElemento']))
                {
                    $objQueryCount->setParameter('intIdElemento', $arrayParametros['intIdElemento']);
                }

                //Pregunta si $arrayParametros['strEstado'] es diferente de vacio
                if(!empty($arrayParametros['strEstado']))
                {
                    $objQueryCount->setParameter('strEstado', $arrayParametros['strEstado']);
                }

                //Pregunta si $arrayParametros['strNombreScope'] es diferente de vacio
                if(!empty($arrayParametros['strNombreScope']))
                {
                    $objQueryCount->setParameter('strNombreScope', '%'.$arrayParametros['strNombreScope'].'%');
                }
                $objQueryCount->setDQL($strQueryCount);
                $arrayResult['intTotal'] = $objQueryCount->getSingleScalarResult();
            }
        }
        catch(\Exception $ex)
        {
            $arrayResult['strMensajeError'] = 'Existio un error en findScopesByNombreElemento - ' . $ex->getMessage();
        }
        return $arrayResult;
    }//findScopesByNombreElemento
    
    /**
     * getResultadoTiposDetalleElemento, Obtiene los Tipos de detalles por OLT
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 19-01-2016
     * 
     * @param   array $arrayParametros['intIdElemento'      => Id del elemento,
     *                                 'strTipoDetalle'     => tipo de detalle]
     * @return array $arrayResult[arrayResultado    => Retorna en un array los datos obtenidos de la consulta
     *                            total             => Retorna el total de los registros obtenidos por la consulta
     *                            strMensajeError   => Retorna el mensaje de error
     *                           ]
     */
    public function getResultadoTiposDetalleElemento($arrayParametros)
    {
        $arrayResult['strMensajeError'] = '';
        $arrayResult['arrayResultado']  = '';
        try
        {
            $objQuery = $this->_em->createQuery();
            $strQuery = "SELECT DISTINCT detEle.detalleNombre AS DETALLE_ELEMENTO ";
            $strFromQuery = " FROM schemaBundle:InfoDetalleElemento detEle where detEle.elementoId = :idOltParam ";
            $objQuery->setParameter('idOltParam', $arrayParametros['intIdElemento']);
            //Pregunta si $arrayParametros['strTipoDetalle'] es diferente de vacio
            if(!empty($arrayParametros['strTipoDetalle']))
            {
                $strFromQuery .= " AND UPPER(detEle.detalleNombre) LIKE :strTipoDetalleParam";
                $objQuery->setParameter('strTipoDetalleParam', '%'.upper($arrayParametros['strTipoDetalle']).'%');
            }
            $objQuery->setDQL($strQuery . $strFromQuery);
            $arrayResult['arrayResultado'] = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            $arrayResult['strMensajeError'] = 'Existio un error en getResultadoTiposDetalleElemento - ' . $ex->getMessage();
        }
        return $arrayResult;
    }//getResultadoTiposDetalleElemento
    
    /**
     * getResultadoDetallesElemento, Obtiene los Tipos de detalles por OLT
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 19-01-2016
     * 
     * @param   array $arrayParametros['intIdElemento'      => Id del elemento,
     *                                 'strTipoDetalle'     => tipo de detalle]
     * @return array $arrayResult[arrayResultado    => Retorna en un array los datos obtenidos de la consulta
     *                            total             => Retorna el total de los registros obtenidos por la consulta
     *                            strMensajeError   => Retorna el mensaje de error
     *                           ]
     */
    public function getResultadoDetallesElemento($arrayParametros)
    {
        $arrayResult['strMensajeError'] = '';
        $arrayResult['arrayResultado']  = '';
        try
        {
            $objQuery  = $this->_em->createQuery();
            $strQuery  = "SELECT detEle.detalleNombre AS DETALLE_ELEMENTO, detEle.detalleValor AS DETALLE_VALOR, ";
            $strQuery .= "detEle.detalleDescripcion AS DETALLE_DESCRIPCION, detEle.feCreacion AS DETALLE_FECREACION, ";
            $strQuery .= "detEle.usrCreacion AS DETALLE_USRCREACION ";
            $strFromQuery = " FROM schemaBundle:InfoDetalleElemento detEle where detEle.elementoId = :idOltParam ";
            $objQuery->setParameter('idOltParam', $arrayParametros['intIdElemento']);
            //Pregunta si $arrayParametros['strTipoDetalle'] es diferente de vacio
            if(!empty($arrayParametros['strNombreDetalle']))
            {
                $strFromQuery .= " AND UPPER(detEle.detalleNombre) = :strNombreDetalleParam";
                $objQuery->setParameter('strNombreDetalleParam', strtoupper($arrayParametros['strNombreDetalle']));
            }
            //Pregunta si $arrayParametros['strTipoDetalle'] es diferente de vacio
            if(!empty($arrayParametros['strValorDetalle']))
            {
                 $strFromQuery .= " AND UPPER(detEle.detalleValor) LIKE :strValorDetalleParam";
                $objQuery->setParameter('strValorDetalleParam', '%'.strtoupper($arrayParametros['strValorDetalle']).'%');
            }
            
            //Pregunta si $arrayParametros['strTipoDetalle'] es diferente de vacio
            if(!empty($arrayParametros['strDescripcionDetalle']))
            {
                 $strFromQuery .= " AND UPPER(detEle.detalleDescripcion) LIKE :strDescripcionDetalleParam";
                $objQuery->setParameter('strDescripcionDetalleParam', '%'.strtoupper($arrayParametros['strDescripcionDetalle']).'%');
            }
            
            //Pregunta si $arrayParametros['strTipoDetalle'] es diferente de vacio
            if(!empty($arrayParametros['strFechaDesde'][0]))
            {
                $dateFD = explode("-",$arrayParametros['strFechaDesde'][0]);
                $fechaSqlDesde = date("Y/m/d H:i:s", strtotime($dateFD[2]."-".$dateFD[1]."-".$dateFD[0]." 00:00:00"));
                $strFromQuery .= " AND (detEle.feCreacion) >= :strFechaDesdeParam";
                $objQuery->setParameter('strFechaDesdeParam', trim($fechaSqlDesde));
            }
            
            //Pregunta si $arrayParametros['strTipoDetalle'] es diferente de vacio
            if(!empty($arrayParametros['strFechaHasta'][0]))
            {
                $dateFH = explode("-",$arrayParametros['strFechaHasta'][0]);
                $fechaSqlHasta = date("Y/m/d H:i:s", strtotime($dateFH[2]."-".$dateFH[1]."-".$dateFH[0]." 23:59:59"));
                $strFromQuery .= " AND (detEle.feCreacion) <= :strFechaHastaParam";
                $objQuery->setParameter('strFechaHastaParam', trim($fechaSqlHasta));
            }
            
            $objQuery->setDQL($strQuery . $strFromQuery);
            $arrayResult['arrayResultado'] = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            $arrayResult['strMensajeError'] = 'Existio un error en getResultadoTiposDetalleElemento - ' . $ex->getMessage();
        }
        return $arrayResult;
    }//getResultadoDetallesElemento
    
    
    /**
     * findDetalleScope, Obtiene el detalle de los scopes
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 08-10-2015
     * 
     * @param   array $arrayRequest['intIdSubred'            => Id de la subred,
*                                   'arraystrDetalleNombre'  => Nombre del detalle del scope,
*                                   'arraystrDetalleNombre_' => Nombre del detalle del scope]
     * 
     * @return array $arrayResponse[strMensaje => Retorna un mensaje de la ejecución del método
     *                              strStatus  => Retorna un codigo [
     *                              001 => Existió un error
     *                              100 => Consulta con éxito
     *                              000 => No realizó la consulta
     *                              ]
     *                              arrayDatos => Retorna el array de la consulta obtenida
     *                              ]
     */
    public function findDetalleScope($arrayRequest)
    {
        $arrayResponse['strMensaje'] = 'No realizó consulta.';
        $arrayResponse['strStatus']  = '000';
        $arrayResponse['arrayDatos'] = '';
        try
        {
            $objQuery = $this->_em->createQuery();
            $strQuery = " SELECT ide_ "
                      . "FROM schemaBundle:InfoDetalleElemento ide "
                      . "LEFT JOIN schemaBundle:InfoDetalleElemento ide_ "
                        . " WITH ide_.parent = ide.id ";
            if(!empty($arrayRequest['arraystrDetalleNombre']))
            {
                $strQuery .= " AND ide_.detalleNombre IN (:arrayStrDetalleNombre_) ";
                $objQuery->setParameter('arrayStrDetalleNombre_', $arrayRequest['arrayStrDetalleNombre_']);
            }
            
            $strQuery   .= " WHERE ide.detalleNombre IN (:arrayStrDetalleNombre)"
                        .  " AND ide.detalleValor = :intIdSubred";
            $objQuery->setParameter('arrayStrDetalleNombre', $arrayRequest['arrayStrDetalleNombre']);
            $objQuery->setParameter('intIdSubred', $arrayRequest['intIdSubred']);
            
            $objQuery->setDQL($strQuery);
            $arrayResponse['arrayDatos'] = $objQuery->getResult();
            
            $arrayResponse['strMensaje'] = 'Consulta realizada con éxito.';
            $arrayResponse['strStatus']  = '100';
        }
        catch(\Exception $ex)
        {
            $arrayResponse['strMensaje'] = 'Existio un error en findDetalleScope - ' . $ex->getMessage();
            $arrayResponse['strStatus']  = '001';
        }
        return $arrayResponse;
    }//findDetalleScope
    
    
    /**
     * getCuadrillasTurnosSolapados, Consulta que el vehículo que se quiere asignar a la cuadrilla no se encuentre ocupado 
     * por otra cuadrilla en un turno que se solape.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 23-12-2015
     * 
     * @param  array $arrayParametros['strInicioCuadrilla'   => string de la fecha y hora de inicio del turno,
     *                                'strFinCuadrilla'      => string de la fecha y hora de inicio del turno,
     *                                'estado'               => Estado de INFO_DETALLE_ELEMENTO
     *                                'detalleNombre'        => nombre del detalle de INFO_DETALLE_ELEMENTO,
     *                                'elementoId'           => id de INFO_ELEMENTO, id del transporte que se quiere asignar a la cuadrilla
     *                                'idCuadrilla'          => En el caso que se quiera validar cambio de horario de una cuadrilla con vehículo
     *                                                          asignado para que no se solape con la misma cuadrilla
     *                               ]
     * 
     * @return array $arrayResultado Retorna el array obtenido de la consulta 
     */
    public function getCuadrillasTurnosSolapados($arrayParametros)
    {
        $rsm                  = new ResultSetMappingBuilder($this->_em);
        $ntvQuery             = $this->_em->createNativeQuery(null, $rsm);
        $arrayResultado       = "";
        try
        {

            $strQuery = "SELECT ac.NOMBRE_CUADRILLA, ac.TURNO_HORA_INICIO, ac.TURNO_HORA_FIN, 
                        ide_fecha_inicio.DETALLE_VALOR AS FECHA_INICIO, 
                        ide_hora_inicio.DETALLE_VALOR AS HORA_INICIO, ide_HORA_fin.DETALLE_VALOR AS HORA_FIN 
                        
                        FROM DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO ide_cuadrilla 

                        INNER JOIN DB_COMERCIAL.ADMI_CUADRILLA ac ON ac.ID_CUADRILLA = ide_cuadrilla.DETALLE_VALOR 

                        INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO ide_fecha_inicio 
                             ON ide_cuadrilla.ID_DETALLE_ELEMENTO = ide_fecha_inicio.REF_DETALLE_ELEMENTO_ID 
                                

                        INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO ide_hora_inicio 
                             ON ide_cuadrilla.ID_DETALLE_ELEMENTO = ide_hora_inicio.REF_DETALLE_ELEMENTO_ID 
                                 

                        INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO ide_hora_fin 
                             ON ide_cuadrilla.ID_DETALLE_ELEMENTO = ide_hora_fin.REF_DETALLE_ELEMENTO_ID 
                                 


                        WHERE ide_cuadrilla.ELEMENTO_ID= :elementoId 
                        AND ide_cuadrilla.DETALLE_NOMBRE = :detalleCuadrilla 
                        AND ide_cuadrilla.ESTADO= :estadoActivo 
                        AND ide_fecha_inicio.DETALLE_NOMBRE = :detalleFechaInicio
                        AND ide_hora_inicio.DETALLE_NOMBRE = :detalleHoraInicio 
                        AND ide_hora_fin.DETALLE_NOMBRE = :detalleHoraFin 
                        
                        AND ide_fecha_inicio.ESTADO= :estadoActivo 
                        AND
                         (
                           (
                               TO_TIMESTAMP( ide_hora_fin.DETALLE_VALOR ,'HH24:MI' ) > 
                               TO_TIMESTAMP(:strHoraDesdeNuevoTurno,'HH24:MI')
                               AND
                               TO_TIMESTAMP( ide_hora_fin.DETALLE_VALOR ,'HH24:MI' ) < 
                               TO_TIMESTAMP(:strHoraHastaNuevoTurno,'HH24:MI')
                           )

                           OR

                           (
                               TO_TIMESTAMP( ide_hora_inicio.DETALLE_VALOR ,'HH24:MI' ) < 
                               TO_TIMESTAMP(:strHoraHastaNuevoTurno,'HH24:MI')
                               AND
                               TO_TIMESTAMP( ide_hora_inicio.DETALLE_VALOR ,'HH24:MI' ) > 
                               TO_TIMESTAMP(:strHoraDesdeNuevoTurno,'HH24:MI')
                           )

                           OR
                           (
                               TO_TIMESTAMP( ide_hora_inicio.DETALLE_VALOR ,'HH24:MI' ) <= 
                               TO_TIMESTAMP(:strHoraDesdeNuevoTurno,'HH24:MI')
                               AND
                               TO_TIMESTAMP( ide_hora_fin.DETALLE_VALOR ,'HH24:MI' ) >= 
                               TO_TIMESTAMP(:strHoraHastaNuevoTurno,'HH24:MI')
                           )
                           OR
                           (
                               TO_TIMESTAMP( ide_hora_inicio.DETALLE_VALOR ,'HH24:MI' ) >= 
                               TO_TIMESTAMP(:strHoraDesdeNuevoTurno,'HH24:MI')
                               AND
                               TO_TIMESTAMP( ide_hora_fin.DETALLE_VALOR ,'HH24:MI' ) <= 
                               TO_TIMESTAMP(:strHoraHastaNuevoTurno,'HH24:MI')
                           )
                         )
                        ";

            
            
            
            
            $rsm->addScalarResult('NOMBRE_CUADRILLA',   'nombreCuadrilla',          'string');
            $rsm->addScalarResult('TURNO_HORA_INICIO',  'turnoHoraInicioCuadrilla', 'string');
            $rsm->addScalarResult('TURNO_HORA_FIN',     'turnoHoraFinCuadrilla',    'string');
            $rsm->addScalarResult('FECHA_INICIO',      'fechaInicioAsignacionVehicular','string');
            $rsm->addScalarResult('HORA_INICIO',      'horaInicioAsignacionVehicular','string');
            $rsm->addScalarResult('HORA_FIN',      'horaFinAsignacionVehicular','string');
            
 
            $ntvQuery->setParameter('estadoActivo', $arrayParametros['estadoActivo']);
            $ntvQuery->setParameter('detalleCuadrilla', $arrayParametros['detalleCuadrilla']);
            
            $ntvQuery->setParameter('detalleFechaInicio', $arrayParametros['detalleFechaInicio']);
            $ntvQuery->setParameter('detalleHoraInicio', $arrayParametros['detalleHoraInicio']);
            $ntvQuery->setParameter('detalleHoraFin', $arrayParametros['detalleHoraFin']);
            
            $ntvQuery->setParameter('elementoId', $arrayParametros['elementoId']);
            
            $ntvQuery->setParameter('strHoraDesdeNuevoTurno', $arrayParametros['strHoraDesdeNuevoTurno']);
            $ntvQuery->setParameter('strHoraHastaNuevoTurno', $arrayParametros['strHoraHastaNuevoTurno']);
            
            if(isset($arrayParametros['idCuadrilla']))
            {
                if($arrayParametros['idCuadrilla'])
                {
                    $strQuery.="AND ac.ID_CUADRILLA <> :idCuadrilla ";
                    $ntvQuery->setParameter('idCuadrilla', $arrayParametros['idCuadrilla']);
                }
            }
            $ntvQuery->setSQL($strQuery);
            $arrayResultado = $ntvQuery->getResult();

        }
        catch(\Exception $e)
        {
            
            error_log($e->getMessage());
        }

        return $arrayResultado;
    }
    

    
    
    /**************************Asignación Vehicular****************************/
    /**
     * getJSONHistorialAsignacionVehicularXCuadrilla, Obtiene el json con el historial de las asignaciones de vehículo que se han realizado a la 
     * cuadrilla 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 
     * 
     * @param  array $arrayParametros[  'intStart'                      => Inicio del Rownum,
     *                                  'intLimit'                      => Fin del Rownum,
     *                                  'strFechaDesde'                 => fecha desde de la consulta,
     *                                  'strFechaHasta'                 => fecha hasta de la consulta,
     *                                  'errorFechas'                   => valor que indica si existe un error en el rango de fechas consultadas
     *                                  'idCuadrilla'                   => id de la cuadrilla que se desea consultar el historial de asignaciones
     *                                  'strDetalleNombreCuadrilla'     => nombre del detalle del elemento que guarda el id de la cuadrilla,
     *                                  'strDetalleNombreFechaInicio'   => nombre del detalle que guarda la fecha en que se realizó la asignacion,
     *                                  'strDetalleNombreFechaFin'      => nombre del detalle que guarda la fecha en que finalizó la asignación,
     *                                  'strDetalleNombreHoraInicio'    => nombre del detalle que guarda la hora inicio de la asignación,
     *                                  'strDetalleNombreHoraFin'       => nombre del detalle que guarda la hora fin de la asignación
     * 
     *                               ]
     * 
     * @return json $jsonData
     */
    public function getJSONHistorialAsignacionVehicularXCuadrilla($arrayParametros)
    {
        $arrayEncontrados   = array();
        if($arrayParametros['errorFechas'])
        {
            $total              = 0;
        }
        else
        {
            $arrayResultado     = $this->getResultadoHistorialAsignacionVehicularXCuadrilla($arrayParametros);
            $resultado          = $arrayResultado['resultado'];
            $intTotal           = $arrayResultado['total'];
            $total              = 0;

            if($resultado)
            {
                $total = $intTotal;
                foreach($resultado as $data)
                {
                    $arrayEncontrados[] = array(
                        "strFechaInicioHisto"   => $data['fecha_inicio'],
                        "strFechaFinHisto"      => $data['fecha_fin'],
                        "strHoraInicioHisto"    => $data['hora_inicio'],
                        "strHoraFinHisto"       => $data['hora_fin'],
                        "strPlacaHisto"         => $data['nombreElemento'],
                        "strEstadoHisto"        => $data['estado']
                    );

                }

            }
        }
        

        $arrayRespuesta = array('total' => $total, 'encontrados' => $arrayEncontrados);
        $jsonData       = json_encode($arrayRespuesta);
        return $jsonData;
    }
    
    /**
     * getResultadoHistorialAsignacionVehicularXCuadrilla, Consulta el historial de las asignaciones de vehículo que se han realizado a la cuadrilla 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 
     * 
     * @param  array $arrayParametros[  'intStart'                      => Inicio del Rownum,
     *                                  'intLimit'                      => Fin del Rownum,
     *                                  'strFechaDesde'                 => fecha desde de la consulta,
     *                                  'strFechaHasta'                 => fecha hasta de la consulta,
     *                                  'errorFechas'                   => valor que indica si existe un error en el rango de fechas consultadas
     *                                  'idCuadrilla'                   => id de la cuadrilla que se desea consultar el historial de asignaciones
     *                                  'strDetalleNombreCuadrilla'     => nombre del detalle del elemento que guarda el id de la cuadrilla,
     *                                  'strDetalleNombreFechaInicio'   => nombre del detalle que guarda la fecha en que se realizó la asignacion,
     *                                  'strDetalleNombreFechaFin'      => nombre del detalle que guarda la fecha en que finalizó la asignación,
     *                                  'strDetalleNombreHoraInicio'    => nombre del detalle que guarda la hora inicio de la asignación,
     *                                  'strDetalleNombreHoraFin'       => nombre del detalle que guarda la hora fin de la asignación
     * 
     *                               ]
     * 
     * @return array $arrayRespuesta [ 'registros', 'total' ]
     */
    public function getResultadoHistorialAsignacionVehicularXCuadrilla($arrayParametros)
    {
        $arrayRespuesta['total']     = 0;
        $arrayRespuesta['resultado'] = "";
        try
        {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsmCount = new ResultSetMappingBuilder($this->_em);
            $ntvQuery = $this->_em->createNativeQuery(null, $rsm);
            $ntvQueryCount = $this->_em->createNativeQuery(null, $rsmCount);

            $strSelectCount     = " SELECT COUNT(*) AS TOTAL ";
            
            $strSelect          = " SELECT DISTINCT(de_cuadrilla.ID_DETALLE_ELEMENTO), de_fecha_inicio.DETALLE_VALOR as FECHA_INICIO,
                                    de_fecha_fin.DETALLE_VALOR as FECHA_FIN,
                                    de_hora_inicio.DETALLE_VALOR as HORA_INICIO,de_hora_fin.DETALLE_VALOR as HORA_FIN,
                                    e.ID_ELEMENTO, e.NOMBRE_ELEMENTO, 
                                    de_cuadrilla.ESTADO ";
            
            $strFromAndWhere    = " FROM DB_INFRAESTRUCTURA.INFO_ELEMENTO e  
                                    INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO de_cuadrilla 
                                        ON de_cuadrilla.ELEMENTO_ID=e.ID_ELEMENTO  
                                            AND de_cuadrilla.DETALLE_NOMBRE = :strDetalleNombreCuadrilla 
                                    INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO de_fecha_inicio 
                                        ON de_cuadrilla.ID_DETALLE_ELEMENTO=de_fecha_inicio.REF_DETALLE_ELEMENTO_ID 
                                        AND de_fecha_inicio.DETALLE_NOMBRE = :strDetalleNombreFechaInicio 
                                    LEFT JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO de_fecha_fin 
                                        ON de_cuadrilla.ID_DETALLE_ELEMENTO=de_fecha_fin.REF_DETALLE_ELEMENTO_ID 
                                        AND de_fecha_fin.DETALLE_NOMBRE = :strDetalleNombreFechaFin
                                    INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO de_hora_inicio 
                                        ON de_cuadrilla.ID_DETALLE_ELEMENTO=de_hora_inicio.REF_DETALLE_ELEMENTO_ID 
                                        AND de_hora_inicio.DETALLE_NOMBRE = :strDetalleNombreHoraInicio 
                                    INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO de_hora_fin 
                                        ON de_cuadrilla.ID_DETALLE_ELEMENTO=de_hora_fin.REF_DETALLE_ELEMENTO_ID 
                                        AND de_hora_fin.DETALLE_NOMBRE = :strDetalleNombreHoraFin 
                                    WHERE de_cuadrilla.DETALLE_VALOR = :idCuadrilla 
                                        ";
            
            
            $strOrderBy         = " ORDER BY de_cuadrilla.ID_DETALLE_ELEMENTO DESC";
            
            
            $rsm->addScalarResult('FECHA_INICIO', 'fecha_inicio', 'string');
            $rsm->addScalarResult('FECHA_FIN', 'fecha_fin', 'string');
            $rsm->addScalarResult('HORA_INICIO', 'hora_inicio', 'string');
            $rsm->addScalarResult('HORA_FIN', 'hora_fin', 'string');
            $rsm->addScalarResult('ID_ELEMENTO', 'id', 'integer');
            $rsm->addScalarResult('NOMBRE_ELEMENTO', 'nombreElemento', 'string');
            $rsm->addScalarResult('ESTADO', 'estado', 'string');

            $rsmCount->addScalarResult('TOTAL', 'total', 'integer');
            
            if(isset($arrayParametros["strFechaDesde"]) && isset($arrayParametros["strFechaHasta"]) )
            {
                if($arrayParametros["strFechaDesde"] && $arrayParametros["strFechaHasta"])
                {
                    $strFromAndWhere.=" AND 
                    (
                        (
                            de_fecha_fin.DETALLE_VALOR is NOT NULL 
                            AND
                            (
                                (
                                TO_TIMESTAMP( de_fecha_fin.DETALLE_VALOR ,'DD/MM/YYYY' ) >= TO_TIMESTAMP(:strFechaDesde,'DD/MM/YYYY' )
                                AND 
                                TO_TIMESTAMP( de_fecha_fin.DETALLE_VALOR ,'DD/MM/YYYY' )<= TO_TIMESTAMP(:strFechaHasta,'DD/MM/YYYY' )
                                )

                                OR

                                (
                                TO_TIMESTAMP( de_fecha_inicio.DETALLE_VALOR ,'DD/MM/YYYY' ) <= TO_TIMESTAMP(:strFechaHasta,'DD/MM/YYYY' )
                                AND 
                                TO_TIMESTAMP( de_fecha_inicio.DETALLE_VALOR ,'DD/MM/YYYY' ) >= TO_TIMESTAMP(:strFechaDesde,'DD/MM/YYYY' )
                                )

                                OR

                                (
                                TO_TIMESTAMP( de_fecha_inicio.DETALLE_VALOR ,'DD/MM/YYYY' ) <= TO_TIMESTAMP(:strFechaDesde,'DD/MM/YYYY' )
                                AND
                                TO_TIMESTAMP( de_fecha_fin.DETALLE_VALOR ,'DD/MM/YYYY' ) >= TO_TIMESTAMP(:strFechaHasta,'DD/MM/YYYY' )
                                )
                            )
                        )
                        OR
                        (
                            TO_TIMESTAMP( de_fecha_inicio.DETALLE_VALOR ,'DD/MM/YYYY' ) >= TO_TIMESTAMP(:strFechaDesde,'DD/MM/YYYY' )
                            AND TO_TIMESTAMP( de_fecha_inicio.DETALLE_VALOR ,'DD/MM/YYYY' ) <= TO_TIMESTAMP(:strFechaHasta,'DD/MM/YYYY' )
                            AND de_cuadrilla.ESTADO = :strEstadoActivo
                            
                        )
                    ) ";
                    
                    $ntvQuery->setParameter('strEstadoActivo', 'Activo');
                    $ntvQueryCount->setParameter('strEstadoActivo', 'Activo');
                    
                    $ntvQuery->setParameter('strFechaDesde', $arrayParametros["strFechaDesde"]);
                    $ntvQueryCount->setParameter('strFechaDesde', $arrayParametros["strFechaDesde"]);
                    
                    $ntvQuery->setParameter('strFechaHasta', $arrayParametros["strFechaHasta"]);
                    $ntvQueryCount->setParameter('strFechaHasta', $arrayParametros["strFechaHasta"]);
                }
            }
            
            $ntvQuery->setParameter('idCuadrilla', $arrayParametros['idCuadrilla']);
            $ntvQueryCount->setParameter('idCuadrilla', $arrayParametros['idCuadrilla']);
            
            $ntvQuery->setParameter('strDetalleNombreCuadrilla', $arrayParametros['strDetalleNombreCuadrilla']);
            $ntvQueryCount->setParameter('strDetalleNombreCuadrilla', $arrayParametros['strDetalleNombreCuadrilla']);

            $ntvQuery->setParameter('strDetalleNombreFechaInicio', $arrayParametros['strDetalleNombreFechaInicio']);
            $ntvQueryCount->setParameter('strDetalleNombreFechaInicio', $arrayParametros['strDetalleNombreFechaInicio']);
            
            $ntvQuery->setParameter('strDetalleNombreFechaFin', $arrayParametros['strDetalleNombreFechaFin']);
            $ntvQueryCount->setParameter('strDetalleNombreFechaFin', $arrayParametros['strDetalleNombreFechaFin']);
            
            $ntvQuery->setParameter('strDetalleNombreHoraInicio', $arrayParametros['strDetalleNombreHoraInicio']);
            $ntvQueryCount->setParameter('strDetalleNombreHoraInicio', $arrayParametros['strDetalleNombreHoraInicio']);
            
            $ntvQuery->setParameter('strDetalleNombreHoraFin', $arrayParametros['strDetalleNombreHoraFin']);
            $ntvQueryCount->setParameter('strDetalleNombreHoraFin', $arrayParametros['strDetalleNombreHoraFin']);

            
            
            $strSqlPrincipal = $strSelect . $strFromAndWhere . $strOrderBy;

            $strSqlFinal = '';

            if(isset($arrayParametros['intStart']) && isset($arrayParametros['intLimit']))
            {
                if($arrayParametros['intStart'] && $arrayParametros['intLimit'])
                {
                    $intInicio = $arrayParametros['intStart'];
                    $intFin = $arrayParametros['intStart'] + $arrayParametros['intLimit'];
                    $strSqlFinal = '  SELECT * FROM 
                                        (
                                            SELECT consultaPrincipal.*,rownum AS consultaPrincipal_rownum 
                                            FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                            WHERE rownum<=' . $intFin . '
                                        ) WHERE consultaPrincipal_rownum >' . $intInicio;
                }
                else
                {
                    $strSqlFinal = '  SELECT consultaPrincipal.* 
                                        FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                        WHERE rownum<=' . $arrayParametros['intLimit'];
                }
            }
            else
            {
                $strSqlFinal = $strSqlPrincipal;
            }

            $ntvQuery->setSQL($strSqlFinal);
            $arrayResultado = $ntvQuery->getResult();

            $strSqlCount = $strSelectCount . " FROM (" . $strSqlPrincipal . ")";
            $ntvQueryCount->setSQL($strSqlCount);

            $intTotal = $ntvQueryCount->getSingleScalarResult();

            $arrayRespuesta['resultado'] = $arrayResultado;
            $arrayRespuesta['total']     = $intTotal;

            return $arrayRespuesta;
            
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
    }
    
    /**************************Fin de Asignación Vehicular****************************/

    
    
    
    

    /**************************Inicio de Asignación Operativa*************************/
    /**
     * getJSONHistorialAsignacionVehicularXElemento, Devuelve el json con la consulta de las asignaciones vehiculares que se han realizado de
     * de determinado vehículo
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 
     * 
     * @param  array $arrayParametros[  'intStart'                      => Inicio del rownum,
     *                                  'intLimit'                      => Fin del rownum,
     *                                  'idElemento'                    => id del vehículo a consultar,
     *                                  'strFechaDesde'                 => fecha desde a consultar,
     *                                  'strFechaHasta'                 => fecha hasta a consultar,
     *                                  'errorFechas'                   => Indica si existe un error o no en el rango de fechas a buscar
     *                                  
     *                                  'arrayDetallesVehicular'     => array con los nombres de los detalles de las fechas y horas de la
     *                                                                     asignación vehicular
     *                                                                     fecha inicio, fecha fin, hora inicio, hora fin
     *                                  
     *                               ]
     * 
     * @return json $jsonData
     */
    public function getJSONHistorialAsignacionVehicularXElemento($arrayParametros,$em)
    {
        $arrayEncontrados   = array();
        if($arrayParametros['errorFechas'])
        {
            $total              = 0;
        }
        else
        {
            $arrayResultado     = $this->getResultadoHistorialAsignacionVehicularXElemento($arrayParametros,$em);
            $resultado          = $arrayResultado['resultado'];
            $intTotal           = $arrayResultado['total'];
            $total              = 0;

            if($resultado)
            {
                $total = $intTotal;
                foreach($resultado as $data)
                {
                    $arrayItem = array();

                    $arrayItem['strFechaInicioAsignacionVehicularHisto']        = $data['fechaInicioAsignacionVehicular'];
                    $arrayItem['strFechaFinAsignacionVehicularHisto']           = $data['fechaFinAsignacionVehicular'];
                    $arrayItem['strHoraInicioAsignacionVehicularHisto']         = $data['horaInicioAsignacionVehicular'];
                    $arrayItem['strHoraFinAsignacionVehicularHisto']            = $data['horaFinAsignacionVehicular'];
                    $arrayItem['strEstadoAsignacionVehicularHisto']             = $data['estadoAsignacionVehicular'] ;

                    $arrayItem['strCuadrillaAsignacionVehicularHisto']          = $data['cuadrillaAsignacionVehicular'];
                    
                    
                    $arrayItem['intIdPersonaEmpresaRolChoferAsignacionVehicularHisto']  = $data['idPerChoferAsignacionVehicular'] ? 
                                                                                                        $data['idPerChoferAsignacionVehicular'] : '';
                    $arrayItem['intIdPersonaChoferAsignacionVehicularHisto']        = $data['idPersonaChoferAsignacionVehicular'] ? 
                                                                                        $data['idPersonaChoferAsignacionVehicular'] : '';
                    
                    $arrayItem['strNombresChoferAsignacionVehicularHisto']          = $data["nombresChoferAsignacionVehicular"];
                    $arrayItem['strApellidosChoferAsignacionVehicularHisto']        = $data["apellidosChoferAsignacionVehicular"];
                    $arrayItem['strIdentificacionChoferAsignacionVehicularHisto']   = $data["identificacionChoferAsignacionVehicular"];
                    
                    //$arrayItem['strNombresApellidosChoferAsignacionVehicularHisto'] = $data["choferAsignacionVehicular"];
                    

                    $arrayEncontrados[] = $arrayItem;

                }

            }
        }
        $arrayRespuesta = array('total' => $total, 'encontrados' => $arrayEncontrados);
        $jsonData       = json_encode($arrayRespuesta);
        return $jsonData;
    }
    
    
    /**
     * getResultadoHistorialAsignacionVehicularXElemento, Consulta las asignaciones vehiculares que se han realizado de
     * de determinado vehículo
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 
     * 
     * @param  array $arrayParametros[  'intStart'                      => Inicio del rownum,
     *                                  'intLimit'                      => Fin del rownum,
     *                                  'idElemento'                    => id del vehículo a consultar,
     *                                  'strFechaDesde'                 => fecha desde a consultar,
     *                                  'strFechaHasta'                 => fecha hasta a consultar,
     *                                  'errorFechas'                   => Indica si existe un error o no en el rango de fechas a buscar
     *                                  
     *                                  'arrayDetallesVehicular'     => array con los nombres de los detalles de las fechas y horas de la
     *                                                                     asignación vehicular
     *                                                                     fecha inicio, fecha fin, hora inicio, hora fin
     *                                  
     *                               ]
     * 
     * @return $arrayRespuesta ['resultado','total']
     */
    public function getResultadoHistorialAsignacionVehicularXElemento($arrayParametros,$em)
    {
        $arrayRespuesta['total']     = 0;
        $arrayRespuesta['resultado'] = "";
        try
        {
            $rsm = new ResultSetMappingBuilder($em);
            $rsmCount = new ResultSetMappingBuilder($em);
            $ntvQuery = $em->createNativeQuery(null, $rsm);
            $ntvQueryCount = $em->createNativeQuery(null, $rsmCount);

            $strSelectCount     = " SELECT COUNT(*) AS TOTAL ";
            
            
            $strSelect          = " SELECT DISTINCT(de_cuadrilla_av.ID_DETALLE_ELEMENTO) as ID_DET_CUADRILLA,
                                    de_fecha_inicio_av.DETALLE_VALOR as FECHA_INICIO,
                                    de_fecha_fin_av.DETALLE_VALOR as FECHA_FIN,
                                    de_hora_inicio_av.DETALLE_VALOR as HORA_INICIO,
                                    de_hora_fin_av.DETALLE_VALOR as HORA_FIN, 
                                    ac.ID_CUADRILLA ,ac.NOMBRE_CUADRILLA, 
                                    de_fecha_inicio_av.ESTADO,
                                    per.ID_PERSONA_ROL, p.ID_PERSONA,p.NOMBRES, p.APELLIDOS, p.IDENTIFICACION_CLIENTE ";
            $strFromAndWhere    = " FROM DB_INFRAESTRUCTURA.INFO_ELEMENTO e  
                                    INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO de_cuadrilla_av 
                                        ON de_cuadrilla_av.ELEMENTO_ID=e.ID_ELEMENTO  
                                            AND de_cuadrilla_av.DETALLE_NOMBRE = :strDetalleCuadrillaAsignacionVehicular 
                                            
                                    INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO de_solicitud_av 
                                        ON de_cuadrilla_av.ID_DETALLE_ELEMENTO =  de_solicitud_av.REF_DETALLE_ELEMENTO_ID
                                            AND de_solicitud_av.DETALLE_NOMBRE = :strDetalleSolAsignacionVehicular 
                                            
                                    INNER JOIN DB_COMERCIAL.ADMI_CUADRILLA ac 
                                        ON ac.ID_CUADRILLA = de_cuadrilla_av.DETALLE_VALOR
                                        
                                    INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO de_fecha_inicio_av 
                                        ON de_cuadrilla_av.ID_DETALLE_ELEMENTO=de_fecha_inicio_av.REF_DETALLE_ELEMENTO_ID 
                                            AND de_fecha_inicio_av.DETALLE_NOMBRE = :strDetalleFechaInicioAsignacionVehicular 
                                            
                                    LEFT JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO de_fecha_fin_av 
                                        ON de_cuadrilla_av.ID_DETALLE_ELEMENTO=de_fecha_fin_av.REF_DETALLE_ELEMENTO_ID 
                                            AND de_fecha_fin_av.DETALLE_NOMBRE = :strDetalleFechaFinAsignacionVehicular 
                                            
                                    INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO de_hora_inicio_av 
                                        ON de_cuadrilla_av.ID_DETALLE_ELEMENTO=de_hora_inicio_av.REF_DETALLE_ELEMENTO_ID 
                                            AND de_hora_inicio_av.DETALLE_NOMBRE = :strDetalleHoraInicioAsignacionVehicular 
                                            
                                    INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO de_hora_fin_av 
                                        ON de_cuadrilla_av.ID_DETALLE_ELEMENTO=de_hora_fin_av.REF_DETALLE_ELEMENTO_ID 
                                            AND de_hora_fin_av.DETALLE_NOMBRE = :strDetalleHoraFinAsignacionVehicular  
                                            
                                    INNER JOIN DB_COMERCIAL.INFO_DETALLE_SOLICITUD detalleSolicitud 
                                        ON detalleSolicitud.ID_DETALLE_SOLICITUD=de_solicitud_av.DETALLE_VALOR 
                                            AND detalleSolicitud.TIPO_SOLICITUD_ID = :idTipoSolicitud
                                        
                                    INNER JOIN DB_SOPORTE.INFO_DETALLE detalle
                                        ON detalle.DETALLE_SOLICITUD_ID=detalleSolicitud.ID_DETALLE_SOLICITUD 
                                        
                                    INNER JOIN DB_SOPORTE.INFO_DETALLE_ASIGNACION detalleAsignacion
                                        ON detalleAsignacion.DETALLE_ID=detalle.ID_DETALLE 
                                    INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL per 
                                        ON per.ID_PERSONA_ROL=detalleAsignacion.PERSONA_EMPRESA_ROL_ID
                                    INNER JOIN DB_COMERCIAL.INFO_PERSONA p ON per.PERSONA_ID = p.ID_PERSONA 
                                    WHERE e.ID_ELEMENTO = :idElemento

                                        ";

            
            $strOrderBy         = " ORDER BY de_cuadrilla_av.ID_DETALLE_ELEMENTO DESC ";
            
            $rsm->addScalarResult('ID_DET_CUADRILLA', 'idDetalleCuadrilla', 'integer');
            $rsm->addScalarResult('FECHA_INICIO', 'fechaInicioAsignacionVehicular', 'string');
            $rsm->addScalarResult('FECHA_FIN', 'fechaFinAsignacionVehicular', 'string');
            $rsm->addScalarResult('HORA_INICIO', 'horaInicioAsignacionVehicular', 'string');
            $rsm->addScalarResult('HORA_FIN', 'horaFinAsignacionVehicular', 'string');
            $rsm->addScalarResult('ID_CUADRILLA', 'idCuadrilla', 'integer');
            $rsm->addScalarResult('NOMBRE_CUADRILLA', 'cuadrillaAsignacionVehicular', 'string');
            $rsm->addScalarResult('ESTADO', 'estadoAsignacionVehicular', 'string');
            
            $rsm->addScalarResult('ID_PERSONA_ROL', 'idPerChoferAsignacionVehicular', 'integer');
            $rsm->addScalarResult('ID_PERSONA', 'idPersonaChoferAsignacionVehicular', 'integer');
            $rsm->addScalarResult('NOMBRES', 'nombresChoferAsignacionVehicular', 'string');
            $rsm->addScalarResult('APELLIDOS', 'apellidosChoferAsignacionVehicular', 'string');
            $rsm->addScalarResult('IDENTIFICACION_CLIENTE', 'identificacionChoferAsignacionVehicular', 'string');
            

            $rsmCount->addScalarResult('TOTAL', 'total', 'integer');
            
            $arrayDetallesVehicular=$arrayParametros['arrayDetallesVehicular'];

            $ntvQuery->setParameter('idElemento', $arrayParametros['idElemento']);
            $ntvQueryCount->setParameter('idElemento', $arrayParametros['idElemento']);
            
            $strDetalleCuadrillaAsignacionVehicular = $arrayDetallesVehicular['strDetalleCuadrillaAsignacionVehicular'];
            $ntvQuery->setParameter('strDetalleCuadrillaAsignacionVehicular',$strDetalleCuadrillaAsignacionVehicular);
            $ntvQueryCount->setParameter('strDetalleCuadrillaAsignacionVehicular',$strDetalleCuadrillaAsignacionVehicular);

            $strDetalleFechaInicioAsignacionVehicular = $arrayDetallesVehicular['strDetalleFechaInicioAsignacionVehicular'];
            $ntvQuery->setParameter('strDetalleFechaInicioAsignacionVehicular',$strDetalleFechaInicioAsignacionVehicular);
            $ntvQueryCount->setParameter('strDetalleFechaInicioAsignacionVehicular',$strDetalleFechaInicioAsignacionVehicular);
            
            $ntvQuery->setParameter('strDetalleFechaFinAsignacionVehicular', $arrayDetallesVehicular['strDetalleFechaFinAsignacionVehicular']);
            $ntvQueryCount->setParameter('strDetalleFechaFinAsignacionVehicular', $arrayDetallesVehicular['strDetalleFechaFinAsignacionVehicular']);
            
            $strDetalleHoraInicioAsignacionVehicular = $arrayDetallesVehicular['strDetalleHoraInicioAsignacionVehicular'];
            $ntvQuery->setParameter('strDetalleHoraInicioAsignacionVehicular', $strDetalleHoraInicioAsignacionVehicular);
            $ntvQueryCount->setParameter('strDetalleHoraInicioAsignacionVehicular', $strDetalleHoraInicioAsignacionVehicular);
            
            $strDetalleHoraFinAsignacionVehicular = $arrayDetallesVehicular['strDetalleHoraFinAsignacionVehicular'];
            $ntvQuery->setParameter('strDetalleHoraFinAsignacionVehicular', $strDetalleHoraFinAsignacionVehicular);
            $ntvQueryCount->setParameter('strDetalleHoraFinAsignacionVehicular', $strDetalleHoraFinAsignacionVehicular);
            
            $strDetalleSolicitudAsignacionVehicular = $arrayDetallesVehicular['strDetalleSolicitudAsignacionVehicular'];
            $ntvQuery->setParameter('strDetalleSolAsignacionVehicular', $strDetalleSolicitudAsignacionVehicular);
            $ntvQueryCount->setParameter('strDetalleSolAsignacionVehicular', $strDetalleSolicitudAsignacionVehicular);
            
            $ntvQuery->setParameter('idTipoSolicitud', $arrayParametros["idTipoSolicitud"]);
            $ntvQueryCount->setParameter('idTipoSolicitud', $arrayParametros["idTipoSolicitud"]);
            
            
            
            
            if(isset($arrayParametros["strFechaDesde"]) && isset($arrayParametros["strFechaHasta"]) )
            {
                if($arrayParametros["strFechaDesde"] && $arrayParametros["strFechaHasta"])
                {
                    $strFromAndWhere.=" AND 
                    (
                        (
                            de_fecha_fin_av.DETALLE_VALOR is NOT NULL 
                            AND
                            (
                                (
                                TO_TIMESTAMP( de_fecha_fin_av.DETALLE_VALOR ,'DD/MM/YYYY' ) >= TO_TIMESTAMP(:strFechaDesde,'DD/MM/YYYY' )
                                AND 
                                TO_TIMESTAMP( de_fecha_fin_av.DETALLE_VALOR ,'DD/MM/YYYY' )<= TO_TIMESTAMP(:strFechaHasta,'DD/MM/YYYY' )
                                )

                                OR

                                (
                                TO_TIMESTAMP( de_fecha_inicio_av.DETALLE_VALOR ,'DD/MM/YYYY' ) <= TO_TIMESTAMP(:strFechaHasta,'DD/MM/YYYY' )
                                AND 
                                TO_TIMESTAMP( de_fecha_inicio_av.DETALLE_VALOR ,'DD/MM/YYYY' ) >= TO_TIMESTAMP(:strFechaDesde,'DD/MM/YYYY' )
                                )

                                OR

                                (
                                TO_TIMESTAMP( de_fecha_inicio_av.DETALLE_VALOR ,'DD/MM/YYYY' ) <= TO_TIMESTAMP(:strFechaDesde,'DD/MM/YYYY' )
                                AND
                                TO_TIMESTAMP( de_fecha_fin_av.DETALLE_VALOR ,'DD/MM/YYYY' ) >= TO_TIMESTAMP(:strFechaHasta,'DD/MM/YYYY' )
                                )
                            )
                        )
                        OR
                        (
                            TO_TIMESTAMP( de_fecha_inicio_av.DETALLE_VALOR ,'DD/MM/YYYY' ) >= TO_TIMESTAMP(:strFechaDesde,'DD/MM/YYYY' )
                            AND TO_TIMESTAMP( de_fecha_inicio_av.DETALLE_VALOR ,'DD/MM/YYYY' ) <= TO_TIMESTAMP(:strFechaHasta,'DD/MM/YYYY' )
                            AND de_cuadrilla_av.ESTADO = :strEstadoActivo
                            
                        )
                    ) ";
                    
                    $ntvQuery->setParameter('strEstadoActivo', 'Activo');
                    $ntvQueryCount->setParameter('strEstadoActivo', 'Activo');
                    
                    $ntvQuery->setParameter('strFechaDesde', $arrayParametros["strFechaDesde"]);
                    $ntvQueryCount->setParameter('strFechaDesde', $arrayParametros["strFechaDesde"]);
                    
                    $ntvQuery->setParameter('strFechaHasta', $arrayParametros["strFechaHasta"]);
                    $ntvQueryCount->setParameter('strFechaHasta', $arrayParametros["strFechaHasta"]);
                }
            }
            
            
            
            if( isset($arrayParametros["criterios_busqueda"]) )
            {
                if($arrayParametros["criterios_busqueda"])
                {
                    if( isset($arrayParametros["criterios_busqueda"]["strNombresChoferAV"]) )
                    {
                        if($arrayParametros["criterios_busqueda"]["strNombresChoferAV"])
                        {
                            $strFromAndWhere.=" AND p.NOMBRES like :nombresChoferAV ";
                            $ntvQuery->setParameter('nombresChoferAV', "%".strtoupper($arrayParametros["criterios_busqueda"]['strNombresChoferAV'])."%");
                            $ntvQueryCount->setParameter('nombresChoferAV', "%".strtoupper($arrayParametros["criterios_busqueda"]['strNombresChoferAV'])."%");
                        }
                    }
                    
                    if( isset($arrayParametros["criterios_busqueda"]["strApellidosChoferAV"]) )
                    {
                        if($arrayParametros["criterios_busqueda"]["strApellidosChoferAV"])
                        {
                            $strFromAndWhere.=" AND p.APELLIDOS like :apellidosChoferAV ";
                            $ntvQuery->setParameter('apellidosChoferAV', "%".strtoupper($arrayParametros["criterios_busqueda"]['strApellidosChoferAV'])."%");
                            $ntvQueryCount->setParameter('apellidosChoferAV', "%".strtoupper($arrayParametros["criterios_busqueda"]['strApellidosChoferAV'])."%");
                        }
                    }
                    
                    
                    if( isset($arrayParametros["criterios_busqueda"]["strIdentificacionChoferAV"]) )
                    {
                        if($arrayParametros["criterios_busqueda"]["strIdentificacionChoferAV"])
                        {
                            $strFromAndWhere.=" AND p.IDENTIFICACION_CLIENTE = :identificacionChoferAV ";
                            $ntvQuery->setParameter('identificacionChoferAV', $arrayParametros["criterios_busqueda"]['strIdentificacionChoferAV']);
                            $ntvQueryCount->setParameter('identificacionChoferAV', $arrayParametros["criterios_busqueda"]['strIdentificacionChoferAV']);
                        }
                    }
                }
            }
            
            
            $strSqlPrincipal = $strSelect . $strFromAndWhere . $strOrderBy;

            $strSqlFinal = '';

            if(isset($arrayParametros['intStart']) && isset($arrayParametros['intLimit']))
            {
                if($arrayParametros['intStart'] && $arrayParametros['intLimit'])
                {
                    $intInicio = $arrayParametros['intStart'];
                    $intFin = $arrayParametros['intStart'] + $arrayParametros['intLimit'];
                    $strSqlFinal = '  SELECT * FROM 
                                        (
                                            SELECT consultaPrincipal.*,rownum AS consultaPrincipal_rownum 
                                            FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                            WHERE rownum<=' . $intFin . '
                                        ) WHERE consultaPrincipal_rownum >' . $intInicio;
                }
                else
                {
                    $strSqlFinal = '  SELECT consultaPrincipal.* 
                                        FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                        WHERE rownum<=' . $arrayParametros['intLimit'];
                }
            }
            else
            {
                $strSqlFinal = $strSqlPrincipal;
            }

            $ntvQuery->setSQL($strSqlFinal);
            $arrayResultado = $ntvQuery->getResult();

            $strSqlCount = $strSelectCount . " FROM (" . $strSqlPrincipal . ")";
            $ntvQueryCount->setSQL($strSqlCount);

            $intTotal = $ntvQueryCount->getSingleScalarResult();
            
            $arrayRespuesta['resultado'] = $arrayResultado;
            $arrayRespuesta['total']     = $intTotal;
           
            return $arrayRespuesta;
            
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
    }
    /**************************Fin de Asignación Operativa****************************/
    
    /**
     * Funcion que sirve para crear el catalogo de VLANs de un PE
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 21-04-2017
     *
     * @param array $arrayParametros [ intIdElementoPe   Elemento Pe a crearle catalogo de vlans
     *                                 strUsrCreacion    Usuario que lanza la ejecucion
     *                               ]
     * @param $strMensajeError Mensaje de error en caso de ser existente 
     */
    public function crearCatalogoVlansPe($arrayParametros)
    {             
        $strMensajeError      = str_pad($strMensajeError, 3000, " ");        

        $sql = "BEGIN INFRK_TRANSACCIONES.INFRP_CREAR_VLANS_EN_PE( "
             . "                                           :Pv_IdElementoPe, "
             . "                                           :Pv_usuarioCreacion,"
             . "                                           :Lv_MensaError"
             . "                                           ); "
             . "END;";
       
        $stmt = $this->_em->getConnection()->prepare($sql);
        
        $stmt->bindParam('Pv_IdElementoPe',      $arrayParametros['intIdElementoPe']);
        $stmt->bindParam('Pv_usuarioCreacion',   $arrayParametros['strUsrCreacion']);
        $stmt->bindParam('Lv_MensaError',        $strMensajeError);
        $stmt->execute();  
        
        return $strMensajeError;
    }       

    /**
     * Documentación para la función 'crearCatalogoVlansOlt'.
     *
     * Función que sirve para crear el catalogo de VLAN de un olt
     *
     * @param array $arrayParametros [
     *                                  "intIdElementoPe"     => Elemento Olt a crearle catalogo de vlans.
     *                                  "strUsrCreacion"      => Usuario que lanza la ejecucion.
     *                               ]
     *
     * @return string $strMensajeError Mensaje de error en caso de ser existente.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 15-11-2019
     *
     */
    public function crearCatalogoVlansOlt($arrayParametros)
    {
        $strMensajeError = "";
        try
        {
            $strSql = "BEGIN INFRK_TRANSACCIONES.P_CREAR_VLANS_EN_OLT( "
                    . "                                           :Pv_IdElementoPe, "
                    . "                                           :Pv_usuarioCreacion,"
                    . "                                           :Pv_MensajeError"
                    . "                                           ); "
                    . "END;";
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            
            $objStmt->bindParam('Pv_IdElementoPe',      $arrayParametros['intIdElementoPe']);
            $objStmt->bindParam('Pv_usuarioCreacion',   $arrayParametros['strUsrCreacion']);
            $objStmt->bindParam('Pv_MensajeError',      $strMensajeError);
            $objStmt->execute();
        }
        catch(\Exception $ex)
        {
            $strMensajeError = " Error en la Creación de las VLANS para el Olt, por favor notificar al departamento de  sistemas";
        }
        return $strMensajeError;
    }
    /**
     * Función que se encarga de obtener los detalles de los elementos.
     *
     * @author German Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 01-04-2018
     *
     * @param  $arrayParametros [
     *                              strDetalleNombre = Detalle nombre
     *                              strDetalleValor  = Valor
     *                              strEstado        = Estado
     *                          ]
     * @return Array
     */
    public function getDetalleElemento($arrayParametros)
    {
        try
        {
            $objQuery = $this->_em->createQuery();

            $strSql = "SELECT ide "
                         ."FROM schemaBundle:InfoDetalleElemento ide "
                        ."WHERE upper(ide.detalleNombre) = upper(:strDetalleNombre) "
                          ."AND ide.detalleValor         = :strDetalleValor "
                          ."AND upper(ide.estado)        = upper(:strEstado) ";

            $objQuery->setParameter('strDetalleNombre', $arrayParametros['strDetalleNombre']);
            $objQuery->setParameter('strDetalleValor' , $arrayParametros['strDetalleValor']);
            $objQuery->setParameter('strEstado'       , $arrayParametros['strEstado']);

            $objQuery->setDQL($strSql);
            $arrayRespuesta = $objQuery->getResult();
        }
        catch (\Exception $objException)
        {
            error_log("Error - InfoDetalleElementoRepository.getDetalleElemento -> ".$objException->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
     * Función que se encarga de obtener lo elementos filtrando por las caracteristicas.
     *
     * @author German Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 02-05-2018
     *
     * @param  $arrayParametros [
     *                              strDetalleNombre    = Detalle del nombre de la tabla INFO_DETALLE_ELEMENTO,
     *                              strCaracteristica   = Descripcion de la caracteristica de la tabla ADMI_CARACTERISTICA,
     *                              intValor            = Valor de la tabla INFO_DETALLE_SOL_CARACT,
     *                              strEstadoElemento   = Estado de la tabla INFO_DETALLE_ELEMENTO,
     *                              strEstadoSol        = Estado de la tabla INFO_DETALLE_SOL_CARACT,
     *                              strEstadoCar        = Estado de la tabla ADMI_CARACTERISTICA
     *                          ]
     * @return Array
     */
    public function getDetalleElementoCaracteristica($arrayParametros)
    {
        try
        {
            $objQuery = $this->_em->createQuery();

            $strSql = "SELECT infoDetalleElemento "
                       ."FROM schemaBundle:InfoDetalleElemento  infoDetalleElemento, "
                            ."schemaBundle:InfoDetalleSolCaract infoDetalleSolCaract, "
                            ."schemaBundle:AdmiCaracteristica   admiCaracteristica "
                      ."WHERE infoDetalleSolCaract.detalleSolicitudId      = infoDetalleElemento.detalleValor "
                        ."AND infoDetalleSolCaract.caracteristicaId        = admiCaracteristica.id "
                        ."AND infoDetalleElemento.detalleNombre            = :strDetalleNombre "
                        ."AND admiCaracteristica.descripcionCaracteristica = :strCaracteristica "
                        ."AND infoDetalleSolCaract.valor                   = :intValor "
                        ."AND infoDetalleElemento.estado                   = :strEstadoElemento "
                        ."AND infoDetalleSolCaract.estado                  = :strEstadoSol "
                        ."AND admiCaracteristica.estado                    = :strEstadoCar ";

            $objQuery->setParameter("strDetalleNombre"  , $arrayParametros['strDetalleNombre']);
            $objQuery->setParameter("strCaracteristica" , $arrayParametros['strCaracteristica']);
            $objQuery->setParameter("intValor"          , $arrayParametros['intValor']);
            $objQuery->setParameter("strEstadoElemento" , $arrayParametros['strEstadoElemento']);
            $objQuery->setParameter("strEstadoSol"      , $arrayParametros['strEstadoSol']);
            $objQuery->setParameter("strEstadoCar"      , $arrayParametros['strEstadoCar']);

            $objQuery->setDQL($strSql);
            $arrayRespuesta = $objQuery->getResult();
        }
        catch (\Exception $objException)
        {
            error_log("Error - InfoDetalleElementoRepository.getDetalleElementoCaracteristica -> ".$objException->getMessage());
        }
        return $arrayRespuesta;
    }
}
