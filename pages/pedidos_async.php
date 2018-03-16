<?php
require("../core/config_var.php");
require(COREROOT."lib/class/sincronizador.class.php");
$Obj_sync= new Sincronizador;
?>

<?php
if($_GET['type']=='lista' ){

    $pedidos=$Obj_sync->get_pedidos();
    
?>
<div class="col-lg-12 ">

      <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-filter"></i>Pedidos: 
                
            </div>
            <div class="actions">
                <a href="javascript:;" onclick="cancelar()" class="btn btn-info"  >Actualizar</a> 
                <a href="javascript:;" onclick="rechazar()" class="btn btn-danger"  >RECHAZAR</a> 
                <a href="javascript:;" onclick="procesar()" class="btn btn-success"  >PROCESAR</a>
            </div>
        </div>
        <div class="portlet-body">
            <br>
            <!-- BEGIN FORM-->
            
           <table id="example" class="display" cellspacing="0">
                <thead>
                    <tr>
                        <th>Ruta</th>
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($pedidos as $p){
                    ?>
                        <tr>
                            <td><?php echo $p['pediv_codivend']; ?></td>
                            <td><?php 
                                    $ped=str_pad($p['pediv_numero'],8,0,STR_PAD_LEFT);
                                    echo 'C'.$ped; 
                                ?>
                            </td>
                            <td><?php echo $p['CLIEV_IDCLIENTE'].' - '.$p['CLIEV_RAZONSOC']; ?></td>
                            <td><?php echo $p['fecha']; ?></td>
                            <td><?php echo number_format($p['pedidocn_iva'],2); ?></td>
                            <td>
                                <b class="fa fa-eye btn btn-warning" style="cursor: pointer;
    display: inline-block;
    float: left;
    margin-right: 10px;"  onclick="detail(<?php echo $p['pediv_numero'] ?>)"></b>
                                <input style="    display: inline-block;
    border: solid;
    overflow: auto;
    width: 35px;
    height: 35px;
    float: left;
    margin-top: 0px;" class="pedidos form-checkbox" data-caduca="<?php echo $p['caduca']; ?>" type="checkbox" name="pedido[]" value="<?php echo $p['pediv_numero']; ?> " ></td>
                        </tr>
                    <?php
                    }
                    ?>

                </tbody>
            </table>
            <br>
        </div>
    </div>
  </div>

<script>
    function rechazar(){
       var resp = validar();
       if(resp==1){
         var pedidos=[];
           var p=0;
           $('.pedidos').each(function(i){
            if($( this ).prop( "checked" )){
                pedidos[p]=$( this ).val();
                p=p+1;
            }
            });
           $.post('pedidos_async.php?type=procesar', 'tipo=RECHAZADO&pedidos='+pedidos,function(e){
               alertify.success("Pedidos Rechazados");
               cancelar();
           })
       }
    }
    function procesar(){
       var resp = validar();
       if(resp==1){
           var pedidos=[];
           var efechas=[];
           var p=0;
           var c=0;
           $('.pedidos').each(function(i){
            if($( this ).prop( "checked" )){
                if($( this ).data('caduca')<=1){
                    pedidos[p]=$( this ).val();
                    p=p+1;
                }else{
                    efechas[c]=$( this ).val();
                    c=c+1;
                    error=1;
                }
            }
            });
          if(p >=1){
           $.post('pedidos_async.php?type=procesar', 'tipo=PROCESADO&pedidos='+pedidos,function(e){
               alertify.success("Pedidos procesados");
           });
          }
           if(c >=1){
             $.jAlert({confirmQuestion:'Desea actualizar la fecha de los pedidos?<br>'+efechas,title:'Fecha de pedidos caducada',
                       theme:'blue','type': 'confirm', 'onConfirm': acceptUpdate, 'onDeny': denyUdate });
           }else{
               cancelar();
           }
           
       }
    }
    function acceptUpdate(){
        //closejAlert
        alert("Ingrese contraseña","<input type='text' autocomplete='off' placeholder='Usuario' class='form-control' name='user ' id='user'><input type='password' autocomplete='off' placeholder='Contraseña' class='form-control' name='pass ' id='pass'> <br><center> <button class='btn btn-danger closejAlert' > Cancelar</button>&nbsp;<button class='btn btn-success closejAlert' onclick='valPassword()'> Aceptar</button> ");
        
    }
    function valPassword(){
        var pass=$('#pass').val();
        var user=$('#user').val();
        $.post('pedidos_async.php?type=validar', 'user='+user+'&pass='+pass,function(e){
           var resp=jQuery.parseJSON(e);
            if(resp.resp==1002){
                var efechas=[];
                var c=0;
                $('.pedidos').each(function(i){
                if($( this ).prop( "checked" )){
                    if($( this ).data('caduca')>2){
                        efechas[c]=$( this ).val();
                        c=c+1;
                    }
                }
            });
                infoAlert(resp.title,resp.mensj);
                $.post('pedidos_async.php?type=updatefechas', 'user='+resp.mensj+'&pedidos='+efechas,function(response){
                     alertify.success("Pedidos procesados");
                    canelar();
                });
               
            }else{
                errorAlert('Error al actualizar',resp.mensj);
            }
        });
    }
    function denyUdate(){
        cancelar();
    }
    function validar(){
        var ckec=0;
        $('.pedidos').each(function(i){
            if($( this ).prop( "checked" )){
                ckec =1;
            }
        });
        if(ckec==0)
            alertify.error("Debe seleccionar pedidos");
        return ckec;
    }
function detail(ped){
    $('.sec-cont').html('<center><img src="../styles/images/cargando.gif" style="width:80px;"><h3>Cargando...</h3></center>');
    $('.sec-cont').load('pedidos_async.php?type=detail&ped='+ped);
}
$(document).ready(function() {
    $('#example').DataTable( {
        "language": {
            "search": "Buscar:",
            "LengthMenu": "Mostrar _MENU_ registros",
            "lengthMenu": 'Mostrar <select>'+
			             '<option value="10">10</option>'+
			             '<option value="20">20</option>'+
			             '<option value="-1">Todos</option>'+
			             '</select> Pedidos',
            "zeroRecords": "<h5 style='color:#ff5806'><strong><i class='fa fa-exclamation-triangle' aria-hidden='true' > </i> ¡Advertencia!</strong> No hay pedidos por procesar</h5>",
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
if($_GET['type']=='procesar'){
    $ped=$_POST['pedidos'];
    $pedidos=explode(',',$ped);
    
   
    if($_POST['tipo']=='PROCESADO'){
        $Obj_sync->insert_pedido($pedidos );
        
    }
    foreach($pedidos as $p){
        $Obj_sync->update_pedido($p,trim($_POST['tipo']));
    }
}
if($_GET['type']=='updatefechas'){
    $user=$_POST['user'];
    $ped=$_POST['pedidos'];
    $pedidos=explode(',',$ped);
    $Obj_sync->insert_pedido($pedidos);
            $Obj_sync->update_pedido($p,"PROCESADO");
        
}
if($_GET['type']=='validar'){
   // print_r($_POST);
    $url = "https://sistemasc.gdp.com.ve/soap_app/login.php";
        $postData = array($_POST['user'],$_POST['pass'],3);
        try {
            $handler = curl_init();  
            curl_setopt($handler, CURLOPT_URL, $url);  
            curl_setopt($handler, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1");  
            curl_setopt($handler, CURLOPT_HTTPHEADER, array("Accept-Language: es-es,en"));
            curl_setopt($handler, CURLOPT_POST,true); 
            curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($handler, CURLOPT_POSTFIELDS, $postData);
            $response = curl_exec($handler);  
            curl_close($handler);
        }catch(Exception $e) {
          var_dump($e);
        }
    $user=base64_decode($response);
    $user=json_decode($response);
    $user=get_object_vars($user);

    if($user['resp']=='ok'){
         $resp=$Obj_sync->validaruser($user['login']);
        if($resp[0]['editped']==1){
            $json=array("resp"=>'1002',"title" => " Fechas de pedidos actualizados por: ", "mensj"=> " <b>Colaborador:</b> ".$user['user']."<br> <b>Cargo: </b> ".$user['cargo']."<br> <b>Departamento:</b>".$user['dept'],"usuario"=>$user['user']);
        }else{
            $json=array("resp"=>'1001',"mensj" => "No posee permisos para actualizar las fecha del pedido");
        }
    }else{
       $json=array("resp"=>'1001',"mensj" => "Contraseña o usuario invalido");
    }
    $json=json_encode($json);
   echo $json;
}
if($_GET['type']=='detail'){
    $ped=$_GET['ped'];
    $pedido=$Obj_sync->get_pedido($ped);
?>
<div class="col-lg-12 ">

      <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-filter"></i>Pedidos: 
                
            </div>
            <div class="actions">
                <a href="javascript:;" onclick="cancelar()" class="btn btn-warning"  >Regresar</a>
            </div>
        </div>
        <div class="portlet-body">
            <br>
            <!-- BEGIN FORM-->
            
           <table id="example" class="display" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID Prod</th>
                        <th>Producto</th>
                        <th>Cajas</th>
                        <th>Unidades</th>
                        <th>Precio</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($pedido as $p){
                    ?>
                        <tr class="list-prod" id="<?php echo $p['pregv_idproducto']; ?>">
                            <td><?php echo $p['pregv_idproducto']; ?></td>
                            <td><?php echo $p['ARTV_DESCART']; ?></td>
                            <td><?php echo $p['pregn_cajas']; ?></td>
                            <td><?php echo $p['pregn_unidades']; ?></td>
                            <td><?php echo $p['pregn_precio']; ?></td>
                            <td>
                                <b class="fa fa-times btn btn-danger"  onclick="eliminar(<?php echo $p['pediv_numero'] ?>,<?php echo $p['pregv_idproducto']; ?>)">&nbsp;</b>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>

                </tbody>
            </table>
            <br>
        </div>
    </div>
  </div>
<script>
function eliminar(ped,pro){
    var t=0;
    $('.list-prod').each(function(i){
        t=t+1;
    });
    console.log(t);
    if(t >1){
       $('#'+pro).remove();
    }else{
          $.jAlert({confirmQuestion:'Eliminar pedido?<br>',title:'Pedido sin SKU',
                       theme:'blue','type': 'confirm', 'onConfirm': quitarsku(ped,pro), 'onDeny': noquitar });
    }
}
function quitarsku(ped,pro){
   $('#'+pro).remove();  
}
function noquitar(){
    
}
$(document).ready(function() {
    $('#example').DataTable( {
        "language": {
            "search": "Buscar:",
            "LengthMenu": "Mostrar _MENU_ registros",
            "lengthMenu": 'Mostrar <select>'+
			             '<option value="10">10</option>'+
			             '<option value="20">20</option>'+
			             '<option value="-1">Todos</option>'+
			             '</select> Pedidos',
            "zeroRecords": "<h5 style='color:#ff5806'><strong><i class='fa fa-exclamation-triangle' aria-hidden='true' > </i> ¡Advertencia!</strong> No hay pedidos por procesar</h5>",
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
?>