<?php
require_once('config.php');
include "header.php";
session_start();
if(!isset($_SESSION['userid'])){
    header("Location:Login.php");
}

class Profile {
    private $userid;

    public function __construct(){
        $this->userid = $_SESSION['userid'];
    }

    public function getRow($r){
        $table = Dbmanagement::$tablename;
        $sql = Dbmanagement::$conn->query("SELECT * FROM $table WHERE id=$this->userid");
        for ($data = []; $row = mysqli_fetch_assoc($sql); $data[] = $row);
        if ($data[0][$r] == null){
            return "N/A";
        }else{
            return $data[0][$r];
        }
    }

    public function getAva(){
        if ($this->getRow('ava') == null || empty($this->getRow('ava')) || $this->getRow('ava') == "N/A"){
            echo "<img src='avatar.jpg' alt='here should be your profile photo' />";
        }else{
            return "<img src='".$this->getRow('ava')."' alt='".$this->getRow('username')."' />";
        }

    }

}
class ProfileConfig {
    private $userid;

    public function __construct(){
        $this->userid = $_SESSION['userid'];

        if (isset($_REQUEST['submit'])){
            foreach ($_REQUEST as $key=>$value){
                if ($key != 'ava' && $key != 'submit'){
                    $this->changeUF($key, $value);
                }
            }
            if (!empty($_FILES['ava']["name"])){
                $this->fileUpload();
            }

        }
    }

    private function fileUpload(){
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["ava"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        $check = getimagesize($_FILES["ava"]["tmp_name"]);
        if($check !== false) {
            echo "<!-- File is an image - " . $check["mime"] . ".-->";
            $uploadOk = 1;
        } else {
            return Form::$error_result .= "File is not an image.";
            $uploadOk = 0;
        }

        if (file_exists($target_file)) {
            return Form::$error_result .= "Sorry, file already exists.";
            $uploadOk = 0;
        }

        if ($_FILES["ava"]["size"] > 500000) {
            return Form::$error_result .= "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
            return Form::$error_result .= "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            return Form::$error_result .= "Sorry, your file was not uploaded.";

        } else {
            if (move_uploaded_file($_FILES["ava"]["tmp_name"], $target_file)) {
                echo "The file ". basename( $_FILES["ava"]["name"]). " has been uploaded.";
                $this->changeUF('ava', $target_file);
            } else {
                return Form::$error_result .= "Sorry, there was an error uploading your file.";
            }
        }
    }
    private function changeUF($field, $replace){
        $table = Dbmanagement::$tablename;
        if (!empty($replace) && isset($replace)){
            $sql = "UPDATE $table SET $field='$replace' WHERE id=$this->userid";
            if (Dbmanagement::$conn->query($sql) === FALSE) {
                echo "Error: " . $sql . "<br>" . Dbmanagement::$conn->error;
            }
        }elseif($field == "sex" || $field == "about" || $field == "status"){
            return true;
        }else{
            return Form::$error_result .= "<p>".ucwords($field)." is empty</p>";
        }
    }


    public function createChangeForm(){
        ?>
<form action='' method='post' enctype="multipart/form-data">
    <p>Change Firstname: <?php echo Form::input(['type'=>'text', 'name'=>'firstname']); ?></p>
    <p>Change Lastname: <?php echo Form::input(['type'=>'text', 'name'=>'lastname']); ?></p>
    <p>Change Email: <?php echo Form::input(['type'=>'email', 'name'=>'email']); ?></p>
    <p>Change Sex: <?php echo Form::select(['name'=>'sex'],['empty'=>'','male'=>'Male','female'=>'Female']); ?></p>
    <p>Change DOB: <?php echo Form::input(['type'=>'date', 'name'=>'dob']); ?></p>
    <p>Change Status: <?php echo Form::input(['type'=>'text', 'name'=>'status']); ?></p>
    <p>Change About: <?php echo Form::textarea(['name'=>'about']); ?></p>
    <p>Upload Avatar: <?php echo Form::input(['type'=>'file', 'name'=>'ava', 'id'=>'ava']); ?></p>
    <p><?php echo Form::submit(['value'=>'Submit', 'name'=>'submit']);?></p>
</form>
        <?php
    }
}
$profile = new Profile();
$prconfig = new ProfileConfig();
?>

<div class="profile">
    <div class="profile-left">
        <div class="ava"><?php echo $profile->getAva(); ?></div>
        <div class="profile-info">
            <p><?php echo $profile->getRow('username'); ?></p>
            <p><?php echo $profile->getRow('status'); ?> </p>
            <p><?php echo $profile->getRow('email'); ?></p>
            <p><a href="logout.php">Logout</a></p>
        </div>
    </div>
    <div class="profile-right">
        <div class="error-box"><?php echo Form::$error_result; ?></div>
        <div class="full-info">
            <p>Username: <?php echo $profile->getRow('username'); ?></p>
            <p>Name: <?php echo $profile->getRow('firstname'); ?></p>
            <p>Lastname: <?php echo $profile->getRow('lastname'); ?></p>
            <p>DOB: <?php echo $profile->getRow('dob'); ?></p>
            <p>Sex: <?php echo $profile->getRow('sex'); ?></p>
            <p>About Me: <?php echo $profile->getRow('about'); ?></p>
            <p>Registration Date: <?php echo $profile->getRow('reg_date'); ?></p>
        </div>
        <div class="change-info">
            <?php  $prconfig->createChangeForm();?>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>