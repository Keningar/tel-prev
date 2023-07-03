<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoEnlaceRepository extends EntityRepository
{
    
    /*
     * getElementosPorInterfaz
     * 
     * Este metodo recibe una interfaz de un elemento y nos devuelve todos los elementos que salen de esta interfaz segun el tipo del elemento 
     * y el nivel, devuelve en un solo string separados por "|"
     * ID_ELEMENTO, NOMBRE_ELEMENTO, ID_INTERFACE_ELEMENTO, NOMBRE_TIPO_ELEMENTO, NIVEL, ELEMENTO_CONTENEDOR;
     * 
     * @param integer $interfaceElementoId  
     * @param integer $tipoElementoPadre 
     * @param integer $nivel 
     * 
     * @author  John Vera <javera@telconet.ec>
     * @version 1.0 15-04-2015 
     *      
     */
    public function getElementosPorInterfaz($interfaceElementoId, $tipoElementoPadre, $nivel)
    {
        $elementos = str_pad($elementos, 32767, " ");

        $sql = "BEGIN :ruta := INFRK_TRANSACCIONES.INFRF_GET_ELEMENTOS_INTERFAZ( :interfaceElementoSplitterId , :tipoElementoPadre, :nivel); END; ";

        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->bindParam('ruta', $elementos);
        $stmt->bindParam(':interfaceElementoSplitterId', $interfaceElementoId);
        $stmt->bindParam(':tipoElementoPadre', $tipoElementoPadre);
        $stmt->bindParam(':nivel', $nivel);

        $stmt->execute();

        return $elementos;
    }

    public function getTipoMedioByUltimaMilla($ultimaMillaId)
    {   
        $query = "	SELECT tm.id, tm.nombreTipoMedio 
					FROM schemaBundle:InfoEnlace e, schemaBundle:AdmiTipoMedio tm 
					WHERE e.tipoMedioId = tm.id 
					AND e.id = '$ultimaMillaId' ";

        return $this->_em->createQuery($query)->getResult();
    }
    
    /**
     * Funcion que sirve para generar un json con los enlaces segun busqueda
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0
     * 
     * Se agrega la obtención de los datos de buffer e hilos
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1 2-05-2016
     *
     * @author  Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 11-06-2018 - Se realizan mejoras en el query para hacerlo mas optimo y menos costoso
     */
    public function generarJsonEnlaces($nombreElementoA, $nombreElementoB,$interfaceA, $interfaceB, $tipoMedio, $tipoEnlace, $estado,$idEmpresa,$start,$limit, $em){
        $arr_encontrados = array();

        $encontrados = $this->getEnlaces($nombreElementoA, $nombreElementoB,$interfaceA,$interfaceB,$tipoMedio,$tipoEnlace,$estado,$idEmpresa,$start,$limit);

        if ($encontrados["total"] > 0) {

            $num = $encontrados["total"];
            
            foreach ($encontrados["registros"] as $entidad)
            {
                $interfaceIniId = $em->getRepository('schemaBundle:InfoInterfaceElemento')->find($entidad["interfaceElementoIni"]);
                
                $interfaceFinId = $em->getRepository('schemaBundle:InfoInterfaceElemento')->find($entidad["interfaceElementoFin"]);

                if(!empty($entidad["bufferHiloId"]))
                {
                    $bufferHilo = $em->getRepository('schemaBundle:InfoBufferHilo')->find($entidad["bufferHiloId"]);

                    if(is_object($bufferHilo))
                    {
                        $bufferColor    = $bufferHilo->getBufferId()->getColorBuffer();
                        $bufferNumero   = $bufferHilo->getBufferId()->getNumeroBuffer();
                        $hiloColor      = $bufferHilo->getHiloId()->getColorHilo();
                        $hiloNumero     = $bufferHilo->getHiloId()->getNumeroHilo();
                    }
                    else
                    {
                        $bufferColor    = "NA";
                        $bufferNumero   = "NA";
                        $hiloColor      = "NA";
                        $hiloNumero     = "NA";
                    }
                }
                else
                {
                    $bufferColor    = "NA";
                    $bufferNumero   = "NA";
                    $hiloColor      = "NA";
                    $hiloNumero     = "NA";
                }

                $arr_encontrados[]=array('idEnlace' =>$entidad["idEnlace"],
                                         'interfaceElementoIniId'   => trim($interfaceIniId),
                                         'interfaceElementoIni'     => $interfaceIniId->getNombreInterfaceElemento(),
                                         'elementoIniNombre'        => $interfaceIniId->getElementoId()->getNombreElemento(),
                                         'interfaceElementoFinId'   => trim($interfaceFinId),
                                         'interfaceElementoFin'     => $interfaceFinId->getNombreInterfaceElemento(),
                                         'elementoFinNombre'        => $interfaceFinId->getElementoId()->getNombreElemento(),
                                         'bufferColor'              => $bufferColor,
                                         'bufferNumero'             => $bufferNumero,
                                         'hiloColor'                => $hiloColor,
                                         'hiloNumero'               => $hiloNumero,
                                         'estado'                   => $entidad["estado"],
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($entidad["estado"])=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($entidad["estado"])=='Eliminado' ? 'button-grid-invisible':'button-grid-delete')
                                         
                    );
            }

            $data=json_encode($arr_encontrados);
            $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';

            return $resultado;
        }
        
    }
    
    /**
     * Documentacion de funcion getEnlaces
     * funcion que permite recuperar la infomarcion de los enlaces creados
     * 
     * @param string  $nombreElementoA Description
     * @param string  $nombreElementoB Description
     * @param integer $interfaceA Description
     * @param integer $interfaceB Description
     * @param integer $tipoMedio Description
     * @param integer $tipoEnlace Description
     * @param integer $estado Description
     * @param integer $idEmpresa Description
     * @param integer $start Description
     * @param integer $limit Description
     *
     * @author  Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.0 11-06-2018 - Se realizan mejoras en el query para hacerlo mas optimo y menos costoso
     *
     * @author  Jesus Bozada <jbozada@telconet.ec>
     * @version 2.0 10-03-2015 - Se agrega validacion para no mostrar enlaces entre el mismo elemento
     *      
     * @version 1.0 Version Inicial
     * @return view
     */
    public function getEnlaces($nombreElementoA, 
                               $nombreElementoB, 
                               $interfaceA, 
                               $interfaceB,
                               $tipoMedio, 
                               $tipoEnlace, 
                               $estado, 
                               $idEmpresa, 
                               $start, 
                               $limit)
    {
        $qb      = $this->_em->createQueryBuilder();
        $qbCount = $this->_em->createQueryBuilder();
        $arrayRegistros = array();

        $qb->select(' e.estado estado, '
            . ' e.id idEnlace, '
            . ' IDENTITY(e.bufferHiloId) bufferHiloId, '
            . ' IDENTITY(e.interfaceElementoIniId) interfaceElementoIni, '
            . ' IDENTITY(e.interfaceElementoFinId) interfaceElementoFin ')

            ->from('schemaBundle:InfoEnlace', 'e')
            ->from('schemaBundle:InfoInterfaceElemento', 'interfaceA')
            ->from('schemaBundle:InfoElemento', 'elementoA')
            ->from('schemaBundle:InfoEmpresaElemento', 'empresaElementoA')
            ->from('schemaBundle:InfoInterfaceElemento', 'interfaceB')
            ->from('schemaBundle:InfoElemento', 'elementoB')
            ->from('schemaBundle:InfoEmpresaElemento', 'empresaElementoB')
            ->where('elementoA = interfaceA.elementoId')
            ->andWhere('elementoA = empresaElementoA.elementoId')
            ->andWhere("empresaElementoA.empresaCod = :paramCodEmpresaA ")
            ->andWhere('elementoB = interfaceB.elementoId')
            ->andWhere('elementoB = empresaElementoB.elementoId')
            ->andWhere("empresaElementoB.empresaCod = :paramCodEmpresaB")
            ->andWhere('e.interfaceElementoIniId = interfaceA')
            ->andWhere('e.interfaceElementoFinId = interfaceB');

        $qb->setParameter("paramCodEmpresaA", $idEmpresa);
        $qb->setParameter("paramCodEmpresaB", $idEmpresa);

        $qbCount->select('count(e.id)')
            ->from('schemaBundle:InfoEnlace', 'e')
            ->from('schemaBundle:InfoInterfaceElemento', 'interfaceA')
            ->from('schemaBundle:InfoElemento', 'elementoA')
            ->from('schemaBundle:InfoEmpresaElemento', 'empresaElementoA')
            ->from('schemaBundle:InfoInterfaceElemento', 'interfaceB')
            ->from('schemaBundle:InfoElemento', 'elementoB')
            ->from('schemaBundle:InfoEmpresaElemento', 'empresaElementoB')
            ->where('elementoA = interfaceA.elementoId')
            ->andWhere('elementoA = empresaElementoA.elementoId')
            ->andWhere("empresaElementoA.empresaCod = :paramCodEmpresaA ")
            ->andWhere('elementoB = interfaceB.elementoId')
            ->andWhere('elementoB = empresaElementoB.elementoId')
            ->andWhere("empresaElementoB.empresaCod = :paramCodEmpresaB")
            ->andWhere('e.interfaceElementoIniId = interfaceA')
            ->andWhere('e.interfaceElementoFinId = interfaceB');

        $qbCount->setParameter("paramCodEmpresaA", $idEmpresa);
        $qbCount->setParameter("paramCodEmpresaB", $idEmpresa);


        if($nombreElementoA != "")
        {
            $qb->andWhere("elementoA.nombreElemento like :paramNombreElementoA");
            $qb->setParameter("paramNombreElementoA", '%'.$nombreElementoA.'%');
            $qbCount->andWhere("elementoA.nombreElemento like :paramNombreElementoA");
            $qbCount->setParameter("paramNombreElementoA", '%'.$nombreElementoA.'%');
        }
        if($nombreElementoB != "")
        {
            $qb->andWhere("elementoB.nombreElemento like :paramNombreElementoB");
            $qb->setParameter("paramNombreElementoB", '%'.$nombreElementoB.'%');
            $qbCount->andWhere("elementoB.nombreElemento like :paramNombreElementoB");
            $qbCount->setParameter("paramNombreElementoB", '%'.$nombreElementoB.'%');
        }
        if($interfaceA != "")
        {
            $qb->andWhere("interfaceA = :paramInterfaceA ");
            $qb->setParameter("paramInterfaceA", $interfaceA );
            $qbCount->andWhere("interfaceA = :paramInterfaceA ");
            $qbCount->setParameter("paramInterfaceA", $interfaceA );
        }
        if($interfaceB != "")
        {
            $qb->andWhere("interfaceB = :paramInterfaceB ");
            $qb->setParameter("paramInterfaceB", $interfaceB );
            $qbCount->andWhere("interfaceB = :paramInterfaceB ");
            $qbCount->setParameter("paramInterfaceB", $interfaceB );
        }
        if($tipoMedio != "")
        {
            $qb->andWhere('e.tipoMedioId = :paramTipoMedio');
            $qb->setParameter("paramTipoMedio", $tipoMedio);
            $qbCount->andWhere('e.tipoMedioId = :paramTipoMedio');
            $qbCount->setParameter("paramTipoMedio", $tipoMedio);
        }
        if($tipoEnlace != "")
        {
            $qb->andWhere('e.tipoEnlace = :paramTipoEnlace');
            $qb->setParameter("paramTipoEnlace", $tipoEnlace);
            $qbCount->andWhere('e.tipoEnlace = :paramTipoEnlace');
            $qbCount->setParameter("paramTipoEnlace", $tipoEnlace);
        }
        if($estado != "Todos")
        {
            $qb->andWhere("e.estado = :paramEstado");
            $qb->setParameter("paramEstado", $estado);
            $qbCount->andWhere("e.estado = :paramEstado");
            $qbCount->setParameter("paramEstado", $estado);
        }

        // se agrega filtro para no observar los enlaces generados automaticamente por el sistema
        $qb->andWhere('interfaceA.elementoId != interfaceB.elementoId');
        $qbCount->andWhere('interfaceA.elementoId != interfaceB.elementoId');

        $queryCount = $qbCount->getQuery();
        $arrayRegistros["total"] = $queryCount->getSingleScalarResult();

        if($start != '')
            $qb->setFirstResult($start);
        if($limit != '')
            $qb->setMaxResults($limit);
        $query = $qb->getQuery();

        $arrayRegistros["registros"] = $query->getResult();

        return $arrayRegistros;
    }
    
    /**
     * getEnlacePadreElemento
     * funcion que permite recuperar el enlace padre de un elemento
     * 
     * @param string  $idElemento
     * 
     * @author  John Vera <javera@telconet.ec>
     * @version 1.0 19-05-2016
     *      
     * @version 1.0
     * @return array $result
     */    
    public function getEnlacePadreElemento($idElemento)
    {        
        $objResultSetMap    = new ResultSetMappingBuilder($this->_em);
        $objNativeQuery     = $this->_em->createNativeQuery(null, $objResultSetMap);
        
        $strSql             = " SELECT EN.ID_ENLACE,
                                    EN.INTERFACE_ELEMENTO_FIN_ID,
                                    EN.INTERFACE_ELEMENTO_INI_ID,
                                    EN.CAPACIDAD_INPUT,
                                    EN.CAPACIDAD_OUTPUT,
                                    EN.ESTADO,
                                    EN.TIPO_ENLACE,
                                    EN.TIPO_MEDIO_ID
                                FROM INFO_ELEMENTO E,
                                  INFO_INTERFACE_ELEMENTO IE,
                                  INFO_ENLACE EN
                                WHERE E.ID_ELEMENTO          = IE.ELEMENTO_ID
                                AND IE.ID_INTERFACE_ELEMENTO = EN.INTERFACE_ELEMENTO_FIN_ID
                                AND EN.ESTADO                = :estado
                                AND E.ID_ELEMENTO            = :idElemento 
                                AND EN.TIPO_ENLACE           = :tipoEnlace ";
        
        $objNativeQuery->setParameter("idElemento", $idElemento);
        $objNativeQuery->setParameter("estado", "Activo");
        $objNativeQuery->setParameter("tipoEnlace", "PRINCIPAL");
        
        $objResultSetMap->addScalarResult('ID_ENLACE','idEnlace','integer');
        $objResultSetMap->addScalarResult('INTERFACE_ELEMENTO_FIN_ID','interfaceElementoFinId','integer');
        $objResultSetMap->addScalarResult('INTERFACE_ELEMENTO_INI_ID','interfaceElementoIniId','integer');
        $objResultSetMap->addScalarResult('CAPACIDAD_INPUT','capacidadInput','integer');
        $objResultSetMap->addScalarResult('CAPACIDAD_OUTPUT','capacidadOutput','integer');
        $objResultSetMap->addScalarResult('ESTADO','estado','string');
        $objResultSetMap->addScalarResult('TIPO_ENLACE','tipoEnlace','string');
        $objResultSetMap->addScalarResult('TIPO_MEDIO_ID','tipoMedioId','integer');
        
        $objNativeQuery->setSQL($strSql);
        
        $result = $objNativeQuery->getResult();

        return $result;
    }
    
    public function getArrayInfoEnlaceUm($idInterfaceElementoConectorFin)
    {        
        $objResultSetMap    = new ResultSetMappingBuilder($this->_em);
        $objNativeQuery     = $this->_em->createNativeQuery(null, $objResultSetMap);
        
        $strSql             = " SELECT
                                    HILO.COLOR_HILO COLOR_HILO,
                                    CLASE_TIPO_MEDIO.NOMBRE_CLASE_TIPO_MEDIO UM
                                FROM 
                                    DB_INFRAESTRUCTURA.INFO_ENLACE ENLACE_FIN,
                                    DB_INFRAESTRUCTURA.INFO_ENLACE ENLACE_INI,
                                    DB_INFRAESTRUCTURA.ADMI_CLASE_TIPO_MEDIO CLASE_TIPO_MEDIO,
                                    DB_INFRAESTRUCTURA.INFO_BUFFER_HILO BUFFER_HILO,
                                    DB_INFRAESTRUCTURA.ADMI_HILO HILO
                                WHERE
                                    ENLACE_FIN.INTERFACE_ELEMENTO_FIN_ID = :idInterfaceElementoConectorFin
                                AND ENLACE_INI.ESTADO                    = :estado
                                AND ENLACE_FIN.ESTADO                    = :estado
                                AND ENLACE_INI.INTERFACE_ELEMENTO_FIN_ID = ENLACE_FIN.INTERFACE_ELEMENTO_INI_ID
                                AND ENLACE_INI.BUFFER_HILO_ID            = BUFFER_HILO.ID_BUFFER_HILO
                                AND BUFFER_HILO.HILO_ID                  = HILO.ID_HILO
                                AND HILO.CLASE_TIPO_MEDIO_ID             = CLASE_TIPO_MEDIO.ID_CLASE_TIPO_MEDIO";
        
        $objNativeQuery->setParameter("idInterfaceElementoConectorFin", $idInterfaceElementoConectorFin);
        $objNativeQuery->setParameter("estado", "Activo");
        
        
        $objResultSetMap->addScalarResult('COLOR_HILO','COLOR_HILO','string');
        $objResultSetMap->addScalarResult('UM','UM','string');
        
        $objNativeQuery->setSQL($strSql);
        
        $result = $objNativeQuery->getSingleResult();

        return $result;
    }
    
    /**
     * Metodo que devuelve el array de la informacion con los enlaces de un servicio determinado
     * 
     * Costo de consulta : 39
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 19-09-2016
     * 
     * @author  Rafael Vera <rsvera@telconet.ec>
     * @version 1.1 15-06-2023 - Se agregó un filtro para mostrar los datos dependiendo a los estados enviados
     *  
     * @param  integer $intIdServicio , $strTipo ,$strEstadoEnlace
     * @return Array $arrayResult
     * @throws type
     */
    public function getArrayResultadoEnlacesServicio($intIdServicio,$strTipo,$strEstadoEnlace = "")
    {                        
        $objResultSetMap    = new ResultSetMappingBuilder($this->_em);
        $objNativeQuery     = $this->_em->createNativeQuery(null, $objResultSetMap);
                
        switch($strTipo)
        {           
            case 'cont':                
                $strSelect = "SELECT COUNT(*) CONT ";
                $objResultSetMap->addScalarResult('CONT','cont','integer');
                break;
            case 'data':                
                $strSelect = "SELECT 
                                O.idElemento_IN    ID_ELEMENTO_INICIAL,
                                O.elemento_IN      ELEMENTO_INICIAL,
                                o.NOMBRE_INTERFACE_INICIAL,
                                o.ESTADO_INTERFACE_INICIAL,
                                O.idElemento_OUT   ID_ELEMENTO_FINAL,
                                O.elemento_OUT     ELEMENTO_FINAL,
                                o.NOMBRE_INTERFACE_FINAL,
                                o.ESTADO_INTERFACE_FINAL,
                                O.INTERFACE_OUT_TRANS  ID_INTERFACE_INICIO,
                                O.INTERFACE_IN_CPE     ID_INTERFACE_FIN,
                                O.ESTADO               ESTADO_ENLACE,
                                O.usr_creacion         USR_CREACION_ENLACE,
                                Tf.nombre_tipo_elemento
                                ||' -> '
                                ||TI.nombre_tipo_elemento TIPO,
                                o.mac_interface_elemento  MAC ";
                
                $objResultSetMap->addScalarResult('ID_ELEMENTO_INICIAL','idElementoInicial','integer');
                $objResultSetMap->addScalarResult('ELEMENTO_INICIAL','nombreElementoInicial','string');
                $objResultSetMap->addScalarResult('NOMBRE_INTERFACE_INICIAL','nombreInterfaceInicial','string');
                $objResultSetMap->addScalarResult('ESTADO_INTERFACE_INICIAL','estadoInterfaceInicial','string');
                $objResultSetMap->addScalarResult('ID_ELEMENTO_FINAL','idElementoFinal','integer');
                $objResultSetMap->addScalarResult('ELEMENTO_FINAL','nombreElementoFinal','string');
                $objResultSetMap->addScalarResult('NOMBRE_INTERFACE_FINAL','nombreInterfaceFinal','string');
                $objResultSetMap->addScalarResult('ESTADO_INTERFACE_FINAL','estadoInterfaceFinal','string');
                $objResultSetMap->addScalarResult('ID_INTERFACE_INICIO','idInterfaceInicial','integer');
                $objResultSetMap->addScalarResult('ID_INTERFACE_FIN','idInterfaceFinal','integer');
                $objResultSetMap->addScalarResult('ESTADO_ENLACE','estadoEnlace','string');
                $objResultSetMap->addScalarResult('TIPO','tipoElemento','string');
                $objResultSetMap->addScalarResult('MAC','mac','string');
                $objResultSetMap->addScalarResult('USR_CREACION_ENLACE','usrCreacionEnlace','string'); 
                break;
        }
                      

        $strSql = " 
                  FROM
                    DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO MI,
                    DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO TI,
                    DB_INFRAESTRUCTURA.INFO_ELEMENTO EI,
                    DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO MF,
                    DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO TF,
                    DB_INFRAESTRUCTURA.INFO_ELEMENTO EF,
                    (select    
                        x.interface_elemento_ini_id                AS INTERFACE_OUT_TRANS,
                        x.interface_elemento_fin_id                AS INTERFACE_IN_CPE,     
                        (select ie.nombre_elemento from 
                                DB_INFRAESTRUCTURA.info_elemento ie where ie.id_elemento=iieIN.Elemento_Id) as elemento_IN, 
                        (select ie.id_elemento from 
                                DB_INFRAESTRUCTURA.info_elemento ie where ie.id_elemento=iieIN.Elemento_Id) as idElemento_IN,       
                        iieIN.nombre_interface_elemento nombre_interface_inicial,
                        iieIN.estado estado_interface_inicial,
                        (select ie.nombre_elemento from 
                                DB_INFRAESTRUCTURA.info_elemento ie where ie.id_elemento=iieOUT.Elemento_Id) as elemento_OUT, 
                        (select ie.id_elemento from 
                                DB_INFRAESTRUCTURA.info_elemento ie where ie.id_elemento=iieOUT.Elemento_Id) as idElemento_OUT,
                        iieOUT.nombre_interface_elemento nombre_interface_final,
                        iieOUT.estado estado_interface_final,
                        iieOUT.mac_interface_elemento,
                        X.ESTADO,
                        X.USR_CREACION
                      from DB_INFRAESTRUCTURA.info_enlace x, 
                           DB_INFRAESTRUCTURA.info_interface_elemento iieIN, 
                           DB_INFRAESTRUCTURA.info_interface_elemento iieOUT
                      where 
                            iieIN.Id_Interface_Elemento    = x.interface_elemento_ini_id 
                        and iieOUT.Id_Interface_Elemento   = x.interface_elemento_fin_id    
                    start with x.interface_elemento_ini_id = (select interface_elemento_id from 
                                                             info_servicio_tecnico where servicio_id = :servicio)
                    connect by x.interface_elemento_ini_id = prior x.interface_elemento_fin_id
                    ) O
                    WHERE
                     O.idElemento_OUT    = EI.ID_ELEMENTO
                     AND EI.MODELO_ELEMENTO_ID = MI.ID_MODELO_ELEMENTO
                     AND MI.TIPO_ELEMENTO_ID   = TI.ID_TIPO_ELEMENTO
                     AND O.idElemento_IN       = EF.ID_ELEMENTO
                     AND EF.MODELO_ELEMENTO_ID = MF.ID_MODELO_ELEMENTO
                     AND MF.TIPO_ELEMENTO_ID   = TF.ID_TIPO_ELEMENTO";

        
        $objNativeQuery->setParameter("servicio", $intIdServicio);

        $strEstadoSql = "";

        if ($strEstadoEnlace !== "" && $strEstadoEnlace !== "Todos")
        {
            $strEstadoSql =" AND o.ESTADO = :estadoEnlaces";

            $objNativeQuery->setParameter("estadoEnlaces", $strEstadoEnlace);
        }
        

        $objNativeQuery->setSQL($strSelect . $strSql. $strEstadoSql);              
        
        return $objNativeQuery->getArrayResult();
    }
    
    /**
     * 
     * Metodo que devuelve el json de la informacion con los enlaces de un servicio determinado
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 19-09-2016
     * 
     * @author  Rafael Vera <rsvera@telconet.ec>
     * @version 1.1 15-06-2023 - Se agregó $strEstadoEnlace para filtrar los datos dependiendo a los estados enviados
     * 
     * @param integer $intServicio
     * @return json 
     * @throws type
     */
    public function getJsonResultadoEnlacesServicio($intServicio,$strEstadoEnlace = "")
    {       
        try
        {
            $arrayResultadoData  = $this->getArrayResultadoEnlacesServicio($intServicio,'data',$strEstadoEnlace);
            $arrayResultadoCont  = $this->getArrayResultadoEnlacesServicio($intServicio,'cont',$strEstadoEnlace);
                        
            $intTotal            = $arrayResultadoCont[0]['cont'];          
           
            $intCont = 0;
            
            foreach($arrayResultadoData as $registros)
            {
                $strTagInicial = "<p>";
                $strTagFinal   = "</p>";
                
                $strElementoInicial = $registros['nombreElementoInicial'];
                $strElementoFinal   = $registros['nombreElementoFinal'];
                $strMac             = $registros['mac'];
                
                if($intCont == 0)
                {
                    $strElementoInicial = '<b style="color:#0B610B;">'.$registros['nombreElementoInicial'].'</b>';
                }
                
                if($intCont == $intTotal-1)
                {
                    $strElementoFinal   = '<b style="color:#0B610B;">'.$registros['nombreElementoFinal'].'</b>';
                    $strMac             = '<b>'.$registros['mac'].'</b>';
                }
                
                if($registros['estadoEnlace'] == 'Eliminado')
                {
                    $strTagInicial = '<p style="color:#8A0808;">';
                }
                
                $arrayRegistros[] = array(
                                        'idElementoInicial'     =>   $strTagInicial.$registros['idElementoInicial'].$strTagFinal,
                                        'nombreElementoInicial' =>   $strTagInicial.$strElementoInicial.$strTagFinal,
                                        'nombreInterfaceInicial'=>   $strTagInicial.$registros['nombreInterfaceInicial'].$strTagFinal,
                                        'estadoInterfaceInicial'=>   $strTagInicial.$registros['estadoInterfaceInicial'].$strTagFinal,
                                        'idElementoFinal'       =>   $strTagInicial.$registros['idElementoFinal'].$strTagFinal,
                                        'nombreElementoFinal'   =>   $strTagInicial.$strElementoFinal.$strTagFinal,
                                        'nombreInterfaceFinal'  =>   $strTagInicial.$registros['nombreInterfaceFinal'].$strTagFinal,
                                        'estadoInterfaceFinal'  =>   $strTagInicial.$registros['estadoInterfaceFinal'].$strTagFinal,
                                        'idInterfaceInicial'    =>   $strTagInicial.$registros['idInterfaceInicial'].$strTagFinal,
                                        'idInterfaceFinal'      =>   $strTagInicial.$registros['idInterfaceFinal'].$strTagFinal,
                                        'estadoEnlace'          =>   $strTagInicial.$registros['estadoEnlace'].$strTagFinal,
                                        'tipoElemento'          =>   $strTagInicial.$registros['tipoElemento'].$strTagFinal,
                                        'mac'                   =>   $strTagInicial.$strMac.$strTagFinal,
                                        'usrCreacionEnlace'     =>   $strTagInicial.$registros['usrCreacionEnlace'].$strTagFinal
                                       );
                
                $intCont ++;
            }
            
            $arrayResultado = array('total' => $intTotal , 'encontrados' => $arrayRegistros);
        } 
        catch (\Exception $ex) 
        {
            throw ($ex);            
        }
        
        return json_encode($arrayResultado);
    }
    
    /**
     * getServiciosPorInterfaceElementoTrad
     *
     * Método que obtiene la mac de un cpe que esta realizando un cambio de tipo medio.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0
     * @since 27-09-2021
     *
     * @param array $arrayParametros
     * @return type
     */
    public function getServiciosPorInterfaceElementoTrad($arrayParametros)
    {

        $arrayServicios = array();
        $intIdServicio  = $arrayParametros['intIdServicio'];
        
        $objRsm    = new ResultSetMappingBuilder($this->_em);
        $objQuery  = $this->_em->createNativeQuery(null, $objRsm);

        $strSql = " SELECT INT.NOMBRE_INTERFACE_ELEMENTO NOMBRE,
                    INT.ESTADO ESTADO, INT.MAC_INTERFACE_ELEMENTO MAC
                    FROM DB_INFRAESTRUCTURA.INFO_DETALLE_INTERFACE DET
                        ,DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO INT
                    WHERE DET.detalle_valor = :valor
                        AND INT.ID_INTERFACE_ELEMENTO = det.INTERFACE_ELEMENTO_ID
                        AND INT.ESTADO = :estado";

        $objRsm->addScalarResult(strtoupper('NOMBRE'), 'nombre', 'string');
        $objRsm->addScalarResult(strtoupper('ESTADO'), 'estado', 'string');
        $objRsm->addScalarResult(strtoupper('MAC'), 'mac', 'string');

        $objQuery->setParameter("valor", $intIdServicio);
        $objQuery->setParameter("estado", 'connected');
           
        $objQuery->setSQL($strSql);

        $objServicios = $objQuery->getResult();
                       
        if($objServicios)
        {
            foreach($objServicios as $objServicio)
            {
                    $arrayServicios[] = array(  'nombre' => $objServicio['nombre'],
                                                'estado' => $objServicio['estado'],
                                                'mac'    => $objServicio['mac']);
            }
        }
        return $arrayServicios;
    }

    /**
     * Funcion que sirve para generar un json con los enlaces de nodo a nodo segun busqueda
     * 
     * @author Anthony Santillan <asantillany@telconet.ec>
     * @version 1.0 15-02-2023 Version Inicial
     */
    public function generarJsonEnlacesBackbone($strNombreElementoA, $strNombreElementoB,$intInterfaceA, $intInterfaceB, $strTipoMedio,
                                               $strTipoEnlace, $strEstado,$intIdEmpresa,$strStart,$strLimit, 
                                               $emInfraestructura, $emComercial)
    {
        
        $arrayEncontrados = array();
        if($strNombreElementoA != '' && $strNombreElementoB != '')
        {
            $objEncontrados = $this->getEnlacesBackbone($strNombreElementoA, $strNombreElementoB, $emInfraestructura);
            return $objEncontrados;
        }
        else
        {
            $objEncontrados = $this->getEnlaces($strNombreElementoA, $strNombreElementoB,$intInterfaceA,$intInterfaceB,$strTipoMedio,
                                            $strTipoEnlace,$strEstado,$intIdEmpresa,$strStart,$strLimit);
        }

        if ($objEncontrados["total"] > 0) 
        {

            $intNum = $objEncontrados["total"];
            
            if($strNombreElementoA != '' || $intInterfaceA != '')
            {
                foreach ($objEncontrados["registros"] as $entidad)
                {
            
                    // nueeva implementacion

                    $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                ->findOneBy( array('id' => $entidad["idEnlace"], 
                               'estado' => 'Activo'));

                    $objInfoEnlaceServicio =  $emInfraestructura->getRepository("schemaBundle:InfoEnlaceServicioBackbone")
                    ->findOneBy(array('enlaceId' => $objEnlace->getId()));
                    
                    if(is_object($objInfoEnlaceServicio))
                    {
                        $strTipoRutaPrincipal = $objInfoEnlaceServicio->getTipoRuta();
                    }
                    
                    $intOrden = 1;
                    $arrayRespuestas =array();

                    while($objEnlace !== null)
                    {
                        $objInterfaceInicio   = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                        ->find($objEnlace->getInterfaceElementoIniId());
                        $intElementoInicio    = $objInterfaceInicio->getElementoId()->getId();
            
                        $objElementoInicio = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                            ->findOneBy( array('id' => $intElementoInicio, 'estado' => 'Activo') );
                        if(is_object($objElementoInicio))
                        {
                            $strNombreElementoInicio = $objElementoInicio->getNombreElemento();
                        }
            
                        $objInterfaceFin     = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                        ->find($objEnlace->getInterfaceElementoFinId());
            
                        $intElementoFin   = $objInterfaceFin->getElementoId()->getId();
            
                        $objElementoFinal = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                            ->findOneBy( array('id' => $intElementoFin, 'estado' => 'Activo') );
            
                        if(is_object($objElementoFinal))
                        {                        
                            $strNombreElementoFinal = $objElementoFinal->getNombreElemento();
                        }
            
                        $strHilo   = '';
                        $strColorHilo = '';
                        $strBufferColor = '';
                        $strLogin = 'LIBRE';
            
                        if($objEnlace->getBufferHiloId())
                        {
                            $objBufferHilo = $emInfraestructura->getRepository("schemaBundle:InfoBufferHilo")
                            ->find($objEnlace->getBufferHiloId()->getId());
            
                            if($objBufferHilo)
                            {
                                $strBufferColor    = $objBufferHilo->getBufferId()->getColorBuffer();

                                $objHilo = $emInfraestructura->getRepository("schemaBundle:AdmiHilo")
                                ->find($objBufferHilo->getHiloId()->getId());
                                if($objHilo)
                                {
                                    $intHiloId = $objHilo->getId();
                                    $strHilo = $objHilo->getNumeroHilo();
                                    $strColorHilo = $objHilo->getColorHilo();
                                }
                            }
                        }

                        $objClaseTipoMedio = $emInfraestructura->getRepository("schemaBundle:AdmiClaseTipoMedio")
                                                ->find($objEnlace->getTipoMedioId());
                        if(is_object($objClaseTipoMedio))
                        {
                            $strClaseTipoMedio = $objClaseTipoMedio->getNombreClaseTipoMedio();
                        }
                        $objInfoEnlaceServicio =  $emInfraestructura->getRepository("schemaBundle:InfoEnlaceServicioBackbone")
                                                    ->findOneBy(array('enlaceId' => $objEnlace->getId()));
                        if(is_object($objInfoEnlaceServicio))
                        {
                            $strLogin = $objInfoEnlaceServicio->getLoginAux();
                        }
                                    
                        $arrayRespuesta =array(
                                                'orden'             => $intOrden,
                                                'idEnlace'          => $objEnlace->getId(),
                                                'elementoInicioId'  => $intElementoInicio,
                                                'elementoFinId'     => $intElementoFin,
                                                'elementoInicio'    => $strNombreElementoInicio,
                                                'elementoFin'       => $strNombreElementoFinal,
                                                'interfaceInicioId' => $objInterfaceInicio->getId(),
                                                'interfaceFinId'    => $objInterfaceFin->getId(),
                                                'interfaceInicio'   => $objInterfaceInicio->getNombreInterfaceElemento(),
                                                'interfaceFin'      => $objInterfaceFin->getNombreInterfaceElemento(),
                                                'hilo'              => $strHilo,
                                                'color'             => $strColorHilo,
                                                'buffer'            => $strBufferColor,
                                                'login'             => $strLogin,
                                                'idClaseTipoMedio'  => $objClaseTipoMedio->getId(),
                                                'claseTipoMedio'    => $strClaseTipoMedio,
                                                'opciones'          => array('inicio' => $objEnlace->getId(),
                                                                             'itrFin' => $objInterfaceFin->getId())
                                                );
            
                        array_push($arrayRespuestas,$arrayRespuesta);
                        
                        $objEnlaces = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                        ->findBy( array('interfaceElementoIniId' => $objEnlace->getInterfaceElementoFinId(),
                        'estado' => 'Activo'));

                        if(empty($objEnlaces))
                        {
                            break;
                        }
                        
                        foreach($objEnlaces as $objEnlaceServicio)
                        {
                            $objInfoEnlaceServicio =  $emInfraestructura->getRepository("schemaBundle:InfoEnlaceServicioBackbone")
                            ->findOneBy(array('enlaceId' => $objEnlaceServicio->getId()));
                            
                            if(is_object($objInfoEnlaceServicio))
                            {
                                $strTipoRuta = $objInfoEnlaceServicio->getTipoRuta();
                            }
                            if($strTipoRuta == $strTipoRutaPrincipal)
                            {
                                $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                ->find($objEnlaceServicio->getId() );
                            }  
                        }
                        
                        $intOrden++;
                    }
                    
                    $objElementoInicio = $arrayRespuestas[0];
                    $objElementoFinal  = $arrayRespuestas[count($arrayRespuestas)-1];

                    $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                ->findOneBy( array('id' => $entidad["idEnlace"], 
                                'estado' => 'Activo'));
                    
                    $objElementoFinal['opciones']['inicio'] = $objEnlace->getId();
                    $arrayEncontrados[]=array('idEnlace'                 =>$objEnlace->getId(),
                                            'interfaceElementoIniId'   => $objElementoInicio['interfaceInicioId'],
                                            'interfaceElementoIni'     => $objElementoInicio['interfaceInicio'],
                                            'elementoIniNombre'        => $objElementoInicio['elementoInicio'],
                                            'interfaceElementoFinId'   => $objElementoFinal['interfaceFinId'],
                                            'interfaceElementoFin'     => $objElementoFinal['interfaceFin'],
                                            'elementoFinNombre'        => $objElementoFinal['elementoFin'],
                                            'hiloColor'                => $objElementoInicio['color'],
                                            'hiloNumero'               => $objElementoInicio['hilo'],
                                            'buffer'                   => $objElementoInicio['buffer'],
                                            'estado'                   => $entidad["estado"],
                                            'login'                    => $objElementoInicio['login'],
                                            'idClaseTipoMedio'         => $objElementoInicio['idClaseTipoMedio'],
                                            'claseTipoMedio'           => $objElementoInicio['claseTipoMedio'],
                                            'idEnlaceIni'              => $entidad["idEnlace"],
                                            'opciones'                 => $objElementoFinal['opciones'],
                                            'action1' => 'button-grid-show',
                                            'action2' => (trim($entidad["estado"])=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                            'action3' => (trim($entidad["estado"])=='Eliminado' ? 'button-grid-invisible':'button-grid-delete')
                                            
                        );
                }
            }
            if($strNombreElementoB != '' || $intInterfaceB != '')
            {
                foreach ($objEncontrados["registros"] as $entidad)
                {
            
                    // nueeva implementacion

                    $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                    ->findOneBy( array('id' => $entidad["idEnlace"], 
                    'estado' => 'Activo'));

                    $objInfoEnlaceServicio =  $emInfraestructura->getRepository("schemaBundle:InfoEnlaceServicioBackbone")
                    ->findOneBy(array('enlaceId' => $objEnlace->getId()));
                    
                    if(is_object($objInfoEnlaceServicio))
                    {
                        $strTipoRutaPrincipal = $objInfoEnlaceServicio->getTipoRuta();
                    }
                    
                    $intOrden = 1;
                    $arrayRespuestas =array();

                    while($objEnlace !== null)
                    {
                        $objInterfaceInicio   = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                        ->find($objEnlace->getInterfaceElementoIniId());
                        $intElementoInicio    = $objInterfaceInicio->getElementoId()->getId();
            
                        $objElementoInicio = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                            ->findOneBy( array('id' => $intElementoInicio, 'estado' => 'Activo') );
                        if(is_object($objElementoInicio))
                        {
                            $strNombreElementoInicio = $objElementoInicio->getNombreElemento();
                        }
            
                        $objInterfaceFin     = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                        ->find($objEnlace->getInterfaceElementoFinId());
            
                        $intElementoFin   = $objInterfaceFin->getElementoId()->getId();
            
                        $objElementoFinal = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                            ->findOneBy( array('id' => $intElementoFin, 'estado' => 'Activo') );
            
                        if(is_object($objElementoFinal))
                        {                        
                            $strNombreElementoFinal = $objElementoFinal->getNombreElemento();
                        }
            
                        $strHilo   = '';
                        $strColorHilo = '';
                        $strBufferColor = '';
                        $strLogin = 'LIBRE';
            
                        if($objEnlace->getBufferHiloId())
                        {
                            $objBufferHilo = $emInfraestructura->getRepository("schemaBundle:InfoBufferHilo")
                            ->find($objEnlace->getBufferHiloId()->getId());
            
                            if($objBufferHilo)
                            {
                                $strBufferColor    = $objBufferHilo->getBufferId()->getColorBuffer();
                                
                                $objHilo = $emInfraestructura->getRepository("schemaBundle:AdmiHilo")
                                ->find($objBufferHilo->getHiloId()->getId());
                                if($objHilo)
                                {
                                    $intHiloId = $objHilo->getId();
                                    $strHilo = $objHilo->getNumeroHilo();
                                    $strColorHilo = $objHilo->getColorHilo();
                                }
                            }
                        }
                        
                        $objClaseTipoMedio = $emInfraestructura->getRepository("schemaBundle:AdmiClaseTipoMedio")->find($objEnlace->getTipoMedioId());
                        if($objClaseTipoMedio)
                        {
                            $strClaseTipoMedio = $objClaseTipoMedio->getNombreClaseTipoMedio();
                        }

                        $objInfoEnlaceServicio =  $emInfraestructura->getRepository("schemaBundle:InfoEnlaceServicioBackbone")
                                                    ->findOneBy(array('enlaceId' => $objEnlace->getId()));
                        if($objInfoEnlaceServicio)
                        {
                            $strLogin = $objInfoEnlaceServicio->getLoginAux();
                        }

                        $arrayRespuesta =array(
                                                'orden'             => $intOrden,
                                                'idEnlace'          => $objEnlace->getId(),
                                                'elementoInicioId'  => $intElementoInicio,
                                                'elementoFinId'     => $intElementoFin,
                                                'elementoInicio'    => $strNombreElementoInicio,
                                                'elementoFin'       => $strNombreElementoFinal,
                                                'interfaceInicioId' => $objInterfaceInicio->getId(),
                                                'interfaceFinId'    => $objInterfaceFin->getId(),
                                                'interfaceInicio'   => $objInterfaceInicio->getNombreInterfaceElemento(),
                                                'interfaceFin'      => $objInterfaceFin->getNombreInterfaceElemento(),
                                                'hilo'              => $strHilo,
                                                'color'             => $strColorHilo,
                                                'buffer'            => $strBufferColor,
                                                'login'             => $strLogin,
                                                'idClaseTipoMedio'  => $objClaseTipoMedio->getId(),
                                                'claseTipoMedio'    => $strClaseTipoMedio,
                                                'opciones'          => array('fin' => $objEnlace->getId(),
                                                                             'itrFin' => $objInterfaceFin->getId())
                                                );
            
                        array_push($arrayRespuestas,$arrayRespuesta);
                         
                        $objEnlaces = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                        ->findBy( array('interfaceElementoFinId' => $objEnlace->getInterfaceElementoIniId(),
                        'estado' => 'Activo'));

                        if(empty($objEnlaces))
                        {
                            break;
                        }
                        
                        foreach($objEnlaces as $objEnlaceServicio)
                        {
                            $objInfoEnlaceServicio =  $emInfraestructura->getRepository("schemaBundle:InfoEnlaceServicioBackbone")
                            ->findOneBy(array('enlaceId' => $objEnlaceServicio->getId()));
                            
                            if(is_object($objInfoEnlaceServicio))
                            {
                                $strTipoRuta = $objInfoEnlaceServicio->getTipoRuta();
                            }
                            if($strTipoRuta == $strTipoRutaPrincipal)
                            {
                                $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                ->find($objEnlaceServicio->getId() );
                            }  
                        }
                        $intOrden++;
                    }
                    
                    $objElementoInicio = $arrayRespuestas[0];
                    $objElementoFinal  = $arrayRespuestas[count($arrayRespuestas)-1];

                    $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                    ->findOneBy( array('id' => $entidad["idEnlace"], 
                    'estado' => 'Activo'));

                    $objElementoFinal['opciones']['inicio'] = $objEnlace->getId();
                                
                    $arrayEncontrados[]=array('idEnlace'                => $objEnlace->getId(),
                                            'interfaceElementoIniId'  => $objElementoFinal['interfaceIniId'],
                                            'interfaceElementoIni'     => $objElementoFinal['interfaceInicio'],
                                            'elementoIniNombre'        => $objElementoFinal['elementoInicio'],
                                            'interfaceElementoFinId'   => $objElementoInicio['interfaceFinId'],
                                            'interfaceElementoFin'     => $objElementoInicio['interfaceFin'],
                                            'elementoFinNombre'        => $objElementoInicio['elementoFin'],
                                            'hiloColor'                => $objElementoInicio['color'],
                                            'hiloNumero'               => $objElementoInicio['hilo'],
                                            'buffer'                   => $objElementoInicio['buffer'],
                                            'estado'                   => $entidad["estado"],
                                            'login'                    => $objElementoInicio['login'],
                                            'idClaseTipoMedio'         => $objElementoInicio['idClaseTipoMedio'],
                                            'claseTipoMedio'           => $objElementoInicio['claseTipoMedio'],
                                            'idEnlaceFin'              => $entidad["idEnlace"],
                                            'opciones'                 => $objElementoInicio['opciones'],
                                            'action1' => 'button-grid-show',
                                            'action2' => (trim($entidad["estado"])=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                            'action3' => (trim($entidad["estado"])=='Eliminado' ? 'button-grid-invisible':'button-grid-delete')
                                            
                        );
                }
            }
            
            $objData=json_encode($arrayEncontrados);
            $objResultado= '{"total":"'.$intNum.'","data":'.$objData.'}';

            return $objResultado;
        }
        else
        {
            $objResultado= '{"total":"0","data":[]}';

            return $objResultado;
        }
    }

    /**
     * Funcion que sirve para generar un json con los enlaces de elemento a elemento segun busqueda
     * 
     * @author Anthony Santillan <asantillany@telconet.ec>
     * @version 1.0 15-02-2023 Version Inicials
     */
    public function getEnlacesBackbone($strNombreElementoA,$strNombreElementoB, $emInfraestructura)
    {
        $arrayEncontrados = array();
        $arrayRespuestas = array();
        $arrayRespuesta  = array();
        // elemento inicio
        $objElementoIni   = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                               ->findOneBy( array('nombreElemento' => $strNombreElementoA, 'estado' => 'Activo') );
                             
        $objJsonIni = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                         ->findBy( array('elementoId' => $objElementoIni->getId()) );

        // elemento fin
        $objElementoFin   = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                               ->findOneBy( array('nombreElemento' => $strNombreElementoB, 'estado' => 'Activo') );

        $objJsonFin =  $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                            ->findBy( array('elementoId' => $objElementoFin->getId()) );
        $intOrden = 0;
        foreach($objJsonIni as $InterfaceInicio)
        {
           
            $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                     ->findOneBy( array('interfaceElementoIniId' => $InterfaceInicio->getId()));
            
            if(is_object($objEnlace))
            {
                $intInterfaceIni = $objEnlace->getInterfaceElementoFinId()->getId();
                
                $intInterfaceFin = 0;
                $objInterfaceFin = $objJsonFin[$intOrden];

                while($intInterfaceIni != $objInterfaceFin->getId())
                {
                   

                    $objEnlaceInterno = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                     ->findOneBy( array('interfaceElementoIniId' => $intInterfaceIni));
                    
                    if(!is_object($objEnlaceInterno))
                    {
                        break;
                    }
                    $intInterfaceIni = $objEnlaceInterno->getInterfaceElementoFinId()->getId();
                }
                if($intInterfaceIni == $objInterfaceFin->getId())
                {
                    $objInterfaceIni = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                            ->find($InterfaceInicio->getId());

                    $objNodoInicio   = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                            ->find($objInterfaceIni->getElementoId());

                    $objInterfaceFin = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                            ->find($objInterfaceFin->getId());

                    $objNodoFin      = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                            ->find($objInterfaceFin->getElementoId());

                    $strHilo      = '';
                    $strColorHilo = '';
                    $strLogin = 'LIBRE';
                    $strBufferColor = '';
        
                    if($objEnlace->getBufferHiloId())
                    {
                        $objBufferHilo = $emInfraestructura->getRepository("schemaBundle:InfoBufferHilo")
                        ->find($objEnlace->getBufferHiloId()->getId());
        
                        if($objBufferHilo)
                        {
                            $strBufferColor    = $objBufferHilo->getBufferId()->getColorBuffer();

                            $objHilo = $emInfraestructura->getRepository("schemaBundle:AdmiHilo")
                            ->find($objBufferHilo->getHiloId()->getId());
                            if($objHilo)
                            {
                                $intHiloId = $objHilo->getId();
                                $strHilo = $objHilo->getNumeroHilo();
                                $strColorHilo = $objHilo->getColorHilo();
                            }
                        }
                    }

                    $objClaseTipoMedio = $emInfraestructura->getRepository("schemaBundle:AdmiClaseTipoMedio")->find($objEnlace->getTipoMedioId());
                    if($objClaseTipoMedio)
                    {
                        $strClaseTipoMedio = $objClaseTipoMedio->getTipoMedioId();
                    }

                    $objInfoEnlaceServicio =  $emInfraestructura->getRepository("schemaBundle:InfoEnlaceServicioBackbone")
                                                ->findOneBy(array('enlaceId' => $objEnlace->getId()));

                    if(is_object($objInfoEnlaceServicio))
                    {
                        $strLogin = $objInfoEnlaceServicio->getLoginAux();
                    }

                    $arrayRespuesta =array(
                        'idEnlace'                  => $objEnlace->getId(),

                        'elementoIniNombre'         => $objNodoInicio->getNombreElemento(),
                        'elementoFinNombre'         => $objNodoFin->getNombreElemento(),

                        'interfaceElementoIniId'    => $objInterfaceIni->getId(),
                        'interfaceElementoFinId'    => $objInterfaceFin->getId(),

                        'interfaceElementoIni'      => $objInterfaceIni->getNombreInterfaceElemento(),
                        'interfaceElementoFin'      => $objInterfaceFin->getNombreInterfaceElemento(),
                        
                        'hiloNumero'                => $strHilo,
                        'hiloColor'                 => $strColorHilo,
                        'login'                     => $strLogin,
                        'buffer'                    => $strBufferColor,
                        'idClaseTipoMedio'          => $objClaseTipoMedio->getId(),
                        'claseTipoMedio'            => $strClaseTipoMedio,
                        'opciones'                  => array('inicio' => $objEnlace->getId(),
                                                             'itrFin' => $objInterfaceFin->getId())
                        );

                    array_push($arrayRespuestas,$arrayRespuesta);
                }
            }
            $intOrden++;
            continue;
        }
        $objResponse = json_encode(array('total' => count($arrayRespuestas), 'data'  => $arrayRespuestas));          
        return $objResponse;
    }
}