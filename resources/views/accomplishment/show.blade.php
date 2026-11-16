@extends('layouts.gad')

@section('page-title', '| Accomplishment Report FY ' . $report->year)
@section('header-class', 'pink')
@section('body-class', 'gad-mini')
@section('page-content')
@livewire('accomplishment-detail', ['report' => $report])
@endsection

