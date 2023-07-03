Ext.onReady(function() {    
    Ext.tip.QuickTipManager.init();
    
    if(mensaje != '')
    {	  
	  Ext.Msg.alert('Mensaje',mensaje, function(btn){
	      if(btn=='ok' && mensaje == 'Encuesta procesada correctamente')
	      {
		    Ext.MessageBox.wait("Esperando Respuesta");	     
		    window.location = pathServicios;
	      }
		  
	  });
    }       
    
    $('.sigPad').signaturePad({drawOnly:true});
});

/*
 * Funcion que valida que se hayan contestado todas las preguntas
 * y crea un String de relacion de preguntas con sus respuestas.
 * 
 * @author Creado: Allan Suarez <arsuarez@telconet.ec>
 * @author Creado: Francisco Adum <fadum@telconet.ec>
 * @version 1.0 28-07-2014
 * @returns {Boolean}
 */
function guardarFirma()
{
    var firma = $('.sigPad').signaturePad({drawOnly:true}).getSignatureString();
    document.getElementById('firma').value = firma;

    ids = document.getElementById('ids').value;                        

    idsArray = ids.split("-");

    var preguntaRespuesta = '';
    
    flag=0; 
    for(i=0;i<idsArray.length-1;i++)
    {
        elementosTipo = document.getElementsByName(idsArray[i])[0];
        elementos = document.getElementsByName(idsArray[i]);
        preguntaRespuesta = preguntaRespuesta + idsArray[i]+"-";
        if(elementosTipo.type == 'radio')
        {
            var radioChk = -1;
            for(var j=0; j< elementos.length ; j++)
            {
                if(elementos[j].checked)
                {
                    radioChk = elementos[j].value;
                    preguntaRespuesta = preguntaRespuesta + radioChk + "|";
                }
            }
            if(radioChk==-1)
            {
                flag=1;
                break;
            }
        }
        else
        {
            textVal = elementosTipo.value;
//             if(textVal=="")
//             {
//                 flag=1;
//                 break;
//             }
            preguntaRespuesta = preguntaRespuesta + textVal + "|";
        }
    }    
      
    if(flag==1)
    {
        alert("Falta llenar datos en la encuesta, favor revisar!");
        return false;
    }
    
    if(firma=="[]")
    {
        alert("Favor firmar la encuesta, Gracias!");
        return false;
    }       
    
    
    
    if(flag!=1 && firma !="[]")
    {       
	document.getElementById('preguntaRespuesta').value = preguntaRespuesta;               
	Ext.MessageBox.wait("Procesando Encuesta...");
 	document.forms[0].submit();
	return true;      
    }else{return false;}
    
    
}          