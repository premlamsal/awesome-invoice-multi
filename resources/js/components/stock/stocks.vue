<template>
    <div>
        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Stock</h1>
        <p class="mb-4">
            <router-link class="btn btn-primary" to="/stockHistory"
                >Stock History</router-link
            >
        </p>
        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6
                    class="m-0 font-weight-bold text-primary"
                    style="display: inline-block;"
                >
                    Stocks
                </h6>
                <div class="text-center" v-if="isLoading == 'Loading all Data'">
                    <b-spinner variant="success" label="Spinning"></b-spinner>
                </div>
                <p>As per {{ new Date().toLocaleString() }}</p>

                <div class="export-block">
                    <!-- <button class="btn btn-warning-success"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button> -->
                </div>
                <!-- <div class="dateSelector" style="float: left;">
                <div class="form-group">
                  <label>Select Date for Stock</label>
                  <date-picker v-model="date" :config="options" :class="['form-control']"></date-picker>
                </div>
              </div> -->
                <!-- <span>{{isLoading}}</span> -->
                <div class="searchTable">
                    <!-- Topbar Search -->
                    <!-- <div class="input-group"> -->
                    <div class="input-group no-border">
                        <input
                            type="text"
                            value=""
                            class="form-control"
                            placeholder="Search..."
                            v-model="searchTableKey"
                            @keyup.enter="searchTableBtn"
                        />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <i
                                    class="nc-icon nc-zoom-split"
                                    @click="searchTableBtn"
                                ></i>
                            </div>
                        </div>
                    </div>
                    <!-- </div> -->
                </div>
            </div>
            <div class="card-body" v-if="stocks.length > 0">
                <div class="table">
                    <table
                        class="table table-striped table-bordered"
                        width="100%"
                        cellspacing="0"
                    >
                        <thead>
                            <tr>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>CP</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Grand Total</th>
                                <th>Rs. {{ grandTotal }}</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr v-for="stock in stocks" v-bind:key="stock.id">
                                <td>{{ stock.product.custom_product_id }}</td>
                                <!-- <td @click="showStock(stock.product_id)">{{stock.product.name}}</td> -->
                                <td>{{ stock.product.name }}</td>
                                <td>{{ stock.quantity }}</td>
                                <td>{{ stock.product.unit.short_name }}</td>
                                <td>Rs. {{ stock.product.cp }}</td>
                                <td>
                                    Rs. {{ stock.quantity * stock.product.cp }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <ul class="pagination">
                            <li
                                class="page-item"
                                v-bind:class="{
                                    disabled: !pagination.first_link
                                }"
                            >
                                <button
                                    @click="fetchStocks(pagination.first_link)"
                                    class="page-link"
                                >
                                    First
                                </button>
                            </li>
                            <li
                                class="page-item"
                                v-bind:class="{
                                    disabled: !pagination.prev_link
                                }"
                            >
                                <button
                                    @click="fetchStocks(pagination.prev_link)"
                                    class="page-link"
                                >
                                    Previous
                                </button>
                            </li>
                            <li
                                v-for="n in pagination.last_page"
                                v-bind:key="n"
                                class="page-item"
                                v-bind:class="{
                                    active: pagination.current_page == n
                                }"
                            >
                                <button
                                    @click="
                                        fetchStocks(pagination.path_page + n)
                                    "
                                    class="page-link"
                                >
                                    {{ n }}
                                </button>
                            </li>
                            <li
                                class="page-item"
                                v-bind:class="{
                                    disabled: !pagination.next_link
                                }"
                            >
                                <button
                                    @click="fetchStocks(pagination.next_link)"
                                    class="page-link"
                                >
                                    Next
                                </button>
                            </li>
                            <li
                                class="page-item"
                                v-bind:class="{
                                    disabled: !pagination.last_link
                                }"
                            >
                                <button
                                    @click="fetchStocks(pagination.last_link)"
                                    class="page-link"
                                >
                                    Last
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        Page: {{ pagination.current_page }}-{{
                            pagination.last_page
                        }}
                        Total Records: {{ pagination.total_pages }}
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
            stocks: [], //contains all the retrived units from the database

            stock: {}, //for form single unit data

            modalForName: "",
            modalForCode: 0,

            searchTableKey: "",
            errors: [],
            pagination: {},

            isLoading: "",

            date: "",

            options: {
                format: "YYYY-MM-DD",
                useCurrent: true,
                showClear: true,
                showClose: true
            } //this variable for the date picker
        };
    },
    created() {
        //this block will execute when component created
        this.fetchStocks();
    },

    methods: {
        //methods codes here
        fetchStocks(page_url) {
            this.$Progress.start();
            this.isLoading = "Loading all Data";
            let vm = this; // current pointer instance while going inside the another functional instance
            page_url = page_url || "api/stocks";
            axios
                .get(page_url)
                .then(function(response) {
                    vm.stocks = response.data.data;
                    if (vm.stocks.length != null) {
                        vm.makePagination(
                            response.data.meta,
                            response.data.links
                        );
                        vm.$Progress.finish();
                    }
                    vm.isLoading = "";
                })
                .catch(function(error) {
                    // console.log();
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
            };
            this.pagination = pagination;
        },

        searchTableBtn() {
            this.autoCompleteTable();
        },
        autoCompleteTable() {
            this.searchTableKey = this.searchTableKey.toLowerCase();
            if (this.searchTableKey != "") {
                this.isLoading = "Loading Data...";
                let currObj = this;
                axios
                    .post("/api/stock_search", {
                        searchQuery: this.searchTableKey
                    })
                    .then(function(response) {
                        currObj.isLoading = "";

                        currObj.stocks = response.data.queryResults;
                        if (response.data.queryResults == "") {
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
                            currObj.validationErrors =
                                error.response.data.errors;
                            currObj.errors = currObj.validationErrors;
                            currObj.isLoading = "Load Failed...";
                            // console.log(currObj.errors);
                        }
                    });
            } else {
                this.isLoading = "Loading all Data";
                this.fetchStocks();
            }
        }, //end of autoCOmpleteTable

        showStock(product_id) {
            // named route
            this.$router.push({ path: `/${product_id}/showStock/` });
        }

        //end of methods block
    },

    computed: {
        grandTotal: function() {
            //reduce function is used to sum the array elements
            this.stocks.grandTotal = this.stocks.reduce(function(carry, stock) {
                return (
                    carry +
                    parseFloat(stock.quantity) * parseFloat(stock.product.cp)
                );
            }, 0);
            return this.stocks.grandTotal;
        }
    }
};
</script>
