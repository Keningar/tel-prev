-- Actualización de función precio con caracter punto y coma para que funcione correctamente en php la evaluación de la fórmula
UPDATE DB_COMERCIAL.ADMI_PRODUCTO
SET FUNCION_PRECIO = 'if ( [TIENE INTERNET]=="SI" &&[CANTIDAD DISPOSITIVOS]==1 ) { PRECIO=2.00;}
else if ( [TIENE INTERNET]=="SI" &&[CANTIDAD DISPOSITIVOS]==3 ) { PRECIO=2.75; }
else if ( [TIENE INTERNET]=="SI" &&[CANTIDAD DISPOSITIVOS]==5 ) { PRECIO=4.50; }
else if ( [TIENE INTERNET]=="SI" &&[CANTIDAD DISPOSITIVOS]==8 ) { PRECIO=8.00; }
else if ( [TIENE INTERNET]=="NO" &&[CANTIDAD DISPOSITIVOS]==1 ) { PRECIO=4.80; }
else if ( [TIENE INTERNET]=="NO" &&[CANTIDAD DISPOSITIVOS]==3 ) { PRECIO=7.00; }
else if ( [TIENE INTERNET]=="NO" &&[CANTIDAD DISPOSITIVOS]==5 ) { PRECIO=11.00; }
else if ( [TIENE INTERNET]=="NO" &&[CANTIDAD DISPOSITIVOS]==8 ) { PRECIO=15.00; }
else if ( [TIENE INTERNET]=="PLAN" &&[CANTIDAD DISPOSITIVOS]==3 ) { PRECIO=1; }'
WHERE ID_PRODUCTO = 210;
COMMIT;
/
