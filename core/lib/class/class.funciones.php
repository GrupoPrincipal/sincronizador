<?php
class funciones extends Model {
    function get_distribuidor(){
        $query="SELECT CIUDAD,ESTADO,RIF,DIRECCION1,DIRECCION2,NOMBALMA FROM tdisb";
        return $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','','_VENTOR');
    }
    function get_retencion_iva($clie,$fact){
        $resp=array();
        $query="SELECT
                   tcppa.NOMBRE,
                    tcppa.RIF,
                    treiva.FORIEMI,
                    treiva.NUMLEGAL,
                    treiva.FACTURA,
                    treiva.CONTROL,
                    treiva.TIPO,
                    treiva.LEGALAFE,
                    treiva.EXENTO,
                    treiva.MONTO,
                    treiva.BASE,
                    treiva.ALIC,
                    treiva.IVA,
                    treiva.RET,
                    treiva.EMISION,
                    treiva.TOTAL,
                    treiva.FORILLE,
                    treiva.RETENCION AS p,
                    treiva.nume,
                    treiva.CODIPROV,
                    tcppa.DIRECCION1,
                    tcppa.DIRECCION2,
                    tciua.NOMBCIUD AS CIUDAD,
                    testa.NOMBESTA AS ESTADO
                FROM
                    treiva
                LEFT JOIN tcppa ON treiva.CODIPROV = tcppa.CODIPROV
                LEFT JOIN tciua ON tcppa.CODICIUD = tciua.CODICIUD
                LEFT JOIN testa ON tcppa.CODIESTA = testa.CODIESTA
                AND tciua.CODIESTA = testa.CODIESTA

                WHERE treiva.MONTO > 0 and
                    tcppa.CODIPROV = '$clie'";
        if(!empty($fact)){
                $query.="AND trim(NUMLEGAL) LIKE '%$fact%' ";
        }else{
                $query.=" order by treiva.FORIEMI desc limit 50 ";
        }
        
        $resp = $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','','_VENTOR');
        
        for($i=0;$i<count($resp);$i++){
            $data=$this->islr_valide($clie,$resp[$i]['NUMLEGAL']);

            if(!empty($data)){
              $resp[$i]=array_merge($resp[$i],$data[0]);
            }
        }
        
        if(!empty($resp) && empty($fact)){
            $j=count($resp);
            $not=" NUMLEGAL NOT IN (";
            foreach($resp as $iva){
              $not.="'".$iva['NUMLEGAL']."',";
            }
            $not = substr($not, 0, -1);
            $not.=")";
            $query="SELECT
                        tcppc.FORIEMI,
                        tislrpv.MONTO,
                        tcppc.NUMLEGAL,
                        tcppc.NUMCONTF AS CONTROL,
                        tislrpv.PORCRET AS RET,
                        tcppc.CODIPROV
                FROM
                    tislrpv
                INNER JOIN tcppc ON tislrpv.CODIPROV = tcppc.CODIPROV
                WHERE
                    tcppc.CODIPROV = '$clie' AND NUMLEGAL !='' AND ".$not;
            
            $ret = $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','','_VENTOR');
            
            if(!empty($ret)){
                for($i=0;$i < count($ret);$i++){
                       $data=$this->islr_valide($ret[$i]['CODIPROV'],$ret[$i]['NUMLEGAL']);
                        if(!empty($data)){
                          $resp[$j]=array_merge($ret[$i],$data[0]);
                            $j++;
                        }
                }
            }
            
        }
        
        
        if(empty($resp)){
            $query="SELECT
                    tcppc.FORIEMI,
                    tislrpv.MONTO,
                    tcppc.NUMLEGAL,
                    tcppc.NUMCONTF AS CONTROL,
                    tislrpv.PORCRET AS RET,
                    tcppc.CODIPROV
                FROM
                    tislrpv
                INNER JOIN tcppc ON tislrpv.CODIPROV = tcppc.CODIPROV
                AND tcppc.NUMEDOCU = tislrpv.NUMEDOCU
                WHERE
                    tcppc.CODIPROV = '$clie' ";
            $resp = $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','','_VENTOR');
            for($i=0;$i < count($resp);$i++){
                $data=$this->islr_valide($resp[$i]['CODIPROV'],$resp[$i]['NUMLEGAL']);
                if(!empty($data)){
                  $resp[$i]=array_merge($resp[$i],$data[0]);  
                }
            }
        }
        //die(var_dump($resp));
        return $resp;
    }
    function get_proveedor(){
        $query="select nombre,proveedor from usuarios where ISNULL(proveedor)= 0 and proveedor != '0' ";
        return $this->_SQL_tool($this->SELECT, __METHOD__,$query);
    }
    
    function islr_valide($prov,$fac){
       // $link = mysql_connect('192.168.64.2:3307', 'estaciones', '123') or die('No se pudo conectar: ' . mysql_error());
        //mysql_select_db('ventoradm001') or die('No se pudo seleccionar la base de datos');
        $query="SELECT
                    tislrpv.MONTO AS ISLMONTO,
                IF (tislrpv.MONTO > 0, 1, 0) AS ISLR
                FROM
                    tcppc
                INNER JOIN tislrpv ON tcppc.CODIPROV = tislrpv.CODIPROV
                AND tcppc.NUMEDOCU = tislrpv.NUMEDOCU
                WHERE
                    tcppc.CODIPROV = '$prov'
                AND tcppc.NUMLEGAL = '$fac'
                GROUP BY
                    tcppc.NUMLEGAL";

           // $result = mysql_query($query) or die('Consulta fallida: ' . mysql_error());
            //$resp[0]=mysql_fetch_array($result);
           
        //    mysql_close($link);
       
        $resp = $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','','_VENTOR');
        //die(var_dump($resp));
        return $resp;
    }
    
    function dologi_ini($user,$pass){
        		 
        $url = "http://sistemasc.gdp.com.ve/soap_app/login.php";

        $postData = array($user,$pass,3);
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
        return $response;
    }
    function update_user($user,$pass){
        $query="update users set passuser='$pass', edituser=now() where codeuser='$user' ";
        return $this->_SQL_tool($this->UPDATE, __METHOD__,$query);
    }
    function update_foto($foto,$cod){
        $query="update users set fotouser='$foto' where codeuser='$cod' ";
        return $this->_SQL_tool($this->UPDATE, __METHOD__,$query);
    }
    function get_retencion_islr($prov,$fac){
        $query="SELECT
                    tcppc.FORIEMI,
                    tcppc.ABONOS,
                    tcppc.MONTEXEN,
                    tcppc.NUMCONTF,
                    tcppc.CODIRETE,
                    tcppc.FORILLE,
                    tcppc.MONTO AS TOTAL,
                    tcppc.MONTO AS PAGO,
                    tcppc.IMPUESTO,
                    tcppc.IMPU1,
                    tcppc.SUSTRAEN,
                    tcppc.RETENIDO,
                    tcppc.PORCRETE,
                    tcppc.MONRETVE,
                    tcppa.NOMBRE,
                    tcppa.DIRECCION1,
                    tcppa.DIRECCION2,
                    tcppa.RIF,
                    tcppa.TELEFONO,
                    tciua.NOMBCIUD AS CIUDAD,
                    testa.NOMBESTA AS ESTADO,
                    tislrpv.CODIRETE,
                    tislrpv.TIPODOCU,
                    tislrpv.SUSTRAEN,
                    tislrpv.MONTO,
                    timph.DESCIMPU AS DESCRIPCION,
                    tcppc.CODIPROV,
                    tcppc.NUMLEGAL,
                    tcppc.NUMCONTF AS CONTROL,
                    tislrpv.PORCRET AS RET
                FROM
                    tcppc
                LEFT JOIN tcppa ON tcppc.CODIPROV = tcppa.CODIPROV
                LEFT JOIN tciua ON tcppa.CODICIUD = tciua.CODICIUD
                LEFT JOIN testa ON tcppa.CODIESTA = testa.CODIESTA
                AND tciua.CODIESTA = testa.CODIESTA
                INNER JOIN tislrpv ON tcppc.CODIPROV = tislrpv.CODIPROV
                AND tcppc.NUMEDOCU = tislrpv.NUMEDOCU
                LEFT JOIN timph ON tcppc.CODIRETE = timph.CODIIMPU
                WHERE
                tcppc.CODIPROV = '$prov'
            AND tcppc.NUMLEGAL = '$fac'  group by tcppc.NUMLEGAL";
        return $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','','_VENTOR');
       //return $query;
    }
    function get_islr($prov,$fact){
        $query="select CODIPROV,NUMEDOCU,CODIRETE from tislrpv where NUMEDOCU='$fact' and ";
        return $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','','_VENTOR');
        $n=0;
        for($i=0;$i<count($record);$i++){
             if($record[$i]['NUMEDOCU'] == $fact && $record[$i]['CODIPROV'] == $prov){
                $rec['CODIPROV']= trim($record[$i]['CODIPROV']);
                $rec['NUMEDOCU']=trim($record[$i]['NUMEDOCU']);
                $rec['CODIRETE']=trim($record[$i]['CODIRETE']);
                $rec['TIPODOCU']=trim($record[$i]['TIPODOCU']);
                $rec['SUSTRAEN']=trim($record[$i]['SUSTRAEN']);
                $rec['MONTO']=trim($record[$i]['MONTO']);
            }
        }
        return $rec;
    }
     function get_cliente($login){
         $query="select NOMBRE,RIF,Codiclie as CODE,Direccion1 from tcpca where Codiclie = '$login' ";
       $resp= $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','','_VENTOR');
        $resp[0]['TIPO']="CL";
        return $resp;
        
    }
    function get_proveed($login){
        $query="select NOMBRE,RIF,Codiprov as CODE,Direccion1 from tcppa where Codiprov = '$login'";
        $resp= $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','','_VENTOR');
        $resp[0]['TIPO']="PR";
        return $resp;
    }
    function add_user($data){
        $nombre=$data[0]['NOMBRE'];
        $cod=$data[0]['CODE'];
        $rif=$data[0]['RIF'];
        $tipo=$data[0]['TIPO'];
         $query="insert into users(nameuser,codiuser,rifuser,tipouser,logiuser,passuser) values('$nombre','$cod','$rif','$tipo','$cod','bGFwcmluY2lwYWw=')";
        return $this->_SQL_tool($this->SELECT, __METHOD__,$query);
    }
}
?>