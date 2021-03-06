<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "blog".
 *
 * @property int $id
 * @property string $title
 * @property string $created_at
 */
class Blog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'blog';
    }
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string'],
            [['text'], 'string'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'created_at' => 'Дата добавления',
            'updated_at' => 'Дата обновления',
            'text' => 'Текст новости'
        ];
    }
    public function getImages()
    {
        return $this->hasMany(ImageManager::className(), ['item_id' => 'id'])->andWhere(['class'=>self::tableName()])->orderBy('sort');
    }
    public function getImage()
    {
        return $this->hasOne(ImageManager::className(), ['item_id' => 'id'])->andWhere(['class'=>self::tableName()])->orderBy('sort');
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
    public function getDescription()
    {
        $string = $this->text;
        $string = substr($string, 0, 200);
        $string = substr($string, 0, strrpos($string, ' '));
        return $string."… ";
    }
    public function getShort()
    {
        $string = $this->text;
        $string = substr($string, 0, 500);
        $string = substr($string, 0, strrpos($string, ' '));
        return $string."… ";
    }

}
