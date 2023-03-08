@extends('layout')

@section('content')

@if (session()->has('success'))
    @include('elements.success')
@elseif (session()->has('error'))
@include('elements.error')
@endif

<div class="text-center w-50 m-auto mt-5">
    <form method="POST" action="{{route('payment')}}">
        @csrf
        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" name="amount" class="form-control" id="amount" placeholder="$ USD">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection
