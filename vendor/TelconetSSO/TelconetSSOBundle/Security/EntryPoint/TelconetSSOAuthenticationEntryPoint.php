<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TelconetSSO\TelconetSSOBundle\Security\EntryPoint;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * FacebookAuthenticationEntryPoint starts an authentication via Facebook.
 *
 * @author Thomas Adam <thomas.adam@tebot.de>
 */
class TelconetSSOAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
	/*
    protected $facebook;
    protected $options;
    protected $permissions;
*/
    /**
     * Constructor
     *
     * @param BaseFacebook $facebook
     * @param array    $options
     */
    public function __construct(array $options = array())//\BaseFacebook $facebook, array $options = array())//, array $permissions = array())
    {
		/*
        $this->facebook = $facebook;
        $this->permissions = $permissions;
        $this->options = new ParameterBag($options);*/
    }

    /**
     * {@inheritdoc}
     *
	 */
    public function start(Request $request, AuthenticationException $authException = null)
    {
		$session = $request->getSession();
		$session->set('JOSSO_ORIGINAL_URL' , $request->getRequestUri() );
			$host = $_SERVER['HTTP_HOST'];
			$params = '&josso_partnerapp_host=' . $host;
			$loginUrl="https://192.168.240.66:8443/josso/signon/login.do?josso_back_to=https://facebooksso.net/app_dev.php/check".$params;
			
		 //$loginUrl = $this->options->get('login_url').'?josso_back_to=https://facebooksso.net/app_dev.php/check';//.//$request->getUriForPath($this->options->get('check_path', ''));
		 //var_dump('en el entrypoint');
		 //exit;
         return new RedirectResponse($loginUrl);
        //return $this->httpUtils->createRedirectResponse($request, $this->loginPath);
	
        /*
		$redirect_uri = $request->getUriForPath($this->options->get('check_path', ''));

        if ($this->options->get('server_url') && $this->options->get('app_url')) {
            $redirect_uri = str_replace($this->options->get('server_url'), $this->options->get('app_url'), $redirect_uri);
        }
        $loginUrl = $this->facebook->getLoginUrl(
           array(
                'display' => $this->options->get('display', 'page'),
                'scope' => implode(',', $this->permissions),
                'redirect_uri' => $redirect_uri,
        ));*/
    }
}
