@extends('layout')

@section('content')
@if (session()->has('success'))
    @include('elements.success')
@elseif (session()->has('error'))
@include('elements.error')
@endif

<table id="payments" class="display">
    <thead>
        <tr>
            <th>Payment Id</th>
            <th>Payer Id</th>
            <th>Payer Email</th>
            <th>amount</th>
            <th>currency</th>
            <th>status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($payments as $payment)
            <tr>
                <td>{{$payment->payment_id}}</td>
                <td>{{$payment->payer_id}}</td>
                <td>{{$payment->payer_email}}</td>
                <td>{{$payment->amount}}</td>
                <td>{{$payment->currency}}</td>
                <td>{{$payment->status}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection

@section('script')
<script>
    $(function() {
        $('#payments').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "responsive": true,
        })
    });
</script>
@endsection