<?php
require_once('JossoUser.class.php');
//require_once(dirname(__FILE__).'/../../../lib/vendor/nusoap/nusoap.php');
//use Acme\BlogBundle\Controller\PagesController;
/**
 * JOSSO Agent class definition.
 *
 * @package org.josso.agent.php
 */

/**
JOSSO: Java Open Single Sign-On

Copyright 2004-2008, Atricore, Inc.

This is free software; you can redistribute it and/or modify it
under the terms of the GNU Lesser General Public License as
published by the Free Software Foundation; either version 2.1 of
the License, or (at your option) any later version.

This software is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this software; if not, write to the Free
Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA
02110-1301 USA, or see the FSF site: http://www.fsf.org.

*/

/**
 * PHP Josso Agent implementation based on WS.
 *
 * @package  org.josso.agent.php
 *
 * @author Sebastian Gonzalez Oyuela <sgonzalez@josso.org>
 * @version $Id: class.jossoagent.php 613 2008-08-26 16:42:10Z sgonzalez $
 * @author <a href="mailto:sgonzalez@josso.org">Sebastian Gonzalez Oyuela</a>
 * @author Updated by Christian A. Rodriguez <car@cespi.unlp.edu.ar>
 * @author <a href="mailto:car@cespi.unlp.edu.ar">Christian A. Rodriguez</a>
 *
 */

class JossoAgent  {


	// ---------------------------------------
	// JOSSO Agent configuration : 
	// --------------------------------------- 
	
	/**
	 * WS End-point
	 * @var string
	 * @access private
	 */
	private $endpoint = 'https://192.168.240.66:8443';
	
	/**
	 * SSOSessionManager service path
	 * @var string
	 * @access private
	 */
	var $sessionManagerServicePath = '/josso/services/SSOSessionManagerSoap?wsdl';
	
	/**
	 * SSOIdentityManager service path
	 * @var string
	 * @access private
	 */
	var $identityManagerServicePath = '/josso/services/SSOIdentityManagerSoap?wsdl';
	///josso/services/SSOIdentityManagerSoap?wsdl
	
	/**
	 * SSOIdentityProvider service path
	 * @var string
	 * @access private
	 */
	var $identityProviderServicePath = '/josso/services/SSOIdentityProviderSoap?wsdl';
	
	/**
	 * WS Proxy Settings
     * @var string
     * @access private
     */
	private $proxyhost = '';

	/**
     * @var string
     * @access private
     */
	private $proxyport = '';

	/**
     * @var string
     * @access private
     */
	private $proxyusername = '';

	/**
     * @var string
     * @access private
     */
	private $proxypassword = '';
	
	// Gateway
    /**
     * @var string
     * @access private
     */
	private $gatewayLoginUrl;

	/**
     * @var string
     * @access private
     */
	private $gatewayLogoutUrl;


		/**
     * @var string
     * @access private
     */
	var $sessionAccessMinInterval = 1000;

	/**
	 * Base path where JOSSO pages  can be found, like josso-security-check.php
	 */
	var $baseCode ;

	/**
	 * MS P3P HTTP Header value, for IFRAMES compatibility with IE 6+
	 */
	var $p3pHeaderValue;
	
	// ---------------------------------------
	// JOSSO Agent internal state : 
	// --------------------------------------- 

	/**
	 * SOAP Clienty for identity mgr.
     * @var string
     * @access private
     */
	private $identityMgrClient;


	/**
	 * SOAP Clienty for identity provider.
     * @var string
     * @access private
     */
	private $identityProviderClient;

	
	/**
	 * SOAP Clienty for session mgr.
     * @var string
     * @access private
     */
	private $sessionMgrClient;
	
	/**
	 * Last occurred error
     * @var string
     * @access private
     */
	var $fault;

	/**
	 * Last occurred fault
     * @var string
     * @access private
     */
	var $err;
	
	/**
	  * Automatic login strategies.
	  * @var array
	  * @access private
	  */
	var $automaticStrategies;

	/**
	  * Partner application IDs.
	  * @var array
	  * @access private
	  */
	var $partnerAppIDs;
	
	/**
	 * @return JossoAgent a new Josso PHP Agent instance.
	 */
	public static function getNewInstance() {
		// Get config variable values from josso.inc.		
		$gateway_login_url= 'https://192.168.240.66:8443/josso/signon/login.do';
		$gateway_logout_url= 'https://192.168.240.66:8443/josso/signon/logout.do';
        $service_endpoint_url= 'https://192.168.240.66:8443';
		$proxy_host='';
		$proxy_port='';
		$proxy_username='';
		$proxy_password='';
		$josso_gatewayLoginUrl=$gateway_login_url;
		$josso_gatewayLogoutUrl=$gateway_logout_url;
		$josso_endpoint=$service_endpoint_url;
		$josso_proxyhost=$proxy_host;
		$josso_proxyport=$proxy_port;
		$josso_proxyusername=$proxy_username;
		$josso_proxypassword=$proxy_password;
		
		return new JossoAgent($josso_gatewayLoginUrl, 
							  $josso_gatewayLogoutUrl, 
							  $josso_endpoint, 
							  $josso_proxyhost, 
							  $josso_proxyport, 
							  $josso_proxyusername, 
							  $josso_proxypassword
            );
	}
	
	
	public static function getNewInstanceWithParams($gateway_login_url,$gateway_logout_url,$service_endpoint_url,$proxy_host,
		$proxy_port='',$proxy_username,$proxy_password) {
		// Get config variable values from josso.inc.				
		
		$josso_gatewayLoginUrl=$gateway_login_url;
		$josso_gatewayLogoutUrl=$gateway_logout_url;
		$josso_endpoint=$service_endpoint_url;
		$josso_proxyhost=$proxy_host;
		$josso_proxyport=$proxy_port;
		$josso_proxyusername=$proxy_username;
		$josso_proxypassword=$proxy_password;
		$josso_node = (isset($_COOKIE['JOSSO_NODE']) ? $_COOKIE['JOSSO_NODE'] : null); // SQA: Se obtiene cookie josso_node
		
		return new JossoAgent($josso_gatewayLoginUrl, 
							  $josso_gatewayLogoutUrl, 
							  $josso_endpoint . ($josso_node ? ('/' . $josso_node) : ''), // SQA: Se concatena josso_node al endpoint 
							  $josso_proxyhost, 
							  $josso_proxyport, 
							  $josso_proxyusername, 
							  $josso_proxypassword
            );
	}
	
	/**
	* constructor
	*
	* @access private
	*
	* @param    string $josso_gatewayLoginUrl 
	* @param    string $josso_gatewayLogoutUrl 
	* @param    string $josso_endpoint SOAP server
	* @param    string $josso_proxyhost
	* @param    string $josso_proxyport
	* @param    string $josso_proxyusername
	* @param    string $josso_proxypassword
	*/
	private function __construct($josso_gatewayLoginUrl, $josso_gatewayLogoutUrl, $josso_endpoint, 
						$josso_proxyhost, $josso_proxyport, $josso_proxyusername, $josso_proxypassword) {
	
		// WS Config
		$this->endpoint = $josso_endpoint;
		$this->proxyhost = $josso_proxyhost;
		$this->proxyport = $josso_proxyport;
		$this->proxyusername = $josso_proxyusername;
		$this->proxypassoword = $josso_proxypassword;
		
		// Agent config
		$this->gatewayLoginUrl = $josso_gatewayLoginUrl;
		$this->gatewayLogoutUrl = $josso_gatewayLogoutUrl;
										
	}
	
	/**
	* Gets the authenticated jossouser, if any.
	*
	* @return JossoUser the authenticated user information or null.
	* @access public
	*/
	
	public function getUserInSession() {
	
		$sessionId = $this->getSessionId();
		if (!isset($sessionId)) {
			return null;
		}
		// SOAP Invocation		
		$soapclient = $this->getIdentityMgrSoapClient();    
    try{      
	  $findUserInSessionRequest = array('FindUserInSession' => array('ssoSessionId' => $sessionId, 'requester' => $this->getRequester()));	 	  
		$findUserInSessionResponse  = $soapclient ->call('findUserInSession', $findUserInSessionRequest, 		
						'urn:org:josso:gateway:ws:1.2:protocol', '', false, null, 'document', 'literal');						
	  if (! $this->checkError($soapclient)) {
			return $this->newUser($findUserInSessionResponse['SSOUser']);
		}
	  
    }catch(SoapFault $e){
      return null;
    }
	}
	
	/**
	* Returns all roles associated to the current sessionId
	*
	* @return JossoRole[] an array with all JossoRole instances
	* @access public
	*/
	public function getRoles () {
	// SOAP Invocation
    $sessionId=$this->getSessionId();
    $soapclient = $this->getIdentityMgrSoapClient();

    $findRolesBySSOSessionIdRequest = array('FindRolesBySSOSessionId' => array('ssoSessionId' => $sessionId, 'requester' => $this->getRequester()));
	
    $findRolesBySSOSessionIdResponse = $soapclient->call('findRolesBySSOSessionId', $findRolesBySSOSessionIdRequest, 
						'urn:org:josso:gateway:ws:1.2:protocol', '', false, null, 'document', 'literal');
   
    if (! $this->checkError($soapclient)) {
			// Build array of roles
			$i = 0;
			$result = $findRolesBySSOSessionIdResponse['roles'];
			if (sizeof($result) == 1) {
				$roles[0] = $this->newRole($result);
			} else {
				foreach($result as $roledata) {
					$roles[$i] = $this->newRole($roledata);
					$i++;
				}
			}
			return $roles;
		}		
	}
	
	/**
	 * Sends a keep-alive notification to the SSO server so that SSO sesison is not lost.
	 * @access public
	 */
	public function accessSession() {
	
		// Check if a session ID is pressent.
		$sessionId = $this->getSessionid();
		if (!isset($sessionId ) || $sessionId == '') {
			return '';
		}

		// Check last access time :
		// $lastAccessTime = $_SESSION['JOSSO_LAST_ACCESS_TIME'];
		// $now = time();

		// Assume that _SESSION is set.
        $soapclient = $this->getSessionMgrSoapClient();

        $accessSessionRequest = array('AccessSession' => array('ssoSessionId' => $sessionId, 'requester' => $this->getRequester()));
        $accessSessionResponse  = $soapclient->call('accessSession', $accessSessionRequest, 
        				'urn:org:josso:gateway:ws:1.2:protocol', '', false, null, 'document', 'literal');

        if ($this->checkError($soapclient)) {
            return '';
        }

        return $accessSessionResponse['ssoSessionId'];
	}
	
	/**
	 * Returns the URL where the user should be redireted to authenticate.
	 *
	 * @return string the configured login url.
	 *
	 * @access public
	 */
	public function getGatewayLoginUrl() {
		return $this->gatewayLoginUrl;
	}

	/**
	 * Returns the SSO Session ID given an assertion id.
	 *
	 * @param string $assertionId
	 *
	 * @return string, the SSO Session associated with the given assertion.
	 *
	 * @access public
	 */
	public function resolveAuthenticationAssertion($assertionId) {
		// SOAP Invocation		
		$soapclient = $this->getIdentityProvdierSoapClient();		
        $resolveAuthenticationAssertionRequest = array('ResolveAuthenticationAssertion' => array('assertionId' => $assertionId, 'requester' => $this->getRequester()));
        $resolveAuthenticationAssertionResponse = $soapclient->call('resolveAuthenticationAssertion', $resolveAuthenticationAssertionRequest, 
        				'urn:org:josso:gateway:ws:1.2:protocol', '', false, null, 'document', 'literal');
        
		if (! $this->checkError($soapclient)) {
			// Return SSO Session ID
			return $resolveAuthenticationAssertionResponse['ssoSessionId'];
		}
	}
	
	/**
	 * Returns the URL where the user should be redireted to logout.
	 *
     * @return string the configured logout url.
     *
     * @access public
	 */
	public function getGatewayLogoutUrl() {
		return $this->gatewayLogoutUrl;
	}


	
	//----------------------------------------------------------------------------------------
	// Protected methods intended to be invoked only within this class or subclasses.
	//----------------------------------------------------------------------------------------
	
	/**
	 * Gets current JOSSO session id, if any.
	 *
	 * @access private
	 */
	private function getSessionId() {
	    if (isset($_COOKIE['JOSSO_SESSIONID']))
		    return $_COOKIE['JOSSO_SESSIONID'];
	}
	
	/**
	 * Factory method to build a user from soap data.
	 *
	 * @param JossoUser as received from WS.
	 * @return jossouser a new jossouser instance.
	 *
	 * @access private
	 */
	private function newUser($user) {
		// Build a new jossouser 
		$username = $user['name'];
		$properties = $user['properties'];
    		//$roles=$this->getRoles();
			$roles=array();
		$user = new JossoUser($username, $properties,$roles);
		
		return $user;
	}
	
	/**
	 * Factory method to build a role from soap data.
	 *
	 * @param array role information as received from WS.
	 * @return jossorole a new jossorole instance
	 *
	 * @access private
	 */
	private function newRole($data) {
		// Build a new jossouser 
		$rolename = $data->name;
		$role = new JossoRole($rolename);
		return $role;
	}
	
	
	/**
	 * Gets the soap client to access identity service.
	 *
	 * @access private
	 */
	private function getIdentityMgrSoapClient() {
		// Lazy load the propper soap client
		if (!isset($this->identityMgrClient)) {
		// var_dump('antes de llamar al identityMgrClient');
      			$this->identityMgrClient = new \Nusoap_Nusoap($this->endpoint . $this->identityManagerServicePath, false, $this->proxyhost, $this->proxyport, $this->proxyusername, $this->proxypassword);

            // Sets default encoding to UTF-8 ...
            $this->identityMgrClient->soap_defencoding = 'UTF-8';
            $this->identityMgrClient->decodeUTF8(false);
			 //var_dump($this->identityMgrClient);
		}
		return $this->identityMgrClient;
	}

	/**
	 * Gets the soap client to access identity provider.
	 *
	 * @access private
	 */
	 private function getIdentityProvdierSoapClient() {
		// Lazy load the propper soap client
		if (!isset($this->identityProviderClient)) {
			$this->identityProviderClient = new \Nusoap_Nusoap($this->endpoint . $this->identityProviderServicePath, false,
											$this->proxyhost, $this->proxyport, $this->proxyusername, $this->proxypassword);

            // Sets default encoding to UTF-8 ...
            $this->identityProviderClient->soap_defencoding = 'UTF-8';
            $this->identityProviderClient->decodeUTF8(false);
		}
		return $this->identityProviderClient;
	}
	

	
	/**
	 * Gets the soap client to access session service.
	 *
	 * @access private
	 */	
	 /*
		function getSessionMgrSoapClient() {
		// Lazy load the propper soap client
		if (!isset($this->sessionMgrClient)) {
			// SSOSessionManager SOAP Client
		try{			
		    $wsdl=$this->endpoint . $this->sessionManagerServicePath;//'/josso/services/SSOIdentityManager?wsdl';
				  $options=array(
					"proxy_host"      =>  $this->proxyhost, 
					"proxy_port"      =>  $this->proxyport, 
					"proxy_login"     =>  $this->proxyusername, 
					"proxy_password"  =>  $this->proxypassword,
					"exceptions"      =>  true,
					"encoding"        =>  "UTF-8",
				  );
				  var_dump('antes de llamar al identityMgrClient');
				$this->sessionMgrClient = new \SoapClient($wsdl,$options);
				var_dump('luego de llamar al identityMgrClient');					            
				var_dump($this->sessionMgrClient->__getFunctions());				
				}
				catch(SoapFault $e){
				var_dump('!!!ERROR!!!'.$e);
				  return null;
				}
		
		}
		return $this->sessionMgrClient;

	}*/

	/**
	 * Gets the soap client to access session service.
	 *
	 * @access private
	 */
	function getSessionMgrSoapClient() {
		// Lazy load the propper soap client
		if (!isset($this->sessionMgrClient)) {
			// SSOSessionManager SOAP Client
			$this->sessionMgrClient = new \Nusoap_Nusoap($this->endpoint . $this->sessionManagerServicePath, false,
										$this->proxyhost, $this->proxyport, $this->proxyusername, $this->proxypassword);
		}
		return $this->sessionMgrClient;

	}
	
	/**
	 * Checks if an error occured with the received soapclient and stores information in agent state.
	 *
	 * @access private
	 */
	function checkError($soapclient) {
		// Clear old error/fault information.
		unset($this->fault);				
		unset($this->err);

		// Check for a fault
		if ($soapclient->fault) {
			$this->fault = $soapclient->fault;
			return TRUE;
		} else {
			// Check for errors
			if ($soapclient->error_str != '') {
			    $this->err = $soapclient->error_str;
				return TRUE;
			} 
		}
		
		// No errors ...
		return FALSE;
	
	}
	/**
	 *
	 * Gets the partner application id associated with the the current context path.
	 * 
	 * @return string
	 * @access private
	 */
	function getRequester() {
		if (isset($this->partnerAppIDs)) {
			$requester = $this->partnerAppIDs[$this->getContextPath()];
			if (isset($requester)) {
				return $requester;
			}
		}
		return null;
	}

}
?>
