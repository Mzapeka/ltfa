<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_list`.
 */
class m180209_214723_create_user_list_table extends Migration
{
    /**
     * @inheritdoc
     */
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
