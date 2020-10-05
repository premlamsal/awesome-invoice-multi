<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Stock</h1>
    <p class="mb-4">
      <!-- DataTales Example -->
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary" style="display: inline-block;">Stock for {{product.name}}</h6>
          <!-- <span>{{isLoading}}</span> -->
          <div class="searchTable">
            <!-- Topbar Search -->
            <!-- <div class="input-group"> -->
            <!--   <div class="input-group no-border">
                        <input type="text" value="" class="form-control" placeholder="Search..." v-model="searchTableKey">
                        <div class="input-group-append">
                          <div class="input-group-text">
                            <i class="nc-icon nc-zoom-split" @click="searchTableBtn"></i>
                          </div>
                        </div>
                      </div>
                    -->
            <!-- </div> -->
          </div>
        </div>
        <div class="card-body" v-if="productDetails.length > 0">
          <div class="table">
            <table class="table table-striped table-bordered" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Product</th>
                  <th>Action</th>
                  <th>Purchase/Sales ID</th>
                  <th>Purchase Quantity</th>
                  <th>Sale Quantity</th>
                  <th>Price</th>
                  <th>In Stock</th>
                  <th>Unit</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <!--     <th>Grand Total</th>
                      <th>Rs. {{grandTotal}}</th> -->
                </tr>
              </tfoot>
              <tbody>
                <tr v-for="productDetail in productDetails" v-bind:key="productDetail.id">
                  <td>{{productDetail.updated_at}}</td>
                  <td>{{productDetail.product.name}}</td>
                  <td>{{productDetail.action}}</td>
                  <td v-if="productDetail.action==='Purchase'" style="background: #4CAF50;color:white">PUR-{{productDetail.action_id}}</td>
                  <td v-else style="background: #F44336;color:white">INV-{{productDetail.action_id}}</td>
                  <td>{{productDetail.purchase_quantity}}</td>
                  <td>{{productDetail.sale_quantity}}</td>
                  <td>{{productDetail.price}}</td>
                  <td>{{productDetail.in_stock}}</td>
                  <td>{{productDetail.unit.short_name}}</td>
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

      productDetails: {},
      id: '',
      pagination: {},

      product: {},


    }
  },
  created() {
    //this block will execute when component created
    this.getIdFromUrl();
    this.fetchProduct();
    this.fetchProductDetail();


  },

  methods: {
    //methods codes here
    fetchProduct() {

      let currObj = this;
      axios.get('/api/product/' + this.id)
        .then(function(response) {


          currObj.product = response.data.product;
          currObj.status = response.data.status;
          // console.log(currObj.product);



        })
        .catch(function(error) {
          if (error.response.status == 422) {
            currObj.validationErrors = error.response.data.errors;
            currObj.errors = currObj.validationErrors;
            // console.log(currObj.errors);
          }
        });


    },
    fetchProductDetail() {

      fetch('api/productDetail/' + this.id)
        .then(res => res.json())
        .then(res => {
          this.productDetails = res.data;
        })
        .catch(err => console.log(err));
    },


    getIdFromUrl() {

      this.id = this.$route.params.id;

    }, //end of getIdFromUrl

    //end of methods block
  },

  computed: {

    // grandTotal: function() {
    //  //reduce function is used to sum the array elements
    //  // this.productDetails.grandTotal= this.productDetails.reduce(function(carry, product) {
    //  // return carry + (parseFloat(product.quantity) * parseFloat(product.price));
    //  //       }, 0);
    //  // return this.productDetails.grandTotal;

    //  },

  }



}
</script>
