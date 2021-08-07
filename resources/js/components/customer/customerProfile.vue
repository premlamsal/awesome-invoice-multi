<template>
    <div class="content">
        <div class="row">
            <div class="col-md-6">
                <button
                    class="btn btn-primary"
                    style="margin-left:10px"
                    @click="showAddModal"
                >
                    Add Payment
                </button>

                <!-- add payment model start -->
                <b-modal id="bv-modal-add-payment" hide-footer>
                    <template v-slot:modal-title>
                        <span class="text-primary">{{ modalForName }}</span>
                    </template>
                    <div class="d-block">
                        <div class="form-group">
                            <input type="hidden" v-model="payment.id" />
                            <label for="Amount">Amount:</label>
                            <input
                                type="text"
                                v-model="payment.amount"
                                :class="['form-control']"
                            />
                            <span v-if="errors.amount" :class="['errorText']">{{
                                errors.amount[0]
                            }}</span>
                        </div>
                        <div class="form-group">
                            <label for="Phone">Notes:</label>
                            <textarea
                                v-model="payment.notes"
                                :class="['form-control']"
                            ></textarea>
                            <span v-if="errors.notes" :class="['errorText']">{{
                                errors.notes[0]
                            }}</span>
                        </div>
                        <div class="form-group">
                            <label>Reference Image</label>
                            <br />
                            <img
                                v-bind:src="imagePreview"
                                class="payment_image_upload"
                            />
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
                    <b-button
                        class="btn-primary mt-3"
                        block
                        @click="callFunc"
                        >{{ modalForName }}</b-button
                    >
                </b-modal>
                <!-- add payment modal end-->

                <div class="card">
                    <h4 style="margin-left:10px;margin-top:10px;">Payments</h4>
                    <div class="card-body" style="overflow:scroll;height:450px">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Modify</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="payment in payments" v-bind:key="payment.id">
                                    <td>{{payment.created_at}}</td>
                                    <td>Rs. {{payment.amount}}</td>
                                    <td>View/ Edit/ Delete</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-user">
                    <div class="image bg-danger" style="width: 100%">
                        <img src="img/cover.jpg" alt="..." />
                    </div>
                    <div class="card-body">
                        <div class="author">
                            <a href="#">
                                <img
                                    class="avatar border-gray"
                                    src="img/1579190640.jpg"
                                    alt="..."
                                />
                                <h5 class="title text-danger">
                                    {{ customer.name }}
                                </h5>
                            </a>
                        </div>
                        <p class="text-center text-dark">
                            {{ customer.details }} <br />
                            {{ customer.address }}<br />
                            {{ customer.phone }} <br />
                        </p>
                    </div>
                    <div class="card-footer">
                        <hr />
                        <div class="button-container">
                            <div class="row">
                                <div class="col-lg-3 col-md-6 col-6 ml-auto">
                                    <h5 class="text-warning">
                                        Rs. 20000<br /><small
                                            >Total Sales</small
                                        >
                                    </h5>
                                </div>
                                <div
                                    class="col-lg-4 col-md-6 col-6 ml-auto mr-auto"
                                >
                                    <h5 class="text-success">
                                        Rs. 2000<br /><small>Paid</small>
                                    </h5>
                                </div>
                                <div class="col-lg-3 mr-auto">
                                    <h5 class="text-danger">
                                        Rs.890000<br /><small
                                            >Due Balance</small
                                        >
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Customer Statment</h3>
                    </div>
                    <div class="card-body">
                        <table class="table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Transaction</th>
                                    <th scope="col">RefID</th>
                                    <th scope="col">Dr.</th>
                                    <th scope="col">Cr.</th>
                                    <th scope="col">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="transaction in transactions"
                                    v:bind:key="transaction.id"
                                >
                                    <th scope="row">
                                        {{ transaction.created_at }}
                                    </th>
                                    <td
                                        v-if="
                                            transaction.transaction_type ===
                                                'opening_balance'
                                        "
                                    >
                                        Opening Balance
                                    </td>
                                    <td
                                        v-if="
                                            transaction.transaction_type ===
                                                'sales'
                                        "
                                    >
                                        Sales
                                    </td>
                                    <td
                                        v-if="
                                            transaction.transaction_type ===
                                                'payment'
                                        "
                                    >
                                        Payment
                                    </td>
                                    <td>{{ transaction.refID }}</td>
                                    <td
                                        v-if="
                                            transaction.transaction_type ===
                                                'sales'
                                        "
                                    >
                                        {{ transaction.amount }}
                                    </td>
                                    <td v-else></td>
                                    <td
                                        v-if="
                                            transaction.transaction_type ===
                                                'payment'
                                        "
                                    >
                                        {{ transaction.amount }}
                                    </td>
                                    <td v-else></td>
                                    <td>
                                        {{ transaction.balance }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr style="border:top:1px solid #fff;">
                                    <td></td>
                                    <td></td>

                                    <td></td>
                                    <td style="font-weight:bold;">
                                        Dr.{{ totalSales }}
                                    </td>
                                    <td style="font-weight:bold;">
                                        Cr.{{ totalPayment }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
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
            customer_id: "",
            customer: {},
            transactions: [],
            payment: {},
            payments: [],
            errors: [],
            modalForName: "",
            modalForCode: 0,
            image: "",
            selectedFile: "",
            isLoading: "",
            imagePreview: ""
        };
    },
    created() {
        this.getIdFromUrl();
        this.getCustomerDetails();
        this.getCustomerTransactions();
        this.getCustomerPayments();
        //will set image to payment
        this.setAvtarUploadImage();
    },
    computed: {
        totalSales: function() {
            let totalSales;
            //reduce function is used to sum the array elements
            totalSales = this.transactions.reduce((carry, transaction) => {
                if (transaction.transaction_type === "sales") {
                    return carry + parseFloat(transaction.amount);
                } else {
                    return carry + 0;
                }
            }, 0);
            return totalSales;
        },
        totalPayment: function() {
            let totalPayment;
            //reduce function is used to sum the array elements
            totalPayment = this.transactions.reduce((carry, transaction) => {
                if (transaction.transaction_type === "payment") {
                    return carry + parseFloat(transaction.amount);
                } else {
                    return carry + 0;
                }
            }, 0);
            return totalPayment;
        }
    },
    methods: {
        showAddModal() {
            this.modalForName = "Add Payment";
            this.modalForCode = 0; //0 for add
            this.payment.amount = "";
            this.payment.notes = "";
            this.errors = ""; //clearing errors
            this.$bvModal.show("bv-modal-add-payment");
        },
        callFunc() {
            if (this.modalForCode == 0) {
                this.addPayment();
            } else if (this.modalForCode == 1) {
                this.updatePayment();
            }
        },
        getCustomerPayments(){
            axios.get('/api/customer/payments/'+this.customer_id)
            .then((response)=>{
                // console.log(response.data.data);
                this.payments=response.data.data;
            })
            .catch((error)=>{
                 this.$toast.error({
                    title: "Error!!",
                    message: "Couldn't retrive payments."
                });
            })
        },
        addPayment() {
            let currObj = this;
            let formData = new FormData();
            formData.append("_METHOD", "POST");
            formData.append("customer_id", this.customer_id);
            formData.append("amount", this.payment.amount);
            formData.append("notes", this.payment.notes);
            formData.append("image", this.image);

            const config = {
                headers: { "content-type": "multipart/form-data" }
            };
            axios
                .post("/api/customer/add-payment", formData, config)
                .then(function(response) {
                    currObj.output = response.data.msg;
                    currObj.status = response.data.status;
                    // alert(currObj.status);
                    currObj.$swal("Info", currObj.output, currObj.status);
                    currObj.$Progress.finish();
                    currObj.$bvModal.hide("bv-modal-add-payment");
                })
                .catch(function(error) {
                    if (error.response.status == 422) {
                        currObj.validationErrors = error.response.data.errors;
                        currObj.errors = currObj.validationErrors;
                        // console.log(currObj.errors);
                    }
                    currObj.$Progress.fail();
                });
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
                function() {
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
                        message: "Jpeg,png,gif only supported."
                    });
                }
            } else {
                this.$Progress.fail();
                this.$toast.error({
                    title: "Error!!",
                    message: "Select a Image file."
                });
            }

            this.$Progress.fail();
        }, //end of fileSelected

        getIdFromUrl() {
            this.customer_id = this.$route.params.id;
        }, //end of getIdFromUrl
        getCustomerTransactions() {
            axios
                .get("/api/customer/transactions/" + this.customer_id)
                .then(response => {
                    this.transactions = response.data.transactions;
                })
                .catch(error => {
                    console.log(error);
                });
        },
        getCustomerDetails() {
            axios
                .get("/api/customer/" + this.customer_id)
                .then(response => {
                    this.customer = response.data.customer;
                })
                .catch(error => {
                    console.log(error);
                });
        }
    }
};
</script>
<style scoped="">
.card-user .avatar {
    width: 200px;
    height: 200px;
    border: 3px solid #ffffff;
    position: relative;
}
.payment_image_upload{
    width: 285px;
    height: 230px;
    margin: 0 auto;
    display: flex;
    }
</style>
