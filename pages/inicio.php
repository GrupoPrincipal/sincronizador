
    
    
<div class="nav-tabs-custom" id="list-rutas"  >
<!-- Tabs within a box -->
<div class="tab-content ">
  <div class="row">
    <section class="col-lg-12 connectedSortable sec-cont">
    </section>
</div>

</div>
</div>
          <!-- /.nav-tabs-custom -->
<script>
$(function(){
    $('.sec-cont').html('<center><img src="../styles/images/cargando.gif" style="width:80px;"><h3>Cargando...</h3></center>');
    $('.sec-cont').load('inicio_async.php?type=lista');
    
   
 });


function cancelar(){
    $('.sec-cont').html('<center><img src="../styles/images/cargando.gif" style="width:80px;"><h3>Cargando...</h3></center>');
    $('.sec-cont').load('inicio_async.php?type=lista');
}
function opcion(id){
   $('.sec-cont').html('<center><img src="../styles/images/cargando.gif" style="width:80px;"><h3>Cargando...</h3></center>');
   $('.sec-cont').load('inicio_async.php?type=opciones&id='+id);
}
</script>

