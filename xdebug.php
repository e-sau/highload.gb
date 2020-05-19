<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

class User
{
    public $data = [
        ["name" => "Wiley", "surname" => "Bartell", "phone" => "1-887-514-5579"],
        ["name" => "Jessy", "surname" => "Cremin", "phone" => "+98(3)8077948342"],
        ["name" => "Erin", "surname" => "Cummings", "phone" => "488-988-6854"],
        ["name" => "Garrett", "surname" => "Conroy", "phone" => "+04(5)8143153582"],
        ["name" => "Arno", "surname" => "Barrows", "phone" => "+60(6)7643641070"],
        ["name" => "Adolfo", "surname" => "Bogisich", "phone" => "(387)862-8671"],
        ["name" => "Steve", "surname" => "Rutherford", "phone" => "(337)136-7092"],
        ["name" => "Quentin", "surname" => "Donnelly", "phone" => "209-264-2821"],
        ["name" => "Jabari", "surname" => "Bechtelar", "phone" => "+86(2)2229281935"],
        ["name" => "Eriberto", "surname" => "Schaefer", "phone" => "(121)421-1355"],
    ];

    public function getData()
    {
        return $this->data;
    }
}

class UserTable
{
    private $user = null;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function renderTable()
    {
        echo "<table>
        <tbody>
            <tr>
                <th>Name</th>
                <th>Surname</th>
                <th>Phone</th>
            </tr>";

        $countUsers = 0;
        $users = $this->user->getData();
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['name']}</td>";
            echo "<td>{$user['surname']}</td>";
            echo "<td>{$user['phone']}</td>";
            echo "</tr>";
            $countUsers++;
        }

        echo "</tbody></table>";
        echo "<div>Всего пользователей: " . $countUsers;
    }
}

$user = new User();
$userTable = new UserTable($user);
$userTable->renderTable();
