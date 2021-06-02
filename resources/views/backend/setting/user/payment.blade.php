@extends('layout.main')
@section('title', $title)
@section('content')
<div class="card ">
  <div class="card-header ">
    <h4 class="card-title">{{$title}}</h4>
  </div>
  <div class="card-body ">
    <form method="post" action="{{route('user.payment.store')}}" class="form-horizontal">
      @csrf
      <div class="row">
        <label class="col-md-3 col-form-label">Nama</label>
        <div class="col-md-9">
          <div class="form-group">
            <input name="name" value="{{$request-name}}" type="text" class="form-control" readonly="">
          </div>
        </div>
      </div>
      <div class="row">
        <label class="col-md-3 col-form-label">Email</label>
        <div class="col-md-9">
          <div class="form-group">
            <input type="email" value="{{$request->email}}" name="email" class="form-control" readonly="">
          </div>
        </div>
      </div>
      <div class="row">
        <label class="col-md-3"></label>
        <div class="col-md-9">
          <div class="form-check">
            <label class="form-check-label">
              <input class="form-check-input" type="checkbox">
              <span class="form-check-sign"></span>
              Ingatkan Saya 
            </label>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer ">
      <div class="row">
        <label class="col-md-3"></label>
        <div class="col-md-9">
          <button type="reset" class="btn btn-fill btn-danger">Reset</button>
          <button type="submit" class="btn btn-fill btn-success">Daftar</button>
        </div>
      </div>
    </div>
  </form>

</div>
@endsection
