<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Select Store</h1>
    <p class="mb-4">
    </p>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary" style="display: inline-block;">Stores</h6>
        <div v-if="isLoading">{{isLoading}}</div>
        <!-- {{isLoading}} -->
      </div>
      <div class="card-body">
        <div class="form-group row">
          <label for="name" class="col-md-4 col-form-label text-md-right">Store Name</label>
          <div class="col-md-6">
            <select v-model="store_id">
              <option selected="" v-for="store in stores" :value="store.id">{{store.name}}</option>
            </select>
          </div>
        </div>
        <div class="form-group row mb-0">
          <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary" @click="setStore()">
              Ok
            </button>
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

      isLoading: '',

      stores: [],

      store_id: '',

    };

  },

  created() {

    this.fetchStores();

    this.store_id = this.$cookie.get('store_id');
  },

  methods: {


    fetchStores() {

      let currObj = this;

      axios.get('/api/stores')
        .then(function(response) {

          currObj.stores = response.data.stores
        })
        .catch(function(error) {
          console.log(error);
        })

    },
    setStore() {

      this.$cookie.set('store_id', this.store_id, 1);

      // go back by one record, the same as history.back()
      this.$router.go(-1)

      // currObj.$router.push({ name: 'invoices'});

      //  // To get the value of a cookie use
      // this.$cookie.get('test');

      // // To delete a cookie use
      // this.$cookie.delete('test');
    },


  } //end of methods



}; //end of default
</script>
