/**
 *
 * Se crean plantillas para el producto HBO-MAX, 
 *	 
 * @author Alberto Arias <farias@telconet.ec>
 * @version 1.0 09-08-2022
 */

set define off;

-- PLANTILLA CUANDO SE CREA EL NUEVO SERVICIO
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
(ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES(DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL, 'HBO-MAX Nuevo servicio', 'HBO-MAX-NUEVO', 'TECNICO',
TO_CLOB('<html style="margin:0;padding:0" lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="format-detection" content="telephone=no">
  <title>Asunto</title>
  <style type="text/css">
    @media screen and (max-width: 480px) {
      .mailpoet_button {
        width: 100% !important;
      }
    }

    @media screen and (max-width: 599px) {
      .mailpoet_header {
        padding: 10px 20px;
      }
') || TO_CLOB('
      .mailpoet_button {
        width: 100% !important;
        padding: 5px 0 !important;
        box-sizing: border-box !important;
      }

      div,
      .mailpoet_cols-two,
      .mailpoet_cols-three {
        max-width: 100% !important;
      }
    }
  </style>') || TO_CLOB('
  <!--[if !mso]><link href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwND40PDg+OG8/b2w8P25rOD08OzpsOmw4bjQ+NT04aDxoPjo0P2xrPit5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ffonts.googleapis.com%2fcss%3ffamily%3dLato%3a400%2c400i%2c700%2c700i|Oswald:400,400i,700,700i" rel="stylesheet"><![endif]-->
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }
') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }
') || TO_CLOB('
    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }

    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }

    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }

    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }

    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>') || TO_CLOB('
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }
') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }
') || TO_CLOB('
    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }

    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }

    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }

    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }

    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>') || TO_CLOB('
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }
') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }
') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }

    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }

    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }
') || TO_CLOB('
    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }

    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }

    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }

    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }

    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }

    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }

    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>') || TO_CLOB('
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }
') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }
') || TO_CLOB('
    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }

    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }

    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }

    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }

    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }

    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }

    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }

    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }
') || TO_CLOB('
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }
') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }
') || TO_CLOB('
    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }

    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }

    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }

    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }

    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }

    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }

    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }

    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>') || TO_CLOB('
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }
') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }
') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }

    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }

    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }
') || TO_CLOB('
    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }

    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }

    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }

    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }

    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }

    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }
') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>') || TO_CLOB('
</head>

<body style="margin:0;padding:0;background-color:#d4d4d4" topmargin="0" marginwidth="0" marginheight="0" leftmargin="0">
  <table class="mailpoet_template"
    style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellspacing="0"
    cellpadding="0" border="0">
    <tbody>
      <tr>
        <td class="mailpoet_preheader"
          style="border-collapse:collapse;display:none;visibility:hidden;mso-hide:all;font-size:1px;color:#333333;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;-webkit-text-size-adjust:none"
          height="1">

        </td>
      </tr>
      <tr>') || TO_CLOB('
        <td class="mailpoet-wrapper" style="border-collapse:collapse;background-color:#d4d4d4" valign="top"
          align="center">
          <!--[if mso]>
                <table align="center" border="0" cellspacing="0" cellpadding="0"
                       width="660">
                    <tr>
                        <td class="mailpoet_content-wrapper" align="center" valign="top" width="660">
                <![endif]-->
          <table class="mailpoet_content-wrapper"
            style="border-collapse:collapse;background-color:#000000;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;max-width:660px;width:100%"
            width="660" cellspacing="0" cellpadding="0" border="0">
            <tbody>
              <tr>') || TO_CLOB('
                <td class="mailpoet_content" style="border-collapse:collapse;background-color:#ffffff!important"
                  bgcolor="#ffffff" align="center">
                  <table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                    width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td style="border-collapse:collapse;padding-left:0;padding-right:0">
                          <table class="mailpoet_cols-one"
                            style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0"
                            width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                              <tr>') || TO_CLOB('
                                <td class="mailpoet_image " style="border-collapse:collapse" valign="top"
                                  align="center">
                                  <img
                                    src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwOW86PT5uNT41aG5sOzw5O2xvOz47aTg8aWs5PjtrbGtoP281ODloPCt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f8db9c45f7b8669635636e6%2f799x163%2f5b839e3aebc1440edba9fdbf4dafd3e1%2ftoop-mail2.jpg&fmlBlkTk"
                                    alt="NETLIFE PLAY"
                                    style="height:auto;max-width:100%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"
                                    width="660"></img>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>') || TO_CLOB('
                    </tbody>
                  </table>
                </td>
              </tr>
              <tr>') || TO_CLOB('
                <td class="mailpoet_content-cols-three"
                  style="border-collapse:collapse;background-color:  !important;background-color: ;" bgcolor="#ffffff"
                  align="left">
                  <table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                    width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>') || TO_CLOB('
                        <td style="border-collapse:collapse;font-size:0;background-color: #ff6700;" align="center">
                          <!--[if mso]>
                  <table border="0" width="100%" cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>
      <td width="220" valign="top">
        <![endif]-->
                          <div style="display:inline-block; max-width:54px; vertical-align:top; width:100%;" wfd-id="2">
                            <table class="mailpoet_cols-three"
                              style="border-collapse:collapse;width:100%;max-width:54px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0"
                              width="220" cellspacing="0" cellpadding="0" border="0" align="right">
                              <tbody></tbody>
                            </table>
                          </div>
                          <!--[if mso]>
      </td>
      <td width="220" valign="top">') || TO_CLOB('
        <![endif]-->
                          <div style="display:inline-block; max-width:552px; vertical-align:top; width:100%;"
                            wfd-id="1">
                            <table class="mailpoet_cols-three"
                              style="border-collapse:collapse;width:100%;max-width:552px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;background-color: ededed;"
                              width="220" cellspacing="0" cellpadding="0" border="0" align="right">
                              <tbody>
                                <tr>
                                  <td class="mailpoet_image " style="border-collapse:collapse" valign="top"
                                    align="center">
                                    <img
                                      src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwbj9rOD01aTxoPjlpPWk8Ojk7bzpuND9pPz1raWk4PT5pNW49aztoOit5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2fHeader-HBOMax.jpg&fmlBlkTk"
                                      alt="noggin"
                                      style="height:auto;max-width:70%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"
                                      width="220"></img>
                                  </td>
                                </tr>
                                <tr>') || TO_CLOB('
                                  <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side"
                                    style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word"
                                    valign="top">
                                    <table
                                      style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                                      width="100%" cellpadding="0">
                                      <tbody>
                                        <tr>
                                          <td class="mailpoet_paragraph"
                                            style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:center;">
                                            <strong>Hola {{ cliente }}</strong>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>') || TO_CLOB('
                                <tr>
                                  <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side"
                                    style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word"
                                    valign="top">
                                    <table
                                      style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                                      width="100%" cellpadding="0">
                                      <tbody>
                                        <tr>') || TO_CLOB('
                                          <td class="mailpoet_paragraph"
                                            style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:justify;">
                                            <p> <strong>NETLIFE</strong> te da la bienvenida al servicio
                                              de&nbsp;<strong>HBO Max.&nbsp;</strong>A continuaci&oacute;n, se muestra
                                              tu usuario de acceso al servicio que fue proporcionado al momento de la
                                              contrataci&oacute;n.<strong>&nbsp;&nbsp;</strong></p>
                                            <p><strong>Al hacer click para acceder a tu servicio podr&aacute;s
                                                configurar tu contrase&ntilde;a de acceso a HBO Max.</strong></p>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>') || TO_CLOB('
                                  </td>
                                </tr>
                                <tr>
                                  <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side"
                                    style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word"
                                    valign="top">
                                    <h1
                                      style="margin:0 0 9px;font-family:Oswald,&#39;Trebuchet MS&#39;,&#39;Lucida Grande&#39;,&#39;Lucida Sans Unicode&#39;,&#39;Lucida Sans&#39;,Tahoma,sans-serif;font-size:30px;line-height:36px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal">
                                      <strong></strong><strong style="color: #000000;">DATOS DE ACCESO<br></strong></h1>
                                  </td>
                                </tr>') || TO_CLOB('
                                <tr>
                                  <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side"
                                    style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word"
                                    valign="top">
                                    <h2
                                      style="margin:0 0 6px;color:#706f6f;font-family:Oswald,&#39;Trebuchet MS&#39;,&#39;Lucida Grande&#39;,&#39;Lucida Sans Unicode&#39;,&#39;Lucida Sans&#39;,Tahoma,sans-serif;font-size:20px;line-height:24px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal">
                                      <strong>Usuario: {{ usuario }}</strong></h2>
                                  </td>
                                </tr>') || TO_CLOB('
                                <tr>
                                  <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side"
                                    style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word"
                                    valign="top">
                                    <h3
                                      style="margin:0 0 5.4px;color:875bd8;font-family:Oswald,&#39;Trebuchet MS&#39;,&#39;Lucida Grande&#39;,&#39;Lucida Sans Unicode&#39;,&#39;Lucida Sans&#39;,Tahoma,sans-serif;font-size:18px;line-height:21.6px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal;padding-top: 30px;">
                                      <strong></strong><strong style="color: #000000;">ACCEDE A TU SERVICIO<br></strong>
                                    </h3>
                                  </td>') || TO_CLOB('
                                </tr>
                                <tr>
                                  <td class="mailpoet_image " style="border-collapse:collapse" valign="top"
                                    align="center">
                                    <a
                                      href="{{url}}"><img
                                        src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwaT1pOThoOD1vPGhrb29uazQ/PDloOjtuPGs6PDlsOzg4NDxvOzU4NSt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2fBoton-Hbo.png&fmlBlkTk"
                                        alt="CLICK AQUI"
                                        style="height:auto;max-width:40%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"
                                        width="220"></img></a>
                                  </td>
                                </tr>') || TO_CLOB('
                                <tr>
                                  <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side"
                                    style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word"
                                    valign="top">
                                    <table
                                      style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                                      width="100%" cellpadding="0">
                                      <tbody>') || TO_CLOB('
                                        <tr>
                                          <td class="mailpoet_paragraph"
                                            style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:center">
                                            <o:p style="font-size: 14.2px">&ldquo;El Servicio contratado HBO Max tiene
                                              un costo mensual de $xxx el mismo que ser&aacute; incluido en su factura
                                              mensual del Servicio de Internet. &ldquo;&nbsp;</o:p>
                                            <p> Gracias por ser parte de NETLIFE,<br>
                                              EQUIPO NETLIFE &bull; 39-20-000 </p>
                                          </td>

                                        </tr>') || TO_CLOB('
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                          <!--[if mso]>
      </td>
      <td width="220" valign="top">') || TO_CLOB('
        <![endif]-->
                          <div style="display:inline-block; max-width:54px; vertical-align:top; width:100%;" wfd-id="0">
                            <table class="mailpoet_cols-three"
                              style="border-collapse:collapse;width:100%;max-width:54px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0"
                              width="220" cellspacing="0" cellpadding="0" border="0" align="right">
                              <tbody></tbody>
                            </table>
                          </div>
                          <!--[if mso]>
      </td>
                  </tr>
                </tbody>') || TO_CLOB('
              </table>
            <![endif]-->
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <table style="background-color: #000000;" width="660" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>') || TO_CLOB('
                        <th style="width: 220px" scope="row"><a
                            href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwNG48a2k7aWlvbD41PD0/bGg0b2tuPDQ/OD9paTpsaGhuPG44NG5pPCt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wPjorZWlhMD0=&url=https%3a%2f%2fwww.facebook.com%2fnetlife.ecuador"><img
                              src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwOmg7bzg9NWs7bm5pbmw/aD1sNDQ0NDRrNDk+bz4/ODw5NDg7aG81Pit5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2f68f99dae7571da4c28f9a63b3c7053b9%2fface.jpg&fmlBlkTk"
                              alt="internet seguro" width="110" height="30	"></img></a></th>
                        <td style="width: 200px"><a
                            href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwODlvPWk1a2tsOms6PThua2w9aT5sPT5vPT88b2hpbDg1Pmg8az8+byt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wPjorZWlhMD0=&url=https%3a%2f%2ftwitter.com%2fNetlifeEcuador"><img
                              src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPD1objg6b2hpbzg0Oz06bGhoPzk8ODU/OTQ/NTU1Ojg4bzhubD1rPSt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2f4b21d67e4dc5a2a472d96e0c2dcc4cad%2ftwitter.jpg&fmlBlkTk"
                              alt="internet seguro" width="110" height="30"></img></a></td>
                        <td style="width: 240px"><a
                            href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPD04PTs/b2g1ND1rOGw9bzw7PzloPTg7Pzw4bz08OGg8b2xuPD1oayt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wPzgrZWlhMD0=&url=https%3a%2f%2fwww.youtube.com%2fchannel%2fUCnc9ndF9fEDdyeVVJ7j1SDw"><img
                              src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPjo1O29paTk9NTg1bDk+PD1uODk8bzQ0bzg1azk6OzQ6azhpOztpNCt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2fe1f1a0638ea7001114b231e1738105ee%2fyoutube.jpg&fmlBlkTk"
                              alt="internet seguro" width="110" height="30"></img></a></td>
                        <td style="width: 200px"><img
                            src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwOWxsOjQ7PTg8OWw0P247a241O287NDg6PDk8OjVsbzg0OG88Oz5obit5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f262x120%2f64bb2db4cce8226e85e878e1c22b8bad%2ffooter-net2.jpg&fmlBlkTk"
                            alt="internet seguro" width="210" height="87"></img></td>
                      </tr>') || TO_CLOB('
                    </tbody>
                  </table>
                </td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
    </tbody>
  </table>') || TO_CLOB('
  <!--[if mso]>
                </td>
                </tr>
                </table>
                <![endif]-->
</body>

</html>'), 'Activo', SYSDATE, 'farias', NULL, NULL, NULL);



-- HBO-MAX
-- PLANTILLA CUANDO SE RESTABLECE LA CONTRASEÑA DEL SERVICIO
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
(ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES(DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL, 'HBO-MAX Restablecer contraseña del servicio', 'HBO-MAX-REST', 'TECNICO',
TO_CLOB('<html style="margin:0;padding:0" lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="format-detection" content="telephone=no">
  <title>Asunto</title>
  <style type="text/css">
    @media screen and (max-width: 480px) {
      .mailpoet_button {
        width: 100% !important;
      }
    }

    @media screen and (max-width: 599px) {
      .mailpoet_header {
        padding: 10px 20px;
      }') || TO_CLOB('

      .mailpoet_button {
        width: 100% !important;
        padding: 5px 0 !important;
        box-sizing: border-box !important;
      }

      div,
      .mailpoet_cols-two,
      .mailpoet_cols-three {
        max-width: 100% !important;
      }') || TO_CLOB('
    }
  </style>
  <!--[if !mso]><link href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwND40PDg+OG8/b2w8P25rOD08OzpsOmw4bjQ+NT04aDxoPjo0P2xrPit5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ffonts.googleapis.com%2fcss%3ffamily%3dLato%3a400%2c400i%2c700%2c700i|Oswald:400,400i,700,700i" rel="stylesheet"><![endif]-->
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }') || TO_CLOB('

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }') || TO_CLOB('

    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }') || TO_CLOB('

    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }

    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }') || TO_CLOB('

    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }

    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }

    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }

    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }

    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>') || TO_CLOB('
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }') || TO_CLOB('

    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }') || TO_CLOB('

    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }

    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }') || TO_CLOB('

    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }

    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }

    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }

    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }

    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }') || TO_CLOB('

    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }') || TO_CLOB('

    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }

    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }') || TO_CLOB('

    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }

    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }

    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }

    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }

    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }

    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }

    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>') || TO_CLOB('
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }') || TO_CLOB('

    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }') || TO_CLOB('

    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }

    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }') || TO_CLOB('

    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }

    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }

    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }

    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }

    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }

    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }

    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>') || TO_CLOB('
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }') || TO_CLOB('

    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }

    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }

    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }') || TO_CLOB('

    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }

    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }

    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }

    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }') || TO_CLOB('

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }') || TO_CLOB('

    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }

    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }') || TO_CLOB('

    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }

    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }') || TO_CLOB('

    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }

    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }

    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }

    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }

    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }

    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }

    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }') || TO_CLOB('

    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>
</head>

<body style="margin:0;padding:0;background-color:#d4d4d4" topmargin="0" marginwidth="0" marginheight="0" leftmargin="0">
  <table class="mailpoet_template"
    style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellspacing="0"
    cellpadding="0" border="0">
    <tbody>
      <tr>
        <td class="mailpoet_preheader"
          style="border-collapse:collapse;display:none;visibility:hidden;mso-hide:all;font-size:1px;color:#333333;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;-webkit-text-size-adjust:none"
          height="1">

        </td>
      </tr>') || TO_CLOB('
      <tr>
        <td class="mailpoet-wrapper" style="border-collapse:collapse;background-color:#d4d4d4" valign="top"
          align="center">
          <!--[if mso]>
                <table align="center" border="0" cellspacing="0" cellpadding="0"
                       width="660">
                    <tr>
                        <td class="mailpoet_content-wrapper" align="center" valign="top" width="660">
                <![endif]-->
          <table class="mailpoet_content-wrapper"
            style="border-collapse:collapse;background-color:#000000;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;max-width:660px;width:100%"
            width="660" cellspacing="0" cellpadding="0" border="0">
            <tbody>
              <tr>') || TO_CLOB('
                <td class="mailpoet_content" style="border-collapse:collapse;background-color:#ffffff!important"
                  bgcolor="#ffffff" align="center">
                  <table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                    width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td style="border-collapse:collapse;padding-left:0;padding-right:0">
                          <table class="mailpoet_cols-one"
                            style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0"
                            width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                              <tr>
                                <td class="mailpoet_image " style="border-collapse:collapse" valign="top"
                                  align="center">
                                  <img
                                    src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwOW86PT5uNT41aG5sOzw5O2xvOz47aTg8aWs5PjtrbGtoP281ODloPCt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f8db9c45f7b8669635636e6%2f799x163%2f5b839e3aebc1440edba9fdbf4dafd3e1%2ftoop-mail2.jpg&fmlBlkTk"
                                    alt="NETLIFE PLAY"
                                    style="height:auto;max-width:100%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"
                                    width="660"></img>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>') || TO_CLOB('
                    </tbody>
                  </table>
                </td>
              </tr>
              <tr>
                <td class="mailpoet_content-cols-three"
                  style="border-collapse:collapse;background-color:  !important;background-color: ;" bgcolor="#ffffff"
                  align="left">
                  <table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                    width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td style="border-collapse:collapse;font-size:0;background-color: #ff6700;" align="center">
                          <!--[if mso]>
                  <table border="0" width="100%" cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>') || TO_CLOB('
      <td width="220" valign="top">
        <![endif]-->
                          <div style="display:inline-block; max-width:54px; vertical-align:top; width:100%;" wfd-id="2">
                            <table class="mailpoet_cols-three"
                              style="border-collapse:collapse;width:100%;max-width:54px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0"
                              width="220" cellspacing="0" cellpadding="0" border="0" align="right">
                              <tbody></tbody>
                            </table>
                          </div>
                          <!--[if mso]>
      </td>
      <td width="220" valign="top">') || TO_CLOB('
        <![endif]-->
                          <div style="display:inline-block; max-width:552px; vertical-align:top; width:100%;"
                            wfd-id="1">
                            <table class="mailpoet_cols-three"
                              style="border-collapse:collapse;width:100%;max-width:552px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;background-color: ededed;"
                              width="220" cellspacing="0" cellpadding="0" border="0" align="right">
                              <tbody>
                                <tr>
                                  <td class="mailpoet_image " style="border-collapse:collapse" valign="top"
                                    align="center">
                                    <img
                                      src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwbj9rOD01aTxoPjlpPWk8Ojk7bzpuND9pPz1raWk4PT5pNW49aztoOit5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2fHeader-HBOMax.jpg&fmlBlkTk"
                                      alt="noggin"
                                      style="height:auto;max-width:70%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"
                                      width="220"></img>
                                  </td>
                                </tr>
                                <tr>') || TO_CLOB('
                                  <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side"
                                    style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word"
                                    valign="top">
                                    <table
                                      style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                                      width="100%" cellpadding="0">
                                      <tbody>
                                        <tr>
                                          <td class="mailpoet_paragraph"
                                            style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:center;">
                                            <strong>Hola {{ cliente }}</strong>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                                <tr>') || TO_CLOB('
                                  <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side"
                                    style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word"
                                    valign="top">
                                    <table
                                      style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                                      width="100%" cellpadding="0">
                                      <tbody>
                                        <tr>
                                          <td class="mailpoet_paragraph"
                                            style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:justify;">
                                            <strong>
                                              <o:p style="font-size:20px;strong;text-align:center">&iquest;OLVIDASTE TU
                                                CONTRASE&Ntilde;A?</o:p>
                                            </strong>

                                            <p style="text-align:center">No hay problema. Para iniciar sesi&oacute;n,
                                              por favor da click en el bot&oacute;n a continuaci&oacute;n y sigue las
                                              instrucciones:</p>
                                            <o:p style="text-align:center;line-height:1px">&nbsp;</o:p>
                                          </td>
                                        </tr>
                                        <tr>') || TO_CLOB('
                                          <td class="mailpoet_image " style="border-collapse:collapse" valign="top"
                                            align="center">
                                            <a
                                              href="{{url}}"><img
                                                src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwaTlpPTVsOms8Pm86Oj01aDw6PTU4OGg/bztvaDQ1PTQ7PW89az88OSt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2fBoton-Rest.png&fmlBlkTk"
                                                alt="CLICK AQUI"
                                                style="height:auto;max-width:60%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"
                                                width="220"></img></a>
                                          </td>
                                        </tr>
                                        <tr>') || TO_CLOB('
                                          <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side"
                                            style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word"
                                            valign="top">
                                            <table
                                              style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                                              width="100%" cellpadding="0">
                                              <tbody>
                                                <tr>') || TO_CLOB('
                                                  <td class="mailpoet_paragraph"
                                                    style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:25px;word-break:break-word;word-wrap:break-word;text-align:center">
                                                    <o:p style="font-size: 17px">&nbsp;</o:p>
                                                    <p style="font-size: 17px">Si no tienes una cuenta en HBO Max o este
                                                      no es el correo asociado a tu cuenta, por favor</p>
                                                    <h1
                                                      style="margin:0 0 9px;font-family:Oswald,&#39;Trebuchet MS&#39;,&#39;Lucida Grande&#39;,&#39;Lucida Sans Unicode&#39;,&#39;Lucida Sans&#39;,Tahoma,sans-serif;font-size:25px;line-height:10px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal">
                                                      <strong></strong><strong
                                                        style="color: #eb702e;">CONT&Aacute;CTANOS AL 39
                                                        20000<br></strong></h1>
                                                    <o:p>&nbsp;</o:p>
                                                    <strong>
                                                      <o:p style="font-size:18px;text-align:center">&iquest;Necesitas
                                                        informaci&oacute;n adicional?</o:p>
                                                    </strong>
                                                    <p style="font-size: 14.2px">Encu&eacute;ntrala f&aacute;cilmente a
                                                      trav&eacute;s de nuestros canales digitales</p>
                                                  </td>

                                                </tr>') || TO_CLOB('
                                              </tbody>
                                            </table>
                                          </td>
                                        </tr>
                                        <tr>
                                          <td class="mailpoet_image " style="border-collapse:collapse" valign="top"
                                            align="center">
                                            <img
                                              src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwO240az01aD9pOjg9bD1uPjxra2hrP2lpPmw/OzQ6Pmw+Pj1obDQ5Pit5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2fCierre-ARA.jpg&fmlBlkTk"
                                              alt="noggin"
                                              style="height:auto;max-width:100%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"
                                              width="220"></img>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>') || TO_CLOB('
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                          <!--[if mso]>
      </td>
      <td width="220" valign="top">
        <![endif]-->
                          <div style="display:inline-block; max-width:54px; vertical-align:top; width:100%;" wfd-id="0">
                            <table class="mailpoet_cols-three"
                              style="border-collapse:collapse;width:100%;max-width:54px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0"
                              width="220" cellspacing="0" cellpadding="0" border="0" align="right">
                              <tbody></tbody>
                            </table>
                          </div>
                          <!--[if mso]>
      </td>
                  </tr>') || TO_CLOB('
                </tbody>
              </table>
            <![endif]-->
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <table style="background-color: #000000;" width="660" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>') || TO_CLOB('
                        <th style="width: 220px" scope="row"><a
                            href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwNG48a2k7aWlvbD41PD0/bGg0b2tuPDQ/OD9paTpsaGhuPG44NG5pPCt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wPjorZWlhMD0=&url=https%3a%2f%2fwww.facebook.com%2fnetlife.ecuador"><img
                              src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwOmg7bzg9NWs7bm5pbmw/aD1sNDQ0NDRrNDk+bz4/ODw5NDg7aG81Pit5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2f68f99dae7571da4c28f9a63b3c7053b9%2fface.jpg&fmlBlkTk"
                              alt="internet seguro" width="110" height="30	"></img></a></th>
                        <td style="width: 200px"><a
                            href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwODlvPWk1a2tsOms6PThua2w9aT5sPT5vPT88b2hpbDg1Pmg8az8+byt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wPjorZWlhMD0=&url=https%3a%2f%2ftwitter.com%2fNetlifeEcuador"><img
                              src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPD1objg6b2hpbzg0Oz06bGhoPzk8ODU/OTQ/NTU1Ojg4bzhubD1rPSt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2f4b21d67e4dc5a2a472d96e0c2dcc4cad%2ftwitter.jpg&fmlBlkTk"
                              alt="internet seguro" width="110" height="30"></img></a></td>
                        <td style="width: 240px"><a
                            href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPD04PTs/b2g1ND1rOGw9bzw7PzloPTg7Pzw4bz08OGg8b2xuPD1oayt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wPzgrZWlhMD0=&url=https%3a%2f%2fwww.youtube.com%2fchannel%2fUCnc9ndF9fEDdyeVVJ7j1SDw"><img
                              src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPjo1O29paTk9NTg1bDk+PD1uODk8bzQ0bzg1azk6OzQ6azhpOztpNCt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2fe1f1a0638ea7001114b231e1738105ee%2fyoutube.jpg&fmlBlkTk"
                              alt="internet seguro" width="110" height="30"></img></a></td>
                        <td style="width: 200px"><img
                            src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwOWxsOjQ7PTg8OWw0P247a241O287NDg6PDk8OjVsbzg0OG88Oz5obit5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f262x120%2f64bb2db4cce8226e85e878e1c22b8bad%2ffooter-net2.jpg&fmlBlkTk"
                            alt="internet seguro" width="210" height="87"></img></td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>') || TO_CLOB('
            </tbody>
          </table>
        </td>
      </tr>
    </tbody>
  </table>
  <!--[if mso]>
                </td>
                </tr>
                </table>
                <![endif]-->
</body>

</html>'), 'Activo', SYSDATE, 'farias', NULL, NULL, NULL);



-- HBO-MAX
-- PLANTILLA CUANDO SE CREA LA CONTRASEÑA DEL SERVICIO POR PRIMERA VES
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
(ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES(DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL, 'HBO-MAX Confirmación de creación de contraseña', 'HBO-MAX-CF-ACT', 'TECNICO',
TO_CLOB('<html style="margin:0;padding:0" lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="format-detection" content="telephone=no">
  <title>Asunto</title>
  <style type="text/css">
    @media screen and (max-width: 480px) {
      .mailpoet_button {
        width: 100% !important;
      }
    }') || TO_CLOB('

    @media screen and (max-width: 599px) {
      .mailpoet_header {
        padding: 10px 20px;
      }

      .mailpoet_button {
        width: 100% !important;
        padding: 5px 0 !important;
        box-sizing: border-box !important;
      }

      div,
      .mailpoet_cols-two,
      .mailpoet_cols-three {
        max-width: 100% !important;
      }
    }') || TO_CLOB('
  </style>
  <!--[if !mso]><link href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwND40PDg+OG8/b2w8P25rOD08OzpsOmw4bjQ+NT04aDxoPjo0P2xrPit5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ffonts.googleapis.com%2fcss%3ffamily%3dLato%3a400%2c400i%2c700%2c700i|Oswald:400,400i,700,700i" rel="stylesheet"><![endif]-->
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }

    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }') || TO_CLOB('

    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }
    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }') || TO_CLOB('
    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }
    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }
    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }
    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }
    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }') || TO_CLOB('
    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }
    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }
    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }
    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }
    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>') || TO_CLOB('
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }') || TO_CLOB('
    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }
    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }
    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }') || TO_CLOB('
    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }
    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }
    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }
    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }
    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }
    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>') || TO_CLOB('
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }') || TO_CLOB('
    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }
    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }') || TO_CLOB('
    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }
    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }
    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }
    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }
    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>') || TO_CLOB('
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }') || TO_CLOB('
    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }
    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }') || TO_CLOB('
    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }
    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }
    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }
    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }
    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>') || TO_CLOB('
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }') || TO_CLOB('
    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }
    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }') || TO_CLOB('

    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }
    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }
    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }
    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }
    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }
    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>') || TO_CLOB('
</head>
<body style="margin:0;padding:0;background-color:#d4d4d4" topmargin="0" marginwidth="0" marginheight="0" leftmargin="0">
  <table class="mailpoet_template"
    style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellspacing="0"
    cellpadding="0" border="0">
    <tbody>
      <tr>
        <td class="mailpoet_preheader"
          style="border-collapse:collapse;display:none;visibility:hidden;mso-hide:all;font-size:1px;color:#333333;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;-webkit-text-size-adjust:none"
          height="1">

        </td>
      </tr>') || TO_CLOB('
      <tr>
        <td class="mailpoet-wrapper" style="border-collapse:collapse;background-color:#d4d4d4" valign="top"
          align="center">
          <!--[if mso]>
                <table align="center" border="0" cellspacing="0" cellpadding="0"
                       width="660">
                    <tr>
                        <td class="mailpoet_content-wrapper" align="center" valign="top" width="660">
                <![endif]-->
          <table class="mailpoet_content-wrapper"
            style="border-collapse:collapse;background-color:#000000;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;max-width:660px;width:100%"
            width="660" cellspacing="0" cellpadding="0" border="0">
            <tbody>
              <tr>') || TO_CLOB('
                <td class="mailpoet_content" style="border-collapse:collapse;background-color:#ffffff!important"
                  bgcolor="#ffffff" align="center">
                  <table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                    width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td style="border-collapse:collapse;padding-left:0;padding-right:0">
                          <table class="mailpoet_cols-one"
                            style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0"
                            width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                              <tr>') || TO_CLOB('
                                <td class="mailpoet_image " style="border-collapse:collapse" valign="top"
                                  align="center">
                                  <img
                                    src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwOW86PT5uNT41aG5sOzw5O2xvOz47aTg8aWs5PjtrbGtoP281ODloPCt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f8db9c45f7b8669635636e6%2f799x163%2f5b839e3aebc1440edba9fdbf4dafd3e1%2ftoop-mail2.jpg&fmlBlkTk"
                                    alt="NETLIFE PLAY"
                                    style="height:auto;max-width:100%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"
                                    width="660"></img>
                                </td>
                              </tr>
                            </tbody>') || TO_CLOB('
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>') || TO_CLOB('
              <tr>
                <td class="mailpoet_content-cols-three"
                  style="border-collapse:collapse;background-color:  !important;background-color: ;" bgcolor="#ffffff"
                  align="left">
                  <table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                    width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td style="border-collapse:collapse;font-size:0;background-color: #ff6700;" align="center">
                          <!--[if mso]>
                  <table border="0" width="100%" cellpadding="0" cellspacing="0">
                    <tbody>') || TO_CLOB('
                      <tr>
      <td width="220" valign="top">
        <![endif]-->
                          <div style="display:inline-block; max-width:54px; vertical-align:top; width:100%;" wfd-id="2">
                            <table class="mailpoet_cols-three"
                              style="border-collapse:collapse;width:100%;max-width:54px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0"
                              width="220" cellspacing="0" cellpadding="0" border="0" align="right">
                              <tbody></tbody>
                            </table>
                          </div>
                          <!--[if mso]>
      </td>
      <td width="220" valign="top">') || TO_CLOB('
        <![endif]-->
                          <div style="display:inline-block; max-width:552px; vertical-align:top; width:100%;"
                            wfd-id="1">
                            <table class="mailpoet_cols-three"
                              style="border-collapse:collapse;width:100%;max-width:552px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;background-color: ededed;"
                              width="220" cellspacing="0" cellpadding="0" border="0" align="right">
                              <tbody>
                                <tr>
                                  <td class="mailpoet_image " style="border-collapse:collapse" valign="top"
                                    align="center">
                                    <img
                                      src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwbj9rOD01aTxoPjlpPWk8Ojk7bzpuND9pPz1raWk4PT5pNW49aztoOit5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2fHeader-HBOMax.jpg&fmlBlkTk"
                                      alt="noggin"
                                      style="height:auto;max-width:70%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"
                                      width="220"></img>
                                  </td>
                                </tr>') || TO_CLOB('
                                <tr>
                                  <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side"
                                    style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word"
                                    valign="top">
                                    <table
                                      style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                                      width="100%" cellpadding="0">
                                      <tbody>
                                        <tr>
                                          <td class="mailpoet_paragraph"
                                            style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:center;">
                                            <strong>Hola {{ cliente }}</strong>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>') || TO_CLOB('
                                  </td>
                                </tr>
                                <tr>
                                  <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side"
                                    style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word"
                                    valign="top">
                                    <table
                                      style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                                      width="100%" cellpadding="0">
                                      <tbody>
                                        <tr>') || TO_CLOB('
                                          <td class="mailpoet_paragraph"
                                            style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:justify;">
                                            <strong>
                                              <p style="font-size:20px;strong;text-align:center">GRACIAS POR ACTUALIZAR
                                                TUS DATOS EN HBO Max</p>
                                            </strong>

                                            <p style="font-size:15px; text-align:center"> Disfruta de tus
                                              pel&iacute;culas, series favoritas, contenido para ni&ntilde;os y mucho
                                              m&aacute;s.</p>
                                            <o:p>&nbsp;</o:p>
                                            <strong>
                                              <p style="font-size:20px;text-align:center">Si no solicitaste este cambio
                                              </p>
                                            </strong>
                                          </td>
                                        </tr>') || TO_CLOB('
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side"
                                    style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word"
                                    valign="top">
                                    <h1
                                      style="margin:0 0 9px;font-family:Oswald,&#39;Trebuchet MS&#39;,&#39;Lucida Grande&#39;,&#39;Lucida Sans Unicode&#39;,&#39;Lucida Sans&#39;,Tahoma,sans-serif;font-size:25px;line-height:10px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal">
                                      <strong></strong><strong style="color: #eb702e;">CONT&Aacute;CTANOS AL 39
                                        20000<br></strong></h1>
                                    <o:p>&nbsp;</o:p>
                                  </td>
                                </tr>') || TO_CLOB('
                                <tr>
                                  <td class="mailpoet_image " style="border-collapse:collapse" valign="top"
                                    align="center">
                                    <a
                                      href="{{url}}"><img
                                        src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPTw5Pm8+OjU/bmxsaD88b2w9bzRvODg+b2tsbDtrbDg5Omw7Pjo1bCt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2fBoton-Descubre-Hbo.png&fmlBlkTk"
                                        alt="CLICK AQUI"
                                        style="height:auto;max-width:40%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"
                                        width="220"></img></a>
                                  </td>
                                </tr>
                                <tr>') || TO_CLOB('
                                  <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side"
                                    style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word"
                                    valign="top">
                                    <table
                                      style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                                      width="100%" cellpadding="0">
                                      <tbody>
                                        <tr>') || TO_CLOB('
                                          <td class="mailpoet_paragraph"
                                            style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:center">
                                            <o:p style="font-size: 14.2px">&nbsp;</o:p>
                                            <strong>
                                              <o:p style="font-size:18px;text-align:center">&iquest;Necesitas
                                                informaci&oacute;n adicional?</o:p>
                                            </strong>
                                            <p style="font-size: 14.2px">Encu&eacute;ntrala f&aacute;cilmente a
                                              trav&eacute;s de nuestros canales digitales</p>

                                          </td>
                                        </tr>
                                      </tbody>') || TO_CLOB('
                                    </table>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="mailpoet_image " style="border-collapse:collapse" valign="top"
                                    align="center">
                                    <img
                                      src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwO240az01aD9pOjg9bD1uPjxra2hrP2lpPmw/OzQ6Pmw+Pj1obDQ5Pit5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2fCierre-ARA.jpg&fmlBlkTk"
                                      alt="noggin"
                                      style="height:auto;max-width:100%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"
                                      width="220"></img>
                                  </td>
                                </tr>') || TO_CLOB('
                              </tbody>
                            </table>
                          </div>
                          <!--[if mso]>
      </td>
      <td width="220" valign="top">') || TO_CLOB('
        <![endif]-->
                          <div style="display:inline-block; max-width:54px; vertical-align:top; width:100%;" wfd-id="0">
                            <table class="mailpoet_cols-three"
                              style="border-collapse:collapse;width:100%;max-width:54px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0"
                              width="220" cellspacing="0" cellpadding="0" border="0" align="right">
                              <tbody></tbody>
                            </table>
                          </div>
                          <!--[if mso]>
      </td>
                  </tr>
                </tbody>
              </table>
            <![endif]-->
                        </td>
                      </tr>') || TO_CLOB('
                    </tbody>
                  </table>
                  <table style="background-color: #000000;" width="660" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>') || TO_CLOB('
                        <th style="width: 220px" scope="row"><a
                            href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwNG48a2k7aWlvbD41PD0/bGg0b2tuPDQ/OD9paTpsaGhuPG44NG5pPCt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wPjorZWlhMD0=&url=https%3a%2f%2fwww.facebook.com%2fnetlife.ecuador"><img
                              src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwOmg7bzg9NWs7bm5pbmw/aD1sNDQ0NDRrNDk+bz4/ODw5NDg7aG81Pit5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2f68f99dae7571da4c28f9a63b3c7053b9%2fface.jpg&fmlBlkTk"
                              alt="internet seguro" width="110" height="30	"></img></a></th>
                        <td style="width: 200px"><a
                            href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwODlvPWk1a2tsOms6PThua2w9aT5sPT5vPT88b2hpbDg1Pmg8az8+byt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wPjorZWlhMD0=&url=https%3a%2f%2ftwitter.com%2fNetlifeEcuador"><img
                              src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPD1objg6b2hpbzg0Oz06bGhoPzk8ODU/OTQ/NTU1Ojg4bzhubD1rPSt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2f4b21d67e4dc5a2a472d96e0c2dcc4cad%2ftwitter.jpg&fmlBlkTk"
                              alt="internet seguro" width="110" height="30"></img></a></td>
                        <td style="width: 240px"><a
                            href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPD04PTs/b2g1ND1rOGw9bzw7PzloPTg7Pzw4bz08OGg8b2xuPD1oayt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wPzgrZWlhMD0=&url=https%3a%2f%2fwww.youtube.com%2fchannel%2fUCnc9ndF9fEDdyeVVJ7j1SDw"><img
                              src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPjo1O29paTk9NTg1bDk+PD1uODk8bzQ0bzg1azk6OzQ6azhpOztpNCt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2fe1f1a0638ea7001114b231e1738105ee%2fyoutube.jpg&fmlBlkTk"
                              alt="internet seguro" width="110" height="30"></img></a></td>
                        <td style="width: 200px"><img
                            src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwOWxsOjQ7PTg8OWw0P247a241O287NDg6PDk8OjVsbzg0OG88Oz5obit5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f262x120%2f64bb2db4cce8226e85e878e1c22b8bad%2ffooter-net2.jpg&fmlBlkTk"
                            alt="internet seguro" width="210" height="87"></img></td>
                      </tr>
                    </tbody>') || TO_CLOB('
                  </table>
                </td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
    </tbody>
  </table>') || TO_CLOB('
  <!--[if mso]>
                </td>
                </tr>
                </table>
                <![endif]-->
</body>
</html>'), 'Activo', SYSDATE, 'farias', NULL, NULL, NULL);





-- HBO-MAX
-- PLANTILLA DE CONFIRMACIÓN CUANDO SE CAMBIA LA CONTRASEÑA DEL SERVICIO
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
(ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES(DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL, 'HBO-MAX Confirmación cambio de contraseña', 'HBO-MAX-CF-REST', 'TECNICO',
TO_CLOB('<html style="margin:0;padding:0" lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="format-detection" content="telephone=no">
  <title>Asunto</title>
  <style type="text/css">
    @media screen and (max-width: 480px) {
      .mailpoet_button {
        width: 100% !important;
      }
    }') || TO_CLOB('
    @media screen and (max-width: 599px) {
      .mailpoet_header {
        padding: 10px 20px;
      }
      .mailpoet_button {
        width: 100% !important;
        padding: 5px 0 !important;
        box-sizing: border-box !important;
      }
      div,
      .mailpoet_cols-two,
      .mailpoet_cols-three {
        max-width: 100% !important;
      }
    }
  </style>') || TO_CLOB('
  <!--[if !mso]><link href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwND40PDg+OG8/b2w8P25rOD08OzpsOmw4bjQ+NT04aDxoPjo0P2xrPit5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ffonts.googleapis.com%2fcss%3ffamily%3dLato%3a400%2c400i%2c700%2c700i|Oswald:400,400i,700,700i" rel="stylesheet"><![endif]-->
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }') || TO_CLOB('
    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }
    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }') || TO_CLOB('

    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }
    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }
    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }
    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }
    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>') || TO_CLOB('
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }') || TO_CLOB('
    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }
    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }') || TO_CLOB('
    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }
    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }
    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }
    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }
    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }
    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>') || TO_CLOB('
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }') || TO_CLOB('
    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }
    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }
    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }') || TO_CLOB('
    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }
    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }
    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }
    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }
    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }
    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }') || TO_CLOB('
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }') || TO_CLOB('
    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }
    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }') || TO_CLOB('
    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }
    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }
    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }
    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }
    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>') || TO_CLOB('
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }') || TO_CLOB('
    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }
    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }') || TO_CLOB('
    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }
    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }
    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }
    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }
    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>') || TO_CLOB('
  <style>
    ._3emE9--dark-theme .-S-tR--ff-downloader {
      background: rgba(30, 30, 30, .93);
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #fff
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      background: #3d4b52
    }') || TO_CLOB('
    ._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #131415
    }
    ._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: rgba(30, 30, 30, .93)
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader {
      background: #fff;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      color: #314c75
    }') || TO_CLOB('
    ._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header {
      font-weight: 700
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      border: 0;
      color: rgba(0, 0, 0, .88)
    }
    ._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer {
      background: #fff
    }') || TO_CLOB('
    .-S-tR--ff-downloader {
      display: block;
      overflow: hidden;
      position: fixed;
      bottom: 20px;
      right: 7.1%;
      width: 330px;
      height: 180px;
      background: rgba(30, 30, 30, .93);
      border-radius: 2px;
      color: #fff;
      z-index: 99999999;
      border: 1px solid rgba(82, 82, 82, .54);
      box-shadow: 0 4px 7px rgba(30, 30, 30, .55);
      transition: .5s
    }
    .-S-tR--ff-downloader._3M7UQ--minimize {
      height: 62px
    }
    .-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,
    .-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header {
      display: none
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._6_Mtt--header {
      padding: 10px;
      font-size: 17px;
      font-family: sans-serif
    }
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn {
      float: right;
      background: #f1ecec;
      height: 20px;
      width: 20px;
      text-align: center;
      padding: 2px;
      margin-top: -10px;
      cursor: pointer
    }
    .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover {
      background: #e2dede
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._13XQ2--error {
      color: red;
      padding: 10px;
      font-size: 12px;
      line-height: 19px
    }
    .-S-tR--ff-downloader ._2dFLA--container {
      position: relative;
      height: 100%
    }
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info {
      padding: 6px 15px 0;
      font-family: sans-serif
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div {
      margin-bottom: 5px;
      width: 100%;
      overflow: hidden
    }
    .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice {
      margin-top: 21px;
      font-size: 11px
    }
    .-S-tR--ff-downloader ._10vpG--footer {
      width: 100%;
      bottom: 0;
      position: absolute;
      font-weight: 700
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader {
      animation: n0BD1--rotation 3.5s linear forwards;
      position: absolute;
      top: -120px;
      left: calc(50% - 35px);
      border-radius: 50%;
      border: 5px solid #fff;
      border-top-color: #a29bfe;
      height: 70px;
      width: 70px;
      display: flex;
      justify-content: center;
      align-items: center
    }
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar {
      width: 100%;
      height: 18px;
      background: #dfe6e9;
      border-radius: 5px
    }') || TO_CLOB('
    .-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar {
      height: 100%;
      background: #8bc34a;
      border-radius: 5px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status {
      margin-top: 10px
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state {
      float: left;
      font-size: .9em;
      letter-spacing: 1pt;
      text-transform: uppercase;
      width: 100px;
      height: 20px;
      position: relative
    }
    .-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage {
      float: right
    }
  </style>') || TO_CLOB('
</head>
<body style="margin:0;padding:0;background-color:#d4d4d4" topmargin="0" marginwidth="0" marginheight="0" leftmargin="0">
  <table class="mailpoet_template"
    style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellspacing="0"
    cellpadding="0" border="0">
    <tbody>
      <tr>
        <td class="mailpoet_preheader"
          style="border-collapse:collapse;display:none;visibility:hidden;mso-hide:all;font-size:1px;color:#333333;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;-webkit-text-size-adjust:none"
          height="1">

        </td>
      </tr>') || TO_CLOB('
      <tr>
        <td class="mailpoet-wrapper" style="border-collapse:collapse;background-color:#d4d4d4" valign="top"
          align="center">
          <!--[if mso]>
                <table align="center" border="0" cellspacing="0" cellpadding="0"
                       width="660">
                    <tr>
                        <td class="mailpoet_content-wrapper" align="center" valign="top" width="660">
                <![endif]-->
          <table class="mailpoet_content-wrapper"
            style="border-collapse:collapse;background-color:#000000;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;max-width:660px;width:100%"
            width="660" cellspacing="0" cellpadding="0" border="0">
            <tbody>') || TO_CLOB('
              <tr>
                <td class="mailpoet_content" style="border-collapse:collapse;background-color:#ffffff!important"
                  bgcolor="#ffffff" align="center">
                  <table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                    width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td style="border-collapse:collapse;padding-left:0;padding-right:0">
                          <table class="mailpoet_cols-one"
                            style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0"
                            width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                              <tr>') || TO_CLOB('
                                <td class="mailpoet_image " style="border-collapse:collapse" valign="top"
                                  align="center">
                                  <img
                                    src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwOW86PT5uNT41aG5sOzw5O2xvOz47aTg8aWs5PjtrbGtoP281ODloPCt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f8db9c45f7b8669635636e6%2f799x163%2f5b839e3aebc1440edba9fdbf4dafd3e1%2ftoop-mail2.jpg&fmlBlkTk"
                                    alt="NETLIFE PLAY"
                                    style="height:auto;max-width:100%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"
                                    width="660"></img>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>') || TO_CLOB('
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
              <tr>') || TO_CLOB('
                <td class="mailpoet_content-cols-three"
                  style="border-collapse:collapse;background-color:  !important;background-color: ;" bgcolor="#ffffff"
                  align="left">
                  <table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                    width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td style="border-collapse:collapse;font-size:0;background-color: #ff6700;" align="center">
                          <!--[if mso]>
                  <table border="0" width="100%" cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>
      <td width="220" valign="top">') || TO_CLOB('
        <![endif]-->
                          <div style="display:inline-block; max-width:54px; vertical-align:top; width:100%;" wfd-id="2">
                            <table class="mailpoet_cols-three"
                              style="border-collapse:collapse;width:100%;max-width:54px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0"
                              width="220" cellspacing="0" cellpadding="0" border="0" align="right">
                              <tbody></tbody>
                            </table>
                          </div>
                          <!--[if mso]>
      </td>
      <td width="220" valign="top">') || TO_CLOB('
        <![endif]-->
                          <div style="display:inline-block; max-width:552px; vertical-align:top; width:100%;"
                            wfd-id="1">
                            <table class="mailpoet_cols-three"
                              style="border-collapse:collapse;width:100%;max-width:552px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;background-color: ededed;"
                              width="220" cellspacing="0" cellpadding="0" border="0" align="right">
                              <tbody>
                                <tr>
                                  <td class="mailpoet_image " style="border-collapse:collapse" valign="top"
                                    align="center">') || TO_CLOB('
                                    <img
                                      src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwbj9rOD01aTxoPjlpPWk8Ojk7bzpuND9pPz1raWk4PT5pNW49aztoOit5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2fHeader-HBOMax.jpg&fmlBlkTk"
                                      alt="noggin"
                                      style="height:auto;max-width:70%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"
                                      width="220"></img>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side"
                                    style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word"
                                    valign="top">
                                    <table
                                      style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                                      width="100%" cellpadding="0">
                                      <tbody>
                                        <tr>') || TO_CLOB('
                                          <td class="mailpoet_paragraph"
                                            style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:center;">
                                            <strong>Hola {{ cliente }}</strong>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                                <tr>') || TO_CLOB('
                                  <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side"
                                    style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word"
                                    valign="top">
                                    <table
                                      style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                                      width="100%" cellpadding="0">
                                      <tbody>
                                        <tr>') || TO_CLOB('
                                          <td class="mailpoet_paragraph"
                                            style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:justify;">
                                            <strong>
                                              <p style="font-size:20px;strong;text-align:center">TU CONTRASE&Ntilde;A HA
                                                SIDO ACTUALIZADA EXITOSAMENTE</p>
                                            </strong>

                                            <p style="font-size:15px; text-align:center"> Disfruta de tus
                                              pel&iacute;culas, series favoritas, contenido para ni&ntilde;os y mucho
                                              m&aacute;s.</p>
                                            <strong>
                                              <p style="font-size:20px;text-align:center">Si no solicitaste este cambio
                                              </p>
                                            </strong>') || TO_CLOB('
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side"
                                    style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word"
                                    valign="top">
                                    <h1
                                      style="margin:0 0 9px;font-family:Oswald,&#39;Trebuchet MS&#39;,&#39;Lucida Grande&#39;,&#39;Lucida Sans Unicode&#39;,&#39;Lucida Sans&#39;,Tahoma,sans-serif;font-size:25px;line-height:10px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal">
                                      <strong></strong><strong style="color: #eb702e;">CONT&Aacute;CTANOS AL 39
                                        20000<br></strong></h1>
                                    <o:p>&nbsp;</o:p>
                                  </td>
                                </tr>') || TO_CLOB('
                                <tr>
                                  <td class="mailpoet_image " style="border-collapse:collapse" valign="top"
                                    align="center">
                                    <a
                                      href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwa2lpPjVoa2k6NTRsNTlraz9oPj4+aDRsODVpNTRpOzg5PDQ9NDhpbyt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2fwww.netlife.ec%2f"><img
                                        src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPTw5Pm8+OjU/bmxsaD88b2w9bzRvODg+b2tsbDtrbDg5Omw7Pjo1bCt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2fBoton-Descubre-Hbo.png&fmlBlkTk"
                                        alt="CLICK AQUI"
                                        style="height:auto;max-width:40%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"
                                        width="220"></img></a>
                                  </td>
                                </tr>
                                <tr>') || TO_CLOB('
                                  <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side"
                                    style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word"
                                    valign="top">
                                    <table
                                      style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                                      width="100%" cellpadding="0">
                                      <tbody>
                                        <tr>
                                          <td class="mailpoet_paragraph"
                                            style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:center">
                                            <o:p style="font-size: 14.2px">&nbsp;</o:p>
                                            <strong>
                                              <o:p style="font-size:18px;text-align:center">&iquest;Necesitas
                                                informaci&oacute;n adicional?</o:p>
                                            </strong>
                                            <p style="font-size: 14.2px">Encu&eacute;ntrala f&aacute;cilmente a
                                              trav&eacute;s de nuestros canales digitales</p>

                                          </td>
                                        </tr>') || TO_CLOB('
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="mailpoet_image " style="border-collapse:collapse" valign="top"
                                    align="center">
                                    <img
                                      src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwO240az01aD9pOjg9bD1uPjxra2hrP2lpPmw/OzQ6Pmw+Pj1obDQ5Pit5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2fcdnnetlife.konibit.com.mx%2fPROD_ENV%2fimagenes%2fmailing%2fCierre-ARA.jpg&fmlBlkTk"
                                      alt="noggin"
                                      style="height:auto;max-width:100%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"
                                      width="220"></img>
                                  </td>
                                </tr>
                              </tbody>') || TO_CLOB('
                            </table>
                          </div>
                          <!--[if mso]>
      </td>
      <td width="220" valign="top">
        <![endif]-->
                          <div style="display:inline-block; max-width:54px; vertical-align:top; width:100%;" wfd-id="0">
                            <table class="mailpoet_cols-three"
                              style="border-collapse:collapse;width:100%;max-width:54px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0"
                              width="220" cellspacing="0" cellpadding="0" border="0" align="right">
                              <tbody></tbody>
                            </table>
                          </div>
                          <!--[if mso]>
      </td>
                  </tr>') || TO_CLOB('
                </tbody>
              </table>
            <![endif]-->
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <table style="background-color: #000000;" width="660" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>') || TO_CLOB('
                        <th style="width: 220px" scope="row"><a
                            href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwNG48a2k7aWlvbD41PD0/bGg0b2tuPDQ/OD9paTpsaGhuPG44NG5pPCt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wPjorZWlhMD0=&url=https%3a%2f%2fwww.facebook.com%2fnetlife.ecuador"><img
                              src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwOmg7bzg9NWs7bm5pbmw/aD1sNDQ0NDRrNDk+bz4/ODw5NDg7aG81Pit5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2f68f99dae7571da4c28f9a63b3c7053b9%2fface.jpg&fmlBlkTk"
                              alt="internet seguro" width="110" height="30	"></img></a></th>
                        <td style="width: 200px"><a
                            href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwODlvPWk1a2tsOms6PThua2w9aT5sPT5vPT88b2hpbDg1Pmg8az8+byt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wPjorZWlhMD0=&url=https%3a%2f%2ftwitter.com%2fNetlifeEcuador"><img
                              src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPD1objg6b2hpbzg0Oz06bGhoPzk8ODU/OTQ/NTU1Ojg4bzhubD1rPSt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2f4b21d67e4dc5a2a472d96e0c2dcc4cad%2ftwitter.jpg&fmlBlkTk"
                              alt="internet seguro" width="110" height="30"></img></a></td>
                        <td style="width: 240px"><a
                            href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPD04PTs/b2g1ND1rOGw9bzw7PzloPTg7Pzw4bz08OGg8b2xuPD1oayt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wPzgrZWlhMD0=&url=https%3a%2f%2fwww.youtube.com%2fchannel%2fUCnc9ndF9fEDdyeVVJ7j1SDw"><img
                              src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwPjo1O29paTk9NTg1bDk+PD1uODk8bzQ0bzg1azk6OzQ6azhpOztpNCt5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2fe1f1a0638ea7001114b231e1738105ee%2fyoutube.jpg&fmlBlkTk"
                              alt="internet seguro" width="110" height="30"></img></a></td>
                        <td style="width: 200px"><img
                            src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVE3PD49Nyt7MD8jPStkaTA9PDc9PCt+ZGpjbHl4f2gwOWxsOjQ7PTg8OWw0P247a241O287NDg6PDk8OjVsbzg0OG88Oz5obit5MDw7ODU4Pz80OzUrfGRpMD87QEZjXk5CPTw6Oz86ID87QEZjXk5cPTw6Oz86K39ufXkwK24wOD8rZWlhMD0=&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f262x120%2f64bb2db4cce8226e85e878e1c22b8bad%2ffooter-net2.jpg&fmlBlkTk"
                            alt="internet seguro" width="210" height="87"></img></td>
                      </tr>
                    </tbody>
                  </table>') || TO_CLOB('
                </td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
    </tbody>
  </table>
  <!--[if mso]>
                </td>
                </tr>
                </table>
                <![endif]-->
</body>

</html>'), 'Activo', SYSDATE, 'farias', NULL, NULL, NULL);



COMMIT;
/


