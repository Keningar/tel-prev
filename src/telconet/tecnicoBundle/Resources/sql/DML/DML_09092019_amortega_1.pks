SET DEFINE OFF;

--=======================================================================
--      Se crea plantilla para notificaciones de mantenimiento de Torres 
--=======================================================================

INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
(ID_PLANTILLA,
NOMBRE_PLANTILLA,
CODIGO,
MODULO,
PLANTILLA,
ESTADO,
FE_CREACION,
USR_CREACION,
FE_ULT_MOD,
USR_ULT_MOD,
EMPRESA_COD)
VALUES
(DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
'Mantenimiento de Torres TN',
'MANT_TORRES',
'TECNICO',
'<html>

<head>
    <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
</head>

<body>
    <table align="center" width="100%" cellspacing="0" cellpadding="5">
        <tr>
            <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
                <img alt="" src="http://images.telconet.net/others/telcos/logo.png" />
            </td>
        </tr>
        <tr>
            <td style="border:1px solid #6699CC;">
                <table width="100%" cellspacing="0" cellpadding="5">
                    <tr>
                        <td colspan="2">Estimado,</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            El presente correo es para informarle que se ha solicitado realizar un mantenimiento preventivo para la siguiente Torre:
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <strong>Datos Torre</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Nodo:</strong>
                        </td>
                        <td>{{ Nodo }}</td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Ubicacion:</strong>
                        </td>
                        <td>{{ Ubicacion }}</td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Canton:</strong>
                        </td>
                        <td>{{ Canton }}</td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Region:</strong>
                        </td>
                        <td>
                            {{ Region }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Ciclo de Mantenimiento:</strong>
                        </td>
                        <td>
                            {{ cicloMantenimiento }} Meses
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Jurisdiccion:</strong>
                        </td>
                        <td>{{ nombreJurisdiccion }}</td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Estado:</strong>
                        </td>
                        <td><strong><label style="color:red">{{ estadoNodo }}</label></strong></td>
                    </tr>
                    <tr>
                        <td colspan="2"><br /></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>

            </td>
        </tr>
        <tr>
            <td><strong>
                    <font size="2" face="Tahoma">Telconet S.A.</font>
                </strong></p>
            </td>
        </tr>
    </table>
</body>

</html>',
'Activo',
SYSDATE,
'amortega',
NULL,
NULL,
NULL)
;

COMMIT;

/
