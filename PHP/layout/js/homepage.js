// Auther: Walid Bakr
// Date: 2024-07-15
// Last Update: 2024-03-08
// Description: Profile Page JS


//*********************************************************
// *****    HomePage.php JQ functions         *************
// *********************************************************
// START of function

//function to change content in education choices
let docedutype = document.querySelector(".type");
if (docedutype) {
  docedutype.addEventListener("change", function (ele) {
    $selectedType = ele.target.value;
    $("select.stage").val($selectedType).change();
    $("select.subject").val($selectedType).change();
    $("select.grade").val($selectedType).change();
    $("select.lang").val($selectedType).change();
  });
}
