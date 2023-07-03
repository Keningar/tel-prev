Ext.onReady(function() {

    var Url = location.href;
    UrlUrl = Url.replace(/.*\?(.*?)/, "$1");
    Variables = Url.split("/");
    var n = Variables.length;
    var idDslam = Variables[n - 2];
    
    // Si existe error se muestra temporalmente
    if (typeof msgErr !== typeof undefined && msgErr != '')
    {
        var obj = $("#lblEditarOdf").text(msgErr);
        obj.html(obj.html().replace(/\n/g, '<br/>'));
    
        setTimeout(function()
        {
            $('#msgEditarOdf').show(100);
        }, 0); // Tiempo que espera antes de ejecutar el código interno
        
        setTimeout(function()
        {
            $('#msgEditarOdf').hide(400);
        }, 6000); // Tiempo que espera antes de ejecutar el código interno
    }

    $("#msgEditarOdf").click(function()
    {
        $("#msgEditarOdf").hide(400); // Cerrar mensaje al dar click.
    });

    Ext.onReady(function() 
    {
        var nodoDef   = parseInt($('#telconet_schemabundle_infoelementoodftype_nodoElementoId').val());
        var rackDef   = parseInt($('#telconet_schemabundle_infoelementoodftype_rackElementoId').val());
        var unidadDef = parseInt($('#telconet_schemabundle_infoelementoodftype_unidadRack').val());

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
                    estado: 'Activo'
                },
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                limitParam: undefined,
                startParam: undefined
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
            queryMode: 'local',
            width: 250,
            emptyText: 'Seleccione Nodo',
            store: storeNodo,
            displayField: 'nombreElemento',
            valueField: 'idElemento',
            renderTo: 'combo_nodos',
            listeners: {
                select: {fn: function(combo) 
                    {
                        nodoDef = combo.getValue();
                        $('#telconet_schemabundle_infoelementoodftype_nodoElementoId').val(nodoDef);
                        $('#telconet_schemabundle_infoelementoodftype_rackElementoId').val('');
                        $('#telconet_schemabundle_infoelementoodftype_unidadRack').val('');
                        Ext.getCmp('combo_rack').reset();
                        Ext.getCmp('combo_unidades').reset();
                        Ext.getCmp('combo_rack').setDisabled(false);
                        Ext.getCmp('combo_unidades').setDisabled(true);
                        storeRack.proxy.extraParams = {popElemento: nodoDef, estado: 'Activo'};
                        storeRack.load();

                    }}}
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
                select: {fn: function(combo, value) 
                    {
                        rackDef = combo.getValue();
                        $('#telconet_schemabundle_infoelementoodftype_rackElementoId').val(rackDef);
                        $('#telconet_schemabundle_infoelementoodftype_unidadRack').val('');
                        Ext.getCmp('combo_unidades').reset();
                        Ext.getCmp('combo_unidades').setDisabled(false);
                        storeUnidadesDisponibles.proxy.extraParams = {idElemento: rackDef};
                        storeUnidadesDisponibles.load();
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
                select: {fn: function(combo, value) 
                    {
                        unidadDef = combo.getValue();
                        $('#telconet_schemabundle_infoelementoodftype_unidadRack').val(unidadDef);
                    }}
            }
        });
    
        storeNodo.on('load', function()
        {
            
            this.each(function(record)
            {
                if (parseInt(record.get('idElemento')) == nodoDef)
                {
                    combo_nodos.select(nodoDef, true);
                }
            });
            if(!isNaN(nodoDef))
            {
                Ext.getCmp('combo_rack').reset();
                Ext.getCmp('combo_rack').setDisabled(false);
                storeRack.proxy.extraParams = {popElemento: nodoDef, estado: 'Activo'};
                storeRack.load();
            }
        });
        
        storeRack.on('load', function()
        {
            this.each(function(record)
            {
                if (parseInt(record.get('idElemento')) == rackDef)
                {
                    combo_rack.select(rackDef, true);
                }
            });
            
            if(!isNaN(rackDef))
            {
                    Ext.getCmp('combo_unidades').reset();
                    Ext.getCmp('combo_unidades').setDisabled(false);
                    storeUnidadesDisponibles.proxy.extraParams = {idElemento: rackDef};
                    storeUnidadesDisponibles.load();
            }
        });
        
        storeUnidadesDisponibles.on('load', function()
        {
            this.each(function(record)
            {
                if (parseInt(record.get('idElemento')) == unidadDef)
                {
                    combo_unidades.select(unidadDef, true);
                }
            });
        });
        
        $('#telconet_schemabundle_infoelementoodftype_claseTipoMedioId').val(strClaseTipoMedio);

    });

});

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
    if (document.getElementById("telconet_schemabundle_infoelementoodftype_nombreElemento").value == "") 
    {
        alert("Falta llenar algunos campos");
        return false;
    }

    //validar modelo
    if (document.getElementById("telconet_schemabundle_infoelementoodftype_modeloElementoId").value == "") 
    {
        alert("Falta llenar algunos campos");
        return false;
    }
    
     //validar clase tipo medio
    if (document.getElementById("telconet_schemabundle_infoelementoodftype_claseTipoMedioId").value == "") 
    {
        alert("Falta llenar algunos campos");
        return false;
    }
    
    //validar nodo
    if (document.getElementById("telconet_schemabundle_infoelementoodftype_nodoElementoId").value == "" || 
        combo_nodos.value == "" || combo_nodos.value == null) 
    {
        alert("Falta llenar algunos campos");
        return false;
    }

    if (document.getElementById("telconet_schemabundle_infoelementoodftype_rackElementoId").value == "" ||
        combo_rack.value == "" || combo_rack.value == null)
    {
        alert("Falta seleccionar el Rack");
        return false;
    }

    if (document.getElementById("telconet_schemabundle_infoelementoodftype_unidadRack").value == "" ||
        combo_unidades.value == "" || combo_unidades.value == null)
    {
        alert("Falta seleccionar la unidad de rack");
        return false;
    }

    
    
    return true;
}