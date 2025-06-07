// Auther: Walid Bakr
// Date: 2024-07-15
// Last Update: 2024-03-08
// Description: Profile Page JS

function saveAsPDF() {
  const divToCapture = document.querySelector(".replica");
  html2canvas(divToCapture).then((canvas) => {
    const imgData = canvas.toDataURL("image/png");
    const pdf = new jsPDF("p", "pt", "a4");
    pdf.addImage(imgData, "JPEG", 0, 0);
    pdf.save("Question.pdf");
  });
}
