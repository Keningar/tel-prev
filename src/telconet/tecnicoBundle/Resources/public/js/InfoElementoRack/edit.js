Ext.onReady(function() {

    var Url = location.href;
    UrlUrl = Url.replace(/.*\?(.*?)/, "$1");
    Variables = Url.split("/");
    var n = Variables.length;
    var idDslam = Variables[n - 2];

    var storeNodo = new Ext.data.Store({
        total: 'total',
        autoLoad: true,
        proxy: {
            timeout: 400000,
            type: 'ajax',
            url: url_getEncontradosNodo,
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
                    $('#telconet_schemabundle_infoelementoracktype_nodoElementoId').val(combo.getValue());

                }}
        }
    });



    var conn = new Ext.data.Connection();
    conn.request
        (
            {
                url: url_obtenerDatosRack,
                method: 'post',
                params: {idRack: idDslam},
                success: function(response)
                {
                    var json = Ext.JSON.decode(response.responseText);

                    if (json.total > 0) {
                        agregarValue("telconet_schemabundle_infoelementoracktype_nodoElementoId", json.encontrados[0]['popElementoId']);
                        combo_nodos.setValue(json.encontrados[0]['popElementoId']);
                    }
                    else {
                        alert("sin datos");
                    }
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);

                }
            }
        );


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

function agregarValue(campo, valor) {
    document.getElementById(campo).value = valor;
}

function validacionesForm() {
    //validar nombre caja
    if (document.getElementById("telconet_schemabundle_infoelementoracktype_nombreElemento").value == "") {
        alert("Falta llenar algunos campos");
        return false;
    }

    //validar modelo
    if (document.getElementById("telconet_schemabundle_infoelementoracktype_modeloElementoId").value == "") {
        alert("Falta llenar algunos campos");
        return false;
    }

    //validar nodo
    if (combo_nodos.value == "" || combo_nodos.value == null) {
        alert("Falta llenar algunos campos");
        return false;
    }

    return true;
}