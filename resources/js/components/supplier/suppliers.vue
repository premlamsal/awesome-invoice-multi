<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Supplier</h1>
    <p class="mb-4" v-if="hasPermission('add_supplier')">
      <b-button id="show-btn" @click="showAddModal" class="btn btn-success" style="margin-top: 8px;">
        <span class="fa fa-plus-circle"></span> Add Supplier</b-button>
    </p>
    <!-- add unit model start -->
    <b-modal id="bv-modal-add-supplier" hide-footer>
      <template v-slot:modal-title>
        <span class="text-primary">{{modalForName}}</span>
      </template>
      <div class="d-block">
        <div class="form-group">
          <input type="hidden" v-model="supplier.id">
          <label for="Name">Name:</label>
          <!--  <input type="text"  v-model="supplier.name" :class="['form-control', errors.name ? 'is-invalid' : '']"> -->
          <input type="text" v-model="supplier.name" :class="['form-control']">
          <span v-if="errors.name" :class="['errorText']">{{ errors.name[0] }}</span>
        </div>
        <div class="form-group">
          <label for="Address">Address:</label>
          <input type="text" v-model="supplier.address" :class="['form-control']">
          <span v-if="errors.address" :class="['errorText']">{{ errors.address[0] }}</span>
        </div>
        <div class="form-group">
          <label for="Phone">Phone:</label>
          <input type="phone" v-model="supplier.phone" :class="['form-control']">
          <span v-if="errors.phone" :class="['errorText']">{{ errors.phone[0] }}</span>
        </div>
         <div class="form-group">
          <label for="Opening_balance">Opening Balance:</label>
          <input type="text" v-model="supplier.opening_balance" :class="['form-control']">
          <span v-if="errors.opening_balance" :class="['errorText']">{{ errors.opening_balance[0] }}</span>
        </div>
        <div class="form-group">
          <label for="Phone">Details:</label>
          <textarea v-model="supplier.details" :class="['form-control']"></textarea>
          <span v-if="errors.details" :class="['errorText']">{{ errors.details[0] }}</span>
        </div>
      </div>
      <b-button class="btn-primary mt-3" block @click="callFunc">{{modalForName}}</b-button>
    </b-modal>
    <!-- add unit modal end-->
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary" style="display: inline-block;">Suppliers</h6>
         <div class="text-center" v-if="isLoading=='Loading all Data'">
          <b-spinner variant="success" label="Spinning"></b-spinner>
        </div>
          <div class="export-block">
            <template>
              <vue-blob-json-csv
              @success="handleSuccessExportCSV"
              @error="handleErrorExportCSV"
              file-type="csv"
              file-name="suppliers"
              :fields="suppliers_export_fileds"
              :data="suppliers">
              
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
      <div class="card-body" v-if="suppliers.length > 0">
        <div class="table">
          <table class="table table-striped table-bordered" width="100%" cellspacing="0">
            <thead>
              <tr>
                <!-- <th>ID</th> -->
                <th>Name</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Open.Bal</th>
                <th>Details</th>
                <th>Updated at</th>
                <th>Modify</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="supplier in suppliers" v-bind:key="supplier.id">
                <!-- <td>{{supplier.id}}</td> -->
                <td @click="supplierProfile(supplier.id)" class="cursor">{{supplier.name}}</td>
                <td>{{supplier.address}}</td>
                <td>{{supplier.phone}}</td>
                <td>{{supplier.opening_balance}}</td>
                <td>{{supplier.details}}</td>
                <td>{{supplier.updated_at | moment("from", "now")}}</td>
                <td>
                  <button class="btn btn-success custom_btn_table" @click=editSupplier(supplier.id) v-if="hasPermission('edit_supplier')"><span class="fa fa-edit custom_icon_table"></span></button>
                  <button class="btn btn-danger custom_btn_table" @click=deleteSupplier(supplier.id) v-if="hasPermission('delete_supplier')"><span class="fa fa-trash custom_icon_table"></span></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-md-8">
            <ul class="pagination">
              <li class="page-item" v-bind:class="{disabled:!pagination.first_link}">
                <button @click="fetchSuppliers(pagination.first_link)" class="page-link">First</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.prev_link}">
                <button @click="fetchSuppliers(pagination.prev_link)" class="page-link">Previous</button>
              </li>
              <li v-for="n in pagination.last_page" v-bind:key="n" class="page-item" v-bind:class="{active:pagination.current_page == n}">
                <button @click="fetchSuppliers(pagination.path_page + n)" class="page-link">{{n}}</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.next_link}">
                <button @click="fetchSuppliers(pagination.next_link)" class="page-link">Next</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.last_link}">
                <button @click="fetchSuppliers(pagination.last_link)" class="page-link">Last</button>
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

      suppliers: [], //contains all the retrived units from the database

      supplier: {}, //for form single unit data

      modalForName: "",
      modalForCode: 0,

      searchTableKey: '',
      errors: [],
      pagination: {},

      isLoading: '',

      // store_id: 3 ,

      suppliers_export_fileds:["name","address","phone","details"],

    }
  },
  created() {


    this.supplier.store_id = 3;

    //this block will execute when component created
    this.fetchSuppliers();


  },

  methods: {
      supplierProfile(id){
      this.$router.push({ path: `${id}/supplier-profile/` });
    },
    //methods codes here
    fetchSuppliers(page_url) {
      this.$Progress.start();
       this.isLoading = "Loading all Data";
      let vm = this; // current pointer instance while going inside the another functional instance
      page_url = page_url || 'api/suppliers'
      axios.get(page_url)
        .then(function(response) {
          vm.suppliers = response.data.data;
          if ((vm.suppliers.length) != null) {
            vm.makePagination(response.data.meta, response.data.links);
            vm.$Progress.finish();
          }
          vm.isLoading = '';

        })
        .catch(function(error) {
          // console.log();
          vm.$Progress.fail();
        });

      //above and below code provide same result but above code need current instance pointer for value assignmnent 

      //below code donot need current pointer to be save becasue it execute in current block rather then another block that need previous pointer.


      // axios.get('/api/suppliers')
      // .then(response=>{
      //   // console.log(response.data.data)
      //   this.suppliers=response.data.data
      // })
      // .catch(error=>{
      //   console.log(error)
      // })


    },


     handleSuccessExportCSV(){
      console.log("success Export");
    },
    handleErrorExportCSV(){
      console.log("errorExport");
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
      this.modalForName = "Add Supplier";
      // Vue.set(this.modalForName,"Add Unit");
      this.modalForCode = 0; //0 for add 

      this.supplier.name = '';
      this.supplier.address = '';
      this.supplier.phone = '';
      this.supplier.details = '';
      this.supplier.opening_balance = '';


      this.errors = ''; //clearing errors

      // Vue.set(this.modalForCode,0);
      this.$bvModal.show('bv-modal-add-supplier');

    },
    callFunc() {

      if (this.modalForCode == 0) {
        this.addSupplier();
        // console.log("Add Unit");
      } else if (this.modalForCode == 1) {
        this.updateSupplier();
        // console.log("Edit Unit");
      }

    },
    addSupplier() {
      this.$Progress.start();
      let currObj = this;
      axios.post('/api/supplier', this.supplier)
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          currObj.$swal('Info', currObj.output, currObj.status);

          currObj.$bvModal.hide('bv-modal-add-supplier');


          currObj.supplier.name = '';
          currObj.supplier.address = '';
          currObj.supplier.phone = '';
          currObj.supplier.opening_balance = '';
          currObj.supplier.details = '';

          currObj.errors = ''; //clearing errors
          currObj.$Progress.finish();

          currObj.fetchSuppliers();

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
    editSupplier(id) {
      this.$Progress.start();
      let currObj = this;
      this.modalForName = "Edit Supplier";
      this.modalForCode = 1; // 1 for Edit
      this.$bvModal.show('bv-modal-add-supplier');
      currObj.errors = ''; //clearing errors
      axios.get('/api/supplier/' + id)
        .then(response => {
          // console.log(response.data.unit)
          Vue.set(this.supplier, 'name', response.data.supplier.name);
          Vue.set(this.supplier, 'address', response.data.supplier.address);
          Vue.set(this.supplier, 'details', response.data.supplier.details);
          Vue.set(this.supplier, 'opening_balance', response.data.supplier.opening_balance);
          Vue.set(this.supplier, 'phone', response.data.supplier.phone);
          Vue.set(this.supplier, 'id', id); //to send id to the update controller 
          this.$Progress.finish();
        })
        .catch(error => {
          // console.log(error)
          this.$Progress.fail();
        })

    },
    updateSupplier() {
      this.$Progress.start();
      let currObj = this;
      let formData = new FormData();
      formData.append('_method', 'PUT'); //add this otherwise data won't pass to backend
      formData.append('name', this.supplier.name);
      formData.append('address', this.supplier.address);
      formData.append('phone', this.supplier.phone);
      formData.append('opening_balance', this.supplier.opening_balance);
      formData.append('id', this.supplier.id);
      formData.append('details',this.supplier.details);

      axios.post('/api/supplier', formData)
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          // alert(currObj.status);

          currObj.$swal('Info', currObj.output, currObj.status);
          currObj.$bvModal.hide('bv-modal-add-supplier');

          currObj.supplier.name = '';
          currObj.supplier.address = '';
          currObj.supplier.phone = '';
          currObj.supplier.details = '';
          currObj.supplier.opening_balance = '';
          currObj.errors = ''; //clearing errors
          currObj.$Progress.finish();
          currObj.fetchSuppliers();

        }).catch(function(error) {
          currObj.$Progress.fail();
          if (error.response.status == 422) {
            currObj.validationErrors = error.response.data.errors;
            currObj.errors = currObj.validationErrors;
            // console.log(currObj.errors);
          }
        })



    },
    deleteSupplier(id) {
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
          axios.delete('/api/supplier/' + id)
            .then(function(response) {
              currObj.output = response.data.msg;
              currObj.status = response.data.status;
              // alert(currObj.status);
             
              let index_to_delete = currObj.suppliers.findIndex(supplier => supplier.id === id)
              currObj.suppliers.splice(index_to_delete,1);
              currObj.$Progress.finish();
              // alert(currObj.status);
              currObj.$swal("Info", currObj.output, currObj.status);

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
        axios.post('/api/suppliers/search', { searchQuery: this.searchTableKey })
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
        this.fetchSuppliers();
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
