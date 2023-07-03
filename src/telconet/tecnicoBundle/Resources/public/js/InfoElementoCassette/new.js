Ext.onReady(function() {
    var storeTipoElementoCaja = new Ext.data.Store({
        total: 'total',
        autoLoad: true,
        proxy: {
            async: false,
            type: 'ajax',
            url: url_getContenido,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'nombreTipo', mapping: 'nombreTipo'}
            ]
    });
    
    var storeElementosA = new Ext.data.Store({
        pageSize: 100,
        total: 'total',
        autoLoad: true,
        proxy: {
            async: false,
            type: 'ajax',
            url:  url_buscarElementoContenedor,
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
                    {name: 'id_elemento', mapping: 'id_elemento'},
                    {name: 'nombre_elemento', mapping: 'nombre_elemento'}
                ]
    });
    
    comboContenido = new Ext.form.ComboBox({
        id: 'comboContenido',
        name: 'comboContenido',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'local',
        width: 300,
        emptyText: 'Seleccione Tipo',
        store: storeTipoElementoCaja,
        displayField: 'nombreTipo',
        valueField: 'nombreTipo',
        renderTo: 'comboContenido',
        listeners: {
            select: {fn: function(combo, value) {
                    $('#telconet_schemabundle_infoelementocassettetype_contenidoEn').val(combo.getValue());
                    $('#telconet_schemabundle_infoelementocassettetype_elementoContenedorId').val('');
                    Ext.getCmp('comboElemento').reset();
                    Ext.getCmp('comboElemento').setDisabled(false);
                    presentarElementos(combo.getValue());

                }}},
        disabled: false,
        forceSelection: true
    });
    
    comboElemento = new Ext.form.ComboBox({
        id: 'comboElemento',
        name: 'comboElemento',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'remote',
        width: 300,
        emptyText: 'Seleccione Elemento',
        store: storeElementosA,
        displayField: 'nombre_elemento',
        valueField: 'id_elemento',
        renderTo: 'comboElemento',
        listeners: {
            select: {fn: function(combo, value) {
                    $('#telconet_schemabundle_infoelementocassettetype_elementoContenedorId').val(combo.getValue());
                }}
        }
    });
    
    storeTipoElementoCaja.load({
        callback: function() {
                           
        }
    });
    
    function presentarElementos(nombreElemento) {
        storeElementosA.proxy.extraParams = {tipoElemento: nombreElemento};
        storeElementosA.load();
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
        especiales = [8, 36, 35, 45, 47, 40, 41, 46, 32];
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
    //validar nombre caja
    if (document.getElementById("telconet_schemabundle_infoelementocassettetype_nombreElemento").value == "") {
        alert("Falta llenar algunos campos");
        return false;
    }

    if (document.getElementById("telconet_schemabundle_infoelementocassettetype_descripcionElemento").value == "") {
        alert("Falta llenar algunos campos");
        return false;
    }

    //validar modelo
    if (document.getElementById("telconet_schemabundle_infoelementocassettetype_modeloElementoId").value == "") {
        alert("Falta llenar algunos campos");
        return false;
    }

    var elemento = Ext.getCmp('comboElemento').getValue();
    $("#telconet_schemabundle_infoelementocassettetype_elementoContenedorId").val(elemento);

    //validar contenedor
    if (document.getElementById("telconet_schemabundle_infoelementocassettetype_elementoContenedorId").value == "") {
        alert("Falta llenar algunos campos, contenedor");
        return false;
    }

    return true;
}
