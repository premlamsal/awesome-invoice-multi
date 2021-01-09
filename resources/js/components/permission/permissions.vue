<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Permissions</h1>
    <p class="mb-4" v-if="hasPermission('add_permissions')">
      <b-button id="show-btn" @click="showAddModal" class="btn btn-success" style="margin-top: 8px;">
        <span class="fa fa-plus-circle"></span> Add Permission</b-button>
    </p>
    <!-- add permission model start -->
    <b-modal id="bv-modal-add-permission" hide-footer>
      <template v-slot:modal-title>
        {{modalForName}}
      </template>
      <div class="row m-4">
        <div class="col-md-2">
          <span>View</span>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="view_customers" v-model="checkedPermissions">
            <label class="form-check-label" for="view_customers">
              Customer
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="view_suppliers" v-model="checkedPermissions">
            <label class="form-check-label" for="view_suppliers">
              Supplier
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="view_invoices" v-model="checkedPermissions">
            <label class="form-check-label" for="view_invoices">
              Invoice
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="view_purchases" v-model="checkedPermissions">
            <label class="form-check-label" for="view_purchases">
              Purchase
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="view_products" v-model="checkedPermissions">
            <label class="form-check-label" for="view_products">
              Product
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="view_categories" v-model="checkedPermissions">
            <label class="form-check-label" for="view_categories">
              Category
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="view_units" v-model="checkedPermissions">
            <label class="form-check-label" for="view_units">
              Unit
            </label>
          </div>
         
        </div>
        <div class="col-md-2">
          <span>Add</span>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="add_customer" v-model="checkedPermissions">
            <label class="form-check-label" for="add_customer">
              Customer
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="add_supplier" v-model="checkedPermissions">
            <label class="form-check-label" for="add_supplier">
              Supplier
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="add_invoice" v-model="checkedPermissions">
            <label class="form-check-label" for="add_invoice">
              Invoice
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="add_purchase" v-model="checkedPermissions">
            <label class="form-check-label" for="add_purchase">
              Purchase
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="add_product" v-model="checkedPermissions">
            <label class="form-check-label" for="add_product">
              Product
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="add_category" v-model="checkedPermissions">
            <label class="form-check-label" for="add_category">
              Category
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="add_unit" v-model="checkedPermissions">
            <label class="form-check-label" for="add_unit">
              Unit
            </label>
          </div>
         
        </div>
        <div class="col-md-2">
          <span>Edit</span>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="edit_customer" v-model="checkedPermissions">
            <label class="form-check-label" for="edit_customer">
              Customer
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="edit_supplier" v-model="checkedPermissions">
            <label class="form-check-label" for="edit_supplier">
              Supplier
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="edit_invoice" v-model="checkedPermissions">
            <label class="form-check-label" for="edit_invoice">
              Invoice
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="edit_purchase" v-model="checkedPermissions">
            <label class="form-check-label" for="edit_purchase">
              Purchase
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="edit_product" v-model="checkedPermissions">
            <label class="form-check-label" for="edit_product">
              Product
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="edit_category" v-model="checkedPermissions">
            <label class="form-check-label" for="edit_category">
              Category
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="edit_unit" v-model="checkedPermissions">
            <label class="form-check-label" for="edit_unit">
              Unit
            </label>
          </div>
          
        </div>
        <div class="col-md-2">
          <span>Delete</span>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="delete_customer" v-model="checkedPermissions">
            <label class="form-check-label" for="delete_customer">
              Customer
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="delete_supplier" v-model="checkedPermissions">
            <label class="form-check-label" for="delete_supplier">
              Supplier
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="delete_invoice" v-model="checkedPermissions">
            <label class="form-check-label" for="delete_invoice">
              Invoice
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="delete_purchase" v-model="checkedPermissions">
            <label class="form-check-label" for="delete_purchase">
              Purchase
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="delete_product" v-model="checkedPermissions">
            <label class="form-check-label" for="delete_product">
              Product
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="delete_category" v-model="checkedPermissions">
            <label class="form-check-label" for="delete_category">
              Category
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="delete_unit" v-model="checkedPermissions">
            <label class="form-check-label" for="delete_unit">
              Unit
            </label>
          </div>
        
        </div>
        <div class="col-md-2">
          <span>Show</span>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="show_customer" v-model="checkedPermissions">
            <label class="form-check-label" for="show_customer">
              Customer
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="show_supplier" v-model="checkedPermissions">
            <label class="form-check-label" for="show_supplier">
              Supplier
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="show_invoice" v-model="checkedPermissions">
            <label class="form-check-label" for="show_invoice">
              Invoice
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="show_purchase" v-model="checkedPermissions">
            <label class="form-check-label" for="show_purchase">
              Purchase
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="show_product" v-model="checkedPermissions">
            <label class="form-check-label" for="show_product">
              Product
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="show_category" v-model="checkedPermissions">
            <label class="form-check-label" for="show_category">
              Category
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="show_unit" v-model="checkedPermissions">
            <label class="form-check-label" for="show_unit">
              Unit
            </label>
          </div>
          
        </div>
        <div class="col-md-2">
          <span>Search</span>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="search_customer" v-model="checkedPermissions">
            <label class="form-check-label" for="search_customer">
              Customer
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="search_supplier" v-model="checkedPermissions">
            <label class="form-check-label" for="search_supplier">
              Supplier
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="search_invoice" v-model="checkedPermissions">
            <label class="form-check-label" for="search_invoice">
              Invoice
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="search_purchase" v-model="checkedPermissions">
            <label class="form-check-label" for="search_purchase">
              Purchase
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="search_product" v-model="checkedPermissions">
            <label class="form-check-label" for="search_product">
              Product
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="search_category" v-model="checkedPermissions">
            <label class="form-check-label" for="search_category">
              Category
            </label>
          </div>
          <div class="form-group">
            <input class="form-check-input" type="checkbox" value="search_unit" v-model="checkedPermissions">
            <label class="form-check-label" for="search_unit">
              Unit
            </label>
          </div>
         
        </div>
      </div>
      <!-- end of one row -->
      <div class="row">
        <div class="col-md-12">
          <div class="d-block">
            <div class="form-group">
              <label for="Name">Permission Names</label>
              <input type="hidden" class="form-control"> {{checkedPermissions}}
              <span v-if="errors.name" :class="['errorText']">{{ errors.name[0] }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="alert alert-success" role="alert">
            You can allow permissions like: Show, Search and View for the better experience. So, read full documentation before adding permission to user.
          </div>
        </div>
        <div class="col-md-4">
          <div class="alert alert-success" role="alert">
            Some "Add" and "Edit" permissions need "View" permision to be given.
          </div>
        </div>
      </div>
      <b-button class="btn-primary mt-3" block @click="callFunc">{{modalForName}}</b-button>
    </b-modal>
    <!-- add permission modal end-->
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary" style="display: inline;">Permissions</h6>
        <div v-if="isLoading">{{isLoading}}</div>
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
      <div class="card-body" v-if="permissions.length > 0">
        <div class="table">
          <table class="table table-striped table-bordered" width="100%" cellspacing="0">
            <thead>
              <tr>
                <!-- <th>ID</th> -->
                <th>Name</th>
                <!-- <th>Permissions</th> -->
                <th>Updated at</th>
                <th>Modify</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="permission in permissions" v-bind:key="permission.id">
                <!-- <td>{{permission.id}}</td> -->
                <td>{{permission.name}}</td>
                <!-- <td>{{permission.long_name}}</td> -->
                <td>{{permission.created_at | moment("from", "now")}}</td>
                <td>
                  <button class="btn btn-outline-success custom_btn_table" @click=editPermission(permission.id) v-if="hasPermission('edit_permission')"><span class="fa fa-edit custom_icon_table"></span></button>
                  <button class="btn btn-outline-danger custom_btn_table" @click=deletePermission(permission.id) v-if="hasPermission('delete_permission')"><span class="fa fa-trash custom_icon_table"></span></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-md-8">
            <ul class="pagination">
              <li class="page-item" v-bind:class="{disabled:!pagination.first_link}">
                <button @click="fetchPermissions(pagination.first_link)" class="page-link">First</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.prev_link}">
                <button @click="fetchPermissions(pagination.prev_link)" class="page-link">Previous</button>
              </li>
              <li v-for="n in pagination.last_page" v-bind:key="n" class="page-item" v-bind:class="{active:pagination.current_page == n}">
                <button @click="fetchPermissions(pagination.path_page + n)" class="page-link">{{n}}</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.next_link}">
                <button @click="fetchPermissions(pagination.next_link)" class="page-link">Next</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.last_link}">
                <button @click="fetchPermissions(pagination.last_link)" class="page-link">Last</button>
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

<style>
#bv-modal-add-permission .modal-dialog {
  max-width: 80% !important;
}
</style>

<script>
export default {

  data() {
    return {

      permissions: [], //contains all the retrived permissions from the database

      permission: {}, //for form single permission data

      modalForName: "",
      modalForCode: 0,

      searchTableKey: '',
      errors: [],
      pagination: {},
      isLoading: '',

      checkedPermissions: []

    }
  },
  created() {
    //this block will execute when component created
    this.fetchPermissions();

  },

  methods: {
    //methods codes here
    fetchPermissions(page_url) {
      this.$Progress.start();
      this.isLoading = "Loading all Data";
      page_url = page_url || '/api/permissions'

      let vm = this; // current pointer instance while going inside the another functional instance
      axios.get(page_url)
        .then(function(response) {
          vm.permissions = response.data.data;
          // console.log(response.data);
          if ((vm.permissions.length) != null) {
            vm.makePagination(response.data.meta, response.data.links);
          }
          vm.isLoading = '';
          vm.$Progress.finish();
        })
        .catch(function(error) {
          // console.log();
          vm.$Progress.fail();
        });

      //above and below code provide same result but above code need current instance pointer for value assignmnent 

      //below code donot need current pointer to be save becasue it execute in current block rather then another block that need previous pointer.


      // axios.get('/api/permissions')
      // .then(response=>{
      //   // console.log(response.data.data)
      //   this.permissions=response.data.data
      // })
      // .catch(error=>{
      //   console.log(error)
      // })


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
      this.modalForName = "Add Permission";
      // Vue.set(this.modalForName,"Add Permission");
      this.modalForCode = 0; //0 for add 
      this.permission.name = '';
      this.errors = ''; //clearing errors       
      // Vue.set(this.modalForCode,0);
      this.$bvModal.show('bv-modal-add-permission')
    },
    callFunc() {

      if (this.modalForCode == 0) {
        this.addPermission();
        // console.log("Add Permission");
      } else if (this.modalForCode == 1) {
        this.updatePermission();
        // console.log("Edit Permission");
      }

    },
    addPermission() {
      this.$Progress.start();
      let currObj = this;

      //set check permission to sending form input
      this.permission.name = this.checkedPermissions;

      axios.post('/api/permission', this.permission)
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          currObj.$swal('Info', currObj.output, currObj.status);

          currObj.$bvModal.hide('bv-modal-add-permission');

          currObj.permission.name = '';

          currObj.errors = ''; //clearing errors

          currObj.$Progress.finish();

          currObj.fetchPermissions();

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
    editPermission(id) {
      this.$Progress.start();
      this.modalForName = "Edit Permission";
      this.modalForCode = 1; // 1 for Edit
      this.$bvModal.show('bv-modal-add-permission');
      this.errors = ''; //clearing errors
      axios.get('/api/permission/' + id)
        .then(response => {
          // console.log(response.data.permission)
          this.checkedPermissions = response.data.permission
          Vue.set(this.permission, 'id', id); //to send id to the update controller 
          this.$Progress.finish();
        })
        .catch(error => {
          // console.log(error)
          this.$Progress.fail();
        })

    },
    updatePermission() {

      this.$Progress.start();
      let currObj = this;

      let formData = new FormData();
      formData.append('_method', 'PUT'); //add this otherwise data won't pass to backend

      formData.append('name', this.checkedPermissions);
      formData.append('id', this.permission.id);

      axios.post('/api/permission', formData)
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          // alert(currObj.status);

          currObj.$swal('Info', currObj.output, currObj.status);
          currObj.$bvModal.hide('bv-modal-add-permission');

          currObj.permission.name = '';
          currObj.errors = ''; //clearing errors
          currObj.$Progress.finish();

          currObj.fetchPermissions();

        })
        .catch(function(error) {
          currObj.$Progress.fail();
          if (error.response.status == 422) {
            currObj.validationErrors = error.response.data.errors;
            currObj.errors = currObj.validationErrors;
            // console.log(currObj.errors);
          }
        })



    },
    deletePermission(id) {
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
          axios.delete('/api/permission/' + id)
            .then(function(response) {
              currObj.output = response.data.msg;
              currObj.status = response.data.status;
              // alert(currObj.status);
              
              let index_to_delete = currObj.permissions.findIndex(permission => permission.id === id)
              currObj.permissions.splice(index_to_delete,1);
              currObj.$Progress.finish();
              // alert(currObj.status);
              currObj.$swal("Info", currObj.output, currObj.status);

            }).catch(function(error) {
              // currObj.output=error;
              // console.log(currObj.output);
              currObj.$Progress.fail();
            })

        }


      });


    }, //end of deletePermission()
    searchTableBtn() {
      this.autoCompleteTable();
    },
    autoCompleteTable() {

      this.searchTableKey = this.searchTableKey.toLowerCase();
      if (this.searchTableKey != '') {
        this.isLoading = 'Loading Data...';
        let currObj = this;
        axios.post('/api/permissions/search', { searchQuery: this.searchTableKey })
          .then(function(response) {

            currObj.isLoading = '';

            currObj.suppliers = response.data.data;
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
        this.fetchPermissions();
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
  }

}
</script>
