<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Stock History</h1>
    <p class="mb-4">
      <!-- DataTales Example -->
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Stock History</h6>
          <div v-if="isLoading">{{isLoading}}</div>
          <div class="row">
            <div class="col-md-2">
              <label>From</label>
              <date-picker v-model="dateFrom" :config="options"></date-picker>
            </div>
            <div class="col-md-2">
              <label>To</label>
              <date-picker v-model="dateTo" :config="options"></date-picker>
            </div>
            <div class="col-md-2">
              <button @click='btnStockHistory' class="btn btn-success" style="margin-top: 22px;">Show</button>
            </div>
          </div>
          <div class="dateSelector" style="float: left;">
          </div>
        </div>
        <div class="card-body" v-if="stocks.length > 0">
          <div class="table">
            <table class="table table-striped table-bordered" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <!-- <th>ID</th> -->
                  <th>Product ID</th>
                  <th>Quantity</th>
                  <th>Date</th>
                </tr>
              </thead>
              <!--  <tfoot>
                    <tr>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                  </tfoot> -->
              <tbody>
                <tr v-for="stock in stocks" v-bind:key="stock.id">
                  <!-- <td>{{stock.id}}</td> -->
                  <td>{{stock.product.custom_product_id}}</td>
                  <td>{{stock.quantity}}</td>
                  <td>{{stock.date}}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="row">
            <div class="col-md-8">
              <ul class="pagination">
                <li class="page-item" v-bind:class="{disabled:!pagination.first_link}">
                  <button @click="fetchStocks(pagination.first_link)" class="page-link">First</button>
                </li>
                <li class="page-item" v-bind:class="{disabled:!pagination.prev_link}">
                  <button @click="fetchStocks(pagination.prev_link)" class="page-link">Previous</button>
                </li>
                <li v-for="n in pagination.last_page" v-bind:key="n" class="page-item" v-bind:class="{active:pagination.current_page == n}">
                  <button @click="fetchStocks(pagination.path_page + n)" class="page-link">{{n}}</button>
                </li>
                <li class="page-item" v-bind:class="{disabled:!pagination.next_link}">
                  <button @click="fetchStocks(pagination.next_link)" class="page-link">Next</button>
                </li>
                <li class="page-item" v-bind:class="{disabled:!pagination.last_link}">
                  <button @click="fetchStocks(pagination.last_link)" class="page-link">Last</button>
                </li>
              </ul>
            </div>
            <div class="col-md-4">
              Page: {{pagination.current_page}}-{{pagination.last_page}} Total Records: {{pagination.total_pages}}
            </div>
          </div>
        </div>
        <div class="errorDivEmptyData" v-else>
          No Data Found
        </div>
      </div>
  </div>
</template>

<script>
export default {

  data() {
    return {

      stocks: [], //contains all the retrived units from the database

      pagination: {},

      isLoading: '',

      dateFrom: '',

      dateTo: '',

      options: {
        format: 'YYYY-MM-DD',
        useCurrent: true,
        showClear: true,
        showClose: true,
      }, //this variable for the date picker   

    }
  },
  created() {
    //this block will execute when component created
    // this.fetchStockHistory();
  },

  methods: {
    //methods codes here
    fetchStockHistory() {

      this.isLoading = "Loading Data";

      let vm = this; // current pointer instance while going inside the another functional instance

      let formData = new FormData();

      formData.append('_method', 'POST'); //add this otherwise data won't pass to backend

      formData.append('dateFrom', this.dateFrom);

      formData.append('dateTo', this.dateTo);


      axios.post('api/stock/history', formData)

      .then(function(response) {

          vm.stocks = response.data;
          vm.isLoading = '';

        })
        .catch(function(error) {
          console.log();
        });

    },
    btnStockHistory() {

      this.fetchStockHistory();
    }

    //end of methods block
  },

  computed: {

    // grandTotal: function() {
    //  //reduce function is used to sum the array elements
    //  this.stocks.grandTotal= this.stocks.reduce(function(carry, stock) {
    //  return carry + (parseFloat(stock.quantity) * parseFloat(stock.product.price));
    //        }, 0);
    //  return this.stocks.grandTotal;

    //  },

  }



}
</script>
