@extends('admin.invoices._layout', ['title' => 'طباعة جماعية (' . $orders->count() . ' فاتورة)'])

@section('content')
  @foreach($orders as $order)
    @include('admin.invoices._partial', ['order' => $order])
  @endforeach
@endsection
