--Actualizando Plantillas ENC-INST-TN
SET SERVEROUTPUT ON 200000;
declare
    plantillaHtml
 clob:='<!DOCTYPE html>';
begin

DBMS_LOB.APPEND(plantillaHtml, 
'
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="author" content="Daniela Cardenas" />
  <meta name="update" content="Wilmer Vera" />
  <meta name="company" content="Telconet SA" />
  <style type="text/css">
      html { font-family:Calibri, Arial, Helvetica, sans-serif; font-size:11pt; background-color:white }
      table { border-collapse:collapse; page-break-after:always }
      .gridlines td { border:1px dotted black }
      .gridlines th { border:1px dotted black }
      .b { text-align:center }
      .e { text-align:center }
      .f { text-align:right }
      .inlineStr { text-align:left }
      .n { text-align:right }
      .s { text-align:left }
      td.style0 { vertical-align:bottom; border-bottom:none #000000; border-top:none #000000; border-left:none #000000; border-right:none #000000; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }

      th.style0 { vertical-align:bottom; border-bottom:none #000000; border-top:none #000000; border-left:none #000000; border-right:none #000000; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style1 { vertical-align:middle; text-align:left; padding-right:0px; padding-left: 5px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style1 { vertical-align:middle; text-align:right; padding-right:0px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style2 { vertical-align:bottom; text-align:left; padding-right:0px; border-bottom:1px solid #000000 !important; padding-left: 5px; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:12pt; background-color:white, align:center; }
      th.style2 { vertical-align:bottom; text-align:right; padding-right:0px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style3 { vertical-align:bottom; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }

      td.style4 { vertical-align:middle; text-align:left; padding-left:5px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style4 { vertical-align:middle; text-align:left; padding-left:0px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style5 { vertical-align:middle; text-align:left; padding-right:0px; padding-left: 5px; border-bottom:none #000000; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style5 { vertical-align:middle; text-align:right; padding-right:0px; border-bottom:none #000000; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style6 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style6 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }

      td.style7 { vertical-align:middle; text-align:left; padding-left:0px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style7 { vertical-align:middle; text-align:left; padding-left:0px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style8 { vertical-align:middle; text-align:left; padding-left:0px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style8 { vertical-align:middle; text-align:left; padding-left:0px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style9 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:12pt; background-color:white }
      th.style9 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:12pt; background-color:white }
      td.style10 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:12pt; background-color:white }

      th.style10 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:12pt; background-color:white }
      td.style11 { vertical-align:bottom; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style11 { vertical-align:bottom; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style12 { vertical-align:middle; text-align:left; padding-left:5px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style12 { vertical-align:middle; text-align:left; padding-left:5px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style13 { vertical-align:middle; text-align:left; padding-left:5px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style13 { vertical-align:middle; text-align:left; padding-left:0px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      
      td.style14 { vertical-align:middle; text-align:left; padding-left:0px; padding-left: 5px; padding-top: 5px;padding-bottom: 5px;border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:13pt; background-color:#FFFFFF }
      th.style14 { vertical-align:middle; text-align:left; padding-left:0px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; padding-top: 5px;padding-bottom: 5px; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:13pt; background-color:#FFFFFF }
      
      td.style15 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:14pt; background-color:#D8D8D8 }
      th.style15 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:14pt; background-color:#D8D8D8 }
      td.style16 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:14pt; background-color:#D8D8D8 }

      th.style16 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:14pt; background-color:#D8D8D8 }
      td.style17 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:14pt; background-color:#D8D8D8 }
      th.style17 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:14pt; background-color:#D8D8D8 }
      td.style18 { vertical-align:middle; text-align:left; padding-left:0px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:#FFFFFF }
      th.style18 { vertical-align:middle; text-align:left; padding-left:0px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:#FFFFFF }
      td.style19 { vertical-align:middle; text-align:left; padding-left:0px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:#FFFFFF }
      th.style19 { vertical-align:middle; text-align:left; padding-left:0px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:#FFFFFF }

      td.style20 { vertical-align:middle; text-align:left; padding-left:5px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:#FFFFFF }
      th.style20 { vertical-align:middle; text-align:left; padding-left:0px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:#FFFFFF }
      td.style21 { vertical-align:middle; text-align:left; padding-left:5px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:#FFFFFF }
      th.style21 { vertical-align:middle; text-align:left; padding-left:0px; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:#FFFFFF }
      td.style22 { vertical-align:middle; text-align:center; border-bottom:none #000000; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style22 { vertical-align:middle; text-align:center; border-bottom:none #000000; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style23 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:none #000000; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style23 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:none #000000; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style24 { vertical-align:middle; text-align:center; border-bottom:none #000000; border-top:none #000000; border-left:1px solid #000000 !important; border-right:none #000000; font-weight:bold; color:#1F497D; font-family:Calibri; font-size:14pt; background-color:white }
      th.style24 { vertical-align:middle; text-align:center; border-bottom:none #000000; border-top:none #000000; border-left:1px solid #000000 !important; border-right:none #000000; font-weight:bold; color:#1F497D; font-family:Calibri; font-size:14pt; background-color:white }

      td.style25 { vertical-align:middle; text-align:center; border-bottom:none #000000; border-top:none #000000; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#1F497D; font-family:Calibri; font-size:14pt; background-color:white }
      th.style25 { vertical-align:middle; text-align:center; border-bottom:none #000000; border-top:none #000000; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#1F497D; font-family:Calibri; font-size:14pt; background-color:white }
      td.style26 { vertical-align:middle; text-align:center; border-bottom:none #000000; border-top:1px solid #000000; border-left:none #000000; border-right:1px solid #000000 !important; font-weight:bold; color:#1F497D; font-family:Calibri; font-size:13pt; background-color:white }
      th.style26 { vertical-align:middle; text-align:center; border-bottom:none #000000; border-top:none #000000; border-left:none #000000; border-right:1px solid #000000 !important; font-weight:bold; color:#1F497D; font-family:Calibri; font-size:13pt; background-color:white }
      td.style27 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:none #000000; border-left:1px solid #000000 !important; border-right:none #000000; font-weight:bold; color:#1F497D; font-family:Calibri; font-size:14pt; background-color:white }
      th.style27 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:none #000000; border-left:1px solid #000000 !important; border-right:none #000000; font-weight:bold; color:#1F497D; font-family:Calibri; font-size:14pt; background-color:white }
      td.style28 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:none #000000; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#1F497D; font-family:Calibri; font-size:14pt; background-color:white }
      th.style28 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:none #000000; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#1F497D; font-family:Calibri; font-size:14pt; background-color:white }
      td.style29 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:none #000000; border-left:none #000000; border-right:1px solid #000000 !important; font-weight:bold; color:#1F497D; font-family:Calibri; font-size:14pt; background-color:white }
      th.style29 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:none #000000; border-left:none #000000; border-right:1px solid #000000 !important; font-weight:bold; color:#1F497D; font-family:Calibri; font-size:14pt; background-color:white }
      td.style30 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:none #000000; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style30 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:none #000000; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }

      td.style31 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style31 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style32 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:1px solid #000000 !important; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style32 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:1px solid #000000 !important; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style33 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style33 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style34 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style34 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style35 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }

      th.style35 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style36 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style36 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style37 { vertical-align:bottom; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:none #000000; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style37 { vertical-align:bottom; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:none #000000; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style38 { vertical-align:bottom; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style38 { vertical-align:bottom; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style39 { vertical-align:bottom; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:1px solid #000000 !important; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style39 { vertical-align:bottom; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:1px solid #000000 !important; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style40 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:12pt; background-color:white }
      th.style40 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:12pt; background-color:white }

      td.style41 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:12pt; background-color:white }
      th.style41 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:12pt; background-color:white }
      td.style42 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:12pt; background-color:white }
      th.style42 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:12pt; background-color:white }
      td.style43 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:12pt; background-color:white }
      th.style43 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:12pt; background-color:white }
      td.style44 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:12pt; background-color:white }
      th.style44 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:12pt; background-color:white }
      td.style45 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:12pt; background-color:white }

      th.style45 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:12pt; background-color:white }
      td.style46 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style46 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      td.style47 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      th.style47 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:none #000000; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:Calibri; font-size:11pt; background-color:white }
      table.sheet0 col.col0 { width:135.555554pt }
      table.sheet0 col.col1 { width:42pt }
      table.sheet0 col.col2 { width:62.35555484pt }
      table.sheet0 col.col3 { width:62.35555484pt }
      table.sheet0 col.col4 { width:61.67777707pt }
      table.sheet0 col.col5 { width:76.58888801pt }
      table.sheet0 col.col6 { width:42pt }
      table.sheet0 col.col7 { width:91.49999895pt }

      table.sheet0 tr { height:15pt }
      table.sheet0 tr.row0 { height:15pt }
      table.sheet0 tr.row1 { height:43.5pt }
      table.sheet0 tr.row2 { height:18.75pt }
      table.sheet0 tr.row10 { height:18.75pt }
      table.sheet0 tr.row15 { height:21.75pt }
      table.sheet0 tr.row16 { height:18.75pt }
      table.sheet0 tr.row17 { height:36pt }
      table.sheet0 tr.row18 { height:30pt }
      table.sheet0 tr.row19 { height:32.25pt }
      table.sheet0 tr.row20 { height:24pt }
      table.sheet0 tr.row21 { height:24pt }
      table.sheet0 tr.row22 { height:33.75pt }
      table.sheet0 tr.row23 { height:30.75pt }
      table.sheet0 tr.row24 { height:30.75pt }
      table.sheet0 tr.row25 { height:27pt }
      table.sheet0 tr.row26 { height:30.75pt }
      table.sheet0 tr.row27 { height:21.75pt }
      table.sheet0 tr.row28 { height:27pt }
      table.sheet0 tr.row29 { height:24.75pt }
      table.sheet0 tr.row30 { height:21.75pt }
      table.sheet0 tr.row31 { height:15.75pt }
      table.sheet0 tr.row32 { height:27pt }
      table.sheet0 tr.row33 { height:15.75pt }
  </style>
</head>

<body>
    <style>
        @page { margin-left: 0.7in; margin-right: 0.7in; margin-top: 0.75in; margin-bottom: 0.75in; }
        body { margin-left: 0.7in; margin-right: 0.7in; margin-top: 0.75in; margin-bottom: 0.75in; }
    </style>

    <table border="0" cellpadding="0" cellspacing="0" id="sheet0" class="sheet0 gridlines">
        <col class="col0">
        <col class="col1">
        <col class="col2">
        <col class="col3">
        <col class="col4">
        <col class="col5">
        <col class="col6">
        <col class="col7">
        <tbody>
          <tr class="row0">
            <td class="column0 style11 null style11">
                <div><img width= 110 height= 60 src={{imagenCabecera}} border="0" /></div></td>
                <td class="column1 style24 s style26" colspan="6">ENCUESTA DE CALIDAD DE INSTALACIÓN DE CLIENTE
CORPORATIVO</td>
                <td class="column7 style22 s style22">CÓDIGO: FOR GC 13<br />
                    Ver: 02 (12 07 2021)</td>
                </tr>
                <tr class="row2">
                    <td class="column0 style15 s style17" colspan="8">DATOS GENERALES</td>
                </tr>
                <tr class="row3">
                    <td class="column0 style2 s" colspan="2">Razón Social:</td>
                    <td align="center" class="column1 style37 null style39" colspan="7">{{ datoGeneral.strRazonSocial }}</td>
                </tr>
                 <tr class="row3">
                    <td class="column0 style2 s" colspan="2">Nombre del Contacto en Sitio:</td>
                    <td align="center" class="column1 style37 null style39" colspan="7">{{ strNombreContactoSitio }}</td>
                </tr>
                 <tr class="row3">
                    <td class="column0 style2 s" colspan="2">Número del Contacto en Sitio:</td>
                    <td align="center" class="column1 style37 null style39" colspan="7">{{ strNumeroContactoSitio }}</td>
                </tr>
                 <tr class="row3">
                    <td class="column0 style2 s" colspan="2">Cuadrilla Instalación:</td>
                    <td align="center" class="column1 style37 null style39" colspan="7">{{ strNombreCuadrilla }}</td>
                </tr>
                 <tr class="row3">
                    <td class="column0 style2 s" colspan="2">Jefe de Cuadrilla:</td>
                    <td align="center" class="column1 style37 null style39" colspan="7">{{ strJefeCuadrilla }}</td>
                </tr>
                 <tr class="row3">
                    <td class="column0 style2 s" colspan="2">Integrantes Cuadrilla:</td>
                    <td align="center" class="column1 style37 null style39" colspan="7">{{ strIntegrantesCuadrilla }}</td>
                </tr>
                <tr class="row9">
                    <td class="column0 style2 s" colspan="2">Fecha y hora Encuesta:</td>
                    <td align="center" class="column1 style11 null style11" colspan="7">{{ datoGeneral.fechaEncuesta }}</td>
                </tr>
                <tr class="row10">
                    <td class="column0 style15 s style17" colspan="8">DETALLES DEL SERVICIO</td>
                </tr>
                
                <tr class="row11">
                    <td class="column0 style2 s" colspan="2">Login:</td>
                    <td align="center" class="column1 style11 null style11" colspan="7">{{ detalleCliente.strLogin }}</td>
                </tr>
                <tr class="row12">
                    <td class="column0 style2 s" colspan="2">Provincia:</td>
                    <td align="center" class="column1 style11 null style11" colspan="7">{{ detalleCliente.strProvincia }}</td>
                </tr>
                <tr class="row13">
                    <td class="column0 style2 s" colspan="2">Ciudad:</td>
                    <td align="center" class="column1 style11 null style11" colspan="7">{{ detalleCliente.strCanton }}</td>
                </tr>
                <tr class="row14">
                    <td class="column0 style5 s" colspan="2">Tipo Enlace Última Milla:</td>
                    <td align="center" class="column1 style11 null style11" colspan="7">{{ detalleCliente.strUltimaMill }}</td>
                </tr>
                 <tr class="row14">
                    <td class="column0 style5 s" colspan="2">Fecha subcripción del contrato:</td>
                    <td align="center" class="column1 style11 null style11" colspan="7">{{ strFechaContrato }}</td>
                </tr>
                <tr class="row16">
                    <td class="column0 style15 s style17" colspan="8">PREGUNTAS</td>
                </tr>


                {% for pregunta in cuerpo %}
                      <tr class="row17">
                          <td class="column0 style14 s style14" colspan="3">{% autoescape %}{{ pregunta.pregunta|raw }}{% endautoescape %}</td>
                          <td class="column3 style6 s" colspan="7">{{ pregunta.respuesta }}</td>
                      </tr>
                {%endfor%}

                <tr class="row32">
                    <td class="column0 style4 s" colspan="3" >Firma del contacto en sitio.</td>
                    <td align="center" class="column1 style46 null style47" colspan="7"><img width="200" height="150" src="{{firmaCliente}}" alt="firma" width="75%"/></td>
                </tr>
                <tr class="row33">
                    <td class="column0 style40 s style42" colspan="8">“Agradecemos sinceramente su esfuerzo y colaboración” </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
'
);

dbms_output.put_line('The length of the manipulated LOB is '||dbms_lob.getlength(plantillaHtml));

UPDATE 
	DB_COMUNICACION.ADMI_PLANTILLA 
SET 
	PLANTILLA = plantillaHtml,
    USR_ULT_MOD = 'wvera'
where CODIGO = 'ENC-INST-TN';
commit;
end;
/
