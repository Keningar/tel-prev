/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    
    storeUsuarios = new Ext.data.Store({  
        pageSize: 100,
        proxy: {
            type: 'ajax',
            timeout: 400000,
            url : getUsuariosPorModelo,
            extraParams: {
                modeloId: '',
                estado:''
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
              {name:'idUsuarioAcceso',      mapping:'idUsuarioAcceso'},
              {name:'nombreUsuarioAcceso',  mapping:'nombreUsuarioAcceso'}
            ]
    });
    
    storeElementos = new Ext.data.Store({  
        pageSize: 100,
        listeners: {
            load: function() {

            }
        },
        proxy: {
            type: 'ajax',
            timeout: 400000,
            url : getElementosPorModelo,
            extraParams: {
                nombreElemento: this.nombreElemento,
                modeloElemento: ''
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
              {name:'idElemento',       mapping:'idElemento'},
              {name:'nombreElemento',   mapping:'nombreElemento'}
            ]
    });
    
    comboElementos = new Ext.form.ComboBox({
        id:             'cmb_elementoA',
        name:           'cmb_elementoA',
        fieldLabel:     false,
        anchor:         '100%',
        queryMode:      'remote',
        width:          300,
        emptyText:      'Seleccione Elemento',
        store:          storeElementos,
        displayField:   'nombreElemento',
        valueField:     'idElemento',
        renderTo:       'comboElemento'
    });
    
    comboUsuarios = new Ext.form.ComboBox({
        id:             'cmb_usuarios',
        name:           'cmb_usuarios',
        fieldLabel:     false,
        anchor:         '100%',
        queryMode:      'local',
        width:          300,
        emptyText:      'Seleccione Usuario',
        store:          storeUsuarios,
        displayField:   'nombreUsuarioAcceso',
        valueField:     'idUsuarioAcceso',
        renderTo:       'comboUsuarios'
    });
});

function presentarModelos(objeto1, objeto2, root, seleccion)
{
 var conn = new Ext.data.Connection();
 conn.request
      (
        {
          url: getModeloPorTipo,
          method: 'post',
          params: {
              nombre:           '',
              marcaElemento:    '',
              tipoElemento:     objeto1.value,
              estado:           'Activo'
          },
          success: function(response)
                   {
                     var json = Ext.JSON.decode(response.responseText);
                     if(root == 'encontrados')
                     {
                       llenarCombo(objeto2, json.encontrados, 'encontrados', seleccion, 'nombreModeloElemento', 'idModeloElemento');
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
        combo.add(new Option('-- Seleccione --', '0'), null);
    }
    catch (e)
    { //in IE
        combo.add(new Option('-- Seleccione --', '0'));
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
    if (value_option_selected)
    {
        var el_option = combo_el.query('option[value=' + value_option_selected + ']');

        el_option = Ext.get(el_option[0]);
        el_option.dom.selected = true;
    }
}

function presentarElementoYUsuarios(objeto1){
    storeElementos.proxy.extraParams = {nombreElemento:this.nombreElemento, modeloElemento:objeto1.value};
    storeElementos.load({params: {}});

    storeUsuarios.proxy.extraParams = {modeloId:objeto1.value, estado:'Activo'};
    storeUsuarios.load({params: {}});
}

function validarFormulario(){
    var elemento = Ext.getCmp('cmb_elementoA').getValue();
    var usuario = Ext.getCmp('cmb_usuarios').getValue();
    var contrasena = $("#telconet_schemabundle_infocontrasenatype_contrasena").val();
    
    if(contrasena  === "" || elemento === "" || elemento === null || usuario === "" || usuario === null){
        alert("Favor revisar, algunos campos estan en blanco!")
        return false;
    }
    
    $("#telconet_schemabundle_infoelementocontrasenatype_elemento").val(elemento);
    $("#telconet_schemabundle_infoelementocontrasenatype_usuario").val(usuario);
    
    return true;
}