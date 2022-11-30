<?php
if (isset($_POST['submit'])) {
    pngtojpgAction($_FILES["fileToUpload"]["tmp_name"], basename($_FILES["fileToUpload"]["name"]));
}
function pngtojpgAction($file, $name)
{
    //Code to convert png to jpg image
    $input = imagecreatefromjpeg($file);
    $width=imagesx($input);
    $height=imagesy($input);
    $output = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($output,  255, 255, 255);
    imagefilledrectangle($output, 0, 0, $width, $height, $white);
    imagecopy($output, $input, 0, 0, 0, 0, $width, $height);

    ob_start();
    imagejpeg($output);
    $contents =  ob_get_contents();
    //Converting Image DPI to 300DPI
    $contents = substr_replace($contents, pack("cnn", 1, 300, 300), 13, 5);
    ob_end_clean();
    header('Content-Type: image/jpeg');
    header('Content-Disposition: attachment; filename="' . basename($name). '"');
    echo file_get_contents('data:image/jpeg;base64,'.base64_encode($contents));
    die();
}

?>

<!DOCTYPE html>
<html>
<body>

<form action="" method="post" enctype="multipart/form-data">
  Select image to upload:
  <input type="file" name="fileToUpload" id="fileToUpload">
  <input type="submit" value="Upload Image" name="submit">
</form>

</body>
</html>
