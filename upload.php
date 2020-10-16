<?php

//upload.php
require_once 'includes/config.php';


$id=trim($_GET['id']);
$album=trim($_GET['album']).'/';

$folder_get = PATH.'images/'.$album.$id.'/';
$folder_view = DIR.'images/'.$album.$id.'/';

if (!file_exists($folder_get)) {
 mkdir($folder_get, 0777, true);

}



/*once id adında klasör oluşturuyorum */


if(!empty($_FILES))
{
 $id=trim($_POST['id']);
 $album=trim($_POST['album']);
 $folder_name = PATH.'images/'.$album.'/'.$id.'/';
 $temp_file = $_FILES['file']['tmp_name'];
 $location = $folder_name.$_FILES['file']['name'];
 move_uploaded_file($temp_file, $location);
}

if(isset($_POST["name"]))
{
 $filename = $folder_get.$_POST["name"];
 unlink($filename);
}

$result = array();

$files = scandir($folder_get);

$output = '<div class="row">';

if(false !== $files)
{
 foreach($files as $file)
 {
  if('.' !=  $file && '..' != $file)
  {
   $output .= '
   <div class="col-md-2">
    <img src="'.$folder_view.$file.'" class="img-thumbnail" width="60" height="60" style="height:65px;" />
    <button type="button" class="btn btn-link remove_image" id="'.$file.'">Sil</button>
   </div>
   ';
  }
 }
}
$output .= '</div>';
echo $output;

?>
