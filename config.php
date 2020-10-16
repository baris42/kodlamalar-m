<?php
//oturumla başlatıyoruz.
error_reporting(E_ALL);
ob_start();
session_start();

//tarih verisini istemiyoruz .
date_default_timezone_set('Europe/Istanbul');

//database credentials
define('DBHOST','localhost');
define('DBUSER','root');
define('DBPASS','mysql');
define('DBNAME','yasarhun');
define('SITEADI','Yasarhun Optik');
define('TEL','0 246 232 27 90');
define('SITEEMAIL','bilgi@yasarhunoptik.com');/*username ve site mail adresi*/
define('ADRES','Sanayi Mh. 3239 Sk No:4 (Eski Gülkent Devlet Hastanesi karşısı) Isparta');
define('INSTAGRAM','https://www.instagram.com/yasarhunoptik/');
define('FACEBOOK','https://www.facebook.com/yasarhunoptik/');
define('LINKEDIN','#');
define('TWITTER','#');
define('SLOGAN','Yasarhun Optik');
define('ACIKLAMA','Yasarhun Optik,Isparta Optik,Isparta Gözlük,Isparta Güneş gözlüğü,Isparta numaralı gözlük,Isparta Lens,güneş gözlükleri,numaralı gözlükler,Isparta Optisyen');
//application address
define('DIR','https://yasarhunoptik.com/');
define('WHATSAPP','https://api.whatsapp.com/send?phone=905542979042');
define('PATH',$_SERVER['DOCUMENT_ROOT'].'/');

//application address mail.php ayarları
define('MAILADI','Yasarhun Optik');
define('MAILEMAIL','bilgi@yasarhunoptik.com');/*username ve site mail adresi*/

define('MAILHOST','smtp.yandex.com');/*sunucu*/
define('MAILPASS','isparta32*+');/*mail password*/
define('MAILPORT','465');/*mail port*/
define('MAILHATA','2');/*mail port*/



try {

	//create PDO connection
	$db = new PDO("mysql:host=".DBHOST.";charset=utf8mb4;dbname=".DBNAME, DBUSER, DBPASS);
    //$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);//Suggested to uncomment on production websites
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Suggested to comment on production websites
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch(PDOException $e) {
	//show error
    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
    exit;
}

require_once PATH.'admin/classes/user.php';
require_once PATH.'admin/classes/phpmailer/mail.php';
require_once PATH.'admin/includes/function.php';
$user = new User($db);



?>
