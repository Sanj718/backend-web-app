<?php
require_once('config.php');
include "header.php";
class Register {
    public function __construct(){
        if (isset($_REQUEST['username'])){
            $username = $this->check_username($_REQUEST['username']);
            $password = $this->checkpassword($_REQUEST['password'], $_REQUEST['password2']);
            $name = $this->check_input($_REQUEST['name'], "Name is empty");
            $lastname = $this->check_input($_REQUEST['lastname'], "Lastname is empty");
            $email = $this->check_input($_REQUEST['email'],"Email is empty.");
            $dob = $this->check_input($_REQUEST['dob'],"DOB is empty.");

            if (!empty($username) && !empty($password) && !empty($email)){
                $table = Dbmanagement::$tablename;
                $passwordinput = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO $table (username, password, firstname, lastname, email, dob) 
                    VALUES ('$username', '$passwordinput', '$name', '$lastname', '$email', '$dob')";
                if (Dbmanagement::$conn->multi_query($sql) === TRUE) {
                    echo "New records created successfully";
                } else {
                    echo "<!-- Error: " . $sql . "<br>" . Dbmanagement::$conn->error ." -->";
                }
            }
        }else{
           return null;
        }

    }
    private function check_username($uncheck){
        $table = Dbmanagement::$tablename;
        $sql = mysqli_query(Dbmanagement::$conn, "SELECT username FROM $table");
        for ($data = []; $row = mysqli_fetch_assoc($sql); $data[] = $row);
        $helper = 0;
        foreach ($data as $d){
            if ($d['username'] == $uncheck){$helper = 1;}
        }
        if ($helper != 0){
            $this->check_input("", "This username already exists.");
        }else{
            return $this->check_input($uncheck, "Username is empty.");
        }
    }
    protected function input($attr){
        return "<input ".Form::getAttr($attr)." >";
    }

    private function check_input($data, $problem='')
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        if($problem && strlen($data) == 0){
            Form::show_error($problem);
        }
        return $data;
    }

    public function password_strength($input){
        $check1 = preg_match("#[a-z]+#", $input);
        $check2 = preg_match("#[A-Z]+#", $input);
        $check3 = preg_match("#[0-9]+#", $input);
        $check4 = preg_match("#[$-/:-?{-~!\"^_`\[\]]+#", $input);
        $check5 = 0;
        if (count(str_split($input)) > 6){$check5 = 1;}
        $res = $check1*$check2*$check3*$check4*$check5;

        if ($res == 0){
            return false;
        }else{
            return $input;

        }
    }
    private function checkpassword($password, $password_compare){
        if ($this->password_strength($password) == false && $password != "" && $password == $password_compare){
            return $this->check_input("", "Password is not strong enought.");
        }elseif($password != $password_compare){
            return $this->check_input("", "Password fields are different.");
        }else{
            return $this->check_input($password, "Password is empty.");
        }
    }

    public function createForm(){

        ?>
        <form action='' method='post'>
        <p><?php echo $this->input(['type'=>'text', 'name'=>'username','value'=>'', 'placeholder'=>'Username']);?></p>
        <p><?php echo $this->input(['type'=>'password', 'name'=>'password','placeholder'=>'Password']);?></p>
        <p><?php echo $this->input(['type'=>'password', 'name'=>'password2','placeholder'=>'Confirm Password']);?></p>
        <p><?php echo $this->input(['type'=>'text', 'name'=>'name','value'=>'', 'placeholder'=>'Name']);?></p>
        <p><?php echo $this->input(['type'=>'text', 'name'=>'lastname','value'=>'', 'placeholder'=>'Lastname']);?></p>
        <p><?php echo $this->input(['type'=>'email', 'name'=>'email','value'=>'', 'placeholder'=>'Email']);?></p>
        <p><?php echo $this->input(['type'=>'date', 'name'=>'dob','value'=>'']);?></p>
        <p><?php echo Form::submit(['value'=>'Submit']);?></p>
        </form>
        <?php
    }
}
class SmartForm extends Register{
    public function input($attr){
        if (isset($attr['name']) && isset($_REQUEST[$attr['name']])){
            if ($attr['type'] != 'password'){
                $attr['value'] = $_REQUEST[$attr['name']];
            }
        }
        return Form::input($attr);
    }
}
$form = new SmartForm();
?>
<div class="register">
    <h3>Sign Up</h3>
    <?php if (Form::$error_result){?><div class="error-box"><?php echo Form::$error_result;?></div><?php } ?>
    <?php $form->createForm(); ?>
</div>
<?php include "footer.php"; ?>