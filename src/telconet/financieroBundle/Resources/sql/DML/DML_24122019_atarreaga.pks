/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 24-12-2019    
 * Se crea DML para cambiar estado a 'Eliminado' registros en la tabla 'DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC' 
 * para regularización en producción por cambio de ciclo.
 */

UPDATE DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC 
SET ESTADO = 'Eliminado'   
WHERE 
PERSONA_EMPRESA_ROL_ID IN (
SELECT TABLA.ID_PERSONA_ROL FROM 
(SELECT ise.punto_facturacion_id,
              ipda.gasto_administrativo,
              ipe.paga_iva,
              ise.tipo_orden,
              DB_FINANCIERO.FNCK_CONSULTS.F_VALIDA_CLIENTE_COMPENSADO(iper.ID_PERSONA_ROL, iper.OFICINA_ID, ier.EMPRESA_COD, ip.SECTOR_ID, ise.PUNTO_FACTURACION_ID ) COMPENSACION,
              (select MAX(IPERC.VALOR)
              from DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC iperc,
                   DB_COMERCIAL.ADMI_CARACTERISTICA ac
              where iperc.PERSONA_EMPRESA_ROL_ID=iper.id_persona_rol
              and AC.ID_CARACTERISTICA=IPERC.CARACTERISTICA_ID
              and iperc.ESTADO='Activo'
              and AC.DESCRIPCION_CARACTERISTICA='CICLO_FACTURACION') as ciclo_id,
              (DB_FINANCIERO.FNCK_FACTURACION.F_GET_FECHA_ULT_FACT(ise.punto_facturacion_id, ise.id_servicio)) AS fe_UltFact_Periodo,
              iper.ID_PERSONA_ROL,
              iper.OFICINA_ID
            FROM DB_COMERCIAL.info_servicio_historial ish
            JOIN DB_COMERCIAL.info_servicio ise
            ON ise.id_servicio=ish.servicio_id
            LEFT JOIN DB_COMERCIAL.info_plan_cab ipc
            ON ipc.id_plan=ise.plan_id
            JOIN DB_COMERCIAL.info_punto ip
            ON ip.id_punto=ise.punto_facturacion_id
            LEFT JOIN DB_COMERCIAL.info_punto_dato_adicional ipda
            ON ipda.punto_id=ip.id_punto
            JOIN DB_COMERCIAL.info_persona_empresa_rol iper
            ON iper.id_persona_rol=ip.persona_empresa_rol_id
            JOIN DB_COMERCIAL.info_persona ipe
            ON ipe.id_persona = iper.persona_id
            JOIN DB_COMERCIAL.info_empresa_rol ier
            ON ier.id_empresa_rol=iper.empresa_rol_id
            JOIN DB_COMERCIAL.admi_tipo_negocio atn
            ON atn.id_tipo_negocio=ip.tipo_negocio_id
            JOIN DB_GENERAL.admi_rol ar
            ON ar.id_rol                 = ier.rol_id
            WHERE ish.estado             = 'Activo'
            AND ise.estado               = 'Activo'
            AND ise.cantidad             > 0
            AND ier.empresa_cod          = 18
            AND ise.es_venta             = 'S'
            AND ar.descripcion_rol       = 'Cliente'
            AND ise.precio_venta         > 0
            AND atn.codigo_tipo_negocio <> 'ISPM'
            AND ise.frecuencia_producto  = 1
            AND EXISTS
              (SELECT 1
               FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPERCA,
                    DB_COMERCIAL.ADMI_CARACTERISTICA ACA
               WHERE IPERCA.PERSONA_EMPRESA_ROL_ID=iper.id_persona_rol
               AND IPERCA.VALOR='N'
               AND IPERCA.ESTADO='Activo'
               AND IPERCA.CARACTERISTICA_ID=ACA.ID_CARACTERISTICA
               AND ACA.DESCRIPCION_CARACTERISTICA='CAMBIO_CICLO_FACTURADO')
            GROUP BY ise.punto_facturacion_id,
              ipe.paga_iva,
              ipda.gasto_administrativo,
              ise.tipo_orden,
              (DB_FINANCIERO.FNCK_FACTURACION.F_GET_FECHA_ULT_FACT(ise.punto_facturacion_id, ise.id_servicio)),
              iper.ID_PERSONA_ROL,
              iper.OFICINA_ID,
              ip.SECTOR_ID,
              ier.EMPRESA_COD) TABLA )
AND ESTADO            = 'Activo'
AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA 
                        FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
                        WHERE DESCRIPCION_CARACTERISTICA = 'CAMBIO_CICLO_FACTURADO');  

COMMIT;
/
