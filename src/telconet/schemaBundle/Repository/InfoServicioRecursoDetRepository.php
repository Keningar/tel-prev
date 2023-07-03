<?php

    namespace telconet\schemaBundle\Repository;

    use Doctrine\ORM\EntityRepository;

    class InfoServicioRecursoDetRepository extends EntityRepository
    {
        /**
         * Costo: 9
         * eliminarMV
         * 
         * Método que elimina una máquina virtual.
         * 
         * @param array $arrayParametros[ 'elementoId'       => id del elemento,
         *                                'nombreElemento'   => nombre del elemento,
         *                                'ipUltMod'         => IP en sesión,
         *                                'usrUltMod'        => Usuario en sesión,
         *                                'estado'           => estado de la máquina ]
         *
         * @return array $arrayResultado
         *
         * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
         * @version 1.0 30-06-2020
         */
        public function eliminarMV($arrayParametros)
        {
            $strSql              = "";
            $jsonParametros      = json_encode($arrayParametros);
            $strStatus           = str_pad('', 10, " ");
            $strMensaje          = str_pad('', 3000, " ");

            try
            {
                $strSql = "BEGIN  DB_INFRAESTRUCTURA.INKG_SOLUCIONES_TRANSACCION.P_ELIMINAR_MV(:Pcl_Request, "
                                                                                             .":Pv_Status,   "
                                                                                             .":Pv_Mensaje); "
                         ."END;";

                $objStmt = $this->_em->getConnection()->prepare($strSql);

                $objStmt->bindParam('Pcl_Request', $jsonParametros);
                $objStmt->bindParam('Pv_Status',   $strStatus);
                $objStmt->bindParam('Pv_Mensaje',  $strMensaje);
                $objStmt->execute();

                $arrayResultado['status']  = $strStatus;
                $arrayResultado['mensaje'] = $strMensaje;

            }
            catch(\Exception $e)
            {
                $arrayResultado['status']  = 'ERROR';
                $arrayResultado['mensaje'] = $e->getMessage();

            }

            return $arrayResultado;
        }

        /**
         * Costo: 9
         * reversarFactibilidadAlqServidor
         * 
         * Método que reversa la facitiblidad para el servicio Alquiler de servidor
         * y la del Pool de recusos asociado
         * 
         * @param array $arrayParametros[ 'servicioId'       => id del servicio,
         *                                'ipUltMod'         => IP en sesión,
         *                                'usrUltMod'        => Usuario en sesión,
         *                                'estado'           => estado de la máquina ]
         *
         * @return array $arrayResultado
         *
         * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
         * @version 1.0 30-06-2020
         */
        public function reversarFactibilidadAlqServidor($arrayParametros)
        {
            $strSql              = "";
            $jsonParametros      = json_encode($arrayParametros);
            $strStatus           = str_pad('', 10, " ");
            $strMensaje          = str_pad('', 3000, " ");

            try
            {
                 $strSql = " BEGIN  DB_INFRAESTRUCTURA.INKG_SOLUCIONES_TRANSACCION.P_REVERSAR_POOL_SERVIDOR(:Pcl_Request, "
                                                                                                        . " :Pv_Status,   "
                                                                                                        . " :Pv_Mensaje);  "
                          . "END;";

                $objStmt = $this->_em->getConnection()->prepare($strSql);

                $objStmt->bindParam('Pcl_Request', $jsonParametros);
                $objStmt->bindParam('Pv_Status',   $strStatus);
                $objStmt->bindParam('Pv_Mensaje',  $strMensaje);
                $objStmt->execute();

                $arrayResultado['status']  = $strStatus;
                $arrayResultado['mensaje'] = $strMensaje;

            }
            catch(\Exception $e)
            {
                $arrayResultado['status']  = 'ERROR';
                $arrayResultado['mensaje'] = $e->getMessage();

            }

            return $arrayResultado;
        }
    }
