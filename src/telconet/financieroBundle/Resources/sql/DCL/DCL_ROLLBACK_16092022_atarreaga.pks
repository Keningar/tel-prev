/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 16-09-2022   
 * Se crea script para reversar privilegio de la tabla.
 */ 

--Se reversa privilegio
REVOKE REFERENCES ON DB_COMERCIAL.ADMI_CARACTERISTICA FROM DB_FINANCIERO CASCADE CONSTRAINTS;

COMMIT;
/
