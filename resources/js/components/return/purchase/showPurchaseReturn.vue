<template>
<div>
      <button class="btn btn-primary" style="float: right;" @click="printPurchase(info.return_purchase_no)"><span class="fa fa-print"></span></button>
<div class="container" id="printThisBlock">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="row p-5">
                        <div class="col-md-6">
                            <img class="comapny_logo_purchase" v-bind:src="store.store_logo" alt="brand-logo-for-purchase" height="100px" width="100px">
                            <h3 style="margin: 0px">{{store.name}}</h3>
                            
                            <p >{{store.address}}</p>

                            <p >{{store.detail}}</p>

                            <p >{{store.tax_number}}</p>
                           
                        </div>

                        <div class="col-md-6 text-right">
                            <p class="font-weight-bold mb-1">Return Purchase # {{info.return_custom_purchase_id}}</p>
                            <p class="text-muted">Return Purchase on: {{info.return_purchase_date}}</p>
                            <p class="text-muted">Due to: {{info.due_date}}</p>

                            <p class="mb-1"><span class="text-muted">Status: </span> {{info.status}}</p>
                            
                        </div>
                    </div>

                    <hr class="my-2">

                    <div class="row pb-5 p-5">
                        <div class="col-md-6">
                            <p class="font-weight-bold mb-4">Suppliers Information</p>
                            <p class="mb-1">{{info.supplier_name}}</p>
                            <p class="mb-1">{{info.supplier_address}}</p>
                            <p class="mb-1">{{info.supplier_phone}}</p>
                        </div>

                        <div class="col-md-6 text-right">

                             <p class="font-weight-bold mb-4">Suppliers Details</p>
                            <p class="mb-1"><span class="text-muted">{{info.supplier_details}}</span> </p>

                        </div>
                    </div>

                    <div class="row p-5">
                        <div class="col-md-12">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="border-0 text-uppercase small font-weight-bold">ID</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">Item</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">Quantity</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">Unit Cost</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <tr v-for="(item,index) in items">
                                     <td>{{item.product.custom_product_id}}</td>
                                    <td class="table-name">
                                        {{item.product_name}}
                                       
                                    </td>
                                    <td class="table-qty">
                                        {{item.quantity}}

                                         {{item.product.unit.short_name}}
                                    </td>
                                    <td class="table-price">
                                        {{item.price}}
                                    </td>
                                    <td class="table-total">
                                        <span class="table-text" v-model="item.line_total">{{item.quantity * item.price}}</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <hr>
                        <p><b>Amount in Words : </b>{{info.grand_total_words}}</p>
                        </div>
                    </div>
                    <div class="d-flex flex-row-reverse bg-danger text-white p-4">
                        
                        <div class="py-3 px-5 text-right">
                            <div class="mb-2">Grand Total</div>
                            <div class="h3 font-weight-light">Rs. {{info.grand_total}}</div>
                        </div>

                        <div class="py-3 px-5 text-right">
                            <div class="mb-2">Discount</div>
                            <div class="h3 font-weight-light">Rs. {{info.discount}}</div>
                        </div>

                        <div class="py-3 px-5 text-right">
                            <div class="mb-2">TAX</div>
                            <div class="h3 font-weight-light">Rs. {{info.taxAmount}}</div>
                        </div> 
                        <div class="py-3 px-5 text-right">
                            <div class="mb-2">Sub - Total amount</div>
                            <div class="h2 font-weight-light">Rs. {{info.sub_total}}</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 <button class="btn btn-success" @click="editReturnPurchase(id)">Edit</button>
<router-link to="/returnPurchases" class="btn btn-danger">Close</router-link>

</div>
</template>



<script>

    
   export default{

        data(){

            return{
                 items:[{

                    product_name:'',
                    price:'',
                    quantity:'',
                    
                    product:{
                      custom_product_id:'',
                      unit:{},
                    },


                    line_total:''

                }],

                info:{},
                id:'',

                store:{},
               
               

            };

        },
        created(){
           
            //methods to be executed while this page is created
            this.getIdFromUrl();
            this.fetchReturnPurchase();
            this.fetchStore();
            // this.$root.test(); calling root function in app.js

        },
        mounted(){
           this.store.store_logo="/img/a.svg";
        },
        methods :{

            getIdFromUrl(){

            this.id=this.$route.params.id;

        },//end of getIdFromUrl
           editReturnPurchase(id){
          // named route
           this.$router.push({ path: `/${id}/editReturnPurchase/` }) 
        },

        fetchReturnPurchase(){

            let currObj=this;

          axios.get('/api/return_purchase/'+this.id)
            .then(function(response){

                Vue.set(currObj.info, 'return_purchase_no', response.data.return_purchase.id),
                Vue.set(currObj.info, 'return_custom_purchase_id', response.data.return_purchase.custom_return_purchase_id),
                Vue.set(currObj.info, 'title', response.data.return_purchase.title),
                Vue.set(currObj.info, 'customer_id', response.data.return_purchase.customer_id),
                Vue.set(currObj.info, 'customer_name', response.data.return_purchase.customer_name),
                 
                Vue.set(currObj.info, 'return_purchase_date', response.data.return_purchase.return_purchase_date),
                Vue.set(currObj.info, 'due_date', response.data.return_purchase.due_date),
                Vue.set(currObj.info, 'discount', response.data.return_purchase.discount),
                Vue.set(currObj.info, 'sub_total', response.data.return_purchase.sub_total),
                Vue.set(currObj.info, 'taxAmount', response.data.return_purchase.tax_amount),
                Vue.set(currObj.info, 'grand_total', response.data.return_purchase.grand_total),
                Vue.set(currObj.info, 'supplier_address', response.data.return_purchase.supplier.address),
                Vue.set(currObj.info, 'supplier_phone', response.data.return_purchase.supplier.phone),
                Vue.set(currObj.info, 'supplier_details', response.data.return_purchase.supplier.details),
                Vue.set(currObj.info, 'status', response.data.return_purchase.status),
                   
                //veu.set will make data reactive and updated
                currObj.items=response.data.return_purchase.return_purchase_detail,
                // console.log(response.data.return_purchase)


                //converting number to words
                currObj.convertToWords()
            })
            .catch(function(error){
               
                  if (error.response.status == 404){
                    currObj.$router.push({ name: '404'});    
                    }
               
            });
           
                    
        },//end of fetchPurchase
        printPurchase(id){

              // this.$router.push({ path: `/${id}/printPurchase/` })

              // let routeData = this.$router.resolve({name: 'printPurchase', query: {data: "someData"}});
              // window.open(routeData.href, '_blank');

              this.$htmlToPaper('printThisBlock');


        },
    fetchStore(){

        let currObj=this;
        
        axios.get('/api/store')
        .then(function(response){

            Vue.set(currObj.store, 'id', response.data.store.id)
            Vue.set(currObj.store, 'name', response.data.store.name)
            Vue.set(currObj.store, 'detail', response.data.store.detail)
            Vue.set(currObj.store, 'email', response.data.store.email)
            Vue.set(currObj.store, 'address', response.data.store.address)
            Vue.set(currObj.store, 'phone', response.data.store.phone)
            Vue.set(currObj.store, 'mobile', response.data.store.phone)
            Vue.set(currObj.store, 'url', response.data.store.url)


            if(response.data.store.store_logo!=null){
            //company image
            Vue.set(currObj.store, 'store_logo',"/img/"+ response.data.store.store_logo)
            // currObj.store_logo="/img/"+data.store.store_logo //concatenate image location and image name
             }

            Vue.set(currObj.store, 'tax_number', response.data.store.tax_number)
            Vue.set(currObj.store, 'tax_percentage', response.data.store.tax_percentage)
            // console.log(data.store.name)
        })
        .catch(function(error){

            console.log(error);

        });


      },//end of fetchStore()

       convertToWords(){

                this.info.grand_total_words=this.convertNumberToWords(this.info.grand_total);
        },


    convertNumberToWords(amount){
           var words = new Array();
    words[0] = '';
    words[1] = 'One';
    words[2] = 'Two';
    words[3] = 'Three';
    words[4] = 'Four';
    words[5] = 'Five';
    words[6] = 'Six';
    words[7] = 'Seven';
    words[8] = 'Eight';
    words[9] = 'Nine';
    words[10] = 'Ten';
    words[11] = 'Eleven';
    words[12] = 'Twelve';
    words[13] = 'Thirteen';
    words[14] = 'Fourteen';
    words[15] = 'Fifteen';
    words[16] = 'Sixteen';
    words[17] = 'Seventeen';
    words[18] = 'Eighteen';
    words[19] = 'Nineteen';
    words[20] = 'Twenty';
    words[30] = 'Thirty';
    words[40] = 'Forty';
    words[50] = 'Fifty';
    words[60] = 'Sixty';
    words[70] = 'Seventy';
    words[80] = 'Eighty';
    words[90] = 'Ninety';
    amount = amount.toString();
    var atemp = amount.split(".");
    var number = atemp[0].split(",").join("");
    var n_length = number.length;
    var words_string = "";
    if (n_length <= 9) {
        var n_array = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0);
        var received_n_array = new Array();
        for (var i = 0; i < n_length; i++) {
            received_n_array[i] = number.substr(i, 1);
        }
        for (var i = 9 - n_length, j = 0; i < 9; i++, j++) {
            n_array[i] = received_n_array[j];
        }
        for (var i = 0, j = 1; i < 9; i++, j++) {
            if (i == 0 || i == 2 || i == 4 || i == 7) {
                if (n_array[i] == 1) {
                    n_array[j] = 10 + parseInt(n_array[j]);
                    n_array[i] = 0;
                }
            }
        }
        var value = "";
        for (var i = 0; i < 9; i++) {
            if (i == 0 || i == 2 || i == 4 || i == 7) {
                value = n_array[i] * 10;
            } else {
                value = n_array[i];
            }
            if (value != 0) {
                words_string += words[value] + " ";
            }
            if ((i == 1 && value != 0) || (i == 0 && value != 0 && n_array[i + 1] == 0)) {
                words_string += "Crores ";
            }
            if ((i == 3 && value != 0) || (i == 2 && value != 0 && n_array[i + 1] == 0)) {
                words_string += "Lakhs ";
            }
            if ((i == 5 && value != 0) || (i == 4 && value != 0 && n_array[i + 1] == 0)) {
                words_string += "Thousand ";
            }
            if (i == 6 && value != 0 && (n_array[i + 1] != 0 && n_array[i + 2] != 0)) {
                words_string += "Hundred and ";
            } else if (i == 6 && value != 0) {
                words_string += "Hundred ";
            }
        }
        words_string = words_string.split("  ").join(" ");
    }
    return words_string;

      },//end of convertNumberToWords


    },// end of methods

    computed:{

      
          

    },//end of computed

};//end of export default





</script>
 <style>
.black{
    color: black;
    }
label{
     color: #000;
        font-weight: bold;
}
.company-Block{
/*border: 1px solid white;*/
margin-bottom: 10px;
text-align: center;
}
img.comapny_logo_purchase{
    width: 134px;
    height: 134px;
    border-radius: 50%;
}



 </style>
