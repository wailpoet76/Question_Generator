<?php
// Auther: Walid Bakr
// Date: 2024-07-15
// Last Update: 2025-4-01
// Description: Profile Page JS


$pageTitle="Teacher Registeration";
$nonavbar='';
include "init.php";
if (isset($_POST['registerbtn']) && $_SERVER['REQUEST_METHOD']=='POST'){
    ?>
    <h1 class='text-center'> Register Teacher</h1>
        <?php
        //Check user name repeatition
            $stmt=$con->prepare("SELECT *
                FROM
                    teachers
                WHERE
                    username=?
                    ");
            $stmt->execute(array($_POST['userName']));
            $teacher=$stmt->fetch();
            $checker=$teacher['username']??"";
            // Check that the user is new if not  a warning message is given and returns
            if(($stmt->rowCount()>0) && ($checker==$_POST['userName'])){
                $msg="Teacher already exists";
                fn_redirect($msg,"warning",3,'../index.html');
            }

            //Hash the password
            if (isset($_POST['password']) && isset($_POST['repassword']) &&
                $_POST['password'] == $_POST['repassword']) {
                //Hash the password
                $_POST['password']=sha1($_POST['password']);
                $_POST['repassword']=sha1($_POST['repassword']);
            } else {
                $msg="Password and Re-Password are not matched";
                fn_redirect($msg,"warning",3,'../index.html');
            }
            
        //Insert into teachers the teacher information inside DB FILE
        $stmt = fn_DB_insert("teachers","ename,aname,tel,whats,email,
            username,password,repassword,forgetpassQ,forgetpassAns"
            ,":Zename,:Zaname,:Ztel,:Zwhats,:Zemail,
            :Zusername,:Zpassword,:Zrepassowrd,:ZforgetpassQ,:ZforgetpassA"
            ,array(
            'Zename'=>$_POST['nameEn'],
            'Zaname'=>$_POST['nameAr'],
            'Ztel'=>$_POST['tel'],
            'Zwhats'=>$_POST['whats'],
            'Zemail'=>$_POST['email'],
            'Zusername'=>$_POST['userName'],
            'Zpassword'=>$_POST['password'],
            'Zrepassowrd'=>$_POST['repassword'],
            'ZforgetpassQ'=>$_POST['forgetpassquestion'],
            'ZforgetpassA'=>$_POST['forgetpassanswer'],
            ));

            //get teacher id from DB
            $stmt=$con->prepare("SELECT *
                FROM
                    teachers
                WHERE
                    username=?
                    ");
            $stmt->execute(array($_POST['userName']));
            $teacher=$stmt->fetch();
            $teacherid=$teacher['teacher_id'];
            //prepare items for teacher
            //separate each subject
            $itemscounterarray=explode(",",$_POST['counterid']);
            
            for ($i=0; $i < count($itemscounterarray); $i++) {
                
                # deal with the item
                # check if the 1st item is null in number
                //insert Teacher information about subject into file Teacher_items

                //only one tab of education is created
                if ($itemscounterarray[$i]==NULL){
                    //set defaults to zero
                    $langstate=0;
                    $varlang1=0;
                    $varlang2=0;
                    //Check the default language if both are checked default will be ENG
                    //both ENG&AR are checked DEFAULT is ENG
                    if(isset($_POST["lang1"])&&isset($_POST["lang2"])){
                        $langstate=1;
                        $varlang1=1;
                        $varlang2=0;
                        // ENG ONLY checked not AR DEFAULT is ENG
                    }else if(isset($_POST["lang1"])&&(!isset($_POST["lang2"]))){
                        $langstate=2;
                        $varlang1=1;
                        $varlang2=0;
                        // AR ONLY checked not ENG DEFAULT is AR
                    }else if(isset($_POST["lang2"])&&(!isset($_POST["lang1"]))){
                        $langstate=3;
                        $varlang1=0;
                        $varlang2=1;
                    }
                    if (isset($_POST["lang1"])) {
                        $stmt2 = fn_DB_insert("teacher_items","type,stage,subject,
                        grade,lang,revision,teacher_id,account_status",
                        ":ztype,:zstage,:zsubject,
                        :zgrade,:zlang,:zrevision,:zteacher,:zaccount_status"
                        ,array(
                        'ztype'=>$_POST['type'],
                        'zstage'=>$_POST['stage'],
                        'zsubject'=>$_POST['subject'],
                        'zgrade'=>$_POST['grade'],
                        'zlang'=>$_POST['lang1'],
                        'zrevision'=>isset($_POST['revisor'])?$_POST['revisor']:'',
                        'zteacher'=>$teacherid,
                        'zaccount_status'=>(!($langstate==3))? 1 : 0
                        ));
                    }
                    if (isset($_POST["lang2"])) {
                        $stmt2 = fn_DB_insert("teacher_items","type,stage,subject,
                        grade,lang,revision,teacher_id,account_status",
                        ":ztype,:zstage,:zsubject,
                        :zgrade,:zlang,:zrevision,:zteacher,:zaccount_status"
                        ,array(
                        'ztype'=>$_POST['type'],
                        'zstage'=>$_POST['stage'],
                        'zsubject'=>$_POST['subject'],
                        'zgrade'=>$_POST['grade'],
                        'zlang'=>$_POST['lang2'],
                        'zrevision'=>isset($_POST['revisor'])?$_POST['revisor']:'',
                        'zteacher'=>$teacherid,
                        'zaccount_status'=>($langstate==3)? 1 : 0
                        ));
                    }
                }else{
                    //Multiple items are created
                    if (isset($_POST["lang1_".$itemscounterarray[$i]])) {
                    $stmt2 = fn_DB_insert("teacher_items","type,stage,subject,
                        grade,lang,revision,teacher_id",
                        ":ztype,:zstage,:zsubject,
                        :zgrade,:zlang,:zrevision,:zteacher"
                        ,array(
                        'ztype'=>$_POST['type_'.$itemscounterarray[$i]],
                        'zstage'=>$_POST['stage_'.$itemscounterarray[$i]],
                        'zsubject'=>$_POST['subject_'.$itemscounterarray[$i]],
                        'zgrade'=>$_POST['grade_'.$itemscounterarray[$i]],
                        'zlang'=>$_POST['lang1_'.$itemscounterarray[$i]],
                        'zrevision'=>isset($_POST['revisor_'.$itemscounterarray[$i]])?$_POST['revisor_'.$itemscounterarray[$i]]:'',
                        'zteacher'=>$teacherid
                        ));
                    }
                    if (isset($_POST["lang2_".$itemscounterarray[$i]])) {
                        $stmt2 = fn_DB_insert("teacher_items","type,stage,subject,
                            grade,lang,revision,teacher_id",
                            ":ztype,:zstage,:zsubject,
                            :zgrade,:zlang,:zrevision,:zteacher"
                            ,array(
                            'ztype'=>$_POST['type_'.$itemscounterarray[$i]],
                            'zstage'=>$_POST['stage_'.$itemscounterarray[$i]],
                            'zsubject'=>$_POST['subject_'.$itemscounterarray[$i]],
                            'zgrade'=>$_POST['grade_'.$itemscounterarray[$i]],
                            'zlang'=>$_POST['lang2_'.$itemscounterarray[$i]],
                            'zrevision'=>isset($_POST['revisor_'.$itemscounterarray[$i]])?$_POST['revisor_'.$itemscounterarray[$i]]:'',
                            'zteacher'=>$teacherid
                        ));
                    }
                }
                
            $msg="Teacher and subjects are added";
            fn_redirect($msg,"warning",3,'../index.html');
        }
    } else{
        $msg="Can't Browse this page Directly.";
        fn_redirect($msg,"success",3,"../index.html");
    }
    include $tpl."footer.php";
    exit();
?>