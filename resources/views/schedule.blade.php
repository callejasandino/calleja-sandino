@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl sm:tracking-tight">
                Schedule a Visit to Our Bakery
            </h1>
            <p class="mt-5 max-w-xl mx-auto text-xl text-gray-500">
                Choose a date and time that works for you and we'll reserve a spot.
            </p>
        </div>

        <div class="lg:flex lg:space-x-8">
            <div class="lg:w-2/3 mb-6 lg:mb-0">
                <appointment-form></appointment-form>
            </div>

            <div class="lg:w-1/3">
                <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Current Status
                        </h3>
                        <div class="mt-4">
                            <store-status></store-status>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 overflow-hidden shadow rounded-lg text-white">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium">
                            Why Visit Us?
                        </h3>
                        <div class="mt-4 space-y-4">
                            <div class="flex">
                                <svg class="h-6 w-6 text-indigo-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <p class="ml-3 text-white">Fresh baked goods daily</p>
                            </div>
                            <div class="flex">
                                <svg class="h-6 w-6 text-indigo-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <p class="ml-3 text-white">Personal service</p>
                            </div>
                            <div class="flex">
                                <svg class="h-6 w-6 text-indigo-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <p class="ml-3 text-white">Custom orders available</p>
                            </div>
                            <div class="flex">
                                <svg class="h-6 w-6 text-indigo-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <p class="ml-3 text-white">Locally sourced ingredients</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 