<?php
include("core/config_var.php");
include("metodoswebservices.php");

if($_GET['view']=='validar'){
    if(!empty($_POST['user'])&& !empty($_POST['pass'])){
        
        $postData = array($_POST['user'],$_POST['pass'],3);
        try {
            $handler = curl_init();  
            curl_setopt($handler, CURLOPT_URL, "https://sistemasc.gdp.com.ve/soap_app/login.php");  
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
        $user=json_decode($response);
        $user=get_object_vars($user);
        unset($user['menu']);
        unset($user['info']);
        $_SESSION['user_gdp']=$user;
        echo $user['resp'];
        
    }else{
        echo 9;
    }
}else{
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Distribuciones Principal</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="styles/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/plugins/fonts/font-awesome.css">
    <link rel="stylesheet" href="styles/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="styles/plugins/iCheck/square/blue.css">
    <link rel="icon" href="styles/images/icono.png">
</head>
<body class="hold-transition login-page">

<div class="login-box">
  <div class="login-logo">
      <a href="http://www.gdp.com.ve/">
    <img src="styles/images/logo_transparent.png">
    Distribuciones Principal</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg"Iniciar session</p>
      <div class="form-group has-feedback">
        <input type="text" class="form-control" placeholder="Usuario">
        <span class="fa fa-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Password">
        <span class="fa fa-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Iniciar</button>
        </div>
        <!-- /.col -->
      </div>
    <!-- /.social-auth-links -->
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.2.3 -->
<script src="styles/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="styles/bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="styles/plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
      var waring='<div class="alert alert-warning alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button> <h4><i class="icon fa fa-warning"></i> Advertencia!</h4>Debe Ingresar el usario y la contraseña. </div>';
      var error='<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h4><i class="icon fa fa-ban"></i> Error!</h4> El usuario o la contraseña no son validos </div>';
      var success='<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h4><i class="icon fa fa-check"></i> Bien!</h4> Espere un momento.</div>';
      var information='<div class="alert alert-info alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h4><i class="icon fa fa-info"></i> Alerta!</h4> Usuario no autorizado.</div>';
      $(":submit").click(function(){
        var user=$(":text").val();
        var pass=$(":password").val();
        if(user!='' && pass!=''){
        var datos="user="+user+"&pass="+pass;
        $.post("?view=validar",datos,function(response){
            console.log(response);
           if(response=='ok'){
               $('body').prepend(success);
             window.location ="pages/";
           }else if(response==1001){
               $('body').prepend(error);
           }else if(response==9){
               $('body').prepend(waring);
           }else{
               console.log(response);
           }
        });
        }else{
            $('body').prepend(waring);
        }
        setTimeout(function(){ $('.alert').remove(); }, 3000);
    });
  });
</script>
</body>
</html>
<?php
}
?>