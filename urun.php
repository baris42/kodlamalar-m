<?php include_once ('ust.php');
error_reporting(E_ALL);

/*
 * ekleme ve düzenleme için kodlamalar
 * */
function klasorsil($klasor){
    if (substr($klasor, -1) != '/')
        $klasor .= '/';
    if ($handle = opendir($klasor)) {
        while ($obj = readdir($handle)) {
            if ($obj!= '.' && $obj!= '..') {
                if (is_dir($klasor.$obj)) {
                    if (!klasorsil($klasor.$obj))
                        return false;
                }elseif (is_file($klasor.$obj)) {
                    if (!unlink($klasor.$obj))
                        return false;
                }
            }
        }
        closedir($handle);
        if (!@rmdir($klasor))
            return false;
        return true;
    }
    return false;
}/*AYAR Çalışalacak Veritabanı adı */
$dbtable="urun";
/*AYAR  resimleri yüklemesi yapılacak adres */
$image_folder_web = DIR.'images/urun/';
$image_folder_real = PATH.'images/urun/';
/*Silme işlemi için geçici onay kodu üret */
/*ekleme ve düzenleme için kodlar son*/
//urun tablosundan veri çek
$urunler_sorgu = $db->prepare("SELECT * FROM urun ");
$urunler_sorgu->execute();
$urunler =$urunler_sorgu->fetchAll(PDO::FETCH_ASSOC);
//urun kategorisini çek verisini çek
$urun_kategori_sorgu= $db->prepare("SELECT * FROM urun_kategori");
$urun_kategori_sorgu->execute();
$urunkategori=$urun_kategori_sorgu->fetchAll(PDO::FETCH_ASSOC);
//çerçeve şeklini çek
//urun kategorisini çek verisini çek
$cerceve_sekli_sorgu= $db->prepare("SELECT DISTINCT cerceve_sekli FROM urun");
$cerceve_sekli_sorgu->execute();
$cerceve_sekli=$cerceve_sekli_sorgu->fetchAll(PDO::FETCH_ASSOC);
//marka çek verisini çek
$marka_sorgu= $db->prepare("SELECT DISTINCT marka FROM urun");
$marka_sorgu->execute();
$marka=$marka_sorgu->fetchAll(PDO::FETCH_ASSOC);
//etiket
//urun kategorisinide virgül ile girilen çklu etiketlerin aynı olanlarını eliyor virgülden sonra arraye eviriyoru yine aynı olanları eliyor. müthiş...
$etiket_sorgu= $db->prepare("SELECT DISTINCT etiket FROM urun");
$etiket_sorgu->execute();
$etiketler=$etiket_sorgu->fetchAll(PDO::FETCH_ASSOC);
foreach( $etiketler as $donguetiket ):
    $etiketListesi[]=$donguetiket['etiket'];
endforeach;
@$etiketListesi=array_unique(explode(',', implode(',',$etiketListesi)));

//diger tablo verilerini çek
/*if(!empty($_GET['bagisturuid'])) {
    //bagisyeri tablosundan veri çek
    $bagisturuid = htmlspecialchars(trim($_GET['bagisturuid']));
    $bagisyeri_sorgu = $db->prepare("SELECT * FROM bagisyeri WHERE bagisturuid=:bagisturuid");
    $bagisyeri_sorgu->execute(array(
        ':bagisturuid' => $bagisturuid
    ));
    $bagisyeri_get_row = $bagisyeri_sorgu->fetchAll(PDO::FETCH_ASSOC);


}*/
//şuna için genel sorgu


/*rapor sayfası için filtreleme kodu */
if(isset($_GET['start'])){
      /*start degiskenini  Y-m-d olarak güncelliyoruz ve -1 yapıyoruz*/
     $start=htmlspecialchars(trim(setdate($_GET['start'],'Y-m-d','-0 day')));
     $end=htmlspecialchars(trim(setdate($_GET['end'],'Y-m-d','+0 day')));
     //bagisturuid yukarıda tanımlı
    @$GETmarka=htmlspecialchars(trim($_GET['marka']));
    @$GETurun_kategori=htmlspecialchars(trim($_GET['urun_kategori']));
    @$GETcinsiyet=htmlspecialchars(trim($_GET['cinsiyet']));
    @$GETcocuk=htmlspecialchars(trim($_GET['cocuk']));
    @$GETcerceve_sekli=htmlspecialchars(trim($_GET['cerceve_sekli']));
    @$GETetiket=htmlspecialchars(trim($_GET['etiket']));

    //2020 sql modifier
        $sql[]="SELECT *FROM urun WHERE(id>'0') ";
       if(!empty($GETmarka)) $sql[]= " and(marka='".$GETmarka."') "; $var=true;
       if(!empty($GETurun_kategori)) $sql[]= " and(urun_kategori_id='".$GETurun_kategori."') "; $var=true;
       if(!empty($GETcinsiyet)) $sql[]= " and(cinsiyet='".$GETcinsiyet."') "; $var=true;
       if(!empty($GETcocuk)) $sql[]= " and(cocuk_mu='".$GETcocuk."') "; $var=true;
       if(!empty($GETcerceve_sekli)) $sql[]= " and(cerceve_sekli='".$GETcerceve_sekli."') "; $var=true;
       if(!empty($GETetiket)) $sql[]= " and(etiket LIKE '".'%'.$GETetiket.'%'."') "; $var=true;
       if(!empty($_GET['start'])) $sql[]= "and(tarih  BETWEEN '".$start."' AND '".$end."')  "; $var=true;
         $sql[]=" ORDER BY id ASC";
 //echo implode($sql);
}
if(@$var){
    $query = $db->query(implode($sql), PDO::FETCH_ASSOC);
}else{
    $query = $db->query("SELECT * FROM urun ORDER BY id ASC LIMIT 5 ", PDO::FETCH_ASSOC);
}
/*ÜRün Ekleme Kısmı*/
if (isset($_POST['urunekle'])) {


    if (!isset($_POST['marka'])) $error[] = "marka Alanı Boş Bırakılmaz";
    if (!isset($_POST['baslik'])) $error[] = "baslik alanı Boş Bırakılamaz";
    if (!isset($_POST['kategori'])) $error[] = "kategori alanı Boş Bırakılamaz";
    if (!isset($_POST['urunkodu'])) $error[] = "urunkodu alanı Boş Bırakılamaz";
    //if (!isset($_POST['barkod'])) $error[] = "barkod alanı Boş Bırakılamaz";
    if (!isset($_POST['cinsiyet'])) $error[] = "cinsiyet alanı Boş Bırakılamaz";
    if (!isset($_POST['cocukmu'])) $error[] = "cocuk_mu alanı Boş Bırakılamaz";
    //if (!isset($_POST['etiket'])) $error[] = "etiket alanı Boş Bırakılamaz";
    if (!isset($_POST['durum'])) $error[] = "durum alanı Boş Bırakılamaz";
    if (!isset($_POST['stokadet'])) $error[] = "stok_adet alanı Boş Bırakılamaz";
    if (!isset($_POST['alisfiyati'])) $error[] = "alis_fiyati alanı Boş Bırakılamaz";
    if (!isset($_POST['satisfiyati'])) $error[] = "satis_fiyati alanı Boş Bırakılamaz";
    //if (!isset($_POST['degrade'])) $error[] = "degrade alanı Boş Bırakılamaz";
    //if (!isset($_POST['polarize'])) $error[] = "polarize alanı Boş Bırakılamaz";
   // if (!isset($_POST['camrengi'])) $error[] = "cam_rengi alanı Boş Bırakılamaz";
   // if (!isset($_POST['urunekartman'])) $error[] = "urun_ekartmani alanı Boş Bırakılamaz";
    //if (!isset($_POST['cammateryal'])) $error[] = "cam_materyal alanı Boş Bırakılamaz";
    if (!isset($_POST['cercevesekli'])) $error[] = "cerceve_sekli alanı Boş Bırakılamaz";
    //if (!isset($_POST['aciklama'])) $error[] = "aciklama alanı Boş Bırakılamaz";
   // if (!isset($_POST['not'])) $error[] = "not alanı Boş Bırakılamaz";


    if (!isset($error)) {

        $urunkodu=htmlspecialchars(trim($_POST['urunkodu']));


        $file_name = $_FILES['photo']['name'];
        $file_type = $_FILES['photo']['type'];
        $file_size = $_FILES['photo']['size'];
        $file_tname = $_FILES['photo']['tmp_name'];
        $file_error = $_FILES['photo']['error'];

        /*Dosyanın Yüklenecegi kısım */
        $image_folder = PATH.'images/urun/';

        $file_ext = substr($file_name, strripos($file_name, '.')); // dosyadan sonra uzantıyı almak için



// check file ext. gif, jpeg, pjpeg, png

        if (($file_type == "image/gif") || ($file_type == "image/jpeg") || ($file_type == "image/jpg") || ($file_type == "image/png") && ($file_size < 20000)) {

            if ($file_error > 0) {

                $error[] = "Resim dosyası degil";

            } else {
                $imgfile = file_get_contents($file_tname);

                //todo işlemi mysqle base64 olarak kaydedecekcek bu degeri kulanacagız .
                /* $base64 = chunk_split(base64_encode(file_get_contents($file_tname)));*/

                $newfilename = $urunkodu.'_'.rand(1000,9999).$file_ext;

                try {

                    $stmt = $db->prepare("INSERT INTO urun (marka, baslik, urun_kategori_id, urunkodu, barkod, cinsiyet, cocuk_mu, etiket, durum, stok_adet, satis_fiyati, alis_fiyati, img, degrade, polarize, cam_rengi, urun_ekartman, cam_materyal, cerceve_sekli, aciklama, notlar ) value (:marka,:baslik,:urun_kategori_id,:urunkodu,:barkod,:cinsiyet,:cocuk_mu,:etiket,:durum,:stok_adet,:satis_fiyati,:alis_fiyati,:img,:degrade,:polarize,:cam_rengi,:urun_ekartman,:cam_materyal,:cerceve_sekli,:aciklama,:notlar)");
                    $stmt->execute(array(
                        ':marka'=>$_POST['marka'],
                        ':baslik'=>$_POST['baslik'],
                        ':urun_kategori_id'=>$_POST['kategori'],
                        ':urunkodu'=>$urunkodu,
                        ':barkod'=>$_POST['barkod'],
                        ':cinsiyet'=>$_POST['cinsiyet'],
                        ':cocuk_mu'=>$_POST['cocukmu'],
                        ':etiket'=>$_POST['etiket'],
                        ':durum'=>$_POST['durum'],
                        ':stok_adet'=>$_POST['stokadet'],
                        ':satis_fiyati'=>$_POST['satisfiyati'],
                        ':alis_fiyati'=>$_POST['alisfiyati'],
                        ':img'=>$newfilename,
                        ':degrade'=>$_POST['degrade'],
                        ':polarize'=>$_POST['polarize'],
                        ':cam_rengi'=>$_POST['camrengi'],
                        ':urun_ekartman'=>$_POST['urunekartman'],
                        ':cam_materyal'=>$_POST['cammateryal'],
                        ':cerceve_sekli'=>$_POST['cercevesekli'],
                        ':aciklama'=>$_POST['aciklama'],
                        ':notlar'=>$_POST['not']









                    ));
                    $id = $db->lastInsertId('id');
                } catch (PDOException $e) {
                    $error[] = $e->getMessage();

                }


//check if image exists in the folder

                if (file_exists($image_folder . $file_name)) {
                    $error[] = "Dosya Mevcut";
                } else {
//if all is ok upload image in image folder

                    $success = move_uploaded_file($file_tname, $image_folder . $newfilename);

                    if (!isset($error))  {
                        /*AYAR  yönlendirme adresi*/
                        header('Location: urun.php?action=basarili');

                    } else {
                        $error[] = "chmod ayarını kontrol et";
                        echo "<script> alert(' Dosya kopyalanırken hata oluştu Aynı dosya mevcut olabilir..'); </script>";
                        /*AYAR  yönlendirme adresi*/
                        //header('Location: urun.php?action=hata');

                    }
                }
            }
        } else {
            $error[] = "Resim Dosyası Degil";
            echo "<script> alert(' Dosya formatı desteklenmiyor Sadece JPG uzantılı.'); </script>";
            /*AYAR  yönlendimre adresi*/
          //  header('Location: urun.php?action=hata');
        }

    }}
/*ÜRün Ekleme Kısmı son*/
/*edit ürün düzenleme*/
/*Satır DÜzenleme */
if (isset($_POST['edit'])) {


    if (!isset($_POST['id'])) $error[] = "ID alanı Boş Bırakılamaz";
    if (!isset($_POST['marka'])) $error[] = "marka Alanı Boş Bırakılmaz";
    if (!isset($_POST['baslik'])) $error[] = "baslik alanı Boş Bırakılamaz";
    if (!isset($_POST['kategori'])) $error[] = "kategori alanı Boş Bırakılamaz";
    if (!isset($_POST['urunkodu'])) $error[] = "urunkodu alanı Boş Bırakılamaz";
    if (!isset($_POST['barkod'])) $error[] = "barkod alanı Boş Bırakılamaz";
    if (!isset($_POST['cinsiyet'])) $error[] = "cinsiyet alanı Boş Bırakılamaz";
    if (!isset($_POST['cocukmu'])) $error[] = "cocukmu alanı Boş Bırakılamaz";
    if (!isset($_POST['degrade'])) $error[] = "degrade alanı Boş Bırakılamaz";
    if (!isset($_POST['polarize'])) $error[] = "polarize alanı Boş Bırakılamaz";
    if (!isset($_POST['cercevesekli'])) $error[] = "cercevesekli alanı Boş Bırakılamaz";
    if (!isset($_POST['aciklama'])) $error[] = "barkod alanı Boş Bırakılamaz";
    if (!isset($_POST['durum'])) $error[] = "barkod alanı Boş Bırakılamaz";

    /*herhangi bir post degerinde hata yoksa eger*/
    if (!isset($error)) {
        $editid= trim($_POST['id']);



        try {

            $stmt = $db->prepare("UPDATE urun SET marka = :marka,baslik = :baslik,urun_kategori_id = :urun_kategori_id,urunkodu = :urunkodu,barkod=:barkod,cinsiyet=:cinsiyet,cocuk_mu=:cocuk_mu,degrade=:degrade,polarize=:polarize,cerceve_sekli=:cerceve_sekli,aciklama=:aciklama,notlar=:notlar,durum=:durum WHERE id = :id");
            $stmt->execute(array(
                ':marka' => $_POST['marka'],
                ':baslik' => $_POST['baslik'],
                ':urun_kategori_id' => $_POST['kategori'],
                ':urunkodu' => $_POST['urunkodu'],
                ':barkod' => $_POST['barkod'],
                ':cinsiyet' => $_POST['cinsiyet'],
                ':cocuk_mu' => $_POST['cocukmu'],
                ':degrade' => $_POST['degrade'],
                ':polarize' => $_POST['polarize'],
                ':cerceve_sekli' => $_POST['cercevesekli'],
                ':aciklama' => $_POST['aciklama'],
                ':notlar' => $_POST['notlar'],
                ':durum' => $_POST['durum'],
                ':id' => $editid
            ));
            //redirect to index page
            header('Location: urun.php?action=basarili');
            exit;

        } catch(PDOException $e) {
            $error[] = $e->getMessage();

        }



    }}
/*ürün düzenleme*/
/*stok ve ürün kartı*/
if (isset($_POST['guncelle'])) {


    if (!isset($_POST['id'])) $error[] = "ID alanı Boş Bırakılamaz";
    if (!isset($_POST['stok_adet'])) $error[] = "stok_adet Alanı Boş Bırakılmaz";
    if (!isset($_POST['durum'])) $error[] = "durum alanı Boş Bırakılamaz";
    if (!isset($_POST['alis_fiyati'])) $error[] = "alis_fiyati alanı Boş Bırakılamaz";
    if (!isset($_POST['satis_fiyati'])) $error[] = "satis_fiyati alanı Boş Bırakılamaz";
    if (!isset($_POST['etiket'])) $error[] = "etiket alanı Boş Bırakılamaz";
    if (!isset($_POST['notlar'])) $error[] = "notlar alanı Boş Bırakılamaz";


    /*herhangi bir post degerinde hata yoksa eger*/
    if (!isset($error)) {
        $editid= trim($_POST['id']);



        try {

            $stmt = $db->prepare("UPDATE urun SET stok_adet = :stok_adet,durum = :durum,alis_fiyati = :alis_fiyati,satis_fiyati = :satis_fiyati,etiket=:etiket,notlar=:notlar WHERE id = :id");
            $stmt->execute(array(
                ':stok_adet' => $_POST['stok_adet'],
                ':durum' => $_POST['durum'],
                ':alis_fiyati' => $_POST['alis_fiyati'],
                ':satis_fiyati' => $_POST['satis_fiyati'],
                ':etiket' => $_POST['etiket'],
                ':notlar' => $_POST['notlar'],
                ':id' => $editid
            ));
            //redirect to index page
            header('Location: urun.php?action=basarili');
            exit;

        } catch(PDOException $e) {
            $error[] = $e->getMessage();

        }



    }}
/*stok ve ürün kartı son*/
/*Silme Kodları*/
if (isset($_POST['delete'])) {

    if (!isset($_POST['id'])) $error[] = "ID alanı Boş Bırakılamaz";
    if (!isset($_POST['onaykodu'])){
        $error[] = "Onay Kodu Alanı Boş Bırakılmaz";
    }else{
        if($_POST['onay'] != $_POST['onaykodu']) $error[] = "Onay Kodları Uyuşmuyor";
    }




    /*herhangi bir hata yoksa */
    if (!isset($error)) {
        $id=$_POST['id'];

        try{
            $unlik = $db->query("SELECT * FROM $dbtable WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
            unlink($image_folder_real.$unlik['img']);
            $rmdir=PATH.'images/urun_detay/'.$id.'/';
            klasorsil($rmdir);

            $query = $db->prepare("DELETE FROM $dbtable WHERE id = :id");
            $delete = $query->execute(array(
                'id' => $id
            ));
            /*AYAR  yönlendirlecek sayfa*/
            header('Location: urun.php?action=basarili');

        } catch (PDOException $e) { $error[]=$e->getMessage();}

    }/*isset error*/


}
?>
<link href="assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/datatables/dataTables.colVis.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/datatables/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/datatables/fixedColumns.dataTables.min.css" rel="stylesheet" type="text/css"/>
<!-- date time picker -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-material-datetimepicker/2.7.1/css/bootstrap-material-datetimepicker.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
<!-- datetimepicker -->
    <div class="content-page">
        <!-- Start content -->
        <div class="content">
            <div class="container">

                <!-- Page-Title -->


                <form name="arama"  id="arama" method="get" action="">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box">
                            <h4 class="m-t-0 m-b-20 header-title"><b>Ürün Yönetimi --</b></h4>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">

                                        <label class="col-lg-4 control-label" style="top: 10px;">Tarih Aralığı</label>
                                        <div class="input-daterange input-group" >
                                            <input type="text" class="form-control" id="date" data-date-format="yyyy-mm-dd"  name="start"  placeholder="Başlangıç Tarihi" value="<?php if(isset($_GET['start'])) {echo @$_GET['start'];} ?>">
                                            <span class="input-group-addon bg-custom b-0 text-white">ile</span>
                                            <input type="text" class="form-control" id="date2"  data-date-format="dd-mm-yyyy" placeholder="Bitis Tarihi" name="end" value="<?php if(isset($_GET['end'])) {echo @$_GET['end'];}?>">
                                        </div>

                                    </div>
                                   </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label"style="top: 10px;" >Marka</label>
                                        <div class="input-daterange input-group" >
                                            <select name="marka" id="marka"  onchange="form.submit();" class="selectpicker form-control"  data-style="btn-white"  >
                                                <option  value="">Seçim Yapınız.</option>
                                                <?php
                                                foreach( $marka as $donguMarka ):   ?>
                                                    <option value="<?= $donguMarka['marka'];?>" <?php if($donguMarka['marka']== @$_GET['marka']){echo 'selected';} ?>><?= $donguMarka['marka']; ?></option>
                                                <?php endforeach;?>
                                                <option value="">Hepsi</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label"style="top: 10px;" >Ürün Kategori</label>
                                        <div class="input-daterange input-group" >
                                            <select name="urun_kategori" id="urun_kategori"  onchange="form.submit();" class="selectpicker form-control"  data-style="btn-white"  >
                                                <option  value="">Seçim Yapınız.</option>
                                                <?php
                                                foreach( $urunkategori as $donguUrunKategori ):   ?>
                                                    <option value="<?= $donguUrunKategori['id'];?>" <?php if($donguUrunKategori['id'] == @$_GET['urun_kategori']){echo 'selected';} ?>><?= $donguUrunKategori['baslik']; ?></option>
                                                <?php endforeach;?>
                                                <option value="">Hepsi</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label"style="top: 10px;" >Cinsiyet</label>
                                        <div class="input-daterange input-group" >
                                            <select name="cinsiyet" class="selectpicker form-control"  onchange="form.submit();"  data-style="btn-white"  >
                                                <option value="<?php  if(!empty($_GET['cinsiyet'])){echo $_GET['cinsiyet'];}else{echo '';};?>" selected><?php  if(!empty($_GET['cinsiyet'])){echo $_GET['cinsiyet'];}else{echo 'Seçim Yapınız';};?></option>
                                                <option value="unisex">Unisex</option>
                                                <option value="erkek">Erkek</option>
                                                <option value="kadin">Kadın</option>
                                                <option value="">Hepsi</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label"style="top: 10px;" >Çocuk ?</label>
                                        <div class="input-daterange input-group" >
                                            <select name="cocuk" class="selectpicker form-control"  onchange="form.submit();"  data-style="btn-white"  >
                                                <option value="<?php  if(!empty($_GET['cocuk'])){echo $_GET['cocuk'];}else{echo '';};?>" selected><?php  if(!empty($_GET['cocuk'])){echo $_GET['cocuk'];}else{echo 'Seçim Yapınız';};?></option>
                                                <option value="evet">Evet</option>
                                                <option value="hayir">Hayır</option>
                                                <option value="">Hepsi</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label"style="top: 10px;" >Çerçeve</label>
                                        <div class="input-daterange input-group" >
                                            <select name="cerceve_sekli" id="cerceve_sekli"  class="selectpicker form-control"  onchange="form.submit();" data-style="btn-white" >
                                                <option value="">Seçim Yapınız.</option>
                                                <?php
                                                foreach( $cerceve_sekli as $dongucerceve_sekli ):   ?>
                                                    <option value="<?= $dongucerceve_sekli['cerceve_sekli'];?>" <?php if($dongucerceve_sekli['cerceve_sekli'] == @$_GET['cerceve_sekli']){echo 'selected';} ?>><?= $dongucerceve_sekli['cerceve_sekli']; ?></option>
                                                <?php endforeach;?>
                                                <option value="">Hepsi</option>

                                            </select>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label"style="top: 10px;" >Etiket</label>
                                        <div class="input-daterange input-group" >
                                            <select name="etiket" id="cerceve_sekli"  class="selectpicker form-control"  onchange="form.submit();" data-style="btn-white" >
                                                <option value="">Seçim Yapınız.</option>
                                                <?php
                                                foreach( $etiketListesi as $donguetiket ):   ?>
                                                    <option value="<?= $donguetiket;?>" <?php if($donguetiket == @$_GET['etiket']){echo 'selected';} ?>><?= $donguetiket; ?></option>
                                                <?php endforeach;?>
                                                <option value="">Hepsi</option>

                                            </select>
                                        </div>

                                    </div>
                                </div>


                                <div class="col-lg-4">
                                    <div class="form-group">
                                           <div class="input-daterange input-group" >
                                                <button type="submit"  name="arama" value="arama" class="btn btn-purple waves-effect waves-light">Filtre Uygula</button>
                                               <a href="urun.php">
                                                   <button type="button" class="btn btn-warning waves-effect waves-light">Temizle</button> </a>
                                               <a href="#custom-modal" data-animation="fadein" data-plugin="custommodal"
                                                  data-overlaySpeed="200" data-overlayColor="#36404a">
                                                   <button type="button" class="btn btn-info waves-effect waves-light">Yeni Ürün Ekle</button> </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table  id="datatable-buttons" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th data-field="marka" data-sortable="true" >Marka</th>
                                    <th data-field="baslik" data-sortable="true" >Ürün Adı</th><!--baslik--> <!-- ürün kartı  açılacak -->
                                    <th data-field="kategori" data-sortable="true" >Kategori</th>
                                    <th data-field="barkod" data-sortable="true" >Ürün Kodu/Barkod</th>
                                    <th data-field="cinsiyet" data-sortable="true" >Cinsiyet/Cocuk?</th>
                                    <th data-field="stok" data-sortable="true" >Durum/Stok</th>
                                    <th data-field="fiyat" data-sortable="true">Alış/Satış Fiyatı</th>
                                    <th data-field="satilan" data-sortable="true">Satılan</th>
                                    <th data-field="tarih" data-sortable="true">En Son İşlem</th>
                                    <th data-field="ozellik" data-sortable="true">Ürün Özellikleri</th><!--img-img2-degrade-polarize-camrengi-ürünekartmani-cammateryal-cerceve sekli-aciklama-not-->
                                    <th data-field="islem" data-sortable="true">İşlemler</th><!-- stokgüncelle-ürün bilgisi güncelle,album işlemleri,etiket bilgileri güncelle-->

                                </tr>
                                </thead>



                                <tbody>
                                <?php

                                        if ( $query->rowCount() ){
                                        foreach( $query as $row ){  $toplam[]=$row['stok_adet']; ?>
											<tr>
                                                <td> <?= $row['marka'];?></td>
                                                <td> <a  href="#satilanurunler?id=<?= $row['id'] ?>"><?= $row['baslik'];?></a> </td>
                                                <td>
                                                   <?php foreach( $urunkategori as $donguUrunKategori ):
                                                    if($donguUrunKategori['id'] == $row['urun_kategori_id']){echo $donguUrunKategori['baslik'];}
                                                    endforeach; ?>
                                                </td>
                                                 <td> <?= $row['urunkodu'].'/'.$row['barkod'];?></td>
                                                <td> <?= $row['cinsiyet'].'/'.$row['cocuk_mu'];?></td>
                                                <td> <?= $row['durum'].'/'.$row['stok_adet'];?></td>
                                                <td> <?= $row['alis_fiyati'].'/'.$row['satis_fiyati'].'₺';?></td>
                                                <td> <?=$row['satilan']; ?></td>
                                                <td> <?= setdate($row['tarih'],'d-m-Y H:i:s','+0 day'); ?></td>
                                                <td> <?= 'ürün özellikleri';?></td>
                                                <td>
                                                    <a href="?id=<?= $row['id'].'&album=urun_detay';?>" >
                                                        <button style="padding: 8px 3px;" type="button" class="btn btn-blue waves-effect waves-light"><i class="md md-insert-photo"></i></button></a>
                                                    <a href="?id=<?= $row['id'].'&stok';?>"  >
                                                        <button style="padding: 8px 3px;" type="button" class="btn btn-blue waves-effect waves-light"><i class="md md-store"></i></button></a>
                                                    <a href="?id=<?= $row['id'].'&edit';?>" >
                                                        <button style="padding: 8px 3px;"  type="button" class="btn btn-blue waves-effect waves-light"><i class="md md-settings"></i></button></a>
                                                   </td>

											</tr>

                                        <?php } }?>

                                </tbody>
                                <tfoot>
                                <tr>

                                    <th ></th>
                                    <th ></th>
                                    <th ></th>
                                    <th ></th>
                                    <th ></th>
                                    <th ></th>
                                    <th ></th>
                                    <th >Toplam</th>
                                    <th >Ürün</th>

                                  <?php  @$sum=number_format(array_sum($toplam), 0, ',', '.'); ?>
                                    <th ><?= @$sum.' adet'; ?></th>

                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>


            </div> <!-- container -->

        </div> <!-- content -->


    </div>
<!--Modal Kodları Buradan Başlıyor -->
<?php
if(isset($_GET['id'])){
    $gosterid=htmlspecialchars(trim($_GET['id']));
    if(is_numeric($gosterid)){
        $stmt = $db->prepare('SELECT * FROM urun WHERE id = :id');
        $stmt->execute(array(':id' => $gosterid));
        $goster = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
$dosyayolu = '/images//urun/';

?>
<!-- Modal -->
<div id="custom-modal" class="modal-demo">
    <button type="button" class="close" onclick="Custombox.close();">
        <span>&times;</span><span class="sr-only">Kapat</span>
    </button>
    <h4 class="custom-modal-title">Urun Ekle</h4>
    <div class="custom-modal-text text-left">
        <form action="" name="urunekle" method="post" enctype="multipart/form-data">

            <!-- Hatayı Görüntüle modal içinde -->
            <?php if(isset($error)){
                foreach($error as $erroryaz){
                    echo "  <div class='alert alert-danger'><p>'.$erroryaz.'</p> </div>";

                }
            } ?>
            <!-- Hatayı Görüntüle modal içinde -->

            <div class="form-group">
                <label for="photo1">Ürün Resmi</label>
                <input type="file"  name="photo" class="form-control" id="img" title="Resim Seciniz." accept="image/jpeg" placeholder="Resim Seçiniz." required>
            </div>

            <div class="form-group">
                <label for="exampleInputEmail1">Marka</label><!--TODO markaları db den çekecek ama ekleme yapılabilecek  -->
                <input type="text"  pattern="[a-zA-Z0-9]+" title="Türkçe karakter ve boşluk olmamalı!"  name="marka" class="form-control" id="exampleInputEmail1" placeholder="Marka" required>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Ürün Adı</label><!--TODO markaları db den çekecek ama ekleme yapılabilecek  -->
                <input type="text"   name="baslik" class="form-control" id="exampleInputEmail1" placeholder="Ürün Adı" required>
            </div>
            <div class="form-group">
                <label for="position">Ürün Kategori</label>
                <select  name="kategori" class="form-control" required="required">
                    <option value="">Seçim Yapınız </option>
                    <?php foreach( $urunkategori as $donguUrunKategori ):   ?>
                    <option value="<?= $donguUrunKategori['id'];?>" <?php if($donguUrunKategori['id'] == @$_GET['urun_kategori']){echo 'selected';} ?>><?= $donguUrunKategori['baslik']; ?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Ürün Kodu</label>
                <input type="text"  pattern="[a-zA-Z0-9]+" title="Türkçe karakter ve boşluk olmamalı!"  name="urunkodu" class="form-control" id="exampleInputEmail1" placeholder="Ürün Kodu" required>
            </div>

            <div class="form-group">
                <label for="position">Barkod</label>
                <input type="text"   pattern="[a-zA-Z0-9]+" title="Türkçe karakter ve boşluk olmamalı!" name="barkod" class="form-control" id="position" placeholder="Ürün Açıklaması">
            </div>

            <div class="form-group">
                <label for="position">Cinsiyet</label>
                <select  name="cinsiyet" class="form-control" required="required">
                    <option value="">Seçim Yapınız </option>
                    <option value="unisex">Unisex</option>
                    <option value="erkek">Erkek</option>
                    <option value="kadin">Kadın</option>
                </select>
            </div>
            <div class="form-group">
            <label for="position">Çocuk Ürünümü</label>
            <select  name="cocukmu" class="form-control" required="required">
                <option value="">Seçim Yapınız </option>
                <option value="evet">Evet</option>
                <option value="hayir">Hayır</option>
            </select>
              </div>
            <div class="form-group">
                <label for="position">Etiket(Virgül ile ayırınız)</label>
                <input type="text"  pattern="[a-zA-Z0-9,]+" title="Türkçe karakter ve boşluk olmamalı!" name="etiket" class="form-control" id="position" placeholder="yenisezon,outlet,indirim">
            </div>
            <div class="form-group">
                <label for="position">Durum(Aktif Bir ürün mü )</label>
                <select  name="durum" class="form-control" required="required">
                    <option value="">Seçim Yapınız </option>
                    <option value="evet">Evet</option>
                    <option value="hayir">Hayır</option>
                </select>
            </div>
            <div class="form-group">
                <label for="position">Stok Adedi</label>
                <input type="number"  pattern="[a-zA-Z0-9]+" title="Türkçe karakter ve boşluk olmamalı!" name="stokadet" class="form-control" id="position" placeholder="Stok Adet Olarak Giriniz." required>
            </div>
            <div class="form-group">
                <label for="position">Alış Fiyatı</label>
                <input type="number"  pattern="[a-zA-Z0-9]+" title="Türkçe karakter ve boşluk olmamalı!" name="alisfiyati" class="form-control" id="position" placeholder="100.00" required>
            </div>
            <div class="form-group">
                <label for="position">Satış Fiyatı</label>
                <input type="number"  pattern="[a-zA-Z0-9]+" title="Türkçe karakter ve boşluk olmamalı!" name="satisfiyati" class="form-control" id="position" placeholder="100.00" required>
            </div>
            <div class="form-group">
                <label for="position">Degrade</label>
                <input type="text"    name="degrade" class="form-control" id="position" placeholder="degrade">
            </div>
            <div class="form-group">
                <label for="position">Polarize</label>
                <input type="text"   pattern="[a-zA-Z0-9]+" title="Türkçe karakter ve boşluk olmamalı!" name="polarize" class="form-control" id="position" placeholder="Polarize">
            </div>
            <div class="form-group">
                <label for="position">Cam Rengi</label>
                <input type="text"   pattern="[a-zA-Z0-9]+" title="Türkçe karakter ve boşluk olmamalı!" name="camrengi" class="form-control" id="position" placeholder="Mavi">
            </div>
            <div class="form-group">
                <label for="position">Ürün Ekarmanı</label>
                <input type="text"   name="urunekartman" class="form-control" id="position" placeholder="25*25*25" >
            </div>
            <div class="form-group">
                <label for="position">Cam Materyal</label>
                <input type="text"   pattern="[a-zA-Z0-9]+" title="Türkçe karakter ve boşluk olmamalı!" name="cammateryal" class="form-control" id="position" placeholder="Organik" >
            </div>
            <div class="form-group">
                <label for="position">Çerçeve Şekli</label>
                <input type="text"   pattern="[a-zA-Z0-9]+" title="Türkçe karakter ve boşluk olmamalı!" name="cercevesekli" class="form-control" id="position" placeholder="Oval" required>
            </div>
            <div class="form-group">
                <label for="position">Web Site Ürün Açıklaması</label>
                <input type="text"   name="aciklama" class="form-control" id="position" placeholder="Ürün Açıklaması">
            </div>
            <div class="form-group">
                <label for="position">Not</label>
                <input type="text"   name="not" class="form-control" id="position" placeholder="Ürün Notu (sadece siz görürsünüz)">
            </div>


            <button type="submit" name="urunekle" class="btn btn-default waves-effect waves-light">Ürünü Ekle</button>
            <button type="button" onclick="Custombox.close();" class="btn btn-danger waves-effect waves-light m-l-10">İptal</button>
        </form>
    </div>
</div><!--ekleme add-->
<div name="album" id="album" class="modal-demo">
        <button type="button" class="close" onclick="Custombox.close();">
            <span>&times;</span><span class="sr-only">Kapat</span>
        </button>
        <h4 class="custom-modal-title">Ürün Diger Resimleri</h4>
        <div class="custom-modal-text text-left">
            <form action="upload.php" name="album" class="dropzone" id="dropzoneFrom">
                <div class="form-group">

                    <input type="hidden" name="id" value="<?=trim($_GET['id']); ?>"  class="form-control" readonly>
                    <input type="hidden" name="album" value="<?= trim($_GET['album']); ?>"  class="form-control" readonly>
                </div>

                <div id="preview"></div>
                <!-- Hatayı Görüntüle modal içinde -->
                <?php if(isset($error)){
                    foreach($error as $erroryaz){
                        echo '  <div class=\'alert alert-danger\'><p>'.$erroryaz.'</p> </div>';

                    }
                } ?>
                <!-- Hatayı Görüntüle modal içinde -->



            </form>

        </div>
    </div>
    <!-- edit Modal -->
<div name="edit" id="edit" class="modal-demo"><!--tc,ad,tel,mail,adres//kayit_yapan//durum -->

        <button type="button" class="close" onclick="Custombox.close();">
            <span>&times;</span><span class="sr-only">Kapat</span>
        </button>
        <h4 class="custom-modal-title">Ürün Düzenleme</h4>
        <div class="custom-modal-text text-left">
            <form action="" name="edit" method="post" ><!-- id,tel,mail,durum -->

                <div class="form-group">
                    <label for="exampleInputEmail1">Marka</label><!--TODO markaları db den çekecek ama ekleme yapılabilecek  -->
                    <input type="hidden" name="id" value="<?= @$goster['id']; ?>"  class="form-control" readonly>
                    <input type="text" value="<?= @$goster['marka']; ?>"  pattern="[a-zA-Z0-9]+" title="Türkçe karakter ve boşluk olmamalı!"  name="marka" class="form-control" id="exampleInputEmail1" placeholder="Marka" required>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Ürün Adı</label><!--TODO markaları db den çekecek ama ekleme yapılabilecek  -->
                    <input type="text" value="<?= @$goster['baslik']; ?>"   name="baslik" class="form-control" id="exampleInputEmail1" placeholder="Ürün Adı" required>
                </div>
                <div class="form-group">
                    <label for="position">Ürün Kategori</label>
                    <select  name="kategori" class="form-control" required="required">
                       <? foreach( $urunkategori as $donguUrunKategori ):   ?>
                        <option value="<?= $donguUrunKategori['id'];?>" <?php if($donguUrunKategori['id'] == @$goster['urun_kategori_id']){echo 'selected';} ?>><?= $donguUrunKategori['baslik']; ?></option>
                        <?php endforeach;?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Ürün Kodu</label>
                    <input type="text" value="<?= $goster['urunkodu'];?>"  pattern="[a-zA-Z0-9]+" title="Türkçe karakter ve boşluk olmamalı!"  name="urunkodu" class="form-control" id="exampleInputEmail1" placeholder="Ürün Kodu" required>
                </div>
                <div class="form-group">
                    <label for="position">Barkod</label>
                    <input type="text" value="<?= $goster['barkod'];?>"  pattern="[a-zA-Z0-9]+" title="Türkçe karakter ve boşluk olmamalı!" name="barkod" class="form-control" id="position" placeholder="Ürün Açıklaması">
                </div>
                <div class="form-group">
                    <label for="position">Cinsiyet</label>
                    <select  name="cinsiyet" class="form-control" required="required">
                        <option value="<?= @$goster['cinsiyet']; ?>" selected>Degiştirmek İçin Seçim  Yapınız </option>
                        <option value="unisex">Unisex</option>
                        <option value="erkek">Erkek</option>
                        <option value="kadin">Kadın</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="position">Çocuk Ürünümü</label>
                    <select  name="cocukmu" class="form-control" required="required">
                        <option value="<?= @$goster['cocuk_mu']; ?>" selected><?= @$goster['cocuk_mu']; ?> </option>
                        <option value="evet">Evet</option>
                        <option value="hayir">Hayır</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="position">Degrade</label>
                    <input type="text" value="<?= @$goster['degrade']; ?>"  pattern="[a-zA-Z0-9]+" title="Türkçe karakter ve boşluk olmamalı!" name="degrade" class="form-control" id="position" placeholder="degrade">
                </div>
                <div class="form-group">
                    <label for="position">Polarize</label>
                    <input type="text" value="<?= @$goster['polarize']; ?>"   pattern="[a-zA-Z0-9]+" title="Türkçe karakter ve boşluk olmamalı!" name="polarize" class="form-control" id="position" placeholder="Polarize">
                </div>

                <div class="form-group">
                    <label for="position">Çerçeve Şekli</label>
                    <input type="text" value="<?= @$goster['cerceve_sekli']; ?>"  pattern="[a-zA-Z0-9]+" title="Türkçe karakter ve boşluk olmamalı!" name="cercevesekli" class="form-control" id="position" placeholder="Oval" required>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Web Site Ürün Açıklaması</label>
                    <textarea name="aciklama" class="form-control"  ><?= @$goster['aciklama'] ?></textarea>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Notlar</label>
                    <textarea  name="notlar" class="form-control"  ><?= @$goster['notlar'] ?></textarea>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Durum</label>
                    <select name="durum" class="form-control" required="required">
                        <option value="<?= @$goster['durum'];?>" selected><?= @$goster['durum'];?></option>
                        <option value="evet">Evet</option>
                        <option value="hayir">Hayır</option>
                    </select>
                </div>
                <button type="submit" name="edit" value="1" class="btn btn-default waves-effect waves-light">Güncelle</button>
                <button type="button" onclick="Custombox.close();" class="btn btn-danger waves-effect waves-light m-l-10">İptal</button>
            </form>
        </div>
    </div>
    <!-- edit Modal -->
<div name="stok" id="stok" class="modal-demo">


        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close"  onclick="Custombox.close();"data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Ürün Kartı</h4>
                </div>
                <div class="modal-body" id="yazdir">
                    <h4 class="modal-title"><?= @$goster['marka']; ?></h4>
                    <form method="post">
                        <input type="hidden" name="id" value="<?= @$goster['id']; ?>"  class="form-control" readonly>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label  class="control-label"><?= @$goster['baslik']; ?></label>

                                    <img src="<?= @$dosyayolu.@$goster['img'];?>" alt="image" class="img-responsive img-rounded" width="200">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">Ürün Kodu:</label>

                                    <input type="text" name="id" value="<?= @$goster['urunkodu'];?>" class="form-control" id="field-2" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">Barkod:</label>
                                    <input type="text" value="<?= @$goster['barkod'];?>" class="form-control" id="field-2" disabled>
                                </div>
                            </div>
                                <div class="col-md-6">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">Cinsiyet/Çocuk?</label>
                                    <input type="text" value="<?= @$goster['cinsiyet'].'/'.@$goster['cocuk_mu'];?>"class="form-control" id="field-2" disabled>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="field-4" class="control-label">Stok (Adet)</label>
                                    <input type="number"  name="stok_adet" value="<?= @$goster['stok_adet'];?>" class="form-control" id="field-4" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="field-5" class="control-label">Satılan (Adet)</label>
                                    <input type="text"  value="<?= @$goster['satilan'];?>" class="form-control" id="field-5" disabled >
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="field-6" class="control-label">Durum</label>
                                    <select name="durum" class="form-control" required="required">
                                        <option value="<?= @$goster['durum'];?>" selected><?= @$goster['durum'];?></option>
                                        <option value="evet">Evet</option>
                                        <option value="hayir">Hayır</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="field-4" class="control-label">Alış Fiyatı</label>
                                    <input type="number"  name="alis_fiyati" value="<?= @$goster['alis_fiyati'];?>" class="form-control" id="field-4" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="field-5" class="control-label">Satış Fiyatı</label>
                                    <input type="text"  name="satis_fiyati" value="<?= @$goster['satis_fiyati'];?>" class="form-control" id="field-5" required >
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="field-5" class="control-label">Çerçeve Şekli</label>
                                    <input type="text"   value="<?= @$goster['cerceve_sekli'];?>" class="form-control" id="field-5" disabled >
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group no-margin">
                                    <label for="field-7" class="control-label">Etiket</label>
                                    <input type="text"  pattern="[a-zA-Z0-9,]+" title="Türkçe Karakter Kullanmayınız" name="etiket" value="<?= @$goster['etiket'];?>" class="form-control" id="field-5" required >
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group no-margin">
                                    <label for="field-7" class="control-label">Not</label>
                                    <textarea name="notlar" class="form-control autogrow" id="field-7"  style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 5px;" > <?php echo @$goster['notlar'];?></textarea>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-icon waves-effect waves-light btn-purple" onclick="printDiv('yazdir')"> <i class="fa fa-print"></i> </button>
                    <button type="button"  onclick="goBack();" class="btn btn-default waves-effect" data-dismiss="modal">Kapat</button>
                    <button  name="guncelle" type="submit" class="btn btn-inverse waves-effect waves-light">Güncelle</button>
                    <a href="?id=<?= $gosterid.'&delete';?>" >
                        <button  type="button" class="btn btn-blue waves-effect waves-light"><i class="md md-delete"></i></button></a>
                    </td>
                </div>
                </form>
            </div>
        </div>
    </div>
 <div name="delete" id="delete" class="modal-demo">
        <button type="button" class="close" onclick="Custombox.close();">
            <span>&times;</span><span class="sr-only">Kapat</span>
        </button>
        <h4 class="custom-modal-title">Silme İşlemi</h4>
        <div class="custom-modal-text text-left">
            <form action="" name="delete" method="post" >
                <div class="form-group">
                    <label for="exampleInputEmail1">ID</label>
                    <?php $onaykodu=rand(1000,9999); ?>
                    <input type="hidden" name="onay" value="<?php echo $onaykodu;?>">
                    <input type="text" name="id" value="<?php echo trim($_GET['id']); ?>"  class="form-control" readonly>
                </div>

                <div class="alert alert-danger">

                    <strong>Geri Dönümsüz İşlem!</strong>  Silme İşlemini Tamamlamak İçin Onay Kodunuz: <strong><?php echo $onaykodu; ?></strong>
                </div>
                <!-- Hatayı Görüntüle modal içinde -->
                <?php if(isset($error)){
                    foreach($error as $erroryaz){
                        echo '  <div class=\'alert alert-danger\'><p>'.$erroryaz.'</p> </div>';

                    }
                } ?>
                <!-- Hatayı Görüntüle modal içinde -->
                <div class="form-group">
                    <label for="exampleInputEmail1">Onay Kodunuz:</label>
                    <input type="number" name="onaykodu"   class="form-control" >
                </div>

                <button type="submit" name="delete" class="btn btn-default waves-effect waves-light">Sil</button>
                <button type="button" onclick="Custombox.close();" class="btn btn-danger waves-effect waves-light m-l-10">İptal</button>

            </form>
        </div>
    </div>

   <?php include_once 'alt.php' ; ?>

<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>
<script src="assets/plugins/datatables/dataTables.buttons.min.js"></script>
<script src="assets/plugins/datatables/buttons.bootstrap.min.js"></script>
<script src="assets/plugins/datatables/jszip.min.js"></script>
<script src="assets/plugins/datatables/pdfmake.min.js"></script>
<script src="assets/plugins/datatables/vfs_fonts.js"></script>
<script src="assets/plugins/datatables/buttons.html5.min.js"></script>
<script src="assets/plugins/datatables/buttons.print.min.js"></script>
<script src="assets/plugins/datatables/dataTables.fixedHeader.min.js"></script>
<script src="assets/plugins/datatables/dataTables.keyTable.min.js"></script>
<script src="assets/plugins/datatables/dataTables.responsive.min.js"></script>
<script src="assets/plugins/datatables/responsive.bootstrap.min.js"></script>
<script src="assets/plugins/datatables/dataTables.scroller.min.js"></script>
<script src="assets/plugins/datatables/dataTables.colVis.js"></script>
<script src="assets/plugins/datatables/dataTables.fixedColumns.min.js"></script>
<script src="assets/pages/datatables.init.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#datatable').dataTable();
        $('#datatable-keytable').DataTable({keys: true});
        $('#datatable-responsive').DataTable();


        var table = $('#datatable-fixed-header').DataTable({fixedHeader: true});
        var table = $('#datatable-fixed-col').DataTable({

            scrollY: "300px",
            scrollX: true,
            scrollCollapse: true,
            paging: false,
            fixedColumns: {
                leftColumns: 1,
                rightColumns: 1

            }

        });
    });
    TableManageButtons.init();

</script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-material-datetimepicker/2.7.1/js/bootstrap-material-datetimepicker.min.js"></script>
<script src="assets/plugins/bootstrap-tagsinput/js/bootstrap-tagsinput.min.js"></script>
<script src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
<script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript"></script>
<!-- bootstrap timepicker-->
    <script>
        $(function () {
            $('#date').bootstrapMaterialDatePicker({
                format: 'DD-MM-YYYY',
                time: false,
                lang: 'tr',
            });
        });
        $(function () {
            $('#date2').bootstrapMaterialDatePicker({
                format: 'DD-MM-YYYY',
                time: false,
                lang: 'tr',
            });
        });
    </script>
<!-- dropzone uploader-->
    <script>

        $(document).ready(function(){

            Dropzone.options.dropzoneFrom = {
                autoProcessQueue: true,
                acceptedFiles:".png,.jpg,.gif,.bmp,.jpeg",
                init: function(){
                    var submitButton = document.querySelector('#submit-all');
                    myDropzone = this;
                    submitButton.addEventListener("click", function(){
                        myDropzone.processQueue();
                    });
                    this.on("complete", function(){
                        if(this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0)
                        {
                            var _this = this;
                            _this.removeAllFiles();
                        }
                        list_image();
                    });
                },
            };

            list_image();

            function list_image()
            {
                $.ajax({
                    url:"upload.php?id=<?= $_GET['id'];?>&album=<?= $_GET['album'];?>",
                    success:function(data){
                        $('#preview').html(data);
                    }
                });
            }

            $(document).on('click', '.remove_image', function(){
                var name = $(this).attr('id');
                $.ajax({
                    url:"upload.php?id=<?= $_GET['id'];?>&album=<?= $_GET['album'];?>",
                    method:"POST",
                    data:{name:name},
                    success:function(data)
                    {
                        list_image();
                    }
                })
            });

        });
    </script>
    <script>
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }
    </script>
<?php
    /*toast notifyjs bilidirmleri  işlem sonucunu bildirmede Kullanılıyor.*/
    if(isset($_GET['action']) )//todo hata ve basari sayyfalarını sinevipteki gibi yap yönlendirme olmadan degişken üzerinden
    {
        if($_GET['action']=='basarili') {    echo "<script>$.Notification.notify('custom','bottom right','İşleminiz Başarılı', 'İşleminiz Başarıyla Sonuçlanmıştır.')</script>";     }
        elseif($_GET['action']=='hata') {    echo "<script>$.Notification.notify('error','bottom right','Hata Mevcut', 'İşleminide Hatalar Oluştu.')</script>";     }

}
if(isset($_GET['id']) && !empty($_GET['id']) )
{
    if(isset($_GET['album'])) { echo "<script>  Custombox.open({target: '#album',effect: 'fadein',overlaySpeed: 200,  overlayColor: '#36404a' }) </script> ";     }
    if(isset($_GET['stok'])) { echo "<script>  Custombox.open({target: '#stok',effect: 'fadein',overlaySpeed: 200,  overlayColor: '#36404a' }) </script> ";     }
    if(isset($_GET['edit'])) { echo "<script>  Custombox.open({target: '#edit',effect: 'fadein',overlaySpeed: 200,  overlayColor: '#36404a' }) </script> ";     }
    if(isset($_GET['delete'])) { echo "<script>  Custombox.open({target: '#delete',effect: 'fadein',overlaySpeed: 200,  overlayColor: '#36404a' }) </script> ";     }

}
?>