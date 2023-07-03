<?php

    namespace telconet\schemaBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * telconet\schemaBundle\Entity\InfoServicioRecursoDet
     *
     * @ORM\Table(name="INFO_SERVICIO_RECURSO_DET")
     * @ORM\Entity
     * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoServicioRecursoDetRepository")
     */
    class InfoServicioRecursoDet
    {
        /**
         * @var integer $id
         *
         * @ORM\Column(name="ID_SERVICIO_RECURSO_DET", type="integer", nullable=false)
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="SEQUENCE")
         * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_SERVICIO_RECURSO_DET", allocationSize=1, initialValue=1)
        */
        private $id;

        /**
         * @var InfoServicioRecursoCab
         *
         * @ORM\ManyToOne(targetEntity="InfoServicioRecursoCab")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="SERVICIO_RECURSO_CAB_ID", referencedColumnName="ID_SERVICIO_RECURSO_CAB")
         * })
         */
        private $servicioRecursoCabId;

        /**
         * @var integer $elementoId
         *
         * @ORM\Column(name="ELEMENTO_ID", type="integer", nullable=true)
         */
        private $elementoId;

         /**
         * @var integer $cantidad
         *
         * @ORM\Column(name="CANTIDAD", type="integer", nullable=true)
         */
        private $cantidad;

        /**
         * @var integer $refRecursoDetId
         *
         * @ORM\Column(name="REF_RECURSO_DET_ID", type="integer", nullable=true)
         */
        private $refRecursoDetId;

        /**
         * @var string $descripcion
         *
         * @ORM\Column(name="DESCRIPCION", type="string", nullable=true)
         */
        private $descripcion;

        /**
         * @var string $estado
         *
         * @ORM\Column(name="ESTADO", type="string", nullable=true)
         */
        private $estado;

        /**
         * @var string $usrCreacion
         *
         * @ORM\Column(name="USR_CREACION", type="string", nullable=true)
         */
        private $usrCreacion;

        /**
         * @var datetime $fecCreacion
         *
         * @ORM\Column(name="FEC_CREACION", type="datetime", nullable=true)
         */
        private $fecCreacion;

        /**
         * @var string $ipCreacion
         *
         * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
         */
        private $ipCreacion;

        /**
         * @var datetime $fecUltMod
         *
         * @ORM\Column(name="FEC_ULT_MOD", type="datetime", nullable=true)
         */
        private $fecUltMod;

        /**
         * @var string $usrUltMod
         *
         * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
         */
        private $usrUltMod;

        /**
         * @var string $ipUltMod
         *
         * @ORM\Column(name="IP_ULT_MOD", type="string", nullable=true)
         */
        private $ipUltMod;


        /**
         * Get id
         *
         * @return integer id
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * Get servicioRecursoCabId
         *
         * @return telconet\schemaBundle\Entity\InfoServicioRecursoCab
         */
        public function getServicioRecursoCabId()
        {
            return $this->servicioRecursoCabId;
        }

        /**
        * Set servicioRecursoCabId
        *
        * @param telconet\schemaBundle\Entity\InfoServicioRecursoCab $objInfoServicioRecursoCab
        */
        public function setServicioRecursoCabId($objInfoServicioRecursoCab)
        {
            $this->servicioRecursoCabId = $objInfoServicioRecursoCab;
        }

        /**
         * Get elementoId
         *
         * @return integer
         */
        public function getElementoId()
        {
            return $this->elementoId;
        }

        /**
         * Set elementoId
         *
         * @param integer $intElementoId
         */
        public function setElementoId($intElementoId)
        {
            $this->elementoId = $intElementoId;
        }

        /**
         * Get cantidad
         *
         * @return integer
         */
        public function getCantidad()
        {
            return $this->cantidad;
        }

        /**
         * Set cantidad
         *
         * @param integer $intCantidad
         */
        public function setCantidad($intCantidad)
        {
            $this->cantidad = $intCantidad;
        }

        /**
         * Get refRecursoDetId
         *
         * @return integer
         */
        public function getRefRecursoDetId()
        {
            return $this->refRecursoDetId;
        }

        /**
         * Set refRecursoDetId
         *
         * @param integer $intRefRecursoDetId
         */
        public function setRefRecursoDetId($intRefRecursoDetId)
        {
            $this->refRecursoDetId = $intRefRecursoDetId;
        }

        /**
         * Get descripcion
         *
         * @return string
         */
        public function getDescripcion()
        {
            return $this->descripcion;
        }

        /**
         * Set descripcion
         *
         * @param string $strDescripcion
         */
        public function setDescripcion($strDescripcion)
        {
            $this->descripcion = $strDescripcion;
        }

        /**
         * Get estado
         *
         * @return string 
         */
        public function getEstado()
        {
            return $this->estado;
        }

        /**
         * Set estado
         *
         * @param string $strEstado
         */
        public function setEstado($strEstado)
        {
            $this->estado = $strEstado;
        }

        /**
         * Get usrCreacion
         *
         * @return string 
         */
        public function getUsrCreacion()
        {
            return $this->usrCreacion;
        }

        /**
         * Set usrCreacion
         *
         * @param string $strUsrCreacion
         */
        public function setUsrCreacion($strUsrCreacion)
        {
            $this->usrCreacion = $strUsrCreacion;
        }

        /**
         * Get fecCreacion
         *
         * @return datetime
         */
        public function getFecCreacion()
        {
            return $this->fecCreacion;
        }

        /**
         * Set fecCreacion
         *
         * @param datetime $objFecCreacion
         */
        public function setFecCreacion($objFecCreacion)
        {
            $this->fecCreacion = $objFecCreacion;
        }

        /**
         * Get ipCreacion
         *
         * @return string 
         */
        public function getIpCreacion()
        {
            return $this->ipCreacion;
        }

        /**
         * Set ipCreacion
         *
         * @param string $strIpCreacion
         */
        public function setIpCreacion($strIpCreacion)
        {
            $this->ipCreacion = $strIpCreacion;
        }

        /**
         * Get usrUltMod
         *
         * @return string 
         */
        public function getUsrUltMod()
        {
            return $this->usrUltMod;
        }

        /**
         * Set usrUltMod
         *
         * @param string $strUsrUltMod
         */
        public function setUsrUltMod($strUsrUltMod)
        {
            $this->usrUltMod = $strUsrUltMod;
        }

        /**
         * Get fecUltMod
         *
         * @return datetime
         */
        public function getFecUltMod()
        {
            return $this->fecUltMod;
        }

        /**
         * Set fecUltMod
         *
         * @param datetime $objFecUltMod
         */
        public function setFecUltMod($objFecUltMod)
        {
            $this->fecUltMod = $objFecUltMod;
        }

        /**
         * Get ipUltMod
         *
         * @return string
         */
        public function getIpUltMod()
        {
            return $this->ipUltMod;
        }

        /**
         * Set ipUltMod
         *
         * @param string $strIpUltMod
         */
        public function setIpUltMod($strIpUltMod)
        {
            $this->ipUltMod = $strIpUltMod;
        }
    }
