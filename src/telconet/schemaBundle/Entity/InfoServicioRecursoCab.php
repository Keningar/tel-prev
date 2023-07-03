<?php

    namespace telconet\schemaBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * telconet\schemaBundle\Entity\InfoServicioRecursoCab
     *
     * @ORM\Table(name="INFO_SERVICIO_RECURSO_CAB")
     * @ORM\Entity
     * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoServicioRecursoCabRepository")
     */
    class InfoServicioRecursoCab
    {
        /**
         * @var integer $id
         *
         * @ORM\Column(name="ID_SERVICIO_RECURSO_CAB", type="integer", nullable=false)
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="SEQUENCE")
         * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_SERVICIO_RECURSO_CAB", allocationSize=1, initialValue=1)
         */
        private $id;

        /**
         * @var string $tipoRecurso
         *
         * @ORM\Column(name="TIPO_RECURSO", type="string", nullable=true)
         */
        private $tipoRecurso;

        /**
         * @var string $descripcionRecurso
         *
         * @ORM\Column(name="DESCRIPCION_RECURSO", type="string", nullable=true)
         */
        private $descripcionRecurso;

        /**
         * @var integer $servicioId
         *
         * @ORM\Column(name = "SERVICIO_ID", type="integer", nullable = false)
         */
        private $servicioId;

        /**
         * @var integer $solicitudId
         *
         * @ORM\Column(name = "SOLICITUD_ID", type="integer", nullable = false)
         */
        private $solicitudId;

        /**
         * @var integer $cantidad
         *
         * @ORM\Column(name="CANTIDAD", type="integer", nullable=true)
         */
        private $cantidad;

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
         * Get tipoRecurso
         *
         * @return string
         */
        public function getTipoRecurso()
        {
            return $this->tipoRecurso;
        }

        /**
         * Set tipoRecurso
         *
         * @param string $strTipoRecurso
         */
        public function setTipoRecurso($strTipoRecurso)
        {
            $this->tipoRecurso = $strTipoRecurso;
        }

        /**
         * Get descripcionRecurso
         *
         * @return string
         */
        public function getDescripcionRecurso()
        {
            return $this->descripcionRecurso;
        }

        /**
         * Set descripcionRecurso
         *
         * @param string $strDescripcionRecurso
         */
        public function setDescripcionRecurso($strDescripcionRecurso)
        {
            $this->descripcionRecurso = $strDescripcionRecurso;
        }

        /**
         * Get servicioId
         *
         * @return integer
         */
        public function getServicioId()
        {
            return $this->servicioId;
        }

        /**
         * Set servicioId
         *
         * @param integer $intServicioId
         */
        public function setServicioId($intServicioId)
        {
            $this->servicioId = $intServicioId;
        }

        /**
         * Get solicitudId
         *
         * @return integer
         */
        public function getSolicitudId()
        {
            return $this->solicitudId;
        }

        /**
         * Set solicitudId
         *
         * @param integer $intSolicitudId
         */
        public function setSolicitudId($intSolicitudId)
        {
            $this->solicitudId = $intSolicitudId;
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
