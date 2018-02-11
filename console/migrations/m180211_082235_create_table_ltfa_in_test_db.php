<?php

use yii\db\Migration;

/**
 * Class m180211_082235_create_table_ltfa_in_test_db
 */
class m180211_082235_create_table_ltfa_in_test_db extends Migration
{

    public function init()
    {
        $this->db = 'db_test';
        parent::init();
    }

    public function up()
    {
        $this->createTable('user_list', [
            'key' => $this->primaryKey(),
            'id' => $this->string(32)->notNull()->comment('Request Id'),
            'user' => $this->string()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user_list');
    }
}
