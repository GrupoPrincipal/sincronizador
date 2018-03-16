<?php
require("../core/config_var.php");
require(COREROOT."lib/class/sincronizador.class.php");
$Obj_sync= new Sincronizador;
?>
<?php
if($_GET['type']=='lista' ){
    
    $sincronizador=$Obj_sync->get_sincronizar();
    //die(var_dump($retenciones));
?>
<div class="col-md-12" style="    background: #fff;
    padding: 19px;
    box-sizing: border-box;
    margin-bottom: 50px;
    margin-top: 19px;
    box-shadow: 6px 6px 35px #428bca;">
            
        
        <table id="example" class="display" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Descripcion</th>
                <th>Desde</th>
                <th>Para</th>
                <th>Vincular</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($sincronizador as $s){
            ?>
                <tr>
                    <td><?php echo $s['VINV_DESCRIPCION']; ?></td>
                    <td><?php echo $s['CONV_DESCRIPCION']; ?></td>
                    <td><?php echo $s['VINV_BASEDATOS']; ?></td>
                    <td><?php echo $s['VINV_TABLADES']; ?></td>
                    <td><a class='fa fa-filter' href="#" onclick="opcion('<?php echo $s['VINN_IDVINCULO']; ?>')" title="Opciones">  </a></td>
                </tr>
            <?php
            }
            ?>

        </tbody>
    </table>
        <h5 style='color:#ff5806'><strong><i class="fa fa-exclamation-triangle" aria-hidden="true" > </i> ¡Advertencia!</strong> En caso que su retencion no se encuentre notifique a administracion.sc@gdp.com.ve</h5>
    </div>
<script>
$(document).ready(function() {
    $('#example').DataTable( {
        "language": {
            "search": "Buscar:",
            "LengthMenu": "Mostrar _MENU_ registros",
            "lengthMenu": 'Mostrar <select>'+
			             '<option value="10">10</option>'+
			             '<option value="20">20</option>'+
			             '<option value="-1">Todos</option>'+
			             '</select> registros',
            "zeroRecords": "<h5 style='color:#ff5806'><strong><i class='fa fa-exclamation-triangle' aria-hidden='true' > </i> ¡Advertencia!</strong> En caso que su retencion no se encuentre notifique a administracion.sc@gdp.com.ve</h5>",
            "info": "Mostrar _PAGE_ de _PAGES_",
            "infoEmpty": "Registro no encontrado",
            "infoFiltered": "",
            "loadingRecords": "Cargando...",
            "Processing": "Procesando...",
            "paginate":{
                "sFirst": "Primero",
                "sLast": "Ultimo",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        }
    } );
    

});
</script>
<?php
}
if($_GET['type']=='opciones'){
    $sync=$Obj_sync->get_vinculo($_GET['id']);
    $sync=$sync[0];
    $data=$Obj_sync->consultar($sync['VINV_CURSORORI'],$sync['desde']);
    
?>

<div class="row">
	<div class="col-md-12">
		<!-- BEGIN EXAMPLE TABLE PORTLET-->
		<div class="portlet light portlet-fit ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-settings font-red"></i>
					<span class="caption-subject font-red sbold uppercase">Sincronizar</span>
				</div>
                <div class="table-toolbar right">
					<div class="row" style=" text-align: right;">
						
							<div class="btn-group">
                                <button id="sample_editable_1_new" class="btn green" onclick='sync()'> Sincronizar
									<i class="fa fa-sync"></i>
								</button> 
							</div>

					</div>
				</div>
			</div>
			<div class="portlet-body cambios_opc">
                Consultar DB <?php echo $sync['CONV_DATA']; ?>
                <br>
                Enviar a DB <?php echo $sync['VINV_BASEDATOS']; ?>
                <br>
                Total de resgistros  <?php echo count($data);  ?>
                <hr>
                <?php
                    $campos='';
                    foreach($data[0] as $key => $d){
                        $campos.=$key.',';
                    }
                    $campos=substr($campos, 0, -1);
    
                    $Obj_sync->insertar($campos,$data,$sync['VINV_TABLADES'],$sync['para']);
                    echo $sync['VINV_DESCRIPCION']." Sincronizacion exitosa";
                ?>
                <div class="registro de">
                
                </div>
			</div>
		</div>
         <div class="form-actions fluid">
                <button class="btn red" onclick="cancelar()">Cancelar</button>
			</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>

<?php
}
?>