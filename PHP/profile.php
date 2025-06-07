<?php
// Auther: Walid Bakr
// Date: 2024-07-15
// Last Update: 2025-03-25
// Description: Profile Page JS

session_start();
// global $langset;
$pageTitle = 'User Profile';
$langset = isset($_GET['Langset']) ? $_GET['Langset'] : "Eng"; //default langauges is English each time
include 'init.php'; // initiate the file with headers and navbar

//check if the user session is found and the call is from homepage.php or goto else
if (isset($_SESSION['teacher_name'])) {
    //get user name to welcome him
    if ($langset == "Eng") {
        $welname = $teacherDB['ename'];
    } else {
        $welname = $teacherDB['aname'];
    }
    $welname = explode(" ", $welname);
    $shortname = $welname[0] . " " . $welname[count($welname) - 1];
    $do = isset($_GET['do']) ? $_GET['do'] : "Profile";
?>
    <div class="container Profile" dir="<?php if ($langset == "Eng") {
                                    echo "ltr";
                                } else {
                                    echo "rtl";
                                } ?>">
        <h6 class="float-r-t"><?php echo fn_lang('WELCOME') . " " . $shortname ?></h6>

    </div>

    <h1 class="text-center" id="header"><?php echo fn_lang("PROFILE");?></h1>
    <!-- START Main Contents Containertabs BOX -->
    <div class="backgrounds" ></div>
    <div class="cont content" dir="<?php echo ($langset == "Eng") ? "ltr" : "rtl"; ?>">
        <?php
        switch ($do) {
            
            case 'Profile':
                # code...
                ?>
                
    <?php
                if (isset($_SESSION['teacher_name'])) {
                    $dbLink = $con->prepare("SELECT *
                        FROM
                            teachers
                        INNER JOIN
                            teacher_items
                        ON
                            teacher_items.teacher_id= teachers.teacher_id
                        WHERE
                            teacher_items.teacher_id=?
                        
                        ");
                    $dbLink->execute(array($_SESSION['teacher_id']));
                    // get profiles linked to teacher
                    $dbprofiles = $dbLink->fetchAll();
                    ?>
                    <!-- Start Container -->
                    <div class="container Profile" >
                    <form action="homepage.php?do=SaveProfile" method="post" enctype="multipart/form-data" class="saveProfile">
                        <h3>Personal Info</h3>
                        <!-- Start User Main Data -->
                        <div class="personal info">
                            <!-- START ENAME FIELD -->
                            <div class="inpData">
                            <label for=""> Full Name</label>
                            <input
                                type="text"
                                id="nameEn"
                                name="nameEn"
                                class="inp name"
                                placeholder="Name in English"
                                autocomplete="username"
                                data-state="<?php echo ($dbprofiles[0]['ename']=='')?  'invalid': 'valid'; ?>"
                                value="<?php echo $dbprofiles[0]['ename']; ?>"/>
                            <div class="toolTip" hidden>Insert your full name</div>
                            </div>
                            <!-- ENDS ENAME FIELD -->
                            <!-- START ANAME FIELD -->
                            <div class="inpData Arabic">
                            <label for="" class="inp nameAr"> الأسم بالكامل</label>
                            <input
                                type="text"
                                id="nameAr"
                                name="nameAr"
                                class="inp nameAr"
                                placeholder="الاسم بالعربى"
                                autocomplete="username"
                                data-state="<?php echo ($dbprofiles[0]['aname']=='')?  'invalid': 'valid'; ?>"
                                value="<?php echo $dbprofiles[0]['aname']; ?>"/>
                            <div class="toolTip" hidden>أدخل اسمك باللغة العربية بالكامل</div>
                            </div>
                            <!-- START ANAME FIELD -->
                            <!-- START EMAIL FIELD -->
                            <div class="inpData">
                                <label for=""> Email</label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="inp emial"
                                    placeholder="Email@server.com"
                                    autocomplete="email"                                    
                                    data-state="<?php echo ($dbprofiles[0]['email']=='')?  'invalid': 'valid'; ?>"
                                    value="<?php echo $dbprofiles[0]['email']; ?>" />
                                <div class="toolTip" hidden>insert a valid Email</div>
                            </div>
                            <!-- ENDS EMAIL FIELD -->
                            <!-- START USERNAME FIELD -->
                            <div class="inpData">
                            <label for=""> User Name</label>
                            <input
                                type="text"
                                id="userName"
                                name="userName"
                                class="inp name"
                                placeholder="Username"
                                autocomplete="username"
                                value="<?php echo $dbprofiles[0]['username']; ?>" disabled/>
                            <div class="toolTip" hidden>
                                Username can't be replace once created
                            </div>
                            </div>
                            <!-- ENDS USERNAME FIELD -->
                            <!-- START TELEPHONE FIELD -->
                            <div class="inpData">
                            <label for=""> Telephone</label>
                            <input
                                type="text"
                                id="tel"
                                name="tel"
                                class="inp name tel"
                                placeholder="telephone"
                                autocomplete="tel-extension"
                                data-state="<?php echo ($dbprofiles[0]['tel']=='')?  'invalid': 'valid'; ?>"
                                maxlength="13"
                                minlength="11" 
                                value="<?php echo $dbprofiles[0]['tel'] ?>" />
                            <div class="toolTip" hidden>insert You telephone number</div>
                            </div>
                            <!-- ENDS TELEPHONE FIELD -->
                            <!-- START WHATSAPP FIELD -->
                            <div class="inpData">
                            <label for=""> WhatsAPP Tel</label>
                            <input
                                type="text"
                                id="whats"
                                name="whats"
                                class="inp name tel"
                                placeholder="Username"
                                autocomplete="tel-extension"
                                data-state="<?php echo ($dbprofiles[0]['whats']=='')?  'invalid': 'valid'; ?>"
                                maxlength="13"
                                minlength="11" 
                                value="<?php echo $dbprofiles[0]['whats'] ?>" />
                            <div class="toolTip" hidden>
                                insert What'sAPP Telephone Number
                            </div>
                            </div>
                            <!-- ENDS WHATSAPP FIELD -->
                            <!-- START PASSWORD BLOCK -->
                            <div class="passwordBlock">
                                <div class="">
                                    <label for="passwordcheck" class="fw-semibold pass_check"><?php echo fn_lang('PASSCHANGE'); ?>
                                        <input type="checkbox" name="passwordcheck" id="passwordcheck" hidden>
                                        <div class="enhanced_bar password" ></div>
                                    </label>
                                </div>
                                <!-- START PASSWORD Group -->
                                <div class="passGroup" hidden>
                                    <!-- START OLD PASSWORD FIELD -->
                                    <div class="inpData" >
                                        <label for=""> <?php echo fn_lang('OLDPASSWORD') ?></label>
                                        <input
                                            type="password"
                                            id="oldpassword"
                                            name="oldpassword"
                                            class="inp password"
                                            placeholder="insert a password if you want to change it"
                                            autocomplete="new-password"
                                            minlength="8"
                                            maxlength="20"
                                            />
                                        <div class="toolTip" hidden>insert old password for checking</div>
                                    </div>
                                    <!-- ENDS OLD PASSWORD FIELD -->
                                    <!-- START New PASSWORD FIELD -->
                                    <div class="inpData" >
                                        <label for=""> <?php echo fn_lang('PASSWORD') ?></label>
                                        <input
                                            type="password"
                                            id="newpassword"
                                            name="newpassword"
                                            class="inp password "
                                            placeholder="insert a password if you want to change it"
                                            autocomplete="new-password"
                                            minlength="8"
                                            maxlength="20"
                                            />
                                        <div class="toolTip" hidden>insert a password if you want to change it</div>
                                    </div>
                                    <!-- ENDS NEW PASSWORD FIELD -->
                                    <!-- START CONFIRM PASSWORD FIELD -->
                                    <div class="inpData" >
                                        <label for=""> <?php echo fn_lang('CONFIRM') ?></label>
                                        <input
                                            type="password"
                                            id="confirmPassword"
                                            name="confirmPassword"
                                            class="inp password"
                                            placeholder="Confirm password"
                                            autocomplete="confirm-passowrd"
                                            />
                                        <div class="toolTip" hidden>Confirm password</div>
                                    </div>
                                    <!-- ENDS CONFIRM PASSWORD FIELD -->
                                </div>
                                <!-- ENDS PASSWORD Group -->
                            </div>
                            <!-- ENDS PASSWORD BLOCK -->
                            <!-- START AVATAR BLOCK -->
                            <?php if (empty($dbprofiles[0]['userImage'])){?>
                            <p class="blinking">Warning. Avatar Image is added ONLY ONCE!</p>
                            <!-- START AVATAR FIELD -->
                            <div class="inpData">
                                <label for=""> Add Image</label>
                                    <input
                                        type="file"
                                        id="avatar"
                                        name="avatar"
                                        class="avatar"/>
                                        <div class="toolTip" hidden>Add avatar</div>
                                </div>
                                <!-- END AVATAR FIELD -->
                                <!-- START AVATAR IMAGE FIELD -->
                                <div class="inpData avatar">
                                    <img
                                        id="avatarImage"
                                        name="avatarImage"
                                        class="avatarImage"
                                        alt="No Images"
                                        />
                                </div>
                                <!-- END AVATAR IMAGE FIELD -->
                                <?php } ?>
                            <!-- END AVATAR BLOCK-->
                        </div>
                        <!-- END Personal Data -->
                        <!-- STARTS TEACHING  information -->
                        <div class="wrapper">

                            <h3>Teaching Info</h3>
                            <!-- START PROFILES  BLOCK -->
                            <?php 
                            //  Define Constants
                            $type=[
                                "National Egyptian Education",
                                "STEM",
                                "SAT",
                                "IG"
                            ];
                            $stage=[
                                "Elementary",
                                "Middle",
                                "Senior"
                            ];
                            $lang=[
                                "ENG"=>"English",
                                "AR"=>"Arabic"
                            ];

                            ?>
                            <div class="teachingProlie">
                                <table class="table table-striped-columns table-responsive table-dark"                    >
                                    <thead class="text-center">
                                        <tr>
                                            <th class="col-1">Active Profile</th>
                                            <th class="col-3">Type</th>
                                            <th class="col-2">Stage</th>
                                            <th class="col-1">Subject</th>
                                            <th class="col-1">Grade</th>
                                            <th class="col-1">Lang</th>
                                            <th class="col-1">Revision</th>
                                            <th class="col-2"><?php echo fn_lang('DELETE'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            <?php 
                                        foreach ($dbprofiles as $index=>$proItem) {
                                            ?>
                                            <tr class="<?php echo($proItem["account_status"]===1)? 'table-primary':'';?>">
                                                <td>
                                                    <input type="radio" name="profileItem" id="<?php echo $index ;?>" value="<?php echo $proItem["item_id"] ;?>"
                                                    <?php echo($proItem["account_status"]===1)?
                                                    'checked':'';?> 
                                                    >
                                                </td>
                                                <td class="text-center">
                                                    <?php echo $type[$proItem["type"]-1];?>
                                                </td>
                                                <td class="text-center">
                                                    <?php echo $stage[$proItem["stage"]-1];?>
                                                </td>
                                                <td class="text-center">
                                                    <?php echo $proItem["subject"];?>
                                                </td>
                                                <td class="text-center">
                                                    <?php echo $proItem["grade"];?>
                                                </td>
                                                <td class="text-center">
                                                    <?php echo $lang[$proItem["lang"]];?>
                                                </td >
                                                <td class="text-center">
                                                    <?php echo $proItem["revision"];?>
                                                </td>
                                                <td class="text-center" 
                                                <?php echo($proItem["account_status"]===1)? 'disabled':'';?>>
                                                    <a href="?do=DeleteProfileItem&Langset=<?php echo ($langset);?>&record=<?php echo $proItem["item_id"] ;?>"class="btn <?php echo($proItem["account_status"]===1)? 'btn-dark':'btn-danger';?> confirm" <?php echo($proItem["account_status"]===1)? 'style="pointer-events: none;color: gray"':'';?> >
                                                        <i class="fa fa-trash-alt" ></i> <?php echo fn_lang('DELETE'); ?>
                                                    </a>
                                                </td>
                                            </tr>
                                                <?php
                                            }
                                            ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- END TEACHING  BLOCK -->
                            <!-- START TEACHING  BLOCK -->
                            <div class="teaching">
                                <!-- STARTS type of education FIELD -->
                                <div class="type">
                                    <h4>Type of Education</h4>
                                    <div>
                                    <select
                                        name="type"
                                        class="type"
                                        data-state="valid">
                                        <option value="0" selected>=== Type of Education ===</option>
                                        <option value="1">National Egyptian Education</option>
                                        <option value="2">STEM</option>
                                        <option value="3">SAT</option>
                                        <option value="4">IG</option>
                                    </select>
                                    </div>
                                </div>
                                <!-- ENDS type of education FIELD -->
                                <!-- STARTS Stage FIELD -->
                                <div class="stage" hidden>
                                    <h4>Stage</h4>
                                    <div>
                                    <select
                                        name="stage"
                                        class="stage"                                    
                                        data-state="valid">
                                        <option value="0" selected>=== Select Stage ===</option>
                                        <option value="1">Elementary</option>
                                        <option value="2">Middle</option>
                                        <option value="3">Senior</option>
                                    </select>
                                    </div>
                                </div>
                                <!-- ENDS Stage FIELD -->
                                <!-- STARTS Subject FIELD -->
                                <div class="subject" hidden>
                                    <h4>Subject</h4>
                                    <div>
                                    <select
                                        name="subject"
                                        class="subject"
                                        data-state="valid">
                                        <option value="0" selected>=== Select Subject ===</option>
                                        <option value="PHY">Physics</option>
                                        <option value="CHE">Chemistry</option>
                                        <option value="BIO">Biology</option>
                                        <option value="GEO">Geology</option>
                                        <option value="MAT">Maths</option>
                                    </select>
                                    </div>
                                </div>
                                <!-- ENDS Subject FIELD -->
                                <!-- STARTS GRADE FIELD -->
                                <div class="grade" hidden>
                                    <h4>Grade</h4>
                                    <div>
                                    <select
                                        name="grade"
                                        class="grade"
                                        data-state="valid">
                                        <option value="0" selected>=== Select Grade ===</option>
                                        <option value="G10">Grade 10</option>
                                        <option value="G11">Grade 11</option>
                                        <option value="G12">Grade 12</option>
                                    </select>
                                    </div>
                                </div>
                                <!-- ENDS GRADE FIELD -->
                                <!-- STARTS LANG TYPE and REVISION FIELD -->
                                <div class="lang" hidden>
                                    <h4>Material</h4>
                                    <section>
                                    <!-- <div name="material" class="material" required data-state='valid'> -->
                                    <div name="lang" class="lang"  data-state="invalid">
                                        <label
                                        ><input type="radio" name="lang" value="ENG" />
                                        English</label
                                        >
                                        <label
                                        ><input type="radio" name="lang" value="AR" />
                                        عربى</label
                                        >
                                        <label
                                        ><input type="checkbox" name="revisor" value="REV" />
                                        Revisor</label
                                        >
                                    </div>
                                    </section>
                                </div>
                                <!-- ENDS LANG TYpE and REVISION FIELD -->
                            </div>
                            <!-- ENDS TEACHING  BLOCK -->
                        </div>
                        <!-- ENDS TEACHING  information -->
                    <!-- START SAVE BTN -->
                    <div class="row">
                        <div class="col col-lg-3">
                            <input type="submit" id="submitBtn" name="save" value="<?php echo fn_lang('SAVEPROFILE') ?>" class="btn success ">
                        </div>
                        <!-- END SAVE BTN -->
                        <!-- START CANCEL BTN -->
                        <div class="col col-md-1">
                            <a href="homepage.php?do=Dash&Langset=<?php echo ($langset) ?>" class="btn cancel">Cancel</a>
                        </div>
                    </div>
                    <!-- END CANCEL BTN -->
                    </form>
                    </div>
                    <!-- END Container -->
                    <?php
                } else {
                    echo "No Session Found";
                }
        break;
        case 'DeleteProfileItem':
            ?>
                <script>
                    document.getElementById("header").innerText = "<?php echo fn_lang('DELETEPROFILE'); ?>";
                </script>
                <!-- START Container Contents -->
            <div class="container">
                <?php
            if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['record'])) {
                $QUESTID = isset($_GET['record']) && is_numeric($_GET['record']) ? intval($_GET['record']) : 0;
                $delPro=$con->prepare("DELETE
                        FROM 
                            teacher_items 
                        WHERE 
                            item_id=?
                            ");
                $delPro->execute(array($QUESTID));
                //check if the delete is success or not
                if ($delPro->rowCount() > 0) {
                    $msg = $delPro->rowCount() . " Profile Deleted Successfully";
                } else {
                    $msg = "Error in deleting Profile";
                }
                //echo delete message
                ?>
                <!-- Page Heading -->
                <div class="question" style='width: 100%;margin:20px;padding:20px;'>
                    <?php fn_redirect($msg, 'danger', 3, "back"); ?>
                </div>
            <?php
            }
            
        break;
        default:
        # code...
        break;
    }
} else {
    fn_redirect("Not authorized Profile login", "danger", 3, "../index.html");
    end($_SESSION);
    session_unset(); ///Unset The Data
    session_destroy(); //Destroy The Data
    exit();
} ?>
<script src="<?php echo $js; ?>profile.js"></script>
<?php
include $tpl . 'footer.php';
?>