/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import BootstrapVue from 'bootstrap-vue';//for modal

Vue.use(BootstrapVue);//for modal



import Vue from 'vue'


import store from './store'

import VueRouter from 'vue-router'



// for sweet alert
import VueSweetalert2 from 'vue-sweetalert2';

// If you don't need the styles, do not connect
import 'sweetalert2/dist/sweetalert2.min.css';

//bootstrap date picker
import datePicker from 'vue-bootstrap-datetimepicker';
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';

//bootstrap select
import VSelect from '@alfsnd/vue-bootstrap-select';

//chart
import { Bar, Line } from 'vue-chartjs';




//notification block//
//Toaster
import CxltToastr from 'cxlt-vue2-toastr';

import 'cxlt-vue2-toastr/dist/css/cxlt-vue2-toastr.css'

var toastrConfigs = {
  position: 'top right',
  showDuration: 1000,
  timeOut: 4000

}
//toastr


//vue-progressbar
import VueProgressBar from 'vue-progressbar'

const progressBarOptions = {
  color: '#bffaf3',
  failedColor: '#874b4b',
  thickness: '2px',
  transition: {
    speed: '0.2s',
    opacity: '0.6s',
    termination: 300
  },
  autoRevert: true,
  location: 'left',
  inverse: false
}
//vue-progressbar

//print div to paper

import VueHtmlToPaper from 'vue-html-to-paper';

const options = {
  name: '_blank',
  specs: [
    'fullscreen=yes',
    'titlebar=yes',
    'scrollbars=yes'
  ],
  styles: [
    'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css',
    'https://unpkg.com/kidlat-css/css/kidlat.css'
  ]
}

Vue.use(VueHtmlToPaper, options);

//end of print div to paper

//export json to csv
import VueBlobJsonCsv from 'vue-blob-json-csv';

Vue.use(VueBlobJsonCsv);


//custom filter to filter underscore from value
Vue.filter('remove_underscore', function (value) {
  return value.replace(/_/g," ");
})









//notification block ends//


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))




let routes = [

  {
    path: '/',
    name:'/',
    component: require('./components/dashboard/dashboard.vue').default,
    meta:{requireAuth:false}

  },

  {
    path: '/dashboard',
    name:'dashboard',
    component: require('./components/dashboard/dashboard.vue').default,

  },
  //invoice routes
  {
    path: '/invoices',
    name: 'invoices',
    component: require('./components/invoice/invoices.vue').default,

     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('view_invoices') || hasAccess.includes('all')) {
          next()
        }
      }

  },

  {
    path: '/newInvoice',
    component: require('./components/invoice/newInvoice.vue').default,

     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('add_invoices') || hasAccess.includes('all')) {
          next()
        }
      }
  },

  {
    path: '/:id/editInvoice',
    name: 'editInvoice',
    component: require('./components/invoice/editInvoice.vue').default,

     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('edit_invoice') || hasAccess.includes('all')) {
          next()
        }
      }
  },

  {
    path: '/:id/returnInvoice',
    name: 'returnInvoice',
    component: require('./components/invoice/returnInvoice.vue').default,

    
     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('return_invoice') || hasAccess.includes('all')) {
          next()
        }
      }
  },

  {
    path: '/:id/showInvoice/',
    name: 'showInvoice',
    component: require('./components/invoice/showInvoice.vue').default,
   
     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('show_invoice') || hasAccess.includes('all')) {
          next()
        }
      }
  },

  {
    path: '/:id/printInvoice/',
    name: 'printInvoice',
    component: require('./components/invoice/printInvoice.vue').default

  },

  //estimate routes
  {
    path: '/estimates',
    name: 'estimates',
    component: require('./components/estimate/estimates.vue').default,
   
     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('view_estimates') || hasAccess.includes('all')) {
          next()
        }
      }
  },

  {
    path: '/newEstimate',
    component: require('./components/estimate/newEstimate.vue').default,

     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('add_estimate') || hasAccess.includes('all')) {
          next()
        }
      }
  },

  {
    path: '/:id/editEstimate',
    name: 'editEstimate',
    component: require('./components/estimate/editEstimate.vue').default,
  
     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('edit_estimate') || hasAccess.includes('all')) {
          next()
        }
      }
  },
  {
    path: '/:id/showEstimate/',
    name: 'showEstimate',
    component: require('./components/estimate/showEstimate.vue').default
  },
  {
    path: '/:id/printEstimate/',
    name: 'printEstimate',
    component: require('./components/estimate/printEstimate.vue').default
  },

  //purchase routes
  {
    path: '/purchases',
    name: 'purchases',
    component: require('./components/purchase/purchases.vue').default,
   
     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('view_purchases') || hasAccess.includes('all')) {
          next()
        }
      }
  },

  {
    path: '/newPurchase',
    component: require('./components/purchase/newPurchase.vue').default,
   
     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('add_purchase') || hasAccess.includes('all')) {
          next()
        }
      }
  },

  {
    path: '/:id/editPurchase',
    name: 'editPurchase',
    component: require('./components/purchase/editPurchase.vue').default,

     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('edit_purchase') || hasAccess.includes('all')) {
          next()
        }
      }
  },

  {
    path: '/:id/returnPurchase',
    name: 'returnPurchase',
    component: require('./components/purchase/returnPurchase.vue').default,

     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('return_purchase') || hasAccess.includes('all')) {
          next()
        }
      }
  },

  {
    path: '/:id/showPurchase/',
    name: 'showPurchase',
    component: require('./components/purchase/showPurchase.vue').default
  },

  {
    path: '/:id/printPurchase/',
    name: 'printPurchase',
    component: require('./components/purchase/printPurchase.vue').default
  },


  //customer routes
  {
    path: '/customers',
    name: 'customers',
    component: require('./components/customer/customers.vue').default,

     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('view_customers') || hasAccess.includes('all')) {
          next()
        }
      }
  },

//customer profile routes
  {
    path: '/:id/customer-profile',
    name: 'customerProfile',
    component: require('./components/customer/customerProfile.vue').default,

     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('view_customer_profile') || hasAccess.includes('all')) {
          next()
        }
      }
  },


  //supplier routes
  {
    path: '/suppliers',
    name: 'suppliers',
    component: require('./components/supplier/suppliers.vue').default,
    
     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('view_suppliers') || hasAccess.includes('all')) {
          next()
        }
      }

  },
  
//supplier profile routes
{
  path: '/:id/supplier-profile',
  name: 'supplierProfile',
  component: require('./components/supplier/supplierProfile.vue').default,

   beforeEnter(to, from, next) {
      let hasAccess = store.getters.permissions
      if (hasAccess.includes('view_supplier_profile') || hasAccess.includes('all')) {
        next()
      }
    }
},


  //units route
  {
    path: '/units/',
    name: 'units', component: require('./components/unit/units.vue').default,
   
     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('view_units') || hasAccess.includes('all')) {
          next()
        }
      }
  },

  //categories route
  {
    path: '/categories/',
    name: 'categories', component: require('./components/category/categories.vue').default,

     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('view_categories') || hasAccess.includes('all')) {
          next()
        }
      }
  },

  //settings route
  {
    path: '/settings/',
    name: 'settings',
    component: require('./components/setting/settings.vue').default,

     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('view_settings') || hasAccess.includes('all')) {
          next()
        }
      }
  },
  //stocks route
  {
    path: '/stocks/',
    name: 'stocks',
    component: require('./components/stock/stocks.vue').default,
    
     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('view_stocks') || hasAccess.includes('all')) {
          next()
        }
      }
  },
  {
    path: '/:id/showStock/',
    name: 'showStock',
    component: require('./components/stock/showStock.vue').default
  },

  {
    path: '/stockHistory/',
    name: 'stockHistory',
    component: require('./components/stock/stockHistory.vue').default,

  
     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('view_stock_history') || hasAccess.includes('all')) {
          next()
        }
      }
  },


  //products route
  {
    path: '/products/',
    name: 'products',
    component: require('./components/product/products.vue').default,

   
     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('view_products') || hasAccess.includes('all')) {
          next()
        }
      }
  },
  //showProduct route

  // showProductDetail route
  {
    path: '/:id/showProductDetail/',
    name: 'showProductDetail',
    component: require('./components/product/showProductDetail.vue').default,

  },
  //users

  {
    path: '/users/',
    name: 'users',
    component: require('./components/user/users.vue').default,
    
     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('view_users') || hasAccess.includes('all')) {
          next()
        }
      }
  },
  //roles
  {
    path: '/roles/',
    name: 'roles',
    component: require('./components/role/roles.vue').default,

     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('view_roles') || hasAccess.includes('all')) {
          next()
        }
      }
  },
  //permissions
  {
    path: '/permissions/',
    name: 'permissions',
    component: require('./components/permission/permissions.vue').default,

     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('view_permissions') || hasAccess.includes('all')) {
          next()
        }
      }
  },
  //chart for testing

  {
    path: '/chart/',
    name: 'chart',
    component: require('./components/chart/chartContainer.vue').default,
    
  },


  {
    path: '/select/store',
    name: 'selectStore',
    component: require('./components/dashboard/selectStore.vue').default,

  },
  {
    path: '/accounts',
    name: 'accounts',
    component: require('./components/account/accounts.vue').default,
    beforeEnter(to, from, next) {
      let hasAccess = store.getters.permissions
      if (hasAccess.includes('view_accounts') || hasAccess.includes('all')) {
        next()
      }
    }

  },
  {
    path: '/transactions',
    name: 'transactions',
    component: require('./components/transaction/transactions.vue').default,
    beforeEnter(to, from, next) {
      let hasAccess = store.getters.permissions
      if (hasAccess.includes('view_transactions') || hasAccess.includes('all')) {
        next()
      }
    }

  },

  {
    path: '/returns',
    name: 'returns',
    component: require('./components/return/returns.vue').default,
    beforeEnter(to, from, next) {
      let hasAccess = store.getters.permissions
      if (hasAccess.includes('view_returns') || hasAccess.includes('all')) {
        next()
      }
    }

  },//returns
  {
    path: '/returnPurchases',
    name: 'returnPurchases',
    component: require('./components/return/purchase/returnPurchases.vue').default,
    beforeEnter(to, from, next) {
      let hasAccess = store.getters.permissions
      if (hasAccess.includes('view_return_purchases') || hasAccess.includes('all')) {
        next()
      }
    }
  },//returnPurchases

  {
    path: '/newPurchaseReturn',
    name: 'newPurchaseReturn',
    component: require('./components/return/purchase/newPurchaseReturn.vue').default,
    beforeEnter(to, from, next) {
      let hasAccess = store.getters.permissions
      if (hasAccess.includes('view_new_purchase_return') || hasAccess.includes('all')) {
        next()
      }
    }

  },//newPurchaseReturn
  {
    path: '/:id/showReturnPurchase/',
    name: 'showReturnPurchase',
    component: require('./components/return/purchase/showPurchaseReturn.vue').default,
   
     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('show_purchase_return') || hasAccess.includes('all')) {
          next()
        }
      }
  },
  {
    path: '/:id/editReturnPurchase',
    name: 'editReturnPurchase',
    component: require('./components/return/purchase/editPurchaseReturn.vue').default,

     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('edit_purchase_return') || hasAccess.includes('all')) {
          next()
        }
      }
  },



  {
    path: '/returnInvoices',
    name: 'returnInvoices',
    component: require('./components/return/invoice/returnInvoices.vue').default,
    beforeEnter(to, from, next) {
      let hasAccess = store.getters.permissions
      if (hasAccess.includes('view_return_invoices') || hasAccess.includes('all')) {
        next()
      }
    }

  },//returnInvoices
  
  {
    path: '/newInvoiceReturn',
    name: 'newInvoiceReturn',
    component: require('./components/return/invoice/newInvoiceReturn.vue').default,
    beforeEnter(to, from, next) {
      let hasAccess = store.getters.permissions
      if (hasAccess.includes('view_new_invoice_return') || hasAccess.includes('all')) {
        next()
      }
    }

  },//newInvoice

  {
    path: '/:id/showReturnInvoice/',
    name: 'showReturnInvoice',
    component: require('./components/return/invoice/showInvoiceReturn.vue').default,
   
     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('show_invoice_return') || hasAccess.includes('all')) {
          next()
        }
      }
  },
  {
    path: '/:id/editReturnInvoice',
    name: 'editReturnInvoice',
    component: require('./components/return/invoice/editInvoiceReturn.vue').default,

     beforeEnter(to, from, next) {
        let hasAccess = store.getters.permissions
        if (hasAccess.includes('edit_invoice_return') || hasAccess.includes('all')) {
          next()
        }
      }
  },
  
  
//account profile routes
{
  path: '/:id/account-profile',
  name: 'accountProfile',
  component: require('./components/account/accountProfile.vue').default,

   beforeEnter(to, from, next) {
      let hasAccess = store.getters.permissions
      if (hasAccess.includes('view_account_profile') || hasAccess.includes('all')) {
        next()
      }
    }
},

  {
    path: '/app/info',
    name: 'appinfo',
    component: require('./components/app/info.vue').default
  },

  {
    path: '/404',
    name: '404',
    component: require('./components/404.vue').default
  },

];





var VueCookie = require('vue-cookie');
// Tell Vue to use the plugin
Vue.use(VueCookie);




// register modal component
Vue.component('modal', {
  template: '#modal-template'

});

// register VueRouter globally
Vue.use(VueRouter);

//register SweetAlert globally
Vue.use(VueSweetalert2);

//register datepicker globally
Vue.use(datePicker);

//select
Vue.use(VSelect);

//moment
Vue.use(require('vue-moment'));

//progressbar
Vue.use(VueProgressBar, progressBarOptions)

//toastr
Vue.use(CxltToastr, toastrConfigs);




const router = new VueRouter({
  // enable this to remove # from the url
  // mode: 'history',
  routes // short for `routes: routes`

});


//async call to fetch permissions
router.beforeEach(async(to, from, next) => {

 
  //if isloaded is false only loads the permission
  if(!store.getters.isLoaded && to.name!=='/'){

    await store.dispatch("getPermissions")
  }
   
  await next()

})

// start of loader


// router.afterEach((to, from) => {
//   // Complete the animation of the route progress bar.
//   NProgress.done()
// })
// // end of loader


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
// axios.defaults.baseURL = 'https://awesomemulti.test/';

//initializing application
const app = new Vue({
  el: '#app',
  router,
  store,

});
