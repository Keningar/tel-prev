<?php  
    namespace TelconetSSO\TelconetSSOBundle\Entity;  
      
    use Symfony\Component\Security\Core\User\UserInterface;  
      
    /** 
     * @MongoDB\Document 
     */  
    class User implements UserInterface, \Serializable
    {  
        /** 
         * 
         * @SSO\Id 
         */  
        protected $cedula;  
      
        /** 
         * @MongoDB\String 
         */  
        protected $username;  
      
        /** 
         * @MongoDB\String 
         */  
        protected $nombres;  
      
        /** 
         * @MongoDB\String 
         */  
        protected $mail;  
        
        protected $ciudad;
      
        protected $roles;

        protected $cod_empresa;
      
        /** 
         * @var string A salt for the password 
         */  
        protected $salt = "Blog";  
      
	    public function __construct($cedula, $username, $nombres, $mail)
		{
			if (empty($username)) {
				throw new \InvalidArgumentException('The username cannot be empty.');
			}

			$this->username = $username;
			$this->cedula = $cedula;
			$this->nombres = $nombres;
			$this->mail = $mail;
			
			$this->cod_empresa = '10';
		}
	  
	  
	  
        /** 
         * The roles a user has 
         * 
         * @return array 
         */  
		 
        public function getRoles ()  
        {  
//            return array(  
//                'ROLE_ADMIN'  
//            );  
            return ($this->roles)?$this->roles:array();
        }
        public function getPassword()
        {
                return $this->getCedula();
        }
      
        /** 
         * Erases the credential information 
         */  
        public function eraseCredentials ()  
        {  
            $this->username = null;  
        }  
      
        /** 
         * Verifies if given user equals the current user 
         * 
         * @param mixed $user 
         * @return Boolean 
         */  
        public function equals (UserInterface $user)  
        {  
            return ($this->getUsername() === $user->getUsername());  
        }  
      
        /** 
         * Returns the salt 
         * 
         * @return string 
         */  
        public function getSalt ()  
        {  
            return $this->salt;  
        }  
      
        /** 
         * Get userID 
         * 
         * @return id $userID 
         */  
        public function getCedula()  
        {  
            return $this->cedula;  
        }  
      
        /** 
         * Set username 
         * 
         * @param string $username 
         */  
        public function setUsername($username)  
        {  
            $this->username = $username;  
        }  
      
        /** 
         * Get username 
         * 
         * @return string $username 
         */  
        public function getUsername()  
        {  
            return $this->username;  
        }  
      
        /** 
         * Set password 
         * 
         * @param string $password 
         */  
        public function setNombres($nombres)  
        {  
            $this->nombres = $nombres;  
        }  
      
        /** 
         * Get password 
         * 
         * @return string $password 
         */  
        public function getNombres()  
        {  
            return $this->nombres;  
        }  
      
        /** 
         * Set firstname 
         * 
         * @param string $firstname 
         */  
        public function setMail($mail)  
        {  
            $this->mail = $mail;  
        }  
      
        /** 
         * Get firstname 
         * 
         * @return string $firstname 
         */  
        public function getMail()  
        {  
            return $this->mail;  
        } 
        
        public function serialize() {
            return serialize(array($this->getUsername()));
        }

        public function unserialize($serialized) {
            $arr = unserialize($serialized);
            $this->setUsername($arr[0]);
        }
        /**
         * Add roles
         *
         * @param TelconetSSO\TelconetSSOBundle\Entity $roles
         */
        public function addRol($rol)
        {
            $this->roles[] = $rol;
        }
        /** 
         * Set ciudad
         * 
         * @param string $ciudad 
         */  
        public function setCiudad($ciudad)  
        {  
            $this->ciudad = $ciudad;  
        }  
      
        /** 
         * Get ciudad 
         * 
         * @return string $ciudad 
         */  
        public function getCiudad()  
        {  
            return $this->ciudad;  
        }

        public function setCodEmpresa($cod_empresa)
        {
            $this->cod_empresa = $cod_empresa;
        }  

        public function getCodEmpresa()
        {
            return $this->cod_empresa;
        }
    }  
