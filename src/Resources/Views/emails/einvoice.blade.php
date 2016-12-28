@extends('pazaryeri-parasut::emails.template')


@section('fullname', $customerName)
@section('content')
    @yield('marketplaceName',$marketplaceName) mağazasından yapmış olduğunuz @yield('orderID',$orderID) numaralı siparişinizin faturasını ekte bulabilirsiniz.
    <br><br>

@endsection
