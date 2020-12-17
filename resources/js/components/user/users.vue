<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Users</h1>
    <p class="mb-4" v-if="hasPermission('add_user')">
      <b-button id="show-btn" @click="showAddModal" class="btn btn-success" style="margin-top: 8px;">
        <span class="fa fa-plus-circle"></span> Add User</b-button>
      <div class="role-permission-block">
        <router-link to="/roles" aria-expanded="false">
          <i class="nc-icon nc-single-02"></i>
          <span>Roles</span>
        </router-link>
        <router-link to="/permissions" aria-expanded="false">
          <i class="nc-icon nc-single-02"></i>
          <span>Permissions</span>
        </router-link>
      </div>
    </p>
    <!-- add user model start -->
    <b-modal id="bv-modal-add-user" hide-footer>
      <template v-slot:modal-title>
        {{modalForName}}
      </template>
      <div class="d-block">
        <div class="form-group">
          <label for="Name">Name</label>
          <input type="hidden" v-model="user.role_id_old">
          <input type="hidden" v-model="user.id">
          <input type="text" :class="['form-control']" v-model="user.name">

          <span v-if="errors.name" :class="['errorText']">{{ errors.name[0] }}</span>
        </div>
        <div class="form-group">
          <label for="Email">Email</label>
          <input type="email" :class="['form-control']" v-model="user.email">
          <span v-if="errors.email" :class="['errorText']">{{ errors.email[0] }}</span>
        </div>
        <div class="form-group">
          <label for="Password">Password</label>
          <input type="password" :class="['form-control']" v-model="user.password">
          <span v-if="errors.password" :class="['errorText']">{{ errors.password[0] }}</span>
        </div>
        <div class="form-group">
          <label for="User Type">User Role</label>
          <select class="form-control" v-model="user.role_id">
            <option selected="" v-for="role in roles" :value="role.id">{{role.name}}</option>
          </select>

          <span v-if="errors.role_id" :class="['errorText']">{{ errors.role_id[0] }}</span>

        </div>
      </div>
      <b-button class="btn-primary mt-3" block @click="callFunc">{{modalForName}}</b-button>
    </b-modal>
    <!-- add user modal end-->
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary" style="display: inline;">Users</h6>
         <div class="text-center" v-if="isLoading=='Loading all Data'">
          <b-spinner variant="success" label="Spinning"></b-spinner>
        </div>
          <div class="export-block">
            <template>
              <vue-blob-json-csv
              @success="handleSuccessExportCSV"
              @error="handleErrorExportCSV"
              file-type="csv"
              file-name="users"
              :fields="users_export_fileds"
              :data="users">
              
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
      <div class="card-body" v-if="users.length > 0">
        <div class="table">
          <table class="table table-striped table-bordered" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Verified at</th>
                <th>Created at</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" v-bind:key="user.id">
                <td>{{user.id}}</td>
                <td>{{user.name}}</td>
                <td>{{user.email}}</td>
                <!-- inner loop for roles since we have data like : users->roles -->
                
                <td v-if="user.roles[0]">{{user.roles[0].name}}</td>
                <td v-else>Not assigned</td>

                <td v-if="user.email_verified_at === null">
                  <span class="bg-danger text-white p-2">
                        Not Verified
                        </span>
                </td>
                <td v-else>
                  <span class="bg-success text-white p-2">
                          {{user.email_verified_at | moment("from", "now")}}
                         </span>
                </td>
                <td>{{user.created_at | moment("from", "now")}}</td>
                <td>
                  <button class="btn btn-outline-success custom_btn_table" @click=editUser(user.id) v-if="hasPermission('edit_user')"><span class="fa fa-edit custom_icon_table"></span></button>
                  <button class="btn btn-outline-danger custom_btn_table" @click=deleteUser(user.id) v-if="hasPermission('delete_user')"><span class="fa fa-trash custom_icon_table"></span></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-md-8">
            <ul class="pagination">
              <li class="page-item" v-bind:class="{disabled:!pagination.first_link}">
                <button @click="fetchUsers(pagination.first_link)" class="page-link">First</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.prev_link}">
                <button @click="fetchUsers(pagination.prev_link)" class="page-link">Previous</button>
              </li>
              <li v-for="n in pagination.last_page" v-bind:key="n" class="page-item" v-bind:class="{active:pagination.current_page == n}">
                <button @click="fetchUsers(pagination.path_page + n)" class="page-link">{{n}}</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.next_link}">
                <button @click="fetchUsers(pagination.next_link)" class="page-link">Next</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.last_link}">
                <button @click="fetchUsers(pagination.last_link)" class="page-link">Last</button>
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

      users: [], //contains all the retrived users from the database

      user: {

      }, //for form single user data

      modalForName: "",
      modalForCode: 0,

      searchTableKey: '',
      errors: [],
      pagination: {},

      isLoading: '',

      store_id: '',

      roles: [],

      users_export_fileds:["name","email","roles","email_verified_at"],

    }
  },
  created() {


    //this block will execute when component created
    this.fetchUsers();
    this.fetchRoles();


  },

  methods: {
    //methods codes here

     handleSuccessExportCSV(){
      console.log("success Export");
    },
    handleErrorExportCSV(){
      console.log("errorExport");
    },
    fetchUsers(page_url) {
      this.$Progress.start();

      this.isLoading = "Loading all Data";
      page_url = page_url || 'api/users'

      let vm = this; // current pointer instance while going inside the another functional instance
      axios.get(page_url)
        .then(function(response) {

          vm.users = response.data.data;

          // console.log(vm.users);
          //filtering out the owner user
          // vm.users = vm.users.filter((user) => {
          //   return user.roles[0].name != 'owner'
          // });
          // console.log(vm.users[0].roles[0]);

          if ((vm.users.length) != null) {
            vm.makePagination(response.data.meta, response.data.links);

          }
          vm.isLoading = '';
          vm.$Progress.finish();


        })
        .catch(function(error) {
          vm.$Progress.fail();
          // vm.$swal('Info',error.message ,'error');

        });


      //above and below code provide same result but above code need current instance pointer for value assignmnent 

      //below code donot need current pointer to be save becasue it execute in current block rather then another block that need previous pointer.


      // axios.get('/api/users')
      // .then(response=>{
      //   // console.log(response.data.data)
      //   this.users=response.data.data
      // })
      // .catch(error=>{
      //   console.log(error)
      // })


    },
    //methods codes here
    fetchRoles(page_url) {
      this.$Progress.start();
      let vm = this; // current pointer instance while going inside the another functional instance
      axios.get('/api/roles')
        .then(function(response) {
          vm.roles = response.data.data;
          // console.log(response.data);
          vm.$Progress.finish();
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
      this.modalForName = "Add User";
      // Vue.set(this.modalForName,"Add User");
      this.modalForCode = 0; //0 for add 
      this.user.name = '';
      this.user.password = '';
      this.user.email = '';
      this.user.role_id = '';
      this.errors = ''; //clearing errors       
      // Vue.set(this.modalForCode,0);
      this.$bvModal.show('bv-modal-add-user')
    },
    callFunc() {

      if (this.modalForCode == 0) {
        this.addUser();
        // console.log("Add User");
      } else if (this.modalForCode == 1) {
        this.updateUser();
        // console.log("Edit User");
      }

    },
    addUser() {
      this.$Progress.start();





      let currObj = this;
      axios.post('/api/user', this.user)
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          currObj.$swal('Info', currObj.output, currObj.status);

          currObj.$bvModal.hide('bv-modal-add-user');
          currObj.user.name = '';
          currObj.user.password = '';
          currObj.user.email = '';
          currObj.user.role_id = '';
          currObj.errors = ''; //clearing errors

          currObj.$Progress.finish();

          currObj.fetchUsers();

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
    editUser(id) {
      this.$Progress.start();
      this.modalForName = "Edit User";
      this.modalForCode = 1; // 1 for Edit
      this.$bvModal.show('bv-modal-add-user');
      this.errors = ''; //clearing errors
      axios.get('/api/user/' + id)
        .then(response => {
          // console.log(response.data.user)
          Vue.set(this.user, 'name', response.data.user.name);
          Vue.set(this.user, 'email', response.data.user.email);
          Vue.set(this.user, 'type', response.data.user.type);
          Vue.set(this.user, 'role_id', response.data.user.roles[0].id);
          Vue.set(this.user, 'role_id_old', response.data.user.roles[0].id);
          Vue.set(this.user, 'id', id); //to send id to the update controller 
          this.$Progress.finish();
        })
        .catch(error => {
          // console.log(error)
          this.$Progress.fail();
        })

    },
    updateUser() {
      this.$Progress.start();
      let currObj = this;
      let formData = new FormData();
      formData.append('_method', 'PUT'); //add this otherwise data won't pass to backend
      formData.append('name', this.user.name);
      formData.append('email', this.user.email);
      formData.append('type', this.user.type);
      formData.append('password', this.user.password);
      formData.append('role_id_old', this.user.role_id_old);
      formData.append('role_id', this.user.role_id);
      formData.append('id', this.user.id);

      axios.post('/api/user', formData)
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          // alert(currObj.status);

          currObj.$swal('Info', currObj.output, currObj.status);
          currObj.$bvModal.hide('bv-modal-add-user');

          currObj.user.name = '';
          currObj.user.password = '';
          currObj.user.email = '';
          currObj.user.role = '';
          currObj.errors = ''; //clearing errors
          currObj.$Progress.finish();

          currObj.fetchUsers();

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
    deleteUser(id) {
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
          axios.delete('/api/user/' + id)
            .then(function(response) {
              currObj.output = response.data.msg;
              currObj.status = response.data.status;
              // alert(currObj.status);

              let index_to_delete = currObj.users.findIndex(user => user.id === id)
              currObj.users.splice(index_to_delete, 1);
              currObj.$Progress.finish();
              // alert(currObj.status);
              currObj.$swal("Info", currObj.output, currObj.status);

            }).catch(function(error) {
              currObj.$Progress.finish();
              // currObj.output=error;
              // console.log(currObj.output);
            })

        }


      });


    }, //end of deleteUser()
    searchTableBtn() {
      this.autoCompleteTable();
    },
    autoCompleteTable() {

      this.searchTableKey = this.searchTableKey.toLowerCase();
      if (this.searchTableKey != '') {
        this.isLoading = 'Loading Data...';
        let currObj = this;
        axios.post('/api/users/search', { searchQuery: this.searchTableKey })
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
        this.fetchUsers();
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
