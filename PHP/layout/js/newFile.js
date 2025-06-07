// Auther: Walid Bakr
// Date: 2024-07-15
// Last Update: 2025-03-08
// Description: Profile Page JS

stem.addEventListener("input", function () {
  if (stem.scrollHeight > scroller) {
    stem.rows += 2;
    scroller = stem.offsetHeight;
  }
  const value = stem.value;
  const selectionStart = stem.selectionStart;

  // Count line breaks before the cursor
  const linesBeforeCursor =
    value.slice(0, selectionStart).split("\n").length - 1;

  // Approximate the row number based on cursor position within the current line
  const currentLine = value.slice(0, selectionStart).split("\n").pop();
  const approximateRow = linesBeforeCursor + currentLine.length / textarea.cols;

  console.log(approximateRow);
  console.log("You are in Line: " + Math.round(approximateRow));
});
