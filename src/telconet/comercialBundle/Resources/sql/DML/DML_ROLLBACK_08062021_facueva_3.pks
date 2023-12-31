/**
 * Se realiza Rollback de lista de perfiles para acción VER en módulos CONTRATO y DOCUMENTOS_AGREGADOS.
 *
 * @author Francisco Cueva <facueva@telconet.ec>
 * @version 1.10-06-2021
 */

DECLARE

CURSOR GET_ID_PERFIL IS 
SELECT SP.* FROM DB_SEGURIDAD.SIST_PERFIL SP 
WHERE UPPER(SP.NOMBRE_PERFIL) IN (
'MD_ABOGADO',
'MD_ABOGADO_COBRANZAS',
'MD_AGENTE_CALIDAD_RETENCION',
'MD_ASESOR_CALLCENTER_VENTAS',
'MD_ASISTENTE_ADMINISTRACION_CONTRATOS',
'MD_ASISTENTE_ADMINISTRATIVA_GERENCIAL',
'MD_ASISTENTE_ADMINISTRATIVA_VENTAS',
'MD_ASISTENTE_AUDITORIA',
'MD_ASISTENTE_COBRANZAS_BANCARIO',
'MD_ASISTENTE_COBRANZAS_JR',
'MD_ASISTENTE_COBRANZAS_SR',
'MD_ASISTENTE_FACTURACION',
'MD_ASISTENTE_SAI',
'MD_ASISTENTE_SERVICIO_CLIENTE',
'MD_AUDITOR_SENIOR',
'MD_COORDINADOR_CALIDAD',
'MD_COORDINADOR_COBRANZAS',
'MD_COORDINADOR_FACTURACION',
'MD_COORDINADOR_IPCC',
'MD_COORDINADOR_SERVICIO_CLIENTE',
'MD_COORDINADOR_VENTAS',
'MD_DISTRIBUIDOR_ATENCIONCLIENTE',
'MD_EJECUTIVO_VENTAS',
'MD_GERENTE_AUDITORIA',
'MD_GERENTE_COMERCIAL',
'MD_GERENTE_SAI',
'MD_JEFE_COBRANZAS',
'MD_JEFE_INV_MERCADOSDB',
'MD_JEFE_IPCC',
'MD_JEFE_VENTAS'
);  


CURSOR GET_DATA(ID_PERFIL NUMBER, ID_RELACION_SIST NUMBER) IS
SELECT SA.* FROM DB_SEGURIDAD.SEGU_ASIGNACION SA 
WHERE SA.PERFIL_ID = ID_PERFIL
AND SA.RELACION_SISTEMA_ID = ID_RELACION_SIST;

CURSOR GET_ID_RELACION IS
SELECT RS.* FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA RS 
WHERE RS.MODULO_ID IN (60, 302)
AND RS.ACCION_ID =8057;

bol_existe_info boolean;
c_datos GET_DATA%rowtype;


BEGIN

  FOR i IN GET_ID_PERFIL LOOP
    FOR j IN GET_ID_RELACION LOOP
        DELETE FROM  DB_SEGURIDAD.SEGU_ASIGNACION
        WHERE PERFIL_ID = i.ID_PERFIL
        AND RELACION_SISTEMA_ID = j.ID_RELACION_SISTEMA;
        COMMIT;
      
    END LOOP;  

  END LOOP;
END;