<?php
// Auther: Walid Bakr
// Date: 2024-07-15
// Last Update: 2025-06-07
// Description: PHP Page NAVIGATION UPPER BAR

//Avatar location
$avatarimgDir=$imgDir."profile/";

//Get the teacher from DB if Found
$stmt = $con->prepare("SELECT * FROM teachers WHERE ename = ?");
$stmt->execute(array($_SESSION['teacher_name']));
$teacherDB = $stmt->fetch();
if (!$teacherDB) {
    // Optionally redirect, show error, or set defaults
    echo "<div class='alert alert-danger'>Teacher not found in database.</div>";
    // You can also exit or return here if you want to stop further execution
    die();
}
//Get the REVISOR CASE FOR teacher from DB
$stmtrev = $con->prepare("SELECT * FROM
                      teacher_items
                    WHERE
                      teacher_id=?
                    AND
                      account_status=?
                    ");
$stmtrev->execute(array($_SESSION['teacher_id'], 1));
$revisor_checker = $stmtrev->fetch();
//Adjust the langauge for default or Eng and adjust the first Entery page to Dash
$langset = isset($_GET["Langset"]) ? $_GET["Langset"] : "Eng";
$do = isset($_GET['do']) ? $_GET['do'] : "Dash";
//@ IS_ADMIN checkes user state
//@ 1 for ADMIN
//@ 2 for USER
//@ 3 for MODERATOR
define('IS_ADMIN', $teacherDB['groupid'] !== 2 ? $teacherDB['groupid'] : 2);
//@ IS_REVISOR checkes user state
//@ REV for REVISOR
//@ NULL for NON_REVISOR
define('IS_REVISOR', $revisor_checker['revision'] === "REV" ? 1 : 0);
?>
<nav class="navbar  navbar-expand-lg bg-body-tertiary bg-madina" dir="<?php if ($langset == "Eng") {
  echo "ltr";
  } else {
    echo "rtl";
  }?>" data-bs-theme="dark">
  <div class="container">
    <button class="navbar-toggler " type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <ul class="navbar-nav mb-2 mb-lg-0">
      <li class="nav-item mb-2 mb-lg-0">
        <a class="nav-link active" aria-current="page" href="homepage.php?Langset=<?php echo $langset; ?>"><?php echo fn_lang('HOME_ADMIN') ?></a>
      </li>
    </ul>
    <div class="collapse navbar-collapse " id="navbarSupportedContent">
      <ul class="navbar-nav  mb-2 mb-lg-0">
        <li class="nav-item mb-2 mb-lg-0"><a class="nav-link" href="profile.php?do=Profile&Langset=<?php echo $langset; ?>"><?php echo fn_lang('USERPROFILE') ?></a></li>
        <li class="nav-item mb-2 mb-lg-0"><a class="nav-link" href="homepage.php?do=Myquest&Langset=<?php echo $langset; ?>"><?php echo fn_lang('QUESTIONS') ?></a></li>
        <div class="langcont">
          <li class="nav-item mb-2 mb-lg-0">
            <p class="nav-link"><?php echo fn_lang('LANG') ?></p>
          </li>
          <li class="nav-item mb-2 mb-lg-0 lang ">
            <a class="nav-link <?php if ($langset == "Eng")
                                  echo "active"; ?>" href="?<?php echo "do=Dash&" ?>Langset=Eng"><?php echo fn_lang('ENGLISH') ?></a>
            <a class="nav-link  <?php if ($langset == "Ar")
                                  echo "active"; ?>" href="?<?php echo "do=Dash&" ?>Langset=Ar"><?php echo fn_lang('ARABIC') ?></a>
          </li>
        </div>
      </ul>
    </div>
    <div>
      <ul class="nav navbar-nav navbar-right">
        <?php if (IS_ADMIN != 2) { ?>
          <li class="nav-item" style="color:white;padding:10px 30px;">
            <div class="admin addtip" data-tooltip="Revert to Admin Page">
              <a href="admin.php"><i class="fa-solid fa-user-tie fa-2x"></i></a>
            </div>
          </li>
        <?php } ?>
        <?php if (IS_REVISOR == 1) { ?>
          <li class="nav-item" style="color:white;padding:10px 30px;">
            <div class="revisor addtip" data-tooltip="Revert to Revisor Page">
              <!-- Get Data from DB file -->
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
              //Get the DB row for the revisor Questions Number Fresh (IN RED)
              $cart = $con->prepare("SELECT * FROM questions_create
                WHERE
                  questions_create.question_case=?
                AND
                  (
                  (revisor_one=? AND cart1=?)
                OR 
                  (revisor_two=? AND cart2=?)
                  )
                AND
                  (pilot = ? OR store = ?)
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
              $cart->execute(array(1,$_SESSION['teacher_id'],'1', $_SESSION['teacher_id'],"1",0,0,$activeAccount['type'],$activeAccount['stage'],$activeAccount['grade'],$activeAccount['subject'],$activeAccount['lang']));
              $result = $cart->rowCount();
              //Get the DB row for the reviewer Questions Number rereview(IN RGEEN)
              $readyrevisor = $con->prepare("SELECT * 
                FROM 
                  question_review
                INNER JOIN
                  questions_create
                ON
                  questions_create.question_id = question_review.question_id
                WHERE
                  revisor_id= ?
                AND
                  review_status= ?
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
                  ");
                  $readyrevisor->execute(array($_SESSION['teacher_id'],3,$activeAccount['type'],$activeAccount['stage'],$activeAccount['grade'],$activeAccount['subject'],$activeAccount['lang']));
              $revisorresult = $readyrevisor->rowCount();
              ?>
              <div class="glasses_child" <?php echo !$result  ? "hidden" : ""; ?>><?php echo $result ?></div>
              <div class="revisor_child" <?php echo !$revisorresult  ? "hidden" : ""; ?>><?php echo $revisorresult ?></div>
              <a href="homepage.php?do=Revisorpage&Langset=<?php echo $langset ?>"><i class="fa-solid fa-glasses fa-2x"></i></a>
            </div>
          </li>
        <?php } ?>
        <?php if (!empty($teacherDB['avatar'])) { ?>
          <li class="nav-item" style="color:white;padding:10px 30px;">
            <div class="avatar"><img 
            src="<?php echo $avatarimgDir . $teacherDB['avatar']; ?>" 
            style="width: 35px;height: 35px; border: 2px solid white; border-radius: 50%;"
            alt="">
            </div>
            </li>
            <?php } ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <?php
              if ($langset == "Eng") {
                $welname = $teacherDB['ename'];
                $splitname = explode(" ", $welname);
                  if (count($splitname) >1) {
                    //english name extract 1st character from 1st and last name
                    echo strtoupper($splitname[0][0] . " " . $splitname[count($splitname) - 1][0]);
                  } else {
                    //if only one name is given, just take the first letter
                    echo strtoupper($splitname[0][0]);
                  }
              } else {
                //arabic name extract 1st character from 1st and last name
                //by special function called preg_split for arabic symbols
                $welname = $teacherDB['aname'];
                $splitname = preg_split('/[\s]+/', $welname, -1, PREG_SPLIT_NO_EMPTY);
                $f1 = preg_split('//u', $splitname[0], -1, PREG_SPLIT_NO_EMPTY);
                $f2 = preg_split('//u', $splitname[count($splitname) - 1], -1, PREG_SPLIT_NO_EMPTY);
                echo $f1[0] . " " . $f2[0] . " ";
              }
              ?>
            </a>
            <ul class="dropdown-menu">
              <li>
                <a class="dropdown-item" href="profile.php?do=Profile&langset=<?php echo $langset; ?>">
                  <?php echo fn_lang('PROFILE') ?>
                </a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <a class="dropdown-item" href="homepage.php?do=Video&Langset=<?php echo $langset; ?>">
                  <?php echo fn_lang('VIDEO') ?>
                </a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item" href="logout.php"><?php echo fn_lang('EXIT') ?></a></li>
            </ul>
        </li>
      </ul>
    </div>


  </div>
</nav>