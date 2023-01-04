@extends('laravelpwa::layouts.master')

@section('floorPlan')
  @include('laravelpwa::pos.floor')
@endsection

@section('orders')
  @include('laravelpwa::pos.orders')
@endsection


@section('orderDetails')
  @include('laravelpwa::pos.order')
@endsection


