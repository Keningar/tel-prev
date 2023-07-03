/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.define('Ext.data.modelGenerico', {
    extend: 'Ext.data.Model',
    constructor: function(options) {
        Ext.apply(this, options || {});
    },
    fields: [
        {name: 'id', mapping: 'id'},
        {name: 'descripcion', mapping: 'descripcion'}
    ]
});

Ext.define('Ext.data.modelMotivosNoPlanificacion', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return Ext.define(this.mName,
            {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id_motivo', mapping: 'id_motivo'},
                    {name: 'nombre_motivo', mapping: 'nombre_motivo'}
                ]
            });
    }
});

Ext.define('Ext.data.modelUltimaMilla', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return Ext.define(this.mName,
            {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idTipoMedio', mapping: 'idTipoMedio'},
                    {name: 'nombreTipoMedio', mapping: 'nombreTipoMedio'}
                ]
            });
    }
});

Ext.define('Ext.data.modelCoordinar2', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return Ext.define(this.mName,
            {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id_factibilidad', mapping: 'idDetalleSolicitud'},
                    {name: 'id_servicio', mapping: 'idServicio'},
                    {name: 'tipo_orden', mapping: 'tipoOrden'},
                    {name: 'id_punto', mapping: 'idPunto'},
                    {name: 'estado_punto', mapping: 'estadoPunto'},
                    {name: 'caja', mapping: 'caja'},
                    {name: 'tercerizadora', mapping: 'tercerizadora'},
                    {name: 'id_orden_trabajo', mapping: 'id_orden_trabajo'},
                    {name: 'cliente', mapping: 'nombreCliente'},
                    {name: 'descripcionSolicitud', mapping: 'descripcionSolicitud'},
                    {name: 'vendedor', mapping: 'nombreVendedor'},
                    {name: 'login2', mapping: 'login'},
                    {name: 'esRecontratacion', mapping: 'esRecontratacion'},
                    {name: 'producto', mapping: 'nombreProducto'},
                    {name: 'productoServicio', mapping: 'nombreProducto'},
                    {name: 'coordenadas', mapping: 'coordenadas'},
                    {name: 'direccion', mapping: 'direccion'},
                    {name: 'observacion', mapping: 'observacion'},
                    {name: 'telefonos', mapping: 'telefonos'},
                    {name: 'ciudad', mapping: 'nombreCanton'},
                    {name: 'jurisdiccion', mapping: 'nombreJurisdiccion'},
                    {name: 'nombreSector', mapping: 'nombreSector'},
                    {name: 'ultimaMilla', mapping: 'ultimaMilla'},
                    {name: 'strMetraje', mapping: 'strMetraje'},
                    {name: 'strPrefijoEmpresa', mapping: 'strPrefijoEmpresa'},
                    {name: 'feSolicitaPlanificacion', mapping: 'feSolicitaPlanificacion'},
                    {name: 'fePlanificada', mapping: 'fePlanificada'},
                    {name: 'HoraIniPlanificada', mapping: 'HoraIniPlanificada'},
                    {name: 'HoraFinPlanificada', mapping: 'HoraFinPlanificada'},
                    {name: 'latitud', mapping: 'latitud'},
                    {name: 'longitud', mapping: 'longitud'},
                    {name: 'strTipoEnlace', mapping: 'strTipoEnlace'},
                    {name: 'estado', mapping: 'estado'},
                    {name: 'tituloCoordinar', mapping: 'tituloCoordinar'},
                    {name: 'rutaCroquis', mapping: 'rutaCroquis'},
                    {name: 'nombreTecnico', mapping: 'nombreTecnico'},
                    {name: 'strTareas', mapping: 'strTareas'},
                    {name: 'intIdComunicacion', mapping: 'intIdComunicacion'},
                    {name: 'intIdDetalle', mapping: 'intIdDetalle'},
                    {name: 'strTareaEsHal', mapping: 'strTareaEsHal'},
                    {name: 'seguimiento', mapping: 'seguimiento'},
                    {name: 'pedidos', mapping: 'pedidos'},
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
                    {name: 'origenPlanificacion', mapping: 'origenPlanificacion'},
                    {name: 'tipo_esquema', mapping: 'tipo_esquema'},
                    {name: 'idIntWifiSim', mapping: 'idIntWifiSim'},
                    {name: 'action12', mapping: 'action12'},
                    {name: 'idIntCouSim', mapping: 'idIntCouSim'},
                    {name: 'arraySimultaneos', mapping: 'arraySimultaneos'},
                    {name: 'action13', mapping: 'action13'},
                    {name: 'action14', mapping: 'action14'},
                    {name: 'arraySimultaneos', mapping: 'arraySimultaneos'},
                    {name: 'muestraIngL2', mapping: 'muestraIngL2'},
                    {name: 'observacionOpcionPyl', mapping: 'observacionOpcionPyl'},
                    {name: 'observacionAdicional', mapping: 'observacionAdicional'},
                    {name: 'strTipoRed', mapping: 'strTipoRed' }
                ]
            });
    }
});
