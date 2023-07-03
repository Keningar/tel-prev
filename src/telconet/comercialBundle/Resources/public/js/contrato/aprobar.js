Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

            var itemsPerPage = 10;
            var store='';
            var area_id='';
            var login_id='';
            var tipo_asignacion='';
            var pto_sucursal='';
            var idClienteSucursalSesion;            
            
/*** El codigo con el cual definiras tu grid debe estar dentro del siguiente codigo:
 *      Ext.onReady(function(){
 *              //--Aqui va todo tu codigo
 *       });
 *****/
            Ext.onReady(function(){

            Ext.define('modelOficina', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idOficina', type: 'string'},
                    {name: 'nombre',  type: 'string'}        
                ]
            });

            var oficina_store = Ext.create('Ext.data.Store', {
		    autoLoad: false,
		    model: "modelOficina",
		    proxy: {
		        type: 'ajax',
		        url : url_lista_oficinas,
		        reader: {
		            type: 'json',
		            root: 'oficinas'
                        }
                    }
            });	
            var oficinas_cmb = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: oficina_store,
                labelAlign : 'left',
                id:'idOficina',
                name: 'idOficina',
				valueField:'idOficina',
                displayField:'nombre',
                fieldLabel: 'Oficina',
				width: 350,
				triggerAction: 'all',
				selectOnFocus:true,
				lastQuery: '',
				mode: 'local',
				allowBlank: true,
                matchFieldWidth: false,
				listeners: {
					select:
					function(e) {
						oficina_id = Ext.getCmp('idOficina').getValue();
					},
					click: {
						element: 'el', //bind to the underlying el property on the panel
						fn: function(){ 
							oficina_id='';
							oficina_store.removeAll();
							oficina_store.load();
						}
					}			
				}
            });

            Ext.define('modelMotivo', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idMotivo', type: 'string'},
                    {name: 'descripcion',  type: 'string'},
                    {name: 'idRelacionSistema',  type: 'string'}                 
                ]
            });

            var motivo_store = Ext.create('Ext.data.Store', {
		    autoLoad: false,
		    model: "modelMotivo",
		    proxy: {
		        type: 'ajax',
		        url : url_lista_motivos,
		        reader: {
		            type: 'json',
		            root: 'motivos'
                        }
                    }
            });	
            var motivo_cmb = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: motivo_store,
                labelAlign : 'left',
                id:'idMotivo',
                name: 'idMotivo',
				valueField:'idMotivo',
                displayField:'descripcion',
                fieldLabel: 'Motivo Rechazo',
				width: 400,
				triggerAction: 'all',
				selectOnFocus:true,
				lastQuery: '',
				mode: 'local',
				allowBlank: true,
				listeners: {
					select:
					function(e) {
						//alert(Ext.getCmp('idestado').getValue());
						motivo_id = Ext.getCmp('idMotivo').getValue();
						relacion_sistema_id=e.displayTplData[0].idRelacionSistema;
					},
					click: {
						element: 'el', //bind to the underlying el property on the panel
						fn: function(){ 
							motivo_id='';
							relacion_sistema_id='';
							motivo_store.removeAll();
							motivo_store.load();
						}
					}			
				}
            });             
                
            //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
            DTFechaDesde = new Ext.form.DateField({
                    id: 'fechaDesde',
                    fieldLabel: 'Desde',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:300,
                    //anchor : '65%',
                    //layout: 'anchor'
            });
            DTFechaHasta = new Ext.form.DateField({
                    id: 'fechaHasta',
                    fieldLabel: 'Hasta',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:300,
                    //anchor : '65%',
                    //layout: 'anchor'
            });
               
            TFNombre = new Ext.form.TextField({
                    id: 'nombre',
                    fieldLabel: 'Identificacion',
                    xtype: 'textfield'
            });
            
          // Agrego Campo en la busqueda para filtrar los contratos Nuevo o los contratos generados 
          // por Cambio de Razon Social por Punto
          var states = Ext.create('Ext.data.Store', {
		  fields: ['abbr', 'idTipoContratoAprob'],
		  data: [	
		  {
		      "abbr": "Contrato Nuevo",
		      "idTipoContratoAprob": "Contrato Nuevo"
		  },
		  {
		      "abbr": "Cambio de Razon Social",
		      "idTipoContratoAprob": "Cambio de Razon Social"
		  }]
	      });
          
	    var cmbTipoContratoAprob = Ext.create('Ext.form.ComboBox', {
		  xtype: 'combobox',
		  fieldLabel: 'Tipo de Aprobacion',
		  store: states,
		  queryMode: 'local',
		  id:'idTipoContratoAprob',
		  name: 'idTipoContratoAprob',
		  valueField:'idTipoContratoAprob',
		  displayField:'idTipoContratoAprob',		  
		  width: 300,
		  triggerAction: 'all',
		  selectOnFocus:true,
		  lastQuery: '',
		  mode: 'local',
		  allowBlank: false,
          value: 'Contrato Nuevo',
          listeners:
              {
                select: function(combo)
                {
                    cambioRazonSocial = combo.displayTplData[0].abbr == 'Cambio de Razon Social';
                    Ext.getCmp('cmbOrigenContrato').setDisabled(cambioRazonSocial);
                    Ext.getCmp('documento').setDisabled(true);
                    if (cambioRazonSocial)
                    {
                        Ext.getCmp('cmbOrigenContrato').setValue('WEB');
                        Ext.getCmp('documento').setValue('P');
                    }
                }
            }
	    });                

    var dataStoreOrigenContrato = Ext.create('Ext.data.Store',
        {
            fields: ['id', 'descripcion'],
            data:
                [
                    {
                        "id": "WEB",
                        "descripcion": "Físico"
                    },
                    {
                        "id": "MOVIL",
                        "descripcion": "Digital"
                    }
                ]
        });
        
    var dataStoreDocumento = Ext.create('Ext.data.Store',
        {
            fields: ['id', 'descripcion'],
            data:
                [
                    {
                        "id": "P",
                        "descripcion": "Pendiente"
                    },
                    {
                        "id": "E",
                        "descripcion": "Entregado"
                    }
                ]
        });

    var dataStorePendienteAprobado = Ext.create('Ext.data.Store',
        {
            fields: ['id', 'descripcion'],
            data:
                [
                    {
                        "id": "P",
                        "descripcion": "Pendientes"
                    },
                    {
                        "id": "E",
                        "descripcion": "Entregados"
                    }
                ]
        });

    var cmbOrigenContrato = Ext.create('Ext.form.ComboBox',
        {
            xtype: 'combobox',
            fieldLabel: 'Tipo Contrato',
            store: dataStoreOrigenContrato,
            queryMode: 'local',
            id: 'cmbOrigenContrato',
            valueField: 'id',
            displayField: 'descripcion',
            width: 300,
            triggerAction: 'all',
            selectOnFocus: true,
            lastQuery: '',
            mode: 'local',
            allowBlank: false,
            value: 'WEB',
            editable: false,
            listeners:
                {
                    select: function(combo)
                    {
                        cambioRazonSocial = combo.displayTplData[0].id == 'WEB';
                        Ext.getCmp('documento').setDisabled(cambioRazonSocial);
                        if(cambioRazonSocial)
                        {
                            Ext.getCmp('documento').setValue('P');
                        }
                    }
                }
        });

    var cmbDocumento = Ext.create('Ext.form.ComboBox',
        {
            xtype: 'combobox',
            fieldLabel: 'Documento',
            labelAlign : 'right',
            store: dataStoreDocumento,
            queryMode: 'local',
            id: 'documento',
            valueField: 'id',
            displayField: 'descripcion',
            width: 300,
            triggerAction: 'all',
            selectOnFocus: true,
            lastQuery: '',
            mode: 'local',
            allowBlank: false,
            editable: false,
            value: 'P'
        });

    Ext.getCmp('documento').setDisabled(true);
    Ext.getCmp('cmbOrigenContrato').setRawValue('Físico');
    
    // Solo MD tiene contratos digitales.
    if (prefijoEmpresa !== 'MD' && prefijoEmpresa !== 'EN')
    {
        Ext.getCmp('documento').hide();
        Ext.getCmp('cmbOrigenContrato').hide();
    }

    var cmbPendienteAprobado = Ext.create('Ext.form.ComboBox',
        {
            xtype: 'combobox',
            fieldLabel: 'Situacion',
            store: dataStorePendienteAprobado,
            queryMode: 'local',
            id: 'pendienteAprobado',
            valueField: 'id',
            displayField: 'descripcion',
            width: 300,
            triggerAction: 'all',
            selectOnFocus: true,
            lastQuery: '',
            mode: 'local',
            allowBlank: false,
            value: 'Todos'
        });
                             
        
                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: [{name:'id', type: 'string'}, 
			    {name:'Numerocontrato', type: 'string'},
                            {name:'Numerocontratoemppub', type: 'string'},
                            {name:'Valorcontrato', type: 'string'},
                            {name:'Valoranticipo', type: 'string'},
                            {name:'Valorgarantia', type: 'string'},
                            {name:'Fefincontrato', type: 'string'},
                            {name:'estado', type: 'string'},
                            {name:'linkVer', type: 'string'},
                            {name:'linkProcesoAprobar', type: 'string'},
                            {name:'strLinkAprobCambioRazonSocial', type: 'string'},                            
                            {name:'cliente', type: 'string'},
                            {name:'origen', type: 'string'},
                            {name:'linkVerArchivo', type: 'string'},
                            {name:'linkVerEntregables', type: 'string'},
                            {name:'oficina', type: 'string'},   
                            {name:'linkAprobar', type: 'string'},
                            {name:'linkCheckList', type: 'string'}
                            ]
                }); 

        /***********
        *Para definir tu grid debes declarar un Store con el cual le indicaras la manera de obtener tus datos (en este caso en formato JSON)        
        ***********/
                store = Ext.create('Ext.data.JsonStore', {
                    model: 'ListaDetalleModel',
                            pageSize: itemsPerPage,
                    proxy: {
                        type: 'ajax',
                        url: url_store,
                        reader: {
                            type: 'json',
                            root: 'arreglo',
                            totalProperty: 'total'
                        },
                        extraParams:{fechaDesde:'',fechaHasta:'',idOficina:'',nombre:''},
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(store)
                        {
                                store.getProxy().extraParams.fechaDesde          = Ext.getCmp('fechaDesde').getValue();
                                store.getProxy().extraParams.fechaHasta          = Ext.getCmp('fechaHasta').getValue();
                                store.getProxy().extraParams.idOficina           = Ext.getCmp('idOficina').getValue();
                                store.getProxy().extraParams.nombre              = Ext.getCmp('nombre').getValue();
                                store.getProxy().extraParams.idTipoContratoAprob = Ext.getCmp('idTipoContratoAprob').getValue();  
                                store.getProxy().extraParams.origen              = Ext.getCmp('cmbOrigenContrato').getValue();  
                                store.getProxy().extraParams.documento           = Ext.getCmp('documento').getValue();  
                        },
                        load: function(store){  
                            store.each(function(record) {                              
                            });
                        }
                    }
                });
        /******************/
               // store.load({params: {start: 0, limit: 10}});    
               store.load();  


        /*****Arreglo de Check de seleccion******/
                sm = new Ext.selection.CheckboxModel( {
                    listeners:{
                        selectionchange: function(selectionModel, selected, options){
                            arregloSeleccionados= new Array();
                            Ext.each(selected, function(record){
                                    //arregloSeleccionados.push(record.data.idOsDet);
                    });			
                        }
                    }
                });
         /***************************************/
    
                var listView = Ext.create('Ext.grid.Panel', {
                    width:1200,
                    height:300,
                    collapsible:false,
                    title: '',
                    selModel: sm,
                    dockedItems: [ {
                                    xtype: 'toolbar',
                                    dock: 'top',
                                    align: '->',
                                    items: [
                                        motivo_cmb,
                                        //tbfill -> alinea los items siguientes a la derecha
                                        { xtype: 'tbfill' },
					, /*{
                                        iconCls: 'icon_check',
                                        text: 'Aprobar',
                                        disabled: false,
                                        itemId: 'aprobar',
                                        scope: this,
                                        handler: function(){aprobarAlgunos()}
                                    },*/ {
                                        iconCls: 'icon_delete',
                                        text: 'Rechazar',
                                        disabled: false,
                                        itemId: 'rechazar',
                                        scope: this,
                                        handler: function(){rechazarAlgunos()}
                                    }
                                
                                ]}],                    
                    renderTo: Ext.get('lista_contratos'),
                    // paging bar on the bottom
                    bbar: Ext.create('Ext.PagingToolbar', {
                        store: store,
                        displayInfo: true,
                        displayMsg: 'Mostrando contratos {0} - {1} of {2}',
                        emptyMsg: "No hay datos para mostrar"
                    }),	
                    store: store,
                    multiSelect: false,
                    viewConfig: {
                        emptyText: '<center><br><b>No hay datos para mostrar',
                        loadingText: '<b>Cargando Contratos, Por favor espere...',
                        getRowClass: function(record, rowIndex, rp, store) 
                        {
                            return "height: 45px";
                        }
                    },
                    columns: [new Ext.grid.RowNumberer(),  
                            {
                        text: 'Oficina Cliente',
                        width: 195,
                        dataIndex: 'oficina'
                    },                        
                            {
                        text: 'Número de contrato',
                        width: 110,
                        dataIndex: 'Numerocontrato'
                    },{
                        text: 'Número emp. pública',
                        width: 120,
                        dataIndex: 'Numerocontratoemppub'
                    },{
                        text: 'Cliente',
                        width: 220,
                        dataIndex: 'cliente'
                    },{
                        text: 'Valor',
                        dataIndex: 'Valorcontrato',
                        align: 'right',
                        width: 70			
                    },{
                        text: 'Anticipo',
                        dataIndex: 'Valoranticipo',
                        align: 'right',
                        width: 50			
                    },{
                        text: 'Garantía',
                        dataIndex: 'Valorgarantia',
                        align: 'right',
                        width: 60			
                    },{
                        text: 'Fecha fin contrato',
                        dataIndex: 'Fefincontrato',
                        align: 'right',
                        flex: 40
                    },{
                        text: 'Estado',
                        dataIndex: 'estado',
                        align: 'right',
                        flex: 20
                    },{
                        text: 'Acciones',
                        width: 140,
                        renderer: renderAcciones
                    }]
                });  
                
    /*
     * Documentación para el método "renderAcciones"
     * 
     * @param String value  valor para procesos específicos.
     * @param Objetc p      objeto html.
     * @param Record record registro actual en el Data Store.
     * 
     * @returns String Contenido Html para presentar los botones de acción.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.1 15-02-2016
     * Se organiza el código y la presentación de los botones de acción
     * Se cambia de estilo de los botones para guarden simetría en su visualización.
     */
    function renderAcciones(value, p, record)
    {
        var arrayBotones = [];
        var rec          = record.data;
        
        arrayBotones.push('<b><a href="' + rec.linkVer + '" onClick="" title="Ver" class="button-grid-show-2"></a></b>');
        
        if (rec.linkProcesoAprobar !== '')
        {
            var titulo = 'Proceso Aprobación';
            
            arrayBotones.push('<b><a href="' + rec.linkProcesoAprobar + '" onClick="" title="' + titulo + '" class="button-grid-Tuerca-2"></a></b>');
        }
        if (rec.strLinkAprobCambioRazonSocial !== '')
        {
            arrayBotones.push('<b><a href="' + rec.strLinkAprobCambioRazonSocial + 
                              '" onClick="" title="Proceso Aprobacion" class="button-grid-aprobar"></a></b>');
        }
        arrayBotones.push('<b><a href="#" onClick="verDocumentos(\'' + rec.linkVerArchivo + 
                          '\')" title="Ver Archivos Digitales" class="button-grid-pdf-2"></a></b>');

        // Si el contrato NO es Físico y pertenece a la empresa MD se muestra la acción de seleccionar los documentos entregables
        if (rec.origen !== 'WEB' && (prefijoEmpresa === 'MD' ||  prefijoEmpresa === 'EN'))
        {
            arrayBotones.push('<b><a href="#" onClick="verEntregables(\'' + rec.linkVerEntregables + '\', \'' + rec.Numerocontrato + 
                              '\')" title="Documentos entregables" class="button-grid-CheckList"></a></b>');
        }
        
        // Se define la tabla en base a la cantidad de botones.
        var acciones = '<table><tr height="30px"><td>';
        var idx      = 1;
        
        Ext.Array.each(arrayBotones, function(rec)
        {
            acciones += rec;
            // máximo 4 botones por fila.
            if (idx % 4 === 0 && arrayBotones.length > 4)
            {
                acciones += '</td></tr><tr height="30px"><td>'; // Divisor de Línea.
            }
            
            idx++;
        });
        
        acciones += '</td></tr></table>';
        
        return acciones;
    }

            var filterPanel = Ext.create('Ext.panel.Panel', {
                bodyPadding: 7,  // Don't want content to crunch against the borders
                border:false,
                buttonAlign: 'center',
                layout:{
                    type:'table',
                    columns: 6,
                    align: 'left',
                },
                bodyStyle: {
                            background: '#fff'
                        },                     

                collapsible : true,
                collapsed: true,
                width: 1200,
                title: 'Criterios de busqueda',
                
                    buttons: [
                        
                        {
                            text: 'Buscar',
                            //xtype: 'button',
                            iconCls: "icon_search",
                            handler: Buscar,
                        },
                        {
                            text: 'Limpiar',
                            iconCls: "icon_limpiar",
                            handler: function(){ limpiar();}
                        }
                        
                        ],                

                        items:
                        [
                            DTFechaDesde,
                            {html: "&nbsp;", border: false, width: 50},
                            DTFechaHasta,
                            {html: "&nbsp;", border: false, width: 605},
                            {html: "&nbsp;", border: false, width: 605},
                            {html: "&nbsp;", border: false, width: 605},
                            
                            oficinas_cmb,
                            {html: "&nbsp;", border: false, width: 50},
                            TFNombre,
                            {html: "&nbsp;", border: false, width: 50},
                            {html: "&nbsp;", border: false, width: 50},
                            {html: "&nbsp;", border: false, width: 50},
                            
                            cmbTipoContratoAprob,
                            {html: "&nbsp;", border: false, width: 50},
                            cmbOrigenContrato,
                            cmbDocumento,
                            {html: "&nbsp;", border: false, width: 5},
                            {html: "&nbsp;", border: false, width: 50}
                        ],		
                renderTo: 'filtro_contratos'
            }); 
    
});
/*** fin Ext.onReady****/
function Buscar(){
             if  (( Ext.getCmp('fechaDesde').getValue()!=null)&&(Ext.getCmp('fechaHasta').getValue()!=null) )
		{
			if (Ext.getCmp('fechaDesde').getValue() > Ext.getCmp('fechaHasta').getValue())
			{
			   Ext.Msg.show({
			   title:'Error en Busqueda',
			   msg: 'Por Favor para realizar la busqueda Fecha Desde debe ser fecha menor a Fecha Hasta.',
			   buttons: Ext.Msg.OK,
			   animEl: 'elId',
			   icon: Ext.MessageBox.ERROR
				});		 

			}
			else
			{			
				store.loadPage(1);                                
			}
		}
		else
		{
            store.loadPage(1);
		}	

}

function aprobarAlgunos(){
var param = '';
if(sm.getSelection().length > 0)
{
  var estado = 0;
  for(var i=0 ;  i < sm.getSelection().length ; ++i)
  {
    param = param + sm.getSelection()[i].data.id;

    if(sm.getSelection()[i].data.estado == 'Eliminado')
    {
      estado = estado + 1;
    }
    if(i < (sm.getSelection().length -1))
    {
      param = param + '|';
    }
  }      
  if(estado == 0)
  {
    Ext.Msg.confirm('Alerta','Se aprobaran los contratos seleccionados. Desea continuar?', function(btn){
        if(btn=='yes'){
            Ext.Ajax.request({
                url: url_aprobar,
                method: 'post',
                params: { param : param},
                success: function(response){
                    var text = response.responseText;
                    store.load();
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                }
            });
        }
    });

  }
  else
  {
    alert('Por lo menos uno de las registro se encuentra en estado ELIMINADO');
  }
}
else
{
  alert('Seleccione por lo menos un registro de la lista');
}
}


function rechazarAlgunos(){
    var param = '';
    if(sm.getSelection().length > 0)
    {
      var estado = 0;
      for(var i=0 ;  i < sm.getSelection().length ; ++i)
      {
        param = param + sm.getSelection()[i].data.id;

        if(sm.getSelection()[i].data.estado == 'Eliminado')
        {
          estado = estado + 1;
        }
        if(i < (sm.getSelection().length -1))
        {
          param = param + '|';
        }
      }      
      if(estado == 0)
      {
          
        if (Ext.getCmp('idMotivo').getValue()){  
        Ext.Msg.confirm('Alerta','Se rechazaran los contratos seleccionados. Desea continuar?', function(btn){
            if(btn=='yes'){
                Ext.Ajax.request({
                    url: url_rechazar,
                    method: 'post',
                    params: { param : param, motivoId:motivo_id },
                    success: function(response){
                        var text = response.responseText;
                        store.load();
                    },
                    failure: function(result)
                    {
                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                    }
                });
            }
        });
       }
       else
       {
        alert('Debe seleccionar un motivo para poder rechazar los contratos.');                
       } 
      }
      else
      {
        alert('Por lo menos uno de las registro se encuentra en estado ELIMINADO');
      }
    }
    else
    {
      alert('Seleccione por lo menos un registro de la lista');
    }
}

function limpiar(){
    Ext.getCmp('fechaDesde').setRawValue("");
    Ext.getCmp('fechaHasta').setRawValue("");      
    Ext.getCmp('nombre').setValue('');
    Ext.getCmp('idOficina').setRawValue("");      
}
function verDocumentos(url_showDocumentosContrato){    
    var store = new Ext.data.Store({ 
           id:'verDocumentosDigitalesStore',
           total: 'total',
           pageSize: 10,
           autoLoad: true,
           proxy: {
               type: 'ajax',                
               url: url_showDocumentosContrato,               
               reader: {
                   type: 'json', 
                   totalProperty: 'total', 
                   root: 'logs'
               }
           },
           fields:
                 [
                   {name:'id', mapping:'id'},                                      
                   {name:'ubicacionLogicaDocumento', mapping:'ubicacionLogicaDocumento'},
                   {name:'tipoDocumentoGeneral', mapping:'tipoDocumentoGeneral'},
                   {name:'feCreacion', mapping:'feCreacion'},
                   {name:'usrCreacion', mapping:'usrCreacion'},
                   {name:'linkVerDocumento', mapping: 'linkVerDocumento'}
                 ]
        });
                
        var gridDocumentosDigitalesContrato = Ext.create('Ext.grid.Panel', {
            id: 'gridDocumentosDigitalesContrato',
            store: store,
            timeout: 60000,
            dockedItems: [ {
                    xtype: 'toolbar',
                    dock: 'top',
                    align: '->',
                    items: [
                        { xtype: 'tbfill' }
                    ]}
            ],                  
            columns:[
                    {
                      id: 'id',
                      header: 'id',
                      dataIndex: 'id',
                      hidden: true,
                      hideable: false
                    },
                    {
                      header: 'Archivo Digital',
                      dataIndex: 'ubicacionLogicaDocumento',
                      width: 300
                    },
                    {
                      header: 'Tipo Documento',
                      dataIndex: 'tipoDocumentoGeneral',
                      width: 150
                    },                  
                    {
                      header: 'Fecha de Creacion',
                      dataIndex: 'feCreacion',
                      width: 160,
                      sortable: true
                    },
                    {
                      header: 'Creado por',
                      dataIndex: 'usrCreacion',
                      width: 80,
                      sortable: true
                    },
                    {
		      text: 'Acciones',
		      width: 80,
		      renderer: renderAcciones,
		    }
                ],
                bbar: Ext.create('Ext.PagingToolbar', {
                    store: store,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar." 
                })
        });
        function renderAcciones(value, p, record) {
                    var iconos='';
                    iconos=iconos+'<b><a href="'+record.data.linkVerDocumento+'" onClick="" title="Ver Archivo Digital" class="button-grid-show"></a></b>';	                                       
                    return Ext.String.format(
                                    iconos,
                        value,
                        '1',
                                    'nada'
                    );
        }
        var pop = Ext.create('Ext.window.Window', {
            title: 'Archivos Digitales',
            height: 400,
            width: 800,
            modal: true,
            layout:{
                type:'fit',
                align:'stretch',
                pack:'start'
            },
            floating: true,
            shadow: true,
            shadowOffset:20,
            items: [gridDocumentosDigitalesContrato] 
        });
        
        
        pop.show();
}

var winDocEntregable;

function verEntregables(url_showDocumentosEntregables, strNumeroContrato)
{
    var dataStoreEntregables = new Ext.data.Store({
        id: 'verDocumentosEntregablesStore',
        total: 'total',
        pageSize: 50,
        autoLoad: true,
        proxy:
            {
                type: 'ajax',
                url: url_showDocumentosEntregables,
                reader:
                    {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'entregables'
                    }
            },
        fields:
            [
                {name: 'idContrato',    type: 'int'},
                {name: 'codEntregable', type: 'string'},
                {name: 'desEntregable', type: 'string'},
                {name: 'valEntregable', type: 'bool'}
            ]
    }); 

    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing',
        {
            clicksToEdit: 1
        });

    var gridDocumentosEntregables = Ext.create('Ext.grid.Panel',
        {
            id: 'gridDocumentosEntregables',
            store: dataStoreEntregables,
            timeout: 60000,
            dockedItems:
                [{
                        xtype: 'toolbar',
                        dock: 'top',
                        align: '->',
                        items: [{xtype: 'tbfill'}]
                    }],
            viewConfig: {stripeRows: true},
            columnLines: true,
            buttons:
                [
                    {
                        text: 'Guardar',
                        iconCls: "iconSave",
                        handler: function()
                        {
                            var myEntregables = [];
                            dataStoreEntregables.each(function(record)
                            {
                                myEntregables.push(record.data);

                            }, this);
                            connGuardandoDatos.request
                                (
                                    {
                                        url: urlGuardarDocumentoEntregable,
                                        method: 'post',
                                        dataType: 'json',
                                        params:
                                            {
                                                jsonEntregables: Ext.encode(myEntregables)
                                            },
                                        success: function(response)
                                        {
                                            var msg = Ext.decode(response.responseText);
                                            if (msg.ESTADO === 'OK')
                                            {
                                                Ext.Msg.alert('Informaci\xf3n', 'Documentos entregables actualizados correctamente.', function()
                                                {
                                                    winDocEntregable.close();
                                                    store.load();
                                                });
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('Error', msg.ERROR, function()
                                                {
                                                    winDocEntregable.show();
                                                });
                                            }

                                        },
                                        failure: function(result)
                                        {
                                            Ext.Msg.alert('Error', result.responseText, function()
                                            {
                                                winDocEntregable.show();
                                            });
                                        }
                                    }
                                );
                        }
                    },
                    {
                        text: 'Cancelar',
                        iconCls: "icon_cerrar",
                        handler: function()
                        {
                            winDocEntregable.close();
                        }
                    }
                ],
            buttonAlign: 'center',
            plugins: [cellEditing],
            columns: [
                {
                    id: 'idContrato',
                    header: 'idContrato',
                    dataIndex: 'idContrato',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'codEntregable',
                    header: 'codEntregable',
                    dataIndex: 'codEntregable',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'desEntregable',
                    header: 'Documento',
                    dataIndex: 'desEntregable',
                    sortable: false,
                    width: 320
                },
                {
                    xtype: 'checkcolumn',
                    header: 'Entregó?',
                    dataIndex: 'valEntregable',
                    width: 60,
                    editor:
                        {
                            xtype: 'checkbox',
                            cls: 'x-grid-checkheader-editor'
                        },
                    stopSelection: false
                }
            ]
        });

    winDocEntregable = Ext.create('Ext.window.Window',
        {
            title: 'Contrato ' + strNumeroContrato + ' - Documentos Entregables',
            height: 200,
            width: 400,
            modal: true,
            layout:
                {
                    type: 'fit',
                    align: 'stretch',
                    pack: 'start'
                },
            floating: true,
            shadow: true,
            shadowOffset: 20,
            items: [gridDocumentosEntregables]
        });

    winDocEntregable.show();
}

var connGuardandoDatos = new Ext.data.Connection(
    {
        listeners:
            {
                'beforerequest':
                    {
                        fn: function(con, opt)
                        {
                            winDocEntregable.hide();
                            Ext.MessageBox.show(
                                {
                                    msg: 'Grabando los datos, Por favor espere!!',
                                    progressText: 'Grabando...',
                                    width: 300,
                                    wait: true,
                                    waitConfig: {interval: 0}
                                });
                        },
                        scope: this
                    },
                'requestcomplete':
                    {
                        fn: function()
                        {
                            Ext.MessageBox.hide();

                        },
                        scope: this
                    },
                'requestexception':
                    {
                        fn: function()
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    }
            }
    });