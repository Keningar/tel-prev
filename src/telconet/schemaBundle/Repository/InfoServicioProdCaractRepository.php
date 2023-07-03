<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoServicioProdCaractRepository extends EntityRepository
{
      public function findByServicioAndEstadoAndTipo($params)
      {
	      $query = $this->_em->createQuery("SELECT spc
						  FROM 
						    schemaBundle:AdmiCaracteristica c,
						    schemaBundle:AdmiProductoCaracteristica pc,
						    schemaBundle:InfoServicioProdCaract spc
						  WHERE lower(c.tipo) = lower('".$params['tipo']."')
						    AND pc.caracteristicaId = c.id
						    AND spc.productoCaracterisiticaId = pc.id
						    AND spc.servicioId = ".$params['servicioId']."
						    AND lower(spc.estado) = lower('".$params['estado']."')");
	      return $query->getResult();
      } 
      
      /**
     * Obtiene el valor de la caracateristica de un producto
     * 
     * @author Josue Valencia <ajvalencia@telconet.ec>
     * @version 1.0 26-10-2022
     * 
     **/
      public function findByValorCaractServicio($arrayParams)
      {
	      $objQuery = $this->_em->createQuery("SELECT spc
                FROM 
                schemaBundle:AdmiCaracteristica c,
                schemaBundle:AdmiProductoCaracteristica pc,
                schemaBundle:InfoServicioProdCaract spc
                WHERE lower(c.tipo) = lower('".$arrayParams['tipo']."')
                AND pc.caracteristicaId = c.id
                AND lower(c.descripcionCaracteristica) 
                = lower('".$arrayParams['descripCaract']."')
                AND spc.productoCaracterisiticaId = pc.id
                AND spc.servicioId = ".$arrayParams['servicioId']."
                AND lower(spc.estado) = lower('".$arrayParams['estado']."')");
	      return $objQuery->getResult();
      } 
      

   /**
     * Obtiene los indices registrados en los servicios Activos por linea pon de OLT
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 23-04-2015
     * 
     * @param integer $elementoId 
     * @param integer $interfaceElementoId
     * @param integer $indice
     * @return array  $arraResult
     **/
    public function obtieneIndiceClientesPorOlt($elementoId, $interfaceElementoId, $indice)
    {
        $strQueryObtenerPadre = "SELECT INFO_SERVICIO.ID_SERVICIO
                                    FROM INFO_SERVICIO ,
                                      INFO_SERVICIO_PROD_CARACT ,
                                      INFO_SERVICIO_TECNICO ,
                                      ADMI_PRODUCTO ,
                                      ADMI_PRODUCTO_CARACTERISTICA ,
                                      ADMI_CARACTERISTICA 
                                WHERE INFO_SERVICIO_TECNICO.SERVICIO_ID                   = INFO_SERVICIO.ID_SERVICIO
                                AND INFO_SERVICIO_PROD_CARACT.SERVICIO_ID                 = INFO_SERVICIO.ID_SERVICIO
                                AND INFO_SERVICIO_TECNICO.ELEMENTO_ID                     = :elementoIdParam
                                AND INFO_SERVICIO_TECNICO.INTERFACE_ELEMENTO_ID           = :interfaceElementoIdParam
                                AND INFO_SERVICIO_PROD_CARACT.VALOR                       = :indiceParam
                                AND INFO_SERVICIO_PROD_CARACT.PRODUCTO_CARACTERISITICA_ID = ADMI_PRODUCTO_CARACTERISTICA.ID_PRODUCTO_CARACTERISITICA
                                AND INFO_SERVICIO_PROD_CARACT.ESTADO                      = :estadoSerProdCaractParam
                                AND INFO_SERVICIO.ESTADO                               = :estadoServicioParam
                                AND ADMI_PRODUCTO_CARACTERISTICA.PRODUCTO_ID           = ADMI_PRODUCTO.ID_PRODUCTO
                                AND ADMI_PRODUCTO_CARACTERISTICA.CARACTERISTICA_ID     = ADMI_CARACTERISTICA.ID_CARACTERISTICA
                                AND ADMI_PRODUCTO.DESCRIPCION_PRODUCTO                 = :descripcionProductoParam
                                AND ADMI_PRODUCTO.ESTADO                               = :estadoProductoParam
                                AND ADMI_PRODUCTO.NOMBRE_TECNICO                       = :nombreTecnicoProductoParam
                                AND ADMI_CARACTERISTICA.DESCRIPCION_CARACTERISTICA     = :descripcionCaracteristicaParam
                                AND ADMI_CARACTERISTICA.ESTADO                         = :estadoCaracteristicaParam ";
        $stmt = $this->_em->getConnection()->prepare($strQueryObtenerPadre);
        $stmt->bindValue('elementoIdParam',                $elementoId);
        $stmt->bindValue('indiceParam',                    $indice);
        $stmt->bindValue('interfaceElementoIdParam',       $interfaceElementoId);
        $stmt->bindValue('estadoSerProdCaractParam',       'Activo');
        $stmt->bindValue('estadoServicioParam',            'Activo');
        $stmt->bindValue('descripcionProductoParam',       'INTERNET DEDICADO');
        $stmt->bindValue('estadoProductoParam',            'Activo');
        $stmt->bindValue('nombreTecnicoProductoParam',     'INTERNET');
        $stmt->bindValue('descripcionCaracteristicaParam', 'INDICE CLIENTE');
        $stmt->bindValue('estadoCaracteristicaParam',      'Activo');
        $stmt->execute();
        $arraResult = $stmt->fetchAll();
        return $arraResult;
    }
    
    /**
     * Documentación para el método 'generarJsonCaracteristicasServicios'.
     *
     * Obtiene registros de Caracteristicas de servicios
     * 
     * @param Integer        $idServicio
     * @param String         $estado
     * @return Object        $datos listado de registros de factibilidad
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 25-11-2014
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 11-05-2016    Se cambia validación de campo valor de caracteristicas a mostrar en grid de registros
     */
    public function generarJsonCaracteristicasServicios($intIdServicio, $strEstado, $strCaraClienteEm)
    {
        $arrayEncontrados   = array();
        $intNumeroRegistros = 0;
        $objRegistros          = $this->getCaracteristicasServicios($intIdServicio, $strEstado, $strCaraClienteEm);
        
        if($objRegistros)
        {
            $intNumeroRegistros = count($objRegistros);
            foreach($objRegistros as $data)
            {
                if ($data["DESCRIPCION_CARACTERISTICA"] != "CLAVE")
                {
                    $arrayEncontrados[] = array(
                                                'idServicioProdCaract'       => ($data["ID_SERVICIO_PROD_CARACT"] ? 
                                                                                 $data["ID_SERVICIO_PROD_CARACT"] : ""),
                                                'descripcionProducto'        => ($data["DESCRIPCION_PRODUCTO"] ? 
                                                                                 $data["DESCRIPCION_PRODUCTO"] : ""),
                                                'descripcionCaracteristica'  => ($data["DESCRIPCION_CARACTERISTICA"] ? 
                                                                                 $data["DESCRIPCION_CARACTERISTICA"] : ""),
                                                'valor'                      => ($data["VALOR"]!=""   ? $data["VALOR"] : ""),
                                                'estado'                     => ($data["ESTADO"]      ? $data["ESTADO"] : ""),
                                                'feCreacion'                 => ($data["FE_CREACION"] ? $data["FE_CREACION"] : ""),
                                                'feUltMod'                   => ($data["FE_ULT_MOD"]  ? $data["FE_ULT_MOD"] : ""),
                                                'mostrarBoton'               => ($data["ESTADO"]=="Eliminado"? "NO" : "SI" )
                                               );
                }
            }

            $dataF     = json_encode($arrayEncontrados);
            $strResultado = '{"total":"' . $intNumeroRegistros . '","encontrados":' . $dataF . '}';
            return $strResultado;
        }
        else
        {
            $strResultado = '{"total":"0","encontrados":[]}';
            return $strResultado;
        }
    }
    
    /**
     * getServiciosPorProductoCaracteristica
     * obtiene servicios segun el nombre del producto, la caracteristica y el valor
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 09-02-2015
     * 
     * costo query 9
     * 
     * @param type $nivel
     * @param type $nombreSplitter
     * 
     * @return string $data
     * */
    public function getServiciosPorProductoCaracteristica($arrayParametros)
    {
        $strValor           = $arrayParametros['strValor'];
        $strProducto        = $arrayParametros['strProducto'];
        $strCaracteristica  = $arrayParametros['strCaracteristica'];        
  
        $objRsm = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);

        $strSql = " select P.LOGIN, S.DESCRIPCION_PRESENTA_FACTURA, S.ESTADO
                    from INFO_SERVICIO_PROD_CARACT SPC,
                    INFO_SERVICIO S,
                    INFO_PUNTO P
                    where SPC.SERVICIO_ID = S.ID_SERVICIO
                    and S.PUNTO_ID = P.ID_PUNTO
                    and SPC.PRODUCTO_CARACTERISITICA_ID =  (select PC.ID_PRODUCTO_CARACTERISITICA
                                                              from ADMI_PRODUCTO_CARACTERISTICA PC,
                                                                ADMI_PRODUCTO P,
                                                                ADMI_CARACTERISTICA C
                                                              where PC.PRODUCTO_ID             = P.ID_PRODUCTO
                                                              and PC.CARACTERISTICA_ID         = C.ID_CARACTERISTICA
                                                              and P.DESCRIPCION_PRODUCTO       = :producto
                                                              and P.NOMBRE_TECNICO             = :producto
                                                              and P.ESTADO                     = :estado
                                                              and C.DESCRIPCION_CARACTERISTICA = :caracteristica
                                                              and C.ESTADO                     = :estado)
                    and SPC.ESTADO = :estado
                    and SPC.VALOR = :valor
                    and S.ESTADO not in (:estados)";

        $objRsm->addScalarResult(strtoupper('LOGIN'), 'login', 'string');
        $objRsm->addScalarResult(strtoupper('DESCRIPCION_PRESENTA_FACTURA'), 'descripcion', 'string');
        $objRsm->addScalarResult(strtoupper('ESTADO'), 'estadoServicio', 'string');

        $objQuery->setParameter("estados", array('Anulado', 'Eliminado', 'Rechazado', 'Rechazada', 'Cancelado'));
        $objQuery->setParameter("valor", $strValor);
        $objQuery->setParameter("producto", $strProducto);
        $objQuery->setParameter("caracteristica", $strCaracteristica);
        $objQuery->setParameter("estado", "Activo");

        $objQuery->setSQL($strSql);

        $arrayData = $objQuery->getResult();

        return $arrayData;
        
    }    
    
    
        /**
     * getLineasTn
     * Obtiene las líneas que tiene asignado un servicio
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 18-07-2018
     * 
     * costo query 9
     * 
     * @param $arrayParametros['intServicio']
     * 
     * @return string $arrayData
     * */
    public function getLineasTn($arrayParametros)
    {
        $intServicio= $arrayParametros['intServicio'];  
        $objRsm     = new ResultSetMappingBuilder($this->_em);
        $objQuery   = $this->_em->createNativeQuery(null, $objRsm);

        $strSql     = " SELECT
                    SPC.ID_SERVICIO_PROD_CARACT,
                    SPC.VALOR NUMERO,
                    SPC.ESTADO,
                      (SELECT ID_SERVICIO_PROD_CARACT
                      FROM INFO_SERVICIO_PROD_CARACT SPC1,
                        ADMI_PRODUCTO_CARACTERISTICA PC1,
                        ADMI_CARACTERISTICA C1
                      WHERE SPC1.REF_SERVICIO_PROD_CARACT_ID = SPC.ID_SERVICIO_PROD_CARACT
                      AND SPC1.SERVICIO_ID                   = SPC.SERVICIO_ID
                      AND SPC1.PRODUCTO_CARACTERISITICA_ID   = PC1.ID_PRODUCTO_CARACTERISITICA
                      AND C1.ID_CARACTERISTICA               = PC1.CARACTERISTICA_ID
                      AND C1.DESCRIPCION_CARACTERISTICA      = :caractDominio
                      AND SPC1.ESTADO                        = :estado) DOMINIO,
                      (SELECT ID_SERVICIO_PROD_CARACT
                      FROM INFO_SERVICIO_PROD_CARACT SPC1,
                        ADMI_PRODUCTO_CARACTERISTICA PC1,
                        ADMI_CARACTERISTICA C1
                      WHERE SPC1.REF_SERVICIO_PROD_CARACT_ID = SPC.ID_SERVICIO_PROD_CARACT
                      AND SPC1.SERVICIO_ID                   = SPC.SERVICIO_ID
                      AND SPC1.PRODUCTO_CARACTERISITICA_ID   = PC1.ID_PRODUCTO_CARACTERISITICA
                      AND C1.ID_CARACTERISTICA               = PC1.CARACTERISTICA_ID
                      AND C1.DESCRIPCION_CARACTERISTICA      = :caractClave
                      AND SPC1.ESTADO                        = :estado  ) CLAVE,
                      (SELECT ID_SERVICIO_PROD_CARACT
                      FROM INFO_SERVICIO_PROD_CARACT SPC1,
                        ADMI_PRODUCTO_CARACTERISTICA PC1,
                        ADMI_CARACTERISTICA C1
                      WHERE SPC1.REF_SERVICIO_PROD_CARACT_ID = SPC.ID_SERVICIO_PROD_CARACT
                      AND SPC1.SERVICIO_ID                   = SPC.SERVICIO_ID
                      AND SPC1.PRODUCTO_CARACTERISITICA_ID   = PC1.ID_PRODUCTO_CARACTERISITICA
                      AND C1.ID_CARACTERISTICA               = PC1.CARACTERISTICA_ID
                      AND C1.DESCRIPCION_CARACTERISTICA      = :caractCanales
                      AND SPC1.ESTADO                        = :estado  ) NUMERO_CANALES  
                    FROM INFO_SERVICIO_PROD_CARACT SPC,
                      ADMI_PRODUCTO_CARACTERISTICA PC,
                      ADMI_CARACTERISTICA C
                    WHERE SPC.SERVICIO_ID               = :idServicio
                    AND SPC.PRODUCTO_CARACTERISITICA_ID = PC.ID_PRODUCTO_CARACTERISITICA
                    AND C.ID_CARACTERISTICA             = PC.CARACTERISTICA_ID
                    AND C.DESCRIPCION_CARACTERISTICA    = :caractNumero
                    AND SPC.ESTADO                     != :estadoNumero ";

        $objRsm->addScalarResult(strtoupper('ID_SERVICIO_PROD_CARACT'), 'idNumero', 'string');
        $objRsm->addScalarResult(strtoupper('NUMERO'), 'numero', 'string');
        $objRsm->addScalarResult(strtoupper('ESTADO'), 'estado', 'string');
        $objRsm->addScalarResult(strtoupper('DOMINIO'), 'idDominio', 'string');
        $objRsm->addScalarResult(strtoupper('CLAVE'), 'idClave', 'string');
        $objRsm->addScalarResult(strtoupper('NUMERO_CANALES'), 'idNumeroCanales', 'string');

        $objQuery->setParameter("idServicio", $intServicio);
        
        $objQuery->setParameter("caractNumero", 'NUMERO');
        $objQuery->setParameter("caractCanales", 'NUMERO CANALES');
        $objQuery->setParameter("caractClave", 'CLAVE');
        $objQuery->setParameter("caractDominio", 'DOMINIO');
        $objQuery->setParameter("estado", 'Activo');
        $objQuery->setParameter("estadoNumero", 'Eliminado');
        

        $objQuery->setSQL($strSql);

        $arrayData = $objQuery->getResult();
        
        
        foreach($arrayData as $arrayLinea)
        {
            $strDominio = '';
            $strClave   = '';
            $strCanales = '';

            if($arrayLinea['idDominio'] > 0 )
            {
                $objSpcDominio = $this->_em->getRepository("schemaBundle:InfoServicioProdCaract")->find($arrayLinea['idDominio']);
                if(is_object($objSpcDominio))
                {
                    $strDominio = $objSpcDominio->getValor();
                }
            }

            if($arrayLinea['idClave'] > 0 )
            {
                $objSpcClave = $this->_em->getRepository("schemaBundle:InfoServicioProdCaract")->find($arrayLinea['idClave']);
                if(is_object($objSpcClave))
                {
                    $strClave = $objSpcClave->getValor();
                }
            }

            if($arrayLinea['idNumeroCanales'] > 0 )
            {
                $objSpcCanales = $this->_em->getRepository("schemaBundle:InfoServicioProdCaract")->find($arrayLinea['idNumeroCanales']);                    
                if(is_object($objSpcCanales))
                {
                    $strCanales = $objSpcCanales->getValor();
                }
            }

            $arrayDataLineas[] = array(
                    'idNumero'          => $arrayLinea['idNumero'],
                    'numero'            => $arrayLinea['numero'],
                    'estado'            => $arrayLinea['estado'],
                    'idDominio'         => $arrayLinea['idDominio'],
                    'dominio'           => $strDominio,
                    'idClave'           => $arrayLinea['idClave'],
                    'clave'             => $strClave,
                    'idNumeroCanales'   => $arrayLinea['idNumeroCanales'],
                    'numeroCanales'     => $strCanales );                   
        }        

        return $arrayDataLineas;
        
    }
    
    /**
     * Obtiene las caracteristicas del servicio 
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 07-05-2015
     * 
     * @param Integer        $idServicio
     * @param String         $estado
     * 
     * @return array $arraResult
     **/
    public function getCaracteristicasServicios($intServicioId ,$strEstado, $strCaraClienteEm)
    {
        $strQueryObtenerCaract = "SELECT INFO_SERVICIO_PROD_CARACT.ID_SERVICIO_PROD_CARACT,
                                    ADMI_PRODUCTO.DESCRIPCION_PRODUCTO,
                                    ADMI_CARACTERISTICA.DESCRIPCION_CARACTERISTICA,
                                    INFO_SERVICIO_PROD_CARACT.VALOR,
                                    INFO_SERVICIO_PROD_CARACT.ESTADO,
                                    INFO_SERVICIO_PROD_CARACT.FE_CREACION,
                                    INFO_SERVICIO_PROD_CARACT.FE_ULT_MOD
                                FROM INFO_SERVICIO_PROD_CARACT,
                                 ADMI_PRODUCTO_CARACTERISTICA,
                                 ADMI_PRODUCTO,
                                 ADMI_CARACTERISTICA
                                WHERE ADMI_PRODUCTO_CARACTERISTICA.ID_PRODUCTO_CARACTERISITICA = INFO_SERVICIO_PROD_CARACT.PRODUCTO_CARACTERISITICA_ID
                                 AND ADMI_PRODUCTO_CARACTERISTICA.PRODUCTO_ID                   = ADMI_PRODUCTO.ID_PRODUCTO
                                 AND ADMI_PRODUCTO_CARACTERISTICA.CARACTERISTICA_ID             = ADMI_CARACTERISTICA.ID_CARACTERISTICA
                                 AND INFO_SERVICIO_PROD_CARACT.SERVICIO_ID                      = :idServicioParam ";
        if ($strEstado != 'Todos')
        {
            $strQueryObtenerCaract = $strQueryObtenerCaract ."AND INFO_SERVICIO_PROD_CARACT.ESTADO = :estadoParam ";
        }
        if ($strCaraClienteEm != '')
        {
            $strQueryObtenerCaract = $strQueryObtenerCaract ."AND ADMI_CARACTERISTICA.DESCRIPCION_CARACTERISTICA <> :strCaraClienteEm ";
        }
        $strQueryObtenerCaract = $strQueryObtenerCaract ." ORDER BY INFO_SERVICIO_PROD_CARACT.ESTADO, ADMI_PRODUCTO.DESCRIPCION_PRODUCTO,
                                                           ADMI_CARACTERISTICA.DESCRIPCION_CARACTERISTICA";
        $stmt = $this->_em->getConnection()->prepare($strQueryObtenerCaract);
        $stmt->bindValue('idServicioParam', $intServicioId);
        if ($strEstado != 'Todos')
        {
            $stmt->bindValue('estadoParam', $strEstado);
        }        
        if ($strCaraClienteEm != '')
        {
            $stmt->bindValue('strCaraClienteEm', $strCaraClienteEm);
        }
        $stmt->execute();
        $arraResult = $stmt->fetchAll();
        return $arraResult;
    }
    
    /**
     * Documentación para el método 'getByServicioCaracteristicaAndProducto'.
     *
     * Método utilizado para obtener las servicios producto caracteristicas de un servicio
     *
     * @param int idServicio id del servicio a buscar las producto caracteristicas
     * @param string nombre de la caracteristica a buscar
     * @param int idProducto id del producto de la caracteristica
     * @param int start min de registros a buscar.
     * @param int limit max de registros a buscar.
     *
     * @return array arrayResultado
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 26-12-2015
    */
    public function getByServicioCaracteristicaAndProducto($idServicio, $caracteristica, $idProducto,$start,$limit)
    {
        $arrayResultado = array(
                                 'total' => 0,
                                 'data'  => array()
                               );
        
        
        if($idServicio > 0 && $idProducto >0 && $caracteristica!="")
        {
        
            $query = $this->_em->createQuery(null);
            
            $select = "select 
                        spc.id,
                        spc.valor as nombre,
                        spc.usrCreacion,
                        to_char(spc.feCreacion) as feCreacion ";
            $dql    = "from 
                        schemaBundle:AdmiCaracteristica carac,
                        schemaBundle:AdmiProductoCaracteristica apc,
                        schemaBundle:InfoServicio s,
                        schemaBundle:InfoServicioProdCaract spc
                    where 
                        apc.productoId       = :productoId
                    and s.id                 = :servicioId   
                    and apc.caracteristicaId = carac.id
                    and s.id                 = spc.servicioId
                    and spc.estado           = :estado
                    and spc.productoCaracterisiticaId   = apc.id
                    and carac.descripcionCaracteristica = :descripcionCaracteristica";

            $query->setParameter('servicioId', $idServicio);
            $query->setParameter('productoId', $idProducto);
            $query->setParameter('descripcionCaracteristica', $caracteristica);
            $query->setParameter('estado', 'Activo');
            
            //registros
            $query->setDQL($select.$dql);   
            if($start!='' && $limit!='') 
            {    
                $query->setFirstResult($start)->setMaxResults($limit);        
            }
            $arrayData = $query->getArrayResult();
            
            
            $arrayResultado = array(
                                    'total' => count($arrayData) ,
                                    'data'  => $arrayData
                                   );

        }
        
        return $arrayResultado;
    }
    
    /**
     * Documentación para el método 'getJsonInfoEnlaceDatos'.
     *
     * Método utilizado para obtener la información en json del enlace de datos de un servicio
     *
     * @param int idServicio id del servicio a buscar las producto caracteristicas
     * @param Service servicioTecnicoService service servicio tecnico
     *
     * @return json arrayResult
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 26-12-2015
    */
    public function getJsonInfoEnlaceDatos($idServicio,$servicioTecnicoService,$tipo)
    {
        $arrayResult = $this->getInfoEnlaceDatos($idServicio,$servicioTecnicoService,$tipo);
        
        return json_encode($arrayResult);
    }
    
    /**
     * Documentación para el método 'getInfoEnlaceDatos'.
     *
     * Método utilizado para obtener la información en array del enlace de datos de un servicio
     *
     * @param int idServicio id del servicio a buscar las producto caracteristicas
     * @param Service servicioTecnicoService service servicio tecnico
     *
     * @return array arrayInfoEnlace
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 26-12-2015
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 24-03-2018 - Se realiza validacion para cuano exista concentradores DC sin data tecnica no se caiga 
     *                           cuando requiera pedir datos tecnicos o caracteristicas
    */
    public function getInfoEnlaceDatos($idServicio,$servicioTecnicoService,$tipo)
    {
        $objServicioOrigenEnlace = null;
        $arrayInfoEnlace = array();
        $arrayInfoEnlace['cliente']      = "Sin Informacion";
        $arrayInfoEnlace['login']        = "Sin Informacion";
        $arrayInfoEnlace['login_aux']    = "Sin Informacion";
        $arrayInfoEnlace['producto']     = "Sin Informacion";
        $arrayInfoEnlace['capacidadUno'] = "Sin Informacion";
        $arrayInfoEnlace['capacidadDos'] = "Sin Informacion";
        
        $objServicio     = $this->_em->getRepository('schemaBundle:InfoServicio')->find($idServicio);
        $objProducto     = $objServicio->getProductoId();
        
        if($tipo=='ORIGEN')
        {
            $objEnlaceDatos  = $servicioTecnicoService->getServicioProductoCaracteristica($objServicio, 
                                                                                        'ENLACE_DATOS',
                                                                                        $objProducto
                                                                                        );
            if($objEnlaceDatos)
            {                                                                            
                //si no se obtienen datos del punto anterior para el historial y desenlace            
                $objServicioOrigenEnlace = $this->_em->getRepository('schemaBundle:InfoServicio')
                                                        ->find($objEnlaceDatos->getValor());
            }
            else
            {
                $objServicioOrigenEnlace = null;
            }
        }  
        elseif($tipo=='DESTINO')
        {
            $objServicioOrigenEnlace = $objServicio;
        }
        
        if($objServicioOrigenEnlace)
        {
            
                                                
            $objProductoOrigenEnlace     = $objServicioOrigenEnlace->getProductoId();
            $objPuntoOrigenEnlace        = $objServicioOrigenEnlace->getPuntoId();
             
            $objCapacidadUnoOrigenEnlace = $servicioTecnicoService->getServicioProductoCaracteristica($objServicioOrigenEnlace, 
                                                                                                    'CAPACIDAD1',
                                                                                                    $objProductoOrigenEnlace
                                                                                                    );
            
            $objCapacidadDosOrigenEnlace = $servicioTecnicoService->getServicioProductoCaracteristica($objServicioOrigenEnlace, 
                                                                                                    'CAPACIDAD2',
                                                                                                    $objProductoOrigenEnlace
                                                                                                    );
            $arrayInfoEnlace['cliente']      = sprintf("%s",$objPuntoOrigenEnlace->getPersonaEmpresaRolId()->getPersonaId());
            $arrayInfoEnlace['login']        = $objPuntoOrigenEnlace->getLogin();
            $arrayInfoEnlace['loginAux']     = $objServicioOrigenEnlace->getLoginAux();
            $arrayInfoEnlace['producto']     = $objProductoOrigenEnlace->getDescripcionProducto();                        
            $arrayInfoEnlace['capacidadUno'] = is_object($objCapacidadUnoOrigenEnlace)?$objCapacidadUnoOrigenEnlace->getValor():0;
            $arrayInfoEnlace['capacidadDos'] = is_object($objCapacidadDosOrigenEnlace)?$objCapacidadDosOrigenEnlace->getValor():0;
            
        }
        
        return $arrayInfoEnlace;
    }
    
    /**
     * Metodo encargado de determinar si un Servicio posee como caracteristica el valor enviado como parametro en conjunto con el tipo de 
     * caracteristica
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 22-02-2017     
     * 
     * Costo 8
     * 
     * @param  Array $arrayParametros [
     *                                  intIdServicio        Servicio involucrado
     *                                  strProceso           Proceso a verificar al armar consulta
     *                                  strCaracteristica    Caracteristica a buscar
     *                                  strValor             Valor a ser comparado
     *                                ]
     * @return String strExisteProcolo SI/NO
     */
    public function getExisteValorCaracteristicaPorProceso($arrayParametros)
    {
        $objResultSetMap = new ResultSetMappingBuilder($this->_em);
        $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);
        
        $strWhere = '';
        
        if(isset($arrayParametros['strProceso']) && !empty($arrayParametros['strProceso']))
        {
            if($arrayParametros['strProceso'] == 'PROTOCOLO')
            {
                $strWhere = ' AND ISPC.SERVICIO_ID  = :servicio ';
                $objNativeQuery->setParameter("servicio",$arrayParametros['intIdServicio']);
            }
        }
        
        $strSql = "    SELECT
                        CASE COUNT(*)
                          WHEN 0
                          THEN 'NO'
                          ELSE 'SI'
                        END EXISTE_VALOR
                      FROM 
                        DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT ISPC,
                        DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA ADC,
                        DB_COMERCIAL.ADMI_CARACTERISTICA C
                      WHERE 
                          ISPC.PRODUCTO_CARACTERISITICA_ID = ADC.ID_PRODUCTO_CARACTERISITICA
                      AND ADC.CARACTERISTICA_ID            = C.ID_CARACTERISTICA
                      AND C.DESCRIPCION_CARACTERISTICA     = :caracteristica
                      AND C.ESTADO                         = :estado
                      AND ISPC.ESTADO                      = :estado
                      AND ISPC.VALOR                       = :valor 
                      $strWhere ";


        $objResultSetMap->addScalarResult('EXISTE_VALOR', 'existeValor', 'string');        

        
        $objNativeQuery->setParameter("caracteristica", $arrayParametros['strCaracteristica']);
        $objNativeQuery->setParameter("estado",        'Activo');
        $objNativeQuery->setParameter("valor",          $arrayParametros['strValor']);

        $objNativeQuery->setSQL($strSql);

        $strExisteValor = $objNativeQuery->getSingleScalarResult();

        return $strExisteValor;
    }    
    /**
     * Documentación para el método 'getVrfsClientePorVlanAndEmpresa'.
     *
     * Método utilizado para obtener las servicios producto caracteristicas de un servicio
     *
     * @param int $idVlan id de la VLAN a buscar
     * @param int $empresaCod 
     * @param string $estado id del producto de la caracteristica
     *
     * @return object $arrayData[0]
     *
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.0 2016-08-22
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 2016-08-31    Se agrega filtro de Maximo de resultados para que solo recupere el primer registro de la consulta ejecutada
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 2016-09-08    Se corrige consulta a la base de datos para recuperar informacion de vlan de manera correcta
     * @since 1.1 2016-09-08
     * 
     * @since 1.0 2016-08-31
     */
    public function getVrfsClientePorVlanAndEmpresa($idVlan, $empresaCod, $estado)
    {
        $query = $this->_em->createQuery(null);

        $select = "select spc ";
        $dql    = "from 
                    schemaBundle:InfoServicioProdCaract spc,
                    schemaBundle:InfoServicio s,
                    schemaBundle:AdmiProducto ap,
                    schemaBundle:AdmiProductoCaracteristica apc,
                    schemaBundle:AdmiCaracteristica ac
                where 
                    s.id                              = spc.servicioId
                    and ap.id                         = s.productoId
                    and apc.productoId                = ap.id
                    and spc.productoCaracterisiticaId = apc.id
                    and apc.caracteristicaId          = ac.id
                    and ap.empresaCod                 = :empresaCod
                    and spc.estado                    = :estado
                    and spc.valor                     = :valor
                    and ac.descripcionCaracteristica  = :nombreCaracteristica";

        $query->setParameter('empresaCod', $empresaCod);
        $query->setParameter('valor', $idVlan);
        $query->setParameter('estado', $estado);
        $query->setParameter('nombreCaracteristica', "VLAN");

        $query->setDQL($select.$dql);  
        $query->setMaxResults(1);
        $data = $query->getOneOrNullResult();
        
        return $data;
    }
    
    /**
     * 
     * Metodo encargado de validar si un AsPrivado existe o no configurado ya en otro Servicio ( INTMPLS ) o en otro cliente ( L3MPLS )
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0  21-02-2017
     * 
     * @param  Array $arrayParametros 
     *                              [
     *                                  intIdPersonaRol       Persona rol del cliente
     *                                  strNombreTecnico      Nombre tecnico del servicio a revisar ( L3MPLS / INTMPLS )
     *                                  intAsPrivado          El asprivado enviado a ser verificado
     *                              ]
     * @return String $strExisteAsPrivado  SI o NO dependiendo si el asprivado se encuentra o no en otro servicio configurado
     */
    public function getAsPrivadoExiste($arrayParametros)
    {
        $objResultSetMap = new ResultSetMappingBuilder($this->_em);
        $objNativeQuery = $this->_em->createNativeQuery(null, $objResultSetMap);
        
        $strSql = "SELECT DB_COMERCIAL.TECNK_SERVICIOS.GET_EXISTE_AS_PRIVADO(:IDPERSONAROL,:ASPRIVADO,:NOMBRE_TECNICO) EXISTE_AS_PRIVADO FROM DUAL";

        $objResultSetMap->addScalarResult('EXISTE_AS_PRIVADO',   'existeAsPrivado',   'string');

        $objNativeQuery->setParameter("IDPERSONAROL",   $arrayParametros['intIdPersonaRol']);
        $objNativeQuery->setParameter("NOMBRE_TECNICO", $arrayParametros['strNombreTecnico']);
        $objNativeQuery->setParameter("ASPRIVADO",      $arrayParametros['intAsPrivado']);

        $objNativeQuery->setSQL($strSql);

        $strExisteAsPrivado = $objNativeQuery->getSingleScalarResult();

        return $strExisteAsPrivado;
    }

    /**
     * getObtenerValorDelServicio
     *
     * Método que retorna el valor de un servicio en base a una caracteristica y un servicio id.
     *
     * Costo = 15.
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.0 21-01-2018
     *
     * @param array $arrayParametros{
     *                                  intServicioId:               Integer: Id del servicio.
     *                                  strDescripcionCaracteristica String:  Descripción de la caracteristica.
     *                              }
     *
     * @return array $arrayRespuesta : Retorna el listado de las tareas interdepartamentales.
     */
    public function getObtenerValorDelServicio($arrayParametros)
    {
        $arrayRespuesta                = array();
        $arrayRespuesta['resultado']   = "";
        try
        {
            $objQuery       = $this->_em->createQuery();
            $strSelect      = "SELECT ispc.valor VALOR
                               FROM schemaBundle:InfoServicioProdCaract ispc,
                                 schemaBundle:AdmiProductoCaracteristica apc,
                                 schemaBundle:AdmiCaracteristica ac
                               WHERE ispc.productoCaracterisiticaId = apc.id
                               AND apc.caracteristicaId             = ac.id
                               AND ac.descripcionCaracteristica     = :strDescripcionCaracteristica
                               AND ispc.servicioId                  = :intServicioId";

            $objQuery->setParameter("intServicioId", $arrayParametros['intServicioId']);
            $objQuery->setParameter("strDescripcionCaracteristica", $arrayParametros['strDescripcionCaracteristica']);

            $objQuery->setDQL($strSelect);
            $arrayRespuesta = $objQuery->getOneOrNullResult();
        }
        catch (\Exception $e)
        {
            error_log('InfoDetalleAsignacionRepository->getTareaInterdepartamentales()  '.$e->getMessage());
        }
        return $arrayRespuesta;
    }
    
    
    /**
     * validaProgresoTarea
     *
     * Método que indica si una tarea tiene el id del progreso del acta.
     *
     * Costo: 1
     * 
     * @author Ronny Morán Chancay. <rmoranc@telconet.ec>
     * @version 1.0 09-07-2019
     * 
     * 
     * Se modifica el método para que permita validar si la tarea tiene progreso 
     *
     * * Costo: 9
     *
     * 
     * @author Ronny Morán Chancay. <rmoranc@telconet.ec>
     * @version 1.1 31-03-2020
     *  
     * Se agrega validaciones para progresos de INGRESO_FIBRA en las tareas de soporte, instalación e interdepartamental.
     * 
     * @author Ronny Morán Chancay. <rmoranc@telconet.ec>
     * @version 1.2 09-07-2020
     * 
     * Se agrega validaciones para progresos de CONFIRMA_IP_SERVICIO en las tareas de soporte TN.
     * 
     * @author Antonio Ayala. <afayala@telconet.ec>
     * @version 1.3 10-07-2020 Se agrega validaciones para progreso de INGRESO_FIBRA e INGRESO_MATERIALES
     *                         para cableado estructurado.
     *
     * @author Ronny Morán. <rmoranc@telconet.ec>
     * @version 1.4 04-06-2021 Se modifica validación en variable $intIdCaso para determinar si es una tarea de soporte.
     * 
     * @author Steven Ruano. <sruano@telconet.ec>
     * @version 1.5 20-03-2023 Se agrega validacion para el servicio Housing que no tiene progreso Fibra, Material o Acta.
     * 
     * @author Emmanuel Martillo. <emartillo@telconet.ec>
     * @version 1.6 28-02-2023 Se agrega validaciones para progresos PROG_SOPORTE_EN_FIBRA, PROG_SOPORTE_EN_MATERIALES, 
     *                         PROG_INSTALACION_EN_FIBRA, PROG_INSTALACION_EN_MATERIALES de Ecuanet.
     * 
     * @author Steven Ruano <sruano@telconet.ec>
     * @version 1.7 13-04-2023 Se modifica la validacion para que obtenga el nombre tecnico de manera generar para servicios que no tengan
     *                         progreso material.
     * 
     * @param array $arrayParametros{
     *                                  detalleid:               Integer: Id del detalle,
     *                                   intProgresosId:          Array: Id de progesos,
     *                                   intEmpresaCod:           Integer: Id de la Empresa
     *                              }
     *
     * @return String $strTieneProgreso
     */
    public function validaProgresoTarea($arrayParametros)
    {
        $objResultSetMap = new ResultSetMappingBuilder($this->_em);
        $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);

        $strTieneProgreso               = 'NO';
        $strProgresoActa                = 'ACTAS';
        $strProgresoFibra               = 'INGRESO_FIBRA';
        $strProgresoMateriales          = 'INGRESO_MATERIALES';
        $strProgresoConfirIpServ        = 'CONFIRMA_IP_SERVICIO';
        $intIdEmpresaMD                 = '18';
        $strPrefijoEmpresaTN            = 'TN';
        $boolNoTieneMaterial            = false;
        $intIdEmpresaEN                 = '33';
        $strCodigoProgresoIn            = $arrayParametros['tipoProgreso'];
        $intIdCaso                      = $arrayParametros['casoId'];
        $intEmpresaId                   = ''.$arrayParametros['intEmpresaCod'];
        $intDetalleId                   = $arrayParametros['detalleId'];
        $strNombreTecnico               = $arrayParametros['nombreTecnico'];
        $strNombreParametroCab          = 'LISTA_CONFIMAR_SERVICIO_CON_SERVIDOR_ALQUILER';
        $boolEsInterdep                 = isset($arrayParametros['esInterdep']) ? $arrayParametros['esInterdep'] : false;
        $strPrefijoEmpresa              = $arrayParametros['strPrefijoEmpresa'];
        $arrayTipoProgreso              = null;
        $boolEsTn                       = false;
        $arrayIdProgresos               = null;
        $strIdsProgSoportMDFibra        = '';
        $strIdsProgSoportMDMaterial     = '';
        $strIdsProgSoportENFibra        = '';
        $strIdsProgSoportENMaterial     = '';
        $strIdsProgSoportTNFibra        = '';
        $strIdsProgSoportTNMaterial     = '';
        $strIdsProgInstMDRuta           = '';    
        $strIdsProgInstMDMater          = '';
        $strIdsProgInstTNActa           = '';
        $strIdsProgInstTNRuta           = '';
        $strIdsProgInstTNMaterial       = '';
        $strIdsProgSoportTNConfirm      = '';
        
        $arrayTipoProgreso                  = null;
        $arrayIdProgresos                   = null;
        $strIdsProgSoportMDFibra            = '';
        $strIdsProgSoportMDMaterial         = '';
        $strIdsProgSoportTNFibra            = '';
        $strIdsProgSoportTNMaterial         = '';
        $strIdsProgInstMDRuta               = '';    
        $strIdsProgInstMDMater              = '';
        $strIdsProgInstENRuta               = '';    
        $strIdsProgInstENMater              = '';
        $strIdsProgInstTNActa               = '';
        $strIdsProgInstTNRuta               = '';
        $strIdsProgInstTNMaterial           = '';
                
        try
        {
        
        $objIdsProgSoportMDFibra = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('IDS_PROGRESOS_TAREAS', 
                        '', 
                        '', 
                        '', 
                        'PROG_SOPORTE_MD_FIBRA', 
                        '', 
                        '', 
                        ''
                    );

        if (is_array($objIdsProgSoportMDFibra))
        {
            $strIdsProgSoportMDFibra = !empty($objIdsProgSoportMDFibra['valor2']) ? $objIdsProgSoportMDFibra['valor2'] : "";
        }
        
        $objIdsProgSoportMDMaterial = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('IDS_PROGRESOS_TAREAS', 
                        '', 
                        '', 
                        '', 
                        'PROG_SOPORTE_MD_MATERIALES', 
                        '', 
                        '', 
                        ''
                    );

        if (is_array($objIdsProgSoportMDMaterial))
        {
            $strIdsProgSoportMDMaterial = !empty($objIdsProgSoportMDMaterial['valor2']) ? $objIdsProgSoportMDMaterial['valor2'] : "";
        }
        
        
        $objIdsProgSoportENFibra = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('IDS_PROGRESOS_TAREAS', 
                                '', 
                                '', 
                                '', 
                                'PROG_SOPORTE_EN_FIBRA', 
                                '', 
                                '', 
                                ''
                            );

        if (is_array($objIdsProgSoportENFibra))
        {
            $strIdsProgSoportENFibra = !empty($objIdsProgSoportENFibra['valor2']) ? $objIdsProgSoportENFibra['valor2'] : "";
        }
        
        $objIdsProgSoportENMaterial = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('IDS_PROGRESOS_TAREAS', 
                        '', 
                        '', 
                        '', 
                        'PROG_SOPORTE_EN_MATERIALES', 
                        '', 
                        '', 
                        ''
                    );

        if (is_array($objIdsProgSoportENMaterial))
        {
            $strIdsProgSoportENMaterial = !empty($objIdsProgSoportENMaterial['valor2']) ? $objIdsProgSoportENMaterial['valor2'] : "";
        }        
        $objIdsProgSoportTNFibra = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('IDS_PROGRESOS_TAREAS', 
                        '', 
                        '', 
                        '', 
                        'PROG_SOPORTE_TN_FIBRA', 
                        '', 
                        '', 
                        ''
                    );

        if (is_array($objIdsProgSoportTNFibra))
        {
            $strIdsProgSoportTNFibra = !empty($objIdsProgSoportTNFibra['valor2']) ? $objIdsProgSoportTNFibra['valor2'] : "";
        }
        
        $objIdsProgSoportTNMaterial = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('IDS_PROGRESOS_TAREAS', 
                        '', 
                        '', 
                        '', 
                        'PROG_SOPORTE_TN_MATERIALES', 
                        '', 
                        '', 
                        ''
                    );

        if (is_array($objIdsProgSoportTNMaterial))
        {
            $strIdsProgSoportTNMaterial = !empty($objIdsProgSoportTNMaterial['valor2']) ? $objIdsProgSoportTNMaterial['valor2'] : "";
        }

        $objIdsProgInstMDFibra = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('IDS_PROGRESOS_TAREAS', 
                        '', 
                        '', 
                        '', 
                        'PROG_INSTALACION_MD_FIBRA', 
                        '', 
                        '', 
                        ''
                    );

        if (is_array($objIdsProgInstMDFibra))
        {
            $strIdsProgInstMDRuta = !empty($objIdsProgInstMDFibra['valor2']) ? $objIdsProgInstMDFibra['valor2'] : "";
        }
        
        $objIdsProgInstMDMater = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('IDS_PROGRESOS_TAREAS', 
                        '', 
                        '', 
                        '', 
                        'PROG_INSTALACION_MD_MATERIALES', 
                        '', 
                        '', 
                        ''
                    );

        if (is_array($objIdsProgInstMDMater))
        {
            $strIdsProgInstMDMater = !empty($objIdsProgInstMDMater['valor2']) ? $objIdsProgInstMDMater['valor2'] : "";
        }
        
        $objIdsProgInstENFibra = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('IDS_PROGRESOS_TAREAS', 
                        '', 
                        '', 
                        '', 
                        'PROG_INSTALACION_EN_FIBRA', 
                        '', 
                        '', 
                        ''
                    );

        if (is_array($objIdsProgInstENFibra))
        {
            $strIdsProgInstENRuta = !empty($objIdsProgInstENFibra['valor2']) ? $objIdsProgInstENFibra['valor2'] : "";
        }
        
        $objIdsProgInstENMater = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('IDS_PROGRESOS_TAREAS', 
                        '', 
                        '', 
                        '', 
                        'PROG_INSTALACION_EN_MATERIALES', 
                        '', 
                        '', 
                        ''
                    );

        if (is_array($objIdsProgInstENMater))
        {
            $strIdsProgInstENMater = !empty($objIdsProgInstENMater['valor2']) ? $objIdsProgInstENMater['valor2'] : "";
        }

        $objIdsProgInstTNActa = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('IDS_PROGRESOS_TAREAS', 
                        '', 
                        '', 
                        '', 
                        'PROG_INSTALACION_TN_ACTA', 
                        '', 
                        '', 
                        ''
                    );

        if (is_array($objIdsProgInstTNActa))
        {
            $strIdsProgInstTNActa = !empty($objIdsProgInstTNActa['valor2']) ? $objIdsProgInstTNActa['valor2'] : "";
        }
        
        $objIdsProgInstTNFibra = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('IDS_PROGRESOS_TAREAS', 
                        '', 
                        '', 
                        '', 
                        'PROG_INSTALACION_TN_FIBRA', 
                        '', 
                        '', 
                        ''
                    );

        if (is_array($objIdsProgInstTNFibra))
        {
            $strIdsProgInstTNRuta = !empty($objIdsProgInstTNFibra['valor2']) ? $objIdsProgInstTNFibra['valor2'] : "";
        }
        
        $objIdsProgInstTNMaterial = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('IDS_PROGRESOS_TAREAS', 
                        '', 
                        '', 
                        '', 
                        'PROG_INSTALACION_TN_MATERIALES', 
                        '', 
                        '', 
                        ''
                    );

        if (is_array($objIdsProgInstTNMaterial))
        {
            $strIdsProgInstTNMaterial = !empty($objIdsProgInstTNMaterial['valor2']) ? $objIdsProgInstTNMaterial['valor2'] : "";
        }
        
        $objIdsProgSoportTNConfirIp = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('IDS_PROGRESOS_TAREAS', 
                        '', 
                        '', 
                        '', 
                        'PROG_SOPORTE_TN_CONFIRMAIP', 
                        '', 
                        '', 
                        ''
                    );

        if (is_array($objIdsProgSoportTNConfirIp))
        {
            $strIdsProgSoportTNConfirm = !empty($objIdsProgSoportTNConfirIp['valor2']) ? $objIdsProgSoportTNConfirIp['valor2'] : "";
        }
        
        if(($intIdCaso != 0) || ($boolEsInterdep) )
        {
            if($intEmpresaId === $intIdEmpresaMD)
            {
                if($strCodigoProgresoIn === $strProgresoFibra)
                {
                    
                            $arrayIdProgresos   = explode (",", $strIdsProgSoportMDFibra);  
                            $arrayTipoProgreso  = $arrayIdProgresos;
                }
                else if($strCodigoProgresoIn === $strProgresoMateriales)
                {
                    
                            $arrayIdProgresos   = explode (",", $strIdsProgSoportMDMaterial);  
                            $arrayTipoProgreso  = $arrayIdProgresos;
                }
            }
            elseif($intEmpresaId === $intIdEmpresaEN)
            {
                if($strCodigoProgresoIn === $strProgresoFibra)
                {
                    
                            $arrayIdProgresos   = explode (",", $strIdsProgSoportENFibra);  
                            $arrayTipoProgreso  = $arrayIdProgresos;
                }
                else if($strCodigoProgresoIn === $strProgresoMateriales)
                {
                    
                            $arrayIdProgresos   = explode (",", $strIdsProgSoportENMaterial);  
                            $arrayTipoProgreso  = $arrayIdProgresos;
                }
            }
            else
            {
                if($strCodigoProgresoIn === $strProgresoFibra)
                {
                            $arrayIdProgresos   = explode (",", $strIdsProgSoportTNFibra);
                            $arrayTipoProgreso  = $arrayIdProgresos;      
                }        
                else if($strCodigoProgresoIn === $strProgresoMateriales)
                {
                            $arrayIdProgresos   = explode (",", $strIdsProgSoportTNMaterial);
                            $arrayTipoProgreso  = $arrayIdProgresos;      
                }
                else if($strCodigoProgresoIn === $strProgresoConfirIpServ)
                {
                            $arrayIdProgresos   = explode (",", $strIdsProgSoportTNConfirm);
                            $arrayTipoProgreso  = $arrayIdProgresos;       
                }
                
            }
        }
        else
        {
            if($intEmpresaId === $intIdEmpresaMD)
            {
                if($strCodigoProgresoIn === $strProgresoFibra)
                {           
                            $arrayIdProgresos   = explode (",", $strIdsProgInstMDRuta);
                            $arrayTipoProgreso  = $arrayIdProgresos;    
                }
                else if($strCodigoProgresoIn === $strProgresoMateriales)
                {
                            $arrayIdProgresos   = explode (",", $strIdsProgInstMDMater);
                            $arrayTipoProgreso  = $arrayIdProgresos;    
                }
            }
            elseif($intEmpresaId === $intIdEmpresaEN)
            {
                if($strCodigoProgresoIn === $strProgresoFibra)
                {           
                            $arrayIdProgresos   = explode (",", $strIdsProgInstENRuta);
                            $arrayTipoProgreso  = $arrayIdProgresos;    
                }
                else if($strCodigoProgresoIn === $strProgresoMateriales)
                {
                            $arrayIdProgresos   = explode (",", $strIdsProgInstENMater);
                            $arrayTipoProgreso  = $arrayIdProgresos;    
                }
            }
            else
            {
                if($strCodigoProgresoIn === $strProgresoActa)
                {
                            $arrayIdProgresos       = explode (",", $strIdsProgInstTNActa);
                            $arrayTipoProgreso      = $arrayIdProgresos;
                }
                else if($strCodigoProgresoIn === $strProgresoFibra)
                {
                            $arrayIdProgresos       = explode (",", $strIdsProgInstTNRuta);
                            $arrayTipoProgreso      = $arrayIdProgresos;
                }
                else if($strCodigoProgresoIn === $strProgresoMateriales)
                {
                            $arrayIdProgresos       = explode (",", $strIdsProgInstTNMaterial);
                            $arrayTipoProgreso      = $arrayIdProgresos;
                }
            }
        }
        
        if($strPrefijoEmpresaTN == $strPrefijoEmpresa)
        {
           $boolEsTn = true;
           
           if($boolEsTn)
            {
                $objEsAlquilerParametroCab =  $this->_em->getRepository('schemaBundle:AdmiParametroCab')
                        ->findOneBy(array( 'nombreParametro' => $strNombreParametroCab));

                $intIdParametroCab = $objEsAlquilerParametroCab->getId();

                $objEsAlquilerParametroDet =  $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                        ->findOneBy(array('parametroId' => $intIdParametroCab)); 
                
                
                $arrayNombreTecnico = !empty($objEsAlquilerParametroDet) ?
                        json_decode($objEsAlquilerParametroDet->getValor1(), true) :
                        null;     

                if (is_array($arrayNombreTecnico))
                {
                    $boolNoTieneMaterial = in_array($strNombreTecnico,$arrayNombreTecnico);
                }

            }  
        }


            
        
        if(isset($intDetalleId) && 
          !empty($intDetalleId))
        {   
            if(isset($arrayTipoProgreso) && 
               !empty($arrayTipoProgreso))
                {
                
                $strSql = " SELECT 
                                IPP.ID_PROGRESO_PORCENTAJE 
                            FROM 
                                DB_SOPORTE.INFO_PROGRESO_PORCENTAJE IPP
                            WHERE
                                IPP.EMPRESA_ID = :idempresa AND
                                IPP.ID_PROGRESO_PORCENTAJE = (
                                SELECT PROGRESO_PORCENTAJE_ID FROM DB_SOPORTE.INFO_PROGRESO_TAREA IPT
                                WHERE IPT.DETALLE_ID = :detalleId
                                AND IPT.PROGRESO_PORCENTAJE_ID IN (:idprogreso))
                            ";

                $objResultSetMap->addScalarResult('ID_PROGRESO_PORCENTAJE', 'idProgresoPorct', 'string');
                $objNativeQuery->setParameter("detalleId", $intDetalleId);
                $objNativeQuery->setParameter("idprogreso", $arrayTipoProgreso);
                $objNativeQuery->setParameter("idempresa", $intEmpresaId);
                $objNativeQuery->setSQL($strSql);
                
                $strExisteValor = $objNativeQuery->getResult();
                
                if(count($strExisteValor)>0)
                {
                    $strTieneProgreso = 'SI';
                    return $strTieneProgreso;
                }
                elseif(count($strExisteValor)<=0 && $boolNoTieneMaterial && $boolEsTn)
                {
                    $strTieneProgreso = 'NO TIENE PROGRESO PORQUE ES UN SERVICIO CON NOMBRE TECNICO' . $strNombreTecnico;
                }
                else
                {
                     $strTieneProgreso = 'NO';
                }
            }
            else
            {
                $strTieneProgreso = 'NO TIENE IDPROGRESO A BUSCAR';
            }
                         
        }
        else
        {
            $strTieneProgreso = 'NO TIENE IDDETALLE';
        }
}
      catch (\Exception $e)
        {
            error_log('InfoServicioProdCaractRepository->validaProgresoTarea()  '.$e->getMessage());
        }        
        return $strTieneProgreso;
    }

    
   
    /**
     * 
     * getInfoTareaByServicioId
     *
     * Método que devuelve información de la tarea según el id del servicio
     *
     *
     * Costo = 31
     * 
     * @author Ronny Morán Chancay. <rmoranc@telconet.ec>
     * @version 1.0 25-07-2019
     *
     * @author David Leon. <mdleon@telconet.ec>
     * @version 1.1 25-09-2020 Se valida que exista en el arreglo el valor tareaId.
     * 
     * @param Integer $arrayParametrosInfo
     *
     * @return array $arrayData
     */
    public function getInfoTareaByServicioId($arrayParametrosInfo)
    {
        try
        {
            $objRsm = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            if (isset($arrayParametrosInfo['tareaId']) && !empty($arrayParametrosInfo['tareaId']) && $arrayParametrosInfo['tareaId'] !== "")
            {
                $strSql = "SELECT INFCM.ID_COMUNICACION, INFPE.PERSONA_ID, IDS.SERVICIO_ID, IFDT.ID_DETALLE, IFDT.TAREA_ID 
                            FROM DB_COMERCIAL.INFO_PUNTO IFPT,
                                    DB_COMUNICACION.INFO_COMUNICACION INFCM, 
                                    DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL INFPE,
                                    DB_SOPORTE.INFO_DETALLE IFDT,
                                    DB_COMERCIAL.INFO_DETALLE_SOLICITUD IDS
                            WHERE IFPT.ID_PUNTO = INFCM.REMITENTE_ID
                                    AND INFCM.DETALLE_ID = IFDT.ID_DETALLE
                                    AND INFPE.ID_PERSONA_ROL = IFPT.PERSONA_EMPRESA_ROL_ID
                                    AND IDS.ID_DETALLE_SOLICITUD = IFDT.DETALLE_SOLICITUD_ID
                                    AND INFCM.EMPRESA_COD = :codEmpresa
                                    AND IDS.SERVICIO_ID = :servicioId
                                    AND IFDT.TAREA_ID = :tareaId";
        
                $objRsm->addScalarResult(strtoupper('ID_COMUNICACION'), 'comunicacionId', 'string');
                $objRsm->addScalarResult(strtoupper('PERSONA_ID'), 'personaId', 'string');
                $objRsm->addScalarResult(strtoupper('SERVICIO_ID'), 'servicioId', 'string');
                $objRsm->addScalarResult(strtoupper('ID_DETALLE'), 'detalleId', 'string');
                $objRsm->addScalarResult(strtoupper('TAREA_ID'), 'tareaId', 'string');
            
                $objQuery->setParameter("servicioId",$arrayParametrosInfo['intServicioId']);
                $objQuery->setParameter("codEmpresa",$arrayParametrosInfo['intEmpresaCod']);
                $objQuery->setParameter("tareaId",$arrayParametrosInfo['tareaId']);
            }
            else
            {
                $strSql = "SELECT INFCM.ID_COMUNICACION, INFPE.PERSONA_ID, IDS.SERVICIO_ID, IFDT.ID_DETALLE, IFDT.TAREA_ID 
                            FROM DB_COMERCIAL.INFO_PUNTO IFPT,
                                    DB_COMUNICACION.INFO_COMUNICACION INFCM, 
                                    DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL INFPE,
                                    DB_SOPORTE.INFO_DETALLE IFDT,
                                    DB_COMERCIAL.INFO_DETALLE_SOLICITUD IDS
                            WHERE IFPT.ID_PUNTO = INFCM.REMITENTE_ID
                                    AND INFCM.DETALLE_ID = IFDT.ID_DETALLE
                                    AND INFPE.ID_PERSONA_ROL = IFPT.PERSONA_EMPRESA_ROL_ID
                                    AND IDS.ID_DETALLE_SOLICITUD = IFDT.DETALLE_SOLICITUD_ID
                                    AND INFCM.EMPRESA_COD = :codEmpresa
                                    AND IDS.SERVICIO_ID = :servicioId";
        
                $objRsm->addScalarResult(strtoupper('ID_COMUNICACION'), 'comunicacionId', 'string');
                $objRsm->addScalarResult(strtoupper('PERSONA_ID'), 'personaId', 'string');
                $objRsm->addScalarResult(strtoupper('SERVICIO_ID'), 'servicioId', 'string');
                $objRsm->addScalarResult(strtoupper('ID_DETALLE'), 'detalleId', 'string');
                $objRsm->addScalarResult(strtoupper('TAREA_ID'), 'tareaId', 'string');
            
                $objQuery->setParameter("servicioId",$arrayParametrosInfo['intServicioId']);
                $objQuery->setParameter("codEmpresa",$arrayParametrosInfo['intEmpresaCod']);
            }
            
            
            $objQuery->setSQL($strSql);
            $arrayData = $objQuery->getArrayResult();
            
        }
        catch (\Exception $e)
        {
            error_log('InfoServicioProdCaractRepository->getInfoTareaByServicioId()  '.$e->getMessage());
        }
        return $arrayData[0];
    }

    /**
     * Documentación para el método 'getCaracteristicasProductoPorServicio'.
     *
     * Obtiene el listado de los valores de las características del producto por el servicio
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 05-03-2020
     *
     * @param Array $arrayParametros [
     *                                  intIdServicio,           id del servicio
     *                                  arrayIdCaracteristicas   arreglo con los nombres de las características
     *                               ]
     *
     * @return Array $arrayResultado [
     *                                  'status'    => estado de respuesta de la operación 'OK' o 'ERROR',
     *                                  'result'    => arreglo con la información de las características o mensaje de error
     *                               ]
     *
     * costoQuery: 16
     */
    public function getCaracteristicasProductoPorServicio($arrayParametros)
    {
        try
        {
            $objRsm     = new ResultSetMappingBuilder($this->_em);
            $objQuery   = $this->_em->createNativeQuery(null, $objRsm);
            $strSql     = " SELECT C.DESCRIPCION_CARACTERISTICA, CARACT.VALOR 
                            FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT CARACT
                            INNER JOIN DB_COMERCIAL.INFO_SERVICIO                 SERV ON SERV.ID_SERVICIO                 = CARACT.SERVICIO_ID
                            INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA  PROD 
                                                                           ON PROD.ID_PRODUCTO_CARACTERISITICA = CARACT.PRODUCTO_CARACTERISITICA_ID
                            INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA           C    ON C.ID_CARACTERISTICA              = PROD.CARACTERISTICA_ID
                            LEFT JOIN DB_COMERCIAL.INFO_PLAN_CAB                  PLC  ON PLC.ID_PLAN                      = SERV.PLAN_ID
                            LEFT JOIN DB_COMERCIAL.INFO_PLAN_DET                  PLD  ON PLD.PLAN_ID                      = PLC.ID_PLAN
                            WHERE (
                            ( SERV.PLAN_ID IS NULL      AND PROD.PRODUCTO_ID = SERV.PRODUCTO_ID ) OR
                            ( SERV.PLAN_ID IS NOT NULL  AND PROD.PRODUCTO_ID = PLD.PRODUCTO_ID  ) )
                            AND PROD.ESTADO = :ESTADO
                            AND CARACT.ESTADO = :ESTADO
                            AND SERV.ID_SERVICIO = :ID_SERVICIO
                            AND C.ID_CARACTERISTICA IN (:ARRAY_ID_CARACTERISTICA)";
            $objRsm->addScalarResult(strtoupper('DESCRIPCION_CARACTERISTICA'), 'caracteristica', 'string');
            $objRsm->addScalarResult(strtoupper('VALOR'),                      'valor',          'string');
            $objQuery->setParameter("ESTADO",                  'Activo');
            $objQuery->setParameter("ID_SERVICIO",             $arrayParametros['intIdServicio']);
            $objQuery->setParameter("ARRAY_ID_CARACTERISTICA", array_values($arrayParametros['arrayIdCaracteristicas']));
            $objQuery->setSQL($strSql);
            $arrayData = $objQuery->getArrayResult();
            $arrayResultado = array(
                'status' => 'OK',
                'result' => $arrayData
            );
        }
        catch (\Exception $e)
        {
            $arrayResultado = array(
                'status' => 'ERROR',
                'result' => $e->getMessage()
            );
        }
        return $arrayResultado;
    }

    /**
     * Documentación para el método 'getServiciosPorProdAdicionalesSafeCity'.
     *
     * Obtiene el listado de los servicios adicionales por el producto Datos SafeCity.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 26-04-2021
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 27-09-2021 - Se agrega validación de los estados de los servicios por parámetros
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 23-08-2022 - Se agrega validación para permitir las relaciones de los servicios con tipo de red
     *
     * @param Array $arrayParametros [
     *                                  intIdProducto,        id del producto
     *                                  intIdServicio,        id del servicio relacionado
     *                                  strNombreParametro,   nombre del parametro
     *                                  strUsoDetalles,       uso de los detalles
     *                               ]
     *
     * @return Array $arrayResultado [
     *                                  'status'    => estado de respuesta de la operación 'OK' o 'ERROR',
     *                                  'result'    => arreglo con el listado de los servicios adicionales o mensaje de error
     *                               ]
     *
     * costoQuery: 21
     */
    public function getServiciosPorProdAdicionalesSafeCity($arrayParametros)
    {
        try
        {
            $objRsm     = new ResultSetMappingBuilder($this->_em);
            $objQuery   = $this->_em->createNativeQuery(null, $objRsm);
            $strSql     = " SELECT SER.ID_SERVICIO, SER.ESTADO
                            FROM DB_COMERCIAL.INFO_SERVICIO SER
                            INNER JOIN DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT CARACT
                                                                             ON CARACT.SERVICIO_ID               = SER.ID_SERVICIO
                            INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA  PROD 
                                                                             ON PROD.ID_PRODUCTO_CARACTERISITICA = CARACT.PRODUCTO_CARACTERISITICA_ID
                            INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA C    ON C.ID_CARACTERISTICA              = PROD.CARACTERISTICA_ID
                            INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET    DET  ON DET.VALOR6                       = C.DESCRIPCION_CARACTERISTICA
                            INNER JOIN DB_GENERAL.ADMI_PARAMETRO_CAB    CAB  ON CAB.ID_PARAMETRO                 = DET.PARAMETRO_ID
                            WHERE CARACT.VALOR = :ID_SERVICIO
                                AND CAB.NOMBRE_PARAMETRO = :NOMBRE_PARAMETRO
                                AND DET.VALOR1    = :ID_PRODUCTO
                                AND DET.VALOR2    = :USO_DETALLE
                                AND DET.VALOR4    = SER.PRODUCTO_ID
                                AND DET.VALOR7    IS NOT NULL
                                AND PROD.ESTADO   = :ESTADO
                                AND CARACT.ESTADO = :ESTADO
                                AND C.ESTADO      = :ESTADO
                                AND DET.ESTADO    = :ESTADO
                                AND CAB.ESTADO    = :ESTADO
                                AND SER.ESTADO    NOT IN (SELECT DET_E.VALOR2 FROM DB_GENERAL.ADMI_PARAMETRO_DET DET_E,
                                                        DB_GENERAL.ADMI_PARAMETRO_CAB CAB_E
                                                        WHERE CAB_E.ID_PARAMETRO = DET_E.PARAMETRO_ID
                                                            AND CAB_E.NOMBRE_PARAMETRO = :NOMBRE_PARAMETRO_ESTADO
                                                            AND DET_E.VALOR1 = :DETALLE_PARAMETRO_ESTADO
                                                            AND CAB_E.ESTADO = :ESTADO
                                                            AND DET_E.ESTADO = :ESTADO)";
            $objRsm->addScalarResult('ID_SERVICIO', 'idServicio', 'integer');
            $objRsm->addScalarResult('ESTADO',      'estado',     'string');
            $objQuery->setParameter("ESTADO",                   'Activo');
            $objQuery->setParameter("NOMBRE_PARAMETRO_ESTADO",  'NUEVA_RED_GPON_TN');
            $objQuery->setParameter("DETALLE_PARAMETRO_ESTADO", 'ESTADOS_SERVICIOS_NO_PERMITIDOS_FLUJO');
            $objQuery->setParameter("ID_PRODUCTO",      $arrayParametros['intIdProducto']);
            $objQuery->setParameter("ID_SERVICIO",      $arrayParametros['intIdServicio']);
            $objQuery->setParameter("NOMBRE_PARAMETRO", $arrayParametros['strNombreParametro']);
            $objQuery->setParameter("USO_DETALLE",      $arrayParametros['strUsoDetalles']);
            $objQuery->setSQL($strSql);
            $arrayData = $objQuery->getArrayResult();
            $arrayResultado = array(
                'status' => 'OK',
                'result' => $arrayData
            );
        }
        catch (\Exception $e)
        {
            $arrayResultado = array(
                'status' => 'ERROR',
                'result' => $e->getMessage()
            );
        }
        return $arrayResultado;
    }
        /**
     * Obtiene la tecnologia y login del punto mediente el servicio
     * 
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.0 15-06-2021
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec> 
     * @version 1.1 06-04-2023 - Se mejora la consulta para obtener la tecnología del olt por empresaId.
     * 
     * @param array $arrayParametros 
     * @return array  $arrayData
     * 
     * costoQuery: 15
     **/
    public function getTecnologiaPuntoAnterior($arrayParametros)
    {
        
        $objRsm = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $strQuery = " SELECT ME.NOMBRE_MARCA_ELEMENTO, IP.LOGIN
                            FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT S,
                                 DB_COMERCIAL.INFO_SERVICIO_TECNICO ST,
                                 DB_COMERCIAL.INFO_SERVICIO INS,
                                 DB_COMERCIAL.INFO_PUNTO IP,
                                 DB_INFRAESTRUCTURA.INFO_ELEMENTO IE,
                                 DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO AME,
                                 DB_INFRAESTRUCTURA.ADMI_MARCA_ELEMENTO ME,
                                 DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC,
                                 DB_COMERCIAL.ADMI_CARACTERISTICA AC,
                                 DB_COMERCIAL.ADMI_PRODUCTO AP
                            WHERE S.VALOR = ST.SERVICIO_ID
                            AND ST.SERVICIO_ID = INS.ID_SERVICIO
                            AND INS.PUNTO_ID  = IP.ID_PUNTO
                            AND ST.ELEMENTO_ID = IE.ID_ELEMENTO
                            AND IE.MODELO_ELEMENTO_ID = AME.ID_MODELO_ELEMENTO
                            AND AME.MARCA_ELEMENTO_ID = ME.ID_MARCA_ELEMENTO
                            AND S.SERVICIO_ID = :servicio
                            AND S.PRODUCTO_CARACTERISITICA_ID = APC.ID_PRODUCTO_CARACTERISITICA
                            AND APC.PRODUCTO_ID = AP.ID_PRODUCTO
                            AND APC.CARACTERISTICA_ID = AC.ID_CARACTERISTICA
                            AND AC.DESCRIPCION_CARACTERISTICA = :descripcionCarac
                            AND AP.EMPRESA_COD = :empresaId
                            AND S.ESTADO = :estado";

        $objRsm->addScalarResult(strtoupper('NOMBRE_MARCA_ELEMENTO'), 'tecnologia', 'string');
        $objRsm->addScalarResult(strtoupper('LOGIN'), 'login', 'string');
        $objQuery->setParameter("servicio", $arrayParametros['servicioId']);
        $objQuery->setParameter("empresaId", $arrayParametros['empresaId']);
        $objQuery->setParameter("estado", $arrayParametros['estado']);
        $objQuery->setParameter("descripcionCarac", 'TRASLADO');

        $objQuery->setSQL($strQuery);  
        $arrayData = $objQuery->getResult();

        return $arrayData;
    }
    
    /**
    * Documentacion para la funcion getReporteSecureCpe
    *
    * Función que retorna el listado de los productos secure cpe
    * en formato json
    * @param mixed $arrayParametros[
    *                               'intEmpresaId'           => id empresa en sesion 
    *                               'strPrefijoEmpresa'      => prefijo de la empresa en sesion
    *                               'strUsrSesion'           => usuario en sesion
    *                               'strEmailUsrSesion'      => email usuario en sesion
    *                               'fechaCreacionDesde'     => rango inicial para fecha de creacion
    *                               'fechaCreacionHasta'     => rango final para fecha de creacion
    *                               'intStart'               => limite de rango de inicio para realizar consulta
    *                               'intLimit'               => limite de rango final para realizar consulta
    *                               ]
    *
    * @return json $objJsonData
    *
    * @author Antonio Ayala <afayala@telconet.ec>
    * @version 1.0 03-09-2021
    *
    */
    public function getReporteSecureCpe($arrayParametros)
    {
        $intTotal      = 0;
        $boolValida    = false;
                  
        try
        { 
            if($arrayParametros && count($arrayParametros)>0)
            {
                $objCursor  = $arrayParametros['cursor'];                 

                $strSql = "BEGIN
                            DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.P_GET_SECURE_CPE
                            (
                                :Pn_EmpresaId,
                                :Pv_PrefijoEmpresa,
                                :Pv_UsrSesion,
                                :Pv_FechaCreacionDesde,
                                :Pv_FechaCreacionHasta,
                                :Pn_Start,
                                :Pn_Limit,
                                :Pn_TotalRegistros,
                                :Pc_Documentos
                            );
                        END;";

                $strStmt           = oci_parse($arrayParametros['oci_con'], $strSql);

                oci_bind_by_name($strStmt, ":Pn_EmpresaId", $arrayParametros['intEmpresaId']);
                oci_bind_by_name($strStmt, ":Pv_PrefijoEmpresa", $arrayParametros['strPrefijoEmpresa']);
                oci_bind_by_name($strStmt, ":Pv_UsrSesion", $arrayParametros['strUsrSesion']);
                oci_bind_by_name($strStmt, ":Pv_FechaCreacionDesde", $arrayParametros['strFechaDesde']);
                oci_bind_by_name($strStmt, ":Pv_FechaCreacionHasta", $arrayParametros['strFechaHasta']);
                oci_bind_by_name($strStmt, ":Pn_Start", $arrayParametros['intStart']);
                oci_bind_by_name($strStmt, ":Pn_Limit", $arrayParametros['intLimit']); 
                oci_bind_by_name($strStmt, ":Pn_TotalRegistros", $intTotal, 10);   
                oci_bind_by_name($strStmt, ":Pc_Documentos", $objCursor, -1, OCI_B_CURSOR);              

                oci_execute($strStmt); 
                oci_execute($objCursor, OCI_DEFAULT);

                while (($objRow = oci_fetch_array($objCursor)) != $boolValida)
                { 
                    $arrayPagos[] = array(
                                           'login'            => trim($objRow['LOGIN']),
                                           'razonSocial'      => trim($objRow['RAZON_SOCIAL']),
                                           'producto'         => trim($objRow['DESCRIPCION_PRODUCTO']),
                                           'serie'            => trim($objRow['SERIE']),
                                           'fechaActivacion'  => trim($objRow['FE_CREACION']),
                                           'fechaCaducidad'   => trim($objRow['FECHA']),
                                           'cpeForti'         => trim($objRow['CPE']),
                                           'planSecure'       => trim($objRow['PLAN'])
                                         );                   
                }
            }
            else
            {
                $strMensaje= 'No se enviaron parámetros para generar la consulta.';
            }

            $arrayResultado = array('total' => $intTotal, 'encontrados' => $arrayPagos);
            
        }catch (\Exception $e) 
        {
            $strMensaje= 'No se enviaron parámetros para generar la consulta.';
            throw($e);
        }           

        $objJsonData    = json_encode($arrayResultado);
        
        return $objJsonData;
    }
    
    /**
    * Documentación para el método 'generarReporteSecureCpe'.
    *
    * Ejecuta la generación y envío de reporte de producto secure cpe según los parámetros indicados.
    *
    * @param mixed $arrayParametros[
    *                               'intEmpresaId'           => id empresa en sesion 
    *                               'strPrefijoEmpresa'      => prefijo de la empresa en sesion
    *                               'strUsrSesion'           => usuario en sesion
    *                               'strEmailUsrSesion'      => email usuario en sesion
    *                               'fechaCreacionDesde'     => rango inicial para fecha de creacion
    *                               'fechaCreacionHasta'     => rango final para fecha de creacion
    *                               ]
    *
    * @author Antonio Ayala <afayala@telconet.ec>
    * @version 1.0 03-09-2021
    */
    public function generarReporteSecureCpe($arrayParametros)
    {      
        try
        {
            if($arrayParametros && count($arrayParametros)>0)
            {
                $strSql = "BEGIN
                            DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.P_REPORTE_SECURE_CPE
                            (
                                :Pn_EmpresaId,
                                :Pv_PrefijoEmpresa,
                                :Pv_UsrSesion,
                                :Pv_EmailUsrSesion,
                                :Pv_FechaCreacionDesde,
                                :Pv_FechaCreacionHasta
                            );
                        END;";

                $strStmt = $this->_em->getConnection()->prepare($strSql);
                $strStmt->bindParam('Pn_EmpresaId', $arrayParametros['intEmpresaId']);
                $strStmt->bindParam('Pv_PrefijoEmpresa', $arrayParametros['strPrefijoEmpresa']);
                $strStmt->bindParam('Pv_UsrSesion', $arrayParametros['strUsrSesion']);
                $strStmt->bindParam('Pv_EmailUsrSesion', $arrayParametros['strEmailUsrSesion']);
                $strStmt->bindParam('Pv_FechaCreacionDesde', $arrayParametros['strFechaDesde']);
                $strStmt->bindParam('Pv_FechaCreacionHasta', $arrayParametros['strFechaHasta']);
                
                $strStmt->execute();
            }
            else
            {
                $strMensaje= 'No se enviaron parámetros para generar la consulta.';
            }

        }catch (\Exception $e) 
        {
            $strMensaje= 'No se enviaron parámetros para generar la consulta.';
            throw($e);
        }
    }

    /* 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 20-01-2023 Se extrae los registros para la ventana de proyectos de Pyl.
     * 
     * costo query 15
     * @param $arrayParametros   ['strTipoConsulta' => si se desea sacar el proyecto, punto o servicios.
     *                            'strProyectoId'   => id del proyecto a buscar(Puntos).
     *                            'strRazonS'       => Razón social del cliente a consultar.
     *                            'strProyecto'     => Proyecto a consultar.
     *                            'strLogin'        => Login a consultar.
     *                            'strEstado'       => Estado del pedido]
     *
     * @return $arrayDatosTotal
     */
    public function getRegistrosProyecto($arrayParametros)
    {
        $objRsm                 = new ResultSetMappingBuilder($this->_em);
        $objQuery               = $this->_em->createNativeQuery(null, $objRsm);
        $strDescripcion         = "PROYECTO_CRM";
        $arrayDatosTotal        = array();
        $strWhere               = " where ";
        $strTipoConsulta        = $arrayParametros['strTipoConsulta'];
        $strProyectoId          = $arrayParametros['strProyectoId'];
        $strRazonSocial         = $arrayParametros['strRazonS'];
        $strProyecto            = $arrayParametros['strProyecto'];
        $strLogin               = $arrayParametros['strLogin'];
        $strEstado              = $arrayParametros['strEstado'];
        
        if($strTipoConsulta == 'Proyecto')
        {
           $strCampos      = 'select distinct ap.id_Proyecto idProyecto, ap.nombre ' ;
           $objRsm->addScalarResult(strtoupper('idProyecto'), 'idProyecto', 'string');
           $objRsm->addScalarResult(strtoupper('nombre'), 'nombre', 'string');
        }
        if($strTipoConsulta == 'Puntos')
        {
           $strCampos      = 'select distinct iser.punto_Id puntoId' ;
           $strWhere       .= "ispc.Valor in (:paramProyectoId) and ";
           $objQuery->setParameter("paramProyectoId", $strProyectoId);
           $objRsm->addScalarResult(strtoupper('puntoId'), 'puntoId', 'string');
        }
        if($strTipoConsulta == 'Servicio')
        {
           $strCampos      = 'select distinct iser.ID_SERVICIO servicioId' ;
           $strWhere       .= "ispc.Valor in (:paramProyectoId) and ";
           $objQuery->setParameter("paramProyectoId", $strProyectoId);
           $objRsm->addScalarResult(strtoupper('servicioId'), 'servicioId', 'string');
        }
        
        $strFrom = ' from DB_COMERCIAL.INFO_SERVICIO iser,'
            . '           DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT ispc, '
            . '           DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA apc, '
            . '           DB_COMERCIAL.ADMI_CARACTERISTICA ac,'
            . '           DB_COMERCIAL.ADMI_PRODUCTO apr,'
            . '           NAF47_TNET.admi_proyecto ap ';
        
        $strWhere .= " ac.DESCRIPCION_CARACTERISTICA = :paramDescripcionCaract and
                        apc.CARACTERISTICA_ID = ac.ID_CARACTERISTICA and
                        ispc.PRODUCTO_CARACTERISITICA_ID = apc.ID_PRODUCTO_CARACTERISITICA and
                        (ispc.valor != '0' and regexp_like(ispc.valor, '^[[:digit:]]+$')) and
                        ap.ID_PROYECTO = ispc.valor and
                        apr.ID_PRODUCTO = iser.PRODUCTO_ID and
                        apr.GRUPO !='DATACENTER' and 
                        iser.ID_SERVICIO = ispc.SERVICIO_ID and
                        iser.ESTADO not in(:estados)";
       
        if(!empty($strLogin) || $strTipoConsulta == 'Servicio')
        {
            $objQuery->setParameter("estados", array('Anulado', 'Eliminado', 'Rechazado', 'Rechazada', 'Cancelado'));
        } 
        else
        {
            $objQuery->setParameter("estados", array('Anulado', 'Eliminado', 'Rechazado', 'Rechazada', 'Cancelado', 'Activo'));
        } 
        
        $objQuery->setParameter("paramDescripcionCaract", $strDescripcion);
        
        if(!empty($strRazonSocial))
        {
            $strFrom .= ' ,DB_COMERCIAL.INFO_PERSONA ip,'
                     . '   DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper,'
                     . '   DB_COMERCIAL.INFO_PUNTO ipu';
            $strWhere .= " and iser.punto_id = ipu.id_punto "
                      . "  and ipu.persona_empresa_rol_id = iper.id_persona_rol "
                      . "  and iper.persona_id = ip.id_persona "
                      . "  and iper.empresa_rol_id = 1 "
                      . "  and ip.razon_social like :razonSocial ";
            $objQuery->setParameter("razonSocial", '%'.$strRazonSocial.'%');
        }
        
        if(!empty($strProyecto))
        {
            $strWhere .= " and ap.nombre like :proyecto ";
            $objQuery->setParameter("proyecto", '%'.$strProyecto.'%');
        }
        
        if(!empty($strLogin))
        {
            if(empty($strRazonSocial))
            {
                $strFrom .= ' ,DB_COMERCIAL.INFO_PUNTO ipu ';
            }
            $strWhere .= " and ipu.id_punto = iser.punto_id "
                       . " and ipu.login = :login ";
            $objQuery->setParameter("login", $strLogin);
        }
        
        if(!empty($strEstado))
        {
            $strFrom .= ' ,DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT ispc2 ';
            $strWhere .= " and ispc.servicio_id = ispc2.servicio_id and"
                        . " iser.estado = 'PrePlanificada' and"
                        . " ispc2.valor = :estado ";
            $objQuery->setParameter("estado", $strEstado);
        }
        
        $strQuery = ($strCampos.$strFrom.$strWhere);
        $objQuery->setSQL($strQuery);  
        $arrayDatos = $objQuery->getResult();       
        
        $arrayDatosTotal["registros"] = $arrayDatos;
        return $arrayDatosTotal;
    }
}
