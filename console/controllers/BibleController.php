<?php

/**
 * Eugine Terentev <eugine@terentev.net>
 */

namespace console\controllers;

use common\models\Bible;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

use function GuzzleHttp\json_decode;

/**
 * Class ExtendedMessageController
 * @package console\controllers
 */
class BibleController extends Controller
{
    public function actionImport($fileName = "rst.json")
    {
        $path = \Yii::$app->basePath . '/../../' . $fileName;

        $handle = fopen($path, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                // process the line read.
                echo $line;

                $object = json_decode($line);
                (new Bible($object))->save();
            }

            fclose($handle);
        } else {
            // error opening the file.
        }
    }
}
