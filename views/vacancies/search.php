<?php

/* @var $this \yii\web\View */
/* @var $model \app\models\VacanciesSearchForm */
/* @var $dataProvider \yii\data\DataProviderInterface */

$this->title = Yii::t('app', 'Vacancies search');
$this->params['breadcrumbs'][] = ['label' => $this->title];
$this->on(\yii\web\View::EVENT_BEGIN_PAGE, function() {
    \app\assets\vacancies\SearchAsset::register($this);
});
?>

<div class="row">
    <div class="col-sm-12">
        <?php $form = \yii\widgets\ActiveForm::begin(['method' => 'get']) ?>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'queryName')->label(false)->textInput(['autofocus' => true, 'placeholder' => $model->getAttributeLabel('queryName')]) ?>
                    <?= $form->field($model, 'queryNameOperator')->label(false)->radioList($model->queryOperatorLabels()) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'queryDescription')->label(false)->textInput(['placeholder' => $model->getAttributeLabel('queryDescription')]) ?>
                    <?= $form->field($model, 'queryDescriptionOperator')->label(false)->radioList($model->queryOperatorLabels()) ?>
                </div>
            </div>

            <?= $form->field($model, 'area')->label(false)->widget(\app\widgets\AreaSelect2\Widget::className()) ?>
            <?= $form->field($model, 'industry')->label(false)->widget(\app\widgets\IndustrySelect2\Widget::className()) ?>

            <div class="form-group">
                <?= \yii\helpers\Html::submitInput(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= \yii\helpers\Html::a(Yii::t('app', 'Reset'), ['/vacancies/search'], ['class' => 'btn btn-danger']) ?>
            </div>
        <?php $form->end() ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <?= \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'label' => Yii::t('app', 'Vacancy Name'),
                    'attribute' => 'name',
                ],
                [
                    'label' => Yii::t('app', 'Employer'),
                    'attribute' => 'employer',
                    'content' => function($model) {
                        return \yii\helpers\Html::a($model['employer']['name'], $model['employer']['alternate_url'], [
                            'target' => '_blank',
                        ]);
                    },
                ],
                [
                    'label' => Yii::t('app', 'Salary'),
                    'attribute' => 'salary',
                    'content' => function($model) {
                        if (!empty($model['salary'])) {
                            $to = isset($model['salary']['to']) ? $model['salary']['to'] : 0;
                            $from = isset($model['salary']['from']) ? $model['salary']['from'] : 0;
                            $currency = isset($model['salary']['currency']) ? $model['salary']['currency'] : '';

                            if (empty($to)) {
                                return Yii::t('app', 'From {from} {currency}', [
                                    'from' => '<b>' . $from . '</b>',
                                    'currency' => $currency,
                                ]);
                            } elseif (empty($from)) {
                                return Yii::t('app', 'To {to} {currency}', [
                                    'to' => '<b>' . $to . '</b>',
                                    'currency' => $currency,
                                ]);
                            } else {
                                return Yii::t('app', 'From {from} to {to} {currency}', [
                                    'from' => '<b>' . $from . '</b>',
                                    'to' => '<b>' . $to . '</b>',
                                    'currency' => $currency,
                                ]);
                            }
                        } else {
                            return Yii::t('app', 'Not specified');
                        }
                    },
                ],
                [
                    'label' => Yii::t('app', 'External URL'),
                    'attribute' => 'alternate_url',
                    'content' => function($model) {
                        return \yii\helpers\Html::a($model['alternate_url'], $model['alternate_url'], ['target' => '_blank']);
                    }
                ],
            ],
        ]) ?>
    </div>
</div>