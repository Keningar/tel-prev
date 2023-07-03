--se crea subred /31 para el uso de RUTASINTMPLS a partir de subredes /30

DECLARE
    
  subred      VARCHAR2(20) := NULL;
  octeto1     NUMBER       := NULL;
  octeto2     NUMBER       := NULL;
  octeto3     NUMBER       := NULL;
  octeto4     NUMBER       := NULL;
  gateway     varchar2(20) := NULL;
  ipInicial   varchar2(20) := NULL;
  ipFinal     varchar2(20) := NULL;

BEGIN

  FOR SUBREDES IN
  (
      select * from DB_INFRAESTRUCTURA.info_subred where uso = 'RUTASINTMPLS' and subred like '%/30' and estado = 'Activo'   
  )
  LOOP 
  
      subred := SUBREDES.GATEWAY;                
      
      SELECT 
        T.col_one, 
        SUBSTR(T.col_two, 1, INSTR(T.col_two, '.')-1) col2,
        SUBSTR(T.col_two, INSTR(T.col_two, '.')+1) col3 
        INTO octeto1, octeto2, subred
      FROM 
      (SELECT SUBSTR(subred, 1, INSTR(subred, '.')-1) AS col_one,
             SUBSTR(subred, INSTR(subred, '.')+1) AS col_two
        FROM DUAL) T;
      
      SELECT 
       SUBSTR(subred, 1, INSTR(subred, '.')-1) AS col_one,
       SUBSTR(subred, INSTR(subred, '.')+1) AS col_two
       INTO octeto3, octeto4
      FROM DUAL;
      
      octeto4 := octeto4 - 1;
      
      subred    := octeto1||'.'||octeto2||'.'||octeto3||'.'||octeto4;
      gateway   := octeto1||'.'||octeto2||'.'||octeto3||'.'||octeto4;
      
      octeto4 := octeto4 + 1;
      
      ipInicial := octeto1||'.'||octeto2||'.'||octeto3||'.'||octeto4;
      ipFinal   := octeto1||'.'||octeto2||'.'||octeto3||'.'||octeto4;      
      
      INSERT INTO DB_INFRAESTRUCTURA.INFO_SUBRED
        ( 
            ID_SUBRED,
            SUBRED, 
            IP_INICIAL,
            IP_FINAL,
            ESTADO,
            FE_CREACION,
            USR_CREACION, 
            IP_CREACION,
            MASCARA,
            GATEWAY,
            SUBRED_ID,
            EMPRESA_COD,
            VERSION_IP,
            TIPO,
            ELEMENTO_ID,
            USO
        )
        VALUES
        (
            DB_INFRAESTRUCTURA.SEQ_INFO_SUBRED.NEXTVAL,
            subred||'/31',
            ipInicial,
            ipFinal,
            'Activo',
            SYSDATE,
            'telcos',
            '127.0.0.1',
            '255.255.255.254',
            gateway,
            SUBREDES.ID_SUBRED,
            '10',
            'IPv4',
            'WAN',
            SUBREDES.ELEMENTO_ID,
            SUBREDES.USO
        );
        
        octeto4 := octeto4 + 1;
      
        subred    := octeto1||'.'||octeto2||'.'||octeto3||'.'||octeto4;
        gateway   := octeto1||'.'||octeto2||'.'||octeto3||'.'||octeto4;
        
        octeto4 := octeto4 + 1;
        
        ipInicial := octeto1||'.'||octeto2||'.'||octeto3||'.'||octeto4;
        ipFinal   := octeto1||'.'||octeto2||'.'||octeto3||'.'||octeto4;                    
      
        INSERT INTO DB_INFRAESTRUCTURA.INFO_SUBRED
        ( 
            ID_SUBRED,
            SUBRED, 
            IP_INICIAL,
            IP_FINAL,
            ESTADO,
            FE_CREACION,
            USR_CREACION, 
            IP_CREACION,
            MASCARA,
            GATEWAY,
            SUBRED_ID,
            EMPRESA_COD,
            VERSION_IP,
            TIPO,
            ELEMENTO_ID,
            USO
        )
        VALUES
        (
            DB_INFRAESTRUCTURA.SEQ_INFO_SUBRED.NEXTVAL,
            subred||'/31',
            ipInicial,
            ipFinal,
            'Activo',
            SYSDATE,
            'telcos',
            '127.0.0.1',
            '255.255.255.254',
            gateway,
            SUBREDES.ID_SUBRED,
            '10',
            'IPv4',
            'WAN',
            SUBREDES.ELEMENTO_ID,
            SUBREDES.USO
        );
  
  END LOOP;
  
  commit;

END;