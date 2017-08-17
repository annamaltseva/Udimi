<?php

namespace app\controllers;

use Yii;
use app\models\Cells;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\Response;
use yii\web\Request;
use yii\db\ActiveRecord as AR;

class CellsController extends Controller
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGet()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;


        if(!$request->isAjax)
            throw new \yii\web\HttpException(405);

        $exists = Cells::find()->all();

        foreach($exists as $cell) {
            $cells[] = ['cid'=>$cell->cid, 'value'=>$cell->value];
        }

        return (isset($cells)) ? $cells : [];
    }

    public function actionSet()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;

        if(!$request->isAjax && !$request->isPost)
            throw new \yii\web\HttpException(405);

        $received = $request->post();

        foreach( $received['dataSet'] as $cell) {
            $cids[] = $cell['cid'];
            $range[$cell['cid']] = $cell['value'];
        }
        $exists = Cells::find()->where(['in','cid',$cids])->asArray()->all();

        foreach($exists as $exist){
            if($range[$exist['cid']])
                $update[] = [
                    'id'    => (int)$exist['id'],
                    'cid'   => (int)$exist['cid'],
                    'value' => (int)$range[$exist['cid']],
                ];
            unset($range[$exist['cid']]);
        }

        foreach($range as $k => $v){
            $insert[] = [
                'id'    => false,
                'cid'   => (int)$k,
                'value' => (int)$v,
            ];
        }

        $cells = new Cells();

        if(isset($insert)) {
            try {
                Yii::$app->db->createCommand()->batchInsert($cells->tableName(), $cells->attributes(), $insert)->execute();
            } catch (\Exception $e) {
                return ['error' => $e];
            }
        }

        if(isset($update)){
            try {
                foreach ($update as $row) {
                    Yii::$app->db->createCommand()->update($cells::tableName(), ['cid' => $row['cid'], 'value' => $row['value']], ['id' => $row['id']])->execute();
                }
            }catch (\Exception $e){
                return ['error'=>$e];
            }
        }

        return ['update'=>true];
    }

    public function batchUpdate($table, $columns, $rows, $duplicates = [])
    {
        $db =Yii::$app->db;

        if (($tableSchema = $db->getTableSchema($table)) !== null) {
            $columnSchemas = $tableSchema->columns;
        } else {
            $columnSchemas = [];
        }

        $sql = $db->getQueryBuilder()->batchInsert($table, $columns, $rows);

        if(!empty($duplicates)) {
            $columnDuplicates = [];

            foreach($duplicates as $i => $column) {
                var_dump($columnSchemas[$duplicates[$i]]);
                /*if(isset($columnSchemas[$duplicates[$i]])) {
                    $column = $db->quoteColumnName($column);
                    $columnDuplicates[] = $column . ' = VALUES(' . $column . ')';
                }*/
            }

            if(!empty($columnDuplicates)) {
                $sql .= ' ON DUPLICATE KEY UPDATE ' . implode(',', $columnDuplicates);
            }
        }

        return $db->createCommand()->setSql($sql);
    }

}