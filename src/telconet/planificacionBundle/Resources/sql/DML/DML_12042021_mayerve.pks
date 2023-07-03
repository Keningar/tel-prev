  /*
   *************************************************
   *                   PLANTILLA                   *
   *        Notificacion GTN Excedentes Materiales *
   *************************************************
  */

SET DEFINE OFF;
INSERT
INTO DB_COMUNICACION.ADMI_PLANTILLA
  (
    ID_PLANTILLA,
    NOMBRE_PLANTILLA,
    CODIGO,
    MODULO,
    PLANTILLA,
    ESTADO,
    FE_CREACION,
    USR_CREACION
  )
  VALUES
  (
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'NotificacionGTNExcesoMaterial',
    'NOTIEXCMATGTN',
    '',
    '<html>
	<head>
		<meta http-equiv=Content-Type content="text/html; charset=UTF-8">
		</head>
		<body>
			<table align="center" width="100%" cellspacing="0" cellpadding="5">
				<tr>
					<td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
						<img src="http://images.telconet.net/others/sit/notificaciones/logo.png"/>
					</td>
				</tr>
				<tr>
					<td style="border:1px solid #6699CC;">
						<table width="100%" cellspacing="0" cellpadding="5">
							<tr>
								<td colspan="2">
									<table cellspacing="0" cellpadding="2">
										<tr>
											<td colspan="2">Estimado,</td>
										</tr>
										<tr>
											<td></td>
										</tr>
										<tr>
											<td>A continuación información de la validación de excedente de material para el punto {{login}} con servicio {{producto}}.
												
												
												<br/>
											</td>
										</tr>
										<tr>
											<td>
												<br>{{mensaje}}
													
													
													<br>
														<br>

Favor proceder con la aprobación o rechazo de la misma.
															<br>
																<br>
Saludos.
        
															
																	<br>
																	</td>
																</tr>
																<td colspan="2">Atentamente,</td>
															</tr>
															<tr>
																<td></td>
															</tr>
															<tr>
																<td colspan="2">
																	<strong>Sistema TelcoS+</strong>
																</td>
															</tr>
														</table>
													</td>
												<tr>
												<tr>
													<td colspan="2">
														<br>
														</td>
													</tr>
												<table>
											</td>
										</tr>
										<tr>
											<td></td>
										</tr>
									</table>
								</body>
							</html>',
    'Activo',
    CURRENT_TIMESTAMP,
    'kyrodriguez'
  );
 
 

COMMIT;


  /*
   *************************************************
   *                   PLANTILLA                   *
   *        Notificacion Asesor Excedentes Materiales *
   *************************************************
  */



SET DEFINE OFF;
INSERT
INTO DB_COMUNICACION.ADMI_PLANTILLA
  (
    ID_PLANTILLA,
    NOMBRE_PLANTILLA,
    CODIGO,
    MODULO,
    PLANTILLA,
    ESTADO,
    FE_CREACION,
    USR_CREACION
  )
  VALUES
  (
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'NotificacionAsesorExcesoMaterial',
    'NOTIEXCMATASE',
    '',
    '<html>
	<head>
		<meta http-equiv=Content-Type content="text/html; charset=UTF-8">
		</head>
		<body>
			<table align="center" width="100%" cellspacing="0" cellpadding="5">
				<tr>
					<td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
						<img src="http://images.telconet.net/others/sit/notificaciones/logo.png"/>
					</td>
				</tr>
				<tr>
					<td style="border:1px solid #6699CC;">
						<table width="100%" cellspacing="0" cellpadding="5">
							<tr>
								<td colspan="2">
									<table cellspacing="0" cellpadding="2">
										<tr>
											<td colspan="2">Estimado asesor,</td>
										</tr>
										<tr>
											<td></td>
										</tr>
										<tr>
											<td>A continuación información de la validación de excedente de material para el punto {{login}} con servicio {{producto}}.
												
												<br/>
											</td>
										</tr>
										<tr>
											<td>
												<br>{{mensaje}}
													
													<br>
														<br>
															<br>
															</td>
														</tr>
														<td colspan="2">Atentamente,</td>
													</tr>
													<tr>
														<td></td>
													</tr>
													<tr>
														<td colspan="2">
															<strong>Sistema TelcoS+</strong>
														</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<br>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td></td>
								</tr>
							</table>
						</body>
					</html>',
    'Activo',
    CURRENT_TIMESTAMP,
    'kyrodriguez'
  );
 
 

COMMIT;

  /*
   **************************************************
   *                    ENVIO ARCHIVOS              *
   * 		Script definir rutas de archivos    *
   **************************************************
  */
  

insert into DB_GENERAL.admi_gestion_directorios
SELECT DB_GENERAL.SEQ_ADMI_GESTION_DIRECTORIOS.NEXTVAL,
(SELECT MAX(CODIGO_APP)+1 FROM  DB_GENERAL.admi_gestion_directorios) COD_APP,
1, 'TelcosWeb',593,'TN','Planificacion','Solicitud/Autorizaciones','Activo', TO_CHAR(SYSDATE, 'DD-Mon-YY') Fecha,
NULL,'mayerve',NULL
FROM DUAL;



COMMIT;

/
