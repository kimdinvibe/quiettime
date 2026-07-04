<?php
/**
 * Created by IntelliJ IDEA.
 * User: admin
 * Date: 24.12.17
 * Time: 21:43
 */

namespace common\behaviors;


use yii\helpers\VarDumper;

class UploadBehavior extends \trntv\filekit\behaviors\UploadBehavior
{
    public $classParent;

    public $classParentAttribute;

    /**
     * @param array $files
     */
    protected function saveFilesToRelation($files)
    {
        $modelClass = $this->getUploadModelClass();

        foreach ($files as $file) {
            $model = new $modelClass;
            $model->setScenario($this->uploadModelScenario);
            $model = $this->loadModel($model, $file);

            if($this->classParentAttribute && $this->classParent) {
                $model->{$this->classParentAttribute} = $this->classParent;
            }

            //echo VarDumper::dump([$files, $this->classParentAttribute, $this->classParent, $model->attributes], 10, true); exit;

            if ($this->getUploadRelation()->via !== null) {
                $model->save(false);
            }
            $this->owner->link($this->uploadRelation, $model);
        }
    }

    /**
     * @param array $files
     */
    protected function updateFilesInRelation($files)
    {
        $modelClass = $this->getUploadModelClass();

        foreach ($files as $file) {
            $model = $modelClass::findOne([$this->getAttributeField('path') => $file['path']]);
            if ($model) {
                $model->setScenario($this->uploadModelScenario);
                $model = $this->loadModel($model, $file);

                if($this->classParentAttribute && $this->classParent) {
                    $model->{$this->classParentAttribute} = $this->classParent;
                }

                $model->save(false);
            }
        }
    }
}