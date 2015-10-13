<?php
$server="";
$username="admin";
$password="";
$port="";
$conn = new Mongo("mongodb://admin:manglayang2010@localhost:27017");
if($conn)
{
    $seldb=$conn->selectDB("Assets");
    if($seldb)
    {
        $selectdata=$seldb->selectCollection("Avatar")->find();
        $hasildata=array();
        foreach($selectdata as $result)
        {
            $categori=(!isset($result['category']))?"":$result['category'];
            $tipe=(!isset($result['tipe']))?"":$result['tipe'];
            $hasildata[]=array(
                'tipe'=>$tipe,
                'category'=>$categori,
            );
        }
//        echo "<pre>";
//        var_dump($hasildata);
//        echo "</pre>";
        foreach($hasildata as $result)
        {
            if($result['tipe']!='')
            {
                $cektipe=$seldb->selectCollection("AvatarBodyPart")->find(array("name"=>$result['tipe']));
                if($cektipe->count()<1)
                {
                    $seldb->selectCollection("AvatarBodyPart")->insert(array("name"=>$result['tipe']));
                }
            }
        }
        foreach($hasildata as $result)
        {
            if($result['category']!='' && $result['tipe']!='')
            {
                $cektipe=$seldb->selectCollection("Category")->find(array("name"=>$result['category'],"tipe"=>$result['tipe']));
                if($cektipe->count()<1)
                {
                    $seldb->selectCollection("Category")->insert(array("name"=>$result['category'],"tipe"=>$result['tipe']));
                }
            }
        }
    }
    else
    {
        die('Database tidak ditemukan');
    }
}
else
{
    die("Gagal koneksi");
}

?>