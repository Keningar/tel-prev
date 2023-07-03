DECLARE
  ln_id_param NUMBER := 0;
BEGIN


--Se crea el nuevo parámetro asociado al proyecto de asignación de recursos de red para nodos wifi
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PARAMETROS OS NODOS WIFI',
    'PARAMETROS UTILIZADOS EN EL PROYECTO DE OS NODO WIFI',
    'INFRAESTRUCTURA',
    'RECURSOS DE RED NODO WIFI',
    'Activo',
    'rcabrera',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );    

                
  --Se obtiene el id del registro recien creado
  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'PARAMETROS OS NODOS WIFI';
    

  --Se configuran los nombres para que aparezcan de manera dinámica en el Telcos
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'Orden Servicio NODO WIFI',
      'Concentrador L3MPLS Administracion', --Valor 1
      ' - Administración', --Valor2
      'Concentrador L3MPLS Navegacion', --Valor3
      ' - Navegación', --Valor4
      'Activo',
      'rcabrera',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL      
    );        
    
  COMMIT;
    

  SYS.DBMS_OUTPUT.PUT_LINE('Insertado Correctamente');
EXCEPTION
WHEN OTHERS THEN
  ROLLBACK;
DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
END;

/
