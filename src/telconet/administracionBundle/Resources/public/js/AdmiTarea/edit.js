Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    
    /* ****************** PROCESO PADRE ************************ */
    Ext.define('ProcesosList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_proceso', type:'int'},
            {name:'nombre_proceso', type:'string'}
        ]
    });
    storeProcesos = Ext.create('Ext.data.Store', {
        pageSize: 200,
		model: 'ProcesosList',
		proxy: {
			type: 'ajax',
			url : '../getProcesos',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			}
		}
    });
    
    combo_procesos = new Ext.form.ComboBox({
		id: 'cmb_proceso',
		name: 'cmb_proceso',
		fieldLabel: false,
		anchor: '100%',
		queryMode:'remote',
		width: 400,
		emptyText: 'Seleccione Proceso',
		store:storeProcesos,
		displayField: 'nombre_proceso',
		valueField: 'id_proceso',
		renderTo: 'combo_proceso'
    });
	
    /* ****************** TAREAS ************************ */
    Ext.define('TareasList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_tarea', type:'int'},
            {name:'nombre_tarea', type:'string'}
        ]
    });
	
    storeTareasAnt = Ext.create('Ext.data.Store', {
        pageSize: 200,
		model: 'TareasList',
		proxy: {
			type: 'ajax',
			url : '../getTareas',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			}
		}
    });
    
    combo_tareas_ant = new Ext.form.ComboBox({
		id: 'cmb_tarea_ant',
		name: 'cmb_tarea_ant',
		fieldLabel: false,
		anchor: '100%',
		queryMode:'remote',
		width: 400,
		emptyText: 'Seleccione Tarea Anterior',
		store:storeTareasAnt,
		displayField: 'nombre_tarea',
		valueField: 'id_tarea',
		renderTo: 'combo_tarea_ant'
    });
	
    storeTareasSig = Ext.create('Ext.data.Store', {
        pageSize: 200,
		model: 'TareasList',
		proxy: {
			type: 'ajax',
			url : '../getTareas',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			}
		}
    });
    combo_tareas_sig = new Ext.form.ComboBox({
		id: 'cmb_tarea_sig',
		name: 'cmb_tarea_sig',
		fieldLabel: false,
		anchor: '100%',
		queryMode:'remote',
		width: 400,
		emptyText: 'Seleccione Tarea Siguiente',
		store:storeTareasSig,
		displayField: 'nombre_tarea',
		valueField: 'id_tarea',
		renderTo: 'combo_tarea_sig'
    });
	
    
    Ext.define('opciones', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'opcion', type: 'string'},
            {name: 'valor',  type: 'string'}
        ]
    });
    
    comboOpcion = new Ext.data.Store({ 
        model: 'opciones',
        data : [
            {opcion:'Elemento'  , valor:'0'},
            {opcion:'Tramo'     , valor:'1'},
        ]
    });
    
    storeCombo = new Ext.data.Store({ 
        total: 'total',
        autoLoad:false,
        proxy: {
            type: 'ajax',
            url : '../../../soporte/admi_tarea/getJsonPorOpcion',
   //         extraParams: {opcion: '999'},
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'id', mapping:'id'},
                {name:'nombre', mapping:'nombre'}
              ]
    });
    
    storeInterfaceModelo = new Ext.data.Store({ 
        total: 'total',
        autoLoad: false, 
        proxy: {
            type: 'ajax',
            url : '../../../tecnico/admi_interface_modelo/getInterfaceModelo',
            extraParams: {modeloElemento: '0'},
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idInterfaceModelo', mapping:'idInterfaceModelo'},
                {name:'nombreTipoInterface', mapping:'nombreTipoInterface'}
              ]
    });
    
    storeTareaInterfaceModeloTramoScript = new Ext.data.Store({ 
        total: 'total',
        autoLoad: false, 
        proxy: {
            type: 'ajax',
            url : 'getDatosTareaInterfaceModeloTramo',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'id'},
                {name:'opcion', mapping:'opcion'},
                {name:'comboId', mapping:'comboId'},
                {name:'nombreCombo', mapping:'nombreCombo'},
                {name:'interfaceModeloId', mapping:'interfaceModeloId'},
                {name:'tipoInterfaceNombre', mapping:'tipoInterfaceNombre'},
                {name:'script', mapping:'script'}
              ]
    });
    
    /*******************Creacion Grid******************/
    ////////////////Grid  Relaciones////////////////
    Ext.define('TareaInterfaceModeloTramo', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'opcion', mapping:'opcion'},
            {name:'idCombo', mapping:'id'},
            {name:'nombreCombo', mapping:'nombre'},
            {name:'interfaceModeloId', mapping:'idInterfaceModelo'},
            {name:'interfaceTipoNombre', mapping:'nombreTipoInterface'},
            {name:'script'}
        ]
    });

        
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2,
        listeners: {
            edit: function(){
                // refresh summaries
                gridTareaInterfaceModeloTramo.getView().refresh();
            }
        }
    });
    
    var selModelRelaciones = Ext.create('Ext.selection.CheckboxModel', {
        listeners: {
            selectionchange: function(sm, selections) {
                gridTareaInterfaceModeloTramo.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    });
    
    // create the grid and specify what field you want
    // to use for the editor at each header.
    gridTareaInterfaceModeloTramo = Ext.create('Ext.grid.Panel', {
        id:'gridTareaInterfaceModeloTramo',
        store: storeTareaInterfaceModeloTramoScript,
        columnLines: true,
        columns: [{
            id: 'opcion',
            header: 'Opcion',
            dataIndex: 'opcion',
            width: 100,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                    record.data.valor = record.data.opcion;
                    for(var i = 0;i< comboOpcion.data.items.length;i++){
                        if(comboOpcion.data.items[i].data.valor == value){
                            record.data.opcion = comboOpcion.data.items[i].data.opcion; 
//                            store.data.items[rowIndex].data.opcion = comboOpcion.data.items[i].data.opcion;
                            if(record.data.opcion=="Elemento"){
                                console.log("elemento = 0")
                                record.data.valor = 0;
                            }
                            else{
                                record.data.valor = 1;
                            }
                            
                            break;
                        }
                    }       
//                    if(record.data.opcion=="Elemento"){
//                                console.log("elemento = 0")
//                                record.data.valor = 0;
//                            }
//                            else{
//                                record.data.valor = 1;
//                            }
                    return record.data.opcion;
                },
            editor   :  {
                    xtype: 'combobox',
                    id:'comboOpcion',
                    name: 'comboOpcion',
                    store: comboOpcion,
                    displayField: 'opcion',
                    valueField: 'valor',
                    queryMode: 'local',
                    lazyRender: true,
                    listeners: {
                        select: function(combo){
                            cargarStores(combo.getValue());
                        }
                    },
                    emptyText: '',
                    forceSelection: true
                }
        },{
            id: 'id',
            header: 'idTareaInterfaceModeloTramo',
            dataIndex: 'id',
            hidden: true,
            hideable: false
        },{
            id: 'idCombo',
            header: 'idCombo',
            dataIndex: 'idCombo',
            hidden: true,
            hideable: false
        }, {
            id: 'nombreCombo',
            header: 'Elemento/Tramo',
            dataIndex: 'nombreCombo',
            width: 220,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                console.log("renderizando");
                if (typeof(record.data.nombreCombo) == "number")
                {
                    
                    record.data.idCombo = record.data.nombreCombo;
                    for (var i = 0;i< storeCombo.data.items.length;i++)
                    {
                        if (storeCombo.data.items[i].data.id == record.data.idCombo)
                        {
                            record.data.nombreCombo = storeCombo.data.items[i].data.nombre;
                            if (record.data.opcion == "Elemento" || storeTareaInterfaceModeloTramoScript.data.items[i].data.opcion=="Elemento")
                            {
                                presentarInterfaceModelo(record.data.idCombo);
                            }
                            break;
                        }
                    }
                }
                return record.data.nombreCombo;
            },
            editor: {
                
                xtype: 'combobox',
                typeAhead: true,
                displayField:'nombre',
                valueField: 'id',
                triggerAction: 'all',
                selectOnFocus: true,
                loadingText: 'Buscando ...',
                hideTrigger: false,
                store: storeCombo,
                lazyRender: true,
                listClass: 'x-combo-list-small'
            }
        },
        {
            id: 'interfaceModeloId',
            header: 'interfaceModeloId',
            dataIndex: 'interfaceModeloId',
            hidden: true,
            hideable: false
        }, {
            id: 'tipoInterfaceNombre',
            header: 'Tipo Interface',
            dataIndex: 'tipoInterfaceNombre',
            width: 220,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                if (typeof(record.data.tipoInterfaceNombre) == "number")
                {
                    record.data.interfaceModeloId = record.data.tipoInterfaceNombre;
                    for (var i = 0;i< storeInterfaceModelo.data.items.length;i++)
                    {
                        console.log(record.data.interfaceModeloId);
                        console.log(storeInterfaceModelo.data.items[i].data.idInterfaceModelo);
                        if (storeInterfaceModelo.data.items[i].data.idInterfaceModelo == record.data.interfaceModeloId)
                        {
                            record.data.tipoInterfaceNombre = storeInterfaceModelo.data.items[i].data.nombreTipoInterface;
                            break;
                        }
                    }
                }
                return record.data.tipoInterfaceNombre;
            },
            editor: {
                xtype: 'combobox',
                typeAhead: true,
                displayField:'nombreTipoInterface',
                valueField: 'idInterfaceModelo',
                triggerAction: 'all',
                selectOnFocus: true,
                loadingText: 'Buscando ...',
                hideTrigger: false,
                store: storeInterfaceModelo,
                lazyRender: true,
                listClass: 'x-combo-list-small'
            }
        },
        {
            id: 'script',
            header: 'script',
            dataIndex: 'script',
            hidden: true,
            hideable: false
        },
        {
            xtype: 'actioncolumn',
            header: 'Acciones',
            width: 120,
            items: [{
                getClass: function(v, meta, rec) {return 'button-grid-show'},
                tooltip: 'Agregar Script',
                handler: function(grid, rowIndex, colIndex) {
                    
                    agregarScript(grid.getStore().getAt(rowIndex).data);
//                    window.location = ""+rec.get('idBuffer')+"/show";
                    }
                }
            ]
        }],
        selModel: selModelRelaciones,
        viewConfig:{
            stripeRows:true
        },

        // inline buttons
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                itemId: 'removeButton',
                text:'Eliminar',
                tooltip:'Elimina el item seleccionado',
                iconCls:'remove',
                disabled: true,
                handler : function(){eliminarSeleccion(gridTareaInterfaceModeloTramo);}
            }, '-', {
                text:'Agregar',
                tooltip:'Agrega un item a la lista',
                iconCls:'add',
                handler : function(){
                    // Create a model instance
                    var r = Ext.create('TareaInterfaceModeloTramo', {
                        id:                 '',
                        opcion:             '',
                        idCombo:                 '',
                        nombreCombo:             '',
                        interfaceModeloId:  '',
                        interfaceTipoNombre:  '',
                        script:             ''
                    });
                    if(!existeRecord(r, gridTareaInterfaceModeloTramo))
                    {
                        storeTareaInterfaceModeloTramoScript.insert(0, r);
                        cellEditing.startEditByPosition({row: 0, column: 1});
                    }
                    else
                    {
                      alert('Ya existe un registro vacio.');
                    }
                }
            }]
        }],

        width: 700,
        height: 200,
        frame: true,
        title: 'Agregar Detalles de Tareas',
        renderTo: 'grid',
        plugins: [cellEditing]
        
        
    });
    
    
    /**************************************************/
    // manually trigger the data store load
    Ext.get(gridTareaInterfaceModeloTramo.getId()).mask('Loading...');
    
    storeCombo.load({
        params: {opcion: '0', port:'todo', limite:100},
        callback:function(){        
            storeInterfaceModelo.load({
                // store loading is asynchronous, use a load listener or callback to handle results
                callback: function(){
                    Ext.get(gridTareaInterfaceModeloTramo.getId()).unmask();
                    gridTareaInterfaceModeloTramo.getStore().load({});
                }
            });
        }
    });
});

function agregarScript(data){
    if(data.opcion=="Elemento"){
        console.log("elemento");
        console.log(data.comboId)
        
        var comboOpcionScript = new Ext.data.Store({ 
            total: 'total',
            autoLoad:true,
            proxy: {
                type: 'ajax',
                url : '../../../tecnico/admi_modelo_elemento/getAllDetalles',
                extraParams: {idModelo: data.comboId},
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
                  [
                    {name:'idDetalleModelo', mapping:'idDetalleModelo'},
                    {name:'valor', mapping:'valor'},
                    {name:'opcionScript', mapping:'nombreDetalle'}
                  ]
        });
    }
    else if(data.opcion=="Tramo"){
        console.log("tramo");
    }
    
//    Ext.define('opcionesScript', {
//        extend: 'Ext.data.Model',
//        fields: [
//            {name: 'opcionScript', type: 'string'},
//            {name: 'valor',  type: 'string'}
//        ]
//    });
//    
//    comboOpcionScript = new Ext.data.Store({ 
//        model: 'opcionesScript',
//        data : [
//            {opcionScript:'puerto'            , valor:'(puerto)'},
//            {opcionScript:'puerto numero'     , valor:'(numero)'},
//        ]
//    });

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
            title: 'Agregar Scripting',
            defaultType: 'textfield',
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
                        xtype: 'combo',
                        id:'opcionScript',
                        name: 'opcionScript',
                        store: comboOpcionScript,
                        fieldLabel: 'Caracteristica',
                        displayField: 'opcionScript',
                        valueField: 'valor',
                        queryMode: 'local'
                    },{
                        xtype: 'button',
                        text: 'Agregar',
                        handler: cargarDatoEnTextArea
                    }]
                },

                {
                    xtype: 'container',
                    layout: {
                        type: 'hbox',
                        pack: 'left'
                    },
                    items: [{
                    xtype: 'textareafield',
                    id:'scripting',
                    name: 'scripting',
                    fieldLabel: 'Script',
                    value: data.script,
                    cols: 80,
                    rows: 10,
                    anchor: '100%'
                    }]
                },

            ]
        }],
        buttons: [{
            text: 'Guardar Script',
            formBind: true,
            handler: function(){
                var datos = "";
//                        var puerto = Ext.getCmp('puerto').value;
//                        var mac = Ext.getCmp('mac').value;
//                        var vlan = Ext.getCmp('vlan').value;
//                        datos = idDispositivo+":"+puerto+":"+mac+":"+vlan;
                if(true){
                    data.script = Ext.getCmp('scripting').getRawValue();
//                            Ext.getCmp("datosMac").setValue(datos);
//                            alert(datos);
                    win.destroy();
                }
                else{
                    Ext.Msg.alert("Failed","Favor Revise los campos", function(btn){
                            if(btn=='ok'){
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
        title: 'Agregar Detalle de Scripting',
        modal: true,
        width: 730,
        closable: false,
        layout: 'fit',
        items: [formPanel]
    }).show();
            

}

function cargarDatoEnTextArea(){
//    alert(Ext.getCmp('opcionScript').value);
    
//    Ext.getCmp('scripting').value = Ext.getCmp('scripting').value + Ext.getCmp('opcionScript').value;
    Ext.getCmp('scripting').setRawValue( Ext.getCmp('scripting').value + Ext.getCmp('opcionScript').value);
}

function cargarStores(opcion){
    presentarCombo(opcion);
}

function eliminarSeleccion(datosSelect)
{
  for(var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
  {
	datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
  }
}

function presentarCombo(id_param){
    storeCombo.proxy.extraParams = {opcion: id_param, port:'todo', limite:100};
    storeCombo.load({params: {}});
}

function presentarInterfaceModelo(id_param){
    storeInterfaceModelo.proxy.extraParams = {modeloElemento: id_param, port:'todo', limite:100};
    storeInterfaceModelo.load({params: {}});
}

function existeRecord(myRecord, grid)
{
  var existe=false;
  var num=grid.getStore().getCount();

  for(var i=0; i < num ; i++)
  {
    var opcion=grid.getStore().getAt(i).get('idCombo');

    if(opcion == myRecord.get('idCombo') ) 
    {
      existe=true;
      break;
    }
  }
  return existe;
}

function isInteger(n) {
    return (typeof n == 'number' && /^-?\d+$/.test(n+''));
}

function validarFormulario()
{
	obtenerRelaciones();

	var tareas=gridTareaInterfaceModeloTramo.getStore().getCount();

	var comboId = 0;  
	for(var i=0; i < gridTareaInterfaceModeloTramo.getStore().getCount(); i++)
	{  	
		if(!gridTareaInterfaceModeloTramo.getStore().getAt(i).data.idCombo)
	  	{
			comboId = comboId + 1;
	  	}
	}  

    var proceso = Ext.getCmp('cmb_proceso').getValue();    
    if(proceso=="" || !proceso){  proceso = 0; } 
	if(isInteger(proceso))
	{
		Ext.get('escogido_proceso_id').dom.value = proceso;	    
	    if(proceso==0)
	    {
			alert("No se ha escogido el Proceso");
	        return false;
	    }
		
		return true;
	} 
	else
	{
		if(Ext.get('escogido_proceso_id').dom.value > 0) 
		{
			return true;
		}
		else
		{
			alert("No se ha escogido el Proceso");
			return false;
		}
	}	
	
    var tarea_ant = Ext.getCmp('cmb_tarea_ant').getValue();    
    if(tarea_ant=="" || !tarea_ant){  tarea_ant = 0; }
	if(isInteger(tarea_ant))
	{
		Ext.get('escogido_tarea_ant_id').dom.value = tarea_ant;
	}
	
    var tarea_sig = Ext.getCmp('cmb_tarea_sig').getValue();    
    if(tarea_sig=="" || !tarea_sig){  tarea_sig = 0; }
	if(isInteger(tarea_sig))
	{
		Ext.get('escogido_tarea_sig_id').dom.value = tarea_sig;
	}
	
	if(tareas == 0)
	{
	    alert("No se han registrado las relaciones");
	    return false;
	}
  
	return true;
}

function obtenerRelaciones()
{
  var array_relaciones = new Object();
  array_relaciones['total'] =  gridTareaInterfaceModeloTramo.getStore().getCount();
  array_relaciones['tareasInterfacesModelosTramos'] = new Array();
  var array_data = new Array();
  for(var i=0; i < gridTareaInterfaceModeloTramo.getStore().getCount(); i++)
  {
  	array_data.push(gridTareaInterfaceModeloTramo.getStore().getAt(i).data);
  }
  array_relaciones['tareasInterfacesModelosTramos'] = array_data;
  Ext.get('telconet_schemabundle_admitareatype_tareasInterfacesModelosTramos').dom.value = Ext.JSON.encode(array_relaciones);
}