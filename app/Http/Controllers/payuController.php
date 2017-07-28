<?php
namespace App\Http\Controllers;

use Tzsk\Payu\Facade\Payment;

class UsuariosController extends Controller {

public function paybefore(){

    return view('admin.payu.payu');

}
public function pay() {
    /**
     * These are the minimum required fieldset.
     */
    $order = Order::find($id);

$data = [
    \PayUParameters::DESCRIPTION => 'Payment cc test',
    \PayUParameters::IP_ADDRESS => '127.0.0.1',
    \PayUParameters::CURRENCY => 'COP',
    \PayUParameters::CREDIT_CARD_NUMBER => '378282246310005',
    \PayUParameters::CREDIT_CARD_EXPIRATION_DATE => '2017/02',
    \PayUParameters::CREDIT_CARD_SECURITY_CODE => '1234',
    \PayUParameters::INSTALLMENTS_NUMBER => 1
];

$order->payWith($data, function($response, $order) {
    if ($response->code == 'SUCCESS') {
        $order->update([
            'payu_order_id' => $response->transactionResponse->orderId,
            'transaction_id' => $response->transactionResponse->transactionId
        ]);
        // ... El resto de acciones sobre la orden
    } else {
    //... El código de respuesta no fue exitoso
    }
}, function($error) {
    // ... Manejo de errores PayUException, InvalidArgument
});

LaravelPayU::doPing(function($response) {
        $code = $response->code;
        // ... revisar el codigo de respuesta
    }, function($error) {
     // ... Manejo de errores PayUException
    });

 LaravelPayU::getPSEBanks(function($banks) {
        //... Usar datos de bancos
        foreach($banks as $bank) {
            $bankCode = $bank->pseCode;
        }
    }, function($error) {
        // ... Manejo de errores PayUException, InvalidArgument
    });


$order = Order::find($id);

$order->searchById(function($response, $order) {
    // ... Usar la información de respuesta
}, function($error) {
    // ... Manejo de errores PayUException, InvalidArgument
});

$order->searchByReference(function($response, $order) {
    // ... Usar la información de respuesta
}, function($error) {
    // ... Manejo de errores PayUException, InvalidArgument
});

$order->searchByTransaction(function($response, $order) {
    // ... Usar la información de respuesta
}, function($error) {
    // ... Manejo de errores PayUException, InvalidArgument
});


}

public function status() {
    $payment = Payment::capture(); # Recieve the payment.
    # Returns PayuPayment Instance.

    $payment->getData(); # Get the full response from Gateway.
$payment->isCaptured(); # Is the payment captured or some internal failure occured.
$payment->transaction_id; # Your Local Transaction ID.
$payment->payment_id; # PayU Payment ID.
$payment->total_amount; # Get Tototal Amount Deducted.
$payment->bank_reference_number; # Issued Bank Refernce Number.
$payment->bank_code; # Issued Bank Code.
$payment->card_number; # Redacted Card Number. If paid through Card.

# New Feature:
$payment->get('Attribute'); # Attribute is anything inside the Post Data.

# Example-
$payment->get('udf5');
# OR
$payment->get('zipcode');
# See more in $payment->getData();


# And many more like 'status', 'mode' are found in the Database Table.
}
}

