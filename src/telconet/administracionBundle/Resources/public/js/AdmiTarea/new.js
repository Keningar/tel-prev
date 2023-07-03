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
		autoLoad: true,
		proxy: {
			type: 'ajax',
			url : 'getProcesos',
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
		autoLoad: true,
		proxy: {
			type: 'ajax',
			url : 'getTareas',
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
		autoLoad: true,
		proxy: {
			type: 'ajax',
			url : 'getTareas',
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
    
    /***************************************************************************/
    
    storeTipoElementos = new Ext.data.Store({ 
        pageSize: 200,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : getTiposElementos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Todos'
            }
        },
        fields:
              [
                {name:'idTipoElemento', mapping:'idTipoElemento'},
                {name:'nombreTipoElemento', mapping:'nombreTipoElemento'}
              ]
    });
    
    
    
    /****************************************************************************/
    
    storeCombo = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '../../../administracion/soporte/admi_tarea/getJsonPorOpcion',
            extraParams: {opcion: '999'},
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
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '../../../administracion/tecnico/admi_interface_modelo/getInterfaceModelo',
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
    
    Ext.define('TareaInterfaceModeloTramo', {
        extend: 'Ext.data.Model',
        fields: [
           // {name:'opcion', mapping:'opcion'},
            {name:'tipoElementoId', mapping:'idTipoElemento'},
	    {name:'tipoElementoNombre', mapping:'nombreTipoElemento'},
	    
            {name:'idCombo', mapping:'id'},
            {name:'nombreCombo', mapping:'nombre'},
            {name:'interfaceModeloId', mapping:'idInterfaceModelo'},
            {name:'interfaceTipoNombre', mapping:'nombreTipoInterface'},
            {name:'script'}
        ]
    });
    
    storeTareaInterfaceModeloTramo = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoDestroy: true,
        autoLoad: false,
        model: 'TareaInterfaceModeloTramo',        
        proxy: {
            type: 'ajax',
            // load remote data using HTTP
            url: 'gridTareaInterfaceModeloTramo',
            // specify a XmlReader (coincides with the XML format of the returned data)
            reader: {
                type: 'json',
                totalProperty: 'total',
                // records will have a 'plant' tag
                root: 'tareasInterfacesModelosTramos'
            }
        }
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
    
    var selTareaInterfaceModeloTramo = Ext.create('Ext.selection.CheckboxModel', {
        listeners: {
            selectionchange: function(sm, selections) {
                gridTareaInterfaceModeloTramo.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    });
    
    gridTareaInterfaceModeloTramo = Ext.create('Ext.grid.Panel', {
        id:'gridTareaInterfaceModeloTramo',
        store: storeTareaInterfaceModeloTramo,
        columnLines: true,
        columns: [
        {
            id: 'tipoElementoId',
            header: 'tipoElementoId',
            dataIndex: 'tipoElementoId',
            hidden: true,
            hideable: false
        },
        {
            id: 'tipoElementoNombre',
            header: 'Opcion',
            dataIndex: 'tipoElementoNombre',
            width: 100,
            sortable: true,
	    
	    renderer: function (value, metadata, record, rowIndex, colIndex, store){
	      
                if (typeof(record.data.tipoElementoNombre) == "number")
                {
                    record.data.tipoElementoId = record.data.tipoElementoNombre;
		    
                    for (var i = 0;i< storeTipoElementos.data.items.length;i++)
                    {
                        if (storeTipoElementos.data.items[i].data.idTipoElemento == record.data.tipoElementoId)
                        {			   
                            record.data.tipoElementoNombre = storeTipoElementos.data.items[i].data.nombreTipoElemento;
                            break;
                        }
                    }
                }
               
                return record.data.tipoElementoNombre;
            },
            editor: 
            {                
                xtype: 'combobox',
		typeAhead: true,
                displayField:'nombreTipoElemento',
                valueField: 'idTipoElemento',
                queryMode: 'remote',
                loadingText: 'Buscando ...',
                store: storeTipoElementos,
                listClass: 'x-combo-list-small',
		listeners: {
		      select: function(combo){				  
			  cargarStores(combo.getValue());
		      }
		}
            }
//             renderer: function (value, metadata, record, rowIndex, colIndex, store){
// 	      
//                     record.data.valor = record.data.opcion;
//                     for(var i = 0;i< comboOpcion.data.items.length;i++){
//                         if(comboOpcion.data.items[i].data.valor == value){
//                             record.data.opcion = comboOpcion.data.items[i].data.opcion;                            
//                             break;
//                         }
//                     }                    
//                     return record.data.opcion;
//                 },
//             editor   :  {
//                     xtype: 'combobox',
//                     id:'comboOpcion',
//                     name: 'comboOpcion',
//                     store: comboOpcion,
//                     displayField: 'opcion',
//                     valueField: 'valor',
//                     queryMode: 'local',
//                     lazyRender: true,
//                     listeners: {
//                         select: function(combo){			    
//                             cargarStores(combo.getValue());
//                         }
//                     },
//                     emptyText: '',
//                     forceSelection: true
//                 }
        },
	{
            id: 'idCombo',
            header: 'idCombo',
            dataIndex: 'idCombo',
            hidden: true,
            hideable: false
        }, {
            id: 'nombreCombo',
            header: 'Elemento/Tramo',
            dataIndex: 'nombreCombo',
            width: 420,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                if (typeof(record.data.nombreCombo) == "number")
                {
                    record.data.idCombo = record.data.nombreCombo;
                    for (var i = 0;i< storeCombo.data.items.length;i++)
                    {
                        if (storeCombo.data.items[i].data.id == record.data.idCombo)
                        {
                            record.data.nombreCombo = storeCombo.data.items[i].data.nombre;
//                             if (record.data.opcion == "Elemento")
//                             {
//                                 presentarInterfaceModelo(record.data.idCombo);
//                             }
                            break;
                        }
                    }
                }
                return record.data.nombreCombo;
            },
            editor: 
            {                
                xtype: 'combobox',
                displayField:'nombre',
                valueField: 'id',
                queryMode: 'local',
                loadingText: 'Buscando ...',
                store: storeCombo,
                listClass: 'x-combo-list-small',
		listeners: {
		      select: function(combo){				
			  presentarInterfaceModelo(combo.getValue());
		      }
		}
            }
        },
        {
            id: 'interfaceModeloId',
            header: 'interfaceModeloId',
            dataIndex: 'interfaceModeloId',
            hidden: true,
            hideable: false
        }, {
            id: 'interfaceTipoNombre',
            header: 'Tipo Interface',
            dataIndex: 'interfaceTipoNombre',
            width: 220,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                if (typeof(record.data.interfaceTipoNombre) == "number")
                {
                    record.data.interfaceModeloId = record.data.interfaceTipoNombre;
                    for (var i = 0;i< storeInterfaceModelo.data.items.length;i++)
                    {
                        if (storeInterfaceModelo.data.items[i].data.idInterfaceModelo == record.data.interfaceModeloId)
                        {
                            record.data.interfaceTipoNombre = storeInterfaceModelo.data.items[i].data.nombreTipoInterface;
                            break;
                        }
                    }
                }
                return record.data.interfaceTipoNombre;
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
            width: 73,
            items: [{
	      
                getClass: function(v, meta, rec) {return 'button-grid-agregarScript'},
                tooltip: 'Agregar Script',
                handler: function(grid, rowIndex, colIndex) {		  			
		  
                        if(grid.getStore().getAt(rowIndex).data.opcion!="Tramo"){
                            agregarScript(grid.getStore().getAt(rowIndex).data);
                        }
                    }
                }
            ]
        }
        ],
        selModel: selTareaInterfaceModeloTramo,
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
                        //opcion:             '',
		        tipoElementoId:'',
			tipoElementoNombre:'',
			
                        idCombo:                 '',
                        nombreCombo:             '',
                        interfaceModeloId:  '',
                        interfaceTipoNombre:  '',
                        script:             ''
                    });
                    if(!existeRecord(r, gridTareaInterfaceModeloTramo))
                    {
                        storeTareaInterfaceModeloTramo.insert(0, r);
                        cellEditing.startEditByPosition({row: 0, column: 1});
                    }
                    else
                    {
                      alert('Ya existe un registro vacio.');
                    }
                }
            }]
        }],

        width: 850,
        height: 200,
        frame: true,
        title: 'Agregar Detalles Tareas',
        renderTo: 'grid',
        plugins: [cellEditing]
    });


});

function agregarScript(data){
    if(data.opcion!="Tramo"){        
        
        var comboOpcionScript = new Ext.data.Store({ 
            total: 'total',
            autoLoad:true,
            proxy: {
                type: 'ajax',
                url : '../../tecnico/admi_modelo_elemento/getAllDetalles',
                extraParams: {idModelo: data.idCombo},
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

function validarFormulario()
{
	obtenerRelaciones();
	//  var tareas=gridTareaInterfaceModeloTramo.getStore().getCount();

	var comboId = 0;  
	for(var i=0; i < gridTareaInterfaceModeloTramo.getStore().getCount(); i++)
	{  	
		if(!gridTareaInterfaceModeloTramo.getStore().getAt(i).data.idCombo)
		{
			comboId = comboId + 1;
		}
	}  
	//if(tareas == 0)
	//  {
	//    alert("No se han registrado las relaciones");
	//    return false;
	//  }
	
    var proceso = Ext.getCmp('cmb_proceso').getValue();    
    if(proceso=="" || !proceso){  proceso = 0; }
    Ext.get('escogido_proceso_id').dom.value = proceso;      
    if(proceso==0)
    {
        alert("No se ha escogido el Proceso");
        return false;
    }	
	
    var tarea_ant = Ext.getCmp('cmb_tarea_ant').getValue();    
    if(tarea_ant=="" || !tarea_ant){  tarea_ant = 0; }
    Ext.get('escogido_tarea_ant_id').dom.value = tarea_ant;
	
    var tarea_sig = Ext.getCmp('cmb_tarea_sig').getValue();    
    if(tarea_sig=="" || !tarea_sig){  tarea_sig = 0; }
    Ext.get('escogido_tarea_sig_id').dom.value = tarea_sig;	
	
	if(comboId>0)
	{
		alert("Por lo menos un registro se encuentra vacia");
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
  console.log(Ext.JSON.encode(array_relaciones));
}