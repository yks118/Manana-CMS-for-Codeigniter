<?php
namespace App\Database\Migrations;

/**
 * Class AddAccount
 *
 * @package App\Database\Migrations
 */
class AddAccount extends \CodeIgniter\Database\Migration {
	/**
	 * up
	 */
	public function up()
	{
		$this->forge->addField([
			'id'    => [
				'type'              => 'INT',
				'constraint'        => 11,
				'unsigned'          => true,
				'auto_increment'    => true
			],
			'username'  => [
				'type'          => 'VARCHAR',
				'constraint'    => 25,
				'null'          => false,
				'unique'        => true
			],
			'password'  => [
				'type'  => 'TEXT',
				'null'  => false
			],
			'name'  => [
				'type'          => 'VARCHAR',
				'constraint'    => 50,
				'null'          => false
			],
			'last_login_at' => [
				'type'  => 'DATETIME',
				'null'  => true
			],
			'created_at'    => [
				'type'  => 'DATETIME',
				'null'  => false
			],
			'updated_at'    => [
				'type'  => 'DATETIME',
				'null'  => false
			],
			'deleted_at'    => [
				'type'  => 'DATETIME',
				'null'  => true
			]
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('account');
	}

	/**
	 * down
	 */
	public function down()
	{
		$this->forge->dropTable('account');
	}
}
