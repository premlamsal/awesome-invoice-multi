<template>
  <div class="container">
    <center>
      <h1></h1></center>
    <canvas ref="chart"></canvas>
  </div>
</template>

<script>
export default {
  mounted() {
    let uri = '/api/sales/chart';
    axios.get(uri).then((response) => {
      var chart = this.$refs.chart;
      var ctx = chart.getContext("2d");
      var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: response.data.month,
          datasets: [{
            label: 'Sales of Last 2 months days',
            data: response.data.data,
            borderWidth: 1,
            backgroundColor: '#f87979',

            // backgroundColor: "transparent",
            borderColor: "rgba(1, 116, 188, 0.50)",
            pointBackgroundColor: "rgba(171, 71, 188, 1)"
          }]
        },
        options: {
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true
              }
            }]
          }
        }
      });
    }).catch(error => {
      console.log(error)
      this.errored = true
    });


  }
}
</script>
