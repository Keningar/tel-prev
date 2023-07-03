/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function validador(e,tipo) {      
  
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
       
    if(tipo=='numeros'){    
      letras = "0123456789";
      especiales = [8,9,45,46];
    }else if(tipo=='letras'){
      letras = "abcdefghijklmnopqrstuvwxyz";
      especiales = [8,36,35,45,47,40,41,46,32,37,39];
    }
    
    //46 => .    
    //32 => ' ' espacio
    //8 => backspace       
    //37 => direccional izq
    //39 => direccional der y '
    //44 => ,

    tecla_especial = false
    for(var i in especiales) {
        if(key == especiales[i]) {
            tecla_especial = true;
            break;
        }
    }                

    if(letras.indexOf(tecla) == -1 && !tecla_especial)   
        return false;
}

function validar()
{
    color  = Ext.get('telconet_schemabundle_admihilotype_colorHilo').dom.value;
    numero = Ext.get('telconet_schemabundle_admihilotype_numeroHilo').dom.value;
    
    if(color==="" || numero==="")
    {
        alert("Falta llenar unos campos, favor revisar!");
        return false;
    }
    
    return true;
}