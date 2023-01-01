<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Transactions</h1>

    <button
      @click="showAddModal()"
      class="btn btn-success"
      style="margin-top: 8px"
    >
      Add Transactions
    </button>

    <!-- add transaction model start -->
    <b-modal id="bv-modal-add-transaction" hide-footer size="lg">
      <template v-slot:modal-title>
        <span class="text-primary">{{ modalForName }}</span>
      </template>
      <div class="d-block">
        <div class="form-group">
          <input type="hidden" v-model="transaction.id" />
          <div class="form-group">
            <label for="User Type">Account</label>
            <select class="form-control" v-model="transaction.account_id">
              <option
                selected=""
                v-for="account in accounts"
                :value="account.id"
              >
                {{ account.name }}
              </option>
            </select>
            <span v-if="errors.account_id" :class="['errorText']">{{
              errors.type[0]
            }}</span>
          </div>
          <b-form-group label="Transaction type" v-slot="{ ariaDescribedby }">
            <b-form-radio
              v-model="transaction.transaction_type"
              :aria-describedby="ariaDescribedby"
              name="some-radios"
              value="income"
              >Income</b-form-radio
            >
            <b-form-radio
              v-model="transaction.transaction_type"
              :aria-describedby="ariaDescribedby"
              name="some-radios"
              value="expense"
              >Expense</b-form-radio
            >
          </b-form-group>
          <label for="Amount">Amount:</label>
          <input
            type="text"
            v-model="transaction.amount"
            :class="['form-control']"
          />
          <span v-if="errors.amount" :class="['errorText']">{{
            errors.amount[0]
          }}</span>
        </div>
        <div class="form-group">
          <label for="Date">Date:</label>
          <input
            type="date"
            v-model="transaction.date"
            :class="['form-control']"
          />
          <span v-if="errors.date" :class="['errorText']">{{
            errors.date[0]
          }}</span>
        </div>
        <div class="form-group">
          <label for="Name">TransactionName:</label>
          <input
            type="text"
            v-model="transaction.transaction_name"
            :class="['form-control']"
          />
          <span v-if="errors.date" :class="['errorText']">{{
            errors.date[0]
          }}</span>
        </div>
        <div class="form-group">
          <label for="Phone">Notes:</label>
          <textarea
            v-model="transaction.notes"
            :class="['form-control']"
          ></textarea>
          <span v-if="errors.notes" :class="['errorText']">{{
            errors.notes[0]
          }}</span>
        </div>
        <div class="form-group">
          <label>Reference Image</label>
          <br />
          <img v-bind:src="imagePreview" class="transaction_image_upload" />
          <input
            type="file"
            :class="['form-control']"
            v-on:change="fileSelected"
          />
          <span v-if="errors.image" :class="['errorText']">{{
            errors.image[0]
          }}</span>
        </div>
      </div>
      <b-button class="btn-primary mt-3" block @click="callFunc">{{
        modalForName
      }}</b-button>
    </b-modal>
    <!-- add transaction modal end-->
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6
          class="m-0 font-weight-bold text-primary"
          style="display: inline-block"
        >
          Transactions
        </h6>
        <div class="text-center" v-if="isLoading == 'Loading all Data'">
          <b-spinner variant="success" label="Spinning"></b-spinner>
        </div>
        <!-- <span>{{isLoading}}</span> -->
      </div>
      <div class="card-body" v-if="transactions.length > 0">
        <div class="table">
          <table
            class="table table-striped table-bordered"
            width="100%"
            cellspacing="0"
          >
            <thead>
              <tr>
                <!-- <th>ID</th> -->
                <th>Date</th>
                <th>Transaction Name</th>
                <th>Transaction Type</th>
                <th>Amount</th>
                <th>Notes</th>
                <th>Account</th>
                <th>Image</th>
                <th>Modify</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="transaction in transactions"
                v-bind:key="transaction.id"
              >
                <!-- <td>{{transaction.id}}</td> -->
                <td @click="transactionProfile(transaction.id)" class="cursor">
                  {{ transaction.date }}
                </td>
                <td>{{ transaction.transaction_name }}</td>
                <td>{{ transaction.transaction_type | remove_underscore}}</td>
                <td>{{ transaction.amount }}</td>
                <td>{{ transaction.notes }}</td>
                <td>{{ transaction.account_id }}</td>
                <td>
                  <img :src="'/img/'+transaction.image" width="100" height="100"  @click="$bvModal.show('modal-transaction-image')" />
                </td>
                     
                <td>
                  <button
                    class="btn btn-success custom_btn_table"
                    @click="editTransaction(transaction.id)"
                    v-if="hasPermission('edit_transaction')"
                  >
                    <span class="fa fa-edit custom_icon_table"></span>
                  </button>
                  <button
                    class="btn btn-danger custom_btn_table"
                    @click="deleteTransaction(transaction.id)"
                    v-if="hasPermission('delete_transaction')"
                  >
                    <span class="fa fa-trash custom_icon_table"></span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
           <b-modal id="modal-transaction-image" title="BootstrapVue" hide-footer size="xl" >
                      <template #modal-title>
                        Transaction Reference Image
                      </template>
                      <div style="display:flex; justify-content:center">
                      <b-img fluid class="my-4" :src="'/img/'+transaction.image" style="max-height:700px"/>
                      </div>
          </b-modal>
        </div>
        <div class="row">
          <div class="col-md-8">
            <ul class="pagination">
              <li
                class="page-item"
                v-bind:class="{ disabled: !pagination.first_link }"
              >
                <button
                  @click="fetchTransactions(pagination.first_link)"
                  class="page-link"
                >
                  First
                </button>
              </li>
              <li
                class="page-item"
                v-bind:class="{ disabled: !pagination.prev_link }"
              >
                <button
                  @click="fetchTransactions(pagination.prev_link)"
                  class="page-link"
                >
                  Previous
                </button>
              </li>
              <li
                v-for="n in pagination.last_page"
                v-bind:key="n"
                class="page-item"
                v-bind:class="{ active: pagination.current_page == n }"
              >
                <button
                  @click="fetchTransactions(pagination.path_page + n)"
                  class="page-link"
                >
                  {{ n }}
                </button>
              </li>
              <li
                class="page-item"
                v-bind:class="{ disabled: !pagination.next_link }"
              >
                <button
                  @click="fetchTransactions(pagination.next_link)"
                  class="page-link"
                >
                  Next
                </button>
              </li>
              <li
                class="page-item"
                v-bind:class="{ disabled: !pagination.last_link }"
              >
                <button
                  @click="fetchTransactions(pagination.last_link)"
                  class="page-link"
                >
                  Last
                </button>
              </li>
            </ul>
          </div>
          <div class="col-md-4">
            Page: {{ pagination.current_page }}-{{ pagination.last_page }} Total
            Records: {{ pagination.total_pages }}
          </div>
        </div>
      </div>
      <div class="errorDivEmptyData" v-else>No Data Found</div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      transaction_id: "",
      transaction: {
        date:'',
        transaction_name:'',
        transaction_type:'',
        notes:'',
        refID:'',
        amount:'',
        account_id:'',
        image:'',
        store_id:'',
      },
      transactions: [],
      errors: [],
      modalForName: "",
      modalForCode: 0,
      image: "",
      selectedFile: "",
      isLoading: "",
      imagePreview: "",
      accounts: [],

      pagination: {},
    };
  },
  created() {
    this.setAvtarUploadImage();
    this.getTransactions();
    this.fetchAccounts();
  },

  methods: {
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
        next_link: links.next,
      };
      this.pagination = pagination;
    },
    // showTransactionImageModal(){
    //   this.$bvModal.show('modal-transaction-image')
    // },
    // hideTransactionImageModal(){
    //   this.$bvModal.hide('modal-transaction-image')
    // },
    showAddModal() {
      this.modalForName = "Add Transaction";
      this.modalForCode = 0; //0 for add
      this.transaction.amount = "";
      this.transaction.notes = "";
      this.transaction.transaction_name = "";
      this.transaction.transaction_type = "";
      this.setAvtarUploadImage();
      this.errors = ""; //clearing errors
      this.$bvModal.show("bv-modal-add-transaction");
    },
    callFunc() {
      if (this.modalForCode == 0) {
        this.addTransaction();
      } else if (this.modalForCode == 1) {
        this.updateTransaction();
      }
    },
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
      reader.addEventListener(
        "load",
        function () {
          this.imagePreview = reader.result;
        }.bind(this),
        false
      );

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
          this.$toast.error({
            title: "Error!!",
            message: "Jpeg,png,gif only supported.",
          });
        }
      } else {
        this.$Progress.fail();
        this.$toast.error({
          title: "Error!!",
          message: "Select a Image file.",
        });
      }

      this.$Progress.fail();
    }, //end of fileSelected

    getTransactions() {
      axios
        .get("/api/transactions/")
        .then((response) => {
          // console.log(response.data.data);
          this.transactions = response.data.data;
        })
        .catch((error) => {
          this.$toast.error({
            title: "Error!!",
            message: "Couldn't retrive transactions.",
          });
        });
    },
    addTransaction() {
      let currObj = this;
      let formData = new FormData();
      formData.append("_METHOD", "POST");
      formData.append("amount", this.transaction.amount);
      formData.append("notes", this.transaction.notes);
      formData.append("date", this.transaction.date);
      formData.append("transaction_type", this.transaction.transaction_type);
      formData.append("transaction_name", this.transaction.transaction_name);
      formData.append("account_id", this.transaction.account_id);
      formData.append("image", this.image);

      const config = {
        headers: { "content-type": "multipart/form-data" },
      };
      axios
        .post("/api/transaction/add", formData, config)
        .then(function (response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          // alert(currObj.status);
          currObj.$swal("Info", currObj.output, currObj.status);
          currObj.$Progress.finish();
          currObj.$bvModal.hide("bv-modal-add-transaction");
          currObj.getTransactions();
        })
        .catch(function (error) {
          if (error.response.status == 422) {
            currObj.validationErrors = error.response.data.errors;
            currObj.errors = currObj.validationErrors;
            // console.log(currObj.errors);
          }
          currObj.$Progress.fail();
        });
    },
    fetchAccounts(page_url) {
      this.$Progress.start();
      this.isLoading = "Loading all Data";

      let vm = this; // current pointer instance while going inside the another functional instance
      page_url = page_url || "api/accounts";
      axios
        .get(page_url)
        .then(function (response) {
          vm.accounts = response.data.data;
          if (vm.accounts.length != null) {
            vm.isLoading = "";
            vm.$Progress.finish();
          }
        })
        .catch(function (error) {
          vm.$Progress.fail();
          if (error.response.status == 422) {
            vm.errors = error.response.data.errors;
            // console.log(currObj.errors);
          } else if (error.response.status == 403) {
            vm.$toast.error({ title: "Warning", message: "Access denied." });
            vm.$router.push({ name: "404" });
          } else {
            vm.$toast.error({
              title: "Opps",
              message: error.response.data.errors,
            });
          }
        });
    },
    editTransaction(transaction_id) {
      this.$Progress.start();
      this.modalForName = "Edit Transaction";
      this.modalForCode = 1; // 1 for Edit
      this.$bvModal.show("bv-modal-add-transaction");
      this.errors = ""; //clearing errors
      axios
        .get("/api/transaction/" + transaction_id)
        .then((response) => {
          this.transaction.id = response.data.data[0].id;
          this.transaction.notes = response.data.data[0].notes;
          this.transaction.amount = response.data.data[0].amount;
          this.transaction.oldamount = response.data.data[0].amount;
          this.transaction.transaction_name = response.data.data[0].transaction_name;
          this.transaction.date = response.data.data[0].date;
          this.transaction.transaction_type =response.data.data[0].transaction_type;
          this.transaction.account_id = response.data.data[0].account_id;
          this.imagePreview = "/img/" + response.data.data[0].image;
          this.$Progress.finish();
        })
        .catch((error) => {
          this.$Progress.fail();
          this.$toast.error({
            title: "Error!!",
            message: "Couldn't retrive transaction",
          });
        });
    },
    updateTransaction() {
      let currObj = this;
      let formData = new FormData();
      formData.append("_METHOD", "POST");
      formData.append("transaction_id", this.transaction.id);
      formData.append("amount", this.transaction.amount);
      formData.append("oldamount", this.transaction.oldamount);
      formData.append("notes", this.transaction.notes);
      formData.append("transaction_type", this.transaction.transaction_type);
      formData.append("transaction_name", this.transaction.transaction_name);
      formData.append("account_id", this.transaction.account_id);
      formData.append("date", this.transaction.date);
      formData.append("image", this.image);

      const config = {
        headers: { "content-type": "multipart/form-data" },
      };
      axios
        .post("/api/transaction", formData, config)
        .then(function (response) {
          currObj.output = response.data.msg;
          currObj.status = response.data.status;
          // alert(currObj.status);
          currObj.$swal("Info", currObj.output, currObj.status);
          currObj.$Progress.finish();
          currObj.$bvModal.hide("bv-modal-add-transaction");
          currObj.getSupplierPayments();
          currObj.getSupplierTransactions();
        })
        .catch(function (error) {
          if (error.response.status == 422) {
            currObj.validationErrors = error.response.data.errors;
            currObj.errors = currObj.validationErrors;
            // console.log(currObj.errors);
          }
          currObj.$Progress.fail();
        });
    },
    deleteTransaction(id) {
      this.$Progress.start();
      let currObj = this;
      this.$swal({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        if (result.value) {
          axios
            .delete("/api/transaction/delete/" + id)
            .then(function (response) {
              currObj.output = response.data.msg;
              currObj.status = response.data.status;
              // alert(currObj.status);
              currObj.$swal("Info", currObj.output, currObj.status);
              currObj.$Progress.finish();
              currObj.getTransactions();
            })
            .catch(function (error) {
              currObj.$Progress.fail();

              // currObj.output=error;

              // console.log(currObj.output);
            });
        }
      });
    },

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
    }, //has permision
  },
};
</script>
<style scoped="">
.card-user .avatar {
  width: 200px;
  height: 200px;
  border: 3px solid #ffffff;
  position: relative;
}
.transaction_image_upload {
  width: 285px;
  height: 230px;
  margin: 0 auto;
  display: flex;
}
.custom-link {
  text-decoration: underline;
}
.custom-link:hover {
  color: orange;
  cursor: pointer;
}
</style>
