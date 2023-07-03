/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var esSwitchVirtual    = false;
var boolEsGpon         = false;

Ext.onReady(function(){

    $('#panel_reservar_vlan').empty();
    if($('#tipoRed').val()===undefined || $('#tipoRed').val() === 'MPLS')
    {
        getMpls();
    }
    else
    {
        getGpon();
    }
    /**
     * Documentación para jquery tipo de red.
     *
     * Función que muestra el formulario de reservar vlans para olt o para switch.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 18-11-2019
     */
    $("#tipoRed" ).change(function() 
    {
        if($('#tipoRed').val() === 'MPLS')
        {
            $('#panel_reservar_vlan').empty();
            getMpls();
        }
        else
        {
            $('#panel_reservar_vlan').empty();
            getGpon();
        }
    });
});
    /**
     * Documentación para la función 'validate'.
     *
     * Función que válida la información ingresada en el formulario de reservar vlans.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 18-11-2019
     */
    function validate()
    {
        if(!Ext.getCmp('vlan_sugerida').isValid())
        {
            var strMensaje = boolEsGpon ? 'permitido':'del anillo';
            Ext.Msg.alert('Error ','Por favor ingrese un número dentro del rango '+strMensaje+' a reservar.');
            return false;
        }

        if(!$.isNumeric(Ext.getCmp('numero_anillo').getValue()) && !esSwitchVirtual && !boolEsGpon)
        {
            Ext.Msg.alert('Error ','No existen datos de Anillo para poder realizar la reserva');
            return false;
        }
        Ext.MessageBox.wait('Guardando datos...');
    }
    /**
     * Documentación para la función 'getMpls'.
     *
     * Función que muestra el formulario de reservar vlans para switch.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 18-11-2019
     */
    function getMpls()
    {
        boolEsGpon           = false;
        storeElementosSwitch = new Ext.data.Store({
            pageSize: 10000,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: urlAjaxGetElementos,
                timeout: 120000,
                reader: {
                    type: 'json',
                    root: 'data'
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                },
                extraParams: {
                    tipo: 'SWITCH'
                }
            },
            fields:
                    [
                        {name: 'id_elemento', mapping: 'id_elemento'},
                        {name: 'nombre_elemento', mapping: 'nombre_elemento'}
                    ]
        });
        
        var comboSwitch = new Ext.form.ComboBox({
            name: 'cmb_switch',
            fieldLabel: "Switch",
            queryMode:'local',
            width: 400,
            emptyText: 'Seleccione',
            store:storeElementosSwitch,
            displayField: 'nombre_elemento',
            valueField: 'id_elemento',
            listeners: {
                select: function(combo){
                    Ext.Ajax.request({
                        url: urlAjaxGetInfoBackboneByElemento,
                        method: 'post',
                        params: { 
                                    idElemento : combo.getValue(),
                                    tipoElementoPadre : 'ROUTER'
                                },
                        success: function(response){
                            if(response.responseText == "Parámetros no definidos." )
                            {
                                Ext.Msg.alert('Error ','WS no devuelve valores completos. <br> Revisar Parámetros de configuración');
                            }
                            else if(response.responseText == "No Existe Conectividad con el WS Networking." )
                            {
                                Ext.Msg.alert('Error ', response.responseText);
                            }
                            else
                            {
                                var json = Ext.decode(response.responseText);
                                
                                Ext.getCmp('id_elemento').setValue(json.idElementoPadre);
                                Ext.getCmp('nombre_elemento').setValue(json.nombreElementoPadre);
                                Ext.getCmp('numero_anillo').setValue(json.anillo);
                                Ext.getCmp('rango_anillo').setValue(json.min + "-" + json.max);
                                Ext.getCmp('vlan_sugerida').setMinValue(json.min);
                                Ext.getCmp('vlan_sugerida').setMaxValue(json.max);
                                
                                if(json.esVirtual === 'SI')
                                {
                                    esSwitchVirtual = true;
                                }

                                if(json.esClienteMapeo === "S")
                                {
                                    Ext.getCmp('vlan_sugerida').setVisible(false);
                                    Ext.getCmp('comboVlan').setVisible(true);
                                    Ext.getCmp('bandMapeoCliente').setValue("S");
                                }
                                else
                                {
                                    Ext.getCmp('vlan_sugerida').setVisible(true);
                                    Ext.getCmp('comboVlan').setVisible(false);
                                    Ext.getCmp('bandMapeoCliente').setValue("N");
                                }
                            }
                        },
                        failure: function(result)
                        {
                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                        }
                    });
                }
            }
        });
        
        Ext.create('Ext.form.Panel', {
            renderTo: 'panel_reservar_vlan',
            width: 500,
            bodyPadding: 7,
            border: false,
            layout: {
                type: 'vbox',
            },
            bodyStyle: {
                background: '#fff'
            },
            title: '',
            items: [
                comboSwitch,
                
                {
                    xtype: 'textfield',
                    id: 'id_elemento',
                    name: 'id_elemento',
                    hidden: true,
                    readOnly: true
                },
                {
                    xtype: 'textfield',
                    id: 'nombre_elemento',
                    name: 'nombre_elemento',
                    fieldLabel: 'Pe',
                    value: '',
                    readOnly: true,
                    width: 350
                },
                
                {
                    xtype: 'textfield',
                    id: 'numero_anillo',
                    name: 'numero_anillo',
                    fieldLabel: 'Anillo #',
                    readOnly: true,
                    value: '',
                    width: 200
                },
                {
                    xtype: 'textfield',
                    id: 'rango_anillo',
                    name: 'rango_anillo',
                    fieldLabel: 'Rango',
                    readOnly: true,
                    value: '',
                    width: 300
                },
                {
                    xtype: 'numberfield',
                    id: 'vlan_sugerida',
                    name: 'vlan_sugerida',
                    fieldLabel: 'Sugerir Vlan',
                    value: '',
                    width: 200,
                },
                {
                    xtype: 'textfield',
                    id: 'bandMapeoCliente',
                    name: 'bandMapeoCliente',
                    fieldLabel: 'Bandera',
                    readOnly: true,
                    value: '',
                    width: 200,
                    hidden: true,
                },
                {
                    xtype: 'combobox',
                    fieldLabel: 'Seleccionar Vlan',
                    id: 'comboVlan',
                    width: 200,
                    name: 'comboVlan',
                    hidden: true,
                    store: [
                        ['42', '42'],
                        ['43', '43'],
                        ['44', '44'],
                        ['45', '45'],
                        ['46', '46'],
                        ['47', '47'],
                        ['48', '48'],
                        ['49', '49'],
                        ['50', '50']
                        ],
                    displayField: 'id_vlan',
                    valueField: 'nombre_vlan',
                }
            ]
        });
    }
    /**
     * Documentación para la función 'getGpon'.
     *
     * Función que muestra el formulario de reservar vlans para olt.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 18-11-2019
     */
    function getGpon()
    {
        boolEsGpon        = true;
        storeElementosOlt = new Ext.data.Store({
            pageSize: 10000,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: urlAjaxGetElementosOltGpon,
                timeout: 120000,
                reader: {
                    type: 'json',
                    root: 'data'
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                }
            },
            fields:
                    [
                        {name: 'id_elemento', mapping: 'id_elemento'},
                        {name: 'nombre_elemento', mapping: 'nombre_elemento'}
                    ]
        });

        var comboOlt = new Ext.form.ComboBox({
            name: 'cmb_olt',
            fieldLabel: "Olt",
            queryMode:'local',
            width: 400,
            emptyText: 'Seleccione',
            store:storeElementosOlt,
            displayField: 'nombre_elemento',
            valueField: 'id_elemento',
            listeners: {
                select: function(combo){
                    Ext.Ajax.request({
                        url: urlAjaxGetInfoBackboneByElemento,
                        method: 'post',
                        params: { 
                                    idElemento : combo.getValue(),
                                    tipoElementoPadre : 'OLT'
                                },
                        success: function(response){
                            if(response.responseText == "Parámetros no definidos." )
                            {
                                Ext.Msg.alert('Error ','WS no devuelve valores completos. <br> Revisar Parámetros de configuración');
                            }
                            else if(response.responseText == "No Existe Conectividad con el WS Networking." )
                            {
                                Ext.Msg.alert('Error ', response.responseText);
                            }
                            else if(response.responseText == "Error al reservar Vlan")
                            {
                                Ext.Msg.alert('Error ', 'Existieron problemas al reservar Vlan, por favor comuníquese con el departamento de sistemas.');
                            }
                            else
                            {
                                var json = Ext.decode(response.responseText);

                                Ext.getCmp('id_elemento').setValue(json.idElementoPadre);
                                Ext.getCmp('nombre_elemento').setValue(json.nombreElementoPadre);
                                Ext.getCmp('rango_anillo').setValue(json.min + "-" + json.max);
                                Ext.getCmp('vlan_sugerida').setMinValue(json.min);
                                Ext.getCmp('vlan_sugerida').setMaxValue(json.max);
                                Ext.getCmp('vlan_sugerida').setVisible(true);
                                Ext.getCmp('bandMapeoCliente').setValue("N");
                            }
                        },
                        failure: function(result)
                        {
                            Ext.Msg.alert('Error ','Error2: ' + result.statusText);
                        }
                    });
                }
            }
        });

        Ext.create('Ext.form.Panel', {
            renderTo: 'panel_reservar_vlan',
            width: 500,
            bodyPadding: 7,
            border: false,
            layout: {
                type: 'vbox',
            },
            bodyStyle: {
                background: '#fff'
            },
            title: '',
            items: [
                comboOlt,
                
                {
                    xtype: 'textfield',
                    id: 'id_elemento',
                    name: 'id_elemento',
                    hidden: true,
                    readOnly: true
                },
                {
                    xtype: 'textfield',
                    id: 'nombre_elemento',
                    name: 'nombre_elemento',
                    fieldLabel: 'Pe',
                    value: '',
                    readOnly: true,
                    width: 350
                },
                {
                    xtype: 'textfield',
                    id: 'numero_anillo',
                    name: 'numero_anillo',
                    fieldLabel: 'Anillo #',
                    hidden: true,
                    readOnly: true,
                    value: '',
                    width: 200
                },
                {
                    xtype: 'textfield',
                    id: 'rango_anillo',
                    name: 'rango_anillo',
                    fieldLabel: 'Rango',
                    readOnly: true,
                    value: '',
                    width: 300
                },
                {
                    xtype: 'numberfield',
                    id: 'vlan_sugerida',
                    name: 'vlan_sugerida',
                    fieldLabel: 'Sugerir Vlan',
                    value: '',
                    width: 200,
                },
                {
                    xtype: 'textfield',
                    id: 'bandMapeoCliente',
                    name: 'bandMapeoCliente',
                    fieldLabel: 'Bandera',
                    readOnly: true,
                    value: '',
                    width: 200,
                    hidden: true,
                }
            ]
        });
    }