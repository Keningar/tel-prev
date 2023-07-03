SET DEFINE OFF
-- Plantillas para goltv
-- Plan 1 - Correo Nuevo
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
(
  ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, ESTADO, FE_CREACION,
  USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD, PLANTILLA
)
VALUES 
(
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Goltv nuevo servicio','GOLTV-NUEVO','TECNICO',
	'Activo',sysdate,'djreyes',null,null,18,
    TO_CLOB('<html style="margin:0;padding:0" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="format-detection" content="telephone=no">
<title>Asunto</title>
<style type="text/css">
@media screen and (max-width: 480px) {.mailpoet_button {width:100% !important;}}
@media screen and (max-width: 599px) {
.mailpoet_header {padding: 10px 20px;}
.mailpoet_button {width: 100% !important;padding: 5px 0 !important;box-sizing:border-box !important;}
div, .mailpoet_cols-two, .mailpoet_cols-three {max-width: 100% !important;}}
</style>
') || TO_CLOB('<style>
._3emE9--dark-theme .-S-tR--ff-downloader{background:rgba(30,30,30,.93);border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#fff}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{background:#3d4b52}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#131415}
._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer{background:rgba(30,30,30,.93)}
._2mDEx--white-theme .-S-tR--ff-downloader{background:#fff;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#314c75}
._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header{font-weight:700}
._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{border:0;color:rgba(0,0,0,.88)}
._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer{background:#fff}
.-S-tR--ff-downloader{display:block;overflow:hidden;position:fixed;bottom:20px;right:7.1%;width:330px;height:180px;background:rgba(30,30,30,.93);border-radius:2px;color:#fff;z-index:99999999;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);transition:.5s}
.-S-tR--ff-downloader._3M7UQ--minimize{height:62px}
.-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,.-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header{display:none}
.-S-tR--ff-downloader ._6_Mtt--header{padding:10px;font-size:17px;font-family:sans-serif}
') || TO_CLOB('.-S-tR--ff-downloader ._6_Mtt--header._2VdJW--minimize-btn{float:right;background:#f1ecec;height:20px;width:20px;text-align:center;padding:2px;margin-top:-10px;cursor:pointer}
.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#e2dede}
.-S-tR--ff-downloader ._13XQ2--error{color:red;padding:10px;font-size:12px;line-height:19px}
.-S-tR--ff-downloader ._2dFLA--container{position:relative;height:100%}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info{padding:6px 15px 0;font-family:sans-serif}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div{margin-bottom:5px;width:100%;overflow:hidden}
.-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{margin-top:21px;font-size:11px}
.-S-tR--ff-downloader ._10vpG--footer{width:100%;bottom:0;position:absolute;font-weight:700}
.-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader{animation:n0BD1--rotation 3.5s linear forwards;position:absolute;top:-120px;left:calc(50% - 35px);border-radius:50%;border:5px solid #fff;border-top-color:#a29bfe;height:70px;width:70px;display:flex;justify-content:center;align-items:center}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar{width:100%;height:18px;background:#dfe6e9;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar{height:100%;background:#8bc34a;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status{margin-top:10px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state{float:left;font-size:.9em;letter-spacing:1pt;text-transform:uppercase;width:100px;height:20px;position:relative}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage{float:right}
</style>
') || TO_CLOB('<style>
._3emE9--dark-theme .-S-tR--ff-downloader{background:rgba(30,30,30,.93);border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#fff}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{background:#3d4b52}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#131415}
._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer{background:rgba(30,30,30,.93)}
._2mDEx--white-theme .-S-tR--ff-downloader{background:#fff;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#314c75}
._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header{font-weight:700}
._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{border:0;color:rgba(0,0,0,.88)}
._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer{background:#fff}
.-S-tR--ff-downloader{display:block;overflow:hidden;position:fixed;bottom:20px;right:7.1%;width:330px;height:180px;background:rgba(30,30,30,.93);border-radius:2px;color:#fff;z-index:99999999;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);transition:.5s}
.-S-tR--ff-downloader._3M7UQ--minimize{height:62px}
.-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,.-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header{display:none}
.-S-tR--ff-downloader ._6_Mtt--header{padding:10px;font-size:17px;font-family:sans-serif}
') || TO_CLOB('.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{float:right;background:#f1ecec;height:20px;width:20px;text-align:center;padding:2px;margin-top:-10px;cursor:pointer}
.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#e2dede}
.-S-tR--ff-downloader ._13XQ2--error{color:red;padding:10px;font-size:12px;line-height:19px}
.-S-tR--ff-downloader ._2dFLA--container{position:relative;height:100%}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info{padding:6px 15px 0;font-family:sans-serif}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div{margin-bottom:5px;width:100%;overflow:hidden}
.-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{margin-top:21px;font-size:11px}
.-S-tR--ff-downloader ._10vpG--footer{width:100%;bottom:0;position:absolute;font-weight:700}
.-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader{animation:n0BD1--rotation 3.5s linear forwards;position:absolute;top:-120px;left:calc(50% - 35px);border-radius:50%;border:5px solid #fff;border-top-color:#a29bfe;height:70px;width:70px;display:flex;justify-content:center;align-items:center}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar{width:100%;height:18px;background:#dfe6e9;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar{height:100%;background:#8bc34a;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status{margin-top:10px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state{float:left;font-size:.9em;letter-spacing:1pt;text-transform:uppercase;width:100px;height:20px;position:relative}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage{float:right}
</style>
') || TO_CLOB('<style>
._3emE9--dark-theme .-S-tR--ff-downloader{background:rgba(30,30,30,.93);border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#fff}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{background:#3d4b52}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#131415}
._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer{background:rgba(30,30,30,.93)}
._2mDEx--white-theme .-S-tR--ff-downloader{background:#fff;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#314c75}
._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header{font-weight:700}
._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{border:0;color:rgba(0,0,0,.88)}
._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer{background:#fff}
.-S-tR--ff-downloader{display:block;overflow:hidden;position:fixed;bottom:20px;right:7.1%;width:330px;height:180px;background:rgba(30,30,30,.93);border-radius:2px;color:#fff;z-index:99999999;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);transition:.5s}
.-S-tR--ff-downloader._3M7UQ--minimize{height:62px}
.-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,.-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header{display:none}
.-S-tR--ff-downloader ._6_Mtt--header{padding:10px;font-size:17px;font-family:sans-serif}
') || TO_CLOB('.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{float:right;background:#f1ecec;height:20px;width:20px;text-align:center;padding:2px;margin-top:-10px;cursor:pointer}
.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#e2dede}
.-S-tR--ff-downloader ._13XQ2--error{color:red;padding:10px;font-size:12px;line-height:19px}
.-S-tR--ff-downloader ._2dFLA--container{position:relative;height:100%}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info{padding:6px 15px 0;font-family:sans-serif}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div{margin-bottom:5px;width:100%;overflow:hidden}
.-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{margin-top:21px;font-size:11px}
.-S-tR--ff-downloader ._10vpG--footer{width:100%;bottom:0;position:absolute;font-weight:700}
.-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader{animation:n0BD1--rotation 3.5s linear forwards;position:absolute;top:-120px;left:calc(50% - 35px);border-radius:50%;border:5px solid #fff;border-top-color:#a29bfe;height:70px;width:70px;display:flex;justify-content:center;align-items:center}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar{width:100%;height:18px;background:#dfe6e9;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar{height:100%;background:#8bc34a;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status{margin-top:10px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state{float:left;font-size:.9em;letter-spacing:1pt;text-transform:uppercase;width:100px;height:20px;position:relative}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage{float:right}
</style>
') || TO_CLOB('<style>
._3emE9--dark-theme .-S-tR--ff-downloader{background:rgba(30,30,30,.93);border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#fff}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{background:#3d4b52}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#131415}
._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer{background:rgba(30,30,30,.93)}
._2mDEx--white-theme .-S-tR--ff-downloader{background:#fff;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#314c75}
._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header{font-weight:700}
._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{border:0;color:rgba(0,0,0,.88)}
._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer{background:#fff}
.-S-tR--ff-downloader{display:block;overflow:hidden;position:fixed;bottom:20px;right:7.1%;width:330px;height:180px;background:rgba(30,30,30,.93);border-radius:2px;color:#fff;z-index:99999999;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);transition:.5s}
.-S-tR--ff-downloader._3M7UQ--minimize{height:62px}
.-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,.-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header{display:none}
.-S-tR--ff-downloader ._6_Mtt--header{padding:10px;font-size:17px;font-family:sans-serif}
') || TO_CLOB('.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{float:right;background:#f1ecec;height:20px;width:20px;text-align:center;padding:2px;margin-top:-10px;cursor:pointer}
.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#e2dede}
.-S-tR--ff-downloader ._13XQ2--error{color:red;padding:10px;font-size:12px;line-height:19px}
.-S-tR--ff-downloader ._2dFLA--container{position:relative;height:100%}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info{padding:6px 15px 0;font-family:sans-serif}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div{margin-bottom:5px;width:100%;overflow:hidden}
.-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{margin-top:21px;font-size:11px}
.-S-tR--ff-downloader ._10vpG--footer{width:100%;bottom:0;position:absolute;font-weight:700}
.-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader{animation:n0BD1--rotation 3.5s linear forwards;position:absolute;top:-120px;left:calc(50% - 35px);border-radius:50%;border:5px solid #fff;border-top-color:#a29bfe;height:70px;width:70px;display:flex;justify-content:center;align-items:center}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar{width:100%;height:18px;background:#dfe6e9;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar{height:100%;background:#8bc34a;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status{margin-top:10px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state{float:left;font-size:.9em;letter-spacing:1pt;text-transform:uppercase;width:100px;height:20px;position:relative}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage{float:right}
</style>
') || TO_CLOB('<style>
._3emE9--dark-theme .-S-tR--ff-downloader{background:rgba(30,30,30,.93);border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#fff}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{background:#3d4b52}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#131415}
._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer{background:rgba(30,30,30,.93)}
._2mDEx--white-theme .-S-tR--ff-downloader{background:#fff;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#314c75}
._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header{font-weight:700}
._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{border:0;color:rgba(0,0,0,.88)}
._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer{background:#fff}
.-S-tR--ff-downloader{display:block;overflow:hidden;position:fixed;bottom:20px;right:7.1%;width:330px;height:180px;background:rgba(30,30,30,.93);border-radius:2px;color:#fff;z-index:99999999;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);transition:.5s}
.-S-tR--ff-downloader._3M7UQ--minimize{height:62px}
.-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,.-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header{display:none}
.-S-tR--ff-downloader ._6_Mtt--header{padding:10px;font-size:17px;font-family:sans-serif}
') || TO_CLOB('.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{float:right;background:#f1ecec;height:20px;width:20px;text-align:center;padding:2px;margin-top:-10px;cursor:pointer}
.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#e2dede}
.-S-tR--ff-downloader ._13XQ2--error{color:red;padding:10px;font-size:12px;line-height:19px}
.-S-tR--ff-downloader ._2dFLA--container{position:relative;height:100%}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info{padding:6px 15px 0;font-family:sans-serif}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div{margin-bottom:5px;width:100%;overflow:hidden}
.-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{margin-top:21px;font-size:11px}
.-S-tR--ff-downloader ._10vpG--footer{width:100%;bottom:0;position:absolute;font-weight:700}
.-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader{animation:n0BD1--rotation 3.5s linear forwards;position:absolute;top:-120px;left:calc(50% - 35px);border-radius:50%;border:5px solid #fff;border-top-color:#a29bfe;height:70px;width:70px;display:flex;justify-content:center;align-items:center}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar{width:100%;height:18px;background:#dfe6e9;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar{height:100%;background:#8bc34a;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status{margin-top:10px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state{float:left;font-size:.9em;letter-spacing:1pt;text-transform:uppercase;width:100px;height:20px;position:relative}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage{float:right}
</style>
') || TO_CLOB('<style>
._3emE9--dark-theme .-S-tR--ff-downloader{background:rgba(30,30,30,.93);border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#fff}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{background:#3d4b52}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#131415}
._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer{background:rgba(30,30,30,.93)}
._2mDEx--white-theme .-S-tR--ff-downloader{background:#fff;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#314c75}
._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header{font-weight:700}
._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{border:0;color:rgba(0,0,0,.88)}
._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer{background:#fff}
.-S-tR--ff-downloader{display:block;overflow:hidden;position:fixed;bottom:20px;right:7.1%;width:330px;height:180px;background:rgba(30,30,30,.93);border-radius:2px;color:#fff;z-index:99999999;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);transition:.5s}
.-S-tR--ff-downloader._3M7UQ--minimize{height:62px}
.-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,.-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header{display:none}
.-S-tR--ff-downloader ._6_Mtt--header{padding:10px;font-size:17px;font-family:sans-serif}
') || TO_CLOB('.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{float:right;background:#f1ecec;height:20px;width:20px;text-align:center;padding:2px;margin-top:-10px;cursor:pointer}
.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#e2dede}
.-S-tR--ff-downloader ._13XQ2--error{color:red;padding:10px;font-size:12px;line-height:19px}
.-S-tR--ff-downloader ._2dFLA--container{position:relative;height:100%}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info{padding:6px 15px 0;font-family:sans-serif}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div{margin-bottom:5px;width:100%;overflow:hidden}
.-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{margin-top:21px;font-size:11px}
.-S-tR--ff-downloader ._10vpG--footer{width:100%;bottom:0;position:absolute;font-weight:700}
.-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader{animation:n0BD1--rotation 3.5s linear forwards;position:absolute;top:-120px;left:calc(50% - 35px);border-radius:50%;border:5px solid #fff;border-top-color:#a29bfe;height:70px;width:70px;display:flex;justify-content:center;align-items:center}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar{width:100%;height:18px;background:#dfe6e9;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar{height:100%;background:#8bc34a;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status{margin-top:10px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state{float:left;font-size:.9em;letter-spacing:1pt;text-transform:uppercase;width:100px;height:20px;position:relative}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage{float:right}
</style>
') || TO_CLOB('</head>
<body style="margin:0;padding:0;background-color:#d4d4d4" topmargin="0" marginwidth="0" marginheight="0" leftmargin="0">
<table class="mailpoet_template" style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody><tr>
<td class="mailpoet_preheader" style="border-collapse:collapse;display:none;visibility:hidden;mso-hide:all;font-size:1px;color:#333333;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;-webkit-text-size-adjust:none" height="1">
</td></tr><tr>
<td class="mailpoet-wrapper" style="border-collapse:collapse;background-color:#d4d4d4" valign="top" align="center">
<table class="mailpoet_content-wrapper" style="border-collapse:collapse;background-color:#000000;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;max-width:660px;width:100%" width="660" cellspacing="0" cellpadding="0" border="0">
<tbody><tr>
<td class="mailpoet_content" style="border-collapse:collapse;background-color:#ffffff!important" bgcolor="#ffffff" align="center">
<table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody><tr>
<td style="border-collapse:collapse;padding-left:0;padding-right:0">
<table class="mailpoet_cols-one" style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0" width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody><tr>
<td class="mailpoet_image " style="border-collapse:collapse" valign="top" align="center">
') || TO_CLOB('<img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0MD9tPm1rOT85ODw4O2g5OGg+MWg8ajk/OzswajtrbWtrPGg5aDFvai99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f8db9c45f7b8669635636e6%2f799x163%2f5b839e3aebc1440edba9fdbf4dafd3e1%2ftoop-mail2.jpg&fmlBlkTk" alt="NETLIFE PLAY" style="height:auto;max-width:100%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%" width="660"></img>
</td></tr></tbody></table></td></tr></tbody></table></td></tr><tr>
<td class="mailpoet_content-cols-three" style="border-collapse:collapse;background-color:  !important;background-color: ;" bgcolor="#ffffff" align="left">
<table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody><tr>
<td style="border-collapse:collapse;font-size:0;background-color: #ff6700;" align="center">
<div style="display:inline-block; max-width:54px; vertical-align:top; width:100%;" wfd-id="2">
<table class="mailpoet_cols-three" style="border-collapse:collapse;width:100%;max-width:54px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0" width="220" cellspacing="0" cellpadding="0" border="0" align="right">
<tbody></tbody></table></div>
<div style="display:inline-block; max-width:552px; vertical-align:top; width:100%;" wfd-id="1">
<table class="mailpoet_cols-three" style="border-collapse:collapse;width:100%;max-width:552px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;background-color: ededed;" width="220" cellspacing="0" cellpadding="0" border="0" align="right">
<tbody><tr>
<td class="mailpoet_image " style="border-collapse:collapse" valign="top" align="center">
') || TO_CLOB('<img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0Pj85bThrP2psbzs9MT8/OzA7bGs8bGhvbDlrOjA4PzBsOjgwbDoxOS99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=http%3a%2f%2fwww.netlife.ec%2fwp-content%2fuploads%2f2021%2f09%2fLOGO_GOLTV_BLACK.png&fmlBlkTk" alt="noggin" style="height:auto;max-width:80%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%" width="220"></img>
</td></tr><tr><td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word" valign="top">
<table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellpadding="0">
<tbody><tr>
<td class="mailpoet_paragraph" style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:center;">
<strong>Hola {{ cliente }}</strong></td></tr></tbody></table></td></tr><tr>
<td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word" valign="top">
<table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellpadding="0">
<tbody><tr>
') || TO_CLOB('<td class="mailpoet_paragraph" style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:justify;">
<strong>NETLIFE</strong> te informa que el servicio de&nbsp;<strong>GOLTV PLAY&nbsp;</strong>ha sido activado satisfactoriamente.<strong>&nbsp;&nbsp;</strong>A continuaci&oacute;n se indicar&aacute; el usuario y contrase&ntilde;a con los cuales podr&aacute;s acceder a la plataforma de&nbsp; <strong>GOLTV PLAY</strong> Si existen inconvenientes o novedades con respecto al servicio no dudes en contactarnos por correo electr&oacute;nico a: <strong>soporte@netlife.net.ec.</strong></td>
</tr></tbody></table></td></tr><tr>
<td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word" valign="top">
<h1 style="margin:0 0 9px;font-family:Oswald,&#39;Trebuchet MS&#39;,&#39;Lucida Grande&#39;,&#39;Lucida Sans Unicode&#39;,&#39;Lucida Sans&#39;,Tahoma,sans-serif;font-size:30px;line-height:36px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal"><strong></strong><strong style="color: #F7941D;">DATOS DE ACCESO<br></strong></h1>
</td></tr><tr>
<td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word" valign="top">
<h2 style="margin:0 0 6px;color:#706f6f;font-family:Oswald,&#39;Trebuchet MS&#39;,&#39;Lucida Grande&#39;,&#39;Lucida Sans Unicode&#39;,&#39;Lucida Sans&#39;,Tahoma,sans-serif;font-size:20px;line-height:24px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal"><strong>Usuario: {{ usuario }}<br>Contrase&ntilde;a : {{ contrasenia }}</strong></h2>
</td></tr><tr><td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word" valign="top">
<h3 style="margin:0 0 5.4px;color:875bd8;font-family:Oswald,&#39;Trebuchet MS&#39;,&#39;Lucida Grande&#39;,&#39;Lucida Sans Unicode&#39;,&#39;Lucida Sans&#39;,Tahoma,sans-serif;font-size:18px;line-height:21.6px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal;padding-top: 30px;"><strong></strong><strong style="color: #000000;">ACCEDE A TU SERVICIO HACIENDO <br></strong></h3>
</td></tr><tr>
<td class="mailpoet_image " style="border-collapse:collapse" valign="top" align="center">
') || TO_CLOB('<a href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0OTkxa21va2s8PTw+a287Oj89OThqO2xtOTgxbG1raD0/a2xqOTBtai99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=https%3a%2f%2fwww.netlife.ec%2f"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0aj0+ODlra2g6ajExOmxrPGhvPm07Pjo6Ojs9MThqaGs/OW1taD04aC99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=http%3a%2f%2fwww.netlife.ec%2fwp-content%2fuploads%2f2021%2f09%2fBotonGOLTV.png&fmlBlkTk" alt="CLICK AQUI" style="height:auto;max-width:40%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%" width="220"></img>
</a></td></tr><tr>
<td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word" valign="top">
<table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellpadding="0">
<tbody><tr>
<td class="mailpoet_paragraph" style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:center">
<o:p style="font-size: 14.2px">&ldquo;El Servicio contratado GOLTV PLAY tiene un costo mensual de $9.99 + IVA el mismo que ser&aacute; incluido en su factura mensual del Servicio de Internet. &ldquo;&nbsp;</o:p>
<p> Gracias por ser parte de NETLIFE,<br> EQUIPO NETLIFE &bull; 39-20-000 </p>
</td></tr></tbody></table></td></tr></tbody></table></div>
<div style="display:inline-block; max-width:54px; vertical-align:top; width:100%;" wfd-id="0">
<table class="mailpoet_cols-three" style="border-collapse:collapse;width:100%;max-width:54px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0" width="220" cellspacing="0" cellpadding="0" border="0" align="right">
<tbody></tbody></table></div></td></tr></tbody></table>
<table style="background-color: #000000;" width="660" cellspacing="0" cellpadding="0" border="0">
<tbody><tr><th style="width: 220px" scope="row">
') || TO_CLOB('<a href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0bD5qO2w6MDw9PGxoPD5raztoPD1sMG0/aD86aDtoPz1obGo+ajw+PC99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDo+L2FtZTQ5&url=https%3a%2f%2fwww.facebook.com%2fnetlife.ecuador"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0bDA7MD9oMDAxPmw4aDEwOTBtPjk+PGwxPDg+MT4xazhsb2w6PT44PS99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2f68f99dae7571da4c28f9a63b3c7053b9%2fface.jpg&fmlBlkTk" alt="internet seguro" width="110" height="30"></img>
</a></th><td style="width: 200px">
<a href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0O2pqMD9qPTg5Pmo+aj88PDBvP2g4OG9qPT5rPzk7bTk+a2hvaDlsai99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDo+L2FtZTQ5&url=https%3a%2f%2ftwitter.com%2fNetlifeEcuador"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0bD07Oj46b284OztrPW0+OGhsOmw7MGo+bTs/Oj0wazxtPj49PTk/bS99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2f4b21d67e4dc5a2a472d96e0c2dcc4cad%2ftwitter.jpg&fmlBlkTk" alt="internet seguro" width="110" height="30"></img>
</a></td><td style="width: 240px">
<a href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0Omg7OG1rOztoMTxobG9vPTE4MG9vPms+bD5vPmgxOTFtaz4+Oz4+MC99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDs8L2FtZTQ5&url=https%3a%2f%2fwww.youtube.com%2fchannel%2fUCnc9ndF9fEDdyeVVJ7j1SDw"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0OmhtOWxsa285bTw/MGhtaGtsamhqPTpqamxobTo4MD08O2g5am1qPi99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2fe1f1a0638ea7001114b231e1738105ee%2fyoutube.jpg&fmlBlkTk" alt="internet seguro" width="110" height="30"></img>
</a></td>
') || TO_CLOB('<td style="width: 200px"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0MTlqPDg4MTA7OjkwP2xoPGswbTE8MDtsPjgxO2o5OD87Pzw9PmprOi99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f262x120%2f64bb2db4cce8226e85e878e1c22b8bad%2ffooter-net2.jpg&fmlBlkTk" alt="internet seguro" width="210" height="87"></img>
</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></body></html>')
);

-- Plan 2 - Correo Reenvio de contrasena
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
(
  ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, ESTADO, FE_CREACION,
  USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD, PLANTILLA
)
VALUES 
(
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Goltv reenvio contrasena','GOLTV-RENCON','TECNICO',
	'Activo',sysdate,'djreyes',null,null,18,
    TO_CLOB('<html style="margin:0;padding:0" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="format-detection" content="telephone=no">
<title>Asunto</title>
<style type="text/css">
@media screen and (max-width: 480px) {.mailpoet_button {width:100% !important;}}
@media screen and (max-width: 599px) {
.mailpoet_header {padding: 10px 20px;}
.mailpoet_button {width: 100% !important;padding: 5px 0 !important;box-sizing:border-box !important;}
div, .mailpoet_cols-two, .mailpoet_cols-three {max-width: 100% !important;}}
</style>
') || TO_CLOB('<style>
._3emE9--dark-theme .-S-tR--ff-downloader{background:rgba(30,30,30,.93);border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#fff}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{background:#3d4b52}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#131415}
._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer{background:rgba(30,30,30,.93)}
._2mDEx--white-theme .-S-tR--ff-downloader{background:#fff;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#314c75}
._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header{font-weight:700}
._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{border:0;color:rgba(0,0,0,.88)}
._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer{background:#fff}
.-S-tR--ff-downloader{display:block;overflow:hidden;position:fixed;bottom:20px;right:7.1%;width:330px;height:180px;background:rgba(30,30,30,.93);border-radius:2px;color:#fff;z-index:99999999;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);transition:.5s}
.-S-tR--ff-downloader._3M7UQ--minimize{height:62px}
.-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,.-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header{display:none}
.-S-tR--ff-downloader ._6_Mtt--header{padding:10px;font-size:17px;font-family:sans-serif}
') || TO_CLOB('.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{float:right;background:#f1ecec;height:20px;width:20px;text-align:center;padding:2px;margin-top:-10px;cursor:pointer}
.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#e2dede}
.-S-tR--ff-downloader ._13XQ2--error{color:red;padding:10px;font-size:12px;line-height:19px}
.-S-tR--ff-downloader ._2dFLA--container{position:relative;height:100%}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info{padding:6px 15px 0;font-family:sans-serif}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div{margin-bottom:5px;width:100%;overflow:hidden}
.-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{margin-top:21px;font-size:11px}
.-S-tR--ff-downloader ._10vpG--footer{width:100%;bottom:0;position:absolute;font-weight:700}
.-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader{animation:n0BD1--rotation 3.5s linear forwards;position:absolute;top:-120px;left:calc(50% - 35px);border-radius:50%;border:5px solid #fff;border-top-color:#a29bfe;height:70px;width:70px;display:flex;justify-content:center;align-items:center}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar{width:100%;height:18px;background:#dfe6e9;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar{height:100%;background:#8bc34a;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status{margin-top:10px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state{float:left;font-size:.9em;letter-spacing:1pt;text-transform:uppercase;width:100px;height:20px;position:relative}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage{float:right}
</style>
') || TO_CLOB('<style>
._3emE9--dark-theme .-S-tR--ff-downloader{background:rgba(30,30,30,.93);border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#fff}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{background:#3d4b52}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#131415}
._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer{background:rgba(30,30,30,.93)}
._2mDEx--white-theme .-S-tR--ff-downloader{background:#fff;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#314c75}
._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header{font-weight:700}
._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{border:0;color:rgba(0,0,0,.88)}
._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer{background:#fff}
.-S-tR--ff-downloader{display:block;overflow:hidden;position:fixed;bottom:20px;right:7.1%;width:330px;height:180px;background:rgba(30,30,30,.93);border-radius:2px;color:#fff;z-index:99999999;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);transition:.5s}
.-S-tR--ff-downloader._3M7UQ--minimize{height:62px}
.-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,.-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header{display:none}
.-S-tR--ff-downloader ._6_Mtt--header{padding:10px;font-size:17px;font-family:sans-serif}
') || TO_CLOB('.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{float:right;background:#f1ecec;height:20px;width:20px;text-align:center;padding:2px;margin-top:-10px;cursor:pointer}
.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#e2dede}
.-S-tR--ff-downloader ._13XQ2--error{color:red;padding:10px;font-size:12px;line-height:19px}
.-S-tR--ff-downloader ._2dFLA--container{position:relative;height:100%}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info{padding:6px 15px 0;font-family:sans-serif}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div{margin-bottom:5px;width:100%;overflow:hidden}
.-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{margin-top:21px;font-size:11px}
.-S-tR--ff-downloader ._10vpG--footer{width:100%;bottom:0;position:absolute;font-weight:700}
.-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader{animation:n0BD1--rotation 3.5s linear forwards;position:absolute;top:-120px;left:calc(50% - 35px);border-radius:50%;border:5px solid #fff;border-top-color:#a29bfe;height:70px;width:70px;display:flex;justify-content:center;align-items:center}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar{width:100%;height:18px;background:#dfe6e9;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar{height:100%;background:#8bc34a;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status{margin-top:10px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state{float:left;font-size:.9em;letter-spacing:1pt;text-transform:uppercase;width:100px;height:20px;position:relative}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage{float:right}
</style>
') || TO_CLOB('<style>
._3emE9--dark-theme .-S-tR--ff-downloader{background:rgba(30,30,30,.93);border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#fff}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{background:#3d4b52}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#131415}
._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer{background:rgba(30,30,30,.93)}
._2mDEx--white-theme .-S-tR--ff-downloader{background:#fff;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#314c75}
._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header{font-weight:700}
._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{border:0;color:rgba(0,0,0,.88)}
._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer{background:#fff}
.-S-tR--ff-downloader{display:block;overflow:hidden;position:fixed;bottom:20px;right:7.1%;width:330px;height:180px;background:rgba(30,30,30,.93);border-radius:2px;color:#fff;z-index:99999999;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);transition:.5s}
.-S-tR--ff-downloader._3M7UQ--minimize{height:62px}
.-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,.-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header{display:none}
.-S-tR--ff-downloader ._6_Mtt--header{padding:10px;font-size:17px;font-family:sans-serif}
') || TO_CLOB('.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{float:right;background:#f1ecec;height:20px;width:20px;text-align:center;padding:2px;margin-top:-10px;cursor:pointer}
.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#e2dede}
.-S-tR--ff-downloader ._13XQ2--error{color:red;padding:10px;font-size:12px;line-height:19px}
.-S-tR--ff-downloader ._2dFLA--container{position:relative;height:100%}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info{padding:6px 15px 0;font-family:sans-serif}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div{margin-bottom:5px;width:100%;overflow:hidden}
.-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{margin-top:21px;font-size:11px}
.-S-tR--ff-downloader ._10vpG--footer{width:100%;bottom:0;position:absolute;font-weight:700}
.-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader{animation:n0BD1--rotation 3.5s linear forwards;position:absolute;top:-120px;left:calc(50% - 35px);border-radius:50%;border:5px solid #fff;border-top-color:#a29bfe;height:70px;width:70px;display:flex;justify-content:center;align-items:center}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar{width:100%;height:18px;background:#dfe6e9;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar{height:100%;background:#8bc34a;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status{margin-top:10px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state{float:left;font-size:.9em;letter-spacing:1pt;text-transform:uppercase;width:100px;height:20px;position:relative}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage{float:right}
</style>
') || TO_CLOB('<style>
._3emE9--dark-theme .-S-tR--ff-downloader{background:rgba(30,30,30,.93);border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#fff}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{background:#3d4b52}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#131415}
._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer{background:rgba(30,30,30,.93)}
._2mDEx--white-theme .-S-tR--ff-downloader{background:#fff;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#314c75}
._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header{font-weight:700}
._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{border:0;color:rgba(0,0,0,.88)}
._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer{background:#fff}
.-S-tR--ff-downloader{display:block;overflow:hidden;position:fixed;bottom:20px;right:7.1%;width:330px;height:180px;background:rgba(30,30,30,.93);border-radius:2px;color:#fff;z-index:99999999;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);transition:.5s}
.-S-tR--ff-downloader._3M7UQ--minimize{height:62px}
.-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,.-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header{display:none}
.-S-tR--ff-downloader ._6_Mtt--header{padding:10px;font-size:17px;font-family:sans-serif}
') || TO_CLOB('.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{float:right;background:#f1ecec;height:20px;width:20px;text-align:center;padding:2px;margin-top:-10px;cursor:pointer}
.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#e2dede}
.-S-tR--ff-downloader ._13XQ2--error{color:red;padding:10px;font-size:12px;line-height:19px}
.-S-tR--ff-downloader ._2dFLA--container{position:relative;height:100%}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info{padding:6px 15px 0;font-family:sans-serif}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div{margin-bottom:5px;width:100%;overflow:hidden}
.-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{margin-top:21px;font-size:11px}
.-S-tR--ff-downloader ._10vpG--footer{width:100%;bottom:0;position:absolute;font-weight:700}
.-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader{animation:n0BD1--rotation 3.5s linear forwards;position:absolute;top:-120px;left:calc(50% - 35px);border-radius:50%;border:5px solid #fff;border-top-color:#a29bfe;height:70px;width:70px;display:flex;justify-content:center;align-items:center}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar{width:100%;height:18px;background:#dfe6e9;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar{height:100%;background:#8bc34a;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status{margin-top:10px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state{float:left;font-size:.9em;letter-spacing:1pt;text-transform:uppercase;width:100px;height:20px;position:relative}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage{float:right}
</style>
') || TO_CLOB('<style>
._3emE9--dark-theme .-S-tR--ff-downloader{background:rgba(30,30,30,.93);border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#fff}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{background:#3d4b52}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#131415}
._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer{background:rgba(30,30,30,.93)}
._2mDEx--white-theme .-S-tR--ff-downloader{background:#fff;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#314c75}
._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header{font-weight:700}
._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{border:0;color:rgba(0,0,0,.88)}
._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer{background:#fff}
.-S-tR--ff-downloader{display:block;overflow:hidden;position:fixed;bottom:20px;right:7.1%;width:330px;height:180px;background:rgba(30,30,30,.93);border-radius:2px;color:#fff;z-index:99999999;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);transition:.5s}
.-S-tR--ff-downloader._3M7UQ--minimize{height:62px}
.-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,.-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header{display:none}
.-S-tR--ff-downloader ._6_Mtt--header{padding:10px;font-size:17px;font-family:sans-serif}
') || TO_CLOB('.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{float:right;background:#f1ecec;height:20px;width:20px;text-align:center;padding:2px;margin-top:-10px;cursor:pointer}
.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#e2dede}
.-S-tR--ff-downloader ._13XQ2--error{color:red;padding:10px;font-size:12px;line-height:19px}
.-S-tR--ff-downloader ._2dFLA--container{position:relative;height:100%}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info{padding:6px 15px 0;font-family:sans-serif}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div{margin-bottom:5px;width:100%;overflow:hidden}
.-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{margin-top:21px;font-size:11px}
.-S-tR--ff-downloader ._10vpG--footer{width:100%;bottom:0;position:absolute;font-weight:700}
.-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader{animation:n0BD1--rotation 3.5s linear forwards;position:absolute;top:-120px;left:calc(50% - 35px);border-radius:50%;border:5px solid #fff;border-top-color:#a29bfe;height:70px;width:70px;display:flex;justify-content:center;align-items:center}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar{width:100%;height:18px;background:#dfe6e9;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar{height:100%;background:#8bc34a;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status{margin-top:10px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state{float:left;font-size:.9em;letter-spacing:1pt;text-transform:uppercase;width:100px;height:20px;position:relative}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage{float:right}
</style>
') || TO_CLOB('<style>
._3emE9--dark-theme .-S-tR--ff-downloader{background:rgba(30,30,30,.93);border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#fff}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{background:#3d4b52}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#131415}
._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer{background:rgba(30,30,30,.93)}
._2mDEx--white-theme .-S-tR--ff-downloader{background:#fff;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#314c75}
._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header{font-weight:700}
._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{border:0;color:rgba(0,0,0,.88)}
._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer{background:#fff}
.-S-tR--ff-downloader{display:block;overflow:hidden;position:fixed;bottom:20px;right:7.1%;width:330px;height:180px;background:rgba(30,30,30,.93);border-radius:2px;color:#fff;z-index:99999999;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);transition:.5s}
.-S-tR--ff-downloader._3M7UQ--minimize{height:62px}
.-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,.-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header{display:none}
.-S-tR--ff-downloader ._6_Mtt--header{padding:10px;font-size:17px;font-family:sans-serif}
') || TO_CLOB('.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{float:right;background:#f1ecec;height:20px;width:20px;text-align:center;padding:2px;margin-top:-10px;cursor:pointer}
.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#e2dede}
.-S-tR--ff-downloader ._13XQ2--error{color:red;padding:10px;font-size:12px;line-height:19px}
.-S-tR--ff-downloader ._2dFLA--container{position:relative;height:100%}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info{padding:6px 15px 0;font-family:sans-serif}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div{margin-bottom:5px;width:100%;overflow:hidden}
.-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{margin-top:21px;font-size:11px}
.-S-tR--ff-downloader ._10vpG--footer{width:100%;bottom:0;position:absolute;font-weight:700}
.-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader{animation:n0BD1--rotation 3.5s linear forwards;position:absolute;top:-120px;left:calc(50% - 35px);border-radius:50%;border:5px solid #fff;border-top-color:#a29bfe;height:70px;width:70px;display:flex;justify-content:center;align-items:center}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar{width:100%;height:18px;background:#dfe6e9;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar{height:100%;background:#8bc34a;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status{margin-top:10px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state{float:left;font-size:.9em;letter-spacing:1pt;text-transform:uppercase;width:100px;height:20px;position:relative}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage{float:right}
</style>
') || TO_CLOB('</head>
<body style="margin:0;padding:0;background-color:#d4d4d4" topmargin="0" marginwidth="0" marginheight="0" leftmargin="0">
<table class="mailpoet_template" style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody><tr>
<td class="mailpoet_preheader" style="border-collapse:collapse;display:none;visibility:hidden;mso-hide:all;font-size:1px;color:#333333;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;-webkit-text-size-adjust:none" height="1">
</td></tr><tr>
<td class="mailpoet-wrapper" style="border-collapse:collapse;background-color:#d4d4d4" valign="top" align="center">
<table class="mailpoet_content-wrapper" style="border-collapse:collapse;background-color:#000000;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;max-width:660px;width:100%" width="660" cellspacing="0" cellpadding="0" border="0">
<tbody><tr>
<td class="mailpoet_content" style="border-collapse:collapse;background-color:#ffffff!important" bgcolor="#ffffff" align="center">
<table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody><tr>
<td style="border-collapse:collapse;padding-left:0;padding-right:0">
<table class="mailpoet_cols-one" style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0" width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody><tr>
<td class="mailpoet_image " style="border-collapse:collapse" valign="top" align="center">
') || TO_CLOB('<img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0MD9tPm1rOT85ODw4O2g5OGg+MWg8ajk/OzswajtrbWtrPGg5aDFvai99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f8db9c45f7b8669635636e6%2f799x163%2f5b839e3aebc1440edba9fdbf4dafd3e1%2ftoop-mail2.jpg&fmlBlkTk" alt="NETLIFE PLAY" style="height:auto;max-width:100%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%" width="660"></img>
</td></tr></tbody></table></td></tr></tbody></table></td></tr><tr>
<td class="mailpoet_content-cols-three" style="border-collapse:collapse;background-color:  !important;background-color: ;" bgcolor="#ffffff" align="left">
<table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody><tr>
<td style="border-collapse:collapse;font-size:0;background-color: #ff6700;" align="center">
<div style="display:inline-block; max-width:54px; vertical-align:top; width:100%;" wfd-id="2">
<table class="mailpoet_cols-three" style="border-collapse:collapse;width:100%;max-width:54px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0" width="220" cellspacing="0" cellpadding="0" border="0" align="right">
<tbody></tbody></table></div>
<div style="display:inline-block; max-width:552px; vertical-align:top; width:100%;" wfd-id="1">
<table class="mailpoet_cols-three" style="border-collapse:collapse;width:100%;max-width:552px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;background-color: ededed;" width="220" cellspacing="0" cellpadding="0" border="0" align="right">
<tbody><tr>
<td class="mailpoet_image " style="border-collapse:collapse" valign="top" align="center">
') || TO_CLOB('<img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0Pj85bThrP2psbzs9MT8/OzA7bGs8bGhvbDlrOjA4PzBsOjgwbDoxOS99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=http%3a%2f%2fwww.netlife.ec%2fwp-content%2fuploads%2f2021%2f09%2fLOGO_GOLTV_BLACK.png&fmlBlkTk" alt="noggin" style="height:auto;max-width:80%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%" width="220"></img>
</td></tr><tr><td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word" valign="top">
<table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellpadding="0">
<tbody><tr>
<td class="mailpoet_paragraph" style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:center;">
<strong>Hola {{ cliente }}</strong></td></tr></tbody></table></td></tr><tr>
<td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word" valign="top">
<table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellpadding="0">
<tbody><tr>
') || TO_CLOB('<td class="mailpoet_paragraph" style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:justify;">
<strong>NETLIFE</strong> te informa que se ha reenviado satisfactoriamente tu contrase&ntilde;a de acceso para el servicio <strong>GOLTV PLAY</strong>. A continuaci&oacute;n se indicar&aacute; el usuario y contrase&ntilde;a con los cuales podr&aacute;s acceder a la plataforma <strong>GOLTV PLAY</strong> Si existen inconvenientes o novedades con respecto al servicio no dudes en contactarnos por correo electr&oacute;nico a: <strong>soporte@netlife.net.ec.</strong></td>
</tr></tbody></table></td></tr><tr>
<td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word" valign="top">
<h1 style="margin:0 0 9px;font-family:Oswald,&#39;Trebuchet MS&#39;,&#39;Lucida Grande&#39;,&#39;Lucida Sans Unicode&#39;,&#39;Lucida Sans&#39;,Tahoma,sans-serif;font-size:30px;line-height:36px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal"><strong></strong><strong style="color: #F7941D;">DATOS DE ACCESO<br></strong></h1>
</td></tr><tr>
<td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word" valign="top">
<h2 style="margin:0 0 6px;color:#706f6f;font-family:Oswald,&#39;Trebuchet MS&#39;,&#39;Lucida Grande&#39;,&#39;Lucida Sans Unicode&#39;,&#39;Lucida Sans&#39;,Tahoma,sans-serif;font-size:20px;line-height:24px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal"><strong>Usuario: {{ usuario }}<br>Contrase&ntilde;a : {{ contrasenia }}</strong></h2>
</td></tr><tr><td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word" valign="top">
<h3 style="margin:0 0 5.4px;color:875bd8;font-family:Oswald,&#39;Trebuchet MS&#39;,&#39;Lucida Grande&#39;,&#39;Lucida Sans Unicode&#39;,&#39;Lucida Sans&#39;,Tahoma,sans-serif;font-size:18px;line-height:21.6px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal;padding-top: 30px;"><strong></strong><strong style="color: #000000;">ACCEDE A TU SERVICIO HACIENDO <br></strong></h3>
</td></tr><tr>
<td class="mailpoet_image " style="border-collapse:collapse" valign="top" align="center">
') || TO_CLOB('<a href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0OTkxa21va2s8PTw+a287Oj89OThqO2xtOTgxbG1raD0/a2xqOTBtai99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=https%3a%2f%2fwww.netlife.ec%2f"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0aj0+ODlra2g6ajExOmxrPGhvPm07Pjo6Ojs9MThqaGs/OW1taD04aC99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=http%3a%2f%2fwww.netlife.ec%2fwp-content%2fuploads%2f2021%2f09%2fBotonGOLTV.png&fmlBlkTk" alt="CLICK AQUI" style="height:auto;max-width:40%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%" width="220"></img>
</a></td></tr><tr>
<td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word" valign="top">
<table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellpadding="0">
<tbody><tr>
<td class="mailpoet_paragraph" style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:center">
<o:p style="font-size: 14.2px">&ldquo;El Servicio contratado GOLTV PLAY tiene un costo mensual de $9.99 + IVA el mismo que ser&aacute; incluido en su factura mensual del Servicio de Internet. &ldquo;&nbsp;</o:p>
<p> Gracias por ser parte de NETLIFE,<br> EQUIPO NETLIFE &bull; 39-20-000 </p>
</td></tr></tbody></table></td></tr></tbody></table></div>
<div style="display:inline-block; max-width:54px; vertical-align:top; width:100%;" wfd-id="0">
<table class="mailpoet_cols-three" style="border-collapse:collapse;width:100%;max-width:54px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0" width="220" cellspacing="0" cellpadding="0" border="0" align="right">
<tbody></tbody></table></div></td></tr></tbody></table>
<table style="background-color: #000000;" width="660" cellspacing="0" cellpadding="0" border="0">
<tbody><tr><th style="width: 220px" scope="row">
') || TO_CLOB('<a href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0bD5qO2w6MDw9PGxoPD5raztoPD1sMG0/aD86aDtoPz1obGo+ajw+PC99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDo+L2FtZTQ5&url=https%3a%2f%2fwww.facebook.com%2fnetlife.ecuador"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0bDA7MD9oMDAxPmw4aDEwOTBtPjk+PGwxPDg+MT4xazhsb2w6PT44PS99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2f68f99dae7571da4c28f9a63b3c7053b9%2fface.jpg&fmlBlkTk" alt="internet seguro" width="110" height="30	"></img>
</a></th><td style="width: 200px">
<a href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0O2pqMD9qPTg5Pmo+aj88PDBvP2g4OG9qPT5rPzk7bTk+a2hvaDlsai99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDo+L2FtZTQ5&url=https%3a%2f%2ftwitter.com%2fNetlifeEcuador"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0bD07Oj46b284OztrPW0+OGhsOmw7MGo+bTs/Oj0wazxtPj49PTk/bS99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2f4b21d67e4dc5a2a472d96e0c2dcc4cad%2ftwitter.jpg&fmlBlkTk" alt="internet seguro" width="110" height="30"></img>
</a></td><td style="width: 240px">
<a href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0Omg7OG1rOztoMTxobG9vPTE4MG9vPms+bD5vPmgxOTFtaz4+Oz4+MC99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDs8L2FtZTQ5&url=https%3a%2f%2fwww.youtube.com%2fchannel%2fUCnc9ndF9fEDdyeVVJ7j1SDw"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0OmhtOWxsa285bTw/MGhtaGtsamhqPTpqamxobTo4MD08O2g5am1qPi99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2fe1f1a0638ea7001114b231e1738105ee%2fyoutube.jpg&fmlBlkTk" alt="internet seguro" width="110" height="30"></img>
</a></td>
') || TO_CLOB('<td style="width: 200px"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0MTlqPDg4MTA7OjkwP2xoPGswbTE8MDtsPjgxO2o5OD87Pzw9PmprOi99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f262x120%2f64bb2db4cce8226e85e878e1c22b8bad%2ffooter-net2.jpg&fmlBlkTk" alt="internet seguro" width="210" height="87"></img>
</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></body></html>')
);

-- Plan 3 - Correo Restablecer contrasena
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
(
  ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, ESTADO, FE_CREACION,
  USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD, PLANTILLA
)
VALUES 
(
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Goltv reestablecer contrasena','GOLTV-RESCON','TECNICO',
    'Activo',sysdate,'djreyes',null,null,18,
    TO_CLOB('<html style="margin:0;padding:0" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="format-detection" content="telephone=no">
<title>Asunto</title>
<style type="text/css">
@media screen and (max-width: 480px) {.mailpoet_button {width:100% !important;}}
@media screen and (max-width: 599px) {
.mailpoet_header {padding: 10px 20px;}
.mailpoet_button {width: 100% !important;padding: 5px 0 !important;box-sizing:border-box !important;}
div, .mailpoet_cols-two, .mailpoet_cols-three {max-width: 100% !important;}}
</style>
') || TO_CLOB('<style>
._3emE9--dark-theme .-S-tR--ff-downloader{background:rgba(30,30,30,.93);border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#fff}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{background:#3d4b52}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#131415}
._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer{background:rgba(30,30,30,.93)}
._2mDEx--white-theme .-S-tR--ff-downloader{background:#fff;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#314c75}
._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header{font-weight:700}
._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{border:0;color:rgba(0,0,0,.88)}
._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer{background:#fff}
.-S-tR--ff-downloader{display:block;overflow:hidden;position:fixed;bottom:20px;right:7.1%;width:330px;height:180px;background:rgba(30,30,30,.93);border-radius:2px;color:#fff;z-index:99999999;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);transition:.5s}
.-S-tR--ff-downloader._3M7UQ--minimize{height:62px}
.-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,.-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header{display:none}
.-S-tR--ff-downloader ._6_Mtt--header{padding:10px;font-size:17px;font-family:sans-serif}
') || TO_CLOB('.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{float:right;background:#f1ecec;height:20px;width:20px;text-align:center;padding:2px;margin-top:-10px;cursor:pointer}
.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#e2dede}
.-S-tR--ff-downloader ._13XQ2--error{color:red;padding:10px;font-size:12px;line-height:19px}
.-S-tR--ff-downloader ._2dFLA--container{position:relative;height:100%}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info{padding:6px 15px 0;font-family:sans-serif}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div{margin-bottom:5px;width:100%;overflow:hidden}
.-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{margin-top:21px;font-size:11px}
.-S-tR--ff-downloader ._10vpG--footer{width:100%;bottom:0;position:absolute;font-weight:700}
.-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader{animation:n0BD1--rotation 3.5s linear forwards;position:absolute;top:-120px;left:calc(50% - 35px);border-radius:50%;border:5px solid #fff;border-top-color:#a29bfe;height:70px;width:70px;display:flex;justify-content:center;align-items:center}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar{width:100%;height:18px;background:#dfe6e9;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar{height:100%;background:#8bc34a;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status{margin-top:10px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state{float:left;font-size:.9em;letter-spacing:1pt;text-transform:uppercase;width:100px;height:20px;position:relative}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage{float:right}
</style>
') || TO_CLOB('<style>
._3emE9--dark-theme .-S-tR--ff-downloader{background:rgba(30,30,30,.93);border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#fff}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{background:#3d4b52}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#131415}
._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer{background:rgba(30,30,30,.93)}
._2mDEx--white-theme .-S-tR--ff-downloader{background:#fff;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#314c75}
._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header{font-weight:700}
._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{border:0;color:rgba(0,0,0,.88)}
._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer{background:#fff}
.-S-tR--ff-downloader{display:block;overflow:hidden;position:fixed;bottom:20px;right:7.1%;width:330px;height:180px;background:rgba(30,30,30,.93);border-radius:2px;color:#fff;z-index:99999999;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);transition:.5s}
.-S-tR--ff-downloader._3M7UQ--minimize{height:62px}
.-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,.-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header{display:none}
.-S-tR--ff-downloader ._6_Mtt--header{padding:10px;font-size:17px;font-family:sans-serif}
') || TO_CLOB('.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{float:right;background:#f1ecec;height:20px;width:20px;text-align:center;padding:2px;margin-top:-10px;cursor:pointer}
.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#e2dede}
.-S-tR--ff-downloader ._13XQ2--error{color:red;padding:10px;font-size:12px;line-height:19px}
.-S-tR--ff-downloader ._2dFLA--container{position:relative;height:100%}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info{padding:6px 15px 0;font-family:sans-serif}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div{margin-bottom:5px;width:100%;overflow:hidden}
.-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{margin-top:21px;font-size:11px}
.-S-tR--ff-downloader ._10vpG--footer{width:100%;bottom:0;position:absolute;font-weight:700}
.-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader{animation:n0BD1--rotation 3.5s linear forwards;position:absolute;top:-120px;left:calc(50% - 35px);border-radius:50%;border:5px solid #fff;border-top-color:#a29bfe;height:70px;width:70px;display:flex;justify-content:center;align-items:center}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar{width:100%;height:18px;background:#dfe6e9;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar{height:100%;background:#8bc34a;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status{margin-top:10px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state{float:left;font-size:.9em;letter-spacing:1pt;text-transform:uppercase;width:100px;height:20px;position:relative}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage{float:right}
</style>
') || TO_CLOB('<style>
._3emE9--dark-theme .-S-tR--ff-downloader{background:rgba(30,30,30,.93);border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#fff}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{background:#3d4b52}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#131415}
._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer{background:rgba(30,30,30,.93)}
._2mDEx--white-theme .-S-tR--ff-downloader{background:#fff;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#314c75}
._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header{font-weight:700}
._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{border:0;color:rgba(0,0,0,.88)}
._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer{background:#fff}
.-S-tR--ff-downloader{display:block;overflow:hidden;position:fixed;bottom:20px;right:7.1%;width:330px;height:180px;background:rgba(30,30,30,.93);border-radius:2px;color:#fff;z-index:99999999;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);transition:.5s}
.-S-tR--ff-downloader._3M7UQ--minimize{height:62px}
.-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,.-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header{display:none}
.-S-tR--ff-downloader ._6_Mtt--header{padding:10px;font-size:17px;font-family:sans-serif}
') || TO_CLOB('.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{float:right;background:#f1ecec;height:20px;width:20px;text-align:center;padding:2px;margin-top:-10px;cursor:pointer}
.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#e2dede}
.-S-tR--ff-downloader ._13XQ2--error{color:red;padding:10px;font-size:12px;line-height:19px}
.-S-tR--ff-downloader ._2dFLA--container{position:relative;height:100%}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info{padding:6px 15px 0;font-family:sans-serif}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div{margin-bottom:5px;width:100%;overflow:hidden}
.-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{margin-top:21px;font-size:11px}
.-S-tR--ff-downloader ._10vpG--footer{width:100%;bottom:0;position:absolute;font-weight:700}
.-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader{animation:n0BD1--rotation 3.5s linear forwards;position:absolute;top:-120px;left:calc(50% - 35px);border-radius:50%;border:5px solid #fff;border-top-color:#a29bfe;height:70px;width:70px;display:flex;justify-content:center;align-items:center}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar{width:100%;height:18px;background:#dfe6e9;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar{height:100%;background:#8bc34a;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status{margin-top:10px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state{float:left;font-size:.9em;letter-spacing:1pt;text-transform:uppercase;width:100px;height:20px;position:relative}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage{float:right}
</style>
') || TO_CLOB('<style>
._3emE9--dark-theme .-S-tR--ff-downloader{background:rgba(30,30,30,.93);border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#fff}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{background:#3d4b52}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#131415}
._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer{background:rgba(30,30,30,.93)}
._2mDEx--white-theme .-S-tR--ff-downloader{background:#fff;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#314c75}
._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header{font-weight:700}
._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{border:0;color:rgba(0,0,0,.88)}
._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer{background:#fff}
.-S-tR--ff-downloader{display:block;overflow:hidden;position:fixed;bottom:20px;right:7.1%;width:330px;height:180px;background:rgba(30,30,30,.93);border-radius:2px;color:#fff;z-index:99999999;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);transition:.5s}
.-S-tR--ff-downloader._3M7UQ--minimize{height:62px}
.-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,.-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header{display:none}
.-S-tR--ff-downloader ._6_Mtt--header{padding:10px;font-size:17px;font-family:sans-serif}
') || TO_CLOB('.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{float:right;background:#f1ecec;height:20px;width:20px;text-align:center;padding:2px;margin-top:-10px;cursor:pointer}
.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#e2dede}
.-S-tR--ff-downloader ._13XQ2--error{color:red;padding:10px;font-size:12px;line-height:19px}
.-S-tR--ff-downloader ._2dFLA--container{position:relative;height:100%}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info{padding:6px 15px 0;font-family:sans-serif}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div{margin-bottom:5px;width:100%;overflow:hidden}
.-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{margin-top:21px;font-size:11px}
.-S-tR--ff-downloader ._10vpG--footer{width:100%;bottom:0;position:absolute;font-weight:700}
.-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader{animation:n0BD1--rotation 3.5s linear forwards;position:absolute;top:-120px;left:calc(50% - 35px);border-radius:50%;border:5px solid #fff;border-top-color:#a29bfe;height:70px;width:70px;display:flex;justify-content:center;align-items:center}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar{width:100%;height:18px;background:#dfe6e9;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar{height:100%;background:#8bc34a;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status{margin-top:10px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state{float:left;font-size:.9em;letter-spacing:1pt;text-transform:uppercase;width:100px;height:20px;position:relative}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage{float:right}
</style>
') || TO_CLOB('<style>
._3emE9--dark-theme .-S-tR--ff-downloader{background:rgba(30,30,30,.93);border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#fff}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{background:#3d4b52}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#131415}
._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer{background:rgba(30,30,30,.93)}
._2mDEx--white-theme .-S-tR--ff-downloader{background:#fff;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#314c75}
._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header{font-weight:700}
._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{border:0;color:rgba(0,0,0,.88)}
._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer{background:#fff}
.-S-tR--ff-downloader{display:block;overflow:hidden;position:fixed;bottom:20px;right:7.1%;width:330px;height:180px;background:rgba(30,30,30,.93);border-radius:2px;color:#fff;z-index:99999999;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);transition:.5s}
.-S-tR--ff-downloader._3M7UQ--minimize{height:62px}
.-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,.-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header{display:none}
.-S-tR--ff-downloader ._6_Mtt--header{padding:10px;font-size:17px;font-family:sans-serif}
') || TO_CLOB('.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{float:right;background:#f1ecec;height:20px;width:20px;text-align:center;padding:2px;margin-top:-10px;cursor:pointer}
.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#e2dede}
.-S-tR--ff-downloader ._13XQ2--error{color:red;padding:10px;font-size:12px;line-height:19px}
.-S-tR--ff-downloader ._2dFLA--container{position:relative;height:100%}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info{padding:6px 15px 0;font-family:sans-serif}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div{margin-bottom:5px;width:100%;overflow:hidden}
.-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{margin-top:21px;font-size:11px}
.-S-tR--ff-downloader ._10vpG--footer{width:100%;bottom:0;position:absolute;font-weight:700}
.-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader{animation:n0BD1--rotation 3.5s linear forwards;position:absolute;top:-120px;left:calc(50% - 35px);border-radius:50%;border:5px solid #fff;border-top-color:#a29bfe;height:70px;width:70px;display:flex;justify-content:center;align-items:center}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar{width:100%;height:18px;background:#dfe6e9;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar{height:100%;background:#8bc34a;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status{margin-top:10px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state{float:left;font-size:.9em;letter-spacing:1pt;text-transform:uppercase;width:100px;height:20px;position:relative}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage{float:right}
</style>
') || TO_CLOB('<style>
._3emE9--dark-theme .-S-tR--ff-downloader{background:rgba(30,30,30,.93);border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#fff}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{background:#3d4b52}
._3emE9--dark-theme .-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#131415}
._3emE9--dark-theme .-S-tR--ff-downloader ._10vpG--footer{background:rgba(30,30,30,.93)}
._2mDEx--white-theme .-S-tR--ff-downloader{background:#fff;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);color:#314c75}
._2mDEx--white-theme .-S-tR--ff-downloader ._6_Mtt--header{font-weight:700}
._2mDEx--white-theme .-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{border:0;color:rgba(0,0,0,.88)}
._2mDEx--white-theme .-S-tR--ff-downloader ._10vpG--footer{background:#fff}
.-S-tR--ff-downloader{display:block;overflow:hidden;position:fixed;bottom:20px;right:7.1%;width:330px;height:180px;background:rgba(30,30,30,.93);border-radius:2px;color:#fff;z-index:99999999;border:1px solid rgba(82,82,82,.54);box-shadow:0 4px 7px rgba(30,30,30,.55);transition:.5s}
.-S-tR--ff-downloader._3M7UQ--minimize{height:62px}
.-S-tR--ff-downloader._3M7UQ--minimize .nxuu4--file-info,.-S-tR--ff-downloader._3M7UQ--minimize ._6_Mtt--header{display:none}
.-S-tR--ff-downloader ._6_Mtt--header{padding:10px;font-size:17px;font-family:sans-serif}
') || TO_CLOB('.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn{float:right;background:#f1ecec;height:20px;width:20px;text-align:center;padding:2px;margin-top:-10px;cursor:pointer}
.-S-tR--ff-downloader ._6_Mtt--header ._2VdJW--minimize-btn:hover{background:#e2dede}
.-S-tR--ff-downloader ._13XQ2--error{color:red;padding:10px;font-size:12px;line-height:19px}
.-S-tR--ff-downloader ._2dFLA--container{position:relative;height:100%}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info{padding:6px 15px 0;font-family:sans-serif}
.-S-tR--ff-downloader ._2dFLA--container .nxuu4--file-info div{margin-bottom:5px;width:100%;overflow:hidden}
.-S-tR--ff-downloader ._2dFLA--container ._2bWNS--notice{margin-top:21px;font-size:11px}
.-S-tR--ff-downloader ._10vpG--footer{width:100%;bottom:0;position:absolute;font-weight:700}
.-S-tR--ff-downloader ._10vpG--footer ._2V73d--loader{animation:n0BD1--rotation 3.5s linear forwards;position:absolute;top:-120px;left:calc(50% - 35px);border-radius:50%;border:5px solid #fff;border-top-color:#a29bfe;height:70px;width:70px;display:flex;justify-content:center;align-items:center}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar{width:100%;height:18px;background:#dfe6e9;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._24wjw--loading-bar ._1FVu9--progress-bar{height:100%;background:#8bc34a;border-radius:5px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status{margin-top:10px}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1XilH--state{float:left;font-size:.9em;letter-spacing:1pt;text-transform:uppercase;width:100px;height:20px;position:relative}
.-S-tR--ff-downloader ._10vpG--footer ._2KztS--status ._1jiaj--percentage{float:right}
</style>
') || TO_CLOB('</head>
<body style="margin:0;padding:0;background-color:#d4d4d4" topmargin="0" marginwidth="0" marginheight="0" leftmargin="0">
<table class="mailpoet_template" style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody><tr>
<td class="mailpoet_preheader" style="border-collapse:collapse;display:none;visibility:hidden;mso-hide:all;font-size:1px;color:#333333;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;-webkit-text-size-adjust:none" height="1">
</td></tr><tr>
<td class="mailpoet-wrapper" style="border-collapse:collapse;background-color:#d4d4d4" valign="top" align="center">
<table class="mailpoet_content-wrapper" style="border-collapse:collapse;background-color:#000000;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;max-width:660px;width:100%" width="660" cellspacing="0" cellpadding="0" border="0">
<tbody><tr>
<td class="mailpoet_content" style="border-collapse:collapse;background-color:#ffffff!important" bgcolor="#ffffff" align="center">
<table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody><tr>
<td style="border-collapse:collapse;padding-left:0;padding-right:0">
<table class="mailpoet_cols-one" style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0" width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody><tr>
<td class="mailpoet_image " style="border-collapse:collapse" valign="top" align="center">
') || TO_CLOB('<img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0MD9tPm1rOT85ODw4O2g5OGg+MWg8ajk/OzswajtrbWtrPGg5aDFvai99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f8db9c45f7b8669635636e6%2f799x163%2f5b839e3aebc1440edba9fdbf4dafd3e1%2ftoop-mail2.jpg&fmlBlkTk" alt="NETLIFE PLAY" style="height:auto;max-width:100%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%" width="660"></img>
</td></tr></tbody></table></td></tr></tbody></table></td></tr><tr>
<td class="mailpoet_content-cols-three" style="border-collapse:collapse;background-color:  !important;background-color: ;" bgcolor="#ffffff" align="left">
<table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody><tr>
<td style="border-collapse:collapse;font-size:0;background-color: #ff6700;" align="center">
<div style="display:inline-block; max-width:54px; vertical-align:top; width:100%;" wfd-id="2">
<table class="mailpoet_cols-three" style="border-collapse:collapse;width:100%;max-width:54px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0" width="220" cellspacing="0" cellpadding="0" border="0" align="right">
<tbody></tbody></table></div>
<div style="display:inline-block; max-width:552px; vertical-align:top; width:100%;" wfd-id="1">
<table class="mailpoet_cols-three" style="border-collapse:collapse;width:100%;max-width:552px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;background-color: ededed;" width="220" cellspacing="0" cellpadding="0" border="0" align="right">
<tbody><tr>
<td class="mailpoet_image " style="border-collapse:collapse" valign="top" align="center">
') || TO_CLOB('<img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0Pj85bThrP2psbzs9MT8/OzA7bGs8bGhvbDlrOjA4PzBsOjgwbDoxOS99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=http%3a%2f%2fwww.netlife.ec%2fwp-content%2fuploads%2f2021%2f09%2fLOGO_GOLTV_BLACK.png&fmlBlkTk" alt="noggin" style="height:auto;max-width:80%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%" width="220"></img>
</td></tr><tr><td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word" valign="top">
<table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellpadding="0">
<tbody><tr>
<td class="mailpoet_paragraph" style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:center;">
<strong>Hola {{ cliente }}</strong></td></tr></tbody></table></td></tr><tr>
<td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word" valign="top">
<table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellpadding="0">
<tbody><tr>
') || TO_CLOB('<td class="mailpoet_paragraph" style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:justify;">
<strong>NETLIFE</strong> te informa que se ha reestablecido satisfactoriamente tu contrase&ntilde;a de acceso para el servicio <strong>GOLTV PLAY</strong>. A continuaci&oacute;n se indicar&aacute; el usuario y contrase&ntilde;a con los cuales podr&aacute;s acceder a la plataforma <strong>GOLTV PLAY</strong> Si existen inconvenientes o novedades con respecto al servicio no dudes en contactarnos por correo electr&oacute;nico a: <strong>soporte@netlife.net.ec.</strong></td>
</tr></tbody></table></td></tr><tr>
<td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word" valign="top">
<h1 style="margin:0 0 9px;font-family:Oswald,&#39;Trebuchet MS&#39;,&#39;Lucida Grande&#39;,&#39;Lucida Sans Unicode&#39;,&#39;Lucida Sans&#39;,Tahoma,sans-serif;font-size:30px;line-height:36px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal"><strong></strong><strong style="color: #F7941D;">DATOS DE ACCESO<br></strong></h1>
</td></tr><tr>
<td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word" valign="top">
<h2 style="margin:0 0 6px;color:#706f6f;font-family:Oswald,&#39;Trebuchet MS&#39;,&#39;Lucida Grande&#39;,&#39;Lucida Sans Unicode&#39;,&#39;Lucida Sans&#39;,Tahoma,sans-serif;font-size:20px;line-height:24px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal"><strong>Usuario: {{ usuario }}<br>Contrase&ntilde;a : {{ contrasenia }}</strong></h2>
</td></tr><tr><td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word" valign="top">
<h3 style="margin:0 0 5.4px;color:875bd8;font-family:Oswald,&#39;Trebuchet MS&#39;,&#39;Lucida Grande&#39;,&#39;Lucida Sans Unicode&#39;,&#39;Lucida Sans&#39;,Tahoma,sans-serif;font-size:18px;line-height:21.6px;margin-bottom:0;text-align:center;padding:0;font-style:normal;font-weight:normal;padding-top: 30px;"><strong></strong><strong style="color: #000000;">ACCEDE A TU SERVICIO HACIENDO <br></strong></h3>
</td></tr><tr>
<td class="mailpoet_image " style="border-collapse:collapse" valign="top" align="center">
') || TO_CLOB('<a href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0OTkxa21va2s8PTw+a287Oj89OThqO2xtOTgxbG1raD0/a2xqOTBtai99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=https%3a%2f%2fwww.netlife.ec%2f"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0aj0+ODlra2g6ajExOmxrPGhvPm07Pjo6Ojs9MThqaGs/OW1taD04aC99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=http%3a%2f%2fwww.netlife.ec%2fwp-content%2fuploads%2f2021%2f09%2fBotonGOLTV.png&fmlBlkTk" alt="CLICK AQUI" style="height:auto;max-width:40%;-ms-interpolation-mode:bicubic;border:0;display:block;outline:none;text-align:center;width:100%" width="220"></img>
</a></td></tr><tr>
<td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" style="border-collapse:collapse;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;word-break:break-word;word-wrap:break-word" valign="top">
<table style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0" width="100%" cellpadding="0">
<tbody><tr>
<td class="mailpoet_paragraph" style="border-collapse:collapse;color:#706f6f;font-family:lato,&#39;helvetica neue&#39;,helvetica,arial,sans-serif;font-size:16px;line-height:22.4px;word-break:break-word;word-wrap:break-word;text-align:center">
<o:p style="font-size: 14.2px">&ldquo;El Servicio contratado GOLTV PLAY tiene un costo mensual de $9.99 + IVA el mismo que ser&aacute; incluido en su factura mensual del Servicio de Internet. &ldquo;&nbsp;</o:p>
<p> Gracias por ser parte de NETLIFE,<br> EQUIPO NETLIFE &bull; 39-20-000 </p>
</td></tr></tbody></table></td></tr></tbody></table></div>
<div style="display:inline-block; max-width:54px; vertical-align:top; width:100%;" wfd-id="0">
<table class="mailpoet_cols-three" style="border-collapse:collapse;width:100%;max-width:54px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0" width="220" cellspacing="0" cellpadding="0" border="0" align="right">
<tbody></tbody></table></div></td></tr></tbody></table>
<table style="background-color: #000000;" width="660" cellspacing="0" cellpadding="0" border="0">
<tbody><tr><th style="width: 220px" scope="row">
') || TO_CLOB('<a href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0bD5qO2w6MDw9PGxoPD5raztoPD1sMG0/aD86aDtoPz1obGo+ajw+PC99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDo+L2FtZTQ5&url=https%3a%2f%2fwww.facebook.com%2fnetlife.ecuador"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0bDA7MD9oMDAxPmw4aDEwOTBtPjk+PGwxPDg+MT4xazhsb2w6PT44PS99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2f68f99dae7571da4c28f9a63b3c7053b9%2fface.jpg&fmlBlkTk" alt="internet seguro" width="110" height="30	"></img>
</a></th><td style="width: 200px">
<a href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0O2pqMD9qPTg5Pmo+aj88PDBvP2g4OG9qPT5rPzk7bTk+a2hvaDlsai99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDo+L2FtZTQ5&url=https%3a%2f%2ftwitter.com%2fNetlifeEcuador"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0bD07Oj46b284OztrPW0+OGhsOmw7MGo+bTs/Oj0wazxtPj49PTk/bS99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2f4b21d67e4dc5a2a472d96e0c2dcc4cad%2ftwitter.jpg&fmlBlkTk" alt="internet seguro" width="110" height="30"></img>
</a></td><td style="width: 240px">
<a href="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0Omg7OG1rOztoMTxobG9vPTE4MG9vPms+bD5vPmgxOTFtaz4+Oz4+MC99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDs8L2FtZTQ5&url=https%3a%2f%2fwww.youtube.com%2fchannel%2fUCnc9ndF9fEDdyeVVJ7j1SDw"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0OmhtOWxsa285bTw/MGhtaGtsamhqPTpqamxobTo4MD08O2g5am1qPi99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f158x44%2fe1f1a0638ea7001114b231e1738105ee%2fyoutube.jpg&fmlBlkTk" alt="internet seguro" width="110" height="30"></img>
</a></td>
') || TO_CLOB('<td style="width: 200px"><img src="https://fm.telconet.net/fmlurlsvc/?fewReq=:B:JVUzOD0wMy9/NDsnOS9gbTQ5ODM5OC96YG5naH18e2w0MTlqPDg4MTA7OjkwP2xoPGswbTE8MDtsPjgxO2o5OD87Pzw9PmprOi99NDg/OjgxODs/Pj4veGBtNDgxTkFBf05/OTs4PTg9JDgxTkFBf05xOTs4PTg9L3tqeX00b2tse2RsZkl9bGVqZmdsfSdsai9qNDw7L2FtZTQ5&url=https%3a%2f%2ftrello-attachments.s3.amazonaws.com%2f5f0e1c290c58972b72a372db%2f262x120%2f64bb2db4cce8226e85e878e1c22b8bad%2ffooter-net2.jpg&fmlBlkTk" alt="internet seguro" width="210" height="87"></img>
</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></body></html>')
);

-- Plan 4 - SMS Nuevo
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
(
  ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO,
  FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD
)
VALUES 
(
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Goltv nuevo servicio sms','SMS_GOLTV_NUE','TECNICO',
    'Estimado/a Cliente, Netlife le informa que su cuenta NETLIFEPLAY / GOLTV ha sido activada. Usuario: {{USUARIO}} Contrasena: {{CONTRASENIA}}, empieza la diversion aqui: https://play.goltv.tv/',
    'Activo',sysdate,'djreyes',null,null,18
);

-- Plan 5 - SMS Restablecer clave
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
(
  ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO,
  FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD
)
VALUES 
(
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Goltv restablecer clave sms','SMS_GOLTV_RES','TECNICO',
    'Estimado/a Cliente, Netlife le informa que se ha actualizado su contrasena del Servicio NETLIFEPLAY / GOLTV Usuario: {{USUARIO}} Contrasena: {{CONTRASENIA}}, empieza la diversion aqui: https://play.goltv.tv/',
    'Activo',sysdate,'djreyes',null,null,18
);

-- Plan 6 - SMS Reenviar clave
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
(
  ID_PLANTILLA, NOMBRE_PLANTILLA, CODIGO, MODULO, PLANTILLA, ESTADO,
  FE_CREACION, USR_CREACION, FE_ULT_MOD, USR_ULT_MOD, EMPRESA_COD
)
VALUES 
(
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Goltv reenviar clave sms','SMS_GOLTV_REN','TECNICO',
    'Estimado/a Cliente, Netlife le informa que sus credenciales NETLIFEPLAY / GOLTV son Usuario: {{USUARIO}} Contrasena: {{CONTRASENIA}}, empieza la diversion aqui: https://play.goltv.tv/',
    'Activo',sysdate,'djreyes',null,null,18
)

COMMIT;
/
