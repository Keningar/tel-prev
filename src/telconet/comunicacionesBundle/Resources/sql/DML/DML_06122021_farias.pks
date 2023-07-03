/**
 *
 * Se crean plantillas de reenvio de credenciales para el producto ECDF
 *	 
 * @author Alberto Arias <farias@telconet.ec>
 * @version 1.0 07-12-2021
 */


SET DEFINE OFF
--PLANTILLA REENVIO CREDENCIALES
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
(ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES(DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL, 'El canal del futbol reenvio de credenciales', 'ECDF-REENV', 'TECNICO',
TO_CLOB('<html style="margin:0;padding:0" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
    </style>
    <!--[if !mso]><link href="https://fonts.googleapis.com/css?family=Lato:400,400i,700,700i|Oswald:400,400i,700,700i" rel="stylesheet"><![endif]-->
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
    </style>
</head>

<body style="margin:0;padding:0;background-color:#d4d4d4" topmargin="0" marginwidth="0" marginheight="0" leftmargin="0">
    <table class="mailpoet_template"
        style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%"
        cellspacing="0" cellpadding="0" border="0">
        <tbody>
            <tr>
                <td class="mailpoet_preheader"
                    style="border-collapse:collapse;display:none;visibility:hidden;mso-hide:all;font-size:1px;color:#333333;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;-webkit-text-size-adjust:none"
                    height="1">

                </td>
            </tr>
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

                            <tr>
                                <td class="mailpoet_content"
                                    style="border-collapse:collapse;background-color:#ffffff!important"
                                    bgcolor="#ffffff" align="center">
                                    <table
                                        style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                                        width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <tbody>
                                            <tr>
                                                <td style="border-collapse:collapse;padding-left:0;padding-right:0">
                                                    <table class="mailpoet_cols-one"
                                                        style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0"
                                                        width="100%" cellspacing="0" cellpadding="0" border="0">
                                                        <tbody>
                                                            <tr>
                                                                <td class="mailpoet_image "
                                                                    style="border-collapse:collapse" valign="top"
                                                                    align="center">
                                                                    <img src="https://trello-attachments.s3.amazonaws.com/5f8db9c45f7b8669635636e6/799x163/5b839e3aebc1440edba9fdbf4dafd3e1/toop-mail2.jpg"
                                                                        alt="NETLIFE PLAY"
                                                                        style="height:auto;max-width:100%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"
                                                                        width="660">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td class="mailpoet_content-cols-three"
                                    style="border-collapse:collapse;background-color:  !important;background-color: ;"
                                    bgcolor="#ffffff" align="left">
                                    <table
                                        style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                                        width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <tbody>
                                            <tr>
                                                <td style="border-collapse:collapse;font-size:0;background-color: #ff6700;"
                                                    align="center">
                                                    <!--[if mso]>
                  <table border="0" width="100%" cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>
      <td width="220" valign="top">
        <![endif]-->
                                                    <div style="display:inline-block; max-width:54px; vertical-align:top; width:100%;"
                                                        wfd-id="2">
                                                        <table class="mailpoet_cols-three"
                                                            style="border-collapse:collapse;width:100%;max-width:54px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0"
                                                            width="220" cellspacing="0" cellpadding="0" border="0"
                                                            align="right">
                                                            <tbody>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <!--[if mso]>
      </td>
      <td width="220" valign="top">
        <![endif]-->
                                                    <div style="display:inline-block; max-width:552px; vertical-align:top; width:100%;"
                                                        wfd-id="1">
                                                        <table class="mailpoet_cols-three"
                                                            style="border-collapse:collapse;width:100%;max-width:552px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;background-color: #ededed;"
                                                            width="220" cellspacing="0" cellpadding="0" border="0"
                                                            align="right">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="mailpoet_image "
                                                                        style="border-collapse:collapse" valign="top"
                                                                        align="center">
                                                                        <img src="https://www.netlife.ec/wp-content/uploads/2021/08/logo-gris-futbo.jpg"
                                                                            alt="El canal del futbol"
                                                                            style="height:auto;max-width:80%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"
                                                                            width="220">
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
                                                                                <tr>
                                                                                    <td class="mailpoet_paragraph"
                                                                                        style="border-collapse:collapse;color:#706f6f;font-family:lato,'helvetica neue',helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:center;">
                                                                                        <strong>Hola {{ cliente
                                                                                            }}</strong>
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
                                                                        <table
                                                                            style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0"
                                                                            width="100%" cellpadding="0">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td class="mailpoet_paragraph"
                                                                                        style="border-collapse:collapse;color:#706f6f;font-family:lato,'helvetica neue',helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:justify;">
                                                                                        <strong>NETLIFE</strong> te
                                                                                        informa que se ha reenviado
                                                                                        satisfactoriamente
                                                                                        tu contraseña de acceso
                                                                                        para el servicio 
                                                                                        <strong>El Canal del Fútbol</strong>. 
                                                                                        A continuación se
                                                                                        indicará el
                                                                                        usuario y contraseña con
                                                                                        los cuales podrás acceder
                                                                                        a la
                                                                                        plataforma <strong>El Canal de
                                                                                            Fútbol</strong>. Si
                                                                                        existen
                                                                                        inconvenientes o novedades con
                                                                                        respecto al servicio no dudes en
                                                                                        contactarnos
                                                                                        por correo electrónico a:
                                                                                        <strong>soporte@netlife.net.ec</strong>
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
                                                                            style="margin:0 0 9px;font-family:Oswald,'Trebuchet MS','Lucida Grande','Lucida Sans Unicode','Lucida Sans',Tahoma,sans-serif;font-size:30px;line-height:36px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal">
                                                                            <strong></strong><strong
                                                                                style="color: #D11E1F;">DATOS DE
                                                                                ACCESO<br></strong>
                                                                        </h1>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side"
                                                                        style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word"
                                                                        valign="top">
                                                                        <h2
                                                                            style="margin:0 0 6px;color:#706f6f;font-family:Oswald,'Trebuchet MS','Lucida Grande','Lucida Sans Unicode','Lucida Sans',Tahoma,sans-serif;font-size:20px;line-height:24px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal">
                                                                            <strong>Usuario: {{ usuario }}<br>Contraseña
                                                                                : {{ contrasenia }}</strong>
                                                                        </h2>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side"
                                                                        style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word"
                                                                        valign="top">
                                                                        <h3
                                                                            style="margin:0 0 5.4px;color:875bd8;font-family:Oswald,'Trebuchet MS','Lucida Grande','Lucida Sans Unicode','Lucida Sans',Tahoma,sans-serif;font-size:18px;line-height:21.6px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal;padding-top: 30px;">
                                                                            <strong></strong><strong
                                                                                style="color: #D11E1F;">ACCEDE A TU
                                                                                SERVICIO HACIENDO
                                                                                <br></strong>
                                                                        </h3>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="mailpoet_image "
                                                                        style="border-collapse:collapse" valign="top"
                                                                        align="center">
                                                                        <a href="https://elcanaldelfutbol.com/netlife"><img
                                                                                src="https://www.netlife.ec/wp-content/uploads/2021/08/btn-futbol.jpg"
                                                                                alt="CLICK AQUI"
                                                                                style="height:auto;max-width:40%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%"
                                                                                width="220"></a>
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
                                                                                <tr>
                                                                                    <td class="mailpoet_paragraph"
                                                                                        style="border-collapse:collapse;color:#706f6f;font-family:lato,'helvetica neue',helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:center">
                                                                                        <o:p style="font-size: 14.2px">
                                                                                            &ldquo;El Servicio
                                                                                            contratado El Canal del
                                                                                            Fútbol tiene un
                                                                                            costo mensual de Usd.
                                                                                            $5.36+IVA, el mismo que
                                                                                            será incluido en su
                                                                                            factura mensual del
                                                                                            Servicio de
                                                                                            Internet.&ldquo;&nbsp;</o:p>
                                                                                        <br>
                                                                                        <br>Gracias por ser parte de
                                                                                        NETLIFE,<br>EQUIPO NETLIFE •
                                                                                        39-20-000
                                                                                    </td>

                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                    </div>
                                                    <!--[if mso]>
      </td>
      <td width="220" valign="top">
        <![endif]-->
                                                    <div style="display:inline-block; max-width:54px; vertical-align:top; width:100%;"
                                                        wfd-id="0">
                                                        <table class="mailpoet_cols-three"
                                                            style="border-collapse:collapse;width:100%;max-width:54px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0"
                                                            width="220" cellspacing="0" cellpadding="0" border="0"
                                                            align="right">
                                                            <tbody>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <!--[if mso]>
      </td>
                  </tr>
                </tbody>
              </table>
            <![endif]-->
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table style="background-color: #000000;" width="660" cellspacing="0"
                                        cellpadding="0" border="0">
                                        <tbody>
                                            <tr>
                                                <th style="width: 220px" scope="row"><a
                                                        href="https://www.facebook.com/netlife.ecuador"><img
                                                            src="https://trello-attachments.s3.amazonaws.com/5f0e1c290c58972b72a372db/158x44/68f99dae7571da4c28f9a63b3c7053b9/face.jpg"
                                                            alt="internet seguro" width="110" height="30	"></a></th>
                                                <td style="width: 200px"><a
                                                        href="https://twitter.com/NetlifeEcuador"><img
                                                            src="https://trello-attachments.s3.amazonaws.com/5f0e1c290c58972b72a372db/158x44/4b21d67e4dc5a2a472d96e0c2dcc4cad/twitter.jpg"
                                                            alt="internet seguro" width="110" height="30"></a></td>
                                                <td style="width: 240px"><a
                                                        href="https://www.youtube.com/channel/UCnc9ndF9fEDdyeVVJ7j1SDw"><img
                                                            src="https://trello-attachments.s3.amazonaws.com/5f0e1c290c58972b72a372db/158x44/e1f1a0638ea7001114b231e1738105ee/youtube.jpg"
                                                            alt="internet seguro" width="110" height="30"></a></td>
                                                <td style="width: 200px"><img
                                                        src="https://trello-attachments.s3.amazonaws.com/5f0e1c290c58972b72a372db/262x120/64bb2db4cce8226e85e878e1c22b8bad/footer-net2.jpg"
                                                        alt="internet seguro" width="210" height="87"></td>
                                            </tr>
                                        </tbody>
                                    </table>
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


/* SE REALIZA LA MODIFICACIÓN DE LA PLANTILLA PARA EL REENVIO DE SMS DEL CANAL DEL FUTBOL*/

UPDATE DB_COMUNICACION.ADMI_PLANTILLA
SET ESTADO = 'Activo', PLANTILLA = 'Estimado/a Cliente, Netlife le reenvía las credenciales del Servicio NETLIFEPLAY / El Canal de Futbol, Usuario: {{USUARIO}} Contrasena: {{CONTRASENIA}}, empieza la diversion aqui: https://elcanaldelfutbol.com/'
where NOMBRE_PLANTILLA = 'SMS_REENV_ECDF' AND CODIGO = 'SMS_REENV_ECDF' AND MODULO = 'TECNICO';




--PLANTILLA DE TAREA DE SOPORTE PARA ACTUALIZAR CORREO ECDF
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
(ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO, FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD)
VALUES(DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL, 'El canal del futbol actualizar correo', 'ACT_CORREO_ECDF', 'SOPORTE',
'<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body><table align="center" width="100%" 
cellspacing="0" cellpadding="5"><td style="border:1px solid #69c"><table width="100%" cellspacing="0" cellpadding="5">
<tr><td colspan="2">Error en la activacion de correo ECDF:</td></tr><tr><td colspan="2"><hr></td>
</tr><tr><td valign="top"><p style="margin-right:.8pt;text-align:justify;mso-height-rule:exactly">
<span style="font-size:11pt;font-family:Arial,sans-serif"><b>WS:</b><span>{{ ws_actualizar_correo }}</span>
</span></p><p style="margin-right:.8pt;text-align:justify;mso-height-rule:exactly">
<span style="font-size:11pt;font-family:Arial,sans-serif"><b>Identificación del cliente:</b>
<span>{{ identificacion_cliente }}</span></span></p><p style="margin-right:.8pt;text-align:justify;mso-height-rule:exactly">
<span style="font-size:11pt;font-family:Arial,sans-serif"><b>Login del cliente:</b><span>{{ login_cliente }}</span></span>
</p><p style="margin-right:.8pt;text-align:justify;mso-height-rule:exactly">
<span style="font-size:11pt;font-family:Arial,sans-serif"><b>Error técnico:</b><span>{{ error_tecnico }}</span>
</span></p></td></tr></table></td><tr><td>&nbsp;</td></tr><tr><td><strong><font size="2" face="Tahoma">
Telcos + Sistema del Grupo Telconet</font></strong></p></td></tr></table></body></html>', 
'Activo', SYSDATE, 'farias', NULL, NULL, NULL);

COMMIT;
/