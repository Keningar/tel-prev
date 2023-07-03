var storeTareas = undefined;

var comboCiudades = undefined;
var comboDepartamentos = undefined;
var comboTareas = undefined;
var comboEmpleados = undefined;
var comboNodo = undefined;
var comboElemento = undefined;


Ext.onReady(function() {
    storeTareas = new Ext.data.Store ({
       total: 'total',        
       proxy: 
       {
           type: 'ajax',
           timeout: 180000,
           url: strUrlGetTareas,
           reader: 
           {
               type: 'json',
           }
       },
       fields:
       [
           {name: 'idTarea', mapping: 'id'}
       ]
    });

    storeElementoNodo = new Ext.data.Store ({
        total: 10,
        proxy: 
        {
            type: 'ajax',
            timeout: 180000,
            url: strUrlGetElementosBitacora,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            }
        },
        listeners: {
            beforeload: function(sender, options )
            {
                $('#telconet_schemabundle_infobitacoraaccesonodotype_elementoNodoNombre').val("");
            }
        },
        fields:
        [
            {name: 'nodo_id',         mapping: 'elemento_id'},
            {name: 'nombre_elemento', mapping: 'nombre_elemento'}
        ]
     });
    
    storeElementoRelacionado = new Ext.data.Store ({
        total: 10, 
        proxy: 
        {
            type: 'ajax',
            timeout: 180000,
            url: strUrlGetElementosBitacora,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            }
        },
        listeners: {
            beforeload: function(sender, options )
            {
                $('#telconet_schemabundle_infobitacoraaccesonodotype_elemento').val("");
            }
        },
        fields:
        [
            {name: 'elemento_id',                 mapping: 'elemento_id'},
            {name: 'nombre_elemento_relacionado', mapping: 'nombre_elemento'}
        ]
    });

    storeDetalleTarea = new Ext.data.Store({
        pageSize: 40,
        model: 'TareaDetalle',
        proxy: {
            type: 'ajax',
            timeout: 180000,
            url: strUrlGetTareaDetalle,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idTarea: '',
            }
        },
        fields:
        [
            {name: 'id_persona',      mapping: 'id_persona'},
            {name: 'nombres',         mapping: 'nombres'},
            {name: 'canton',          mapping: 'canton'},
            {name: 'nodo_id',         mapping: 'nodo_id'},
            {name: 'nombre_elemento', mapping: 'nombre_elemento'},
            {name: 'login',           mapping: 'login'},
            {name: 'id_departamento', mapping: 'id_departamento'},
            {name: 'departamento',    mapping: 'departamento'},
            {name: 'telefono',        mapping: 'telefono'},
            {name: 'elemento_id',     mapping: 'elemento_id'},
            {name: 'nombre_elemento_relacionado', mapping: 'nombre_elemento_relacionado'}
        ],
        listeners: {
            beforeload: function(sender, options )
            {
                limpiar();
                Ext.MessageBox.show({
                    msg: 'Cargando los datos, Por favor espere!!',
                    progressText: 'Saving...',
                    width:250,
                    wait:true,
                    waitConfig: {interval:250}
                });
            },
            load: function(sender, node, records) {
                Ext.MessageBox.hide();
                if(storeDetalleTarea.getCount()>0){			
                    var rec = storeDetalleTarea.getAt(0);
                    Ext.getCmp('combo_ciudades').setValue(rec.get('canton'));
                    Ext.getCmp('combo_departamentos').setValue(rec.get('departamento'));
                    Ext.getCmp('combo_empleados').setValue(rec.get('nombres'));

                    Ext.getCmp('combo_nodo').setReadOnly(false);
                    Ext.getCmp('combo_elemento').setReadOnly(false);

                    $("#telconet_schemabundle_infobitacoraaccesonodotype_canton").val(rec.get('canton'));
                    $('#telconet_schemabundle_infobitacoraaccesonodotype_departamento').val(rec.get('id_departamento'));
                    $('#telconet_schemabundle_infobitacoraaccesonodotype_tecnicoAsignado').val(rec.get('login'));
                    $('#telconet_schemabundle_infobitacoraaccesonodotype_telefono').val(rec.get('telefono'));
                    if (!rec.get('login')) 
                    {
                        Ext.Msg.alert("Error", "No se encontró información asociada a la tarea");
                    }
                    if(rec.get('elemento_id'))
                    {
                        Ext.getCmp('combo_elemento').setValue(rec.get('nombre_elemento_relacionado'));
                        $('#telconet_schemabundle_infobitacoraaccesonodotype_elemento').val(rec.get('elemento_id'));
                    }
                    
                    if(rec.get('nodo_id'))
                    {
                        Ext.getCmp('combo_nodo').setValue(rec.get('nombre_elemento'));
                        $('#telconet_schemabundle_infobitacoraaccesonodotype_elementoNodoNombre').val(rec.get('nodo_id'));
                    }
                }
            }
        }
    });

    function limpiar(){
        Ext.getCmp('combo_ciudades').value="";
        Ext.getCmp('combo_ciudades').setRawValue("");

        Ext.getCmp('combo_departamentos').value="";
        Ext.getCmp('combo_departamentos').setRawValue("");

        Ext.getCmp('combo_empleados').value="";
        Ext.getCmp('combo_empleados').setRawValue("");

        Ext.getCmp('combo_nodo').value="";
        Ext.getCmp('combo_nodo').setRawValue("");

        Ext.getCmp('combo_elemento').value="";
        Ext.getCmp('combo_elemento').setRawValue("");

        Ext.get('telconet_schemabundle_infobitacoraaccesonodotype_telefono').dom.value="";
        Ext.get('telconet_schemabundle_infobitacoraaccesonodotype_codigos').dom.value="";
        Ext.get('telconet_schemabundle_infobitacoraaccesonodotype_observacion').dom.value="";

        $("#telconet_schemabundle_infobitacoraaccesonodotype_canton").val("");
        $('#telconet_schemabundle_infobitacoraaccesonodotype_departamento').val("");
        $('#telconet_schemabundle_infobitacoraaccesonodotype_tecnicoAsignado').val("");
        $('#telconet_schemabundle_infobitacoraaccesonodotype_elementoNodoNombre').val("");
        $('#telconet_schemabundle_infobitacoraaccesonodotype_telefono').val("");
        $('#telconet_schemabundle_infobitacoraaccesonodotype_observacion').val("");
        $('#telconet_schemabundle_infobitacoraaccesonodotype_codigos').val("");
        $('#telconet_schemabundle_infobitacoraaccesonodotype_elemento').val("");
    }

    comboTareas = new Ext.form.ComboBox({
        id: 'combo_tareas',
        name: 'combo_tareas',
        width: 250,
        minChars : 4,
        fieldLabel: false,
        store: storeTareas,
        displayField: 'idTarea',
        valueField: 'idTarea',
        queryMode: 'remote',
        emptyText: '',
        renderTo: 'combo_tareas',
        listeners: {
            select: { fn: function(combo, value) {
                $('#telconet_schemabundle_infobitacoraaccesonodotype_tareaId').val(combo.getValue());
                storeDetalleTarea.proxy.extraParams = {idTarea: combo.getValue()};
                storeDetalleTarea.load();
            }}
        }
    });

    comboCiudades = new Ext.form.TextField({
        id: 'combo_ciudades',
        name: 'combo_ciudades',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'remote',
        minChars : 2,
        width: 250,
        displayField: 'canton',
        valueField: 'idCanton',
        renderTo: 'combo_ciudades',
        readOnly: true
    });

    comboDepartamentos = new Ext.form.TextField({
        id: 'combo_departamentos',
        name: 'combo_departamentos',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'remote',
        minChars : 2,
        width: 250,
        displayField: 'departamento',
        valueField: 'idDepartamento',
        renderTo: 'combo_departamentos',
        readOnly: true
    });

    comboEmpleados = new Ext.form.TextField({
        id: 'combo_empleados',
        name: 'combo_empleados',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'remote',
        minChars : 2,
        width: 250,
        displayField: 'tecnico',
        valueField: 'login',
        renderTo: 'combo_empleados',
        readOnly: true
    });

    comboNodo = new Ext.form.ComboBox({
        id: 'combo_nodo',
        name: 'combo_nodo',
        width: 250,
        minChars : 5,
        fieldLabel: false,
        store: storeElementoNodo,
        displayField: 'nombre_elemento',
        valueField: 'nodo_id',
        queryMode: 'remote',
        emptyText: '',
        renderTo: 'combo_nodo',
        readOnly: true,
        listeners: {
            select: { fn: function(combo, value) {
                $('#telconet_schemabundle_infobitacoraaccesonodotype_elementoNodoNombre').val(combo.getValue());
            }},
            change: { fn: function(value) {
                txtNodo = value.rawValue
                if(txtNodo.length == 0){
                    $('#telconet_schemabundle_infobitacoraaccesonodotype_elementoNodoNombre').val("");
                }
            }},
        }
    });

    comboElemento = new Ext.form.ComboBox({
        id: 'combo_elemento',
        name: 'combo_elemento',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'remote',
        minChars : 5,
        store: storeElementoRelacionado,
        width: 250,
        displayField: 'nombre_elemento_relacionado',
        valueField: 'elemento_id',
        renderTo: 'combo_elemento',
        readOnly: true,
        listeners: {
            select: { fn: function(combo, value) {
                $('#telconet_schemabundle_infobitacoraaccesonodotype_elemento').val(combo.getValue());
            }}
        }
    });

});


function validacionesForm() {

    if (document.getElementById("telconet_schemabundle_infobitacoraaccesonodotype_tareaId").value == '') 
    {
        Ext.Msg.alert("Warning", "Falta escoger la tarea");
        return false;
    }
    
    if (document.getElementById("telconet_schemabundle_infobitacoraaccesonodotype_canton").value == '') 
    {
        Ext.Msg.alert("Warning", "Falta escoger la Ciudad");
        return false;
    }

    if (document.getElementById("telconet_schemabundle_infobitacoraaccesonodotype_departamento").value == '') 
    {
        Ext.Msg.alert("Warning", "Falta escoger el Departamento");
        return false;
    }

    if (document.getElementById("telconet_schemabundle_infobitacoraaccesonodotype_tecnicoAsignado").value == '') 
    {
        Ext.Msg.alert("Warning", "Falta llenar el campo Empleado");
        return false;
    }

    if (document.getElementById("telconet_schemabundle_infobitacoraaccesonodotype_elementoNodoNombre").value == '') 
    {
        Ext.Msg.alert("Warning", "Falta escoger el Elemento Nodo");
        return false;
    }

    return true;
}

function verificarSoloNumeros(e)
{
    var k = (document.all) ? e.keyCode : e.which;

    if (k==8 || k==0)
    {
        return true;
    }

    var patron = /[0-9 ]/;
    var n = String.fromCharCode(k);

    return patron.test(n);
}