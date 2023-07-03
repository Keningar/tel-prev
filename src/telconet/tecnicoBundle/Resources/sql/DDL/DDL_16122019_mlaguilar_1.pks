--Creando paquete con proceso para transformar coordenadas
--------------------------------------------------------
--  DDL for Package SDE.ARPK_SINC_ARCGIS
--------------------------------------------------------

  CREATE OR REPLACE PACKAGE SDE.ARPK_SINC_ARCGIS AS 
 
  /**
   * Documentación para P_UTMAGRADOS
   * Procedimiento que transforma coordenadas de formato UTM a Grados, Minutos y Segundos
   * 
   * @param Pn_LATITUD         IN OUT NUMBER Latitud en formato UTM
   * @param Pn_LONGITUD        IN OUT NUMBER Longitud en formato UTM
   * @param Pn_GLAT            OUT NUMBER Latitud en grados
   * @param Pn_MLAT            OUT NUMBER Latitud en minutos
   * @param Pn_SLAT            OUT NUMBER Latitud en segundos
   * @param Pn_GLON            OUT NUMBER Longitud en grados
   * @param Pn_MLON            OUT NUMBER Longitud en minutos
   * @param Pn_SLON            OUT NUMBER Longitud en segundos
   *
   * @author Marlon Aguilar <mlaguilar@telconet.ec>
   * @version 1.0 18/12/2019
   */
  PROCEDURE P_UTMAGRADOS(
  Pn_LATITUD   IN OUT NUMBER, 
  Pn_LONGITUD  IN OUT NUMBER,
  Pn_GLAT      OUT NUMBER, 
  Pn_MLAT      OUT NUMBER, 
  Pn_SLAT      OUT NUMBER, 
  Pn_GLON      OUT NUMBER, 
  Pn_MLON      OUT NUMBER, 
  Pn_SLON      OUT NUMBER
);
  
END ARPK_SINC_ARCGIS;

/

--------------------------------------------------------
--  DDL for Package Body SDE.ARPK_SINC_ARCGIS
--------------------------------------------------------

CREATE OR REPLACE PACKAGE BODY SDE.ARPK_SINC_ARCGIS AS

PROCEDURE P_UTMAGRADOS
(
  Pn_LATITUD   IN OUT NUMBER, 
  Pn_LONGITUD  IN OUT NUMBER,
  Pn_GLAT      OUT NUMBER, 
  Pn_MLAT      OUT NUMBER, 
  Pn_SLAT      OUT NUMBER, 
  Pn_GLON      OUT NUMBER, 
  Pn_MLON      OUT NUMBER, 
  Pn_SLON      OUT NUMBER
) AS

Ln_X        NUMBER (16,6);
Ln_Y        NUMBER (16,6);
Ln_ZONE     NUMBER (2) := 17;
Lv_SN       VARCHAR2(1) :='S';
Ln_E2       NUMBER (38,12);
Ln_E2CUAD   NUMBER (38,12);
Ln_C        NUMBER (38,12);
Ln_S        NUMBER (38,12);
Ln_LAT      NUMBER (38,12);
Ln_V        NUMBER (38,12);
Ln_A        NUMBER (38,12);
Ln_A1       NUMBER (38,12);
Ln_A2       NUMBER (38,12);
Ln_J2       NUMBER (38,12);
Ln_J4       NUMBER (38,12);
Ln_J6       NUMBER (38,12);
Ln_ALFA     NUMBER (38,12);
Ln_BETA     NUMBER (38,12);
Ln_GAMA     NUMBER (38,12);
Ln_BM       NUMBER (38,12);
Ln_B        NUMBER (38,12);
Ln_EPSI     NUMBER (38,12);
Ln_EPS      NUMBER (38,12);
Ln_NAB      NUMBER (38,12);
Ln_SENOHEPS NUMBER (38,12);
Ln_DELT     NUMBER (38,12);
Ln_TAO      NUMBER (38,12);
Ln_LATITUD  NUMBER (38,12);
Ln_LONGITUD NUMBER (38,12);
Ln_T        NUMBER (38,12);
Ln_SA       NUMBER (30,12);
Ln_SB       NUMBER (30,12);
Ln_PI       NUMBER (30,12);
BEGIN
  Ln_X := Pn_LONGITUD;
  Ln_Y := Pn_LATITUD;
  --
  IF (Lv_SN='S') THEN
    Ln_Y:=Ln_Y-10000000;
  END IF;
  --
  Ln_X        := Ln_X - 500000;
  Ln_SA       := 6378137.000000;
  Ln_SB       := 6356752.314245;
  Ln_PI       := 3.14159265358;
  Ln_E2       := POWER (POWER(Ln_SA,2)-POWER(Ln_SB,2), 0.5)/Ln_SB;
  Ln_E2CUAD   := POWER(Ln_E2,2);
  Ln_C        := POWER(Ln_SA,2)/Ln_SB;
  Ln_S        := ((Ln_ZONE*6)-183);
  Ln_LAT      := Ln_Y/(6366197.724 * 0.9996);
  Ln_V        := (Ln_C * 0.9996)/POWER( (1 + (Ln_E2CUAD*  POWER(COS(Ln_LAT),2))   ),0.5);
  Ln_A        := Ln_X / Ln_V;
  Ln_A1       := SIN(2*Ln_LAT);
  Ln_A2       := Ln_A1 * POWER(COS(Ln_LAT),2);
  Ln_J2       := Ln_LAT + (Ln_A1/2);
  Ln_J4       := ((3*Ln_J2)+Ln_A2)/4;
  Ln_J6       := ((5*Ln_J4)+(Ln_A2 * POWER(COS(Ln_LAT),2)))/3;
  Ln_ALFA     := (3/4) * Ln_E2CUAD;
  Ln_BETA     := (5/3) * POWER(Ln_ALFA,2);
  Ln_GAMA     := (35/27) * POWER(Ln_ALFA,3);
  Ln_BM       := 0.9996 * Ln_C * (Ln_LAT - Ln_ALFA * Ln_J2 + Ln_BETA * Ln_J4 - Ln_GAMA * Ln_J6);
  Ln_B        := (Ln_Y - Ln_BM)/Ln_V;
  Ln_EPSI     := ((Ln_E2CUAD * POWER(Ln_A,2))/2) * POWER(COS(Ln_LAT),2);
  Ln_EPS      := Ln_A * (1 - (Ln_EPSI/3));
  Ln_NAB      := (Ln_B * (1 - Ln_EPSI)) + Ln_LAT;
  Ln_SENOHEPS := (EXP(Ln_EPS) - EXP(-Ln_EPS))/2;
  Ln_DELT     := ATAN(Ln_SENOHEPS) / COS(Ln_NAB);
  Ln_TAO      := ATAN(COS(Ln_DELT) * TAN(Ln_NAB));
  Ln_LONGITUD := (Ln_DELT *(180/Ln_PI)) + Ln_S;
  Ln_LATITUD  := (Ln_LAT + (1 + Ln_E2CUAD * POWER(COS(Ln_LAT),2) - (3/2) * Ln_E2CUAD * SIN(Ln_LAT) * COS(Ln_LAT) * (Ln_TAO - Ln_LAT)) * (Ln_TAO - Ln_LAT)) * (180/Ln_PI);
  
  --LATITUD A GRADOS MINUTOS, SEGUNDOS
  Pn_GLAT := TRUNC (Ln_LATITUD,0);
  Ln_T    := Ln_LATITUD -Pn_GLAT;
  --
  IF Pn_GLAT < 0 THEN 
    Ln_T := Ln_T * -1;
  END IF;
  --
  Pn_MLAT := TRUNC((Ln_T *60),0);
  Ln_T    :=Ln_T*60 - TRUNC((Ln_T *60),0);
  Pn_SLAT := TRUNC((Ln_T *60),1);
  
  -- LONGITUD A GRADOS MINUTOS Y SEGUNDOS
  Pn_GLON := TRUNC (Ln_LONGITUD,0);
  Ln_T    := Ln_LONGITUD -Pn_GLON;
  --
  IF Pn_GLON < 0 THEN 
    Ln_T := Ln_T * -1;
    Pn_GLON := Pn_GLON * -1;
  END IF;
  --
  Pn_MLON := TRUNC((Ln_T *60),0);
  Ln_T    :=Ln_T*60 - TRUNC((Ln_T *60),0);
  Pn_SLON := TRUNC((Ln_T *60),1);
  
  --SETEAR DATOS DE RETORNO EN DECIMAL
  Pn_LATITUD :=Ln_LATITUD;
  Pn_LONGITUD:=Ln_LONGITUD;
END P_UTMAGRADOS;

END ARPK_SINC_ARCGIS;

/