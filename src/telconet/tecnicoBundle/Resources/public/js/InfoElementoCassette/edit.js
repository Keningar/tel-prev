Ext.onReady(function() {

    Ext.define('tipoCaracteristica', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'tipo', type: 'string'}
        ]
    });

    storeElementosA = new Ext.data.Store({
        pageSize: 100,
        autoload: false,
        listeners: {
            load: function() {

            }
        },
        proxy: {
            type: 'ajax',
            url: url_buscarElementoContenedor,
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

    var storeTipoElementoCaja = new Ext.data.Store({
        model: 'tipoCaracteristica',
        data: [
            {tipo: 'CAJA DISPERSION'}
        ]
    });

    comboContenido = new Ext.form.ComboBox({
        id: 'cmb_contenido',
        name: 'cmb_contenido',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'local',
        width: 300,
        emptyText: 'Seleccione Tipo',
        store: storeTipoElementoCaja,
        displayField: 'tipo',
        valueField: 'tipo',
        renderTo: 'comboContenido',
        disabled: true
    });
    comboContenido.select(comboContenido.getStore().collect(comboContenido.valueField));


    comboElementos = new Ext.form.ComboBox({
        id: 'cmb_elementoA',
        name: 'cmb_elementoA',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'remote',
        width: 300,
        emptyText: 'Seleccione Elemento',
        store: storeElementosA,
        displayField: 'nombre_elemento',
        valueField: 'id_elemento',
        renderTo: 'comboElemento'
    });
    storeElementosA.proxy.extraParams = {tipoElemento: comboContenido.getValue(), nombreElemento: strNombreElementoContenedor};
    storeElementosA.load({params: {}});

    comboElementos.setValue(parseInt(strElementoContenedor));

});


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

    var elemento = Ext.getCmp('cmb_elementoA').getValue();
    $("#telconet_schemabundle_infoelementocassettetype_elementoContenedorId").val(elemento);

    //validar contenedor
    if (document.getElementById("telconet_schemabundle_infoelementocassettetype_elementoContenedorId").value == "") {
        alert("Falta llenar algunos campos, contenedor");
        return false;
    }

    return true;
}

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