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
    else if (tipo == 'ip') {
        letras = "0123456789";
        especiales = [8, 46];
    }
    else{ 
      letras = "abcdefghijklmnopqrstuvwxyz0123456789";
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

Ext.onReady(function() {
    
    Ext.define('tipoCaracteristica', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'tipo', type: 'string'}
            ]
        });
    
    var storeTipoElementoRed = new Ext.data.Store({
        model: 'tipoCaracteristica',
        data: [
            {tipo: 'BACKBONE'},
            {tipo: 'REPETIDORA IN'},
            {tipo: 'REPETIDORA OUT'},
        ]
    });
    
    combo_tipoElementoRed = new Ext.form.ComboBox({
        id: 'combo_tipoElementoRed',
        name: 'combo_tipoElementoRed',
        fieldLabel: false,
        editable: false,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        emptyText: 'Seleccione Tipo Elemento Red',
        store: storeTipoElementoRed,
        displayField: 'tipo',
        valueField: 'tipo',
        renderTo: 'combo_tipoElementoRed',
        listeners: {
            select: {fn: function(combo, value) {
                   $('#telconet_schemabundle_infoelementoradiotype_tipoElementoRed').val(combo.getValue());
                   var tipoElementoRed = combo.getRawValue();
                   if (tipoElementoRed != 'BACKBONE')
                   {
                        Ext.getCmp('combo_radioInicioId').setDisabled(false);
                        var tipoElementoRedParam = '';
                        if (tipoElementoRed == 'REPETIDORA IN')
                        {
                            tipoElementoRedParam = 'BACKBONE';
                        }
                        else
                        {
                            tipoElementoRedParam = 'REPETIDORA IN';
                        }
                        Ext.getCmp('combo_switch').reset();
                        Ext.getCmp('combo_switch').setDisabled(true);
                        Ext.getCmp('combo_intSwitch').reset();
                        Ext.getCmp('combo_intSwitch').setDisabled(true);
                        $('#telconet_schemabundle_infoelementoradiotype_switchElementoId').val('');
                        $('#telconet_schemabundle_infoelementoradiotype_interfaceSwitchId').val('');
                        storeElementosRadio.proxy.extraParams = {tipoElementoRed: tipoElementoRedParam,elemento: 'RADIO'};
                        storeElementosRadio.load({});
                   }
                   else
                   {
                       Ext.getCmp('combo_radioInicioId').reset();
                       Ext.getCmp('combo_radioInicioId').setDisabled(true);
                       $('#telconet_schemabundle_infoelementoradiotype_radioInicioId').val('');
                       Ext.getCmp('combo_switch').reset();
                       Ext.getCmp('combo_switch').setDisabled(false);
                       Ext.getCmp('combo_intSwitch').reset();
                       Ext.getCmp('combo_intSwitch').setDisabled(true);
                       $('#telconet_schemabundle_infoelementoradiotype_switchElementoId').val('');
                       $('#telconet_schemabundle_infoelementoradiotype_interfaceSwitchId').val('');
                   }
                }}},
        forceSelection: true
    });
    
    var storeElementosRadio = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            proxy: {
                timeout: 60000,
                type: 'ajax',
                url: getRadioInicio,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                },
                extraParams: {
                    nombre: '' ,
                    modelo: '',
                    elemento: 'RADIO',
                    tipoElementoRed: '' 
                }
            },
            fields:
                    [
                        {name: 'idElemento', mapping: 'idElemento'},
                        {name: 'nombreElemento', mapping: 'nombreElemento'}
                    ],
            autoLoad: false
        });
        
    combo_radioInicioId = new Ext.form.ComboBox({
        id: 'combo_radioInicioId',
        name: 'combo_radioInicioId',
        fieldLabel: false,
        disabled: true,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        emptyText: 'Seleccione Radio Inicio',
        store: storeElementosRadio,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        renderTo: 'combo_radioInicioId',
        listeners: {
            select: {fn: function(combo, value) 
                        {
                            $('#telconet_schemabundle_infoelementoradiotype_radioInicioId').val(combo.getValue());
                            var tipoRepetidora = '';
                            var tipoElementoRed = Ext.getCmp('combo_tipoElementoRed').getValue();
                            var nombreElemento = '';
                            if (tipoElementoRed == 'REPETIDORA IN')
                            {
                                tipoRepetidora = 'IN';
                                nombreElemento = "REP-" + combo.getRawValue();
                            }
                            else
                            {
                                tipoRepetidora = 'OUT';
                                nombreElemento = combo.getRawValue();
                            }
                            
                            Ext.MessageBox.wait("Generando Nombre Elemento...");
                            Ext.Ajax.request({
                                url: getNombreRepetidora,
                                method: 'post',
                                timeout: 40000,
                                params: { 
                                    nombreElemento: nombreElemento,
                                    tipoRepetidora: tipoRepetidora                                  
                                },
                                success: function(response){
                                    if(response.responseText != "ERROR")
                                    {
                                        $('#telconet_schemabundle_infoelementoradiotype_nombreElemento').val(response.responseText);
                                    }
                                    else
                                    {
                                        $('#telconet_schemabundle_infoelementoradiotype_nombreElemento').val("Ingrese Nombre");
                                    }
                                    Ext.MessageBox.hide();
                                },
                                failure: function(result)
                                {
                                   $('#telconet_schemabundle_infoelementoradiotype_nombreElemento').val("Ingrese Nombre");
                                   Ext.MessageBox.hide();
                                }
                            }); 
                        }
                    },
            change: function(object, newValue, odlValue, eOpts)
                    {
                        $('#telconet_schemabundle_infoelementoradiotype_radioInicioId').val('');
                    }
        
        },
        forceSelection: true
    });

    var storeNodo = new Ext.data.Store({
        total: 'total',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: url_getEncontradosNodo,
            timeout: 400000,
            extraParams: {
                nombreElemento: '',
                modeloElemento: '',
                marcaElemento: '',
                canton: '',
                jurisdiccion: '',
                estado: 'Todos',
                procesoBusqueda: 'limitado'
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            limitParam: undefined,
            startParam: undefined,
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'}
            ]
    });

   
    combo_nodos = new Ext.form.ComboBox({
        id: 'combo_nodos',
        name: 'combo_nodos',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'remote',
        width: 250,
        minChars : 3,
        emptyText: 'Seleccione Nodo',
        store: storeNodo,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        renderTo: 'combo_nodos',
        listeners: {
            select: {fn: function(combo, value) {
                    $('#telconet_schemabundle_infoelementoradiotype_nodoElementoId').val(combo.getValue());
                }}},
        forceSelection: true
    });
    
    storeSwitch = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            timeout: 3000000,
            type: 'ajax',
            url : getElementoSwitch,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreElemento: '',
                marcaElemento:  '',
                modeloElemento: '',
                canton:         '',
                jurisdiccion:   '',
                tipoElemento:   'SWITCH',
                estado:         'Todos'
            }
        },
        fields:
                  [
                    {name:'idElemento',         mapping:'idElemento'},
                    {name:'nombreElemento',     mapping:'nombreElemento'},
                    {name:'ipElemento',         mapping:'ipElemento'},
                    {name:'cantonNombre',       mapping:'cantonNombre'},
                    {name:'jurisdiccionNombre', mapping:'jurisdiccionNombre'},
                    {name:'marcaElemento',      mapping:'marcaElemento'},
                    {name:'modeloElemento',     mapping:'modeloElemento'},
                    {name:'longitud',           mapping:'longitud'},
                    {name:'latitud',            mapping:'latitud'},
                    {name:'estado',             mapping:'estado'},
                    {name:'action1',            mapping:'action1'},
                    {name:'action2',            mapping:'action2'},
                    {name:'action3',            mapping:'action3'}
                  ]
    });
    
    combo_switch = new Ext.form.ComboBox({
        id: 'combo_switch',
        name: 'combo_switch',
        fieldLabel: false,
        disabled: true,
        anchor: '100%',
        queryMode: 'remote',
        width: 250,
        emptyText: 'Seleccione Switch',
        store: storeSwitch,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        renderTo: 'combo_switch',
        listeners: {
            select: {fn: function(combo, value) {
                    $('#telconet_schemabundle_infoelementoradiotype_switchElementoId').val(combo.getValue());
                    $('#telconet_schemabundle_infoelementoradiotype_interfaceSwitchId').val('');
                    Ext.getCmp('combo_intSwitch').reset();
                    Ext.getCmp('combo_intSwitch').setDisabled(false);
                    storeIntSwitch.proxy.extraParams = {idElemento: combo.getValue()};
                    storeIntSwitch.load({
                        callback: function() {
                            storeIntSwitch.filter(function(r) {
                                var value = r.get('estado');
                                return (value == 'not connect' || value == 'reserved');
                            });
                        }

                    });
                    
                }},
            change: function(object, newValue, odlValue, eOpts)
                    {
                        $('#telconet_schemabundle_infoelementoradiotype_interfaceSwitchId').val('');
                        Ext.getCmp('combo_intSwitch').reset();
                        Ext.getCmp('combo_intSwitch').setDisabled(true);
                    }
        }
    });
    
     storeIntSwitch = new Ext.data.Store({  
        pageSize: 500,

        proxy: {
            type: 'ajax',
            timeout: 400000,
            url : getInterfaceElemento,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
              {name:'idInterface', mapping:'idInterface'},
              {name:'nombreInterfaceElemento', mapping:'nombreInterfaceElemento'},
              {name:'estado', mapping:'estado'},
            ]
    });
    
   combo_intSwitch = new Ext.form.ComboBox({
        id: 'combo_intSwitch',
        name: 'combo_intSwitch',
        fieldLabel: false,
        disabled: true,
        anchor: '100%',
        queryMode:'local',
        width: 200,
        emptyText: 'Seleccione Interface Switch',
        store:storeIntSwitch,
        displayField: 'nombreInterfaceElemento',
        valueField: 'idInterface',
        renderTo: 'combo_intSwitch',
        listeners: {
            select: {fn: function(combo, value) {
                    $('#telconet_schemabundle_infoelementoradiotype_interfaceSwitchId').val(combo.getValue());
                    var nombreElemento = "AP-" + Ext.getCmp('combo_switch').getRawValue() + "-" + Ext.getCmp('combo_intSwitch').getRawValue();
                    nombreElemento= nombreElemento.replace(".telconet.net", "");
                    $('#telconet_schemabundle_infoelementoradiotype_nombreElemento').val(nombreElemento);
                }}
        }
    });
    
});

function validacionesForm() {
    //validar nombre caja
    if (document.getElementById("telconet_schemabundle_infoelementoradiotype_nombreElemento").value == "") 
    {
        alert("Falta llenar algunos campos");
        return false;
    }

    //validar ip
    if (document.getElementById("telconet_schemabundle_infoelementoradiotype_ipElemento").value == "") 
    {
        alert("Falta llenar algunos campos");
        return false;
    }

    //validar modelo
    if (document.getElementById("telconet_schemabundle_infoelementoradiotype_modeloElementoId").value == "") 
    {
        alert("Falta llenar algunos campos");
        return false;
    }
    
    //validar sid
    if (document.getElementById("telconet_schemabundle_infoelementoradiotype_sid").value == "") 
    {
        alert("Falta llenar algunos campos");
        return false;
    }
    
    //validar mac
    if (document.getElementById("telconet_schemabundle_infoelementoradiotype_macElemento").value == "") 
    {
        alert("Falta llenar algunos campos");
        return false;
    }
    else
    {
        var mac = document.getElementById("telconet_schemabundle_infoelementoradiotype_macElemento").value;

        if(!mac.match("[a-fA-f0-9]{4}[\.]+[a-fA-f0-9]{4}[\.]+[a-fA-F0-9]{4}$"))
        {
            alert("Mac Incorrecta");
            return false;
        }
    }
    
    //validar nodo
    if (document.getElementById("telconet_schemabundle_infoelementoradiotype_nodoElementoId").value == "" || 
        combo_nodos.value == "" || combo_nodos.value == null) 
    {
        alert("Falta llenar algunos campos");
        return false;
    }
    
    if (document.getElementById("telconet_schemabundle_infoelementoradiotype_tipoElementoRed").value == "" || 
        combo_tipoElementoRed.value == "" || combo_tipoElementoRed.value == null) 
    {
        alert("Falta llenar algunos campos");
        return false;
    }
    else
    {
        if (combo_tipoElementoRed.value != "BACKBONE")
        {
            if (document.getElementById("telconet_schemabundle_infoelementoradiotype_radioInicioId").value == "" || 
                combo_radioInicioId.value == "" || combo_radioInicioId.value == null) 
            {
                alert("Falta llenar algunos campos");
                return false;
            }
        }
        else
        {
            //validar switch
            if (document.getElementById("telconet_schemabundle_infoelementoradiotype_switchElementoId").value == "" || 
                combo_switch.value == "" || combo_switch.value == null) 
            {
                alert("Falta llenar algunos campos");
                return false;
            }

            //validar interface switch
            if (document.getElementById("telconet_schemabundle_infoelementoradiotype_interfaceSwitchId").value == "" || 
                combo_intSwitch.value == "" || combo_intSwitch.value == null) 
            {
                alert("Falta llenar algunos campos");
                return false;
            }
        }
    }
    
    return true;
}