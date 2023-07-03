--MIGRACION DE ARBOL DE TAREAS PARA DEPARTAMENTO TI
DECLARE

    type itemarray IS VARRAY(500) OF VARCHAR(1000);
    type departamentosarray IS VARRAY(500) OF itemarray;

    departamentos departamentosarray;
    ti1 itemarray;
    ti2 itemarray;

    items itemarray;

    tareanivel1 VARCHAR2(200);
    tareanivel2 VARCHAR2(200);
    tareanivel3 VARCHAR2(200);
    tareanivel4 VARCHAR2(200);
    tareanivel5 VARCHAR2(200);

    countn1 NUMBER;

    tareaCaracteristicaId NUMBER;

    idtareanueva NUMBER;
    idcaracrequirematerial NUMBER := 1311;
    transInsert NUMBER := 0;
    totalExiste NUMBER := 0;
    totalTareasNuevas NUMBER := 0;

    total integer;
    nombredepartamento      VARCHAR2(100);
    iddepartamento          VARCHAR2(100);
    idproceso               VARCHAR2(20);
    descripcionparametro    VARCHAR2(100) := 'CATEGORIAS DE LAS TAREAS';
    descripcionparametrocab VARCHAR2(100) := 'CATEGORIA_TAREA';

    imagen                  VARCHAR2(50);

    Le_Exception                EXCEPTION;
    Lv_MensajeError             VARCHAR2(4000);

    Lv_proceso_ont      VARCHAR2(100);
    Ln_proceso_ont      NUMBER;
BEGIN

    ti1 := itemarray(
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|REINICIAR ELEMENTOS DE SERVICIO DE RESPALDO|6055|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|CAPACITACION INTERNA|6044|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|REVISAR ESTADO DE LOS SERVICIOS EN SERVIDORES DE CORREO|6049|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|ARTICULO KB|6051|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|INVESTIGACION DE SOLUCIONES|6053|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|BRINDAR CAPACITACION|6043|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|DESARROLLO DE INFORMES|6052|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|PRUEBAS DE CONCEPTO|6054|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|REALIZAR DESPLIEGUE DE OVA/OVF DE CLIENTE|5929|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|REVISAR ESTADO SERVIDORES DE STORAGE|6061|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|REVISAR ESTADO SERVIDORES DE RESPALDO|6056|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|SOPORTE PROBLEMAS DE RENDIMIENTO DE CPU/MEM/DISK|6047|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|DESBLOQUEO DE IP EN CPANEL|6063|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|REVISAR ALERTAS ENCLOSURES|6045|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|REVISIÓN LOG SERVIDORES DE CORREO POR PROBLEMAS CON CUENTAS O SPAM.EJECUTAR ACCIONES|6050|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|REUNION CON CLIENTES/PROVEEDORES|6058|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|EDITAR LPAR’S|5953|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|BORRADO DE COLA DE CORREOS ZIMBRA|6048|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|REUNIONES INTERNAS|6057|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|REVISAR ALERTAS VCENTER|6046|',
            'EJECUCION DE TAREAS|TI - GESTION DE PROBLEMAS CLOUD|SOPORTE DE PROBLEMA DE RED|6062|',
            'PLANIFICACION DE RECURSOS|TI - PLANIFICACIÓN DE CAPACIDAD DC (CLOUD)|Revision reporte capacidad|6856|',
            'PLANIFICACION DE RECURSOS|TI - PLANIFICACIÓN DE CAPACIDAD DC (CLOUD)|Generar plan de implementacion de capacidad|6857|',
            'PLANIFICACION DE RECURSOS|TI - PLANIFICACIÓN DE CAPACIDAD DC (CLOUD)|Generar reporte de capacidad|6855|',
            'PLANIFICACIÓN DE TAREAS / MANTENIMIENTO|TI - PLANIFICACIÓN DE CAMBIOS DC (CLOUD)|Revision de ejecucion de tareas|6874|',
            'PLANIFICACIÓN DE TAREAS / MANTENIMIENTO|TI - PLANIFICACIÓN DE CAMBIOS DC (CLOUD)|Revisión de pre-requisitos|6859|',
            'PLANIFICACIÓN DE TAREAS / MANTENIMIENTO|TI - PLANIFICACIÓN DE CAMBIOS DC (CLOUD)|Realizar SMOP|6860|',
            'PLANIFICACIÓN DE TAREAS / MANTENIMIENTO|TI - PLANIFICACIÓN DE CAMBIOS DC (CLOUD)|Solicitud de recursos/materiales|6871|',
            'PLANIFICACIÓN DE TAREAS / MANTENIMIENTO|TI - PLANIFICACIÓN DE CAMBIOS DC (CLOUD)|Analisis de version a actualizar|6862|',
            'PLANIFICACIÓN DE TAREAS / MANTENIMIENTO|TI - PLANIFICACIÓN DE CAMBIOS DC (CLOUD)|Cambio de cronograma de mantenimiento|6858|',
            'PLANIFICACIÓN DE TAREAS / MANTENIMIENTO|TI - PLANIFICACIÓN DE CAMBIOS DC (CLOUD)|Revisar y aprobar SMOP|6867|',
            'PLANIFICACIÓN DE TAREAS / MANTENIMIENTO|TI - PLANIFICACIÓN DE CAMBIOS DC (CLOUD)|Confirmar ejecución de trabajos de mantenimientos|6866|',
            'PLANIFICACIÓN DE TAREAS / MANTENIMIENTO|TI - PLANIFICACIÓN DE CAMBIOS DC (CLOUD)|Planificaciones de tareas|6873|',
            'PLANIFICACIÓN DE TAREAS / MANTENIMIENTO|TI - PLANIFICACIÓN DE CAMBIOS DC (CLOUD)|Interactuar con el Proveedor o Fabricante|6870|',
            'PLANIFICACIÓN DE TAREAS / MANTENIMIENTO|TI - PLANIFICACIÓN DE CAMBIOS DC (CLOUD)|Generar actividades para proyecto|6872|',
            'PLANIFICACIÓN DE TAREAS / MANTENIMIENTO|TI - PLANIFICACIÓN DE CAMBIOS DC (CLOUD)|Coordinar trabajos de mantenimiento|6865|',
            'PLANIFICACIÓN DE TAREAS / MANTENIMIENTO|TI - PLANIFICACIÓN DE CAMBIOS DC (CLOUD)|Reporte de trabajos de mantenimiento correctivo|6869|',
            'PLANIFICACIÓN DE TAREAS / MANTENIMIENTO|TI - PLANIFICACIÓN DE CAMBIOS DC (CLOUD)|Revisar actividades de implementación de proyectos|6864|',
            'PLANIFICACIÓN DE TAREAS / MANTENIMIENTO|TI - PLANIFICACIÓN DE CAMBIOS DC (CLOUD)|Reporte de trabajos de mantenimiento preventivo|6868|',
            'PLANIFICACIÓN DE TAREAS / MANTENIMIENTO|TI - PLANIFICACIÓN DE CAMBIOS DC (CLOUD)|Realizar acciones para minimizar impacto|6861|',
            'PLANIFICACIÓN DE TAREAS / MANTENIMIENTO|TI - PLANIFICACIÓN DE CAMBIOS DC (CLOUD)|Generar el cronograma de mantenimiento|6863|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|OVM_Instalar Oracle VM Guest Additions|7028|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Instalacion de VMware-Tools en VMs|6975|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FCompute_Instalación de controladores de paravirtualización (tools)|7043|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|CF_Eliminar servicio de catálogo|7100|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FCompute_Migrar VM a otro host en caliente|7044|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Hyper-V_Verificación y reinicio de servicios en SCVMM|7025|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Telcodrive_Actualizar certificado SSL|7088|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Hyper-V_Migrar VM a otro host en caliente|7012|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|BL-HPE_Mantenimiento y reinicio de blades|7063|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|CF_Revisar estado de appliance|7103|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHEV/oVirt_Instalacion de VMware-Tools en VMs|6994|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FAccess_Modificar plantilla de VDI|7058|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FAccess_Desplegar VDI|7059|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Eliminar VM|6978|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Licenciamiento Microsoft|8038|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FCompute_Instalar servidor FusionSphere|7050|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Aplicacion de actualizaciones|8040|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|AD_Reinicio del servicio o servidor|7080|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Hyper-V_Agregar servidor Hyper-V a cluster|7021|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|OVM_Instalar servidor OVM|7035|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|OVM_Mantenimiento y reinicio de servidor|7038|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|NTP_Configuracion|7077|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Licenciamiento Redhat|8039|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|BL-UCS_Eliminar VLAN|7070|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Telcodrive_Aumentar cuota de usuario|7084|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHEV/oVirt_Reiniciar vCenter Server|7007|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Zerto_Instalar VRA en host|7072|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Llaves_Respaldos DB|6971|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|SMTP_Configurar servicio SMTP|7090|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|OVM_Despresentar servidor de un Cluster|7037|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|OVM_Eliminar VM|7031|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|CF_Agregar nuevo appliance CloudForms|7105|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|NXLAN_Activacion de puertos de clientes para entregar servicio|6957|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Correo_Creacion de nuevo dominio|6960|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHEV/oVirt_Realizar vMotion|6995|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Hyper-V_Crear almacén de datos|7026|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|WS-CF_Implementar nuevas funcionalidades|7114|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|NXLAN_Deshabilitar/Habilitar port-security como troubleshooting|6956|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FE-CF_Implementar nuevas funcionalidades|7111|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Hyper-V_Eliminar VLAN|7019|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Almacenamiento_Agregar LUNs a nuevos servidores|6966|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|TelcoOffice_Verificación de conexión con TelcoDrive|7098|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHEV/oVirt_Eliminar VM|6997|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHEV/oVirt_Eliminar Port Group en dvswitch|7002|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Almacenamiento_Eliminar una LUNs|6965|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|KB_Creación de un nuevo sitio para departamento|7089|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|OVM_Agregar servidor OVM a cluster|7036|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Creación de nuevos Port Groups en dvswitch|6982|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|CF_Etiquetar datastores, clusters, hosts para que puedan ser usados en automatización|7101|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|CF_Configurar servicios en el appliance|7104|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|NXLAN_Ejecución de respaldos de configuración|6951|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHEV/oVirt_Clonar VM|7000|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Hyper-V_Mantenimiento y reinicio de servidor|7023|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Creación de VMs|6973|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|NXLAN_Creacion/Eliminacion de Macs en puertos de clientes en port-security|6952|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FE-CF_Actualizar certificado SSL|7112|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Syslog_Revisión de archivos de LOGs y BDD|6958|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|BL-UCS_Mantenimiento y reinicio de blades|7067|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Hyper-V_Clonar VM|7017|'
    );
    ti2 := itemarray(
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|BL-HPE_Crear nueva VLAN|7065|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Almacenamiento_Crear replicación en vdisks|6963|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FCompute_Clonar VM|7047|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|OVM_Verificación y reinicio de servicios en OVMM|7040|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|DNS_Crear zona|7081|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FCompute_Crear almacén de datos|7056|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHEV/oVirt_Instalar servidor ESXi|7003|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Creación de VMs|6972|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FCompute_Mantenimiento y reinicio de servidor|7053|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|DNS_Crear registro DNS|7082|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Consolidar discos VMs|6991|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHUI_Reinicio de servidores|7097|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Hyper-V_Creación de VMs|7010|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Hyper-V_Crear un punto de control|7015|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Crear almacén de datos|6990|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|NXLAN_Reinicio de puerto de Nexus en puerto de clientes|6953|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|SMTP_Reinicio de servicio o de servidor|7092|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|CF_Editar credenciales, nombres de elementos usados en el código de automatización|7108|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Almacenamiento_Desprensentar Lun de servidor en mantenimiento|6964|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Zerto_Migración controlada|7076|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Realizar vMotion|6976|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Telcodrive_Reinicio de servicio o de servidor|7086|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|BL-UCS_Crear nueva VLAN|7069|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FAccess_Crear usuario y asignarle rol en VDI|7060|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Correo_Creación, modificación y eliminación de cuentas|6959|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Verificación y reinicio servicios en vCenter Server|6989|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Hyper-V_Despresentar servidor de un Cluster|7022|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Actualizacion de Vmware-Tools en VMs (sin afectación)|6974|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Despresentar servidor de un Cluster|6986|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Llaves_Creación de usuarios administradores|6970|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHEV/oVirt_Incremento de recursos VMs|6996|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FCompute_Verificación y reinicio de servicios en VRM|7055|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|CF_Crear nuevo servicio de catálogo|7099|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|CF_Creación de servicio, máquina|7107|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FCompute_Reiniciar VRM|7054|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHUI_Crear RPM para instalación de repositorio|7095|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Almacenamiento_Crear y agregar LUNs a servidores existentes|6962|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHEV/oVirt_Crear almacén de datos|7009|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|OVM_Eliminar VLAN|7034|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHEV/oVirt_Sacar un snapshot|6998|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHEV/oVirt_Eliminar un snapshot|6999|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|OVM_Crear nueva VLAN|7033|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Hyper-V_Incremento de recursos VMs (BOC)|7013|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|WS-CF_Creación manual de usuarios CloudForms|7113|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Clonar VM|6981|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|DC-CF_Respaldar base de datos|7110|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|NXLAN_Creacion/Eliminacion de vlans en puertos de clientes|6954|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FCompute_Despresentar servidor de un Cluster|7052|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|OVM_Clonar VM|7032|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|OVM_Reiniciar OVM Manager|7039|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Instalar servidor ESXi|6984|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Zerto_Crear un perfil de servicio de Zerto|7074|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|LDAP-CF_Gestión de usuarios|7109|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|NXLAN_Cambio de velocidad y negociacion en puertos de clientes|6955|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHEV/oVirt_Creación de VMs|6992|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|OVM_Creación de VMs|7027|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FCompute_Eliminar VM|7046|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FAccess_Crear plantilla de VDI|7057|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHEV/oVirt_Agregar servidor ESXi a vCenter|7004|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Respaldo_Creación, modificación y eliminación de cuentas de BackupNet 2.0|6969|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHUI_Administrar certificados (crear, renovar)|7094|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHEV/oVirt_Despresentar servidor de un Cluster|7005|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|OVM_Crear almacén de datos|7041|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHEV/oVirt_Mantenimiento y reinicio de servidor|7006|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Hyper-V_Instalar servidor Hyper-V|7020|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|BL-UCS_Creación, clonación y actualización de perfiles Virtual Connects|7068|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Hyper-V_Eliminar un punto de control|7016|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|BL-HPE_Eliminar VLAN|7066|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|CF_Exportar-respaldar código de automatización|7102|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|OVM_Migrar VM a otro host en caliente|7029|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FCompute_Incremento de recursos VMs|7045|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FCompute_Creación de VMs|7042|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|OVM_Incremento de recursos VMs|7030|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Telcodrive_Ejecutar proceso de limpieza|7087|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Hyper-V_Activación de servicios de integración|7011|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHUI_Administrar repositorios (añadir, sincronizar, eliminar)|7093|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FAccess_Eliminar usuario|7061|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FCompute_Eliminar VLAN|7049|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Respaldo_Crear trabajos de respaldo|6968|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Zerto_Crear VPG|7073|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Correo_Actualizacion de certificados digitales|6961|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Zerto_Prueba de falla|7075|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|AD_Unir nuevo AD al dominio|7079|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FAccess_Crear servidor DHCP para nueva VLAN|7062|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Eliminar un snapshot|6980|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|SMTP_Permitir IP o red para envío de correos|7091|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHUI_Verificación y reinicio de servicios (troubleshooting)|7096|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Hyper-V_Eliminar VM|7014|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FCompute_Crear nueva VLAN|7048|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Respaldo_Instalar agentes de respaldo|6967|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHEV/oVirt_Creación de nuevos Port Groups en dvswitch|7001|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Reiniciar vCenter Server|6988|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Sacar un snapshot|6979|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Incremento de recursos VMs|6977|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Telcodrive_Reparar corrupción en una biblioteca|7085|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Hyper-V_Crear nueva VLAN|7018|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Hyper-V_Reiniciar SCVMM|7024|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Mantenimiento y reinicio de servidor|6987|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|BL-HPE_Creación, clonación y actualización de perfiles Virtual Connects|7064|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|AD_Crear usuario|7078|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|DNS_Reinicio del servicio o servidor|7083|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHEV/oVirt_Actualizacion de Vmware-Tools en VMs (sin afectación)|6993|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|FCompute_Agregar servidor FusionSphere a cluster|7051|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Eliminar Port Group en dvswitch|6983|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|CF_Revisar problemas de aprovisionamiento o|7106|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Zerto_Instalar ZVM|7071|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|Vmware_Agregar servidor ESXi a vCenter|6985|',
            'EJECUCION DE TAREAS|TI - EJECUCIÓN DE CAMBIOS DC (CLOUD)|RHEV/oVirt_Verificación y reinicio servicios en vCenter Server|7008|'
    );

    departamentos := departamentosarray(ti1, ti2);

    FOR d in 1 .. departamentos.count LOOP

        items             := departamentos(d);
        total             := items.count;
        totalTareasNuevas := 0;
        transInsert       := 0;
        totalExiste       := 0;

       IF(d = 1 OR d = 2) THEN
            nombredepartamento := 'TI'; --Data Center Ti
            iddepartamento     := 821;
        END IF;

        FOR i in 1 .. total LOOP
            countn1:=0;
            idtareanueva:=0;

            tareanivel1 := SUBSTR(items(i),0,(INSTR(items(i),'|')-1)); 
            tareanivel2 := SUBSTR(items(i), (INSTR(items(i),'|')+1), LENGTH(items(i)) );
            tareanivel3 := SUBSTR(tareanivel2,(INSTR(tareanivel2,'|')+1),LENGTH(tareanivel2)); 
            tareanivel2 := SUBSTR(tareanivel2,0,(INSTR(tareanivel2,'|')-1));
            tareanivel4 := SUBSTR(tareanivel3,INSTR(tareanivel3,'|')+1,LENGTH(tareanivel3)); 
            tareanivel3 := SUBSTR(tareanivel3,0,(INSTR(tareanivel3,'|')-1)); 
            tareanivel5 := SUBSTR(tareanivel4,INSTR(tareanivel4,'|')+1,LENGTH(tareanivel4));
            tareanivel4 := SUBSTR(tareanivel4,0,(INSTR(tareanivel4,'|')-1));

            --CONSULTA TAREA SI EXISTE
            BEGIN
                IF tareanivel4 IS NOT NULL THEN
                    --BUSCA TAREA POR ID
                    SELECT ID_TAREA INTO idtareanueva FROM DB_SOPORTE.ADMI_TAREA WHERE ID_TAREA = TRIM(tareanivel4);
                ELSE
                    --BUSCA TAREA POR EL NOMBRE
                    SELECT NVL(MAX(ID_TAREA),0) INTO idtareanueva FROM DB_SOPORTE.ADMI_TAREA 
                    WHERE translate(UPPER(NOMBRE_TAREA),'áéíóúÁÉÍÓÚ','aeiouAEIOU') = TRIM(translate(UPPER(tareanivel3),'áéíóúÁÉÍÓÚ','aeiouAEIOU')) 
                    AND ESTADO='Activo';

                    --SI NO ENCONTRO POR NOMBRE ENTONCES LA CREA
                    IF idtareanueva = 0 THEN
                        idtareanueva := DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL;

                        INSERT INTO DB_SOPORTE.ADMI_TAREA (ID_TAREA,PROCESO_ID,ROL_AUTORIZA_ID,TAREA_ANTERIOR_ID,
                                                        TAREA_SIGUIENTE_ID,PESO,ES_APROBADA,NOMBRE_TAREA,
                                                        DESCRIPCION_TAREA,TIEMPO_MAX,UNIDAD_MEDIDA_TIEMPO,COSTO,
                                                        PRECIO_PROMEDIO,ESTADO,USR_CREACION,FE_CREACION,
                                                        USR_ULT_MOD,FE_ULT_MOD,AUTOMATICA_WS,
                                                        CATEGORIA_TAREA_ID,PRIORIDAD,REQUIERE_FIBRA,VISUALIZAR_MOVIL) 
                        VALUES (idtareanueva,idproceso,null,null,null,'1','0',TRIM(UPPER(tareanivel3)),TRIM(UPPER(tareanivel3)),'3',
                                'HORAS','0','0','Activo','jobedon',sysdate,
                                'jobedon',sysdate,null,null,null,'N','S');

                        COMMIT;

                        totalTareasNuevas := totalTareasNuevas + 1;

                    END IF;

                END IF;

            EXCEPTION
                WHEN OTHERS THEN
                    Lv_MensajeError := SQLCODE || ' -ERROR- ' || SQLERRM ;
                    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                                        'MIGRACION ARBOL TAREAS - SYSCLOUD',
                                                        '[ERROR AL CREAR TAREA EN DB_SOPORTE.ADMI_TAREA] => '||Lv_MensajeError,
                                                        NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_SOPORTE'),
                                                        SYSDATE,
                                                        NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),
                                                        '127.0.0.1')
                                                        );
            END;

            --SE ACTUALIZA QUE SE VISUALIZA EN MOVIL
            IF idtareanueva IS NOT NULL THEN
                    UPDATE DB_SOPORTE.ADMI_TAREA SET VISUALIZAR_MOVIL='N' WHERE ID_TAREA = idtareanueva;
            END IF;

            --CONSULTA SI EXISTE YA EN EL ARBOL
            SELECT COUNT(ID_PARAMETRO_DET) INTO countn1 FROM DB_GENERAL.ADMI_PARAMETRO_DET 
            WHERE UPPER(DESCRIPCION) = UPPER(descripcionparametro) 
            AND translate(UPPER(VALOR1),'áéíóúÁÉÍÓÚ','aeiouAEIOU')= TRIM(translate(UPPER(tareanivel1),'áéíóúÁÉÍÓÚ','aeiouAEIOU'))
            AND translate(UPPER(VALOR2),'áéíóúÁÉÍÓÚ','aeiouAEIOU') = TRIM(translate(UPPER(tareanivel2),'áéíóúÁÉÍÓÚ','aeiouAEIOU')) 
            AND VALOR3 = TO_CHAR(idtareanueva) AND ESTADO = 'Activo';

            IF countn1 <= 0 THEN

                INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
                (SELECT id_parametro FROM db_general.admi_parametro_cab WHERE nombre_parametro = descripcionparametrocab),
                descripcionparametro,TRIM(UPPER(tareanivel1)),TRIM(UPPER(tareanivel2)),idtareanueva,
                imagen,'Activo','jobedon',SYSDATE,'127.0.0.1', NULL,NULL,NULL,iddepartamento,NULL,NULL,NULL,
                'VALOR1=> NIVEL 1, VALOR2 => NIVEL 2, VALOR3 => NIVEL 3 (ID TAREA)' );

                transInsert := transInsert +1;
            ELSE
        DBMS_OUTPUT.PUT_LINE('[NIVEL1]=>'||TRIM(UPPER(tareanivel1))||' [NIVEL2]=>'||TRIM(UPPER(tareanivel2))||' [NIVEL3]=>'||TRIM(UPPER(tareanivel3)));

                totalExiste := totalExiste + 1;
            END IF;

        END LOOP;

        DBMS_OUTPUT.PUT_LINE('[DEPARTAMENTO]=>'||nombredepartamento||' [TOTAL A REGISTRAR]=> '||total ||' [TOTAL YA EXISTEN]=> '||totalExiste 
                                                    ||' [TOTAL TAREAS NUEVAS]=> '||totalTareasNuevas 
                                                    ||' [TOTAL REGISTRADAS]=> '||transInsert);

        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                            'MIGRACION ARBOL TAREAS - SYSCLOUD',
                                            '[DEPARTAMENTO]=>'||nombredepartamento||
                                            ' [TOTAL A REGISTRAR]=> '||total ||' [TOTAL YA EXISTEN]=> '||totalExiste 
                                            ||' [TOTAL TAREAS NUEVAS]=> '||totalTareasNuevas 
                                            ||' [TOTAL REGISTRADAS]=> '||transInsert,
                                            NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_SOPORTE'),
                                            SYSDATE,
                                            NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),
                                            '127.0.0.1')
                                            );
    COMMIT;

   END LOOP;

  EXCEPTION
  WHEN OTHERS THEN
  --
  Lv_MensajeError := SQLCODE || ' -ERROR- ' || SQLERRM ;
  DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                        'MIGRACION ARBOL TAREAS - SYSCLOUD',
                                        Lv_MensajeError,
                                        NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_SOPORTE'),
                                        SYSDATE,
                                        NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'),
                                        '127.0.0.1')
                                      );
END;

/