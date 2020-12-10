<template>
  <div>
    <!-- Page Heading -->
    <!-- <h1 class="h3 mb-2 text-gray-800">Stores</h1> -->
    <!-- Store -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Company Profile</h6>
         <div class="text-center" v-if="isLoading=='Loading all Data'">
          <b-spinner variant="success" label="Spinning"></b-spinner>
        </div>
       <!--  <div v-if="isLoading">{{isLoading}}</div> -->
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <form @submit="formSubmit" enctype="multipart/form-data">
              <div class="form-group">
                <label>Company Name</label>
                <input type="text" :class="['form-control']" placeholder="Company Name" v-model="store.name">
                <span v-if="errors.name" :class="['errorText']">{{ errors.name[0] }}</span>
              </div>
              <div class="form-group">
                <label>Company Details</label>
                <textarea :class="['form-control']" v-model="store.detail">{{store.detail}}</textarea>
                <span v-if="errors.detail" :class="['errorText']">{{ errors.detail[0] }}</span>
              </div>
              <div class="form-group">
                <label>Company Email</label>
                <input type="text" :class="['form-control']" placeholder="Company Email" v-model="store.email">
                <span v-if="errors.email" :class="['errorText']">{{ errors.email[0] }}</span>
              </div>
              <div class="form-group">
                <label>Company Address</label>
                <input type="text" :class="['form-control']" placeholder="Company Address" v-model="store.address">
                <span v-if="errors.address" :class="['errorText']">{{ errors.address[0] }}</span>
              </div>
              <div class="form-group">
                <label>Company Phone</label>
                <input type="phone" :class="['form-control']" placeholder="Company Phone" v-model="store.phone">
                <span v-if="errors.phone" :class="['errorText']">{{ errors.phone[0] }}</span>
              </div>
              <div class="form-group">
                <label>Company Mobile</label>
                <input type="phone" :class="['form-control']" placeholder="Company Mobile" v-model="store.mobile">
                <span v-if="errors.mobile" :class="['errorText']">{{ errors.mobile[0] }}</span>
              </div>
              <div class="form-group">
                <label>Tax Percentage</label>
                <input type="number" :class="['form-control']" placeholder="Tax Percentage" v-model="store.tax_percentage">
                <span v-if="errors.tax_percentage" :class="['errorText']">{{ errors.tax_percentage[0] }}</span>
              </div>
              <div class="form-group">
                <label>Tax Number</label>
                <input type="number" :class="['form-control']" placeholder="Tax Number" v-model="store.tax_number">
                <span v-if="errors.tax_number" :class="['errorText']">{{ errors.tax_number[0] }}</span>
              </div>
              <div class="form-group">
                <label>Profit percentage</label>
                <input type="number" :class="['form-control']" placeholder="Profit Percentage" v-model="store.profit_percentage">
                <span v-if="errors.profit_percentage" :class="['errorText']">{{ errors.profit_percentage[0] }}</span>
              </div>
              <div class="form-group">
                <label>Company Website</label>
                <input type="text" :class="['form-control']" placeholder="http://example.com" v-model="store.url">
                <span v-if="errors.url" :class="['errorText']">{{ errors.company_url[0] }}</span>
              </div>
              <div class="form-group">
                <label>Company Logo</label>
                <br/>
                <img v-bind:src="imagePreview" class="company_logo_img" />
                <input type="file" :class="['form-control']" v-on:change="fileSelected">
                <span v-if="errors.image" :class="['errorText']">{{ errors.image[0] }}</span>
              </div>
              <button class="btn btn-primary" v-if="hasPermission('update_store')">Save</button>
            </form>
          </div>
          <div class="col-md-1"></div>
          <div class="col-md-2">
            <div class="card" style="width: 18rem;">
              <!--  <div class="card-body">
                        <p class="card-text bg-primary text-white p-2" style="text-align: center;">{{store.product_id_count}}</p>
                        <p class="card-text bg-success text-white p-2" style="text-align: center;">{{store.invoice_id_count}}</p>
                        <p class="card-text bg-danger text-white p-2" style="text-align: center;">{{store.purchase_id_count}}</p>
                      </div> -->
            </div>
          </div>
          <div class="col-md-1"></div>
          <div class="col-md-3">
            <div class="card" style="width: 18rem;">
              <img class="card-img-top image" v-bind:src="store.store_logo" alt="card image cap">
              <div class="card-body">
                <h4 style="text-align: center;" class="text-primary-custom">{{store.name}}</h4>
                <p class="card-text" style="text-align: center;">{{store.address}}</p>
                <p class="card-text" style="text-align: center;">{{store.phone}}</p>
                <p class="card-text" style="text-align: center;">{{store.mobile}}</p>
                <p class="card-text" style="text-align: center;">{{store.tax_percentage}}% TAX</p>
                <p class="card-text" style="text-align: center;">{{store.tax_number}}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- end of card body -->
    </div>
  </div>
</template>

<style>
.image {
  margin: 0 auto;
  width: 225px;
  height: 225px;
}
</style>

<script>
export default {

  data() {

    return {
      //list of data goes here

      store: {},
      image: '',
      selectedFile: '',

      errors: [],
      isLoading: '',
      imagePreview: '',




    }; //end of return block inside of data block

  }, //end of data block

  created() {

    this.store.store_logo = "/img/a.svg";
    this.fetchStore();
    this.setAvtarUploadImage();

  },


  methods: {
    //list of methods goes here
    fetchStore() {
      this.$Progress.start();
      let currObj = this;
     this.isLoading = "Loading all Data";

      axios.get('api/store')

      .then(function(response) {
        Vue.set(currObj.store, 'id', response.data.store.id)
        Vue.set(currObj.store, 'name', response.data.store.name)
        Vue.set(currObj.store, 'detail', response.data.store.detail)
        Vue.set(currObj.store, 'tax_number', response.data.store.tax_number)
        Vue.set(currObj.store, 'tax_percentage', response.data.store.tax_percentage)
        Vue.set(currObj.store, 'profit_percentage', response.data.store.profit_percentage)
        Vue.set(currObj.store, 'email', response.data.store.email)
        Vue.set(currObj.store, 'address', response.data.store.address)
        Vue.set(currObj.store, 'phone', response.data.store.phone)
        Vue.set(currObj.store, 'mobile', response.data.store.mobile)
        Vue.set(currObj.store, 'url', response.data.store.url)
          // Vue.set(currObj.store, 'product_id_count', response.data.store.product_id_count)
          // Vue.set(currObj.store, 'invoice_id_count', response.data.store.invoice_id_count)
          // Vue.set(currObj.store, 'purchase_id_count', response.data.store.purchase_id_count)

        if (response.data.store.store_logo != null) {
          //company image
          Vue.set(currObj.store, 'store_logo', "/img/" + response.data.store.store_logo)

          // this.company_logo="/img/"+data.store.company_logo //concatenate image location and image name
        }
        currObj.$Progress.finish();
        // console.log(data.store.company_name)
        currObj.isLoading = ''
      })

    }, //end of fetchStores()

    setAvtarUploadImage() {

      this.imagePreview = "/img/upload_image.png";
    },

    fileSelected(e) {
      this.$Progress.start();
      // alert("File Selected");
      this.image = e.target.files[0];
      //console.log(this.image);

      // this.image=e.target.files[0];
      let currObj = this;

      // this.product.image=this.image;

      /*
          Initialize a File Reader object
        */
      let reader = new FileReader();

      /*
          Add an event listener to the reader that when the image
          has been loaded, we flag the show preview as true and set the
          image to be what was read from the reader.
        */
      reader.addEventListener("load", function() {
        this.imagePreview = reader.result;
      }.bind(this), false);

      /*
        Check to see if the image is not empty.
      */
      if (this.image) {
        /*
          Ensure the image is an image image.
        */
        if (/\.(jpe?g|png|gif)$/i.test(this.image.name)) {
          /*
            Fire the readAsDataURL method which will read the image in and
            upon completion fire a 'load' event which we will listen to and
            display the image in the preview.
          */
          reader.readAsDataURL(this.image);

          this.$Progress.finish();

        } else {
          this.$Progress.fail();
          this.$toast.error({ title: 'Error!!', message: 'Jpeg,png,gif only supported.' });
        }


      } else {
        this.$Progress.fail();
        this.$toast.error({ title: 'Error!!', message: 'Select a Image file.' });
      }


      this.$Progress.fail();

    }, //end of fileSelected
    formSubmit(e) {
      this.$Progress.start();
      e.preventDefault();
      let currObj = this;

      const config = {
        headers: { 'content-type': 'multipart/form-data' },

      }
      let formData = new FormData();
      formData.append('image', this.image);
      formData.append('_method', 'PUT'); //add this otherwise data won't pass to backend
      formData.append('id', this.store.id);
      formData.append('name', this.store.name);
      formData.append('detail', this.store.detail);
      formData.append('email', this.store.email);
      formData.append('address', this.store.address);
      formData.append('phone', this.store.phone);
      formData.append('mobile', this.store.mobile);
      formData.append('url', this.store.url);
      formData.append('tax_percentage', this.store.tax_percentage);
      formData.append('tax_number', this.store.tax_number);
      formData.append('profit_percentage', this.store.profit_percentage);
      // Display the key/value pairs

      // posting data //using post and sending form data as PUT to match the api route name setting
      axios.post('/api/store', formData, config)
        .then(function(response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          // alert(currObj.status);

          currObj.$swal('Info', currObj.output, currObj.status);
          currObj.$Progress.finish();
          currObj.fetchStore();



        })
        .catch(function(error) {
          if (error.response.status == 422) {
            currObj.validationErrors = error.response.data.errors;
            currObj.errors = currObj.validationErrors;
            // console.log(currObj.errors);
          }
          currObj.$Progress.fail();
        });

    }, //end of formSubmit

    hasPermission(action) {
      let permissions_from_store = this.$store.getters.permissions

      if (permissions_from_store.includes(action) || permissions_from_store.includes('all')) {
        return true
      } else {
        return false
      }

    } //has permision

  }, //end of methods block




}
</script>
