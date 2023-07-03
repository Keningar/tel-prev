Ext.onReady(function(){ 
    Ext.MessageBox.wait("Cargando Datos...");
    var Url = location.href; 
//    alert(Url);
    UrlUrl = Url.replace(/.*\?(.*?)/,"$1");  
    Variables = Url.split ("/");
    var n = Variables.length;
//    alert(Variables[n-1]);
    var idSplitter = Variables[n-2];
    
//    alert(idDslam);
    
    var accion="cargarDatosSplitter";
    var conn = new Ext.data.Connection();
    conn.request
      (
        {
          url: ''+accion,
          method: 'post',
          params: {idSplitter : idSplitter},
          success: function(response)
                   {
                     var json = Ext.JSON.decode(response.responseText);
                     
                     if(json.total>0){
                        presentarContenidoEnEdit(json.encontrados[0]['tipoElementoContenedor'],json.encontrados[0]['nombreElementoContenedor'],json.encontrados[0]['cajaElementoId']);
//                         agregarValue("telconet_schemabundle_infoelementosplittertype_contenidoEn", json.encontrados[0]['tipoElementoContenedor']);
                         agregarValue("telconet_schemabundle_infoelementosplittertype_nivel", json.encontrados[0]['nivel']);
//                         presentarElementoEdit(document.getElementById('telconet_schemabundle_infoelementosplittertype_contenidoEn'), "telconet_schemabundle_infoelementosplittertype_elementoContenedorId", "buscarElementoContenedor", "encontrados", "", json.encontrados[0]['cajaElementoId']);
                         
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

function presentarContenidoEnEdit(valor1, valor2, idValor2){
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
//        autoLoad: true,
        listeners: {
            load: function() {

            }
        },
        proxy: {
            type: 'ajax',
//            timeout: 400000,
            url : '../../splitter/buscarElementoContenedor',
            extraParams: {
                nombreElemento: this.nombre_elemento,
                tipoElemento: 'NODO,CAJA DISPERSION'
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
        value: valor1,
        renderTo: 'comboContenido',
        listeners: {
            select: function(combo){
                Ext.getCmp('cmb_elementoA').setRawValue("");
                Ext.getCmp('cmb_elementoA').setValue("");
                storeElementosA.proxy.extraParams = {tipoElemento: combo.getValue()};
                storeElementosA.load({params: {}});
                agregarValue("telconet_schemabundle_infoelementosplittertype_contenidoEn",combo.getValue());
                
                
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
        value: valor2,
        renderTo: 'comboElemento',
        listeners: {
            select: function(combo){
                agregarValue("telconet_schemabundle_infoelementosplittertype_elementoContenedorId",combo.getValue());
            }
        }
    });

    $("#telconet_schemabundle_infoelementosplittertype_elementoContenedorId").val(idValor2);
    
    Ext.MessageBox.destroy();
}

function presentarElementoEdit(objeto_1, objeto_2, accion, root, seleccion, valor_campo)
{
 var conn = new Ext.data.Connection();
 conn.request
      (
        {
          url: '../../../../../tecnico/elemento/splitter/'+accion,
          method: 'post',
          timeout: 400000,
          params: {tipoElemento : objeto_1.value},
          success: function(response)
                   {
                     var json = Ext.JSON.decode(response.responseText);
                     if(root == 'encontrados')
                     {
                       llenarCombo(objeto_2, json.encontrados, 'encontrados', seleccion, 'nombre_elemento', 'id_elemento', 'telconet_schemabundle_infoelementosplittertype_elementoContenedorId', valor_campo);
                       Ext.MessageBox.destroy();
                     }
                   },
          failure: function(result)
                   {
                     Ext.Msg.alert('Error ','Error: ' + result.statusText);
                   }
        }
      );
}

function presentarElemento(objeto_1, objeto_2, accion, root, seleccion)
{
 var conn = new Ext.data.Connection();
 conn.request
      (
        {
          url: '../../../../../tecnico/elemento/splitter/'+accion,
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