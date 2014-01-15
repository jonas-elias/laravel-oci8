<?php

use Mockery as m;
use Illuminate\Database\Schema\Blueprint;

class Oci8SchemaGrammarTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}

	public function testBasicCreateTable()
	{
		$blueprint = new Blueprint('users');
		$blueprint->create();
		$blueprint->increments('id');
		$blueprint->string('email');

		$conn = $this->getConnection();

		$statements = $blueprint->toSql($conn, $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('create table users ( id number(10,0) not null, email varchar2(255) not null, constraint users_id_primary primary key ( id ) )', $statements[0]);
	}

	public function testBasicCreateTableWithPrimary()
	{
		$blueprint = new Blueprint('users');
		$blueprint->create();
		$blueprint->integer('id')->primary();
		$blueprint->string('email');

		$conn = $this->getConnection();

		$statements = $blueprint->toSql($conn, $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('create table users ( id number(10,0) not null, email varchar2(255) not null, constraint users_id_primary primary key ( id ) )', $statements[0]);
    }

	public function testBasicCreateTableWithPrefix()
	{
		$blueprint = new Blueprint('users');
		$blueprint->create();
		$blueprint->increments('id');
		$blueprint->string('email');
		$grammar = $this->getGrammar();
		$grammar->setTablePrefix('prefix_');

		$conn = $this->getConnection();

		$statements = $blueprint->toSql($conn, $grammar);

		$this->assertEquals(1, count($statements));
		$this->assertEquals('create table prefix_users ( id number(10,0) not null, email varchar2(255) not null, constraint users_id_primary primary key ( id ) )', $statements[0]);
	}

	public function testBasicCreateTableWithPrefixAndPrimary()
	{
		$blueprint = new Blueprint('users');
		$blueprint->create();
		$blueprint->integer('id')->primary();
		$blueprint->string('email');
		$grammar = $this->getGrammar();
		$grammar->setTablePrefix('prefix_');

		$conn = $this->getConnection();

		$statements = $blueprint->toSql($conn, $grammar);

		$this->assertEquals(1, count($statements));
		$this->assertEquals('create table prefix_users ( id number(10,0) not null, email varchar2(255) not null, constraint users_id_primary primary key ( id ) )', $statements[0]);
    }

	public function testBasicCreateTableWithPrefixPrimaryAndForeignKeys()
	{
        $blueprint = new Blueprint('users');
		$blueprint->create();
		$blueprint->integer('id')->primary();
		$blueprint->string('email');
		$blueprint->integer('foo_id');
		$blueprint->foreign('foo_id')->references('id')->on('orders');
		$grammar = $this->getGrammar();
		$grammar->setTablePrefix('prefix_');

		$conn = $this->getConnection();

		$statements = $blueprint->toSql($conn, $grammar);

		$this->assertEquals(1, count($statements));
		$this->assertEquals('create table prefix_users ( id number(10,0) not null, email varchar2(255) not null, foo_id number(10,0) not null, constraint users_foo_id_foreign foreign key ( foo_id ) references prefix_orders ( id ), constraint users_id_primary primary key ( id ) )', $statements[0]);
    }

	public function testBasicCreateTableWithPrefixPrimaryAndForeignKeysWithCascadeDelete()
	{
        $blueprint = new Blueprint('users');
		$blueprint->create();
		$blueprint->integer('id')->primary();
		$blueprint->string('email');
		$blueprint->integer('foo_id');
		$blueprint->foreign('foo_id')->references('id')->on('orders')->onDelete('cascade');
		$grammar = $this->getGrammar();
		$grammar->setTablePrefix('prefix_');

		$conn = $this->getConnection();

		$statements = $blueprint->toSql($conn, $grammar);

		$this->assertEquals(1, count($statements));
		$this->assertEquals('create table prefix_users ( id number(10,0) not null, email varchar2(255) not null, foo_id number(10,0) not null, constraint users_foo_id_foreign foreign key ( foo_id ) references prefix_orders ( id ) on delete cascade, constraint users_id_primary primary key ( id ) )', $statements[0]);
    }

	public function testBasicAlterTable()
	{
		$blueprint = new Blueprint('users');
		$blueprint->increments('id');
		$blueprint->string('email');

		$conn = $this->getConnection();

		$statements = $blueprint->toSql($conn, $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( id number(10,0) not null, email varchar2(255) not null, constraint users_id_primary primary key ( id ) )', $statements[0]);
	}

	public function testBasicAlterTableWithPrimary()
	{
		$blueprint = new Blueprint('users');
		$blueprint->increments('id');
		$blueprint->string('email');

		$conn = $this->getConnection();

		$statements = $blueprint->toSql($conn, $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( id number(10,0) not null, email varchar2(255) not null, constraint users_id_primary primary key ( id ) )', $statements[0]);
	}

	public function testBasicAlterTableWithPrefix()
	{
		$blueprint = new Blueprint('users');
		$blueprint->increments('id');
		$blueprint->string('email');
		$grammar = $this->getGrammar();
		$grammar->setTablePrefix('prefix_');

		$conn = $this->getConnection();

		$statements = $blueprint->toSql($conn, $grammar);

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table prefix_users add ( id number(10,0) not null, email varchar2(255) not null, constraint users_id_primary primary key ( id ) )', $statements[0]);
	}

	public function testBasicAlterTableWithPrefixAndPrimary()
	{
		$blueprint = new Blueprint('users');
		$blueprint->increments('id');
		$blueprint->string('email');
		$grammar = $this->getGrammar();
		$grammar->setTablePrefix('prefix_');

		$conn = $this->getConnection();

		$statements = $blueprint->toSql($conn, $grammar);

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table prefix_users add ( id number(10,0) not null, email varchar2(255) not null, constraint users_id_primary primary key ( id ) )', $statements[0]);
	}

	public function testDropTable()
	{
		$blueprint = new Blueprint('users');
		$blueprint->drop();
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('drop table users', $statements[0]);
	}

	public function testDropTableWithPrefix()
	{
		$blueprint = new Blueprint('users');
		$blueprint->drop();
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('drop table users', $statements[0]);
	}

	public function testDropColumn()
	{
		$blueprint = new Blueprint('users');
		$blueprint->dropColumn('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users drop column ( foo )', $statements[0]);

		$blueprint = new Blueprint('users');
		$blueprint->dropColumn(array('foo', 'bar'));
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users drop column ( foo, bar )', $statements[0]);
	}

	public function testDropPrimary()
	{
		$blueprint = new Blueprint('users');
		$blueprint->dropPrimary('users_pk');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users drop constraint users_pk', $statements[0]);
	}

	public function testDropUnique()
	{
		$blueprint = new Blueprint('users');
		$blueprint->dropUnique('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users drop constraint foo', $statements[0]);
	}

	public function testDropIndex()
	{
		$blueprint = new Blueprint('users');
		$blueprint->dropIndex('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('drop index foo', $statements[0]);
	}

	public function testDropForeign()
	{
		$blueprint = new Blueprint('users');
		$blueprint->dropForeign('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users drop constraint foo', $statements[0]);
	}

	public function testDropTimestamps()
	{
		$blueprint = new Blueprint('users');
		$blueprint->dropTimestamps();
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users drop column ( created_at, updated_at )', $statements[0]);
	}

    public function testRenameTable()
	{
		$blueprint = new Blueprint('users');
		$blueprint->rename('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users rename to foo', $statements[0]);
	}

	public function testRenameTableWithPrefix()
	{
		$blueprint = new Blueprint('users');
		$blueprint->rename('foo');
                $grammar = $this->getGrammar();
		$grammar->setTablePrefix('prefix_');

		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users rename to foo', $statements[0]);
	}

	public function testAddingPrimaryKey()
	{
		$blueprint = new Blueprint('users');
		$blueprint->primary('foo', 'bar');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add constraint bar primary key (foo)', $statements[0]);
	}

	public function testAddingUniqueKey()
	{
		$blueprint = new Blueprint('users');
		$blueprint->unique('foo', 'bar');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add constraint bar unique ( foo )', $statements[0]);
	}

	public function testAddingIndex()
	{
		$blueprint = new Blueprint('users');
		$blueprint->index(array('foo', 'bar'), 'baz');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));

        $this->assertEquals('create index baz on users ( foo, bar )', $statements[0]);
	}

	public function testAddingForeignKey()
	{
		$blueprint = new Blueprint('users');
		$blueprint->foreign('foo_id')->references('id')->on('orders');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add constraint users_foo_id_foreign foreign key ( foo_id ) references orders ( id )', $statements[0]);
	}

	public function testAddingForeignKeyWithCascadeDelete()
	{
		$blueprint = new Blueprint('users');
		$blueprint->foreign('foo_id')->references('id')->on('orders')->onDelete('cascade');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add constraint users_foo_id_foreign foreign key ( foo_id ) references orders ( id ) on delete cascade', $statements[0]);
	}

	public function testAddingIncrementingID()
	{
		$blueprint = new Blueprint('users');
		$blueprint->increments('id');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( id number(10,0) not null, constraint users_id_primary primary key ( id ) )', $statements[0]);
	}

	public function testAddingString()
	{
		$blueprint = new Blueprint('users');
		$blueprint->string('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo varchar2(255) not null )', $statements[0]);

		$blueprint = new Blueprint('users');
		$blueprint->string('foo', 100);
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo varchar2(100) not null )', $statements[0]);

		$blueprint = new Blueprint('users');
		$blueprint->string('foo', 100)->nullable()->default('bar');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo varchar2(100) null default \'bar\' )', $statements[0]);

		$blueprint = new Blueprint('users');
		$blueprint->string('foo', 100)->nullable()->default(new Illuminate\Database\Query\Expression('CURRENT TIMESTAMP'));
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo varchar2(100) null default CURRENT TIMESTAMP )', $statements[0]);
	}

	public function testAddingLongText()
	{
		$blueprint = new Blueprint('users');
		$blueprint->longText('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo clob not null )', $statements[0]);
	}

	public function testAddingMediumText()
	{
		$blueprint = new Blueprint('users');
		$blueprint->mediumText('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo clob not null )', $statements[0]);
	}

	public function testAddingText()
	{
		$blueprint = new Blueprint('users');
		$blueprint->text('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo varchar2(4000) not null )', $statements[0]);
	}

	public function testAddingBigInteger()
	{
		$blueprint = new Blueprint('users');
		$blueprint->bigInteger('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo number(19,0) not null )', $statements[0]);

		$blueprint = new Blueprint('users');
		$blueprint->bigInteger('foo', true);
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo number(19,0) not null, constraint users_foo_primary primary key ( foo ) )', $statements[0]);
	}

    public function testAddingInteger()
	{
		$blueprint = new Blueprint('users');
		$blueprint->integer('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo number(10,0) not null )', $statements[0]);

		$blueprint = new Blueprint('users');
		$blueprint->integer('foo', true);
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo number(10,0) not null, constraint users_foo_primary primary key ( foo ) )', $statements[0]);
	}

	public function testAddingMediumInteger()
	{
		$blueprint = new Blueprint('users');
		$blueprint->mediumInteger('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo number(7,0) not null )', $statements[0]);
	}

	public function testAddingSmallInteger()
	{
		$blueprint = new Blueprint('users');
		$blueprint->smallInteger('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo number(5,0) not null )', $statements[0]);
	}

	public function testAddingTinyInteger()
	{
		$blueprint = new Blueprint('users');
		$blueprint->tinyInteger('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo number(3,0) not null )', $statements[0]);
	}

	public function testAddingFloat()
	{
		$blueprint = new Blueprint('users');
		$blueprint->float('foo', 5, 2);
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo number(5, 2) not null )', $statements[0]);
	}

	public function testAddingDouble()
	{
		$blueprint = new Blueprint('users');
		$blueprint->double('foo', 5, 2);
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo number(5, 2) not null )', $statements[0]);
	}

	public function testAddingDecimal()
	{
		$blueprint = new Blueprint('users');
		$blueprint->decimal('foo', 5, 2);
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo number(5, 2) not null )', $statements[0]);
	}

	public function testAddingBoolean()
	{
		$blueprint = new Blueprint('users');
		$blueprint->boolean('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo char(1) not null )', $statements[0]);
	}

	public function testAddingEnum()
	{
		$blueprint = new Blueprint('users');
		$blueprint->enum('foo', array('bar', 'baz'));
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo varchar2(255) not null )', $statements[0]);
	}

	public function testAddingDate()
	{
		$blueprint = new Blueprint('users');
		$blueprint->date('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo date not null )', $statements[0]);
	}

	public function testAddingDateTime()
	{
		$blueprint = new Blueprint('users');
		$blueprint->dateTime('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo date not null )', $statements[0]);
	}

	public function testAddingTime()
	{
		$blueprint = new Blueprint('users');
		$blueprint->time('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo date not null )', $statements[0]);
	}

	public function testAddingTimeStamp()
	{
		$blueprint = new Blueprint('users');
		$blueprint->timestamp('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo date default sysdate not null )', $statements[0]);
	}

	public function testAddingTimeStamps()
	{
		$blueprint = new Blueprint('users');
		$blueprint->timestamps();
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( created_at date default sysdate not null, updated_at date default sysdate not null )', $statements[0]);
	}

	public function testAddingBinary()
	{
		$blueprint = new Blueprint('users');
		$blueprint->binary('foo');
		$statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

		$this->assertEquals(1, count($statements));
		$this->assertEquals('alter table users add ( foo blob not null )', $statements[0]);
	}

	protected function getConnection()
	{
		return m::mock('Illuminate\Database\Connection');
	}

	public function getGrammar()
	{
		return new yajra\Oci8\Schema\Grammars\OracleGrammar;
	}

}