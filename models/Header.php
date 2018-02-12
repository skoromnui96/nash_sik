<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\ImageManager;

/**
 * This is the model class for table "header".
 *
 * @property int $id
 * @property string $header
 */
class Header extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'header';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['header'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'header' => 'Header',
        ];
    }
    public function getImages()
    {
        return $this->hasMany(ImageManager::className(), ['item_id' => 'id'])->andWhere(['class'=>self::tableName()])->orderBy('sort');
    }
    public function getImagesLinks()
    {
        return ArrayHelper::getColumn($this->images,'imageUrl');
    }
    public function getImagesLinksData()
    {
        return ArrayHelper::toArray($this->images,[
                ImageManager::className() => [
                    'caption'=>'name',
                    'key'=>'id',
                ]]
        );
    }

    public function beforeDelete()
    {
        if ($this->getImages()){
            foreach ($this->images as $one){
                unlink(Yii::getAlias('@images').'/header/'. $one->name);
            }
        }
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    public static function getIds()
    {
        return  self::find()->select('id')->orderBy('id DESC')->one();
    }
}