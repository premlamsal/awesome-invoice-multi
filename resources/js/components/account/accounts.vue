<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Account</h1>
    <p class="mb-4" >
      <div id="btn addaccount" v-if="hasPermission('add_account')">
        <b-button id="show-btn" @click="showAddModal" class="btn btn-success" style="margin-top: 8px;">
          <span class="fa fa-plus-circle"></span> Add Account</b-button>
      </div>
    </p>
    <!-- add account model start -->
    <b-modal id="bv-modal-add-account" hide-footer>
      <template v-slot:modal-title>
        <span class="text-primary">{{modalForName}}</span>
      </template>
      <div class="d-block">
        <div class="form-group">
          <input type="hidden" v-model="account.id">
          <label for="Name">Name:</label>
          <!--  <input type="text"  v-model="account.name" :class="['form-control', errors.name ? 'is-invalid' : '']"> -->
          <input type="text" v-model="account.name" :class="['form-control']">
          <span v-if="errors.name" :class="['errorText']">{{ errors.name[0] }}</span>
        </div>
        <div class="form-group">
          <label for="bank_name">Bank Name:</label>
          <input type="text" v-model="account.bank_name" :class="['form-control']">
          <span v-if="errors.address" :class="['errorText']">{{ errors.bank_name[0] }}</span>
        </div>
         <div class="form-group">
          <label for="holder_name">Account Holder:</label>
          <input type="text" v-model="account.holder_name" :class="['form-control']">
          <span v-if="errors.address" :class="['errorText']">{{ errors.holder_name[0] }}</span>
        </div>
        <div class="form-group">
          <label for="bank_acc_num">Account Number:</label>
          <input type="text" v-model="account.bank_acc_num" :class="['form-control']">
          <span v-if="errors.phone" :class="['errorText']">{{ errors.bank_acc_num[0] }}</span>
        </div>
         <div class="form-group">
          <label for="opening_balance">Opening Balance:</label>
          <input type="text" v-model="account.opening_balance" :class="['form-control']">
          <span v-if="errors.opening_balance" :class="['errorText']">{{ errors.opening_balance[0] }}</span>
        </div>
        <div class="form-group">
          <label for="account_info">Account Information :</label>
          <textarea v-model="account.account_info" :class="['form-control']"></textarea>
          <span v-if="errors.details" :class="['errorText']">{{ errors.account_info[0] }}</span>
        </div>
      </div>
      <b-button class="btn-primary mt-3" block @click="callFunc">{{modalForName}}</b-button>
    </b-modal>
    <!-- add account modal end-->
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary" style="display: inline-block;">Accounts</h6>
               
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
              :fields="accounts_export_fileds"
              :data="accounts">
              
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
      <div class="card-body" v-if="accounts.length > 0 ">
         
        <div class="table">
          <table class="table table-striped table-bordered" width="100%" cellspacing="0">
            <thead>
              <tr>
                <!-- <th>ID</th> -->
                <th>Name</th>
                <th>Balance</th>
                <th>Acc. Holder</th>
                <th>Bank</th>
                <th>Bank Acc. Number</th>
               <!--  <th>Last Updated at</th> -->
              </tr>
            </thead>
            <tbody>
              <tr v-for="account in accounts" v-bind:key="account.id">
                <!-- <td>{{account.id}}</td> -->
                <td @click="accountProfile(account.id)" class="cursor">{{account.name}}</td>
                <td>Rs. {{account.balance}}</td>
                <td>{{account.holder_name}}</td>
                <td>{{account.bank_name}}</td>
                <td>{{account.bank_acc_num}}</td>
               <!--   <td>{{account.updated_at | moment("from", "now")}}</td> -->
                <td>
                  <button class="btn btn-outline-success custom_btn_table" @click=editAccount(account.id) v-if="hasPermission('edit_account')"><span class="fa fa-edit custom_icon_table"></span></button>
                  <button class="btn btn-outline-danger custom_btn_table" @click=deleteAccount(account.id) v-if="hasPermission('delete_account')"><span class="fa fa-trash custom_icon_table"></span></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-md-8">
            <ul class="pagination">
              <li class="page-item" v-bind:class="{disabled:!pagination.first_link}">
                <button @click="fetchAccounts(pagination.first_link)" class="page-link">First</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.prev_link}">
                <button @click="fetchAccounts(pagination.prev_link)" class="page-link">Previous</button>
              </li>
              <li v-for="n in pagination.last_page" v-bind:key="n" class="page-item" v-bind:class="{active:pagination.current_page == n}">
                <button @click="fetchAccounts(pagination.path_page + n)" class="page-link">{{n}}</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.next_link}">
                <button @click="fetchAccounts(pagination.next_link)" class="page-link">Next</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.last_link}">
                <button @click="fetchAccounts(pagination.last_link)" class="page-link">Last</button>
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

      accounts: [], //contains all the retrived accounts from the database

      account: {}, //for form single account data

      modalForName: "",
      modalForCode: 0,

      searchTableKey: '',
      errors: [],
      pagination: {},

      isLoading: '',

      permissions: [],

      accounts_export_fileds:["name","balance","balan","details"],
    }
  },

  async created() {

    this.fetchAccounts();

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
    accountProfile(id){
      this.$router.push({ path: `${id}/account-profile/` });
    },

    getPermissions: async function() {

      // this.permissions=['view_account','add_account','edit_account','delete_account'];
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
    fetchAccounts(page_url) {
      this.$Progress.start();
      this.isLoading = "Loading all Data";
     
      let vm = this; // current pointer instance while going inside the another functional instance
      page_url = page_url || 'api/accounts'
      axios.get(page_url)
        .then(function(response) {
          vm.accounts = response.data.data;
          if ((vm.accounts.length) != null) {
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
      this.modalForName = "Add Account";
      // Vue.set(this.modalForName,"Add Account");
      this.modalForCode = 0; //0 for add 

        this.account.name = '';
          this.account.balance = '';
          this.account.opening_balance = '';
          this.account.bank_name = '';
          this.account.holder_name = '';
          this.account.account_info = '';
          this.account.bank_acc_num = '';

      this.errors = ''; //clearing errors

      // Vue.set(this.modalForCode,0);
      this.$bvModal.show('bv-modal-add-account');

    },
    callFunc() {

      if (this.modalForCode == 0) {
        this.addAccount();
        // console.log("Add Account");
      } else if (this.modalForCode == 1) {
        this.updateAccount();
        // console.log("Edit Account");
      }

    },
    addAccount() {

      this.$Progress.start();


      // if(this.isExistPermission('add_account')){
      //     console.log('can add account')
      //     }
      //     else{
      //       console.log('access denied')
      //     }       
      let currObj = this;
      axios.post('/api/account', this.account)
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          currObj.$swal('Info', currObj.output, currObj.status);

          currObj.$bvModal.hide('bv-modal-add-account');


          currObj.account.name = '';
          currObj.account.balance = '';
          currObj.account.opening_balance = '';
          currObj.account.bank_name = '';
          currObj.account.holder_name = '';
          currObj.account.account_info = '';
          currObj.account.bank_acc_num = '';


          currObj.errors = ''; //clearing errors

          currObj.fetchAccounts();
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
    editAccount(id) {
      this.$Progress.start();
      let currObj = this;
      this.modalForName = "Edit Account";
      this.modalForCode = 1; // 1 for Edit
      this.$bvModal.show('bv-modal-add-account');
      currObj.errors = ''; //clearing errors
      axios.get('/api/account/' + id)
        .then(response => {
          // console.log(response.data.account)
          Vue.set(this.account, 'name', response.data.account.name);
          Vue.set(this.account, 'balance', response.data.account.balance);
          Vue.set(this.account, 'bank_name', response.data.account.bank_name);
          Vue.set(this.account, 'bank_acc_num', response.data.account.bank_acc_num);
          Vue.set(this.account, 'opening_balance', response.data.account.opening_balance);
          Vue.set(this.account, 'balance', response.data.account.balance);
          Vue.set(this.account, 'account_info', response.data.account.account_info);
          Vue.set(this.account, 'holder_name', response.data.account.holder_name);

          Vue.set(this.account, 'id', id); //to send id to the update controller 
          this.$Progress.finish();
        })
        .catch(error => {
          // console.log(error)
          this.$Progress.fail();
        })

    },
    updateAccount() {

      this.$Progress.start();
      let currObj = this;
      let formData = new FormData();
      formData.append('_method', 'PUT'); //add this otherwise data won't pass to backend
      formData.append('name', this.account.name);
      // formData.append('balance', this.account.balance);
      formData.append('bank_name', this.account.bank_name);
      formData.append('opening_balance', this.account.opening_balance);
      formData.append('account_info', this.account.account_info);
      formData.append('bank_acc_num', this.account.bank_acc_num);
      formData.append('holder_name', this.account.holder_name);
      formData.append('id', this.account.id);

      axios.post('/api/account', formData)
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;

          currObj.$swal('Info', currObj.output, currObj.status);
          currObj.$bvModal.hide('bv-modal-add-account');

          currObj.account.name = '';
          currObj.account.balance = '';
          currObj.account.opening_balance = '';
          currObj.account.bank_name = '';
          currObj.account.holder_name = '';
          currObj.account.account_info = '';
          currObj.account.bank_acc_num = '';
          currObj.errors = ''; //clearing errors
          currObj.$Progress.finish();
          currObj.fetchAccounts();

        }).catch(function(error) {
          currObj.$Progress.fail();
          if (error.response.status == 422) {
            currObj.validationErrors = error.response.data.errors;
            currObj.errors = currObj.validationErrors;
            // console.log(currObj.errors);
          }
        })



    },
    deleteAccount(id) {
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
          axios.delete('/api/account/' + id)
            .then(function(response) {
              currObj.output = response.data.msg;
              currObj.status = response.data.status;
              // alert(currObj.status);
              currObj.$swal('Info', currObj.output, currObj.status);
              currObj.$Progress.finish();
              currObj.fetchAccounts();

            }).catch(function(error) {
              currObj.$Progress.fail();

              // currObj.output=error;

              // console.log(currObj.output);
            })

        }


      });


    }, //end of deleteAccount()
    searchTableBtn() {
      this.autoCompleteTable();
    },
    autoCompleteTable() {

      this.searchTableKey = this.searchTableKey.toLowerCase();
      if (this.searchTableKey != '') {
        this.isLoading = 'Loading Data...';
        let currObj = this;
        axios.post('/api/accounts/search', { searchQuery: this.searchTableKey })
          .then(function(response) {

            currObj.isLoading = '';

            currObj.accounts = response.data.data;
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
        this.fetchAccounts();
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
