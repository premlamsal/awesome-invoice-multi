<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Stock Adjustments</h1>
    <p class="mb-4">
      <!-- DataTales Example -->
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary" style="display: inline-block;">Stock Adjustments for {{stock.product.name}} || Stock ID: {{stock.id}}</h6>
          <!-- <span>{{isLoading}}</span> -->
        </div>
        <div class="card-body" v-if="stockAdjustments.length > 0">
          <div class="table">
            <table class="table table-striped table-bordered" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Reason</th>
                  <th>Type</th>
                  <th>Quantity</th>
                  <th>Price</th>
                  <th>Notes</th>
                </tr>
              </thead>
             
              <tbody>
                <tr v-for="stockAdjustment in stockAdjustments" v-bind:key="stockAdjustment.id">
                  <td>{{stockAdjustment.date}}</td>
                  <td>{{stockAdjustment.reason}}</td>
                  <td>{{stockAdjustment.type}}</td>
                  <td>{{stockAdjustment.quantity}}</td>
                  <td>{{stockAdjustment.price}}</td>
                  <td>{{stockAdjustment.notes}}</td>
                </tr>
              </tbody>
            </table>
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

      stockAdjustments: {},
      id: '',
      pagination: {},
      stock:{
        product:'',
        id:'',
      },



    }
  },
  created() {
    //this block will execute when component created
    this.getIdFromUrl();
    this.fetchStockDetail();


  },

  methods: {
    //methods codes here
  
    fetchStockDetail() {
        axios.get('api/stock_adjustments/'+this.id)
        .then(response=>{
          this.stockAdjustments=response.data.stock_adjustments;
          this.stock=response.data.stock;
        })
        .catch(error=>{
          console.log(error);
        })
    },


    getIdFromUrl() {
      this.id = this.$route.params.id;
    }, //end of getIdFromUrl

    //end of methods block
  },

  computed: {

  }



}
</script>
