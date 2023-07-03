<?php

namespace telconet\comercialBundle\Service;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use telconet\schemaBundle\Entity\AdmiProdCaracComp;

/**
 * Documentación de la clase AdmiProductoService
 * 
 * @author Jose Bedon Sanchez <jobedon@telconet.ec>
 * @version 1.0
 *  
 */
class AdmiProductoService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emcom;


    private $serviceUtil;
    /**
     * Documentación para el método 'setDependencies'
     *
     * Método inicial que inyecta las dependencias usadas en el service 'AdmiProductoService'
     * 
     * @author Jose Bedon Sanchez <jobedon@telconet.ec>
     * @version 1.0 06-01-2020 - Se agrega parametro para obtenes entity manager de comercial
     *
     */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer) 
    {
        $this->emcom       = $objContainer->get('doctrine.orm.telconet_entity_manager');
        $this->serviceUtil = $objContainer->get('schema.Util');
    }

    /**
     * Documentación para el método obteberCaracteristicasProducto
     * 
     * Función que obtienes las caracteristicas de in Producto
     * 
     * @param array $arrayParametros [ 'idProducto' => 'Id del Producto' ]
     * 
     * @return array $arrayResponse [ 'idProducto'      => 'Id del Producto',
     *                                'nombreProducto'  => 'Nombre del Producto',
     *                                'nombreTecnico'   => 'Nombre Tecnico del Producto',
     *                                'caracteristicas' => 'Caracteristicas del producto',
     *                                'estado'          => 'Estado del Producto' ]
     * 
     * @author José Bedón Sánchez <jobedon@telconet.ec>
     * @version 1.0 06-01-2020 - Se obtiene caracteristicas del producto
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.1 12-11-2021 - Se agrega campos de la tabla característica Comportamiento.
     * 
     */
    public function obteberCaracteristicasProducto($arrayParametros)
    {
        try
        {
            $intIdProducto = $arrayParametros['idProducto'];

            $entityAdmiProducto = $this->emcom->getRepository('schemaBundle:AdmiProducto')
                                                ->find($intIdProducto);

            if (!$entityAdmiProducto)
            {
                throw new Exception('El producto no existe en el sistema.');
            }

            $arrayAdmiProductoCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                            ->findBy(array(
                                                                'estado'           => 'Activo',
                                                                'visibleComercial' => 'SI',
                                                                'productoId'       => $entityAdmiProducto
                                                            ));

            $strDescripcionCaracteristica = '';
            $arrayCaracteristicas = array();
            foreach ($arrayAdmiProductoCaracteristica as $entityAdmiProductoCaracteristica)
            {
                $strDescripcionCaracteristica = $entityAdmiProductoCaracteristica->getCaracteristicaId()->getDescripcionCaracteristica();
                $objAdmiProdCaracComp = $this->emcom->getRepository('schemaBundle:AdmiProdCaracComp')
                                                            ->findOneBy(array(
                                                                'productoCaracteristicaId' => $entityAdmiProductoCaracteristica->getId()
                                                            ));
                $arrayCaractComportamiento  =   array();
                $arrayValSel                =   array();
                $intVisible                 =   0;
                $intEditable                =   0;
                $strValoresSeleccionable    =   '';
                $strValorDefault            =   '';
                $strEstado                  =   '';
                
                if(is_object($objAdmiProdCaracComp))
                {
                    $intVisible                 =   $objAdmiProdCaracComp->getEsVisible();
                    $intEditable                =   $objAdmiProdCaracComp->getEditable();
                    $strValoresSeleccionable    =   $objAdmiProdCaracComp->getValoresSeleccionable();
                    $strValorDefault            =   $objAdmiProdCaracComp->getValoresDefault();
                    $strEstado                  =   $objAdmiProdCaracComp->getEstado();
                    if(!empty($strValoresSeleccionable))
                    {
                        
                        $arrayValSeleccionable = explode(';',$strValoresSeleccionable);
                        foreach($arrayValSeleccionable as $strListadoOpciones)
                        {
                            $arrayValSel[] = array('k'  => $strListadoOpciones,
                                                   'v'  => $strListadoOpciones);
                        }
                    }

                    $arrayCaractComportamiento  =   array(
                        'esVisible'               =>   $intVisible,
                        'editable'                =>   $intEditable,
                        'valoresSeleccionable'    =>   $arrayValSel,
                        'valorDefault'            =>   $strValorDefault,
                        'estado'                  =>   $strEstado
                    );
                }

                $entityAdmiParametroCab = $this->emcom->getRepository('schemaBundle:AdmiParametroCab')
                                                        ->findOneBy(array(
                                                            'estado'      => 'Activo',
                                                            'descripcion' => 'PROD_' . $strDescripcionCaracteristica
                                                        ));
                $arrayAdmiParametroDet = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->findBy(array(
                                                            'estado'      => 'Activo',
                                                            'parametroId' => $entityAdmiParametroCab
                                                        ));
                $arrayValores = array();
                foreach ($arrayAdmiParametroDet as $entityAdmiParametroDet)
                {
                    $arrayValores[] = $entityAdmiParametroDet->getValor1();
                }

                $arrayCaracteristica = array(
                    'nombre'  => $strDescripcionCaracteristica,
                    'valores' => $arrayValores,
                    'comportamiento' => $arrayCaractComportamiento
                );

                array_push($arrayCaracteristicas, $arrayCaracteristica);

            }

            $arrayResponse = array(
                'idProducto'      => $entityAdmiProducto->getId(),
                'nombreProducto'  => $entityAdmiProducto->getDescripcionProducto(),
                'nombreTecnico'   => $entityAdmiProducto->getNombreTecnico(),
                'caracteristicas' => $arrayCaracteristicas,
                'estado'          => $entityAdmiProducto->getEstado()
            );
        }
        catch (Exception $ex)
        {
            throw new Exception('Hubo un error en la consulta : '. $ex->getMessage());
        }

        return $arrayResponse;
    }

    /**
     * Documentación para el método 'actualizarCaracComportamiento'.
     *
     * Método que actualiza/guarda el comportamiento de las caracteristica de un producto.
     *
     * @param Array $arrayParametros Cadena Json con el listado de caracterisitica de un producto.
     *
     * @return Array Respuesta de ejecución del guardado de los entregables.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 20-05-2021
     */
    public function actualizarCaracComportamiento($arrayParametros)
    {
        $arrayCaractComp = json_decode($arrayParametros['objJsonCaractComp'], true);
        $this->emcom->getConnection()->beginTransaction();
        $arrayResultado['ESTADO'] = 'OK';
        try
        {
            foreach($arrayCaractComp as $arrayCaractComportamiento)
            {
                $intIdProdCaractComp = !empty($arrayCaractComportamiento['idProdCaracComp']) ? $arrayCaractComportamiento['idProdCaracComp'] : '0';

                if(!empty($intIdProdCaractComp) && $intIdProdCaractComp!= 0)
                {
                    $objProdCaracComp    = $this->emcom->getRepository('schemaBundle:AdmiProdCaracComp')
                                                ->findOneById($arrayCaractComportamiento['idProdCaracComp']);
                }
                $intIdProducto = null;
                if(empty($arrayCaractComportamiento['idProdCaracComp']) || 
                    (empty($objProdCaracComp) && !is_object($objProdCaracComp)) )
                {
                    $objProdCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                            ->findOneById($arrayCaractComportamiento['idProductoCaracteristica']);
                    $intIdProducto       = $objProdCaracteristica->getProductoId();
                    $intIdCaracteristica = $objProdCaracteristica->getCaracteristicaId();
                    $objProducto = $this->emcom->getRepository('schemaBundle:AdmiProducto')->find($intIdProducto);
                    $objCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')->find($intIdCaracteristica);
                    $strFuncionPrecio = $objProducto->getFuncionPrecio();
                    $strValoresDefault = $arrayCaractComportamiento['valoresDefault'];
                    if (strpos($strFuncionPrecio, $objCaracteristica->getDescripcionCaracteristica()) && 
                        empty($strValoresDefault) && strlen($strValoresDefault) < 1 )
                    {
                        throw new \Exception("Debe llenar el valor default de la característica " . 
                                             $objCaracteristica->getDescripcionCaracteristica(),206 );
                    }
                    $arrayProductoCaracteristica[$objCaracteristica->getDescripcionCaracteristica()] = $arrayCaractComportamiento['valoresDefault'];
                    $objProdCaracComp = new AdmiProdCaracComp();
                    $objProdCaracComp->setProductoCaracteristicaId($objProdCaracteristica);
                    $intVisible = !empty($arrayCaractComportamiento['esVisible']) ? $arrayCaractComportamiento['esVisible'] : 0;
                    $objProdCaracComp->setEsVisible($intVisible);
                    $intEditable = !empty($arrayCaractComportamiento['editable']) ? $arrayCaractComportamiento['editable'] : 0;
                    $objProdCaracComp->setEditable($intEditable);
                    $strValoresSeleccionable = !empty($arrayCaractComportamiento['valoresSeleccionable']) ?
                                                $arrayCaractComportamiento['valoresSeleccionable'] : '';
                    $objProdCaracComp->setValoresSeleccionable($strValoresSeleccionable);
                    $strValoresDefault = empty($strValoresDefault) && strlen($strValoresDefault) < 1 ?
                                         '' : $strValoresDefault;
                    $objProdCaracComp->setValoresDefault($strValoresDefault);
                    $objProdCaracComp->setEstado('Activo');
                    $objProdCaracComp->setFeCreacion(new \DateTime('now'));
                    $objProdCaracComp->setUsrCreacion($arrayParametros['strUsrCreacion']);
                }
                else
                {
                    $objProdCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                         ->findOneById($arrayCaractComportamiento['idProductoCaracteristica']);
                    $intIdProducto       = $objProdCaracComp->getProductoCaracteristicaId()->getProductoId();
                    $intIdCaracteristica = $objProdCaracComp->getProductoCaracteristicaId()->getCaracteristicaId();
                    $objProducto = $this->emcom->getRepository('schemaBundle:AdmiProducto')->find($intIdProducto);
                    $objCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')->find($intIdCaracteristica);
                    $strFuncionPrecio = $objProducto->getFuncionPrecio();
                    $strValoresDefault = $arrayCaractComportamiento['valoresDefault'];
                    if (strpos($strFuncionPrecio, $objCaracteristica->getDescripcionCaracteristica()) && 
                        empty($strValoresDefault) && strlen($strValoresDefault) < 1 )
                    {
                        throw new \Exception("Debe llenar el valor default de la característica " . 
                        $objCaracteristica->getDescripcionCaracteristica(),206 );
                    }
                    $arrayProductoCaracteristica[$objCaracteristica->getDescripcionCaracteristica()] = 
                    $arrayCaractComportamiento['valoresDefault'];    

                    $intVisible = !empty($arrayCaractComportamiento['esVisible']) ? $arrayCaractComportamiento['esVisible'] : 0;
                    $objProdCaracComp->setEsVisible($intVisible);
                    $intEditable = !empty($arrayCaractComportamiento['editable']) ? $arrayCaractComportamiento['editable'] : 0;
                    $objProdCaracComp->setEditable($intEditable);
                    $strValoresSeleccionable = !empty($arrayCaractComportamiento['valoresSeleccionable']) ?
                                                $arrayCaractComportamiento['valoresSeleccionable'] : '';
                    $objProdCaracComp->setValoresSeleccionable($strValoresSeleccionable);
                    $strValoresDefault = empty($strValoresDefault) && strlen($strValoresDefault) < 1 ?
                                         '' : $strValoresDefault;
                    $objProdCaracComp->setValoresDefault($strValoresDefault);
                    $objProdCaracComp->setEstado($arrayCaractComportamiento['estado']);
                    $objProdCaracComp->setFeUltMod(new \DateTime('now'));
                    $objProdCaracComp->setUsrUltMod($arrayParametros['strUsrCreacion']);
                }

                $this->emcom->persist($objProdCaracComp);
            }

            if ($this->evaluarFuncionPrecio($strFuncionPrecio, $arrayProductoCaracteristica) === 0)
            {
                throw new \Exception("No se pudo evaluar la funcion precio",206 );
            }
            $this->emcom->flush();
            $this->emcom->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
            }
            $this->emcom->getConnection()->close();
            error_log('ACTUALIZAR CARACTERISTICA COMPORTAMIENTO_ERROR: ' . $e->getMessage());
            $arrayResultado['ESTADO'] = 'ERROR';
            $arrayResultado['ERROR']  = "No se pudieron crear los comportamiento de las caracteristica, por favor".
                                        " verificar con el departamento de sistemas";
            if ($e->getCode() === 206)
            {
                $arrayResultado['ERROR']  = $e->getMessage();  
            }
            $arrayParametrosLog['enterpriseCode']   = $arrayParametros['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $e->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayParametros, 128);
            $arrayParametrosLog['creationUser']     = $arrayContrato['strUsrCreacion'];

            $this->serviceUtil->insertLog($arrayParametrosLog);            
        }

        return $arrayResultado;
    }

    /**
     * Documentación para el método 'actualizarCaracComportamiento'.
     *
     * Método que actualiza/guarda el comportamiento de las caracteristica de un producto.
     *
     * @param Array $arrayParametros Cadena Json con el listado de caracterisitica de un producto.
     *
     * @return Array Respuesta de ejecución del guardado de los entregables.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 20-05-2021
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 17-08-2022 se Agrega validacion para que no se envie netvoice cuando sea un AS
     */
    public function getProductosAdicionales($arrayParametros)
    {
        $arrayRespuesta = array();
        $arrayParamProd    = array('strEstado'          => 'Activo',
                                   'strNombreTecnico'   => 'FINANCIERO',
                                   'strEsConcentrador'  => 'SI',
                                   'strCodEmpresa'      => $arrayParametros['strCodEmpresa']);
        error_log("llego 2");
        $arrayAdmiProducto = $this->emcom->getRepository('schemaBundle:AdmiProducto')
                                         ->getProductosAdicionales($arrayParamProd);
        error_log("llego 3");                                 
        foreach($arrayAdmiProducto as $arrayAdmiProdAdicional)
        {
            
            $boolEnvia = false; 
            $arrayCaracteristicaProd = array();
            $arrayCompCaract         = array();
            $arrayParamCaracProd = array('productoId'   => $arrayAdmiProdAdicional['idProducto'],
                                         'estado'       => $arrayAdmiProdAdicional['estado']);
            $arrayCaractProd     = $this->emcom->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                               ->findProdAdicCaracteristica($arrayParamCaracProd);
            $strEditable = $arrayAdmiProdAdicional['requierePlanificacion'] == "SI" ? "NO" : "SI";

            foreach($arrayCaractProd as $arrayCaract)
            {
                $arrayCompCaract          = array();
                $objProdCarac              = $this->emcom->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                         ->findOneById($arrayCaract['idProductoCaracteristica']);

                $objCompCaractProd         = $this->emcom->getRepository('schemaBundle:AdmiProdCaracComp')
                                                         ->findOneBy(array('productoCaracteristicaId'   => $objProdCarac,
                                                                           'estado'                     => $arrayAdmiProdAdicional['estado']));


                if(is_object($objCompCaractProd))
                {
                    $boolSeleccionable = false;
                    $arrayComCaraProd  = array('k'   => 'VISIBLE',
                                               'v'   => ($objCompCaractProd->getEsVisible() === 0) ? 'NO' : 'SI');
                    $arrayCompCaract[] = $arrayComCaraProd;

                    $arrayComCaraProd  = array('k'   => 'EDITABLE',
                                               'v'   => ($objCompCaractProd->getEditable() === 0) ? 'NO' : "SI");
                    $arrayCompCaract[] = $arrayComCaraProd;

                    $strValorDefault   = $objCompCaractProd->getValoresDefault();
                    $arrayComCaraProd  = array('k'   => 'POR_DEFECTO');
                    if(!empty($strValorDefault))
                    {
                        $arrayComCaraProd['v'] = $strValorDefault;
                    }
                    $arrayCompCaract[] = $arrayComCaraProd;

                    $strValorSeleccion = $objCompCaractProd->getValoresSeleccionable();
                    $arrayComCaraProd  = array('k'   => 'OPCIONES');
                    if(!empty($strValorSeleccion))
                    {
                        $boolSeleccionable     = true;
                        $arrayValSel           = array();
                        $arrayValSeleccionable = explode(';',$strValorSeleccion);
                        foreach($arrayValSeleccionable as $strListadoOpciones)
                        {
                            $arrayValSel[] = array('k'  => $strListadoOpciones,
                                                   'v'  => $strListadoOpciones);
                        }
                        $arrayComCaraProd['items'] = $arrayValSel;
                    }
                    else
                    {
                        $arrayComCaraProd['items'] = array();
                    }
                    $arrayCompCaract[] = $arrayComCaraProd;


                    $arrayComCaraProd  = array('k'   => 'TIPO_ENTRADA');
                    $objCaracteristica = $objCompCaractProd->getProductoCaracteristicaId()->getCaracteristicaId();
                    if(is_object($objCaracteristica))
                    {
                        if ($arrayCaract['descripcionCaracteristica'] === 'VISUALIZAR_EN_MOVIL' &&
                            $objCompCaractProd->getEsVisible() === 1)
                        {
                            $boolEnvia = true;
                        }    
                        if($arrayCaract['descripcionCaracteristica'] === 'CORREO ELECTRONICO')
                        {
                            $arrayComCaraProd['v'] = 'EMAIL';
                        }
                        else if($objCaracteristica->getTipoIngreso() === 'S')
                        {
                            $arrayComCaraProd['v'] = 'SELECCIONABLE';
                        }
                        else
                        {
                            $arrayComCaraProd['v'] = ($objCaracteristica->getTipoIngreso() === 'N') ? 'NUMERO' : 'TEXTO';
                        }
                    }
                    else
                    {
                        $arrayComCaraProd['v'] = '';
                    }
                    $arrayCompCaract[] = $arrayComCaraProd;
                }
                if ($arrayCaract['descripcionCaracteristica'] !== 'VISUALIZAR_EN_MOVIL')
                {
                    $arrayCaracteristicaProd[] = array('k'      => $arrayCaract['idProductoCaracteristica'],
                                                       'v'      => $arrayCaract['descripcionCaracteristica'],
                                                       'items'  => $arrayCompCaract);                    
                }
                //valido si es netvoice y es AS no se envie el producto
                $objAdendums = $this->emcom->getRepository('schemaBundle:InfoAdendum')->findBy(array("puntoId" => $arrayParametros['intIdPunto']));
                if ($objAdendums)
                {
                    foreach ($objAdendums as $objAdendum)
                    {
                        if ($arrayAdmiProdAdicional['descrpcionProducto'] == "NETVOICE" && in_array($objAdendum->getTipo(), array("C", "AP")))
                        {
                            $boolEnvia = false;
                            break;
                        }
                    }
                }
            }
            if ($boolEnvia)
            {   
                $arrayValores = array('k' => $arrayAdmiProdAdicional['idProducto'],
                                      'v' => $arrayAdmiProdAdicional['descripcionProducto'],
                                      'f' => !empty($arrayAdmiProdAdicional['funcionPrecio']) ? $arrayAdmiProdAdicional['funcionPrecio'] : '',
                                      't' => $arrayAdmiProdAdicional['nombreTecnico'],
                                      'i' => $arrayAdmiProdAdicional['porcentajeImpuesto'],
                                      'g' => !empty($arrayAdmiProdAdicional['grupo']) ? $arrayAdmiProdAdicional['grupo'] : '',
                                      'r' => !empty($arrayAdmiProdAdicional['frecuencia']) ? $arrayAdmiProdAdicional['frecuencia'] : null,
                                      'c' => $arrayCaracteristicaProd,
                                      'q' => $strEditable);
                $arrayProdAdicionales[] = $arrayValores;

            }
        }
        $arrayRespuesta['data'] = $arrayProdAdicionales;
        return $arrayRespuesta;
    }

    /**
     * evaluarFuncionPrecio, Evalua la funcion de precio en base a unos parametros dados y retorna el precio
     * 
     * Nota: En esta función se usa eval() el cual es muy peligroso porque permite la ejecución de 
     * código de PHP arbitrario. Se debe validar correctamente la información ingresada 
     * directamente por el usuario antes de ser usada por eval. Copy of InfoContratoDigitalService
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 16-07-2021
     * 
     */
    private function evaluarFuncionPrecio($strFuncionPrecio, $arrayProductoCaracteristicasValores)
    {
        $floatPrecio        = 0;        
        $arrayFunctionJs    = array('Math.ceil','Math.floor','Math.pow',"}");
        $arrayFunctionPhp   = array('ceil','floor','pow',';}');
        $strFuncionPrecio   = str_replace($arrayFunctionJs, $arrayFunctionPhp, $strFuncionPrecio);
        $strFuncionPrecio   = str_replace('"[', '[', $strFuncionPrecio);
        $strFuncionPrecio   = str_replace(']"', ']', $strFuncionPrecio);
        foreach($arrayProductoCaracteristicasValores as $strClave => $strValor)
        {
            $strFuncionPrecio = str_replace("[" . $strClave . "]", '"'. $strValor . '"', $strFuncionPrecio);
        }
        $strFuncionPrecio      = str_replace('PRECIO', '$floatPrecio', $strFuncionPrecio);
        $strDigitoVerificacion = substr($strFuncionPrecio, -1, 1);
        if(is_numeric($strDigitoVerificacion))
        {
            $strFuncionPrecio = $strFuncionPrecio . ";";
        }
        
        
        eval($strFuncionPrecio);
        return $floatPrecio;
    }
    
}
