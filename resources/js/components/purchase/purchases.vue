<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Purchases</h1>
    <p class="mb-4" v-if="hasPermission('add_purchase')">
      <router-link class="btn btn-primary" to="/newPurchase">New Purchase</router-link>
    </p>
    <!-- DataTales Example -->
    <!-- <div class="dataSelector">
      <h6>Filter data by</h6>
      <a href="#" @click="filterBy('ToPay')">To Pay</a>
    </div> -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary" style="display: inline-block;">Purchases</h6>
         <div class="text-center" v-if="isLoading=='Loading all Data'">
          <b-spinner variant="success" label="Spinning"></b-spinner>
        </div>
          <div class="export-block">
            <template>
              <vue-blob-json-csv
              @success="handleSuccessExportCSV"
              @error="handleErrorExportCSV"
              file-type="csv"
              file-name="purchases"
              :fields="purchases_export_fileds"
              :data="purchases">
              
              <!-- <button class="btn btn-warning-success"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button> -->
                <img src="img/icon-red-csv.png" class="icon-red-csv-export" alt="Export data to CSV">
            </vue-blob-json-csv>
          </template>
        </div>

        
        <!-- {{isLoading}} -->
        <div class="searchTable">
          <!-- Topbar Search -->
          <!-- <div class="input-group"> -->
          <div class="input-group no-border">
            <input type="text" value class="form-control" placeholder="Search..." v-model="searchTableKey" @keyup.enter="searchTableBtn" />
            <div class="input-group-append">
              <div class="input-group-text">
                <i class="nc-icon nc-zoom-split" @click="searchTableBtn"></i>
              </div>
            </div>
          </div>
          <!-- </div> -->
        </div>
      </div>
      <div class="card-body" v-if="purchases.length > 0">
        <div class="table">
          <table class="table table-striped table-bordered" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>Purchase No.</th>
                <th>Purchase RefId.</th>
                <th>Grand Total</th>
                <th>Supplier</th>
                <th>Purchase Date</th>
                <th>Due Date</th>
               <!-- <th>Status</th> -->
                <th>Last Modified at</th>
                <th>Modify</th>
              </tr>
            </thead>
            <!--  <tfoot>
                    <tr>
                      <th>Purchase No.</th>
                      <th>Grand Total</th>
                      <th>Purchase</th>
                      <th>Purchase Date</th>
                      <th>Due Date</th>
                      <th>Created At</th>
                    </tr>
            </tfoot>-->
            <tbody>
              <tr v-for="purchase in purchases" v-bind:key="purchase.id">
                <td>{{purchase.custom_purchase_id}}</td>
                <td>{{purchase.purchase_reference_id}}</td>
                <td>Rs. {{purchase.grand_total}}</td>
                <td>{{purchase.supplier_name}}</td>
                <td>{{purchase.purchase_date}}</td>
                <td>{{purchase.due_date | moment("from", "now")}}</td>
      
              <!--
               <td v-if="(purchase.status==='Paid')">
                       <toggle-button v-bind:status="true" @statusChanges ="updateStatus($event,purchase.id)"/> 
                </td>
                
                <td v-else-if="(purchase.status==='To Pay')">
                       <toggle-button v-bind:status="false" @statusChanges ="updateStatus($event,purchase.id)"/> 
                </td>
                -->

                <td>{{purchase.updated_at | moment("from", "now")}}</td>
                <td>
                  <button class="btn btn-outline-primary custom_btn_table" @click="showPurchase(purchase.id)"  v-if="hasPermission('show_purchase')">
                    <span class="fa fa-align-justify custom_icon_table"></span>
                  </button>
                  <button class="btn btn-outline-success custom_btn_table" @click="editPurchase(purchase.id)" v-if="hasPermission('edit_purchase')">
                    <span class="fa fa-edit custom_icon_table" ></span>
                  </button>
                  <!--  <button class="btn btn-outline-success" @click="returnPurchase(purchase.id)" style="margin-right: 5px;"><span class="fa fa-reply" style="font-size: 15px"></span>
                  </button>-->
                  <button class="btn btn-outline-danger custom_btn_table" @click="deletePurchase(purchase.id)" v-if="hasPermission('delete_purchase')">
                    <span class="fa fa-trash custom_icon_table"></span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-md-8">
            <ul class="pagination">
              <li class="page-item" v-bind:class="{disabled:!pagination.first_link}">
                <button @click="fetchPurchases(pagination.first_link)" class="page-link">First</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.prev_link}">
                <button @click="fetchPurchases(pagination.prev_link)" class="page-link">Previous</button>
              </li>
              <li v-for="n in pagination.last_page" v-bind:key="n" class="page-item" v-bind:class="{active:pagination.current_page == n}">
                <button @click="fetchPurchases(pagination.path_page + n)" class="page-link">{{n}}</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.next_link}">
                <button @click="fetchPurchases(pagination.next_link)" class="page-link">Next</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.last_link}">
                <button @click="fetchPurchases(pagination.last_link)" class="page-link">Last</button>
              </li>
            </ul>
          </div>
          <div class="col-md-4">
            Page: {{pagination.current_page}}-{{pagination.last_page}} Total Records: {{pagination.total_pages}}
          </div>
        </div>
      </div>
      <div class="errorDivEmptyData" v-else>No Data Found</div>
    </div>
  </div>
</template>

<script>

//custome toggle button
import ToggleButton from "../widgets/ToggleButton";

export default {

  components:{
      ToggleButton,
  },
  data() {
    return {
      purchases: [],
      purchase_id: "",
      pagination: {},
      edit: false,
      searchTableKey: "",
      tempStatus:{},
      isLoading: "",
      purchases_export_fileds:["custom_purchase_id","grand_total","supplier_name","status","purchase_date","due_date"],
    };
  },

  created() {
    this.fetchPurchases();
  },

  methods: {
    fetchPurchases(page_url) {
      this.$Progress.start();
      this.isLoading = "Loading all Data";
      page_url = page_url || "/api/purchases";
      let vm = this;

      axios
        .get(page_url)
        .then(function(response) {
          vm.purchases = response.data.data;
          vm.isLoading = "";
          if (vm.purchases.length != null) {
            vm.makePagination(response.data.meta, response.data.links);
            vm.$Progress.finish();

            vm.isLoading = "";
          }
        })

      .catch(err => this.$Progress.fail());
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
      };
      this.pagination = pagination;
    },

    deletePurchase(id) {
      this.$Progress.start();
      let currObj = this;

      this.$swal({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then(result => {
        if (result.value) {
          //delete code here

          axios
            .delete("api/purchase/" + id)

          .then(function(response) {
              // alert('Purchase Removed');
              currObj.output = response.data.msg;
              currObj.status = response.data.status;
              // alert(currObj.status);
              
              let index_to_delete = currObj.purchases.findIndex(purchase => purchase.id === id)
              currObj.purchases.splice(index_to_delete,1);
              currObj.$Progress.finish();
              // alert(currObj.status);
              currObj.$swal("Info", currObj.output, currObj.status);
            })
            .catch(err => this.$Progress.fail());
        }
      });
    },

    editPurchase(id) {
      // named route
      // this.$router.push({ name: 'editPurchase', params: { id } });
      this.$router.push({ path: `${id}/editPurchase/` });
    },
    returnPurchase(id) {
      // named route
      // this.$router.push({ name: 'returnPurchase', params: { id } });
      this.$router.push({ path: `${id}/returnPurchase/` });
    },
    showPurchase(id) {
      // named route

      // this.$router.push({ name: 'showPurchase', params: { id } }); //will hide parameter in url

      this.$router.push({ path: `${id}/showPurchase/` });
    },
    filterBy(action){

      this.purchases= this.purchases.filter((purchase) => {
            return purchase.status === 'To Pay'
          });

    },
    searchTableBtn() {
      this.autoCompleteTable();
    },
     updateStatus(event,key){
      
      this.tempStatus[key]=event;

      let formData=new FormData();
      formData.append("_method","PUT");
      formData.append("key",key);
      if(event==true){
        formData.append("value","Paid");  
      }
      else{
        formData.append("value","To Pay");
      }
      
      axios.post('/api/changePurchaseStatus',formData)
      .then(response=>{

         this.$toast.success({
          title: response.data.status,
          message: response.data.msg
        });



      })
      .catch(error=>{

        this.$toast.error({
          title: "Error",
          message: "some error occured"
        });

        this.fetchPurchases();


      });
    },
    autoCompleteTable() {
      this.searchTableKey = this.searchTableKey.toLowerCase();
      if (this.searchTableKey != "") {
        this.isLoading = "Loading Data...";
        let currObj = this;
        axios
          .post("/api/purchases/search", { searchQuery: this.searchTableKey })
          .then(function(response) {
            currObj.isLoading = "";

            currObj.purchases = response.data.data;
            if (response.data.data == "") {
              currObj.isLoading = "No Data Found";
            }
            // if((this.estimates.length)!=null){
            // // currObj.makePagination(res.meta,res.links);
            // }
            // currObj.status=response.data.status;
            currObj.errors = ""; //clearing errors
          })
          .catch(function(error) {
            if (error.response.status == "422") {
              currObj.validationErrors = error.response.data.errors;
              currObj.errors = currObj.validationErrors;
              currObj.isLoading = "Load Failed...";
              // console.log(currObj.errors);
            }
          });
      } else {
        this.isLoading = "Loading all Data";
        this.fetchPurchases();
      }
    }, //end of autoCOmpleteTable
    hasPermission(action) {
      let permissions_from_store = this.$store.getters.permissions;

      if (
        permissions_from_store.includes(action) ||
        permissions_from_store.includes("all")
      ) {
        return true;
      } else {
        return false;
      }
    } //has permision
  } //end of methods
}; //end of default
</script>
