Ext.onReady(function() { 
    Ext.define('RegionesList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'nombre_region', type:'string'},
            {name:'value_region', type:'string'}
        ]
    }); 
    
    storeRegiones = Ext.create('Ext.data.Store', {
        pageSize: 200,
        model: 'RegionesList',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : 'getRegionesVehiculos',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        }
    });
    
    combo_regiones = new Ext.form.ComboBox({
        id: 'cmb_region',
        name: 'cmb_region',
        fieldLabel: false,
        anchor: '100%',
        queryMode:'remote',
        width: 400,
        emptyText: 'Seleccione la región',
        store:storeRegiones,
        displayField: 'nombre_region',
        valueField: 'value_region',
        renderTo: 'combo_region'
    });
    combo_regiones.value="";
    combo_regiones.setRawValue("");

    //Consulta de filiales
    Ext.define('FilialesList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'oficina', type:'string'},
            {name:'id_oficina', type:'string'}
        ]
    }); 
    
    storeRegiones = Ext.create('Ext.data.Store', {
        pageSize: 200,
        model: 'FilialesList',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : strUrlGetOficinas,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        }
    });
    
    combo_filiales = new Ext.form.ComboBox({
        id: 'cmb_filial',
        name: 'cmb_filial',
        fieldLabel: false,
        anchor: '100%',
        queryMode:'remote',
        width: 300,
        emptyText: 'Seleccione la filial',
        store:storeRegiones,
        displayField: 'oficina',
        valueField: 'id_oficina',
        renderTo: 'combo_filial'
    });
    combo_filiales.value="";
    combo_filiales.setRawValue("");


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
            url : 'getPlanesMantenimiento',
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
        emptyText: 'N/A',
        store:storeProcesos,
        displayField: 'nombre_proceso',
        valueField: 'id_proceso',
        renderTo: 'combo_proceso',
        listeners: 
        {
            change: function(combo)
            {	
                if(isInteger(combo.getValue()))
                {
                    Ext.get('escogido_proceso_id').dom.value = combo.getValue();
                }
                else
                {
                    Ext.get('escogido_proceso_id').dom.value = "0";
                }
            }
        }
    });

    fechaInicioContrato =   Ext.create('Ext.form.Panel', {
                                renderTo: 'div_fe_inicio_contrato',
                                id: 'fe_inicio_contrato',
                                name:'fe_inicio_contrato',
                                width: 144,
                                frame:false,
                                bodyPadding: 0,
                                height:30,
                                border:0,
                                margin:0,
                                items: [{
                                        xtype: 'datefield',
                                        id: 'fe_inicio_contrato_value',
                                        name:'fe_inicio_contrato_value',
                                        editable: false,
                                        anchor: '100%',
                                        format: 'Y-m-d',
                                        value:new Date(),
                                        maxValue: new Date()  // limited to the current date or prior
                                }]
    });

    fechaFinContrato =   Ext.create('Ext.form.Panel', {
                            renderTo: 'div_fe_fin_contrato',
                            id: 'fe_fin_contrato',
                            name:'fe_fin_contrato',
                            width: 144,
                            frame:false,
                            bodyPadding: 0,
                            height:30,
                            border:0,
                            margin:0,
                            items: [{
                                    xtype: 'datefield',
                                    id: 'fe_fin_contrato_value',
                                    name:'fe_fin_contrato_value',
                                    editable: false,
                                    anchor: '100%',
                                    format: 'Y-m-d',
                                    value:new Date(),
                                    maxValue: new Date()  // limited to the current date or prior
        }]
    });

    //******** RADIOBUTTONS -- TIPO DE VEHICULO
    var tiposHtml = '<input type="radio" onchange="cambiarTipoDeVehiculo(this.value);" checked="" value="EMPRESA" '+
                    'name="tipoVehiculo">&nbsp;EMPRESA' +'&nbsp;&nbsp;'+
                    '<input type="radio" onchange="cambiarTipoDeVehiculo(this.value);" value="SUBCONTRATADO" '+
                    'name="tipoVehiculo">&nbsp;SUBCONTRATADO' + '&nbsp;&nbsp;'+ '';

    RadiosTiposVehiculos =  Ext.create('Ext.Component', {
        html: tiposHtml,
        width: 600,
        padding: 4,
        style: { color: '#000000' }
    });



    formRadioButtonsTipos = Ext.create('Ext.form.Panel', {
        renderTo: 'div_rb_tipo_vehiculo',
        id: 'tipo_vehiculo',
        name:'tipo_vehiculo',
        width: 600,
        frame:false,
        bodyPadding: 0,
        height:30,
        border:0,
        margin:0,
        items: [RadiosTiposVehiculos]
    });

});


function cambiarTipoDeVehiculo(tipo)
{
    if(tipo == "EMPRESA")
    {
        document.getElementById("tituloPorTipoVehiculo").innerHTML      = "Ficha T&eacute;cnica";
        document.getElementById('formularioContrato').style.display     = 'none';
        document.getElementById('formularioFichaTecnica').style.display = 'block';
    }
    else
    {
        document.getElementById("tituloPorTipoVehiculo").innerHTML      = "Pertenece a";
        document.getElementById('formularioFichaTecnica').style.display = 'none';
        document.getElementById('formularioContrato').style.display     = 'block';
    }
    document.getElementById("escogido_tipo_vehiculo").value=tipo;

    limpiarCampos(tipo);

}

function limpiarCampos(tipo)
{
    if(tipo == "EMPRESA")
    {
        document.getElementById("infocontratoextratype_contratista").value  = "";
        document.getElementById("infocontratoextratype_idcontratista").value= "";

    }
    else
    {
        Ext.getCmp('cmb_proceso').setValue(null);
        Ext.getCmp('cmb_region').setValue(null);
        Ext.getCmp('cmb_filial').setValue(null);
        document.getElementById("alertaKM").value       ="500";

    }
}

/**
 * Valida los campos necesarios del formulario
 * @version 1.0
 *
 * Para el modelo de motos no es necesario los valores de GPS, IMEI y CHIP
 * @author Jose Bedon Sanchez <jobedon@telconet.ec>
 * @version 1.1 27-10-2020
 *
 */
function validarFormulario()
{
    var esMonitoreado  = document.getElementById("esM").checked;
    var valorMonitoreo = esMonitoreado ? 'S':'N';
    Ext.get('escogido_es_monitoreado').dom.value=valorMonitoreo;    
    var value_filial    = Ext.getCmp('cmb_filial').value;
    var value_gps       = document.getElementById("gps").value;
    var value_imei      = document.getElementById("imei").value;
    var value_chip      = document.getElementById("chip").value;

    var strModeloSeleccionado = $("#modeloElementoId option:selected").text();
        strModeloSeleccionado = strModeloSeleccionado.trim();
        strModeloSeleccionado = strModeloSeleccionado.toLowerCase();
    var intPos                = strModeloSeleccionado.indexOf("moto"); 

    // Solo validar cuando sean diferente a Moto
    if (intPos !== 0) {
        if(Ext.isEmpty(value_gps))
        {
            Ext.Msg.alert("Alerta","No se ha ingresado el número GPS");
            return false;
        }
        if(Ext.isEmpty(value_imei))
        {
            Ext.Msg.alert("Alerta","No se ha ingresado el número IMEI");
            return false;
        }
        if(Ext.isEmpty(value_chip))
        {
            Ext.Msg.alert("Alerta","No se ha ingresado el número del CHIP");
            return false;
        }
    }
    else
    {
        if(!Ext.isEmpty(value_gps) && Ext.isEmpty(value_imei) && Ext.isEmpty(value_chip))
        {
            Ext.Msg.alert("Alerta","GPS Ingresado, debe ingresar EMEI y CHIP");
            return false;
        }
        if(Ext.isEmpty(value_imei) && !Ext.isEmpty(value_chip))
        {
            Ext.Msg.alert("Alerta","Chip ingresado, debe ingresar un IMEI");
            return false;
        }
        if(!Ext.isEmpty(value_imei) && Ext.isEmpty(value_chip))
        {
            Ext.Msg.alert("Alerta","IMEI ingresado, debe ingresar un Chip");
            return false;
        }
        if(Ext.isEmpty(value_gps) && (!Ext.isEmpty(value_imei) || !Ext.isEmpty(value_chip)))
        {
            Ext.Msg.alert("Alerta","CHIP e IMEI ingresados, debe ingresar GPS");
            return false;
        }
    }
    
    //validar filial
    if(!Ext.isEmpty(value_filial))
    {
        Ext.get('escogida_filial_value').dom.value=value_filial;
    }
    else
    {
        Ext.get('escogida_filial_value').dom.value = "";
        Ext.Msg.alert("Alerta","No se ha escogido la filial.");
        return false;
    }

    var tipoVehiculo=document.getElementById("escogido_tipo_vehiculo").value;
    if(tipoVehiculo=="EMPRESA")
    {
        var value_region    = Ext.getCmp('cmb_region').value;        
        if(value_region)
        {
            Ext.get('escogida_region_value').dom.value=value_region;
        }
        else
        {
            Ext.get('escogida_region_value').dom.value = "";
            Ext.Msg.alert("Alerta","No se ha escogido la región.");
            return false;
        }        
        
        var planMantenimiento = Ext.getCmp('cmb_proceso').getValue();
        if(planMantenimiento=="" || !planMantenimiento)
        {  
            planMantenimiento = 0; 
        }
        
        if(isInteger(planMantenimiento))
        {
            Ext.get('escogido_proceso_id').dom.value = planMantenimiento;

        }
        
        var alertaKM = document.getElementById("alertaKM").value;
        if(alertaKM=="")
        {
            Ext.Msg.alert("Alerta","Por favor introduzca el umbral(KM) para realizar las alertas.");
            return false;
        }
    }
    else if(tipoVehiculo=="SUBCONTRATADO")
    {
        var contratista = document.getElementById("infocontratoextratype_idcontratista").value;

        if(contratista=="" || !contratista){  contratista = 0; }
        if(contratista==0)
        {
            Ext.Msg.alert("Alerta","No se ha escogido el Contratista.");
            return false;
        }

        var fechaInicioContrato = document.getElementById("fecha_inicio_contrato");
        fechaInicioContrato.value =  Ext.getCmp('fe_inicio_contrato').getValues().fe_inicio_contrato_value;
        if(fechaInicioContrato.value==""){
            Ext.Msg.alert("Alerta","El campo Fecha de Inicio de Contrato es requerido.");
            return false;
        }

        var fechaFinContrato = document.getElementById("fecha_fin_contrato");
        fechaFinContrato.value =  Ext.getCmp('fe_fin_contrato').getValues().fe_fin_contrato_value;
        if(fechaFinContrato.value==""){
            Ext.Msg.alert("Alerta","El campo Fecha de Fin de Contrato es requerido.");
            return false;
        }

    }
    else
    {
        return false;
    }

    verificarPlacaExistente('guardar');
    return false;

}


function isInteger(n) {
    return (typeof n == 'number' && /^-?\d+$/.test(n+''));
}