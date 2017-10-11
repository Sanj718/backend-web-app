<?php
require_once('config.php');
include "header.php";
class Authorize{
    public function __construct(){
        if (isset($_REQUEST['username']) && isset($_REQUEST['password']) && !empty($_REQUEST['username']) && !empty($_REQUEST['password'])){
            $username = $_REQUEST['username'];
            $password = $_REQUEST['password'];
            $this->checkUsername($username, $password);
        }elseif(isset($_REQUEST['username']) && isset($_REQUEST['password'])&& empty($_REQUEST['username']) && empty($_REQUEST['password'])){
            Form::$error_result .="Please enter correct Username/Password";
        }
    }

    private function checkUsername($user, $pass){
        $user1 = htmlspecialchars(stripslashes(trim($user)));
        $pass1 = htmlspecialchars(stripslashes(trim($pass)));
        $table = Dbmanagement::$tablename;
        $sql = mysqli_query(Dbmanagement::$conn, "SELECT id, username, password FROM $table");
        for ($data = []; $row = mysqli_fetch_assoc($sql); $data[] = $row);
        $helper = 0;
        $userid = 0;
        foreach ($data as $d){
            if ($d['username'] == $user1 && password_verify($pass1, $d['password']))
            {
                $userid = $d['id'];
                $helper = 1;
            }
        }
        if ($helper == 1){
            echo "Access Granted!";
            // server should keep session data for AT LEAST 1 hour
            ini_set('session.gc_maxlifetime', 3600);
            // each client should remember their session id for EXACTLY 1 hour
            session_set_cookie_params(3600);
            session_start();
            $_SESSION['userid'] = $userid;
            header("Location:profile.php");
        }else{
            Form::show_error("Incorrect Username/Password!");
        }
    }

    public function createForm(){
        ?>
        <form action='' method='post'>
            <p><?php echo Form::input(['type'=>'text', 'name'=>'username','value'=>'', 'placeholder'=>'Username']);?></p>
            <p><?php echo Form::input(['type'=>'password', 'name'=>'password','placeholder'=>'Password']);?></p>
            <p><?php echo Form::submit(['value'=>'Submit']);?></p>
        </form>
        <?php
    }
}

$auth = new Authorize();
?>
<div class="login">
    <h3>Login</h3>
    <?php if (Form::$error_result){?><div class="error-box"><?php echo Form::$error_result;?></div><?php } ?>
    <div class="loginform"><?php $auth->createForm();?></div>
</div>


<?php include "footer.php"; ?>