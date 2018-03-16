<?php
class Sincronizador extends Model{
    function get_sincronizar(){
        $query="SELECT
                    tvinculos_enc.VINN_IDAPLICACION,
                    tvinculos_enc.VINN_IDSESION,
                    tvinculos_enc.VINN_IDVINCULO,
                    tvinculos_enc.VINV_DESCRIPCION,
                    tvinculos_enc.VINV_CURSORORI,
                    tvinculos_enc.VINV_TABLADES,
                    tvinculos_enc.VINV_INDICES,
                    tvinculos_enc.VINV_BASEDATOS,
                    tvinculos_enc.VINV_PROCESO,
                    tvinculos_enc.VINV_EXTRACUR,
                    tvinculos_enc.VINV_SQLANTES,
                    tvinculos_enc.VINV_SQLDESPUES,
                    tconexion.CONV_DESCRIPCION,
                    tconexion.CONV_DATA,
                     IF(tconexion.CONV_DATA = 'ventoradm001','_VENTOR',
                    IF(tconexion.CONV_DATA = 'webs4gcom','_WEBS4GCOM',
                    IF(tconexion.CONV_DATA = 's4gcomweb','_S4GCOMWEB','')
                    )
                    ) as desde,
                    IF(tvinculos_enc.VINV_BASEDATOS = 'ventoradm001','_VENTOR',
                    IF(tvinculos_enc.VINV_BASEDATOS = 'webs4gcom','_WEBS4GCOM',
                    IF(tvinculos_enc.VINV_BASEDATOS = 's4gcomweb','_S4GCOMWEB','')
                    )
                    ) as para
                    FROM
                    tvinculos_enc
                    INNER JOIN tconexion ON tvinculos_enc.VINN_IDSESION = tconexion.CONN_IDCONEXION
               ORDER BY
	               tvinculos_enc.VINV_DESCRIPCION";
        
         return $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','','_SYNC');
    }
    
    function get_vinculo($id){
        $query="SELECT
                    tvinculos_enc.VINN_IDAPLICACION,
                    tvinculos_enc.VINN_IDSESION,
                    tvinculos_enc.VINN_IDVINCULO,
                    tvinculos_enc.VINV_DESCRIPCION,
                    tvinculos_enc.VINV_CURSORORI,
                    tvinculos_enc.VINV_TABLADES,
                    tvinculos_enc.VINV_INDICES,
                    tvinculos_enc.VINV_BASEDATOS,
                    tvinculos_enc.VINV_PROCESO,
                    tvinculos_enc.VINV_EXTRACUR,
                    tvinculos_enc.VINV_SQLANTES,
                    tvinculos_enc.VINV_SQLDESPUES,
                    tconexion.CONV_DESCRIPCION,
                    tconexion.CONV_DATA,
                    IF(tconexion.CONV_DATA = 'ventoradm001','_VENTOR',
                    IF(tconexion.CONV_DATA = 'webs4gcom','_WEBS4GCOM',
                    IF(tconexion.CONV_DATA = 's4gcomweb','_S4GCOMWEB','')
                    )
                    ) as desde,
                    IF(tvinculos_enc.VINV_BASEDATOS = 'ventoradm001','_VENTOR',
                    IF(tvinculos_enc.VINV_BASEDATOS = 'webs4gcom','_WEBS4GCOM',
                    IF(tvinculos_enc.VINV_BASEDATOS = 's4gcomweb','_S4GCOMWEB','')
                    )
                    ) as para
                    FROM
                    tvinculos_enc
                    INNER JOIN tconexion ON tvinculos_enc.VINN_IDSESION = tconexion.CONN_IDCONEXION
                    where VINN_IDVINCULO = '$id' ";
        
         return $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','','_SYNC');
    }
    function consultar($query,$db_search){
       // die(print_r($query));
        return $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','',$db_search);
        
    }
    function truncar($tabla,$db_search){
        $query="TRUNCATE TABLE ".$tabla."";
        $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','',$db_search);
    }
    function insertar($campos,$data,$tabla,$db_insert){
        $query.="insert into ".$tabla."(".$campos.")values";
        $values.='';
        foreach($data as $k => $d){
            $values.="(";
                foreach($data[$k] as $val){
                    $valor=addslashes($val);
                    $values.="'".$valor."',";
                }
            $values=substr($values, 0, -1);
            $values.="),";
        }
        $values=substr($values, 0, -1);
        $query=$query.' '.$values;
        $this->truncar($tabla,$db_insert);
        //die(print_r($query));
        return $this->_SQL_tool($this->INSERT, __METHOD__,$query,'','','mysql','',$db_insert);
    }
    function get_pedidos(){        
        //die("get_pedidos");
        $query="SELECT
                    tclientes.CLIEV_IDCLIENTE,
                    tclientes.CLIEV_RAZONSOC,
                    DATE_FORMAT(tpedidos_enc.pedid_fecha,'%Y-%m-%d') as fecha,
                    tpedidos_enc.pediv_numero,
                    tpedidos_enc.pediv_status,
                    tpedidos_reg.pregn_unidades,
                    tpedidos_reg.pregn_precio,
                    Sum(tpedidos_reg.pregn_cajas) AS cajas,
                    tpedidos_reg.pregn_impuesto,
                    Sum((tpedidos_reg.pregn_precio*tpedidos_reg.pregn_cajas)*(1+(tpedidos_reg.pregn_impuesto/100))) AS pedidocn_iva,
                    Sum((tpedidos_reg.pregn_precio*tpedidos_reg.pregn_cajas)*(tpedidos_reg.pregn_impuesto/100)) AS iva,
                    Sum(tpedidos_reg.pregn_precio*tpedidos_reg.pregn_cajas) AS pedidosn_iva,
                    tpedidos_enc.pediv_codivend,
                    DATEDIFF(NOW(),tpedidos_enc.pedid_fecha)  as caduca
                    FROM
                        tpedidos_enc
                    INNER JOIN tpedidos_reg ON tpedidos_enc.pediv_numero = tpedidos_reg.pregv_numero
                    INNER JOIN tclientes ON tpedidos_enc.pediv_idcliente = tclientes.CLIEV_IDCLIENTE
                     WHERE tpedidos_enc.pediv_status IN ('PENDIENTE', 'ACTUALIZADO')
                    GROUP BY tpedidos_enc.pediv_numero";
        //WHERE tpedidos_enc.pediv_status IN ('PENDIENTE', 'ACTUALIZADO')
        return $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','','_S4GCOMWEB');
    }
    function update_pedido($p,$status){
        $query="update tpedidos_enc set pediv_status='$status' where pediv_numero='$p' ";
        $this->_SQL_tool($this->UPDATE, __METHOD__,$query,'','','mysql','','_S4GCOMWEB');
        
        $query="update tpedidos_enc set pediv_status='$status' where pediv_numero='$p' ";
        $this->_SQL_tool($this->UPDATE, __METHOD__,$query,'','','mysql','','_WEBS4GCOM');
    }
    function updatefecha_pedido($p){
        $hoy=date("Y-m-d H:i:s");
        $query="update tpedidos_enc set pedid_fecha='$hoy' where pediv_numero='$p' ";
        $this->_SQL_tool($this->UPDATE, __METHOD__,$query,'','','mysql','','_S4GCOMWEB');
        $query="update tpedidos_reg set pregd_fecha='$hoy' where pregv_numero='$p' ";
        $this->_SQL_tool($this->UPDATE, __METHOD__,$query,'','','mysql','','_S4GCOMWEB');
        
        $query="update tpedidos_enc set pedid_fecha='$hoy' where pediv_numero='$p' ";
        $this->_SQL_tool($this->UPDATE, __METHOD__,$query,'','','mysql','','_WEBS4GCOM');
        $query="update tpedidos_reg set pregd_fecha='$hoy' where pregv_numero='$p' ";
        $this->_SQL_tool($this->UPDATE, __METHOD__,$query,'','','mysql','','_WEBS4GCOM');
    }
    function get_pedido($p){
        $query="SELECT
                    tpedidos_enc.pediv_numero,
                    tpedidos_enc.pediv_idcliente,
                    tpedidos_enc.pedid_fecha,
                    tpedidos_enc.pedin_diascredito,
                    tpedidos_enc.pediv_iddesglobal,
                    tpedidos_enc.pediv_comentario,
                    tpedidos_enc.pediv_status,
                    tpedidos_enc.pedid_envio,
                    tpedidos_enc.pediv_voucher,
                    tpedidos_enc.pediv_codivend,
                    tpedidos_reg.pregv_idproducto,
                    tpedidos_reg.pregv_idcliente,
                    tpedidos_reg.pregd_fecha,
                    tpedidos_reg.pregn_cajas,
                    tpedidos_reg.pregn_unidades,
                    tpedidos_reg.pregn_precio,
                    tpedidos_reg.pregn_descuento1,
                    tpedidos_reg.pregn_descuento2,
                    tpedidos_reg.pregn_impuesto,
                    tpedidos_reg.pregv_tipoprecio,
                    tpedidos_reg.pregb_actulizar,
                    tpedidos_reg.pregv_codivend,
                    tpedidos_reg.pregv_promo,
                    tarticulos.ARTV_DESCARTDETA,
                    tarticulos.ARTV_DESCART
                FROM
                    tpedidos_enc
                INNER JOIN tpedidos_reg ON tpedidos_enc.pediv_numero = tpedidos_reg.pregv_numero
                INNER JOIN tarticulos ON tpedidos_reg.pregv_idproducto = tarticulos.ARTV_IDARTICULO
                WHERE pediv_numero='$p' and tpedidos_enc.pediv_status IN ('PENDIENTE', 'ACTUALIZADO') ";
        return $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','','_S4GCOMWEB');
    }
    function validaruser($user){
        $query="SELECT
                    usuarios.nameuser,
                    departamentos.descripcion AS departamento,
                    usuarios.depauser,
                    cargos.descripcion AS cargo,
                    usuarios.carguser,
                    cargos.codecarg,
                    cargos.editped
                FROM
                    usuarios
                INNER JOIN departamentos ON usuarios.depauser = departamentos.codedept
                INNER JOIN cargos ON departamentos.codedept = cargos.codedpt
                AND usuarios.carguser = cargos.codecarg
                WHERE
                    logiuser = '$user' ";
       return $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','','_DBPRINCIPAL');
    }
    function insert_pedido($pedidos){
        foreach($pedidos as $p){
            $ped=str_pad(trim($p),8,0,STR_PAD_LEFT);
            $ped='C'.$ped;
            $pedido= $this->get_pedido($p);
            $encabezado=$pedido[0];
            $query="INSERT INTO tfacpeda (numepedi,fecha,codiruta,codiclie,diascred,codiglob,porcglob,mensaje,grupfact) VALUES 
            ('".$ped."','".$encabezado['pedid_fecha']."','".$encabezado['pediv_codivend']."','".$encabezado['pediv_idcliente']."',
            ".$encabezado['pedin_diascredito'].",'',0,'','1')";
            $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','','_VENTOR');
            foreach($pedido as $pd){
                $entero = number_format($pd['pregn_cajas'],0); 
                $decimales = abs(($pd['pregn_cajas']-$entero)*100);
                $unidades=$pd['pregn_unidades'];
                $precio=$pd['pregn_precio'];
                $desc1=$pd['pregn_descuento1'];
                $desc2=$pd['pregn_descuento2'];
                $imput=$pd['pregn_impuesto'];
                
                if($decimales == 0 || $decimales==75 ||  $decimales == 25 || $decimales == 50){
                    $cajas=$pd['pregn_cajas'];
                }else{
                    $cajas=0;
                }
                
                $Squery="INSERT INTO tfacpedb (codiclie,numepedi,fecha,codiprod,cajas,unidades,codiruta,precio,descuento,descuent2,impu1,tipoprec,grupfact,lote,deposito)VALUES ('".$encabezado['pediv_idcliente']."','".$ped."','".$encabezado['pedid_fecha']."','".$pd['pregv_idproducto']."',$cajas,$unidades,'".$encabezado['pediv_codivend']."', $precio,$desc1,$desc2,$imput,'".$pd['pregv_tipoprecio']."','1','1','01')";
               $this->_SQL_tool($this->SELECT, __METHOD__,$Squery,'','','mysql','','_VENTOR');
            }
        }
    }
    function consultarpedidos(){
        $query="SELECT
                    trim(tpedidos_enc.pediv_numero) as pedido,
                    trim(tclientes.CLIEV_RAZONSOC) as cliente
                FROM
                    tpedidos_enc
                INNER JOIN tclientes ON tpedidos_enc.pediv_idcliente = tclientes.CLIEV_IDCLIENTE
                WHERE
                    tpedidos_enc.pediv_status = 'PENDIENTE' ";
        
        if(isset($_SESSION['pedidos'])){
            $d='';
            foreach($_SESSION['pedidos'] as $p){
                $d.="'".$p."',";
            }
            $d=substr($d, 0, -1);   
            $query.=" and tpedidos_enc.pediv_numero NOT IN ($d)";
        }
        
        //return $query;
        return $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','','_S4GCOMWEB');
    }
    function consultar_reg($query,$db_search,$db_insert,$table,$id){
        $id=explode(',',$id);
        foreach($id as $d){
            $all.=" AND $d = '##".$d."##' ";
        }
        $vinc_desd= $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','',$db_search);
        $campos='';
        foreach($vinc_desd[0] as $key => $d){
            $campos.=$key.',';
            $pr_update.=$key.'= "##'.$key.'##" ,';
            $pr_insert.="'##$key##',";
            $cmp[]=$key;
        }
        $campos=substr($campos, 0, -1);
        $pr_update=substr($pr_update, 0, -1);
        $pr_insert=substr($pr_insert, 0, -1);
        $pr_insert="($pr_insert)";
        $lista=array();
        $p=0;
        
        foreach($vinc_desd as $k => $v){
            $query="Select ".$campos." from ".$table." where true $all ";
            $cond=" where true $all ";
            foreach($id as $d){
                $query=str_replace('##'.$d.'##',$v[$d],$query);
                $cond=str_replace('##'.$d.'##',$v[$d],$cond);
            }
            
            $lista[$p]['query']=$query;
            $lista[$p]['condicion']=$cond;
            $lista[$p]['tabla']=$table;
            $lista[$p]['campos']=$campos;
            $lista[$p]['database']=$db_insert;
            $lista[$p]['data']=$v;
            $vinc_para = $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','',$db_insert);
            if(!empty($vinc_para))
                $lista[$p]['method']='update';
            else
                $lista[$p]['method']='insert';
            
            $p++;
        }
        $upd=0;
        $ins=0;
        for($i=0;$i<count($lista);$i++){
            if($lista[$i]['method']=='insert'){
                 $sql= "insert into $table (".$lista[$i]['campos'].")values $pr_insert";
                    foreach($cmp as $d){
                      $sql=str_replace('##'.$d.'##',trim($lista[$i]['data'][$d]),$sql); 
                    }
                $this->_SQL_tool($this->INSERT, __METHOD__,$sql,'','','mysql','',$lista[$i]['database']);
                    $ins++;
            }elseif($lista[$i]['method']=='update'){
                $sql= "update $table set $pr_update ".$lista[$i]['condicion'];
                    foreach($cmp as $d){
                      $sql=str_replace('##'.$d.'##',trim($lista[$i]['data'][$d]),$sql); 
                    }
                $resp=$this->_SQL_tool($this->UPDATE, __METHOD__,$sql,'','','mysql','',$lista[$i]['database']);
                
                    $upd++;
            }
            
        }
        return $ins." Registros Agregados \n".$upd." Registros actualizados";
       // $vinc_desd= $this->_SQL_tool($this->SELECT, __METHOD__,$query,'','','mysql','',$db_insert);
    }
}
?>
