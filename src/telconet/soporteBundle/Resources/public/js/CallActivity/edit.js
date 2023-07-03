/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function validarFormulario(){

    fecha = document.getElementById("fecha_apertura");
    fecha.value =  Ext.getCmp('fe_apertura').getValues().fe_apertura_value;
    if(fecha.value==""){
        Ext.Msg.alert("Alerta","El campo fecha de apertura es requerido.");
        return false;
    }
    hora = document.getElementById("hora_apertura");
    hora.value =  Ext.getCmp('ho_apertura').getValues().ho_apertura_value;
    if(hora.value==""){
        Ext.Msg.alert("Alerta","El campo hora de apertura es requerido.");
        return false;
    }
    if(comboCliente.getRawValue()=="")
    {
        Ext.Msg.alert("Alerta","Debe escoger un cliente.");
        return false;   
    }
    document.getElementById("cliente").value=comboCliente.getValue();
    
    if(document.getElementById("telconet_schemabundle_callactivitytype_tipo").value=="")
    {
        Ext.Msg.alert("Alerta","Debe escoger un tipo.");
        return false;   
    }
    if(document.getElementById("telconet_schemabundle_callactivitytype_claseDocumento").value=="")
    {
        Ext.Msg.alert("Alerta","Debe escoger un detalle.");
        return false;   
    }
    
    if(Ext.getCmp("comboCliente").getRawValue()==document.getElementById("clienteNombre").value)
        document.getElementById("cliente").value=document.getElementById("clienteId").value;
        
    return true;
}


Ext.onReady(function(){   
    
    fecha = Ext.create('Ext.form.Panel', {
        renderTo: 'div_fe_apertura',
        id: 'fe_apertura',
        name:'fe_apertura',
        width: 144,
        frame:false,
        bodyPadding: 0,
        height:30,
        border:0,
        margin:0,
        items: [{
            xtype: 'datefield',
            id: 'fe_apertura_value',
            name:'fe_apertura_value',
            editable: false,
            anchor: '100%',
            format: 'Y-m-d',
            value:document.getElementById("fecha_apertura").value,
            maxValue: new Date()  // limited to the current date or prior
        }]
    });
    hora = Ext.create('Ext.form.Panel', {
        width: 144,        
        frame:false,
        height:30,
        id: 'ho_apertura',
        name:'ho_apertura',
        border:0,
        margin:0,
        renderTo: 'div_hora_apertura',
        items: [{
            xtype: 'timefield',
            format: 'H:i',
            id: 'ho_apertura_value',
            name: 'ho_apertura_value',
            minValue: '00:01 AM',
            maxValue: '23:59 PM',
            increment: 1,
            value:document.getElementById("hora_apertura").value,
            anchor: '100%'
        }]
    });
    comboClienteStore = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '../getClientes',
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
                {name:'id_cliente', mapping:'id_cliente'},
                {name:'cliente', mapping:'cliente'}
              ],
        listeners: {
            load: function(sender, node, records) {
                
                if(!Ext.getCmp("comboCliente")){
                    comboCliente = Ext.create('Ext.form.ComboBox', {
						id:'comboCliente',
						store: comboClienteStore,
						displayField: 'cliente',
						valueField: 'id_cliente',
						height:30,
						border:0,
						margin:0,
						fieldLabel: false,	
						queryMode: "remote",
						emptyText: '',
						renderTo: 'cliente_combo'
					});

                    if(comboClienteStore.count()==1)
                        comboCliente.select(comboCliente.getStore().data.items[0]);
                    
					document.getElementById("telconet_schemabundle_callactivitytype_tipo").value = document.getElementById("formaContacto").value;
                    document.getElementById("telconet_schemabundle_callactivitytype_claseDocumento").value = document.getElementById("detalle").value;
                    document.getElementById("telconet_schemabundle_callactivitytype_observacion").value = document.getElementById("observacion").value;
                    
                        
                    
                    Ext.getCmp("comboCliente").setRawValue(document.getElementById("clienteNombre").value);
                    
                }
                
            }
        }
    });
    

});
