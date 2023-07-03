/**
 *
 * Se crean parametros para el proyecto de subneteo de clase B
 *	 
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 22-09-2020
 */

DECLARE
  ln_id_param NUMBER := 0;
BEGIN

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'SUBNETEO DE CLASE B',
    'SUBNETEO DE CLASE B',
    'INFRAESTRUCTURA',
    'SUBNETEAR',
    'Activo',
    'rcabrera',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );               


  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'SUBNETEO DE CLASE B';
     
    
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    '16',
    '256',
    '255.255.0.0',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
);  

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    '17',
    '128',
    '255.255.128.0',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
);  


INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    '18',
    '64',
    '255.255.192.0',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
);  


INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    '19',
    '32',
    '255.255.224.0',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
);  


INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    '20',
    '16',
    '255.255.240.0',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
);  


INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    '21',
    '8',
    '255.255.248.0',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
);  


INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    '22',
    '4',
    '255.255.252.0',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
);  


INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    '23',
    '2',
    '255.255.254.0',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
);  

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    '24',
    '1',
    '255.255.255.0',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
);

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    '25',
    '128',    
    '255.255.255.128',
    '2',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
);

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,    
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    '26',
    '64',
    '255.255.255.192',
    '4',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
);

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    '27',
    '32',
    '255.255.255.224',
    '8',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
);

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    '28',
    '16',    
    '255.255.255.240',
    '16',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
);

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    '29',
    '8',    
    '255.255.255.248',
    '32',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
);

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    '30',
    '4',
    '255.255.255.252',
    '64',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
);

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    '31',
    '2',
    '255.255.255.254',
    '128',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
);


INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'mascara_maxima',
    '31',   
    '1',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
); 



COMMIT;

EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/ 
