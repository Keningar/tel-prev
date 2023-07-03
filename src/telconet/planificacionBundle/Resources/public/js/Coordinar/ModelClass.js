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

Ext.define('Ext.data.modelSectores', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return Ext.define(this.mName,
            {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id_sector', mapping: 'id_sector'},
                    {name: 'nombre_sector', mapping: 'nombre_sector'}
                ]
            });
    }
});

Ext.define('Ext.data.modelCiudades', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return Ext.define(this.mName,
            {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id_canton', mapping: 'strValue'},
                    {name: 'nombre_canton', mapping: 'strNombre'}
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

Ext.define('Ext.data.modelAsignarPlanificacion', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return Ext.define(this.mName,
            {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'ultimaMilla', mapping: 'ultimaMilla'},
                    {name: 'pop', mapping: 'pop'},
                    {name: 'dslam', mapping: 'dslam'},
                    {name: 'elementoId', mapping: 'elementoId'},
                    {name: 'id_factibilidad', mapping: 'id_factibilidad'},
                    {name: 'id_servicio', mapping: 'id_servicio'},
                    {name: 'id_punto', mapping: 'id_punto'},
                    {name: 'traslado', mapping: 'traslado'},
                    {name: 'tipo_orden', mapping: 'tipo_orden'},
                    {name: 'cliente', mapping: 'cliente'},
                    {name: 'esRecontratacion', mapping: 'esRecontratacion'},
                    {name: 'vendedor', mapping: 'vendedor'},
                    {name: 'login2', mapping: 'login2'},
                    {name: 'tercerizadora', mapping: 'tercerizadora'},
                    {name: 'descripcionSolicitud', mapping: 'descripcionSolicitud'},
                    {name: 'producto', mapping: 'producto'},
                    {name: 'coordenadas', mapping: 'coordenadas'},
                    {name: 'direccion', mapping: 'direccion'},
                    {name: 'ciudad', mapping: 'ciudad'},
                    {name: 'jurisdiccion', mapping: 'jurisdiccion'},
                    {name: 'nombreSector', mapping: 'nombreSector'},
                    {name: 'feSolicitaPlanificacion', mapping: 'feSolicitaPlanificacion'},
                    {name: 'fePlanificada', mapping: 'fePlanificada'},
                    {name: 'HoraIniPlanificada', mapping: 'HoraIniPlanificada'},
                    {name: 'HoraFinPlanificada', mapping: 'HoraFinPlanificada'},
                    {name: 'rutaCroquis', mapping: 'rutaCroquis'},
                    {name: 'latitud', mapping: 'latitud'},
                    {name: 'longitud', mapping: 'longitud'},
                    {name: 'strTipoEnlace', mapping: 'strTipoEnlace'},
                    {name: 'estado', mapping: 'estado'},
                    {name: 'caja', mapping: 'caja'},
                    {name: 'telefonos', mapping: 'telefonos'},
                    {name: 'observacion', mapping: 'observacion1'},
                    {name: 'action1', mapping: 'action1'},
                    {name: 'action2', mapping: 'action2'},
                    {name: 'action3', mapping: 'action3'},
                    {name: 'action4', mapping: 'action4'}
                ]
            });
    }
});

Ext.define('Ext.data.modelEstadoPunto', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return Ext.define(this.mName,
            {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'estado_punto_busqueda', mapping: 'estado_punto'},
                ]
            });
    }
});

Ext.define('Ext.data.modelMotivos', {
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

Ext.define('Ext.data.modelMotivosRePlanificacion', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return Ext.define(this.mName,
            {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id_motivo', mapping: 'intIdMotivo'},
                    {name: 'nombre_motivo', mapping: 'strMotivo'}
                ]
            });
    }
});

Ext.define('Ext.data.modelTipoSolicitud', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return Ext.define(this.mName,
            {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id_tipo_solicitud', mapping: 'id_tipo_solicitud'},
                    {name: 'tipo_solicitud', mapping: 'tipo_solicitud'}
                ]
            });
    }
});

Ext.define('Ext.data.modelCoordinar', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return Ext.define(this.mName,
            {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id_factibilidad', mapping: 'id_factibilidad'},
                    {name: 'id_servicio', mapping: 'id_servicio'},
                    {name: 'tipo_orden', mapping: 'tipo_orden'},
                    {name: 'id_punto', mapping: 'id_punto'},
                    {name: 'estado_punto', mapping: 'estado_punto'},
                    {name: 'caja', mapping: 'caja'},
                    {name: 'tercerizadora', mapping: 'tercerizadora'},
                    {name: 'id_orden_trabajo', mapping: 'id_orden_trabajo'},
                    {name: 'cliente', mapping: 'cliente'},
                    {name: 'descripcionSolicitud', mapping: 'descripcionSolicitud'},
                    {name: 'vendedor', mapping: 'vendedor'},
                    {name: 'login2', mapping: 'login2'},
                    {name: 'esRecontratacion', mapping: 'esRecontratacion'},
                    {name: 'producto', mapping: 'producto'},
                    {name: 'productoServicio', mapping: 'productoServicio'},
                    {name: 'coordenadas', mapping: 'coordenadas'},
                    {name: 'direccion', mapping: 'direccion'},
                    {name: 'observacion', mapping: 'observacion'},
                    {name: 'telefonos', mapping: 'telefonos'},
                    {name: 'ciudad', mapping: 'ciudad'},
                    {name: 'jurisdiccion', mapping: 'jurisdiccion'},
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
                    {name: 'action15', mapping: 'action15'},
                    {name: 'action16', mapping: 'action16'},
                    {name: 'arraySimultaneos', mapping: 'arraySimultaneos'},
                    {name: 'muestraIngL2', mapping: 'muestraIngL2'},
                    {name: 'observacionOpcionPyl', mapping: 'observacionOpcionPyl'},
                    {name: 'observacionAdicional', mapping: 'observacionAdicional'},
                    {name: 'strTipoRed', mapping: 'strTipoRed' },
                    {name: 'arrayPersonalizacionOpcionesGridCoordinar', mapping: 'arrayPersonalizacionOpcionesGridCoordinar'}
                ]
            });
    }
});
