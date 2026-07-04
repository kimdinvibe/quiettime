<?php

/**
 * Created by PhpStorm.
 * User: zein
 * Date: 8/2/14
 * Time: 11:20 AM
 */

namespace backend\controllers;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\imagine\Image;
use yii\web\Controller;
use yii\helpers\VarDumper;
use yii\filters\VerbFilter;
use backend\models\LoginForm;
use backend\models\AccountForm;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;
use Intervention\Image\ImageManagerStatic;

class SystemController extends Controller
{
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            set_time_limit(0);
            return true;
        }

        return false;
    }

    public function actionCreateUsers($count = 10)
    {
        for ($index = 0; $index < $count; $index++) {
            $user = new \common\models\User();
            $user->username = Yii::$app->security->generateRandomString(34) . time();
            $user->email = Yii::$app->security->generateRandomString(34) . '@gmail.com';
            $user->setPassword('123456789');

            if ($user->save(false)) {
                echo 'Saved: ' . $user->id . ', ' . $user->username . '<br>';

                $profile = new \common\models\UserProfile();
                $profile->locale = Yii::$app->language;
                $profile->load([
                    'firstname' => Yii::$app->security->generateRandomString(8),
                    'middlename' => Yii::$app->security->generateRandomString(8),
                    'lastname' => Yii::$app->security->generateRandomString(8),
                ], '');
                $user->link('userProfile', $profile);

                $auth =  Yii::$app->authManager;
                $auth->assign($auth->getRole(\common\models\User::ROLE_USER), $user->getId());
            } else {
                var_dump($user->errors);
            }
        }

        exit();
    }

    public function actionRemoveTable($slug)
    {
        $primaryIds = Yii::$app->db->createCommand("DROP TABLE $slug")->query();
        var_dump($primaryIds);

        exit();
    }

    public function actionGetListTables()
    {
        if ($tables = Yii::$app->db->schema->getTableNames()) {
            foreach ($tables as $key => $value) {
                echo $value
                    . ' | ' . Html::a('Удалить таблицу', ['system/remove-table', 'slug' => $value], [
                        'target' => '_blank'
                    ])
                    . ' | ' . Html::a('Удалить каждую n-запись (10)', [
                        'system/remove-each', 'slug' => $value, 'count' => 10
                    ], [
                        'target' => '_blank'
                    ])
                    . ' | ' . Html::a('Наполнить n-записью', ['system/add-each', 'slug' => $value, 'count' => 10], [
                        'target' => '_blank'
                    ]) . '<br>';
            }
        }

        exit();
    }

    public function actionRemoveEach($slug, $count, $primaryKey = 'id', $condition = '{primaryKey} % {count} = 0')
    {
        $condition = str_replace('{primaryKey}', $primaryKey, $condition);
        $condition = str_replace('{count}', $count, $condition);

        $primaryIds = Yii::$app->db->createCommand("delete from $slug where $condition")->query();

        var_dump($primaryIds);

        exit();
    }

    public function actionAddEach($slug, $count)
    {
        echo $slug;

        $query = "SHOW KEYS FROM $slug WHERE Key_name = 'PRIMARY'";
        $primaryIds = Yii::$app->db->createCommand($query)->queryAll();

        echo 'Primary keys: ' . '<br>';
        var_dump($primaryIds);

        echo '<br><br>';

        echo 'Required columns: ' . '<br>';
        $columns = Yii::$app->db->createCommand("
        select 
        -- tab.table_schema as database_name,
        --     tab.table_name,
        --     col.ordinal_position as column_id,
            col.column_name,
            col.data_type,
            case when col.numeric_precision is not null
                    then col.numeric_precision
                else col.character_maximum_length end as max_length,
            case when col.datetime_precision is not null
                    then col.datetime_precision
                when col.numeric_scale is not null
                    then col.numeric_scale
                else 0 end as 'precision'
        from information_schema.tables as tab
        join information_schema.columns as col
                on col.table_schema = tab.table_schema
                and col.table_name = tab.table_name
                and col.is_nullable = 'no'
        where tab.table_schema not in ('information_schema', 'sys', 
                                    'mysql','performance_schema')
            and tab.table_type = 'BASE TABLE'
            -- and tab.table_schema = 'database name'
            and tab.table_name = '$slug'
        order by tab.table_schema,
                tab.table_name,
                col.ordinal_position;
        ")->queryAll();
        var_dump($columns);

        for ($i = 0; $i < $count; $i++) {
            foreach ($columns as $key => $value) {
                if ($value['column_name'] = 'id') {
                    if (in_array($value['data_type'], ['text', 'varchar', 'blob', 'mediumtext', 'longtext'])) {
                    }
                }
            }
        }

        exit();
    }

    // $each удалить каждый десятый если стоит $all то  все файлы
    public function actionRemoveFiles($all = null, $each = 10)
    {
        // echo Yii::getAlias('@app').'/../';

        function getDirContents($dir, $ignoreFolders = [], $isAll = null, $each = 10, &$results = array(), &$counter = 0)
        {
            $files = scandir($dir);

            foreach ($files as $key => $value) {
                if (in_array(trim($value), $ignoreFolders)) {
                    continue;
                }

                // $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
                $path = $dir . DIRECTORY_SEPARATOR . $value;

                if (!is_dir($path)) {
                    $counter = $counter + 1;

                    if ($isAll || $counter == $each) {
                        $counter = 0;
                        $results[] = $path;
                        unlink($path);
                    }
                } else if ($value != "." && $value != "..") {
                    getDirContents($path, $ignoreFolders, $isAll, $each, $results, $counter);
                    // add only files
                    // $results[] = $path;
                }
            }

            return $results;
        }

        $path = Yii::getAlias('@backend');
        $pathList = explode("/backend", $path);

        $path = '';

        for ($index = 0; $index < (count($pathList) > 1 ? count($pathList) - 1 : 1) ; $index++) {
            $path .= $pathList[$index];
        }

        VarDumper::dump(
            getDirContents(
                $path,
                [
                    'vendor',
                    '.git',
                    '.idea',
                    '.dockerignore',
                    // 'assets'
                ],
                $all,
                $each
            ),
            $depth = 10,
            $highlight = true
        );

        exit();
    }
}
