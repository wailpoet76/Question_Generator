<?php
// Auther: Walid Bakr
// Date: 2024-07-15
// Last Update: 2025-06-15
// Description: PHP HOME MAIN Page


session_start();
$pageTitle = 'Homepage';
$langset = isset($_GET['Langset']) ? $_GET['Langset'] : "Eng"; //default langauges is English each time
include 'init.php'; // initiate the file with headers and navbar


//check if the user session is found and the call is from login.php or goto else
if (isset($_SESSION['teacher_name'])) {
    //get user name to welcome him
    if ($langset == "Eng") {
        $welname = $teacherDB['ename'];
    } else {
        $welname = $teacherDB['aname'];
    }
    $welname = explode(" ", $welname);
    $shortname = $welname[0] . " " . $welname[count($welname) - 1];
    $do = isset($_GET['do']) ? $_GET['do'] : "Dash";
?>
    <!-- adding background effect  grounds for stop-->
    <!-- adding background effect -->
    <!-- Start Coding -->
    <div class="container" dir="<?php if ($langset == "Eng") {
                                    echo "ltr";
                                } else {
                                    echo "rtl";
                                } ?>">
        <h6 class="float-r-t"><?php echo fn_lang('WELCOME') . " " . $shortname ?></h6>

    </div>
    <?php
    //Active account
    $DBO = $con->prepare("SELECT *
        FROM
            teacher_items                        
        WHERE
            teacher_id = ?
        AND
            account_status = ?
            ");
    $DBO->execute(array($_SESSION['teacher_id'],1));
    $activeAccount=$DBO->fetch();                
    //Approved Q only
    $DBO= $con->prepare("SELECT COUNT(*)
        FROM
            questions_create                                       
        WHERE
            (rev_one_approval = ? AND rev_two_approval  = ?)
        AND
            type = ?
        AND
            stage = ?
        AND
            grade = ?
        AND
            subject = ?
        AND
            lang = ?
            ");
    $DBO->execute(array(1,1,$activeAccount['type'],$activeAccount['stage'],$activeAccount['grade'],$activeAccount['subject'],$activeAccount['lang']));
    $approvedQuestions=$DBO->fetchColumn();
    //created Q only by Profile
    $DBO= $con->prepare("SELECT COUNT(*) 
        FROM 
            questions_create
        WHERE
            type = ?
        AND
            stage = ?
        AND
            grade = ?
        AND
            subject = ?
        AND
            lang = ?
            ");
    $DBO->execute(array($activeAccount['type'],$activeAccount['stage'],$activeAccount['grade'],$activeAccount['subject'],$activeAccount['lang']));
    $createdQuestions=$DBO->fetchColumn();
    //User created Q only by Profile
    $DBO= $con->prepare("SELECT COUNT(*) 
        FROM 
            questions_create
        WHERE
            type = ?
        AND
            stage = ?
        AND
            grade = ?
        AND
            subject = ?
        AND
            lang = ?
        AND
            teacher_id = ?
            ");
    $DBO->execute(array($activeAccount['type'],$activeAccount['stage'],$activeAccount['grade'],$activeAccount['subject'],$activeAccount['lang'],$_SESSION['teacher_id']));
    $userQuestions=$DBO->fetchColumn();
    //print data
    ?>
    <div class="datashow container" dir="<?php if ($langset == "Eng") {echo "ltr";} else {echo "rtl";} ?>">
        <p class="float-r-t"><?php echo fn_lang('CREATEDQ') . ": " . $createdQuestions;?></p>
        <p class="float-r-t"><?php echo fn_lang('APPROVEDQ') . ": " . $approvedQuestions;?></p>
        <p class="float-r-t"><?php echo fn_lang('USERCREAT') . ": " . $userQuestions;?></p>
    </div>

    <h1 class="text-center" id="header">Dash Board</h1>
    <!-- START Main Contents Containertabs BOX -->
    <div class="cont content" dir="<?php echo ($langset == "Eng") ? "ltr" : "rtl"; ?>">
        <!-- START sidebar BOX -->
        <div class="sidebar">
            <div class="urg">
                <p class="logo"><i class="fa-solid fa-bolt fa-2x" style="color: #333d29;"></i>
                <h5><?php echo fn_lang('QG'); ?></h5>
            </div>
            <div class="sidebar-contents">
                <ul class="sb-items">
                    <li><a href="homepage.php?do=Urgent&Langset=<?php echo ($langset) ?>" id="tag1"><i class="fa-solid fa-exclamation-triangle text-danger fa-1x Urgent" style="color: #F00;"></i> <span><?php echo fn_lang('URGENT'); ?></span></a></li>
                    <li><a href="homepage.php?do=Myquest&Langset=<?php echo ($langset) ?>" id="tag2"><i class="fa-regular fa-folder-open fa-1x Myquest" style="color: var(--Dark-Green-color);"></i> <span><?php echo fn_lang('QUESTIONS'); ?></span></a></li>
                    <li><a href="homepage.php?do=Newquest&Langset=<?php echo ($langset) ?>" id="tag3"><i class="fa-solid fa-folder-plus fa-1x Newquest" style="color: var(--Dark-Green-color);"></i> <span><?php echo fn_lang('CREATEQ'); ?></span></a></li>
                    <li><a href="homepage.php?do=Dataquest&Langset=<?php echo ($langset) ?>" id="tag4"><i class="fa-solid fa-chart-simple fa-1x Dataquest" style="color: var(--Dark-Green-color);"></i> <span><?php echo fn_lang('DATAQ'); ?></span></a></li>
                </ul>
                <ul class="sb-items">
                    <li><a href="homepage.php?do=Collectquest&Langset=<?php echo ($langset) ?>" id="tag5"><i class="fa-solid fa-chain fa-1x collectquest" style="color: var(--Lion-color);"></i><span> <?php echo fn_lang('GETQ'); ?></span></a></li>
                    <li><a href="profile.php?do=Profile&Langset=<?php echo ($langset) ?>"><i class="fa-solid fa-gear fa-1x profile" style="color: var(--Lion-color);"></i><span><?php echo fn_lang('ACCOUNT'); ?></span></a></li>
                    <li><a href="homepage.php?do=Logout"><i class="fa-solid fa-door-open fa-1x Logout" style="color: var(--Lion-color);"></i><span><?php echo fn_lang('EXIT'); ?></span></a></li>

                </ul>
                <!-- END sidebar BOX -->
            </div>
        </div>
        <!-- END sidebar BOX-->
        <?php
        switch ($do) {
            case 'Dash':
                # code...
                //prepare for Urgent Q
                //prepare data from teacher_items
                $teacherProfile = fn_getRecordsV2("*", "teacher_items", 
                "WHERE teacher_id=? AND account_status=?",
                array($_SESSION['teacher_id'], 1));
                $dbUrgent = $con->prepare("SELECT *
                        FROM
                            questions_create
                        INNER JOIN
                            teachers
                        ON
                            questions_create.teacher_id=teachers.teacher_id
                        WHERE
                            questions_create.admin_urgent=?
                        AND
                            teachers.ename=?
                        AND
                            questions_create.teacher_item_id=?
                        ");
                $dbUrgent->execute(array(1, $_SESSION['teacher_name'],$teacherProfile[0]['item_id']));
                $DBrowsUrgent = $dbUrgent->fetchAll();
                //prepare for Finished Q to be sent to revision
                $dbMyQ = $con->prepare("SELECT *
                        FROM
                            questions_create
                        INNER JOIN
                            teachers
                        ON
                            questions_create.teacher_id=teachers.teacher_id
                        INNER JOIN
                            question_review
                        ON
                            questions_create.question_id=question_review.question_id
                        WHERE
                            questions_create.question_case=?
                        AND
                            questions_create.teacher_item_id=?
                        AND
                            question_review.review_status=?
                        ORDER BY
                            creation_date DESC
                        ");
                $dbMyQ->execute(array(1,$teacherProfile[0]['item_id'],2));
                $DBrowsMyQ = $dbMyQ->fetchAll();
                //prepare teacherDB for Created Q
                $dbCreatedQ = $con->prepare("SELECT *
                        FROM
                            questions_create
                        INNER JOIN
                            teachers
                        ON
                            questions_create.teacher_id=teachers.teacher_id
                        WHERE
                            questions_create.question_case=?
                        AND
                            questions_create.teacher_item_id=?
                        ORDER BY
                            creation_date DESC
                        ");
                $dbCreatedQ->execute(array(0,$teacherProfile[0]['item_id']));
                $DBrowsdbCreatedQ = $dbCreatedQ->fetchAll();
                //prepare tab of QuestionDatabase for pilot Q
                $dbo = $con->prepare("SELECT *
                        FROM
                            questions_create
                        INNER JOIN
                            teachers
                        ON
                            questions_create.teacher_id=teachers.teacher_id
                        WHERE
                            questions_create.question_case=?
                        AND
                            questions_create.pilot=?
                        AND
                            questions_create.teacher_item_id=?
                        ORDER BY
                            creation_date DESC
                        ");
                $dbo->execute(array(2,1,$teacherProfile[0]['item_id']));
                $DBrowsdbQ = $dbo->fetchAll();
                
                //set up  variables from DB
                $questions_urgent = (count($DBrowsUrgent) > 0) ? count($DBrowsUrgent) : 0;
                @$myUrgentDate = explode(" ", $DBrowsUrgent[0]['creation_date']);

                $myquestions = (count($DBrowsMyQ) > 0) ? count($DBrowsMyQ) : 0;
                @$myQDate = explode(" ", $DBrowsMyQ[0]['creation_date']);

                $createquestions = (count($DBrowsdbCreatedQ) > 0) ? count($DBrowsdbCreatedQ) : 0;
                @$myCreateDate = explode(" ", $DBrowsdbCreatedQ[0]['creation_date']);

                $databasequestions = (count($DBrowsdbQ) > 0) ? count($DBrowsdbQ) : 0;;
                @$myDatabaseQDate = explode(" ", $DBrowsdbQ[0]['submit_date']);

                $collectquestions = 0;
        ?>
                <!-- START Contents Containertabs BOX -->
                <div class="container tabs" dir="<?php echo ($langset == "Eng") ?  "ltr" : "rtl";  ?>">
                    <!-- START Urgent BOX -->
                    <div class="urgent">
                        <div class="colorbar <?php echo (count($DBrowsUrgent) > 0) ? 'urgactive' : ''; ?>"></div>
                        <div class="create"><?php echo fn_lang("CREATED") ?>: <?php echo $myUrgentDate[0] ? $myUrgentDate[0] : "EMPTY"; ?></div>
                        <div class="tabcontent">
                            <p class="logo"><i class="fa-solid fa-exclamation-triangle text-danger fa-3x" style="color: #F00;"></i>
                            <div class="label">
                                <h5><?php echo fn_lang("URGENT") ?></h5>
                            </div>
                        </div>
                        <div class="tabfooter">
                            <p><?php echo fn_Lang("RESULT");
                                echo " (" . $questions_urgent . ")" ?></a></p>
                        </div>
                    </div>
                    <!-- END Urgent BOX -->
                    <!-- START my Questions BOX -->
                    <div class="myquestions">
                        <div class="colorbar <?php echo (count($DBrowsMyQ) > 0) ? 'active' : ''; ?>"></div>
                        <div class="create"><?php echo fn_lang("CREATED") ?>: <?php echo $myQDate[0] ? $myQDate[0] : "EMPTY"; ?></div>
                        <div class="tabcontent">
                            <p class="logo"><i class="fa-solid fa-folder-open fa-3x" style="color: #0BC279;"></i>
                            <div class="label">
                                <h5><?php echo fn_lang("QUESTIONS") ?></h5>
                            </div>
                        </div>
                        <div class="tabfooter">
                            <p><?php echo fn_Lang("RESULT");
                                echo " (" . $myquestions . ")" ?></a></p>
                        </div>
                    </div>
                    <!-- END my Questions BOX -->
                    <!-- START Add New Question BOX -->
                    <div class="newquestion">
                        <div class="colorbar <?php echo (count($DBrowsdbCreatedQ) > 0) ? 'active' : ''; ?>"></div>
                        <div class="create"><?php echo fn_lang("CREATED") ?>: <?php echo $myCreateDate[0] ? $myCreateDate[0] : "EMPTY"; ?></div>
                        <div class="tabcontent">
                            <p class="logo"><i class="fa-solid fa-folder-plus fa-3x" style="color: #0BC279;"></i>
                            <div class="label">
                                <h5><?php echo fn_lang("CREATEQ") ?></h5>
                            </div>
                        </div>
                        <div class="tabfooter">
                            <p><?php echo fn_Lang("RESULT");
                                echo " (" . $createquestions . ")" ?></a></p>
                        </div>
                    </div>
                    <!-- END Add New Question BOX -->
                    <!-- START DATABASE BOX -->
                    <div class="databasequestion">
                        <div class="colorbar <?php echo (($databasequestions) > 0) ? 'active' : ''; ?>"></div>
                        <div class="create"><?php echo fn_lang("SUBMITTED") ?>: <?php echo $myDatabaseQDate[0] ? $myDatabaseQDate[0] : "EMPTY"; ?></div>
                        <div class="tabcontent">
                            <p class="logo"><i class="fa-solid fa-chart-simple fa-3x" style="color: #0BC279;"></i>
                            <div class="label">
                                <h5><?php echo fn_lang("DATAQ") ?></h5>
                            </div>
                        </div>
                        <div class="tabfooter">
                            <p><?php echo fn_Lang("RESULT");
                                echo " (" . $databasequestions . ")" ?></a></p>
                        </div>
                    </div>
                    <!-- END DATABASE BOX -->
                    <!-- START Add Question BOX -->
                    <div class="collectquestion">
                        <div class="colorbar"></div>
                        <div class="create"><?php echo fn_lang("CREATED") ?>: 2024-09-27</div>
                        <div class="tabcontent">
                            <p class="logo"><i class="fa-solid fa-chain fa-3x" style="color: #0BC279;"></i>
                            <div class="label">
                                <h5><?php echo fn_lang("GETQ") ?></h5>
                            </div>
                        </div>
                        <div class="tabfooter">
                            <p><?php echo fn_Lang("RESULT");
                                echo " (" . $collectquestions . ")" ?></a></p>
                        </div>
                    </div>
                    <!-- END Add Question BOX -->
                </div>
                <!-- END Side Bar Contents Containertabs BOX -->
    </div>
    <!-- END Main Contents Containertabs BOX -->

<?php
                break;

            case 'Urgent':
                $db = $con->prepare("SELECT *
                        FROM
                            questions_create
                        INNER JOIN
                            teachers
                        ON
                            questions_create.teacher_id=teachers.teacher_id
                        WHERE
                            questions_create.admin_urgent=?
                        AND
                            teachers.ename=?
                        ");
                $db->execute(array(1, $_SESSION['teacher_name']));
                $DBrows = $db->fetchAll();
                $questions_urgent = (count($DBrows) > 0) ? count($DBrows) : 0;
?>
    <!-- Page Heading -->
    <script>
        document.getElementById("header").innerText = "<?php echo fn_lang('URGQ'); ?>";
        document.getElementById("tag1").parentNode.classList.add("active");
    </script>

    <div class="container content" dir="<?php if ($langset == "Eng") {
                                            echo "ltr";
                                        } else {
                                            echo "rtl";
                                        } ?>">
        <!-- START Urgent Questions Area -->
        <div class="questions table">
            <?php
                foreach ($DBrows as $eachQ) {
                    # code...
            ?>
                <div class="question contents">
                    <p>Hi one
                </div>
            <?php
                }
            ?>
        </div>
        <div class="question contents">
        <p>Hi collection</p>
        <h1>Under construction</h1>
    </div>
    </div>

<?php
                break;
            case 'Myquest':
                //Prepare DB
                $dbPDO = $con->prepare("SELECT *
                        FROM
                            questions_create
                        INNER JOIN
                            teachers
                        ON
                            questions_create.teacher_id=teachers.teacher_id
                        INNER JOIN
                            question_review
                        ON
                            questions_create.question_id=question_review.question_id
                        WHERE
                            questions_create.question_case=?
                        AND
                            questions_create.teacher_id=?
                        AND
                            question_review.review_status=?
                        ORDER BY
                            creation_date DESC
                        ");
                $dbPDO->execute(array(1,$_SESSION['teacher_id'],2));
                $dbMyNewQ = $dbPDO->fetchAll();
?>
    <!-- Page Heading -->
    <script>
        document.getElementById("header").innerText = "<?php echo fn_lang('QUESTIONS'); ?>";
        document.getElementById("tag2").parentNode.classList.add("active");
    </script>

    <div class="container showtable" dir="<?php if ($langset == "Eng") {
                                            echo "ltr";
                                        } else {
                                            echo "rtl";
                                        } ?>">
        <div class="table-responsive">
            <table class="tableRecords table table-striped table-striped-columns " >
                <caption class="caption-top text-center"><strong><?php echo fn_lang('RETURNED'); ?></strong></caption>
                <thead class="table-dark text-left">
                    <tr>
                        <th>
                            <h5><?php echo fn_lang('STEM'); ?></h5>
                        </th>
                        <th>
                            <h5><?php echo fn_lang('REVICE'); ?></h5>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ($i = 0; $i < count($dbMyNewQ); $i++) {
                        # code...
                    ?>
                        <tr class="text-center">
                            <td class="text-left ">
                                <div>
                                <?php
                                echo $dbMyNewQ[$i]['Question_Body'];
                                ?>
                                </div>
                                <div>
                                <?php
                                if ($dbMyNewQ[$i]['supported_image']===1){
                                    ?>
                                    <img alt="" src="<?php echo IMG_DIR . "Questions/" . $dbMyNewQ[$i]['image_link'];?>"
                                    style="width: 200px; height: 200px;" >
                                <?php     
                                }
                                ?>
                                </div>
                            </td>
                            <td>
                                <a href="?do=ReRevisionCreator&Langset=<?php echo ($langset)?>&record=<?php echo $dbMyNewQ[$i]['question_id'] ?>&reviewQID=<?php echo $dbMyNewQ[$i]['id']?>&reviewRevisorID=<?php echo $dbMyNewQ[$i]['revisor_id'];?>" class="btn btn-primary"><i class='fa fa-check'></i> <?php echo fn_lang('REVICE'); ?></a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        ?>
    </div>

<?php
                break;
            case 'ReRevisionCreator':
                ?>
            <script>
                document.getElementById("header").innerText = "<?php echo fn_lang('REREVISE'); ?>";
                document.getElementById("tag2").parentNode.classList.add("active");
            </script>
            <?php
                
                //prepare data from teacher_items
                $DBrow = fn_getRecordsV2("*", "teacher_items", "WHERE teacher_id=? AND account_status=?", array($_SESSION['teacher_id'], 1));
                $DBEdit = $con->prepare("SELECT *
                FROM
                    questions_create
                WHERE
                    questions_create.question_id=?
                ORDER BY
                    creation_date DESC
                ");
                $DBEdit->execute(array($_GET['record']));
                $DBEditrow = $DBEdit->fetch();
                //Get review items
                $DBO= $con->prepare("SELECT *
                FROM
                    question_review
                WHERE
                    id=?
                ");
                $DBO->execute(array($_GET['reviewQID']));
                $reviewData=$DBO->fetch();//Get review record to analysis
                $DBO= $con->prepare("SELECT *
                FROM
                    teachers
                WHERE
                    teacher_id=?
                    ");
                    $DBO->execute(array($_GET['reviewRevisorID']));
                    $revisorName=$DBO->fetch();//Get revisor name
                    //adjust time
                    $now=new Datetime();
            ?>
            <!-- START Container Contents -->
            <div class="container content" dir="<?php echo ($langset == "Eng") ? "ltr" : "rtl"; ?>">
                <div class="question contents">
                    <!-- START Edit Question Section -->
                    <form action="?do=Update&Langset=<?php echo ($langset);?>&record=<?php echo $DBEditrow['question_id'];?>&review_id=<?php echo $reviewData['id'];?>&returnRevisor=<?php echo $now->format('Y-m-d H:i:s');?>" method="post" enctype="multipart/form-data">
                        <?php
                        $outcomes = fn_getOutComeV2($DBrow[0]['type'], $DBrow[0]['stage'], $DBrow[0]['subject'], $DBrow[0]['grade'], $DBrow[0]['lang']);
                        // start calculating time for creator review
                        ?>
                        <!-- The whole subjects are put here from function.php -->
                        <!-- START REVISOR -->
                        <div class="body mb-3">
                                <label for="revisor" class="fw-semibold"><?php echo fn_lang('REVISORNAME');?>:
                                </label>
                                    <div class="revisor">
                                        <?php echo ($langset=="Eng")? $revisorName['ename']:$revisorName['aname'] ;?>
                                    </div>
                        </div>
                        <!-- END REVISOR -->
                        <!-- START PALIGRISM -->
                        <div class="body mb-3">
                                <label for="paligrism" class="fw-semibold"><?php echo fn_lang('PALIGRISMCHECKER');?>:
                                </label>
                                    <div>
                                        <?php echo fn_lang("PALIGRISM");?>
                                    </div>
                                    <div class="paligrism">
                                        <?php echo ($reviewData['plagiarism_percent']=="")? "0%":$reviewData['plagiarism_percent'] ;?>
                                    </div>
                        </div>
                        <!-- END PALIGRISM -->
                        <!-- START The outcomes -->
                        <div class="body mb-3">
                            <label for="outcome" class="fw-semibold"><?php echo fn_lang('OUTCOME'); ?>
                                <select name="outcome" id="outcome" class="outcome form-select <?php if($reviewData['content_aligned']===1) echo 'revactive';?>"required>
                                    <?php
                                    for ($i = 0; $i < count($outcomes); $i++) {
                                        echo "<option value=" . $outcomes[$i]['outcome_id'] . ">" . "Unit" . $outcomes[$i]['unit'] . " - Ch" . $outcomes[$i]['chapter'] . " - " . $outcomes[$i]['item'] . " - " . substr($outcomes[$i]["content"], 0, 120) . "</option>";
                                    }
                                    // 
                                    ?>
                                </select>
                            </label>
                        </div>
                        <script>
                            document.getElementById("outcome").value = <?php echo $DBEditrow['outcome_id']; ?>; // Change to the option with the saved value
                        </script>
                        <!-- START The REVISOR NOTE -->
                        <?php if($reviewData['content_aligned']===1){
                            ?>
                        <div class="revnote"><?php echo $reviewData['content_aligned_note'] ;?></div>
                        <?php } ?>
                        <!-- END The REVISOR NOTE -->
                        <!-- END The outcomes -->
                        <!-- START The Question Body -->
                        <div>
                            <h5 class="w-100 p-1 QC"><?php echo fn_lang('QCONSTRUCT'); ?></h5>
                            <!-- START WRITTER -->
                            <div class="body mb-3">
                                <label for="writter" class="fw-semibold"><?php echo fn_lang('WRITTER'); ?>
                                </label>
                                <input type="text" name="writter" id="writter"
                                    class="form-control"
                                    value="<?php echo $_SESSION['teacher_name'] ?>"
                                    required disabled>
                            </div>
                            <!-- END WRITTER -->
                            <!-- START DATE -->
                            <div class="body mb-3">
                                <label for="crea_date" class="fw-semibold"><?php echo fn_lang('EDITDATE'); ?>
                                </label>
                                <input type="datetime-local" name="edit_date" id="edit_date"
                                    class="form-control"
                                    value="<?php echo date("Y-m-d H:i:s") ?>"
                                    required disabled>
                            </div>
                            <!-- END DATE -->
                            <!-- START SUBJECT -->
                            <div class="body mb-3">
                                <label for="subject" class="fw-semibold"><?php echo fn_lang('SUBJECT'); ?>
                                </label>
                                <input type="text" name="subject" id="subject"
                                    class="form-control"
                                    value="<?php echo $DBrow[0]['subject'] . "-" . $DBrow[0]['grade'] . "-" . $DBrow[0]['lang'] ?>"
                                    required disabled>
                            </div>
                            <!-- END SUBJECT -->
                            <!-- START DEPTH -->
                            <div class="body mb-3">
                                <label for="depth" class="fw-semibold"><?php echo fn_lang('DEPTH'); ?>
                                    <select name="depth" id="depth" class="depth form-select <?php if($reviewData['dok']===1) echo 'revactive';?>" required>
                                        <option value="RECALL"><?php echo fn_lang('RECALL') ?></option>";
                                        <option value="DIRECT"><?php echo fn_lang('DIRECT') ?></option>";
                                        <option value="STRATEGIC THINKNG"><?php echo fn_lang('STRATEGIC THINKNG') ?></option>";
                                    </select>
                                </label>
                            </div>

                            <script>
                                document.getElementById("depth").value = <?php echo '"' . $DBEditrow['depth'] . '"'; ?>; // Change to the option with the saved value
                            </script>
                            <!-- START The REVISOR NOTE -->
                            <?php if($reviewData['dok']===1){
                                ?>
                            <div class="revnote"><?php echo $reviewData['dok_note'] ;?></div>
                            <?php } ?>
                            <!-- END The REVISOR NOTE -->
                            <!-- END DEPTH -->
                            <!-- START DOKPROFF -->
                            <div class="body mb-3">
                                <label for="dok" class="fw-semibold"><?php echo fn_lang('DOK'); ?>
                                </label>
                                <input type="text" name="dok" id="dok" class="dok form-control <?php if($reviewData['dok_proff']===1) echo 'revactive';?>" value="<?php echo $DBEditrow['dok']; ?>">
                            </div>
                            <!-- START The DOKPROFF NOTE -->
                            <?php if($reviewData['dok_proff']===1){
                                ?>
                            <div class="revnote"><?php echo $reviewData['dok_proff_note'] ;?></div>
                            <?php } ?>
                            <!-- END The DOKPROFF NOTE -->
                            <!-- END DOKPROFF -->
                            <!-- START REFERENCE -->
                            <div class="body mb-3">
                                <label for="REFERENCE_EDIT" class="fw-semibold"><?php echo fn_lang('REFERENCE'); ?>
                                    <select id="REFERENCE_EDIT" name="reference" class="ref_sel form-select <?php if($reviewData['reference']===1) echo 'revactive';?>" required>
                                        <option value="none"><?php echo fn_lang('NO_REF'); ?></option>";
                                        <option value="NA"><?php echo fn_lang('BRAND_NEW'); ?></option>";
                                        <option value="REF"><?php echo fn_lang('REF_SELECT'); ?></option>";
                                    </select>
                                </label>
                                <script>
                                    document.getElementById("REFERENCE_EDIT").value = <?php echo '"' . $DBEditrow['reference'] . '"'; ?>; // Change to the option with the saved value
                                </script>
                                <input type="text" name="ref_inp" id="ref_inp_edit"
                                    class="ref_inp form-control"
                                    <?php if ($DBEditrow['reference'] !== "REF") {
                                        echo "disabled style='display:none;'";
                                    }
                                    ?>
                                    placeholder="<?php echo fn_lang("REF"); ?>"
                                    value=<?php echo "'" . $DBEditrow['ref_txt'] . "'"; ?>>
                            </div>
                            <!-- START The REFERENCE NOTE -->
                            <?php if($reviewData['reference']===1){
                                ?>
                            <div class="revnote"><?php echo $reviewData['reference_note'] ;?></div>
                            <?php } ?>
                            <!-- END The REFERENCE NOTE -->
                            <!-- END REFERENCE -->
                            <!-- START STEM -->
                            <div class="body mb-3 stem_bar">
                                <label for="textarea" class="fw-semibold"><?php echo fn_lang('STEM'); ?>
                            </label>
                            <!-- START STEM OF QUESTION NOTES -->
                            <?php if($reviewData['errors']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('ERROR'); ?>
                            <div class="revnote"><?php echo $reviewData['errors_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['key_good']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('KEYEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['key_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['identifier']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('IDENTIFIER'); ?>
                            <div class="revnote"><?php echo $reviewData['identifier_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['identifier_only']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('IDENTIFIERONLY'); ?>
                            <div class="revnote"><?php echo $reviewData['identifier_only_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['distractors']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('DISTEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['distractors_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['rev_lang']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('LANGEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['rev_lang_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['replicant']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('NEWQ'); ?>
                            <div class="revnote"><?php echo $reviewData['replicant_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['grammer']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('GRAMMEREXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['grammer_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['abbreviation']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('ABBREVEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['abbreviation_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['symbols']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('SYMBOLEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['symbols_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['Negative']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('NEGATIVEEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['Negative_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['letters']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('LETTERSEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['letters_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['stem_end']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('STEMREVEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['stem_end_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['stem_good']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('SIMPLEEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['stem_good_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['stem_third']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('THIRDEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['stem_third_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['stem_modify']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('STEMMODEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['stem_modify_note'] ;?></div>
                            <?php } ?>                            
                            <!-- END STEM OF QUESTION NOTES -->
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('STEMREPORT'); ?>
                            <!-- Math bar inserted here by following function -->
                            <?php echo fn_QuestionBar("stem") ?>
                            <script>
                                document.getElementById('textarea-stem').value = <?php echo json_encode($DBEditrow['Question_Body']); ?>;
                            </script>
                            </div>
                            <!-- END STEM -->
                            <!-- START STEM Image -->
                            <div class="body mb-3 checkbar">
                                <label for="image" class="fw-semibold image_check"><?php echo fn_lang('IMAGE'); ?>
                                    <input type="checkbox" name="stemImageCheck" id="image"
                                        <?php
                                        if ($DBEditrow['supported_image'] != 0)
                                            echo "checked";
                                        ?> hidden>
                                    <div class="enhanced_bar img
                                    <?php
                                    if ($DBEditrow['supported_image'] != 0)
                                        echo "active";
                                    ?>
                                    "></div>
                                </label>
                            </div>
                            <div class="body mb-3">
                                <label for="image_path" class="fw-semibold drop"
                                    <?php
                                    if ($DBEditrow['supported_image'] != 0) {
                                        echo 'style="display: block;"';
                                    } else {
                                        echo 'style="display: none;"';
                                    }
                                    ?>>
                                    <input type="file" accept="image" name="image_path" id="image_path" class="image_path form-control"
                                        autocomplete="on" style="display: none;">
                                    <div class="img">
                                        <img alt="no image" class="imageArea" style="display:<?php echo ($DBEditrow['supported_image'] == 0) ? 'none;' : 'block;' ?>"
                                            src="<?php echo ($DBEditrow['supported_image'] == 0) ? '' : IMG_DIR . "Questions/" . $DBEditrow['image_link']; ?>">
                                        <input class="DBImages" name="stemImageDB" value="<?php echo ($DBEditrow['supported_image'] == 0) ? '' : $DBEditrow['image_link']; ?>" hidden>
                                        <div class="cloud">
                                            <i class="fa fa-cloud-arrow-up fa-3x" color="#0BC279"
                                                <?php
                                                if ($DBEditrow['supported_image'] != 0) {
                                                    echo 'style="display: none;"';
                                                } else {
                                                    echo 'style="display: block;"';
                                                }
                                                ?>></i>
                                            <p
                                                <?php
                                                if ($DBEditrow['supported_image'] != 0) {
                                                    echo 'style="display: none;"';
                                                } else {
                                                    echo 'style="display: block;"';
                                                }
                                                ?>>Drag and drop or click <strong>here</strong><br>to upload Image</p>
                                            <span
                                                <?php
                                                if ($DBEditrow['supported_image'] != 0) {
                                                    echo 'style="display: none;"';
                                                } else {
                                                    echo 'style="display: block;"';
                                                }
                                                ?>>Upload any images from your local storage</span>
                                            <p class="alert alert-danger m-0 p-1 sizeMsg" hidden>Too <strong>Large</strong> File, Try to upload file smaller than <strong>2MB</strong></p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <!-- START STEM Image NOTE -->
                            <?php if($reviewData['stimulus_adjust']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('RECEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['stimulus_adjust_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['stimulus_needed']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('NEEDEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['stimulus_needed_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['stimulus_modification']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('RECEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['stimulus_modification_note'] ;?></div>
                            <?php } ?>                            
                            <!-- END STEM Image NOTE-->
                            <!-- END STEM Image -->
                            <!-- START SUB STEM -->
                            <div class="body mb-3 stem_bar">
                                <label for="substem" class="fw-semibold"><?php echo fn_lang('SUBSTEM'); ?>
                                </label>
                                <!-- Math Bar inserted below -->
                                <?php echo fn_QuestionBar("substem") ?>
                                <script>
                                    document.getElementById('textarea-substem').value = <?php echo  json_encode($DBEditrow['sub_stem']) ; ?>;
                                </script>
                            </div>
                            <!-- START SUBSTEM NOTE -->
                            <!-- END SUBSTEM NOTE -->
                            <?php if($reviewData['stem_end']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('STEMREVEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['stem_end_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['stem_good']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('SIMPLE'); ?>
                            <div class="revnote"><?php echo $reviewData['stem_good_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['stem_third']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('THIRDEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['stem_third_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['stem_modify']===1){
                                ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('STEMMODEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['stem_modify_note'] ;?></div>
                            <?php } ?>
                            <!-- END SUBSTEM -->
                            <!-- START SUBSTEM Image -->
                            <div class="body mb-3 checkbar">
                                <label for="subimage" class="fw-semibold image_check"><?php echo fn_lang('IMAGE'); ?>
                                    <input type="checkbox" name="substemImageCheck" id="subimage"
                                        <?php
                                        if ($DBEditrow['substem_sup_img'] != 0)
                                            echo "checked";
                                        ?> hidden>
                                    <div class="enhanced_bar subimg
                                    <?php
                                    if ($DBEditrow['substem_sup_img'] != 0)
                                        echo "active";
                                    ?>
                                    "></div>
                                </label>
                            </div>
                            <div class="body mb-3">
                                <label for="subimage_path" class="fw-semibold subdrop"
                                    <?php
                                    if ($DBEditrow['substem_sup_img'] != 0) {
                                        echo 'style="display: block;"';
                                    } else {
                                        echo 'style="display: none;"';
                                    }
                                    ?>>
                                    <input type="file" accept="image" name="subimage_path" id="subimage_path" class="image_path form-control"
                                        autocomplete="on" style="display: none;">
                                    <div class="img">
                                        <img alt="no image" class="subimageArea" style="display:<?php echo ($DBEditrow['substem_sup_img'] == 0) ? 'none;' : 'block;' ?>"
                                            src="<?php echo ($DBEditrow['substem_sup_img'] == 0) ? '' : IMG_DIR . "Questions/" . $DBEditrow['substem_image_link']; ?>">
                                        <input class="DBImages" name="subStemImageDB" value="<?php echo ($DBEditrow['substem_sup_img'] == 0) ? '' : $DBEditrow['substem_image_link']; ?>" hidden>
                                        <div class="subcloud cloud">
                                            <i class="fa fa-cloud-arrow-up fa-3x" color="#0BC279"
                                                <?php
                                                if ($DBEditrow['substem_sup_img'] != 0) {
                                                    echo 'style="display: none;"';
                                                } else {
                                                    echo 'style="display: block;"';
                                                }
                                                ?>></i>
                                            <p
                                                <?php
                                                if ($DBEditrow['substem_sup_img'] != 0) {
                                                    echo 'style="display: none;"';
                                                } else {
                                                    echo 'style="display: block;"';
                                                }
                                                ?>>Drag and drop or click <strong>here</strong><br>to upload Image</p>
                                            <span
                                                <?php
                                                if ($DBEditrow['substem_sup_img'] != 0) {
                                                    echo 'style="display: none;"';
                                                } else {
                                                    echo 'style="display: block;"';
                                                }
                                                ?>>Upload any images from your local storage</span>
                                            <p class="alert alert-danger m-0 p-1 sizeMsg" hidden>Too <strong>Large</strong> File, Try to upload file smaller than <strong>2MB</strong></p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <!-- END SUBSTEM Image -->
                            <!-- START OPTIONS -->
                            <!-- START OPTIONS NOTES-->
                            <h3 class="Questions" ><?php echo fn_lang('QUESTIONREV') ?></h3>
                            <?php if($reviewData['response_relation']===1){
                            ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('RESPONDEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['response_relation_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['response_easy']===1){
                            ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('RESPEASEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['response_easy_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['all_above']===1){
                            ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('RESTRICTEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['all_above_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['response_length']===1){
                            ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('OPLENGTHEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['response_length_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['response_grammer']===1){
                            ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('OPGRAEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['response_grammer_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['response_repeat']===1){
                            ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('OPREAPEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['response_repeat_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['Words_special']===1){
                            ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('WORDREAPEATEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['Words_special_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['nouns']===1){
                            ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('NOUNEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['nouns_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['response_numerical']===1){
                            ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('OPNUMEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['response_numerical_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['response_numerical_decimal']===1){
                            ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('OPDECEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['response_numerical_decimal_note'] ;?></div>
                            <?php } ?>
                            <?php if($reviewData['response_modify']===1){
                            ?>
                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('OPMODEXTRA'); ?>
                            <div class="revnote"><?php echo $reviewData['response_modify_note'] ;?></div>
                            <?php } ?>
                            <!-- END OPTIONS NOTES-->
                            <div class="Question coloring">
                                <?php for ($i = 1; $i < 5; $i++) {
                                    $questionName = ["One", "Two", "Three", "Four"]
                                ?>

                                    <!-- START OPTION ONE TWO THREE FOUR TAGS -->
                                    <div class="coloringChildren">
                                        <div class="body Question mb-3 option<?php echo $questionName[$i - 1] ?>">
                                            <div class="body mb-3">
                                                <!-- START TRUE ANSWER -->
                                                <div>
                                                    <label for="radio<?php echo $i ?>" class="fw-semibold"><?php echo fn_lang('TRUE'); ?>
                                                    </label>
                                                    <input type="radio" name="radioOptTrue" id="radio<?php echo $i ?>" class="choiceRadio form-radio" value="<?php echo $i; ?>"
                                                        <?php if ($i == $DBEditrow['correct_answer']) echo "checked"; ?>>
                                                </div>
                                                <!-- END TRUE ANSWER -->
                                                <!-- START ANSWER PHRASE -->
                                                <div>
                                                    <label for="opt<?php echo $i ?>" class="fw-semibold"><?php echo fn_lang('CHOICE') . ' ' . $questionName[$i - 1]; ?>
                                                    </label>
                                                    <?php echo fn_QuestionBar("opt".$i);?>
                                                    <script>
                                                        document.getElementById('<?php echo"opt".$i;?>').value = <?php echo  json_encode($DBEditrow['answer' . $i]) ; ?>;
                                                    </script>
                                                </div>
                                                <!-- END ANSWER PHRASE -->
                                                <!-- START ANSWER REASON -->
                                                <div>
                                                    <label for="opt<?php echo $i ?>Exp" class="fw-semibold"><?php echo fn_lang('CAUSE'); ?>
                                                    </label>
                                                    <input type="text" name="opt<?php echo $i ?>Exp" id="opt<?php echo $i ?>Exp" class="form-control " value=<?php echo '"' . $DBEditrow['ans' . $i . '_explain'] . '"'; ?>>
                                                </div>
                                                <!-- END ANSWER REASON -->
                                            </div>
                                            <!-- START SUPPORT IMAGE -->
                                            <label for="opimage<?php echo $i ?>" class="fw-semibold image_check"><?php echo fn_lang('OPTION'); ?>
                                                <input type="checkbox" name="opimage<?php echo $i ?>" id="opimage<?php echo $i ?>" data-number="<?php echo $i ?>" class="optionImage"
                                                    <?php
                                                    if ($DBEditrow['ans' . $i . '_sup_img'] != 0)
                                                        echo "checked";
                                                    ?> hidden>
                                                <div class="enhanced_bar op <?php echo $i ?> <?php
                                                    if ($DBEditrow['ans' . $i . '_sup_img'] != 0)
                                                        echo "active";
                                                    ?>
                                                "></div>
                                            </label>
                                            <!-- END SUPPORT IMAGE -->
                                        </div>
                                        <!-- START IMAGE SECTION -->
                                        <div class="body mb-3">
                                            <label for="Qopt_path<?php echo $i ?>" class="fw-semibold drop Q Area" data-number="<?php echo $i ?>"
                                                <?php
                                                if ($DBEditrow['ans' . $i . '_sup_img'] != 0) {
                                                    echo 'style="display: block;"';
                                                } else {
                                                    echo 'style="display: none;"';
                                                }
                                                ?>>
                                                <input type="file" accept="image" name="option<?php echo $questionName[$i - 1] ?>_path" id="Qopt_path<?php echo $i ?>" data-number="<?php echo $i ?>" class="Q option_path form-control"
                                                    autocomplete="on" style="display: none;">
                                                <div class="Q  img">
                                                    <img alt="" class="Q imageArea" style="display:<?php echo ($DBEditrow['ans' . $i . '_sup_img'] == 0) ? 'none;' : 'block;' ?>"
                                                        src="<?php echo ($DBEditrow['ans' . $i . '_sup_img'] == 0) ? '' : IMG_DIR . "Questions/" . $DBEditrow['ans' . $i . '_image_link']; ?>">
                                                    <input class="DBImages" name="<?php echo 'ans' . $i . 'ImageDB'; ?>" value="<?php echo ($DBEditrow['ans' . $i . '_sup_img'] == 0) ? '' : $DBEditrow['ans' . $i . '_image_link']; ?>" hidden>
                                                    <div class="Q cloud">
                                                        <i class="fa fa-cloud-arrow-up fa-3x" color="#0BC279" <?php
                                                                                                                if ($DBEditrow['ans' . $i . '_sup_img'] != 0) {
                                                                                                                    echo 'style="display: none;"';
                                                                                                                } else {
                                                                                                                    echo 'style="display: block;"';
                                                                                                                }
                                                                                                                ?>></i>
                                                        <p
                                                            <?php
                                                            if ($DBEditrow['ans' . $i . '_sup_img'] != 0) {
                                                                echo 'style="display: none;"';
                                                            } else {
                                                                echo 'style="display: block;"';
                                                            }
                                                            ?>>Drag and drop or click <strong>here</strong><br>to upload Image</p>
                                                        <span
                                                            <?php
                                                            if ($DBEditrow['ans' . $i . '_sup_img'] != 0) {
                                                                echo 'style="display: none;"';
                                                            } else {
                                                                echo 'style="display: block;"';
                                                            }
                                                            ?>>Upload any images from your local storage</span>
                                                        <p class="alert alert-danger m-0 p-1 optionsImg" hidden>Too <strong>Large</strong> File, Try to upload file smaller than <strong>2MB</strong></p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        <!-- END IMAGE SECTION -->
                                    </div>
                                    <!-- END OPTION ONE TWO THREE FOUR TAGS -->
                                <?php
                                }
                                // END LOOPING -->
                                echo "<br>"; ?>
                            </div>
                            <!-- END OPTIONS -->
                        </div>
                        <!-- END The Question Body -->
                        <!-- START VARIABLES SECTION -->
                        <div class="d-grid gap-3 col-12 offset-0">
                            <input type="text" name="writter" value="<?php echo $_SESSION['teacher_name'] ?>" hidden>
                            <input type="datetime-local" name="creation_date" value="<?php echo date("Y-m-d H:i:s") ?>" hidden>
                            <input type="text" name="type" value="<?php echo $DBrow[0]['type'] ?>" hidden>
                            <input type="text" name="stage" value="<?php echo $DBrow[0]['stage'] ?>" hidden>
                            <input type="text" name="subject" value="<?php echo $DBrow[0]['subject'] ?>" hidden>
                            <input type="text" name="grade" value="<?php echo $DBrow[0]['grade'] ?>" hidden>
                            <input type="text" name="lang" value="<?php echo $DBrow[0]['lang'] ?>" hidden>
                        </div>
                        <!-- END VARIABLES SECTION -->
                        <div class="d-flex gap-3 col-12 offset-0">
                            <!-- START BTN AREA -->
                            <!-- START SUMBIT BTN -->
                            <div class="d-grid col-3">
                                <input type="submit" id="submitbtn" name="updateDB" value="<?php echo fn_lang('RETURNTOREVISOR') ?>" class="btn success  btn-lg">
                            </div>
                            <!-- END SUMBIT BTN -->
                            <!-- START CANCEL BTN -->
                            <div class="d-grid col-3">
                                <a href="?do=Dash&Langset=<?php echo ($langset) ?>" class="btn  cancel  btn-lg">Cancel</a>
                            </div>
                            <!-- END CANCEL BTN -->
                            <!-- END BTN AREA -->
                        </div>
                    </form>

                    <!-- END Edit Question Section -->


                </div>
            </div>
            <?php
                break;
            case 'Newquest':
                //prepare data from teacher_items
                $DBrow = fn_getRecordsV2("*", "teacher_items", 
                "WHERE teacher_id=? AND account_status=?",
                array($_SESSION['teacher_id'], 1));
                $DBEdit = $con->prepare("SELECT *
                        FROM
                            questions_create
                        INNER JOIN
                            teachers
                        ON
                            questions_create.teacher_id=teachers.teacher_id
                        WHERE
                            questions_create.question_case=?
                        AND
                            questions_create.teacher_item_id=?
                        ORDER BY
                            creation_date DESC
                        ");
                $DBEdit->execute(array(
                    0,$DBrow[0]['item_id']
                    ));
                $DBEditrows = $DBEdit->fetchAll();
                
?>
    <script>
        document.getElementById("header").innerText = "<?php echo fn_lang('QCREATOR'); ?>";
        document.getElementById("tag3").parentNode.classList.add("active");
    </script>
    <!-- START Container Contents -->
    <div class="container content" dir="<?php echo ($langset == "Eng") ? "ltr" : "rtl"; ?>">
        <!-- START sidebar BOX -->
        <div class="question contents">
            
            <!-- START Edit Question Section -->
            <div class="table-responsive">
                <input type="button" class="btn btn-primary m-2" id="showTable" value="<?php echo fn_lang('TABLESHOW'); ?>">
                <table class="tableRecords table table-striped table-striped-columns " style="display: none;">
                    <caption class="caption-top text-center"><strong>My Created Questions </strong></caption>
                    <thead class="table-dark text-left">
                        <tr>
                            <th>
                                <h5><?php echo fn_lang('STEM'); ?></h5>
                            </th>
                            <th>
                                <h5><?php echo fn_lang('EDIT'); ?></h5>
                            </th>
                            <th>
                                <h5><?php echo fn_lang('DELETE'); ?></h5>
                            </th>
                            <th>
                                <h5><?php echo fn_lang('SEND'); ?></h5>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        for ($i = 0; $i < count($DBEditrows); $i++) {
                            # code...
                        ?>
                            <tr class="text-center">
                                <td class="text-left ">
                                    <?php
                                    echo $DBEditrows[$i]['Question_Body'];
                                    ?>
                                </td>
                                <td>
                                    <a href="?do=Edit&Langset=<?php echo ($langset) ?>&record=<?php echo $DBEditrows[$i]['question_id'] ?>" class="btn btn-success "><i class='fa fa-edit'></i> <?php echo fn_lang('EDIT'); ?></a>
                                </td>
                                <td>
                                    <a href="?do=Delete&Langset=<?php echo ($langset) ?>&record=<?php echo $DBEditrows[$i]['question_id'] ?>" class="btn btn-danger confirm"><i class='fa fa-trash'></i> <?php echo fn_lang('DELETE'); ?></a>
                                </td>
                                <td>
                                    <!--old <a href="?do=RevisionSend&Langset... -->
                                    <a href="?do=RevisionJump&Langset=<?php echo ($langset) ?>&DBrecord=<?php echo $DBEditrows[$i]['question_id'] ?>" class="btn btn-primary"><i class='fa fa-check'></i> <?php echo fn_lang('SEND'); ?></a>
                                </td>
                            </tr>
                        <?php
                        }

                        ?>
                    </tbody>
                </table>
            </div>

            <!-- END Edit Question Section -->
            <form action="?do=Save&Langset=<?php echo ($langset); ?>" method="post" enctype="multipart/form-data">
                <?php
                $outcomes = fn_getOutComeV2($DBrow[0]['type'], $DBrow[0]['stage'], $DBrow[0]['subject'], $DBrow[0]['grade'], $DBrow[0]['lang']);
                ?>
                <!-- The whole subjects are put here from function.php -->
                <!-- START The outcomes -->
                <div>
                    <label for="outcome"><?php echo fn_lang('OUTCOME'); ?>
                        <select name="outcome" id="outcome" class="outcome form-select API" required>
                            <?php
                            for ($i = 0; $i < count($outcomes); $i++) {
                                echo "<option value=" . $outcomes[$i]['outcome_id'] . ">" . "Unit" . $outcomes[$i]['unit'] . " - Ch" . $outcomes[$i]['chapter'] . " - " . $outcomes[$i]['item'] . " - " . substr($outcomes[$i]["content"], 0, 120) . "</option>";
                            }
                            ?>
                        </select>
                    </label>
                    <!-- outcome item number in Question Bank -->
                    <span class="outrecall"></span>
                    <?php echo callOutApi($outcomes[0]['outcome_id']);?>
                </div>
                <!-- END The outcomes -->
                <!-- START The Question Body -->
                <div>
                    <h5 class="w-100 p-1 QC"><?php echo fn_lang('QCONSTRUCT'); ?></h5>
                    <!-- START WRITTER -->
                    <div class="body mb-3">
                        <label for="writter" class="fw-semibold"><?php echo fn_lang('WRITTER'); ?>
                        </label>
                        <input type="text" name="writter" id="writter"
                            class="form-control"
                            value="<?php echo $_SESSION['teacher_name'] ?>"
                            required disabled>
                    </div>
                    <!-- END WRITTER -->
                    <!-- START DATE -->
                    <div class="body mb-3">
                        <label for="crea_date" class="fw-semibold"><?php echo fn_lang('DATE'); ?>
                        </label>
                        <input type="datetime-local" name="creation_date" id="crea_date"
                            class="form-control"
                            value="<?php echo date("Y-m-d H:i:s") ?>"
                            required disabled>
                    </div>
                    <!-- END DATE -->
                    <!-- START SUBJECT -->
                    <div class="body mb-3">
                        <label for="subject" class="fw-semibold"><?php echo fn_lang('SUBJECT'); ?>
                        </label>
                        <input type="text" name="subject" id="subject"
                            class="form-control"
                            value="<?php echo $DBrow[0]['subject'] . "-" . $DBrow[0]['grade'] . "-" . $DBrow[0]['lang'] ?>"
                            required disabled>
                    </div>
                    <!-- END SUBJECT -->
                    <!-- START DEPTH -->
                    <div class="body mb-3">
                        <label for="depth" class="fw-semibold"><?php echo fn_lang('DEPTH'); ?>
                            <select name="depth" id="depth" class="depth form-select" required>
                                <option value="RECALL"><?php echo fn_lang('RECALL') ?></option>";
                                <option value="DIRECT"><?php echo fn_lang('DIRECT') ?></option>";
                                <option value="STRATEGIC THINKNG"><?php echo fn_lang('STRATEGIC THINKNG') ?></option>";
                            </select>
                        </label>
                    </div>
                    <!-- END DEPTH -->
                    <!-- START DOK -->
                    <div class="body mb-3">
                        <label for="dok" class="fw-semibold"><?php echo fn_lang('DOK'); ?>
                        </label>
                        <input type="text" name="dok" id="dok" class="dok form-control">
                    </div>
                    <!-- END DOK -->
                    <!-- START REFERENCE -->
                    <div class="body mb-3">
                        <label for="REFERENCE" class="fw-semibold"><?php echo fn_lang('REFERENCE'); ?>
                            <select id="REFERENCE" name="reference" class="ref_sel form-select" required>
                                <option value="none" selected><?php echo fn_lang('NO_REF'); ?></option>";
                                <option value="NA"><?php echo fn_lang('BRAND_NEW'); ?></option>";
                                <option value="REF"><?php echo fn_lang('REF_SELECT'); ?></option>";
                            </select>
                        </label>
                        <input type="text" name="ref_inp" id="ref_inp"
                            class="ref_inp form-control" disabled style="display: none;"
                            placeholder="<?php echo fn_lang("REF"); ?>">
                    </div>
                    <!-- END REFERENCE -->
                    <!-- START STEM -->
                    <div class="body mb-3 stem_bar">
                        <label for="textarea" class="fw-semibold"><?php echo fn_lang('STEM'); ?>
                        </label>
                        <!-- Math bar inserted here by following function -->
                        <?php echo fn_QuestionBar("stem") ?>
                    </div>
                    <!-- END STEM -->
                    <!-- START STEM Image -->
                    <div class="body mb-3 checkbar">
                        <label for="image" class="fw-semibold image_check"><?php echo fn_lang('IMAGE'); ?>
                            <input type="checkbox" name="stemImageCheck" id="image" hidden>
                            <div class="enhanced_bar img"></div>
                        </label>
                    </div>
                    <div class="body mb-3">
                        <label for="image_path" class="fw-semibold drop" style="display: none;">
                            <input type="file" accept="image" name="image_path" id="image_path" class="image_path form-control"
                                autocomplete="on" style="display: none;">
                            <div class="img">
                                <img alt="" class="imageArea" width="3px">
                                <div class="cloud">
                                    <i class="fa fa-cloud-arrow-up fa-3x" color="#0BC279"></i>
                                    <p>Drag and drop or click <strong>here</strong><br>to upload Image</p>
                                    <span>Upload any images from your local storage</span>
                                    <p class="alert alert-danger m-0 p-1 sizeMsg" hidden>Too <strong>Large</strong> File, Try to upload file smaller than <strong>2MB</strong></p>
                                </div>
                            </div>
                        </label>
                    </div>
                    <!-- END STEM Image -->
                    <!-- START SUB STEM -->
                    <div class="body mb-3 stem_bar">
                        <label for="substem" class="fw-semibold"><?php echo fn_lang('SUBSTEM'); ?>
                        </label>
                        <?php echo fn_QuestionBar("substem") ?>
                    </div>
                    <!-- END SUBSTEM -->
                    <!-- START SUBSTEM Image -->
                    <div class="body mb-3 checkbar">
                        <label for="subimage" class="fw-semibold image_check"><?php echo fn_lang('IMAGE'); ?>
                            <input type="checkbox" name="substemImageCheck" id="subimage" hidden>
                            <div class="enhanced_bar subimg"></div>
                        </label>
                    </div>
                    <div class="body mb-3">
                        <label for="subimage_path" class="fw-semibold subdrop" style="display: none;">
                            <input type="file" accept="image" name="subimage_path" id="subimage_path" class="image_path form-control"
                                autocomplete="on" style="display: none;">
                            <div class="img">
                                <img alt="" class="subimageArea">
                                <div class="subcloud cloud">
                                    <i class="fa fa-cloud-arrow-up fa-3x" color="#0BC279"></i>
                                    <p>Drag and drop or click <strong>here</strong><br>to upload Image</p>
                                    <span>Upload any images from your local storage</span>
                                    <p class="alert alert-danger m-0 p-1 sizeMsg" hidden>Too <strong>Large</strong> File, Try to upload file smaller than <strong>2MB</strong></p>
                                </div>
                            </div>
                        </label>
                    </div>
                    <!-- END SUBSTEM Image -->
                    <!-- START OPTIONS -->
                    <h3 class="Questions" ><?php echo fn_lang('QUESTIONREV') ?></h3>
                    <div class="Question coloring">
                        <?php for ($i = 1; $i < 5; $i++) {
                            $questionName = ["One", "Two", "Three", "Four"]
                        ?>

                            <!-- START OPTION ONE TWO THREE FOUR TAGS -->
                            <div class="coloringChildren">
                                <div class="body Question mb-3 option<?php echo $questionName[$i - 1] ?>">
                                    <div class="body mb-3">
                                        <!-- START TRUE ANSWER -->
                                        <div>
                                            <label for="radio<?php echo $i ?>" class="fw-semibold"><?php echo fn_lang('TRUE'); ?>
                                            </label>
                                            <input type="radio" name="radioOptTrue" id="radio<?php echo $i ?>" class="choiceRadio form-radio" value="<?php echo $i; ?>">
                                        </div>
                                        <!-- END TRUE ANSWER -->
                                        <!-- START ANSWER PHRASE -->
                                        <div>
                                            <label for="opt<?php echo $i ?>" class="fw-semibold"><?php echo fn_lang('CHOICE') . ' ' . $questionName[$i - 1]; ?>
                                            </label>
                                            <?php echo fn_QuestionBar("opt".$i);?>
                                        </div>
                                        <!-- <input type="text" name="opt<?php echo $i ?>" id="opt<?php echo $i ?>" class="option form-control "> -->
                                        <!-- END ANSWER PHRASE -->
                                        <!-- START ANSWER REASON -->
                                        <div>
                                            <label for="opt<?php echo $i ?>Exp" class="fw-semibold"><?php echo fn_lang('CAUSE'); ?>
                                            </label>
                                            <input type="text" name="opt<?php echo $i ?>Exp" id="opt<?php echo $i ?>Exp" class="form-control ">
                                        </div>
                                        <!-- END ANSWER REASON -->
                                    </div>
                                    <!-- START SUPPORT IMAGE -->
                                    <label for="opimage<?php echo $i ?>" class="fw-semibold image_check"><?php echo fn_lang('OPTION'); ?>
                                        <input type="checkbox" name="opimage<?php echo $i ?>" id="opimage<?php echo $i ?>" data-number="<?php echo $i ?>" class="optionImage" hidden>
                                        <div class="enhanced_bar op <?php echo $i ?>"></div>
                                    </label>
                                    <!-- END SUPPORT IMAGE -->
                                </div>
                                <!-- START IMAGE SECTION -->
                                <div class="body mb-3">
                                    <label for="Qopt_path<?php echo $i ?>" class="fw-semibold drop Q Area" style="display: none;" data-number="<?php echo $i ?>">
                                        <input type="file" accept="image" name="option<?php echo $questionName[$i - 1] ?>_path" id="Qopt_path<?php echo $i ?>" data-number="<?php echo $i ?>" class="Q option_path form-control"
                                            autocomplete="on" style="display: none;">
                                        <div class="Q  img">
                                            <img alt="" class="Q imageArea">
                                            <div class="Q cloud">
                                                <i class="fa fa-cloud-arrow-up fa-3x" color="#0BC279"></i>
                                                <p>Drag and drop or click <strong>here</strong><br>to upload Image</p>
                                                <span>Upload any images from your local storage</span>
                                                <p class="alert alert-danger m-0 p-1 optionsImg" hidden>Too <strong>Large</strong> File, Try to upload file smaller than <strong>2MB</strong></p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <!-- END IMAGE SECTION -->
                            </div>
                            <!-- END OPTION ONE TWO THREE FOUR TAGS -->
                        <?php
                        }
                        // END LOOPING -->
                        echo "<br>"; ?>
                    </div>
                    <!-- END OPTIONS -->
                </div>
                <!-- END The Question Body -->
                <!-- START VARIABLES SECTION -->
                <div class="d-grid gap-3 col-12 offset-0">
                    <input type="text" name="writter" value="<?php echo $_SESSION['teacher_name'] ?>" hidden>
                    <input type="datetime-local" name="creation_date" value="<?php echo date("Y-m-d H:i:s") ?>" hidden>
                    <input type="text" name="type" value="<?php echo $DBrow[0]['type'] ?>" hidden>
                    <input type="text" name="stage" value="<?php echo $DBrow[0]['stage'] ?>" hidden>
                    <input type="text" name="subject" value="<?php echo $DBrow[0]['subject'] ?>" hidden>
                    <input type="text" name="grade" value="<?php echo $DBrow[0]['grade'] ?>" hidden>
                    <input type="text" name="lang" value="<?php echo $DBrow[0]['lang'] ?>" hidden>
                </div>
                <!-- END VARIABLES SECTION -->
                <div class="d-flex gap-3 col-12 offset-0">
                    <!-- START SUMBIT BTN -->
                    <div class="d-grid col-3">
                        <input type="submit" id="submitBtn" name="saveEdit" value="<?php echo fn_lang('SAVE') ?>" class="btn success  btn-md">
                    </div>
                    <!-- END SUMBIT BTN -->
                    <!-- START SEND BTN -->
                    <div class="d-grid col-3">
                        <input type="submit" id="submitBtn" name="send" value="<?php echo fn_lang('SEND') ?>" class="btn success_sec  btn-md">
                    </div>
                    <!-- END SEND BTN -->
                    <!-- START CANCEL BTN -->
                    <div class="d-grid col-3">
                        <a href="?do=Dash&Langset=<?php echo ($langset) ?>" class="btn  cancel  btn-md">Cancel</a>
                    </div>
                    <!-- END CANCEL BTN -->
                </div>
            </form>
        </div>
    </div>
<?php
                break;
            case 'Save':
?>
    <script>
        document.getElementById("header").innerText = "<?php echo fn_lang('SAVE'); ?>";
    </script>
    <!-- START Container Contents -->
    <div class="container">
        <?php
                //if revisor btn is pushed make question case 1 else (save btn) is pushed and make question case 0
                @$sendbutton = $_POST['send'];
                $question_case = 0;
                if ($sendbutton) {
                    # code...
                    $question_case = 1;
                }
                $errMsg = array();
                $imageParent = [
                    "image_path" => "Stem Image",
                    "subimage_path" => "Sub Stem Image",
                    "optionOne_path" => "1st Option Image",
                    "optionTwo_path" => "2nd Option Image",
                    "optionThree_path" => "3rd Option Image",
                    "optionFour_path" => "4th Option Image",
                ];
                $errCount = 0;

                $itemallowedtypes = array("jpeg", "jpg", "png", "gif", "jfif", "ico");

                if ($_SERVER['REQUEST_METHOD'] === "POST") {
                    foreach ($_FILES as $Arrname => $file) {
                        # code...
                        $temp = explode(".", $file['name']);
                        $imageExtension = strtolower(end($temp));

                        if (!empty($file['name'])) {
                            if ($file['size'] > 2097152) {
                                $errMsg[] = "<div class='alert alert-danger'>Size of the <strong>$imageParent[$Arrname]</strong> is <strong>Larger than 2MB.</strong> Use lower size image</div>";
                                $errCount++;
                            }
                            if (!(in_array($imageExtension, $itemallowedtypes))) {
                                $errMsg[] = "<div class='alert alert-danger'><strong>$imageParent[$Arrname] Not Allowed type</strong> for Uploaded File Image. Try using one of these types <strong>(jpeg, jpg, png, jfif, gif or ico)</strong></div>";
                                $errCount++;
                            }
                        }
                    }
                    foreach ($errMsg as $err) {
                        echo $err;
                    }
                    // START SAVING IF AFTER VALIDATION NO ERRORS
                    if ($errCount === 0) {
                        if (isset($_FILES)) {
                            rnd:
                            $random = rand(1, 100000000);
                            $QuestionDir = $imgDir . "Questions/";
                            $fileErr = 0;
                            $correctAnswer = "";

                            //Check the existance of file
                            foreach ($_FILES as $eachfile => $fileValues) {
                                //exclude the first section from name
                                $temp = explode("_", $eachfile);
                                $tempext = explode(".", $fileValues['name']);
                                $imageExtension = strtolower(end($tempext));
                                $name = $random . "_" . strtolower($temp[0]) . "." . $imageExtension;
                                //if file random number already exists make errorhandler
                                @$ftp = fopen($QuestionDir . $name, 'r');
                                if ($ftp) {
                                    $fileErr++;
                                }
                            }
                            //try using another random number
                            if ($fileErr) {
                                goto rnd;
                            } else if (!$fileErr) {
                                foreach ($_FILES as $eachfile => $fileValues) {
                                    # code...
                                    if (!empty($fileValues['name'])) {
                                        //exclude the first section from name
                                        $temp = explode("_", $eachfile);
                                        $tempext = explode(".", $fileValues['name']);
                                        $imageExtension = strtolower(end($tempext));
                                        $name = $random . "_" . strtolower($temp[0]) . "." . $imageExtension;
                                        move_uploaded_file($fileValues['tmp_name'], $QuestionDir . $name);
                                    }
                                }
                            }
                            //Insert into Question the new Question
                            //***************************** */
                            //***************************** */
                            //first adjust choices
                            //***************************** */
                            //***************************** */
                            //START STEM IMAGE
                            //if image is found take image else put ""
                            @$tmpChecker = $_POST['stemImageCheck'];
                            if ($tmpChecker) {
                                $stemApproval = 1;
                                $tmp = explode(".", $_FILES['image_path']['name']);
                                $stemImageLink = $random . "_image." . end($tmp);
                            } else {
                                $stemApproval = 0;
                                $stemImageLink = "";
                            };
                            //END STEM IMAGE
                            //START SUBSTEM Phrase
                            //if phrase is found take the phrase else put ""
                            @$tmpChecker = $_POST['substem'];
                            if ($tmpChecker) {
                                $substemphrase = $_POST['substem'];
                            } else {
                                $substemphrase = "";
                            };
                            //END SUBSTEM Phrase
                            //START SUBSTEM IMAGE
                            //if image is found take image else put ""
                            @$tmpChecker = $_POST['substemImageCheck'];
                            if ($tmpChecker) {
                                $substemApproval = 1;
                                $tmp = explode(".", $_FILES['subimage_path']['name']);
                                $substemImageLink = $random . "_subimage." . end($tmp);
                            } else {
                                $substemApproval = 0;
                                $substemImageLink = "";
                            };
                            //END SUBSTEM IMAGE
                            //START Options IMAGES checker
                            if (isset($_POST['radioOptTrue'])) {
                                $correctAnswer = $_POST['radioOptTrue'];
                            }
                            //if image is found take image else put ""
                            $questionName = ["One", "Two", "Three", "Four"];
                            for ($i = 0; $i < 4; $i++) {
                                # code...
                                $Qphrase[$i] = $_POST["opt" . $i + 1];
                                @$tmpChecker = $_FILES['option' . $questionName[$i] . '_path'];
                                if ($tmpChecker["name"]) {
                                    $QApproval[$i] = 1;
                                    $tmp = explode(".", $_FILES['option' . $questionName[$i] . '_path']['name']);
                                    $QImageLink[$i] = $random . "_option" . strtolower($questionName[$i]) . "." . end($tmp);
                                } else {
                                    $QApproval[$i] = 0;
                                    $QImageLink[$i] = "";
                                };
                            }
                            //END Options IMAGES
                            //START Prepare Revisors
                            $stmt2 = $con->prepare("SELECT *
                                    FROM
                                        teacher_items
                                    INNER JOIN
                                        teachers
                                    ON
                                        teachers.teacher_id=teacher_items.teacher_id
                                    WHERE
                                        teacher_items.revision=?
                                    AND
                                        teachers.regstatus=?
                                    AND
                                        teacher_items.type=?
                                    AND
                                        teacher_items.stage=?
                                    AND
                                        teacher_items.subject=?
                                    AND
                                        teacher_items.grade=?
                                    AND
                                        teacher_items.lang=?
                                    AND
                                        teacher_items.free_for_revision=?
                                    AND
                                        teachers.teacher_id <> ?
                                        ");
                            $stmt2->execute(array("REV", 1, $_POST['type'], $_POST['stage'], $_POST['subject'], $_POST['grade'], $_POST['lang'], 1, $_SESSION['teacher_id']));
                            $results = $stmt2->fetchAll();
                            $rev = [];
                            $rev_id = [];
                            $rev_itemid = [];
                            $revisors;
                            $errcounter = 0;
                            switch (count($results)) {
                                case '0':
                                    //reorder revisors again
                                    $reorder = $con->prepare("SELECT *
                                    FROM
                                        teacher_items
                                        INNER JOIN
                                            teachers
                                        ON
                                            teachers.teacher_id=teacher_items.teacher_id
                                        WHERE
                                            teacher_items.revision=?
                                        AND
                                            teachers.regstatus=?
                                        AND
                                            teacher_items.type=?
                                        AND
                                            teacher_items.stage=?
                                        AND
                                            teacher_items.subject=?
                                        AND
                                            teacher_items.grade=?
                                        AND
                                            teacher_items.lang=?
                                        AND
                                            teachers.teacher_id <> ?
                                        ");
                                    $reorder->execute(array("REV", 1, $_POST['type'], $_POST['stage'], $_POST['subject'], $_POST['grade'], $_POST['lang'], $_SESSION['teacher_id']));
                                    $results = $reorder->fetchAll();


                                    // Get data
                                    $revisors = 2;
                                    //check that revisors are not the same or one of them is not the creator
                                    $rev[0] = rand(0, count($results) - 1);
                                    $rev_id[0] = $results[$rev[0]]['teacher_id'];
                                    $rev_itemid[0] = $results[$rev[0]]['item_id'];
                                    rept0:
                                    $rev[1] = rand(0, count($results) - 1);
                                    if ($errcounter > 10) {
                                        $errcounter = 0;
                                        fn_redirect("Can't send now, no avaliable Revisors, Try again later", "info", 6, "back");
                                    }
                                    if ($rev[1] === $rev[0]) {
                                        ++$errcounter;
                                        goto rept0;
                                    }
                                    $rev_id[1] = $results[$rev[1]]['teacher_id'];
                                    $rev_itemid[1] = $results[$rev[1]]['item_id'];
                                    break;
                                case '1':
                                    //check that revisors are not the same or one of them is not the creator

                                    $reorder = $con->prepare("SELECT *
                                    FROM
                                        teacher_items
                                        INNER JOIN
                                            teachers
                                        ON
                                            teachers.teacher_id=teacher_items.teacher_id
                                        WHERE
                                            teacher_items.revision=?
                                        AND
                                            teachers.regstatus=?
                                        AND
                                            teacher_items.type=?
                                        AND
                                            teacher_items.stage=?
                                        AND
                                            teacher_items.subject=?
                                        AND
                                            teacher_items.grade=?
                                        AND
                                            teacher_items.lang=?
                                        AND
                                            teachers.teacher_id <> ?
                                        ");
                                    $reorder->execute(array("REV", 1, $_POST['type'], $_POST['stage'], $_POST['subject'], $_POST['grade'], $_POST['lang'], $_SESSION['teacher_id']));
                                    $results = $reorder->fetchAll();

                                    // Get data
                                    $revisors = 2;
                                    //check that revisors are not the same or one of them is not the creator
                                    $rev[0] = rand(0, count($results) - 1);
                                    $rev_id[0] = $results[$rev[0]]['teacher_id'];
                                    $rev_itemid[0] = $results[$rev[0]]['item_id'];
                                    rept1:
                                    $rev[1] = rand(0, count($results) - 1);
                                    if ($errcounter > 10) {
                                        $errcounter = 0;
                                        fn_redirect("Can't send now, no avaliable Revisors, Try again later", "info", 6, "back");
                                    }
                                    if ($rev[1] === $rev[0]) {
                                        ++$errcounter;
                                        goto rept1;
                                    }
                                    $rev_id[1] = $results[$rev[1]]['teacher_id'];
                                    $rev_itemid[1] = $results[$rev[1]]['item_id'];
                                    break;
                                default:
                                    // pick up 2 random revisors
                                    $revisors = 2;
                                    // rept2:
                                    $rev[0] = rand(0, count($results) - 1);
                                    $rev_id[0] = $results[$rev[0]]['teacher_id'];
                                    $rev_itemid[0] = $results[$rev[0]]['item_id'];
                                    rept:
                                    $rev[1] = rand(0, count($results) - 1);
                                    if ($errcounter > 10) {
                                        $errcounter = 0;
                                        fn_redirect("Can't send now, no avaliable Revisors, Try again later", "info", 6, "back");
                                    }
                                    if ($rev[1] === $rev[0]) {
                                        ++$errcounter;
                                        goto rept;
                                    }
                                    $rev_id[1] = $results[$rev[1]]['teacher_id'];
                                    $rev_itemid[1] = $results[$rev[1]]['item_id'];
                                    break;
                            }

                            //END Prepare Revisors
                            //get DOK if found

                            $dok = $_POST['dok'] ? $_POST['dok'] : "";
                            $ref = $_POST['reference'] ? $_POST['reference'] : "";
                            @$ref_txt = $_POST['ref_inp'] ? $_POST['ref_inp'] : "";

                            //declare variables
                            $explained_1 = $_POST['opt1Exp'] ? $_POST['opt1Exp'] : "";
                            $explained_2 = $_POST['opt2Exp'] ? $_POST['opt2Exp'] : "";
                            $explained_3 = $_POST['opt3Exp'] ? $_POST['opt3Exp'] : "";
                            $explained_4 = $_POST['opt4Exp'] ? $_POST['opt4Exp'] : "";

                            //Fetch profile number from teacher_items
                            $teacherProfile = fn_getRecordsV2("*", "teacher_items", 
                            "WHERE teacher_id=? AND account_status=?",
                            array($_SESSION['teacher_id'], 1));
                            //saving data in DB FILE
                            $stmt = fn_DB_insert(
                                "questions_create",
                                "type,stage,subject,grade,
                        lang,creation_date,Question_Body,supported_image,
                        image_link,sub_stem,substem_sup_img,substem_image_link,
                        answer1,ans1_sup_img,ans1_image_link,answer2,
                        ans2_sup_img,ans2_image_link,answer3,ans3_sup_img,
                        ans3_image_link,answer4,ans4_sup_img,ans4_image_link,
                        correct_answer,teacher_id,revisor_one,revisor_two,
                        outcome_id,depth,dok,number_of_revisors,question_case,reference,ref_txt,
                        ans1_explain,ans2_explain,ans3_explain,ans4_explain,teacher_item_id",
                                ":ztype,:zstage,:zsubject,:zgrade,
                    :zlang,:zcreation_date,:zQuestion_Body,:zstemimageApproval,
                    :zstemimage_link,:zsub_stem,:zsubstemimgApproval,:zsubstem_image_link,
                    :zanswer1,:zans1_sup_img,:zans1_image_link,:zanswer2,
                    :zans2_sup_img,:zans2_image_link,:zanswer3,:zans3_sup_img,
                    :zans3_image_link,:zanswer4,:zans4_sup_img,:zans4_image_link,
                    :zcorrect_answer,:zteacher_id,:zrevisor_one,:zrevisor_two,
                    :zoutcome,:zdepth,:zdok,:znumber_of_revisors,:zquestion_case,:zreference,:zref_txt,
                    :zans1_explain,:zans2_explain,:zans3_explain,:zans4_explain,:zteacher_item_id",
                                array(
                                    ':ztype' => $_POST['type'],
                                    ':zstage' => $_POST['stage'],
                                    ':zsubject' => $_POST['subject'],
                                    ':zgrade' => $_POST['grade'],
                                    ':zlang' => $_POST['lang'],
                                    ':zcreation_date' => $_POST['creation_date'],
                                    ':zQuestion_Body' => $_POST['stem'] ? $_POST['stem'] : "",
                                    ':zstemimageApproval' => $stemApproval,
                                    ':zstemimage_link' => $stemImageLink,
                                    'zsub_stem' => $substemphrase,
                                    ':zsubstemimgApproval' => $substemApproval,
                                    ':zsubstem_image_link' => $substemImageLink,
                                    ':zanswer1' => $Qphrase[0],
                                    ':zans1_sup_img' => $QApproval[0],
                                    ':zans1_image_link' => $QImageLink[0],
                                    ':zanswer2' => $Qphrase[1],
                                    ':zans2_sup_img' => $QApproval[1],
                                    ':zans2_image_link' => $QImageLink[1],
                                    ':zanswer3' => $Qphrase[2],
                                    ':zans3_sup_img' => $QApproval[2],
                                    ':zans3_image_link' => $QImageLink[2],
                                    ':zanswer4' => $Qphrase[3],
                                    ':zans4_sup_img' => $QApproval[3],
                                    ':zans4_image_link' => $QImageLink[3],
                                    ':zcorrect_answer' => $correctAnswer,
                                    ':zteacher_id' => $_SESSION['teacher_id'],
                                    ':zrevisor_one' => $rev_id[0],
                                    ':zrevisor_two' => $rev_id[1],
                                    ':zoutcome' => $_POST['outcome'],
                                    ':zdepth' => $_POST['depth'],
                                    ':zdok' => $dok,
                                    ':znumber_of_revisors' => $revisors,
                                    ':zquestion_case' => $question_case,
                                    ':zreference' => $ref,
                                    ':zref_txt' => $ref_txt,
                                    ":zans1_explain" => $explained_1,
                                    ":zans2_explain" => $explained_2,
                                    ":zans3_explain" => $explained_3,
                                    ":zans4_explain" => $explained_4,
                                    "zteacher_item_id"=> $teacherProfile[0]['item_id']
                                )
                            );
                            //update revisors Case to busy
                            for ($i = 0; $i < $revisors; $i++) {
                                $changeRevisorCase = $con->prepare("UPDATE teacher_items
                                                                    SET
                                                                        free_for_revision = ?
                                                                    WHERE
                                                                        item_id = ?
                                                                ");
                                $changeRevisorCase->execute(array(false, $rev_itemid[$i]));
                            }
        ?>
                    <script>
                        if (imgLink) URL.revokeObjectURL(imgLink);
                        if (subimgLink) URL.revokeObjectURL(subimgLink);
                        if (QimgLink) URL.revokeObjectURL(QimgLink);
                    </script>
        <?php
                              
                                //prepare data to send email to creator and revisors
                                $DBCALL = $con->prepare("SELECT *
                                    FROM
                                        teachers                                       
                                    WHERE
                                        teacher_id=?
                                    ");
                                $DBCALL->execute(array($_SESSION['teacher_id']));
                                $emailCreator=$DBCALL->fetch();
                                $DBCALL = $con->prepare("SELECT COUNT(*)
                                    FROM
                                        questions_create                                       
                                    WHERE
                                        teacher_id=?
                                    ");
                                $DBCALL->execute(array($_SESSION['teacher_id']));
                                $Qno=$DBCALL->fetchColumn();
                                //prepare data to send email to revisor1
                                $DBCALL = $con->prepare("SELECT *
                                    FROM
                                        teacher_items
                                    INNER JOIN
                                        teachers
                                    ON
                                        teachers.teacher_id=teacher_items.teacher_id
                                    WHERE
                                        teacher_items.item_id=?
                                    ");
                                $DBCALL->execute(array($rev_itemid[0]));
                                $emailRevisor1=$DBCALL->fetch();
                                //prepare data to send email to revisor2
                                $DBCALL = $con->prepare("SELECT *
                                    FROM
                                        teacher_items
                                    INNER JOIN
                                        teachers
                                    ON
                                        teachers.teacher_id=teacher_items.teacher_id
                                    WHERE
                                        teacher_items.item_id=?
                                    ");
                                $DBCALL->execute(array($rev_itemid[1]));
                                $emailRevisor2=$DBCALL->fetch();
                                //sending email to creator
                                $body = "<h1>Congratulation</h1> <h2>".$emailCreator['ename']."</h2>";
                                $msg="Well done You have been created (" . $Qno . ") Questions till now.<h2>Don't replay</h2><p>Well done</p>" . $web;
                                //call email function
                                sendEmail($emailCreator['email'],$emailCreator['ename'],"Question Created",$body.$msg,$msg);
                                //check pushed btn send revisor (send) save question (else)
                                if (isset($_POST['send'])) {
                                $DBREVISION = $con->prepare("SELECT * FROM questions_create ORDER BY question_id DESC LIMIT 1;");
                                $DBREVISION->execute();
                                $current_record = $DBREVISION->fetch();
                                //sending emails to revisor number (1)
                                $body="<h1>Congratulation</h1> <h2>".$emailRevisor1['ename']."</h2>";
                                $msg="You Have been Picked up to revise A Question in your profile for(" . $_POST['subject']."/".$_POST['grade']."/".$_POST['lang'] . ") Harry Up.<h2>Don't replay</h2>" . $web;
                                sendEmail($emailRevisor1['email'],$emailRevisor1['ename'],"Question to be revised",$body.$msg,$msg);
                                //sending emails to revisor number (2)
                                $body="<h1>Congratulation</h1> <h2>".$emailRevisor2['ename']."</h2>";
                                sendEmail($emailRevisor2['email'],$emailRevisor2['ename'],"Question to be revised",$body.$msg,$msg);
                                //finish mission
                                fn_redirect("Question is Recorded, and sending to revisors...", "success", 6, "?do=RevisionSend&Langset=$langset&record=$current_record[question_id]");
                            } else {
                                
                                fn_redirect("Question is Recorded", "success", 4, "?do=Dash&Langset=$langset");
                            }
                        }
                    } else {
                        fn_redirect("Can't Complete Action Correctly", "danger", 3, "back");
                    }
                } else {
                    fn_redirect("WRONG ACCESS TO PAGE", "danger", 3, "?do=Dash&Langset=$langset");
                }
        ?>
    </div>
<?php
                break;
            case 'RevisionJump':
                ?>
                <div class="container">
                    <?php
                    //prepare data to send email to revisors
                    $DBCALL = $con->prepare("SELECT *
                    FROM
                        questions_create
                    INNER JOIN
                        teachers
                    ON
                        questions_create.teacher_id=teachers.teacher_id
                    WHERE
                        questions_create.question_id=?
                    ORDER BY
                        creation_date ASC
                    ");
                    $DBCALL->execute(array($_GET['DBrecord']));
                    $data=$DBCALL->fetch();
                    $rev1=$data['revisor_one'];
                    $rev2=$data['revisor_two'];
                    //prepare data to send email to revisor1
                    $DBCALL = $con->prepare("SELECT *
                        FROM
                            teachers
                        INNER JOIN
                            teacher_items
                        ON
                            teachers.teacher_id=teacher_items.teacher_id
                        WHERE
                            teachers.teacher_id=?
                        ");
                    $DBCALL->execute(array($rev1));
                    $emailRevisor1=$DBCALL->fetch();
                    //prepare data to send email to revisor2
                    $DBCALL = $con->prepare("SELECT *
                        FROM
                            teachers
                        INNER JOIN
                            teacher_items
                        ON
                            teachers.teacher_id=teacher_items.teacher_id
                        WHERE
                            teachers.teacher_id=?
                        ");
                    $DBCALL->execute(array($rev2));
                    $emailRevisor2=$DBCALL->fetch();
                    //sending emails to revisor number (1)
                    $body="<h1>Congratulation</h1> <h2>".$emailRevisor1['ename']."</h2>";
                    $msg="You Have been Picked up to revise A Question in your profile for(" . $data['subject']."/".$data['grade']."/".$data['lang'] . ") Harry Up.<h2>Don't replay</h2>" . $web;
                    sendEmail($emailRevisor1['email'],$emailRevisor1['ename'],"Question to be revised",$body.$msg,$msg);
                    //sending emails to revisor number (2)
                    $body="<h1>Congratulation</h1> <h2>".$emailRevisor2['ename']."</h2>";
                    sendEmail($emailRevisor2['email'],$emailRevisor2['ename'],"Question to be revised",$body.$msg,$msg);
                    //finish mission

                    fn_redirect("Question is Recorded, and sending to revisors...", "success", 6, "?do=RevisionSend&Langset=".$_GET['Langset']."&record=".$_GET['DBrecord']);
                    ?>
                </div>
                <?php
                break;
            case 'Update':
                //prepare data from teacher_items
                $DBrow = fn_getRecordsV2("*", "teacher_items", "WHERE teacher_id=? AND account_status=?", array($_SESSION['teacher_id'], 1));
                $DBEdit = $con->prepare("SELECT *
                        FROM
                            questions_create
                        WHERE
                            questions_create.question_id=?
                        ORDER BY
                            creation_date DESC
                        ");
                $DBEdit->execute(array($_GET['record']));
                $DBEditrow = $DBEdit->fetch();
?>
    <script>
        document.getElementById("header").innerText = "<?php echo fn_lang('SAVE'); ?>";
    </script>

    <!-- START Container Contents -->
    <div class="container">
        <?php
                //some defaults variables for checking valiables
                $errMsg = array();
                $imageParent = [
                    "image_path" => "Stem Image",
                    "subimage_path" => "Sub Stem Image",
                    "optionOne_path" => "1st Option Image",
                    "optionTwo_path" => "2nd Option Image",
                    "optionThree_path" => "3rd Option Image",
                    "optionFour_path" => "4th Option Image",
                ];
                $DBImagelinkField = [

                    "optionOne" => "ans1_image_link",
                    "optionTwo" => "ans2_image_link",
                    "optionThree" => "ans3_image_link",
                    "optionFour" => "ans4_image_link",
                ];
                $itemallowedtypes = ["jpeg", "jpg", "png", "gif", "jfif", "ico"];
                $errCount = 0;
                
                //check that the call is from inside the program
                if ($_SERVER['REQUEST_METHOD'] === "POST") {
                    //check $_FILES for new images - injection
                    foreach ($_FILES as $imagesNames => $file) {
                        # code...
                        $temp = explode(".", $file['name']);
                        $imageExtension = strtolower(end($temp));
                        if (!empty($file['name'])) {
                            if ($file['size'] > 2097152) {
                                $errMsg[] = "<div class'alert alert-danger'>Size of the <strong>$imageParent[$Arrname]</strong> is <strong>Larger than 2MB.</strong> Use lower size image</div>";
                                $errCount++;
                            }
                            if (!(in_array($imageExtension, $itemallowedtypes))) {
                                $errMsg[] = "<div class='alert alert-danger'><strong>$imageParent[$Arrname] Not Allowed type</strong> for Uploaded File Image. Try using one of these types <strong>(jpeg, jpg, png, jfif, gif or ico)</strong></div>";
                                $errCount++;
                            }
                        }
                    }
                    foreach ($errMsg as $err) {
                        echo $err;
                    }
                    // START SAVING IF AFTER VALIDATION NO ERRORS
                    if ($errCount === 0) {
                        if (isset($_FILES)) {
                            //get old names;
                            $QuestionDir = $imgDir . "Questions/";
                            $fileErr = 0;
                            $correctAnswer = "";
                            $operator = "";
                            //get or generate random operator
                            if (!empty($DBEditrow['image_link'])) {
                                $getDBImageName = explode("_", $DBEditrow['image_link']);
                                $operator = $getDBImageName[0];
                            } else if (!empty($DBEditrow['substem_image_link'])) {
                                $getDBImageName = explode("_", $DBEditrow['substem_image_link']);
                                $operator = $getDBImageName[0];
                            } else if (!empty($DBEditrow['ans1_image_link'])) {
                                $getDBImageName = explode("_", $DBEditrow['ans1_image_link']);
                                $operator = $getDBImageName[0];
                            } else if (!empty($DBEditrow['ans2_image_link'])) {
                                $getDBImageName = explode("_", $DBEditrow['ans2_image_link']);
                                $operator = $getDBImageName[0];
                            } else if (!empty($DBEditrow['ans3_image_link'])) {
                                $getDBImageName = explode("_", $DBEditrow['ans3_image_link']);
                                $operator = $getDBImageName[0];
                            } else if (!empty($DBEditrow['ans4_image_link'])) {
                                $getDBImageName = explode("_", $DBEditrow['ans4_image_link']);
                                $operator = $getDBImageName[0];
                            } else //No operator before - brand new images
                            {
                                rndUpdate:
                                $operator = rand(1, 100000000);
                                //if file random number already exists make errorhandler
                                @$ftp = fopen($QuestionDir . $name, 'r');
                                if ($ftp) {
                                    $fileErr++;
                                }
                                //try using another random number
                                if ($fileErr) {
                                    goto rndUpdate;
                                }
                            }
                            //Check the existance of file
                            foreach ($_FILES as $eachfile => $fileValues) {
                                //exclude the first section from name
                                $temp = explode("_", $eachfile);
                                $tempext = explode(".", $fileValues['name']);
                                $imageExtension = strtolower(end($tempext));
                                //random number before images
                                if (!($fileValues['size'] == 0)) { 
                                    //Get old Image name
                                    $ServerNewFileName = $temp[0];
                                    switch ($temp[0]) {
                                        case 'image':
                                            $DBField = $temp[0] . "_link";
                                            break;
                                        case 'subimage':
                                            $DBField = "substem_image_link";
                                            break;
                                        default:
                                            $DBField = $DBImagelinkField[$temp[0]];
                                            break;
                                    }
                                    //record the new file with old name
                                    $name = $operator . "_" . strtolower($temp[0]) . "." . $imageExtension;
                                    $setNewName = $con->prepare("UPDATE questions_create SET $DBField=? WHERE question_id=?");
                                    $setNewName->execute(array($name, $_GET['record']));
                                    $oldName = $DBEditrow[$DBField];
                                    if (file_exists($QuestionDir.$oldName) && $oldName!="") {
                                        unlink($QuestionDir.$oldName); //delete old file
                                    }
                                    move_uploaded_file($fileValues['tmp_name'], $QuestionDir . $name);
                                }
                            }
                            //get variables from edit page;
                            $userInfo = [
                                ':zoutcome' => isset($_POST['outcome'])?$_POST['outcome'] : $DBEditrow['outcome_id'],
                                ':zeditdate' => date("Y-m-d H:i:s"),
                                ':zdepth' => isset($_POST['depth'])?$_POST['depth'] : $DBEditrow['depth'],
                                ':zdok' => isset($_POST['dok'])?$_POST['dok'] : $DBEditrow['dok'],
                                ':zreference' => isset($_POST['reference'])?$_POST['reference'] : $DBEditrow['reference'],
                                ':zref_txt' => isset($_POST['ref_inp'])?$_POST['ref_inp'] : $DBEditrow['ref_txt'],
                                ':zstemImageCheck' => isset($_POST['stemImageCheck'])? 1:0,
                                ':zstemQ' => isset($_POST['stem'])?$_POST['stem'] : $DBEditrow['Question_Body'],
                                ':zsubstemImageCheck' => isset($_POST['substemImageCheck'])? 1:0,
                                ':zsubstemQ' => isset($_POST['substem'])?$_POST['substem'] : $DBEditrow['sub_stem'],
                                ':zradioOptTrue' => isset($_POST['radioOptTrue'])?$_POST['radioOptTrue'] : $DBEditrow['correct_answer'],
                                ':zopt1' => isset($_POST['opt1'])?$_POST['opt1'] : $DBEditrow['answer1'],
                                ':zopimage1' => isset($_POST['opimage1'])? 1:0,
                                ':zopimageExp_1' => isset($_POST['opt1Exp'])?$_POST['opt1Exp'] : $DBEditrow['ans1_explain'],
                                ':zopt2' => isset($_POST['opt2'])?$_POST['opt2'] : $DBEditrow['answer2'],
                                ':zopimage2' => isset($_POST['opimage2'])? 1:0,
                                ':zopimageExp_2' => isset($_POST['opt2Exp'])?$_POST['opt2Exp'] : $DBEditrow['ans2_explain'],
                                ':zopt3' => isset($_POST['opt3'])?$_POST['opt3'] : $DBEditrow['answer3'],
                                ':zopimage3' => isset($_POST['opimage3'])? 1:0,
                                ':zopimageExp_3' => isset($_POST['opt3Exp'])?$_POST['opt3Exp'] : $DBEditrow['ans3_explain'],
                                ':zopt4' => isset($_POST['opt4'])?$_POST['opt4'] : $DBEditrow['answer4'],
                                ':zopimage4' => isset($_POST['opimage4'])? 1:0,
                                ':zopimageExp_4' => isset($_POST['opt4Exp'])?$_POST['opt4Exp'] : $DBEditrow['ans4_explain'],
                                ':zrecord'=> $_GET['record']
                            ];
                            $upDateRec=$con->prepare("UPDATE questions_create SET 
                            outcome_id=:zoutcome,edit_date=:zeditdate,depth=:zdepth,dok=:zdok,
                            reference=:zreference,ref_txt=:zref_txt,supported_image=:zstemImageCheck,
                            substem_sup_img=:zsubstemImageCheck,
                            Question_Body=:zstemQ,sub_stem=:zsubstemQ,
                            correct_answer=:zradioOptTrue,
                            answer1=:zopt1,ans1_sup_img=:zopimage1,ans1_explain=:zopimageExp_1,
                            answer2=:zopt2,ans2_sup_img=:zopimage2,ans2_explain=:zopimageExp_2,
                            answer3=:zopt3,ans3_sup_img=:zopimage3,ans3_explain=:zopimageExp_3,
                            answer4=:zopt4,ans4_sup_img=:zopimage4,ans4_explain=:zopimageExp_4
                            WHERE question_id=:zrecord");
                            $upDateRec->execute($userInfo);
                            if(isset($_GET['returnRevisor'])){
                                $temp= $con->prepare("SELECT * FROM question_review WHERE id=?");
                                $temp->execute(array($_GET['review_id']));
                                $origin=$temp->fetch();//get from this record  creator time review
                                if ($origin['creator_time']==NULL){
                                    list($H,$M,$S)=[0,0,0];
                                }else{
                                    list($H,$M,$S)=explode(":",$origin['creator_time']);
                                }
                                $orginToTime=intval($H)*3600 + intval($M)*60 + intval($S);
                                // get the starting time of creator review in seconds
                                $now=new Datetime($_GET['returnRevisor']);
                                $revTime=new Datetime();
                                $diff=$revTime->diff($now);
                                $toTime=$diff->d*24*60*60 + $diff->h*60*60 + $diff->i*60 + $diff->s;
                                $finalTime = $toTime + $orginToTime;
                                
                                $DBO= $con->prepare("UPDATE question_review SET 
                                review_status=?,creator_time=?
                                WHERE question_review.id=?");
                                $DBO->execute(array(3,gmdate("H:i:s",$finalTime), $_GET['review_id']));// return to re revision
                                //prepare for Emails
                                $DPO=$con->prepare("SELECT * FROM question_review WHERE id = ?");
                                $DPO->execute(array($_GET['review_id']));
                                $review=$DPO->fetch();
                                $DPO=$con->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
                                $DPO->execute(array($review['revisor_id']));
                                $emailRevisor=$DPO->fetch();
                                $DPO=$con->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
                                $DPO->execute(array($_SESSION['teacher_id']));
                                $emailCreator=$DPO->fetch();
                                //Report message and email to creator
                                //sending email to creator
                                $body = "<h1>Be Alerted</h1> <h2>".$emailRevisor['ename']."</h2>";
                                $msg=$emailCreator['ename']." found fixes his question and return it to you to be re revised.<h2>Don't replay</h2><p>Well done</p>" . $web;            
                                sendEmail($emailRevisor['email'],$emailRevisor['ename'],"Question Have returned",$body.$msg,$msg);
                                //sending message to creator
                                fn_redirect("Record Updated, And returned to revisor", "success", 3, "?do=Dash&Langset=$langset");
                            }
                            fn_redirect("Record Updated", "success", 3, "?do=Newquest&Langset=$langset");
                        }

                    } else {
                        fn_redirect("WRONG ACCESS TO PAGE", "danger", 3, "?do=Newquest&Langset=$langset");
                    }
                }
        ?>
    </div>
<?php
                break;
            case 'Edit':
?>
    <!-- Page Heading -->
    <script>
        document.getElementById("header").innerText = "<?php echo fn_lang('EDITQ'); ?>";
    </script>
    <?php
                //prepare data from teacher_items
                $DBrow = fn_getRecordsV2("*", "teacher_items", "WHERE teacher_id=? AND account_status=?", array($_SESSION['teacher_id'], 1));
                $DBEdit = $con->prepare("SELECT *
            FROM
                questions_create
            WHERE
                questions_create.question_id=?
            ORDER BY
                creation_date DESC
            ");
                $DBEdit->execute(array($_GET['record']));
                $DBEditrow = $DBEdit->fetch();

    ?>
    <!-- START Container Contents -->
    <div class="container content" dir="<?php echo ($langset == "Eng") ? "ltr" : "rtl"; ?>">
        <div class="question contents">
            <!-- START Edit Question Section -->
            <form action="?do=Update&Langset=<?php echo ($langset) ?>&record=<?php echo $DBEditrow['question_id'] ?>" method="post" enctype="multipart/form-data">
                <?php
                $outcomes = fn_getOutComeV2($DBrow[0]['type'], $DBrow[0]['stage'], $DBrow[0]['subject'], $DBrow[0]['grade'], $DBrow[0]['lang']);
                ?>
                <!-- The whole subjects are put here from function.php -->
                <!-- START The outcomes -->
                <div>
                    <label for="outcome"><?php echo fn_lang('OUTCOME'); ?>
                        <select name="outcome" id="outcome" class="outcome form-select" required>
                            <?php
                            for ($i = 0; $i < count($outcomes); $i++) {
                                echo "<option value=" . $outcomes[$i]['outcome_id'] . ">" . "Unit" . $outcomes[$i]['unit'] . " - Ch" . $outcomes[$i]['chapter'] . " - " . $outcomes[$i]['item'] . " - " . substr($outcomes[$i]["content"], 0, 120) . "</option>";
                            }
                            // 
                            ?>
                        </select>
                    </label>
                </div>
                <script>
                    document.getElementById("outcome").value = <?php echo $DBEditrow['outcome_id']; ?>; // Change to the option with the saved value
                </script>
                <!-- END The outcomes -->
                <!-- START The Question Body -->
                <div>
                    <h5 class="w-100 p-1 QC"><?php echo fn_lang('QCONSTRUCT'); ?></h5>
                    <!-- START WRITTER -->
                    <div class="body mb-3">
                        <label for="writter" class="fw-semibold"><?php echo fn_lang('WRITTER'); ?>
                        </label>
                        <input type="text" name="writter" id="writter"
                            class="form-control"
                            value="<?php echo $_SESSION['teacher_name'] ?>"
                            required disabled>
                    </div>
                    <!-- END WRITTER -->
                    <!-- START DATE -->
                    <div class="body mb-3">
                        <label for="crea_date" class="fw-semibold"><?php echo fn_lang('EDITDATE'); ?>
                        </label>
                        <input type="datetime-local" name="edit_date" id="edit_date"
                            class="form-control"
                            value="<?php echo date("Y-m-d H:i:s") ?>"
                            required disabled>
                    </div>
                    <!-- END DATE -->
                    <!-- START SUBJECT -->
                    <div class="body mb-3">
                        <label for="subject" class="fw-semibold"><?php echo fn_lang('SUBJECT'); ?>
                        </label>
                        <input type="text" name="subject" id="subject"
                            class="form-control"
                            value="<?php echo $DBrow[0]['subject'] . "-" . $DBrow[0]['grade'] . "-" . $DBrow[0]['lang'] ?>"
                            required disabled>
                    </div>
                    <!-- END SUBJECT -->
                    <!-- START DEPTH -->
                    <div class="body mb-3">
                        <label for="depth" class="fw-semibold"><?php echo fn_lang('DEPTH'); ?>
                            <select name="depth" id="depth" class="depth form-select" required>
                                <option value="RECALL"><?php echo fn_lang('RECALL') ?></option>";
                                <option value="DIRECT"><?php echo fn_lang('DIRECT') ?></option>";
                                <option value="STRATEGIC THINKNG"><?php echo fn_lang('STRATEGIC THINKNG') ?></option>";
                            </select>
                        </label>
                    </div>

                    <script>
                        document.getElementById("depth").value = <?php echo '"' . $DBEditrow['depth'] . '"'; ?>; // Change to the option with the saved value
                    </script>
                    <!-- END DEPTH -->
                    <!-- START DOK -->
                    <div class="body mb-3">
                        <label for="dok" class="fw-semibold"><?php echo fn_lang('DOK'); ?>
                        </label>
                        <input type="text" name="dok" id="dok" class="dok form-control" value="<?php echo $DBEditrow['dok']; ?>">
                    </div>
                    <!-- END DOK -->
                    <!-- START REFERENCE -->
                    <div class="body mb-3">
                        <label for="REFERENCE_EDIT" class="fw-semibold"><?php echo fn_lang('REFERENCE'); ?>
                            <select id="REFERENCE_EDIT" name="reference" class="ref_sel form-select" required>
                                <option value="none"><?php echo fn_lang('NO_REF'); ?></option>";
                                <option value="NA"><?php echo fn_lang('BRAND_NEW'); ?></option>";
                                <option value="REF"><?php echo fn_lang('REF_SELECT'); ?></option>";
                            </select>
                        </label>
                        <script>
                            document.getElementById("REFERENCE_EDIT").value = <?php echo '"' . $DBEditrow['reference'] . '"'; ?>; // Change to the option with the saved value
                        </script>
                        <input type="text" name="ref_inp" id="ref_inp_edit"
                            class="ref_inp form-control"
                            <?php if ($DBEditrow['reference'] !== "REF") {
                                echo "disabled style='display:none;'";
                            }
                            ?>
                            placeholder="<?php echo fn_lang("REF"); ?>"
                            value=<?php echo "'" . $DBEditrow['ref_txt'] . "'"; ?>>
                    </div>
                    <!-- END REFERENCE -->
                    <!-- START STEM -->
                    <div class="body mb-3 stem_bar">
                        <label for="textarea" class="fw-semibold"><?php echo fn_lang('STEM'); ?>
                        </label>
                        <!-- Math bar inserted here by following function -->
                        <?php echo fn_QuestionBar("stem") ?>
                        <script>
                            document.getElementById('textarea-stem').value = <?php echo json_encode($DBEditrow['Question_Body']); ?>;
                        </script>
                    </div>
                    <!-- END STEM -->
                    <!-- START STEM Image -->
                    <div class="body mb-3 checkbar">
                        <label for="image" class="fw-semibold image_check"><?php echo fn_lang('IMAGE'); ?>
                            <input type="checkbox" name="stemImageCheck" id="image"
                                <?php
                                if ($DBEditrow['supported_image'] != 0)
                                    echo "checked";
                                ?> hidden>
                            <div class="enhanced_bar img
                            <?php
                            if ($DBEditrow['supported_image'] != 0)
                                echo "active";
                            ?>
                            "></div>
                        </label>
                    </div>
                    <div class="body mb-3">
                        <label for="image_path" class="fw-semibold drop"
                            <?php
                            if ($DBEditrow['supported_image'] != 0) {
                                echo 'style="display: block;"';
                            } else {
                                echo 'style="display: none;"';
                            }
                            ?>>
                            <input type="file" accept="image" name="image_path" id="image_path" class="image_path form-control"
                                autocomplete="on" style="display: none;">
                            <div class="img">
                                <img alt="no image" class="imageArea" style="display:<?php echo ($DBEditrow['supported_image'] == 0) ? 'none;' : 'block;' ?>"
                                    src="<?php echo ($DBEditrow['supported_image'] == 0) ? '' : IMG_DIR . "Questions/" . $DBEditrow['image_link']; ?>">
                                <input class="DBImages" name="stemImageDB" value="<?php echo ($DBEditrow['supported_image'] == 0) ? '' : $DBEditrow['image_link']; ?>" hidden>
                                <div class="cloud">
                                    <i class="fa fa-cloud-arrow-up fa-3x" color="#0BC279"
                                        <?php
                                        if ($DBEditrow['supported_image'] != 0) {
                                            echo 'style="display: none;"';
                                        } else {
                                            echo 'style="display: block;"';
                                        }
                                        ?>></i>
                                    <p
                                        <?php
                                        if ($DBEditrow['supported_image'] != 0) {
                                            echo 'style="display: none;"';
                                        } else {
                                            echo 'style="display: block;"';
                                        }
                                        ?>>Drag and drop or click <strong>here</strong><br>to upload Image</p>
                                    <span
                                        <?php
                                        if ($DBEditrow['supported_image'] != 0) {
                                            echo 'style="display: none;"';
                                        } else {
                                            echo 'style="display: block;"';
                                        }
                                        ?>>Upload any images from your local storage</span>
                                    <p class="alert alert-danger m-0 p-1 sizeMsg" hidden>Too <strong>Large</strong> File, Try to upload file smaller than <strong>2MB</strong></p>
                                </div>
                            </div>
                        </label>
                    </div>
                    <!-- END STEM Image -->
                    <!-- START SUB STEM -->
                    <div class="body mb-3 stem_bar">
                        <label for="substem" class="fw-semibold"><?php echo fn_lang('SUBSTEM'); ?>
                        </label>
                        <!-- Math Bar inserted below -->
                        <?php echo fn_QuestionBar("substem") ?>
                        <script>
                            document.getElementById('textarea-substem').value = <?php echo  json_encode($DBEditrow['sub_stem']) ; ?>;
                        </script>
                    </div>
                    <!-- END SUBSTEM -->
                    <!-- START SUBSTEM Image -->
                    <div class="body mb-3 checkbar">
                        <label for="subimage" class="fw-semibold image_check"><?php echo fn_lang('IMAGE'); ?>
                            <input type="checkbox" name="substemImageCheck" id="subimage"
                                <?php
                                if ($DBEditrow['substem_sup_img'] != 0)
                                    echo "checked";
                                ?> hidden>
                            <div class="enhanced_bar subimg
                            <?php
                            if ($DBEditrow['substem_sup_img'] != 0)
                                echo "active";
                            ?>
                            "></div>
                        </label>
                    </div>
                    <div class="body mb-3">
                        <label for="subimage_path" class="fw-semibold subdrop"
                            <?php
                            if ($DBEditrow['substem_sup_img'] != 0) {
                                echo 'style="display: block;"';
                            } else {
                                echo 'style="display: none;"';
                            }
                            ?>>
                            <input type="file" accept="image" name="subimage_path" id="subimage_path" class="image_path form-control"
                                autocomplete="on" style="display: none;">
                            <div class="img">
                                <img alt="no image" class="subimageArea" style="display:<?php echo ($DBEditrow['substem_sup_img'] == 0) ? 'none;' : 'block;' ?>"
                                    src="<?php echo ($DBEditrow['substem_sup_img'] == 0) ? '' : IMG_DIR . "Questions/" . $DBEditrow['substem_image_link']; ?>">
                                <input class="DBImages" name="subStemImageDB" value="<?php echo ($DBEditrow['substem_sup_img'] == 0) ? '' : $DBEditrow['substem_image_link']; ?>" hidden>
                                <div class="subcloud cloud">
                                    <i class="fa fa-cloud-arrow-up fa-3x" color="#0BC279"
                                        <?php
                                        if ($DBEditrow['substem_sup_img'] != 0) {
                                            echo 'style="display: none;"';
                                        } else {
                                            echo 'style="display: block;"';
                                        }
                                        ?>></i>
                                    <p
                                        <?php
                                        if ($DBEditrow['substem_sup_img'] != 0) {
                                            echo 'style="display: none;"';
                                        } else {
                                            echo 'style="display: block;"';
                                        }
                                        ?>>Drag and drop or click <strong>here</strong><br>to upload Image</p>
                                    <span
                                        <?php
                                        if ($DBEditrow['substem_sup_img'] != 0) {
                                            echo 'style="display: none;"';
                                        } else {
                                            echo 'style="display: block;"';
                                        }
                                        ?>>Upload any images from your local storage</span>
                                    <p class="alert alert-danger m-0 p-1 sizeMsg" hidden>Too <strong>Large</strong> File, Try to upload file smaller than <strong>2MB</strong></p>
                                </div>
                            </div>
                        </label>
                    </div>
                    <!-- END SUBSTEM Image -->
                    <!-- START OPTIONS -->
                    <h3 class="Questions" ><?php echo fn_lang('QUESTIONREV') ?></h3>
                    <div class="Question coloring">
                        <?php for ($i = 1; $i < 5; $i++) {
                            $questionName = ["One", "Two", "Three", "Four"]
                        ?>

                            <!-- START OPTION ONE TWO THREE FOUR TAGS -->
                            <div class="coloringChildren">
                                <div class="body Question mb-3 option<?php echo $questionName[$i - 1] ?>">
                                    <div class="body mb-3">
                                        <!-- START TRUE ANSWER -->
                                        <div>
                                            <label for="radio<?php echo $i ?>" class="fw-semibold"><?php echo fn_lang('TRUE'); ?>
                                            </label>
                                            <input type="radio" name="radioOptTrue" id="radio<?php echo $i ?>" class="choiceRadio form-radio" value="<?php echo $i; ?>"
                                                <?php if ($i == $DBEditrow['correct_answer']) echo "checked"; ?>>
                                        </div>
                                        <!-- END TRUE ANSWER -->
                                        <!-- START ANSWER PHRASE -->
                                        <div>
                                            <label for="opt<?php echo $i ?>" class="fw-semibold"><?php echo fn_lang('CHOICE') . ' ' . $questionName[$i - 1]; ?>
                                            </label>
                                            <?php echo fn_QuestionBar("opt".$i);?>
                                            <script>
                                                document.getElementById('<?php echo"opt".$i;?>').value = <?php echo  json_encode($DBEditrow['answer' . $i]) ; ?>;
                                            </script>
                                        </div>
                                        <!-- END ANSWER PHRASE -->
                                        <!-- START ANSWER REASON -->
                                        <div>
                                            <label for="opt<?php echo $i ?>Exp" class="fw-semibold"><?php echo fn_lang('CAUSE'); ?>
                                            </label>
                                            <input type="text" name="opt<?php echo $i ?>Exp" id="opt<?php echo $i ?>Exp" class="form-control " value=<?php echo '"' . $DBEditrow['ans' . $i . '_explain'] . '"'; ?>>
                                        </div>
                                        <!-- END ANSWER REASON -->
                                    </div>
                                    <!-- START SUPPORT IMAGE -->
                                    <label for="opimage<?php echo $i ?>" class="fw-semibold image_check"><?php echo fn_lang('OPTION'); ?>
                                        <input type="checkbox" name="opimage<?php echo $i ?>" id="opimage<?php echo $i ?>" data-number="<?php echo $i ?>" class="optionImage"
                                            <?php
                                            if ($DBEditrow['ans' . $i . '_sup_img'] != 0)
                                                echo "checked";
                                            ?> hidden>
                                        <div class="enhanced_bar op <?php echo $i ?> <?php
                                                                                        if ($DBEditrow['ans' . $i . '_sup_img'] != 0)
                                                                                            echo "active";
                                                                                        ?>
                                        "></div>
                                    </label>
                                    <!-- END SUPPORT IMAGE -->
                                </div>
                                <!-- START IMAGE SECTION -->
                                <div class="body mb-3">
                                    <label for="Qopt_path<?php echo $i ?>" class="fw-semibold drop Q Area" data-number="<?php echo $i ?>"
                                        <?php
                                        if ($DBEditrow['ans' . $i . '_sup_img'] != 0) {
                                            echo 'style="display: block;"';
                                        } else {
                                            echo 'style="display: none;"';
                                        }
                                        ?>>
                                        <input type="file" accept="image" name="option<?php echo $questionName[$i - 1] ?>_path" id="Qopt_path<?php echo $i ?>" data-number="<?php echo $i ?>" class="Q option_path form-control"
                                            autocomplete="on" style="display: none;">
                                        <div class="Q  img">
                                            <img alt="" class="Q imageArea" style="display:<?php echo ($DBEditrow['ans' . $i . '_sup_img'] == 0) ? 'none;' : 'block;' ?>"
                                                src="<?php echo ($DBEditrow['ans' . $i . '_sup_img'] == 0) ? '' : IMG_DIR . "Questions/" . $DBEditrow['ans' . $i . '_image_link']; ?>">
                                            <input class="DBImages" name="<?php echo 'ans' . $i . 'ImageDB'; ?>" value="<?php echo ($DBEditrow['ans' . $i . '_sup_img'] == 0) ? '' : $DBEditrow['ans' . $i . '_image_link']; ?>" hidden>
                                            <div class="Q cloud">
                                                <i class="fa fa-cloud-arrow-up fa-3x" color="#0BC279" <?php
                                                                                                        if ($DBEditrow['ans' . $i . '_sup_img'] != 0) {
                                                                                                            echo 'style="display: none;"';
                                                                                                        } else {
                                                                                                            echo 'style="display: block;"';
                                                                                                        }
                                                                                                        ?>></i>
                                                <p
                                                    <?php
                                                    if ($DBEditrow['ans' . $i . '_sup_img'] != 0) {
                                                        echo 'style="display: none;"';
                                                    } else {
                                                        echo 'style="display: block;"';
                                                    }
                                                    ?>>Drag and drop or click <strong>here</strong><br>to upload Image</p>
                                                <span
                                                    <?php
                                                    if ($DBEditrow['ans' . $i . '_sup_img'] != 0) {
                                                        echo 'style="display: none;"';
                                                    } else {
                                                        echo 'style="display: block;"';
                                                    }
                                                    ?>>Upload any images from your local storage</span>
                                                <p class="alert alert-danger m-0 p-1 optionsImg" hidden>Too <strong>Large</strong> File, Try to upload file smaller than <strong>2MB</strong></p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <!-- END IMAGE SECTION -->
                            </div>
                            <!-- END OPTION ONE TWO THREE FOUR TAGS -->
                        <?php
                        }
                        // END LOOPING -->
                        echo "<br>"; ?>
                    </div>
                    <!-- END OPTIONS -->
                </div>
                <!-- END The Question Body -->
                <!-- START VARIABLES SECTION -->
                <div class="d-grid gap-3 col-12 offset-0">
                    <input type="text" name="writter" value="<?php echo $_SESSION['teacher_name'] ?>" hidden>
                    <input type="datetime-local" name="creation_date" value="<?php echo date("Y-m-d H:i:s") ?>" hidden>
                    <input type="text" name="type" value="<?php echo $DBrow[0]['type'] ?>" hidden>
                    <input type="text" name="stage" value="<?php echo $DBrow[0]['stage'] ?>" hidden>
                    <input type="text" name="subject" value="<?php echo $DBrow[0]['subject'] ?>" hidden>
                    <input type="text" name="grade" value="<?php echo $DBrow[0]['grade'] ?>" hidden>
                    <input type="text" name="lang" value="<?php echo $DBrow[0]['lang'] ?>" hidden>
                </div>
                <!-- END VARIABLES SECTION -->
                <div class="d-flex gap-3 col-12 offset-0">
                    <!-- START BTN AREA -->
                    <!-- START SUMBIT BTN -->
                    <div class="d-grid col-3">
                        <input type="submit" id="submitbtn" name="updateDB" value="<?php echo fn_lang('SAVE') ?>" class="btn btn-primary  btn-lg">
                    </div>
                    <!-- END SUMBIT BTN -->
                    <!-- START CANCEL BTN -->
                    <div class="d-grid col-3">
                        <a href="?do=Dash&Langset=<?php echo ($langset) ?>" class="btn  btn-warning  btn-lg">Cancel</a>
                    </div>
                    <!-- END CANCEL BTN -->
                    <!-- END BTN AREA -->
                </div>
            </form>

            <!-- END Edit Question Section -->


        </div>
    </div>
    <?php
                break;
            case 'Delete':
                if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['record'])) {
                    $QUESTID = isset($_GET['record']) && is_numeric($_GET['record']) ? intval($_GET['record']) : 0;
                    //check images first
                    $DBRow=$con->prepare("SELECT * FROM questions_create WHERE question_id=?");
                    $DBRow->execute(array($_GET['record']));
                    $DBEditRow=$DBRow->fetch();
                    $QuestionDir = $imgDir . "Questions/";
                    // if any of image files are exist this code will delete file (Unlink)
                    for($i=1;$i<=6;$i++){
                        switch($i){
                            case '5':
                                $getDBImageName = explode("_", $DBEditRow['image_link']);
                                if ($getDBImageName[0]!==""){
                                    unlink($QuestionDir.$DBEditRow['image_link']); //delete the file
                                }
                                break;
                            case '6':
                                $getDBImageName = explode("_", $DBEditRow['substem_image_link']);
                                if ($getDBImageName[0]!==""){
                                    unlink($QuestionDir.$DBEditRow['substem_image_link']); //delete the file
                                }
                                break;
                            default:
                                $getDBImageName = explode("_", $DBEditRow['ans'.$i.'_image_link']);
                                if ($getDBImageName[0]!==""){
                                    unlink($QuestionDir.$DBEditRow['ans'.$i.'_image_link']); //delete the file
                                }
                        }
                    }
                    $delQ = $con->prepare("DELETE FROM questions_create WHERE question_id=$QUESTID");
                    $delQ->execute();
                    //Echo sucess method
                    $msg = $delQ->rowCount() . " Record(s) Deleted";
    ?>
        <!-- Page Heading -->
        <script>
            document.getElementById("header").innerText = "<?php echo fn_lang('DELETEQ'); ?>";
        </script>
        <div class="question" style='width: 100%;margin:20px;padding:20px;'>
            <?php fn_redirect($msg, 'danger', 3, "back") ?>
        </div>
    <?php
                } else {
                    $msg = "No Record(s) is Deleted";
    ?>
        <div class="question" style='width: 100%;margin:20px;padding:20px;'>
            <?php fn_redirect($msg, 'primary', 3, "homepage.php?do=Dash&Langset=$langset"); ?>
        </div>
    <?php

                }
                break;
            case 'RevisionSend':
                if (($_SERVER["REQUEST_METHOD"] == "GET" || $_SERVER["REQUEST_METHOD"] == "POST") && isset($_GET['record'])) {
                    $QUESTIONID = isset($_GET['record']) && is_numeric($_GET['record']) ? intval($_GET['record']) : 0;
                    $RevQ = $con->prepare("UPDATE questions_create SET  question_case=?,cart=?,cart1=?,cart2=?,sent_to_revision_date=? WHERE question_id=?");
                    $RevQ->execute(array(1, 1,1,1,date("Y-m-d H:i:s"), $QUESTIONID));
                    //Echo sucess mthods
                    $msg = $RevQ->rowCount() . " Record(s) Sent To revision";
    ?>
        <!-- Page Heading -->
        <script>
            document.getElementById("header").innerText = "<?php echo fn_lang('SEND'); ?>";
        </script>
        <div class="question" style='width: 100%;margin:20px;padding:20px;'>
            <?php fn_redirect($msg, 'success', 3, "homepage.php?do=Dash&Langset=$langset") ?>
        </div>
    <?php
                } else {
                    $msg = "No Record(s) is Sent To revision";
    ?>
        <div class="question" style='width: 100%;margin:20px;padding:20px;'>
            <?php fn_redirect($msg, 'primary', 3, "homepage.php?do=Dash&Langset=$langset"); ?>
        </div>
    <?php

                }
                break;
            case 'Dataquest':
    ?>
    <!-- Page Heading -->
    <script>
        document.getElementById("header").innerText = "<?php echo fn_lang('DATAQ'); ?>";
        document.getElementById("tag4").parentNode.classList.add("active");
    </script>
    <div class="question contents">
        <p>Hi Database</p>
        <h1>Under construction</h1>
    </div>
<?php
                break;
            case 'Collectquest':
    ?>
    <!-- Page Heading -->
    <script>
        document.getElementById("header").innerText = "<?php echo fn_lang('GETQ'); ?>";
        document.getElementById("tag5").parentNode.classList.add("active");
    </script>
    <div class="question contents">
        <p>Hi collection</p>
        <h1>Under construction</h1>
    </div>
<?php
    break;
case 'Revisorpage'://glasses btn clicked
?>
<!-- START RIVISOR GENERAL PAGE -->
    <!-- Page Heading -->
    <script>
        document.getElementById("header").innerText = "<?php echo fn_lang('REVISOR'); ?>";
    </script>
    <div class="container revisor" dir="<?php echo ($langset == "Eng") ? "ltr" : "rtl"; ?>">
        <div class="tabs">
            <div class="tab active" onclick="switchTab(event, 'tab1')"><?php echo fn_lang('REVISION');?></div>
            <div class="tab" onclick="switchTab(event, 'tab2')"><?php echo fn_lang('STUDY');?></div>
            <?php
                //Active account
                $DBO = $con->prepare("SELECT *
                    FROM
                        teacher_items                        
                    WHERE
                        teacher_id = ?
                    AND
                        account_status = ?
                        ");
                $DBO->execute(array($_SESSION['teacher_id'],1));
                $activeAccount=$DBO->fetch();
            ?>
            <!-- REVISOR first time revision page -->
            <div id="tab1" class="tab-content active">
                <div class="questionsarea">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th scope="col"><?php echo fn_lang('OUTCOME');?></th>
                                    <th scope="col"><?php echo fn_lang('QUESTION');?></th>
                                    <th scope="col"><?php echo fn_lang('ACTION');?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $DBrow = fn_getRecordsV2("*", "teacher_items", "WHERE teacher_id=? AND account_status=?", array($_SESSION['teacher_id'], 1));
                                $DBEdit = $con->prepare("SELECT *
                                FROM
                                    questions_create
                                WHERE
                                    questions_create.question_case=:zqcase
                                AND
                                (
                                    (revisor_one=:zrev1 AND cart1=:zcart1)
                                OR
                                    (revisor_two=:zrev2 AND cart2=:zcart2)
                                )
                                AND
                                    questions_create.type = :ztype
                                AND                  
                                    questions_create.stage = :zstage
                                AND                  
                                    questions_create.grade = :zgrade
                                AND
                                    questions_create.subject = :zsubject
                                AND
                                    questions_create.lang = :zlang
                                ORDER BY
                                    creation_date ASC
                                ");
                                $DBEdit->execute(array(
                                    ':zqcase'=>1,
                                    ':zrev1'=>$_SESSION['teacher_id'],
                                    ':zcart1'=>1,
                                    ':zrev2'=>$_SESSION['teacher_id'],
                                    ':zcart2'=>1,
                                    ':ztype'=>$activeAccount['type'],
                                    ':zstage'=>$activeAccount['stage'],
                                    ':zgrade'=>$activeAccount['grade'],
                                    ':zsubject'=>$activeAccount['subject'],
                                    ':zlang'=>$activeAccount['lang']
                                ));
                                while ($DBEditrow = $DBEdit->fetch()) {
                                    // $DBLO database learning objects
                                    $DBLO=fn_getRecordsV2("*", "learning_outcomes", "WHERE outcome_id=?", array($DBEditrow['outcome_id']));
                                        $LO=$DBLO[0]['unit']." - ".$DBLO[0]['chapter']." - ".$DBLO[0]['item'];
                                        
                                    // outcomes", "WHERE outcome_id=?", array($DBEditrow['outcome_id']));
                                    if($DBEditrow['revisor_one']==$_SESSION['teacher_id']){
                                        $revisorQue=1;
                                        $revisor=$DBEditrow['revisor_one'];
                                    }else{
                                            $revisorQue=2;
                                            $revisor=$DBEditrow['revisor_two'];
                                        }
                                    $LO=$DBLO[0]['unit']." - ".$DBLO[0]['chapter']." - ".$DBLO[0]['item'];
                                    echo "<tr>";
                                    echo "<td class='text-center'>" . $LO. "</td>";
                                    echo "<td>" . $DBEditrow['Question_Body'] . "</td>";
                                    if($revisorQue==1){
                                        $glass = 'cart1';
                                    }else{
                                        $glass = 'cart2';
                                    }
                                    echo "<td>"."<a href=?do=Revisoredit&record=" . $DBEditrow['question_id']."&Revisor=".$revisor."&RevisorQue=".$revisorQue."&glass=".$glass."&Langset=".$langset."&New=1>".fn_lang('REVLINK')."</a></td>";
                                    echo "</tr>";
                                };
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- REVISOR Under Study page -->
            <div id="tab2" class="tab-content">   
                <div class="questionsarea">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th scope="col"><?php echo fn_lang('OUTCOME');?></th>
                                    <th scope="col"><?php echo fn_lang('QUESTION');?></th>
                                    <th scope="col"><?php echo fn_lang('ACTION');?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                //get active profile for revisor
                                $DBrow = fn_getRecordsV2("*", "teacher_items", "WHERE teacher_id=? AND account_status=?", array($_SESSION['teacher_id'], 1));
                                //get records to be revised [Green]
                                $DBEdit = $con->prepare("SELECT *
                                FROM
                                    questions_create
                                INNER JOIN
                                    question_review
                                ON
                                    questions_create.question_id = question_review.question_id
                                WHERE
                                    questions_create.question_case=?
                                AND
                                (
                                    (revisor_one=? AND cart1=?)
                                OR 
                                    (revisor_two=? AND cart2=?)
                                )
                                AND
                                    questions_create.type = ?
                                AND                  
                                    questions_create.stage = ?
                                AND                  
                                    questions_create.grade = ?
                                AND
                                    questions_create.subject = ?
                                AND
                                    questions_create.lang = ?
                                AND
                                    question_review.revisor_id=? 
                                AND 
                                    question_review.review_status=?
                                ORDER BY
                                    creation_date ASC
                                ");
                                $DBEdit->execute(array(1,$_SESSION['teacher_id'],0,$_SESSION['teacher_id'],0,$activeAccount['type'],$activeAccount['stage'],$activeAccount['grade'],$activeAccount['subject'],$activeAccount['lang'],$_SESSION['teacher_id'],3));
                                $result=$DBEdit->fetchAll();
                                // while ($DBEditrow = $DBEdit->fetch()) { 
                                foreach($result as $DBEditrow){
                                    if($DBEditrow['revisor_one']==$_SESSION['teacher_id']){
                                        $revisorQue=1;
                                        $revisor=$DBEditrow['revisor_one'];
                                    }else{
                                            $revisorQue=2;
                                            $revisor=$DBEditrow['revisor_two'];
                                        }
                                    // //Get reviewed for the Revisor
                                    // $DBreview=$con->prepare("SELECT * FROM question_review 
                                    // WHERE 
                                    //     revisor_id=? 
                                    // AND 
                                    //     review_status=?
                                    // ");
                                    // $DBreview->execute(array($_SESSION['teacher_id'],3));
                                    // $DBreviewQuestions=$DBreview->fetchAll();
                                    // $revRec=0;
                                    // foreach ($DBreviewQuestions as $DBreviewQuest) {
                                    //     echo "Review Q_ID: ".$DBreviewQuest['question_id']."<br>";
                                    //     echo $DBEditrow['question_id']."<br>====================<br>";
                                    //     if ($DBreviewQuest['question_id']===$DBEditrow['question_id']) $revRec=$DBreviewQuest['id'];
                                    //     else continue;
                                    // }
                                    $GETQ=fn_getRecordsV2("*", "questions_create", "WHERE question_id=?", array($DBEditrow['question_id']));
                                    $DBLO=fn_getRecordsV2("*", "learning_outcomes", "WHERE outcome_id=?", array($GETQ[0]['outcome_id']));
                                    $LO=$DBLO[0]['unit']." - ".$DBLO[0]['chapter']." - ".$DBLO[0]['item'];
                                    echo "<tr>";
                                    echo "<td class='text-center'>" . $LO. "</td>";
                                    echo "<td>" . $GETQ[0]['Question_Body'] . "</td>";
                                    if($revisorQue==1){
                                        $glass = 'cart1';
                                    }else{
                                        $glass = 'cart2';
                                    }
                                    // echo "<td>" ."<a href=?do=Revisoredit&record=" . $DBreviewQuestions[$revRec]['question_id']."&review_id=" .$DBreviewQuestions[$revRec]['id'] ."&Revisor=".$DBreviewQuestions[$revRec]['revisor_id']."&RevisorQue=".$revisorQue."&glass=".$glass. "&Langset=".$langset."&New=0>".fn_lang('REVSTUDY')."</a></td>";
                                    echo "<td>" ."<a href=?do=Revisoredit&record=" . $DBEditrow['question_id']."&review_id=" .$DBEditrow['id'] ."&Revisor=".$DBEditrow['revisor_id']."&RevisorQue=".$revisorQue."&glass=".$glass. "&Langset=".$langset."&New=0>".fn_lang('REVSTUDY')."</a></td>";
                                    echo "</tr>";
                                };
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> 
        </div> 
    </div>
<!-- END REVISOR GENERAL PAGE -->
<?php
break;
case 'Revisoredit':
    ?>
    <!-- START RIVISOR EDIT PAGE -->
<!-- Page Heading -->
<script>
    document.getElementById("header").innerText = "<?php echo fn_lang('REVISOR'); ?>";
</script>

<div class="container revisor" dir="<?php echo ($langset == "Eng") ? "ltr" : "rtl"; ?>">
    <?php
    //  Database adjustments
    $DBrow = fn_getRecordsV2("*", "teacher_items", "WHERE teacher_id=? AND account_status=?", array($_SESSION['teacher_id'], 1));
        $DBEdit = $con->prepare("SELECT *
        FROM
            questions_create
        WHERE
            questions_create.question_id=?
        ");
        $DBEdit->execute(array($_GET['record']));
        $DBEditrow = $DBEdit->fetch();//record Question line in the variable;
        $glass=$_GET['glass']; // get cart1 or cart2
        // change cart after creating the new file of review
        if($_GET['New']==1){//1st time to revise a question
            // Create the review record in DATABASE
            // Add into DB question_review
            $getrevisor=($_GET['RevisorQue']==1)?$DBEditrow['revisor_one']:$DBEditrow['revisor_two'];
            // Check if the record already exists to prevent duplicated
            $checkReview = $con->prepare("SELECT COUNT(*) FROM 
                question_review 
            WHERE 
                question_id = ? 
            AND 
                revisor_id = ?
                ");
            $checkReview->execute(array($_GET['record'], $_GET['Revisor']));
            $exists = $checkReview->fetchColumn();

            if ($exists == 0) {
                // don't Duplicate and continue action
                $DBreview=$con->prepare("INSERT INTO question_review(
                    question_id, 
                    teacher_id, 
                    revisor_id, 
                    start_review_date, 
                    review_date, 
                    review_status) 
                VALUES (
                :zquestion_id,
                :zteacher_id,
                :zrevisor_id,
                :zreview_date,
                :zreview_date,
                :zreview_status)
                ON DUPLICATE KEY UPDATE start_review_date = VALUES(start_review_date)
                ");
                $DBreview->execute(array(
                    ':zquestion_id'=>$_GET['record'],
                    ':zteacher_id'=>$DBEditrow['teacher_id'],
                    ':zrevisor_id'=>$getrevisor,
                    ':zreview_date'=>date("Y-m-d H:i:s"),
                    ':zreview_status'=>1
                ));
            }
            // change cart to be revert to review
            $DBEdit2=$con->prepare("UPDATE questions_create SET $glass=? WHERE question_id=?");
            $DBEdit2->execute(array(0,$DBEditrow['question_id']));
            
            //start REVISION for question items
            //prepare DB file for edit
            
            $DBfile=$con->prepare("SELECT * 
                FROM 
                    question_review 
                INNER JOIN
                    questions_create
                ON
                    questions_create.question_id = question_review.question_id                
                WHERE 
                    question_review.question_id=? 
                AND 
                    question_review.revisor_id=?
                ");
            $DBfile->execute(array($_GET['record'],$_GET['Revisor']));
            $result=$DBfile->fetch();//select the review record
            //get Creator
            $DBQuestion=$con->prepare("SELECT * 
            FROM 
                teachers 
            WHERE 
                teacher_id=?
            ");
            $DBQuestion->execute(array($result['teacher_id']));
            $creator=$DBQuestion->fetch();
            //Get LEARNING OUTCOME
            $DBLO=fn_getRecordsV2("*", "learning_outcomes", "WHERE outcome_id=?", array($result['outcome_id']));
            $LO=$DBLO[0]['unit']." - ".$DBLO[0]['chapter']." - ".$DBLO[0]['item']." - ".$DBLO[0]['content'];
            $now= new Datetime();
            ?>
            <div class="question" style='width: 100%;margin:20px;padding:20px;'>
                <form action="?do=AcceptQuestion&Langset=<?php echo ($langset); ?>&Revisor=<?php echo $_SESSION['teacher_id'];?>&QuestionID=<?php echo $DBEditrow['question_id'];?>&ReviewID=<?php echo $result['id'] ;?>&RevisorQue=<?php echo $_GET['RevisorQue'];?>" method="POST" encrypted="">
                    <!-- START Revision Page -->
                    <!-- START QUESTION BODY SECTION -->
                    <section class="question">
                        <div class="body mb-3">
                            <label for="writter" class="fw-semibold" style="diplay : block;width : 100%;"><?php echo fn_lang('STEMQ'); ?>
                            </label>
                            <?php echo $result['Question_Body'] ?>
                        </div>
                        <div class="body mb-3">
                            <?php echo ($result['supported_image']==1)
                            ?'<img src="' . $imgDir ."Questions/". $result['image_link'] . '" style="max-width: 40%; ">' 
                            : '' ?>
                        </div>
                        <div class="body mb-3">
                            <?php echo ($result['sub_stem']!="")
                            ? $result['sub_stem']
                            : '' ?>
                        </div>
                        <div class="body mb-3">
                            <?php echo ($result['substem_sup_img']==1)
                            ?'<img src="' . $imgDir ."Questions/". $result['substem_image_link'] . '" style="max-width: 40%;">' 
                            : '' ?>
                        </div>
                        <?php
                            $questionLabel = ["A", "B", "C", "D"];
                            for ($i = 1; $i < 5; $i++) {
                        ?>
                        <div class="body mb-3" dir="<?php echo ($langset == "Eng") ? "ltr" : "rtl"; ?>">
                            <span class="labels <?php echo($result['correct_answer']==$i) ? 'answer':'' ;?>"dir="<?php echo ($langset == "Eng") ? "ltr" : "rtl"; ?>"><?php echo $questionLabel[$i-1];?></span>
                            <span dir="<?php echo ($langset == "Eng") ? "ltr" : "rtl"; ?>">
                            <?php echo ($result['answer'.$i]!=="")
                            ?$result['answer'.$i]
                            : '' ?>
                            </span>
                            <?php if($result['ans'.$i.'_sup_img']==1){
                            echo '<span dir="'.($langset == "Eng") ? "ltr" : "rtl".'">';
                            '<img src="' . $imgDir ."Questions/". $result['ans'.$i.'_image_link'] 
                            ."</span>";
                            }?>
                        </div>
                        <div class="explained" style="margin-bottom : 20px;">
                            <span style="font-weight : bold;">
                                <?php echo fn_lang('EXPLAIN');?> : 
                            </span>
                            <span>
                            <?php echo $result['ans'.$i.'_explain']; ?>
                            </span>
                        </div>
                        <?php
                        }
                        ?>
                    </section>
                    <!-- END QUESTION BODY SECTION-->
                    <!-- START QUESTION DATA SECTION -->
                    <section class="data">
                        <!-- START WRITTER -->
                        <div class="body mb-3">
                            <label for="writter" class="fw-semibold"><?php echo fn_lang('WRITTER'); ?>
                            </label>
                            <input type="text" id="writter"
                                class="form-control"
                                value="<?php echo $creator['ename'] ?>"
                                disabled>
                        </div>
                        <!-- END WRITTER -->
                        <!-- START CREATEDATE -->
                        <div class="body mb-3">
                            <label for="createdate" class="fw-semibold"><?php echo fn_lang('DATEWRITTEN'); ?>
                            </label>
                            <input type="text" id="creatdate"
                                class="form-control"
                                value="<?php echo $result['creation_date'] ?>"
                                disabled>
                        </div>
                        <!-- END CREATEDATE -->
                        <!-- START SUBJECT -->
                        <div class="body mb-3">
                            <label for="subject" class="fw-semibold"><?php echo fn_lang('SUBJECT'); ?>
                            </label>
                            <input type="text" id="subject"
                                class="form-control"
                                value="<?php echo $result['subject'] ?>"
                                disabled>
                        </div>
                        <!-- END SUBJECT -->
                        <!-- START PALIGRISM -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('PALGRISM'); ?>
                            </label>
                            <p>
                            <?php echo fn_lang('PALGRISMTALK'); ?>
                            </p>
                            <!-- START PALIGRISM CONTENT-->
                            <input type="text" name="palcontent" id="palcontent"
                            class="form-control"
                            value="">
                            <!-- END PALIGRISM CONTENT-->
                        </div>
                        <!-- END PALIGRISM -->
                    </section>
                    <!-- END QUESTION DATA SECTION -->
                    <!-- START CONTENT SUITABLILITY OUTCOME SECTION -->
                    <section class="outcome">
                        <!-- START LO -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('LO'); ?>
                                <input class="checker" type="checkbox" name="lo" id="lo" checked hidden>
                                
                            <!-- START LO BTN -->
                                <div class="enhanced_bar rev active"
                                style="
                                display: inline-block;
                                "></div>
                            </label>
                            <!-- END LO BTN-->
                            <!-- START LO CONTENT-->
                            <input type="text" name="locontent" id="locontent"
                            class="form-control revinp"
                            value="<?php echo $LO;?>"
                                disabled>
                            <!-- END LO CONTENT-->
                            <!-- START LO COMMENT -->
                            <div class="body mb-3">
                                <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="locheck" id="locheck" hidden></textarea>
                            </div>
                            <!-- END LO COMMENT-->
                        </div>
                        <!-- END LO -->
                        <!-- START DEPTH OF KNOWLEDGE (DOK) -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('DEPTH'); ?>
                                <input class="checker" type="checkbox" name="revdok" id="depth" checked hidden>
                            <!-- START DOK BTN -->
                                <div class="enhanced_bar rev active"
                                style="
                                display: inline-block;
                                "></div>
                            </label>
                            <!-- END DOK BTN-->
                            <!-- START DOK CONTENT-->
                            <input type="text" name="depthcontent" id="depthcontent"
                            class="form-control revinp"
                            value="<?php echo fn_lang($result['depth']);?>"
                                disabled>                                
                                <!-- DEBUG -->
                            <!-- END DOK CONTENT-->
                            <!-- START DOK COMMENT -->
                            <div class="body mb-3">
                                <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="dokcheck" id="dokcheck" hidden></textarea>
                            </div>
                            <!-- END DOK COMMENT-->
                            <!-- START DOK EXTRA NOTE -->
                            <div class="body mb-3">
                                <?php echo fn_lang("DOKEXTRA");?>
                            </div>
                            <!-- END DOK EXTRA NOTE-->
                        </div>
                        <!-- END DEPTH OF KNOWLEDGE (DOK) -->
                        <!-- START DOKPROFF -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('DOKPROFF'); ?>
                                <input class="checker" type="checkbox" name="dokproff" id="dokproff" checked hidden>
                            <!-- START DOK BTN -->
                                <div class="enhanced_bar rev active"
                                style="
                                display: inline-block;
                                "></div>
                            </label>
                            <!-- END DOK BTN-->
                            <!-- START DOK CONTENT-->
                            <input type="text" name="dokproffcontent" id="dokproffcontent"
                            class="form-control revinp"
                            value="<?php echo $result['dok'];?>"
                                disabled>
                            <!-- END DOK CONTENT-->
                            <!-- START DOK COMMENT -->
                            <div class="body mb-3">
                                <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="dokproffcheck" id="dokproffcheck" hidden></textarea>
                            </div>
                            <!-- END DOK COMMENT-->
                            <!-- START DOK EXTRA NOTE -->
                            <div class="body mb-3">
                                <?php echo fn_lang("DOKPROFFEXTRA");?>
                            </div>
                            <!-- END DOK EXTRA NOTE-->
                        </div>
                        <!-- END DOKPROFF -->
                        <!-- START REFERENCE -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('REFERENCE'); ?>
                                <input class="checker" type="checkbox" name="refstatus" id="reference" checked hidden>
                                
                            <!-- START REFERENCE BTN -->
                                <div class="enhanced_bar rev active"
                                style="
                                display: inline-block;
                                "></div>
                            </label>
                            <!-- END REFERENCE BTN-->
                            <!-- START REFERENCE CONTENT-->
                            <input type="text" name="reftxtcontent" id="reftxtcontent"
                            class="form-control revinp"
                            value="<?php if ($result['reference']=='REF'){
                                echo $result['reference'].'/ '.$result['ref_txt'];
                                } else if($result['reference']=='NA') {
                                    echo 'Brand New / No reference name';
                                }else {
                                    echo $result['reference'];
                                }?>"                            
                                disabled>
                            <!-- END REFERENCE CONTENT-->
                            <!-- START REFERENCE COMMENT -->
                            <div class="body mb-3">
                                <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="refcheck" id="referencecheck" hidden></textarea>
                            </div>
                            <!-- END REFERENCE COMMENT-->
                        </div>
                        <!-- END REFERENCE -->
                        <!-- START ERROR -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('ERROR'); ?>
                                <input class="checker" type="checkbox" name="err" id="err" checked hidden>
                            <!-- START ERROR BTN -->
                                <div class="enhanced_bar rev active"
                                style="
                                display: inline-block;
                                "></div>
                            </label>
                            <!-- END ERROR BTN-->
                            <!-- START ERROR CONTENT-->
                            <input type="text"  id="errcontent"
                            class="form-control revinp"
                            value=""
                                disabled hidden>
                            <!-- END ERROR CONTENT-->
                            <!-- START ERROR COMMENT -->
                            <div class="body mb-3">
                                <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="errcheck" id="errcheck" hidden></textarea>
                            </div>
                            <!-- END ERROR COMMENT-->
                            
                        </div>
                        <!-- END ERROR -->
                        <!-- START KEY -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('KEY'); ?>
                                <input class="checker" type="checkbox" name="key" id="key" checked hidden>
                            <!-- START KEY BTN -->
                                <div class="enhanced_bar rev active"
                                style="
                                display: inline-block;
                                "></div>
                            </label>
                            <!-- END KEY BTN-->
                            <!-- START KEY CONTENT-->
                            <input type="text"  id="keycontent"
                            class="form-control revinp"
                            value=""
                                disabled hidden>
                            <!-- END KEY CONTENT-->
                            <!-- START KEY COMMENT -->
                            <div class="body mb-3">
                                <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="keycheck" id="keycheck" hidden></textarea>
                            </div>
                            <!-- END KEY COMMENT-->
                            <!-- START DOK EXTRA NOTE -->
                            <div class="body mb-3">
                                <?php echo fn_lang("KEYEXTRA");?>
                            </div>
                            <!-- END DOK EXTRA NOTE-->
                        </div>
                        <!-- END KEY -->
                        <!-- START IDENTIFIER -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('IDENTIFIER'); ?>
                                <input class="checker" type="checkbox" name="identifier" id="identifier" checked hidden>
                            <!-- START IDENTIFIER BTN -->
                                <div class="enhanced_bar rev active"
                                style="
                                display: inline-block;
                                "></div>
                            </label>
                            <!-- END IDENTIFIER BTN-->
                            <!-- START IDENTIFIER CONTENT-->
                            <input type="text"  id="identifiercontent"
                            class="form-control revinp"
                            value=""
                                disabled hidden>
                            <!-- END IDENTIFIER CONTENT-->
                            <!-- START IDENTIFIER COMMENT -->
                            <div class="body mb-3">
                                <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="identifiercheck" id="identifiercheck" hidden></textarea>
                            </div>
                            <!-- END IDENTIFIER COMMENT-->
                            
                        </div>
                        <!-- END IDENTIFIER -->
                        <!-- START IDENTIFIERONLY -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('IDENTIFIERONLY'); ?>
                                <input class="checker" type="checkbox" name="identifier_only" id="identifieronly" checked hidden>
                            <!-- START IDENTIFIER BTN -->
                                <div class="enhanced_bar rev active"
                                style="
                                display: inline-block;
                                "></div>
                            </label>
                            <!-- END IDENTIFIER BTN-->
                            <!-- START IDENTIFIER CONTENT-->
                            <input type="text"  id="identifieronlycontent"
                            class="form-control revinp"
                            value=""
                                disabled hidden>
                            <!-- END IDENTIFIER CONTENT-->
                            <!-- START IDENTIFIER COMMENT -->
                            <div class="body mb-3">
                                <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="identifieronlycheck" id="identifieronlycheck" hidden></textarea>
                            </div>
                            <!-- END IDENTIFIER COMMENT-->
                            
                        </div>
                        <!-- END IDENTIFIERONLY -->
                        <!-- START DISTRACTORS -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('DISTRACTORS'); ?>
                                <input class="checker" type="checkbox" name="distract" id="distract" checked hidden>
                            <!-- START DISTRACTORS BTN -->
                                <div class="enhanced_bar rev active"
                                style="
                                display: inline-block;
                                "></div>
                            </label>
                            <!-- END DISTRACTORS BTN-->
                            <!-- START DISTRACTORS CONTENT-->
                            <input type="text"  id="distractcontent"
                            class="form-control revinp"
                            value=""
                                disabled hidden>
                            <!-- END DISTRACTORS CONTENT-->
                            <!-- START DISTRACTORS COMMENT -->
                            <div class="body mb-3">
                                <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="distractcheck" id="distractcheck" hidden></textarea>
                            </div>
                            <!-- END DISTRACTORS COMMENT-->
                            <!-- START DISTRACTORS EXTRA NOTE -->
                            <div class="body mb-3">
                                <?php echo fn_lang("DISTEXTRA");?>
                            </div>
                            <!-- END DISTRACTORS EXTRA NOTE-->
                        </div>
                        <!-- END DISTRACTORS -->
                    </section>
                    <!-- END CONTENT SUITABLILITY OUTCOME SECTION -->
                    <!-- START OVERALL SECTION -->
                    <section class="overall">
                        <?php
                        $comments=['LANG','NEW QUESTION','GRAMMER','ABBREVIATION','SYMBOL',
                        'NEGATIVE','LETTERS','DRAWING','NEEDED DRAWING','RECOMMENDED ALTERNATIVES',
                        'STEM END OF QUESTION','SIMPLE PHRASE','THIRD PERSON','STEM MODIFY','RESPONDS',
                        'RESPONDS EASY TO UNDERSTAND','RESTRICT UNWANTED OPTIONS','OPLENGTH','OPGRA','OPREAP',
                        'WORDREAPEAT','NOUN','OPTION NUMBERS','OPTION DECIMALS','OPTION MODIFY'];
                        $items=['LANG','NEWQ','GRAMMER','ABBREV','SYMBOL',
                        'NEGATIVE','LETTERS','DRAWING','NEED','REC',
                        'STEMREV','SIMPLE','THIRD','STEMMOD','RESPOND',
                        'RESPEAS','RESTRICT','OPLENGTH','OPGRA','OPREAP',
                        'WORDREAPEAT','NOUN','OPNUM','OPDEC','OPMOD'];
                        $programing=['lang','newq','grammer','abbrev','symbol',
                        'negative','letters','drawing','need','rec',
                        'stemrev','simple','third','stemmod','respond',
                        'respeasy','restrict','oplength','opgrammer','oprepeat',
                        'wordrepeat','noun','opnumber','opdecimal','opmodify'];
                        for ($i=0; $i <7 ; $i++) { 
                            echo "<!-- START" . $comments[$i] . "-->";
                            ?>
                            <div class="body mb-3">
                                <label class="fw-semibold">
                                    <?php echo fn_lang($items[$i]); ?>
                                    <input class="checker" type="checkbox" name="<?php echo $programing[$i]; ?>" id="<?php echo $programing[$i]; ?>" checked hidden>
                                <!-- START <?php echo $comments[$i];?> BTN -->
                                    <div class="enhanced_bar rev active"
                                    style="
                                    display: inline-block;
                                    "></div>
                                </label>
                                <!-- END <?php echo $comments[$i];?> BTN-->
                                <!-- START <?php echo $comments[$i];?> CONTENT-->
                                <input type="text" name="" id="<?php echo $programing[$i]; ?>content"
                                class="form-control revinp"
                                value=""
                                    disabled hidden>
                                <!-- END <?php echo $comments[$i];?> CONTENT-->
                                <!-- START <?php echo $comments[$i];?> COMMENT -->
                                <div class="body mb-3">
                                    <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="<?php echo $programing[$i]; ?>check" id="<?php echo $programing[$i]; ?>check" hidden></textarea>
                                </div>
                                <!-- END <?php echo $comments[$i];?> COMMENT-->
                                <!-- START <?php echo $comments[$i];?> EXTRA NOTE -->
                                <div class="body mb-3">
                                    <?php echo fn_lang($items[$i]."EXTRA");?>
                                </div>
                                <!-- END <?php echo $comments[$i];?> EXTRA NOTE-->
                            </div>
                            <?php echo "<!-- END" . $comments[$i] ."-->";
                            }
                            ?>
                    </section>
                    <!-- END OVERALL SECTION -->
                    <!-- START STIMULUS (DRAWINGS) SECTION -->
                    <section class="stimulus">
                        <?php
                        for ($i=7; $i <10 ; $i++) { 
                            echo "<!-- START" . $comments[$i] . "-->";
                            ?>
                            <div class="body mb-3">
                                <label class="fw-semibold">
                                    <?php echo fn_lang($items[$i]); ?>
                                    <input class="checker" type="checkbox" name="<?php echo $programing[$i]; ?>" id="<?php echo $programing[$i]; ?>" checked hidden>
                                <!-- START <?php echo $comments[$i];?> BTN -->
                                    <div class="enhanced_bar rev active"
                                    style="
                                    display: inline-block;
                                    "></div>
                                </label>
                                <!-- END <?php echo $comments[$i];?> BTN-->
                                <!-- START <?php echo $comments[$i];?> CONTENT-->
                                <input type="text" name="" id="<?php echo $programing[$i]; ?>content"
                                class="form-control revinp"
                                value=""
                                    disabled hidden>
                                <!-- END <?php echo $comments[$i];?> CONTENT-->
                                <!-- START <?php echo $comments[$i];?> COMMENT -->
                                <div class="body mb-3">
                                    <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="<?php echo $programing[$i]; ?>check" id="<?php echo $programing[$i]; ?>check" hidden></textarea>
                                </div>
                                <!-- END <?php echo $comments[$i];?> COMMENT-->
                                <!-- START <?php echo $comments[$i];?> EXTRA NOTE -->
                                <div class="body mb-3">
                                    <?php echo fn_lang($items[$i]."EXTRA");?>
                                </div>
                                <!-- END <?php echo $comments[$i];?> EXTRA NOTE-->
                            </div>
                            <?php echo "<!-- END" . $comments[$i] ."-->";
                            }
                            ?>
                    </section>
                    <!-- END STIMULUS (DRAWINGS) SECTION -->
                    <!-- START STEM SECTION -->
                    <section class="stem">
                        <?php
                        for ($i=10; $i <14 ; $i++) { 
                            echo "<!-- START" . $comments[$i] . "-->";
                            ?>
                            <div class="body mb-3">
                                <label class="fw-semibold">
                                    <?php echo fn_lang($items[$i]); ?>
                                    <input class="checker" type="checkbox" name="<?php echo $programing[$i]; ?>" id="<?php echo $programing[$i]; ?>" checked hidden>
                                <!-- START <?php echo $comments[$i];?> BTN -->
                                    <div class="enhanced_bar rev active"
                                    style="
                                    display: inline-block;
                                    "></div>
                                </label>
                                <!-- END <?php echo $comments[$i];?> BTN-->
                                <!-- START <?php echo $comments[$i];?> CONTENT-->
                                <input type="text" name="" id="<?php echo $programing[$i]; ?>content"
                                class="form-control revinp"
                                value=""
                                    disabled hidden>
                                <!-- END <?php echo $comments[$i];?> CONTENT-->
                                <!-- START <?php echo $comments[$i];?> COMMENT -->
                                <div class="body mb-3">
                                    <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="<?php echo $programing[$i]; ?>check" id="<?php echo $programing[$i]; ?>check" hidden></textarea>
                                </div>
                                <!-- END <?php echo $comments[$i];?> COMMENT-->
                                <!-- START <?php echo $comments[$i];?> EXTRA NOTE -->
                                <div class="body mb-3">
                                    <?php echo fn_lang($items[$i]."EXTRA");?>
                                </div>
                                <!-- END <?php echo $comments[$i];?> EXTRA NOTE-->
                            </div>
                            <?php echo "<!-- END" . $comments[$i] ."-->";
                            }
                            ?>
                    </section>
                    <!-- END STEM SECTION -->
                    <!-- START OPTIONS SECTION -->
                    <section class="options">
                        <?php
                        for ($i=14; $i <25; $i++) { 
                            echo "<!-- START" . $comments[$i] . "-->";
                            ?>
                            <div class="body mb-3">
                                <label class="fw-semibold">
                                    <?php echo fn_lang($items[$i]); ?>
                                    <input class="checker" type="checkbox" name="<?php echo $programing[$i]; ?>" id="<?php echo $programing[$i]; ?>" checked hidden>
                                <!-- START <?php echo $comments[$i];?> BTN -->
                                    <div class="enhanced_bar rev active"
                                    style="
                                    display: inline-block;
                                    "></div>
                                </label>
                                <!-- END <?php echo $comments[$i];?> BTN-->
                                <!-- START <?php echo $comments[$i];?> CONTENT-->
                                <input type="text" name="" id="<?php echo $programing[$i]; ?>content"
                                class="form-control revinp"
                                value=""
                                    disabled hidden>
                                <!-- END <?php echo $comments[$i];?> CONTENT-->
                                <!-- START <?php echo $comments[$i];?> COMMENT -->
                                <div class="body mb-3">
                                    <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="<?php echo $programing[$i]; ?>check" id="<?php echo $programing[$i]; ?>check" hidden></textarea>
                                </div>
                                <!-- END <?php echo $comments[$i];?> COMMENT-->
                                <!-- START <?php echo $comments[$i];?> EXTRA NOTE -->
                                <div class="body mb-3">
                                    <?php echo fn_lang($items[$i]."EXTRA");?>
                                </div>
                                <!-- END <?php echo $comments[$i];?> EXTRA NOTE-->
                            </div>
                            <?php echo "<!-- END" . $comments[$i] ."-->";
                            }
                            ?>
                    </section>
                    <!-- END OPTIONS SECTION -->
                    <input type="submit" class="btn success m-3 Accept"name = "accept" value="<?php echo fn_lang("ACCEPT");?>">
                    <input type="submit" class="btn success_sec m-3 Report" name="report" value="<?php echo fn_lang("REPORT");?>">
                    <a href="?do=CancelRevision&Langset=<?php echo ($langset)?>&ReviewID=<?php echo $result['id'];?>&glass=<?php echo $glass;?>&QuestionID=<?php echo $DBEditrow['question_id'];?>"
                    class="btn cancel m-3">  <?php echo fn_lang("CANCEL");?></a>
                </form>
            </div>
            <?php
        
        }else{
            //New=0
            // Not a new question// this part is for rereview section
            //start study for question items
            // record date time of review
            //prepare DB file for edit 
           
            $DBreview=$con->prepare("UPDATE question_review
            SET
                review_date= :zreview_date
            WHERE
                id=:zrev_id
            ");
            $DBreview->execute(array(':zrev_id'=>$_GET['review_id'],
                ':zreview_date'=>date("Y-m-d H:i:s")));
            
            //prepare DB file for edit
            
            $DBfile=$con->prepare("SELECT * 
                FROM 
                    question_review 
                INNER JOIN
                    questions_create
                ON
                    questions_create.question_id = question_review.question_id                
                WHERE 
                    question_review.question_id=? 
                AND 
                    question_review.revisor_id=?
                ");
            $DBfile->execute(array($_GET['record'],$_GET['Revisor']));
            $result=$DBfile->fetch();
            //get Creator
            $DBQuestion=$con->prepare("SELECT * 
            FROM 
                teachers 
            WHERE 
                teacher_id=?
            ");
            $DBQuestion->execute(array($result['teacher_id']));
            $creator=$DBQuestion->fetch();
            //Get LEARNING OUTCOME
            $DBLO=fn_getRecordsV2("*", "learning_outcomes", "WHERE outcome_id=?", array($result['outcome_id']));
            $LO=$DBLO[0]['unit']." - ".$DBLO[0]['chapter']." - ".$DBLO[0]['item']." - ".$DBLO[0]['content'];
            $now= new Datetime();
            ?>
            <div class="question" style='width: 100%;margin:20px;padding:20px;'>
                <form action="?do=AcceptQuestion&Langset=<?php echo ($langset); ?>&Revisor=<?php echo $_SESSION['teacher_id'];?>&QuestionID=<?php echo $DBEditrow['question_id'];?>&ReviewID=<?php echo $result['id'];?>&RevisorQue=<?php echo $_GET['RevisorQue'];?>" method="POST" enctype="">
                    <!-- START Revision Page -->
                    <!-- START QUESTION BODY SECTION -->
                    <section class="question">
                        <div class="body mb-3">
                            <label for="writter" class="fw-semibold" style="diplay : block;width : 100%;"><?php echo fn_lang('STEMQ'); ?>
                            </label>
                            <?php echo $result['Question_Body'] ?>
                        </div>
                        <div class="body mb-3">
                            <?php echo ($result['supported_image']==1)
                            ?'<img src="' . $imgDir ."Questions/". $result['image_link'] . '" style="max-width: 40%; ">' 
                            : '' ?>
                        </div>
                        <div class="body mb-3">
                            <?php echo ($result['sub_stem']!="")
                            ? $result['sub_stem']
                            : '' ?>
                        </div>
                        <div class="body mb-3">
                            <?php echo ($result['substem_sup_img']==1)
                            ?'<img src="' . $imgDir ."Questions/". $result['substem_image_link'] . '" style="max-width: 40%;">' 
                            : '' ?>
                        </div>
                        <?php
                            $questionLabel = ["A", "B", "C", "D"];
                            for ($i = 1; $i < 5; $i++) {
                        ?>
                        <div class="body mb-3" dir="<?php echo ($langset == "Eng") ? "ltr" : "rtl"; ?>">
                            <span class="labels <?php echo($result['correct_answer']==$i) ? 'answer':'' ;?>"dir="<?php echo ($langset == "Eng") ? "ltr" : "rtl"; ?>"><?php echo $questionLabel[$i-1];?></span>
                            <span dir="<?php echo ($langset == "Eng") ? "ltr" : "rtl"; ?>">
                            <?php echo ($result['answer'.$i]!=="")
                            ?$result['answer'.$i]
                            : '' ?>
                            </span>
                            <?php if($result['ans'.$i.'_sup_img']==1){
                            echo '<span dir="'.($langset == "Eng") ? "ltr" : "rtl".'">';
                            '<img src="' . $imgDir ."Questions/". $result['ans'.$i.'_image_link'] 
                            ."</span>";
                            }?>
                        </div>
                        <div class="explained" style="margin-bottom : 20px;">
                            <span style="font-weight : bold;">
                                <?php echo fn_lang('EXPLAIN');?> : 
                            </span>
                            <span>
                            <?php echo $result['ans'.$i.'_explain']; ?>
                            </span>
                        </div>
                        <?php
                        }
                        ?>
                    </section>
                    <!-- END QUESTION BODY SECTION-->
                    <!-- START QUESTION DATA SECTION -->
                    <section class="data">
                        <!-- START WRITTER -->
                        <div class="body mb-3">
                            <label for="writter" class="fw-semibold"><?php echo fn_lang('WRITTER'); ?>
                            </label>
                            <input type="text" id="writter"
                                class="form-control"
                                value="<?php echo $creator['ename'] ?>"
                                disabled>
                        </div>
                        <!-- END WRITTER -->
                        <!-- START CREATEDATE -->
                        <div class="body mb-3">
                            <label for="createdate" class="fw-semibold"><?php echo fn_lang('DATEWRITTEN'); ?>
                            </label>
                            <input type="text" id="creatdate"
                                class="form-control"
                                value="<?php echo $result['creation_date'] ?>"
                                disabled>
                        </div>
                        <!-- END CREATEDATE -->
                        <!-- START SUBJECT -->
                        <div class="body mb-3">
                            <label for="subject" class="fw-semibold"><?php echo fn_lang('SUBJECT'); ?>
                            </label>
                            <input type="text" id="subject"
                                class="form-control"
                                value="<?php echo $result['subject'] ?>"
                                disabled>
                        </div>
                        <!-- END SUBJECT -->
                        <!-- START PALIGRISM -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('PALGRISM'); ?>
                            </label>
                            <p>
                            <?php echo fn_lang('PALGRISMTALK'); ?>
                            </p>
                            <!-- START PALIGRISM CONTENT-->
                            <input type="text" name="palcontent" id="palcontent"
                            class="form-control"
                            value="<?php echo ($result['plagiarism_percent']=="")? "0%":$result['plagiarism_percent'] ;?>">
                            <!-- END PALIGRISM CONTENT-->
                        </div>
                        <!-- END PALIGRISM -->
                    </section>
                    <!-- END QUESTION DATA SECTION -->
                    <!-- START CONTENT SUITABILITY SECTION -->
                    <section class="outcome">
                        <!-- START LO -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('LO'); ?>
                                <input class="checker" type="checkbox" name="lo" id="lo" 
                                <?php echo ($result['content_aligned']==0)? "checked":"";?> hidden>
                            <!-- START LO BTN -->
                                <div class="enhanced_bar rev <?php echo ($result['content_aligned']==0)? "active":"";?>"
                                style="
                                display: inline-block;
                                "></div>
                            </label>
                            <!-- END LO BTN-->
                            <!-- START LO CONTENT-->
                            <input type="text" name="locontent" id="locontent"
                            class="form-control revinp"
                            value="<?php echo $LO;?>"
                            <?php if($result['content_aligned'] == 1){
                                echo "style='background-color: rgba(255, 0, 0, 0.3);'";
                            };?>
                                disabled>
                            <!-- END LO CONTENT-->
                            <!-- START LO COMMENT -->
                            <div class="body mb-3">
                                <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" 
                                name="locheck" id="locheck"<?php echo($result['content_aligned'] == 0)? "hidden":"";?>><?php echo $result['content_aligned_note'];?></textarea>
                            </div>
                            <!-- END LO COMMENT-->
                        </div>
                        <!-- END LO -->
                        <!-- START DOK  -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('DOK'); ?>
                                <input class="checker" type="checkbox" name="revdok" id="revdok" 
                                <?php echo ($result['11']==0)? "checked":"";//11 for replacing dok_review ?> hidden>
                            <!-- START DOK BTN -->
                                <div class="enhanced_bar rev <?php echo ($result['11']==0)? "active":"";//'11' replacement for dok_review DB?>"
                                style="display: inline-block;"></div>
                            </label>
                            <!-- END DOK BTN-->
                            <!-- START DOK CONTENT-->                            
                            <input type="text" name="dokcontent" id="dokcontent"
                            class="form-control revinp"
                            value="<?php echo fn_lang($result['depth']);?>"
                            <?php if($result['11'] == 1){//'11' replacement for dok_review DB
                                echo "style='background-color: rgba(255, 0, 0, 0.3);'";
                            };?>
                                disabled>
                            <!-- END DOK CONTENT-->
                            <!-- START DOK COMMENT -->
                            <div class="body mb-3">
                                <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="dokcheck" id="dokcheck" <?php echo($result['11'] == 0)? "hidden":"";?>><?php echo $result['dok_note'];?></textarea>
                            </div>
                            <!-- END DOK COMMENT-->
                            <!-- START DOK EXTRA NOTE -->
                            <div class="body mb-3">
                                <?php echo fn_lang("DOKEXTRA");?>
                            </div>
                            <!-- END DOK EXTRA NOTE-->
                        </div>
                        <!-- END DOK  -->
                        <!-- START DOK (DEPTH) -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('DOKPROFF'); ?>
                                <input class="checker" type="checkbox" name="dokproff" id="dokproff" 
                                <?php echo ($result['dok_proff']==0)? "checked":"";?> hidden>
                            <!-- START DOK(DEPTH) BTN -->
                                <div class="enhanced_bar rev <?php echo ($result['dok_proff']==0)? "active":"";?>"
                                style="
                                display: inline-block;
                                "></div>
                            </label>
                            <!-- END DOK(DEPTH) BTN-->
                            <!-- START DOK(DEPTH) CONTENT-->
                            <input type="text" name="dokproffcontent" id="dokproffcontent"
                            class="form-control revinp"
                            value="<?php echo $result['dok'];?>"
                            <?php if($result['dok_proff'] == 1){
                                echo "style='background-color: rgba(255, 0, 0, 0.3);'";
                            };?>
                                disabled>
                            <!-- END DOK(DEPTH) CONTENT-->
                            <!-- START DOK(DEPTH) COMMENT -->
                            <div class="body mb-3">
                                <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="dokproffcheck" id="dokproffcheck" <?php echo($result['dok_proff'] == 0)? "hidden":"";?>><?php echo $result['dok_proff_note'];?></textarea>
                            </div>
                            <!-- END DOK(DEPTH) COMMENT-->
                            <!-- START DOK(DEPTH) EXTRA NOTE -->
                            <div class="body mb-3">
                                <?php echo fn_lang("DOKPROFFEXTRA");?>
                            </div>
                            <!-- END DOK(DEPTH) EXTRA NOTE-->
                        </div>
                        <!-- END DOK (DEPTH) -->
                        
                        <!-- START REFERENCE -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('REFSTATE'); ?>
                                <input class="checker" type="checkbox" name="refstatus" id="refstatus" 
                                <?php echo ($result['15']==0)? "checked":"";//represents reference in review?> hidden>
                            <!-- START REFERENCE BTN -->
                                <div class="enhanced_bar rev <?php echo ($result['15']==0)? "active":"";?>"
                                style="
                                display: inline-block;
                                "></div>
                            </label>
                            <!-- END REFERENCE BTN-->
                            <!-- START REFERENCE CONTENT-->
                            <input type="text" name="refcontent" id="refcontent"
                            class="form-control revinp"
                            value="<?php if ($result['reference']=='REF'){
                                echo $result['reference'].'/ '.$result['ref_txt'];
                                } else if($result['reference']=='NA') {
                                    echo 'Brand New / No reference name';
                                }else {
                                    echo $result['reference'];
                                }?>"
                            <?php if($result['15'] == 1){
                                echo "style='background-color: rgba(255, 0, 0, 0.3);'";
                            };?>
                                disabled>
                            <!-- END REFERENCE CONTENT-->
                            <!-- START REFERENCE COMMENT -->
                            <div class="body mb-3">
                                <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="refcheck" id="refcheck" <?php echo($result['15'] == 0)? "hidden":"";?>><?php echo $result['reference_note'];?></textarea>
                            </div>
                            <!-- END REFERENCE COMMENT-->
                            <!-- START REFERENCE EXTRA NOTE -->
                            <div class="body mb-3">
                                <?php echo fn_lang("REFSTATEEXTRA");?>
                            </div>
                            <!-- END REFERENCE EXTRA NOTE-->
                        </div>
                        <!-- END REFERENCE -->                        
                        <!-- START ERROR -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('ERROR'); ?>
                                <input class="checker" type="checkbox" name="err" id="err" 
                                <?php echo ($result['errors']==0)? "checked":"";?> hidden>
                            <!-- START ERROR BTN -->
                                <div class="enhanced_bar rev <?php echo ($result['errors']==0)? "active":"";?>"
                                style="
                                display: inline-block;
                                "></div>
                            </label>
                            <!-- END ERROR BTN-->
                            <!-- START ERROR CONTENT-->
                            <input type="text"  id="errcontent"
                            class="form-control revinp"
                            value=""disabled hidden>
                            <!-- END ERROR CONTENT-->
                            <!-- START ERROR COMMENT -->
                            <div class="body mb-3">
                                <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="errcheck" id="errcheck" <?php echo($result['errors'] == 0)? "hidden":"";?>><?php echo $result['errors_note'];?></textarea>
                            </div>
                            <!-- END ERROR COMMENT-->
                            
                        </div>
                        <!-- END ERROR -->
                        <!-- START KEY -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('KEY'); ?>
                                <input class="checker" type="checkbox" name="key" id="key"  
                                <?php echo ($result['key_good']==0)? "checked":"";?> hidden>
                            <!-- START KEY BTN -->
                                <div class="enhanced_bar rev <?php echo ($result['key_good']==0)? "active":"";?>"
                                style="
                                display: inline-block;
                                "></div>
                            </label>
                            <!-- END KEY BTN-->
                            <!-- START KEY CONTENT-->
                            <input type="text"  id="keycontent"
                            class="form-control revinp"
                            value=""
                                disabled hidden>
                            <!-- END KEY CONTENT-->
                            <!-- START KEY COMMENT -->
                            <div class="body mb-3">
                                <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="keycheck" id="keycheck"  <?php echo($result['key_good'] == 0)? "hidden":"";?>><?php echo $result['key_note'];?></textarea>
                            </div>
                            <!-- END KEY COMMENT-->
                            <!-- START DOK EXTRA NOTE -->
                            <div class="body mb-3">
                                <?php echo fn_lang("KEYEXTRA");?>
                            </div>
                            <!-- END DOK EXTRA NOTE-->
                        </div>
                        <!-- END KEY -->
                        <!-- START IDENTIFIER -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('IDENTIFIER'); ?>
                                <input class="checker" type="checkbox" name="identifier" id="identifier"   
                                <?php echo ($result['identifier']==0)? "checked":"";?> hidden>
                            <!-- START IDENTIFIER BTN -->
                                <div class="enhanced_bar rev <?php echo ($result['identifier']==0)? "active":"";?>"
                                style="
                                display: inline-block;
                                "></div>
                            </label>
                            <!-- END IDENTIFIER BTN-->
                            <!-- START IDENTIFIER CONTENT-->
                            <input type="text"  id="identifiercontent"
                            class="form-control revinp"
                            value=""
                                disabled hidden>
                            <!-- END IDENTIFIER CONTENT-->
                            <!-- START IDENTIFIER COMMENT -->
                            <div class="body mb-3">
                                <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="identifiercheck" id="identifiercheck"  <?php echo($result['identifier'] == 0)? "hidden":"";?>><?php echo $result['identifier_note'];?></textarea>
                            </div>
                            <!-- END IDENTIFIER COMMENT-->
                            
                        </div>
                        <!-- END IDENTIFIER -->
                        <!-- START IDENTIFIERONLY -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('IDENTIFIERONLY'); ?>
                                <input class="checker" type="checkbox" name="identifier_only" id="identifier_only"  
                                <?php echo ($result['identifier_only']==0)? "checked":"";?> hidden>
                            <!-- START IDENTIFIERONLY BTN -->
                                <div class="enhanced_bar rev <?php echo ($result['identifier_only']==0)? "active":"";?>"
                                style="
                                display: inline-block;
                                "></div>
                            </label>
                            <!-- END IDENTIFIERONLY BTN-->
                            <!-- START IDENTIFIERONLY CONTENT-->
                            <input type="text"  id="identifieronlycontent"
                            class="form-control revinp"
                            value=""
                                disabled hidden>
                            <!-- END IDENTIFIERONLY CONTENT-->
                            <!-- START IDENTIFIERONLY COMMENT -->
                            <div class="body mb-3">
                                <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="identifieronlycheck" id="identifieronlycheck" <?php echo($result['identifier_only'] == 0)? "hidden":"";?>><?php echo $result['identifier_only_note'];?></textarea>
                            </div>
                            <!-- END IDENTIFIERONLY COMMENT-->
                            
                        </div>
                        <!-- END IDENTIFIERONLY -->
                        <!-- START DISTRACTORS -->
                        <div class="body mb-3">
                            <label class="fw-semibold">
                                <?php echo fn_lang('DISTRACTORS'); ?>
                                <input class="checker" type="checkbox" name="distract" id="distract"    
                                <?php echo ($result['distractors']==0)? "checked":"";?> hidden>
                            <!-- START DISTRACTORS BTN -->
                                <div class="enhanced_bar rev <?php echo ($result['distractors']==0)? "active":"";?>"
                                style="
                                display: inline-block;
                                "></div>
                            </label>
                            <!-- END DISTRACTORS BTN-->
                            <!-- START DISTRACTORS CONTENT-->
                            <input type="text"  id="distractcontent"
                            class="form-control revinp"
                            value=""
                                disabled hidden>
                            <!-- END DISTRACTORS CONTENT-->
                            <!-- START DISTRACTORS COMMENT -->
                            <div class="body mb-3">
                                <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="distractcheck" id="distractcheck" <?php echo($result['distractors'] == 0)? "hidden":"";?>><?php echo $result['distractors_note'];?></textarea>
                            </div>
                            <!-- END DISTRACTORS COMMENT-->
                            <!-- START DISTRACTORS EXTRA NOTE -->
                            <div class="body mb-3">
                                <?php echo fn_lang("DISTEXTRA");?>
                            </div>
                            <!-- END DISTRACTORS EXTRA NOTE-->
                        </div>
                        <!-- END DISTRACTORS -->
                    </section>
                    <!-- END CONTENT SUITABILITY SECTION -->
                    <!-- START OVERALL SECTION -->
                    <section class="overall">
                        <?php
                        $comments=['LANG','NEW QUESTION','GRAMMER','ABBREVIATION','SYMBOL',
                        'NEGATIVE','LETTERS','DRAWING','NEEDED DRAWING','RECOMMENDED ALTERNATIVES',
                        'STEM END OF QUESTION','SIMPLE PHRASE','THIRD PERSON','STEM MODIFY','RESPONDS',
                        'RESPONDS EASY TO UNDERSTAND','RESTRICT UNWANTED OPTIONS','OPLENGTH','OPGRA','OPREAP',
                        'WORDREAPEAT','NOUN','OPTION NUMBERS','OPTION DECIMALS','OPTION MODIFY'];
                        $items=['LANG','NEWQ','GRAMMER','ABBREV','SYMBOL',
                        'NEGATIVE','LETTERS','DRAWING','NEED','REC',
                        'STEMREV','SIMPLE','THIRD','STEMMOD','RESPOND',
                        'RESPEAS','RESTRICT','OPLENGTH','OPGRA','OPREAP',
                        'WORDREAPEAT','NOUN','OPNUM','OPDEC','OPMOD'];
                        $programing=['lang','newq','grammer','abbrev','symbol',
                        'negative','letters','drawing','need','rec',
                        'stemrev','simple','third','stemmod','respond',
                        'respeasy','restrict','oplength','opgrammer','oprepeat',
                        'wordrepeat','noun','opnumber','opdecimal','opmodify'];
                        $dbcalls=['rev_lang','replicant','grammer','abbreviation','symbols',
                        'Negative','letters','stimulus_adjust','stimulus_needed','stimulus_modification',
                        'stem_end','stem_good','stem_third','stem_modify','response_relation',
                        'response_easy','all_above','response_length','response_grammer','response_repeat',
                        'Words_special','nouns','response_numerical','response_numerical_decimal','response_modify'];
                        for ($i=0; $i <7 ; $i++) { 
                            echo "<!-- START" . $comments[$i] . "-->";
                            ?>
                            <div class="body mb-3">
                                <label class="fw-semibold">
                                    <?php echo fn_lang($items[$i]); ?>
                                    <input class="checker" type="checkbox" name="<?php echo $programing[$i]; ?>" id="<?php echo $programing[$i]; ?>"    
                                <?php echo ($result[$dbcalls[$i]]==0)? "checked":"";?> hidden>
                                <!-- START <?php echo $comments[$i];?> BTN -->
                                    <div class="enhanced_bar rev <?php echo ($result[$dbcalls[$i]]==0)? "active":"";?>"
                                    style="
                                    display: inline-block;
                                    "></div>
                                </label>
                                <!-- END <?php echo $comments[$i];?> BTN-->
                                <!-- START <?php echo $comments[$i];?> CONTENT-->
                                <input type="text" name="" id="<?php echo $programing[$i]; ?>content"
                                class="form-control revinp"
                                value=""
                                    disabled hidden>
                                <!-- END <?php echo $comments[$i];?> CONTENT-->
                                <!-- START <?php echo $comments[$i];?> COMMENT -->
                                <div class="body mb-3">
                                    <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="<?php echo $programing[$i]; ?>check" id="<?php echo $programing[$i]; ?>check" <?php echo($result[$dbcalls[$i]] == 0)? "hidden":"";?>><?php echo $result[$dbcalls[$i]."_note"];?></textarea>
                                </div>
                                <!-- END <?php echo $comments[$i];?> COMMENT-->
                                <!-- START <?php echo $comments[$i];?> EXTRA NOTE -->
                                <div class="body mb-3">
                                    <?php echo fn_lang($items[$i]."EXTRA");?>
                                </div>
                                <!-- END <?php echo $comments[$i];?> EXTRA NOTE-->
                            </div>
                            <?php echo "<!-- END" . $comments[$i] ."-->";
                            }
                            ?>
                    </section>
                    <!-- END OVERALL SECTION -->
                    <!-- START STIMULUS (DRAWINGS) SECTION -->
                    <section class="stimulus">
                        <?php
                        for ($i=7; $i <10 ; $i++) { 
                            echo "<!-- START" . $comments[$i] . "-->";
                            ?>
                            <div class="body mb-3">
                                <label class="fw-semibold">
                                    <?php echo fn_lang($items[$i]); ?>
                                    <input class="checker" type="checkbox" name="<?php echo $programing[$i]; ?>" id="<?php echo $programing[$i]; ?>"    
                                    <?php echo ($result[$dbcalls[$i]]==0)? "checked":"";?> hidden>
                                <!-- START <?php echo $comments[$i];?> BTN -->
                                    <div class="enhanced_bar rev <?php echo ($result[$dbcalls[$i]]==0)? "active":"";?>"
                                    style="
                                    display: inline-block;
                                    "></div>
                                </label>
                                <!-- END <?php echo $comments[$i];?> BTN-->
                                <!-- START <?php echo $comments[$i];?> CONTENT-->
                                <input type="text" name="" id="<?php echo $programing[$i]; ?>content"
                                class="form-control revinp"
                                value=""
                                    disabled hidden>
                                <!-- END <?php echo $comments[$i];?> CONTENT-->
                                <!-- START <?php echo $comments[$i];?> COMMENT -->
                                <div class="body mb-3">
                                    <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="<?php echo $programing[$i]; ?>check" id="<?php echo $programing[$i]; ?>check" <?php echo($result[$dbcalls[$i]] == 0)? "hidden":"";?>><?php echo $result[$dbcalls[$i]."_note"];?></textarea>
                                </div>
                                <!-- END <?php echo $comments[$i];?> COMMENT-->
                                <!-- START <?php echo $comments[$i];?> EXTRA NOTE -->
                                <div class="body mb-3">
                                    <?php echo fn_lang($items[$i]."EXTRA");?>
                                </div>
                                <!-- END <?php echo $comments[$i];?> EXTRA NOTE-->
                            </div>
                            <?php echo "<!-- END" . $comments[$i] ."-->";
                            }
                            ?>
                    </section>
                    <!-- END STIMULUS (DRAWINGS) SECTION -->
                    <!-- START STEM SECTION -->
                    <section class="stem">
                        <?php
                        for ($i=10; $i <14 ; $i++) { 
                            echo "<!-- START" . $comments[$i] . "-->";
                            ?>
                            <div class="body mb-3">
                                <label class="fw-semibold">
                                    <?php echo fn_lang($items[$i]); ?>
                                    <input class="checker" type="checkbox" name="<?php echo $programing[$i]; ?>" id="<?php echo $programing[$i]; ?>"    
                                    <?php echo ($result[$dbcalls[$i]]==0)? "checked":"";?> hidden>
                                <!-- START <?php echo $comments[$i];?> BTN -->
                                    <div class="enhanced_bar rev <?php echo ($result[$dbcalls[$i]]==0)? "active":"";?>"
                                    style="
                                    display: inline-block;
                                    "></div>
                                </label>
                                <!-- END <?php echo $comments[$i];?> BTN-->
                                <!-- START <?php echo $comments[$i];?> CONTENT-->
                                <input type="text" name="" id="<?php echo $programing[$i]; ?>content"
                                class="form-control revinp"
                                value=""
                                    disabled hidden>
                                <!-- END <?php echo $comments[$i];?> CONTENT-->
                                <!-- START <?php echo $comments[$i];?> COMMENT -->
                                <div class="body mb-3">
                                    <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="<?php echo $programing[$i]; ?>check" id="<?php echo $programing[$i]; ?>check" <?php echo($result[$dbcalls[$i]] == 0)? "hidden":"";?>><?php echo $result[$dbcalls[$i]."_note"];?></textarea>
                                </div>
                                <!-- END <?php echo $comments[$i];?> COMMENT-->
                                <!-- START <?php echo $comments[$i];?> EXTRA NOTE -->
                                <div class="body mb-3">
                                    <?php echo fn_lang($items[$i]."EXTRA");?>
                                </div>
                                <!-- END <?php echo $comments[$i];?> EXTRA NOTE-->
                            </div>
                            <?php echo "<!-- END" . $comments[$i] ."-->";
                            }
                            ?>
                    </section>
                    <!-- END STEM SECTION -->
                    <!-- START OPTIONS SECTION -->
                    <section class="options">
                        <?php
                        for ($i=14; $i <25 ; $i++) { 
                            echo "<!-- START" . $comments[$i] . "-->";
                            ?>
                            <div class="body mb-3">
                                <label class="fw-semibold">
                                    <?php echo fn_lang($items[$i]); ?>
                                    <input class="checker" type="checkbox" name="<?php echo $programing[$i]; ?>" id="<?php echo $programing[$i]; ?>"    
                                    <?php echo ($result[$dbcalls[$i]]==0)? "checked":"";?> hidden>
                                <!-- START <?php echo $comments[$i];?> BTN -->
                                    <div class="enhanced_bar rev <?php echo ($result[$dbcalls[$i]]==0)? "active":"";?>"
                                    style="
                                    display: inline-block;
                                    "></div>
                                </label>
                                <!-- END <?php echo $comments[$i];?> BTN-->
                                <!-- START <?php echo $comments[$i];?> CONTENT-->
                                <input type="text" name="" id="<?php echo $programing[$i]; ?>content"
                                class="form-control revinp"
                                value=""
                                    disabled hidden>
                                <!-- END <?php echo $comments[$i];?> CONTENT-->
                                <!-- START <?php echo $comments[$i];?> COMMENT -->
                                <div class="body mb-3">
                                    <textarea placeholder="<?php echo fn_lang("REVCAUSE");?>" class="form-control" name="<?php echo $programing[$i]; ?>check" id="<?php echo $programing[$i]; ?>check" <?php echo($result[$dbcalls[$i]] == 0)? "hidden":"";?>><?php echo $result[$dbcalls[$i]."_note"];?></textarea>
                                </div>
                                <!-- END <?php echo $comments[$i];?> COMMENT-->
                                <!-- START <?php echo $comments[$i];?> EXTRA NOTE -->
                                <div class="body mb-3">
                                    <?php echo fn_lang($items[$i]."EXTRA");?>
                                </div>
                                <!-- END <?php echo $comments[$i];?> EXTRA NOTE-->
                            </div>
                            <?php echo "<!-- END" . $comments[$i] ."-->";
                            }
                            ?>
                    </section>
                    <!-- END OPTIONS SECTION -->
                    <input type="submit" class="btn success m-3 Accept"name = "accept" value="<?php echo fn_lang("ACCEPT");?>">
                    <input type="submit" class="btn success_sec m-3 Report" name="report" value="<?php echo fn_lang("REPORT");?>">
                    <a href="?do=Dash&Langset=<?php echo ($langset) ?>" class="btn cancel m-3">  <?php echo fn_lang("CANCEL");?></a>
                    <!-- END Revision Page -->
                </form>
            </div>
            <!-- END Revision Page -->
            <?php
        }
        ?>
</div>
        <?php
    break;
case 'CancelRevision':
    // return file and return to cart
    $glass=$_GET['glass'];
    $DBQCreate=$con->prepare("UPDATE questions_create SET $glass=? WHERE question_id=?");
    $DBQCreate->execute(array(1,$_GET['QuestionID']));
    // DElete the review file
    $DBRevFile=$con->prepare("DELETE FROM question_review WHERE id=?");
    $DBRevFile->execute(array($_GET['ReviewID']));
    echo '<div class="container">';
    fn_redirect("Revision Cancelled", "danger", 1, "?do=Dash&Langset=".$langset);
    echo '</div>';
    break;
case 'AcceptQuestion':
    //check calling at first
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        //check Report btn is pushed
        //prepare for Emails
        $DPO=$con->prepare("SELECT * FROM question_review WHERE id = ?");
        $DPO->execute(array($_GET['ReviewID']));
        $review=$DPO->fetch();
        $DPO=$con->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
        $DPO->execute(array($review['teacher_id']));
        $emailCreator=$DPO->fetch();
        $DPO=$con->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
        $DPO->execute(array($_SESSION['teacher_id']));
        $emailRevisor=$DPO->fetch();
        if(isset($_POST['report'])){
            //Report for the review and rereview
            // Prepare Data Before DB
            // Calculate time of revise
            $now = new Datetime();
            $temp= $con->prepare("SELECT * FROM question_review WHERE id=?");
            $temp->execute(array($_GET['ReviewID']));
            $origin=$temp->fetch();//get this record starting date review
            $revDate=new Datetime($origin['review_date']);
            if($origin['revisor_time']!=NULL){
                list($H,$M,$S)=explode(":",$origin['revisor_time']);
                $orginToTime=intval($H)*3600 + intval($M)*60 + intval($S);
            }
            $diff=$revDate->diff($now);
            $toTime=$diff->d*24*60*60 + $diff->h*60*60 + $diff->i*60 + $diff->s;
            
            //  Database adjustments
            $reportArray = array(
                //review Q ID
                ':zid' => intval($_GET['ReviewID']), 
                // prepare to send to creator
                ':zreport_status' => 2, 
                // Start collecting data
                ':zlosuitable' => isset($_POST['lo']) ? 0: 1,
                ':zlosuitable_comment' => isset($_POST['locheck']) ? $_POST['locheck'] : "",
                ':zpalegrism' => isset($_POST['palcontent']) ? $_POST['palcontent'] : "",
                ':zdok' => isset($_POST['revdok']) ? 0 : 1,
                ':zdok_comment' => isset($_POST['dokcheck']) ? $_POST['dokcheck'] : "",
                ':zdokproff' => isset($_POST['dokproff']) ? 0 : 1,
                ':zdokproff_comment' => isset($_POST['dokproffcheck']) ? $_POST['dokproffcheck'] : "",
                ':zreference' => isset($_POST['refstatus']) ? 0 : 1,
                ':zreference_comment' => isset($_POST['refcheck']) ? $_POST['refcheck'] : "",
                ':zerr' => isset($_POST['err']) ? 0 : 1,
                ':zerr_comment' => isset($_POST['errcheck']) ? $_POST['errcheck'] : "",
                ':zkey' => isset($_POST['key']) ? 0 : 1,
                ':zkey_comment' => isset($_POST['keycheck']) ? $_POST['keycheck'] : "",
                ':zidentifier' => isset($_POST['identifier']) ? 0 : 1,
                ':zidentifier_comment' => isset($_POST['identifiercheck']) ? $_POST['identifiercheck'] : "",
                ':zidentifieronly' => isset($_POST['identifier_only']) ? 0 : 1,
                ':zidentifieronly_comment' => isset($_POST['identifieronlycheck']) ? $_POST['identifieronlycheck'] : "",
                ':zdistractors' => isset($_POST['distract']) ? 0 : 1,
                ':zdistractors_comment' => isset($_POST['distractcheck']) ? $_POST['distractcheck'] : "",
                ':zlang' => isset($_POST['lang']) ? 0 : 1,
                ':zlang_comment' => isset($_POST['langcheck']) ? $_POST['langcheck'] : "",
                ':znewq' => isset($_POST['newq']) ? 0 : 1,
                ':znewq_comment' => isset($_POST['newqcheck']) ? $_POST['newqcheck'] : "",
                // 20 items
                ':zgrammer' => isset($_POST['grammer']) ? 0 : 1,
                ':zgrammer_comment' => isset($_POST['grammercheck']) ? $_POST['grammercheck'] : "",
                ':zabbrev' => isset($_POST['abbrev']) ? 0 : 1,
                ':zabbrev_comment' => isset($_POST['abbrevcheck']) ? $_POST['abbrevcheck'] : "",
                ':zsymbol' => isset($_POST['symbol']) ? 0 : 1,
                ':zsymbol_comment' => isset($_POST['symbolcheck']) ? $_POST['symbolcheck'] : "",
                ':znegative' => isset($_POST['negative']) ? 0 : 1,
                ':znegative_comment' => isset($_POST['negativecheck']) ? $_POST['negativecheck'] : "",
                ':zletters' => isset($_POST['letters']) ? 0: 1,
                ':zletters_comment' => isset($_POST['letterscheck']) ? $_POST['letterscheck'] : "",
                ':zneed' => isset($_POST['need']) ? 0 : 1,
                ':zneed_comment' => isset($_POST['needcheck']) ? $_POST['needcheck'] : "",
                ':zstimadj' => isset($_POST['drawing']) ? 0 : 1,
                ':zstimadj_comment' => isset($_POST['drawingcheck']) ? $_POST['drawingcheck'] : "",
                ':zrec' => isset($_POST['rec']) ? 0 : 1,
                ':zrec_comment' => isset($_POST['reccheck']) ? $_POST['reccheck'] : "",
                ':zstemrev' => isset($_POST['stemrev']) ? 0: 1,
                ':zstemrev_comment' => isset($_POST['stemrevcheck']) ? $_POST['stemrevcheck'] : "",
                ':zsimple' => isset($_POST['simple']) ? 0 : 1,
                ':zsimple_comment' => isset($_POST['simplecheck']) ? $_POST['simplecheck'] : "",
                ':zthird' => isset($_POST['third']) ? 0 : 1,
                ':zthird_comment' => isset($_POST['thirdcheck']) ? $_POST['thirdcheck'] : "",
                // 40 items
                ':zstemmod' => isset($_POST['stemmod']) ? 0 : 1,
                ':zstemmod_comment' => isset($_POST['stemmodcheck']) ? $_POST['stemmodcheck'] : "",
                ':zrespond' => isset($_POST['respond']) ? 0 : 1,
                ':zrespond_comment' => isset($_POST['respondcheck']) ? $_POST['respondcheck'] : "",
                ':zrespeasy' => isset($_POST['respeasy']) ? 0 : 1,
                ':zrespeasy_comment' => isset($_POST['respeasycheck']) ? $_POST['respeasycheck'] : "",
                ':zrestrict' => isset($_POST['restrict']) ? 0 : 1,
                ':zrestrict_comment' => isset($_POST['restrictcheck']) ? $_POST['restrictcheck'] : "",
                ':zoplength' => isset($_POST['oplength']) ? 0 : 1,
                ':zoplength_comment' => isset($_POST['oplengthcheck']) ? $_POST['oplengthcheck'] : "",
                ':zopgrammer' => isset($_POST['opgrammer']) ? 0 : 1,
                ':zopgrammer_comment' => isset($_POST['opgrammercheck']) ? $_POST['opgrammercheck'] : "",
                ':zoprepeat' => isset($_POST['oprepeat']) ? 0 : 1,
                ':zoprepeat_comment' => isset($_POST['oprepeatcheck']) ? $_POST['oprepeatcheck'] : "",
                ':zwordrepeat' => isset($_POST['wordrepeat']) ? 0 : 1,
                ':zwordrepeat_comment' => isset($_POST['wordrepeatcheck']) ? $_POST['wordrepeatcheck'] : "",
                ':znoun' => isset($_POST['noun']) ? 0 : 1,
                ':znoun_comment' => isset($_POST['nouncheck']) ? $_POST['nouncheck'] : "",
                ':zopnumber' => isset($_POST['opnumber']) ? 0 : 1,
                ':zopnumber_comment' => isset($_POST['opnumbercheck']) ? $_POST['opnumbercheck'] : "",
                ':zopdecimal' => isset($_POST['opdecimal']) ? 0 : 1,
                ':zopdecimal_comment' => isset($_POST['opdecimalcheck']) ? $_POST['opdecimalcheck'] : "",
                ':zopmodify' => isset($_POST['opmodify']) ? 0 : 1,
                ':zopmodify_comment' => isset($_POST['opmodifycheck']) ? $_POST['opmodifycheck'] : "",
                ':zrevisor_time' => ($origin['revisor_time'] != NULL) ? gmdate("H:i:s",$orginToTime + $toTime) : gmdate("H:i:s",$toTime)
            );
            
            $DBO=$con->prepare("UPDATE 
                    question_review 
                SET review_status=:zreport_status,
                    content_aligned=:zlosuitable,
                    content_aligned_note=:zlosuitable_comment,
                    plagiarism_percent=:zpalegrism,
                    dok=:zdok,
                    dok_note=:zdok_comment,
                    dok_proff=:zdokproff,
                    dok_proff_note=:zdokproff_comment,
                    reference=:zreference,
                    reference_note=:zreference_comment,
                    errors=:zerr,
                    errors_note=:zerr_comment,
                    key_good=:zkey,
                    key_note=:zkey_comment,
                    identifier=:zidentifier,
                    identifier_note=:zidentifier_comment,
                    identifier_only=:zidentifieronly,
                    identifier_only_note=:zidentifieronly_comment,
                    distractors=:zdistractors,
                    distractors_note=:zdistractors_comment,
                    rev_lang=:zlang,
                    rev_lang_note=:zlang_comment,
                    replicant=:znewq,
                    replicant_note=:znewq_comment,
                    grammer=:zgrammer,
                    grammer_note=:zgrammer_comment,
                    abbreviation=:zabbrev,
                    abbreviation_note=:zabbrev_comment,
                    symbols=:zsymbol,
                    symbols_note=:zsymbol_comment,
                    Negative=:znegative,
                    Negative_note=:znegative_comment,
                    letters=:zletters,
                    letters_note=:zletters_comment,
                    stimulus_needed=:zneed,
                    stimulus_needed_note=:zneed_comment,
                    stimulus_adjust=:zstimadj,
                    stimulus_adjust_note=:zstimadj_comment,
                    stimulus_modification=:zrec,
                    stimulus_modification_note=:zrec_comment,
                    stem_end=:zstemrev,
                    stem_end_note=:zstemrev_comment,
                    stem_good=:zsimple,
                    stem_good_note=:zsimple_comment,
                    stem_third=:zthird,
                    stem_third_note=:zthird_comment,
                    stem_modify=:zstemmod,
                    stem_modify_note=:zstemmod_comment,
                    response_relation=:zrespond,
                    response_relation_note=:zrespond_comment,
                    response_easy=:zrespeasy,
                    response_easy_note=:zrespeasy_comment,
                    all_above=:zrestrict,
                    all_above_note=:zrestrict_comment,
                    response_length=:zoplength,
                    response_length_note=:zoplength_comment,
                    response_grammer=:zopgrammer,
                    response_grammer_note=:zopgrammer_comment,
                    response_repeat=:zoprepeat,
                    response_repeat_note=:zoprepeat_comment,
                    Words_special=:zwordrepeat,
                    Words_special_note=:zwordrepeat_comment,
                    nouns=:znoun,
                    nouns_note=:znoun_comment,
                    response_numerical=:zopnumber,
                    response_numerical_note=:zopnumber_comment,
                    response_numerical_decimal=:zopdecimal,
                    response_numerical_decimal_note=:zopdecimal_comment,
                    response_modify=:zopmodify,
                    response_modify_note=:zopmodify_comment,
                    revisor_time=:zrevisor_time                
                WHERE
                    id=:zid
                ");
            $DBO->execute($reportArray);
            ?>
            <!-- START Page Heading -->
            <script>
                document.getElementById("header").innerText = "<?php echo fn_lang('REPORTPREPARE'); ?>";
            </script>
            <!-- END Page Heading -->
            <!-- START PREPARE REPORT -->
            <div class="container" dir="<?php echo ($langset == "Eng") ? "ltr" : "rtl"; ?>">
                <?php
                    //Report message and email to creator
                    //sending email to creator
                    $body = "<h1>Be Alerted</h1> <h2>".$emailCreator['ename']."</h2>";
                    $msg=$emailRevisor['ename']." found some bugs in your Question, harry up and solve them and return question to be re-revised.<h2>Don't replay</h2><p>Well done</p>" . $web;            
                    sendEmail($emailCreator['email'],$emailCreator['ename'],"Question Have some Bugs
                    ",$body.$msg,$msg);
                    //sending message to creator
                    fn_redirect("Sending report to Question creator", "success", 3, "?do=Dash&Langset=".$langset);
                ?>
            </div>
            <!-- END PREPARE REPORT -->
            <?php            
        }else if(isset($_POST['accept'])){
            //check Accept Q is pushed
            //continue if Accept Q is pushed
            // clear all options in the review file
            //Report for the review and rereview
            // Prepare Data Before DB
            // Calculate time of revise
            $now = new Datetime();
            $temp= $con->prepare("SELECT * FROM question_review WHERE id=?");
            $temp->execute(array($_GET['ReviewID']));
            $origin=$temp->fetch();//get this record starting date review
            $revDate=new Datetime($origin['review_date']);
            if($origin['revisor_time']!=NULL){
                list($H,$M,$S)=explode(":",$origin['revisor_time']);
                $orginToTime=intval($H)*3600 + intval($M)*60 + intval($S);
            }
            $diff=$revDate->diff($now);
            $toTime=$diff->d*24*60*60 + $diff->h*60*60 + $diff->i*60 + $diff->s;
            
            //  Database adjustments
            // release the version before sending // clear all faults
            $reportArray = array(
                //review Q ID
                ':zid' => intval($_GET['ReviewID']), 
                // prepare to send to creator
                ':zreport_status' => 4, 
                // Start collecting data
                ':zlosuitable' => isset($_POST['lo']) ? 0: 1,
                ':zlosuitable_comment' => isset($_POST['locheck']) ? $_POST['locheck'] : "",
                ':zpalegrism' => isset($_POST['palcontent']) ? $_POST['palcontent'] : "",
                ':zdok' => isset($_POST['revdok']) ? 0 : 1,
                ':zdok_comment' => isset($_POST['dokcheck']) ? $_POST['dokcheck'] : "",
                ':zdokproff' => isset($_POST['dokproff']) ? 0 : 1,
                ':zdokproff_comment' => isset($_POST['dokproffcheck']) ? $_POST['dokproffcheck'] : "",
                ':zreference' => isset($_POST['refstatus']) ? 0 : 1,
                ':zreference_comment' => isset($_POST['refcheck']) ? $_POST['refcheck'] : "",
                ':zerr' => isset($_POST['err']) ? 0 : 1,
                ':zerr_comment' => isset($_POST['errcheck']) ? $_POST['errcheck'] : "",
                ':zkey' => isset($_POST['key']) ? 0 : 1,
                ':zkey_comment' => isset($_POST['keycheck']) ? $_POST['keycheck'] : "",
                ':zidentifier' => isset($_POST['identifier']) ? 0 : 1,
                ':zidentifier_comment' => isset($_POST['identifiercheck']) ? $_POST['identifiercheck'] : "",
                ':zidentifieronly' => isset($_POST['identifier_only']) ? 0 : 1,
                ':zidentifieronly_comment' => isset($_POST['identifieronlycheck']) ? $_POST['identifieronlycheck'] : "",
                ':zdistractors' => isset($_POST['distract']) ? 0 : 1,
                ':zdistractors_comment' => isset($_POST['distractcheck']) ? $_POST['distractcheck'] : "",
                ':zlang' => isset($_POST['lang']) ? 0 : 1,
                ':zlang_comment' => isset($_POST['langcheck']) ? $_POST['langcheck'] : "",
                ':znewq' => isset($_POST['newq']) ? 0 : 1,
                ':znewq_comment' => isset($_POST['newqcheck']) ? $_POST['newqcheck'] : "",
                // 20 items
                ':zgrammer' => isset($_POST['grammer']) ? 0 : 1,
                ':zgrammer_comment' => isset($_POST['grammercheck']) ? $_POST['grammercheck'] : "",
                ':zabbrev' => isset($_POST['abbrev']) ? 0 : 1,
                ':zabbrev_comment' => isset($_POST['abbrevcheck']) ? $_POST['abbrevcheck'] : "",
                ':zsymbol' => isset($_POST['symbol']) ? 0 : 1,
                ':zsymbol_comment' => isset($_POST['symbolcheck']) ? $_POST['symbolcheck'] : "",
                ':znegative' => isset($_POST['negative']) ? 0 : 1,
                ':znegative_comment' => isset($_POST['negativecheck']) ? $_POST['negativecheck'] : "",
                ':zletters' => isset($_POST['letters']) ? 0: 1,
                ':zletters_comment' => isset($_POST['letterscheck']) ? $_POST['letterscheck'] : "",
                ':zneed' => isset($_POST['need']) ? 0 : 1,
                ':zneed_comment' => isset($_POST['needcheck']) ? $_POST['needcheck'] : "",
                ':zstimadj' => isset($_POST['drawing']) ? 0 : 1,
                ':zstimadj_comment' => isset($_POST['drawingcheck']) ? $_POST['drawingcheck'] : "",
                ':zrec' => isset($_POST['rec']) ? 0 : 1,
                ':zrec_comment' => isset($_POST['reccheck']) ? $_POST['reccheck'] : "",
                ':zstemrev' => isset($_POST['stemrev']) ? 0: 1,
                ':zstemrev_comment' => isset($_POST['stemrevcheck']) ? $_POST['stemrevcheck'] : "",
                ':zsimple' => isset($_POST['simple']) ? 0 : 1,
                ':zsimple_comment' => isset($_POST['simplecheck']) ? $_POST['simplecheck'] : "",
                ':zthird' => isset($_POST['third']) ? 0 : 1,
                ':zthird_comment' => isset($_POST['thirdcheck']) ? $_POST['thirdcheck'] : "",
                // 40 items
                ':zstemmod' => isset($_POST['stemmod']) ? 0 : 1,
                ':zstemmod_comment' => isset($_POST['stemmodcheck']) ? $_POST['stemmodcheck'] : "",
                ':zrespond' => isset($_POST['respond']) ? 0 : 1,
                ':zrespond_comment' => isset($_POST['respondcheck']) ? $_POST['respondcheck'] : "",
                ':zrespeasy' => isset($_POST['respeasy']) ? 0 : 1,
                ':zrespeasy_comment' => isset($_POST['respeasycheck']) ? $_POST['respeasycheck'] : "",
                ':zrestrict' => isset($_POST['restrict']) ? 0 : 1,
                ':zrestrict_comment' => isset($_POST['restrictcheck']) ? $_POST['restrictcheck'] : "",
                ':zoplength' => isset($_POST['oplength']) ? 0 : 1,
                ':zoplength_comment' => isset($_POST['oplengthcheck']) ? $_POST['oplengthcheck'] : "",
                ':zopgrammer' => isset($_POST['opgrammer']) ? 0 : 1,
                ':zopgrammer_comment' => isset($_POST['opgrammercheck']) ? $_POST['opgrammercheck'] : "",
                ':zoprepeat' => isset($_POST['oprepeat']) ? 0 : 1,
                ':zoprepeat_comment' => isset($_POST['oprepeatcheck']) ? $_POST['oprepeatcheck'] : "",
                ':zwordrepeat' => isset($_POST['wordrepeat']) ? 0 : 1,
                ':zwordrepeat_comment' => isset($_POST['wordrepeatcheck']) ? $_POST['wordrepeatcheck'] : "",
                ':znoun' => isset($_POST['noun']) ? 0 : 1,
                ':znoun_comment' => isset($_POST['nouncheck']) ? $_POST['nouncheck'] : "",
                ':zopnumber' => isset($_POST['opnumber']) ? 0 : 1,
                ':zopnumber_comment' => isset($_POST['opnumbercheck']) ? $_POST['opnumbercheck'] : "",
                ':zopdecimal' => isset($_POST['opdecimal']) ? 0 : 1,
                ':zopdecimal_comment' => isset($_POST['opdecimalcheck']) ? $_POST['opdecimalcheck'] : "",
                ':zopmodify' => isset($_POST['opmodify']) ? 0 : 1,
                ':zopmodify_comment' => isset($_POST['opmodifycheck']) ? $_POST['opmodifycheck'] : "",
                ':zrevisor_time' => ($origin['revisor_time'] != NULL) ? gmdate("H:i:s",$orginToTime + $toTime) : gmdate("H:i:s",$toTime)
            );
            
            $DBO=$con->prepare("UPDATE 
                    question_review 
                SET review_status=:zreport_status,
                    content_aligned=:zlosuitable,
                    content_aligned_note=:zlosuitable_comment,
                    plagiarism_percent=:zpalegrism,
                    dok=:zdok,
                    dok_note=:zdok_comment,
                    dok_proff=:zdokproff,
                    dok_proff_note=:zdokproff_comment,
                    reference=:zreference,
                    reference_note=:zreference_comment,
                    errors=:zerr,
                    errors_note=:zerr_comment,
                    key_good=:zkey,
                    key_note=:zkey_comment,
                    identifier=:zidentifier,
                    identifier_note=:zidentifier_comment,
                    identifier_only=:zidentifieronly,
                    identifier_only_note=:zidentifieronly_comment,
                    distractors=:zdistractors,
                    distractors_note=:zdistractors_comment,
                    rev_lang=:zlang,
                    rev_lang_note=:zlang_comment,
                    replicant=:znewq,
                    replicant_note=:znewq_comment,
                    grammer=:zgrammer,
                    grammer_note=:zgrammer_comment,
                    abbreviation=:zabbrev,
                    abbreviation_note=:zabbrev_comment,
                    symbols=:zsymbol,
                    symbols_note=:zsymbol_comment,
                    Negative=:znegative,
                    Negative_note=:znegative_comment,
                    letters=:zletters,
                    letters_note=:zletters_comment,
                    stimulus_needed=:zneed,
                    stimulus_needed_note=:zneed_comment,
                    stimulus_adjust=:zstimadj,
                    stimulus_adjust_note=:zstimadj_comment,
                    stimulus_modification=:zrec,
                    stimulus_modification_note=:zrec_comment,
                    stem_end=:zstemrev,
                    stem_end_note=:zstemrev_comment,
                    stem_good=:zsimple,
                    stem_good_note=:zsimple_comment,
                    stem_third=:zthird,
                    stem_third_note=:zthird_comment,
                    stem_modify=:zstemmod,
                    stem_modify_note=:zstemmod_comment,
                    response_relation=:zrespond,
                    response_relation_note=:zrespond_comment,
                    response_easy=:zrespeasy,
                    response_easy_note=:zrespeasy_comment,
                    all_above=:zrestrict,
                    all_above_note=:zrestrict_comment,
                    response_length=:zoplength,
                    response_length_note=:zoplength_comment,
                    response_grammer=:zopgrammer,
                    response_grammer_note=:zopgrammer_comment,
                    response_repeat=:zoprepeat,
                    response_repeat_note=:zoprepeat_comment,
                    Words_special=:zwordrepeat,
                    Words_special_note=:zwordrepeat_comment,
                    nouns=:znoun,
                    nouns_note=:znoun_comment,
                    response_numerical=:zopnumber,
                    response_numerical_note=:zopnumber_comment,
                    response_numerical_decimal=:zopdecimal,
                    response_numerical_decimal_note=:zopdecimal_comment,
                    response_modify=:zopmodify,
                    response_modify_note=:zopmodify_comment,
                    revisor_time=:zrevisor_time                
                WHERE
                    id=:zid
                ");
            $DBO->execute($reportArray);
            
            //DO THE JOB for ACCEPT
            //send revisor approval on file create Question
            if ($_GET['RevisorQue']==1){
                $datafield="rev_one_approval";
            }else{
                $datafield="rev_two_approval";
            }
            $DBRevUpdate=$con->prepare("UPDATE questions_create SET $datafield=? WHERE question_id=?");
            $DBRevUpdate->execute(array(1,intval($_GET['QuestionID'])));
            //make revisor free once more
            $DBRevUpdate=$con->prepare("UPDATE teacher_items SET free_for_revision=? WHERE teacher_id=? and account_status=?");
            $DBRevUpdate->execute(array(1,$_GET['Revisor'],1));
            // Adjust DB FILES
            // Update Create finished revision
            //first get the record
            $DBO=$con->prepare("SELECT * FROM questions_create WHERE question_id=? ");
            $DBO->execute(array($_GET['QuestionID']));
            $DBRecord=$DBO->fetch();

            //second adjust data
            $target='';
            if($DBRecord['revisor_one']==$_GET['Revisor']){
                $target='rev_one_approval';
            }else{
                $target='rev_two_approval';
            }
            $DBQCreate=$con->prepare("UPDATE questions_create SET $target=? WHERE question_id=?");
            $DBQCreate->execute(array(1,$_GET['QuestionID']));
            
            //check both revisors approval
            if ($DBRecord['rev_one_approval']==1 && $DBRecord['rev_two_approval']==1){
                //both approved
                $datenow=$now->format('Y-m-d H:i:s');
                $DBO=$con->prepare("UPDATE questions_create SET cart=?,pilot=?,submit_date=?,question_case=? WHERE question_id=?");
                $DBO->execute(array(0,1,$datenow,2,$_GET['QuestionID']));
            }
            //Get the record from question_review DB
            $DBRevFile=$con->prepare("SELECT * FROM question_review WHERE question_id=? AND revisor_id=?");
            $DBRevFile->execute(array($_GET['QuestionID'],$_GET['Revisor']));
            $catch=$DBRevFile->fetch();
            //send Question to Pilot - mission done
            $DBRevUpdate=$con->prepare("UPDATE question_review SET review_status=?, finish_review_date=? WHERE id=?");
            $DBRevUpdate->execute(array(4,date("Y-m-d H:i:s"),$catch['id']));
            //call email function
            
            echo '<div class="container">';
                //Report message and email to creator
                //sending email to creator
                $body = "<h1>Congratulation</h1> <h2>".$emailCreator['ename']."</h2>";
                $msg=$emailRevisor['ename']." Approved your Question.<h2>Don't replay</h2><p>Well done</p>" . $web;            
                sendEmail($emailCreator['email'],$emailCreator['ename'],"Question Accepted",$body.$msg,$msg);
                //sending message email to creator
                fn_redirect("Revision Accepted", "success", 3, "?do=Dash&Langset=".$langset);
            echo '</div>';
        }
    } else {
    fn_redirect("Can't Complete Action Correctly", "danger", 3, "back");
    }
    break;
case 'Video':
    ?>
    <!-- Page Heading -->
    <script>
        document.getElementById("header").innerText = "<?php echo fn_lang('VIDEOPAGE'); ?>";
    </script>
    <!-- Page Heading -->
    <div class="Container" style="
        padding: 20px;
        width:100%;
        ">
        <div class="videos">
            <h2 class="header">
                <?php echo fn_lang('VIDEOLINK');?>
            </span></h2>
            <div class="workingarea">
                <button class="btn btn-outline-info" value="0">
                    <?php echo fn_lang('VID1');?>
                </button>
                <button class="btn btn-outline-info" value="1">
                    <?php echo fn_lang('VID2');?>
                </button>
                <button class="btn btn-outline-info" value="2">
                    <?php echo fn_lang('VID3');?>
                </button>
                <button class="btn btn-outline-info" value="3">
                    <?php echo fn_lang('VID4');?>
                </button>
                <button class="btn btn-outline-info" value="4">
                    <?php echo fn_lang('VID5');?>
                </button>
                <button class="btn btn-outline-info" value="5">
                    <?php echo fn_lang('VID6');?>
                </button>
                <button class="btn btn-outline-info" value="6">
                    <?php echo fn_lang('VID7');?>
                </button>
            </div>
        </div>
        <div class="show">
            <iframe id="youtube-video" 
                src="" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen>
            </iframe>
        </div>
    </div>
    
<?php
                break;

            case 'Logout':
                end($_SESSION);
                session_unset(); ///Unset The Data
                session_destroy(); //Destroy The Data
                header("location:../index.html");
                exit();
            case 'SaveProfile':
                ?>
                <script>
                    document.getElementById("header").innerText = "<?php echo fn_lang('SAVEPROFILE'); ?>";
                </script>
                <!-- START Container Contents -->
            <div class="container">
                <?php
                if($_SERVER['REQUEST_METHOD']==='POST'){
                    //get DBFILE
                    $dbfile=$con->prepare("SELECT *
                        FROM
                            teachers
                        WHERE
                            teacher_id=?
                    ");
                    $dbfile->execute(array($_SESSION['teacher_id']));
                    $dbDataTeacher=$dbfile->fetch();
                    //check for uploaded file errors
                    $itemallowedtypes = array("jpeg", "jpg", "png", "gif", "jfif", "ico");
                    $errFileCounter=0;
                    $errCounter=0;
                    if(isset($_FILES['avatar'])&& $_FILES['avatar']['error']!=4){
                        $avFile= $_FILES['avatar'];
                        $temp= explode(".",$avFile['name']);
                        $imgEXT=strtolower(end($temp));
                        if(!in_array($imgEXT,$itemallowedtypes)){
                            $errFileCounter++;
                            $errmsg[]="Avatar File Type is not allowed.";
                        }
                        if($avFile['size']>2097152){
                            $errFileCounter++;
                            $errmsg[]="Avatar File Size is larger than 2MB.";
                        }
                    }
                    //check for Password errors
                    if (isset($_POST['passwordcheck'])) {
                        
                        //Hash the oldpassword
                        $_POST["oldpassword"]=sha1($_POST["oldpassword"]);
                        
                        if ($_POST['oldpassword']!=$dbDataTeacher['password']) {
                            $errCounter++;
                            $errmsg[] = "Wrong Password."; 
                        }
                    }
                    //check type of profile language
                    if(isset($_POST['grade'])&& $_POST['grade']!=0){
                        if(!(isset($_POST['lang']))){
                                $errCounter++;
                                $errmsg[]="Select Langauge or Revisor or both for Profile";
                            }
                        }
                    //browse Error msgs
                    if($errCounter!=0 || $errFileCounter!=0){
                        foreach ($errmsg as $err ) {
                            # code...
                            echo "<div class='border-start border-info  border-5 border-top-0 border-bottom-0 border-end-0 alert alert-info'> $err</div>";
                        }
                        fn_redirect("Solve Errors", "danger", 10, "back");
                    }else{
                        //start saving data
                        //fetch data for update
                        //starting with avatar first if existed
                        if(isset($_FILES['avatar'])&&
                            $_FILES['avatar']['error']==0&&
                            $_FILES['avatar']['size']!=0
                        ){
                            //add file to avatar director
                            $tempext = explode(".", $avFile['name']);
                            $imageExtension = strtolower(end($tempext));
                            $name = $_SESSION['teacher_id'] . "." . $imageExtension;
                            move_uploaded_file($avFile['tmp_name'], $imgDir . "Profile/".$name);
                            $dbfile=$con->prepare("UPDATE teachers 
                                                SET
                                                    avatar=:zavatar
                                                    WHERE
                                                    teacher_id=:zid
                                                ");
                            $dbfile->execute(array(
                                'zavatar'=>$name,
                                'zid'=>$_SESSION['teacher_id']
                            ));
                        }
                        //Update data in DB
                        //Catch INFO file DBFILE
                        $dbfile=$con->prepare("SELECT *
                            FROM
                                teachers
                            WHERE
                                teacher_id=?
                        ");
                        $dbfile->execute(array($_SESSION['teacher_id']));
                        $dbDataTeacher=$dbfile->fetch();
                        //UPDATE FILE WITH NEW DATA
                        $tempPass=isset($_POST['passwordcheck'])?sha1($_POST['newpassword']):$dbDataTeacher['password'];
                        $dbTeacherInfo=$con->prepare("UPDATE teachers
                                        SET
                                            ename=:znameEn,
                                            aname=:znameAr,
                                            email=:zemail,
                                            tel=:ztel,
                                            whats=:zwhats,
                                            password=:znewpass,
                                            repassword=:zconfirm
                                        WHERE
                                                teacher_id=:zid
                            ");
                        $dbTeacherInfo->execute(array(
                            'znameEn'=>$_POST['nameEn'],
                            'znameAr'=>$_POST['nameAr'],
                            'zemail'=>$_POST['email'],
                            'ztel'=>$_POST['tel'],
                            'zwhats'=>$_POST['whats'],
                            'znewpass'=>$tempPass,
                            'zconfirm'=>$tempPass,
                            'zid'=>$_SESSION['teacher_id']
                            ));
                        // Get TEACHER INFO
                        $dbfile=$con->prepare("SELECT *
                            FROM
                                teacher_items
                            WHERE
                                teacher_id=?
                            AND
                                account_status=?
                                ");
                        $dbfile->execute(array($_SESSION['teacher_id'],1));
                        $dbTeacherInfo=$dbfile->fetch();
                        //change profile if checked
                        if ($_POST['profileItem']!=$dbTeacherInfo['item_id']){
                            $profileDB=$con->prepare("UPDATE teacher_items
                                    SET
                                        account_status=:zstatus
                                        WHERE
                                        item_id=:zid
                            ");
                            //convert old selected profile
                            $profileDB->execute(array(
                                ':zstatus'=>0,
                                ":zid"=>$dbTeacherInfo['item_id']
                            ));
                            $profileDB->execute(array(
                                ':zstatus'=>1,
                                ":zid"=>$_POST['profileItem']
                            ));
                        }
                        //Save New Profile
                        if(isset($_POST['grade'])&& $_POST['grade']!=0){
                            $revisor=isset($_POST['revisor'])?"REV":"";
                            $revisioncase=isset($_POST['revisor'])?1:0;
                            $profileDB=$con->prepare("INSERT INTO 
                                        teacher_items(
                                            type,stage,subject,grade,
                                            lang,revision,
                                            teacher_id,
                                            account_status,
                                            free_for_revision)
                                        VALUES(
                                            :ztype,:zstage,:zsubject,:zgrade,
                                            :zlang,:zrevision,
                                            :zid,
                                            :zaccount_status,
                                            :zfreerevision
                                            )
                                        ");
                            //convert old selected profile
                            $profileDB->execute(array(
                                ':ztype'=>$_POST['type'],
                                ':zstage'=>$_POST['stage'],
                                ':zsubject'=>$_POST['subject'],
                                ':zgrade'=>$_POST['grade'],
                                ':zlang'=>$_POST['lang'],
                                ':zrevision'=>$revisor,
                                ':zid'=>$_SESSION['teacher_id'],
                                ':zaccount_status'=>0,
                                ':zfreerevision'=>$revisioncase
                            ));
                        }
                        fn_redirect("SAVING DONE SAFTLY","info", 3, "?do=Dash&Langset=$langset");
                    }
                }else {
                    fn_redirect("WRONG ACCESS TO PAGE", "danger", 3, "?do=Dash&Langset=$langset");
                }
                ?>
                </div>
                <?php
                break;
                case 'StartRevision':
                    ?>
                        <!-- Page Heading -->
                        <script>
                            document.getElementById("header").innerText = "<?php echo fn_lang('EDITQ'); ?>";
                        </script>
                        <?php
                            //prepare data from teacher_items
                            $DBrow = fn_getRecordsV2("*", "teacher_items", "WHERE teacher_id=? AND account_status=?", array($_SESSION['teacher_id'], 1));
                            $DBEdit = $con->prepare("SELECT *
                        FROM
                            questions_create
                        WHERE
                            questions_create.question_id=?
                        ORDER BY
                            creation_date DESC
                        ");
                            $DBEdit->execute(array($_GET['record']));
                            $DBEditrow = $DBEdit->fetch();
                            ?>
                        <!-- START Container Contents -->
                        <div class="container content" dir="<?php echo ($langset == "Eng") ? "ltr" : "rtl"; ?>">
                            <div class="question contents">
                                <!-- START Edit Question Section -->
                                <form action="?do=Update&Langset=<?php echo ($langset) ?>&record=<?php echo $DBEditrow['question_id'] ?>" method="post" enctype="multipart/form-data">
                                    <?php
                                    $outcomes = fn_getOutComeV2($DBrow[0]['type'], $DBrow[0]['stage'], $DBrow[0]['subject'], $DBrow[0]['grade'], $DBrow[0]['lang']);
                                    ?>
                                    <!-- The whole subjects are put here from function.php -->
                                    <!-- START The outcomes -->
                                    <div>
                                        <label for="outcome"><?php echo fn_lang('OUTCOME'); ?>
                                            <select name="outcome" id="outcome" class="outcome form-select" required>
                                                <?php
                                                for ($i = 0; $i < count($outcomes); $i++) {
                                                    echo "<option value=" . $outcomes[$i]['outcome_id'] . ">" . "Unit" . $outcomes[$i]['unit'] . " - Ch" . $outcomes[$i]['chapter'] . " - " . $outcomes[$i]['item'] . " - " . substr($outcomes[$i]["content"], 0, 120) . "</option>";
                                                }
                                                // 
                                                ?>
                                            </select>
                                        </label>
                                    </div>
                                    <script>
                                        document.getElementById("outcome").value = <?php echo $DBEditrow['outcome_id']; ?>; // Change to the option with the saved value
                                    </script>
                                    <!-- END The outcomes -->
                                    <!-- START The Question Body -->
                                    <div>
                                        <h5 class="w-100 p-1 QC"><?php echo fn_lang('QCONSTRUCT'); ?></h5>
                                        <!-- START WRITTER -->
                                        <div class="body mb-3">
                                            <label for="writter" class="fw-semibold"><?php echo fn_lang('WRITTER'); ?>
                                            </label>
                                            <input type="text" name="writter" id="writter"
                                                class="form-control"
                                                value="<?php echo $_SESSION['teacher_name'] ?>"
                                                required disabled>
                                        </div>
                                        <!-- END WRITTER -->
                                        <!-- START DATE -->
                                        <div class="body mb-3">
                                            <label for="crea_date" class="fw-semibold"><?php echo fn_lang('EDITDATE'); ?>
                                            </label>
                                            <input type="datetime-local" name="edit_date" id="edit_date"
                                                class="form-control"
                                                value="<?php echo date("Y-m-d H:i:s") ?>"
                                                required disabled>
                                        </div>
                                        <!-- END DATE -->
                                        <!-- START SUBJECT -->
                                        <div class="body mb-3">
                                            <label for="subject" class="fw-semibold"><?php echo fn_lang('SUBJECT'); ?>
                                            </label>
                                            <input type="text" name="subject" id="subject"
                                                class="form-control"
                                                value="<?php echo $DBrow[0]['subject'] . "-" . $DBrow[0]['grade'] . "-" . $DBrow[0]['lang'] ?>"
                                                required disabled>
                                        </div>
                                        <!-- END SUBJECT -->
                                        <!-- START DEPTH -->
                                        <div class="body mb-3">
                                            <label for="depth" class="fw-semibold"><?php echo fn_lang('DEPTH'); ?>
                                                <select name="depth" id="depth" class="depth form-select" required>
                                                    <option value="RECALL"><?php echo fn_lang('RECALL') ?></option>";
                                                    <option value="DIRECT"><?php echo fn_lang('DIRECT') ?></option>";
                                                    <option value="STRATEGIC THINKNG"><?php echo fn_lang('STRATEGIC THINKNG') ?></option>";
                                                </select>
                                            </label>
                                        </div>
                    
                                        <script>
                                            document.getElementById("depth").value = <?php echo '"' . $DBEditrow['depth'] . '"'; ?>; // Change to the option with the saved value
                                        </script>
                                        <!-- END DEPTH -->
                                        <!-- START DOK -->
                                        <div class="body mb-3">
                                            <label for="dok" class="fw-semibold"><?php echo fn_lang('DOK'); ?>
                                            </label>
                                            <input type="text" name="dok" id="dok" class="dok form-control" value="<?php echo $DBEditrow['dok']; ?>">
                                        </div>
                                        <!-- END DOK -->
                                        <!-- START REFERENCE -->
                                        <div class="body mb-3">
                                            <label for="REFERENCE_EDIT" class="fw-semibold"><?php echo fn_lang('REFERENCE'); ?>
                                                <select id="REFERENCE_EDIT" name="reference" class="ref_sel form-select" required>
                                                    <option value="none"><?php echo fn_lang('NO_REF'); ?></option>";
                                                    <option value="NA"><?php echo fn_lang('BRAND_NEW'); ?></option>";
                                                    <option value="REF"><?php echo fn_lang('REF_SELECT'); ?></option>";
                                                </select>
                                            </label>
                                            <script>
                                                document.getElementById("REFERENCE_EDIT").value = <?php echo '"' . $DBEditrow['reference'] . '"'; ?>; // Change to the option with the saved value
                                            </script>
                                            <input type="text" name="ref_inp" id="ref_inp_edit"
                                                class="ref_inp form-control"
                                                <?php if ($DBEditrow['reference'] !== "REF") {
                                                    echo "disabled style='display:none;'";
                                                }
                                                ?>
                                                placeholder="<?php echo fn_lang("REF"); ?>"
                                                value=<?php echo "'" . $DBEditrow['ref_txt'] . "'"; ?>>
                                        </div>
                                        <!-- END REFERENCE -->
                                        <!-- START STEM -->
                                        <div class="body mb-3 stem_bar">
                                            <label for="textarea" class="fw-semibold"><?php echo fn_lang('STEM'); ?>
                                            </label>
                                            <!-- Math bar inserted here by following function -->
                                            <?php echo fn_QuestionBar("stem") ?>
                                            <script>
                                                document.getElementById('textarea-stem').value = <?php echo json_encode($DBEditrow['Question_Body']); ?>;
                                            </script>
                                        </div>
                                        <!-- END STEM -->
                                        <!-- START STEM Image -->
                                        <div class="body mb-3 checkbar">
                                            <label for="image" class="fw-semibold image_check"><?php echo fn_lang('IMAGE'); ?>
                                                <input type="checkbox" name="stemImageCheck" id="image"
                                                    <?php
                                                    if ($DBEditrow['supported_image'] != 0)
                                                        echo "checked";
                                                    ?> hidden>
                                                <div class="enhanced_bar img
                                                <?php
                                                if ($DBEditrow['supported_image'] != 0)
                                                    echo "active";
                                                ?>
                                                "></div>
                                            </label>
                                        </div>
                                        <div class="body mb-3">
                                            <label for="image_path" class="fw-semibold drop"
                                                <?php
                                                if ($DBEditrow['supported_image'] != 0) {
                                                    echo 'style="display: block;"';
                                                } else {
                                                    echo 'style="display: none;"';
                                                }
                                                ?>>
                                                <input type="file" accept="image" name="image_path" id="image_path" class="image_path form-control"
                                                    autocomplete="on" style="display: none;">
                                                <div class="img">
                                                    <img alt="no image" class="imageArea" style="display:<?php echo ($DBEditrow['supported_image'] == 0) ? 'none;' : 'block;' ?>"
                                                        src="<?php echo ($DBEditrow['supported_image'] == 0) ? '' : IMG_DIR . "Questions/" . $DBEditrow['image_link']; ?>">
                                                    <input class="DBImages" name="stemImageDB" value="<?php echo ($DBEditrow['supported_image'] == 0) ? '' : $DBEditrow['image_link']; ?>" hidden>
                                                    <div class="cloud">
                                                        <i class="fa fa-cloud-arrow-up fa-3x" color="#0BC279"
                                                            <?php
                                                            if ($DBEditrow['supported_image'] != 0) {
                                                                echo 'style="display: none;"';
                                                            } else {
                                                                echo 'style="display: block;"';
                                                            }
                                                            ?>></i>
                                                        <p
                                                            <?php
                                                            if ($DBEditrow['supported_image'] != 0) {
                                                                echo 'style="display: none;"';
                                                            } else {
                                                                echo 'style="display: block;"';
                                                            }
                                                            ?>>Drag and drop or click <strong>here</strong><br>to upload Image</p>
                                                        <span
                                                            <?php
                                                            if ($DBEditrow['supported_image'] != 0) {
                                                                echo 'style="display: none;"';
                                                            } else {
                                                                echo 'style="display: block;"';
                                                            }
                                                            ?>>Upload any images from your local storage</span>
                                                        <p class="alert alert-danger m-0 p-1 sizeMsg" hidden>Too <strong>Large</strong> File, Try to upload file smaller than <strong>2MB</strong></p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        <!-- END STEM Image -->
                                        <!-- START SUB STEM -->
                                        <div class="body mb-3 stem_bar">
                                            <label for="substem" class="fw-semibold"><?php echo fn_lang('SUBSTEM'); ?>
                                            </label>
                                            <!-- Math Bar inserted below -->
                                            <?php echo fn_QuestionBar("substem") ?>
                                            <script>
                                                document.getElementById('textarea-substem').value = <?php echo  json_encode($DBEditrow['sub_stem']) ; ?>;
                                            </script>
                                        </div>
                                        <!-- END SUBSTEM -->
                                        <!-- START SUBSTEM Image -->
                                        <div class="body mb-3 checkbar">
                                            <label for="subimage" class="fw-semibold image_check"><?php echo fn_lang('IMAGE'); ?>
                                                <input type="checkbox" name="substemImageCheck" id="subimage"
                                                    <?php
                                                    if ($DBEditrow['substem_sup_img'] != 0)
                                                        echo "checked";
                                                    ?> hidden>
                                                <div class="enhanced_bar subimg
                                                <?php
                                                if ($DBEditrow['substem_sup_img'] != 0)
                                                    echo "active";
                                                ?>
                                                "></div>
                                            </label>
                                        </div>
                                        <div class="body mb-3">
                                            <label for="subimage_path" class="fw-semibold subdrop"
                                                <?php
                                                if ($DBEditrow['substem_sup_img'] != 0) {
                                                    echo 'style="display: block;"';
                                                } else {
                                                    echo 'style="display: none;"';
                                                }
                                                ?>>
                                                <input type="file" accept="image" name="subimage_path" id="subimage_path" class="image_path form-control"
                                                    autocomplete="on" style="display: none;">
                                                <div class="img">
                                                    <img alt="no image" class="subimageArea" style="display:<?php echo ($DBEditrow['substem_sup_img'] == 0) ? 'none;' : 'block;' ?>"
                                                        src="<?php echo ($DBEditrow['substem_sup_img'] == 0) ? '' : IMG_DIR . "Questions/" . $DBEditrow['substem_image_link']; ?>">
                                                    <input class="DBImages" name="subStemImageDB" value="<?php echo ($DBEditrow['substem_sup_img'] == 0) ? '' : $DBEditrow['substem_image_link']; ?>" hidden>
                                                    <div class="subcloud cloud">
                                                        <i class="fa fa-cloud-arrow-up fa-3x" color="#0BC279"
                                                            <?php
                                                            if ($DBEditrow['substem_sup_img'] != 0) {
                                                                echo 'style="display: none;"';
                                                            } else {
                                                                echo 'style="display: block;"';
                                                            }
                                                            ?>></i>
                                                        <p
                                                            <?php
                                                            if ($DBEditrow['substem_sup_img'] != 0) {
                                                                echo 'style="display: none;"';
                                                            } else {
                                                                echo 'style="display: block;"';
                                                            }
                                                            ?>>Drag and drop or click <strong>here</strong><br>to upload Image</p>
                                                        <span
                                                            <?php
                                                            if ($DBEditrow['substem_sup_img'] != 0) {
                                                                echo 'style="display: none;"';
                                                            } else {
                                                                echo 'style="display: block;"';
                                                            }
                                                            ?>>Upload any images from your local storage</span>
                                                        <p class="alert alert-danger m-0 p-1 sizeMsg" hidden>Too <strong>Large</strong> File, Try to upload file smaller than <strong>2MB</strong></p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        <!-- END SUBSTEM Image -->
                                        <!-- START OPTIONS -->
                                        <h3 class="Questions" ><?php echo fn_lang('QUESTIONREV') ?></h3>
                                        <div class="Question coloring">
                                            <?php for ($i = 1; $i < 5; $i++) {
                                                $questionName = ["One", "Two", "Three", "Four"]
                                            ?>
                    
                                                <!-- START OPTION ONE TWO THREE FOUR TAGS -->
                                                <div class="coloringChildren">
                                                    <div class="body Question mb-3 option<?php echo $questionName[$i - 1] ?>">
                                                        <div class="body mb-3">
                                                            <!-- START TRUE ANSWER -->
                                                            <div>
                                                                <label for="radio<?php echo $i ?>" class="fw-semibold"><?php echo fn_lang('TRUE'); ?>
                                                                </label>
                                                                <input type="radio" name="radioOptTrue" id="radio<?php echo $i ?>" class="choiceRadio form-radio" value="<?php echo $i; ?>"
                                                                    <?php if ($i == $DBEditrow['correct_answer']) echo "checked"; ?>>
                                                            </div>
                                                            <!-- END TRUE ANSWER -->
                                                            <!-- START ANSWER PHRASE -->
                                                            <div>
                                                                <label for="opt<?php echo $i ?>" class="fw-semibold"><?php echo fn_lang('CHOICE') . ' ' . $questionName[$i - 1]; ?>
                                                                </label>
                                                                <?php echo fn_QuestionBar("opt".$i);?>
                                                                <script>
                                                                    document.getElementById('<?php echo"opt".$i;?>').value = <?php echo  json_encode($DBEditrow['answer' . $i]) ; ?>;
                                                                </script>
                                                            </div>
                                                            <!-- END ANSWER PHRASE -->
                                                            <!-- START ANSWER REASON -->
                                                            <div>
                                                                <label for="opt<?php echo $i ?>Exp" class="fw-semibold"><?php echo fn_lang('CAUSE'); ?>
                                                                </label>
                                                                <input type="text" name="opt<?php echo $i ?>Exp" id="opt<?php echo $i ?>Exp" class="form-control " value=<?php echo '"' . $DBEditrow['ans' . $i . '_explain'] . '"'; ?>>
                                                            </div>
                                                            <!-- END ANSWER REASON -->
                                                        </div>
                                                        <!-- START SUPPORT IMAGE -->
                                                        <label for="opimage<?php echo $i ?>" class="fw-semibold image_check"><?php echo fn_lang('OPTION'); ?>
                                                            <input type="checkbox" name="opimage<?php echo $i ?>" id="opimage<?php echo $i ?>" data-number="<?php echo $i ?>" class="optionImage"
                                                                <?php
                                                                if ($DBEditrow['ans' . $i . '_sup_img'] != 0)
                                                                    echo "checked";
                                                                ?> hidden>
                                                            <div class="enhanced_bar op <?php echo $i ?> <?php
                                                                                                            if ($DBEditrow['ans' . $i . '_sup_img'] != 0)
                                                                                                                echo "active";
                                                                                                            ?>
                                                            "></div>
                                                        </label>
                                                        <!-- END SUPPORT IMAGE -->
                                                    </div>
                                                    <!-- START IMAGE SECTION -->
                                                    <div class="body mb-3">
                                                        <label for="Qopt_path<?php echo $i ?>" class="fw-semibold drop Q Area" data-number="<?php echo $i ?>"
                                                            <?php
                                                            if ($DBEditrow['ans' . $i . '_sup_img'] != 0) {
                                                                echo 'style="display: block;"';
                                                            } else {
                                                                echo 'style="display: none;"';
                                                            }
                                                            ?>>
                                                            <input type="file" accept="image" name="option<?php echo $questionName[$i - 1] ?>_path" id="Qopt_path<?php echo $i ?>" data-number="<?php echo $i ?>" class="Q option_path form-control"
                                                                autocomplete="on" style="display: none;">
                                                            <div class="Q  img">
                                                                <img alt="" class="Q imageArea" style="display:<?php echo ($DBEditrow['ans' . $i . '_sup_img'] == 0) ? 'none;' : 'block;' ?>"
                                                                    src="<?php echo ($DBEditrow['ans' . $i . '_sup_img'] == 0) ? '' : IMG_DIR . "Questions/" . $DBEditrow['ans' . $i . '_image_link']; ?>">
                                                                <input class="DBImages" name="<?php echo 'ans' . $i . 'ImageDB'; ?>" value="<?php echo ($DBEditrow['ans' . $i . '_sup_img'] == 0) ? '' : $DBEditrow['ans' . $i . '_image_link']; ?>" hidden>
                                                                <div class="Q cloud">
                                                                    <i class="fa fa-cloud-arrow-up fa-3x" color="#0BC279" <?php
                                                                                                                            if ($DBEditrow['ans' . $i . '_sup_img'] != 0) {
                                                                                                                                echo 'style="display: none;"';
                                                                                                                            } else {
                                                                                                                                echo 'style="display: block;"';
                                                                                                                            }
                                                                                                                            ?>></i>
                                                                    <p
                                                                        <?php
                                                                        if ($DBEditrow['ans' . $i . '_sup_img'] != 0) {
                                                                            echo 'style="display: none;"';
                                                                        } else {
                                                                            echo 'style="display: block;"';
                                                                        }
                                                                        ?>>Drag and drop or click <strong>here</strong><br>to upload Image</p>
                                                                    <span
                                                                        <?php
                                                                        if ($DBEditrow['ans' . $i . '_sup_img'] != 0) {
                                                                            echo 'style="display: none;"';
                                                                        } else {
                                                                            echo 'style="display: block;"';
                                                                        }
                                                                        ?>>Upload any images from your local storage</span>
                                                                    <p class="alert alert-danger m-0 p-1 optionsImg" hidden>Too <strong>Large</strong> File, Try to upload file smaller than <strong>2MB</strong></p>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                    <!-- END IMAGE SECTION -->
                                                </div>
                                                <!-- END OPTION ONE TWO THREE FOUR TAGS -->
                                            <?php
                                            }
                                            // END LOOPING -->
                                            echo "<br>"; ?>
                                        </div>
                                        <!-- END OPTIONS -->
                                    </div>
                                    <!-- END The Question Body -->
                                    <!-- START VARIABLES SECTION -->
                                    <div class="d-grid gap-3 col-12 offset-0">
                                        <input type="text" name="writter" value="<?php echo $_SESSION['teacher_name'] ?>" hidden>
                                        <input type="datetime-local" name="creation_date" value="<?php echo date("Y-m-d H:i:s") ?>" hidden>
                                        <input type="text" name="type" value="<?php echo $DBrow[0]['type'] ?>" hidden>
                                        <input type="text" name="stage" value="<?php echo $DBrow[0]['stage'] ?>" hidden>
                                        <input type="text" name="subject" value="<?php echo $DBrow[0]['subject'] ?>" hidden>
                                        <input type="text" name="grade" value="<?php echo $DBrow[0]['grade'] ?>" hidden>
                                        <input type="text" name="lang" value="<?php echo $DBrow[0]['lang'] ?>" hidden>
                                    </div>
                                    <!-- END VARIABLES SECTION -->
                                    <div class="d-flex gap-3 col-12 offset-0">
                                        <!-- START BTN AREA -->
                                        <!-- START SUMBIT BTN -->
                                        <div class="d-grid col-3">
                                            <input type="submit" id="submitbtn" name="updateDB" value="<?php echo fn_lang('SAVE') ?>" class="btn btn-primary  btn-lg">
                                        </div>
                                        <!-- END SUMBIT BTN -->
                                        <!-- START CANCEL BTN -->
                                        <div class="d-grid col-3">
                                            <a href="?do=Dash&Langset=<?php echo ($langset) ?>" class="btn  btn-warning  btn-lg">Cancel</a>
                                        </div>
                                        <!-- END CANCEL BTN -->
                                        <!-- END BTN AREA -->
                                    </div>
                                </form>
                    
                                <!-- END Edit Question Section -->
                    
                    
                            </div>
                        </div>
                        <?php
                break;                                
            default:
                # code...
                break;
            }
} else {
    fn_redirect("Not authorized login", "danger", 3, "../index.html");
    end($_SESSION);
    session_unset(); ///Unset The Data
    session_destroy(); //Destroy The Data
    exit();
} ?>
<script src="<?php echo $js; ?>homepage.js"></script>
<?php
include $tpl . 'footer.php';
?>