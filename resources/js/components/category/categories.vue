<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Category</h1>
    <p class="mb-4" v-if="hasPermission('add_category')">
      <b-button id="show-btn" @click="showAddModal" class="btn btn-success" style="margin-top: 8px;">
        <span class="fa fa-plus-circle"></span> Add Category</b-button>
    </p>
    <!-- add category model start -->
    <b-modal id="bv-modal-add-category" hide-footer>
      <template v-slot:modal-title>
        {{modalForName}}
      </template>
      <div class="d-block">
        <div class="form-group">
          <label for="Name">Name</label>
          <input type="hidden" v-model="category.id">
          <input type="text" :class="['form-control']" placeholder="ex. Tiles, Sanitary" v-model="category.name">
          <span v-if="errors.name" :class="['errorText']">{{ errors.name[0] }}</span>
        </div>
        <div class="form-group">
          <label for="Description">Description</label>
          <textarea :class="['form-control']" placeholder="ex. Tiles Vetrified, Sanitary Commode" v-model="category.description"></textarea>
          <span v-if="errors.description" :class="['errorText']">{{ errors.description[0] }}</span>
        </div>
      </div>
      <b-button class="btn-primary mt-3" block @click="callFunc">{{modalForName}}</b-button>
    </b-modal>
    <!-- add category modal end-->
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary" style="display: inline;">Categories</h6>
        
         <div class="text-center" v-if="isLoading=='Loading all Data'">
          <b-spinner variant="success" label="Spinning"></b-spinner>
        </div>
          <div class="export-block">
            <template>
              <vue-blob-json-csv
              @success="handleSuccessExportCSV"
              @error="handleErrorExportCSV"
              file-type="csv"
              file-name="categories"
              :fields="categories_export_fileds"
              :data="categories">
              
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
      <div class="card-body" v-if="categories.length > 0">
        <div class="table">
          <table class="table table-striped table-bordered" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Updated at</th>
                <th>Modify</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="category in categories" v-bind:key="category.id">
                <td>{{category.name}}</td>
                <td>{{category.description}}</td>
                <td>{{category.updated_at | moment("from", "now")}}</td>
                <td>
                  <button class="btn btn-outline-success custom_btn_table" style="margin-right: 5px;" @click=editCategory(category.id) v-if="hasPermission('edit_category')"><span class="fa fa-edit custom_icon_table"></span></button>
                  <button class="btn btn-outline-danger custom_btn_table" @click=deleteCategory(category.id) v-if="hasPermission('delete_category')"><span class="fa fa-trash custom_icon_table"></span></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-md-8">
            <ul class="pagination">
              <li class="page-item" v-bind:class="{disabled:!pagination.first_link}">
                <button @click="fetchCategories(pagination.first_link)" class="page-link">First</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.prev_link}">
                <button @click="fetchCategories(pagination.prev_link)" class="page-link">Previous</button>
              </li>
              <li v-for="n in pagination.last_page" v-bind:key="n" class="page-item" v-bind:class="{active:pagination.current_page == n}">
                <button @click="fetchCategories(pagination.path_page + n)" class="page-link">{{n}}</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.next_link}">
                <button @click="fetchCategories(pagination.next_link)" class="page-link">Next</button>
              </li>
              <li class="page-item" v-bind:class="{disabled:!pagination.last_link}">
                <button @click="fetchCategories(pagination.last_link)" class="page-link">Last</button>
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

      categories: [], //contains all the retrived categories from the database

      category: {}, //for form single category data

      modalForName: "",
      modalForCode: 0,

      searchTableKey: '',
      errors: [],
      pagination: {},
      isLoading: '',
      categories_export_fileds:["name","description"],

    }
  },
  created() {
    //this block will execute when component created
    this.fetchCategories();
  },

  methods: {
    //methods codes here
     handleSuccessExportCSV(){
      console.log("success Export");
    },
    handleErrorExportCSV(){
      console.log("errorExport");
    },
    fetchCategories(page_url) {
      this.$Progress.start();
      this.isLoading = "Loading all Data";
      page_url = page_url || 'api/categories'

      let vm = this; // current pointer instance while going inside the another functional instance
      axios.get(page_url)
        .then(function(response) {
          vm.categories = response.data.data;
          // console.log(response.data);
          if ((vm.categories.length) != null) {
            vm.makePagination(response.data.meta, response.data.links);
            vm.isLoading = '';
            vm.$Progress.finish();
          }

        })
        .catch(function(error) {

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
      this.modalForName = "Add Category";
      // Vue.set(this.modalForName,"Add Category");
      this.modalForCode = 0; //0 for add 
      this.category.name = '';
      this.category.description = '';
      this.errors = ''; //clearing errors       
      // Vue.set(this.modalForCode,0);
      this.$bvModal.show('bv-modal-add-category')
    },
    callFunc() {

      if (this.modalForCode == 0) {
        this.addCategory();
        // console.log("Add Category");
      } else if (this.modalForCode == 1) {
        this.updateCategory();
        // console.log("Edit Category");
      }

    },
    addCategory() {
      this.$Progress.start();
      let currObj = this;
      axios.post('/api/category', this.category)
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          currObj.$swal('Info', currObj.output, currObj.status);

          currObj.$bvModal.hide('bv-modal-add-category');

          currObj.errors = ''; //clearing errors

          currObj.category.name = '';
          currObj.category.description = '';
          currObj.$Progress.finish();

          currObj.fetchCategories();


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
    editCategory(id) {
      this.$Progress.start();
      // //this.$Progress.start();     
      this.modalForName = "Edit Category";
      this.modalForCode = 1; // 1 for Edit
      this.$bvModal.show('bv-modal-add-category');
      this.errors = ''; //clearing errors
      axios.get('/api/categories/' + id)
        .then(response => {
          // console.log(response.data.category)
          Vue.set(this.category, 'name', response.data.category.name);
          Vue.set(this.category, 'description', response.data.category.description);
          Vue.set(this.category, 'id', id); //to send id to the update controller 
          this.$Progress.finish();


        })
        .catch(error => {
          // console.log(error)
          this.$Progress.fail();


        })

    },
    updateCategory() {
      this.$Progress.start();
      let currObj = this;
      let formData = new FormData();
      formData.append('_method', 'PUT'); //add this otherwise data won't pass to backend
      formData.append('name', this.category.name);
      formData.append('description', this.category.description);
      formData.append('id', this.category.id);

      axios.post('/api/category', formData)
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          // alert(currObj.status);

          currObj.$swal('Info', currObj.output, currObj.status);
          currObj.$bvModal.hide('bv-modal-add-category');

          currObj.category.name = '';
          currObj.category.description = '';
          currObj.errors = ''; //clearing errors
          currObj.$Progress.finish();

          currObj.fetchCategories();

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
    deleteCategory(id) {
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
          axios.delete('/api/category/' + id)
            .then(function(response) {
              currObj.output = response.data.msg;
              currObj.status = response.data.status;
              // alert(currObj.status);
              
              let index_to_delete = currObj.categories.findIndex(category => category.id === id)
              currObj.categories.splice(index_to_delete,1);
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


    }, //end of deleteCategory()
    searchTableBtn() {
      this.autoCompleteTable();
    },
    autoCompleteTable() {

      this.searchTableKey = this.searchTableKey.toLowerCase();
      if (this.searchTableKey != '') {
        this.isLoading = 'Loading Data...';
        let currObj = this;
        axios.post('/api/categories/search', { searchQuery: this.searchTableKey })
          .then(function(response) {

            currObj.isLoading = '';
 
 

            currObj.categories=response.data.data;


            console.log(currObj.categories);

            if (response.data.data == "") {

              currObj.isLoading = "No Data Found";

            }
          
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
        this.fetchCategories();
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


}
</script>
