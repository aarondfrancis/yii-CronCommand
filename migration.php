<?php

class m130426_161933_cron_table extends CDbMigration
{
	public function safeUp()
	{
		$this->createTable('tbl_cron_jobs', array(
			'id' => 'pk',
			'execute_after' => 'timestamp',
			'executed_at' => 'timestamp NULL',
			'succeeded' => 'boolean',
			'action' => 'string NOT NULL',
			'parameters' => 'text',
			'execution_result' => 'text'
		));
	}

	public function safeDown()
	{
		$this->truncateTable('tbl_cron_jobs');
		$this->dropTable('tbl_cron_jobs');
	}
}