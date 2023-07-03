Ext.onReady(function(){ 
    
    var Url = location.href; 
//    alert(Url);
    UrlUrl = Url.replace(/.*\?(.*?)/,"$1");  
    Variables = Url.split ("/");
    var n = Variables.length;
//    alert(Variables[n-1]);
    var idPop = Variables[n-2];
    
//    alert(idDslam);
    
    var accion="cargarDatosPop";
    var conn = new Ext.data.Connection();
    conn.request
      (
        {
          url: ''+accion,
          method: 'post',
          params: {idPop : idPop},
          success: function(response)
                   {
                     var json = Ext.JSON.decode(response.responseText);
                     
                     if(json.total>0){
                         
                         agregarValue("telconet_schemabundle_infoelementopoptype_jurisdiccionId", json.encontrados[0]['idJurisdiccion']);
                         presentarCantonesEdit(Ext.get('telconet_schemabundle_infoelementopoptype_jurisdiccionId'), "telconet_schemabundle_infoelementopoptype_cantonId", "buscarCantones", "encontrados", "",json.encontrados[0]['idCanton'], json.encontrados[0]['idParroquia']);
                         //sleep(3000);
                         
                         
//                         presentarParroquias(document.getElementById("telconet_schemabundle_infoelementopoptype_cantonId"), "telconet_schemabundle_infoelementopoptype_parroquiaId", "buscarParroquias", "encontrados", "", json.encontrados[0]['idParroquia']);
//                         
//                         
//                         
//                         
//                         
//                       presentarCantones(document.getElementById("telconet_schemabundle_infoelementopoptype_jurisdiccionId"), "telconet_schemabundle_infoelementopoptype_cantonId", "buscarCantones", "encontrados", "", json.encontrados[0]['idCanton']);
//                         
                         
                     }
                     else{
                         alert("sin datos");
                     }
                   },
          failure: function(result)
                   {
                     Ext.Msg.alert('Error ','Error: ' + result.statusText);
                     
                   }
        }
      );
});

function presentarCantonesEdit(objeto_1, objeto_2, accion, root, seleccion, valor_campo, valor_campo1)
{
 var conn = new Ext.data.Connection();
 
 conn.request
      (
        {
          url: '../../../../../administracion/general/admi_canton/'+accion,
          method: 'post',
          params: {idJurisdiccion : objeto_1.getValue()},
          success: function(response)
                   {
                     var json = Ext.JSON.decode(response.responseText);
                     if(root == 'encontrados')
                     {
                       llenarCombo(objeto_2, json.encontrados, 'encontrados', seleccion, 'nombre_canton', 'id_canton', 'telconet_schemabundle_infoelementopoptype_cantonId', valor_campo);
                       
                       presentarParroquiasEdit(Ext.get('telconet_schemabundle_infoelementopoptype_cantonId'), "telconet_schemabundle_infoelementopoptype_parroquiaId", "buscarParroquias", "encontrados", "",valor_campo1);
                     }
                   },
          failure: function(result)
                   {
                     Ext.Msg.alert('Error ','Error: ' + result.statusText);
                   }
        }
      );
}

function presentarParroquiasEdit(objeto_1, objeto_2, accion, root, seleccion, valor_campo)
{
//    alert(objeto_1.value);
 var conn = new Ext.data.Connection();
 conn.request
      (
        {
          url: '../../../../../administracion/general/admi_parroquia/'+accion,
          method: 'post',
          params: {idCanton : objeto_1.getValue()},
          success: function(response)
                   {
                     var json = Ext.JSON.decode(response.responseText);
                     if(root == 'encontrados')
                     {
                       llenarCombo(objeto_2, json.encontrados, 'encontrados', seleccion, 'nombre_parroquia', 'id_parroquia', 'telconet_schemabundle_infoelementopoptype_parroquiaId', valor_campo);
                     }
                   },
          failure: function(result)
                   {
                     Ext.Msg.alert('Error ','Error: ' + result.statusText);
                   }
        }
      );
}

function presentarCantones(objeto_1, objeto_2, accion, root, seleccion, valor_campo, valor_campo1)
{
 var conn = new Ext.data.Connection();
 
 conn.request
      (
        {
          url: '../../../../../administracion/general/admi_canton/'+accion,
          method: 'post',
          params: {idJurisdiccion : objeto_1.value},
          success: function(response)
                   {
                     var json = Ext.JSON.decode(response.responseText);
                     if(root == 'encontrados')
                     {
                       llenarCombo(objeto_2, json.encontrados, 'encontrados', seleccion, 'nombre_canton', 'id_canton', 'telconet_schemabundle_infoelementopoptype_cantonId', valor_campo);
                       
                     }
                   },
          failure: function(result)
                   {
                     Ext.Msg.alert('Error ','Error: ' + result.statusText);
                   }
        }
      );
}

function presentarParroquias(objeto_1, objeto_2, accion, root, seleccion, valor_campo)
{
//    alert(objeto_1.value);
 var conn = new Ext.data.Connection();
 conn.request
      (
        {
          url: '../../../../../administracion/general/admi_parroquia/'+accion,
          method: 'post',
          params: {idCanton : objeto_1.value},
          success: function(response)
                   {
                     var json = Ext.JSON.decode(response.responseText);
                     if(root == 'encontrados')
                     {
                       llenarCombo(objeto_2, json.encontrados, 'encontrados', seleccion, 'nombre_parroquia', 'id_parroquia', 'telconet_schemabundle_infoelementopoptype_parroquiaId', valor_campo);
                     }
                   },
          failure: function(result)
                   {
                     Ext.Msg.alert('Error ','Error: ' + result.statusText);
                   }
        }
      );
}

function llenarCombo(name_id_combo, objetos, tag, value_option_selected, valor, id, nombre_campo, valor_campo)
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
 
 agregarValue(nombre_campo,valor_campo);
}

function agregarValue(campo, valor){
    document.getElementById(campo).value = valor;
}

function validador(e,tipo) {      
  
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
       
    console.log(key);
    if(tipo=='numeros'){    
      letras = "0123456789";
      especiales = [45,46];
    }else if(tipo=='letras'){
      letras = "abcdefghijklmnopqrstuvwxyz";
      especiales = [8, 37, 32,46,36,38,40,44,41];
    }
    else{ 
      letras = "abcdefghijklmnopqrstuvwxyz0123456789";
      especiales = [8, 37, 32,46,36,38,40,44,41];
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