/**
 * Se crea funcion que permite el ingreso de Contactos para el formulario Soporte
 * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
 * @version 1.0 10-09-2021
 * @since 1.0
 */

Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
	
Ext.onReady(function() { 
    Ext.tip.QuickTipManager.init();
         
    Ext.define('ModelStore', {
        extend: 'Ext.data.Model',
        fields:
		[				
			{name:'idParametroDetEnvioSMS', mapping:'idParametroDetEnvioSMS'},
			{name:'descripcionProducto',    mapping:'descripcionProducto'},
			{name:'nombreTecnicoProducto',  mapping:'nombreTecnicoProducto'}
		],
        idProperty: 'idParametroDetEnvioSMS'
    });
	
    store = new Ext.data.Store({ 
        pageSize: 10,
        model: 'ModelStore',
        total: 'total',
        proxy: {
            type: 'ajax',
			timeout: 600000,
            url : strUrlGridAdmiFormularioSoporte,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        autoLoad: true
    });
   
    grid = Ext.create('Ext.grid.Panel', {
        id : 'grid',
        title: 'Listado de productos',
        width: 602,
        height: 300,
        store: store,
        plugins: [{ptype : 'pagingselectpersist'}],
        viewConfig: {
			enableTextSelection: true,
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
        columns:
        [
                {
                  id: 'descripcionProducto',
                  header: 'Descripción',
                  dataIndex: 'descripcionProducto',
                  width: 300,
                  sortable: true
                },
                {
                  id: 'nombreTecnicoProducto',
                  header: 'Nombre técnico',
                  dataIndex: 'nombreTecnicoProducto',
                  width: 180,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 120,
                    items: [
                        {
                            getClass: function(v, meta, rec) 
                            {
                                var strClassButton = 'button-grid-show';
                                if(rec.get('nombreTecnicoProducto')!= null)
                                {
                                    this.items[0].tooltip = 'Ver Correo';
                                }
                                else
                                {
                                    strClassButton          = "";
                                    this.items[0].tooltip   = '';
                                }
                                return strClassButton;
                            },
                            tooltip: 'Ver Correo',
                            handler: function(grid, rowIndex, colIndex) 
                            {
                                var rec = store.getAt(rowIndex);
                                var arrayParametros                 = [];
                                arrayParametros['nombreTecnicoProducto']        = rec.get('nombreTecnicoProducto');
                                VerContacto(arrayParametros);
                            }
                        },
                        {
                            getClass: function(v, meta, rec) 
                            {
                                var strClassButton = 'button-grid-edit';
                                if(rec.get('nombreTecnicoProducto')!= null)
                                {
                                    this.items[0].tooltip = 'Editar Correo';
                                }
                                else
                                {
                                    strClassButton          = "";
                                    this.items[0].tooltip   = '';
                                }
                                return strClassButton;
                            },
                            tooltip: 'Editar Correo',
                            handler: function(grid, rowIndex, colIndex) 
                            {
                                var rec = store.getAt(rowIndex);
                                var arrayParametros                 = [];
                                arrayParametros['nombreTecnicoProducto']        = rec.get('nombreTecnicoProducto');
                                ingresarContacto(arrayParametros);
                            }
                        }
                    ]
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });
    
});


 function ingresarContacto(arrayParametros)
 {
     var nombreProducto  = arrayParametros['nombreTecnicoProducto'];
 
     Ext.onReady(function()
     {
         Ext.define('GridCaractCorreo',
         {
             extend: 'Ext.data.Model',
             fields:
                 [
                     {name: 'idParametroDetCorreoForm', type: 'integer'},
                     {name: 'valor', type: 'string'},
                     {name: 'formaContacto', type: 'string'}
                 ]
         });
         storePersonaFormasContacto = Ext.create('Ext.data.Store',
         {
             autoDestroy: true,
             model: 'GridCaractCorreo',
             proxy:
                 {
                     type: 'ajax',
                     url: UrlGridCorreo,
                     reader:
                         {
                             type: 'json',
                             root: 'encontrados',
                             totalProperty: 'total'
                         },
                     extraParams:
                         {
                             nombreProducto: nombreProducto
                         },
                     simpleSortMode: true
                 }
         });
         var cellEditing = Ext.create('Ext.grid.plugin.CellEditing',
         {
             clicksToEdit: 2
         });
         var gridFormasContacto = Ext.create('Ext.grid.Panel',{
             store: storePersonaFormasContacto,
             columns : 
             [
                 {
                     text: 'Correo',
                     dataIndex: 'valor',
                     width: 400,
                     align: 'center',
                     editor:
                         {
                             width: '80%',
                             xtype: 'textfield',
                             allowBlank: false
                         }
                 },
                 {
                     text: 'Accion',
                     xtype: 'actioncolumn',
                     align: 'center',
                     width: 70,
                     sortable: false,
                     items:
                         [
                             {
                                 getClass: function()
                                 {
                                    var strEliminarFormaContacto = 'button-grid-invisible';
                                    var permiso     = $("#ROLE_469-1");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if(!boolPermiso)
                                    {
                                        return strEliminarFormaContacto;
                                    }
                                    else
                                    {
                                        strEliminarFormaContacto = 'button-grid-delete';
                                        return strEliminarFormaContacto;
                                    }
                                 },
                                 tooltip: 'Eliminar Correo',
                                 style: 'cursor:pointer',
                                 handler: function(grid, rowIndex, colIndex)
                                 {
                                     storePersonaFormasContacto.removeAt(rowIndex);
                                 }
                             }
                         ]
                 }
             ],
             selModel:
                 {
                     selType: 'cellmodel'
                 },
             width: 300,
             height: 300,
             title: '',
             tbar:
             [
                 {
                     text: 'Agregar',
                     handler: function()
                     {
                         var boolError = false;
                         var columna = 0;
                         var fila = 0;
                         for (var i = 0; i < gridFormasContacto.getStore().getCount(); i++)
                         {
                             variable = gridFormasContacto.getStore().getAt(i).data;
                             boolError = trimAll(variable['formaContacto']) == '';
     
                             if (boolError)
                             {
                                 fila = i;
                                 break;
                             }
                             else
                             {
                                 boolError = trimAll(variable['valor']) == '';
                                 if (boolError)
                                 {
                                     columna = 1;
                                     fila = i;
                                     break;
                                 }
                             }
                         }
                         if (!boolError)
                         {
                             var r = Ext.create('GridCaractCorreo',
                                 {
                                    formaContacto: '',
                                    valor: ''
                                 });
                             storePersonaFormasContacto.insert(0, r);
                         }
                         cellEditing.startEditByPosition({row: fila, column: columna});
                     }
                 }
             ],
             plugins: [cellEditing],
             bbar:
             [
                 {
                     text: 'Guardar',
                     xtype: 'button',
                     style: 'margin-left:170px',
                     handler: function()
                     {
                        var array_data = grabarFormasContacto();
                        var boolValidaGrid = validaFormasContacto();
                        if(array_data && boolValidaGrid != false)
                        {
                            Ext.Msg.confirm('Alerta', 'Seguro desea guardar los cambios?', function (btn) {
                                if(btn=='yes')
                                {
                                    Ext.MessageBox.wait("Guardando Correos...");
                                    Ext.Ajax.request({
                                        url: urlRegistroCorreoForm,
                                        method: 'POST',
                                        dataType: 'json',
                                        timeout: 400000,
                                        params:
                                        {
                                            nombreProducto     : nombreProducto,
                                            array_data         : array_data
                                        },
                                        success: function (response)
                                        {
                                                var variable = response.responseText;
                                                
                                                if ("OK" == variable)
                                                {
                                                    Ext.Msg.alert('Mensaje del Sistema', 'Los Contactos han sido guardado correctamente');
                                                    storePersonaFormasContacto.load();
                                                }
                                                else
                                                {
                                                    Ext.Msg.alert('Error ', result.responseText);
                                                }
                                                
                                        },
                                        failure: function (response) 
                                        {
                                            Ext.Msg.alert('Error', 'Error al Guardar los Contactos, por favor consulte con el Administrador.');
                                        }
                                    });
                                }
                            })
                        }
                         
                     }
                 },
                 {
                     text: 'Cancelar',
                     xtype: 'button',
                     style: 'margin-left:10px',
                     handler: function()
                     {
                         winActualizarCorreo.close();
                     }
                 }
             ]
         });
         storePersonaFormasContacto.load();
         var winActualizarCorreo = Ext.create('Ext.window.Window',
             {
                title: 'Ingreso de Correo Electronico',
                modal: true,
                width: 485,
                closable: true,
                layout: 'fit',
                items: [gridFormasContacto]
             }).show();

             function trimAll(texto)
             {
                 return texto.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g, '').replace(/\s+/g, ' ').trim();
             }
             function grabarFormasContacto()
             {
                 var variable = new Array();
                 for (var i = 0; i < gridFormasContacto.getStore().getCount(); i++)
                 {
                     variable[i] = gridFormasContacto.getStore().getAt(i).data;
                     if(variable[i]['formaContacto']=="")
                     {
                         variable[i]['formaContacto'] = 'Correo Electronico';
                     }
                     if(variable[i]['valor'] == '' || variable[i]['valor'] == null)
                     {
                         Ext.Msg.alert("Error",'El campo Correo se encuentra vacio, por favor corregir.');
                         return null;
                     }
                 }
                 return Ext.JSON.encode(variable);
            }
            function validaFormasContacto()
            {
               return Utils.validaFormasContacto(gridFormasContacto);
            }
             
    });
 }

 function VerContacto(arrayParametros)
 {
     var nombreProducto  = arrayParametros['nombreTecnicoProducto'];
 
     Ext.onReady(function()
     {
         Ext.define('GridCaractCorreo',
         {
             extend: 'Ext.data.Model',
             fields:
                 [
                     {name: 'idParametroDetCorreoForm', type: 'integer'},
                     {name: 'valor', type: 'string'},
                     {name: 'formaContacto', type: 'string'}
                 ]
         });
         storePersonaFormasContacto = Ext.create('Ext.data.Store',
         {
             autoDestroy: true,
             model: 'GridCaractCorreo',
             proxy:
                 {
                     type: 'ajax',
                     url: UrlGridCorreo,
                     reader:
                         {
                             type: 'json',
                             root: 'encontrados',
                             totalProperty: 'total'
                         },
                     extraParams:
                         {
                             nombreProducto: nombreProducto
                         },
                     simpleSortMode: true
                 }
         });
         var cellEditing = Ext.create('Ext.grid.plugin.CellEditing',
         {
             clicksToEdit: 2
         });
         var gridFormasContacto = Ext.create('Ext.grid.Panel',{
             store: storePersonaFormasContacto,
             columns : 
             [
                 {
                     text: 'Correo',
                     dataIndex: 'valor',
                     width: 400,
                     align: 'center',
                     editor:
                         {
                             width: '80%',
                             xtype: 'textfield',
                             allowBlank: false,
                             readOnly: true
                         }
                 }
             ],
             selModel:
                 {
                     selType: 'cellmodel'
                 },
             width: 300,
             height: 300,
             title: '',
             plugins: [cellEditing],
             bbar:
             [
                 {
                     text: 'Cancelar',
                     xtype: 'button',
                     style: 'margin-left:170px',
                     handler: function()
                     {
                         winActualizarCorreo.close();
                     }
                 }
             ]
         });
         storePersonaFormasContacto.load();
         var winActualizarCorreo = Ext.create('Ext.window.Window',
             {
                title: 'Ver Correos Electronicos',
                modal: true,
                width: 414,
                closable: true,
                layout: 'fit',
                items: [gridFormasContacto]
             }).show();
    });
 }