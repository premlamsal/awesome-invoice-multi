<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Roles</h1>
    <p class="mb-4" v-if="hasPermission('add_role')">
      <b-button id="show-btn" @click="showAddModal" class="btn btn-success" style="margin-top: 8px;">
        <span class="fa fa-plus-circle"></span> Add Role</b-button>
    </p>
    <!-- add role model start -->
    <b-modal id="bv-modal-add-role" hide-footer>
      <template v-slot:modal-title>
        {{modalForName}}
      </template>
      <div class="d-block">
        <div class="form-group">
          <label for=" Name">Name</label>
          <input type="hidden" v-model="role.id">
          <input type="hidden" v-model="role.permission_id_old">
          <input type="text" :class="['form-control']" placeholder="ex. admin or sales" v-model="role.name">
          <span v-if="errors.name" :class="['errorText']">{{ errors.name[0] }}</span>
        </div>
        <div class="form-group">
          <label for="User Type">User Permission</label>
          <select class="form-control" v-model="role.permission_id">
            <option selected="" v-for="permission in permissions" :value="permission.id">{{permission.name}}</option>
          </select>
          <span v-if="errors.permission_id" :class="['errorText']">{{ errors.type[0] }}</span>
        </div>
      </div>
      <b-button class="btn-primary mt-3" block @click="callFunc">{{modalForName}}</b-button>
    </b-modal>
    <!-- add role modal end-->
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary" style="display: inline;">Roles</h6>
         <div class="text-center" v-if="isLoading=='Loading all Data'">
          <b-spinner variant="success" label="Spinning"></b-spinner>
        </div>
          <div class="export-block">
            <template>
              <vue-blob-json-csv
              @success="handleSuccessExportCSV"
              @error="handleErrorExportCSV"
              file-type="csv"
              file-name="roles"
              :fields="roles_export_fileds"
              :data="roles">
              
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
      <div class="card-body" v-if="roles.length > 0">
        <div class="table">
          <table class="table table-striped table-bordered" width="100%" cellspacing="0">
            <thead>
              <tr>
                <!-- <th>ID</th> -->
                <th>Name</th>
                <th>Permissions</th>
                <th>Updated at</th>
                <th>Modify</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="role in roles" v-bind:key="role.id">
                <!-- <td>{{role.id}}</td> -->
                <td>{{role.name}}</td>
                <td>{{role.permissions[0].name}}</td>
                <td>{{role.updated_at | moment("from", "now")}}</td>
                <td>
                  <button class="btn btn-outline-success custom_btn_table" @click=editRole(role.id) v-if="hasPermission('edit_role')"><span class="fa fa-edit custom_icon_table"></span></button>
                  <button class="btn btn-outline-danger custom_btn_table" @click=deleteRole(role.id) v-if="hasPermission('delete_role')"><span class="fa fa-trash custom_icon_table"></span></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-md-8">
            <ul class="pagination">
              <li class="page-item" v-bind:class="{disabled:!pagination.first_link}">
                <button @click="fetchRoles(pagination.first_link)" class="page-link">First</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.prev_link}">
                <button @click="fetchRoles(pagination.prev_link)" class="page-link">Previous</button>
              </li>
              <li v-for="n in pagination.last_page" v-bind:key="n" class="page-item" v-bind:class="{active:pagination.current_page == n}">
                <button @click="fetchRoles(pagination.path_page + n)" class="page-link">{{n}}</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.next_link}">
                <button @click="fetchRoles(pagination.next_link)" class="page-link">Next</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.last_link}">
                <button @click="fetchRoles(pagination.last_link)" class="page-link">Last</button>
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

      roles: [], //contains all the retrived roles from the database

      role: {}, //for form single role data

      modalForName: "",
      modalForCode: 0,

      searchTableKey: '',
      errors: [],
      pagination: {},
      isLoading: '',
      permissions: [],
      roles_export_fileds:["name","permissions"],

    }
  },
  created() {
    //this block will execute when component created
    this.fetchRoles();


    this.fetchPermissions();

  },

  methods: {
    //methods codes here
    fetchRoles(page_url) {
      this.$Progress.start();
      this.isLoading = "Loading all Data";

      page_url = page_url || 'api/roles'

      let vm = this; // current pointer instance while going inside the another functional instance
      axios.get(page_url)
        .then(function(response) {
          vm.roles = response.data.data;
          // console.log(response.data);
          if ((vm.roles.length) != null) {
            vm.makePagination(response.data.meta, response.data.links);
          }
          vm.isLoading = "";
          vm.$Progress.finish();
        })
        .catch(function(error) {
          // console.log();
          vm.$Progress.finish();
        });

    },
     handleSuccessExportCSV(){
      console.log("success Export");
    },
    handleErrorExportCSV(){
      console.log("errorExport");
    },

    //methods codes here
    fetchPermissions(page_url) {
      this.$Progress.start();
      page_url = page_url || 'api/permissions'

      let vm = this; // current pointer instance while going inside the another functional instance
      axios.get(page_url)
        .then(function(response) {
          vm.permissions = response.data.data;
          // console.log(response.data);
          if ((vm.permissions.length) != null) {
            vm.makePagination(response.data.meta, response.data.links);
            vm.$Progress.finish();

          }

        })
        .catch(function(error) {
          // console.log();
          vm.$Progress.fail();
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
      this.modalForName = "Add Role";
      // Vue.set(this.modalForName,"Add Role");
      this.modalForCode = 0; //0 for add 
      this.role.short_name = '';
      this.role.long_name = '';
      this.errors = ''; //clearing errors       
      // Vue.set(this.modalForCode,0);
      this.$bvModal.show('bv-modal-add-role')
    },
    callFunc() {

      if (this.modalForCode == 0) {
        this.addRole();
        // console.log("Add Role");
      } else if (this.modalForCode == 1) {
        this.updateRole();
        // console.log("Edit Role");
      }

    },
    addRole() {
      this.$Progress.start();
      let currObj = this;
      axios.post('/api/role', this.role)
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          currObj.$swal('Info', currObj.output, currObj.status);

          currObj.$bvModal.hide('bv-modal-add-role');

          currObj.role.name = '';

          currObj.errors = ''; //clearing errors

          currObj.$Progress.finish();

          currObj.fetchRoles();

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
    editRole(id) {
      this.$Progress.start();
      this.modalForName = "Edit Role";
      this.modalForCode = 1; // 1 for Edit
      this.$bvModal.show('bv-modal-add-role');
      this.errors = ''; //clearing errors
      axios.get('/api/role/' + id)
        .then(response => {
          // console.log(response.data.role)
          Vue.set(this.role, 'name', response.data.data.name);
          Vue.set(this.role, 'permission_id', response.data.data.permissions[0].id);
          Vue.set(this.role, 'permission_id_old', response.data.data.permissions[0].id);
          Vue.set(this.role, 'id', id); //to send id to the update controller 
          this.$Progress.finish();
        })
        .catch(error => {
          // console.log(error)
          this.$Progress.fail();
        })

    },
    updateRole() {
      this.$Progress.start();
      let currObj = this;
      let formData = new FormData();
      formData.append('_method', 'PUT'); //add this otherwise data won't pass to backend
      formData.append('name', this.role.name);
      formData.append('permission_id', this.role.permission_id);
      formData.append('permission_id_old', this.role.permission_id_old);
      formData.append('id', this.role.id);

      axios.post('/api/role', formData)
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          // alert(currObj.status);

          currObj.$swal('Info', currObj.output, currObj.status);
          currObj.$bvModal.hide('bv-modal-add-role');

          currObj.role.short_name = '';
          currObj.role.long_name = '';
          currObj.errors = ''; //clearing errors

          currObj.$Progress.finish();

          currObj.fetchRoles();

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
    deleteRole(id) {
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
          axios.delete('/api/role/' + id)
            .then(function(response) {
              currObj.output = response.data.msg;
              currObj.status = response.data.status;
              // alert(currObj.status);
              
              let index_to_delete = currObj.roles.findIndex(role => role.id === id)
              currObj.roles.splice(index_to_delete,1);
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


    }, //end of deleteRole()
    searchTableBtn() {
      this.autoCompleteTable();
    },
    autoCompleteTable() {

      this.searchTableKey = this.searchTableKey.toLowerCase();
      if (this.searchTableKey != '') {
        this.isLoading = 'Loading Data...';
        let currObj = this;
        axios.post('/api/roles/search', { searchQuery: this.searchTableKey })
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
        this.fetchRoles();
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
