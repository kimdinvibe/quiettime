<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\SpotRemark */

$this->title = Yii::t('backend', 'Test');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php if($model->spotAttachments): ?>
    <h3><?= Yii::t('backend', 'Images') ?></h3>
    <?php foreach ($model->spotAttachments as $attach): ?>
        <?php \yii\helpers\VarDumper::dump($attach->attributes, 10, true) ?>
        <?= Html::a(Html::img($attach->getFullPath(), [
            'style' => 'width: 23%; margin-right: 1%; margin-bottom: 1%;',
        ]), $attach->getFullPath(), ['rel' => 'fancybox']); ?>

        <?php echo $uploaded_img = $_SERVER["DOCUMENT_ROOT"].$attach->base_url."/".$attach->path ?>

        <?php

            $exif = exif_read_data($uploaded_img);

        \yii\helpers\VarDumper::dump($exif, 10, true)

        ?>

        end

    <?php endforeach; ?>
<?php endif; ?>

<?php function image_fix_orientation($filename) {
    $exif = exif_read_data($filename);
    if (!empty($exif['Orientation'])) {
        $image = imagecreatefromjpeg($filename);
        switch ($exif['Orientation']) {
            case 3:
                $image = imagerotate($image, 180, 0);
                break;

            case 6:
                $image = imagerotate($image, -90, 0);
                break;

            case 8:
                $image = imagerotate($image, 90, 0);
                break;
        }

        imagejpeg($image, $filename, 90);
    }
} ?>
