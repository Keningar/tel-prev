Ext.onReady(function() {

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
                marcaElemento:  '',
                canton:         '',
                jurisdiccion:   '',
                estado:         'Activo'
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

    var storeRack = new Ext.data.Store({
        total: 'total',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: url_getEncontradosRack,
            extraParams: {
                nombreElemento: '',
                modeloElemento: '',
                marcaElemento: '',
                canton: '',
                jurisdiccion: '',
                estado: 'Todos'
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

    var storeUnidadesDisponibles = new Ext.data.Store({
        total: 'total',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: url_getUnidadesElemento,
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
                {name: 'nombreElemento', mapping: 'nombreElemento'},
                {name: 'estado', mapping: 'estado'},
                {name: 'nombreElementoUnidad', mapping: 'nombreElementoUnidad'},
                {name: 'nombreEstado', mapping: 'nombreEstado'}
            ]
    });

    combo_nodos = new Ext.form.ComboBox({
        id: 'combo_nodos',
        name: 'combo_nodos',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'remote',
        width: 250,
        emptyText: 'Seleccione Nodo',
        store: storeNodo,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        renderTo: 'combo_nodos',
        listeners: {
            select: {fn: function(combo, value) {
                    $('#telconet_schemabundle_infoelementoswitchtype_nodoElementoId').val(combo.getValue());
                    $('#telconet_schemabundle_infoelementoswitchtype_rackElementoId').val('');
                    $('#telconet_schemabundle_infoelementoswitchtype_unidadRack').val('');
                    Ext.getCmp('combo_rack').reset();
                    Ext.getCmp('combo_unidades').reset();
                    Ext.getCmp('combo_rack').setDisabled(false);
                    Ext.getCmp('combo_unidades').setDisabled(true);
                    presentarRacks(combo.getValue());

                }}},
        forceSelection: true
    });

    combo_rack = new Ext.form.ComboBox({
        id: 'combo_rack',
        name: 'combo_rack',
        fieldLabel: false,
        disabled: true,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        emptyText: 'Seleccione Rack',
        store: storeRack,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        renderTo: 'combo_rack',
        listeners: {
            select: {fn: function(combo, value) {
                    $('#telconet_schemabundle_infoelementoswitchtype_rackElementoId').val(combo.getValue());
                    $('#telconet_schemabundle_infoelementoswitchtype_unidadRack').val('');
                    Ext.getCmp('combo_unidades').reset();
                    Ext.getCmp('combo_unidades').setDisabled(false);
                    presentarUnidades(combo.getValue());
                }}
        }
    });

    combo_unidades = new Ext.form.ComboBox({
        id: 'combo_unidades',
        name: 'combo_unidades',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        disabled: true,
        emptyText: 'Seleccione Unidad',
        store: storeUnidadesDisponibles,
        displayField: 'nombreEstado',
        valueField: 'idElemento',
        renderTo: 'combo_unidades',
        listeners: {
            select: {fn: function(combo, value) {
                    $('#telconet_schemabundle_infoelementoswitchtype_unidadRack').val(combo.getValue());
                }}
        }
    });    

    function presentarRacks(nodoId) {

        storeRack.proxy.extraParams = {popElemento: nodoId, estado: 'Activo'};
        storeRack.load();
    }

    function presentarUnidades(rackId) {

        storeUnidadesDisponibles.proxy.extraParams = {idElemento: rackId};
        storeUnidadesDisponibles.load();
    }

});


/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function validador(e, tipo) {

    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();

    if (tipo == 'numeros') {
        letras = "0123456789";
        especiales = [45, 46];
    } else if (tipo == 'letras') {
        letras = "abcdefghijklmnopqrstuvwxyz";
        especiales = [8, 36, 35, 45, 47, 40, 41, 46, 32, 37, 39];
    }
    else if (tipo == 'ip') {
        letras = "0123456789";
        especiales = [8, 46];
    }
    else {
        letras = "abcdefghijklmnopqrstuvwxyz0123456789";
        especiales = [8, 36, 35, 45, 47, 40, 41, 46];
    }

    //46 => .    
    //32 => ' ' espacio
    //8 => backspace       
    //37 => direccional izq
    //39 => direccional der y '
    //44 => ,

    tecla_especial = false
    for (var i in especiales) {
        if (key == especiales[i]) {
            tecla_especial = true;
            break;
        }
    }


    if (letras.indexOf(tecla) == -1 && !tecla_especial)
        return false;
}

function validacionesForm() {
    //validar nombre elemento
    if (document.getElementById("telconet_schemabundle_infoelementoswitchtype_nombreElemento").value == "") 
    {
        alert("Falta llenar algunos campos");
        return false;
    }

    //validar ip
    if (document.getElementById("telconet_schemabundle_infoelementoswitchtype_ipElemento").value == "") 
    {
        alert("Falta llenar algunos campos");
        return false;
    }

    //validar modelo
    if (document.getElementById("telconet_schemabundle_infoelementoswitchtype_modeloElementoId").value == "") 
    {
        alert("Falta llenar algunos campos");
        return false;
    }
    
    //validar nodo
    if (document.getElementById("telconet_schemabundle_infoelementoswitchtype_nodoElementoId").value == "" || 
        combo_nodos.value == "" || combo_nodos.value == null) 
    {
        alert("Falta llenar algunos campos");
        return false;
    }
    //validar anillo
    if (document.getElementById("telconet_schemabundle_infoelementoswitchtype_anillo").value === "Seleccione Anillo...")
    {
        alert("Falta escoger el Anillo al cual pertenece el Switch");
        return false;
    }
    else
    {
        if (document.getElementById("telconet_schemabundle_infoelementoswitchtype_rackElementoId").value != "") 
        {
            if (combo_rack.value == "" || combo_rack.value == null)
            {
                alert("Falta seleccionar el Rack");
                return false;
            }
            else
            {
                if (document.getElementById("telconet_schemabundle_infoelementoswitchtype_unidadRack").value == "" || 
                    combo_unidades.value == "" || combo_unidades.value == null) 
                {
                    alert("Falta seleccionar la unidad de rack");
                    return false;
                }
            }
        }
    }
    return true;
}
