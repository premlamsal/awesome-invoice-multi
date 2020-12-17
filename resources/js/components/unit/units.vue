<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Units</h1>
    <p class="mb-4" v-if="hasPermission('add_unit')">
      <b-button id="show-btn" @click="showAddModal" class="btn btn-success" style="margin-top: 8px;">
        <span class="fa fa-plus-circle"></span> Add Unit</b-button>
    </p>
    <!-- add unit model start -->
    <b-modal id="bv-modal-add-unit" hide-footer>
      <template v-slot:modal-title>
        {{modalForName}}
      </template>
      <div class="d-block">
        <div class="form-group">
          <label for="Short Name">Short Name</label>
          <input type="hidden" v-model="unit.id">
          <input type="text" :class="['form-control']" placeholder="ex. kg or sq.ft or ltr" v-model="unit.short_name">
          <span v-if="errors.short_name" :class="['errorText']">{{ errors.short_name[0] }}</span>
        </div>
        <div class="form-group">
          <label for="Long Name">Long Name</label>
          <input type="text" :class="['form-control']" placeholder="ex. kilogram or square foot or litre" v-model="unit.long_name">
          <span v-if="errors.long_name" :class="['errorText']">{{ errors.long_name[0] }}</span>
        </div>
      </div>
      <b-button class="btn-primary mt-3" block @click="callFunc">{{modalForName}}</b-button>
    </b-modal>
    <!-- add unit modal end-->
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary" style="display: inline;">Units</h6>
         <div class="text-center" v-if="isLoading=='Loading all Data'">
          <b-spinner variant="success" label="Spinning"></b-spinner>
        </div>
          <div class="export-block">
            <template>
              <vue-blob-json-csv
              @success="handleSuccessExportCSV"
              @error="handleErrorExportCSV"
              file-type="csv"
              file-name="units"
             :fields="units_export_fileds"
              :data="units">
              
              <!-- <button class="btn btn-warning-success"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button> -->
                <img src="img/icon-red-csv.png" class="icon-red-csv-export" alt="Export data to CSV">
            </vue-blob-json-csv>
          </template>
        </div>

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
      <div class="card-body" v-if="units.length > 0">
        <div class="table">
          <table class="table table-striped table-bordered" width="100%" cellspacing="0">
            <thead>
              <tr>
                <!-- <th>ID</th> -->
                <th>Short Name</th>
                <th>Long Name</th>
                <th>Updated at</th>
                <th>Modify</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="unit in units" v-bind:key="unit.id">
                <!-- <td>{{unit.id}}</td> -->
                <td>{{unit.short_name}}</td>
                <td>{{unit.long_name}}</td>
                <td>{{unit.updated_at | moment("from", "now")}}</td>
                <td>
                  <button class="btn btn-outline-success custom_btn_table" @click=editUnit(unit.id) v-if="hasPermission('edit_unit')"><span class="fa fa-edit custom_icon_table"></span></button>
                  <button class="btn btn-outline-danger custom_btn_table" @click=deleteUnit(unit.id) v-if="hasPermission('delete_unit')"><span class="fa fa-trash custom_icon_table"></span></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-md-8">
            <ul class="pagination">
              <li class="page-item" v-bind:class="{disabled:!pagination.first_link}">
                <button @click="fetchUnits(pagination.first_link)" class="page-link">First</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.prev_link}">
                <button @click="fetchUnits(pagination.prev_link)" class="page-link">Previous</button>
              </li>
              <li v-for="n in pagination.last_page" v-bind:key="n" class="page-item" v-bind:class="{active:pagination.current_page == n}">
                <button @click="fetchUnits(pagination.path_page + n)" class="page-link">{{n}}</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.next_link}">
                <button @click="fetchUnits(pagination.next_link)" class="page-link">Next</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.last_link}">
                <button @click="fetchUnits(pagination.last_link)" class="page-link">Last</button>
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

      units: [], //contains all the retrived units from the database

      unit: {}, //for form single unit data

      modalForName: "",
      modalForCode: 0,

      searchTableKey: '',
      errors: [],
      pagination: {},
      isLoading: '',
      units_export_fileds:["short_name","long_name"],

    }
  },
  created() {
    //this block will execute when component created
    this.fetchUnits();

  },

  methods: {
    //methods codes here
    fetchUnits(page_url) {
      this.$Progress.start();
      this.isLoading = "Loading all Data";
      page_url = page_url || 'api/units'

      let vm = this; // current pointer instance while going inside the another functional instance
      axios.get(page_url)
        .then(function(response) {
          vm.units = response.data.data;
          // console.log(response.data);
          if ((vm.units.length) != null) {
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


      // axios.get('/api/units')
      // .then(response=>{
      //   // console.log(response.data.data)
      //   this.units=response.data.data
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
      this.modalForName = "Add Unit";
      // Vue.set(this.modalForName,"Add Unit");
      this.modalForCode = 0; //0 for add 
      this.unit.short_name = '';
      this.unit.long_name = '';
      this.errors = ''; //clearing errors       
      // Vue.set(this.modalForCode,0);
      this.$bvModal.show('bv-modal-add-unit')
    },
    callFunc() {

      if (this.modalForCode == 0) {
        this.addUnit();
        // console.log("Add Unit");
      } else if (this.modalForCode == 1) {
        this.updateUnit();
        // console.log("Edit Unit");
      }

    },
    addUnit() {
      this.$Progress.start();
      let currObj = this;
      axios.post('/api/unit', this.unit)
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          currObj.$swal('Info', currObj.output, currObj.status);

          currObj.$bvModal.hide('bv-modal-add-unit');

          currObj.unit.short_name = '';

          currObj.unit.long_name = '';

          currObj.errors = ''; //clearing errors
          currObj.$Progress.finish();

          currObj.fetchUnits();

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
    editUnit(id) {
      this.$Progress.start();
      this.modalForName = "Edit Unit";
      this.modalForCode = 1; // 1 for Edit
      this.$bvModal.show('bv-modal-add-unit');
      this.errors = ''; //clearing errors
      axios.get('/api/units/' + id)
        .then(response => {
          // console.log(response.data.unit)
          Vue.set(this.unit, 'short_name', response.data.unit.short_name);
          Vue.set(this.unit, 'long_name', response.data.unit.long_name);
          Vue.set(this.unit, 'id', id); //to send id to the update controller 
          this.$Progress.finish();
        })
        .catch(error => {
          // console.log(error)
          this.$Progress.finish();
        })

    },
    updateUnit() {
      this.$Progress.start();
      let currObj = this;
      let formData = new FormData();
      formData.append('_method', 'PUT'); //add this otherwise data won't pass to backend
      formData.append('short_name', this.unit.short_name);
      formData.append('long_name', this.unit.long_name);
      formData.append('id', this.unit.id);

      axios.post('/api/unit', formData)
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          // alert(currObj.status);

          currObj.$swal('Info', currObj.output, currObj.status);
          currObj.$bvModal.hide('bv-modal-add-unit');

          currObj.unit.short_name = '';
          currObj.unit.long_name = '';
          currObj.errors = ''; //clearing errors
          currObj.$Progress.finish();
          currObj.fetchUnits();

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
    deleteUnit(id) {
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
          axios.delete('/api/unit/' + id)
            .then(function(response) {
              currObj.output = response.data.msg;
              currObj.status = response.data.status;
              // alert(currObj.status);
             
              let index_to_delete = currObj.units.findIndex(unit => unit.id === id)
              currObj.units.splice(index_to_delete,1);
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
        axios.post('/api/units/search', { searchQuery: this.searchTableKey })
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
        this.fetchUnits();
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
