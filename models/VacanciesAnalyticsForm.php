<?php

namespace app\models;

use app\components\data\VacanciesDataProvider;
use yii\authclient\InvalidResponseException;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class VacanciesAnalyticsForm extends Model
{
    const MAX_PAGE = 19;
//    const MAX_PAGE = 2;
    const PAGE_SIZE = 100;

    public $query;

    protected $_totalCount;
    protected $_salaryAverage;
    protected $_salaryMax;
    protected $_salaryMin;

    public function rules()
    {
        return [
            ['query', 'string'],
        ];
    }

    public function process()
    {
        $allModels = [];
        $page = 1;

        $dataProvider = new VacanciesDataProvider([
            'params' => [
                'text' => $this->query,
                'only_with_salary' => true,
                'area' => 113,
                'currency' => 'RUR',
            ],
            'pagination' => [
                'page' => $page,
                'pageSize' => self::PAGE_SIZE,
            ],
        ]);

        while ($page++ < self::MAX_PAGE && ($models = $dataProvider->getModels())) {
            $allModels = ArrayHelper::merge($allModels, $models);
            $dataProvider->pagination->setPage($page);
            $dataProvider->prepare(true);
        }

        $this->_salaryMax = array_reduce($allModels, function($carry, $item) {
            if (!empty($item['salary']['to'])) {
                return max([$item['salary']['to'], $carry]);
            } elseif (!empty($item['salary']['from'])) {
                return max([$item['salary']['from'], $carry]);
            } else {
                return $carry;
            }
        }, 0);

        $this->_salaryMin = array_reduce($allModels, function($carry, $item) {
            if (!empty($item['salary']['from'])) {
                return min([$item['salary']['from'], $carry]);
            } elseif (!empty($item['salary']['to'])) {
                return min([$item['salary']['to'], $carry]);
            } else {
                return $carry;
            }
        }, 0);

        $salarySum = array_reduce($allModels, function($carry, $item) {
            if (!empty($item['salary']['to']) && !empty($item['salary']['from'])) {
                $salary = round(($item['salary']['to'] + $item['salary']['from']) / 2);
            } else if (!empty($item['salary']['to'])) {
                $salary = $item['salary']['to'];
            } else if (!empty($item['salary']['from'])) {
                $salary = $item['salary']['from'];
            } else {
                $salary = 0;
            }
            return $carry + $salary;
        }, 0);

        $this->_totalCount = count($allModels);
        $this->_salaryAverage = round($salarySum / $this->_totalCount);
    }

    /**
     * @return mixed
     */
    public function getSalaryAverage()
    {
        return $this->_salaryAverage;
    }

    /**
     * @return mixed
     */
    public function getTotalCount()
    {
        return $this->_totalCount;
    }

    /**
     * @return mixed
     */
    public function getSalaryMax()
    {
        return $this->_salaryMax;
    }

    /**
     * @return mixed
     */
    public function getSalaryMin()
    {
        return $this->_salaryMin;
    }
}