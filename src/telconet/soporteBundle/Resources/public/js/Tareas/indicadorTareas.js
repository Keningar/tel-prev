function actualizarIndicadoresTareas()
{
    Ext.Ajax.request({
        url: url_indicadorTareas,
        method: 'post',
        timeout: 9600000,
        params: {
            personaEmpresaRol : intPersonaEmpresaRolId
        },
        success: function(response) {
            var text = Ext.decode(response.responseText);
                $("#spanTareasDepartamento").text(text.tareasPorDepartamento);
                $("#spanTareasPersonales").text(text.tareasPersonales);
                $("#spanCasosMoviles").text(text.cantCasosMoviles);
                
        },
        failure: function(result)
        {
            Ext.MessageBox.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });
}

function seleccionarCuadrillas(arrayData,isRedisenioTarea) {

    var storeSeleccionCuadrillas = arrayData.storeSeleccionCuadrillas;
    var isRedisenio = (typeof isRedisenioTarea != 'undefined')?isRedisenioTarea:'N';
    var classFaSearchCuadrilla = (isRedisenio == 'S')?'fa-cuadr':'';
    var classFaAddcuadrilla = (isRedisenio == 'S')?'fa-add-cuadr':'';
    //CheckboxModel del grid de cuadrillas seleccionadas
    var smSeleccion = new Ext.selection.CheckboxModel({
        listeners: {
            select  : function() {
                var grid = Ext.getCmp("gridSeleccionCuadrillas");
                if (grid.getSelectionModel().getSelection().length > 0) {
                    Ext.getCmp("btnEliminarSeleccion").setDisabled(false);
                }
            },
            deselect  : function(){
                var grid = Ext.getCmp("gridSeleccionCuadrillas");
                if (grid.getSelectionModel().getSelection().length < 1) {
                    Ext.getCmp("btnEliminarSeleccion").setDisabled(true);
                }
            }
        }
    });

    //CheckboxModel del grid de cuadrillas
    var sm = new Ext.selection.CheckboxModel({
        listeners: {
            select  : function() {
                var grid = Ext.getCmp("gridCuadrillas");
                if (grid.getSelectionModel().getSelection().length > 0) {
                    Ext.getCmp("btnAgregar").setDisabled(false);
                }
            },
            deselect  : function(){
                var grid = Ext.getCmp("gridCuadrillas");
                if (grid.getSelectionModel().getSelection().length < 1) {
                    Ext.getCmp("btnAgregar").setDisabled(true);
                }
            }
        }
    });

    //Cargamos el store de cuadrillas
    arrayData.storeCuadrillas.load();
    arrayData.storeCuadrillas.on('load',function (){
        Ext.getCmp('btnBuscarCuadrilla').setDisabled(false);
    });

    //Grid de cuadrillas
    var gridCuadrillas = Ext.create('Ext.grid.Panel', {
        id         : 'gridCuadrillas',
        title      : 'Cuadrillas',
        width      : 310,
        height     : 270,
        store      : arrayData.storeCuadrillas,
        loadMask   : true,
        frame      : false,
        selModel   : sm,
        listeners  : {
            itemdblclick: function( view, record, item, index, eventobj, obj){
                var position = view.getPositionByEvent(eventobj);
                var data  = record.data;
                var value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title    :'Copiar texto?',
                    msg      : "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    closable : false,
                    buttons  : Ext.Msg.OK,
                    icon     : Ext.Msg.INFORMATION
                });
            },
            viewready: function (grid) {
                var view = grid.view;
                grid.mon(view, {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });
                grid.tip = Ext.create('Ext.tip.ToolTip', {
                    target    : view.el,
                    delegate  : '.x-grid-cell',
                    trackMouse: true,
                    renderTo  : Ext.getBody(),
                    listeners: {
                        beforeshow: function (tip) {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                var header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                            }
                        }
                    }
                });
            }
        },
        dockedItems:
        [
            {
                xtype: 'toolbar',
                dock : 'top',
                align: '->',
                items:
                [
                    {
                        xtype      : 'displayfield',
                        fieldLabel : 'Cuadrilla:',
                        width      : 90
                    },
                    {
                        xtype      : 'textfield',
                        id         : 'txtBuscarCuadrilla',
                        padding    : '-30px',
                        width      : 150
                    },
                    { xtype: 'tbfill' },
                    {
                        id       : 'btnBuscarCuadrilla',
                        text     : '<label style="color:green;"><i class="fa fa-fw fa-search '+classFaSearchCuadrilla+'" aria-hidden="true"></i></label>'+
                                   '&nbsp;<b>Buscar</b>',
                        scope    : this,
                        disabled : true,
                        handler  : function()
                        {
                            Ext.getCmp('btnBuscarCuadrilla').setDisabled(true);
                            arrayData.storeCuadrillas.getProxy().extraParams.query = Ext.getCmp('txtBuscarCuadrilla').value;
                            arrayData.storeCuadrillas.load();
                            arrayData.storeCuadrillas.on('load',function (){
                                Ext.getCmp('btnBuscarCuadrilla').setDisabled(false);
                            });
                        }
                    }
                ]
            }
        ],
        columns:
        [
            {
                header    : 'Id Cuadrilla',
                dataIndex : 'id_cuadrilla',
                hidden    : true,
                sortable  : false,
                hideable  : false
            },{
                header    : '<b>Nombre</b>',
                dataIndex : 'nombre_cuadrilla',
                width     : 250,
                sortable  : false,
                hideable  : false
            }
        ]
    });

    //Grid de cuadrillas seleccionadas
    var gridSeleccionCuadrillas = Ext.create('Ext.grid.Panel', {
        id         :'gridSeleccionCuadrillas',
        title      :'Cuadrillas Seleccionadas',
        width      : 310,
        height     : 270,
        store      : storeSeleccionCuadrillas,
        loadMask   : true,
        frame      : false,
        selModel   : smSeleccion,
        listeners  : {
            itemdblclick: function( view, record, item, index, eventobj, obj){
                var position = view.getPositionByEvent(eventobj);
                var data  = record.data;
                var value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title    :'Copiar texto?',
                    msg      : "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    closable : false,
                    buttons  : Ext.Msg.OK,
                    icon     : Ext.Msg.INFORMATION
                });
            },
            viewready: function (grid) {
                var view = grid.view;
                grid.mon(view, {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });
                grid.tip = Ext.create('Ext.tip.ToolTip', {
                    target    : view.el,
                    delegate  : '.x-grid-cell',
                    trackMouse: true,
                    renderTo  : Ext.getBody(),
                    listeners: {
                        beforeshow: function (tip) {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                var header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                            }
                        }
                    }
                });
            }
        },
        dockedItems:
        [
            {
                xtype: 'toolbar',
                dock : 'top',
                align: '<-',
                items:
                [
                    {xtype: 'tbfill'},
                    {
                        id       :'btnEliminarSeleccion',
                        height   : 25,
                        disabled : true,
                        text     : '<label style="color:red;"><i class="fa fa-trash" aria-hidden="true"></i></label>'+
                                   '&nbsp;<b>Eliminar</b>',
                        scope    : this,
                        handler  : function() {
                            var grid           = Ext.getCmp("gridSeleccionCuadrillas");
                            var arraySelection = grid.getSelectionModel().getSelection();
                            var i;
                            for(i = 0 ; i < arraySelection.length ; ++i) {
                                var index = storeSeleccionCuadrillas.findBy(function (record) {
                                    return record.data.id_cuadrilla == arraySelection[i].data.id_cuadrilla;
                                });
                                grid.getStore().removeAt(index);//Eliminar del store
                            }

                            Ext.getCmp("btnEliminarSeleccion").setDisabled(true);
                            if (grid.getStore().data.items.length <= 0) {
                                if(isRedisenio === "S")
                                {
                                    $('#txt_cmb_cuadrillas').val('');
                                    $('#cmb_departamento').val('');
                                    $('#txt_cmb_departamento').val('');
                                    $('#txt_cmb_departamento,#btn_cmb_departamento').attr('disabled','disabled');
                                    $('#cmbEmpresaBusqueda,txt_cmbEmpresaBusqueda').val('');
                                    $('#txt_cmbEmpresaBusqueda,#btn_cmbEmpresaBusqueda').removeAttr('disabled');

                                }else{
                                    Ext.getCmp('cmb_cuadrillas').value = "";
                                    Ext.getCmp('cmb_cuadrillas').setRawValue("");
                                    Ext.getCmp('cmb_departamento').value = "";
                                    Ext.getCmp('cmb_departamento').setRawValue("");
                                    Ext.getCmp('cmb_departamento').setDisabled(true);
                                    Ext.getCmp('cmbEmpresaBusqueda').value = "";
                                    Ext.getCmp('cmbEmpresaBusqueda').setRawValue("");
                                    Ext.getCmp('cmbEmpresaBusqueda').setDisabled(false);
                                }                                
                            } else {
                                for(i = 0 ; i < grid.getStore().data.items.length ; ++i) {
                                    var cuadrilla;
                                    if (Ext.isEmpty(cuadrilla)) {
                                        cuadrilla = grid.getStore().data.items[i].data.nombre_cuadrilla;
                                    } else {
                                        cuadrilla = cuadrilla + ',  '+grid.getStore().data.items[i].data.nombre_cuadrilla;
                                    }
                                }
                                if(isRedisenio === "S")
                                {
                                    $('#txt_cmb_cuadrillas').val(cuadrilla);
                                }else{
                                    Ext.getCmp('cmb_cuadrillas').setValue(cuadrilla);
                                }
                            }
                        }
                    }
                ]
            }
        ],
        columns:
        [
            {
                header    : 'Id Cuadrilla',
                dataIndex : 'id_cuadrilla',
                hidden    : true,
                sortable  : false,
                hideable  : false
            },{
                header    : '<b>Nombre</b>',
                dataIndex : 'nombre_cuadrilla',
                width     : 250,
                sortable  : false,
                hideable  : false
            }
        ]
    });

    //Panel para creacion de planificacion HAL
    var panelCuadrillas = Ext.create('Ext.form.Panel', {
        id          :'panelCuadrillas',
        height      : true,
        width       : 740,
        bodyPadding : 10,
        items:
        [
            {
                xtype : 'fieldset',
                layout: {
                    type    : 'table',
                    columns : 5,
                    pack    : 'center'
                },
                items:
                [
                    gridCuadrillas,
                    {html:"&nbsp;",border:false,width:50},
                    gridSeleccionCuadrillas,
                    {width: '10%', border: false},
                    {width: '10%', border: false},
                    {width: '10%', border: false},
                    {html:"&nbsp;",border:false,width:50},
                    {width: '10%', border: false},
                    {width: '10%', border: false},
                    {width: '10%', border: false},
                    {width: '10%', border: false},
                    {
                        id       :'btnAgregar',
                        xtype    : 'button',
                        disabled : true,
                        text     : '<label style="color:blue;"><i class="fa fa-plus-square '+classFaAddcuadrilla+'" aria-hidden="true"></i></label>'+
                                   '&nbsp;<b>Agregar</b>',
                        listeners:
                        {
                            click: function () {
                                var cuadrilla   = "";
                                if(isRedisenio === "S"){
                                    cuadrilla = $('#txt_cmb_cuadrillas').val() !== ''?$('#txt_cmb_cuadrillas').val():'';
                                }else{
                                    cuadrilla      = Ext.getCmp('cmb_cuadrillas').value ? Ext.getCmp('cmb_cuadrillas').value : ''; 
                                }
                                var grid           = Ext.getCmp("gridCuadrillas");
                                var arraySelection = grid.getSelectionModel().getSelection();
                                $.each(arraySelection, function(i, item) {
                                    var index = storeSeleccionCuadrillas.findBy(function (record) {
                                        return record.data.id_cuadrilla == item.data.id_cuadrilla;
                                    });
                                    if (index < 0) {
                                        storeSeleccionCuadrillas.add({"id_cuadrilla"     : item.data.id_cuadrilla,
                                                                      "nombre_cuadrilla" : item.data.nombre_cuadrilla});
                                        if (Ext.isEmpty(cuadrilla)) {
                                            cuadrilla = item.data.nombre_cuadrilla;
                                        } else {
                                            cuadrilla = cuadrilla + ',  '+item.data.nombre_cuadrilla;
                                        }
                                    }
                                });
                                if(isRedisenio === "S")
                                {
                                    $('#txt_cmb_cuadrillas').val(cuadrilla);
                                    $('#cmb_departamento').val('');
                                    $('#txt_cmb_departamento').val('');
                                    $('#txt_cmb_departamento,#btn_cmb_departamento').attr('disabled','disabled');
                                    $('#cmbEmpresaBusqueda,#txt_cmbEmpresaBusqueda').val('');
                                    $('#txt_cmbEmpresaBusqueda,#btn_cmbEmpresaBusqueda').attr('disabled','disabled');

                                }else{
                                    Ext.getCmp('cmb_cuadrillas').setValue(cuadrilla);
                                    Ext.getCmp('cmb_departamento').reset();
                                    Ext.getCmp('cmb_departamento').setDisabled(true);
                                    Ext.getCmp('cmbEmpresaBusqueda').reset();
                                    Ext.getCmp('cmbEmpresaBusqueda').setDisabled(true);
                                }
                            }
                        }
                    },
                    {width: '10%', border: false},
                    {width: '10%', border: false}
                ]
            }
        ]
    });

    //Ventana
    var winSeleccionCuadrillas = Ext.widget('window', {
        id          : 'winSeleccionCuadrillas',
        layout      : 'fit',
        resizable   : false,
        modal       : true,
        closable    : false,
        width       : 'auto',
        items       : [panelCuadrillas],
        buttonAlign : 'right',
        buttons     : [
            {
                text  : '<label style="color:red;"><i class="fa fa-window-close-o" aria-hidden="true"></i></label>'+
                        '&nbsp;<b>Cerrar</b>',
                align : 'center',
                handler: function() {
                    winSeleccionCuadrillas.close();
                    winSeleccionCuadrillas.destroy();
                }
            }
        ]
    }).show();
}