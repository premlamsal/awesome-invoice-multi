@extends('layouts.app')

@section('content')
<div class="container">
  <div class="alert alert-warning" role="alert">
        {{$msg}}
      </div>
    <div class="row justify-content-center">

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create Store</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('saveStore') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Store Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" required  autofocus>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="address" class="col-md-4 col-form-label text-md-right">Address</label>

                            <div class="col-md-6">
                                <input id="address" type="text" class="form-control" name="address" required>

                            </div>
                        </div>
                          <div class="form-group row">
                            <label for="phone" class="col-md-4 col-form-label text-md-right">Phone</label>

                            <div class="col-md-6">
                                <input id="phone" type="text" class="form-control" name="phone" required>

                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="mobile" class="col-md-4 col-form-label text-md-right">Mobile</label>

                            <div class="col-md-6">
                                <input id="mobile" type="text" class="form-control" name="mobile" required>

                            </div>
                        </div>         

                       <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" required>

                            </div>
                        </div>    

                        <div class="form-group row">
                            <label for="address" class="col-md-4 col-form-label text-md-right">Url</label>

                            <div class="col-md-6">
                                <input id="url" type="text" class="form-control" name="url" required>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="tax-number" class="col-md-4 col-form-label text-md-right">Tax Number</label>

                            <div class="col-md-6">
                                <input id="tax_number" type="number" class="form-control" name="tax_number" required>

                            </div>
                        </div>  
                          <div class="form-group row">
                            <label for="tax-percentage" class="col-md-4 col-form-label text-md-right">Tax Percentage</label>

                            <div class="col-md-6">
                                <input id="tax_percentage" type="number" class="form-control" name="tax_percentage" required>

                            </div>
                        </div> 
                         <div class="form-group row">
                            <label for="profit-percentage" class="col-md-4 col-form-label text-md-right">Profit Percentage</label>

                            <div class="col-md-6">
                                <input id="profit_percentage" type="number" class="form-control" name="profit_percentage" required>

                            </div>
                        </div>     
                                 



                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                   Create
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
