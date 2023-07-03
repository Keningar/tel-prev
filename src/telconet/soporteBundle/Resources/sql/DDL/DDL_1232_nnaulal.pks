CREATE OR REPLACE PACKAGE DB_COMERCIAL.SPKG_OBTENER_CAJA_ANTERIOR
AS 
/**
* Documentación para la funciÓn SPLIT
* La función SPLIT retorna la caja anterior del cliente si existe
*
* @param VARCHAR2 Fv_nombreTarea Recibe el nombre de la tarea
* @return VARCHAR2  Retorna el id de la tarea
* @author Néstor Naula <nnaulal@telconet.ec>
* @version 1.0 26-12-2018
*/

FUNCTION SPLIT(p_cadena     in VARCHAR2,
               p_simbolo    in VARCHAR2,
               p_ocurrencia in number) return VARCHAR2;

END SPKG_OBTENER_CAJA_ANTERIOR;
/ 

CREATE OR REPLACE PACKAGE BODY DB_COMERCIAL.SPKG_OBTENER_CAJA_ANTERIOR
AS

FUNCTION SPLIT (p_cadena     in VARCHAR2,
                p_simbolo    in VARCHAR2,
                p_ocurrencia in number) return VARCHAR2 Is

v_txt     VARCHAR2(200);
v_pos_ini integer := 0;
v_pos_fi  integer;

-- Función que separa cadenas de texto
Begin
    -- Función que separa cadenas de texto (p_cadena) por el símbolo p_simbolo
    -- Devuelve la ocurrencia p_ocurrencia
    -- Devuelve null si la ocurrencia no existe
    -- Devuelve el símbolo p_simbolo si no hay ningún carácter entre dos
    --ocurrencias de p_simbolo
    if p_ocurrencia > 1 then
      v_pos_ini := instr (str1 => p_cadena,
                          str2 => p_simbolo,
                          pos  => 1,
                          nth  => p_ocurrencia - 1);
    end if;
    --
    v_pos_fi := instr (str1 => p_cadena,       -- test string
                       str2 => p_simbolo,      -- string to locate
                       pos  => 1,              -- position
                       nth  => p_ocurrencia);  -- occurrence number

    if v_pos_ini = v_pos_fi then
      if p_ocurrencia = 1 then
        v_txt := substr (str1 => p_cadena, pos => 1);
      end if;
    elsif v_pos_ini > v_pos_fi then
      v_txt := substr (str1 => p_cadena, pos => v_pos_ini + 1);
    elsif v_pos_fi = v_pos_ini + 1 then
      v_txt := p_simbolo;
    else
      v_txt := substr (str1 => p_cadena, pos => v_pos_ini + 1,
                       len  => (v_pos_fi - v_pos_ini) - 1);
    end if;
    return v_txt;
End Split;

END SPKG_OBTENER_CAJA_ANTERIOR;

/