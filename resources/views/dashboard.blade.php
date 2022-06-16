
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="{{asset('img/a.svg')}}">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Laravel') }}</title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
  <!-- CSS Files -->
  <link href="{{asset('css/app.css')}}" rel="stylesheet" />
  <link href="{{asset('assets/css/paper-dashboard.css?v=2.0.0')}}" rel="stylesheet" />
  <link href="{{asset('css/custom.css')}}" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">

  <!-- bootstrap select css -->
  <!-- <link href="{{asset('css/bootstrap-select.min.css')}}" rel="stylesheet" /> -->

  <!-- loader -->
  <link href="https://unpkg.com/nprogress@0.2.0/nprogress.css" rel="stylesheet" />





</head>

<body class="">
<div id="app">
  <div class="wrapper ">
    <div class="sidebar" data-color="black" data-active-color="danger">
      <!--
        Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red | yellow"
    -->
      <div class="logo">
        <a href="#" class="simple-text logo-mini">
          <div class="logo-image-small">
            <img src="{{asset('img/a.svg')}}">
          </div>
        </a>
        <a href="/" class="simple-text logo-normal">
          {{ config('app.name', 'Laravel') }}
          <!-- <div class="logo-image-big">
            <img src="../assets/img/logo-big.png">
          </div> -->
        </a>
      </div>
      <div class="sidebar-wrapper">
        <ul class="nav">

          <li>
            <router-link  to="/dashboard" aria-expanded="false">
              <i class="nc-icon nc-bank"></i>
                <span>Dashboard</span>
                <sup>{{Auth::user()->type}}</sup>
            </router-link>
          </li>

       
          @can('hasPermission','view_transactions')
           <li>
            <router-link  to="/transactions" aria-expanded="false">
            <i class="nc-icon nc-chart-bar-32"></i>
                <span> Transactions</span>
            </router-link>
          </li>
          
          @endcan
          @can('hasPermission','view_accounts')
           <li>
            <router-link  to="/accounts" aria-expanded="false">
            <i class="nc-icon nc-credit-card"></i>
                <span> Accounts</span>
            </router-link>
          </li>
          @endcan

          @can('hasPermission','view_customers')
           <li>
            <router-link  to="/customers" aria-expanded="false">
              <i class="nc-icon nc-circle-10"></i>
                <span>Customers</span>
            </router-link>
          </li>
          @endcan

          @can('hasPermission','view_categories')
           <li>
            <router-link  to="/categories" aria-expanded="false">
              <i class="nc-icon nc-bullet-list-67"></i>
                <span>Categories</span>
            </router-link>
          </li>
          @endcan

          @can('hasPermission','view_purchases')
          <li>
            <router-link  to="/purchases" aria-expanded="false">
              <i class="nc-icon nc-basket"></i>
                <span>Purchases</span>
            </router-link>
          </li>
          @endcan

          @can('hasPermission','view_products')
          <li>
            <router-link  to="/products" aria-expanded="false">
              <i class="nc-icon nc-tile-56"></i>
                <span>Products</span>
            </router-link>
          </li>
          @endcan

          @can('hasPermission','view_suppliers')
          <li>
            <router-link  to="/suppliers" aria-expanded="false">
              <i class="nc-icon nc-delivery-fast"></i>
                <span>Suppliers</span>
            </router-link>
          </li>
          @endcan

          @can('hasPermission','view_invoices')
          <li>
            <router-link  to="/invoices" aria-expanded="false">
              <i class="nc-icon nc-single-copy-04"></i>
                <span>Invoices</span>
            </router-link>
          </li>
          @endcan

          @can('hasPermission','view_returns')
          <li>
            <router-link  to="/returns" aria-expanded="false">
              <i class="nc-icon nc-refresh-69"></i>
                <span>Returns</span>
            </router-link>
          </li>
          @endcan

          @can('hasPermission','view_stocks')
          <li>
            <router-link  to="/stocks" aria-expanded="false">
              <i class="nc-icon nc-briefcase-24"></i>
                <span>Stocks</span>
            </router-link>
          </li>
          @endcan

          @can('hasPermission','view_units')
          <li>
            <router-link  to="/units" aria-expanded="false">
              <i class="nc-icon nc-ruler-pencil"></i>
                <span>Units</span>
            </router-link>
          </li>
          @endcan


          @can('hasPermission','view_users')
           <li>
            <router-link  to="/users" aria-expanded="false">
              <i class="nc-icon nc-single-02"></i>
                <span>Users</span>
            </router-link>
          </li>
          @endcan

          @can('hasPermission','view_settings')

          <li>
            <router-link  to="/settings" aria-expanded="false">
              <i class="nc-icon nc-settings-gear-65"></i>
                <span>Settings</span>
            </router-link>
          </li>
          @endcan

        </ul>
      </div>
    </div>
    <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <div class="navbar-toggle">
              <button type="button" class="navbar-toggler">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </button>
            </div>
            <a class="navbar-brand" href="/">{{Auth::user()->stores[0]->name}}</a>


          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end" id="navigation">
            <!-- <form>
              <div class="input-group no-border">
                <input type="text" value="" class="form-control" placeholder="Search...">
                <div class="input-group-append">
                  <div class="input-group-text">
                    <i class="nc-icon nc-zoom-split"></i>
                  </div>
                </div>
              </div>
            </form> -->
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link btn-magnify" href="#pablo">
                  <p>
                      {{Auth::user()->name}}
                  </p>
                </a>
              </li>
             <!--  <li class="nav-item btn-rotate dropdown">
                <a class="nav-link dropdown-toggle" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="nc-icon nc-bell-55"></i>
                  <p>
                    <span class="d-lg-none d-md-block">Notification</span>
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                  <a class="dropdown-item" href="#">Action</a>
                  <a class="dropdown-item" href="#">Another action</a>
                  <a class="dropdown-item" href="#">Something else here</a>
                </div>
              </li> -->
              <li class="nav-item btn-rotate dropdown">
                <a class="nav-link dropdown-toggle" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                   <i class="nc-icon nc-single-02"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                  <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                      document.getElementById('logout-form').submit();">
                    <i class="nc-icon nc-user-run"></i> {{ __('Logout') }}</a>
                  </a>
                </div>
                  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                  </form>
              </li>

            </ul>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
      <!-- <div class="panel-header panel-header-lg">

  <canvas id="bigDashboardChart"></canvas>


</div> -->
      <div class="content">



      <!-- vue routes will load here -->

      <router-view></router-view>

      <!-- set progressbar -->
      <vue-progress-bar></vue-progress-bar>


      </div>
      <footer class="footer footer-black  footer-white ">
        <div class="container-fluid">
          <div class="row">
            <nav class="footer-nav">
              <ul>

                <li>
                  <router-link  to="/app/info" aria-expanded="false">
                      <span>App Info</span>
                  </router-link>
                </li>
              </ul>
            </nav>
            <div class="credits ml-auto">
              <span class="copyright">
                ©Made with <i class="fa fa-heart heart"></i> by Prem Lamsal
              </span>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
</div>
 <!-- loader -->
 <script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>
 <!-- core app js -->
 <script src="{{asset('js/app.js')}}"></script>
  <!--   Core JS Files   -->
  <script src="{{asset('assets/js/core/popper.min.js')}}"></script>
  <!-- <script src="../assets/js/core/bootstrap.min.js"></script> -->
  <script src="{{asset('assets/js/plugins/perfect-scrollbar.jquery.min.js')}}"></script>
  <!--  Notifications Plugin    -->
  <script src="{{asset('assets/js/plugins/bootstrap-notify.js')}}"></script>
  <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{asset('assets/js/paper-dashboard.min.js?v=2.0.0')}}" type="text/javascript"></script>

  <!-- Paper Dashboard DEMO methods, don't include it in your project! -->
  <script src="{{asset('assets/demo/demo.js')}}"></script>
  <!-- bootstrap select javascript -->
  <!-- <script src="{{asset('js/bootstrap-select.min.js')}}"></script> -->


</body>

</html>
