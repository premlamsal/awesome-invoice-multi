<template>
  <div>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Return Invoice</h6>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-sm-4">
            <label>Invoice No.</label>
            {{info.invoice_no}}
            <br />
            <div class="form-group">
              <label>Status</label>
              {{info.status}}
            </div>
            <div class="form-group" style="position: relative;">
              <label>Customer</label>
              {{info.customer_name}}
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
              {{info.title}}
            </div>
            <div class="row">
              <div class="col-sm-6">
                <label>Invoice Date</label>
                {{info.date}}
              </div>
              <div class="col-sm-6">
                <label>Due Date</label>
                {{info.due_date}}
              </div>
            </div>
          </div>
        </div>
        <hr />
        <div class="invoice">
          <div class="invoice-head">
            <div class="row">
              <div class="col-md-1">
                <h6>ID</h6>
              </div>
              <div class="col-md-3">
                <h6>Product Name</h6>
              </div>
              <div class="col-md-1">
                <h6>Quanity</h6>
              </div>
              <div class="col-md-1">
                <h6> Unit</h6>
              </div>
              <div class="col-md-2">
                <h6>Price</h6>
              </div>
              <div class="col-md-2">
                <h6> Total</h6>
              </div>
              <div class="col-md-2">
                <h6> Action</h6>
              </div>
            </div>
          </div>
          <!-- end of invoice head-->
          <div class="invoice-body">
            <div class="invoice-items" v-for="(item,index) in items" v-bind:key="item.id">
              <div class="row">
                <div class="col-md-1" v-if="item.custom_product_id!=null">
                  {{item.custom_product_id}}
                </div>
                <div class="col-md-1" v-else>
                  #
                </div>
                <div class="col-md-3">
                  <div class="auto-complete-product-container">
                    <div class="form-group">
                      <input type="text" class="form-control" placeholder="Product Name" v-model="item.product_name" v-on:keydown="autoCompleteProduct(index)" :class="{'is-invalid':errors['items.' + index + '.product_name']}" />
                      <span v-if="errors['items.' + index + '.product_name']" :class="['errorText']">{{errors['items.' + index + '.product_name'][0]}}</span>
                      <!--  suggestion block -->
                      <div class="product-search-suggestion-invoice" v-for="queryResultsProduct in queryResultsProducts[index]" v-bind:key="queryResultsProduct.id">
                        <ul>
                          <li @click="clickSearchProductSuggestion(queryResultsProduct.id,queryResultsProduct.custom_product_id,queryResultsProduct.name,queryResultsProduct.unit.id,queryResultsProduct.price,index)">{{queryResultsProduct.name}} -></li>
                        </ul>
                      </div>
                      <!--  <span v-if="errors['items.' + index + '.product_name']">
                      {{ errors['items.' + index + '.product_name'] }}
                    </span> -->
                    </div>
                  </div>
                </div>
                <div class="col-md-1">
                  <input type="number" class="form-control" placeholder="Quantity" v-model="item.quantity" v-on:keydown="checkQuantityInStock(index)" @change="checkQuantityInStock(index)" :class="{'is-invalid':errors['items.' + index + '.quantity']}" />
                </div>
                <div class="col-md-1">
                  <template v-if="units.length>0">
                    <select class="form-control" v-model="item.unit_id" v-if="item.product_id" :class="{'is-invalid':errors['items.' + index + '.id']}">
                      <option selected v-for="unit in units" :value="unit.id" v-bind:key="unit.id">{{unit.short_name}}</option>
                    </select>
                  </template>
                  <template v-else>add some unit</template>
                </div>
                <div class="col-md-2">
                  <input type="text" class="form-control" placeholder="Enter the price" v-model="item.price" v-if="item.product_id" :class="{'is-invalid':errors['items.' + index + '.price']}" />
                </div>
                <div class="col-md-2">
                  <span class="table-text">{{item.quantity * item.price}}</span>
                </div>
                <div class="col-md-2">
                  <button href class="btn btn-danger" style="border: none" @click="removeLine(index)">
                    <span class="nc-icon nc-simple-remove" style="font-size: 15px"></span>
                  </button>
                </div>
              </div>
            </div>
            <!-- end of invoice items-->
          </div>
          <!-- end of invoice body-->
          <div class="invoice-foot">
            <div class="row">
              <div class="col-md-2">
                <button class="table-add_line btn btn-primary" @click="addNewLine">
                  <span class="fa fa-plus-circle"></span>
                </button>
              </div>
              <div class="col-md-2">
                <h6>Grand Total</h6> {{grandTotal}}
              </div>
              <div class="col-md-2">
                <h6>Discount</h6>
                <input type="text" class="table-discount_input form-control" v-model="info.discount" :class="{'is-invalid':errors['info.discount']}" />
                <span v-if="errors['info.discount']" :class="['errorText']">{{errors['info.discount'][0]}}</span>
              </div>
              <div class="col-md-2">
                <h6>{{store.tax_percentage}} % Tax</h6> {{taxAmount}}
              </div>
              <div class="col-md-2">
                <h6>SubTotal</h6> {{subTotal}}
              </div>
              <div class="col-md-2">
              </div>
            </div>
          </div>
          <!-- end of invoice foot -->
        </div>
        <!-- end of invoice -->
        <button class="btn btn-success" @click="returnInvoice">Return Invoice</button>
        <router-link to="/invoices" class="btn btn-danger">Close</router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      items: [{
        product_name: '',
        price: '',
        quantity: '',
        product: {
          custom_product_id: '',
          unit: {},
        },
        line_total: '',
        changed: false,
      }],
      cloneItems: [{
        product_name: "",
        price: "0",
        quantity: "1",
        product: {
          custom_product_id: '',
          unit: {},
        },
        line_total: "",
        changed: false
      }],
      info: {},
      id: "",
      customer: {},
      showModal: false,
      queryResults: [],
      queryResultsProducts: [],
      errors: [],
      units: [],

      showProductSuggestion: false,

      options: {
        format: "YYYY-MM-DD",
        useCurrent: true,
        showClear: true,
        showClose: true
      } //this variable for the date picker
    };
  },
  created() {
    //methods to be executed while this page is created
    this.getIdFromUrl();
    this.fetchInvoice();
    this.fetchUnits();
  },
  mounted() {},
  methods: {
    //fetch(){} //all memeber functions will be created here
    addNewLine() {

      this.items.push({
        product_name: '',
        price: '0',
        quantity: '1',
        product: {
          custom_product_id: '',
          unit: {},
        },
        line_total: '',
        changed: false,

      });

      this.cloneItems.push({
        product_name: '',
        price: '0',
        quantity: '1',
        product: {
          custom_product_id: '',
          unit: {},
        },
        line_total: '',
        changed: false,

      });
    }, // end of add new line
    removeLine(index) {
      // this.invoices.remove();
      if (index != 0) {
        this.items.splice(index, 1);
        this.cloneItems.splice(index, 1);
      } else {
        // alert('You can\'t delete this');
        this.$toast.error({
          title: "Opps!!",
          message: "You can't delete this."
        });
      }
    }, //end of removeLine function

    calLineTotal(index) {
      // alert(this.invoices[index].price);
      this.items[index].line_total = this.items[index].price * this.items[index].quantity;
      this.cloneItems[index].line_total = this.items[index].line_total;
    },
    returnInvoice() {
      if (this.info.discount == null || this.info.discount == "") {
        this.info.discount = 0;
      }

      let currObj = this;
      axios
        .put("/api/invoice/return", {
          info: this.info,
          items: this.items,
          id: this.id
        })
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          currObj.$swal("Info", currObj.output, currObj.status);
          currObj.$router.push({ name: "invoices" });
          // currObj.errors = '';//clearing errors
        })
        .catch(function(error) {
          if (error.response.status == 422) {
            currObj.validationErrors = error.response.data.errors;
            currObj.errors = currObj.validationErrors;
            // console.log(currObj.errors);
            currObj.$toast.error({
              title: "Opps!!",
              message: "Something Happened."
            });
          }
        });
    },

    getIdFromUrl() {
      this.id = this.$route.params.id;
    }, //end of getIdFromUrl
    addCustomer() {
      let currObj = this;
      axios
        .post("/api/customer", this.customer)
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          currObj.$swal("Info", currObj.output, currObj.status);

          currObj.$bvModal.hide("bv-modal-add-customer");

          currObj.customer.name = "";
          currObj.customer.address = "";
          currObj.customer.phone = "";
          currObj.customer.details = "";

          currObj.errors = ""; //clearing errors

          currObj.fetchCustomers();
        })
        .catch(function(error) {
          if (error.response.status == 422) {
            currObj.validationErrors = error.response.data.errors;
            currObj.errors = currObj.validationErrors;
            // console.log(currObj.errors);
            currObj.$toast.error({
              title: "Opps!!",
              message: error.response.data.message
            });
          }
        });
    },
    autoComplete: _.debounce(function() {
      let currObj = this;
      if (this.info.customer_name === "") {
        this.queryResults = null;
      } else {
        axios
          .post("api/customer_search", {
            searchQuery: this.info.customer_name
          })
          .then(response => {
            this.queryResults = response.data.queryResults;
          })
          .catch(error => {
            if (error.response.status == 422) {
              currObj.validationErrors = error.response.data.errors;
              currObj.errors = currObj.validationErrors;
              // console.log(currObj.errors);
              currObj.$toast.error({
                title: "Opps!!",
                message: error.response.data.message
              });
            }
          });
      }
    }, 500),

    autoCompleteProduct: _.debounce(function(index) {
      if (this.items[index].product_name === "") {
        this.queryResultsProducts = new Array();
        this.showProductSuggestion = false;
      } else {
        axios
          .post("api/product_search", {
            searchQuery: this.items[index].product_name
          })
          .then(response => {
            this.queryResultsProducts[index] = response.data.queryResults;
            if (this.queryResultsProducts[index].length > 0) {
              this.showProductSuggestion = true;
            } else {
              this.showProductSuggestion = false;
            }
          })
          .catch(error => {
            if (error.response.status) {
              this.errors = error.response.data.errors;
              console.log(this.errors);
            }
          });
      }
      // alert(this.items[index].product_name);
    }, 300),
    clickSearchProductSuggestion(product_id,
      custom_product_id,
      product_name,
      unit_id,
      price,
      index) {


      if (!this.hasItem(product_name)) {
        // console.log("Item not in List. So adding");
        Vue.set(this.items[index], "product_id", product_id);

        Vue.set(this.items[index], "custom_product_id", custom_product_id);

        Vue.set(this.items[index], "product_name", product_name);

        Vue.set(this.items[index], "unit_id", unit_id);

        Vue.set(
          this.items[index],
          "price",
          parseFloat(price) +
          (parseFloat(price) * parseFloat(this.store.profit_percentage)) / 100
        );


        Vue.set(this.cloneItems[index], "product_id", product_id);

        Vue.set(this.cloneItems[index], "custom_product_id", custom_product_id);

        Vue.set(this.cloneItems[index], "product_name", product_name);

        Vue.set(this.cloneItems[index], "unit_id", unit_id);

        Vue.set(
          this.cloneItems[index],
          "price",
          parseFloat(price) +
          (parseFloat(price) * parseFloat(this.store.profit_percentage)) / 100
        );

        // this.items[index] = this.items[index] + (this.store.profit_percentage)/100;

        // console.log(product_id);
        // console.log(product_name);
        // console.log(index);
        this.queryResultsProducts = new Array();

      } else {
        // console.log("Item exits in list so deleting the current index item to remove duplicate entry in items array");
        this.displayToastErrorMessage('Opps', product_name + ' already on the list. You can increase the quantity');


        this.items.splice(index);

        this.cloneItems.splice(index);

        this.queryResultsProducts = new Array();

      }
    },

    clickSearchSuggestion(customer_id, customer_name) {
      Vue.set(this.info, "customer_id", customer_id);
      Vue.set(this.info, "customer_name", customer_name);
      this.queryResults = null;
    },
    fetchInvoice() {
      let currObj = this;

      axios
        .get("api/invoice/" + this.id)
        .then(function(response) {
          Vue.set(currObj.info, "invoice_no", data.invoice.id),
            Vue.set(currObj.info, "title", data.invoice.title),
            Vue.set(currObj.info, "customer_id", data.invoice.customer_id),
            Vue.set(currObj.info, "customer_name", data.invoice.customer_name),
            Vue.set(currObj.info, "invoice_date", data.invoice.invoice_date),
            Vue.set(currObj.info, "due_date", data.invoice.due_date),
            Vue.set(currObj.info, "discount", data.invoice.discount),
            Vue.set(currObj.info, "status", data.invoice.status),
            //veu.set will make data reactive and updated
            currObj.items = data.invoice.invoice_detail,
            currObj.cloneItems = currObj.items
        })
        .catch(function(error) {
          console.log(error);
        });
    }, //end of fetchInvoice

    fetchUnits() {
      let vm = this; // current pointer instance while going inside the another functional instance
      axios
        .get("/api/units")
        .then(function(response) {
          vm.units = response.data.data;
        })
        .catch(function(error) {
          console.log();
        });
    }
  }, // end of methods

  computed: {
    subTotal: function() {
      //reduce function is used to sum the array elements
      this.info.subTotal = this.items.reduce(function(carry, item) {
        return carry + parseFloat(item.quantity) * parseFloat(item.price);
      }, 0);
      return this.info.subTotal;
    },
    setInvoiceVars: function() {},

    taxAmount: function() {
      return this.subTotal * 0.13;
    },

    grandTotal: function() {
      if (this.info.discount != null) {
        return this.subTotal - parseFloat(this.info.discount) + this.taxAmount;
      } else {
        return this.subTotal + this.taxAmount;
      }
    }
  }, //end of computed

  watch: {

    "info.note": function(val, oldVal) {
      // console.log(this.errors['info.note'][0]);
      if (this.errors != "") {
        this.errors["info.note"] = false;
        // this.errors["info.note"][0] = null;
      }
    },

    "info.due_date": function(val, oldVal) {
      // console.log('due_date changes');
      if (this.errors != "") {
        this.errors["info.due_date"] = false;
        // this.errors["info.due_date"][0] = null;
      }
    },

    "info.invoice_date": function(val, oldVal) {
      // console.log('estimate_date changes');
      if (this.errors != "") {
        this.errors["info.invoice_date"] = false;
        // this.errors["info.invoice_date"][0] = null;
      }
    },

    "info.customer_name": function(val, oldVal) {
      // console.log('customer_name changes');
      if (this.errors != "") {
        this.errors["info.customer_name"] = false;
        // this.errors["info.customer_name"][0] = null;
      }
    },

    items: {
      handler: function(val, oldVal) {
        var vm = this;
        val.filter(function(p, idx) {
          return Object.keys(p).some(function(prop) {
            var diff = p[prop] !== vm.cloneItems[idx][prop];
            if (diff) {
              p.changed = true;
              vm.cloneItems[idx][prop] = p[prop];

              // console.log(idx);
              // console.log(vm.items[idx])
              // console.log(vm.cloneItems[idx])
              //set error to null since user is typing new data, so remove error css to input field of items bar
              vm.errors['items.' + idx + '.product_name'] = false;

            }
          })
        });
      },
      deep: true
    },




  } //end of watch
}; //end of export default
</script>

<style>
.invoice {
  margin-top: 5em;
}

.invoice-body {
  margin-top: 2em;
  padding: 8px;
}

.invoice-head {
  padding: 1em;
  /*border-bottom: 1px solid #eee;*/
  background: coral;
  color: white;
  box-shadow: 1px 7px 17px -12px;
}

.invoice-foot {
  margin-top: 1em;
  padding: 2em;
  border-top: 1px solid #eee;
}

.datetime-picker {}

.datetime-picker input {
  display: block;
  width: 100%;
  height: calc(1.5em + 0.75rem + 2px);
  padding: 0.375rem 0.75rem;
  font-size: 1rem;
  font-weight: 400;
  line-height: 1.5;
  color: #495057;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.customer-search-suggestion {
  background: #fff;
  position: absolute;
  overflow-y: scroll;
  height: auto;
  max-height: 9em;
  color: #000;
  border: 1px solid #e2dfdf;
  border-top: 0px;
  width: 100%;
  box-shadow: 1px 7px 17px -12px;
  border-radius: 4px;
}

.customer-search-suggestion-inner {
  padding: 1px;
  border-top: 1px solid #d6d6d6;
}

.customer-search-suggestion-inner ul {
  list-style: none;
  margin: 0;
  padding: 0;
  display: block;
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
  background-color: #f5f5f5;
}

.customer-search-suggestion::-webkit-scrollbar {
  width: 6px;
  background-color: #f5f5f5;
}

.customer-search-suggestion::-webkit-scrollbar-thumb {
  background-color: #000000;
}

.auto-complete-product-container {
  position: relative;
}

.product-search-suggestion-invoice {
  position: absolute;
  /* background: #f4f3ef; */
  width: 100%;
  color: #212120;
  /* padding-right: 12px; */
  overflow-y: scroll;
  max-height: 9em;
  z-index: 1;
  box-shadow: 1px 7px 17px -12px;
  border-radius: 4px;
}

.product-search-suggestion-invoice ul {
  list-style: none;
  margin: 0px;
  padding: 0px;
}

.product-search-suggestion-invoice ul li {
  padding: 0.7em;
  cursor: pointer;
  background: #f4f3ef;
}

.product-search-suggestion-invoice ul li:hover {
  background: #51cbce;
  color: white;
}

.product-search-suggestion-invoice::-webkit-scrollbar-track {
  -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
  background-color: #f5f5f5;
}

.product-search-suggestion-invoice::-webkit-scrollbar {
  width: 6px;
  background-color: #f5f5f5;
}

.product-search-suggestion-invoice::-webkit-scrollbar-thumb {
  background-color: #000000;
}
</style>
