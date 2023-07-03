/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//alert("hola");

var dataCasos;

Ext.QuickTips.init();
Ext.onReady(function() {    
    Ext.tip.QuickTipManager.init();

    if ((strPrefijoEmpresaSession === 'TN') && boolPuedeVerGridActividades) {
        storeActividadesPuntoAfectado = new Ext.data.Store({
            pageSize: 4,
            autoLoad: true,
            total: 'total',
            proxy: {
                type: 'ajax',
                url: url_getActividadesPuntoAfectado,
                timeout: 60000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'actividades'
                }
            },
            fields:
                [
                    {name: 'idActividad', mapping: 'idActividad'},
                    {name: 'titulo', mapping: 'titulo'},
                    {name: 'motivo', mapping: 'motivo'},
                    {name: 'responsable', mapping: 'responsable'},
                    {name: 'areaResponsable', mapping: 'areaResponsable'},
                    {name: 'contacto', mapping: 'contacto'},
                    {name: 'notificado', mapping: 'notificado'},
                    {name: 'fechaNotificacion', mapping: 'fechaNotificacion'},
                    {name: 'asuntoNotificacion', mapping: 'asuntoNotificacion'},
                    {name: 'tipoAfectacion', mapping: 'tipoAfectacion'},
                    {name: 'serviciosAfectados', mapping: 'serviciosAfectados'},
                    {name: 'origen', mapping: 'origen'},
                    {name: 'fechaInicio', mapping: 'fechaInicio'},
                    {name: 'fechaFin', mapping: 'fechaFin'},
                    {name: 'estado', mapping: 'estado'},
                    {name: 'idCasoTarea', mapping: 'idCasoTarea'},
                    {name: 'tipoCasoTarea', mapping: 'tipoCasoTarea'}
                ]
        });
    
        gridActividadesPuntoAfectado = Ext.create('Ext.grid.Panel', {
            store: storeActividadesPuntoAfectado,
            collapsible: true,
            collapsed: false,
            width: 1000,
            height: 175,
            viewConfig: { enableTextSelection: true },
            title: 'Trabajos realizados recientemente', 
            columnLines: true,  
            columns: [
                {
                    header: 'Origen',
                    dataIndex: 'origen',
                    width: 90
                },
                {
				    header: 'idCasoTarea',
				    dataIndex: 'idCasoTarea',
				    hidden: true,
				    hideable: false
                },
                {
				    header: 'tipoCasoTarea',
				    dataIndex: 'tipoCasoTarea',
				    hidden: true,
				    hideable: false
                },
                {
                    header: 'Id Actividad',
                    dataIndex: 'idActividad',
                    width: 100,
                    renderer : function(value, p,record){
                        var idCasoTarea = record.data.idCasoTarea;
                        var idActividad = record.data.idActividad;
                        var tipoCasoTarea = record.data.tipoCasoTarea;
                        return '<a href="#" onclick="verInformacionActividad(\''+idCasoTarea+'\',\''+tipoCasoTarea+'\');">'+idActividad+'</a>';
                    } 
                },
                {
                    header: 'Titulo',
                    dataIndex: 'titulo',
                    width: 150
                },
                {
                    header: 'Motivo',
                    dataIndex: 'motivo',
                    width: 205
                },
                {
                    header: 'Fecha Inicio',
                    dataIndex: 'fechaInicio',
                    width: 120
                },
                {
                    header: 'Fecha Fin',
                    dataIndex: 'fechaFin',
                    width: 120
                },
                {
                    header: 'Estado',
                    dataIndex: 'estado',
                    width: 70
                },
                {
                    header: 'Responsable',
                    dataIndex: 'responsable',
                    width: 200
                },
                {
                    header: 'Area Respon.',
                    dataIndex: 'areaResponsable',
                    width: 90
                },
                {
                    header: 'Tipo Afectacion',
                    dataIndex: 'tipoAfectacion',
                    width: 100
                },
                {
                    header: 'Servicios',
                    dataIndex: 'serviciosAfectados',
                    width: 90
                },
                {
                    header: 'Contacto',
                    dataIndex: 'contacto',
                    width: 230
                },
                {
                    header: 'Notificado',
                    dataIndex: 'notificado',
                    width: 60
                },
                {
                    header: 'Fecha Notificacion',
                    dataIndex: 'fechaNotificacion',
                    width: 120
                },
                {
                    header: 'Asunto Notificacion',
                    dataIndex: 'asuntoNotificacion',
                    width: 300
                }],
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeActividadesPuntoAfectado,
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
            renderTo: 'actividadesPuntoAfectado'
        });
    }
    
    //Actualizar el indicador de tareas
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
    
    let autoloadStore = boolClienteSesion?true:false;
    if (strTipoConsulta !== "")
    {
        autoloadStore = true;
    }

	// **************** CASOS ******************
	store = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url: url_grid,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: '',
                strOrigen: strOrigen,
                strTipoConsulta: strTipoConsulta
			}
		},
		fields:
		[
                {name: 'tiempoCliente'    , mapping: 'tiempoCliente'},
                {name: 'tiempoEmpresa'    , mapping: 'tiempoEmpresa'},
                {name: 'tiempoIncidencia' , mapping: 'tiempoIncidencia'},
                {name: 'tiempoTotalCierre', mapping: 'tiempoTotalCierre'},
                {name: 'id_caso', mapping: 'id_caso'},
                {name: 'numero_caso', mapping: 'numero_caso'},
                {name: 'titulo_ini', mapping: 'titulo_ini'},
                {name: 'titulo_fin', mapping: 'titulo_fin'},
                {name: 'version_ini', mapping: 'version_ini'},
                {name: 'version_fin', mapping: 'version_fin'},
                {name: 'departamento_asignado', mapping: 'departamento_asignado'},
                {name: 'empleado_asignado', mapping: 'empleado_asignado'},
                {name: 'oficina_asignada', mapping: 'oficina_asignada'},
                {name: 'empresa_asignada', mapping: 'empresa_asignada'},
                {name: 'asignado_por', mapping: 'asignado_por'},
                {name: 'fecha_asignacionCaso', mapping: 'fecha_asignacionCaso'},
                {name: 'fecha_apertura', mapping: 'fecha_apertura'},
                {name: 'hora_apertura', mapping: 'hora_apertura'},
                {name: 'fecha_cierre', mapping: 'fecha_cierre'},
                {name: 'hora_cierre', mapping: 'hora_cierre'},
                {name: 'estado', mapping: 'estado'},
                {name: 'usuarioApertura', mapping: 'usuarioApertura'},
                {name: 'usuarioCierre', mapping: 'usuarioCierre'},
                {name: 'tiempo_total', mapping: 'tiempo_total'},
                {name: 'empresa', mapping: 'empresa'},
                {name: 'edicionReporteEjecutivo', mapping: 'edicionReporteEjecutivo'},
                {name: 'fechaFin', mapping: 'fechaFin'},
                {name: 'horaFin', mapping: 'horaFin'},
                {name: 'action1', mapping: 'action1'},
                {name: 'action2', mapping: 'action2'},
                {name: 'action3', mapping: 'action3'},
                {name: 'action4', mapping: 'action4'},
                {name: 'action5', mapping: 'action5'},
                {name: 'action6', mapping: 'action6'},
                {name: 'action7', mapping: 'action7'},
                {name: 'action8', mapping: 'action8'},
                {name: 'action9', mapping: 'action9'},
                {name: 'action10', mapping: 'action10'},
                {name: 'action11', mapping: 'action11'},
                {name: 'action12', mapping: 'action12'},
                {name: 'action13', mapping: 'action13'},
                {name: 'idPunto', mapping: 'idPunto'},
                {name: 'flag1', mapping: 'flag1'},
                {name: 'flag2', mapping: 'flag2'},
                {name: 'flag3', mapping: 'flag3'},
                {name: 'flagCreador', mapping: 'flagCreador'},
                {name: 'flagBoolAsignado', mapping: 'flagBoolAsignado'},
                {name: 'flagAsignado', mapping: 'flagAsignado'},
                {name: 'flagTareasTodas', mapping: 'flagTareasTodas'},
                {name: 'flagTareasTodasCanceladas', mapping: 'flagTareasTodasCanceladas'},
                {name: 'flagTareasAbiertas', mapping: 'flagTareasAbiertas'},
                {name: 'flagTareasSolucionadas', mapping: 'flagTareasSolucionadas'},
                {name: 'siTareasAbiertas', mapping: 'siTareasAbiertas'},
                {name: 'siTareasSolucionadas', mapping: 'siTareasSolucionadas'},
                {name: 'siTareasTodas', mapping: 'siTareasTodas'},
                {name: 'esDepartamento', mapping: 'esDepartamento'},
                {name: 'elementoAfectado', mapping: 'elementoAfectado'},
                {name: 'hipotesisIniciales', mapping: 'hipotesisIniciales'},
                {name: 'cantidadDocumentos', mapping: 'cantidadDocumentos'},
                {name: 'date', mapping: 'date'},
                {name: 'tipoCaso', mapping: 'tipoCaso'},
                {name: 'tipo_afectacion', mapping: 'tipo_afectacion'},
                {name: 'nuevo_esquema', mapping: 'nuevo_esquema'},
                {name: 'tiempo_total_caso', mapping: 'tiempo_total_caso'},
                {name: 'estadoInforme', mapping: 'estadoInforme'}
		],
		autoLoad: autoloadStore
	});
   
    var pluginExpanded = true;
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })
    
    
    grid = Ext.create('Ext.grid.Panel', {
        width: 1000,
        height: 1000,
        store: store,
	viewConfig: { enableTextSelection: true },  
        loadMask: true,
        frame: false,
        dockedItems: 
		[ 
			{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: 
				[                    
                    { xtype: 'tbfill' },
                    {
                        iconCls: 'icon_exportar',
                        text: 'Exportar',
                        scope: this,
                        handler: function(){ exportarExcel(Ext.getCmp('comboEmpleados1').value);}
                    }
                ]
			}
        ],                  
        columns:
		[		
			{
			  id: 'id_caso',
			  header: 'IdCaso',
			  dataIndex: 'id_caso',
			  hidden: true,
			  hideable: false
			},	
			{
			  id: 'flag1',
			  header: 'flag1',
			  dataIndex: 'flag1',
			  hidden: true,
			  hideable: false
			},	
			{
			  id: 'flag2',
			  header: 'flag2',
			  dataIndex: 'flag2',
			  hidden: true,
			  hideable: false
			},	
			{
			  id: 'flag3',
			  header: 'flag3',
			  dataIndex: 'flag3',
			  hidden: true,
			  hideable: false
			},	
			{
			  id: 'flagCreador',
			  header: 'flagCreador',
			  dataIndex: 'flagCreador',
			  hidden: true,
			  hideable: false
			},	
			{
			  id: 'flagBoolAsignado',
			  header: 'flagBoolAsignado',
			  dataIndex: 'flagBoolAsignado',
			  hidden: true,
			  hideable: false
			},	
			{
			  id: 'flagAsignado',
			  header: 'flagAsignado',
			  dataIndex: 'flagAsignado',
			  hidden: true,
			  hideable: false
			},	
			{
			  id: 'flagTareasTodas',
			  header: 'flagTareasTodas',
			  dataIndex: 'flagTareasTodas',
			  hidden: true,
			  hideable: false
			},	
			{
			  id: 'flagTareasAbiertas',
			  header: 'flagTareasAbiertas',
			  dataIndex: 'flagTareasAbiertas',
			  hidden: true,
			  hideable: false
			},	
			{
			  id: 'flagTareasSolucionadas',
			  header: 'flagTareasSolucionadas',
			  dataIndex: 'flagTareasSolucionadas',
			  hidden: true,
			  hideable: false
			},
			{
			  id: 'numero_caso',
			  header: 'Numero Caso',
			  dataIndex: 'numero_caso',
			  width: 100,
			  sortable: true
			},
			{
			  id: 'caso',
			  header: 'Caso',
			  xtype: 'templatecolumn', 
			  width: 340,
			  tpl: '<tpl if="version_fin==\'N/A\'">\n\
						  <span class="bold">Titulo Inicial:</span></br>\n\
						  <span class="box-detalle">{titulo_ini}</span>\n\
						  <span class="bold">Version Inicial:</span></br>\n\
						  <span>{version_ini}</span></br>\n\
					</tpl>\n\
					<tpl if="version_fin!=\'N/A\'">\n\
						  <span class="bold">Titulo Final:</span></br>\n\
						  <span class="box-detalle">{titulo_fin}</span>\n\
						  <span class="bold">Version Final:</span></br>\n\
						  <span>{version_fin}</span></br></br>\n\
					</tpl></br>\n\\n\\n\
                    <span class="bold">Tipo Caso:</span> {tipoCaso}</br></br>\n\\n\
					<span class="bold">Empresa Asignada:</span> {empresa_asignada}</br>\n\\n\
					<span class="bold">Oficina Asignada:</span> {oficina_asignada}</br>\n\\n\
					<span class="bold">Departamento Asignado:</span> {departamento_asignado}</br>\n\\n\
					<span class="bold">Asignado Por:</span> {asignado_por}</br>\n\\n\
					<span class="bold">Empleado Asignado:</span> {empleado_asignado}</br>\n\\n\
					<span class="bold">Fecha Asignacion:</span> {fecha_asignacionCaso}</br></br>\n\\n\
					<span class="bold">Usuario Apertura:</span> {usuarioApertura}</br>\n\\n\
					<span class="bold">Usuario Cierre:</span> {usuarioCierre}</br></br>\n\\n\
					<span class="bold">Tareas Creadas:</span> {siTareasTodas}</br>\n\\n\
					<span class="bold">Tareas Abiertas:</span> {siTareasAbiertas}</br>\n\\n\
					<span class="bold">Tareas Solucionadas:</span> {siTareasSolucionadas}</br></br>\n\\n\
					<tpl if="estado==\'Cerrado\'">\n\
                                                <span class="bold">Tiempo Total Caso:</span> {tiempoTotalCierre}</br>\n\\n\
						<span class="bold">Tiempo Total Incidencia:</span> {tiempoIncidencia}</br>\n\\n\
						<span class="bold">Tiempo Total Empresa:</span> {tiempoEmpresa}</br>\n\\n\
						<span class="bold">Tiempo Total Cliente:</span> {tiempoCliente}\n\\n\
					</tpl>'
			},
			{
			  header: 'Fecha Apertura',
			  xtype: 'templatecolumn', 
			  align: 'center',
			  tpl: '<span class="center">{fecha_apertura}</br>{hora_apertura}</span>',
			  width: 100
			},
			{
			  header: 'Fecha Cierre',
			  xtype: 'templatecolumn', 
			  align: 'center',
			  tpl: '<span class="center">{fecha_cierre}</br>{hora_cierre}</span>',
			  width: 100
			},
			{
			  header: 'Estado',
			  dataIndex: 'estado',
			  width: 60,
			  sortable: true
			},
			{
				xtype: 'actioncolumn',
				header: 'Acciones',
				width: 280,
				items: 
				[				
					{
						getClass: function(v, meta, rec) {
							var permiso = '{{ is_granted("ROLE_78-6") }}';
							var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
							if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
							
							if (rec.get('action1') == "icon-invisible") 
								this.items[0].tooltip = '';
							else 
								this.items[0].tooltip = 'Ver Caso';
								
							return rec.get('action1')
						},
						tooltip: 'Ver Caso',
						handler: function(grid, rowIndex, colIndex) {
							var rec = store.getAt(rowIndex);
							
							var permiso = '{{ is_granted("ROLE_78-6") }}';
							var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
							if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
															
							if(rec.get('action1')!="icon-invisible")
								window.location = rec.get('id_caso')+"/show";
							else
								Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
								
						}
					},
					{
						getClass: function(v, meta, rec) 
						{
							var permiso1 = '{{ is_granted("ROLE_78-39") }}';	
							var permiso2 = '{{ is_granted("ROLE_78-42") }}';
							var boolPermiso1 = (typeof permiso1 === 'undefined') ? false : (permiso1 ? true : false);
							var boolPermiso2 = (typeof permiso2 === 'undefined') ? false : (permiso2 ? true : false);						
							if(!boolPermiso1 || !boolPermiso2){ rec.data.action8 = "icon-invisible"; }
							
							if (rec.get('action8') == "icon-invisible") 
								this.items[1].tooltip = '';
							else 
								this.items[1].tooltip = 'Ver Afectados';
							
							return rec.get('action8');
						},
						handler: function(grid, rowIndex, colIndex) 
						{
							var rec = store.getAt(rowIndex);
							
							var permiso1 = '{{ is_granted("ROLE_78-39") }}';	
							var permiso2 = '{{ is_granted("ROLE_78-42") }}';
							var boolPermiso1 = (typeof permiso1 === 'undefined') ? false : (permiso1 ? true : false);
							var boolPermiso2 = (typeof permiso2 === 'undefined') ? false : (permiso2 ? true : false);						
							if(!boolPermiso1 || !boolPermiso2){ rec.data.action8 = "icon-invisible"; }
							
							if(rec.get('action8')!="icon-invisible")
								verAfectados(rec);
							else
								Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
						}
					},
                    //Agregar Afectados al Caso--------------------------------------------------------------------
                    {
						getClass: function(v, meta, rec) 
						{
							var permiso1 = '{{ is_granted("ROLE_78-39") }}';	
							var permiso2 = '{{ is_granted("ROLE_78-42") }}';
							var boolPermiso1 = (typeof permiso1 === 'undefined') ? false : (permiso1 ? true : false);
							var boolPermiso2 = (typeof permiso2 === 'undefined') ? false : (permiso2 ? true : false);						
							if(!boolPermiso1 || !boolPermiso2 || rec.get('empresa') === 'MD' || rec.get('empresa') === 'EN'  || rec.get('tipoCaso') !== 'Backbone' )
                            { 
                                rec.data.action12 = "icon-invisible"; 
                            }                                                        
							
							if (rec.get('action12') === "icon-invisible")
                            {
								this.items[2].tooltip = '';
                            }
							else 
                            {
								this.items[2].tooltip = 'Agregar Afectados';
                            }
							
							return rec.get('action12');
						},
						handler: function(grid, rowIndex, colIndex) 
						{
							var rec = store.getAt(rowIndex);                            
                            agregarAfectadosCaso(rec.raw.id_caso,"index");                            
						}
					},
                    //Fin Agregar Afectados al Caso--------------------------------------------------------------------
					{
						getClass: function(v, meta, rec) 
                        {
							var permiso = '{{ is_granted("ROLE_78-32") }}';
							var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
							if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
							
							if (rec.get('action2') === "icon-invisible") 
                            {
								this.items[3].tooltip = '';
                            }
							else 
                            {
								this.items[3].tooltip = 'Ingresar Sintomas';
                            }
								
							return rec.get('action2');
						},
						handler: function(grid, rowIndex, colIndex) 
						{
							var rec = store.getAt(rowIndex);														
                            agregarSintoma(rec.raw);							
						}
					},
					{
						getClass: function(v, meta, rec)
						{
							var permiso = '{{ is_granted("ROLE_78-31") }}';
							var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
							if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
							
							if (rec.get('action3') == "icon-invisible") 
								this.items[4].tooltip = '';
							else 
								this.items[4].tooltip = 'Ingresar Hipotesis';
							
							return rec.get('action3');
						},
						handler: function(grid, rowIndex, colIndex) 
						{
							var rec = store.getAt(rowIndex);
							
							var permiso = '{{ is_granted("ROLE_78-31") }}';
							var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
							if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
															
							if(rec.get('action3')!="icon-invisible")
								agregarHipotesis(rec);
							else
								Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
						}
					},
					{
						getClass: function(v, meta, rec)
						{
							var permiso = '{{ is_granted("ROLE_78-33") }}';
							var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
							if(!boolPermiso){ rec.data.action4 = "icon-invisible"; }
							
							if (rec.get('action4') == "icon-invisible") 
								this.items[5].tooltip = '';
							else 
								this.items[5].tooltip = 'Ingresar Tarea';
							
							return rec.get('action4');
						},
						handler: function(grid, rowIndex, colIndex)
						{
							var rec = store.getAt(rowIndex);
							
							var permiso = '{{ is_granted("ROLE_78-33") }}';
							var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
							if(!boolPermiso){ rec.data.action4 = "icon-invisible"; }
							
							if(rec.get('action4')!="icon-invisible")
								agregarTarea(rec);
							else
								Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
						}
					},
					{
						getClass: function(v, meta, rec) 
						{
							var permiso = '{{ is_granted("ROLE_78-51") }}';
							var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
							if(!boolPermiso){ rec.data.action5 = "icon-invisible"; }
							
							if (rec.get('action5') == "icon-invisible") 
								this.items[6].tooltip = '';
							else 
								this.items[6].tooltip = 'Administrar Tareas';
							
							return rec.get('action5');
						},
						handler: function(grid, rowIndex, colIndex) 
						{
							var rec = store.getAt(rowIndex);
							
							var permiso = '{{ is_granted("ROLE_78-51") }}';
							var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
							if(!boolPermiso){ rec.data.action5 = "icon-invisible"; }
							
							if(rec.get('action5')!="icon-invisible")
								administrarTareas(rec);
							else
								Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
						}
					},                                      
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var permiso = '{{ is_granted("ROLE_78-6") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
                            if(!boolPermiso){ rec.data.action11 = "icon-invisible"; }

                            if (rec.get('action11') == "icon-invisible") 
                                this.items[7].tooltip = '';
                            else 
                                this.items[7].tooltip = 'Ver seguimientos de Tareas';

                            return rec.get('action11');
                        },
                        tooltip: 'Ver seguimientos de Tareas',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);

                            var permiso = '{{ is_granted("ROLE_78-6") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                            if(!boolPermiso){ rec.data.action11 = "icon-invisible"; }

                            if(rec.get('action11')!="icon-invisible")
                                verSeguimientoTareasXCaso(rec.data.id_caso);
                            else
                                Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');

                        }
                    },
					{
                        getClass: function(v, meta, rec)
                        {
                            var permiso = '{{ is_granted("ROLE_78-33") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                            if(!boolPermiso){ rec.data.action9 = "icon-invisible"; }

                            if (rec.get('action9') == "icon-invisible")
                                this.items[8].tooltip = '';
                            else
                                this.items[8].tooltip = 'Cargar Archivo';

                            return rec.get('action9')
                        },
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var rec = store.getAt(rowIndex);

                            var permiso = '{{ is_granted("ROLE_78-33") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                            if(!boolPermiso){ rec.data.action9 = "icon-invisible"; }

                            if(rec.get('action9')!="icon-invisible")
                                subirMultipleAdjuntosCaso(rec);
                            else
                                Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
                        }
					},
					{
                        getClass: function(v, meta, rec)
                        {
                            var permiso = '{{ is_granted("ROLE_78-33") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                            if(!boolPermiso){ rec.data.action10 = "icon-invisible"; }

                            if (rec.get('action10') == "icon-invisible")
                                this.items[9].tooltip = '';
                            else
                                this.items[9].tooltip = 'Ver Archivos';

                            return rec.get('action10')
                        },
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var rec = store.getAt(rowIndex);

                            var permiso = '{{ is_granted("ROLE_78-33") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                            if(!boolPermiso){ rec.data.action10 = "icon-invisible"; }

                            if(rec.get('action10')!="icon-invisible")
                                presentarDocumentosCasos(rec);
                            else
                                Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
                        }
					},
                            {
                                getClass: function(v, meta, rec)
                                {
                                    var permiso = '{{ is_granted("ROLE_78-36") }}';
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                                    if (!boolPermiso) {
                                        rec.data.action7 = "icon-invisible";
                                    }

                                    if (rec.get('action7') == "icon-invisible")
                                        this.items[10].tooltip = '';
                                    else
                                        this.items[10].tooltip = 'Cerrar Caso';

                                    return rec.get('action7');
                                },
                                handler: function(grid, rowIndex, colIndex)
                                {
                                    var rec = store.getAt(rowIndex);

                                    dataCasos = rec; //Variable global con informacion de casos

                                    var permiso = '{{ is_granted("ROLE_78-36") }}';
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                                    
                                    if (!boolPermiso) 
                                    {
                                        rec.data.action7 = "icon-invisible";
                                    }

                                    if (rec.get('action7') !== "icon-invisible")
                                    {
                                        var tieneTareas = false;
                                        if(rec.get('flagTareasTodas')==1 || rec.get('flagTareasTodas')=="Si" || rec.get('flagTareasTodas'))
                                        {
                                            tieneTareas = true;
                                        }

                                        var irFlujoTN=0;
                                        if(rec.get('empresa')=="TN")
                                        {
                                            if(tieneTareas)
                                            {
                                                //Si todas las tareas fueron canceladas ir por el flujo de MD
                                                if(!rec.get('flagTareasTodasCanceladas'))
                                                {
                                                    irFlujoTN=1;
                                                }
                                            }
                                        }
                                        
                                        if(irFlujoTN==1)
                                        {
                                            var data = {
                                                           id_caso          : rec.get('id_caso'),
                                                           numero_caso      : rec.get('numero_caso'),
                                                           fecha_apertura   : rec.get('fecha_apertura'),
                                                           hora_apertura    : rec.get('hora_apertura'),
                                                           tiene_tareas     : rec.get('flagTareasTodas'),
                                                           hora_final       : rec.get('horaFin'),
                                                           fecha_final      : rec.get('fechaFin'),
                                                           hipotesisIniciales: rec.get('hipotesisIniciales'),
                                                           afectacion       : rec.get('tipo_afectacion'),
                                                           boolPermisoVerSeguimientosCerrarCaso: boolPermisoVerSeguimientosCerrarCaso,
                                                           nuevoEsquema    : rec.get('nuevo_esquema'),
                                                           tiempoTotalCaso : rec.get('tiempo_total_caso')
                                                       };

                                            cerrarCasoTN(data);
                                        }
                                        else
                                        {
                                            var conn = new Ext.data.Connection({
                                                listeners: {
                                                    'beforerequest': {
                                                        fn: function(con, opt) {
                                                            Ext.get(document.body).mask('Obteniendo Fecha y Hora...');
                                                        },
                                                        scope: this
                                                    },
                                                    'requestcomplete': {
                                                        fn: function(con, res, opt) {
                                                            Ext.get(document.body).unmask();
                                                        },
                                                        scope: this
                                                    },
                                                    'requestexception': {
                                                        fn: function(con, res, opt) {
                                                            Ext.get(document.body).unmask();
                                                        },
                                                        scope: this
                                                    }
                                                }
                                            });
                                            conn.request({
                                               method: 'POST',
                                               url: url_obtenerFechaServer,
                                               success: function(response)
                                               {
                                                   var json = Ext.JSON.decode(response.responseText);

                                                if (json.success)
                                                {                                                                            
                                                    var data = {
                                                        id_caso          : rec.get('id_caso'),
                                                        numero_caso      : rec.get('numero_caso'),
                                                        fecha_apertura   : rec.get('fecha_apertura'),
                                                        hora_apertura    : rec.get('hora_apertura'),
                                                        tiene_tareas     : rec.get('flagTareasTodas'),
                                                        hora_final       : rec.get('horaFin'),
                                                        fecha_final      : rec.get('fechaFin'),
                                                        hipotesisIniciales: rec.get('hipotesisIniciales'),
                                                        fechaActual      : json.fechaActual,
                                                        horaActual       : json.horaActual,
                                                        afectacion       : rec.get('tipo_afectacion'),
                                                        nuevoEsquema     : rec.get('nuevo_esquema'),
                                                        tiempoTotalCaso  : rec.get('tiempo_total_caso')
                                                    };
                                                    
                                                    cerrarCaso(data);
                                                }
                                                else
                                                {
                                                    Ext.Msg.alert('Alerta ', json.error);
                                                }
                                            }
                                        });                                                                                                                        

                                    }
                                }
                                    else
                                    {
                                        Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                                    }
                                }
                            },                            
                            {
                                    getClass: function(v, meta, rec) 
                                    {
                                            var button   = '';
                                            var permiso = $("#ROLE_57-5837");
                                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);  
					
                                            if(rec.get('estado') != 'Cerrado' || rec.get('estadoInforme') != '' || !boolPermiso 
                                                || rec.get('empresa') == "MD" || rec.get('empresa') == "EN" )
                                            { 
                                                button = "icon-invisible"; 
                                                this.items[11].tooltip = '';
                                            }
                                            else
                                            {
                                                button= "button-grid-renovarContrato"; 
                                                this.items[11].tooltip = 'Solicitar Informe Ejecutivo';
                                            }

                                            return button;
                                    },
                                    handler: function(grid, rowIndex, colIndex) 
                                    {
                                            var rec = store.getAt(rowIndex);
                                            
                                            solicitarInformeEjecutivo(rec.get('id_caso'));

                                    }
                            },
                            {
                                    getClass: function(v, meta, rec) 
                                    {

                                            var permiso = $("#ROLE_57-5838");
                                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
					
                                            if(rec.get('estadoInforme') != '' && rec.get('estadoInforme') != 'Finalizado'
                                                && rec.get('empresa') == "TN"  && ( boolPermiso || rec.get('edicionReporteEjecutivo') == "S"))
                                            {
                                                button = "button-grid-editarCaracteristicas"; 
                                                this.items[12].tooltip = 'Editar Informe Ejecutivo';                                                
                                            }
                                            else
                                            {
                                                button = "icon-invisible"; 
                                                this.items[12].tooltip = '';
                                            }

                                            return button;
                                    },
                                    handler: function(grid, rowIndex, colIndex) 
                                    {
                                            var rec = store.getAt(rowIndex);
                                            getInformeEjecutivo(rec);

                                    }
                            },
                            {
                            getClass: function(v, meta, rec) 
                            {

                                        var permiso = $("#ROLE_57-5838");
                                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);  

                                        if(rec.get('estadoInforme') == 'Finalizado' && rec.get('empresa') == "TN")
                                        { 
                                            button = "button-grid-pdf-2"; 
                                            this.items[13].tooltip = 'Descargar Informe Ejecutivo';                                                
                                        }
                                        else
                                        {
                                            button = "icon-invisible"; 
                                            this.items[13].tooltip = '';
                                        }

                                        return button;
                                },
                                handler: function(grid, rowIndex, colIndex) 
                                {
                                        var rec = store.getAt(rowIndex);
                                        generarPDF(rec.get('id_caso'));

                                }
                            },
                            //--------- Se adiciona el nuevo boton para agregar afectado cliente servicio
                            {
                                getClass: function(v, meta, rec) {
                                    var permiso = '{{ is_granted("ROLE_78-33") }}';
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                                    if(!boolPermiso){ rec.data.action13 = "icon-invisible"; }

                                    if (rec.get('action13') == "icon-invisible")
                                        this.items[14].tooltip = '';
                                    else
                                        this.items[14].tooltip = 'Afectar Servicio Cliente';

                                    return rec.get('action13');
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                idPuntoCliente = grid.getStore().getAt(rowIndex).data.idPunto;
                                afectoServicio = true;
                                if (idPuntoCliente === 'no cliente')
                                {
                                    Ext.Msg.alert("Alerta", "Para agregar servicios afectados debe tener un cliente en Sesión");
                                    return false;
                                }
                                seleccionarTipoCaso = 1;
                                tipoCaso = grid.getStore().getAt(rowIndex).data.tipoCaso;
                                casoIdAfectado = grid.getStore().getAt(rowIndex).data.id_caso;
                                agregarAfectadosXSintoma(grid.getStore().getAt(rowIndex).data.hipotesisIniciales, "PanelServicios");
                            }
                            },
                            {
                                getClass: function(v, meta, rec) {
                                    var button = 'icon-invisible';
                                    this.items[15].tooltip = '';
                                    if (rec.get('estado') !== "Cerrado" && rec.get('tipoCaso') === "Backbone" &&
                                            rec.get('empresa') === "TN") {
                                        button = "button-grid-VerCasosClientes";
                                        this.items[15].tooltip = 'Ver Casos Aperturados';
                                    }
                                    return button;
                                },
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec  = store.getAt(rowIndex);
                                    var data = {idCaso : rec.get('id_caso')};
                                    verCasosAperturados(data);
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
    
    storeNivelCriticidad = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : '../../administracion/soporte/admi_nivel_criticidad/grid',
	   // url : url_adminivelcriticidad_grid,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                    nombre: '',
                    estado: 'Activo'
                }
        },
        fields:
		[
			{name:'id_nivel_criticidad', mapping:'id_nivel_criticidad'},
			{name:'nombre_nivel_criticidad', mapping:'nombre_nivel_criticidad'}
		]
    });

    // Create the combo box, attached to the states data store
    comboNivelCriticidad = Ext.create('Ext.form.ComboBox', {
        id:'comboNivelCriticidad',
        name:'comboNivelCriticidad',
        fieldLabel: 'Nivel de Criticidad:',
        store: storeNivelCriticidad,
        displayField: 'nombre_nivel_criticidad',
        valueField: 'id_nivel_criticidad',
        height:30,
		width: 400,
        border:0,
        margin:0,
		queryMode: "remote",
		emptyText: ''
    });
    
    storeTipoCaso = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
           url : '../../administracion/soporte/admi_tipo_caso/grid',
	    //url : url_admitipocaso_grid,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                    nombre: '',
                    estado: 'Activo'
                }
        },
        
        fields:
              [
                {name:'id_tipo_caso', mapping:'id_tipo_caso'},
                {name:'nombre_tipo_caso', mapping:'nombre_tipo_caso'}
              ]
    });

    // Create the combo box, attached to the states data store
    comboTipoCaso = Ext.create('Ext.form.ComboBox', {
        id:'comboTipoCaso',
        name:'comboTipoCaso',
        fieldLabel: 'Tipo Caso:',
        store: storeTipoCaso,
        displayField: 'nombre_tipo_caso',
        valueField: 'id_tipo_caso',
        height:30,
		width: 400,
        border:0,
        margin:0,
		queryMode: "remote",
		emptyText: ''
    });
    
    storeOficina= new Ext.data.Store({ 
   total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getOficinas',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                    nombre: '',
                    estado: 'Activo'
                }
        },
        fields:
              [
                {name:'id_oficina', mapping:'id_oficina'},
                {name:'nombre_oficina', mapping:'nombre_oficina'}
              ]
     
    });
    
    // Create the combo box, este como es para la oficina
    comboOficina = Ext.create('Ext.form.ComboBox', {
        id:'comboOficina',
        name:'comboOficina',
        fieldLabel: 'Oficina Creacion:',
        store: storeOficina,
        displayField: 'nombre_oficina',
        valueField: 'id_oficina',
        height:30,
            width: 400,
        border:0,
        margin:0,
		queryMode: "remote",
		emptyText: ''
    });
    
   
    
    storeEmpleados = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getEmpleados',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                    nombre: '',
                    estado: 'Activo'
                }
        },
        fields:
              [
                {name:'login_empleado', mapping:'login_empleado'},
                {name:'nombre_empleado', mapping:'nombre_empleado'}
              ]
    });

    storeEmpleados2 = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getEmpleados',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                    nombre: '',
                    estado: 'Activo'
                }
        },
        fields:
              [
                {name:'login_empleado', mapping:'login_empleado'},
                {name:'nombre_empleado', mapping:'nombre_empleado'}
              ]
    });

    // Create the combo box, attached to the states data store
    comboEmpleados1 = Ext.create('Ext.form.ComboBox', {
        id:'comboEmpleados1',
        name:'comboEmpleados1',
        fieldLabel: 'Usuario Apertura:',
        store: storeEmpleados,
        displayField: 'nombre_empleado',
        valueField: 'login_empleado',
        height:30,
	width: 400,
        border:0,
        margin:0,
	queryMode: "remote",
	emptyText: ''
    });
    
    comboEmpleados2 = Ext.create('Ext.form.ComboBox', {
        id:'comboEmpleados2',
        name:'comboEmpleados2',
        fieldLabel: 'Usuario Cierre:',
        store: storeEmpleados2,
        displayField: 'nombre_empleado',
        valueField: 'login_empleado',
        height:30,
		width: 400,
        border:0,
        margin:0,	
		queryMode: "remote",
		emptyText: ''
    });

	comboHipotesisStore_index = new Ext.data.Store({ 
		total: 'total',
		proxy: {
			type: 'ajax',
			url: '../../administracion/soporte/admi_hipotesis/grid',
			//url: url_admihipotesis_grid,
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
			extraParams: {
				nombre: '',
				estado: 'Activo'
			}
		},
		fields:
		[
			{name:'id_hipotesis', mapping:'id_hipotesis'},
			{name:'nombre_hipotesis', mapping:'nombre_hipotesis'}
		]
	});
		
	 // Create the combo box, attached to the states data store
	comboHipotesis_index = Ext.create('Ext.form.ComboBox', {
		id:'comboHipotesis_index',
		name:'comboHipotesis_index',
		store: comboHipotesisStore_index,
		displayField: 'nombre_hipotesis',
		valueField: 'id_hipotesis',
		width: 290,
		height:30,
		border:0,
		margin:0,
		fieldLabel: 'Hipotesis:',	
		queryMode: "remote",
		emptyText: ''
	});
	
	  
  /**********************************************************************************************/
  
        storeEmpleados = new Ext.data.Store({ 
        total: 'total',
        pageSize: 1000,
        proxy: {
            type: 'ajax',
            //url: 'getEmpleadosXDepartamento',
	    //url: 'getEmpleadosAllXDepartamento',
	    url: 'getEmpleadosPorDepartamentoCiudad',
            reader: {
                type: 'json',
                totalProperty: 'result.total',
                root: 'result.encontrados',
		metaProperty: 'myMetaData'
            },
	     extraParams: {
				    nombre: ''				    
				}
        },
        fields:
		  [
			{name:'id_empleado', mapping:'id_empleado'},
			{name:'nombre_empleado', mapping:'nombre_empleado'}
		  ],
		listeners: {
			load: function(sender, node, records) {
				var myData_message = storeEmpleados.getProxy().getReader().jsonData.myMetaData.message;
				var myData_boolSuccess = storeEmpleados.getProxy().getReader().jsonData.myMetaData.boolSuccess;
				
				if(myData_boolSuccess != "1")
				{	
					Ext.Msg.alert('Alerta ', myData_message);
				}
				else
				{
					Ext.each(sender, function(record, index){
						if(storeEmpleados.getCount()<=0){
							Ext.Msg.alert('Alerta ', "No existen empleados asignados para este departamento.");
						}
					});
				}
			}
		}
    });
        
        var storeEmpresas = new Ext.data.Store({
            total: 'total',
            pageSize: 200,
            proxy: {
                type: 'ajax',
                method: 'post',
                url: url_empresaPorSistema,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    app: 'TELCOS'                    
                }                
            },
            fields:
                [
                    {name: 'opcion', mapping: 'nombre_empresa'},
                    {name: 'valor', mapping: 'prefijo'}
                ]
        });   
  
    storeCiudades = new Ext.data.Store({ 
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: 'getCiudadesPorEmpresa',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo'		
            }
        },
        fields:
              [
                {name:'id_canton', mapping:'id_canton'},
                {name:'nombre_canton', mapping:'nombre_canton'}
              ]
	});   
      
      
       storeDepartamentosCiudad = new Ext.data.Store({ 
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: 'getDepartamentosPorEmpresaYCiudad',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo'		
            }
        },
        fields:
              [
                {name:'id_departamento', mapping:'id_departamento'},
                {name:'nombre_departamento', mapping:'nombre_departamento'}
              ]
	});   
       
       
       combo_empleados = new Ext.form.ComboBox({
        id: 'comboEmpleado',
        name: 'comboEmpleado',
        fieldLabel: "Empleado Asignado",
        store: storeEmpleados,
        displayField: 'nombre_empleado',
        valueField: 'id_empleado',
	queryMode: "remote",
	emptyText: '',
	width:400,
	disabled: true
    });    
       
    /**********************************************/    
    
	
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, // Don't want content to crunch against the borders
        //bodyBorder: false,
        border: false,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 1000,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function(){ buscar();}
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function(){ 
                limpiar();
                }
            }
        ],                
		items: [
			{html:"&nbsp;",border:false,width:50},
			{
				xtype: 'textfield',
				id: 'txtNumero',
				name: 'txtNumero',
				fieldLabel: 'Numero',
				value: '',
				width: 400
			},
			{html:"&nbsp;",border:false,width:80},
			{
				xtype: 'combobox',
				fieldLabel: 'Estado',
				id: 'sltEstado',
				name: 'sltEstado',
				value:'Asignado',
				store: [					
					['Creado','Creado'],
					['Asignado','Asignado'],					
					['Cerrado','Cerrado']
				],
				width: 400
			},
			{html:"&nbsp;",border:false,width:50},
			
			{html:"&nbsp;",border:false,width:50},
			{
				xtype: 'textfield',
				id: 'txtTituloInicial',
				name: 'txtTituloInicial',
				fieldLabel: 'Titulo Inicial',
				value: '',
				width: 400
			},
			{html:"&nbsp;",border:false,width:80},
			{
				xtype: 'textfield',
				id: 'txtVersionInicial',
				name: 'txtVersionInicial',
				fieldLabel: 'Version Inicial',
				value: '',
				width: 400
			},
			{html:"&nbsp;",border:false,width:50},
			
			{html:"&nbsp;",border:false,width:50},
			{
				xtype: 'fieldcontainer',
				fieldLabel: 'Titulo Final',
				items: [
					{
						xtype: 'textfield',
						width: 290,
						id: 'txtTituloFinal',
						name: 'txtTituloFinal',
						fieldLabel: 'Texto:',
						value: ''
					},
					comboHipotesis_index
				]
			},                        			
			{html:"&nbsp;",border:false,width:80},
			{
				xtype: 'textfield',
				id: 'txtVersionFinal',
				name: 'txtVersionFinal',
				fieldLabel: 'Version Final',
				value: '',
				width: 400
			},
			{html:"&nbsp;",border:false,width:50},
			
			{html:"&nbsp;",border:false,width:50},
			comboTipoCaso,
			{html:"&nbsp;",border:false,width:80},
			comboNivelCriticidad,
			{html:"&nbsp;",border:false,width:50},
			
			{html:"&nbsp;",border:false,width:50},
			{
				xtype: 'textfield',
				id: 'txtClienteAfectado',
				name: 'txtClienteAfectado',
				fieldLabel: 'Cliente Afectado',
				value: '',
				width: 400
			},
			{html:"&nbsp;",border:false,width:80},
			{
				xtype: 'textfield',
				id: 'txtLoginAfectado',
				name: 'txtLoginAfectado',
				fieldLabel: 'Login Afectado',
				value: '',
				width: 400
			},
			{html:"&nbsp;",border:false,width:50},
			
			
			{html:"&nbsp;",border:false,width:50},                                          			
			{
				xtype: 'combobox',
				fieldLabel: 'Empresa Asignado:',
				id: 'sltEmpresa',
				name: 'sltEmpresa',
				store: storeEmpresas,
				displayField: 'opcion',
				valueField: 'valor',
				queryMode: "remote",
				emptyText: '',
				width:400,
				listeners: {
						  select: function(combo){							
						    
							  Ext.getCmp('comboCiudad').reset();									
							  Ext.getCmp('comboDepartamento').reset();
							  Ext.getCmp('comboEmpleado').reset();
															  
							  Ext.getCmp('comboCiudad').setDisabled(false);								
							  Ext.getCmp('comboDepartamento').setDisabled(true);
							  Ext.getCmp('comboEmpleado').setDisabled(true);
							  
							  presentarCiudades(combo.getValue());
						  }
				},
				forceSelection: true
			}, 
			{html:"&nbsp;",border:false,width:80},
			{
				xtype: 'combobox',
				fieldLabel: 'Ciudad Asignado',
				id: 'comboCiudad',
				name: 'comboCiudad',
				store: storeCiudades,
				displayField: 'nombre_canton',
				valueField: 'id_canton',
				queryMode: "remote",
				emptyText: '',
				width:400,
				disabled: true,
				listeners: {
					select: function(combo){															
						Ext.getCmp('comboDepartamento').reset();
						Ext.getCmp('comboEmpleado').reset();
																						
						Ext.getCmp('comboDepartamento').setDisabled(false);
						Ext.getCmp('comboEmpleado').setDisabled(true);
						
						empresa = Ext.getCmp('sltEmpresa').getValue();
						
						presentarDepartamentosPorCiudad(combo.getValue(),empresa);
					}
				},
				forceSelection: true
			}, 
			{html:"&nbsp;",border:false,width:50},
			
			{html:"&nbsp;",border:false,width:50},
			{
				xtype: 'combobox',
				fieldLabel: 'Departamento Asignado',
				id: 'comboDepartamento',
				name: 'comboDepartamento',
				store: storeDepartamentosCiudad,
				displayField: 'nombre_departamento',
				valueField: 'id_departamento',
				queryMode: "remote",
				minChars: 3,
				emptyText: '',
				width:400,
				disabled: true,
				listeners: {
					select: function(combo){							
						
						
						Ext.getCmp('comboEmpleado').reset();
																														
						Ext.getCmp('comboEmpleado').setDisabled(false);
						
						empresa = Ext.getCmp('sltEmpresa').getValue();
						
						canton  = Ext.getCmp('comboCiudad').getValue();
						
						presentarEmpleadosXDepartamentoCiudad(combo.getValue(), canton, empresa, '','si');
					}
				},
				forceSelection: true
			}, 
			{html:"&nbsp;",border:false,width:80},
			combo_empleados,
			{html:"&nbsp;",border:false,width:50},   
						
			
			{html:"&nbsp;",border:false,width:50},
			comboEmpleados1,
			{html:"&nbsp;",border:false,width:80},
			comboEmpleados2,
			{html:"&nbsp;",border:false,width:50},	
			 
			{html:"&nbsp;",border:false,width:50},
			{
				xtype: 'fieldcontainer',
				fieldLabel: 'Fecha Apertura',
				//layout: 'hbox',
				items: [
					{
						xtype: 'datefield',
						width: 290,
						id: 'feAperturaDesde',
						name: 'feAperturaDesde',
						fieldLabel: 'Desde:',
						format: 'Y-m-d',
						editable: false
					},
					{
						xtype: 'datefield',
						width: 290,
						id: 'feAperturaHasta',
						name: 'feAperturaHasta',
						fieldLabel: 'Hasta:',
						format: 'Y-m-d',
						editable: false
					}
				]
			},
			{html:"&nbsp;",border:false,width:80},
			{
				xtype: 'fieldcontainer',
				fieldLabel: 'Fecha Cierre',				
				items: [
					{
						xtype: 'datefield',
						width: 290,
						id: 'feCierreDesde',
						name: 'feCierreDesde',
						fieldLabel: 'Desde:',
						format: 'Y-m-d',
						editable: false
					},
					{
						xtype: 'datefield',
						width: 290,
						id: 'feCierreHasta',
						name: 'feCierreHasta',
						fieldLabel: 'Hasta:',
						format: 'Y-m-d',
						editable: false
					}
				]
			},                        
                        {html:"&nbsp;",border:false,width:50},
			
			{html:"&nbsp;",border:false,width:50},
			{
				xtype: 'combobox',
				fieldLabel: 'Empresa Creación Caso:',
				id: 'sltEmpresaCaso',
				name: 'sltEmpresaCaso',
				store: storeEmpresas,
				displayField: 'opcion',
				valueField: 'valor',
				queryMode: "remote",
				emptyText: '',
				width:400,				
				forceSelection: true
			}, 
                        
			                     
                               
		],	
        renderTo: 'filtro'
    });
       
});


function presentarCiudades(empresa){
      
    storeCiudades.proxy.extraParams = { empresa:empresa};
    storeCiudades.load();
  
  
}


function presentarDepartamentosPorCiudad(id_canton , empresa){
  
    storeDepartamentosCiudad.proxy.extraParams = { id_canton:id_canton,empresa:empresa};
    storeDepartamentosCiudad.load();
  
}


function presentarEmpleadosXDepartamentoCiudad(id_departamento, id_canton, empresa, valorIdDepartamento, esJefe){
  
    storeEmpleados.proxy.extraParams = { id_canton:id_canton,empresa:empresa,id_departamento:id_departamento,departamento_caso:valorIdDepartamento,es_jefe:esJefe};
    storeEmpleados.load();
  
}



function buscar(){
    
    estado          = Ext.getCmp('sltEstado').value;
    numeroCaso      = Ext.getCmp('txtNumero').value;    
    feAperturaDesde = Ext.getCmp('feAperturaDesde').value;
    feAperturaHasta = Ext.getCmp('feAperturaHasta').value; 
    
    //Si el cliente se encuentra en sesion la busqueda de los Casos permanece sin cambio
    //caso contrario se valida que se escoja fechas de apertura siempre y cuando no pongan el
    //filtro de numero de caso
    if(!boolClienteSesion)
    {            
        if (numeroCaso === "")
        {                                       
            if ((isNaN(feAperturaDesde) || isNaN(feAperturaHasta)) || (feAperturaDesde==="" || feAperturaHasta==="" ))
            {
                Ext.Msg.alert('Alerta ', 'Debe escoger al menos la Fecha Apertura para su busqueda');
                return;
            }
            else
            {
                if(getDiferenciaTiempo(Ext.getCmp('feAperturaDesde').value , Ext.getCmp('feAperturaHasta').value ) > 31)
                {
                    Ext.Msg.alert('Alerta ', "Consulta permitida con un maximo de 30 dias");
                    return;
                }
            }
        }    
    }    

    if (isNaN(comboTipoCaso.getValue()))
        comboTipoCaso.setValue('');
    if (isNaN(comboHipotesis_index.getValue()))
        comboHipotesis_index.setValue('');
    if (isNaN(comboNivelCriticidad.getValue()))
        comboNivelCriticidad.setValue('');

    if (isNaN(Ext.getCmp('comboDepartamento').getValue()))
        Ext.getCmp('comboDepartamento').value('');
    if (isNaN(Ext.getCmp('comboCiudad').getValue()))
        Ext.getCmp('comboCiudad').value('');
    empleado = Ext.getCmp('comboEmpleado').getValue();

    store.proxy.extraParams = {
        numero         : numeroCaso,
        estado         : estado,
        empresa        : Ext.getCmp('sltEmpresaCaso').value,
        tipoCaso       : (!isNaN(comboTipoCaso.getValue()) ? comboTipoCaso.getValue() : ''),
        tituloInicial  : Ext.getCmp('txtTituloInicial').value,
        versionInicial : Ext.getCmp('txtVersionInicial').value,
        tituloFinal    : Ext.getCmp('txtTituloFinal').value,
        tituloFinalHip : (!isNaN(comboHipotesis_index.getValue()) ? comboHipotesis_index.getValue() : ''),
        versionFinal   : Ext.getCmp('txtVersionFinal').value,
        nivelCriticidad: (!isNaN(comboNivelCriticidad.getValue()) ? comboNivelCriticidad.getValue() : ''),
        clienteAfectado: Ext.getCmp('txtClienteAfectado').value,
        loginAfectado  : Ext.getCmp('txtLoginAfectado').value,
        ca_departamento: (!isNaN(Ext.getCmp('comboDepartamento').getValue()) ? Ext.getCmp('comboDepartamento').getValue() : ''),
        ca_empleado    : empleado ? empleado : '',
        ca_ciudad      : (!isNaN(Ext.getCmp('comboCiudad').getValue()) ? Ext.getCmp('comboCiudad').getValue() : ''),
        usrApertura    : (comboEmpleados1.getValue() ? comboEmpleados1.getValue() : ''),
        usrCierre      : (comboEmpleados2.getValue() ? comboEmpleados2.getValue() : ''),
        feAperturaDesde: feAperturaDesde,
        feAperturaHasta: feAperturaHasta,
        feCierreDesde  : Ext.getCmp('feCierreDesde').value,
        feCierreHasta  : Ext.getCmp('feCierreHasta').value,
        boolSearch     : true
    };
    store.load();
}
function limpiar(){
    Ext.getCmp('txtNumero').value = "";
    Ext.getCmp('txtNumero').setRawValue("");
    Ext.getCmp('sltEstado').value = boolClienteSesion ? "" : "Asignado";
    Ext.getCmp('sltEstado').setRawValue(boolClienteSesion ? "" : "Asignado");
    Ext.getCmp('sltEmpresa').value = "";
    Ext.getCmp('sltEmpresa').setRawValue("");
    Ext.getCmp('sltEmpresaCaso').value = "";
    Ext.getCmp('sltEmpresaCaso').setRawValue("");
    Ext.getCmp('comboHipotesis_index').value = "";
    Ext.getCmp('comboHipotesis_index').setRawValue("");
    Ext.getCmp('comboDepartamento').value = "";
    Ext.getCmp('comboDepartamento').setRawValue("");
    Ext.getCmp('comboEmpleado').value = "";
    Ext.getCmp('comboEmpleado').setRawValue("");
    Ext.getCmp('comboCiudad').value = "";
    Ext.getCmp('comboCiudad').setRawValue("");
    Ext.getCmp('comboHipotesis_index').reset();
    Ext.getCmp('comboDepartamento').reset();
    Ext.getCmp('comboEmpleado').reset();
    Ext.getCmp('comboCiudad').reset();
    Ext.getCmp('comboDepartamento').setDisabled(true);
    Ext.getCmp('comboEmpleado').setDisabled(true);
    Ext.getCmp('comboCiudad').setDisabled(true);
    Ext.getCmp('txtTituloInicial').value = "";
    Ext.getCmp('txtTituloInicial').setRawValue("");
    Ext.getCmp('txtVersionInicial').value = "";
    Ext.getCmp('txtVersionInicial').setRawValue("");
    Ext.getCmp('txtTituloFinal').value = "";
    Ext.getCmp('txtTituloFinal').setRawValue("");
    Ext.getCmp('txtVersionFinal').value = "";
    Ext.getCmp('txtVersionFinal').setRawValue("");
    Ext.getCmp('comboTipoCaso').value = "";
    Ext.getCmp('comboTipoCaso').setRawValue("");
    Ext.getCmp('comboNivelCriticidad').value = "";
    Ext.getCmp('comboNivelCriticidad').setRawValue("");
    Ext.getCmp('txtClienteAfectado').value = "";
    Ext.getCmp('txtClienteAfectado').setRawValue("");
    Ext.getCmp('txtLoginAfectado').value = "";
    Ext.getCmp('txtLoginAfectado').setRawValue("");
    Ext.getCmp('comboEmpleados1').value = "";
    Ext.getCmp('comboEmpleados1').setRawValue("");
    Ext.getCmp('comboEmpleados2').value = "";
    Ext.getCmp('comboEmpleados2').setRawValue("");
    Ext.getCmp('feAperturaDesde').value = "";
    Ext.getCmp('feAperturaDesde').setRawValue("");
    Ext.getCmp('feAperturaHasta').value = "";
    Ext.getCmp('feAperturaHasta').setRawValue("");
    Ext.getCmp('feCierreDesde').value = "";
    Ext.getCmp('feCierreDesde').setRawValue("");
    Ext.getCmp('feCierreHasta').value = "";
    Ext.getCmp('feCierreHasta').setRawValue("");
			
    store.proxy.extraParams = {
        numero: '',        
        estado: '',
        tituloInicial: '',
        versionInicial: '',
        tituloFinal: '',
        tituloFinalHip: '',
        versionFinal: '',
        tipoCaso: '',
        nivelCriticidad: '',
        ca_departamento: '',
        ca_empleado: '',
        ca_ciudad: '',
        usrApertura: '',
        usrCierre: '',
        feAperturaDesde: '',
        feAperturaHasta: '',
        feCierreDesde: '',
        feCierreHasta: ''        
    };   
    
    grid.getStore().removeAll();            
}

function eliminarAlgunos(){
    var param = '';
    if(sm.getSelection().length > 0)
    {
      var estado = 0;
      for(var i=0 ;  i < sm.getSelection().length ; ++i)
      {
        param = param + sm.getSelection()[i].data.id_caso;
        
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
        Ext.Msg.confirm('Alerta','Se eliminaran los registros. Desea continuar?', function(btn){
            if(btn=='yes'){
                Ext.Ajax.request({
                    url: "deleteAjax",
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
      alert('Seleccione por lo menos un registro de la lista.');
    }
}


function verAfectados(rec)
{	
	var id_caso = rec.get('id_caso');
	var numero = rec.get('numero_caso');
	var fecha = rec.get('fecha_apertura');
	var hora = rec.get('hora_apertura');
	var version_inicial = rec.get('version_ini');
	
	winVerAfectados = "";
	var formPanelVerAfectados = "";
	
    if (winVerAfectados)
    {
		cierraVentanaByIden(winVerAfectados);
		winVerAfectados = "";
	}

    if (!winSintomas)
    { 
		btncancelar = Ext.create('Ext.Button', {
			text: 'Cerrar',
			cls: 'x-btn-rigth',
			handler: function() {
				cierraVentanaByIden(winVerAfectados);
			}
		});	
		
		var string_html  = "<table width='100%' border='0' >";
		string_html += "    <tr>";
		string_html += "        <td colspan='6'>";
		string_html  = "			<table width='100%' border='0' >";
		string_html += "                <tr style='height:270px'>";
		string_html += "                    <td colspan='4'><div id='criterios_aceptar'></div></td>";
		string_html += "                </tr>";
		string_html += "                <tr style='height:270px'>";
		string_html += "                    <td colspan='4'><div id='afectados_aceptar'></div></td>";
		string_html += "                </tr>";
		string_html += "            </table>";
		string_html += "        </td>";
		string_html += "    </tr>";
		string_html += "</table>";
		
		DivsCriteriosAfectados =  Ext.create('Ext.Component', {
			html: string_html,
			padding: 1,
			layout: 'anchor',
			style: { border: '0' }
		});
		
		
		formPanelVerAfectados = Ext.create('Ext.form.Panel', {
			bodyPadding: 5,
			waitMsgTarget: true,
			layout: 'column',
			fieldDefaults: {
				labelAlign: 'left',
				labelWidth: 150,
				msgTarget: 'side'
			},
			items: 
			[
				{
					xtype: 'fieldset',
					title: 'Información de Afectados',                    
					autoHeight: true,
					width: 1030,
					items: 
					[
						DivsCriteriosAfectados
					]
				}
			]
		});

		winVerAfectados = Ext.create('Ext.window.Window', {
			title: 'Caso ' + numero,
			modal: true,
			width: 1060,
			height: 640,
			resizable: false,
			layout: 'fit',
			items: [formPanelVerAfectados],
			buttonAlign: 'center',
			buttons:[btncancelar]
		}).show(); 
		
		
		////////////////Grid  Criterios////////////////  
		storeCriterios_aceptar = new Ext.data.JsonStore(
		{
			total: 'total',
			pageSize: 10,
			autoLoad: true,
			proxy: {
				type: 'ajax',
				url : ''+id_caso+'/getCriterios',
				reader: {
					type: 'json',
					totalProperty: 'total',
					root: 'encontrados'
				},
				extraParams: {
					todos: 'YES'
				}
			},
			fields:
			[
				{name:'tipo', mapping:'tipo'},
				{name:'nombre', mapping:'nombre'},
				{name:'id_criterio_afectado', mapping:'id_criterio_afectado'},
				{name:'caso_id', mapping:'caso_id'},
				{name:'criterio', mapping:'criterio'},
				{name:'opcion', mapping:'opcion'}
			]                
		});
		gridCriterios_aceptar = Ext.create('Ext.grid.Panel', {
			title:'Criterios de Seleccion',
			width: 1000,
			height: 260,
			autoRender:true,
			enableColumnResize :false,
			id:'gridCriterios_aceptar',
			store: storeCriterios_aceptar,
			viewConfig: { enableTextSelection: true }, 
			loadMask: true,
			frame:true,
			forceFit:true,
			columns:
			[
				{
				  id: 'aceptar_id_criterio_afectado',
				  header: 'id_criterio_afectado',
				  dataIndex: 'id_criterio_afectado',
				  hidden: true,
				  hideable: false
				},
				{
				  id: 'aceptar_caso_id',
				  header: 'caso_id',
				  dataIndex: 'caso_id',
				  hidden: true,
				  sortable: true
				},
				{
				  id: 'aceptar_tipo_criterio',
				  header: 'Tipo',
				  dataIndex: 'tipo',
				  width:60,
				  hideable: false
				},
				{
				  id: 'aceptar_nombre_tipo_criterio',
				  header: 'Nombre',
				  dataIndex: 'nombre',
				  width:80,
				  hideable: false,
				  sortable: true
				},
				{
				  id: 'aceptar_criterio',
				  header: 'Criterio',
				  dataIndex: 'criterio',
				  width: 70,
				  hideable: false
				},
				{
				  id: 'aceptar_opcion',
				  header: 'Opcion',
				  dataIndex: 'opcion',
				  width: 260,
				  sortable: true
				}
			],
			bbar: Ext.create('Ext.PagingToolbar', {
			    store: storeCriterios_aceptar,
			    displayInfo: true,
			    displayMsg: 'Mostrando {0} - {1} de {2}',
			    emptyMsg: "No hay datos que mostrar."
			}),
			renderTo: 'criterios_aceptar'
		});
		
		////////////////Grid  Afectados////////////////  
		storeAfectados_aceptar = new Ext.data.JsonStore(
		{
			autoLoad: true,
			pageSize: 10,
			total: 'total',
			proxy: {
				type: 'ajax',
				url : ''+id_caso+'/getAfectados',
				reader: {
					type: 'json',
					totalProperty: 'total',
					root: 'encontrados'
				},
				extraParams: {
					todos: 'YES'
				}
			},
			fields:
			[
				{name:'tipo', mapping:'tipo'},
				{name:'nombre', mapping:'nombre'},
				{name:'id', mapping:'id'},
				{name:'id_afectado', mapping:'id_afectado'},
				{name:'id_criterio', mapping:'id_criterio'},
				{name:'caso_id_afectado', mapping:'caso_id_afectado'},
				{name:'nombre_afectado', mapping:'nombre_afectado'},
				{name:'descripcion_afectado', mapping:'descripcion_afectado'}
			]                
		});
		gridAfectados_aceptar = Ext.create('Ext.grid.Panel', {
			title:'Equipos Afectados',
			width: 1000,
			height: 260,
			sortableColumns:false,
			store: storeAfectados_aceptar,
			viewConfig: { enableTextSelection: true }, 
			id:'gridAfectados_aceptar',
			enableColumnResize :false,
			loadMask: true,
			frame:true,
			forceFit:true,
			columns: [
				Ext.create('Ext.grid.RowNumberer'),
				{
				  id: 'aceptar_id',
				  header: 'id',
				  dataIndex: 'id',
				  hidden: true,
				  hideable: false
				},
				 {
				  id: 'aceptar_id_afectado',
				  header: 'id_afectado',
				  dataIndex: 'id_afectado',
				  hidden: true,
				  hideable: false
				},
				{
				  id: 'aceptar_id_criterio',
				  header: 'id_criterio',
				  dataIndex: 'id_criterio',
				  hidden: true,
				  hideable: false
				},
				{
				  id: 'aceptar_caso_id_afectado',
				  header: 'caso_id_afectado',
				  dataIndex: 'caso_id_afectado',
				  hidden: true,
				  hideable: false 
				},
				{
				  id: 'aceptar_tipo_afectado',
				  header: 'Tipo',
				  dataIndex: 'tipo',
				  width:65,
				  hideable: false
				},
				{
				  id: 'aceptar_nombre_tipo_afectado',
				  header: 'Nombre',
				  dataIndex: 'nombre',
				  width:85,
				  hideable: false,
				  sortable: true
				},
				{
				  id: 'aceptar_nombre_afectado',
				  header: 'Parte Afectada',
				  dataIndex: 'nombre_afectado',
				  width:210,
				  sortable: true
				},
				{
				  id: 'aceptar_descripcion_afectado',
				  header: 'Descripcion',
				  dataIndex: 'descripcion_afectado',
				  width:145,
				  sortable: true
				}
			],  
			bbar: Ext.create('Ext.PagingToolbar', {
			    store: storeAfectados_aceptar,
			    displayInfo: true,
			    displayMsg: 'Mostrando {0} - {1} de {2}',
			    emptyMsg: "No hay datos que mostrar."
			}),
			renderTo: 'afectados_aceptar'
		});
	}
}


function solicitarInformeEjecutivo(idCaso)
{
    Ext.get(document.body).mask('Loading...');    
    Ext.Ajax.request({
        url: url_generarInforme,
        method: 'post',
        timeout: 400000,
        params: {
            idCaso: idCaso
        },
        success: function (response) 
        {
            var json = Ext.JSON.decode(response.responseText);            

            Ext.get(document.body).unmask();
            
            if(json.status == 'OK')
            {
                store.load();
            }
            Ext.Msg.alert('Mensaje ', json.mensaje);

        }})
}


function editarInformeEjecutivo(id_caso)
{
    Ext.Ajax.request({
        url: url_editarInforme,
        method: 'post',
        timeout: 400000,
        params: {
            idCaso: id_caso
        },
        success: function (response) 
        {
            var json = Ext.JSON.decode(response.responseText);            
            Ext.Msg.alert('Mensaje ', json.mensaje);
            store.load();

        }})
}

function getInformeEjecutivo(rec)
{
    var id_caso = rec.get('id_caso');
    var conn = new Ext.data.Connection({
      listeners: {
          'beforerequest': {
              fn: function (con, opt) {
                  Ext.get(document.body).mask('Loading...');
              },
              scope: this
          },
          'requestcomplete': {
              fn: function (con, res, opt) {
                  Ext.get(document.body).unmask();
              },
              scope: this
          },
          'requestexception': {
              fn: function (con, res, opt) {
                  Ext.get(document.body).unmask();
              },
              scope: this
          }
      }
    });
   
    btncancelar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            iconCls: 'icon_cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
              winSeguimientoTareasXCaso.destroy();													
            }
    });
    
    btnReasignar = Ext.create('Ext.Button', {
            text: 'Reasignar',
            iconCls: 'icon_solicitud',
            cls: 'x-btn-rigth',
            handler: function() {
                
                if(rec.get('estadoInforme') == 'Pendiente')
                {
                    Ext.get(document.body).mask('Loading...');    
                    Ext.Ajax.request({
                        url: url_ReasignarInformeEjecutivo,
                        method: 'post',
                        timeout: 400000,
                        params: {
                            idCaso: id_caso
                        },
                        success: function (response) 
                        {
                            Ext.get(document.body).unmask();
                            var json = Ext.JSON.decode(response.responseText);

                            if(json.status == 'OK')
                            {
                                store.load();
                            }
                            Ext.Msg.alert('Mensaje ', json.mensaje);
                            winSeguimientoTareasXCaso.destroy();

                        }})     
                    
                }
                else
                {
                    Ext.Msg.alert('Mensaje ', 'El informe ejecutivo ya fue reasignado.');
                }
              
           
            }
    });
    
    
    btnGenerarPdf = Ext.create('Ext.Button', {
            text: 'Generar PDF',
            iconCls: 'icon_exportar_pdf',
            cls: 'x-btn-rigth',
            handler: function() {
              generarPDF(id_caso);											
            }
    });
    
	storeSeguimientoTareasXcaso = new Ext.data.Store({ 
		total: 'total',
		autoLoad: true,
		proxy: {
			type: 'ajax',
			url : url_getInforme,
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
			extraParams: {
				idCaso: id_caso			
			}
		},
		fields:
		[
		      {name:'idEncuestaPregunta', mapping:'idEncuestaPregunta'},
                      {name:'numero', mapping:'numero'},
                      {name:'pregunta', mapping:'pregunta'},
		      {name:'respuesta', mapping:'respuesta'}			
		]
	});
	gridSeguimientoTareasXCaso = Ext.create('Ext.grid.Panel', {
		id:'gridSeguimientoTareasXCaso',
		store: storeSeguimientoTareasXcaso,		
		columnLines: true,
        cellWrap: true,
		columns: [
            {
                id: 'numero',
                header: '#',
                dataIndex: 'numero',
                width:30,
                sortable: true						 
            },
            {
                id: 'pregunta',
                header: 'Enunciado',
                dataIndex: 'pregunta',
                width:250,
                sortable: true						 
            },
            {
                id: 'respuesta',
                header: 'Respuesta',
                dataIndex: 'respuesta',                
                renderer: function(v) {
                  return Ext.util.Format.substr(v, 0, 150);
                },
                width:650				 
            },            
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 80,
                items:
                        [
                            {
                                getClass: function (v, meta, rec) {

                                    return 'button-grid-edit';

                                },
                                tooltip: 'Editar',
                                handler: function (grid, rowIndex, colIndex) {
                                    var rec = storeSeguimientoTareasXcaso.getAt(rowIndex);
                                    editarCampo(rec);
                                }
                            },
                            {
                                getClass: function (v, meta, rec) {


                                if('PLAN DE MEJORAMIENTO' == rec.data.pregunta)
                                {
                                    return 'button-grid-verDetalle';
                                }
                                
                                

                                },
                                tooltip: 'Crear Tarea',
                                handler: function (grid, rowIndex, colIndex) {
                                    conn.request({
                                                    method: 'POST',
                                                    url: url_obtenerFechaServer,
                                                    success: function(response)
                                                    {
                                                        var json = Ext.JSON.decode(response.responseText);

                                                        if (json.success)
                                                        {    
                                                           var fechaFinArray = json.fechaActual.split("-");
                                                           var fechaActual = fechaFinArray[0] + "-" + fechaFinArray[1] + "-" + fechaFinArray[2];
                                                           var rec = storeSeguimientoTareasXcaso.getAt(rowIndex);
                                                           crearTarea(rec, fechaActual, json.horaActual,storeSeguimientoTareasXcaso.getAt(rowIndex).data.idEncuestaPregunta);
                                                        }
                                                        else
                                                        {
                                                            Ext.Msg.alert('Alerta ', json.error);
                                                        }
                                                    }
                                                });
                                }
                            }
                        ]
            }
        ],		
		width: 1010,
		height: 400,
		listeners:
            {
                itemdblclick: function( view, record, item, index, eventobj, obj ){
                    var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                    Ext.Msg.show({
                        title:'Copiar texto?',
                        msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                        buttons: Ext.Msg.OK,
                        icon: Ext.Msg.INFORMATION
                    });
                    
                    
                },
                viewready: function (grid) {
                    var view = grid.view;

                    // record the current cellIndex
                    grid.mon(view, {
                        uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                            grid.cellIndex = cellIndex;
                            grid.recordIndex = recordIndex;
                        }
                    });

                    grid.tip = Ext.create('Ext.tip.ToolTip', {
                        target: view.el,
                        delegate: '.x-grid-cell',
                        trackMouse: true,
                        renderTo: Ext.getBody(),
                        listeners: {
                            beforeshow: function updateTipBody(tip) {
                                if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                    header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                    tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                                }
                            }
                        }
                    });

                }                                    
            }
	});
    
	formPanelSeguimientoTareasXCaso = Ext.create('Ext.form.Panel', {
			bodyPadding: 5,
			waitMsgTarget: true,
			height: 500,
			width:700,
			layout: 'fit',
			fieldDefaults: {
				labelAlign: 'left',
				//labelWidth: 140,
				msgTarget: 'side'
			},

			items: [{
				xtype: 'fieldset',				
				defaultType: 'textfield',
				items: [					
					gridSeguimientoTareasXCaso
				]
			}]
		 });
	winSeguimientoTareasXCaso = Ext.create('Ext.window.Window', {
			title: 'Informe ejecutivo del caso',
			modal: true,
			width: 1060,
			height: 510,
			resizable: true,
			layout: 'fit',
			items: [formPanelSeguimientoTareasXCaso],
			buttonAlign: 'center',
			buttons:[btnGenerarPdf, btnReasignar, btncancelar]
	}).show();       
}


function generarPDF(id_caso)
{   
    window.open("ajaxGenerarPdfInformeEjecutivo?idCaso="+id_caso,'_blank');
}


/************************************************************************ */
/*********************** CREAR SUB TAREAS ******************************** */
/************************************************************************ */

function crearTarea(data,fecha,hora, pregunta)
{
    var valorIdDepartamento = '';

    storeProcesos = new Ext.data.Store({
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_procesos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            }
        },
        fields:
            [
                {name: 'id', mapping: 'id'},
                {name: 'nombreProceso', mapping: 'nombreProceso'}
            ]

    });

    storeTareas = new Ext.data.Store({
        total: 'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_tareaProceso,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreProceso: 'TAREAS SOPORTE',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'idTarea', mapping: 'idTarea'},
                {name: 'nombreTarea', mapping: 'nombreTarea'}
            ]
    });

    //Informacion de asignacion

    storeEmpleados = new Ext.data.Store({
        total: 'total',
        pageSize: 1000,
        proxy: {
            type: 'ajax',
            url: url_empleadoPorDepartamentoCiudad,
            reader: {
                type: 'json',
                totalProperty: 'result.total',
                root: 'result.encontrados',
                metaProperty: 'myMetaData'
            },
            extraParams: {
                nombre: ''
            }
        },
        fields:
            [
                {name: 'id_empleado', mapping: 'id_empleado'},
                {name: 'nombre_empleado', mapping: 'nombre_empleado'}
            ]
    });


    storeEmpresas = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_empresaPorSistema,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                app: 'TELCOS'
            }
        },
        fields:
            [
                {name: 'opcion', mapping: 'nombre_empresa'},
                {name: 'valor', mapping: 'prefijo'}
            ]
    });

    storeCiudades = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_ciudadPorEmpresa,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'id_canton', mapping: 'id_canton'},
                {name: 'nombre_canton', mapping: 'nombre_canton'}
            ]
    });


    storeDepartamentosCiudad = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_departamentoPorEmpresaCiudad,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'id_departamento', mapping: 'id_departamento'},
                {name: 'nombre_departamento', mapping: 'nombre_departamento'}
            ]
    });


    storeCuadrillas = new Ext.data.Store({
        total: 'total',
        pageSize: 9999,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_integrantesCuadrilla,
            reader: {
                type: 'json',
                totalProperty: 'result.total',
                root: 'result.encontrados',
                metaProperty: 'myMetaData'
            },
            extraParams: {
                estado: 'Eliminado'
            }
        },
        fields:
            [
                {name: 'idCuadrilla', mapping: 'idCuadrilla'},
                {name: 'nombre', mapping: 'nombre'}
            ],
        listeners: {
            load: function(store) {
                if (store.proxy.extraParams.origenD === "Departamento")
                {
                    document.getElementById('radio_e').disabled = false;
                    document.getElementById('radio_c').disabled = false;
                    document.getElementById('radio_co').disabled = false;
                    document.getElementById('radio_e').checked = false;
                    document.getElementById('radio_c').checked = false;
                    document.getElementById('radio_co').checked = false;
                    Ext.getCmp('comboCuadrilla').setDisabled(true);
                    Ext.getCmp('combo_empleados').setDisabled(true);
                    Ext.getCmp('comboContratista').setDisabled(true);
                    
                    storeCuadrillas.proxy.extraParams.origenD = '';
                }
            }

        }
    });

    storeContratista = new Ext.data.Store({
        total: 'total',
        pageSize: 9999,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_empresasExternas,
            reader: {
                type: 'json',
                totalProperty: 'result.total',
                root: 'result.encontrados',
                metaProperty: 'myMetaData'
            },
            extraParams: {
            }
        },
        fields:
            [
                {name: 'id_empresa_externa', mapping: 'id_empresa_externa'},
                {name: 'nombre_empresa_externa', mapping: 'nombre_empresa_externa'}
            ]
    });

    var iniHtml = '<div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n\
                      &nbsp;<input type="radio" onchange="setearCombo4(1);" value="empleado" name="radioCuadrilla" id="radio_e" disabled>&nbsp;\n\
                      Empleado&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" onchange="setearCombo4(2);" value="cuadrilla" name="radioCuadrilla" \n\
                      id="radio_c" disabled>&nbsp;Cuadrilla&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" onchange="setearCombo4(3);"\n\
                      value="contratista" name="radioCuadrilla" id="radio_co" disabled>&nbsp;Contratista</div>';

    RadiosTiposResponsable = Ext.create('Ext.Component', {
        html: iniHtml,
        width: 600,
        padding: 10,
        style: {color: '#000000'}});

    combo_empleados = new Ext.form.ComboBox({
        id: 'combo_empleados',
        name: 'combo_empleados',
        fieldLabel: "Empleado",
        store: storeEmpleados,
        displayField: 'nombre_empleado',
        valueField: 'id_empleado',
        queryMode: "remote",
        emptyText: '',
        disabled: true,
        width: 380,
        listeners: {
            select: function(){
                Ext.getCmp('comboCuadrilla').value = "";
                Ext.getCmp('comboCuadrilla').setRawValue("");
                Ext.getCmp('comboContratista').value = "";
                Ext.getCmp('comboContratista').setRawValue("");
            }
        }
    });
    var formPanelTareas = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 5,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        autoHeight: true,
                        width: 450,
                        items:
                            [
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Procesos:',
                                    id:'cmbProcesos',
                                    store: storeProcesos,
                                    displayField: 'nombreProceso',
                                    valueField: 'id',
                                    queryMode: "remote",
                                    emptyText: '',
                                    width: 380,
                                    listeners: {
                                        select: function(combo, records, eOpts)
                                        {
                                            storeTareas.proxy.extraParams = {id: combo.getValue()};
                                            storeTareas.load();
                                            Ext.getCmp('cmbTarea').setVisible(true);
                                            Ext.getCmp('cmbTarea').setDisabled(false);

                                        }
                                    }
                                },
                                {
                                    xtype: 'combobox',
                                    id: 'cmbTarea',
                                    store: storeTareas,
                                    displayField: 'nombreTarea',
                                    valueField: 'idTarea',
                                    fieldLabel: 'Tarea:',
                                    queryMode: "remote",
                                    emptyText: '',
                                    width: 380,
                                    disabled: true
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Empresa:',
                                    id: 'comboEmpresa',
                                    name: 'comboEmpresa',
                                    store: storeEmpresas,
                                    displayField: 'opcion',
                                    valueField: 'valor',
                                    queryMode: "remote",
                                    emptyText: '',
                                    width: 380,
                                    listeners: {
                                        select: function(combo) {

                                            Ext.getCmp('comboCiudad').reset();
                                            Ext.getCmp('comboDepartamento').reset();
                                            Ext.getCmp('combo_empleados').reset();

                                            Ext.getCmp('comboCiudad').setDisabled(false);
                                            Ext.getCmp('comboDepartamento').setDisabled(true);
                                            Ext.getCmp('combo_empleados').setDisabled(true);

                                            presentarCiudades(combo.getValue());
                                        }
                                    },
                                    forceSelection: true
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Ciudad',
                                    id: 'comboCiudad',
                                    name: 'comboCiudad',
                                    store: storeCiudades,
                                    displayField: 'nombre_canton',
                                    valueField: 'id_canton',
                                    queryMode: "remote",
                                    emptyText: '',
                                    disabled: true,
                                    width: 380,
                                    listeners: {
                                        select: function(combo) {
                                            Ext.getCmp('comboDepartamento').reset();
                                            Ext.getCmp('combo_empleados').reset();

                                            Ext.getCmp('comboDepartamento').setDisabled(false);
                                            Ext.getCmp('combo_empleados').setDisabled(true);

                                            empresa = Ext.getCmp('comboEmpresa').getValue();

                                            presentarDepartamentosPorCiudad(combo.getValue(), empresa);
                                        }
                                    },
                                    forceSelection: true
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Departamento',
                                    id: 'comboDepartamento',
                                    name: 'comboDepartamento',
                                    store: storeDepartamentosCiudad,
                                    displayField: 'nombre_departamento',
                                    valueField: 'id_departamento',
                                    queryMode: "remote",
                                    emptyText: '',
                                    disabled: true,
                                    width: 380,
                                    listeners: {
                                        afterRender: function(combo) {
                                            if(typeof strPrefijoEmpresaSession !== 'undefined' && strPrefijoEmpresaSession.trim())
                                            {
                                                storeEmpresas.load(function() {
                                                    Ext.getCmp('comboEmpresa').setValue(strPrefijoEmpresaSession);
                                                    storeCiudades.proxy.extraParams = { empresa:strPrefijoEmpresaSession };
                                                    storeCiudades.load(function() {
                                                        Ext.getCmp('comboCiudad').setDisabled(false);
                                                        if(typeof strIdCantonUsrSession !== 'undefined' && strIdCantonUsrSession.trim())
                                                        {
                                                            Ext.getCmp('comboCiudad').setValue(Number(strIdCantonUsrSession));
                                                            storeDepartamentosCiudad.proxy.extraParams = { id_canton:   strIdCantonUsrSession,
                                                                                                           empresa  :   strPrefijoEmpresaSession};
                                                            storeDepartamentosCiudad.load(function() {
                                                                Ext.getCmp('comboDepartamento').setDisabled(false);
                                                                combo.setValue(Number(strIdDepartamentoUsrSession));
                                                                presentarEmpleadosXDepartamentoCiudad(strIdDepartamentoUsrSession, 
                                                                                                      strIdCantonUsrSession, 
                                                                                                      strPrefijoEmpresaSession);
                                                                presentarCuadrillasXDepartamento(strIdDepartamentoUsrSession);
                                                                presentarContratistas();
                                                                elWinCrearTarea.unmask();
                                                            });
                                                        }
                                                        else
                                                        {
                                                            elWinCrearTarea.unmask();
                                                        }
                                                    });
                                                });
                                            }
                                            else
                                            {
                                                elWinCrearTarea.unmask();
                                            }
                                        },
                                        select: function(combo) {


                                            Ext.getCmp('combo_empleados').reset();
                                            Ext.getCmp('combo_empleados').setDisabled(true);
                                            Ext.getCmp('comboCuadrilla').value = "";
                                            Ext.getCmp('comboCuadrilla').setRawValue("");
                                            Ext.getCmp('comboCuadrilla').setDisabled(true);
                                            Ext.getCmp('comboContratista').value = "";
                                            Ext.getCmp('comboContratista').setRawValue("");
                                            Ext.getCmp('comboContratista').setDisabled(true);
                                            empresa = Ext.getCmp('comboEmpresa').getValue();
                                            canton = Ext.getCmp('comboCiudad').getValue();
                                            presentarEmpleadosXDepartamentoCiudad(combo.getValue(), canton, empresa, valorIdDepartamento, 'no');
                                            presentarCuadrillasXDepartamento(Ext.getCmp('comboDepartamento').getValue());
                                            presentarContratistas();
                                        }
                                    },
                                    forceSelection: true
                                },
                                RadiosTiposResponsable,
                                {
                                    xtype: 'combobox',
                                    id: 'combo_empleados',
                                    name: 'combo_empleados',
                                    fieldLabel: "Empleado",
                                    store: storeEmpleados,
                                    displayField: 'nombre_empleado',
                                    valueField: 'id_empleado',
                                    queryMode: "remote",
                                    emptyText: '',
                                    disabled: true,
                                    width: 380,
                                    listeners: {
                                        select: function() {
                                            Ext.getCmp('comboCuadrilla').value = "";
                                            Ext.getCmp('comboCuadrilla').setRawValue("");
                                            Ext.getCmp('comboContratista').value = "";
                                            Ext.getCmp('comboContratista').setRawValue("");
                                        }
                                    }
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Cuadrilla',
                                    id: 'comboCuadrilla',
                                    name: 'comboCuadrilla',
                                    store: storeCuadrillas,
                                    displayField: 'nombre',
                                    valueField: 'idCuadrilla',
                                    queryMode: "remote",
                                    emptyText: '',
                                    disabled: true,
                                    width: 380,
                                    listeners: {
                                        select: function(combo) {
                                            Ext.getCmp('combo_empleados').value = "";
                                            Ext.getCmp('combo_empleados').setRawValue("");
                                            Ext.getCmp('comboContratista').value = "";
                                            Ext.getCmp('comboContratista').setRawValue("");
                                            validarTabletPorCuadrilla(combo.getValue());
                                        }
                                    }

                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Contratista',
                                    id: 'comboContratista',
                                    name: 'comboContratista',
                                    store: storeContratista,
                                    displayField: 'nombre_empresa_externa',
                                    valueField: 'id_empresa_externa',
                                    queryMode: "remote",
                                    emptyText: '',
                                    disabled: true,
                                    width: 380,
                                    listeners: {
                                        select: function() {
                                            Ext.getCmp('combo_empleados').value = "";
                                            Ext.getCmp('combo_empleados').setRawValue("");
                                            Ext.getCmp('comboCuadrilla').value = "";
                                            Ext.getCmp('comboCuadrilla').setRawValue("");
                                        }
                                    }

                                },
                                {
                                    xtype: 'datefield',
                                    fieldLabel: 'Fecha de Ejecución:',
                                    id: 'fecha_ejecucion',
                                    name: 'fecha_ejecucion',
                                    editable: false,
                                    format: 'Y-m-d',
                                    width: 380,
                                    value: fecha,
                                    minValue: fecha
                                },
                                {
                                    xtype: 'timefield',
                                    fieldLabel: 'Hora de Ejecución:',
                                    format: 'H:i',
                                    id: 'hora_ejecucion',
                                    name: 'hora_ejecucion',
                                    minValue: '00:01',
                                    maxValue: '23:59',
                                    increment: 1,
                                    editable: true,
                                    value: hora,
                                    width: 380
                                },
                                {
                                    xtype: 'textarea',
                                    id: 'observacionAsignacion',
                                    fieldLabel: 'Observacion',
                                    name: 'observacion',
                                    rows: 3,
                                    allowBlank: false,
                                    width: 380
                                }
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: 'Agregar Tarea',
                        handler: function()
                        {winSeguimientoTareasXCaso.mask('Cargando...');
                            if (Ext.getCmp('cmbTarea').value !== null && Ext.getCmp('cmbTarea').value !== "")
                            {
                                if (Ext.getCmp('comboEmpresa').getValue() !== null && Ext.getCmp('comboDepartamento').getValue() !== null &&
                                    Ext.getCmp('comboCiudad').getValue() !== null)
                                {

                                    if ((Ext.getCmp('combo_empleados') && Ext.getCmp('combo_empleados').value) ||
                                        (Ext.getCmp('comboCuadrilla') && Ext.getCmp('comboCuadrilla').value && valorAsignacion === "cuadrilla") ||
                                        (Ext.getCmp('comboContratista') && Ext.getCmp('comboContratista').value &&
                                         valorAsignacion === "contratista"))
                                    {
                                        personaEmpresaRol = null;
                                        refAsignadoNombre = null;

                                        if (valorAsignacion === "empleado")
                                        {
                                            var comboEmpleado        = Ext.getCmp('combo_empleados').value;
                                            var valoresComboEmpleado = comboEmpleado.split("@@");
                                            refAsignadoId       = valoresComboEmpleado[0];
                                            personaEmpresaRol   = valoresComboEmpleado[1];
                                            refAsignadoNombre   = Ext.getCmp('combo_empleados').rawValue;
                                            asignadoId          = Ext.getCmp('comboDepartamento').value;
                                            asignadoNombre      = Ext.getCmp('comboDepartamento').rawValue;
                                            tipoAsignado        = "EMPLEADO";

                                        }
                                        else if (valorAsignacion === "cuadrilla")
                                        {
                                            refAsignadoId       = "0";
                                            asignadoId          = Ext.getCmp('comboCuadrilla').value;
                                            asignadoNombre      = Ext.getCmp('comboCuadrilla').rawValue;
                                            tipoAsignado        = "CUADRILLA";
                                        }
                                        else
                                        {
                                            refAsignadoId       = "0";
                                            asignadoId          = Ext.getCmp('comboContratista').value;
                                            asignadoNombre      = Ext.getCmp('comboContratista').rawValue;
                                            tipoAsignado        = "EMPRESAEXTERNA";
                                        }

                                        observacion     = Ext.getCmp('observacionAsignacion').value;
                                        fechaEjecucion  = Ext.getCmp('fecha_ejecucion').value;
                                        horaEjecucion   = Ext.getCmp('hora_ejecucion').value;

                                        var conn = new Ext.data.Connection({
                                            listeners: {
                                                'beforerequest': {
                                                    fn: function(con, opt) {
                                                        elWinCrearTarea.mask('Creando tarea');
                                                    },
                                                    scope: this
                                                },
                                                'requestcomplete': {
                                                    fn: function(con, res, opt) {
                                                        Ext.getBody().unmask();
                                                        winSeguimientoTareasXCaso.unmask();
                                                    },
                                                    scope: this
                                                },
                                                'requestexception': {
                                                    fn: function(con, res, opt) {
                                                        Ext.getBody().unmask();
                                                        winSeguimientoTareasXCaso.unmask();
                                                    },
                                                    scope: this
                                                }
                                            }
                                        });

                                        conn.request({
                                            method: 'POST',
                                            params: {
                                                detalleIdRelac    : data.id_detalle,
                                                idTarea           : Ext.getCmp('cmbTarea').value,
                                                personaEmpresaRol : personaEmpresaRol,
                                                asignadoId        : asignadoId,
                                                nombreAsignado    : asignadoNombre,
                                                refAsignadoId     : refAsignadoId,
                                                refAsignadoNombre : refAsignadoNombre,
                                                observacion       : observacion,
                                                fechaEjecucion    : fechaEjecucion,
                                                horaEjecucion     : horaEjecucion,
                                                tipoAsignacion    : tipoAsignado,
                                                numeroTarea       : data.numero_tarea,
                                                empresaAsignacion : Ext.getCmp('comboEmpresa').value,
                                                intIdDetalleHist  : data.intIdDetalleHist,
                                                strValidarAccion  : 'SI',
                                                intPregunta       : pregunta
                                            },
                                            url: url_crearSubTarea,
                                            success: function(response)
                                            {                                           
                                                var json = Ext.JSON.decode(response.responseText);

                                                if (!json.success && !json.seguirAccion) {
                                                    Ext.MessageBox.show({
                                                        closable   :  false  , multiline : false,
                                                        title      : 'Alerta', msg : json.mensaje,
                                                        buttons    :  Ext.MessageBox.OK, icon : Ext.MessageBox.WARNING,
                                                        buttonText : {ok: 'Cerrar'},
                                                        fn : function (button) {
                                                            if(button === 'ok') {
                                                                winAsignarTarea.destroy();
                                                                store.load();
                                                            }
                                                        }
                                                    });
                                                    return;
                                                }

                                                Ext.Msg.alert('Mensaje', json.mensaje, function(btn) {
                                                    if (btn == 'ok') {
                                                        winAsignarTarea.destroy();
                                                        storeSeguimientoTareasXcaso.load();
                                                        store.load();
                                                    }
                                                });                                              
                                            },
                                            failure: function(rec, op) {
                                                var json = Ext.JSON.decode(op.response.responseText);
                                                Ext.Msg.alert('Alerta ', json.mensaje);
                                            }
                                        });
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Alerta ', 'Por favor escoja un empleado, cuadrilla o contratista');
                                    }
                                }
                                else
                                {
                                    Ext.Msg.alert('Alerta ', 'Campos incompletos, debe seleccionar Empresa,Ciudad y Departamento');
                                }
                            }
                            else
                            {
                                Ext.Msg.alert('Alerta ', 'Debe escoger una Tarea a asignar');
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                            winAsignarTarea.destroy();
                        }
                    }
                ]
        });
    var winAsignarTarea = Ext.create('Ext.window.Window',
        {
            title: 'Asignar Tarea',
            modal: true,
            width: 480,
            closable: true,
            layout: 'fit',
            items: [formPanelTareas]
        }).show();
        
    elWinCrearTarea = winAsignarTarea.getEl();
    elWinCrearTarea.mask('Cargando...');
}

function setearCombo4(tipo)
{

    if(tipo == "1")
    {        
        Ext.getCmp('combo_empleados').value = "";
        Ext.getCmp('combo_empleados').setRawValue("");
        cuadrillaAsignada = "S";
        var myData_message = storeEmpleados.getProxy().getReader().jsonData.myMetaData.message;
        var myData_boolSuccess = storeEmpleados.getProxy().getReader().jsonData.myMetaData.boolSuccess;
                
        if (myData_boolSuccess != "1")
        {
            Ext.Msg.alert('Mensaje ', myData_message);
            Ext.getCmp('combo_empleados').setDisabled(true); 
            Ext.getCmp('comboCuadrilla').setDisabled(true); 
            Ext.getCmp('comboCuadrilla').setValue("");
            Ext.getCmp('comboContratista').setDisabled(true);
            Ext.getCmp('comboContratista').setValue("");
        }
        else
        {
            if (storeEmpleados.getCount() <= 1 && myData_boolSuccess != "1") {
                Ext.Msg.alert('Mensaje ', "No existen empleados asignados para este departamento.");
                Ext.getCmp('combo_empleados').setDisabled(true);  
                Ext.getCmp('comboCuadrilla').setDisabled(true); 
                Ext.getCmp('comboCuadrilla').setValue("");
                Ext.getCmp('comboContratista').setDisabled(true);
                Ext.getCmp('comboContratista').setValue("");
            }
            else
            {
                Ext.getCmp('combo_empleados').setDisabled(false);
                Ext.getCmp('comboCuadrilla').setDisabled(true); 
                Ext.getCmp('comboCuadrilla').setValue("");
                Ext.getCmp('comboContratista').setDisabled(true);
                Ext.getCmp('comboContratista').setValue("");
                valorAsignacion = "empleado";  
            }
            
        }
    }
    else if (tipo == "2")
    {     
        myData_message = storeCuadrillas.getProxy().getReader().jsonData.myMetaData.message;
        myData_boolSuccess = storeCuadrillas.getProxy().getReader().jsonData.myMetaData.boolSuccess;                 

        if (myData_boolSuccess != "1")
        {
            Ext.Msg.alert('Alerta ', myData_message);
            Ext.getCmp('comboCuadrilla').setDisabled(true);
            Ext.getCmp('combo_empleados').setDisabled(true); 
            Ext.getCmp('combo_empleados').setValue("");
            Ext.getCmp('comboContratista').setDisabled(true);
        }                
        else
        {                
            Ext.getCmp('comboCuadrilla').setDisabled(false);
            Ext.getCmp('combo_empleados').setDisabled(true);
            Ext.getCmp('combo_empleados').setValue("");
            Ext.getCmp('comboContratista').setDisabled(true);
            Ext.getCmp('comboContratista').setValue("");
            valorAsignacion = "cuadrilla";
        }
    } 
    else
    {
        cuadrillaAsignada = "S";
        myData_message = storeContratista.getProxy().getReader().jsonData.myMetaData.message;
        myData_boolSuccess = storeContratista.getProxy().getReader().jsonData.myMetaData.boolSuccess;

        if (myData_boolSuccess != "1")
        {
            Ext.Msg.alert('Alerta ', myData_message);
            Ext.getCmp('comboCuadrilla').setDisabled(true);
            Ext.getCmp('comboCuadrilla').setValue("");
            Ext.getCmp('combo_empleados').setDisabled(true);
            Ext.getCmp('combo_empleados').setValue("");
            Ext.getCmp('comboContratista').setDisabled(true);
        }
        else
        {
            Ext.getCmp('comboCuadrilla').setDisabled(true);
            Ext.getCmp('comboCuadrilla').setValue("");
            Ext.getCmp('combo_empleados').setDisabled(true);
            Ext.getCmp('combo_empleados').setValue("");
            Ext.getCmp('comboContratista').setDisabled(false);
            valorAsignacion = "contratista";
        }
    }

    //Ext.getCmp('cbox_responder').setValue(false);
}

function editarCampo(rec)
{
    winAsignadoTarea = "";
    var formPanel2 = "";

    if (winAsignadoTarea)
    {
        cierraVentanaByIden(winAsignadoTarea);
        winAsignadoTarea = "";
    }

    if (!winAsignadoTarea)
    {
        btncancelar2 = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function () {
                cierraVentanaByIden(winAsignadoTarea);
            }
        });
        
        btnActualizar = Ext.create('Ext.Button', {
            text: 'Actualizar',
            cls: 'x-btn-rigth',
            handler: function () 
            {
                Ext.Ajax.request
                ({
                    url: url_editarInforme,
                    method: 'post',
                    timeout: 400000,
                    params: {
                        idEncuestaPregunta: rec.get('idEncuestaPregunta'),
                        respuesta: Ext.getCmp('idRespuesta').value
                        
                    },
                    success: function (response) 
                    {
                        var json = Ext.JSON.decode(response.responseText);     
                        Ext.Msg.alert('Mensaje ', json.mensaje);
                        cierraVentanaByIden(winAsignadoTarea);
                        storeSeguimientoTareasXcaso.load();

                }})
                //cierraVentanaByIden(winAsignadoTarea);
            }
        });

        formPanel2 = Ext.create('Ext.form.Panel', {
            bodyPadding: 5,
            waitMsgTarget: true,
            width: 700,
            height: 500,
            layout: 'fit',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 200,
                msgTarget: 'side'
            },
            items: [{
                    xtype: 'fieldset',
                    title: 'Información de Asignacion',
                    defaultType: 'textfield',
                    items: [
                        {
                            xtype: 'htmleditor',
                            fieldLabel: rec.get('pregunta'),
                            id: 'idRespuesta',
                            name: 'idRespuesta',
                            value: rec.get('respuesta'),
                            height: 450,
                            width: 850
                        }
                    ]
                }]
        });
        winAsignadoTarea = Ext.create('Ext.window.Window', {
            title: 'Editar',
            modal: true,
            width: 900,
            height: 600,
            resizable: false,
            layout: 'fit',
            closabled: false,
            items: [formPanel2],
            buttonAlign: 'center',
            buttons: [btncancelar2, btnActualizar]
        }).show();

        Ext.MessageBox.hide();
    }

}

    
function cierraVentanaByIden(winID)
{
    winID.close();
    winID.destroy();
} 

function verSeguimientoTareasXCaso(id_caso)
{
    var conn = new Ext.data.Connection({
      listeners: {
          'beforerequest': {
              fn: function (con, opt) {
                  Ext.get(document.body).mask('Loading...');
              },
              scope: this
          },
          'requestcomplete': {
              fn: function (con, res, opt) {
                  Ext.get(document.body).unmask();
              },
              scope: this
          },
          'requestexception': {
              fn: function (con, res, opt) {
                  Ext.get(document.body).unmask();
              },
              scope: this
          }
      }
    });
   
    btncancelar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
              winSeguimientoTareasXCaso.destroy();													
            }
    });
    
	storeSeguimientoTareasXcaso = new Ext.data.Store({ 
		total: 'total',
		autoLoad: true,
		proxy: {
			type: 'ajax',
			url : 'verSeguimientoTareasXCaso',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
			extraParams: {
				id_caso: id_caso			
			}
		},
		fields:
		[
		      {name:'tarea', mapping:'tarea'},
              {name:'estado', mapping:'estado'},
		      {name:'observacion', mapping:'observacion'},
		      {name:'departamento', mapping:'departamento'},
		      {name:'empleado', mapping:'empleado'},
		      {name:'fecha', mapping:'fecha'}					
		]
	});
	gridSeguimientoTareasXCaso = Ext.create('Ext.grid.Panel', {
		id:'gridSeguimientoTareasXCaso',
		store: storeSeguimientoTareasXcaso,		
		columnLines: true,
		columns: [
            {
                id: 'tarea',
                header: 'Tarea',
                dataIndex: 'tarea',
                width:350,
                sortable: true						 
            },
            {
                id: 'estado',
                header: 'Estado',
                dataIndex: 'estado',
                width:80,
                sortable: true						 
            },
            {
                id: 'observacion',
                header: 'Observacion',
                dataIndex: 'observacion',
                width:400,
                sortable: true						 
            },
            {
                id: 'empleado',
                header: 'Usr. Creacion',
                dataIndex: 'empleado',
                width:80,
                sortable: true						 
            },
            {
                id: 'departamento',
                header: 'Departamento',
                dataIndex: 'departamento',
                width:100,
                sortable: true						 
            },
            {
                id: 'fecha',
                header: 'Fecha Observacion',
                dataIndex: 'fecha',
                width:120,
                sortable: true						 
            }
        ],		
		width: 1010,
		height: 270,
		listeners:
            {
                itemdblclick: function( view, record, item, index, eventobj, obj ){
                    var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                    Ext.Msg.show({
                        title:'Copiar texto?',
                        msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                        buttons: Ext.Msg.OK,
                        icon: Ext.Msg.INFORMATION
                    });
                },
                viewready: function (grid) {
                    var view = grid.view;

                    // record the current cellIndex
                    grid.mon(view, {
                        uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                            grid.cellIndex = cellIndex;
                            grid.recordIndex = recordIndex;
                        }
                    });

                    grid.tip = Ext.create('Ext.tip.ToolTip', {
                        target: view.el,
                        delegate: '.x-grid-cell',
                        trackMouse: true,
                        renderTo: Ext.getBody(),
                        listeners: {
                            beforeshow: function updateTipBody(tip) {
                                if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                    header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                    tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                                }
                            }
                        }
                    });

                }                                    
            }
	});
    
	formPanelSeguimientoTareasXCaso = Ext.create('Ext.form.Panel', {
			bodyPadding: 5,
			waitMsgTarget: true,
			height: 370,
			width:700,
			layout: 'fit',
			fieldDefaults: {
				labelAlign: 'left',
				//labelWidth: 140,
				msgTarget: 'side'
			},

			items: [{
				xtype: 'fieldset',				
				defaultType: 'textfield',
				items: [					
					gridSeguimientoTareasXCaso
				]
			}]
		 });
	winSeguimientoTareasXCaso = Ext.create('Ext.window.Window', {
			title: 'Seguimiento Tareas del Caso',
			modal: true,
			width: 1060,
			height: 370,
			resizable: true,
			layout: 'fit',
			items: [formPanelSeguimientoTareasXCaso],
			buttonAlign: 'center',
			buttons:[btncancelar]
	}).show();       
}

/**
 * Funcion que permite visualizar la informacion de un caso o tarea en una ventana nueva
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0 11/08/2021
 * @param  strIdCasoTarea Numero del Caso o Tarea,
 * @param  strIdentificadorActividad Identifica si es un Caso o Tarea
 */
 function verInformacionActividad(strIdCasoTarea,strIdentificadorActividad)
 {     
    if (strIdentificadorActividad === 'C')
    {
        window.open('/soporte/info_caso/'+strIdCasoTarea+'/show', '_blank');
    } 
    else if  (strIdentificadorActividad === 'T') 
    {
        window.open('/soporte/call_activity/'+strIdCasoTarea+'/show', '_blank');
    }
 }
