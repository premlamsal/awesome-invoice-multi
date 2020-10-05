<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Estimates</h1>
    <p class="mb-4">
      <router-link class="btn btn-primary" to="/newEstimate">New Estimate</router-link>
    </p>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary" style="display: inline-block;">Estimates</h6>
        <div v-if="isLoading">{{isLoading}}</div>
        <div class="searchTable">
          <!-- Topbar Search -->
          <div class="input-group">
            <input type="text" class="form-control border-primary small" placeholder="Search for Customer" aria-label="Search" aria-describedby="basic-addon2" v-model="searchTableKey">
            <div class="input-group-append">
              <button class="btn btn-primary" type="button" @click="searchTableBtn">
                <i class="fas fa-search fa-sm"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>Estimate No.</th>
                <th>Grand Total</th>
                <th>Client</th>
                <th>Estimate Date</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Last Modified at</th>
                <th>Modify</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="estimate in estimates" v-bind:key="estimate.id">
                <td>{{estimate.id}}</td>
                <td>Rs. {{estimate.grand_total}}</td>
                <td>{{estimate.customer_name}}</td>
                <td>{{estimate.estimate_date}}</td>
                <td>{{estimate.due_date}}</td>
                <td style="color: #fff;text-align: center;" v-if="(estimate.status==='Paid')">
                  <button class="btn btn-outline-success">
                    {{estimate.status}}
                    <span class="fa fa-check"></span>
                  </button>
                </td>
                <td style="color: #fff;text-align: center;" v-else-if="(estimate.status==='To Pay')">
                  <button class="btn btn-outline-danger">
                    {{estimate.status}}
                    <span class="fa fa-times"></span>
                  </button>
                </td>
                <td>{{estimate.updated_at}}</td>
                <td>
                  <button class="btn btn-outline-primary custom_btn_table" @click="showEstimate(estimate.id)"><span class="fa fa-align-justify custom_icon_table"></span></button>
                  <button class="btn btn-outline-success custom_btn_table" @click="editEstimate(estimate.id)"><span class="fa fa-edit custom_icon_table"></span></button>
                  <button class="btn btn-outline-danger custom_btn_table" @click="deleteEstimate(estimate.id)"><span class="fa fa-trash custom_icon_table"></span></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-md-8">
            <ul class="pagination">
              <li class="page-item" v-bind:class="{disabled:!pagination.first_link}">
                <button @click="fetchEstimates(pagination.first_link)" class="page-link">First</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.prev_link}">
                <button @click="fetchEstimates(pagination.prev_link)" class="page-link">Previous</button>
              </li>
              <li v-for="n in pagination.last_page" v-bind:key="n" class="page-item" v-bind:class="{active:pagination.current_page == n}">
                <button @click="fetchEstimates(pagination.path_page + n)" class="page-link">{{n}}</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.next_link}">
                <button @click="fetchEstimates(pagination.next_link)" class="page-link">Next</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.last_link}">
                <button @click="fetchEstimates(pagination.last_link)" class="page-link">Last</button>
              </li>
            </ul>
          </div>
          <div class="col-md-4">
            Page: {{pagination.current_page}}-{{pagination.last_page}} Total Records: {{pagination.total_pages}}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {

  data() {
    return {
      estimates: [],
      estimate_id: '',
      pagination: {},
      edit: false,
      searchTableKey: '',
      isLoading: ''

    };

  },

  created() {

    this.fetchEstimates();

  },

  methods: {

    fetchEstimates(page_url) {
      this.isLoading = "Loading all Data";
      page_url = page_url || 'api/estimates'
      let vm = this;
      fetch(page_url)
        .then(res => res.json())
        .then(res => {
          // console.log(res);
          this.isLoading = "";
          this.estimates = res.data;
          if ((this.estimates.length) != null) {
            vm.makePagination(res.meta, res.links);
            vm.isLoading = '';
          } else {

          }

        })
        .catch(err => console.log(err));
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

    deleteEstimate(id) {

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
          //delete code here
          fetch('api/estimate/' + id, {
              method: 'delete'
            })
            .then(res => res.json())
            .then(data => {

              this.$swal(
                'Deleted!',
                'Estimate has been deleted.',
                'success'
              );

              this.message = "Estimate Removed";
              this.fetchEstimates();
            })
            .catch(err => console.log(err))

        }
      })

    },
    editEstimate(id) {
      // named route
      this.$router.push({ path: `${id}/editEstimate/` })
    },
    showEstimate(id) {
      // named route

      this.$router.push({ path: `${id}/showEstimate/` })


    },
    searchTableBtn() {
      this.autoCompleteTable();
    },
    autoCompleteTable() {

      this.searchTableKey = this.searchTableKey.toLowerCase();
      if (this.searchTableKey != '') {
        this.isLoading = 'Loading Data...';
        let currObj = this;
        axios.post('/api/estimates/search', { searchTableKey: this.searchTableKey })
          .then(function(response) {

            currObj.isLoading = '';

            currObj.estimates = response.data.data;
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
        this.fetchEstimates();
      }

    } //end of autoCOmpleteTable

  } //end of methods



}; //end of default
</script>
