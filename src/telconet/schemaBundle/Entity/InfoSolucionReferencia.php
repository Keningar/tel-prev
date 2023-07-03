<?php

    namespace telconet\schemaBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * telconet\schemaBundle\Entity\InfoSolucionReferencia
     *
     * @ORM\Table(name = "INFO_SOLUCION_REFERENCIA")
     * @ORM\Entity
     * @ORM\Entity(repositoryClass = "telconet\schemaBundle\Repository\InfoSolucionReferenciaRepository")
     */
    class InfoSolucionReferencia
    {
        /**
         * @var integer $id
         *
         * @ORM\Column(name = "ID_SOLUCION_REFERENCIA", type = "integer", nullable = false)
         * @ORM\Id
         * @ORM\GeneratedValue(strategy = "SEQUENCE")
         * @ORM\SequenceGenerator(sequenceName = "SEQ_INFO_SOLUCION_REFERENCIA", allocationSize = 1, initialValue = 1)
         */
        private $id;

        /**
         * @var integer $solucionDetIdA
         *
         * @ORM\Column(name = "SOLUCION_DET_ID_A", type = "integer", nullable = false)
         */
        private $solucionDetIdA;

        /**
         * @var integer $solucionDetIdB
         *
         * @ORM\Column(name = "SOLUCION_DET_ID_B", type = "integer", nullable = false)
         */
        private $solucionDetIdB;

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
         * Get solucionDetIdA
         *
         * @return integer solucionDetIdA
         */
        public function getSolucionDetIdA()
        {
            return $this->solucionDetIdA;
        }

        /**
         * Set solucionDetIdA
         *
         * @param integer $intSolucionDetIdA
         */
        public function setSolucionDetIdA($intSolucionDetIdA)
        {
            $this->solucionDetIdA = $intSolucionDetIdA;
        }

        /**
         * Get solucionDetIdB
         *
         * @return integer solucionDetIdB
         */
        public function getSolucionDetIdB()
        {
            return $this->solucionDetIdB;
        }

        /**
         * Set solucionDetIdB
         *
         * @param integer $intSolucionDetIdB
         */
        public function setSolucionDetIdB($intSolucionDetIdB)
        {
            $this->solucionDetIdB = $intSolucionDetIdB;
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
