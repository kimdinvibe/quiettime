<?php

namespace frontend\components;

use Yii;
use Curl\Curl;
use Exception;

class CloudPayments
{
    public $secretKey;
    public $publicKey;
    public $domain = 'https://api.cloudpayments.ru';

    function __construct()
    {
        if ($apiKey = Yii::$app->keyStorage->get('cloudpayments.secret.key')) {
            $this->secretKey = $apiKey;
        }

        if ($apiKey = Yii::$app->keyStorage->get('cloudpayments.public.key')) {
            $this->publicKey = $apiKey;
        }
    }

    private function getCurl()
    {
        $curl = new Curl();
        $curl->setBasicAuthentication($this->publicKey, $this->secretKey);
        $curl->setHeader('Content-Type', 'application/json');

        return $curl;
    }

    private function getPath($path)
    {
        return $this->domain . '/' . $path;
    }

    public function test()
    {
        $curl = $this->getCurl();
        $curl->get($this->getPath('test'));

        if ($curl->error) {
            throw new Exception('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage);
        } else {
            return ((array)$curl->response);
        }
    }

    public function paymentsTokensCharge($amount, $invoiceId, $accountId, $token)
    {
        $curl = $this->getCurl();
        $curl->post($this->getPath('payments/tokens/charge'), [
            'Amount' => $amount,
            "Currency" => "RUB",
            "InvoiceId" => $invoiceId,
            "AccountId" => $accountId,
            "Token" => $token,
        ]);

        if ($curl->error) {
            throw new Exception('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage);
        } else {
            return $curl;
        }
    }

    public function paymentsCardCharge($amount, $invoiceId, $accountId, $cardHolder, $cryptogram)
    {
        $curl = $this->getCurl();
        $curl->post($this->getPath('payments/cards/charge'), [
            'Amount' => $amount,
            "Currency" => "RUB",
            "InvoiceId" => $invoiceId,
            "AccountId" => $accountId,
            "Name" => $cardHolder,
            "CardCryptogramPacket" => $cryptogram,
        ]);

        if ($curl->error) {
            throw new Exception('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage);
        } else {
            return $curl;
        }
    }

    public function paymentsCardPost3ds($transactionId, $paRes)
    {
        $curl = $this->getCurl();
        $curl->post($this->getPath('payments/cards/post3ds'), [
            'TransactionId' => $transactionId,
            "PaRes" => $paRes,
        ]);

        if ($curl->error) {
            throw new Exception('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage);
        } else {
            return $curl;
        }
    }
}
