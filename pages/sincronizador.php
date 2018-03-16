<?php
error_reporting(0);
ini_set(display_errors, 0);
require("core/config_var.php");
require(COREROOT."lib/class/sincronizador.class.php");
$Obj_sync= new Sincronizador;
echo "Iniciando sincronizacion \n";
$enlaces=$Obj_sync->get_sincronizar();
foreach($enlaces as $a){
    echo "Extrayendo ".$a['VINV_DESCRIPCION']." de ".$a['CONV_DESCRIPCION']."\n";
        $data=$Obj_sync->consultar_reg($a['VINV_CURSORORI'],$a['desde'],$a['para'],$a['VINV_TABLADES'],$a['VINV_INDICES']);
    echo $data;
    echo "\n";
}