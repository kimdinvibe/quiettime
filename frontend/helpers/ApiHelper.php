<?php 

namespace frontend\helpers;

use Yii;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;


// $values = [
//     'phone' => Yii::t('common', $label),
// ];

class ApiHelper {
    static function checkRequiredFields($values, $params) {
        foreach ($values as $value => $label) {
            if (!isset($params[$value]) || !$params[$value] || !trim($params[$value])) {
                throw new HttpException(404, Yii::t('api', 'Need to fill required field «{0}».', [
                    $label,
                ]));
            }
        }

        return true;
    }

    static function returnSuccess($params = []) {
        return array_merge_recursive([
            'result' => 'success'
        ], $params);
    }
}

?>