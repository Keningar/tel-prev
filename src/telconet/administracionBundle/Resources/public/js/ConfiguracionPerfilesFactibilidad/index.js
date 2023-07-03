/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var id_departamento = '';
var eliminados = [];
var pageSize = 10;
Ext.QuickTips.init();
Ext.onReady(function () {
    Ext.tip.QuickTipManager.init();
    storePerfiles = new Ext.data.Store({
        pageSize: pageSize,
        total: 'total',
        autoLoad: true,
        proxy:
        {
            type: 'memory',
            enablePaging: true,
            data: []
        },
        fields:
            [
                { name: 'en_base', mapping: 'en_base' },
                { name: 'id_empleado', mapping: 'id_empleado' },
                { name: 'nombre_empleado', mapping: 'nombre_empleado' },
                { name: 'id_jurisdiccion', mapping: 'id_jurisdiccion' },
                { name: 'nombre_jurisdiccion', mapping: 'nombre_jurisdiccion' },
                { name: 'id_canton', mapping: 'id_canton' },
                { name: 'nombre_canton', mapping: 'nombre_canton' },
                { name: 'id_departamento', mapping: 'id_departamento' },
                { name: 'nombre_departamento', mapping: 'nombre_departamento' }

            ]
    });
    storePerfilesCopy = new Ext.data.Store({
        pageSize: pageSize,
        total: 'total',
        autoLoad: true,
        proxy:
        {
            type: 'memory',
            enablePaging: true,
            data: []
            // url: 'grid',
        },
        fields:
            [
                { name: 'en_base', mapping: 'en_base' },
                { name: 'id_empleado', mapping: 'id_empleado' },
                { name: 'nombre_empleado', mapping: 'nombre_empleado' },
                { name: 'id_jurisdiccion', mapping: 'id_jurisdiccion' },
                { name: 'nombre_jurisdiccion', mapping: 'nombre_jurisdiccion' },
                { name: 'id_canton', mapping: 'id_canton' },
                { name: 'nombre_canton', mapping: 'nombre_canton' },
                { name: 'id_departamento', mapping: 'id_departamento' },
                { name: 'nombre_departamento', mapping: 'nombre_departamento' }

            ]
    });

    gridPerfiles = Ext.create('Ext.grid.Panel', {
        id: 'gridPerfiles',
        width: 800,
        height: 400,
        store: storePerfiles,
        viewConfig:
        {
            enableTextSelection: true,
            loadingText: '<b>Cargando Perfiles, Por favor espere...',
            emptyText: '<center><br/><b/>*** No se encontraron Perfiles ***',
            loadMask: true
        },
        loadMask: true,
        frame: false,
        listeners:
        {
            sortchange: function () {
                gridPerfiles.getPlugin('pagingSelectionPersistence').clearPersistedSelection();
            }
        },
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            align: '->',
            items: [
                { xtype: 'tbfill' }, // alinea los items siguientes a la derecha
                {
                    iconCls: 'icon_add',
                    text: 'Agregar',
                    id: 'add',
                    itemId: 'add',
                    disabled: true,
                    scope: this,
                    handler: function () { agregar(); }
                },
            ]
        }
        ],
        columns: [
            {
                id: 'id',
                header: 'ID',
                dataIndex: 'id',
                hideable: false,
                hidden: true,
                width: 30
            },
            {
                id: 'en_base',
                header: 'Base',
                dataIndex: 'en_base',
                hideable: false,
                hidden: true,
                width: 30
            },
            {
                id: 'nombre_departamento',
                header: 'Departamento',
                dataIndex: 'nombre_departamento',
                width: 220
            },
            {
                id: 'id_departamento',
                header: 'ID Departamento',
                dataIndex: 'id_departamento',
                hideable: false,
                hidden: true,
                width: 100
            },
            {
                id: 'nombre_empleado',
                header: 'Usuario',
                dataIndex: 'nombre_empleado',
                width: 200
            },
            {
                id: 'id_empleado',
                header: 'ID Empleado',
                dataIndex: 'id_empleado',
                hideable: false,
                hidden: true,
                width: 100
            },
            {
                id: 'nombre_jurisdiccion',
                header: 'Jurisdicción',
                dataIndex: 'nombre_jurisdiccion',
                width: 150
            },
            {
                id: 'id_jurisdiccion',
                header: 'ID Jurisdiccion',
                dataIndex: 'id_jurisdiccion',
                hideable: false,
                hidden: true,
                width: 100
            },
            {
                xtype: 'actioncolumn',
                header: 'Acción',
                width: 100,
                items: [
                    {
                        iconCls: 'button-grid-delete',
                        tooltip: 'Eliminar',
                        handler: function (grid, rowIndex, colIndex) {
                            var rec = grid.getStore().getAt(rowIndex);
                            const { id_canton, id_departamento, id_empleado, id_jurisdiccion } = rec.data;
                            const indexRemove = storePerfilesCopy.data.items.findIndex(element => {
                                const record = element.data;
                                if (record.id_canton == id_canton && record.id_departamento == id_departamento &&
                                    record.id_empleado == id_empleado && record.id_jurisdiccion == id_jurisdiccion) {
                                    return true;
                                }
                                return false;
                            });
                            storePerfilesCopy.proxy.data.splice(indexRemove, 1);
                            storePerfilesCopy.reload();
                            storePerfiles.proxy.data = storePerfilesCopy.proxy.data.slice(0, pageSize);
                            storePerfiles.reload();
                            eliminados.push(rec.data);
                        }

                    }
                ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storePerfilesCopy,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar.",
            listeners: {
                change: {
                    fn: function (barra, value) {
                        if (value) {
                            const recordsCount = (value.fromRecord - 1);
                            storePerfiles.proxy.data = storePerfilesCopy.proxy.data.slice(recordsCount, (pageSize + recordsCount));
                            storePerfiles.reload();
                        }
                    }
                }
            }
        }),
        renderTo: 'grid'
    });

    //Data de los combos

    storeCiudades = new Ext.data.Store
        ({
            total: 'total',
            pageSize: 200,
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: strUrlCiudadesEmpresa,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams:
                {
                    nombre: '',
                    estado: 'Activo'
                }
            },
            fields:
                [
                    { name: 'id_canton', mapping: 'id_canton' },
                    { name: 'nombre_canton', mapping: 'nombre_canton' }
                ]
        });

    storeJurisdicciones = new Ext.data.Store
        ({
            total: 'total',
            pageSize: 200,
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: strUrlComboJurisdiccion,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados',
                },
                extraParams:
                {
                    nombre: '',
                    id_departamento: ''
                }
            },
            fields:
                [
                    { name: 'id_jurisdiccion', mapping: 'id_jurisdiccion' },
                    { name: 'nombre_jurisdiccion', mapping: 'nombre_jurisdiccion' }
                ]
        });


    storeDepartamentos = new Ext.data.Store
        ({
            total: 'total',
            pageSize: 200,
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: strUrlComboDepartamento,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados',
                },
                extraParams:
                {
                    nombre: '',
                    estado: 'Activo'
                }
            },
            fields:
                [
                    { name: 'id_departamento', mapping: 'id_departamento' },
                    { name: 'nombre_departamento', mapping: 'nombre_departamento' }
                ]
        });


    storeEmpleado = new Ext.data.Store
        ({
            total: 'total',
            autoLoad: true,
            proxy:
            {
                type: 'ajax',
                url: strUrlEmpleadosDepartamentCiudad,

                reader:
                {
                    type: 'json',
                    totalProperty: 'result.total',
                    root: 'result.encontrados',
                    metaProperty: 'myMetaData'
                }
            },
            fields:
                [
                    { name: 'id_empleado', mapping: 'id_empleado' },
                    { name: 'nombre_empleado', mapping: 'nombre_empleado' }
                ]
        });

    function presentarEmpleadosXDepartamentoCiudad(id_departamento, id_canton, empresa, valorIdDepartamento) {
        storeEmpleado.proxy.extraParams = {
            id_canton: id_canton,
            empresa: empresa,
            id_departamento: id_departamento,
            departamento_caso: valorIdDepartamento
        };
        storeEmpleado.load();
    }

    //Combobox de empleados
    combo_empleados = new Ext.form.ComboBox({
        id: 'comboEmpleado',
        name: 'comboEmpleado',
        fieldLabel: "Empleado",
        store: storeEmpleado,
        displayField: 'nombre_empleado',
        valueField: 'id_empleado',
        queryMode: "remote",
        emptyText: '',
        labelWidth: '9',
        width: 250,
        disabled: true,
        listeners: {
            select: async function (e) {
                const wait = Ext.MessageBox.wait("Consultando registro(s)...", "Espere...");
                await storeJurisdicciones.load();
                await buscar(wait);
            }
        }
    });


    //Boton guardar
    var button = Ext.create('Ext.Button', {
        text: 'Guardar',
        padding: 5,
        handler: function () {
            if (eliminados.length == 0 && storePerfilesCopy.proxy.data.length == 0)
            {
                Ext.Msg.alert('Error', 'No hay registros para actualizar.');
                return;
            } 
            const usuarios = new Set();
            storePerfilesCopy.data.items.forEach(element => {
                usuarios.add(element.data.id_empleado.split("@@")[0]);
            }
            );
            const stringListUsers = Array.from(usuarios).join();
            const dataInsert = storePerfilesCopy.proxy.data.filter(el => el.en_base == "");
            const dataDelete = eliminados.filter(el => el.en_base != "");
            const idDepartamento = id_departamento;
            const wait = Ext.MessageBox.wait("Guardando registro(s)...", "Espere...");
            Ext.Ajax.request({
                url: enviarABase,
                method: 'post',
                params:{
                    array: JSON.stringify(dataInsert),
                    arrayEliminados: JSON.stringify(dataDelete),
                    arrayUsuarios: stringListUsers,
                    id_departamento: idDepartamento
                },
                success: function(response){
                    let responseJSON = "seleccionada";
                    if (response.responseText != "") {
                        try {
                            responseJSON = JSON.parse(response.responseText).id;
                        } catch (error) {
                            responseJSON = "seleccionada";
                        }
                    }
                    wait.hide();
                    Ext.Msg.alert('Éxito', `Los registros se actualizaron para la empresa ${responseJSON}`);
                    storePerfiles.proxy.data = [];
                    storePerfilesCopy.proxy.data = [];
                    storePerfiles.reload();
                    storePerfilesCopy.reload();
                    eliminados = [];
                    Ext.getCmp("comboDepartamento").reset();
                    Ext.getCmp("comboDepartamento").setDisabled(true);
                    Ext.getCmp("comboEmpleado").reset();
                    Ext.getCmp("comboEmpleado").setDisabled(true);
                    Ext.getCmp('comboCiudad').reset();
                    Ext.getCmp('comboJurisdiccion').reset();
                },
                failure: function() {	
                    wait.hide();			
                    Ext.Msg.alert('Alerta ','Error al realizar la acción');
                }
            });
        },
        renderTo: 'button-guardar'

    });



    // /************************************************************************************/
    //
    //			criterio de busqueda de perfiles factibilidad
    //
    /************************************************************************************/
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,  // Don't want content to crunch against the borders
        //bodyBorder: false,
        border: false,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 4
        },
        bodyStyle: {
            background: '#fff'
        },

        collapsible: true,
        collapsed: false,
        width: 800,
        title: 'Criterios de búsqueda',
        buttons:
            [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: async function () {
                        const wait = Ext.MessageBox.wait("Consultando registro(s)...", "Espere...");
                        await storeJurisdicciones.load();
                        await buscar(wait);
                    }
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function () {
                        Ext.getCmp("comboDepartamento").reset();
                        Ext.getCmp("comboDepartamento").setDisabled(true);
                        Ext.getCmp("comboEmpleado").reset();
                        Ext.getCmp("comboEmpleado").setDisabled(true);
                        Ext.getCmp('comboCiudad').reset();
                        Ext.getCmp('comboJurisdiccion').reset();
                        storePerfiles.data.items = [];
                        storePerfiles.proxy.data = [];
                        storePerfilesCopy.data.items = [];
                        storePerfilesCopy.proxy.data = [];
                        storePerfilesCopy.reload();
                        storePerfiles.reload();
                        eliminados = [];
                        Ext.getCmp("add").setDisabled(true);

                    }
                }
            ],
        items:
            [
                { width: 60, border: false },
                {
                    xtype: 'combobox',
                    fieldLabel: 'Ciudad',
                    id: 'comboCiudad',
                    name: 'comboCiudad',
                    store: storeCiudades,
                    displayField: 'nombre_canton',
                    valueField: 'id_canton',
                    queryMode: "remote",
                    disabled: false,
                    width: 250,
                    labelWidth: '9',
                    emptyText: '',
                    listeners:
                    {
                        select: function (combo) {
                            if (combo.getValue()) {
                                Ext.getCmp("comboDepartamento").reset();
                                Ext.getCmp("comboDepartamento").setDisabled(false);
                                Ext.getCmp('comboEmpleado').reset();
                                Ext.getCmp('comboEmpleado').setDisabled(true);
                            }
                        }
                    },
                    forceSelection: true
                },
                { width: 60, border: false },
                combo_empleados,
                { width: 60, border: false },
                {
                    xtype: 'combobox',
                    fieldLabel: 'Departamento',
                    id: 'comboDepartamento',
                    name: 'comboDepartamento',
                    store: storeDepartamentos,
                    displayField: 'nombre_departamento',
                    valueField: 'id_departamento',
                    queryMode: "remote",
                    disabled: true,
                    width: 250,
                    labelWidth: '9',
                    emptyText: '',
                    minChars: 3,
                    listeners:
                    {
                        select: function (combo) {
                            if (combo.getValue()) {
                                Ext.getCmp('comboEmpleado').reset();
                                Ext.getCmp("comboEmpleado").setDisabled(false);
                                canton = Ext.getCmp('comboCiudad').getValue();
                                presentarEmpleadosXDepartamentoCiudad(combo.getValue(), canton, codEmpresa);
                                storeJurisdicciones.proxy.extraParams.id_departamento = combo.getValue();
                                id_departamento = combo.getValue();
                                //Ext.getCmp('comboJurisdiccion').reset();
                            }
                        }
                    },
                    forceSelection: true
                },
                { width: 60, border: false },
                {
                    xtype: 'combobox',
                    fieldLabel: 'Jurisdicción:',
                    id: 'comboJurisdiccion',
                    name: 'comboJurisdiccion',
                    store: storeJurisdicciones,
                    displayField: 'nombre_jurisdiccion',
                    valueField: 'id_jurisdiccion',
                    queryMode: "remote",
                    width: 250,
                    labelWidth: '9',
                    emptyText: '',
                    listeners: {
                    },
                    forceSelection: true
                },

            ]
        ,
        renderTo: 'filtro'

    });
    // storeJurisdicciones.load();

});




function findEmpleadoByParameters(idCiudad, idDepartamento, idEmpleado, idJurisdiccion) {
    return storePerfilesCopy.data.items.findIndex(element => {
        const record = element.data;
        if (record.id_canton == idCiudad && record.id_departamento == idDepartamento &&
            record.id_empleado == idEmpleado && record.id_jurisdiccion == idJurisdiccion) {
            return true;
        }
        return false;
    });
}

function findEmpledoByParametersEliminados(idCiudad, idDepartamento, idEmpleado, idJurisdiccion) {
    return eliminados.findIndex(el => {
        if (el.id_ciudad == idCiudad && el.id_departamento == idDepartamento && el.id_empleado == idEmpleado && el.id_jurisdiccion == idJurisdiccion) {
            return true;
        }
        return false;
    });
}

function agregarInterna(idJurisdiccion, nombreJurisdiccion, enBase = "", showAlert = true) {
    record = {
        id_empleado: empleado.getValue(),
        nombre_empleado: empleado.getRawValue(),
        id_canton: ciudad.getValue(),
        nombre_canton: ciudad.getRawValue(),
        id_departamento: departamento.getValue(),
        nombre_departamento: departamento.getRawValue(),
        id_jurisdiccion: idJurisdiccion,
        nombre_jurisdiccion: nombreJurisdiccion,
        en_base: enBase
    };
    const indexEliminado = findEmpledoByParametersEliminados(record.id_canton, record.id_departamento, record.id_empleado, record.id_jurisdiccion);
    if (indexEliminado > -1) {
        record.en_base = eliminados[indexEliminado].en_base;
        eliminados.splice(indexEliminado, 1);
        storePerfilesCopy.proxy.data.unshift(record);
        storePerfilesCopy.reload();
        if (storePerfiles.getCount() < pageSize) {
            storePerfiles.proxy.data = storePerfilesCopy.proxy.data.slice(0, pageSize);
            storePerfiles.reload();
        }
    }
    else if (findEmpleadoByParameters(record.id_canton, record.id_departamento, record.id_empleado, record.id_jurisdiccion) == -1) {
        storePerfilesCopy.proxy.data.unshift(record);
        storePerfilesCopy.reload();
        if (storePerfiles.getCount() < pageSize) {
            storePerfiles.proxy.data = storePerfilesCopy.proxy.data.slice(0, pageSize);
            storePerfiles.reload();
        }
    } else {
        if (showAlert) {
            Ext.Msg.alert('Error', 'El registro ya existe', Ext.emptyFn);
        }
    }
}

function agregar() {

    storeJurisdicciones.load();
    jurisdiccion = Ext.getCmp('comboJurisdiccion');
    ciudad = Ext.getCmp('comboCiudad');
    departamento = Ext.getCmp('comboDepartamento');
    empleado = combo_empleados;

    if (ciudad.getValue() != null && departamento.getValue() != null && empleado.getValue() != null && jurisdiccion.getValue() != null) {
        if (jurisdiccion.getValue() == -1) {
            Ext.MessageBox.wait("Agregando registro(s)...", "Espere...");
            setTimeout(() => {
                storeJurisdicciones.data.items.forEach(el => {
                    if (el.data.id_jurisdiccion != -1) {
                        agregarInterna(el.data.id_jurisdiccion, el.data.nombre_jurisdiccion, "", false);
                    }
                });
                Ext.MessageBox.hide();
            }, 2000);
        } else {
            agregarInterna(jurisdiccion.getValue(), jurisdiccion.getRawValue(), "");
        }
    } else {
        Ext.Msg.alert('Error', 'Debes seleccionar todos los campos para agregar un registro.', Ext.emptyFn);
    }
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function buscar(wait) {

    ciudad = Ext.getCmp('comboCiudad');
    departamento = Ext.getCmp('comboDepartamento');
    empleado = combo_empleados;
    if (ciudad.getValue() == null || departamento.getValue() == null || empleado.getValue() == null)
    {
        Ext.Msg.alert('Error', 'Debe seleccionar un empleado para realizar la búsqueda.');
        wait.hide();
        return;
    }
    await storeJurisdicciones.load();
    //departamento = Ext.getCmp('comboDepartamento');
    Ext.Ajax.request({
        url: obtenerDeBase,
        method: 'get',
        params:{
            idEmpleado: combo_empleados.getValue()
        },
        success: async function(response){
            await storeJurisdicciones.load();
            await sleep(2000);
            eliminados = [];
            storePerfilesCopy.proxy.data = []
            storePerfilesCopy.reload();
            storePerfiles.proxy.data = [];
            storePerfiles.reload();
            const jurisdicciones = JSON.parse(response.responseText).encontrados;
            ciudad = Ext.getCmp('comboCiudad');
            departamento = Ext.getCmp('comboDepartamento');
            empleado = combo_empleados;
            jurisdicciones.forEach( element => {
                const id = Number(element.id_jurisdiccion);
                storeJurisdicciones.data.items.forEach( item => {
                    if (item.data.id_jurisdiccion == id){
                        agregarInterna(id, item.data.nombre_jurisdiccion, element.id_registro);
                    }
                });
            });
            Ext.getCmp("add").setDisabled(false);
            wait.hide();
            // storePerfiles.reload();
        },
        failure: function() {				
            Ext.Msg.alert('Alerta ','Error al realizar la acción');
            wait.hide();
        }
    });
    // if (storeJurisdicciones.getCount() > 0) {
        
    // } else {
    //     wait.hide();
    //     Ext.Msg.alert('Información ','Error al realizar la acción, verifique que se hayan cargado las jurisdicciones previamente.');
    // }
}