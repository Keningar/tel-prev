<?php

namespace telconet\schemaBundle\Service;

/**
 * Clase para hacer llamadas a web services REST.
 * Requiere que la extension CURL este habilitada en php.ini.
 * @see http://edyluisrey.tumblr.com/post/36824495059/consumiendo-datos-desde-rest-con-curl-en-php
 * @see http://www.lornajane.net/posts/2011/posting-json-data-with-php-curl
 * @see http://www.php.net/curl_setopt
 * @see http://www.php.net/manual/en/curl.constants.php
 * @see http://altafphp.blogspot.com/2012/12/difference-between-curloptconnecttimeou.html
 */
class RestClientService {
    
    /**
     * 
     * @version Inicial
     * 
     * @author Modificado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 10-08-2016   Se adiciona a este método el customrequest GET
     * 
     * 
     * Consume datos desde un URL mediante GET
     * @param string $url la direccion url de la api
     * @param array $options a agregar mediante curl_setopt
     * @return array con tres valores:
     * result (mensaje de respuesta o false en caso de error),
     * status (codigo de status HTTP, 200 si es correcto, 404 si no se encuentra, 500 si hay error, etc.),
     * error (ultimo mensaje de error o '')
     */
    public function get($url, array $options = array())
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        // que se devuelva el resultado de la ejecucion
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // default connect timeout de 15 segundos
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
        // default execution timeout de 30 segundos (incluye el connect timeout)
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // definir opciones adicionales
        foreach ($options as $option => $value)
        {
            curl_setopt($curl, $option, $value);
        }
        // ejecutar, obtener error si lo hay, cerrar sesion
        $result = curl_exec($curl);
        $error = curl_error($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $array = array('result' => $result, 'status' => $http_status, 'error' => $error);
        return $array;
    }

    /**
     * Envió de datos mediante DELETE a una URL REST
     *
     * @param string $strUrlFinal la direccion url de la api
     * @param array $arrayOptions a agregar mediante curl_setopt
     * @return array con tres valores:
     * result (mensaje de respuesta o false en caso de error),
     * status (codigo de status HTTP, 200 si es correcto, 404 si no se encuentra, 500 si hay error, etc.),
     * error (ultimo mensaje de error o '')
     * @version 1.0 11-11-2019
     *
     * @author Marlon Plúas <mpluas@telconet.ec>
     */
    public function delete($strUrlFinal, array $arrayOptions = array())
    {
        $objCurl = curl_init();
        curl_setopt($objCurl, CURLOPT_URL, $strUrlFinal);
        curl_setopt($objCurl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($objCurl, CURLOPT_HEADER, 0);
        curl_setopt($objCurl, CURLOPT_FOLLOWLOCATION, true);
        // que se devuelva el resultado de la ejecucion
        curl_setopt($objCurl, CURLOPT_RETURNTRANSFER, true);
        // default connect timeout de 15 segundos
        curl_setopt($objCurl, CURLOPT_CONNECTTIMEOUT, 15);
        // default execution timeout de 30 segundos (incluye el connect timeout)
        curl_setopt($objCurl, CURLOPT_TIMEOUT, 30);
        curl_setopt($objCurl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // definir opciones adicionales
        foreach ($arrayOptions as $option => $value)
        {
            curl_setopt($objCurl, $option, $value);
        }
        // ejecutar, obtener error si lo hay, cerrar sesion
        $strResult = curl_exec($objCurl);
        $strError = curl_error($objCurl);
        $strHttpStatus = curl_getinfo($objCurl, CURLINFO_HTTP_CODE);
        curl_close($objCurl);
        $arrayResponse = array('result' => $strResult, 'status' => $strHttpStatus, 'error' => $strError);
        return $arrayResponse;
    }

    /**
     * Envio de datos mediante POST a una URL REST
     * @param string $strUrlFinal la direccion url de la api
     * @param string $strData data que se envia
     * @param array $arrayOptions a agregar mediante curl_setopt
     * @return array con tres valores:
     * result (mensaje de respuesta o false en caso de error),
     * status (codigo de status HTTP, 200 si es correcto, 404 si no se encuentra, 500 si hay error, etc.),
     * error (ultimo mensaje de error o '')
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 20-12-2018     Se aumenta el parámetro timeout según lo solicitado por Devops para solventar problemas de consumo de WS 
     *                             middleware RDA en el cual un proceso demora casi 5 minutos
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 16-11-2021 Se modifica función para permitir nuevos valores de connecttimeout y timeout al consumir un web service 
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 09-12-2021 Se agregan comillas en los índices del arreglo $arrayOptions, puesto que da problema al ejecutar un post 
     *                         desde el comand de php por un NOTICE
     * 
     * @since 1.0
     */
    private function post($strUrlFinal, $strData, array $arrayOptions = array())
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $strUrlFinal);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $strData);
        // que se devuelva el resultado de la ejecucion
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if(isset($arrayOptions['CURLOPT_CONNECTTIMEOUT_CUSTOM']) && !empty($arrayOptions['CURLOPT_CONNECTTIMEOUT_CUSTOM']))
        {
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $arrayOptions['CURLOPT_CONNECTTIMEOUT_CUSTOM']);
            unset($arrayOptions['CURLOPT_CONNECTTIMEOUT_CUSTOM']);
        }
        else
        {
            // default connect timeout de 30 segundos
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        }
        
        if(isset($arrayOptions['CURLOPT_TIMEOUT_CUSTOM']) && !empty($arrayOptions['CURLOPT_TIMEOUT_CUSTOM']))
        {
            curl_setopt($curl, CURLOPT_TIMEOUT, $arrayOptions['CURLOPT_TIMEOUT_CUSTOM']);
            unset($arrayOptions['CURLOPT_TIMEOUT_CUSTOM']);
        }
        else
        {
            // default execution timeout de 600 segundos (incluye el connect timeout)
            curl_setopt($curl, CURLOPT_TIMEOUT, 600);
        }
        
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // definir opciones adicionales
        foreach ($arrayOptions as $option => $value)
        {
            curl_setopt($curl, $option, $value);
        }
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        // ejecutar, obtener error si lo hay, cerrar sesion
        $result = curl_exec($curl);
        $error = curl_error($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $array = array('result' => $result, 'status' => $http_status, 'error' => $error);
        return $array;
    }
	
	/**
	 * Envio de datos JSON mediante POST a una URL REST
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019 Se agrega lógica para agregar valores a la cabecera HEADER
	 *
	 * @param array $arrayOptions a agregar mediante curl_setopt
	 * @param string $strUrl      la direccion url de la api
	 * @param string $strData     data que se envia
	 *
	 * @return array con tres valores:
	 * result (mensaje de respuesta o false en caso de error),
	 * status (codigo de status HTTP, 200 si es correcto, 404 si no se encuentra, 500 si hay error, etc.),
	 * error (ultimo mensaje de error o '')
	 *
	 */
	public function postJSON($strUrl, $strData, array $arrayOptions = array())
	{
		// Guardo el array headers que retorna el método
		$arrayPreOptionsHeaders = $arrayOptions[CURLOPT_HTTPHEADER] ? $arrayOptions[CURLOPT_HTTPHEADER] : array();
		
		$arrayOptions[CURLOPT_HTTPHEADER] = array('Content-Type: application/json',
		                                          'Content-Length: '.strlen($strData));
		
		// Agrego los headers adiciones si existen
		foreach($arrayPreOptionsHeaders as $value)
		{
			array_push($arrayOptions[CURLOPT_HTTPHEADER], $value);
		}
		
		return $this->post($strUrl, $strData, $arrayOptions);
	}
    
    /**
	 * Envio de datos JSON mediante GET a una URL REST
	 *
	 * @author  Ivan Romero <icromeros@telconet.ec>
	 * @version 1.0 25-12-2021 Se agrega lógica para agregar valores a la cabecera HEADER
	 *
	 * @param array $arrayOptions a agregar mediante curl_setopt
	 * @param string $strUrl      la direccion url de la api
	 *
	 * @return array con tres valores:
	 * result (mensaje de respuesta o false en caso de error),
	 * status (codigo de status HTTP, 200 si es correcto, 404 si no se encuentra, 500 si hay error, etc.),
	 * error (ultimo mensaje de error o '')
	 *
	 */
	public function getJSON($strUrl,  array $arrayOptions = array())
	{
		// Guardo el array headers que retorna el método
		$arrayPreOptionsHeaders = $arrayOptions[CURLOPT_HTTPHEADER] ? $arrayOptions[CURLOPT_HTTPHEADER] : array();
		
		$arrayOptions[CURLOPT_HTTPHEADER] = array('Content-Type: application/json');
		
		// Agrego los headers adiciones si existen
		foreach($arrayPreOptionsHeaders as $value)
		{
			array_push($arrayOptions[CURLOPT_HTTPHEADER], $value);
		}
		
		return $this->get($strUrl, $arrayOptions);
	}

    /**
	 * Envio de datos JSON mediante DELETE a una URL REST
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 25-12-2021 Se agrega lógica para agregar valores a la cabecera HEADER
	 *
	 * @param array $arrayOptions a agregar mediante curl_setopt
	 * @param string $strUrl      la direccion url de la api
	 *
	 * @return array con tres valores:
	 * result (mensaje de respuesta o false en caso de error),
	 * status (codigo de status HTTP, 200 si es correcto, 404 si no se encuentra, 500 si hay error, etc.),
	 * error (ultimo mensaje de error o '')
	 *
	 */
	public function deleteJSON($strUrl,  array $arrayOptions = array())
	{
		// Guardo el array headers que retorna el método
		$arrayPreOptionsHeaders = $arrayOptions[CURLOPT_HTTPHEADER] ? $arrayOptions[CURLOPT_HTTPHEADER] : array();
		
		$arrayOptions[CURLOPT_HTTPHEADER] = array('Content-Type: application/json');
		
		// Agrego los headers adiciones si existen
		foreach($arrayPreOptionsHeaders as $value)
		{
			array_push($arrayOptions[CURLOPT_HTTPHEADER], $value);
		}
		
		return $this->delete($strUrl, $arrayOptions);
	}
    /**
     * Envio de datos JSON mediante POST a una URL REST
     * @param string $strUrl la direccion url de la api
     * @param string $strData data que se envia
     * @param array $arrayOptions a agregar mediante curl_setopt
     * @return array con tres valores:
     * result (mensaje de respuesta o false en caso de error),
     * status (codigo de status HTTP, 200 si es correcto, 404 si no se encuentra, 500 si hay error, etc.),
     * error (ultimo mensaje de error o '')
     */
    public function postJSONToken($strUrl, $strData, $strToken, array $arrayOptions = array())
    {
        if($strToken)
        {
            $arrayOptions[CURLOPT_HTTPHEADER] = array(
                'Content-Type: application/json',
                'Authorization: '.$strToken,
                'Content-Length: ' . strlen($strData)
            );
        }
        else
        {
            $arrayOptions[CURLOPT_HTTPHEADER] = array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($strData)
            );
        }

        return $this->post($strUrl, $strData, $arrayOptions);
    }

    /**
     * Envio de datos XML mediante POST a una URL REST
     * @param string $url la direccion url de la api
     * @param string $data_string data que se envia
     * @param array $options a agregar mediante curl_setopt
     * @return array con tres valores:
     * result (mensaje de respuesta o false en caso de error),
     * status (codigo de status HTTP, 200 si es correcto, 404 si no se encuentra, 500 si hay error, etc.)
     * error (ultimo mensaje de error o '')
     */
    public function postXML($url, $data_string, array $options = array())
    {
        $options[CURLOPT_HTTPHEADER] = array(
                        'Content-Type: application/xml',
                        'Content-Length: ' . strlen($data_string)
        );
        return $this->post($url, $data_string, $options);
    }
    
    /**
     * Envio de datos mediante PUT a una URL REST
     * @param string $strUrl la direccion url de la api
     * @param string $strData_string data que se envia
     * @param array $arrayOptions a agregar mediante curl_setopt
     * @return array con tres valores:
     * result (mensaje de respuesta o false en caso de error),
     * status (codigo de status HTTP, 200 si es correcto, 404 si no se encuentra, 500 si hay error, etc.),
     * error (ultimo mensaje de error o '')
     * 
     * @author: Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 09/05/2019
     */
    private function put($strUrl, $strData, array $arrayOptions = array())
    {
        $intCurl = curl_init();
        curl_setopt($intCurl, CURLOPT_URL, $strUrl);
        curl_setopt($intCurl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($intCurl, CURLOPT_POSTFIELDS, $strData);
        // que se devuelva el resultado de la ejecucion
        curl_setopt($intCurl, CURLOPT_RETURNTRANSFER, true);
        // default connect timeout de 30 segundos
        curl_setopt($intCurl, CURLOPT_CONNECTTIMEOUT, 30);
        // default execution timeout de 240 segundos (incluye el connect timeout)
        curl_setopt($intCurl, CURLOPT_TIMEOUT, 600);
        curl_setopt($intCurl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // definir opciones adicionales
        foreach ($arrayOptions as $arrayOption => $strValue)
        {
            curl_setopt($intCurl, $arrayOption, $strValue);
        }
        // ejecutar, obtener error si lo hay, cerrar sesion
        $strResult = curl_exec($intCurl);
        $strError  = curl_error($intCurl);
        $strHttpStatus = curl_getinfo($intCurl, CURLINFO_HTTP_CODE);
        curl_close($intCurl);
        $arrayReturn = array('result' => $strResult, 'status' => $strHttpStatus, 'error' => $strError);
        return $arrayReturn;
    }

    /**
     * Envió de datos mediante PATCH a una URL REST
     *
     * @author Marlon Plúas <mpluas@telconet.ec>
     * @version 1.0 11-11-2019
     *
     * @param string $strUrl la direccion url de la api
     * @param string $strData_string data que se envia
     * @param array $arrayOptions a agregar mediante curl_setopt
     * @return array con tres valores:
     * result (mensaje de respuesta o false en caso de error),
     * status (codigo de status HTTP, 200 si es correcto, 404 si no se encuentra, 500 si hay error, etc.),
     * error (ultimo mensaje de error o '')
     */
    private function patch($strUrl, $strData, array $arrayOptions = array())
    {
        $intCurl = curl_init();
        curl_setopt($intCurl, CURLOPT_URL, $strUrl);
        curl_setopt($intCurl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($intCurl, CURLOPT_POSTFIELDS, $strData);
        // que se devuelva el resultado de la ejecucion
        curl_setopt($intCurl, CURLOPT_RETURNTRANSFER, true);
        // default connect timeout de 30 segundos
        curl_setopt($intCurl, CURLOPT_CONNECTTIMEOUT, 30);
        // default execution timeout de 240 segundos (incluye el connect timeout)
        curl_setopt($intCurl, CURLOPT_TIMEOUT, 600);
        curl_setopt($intCurl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // definir opciones adicionales
        foreach ($arrayOptions as $arrayOption => $strValue)
        {
            curl_setopt($intCurl, $arrayOption, $strValue);
        }
        // ejecutar, obtener error si lo hay, cerrar sesion
        $strResult = curl_exec($intCurl);
        $strError  = curl_error($intCurl);
        $strHttpStatus = curl_getinfo($intCurl, CURLINFO_HTTP_CODE);
        curl_close($intCurl);
        $arrayReturn = array('result' => $strResult, 'status' => $strHttpStatus, 'error' => $strError);
        return $arrayReturn;
    }

    /**
     * Envio de datos FORMURLENCODED mediante PUT a una URL REST
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 19-03-2019
     * @param string $strUrl la direccion url de la api
     * @param string $strData data que se envia
     * @param array $arrayOptions a agregar mediante curl_setopt
     * @return array con tres valores:
     * result (mensaje de respuesta o false en caso de error),
     * status (codigo de status HTTP, 200 si es correcto, 404 si no se encuentra, 500 si hay error, etc.),
     * error (ultimo mensaje de error o '')
     */
    public function putFormURLEncoded($strUrl, $strData, array $arrayOptions = array())
    {
        $arrayOptions[CURLOPT_HTTPHEADER] = array(
                        'Content-Type: application/x-www-form-urlencoded',
                        'Content-Length: ' . strlen($strData));
        $arrayOptions[CURLOPT_SSL_VERIFYPEER] = false;

        return $this->put($strUrl, $strData, $arrayOptions);
    }

    /**
     * Envió de datos FORMURLENCODED mediante POST a una URL REST
     *
     * @author Marlon Plúas <mpluas@telconet.ec>
     * @version 1.0 11-11-2019
     *
     * @param string $strUrl la direccion url de la api
     * @param string $strData_string data que se envia
     * @param array $arrayOptions a agregar mediante curl_setopt
     * @return array con tres valores:
     * result (mensaje de respuesta o false en caso de error),
     * status (codigo de status HTTP, 200 si es correcto, 404 si no se encuentra, 500 si hay error, etc.),
     * error (ultimo mensaje de error o '')
     */
    public function postFormURLEncoded($strUrl, $strData, array $arrayOptions = array())
    {
        // Guardo el array headers que retorna el método
	    $arrayPreOptionsHeaders = $arrayOptions[CURLOPT_HTTPHEADER] ? $arrayOptions[CURLOPT_HTTPHEADER] : array();
        $arrayOptions[CURLOPT_HTTPHEADER]       = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($strData),
        );
        $arrayOptions[CURLOPT_SSL_VERIFYPEER]   = false;

        // Agrego los headers adiciones si existen
        foreach($arrayPreOptionsHeaders as $value)
        {
            array_push($arrayOptions[CURLOPT_HTTPHEADER], $value);
        }

        return $this->post($strUrl, $strData, $arrayOptions);
    }

    /**
     * Envió de datos mediante PATCH a una URL REST
     *
     * @author Marlon Plúas <mpluas@telconet.ec>
     * @version 1.0 11-11-2019
     *
     * @param string $strUrl la direccion url de la api
     * @param string $strData_string data que se envia
     * @param array $arrayOptions a agregar mediante curl_setopt
     * @return array con tres valores:
     * result (mensaje de respuesta o false en caso de error),
     * status (codigo de status HTTP, 200 si es correcto, 404 si no se encuentra, 500 si hay error, etc.),
     * error (ultimo mensaje de error o '')
     */
    public function patchJSON($strUrl, $strData, array $arrayOptions = array())
    {
        // Guardo el array headers que retorna el método
	    $arrayPreOptionsHeaders = $arrayOptions[CURLOPT_HTTPHEADER] ? $arrayOptions[CURLOPT_HTTPHEADER] : array();
        $arrayOptions[CURLOPT_HTTPHEADER]       = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($strData),
        );
        $arrayOptions[CURLOPT_SSL_VERIFYPEER]   = false;

        // Agrego los headers adiciones si existen
        foreach($arrayPreOptionsHeaders as $value)
        {
            array_push($arrayOptions[CURLOPT_HTTPHEADER], $value);
        }

        return $this->patch($strUrl, $strData, $arrayOptions);
    }

    /**
     * Envió de datos mediante PUT a una URL REST
     *
     * @author Marlon Plúas <mpluas@telconet.ec>
     * @version 1.0 11-11-2019
     *
     * @param string $strUrl la direccion url de la api
     * @param string $strData_string data que se envia
     * @param array $arrayOptions a agregar mediante curl_setopt
     * @return array con tres valores:
     * result (mensaje de respuesta o false en caso de error),
     * status (codigo de status HTTP, 200 si es correcto, 404 si no se encuentra, 500 si hay error, etc.),
     * error (ultimo mensaje de error o '')
     */
    public function putJSON($strUrl, $strData, array $arrayOptions = array())
    {
        // Guardo el array headers que retorna el método
	    $arrayPreOptionsHeaders = $arrayOptions[CURLOPT_HTTPHEADER] ? $arrayOptions[CURLOPT_HTTPHEADER] : array();
        $arrayOptions[CURLOPT_HTTPHEADER]       = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($strData),
        );
        $arrayOptions[CURLOPT_SSL_VERIFYPEER]   = false;

        // Agrego los headers adiciones si existen
        foreach($arrayPreOptionsHeaders as $value)
        {
            array_push($arrayOptions[CURLOPT_HTTPHEADER], $value);
        }

        return $this->put($strUrl, $strData, $arrayOptions);
    }    
    /**
     * Envio de datos FORMURLENCODED mediante PUT a una URL REST
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 19-03-2019
     * @param string $strUrl la direccion url de la api
     * @param string $strData data que se envia
     * @param array $arrayOptions a agregar mediante curl_setopt
     * @return array con tres valores:
     * result (mensaje de respuesta o false en caso de error),
     * status (codigo de status HTTP, 200 si es correcto, 404 si no se encuentra, 500 si hay error, etc.),
     * error (ultimo mensaje de error o '')
     */
    public function postQueryParams($arrayData, array $arrayOptions = array())
    {
        $arrayOptions[CURLOPT_SSL_VERIFYPEER] = false;

        return $this->post2($arrayData['strUrl'] . "?" . $arrayData['strData'], $arrayOptions);
    }    
    /**
     * 
     * @version Inicial
     * 
     * @author Modificado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 10-08-2016   Se adiciona a este método el customrequest GET
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.2 24-10-2020 se agrega el timeout del ws y se lo setea en el consumo
     * 
     * Consume datos desde un URL mediante GET
     * @param string $url la direccion url de la api
     * @param array $options a agregar mediante curl_setopt
     * @return array con tres valores:
     * result (mensaje de respuesta o false en caso de error),
     * status (codigo de status HTTP, 200 si es correcto, 404 si no se encuentra, 500 si hay error, etc.),
     * error (ultimo mensaje de error o '')
     */
    public function post2($strUrl, array $arrayOptions = array())
    {
        $objCurl = curl_init();
        curl_setopt($objCurl, CURLOPT_URL, $strUrl);
        curl_setopt($objCurl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($objCurl, CURLOPT_HEADER, 0);
        curl_setopt($objCurl, CURLOPT_FOLLOWLOCATION, true);
        // que se devuelva el resultado de la ejecucion
        curl_setopt($objCurl, CURLOPT_RETURNTRANSFER, true);
        // default connect timeout de 15 segundos
        curl_setopt($objCurl, CURLOPT_CONNECTTIMEOUT, 15);
        // default execution timeout de 30 segundos (incluye el connect timeout)
        curl_setopt($objCurl, CURLOPT_TIMEOUT_MS, 30000);
        curl_setopt($objCurl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // definir opciones adicionales
        foreach ($arrayOptions as $arrayOption => $value)
        {
            curl_setopt($objCurl, $arrayOption, $value);
        }
        // ejecutar, obtener error si lo hay, cerrar sesion
        $strResult = curl_exec($objCurl);
        $strError = curl_error($objCurl);
        $strHttpStatus = curl_getinfo($objCurl, CURLINFO_HTTP_CODE);
        curl_close($objCurl);
        $arrayRespuesta = array('result' => $strResult, 'status' => $strHttpStatus, 'error' => $strError);
        return $arrayRespuesta;
    }
    
    /**
	 * Envio de datos JSON mediante POST a una URL REST
	 *
	 * @author  Antonio Ayala <afayala@telconet.ec>
	 * @version 1.0 14-09-2021 Se agrega lógica para agregar valores a la cabecera HEADER
	 *
	 * @param array $arrayOptions a agregar mediante curl_setopt
	 * @param string $strUrl      la direccion url de la api
	 * @param string $strData     data que se envia
	 *
	 * @return array con tres valores:
	 * result (mensaje de respuesta o false en caso de error),
	 * status (codigo de status HTTP, 200 si es correcto, 404 si no se encuentra, 500 si hay error, etc.),
	 * error (ultimo mensaje de error o '')
	 *
	 */
	public function postJSONSecure($arrayParametros, array $arrayOptions = array())
	{
        $strUrl   = $arrayParametros['strUrl'];
        $strToken = $arrayParametros['strToken'];
        $strData  = $arrayParametros['strDataString'];
        // Guardo el array headers que retorna el método
		$arrayPreOptionsHeaders = $arrayOptions[CURLOPT_HTTPHEADER] ? $arrayOptions[CURLOPT_HTTPHEADER] : array();
		
		$arrayOptions[CURLOPT_HTTPHEADER] = array('Content-Type: application/json',
                                                  'x-access-tokens: '.$strToken,
		                                          'Content-Length: '.strlen($strData));
		
		// Agrego los headers adiciones si existen
		foreach($arrayPreOptionsHeaders as $value)
		{
			array_push($arrayOptions[CURLOPT_HTTPHEADER], $value);
		}
		
		return $this->post($strUrl, $strData, $arrayOptions);
	}
    
       
    /**
     * Envio de datos JSON mediante POST a una URL REST
     * 
     * @author  Antonio Ayala <afayala@telconet.ec>
	 * @version 1.0 14-09-2021 Se agrega lógica para agregar valores a la cabecera HEADER
     * 
     * @param string $strUrl la direccion url de la api
     * @param string $strData data que se envia
     * @param array $arrayOptions a agregar mediante curl_setopt
     * 
     * @return array con tres valores:
     * result (mensaje de respuesta o false en caso de error),
     * status (codigo de status HTTP, 200 si es correcto, 404 si no se encuentra, 500 si hay error, etc.),
     * error (ultimo mensaje de error o '')
     */
    public function postJSONTokenSecure($arrayParametros, array $arrayOptions = array())
    {
        $strUrl          = $arrayParametros['strUrl'];
        $strCredenciales = $arrayParametros['strCredenciales'];
        
        // Guardo el array headers que retorna el método
		$arrayPreOptionsHeaders = $arrayOptions[CURLOPT_HTTPHEADER] ? $arrayOptions[CURLOPT_HTTPHEADER] : array();
        
        $arrayOptions[CURLOPT_HTTPHEADER] = array(
                'Content-Type: application/json',
                'Authorization: Basic '.$strCredenciales
        );
        
        // Agrego los headers adiciones si existen
		foreach($arrayPreOptionsHeaders as $value)
		{
			array_push($arrayOptions[CURLOPT_HTTPHEADER], $value);
		}
        
        return $this->post($strUrl, '', $arrayOptions);
    }


    /**
     * Envio de datos JSON mediante GET a una URL REST
     * 
     * @author  Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0 28-09-2022
     * 
     * @param string $strUrl la direccion url de la api
     * @param string $data data que se envia
     * 
     * @return array
    */
    public function getJsonCurl($arrayParametros)
    {
        $strUrl        = $arrayParametros['strUrl'];
        $objData       = $arrayParametros['data'];

        $objCurl = curl_init();

        curl_setopt_array($objCurl, array(
            CURLOPT_URL => $strUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => $objData,
            CURLOPT_HTTPHEADER => array('Accept: application/json', 
                                'Content-Type: application/json')
            ));

        $strHttpStatus = curl_getinfo($objCurl, CURLINFO_HTTP_CODE);
        $arrayCurl = curl_exec($objCurl);
        curl_close($objCurl);
        $arrayRespuesta = array('result' => $arrayCurl, 'status' => $strHttpStatus);

        return $arrayRespuesta;

    }

}
