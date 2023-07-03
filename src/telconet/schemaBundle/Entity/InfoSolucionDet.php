<?php

    namespace telconet\schemaBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * telconet\schemaBundle\Entity\InfoSolucionDet
     *
     * @ORM\Table(name = "INFO_SOLUCION_DET")
     * @ORM\Entity
     * @ORM\Entity(repositoryClass = "telconet\schemaBundle\Repository\InfoSolucionDetRepository")
     */
    class InfoSolucionDet
    {
        /**
         * @var integer $id
         *
         * @ORM\Column(name = "ID_SOLUCION_DET", type = "integer", nullable = false)
         * @ORM\Id
         * @ORM\GeneratedValue(strategy = "SEQUENCE")
         * @ORM\SequenceGenerator(sequenceName = "SEQ_INFO_SOLUCION_DET", allocationSize = 1, initialValue = 1)
         */
        private $id;

        /**
         * @var InfoSolucionCab
         *
         * @ORM\ManyToOne(targetEntity="InfoSolucionCab")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="SOLUCION_CAB_ID", referencedColumnName="ID_SOLUCION_CAB")
         * })
         */
        private $solucionCabId;

        /**
         * @var integer $servicioId
         *
         * @ORM\Column(name = "SERVICIO_ID", type = "integer", nullable = false)
         */
        private $servicioId;

        /**
         * @var string $tipoSolucion
         *
         * @ORM\Column(name = "TIPO_SOLUCION", type = "string", nullable = false)
         */
        private $tipoSolucion;

        /**
         * @var string $descripcion
         *
         * @ORM\Column(name = "DESCRIPCION", type = "string", nullable = false)
         */
        private $descripcion;

        /**
         * @var string $esCore
         *
         * @ORM\Column(name = "ES_CORE", type = "string", nullable = false)
         */
        private $esCore;

        /**
         * @var string $esPreferencial
         *
         * @ORM\Column(name = "ES_PREFERENCIAL", type = "string", nullable = false)
         */
        private $esPreferencial;

        /**
         * @var string $estado
         *
         * @ORM\Column(name = "ESTADO", type = "string", nullable = false)
         */
        private $estado;

        /**
         * @var string $usrCreacion
         *
         * @ORM\Column(name = "USR_CREACION", type = "string", nullable = false)
         */
        private $usrCreacion;

        /**
         * @var datetime $fecCreacion
         *
         * @ORM\Column(name = "FEC_CREACION", type = "datetime", nullable = false)
         */
        private $fecCreacion;

        /**
         * @var string $ipCreacion
         *
         * @ORM\Column(name = "IP_CREACION", type = "string", nullable = false)
         */
        private $ipCreacion;

        /**
         * @var string $usrUltMod
         *
         * @ORM\Column(name = "USR_ULT_MOD", type = "string", nullable = false)
         */
        private $usrUltMod;

        /**
         * @var datetime $fecUltMod
         *
         * @ORM\Column(name = "FEC_ULT_MOD", type = "datetime", nullable = false)
         */
        private $fecUltMod;

        /**
         * @var string $ipUltMod
         *
         * @ORM\Column(name = "IP_ULT_MOD", type = "string", nullable = false)
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
         * Get solucionCabId
         *
         * @return telconet\schemaBundle\Entity\InfoSolucionCab
         */
        public function getSolucionCabId()
        {
            return $this->solucionCabId;
        }

        /**
         * Get servicioId
         *
         * @return integer servicioId
         */
        public function getServicioId()
        {
            return $this->servicioId;
        }

        /**
         * Get tipoSolucion
         *
         * @return string tipoSolucion
         */
        public function getTipoSolucion()
        {
            return $this->tipoSolucion;
        }

        /**
         * Get descripcion
         *
         * @return string descripcion
         */
        public function getDescripcion()
        {
            return $this->descripcion;
        }

        /**
         * Get esCore
         *
         * @return string esCore
         */
        public function getEsCore()
        {
            return $this->esCore;
        }

        /**
         * Get esPreferencial
         *
         * @return string esPreferencial
         */
        public function getEsPreferencial()
        {
            return $this->esPreferencial;
        }

        /**
         * Set solucionCabId
         *
         * @param telconet\schemaBundle\Entity\InfoSolucionCab $objInfoSolucionCab
         */
        public function setSolucionCabId($objInfoSolucionCab)
        {
            $this->solucionCabId = $objInfoSolucionCab;
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
         * Set tipoSolucion
         *
         * @param string $strTipoSolucion
         */
        public function setTipoSolucion($strTipoSolucion)
        {
            $this->tipoSolucion = $strTipoSolucion;
        }

        /**
         * Set descripcion
         *
         * @param string $strDescripcion
         */
        public function setDescripcion($strDescripcion) {
            $this->descripcion = $strDescripcion;
        }

        /**
         * Set esCore
         *
         * @param string $strEsCore
         */
        public function setEsCore($strEsCore) {
            $this->esCore = $strEsCore;
        }

        /**
         * Set esPreferencial
         *
         * @param string $strEsPreferencial
         */
        public function setEsPreferencial($strEsPreferencial) {
            $this->esPreferencial = $strEsPreferencial;
        }

        /**
         * Get estado
         *
         * @return string estado
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
            $this->estado  =  $strEstado;
        }

        /**
         * Get usrCreacion
         *
         * @return string usrCreacion
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
            $this->usrCreacion  =  $strUsrCreacion;
        }

        /**
         * Get fecCreacion
         *
         * @return datetime fecCreacion
         */
        public function getFecCreacion()
        {
            return $this->fecCreacion;
        }

        /**
         * Set fecCreacion
         *
         * @param datetime $objfecCreacion
         */
        public function setFecCreacion($objfecCreacion)
        {
            $this->fecCreacion  =  $objfecCreacion;
        }

        /**
         * Get ipCreacion
         *
         * @return string ipCreacion
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
            $this->ipCreacion  =  $strIpCreacion;
        }

        /**
         * Get usrUltMod
         *
         * @return string usrUltMod
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
            $this->usrUltMod  =  $strUsrUltMod;
        }

        /**
         * Get fecUltMod
         *
         * @return datetime fecUltMod
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
            $this->fecUltMod  =  $objFecUltMod;
        }

        /**
         * Get ipUltMod
         *
         * @return string ipUltMod
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
            $this->ipUltMod  =  $strIpUltMod;
        }
    }
