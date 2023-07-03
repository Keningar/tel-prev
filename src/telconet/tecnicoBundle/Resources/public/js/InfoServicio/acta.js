Ext.onReady(function() {    
    Ext.tip.QuickTipManager.init();
    
    if(mensaje != '')
    {	  
	  Ext.Msg.alert('Mensaje',mensaje, function(btn){
	      if(btn == 'ok' && mensaje == 'Acta procesada correctamente')
	      {
		    window.location = pathServicios;     
	      }
		  
	  });
    }       
    
});

/*
 * Función que valida que se hayan contestado todas las preguntas mostradas.
 * y crea un String en relacion de las preguntas con sus respuestas.
 * 
 * @author Creado: Ronny Morán <rmoranc@telconet.ec>
 * @version 1.0 24-04-2019
 * @returns {Boolean}
 * 
 * Se agrega un orden descendente para evitar 
 * 
 * @autor Creado: Wilmer Vera <wveera@telconet.ec>
 * @version 1.1 30-09-2021
 * @returns {Boolean}
 */
function validarActa()
{
    
    var ids                 = document.getElementById('ids').value;                        
    var idsArray            = ids.split("-");
    var preguntaRespuesta   = '';
    var flag                = 0; 
    idsArray.sort(function(a, b){return b-a});
    for(let i = 0; i<idsArray.length-1; i++)
    {
        var elementosTipo   = document.getElementsByName(idsArray[i])[0];
        var elementos       = document.getElementsByName(idsArray[i]);
        preguntaRespuesta   = preguntaRespuesta + idsArray[i]+"-";
        
	if(elementosTipo.type === 'checkbox')
        {
            var checkboxChk = -1;
            for(var j = 0; j < elementos.length ; j++)
            {
                if(elementos[j].checked)
                {
                    checkboxChk         = elementos[j].value;
                    preguntaRespuesta   = preguntaRespuesta + checkboxChk + "-";
                }
            }
            
            if(checkboxChk == -1)
            {
                flag = 1;                
            }
        }
	else if(elementosTipo.type === 'radio')
        {
            var radioChk = -1;
            for(var k = 0; k < elementos.length ; k++)
            {
                if(elementos[k].checked)
                {
                    radioChk            = elementos[k].value;
                    preguntaRespuesta   = preguntaRespuesta + radioChk + "|";
                }
            }
            
            if(radioChk == -1)
            {
                flag = 1;
                break;
            }
        }
        else if(elementosTipo.type === 'textarea')
        {
            var textVal         = elementosTipo.value;
	    preguntaRespuesta   = preguntaRespuesta + textVal + "|";
        }
    }
    
    if(flag === 1)
    {
        Ext.Msg.alert('Mensaje',"Falta llenar datos en el acta, favor revisar!");
        return false;
    }else
    {
        document.getElementById('preguntaRespuesta').value = preguntaRespuesta;               
	Ext.MessageBox.wait("Procesando Acta...");
 	document.forms[0].submit();
	return true;      
    }
    
}          