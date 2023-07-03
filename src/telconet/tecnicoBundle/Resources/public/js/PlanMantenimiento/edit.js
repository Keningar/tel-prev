Ext.require([
            '*',
            'Ext.tip.QuickTipManager',
                'Ext.window.MessageBox'
            ]);

    var itemsPerPage = 10;
    var storeTareas='';            
var comboTareasMantenimientoStore=null;
Ext.onReady(function(){  
    
    /*Ext.define('TareaModel', {
        extend: 'Ext.data.Model',                                                                				                                    
        fields: [
                    {name:'idTarea', mapping: 'idTarea'},
                    {name:'nombreTarea', mapping:'nombreTarea'},
                    {name:'estado', mapping:'estado'}
                ]
    }); */

    var arrayIndicesMantenimientos  = strIdsMantenimientos.split(",");
    var numMantenimientoShow=0;
    console.log(i);
    for (var i = 0; i < numMantenimientosPlan; i++) 
    {
        console.log(i);
        numMantenimientoShow++;
        var newDivMantenimientoTarea= $('<div/>', { id: 'div_gridTareas_'+i, class: 'div_mantenimiento'});
            newDivMantenimientoTarea.appendTo($('#mantenimientos_principal'));
            
        var storeTareasXMantenimiento = Ext.create('Ext.data.JsonStore', {
            //model: 'TareaModel',
            pageSize: itemsPerPage,
            proxy: {
                type: 'ajax',
                url : urlGetTareasMantenimientosPlan,
                reader: {
                    type: 'json',
                    root: 'encontrados',
                    totalProperty: 'total'
                },
                extraParams:{
                    idMantenimiento: arrayIndicesMantenimientos[i]
                }
            },
            fields:
            [
                {name:'idTarea',            mapping: 'idTarea'},
                {name:'nombreTarea',        mapping:'nombreTarea'},
                {name:'estado',             mapping:'estado'}
            ]
        });
        
        storeTareasXMantenimiento.load();
    
        /*var selModelTareas = Ext.create('Ext.selection.CheckboxModel', {
            listeners: {
                 selectionchange: function(sm, selections) {
                     console.log("model"+i);
                     //var gridTareasMantenimiento = Ext.getCmp("gridTareas_"+i);
                     gridTareasXMantenimiento.down('#removeButton').setDisabled(selections.length == 0);
                 }
             }
         });*/
         
         var selModelTareas = new Ext.selection.CheckboxModel( {
                   listeners:{
                           selectionchange: function(selectionModel, selected, options){
                               arregloSeleccionados= new Array();
                               Ext.each(selected, function(record){						
                               });							
                           }
                   }
         });

         var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
             clicksToEdit: 1,
             listeners: {
                 edit: function(editor, object) {
                     var rowIdx = object.rowIdx;
                     var column = object.field;
                     var currentIp = object.value;
                     var store = Ext.getCmp("gridTareas_"+i).getStore().getAt(rowIdx);
                 }
             }
         });
         
        var gridTareasXMantenimiento = Ext.create('Ext.grid.Panel', 
        {
            id:'gridTareas_'+i,
            store: storeTareasXMantenimiento,
            columnLines: true,
            selModel: selModelTareas,
            
            columns: 
            [
                Ext.create('Ext.grid.RowNumberer'),
                {
                    id: 'idTarea_'+i,
                    header: 'idTarea',
                    dataIndex: 'idTarea',
                    hidden: true,
                    hideable: false
                }, 
                {
                    id: 'nombreTarea_'+i,
                    header: 'Nombre Tarea',
                    dataIndex: 'nombreTarea',
                    width: 400,
                    sortable: true,
                    /*renderer: function(value, metadata, record, rowIndex, colIndex, store)
                    {

                        for (var i = 0; i < comboTareasMantenimientoStore.data.items.length; i++) 
                        {
                            if ((comboTareasMantenimientoStore.data.items[i].data.nombreTareaCombo == value) ||
                                (comboTareasMantenimientoStore.data.items[i].data.idTareaCombo == value))
                            {
                                record.data.idTarea     = comboTareasMantenimientoStore.data.items[i].data.idTareaCombo;
                                record.data.nombreTarea = comboTareasMantenimientoStore.data.items[i].data.nombreTareaCombo;

                                break;
                            }
                            if (i == (comboTareasMantenimientoStore.data.items.length - 1))
                            {
                                record.data.nombreTarea = '';
                            }
                        }
                        return record.data.nombreTarea;
                    },
                    editor: {
                        id: 'searchTarea_cmp'+i,
                        xtype: 'combobox',
                        displayField: 'nombreTareaCombo',
                        valueField: 'idTareaCombo',
                        loadingText: 'Buscando ...',
                        store: comboTareasMantenimientoStore,
                        fieldLabel: false,
                        queryMode: "remote",
                        emptyText: '',
                        listClass: 'x-combo-list-small'
                    }*/
                },
                {
                    id: 'estado_'+i,
                    header: 'Estado',
                    dataIndex: 'estado',
                    width: 70,
                    sortable: true
                }
            ],
            dockedItems: 
            [
                {
                    xtype: 'toolbar',
                    items: 
                    [
                        {
                            disabled: false,
                            itemId: 'removeButton',
                            text:'Eliminar',
                            tooltip:'Elimina el item seleccionado',
                            handler : function()
                            {
                                
                                eliminarSeleccion(gridTareasXMantenimiento);
                                //eliminarTareaMantenimientoPlan(2,gridTareasXMantenimiento,'');
                                
                            }
                        },
                        '-', 
                        {
                            text:'Agregar',
                            tooltip:'Agrega un item a la lista',
                            handler : function(){
                                agregarTareaMantenimiento(gridTareasXMantenimiento);
                                agregar(i);
                                console.log(numMantenimientoShow);
                                /*
                                console.log("agregar"+i);
                                var strMensaje='';
                                var storeValida = Ext.getCmp("gridTareas_"+i).getStore();
                                var storeTareasMantenimiento= Ext.getCmp("gridTareas_"+i).getStore();

                                var bool_OK = false;

                                if(storeValida.getCount() > 0)
                                {
                                    var bool_tiene_registros_vacios     = false;
                                    var bool_tiene_registros_repetidos  = false;

                                    //Recorre las tareas dentro del grid
                                    for(var contador = 0; contador < storeValida.getCount(); contador++)
                                    {
                                        var id_tarea = storeValida.getAt(contador).data.idTarea;
                                        var nombre_tarea = storeValida.getAt(contador).data.nombreTarea;     

                                        if(id_tarea != "" && nombre_tarea != ""){}
                                        else 
                                        {  
                                            bool_tiene_registros_vacios = true;
                                            break;
                                        }

                                        if(i>0)
                                        {
                                            for(var j = 0; j < contador; j++)
                                            {
                                                var id_tarea_valida = storeValida.getAt(j).data.idTarea;
                                                var nombre_tarea_valida = storeValida.getAt(j).data.nombreTarea;

                                                if(id_tarea_valida == id_tarea || nombre_tarea_valida == nombre_tarea)
                                                {
                                                    bool_tiene_registros_repetidos = true;
                                                    break;
                                                }
                                            }
                                        }
                                    }

                                    if(!bool_tiene_registros_vacios && !bool_tiene_registros_repetidos)
                                    {
                                        bool_OK=true;
                                    }
                                    else if(bool_tiene_registros_vacios)
                                    {
                                        strMensaje+='Debe completar datos de las tareas a ingresar, antes de solicitar una nueva tarea';
                                        Ext.Msg.alert('Alerta ',strMensaje);
                                        return false;
                                    }
                                    else if(bool_tiene_registros_repetidos)
                                    {
                                        strMensaje+='No puede ingresar tareas repetidas en el Mantenimiento .Debe modificar el registro repetido ';
                                        Ext.Msg.alert('Alerta ',strMensaje);
                                        return false;
                                    }
                                }
                                else
                                {
                                    bool_OK = true;
                                }

                                if(bool_OK)
                                {
                                    var tarea = Ext.create('Tarea', {
                                        idTarea     : '',
                                        nombreTarea : '',
                                        estado      : 'Activo'
                                    });
                                    storeTareasMantenimiento.insert(0, tarea);
                                }
                                */
                            }
                        }
                    ]
                }
            ],
            viewConfig:{
                stripeRows:true
            },
            width: 850,
            height: 250,
            frame: true,
            title: 'Mantenimiento '+numMantenimientoShow,
            renderTo: 'div_gridTareas_'+i
        });
        

    }//Fin de for
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /*
    
    storeTareas = Ext.create('Ext.data.JsonStore', {
        model: 'TareaModel',
        pageSize: itemsPerPage,
        proxy: {
            type: 'ajax',
            url: 'getTareasPlanMantenimiento',
            reader: {
                type: 'json',
                root: 'encontrados',
                totalProperty: 'total'
            },					
            simpleSortMode: true
        }
    });

    storeTareas.load(); 

    
    cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2,
        listeners: {
            beforeedit: function(editor, event, opts) {
                if (!event.record.phantom) {
                    return false;
                }
            } 
        }
    });

    selModelTareas = new Ext.selection.CheckboxModel( {
        listeners:{
            selectionchange: function(selectionModel, selected, options){
                arregloSeleccionados= new Array();
                Ext.each(selected, function(record){						
                });							
            }
        }
    });


    gridTareas = Ext.create('Ext.grid.Panel', {
        id: 'gridTareas',
        store: storeTareas,
        width: 850,
        height: 450,
        frame: true,
        collapsible:false,
        title: 'Tareas del Plan de Mantenimiento',
        selModel: selModelTareas,
        dockedItems: [{
        xtype: 'toolbar',
        items: [{
            itemId: 'removeButton',
            text:'Eliminar',
            tooltip:'Elimina el item seleccionado',
            disabled: false,
            handler : function(){
                eliminarTareaPlan(2,'');
            }
        }, '-', {
            text:'Agregar',
            tooltip:'Agrega una tarea al plan',
            handler : function()
            {
                var r = Ext.create('TareaModel', {
                    idTarea:            '',
                    nombreTarea:        '',
                    estado:             'Activo'
                });
                if(!existeRecord(r, gridTareas))
                {
                    storeTareas.insert(0, r);
                    cellEditing.startEditByPosition({row: 0, column: 1});
                    
                }
                else
                {
                  alert('Ya existe un registro vacío.');
                }
            }
            }]
        }],
        renderTo: Ext.get('div_tareas'),
        plugins: [cellEditing],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeTareas,
            displayInfo: true,
            displayMsg: 'Mostrando Tareas {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        columns: [
            {
                id: 'idTarea',
                header: 'tareaId',
                dataIndex: 'idTarea',
                hidden: true,
                hideable: false
            },

            {
                id: 'nombreTarea',
                header: 'Nombre',
                dataIndex: 'nombreTarea',
                width: 200,
                sortable: true,
                editor: {
                    xtype: 'textfield',
                    allowBlank: false
                }
            },
            {
                id: 'estado',
                header: 'Estado',
                dataIndex: 'estado',
                width: 70,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 100,
                items: [
                            {
                                getClass: function(v, meta, rec) 
                                {
                                    if(rec.data.idTarea=="")
                                    {
                                        rec.data.action1 = "icon-invisible";
                                    }
                                    else
                                    {
                                        rec.data.action1 = "button-grid-show";
                                    }
                                    return rec.data.action1;
                                },
                                tooltip: 'Ver Tarea',
                                handler: function(grid, rowIndex, colIndex) 
                                {
                                    var rec = storeTareas.getAt(rowIndex);
                                    if(rec.get('action1')!="icon-invisible")
                                    {
                                        verTareaPlan(grid.getStore().getAt(rowIndex).data);
                                    } 
                                    
                                }
                            },
                            {
                                getClass: function(v, meta, rec) {
                                    if(rec.data.estado=="Eliminado" || rec.data.idTarea=="")
                                    {
                                        rec.data.action2 = "icon-invisible";
                                    }
                                    else
                                    {
                                        rec.data.action2 = "button-grid-edit";
                                    }
                                    return rec.data.action2;
                                },
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = storeTareas.getAt(rowIndex);
                                    if(rec.get('action2')!="icon-invisible")
                                    {
                                        editarTareaPlan(grid.getStore().getAt(rowIndex).data);
                                    } 

                                }
                            },
                            {
                                getClass: function(v, meta, rec) {
                                    if(rec.data.estado=="Eliminado" || rec.data.idTarea=="")
                                    {
                                        rec.data.action3 = "icon-invisible";
                                    }
                                    else
                                    {
                                        rec.data.action3 = "button-grid-delete";
                                    }
                                    return rec.data.action3;
                                },
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = storeTareas.getAt(rowIndex);
                                    if(rec.get('action3')!="icon-invisible")
                                    {
                                        eliminarTareaPlan(1,rec.get('idTarea'));
                                    }
                                }
                            }
                        ]
            }
        ]
    }); */                      
});


function crearFilasTareasNuevas(tareasNuevas)
{
    
    if(tareasNuevas.total!= 0)
    {

        var numRowTareas=0;
        for(var i=0; i < tareasNuevas.total; i++)
        {
            var nombreTarea=tareasNuevas.tareas[i].nombreTarea;
            var descripcionTarea=tareasNuevas.tareas[i].nombreTarea;
            var r = Ext.create('TareaModel', {
                idTarea:            '',
                nombreTarea:        nombreTarea,
                descripcionTarea:   descripcionTarea,
                estado:             'Activo'
            });
            storeTareas.insert(numRowTareas, r); 
            numRowTareas++;
        }
        cellEditing.startEditByPosition({row: numRowTareas, column: 1});
        Ext.getCmp('gridTareas').getView().refresh();
        
        
        var storeT = gridTareas.getStore();
        var viewT = gridTareas.getView();
        columnLength = gridTareas.columns.length;
        storeT.each(function(record,idx)
        {
            var boolEsFilaNueva=false;
            for (var i = 0; i < columnLength; i++) 
            {
                cell = viewT.getCellByPosition({row: idx, column: i});
                fieldName = gridTareas.columns[i].dataIndex;
                data = record.data;
                if (fieldName === 'idTarea') 
                {
                    value = data[fieldName];
                    if(value=="")
                    {
                        boolEsFilaNueva=true;
                    }
                }

                if(boolEsFilaNueva && (fieldName === 'nombreTarea' || fieldName === 'descripcionTarea'))
                {
                    cell.addCls("x-grid-dirty-cell");
                }
            }  
        });
    }
}

function eliminarSeleccion(datosSelect)
{	
    for(var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
    {		
        datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
    }
}


function agregarTareaMantenimiento(datosSelect)
{	
    for(var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
    {		
        
        //datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
    }
    console.log("len"+datosSelect.getSelectionModel().getCount());
}

function eliminarTareaMantenimientoPlan(tipo,idTarea)
{
    var param = '';
    var idTareaPlan='';
    var estado = 0;
    var strAlerta='';
    var strEliminacionTarea='';
    var boolError=false;
    //tipo=1 Eliminación desde la acción eliminar de una tarea
    if(tipo==1)
    {
        idTareaPlan=idTarea;
        
        strAlerta='Se eliminara el registro. Desea continuar?';
        strEliminacionTarea='Eliminando Tarea...';
    }
    //tipo=2 Eliminación Masiva desde el botón superior eliminar
    else
    {
        strAlerta='Se eliminaran los registros. Desea continuar?';
        strEliminacionTarea='Eliminando Tareas...';

        if(selModelTareas.getSelection().length > 0)
        {
            for(var i=0 ;  i < selModelTareas.getSelection().length ; ++i)
            {
                param = param + selModelTareas.getSelection()[i].data.idTarea;

                if(selModelTareas.getSelection()[i].data.estado == 'Eliminado')
                {
                    estado = estado + 1;
                }
                if(i < (selModelTareas.getSelection().length -1))
                {
                    param = param + '|';
                }
            }
            if(estado != 0)
            {
                alert('Por lo menos uno de los registro se encuentra en estado ELIMINADO');
                boolError=true;
            }
        }
        else
        {
          alert('Seleccione por lo menos un registro de la lista');
          boolError=true;
        }
    }

    if(!boolError)
    {
        Ext.Msg.confirm('Alerta',strAlerta, function(btn){
            if(btn=='yes'){
                Ext.MessageBox.wait(strEliminacionTarea, 'Por favor espere');
                Ext.Ajax.request({
                    url: url_eliminar_tarea_plan,
                    method: 'post',
                    params: { id:idTareaPlan, param : param, tipo:tipo},
                    success: function(response){
                        Ext.MessageBox.hide();
                        
                        json_tareas_nuevas_actualizar = obtenerTareasNuevas();
                        tareasNuevasActualizacion = Ext.JSON.decode(json_tareas_nuevas_actualizar);
                        if(tareasNuevasActualizacion.total != 0)
                        {
                            $('#tareas_nuevas').val(json_tareas_nuevas_actualizar);
                            storeTareas.load({
                                callback: function(records, operation, success) {
                                    crearFilasTareasNuevas(tareasNuevasActualizacion);
                                }
                            });
                        }
                        else
                        {
                            storeTareas.load();
                        }
                    },
                    failure: function(result)
                    {
                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                    }
                });
            }
        });
    }
}

function eliminarTareaPlan(tipo,idTarea)
{
    var param = '';
    var idTareaPlan='';
    var estado = 0;
    var strAlerta='';
    var strEliminacionTarea='';
    var boolError=false;
    //tipo=1 Eliminación desde la acción eliminar de una tarea
    if(tipo==1)
    {
        idTareaPlan=idTarea;
        
        strAlerta='Se eliminara el registro. Desea continuar?';
        strEliminacionTarea='Eliminando Tarea...';
    }
    //tipo=2 Eliminación Masiva desde el botón superior eliminar
    else
    {
        strAlerta='Se eliminaran los registros. Desea continuar?';
        strEliminacionTarea='Eliminando Tareas...';

        if(selModelTareas.getSelection().length > 0)
        {
            for(var i=0 ;  i < selModelTareas.getSelection().length ; ++i)
            {
                param = param + selModelTareas.getSelection()[i].data.idTarea;

                if(selModelTareas.getSelection()[i].data.estado == 'Eliminado')
                {
                    estado = estado + 1;
                }
                if(i < (selModelTareas.getSelection().length -1))
                {
                    param = param + '|';
                }
            }
            if(estado != 0)
            {
                alert('Por lo menos uno de los registro se encuentra en estado ELIMINADO');
                boolError=true;
            }
        }
        else
        {
          alert('Seleccione por lo menos un registro de la lista');
          boolError=true;
        }
    }

    if(!boolError)
    {
        Ext.Msg.confirm('Alerta',strAlerta, function(btn){
            if(btn=='yes'){
                Ext.MessageBox.wait(strEliminacionTarea, 'Por favor espere');
                Ext.Ajax.request({
                    url: url_eliminar_tarea_plan,
                    method: 'post',
                    params: { id:idTareaPlan, param : param, tipo:tipo},
                    success: function(response){
                        Ext.MessageBox.hide();
                        
                        json_tareas_nuevas_actualizar = obtenerTareasNuevas();
                        tareasNuevasActualizacion = Ext.JSON.decode(json_tareas_nuevas_actualizar);
                        if(tareasNuevasActualizacion.total != 0)
                        {
                            $('#tareas_nuevas').val(json_tareas_nuevas_actualizar);
                            storeTareas.load({
                                callback: function(records, operation, success) {
                                    crearFilasTareasNuevas(tareasNuevasActualizacion);
                                }
                            });
                        }
                        else
                        {
                            storeTareas.load();
                        }
                    },
                    failure: function(result)
                    {
                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                    }
                });
            }
        });
    }
}



function validarFormulario(){  
    Ext.MessageBox.wait("Editando Datos...", 'Por favor espere'); 
    if(document.getElementById("telconet_schemabundle_planmantenimientotype_nombreProceso").value==""){
        Ext.Msg.alert("Alerta","El campo nombre del plan es requerido.");
        return false;
    }
    if(document.getElementById("telconet_schemabundle_planmantenimientotype_descripcionProceso").value==""){
        Ext.Msg.alert("Alerta","El campo descripción del plan es requerido.");
        return false;
    }
    
    var valorBool = validarTareasNuevas();			

    if(valorBool)
    {
        json_tareas_nuevas = obtenerTareasNuevas();
        
        tareaNueva = Ext.JSON.decode(json_tareas_nuevas);

        if(tareaNueva.total != 0)
        {
            $('#tareas_nuevas').val(json_tareas_nuevas);
        }
    }	
    else
    {
        return false;
    }

    Ext.Ajax.request
    ({
        url: strUrlVerificarNombrePlan,
        method: 'post',
        params: 
        { 
            nombrePlan    : $("#telconet_schemabundle_planmantenimientotype_nombreProceso").val(),
            idPlan        : $("#id_plan_mantenimiento").val()
        },
        success: function(response)
        {
            var text = response.responseText;
            if(text === "OK")
            {
                document.getElementById("form_edit_proceso").submit();
            }
            else
            {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Error','El plan que desea ingresar ya existe.');
                
            }
        },
        failure: function(result)
        {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error',result.responseText);
        }
    });
    return false;
}


function editarTareaPlan(data){
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [{
            xtype: 'fieldset',
            title: 'Datos de la Tarea',
            defaultType: 'displayfield',
            defaults: {
                width: 650
            },
            items: [
                {
                    xtype: 'container',
                    layout: {
                        type: 'hbox',
                        pack: 'left'
                    },
                    items: [{
                    xtype: 'textfield',
                    id:'textNombreTarea',
                    name: 'textNombreTarea',
                    fieldLabel: 'Nombre',
                    value: data.nombreTarea,
                    width: '100%'
                    }]
                },
                {html:"&nbsp;",border:false,width:'100%'},
                {
                    xtype: 'container',
                    layout: {
                        type: 'hbox',
                        pack: 'left'
                    },
                    items: [{
                    xtype: 'textareafield',
                    id:'textDescripcionTarea',
                    name: 'textDescripcionTarea',
                    fieldLabel: 'Descripcion',
                    value: data.descripcionTarea,
                    width: '100%'
                    }]
                }

            ]
        }],
        buttons: [{
            text: 'Actualizar Tarea',
            formBind: true,
            handler: function(){
                var nombreTareaNuevo        = Ext.getCmp('textNombreTarea').getRawValue();
                var descripcionTareaNuevo   = Ext.getCmp('textDescripcionTarea').getRawValue();
                var boolError=false;
                if(nombreTareaNuevo.trim()==""){
                    Ext.Msg.alert("Alerta","El campo nombre de la tarea es requerido.");
                    boolError=true;
                }
                if(descripcionTareaNuevo.trim()==""){
                    Ext.Msg.alert("Alerta","El campo descripcion de la tarea es requerido.");
                    boolError=true;
                }
                if(!boolError)
                {
                    Ext.Msg.confirm('Alerta',"Se actualizarán los datos de la tarea. Desea continuar? ", function(btn){
                    if(btn=='yes')
                    {
                        var idPlanMantenimiento     = document.getElementById("id_plan_mantenimiento").value;

                        Ext.MessageBox.wait("Actualizando Datos de la Tarea...", 'Por favor espere');
                        Ext.Ajax.request({
                            url: url_actualizar_tarea_plan,
                            method: 'post',
                            params: {   idTarea:data.idTarea, idPlanMantenimiento:idPlanMantenimiento, 
                                        nombreTarea : nombreTareaNuevo, descripcionTarea:descripcionTareaNuevo},
                            success: function(response){
                                Ext.MessageBox.hide();
                                win.destroy();
                                json_tareas_nuevas_actualizar = obtenerTareasNuevas();
                                tareasNuevasActualizacion = Ext.JSON.decode(json_tareas_nuevas_actualizar);
                                if(tareasNuevasActualizacion.total != 0)
                                {
                                    $('#tareas_nuevas').val(json_tareas_nuevas_actualizar);
                                    storeTareas.load({
                                        callback: function(records, operation, success) {
                                            crearFilasTareasNuevas(tareasNuevasActualizacion);
                                        }
                                    });
                                }
                                else
                                {
                                    storeTareas.load();
                                }
                                
                            },
                            failure: function(result)
                            {
                                Ext.Msg.alert('Error ','Error: ' + result.statusText);
                            }
                        });
                    }
                    });
                }
                
            }
            },{
                text: 'Cancelar',
                handler: function(){
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Editar Tarea',
        modal: true,
        width: 730,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
    win.query('fieldset')[0].setHeight('auto');

}


function existeRecord(myRecord, grid)
{
  var existe=false;
  var num=grid.getStore().getCount();

  for(var i=0; i < num ; i++)
  {
    var nombreTarea=grid.getStore().getAt(i).get('nombreTarea');
    var descripcionTarea=grid.getStore().getAt(i).get('descripcionTarea');
    if(nombreTarea == myRecord.get('nombreTarea')  || descripcionTarea == myRecord.get('descripcionTarea')) 
    {
      existe=true;
      break;
    }
  }
  return existe;
}



function obtenerTareasNuevas()
{
    
    var gridTareas = Ext.getCmp("gridTareas");
    var array = new Object();
    var numTareasNuevas=0;
    
    array['tareas'] = new Array();
    var array_data = new Array();
    for(var i=0; i < gridTareas.getStore().getCount(); i++)
    {
        var idTarea=gridTareas.getStore().getAt(i).data.idTarea;
        if(idTarea=="")
        {
            array_data.push(gridTareas.getStore().getAt(i).data);
            numTareasNuevas++;
        }
        
    }
    array['total'] =  numTareasNuevas;
    array['tareas'] = array_data;
    return Ext.JSON.encode(array);
}

function validarTareasNuevas()
{		
    var storeValida = Ext.getCmp("gridTareas").getStore();
    var boolSigue = false;
    var boolSigue2 = false;

    if(storeValida.getCount() > 0)
    {
        var boolSigue_vacio = true;
        var boolSigue_igual = true;
        for(var i = 0; i < storeValida.getCount(); i++)
        {
            var idTarea=storeValida.getAt(i).data.idTarea;
            var nombreTarea = storeValida.getAt(i).data.nombreTarea;
            var descripcionTarea = storeValida.getAt(i).data.descripcionTarea;
            if(idTarea=="")
            {
                if(nombreTarea == "" || descripcionTarea == "")
                {
                    boolSigue_vacio = false;
                }

                if(i>0)
                {
                    for(var j = 0; j < i; j++)
                    {
                        var nombreTareaValida = storeValida.getAt(j).data.nombreTarea;
                        var descripcionTareaValida = storeValida.getAt(j).data.nombreTarea;

                        if(nombreTareaValida == nombreTarea || descripcionTareaValida == descripcionTarea)
                        {
                            boolSigue_igual = false;	
                        }
                    }
                }
            }
        } 

        if(boolSigue_vacio) { boolSigue = true; }	
        if(boolSigue_igual) { boolSigue2 = true; }					
    }
    else
    {
        boolSigue = true;
        boolSigue2 = true;
    }

    if(boolSigue && boolSigue2)
    {
        return true;
    }
    else if(!boolSigue)
    {
        Ext.Msg.alert('Alerta ',"Debe completar todos los datos de las tareas a ingresar");
        return false;
    }
    else if(!boolSigue2)
    {
        Ext.Msg.alert('Alerta ',"No puede ingresar la misma tarea! Debe modificar el registro repetido, antes de guardar");
        return false;
    }
    else
    {
        Ext.Msg.alert('Alerta ',"Debe completar datos de las tareas a ingresar");
        return false;
    }
}
function agregar(indice)
{
    console.log(indice);
}


