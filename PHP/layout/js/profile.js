// Auther: Walid Bakr
// Date: 2024-07-15
// Last Update: 2025-03-28
// Description: Profile Page JS




//******************************************************** */
//********       Start Profile          ****************** */
//******************************************************** */

//prepare Tooltip
const inputField= document.querySelectorAll('.inpData');
  

inputField.forEach((input)=>{
  input.addEventListener("mouseover", function(){
    this.children[2].removeAttribute("hidden");
    this.children[2].style.display="inline-block";
  });
  input.addEventListener("mouseleave", function(){
    this.children[2].setAttribute("hidden","ture");
    this.children[2].style.display="none";
    
  });
});
// Enhanced Bar dealing
const passChecker = document.querySelector("#passwordcheck");
const profilePass = document.querySelector(".enhanced_bar.password");
const passGroup = document.querySelector(".passGroup");
passChecker.addEventListener("click", function (ele) {
  
  profilePass.classList.toggle("active");
  if (this.checked === true) {
    //show the group
    passGroup.style.display = "block";
    passGroup.removeAttribute("hidden");
    //change action on input to be required
    let items=document.querySelectorAll(".inp.password");
    items.forEach(ele => {
      ele.setAttribute("required","");
    });
  } else {
    //Hide the group
    passGroup.style.display = "none";
    passGroup.setAttribute("hidden","");
    //change action on input to be NOT-required
    let items=document.querySelectorAll(".inp.password");
    items.forEach(ele => {
      ele.removeAttribute("required");
      ele.classList.remove("wrong")
    });
  }
});

//upload image in avatar area after onload
const avatarFile=document.querySelector('.avatar');
const avatarImage=document.querySelector('.avatarImage');

avatarFile.addEventListener("change",function(){
  const file=this.files[0]; //get the uploaded file
  
  if(file){
    const reader = new FileReader();
    reader.onload = function(e){
      avatarImage.src = e.target.result;
      avatarImage.style.display ='block';
    }
    reader.readAsDataURL(file);
  }else{
    avatarImage.src ="";
  }
});


// Object to collect form items data
const regFormObj = {
  fName: "",
  fNameAr: "",
  email: "",
  phone: "",
  whatsApp: "",
  userOldPass: "",
  userPass: "",
  userPassConfirm: "",
};
const regFormObjTeach = {
  type: "",
  stage: "",
  subject: "",
  grade: "",
  lang: "",
  rev: "",
};

//assign form items to Object items
regFormObj.fName = document.getElementById("nameEn");
regFormObj.fNameAr = document.getElementById("nameAr");
regFormObj.email = document.getElementById("email");
regFormObj.phone = document.getElementById("tel");
regFormObj.whatsApp = document.getElementById("whats");
regFormObj.userOldPass = document.getElementById("oldpassword");
regFormObj.userPass = document.getElementById("newpassword");
regFormObj.userPassConfirm = document.getElementById("confirmPassword");

//assign form Teaching items to Object items
// regFormObjTeach.type = document.querySelectorAll("select.type");
regFormObjTeach.type = document.getElementsByClassName("type");
regFormObjTeach.stage = document.getElementsByClassName("stage");
regFormObjTeach.subject = document.getElementsByClassName("subject");
regFormObjTeach.grade = document.getElementsByClassName("grade");
regFormObjTeach.lang = document.getElementsByClassName("lang");

//******************************************************** */
//******************************************************** */
//******************************************************** */
//********     Listen to inputs         ****************** */
//******************************************************** */
// keypressed for Eng and Arabic name fields
const enInp = document.getElementById("nameEn");
const arInp = document.getElementById("nameAr");
let inpValueEn = enInp.value;
let inpValueAR = arInp.value;
//START CHECK LANG
// //Arabic Letters check
const arRegEX = /^[\u0600-\u06ff\s]+$/; //Arabic charachters
arInp.addEventListener("keypress", function (e) {
  if(!arRegEX.test(e.key)){
    e.preventDefault();
  }
});
arInp.addEventListener("keyup", function (e) {
  inpValueAR=this.value; //no special code needed for space
});
//English Letters check
enInp.addEventListener("keypress", function (e) {
  if(arRegEX.test(e.key)){
    e.preventDefault();
  }
});
enInp.addEventListener("keyup", function (e) {
  if (e.key===" "){
    this.value=this.value + " "; //add space
  }else{
  inpValueEn=this.value;
  this.value=inpValueEn;
  }
});
//******************** */
//   END CHECK LANG    */
//******************** */
//* ********************************** */
//*     START CHECK INPUTS VALIDITY    */
//* ********************************** */
for (const key in regFormObj) {
  regFormObj[key].addEventListener("input", function (event) {
    if (this.value !== "") {
      regFormObj[key].dataset.state = "valid";
    } else {
      regFormObj[key].dataset.state = "invalid";
    }
  });
  regFormObj[key].addEventListener("focusout", function (event) {
    if (this.value !== "") {
      //focus out from passwords input
      if (this.id === "oldpassword") {
        document.getElementById("newpassword").type = "password";
      }
      if (this.id === "newpassword") {
        document.getElementById("confirmPassword").value = "";
      }
    if (this.value !== "") {
      let result;
      switch (this.type) {
        //check the text fields didn't contain special chars
        case "text":
          this.classList.remove("wrong");
          if (this.classList.contains("tel")) {
            regExp = /[0-9]|\+/gi;
            result = this.value.match(regExp) || [];
            if (result.length == this.value.length) {
              this.classList.remove("wrong");
            } else {
              this.classList.add("wrong");
            }
          } 
          if (this.classList.contains("tel")) {
            (this.value.length <11)? 
            this.classList.add("wrong"):this.classList.remove("wrong");
          } 
          break;
        case "email":
          emailRegExp = /^[^\.\s]\w+@\w+\.\w{2,}/gi;
          result = this.value.match(emailRegExp) || [];
          if (result.length) {
            this.classList.remove("wrong");
          } else {
            this.classList.add("wrong");
          }
          break;
        case "password":
          if(this.value===""){
            this.classList.add("wrong");
            this.dataset.state = "invalid";
            console.log("Here");
          }else{
            this.classList.remove("wrong");
            this.dataset.state = "valid";
          }
          if (this.id === "confirmPassword") {
            if (this.value === document.getElementById("newpassword").value) {
              this.classList.remove("wrong");
              this.dataset.state = "valid";
            } else {
              this.classList.add("wrong");
              this.dataset.state = "invalid";
              alert("passwords don't match!");
              document.getElementById("oldpassword").value="";
              document.getElementById("oldpassword").classList.add("wrong");
              document.getElementById("oldpassword").dataset.state = "invalid";
              document.getElementById("newpassword").value="";
              document.getElementById("newpassword").classList.add("wrong");
              document.getElementById("newpassword").dataset.state = "invalid";
              document.getElementById("confirmPassword").value="";
            }
          }
          break;
        default:
          break;
      }
    }}else{
      this.classList.add("wrong");
    }
  });
};
/* ************************************ */
/*    END CHECK INPUTS VALIDITY         */
/* ************************************ */
/* ************************************ */
/*    START TEACHING ITEMS              */
/* ************************************ */

  //******************************************************** */
  //********      Type of Education Bar   ****************** */
  //******************************************************** */
  let edu = document.querySelector(".teaching"); // counter for teaching items number
  let type=   edu.children[0].children[1].children[0];
  let stage=  edu.children[1].children[1].children[0];
  let subject=edu.children[2].children[1].children[0];
  let grade=  edu.children[3].children[1].children[0];
  let lang=   edu.children[4].children[1].children[0];
    
  // changing type of education select bar
  type.addEventListener("change", (ele) => {
  //called when change type of Education
  // in case of STEM enable Senior only
  if (ele.target.value == "2") {
    stage.children[1].setAttribute("hidden",""); // hide stage Ememantry
    stage.children[2].setAttribute("hidden",""); // hide stage Middle
  } else {
    stage.children[1].removeAttribute("hidden"); // show stage Ememantry
    stage.children[2].removeAttribute("hidden");// show stage Middle
  }
  // in case of STEM or SAT or IG Disable Arabic Lang in LANG BAR
  if (ele.target.value == "1") {
    // lang.parentElement.parentElement.style.display = "block"; // Enable lang
    lang.children[1].children[0].disabled = false; // Enable lang Arabic INPUT
    lang.children[1].style.color =getComputedStyle(document.documentElement).getPropertyValue('--Lite-grey-color'); // Enable lang Arabic LABEL
  } else {
    lang.children[1].children[0].checked =false; // Uncheck lang Arabic INPUT
    lang.children[1].children[0].disabled = true; // disable lang Arabic INPUT
    lang.children[1].style.color =
    getComputedStyle(document.documentElement).getPropertyValue('--Dark-Green-color'); // disable lang Arabic LABEL
  }
  // in case of choosing type(0) delete all    
  if (ele.target.value == "0") {
    stage.parentElement.parentElement.setAttribute("hidden",""); //disable Stage
    subject.parentElement.parentElement.setAttribute("hidden",""); //disable subjects
    grade.parentElement.parentElement.setAttribute("hidden",""); //disable Grade
    lang.parentElement.parentElement.setAttribute("hidden","");// disable lang
    type.children[0].style.color = "black"; // title of type is active    
    type.dataset.state = "valid";
    stage.dataset.state = "valid";
    subject.dataset.state = "valid";
    grade.dataset.state = "valid";
    lang.dataset.state = "valid";
  }else{
      //Disable all choices but Stage
      stage.parentElement.parentElement.removeAttribute("hidden"); //enable Stage
      subject.parentElement.parentElement.setAttribute("hidden",""); //disable subjects
      grade.parentElement.parentElement.setAttribute("hidden",""); //disable Grade
      lang.parentElement.parentElement.setAttribute("hidden","");// disable lang
      type.children[0].style.color = "gray"; // title of type changing color inactive
      stage.dataset.state = "invalid";
      stage.indexSelected=0;
      stage.children[0].disabled = false;
      stage.children[0].selected = true;
      stage.children[0].disabled = true;
    }
  });
    // ******************************************************** */
    // ******************************************************** */
    // ********              Stage          ****************** */
    // ******************************************************** */
    stage.addEventListener("change", (ele) => {
      //ele is Stage SELECT options
      ele.target.dataset.state = "valid";
      subject.dataset.state = "invalid";
      grade.dataset.state = "invalid";
      lang.dataset.state = "invalid";
      subject.children[0].selected = true;
      subject.children[0].disabled = true;
      subject.parentElement.parentElement.removeAttribute("hidden"); //Enable subjects
      grade.parentElement.parentElement.setAttribute("hidden",""); //disable Grade
      lang.parentElement.parentElement.setAttribute("hidden","");// disable lang
      getSubjectItems(ele, subject.children[0], grade.children[0]);
    });
    // //******************************************************** */

    // //******************************************************** */
    // //********              subject         ****************** */
    // //******************************************************** */

    subject.addEventListener("change", (ele) => {
      //ele is Subject options
      ele.target.dataset.state = "valid";
      grade.dataset.state = "invalid";
      lang.dataset.state = "invalid";
      grade.children[0].selected = true;
      grade.children[0].disabled = true;
      grade.parentElement.parentElement.removeAttribute("hidden"); //Enable Grade
      lang.parentElement.parentElement.setAttribute("hidden",""); // disable lang
      // getSubjectItems(type, ele);
    });
    // //******************************************************** */

    // //******************************************************** */
    // //********              Grade         ****************** */
    // //******************************************************** */
    grade.addEventListener("change", (ele) => {
      ele.target.dataset.state = "valid";
      lang.dataset.state = "invalid";
      lang.children[0].disabled = true;
      lang.children[0].selected = true;
      lang.parentElement.parentElement.removeAttribute("hidden"); // enable lang
      lang.parentElement.parentElement.style.display = "block"; // Enable lang
    });
    // //******************************************************** */

    // //******************************************************** */
    // //********              Lang            ****************** */
    // //******************************************************** */
    lang.addEventListener("change", (ele) => {
      grade.dataset.state = "valid";
      const checkbox = document.querySelectorAll("[type='checkbox']");
      let counter = 0;
      // event.children[5].style.display = "block";// enable lang
      for (let index = 0; index < checkbox.length; index++) {
        if (!checkbox[index].checked) {
          counter++;
        }
      }
      if (counter === 3)
        lang.dataset.state = "invalid";
      else lang.dataset.state = "valid";
    });
    //******************************************************** */
  
//******************************************************** */
//********      Function GetSubjects    ****************** */
//******************************************************** */

// adjust subject and grade for your stage selection
function getSubjectItems(fstage, fsubject, fgrade) {
  //function to replace Stage and adjust subject andgrade items
  let SubjectList = {
    //elementary
    elementary: {
      0: "=== Select Subject ===",
      1: "Science",
      2: "Math's",
    },
    middle: {
      0: "=== Select Subject ===",
      1: "Science",
      2: "Math's",
    },
    senior: {
      0: "=== Select Subject ===",
      1: "Physics",
      2: "Chemistry",
      3: "Biology",
      4: "Geology",
      5: "Maths",
    },
  };
  let SubjectValues = {
    //elementary
    elementary: {
      0: "0",
      1: "SCI",
      2: "MAT",
    },
    middle: {
      0: "0",
      1: "SCI",
      2: "MAT",
    },
    senior: {
      0: "0",
      1: "PHY",
      2: "CHE",
      3: "BIO",
      4: "GEO",
      5: "MAT",
    },
  };
  let gradeList = {
    //elementary
    elementary: {
      0: "=== Select Grade ===",
      1: "Grade 1",
      2: "Grade 2",
      3: "Grade 3",
      4: "Grade 4",
      5: "Grade 5",
      6: "Grade 6",
    },
    middle: {
      0: "=== Select Grade ===",
      1: "Grade 7",
      2: "Grade 8",
      3: "Grade 9",
    },
    senior: {
      0: "=== Select Grade ===",
      1: "Grade 10",
      2: "Grade 11",
      3: "Grade 12",
    },
  };
  let gradeValues = {
    //elementary
    elementary: {
      0: "0",
      1: "G1",
      2: "G2",
      3: "G3",
      4: "G4",
      5: "G5",
      6: "G6",
    },
    middle: {
      0: "0",
      1: "G7",
      2: "G8",
      3: "G9",
    },
    senior: {
      0: "0",
      1: "G10",
      2: "G11",
      3: "G12",
    },
  };

  // according to choice from stage we switch itemes
  let X;
  switch (fstage.target.value) {
    case "1":
      X = "elementary";
      break;
    case "2":
      X = "middle";
      break;
    case "3":
      X = "senior";
      break;
  }

  //remove all subject items
  while (subject.firstChild) {
    subject.removeChild(subject.firstChild);
  }
  // add new subject items according to your select
  for (i = 0; i < Object.keys(SubjectList[X]).length; i++) {
    var opt = document.createElement("option");
    opt.value = SubjectValues[X][i];
    opt.innerHTML = SubjectList[X][i];
    subject.appendChild(opt);
  }

  //remove all Grade items
  while (grade.firstChild) {
    grade.removeChild(grade.firstChild);
  }

  // add new Grade items according to your select
  for (i = 0; i < Object.keys(gradeList[X]).length; i++) {
    var opt = document.createElement("option");
    opt.value = gradeValues[X][i];
    opt.innerHTML = gradeList[X][i];
    grade.appendChild(opt);
  }
};
//******************************************************** */



/* ************************************ */
/*    START TEACHING ITEMS              */
/* ************************************ */


//************************************ */
//     START Submit button click       */
//************************************ */
//Check before submit button
submitBtn.onclick = function (e) {
    for (const key in regFormObj) {
      if (regFormObj[key].dataset.state == "invalid" || regFormObj[key].classList.contains("wrong")) {
        regFormObj[key].focus();
        alert("Error Personal Data Entery");
        e.preventDefault();
        break;
      }
    }
    for (const key in regFormObjTeach) {
      if (regFormObjTeach[key][1]&&regFormObjTeach[key][1].dataset.state == "invalid") {
        regFormObjTeach[key][1].focus();
        alert("Invalid teaching info Entery");
        e.preventDefault();
        break;
      }
    }
  };
  
//************************************ */
//     END Submit button click         */
//************************************ */

//******************************************************** */
//********       END Profile            ****************** */
//******************************************************** */
