@extends('layout')

@section('content')
<div class="text-center w-75 m-auto mt-5">
    <h1>Welcome to PayPal integeration app</h1>
    <h2>Proceed to checkout</h2>

    @if (session()->has('success'))
    @include('elements.success')
    @elseif (session()->has('error'))
    @include('elements.error')
    @endif

    @if($errors->has('amount'))
        <div class="alert alert-danger d-flex align-items-center w-25 m-auto" role="alert">
            <div>
                {{ $errors->first('amount') }}
            </div>
        </div>
    @endif

    <form method="POST" action="{{route('payment')}}">
        @csrf
        <div class="mb-3 w-50 m-auto">
            <label for="amount" class="form-label">Amount</label>
            <input type="text" name="amount" class="form-control" id="amount" placeholder="$ USD">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection
