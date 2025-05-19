var button2 = document.getElementById("pre");
var button1 = document.getElementById("fresh");

var content1 = document.getElementById("content1");
var content2 = document.getElementById("content2");
var upperhead1 = document.getElementById("heading-fresh");
var upperhead2 = document.getElementById("heading-pre");

upperhead2.style.display = "none";
content2.classList.remove("visible");
content2.classList.add("hidden");

button2.addEventListener("click", (event) => {
  event.preventDefault();

  content1.classList.remove("visible");
  content2.classList.remove("hidden");
  content1.classList.add("hidden");
  content2.classList.add("visible");
  upperhead2.style.display = "block";
  upperhead1.style.display = "none";
});


button1.addEventListener("click", (event) => {
  event.preventDefault();
  content1.classList.remove("hidden");
  content1.classList.add("visible");
  content2.classList.remove("visible");
  content2.classList.add("hidden");
  upperhead1.style.display = "block";
  upperhead2.style.display = "none";
});







let increaseProgress = async (increment = 10) => {
  let scrollprogress = document.getElementById("Atprogressbar");
  let progressvalue = document.getElementById("progressvalue");
  let increaseButton = document.getElementById("increaseButton");

  
  let currentValue = parseInt(progressvalue.textContent);

  let newValue = Math.min(currentValue + increment, 100);

 
  scrollprogress.style.background = `conic-gradient(#008fff ${newValue}%, #c0c0ff ${newValue}%)`;
  progressvalue.textContent = `${newValue}%`;


  if (newValue === 100) {
    increaseButton.disabled = true;
    increaseButton.textContent = "Completed";
  }


  let subjectId = increaseButton.getAttribute("data-subject-id");

  try {
    let response = await fetch("mark_subject_done.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ subjectId: subjectId, progress: newValue }),
    });
    let result = await response.json();
    console.log(result.message);
  } catch (error) {
    console.error("There was a problem with the fetch operation:", error);
  }
};

document.getElementById("increaseButton").onclick = () => increaseProgress(10);
