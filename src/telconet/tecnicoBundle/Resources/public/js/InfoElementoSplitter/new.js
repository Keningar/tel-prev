/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Ext.onReady(function(){
    Ext.define('tipoCaracteristica', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'tipo', type: 'string'}
        ]
    });

    var comboModoOperacionCpe = new Ext.data.Store({ 
        model: 'tipoCaracteristica',
        data : [
            {tipo:'NODO' },
            {tipo:'CAJA DISPERSION' }
        ]
    });
    
    storeElementosA = new Ext.data.Store({  
        pageSize: 100,
        listeners: {
            load: function() {

            }
        },
        proxy: {
            type: 'ajax',
//            timeout: 400000,
            url : '../../../tecnico/elemento/splitter/buscarElementoContenedor',
            extraParams: {
                nombreElemento: this.nombre_elemento
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
              {name:'id_elemento', mapping:'id_elemento'},
              {name:'nombre_elemento', mapping:'nombre_elemento'}
            ]
    });
    
    comboContenido = new Ext.form.ComboBox({
        id: 'cmb_contenido',
        name: 'cmb_contenido',
        fieldLabel: false,
        anchor: '100%',
        queryMode:'local',
        width: 300,
        emptyText: 'Seleccione Tipo',
        store:comboModoOperacionCpe,
        displayField: 'tipo',
        valueField: 'tipo',
        renderTo: 'comboContenido',
        listeners: {
            select: function(combo){
                storeElementosA.proxy.extraParams = {tipoElemento: combo.getValue()};
                storeElementosA.load({params: {}});
            }
        }
    });
    
    comboElementos = new Ext.form.ComboBox({
        id: 'cmb_elementoA',
        name: 'cmb_elementoA',
        fieldLabel: false,
        anchor: '100%',
        queryMode:'remote',
        width: 300,
        emptyText: 'Seleccione Elemento',
        store:storeElementosA,
        displayField: 'nombre_elemento',
        valueField: 'id_elemento',
        renderTo: 'comboElemento'
    });
});

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
      especiales = [8,36,35,45,47,40,41,46,32];
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
    if(document.getElementById("telconet_schemabundle_infoelementosplittertype_nombreElemento").value==""){
        alert("Falta llenar algunos campos");
        return false;
    }
    
    if(document.getElementById("telconet_schemabundle_infoelementosplittertype_descripcionElemento").value==""){
        alert("Falta llenar algunos campos");
        return false;
    }
    
    //validar modelo
    if(document.getElementById("telconet_schemabundle_infoelementosplittertype_modeloElementoId").value==""){
        alert("Falta llenar algunos campos");
        return false;
    }
    
    var elemento = Ext.getCmp('cmb_elementoA').getValue();
    $("#telconet_schemabundle_infoelementosplittertype_elementoContenedorId").val(elemento);
    
    //validar contenedor
    if(document.getElementById("telconet_schemabundle_infoelementosplittertype_elementoContenedorId").value==""){
        alert("Falta llenar algunos campos, contenedor");
        return false;
    }
    
    //validar nivel
    if(document.getElementById("telconet_schemabundle_infoelementosplittertype_nivel").value==""){
        alert("Falta llenar algunos campos");
        return false;
    }
    
    return true;
}

function presentarElemento(objeto_1, objeto_2, accion, root, seleccion){
 var conn = new Ext.data.Connection();
 conn.request
      (
        {
          url: accion,
          method: 'post',
          params: {tipoElemento : objeto_1.value},
          success: function(response)
                   {
                     var json = Ext.JSON.decode(response.responseText);
                     if(root == 'encontrados')
                     {
                       llenarCombo(objeto_2, json.encontrados, 'encontrados', seleccion, 'nombre_elemento', 'id_elemento');
                     }
                   },
          failure: function(result)
                   {
                     Ext.Msg.alert('Error ','Error: ' + result.statusText);
                   }
        }
      );
}

function llenarCombo(name_id_combo, objetos, tag, value_option_selected, valor, id){
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