  /**
   * Permisos de ejecución para comercial
   * @author Jessenia Piloso <jpiloso@telconet.ec>
   * @version 1.0
   * @since 07-12-2022
   */
GRANT READ, WRITE ON DIRECTORY RESPSOLARIS TO DB_COMERCIAL;
GRANT EXECUTE ON DB_GENERAL.GNKG_AS_XLSX  TO DB_COMERCIAL;
GRANT READ, WRITE ON DIRECTORY RESPSOLARIS TO DB_GENERAL;
