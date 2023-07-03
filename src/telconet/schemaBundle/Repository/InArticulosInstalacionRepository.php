<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;

class InArticulosInstalacionRepository extends EntityRepository
{

    /**
     * Funcion que obtiene elementos TRANSCEIVER de la base de datos del NAF
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.0 2016-06-23
     * 
     * @param array $parametros arreglo de parámetros
     *                          ['modeloElemento','tipoActivo','serial','start','limit','idEmpresa' ]
     * 
     * @return array con resultado $resultado
     */
    public function getElementosTransceiver($parametros)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->from('schemaBundle:InArticulosInstalacion', 'e')
            ->where('e.tipoArticulo = ?1')
            ->setParameter(1, $parametros['tipoActivo'] );

        if($parametros['serial'] != "")
        {
            $qb->andWhere('UPPER(e.numeroSerie) like ?2');
            $qb->setParameter(2, '%' . $parametros['serial'] . '%');
        }

        if($parametros['modeloElemento'] != "")
        {
            $qb->andWhere('e.modelo = ?3')
                ->setParameter(3, $parametros['modeloElemento']);
        }
        if($parametros['idEmpresa'] != "")
        {
            $qb->andWhere('e.idCompania = ?4')
                ->setParameter(4, $parametros['idEmpresa']);
        }

        //contar cuantos datos trae en total
        $qb->select('count(e)');
        $total = $qb->getQuery()->getSingleScalarResult();

        //select de datos
        $qb->select('e');
        $qb->orderBy('e.descripcion', 'ASC');

        //datos con limits
        if($parametros['start'] != '')
        {
            $qb->setFirstResult($parametros['start']);
        }

        if($parametros['limit'] != '')
        {
            $qb->setMaxResults($parametros['limit']);
        }

        $query = $qb->getQuery();

        $datos = $query->getResult();
        $resultado['registros'] = $datos;
        $resultado['total'] = $total;

        return $resultado;
    }

    /**
     * Función que obtiene elementos TRANSCEIVER de la base de datos del NAF
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.0 2016-06-23
     * 
     * @param array $parametros arreglo de parámetros
     *                          ['modeloElemento','tipoElemento','serial',
     *                           'start','limit','idEmpresa','emComercial']
     * 
     * @return array con resultado $resultado
     */
    public function generarJsonElementoTransceiver($parametros)
    {
        $arr_encontrados   = array();
        $encontrados       = $this->getElementosTransceiver($parametros);
        $datos             = $encontrados['registros'];
        $total             = $encontrados['total'];
        $nombreResponsable = "S/R";
            
        foreach($datos as $elementoNaf)
        {
            $estadoNaf = $elementoNaf->getEstado();
            if($estadoNaf == "PI")
            {
                $estadoNaf = "Pendiente Instalar";
            }
            else if($estadoNaf == "RE")
            {
                $estadoNaf = "Retirado";
            }
            else if($estadoNaf == "IN")
            {
                $estadoNaf = "Instalado";
            }
            else
            {
                $estadoNaf = "Desconocido";
            }
            $cedulaResponsable = $elementoNaf->getCedula();
            $responsable = $parametros['emComercial']->getRepository('schemaBundle:InfoPersona')
                                                     ->findOneBy(array("identificacionCliente" => $cedulaResponsable));
            if($responsable)
            {
                $nombreResponsable = $responsable->getNombres() . " " . $responsable->getApellidos();
            }
            $idActivo = array('secuencia'     => $elementoNaf->getSecuencia(),
                              'idInstalacion' => $elementoNaf->getIdInstalacion(),
                              'idCompania'    => $elementoNaf->getIdCompania());
            
            $arr_encontrados[] = array('idActivo'    => $idActivo,
                                       'descripcion' => $elementoNaf->getDescripcion(),
                                       'modelo'      => $elementoNaf->getModelo(),
                                       'numeroSerie' => $elementoNaf->getNumeroSerie(),
                                       'responsable' => $nombreResponsable,
                                       'estado'      => $estadoNaf,
                                       'fecha'       => $elementoNaf->getFecha()->format('Y-m-d H:i:s')
                        );
        }
        $resultado = '{"total":"' . $total . '","encontrados":' . json_encode($arr_encontrados) . '}';

        return $resultado;
    }

     /**
     * Función que obtiene los elementos junto con su marca y modelo de la base de datos del NAF y TELCOS
     * 
     * @author Angel Cudco P. <acudco@telconet.ec>
     * @version 1.0 2023-02-17
     * 
     * @param array $arrayParametro arreglo de parámetros
     *                          ['strSerie','strModelo','strMarca',
     *                           'strEstado']
     * 
     * @return array con resultado $resultado
     */
    public function getMarcaModelosBySerie($arrayParametro)
    {

        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);
    
        $strSerie     = $arrayParametro['strSerie'];
        $strModelo    = $arrayParametro['strModelo'];
        $strMarca     = $arrayParametro['strMarca'];
        $strEstado    = $arrayParametro['strEstado'];
    
        $strSql = " SELECT NAI.ID_COMPANIA, 
                        NAI.ID_INSTALACION, 
                        NAI.ESTADO,
                        NAI.DESCRIPCION, 
                        NAI.MODELO,
                        MOD_T.NOMBRE_MODELO_ELEMENTO,
                        NAI.NUMERO_SERIE AS SERIE_ELEMENTO, 
                        NAI.MARCA, 
                        MRC_NF.COD_MARCA,
                        MRC_NF.DESCRIPCION AS DESCRIPCION_MARCA,
                        MRC_TL.NOMBRE_MARCA_ELEMENTO,
                        MOD_T.ID_MODELO_ELEMENTO,
                        MOD_T.MARCA_ELEMENTO_ID,
                        MRC_TL.ID_MARCA_ELEMENTO
                    FROM NAF47_TNET.IN_ARTICULOS_INSTALACION NAI
                    INNER JOIN NAF47_TNET.AF_MARCAS MRC_NF
                        ON (NAI.MARCA = MRC_NF.COD_MARCA) AND (NAI.ID_COMPANIA=MRC_NF.NO_CIA)
                    INNER JOIN DB_INFRAESTRUCTURA.ADMI_MARCA_ELEMENTO MRC_TL
                        ON UPPER(MRC_NF.DESCRIPCION)=UPPER(MRC_TL.NOMBRE_MARCA_ELEMENTO)
                    INNER JOIN DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO MOD_T
                        ON (UPPER(NAI.MODELO)=UPPER(MOD_T.NOMBRE_MODELO_ELEMENTO)) 
                            AND (MRC_TL.ID_MARCA_ELEMENTO = MOD_T.MARCA_ELEMENTO_ID)                  
                    WHERE NAI.NUMERO_SERIE                  = :strInSerie
                        AND NAI.MODELO                      = :strInModelo
                        AND NAI.ESTADO                      = :strInEstado
                        AND MRC_TL.NOMBRE_MARCA_ELEMENTO    = :strInMarca
                        AND NAI.ID_COMPANIA                 = '10'
                        AND MRC_NF.NO_CIA                   = '10'
                        AND MOD_T.ESTADO                    = 'Activo'
                    ORDER BY NAI.ID_INSTALACION";
    
            $objQuery->setParameter("strInSerie", $strSerie);
            $objQuery->setParameter("strInModelo", $strModelo); 
            $objQuery->setParameter("strInEstado", $strEstado);
            $objQuery->setParameter("strInMarca", $strMarca);
                           
            $objRsm->addScalarResult('DESCRIPCION_MARCA', 'marcaElemento', 'string');
            $objRsm->addScalarResult('SERIE_ELEMENTO', 'serieElemento', 'string');
            $objRsm->addScalarResult('ID_MODELO_ELEMENTO', 'idModeloElemento', 'integer');
            $objRsm->addScalarResult('MARCA_ELEMENTO_ID', 'idMarcaElemento', 'integer');
    
            $objQuery->setSQL($strSql);
            $arrayDatos = $objQuery->getScalarResult();
            
            return $arrayDatos;
    }

    /**
     * Función que permite validar que exista un elemento en la base datos del NAF mediante su número de serie y estado
     * 
     * @author Angel Cudco P. <acudco@telconet.ec>
     * @version 1.0 2023-02-17
     * 
     * @param array $arrayParametro arreglo de parámetros
     *                          ['strSerie','strEstado']
     * 
     * @return array con resultado $resultado
     */
    public function validarSerieNaf($arrayParametros)
    {

        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);
    
        $strSerie  = $arrayParametros['strSerie'];        
        $strEstado = $arrayParametros['strEstado'];
        $strTipo   = $arrayParametros['strTipo'];
    
        $strSql    = "SELECT AIN.DESCRIPCION,
                            AIN.NUMERO_SERIE,
                            MDL.ID_MODELO_ELEMENTO,
                            AIN.MODELO,
                            MDL.NOMBRE_MODELO_ELEMENTO,
                            MDL.TIPO_ELEMENTO_ID,
                            TIPO.ID_TIPO_ELEMENTO,
                            TIPO.NOMBRE_TIPO_ELEMENTO,
                            MDL.MARCA_ELEMENTO_ID,
                            MRC.ID_MARCA_ELEMENTO,
                            MRC.NOMBRE_MARCA_ELEMENTO,
                            AIN.MARCA,
                            MRK_NF.CODIGO,
                            MRK_NF.DESCRIPCION AS MARCA_NAF,
                            AIN.ESTADO,
                            AIN.ID_COMPANIA,
                            MRK_NF.NO_CIA,
                            MDL.CAPACIDAD_ENTRADA,
                            AIN.MAC
                    FROM NAF47_TNET.IN_ARTICULOS_INSTALACION AIN
                    INNER JOIN DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO MDL
                        ON UPPER(AIN.MODELO)=UPPER(MDL.NOMBRE_MODELO_ELEMENTO)
                    INNER JOIN DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO TIPO
                        ON MDL.TIPO_ELEMENTO_ID=TIPO.ID_TIPO_ELEMENTO
                    INNER JOIN DB_INFRAESTRUCTURA.ADMI_MARCA_ELEMENTO MRC
                        ON MDL.MARCA_ELEMENTO_ID=MRC.ID_MARCA_ELEMENTO
                    INNER JOIN NAF47_TNET.MARCAS MRK_NF
                        ON (AIN.MARCA=MRK_NF.CODIGO) AND (MRC.NOMBRE_MARCA_ELEMENTO=MRK_NF.DESCRIPCION) AND (AIN.ID_COMPANIA=MRK_NF.NO_CIA)
                    WHERE AIN.ID_COMPANIA               = '10'
                        AND MRK_NF.NO_CIA               = '10'
                        AND TIPO.NOMBRE_TIPO_ELEMENTO   = :strInTipo
                        AND AIN.NUMERO_SERIE            = :strInSerie
                        AND AIN.ESTADO                  = :strInEstado
                        AND AIN.MODELO NOT IN (SELECT VALOR1 
                                               FROM DB_GENERAL.ADMI_PARAMETRO_DET 
                                               WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO 
                                                                     FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                                     WHERE NOMBRE_PARAMETRO = 'EQUIPOS SIN MODELO')
                                                )";
    
        $objQuery->setParameter("strInTipo", $strTipo);    
        $objQuery->setParameter("strInSerie", $strSerie);        
        $objQuery->setParameter("strInEstado", $strEstado);
        
                        
        $objRsm->addScalarResult('NUMERO_SERIE', 'serieElemento', 'string');
        $objRsm->addScalarResult('DESCRIPCION', 'descripcion', 'string');
        $objRsm->addScalarResult('NOMBRE_MARCA_ELEMENTO', 'marcaElemento', 'string');        
        $objRsm->addScalarResult('MODELO', 'modeloElemento', 'string');
        $objRsm->addScalarResult('CAPACIDAD_ENTRADA', 'capacidadEntrada', 'string');
        $objRsm->addScalarResult('MAC', 'mac', 'string');
        $objRsm->addScalarResult('ID_MARCA_ELEMENTO', 'idMarcaElemento', 'string');
        $objRsm->addScalarResult('ID_MODELO_ELEMENTO', 'idModeloElemento', 'string');
    
        $objQuery->setSQL($strSql);
        $arrayDatos = $objQuery->getScalarResult();
        
        return $arrayDatos;
    }

    /**
     * Función que trae los datos a utilizarse en la generación dinámica del nombre
     * 
     * @author Angel Cudco P. <acudco@telconet.ec>
     * @version 1.0 2023-03-17
     * 
     * @param array $arrayParametro arreglo de parámetros
     *                          ['strTipoElemento',
     *                           'intIdNodoContenedor',
     *                           'strClaseElemento',
     *                           'strBndera']
     * 
     * @return array con resultado $resultado
     */
    public function getDatosNombreAutomatico($arrayParametros)
    {
        $objRsm                 = new ResultSetMappingBuilder($this->_em);
        $objQuery               = $this->_em->createNativeQuery(null, $objRsm);
        
        $strTipoElemento        = $arrayParametros['strTipoElemento'];
        $intIdNodoContenedor    = $arrayParametros['intIdNodoContenedor'];
        $strClaseElemento       = $arrayParametros['strClaseElemento'];
        $strBndera              = $arrayParametros['strBndera'];
               
        if(!$strBndera)
        {
            $strSql  = "SELECT  DISTINCT(HIJO.NOMBRE_ELEMENTO) AS ELEMENTO,
                            R_ELM.ELEMENTO_ID_A AS ID_NODO,
                            INF_ELM.ID_ELEMENTO AS ID_NODO_CONTNR,
                            UPPER(INF_ELM.NOMBRE_ELEMENTO) AS NOMBRE_CONTENEDOR,       
                            R_ELM.ELEMENTO_ID_B AS ID_CONTENEDOR, 
                            TIP.NOMBRE_TIPO_ELEMENTO,
                            HIJO.NOMBRE_ELEMENTO,
                            HIJO.DESCRIPCION_ELEMENTO,
                            MDL.NOMBRE_MODELO_ELEMENTO,
                            UBICACION.NOMBRE_CANTON,
                            UBICACION.ID_CANTON,
                            UPPER(CANTON.SIGLA) AS SIGLA,
                            DETALLE.DETALLE_VALOR AS CLASE
                        FROM DB_INFRAESTRUCTURA.INFO_RELACION_ELEMENTO R_ELM
                        INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO INF_ELM
                            ON R_ELM.ELEMENTO_ID_A       = INF_ELM.ID_ELEMENTO
                        INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO HIJO
                            ON R_ELM.ELEMENTO_ID_B       = HIJO.ID_ELEMENTO
                        LEFT JOIN DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO MDL
                            ON HIJO.MODELO_ELEMENTO_ID   = MDL.ID_MODELO_ELEMENTO
                        LEFT JOIN DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO TIP    
                            ON MDL.TIPO_ELEMENTO_ID      = TIP.ID_TIPO_ELEMENTO
                        LEFT JOIN DB_INFRAESTRUCTURA.vista_info_nodos UBICACION
                            ON INF_ELM.NOMBRE_ELEMENTO   = UBICACION.NOMBRE_ELEMENTO
                        LEFT JOIN DB_INFRAESTRUCTURA.ADMI_CANTON CANTON
                            ON UBICACION.ID_CANTON       = CANTON.ID_CANTON
                        LEFT JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO DETALLE
                            ON R_ELM.ELEMENTO_ID_B       = DETALLE.ELEMENTO_ID
                        WHERE R_ELM.ELEMENTO_ID_A        = :intIdNodoContenedorInpt
                            AND TIP.NOMBRE_TIPO_ELEMENTO = :strTipoElementoInpt
                            AND DETALLE.DETALLE_VALOR    = :strClaseElementoInpt
                        ORDER BY HIJO.NOMBRE_ELEMENTO DESC";
        }
        elseif($strBndera=='NUEVO')
        {
            $strSql  = "SELECT  DISTINCT(HIJO.NOMBRE_ELEMENTO) AS ELEMENTO,
                            R_ELM.ELEMENTO_ID_A AS ID_NODO,
                            INF_ELM.ID_ELEMENTO AS ID_NODO_CONTNR,
                            UPPER(INF_ELM.NOMBRE_ELEMENTO) AS NOMBRE_CONTENEDOR,       
                            R_ELM.ELEMENTO_ID_B AS ID_CONTENEDOR, 
                            TIP.NOMBRE_TIPO_ELEMENTO,
                            HIJO.NOMBRE_ELEMENTO,
                            HIJO.DESCRIPCION_ELEMENTO,
                            MDL.NOMBRE_MODELO_ELEMENTO,
                            UBICACION.NOMBRE_CANTON,
                            UBICACION.ID_CANTON,
                            UPPER(CANTON.SIGLA) AS SIGLA,
                            DETALLE.DETALLE_VALOR AS CLASE
                        FROM DB_INFRAESTRUCTURA.INFO_RELACION_ELEMENTO R_ELM
                        INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO INF_ELM
                            ON R_ELM.ELEMENTO_ID_A       = INF_ELM.ID_ELEMENTO
                        INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO HIJO
                            ON R_ELM.ELEMENTO_ID_B       = HIJO.ID_ELEMENTO
                        LEFT JOIN DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO MDL
                            ON HIJO.MODELO_ELEMENTO_ID   = MDL.ID_MODELO_ELEMENTO
                        LEFT JOIN DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO TIP    
                            ON MDL.TIPO_ELEMENTO_ID      = TIP.ID_TIPO_ELEMENTO
                        LEFT JOIN DB_INFRAESTRUCTURA.vista_info_nodos UBICACION
                            ON INF_ELM.NOMBRE_ELEMENTO   = UBICACION.NOMBRE_ELEMENTO
                        LEFT JOIN DB_INFRAESTRUCTURA.ADMI_CANTON CANTON
                            ON UBICACION.ID_CANTON       = CANTON.ID_CANTON
                        LEFT JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO DETALLE
                            ON R_ELM.ELEMENTO_ID_B       = DETALLE.ELEMENTO_ID
                        WHERE R_ELM.ELEMENTO_ID_A        = :intIdNodoContenedorInpt
                        ORDER BY HIJO.NOMBRE_ELEMENTO DESC";
        }
        elseif($strBndera=='NODO_VACIO')
        {
            $strSql  = "SELECT DISTINCT (NODO.ID_ELEMENTO),
                            UPPER(NODO.NOMBRE_ELEMENTO) AS ELEMENTO,
                            UPPER(NODO.NOMBRE_ELEMENTO) AS NOMBRE_CONTENEDOR,
                            NODO.NOMBRE_CANTON,
                            UPPER(CANTON.SIGLA) AS SIGLA
                        FROM DB_INFRAESTRUCTURA.vista_info_nodos NODO
                        INNER JOIN DB_INFRAESTRUCTURA.ADMI_CANTON CANTON
                            ON NODO.ID_CANTON           = CANTON.ID_CANTON
                        WHERE ID_ELEMENTO               = :intIdNodoContenedorInpt";
        }
        elseif($strBndera=='SIN_CLASE')
        {
            $strSql  = "SELECT  DISTINCT(HIJO.NOMBRE_ELEMENTO) AS ELEMENTO,
                                R_ELM.ELEMENTO_ID_A AS ID_NODO,
                                INF_ELM.ID_ELEMENTO AS ID_NODO_CONTNR,
                                UPPER(INF_ELM.NOMBRE_ELEMENTO) AS NOMBRE_CONTENEDOR,       
                                R_ELM.ELEMENTO_ID_B AS ID_CONTENEDOR, 
                                TIP.NOMBRE_TIPO_ELEMENTO,
                                HIJO.NOMBRE_ELEMENTO,
                                HIJO.DESCRIPCION_ELEMENTO,
                                MDL.NOMBRE_MODELO_ELEMENTO,
                                UBICACION.NOMBRE_CANTON,
                                UBICACION.ID_CANTON,
                                UPPER(CANTON.SIGLA) AS SIGLA
                        FROM DB_INFRAESTRUCTURA.INFO_RELACION_ELEMENTO R_ELM
                        INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO INF_ELM
                            ON R_ELM.ELEMENTO_ID_A       = INF_ELM.ID_ELEMENTO
                        INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO HIJO
                            ON R_ELM.ELEMENTO_ID_B       = HIJO.ID_ELEMENTO
                        LEFT JOIN DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO MDL
                            ON HIJO.MODELO_ELEMENTO_ID   = MDL.ID_MODELO_ELEMENTO
                        LEFT JOIN DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO TIP    
                            ON MDL.TIPO_ELEMENTO_ID      = TIP.ID_TIPO_ELEMENTO
                        LEFT JOIN DB_INFRAESTRUCTURA.vista_info_nodos UBICACION
                            ON INF_ELM.NOMBRE_ELEMENTO = UBICACION.NOMBRE_ELEMENTO
                        LEFT JOIN DB_INFRAESTRUCTURA.ADMI_CANTON CANTON
                            ON UBICACION.ID_CANTON=CANTON.ID_CANTON
                        WHERE R_ELM.ELEMENTO_ID_A        = :intIdNodoContenedorInpt
                            AND TIP.NOMBRE_TIPO_ELEMENTO = :strTipoElementoInpt
                        ORDER BY HIJO.NOMBRE_ELEMENTO DESC";
        }
        else
        {
            $strSinClase='SI';
        }
                
        $objQuery->setParameter("strTipoElementoInpt", $strTipoElemento);    
        $objQuery->setParameter("intIdNodoContenedorInpt", $intIdNodoContenedor);        
        $objQuery->setParameter("strClaseElementoInpt", $strClaseElemento);
        
        $objRsm->addScalarResult('ELEMENTO', 'strNombreElemento', 'string');
        $objRsm->addScalarResult('NOMBRE_CONTENEDOR', 'strContenedor', 'string');
        $objRsm->addScalarResult('NOMBRE_TIPO_ELEMENTO', 'strTipoElemento', 'string');
        $objRsm->addScalarResult('SIGLA', 'strSiglaCanton', 'string');

        $objQuery->setSQL($strSql);
        $arrayDatos = $objQuery->getScalarResult();
        
        return $arrayDatos;

        
    }

}