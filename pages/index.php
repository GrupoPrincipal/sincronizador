<?php 
include('top.php');
?>

<div class=" content" style="margin-top:5px">
    <div class="col-md-10 col-md-offset-1 row" style="min-height: 450px;">
     
        <section class="content">
        <?php
            if($_GET['view']=='index' || !isset($_GET['view']) ){
                include("inicio.php");
            }else{
                if(file_exists($_GET['view'].".php")){
                    include($_GET['view'].".php");
                }else{
                   ?>
            <h2>PAGINA NO ENCONTRADA <?php echo PAGES.''.$_GET['view']; ?></h2>
                    <?php
                }
            }
        ?>
        </section>
    </div>   
</div>
 <script>
  
  </script>
<?php include('footer.php'); ?>