<?php
// Auther: Walid Bakr
// Date: 2024-07-15
// Last Update: 2025-06-08
// Description: MAIN USED FUNCTIONS page

ob_start();
// Title Function which Echo page Title
function fn_pageTitle(){
    global $pageTitle;
    if(isset($pageTitle)){
        echo $pageTitle;
    }else{
        echo 'Question Generator';
    }
}

// Redirect Function
//version 1.0
//Functionto return to a certain location after a warning message
function fn_redirect($msg,$type="danger",$sec = 3,$location){
    $timer = $sec;
    if($msg){
        echo "<div class='border-start border-$type  border-5 border-top-0 border-bottom-0 border-end-0 alert alert-$type'> $msg</div>";
    }
    echo "<div class='border-start border-info  border-5 border-top-0 border-bottom-0 border-end-0 alert alert-info'>you will be redirected to home page after <strong><span class='time'>$timer</strong></span> Seconds.</div>";
    ?>
    <script>
        const timeSpan=document.querySelector(".time");
        let timeJS=<?php echo $timer?>;
        let x= setInterval(function(){
                timeSpan.innerHTML=--timeJS;
                if(timeJS<1){
                    clearInterval(x);
                }
            },1000);
    </script>

    <?php
    if ($location=="back"){
        $location=$_SERVER["HTTP_REFERER"];
    }
    header("refresh:$sec;url=$location");
    ob_end_flush();
    exit();
}


//Get RecoredsFrom DB File Function v1.0
function fn_getRecords($select,$table,$where){
    global $con;
    $stmt=$con->prepare("SELECT $select FROM $table $where");
    $stmt->execute();
    return $stmt->fetchAll();

}

//Get RecoredsFrom DB File Function v2.0
function fn_getRecordsV2($select,$table,$where,$excute){
    global $con;
    $stmt=$con->prepare("SELECT $select FROM $table $where");
    $stmt->execute($excute);
    return $stmt->fetchAll();

}


// function Adding new record in a DBFILE
//version 1.0
//Functionto return ****number**** of Recorded items and DBFILE NAME
function fn_DB_insert($table,$fields,$values,$arrayofvalues){
    global $con;
    $stmt=$con->prepare("INSERT INTO
            $table($fields) VALUES ($values)");
    $stmt->execute($arrayofvalues);
    return $stmt->rowCount()." Record(s) is Added in ".$table." Database File<br>";
}

// function Adding new record in a DBFILE
//version 1.0
//Functionto return ****number**** of Recorded items and DBFILE NAME
function fn_DB_insertCond($table,$fields,$values,$arrayofvalues,$inner,$where){
    global $con;
    $stmt=$con->prepare("INSERT INTO
            $table($fields) VALUES ($values)");
    $stmt->execute($arrayofvalues);
    return $stmt->rowCount()." Record(s) is Added in ".$table." Database File<br>";
}



// function seraching for item in DB Function
//version 1.0
//Functionto return ****number**** of found items meet a certain Where
function fn_checkMember($select,$from,$value){
    global $con;
    $stmt=$con->prepare("SELECT $select FROM $from WHERE $select = ?");
    $stmt->execute(array($value));
    return $stmt->rowCount();
}


// count number of itmes function V1.0
// function seraching for item in DB
//version 1.0
//Functionto return ****number**** of found items meet a certain
function fn_countItems($select,$DBfrom){
    global $con;

    $stmt=$con->prepare("SELECT $select FROM $DBfrom");
    $stmt->execute();
    return $stmt->fetchColumn();
}

//Get latest Records Function v1.0
//Get latest Items from DB [Users, Items, Comments] by a pre definined limit
function fn_getLatest($select,$table,$where,$order,$type,$limit){
    global $con;
    $stmt=$con->prepare("SELECT $select FROM $table $where $order $type  $limit");
    $stmt->execute();
    return $stmt->fetchAll();

}

//my 1st use of PHPMAILER
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';
//Version 1.0 
function sendEmail($to,$receivename,$subject,$body,$altbody){    
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';       // Your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'assessmentinstitution2023@gmail.com';     // SMTP username
        $mail->Password = 'qumz uaod kltq zqur';       // SMTP password
        // $mail->SMTPSecure = 'tls';              // or 'ssl'
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;              // or 'ssl'
        $mail->Port = 587;                      // or 465 for ssl

        // Recipients
        $sender=array('assessmentinstitution2023@gmail.com', 'Question Generator');
        $mail->setFrom($sender[0],$sender[1]);
        $mail->addAddress($to, $receivename);
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altbody;//'Hello! Thanks for joining us.'

        $mail->send();
    } catch (Exception $e) {
        echo "Email could not be sent. Error: {$mail->ErrorInfo}";
    }
}
//my 1st API call 2025-06-17
function callOutApi($i){
    ?>
    <script>
            async function sendData() {
        try {
            const selectedValue = <?php echo $i;?>;
            const outrecall = document.querySelector(".outrecall");
            const response = await fetch("includes\\templates\\route\\outcome.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ value: selectedValue })
            });

            if (!response.ok) {
            throw new Error(`Server error: ${response.status}`);
            }

            const result = await response.json();
            outrecall.textContent=" this item is found (" + result["message"] + ") times in Bank";
        } catch (error) {
            console.error("Fetch error:", error.message);
        }
        }
        sendData();
        </script>
    <?php
}

//********************************************** */
//*           FRONT END FUNCTIONS                */
//********************************************** */

// Home page Dashboard  functions


// count member who bought my items function V1.0
function fn_countItems2($select,$from,$tblecol,$relation,$cond,$more = "",$tblecol2 = "",$rel2 = "",$cond2 = ""){
    global $con;

    $stmt=$con->prepare("SELECT $select FROM $from WHERE $tblecol $relation $cond $more $tblecol2 $rel2 $cond2");
    $stmt->execute();
    return $stmt->fetchColumn();
}

function fn_checkMemberType($select,$from,$cond,$rel_value,$more = "",$cond2="",$rel2="",$extra=""){
    global $con;
    $stmt=$con->prepare("SELECT $select FROM $from WHERE $cond $rel_value $more $cond2 $rel2 $extra");
    $stmt->execute();
    return $stmt->fetch();
}


// adjust all subject items for your selection

//******************************************* */
//*      START NEW QUESTION HTML ADJUST       */
//******************************************* */
function getSubjectItemsPHP($type,$stage, $subject, $grade,$lang,$selection=1){
    //function to adjust teacher choices from DB
    global $con;
    //prepare data from teacher items
    $db= $con->prepare("SELECT *
        FROM
            teacher_items
        WHERE
            teacher_id=?
    ");
    $db->execute(array($_SESSION['teacher_id']));
    $DBrows=$db->fetchAll();
    for($i=0;$i<count($DBrows);$i++){
        // print_r($DBrows[$i]);
        $itemid[$i]= $DBrows[$i]['item_id'];
        $type[$i]= $DBrows[$i]['type'];
        $stage[$i]= $DBrows[$i]['stage'];
        $subject[$i]= $DBrows[$i]['subject'];
        $grade[$i]= $DBrows[$i]['grade'];
        $grade[$i]= $DBrows[$i]['grade'];
        $lang[$i]= $DBrows[$i]['lang'];
    };
    //fill the contents in WebPage
    $typeTxt=[
        "1"=>"National Egyptian Education",
        "2"=>"STEM",
        "3"=>"SAT",
        "4"=>"IG"
    ];
    $stageTxt=[
        "1"=>"Elementary",
        "2"=>"Middle",
        "3"=>"Senior"
    ];
    $subjectTxt=[
        "PHY"=>"PHY",
        "CHE"=>"CHE",
        "BIO"=>"BIO",
        "GEO"=>"GEO",
        "MAT"=>"MAT"
    ];
    $gradeTxt=[
        "G1"=>"G1",
        "G2"=>"G2",
        "G3"=>"G3",
        "G4"=>"G4",
        "G5"=>"G5",
        "G6"=>"G6",
        "G7"=>"G7",
        "G8"=>"G8",
        "G9"=>"G9",
        "G10"=>"G10",
        "G11"=>"G11",
        "G12"=>"G12",
    ];
    $LangTxt=[
        "ENG"=>"English",
        "AR"=>"عربى",
    ];
    ?>
    <!-- START Question FROM  BLOCK -->
    <!-- //call from form -->
    <!-- START TEACHING  BLOCK -->
    <div class="teaching">
        <!-- adjust numbering -->
        <div class="numberzero" hidden></div>
        <!-- STARTS type of education FIELD -->
        <div class="type">
            <h5><?php echo fn_lang('TYPE') ?></h5>
            <?php
            ?>
            <div>
                <select name="type" class="type form-select" required <?php echo (count($type)==$selection)? "disabled" : "";?>>
                    <?php
                    for($i=0;$i<count($type);$i++){
                        echo "<option value=".$i.">".$typeTxt[$type[$i]]."</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <!-- ENDS type of education FIELD -->
        <!-- STARTS Stage FIELD -->
        <div class="stage" >
            <h5><?php echo fn_lang('STAGE') ?></h5>
            <div>
                <select name="stage" class="stage form-select" required data-state='invalid' disabled>
                    <?php
                    for($i=0;$i<count($stage);$i++){
                        echo "<option value=".$i.">".$stageTxt[$stage[$i]]."</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <!-- ENDS Stage FIELD -->
        <!-- STARTS Subject FIELD -->
        <div class="subject" >
            <h5><?php echo fn_lang('SUBJECT') ?></h5>
            <div>
                <select name="subject" class="subject form-select" required data-state='invalid' disabled>
                    <?php
                    for($i=0;$i<count($subject);$i++){
                        echo "<option value=".$i.">".$subjectTxt[$subject[$i]]."</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <!-- ENDS Subject FIELD -->
        <!-- STARTS GRADE FIELD -->
        <div class="grade" >
            <h5><?php echo fn_lang('GRADE') ?></h5>
            <div>
                <select name="grade" class="grade form-select" required data-state='invalid' disabled>
                    <?php
                    for($i=0;$i<count($grade);$i++){
                        echo "<option value=".$i.">".$gradeTxt[$grade[$i]]."</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <!-- ENDS GRADE FIELD -->
        <!-- STARTS LANG FIELD -->
        <div class="lang" >
            <h5><?php echo fn_lang('LANG') ?></h5>
            <div>

                <select name="lang" class="lang form-select" required data-state='invalid' disabled>
                    <?php
                    for($i=0;$i<count($lang);$i++){
                        echo "<option value=".$i.">".$LangTxt[$lang[$i]]."</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <!-- ENDS LANG FIELD -->
    </div>
    <!-- ENDS TEACHING BLOCK -->

    <?php
    // call function to fill the learning outcomes for 1st time


    $outcomes=fn_getOutCome($typeTxt[$type[0]],$stageTxt[$stage[0]],$subject[0],$grade[0],$LangTxt[$lang[0]]);
    ?>
    <!-- STARTS Learning OutCome ZONE -->
    <div class="learning_outcomes" >
        <h5>Learning Outcomes</h5>
        <!-- STARTS CHAPTER FIELD -->
        <div class="col-xs-2">
            <select name="outcome" class=" form-select outcome" required data-state='invalid' style="width:100px;">
                <?php
                for($i=0;$i<count($outcomes);$i++){
                    ?>
                    <option style='width: 400px' value="<?php echo $outcomes[$i]['outcome_id'];?>">
                        Unit(<?php echo $outcomes[$i]["unit"];?>) -
                        CH(<?php echo $outcomes[$i]["chapter"];?>) -
                        ITEM(<?php echo $outcomes[$i]["item"];?>) -
                        <?php echo substr($outcomes[$i]["content"],0,60);?>
                    </option>;
                    <?php
                }
                ?>
            </select>
        </div>
        <!-- ENDS  CHAPTER FIELD -->
    </div>
    <!-- ENDS  Learning OutCome ZONE -->
    <!-- //return to the form -->
    <?php
}
// END of function getSubjectItemsPHP

// START of function Call From JS file
if (isset($_POST['v1'])) {
    $type = $_POST['v1'];
    $stage = $_POST['v2'];
    $subject = $_POST['v3'];
    $grade = $_POST['v4'];
    $lang = $_POST['v5'];
    $outcomeHTML = $_POST['v6'];
    $selection=$_POST['v7'];
    $response="You have Selected".$selection;
    echo $response;
    // for($i=0;$i<count($outcomes);$i++){
    //     echo $outcomes[$i]['content'];
    // }
}

//get learning outcomes from DBFILE
function fn_getOutCome($type,$stage,$subject,$grade,$lang){
    $typeNo=[
        "National Egyptian Education"=>1,
        "STEM"=>2,
        "SAT"=>3,
        "IG"=>4
    ];
    $stageNo=[
        "Elementary"=>1,
        "Middle"=>2,
        "Senior"=>3
    ];
    $langNo=[
        "English"=>"ENG",
        "عربى"=>"AR",
    ];

    $DB_outcomes= fn_getRecords("*","learning_outcomes","WHERE type=$typeNo[$type] AND stage=$stageNo[$stage] AND subject='$subject' AND grade='$grade' AND lang='$langNo[$lang]'");
    if ($DB_outcomes==""){
        $DB_outcomes=["NULL"];
    }
    return $DB_outcomes;
}
//get learning outcomes from DBFILE
// Function Version 2.0
function fn_getOutComeV2($type,$stage,$subject,$grade,$lang){
    $DB_outcomes= fn_getRecords("*","learning_outcomes","WHERE type=$type AND stage=$stage AND subject='$subject' AND grade='$grade' AND lang='$lang'");
    if ($DB_outcomes==""){
        $DB_outcomes=["NULL"];
    }
    return $DB_outcomes;
}


//******************************************* */
//*      ENDS NEW QUESTION HTML ADJUST        */
//******************************************* */

/******************************************* */
/*      STARTS QUESTION BAR BUTTONS          */
/******************************************* */
function fn_QuestionBar($classname){
    ?>
    <div class="walid_math">
        <div class="header" dir="ltr">
            <div class="special format">
                <div class="qtoolbar">
                    <span class="" data-name="1" title="Title">Header</span>
                    <span class="" data-name="2" title="Bold">B</span>
                    <span class="" data-name="3" title="Italic">I</span>
                    <span class="" data-name="4" title="Underlined">U</span>
                </div>
            </div>
            <div class="separator"></div>
            <div class="special">
                <div class="qtoolbar">
                    <span class="" data-name="5" title="Power">X<sup>2</sup></span>
                    <span class="" data-name="6"title="subscript">X<sub>2</sub></span>
                    <span class="" data-name="7"title="small script">small</span>
                    <span class="" data-name="8" title="Ohm symbol">&ohm;</span>
                </div>
            </div>
            <div class="separator"></div>
            <div class="special">
                <div class="qtoolbar">
                    <span class="" data-name="9" title="sqare root"><sub>\</sub>/x</span>
                    <span class="" data-name="10" title="division">x/y</span>
                </div>
            </div>
            <div class="separator"></div>
            <div class="special">
                <div class="qtoolbar">
                    <span class="" data-name="11" title="tab"><strong>-></strong></span>
                    <span class="vector" data-name="12" title="Vector">x</span>
                    <span class="bar" data-name="13" title="bar">x</span>
                </div>
            </div>
            <div class="separator"></div>
            <div class="special">
                <div class="qtoolbar">
                    <span class="" data-name="14" title="normal">(-)</span>
                    <span class="" data-name="15" title="round">(-)</span>
                    <span class="" data-name="16" title="square">[-]</span>
                    <span class="" data-name="17" title="curl">(-)</span>
                </div>
            </div>
            <div class="separator"></div>
            <div class="special">
                <div class="qtoolbar">
                    <span class="arrow" data-name="18" title="Arrow"></span>
                    <span class="arrow cndUp" data-name="19" title="Arrow&cond"><span>x</span></span>
                    <span class="arrow cndUpDown" data-name="20" title="Arrow&cond"><span>x</span></span>
                    <span class="arrowVert" data-name="21" title="rise"></span>
                    <span class="arrowVert ppt" data-name="22" title="ppt"></span>
                </div>
            </div>
        </div>

        <div class="textarea_container">
            <?php if($classname=="stem" || $classname=="substem") {?>                
            <textarea type="text" name="<?php echo $classname?>" id="textarea-<?php echo $classname;?>" class="textarea <?php echo $classname?>" rows="4" <?php if($classname=="stem") echo "required";?>></textarea>
            <?php }else{?>
                <textarea type="text" name="<?php echo $classname?>" id="<?php echo $classname?>" rows="4" class="textarea option form-control "></textarea>
            <?php } ?>
            <div class="replica <?php echo $classname?>" dir="ltr"></div>
        </div>
        <div class="Qfooter">
            Powered by: <a href="https://wailpoet76.github.io/CV/" target="_blank">Lion Coder</a>
        </div>
    </div>        
<?php
}

/******************************************* */
/*      ENDS QUESTION BAR BUTTONS          */
/******************************************* */

/******************************************* */
/*      START REVISION PAGE                  */
/******************************************* */
function fn_revisionCancel($reviewQID,$QID){
    echo "<div class='alert alert-danger'>";
    echo "<p>$reviewQID</p>";
    echo "<p>$QID</p>";
    echo "</div>";
}
/******************************************* */
/*      START REVISION PAGE                  */
/******************************************* */
// End Of phpFile and last tag
?>