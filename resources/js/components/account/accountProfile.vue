<template>
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3>Account Statment</h3>
          </div>
          <div class="card-body">
            <table class="table" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th scope="col">Date</th>
                  <th scope="col">Transaction</th>
                  <th scope="col">RefID</th>
                  <th scope="col" style="color: red">Dr.</th>
                  <th scope="col" style="color: green">Cr.</th>
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
                  <td v-if="transaction.transaction_type === 'opening_balance'">
                    Opening Balance
                  </td>
                  <td v-if="transaction.transaction_type === 'income'">
                    Income
                  </td>
                  <td v-if="transaction.transaction_type === 'sales_payment'">
                    Sales Payment
                  </td>
                  <td
                    v-if="transaction.transaction_type === 'purchase_payment'"
                  >
                    Purchase Payment
                  </td>

                  <td v-if="transaction.transaction_type === 'expense'">
                    Expense
                  </td>

                  <td>{{ transaction.refID }}</td>

                  <td v-if="transaction.transaction_type === 'expense'">
                    {{ transaction.amount }}
                  </td>
                  <td
                    v-else-if="
                      transaction.transaction_type === 'purchase_payment'
                    "
                  >
                    {{ transaction.amount }}
                  </td>
                  <td v-else></td>
                  <td v-if="transaction.transaction_type === 'income'">
                    {{ transaction.amount }}
                  </td>
                  <td
                    v-else-if="transaction.transaction_type === 'sales_payment'"
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

                  <td style="font-weight: bold">Dr.{{ totalExpense }}</td>
                  <td style="font-weight: bold">Cr.{{ totalIncome }}</td>
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
      account_id: "",
      account: {},
      transactions: [],
      payment: {},
      payments: [],
      errors: [],
      modalForName: "",
      modalForCode: 0,
      image: "",
      selectedFile: "",
      isLoading: "",
      imagePreview: "",
    };
  },
  created() {
    console.log("all ok");
    this.getIdFromUrl();
    this.getAccountDetails();
    this.getAccountTransactions();
  },
  computed: {
    totalExpense: function () {
      let totalExpense;
      //reduce function is used to sum the array elements
      totalExpense = this.transactions.reduce((carry, transaction) => {
        if (
          transaction.transaction_type === "expense" ||
          transaction.transaction_type == "purchase_payment"
        ) {
          return carry + parseFloat(transaction.amount);
        } else {
          return carry + 0;
        }
      }, 0);
      return totalExpense;
    },
    totalIncome: function () {
      let totalIncome;
      //reduce function is used to sum the array elements
      totalIncome = this.transactions.reduce((carry, transaction) => {
        if (
          transaction.transaction_type === "income" ||
          transaction.transaction_type === "sales_payment"
        ) {
          return carry + parseFloat(transaction.amount);
        } else {
          return carry + 0;
        }
      }, 0);
      return totalIncome;
    },
  },
  methods: {
    getIdFromUrl() {
      this.account_id = this.$route.params.id;
    }, //end of getIdFromUrl
    getAccountTransactions() {
      axios
        .get("/api/account/transactions/" + this.account_id)
        .then((response) => {
          this.transactions = response.data.transactions;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getAccountDetails() {
      axios
        .get("/api/account/" + this.account_id)
        .then((response) => {
          this.account = response.data.account;
          this.account.invoice_amount = response.data.invoice_amount;
          this.account.paid_amount = response.data.paid_amount;
          this.account.balance_due = response.data.balance_due;
        })
        .catch((error) => {
          console.log(error);
        });
    },
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
.payment_image_upload {
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
