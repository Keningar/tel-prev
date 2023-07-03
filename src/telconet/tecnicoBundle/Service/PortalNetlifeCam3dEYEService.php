<?php

namespace telconet\tecnicoBundle\Service;

use Doctrine\DBAL\DBALException;
use Symfony\Component\Config\Definition\Exception\Exception;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoPuntoCaracteristica;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;

/**
 * Documentación para la clase 'PortalNetlifeCam3dEYEService'.
 *
 * Clase utilizada para manejar métodos que permiten realizar la API REST o transacciones relacionadas al portal de 3dEYE
 *
 * @author  Marlon Plúas <mpluas@telconet.ec>
 * @version 1.0 11-11-2019
 */
class PortalNetlifeCam3dEYEService
{
	/**
	 * Usuario Netlife del portal 3deye
	 *
	 * @var string
	 */
	private $strUserNetlifePortal;
	/**
	 * Password Netlife del portal 3deye
	 *
	 * @var string
	 */
	private $strPassNetlifePortal;
	/**
	 * Api Key Netlife del portal 3deye
	 *
	 * @var string
	 */
	private $strApiKeyNetlifePortal;
	/**
	 * Endpoint para obtener token del customer
	 *
	 * @var string
	 */
	private $strUrlToken;
	/**
	 * Endpoint para transacciones del user
	 *
	 * @var string
	 */
	private $strUrlUser;
	/**
	 * Endpoint que retorna todos los user de un rol
	 *
	 * @var string
	 */
	private $strUrlRolGetUser;
	/**
	 * Endpoint para transacciones de la cámara
	 *
	 * @var string
	 */
	private $strUrlCamaras;
	/**
	 * Endpoint para transacciones de un usuario
	 *
	 * @var string
	 */
	private $strUrlUserById;
	/**
	 * Endpoint para crear cámaras con activación P2P
	 *
	 * @var string
	 */
	private $strUrlCreateCamaraP2P;
	/**
	 * Endpoint para crear cámaras con activación ONVIF
	 *
	 * @var string
	 */
	private $strUrlCreateCamaraONVIF;
	/**
	 * Endpoint para crear cámaras con activación GENERIC
	 *
	 * @var string
	 */
	private $strUrlCreateCamaraGENERIC;
	/**
	 * Endpoint para validar si la cámara esta online
	 *
	 * @var string
	 */
	private $strUrlValidateOnlineCamara;
	/**
	 * Endpoint para transacciones de roles
	 *
	 * @var string
	 */
	private $strUrlRoles;
	/**
	 * Endpoint para asignar o remover una cámara a un respectivo rol
	 *
	 * @var string
	 */
	private $strUrlCamaraRol;
	/**
	 * Endpoint para asignar o actualizar un usuario a un respectivo rol
	 *
	 * @var string
	 */
	private $strUrlAsignarUserRol;
	/**
	 *
	 * @var \telconet\schemaBundle\Service\RestClientService
	 */
	private $restClient;
	/**
	 * service $serviceUtil
	 */
	private $serviceUtil;
	/**
	 * service $serviceEnvioPlantilla
	 */
	private $serviceEnvioPlantilla;
	private $session;
	private $strStatusGlobal  = "OK";
	private $strMessageGlobal = "Transacción realizada";
	private $emNaf;
	private $emComercial;
	private $emInfraestructura;
	private $serviceInfoServicioTecnico;
	private $emSeguridad;
	private $intWs3dEYETimeOut;
	
	public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer)
	{
		$this->container                        = $objContainer;
		$this->emSoporte                        = $objContainer->get('doctrine')->getManager('telconet_soporte');
		$this->emInfraestructura                = $objContainer->get('doctrine')->getManager('telconet_infraestructura');
		$this->emSeguridad                      = $objContainer->get('doctrine')->getManager('telconet_seguridad');
		$this->emComercial                      = $objContainer->get('doctrine')->getManager('telconet');
		$this->emComunicacion                   = $objContainer->get('doctrine')->getManager('telconet_comunicacion');
		$this->emNaf                            = $objContainer->get('doctrine')->getManager('telconet_naf');
		$this->emGeneral                        = $objContainer->get('doctrine')->getManager('telconet_general');
		$this->session                          = $objContainer->get('session');
		$this->serviceUtil                      = $objContainer->get('schema.Util');
		$this->restClient                       = $objContainer->get('schema.RestClient');
		$this->serviceEnvioPlantilla            = $objContainer->get('soporte.EnvioPlantilla');
		$this->serviceInfoServicioTecnico       = $objContainer->get('tecnico.InfoServicioTecnico');
		$this->strUserNetlifePortal             = $objContainer->getParameter('3deye.userNetlife');
		$this->strPassNetlifePortal             = $objContainer->getParameter('3deye.passNetlife');
		$this->strApiKeyNetlifePortal           = $objContainer->getParameter('3deye.apiKey');
		$this->intWs3dEYETimeOut                = $objContainer->getParameter('3deye.WsTimeOut');
		$this->strUrlToken                      = $objContainer->getParameter('3deye.urlToken');
		$this->strUrlUser                       = $objContainer->getParameter('3deye.urlUser');
		$this->strUrlRolGetUser                 = $objContainer->getParameter('3deye.urlRolGetUser');
		$this->strUrlCreateCamaraP2P            = $objContainer->getParameter('3deye.urlCreateCamaraP2P');
		$this->strUrlCreateCamaraONVIF          = $objContainer->getParameter('3deye.urlCreateCamaraONVIF');
		$this->strUrlCreateCamaraGENERIC        = $objContainer->getParameter('3deye.urlCreateCamaraGENERIC');
		$this->strUrlValidateOnlineCamara       = $objContainer->getParameter('3deye.urlValidateOnlineCamara');
		$this->strUrlCamaras                    = $objContainer->getParameter('3deye.urlCamaras');
		$this->strUrlRoles                      = $objContainer->getParameter('3deye.urlRoles');
		$this->strUrlCamaraRol                  = $objContainer->getParameter('3deye.urlCamaraRol');
		$this->strUrlAsignarUserRol             = $objContainer->getParameter('3deye.urlAsignarUserRol');
		$this->strUrlUserById                   = $objContainer->getParameter('3deye.urlUserById');
	}
	
	/**
	 * Documentación para la función 'getDatosClienteById'
	 * Función utilizada para retornar los datos de un usuario del portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $strUser3DEYE
	 *
	 * @return  $objResponse
	 */
	public function getDatosClienteById($strUser3DEYE)
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = $this->strStatusGlobal;
		$strMessage     = $this->strMessageGlobal;
		$arrayData      = "";
		
		$strToken = $this->getAuthTokenCustomer($this->strUserNetlifePortal,
		                                        $this->strPassNetlifePortal,
		                                        $this->strApiKeyNetlifePortal);
		
		if($strToken)
		{
			$objHeaders[CURLOPT_HTTPHEADER] = array("Accept: application/json",
			                                        "x-api-key: ".$this->strApiKeyNetlifePortal,
			                                        'Authorization: Bearer '.$strToken,);
			$objHeaders[CURLOPT_TIMEOUT]    = $this->intWs3dEYETimeOut;
			
			$strUrlFinal                    = $this->strUrlUser."/".$strUser3DEYE;
			$arrayRespJsonWS                = $this->restClient->get($strUrlFinal, $objHeaders);
			$arrayResultado                 = json_decode($arrayRespJsonWS['result'], true);
			if(($arrayResultado['error'] || $arrayResultado['errors']) || $arrayRespJsonWS["error"])
			{
				$strErrorDef = "";
				foreach($arrayResultado['errors'] as $error)
				{
					$strErrorDef = $strErrorDef.$error["code"]." - ".$error["message"]."| ";
				}
				$strErrorMesg = $arrayRespJsonWS["error"] ? $arrayRespJsonWS["error"] : $strErrorDef;
				$this->serviceUtil->insertError('Telcos+',
				                                'PortalNetlifeCam3dEYEService->getDatosClienteById',
				                                $arrayResultado['error_description'] ? $arrayResultado['error_description'] : $strErrorMesg,
				                                $strUsrCreacion,
				                                '127.0.0.1');
				
				if($arrayRespJsonWS["error"])
				{
					throw new Exception('Ha ocurrido un problema. Por favor notifique a Sistemas!');
				}
				
				$strStatus  = 'ERROR';
				$strMessage = 'Transacción fallida';
			}
			else
			{
				$arrayData = $arrayResultado;
			}
		}
		else
		{
			$strStatus  = 'ERROR';
			$strMessage = 'Transacción fallida';
		}
		
		$objResponse['strStatus']  = $strStatus;
		$objResponse['strMessage'] = $strMessage;
		$objResponse['arrayData']  = $arrayData;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'createCamaraP2P'
	 * Función utilizada para crear cámara tipo P2P en el portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $arrayDataCreateCamara
	 *
	 * @return  $objResponse
	 */
	public function createCamaraP2P($arrayDataCreateCamara)
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = $this->strStatusGlobal;
		$strMessage     = $this->strMessageGlobal;
		$arrayData      = "";
		
		$strToken = $this->getAuthTokenCustomer($this->strUserNetlifePortal,
		                                        $this->strPassNetlifePortal,
		                                        $this->strApiKeyNetlifePortal);
		
		if($strToken)
		{
			$objHeaders[CURLOPT_HTTPHEADER] = array("Accept: application/json",
			                                        "x-api-key: ".$this->strApiKeyNetlifePortal,
			                                        'Authorization: Bearer '.$strToken,);
			$objHeaders[CURLOPT_TIMEOUT]    = $this->intWs3dEYETimeOut;
			
			$arrayDataPost = array("name"             => $arrayDataCreateCamara["strNombreCamara"],
			                       "registrationCode" => $arrayDataCreateCamara["strRegistrationCode"],
			                       "adminName"        => $arrayDataCreateCamara["strAdminName"],
			                       "adminPassword"    => $arrayDataCreateCamara["strAdminPassword"],);
			
			$strJsonData     = json_encode($arrayDataPost, true);
			$strUrlFinal     = $this->strUrlCreateCamaraP2P;
			$arrayRespJsonWS = $this->restClient->postJSON($strUrlFinal, $strJsonData, $objHeaders);
			$arrayResultado  = json_decode($arrayRespJsonWS['result'], true);
			if(($arrayResultado['error'] || $arrayResultado['errors']) || $arrayRespJsonWS["error"])
			{
				$strErrorDef = "";
				foreach($arrayResultado['errors'] as $error)
				{
					$strErrorDef = $strErrorDef.$error["code"]." - ".$error["message"]."| ";
				}
				$strErrorMesg = $arrayRespJsonWS["error"] ? $arrayRespJsonWS["error"] : $strErrorDef;
				$this->serviceUtil->insertError('Telcos+',
				                                'PortalNetlifeCam3dEYEService->createCamaraP2P',
				                                $arrayResultado['error_description'] ? $arrayResultado['error_description'] : $strErrorMesg,
				                                $strUsrCreacion,
				                                '127.0.0.1');
				
				if($arrayRespJsonWS["error"])
				{
					throw new Exception('Ha ocurrido un problema. Por favor notifique a Sistemas!');
				}
			}
			else
			{
				$arrayData = $arrayResultado;
			}
		}
		else
		{
			$strStatus  = 'ERROR';
			$strMessage = 'Transacción fallida';
		}
		
		$objResponse['strStatus']  = $strStatus;
		$objResponse['strMessage'] = $strMessage;
		$objResponse['arrayData']  = $arrayData;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'createCamaraONVIF'
	 * Función utilizada para crear cámara tipo ONVIF en el portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $arrayDataCreateCamara
	 *
	 * @return  $objResponse
	 */
	public function createCamaraONVIF($arrayDataCreateCamara)
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = $this->strStatusGlobal;
		$strMessage     = $this->strMessageGlobal;
		$arrayData      = "";
		
		$strToken = $this->getAuthTokenCustomer($this->strUserNetlifePortal,
		                                        $this->strPassNetlifePortal,
		                                        $this->strApiKeyNetlifePortal);
		
		if($strToken)
		{
			$objHeaders[CURLOPT_HTTPHEADER] = array("Accept: application/json",
			                                        "x-api-key: ".$this->strApiKeyNetlifePortal,
			                                        'Authorization: Bearer '.$strToken,);
			$objHeaders[CURLOPT_TIMEOUT]    = $this->intWs3dEYETimeOut;
			
			$strHttpAccessUrl = trim($arrayDataCreateCamara["strDDNSCamaraONVIF"]).":".trim($arrayDataCreateCamara["strPuertoHTTPCamaraONVIF"]);
			$arrayDataPost    = array("name"          => $arrayDataCreateCamara["strNombreCamara"],
			                          "httpAccessUrl" => $strHttpAccessUrl,
			                          "rtspPort"      => $arrayDataCreateCamara["strPuertoRTSPCamaraONVIF"],
			                          "adminName"     => $arrayDataCreateCamara["strAdminName"],
			                          "adminPassword" => $arrayDataCreateCamara["strAdminPassword"],
			
			);
			
			$strJsonData     = json_encode($arrayDataPost, true);
			$strUrlFinal     = $this->strUrlCreateCamaraONVIF;
			$arrayRespJsonWS = $this->restClient->postJSON($strUrlFinal, $strJsonData, $objHeaders);
			$arrayResultado  = json_decode($arrayRespJsonWS['result'], true);
			if(($arrayResultado['error'] || $arrayResultado['errors']) || $arrayRespJsonWS["error"])
			{
				$strErrorDef = "";
				foreach($arrayResultado['errors'] as $error)
				{
					$strErrorDef = $strErrorDef.$error["code"]." - ".$error["message"]."| ";
				}
				$strErrorMesg = $arrayRespJsonWS["error"] ? $arrayRespJsonWS["error"] : $strErrorDef;
				$this->serviceUtil->insertError('Telcos+',
				                                'PortalNetlifeCam3dEYEService->createCamaraONVIF',
				                                $arrayResultado['error_description'] ? $arrayResultado['error_description'] : $strErrorMesg,
				                                $strUsrCreacion,
				                                '127.0.0.1');
				
				if($arrayRespJsonWS["error"])
				{
					throw new Exception('Ha ocurrido un problema. Por favor notifique a Sistemas!');
				}
			}
			else
			{
				$arrayData = $arrayResultado;
			}
		}
		else
		{
			$strStatus  = 'ERROR';
			$strMessage = 'Transacción fallida';
		}
		
		$objResponse['strStatus']  = $strStatus;
		$objResponse['strMessage'] = $strMessage;
		$objResponse['arrayData']  = $arrayData;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'createCamaraGENERIC'
	 * Función utilizada para crear cámara tipo GENERIC en el portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $arrayDataCreateCamara
	 *
	 * @return  $objResponse
	 */
	public function createCamaraGENERIC($arrayDataCreateCamara)
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = $this->strStatusGlobal;
		$strMessage     = $this->strMessageGlobal;
		$arrayData      = "";
		
		$strToken = $this->getAuthTokenCustomer($this->strUserNetlifePortal,
		                                        $this->strPassNetlifePortal,
		                                        $this->strApiKeyNetlifePortal);
		
		if($strToken)
		{
			$objHeaders[CURLOPT_HTTPHEADER] = array("Accept: application/json",
			                                        "x-api-key: ".$this->strApiKeyNetlifePortal,
			                                        'Authorization: Bearer '.$strToken,);
			$objHeaders[CURLOPT_TIMEOUT]    = $this->intWs3dEYETimeOut;
			
			$strHttpAccessUrl = trim($arrayDataCreateCamara["strDDNSCamaraGENERIC"]).":".trim($arrayDataCreateCamara["strPuertoHTTPCamaraGENERIC"]);
			$strRtspAccessUrl = trim($arrayDataCreateCamara["strRTSPCamaraGENERIC"]).":".trim($arrayDataCreateCamara["strPuertoRTSPCamaraGENERIC"]);
			$arrayDataPost    = array("name"          => $arrayDataCreateCamara["strNombreCamara"],
			                          "httpAccessUrl" => $strHttpAccessUrl,
			                          "rtspAccessUrl" => $strRtspAccessUrl,
			                          "deviceBrand"   => $arrayDataCreateCamara["strDeviceBrandGENERIC"],
			                          "adminName"     => $arrayDataCreateCamara["strAdminName"],
			                          "adminPassword" => $arrayDataCreateCamara["strAdminPassword"]);
			
			$strJsonData     = json_encode($arrayDataPost, true);
			$strUrlFinal     = $this->strUrlCreateCamaraGENERIC;
			$arrayRespJsonWS = $this->restClient->postJSON($strUrlFinal, $strJsonData, $objHeaders);
			$arrayResultado  = json_decode($arrayRespJsonWS['result'], true);
			if(($arrayResultado['error'] || $arrayResultado['errors']) || $arrayRespJsonWS["error"])
			{
				$strErrorDef = "";
				foreach($arrayResultado['errors'] as $error)
				{
					$strErrorDef = $strErrorDef.$error["code"]." - ".$error["message"]."| ";
				}
				$strErrorMesg = $arrayRespJsonWS["error"] ? $arrayRespJsonWS["error"] : $strErrorDef;
				$this->serviceUtil->insertError('Telcos+',
				                                'PortalNetlifeCam3dEYEService->createCamaraGENERIC',
				                                $arrayResultado['error_description'] ? $arrayResultado['error_description'] : $strErrorMesg,
				                                $strUsrCreacion,
				                                '127.0.0.1');
				
				if($arrayRespJsonWS["error"])
				{
					throw new Exception('Ha ocurrido un problema. Por favor notifique a Sistemas!');
				}
			}
			else
			{
				$arrayData = $arrayResultado;
			}
		}
		else
		{
			$strStatus  = 'ERROR';
			$strMessage = 'Transacción fallida';
		}
		
		$objResponse['strStatus']  = $strStatus;
		$objResponse['strMessage'] = $strMessage;
		$objResponse['arrayData']  = $arrayData;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'createRol'
	 * Función utilizada para crear un rol en el portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $arrayDataCreateRol
	 *
	 * @return  $objResponse
	 */
	public function createRol($arrayDataCreateRol)
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = $this->strStatusGlobal;
		$strMessage     = $this->strMessageGlobal;
		$arrayData      = "";
		$boolExisteRol  = false;
		
		// Valido si existe un rol con el nombre del login
		$arrayRespValidateRol = $this->validateRolByName($arrayDataCreateRol["strName"]);
		if($arrayRespValidateRol["arrayData"])
		{
			$arrayData     = $arrayRespValidateRol["arrayData"];
			$boolExisteRol = true;
		}
		
		if(!$boolExisteRol)
		{
			$strToken = $this->getAuthTokenCustomer($this->strUserNetlifePortal,
			                                        $this->strPassNetlifePortal,
			                                        $this->strApiKeyNetlifePortal);
			
			if($strToken)
			{
				$objHeaders[CURLOPT_HTTPHEADER] = array("Accept: application/json",
				                                        "x-api-key: ".$this->strApiKeyNetlifePortal,
				                                        'Authorization: Bearer '.$strToken,);
				$objHeaders[CURLOPT_TIMEOUT]    = $this->intWs3dEYETimeOut;
				
				$arrayDataPost = array("name"        => $arrayDataCreateRol["strName"],
				                       "description" => $arrayDataCreateRol["strDescription"],
				                       "type"        => $arrayDataCreateRol["strType"],);
				
				$strJsonData     = json_encode($arrayDataPost, true);
				$strUrlFinal     = $this->strUrlRoles;
				$arrayRespJsonWS = $this->restClient->postJSON($strUrlFinal, $strJsonData, $objHeaders);
				$arrayResultado  = json_decode($arrayRespJsonWS['result'], true);
				if(($arrayResultado['error'] || $arrayResultado['errors']) || $arrayRespJsonWS["error"])
				{
					$strErrorDef = "";
					foreach($arrayResultado['errors'] as $error)
					{
						$strErrorDef = $strErrorDef.$error["code"]." - ".$error["message"]."| ";
					}
					$strErrorMesg = $arrayRespJsonWS["error"] ? $arrayRespJsonWS["error"] : $strErrorDef;
					$this->serviceUtil->insertError('Telcos+',
					                                'PortalNetlifeCam3dEYEService->createRol',
					                                $arrayResultado['error_description'] ? $arrayResultado['error_description'] : $strErrorMesg,
					                                $strUsrCreacion,
					                                '127.0.0.1');
					
					if($arrayRespJsonWS["error"])
					{
						throw new Exception('Ha ocurrido un problema. Por favor notifique a Sistemas!');
					}
				}
				else
				{
					$arrayData = $arrayResultado;
				}
			}
			else
			{
				$strStatus  = 'ERROR';
				$strMessage = 'Transacción fallida';
			}
		}
		
		$objResponse['strStatus']  = $strStatus;
		$objResponse['strMessage'] = $strMessage;
		$objResponse['arrayData']  = $arrayData;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'createUser'
	 * Función utilizada para crear un usuario en el portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $arrayDataCreateUser
	 *
	 * @return  $objResponse
	 */
	public function createUser($arrayDataCreateUser)
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = $this->strStatusGlobal;
		$strMessage     = $this->strMessageGlobal;
		$arrayData      = "";
		
		$strToken = $this->getAuthTokenCustomer($this->strUserNetlifePortal,
		                                        $this->strPassNetlifePortal,
		                                        $this->strApiKeyNetlifePortal);
		
		if($strToken)
		{
			$objHeaders[CURLOPT_HTTPHEADER] = array("Accept: application/json",
			                                        "x-api-key: ".$this->strApiKeyNetlifePortal,
			                                        'Authorization: Bearer '.$strToken,);
			$objHeaders[CURLOPT_TIMEOUT]    = $this->intWs3dEYETimeOut;
			
			$arrayDataPost = array("firstName" => $arrayDataCreateUser["strFirstName"],
			                       "lastName"  => $arrayDataCreateUser["strLastName"],
			                       "email"     => $arrayDataCreateUser["strEmail"],
			                       "password"  => $arrayDataCreateUser["strPassword"],);
			
			$strJsonData     = json_encode($arrayDataPost, true);
			$strUrlFinal     = $this->strUrlUser;
			$arrayRespJsonWS = $this->restClient->postJSON($strUrlFinal, $strJsonData, $objHeaders);
			$arrayResultado  = json_decode($arrayRespJsonWS['result'], true);
			if(($arrayResultado['error'] || $arrayResultado['errors']) || $arrayRespJsonWS["error"])
			{
				$strErrorDefDupli = $arrayResultado['errors'][0]['code'];
				// Si existe usuario, actualizo su informaciom y retorno los datos
				if($strErrorDefDupli == "duplicate_object")
				{
					$arrayRespDatosUserForUsername = $this->validateUserForUsername($arrayDataCreateUser["strEmail"]);
					$arrayData                     = $arrayRespDatosUserForUsername["arrayData"];
					if($arrayData)
					{
						$arrayData["boolUserExiste"] = true;
						// Actualizar la data si es diferente
						if($arrayData["firstName"] != $arrayDataCreateUser["strFirstName"]
						   || $arrayData["lastName"] != $arrayDataCreateUser["strLastName"])
						{
							$strIdUser3DEYE  = $arrayData["id"];
							$arrayDataUpdate = array("newFirstName" => $arrayDataCreateUser["strFirstName"],
							                         "newLastName"  => $arrayDataCreateUser["strLastName"],);
							$strJsonData     = json_encode($arrayDataUpdate, true);
							$this->updateUserById($strIdUser3DEYE, $strJsonData);
						}
					}
				}
				else
				{
					$strErrorDef = "";
					foreach($arrayResultado['errors'] as $error)
					{
						$strErrorDef = $strErrorDef.$error["code"]." - ".$error["message"]."| ";
					}
					$strErrorMesg = $arrayRespJsonWS["error"] ? $arrayRespJsonWS["error"] : $strErrorDef;
					$this->serviceUtil->insertError('Telcos+',
					                                'PortalNetlifeCam3dEYEService->createUser',
					                                $arrayResultado['error_description'] ? $arrayResultado['error_description'] : $strErrorMesg,
					                                $strUsrCreacion,
					                                '127.0.0.1');
				}
				
				if($arrayRespJsonWS["error"])
				{
					throw new Exception('Ha ocurrido un problema. Por favor notifique a Sistemas!');
				}
			}
			else
			{
				$arrayData = $arrayResultado;
				$arrayData["boolUserExiste"] = false;
			}
		}
		else
		{
			$strStatus  = 'ERROR';
			$strMessage = 'Transacción fallida';
		}
		
		$objResponse['strStatus']  = $strStatus;
		$objResponse['strMessage'] = $strMessage;
		$objResponse['arrayData']  = $arrayData;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'validateUserForUsername'
	 * Función utilizada para validar si el usuario existe en el portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $strEmail
	 *
	 * @return  $objResponse
	 */
	public function validateUserForUsername($strEmail)
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = $this->strStatusGlobal;
		$strMessage     = $this->strMessageGlobal;
		$arrayData      = "";
		
		$strToken = $this->getAuthTokenCustomer($this->strUserNetlifePortal,
		                                        $this->strPassNetlifePortal,
		                                        $this->strApiKeyNetlifePortal);
		
		if($strToken)
		{
			$objHeaders[CURLOPT_HTTPHEADER] = array("Accept: application/json",
			                                        "x-api-key: ".$this->strApiKeyNetlifePortal,
			                                        'Authorization: Bearer '.$strToken,);
			$objHeaders[CURLOPT_TIMEOUT]    = $this->intWs3dEYETimeOut;
			
			$strUrlFinal                    = $this->strUrlUser;
			$arrayRespJsonWS                = $this->restClient->get($strUrlFinal, $objHeaders);
			$arrayResultado                 = json_decode($arrayRespJsonWS['result'], true);
			if(($arrayResultado['error'] || $arrayResultado['errors']) || $arrayRespJsonWS["error"])
			{
				$strErrorDef = "";
				foreach($arrayResultado['errors'] as $error)
				{
					$strErrorDef = $strErrorDef.$error["code"]." - ".$error["message"]."| ";
				}
				$strErrorMesg = $arrayRespJsonWS["error"] ? $arrayRespJsonWS["error"] : $strErrorDef;
				$this->serviceUtil->insertError('Telcos+',
				                                'PortalNetlifeCam3dEYEService->validateUserForUsername',
				                                $arrayResultado['error_description'] ? $arrayResultado['error_description'] : $strErrorMesg,
				                                $strUsrCreacion,
				                                '127.0.0.1');
				
				if($arrayRespJsonWS["error"])
				{
					throw new Exception('Ha ocurrido un problema. Por favor notifique a Sistemas!');
				}
			}
			else
			{
				foreach($arrayResultado["users"] as $user)
				{
					if($user["userName"] == $strEmail)
					{
						$arrayData = $user;
						break;
					}
				}
			}
		}
		else
		{
			$strStatus  = 'ERROR';
			$strMessage = 'Transacción fallida';
		}
		
		$objResponse['strStatus']  = $strStatus;
		$objResponse['strMessage'] = $strMessage;
		$objResponse['arrayData']  = $arrayData;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'updateUserById'
	 * Función utilizada para actualizar los datos de un usuario en el portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $strIdUser3DEYE
	 * @param $strJsonData
	 *
	 * @return  $objResponse
	 */
	public function updateUserById($strIdUser3DEYE, $strJsonData)
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = $this->strStatusGlobal;
		$strMessage     = $this->strMessageGlobal;
		$boolUpdateUser = false;
		
		$strToken = $this->getAuthTokenCustomer($this->strUserNetlifePortal,
		                                        $this->strPassNetlifePortal,
		                                        $this->strApiKeyNetlifePortal);
		
		if($strToken)
		{
			$objHeaders[CURLOPT_HTTPHEADER] = array("Accept: application/json",
			                                        "x-api-key: ".$this->strApiKeyNetlifePortal,
			                                        'Authorization: Bearer '.$strToken,);
			$objHeaders[CURLOPT_TIMEOUT]    = $this->intWs3dEYETimeOut;
			
			$strUrlFinal     = $this->strUrlUser."/".$strIdUser3DEYE;
			$arrayRespJsonWS = $this->restClient->patchJSON($strUrlFinal, $strJsonData, $objHeaders);
			$arrayResultado  = json_decode($arrayRespJsonWS['result'], true);
			if(($arrayResultado['error'] || $arrayResultado['errors']) || $arrayRespJsonWS["error"])
			{
				$strErrorDef = "";
				foreach($arrayResultado['errors'] as $error)
				{
					$strErrorDef = $strErrorDef.$error["code"]." - ".$error["message"]."| ";
				}
				$strErrorMesg = $arrayRespJsonWS["error"] ? $arrayRespJsonWS["error"] : $strErrorDef;
				$this->serviceUtil->insertError('Telcos+',
				                                'PortalNetlifeCam3dEYEService->updateUserById',
				                                $arrayResultado['error_description'] ? $arrayResultado['error_description'] : $strErrorMesg,
				                                $strUsrCreacion,
				                                '127.0.0.1');
				
				if($arrayRespJsonWS["error"])
				{
					throw new Exception('Ha ocurrido un problema. Por favor notifique a Sistemas!');
				}
			}
			else
			{
				$boolUpdateUser = true;
			}
		}
		else
		{
			$strStatus  = 'ERROR';
			$strMessage = 'Transacción fallida';
		}
		
		$objResponse['strStatus']      = $strStatus;
		$objResponse['strMessage']     = $strMessage;
		$objResponse['boolUpdateUser'] = $boolUpdateUser;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'validateRolByName'
	 * Función utilizada para validar un rol en el portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $strNombreRol
	 *
	 * @return  $objResponse
	 */
	public function validateRolByName($strNombreRol)
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = $this->strStatusGlobal;
		$strMessage     = $this->strMessageGlobal;
		$arrayData      = "";
		
		$strToken = $this->getAuthTokenCustomer($this->strUserNetlifePortal,
		                                        $this->strPassNetlifePortal,
		                                        $this->strApiKeyNetlifePortal);
		
		if($strToken)
		{
			$objHeaders[CURLOPT_HTTPHEADER] = array("Accept: application/json",
			                                        "x-api-key: ".$this->strApiKeyNetlifePortal,
			                                        'Authorization: Bearer '.$strToken,);
			$objHeaders[CURLOPT_TIMEOUT]    = $this->intWs3dEYETimeOut;
			
			$strUrlFinal                    = $this->strUrlRoles;
			$arrayRespJsonWS                = $this->restClient->get($strUrlFinal, $objHeaders);
			$arrayResultado                 = json_decode($arrayRespJsonWS['result'], true);
			if(($arrayResultado['error'] || $arrayResultado['errors']) || $arrayRespJsonWS["error"])
			{
				$strErrorDef = "";
				foreach($arrayResultado['errors'] as $error)
				{
					$strErrorDef = $strErrorDef.$error["code"]." - ".$error["message"]."| ";
				}
				$strErrorMesg = $arrayRespJsonWS["error"] ? $arrayRespJsonWS["error"] : $strErrorDef;
				$this->serviceUtil->insertError('Telcos+',
				                                'PortalNetlifeCam3dEYEService->validateRolByName',
				                                $arrayResultado['error_description'] ? $arrayResultado['error_description'] : $strErrorMesg,
				                                $strUsrCreacion,
				                                '127.0.0.1');
				
				if($arrayRespJsonWS["error"])
				{
					throw new Exception('Ha ocurrido un problema. Por favor notifique a Sistemas!');
				}
			}
			else
			{
				foreach($arrayResultado["roles"] as $rol)
				{
					if($rol["name"] == $strNombreRol)
					{
						$arrayData = $rol;
						break;
					}
				}
			}
		}
		else
		{
			$strStatus  = 'ERROR';
			$strMessage = 'Transacción fallida';
		}
		
		$objResponse['strStatus']  = $strStatus;
		$objResponse['strMessage'] = $strMessage;
		$objResponse['arrayData']  = $arrayData;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'getDatosCompletosRolById'
	 * Función utilizada para obtener los datos completos de un rol en el portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $intIdRol3DEYE
	 *
	 * @return  $objResponse
	 */
	public function getDatosCompletosRolById($intIdRol3DEYE)
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = $this->strStatusGlobal;
		$strMessage     = $this->strMessageGlobal;
		$arrayData      = "";
		
		$strToken = $this->getAuthTokenCustomer($this->strUserNetlifePortal,
		                                        $this->strPassNetlifePortal,
		                                        $this->strApiKeyNetlifePortal);
		
		if($strToken)
		{
			$objHeaders[CURLOPT_HTTPHEADER] = array("Accept: application/json",
			                                        "x-api-key: ".$this->strApiKeyNetlifePortal,
			                                        'Authorization: Bearer '.$strToken,);
			$objHeaders[CURLOPT_TIMEOUT]    = $this->intWs3dEYETimeOut;
			
			$strUrlFinal                    = $this->strUrlRoles;
			$arrayRespJsonWS                = $this->restClient->get($strUrlFinal, $objHeaders);
			$arrayResultado                 = json_decode($arrayRespJsonWS['result'], true);
			if(($arrayResultado['error'] || $arrayResultado['errors']) || $arrayRespJsonWS["error"])
			{
				$strErrorDef = "";
				foreach($arrayResultado['errors'] as $error)
				{
					$strErrorDef = $strErrorDef.$error["code"]." - ".$error["message"]."| ";
				}
				$strErrorMesg = $arrayRespJsonWS["error"] ? $arrayRespJsonWS["error"] : $strErrorDef;
				$this->serviceUtil->insertError('Telcos+',
				                                'PortalNetlifeCam3dEYEService->getDatosCompletosRolById',
				                                $arrayResultado['error_description'] ? $arrayResultado['error_description'] : $strErrorMesg,
				                                $strUsrCreacion,
				                                '127.0.0.1');
				
				if($arrayRespJsonWS["error"])
				{
					throw new Exception('Ha ocurrido un problema. Por favor notifique a Sistemas!');
				}
				
				$strStatus  = 'ERROR';
				$strMessage = 'Transacción fallida';
			}
			else
			{
				foreach($arrayResultado["roles"] as $rol)
				{
					if($rol["id"] == $intIdRol3DEYE)
					{
						$arrayData = $rol;
						break;
					}
				}
			}
		}
		else
		{
			$strStatus  = 'ERROR';
			$strMessage = 'Transacción fallida';
		}
		
		$objResponse['strStatus']  = $strStatus;
		$objResponse['strMessage'] = $strMessage;
		$objResponse['arrayData']  = $arrayData;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'asignarCamaraRol'
	 * Función utilizada para asignar o actualizar permisos de una cámara a un rol en el portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $intIdRol3dEYE
	 * @param $intIdCam3dEYE
	 * @param null $arrayDataPost
	 *
	 * @return  $objResponse
	 */
	public function asignarCamaraRol($intIdRol3dEYE, $intIdCam3dEYE, $arrayDataPost = null)
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = $this->strStatusGlobal;
		$strMessage     = $this->strMessageGlobal;
		$boolAsigCamRol = false;
		
		$strToken = $this->getAuthTokenCustomer($this->strUserNetlifePortal,
		                                        $this->strPassNetlifePortal,
		                                        $this->strApiKeyNetlifePortal);
		
		if($strToken)
		{
			$objHeaders[CURLOPT_HTTPHEADER] = array("Accept: application/json",
			                                        "x-api-key: ".$this->strApiKeyNetlifePortal,
			                                        'Authorization: Bearer '.$strToken,);
			$objHeaders[CURLOPT_TIMEOUT]    = $this->intWs3dEYETimeOut;
			
			$strJsonData      = json_encode($arrayDataPost, true);
			$strUrlAsignarRol = str_replace("roleId", $intIdRol3dEYE, $this->strUrlCamaraRol);
			$strUrlFinal      = str_replace("cameraId", $intIdCam3dEYE, $strUrlAsignarRol);
			$arrayRespJsonWS  = $this->restClient->putJSON($strUrlFinal, $strJsonData, $objHeaders);
			$arrayResultado   = json_decode($arrayRespJsonWS['result'], true);
			if(($arrayResultado['error'] || $arrayResultado['errors']) || $arrayRespJsonWS["error"])
			{
				$strErrorDef = "";
				foreach($arrayResultado['errors'] as $error)
				{
					$strErrorDef = $strErrorDef.$error["code"]." - ".$error["message"]."| ";
				}
				$strErrorMesg = $arrayRespJsonWS["error"] ? $arrayRespJsonWS["error"] : $strErrorDef;
				$this->serviceUtil->insertError('Telcos+',
				                                'PortalNetlifeCam3dEYEService->asignarCamaraRol',
				                                $arrayResultado['error_description'] ? $arrayResultado['error_description'] : $strErrorMesg,
				                                $strUsrCreacion,
				                                '127.0.0.1');
				
				if($arrayRespJsonWS["error"])
				{
					throw new Exception('Ha ocurrido un problema. Por favor notifique a Sistemas!');
				}
			}
			else
			{
				$boolAsigCamRol = true;
			}
		}
		else
		{
			$strStatus  = 'ERROR';
			$strMessage = 'Transacción fallida';
		}
		
		$objResponse['strStatus']      = $strStatus;
		$objResponse['strMessage']     = $strMessage;
		$objResponse['boolAsigCamRol'] = $boolAsigCamRol;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'asignarUserRol'
	 * Función utilizada para asignar un usuario a un rol en el portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $intIdRol3dEYE
	 * @param $intIdUser3dEYE
	 * @param null $arrayDataPost
	 *
	 * @return  $objResponse
	 */
	public function asignarUserRol($intIdRol3dEYE, $intIdUser3dEYE, $arrayDataPost = null)
	{
		$objResponse     = array();
		$strUsrCreacion  = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus       = $this->strStatusGlobal;
		$strMessage      = $this->strMessageGlobal;
		$boolAsigUserRol = false;
		
		$strToken = $this->getAuthTokenCustomer($this->strUserNetlifePortal,
		                                        $this->strPassNetlifePortal,
		                                        $this->strApiKeyNetlifePortal);
		
		if($strToken)
		{
			$objHeaders[CURLOPT_HTTPHEADER] = array("Accept: application/json",
			                                        "x-api-key: ".$this->strApiKeyNetlifePortal,
			                                        'Authorization: Bearer '.$strToken,);
			$objHeaders[CURLOPT_TIMEOUT]    = $this->intWs3dEYETimeOut;
			
			$strJsonData      = json_encode($arrayDataPost, true);
			$strUrlAsignarRol = str_replace("roleId", $intIdRol3dEYE, $this->strUrlAsignarUserRol);
			$strUrlFinal      = str_replace("userId", $intIdUser3dEYE, $strUrlAsignarRol);
			$arrayRespJsonWS  = $this->restClient->putJSON($strUrlFinal, $strJsonData, $objHeaders);
			$arrayResultado   = json_decode($arrayRespJsonWS['result'], true);
			if(($arrayResultado['error'] || $arrayResultado['errors']) || $arrayRespJsonWS["error"])
			{
				$strErrorDef = "";
				foreach($arrayResultado['errors'] as $error)
				{
					$strErrorDef = $strErrorDef.$error["code"]." - ".$error["message"]."| ";
				}
				$strErrorMesg = $arrayRespJsonWS["error"] ? $arrayRespJsonWS["error"] : $strErrorDef;
				$this->serviceUtil->insertError('Telcos+',
				                                'PortalNetlifeCam3dEYEService->asignarUserRol',
				                                $arrayResultado['error_description'] ? $arrayResultado['error_description'] : $strErrorMesg,
				                                $strUsrCreacion,
				                                '127.0.0.1');
				
				if($arrayRespJsonWS["error"])
				{
					throw new Exception('Ha ocurrido un problema. Por favor notifique a Sistemas!');
				}
			}
			else
			{
				$boolAsigUserRol = true;
			}
		}
		else
		{
			$strStatus  = 'ERROR';
			$strMessage = 'Transacción fallida';
		}
		
		$objResponse['strStatus']       = $strStatus;
		$objResponse['strMessage']      = $strMessage;
		$objResponse['boolAsigUserRol'] = $boolAsigUserRol;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'validateOnlineCamara'
	 * Función utilizada para validar el estado online de una cámara en el portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $intIdCamara
	 * @param $strTipoActivacionCamara
	 *
	 * @return  $objResponse
	 */
	public function validateOnlineCamara($intIdCamara, $strTipoActivacionCamara)
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = $this->strStatusGlobal;
		$strMessage     = $this->strMessageGlobal;
		$boolStatusCam  = false;
		
		$strToken = $this->getAuthTokenCustomer($this->strUserNetlifePortal,
		                                        $this->strPassNetlifePortal,
		                                        $this->strApiKeyNetlifePortal);
		
		if($strToken)
		{
			$objHeaders[CURLOPT_HTTPHEADER] = array("Accept: application/json",
			                                        "x-api-key: ".$this->strApiKeyNetlifePortal,
			                                        'Authorization: Bearer '.$strToken,);
			$objHeaders[CURLOPT_TIMEOUT]    = $this->intWs3dEYETimeOut;
			
			$strUrlFinal     = str_replace("cameraId", $intIdCamara, $this->strUrlValidateOnlineCamara);
			$arrayRespJsonWS = $this->restClient->get($strUrlFinal, $objHeaders);
			$arrayResultado  = json_decode($arrayRespJsonWS['result'], true);
			if(($arrayResultado['error'] || $arrayResultado['errors']) || $arrayRespJsonWS["error"])
			{
				$strErrorDef = "";
				foreach($arrayResultado['errors'] as $error)
				{
					$strErrorDef = $strErrorDef.$error["code"]." - ".$error["message"]."| ";
				}
				$strErrorMesg = $arrayRespJsonWS["error"] ? $arrayRespJsonWS["error"] : $strErrorDef;
				$this->serviceUtil->insertError('Telcos+',
				                                'PortalNetlifeCam3dEYEService->validateOnlineCamara',
				                                $arrayResultado['error_description'] ? $arrayResultado['error_description'] : $strErrorMesg,
				                                $strUsrCreacion,
				                                '127.0.0.1');
				
				if($arrayRespJsonWS["error"])
				{
					throw new Exception('Ha ocurrido un problema. Por favor notifique a Sistemas!');
				}
			}
			else
			{
				if($strTipoActivacionCamara == "P2P")
				{
					if($arrayResultado["rtspState"]["isAvailable"]
					   && $arrayResultado["onvifState"]["isAvailable"])
					{
						$boolStatusCam = true;
					}
				}
				elseif($strTipoActivacionCamara == "ONVIF")
				{
					if($arrayResultado["onvifState"]["isAvailable"])
					{
						$boolStatusCam = true;
					}
				}
				elseif($strTipoActivacionCamara == "GENERIC")
				{
					if($arrayResultado["rtspState"]["isAvailable"])
					{
						$boolStatusCam = true;
					}
				}
			}
		}
		else
		{
			$strStatus  = 'ERROR';
			$strMessage = 'Transacción fallida';
		}
		
		$objResponse['strStatus']     = $strStatus;
		$objResponse['strMessage']    = $strMessage;
		$objResponse['boolStatusCam'] = $boolStatusCam;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'deleteCamaraById'
	 * Función utilizada para eliminar una cámara en el portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $intIdCamara
	 *
	 * @return  $objResponse
	 */
	public function deleteCamaraById($intIdCamara)
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = $this->strStatusGlobal;
		$strMessage     = $this->strMessageGlobal;
		$boolDeleteCam  = false;
		
		$strToken = $this->getAuthTokenCustomer($this->strUserNetlifePortal,
		                                        $this->strPassNetlifePortal,
		                                        $this->strApiKeyNetlifePortal);
		
		if($strToken)
		{
			$objHeaders[CURLOPT_HTTPHEADER] = array("Accept: application/json",
			                                        "x-api-key: ".$this->strApiKeyNetlifePortal,
			                                        'Authorization: Bearer '.$strToken,);
			$objHeaders[CURLOPT_TIMEOUT]    = $this->intWs3dEYETimeOut;
			
			$strUrlFinal     = $this->strUrlCamaras."/".$intIdCamara;
			$arrayRespJsonWS = $this->restClient->delete($strUrlFinal, $objHeaders);
			$arrayResultado  = json_decode($arrayRespJsonWS['result'], true);
			if(($arrayResultado['error'] || $arrayResultado['errors']) || $arrayRespJsonWS["error"])
			{
				$strErrorDef = "";
				foreach($arrayResultado['errors'] as $error)
				{
					$strErrorDef = $strErrorDef.$error["code"]." - ".$error["message"]."| ";
				}
				$strErrorMesg = $arrayRespJsonWS["error"] ? $arrayRespJsonWS["error"] : $strErrorDef;
				$this->serviceUtil->insertError('Telcos+',
				                                'PortalNetlifeCam3dEYEService->deleteCamaraById',
				                                $arrayResultado['error_description'] ? $arrayResultado['error_description'] : $strErrorMesg,
				                                $strUsrCreacion,
				                                '127.0.0.1');
				
				if($arrayRespJsonWS["error"])
				{
					throw new Exception('Ha ocurrido un problema. Por favor notifique a Sistemas!');
				}
			}
			else
			{
				$boolDeleteCam = true;
			}
		}
		else
		{
			$strStatus  = 'ERROR';
			$strMessage = 'Transacción fallida';
		}
		
		$objResponse['strStatus']     = $strStatus;
		$objResponse['strMessage']    = $strMessage;
		$objResponse['boolDeleteCam'] = $boolDeleteCam;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'deleteUserById'
	 * Función utilizada para eliminar un usuario en el portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $intIdUser
	 *
	 * @return  $objResponse
	 */
	public function deleteUserById($intIdUser)
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = $this->strStatusGlobal;
		$strMessage     = $this->strMessageGlobal;
		$boolDeleteUser = false;
		
		$strToken = $this->getAuthTokenCustomer($this->strUserNetlifePortal,
		                                        $this->strPassNetlifePortal,
		                                        $this->strApiKeyNetlifePortal);
		
		if($strToken)
		{
			$objHeaders[CURLOPT_HTTPHEADER] = array("Accept: application/json",
			                                        "x-api-key: ".$this->strApiKeyNetlifePortal,
			                                        'Authorization: Bearer '.$strToken,);
			$objHeaders[CURLOPT_TIMEOUT]    = $this->intWs3dEYETimeOut;
			
			$strUrlFinal     = str_replace("userId", $intIdUser, $this->strUrlUserById);
			$arrayRespJsonWS = $this->restClient->delete($strUrlFinal, $objHeaders);
			$arrayResultado  = json_decode($arrayRespJsonWS['result'], true);
			if(($arrayResultado['error'] || $arrayResultado['errors']) || $arrayRespJsonWS["error"])
			{
				$strErrorDef = "";
				foreach($arrayResultado['errors'] as $error)
				{
					$strErrorDef = $strErrorDef.$error["code"]." - ".$error["message"]."| ";
				}
				$strErrorMesg = $arrayRespJsonWS["error"] ? $arrayRespJsonWS["error"] : $strErrorDef;
				$this->serviceUtil->insertError('Telcos+',
				                                'PortalNetlifeCam3dEYEService->deleteUserById',
				                                $arrayResultado['error_description'] ? $arrayResultado['error_description'] : $strErrorMesg,
				                                $strUsrCreacion,
				                                '127.0.0.1');
				
				if($arrayRespJsonWS["error"])
				{
					throw new Exception('Ha ocurrido un problema. Por favor notifique a Sistemas!');
				}
			}
			else
			{
				$boolDeleteUser = true;
			}
		}
		else
		{
			$strStatus  = 'ERROR';
			$strMessage = 'Transacción fallida';
		}
		
		$objResponse['strStatus']      = $strStatus;
		$objResponse['strMessage']     = $strMessage;
		$objResponse['boolDeleteUser'] = $boolDeleteUser;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'getDatosRolById'
	 * Función utilizada para retornar los datos de un rol del portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $intIdRol3DEYE
	 *
	 * @return  $objResponse
	 */
	public function getDatosRolById($intIdRol3DEYE)
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = $this->strStatusGlobal;
		$strMessage     = $this->strMessageGlobal;
		$arrayData      = "";
		
		$strToken = $this->getAuthTokenCustomer($this->strUserNetlifePortal,
		                                        $this->strPassNetlifePortal,
		                                        $this->strApiKeyNetlifePortal);
		
		if($strToken)
		{
			$objHeaders[CURLOPT_HTTPHEADER] = array("Accept: application/json",
			                                        "x-api-key: ".$this->strApiKeyNetlifePortal,
			                                        'Authorization: Bearer '.$strToken,);
			$objHeaders[CURLOPT_TIMEOUT]    = $this->intWs3dEYETimeOut;
			
			$strUrlFinal                    = str_replace("roleId", $intIdRol3DEYE, $this->strUrlRolGetUser);
			$arrayRespJsonWS                = $this->restClient->get($strUrlFinal, $objHeaders);
			$arrayResultado                 = json_decode($arrayRespJsonWS['result'], true);
			if(($arrayResultado['error'] || $arrayResultado['errors']) || $arrayRespJsonWS["error"])
			{
				$strErrorDef = "";
				foreach($arrayResultado['errors'] as $error)
				{
					$strErrorDef = $strErrorDef.$error["code"]." - ".$error["message"]."| ";
				}
				$strErrorMesg = $arrayRespJsonWS["error"] ? $arrayRespJsonWS["error"] : $strErrorDef;
				$this->serviceUtil->insertError('Telcos+',
				                                'PortalNetlifeCam3dEYEService->getDatosRolById',
				                                $arrayResultado['error_description'] ? $arrayResultado['error_description'] : $strErrorMesg,
				                                $strUsrCreacion,
				                                '127.0.0.1');
				
				if($arrayRespJsonWS["error"])
				{
					throw new Exception('Ha ocurrido un problema. Por favor notifique a Sistemas!');
				}
				
				$strStatus  = 'ERROR';
				$strMessage = 'Transacción fallida';
			}
			else
			{
				// Retornar todos los datos del rol
				$arrayRespDatosCompletosRol = $this->getDatosCompletosRolById($intIdRol3DEYE);
				if($arrayRespDatosCompletosRol['strStatus'] == "OK")
				{
					$arrayDatosCompletosRol = $arrayRespDatosCompletosRol["arrayData"];
					$arrayData              = array_merge($arrayDatosCompletosRol, $arrayResultado);
				}
				else
				{
					$strStatus  = 'ERROR';
					$strMessage = 'Transacción fallida';
				}
			}
		}
		else
		{
			$strStatus  = 'ERROR';
			$strMessage = 'Transacción fallida';
		}
		
		$objResponse['strStatus']  = $strStatus;
		$objResponse['strMessage'] = $strMessage;
		$objResponse['arrayData']  = $arrayData;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'validateCamaraByName'
	 * Función utilizada para validar si existe una cámara en el portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $strNombreCamara
	 *
	 * @return  $objResponse
	 */
	public function validateCamaraByName($strNombreCamara)
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = $this->strStatusGlobal;
		$strMessage     = $this->strMessageGlobal;
		$arrayData      = "";
		
		$strToken = $this->getAuthTokenCustomer($this->strUserNetlifePortal,
		                                        $this->strPassNetlifePortal,
		                                        $this->strApiKeyNetlifePortal);
		
		if($strToken)
		{
			$objHeaders[CURLOPT_HTTPHEADER] = array("Accept: application/json",
			                                        "x-api-key: ".$this->strApiKeyNetlifePortal,
			                                        'Authorization: Bearer '.$strToken,);
			$objHeaders[CURLOPT_TIMEOUT]    = $this->intWs3dEYETimeOut;
			
			$strUrlFinal                    = $this->strUrlCamaras;
			$arrayRespJsonWS                = $this->restClient->get($strUrlFinal, $objHeaders);
			$arrayResultado                 = json_decode($arrayRespJsonWS['result'], true);
			if(($arrayResultado['error'] || $arrayResultado['errors']) || $arrayRespJsonWS["error"])
			{
				$strErrorDef = "";
				foreach($arrayResultado['errors'] as $error)
				{
					$strErrorDef = $strErrorDef.$error["code"]." - ".$error["message"]."| ";
				}
				$strErrorMesg = $arrayRespJsonWS["error"] ? $arrayRespJsonWS["error"] : $strErrorDef;
				$this->serviceUtil->insertError('Telcos+',
				                                'PortalNetlifeCam3dEYEService->validateCamaraByName',
				                                $arrayResultado['error_description'] ? $arrayResultado['error_description'] : $strErrorMesg,
				                                $strUsrCreacion,
				                                '127.0.0.1');
				
				if($arrayRespJsonWS["error"])
				{
					throw new Exception('Ha ocurrido un problema. Por favor notifique a Sistemas!');
				}
				
				$strStatus  = 'ERROR';
				$strMessage = 'Transacción fallida';
			}
			else
			{
				foreach($arrayResultado["cameras"] as $camera)
				{
					if($camera["name"] == $strNombreCamara)
					{
						$arrayData = $camera;
						break;
					}
				}
			}
		}
		else
		{
			$strStatus  = 'ERROR';
			$strMessage = 'Transacción fallida';
		}
		
		$objResponse['strStatus']  = $strStatus;
		$objResponse['strMessage'] = $strMessage;
		$objResponse['arrayData']  = $arrayData;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'removerCamaraByRol'
	 * Función utilizada para remover una cámara de un rol en el portal 3dEYE.
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 03-12-2019
	 *
	 * @param $intIdRol3dEYE id Rol 3dEYE
	 * @param $intIdCam3dEYE id Cámara 3dEYE
	 *
	 * @return  $objResponse
	 */
	public function removerCamaraByRol($intIdRol3dEYE, $intIdCam3dEYE)
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = $this->strStatusGlobal;
		$strMessage     = $this->strMessageGlobal;
		$boolRemoCamRol = false;
		
		$strToken = $this->getAuthTokenCustomer($this->strUserNetlifePortal,
		                                        $this->strPassNetlifePortal,
		                                        $this->strApiKeyNetlifePortal);
		
		if($strToken)
		{
			$objHeaders[CURLOPT_HTTPHEADER] = array("Accept: application/json",
			                                        "x-api-key: ".$this->strApiKeyNetlifePortal,
			                                        'Authorization: Bearer '.$strToken,);
			$objHeaders[CURLOPT_TIMEOUT]    = $this->intWs3dEYETimeOut;
			
			$strUrlAsignarRol = str_replace("roleId", $intIdRol3dEYE, $this->strUrlCamaraRol);
			$strUrlFinal      = str_replace("cameraId", $intIdCam3dEYE, $strUrlAsignarRol);
			$arrayRespJsonWS  = $this->restClient->delete($strUrlFinal, $objHeaders);
			$arrayResultado   = json_decode($arrayRespJsonWS['result'], true);
			if(($arrayResultado['error'] || $arrayResultado['errors']) || $arrayRespJsonWS["error"])
			{
				$strErrorDef = "";
				foreach($arrayResultado['errors'] as $error)
				{
					$strErrorDef = $strErrorDef.$error["code"]." - ".$error["message"]."| ";
				}
				$strErrorMesg = $arrayRespJsonWS["error"] ? $arrayRespJsonWS["error"] : $strErrorDef;
				$this->serviceUtil->insertError('Telcos+',
				                                'PortalNetlifeCam3dEYEService->asignarCamaraRol',
				                                $arrayResultado['error_description'] ? $arrayResultado['error_description'] : $strErrorMesg,
				                                $strUsrCreacion,
				                                '127.0.0.1');
				
				if($arrayRespJsonWS["error"])
				{
					throw new Exception('Ha ocurrido un problema. Por favor notifique a Sistemas!');
				}
			}
			else
			{
				$boolRemoCamRol = true;
			}
		}
		else
		{
			$strStatus  = 'ERROR';
			$strMessage = 'Transacción fallida';
		}
		
		$objResponse['strStatus']      = $strStatus;
		$objResponse['strMessage']     = $strMessage;
		$objResponse['boolRemoCamRol'] = $boolRemoCamRol;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'getAuthTokenCustomer'
	 * Función utilizada para generar token de seguridad del portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $strUserPortal
	 * @param $strPassPortal
	 * @param $strApiKey
	 * @param string $strGrantType
	 * @param string $strClientId
	 *
	 * @return string $strToken
	 */
	public function getAuthTokenCustomer($strUserPortal, $strPassPortal, $strApiKey)
	{
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strGrantType   = "password";
		$strClientId    = "ExternalApi";
		
		$objHeaders[CURLOPT_HTTPHEADER] = array("x-api-key: ".$strApiKey,);
		$objHeaders[CURLOPT_TIMEOUT]    = $this->intWs3dEYETimeOut;
		
		// Lleno el formulario URL Encoded
		$strDataRequest = "";
		$strDataRequest .= "grant_type=".urlencode($strGrantType)."&";
		$strDataRequest .= "client_id=".urlencode($strClientId)."&";
		$strDataRequest .= "username=".urlencode($strUserPortal)."&";
		$strDataRequest .= "password=".urlencode($strPassPortal)."&";
		
		$strUrlFinal     = $this->strUrlToken;
		$arrayRespJsonWS = $this->restClient->postFormURLEncoded($strUrlFinal,
		                                                         $strDataRequest,
		                                                         $objHeaders);
		$arrayResultado  = json_decode($arrayRespJsonWS['result'], true);
		
		if(($arrayResultado['error'] || $arrayResultado['errors']) || $arrayRespJsonWS["error"])
		{
			$strErrorDef = "";
			foreach($arrayResultado['errors'] as $error)
			{
				$strErrorDef = $strErrorDef.$error["code"]." - ".$error["message"]."| ";
			}
			$strErrorMesg = $arrayRespJsonWS["error"] ? $arrayRespJsonWS["error"] : $strErrorDef;
			$this->serviceUtil->insertError('Telcos+',
			                                'PortalNetlifeCam3dEYEService->getAuthTokenCustomer',
			                                $arrayResultado['error_description'] ? $arrayResultado['error_description'] : $strErrorMesg,
			                                $strUsrCreacion,
			                                '127.0.0.1');
			
			throw new Exception('Ha ocurrido un problema. Por favor notifique a Sistemas!');
		}
		else
		{
			$strToken = $arrayResultado['access_token'];
		}
		
		return $strToken;
	}
	
	/**
	 * Documentación para el método 'generarClaveAleatoria'.
	 *
	 * Función que genera una clave aleatoria de longitud X, donde X se encuentra en un rango entre intMinLongitudClave y intMaxLongitudClave.
	 * La clave generada tendrá al menos 1 letra minúscula, 1 letra mayúscula, 1 número y un caracter especial.
	 *
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param type array $arrayParametros [
	 *                                      "intMinLongitudClave"   => longitud mínima de la clave que se desea generar
	 *                                      "intMaxLongitudClave"   => longitud máxima de la clave que se desea generar
	 *                                      ]
	 *
	 * @return string $strClaveGeneradaFinal
	 *
	 */
	public function generarClaveAleatoria($arrayParametros)
	{
		//Generar la longitud de la clave de manera aleatoria
		$intMinLongitudClave = $arrayParametros["intMinLongitudClave"];
		$intMaxLongitudClave = $arrayParametros["intMaxLongitudClave"];
		$intLongitudClave    = $arrayParametros["intLongitudClave"];
		
		if(isset($intLongitudClave) && !empty($intLongitudClave))
		{
			$intLongitudFinalClave = $intLongitudClave;
		}
		else
		{
			$arrayLongsPosibleClave = array();
			
			for($intContLongClave = $intMinLongitudClave; $intContLongClave <= $intMaxLongitudClave; $intContLongClave++)
			{
				$arrayLongsPosibleClave[] = $intContLongClave;
			}
			
			$intLongitudFinalClave = $arrayLongsPosibleClave[array_rand($arrayLongsPosibleClave)];
		}
		
		$arrayCaracteres               = array();
		$arrayCaracteres["lower_case"] = 'abcdefghijklmnopqrstuvwxyz';
		$arrayCaracteres["upper_case"] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$arrayCaracteres["numbers"]    = '1234567890';
		
		$strTodosCaracteres = '';
		$strClaveGenerada   = '';
		foreach($arrayCaracteres as $strCaracteresPorTipo)
		{
			$arrayCaracteresPorTipo = str_split($strCaracteresPorTipo);
			$strClaveGenerada       .= $strCaracteresPorTipo[array_rand($arrayCaracteresPorTipo)];
			$strTodosCaracteres     .= $strCaracteresPorTipo;
		}
		
		$arrayTodosCaracteres = str_split($strTodosCaracteres);
		for($intNum = 0; $intNum < $intLongitudFinalClave - count($arrayCaracteres); $intNum++)
		{
			$strClaveGenerada .= $arrayTodosCaracteres[array_rand($arrayTodosCaracteres)];
		}
		$strClaveGeneradaFinal = str_shuffle($strClaveGenerada);
		
		return $strClaveGeneradaFinal;
	}
	
	/**
	 * Función que sirve para enviar el correo al cliente al activar un servicio
	 *
	 * @author  Lizbeth Cruz <mlcruz@telconet.ec>
	 * @version 1.0 08-06-2017
	 *
	 * @param array $arrayParametros
	 *                          [
	 *
	 *                              "objPersonaEmpresaRol"  => objeto persona empresa rol del cliente
	 *                              "strUsrCreacion"        => usuario de creación
	 *                              "strIpClient"           => ip
	 *                              "strCodPlantilla"       => código de la plantilla que se enviará al correo del cliente
	 *                              "strAsunto"             => asunto del correo
	 *                              "arrayDataMail"         => parámetros requeridos en la plantilla
	 *                          ]
	 *
	 * @return array $arrayRespuesta
	 *                              [
	 *                                  "status"    => "OK" o "ERROR"
	 *                                  "strMsj"    => mensaje de información
	 *                              ]
	 *
	 */
	public function enviarInformacionCorreoNetlifeCam($arrayParametros)
	{
		$arrayRespuesta     = array();
		$strStatus          = "ERROR";
		$strMensaje         = "";
		try
		{
			$this->serviceEnvioPlantilla->generarEnvioPlantilla($arrayParametros["strAsunto"],
			                                                    $arrayParametros["strDestinatario"],
			                                                    $arrayParametros["strCodPlantilla"],
			                                                    $arrayParametros["arrayDataMail"],
			                                                    '',
			                                                    '',
			                                                    '',
			                                                    null,
			                                                    false,
			                                                    'netcam@netlife.info.ec');
			$strStatus  = 'OK';
			$strMensaje = 'Información Enviada Exitosamente!!!';
		}
		catch(\Exception $e)
		{
			$strMensaje = 'No se envió la información del usuario al correo';
			$this->serviceUtil->insertError('Telcos+',
			                                'PortalNetlifeCamService->enviarInformacionCorreoNetlifeCam',
			                                $e->getMessage(),
			                                $arrayParametros["strUsrCreacion"],
			                                $arrayParametros["strIpClient"]);
		}
		$arrayRespuesta['strStatus'] = $strStatus;
		$arrayRespuesta['strMsj']    = $strMensaje;
		
		return $arrayRespuesta;
	}
	
	/**
	 * Documentación para la función 'cortarServicioNetCam'
	 * Función utilizada para cortar el servicio NetCam en telcos y en el portal 3dEYE.
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 02-12-2019
	 *
	 * @param $intIdServicio id servicio
	 * @param $intIdAccion   id acción
	 *
	 * @return  $objResponse
	 */
	public function cortarServicioNetCam($intIdServicio, $intIdAccion)
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = "ERROR";
		
		try
		{
			$arrayParamsCortarServicio = array("intIdServicio" => $intIdServicio,
			                                   "intIdAccion"   => $intIdAccion,
			                                   "strUser"       => $strUsrCreacion);
			
			$arrayCortarServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')
			                                         ->cortarServicioNetCam($arrayParamsCortarServicio);
			
			$strStatus  = $arrayCortarServicio["strStatus"];
			$strMessage = $arrayCortarServicio["strMensaje"];
		}
		catch(Exception $e)
		{
			$strMessage = "Ha ocurrido un problema. Por favor notifique a Sistemas!";
			$this->serviceUtil->insertError('Telcos+',
			                                'PortalNetlifeCamService->cortarServicioNetCam',
			                                $e->getMessage(),
			                                $strUsrCreacion,
			                                '127.0.0.1');
		}
		
		$objResponse['strStatus']  = $strStatus;
		$objResponse['strMessage'] = $strMessage;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'reconectarServicioNetCam'
	 * Función utilizada para reconectar el servicio NetCam en telcos y en el portal 3dEYE.
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 02-12-2019
	 *
	 * @param $intIdServicio id servicio
	 * @param $intIdAccion   id acción
	 *
	 * @return  $objResponse
	 */
	public function reconectarServicioNetCam($intIdServicio, $intIdAccion)
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = "ERROR";
		
		try
		{
			$arrayParamsCortarServicio = array("intIdServicio" => $intIdServicio,
			                                   "intIdAccion"   => $intIdAccion,
			                                   "strUser"       => $strUsrCreacion);
			
			$arrayCortarServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')
			                                         ->reconectarServicioNetCam($arrayParamsCortarServicio);
			
			$strStatus  = $arrayCortarServicio["strStatus"];
			$strMessage = $arrayCortarServicio["strMensaje"];
		}
		catch(Exception $e)
		{
			$strMessage = "Ha ocurrido un problema. Por favor notifique a Sistemas!";
			$this->serviceUtil->insertError('Telcos+',
			                                'PortalNetlifeCamService->reconectarServicioNetCam',
			                                $e->getMessage(),
			                                $strUsrCreacion,
			                                '127.0.0.1');
		}
		
		$objResponse['strStatus']  = $strStatus;
		$objResponse['strMessage'] = $strMessage;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'cancelarServicioNetCam'
	 * Función utilizada para cancelar el servicio NetCam en telcos y en el portal 3dEYE.
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 02-12-2019
	 *
	 * @param $intIdServicio id servicio
	 * @param $intIdAccion   id acción
	 * @param $strEsMasivo   S = Masivo, N = No masivo
	 *
	 * @return  $objResponse
	 */
	public function cancelarServicioNetCam($intIdServicio, $intIdAccion, $strEsMasivo = "N")
	{
		$objResponse    = array();
		$strUsrCreacion = $this->session->get('user') ? $this->session->get('user') : 'telcos';
		$strStatus      = "ERROR";
		
		try
		{
			$arrayParamsCortarServicio = array("intIdServicio" => $intIdServicio,
			                                   "intIdAccion"   => $intIdAccion,
			                                   "strEsMasivo"   => $strEsMasivo,
			                                   "strUser"       => $strUsrCreacion);
			
			$arrayCortarServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')
			                                         ->cancelarServicioNetCam($arrayParamsCortarServicio);
			
			$strStatus  = $arrayCortarServicio["strStatus"];
			$strMessage = $arrayCortarServicio["strMensaje"];
		}
		catch(Exception $e)
		{
			$strMessage = "Ha ocurrido un problema. Por favor notifique a Sistemas!";
			$this->serviceUtil->insertError('Telcos+',
			                                'PortalNetlifeCamService->cancelarServicioNetCam',
			                                $e->getMessage(),
			                                $strUsrCreacion,
			                                '127.0.0.1');
		}
		
		$objResponse['strStatus']  = $strStatus;
		$objResponse['strMessage'] = $strMessage;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'activarServicioNetCam'
	 * Función utilizada para activar el servicio NetCam en telcos y en el portal 3dEYE.
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 02-12-2019
	 *
	 * @param array $arrayParams
	 *                          [
	 *                              "objServicio"               => objeto del servicio
	 *                              "intIdAccion"               => id acción
	 *                              "intIdModeloCam"            => modelo cámara
	 *                              "strTipoActivacionCamara"   => tipo de activación
	 *                              "arrayDataCreateCamara"     => datos de la cámara
	 *                              "arrayDataCreateRol"        => datos del rol
	 *                              "arrayDataCreateUser"       => datos del usuario
	 *                              "strMACCam"                 => mac cámara
	 *                              "strSerieCam"               => serie cámara
	 *                              "strUsrCreacion"            => usuario de creación
	 *                              "strIpClient"               => ip
	 *                          ]
	 *
	 * @return  $objResponse
	 */
	public function activarServicioNetCam($arrayParams)
	{
		$objResponse             = array();
		$objServicio             = $arrayParams['objServicio'];
		$intIdServicio           = $objServicio->getId();
		$intIdAccion             = $arrayParams['intIdAccion'];
		$intIdModeloCam          = $arrayParams['intIdModeloCam'];
		$strTipoActivacionCamara = $arrayParams['strTipoActivacionCamara'];
		$arrayDataCreateCamara   = $arrayParams['arrayDataCreateCamara'];
		$arrayDataCreateRol      = $arrayParams['arrayDataCreateRol'];
		$arrayDataCreateUser     = $arrayParams['arrayDataCreateUser'];
		$strMACCam               = $arrayParams['strMACCam'];
		$strSerieCam             = $arrayParams['strSerieCam'];
		$strUsrCreacion          = $arrayParams['strUsrCreacion'];
		$strIpClient             = $arrayParams['strIpClient'];
		
		$boolConfirCam       = false;
		$boolEliminarUsuario = false;
		$arrayCamCreada3dEYE = "";
		$strStatus           = "ERROR";
		
		$this->emInfraestructura->beginTransaction();
		$this->emComercial->beginTransaction();
		try
		{
			if(is_object($objServicio))
			{
				$objPunto               = $objServicio->getPuntoId();
				$objInfoServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
				                                            ->findOneByServicioId($objServicio);
				if(is_object($objInfoServicioTecnico))
				{
					if($intIdModeloCam)
					{
						$objModeloElementoCam = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
						                                                ->find($intIdModeloCam);
						if(is_object($objModeloElementoCam))
						{
							// Se crea la cámara en el portal
							if($strTipoActivacionCamara == "P2P")
							{
								$arrayRespCreateCamP2P = $this->createCamaraP2P($arrayDataCreateCamara);
								$arrayCamCreada3dEYE   = $arrayRespCreateCamP2P["arrayData"];
							}
							elseif($strTipoActivacionCamara == "ONVIF")
							{
								$arrayRespCreateCamONVIF = $this->createCamaraONVIF($arrayDataCreateCamara);
								$arrayCamCreada3dEYE     = $arrayRespCreateCamONVIF["arrayData"];
							}
							elseif($strTipoActivacionCamara == "GENERIC")
							{
								$arrayRespCreateCamGENERIC = $this->createCamaraGENERIC($arrayDataCreateCamara);
								$arrayCamCreada3dEYE       = $arrayRespCreateCamGENERIC["arrayData"];
							}
							
							// Confirmar estado de la cámara
							if($arrayCamCreada3dEYE)
							{
								$arrayRespEstOnlineCam3dEYE = $this->validateOnlineCamara($arrayCamCreada3dEYE["id"],
								                                                          $strTipoActivacionCamara);
								$boolConfirCam              = $arrayRespEstOnlineCam3dEYE["boolStatusCam"];
							}
						}
						else
						{
							throw new Exception("No se ha podido encontrar el modelo de la cámara");
						}
					}
					else
					{
						throw new Exception("No se ha enviado el parámetro del modelo de la cámara");
					}
				}
				else
				{
					throw new Exception("No se ha podido encontrar la información técnica del servicio");
				}
				
				// Cámara creada, procedo a crear rol y asignar cámara al rol
				$intIdCam3dEYE   = $arrayCamCreada3dEYE["id"];
				$strNombreCamara = $arrayCamCreada3dEYE["name"];
				if($boolConfirCam)
				{
					$objElementoCam = new InfoElemento();
					$objElementoCam->setNombreElemento($strNombreCamara);
					$objElementoCam->setDescripcionElemento("CAMARA: ".$strNombreCamara." TIPO: ".$strTipoActivacionCamara);
					$objElementoCam->setSerieFisica($strSerieCam);
					$objElementoCam->setModeloElementoId($objModeloElementoCam);
					$objElementoCam->setUsrCreacion($strUsrCreacion);
					$objElementoCam->setFeCreacion(new \DateTime('now'));
					$objElementoCam->setIpCreacion($strIpClient);
					$objElementoCam->setEstado("Activo");
					$this->emInfraestructura->persist($objElementoCam);
					$this->emInfraestructura->flush();
					
					$strObservacionCamHistorial = '<b>Datos Nuevos<b><br>';
					$strObservacionCamHistorial .= 'Tipo: CAMARA<br>';
					$strObservacionCamHistorial .= 'Modelo: '.$objModeloElementoCam->getNombreModeloElemento().'<br>';
					$strObservacionCamHistorial .= 'Descripcion: CAMARA '.$strNombreCamara.'<br>';
					$strObservacionCamHistorial .= 'Serie Fisica: '.$strSerieCam.'<br>';
					
					$intIdElementoCam = $objElementoCam->getId();
					// Agregar los detalles del elemento
					$this->serviceInfoServicioTecnico->ingresarDetalleElemento($objElementoCam,
					                                                           "MAC_CAMARA",
					                                                           "MAC_CAMARA",
					                                                           $strMACCam,
					                                                           $strUsrCreacion,
					                                                           $strIpClient);
					$strObservacionCamHistorial .= 'MAC: '.$strMACCam.'<br>';
					$this->serviceInfoServicioTecnico->ingresarDetalleElemento($objElementoCam,
					                                                           "USER_CAMARA",
					                                                           "USER_CAMARA",
					                                                           $arrayDataCreateCamara["strAdminName"],
					                                                           $strUsrCreacion,
					                                                           $strIpClient);
					$strObservacionCamHistorial .= 'User Cámara: '.$arrayDataCreateCamara["strAdminName"].'<br>';
					$this->serviceInfoServicioTecnico->ingresarDetalleElemento($objElementoCam,
					                                                           "PASS_CAMARA",
					                                                           "PASS_CAMARA",
					                                                           $arrayDataCreateCamara["strAdminPassword"],
					                                                           $strUsrCreacion,
					                                                           $strIpClient);
					$strObservacionCamHistorial .= 'Pass Cámara: '.$arrayDataCreateCamara["strAdminPassword"].'<br>';
					$this->serviceInfoServicioTecnico->ingresarDetalleElemento($objElementoCam,
					                                                           "TIPO_ACTIVACION_CAMARA",
					                                                           "TIPO_ACTIVACION_CAMARA",
					                                                           $strTipoActivacionCamara,
					                                                           $strUsrCreacion,
					                                                           $strIpClient);
					$strObservacionCamHistorial .= 'Tipo de activación: '.$strTipoActivacionCamara.'<br>';
					
					if($strTipoActivacionCamara == "P2P")
					{
						$strDetalleCodigoPush = "CODIGO_PUSH_CAMARA";
						$this->serviceInfoServicioTecnico->ingresarDetalleElemento($objElementoCam,
						                                                           $strDetalleCodigoPush,
						                                                           $strDetalleCodigoPush,
						                                                           $arrayDataCreateCamara["strRegistrationCode"],
						                                                           $strUsrCreacion,
						                                                           $strIpClient);
						$strObservacionCamHistorial .= 'Código Push: '.$arrayDataCreateCamara["strRegistrationCode"].'<br>';
					}
					elseif($strTipoActivacionCamara == "ONVIF")
					{
						$strDetalleDDNSONVIF = "DDNS_CAMARA";
						$this->serviceInfoServicioTecnico->ingresarDetalleElemento($objElementoCam,
						                                                           $strDetalleDDNSONVIF,
						                                                           $strDetalleDDNSONVIF,
						                                                           $arrayDataCreateCamara["strDDNSCamaraONVIF"],
						                                                           $strUsrCreacion,
						                                                           $strIpClient);
						$strObservacionCamHistorial .= 'DDNS: '.$arrayDataCreateCamara["strDDNSCamaraONVIF"].'<br>';
						$strDetallePuertoHttpONVIF  = "PUERTO_HTTP_CAMARA";
						$this->serviceInfoServicioTecnico->ingresarDetalleElemento($objElementoCam,
						                                                           $strDetallePuertoHttpONVIF,
						                                                           $strDetallePuertoHttpONVIF,
						                                                           $arrayDataCreateCamara["strPuertoHTTPCamaraONVIF"],
						                                                           $strUsrCreacion,
						                                                           $strIpClient);
						$strObservacionCamHistorial .= 'Puerto HTTP: '.$arrayDataCreateCamara["strPuertoHTTPCamaraONVIF"].'<br>';
						$strDetallePuertoRtspONVIF  = "PUERTO_RTSP_CAMARA";
						$this->serviceInfoServicioTecnico->ingresarDetalleElemento($objElementoCam,
						                                                           $strDetallePuertoRtspONVIF,
						                                                           $strDetallePuertoRtspONVIF,
						                                                           $arrayDataCreateCamara["strPuertoRTSPCamaraONVIF"],
						                                                           $strUsrCreacion,
						                                                           $strIpClient);
						$strObservacionCamHistorial .= 'Puerto RTSP: '.$arrayDataCreateCamara["strPuertoRTSPCamaraONVIF"].'<br>';
					}
					elseif($strTipoActivacionCamara == "GENERIC")
					{
						$strDetalleFabricanteGENERIC = "FABRICANTE_CAMARA";
						$this->serviceInfoServicioTecnico->ingresarDetalleElemento($objElementoCam,
						                                                           $strDetalleFabricanteGENERIC,
						                                                           $strDetalleFabricanteGENERIC,
						                                                           $arrayDataCreateCamara["strDeviceBrandGENERIC"],
						                                                           $strUsrCreacion,
						                                                           $strIpClient);
						$strObservacionCamHistorial .= 'Fabricante: '.$arrayDataCreateCamara["strDeviceBrandGENERIC"].'<br>';
						$strDetalleDDNSGENERIC      = "DDNS_CAMARA";
						$this->serviceInfoServicioTecnico->ingresarDetalleElemento($objElementoCam,
						                                                           $strDetalleDDNSGENERIC,
						                                                           $strDetalleDDNSGENERIC,
						                                                           $arrayDataCreateCamara["strDDNSCamaraGENERIC"],
						                                                           $strUsrCreacion,
						                                                           $strIpClient);
						$strObservacionCamHistorial  .= 'DDNS: '.$arrayDataCreateCamara["strDDNSCamaraGENERIC"].'<br>';
						$strDetallePuertoHttpGENERIC = "PUERTO_HTTP_CAMARA";
						$this->serviceInfoServicioTecnico->ingresarDetalleElemento($objElementoCam,
						                                                           $strDetallePuertoHttpGENERIC,
						                                                           $strDetallePuertoHttpGENERIC,
						                                                           $arrayDataCreateCamara["strPuertoHTTPCamaraGENERIC"],
						                                                           $strUsrCreacion,
						                                                           $strIpClient);
						$strObservacionCamHistorial .= 'Puerto HTTP: '.$arrayDataCreateCamara["strPuertoHTTPCamaraGENERIC"].'<br>';
						$strDetalleRTSPGENERIC      = "RTSP_CAMARA";
						$this->serviceInfoServicioTecnico->ingresarDetalleElemento($objElementoCam,
						                                                           $strDetalleRTSPGENERIC,
						                                                           $strDetalleRTSPGENERIC,
						                                                           $arrayDataCreateCamara["strRTSPCamaraGENERIC"],
						                                                           $strUsrCreacion,
						                                                           $strIpClient);
						$strObservacionCamHistorial  .= 'RTSP: '.$arrayDataCreateCamara["strRTSPCamaraGENERIC"].'<br>';
						$strDetallePuertoRtspGENERIC = "PUERTO_RTSP_CAMARA";
						$this->serviceInfoServicioTecnico->ingresarDetalleElemento($objElementoCam,
						                                                           $strDetallePuertoRtspGENERIC,
						                                                           $strDetallePuertoRtspGENERIC,
						                                                           $arrayDataCreateCamara["strPuertoRTSPCamaraGENERIC"],
						                                                           $strUsrCreacion,
						                                                           $strIpClient);
						$strObservacionCamHistorial .= 'Puerto RTSP: '.$arrayDataCreateCamara["strPuertoRTSPCamaraGENERIC"].'<br>';
					}
					$objInfoHistorialElementoCam = new InfoHistorialElemento();
					$objInfoHistorialElementoCam->setElementoId($objElementoCam);
					$objInfoHistorialElementoCam->setObservacion($strObservacionCamHistorial);
					$objInfoHistorialElementoCam->setFeCreacion(new \DateTime('now'));
					$objInfoHistorialElementoCam->setUsrCreacion($strUsrCreacion);
					$objInfoHistorialElementoCam->setIpCreacion($strIpClient);
					$objInfoHistorialElementoCam->setEstadoElemento("Activo");
					$this->emInfraestructura->persist($objInfoHistorialElementoCam);
					$this->emInfraestructura->flush();
					
					$objInfoServicioTecnico->setElementoClienteId($intIdElementoCam);
					$this->emComercial->persist($objInfoServicioTecnico);
					$this->emComercial->flush();
					
					$objProductoCamaraIp = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
					                                         ->findOneBy(array("nombreTecnico" => "CAMARA IP",
					                                                           "estado"        => "Activo"));
					
					if(is_object($objProductoCamaraIp))
					{
						$arrayParametrosCaractCam3dEYE = array("descripcionCaracteristica" => "CAMARA 3DEYE",
						                                       "estado"                    => "Activo");
						
						$objCaractCam3dEYE = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
						                                       ->findOneBy($arrayParametrosCaractCam3dEYE);
						
						if(is_object($objCaractCam3dEYE))
						{
							$objProdCaractCam3dEYE = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
							                                           ->findOneBy(array("productoId"       => $objProductoCamaraIp,
							                                                             "caracteristicaId" => $objCaractCam3dEYE,
							                                                             "estado"           => "Activo"));
							if(is_object($objProdCaractCam3dEYE))
							{
								$intIdProdCaractCam3dEYE  = $objProdCaractCam3dEYE->getId();
								$objServProdCaracCam3dEYE = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
								                                              ->findOneBy(array("servicioId"                => $intIdServicio,
								                                                                "productoCaracterisiticaId" => $intIdProdCaractCam3dEYE,
								                                                                "estado"                    => "Activo"));
								
								if(is_object($objServProdCaracCam3dEYE))
								{
									$objServProdCaracCam3dEYE->setValor($intIdCam3dEYE);
									$objServProdCaracCam3dEYE->setFeUltMod(new \DateTime('now'));
									$objServProdCaracCam3dEYE->setUsrUltMod($strUsrCreacion);
									$this->emComercial->persist($objServProdCaracCam3dEYE);
									$this->emComercial->flush();
								}
								else
								{
									$objServProdCaracCam3dEYE = new InfoServicioProdCaract();
									$objServProdCaracCam3dEYE->setServicioId($intIdServicio);
									$objServProdCaracCam3dEYE->setProductoCaracterisiticaId($intIdProdCaractCam3dEYE);
									$objServProdCaracCam3dEYE->setValor($intIdCam3dEYE);
									$objServProdCaracCam3dEYE->setEstado("Activo");
									$objServProdCaracCam3dEYE->setUsrCreacion($strUsrCreacion);
									$objServProdCaracCam3dEYE->setFeCreacion(new \DateTime('now'));
									$this->emComercial->persist($objServProdCaracCam3dEYE);
									$this->emComercial->flush();
								}
							}
							else
							{
								throw new Exception("No se ha encontrado un producto asociado a la característica cámara 3dEYE");
							}
						}
						else
						{
							throw new Exception("No se ha encontrado la característica asociada al plan para determinar la cámara del servicio");
						}
						
						$arrayParametrosCaractRol3dEYE = array("descripcionCaracteristica" => "ROL 3DEYE",
						                                       "estado"                    => "Activo");
						
						$objCaractRol3dEYE = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
						                                       ->findOneBy($arrayParametrosCaractRol3dEYE);
						if(is_object($objCaractRol3dEYE))
						{
							$objProdCaractRol3dEYE = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
							                                           ->findOneBy(array("productoId"       => $objProductoCamaraIp,
							                                                             "caracteristicaId" => $objCaractRol3dEYE,
							                                                             "estado"           => "Activo"));
							if(is_object($objProdCaractRol3dEYE))
							{
								$intIdProdCaractRol3dEYE  = $objProdCaractRol3dEYE->getId();
								$objServProdCaracRol3dEYE = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
								                                              ->findOneBy(array("servicioId"                => $intIdServicio,
								                                                                "productoCaracterisiticaId" => $intIdProdCaractRol3dEYE,
								                                                                "estado"                    => "Activo"));
								
								$objInfoPuntoCaracRol3dEYE = $this->emComercial->getRepository("schemaBundle:InfoPuntoCaracteristica")
								                                               ->findOneBy(array("puntoId"          => $objPunto,
								                                                                 "caracteristicaId" => $objCaractRol3dEYE,
								                                                                 "estado"           => "Activo"));
								
								$intIdRol3dEYE     = $arrayDataCreateRol["arrayRol3dEYE"]["id"];
								$strNombreRol3dEYE = $arrayDataCreateRol["arrayRol3dEYE"]["name"];
								if(!$arrayDataCreateRol["boolRolExiste"])
								{
									// Verifico si existe el rol caso contrario lo creo
									$arrayRespCreateRol  = $this->createRol($arrayDataCreateRol);
									$arrayRolCreada3dEYE = $arrayRespCreateRol["arrayData"];
									$intIdRol3dEYE       = $arrayRolCreada3dEYE["id"];
									$strNombreRol3dEYE   = $arrayRolCreada3dEYE["name"];
								}
								
								if($intIdRol3dEYE)
								{
									if(is_object($objInfoPuntoCaracRol3dEYE))
									{
										$arrayDatosRol = $this->getDatosRolById($objInfoPuntoCaracRol3dEYE->getValor());
										if(!$arrayDatosRol["arrayData"])
										{
											$objInfoPuntoCaracRol3dEYE->setValor($intIdRol3dEYE);
											$objInfoPuntoCaracRol3dEYE->setFeUltMod(new \DateTime('now'));
											$objInfoPuntoCaracRol3dEYE->setUsrUltMod($strUsrCreacion);
											$this->emComercial->persist($objInfoPuntoCaracRol3dEYE);
											$this->emComercial->flush();
										}
									}
									else
									{
										$objInfoPuntoCaracRol3dEYE = new InfoPuntoCaracteristica();
										$objInfoPuntoCaracRol3dEYE->setPuntoId($objPunto);
										$objInfoPuntoCaracRol3dEYE->setCaracteristicaId($objCaractRol3dEYE);
										$objInfoPuntoCaracRol3dEYE->setValor($intIdRol3dEYE);
										$objInfoPuntoCaracRol3dEYE->setFeCreacion(new \DateTime('now'));
										$objInfoPuntoCaracRol3dEYE->setUsrCreacion($strUsrCreacion);
										$objInfoPuntoCaracRol3dEYE->setIpCreacion($strIpClient);
										$objInfoPuntoCaracRol3dEYE->setEstado('Activo');
										$this->emComercial->persist($objInfoPuntoCaracRol3dEYE);
										$this->emComercial->flush();
									}
									
									if(is_object($objServProdCaracRol3dEYE))
									{
										$objServProdCaracRol3dEYE->setValor($intIdRol3dEYE);
										$objServProdCaracRol3dEYE->setFeUltMod(new \DateTime('now'));
										$objServProdCaracRol3dEYE->setUsrUltMod($strUsrCreacion);
										$this->emComercial->persist($objServProdCaracRol3dEYE);
										$this->emComercial->flush();
									}
									else
									{
										$objServProdCaracRol3dEYE = new InfoServicioProdCaract();
										$objServProdCaracRol3dEYE->setServicioId($intIdServicio);
										$objServProdCaracRol3dEYE->setProductoCaracterisiticaId($intIdProdCaractRol3dEYE);
										$objServProdCaracRol3dEYE->setValor($intIdRol3dEYE);
										$objServProdCaracRol3dEYE->setEstado("Activo");
										$objServProdCaracRol3dEYE->setUsrCreacion($strUsrCreacion);
										$objServProdCaracRol3dEYE->setFeCreacion(new \DateTime('now'));
										$this->emComercial->persist($objServProdCaracRol3dEYE);
										$this->emComercial->flush();
									}
									
									// Asignar cámara al rol
									// Asignar permisos de la cámara en el rol
									$arrayPermissions = array("View", "SaveClip", "Share", "Ptz", "EditSettings");
									
									$strJsonDataAsigCamRol = array("permissions" => $arrayPermissions,);
									
									$arrayRespAsigCamRol = $this->asignarCamaraRol($intIdRol3dEYE, $intIdCam3dEYE, $strJsonDataAsigCamRol);
									$boolAsigCamRol      = $arrayRespAsigCamRol["boolAsigCamRol"];
									if(!$boolAsigCamRol)
									{
										throw new Exception("Existe un error al asignar la cámara al rol");
									}
								}
								else
								{
									throw new Exception("Existe un error al configurar el rol del cliente");
								}
							}
							else
							{
								throw new Exception("No se ha encontrado un producto asociado a la característica del rol 3dEYE");
							}
						}
						else
						{
							throw new Exception("No se ha encontrado la característica asociada al plan para determinar "."el rol del portal");
						}
						
						$arrayParametrosCaractUser3dEYE = array("descripcionCaracteristica" => "USER 3DEYE",
						                                        "estado"                    => "Activo");
						
						$objCaractUser3dEYE = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
						                                        ->findOneBy($arrayParametrosCaractUser3dEYE);
						if(is_object($objCaractUser3dEYE))
						{
							$objProdCaractUser3dEYE = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
							                                            ->findOneBy(array("productoId"       => $objProductoCamaraIp,
							                                                              "caracteristicaId" => $objCaractUser3dEYE,
							                                                              "estado"           => "Activo"));
							if(is_object($objProdCaractUser3dEYE))
							{
								$intIdProdCaractUser3dEYE  = $objProdCaractUser3dEYE->getId();
								$objServProdCaracUser3dEYE = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
								                                               ->findOneBy(array("servicioId"                => $intIdServicio,
								                                                                 "productoCaracterisiticaId" => $intIdProdCaractUser3dEYE,
								                                                                 "estado"                    => "Activo"));
								
								$objInfoPuntoCaracUser3dEYE = $this->emComercial->getRepository("schemaBundle:InfoPuntoCaracteristica")
								                                                ->findOneBy(array("puntoId"          => $objPunto,
								                                                                  "caracteristicaId" => $objCaractUser3dEYE,
								                                                                  "estado"           => "Activo"));
								
								// Crear usuario si no existe
								$intIdUser3dEYE        = $arrayDataCreateUser["arrayUser3dEYE"]["id"];
								$strUsername3dEYE      = $arrayDataCreateUser["arrayUser3dEYE"]["userName"];
								$strNombreCliente3dEYE =
										$arrayDataCreateUser["arrayUser3dEYE"]["firstName"]." ".$arrayDataCreateUser["arrayUser3dEYE"]["lastName"];
								if(!$arrayDataCreateUser["boolUserExiste"])
								{
									$arrayRespCreateUser                   = $this->createUser($arrayDataCreateUser);
									$arrayUserCreada3dEYE                  = $arrayRespCreateUser["arrayData"];
									$intIdUser3dEYE                        = $arrayUserCreada3dEYE["id"];
									$strUsername3dEYE                      = $arrayUserCreada3dEYE["userName"];
									$strNombreCliente3dEYE                 =
											$arrayUserCreada3dEYE["firstName"]." ".$arrayUserCreada3dEYE["lastName"];
									$arrayDataCreateUser["boolUserExiste"] = $arrayUserCreada3dEYE["boolUserExiste"];
								}
								
								if($intIdUser3dEYE)
								{
									if(is_object($objInfoPuntoCaracUser3dEYE))
									{
										$arrayDatosUser = $this->getDatosClienteById($objInfoPuntoCaracUser3dEYE->getValor());
										if(!$arrayDatosUser["arrayData"])
										{
											$objInfoPuntoCaracUser3dEYE->setValor($intIdUser3dEYE);
											$objInfoPuntoCaracUser3dEYE->setFeUltMod(new \DateTime('now'));
											$objInfoPuntoCaracUser3dEYE->setUsrUltMod($strUsrCreacion);
											$this->emComercial->persist($objInfoPuntoCaracUser3dEYE);
											$this->emComercial->flush();
										}
									}
									else
									{
										$objInfoPuntoCaracUser3dEYE = new InfoPuntoCaracteristica();
										$objInfoPuntoCaracUser3dEYE->setPuntoId($objPunto);
										$objInfoPuntoCaracUser3dEYE->setCaracteristicaId($objCaractUser3dEYE);
										$objInfoPuntoCaracUser3dEYE->setValor($intIdUser3dEYE);
										$objInfoPuntoCaracUser3dEYE->setFeCreacion(new \DateTime('now'));
										$objInfoPuntoCaracUser3dEYE->setUsrCreacion($strUsrCreacion);
										$objInfoPuntoCaracUser3dEYE->setIpCreacion($strIpClient);
										$objInfoPuntoCaracUser3dEYE->setEstado('Activo');
										$this->emComercial->persist($objInfoPuntoCaracUser3dEYE);
										$this->emComercial->flush();
									}
									
									if(is_object($objServProdCaracUser3dEYE))
									{
										$objServProdCaracUser3dEYE->setValor($intIdUser3dEYE);
										$objServProdCaracUser3dEYE->setFeUltMod(new \DateTime('now'));
										$objServProdCaracUser3dEYE->setUsrUltMod($strUsrCreacion);
										$this->emComercial->persist($objServProdCaracUser3dEYE);
										$this->emComercial->flush();
									}
									else
									{
										$objServProdCaracUser3dEYE = new InfoServicioProdCaract();
										$objServProdCaracUser3dEYE->setServicioId($intIdServicio);
										$objServProdCaracUser3dEYE->setProductoCaracterisiticaId($intIdProdCaractUser3dEYE);
										$objServProdCaracUser3dEYE->setValor($intIdUser3dEYE);
										$objServProdCaracUser3dEYE->setEstado("Activo");
										$objServProdCaracUser3dEYE->setUsrCreacion($strUsrCreacion);
										$objServProdCaracUser3dEYE->setFeCreacion(new \DateTime('now'));
										$this->emComercial->persist($objServProdCaracUser3dEYE);
										$this->emComercial->flush();
									}
									
									// Asignar usuario al rol
									$arrayRespAsigUserRol = $this->asignarUserRol($intIdRol3dEYE, $intIdUser3dEYE);
									$boolAsigUserRol      = $arrayRespAsigUserRol["boolAsigUserRol"];
									
									if($boolAsigUserRol)
									{
										/**
										 * Se activa el servicio
										 */
										$objServicio->setEstado("Activo");
										$this->emComercial->persist($objServicio);
										$this->emComercial->flush();
										
										/**
										 * Se genera el historial del servicio
										 */
										$strObservacionPortal3dEYE = '<b>Datos Portal 3dEYE<b><br>';
										$strObservacionPortal3dEYE .= 'Acción: Activación '.$strTipoActivacionCamara.'<br>';
										$strObservacionPortal3dEYE .= 'Nombre cámara: '.$strNombreCamara.'<br>';
										$strObservacionPortal3dEYE .= 'Usuario: '.$strUsername3dEYE.'<br>';
										$strObservacionPortal3dEYE .= 'Rol: '.$strNombreRol3dEYE.'<br>';
										
										$objServicioHistorialPortal3dEYE = new InfoServicioHistorial();
										$objServicioHistorialPortal3dEYE->setServicioId($objServicio);
										$objServicioHistorialPortal3dEYE->setObservacion($strObservacionPortal3dEYE);
										$objServicioHistorialPortal3dEYE->setEstado("Activo");
										$objServicioHistorialPortal3dEYE->setUsrCreacion($strUsrCreacion);
										$objServicioHistorialPortal3dEYE->setFeCreacion(new \DateTime('now'));
										$objServicioHistorialPortal3dEYE->setIpCreacion($strIpClient);
										$this->emComercial->persist($objServicioHistorialPortal3dEYE);
										$this->emComercial->flush();
										
										$objAccion = $this->emSeguridad->getRepository('schemaBundle:SistAccion')
										                               ->find($intIdAccion);
										
										$objServicioHistorial = new InfoServicioHistorial();
										$objServicioHistorial->setServicioId($objServicio);
										$objServicioHistorial->setObservacion("Se confirmó el servicio");
										$objServicioHistorial->setEstado("Activo");
										$objServicioHistorial->setUsrCreacion($strUsrCreacion);
										$objServicioHistorial->setFeCreacion(new \DateTime('now'));
										$objServicioHistorial->setIpCreacion($strIpClient);
										$objServicioHistorial->setAccion($objAccion->getNombreAccion());
										$this->emComercial->persist($objServicioHistorial);
										$this->emComercial->flush();
										
										/**
										 * Se finaliza la solicitud de planificación
										 */
										$objTipoSolPlanif = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
										                                      ->findOneBy(array("descripcionSolicitud" => "SOLICITUD PLANIFICACION",
										                                                        "estado"               => "Activo"));
										if(is_object($objTipoSolPlanif))
										{
											$intIdTipoSolPlanif        = $objTipoSolPlanif->getId();
											$objSolicitudPlanificacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
											                                               ->findOneBy(array("servicioId"      => $intIdServicio,
											                                                                 "tipoSolicitudId" => $intIdTipoSolPlanif,
											                                                                 "estado"          => "Asignada"));
											if($objSolicitudPlanificacion)
											{
												$objSolicitudPlanificacion->setEstado("Finalizada");
												$this->emComercial->persist($objSolicitudPlanificacion);
												$this->emComercial->flush();
												
												//crear historial para la solicitud
												$objHistorialSolicitudPlanif = new InfoDetalleSolHist();
												$objHistorialSolicitudPlanif->setDetalleSolicitudId($objSolicitudPlanificacion);
												$objHistorialSolicitudPlanif->setEstado("Finalizada");
												$objHistorialSolicitudPlanif->setObservacion("Cliente instalado");
												$objHistorialSolicitudPlanif->setUsrCreacion($strUsrCreacion);
												$objHistorialSolicitudPlanif->setFeCreacion(new \DateTime('now'));
												$objHistorialSolicitudPlanif->setIpCreacion($strIpClient);
												$this->emComercial->persist($objHistorialSolicitudPlanif);
												$this->emComercial->flush();
												
												$intIdSolicitudPlanificacion                   = $objSolicitudPlanificacion->getId();
												$arrayParamsFinTareas['intIdDetalleSolicitud'] = $intIdSolicitudPlanificacion;
												$arrayParamsFinTareas['strProceso']            = "Activar";
												
												$strMsjResponse = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleSolicitud')
												                                          ->cerrarTareasPorSolicitud($arrayParamsFinTareas);
												
												if($strMsjResponse != "OK")
												{
													throw new Exception("No se han podido cerrar las tareas para la solicitud con id ".$intIdSolicitudPlanificacion);
												}
											}
											else
											{
												throw new Exception("No existe la solicitud de planificación para el servicio con id ".$intIdServicio);
											}
										}
										else
										{
											throw new Exception("No existe el tipo de solicitud de planificación");
										}
										
										$strCodEmpresaNaf  = "";
										$strCodArticulo    = "";
										$strIdentificacion = "";
										$strTipoArticulo   = "AF";
										
										$arrayParamsInstalacionCam = array("codigoEmpresaNaf"      => $strCodEmpresaNaf,
										                                   "codigoArticulo"        => $strCodArticulo,
										                                   "tipoArticulo"          => $strTipoArticulo,
										                                   "identificacionCliente" => $strIdentificacion,
										                                   "serieCpe"              => $strSerieCam,
										                                   "strUsrCreacion"        => $strUsrCreacion,
										                                   "strIpClient"           => $strIpClient,);
										
										$arrayRespInstCam = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
										                                            ->procesarInstalacionNAF($arrayParamsInstalacionCam,
										                                                                     $this->serviceUtil);
										
										if(strlen(trim($arrayRespInstCam["strMsj"])) > 0 || $arrayRespInstCam["strStatus"] == "ERROR")
										{
											throw new Exception("ERROR NAF: ".$arrayRespInstCam["strMsj"]);
										}
										else
										{
											if(!$arrayDataCreateUser["boolUserExiste"])
											{
												$arrayParametrosEnvioMail = array("strUsrCreacion"  => $strUsrCreacion,
												                                  "strIpClient"     => $strIpClient,
												                                  "strAsunto"       => "Bienvenido a NetCam. "."El servicio se activó correctamente. ",
												                                  "strCodPlantilla" => 'ACTIVAR_NTLFCAM',
												                                  "strDestinatario" => array($strUsername3dEYE,),
												                                  "arrayDataMail"   => array("strNombreCliente" => $strNombreCliente3dEYE,
												                                                             "strUserPortal"    => $strUsername3dEYE,
												                                                             "strPassPortal"    => $arrayDataCreateUser["strPassword"],),);
												
												$arrayRespEnvCorreo = $this->enviarInformacionCorreoNetlifeCam($arrayParametrosEnvioMail);
												if($arrayRespEnvCorreo["strStatus"] == "ERROR")
												{
													$boolEliminarUsuario = true;
													throw new Exception($arrayRespEnvCorreo["strMsj"]);
												}
											}
											$strStatus  = "OK";
											$strMensaje = "Se activó la cámara ".$strNombreCamara;
											$this->emComercial->commit();
											$this->emInfraestructura->commit();
										}
									}
									else
									{
										throw new Exception("Existe un error al asignar el usuario al rol");
									}
								}
								else
								{
									throw new Exception("Existe un error al configurar el user del cliente");
								}
							}
							else
							{
								throw new Exception("No se ha encontrado un producto asociado a la característica del user 3dEYE");
							}
						}
						else
						{
							throw new Exception("No se ha encontrado la característica asociada al plan para determinar "."el user del portal");
						}
					}
					else
					{
						throw new Exception("No existe el producto de cámara ip");
					}
				}
				else
				{
					if($arrayCamCreada3dEYE)
					{
						$strMensaje =
								"No se pudo crear la cámara ".$arrayDataCreateCamara["strNombreCamara"]." verifique los datos técnicos y su conexión a la red.";
						throw new Exception($strMensaje);
					}
					else
					{
						throw new Exception("Ha ocurrido un problema. Por favor notifique a Sistemas!");
					}
				}
			}
			else
			{
				throw new Exception("No se ha podido encontrar la información del servicio");
			}
		}
		catch(Exception $e)
		{
			$strMensaje = $e->getMessage();
			if($intIdCam3dEYE != null && $strNombreCamara != null)
			{
				$arrayRespEliminarCam = $this->eliminarCam($intIdCam3dEYE, $strNombreCamara);
				if($arrayRespEliminarCam["strStatus"] == "OK")
				{
					$strMensaje = $e->getMessage();
				}
				else
				{
					$strMensaje = $arrayRespEliminarCam["strMensaje"];
				}
			}
			else
			{
				$arrayRespRevElimCam = $this->reverificacionDeleteCam($arrayDataCreateCamara["strNombreCamara"]);
				if($arrayRespRevElimCam['strStatus'] == "ERROR")
				{
					$strMensaje = $arrayRespRevElimCam['strMensaje'];
				}
			}
			// Rollback del usuario si no envia el correo
			if($boolEliminarUsuario)
			{
				$this->deleteUserById($intIdUser3dEYE);
			}
			
			if($this->emComercial->getConnection()
			                     ->isTransactionActive())
			{
				$this->emComercial->rollback();
				$this->emComercial->close();
			}
			
			if($this->emInfraestructura->getConnection()
			                           ->isTransactionActive())
			{
				$this->emInfraestructura->rollback();
				$this->emInfraestructura->close();
			}
		}
		catch(DBALException $ex)
		{
			$strMensaje = $ex->getMessage();
			if($intIdCam3dEYE != null && $strNombreCamara != null)
			{
				$arrayRespEliminarCam = $this->eliminarCam($intIdCam3dEYE, $strNombreCamara);
				if($arrayRespEliminarCam["strStatus"] == "OK")
				{
					$strMensaje = $ex->getMessage();
				}
				else
				{
					$strMensaje = $arrayRespEliminarCam["strMensaje"];
				}
			}
			else
			{
				$arrayRespRevElimCam = $this->reverificacionDeleteCam($arrayDataCreateCamara["strNombreCamara"]);
				if($arrayRespRevElimCam['strStatus'] == "ERROR")
				{
					$strMensaje = $arrayRespRevElimCam['strMensaje'];
				}
			}
			// Rollback del usuario si no envia el correo
			if($boolEliminarUsuario)
			{
				$this->deleteUserById($intIdUser3dEYE);
			}
			
			if($this->emComercial->getConnection()
			                     ->isTransactionActive())
			{
				$this->emComercial->rollback();
				$this->emComercial->close();
			}
			
			if($this->emInfraestructura->getConnection()
			                           ->isTransactionActive())
			{
				$this->emInfraestructura->rollback();
				$this->emInfraestructura->close();
			}
		}
		$objResponse['strStatus']  = $strStatus;
		$objResponse['strMensaje'] = $strMensaje;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'reverificacionDeleteCam'
	 * Función utilizada para verificar si una cámara esta eliminada en el portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $strNombreCamara
	 *
	 * @return  $objResponse
	 */
	public function reverificacionDeleteCam($strNombreCamara)
	{
		$objResponse = array();
		$strStatus   = "ERROR";
		$strMensaje  = "";
		
		$arrayRespDatosCamara = $this->validateCamaraByName($strNombreCamara);
		if($arrayRespDatosCamara['strStatus'] == "OK")
		{
			$arrayDatosCamara = $arrayRespDatosCamara["arrayData"];
			if($arrayDatosCamara)
			{
				$arrayRespDeleteCam3dEYE = $this->deleteCamaraById($arrayDatosCamara["id"]);
				$boolDeleteCam           = $arrayRespDeleteCam3dEYE["boolDeleteCam"];
				if(!$boolDeleteCam)
				{
					$strMensaje = "No se ha podido eliminar la cámara ".$strNombreCamara.". Por favor realizar
							la eliminación manual en el portal 3dEYE.";
				}
				else
				{
					$strStatus = "OK";
				}
			}
			else
			{
				$strStatus = "OK";
			}
		}
		else
		{
			$strMensaje = "Error en la transacción. Por favor notifique a Sistemas!";
		}
		
		$objResponse["strStatus"]  = $strStatus;
		$objResponse["strMensaje"] = $strMensaje;
		
		return $objResponse;
	}
	
	/**
	 * Documentación para la función 'eliminarCam'
	 * Función utilizada para eliminar una cámara en el portal 3dEYE
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param $intIdCamara
	 * @param $strNombreCamara
	 *
	 * @return  $objResponse
	 */
	public function eliminarCam($intIdCamara, $strNombreCamara = null)
	{
		$objResponse   = array();
		$boolDeleteCam = false;
		$intIntento    = 2;
		
		$strStatus  = "ERROR";
		$strMensaje = "";
		
		while(!$boolDeleteCam)
		{
			if($intIntento != 0)
			{
				$arrayRespDeleteCam3dEYE = $this->deleteCamaraById($intIdCamara);
				$boolDeleteCam           = $arrayRespDeleteCam3dEYE["boolDeleteCam"];
				if($boolDeleteCam)
				{
					$strStatus = "OK";
				}
				$intIntento--;
			}
			else
			{
				$boolDeleteCam = true;
				$strMensaje    =
						"Existe un error al configurar la cámara ".$strNombreCamara.". No se pudo eliminar la cámara en el portal 3dEYE,
						Por favor notifique a Sistemas!";
			}
		}
		
		// Reverificación de la cámara
		$arrayRespDatosCamara = $this->validateCamaraByName($strNombreCamara);
		if($arrayRespDatosCamara['strStatus'] == "OK")
		{
			$arrayDatosCamara = $arrayRespDatosCamara["arrayData"];
			if($arrayDatosCamara)
			{
				$arrayRespDeleteCam3dEYE = $this->deleteCamaraById($intIdCamara);
				$boolDeleteCam           = $arrayRespDeleteCam3dEYE["boolDeleteCam"];
				if(!$boolDeleteCam)
				{
					throw new Exception("No se ha podido eliminar la cámara ".$strNombreCamara.". Por favor realizar la eliminación
					manual en el portal 3dEYE.");
				}
				else
				{
					$strStatus = "OK";
					$strMensaje = "";
				}
			}
		}
		
		$objResponse["strStatus"]  = $strStatus;
		$objResponse["strMensaje"] = $strMensaje;
		
		return $objResponse;
	}
	
}

