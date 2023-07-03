<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiProductoCaracteristicaRepository extends EntityRepository
{
    public function findByProductoIdyEstado($idProducto, $estado)
    {	
        $query = $this->_em->createQuery("SELECT p
                FROM schemaBundle:AdmiProductoCaracteristica p
                WHERE 
                p.productoId = :idProducto AND
                p.estado = :estado AND
                p.visibleComercial = 'SI'");
        $query->setParameter('idProducto', $idProducto);
        $query->setParameter('estado', $estado);
        $datos = $query->getResult();
        return $datos;
    }
    
    public function findByProductoIdyEstadoTecnico($idProducto, $estado)
    {	
        $query = $this->_em->createQuery("SELECT p
                FROM schemaBundle:AdmiProductoCaracteristica p
                WHERE 
                p.productoId = :idProducto AND
                p.estado = :estado AND
                p.visibleComercial = 'NO'");
        $query->setParameter('idProducto', $idProducto);
        $query->setParameter('estado', $estado);
        $datos = $query->getResult();
        return $datos;
    }
    
    /**
     * Documentación para el método 'findByDescripcionProductoAndCaracteristica'.
     *
     * Método utilizado para obtener el AdmiProductoCaracteristica buscando por la descripción del Producto y de la Caracteristica
     * costoQuery: 6
     * CostoQuery: 7 (Cuando se usan los parámetros 'strDescCaracteristica', 'strEstado' y 'intIdProducto')
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.0 2016-09-27
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 15-02-2017 - Se modifica la función para agregar el filtro por id de producto usando el parámetro 'intIdProducto'
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 15-03-2018 - Se agrega validacion correcta en las condicionales para evitar generación de log innecesario
     *
     * @param array $arrayParametros [ 'strDescProducto'       => Descripción del Producto
     *                                 'strDescCaracteristica' => Descripción de la Caracteristica
     *                                 'strEstado'             => El estado de los elemntos a consultar
     *                                 'intIdProducto'         => Id del producto ]
     * @return AdmiProductoCaracteristica
     */
    public function findByDescripcionProductoAndCaracteristica($arrayParametros)
    {	
        try
        {
            $objQuery = $this->_em->createQuery(null);
            $strQuery = "SELECT apc
                        FROM schemaBundle:AdmiProductoCaracteristica apc,
                             schemaBundle:AdmiProducto ap,
                             schemaBundle:AdmiCaracteristica ac
                        WHERE 
                                apc.productoId = ap.id
                            AND apc.caracteristicaId = ac.id";
            
            if( isset($arrayParametros['intIdProducto']) && !empty($arrayParametros['intIdProducto']) )
            {
                $strQuery .= " AND ap.id = :intIdProducto";
                $objQuery->setParameter('intIdProducto', $arrayParametros['intIdProducto']);
            }
            
            if(isset($arrayParametros['strDescProducto']) && !empty($arrayParametros['strDescProducto']))
            {
                $strQuery .= " AND ap.descripcionProducto = :producto";
                $objQuery->setParameter('producto', $arrayParametros['strDescProducto']);
            }
            if(isset($arrayParametros['strDescCaracteristica']) && !empty($arrayParametros['strDescCaracteristica']))
            {
                $strQuery .= " AND ac.descripcionCaracteristica = :caracteristica";
                $objQuery->setParameter('caracteristica', $arrayParametros['strDescCaracteristica']);
            }
            if(isset($arrayParametros['strEstado']) && !empty($arrayParametros['strEstado']))
            {
                $strQuery .= " AND apc.estado = :estado
                               AND ap.estado = :estado
                               AND ac.estado = :estado";
                $objQuery->setParameter('estado', $arrayParametros['strEstado']);
            }
            $objQuery->setDQL($strQuery);
            $datos = $objQuery->getOneOrNullResult();
            return $datos;
        }
        catch(\Exception $ex)
        {
            return null;
        }
    }
    
    
     /**
     * Costo: 6
     * getCaracteristicaProducto
     * 
     * Método que retorna si el producto tiene asociado una caracteristica puntual
     * 
     * @param array $arrayParametros[ 'strCaracteristica'  => descripcion de la caracteristica
     *                                'strEstado'          => estado de la caracteristica
     *                                'intServicioId'      => id del servicio ]
     *
     * @return string $strExisteCaracteristica
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 11-04-2017
     */
    public function getCaracteristicaProducto($arrayParametros)
    { 
        $intCaracteristica       = 0;
        $strExisteCaracteristica = "N";

        $strSql = " SELECT COUNT(admiproductocarac.ID_PRODUCTO_CARACTERISITICA) as TOTAL 
                        FROM admi_producto_caracteristica admiproductocarac
                            WHERE admiproductocarac.caracteristica_id = (SELECT admicaracteristica.id_caracteristica 
                            FROM admi_caracteristica admicaracteristica 
                            WHERE admicaracteristica.descripcion_caracteristica = :descripcionCaracteristica 
                            AND admicaracteristica.estado = :estado)
                            AND admiproductocarac.producto_id = (
                            SELECT infoservicio.producto_id FROM info_servicio infoservicio 
                            WHERE infoservicio.id_servicio = :servicioId) ";            
                        
        $objStmt = $this->_em->getConnection()->prepare($strSql);
        $objStmt->bindValue('descripcionCaracteristica',$arrayParametros["strCaracteristica"]);
        $objStmt->bindValue('estado',$arrayParametros["strEstado"]);
        $objStmt->bindValue('servicioId',$arrayParametros["intServicioId"]);
        $objStmt->execute();            
                             
        $intCaracteristica = $objStmt->fetchColumn();

        if($intCaracteristica > 0)
        {
            $strExisteCaracteristica = "S";
        }

        return $strExisteCaracteristica;
    }       
    
    
    public function getDominiosTelconet()
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);        
        $valor = '%TELCONET.NET';
        
        $sql="SELECT ID_SERVICIO_PROD_CARACT,
                        SERVICIO_ID,
                        PRODUCTO_CARACTERISITICA_ID,
                        VALOR, 
                        ESTADO 
              FROM INFO_SERVICIO_PROD_CARACT 
              WHERE UPPER(VALOR) LIKE :valor AND PRODUCTO_CARACTERISITICA_ID = (
                        SELECT ID_PRODUCTO_CARACTERISITICA 
                        FROM ADMI_PRODUCTO_CARACTERISTICA 
                        WHERE PRODUCTO_ID=(
                                    SELECT ID_PRODUCTO 
                                    FROM ADMI_PRODUCTO 
                                    WHERE UPPER(NOMBRE_TECNICO)='DOMINIO' AND ESTADO= :estado AND EMPRESA_COD=10)
                        AND CARACTERISTICA_ID=(
                                    SELECT ID_CARACTERISTICA 
                                    FROM ADMI_CARACTERISTICA 
                                    WHERE UPPER(DESCRIPCION_CARACTERISTICA)='DOMINIO'))";
        
        
        $query->setParameter("valor", $valor);
        $query->setParameter("estado"    , 'Activo');
      
        
        $rsm->addScalarResult('ID_SERVICIO_PROD_CARACT' ,'idServicioProdCarac'  , 'integer');
        $rsm->addScalarResult('SERVICIO_ID'             ,'idServicio'           , 'integer');
        $rsm->addScalarResult('PRODUCTO_CARACTERISITICA','idProdCaract'         , 'integer');
        $rsm->addScalarResult('VALOR'                   ,'valor'                , 'string');
        $rsm->addScalarResult('ESTADO'                  ,'estado'               , 'string');
        
        $query->setSQL($sql);
        $datos = $query->getResult();

        return $datos;
    }
    
    /**
     * getDatosServicioProductoNethome
     * 
     * Método utilizado para obtener los productos y la cantidad de la solución NetHome procesada
     * 
     * @param  Array $arrayParametros [
     *                                  intIdServicio      Identificador de servicio a procesar
     *                                  strProceso         Proceso a ejecutar para generar información
     *                                ]
     * 
     * @return String o Array Texto formateado con el nombre del producto y características Cantidad.
     *
     * costoQuery: 10
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 17-09-2018
     * @since 1.0
     */
    public function getDatosServicioProductoNethome($arrayParametros)
    {
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $objQuery->setParameter("servicioIdParam", $arrayParametros['intIdServicio']);
        $objQuery->setParameter("cantidadParam",   array('CANTIDAD_NETHOME'));
        $objQuery->setParameter("estadoParam",     'Activo');
        $objQuery->setParameter("requiereParam",   'REQUIERE_SERIE');
        $strSqlInternoShow      = "";
        $strSqlInternoWhereShow = "";
        
        if ($arrayParametros['strProceso'] == "show")
        {
            $objQuery->setParameter("precioParam",   array('PRECIO_NETHOME'));
            $strSqlInternoShow      = " '</td><td style=''width:65px; text-align: center''>' ||
                                       ' '
                                       || SPC2.VALOR || ";
            
            $strSqlShow             = " <th style=''width:65px''> PRECIO</th> ";
            
            $strSqlInternoWhereShow = " INNER JOIN (SELECT SERVPROD.VALOR AS VALOR ,SERVPROD.SERVICIO_ID AS SERVICIO_ID, 
                                        PC2.PRODUCTO_ID AS PRODUCTO_ID,
                                        SERVPROD.ESTADO AS ESTADO
                                        FROM INFO_SERVICIO_PROD_CARACT    SERVPROD
                                        INNER JOIN ADMI_PRODUCTO_CARACTERISTICA PC2 ON 
                                        PC2.ID_PRODUCTO_CARACTERISITICA = SERVPROD.PRODUCTO_CARACTERISITICA_ID
                                        INNER JOIN ADMI_CARACTERISTICA          C2 
                                        ON C2.ID_CARACTERISTICA            = PC2.CARACTERISTICA_ID
                                        AND C2.DESCRIPCION_CARACTERISTICA  IN (:precioParam)
                                        INNER JOIN ADMI_PRODUCTO                PRO2 ON PRO2.ID_PRODUCTO               = PC2.PRODUCTO_ID
                                        ) SPC2 ON S.ID_SERVICIO = SPC2.SERVICIO_ID AND SPC2.PRODUCTO_ID=PRO.ID_PRODUCTO ";
            
        }
        
        $strSqlInterno = "SELECT C.ID_CARACTERISTICA ID_, 
                                '<tr><td style=''width:85px''>
                               ' ||
                                PRO.DESCRIPCION_PRODUCTO|| 
                                '</td><td style=''width:65px; text-align: center''>' || 
                                ' '
                                || SPC.VALOR ||
                            
                                ".$strSqlInternoShow."

                                '</td></tr>'  AS CARACTERISTICA,
                                PRO.ID_PRODUCTO IDPRODUCTO,
                                PRO.DESCRIPCION_PRODUCTO TIPOELEMENTO,
                                SPC.VALOR CANTIDADELEMENTO,
                                '' SERIEELEMENTO,
                                '' MODELOELEMENTO,
                                '' DESCRIPCIONELEMENTO,
                                NVL((select 'SI' from DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA, 
                                DB_COMERCIAL.admi_caracteristica
                                where ADMI_PRODUCTO_CARACTERISTICA.CARACTERISTICA_ID = admi_caracteristica.ID_CARACTERISTICA and
                                ADMI_PRODUCTO_CARACTERISTICA.PRODUCTO_ID = PRO.ID_PRODUCTO and 
                                admi_caracteristica.DESCRIPCION_CARACTERISTICA=:requiereParam and 
                                ADMI_PRODUCTO_CARACTERISTICA.estado=:estadoParam),'NO') REQUIERESERIE
                                FROM       DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT    SPC
                                INNER JOIN DB_COMERCIAL.INFO_SERVICIO                S   ON S.ID_SERVICIO                  = SPC.SERVICIO_ID
                                INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA PC  ON PC.ID_PRODUCTO_CARACTERISITICA = SPC.PRODUCTO_CARACTERISITICA_ID
                                INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA          C   ON ID_CARACTERISTICA              = PC.CARACTERISTICA_ID
                                INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO                PRO ON ID_PRODUCTO                  = PC.PRODUCTO_ID
                                ". $strSqlInternoWhereShow ."
                                WHERE C.DESCRIPCION_CARACTERISTICA IN (:cantidadParam)
                                AND S.ID_SERVICIO = :servicioIdParam
                                AND SPC.ESTADO    = :estadoParam";
        
        if ($arrayParametros['strProceso'] == "coordinar" || $arrayParametros['strProceso'] == "show")
        {
            $strSql = "SELECT '<table cellpadding=''5''>
                              <tr><td valign=''center''><table cellpadding=''5''><tr><td valign=''center''>NOMBREPRODUCTO</td></tr>'||'
                              <tr><td width=''150px''><div id=''table-wrapper''><div id=''table-scroll''><table id=''customers'' cellpadding=''5''>
                              <thead>
                              <tr>
                              <th style=''width:85px''>
                              ELEMENTO</th><th style=''width:65px''>
                              CANTIDAD</th>
                              ". $strSqlShow."
                              </tr></thead>
                              <tbody>
                              '|| VALOR || '
                              </tbody>
                              </table></div></div></td></tr></table></td></tr></table>' RESPUESTA FROM  
                        (
                            SELECT LISTAGG(CARACTERISTICA , '') WITHIN GROUP (ORDER BY ID_) VALOR FROM 
                            (
                                SELECT ID_, CARACTERISTICA  FROM 
                                (
                                    ".$strSqlInterno."
                                )
                            )
                        )";
            $objRsm->addScalarResult('RESPUESTA', 'respuesta', 'string');
            return $objQuery->setSQL($strSql)->getSingleScalarResult();
        }
        elseif ($arrayParametros['strProceso'] == "activar")
        {
            $strSql = $strSqlInterno;
            $objRsm->addScalarResult('ID_',                 'id_caract',            'integer');
            $objRsm->addScalarResult('CARACTERISTICA',      'caracteristica',       'string');
            $objRsm->addScalarResult('IDPRODUCTO',          'idProducto',           'integer');
            $objRsm->addScalarResult('TIPOELEMENTO',        'tipoElemento',         'string');
            $objRsm->addScalarResult('SERIEELEMENTO',       'serieElemento',        'string');
            $objRsm->addScalarResult('MODELOELEMENTO',      'modeloElemento',       'string');
            $objRsm->addScalarResult('DESCRIPCIONELEMENTO', 'descripcionElemento',  'string');
            $objRsm->addScalarResult('CANTIDADELEMENTO',    'cantidadElemento',     'string');
            $objRsm->addScalarResult('REQUIERESERIE',       'requiereSerie',        'string');
            return $objQuery->setSQL($strSql)->getResult();
        }
    }
    
    /**
     * getDetalleElementosNethome
     * 
     * Método utilizado para obtener los productos y la cantidad de la solución NetHome procesada
     * 
     * @param  Array $arrayParametros [
     *                                  intIdServicio      Identificador de servicio a procesar
     *                                  strProceso         Proceso a ejecutar para generar información
     *                                ]
     * 
     * @return String o Array Texto formateado con el nombre del producto y características Cantidad.
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 17-09-2018
     * @since 1.0
     */
    public function getDetalleElementosNethome($arrayParametros)
    {
        $arrayEncontrados = array();
        $arrayResultado   = $this->getDatosServicioProductoNethome($arrayParametros);
        if($arrayResultado)
        {
            foreach($arrayResultado as $registro)
            {
                if($registro['requiereSerie'] == "SI")
                {
                    for($i = 0; $i < $registro['cantidadElemento']; $i++)
                    {
                        $arrayEncontrados[] = $registro;
                    }
                }
            }
        }
        return $arrayEncontrados;
    }
    
    /**
     * Documentación para el método 'getResultadoDatosServicioProducto'.
     *
     * Método utilizado para obtener el ancho de banda y la relación del concentrador que tiene el Producto(servicio) del Punto del Cliente.
     * 
     * @param  $intServicio integer PK del servicio.
     * 
     * @return String Texto formateado con el nombre del producto y características de Capacidad 1, Capacidad2 y Enlace_Datos.
     *
     * costoQuery: 49
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-07-2016
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.1 07-07-2019 - Se realiza la consulta de los campos Grupo y Linea de Negocio para ser mostrados en el Grid.
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.2 14-04-2020 - Se agrega la caracteristica Cotización a ser mostrada en el Grid.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.3 17-07-2020 - Se agrega la caracteristica nombre del proyecto para su visualización en el grid Comercial.
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.4 03-09-2020 - Se agrega la característica nombre de la propuesta para su visualización en el grid Comercial.
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.5 09-03-2021 - Se agrega dos sub-consulta para saber a que tipo de red(MPLS/GPON),
     *                           pertenecen los productos, mostrados en el Grid de servicio.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.6 20-07-2021 Se agrega validaciones para los tipos de red
     *
     */
    public function getResultadoDatosServicioProducto($intServicio)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);
        
        $strProducto       =  "";
        $strCaracteristica =  "ENLACE_DATOS";
        
        $objServicioTecnico = $this->_em->getRepository('schemaBundle:InfoServicioTecnico')->findOneBy(array("servicioId"=>$intServicio));
        
        if(is_object($objServicioTecnico))
        {
            if('BACKUP'===$objServicioTecnico->getTipoEnlace())
            {
                $strProducto       = "PRINCIPAL"; 
                $strCaracteristica = "ES_BACKUP";
            }
            elseif('PRINCIPAL'===$objServicioTecnico->getTipoEnlace())
            {
                $strProducto = "CONCENTRADOR";                
            }            
            
        }
        
        //obtener tipo red gpon-mpls
        $strTipoRedGpon = "GPON_MPLS";
        $strTipoRedMpls = "MPLS";
        $arrayParVerTipoRedGpon = $this->_em->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                'COMERCIAL',
                                                                                                '',
                                                                                                'VERIFICAR TIPO RED',
                                                                                                'VERIFICAR_GPON',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                '');
        if(isset($arrayParVerTipoRedGpon) && !empty($arrayParVerTipoRedGpon) && !empty($arrayParVerTipoRedGpon['valor2']))
        {
            $strTipoRedGpon = $arrayParVerTipoRedGpon['valor2'];
        }
        $arrayParVerTipoRedMpls = $this->_em->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                'COMERCIAL',
                                                                                                '',
                                                                                                'VERIFICAR TIPO RED',
                                                                                                'VERIFICAR_MPLS',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                '');
        if(isset($arrayParVerTipoRedMpls) && !empty($arrayParVerTipoRedMpls) && !empty($arrayParVerTipoRedMpls['valor2']))
        {
            $strTipoRedMpls = $arrayParVerTipoRedMpls['valor2'];
        }
        
        $sql = "SELECT '<table><tr height=*80px*><td valign=*top*><table><tr height=*14px*><td valign=*top*>NAME_PRODUCT</td></tr>' || VALOR || 
                       '</table></td></tr></table>' RESPUESTA FROM  
                (
                    SELECT LISTAGG(CARACTERISTICA , '') WITHIN GROUP (ORDER BY ID_) VALOR FROM 
                    (
                        SELECT ID_, CARACTERISTICA  FROM 
                        (
                            WITH ENLACE AS (
                            SELECT C.ID_CARACTERISTICA ID_, 
                            '<tr><td width=*110px*><label style=STYLE_NAME_PROD>".$strProducto."</label><br/>' ||
                            '<label style=STYLE_ARROWS_1>&#10551;</label><label>' CARACTERISTICA, 
                            TO_NUMBER(SPC.VALOR) EXTREMO
                            FROM       INFO_SERVICIO_PROD_CARACT    SPC
                            INNER JOIN INFO_SERVICIO                S   ON S.ID_SERVICIO                  = SPC.SERVICIO_ID
                            INNER JOIN ADMI_PRODUCTO_CARACTERISTICA PC  ON PC.ID_PRODUCTO_CARACTERISITICA = SPC.PRODUCTO_CARACTERISITICA_ID
                            INNER JOIN ADMI_CARACTERISTICA          C   ON ID_CARACTERISTICA              = PC.CARACTERISTICA_ID

                            WHERE C.DESCRIPCION_CARACTERISTICA = :ENLACE
                            AND S.ID_SERVICIO = :SERVICIO
                            AND SPC.ESTADO    = :ESTADO
                            )

                            SELECT C.ID_CARACTERISTICA ID_, 
                            '<tr><td width=*110px*><table><tr><td width=*75px*><label style=STYLE_NAME_PROD>CAPACIDAD ' ||
                            CASE WHEN C.DESCRIPCION_CARACTERISTICA = 'CAPACIDAD1' THEN '1' ELSE '2'END || 
                            '</label></td><td width=*20px*><label style=STYLE_ARROWS_1>&#10137;</label></td><td>' || 
                            SPC.VALOR || '&nbsp;</td></tr></table></td></tr>' AS CARACTERISTICA
                            FROM       INFO_SERVICIO_PROD_CARACT    SPC
                            INNER JOIN INFO_SERVICIO                S   ON S.ID_SERVICIO                  = SPC.SERVICIO_ID
                            INNER JOIN ADMI_PRODUCTO_CARACTERISTICA PC  ON PC.ID_PRODUCTO_CARACTERISITICA = SPC.PRODUCTO_CARACTERISITICA_ID
                            INNER JOIN ADMI_CARACTERISTICA          C   ON ID_CARACTERISTICA              = PC.CARACTERISTICA_ID
                            WHERE C.DESCRIPCION_CARACTERISTICA IN (:CAPACIDADES)
                            AND S.ID_SERVICIO = :SERVICIO
                            AND SPC.ESTADO    = :ESTADO
                            
                            UNION
                            
                            SELECT AP.ID_PRODUCTO,
                            '<tr><td width=*110px*><table><tr><td width=*75px*><label style=STYLE_NAME_PROD>GRUPO 
                            &emsp;&emsp;&emsp;&emsp; ' || 
                            '</label></td><td width=*20px*><label style=STYLE_ARROWS_1>&#10137;</label></td><td>' || 
                            '<b style=*color:#FF5733; font-size: 9.5px;*>&nbsp;' || AP.GRUPO || '</b></td></tr></table></td></tr>'||
                            '<tr><td width=*110px*><table><tr><td width=*75px*><label style=STYLE_NAME_PROD>L_NEGOCIO ' ||
                            '</label></td><td width=*20px*><label style=STYLE_ARROWS_1>&#10137;</label></td><td>' || 
                            '<b style=*color:#FF5733; font-size: 9.5px;*>' || AP.LINEA_NEGOCIO || '</b>
                            </td></tr></table></td></tr>' AS CARACTERISTICA
                            FROM INFO_SERVICIO S2
                            INNER JOIN ADMI_PRODUCTO AP ON AP.ID_PRODUCTO =S2.PRODUCTO_ID
                            WHERE S2.ID_SERVICIO =:SERVICIO

                            UNION
                            
                            SELECT C.ID_CARACTERISTICA ID_, 
                            '<tr><td width=*110px*><table><tr><td width=*75px*><label style=STYLE_NAME_PROD>NO_COTIZACION ' ||
                            '</label></td><td width=*20px*><label style=STYLE_ARROWS_1>&#10137;</label></td><td>' || 
                            SPC.VALOR || '&nbsp;</td></tr></table></td></tr>' AS CARACTERISTICA
                            FROM       INFO_SERVICIO_PROD_CARACT    SPC
                            INNER JOIN INFO_SERVICIO                S   ON S.ID_SERVICIO                  = SPC.SERVICIO_ID
                            INNER JOIN ADMI_PRODUCTO_CARACTERISTICA PC  ON PC.ID_PRODUCTO_CARACTERISITICA = SPC.PRODUCTO_CARACTERISITICA_ID
                            INNER JOIN ADMI_CARACTERISTICA          C   ON ID_CARACTERISTICA              = PC.CARACTERISTICA_ID
                            WHERE C.DESCRIPCION_CARACTERISTICA IN ('COTIZACION_NOMBRE')
                            AND S.ID_SERVICIO = :SERVICIO
                            AND SPC.ESTADO    = :ESTADO
                            
                            UNION

                            SELECT C.ID_CARACTERISTICA ID_, 
                            '<tr><td width=*110px*><table><tr><td width=*75px*><label style=STYLE_NAME_PROD>NO_PROYECTO' ||
                            '</label></td><td width=*20px*><label style=STYLE_ARROWS_1>&#10137;</label></td><td>' || 
                            SPC.VALOR || '&nbsp;</td></tr></table></td></tr>' AS CARACTERISTICA
                            FROM       INFO_SERVICIO_PROD_CARACT    SPC
                            INNER JOIN INFO_SERVICIO                S   ON S.ID_SERVICIO                  = SPC.SERVICIO_ID
                            INNER JOIN ADMI_PRODUCTO_CARACTERISTICA PC  ON PC.ID_PRODUCTO_CARACTERISITICA = SPC.PRODUCTO_CARACTERISITICA_ID
                            INNER JOIN ADMI_CARACTERISTICA          C   ON ID_CARACTERISTICA              = PC.CARACTERISTICA_ID
                            WHERE C.DESCRIPCION_CARACTERISTICA IN ('NOMBRE_PROYECTO')
                            AND S.ID_SERVICIO = :SERVICIO
                            AND SPC.ESTADO    = :ESTADO

                            UNION

                            SELECT C.ID_CARACTERISTICA ID_, 
                            '<tr><td width=*110px*><table><tr><td width=*75px*><label style=STYLE_NAME_PROD>NO_PROPUESTA' ||
                            '</label></td><td width=*20px*><label style=STYLE_ARROWS_1>&#10137;</label></td><td>' || 
                            SPC.VALOR || '&nbsp;</td></tr></table></td></tr>' AS CARACTERISTICA
                            FROM       INFO_SERVICIO_PROD_CARACT    SPC
                            INNER JOIN INFO_SERVICIO                S   ON S.ID_SERVICIO                  = SPC.SERVICIO_ID
                            INNER JOIN ADMI_PRODUCTO_CARACTERISTICA PC  ON PC.ID_PRODUCTO_CARACTERISITICA = SPC.PRODUCTO_CARACTERISITICA_ID
                            INNER JOIN ADMI_CARACTERISTICA          C   ON ID_CARACTERISTICA              = PC.CARACTERISTICA_ID
                            WHERE C.DESCRIPCION_CARACTERISTICA IN ('NOMBRE_PROPUESTA')
                            AND S.ID_SERVICIO = :SERVICIO
                            AND SPC.ESTADO    = :ESTADO

                            UNION

                            SELECT ISER.ID_SERVICIO ID_, 
                            '<tr>
                                <td width=*110px*>
                                    <table>
                                        <tr>
                                            <td width=*75px*>
                                                <label style=STYLE_NAME_PROD>Tipo de Red&emsp;&emsp; ' || '</label>
                                            </td>
                                            <td width=*20px*>
                                                <label style=STYLE_ARROWS_1>&#10137;</label>
                                            </td>
                                            <td>' || '<b style=*color:#FF5733; font-size: 9.5px;*>&nbsp;' 
                                            ||ISER_CARAC.VALOR|| '</b>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>' AS CARACTERISTICA
                                FROM
                                    INFO_SERVICIO                  ISER
                                    JOIN INFO_SERVICIO_PROD_CARACT ISER_CARAC 
                                        ON ISER_CARAC.SERVICIO_ID = ISER.ID_SERVICIO
                                    JOIN ADMI_PRODUCTO_CARACTERISTICA ACAR
                                        ON ACAR.ID_PRODUCTO_CARACTERISITICA = ISER_CARAC.PRODUCTO_CARACTERISITICA_ID
                                    JOIN ADMI_CARACTERISTICA          AC
                                        ON AC.ID_CARACTERISTICA = ACAR.CARACTERISTICA_ID
                                WHERE
                                    ISER.ID_SERVICIO                    = :SERVICIO
                                    AND AC.DESCRIPCION_CARACTERISTICA   = 'TIPO_RED'
                                    AND AC.ESTADO                       = :ESTADO
                                    AND ACAR.ESTADO                     = :ESTADO
                                    AND ISER_CARAC.ESTADO               = :ESTADO
                                    AND ISER_CARAC.VALOR                IN ('$strTipoRedGpon', '$strTipoRedMpls')

                            UNION
                            SELECT E.ID_, E.CARACTERISTICA || '<a style=*color:green;* href=*/comercial/punto/' || 
                            S.PUNTO_ID || '/' || TR.DESCRIPCION_TIPO_ROL ||'/show*>' || S.LOGIN_AUX || '</a></label></td></tr>'
                            FROM       INFO_PUNTO    P
                            INNER JOIN INFO_SERVICIO S ON S.PUNTO_ID = P.ID_PUNTO
                            INNER JOIN ENLACE        E ON E.EXTREMO  = S.ID_SERVICIO
                            INNER JOIN INFO_PERSONA_EMPRESA_ROL     PER ON PER.ID_PERSONA_ROL             = P.PERSONA_EMPRESA_ROL_ID
                            INNER JOIN INFO_EMPRESA_ROL             ER  ON ER.ID_EMPRESA_ROL              = PER.EMPRESA_ROL_ID
                            INNER JOIN ADMI_ROL                     R   ON R.ID_ROL                       = ER.ROL_ID
                            INNER JOIN ADMI_TIPO_ROL                TR  ON TR.ID_TIPO_ROL                 = R.TIPO_ROL_ID
                        )
                    )
                )";
        
        $query->setParameter("SERVICIO",    $intServicio);
        $query->setParameter("CAPACIDADES", array('CAPACIDAD1', 'CAPACIDAD2'));
        $query->setParameter("ENLACE",      $strCaracteristica);
        $query->setParameter("ESTADO",      'Activo');
        
        $rsm->addScalarResult('RESPUESTA', 'respuesta', 'string');

        return $query->setSQL($sql)->getSingleScalarResult();
    }

    /**
     * Documentación para el método 'getServicioPorCaracteristica'.
     *
     * Método encargado de retornar los servicios asociados a la descripción enviada por parámetro.
     *
     * Costo 39
     *
     * @param array $arrayParametros [
     *                                  "arrayDescripcionCaracteristica" => Descripción de la caracteristica a buscar.
     *                                  "arrayValor"                     => valor del servicio a buscar.
     *                               ]
     *
     * @return array $arrayResultado arreglo de los servicios.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 17-07-2020
     *
     */
    public function getServicioPorCaracteristica($arrayParametros)
    {
        try
        {
            $arrayDescripcionCaracteristica = $arrayParametros['arrayDescripcionCaracteristica'] 
                                              ? $arrayParametros['arrayDescripcionCaracteristica']:"";
            $arrayValor                     = $arrayParametros['arrayValor'] ? $arrayParametros['arrayValor']:"";
            $arrayDatos                     = array();
            $strMensajeError                = "";
            $objRsm                         = new ResultSetMappingBuilder($this->_em);
            $objQuery                       = $this->_em->createNativeQuery(null,$objRsm);

            if( empty($arrayDescripcionCaracteristica) && empty($arrayValor) )
            {
                throw new \Exception('El campo arrayDescripcionCaracteristica y arrayValor es obligatorio.');
            }

            $strSelect = " SELECT distinct(ISER.ID_SERVICIO)";

            $strFrom   = " FROM ADMI_PRODUCTO_CARACTERISTICA        APC
                                JOIN ADMI_PRODUCTO                  AP   ON APC.PRODUCTO_ID                  = AP.ID_PRODUCTO
                                JOIN ADMI_CARACTERISTICA            AC   ON APC.CARACTERISTICA_ID            = AC.ID_CARACTERISTICA
                                                                         AND AC.ESTADO                       = :estado
                                JOIN INFO_SERVICIO_PROD_CARACT      ISPC ON ISPC.PRODUCTO_CARACTERISITICA_ID = APC.ID_PRODUCTO_CARACTERISITICA
                                                                         AND ISPC.ESTADO                     = :estado
                                JOIN INFO_SERVICIO                  ISER ON ISER.ID_SERVICIO                 = ISPC.SERVICIO_ID ";

            $strWhere  = " WHERE AC.DESCRIPCION_CARACTERISTICA IN (:arrayDescripcionCaracteristica)
                           AND to_char(ISPC.VALOR)  in(:arrayValor) ";
            $objQuery->setParameter("estado", "Activo");
            $objQuery->setParameter("arrayDescripcionCaracteristica", $arrayDescripcionCaracteristica);
            $objQuery->setParameter("arrayValor", $arrayValor);

            $objRsm->addScalarResult('ID_SERVICIO', 'ID_SERVICIO', 'string');

            $strSql= $strSelect.$strFrom.$strWhere;

            $objQuery->setSQL($strSql);
            $arrayDatos = $objQuery->getResult();
        } 
        catch (\Exception $ex) 
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayResultado['registros'] = $arrayDatos;
        $arrayResultado['error']     = $strMensajeError;
        return $arrayResultado;
    }


    /**
     * Documentación para el método 'getServicioPorCaracteristica'.
     *
     * Método encargado de retornar los servicios asociados a la descripción enviada por parámetro.
     *
     * Costo 8
     *
     * @param array $arrayParametros [
     *                                  "arrayDescripcionCaracteristica" => Descripción de la caracteristica a buscar.
     *                                  "arrayValor"                     => valor del servicio a buscar.
     *                               ]
     *
     * @return array $arrayResultado arreglo de los servicios.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 17-05-2021
     */
    public function findProdCaractComp($arrayParametros)
    {
        $objRsm     = new ResultSetMappingBuilder($this->_em);
        $objQuery   = $this->_em->createNativeQuery(null, $objRsm);

        $strSql="SELECT
                apcc.id_prod_carac_comp,
                adpc.id_producto_caracterisitica,
                adpc.caracteristica_id,
                adca.descripcion_caracteristica,
                adca.tipo_ingreso,
                apcc.es_visible,
                apcc.editable,
                apcc.estado,
                apcc.valores_seleccionable,
                apcc.valores_default
            FROM
                db_comercial.admi_producto                    adpr,
                db_comercial.admi_producto_caracteristica     adpc,
                db_comercial.admi_prod_carac_comportamiento   apcc,
                db_comercial.admi_caracteristica              adca
            WHERE
                adpr.id_producto = adpc.producto_id
                AND adpc.caracteristica_id = adca.id_caracteristica
                AND adpc.id_producto_caracterisitica = apcc.producto_caracteristica_id (+)
                AND adpc.producto_id = :productoId
                AND adpc.visible_comercial = :visibleComer";


        $objQuery->setParameter("productoId"   , $arrayParametros['productoId']);
        $objQuery->setParameter("visibleComer" , $arrayParametros['visibleComercial']);


        $objRsm->addScalarResult('ID_PROD_CARAC_COMP'          ,'idProdCaracComp'           , 'integer');
        $objRsm->addScalarResult('ID_PRODUCTO_CARACTERISITICA' ,'idProductoCaracteristica'  , 'integer');
        $objRsm->addScalarResult('CARACTERISTICA_ID'           ,'caracteristicaId'          , 'integer');
        $objRsm->addScalarResult('DESCRIPCION_CARACTERISTICA'  ,'descripcionCaracteristica' , 'string');
        $objRsm->addScalarResult('TIPO_INGRESO'                ,'tipoIngreso'               , 'string');
        $objRsm->addScalarResult('ES_VISIBLE'                  ,'esVisible'                 , 'integer');
        $objRsm->addScalarResult('EDITABLE'                    ,'editable'                  , 'integer');
        $objRsm->addScalarResult('ESTADO'                      ,'estado'                    , 'string');
        $objRsm->addScalarResult('VALORES_SELECCIONABLE'       ,'valoresSeleccionable'      , 'string');
        $objRsm->addScalarResult('VALORES_DEFAULT'             ,'valoresDefault'            , 'string');


        $objQuery->setSQL($strSql);
        $arrayRespuesta = $objQuery->getResult();

        return $arrayRespuesta;
    }

    /**
     * Documentación para el método 'findProdAdicCaracteristica'.
     *
     * Método encargado de retornar las caracteristica del producto.
     *
     * Costo 8
     *
     * @param array $arrayParametros [
     *                                  "intIdProducto"         => Id producto,
     *                                  "strEstado"             => estado.
     *                                  "strVisibleComercial    => Es visible comercial ?
     *                               ]
     *
     * @return array $arrayResultado arreglo de las caracteristica del producto.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 17-05-2021
     */
    public function findProdAdicCaracteristica($arrayParametros)
    {
        $objRsm     = new ResultSetMappingBuilder($this->_em);
        $objQuery   = $this->_em->createNativeQuery(null, $objRsm);

        $strSql="SELECT PCA.ID_PRODUCTO_CARACTERISITICA,
                        CAR.DESCRIPCION_CARACTERISTICA
                FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA PCA
                    LEFT JOIN DB_COMERCIAL.ADMI_CARACTERISTICA CAR
                    ON PCA.CARACTERISTICA_ID = CAR.ID_CARACTERISTICA
                WHERE PCA.PRODUCTO_ID = :productoId
                    AND PCA.ESTADO = :estado
                    AND PCA.VISIBLE_COMERCIAL = :visibleCom";

        $objQuery->setParameter("productoId"   , $arrayParametros['productoId']);
        $objQuery->setParameter("estado"       , $arrayParametros['estado']);
        $objQuery->setParameter("visibleCom"   , 'SI');

        $objRsm->addScalarResult('ID_PRODUCTO_CARACTERISITICA' ,'idProductoCaracteristica'  , 'integer');
        $objRsm->addScalarResult('DESCRIPCION_CARACTERISTICA'  ,'descripcionCaracteristica' , 'string');

        $objQuery->setSQL($strSql);
        $arrayRespuesta = $objQuery->getResult();

        return $arrayRespuesta;
    }

}
