@extends('templates.main')

@section('titulo','Login Domina')

@section('styles')
@endsection

@section('content')


@section('content')
<a href="http://www.payulatam.com/logos/pol.php?l=133&c=591db350e1e5d" target="_blank"><img src="http://www.payulatam.com/logos/logo.php?l=133&c=591db350e1e5d" alt="PayU Latam" border="0" /></a>
<div class="container">
 <div class="row">
 <div class="col-md-10 col-md-offset-1">
 <div class="panel panel-default">
 <div class="panel-heading">Payment</div>
 
 <div class="panel-body">
 <form action="" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <script
                        src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                        data-key="pk_test_xxxxxxxxxxxxxx"
                        data-amount="1000"
                        data-name="Pago semanal"
                        data-description="10.00 $ "
                        data-image="https://stripe.com/img/documentation/checkout/marketplace.png">
                      </script>
                    </form>
 </div>
 </div>
 </div>
 </div>
</div>
@endsection

@endsection