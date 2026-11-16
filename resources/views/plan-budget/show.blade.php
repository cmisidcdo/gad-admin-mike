@extends('layouts.gad')

@section('page-title', '| Plan and Budget FY ' . $plan->year)
@section('body-class', 'gad-mini')
@section('page-content')
@livewire('plan-budget-detail', ['plan' => $plan])
@endsection

