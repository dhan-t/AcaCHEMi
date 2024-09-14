// naccaw ko dito: https://www.youtube.com/watch?v=jqjzM85pcYM

document.addEventListener("DOMContentLoaded", showPieChart);

function showPieChart() {
  console.log("piehcart on load");

  let grade12 = { size: 100, color: "#C3FBF7" };
  let grade11 = { size: 100, color: "#59D9D1" };
  let JHS = { size: 100, color: "#0CA79D" };
  let others = { size: 100, color: "#176762" };

  const values = [grade12.size, grade11.size, JHS.size, others.size];

  const total = values.reduce((acc, val) => acc + val, 0);

  let startAngle = 0;

  // Values of the pie chart %
  const canvas = document.getElementById("pie-chart");
  const ctx = canvas.getContext("2d");

  // Calculate angles
  values.forEach((value, index) => {
    const angle = (value / total) * Math.PI * 2;

    // Draw a slice
    ctx.beginPath();
    ctx.moveTo(canvas.width / 2, canvas.height / 2);
    ctx.arc(
      canvas.width / 2,
      canvas.height / 2,
      canvas.width / 2,
      startAngle,
      startAngle + angle
    );
    ctx.closePath();

    ctx.fillStyle =
      index === 0
        ? grade12.color
        : index === 1
        ? grade11.color
        : index === 2
        ? JHS.color
        : others.color;
    ctx.fill();

    startAngle += angle;
  });

  const legend = document.getElementById("pie-chart-legend");

  legend.innerHTML = `
  <div class="legend-item">
    <div class="legend-color" style="background-color: ${grade12.color}"></div>
    <div class="legend-label">
      Grade 12: ${grade12.size} - ${((grade12.size / total) * 100).toFixed(2)} %
    </div>
  </div>
  <div class="legend-item">
  <div class="legend-color" style="background-color: ${grade11.color}"></div>
  <div class="legend-label">
    Grade 11: ${grade11.size} - ${((grade11.size / total) * 100).toFixed(2)} %
  </div>
</div>
<div class="legend-item">
<div class="legend-color" style="background-color: ${JHS.color}"></div>
<div class="legend-label">
  JHS: ${JHS.size} - ${((JHS.size / total) * 100).toFixed(2)} %
</div>
</div>
<div class="legend-item">
<div class="legend-color" style="background-color: ${others.color}"></div>
<div class="legend-label">
  Others: ${others.size} - ${((others.size / total) * 100).toFixed(2)} %
</div>
</div>
  `;

  //Naccaw from Gemini
  const ctxq = document.getElementById("avgQuizScore").getContext("2d");
  const quizScores = [75, 82, 90, 80, 90];
  const quizLabels = ["Quiz 1", "Quiz 2", "Quiz 3", "Quiz 4", "Quiz 5"];

  function calculateAverage(scores) {
    if (!scores.length) {
      return 0;
    }
    const totalScore = scores.reduce((acc, score) => acc + score, 0);
    const averageScore = totalScore / scores.length;
    return averageScore;
  }
  const average = calculateAverage(quizScores);

  const chartConfig = {
    type: "bar",
    data: {
      labels: quizLabels,
      datasets: [
        {
          label: "Average Score: " + average,
          backgroundColor: ["#C3FBF7", "#59D9D1", "#0CA79D"],
          borderColor: "#444444",
          borderWidth: 1,
          data: quizScores,
        },
      ],
    },
    options: {
      scales: {
        yAxes: [
          {
            ticks: {
              beginAtZero: true,
              max: 100,
            },
          },
        ],
      },
    },
  };

  // Create the chart using Chart.js library
  new Chart(ctxq, chartConfig);
}
