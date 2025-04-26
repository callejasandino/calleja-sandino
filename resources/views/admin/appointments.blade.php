@extends('layouts.admin')

@section('header')
    Appointments Management
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <p class="mb-6 text-gray-600">
                View and manage customer appointments.
            </p>
            
            <admin-appointments></admin-appointments>
        </div>
    </div>
@endsection 