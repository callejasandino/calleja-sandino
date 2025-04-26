@extends('layouts.admin')

@section('header')
    Opening Hours Management
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <p class="mb-6 text-gray-600">
                Configure the bakery's opening hours, including regular hours, breaks, and special schedules like the biweekly Saturday opening.
            </p>
            
            <admin-opening-hours></admin-opening-hours>
        </div>
    </div>
@endsection 