<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cells".
 *
 * @property int $id
 * @property int $cid Cell position id
 * @property int $value
 */
class Cells extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cells';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cid', 'value'], 'required'],
            [['cid', 'value'], 'integer'],
            [['cid'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cid' => 'Cid',
            'value' => 'Value',
        ];
    }
}