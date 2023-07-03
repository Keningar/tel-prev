<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use telconet\schemaBundle\Entity\ReturnResponse;


class InfoInterfaceElementoRepository extends EntityRepository
{
        
    /**
     * Obtener servicios segun el interface del elemento
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 09-02-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec> 
     * @version 1.1 20-02-2018 Se agrega validación para considerar los servicios Internet Small Business
     *                         Costo = 11
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 22-05-2019 Se agrega validación para mostrar el login de los servicios Telcohome en la administración de puertos
     *                          Costo = 16
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.3 17-06-2019 Debido a la creación del producto SMALL BUSINESS CENTROS COMERCIALES el cual comparte el mismo nombre tecnico
     *                         se procede a modificar la consulta para que soporte dicha lógica.
     *                         Costo = 21
     * 
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 1.4 05-04-2023 Se modifica consulta mediante codigo empresa para obtener informacion de los splitter 
     *                          para Megadatos como para Ecuanet.
     *
     * @param type $idInterfaceElemento
     * @param type $codEmpresa
     * 
     * @return string $data
     **/
     public function getServiciosPorInterface($idInterfaceElemento, $codEmpresa)
    {

        $serviciosArray = array();
        $arrayEmpresas = array();
        if($codEmpresa == 18)
        {
            array_push($arrayEmpresas,$codEmpresa, 33);
        }
        elseif($codEmpresa == 33)
        {
            array_push($arrayEmpresas,$codEmpresa, 18);
        }
        else
        {
            array_push($arrayEmpresas,$codEmpresa);
        }
        if($idInterfaceElemento && $codEmpresa)
        {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $query = $this->_em->createNativeQuery(null, $rsm);

            $strSql = " SELECT DISTINCT S.ID_SERVICIO, P.LOGIN
                        FROM DB_COMERCIAL.INFO_SERVICIO_TECNICO ST
                        INNER JOIN DB_COMERCIAL.INFO_SERVICIO S
                        ON ST.SERVICIO_ID = S.ID_SERVICIO
                        INNER JOIN DB_COMERCIAL.INFO_PUNTO P
                        ON S.PUNTO_ID      =   P.ID_PUNTO 
                        LEFT JOIN DB_COMERCIAL.INFO_PLAN_DET PD 
                        ON S.PLAN_ID = PD.PLAN_ID
                        LEFT JOIN DB_COMERCIAL.ADMI_PRODUCTO PROD
                        ON S.PRODUCTO_ID = PROD.ID_PRODUCTO
                        WHERE ST.INTERFACE_ELEMENTO_CONECTOR_ID = :idInterfaceElemento 
                        AND(
                            PD.PRODUCTO_ID  IN ( SELECT ID_PRODUCTO
                                                FROM DB_COMERCIAL.ADMI_PRODUCTO
                                                WHERE NOMBRE_TECNICO = :nombreProducto
                                                AND EMPRESA_COD      IN (:idEmpresas)
                                                AND ESTADO           = :nombreEstado )
                            OR 
                            PROD.ID_PRODUCTO IN ( SELECT ID_PRODUCTO
                                                FROM DB_COMERCIAL.ADMI_PRODUCTO
                                                WHERE NOMBRE_TECNICO = :nombreProductoIsb
                                                AND EMPRESA_COD      = :idEmpresaTn
                                                AND ESTADO           = :nombreEstado )
                            OR 
                            PROD.ID_PRODUCTO = ( SELECT ID_PRODUCTO
                                                FROM DB_COMERCIAL.ADMI_PRODUCTO
                                                WHERE NOMBRE_TECNICO = :nombreProductoTelcoHome
                                                AND EMPRESA_COD      = :idEmpresaTn
                                                AND ESTADO           = :nombreEstado )
                            OR
                            PROD.ID_PRODUCTO IN ( SELECT ID_PRODUCTO
                                                FROM DB_COMERCIAL.ADMI_PRODUCTO
                                                WHERE NOMBRE_TECNICO IN (:nombresProductosGpon)
                                                AND EMPRESA_COD      = :idEmpresaTn
                                                AND ESTADO           = :nombreEstado )
                            )
                        AND S.ESTADO        IN (:nombreServicio)";

            $rsm->addScalarResult(strtoupper('ID_SERVICIO'), 'idServicio', 'integer');
            $rsm->addScalarResult(strtoupper('LOGIN'), 'login', 'string');

            $query->setParameter("idInterfaceElemento", $idInterfaceElemento);
            $query->setParameter("nombreProducto", 'INTERNET');
            $query->setParameter("nombreEstado", 'Activo');
            $query->setParameter("idEmpresas", $arrayEmpresas);
            $query->setParameter("nombreProductoIsb", 'INTERNET SMALL BUSINESS');
            $query->setParameter("nombreProductoTelcoHome", 'TELCOHOME');
            $query->setParameter("idEmpresaTn", "10");
            $query->setParameter("nombresProductosGpon", array('L3MPLS', 'INTMPLS', 'INTERNET','DATOS SAFECITY'));
            $query->setParameter("nombreServicio", array('Activo', 'Asignada', 'AsignadoTarea', 'Detenido', 'EnPruebas', 'EnVerificacion', 'Factible',
                                                         'In-Corte', 'In-Temp', 'Planificada', 'PreAsignacionInfoTecnica', 'PrePlanificada',
                                                         'Replanificada', 'PreFactible', 'PreFactibilidad','FactibilidadEnProceso'));

            $query->setSQL($strSql);

            $servicios = $query->getResult();
                        
            if($servicios)
            {
                foreach($servicios as $servicio)
                {
                        $serviciosArray[] = array(  'idServicio' => $servicio['idServicio'],
                                                    'login'     => $servicio['login']);
                }

            }

            return $serviciosArray;
        }
    }
    
     /**
     * obtener json logines de nodos wifi
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 23-09-2016
     * 
     * @param type $idNodoWifi
     * 
     * @return json $respuesta dentro del json devuelve el total de las capacidades en color rojo, en el caso que no esté asociado a un login
     *                        y esté a un elemento este se indicará en color azul
     **/
    public function getJsonLoginPorNodoWifi($idNodoWifi)
    {
        $arrayLogines = array();
        
        $arrayResultadoLogin = $this->getLoginPorNodoWifi($idNodoWifi);
        
        if($arrayResultadoLogin)
        {

            foreach($arrayResultadoLogin as $arrayRegistro)
            {
                
                //si el login viene null verifico si la interface está en la info enlace
                if (!$arrayRegistro['login'] && ($arrayRegistro['estadoInterface'] == 'connected') )
                {                    
                    $objEnlace = $this->_em->getRepository('schemaBundle:InfoEnlace')
                                           ->findOneBy(array('interfaceElementoFinId' => $arrayRegistro['idInterfaceElemento'],
                                                             'estado'                 => 'Activo'));
                    if(!$objEnlace)
                    {
                        $objEnlace = $this->_em->getRepository('schemaBundle:InfoEnlace')
                                               ->findOneBy(array('interfaceElementoIniId' => $arrayRegistro['idInterfaceElemento'],
                                                                 'estado'                 => 'Activo'));
                        if($objEnlace)
                        {
                            $objInterfaceElemento = $this->_em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                              ->find($objEnlace->getInterfaceElementoFinId());
                        }
                    }
                    else
                    {
                        $objInterfaceElemento = $this->_em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                          ->find($objEnlace->getInterfaceElementoIniId());
                    }                   
 
                    if($objInterfaceElemento)
                    {
                        $objElemento = $objInterfaceElemento->getElementoId();
                        if($objElemento)
                        {
                            $arrayRegistro['login'] = '<font color="BLUE"><b>'.$objElemento->getNombreElemento().'</b></font>';
                        }
                    }
                    
                }
                
                $arrayLogines[] = array(  'idServicio'                => $arrayRegistro['idServicio'],
                                          'nombreElemento'            => $arrayRegistro['nombreElemento'],
                                          'nombreInterfaceElemento'   => $arrayRegistro['nombreInterfaceElemento'],
                                          'estadoInterface'           => $arrayRegistro['estadoInterface'],
                                          'login'                     => $arrayRegistro['login'],
                                          'loginAux'                  => $arrayRegistro['loginAux'],
                                          'capacidad'                 => $arrayRegistro['capacidad'] );
                $intTotalCapacidad = $intTotalCapacidad + (int) $arrayRegistro['capacidad'];                
                
            }
            $arrayLogines[] = array( 'nombreElemento'   => '<font color="RED"><b>TOTAL</b></font>',
                                     'capacidad'        => '<font color="RED"><b>'.$intTotalCapacidad.'</b></font>' );
            
            $strRespuesta = '{"status":"OK","total":"' .$intTotalCapacidad. '","encontrados":' . json_encode($arrayLogines) . '}';
        }
        else
        {
            $strRespuesta = '{"status":"ERROR","total":"0","encontrados":"La consulta no obtuvo información."}';            
        }
        
        return $strRespuesta;
    }
    
    
    /**
    * Metodo que devuelve las interfaces y elementos de los servicios agrupados por interfaces, según el producto y el cliente
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0
    *
    * @param string $strProducto
    * @param string $strIdentificacionCliente
    *
    * @return arrayResultado
    */
    
    public function getPuertosPorProductoCliente($strProducto, $strIdentificacionCliente)
    {
        $arrayResultado = array();
        try
        {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $query = $this->_em->createNativeQuery(null, $rsm);
            $sql = "SELECT * FROM TEMP_NODO_WIFI";
            $rsm->addScalarResult(strtoupper('NOMBRE_INTERFACE_ELEMENTO'), 'nombreInterface', 'string');
            $rsm->addScalarResult(strtoupper('NOMBRE_ELEMENTO'), 'nombreElemento', 'string');
            $rsm->addScalarResult(strtoupper('ANILLO'), 'anillo', 'string');
            $rsm->addScalarResult(strtoupper('ANCHO_BANDA'), 'anchoBanda', 'string');

            $query->setSQL($sql);
            $arrayResultado = $query->getResult();
        }
        catch(\Exception $ex)
        {
            error_log($ex->getMessage());
        }
        return $arrayResultado;
    }

    /**
     * obtener logines de nodos wifi
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 23-09-2016
     * 
     * @param type $idNodoWifi
     * 
     * @return array $data
     **/
    public function getLoginPorNodoWifi($intIdNodoWifi)
    {

        $objRsm = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSql = "SELECT S.ID_SERVICIO,
                E.NOMBRE_ELEMENTO,
                IE.NOMBRE_INTERFACE_ELEMENTO ,
                IE.ESTADO,
                IE.ID_INTERFACE_ELEMENTO,
                S.LOGIN_AUX,
                (SELECT LOGIN FROM DB_COMERCIAL.INFO_PUNTO WHERE ID_PUNTO = S.PUNTO_ID) LOGIN,
                (SELECT VALOR
                FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT
                WHERE SERVICIO_ID               = S.ID_SERVICIO
                AND PRODUCTO_CARACTERISITICA_ID = (SELECT PC.ID_PRODUCTO_CARACTERISITICA
                                                  FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA PC,
                                                    DB_COMERCIAL.ADMI_PRODUCTO P,
                                                    DB_COMERCIAL.ADMI_CARACTERISTICA C
                                                  WHERE P.NOMBRE_TECNICO           = :producto
                                                  AND P.ESTADO                     = :estado
                                                  AND C.DESCRIPCION_CARACTERISTICA = :caracteristica
                                                  AND C.ESTADO                     = :estado
                                                  AND PC.PRODUCTO_ID               = P.ID_PRODUCTO
                                                  AND PC.CARACTERISTICA_ID         = C.ID_CARACTERISTICA
                                                  AND PC.ESTADO                    = :estado )
                AND ESTADO = :estado) CAPACIDAD
              FROM DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO IE,
                DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT SPC,
                DB_INFRAESTRUCTURA.INFO_ELEMENTO E ,
                DB_COMERCIAL.INFO_SERVICIO S
              WHERE SPC.VALOR (+)  = TO_CHAR(IE.ID_INTERFACE_ELEMENTO )
              AND S.ID_SERVICIO(+) = SPC.SERVICIO_ID
              AND E.ID_ELEMENTO    = IE.ELEMENTO_ID
              AND SPC.ESTADO (+)   = :estado
              AND IE.ESTADO       IN (:estadoInterface)
              AND IE.ELEMENTO_ID  IN  ( SELECT RE.ELEMENTO_ID_B
                                        FROM DB_INFRAESTRUCTURA.INFO_RELACION_ELEMENTO RE
                                        WHERE RE.ELEMENTO_ID_A = :idNodoWifi
                                        AND RE.ESTADO          = :estado )
              ORDER BY IE.ID_INTERFACE_ELEMENTO ASC ";

        $objRsm->addScalarResult(strtoupper('ID_SERVICIO'), 'idServicio', 'integer');
        $objRsm->addScalarResult(strtoupper('NOMBRE_ELEMENTO'), 'nombreElemento', 'string');
        $objRsm->addScalarResult(strtoupper('ID_INTERFACE_ELEMENTO'), 'idInterfaceElemento', 'integer');
        $objRsm->addScalarResult(strtoupper('NOMBRE_INTERFACE_ELEMENTO'), 'nombreInterfaceElemento', 'string');
        $objRsm->addScalarResult(strtoupper('ESTADO'), 'estadoInterface', 'string');
        $objRsm->addScalarResult(strtoupper('LOGIN'), 'login', 'string');
        $objRsm->addScalarResult(strtoupper('LOGIN_AUX'), 'loginAux', 'string');
        $objRsm->addScalarResult(strtoupper('CAPACIDAD'), 'capacidad', 'string');

        $objQuery->setParameter("idNodoWifi", $intIdNodoWifi);
        $objQuery->setParameter("estado", 'Activo');
        $objQuery->setParameter("estadoInterface", array('connected','not connect'));
        $objQuery->setParameter("producto", 'INTERNET WIFI');
        $objQuery->setParameter("caracteristica", 'CAPACIDAD1');

        $objQuery->setSQL($strSql);

        $arrayLogines = $objQuery->getResult();
        
        return $arrayLogines;
    }
        
    /**
     * Obtener login segun el interface del elemento que pertenezcan a TN
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 24-08-2016
     * 
     * @param array $arrayParams['idInterfaceElemento'] String: Id Interface Elemento
     *                          ['tipo']                String: Tipo de Elemento
     * 
     * @return array $arrayServicios usuario que tiene asignado el puerto del cassette
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 29-11-2016 se incluye filtro para que no presente servicios en estado Cancel,Cancelado, Eliminado, Anulado
     * 
     **/
     public function getLoginInterfaceElementoConector($arrayParams)
    {

        $arrayServicios = array();
        try
        {
            if($arrayParams['idInterfaceElemento'])
            {
                $query  = $this->_em->createQuery();

                if($arrayParams['tipo'] === 'CASSETTE')
                {
                    $sql = "SELECT P.login 
                        FROM schemaBundle:InfoServicioTecnico ST,
                          schemaBundle:InfoServicio S,                   
                          schemaBundle:InfoPunto P
                        WHERE ST.interfaceElementoConectorId = :idInterfaceElemento
                        AND ST.servicioId                    = S
                        AND S.estado not in (:estadoServicio)
                        AND S.puntoId                        = P                               
                        GROUP BY P.login";                

                    $query->setParameter("idInterfaceElemento", $arrayParams['idInterfaceElemento']);       
                    $query->setParameter("estadoServicio", array('Cancel','Cancelado', 'Eliminado', 'Anulado'));
                    
                    $query->setDQL($sql);

                    $servicios = $query->getResult();

                    if($servicios)
                    {
                        foreach($servicios as $servicio)
                        {
                            $arrayServicios[] = array('login'  => strtoupper($servicio['login']));
                        }
                    }
                }
                return $arrayServicios;
            }
        }
        catch (\Exception $ex)
        {
            throw $ex;
        }
    }
    
    /**
     * Obtener servicios segun el interface del elemento y el tipo de producto 
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 08-04-2016
     * 
     * @param integer $idInterface
     * @param string  $nombreProducto
     * @param integer $codEmpresa
     * 
     * @return Array $serviciosArray [ idServicio , login ]
     **/
     public function getServiciosPorInterfaceProducto($idInterface, $nombreProducto , $codEmpresa)
    {
        $serviciosArray = array();
        
        if($idInterface && $nombreProducto && $codEmpresa)
        {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $query = $this->_em->createNativeQuery(null, $rsm);

            $sql = "SELECT 
                        S.ID_SERVICIO,
                        P.LOGIN
                      FROM INFO_SERVICIO_TECNICO ST,
                        INFO_SERVICIO S,
                        INFO_PUNTO P,
                        ADMI_PRODUCTO PRODUCTO
                      WHERE ST.INTERFACE_ELEMENTO_CONECTOR_ID = :interface
                      AND ST.SERVICIO_ID                      = S.ID_SERVICIO
                      AND S.PUNTO_ID                          = P.ID_PUNTO
                      AND S.PRODUCTO_ID                       = PRODUCTO.ID_PRODUCTO
                      AND PRODUCTO.ID_PRODUCTO               IN
                        (SELECT ID_PRODUCTO
                        FROM ADMI_PRODUCTO
                        WHERE NOMBRE_TECNICO  = :producto
                        AND EMPRESA_COD       = :empresa
                        AND ESTADO            = :estado
                        )
                      AND S.ESTADO IN (:estados)";

            $rsm->addScalarResult(strtoupper('ID_SERVICIO'), 'idServicio', 'integer');
            $rsm->addScalarResult(strtoupper('LOGIN'), 'login', 'string');

            $query->setParameter("interface", $idInterface);
            $query->setParameter("producto", $nombreProducto);
            $query->setParameter("estado", 'Activo');
            $query->setParameter("empresa", $codEmpresa);
            $query->setParameter("estados", array('Activo', 'Asignada', 'AsignadoTarea', 'Detenido', 'EnPruebas', 'EnVerificacion', 'Factible',
                                                         'In-Corte', 'In-Temp', 'Planificada', 'PreAsignacionInfoTecnica', 'PrePlanificada',
                                                         'Replanificada', 'PreFactible', 'PreFactibilidad','FactibilidadEnProceso'));
            $query->setSQL($sql);

            $servicios = $query->getResult();
                        
            if($servicios)
            {
                foreach($servicios as $servicio)
                {
                        $serviciosArray[] = array(  'idServicio' => $servicio['idServicio'],
                                                    'login'      => $servicio['login']);
                }

            }

            return $serviciosArray;
        }
    }
     /**
     * Funcion que sirve para obtener la trazabilidad de interfaces del elemento,
     * Recibe la interface del elemento y el tipo de elemento hasta el cual se desea escalar
     * 
     * @author Creado: John Vera <javera@telconet.ec>
     * @version 1.0 09-04-2015
     */    
    
    public function getTrazabilidadInterfaceElemento($interfaceElementoId, $tipoElementoPadre)
    {   
        $ruta = str_pad($ruta, 5000, " ");

        $sql = "BEGIN  :ruta := INFRK_TRANSACCIONES.INFRF_GET_TRAZA_ELEMENTOS( :interfaceElementoSplitterId , :tipoElementoPadre); END; ";
        
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->bindParam('ruta', $ruta);
        $stmt->bindParam(':interfaceElementoSplitterId', $interfaceElementoId);
        $stmt->bindParam(':tipoElementoPadre', $tipoElementoPadre);

        $stmt->execute();
        
        return $ruta;
    }
    
     /**
     * getInterfaceElementoPadre
     * Funcion que sirve para obtener el id interface del elemento padre de un elemento
     * 
     * @return $result 
     * 
     * @author Creado: John Vera <javera@telconet.ec>
     * @version 1.0 18-04-2016
     */    
    
    public function getInterfaceElementoPadre($idParametro, $tipoParametro, $tipoElementoPadre)
    {   
        $result = str_pad($result, 5000, " ");

        $sql = "BEGIN  :result := DB_INFRAESTRUCTURA.GET_ELEMENTO_PADRE( :idParametro , :tipoParametro, :tipoElementoPadre); END; ";
        
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->bindParam('result', $result);
        $stmt->bindParam(':idParametro', $idParametro);
        $stmt->bindParam(':tipoParametro', $tipoParametro);
        $stmt->bindParam(':tipoElementoPadre', $tipoElementoPadre);

        $stmt->execute();
        
        return $result;
    }    
    
    public function getElementosByInterfaceElemento($interfaceElementoId)
    {   
        $query = "	SELECT 
						e1.id as idElemento, e1.nombreElemento as nombreElemento, 
						e2.id as idElementoPadre, e2.nombreElemento as nombreElementoPadre 
					FROM 
						schemaBundle:InfoInterfaceElemento ie, 
						schemaBundle:InfoElemento e1,
						schemaBundle:InfoRelacionElemento re, 
						schemaBundle:InfoElemento e2
						
					WHERE 
						ie.elementoId = e1.id 
						AND re.elementoIdB = e1.id
						AND re.elementoIdA = e2.id
						AND ie.id = '$interfaceElementoId' ";

        return $this->_em->createQuery($query)->getResult();
    }
    public function getDisponibilidadElemento($idElemento)
    {
        $sql = "SELECT count(ie) as cont
                FROM schemaBundle:InfoInterfaceElemento ie
                WHERE ie.elementoId = '$idElemento' 
                AND LOWER(ie.estado) = LOWER('not connect') 
               "; 
        
        $query = $this->_em->createQuery($sql);
        $datos = $query->getSingleResult();
        return $datos;
    }

    public function getCountByElemento($idElemento, $estado)
    {
        $sql = "SELECT count(ie) as cont
                FROM schemaBundle:InfoInterfaceElemento ie
                WHERE ie.elementoId = '$idElemento' 
                AND LOWER(ie.estado) = LOWER('$estado') 
               "; 
        
        $query = $this->_em->createQuery($sql);
        $datos = $query->getSingleResult();
        return $datos;
    }
    
    public function generarJsonInterfaces($idElemento,$estado_buscar,$start,$limit){
        $arr_encontrados = array();
    
        $entityTotal = $this->getInterfaces($idElemento,$estado_buscar,'','');
    
        $entidades = $this->getInterfaces($idElemento,$estado_buscar,$start,$limit);
        //        error_log('entra');
        if ($entidades) {
    
            $num = count($entityTotal);
    
            foreach ($entidades as $entidad)
            {
                $estado = $entidad->getEstado();
    
                if($entidad->getEstado()=="not connect"){
                    $estado = "Activo";
                }
                else if($entidad->getEstado()=="connected"){
                    $estado = "Online";
                }
                else if($entidad->getEstado()=="err-disabled"){
                    $estado = "Dañado";
                }
                else if($entidad->getEstado()=="disabled"){
                    $estado = "Inactivo";
                }
    
                $arr_encontrados[]=array('idInterfaceElemento' =>$entidad->getId(),
                                'nombreInterfaceElemento' =>trim($entidad->getNombreInterfaceElemento()),
                                'estado' =>$estado);
            }
    
            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                'encontrados' => array('idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno','idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);
    
                return $resultado;
            }
            else
            {
                $data=json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';
    
                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
    
            return $resultado;
        }
    
    }
    
    /**
     * Documentación para el método 'generarJsonInterfacesPorTipo'.
     *
     * Obtiene registros de Interfaces de Splitter
     * @param integer        $idElemento        Identificador del elemento
     * @param integer        $estado            Estado del elemento
     * @param String         $tipo              Tipo del elemento
     * @param integer        $start             Inicio de registro de paginacion
     * @param integer        $limit             Fin de registro de paginacion
     * @return Object        $resultado listado de registros de Interfaces de Splitter
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 16-12-2014
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 03-06-2015  John Vera
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 19-07-2016  Se agrega control de excepcion al momento de recuperar servicios pertenecientes a una interface
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.3 19-08-2016  Se cambia el nombre del método por un nombre más genérico generarJsonInterfacesSplitter -> generarJsonInterfacesPorTipo
     *                          Se modifican los parametros de entrada del metodo a un array
     *                          Se adiciona logica para elemento CASSETE para el CLIENTE TELCONET.
     */
    public function generarJsonInterfacesPorTipo($arrayParametros)
    {
        $arrayEncontrados    = array();
        $arrayLoginInterface = array();
        $objResultado        = null;
        try
        {
            $entityTotal     = $this->getInterfaces($arrayParametros["idElemento"],
                                                    $arrayParametros["estado"],
                                                    '',
                                                    '');
            $entidades       = $this->getInterfaces($arrayParametros["idElemento"],
                                                    $arrayParametros["estado"],
                                                    $arrayParametros["start"],
                                                    $arrayParametros["limit"]);

            if ($entidades) 
            {

                $num = count($entityTotal);

                foreach ($entidades as $entidad)
                {
                    $estado = "";
                    if($entidad->getEstado()!="deleted")
                    {
                        if($entidad->getEstado()=="not connect")
                        {
                            $estado = "Libre";
                        }
                        else if($entidad->getEstado()=="connected")
                        {
                            $estado = "Ocupado";
                        }
                        else if($entidad->getEstado()=="err-disabled")
                        {
                            $estado = "Dañado";
                        }
                        else if($entidad->getEstado()=="disabled")
                        {
                            $estado = "Inactivo";
                        }
                        else if($entidad->getEstado()=="reserved")
                        {
                            $estado = "Reservado";
                        }
                        else if($entidad->getEstado()=="Factible")
                        {
                            $estado = "Factible";
                        }

                        $login = "";

                        //bloque de codigo para recuperar servicios asociados a un puerto del elemento
                        try
                        {
                            if($arrayParametros["tipo"] == 'CASSETTE')
                            {
                                $arrayLoginInterface['idInterfaceElemento']  = $entidad->getId();
                                $arrayLoginInterface['tipo']                 = $arrayParametros["tipo"];
                                $serviciosPorInterface                       = $this->getLoginInterfaceElementoConector($arrayLoginInterface);
                            }
                            else
                            {
                                $serviciosPorInterface                   = $this->getServiciosPorInterface($entidad->getId(),
                                                                                                           $arrayParametros["codEmpresa"]);
                            }

                            foreach($serviciosPorInterface as $arrayServicio)
                            {
                                if(!$login)
                                {
                                    $login = $arrayServicio['login'];
                                }
                                else
                                {
                                    $login = $login . ", " . $arrayServicio['login'];
                                }
                            }
                        }
                        catch (\Exception $ex) 
                        {
                            $mensajeError = "Error: " . $ex->getMessage();
                            $login        = "";
                            error_log($mensajeError);
                        }    
                        //Se agrega recuperacion de color de hilo en caso de ser ODF
                        $objDetalleInterface = $this->_em->getRepository('schemaBundle:InfoDetalleInterface')
                                                    ->findOneBy(array("interfaceElementoId" => $entidad->getId(),
                                                                      "detalleNombre"       => "Color Hilo"));
                        $strColorHiloInterface = "";
                        if ($objDetalleInterface)
                        {
                            $strColorHiloInterface = $objDetalleInterface->getDetalleValor();
                        }   

                        $arrayEncontrados[] = array('idInterfaceElemento'     => $entidad->getId(),
                                                   'nombreInterfaceElemento' => trim($entidad->getNombreInterfaceElemento()),
                                                   'estado'                  => $estado,
                                                   'login'                   => $login,
                                                   'nombreEstado'            => trim($entidad->getNombreInterfaceElemento()).' / '.$estado,
                                                   'colorHilo'               => $strColorHiloInterface
                                                  );
                    }
                }

                if($num == 0)
                {
                    $objResultado  = array('total'       => 1 ,
                                           'encontrados' => array('idConectorInterface'     => 0 , 
                                                                   'nombreConectorInterface' => 'Ninguno',
                                                                   'idConectorInterface'     => 0 , 
                                                                   'nombreConectorInterface' => 'Ninguno', 
                                                                   'estado'                  => 'Ninguno'),
                                           'status'      => "OK",
                                           'mensaje'     => "Consulta Exitosa.");
                }
                else
                {
                    $objResultado  = array('total'    => $num ,
                                        'encontrados' => $arrayEncontrados,
                                        "status"      => "OK",
                                        "mensaje"     => "Consulta Exitosa.");
                }
            }
            else
            {
                $objResultado  = array('total'       => 0 ,
                                       'encontrados' => [],
                                       'status'      => "ERROR",
                                       'mensaje'     => "ERROR AL OBTENER LAS INTERFACES");
            }
        }
        catch (\Exception $ex) 
        {
            $objResultado  = array('total'       => 0 ,
                                   'encontrados' => [],
                                   "status"      => "ERROR",
                                   "mensaje"     => "Error: " . $ex->getMessage());
        }
        $objResultado = json_encode($objResultado);
        return $objResultado;
    }
    
    /**
     * Documentación para el método 'generarJsonInterfacesOtl'.
     *
     * Obtiene registros de Interfaces de Olt para la administración de tarjetas
     * @param integer        $idElemento        Identificador del elemento
     * @return Object        $resultado listado de registros de Interfaces de Olt
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 31-08-2014
     */
    public function generarJsonInterfacesOtl($idElemento){
        $arr_encontrados   = array();
        $strPermiteReducir = "";
        $encontrados = $this->getInterfacesOlt($idElemento);
        if ($encontrados) 
        {
            foreach ($encontrados as $registro)
            {
                if ( $registro['CANTIDAD_PUERTOS'] > 8 )
                {
                    $strPermiteReducir = "SI";
                }
                else
                {
                    $strPermiteReducir = "NO";
                }
                    
                $arr_encontrados[]=array('idOlt'              => $registro['ID_OLT'],
                                         'nombreTarjeta'      => $registro['TARJETA'],
                                         'cantidadPuertos'    => $registro['CANTIDAD_PUERTOS'],
                                         'cantidadConectados' => $registro['CANTIDAD_CONECTADOS'],
                                         'cantidadLibres'     => $registro['CANTIDAD_LIBRES'],
                                         'permiteAcciones'    => $strPermiteReducir
                                        );
            }
            
            $data      = json_encode($arr_encontrados);
            $resultado = '{"encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado = '{"encontrados":[]}';

            return $resultado;
        }
        
    }
    
    /**
     * Documentación para el método 'generarJsonInterfacesLibres'.
     *
     * Obtiene registros de Interfaces libres
     * @param entityManager  $em
     * @param integer        $idElemento
     * @param integer        $interfaceSplitter Parametro utilizado para filtrar interfaces con estado factible
     * @return Object        $datos listado de registros de Interfaces libres
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 01-12-2014
     */
    public function generarJsonInterfacesLibres($idElemento, $interfaceSplitter = "", $estado="not connect")
    {
        $arr_encontrados = array();
        $entidades = $this->getInterfacesLibresByIdElemento($idElemento,$estado);
        if($entidades)
        {
            foreach($entidades as $entidad)
            {

                $arr_encontrados[] = array('idInterfaceElemento' => $entidad->getId(),
                    'nombreInterfaceElemento' => trim($entidad->getNombreInterfaceElemento()),
                    'estado' => $entidad->getEstado());
            }
        }

        if($interfaceSplitter != "")
        {
            $entityInterfaceSplitter = $this->_em->getRepository('schemaBundle:InfoInterfaceElemento')->find($interfaceSplitter);
            if($entityInterfaceSplitter)
            {
                $arr_encontrados[] = array('idInterfaceElemento' => $entityInterfaceSplitter->getId(),
                    'nombreInterfaceElemento' => trim($entityInterfaceSplitter->getNombreInterfaceElemento()),
                    'estado' => $entityInterfaceSplitter->getEstado());
                //ordena arreglo de menor a mayor
                sort($arr_encontrados);
            }
        }
        if(count($arr_encontrados) > 0)
        {
            $data = json_encode($arr_encontrados);
            $resultado = '{"total":"' . count($arr_encontrados) . '","encontrados":' . $data . '}';

            return $resultado;
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';

            return $resultado;
        }
    }

    public function getInterfaces($idElemento,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:InfoInterfaceElemento','e');
               
            
        if($idElemento!=""){
            $qb ->where( 'e.elementoId = ?1');
            $qb->setParameter(1, $idElemento);
        }
        if($estado!="Todos"){
            $qb ->andWhere('e.estado = ?2');
            $qb->setParameter(2, $estado);
        }
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query->getResult();
    }
    
    public function getInterfacesByIdElemento($idElemento)
    {
        $sql = "SELECT iie
                FROM schemaBundle:InfoInterfaceElemento iie
                WHERE iie.elementoId = $idElemento 
                AND LOWER(iie.estado) not in ('eliminado','deleted')
                ORDER BY iie.id
               "; 
        
        $query = $this->_em->createQuery($sql);
        $datos = $query->getResult();
        
        return $datos;
    }
    
    /**
     * getCapacidadesPorInterface
     * Metodo que devuelve el total de ancho de banda por puerto de un determinado switch que contenga servicios 
     * Costo Query 31
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 09-05-2017
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.1 15-01-2018 - Se añade login del punto y se realiza agrupacion por login del total de ancho de banda por determinado switch.
     * 
     * @author Fabricio Bermeo R <fbermeo@telconet.ec>
     * @version 1.2 08-02-2018 - Se busca el archivo de banda por puerto de el ultimo servicio que fue cancelado, se mejora la formación del sql
     * 
     * @param integer $arrayParametros['intInterfaceElemento']
     *         string $arrayParametros['arrayEstados']
     *         string $arrayParametros['arrayProductoMarginado']
     * @return Array $arrayResultado [ totalCapacidad1 , totalCapacidad2 ] 
     */
    public function getCapacidadesPorInterface($arrayParametros)
    {
        $intInterfaceElemento   = $arrayParametros['intInterfaceElemento'];
        $arrayEstados           = $arrayParametros['arrayEstados'];
        $arrayProductoMarginado = $arrayParametros['arrayProductoMarginado'];
        
        $arrayResultado = array();
        
        try
        {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $query = $this->_em->createNativeQuery(null, $rsm);

            $strSql           = "";
            $strCriterio      = " ";
            $strQueryInicial  = "SELECT 
                                    NVL(SUM(INFO.CAPACIDAD1),0) TOTAL_CAPACIDAD1,
                                    NVL(SUM(INFO.CAPACIDAD2),0) TOTAL_CAPACIDAD2,
                                    INFO.ESTADO AS ESTADO
                                 FROM";
            $strSelect = "          (SELECT
                                       DB_COMERCIAL.TECNK_SERVICIOS.GET_VALOR_SERVICIO_PROD_CARACT(S.ID_SERVICIO,:capacidad1) CAPACIDAD1,
                                       DB_COMERCIAL.TECNK_SERVICIOS.GET_VALOR_SERVICIO_PROD_CARACT(S.ID_SERVICIO,:capacidad2) CAPACIDAD2";
            $strFrom = "            \nFROM INFO_SERVICIO_TECNICO ST,
                                        INFO_SERVICIO S";
            $strWhere = "           \nWHERE ST.INTERFACE_ELEMENTO_ID = :interface
                                        AND ST.SERVICIO_ID       = S.ID_SERVICIO";
            switch($arrayParametros['tipo'])
            {
                case '1':
                    $strSelect .= ",'ACTIVO' AS ESTADO";
                    $strWhere .= " AND S.ESTADO IN (:estado)";
                    break;
                case '2':
                    $strSelect .= ",'CANCELADO' AS ESTADO";
                    $strFrom .= ",INFO_SERVICIO_HISTORIAL HIS";
                    $strWhere .= " AND S.ID_SERVICIO = HIS.SERVICIO_ID";
                    $strWhere .= " AND HIS.ID_SERVICIO_HISTORIAL = (
                                                                    SELECT MAX(ISH.ID_SERVICIO_HISTORIAL)
                                                                    FROM DB_COMERCIAL.INFO_SERVICIO SER,
                                                                      DB_COMERCIAL.INFO_SERVICIO_HISTORIAL ISH
                                                                    WHERE SER.ID_SERVICIO = ISH.SERVICIO_ID
                                                                    AND ISH.ESTADO IN (:estado)
                                                                    )";
                    break;
                default:
                    break;
            }
            if($arrayProductoMarginado)
            {
                $strCriterio  = " AND S.PRODUCTO_ID NOT IN (SELECT ID_PRODUCTO P 
                                                                 FROM DB_COMERCIAL.ADMI_PRODUCTO P 
                                                                 WHERE P.NOMBRE_TECNICO IN (:producto)) ";
                $query->setParameter("producto", $arrayProductoMarginado);
            }
            
            $strWhere .= $strCriterio .") INFO\nGROUP BY INFO.ESTADO";
            $strSql = $strQueryInicial . $strSelect . $strFrom . $strWhere;
            $rsm->addScalarResult('TOTAL_CAPACIDAD1', 'totalCapacidad1', 'integer');
            $rsm->addScalarResult('TOTAL_CAPACIDAD2', 'totalCapacidad2', 'integer');
            $rsm->addScalarResult('ESTADO', 'estado', 'string');
            
            $query->setParameter("interface", $intInterfaceElemento);
            $query->setParameter("estado",  $arrayEstados);
            $query->setParameter("capacidad1", 'CAPACIDAD1');
            $query->setParameter("capacidad2", 'CAPACIDAD2');
            
            $query->setSQL($strSql);

            $arrayResultado = $query->getOneOrNullResult();
        } 
        catch (\Exception $ex) 
        {
            error_log($ex->getMessage());
        }
        
        return $arrayResultado;
    }    
    
    /**
     * getCapacidadesPorIp
     * Metodo que devuelve el total de ancho de banda por ip que contenga servicios 
     * Costo Query 31
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 03-10-2017
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.1 15-01-2018 - Se añade login del punto y se realiza agrupacion por login del total de ancho de banda por ip.
     * 
     * @param  string $arrayParametros['strIp']
     *         array  $arrayParametros['arrayEstados']
     *         array  $arrayParametros['arrayEstadosIps']
     *         array  $arrayParametros['arrayProductoMarginado']
     * @return Array $arrayResultado [ totalCapacidad1 , totalCapacidad2 ] 
     */
    public function getCapacidadesPorIp($arrayParametros)
    {
        $strIp                  = $arrayParametros['strIp'];
        $arrayEstados           = $arrayParametros['arrayEstados'];
        $arrayEstadosIps        = $arrayParametros['arrayEstadosIps'];
        $arrayProductoMarginado = $arrayParametros['arrayProductoMarginado'];
        
        $arrayResultado = array();
        
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = "SELECT 
                        NVL(SUM(INFO.CAPACIDAD1),0) TOTAL_CAPACIDAD1,
                        NVL(SUM(INFO.CAPACIDAD2),0) TOTAL_CAPACIDAD2
                      FROM
                        (SELECT
                          DB_COMERCIAL.TECNK_SERVICIOS.GET_VALOR_SERVICIO_PROD_CARACT(S.ID_SERVICIO,:capacidad1) CAPACIDAD1,
                          DB_COMERCIAL.TECNK_SERVICIOS.GET_VALOR_SERVICIO_PROD_CARACT(S.ID_SERVICIO,:capacidad2) CAPACIDAD2
                        FROM INFO_IP IIP,
                          INFO_SERVICIO S
                        WHERE S.ID_SERVICIO = IIP.SERVICIO_ID
                        AND IIP.ESTADO      IN (:estadoIp)
                        AND S.ESTADO        IN (:estado)
                        AND IIP.IP          =  :ipServicio";
            
            if($arrayProductoMarginado)
            {
                $strSql.= "AND S.PRODUCTO_ID              NOT IN (SELECT ID_PRODUCTO P 
                                                                FROM DB_COMERCIAL.ADMI_PRODUCTO P 
                                                               WHERE P.NOMBRE_TECNICO IN (:producto)) ";
                $objQuery->setParameter("producto", $arrayProductoMarginado);
            }
            
            $strSql.= " ) INFO";

            $objRsm->addScalarResult('TOTAL_CAPACIDAD1', 'totalCapacidad1', 'integer');
            $objRsm->addScalarResult('TOTAL_CAPACIDAD2', 'totalCapacidad2', 'integer');

            $objQuery->setParameter("estadoIp"  , $arrayEstadosIps);
            $objQuery->setParameter("ipServicio", $strIp);
            $objQuery->setParameter("estado"    , $arrayEstados);
            $objQuery->setParameter("capacidad1", 'CAPACIDAD1');
            $objQuery->setParameter("capacidad2", 'CAPACIDAD2');
                        
            $objQuery->setSQL($strSql);
            $arrayResultado = $objQuery->getOneOrNullResult();
        } 
        catch (\Exception $objEx) 
        {
            error_log($objEx->getMessage());
        }
        
        return $arrayResultado;
    }  
    
    /**
     * getServiciosPorInterfaceElemento
     * Metodo que devuelve el total de ancho de banda por puerto de un determinado switch que contenga servicios 
     * Costo Query 65
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 09-05-2017
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 29-11-2017       Se agregan modificaciones para retornar CAPACIDAD 2 de servicios consultados
     * @since 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 24-04-2019  - Se agrega el campo razón social, cambio solicitado por reporte a gerencia
     * @since 1.1
     *
     * @param  Array $arrayParametros['intInterfaceElemento']
     *        string $arrayParametros['arrayEstados']
     *        array  $arrayParametros['arrayProductoMarginado']
     * @return Array $arrayResultado [ totalCapacidad1 , totalCapacidad2 ] 
     */
    public function getServiciosPorInterfaceElemento($arrayParametros)
    {
        $intInterfaceElemento   = $arrayParametros['intInterfaceElemento'];
        $arrayEstados           = $arrayParametros['arrayEstados'];
        $arrayProductoMarginado = $arrayParametros['arrayProductoMarginado'];
        
        $arrayResultado = array();
        
        try
        {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $query = $this->_em->createNativeQuery(null, $rsm);

            $sql = "SELECT 
                    PU.LOGIN,
                    S.LOGIN_AUX,
                    (SELECT DECODE(IPE.RAZON_SOCIAL,NULL,IPE.NOMBRES||' '||IPE.NOMBRES,IPE.RAZON_SOCIAL)
                    FROM DB_COMERCIAL.INFO_PERSONA IPE WHERE IPE.ID_PERSONA = (
                    SELECT IPER.PERSONA_ID FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER
                    WHERE IPER.ID_PERSONA_ROL = (PU.PERSONA_EMPRESA_ROL_ID))) AS RAZON_SOCIAL,
                    P.DESCRIPCION_PRODUCTO         AS PRODUCTO,
                    S.DESCRIPCION_PRESENTA_FACTURA AS SERVICIO,
                    S.ESTADO,
                    S.USR_VENDEDOR,
                    DB_COMERCIAL.TECNK_SERVICIOS.GET_VALOR_SERVICIO_PROD_CARACT(S.ID_SERVICIO,:capacidad1) AS BW_SUBIDA,
                    DB_COMERCIAL.TECNK_SERVICIOS.GET_VALOR_SERVICIO_PROD_CARACT(S.ID_SERVICIO,:capacidad2) AS BW_BAJADA
                  FROM
                    DB_COMERCIAL.INFO_SERVICIO_TECNICO ST,
                    DB_COMERCIAL.INFO_PUNTO PU,
                    DB_COMERCIAL.INFO_SERVICIO S,
                    DB_COMERCIAL.ADMI_PRODUCTO P
                  WHERE
                      PU.ID_PUNTO                    = S.PUNTO_ID
                  AND P.ID_PRODUCTO                  = S.PRODUCTO_ID
                  AND S.ID_SERVICIO                  = ST.SERVICIO_ID
                  AND ST.INTERFACE_ELEMENTO_ID       = :interface 
                  AND S.ESTADO                      IN (:estado) ";
            
            if($arrayProductoMarginado)
            {
                $sql.= "AND S.PRODUCTO_ID          NOT IN (SELECT ID_PRODUCTO P 
                                                             FROM DB_COMERCIAL.ADMI_PRODUCTO P 
                                                            WHERE P.NOMBRE_TECNICO IN (:producto)) ";
                $query->setParameter("producto", $arrayProductoMarginado);
            }            

            $rsm->addScalarResult('LOGIN', 'login', 'string');
            $rsm->addScalarResult('LOGIN_AUX', 'loginAux', 'string');
            $rsm->addScalarResult('RAZON_SOCIAL', 'razonSocial', 'string');
            $rsm->addScalarResult('PRODUCTO', 'producto', 'string');
            $rsm->addScalarResult('SERVICIO', 'servicio', 'string');
            $rsm->addScalarResult('ESTADO', 'estado', 'string');
            $rsm->addScalarResult('USR_VENDEDOR', 'vendedor', 'string');
            $rsm->addScalarResult('BW_SUBIDA', 'bwSubida', 'integer');
            $rsm->addScalarResult('BW_BAJADA', 'bwBajada', 'integer');

            $query->setParameter("interface", $intInterfaceElemento);
            $query->setParameter("estado",  $arrayEstados);
            $query->setParameter("capacidad1", 'CAPACIDAD1');
            $query->setParameter("capacidad2", 'CAPACIDAD2');
            $query->setParameter("estadoSpc1", 'Activo');
            $query->setParameter("estadoSpc2", 'Activo');
            
            $query->setSQL($sql);

            $arrayResultado = $query->getResult();
        } 
        catch (\Exception $ex) 
        {
            error_log($ex->getMessage());
        }
        
        return $arrayResultado;
    }        
    
    /**
     * getServiciosPorIp
     * Metodo que devuelve el total de ancho de banda por ip que contenga servicios 
     * Costo Query 29
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 03-10-2017
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 29-11-2017       Se agregan modificaciones para retornar CAPACIDAD 2 de servicios consultados
     * @since 1.0
     *
     * Costo: 10
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 13-03-2019       Se agrega información de la ultima milla,nombre del switch y puerto de donde sale el cliente.
     * @since 1.1
     *
     * @param  string $arrayParametros['strIp']
     *         array  $arrayParametros['arrayEstados']
     *         array  $arrayParametros['arrayEstadosIps']
     *         array  $arrayParametros['arrayProductoMarginado']
     * @return Array $arrayResultado [ totalCapacidad1 , totalCapacidad2 ] 
     */
    public function getServiciosPorIp($arrayParametros)
    {
        $strIp                  = $arrayParametros['strIp'];
        $arrayEstadosIps        = $arrayParametros['arrayEstadosIps'];
        $arrayEstados           = $arrayParametros['arrayEstados'];
        $arrayProductoMarginado = $arrayParametros['arrayProductoMarginado'];
        
        $arrayResultado = array();
        
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = "SELECT
                    PU.LOGIN,
                    S.LOGIN_AUX,
                    (SELECT IE.NOMBRE_ELEMENTO FROM INFO_ELEMENTO IE WHERE IE.ID_ELEMENTO = (
                        SELECT IST2.ELEMENTO_ID FROM INFO_SERVICIO_TECNICO IST2 WHERE IST2.ID_SERVICIO_TECNICO = (
                        SELECT MAX(IST.ID_SERVICIO_TECNICO) FROM INFO_SERVICIO_TECNICO IST WHERE IST.SERVICIO_ID = S.ID_SERVICIO))) SW,
                    (SELECT IIE.NOMBRE_INTERFACE_ELEMENTO FROM INFO_INTERFACE_ELEMENTO IIE WHERE IIE.ID_INTERFACE_ELEMENTO = (
                        SELECT IST2.INTERFACE_ELEMENTO_ID FROM INFO_SERVICIO_TECNICO IST2 WHERE IST2.ID_SERVICIO_TECNICO = (
                        SELECT MAX(IST.ID_SERVICIO_TECNICO) FROM INFO_SERVICIO_TECNICO IST WHERE IST.SERVICIO_ID = S.ID_SERVICIO))) PUERTO_SW,
                    P.DESCRIPCION_PRODUCTO         AS PRODUCTO,
                    S.DESCRIPCION_PRESENTA_FACTURA AS SERVICIO,
                    S.ESTADO,
                    S.USR_VENDEDOR,
                    DB_COMERCIAL.TECNK_SERVICIOS.GET_VALOR_SERVICIO_PROD_CARACT(S.ID_SERVICIO,:capacidad1) AS BW_SUBIDA,
                    DB_COMERCIAL.TECNK_SERVICIOS.GET_VALOR_SERVICIO_PROD_CARACT(S.ID_SERVICIO,:capacidad2) AS BW_BAJADA
                  FROM
                    DB_INFRAESTRUCTURA.INFO_IP IIP,
                    DB_COMERCIAL.INFO_PUNTO PU,
                    DB_COMERCIAL.INFO_SERVICIO S,
                    DB_COMERCIAL.ADMI_PRODUCTO P
                  WHERE
                      PU.ID_PUNTO                    = S.PUNTO_ID
                  AND P.ID_PRODUCTO                  = S.PRODUCTO_ID
                  AND S.ID_SERVICIO                  = IIP.SERVICIO_ID
                  AND IIP.ESTADO                    IN (:estadoIp)
                  AND S.ESTADO                      IN (:estado)
                  AND IIP.IP                         = :ipServicio  ";
            
            if($arrayProductoMarginado)
            {
                $strSql.= "AND S.PRODUCTO_ID          NOT IN (SELECT ID_PRODUCTO P 
                                                             FROM DB_COMERCIAL.ADMI_PRODUCTO P 
                                                            WHERE P.NOMBRE_TECNICO IN (:producto)) ";
                $objQuery->setParameter("producto", $arrayProductoMarginado);
            }            

            $objRsm->addScalarResult('LOGIN', 'login', 'string');
            $objRsm->addScalarResult('LOGIN_AUX', 'loginAux', 'string');
            $objRsm->addScalarResult('SW', 'sw', 'string');
            $objRsm->addScalarResult('PUERTO_SW', 'puerto_sw', 'string');
            $objRsm->addScalarResult('PRODUCTO', 'producto', 'string');
            $objRsm->addScalarResult('SERVICIO', 'servicio', 'string');
            $objRsm->addScalarResult('ESTADO', 'estado', 'string');
            $objRsm->addScalarResult('USR_VENDEDOR', 'vendedor', 'string');
            $objRsm->addScalarResult('BW_SUBIDA', 'bwSubida', 'integer');
            $objRsm->addScalarResult('BW_BAJADA', 'bwBajada', 'integer');

            $objQuery->setParameter("ipServicio", $strIp);
            $objQuery->setParameter("estado",  $arrayEstados);
            $objQuery->setParameter("estadoIp",  $arrayEstadosIps);
            $objQuery->setParameter("capacidad1", 'CAPACIDAD1');
            $objQuery->setParameter("capacidad2", 'CAPACIDAD2');
            $objQuery->setParameter("estadoSpc1", 'Activo');
            $objQuery->setParameter("estadoSpc2", 'Activo');
            
            $objQuery->setSQL($strSql);
            $arrayResultado = $objQuery->getResult();
        } 
        catch (\Exception $objEx) 
        {
            error_log($objEx->getMessage());
        }
        
        return $arrayResultado;
    }    
    
    public function getInterfacesLibresByIdElemento($idElemento,$estado)
    {
        $sql = "SELECT iie
                FROM schemaBundle:InfoInterfaceElemento iie
                WHERE iie.elementoId = $idElemento 
                AND LOWER(iie.estado) in ('".$estado."')
                ORDER BY iie.id
               "; 
        $query = $this->_em->createQuery($sql);
        $datos = $query->getResult();
        
        return $datos;
    }
    
    /**
     * Documentación para el método 'getInterfacesOlt'.
     *
     * Obtiene registros de Interfaces de Olt para la adminsitración de tarjetas
     * @param integer        $idElemento
     * @return Object        $datos listado de registros de Interfaces de olt
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 31-08-2015
     */
    public function getInterfacesOlt($idElemento) 
    {
        $sql = "SELECT id_olt,
                nombre_tarjeta
                ||'/' tarjeta,
                cantidad_puertos,
                (SELECT count(1)
                FROM info_interface_elemento
                WHERE info_interface_elemento.elemento_id=id_olt
                AND substr(nombre_interface_elemento, :limiteIni1 , :limiteFin2 )=nombre_tarjeta
                AND estado                               =:estado1Param
                ) cantidad_conectados,
                (SELECT count(1)
                FROM info_interface_elemento
                WHERE info_interface_elemento.elemento_id=id_olt
                AND substr(nombre_interface_elemento, :limiteIni3 , :limiteFin4 )=nombre_tarjeta
                AND estado                               =:estado2Param
                ) cantidad_libres
              FROM
                (SELECT elemento_id id_olt,
                  substr(nombre_interface_elemento, 0 , 1 ) nombre_tarjeta,
                  count(1) cantidad_puertos
                FROM info_interface_elemento
                WHERE elemento_id=:idElementoParam
                AND estado not in (:estadoInt1Param, :estadoInt2Param)
                GROUP BY substr(nombre_interface_elemento, 0 , 1 ),
                  elemento_id
                ) registros ORDER BY nombre_tarjeta ASC";
        $strEstadoConnected  = "connected";
        $strEstadoNotConnect = "not connect";
        $strEstadoErr = "err-disabled";
        $strEstadoEli = "Eliminado";
        $intNumCero = 0;
        $intNumUno  = 1;
        $stmt = $this->_em->getConnection()->prepare($sql); 
        $stmt->bindValue('limiteIni1',   $intNumCero);
        $stmt->bindValue('limiteFin2',   $intNumUno);
        $stmt->bindValue('estado1Param', $strEstadoConnected);
        $stmt->bindValue('limiteIni3',   $intNumCero);
        $stmt->bindValue('limiteFin4',   $intNumUno);
        $stmt->bindValue('estado2Param', $strEstadoNotConnect);
        $stmt->bindValue('estadoInt1Param', $strEstadoErr);
        $stmt->bindValue('estadoInt2Param', $strEstadoEli);
        $stmt->bindValue('idElementoParam', $idElemento);
        $stmt->execute();
        $arrayResult = $stmt->fetchAll();
        return $arrayResult;
    }
    
    /**
     * Documentación para el método 'generarJsonInterfacesTarjeta'.
     *
     * Obtiene registros de Interfaces de una Tarjeta de Olt
     * @param integer        $idElemento
     * @param string         $nombreTarjeta
     * @return Object        $datos listado de registros de Interfaces de Tarjeta
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 01-09-2015
     */
    public function generarJsonInterfacesTarjeta($idElemento, $nombreTarjeta)
    {
        $arr_encontrados = array();
        $entidades       = $this->getInterfacesTarjeta($idElemento,$nombreTarjeta);
        if($entidades)
        {
            foreach($entidades as $entidad)
            {

                $arr_encontrados[] = array('idInterfaceElemento'     => $entidad->getId(),
                                           'nombreInterfaceElemento' => trim($entidad->getNombreInterfaceElemento()),
                                           'estado'                  => $entidad->getEstado());
            }
        }

        if(count($arr_encontrados) > 0)
        {
            $data      = json_encode($arr_encontrados);
            $resultado = '{"total":"' . count($arr_encontrados) . '","encontrados":' . $data . '}';

            return $resultado;
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';

            return $resultado;
        }
    }
    
    /**
     * Documentación para el método 'getInterfacesTarjeta'.
     *
     * Obtiene registros de Interfaces de una tarjeta de  Olt
     * @param integer        $idElemento
     * @param string         $nombreTarjeta
     * @return Object        $datos listado de registros de Interfaces de olt
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 31-08-2015
     */
    public function getInterfacesTarjeta($idElemento,$nombreTarjeta)
    {
        $sql = "SELECT iie
                FROM schemaBundle:InfoInterfaceElemento iie
                WHERE iie.elementoId = :idElementoParam 
                AND iie.nombreInterfaceElemento like :nombreInterfaceParam
                AND iie.estado not in (:estadoInt1Param, :estadoInt2Param)
                ORDER BY iie.id
               "; 
        $query = $this->_em->createQuery($sql);
        $query->setParameter('idElementoParam',      $idElemento);
        $query->setParameter('nombreInterfaceParam', $nombreTarjeta .'%');
        $query->setParameter('estadoInt1Param', "err-disabled");
        $query->setParameter('estadoInt2Param', "Eliminado");
        $datos = $query->getResult();
        
        return $datos;
    }

    /**
     * 
     * Metodo que devuelve el total de ancho de banda por puerto de un determinado switch que contenga servicios en estado Activo
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 21-04-2016
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 26-01-2017 se requiere excluir el producto internet wifi para que no sume bw en la reconfiguración del puerto
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 04-07-2017 se excluye estado In-Corte a la consulta
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 08-06-2020 - Se agrega parametros para validaciones de productos y estados del servicio
     *
     * @param integer $intInterfaceId
     * @return Array $arrayResultado [ totalCapacidad1 , totalCapacidad2 ] 
     */
    public function getResultadoCapacidadesPorInterface($intInterfaceId)
    {
        $arrayResultado = array();
        
        try
        {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $query = $this->_em->createNativeQuery(null, $rsm);

            $strSql = "SELECT
                        DB_COMERCIAL.TECNK_SERVICIOS.GET_VALOR_SERVICIO_PROD_CARACT(SER.ID_SERVICIO,:capacidad1) CAPACIDAD1,
                        DB_COMERCIAL.TECNK_SERVICIOS.GET_VALOR_SERVICIO_PROD_CARACT(SER.ID_SERVICIO,:capacidad2) CAPACIDAD2
                    FROM
                        DB_COMERCIAL.INFO_SERVICIO SER
                    INNER JOIN DB_COMERCIAL.INFO_SERVICIO_TECNICO         TEC ON SER.ID_SERVICIO = TEC.SERVICIO_ID
                    INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO                 PRO ON SER.PRODUCTO_ID = PRO.ID_PRODUCTO
                    WHERE SER.ESTADO IN (:ESTADO_SERVICIOS) AND TEC.INTERFACE_ELEMENTO_ID = :interface";

            //obtengo la cabecera de los productos permitidos
            $objAdmiParametroCabProPer  = $this->_em->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                    array('nombreParametro' => 'PRODUCTOS_PERMITIDOS_BW_INTERFACE',
                                                          'estado'          => 'Activo'));
            if( is_object($objAdmiParametroCabProPer) )
            {
                //arreglo de los productos permitidos
                $arrayIdProductosPermitidos = array();
                $arrayParametrosDet = $this->_em->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                        array("parametroId" => $objAdmiParametroCabProPer->getId(),
                                                              "estado"      => "Activo"));
                foreach($arrayParametrosDet as $objParametro)
                {
                    $arrayIdProductosPermitidos[] = $objParametro->getValor1();
                }
                if( !empty($arrayIdProductosPermitidos) )
                {
                    $strSql = $strSql." AND PRO.ID_PRODUCTO IN (:ARRAY_ID_PRODUCTOS)";
                    $query->setParameter("ARRAY_ID_PRODUCTOS", array_values($arrayIdProductosPermitidos));
                }
            }

            //obtengo la cabecera de los productos no permitidos
            $objAdmiParametroCabProNoPer  = $this->_em->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                    array('nombreParametro' => 'PRODUCTOS_NO_PERMITIDOS_BW_INTERFACE',
                                                          'estado'          => 'Activo'));
            if( is_object($objAdmiParametroCabProNoPer) )
            {
                //arreglo de los productos no permitidos
                $arrayIdProductosNoPermitidos = array();
                $arrayParametrosDet = $this->_em->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                        array("parametroId" => $objAdmiParametroCabProNoPer->getId(),
                                                              "estado"      => "Activo"));
                foreach($arrayParametrosDet as $objParametro)
                {
                    $arrayIdProductosNoPermitidos[] = $objParametro->getValor1();
                }
                if( !empty($arrayIdProductosNoPermitidos) )
                {
                    $strSql = $strSql." AND PRO.ID_PRODUCTO NOT IN (:ARRAY_ID_PRODUCTOS_NOT)";
                    $query->setParameter("ARRAY_ID_PRODUCTOS_NOT", array_values($arrayIdProductosNoPermitidos));
                }
            }

            //arreglo de los estados de los servicios permitidos
            $arrayEstadosServiciosPermitidos = array();
            //obtengo la cabecera de los estados de los servicios permitidos
            $objAdmiParametroCabEstadosServ  = $this->_em->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                        array('nombreParametro' => 'ESTADOS_SERVICIOS_BW_INTERFACE',
                                                              'estado'          => 'Activo'));
            if( is_object($objAdmiParametroCabEstadosServ) )
            {
                $arrayParametrosDet = $this->_em->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                        array(  "parametroId" => $objAdmiParametroCabEstadosServ->getId(),
                                                                "estado"      => "Activo"));
                foreach($arrayParametrosDet as $objParametro)
                {
                    $arrayEstadosServiciosPermitidos[] = $objParametro->getValor1();
                }
            }
            if( !empty($arrayEstadosServiciosPermitidos) )
            {
                $query->setParameter("ESTADO_SERVICIOS", array_values($arrayEstadosServiciosPermitidos));
            }
            else
            {
                $query->setParameter("ESTADO_SERVICIOS",  array("Activo","EnPruebas"));
            }

            $strSql = "SELECT 
                        NVL(SUM(INFO.CAPACIDAD1),0) TOTAL_CAPACIDAD1,
                        NVL(SUM(INFO.CAPACIDAD2),0) TOTAL_CAPACIDAD2
                    FROM ".
                    "( ".$strSql." ) INFO";

            $rsm->addScalarResult('TOTAL_CAPACIDAD1', 'totalCapacidad1', 'integer');
            $rsm->addScalarResult('TOTAL_CAPACIDAD2', 'totalCapacidad2', 'integer');

            $query->setParameter("interface", $intInterfaceId);
            $query->setParameter("capacidad1", 'CAPACIDAD1');
            $query->setParameter("capacidad2", 'CAPACIDAD2');
            
            $query->setSQL($strSql);

            $arrayResultado = $query->getOneOrNullResult();
        } 
        catch (\Exception $ex) 
        {
            error_log($ex->getMessage());
        }
        
        return $arrayResultado;
    }
    
    /**
     * 
     * Metodo que devuelve las vlans por servicios activos vinculados a una interface del switch
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 21-04-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1
     * @since 14-09-2016 - Se devuelve informacion  de login auxiliar en la consulta
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.2 2016-09-21 - Se devuelve ELEMENTO_CLIENTE_ID,ELEMENTO_CONECTOR_ID,ULTIMA_MILLA_ID del Servicio Tecnico
     * 
     * @param integer $intInterfaceId
     * @return Array $arrayResultado [ idServicio , interface , vlan , loginAux ,
     *                                 elementoClienteId , elementoConectorId , ultimaMillaId] 
     */
    public function getResultadoVlansPorInterface($intInterfaceId)
    {
        $arrayResultado = array();
        
        try
        {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $query = $this->_em->createNativeQuery(null, $rsm);

            $sql = "  SELECT     
                        SERV.ID_SERVICIO,
                        SERV.LOGIN_AUX,
                        ST.INTERFACE_ELEMENTO_CONECTOR_ID INTERFACE,
                        ST.ELEMENTO_CLIENTE_ID,
                        ST.ELEMENTO_CONECTOR_ID,
                        ST.ULTIMA_MILLA_ID,
                        DB_COMERCIAL.TECNK_SERVICIOS.GET_VALOR_DETALLE_ELEMENTO(DB_COMERCIAL.TECNK_SERVICIOS.GET_VALOR_PER_EMP_ROL_CAR(
                        DB_COMERCIAL.TECNK_SERVICIOS.GET_VALOR_SERVICIO_PROD_CARACT(SERV.ID_SERVICIO,:vlan),PROD.NOMBRE_TECNICO,0,'')) VLAN
                      FROM 
                        INFO_SERVICIO SERV,
                        ADMI_PRODUCTO PROD,
                        INFO_SERVICIO_TECNICO ST
                      WHERE 
                        SERV.ID_SERVICIO IN
                        (SELECT S.ID_SERVICIO
                        FROM INFO_SERVICIO_TECNICO ST,
                          INFO_SERVICIO S
                        WHERE ST.INTERFACE_ELEMENTO_ID = :interface
                        AND ST.SERVICIO_ID             = S.ID_SERVICIO
                        AND S.ESTADO                   IN (:estado)
                        )
                      AND SERV.ID_SERVICIO = ST.SERVICIO_ID
                      AND SERV.PRODUCTO_ID = PROD.ID_PRODUCTO ";

            $rsm->addScalarResult('ID_SERVICIO', 'idServicio', 'integer');
            $rsm->addScalarResult('INTERFACE', 'interface', 'integer');
            $rsm->addScalarResult('ELEMENTO_CLIENTE_ID', 'elementoClienteId', 'integer');
            $rsm->addScalarResult('ELEMENTO_CONECTOR_ID', 'elementoConectorId', 'integer');
            $rsm->addScalarResult('ULTIMA_MILLA_ID', 'ultimaMillaId', 'integer');
            $rsm->addScalarResult('VLAN', 'vlan', 'integer');
            $rsm->addScalarResult('LOGIN_AUX', 'loginAux', 'string');

            $query->setParameter("interface", $intInterfaceId);
            $query->setParameter("estado", array("Activo","EnPruebas","In-Corte"));
            $query->setParameter("vlan", 'VLAN');
            
            $query->setSQL($sql);

            $arrayResultado = $query->getResult();
        } 
        catch (\Exception $ex) 
        {
            error_log($ex->getMessage());
        }
        
        return $arrayResultado;
    }

    /**
     * getHilosServicios, obtiene los hilos usados en los servicios del punto.
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 20-05-2016
     * @since 1.0
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 28-11-2016      Se agrega filtro de tipo de enlace a consultar al recuperar 
     *                              hilos de servicios del punto disponibles a usar
     * @since 1.0
     *
     * @param array $arrayParametros[
     *                              '(array)'                    => (array) Para todos los array's que contengan los elementos.
     *                                                              [
     *                                                              'strComparador' => Simbolo con el que se quiere realizar la busqueda (IN, =, LIKE)
     *                                                              'arrayEstado'   => Recibe el estado.
     *                                                              ]
     *                              'arrayEnlace'                => ['arrayEstado']
     *                              'arrayEnlaceIni'             => ['arrayEstado']
     *                              'arrayInterfaceElemento'     => ['arrayEstado']
     *                              'arrayServicio'              => ['arrayEstado']
     *                              'arrayPunto'                 => ['arrayEstado', 'arrayPunto' => Recibe el id del punto]
     *                              'arrayTipoEnlace'            => ['arrayTipoEnlace'] Tipo de enlace del servicio a asignar factibilidad
     *                              'intStart'                   => Recibe el inicio para el resultado de la busqueda.
     *                              'intLimit'                   => Recibe el fin para el resultado de la busqueda del query.
     *                              ]
     * @return \telconet\schemaBundle\Entity\ReturnResponse Retorna un objeto con los registros
     */
    public function getHilosServicios($arrayParametros)
    {
        $objReturnResponse = new ReturnResponse();
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        try
        {
            $objQueryCount = $this->_em->createQuery();
            $strQueryCount = "SELECT count(ist.id) ";
            $objQuery = $this->_em->createQuery();
            $strQuery = "SELECT ist.id intIdServicioTecnico, "
                      . " iie.id intIdInterfaceElemento, "
                      . " iie.nombreInterfaceElemento strNombreInterfaceElemento, "
                      . " ip.id intIdPunto, "
                      . " isr.id intIdServicio, "
                      . " ibh.id intIdBufferHilo, "
                      . " ab.id intIdBuffer, "
                      . " ah.id intIdHilo, "
                      . " ah.colorHilo strColorHilo, "
                      . " ah.numeroHilo intNumeroHilo, "
                      . " IDENTITY (ieIni.interfaceElementoFinId) intIdInterfaceElementoPadre ";

            $strFromQuery = "FROM schemaBundle:InfoServicioTecnico ist, "
                                . " schemaBundle:InfoEnlace ie, "
                                . " schemaBundle:InfoEnlace ieIni, "
                                . " schemaBundle:InfoInterfaceElemento iie, "
                                . " schemaBundle:InfoServicio isr, "
                                . " schemaBundle:InfoPunto ip, "
                                . " schemaBundle:InfoBufferHilo ibh, "
                                . " schemaBundle:AdmiBuffer ab, "
                                . " schemaBundle:AdmiHilo ah "
                                . " WHERE ist.servicioId             = isr.id "
                                . " AND isr.puntoId                  = ip.id "
                                . " AND ie.interfaceElementoFinId    = ist.interfaceElementoConectorId "
                                . " AND iie.id                       = ist.interfaceElementoConectorId "
                                . " AND ieIni.interfaceElementoFinId = ie.interfaceElementoIniId "
                                . " AND ieIni.bufferHiloId           = ibh.id "
                                . " AND ibh.bufferId                 = ab.id "
                                . " AND ibh.hiloId                   = ah.id ";

            //Pregunta si $arrayParametros['arrayEnlace']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayEnlace']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ie.estado ';
                $arrayParams['strComparador']   = $arrayParametros['arrayEnlace']['strComparador'];
                $arrayParams['strBindParam']    = ':arrayEstadoEnlace';
                $arrayParams['arrayValue']      = $arrayParametros['arrayEnlace']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoEnlace', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoEnlace', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayEnlaceIni']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayEnlaceIni']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ieIni.estado ';
                $arrayParams['strComparador']   = $arrayParametros['arrayEnlaceIni']['strComparador'];
                $arrayParams['strBindParam']    = ':arrayEstadoEnlaceIni';
                $arrayParams['arrayValue']      = $arrayParametros['arrayEnlaceIni']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoEnlaceIni', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoEnlaceIni', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayInterfaceElemento']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayInterfaceElemento']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' iie.estado ';
                $arrayParams['strComparador']   = $arrayParametros['arrayInterfaceElemento']['strComparador'];
                $arrayParams['strBindParam']    = ':arrayEstadoInterfaceElemento';
                $arrayParams['arrayValue']      = $arrayParametros['arrayInterfaceElemento']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoInterfaceElemento', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoInterfaceElemento', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayServicio']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayServicio']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' isr.estado ';
                $arrayParams['strComparador']   = $arrayParametros['arrayServicio']['arrayEstadoComparador'];
                $arrayParams['strBindParam']    = ':arrayEstadoServicio';
                $arrayParams['arrayValue']      = $arrayParametros['arrayServicio']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoServicio', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoServicio', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayPunto']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayPunto']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ip.estado ';
                $arrayParams['strComparador']   = $arrayParametros['arrayPunto']['arrayEstadoComparador'];
                $arrayParams['strBindParam']    = ':arrayEstadoPunto';
                $arrayParams['arrayValue']      = $arrayParametros['arrayPunto']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoPunto', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoPunto', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayPunto']['arrayPunto'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayPunto']['arrayPunto']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ip.id ';
                $arrayParams['strComparador']   = $arrayParametros['arrayPunto']['strComparador'];
                $arrayParams['strBindParam']    = ':arrayPunto';
                $arrayParams['arrayValue']      = $arrayParametros['arrayPunto']['arrayPunto'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayPunto', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayPunto', $objReturnResponse->putTypeParamBind($arrayParams));
            }
            
            //Pregunta si $arrayParametros['arrayTipoEnlace']['arrayTipoEnlace'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayTipoEnlace']['arrayTipoEnlace']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ie.tipoEnlace ';
                $arrayParams['strComparador']   = $arrayParametros['arrayTipoEnlace']['strComparador'];
                $arrayParams['strBindParam']    = ':arrayTipoEnlace';
                $arrayParams['arrayValue']      = $arrayParametros['arrayTipoEnlace']['arrayTipoEnlace'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayTipoEnlace', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayTipoEnlace', $objReturnResponse->putTypeParamBind($arrayParams));
            }
            
            $objQuery->setDQL($strQuery . $strFromQuery);
            //Pregunta si $arrayParametros['intStart'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['intStart']))
            {
                $objQuery->setFirstResult($arrayParametros['intStart']);
            }
            //Pregunta si $arrayParametros['intLimit'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['intLimit']))
            {
                $objQuery->setMaxResults($arrayParametros['intLimit']);
            }
            $objReturnResponse->setRegistros($objQuery->getResult());
            $objReturnResponse->setTotal(0);
            if($objReturnResponse->getRegistros())
            {
                $strQueryCount = $strQueryCount . $strFromQuery;
                $objQueryCount->setDQL($strQueryCount);
                $objReturnResponse->setTotal($objQueryCount->getSingleScalarResult());
            }
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrMessageStatus('Existion un error en getHilosServicios - ' . $ex->getMessage());
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
        }
        return $objReturnResponse;
    } //getHilosServicios

    /*
     * getResultadoInterfacesByElementoTipo, obtiene las interfaces de un elemento segun su nombre, descripcion o estado.
     * 
     * @param array $arrayParametros[
     *                              intIdElemento                   Recibe el id del elemento.
     *                              arrayNombreInterfaceElemento => [
     *                                                              strComparador               => Simbolo con el que se quiere realizar la busqueda
     *                                                                                              (= o %, IN)
     *                                                              strNombreInterfaceElemento  => Recibe el nombre de la interface a buscar.
     *                                                              ]
     *                              arrayDescInterfaceElemento   => [
     *                                                              strComparador            => Simbolo con el que se quiere realizar la busqueda
     *                                                                                           (= o %, IN)
     *                                                              strDescInterfaceElemento => Recibe la descripcion de la interface a buscar.
     *                                                              ]
     *                              arrayEstado                  => [
     *                                                              strComparador   => Simbolo con el que se quiere realizar la busqueda
     *                                                                                 (= o %, IN)
     *                                                              strEstado       => Recibe descripcion del estado a buscar.
     *                                                              ]
     *                              intStart                     => Recibe el inicio para el resultado de la busqueda.
     *                              intLimit                     => Recibe el fin para el resultado de la busqueda del query.
     *                              ]
     * @return Object ReturnResponse Retorna un mensaje y estado de la consulta con sus datos y el numero de registros.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 17-04-2016
     * @since 1.0
     */
    public function getResultadoInterfacesByElementoTipo($arrayParametros)
    {
        $objReturnResponse = new ReturnResponse();
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        try
        {
            $objQueryCount = $this->_em->createQuery();
            $strQueryCount = "SELECT count(iie.id) ";
            $objQuery = $this->_em->createQuery();
            $strQuery = "SELECT iie.id intIdInterfaceElemento, "
                        . "iie.nombreInterfaceElemento strNombreInterfaceElemento, "
                        . "iie.descripcionInterfaceElemento strDescripcionInterfaceElemento, "
                        . "iie.estado strEstado ";

            $strFromQuery = "FROM schemaBundle:InfoInterfaceElemento iie "
                          . " WHERE 1 = 1 ";

            //Pregunta si $arrayParametros['intIdElemento'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['intIdElemento']))
            {
                $strFromQuery .= " AND iie.elementoId = :intIdElemento";
                $objQuery->setParameter('intIdElemento', $arrayParametros['intIdElemento']);
                $objQueryCount->setParameter('intIdElemento', $arrayParametros['intIdElemento']);
            }

            //Pregunta si $arrayParametros['strEstadoTipoRol'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayNombreInterfaceElemento']['strComparador']))
            {
                $strFromQuery .= " AND iie.nombreInterfaceElemento " . 
                              $arrayParametros['arrayNombreInterfaceElemento']['strComparador'] . " :strNombreInterfaceElemento";
                $objQuery->setParameter('strNombreInterfaceElemento', $arrayParametros['arrayNombreInterfaceElemento']['strNombreInterfaceElemento']);
                $objQueryCount->setParameter('strNombreInterfaceElemento', 
                                             $arrayParametros['arrayNombreInterfaceElemento']['strNombreInterfaceElemento']);
            }
            
            //Pregunta si $arrayParametros['arrayDescInterfaceElemento'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayDescInterfaceElemento']['strComparador']))
            {
                $strFromQuery .= " AND iie.descripcionInterfaceElemento " . 
                              $arrayParametros['arrayDescInterfaceElemento']['strComparador'] . " :strDescInterfaceElemento";
                $objQuery->setParameter('strDescInterfaceElemento', $arrayParametros['arrayDescInterfaceElemento']['strDescInterfaceElemento']);
                $objQueryCount->setParameter('strDescInterfaceElemento', 
                                             $arrayParametros['arrayDescInterfaceElemento']['strDescInterfaceElemento']);
            }

            //Pregunta si $arrayParametros['arrayDescInterfaceElemento'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayEstado']['strComparador']))
            {
                $strFromQuery .= " AND iie.estado " . $arrayParametros['arrayEstado']['strComparador'] . " :strEstado";
                $objQuery->setParameter('strEstado', $arrayParametros['arrayEstado']['strEstado']);
                $objQueryCount->setParameter('strEstado', $arrayParametros['arrayEstado']['strEstado']);
            }
            $objQuery->setDQL($strQuery . $strFromQuery);
            //Pregunta si $arrayParametros['intStart'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['intStart']))
            {
                $objQuery->setFirstResult($arrayParametros['intStart']);
            }
            //Pregunta si $arrayParametros['intLimit'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['intLimit']))
            {
                $objQuery->setMaxResults($arrayParametros['intLimit']);
            }
            $objReturnResponse->setRegistros($objQuery->getResult());
            $objReturnResponse->setTotal(0);
            if($objReturnResponse->getRegistros())
            {
                $strQueryCount = $strQueryCount . $strFromQuery;
                $objQueryCount->setDQL($strQueryCount);
                $objReturnResponse->setTotal($objQueryCount->getSingleScalarResult());
            }
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrMessageStatus('Existion un error en getResultadoContactoClienteByTipoRol - ' . $ex->getMessage());
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
        }
        return $objReturnResponse;
    }//getResultadoInterfacesByElementoTipo

    /**
     * getJSONInterfacesByElementoTipoInterface, restorna el resultado en json de la busqueda del metodo getResultadoInterfacesByElementoTipo
     * 
     * @param array $arrayParametros[
     *                              intIdElemento                   Recibe el id del elemento.
     *                              arrayNombreInterfaceElemento => [
     *                                                              strComparador               => Simbolo con el que se quiere realizar la busqueda
     *                                                                                              (= o %, IN)
     *                                                              strNombreInterfaceElemento  => Recibe el nombre de la interface a buscar.
     *                                                              ]
     *                              arrayDescInterfaceElemento   => [
     *                                                              strComparador            => Simbolo con el que se quiere realizar la busqueda
     *                                                                                           (= o %, IN)
     *                                                              strDescInterfaceElemento => Recibe la descripcion de la interface a buscar.
     *                                                              ]
     *                              arrayEstado                  => [
     *                                                              strComparador   => Simbolo con el que se quiere realizar la busqueda
     *                                                                                 (= o %, IN)
     *                                                              strEstado       => Recibe descripcion del estado a buscar.
     *                                                              ]
     *                              intStart                     => Recibe el inicio para el resultado de la busqueda.
     *                              intLimit                     => Recibe el fin para el resultado de la busqueda del query.
     *                              ]
     * 
     * @return json $jsonData Retorna la informacion en formato json
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 17-04-2016
     * @since 1.0
     */
    function getJSONInterfacesByElementoTipoInterface($arrayParametros)
    {
        $objReturnResponse = new ReturnResponse();
        try
        {
            $objGetResult = $this->getResultadoInterfacesByElementoTipo($arrayParametros);
            $jsonData = json_encode(array('strStatus'        => $objReturnResponse::PROCESS_SUCCESS, 
                                          'strMessageStatus' => $objReturnResponse::MSN_PROCESS_SUCCESS, 
                                          'total'            => $objGetResult->getTotal(), 
                                          'encontrados'      => $objGetResult->getRegistros()));
        }
        catch(\Exception $ex)
        {
            $jsonData = json_encode(array('strStatus' => $objReturnResponse::ERROR, 'strMessageStatus' => $objReturnResponse::MSN_ERROR .
                                          ' ' . $ex->getMessage()));
        }
        return $jsonData;
    } //getJSONInterfacesByElementoTipoInterface
    
    /**
     * Metoto que devuelve la Mac del Cpe del cliente relacionado a la interface
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 05-07-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - Se devuelve en resultado de consulta información del elemento
     * @since 16-09-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 - Se modifica para poder devolver la mac ligada al servicio de acuerdo a si este es pseudoPe o servicio normal
     * @since 18-11-2016
     * 
     * @param boolean $boolEsPseudoPe
     * @param integer $intIdServicio
     * @return arrayResultado
     */
    public function getMacCpePorServicioInterface($intIdServicio , $boolEsPseudoPe = false)
    {
        $arrayResultado = array();
        
        try
        {
            $rsm      = new ResultSetMappingBuilder($this->_em);	      
            $objQuery = $this->_em->createNativeQuery(null, $rsm);	    
            
            $strSelect= "";
            $strFrom  = "";
            $strWhere = "";
            
            if(!$boolEsPseudoPe)
            {
                $strSelect.= " , EN.TIPO_ENLACE ";
                $strFrom  .= " ,DB_INFRAESTRUCTURA.INFO_ENLACE EN ";
                $strWhere .= " AND EN.INTERFACE_ELEMENTO_FIN_ID = DI.INTERFACE_ELEMENTO_ID
                               AND EN.ESTADO                    = :estadoEnlace ";
                $objQuery->setParameter('estadoEnlace',  'Activo');
            }
               
            $strSql = "SELECT 
                        IE.ID_INTERFACE_ELEMENTO,
                        IE.ELEMENTO_ID,
                        IE.ESTADO,
                        IE.MAC_INTERFACE_ELEMENTO,
                        IE.NOMBRE_INTERFACE_ELEMENTO
                        $strSelect
                      FROM 
                        DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO IE,
                        DB_INFRAESTRUCTURA.INFO_DETALLE_INTERFACE DI
                        $strFrom
                      WHERE 
                          IE.ID_INTERFACE_ELEMENTO     = DI.INTERFACE_ELEMENTO_ID                      
                      AND IE.ESTADO                    = :estado
                      AND DI.DETALLE_NOMBRE            = :detalle
                      AND DI.DETALLE_VALOR             = :servicio
                      $strWhere
                    ";
                            
            $rsm->addScalarResult(strtoupper('ID_INTERFACE_ELEMENTO'),'idInterface','integer');                                   		             
            $rsm->addScalarResult(strtoupper('ESTADO'),'estado','string');			                         	                                                 
            $rsm->addScalarResult(strtoupper('MAC_INTERFACE_ELEMENTO'),'mac','string');	            
            $rsm->addScalarResult(strtoupper('NOMBRE_INTERFACE_ELEMENTO'),'nombreInterface','string');
            $rsm->addScalarResult(strtoupper('ELEMENTO_ID'),'elementoId','integer');
            $rsm->addScalarResult(strtoupper('TIPO_ENLACE'),'tipoEnlace','string');
                                          
            $objQuery->setParameter('servicio', $intIdServicio);  
            $objQuery->setParameter('estado',   'connected');        
            $objQuery->setParameter('detalle',  'servicio');                       

            $objQuery->setSQL($strSql);                   
            
            $arrayResultado = $objQuery->getOneOrNullResult();           
        } 
        catch (\Exception $ex) 
        {
            error_log($ex->getMessage());
        }
        
        return $arrayResultado;
    }

    /**
     * getMacCpeCambioTipoMedioInterface
     *
     * Método que obtiene la mac de un cpe que esta realizando un cambio de tipo medio.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0
     * @since 21-01-2018
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1
     * @since 14-05-2019 - Se considera el estado Eliminado en los enlaces
     *
     * @param array $arrayParametros
     * @return type
     */
    public function getMacCpeCambioTipoMedioInterface($arrayParametros)
    {
        $arrayResultado = array();

        try
        {
            $rsm      = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $rsm);
            $strSelect= "";
            $strFrom  = "";
            $strWhere = "";

            if(!$arrayParametros['boolEsPseudoPe'])
            {
                $strSelect.= " , EN.TIPO_ENLACE ";
                $strFrom  .= " ,DB_INFRAESTRUCTURA.INFO_ENLACE EN ";
                $strWhere .= " AND EN.INTERFACE_ELEMENTO_FIN_ID = DI.INTERFACE_ELEMENTO_ID
                               AND ( EN.ESTADO = :estadoEnlace OR EN.ESTADO = :estadoEnlace2 ) ";
                $objQuery->setParameter('estadoEnlace',  'Activo');
                $objQuery->setParameter('estadoEnlace2', 'Eliminado');
            }

            if($arrayParametros['strTipoOrden'] == 'C')
            {
                $strWhere .= " AND EN.TIPO_MEDIO_ID = :intIdTipoMedio ";
                $objQuery->setParameter('intIdTipoMedio',  $arrayParametros['intIdTipoMedio']);
            }
            $strSql = "SELECT
                        IE.ID_INTERFACE_ELEMENTO,
                        IE.ELEMENTO_ID,
                        IE.ESTADO,
                        IE.MAC_INTERFACE_ELEMENTO,
                        IE.NOMBRE_INTERFACE_ELEMENTO
                        $strSelect
                      FROM
                        DB_INFRAESTRUCTURA.INFO_INTERFACE_ELEMENTO IE,
                        DB_INFRAESTRUCTURA.INFO_DETALLE_INTERFACE DI
                        $strFrom
                      WHERE
                          IE.ID_INTERFACE_ELEMENTO     = DI.INTERFACE_ELEMENTO_ID
                      AND IE.ESTADO                    = :estado
                      AND DI.DETALLE_NOMBRE            = :detalle
                      AND DI.DETALLE_VALOR             = :servicio
                      $strWhere
                    ";

            $rsm->addScalarResult(strtoupper('ID_INTERFACE_ELEMENTO'),'idInterface','integer');
            $rsm->addScalarResult(strtoupper('ESTADO'),'estado','string');
            $rsm->addScalarResult(strtoupper('MAC_INTERFACE_ELEMENTO'),'mac','string');
            $rsm->addScalarResult(strtoupper('NOMBRE_INTERFACE_ELEMENTO'),'nombreInterface','string');
            $rsm->addScalarResult(strtoupper('ELEMENTO_ID'),'elementoId','integer');
            $rsm->addScalarResult(strtoupper('TIPO_ENLACE'),'tipoEnlace','string');

            $objQuery->setParameter('servicio', $arrayParametros['intIdServicio']);
            $objQuery->setParameter('estado',   'connected');
            $objQuery->setParameter('detalle',  'servicio');

            $objQuery->setSQL($strSql);
            $arrayResultado = $objQuery->getOneOrNullResult();
        }
        catch (\Exception $ex)
        {
            error_log('InfoInterfaceElementoRepository->getMacCpeCambioTipoMedioInterface()'.$ex->getMessage());
        }

        return $arrayResultado;
    }
}
