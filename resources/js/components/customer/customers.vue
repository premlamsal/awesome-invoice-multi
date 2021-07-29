<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Customer</h1>
    <p class="mb-4" >
      <div id="btn addcustomer" v-if="hasPermission('add_customer')">
        <b-button id="show-btn" @click="showAddModal" class="btn btn-success" style="margin-top: 8px;">
          <span class="fa fa-plus-circle"></span>Add Customer</b-button>
      </div>
    </p>
    <!-- add unit model start -->
    <b-modal id="bv-modal-add-customer" hide-footer>
      <template v-slot:modal-title>
        <span class="text-primary">{{modalForName}}</span>
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
         <div class="form-group">
          <label for="Opening_balance">Opening Balance:</label>
          <input type="text" v-model="customer.opening_balance" :class="['form-control']">
          <span v-if="errors.opening_balance" :class="['errorText']">{{ errors.opening_balance[0] }}</span>
        </div>
        <div class="form-group">
          <label for="Phone">Details:</label>
          <textarea v-model="customer.details" :class="['form-control']"></textarea>
          <span v-if="errors.details" :class="['errorText']">{{ errors.details[0] }}</span>
        </div>
      </div>
      <b-button class="btn-primary mt-3" block @click="callFunc">{{modalForName}}</b-button>
    </b-modal>
    <!-- add unit modal end-->
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary" style="display: inline-block;">Customers</h6>
               
        <div class="text-center" v-if="isLoading=='Loading all Data'">
          <b-spinner variant="success" label="Spinning"></b-spinner>
        </div>
       
        <div class="export-block">
            <template>
              <vue-blob-json-csv
              @success="handleSuccessExportCSV"
              @error="handleErrorExportCSV"
              file-type="csv"
              file-name="sample"
              :fields="customers_export_fileds"
              :data="customers">
              
              <!-- <button class="btn btn-warning-success"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button> -->
                <img src="img/icon-red-csv.png" class="icon-red-csv-export" alt="Export data to CSV">
            </vue-blob-json-csv>
          </template>
        </div>

    
        <!-- <span>{{isLoading}}</span> -->
        <div class="searchTable">
          <!-- Topbar Search -->
          <!-- <div class="input-group"> -->
          <div class="input-group no-border">
            <input type="text" value="" class="form-control" placeholder="Search..." v-model="searchTableKey" @keyup.enter="searchTableBtn">
            <div class="input-group-append">
              <div class="input-group-text">
                <i class="nc-icon nc-zoom-split" @click="searchTableBtn"></i>
              </div>
            </div>
          </div>
          <!-- </div> -->
        </div>
      </div>
      <div class="card-body" v-if="customers.length > 0 ">
         
        <div class="table">
          <table class="table table-striped table-bordered" width="100%" cellspacing="0">
            <thead>
              <tr>
                <!-- <th>ID</th> -->
                <th>Name</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Details</th>
                <th>Updated at</th>
                <th>Modify</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="customer in customers" v-bind:key="customer.id">
                <!-- <td>{{customer.id}}</td> -->
                <td @click="customerProfile(customer.id)" class="cursor">{{customer.name}}</td>
                <td>{{customer.address}}</td>
                <td>{{customer.phone}}</td>
                <td>{{customer.details}}</td>
                <td>{{customer.updated_at | moment("from", "now")}}</td>
                <td>
                  <button class="btn btn-outline-success custom_btn_table" @click=editCustomer(customer.id) v-if="hasPermission('edit_customer')"><span class="fa fa-edit custom_icon_table"></span></button>
                  <button class="btn btn-outline-danger custom_btn_table" @click=deleteCustomer(customer.id) v-if="hasPermission('delete_customer')"><span class="fa fa-trash custom_icon_table"></span></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-md-8">
            <ul class="pagination">
              <li class="page-item" v-bind:class="{disabled:!pagination.first_link}">
                <button @click="fetchCustomers(pagination.first_link)" class="page-link">First</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.prev_link}">
                <button @click="fetchCustomers(pagination.prev_link)" class="page-link">Previous</button>
              </li>
              <li v-for="n in pagination.last_page" v-bind:key="n" class="page-item" v-bind:class="{active:pagination.current_page == n}">
                <button @click="fetchCustomers(pagination.path_page + n)" class="page-link">{{n}}</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.next_link}">
                <button @click="fetchCustomers(pagination.next_link)" class="page-link">Next</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.last_link}">
                <button @click="fetchCustomers(pagination.last_link)" class="page-link">Last</button>
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

      customers: [], //contains all the retrived units from the database

      customer: {}, //for form single unit data

      modalForName: "",
      modalForCode: 0,

      searchTableKey: '',
      errors: [],
      pagination: {},

      isLoading: '',

      permissions: [],

      customers_export_fileds:["name","address","phone","details"],
    }
  },

  async created() {

    this.fetchCustomers();

  },

  // 
  //  
  methods: {
    handleSuccessExportCSV(){
      console.log("success Export");
    },
    handleErrorExportCSV(){
      console.log("errorExport");
    },
    customerProfile(id){
      this.$router.push({ path: `${id}/customer-profile/` });
    },

    getPermissions: async function() {

      // this.permissions=['view_customer','add_customer','edit_customer','delete_customer'];
      let condition = false;
      let perm;
      try {
        perm = await axios.get('api/permissions/check')

        console.log('from inside');
        this.permissions = perm.data.permissions
      } catch (err) {
        console.log(err)
      }

      console.log('from outsside')
    },



    isExistPermission(action) {

      for (var i = 0; i < this.permissions.length; i++) {

        if (this.permissions[i] == action) {

          return true
        }
      }
      return false
    }, //is exit


    //methods codes here
    fetchCustomers(page_url) {
      this.$Progress.start();
      this.isLoading = "Loading all Data";
     
      let vm = this; // current pointer instance while going inside the another functional instance
      page_url = page_url || 'api/customers'
      axios.get(page_url)
        .then(function(response) {
          vm.customers = response.data.data;
          if ((vm.customers.length) != null) {
            vm.makePagination(response.data.meta, response.data.links);
             vm.isLoading ='';
            vm.$Progress.finish();
          }

        })
        .catch(function(error) {
          vm.$Progress.fail();
          if (error.response.status == 422) {
            vm.validationErrors = error.response.data.errors;
            vm.errors = vm.validationErrors;
            // console.log(currObj.errors);
          } else if (error.response.status == 403) {

            vm.$toast.error({ title: 'Warning', message: 'Access denied.' });
            vm.$router.push({ name: '404' });

          } else {

            vm.$toast.error({ title: 'Opps', message: error.response.data.errors });
          }
        });
    },
    makePagination(meta, links) {
      let pagination = {
        current_page: meta.current_page,
        last_page: meta.last_page,
        from_page: meta.from,
        to_page: meta.to,
        total_pages: meta.total,
        path_page: meta.path + "?page=",
        first_link: links.first,
        last_link: links.last,
        prev_link: links.prev,
        next_link: links.next

      }
      this.pagination = pagination;
    },
    showAddModal() {
      this.modalForName = "Add Customer";
      // Vue.set(this.modalForName,"Add Unit");
      this.modalForCode = 0; //0 for add 

      this.customer.name = '';
      this.customer.address = '';
      this.customer.opening_balance = '';
      this.customer.phone = '';
      this.customer.details = '';

      this.errors = ''; //clearing errors

      // Vue.set(this.modalForCode,0);
      this.$bvModal.show('bv-modal-add-customer');

    },
    callFunc() {

      if (this.modalForCode == 0) {
        this.addCustomer();
        // console.log("Add Unit");
      } else if (this.modalForCode == 1) {
        this.updateCustomer();
        // console.log("Edit Unit");
      }

    },
    addCustomer() {

      this.$Progress.start();


      // if(this.isExistPermission('add_customer')){
      //     console.log('can add customer')
      //     }
      //     else{
      //       console.log('access denied')
      //     }       
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
          currObj.customer.opening_balance = '';

          currObj.customer.details = '';

          currObj.errors = ''; //clearing errors

          currObj.fetchCustomers();
          currObj.$Progress.finish();


        })
        .catch(function(error) {
          currObj.$Progress.fail();
          if (error.response.status == 422) {
            currObj.validationErrors = error.response.data.errors;
            currObj.errors = currObj.validationErrors;
            // console.log(currObj.errors);
          }
        });



    },
    editCustomer(id) {
      this.$Progress.start();
      let currObj = this;
      this.modalForName = "Edit Customer";
      this.modalForCode = 1; // 1 for Edit
      this.$bvModal.show('bv-modal-add-customer');
      currObj.errors = ''; //clearing errors
      axios.get('/api/customer/' + id)
        .then(response => {
          // console.log(response.data.unit)
          Vue.set(this.customer, 'name', response.data.customer.name);
          Vue.set(this.customer, 'address', response.data.customer.address);
          Vue.set(this.customer, 'details', response.data.customer.details);
          Vue.set(this.customer, 'opening_balance', response.data.customer.opening_balance);
          Vue.set(this.customer, 'phone', response.data.customer.phone);
          Vue.set(this.customer, 'id', id); //to send id to the update controller 
          this.$Progress.finish();
        })
        .catch(error => {
          // console.log(error)
          this.$Progress.fail();
        })

    },
    updateCustomer() {

      this.$Progress.start();
      let currObj = this;
      let formData = new FormData();
      formData.append('_method', 'PUT'); //add this otherwise data won't pass to backend
      formData.append('name', this.customer.name);
      formData.append('address', this.customer.address);
      formData.append('phone', this.customer.phone);
      formData.append('opening_balance', this.customer.opening_balance);
      formData.append('id', this.customer.id);
      formData.append('details', this.customer.details);

      axios.post('/api/customer', formData)
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;

          currObj.$swal('Info', currObj.output, currObj.status);
          currObj.$bvModal.hide('bv-modal-add-customer');

          currObj.customer.name = '';
          currObj.customer.address = '';
          currObj.customer.phone = '';
          currObj.customer.opening_balance = '';
          currObj.customer.details = '';
          currObj.errors = ''; //clearing errors
          currObj.$Progress.finish();
          currObj.fetchCustomers();

        }).catch(function(error) {
          currObj.$Progress.fail();
          if (error.response.status == 422) {
            currObj.validationErrors = error.response.data.errors;
            currObj.errors = currObj.validationErrors;
            // console.log(currObj.errors);
          }
        })



    },
    deleteCustomer(id) {
      this.$Progress.start();
      let currObj = this;
      this.$swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {

        if (result.value) {
          axios.delete('/api/customer/' + id)
            .then(function(response) {
              currObj.output = response.data.msg;
              currObj.status = response.data.status;
              // alert(currObj.status);
              currObj.$swal('Info', currObj.output, currObj.status);
              currObj.$Progress.finish();
              currObj.fetchCustomers();

            }).catch(function(error) {
              currObj.$Progress.fail();

              // currObj.output=error;

              // console.log(currObj.output);
            })

        }


      });


    }, //end of deleteUnit()
    searchTableBtn() {
      this.autoCompleteTable();
    },
    autoCompleteTable() {

      this.searchTableKey = this.searchTableKey.toLowerCase();
      if (this.searchTableKey != '') {
        this.isLoading = 'Loading Data...';
        let currObj = this;
        axios.post('/api/customers/search', { searchQuery: this.searchTableKey })
          .then(function(response) {

            currObj.isLoading = '';

            currObj.customers = response.data.data;
            if (response.data.data == "") {

              currObj.isLoading = "No Data Found";

            }
            // if((this.estimates.length)!=null){
            // // currObj.makePagination(res.meta,res.links);
            // }
            // currObj.status=response.data.status;
            currObj.errors = ''; //clearing errors

          })
          .catch(function(error) {
            if (error.response.status == '422') {
              currObj.validationErrors = error.response.data.errors;
              currObj.errors = currObj.validationErrors;
              currObj.isLoading = 'Load Failed...';
              // console.log(currObj.errors);

            }
          });
      } else {
        this.isLoading = "Loading all Data";
        this.fetchCustomers();
      }

    }, //end of autoCOmpleteTable
    hasPermission(action) {
      let permissions_from_store = this.$store.getters.permissions

      if (permissions_from_store.includes(action) || permissions_from_store.includes('all')) {
        return true
      } else {
        return false
      }

    } //has permision


    //end of methods block
  },



} //end of export defualt
</script>
