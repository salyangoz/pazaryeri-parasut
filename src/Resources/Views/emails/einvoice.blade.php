@extends('pazaryeri-parasut::emails.template')


@section('fullname', $customerName)
@section('content')
    @yield('marketplaceName',$marketplaceName) mağazasından yapmış olduğunuz #@yield('orderID',$orderID) numaralı siparişinizin faturasını ekte bulabilirsiniz.
    <br><br>
																	
	<br/>
	Ekteki faturayı çıktı alarak muhasebeleştirebilirsiniz.
	<br/>
	İade işlemlerinizde fatura üzerindeki gerekli alanları doldurarak ürünle birlikte kargoya vermeniz gerekmektedir.
	<br/><br/>
	Sormak istediğiniz tüm sorular için bizimle iletişime geçebilirsiniz.
	<br/><br/>
	<b>İletişim Bilgilerimiz</b>
	<br/>
	<b>Telefon: <span style="color:#181818; font-weight:normal; text-decoration:none;" href="tel:@yield('companyPhone',$companyPhone)">@yield('companyPhone',$companyPhone)</span></b> 
	<br/>
	<b>E-posta: </b><a style="color:#181818;text-decoration:none;" href="mailto:@yield('companyEmail',$companyEmail)">@yield('companyEmail',$companyEmail)</a>

@endsection
