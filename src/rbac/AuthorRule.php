<?php
namespace mole\yii\rbac;

use yii\base\InvalidParamException;
use yii\rbac\Rule;

class AuthorRule extends Rule
{
    public $name = 'isAuthor';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        if (!isset($params['key'])) {
            throw new InvalidParamException('key must be in $params');
        }
        if (!isset($params['item'])) {
            throw new InvalidParamException('item must be in $params');
        }

        $key = $params['key'];
        $data = $params['item'];
        if (!is_array($data) || !isset($data[$key])) {
            return false;
        }

        return $data[$key] == $user;
    }
}
