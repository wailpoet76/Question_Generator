<?php
// Auther: Walid Bakr
// Date: 2024-07-15
// Last Update: 2024-07-27
// Description: PHP HOME MAIN Page

session_start();
$nonavbar='';
$pageTitle='Login';
// $langset="Eng";
include "init.php";

if (isset($_SESSION['teacher_name']) && !(isset($_GET['do']))){
    header('location: homepage.php');
    exit;
}
//prepare and check then get DB info
if (isset($_POST["userName"])){
$stmt=$con->prepare("SELECT *
    FROM
        teachers
    WHERE
        username=?
    ");
    $stmt->execute(array($_POST["userName"]));
    $DBrecord=$stmt->fetch();
}else{
    $msg="Wrong processing";
    fn_redirect($msg,"danger",3,"../index.html");
}
//*************************************************************** */
//*************************************************************** */
//********                   Login                  ************* */
//*************************************************************** */
//*************************************************************** */
if($stmt->rowCount()==0){
    $msg="No such user";
    fn_redirect($msg,"danger",3,"../index.html");
    exit();
}
if(isset($_POST["loginBtn"])&& $_SERVER['REQUEST_METHOD']=="POST"){
    //Hash the password
    $_POST["password"]=sha1($_POST["password"]);

    if ($_POST["userName"]==$DBrecord['username'] && $_POST["password"]==$DBrecord['password']&& $DBrecord['regstatus']!=0){
        $_SESSION['teacher_name']=$DBrecord['ename'];//Register Seesion user Name
        $_SESSION['teacher_id']=$DBrecord['teacher_id'];//Register Seesion ID
        header('Location: homepage.php?Langset=Eng');
    }else if($_POST["userName"]==$DBrecord['username'] && $_POST["password"]==$DBrecord['password']&& $DBrecord['regstatus']==0){
        $msg="User is not active yet, waiting for approval";
        fn_redirect($msg,"danger",6,"../index.html");
    }else{
        $msg="Wrong Username or Password";
        fn_redirect($msg,"danger",3,"../index.html");
    }

//*************************************************************** */
//*************************************************************** */
//********                   Forget                 ************* */
//*************************************************************** */
//*************************************************************** */
}else if(isset($_POST["forgetBtn"])&& $_SERVER['REQUEST_METHOD']=="POST"){
    ?>
    <div class="container text-center">
        <form action="login.php?do=Retrive" method="post">
            <div class="inpData pass mt-3 text-center">
                <input hidden type="text" name="userName" value="<?php echo $DBrecord['username'] ;?>">
                <h4 class="text-center">Password Retrive</h4>
                <label for=""> <?php echo $DBrecord["forgetpassQ"] ?></label>
                <input type="text" name="answerQ" autocomplete='none'>
            </div>
                <button type="submit" name="retrive">Retrive Password</button>
        </form>
    </div>
    <?php
}else if(isset($_POST["retrive"])&& $_SERVER['REQUEST_METHOD']=="POST" && $_GET['do']=='Retrive'){
    if($DBrecord["forgetpassAns"]==$_POST["answerQ"]){
        $msg="Save it, Your passowrd is: ".$DBrecord["password"];
        fn_redirect($msg,"success",10,"../index.html");
    }else{
        $msg="Bad Answer";
        fn_redirect($msg,"danger",3,"../index.html");
    }
}else{
    $msg="Can't browse directly to this page";
    fn_redirect($msg,"danger",3,"../index.html");

}
include $tpl."footer.php";
?>