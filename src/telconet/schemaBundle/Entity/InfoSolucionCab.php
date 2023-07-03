<?php

    namespace telconet\schemaBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * telconet\schemaBundle\Entity\InfoSolucionCab
     *
     * @ORM\Table(name = "INFO_SOLUCION_CAB")
     * @ORM\Entity
     * @ORM\Entity(repositoryClass = "telconet\schemaBundle\Repository\InfoSolucionCabRepository")
     */
    class InfoSolucionCab
    {
        /**
         * @var integer $id
         *
         * @ORM\Column(name = "ID_SOLUCION_CAB", type = "integer", nullable = false)
         * @ORM\Id
         * @ORM\GeneratedValue(strategy = "SEQUENCE")
         * @ORM\SequenceGenerator(sequenceName = "SEQ_INFO_SOLUCION_CAB", allocationSize = 1, initialValue = 1)
         */
        private $id;

        /**
         * @var integer $puntoId
         *
         * @ORM\Column(name = "PUNTO_ID", type = "integer", nullable = false)
         */
        private $puntoId;

        /**
         * @var integer $numeroSolucion
         *
         * @ORM\Column(name = "NUMERO_SOLUCION", type = "integer", nullable = false)
         */
        private $numeroSolucion;

        /**
         * @var string $nombreSolucion
         *
         * @ORM\Column(name = "NOMBRE_SOLUCION", type = "string", nullable = false)
         */
        private $nombreSolucion;

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
         * Get puntoId
         *
         * @return integer puntoId
         */
        public function getPuntoId()
        {
            return $this->puntoId;
        }

        /**
         * Set puntoId
         *
         * @param integer $intPuntoId
         */
        public function setPuntoId($intPuntoId)
        {
            $this->puntoId = $intPuntoId;
        }

        /**
         * Get numeroSolucion
         *
         * @return integer numeroSolucion
         */
        public function getNumeroSolucion()
        {
            return $this->numeroSolucion;
        }

        /**
         * Set numeroSolucion
         *
         * @param integer $intNumeroSolucion
         */
        public function setNumeroSolucion($intNumeroSolucion)
        {
            $this->numeroSolucion = $intNumeroSolucion;
        }

        /**
         * Get nombreSolucion
         *
         * @return string nombreSolucion
         */
        public function getNombreSolucion()
        {
            return $this->nombreSolucion;
        }

        /**
         * Set nombreSolucion
         *
         * @param string $strNombreSolucion
         */
        public function setNombreSolucion($strNombreSolucion)
        {
            $this->nombreSolucion = $strNombreSolucion;
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
            $this->estado = $strEstado;
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
            $this->usrCreacion = $strUsrCreacion;
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
            $this->fecCreacion = $objfecCreacion;
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
            $this->ipCreacion = $strIpCreacion;
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
            $this->usrUltMod = $strUsrUltMod;
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
            $this->fecUltMod = $objFecUltMod;
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
            $this->ipUltMod = $strIpUltMod;
        }
    }
