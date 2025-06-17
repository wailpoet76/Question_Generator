// Auther: Walid Bakr
// Date: 2024-07-15
// Last Update: 2025-04-30
// Description: Profile Page JS

$(function () {
  "use strict";
  $(".langselect").click(function () {
    $(".langselect").removeClass("active");
    $(this).toggleClass("active");
    if ($(this).text == "Eng") {
      $langset = "Eng";
    } else if ($(this).text == "Ar") {
      $langset = "Ar";
    }
  });

  $(".sb-items a").click(function () {
    $(".sb-items li").removeClass("active");
    $(this).parent().toggleClass("active");
  });
});

$(".tabs>div").click(function () {
  switch (this.className) {
    case "urgent":
      // <?php
      document.getElementById("tag1").click();
      // ?>
      break;
    case "myquestions":
      document.getElementById("tag2").click();
      break;
    case "newquestion":
      document.getElementById("tag3").click();
      break;
    case "databasequestion":
      document.getElementById("tag4").click();
      break;
    case "collectquestion":
      document.getElementById("tag5").click();
      break;
    default:
      console.log("not good");
      break;
  }
});

//******************************************************** */
//********       Start New question     ****************** */
//********       Setup HTML items       ****************** */
//******************************************************** */

//prepare showTable
const showTable = document.getElementById("showTable");
const showTableTable = document.querySelector(".tableRecords");
if(showTable){
showTable.addEventListener("click", function () {
  showTable.classList.toggle("active");
  showTable.classList.toggle("btn-primary");
  showTable.classList.toggle("btn-success");
  if (showTable.classList.contains("active")) {
    showTableTable.style.display = "block";
  } else {
    showTableTable.style.display = "none";
  }
});
}

//adjust selection of type of education relavents
$("select.type").change(function () {
  $selectedType = $(this).children("option:selected").val();
  $("select.stage").val($selectedType).change();
  $("select.subject").val($selectedType).change();
  $("select.grade").val($selectedType).change();
});

//Delete confirm btn
$(".confirm").click(function () {
  return confirm("Warning! Are you sure?");
});

const reference = document.getElementById("REFERENCE");
const reference_inp = document.getElementById("ref_inp");

//Learning outcome API (My 1st API) added in 2025-06-17
const outcome=document.querySelector(".API");
const outrecall=document.querySelector(".outrecall");
if (outcome) {outcome.addEventListener("change", function () {
  const selectedValue = this.value
    async function sendData() {
  try {
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
});
}

//checker to produce fraction
let divisionChecker = 0;
//checker to Arrow cond
let arrowUpDown = 0;
//checker to Arrow size according to cond width
let arrowSize = "50px";
if (reference) {
  reference.addEventListener("change", function (ele) {
    if (this.value === "none") {
      reference_inp.value = "";
      reference_inp.style.visibility = "hidden";
      reference_inp.style.display = "none";
      reference_inp.disabled = true;
    } else if (this.value === "NA") {
      reference_inp.value = "";//this.value;
      reference_inp.style.visibility = "hidden";
      reference_inp.style.display = "none";
      reference_inp.disabled = true;
    } else if (this.value === "REF") {
      reference_inp.value = "";
      reference_inp.style.visibility = "visible";
      reference_inp.style.display = "block";
      reference_inp.disabled = false;
    }
  });
}

//STARTS Question TextArea typing
const textareas = document.querySelectorAll(".walid_math .textarea");
if (textareas) {
  textareas.forEach((textarea) => {
    textarea.addEventListener("input", function (e) {
      textarea.placeholder = "";
      let value = e.target.value;
      //Start Question TextArea adjust height
      let selectionStart = e.target.selectionStart;
      const nextCursorLine = value.slice(0, selectionStart).split(/\n/).length;
      textarea.rows = nextCursorLine + 2;
      if (textarea.rows < 4) textarea.rows = 4;
      //ENDS Question TextArea adjust height

      //replica contents and empty textarea
      let textValue = e.target.parentNode.children[1];
      textValue.innerHTML = value.replace(/\n/g, "<br>");
    });
    //if pressed key is enter add <br> to div
    textarea.addEventListener("keydown", function (e) {
      if (e.key === "Enter") {
        // e.target.value += "<br>";//produce error in JS
        e.preventDefault(); // Prevent the default behavior of the Enter key
        const cursorPosition = e.target.selectionStart;
        const textBefore = e.target.value.substring(0, cursorPosition);
        const textAfter = e.target.value.substring(cursorPosition);
        e.target.value = textBefore + "\n" + textAfter; // Add a newline character
        e.target.selectionStart = e.target.selectionEnd = cursorPosition + 1; // Move the cursor after the newline  
        //replica contents and empty textarea
        let textValue = e.target.parentNode.children[1];
        textValue.innerHTML = e.target.value.replace(/\n/g, "<br>");
      }
    });
  });
}

//STARTS Question TextArea Button Action
const qtoolbars = document.querySelectorAll(".qtoolbar span");
qtoolbars.forEach((toolButton) => {
  toolButton.addEventListener("click", function (e) {
    // change key case active and inactive
    e.target.classList.toggle("active");
    // Get textArea Related
    textValue =
      e.target.parentNode.parentNode.parentNode.parentNode.children[1]
        .children[0];
        const start = textValue.selectionStart;
        const end = textValue.selectionEnd;
        
        // Insert the text at the cursor
        const before = textValue.value.substring(0, start);
        const after = textValue.value.substring(end, textValue.value.length);

        // Move cursor to end of inserted text
        const cursorPos = start + textValue.length;
        textValue.selectionStart = textValue.selectionEnd = cursorPos;
    if (e.target.classList.contains("active")) {
      switch (e.target.dataset.name) {
        case "1":
          textValue.value = before + "<h4>" + after;
          break;
        case "2":
          textValue.value = before + "<b>" + after;
          break;
        case "3":
          textValue.value = before + "<i>" + after;
          break;
        case "4":
          textValue.value = before + "<u>" + after;
          break;
        case "5":
          textValue.value = before + "<sup>" + after;
          break;
        case "6":
          textValue.value = before + "<sub>" + after;
          break;
        case "7":
          textValue.value = before + "<small>" + after;
          break;
        case "8":
          textValue.value = before + "Ω" + after;
          e.target.classList.toggle("active");
          break;
        case "9":
          textValue.value = before + "<span class='sqrt'>" + after;
          break;
        case "10":
          if (divisionChecker++ === 0) {
            textValue.value = before + "<span class='frac'><span>" + after;
            e.target.style.backgroundColor = "greenyellow";
          } else {
            //add numinator
            divisionChecker = 0;
            textValue.value = before + "</span></span>" + after;
            // e.target.style..removeProperty('background-color');
            e.target.style.backgroundColor = "transparent";
            e.target.classList.toggle("active");
          }
          break;
        case "11":
          textValue.value = before + "&nbsp&nbsp&nbsp&nbsp" + after;
          e.target.classList.toggle("active");
          break;
        case "12":
          textValue.value = before + "<span class='vect'>" + after;
          break;
        case "13":
          textValue.value = before + "<span class='barD'>" + after;
          break;
        case "14":
          textValue.value = before + "<span class='surround'>" + after;
          break;
        case "15":
          textValue.value = before + "<span class='surround round'>" + after;
          break;
        case "16":
          textValue.value = before + "<span class='surround square'>" + after;
          break;
        case "17":
          textValue.value = before + "<span class='surround curly'>" + after;
          break;
        case "18":
          textValue.value = before + "<span class='arr'></span>" + after;
          e.target.classList.toggle("active");
          break;
        case "19":
          textValue.value = before + "<span class='arr'><span class='arrCndUp'>" + after;
          break;
        case "20":
          if (arrowUpDown++ === 0) {
            textValue.value = before + "<span class='arr'><span class='arrCndUp'>" + after;
            e.target.style.backgroundColor = "greenyellow";
          } else {
            //add numinator
            arrowUpDown = 0;
            textValue.value = before + "</span></span>" + after;
            // e.target.style..removeProperty('background-color');
            e.target.style.backgroundColor = "transparent";
            e.target.classList.toggle("active");
          }
          break;
        case "21":
          textValue.value = before + "<span class='arrVert up'></span>" + after;
          e.target.classList.toggle("active");
          break;
        case "22":
          textValue.value = before + "<span class='arrVert down'></span>" + after;
          e.target.classList.toggle("active");
          break;
        default:
          break;
      }
      textValue.focus();
    } else {
      switch (e.target.dataset.name) {
        case "1":
          textValue.value = before + "</h4>" + after;

          break;
        case "2":
          textValue.value = before + "</b>" + after;
          break;
        case "3":
          textValue.value = before + "</i>" + after;
          break;
        case "4":
          textValue.value = before + "</u>" + after;
          break;
        case "5":
          textValue.value = before + "</sup>" + after;
          break;
        case "6":
          textValue.value = before + "</sub>" + after;
          break;
        case "7":
          textValue.value = before + "</small>" + after;
          break;
        case "9":
          textValue.value = before + "</span>" + after;
          break;
          case "10":
          //1st press add dominator
          if (divisionChecker++ === 1) {
            textValue.value = before + "</span><span>" + after;
            e.target.style.backgroundColor = "skyblue";
          }

          break;

        case "12":
        case "13":
        case "14":
        case "15":
        case "16":
        case "17":
          textValue.value = before + "</span>" + after;
          break;
        case "19":
          textValue.value = before + "</span></span>" + after;
          textValue.value += "</span></span>";
          break;
        case "20":
          if (arrowUpDown++ === 1) {
            textValue.value = before + "</span><span class='arrCndDown'>" + after;
            e.target.style.backgroundColor = "skyblue";
          }
          break;

        default:
          break;
      }
      textValue.focus();
    }
  });
});

//Start save AS section

// //function save image
// function saveAsImage() {
//   const divToCapture = document.querySelector(".replica");
//   html2canvas(divToCapture).then((canvas) => {
//     const imageData = canvas.toDataURL("image/jpeg");
//     const link = document.createElement("a");
//     link.download = "Question.jpg";
//     link.href = imageData;
//     link.click();
//   });
// }
// function saveAsPDF() {
//   html2canvas(divToCapture).then((canvas) => {
//     const imgData = canvas.toDataURL("image/png");
//     const pdf = new jsPDF("p", "pt", "a4");
//     pdf.addImage(imgData, "JPEG", 0, 0);
//     pdf.save("Question.pdf");
//   });
// }
//ENDS Question TextArea Button Action

//Question setDate before stem

const dateTag = document.getElementById("crea_date");
// Set the value to a specific date
const timer = setInterval(calltimer, 10000);
if (!dateTag) {
  clearInterval(timer);
}
function calltimer() {
  //get new date
  const now = new Date();
  //adjust current time
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0'); // Ensure two digits
  const day = String(now.getDate()).padStart(2, '0');
  const hours = String(now.getHours()).padStart(2, '0');
  const minutes = String(now.getMinutes()).padStart(2, '0');
  const seconds = String(now.getSeconds()).padStart(2, '0');
    
  const formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`;
  dateTag.value = formattedDateTime;
}
//Question STEM image show and hide
const StemTextarea = document.querySelectorAll("textarea")[0]; //select stem textarea
const image = document.getElementById("image");
const imageEnhBar = document.querySelector(".enhanced_bar.img");
const imagebrowse = document.querySelector(".cloud");
const inputfile = document.getElementById("image_path");
const image_content = document.querySelector(".drop");
const imageArea = document.querySelector(".imageArea");
const msgSize = document.querySelectorAll(".sizeMsg");

if (image) {
  image.addEventListener("click", function (ele) {
    imageEnhBar.classList.toggle("active");
    if (this.checked === true) {
      image_content.style.display = "block";
      StemTextarea.required = false;
    } else {
      image_content.style.display = "none";
      StemTextarea.required = true;
    }
  });
}

if (inputfile) {
  inputfile.addEventListener("change", fn_uploadImage);
}

if (image_content) {
  image_content.addEventListener("dragover", function (e) {
    e.preventDefault();
  });
}

if (image_content) {
  image_content.addEventListener("drop", function (e) {
    e.preventDefault();
    inputfile.files = e.dataTransfer.files;
    fn_uploadImage();
  });
}
function fn_uploadImage() {
  if (inputfile.files[0] && !(inputfile.files[0]["size"] > 2097152)) {
    let imgLink = URL.createObjectURL(inputfile.files[0]);
    imageArea.src = imgLink;
    imageArea.style.display = "block";
    imageArea.style.width = "100%";
    imageArea.style.height = "50%";
    imagebrowse.style.display = "none";
    StemTextarea.required = false;
  } else if (inputfile.files[0] && inputfile.files[0]["size"] > 2097152) {
    imageArea.src = "";
    imageArea.style.display = "none";
    msgSize[0].removeAttribute("hidden");
    imagebrowse.style.display = "block";
    StemTextarea.required = true;
  } else {
    imageArea.src = "";
    imageArea.style.display = "none";
    imagebrowse.style.display = "block";
    StemTextarea.required = true;
    msgSize[0].setAttribute("hidden", "");
  }
}

//Question SUBSTEM image show and hide
const SubstemTextarea = document.querySelectorAll("textarea")[1]; //select substem textarea
const subimage = document.getElementById("subimage");
const subimageEnhBar = document.querySelector(".enhanced_bar.subimg");
const subimagebrowse = document.querySelector(".subcloud");
const subinputfile = document.getElementById("subimage_path");
const subimage_content = document.querySelector(".subdrop");
const subimageArea = document.querySelector(".subimageArea");
if (subimage) {
  subimage.addEventListener("click", function (ele) {
    subimageEnhBar.classList.toggle("active");
    if (this.checked === true) {
      subimage_content.style.display = "block";
    } else {
      subimage_content.style.display = "none";
    }
  });
}

if (subinputfile) {
  subinputfile.addEventListener("change", fn_uploadSubImage);
}

if (subimage_content) {
  subimage_content.addEventListener("dragover", function (e) {
    e.preventDefault();
  });
}

if (subimage_content) {
  subimage_content.addEventListener("drop", function (e) {
    e.preventDefault();
    subinputfile.files = e.dataTransfer.files;
    fn_uploadImage();
  });
}
function fn_uploadSubImage() {
  if (subinputfile.files[0] && !(subinputfile.files[0]["size"] > 2097152)) {
    let subimgLink = URL.createObjectURL(subinputfile.files[0]);
    subimageArea.src = subimgLink;
    subimageArea.style.display = "block";
    subimageArea.style.width = "100%";
    subimageArea.style.height = "50%";
    subimagebrowse.style.display = "none";
  } else if (subinputfile.files[0] && subinputfile.files[0]["size"] > 2097152) {
    subimageArea.src = "";
    subimageArea.style.display = "none";
    msgSize[1].removeAttribute("hidden");
    subimagebrowse.style.display = "block";
  } else {
    subimageArea.src = "";
    subimageArea.style.display = "none";
    subimagebrowse.style.display = "block";
    msgSize[1].setAttribute("hidden", "");
  }
}

//************************************
// Options Area
//************************************

//radio button for correct answer
const radiobtns = document.querySelectorAll(".choiceRadio");
radiobtns.forEach((radio) => {
  radio.addEventListener("click", function (e) {
    if (e.target.checked == true) {
      //select cause item input
      let cause =
        e.target.parentNode.nextElementSibling.nextElementSibling.children[1];
      cause.value = "The Correct Answer";
      fn_clearChoices(radiobtns);
    }
  });
});

function fn_clearChoices(radiobtns) {
  radiobtns.forEach((radio) => {
    if (radio.checked == false) {
      //select cause item input
      let cause =
        radio.parentNode.nextElementSibling.nextElementSibling.children[1];
      if (cause.value == "The Correct Answer") cause.value = "";
    }
  });
}

// section for options and select option image checkbox
const optionDivs = document.querySelectorAll(".optionImage"); //check radio itself
const imageForOption = document.querySelectorAll(".enhanced_bar.op"); //instead blue bar
const Qimagebrowse = document.querySelectorAll(".Q.cloud"); //cloud image for drag anddrop
const Qinputfile = document.querySelectorAll(".Q.option_path"); //File path Holder Input
const Qimage_content = document.querySelectorAll(".drop.Q"); // Label for Qinputfile
const QimageArea = document.querySelectorAll(".Q.imageArea"); // image will show here
const optionsImage = document.querySelectorAll(".optionsImg"); // image will show here
optionDivs.forEach((ele) => {
  ele.addEventListener("click", function (e) {
    imageForOption[ele.dataset.number - 1].classList.toggle("active");
    if (this.checked === true) {
      Qimage_content[ele.dataset.number - 1].style.display = "block";
    } else {
      Qimage_content[ele.dataset.number - 1].style.display = "none";
    }
  });
});
Qinputfile.forEach((eachinput) => {
  eachinput.addEventListener("change", function (e) {
    if (eachinput.files[0] && !(eachinput.files[0]["size"] > 2097152)) {
      let QimgLink = URL.createObjectURL(e.target.files[0]);
      QimageArea[e.target.dataset.number - 1].src = QimgLink;
      QimageArea[e.target.dataset.number - 1].style.display = "block";
      Qimagebrowse[e.target.dataset.number - 1].style.display = "none";
    } else if (eachinput.files[0] && eachinput.files[0]["size"] > 2097152) {
      QimageArea[e.target.dataset.number - 1].src = "";
      QimageArea[e.target.dataset.number - 1].style.display = "none";
      optionsImage[e.target.dataset.number - 1].removeAttribute("hidden");
      Qimagebrowse[e.target.dataset.number - 1].style.display = "block";
    } else if (!eachinput.files[0]) {
      QimageArea[e.target.dataset.number - 1].src = "";
      QimageArea[e.target.dataset.number - 1].style.display = "none";
      Qimagebrowse[e.target.dataset.number - 1].style.display = "block";
      optionsImage[e.target.dataset.number - 1].setAttribute("hidden", "");
    }
  });
});

Qimage_content.forEach((eachQimage_content) => {
  if (eachQimage_content) {
    eachQimage_content.addEventListener("dragover", function (e) {
      e.preventDefault();
    });

    eachQimage_content.addEventListener("drop", function (e) {
      e.preventDefault();
      Qinputfile[eachQimage_content.dataset.number - 1].files =
        e.dataTransfer.files;
      eachQimage_content.style.width = "100%";
      eachQimage_content.style.height = "50%";
      fn_QuploadImageDrop(eachQimage_content.dataset.number - 1);
    });
  }
});

function fn_QuploadImageDrop(element) {
  if (Qinputfile[element].files[0]) {
    let QimgLink = URL.createObjectURL(Qinputfile[element].files[0]);
    QimageArea[element].src = QimgLink;
    QimageArea[element].style.display = "block";
    Qimagebrowse[element].style.display = "none";
  }
}

// Handle send to revision buttons
document.querySelectorAll(".send-to-revision").forEach((button) => {
  button.addEventListener("click", function () {
    const questionId = this.getAttribute("data-question-id");
    // You can add confirmation here if needed
    fetch(`homepage.php?action=sendToRevision&questionId=${questionId}`)
      .then((response) => response.json())
      .then((data) => {
        console.log("Question sent to revision");
        // You can add success feedback here
      })
      .catch((error) => console.error("Error:", error));
  });
});

//******************************************************** */
//********       END New question       ****************** */
//******************************************************** */

//******************************************************** */
//******************************************************** */
//********       Start Edit question    ****************** */
//******************************************************** */
//******************************************************** */

const referenceEdit = document.getElementById("REFERENCE_EDIT");
const reference_inpEdit = document.getElementById("ref_inp_edit");
if (referenceEdit) {
  referenceEdit.addEventListener("change", function (ele) {
    if (this.value === "none") {
      reference_inpEdit.value = "";
      reference_inpEdit.style.visibility = "hidden";
      reference_inpEdit.style.display = "none";
      reference_inpEdit.disabled = true;
    } else if (this.value === "NA") {
      reference_inpEdit.value = "";
      reference_inpEdit.style.visibility = "hidden";
      reference_inpEdit.style.display = "none";
      reference_inpEdit.disabled = true;
    } else if (this.value === "REF") {
      reference_inpEdit.value = "";
      reference_inpEdit.style.visibility = "visible";
      reference_inpEdit.style.display = "block";
      reference_inpEdit.disabled = false;
    }
  });
}

//******************************************************** */
//********       END EDIT question       ****************** */
//******************************************************** */

//******************************************************** */
//********       START VIDEO PAGE       ****************** */
//******************************************************** */
let vidbtns = document.querySelectorAll(".workingarea button");
let vidshow = document.querySelector(".show iframe");
let urlsite=["dm7X2qtPywU","9PKOOS6kfPs","xxTv3_BB1iY","xa0OPhZY06w",
              "jxmqv7Gn-wk","2pSP_yYSSCY","AmnGij6uWjE"];
let urlchoice;
vidbtns.forEach(e => {
  e.addEventListener("click", function () {
    urlchoice=urlsite[this.value];
    vidshow.parentNode.removeAttribute("hidden");
    vidshow.src="https://www.youtube.com/embed/"
      +urlchoice+"?modestbranding=1&rel=0&showinfo=0&controls=1";
    });
    
});

//******************************************************** */
//********         END VIDEO PAGE       ****************** */
//******************************************************** */

//******************************************************** */
//********       START REVISION PAGE       *************** */
//******************************************************** */
document.addEventListener("DOMContentLoaded", function() {
    window.switchTab = function(evt, tabId) {
      const tabs = document.querySelectorAll('.tab');
      const contents = document.querySelectorAll('.tab-content');

      tabs.forEach(tab => tab.classList.remove('active'));
      contents.forEach(content => content.classList.remove('active'));

      evt.currentTarget.classList.add('active');
      document.getElementById(tabId).classList.add('active');
    };
});
function switchTab(evt, tabId) {
  const tabs = document.querySelectorAll('.tab');
  const contents = document.querySelectorAll('.tab-content');

  tabs.forEach(tab => tab.classList.remove('active'));
  contents.forEach(content => content.classList.remove('active'));

  evt.currentTarget.classList.add('active');
  document.getElementById(tabId).classList.add('active');
}
// Handle the click event for the revision checkboxes
const rev_checkbox = document.querySelectorAll(".checker");
const revEnhBar = document.querySelectorAll(".enhanced_bar.rev");

if(rev_checkbox){
  rev_checkbox.forEach((ele) => {
    ele.addEventListener("click", function (e) {
      e.target.nextElementSibling.classList.toggle("active");
      if (this.checked === true) {
        // get input good
        e.target.parentNode.nextElementSibling.style.backgroundColor =" rgba(6, 235, 63, 0.3)";
        // get comment text area
        e.target.parentNode.parentNode.children[2].children[0].value="";
        e.target.parentNode.parentNode.children[2].children[0].setAttribute("hidden", '');
      } else {
        // get input error red
        e.target.parentNode.nextElementSibling.style.backgroundColor =" rgba(255, 0, 0, 0.3)";
        // get comment text area
        e.target.parentNode.parentNode.children[2].children[0].removeAttribute("hidden");
      }
  });
});
};
// Handle send to revision buttons

//Checker for revision controller
const checker = document.querySelectorAll(".checker");
var checkercounter=0;//check if all checkboxes are good
//pre calculate any report items
checker.forEach((ele) => {
  if (ele.checked === false) {
      checkercounter++;
      // if no checkbox is selected activate the accept Question Button
    }
    if(checkercounter){
      document.querySelector(".Accept").disabled = true;
      document.querySelector(".Report").disabled = false;
    }else{
      document.querySelector(".Accept").disabled = false;
      document.querySelector(".Report").disabled = true;
    }
  });
checker.forEach((ele) => {
  ele.addEventListener("click", function (e) {
    if (this.checked === true) {
      checkercounter--;
    } else {
      checkercounter++;
      // if no checkbox is selected activate the accept Question Button
    }
    console.log(checkercounter);
    
    if (checkercounter == 0){//all good
      document.querySelector(".Accept").disabled = false;
      document.querySelector(".Report").disabled = true;
    }else{//some are not good
      document.querySelector(".Accept").disabled = true;
      document.querySelector(".Report").disabled = false;
    }
  });
});
//******************************************************** */
//********         END REVISION PAGE      **************** */
//******************************************************** */
