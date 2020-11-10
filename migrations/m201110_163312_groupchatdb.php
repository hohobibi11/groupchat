<?php

use yii\db\Migration;

/**
 * Class m201110_163312_groupchatdb
 */
class m201110_163312_groupchatdb extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string(60)->notNull()->unique(),
            'password' => $this->string(256)->notNull(),
            'role' => $this->init(),
            'authKey' => $this->string(256)->notNull(),
            'accessToken' => $this->string(256)->notNull()
        ]);
        $this->createTable('message', [
            'id' => $this->primaryKey(),
            'content' => $this->string(1000)->notNull(),
            'deleted' => $this->boolean()->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'user_id' => $this->integer()->notNull()
        ]);
        $this->createIndex('ind_user_id', 'message', 'user_id');
        $this->addForeignKey('fk_message_user', 'message', 'user_id', 'user', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_message_user', 'message');
        $this->dropIndex('ind_user_id', 'message');
        $this->dropTable('message');
        $this->dropTable('user');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201110_163312_groupchatdb cannot be reverted.\n";

        return false;
    }
    */
}
