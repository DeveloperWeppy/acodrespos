@extends('poscloud::layouts.master')

@if(auth()->user()->hasRole('staff'))
@section('floorPlan')
  @include('poscloud::pos.floor')
@endsection

@section('orders')
  @include('poscloud::pos.orders')
@endsection


@section('orderDetails')
  @include('poscloud::pos.order')
@endsection

@endif


