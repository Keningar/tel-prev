/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function validador(e,tipo) {      
  
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
       
    console.log(key);
    if(tipo=='numeros'){    
      letras = "0123456789";
      especiales = [45,46];
    }else if(tipo=='letras'){
      letras = "abcdefghijklmnopqrstuvwxyz";
      especiales = [8,36,35,45,47,40,41,46,32,37,39];
    }
    else if(tipo=='ip'){    
      letras = "0123456789";
      especiales = [8,46];
    }
    else{ 
      letras = "abcdefghijklmnopqrstuvwxyz0123456789";
      especiales = [8,36,35,45,47,40,41,46];
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

function validacionesForm(){
    //validar nombre caja
    if(document.getElementById("telconet_schemabundle_infoelementodslamtype_nombreElemento").value==""){
        alert("Falta llenar algunos campos");
        return false;
    }
    
    //validar ip
    if(document.getElementById("telconet_schemabundle_infoelementodslamtype_ipElemento").value==""){
        alert("Falta llenar algunos campos");
        return false;
    }
    
    //validar modelo
    if(document.getElementById("telconet_schemabundle_infoelementodslamtype_modeloElementoId").value==""){
        alert("Falta llenar algunos campos");
        return false;
    }
    
    //validar nodo
    if(document.getElementById("telconet_schemabundle_infoelementodslamtype_popElementoId").value==""){
        alert("Falta llenar algunos campos");
        return false;
    }
    return true;
}

function presentarCantones(objeto_1, objeto_2, accion, root, seleccion)
{
 var conn = new Ext.data.Connection();
 conn.request
      (
        {
          url: '../../../administracion/general/admi_canton/'+accion,
          method: 'post',
          params: {idJurisdiccion : objeto_1.value},
          success: function(response)
                   {
                     var json = Ext.JSON.decode(response.responseText);
                     if(root == 'encontrados')
                     {
                       llenarCombo(objeto_2, json.encontrados, 'encontrados', seleccion, 'nombre_canton', 'id_canton');
                     }
                   },
          failure: function(result)
                   {
                     Ext.Msg.alert('Error ','Error: ' + result.statusText);
                   }
        }
      );
}

function presentarParroquias(objeto_1, objeto_2, accion, root, seleccion)
{
 var conn = new Ext.data.Connection();
 conn.request
      (
        {
          url: '../../../administracion/general/admi_parroquia/'+accion,
          method: 'post',
          params: {idCanton : objeto_1.value},
          success: function(response)
                   {
                     var json = Ext.JSON.decode(response.responseText);
                     if(root == 'encontrados')
                     {
                       llenarCombo(objeto_2, json.encontrados, 'encontrados', seleccion, 'nombre_parroquia', 'id_parroquia');
                     }
                   },
          failure: function(result)
                   {
                     Ext.Msg.alert('Error ','Error: ' + result.statusText);
                   }
        }
      );
}

function presentarInterfaces(objeto_1, objeto_2, accion, root, seleccion)
{
 var conn = new Ext.data.Connection();
 conn.request
      (
        {
          url: '../../../tecnico/elemento/dslam/'+accion,
          method: 'post',
          params: {idElemento : objeto_1.value},
          success: function(response)
                   {
                     var json = Ext.JSON.decode(response.responseText);
                     if(root == 'encontrados')
                     {
                       llenarCombo(objeto_2, json.encontrados, 'encontrados', seleccion, 'nombreInterfaceElemento', 'idInterface');
                     }
                   },
          failure: function(result)
                   {
                     Ext.Msg.alert('Error ','Error: ' + result.statusText);
                   }
        }
      );
}

function llenarCombo(name_id_combo, objetos, tag, value_option_selected, valor, id)
{
 var combo_el = Ext.get(name_id_combo);
 var combo = Ext.getDom(name_id_combo); //combo_el.dom;
 var size_combo = combo.length;

 while (combo.length > 0)
 {
   combo.removeChild(combo.firstChild);
 }

 try
 {
   combo.add(new Option('-- Seleccione --','0'), null);
 }
 catch(e)
 { //in IE
   combo.add(new Option('-- Seleccione --', '0'));
 }

 for(var i=0 ;  i < objetos.length ; ++i)
 {
   try
   {
     combo.add(new Option(objetos[i][valor], objetos[i][id]), null);
   }
   catch(e)
   { //in IE
     combo.add(new Option(objetos[i][valor], objetos[i][id]));
   }
 }
 if(value_option_selected)
 {
   var el_option = combo_el.query('option[value='+value_option_selected+']');

   el_option = Ext.get(el_option[0]);
   el_option.dom.selected=true;
 }
}