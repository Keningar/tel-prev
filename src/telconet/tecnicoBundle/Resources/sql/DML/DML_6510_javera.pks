-- SE ACTUALIZAN LAS REDES CON PREFIJO /31 EN LA TABLA INFO_SUBRED Y SE ACTUALIZAN LAS IP DE INICIO Y FINAL EN LA CADA REGISTRO
--
DECLARE
  --
  CURSOR ipSubredes
  IS
    SELECT ID_SUBRED,
      SUBRED,
      trim(rtrim(subred, '/31')) as gateway,
      trim(rtrim(subred, '/31')) as ip_inicial,
      gateway as ip_final
    FROM db_infraestructura.info_subred
    WHERE MASCARA = '255.255.255.254'
    AND uso       = 'TELEFONIA';
  --
BEGIN
  --
  FOR c1 IN ipSubredes
  LOOP
    UPDATE db_infraestructura.info_subred
    SET gateway     = c1.gateway,
      ip_inicial    = c1.ip_inicial,
      ip_final      = c1.ip_final
    WHERE id_subred = c1.id_subred;
  END LOOP;
  --
  commit;
END;