<?php

class Image {
    public static function uploadImage($loggedinUsername) {
        $imgName = $_FILES['img']['name'];
        $imgTmp  = $_FILES['img']['tmp_name'];
        $imgSize = $_FILES['img']['size'];
        $imgExtension = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
        $allowedExtension = array('png', 'jpg', 'jpeg');

        $formErrors = array();

        if(!in_array($imgExtension, $allowedExtension)) {
            $formErrors[] = "not allowed type";
        }
        if($imgSize > 4 * 1024 * 1024) {
            $formErrors[] = "maximum size is 5MB";
        }
        if(empty($formErrors)) { // now upload your img

            //if(is_uploaded_file($imgTmp)) {echo "uploaded";}
            move_uploaded_file($imgTmp, 'uploads/'.$imgName);

            /*$userDir = "uploads/users/".$loggedinUsername;

            if(is_dir($userDir)) {

                echo "user has a dir";

            } else {

                chmod("uploads/users", 0755);

                if(mkdir($userDir)) {
                    echo "make it";
                }

                echo "user has no dir";

            }*/

        } else {
            foreach ($formErrors as $error) {
                echo "<div class=''>$error</div>";
            }
            die;
        }
    }
}
