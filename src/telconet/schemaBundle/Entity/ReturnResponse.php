<?php

namespace telconet\schemaBundle\Entity;

/**
 * ReturnResponse, Clase que tiene como finalidad crear un objeto para retornarlo en las peticiones Ajax desde los controladores
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 16-09-2015
 * 
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.1 05-04-2016 Se aumentaron atributos a la clase.
 * @since 1.0
 */
class ReturnResponse
{

    //Contiene el código cuando el proceso se realizó con éxito.
    const PROCESS_SUCCESS = "100";
    const MSN_PROCESS_SUCCESS = "Se realizo el proceso con exito.";
    //Contiene el código cuando no se obtuvo resultado en el proceso
    const NOT_RESULT = "000";
    const MSN_NOT_RESULT = "No se obtuvo resultado.";
    //Contiene el código de error.
    const ERROR = "001";
    const MSN_ERROR = "Existio un error.";
    
    const ERROR_TRANSACTION = "300";
    const MSN_ERROR_TRANSACTION = "No se completó la transacción.";

    //Parametro define el estatus del proceso.
    public $strStatus;
    //Parametro que define el mensaje del proceso
    public $strMessageStatus;
    public $registros;
    public $total = 0;
    //Variable usada para almacenar los items
    public $arrayList = array();

    /**
     * Retorna un mensaje
     */
    public function getStrMessageStatus()
    {
        return $this->strMessageStatus;
    }

    /**
     * Setea un mensaje
     */
    public function setStrMessageStatus($strMessageStatus)
    {
        $this->strMessageStatus = $strMessageStatus;
    }

    /**
     * Retorna un estatus
     */
    public function getStrStatus()
    {
        return $this->strStatus;
    }

    /**
     * Setea un estatus
     */
    public function setStrStatus($strStatus)
    {
        $this->strStatus = $strStatus;
    }

    public function getRegistros()
    {
        return $this->registros;
    }

    public function setRegistros($registros)
    {
        $this->registros = $registros;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * add, agrega un item al array
     * @param Object $objItem Item que se quiere crear
     * @param String $key     key del item enviado a crear
     */
    public function add($objItem, $key = null)
    {
        //Crea el registro sin key
        if($key == null)
        {
            $this->arrayList[] = $objItem;
        }
        else //Crea el registro con key
        {
            $this->arrayList[$key] = $objItem;
        }
    }

    /**
     * toArray, obtiene el contenido del array
     * @return array Retorna el array almacenado en arrayList
     */
    public function toArray()
    {
        return $this->arrayList;
    }

    /**
     * size, obtiene el numero de elementos que tenga el array
     * @return int Retorna el conteo del array
     */
    public function size()
    {
        return count($this->arrayList);
    }

    /**
     * remove, remueve un elemento del array segun el key
     * @param String $key Recibe el key del elemento
     */
    public function remove($key)
    {
        if(array_key_exists($key, $this->arrayList))
        {
            unset($this->arrayList[$key]);
        }
    }
    
    /**
     * remove, remueve un elemento del array segun el key
     * @param String $key Recibe el key del elemento
     */
    public function removeInArray($key, $array)
    {
        if(array_key_exists($key, $array))
        {
            unset($array[$key]);
        }
        return $array;
    }

    /**
     * getObj, obtiene el contenido del elemento del array segun el key
     * @param String $key Recibe el key del elemento
     * @return Object Puede retornar un objecto si es encontrado
     */
    public function getObj($key)
    {
        //retorna el dato del array segun el key
        if(array_key_exists($key, $this->arrayList))
        {
            return $this->arrayList[$key];
        }
        else
        {
            return NULL;
        }
    }

    /**
     * keys, retorna los keys del array
     * @return array Retorna el nombre de la definicion de los elementos
     */
    public function keys()
    {
        return array_keys($this->arrayList);
    }

    /**
     * keyExists, verifica si un elemento del array existe
     * @param String $key Key con el cual se envia a buscar
     * @return int retorna 1 si el elemento existe 0 cuando no existe.
     */
    public function keyExists($key)
    {
        if(isset($this->arrayList[$key]))
        {
            return 1;
        }
        return 0;
    }

    /**
     * putParenthesis, retorna un array con los parentesis para la comparacion IN
     * @param String $strComparador Recibe el comparador de la clausula
     * @return array                Retorna un array segun la validacion
     */
    public function putParenthesis($strComparador)
    {
        if("IN" === trim($strComparador) || "NOT IN" === trim($strComparador))
        {
            return ["(", ")"];
        }
        return ["", ""];
    }

    /**
     * putWhereClause, se ecarga de concatenar las variables para formar la clausula en el where
     * @param string $arrayParams[
     *                           'strOperator'      => Recibe el operador para la clausula ej: AND, OR
     *                           'strComparador'    => Recibe el comparador para la clausula ej: =, LIKE, IN
     *                           'strBindParam'     => Recibe el nombre del parametro de enlace ej: :variableBindEjemplo
     *                           'strField'         => Recibe el campo de comparacion
     *                           ]
     * @return string $strWhereClause Retorna la clausula formada.
     */
    public function putWhereClause($arrayParams)
    {
        $strWhereClause = "";
        if(empty($arrayParams['strOperator']))
        {
            $arrayParams['strOperator'] = " AND ";
        }
        if(empty($arrayParams['strComparador']))
        {
            $arrayParams['strComparador'] = " = ";
        }
        if(!empty($arrayParams['strOperator']) && !empty($arrayParams['strComparador']) && !empty($arrayParams['strBindParam']))
        {
            $arrayParenthesis = $this->putParenthesis($arrayParams['strComparador']);
            $strWhereClause = $arrayParams['strOperator'] . " "
                . $arrayParams['strField'] . " "
                . $arrayParams['strComparador'] . " "
                . $arrayParenthesis[0] . " "
                . $arrayParams['strBindParam'] . " "
                . $arrayParenthesis[1];
        }
        return $strWhereClause;
    }

    /**
     * putTypeParamBind, retorna un array cuando el comparador sea IN caso contrario retornara un string.
     * @param type $arrayParams[
     *                          'strComparador' => Recibe el comparador ejemplo: IN, LIKE o =
     *                          'arrayValue'    => Recibe un array con el valor para el enlace de variable.
     *                          ]
     * @return mixed Puede retorna un array o un String
     */
    public function putTypeParamBind($arrayParams)
    {
        if("IN" === trim($arrayParams['strComparador']) || "NOT IN" === trim($arrayParams['strComparador']))
        {
            return $arrayParams['arrayValue'];
        }
        return $arrayParams['arrayValue'][0];
    }

    function emptyArray($mixed)
    {
        if(is_array($mixed))
        {
            foreach($mixed as $value)
            {
                if(!$this->emptyArray($value))
                {
                    return false;
                }
            }
        }
        elseif(!empty($mixed))
        {
            return false;
        }
        return true;
    }

}
