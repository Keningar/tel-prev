/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


modelGenerico = Ext.create('Ext.data.modelGenerico', {mName: 'modelGenerico'});
Ext.define('Ext.data.storeGenerico', {
    extend: 'Ext.data.Store',
    mName: '',
    mUrl: '',
    total: 'total',
    proxy: {
        type: 'proxyGenerico',
    },
    model: modelGenerico,
    autoLoad: true,
    constructor: function(options) {
        Ext.apply(this, options || {});
        //this.superclass.constructor.apply(this,new Array(options));
    }
});

modelSectores = Ext.create('Ext.data.modelSectores', {mName: 'modelSectores'});
Ext.define('Ext.data.storeSectores', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return new Ext.data.Store({
            total: 'total',
            proxy: {
                type: 'ajax',
                url: ajaxGetSectores,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    nombre: '',
                    estado: 'ACTIVE'
                }
            },
            model: modelSectores,
            autoLoad: true
        });
    }
});

modelEstadoPunto = Ext.create('Ext.data.modelEstadoPunto', {mName: 'modelEstadoPunto'});
Ext.define('Ext.data.storeEstadoPunto', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return new Ext.data.Store({
            total: 'total',
            proxy: {
                type: 'ajax',
                url: strUrlEstadoPuntos,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    nombre: '',
                    estado: 'ACTIVE'
                }
            },
            model: modelEstadoPunto,
            autoLoad: true
        });
    }
});

modelMotivosRechazo = Ext.create('Ext.data.modelMotivos', {mName: 'modelMotivos'});
Ext.define('Ext.data.storeMotivosRechazo', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return new Ext.data.Store({
            total: 'total',
            proxy: {
                type: 'ajax',
                url: strUrlMotivosRechazo,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    nombre: '',
                    estado: 'ACTIVE'
                }
            },
            model: modelMotivosRechazo,
            autoLoad: true
        });
    }
});

modelMotivosAnulacion = Ext.create('Ext.data.modelMotivos', {mName: 'modelMotivos'});
Ext.define('Ext.data.storeMotivosAnulacion', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return new Ext.data.Store({
            total: 'total',
            proxy: {
                type: 'ajax',
                url: strUrlMotivosAnulacion,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    nombre: '',
                    estado: 'ACTIVE'
                }
            },
            model: modelMotivosAnulacion,
            autoLoad: true
        });
    }
});

modelMotivosRePlanificacion = Ext.create('Ext.data.modelMotivosRePlanificacion', {mName: 'modelMotivosRePlanificacion'});
Ext.define('Ext.data.storeMotivosRePlanificacion', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return new Ext.data.Store({
            total: 'total',
            proxy: {
                type: 'ajax',
                url: strUrlMotivosRePlanificacion,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    nombre: '',
                    estado: 'ACTIVE'
                }
            },
            model: modelMotivosRePlanificacion,
            autoLoad: true
        });
    }
});

modelMotivosDetenido = Ext.create('Ext.data.modelMotivos', {mName: 'modelMotivos'});
Ext.define('Ext.data.storeMotivosDetenido', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return new Ext.data.Store({
            total: 'total',
            proxy: {
                type: 'ajax',
                url: strUrlMotivosDetenido,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    nombre: '',
                    estado: 'ACTIVE'
                }
            },
            model: modelMotivosDetenido,
            autoLoad: true
        });
    }
});

    Ext.define('Ext.data.storeMotivosReplanificarInspeccion', {
        mName: '',
        constructor: function(options) {
            Ext.apply(this, options || {});
            return new Ext.data.Store({
                total: 'total',
                proxy: {
                    type: 'ajax',
                    url: strUrlMotivosGestionarInspeccion,
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        nombre: '',
                        estado: 'ACTIVE',
                        mod: 'coordinar',
                        acc: 'getMotivosReplanificarInspeccion'
                    }
                },
                model: modelMotivosRePlanificacion,
                autoLoad: true
            });
        }
    });


    Ext.define('Ext.data.storeMotivosDetenerInspeccion', {
        mName: '',
        constructor: function(options) {
            Ext.apply(this, options || {});
            return new Ext.data.Store({
                total: 'total',
                proxy: {
                    type: 'ajax',
                    url: strUrlMotivosGestionarInspeccion,
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        nombre: '',
                        estado: 'ACTIVE',
                        mod: 'coordinar',
                        acc: 'getMotivosDetenerInspeccion'
                    }
                },
                model: modelMotivosRePlanificacion,
                autoLoad: true
            });
        }
    });

    Ext.define('Ext.data.storeMotivosRechazarInspeccion', {
        mName: '',
        constructor: function(options) {
            Ext.apply(this, options || {});
            return new Ext.data.Store({
                total: 'total',
                proxy: {
                    type: 'ajax',
                    url: strUrlMotivosGestionarInspeccion,
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        nombre: '',
                        estado: 'ACTIVE',
                        mod: 'coordinar',
                        acc: 'getMotivosRechazarInspeccion'
                    }
                },
                model: modelMotivosRePlanificacion,
                autoLoad: true
            });
        }
    });
    Ext.define('Ext.data.storeMotivosAnularInspeccion', {
        mName: '',
        constructor: function(options) {
            Ext.apply(this, options || {});
            return new Ext.data.Store({
                total: 'total',
                proxy: {
                    type: 'ajax',
                    url: strUrlMotivosGestionarInspeccion,
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        nombre: '',
                        estado: 'ACTIVE',
                        mod: 'coordinar',
                        acc: 'getMotivosAnularInspeccion'
                    }
                },
                model: modelMotivosRePlanificacion,
                autoLoad: true
            });
        }
    });
    Ext.define('Ext.data.storeMotivosRechazarSolInspeccion', {
        mName: '',
        constructor: function(options) {
            Ext.apply(this, options || {});
            return new Ext.data.Store({
                total: 'total',
                proxy: {
                    type: 'ajax',
                    url: strUrlMotivosGestionarInspeccion,
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        nombre: '',
                        estado: 'ACTIVE',
                        mod: 'coordinar',
                        acc: 'getMotivosRechazarSolicitudInspeccion'
                    }
                },
                model: modelMotivosRePlanificacion,
                autoLoad: true
            });
        }
    });
modelTipoSolicitud = Ext.create('Ext.data.modelTipoSolicitud', {mName: 'modelTipoSolicitud'});
Ext.define('Ext.data.storeTipoSolicitud', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return new Ext.data.Store({
            total: 'total',
            proxy: {
                type: 'ajax',
                url: strUrlTipoSolicitudd,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    nombre: '',
                    estado: 'ACTIVE'
                }
            },
            model: modelTipoSolicitud,
            autoLoad: true
        });
    }
});

modelUltimaMilla = Ext.create('Ext.data.modelUltimaMilla', {mName: 'modelUltimaMilla'});
Ext.define('Ext.data.storeUltimaMilla', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return new Ext.data.Store({
            total: 'total',
            proxy: {
                type: 'ajax',
                url: strUrlUltimaMillaCoordinar,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            model: modelUltimaMilla,
            autoLoad: true
        });
    }
});

/*Ext.define('Ext.data.storeCoordinar',{
 mName : '',
 total: 'total',
 model: Ext.create('Ext.data.modelCoordinar',{mName:'modelCoordinar'}),
 autoLoad: true,
 proxy: {
 type: 'ajax',
 url : strUrlGrid,
 timeout: 120000,
 reader: {
 type: 'json',
 totalProperty: 'total',
 root: 'encontrados'
 },
 extraParams: {
 fechaDesdePlanif: '',
 fechaHastaPlanif: '',
 fechaDesdeIngOrd: '',
 fechaHastaIngOrd: '',
 login: '',
 descripcionPunto: '',
 vendedor: '',
 ciudad: '',
 numOrdenServicio: '',
 ultimaMilla: '',
 estado: 'Todos'
 }
 },
 initComponent: function() {
 var me = this;
 me.callParent();
 },
 constructor	: function(options){
 Ext.apply(this,options || {});
 this.superclass.constructor.apply(this,new Array(options));
 }

 });*/
