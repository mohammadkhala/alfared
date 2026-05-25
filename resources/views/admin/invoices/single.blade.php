@extends('admin.invoices._layout', ['title' => 'فاتورة #' . $order->order_number])

@section('content')
  @include('admin.invoices._partial', ['order' => $order])
@endsection
