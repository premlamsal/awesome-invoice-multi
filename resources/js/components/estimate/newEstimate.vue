<template>
  <div>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Create Estimate</h6>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <label>Estimate No.</label>
              Will be generated after Estimate Creation
            </div>
            <div class="form-group" style="position: relative;">
              <label>Customer</label>
              <input type="text" v-model="info.customer_name" v-on:keyup="autoComplete" class="form-control">
              <span v-if="errors['info.customer_name']" :class="['errorText']">{{errors['info.customer_name'][0]}} <br></span>
              <!-- Search suggestion block -->
              <div class="customer-search-suggestion">
                <div v-for="queryResult in queryResults" v-bind:key="queryResult.id" class="customer-search-suggestion-inner">
                  <ul>
                    <li @click="clickSearchSuggestion(queryResult.id,queryResult.name)">{{queryResult.name}}</li>
                  </ul>
                </div>
              </div>
              <b-button id="show-btn" @click="$bvModal.show('bv-modal-add-customer')" class="btn btn-warning" style="margin-top: 8px;">
                <span class="fa fa-plus-circle"></span> Add Customer</b-button>
              <b-modal id="bv-modal-add-customer" hide-footer>
                <template v-slot:modal-title>
                  Add Customer
                </template>
                <div class="d-block">
                  <div class="form-group">
                    <input type="hidden" v-model="customer.id">
                    <label for="Name">Name:</label>
                    <!--  <input type="text"  v-model="customer.name" :class="['form-control', errors.name ? 'is-invalid' : '']"> -->
                    <input type="text" v-model="customer.name" :class="['form-control']">
                    <span v-if="errors.name" :class="['errorText']">{{ errors.name[0] }}</span>
                  </div>
                  <div class="form-group">
                    <label for="Address">Address:</label>
                    <input type="text" v-model="customer.address" :class="['form-control']">
                    <span v-if="errors.address" :class="['errorText']">{{ errors.address[0] }}</span>
                  </div>
                  <div class="form-group">
                    <label for="Phone">Phone:</label>
                    <input type="phone" v-model="customer.phone" :class="['form-control']">
                    <span v-if="errors.phone" :class="['errorText']">{{ errors.phone[0] }}</span>
                  </div>
                </div>
                <b-button class="mt-3" block @click="addCustomer">Add Customer</b-button>
              </b-modal>
              <!-- Search suggestion block ends -->
            </div>
            <div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <!--   <label>Customer Address</label>
            <textarea class="form-control" style="height: 7.6em" v-model="info.client_address"></textarea>
                
            -->
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <label>Title</label>
              <input type="text" v-model="info.title" :class="['form-control']">
              <span v-if="errors['info.title']" :class="['errorText']">{{errors['info.title'][0]}}</span>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <label>Estimate Date</label>
                <date-picker v-model="info.estimate_date" :config="options" :class="['form-control']"></date-picker>
                <!--    <input type="date" v-model="info.estimate_date" :class="['form-control']"> -->
                <span v-if="errors['info.estimate_date']" :class="['errorText']">{{errors['info.estimate_date'][0]}}</span>
              </div>
              <div class="col-sm-6">
                <label>Due Date</label>
                <date-picker v-model="info.due_date" :config="options" :class="['form-control']"></date-picker>
                <!-- <input type="date" v-model="info.due_date" :class="['form-control']"> -->
                <span v-if="errors['info.due_date']" :class="['errorText']">{{errors['info.due_date'][0]}}</span>
                <!-- <span v-if="errors['items'.'due_date']" :class="['errorText']"></span> -->
              </div>
            </div>
          </div>
        </div>
        <hr>
        <table class="table table-bordered table-form">
          <thead>
            <tr>
              <th>Item Name</th>
              <th>Qty</th>
              <th>Unit</th>
              <th>Price</th>
              <th>Total</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item,index) in items">
              <td class="table-name" :class="{'table-error':errors['items.' + index + '.product_name']}">
                <input type="text" :class="['form-control']" placeholder="Product Name" v-model="item.product_name">
              </td>
              <td class="table-quantity" :class="{'table-error':errors['items.' + index + '.quantity']}">
                <input type="number" :class="['form-control']" placeholder="Quantity" v-model="item.quantity">
              </td>
              <td class="table-unit" :class="{'table-error':errors['items.' + index + '.unit']}">
                <template>
                  <select class="form-control" v-model="item.unit">
                    <option selected="" v-for="unit in units">{{unit.short_name}}</option>
                  </select>
                </template>
              </td>
              <td class="table-price" :class="{'table-error':errors['items.' + index + '.price']}">
                <input type="text" :class="['form-control']" placeholder="Enter the price" v-model="item.price">
                <!-- <span v-if="errors['items.' + index + '.price']" :class="['errorText']"></span> -->
                <!-- <span v-if="errors['items.' + index + '.price']" :class="['errorText']">{{errors['items.' + index + '.price']}}</span> -->
              </td>
              <td class="table-total">
                <span class="table-text" v-model="item.line_total">{{item.quantity * item.price}}</span>
              </td>
              <td class="table-remove">
                <button href="" class="btn btn-danger" style="border: none" @click="removeLine(index)"><span class="fa fa-trash"></span></button>
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td class="table-empty" colspan="3">
                <button class="table-add_line btn btn-primary" @click="addNewLine"><span class="fa fa-plus-circle"></span></button>
              </td>
              <td class="table-label">Sub Total</td>
              <td class="table-amount">{{subTotal}}</td>
            </tr>
            <tr>
              <td class="table-empty" colspan="3"></td>
              <td class="table-label">Discount</td>
              <td class="table-discount">
                <input type="text" v-model="info.discount" :class="['table-discount_input form-control']">
                <span v-if="errors['info.discount']" :class="['errorText']">{{errors['info.discount'][0]}}</span>
              </td>
            </tr>
            <tr>
              <td class="table-empty" colspan="3"></td>
              <td class="table-label text-primary" style="font-weight: bold;">Grand Total</td>
              <td class="table-amount" style="font-weight: bold;">{{grandTotal}}</td>
            </tr>
          </tfoot>
        </table>
        <button class="btn btn-success" @click="createEstimate">Create</button>
        <router-link to="/estimates" class="btn btn-danger">Close</router-link>
      </div>
    </div>
  </div>
</template>

<style>
.customer-search-suggestion {
  background: #f2f2f2;
  position: absolute;
  overflow-y: scroll;
  max-height: 9em;
  color: #000;
  border: 1px solid #e2dfdf;
  border-top: 0px;
  width: 100%;
}

.customer-search-suggestion-inner {
  padding: 1px;
  border-top: 1px solid #d6d6d6;
}

.customer-search-suggestion-inner ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.customer-search-suggestion-inner li {
  cursor: pointer;
  padding: 10px;
}

.customer-search-suggestion-inner li:hover {
  background: #007bff;
  color: #fff;
}

.customer-search-suggestion::-webkit-scrollbar-track {
  -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
  background-color: #F5F5F5;
}

.customer-search-suggestion::-webkit-scrollbar {
  width: 6px;
  background-color: #F5F5F5;
}

.customer-search-suggestion::-webkit-scrollbar-thumb {
  background-color: #000000;
}
</style>

<script>
export default {

  data() {

    return {
      items: [{

        product_name: '',
        price: '0',
        quantity: '1',
        line_total: ''

      }],

      info: {},
      customer: {},
      showModal: false,
      queryResults: [],
      errors: [],
      units: [],

      raw: [],

      options: {
        format: 'DD/MM/YYYY',
        useCurrent: true,
        showClear: true,
        showClose: true,
      }, //this variable for the date picker   

    };

  },
  created() {
    //methods to be executed while this page is created
    // this.info.customer_name="";
    // this.info.title="";
    // this.info.due_date="";
    // this.info.estimate_date="";
    this.fetchUnits();


  },

  methods: {
    //fetch(){} //all memeber functions will be created here
    addNewLine() {
      this.items.push({
        product_name: '',
        price: '0',
        quantity: '1',
        line_total: ''

      });

    }, // end of add new line
    removeLine(index) {

      if (index != 0) {
        this.items.splice(index, 1);
      } else {
        // alert('You can\'t delete this');
        this.$toast.error({
          title: 'Opps!!',
          message: 'You can\'t delete this.'
        });

      }


    }, //end of removeLine function

    calLineTotal(index) {


      this.items[index].line_total = this.items[index].price * this.items[index].quantity;

    },
    createEstimate() {

      this.info.status = "To Pay";
      if (this.info.discount == null || this.info.discount == "") {
        this.info.discount = 0;
      }

      let currObj = this;
      axios.post('/api/estimate', { info: this.info, items: this.items })
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          currObj.$swal('Info', currObj.output, currObj.status);
          currObj.$router.push({ name: 'estimates' });
          currObj.errors = ''; //clearing errors



        })
        .catch(function(error) {
          if (error.response.status == 422) {
            currObj.validationErrors = error.response.data.errors;
            currObj.errors = currObj.validationErrors;
            // console.log(currObj.errors);
            currObj.$toast.error({
              title: 'Opps!!',
              message: 'Something Happened.'
            });
          }
        });


    },
    addCustomer() {
      let currObj = this;
      axios.post('/api/customer', this.customer)
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          currObj.$swal('Info', currObj.output, currObj.status);

          currObj.$bvModal.hide('bv-modal-add-customer');


          currObj.customer.name = '';
          currObj.customer.address = '';
          currObj.customer.phone = '';

          currObj.errors = ''; //clearing errors

          currObj.fetchCustomers();

        })
        .catch(function(error) {
          if (error.response.status == 422) {
            currObj.validationErrors = error.response.data.errors;
            currObj.errors = currObj.validationErrors;
            // console.log(currObj.errors);


          }
        });

    },

    autoComplete: _.debounce(function() {

      if (this.info.customer_name === "") {

        this.queryResults = null;

      } else {

        fetch('api/customer_search/', {
            method: 'post',
            body: JSON.stringify({ 'searchQuery': this.info.customer_name }),
            headers: {
              'content-type': 'application/json'
            }
          })
          .then(res => res.json())
          .then(data => {

            this.queryResults = data.queryResults;



          })
          .catch(err => console.log(err));
        console.log();
      }


    }, 500),
    clickSearchSuggestion(customer_id, customer_name) {

      Vue.set(this.info, 'customer_id', customer_id);
      Vue.set(this.info, 'customer_name', customer_name);
      this.queryResults = null;
    },

    displayToastMessage(title, message) {
      this.$toast.error({
        title: title,
        message: message
      });
    },

    fetchUnits() {

      let vm = this; // current pointer instance while going inside the another functional instance
      axios.get('/api/units')
        .then(function(response) {
          vm.units = response.data.data;
        })
        .catch(function(error) {
          console.log();
        });

    },

  }, // end of methods

  computed: {
    //checks errors in the fields


    subTotal: function() {
      //reduce function is used to sum the array elements
      this.info.subTotal = this.items.reduce(function(carry, item) {
        return carry + (parseFloat(item.quantity) * parseFloat(item.price));
      }, 0);
      return this.info.subTotal;

    },

    grandTotal: function() {

      if (this.info.discount != null) {
        return this.subTotal - parseFloat(this.info.discount);
      } else {
        return this.subTotal;
      }

    }


  }, //end of computed

  watch: {



    'info.title': function(val, oldVal) {

      // console.log(this.errors['info.title'][0]);
      if (this.errors != "") {
        this.errors['info.title'][0] = '';
      }


    },

    'info.due_date': function(val, oldVal) {

      // console.log('due_date changes');
      if (this.errors != "") {
        this.errors['info.due_date'][0] = '';
      }

    },

    'info.estimate_date': function(val, oldVal) {

      // console.log('estimate_date changes');
      if (this.errors != "") {
        this.errors['info.estimate_date'][0] = '';
      }

    },

    'info.customer_name': function(val, oldVal) {

      // console.log('customer_name changes');
      if (this.errors != "") {
        this.errors['info.customer_name'][0] = '';
      }

    },


  }, //end of watch



}; //end of export default
</script>
