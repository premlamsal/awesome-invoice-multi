<template>
  <div>
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card card-stats">
          <div class="card-body ">
            <div class="row">
              <div class="col-5 col-md-4">
                <div class="icon-big text-center icon-warning">
                  <i class="nc-icon nc-basket text-warning"></i>
                </div>
              </div>
              <div class="col-7 col-md-8">
                <div class="numbers">
                  <p class="card-category">Purchase</p>
                  <p class="card-title">Rs. {{dash.purchase}}
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer ">
            <hr>
            <div class="stats">
              <router-link to="/purchases" aria-expanded="false">
                <i class="nc-icon nc-basket"></i>
                <span>Purchases</span>
              </router-link>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card card-stats">
          <div class="card-body ">
            <div class="row">
              <div class="col-5 col-md-4">
                <div class="icon-big text-center icon-warning">
                  <i class="nc-icon nc-single-copy-04 text-success"></i>
                </div>
              </div>
              <div class="col-7 col-md-8">
                <div class="numbers">
                  <p class="card-category">Invoices</p>
                  <p class="card-title">Rs. {{dash.invoice}}
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer ">
            <hr>
            <div class="stats">
              <router-link to="/invoices" aria-expanded="false">
                <i class="nc-icon nc-single-copy-04"></i>
                <span>Invoices</span>
              </router-link>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card card-stats">
          <div class="card-body ">
            <div class="row">
              <div class="col-5 col-md-4">
                <div class="icon-big text-center icon-warning">
                  <i class="nc-icon nc-delivery-fast text-danger"></i>
                </div>
              </div>
              <div class="col-7 col-md-8">
                <div class="numbers">
                  <p class="card-category">Suppliers</p>
                  <p class="card-title">{{dash.supplier}}
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer ">
            <hr>
            <div class="stats">
              <router-link to="/suppliers" aria-expanded="false">
                <i class="nc-icon nc-delivery-fast"></i>
                <span>Suppliers</span>
              </router-link>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card card-stats">
          <div class="card-body ">
            <div class="row">
              <div class="col-5 col-md-4">
                <div class="icon-big text-center icon-warning">
                  <i class="nc-icon nc-tile-56 text-primary"></i>
                </div>
              </div>
              <div class="col-7 col-md-8">
                <div class="numbers">
                  <p class="card-category">Products</p>
                  <p class="card-title">
                    {{dash.product}}
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer ">
            <hr>
            <div class="stats">
              <router-link to="/products" aria-expanded="false">
                <i class="nc-icon nc-tile-56"></i>
                <span>Products</span>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card ">
          <div class="card-header ">
            <h5 class="card-title">Sales Report</h5>
            <p class="card-category">sales performance over {{before_month}} months</p>
          </div>
          <div class="card-body ">
            <div id="outerChart">
              <!-- <line-chart
                    v-if="loaded"
                    :chartdata="chartdata"
                    :options="options"/> -->
              <bar-chart v-if="loaded && showBar" :chartdata="chartdata" :options="options" />
              <line-chart v-if="loaded && showLine" :chartdata="chartdata" :options="options" />
            </div>
          </div>
          <div class="card-footer ">
            <hr>
            <div class="row">
              <div class="col-md-4">
                <div class="from-group">
                  <label>Chart Type</label>
                  <select v-model="type" class="from-control" @change="changeSalesChartType">
                    <option value="line">Line</option>
                    <option value="bar">Bar</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="from-group">
                  <label>Records Before</label>
                  <select v-model="before_month" class="from-control" @change="changeSalesChartMonth">
                    <option value="3">3 months</option>
                    <option value="6">6 months</option>
                    <option value="9">9 months</option>
                    <option value="12">12 months</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
   <!--  <div class="row">
          <div class="col-md-6">
            <div class="card ">
              <div class="card-header ">
                <h5 class="card-title">Email Statistics</h5>
                <p class="card-category">Last Campaign Performance</p>
              </div>
              <div class="card-body ">
                
              </div>
              <div class="card-footer ">
                <div class="legend">
                  <i class="fa fa-circle text-primary"></i> Opened
                  <i class="fa fa-circle text-warning"></i> Read
                  <i class="fa fa-circle text-danger"></i> Deleted
                  <i class="fa fa-circle text-gray"></i> Unopened
                </div>
                <hr>
                <div class="stats">
                  <i class="fa fa-calendar"></i> Number of emails sent
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card card-chart">
              <div class="card-header">
                <h5 class="card-title">NASDAQ: AAPL</h5>
                <p class="card-category">Line Chart with Points</p>
              </div>
              <div class="card-body">
               
              </div>
              <div class="card-footer">
                <div class="chart-legend">
                  <i class="fa fa-circle text-info"></i> Tesla Model S
                  <i class="fa fa-circle text-warning"></i> BMW 5 Series
                </div>
                <hr/>
                <div class="card-stats">
                  <i class="fa fa-check"></i> Data information certified
                </div>
              </div>
            </div>
          </div>
        </div> -->
  </div>
</template>

<script>
import BarChart from "../chart/barChart";
import LineChart from "../chart/lineChart";

export default {

  components: {

    BarChart,
    LineChart
  },

  data() {

    return {

      store_id: '',

      showBar: false,
      showLine: true,

      loaded: false,

      chartdata: {

        labels: '',

        datasets: [{

          label: 'Sales over past months',
          data: '',
          borderWidth: 1,
          backgroundColor: [

            "rgb(255, 84, 64,0.6)",
            "rgba(54, 162, 235, 0.6)",
            "rgba(255, 206, 86, 0.6)",
            "rgba(75, 192, 192, 0.6)",
            "rgba(153, 102, 255, 0.6)",
            "rgba(255, 159, 64, 0.6)"
          ],

          // backgroundColor: "transparent",
          borderColor: [

            "rgba(255,99,132,1)",
            "rgba(54, 162, 235, 1)",
            "rgba(255, 206, 86, 1)",
            "rgba(75, 192, 192, 1)",
            "rgba(153, 102, 255, 1)",
            "rgba(255, 159, 64, 1)"
          ],
          // pointBackgroundColor: []
        }]
      },

      options: {
        responsive: true,
        maintainAspectRatio: false
      },

      dash: [],

      type: 'line',


      before_month: '6', //default sales chart will show 6 months records

    }; //end of return

  }, //end of data

  created() {

    this.store_id = this.$cookie.get('store_id');

    // if(!this.store_id==null){
    //   this.$Progress.start();
    this.fetchDash();
    //   this.$Progress.finish();
    // }
    // else{
    //   this.$router.push({ name: 'selectStore'});
    // }

  },



  mounted() {

    this.getSalesChart();

  },

  methods: {

    changeSalesChartType() {

      if (this.type === 'line') {
        this.showBar = false
        this.showLine = true
      } else if (this.type == 'bar') {
        this.showBar = true
        this.showLine = false
      }

    },
    changeSalesChartMonth() {
      this.getSalesChart();
    },

    async getSalesChart() {
      this.loaded = false
      try {
        let resp = await axios.get('/api/sales/chart/' + this.before_month)

        this.chartdata.labels = resp.data.month

        Vue.set(this.chartdata.datasets, 'data', resp.data.data)

        this.chartdata.datasets[0].data = resp.data.data
        this.chartdata.datasets[0].label = 'Sales Data from past ' + this.before_month + ' month(s)'

        this.loaded = true

      } catch (e) {
        // console.error(e)
        // console.log('Somthing happened');
        this.$toast.error({
          title: 'Opps!!',
          message: e.message.toString()
        });
      }
    },
    fetchDash() {

      let currObj = this;

      axios.get('/api/dashInfo')
        .then(function(response) {

          currObj.dash = response.data;

        })
        .catch(function(error) {
          console.log(error);
        });


    },

  },

  computed: {
    //use {{ currentRouteName }} in template or use directly {{ $route.name }} without using computed property
    currentRouteName() {
      return this.$route.name;
    }
  }
}
</script>
