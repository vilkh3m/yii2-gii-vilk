<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use <?= $generator->indexWidgetType === 'grid' ? "kartik\\grid\\GridView" : "kartik\\widgets\\ListView" ?>;
<?= $generator->enablePjax ? 'use yii\widgets\Pjax;' : '' ?>

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?= $generator->generateString(StringHelper::basename($generator->modelClass)) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index box box-primary">
<?= $generator->enablePjax ? "    <?php Pjax::begin(); ?>\n" : ''
?>    <div class="box-header with-border">
        <?= '<?php if (Yii::$app->user->can(\'/\' . Yii::$app->controller->id . \'/create\'))'."\n\t\t\techo " ?>Html::a(<?= $generator->generateString('Create') ?>, ['create'], ['class' => 'btn btn-success btn-flat']); ?>
    </div>
    <div class="box-body table-responsive no-padding">
<?php if(!empty($generator->searchModelClass)): ?>
<?= "        <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif;
        echo "\t\t<?php\n"; ?>
        $isFa = false;
        $kiedy = "Wygenerowane: ".date('Y-m-d H:i:s');

        $defaultExportConfig = [
            GridView::PDF => [
                'label' =>'PDF',
                'icon' => $isFa ? 'file-pdf-o' : 'floppy-disk',
                'iconOptions' => ['class' => 'text-danger'],
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => 'export_<?=StringHelper::basename($generator->modelClass)?>',
                'alertMsg' => 'Za chwilę zostanie wygenerowany PDF',
                'options' => ['title' =>'Portable Document Format'],
                'mime' => 'application/pdf',
                'config' => [
                    'mode' => \kartik\mpdf\Pdf::MODE_UTF8,
                    'format' => \kartik\mpdf\Pdf::FORMAT_A4,
                    'destination' => \kartik\mpdf\Pdf::DEST_DOWNLOAD,
                    'marginTop' => 20,
                    'marginBottom' => 20,
                    'cssInline' => '.kv-wrap{padding:20px;}' .
                        '.kv-align-center{text-align:center;}' .
                        '.kv-align-left{text-align:left;}' .
                        '.kv-align-right{text-align:right;}' .
                        '.kv-align-top{vertical-align:top!important;}' .
                        '.kv-align-bottom{vertical-align:bottom!important;}' .
                        '.kv-align-middle{vertical-align:middle!important;}' .
                        '.kv-page-summary{border-top:4px double #ddd;font-weight: bold;}' .
                        '.kv-table-footer{border-top:4px double #ddd;font-weight: bold;}' .
                        '.kv-table-caption{font-size:1.5em;padding:8px;border:1px solid #ddd;border-bottom:none;}',
                    'methods' => [
                        'SetHeader' => [Yii::$app->params['pkm'].'|<?=StringHelper::basename($generator->modelClass)?>|'.$kiedy],
                        'SetFooter' => ['||{PAGENO}'],
                        'SetAuthor' => Yii::$app->params['pkm'],
                        'SetCreator' => 'int.pkm',
                    ],
                ]
            ],
            GridView::EXCEL => [
                'label' => 'Excel',
                'icon' => $isFa ? 'file-excel-o' : 'floppy-remove',
                'iconOptions' => ['class' => 'text-success'],
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => 'export_<?=StringHelper::basename($generator->modelClass)?>',
                'options' => ['title' => 'Microsoft Excel 95+'],
                'mime' => 'application/vnd.ms-excel',
                'config' => [
                    'worksheet' => 'Export<?=StringHelper::basename($generator->modelClass)?>',
                    'cssFile' => ''
                ]
            ]
        ];
        <?php echo "?>\n";

if ($generator->indexWidgetType === 'grid'):
    echo "        <?= " ?>GridView::widget([
            'exportConfig' => $defaultExportConfig,
            'dataProvider' => $dataProvider,
            <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n\t\t\t'rowOptions' => function (\$model) {\n\t\t\t\tif (\$model->status == 0) {\n\t\t\t\t\treturn ['class' => 'danger'];\n\t\t\t\t}\n\t\t\t\treturn null;\n\t\t\t},\n\t\t\t'layout' => \"{items}\\n{summary}\\n{pager}\",\n\t\t\t'columns' => [\n" : "'layout' => \"{items}\\n{summary}\\n{pager}\",\n\t\t\t'columns' => [\n"; ?>
                ['class' => 'kartik\grid\SerialColumn'],

<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "                '" . $name . "',\n";
        } else {
            echo "                // '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (++$count < 6) {
            echo "                '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        } else {
            echo "                // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'buttons' => [
                        'delete' => function ($url, $model) {
                            if ($model->status == 1) {
                                return Html::a('<span class="fa fa-unlock"></span>',
                                    $url,
                                    [
                                        'title' => Yii::t('app', 'Deaktywuj'),
                                        'data-confirm' => Yii::t('app', 'Are you sure you want to deactivate this item?'),
                                        'data-method' => 'post',
                                    ]
                                );
                            } else {
                                return Html::a('<span class="fa fa-lock"></span>',
                                    $url,
                                    [
                                        'title' => Yii::t('app', 'Aktywuj'),
                                        'data-confirm' => Yii::t('app', 'Are you sure you want to activate this item?'),
                                        'data-method' => 'post',
                                    ]
                                );
                            }
                        }
                    ],
                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action === 'view') {
                            return \yii\helpers\Url::to([Yii::$app->controller->id . '/view', 'id' => $model->id]);
                        }
                        if ($action === 'update') {
                            return \yii\helpers\Url::to([Yii::$app->controller->id . '/update', 'id' => $model->id]);
                        }
                        if ($action === 'delete') {
                            ($model->status == 1 ? $akcja = "/delete" : $akcja = "/aktywacja");
                            return \yii\helpers\Url::to([Yii::$app->controller->id . $akcja, 'id' => $model->id]);
                        }
                    },
                    'visibleButtons' => [
                        'delete' => (Yii::$app->user->can('/' . Yii::$app->controller->id . '/delete') AND Yii::$app->user->can('/' . Yii::$app->controller->id . '/aktywacja')),
                        'view' => Yii::$app->user->can('/' . Yii::$app->controller->id . '/view'),
                        'update' => Yii::$app->user->can('/' . Yii::$app->controller->id . '/update'),
                    ],
                ],
            ],
            'panel' => [
                'type' => 'primary',
            ],
        ]); ?>
<?php else: ?>
        <?= "<?= " ?>ListView::widget([
            'dataProvider' => $dataProvider,
            'itemOptions' => ['class' => 'item'],
            'itemView' => function ($model, $key, $index, $widget) {
                return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
            },
        ]) ?>
<?php endif; ?>
    </div>
<?= $generator->enablePjax ? "    <?php Pjax::end(); ?>\n" : '' ?>
</div>
