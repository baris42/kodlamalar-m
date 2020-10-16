<?php
/**18072020 klasör sil fonskyionu eklendi */
require_once 'config.php';


/*date format and add date +- days moths*/
function setdate($tarih,$format,$gun){
//date format using  'Y-m-d' ,'d-m-Y
//date example 10-04-2020
 // how do add  +2 -2 day month years example
    $islem = new DateTime($tarih);
    $islem = $islem->modify($gun);
    $islem =$islem->format($format);

    return $islem;
   // example echo $result = setdate('10-04-2020','Y-m-d','+1 day');

}
//get settings data

//$settings = $db->query("SELECT * FROM site_ayar ");
//$settings->execute();
//$settings =$settings->fetch(PDO::FETCH_ASSOC);

//get webpage date
//$mansetler = $db->query("SELECT * FROM veri where verituru='Manset'AND durum='Yes'", PDO::FETCH_ASSOC);
//$kurulumlar = $db->query("SELECT * FROM veri where verituru='Kurulum'AND durum='Yes'", PDO::FETCH_ASSOC);
//$paketler = $db->query("SELECT * FROM paketler", PDO::FETCH_ASSOC);
//$kanal4 = $db->query("SELECT * FROM veri where verituru='Kanal' AND durum='Yes'LIMIT 4 ", PDO::FETCH_ASSOC);

//davetiye ve token sistemi
function KodUret($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
// Echo the random string.
// Optionally, you can give it a desired string length.
//echo KodUret();

//spam mail listesi tersine çalışıyor sadece izin verilenler listesi yaptık o liste dışındaki mail servilserden üye olunamıyor.
function is_spam_mail($mail) {
    $mail_domains_ko ='';
    include 'spam_mail_list.php';
    foreach($mail_domains_ko as $ko_mail) {
        list(,$mail_domain) = explode('@',$mail);
        if(strcasecmp($mail_domain, $ko_mail) == 0){
            return true;
        }
    }
    return false;
    //if(is_smap_mail('mucahidbaris@yopmail.me')){
    //    echo 'spam mail adresi';
    //
    //}
}



/// ip adresini al
function get_ip_address(){
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        if (array_key_exists($key, $_SERVER) === true){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); // just to be safe

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
                }
            }
        }
    }
}



// last login ve ip adres mysqle kayit 
/*if( $user->is_logged_in() ){ //giriş yaptıysa bunu çalıştır
if(!isset($_SESSION['last_login'])){
    try {

        $stmt = $db->prepare("UPDATE users SET ip = :ip, last_login = :last_login WHERE id =:id");
        $stmt->execute(array(
            'ip' => get_ip_address(),
            'last_login' => date("Y-m-d H:i:s"),
            'id' => $_SESSION['id'],

    
        ));
        $_SESSION['last_login'] = get_ip_address();

        

    } catch(PDOException $e) {
        $error[] = $e->getMessage();
    }

}}*/

//sms göndermek için fonksiyon 
function SendSms($recipients,$message){

//$recipients,message
$url = "https://gatewayapi.com/rest/mtsms";
$api_token = "pmqYFjp9SEmKGd5EsjGa7ohTCXOG8g5YfWADuqVUsXiFg6ZxMVmiH1taVV3TpFd-";
$json = [
   'sender' => 'SineVip',
   'message' => $message,
   'recipients' => [],
];
foreach ($recipients as $msisdn) {
   $json['recipients'][] = ['msisdn' => $msisdn];
}
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
curl_setopt($ch,CURLOPT_USERPWD, $api_token.":");
curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($json));
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
//print($result); // print result as json string
//$json = json_decode($result); // convert to object
//print_r($json->ids); // print the array with ids
//print_r($json->usage->total_cost); // print total cost from ‘usage’ object


}
function WPMessage($phone,$message){

    $json=json_encode(array(
        "phone"=>$phone,
        "body"=>$message
    ));
    $url=WPURL."message?token=".WPTOKEN;
    
    
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$json);
    curl_setopt($ch,CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
    $result=curl_exec($ch);
    curl_close($ch);
    //echo result

    
    }
    function WPMessage2($phone,$message){
        global $db;
       //insert into database with a prepared statement
    $stmt = $db->prepare('INSERT INTO message (phone,content) VALUES (:phone,:content)');
    $stmt->execute(array(
        ':phone' => $phone,
        ':content' => $message
       
    ));
       }
    

//resim yükle kısmından sonra o girdi silinince o konuya ait klasör ve içindekileri siler
/*function klasorsil($klasor){
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
}*/

///google dogrulama fonsiyonu
function GDogrulama($response)
{

    $fields = [
        'secret' => SECRETKEY,
        'response' => $response
    ];

    $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($fields),
        CURLOPT_RETURNTRANSFER => true
    ]);
    $result = curl_exec($ch);
    curl_close($ch);
    $output= json_decode($result, true);
    $output=$output['success'];
    return $output;

   
}




?>
