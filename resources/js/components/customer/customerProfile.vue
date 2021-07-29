<template>
    <div class="content">
        <div class="row">
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
                        <table
                            class="table"
                            width="100%"
                            cellspacing="0"
                        >
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
                                    <td v-if="transaction.transaction_type==='opening_balance'">
                                    Opening Balance
                                    </td>
                                    <td v-if="transaction.transaction_type==='sales'">
                                    Sales 
                                    </td>
                                     <td v-if="transaction.transaction_type==='payment'">
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
                                    {{transaction.balance}}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                            <tr style="border:top:1px solid #fff;">
                            <td>
                            </td>
                            <td>
                            </td>
                            
                            <td>
                            </td>
                            <td style="font-weight:bold;">
                            Dr.{{totalSales}}
                            </td>
                             <td style="font-weight:bold;">
                            Cr.{{totalPayment}}
                            </td>
                             <td>
                            </td>
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
            transactions: []
        };
    },
    created() {
        this.getIdFromUrl();
        this.getCustomerDetails();
        this.getCustomerTransactions();
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
</style>
