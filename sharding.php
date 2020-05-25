<?php

// Создать логику общения с ними тестового PHP-скрипта — например, распределение новых пользователей по шардам.

Class User
{
	public $id;
	public $login;
	public $name;
	public $email;

	public function __construct($id, $login, $name, $email)
	{
		$this->id = $id;
		$this->login = $login;
		$this->name = $name;
		$this->email = $email;
	}
}

Class DB
{
	private static $instance = null;
	private $server_even;
	private $server_odd;

	private function __construct()
	{
		$this->server_even = new PDO('mysql:host=127.0.0.1;port=3306;dbname=testdb', 'user', 'pass');
		$this->server_odd = new PDO('mysql:host=127.0.0.1;port=3307;dbname=testdb', 'user', 'pass');
	}

	public static function getInstance()
	{
		if (static::$instance === null) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	public function getConnection(User $user)
	{
		return $user->id % 2 === 0 ? $this->server_even : $this->server_odd;
	}
}

Class UserStorage
{
	protected function getConn(User $user)
	{
		return DB::getInstance()->getConnection($user);
	}

	public function insert(User $user)
	{
		$conn = $this->getConn($user);
		$sql = "INSERT INTO users (id, login, name, email) VALUES (:id, :login, :name, :email)";
		$sth = $conn->prepare($sql);
		$sth->execute([
			":id" => $user->id,
			":login" => $user->login,
			":name" => $user->name,
			":email" => $user->email
		]);
		echo $conn->lastInsertId();
	}

	public function delete(User $user)
	{
		$conn = $this->getConn($user);
		$sql = "DELETE FROM users WHERE ID = :id";
		$sth = $conn->prepare($sql);
		$sth->execute([
			":id" => $user->id
		]);
	}
}

$admin = new User(0, 'admin', 'Super Admin', 'admin@site.com');
$user = new User(1, 'user', 'Simple User', 'user@site.com');

$userStorage = new UserStorage();

$userStorage->insert($admin);
$userStorage->insert($user);
$userStorage->delete($user);