document.addEventListener("DOMContentLoaded", function () {
  console.log("Chart Data:", kaemChartData);
  if (typeof kaemChartData === "undefined") {
    console.log("No data found");
    return;
  }

  let labels = Object.keys(kaemChartData);
  let values = Object.values(kaemChartData);

  const canvas = document.getElementById("kaemChart");

  if (!canvas) {
    console.log("Canvas not found");
    return;
  }

  const ctx = canvas.getContext("2d");

  new Chart(ctx, {
    type: "line",
    data: {
      labels: labels,
      datasets: [
        {
          label: "Monthly Bookings",
          data: values,
          borderWidth: 2,
          fill: false,
          tension: 0.3,
        },
      ],
    },
    options: {
      responsive: true,
    },
  });
});
