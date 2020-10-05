@extends('layouts.app')

@section('content')
<div class="container">
  <div class="alert alert-warning" role="alert">
        {{$msg}}
      </div>
    <div class="row justify-content-center">

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Select Store</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('selectStoreSave') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Store Name</label>

                            <div class="col-md-6">
                                <select name="selected_store_id">
                                    @foreach($stores as $store)
                                    <option value="{{$store->id}}">{{$store->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                   Next
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
