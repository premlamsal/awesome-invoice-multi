<template>
  <div>
    <h1>Welcome to returns</h1>
    <p>Please select the return first</p>
    <button class="btn btn-primary" @click="returnPurchases()">
      Return Purchases
    </button>
    <button class="btn btn-primary" @click="returnInvoices()">
      Return Invoices
    </button>
  </div>
</template>

<script>
export default {
  data() {
    return {};
  },
  created() {},

  methods: {
    returnPurchases() {
      this.$router.push({name:"returnPurchases"});
    },
    returnInvoices() {
      this.$router.push({name:"returnInvoices"});

    },
  },
};
</script>
