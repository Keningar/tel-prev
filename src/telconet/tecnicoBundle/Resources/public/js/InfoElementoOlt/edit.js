Ext.onReady(function() {

    var storeNodo = new Ext.data.Store({
        total: 'total',
        autoLoad: true,
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

    var storeRack = new Ext.data.Store({
        total: 'total',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: url_getEncontradosRack,
            timeout: 400000,
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
        queryMode: 'local',
        width: 250,
        emptyText: 'Seleccione Nodo',
        store: storeNodo,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        renderTo: 'combo_nodos',
        listeners: {
            select: {fn: function(combo, value) {
                    $('#telconet_schemabundle_infoelementoolttype_nodoElementoId').val(combo.getValue());
                    $('#telconet_schemabundle_infoelementoolttype_rackElementoId').val('');
                    $('#telconet_schemabundle_infoelementoolttype_unidadRack').val('');
                    Ext.getCmp('combo_rack').reset();
                    Ext.getCmp('combo_unidades').reset();
                    Ext.getCmp('combo_rack').setDisabled(false);
                    Ext.getCmp('combo_unidades').setDisabled(true);
                    presentarRacks(combo.getValue());

                }}}
    });

    combo_nodos.setValue(parseInt($('#telconet_schemabundle_infoelementoolttype_nodoElementoId').val()));
    if(enableSelectNodo == 'N'){
        combo_nodos.setDisabled(true);
    }

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
                    $('#telconet_schemabundle_infoelementoolttype_rackElementoId').val(combo.getValue());
                    $('#telconet_schemabundle_infoelementoolttype_unidadRack').val('');
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
                    $('#telconet_schemabundle_infoelementoolttype_unidadRack').val(combo.getValue());
                }}
        }
    });

    if ($('#telconet_schemabundle_infoelementoolttype_rackElementoId').val() != "")
    {
        Ext.getCmp('combo_rack').reset();
        Ext.getCmp('combo_rack').setDisabled(false);
        presentarRacks($('#telconet_schemabundle_infoelementoolttype_nodoElementoId').val());
        combo_rack.setValue(parseInt($('#telconet_schemabundle_infoelementoolttype_rackElementoId').val()));
        Ext.getCmp('combo_unidades').reset();
        Ext.getCmp('combo_unidades').setDisabled(false);
        presentarUnidades($('#telconet_schemabundle_infoelementoolttype_rackElementoId').val());
        combo_unidades.setValue(parseInt($('#telconet_schemabundle_infoelementoolttype_unidadRack').val()));
    }


    function presentarRacks(nodoId) {

        storeRack.proxy.extraParams = {popElemento: nodoId, estado: 'Activo'};
        storeRack.load();
    }

    function presentarUnidades(rackId) {

        storeUnidadesDisponibles.proxy.extraParams = {idElemento: rackId};
        storeUnidadesDisponibles.load();
    }

});

function validador(e, tipo) {

    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();

    console.log(key);
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
        especiales = [8, 36, 35, 45, 47, 40, 41, 46, 32, 37, 39];
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

function presentarCantonesEdit(objeto_1, objeto_2, accion, root, seleccion, valor_campo, valor_campo1)
{
    var conn = new Ext.data.Connection();

    conn.request
        (
            {
                url: '../../../../../administracion/general/admi_canton/' + accion,
                method: 'post',
                params: {idJurisdiccion: objeto_1.getValue()},
                success: function(response)
                {
                    var json = Ext.JSON.decode(response.responseText);
                    if (root == 'encontrados')
                    {
                        llenarCombo(objeto_2, 
                                    json.encontrados, 
                                    'encontrados', 
                                    seleccion, 
                                    'nombre_canton', 
                                    'id_canton', 
                                    'telconet_schemabundle_infoelementoolttype_cantonId', 
                                    valor_campo);

                        presentarParroquiasEdit(Ext.get('telconet_schemabundle_infoelementoolttype_cantonId'), 
                                                        "telconet_schemabundle_infoelementoolttype_parroquiaId", 
                                                        "buscarParroquias", 
                                                        "encontrados", 
                                                        "", 
                                                        valor_campo1);
                    }
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
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
                url: '../../../../../administracion/general/admi_parroquia/' + accion,
                method: 'post',
                params: {idCanton: objeto_1.getValue()},
                success: function(response)
                {
                    var json = Ext.JSON.decode(response.responseText);
                    if (root == 'encontrados')
                    {
                        llenarCombo(objeto_2, 
                                    json.encontrados, 
                                    'encontrados', 
                                    seleccion, 
                                    'nombre_parroquia', 
                                    'id_parroquia', 
                                    'telconet_schemabundle_infoelementoolttype_parroquiaId', 
                                    valor_campo);
                    }
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
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
                url: '../../../../../administracion/general/admi_canton/' + accion,
                method: 'post',
                params: {idJurisdiccion: objeto_1.value},
                success: function(response)
                {
                    var json = Ext.JSON.decode(response.responseText);
                    if (root == 'encontrados')
                    {
                        llenarCombo(objeto_2, 
                                    json.encontrados, 
                                    'encontrados', 
                                    seleccion, 
                                    'nombre_canton', 
                                    'id_canton', 
                                    'telconet_schemabundle_infoelementoolttype_cantonId', 
                                    valor_campo);

                    }
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
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
                url: '../../../../../administracion/general/admi_parroquia/' + accion,
                method: 'post',
                params: {idCanton: objeto_1.value},
                success: function(response)
                {
                    var json = Ext.JSON.decode(response.responseText);
                    if (root == 'encontrados')
                    {
                        llenarCombo(objeto_2, 
                                    json.encontrados, 
                                    'encontrados', 
                                    seleccion, 
                                    'nombre_parroquia', 
                                    'id_parroquia', 
                                    'telconet_schemabundle_infoelementoolttype_parroquiaId', 
                                    valor_campo);
                    }
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
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

    agregarValue(nombre_campo, valor_campo);
}

function agregarValue(campo, valor) {
    document.getElementById(campo).value = valor;
}

function validacionesForm() {

    //validar nombre caja
    if (document.getElementById("telconet_schemabundle_infoelementoolttype_nombreElemento").value == "")
    {
        alert("Falta llenar algunos campos");
        return false;
    }

    //validar ip
    if (document.getElementById("telconet_schemabundle_infoelementoolttype_ipElemento").value == "")
    {
        alert("Falta llenar algunos campos");
        return false;
    }

    //validar modelo
    if (document.getElementById("telconet_schemabundle_infoelementoolttype_modeloElementoId").value == "")
    {
        alert("Falta llenar algunos campos");
        return false;
    }

    //validar nodo
    if (document.getElementById("telconet_schemabundle_infoelementoolttype_nodoElementoId").value == "" ||
        combo_nodos.value == "" || combo_nodos.value == null)
    {
        alert("Falta llenar algunos campos");
        return false;
    }
    else
    {

        if (document.getElementById("telconet_schemabundle_infoelementoolttype_rackElementoId").value != "")
        {
            if (combo_rack.value == "" || combo_rack.value == null)
            {
                alert("Falta seleccionar el Rack");
                return false;
            }
            else
            {
                if (document.getElementById("telconet_schemabundle_infoelementoolttype_unidadRack").value == "" ||
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