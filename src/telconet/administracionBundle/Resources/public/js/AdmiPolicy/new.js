Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
	
Ext.onReady(function() { 
    
    if(errorMsg != '')
    {
        Ext.Msg.alert('Error ', errorMsg);
    }
    
    
    Ext.tip.QuickTipManager.init();        
    
    Ext.define('DnsServers', { 	
        extend: 'Ext.data.Model', 	
        fields: [
            {name:'dns', type:'string'}         
        ]
    });        

    var store = Ext.create('Ext.data.Store', {              
        model: 'DnsServers',       
    });
    
    selModelSintomas = Ext.create('Ext.selection.CheckboxModel', {
	   listeners: {
			selectionchange: function(sm, selections) {
				gridDnsServers.down('#removeButton').setDisabled(selections.length == 0);
			}
		}
	});
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {clicksToEdit: 1});
       
    gridDnsServers = Ext.create('Ext.grid.Panel', 
        {
            id: 'gridDnsServers',            
            store:store,
            plugins: [cellEditing],
            viewConfig: {enableTextSelection: true, stripeRows: true},
            columnLines: true,
            columns: [
                {
                    id: 'dns',
                    header: 'Dns Server',
                    dataIndex: 'dns',
                    width: 350,
                    sortable: true,                    
                    editor: {       
                        id:'txtDns',
                        width: 350,
                        xtype: 'textfield',  
                        allowBlank: false,
                        blankText: 'Campo Obligatorio.',
                        enableKeyEvents: true,
                        emptyText: '',
                        listClass: 'x-combo-list-small',
                        maskRe: /[0-9.]/                        
                    }			
			}],
			selModel: selModelSintomas,			
			dockedItems: [{
				xtype: 'toolbar',
				items: [{
					itemId: 'removeButton',
					text:'Eliminar',
					tooltip:'Elimina el item seleccionado',
					disabled: true,
					handler : function(){eliminarSeleccion(gridDnsServers);}
				}, '-', {
					text:'Agregar',
					tooltip:'Agrega un item a la lista',
					handler : function(){																		
							var r = Ext.create('DnsServers', {
								dns: ''								
							});
							store.insert(0, r);	                            
					}
				}]
			}],
			width: 400,
			height:  200,
			renderTo: 'dns_servers',
			frame: true,
			title: 'Agregar Dns Servers',
		});
                  
    
});



function validarFormulario() 
{
    nombrePolicy = document.getElementById("telconet_schemabundle_admipolicytype_nombrePolicy").value;        
    leaseTime    = document.getElementById("telconet_schemabundle_admipolicytype_leaseTime").value;  
    mascara      = document.getElementById("telconet_schemabundle_admipolicytype_mascara").value;  
    dnsName      = document.getElementById("telconet_schemabundle_admipolicytype_dnsName").value;  
    elemento     = document.getElementById("telconet_schemabundle_admipolicytype_elementoId").value;          
    elemento     = document.getElementById("telconet_schemabundle_admipolicytype_gateway").value;  
    gateway    = document.getElementById("seEjecuta").value;         
        
    if(seEjecuta == 'Seleccione')
    {
        Ext.Msg.alert('Alerta ', 'Debe Seleccionar si se ejecuta o no');
        return false;
    }
    
    if (nombrePolicy === '') 
    {
        Ext.Msg.alert('Alerta ', 'Debe ingresar el nombre del Policy');
        return false;
    }
    if ( (elemento === '' || elemento === 'Seleccione') && seEjecuta == 'SI') 
    {
        Ext.Msg.alert('Alerta ', 'Debe seleccionar el elemento de relacion');
        return false;
    }
    if(leaseTime === '')
    {
        Ext.Msg.alert('Alerta ', 'Debe ingresar el lease Time del Policy');
        return false;
    }
    if(mascara === '')
    {
        Ext.Msg.alert('Alerta ', 'Debe ingresar la mascara del Policy');
        return false;
    }
    if(gateway === '')
    {
        Ext.Msg.alert('Alerta ', 'Debe ingresar el Gateway Policy');
        return false;
    }
    if(dnsName === '')
    {
        Ext.Msg.alert('Alerta ', 'Debe ingresar el DNS Name del Policy');
        return false;
    }
    
    dnsServers = obtenerInformacionGrid();        
    
    if(dnsServers)
    {        
        document.getElementById("dns-servers").value = dnsServers.substring(0, dnsServers.length-1);;
    }
    else return false;       
    
    return true;
}


function eliminarSeleccion(datosSelect) 	
{
    for (var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
    {
        datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]); 	
    }
}

function obtenerInformacionGrid()
{    
    var grid = gridDnsServers;

    var dnsServers = '';

    if (grid.getStore().getCount() !== 0)
    {
        for (var i = 0; i < grid.getStore().getCount(); i++) 	
        { 
            dns = grid.getStore().getAt(i).data.dns;
            if(dns === '')
            {
                 Ext.Msg.alert("Advertencia", "No puede ingresar DNS vacios");
                return false;
            }
            else
            {
                dnsServers += grid.getStore().getAt(i).data.dns + "|";                
            }
        }
    }
    else
    {
        Ext.Msg.alert("Advertencia", "No ha ingresado los DNS Servers del policy");
        return false;
    }
    
    return dnsServers;
}

function isNumeric(event)  	
{
    e = event.charCode; 
    c = event.keyCode;          

    if( (e > 47 && e < 60) || c === 190 || c === 35 || c === 36 || e === 46 || c === 8 || e === 32 )
    {
        return true;
    }
    else
    {        
        return false;
    }
}

