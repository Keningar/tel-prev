/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    
    storeElementosA = new Ext.data.Store({  
        pageSize: 100,
        listeners: {
            load: function() {

            }
        },
        proxy: {
            type: 'ajax',
            timeout: 400000,
            url : '../../../tecnico/enlace/enlace_elemento/buscarElementoPorTipoElemento',
            extraParams: {
                idServicio: '',
                nombreElemento: this.nombreElemento,
                tipoElemento: ''
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
              {name:'idElemento', mapping:'idElemento'},
              {name:'nombreElemento', mapping:'nombreElemento'},
              {name:'ipElemento', mapping:'ip'}
            ]
    });
    
    storeElementosB = new Ext.data.Store({  
        pageSize: 100,
        listeners: {
            load: function() {

            }
        },
        proxy: {
            type: 'ajax',
            timeout: 400000,
            url : '../../../tecnico/enlace/enlace_elemento/buscarElementoPorTipoElemento',
            extraParams: {
                idServicio: '',
                nombreElemento: this.nombreElemento,
                tipoElemento: ''
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
              {name:'idElemento', mapping:'idElemento'},
              {name:'nombreElemento', mapping:'nombreElemento'},
              {name:'ipElemento', mapping:'ip'}
            ]
    });
    
    storeInterfacesElementoA = new Ext.data.Store({  
        pageSize: 500,
//                autoLoad: true,
        proxy: {
            type: 'ajax',
            timeout: 400000,
            url : '../../../tecnico/enlace/enlace_elemento/buscarInterfacesPorElemento',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
              {name:'idInterface'            , mapping:'idInterface'},
              {name:'nombreInterfaceElemento', mapping:'nombreInterfaceElemento'},
              {name:'nombreEstadoInterface'  , mapping:'nombreEstadoInterface'},
              {name:'estado'                 , mapping:'estado'}
            ]
    });
    
    storeInterfacesElementoB = new Ext.data.Store({  
        pageSize: 500,
//                autoLoad: true,
        proxy: {
            type: 'ajax',
            timeout: 400000,
            url : '../../../tecnico/enlace/enlace_elemento/buscarInterfacesPorElemento',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
              {name:'idInterface'            , mapping:'idInterface'},
              {name:'nombreInterfaceElemento', mapping:'nombreInterfaceElemento'},
              {name:'nombreEstadoInterface'  , mapping:'nombreEstadoInterface'},
              {name:'estado'                 , mapping:'estado'}
            ]
    });
    
    comboElementos = new Ext.form.ComboBox({
        id: 'cmb_elementoA',
        name: 'cmb_elementoA',
        fieldLabel: false,
        anchor: '100%',
        queryMode:'remote',
        width: 300,
        minChars:3,
        emptyText: 'Seleccione Elemento',
        store:storeElementosA,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        renderTo: 'comboElementoA',
        listeners: {
            select: function(combo){
                storeInterfacesElementoA.proxy.extraParams = {idElemento: combo.getValue()};
                storeInterfacesElementoA.load({
                    callback: function() {
                            storeInterfacesElementoA.filter('estado',/not connect/);
                        }
                });
            }
        }
    });
    
    comboElementos = new Ext.form.ComboBox({
        id: 'cmb_elementoB',
        name: 'cmb_elementoB',
        fieldLabel: false,
        anchor: '100%',
        queryMode:'remote',
        width: 300,
        minChars:3,
        emptyText: 'Seleccione Elemento',
        store:storeElementosB,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        renderTo: 'comboElementoB',
        listeners: {
            select: function(combo){
                storeInterfacesElementoB.proxy.extraParams = {idElemento: combo.getValue()};
                storeInterfacesElementoB.load({
                    callback: function() {
                            storeInterfacesElementoB.filter('estado',/not connect/);
                        }
                });
            }
        }
    });
    
    comboInterfaces = new Ext.form.ComboBox({
        id: 'cmb_interfaceA',
        name: 'cmb_interfaceA',
        fieldLabel: false,
        anchor: '100%',
        queryMode:'local',
        width: 200,
        emptyText: 'Seleccione Interface',
        store:storeInterfacesElementoA,
        displayField: 'nombreInterfaceElemento',
        valueField: 'idInterface',
        renderTo: 'comboInterfaceA'
    });
    
    comboInterfaces = new Ext.form.ComboBox({
        id: 'cmb_interfaceB',
        name: 'cmb_interfaceB',
        fieldLabel: false,
        anchor: '100%',
        queryMode:'local',
        width: 200,
        emptyText: 'Seleccione Interface',
        store:storeInterfacesElementoB,
        displayField: 'nombreInterfaceElemento',
        valueField: 'idInterface',
        renderTo: 'comboInterfaceB'
    });
});

function presentarElementoA(objeto_1, objeto_2, accion, root, seleccion){
    storeElementosA.proxy.extraParams = {idServicio: '', nombreElemento:this.nombreElemento, tipoElemento:objeto_1.value};
    storeElementosA.load({params: {}});
}

function presentarElementoB(objeto_1, objeto_2, accion, root, seleccion){
    storeElementosB.proxy.extraParams = {idServicio: '', nombreElemento:this.nombreElemento, tipoElemento:objeto_1.value};
    storeElementosB.load({params: {}});
}

function presentarInterfaces(objeto_1, objeto_2, accion, root, seleccion)
{
 var conn = new Ext.data.Connection();
 conn.request
      (
        {
          url: accion,
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

function validarFormulario()
{
    var interfaceB = Ext.getCmp('cmb_interfaceB').getValue();       
    var interfaceA = Ext.getCmp('cmb_interfaceA').getValue();   
    
    $("#telconet_schemabundle_infoenlacetype_interfaceElementoIdA").val(interfaceA);
    $("#telconet_schemabundle_infoenlacetype_interfaceElementoIdB").val(interfaceB);
        
    tipoMedio = $("#telconet_schemabundle_infoenlacetype_tipoMedioId").val();
    claseTipoMedio = $("#telconet_schemabundle_infoenlacetype_claseTipoMedioId").val();
    buffer    = $("#telconet_schemabundle_infoenlacetype_bufferId").val();
    hilo      = $("#telconet_schemabundle_infoenlacetype_hiloId").val();   
    
    var elementoA = Ext.getCmp('cmb_elementoA').getValue();
    var elementoB = Ext.getCmp('cmb_elementoB').getValue();
    
    if(elementoA === elementoB)
    {
        Ext.Msg.alert('Advertencia','Debe seleccionar diferentes elementos para la creación de un enlace');
        return false;
    }
    
    if(tipoMedio === 'Seleccione' || tipoMedio == 0 || tipoMedio == null)
    {
        Ext.Msg.alert('Advertencia','Debe escoger el Tipo Medio');
        return false;
    }
    
    if(claseTipoMedio !== 'Seleccione' && claseTipoMedio != 0 && claseTipoMedio != null)
    {
        if(buffer ==  null || buffer == 0)
        {
            Ext.Msg.alert('Advertencia','Debe escoger el buffer');
            return false;
        }
        if(hilo == null || hilo == 0)
        {
            Ext.Msg.alert('Advertencia','Debe escoger el Hilo');
            return false;
        }
    }
    
    
    
    return true;        
}


function presentarClaseTipoMedio(valor_padre, combo_destino, valor_campo)
{

    var conn = new Ext.data.Connection();        
    conn.request
        (
            {
                url: url_claseTipoMedio,
                method: 'post',
                params: {
                    tipoMedioId: valor_padre,
                    estado     : 'Activo'
                },
                success: function(response)
                {
                    var json = Ext.JSON.decode(response.responseText);
                    llenarCombo(combo_destino, json.encontrados, 'nombreClaseTipoMedio', 'idClaseTipoMedio', valor_campo);
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            }

        );
}

function presentarHilo(valor_padre, combo_destino, valor_campo)
{

    var conn = new Ext.data.Connection();        
    conn.request
        (
            {
                url: url_hilosPorBuffer,
                method: 'post',
                params: {
                    buffer     : valor_padre,
                    estado     : 'Activo'                   
                },
                success: function(response)
                {
                    var json = Ext.JSON.decode(response.responseText);
                    llenarCombo(combo_destino, json.encontrados, 'hilo', 'idHilo', valor_campo);
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            }

        );
}

function presentarBuffer(valor_padre, combo_destino, valor_campo)
{

    var conn = new Ext.data.Connection();        
    conn.request
        (
            {
                url: url_bufferHiloTipoMedio,
                method: 'post',
                params: {
                    tipoMedioId          : valor_padre,
                    estado               : 'Activo',
                    estadoBufferHilo     : 'Activo'                    
                },
                success: function(response)
                {
                    var json = Ext.JSON.decode(response.responseText);
                    llenarCombo(combo_destino, json.encontrados, 'buffer', 'idBuffer', valor_campo);
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            }

        );
}

function llenarCombo(name_id_combo, objetos, valor, id, valor_campo)
{
    var combo = Ext.getDom(name_id_combo);

    while (combo.length > 0)
    {
        combo.removeChild(combo.firstChild);
    }

    try
    {
        combo.add(new Option('Seleccione', '0'), null);
    }
    catch (e)
    { //in IE
        combo.add(new Option('Seleccione', '0'));
    }

    for (var i = 0; i < objetos.length; ++i)
    {
        try
        {
            combo.add(new Option(objetos[i][valor], objetos[i][id]), null);
        }
        catch (e)
        { //in IE
            combo.add(new Option(objetos[i][valor], objetos[i][id]));
        }
    }

    agregarValue(name_id_combo, valor_campo);
}

function agregarValue(campo, valor) 
{
    document.getElementById(campo).value = valor;
}