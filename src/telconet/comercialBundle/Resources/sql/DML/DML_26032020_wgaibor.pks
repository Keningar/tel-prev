DECLARE
    CURSOR c_netlife_cloud IS
    SELECT
        iser.id_servicio, necl.*
    FROM
        db_comercial.info_punto      inpu,
        db_comercial.info_servicio   iser,
        db_comercial.info_plan_cab   ipcb,
        db_comercial.netlife_cloud   necl
    WHERE
        inpu.id_punto = iser.punto_id
        AND inpu.login = necl.login
        AND iser.plan_id = ipcb.id_plan
        and upper(ipcb.nombre_plan) = upper(necl.nombreplan)
        AND iser.estado = 'Activo'
        AND iser.producto_id IS NULL
        AND iser.frecuencia_producto != 0
        and necl.estado = 'NOSERVICIO';

    CURSOR c_netlife_cloud_pendientes IS
    SELECT
        ncloud.*
    FROM
        db_comercial.netlife_cloud ncloud
    WHERE
        ncloud.estado = 'Pendiente';

    ln_servicioid                       NUMBER;
    ln_caracteristica_ordernumber       NUMBER;
    ln_caracteristica_productkey        NUMBER;
    ln_caracteristica_urloffice         NUMBER;
    ln_caracteristica_descripcion       NUMBER;
    lv_DescripcionProducto              VARCHAR2(400):='Microsoft Office 365 Home - Licencia de suscripción ( 1 año ) - 5 teléfonos, 5 PC/Mac, 5 tabletas, espacio de almacenamiento en la nube de 1 TB - no comercial - ESD - 32/64-bit - Win, Mac, Android, iOS - All Languages';
    TYPE Tv_NetlifeCloud    IS TABLE OF c_netlife_cloud%ROWTYPE;
    Lc_netlifeCloud         Tv_NetlifeCloud;
    TYPE Tv_NCpendientes    IS TABLE OF c_netlife_cloud_pendientes%ROWTYPE;
    Lc_NCpendientes         Tv_NCpendientes;
BEGIN
 --PRIMERO BUSCAMOS EL SERVICIO
 --
  OPEN c_netlife_cloud;
  LOOP
  --
    FETCH c_netlife_cloud BULK COLLECT INTO Lc_netlifeCloud LIMIT 5000;
    EXIT WHEN Lc_netlifeCloud.COUNT = 0;
    FORALL I IN Lc_netlifeCloud.FIRST .. Lc_netlifeCloud.LAST SAVE EXCEPTIONS
      UPDATE db_comercial.netlife_cloud necl
        SET
            necl.servicio_id = Lc_netlifeCloud(I).id_servicio,
            estado = 'Pendiente'
        WHERE
            necl.login = Lc_netlifeCloud(I).login;
    --
    END LOOP;
    --
  CLOSE c_netlife_cloud;
  --
  COMMIT;
--Obtener el producto caracteristica ORDERNUMBER
    SELECT
        apca.id_producto_caracterisitica
    INTO ln_caracteristica_ordernumber
    FROM
        db_comercial.admi_producto_caracteristica apca
    WHERE
        producto_id = (
            SELECT
                adpr.id_producto
            FROM
                db_comercial.admi_producto adpr
            WHERE
                descripcion_producto = 'NetlifeCloud'
        )
        AND caracteristica_id = (
            SELECT
                id_caracteristica
            FROM
                db_comercial.admi_caracteristica
            WHERE
                descripcion_caracteristica = 'ORDERNUMBER'
        );
--Obtener el producto caracteristica PRODUCTKEY

    SELECT
        apca.id_producto_caracterisitica
    INTO ln_caracteristica_productkey
    FROM
        db_comercial.admi_producto_caracteristica apca
    WHERE
        producto_id = (
            SELECT
                adpr.id_producto
            FROM
                db_comercial.admi_producto adpr
            WHERE
                descripcion_producto = 'NetlifeCloud'
        )
        AND caracteristica_id = (
            SELECT
                id_caracteristica
            FROM
                db_comercial.admi_caracteristica
            WHERE
                descripcion_caracteristica = 'PRODUCTKEY'
        );
--Obtener el producto caracteristica URLOFFICE

    SELECT
        apca.id_producto_caracterisitica
    INTO ln_caracteristica_urloffice
    FROM
        db_comercial.admi_producto_caracteristica apca
    WHERE
        producto_id = (
            SELECT
                adpr.id_producto
            FROM
                db_comercial.admi_producto adpr
            WHERE
                descripcion_producto = 'NetlifeCloud'
        )
        AND caracteristica_id = (
            SELECT
                id_caracteristica
            FROM
                db_comercial.admi_caracteristica
            WHERE
                descripcion_caracteristica = 'URLOFFICE'
        );
--Obtener el producto caracteristica DESCRIPCIONOFFICE

    SELECT
        apca.id_producto_caracterisitica
    INTO ln_caracteristica_descripcion
    FROM
        db_comercial.admi_producto_caracteristica apca
    WHERE
        producto_id = (
            SELECT
                adpr.id_producto
            FROM
                db_comercial.admi_producto adpr
            WHERE
                descripcion_producto = 'NetlifeCloud'
        )
        AND caracteristica_id = (
            SELECT
                id_caracteristica
            FROM
                db_comercial.admi_caracteristica
            WHERE
                descripcion_caracteristica = 'DESCRIPCIONOFFICE'
        );
--Procesaremos los servicios que se encuentre con estado PENDIENTE unicamente
--
  OPEN c_netlife_cloud_pendientes;
  LOOP
  --
    FETCH c_netlife_cloud_pendientes BULK COLLECT INTO lc_ncpendientes LIMIT 5000;
    EXIT WHEN lc_ncpendientes.COUNT = 0;
    FORALL I IN lc_ncpendientes.FIRST .. lc_ncpendientes.LAST SAVE EXCEPTIONS
    INSERT INTO db_comercial.info_servicio_prod_caract (
        id_servicio_prod_caract,
        servicio_id,
        producto_caracterisitica_id,
        valor,
        fe_creacion,
        usr_creacion,
        estado
      ) VALUES (
        db_comercial.seq_info_servicio_prod_caract.nextval,
        lc_ncpendientes(I).servicio_id,
        ln_caracteristica_ordernumber,
        lc_ncpendientes(I).ordernumber,
        sysdate,
        'NetlifeCloud',
        'Activo'
      );
    FORALL I IN lc_ncpendientes.FIRST .. lc_ncpendientes.LAST SAVE EXCEPTIONS
      INSERT INTO db_comercial.info_servicio_prod_caract (
        id_servicio_prod_caract,
        servicio_id,
        producto_caracterisitica_id,
        valor,
        fe_creacion,
        usr_creacion,
        estado
      ) VALUES (
        db_comercial.seq_info_servicio_prod_caract.nextval,
        lc_ncpendientes(I).servicio_id,
        ln_caracteristica_productkey,
        lc_ncpendientes(I).productkey,
        sysdate,
        'NetlifeCloud',
        'Activo'
      );
    FORALL I IN lc_ncpendientes.FIRST .. lc_ncpendientes.LAST SAVE EXCEPTIONS
      INSERT INTO db_comercial.info_servicio_prod_caract (
        id_servicio_prod_caract,
        servicio_id,
        producto_caracterisitica_id,
        valor,
        fe_creacion,
        usr_creacion,
        estado
      ) VALUES (
        db_comercial.seq_info_servicio_prod_caract.nextval,
        lc_ncpendientes(I).servicio_id,
        ln_caracteristica_urloffice,
        lc_ncpendientes(I).urloffice,
        sysdate,
        'NetlifeCloud',
        'Activo'
      );
    FORALL I IN lc_ncpendientes.FIRST .. lc_ncpendientes.LAST SAVE EXCEPTIONS
      INSERT INTO db_comercial.info_servicio_prod_caract (
        id_servicio_prod_caract,
        servicio_id,
        producto_caracterisitica_id,
        valor,
        fe_creacion,
        usr_creacion,
        estado
      ) VALUES (
        db_comercial.seq_info_servicio_prod_caract.nextval,
        lc_ncpendientes(I).servicio_id,
        ln_caracteristica_descripcion,
        lv_descripcionproducto,
        sysdate,
        'NetlifeCloud',
        'Activo'
      );
    FORALL I IN lc_ncpendientes.FIRST .. lc_ncpendientes.LAST SAVE EXCEPTIONS
      UPDATE db_comercial.netlife_cloud necl
        SET
            estado = 'Procesado'
        WHERE
            necl.login = lc_ncpendientes(I).login;
    --
    END LOOP;
    --
  CLOSE c_netlife_cloud_pendientes;
  --
  COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        dbms_output.put_line('ERROR NO CONTORLADO. ' || sqlerrm);
        ROLLBACK;
END;

/
