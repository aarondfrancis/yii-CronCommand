<?php

/**
 * This is the model class for table "tbl_cron_jobs".
 *
 * The followings are the available columns in table 'tbl_cron_jobs':
 * @property integer $id
 * @property string $execute_after
 * @property string $executed_at
 * @property string $action
 * @property string $parameters
 */
class CronJob extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CronJob the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'tbl_cron_jobs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('execute_after, action', 'required'),
			array('succeeded', 'numerical', 'integerOnly'=>true),
			array('action', 'length', 'max'=>255),
			array('executed_at, parameters, execution_result', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, execute_after, executed_at, succeeded, action, parameters, execution_result', 'safe', 'on'=>'search')
		);
	}

	// doesn't matter
	public function relations(){ return array();}
	public function attributeLabels(){ return array(); }
	
	public function beforeValidate(){
		if(gettype($this->parameters) !== "string"){
			$this->parameters = serialize($this->parameters);
		}
		return parent::beforeValidate();
	}

	public function afterFind(){
		$this->parameters = unserialize($this->parameters);
		return parent::afterFind();
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('execute_after',$this->execute_after,true);
		$criteria->compare('executed_at',$this->executed_at,true);
		$criteria->compare('action',$this->action,true);
		$criteria->compare('parameters',$this->parameters,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}