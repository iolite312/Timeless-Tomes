<?php

namespace App\Validation;

use Rakit\Validation\Rule;
use App\Repositories\DatabaseRepository;

class UniqueRule extends Rule
{
    protected $message = ':attribute has been used';

    protected $fillableParams = ['table', 'column', 'except'];

    protected \PDO $pdo;

    public function __construct()
    {
        $repository = new DatabaseRepository();
        $this->pdo = $repository->getConnection();
    }

    public function check($value): bool
    {
        // make sure required parameters exists
        $this->requireParameters(['table', 'column']);

        // getting parameters
        $column = $this->parameter('column');
        $table = $this->parameter('table');
        $except = $this->parameter('except');

        if ($except and $except == $value) {
            return true;
        }

        // do query
        $stmt = $this->pdo->prepare("select count(*) as count from `{$table}` where `{$column}` = :value");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        // true for valid, false for invalid
        return intval($data['count']) === 0;
    }
}
