<?php

namespace mole\yii\validators;

return [
    'rbc' => [
        'min' => 8,
        'max' => 24,
        'upper' => 1,
        'lower' => 1,
        'digit' => 1,
        'special' => 1,
        'hasUser' => false,
        'hasEmail' => false,
    ],
    PasswordValidator::SIMPLE => [
        'min' => 6,
        'upper' => 0,
        'lower' => 1,
        'digit' => 1,
        'special' => 0,
        'hasUser' => false,
        'hasEmail' => false
    ],
    PasswordValidator::NORMAL => [
        'min' => 8,
        'upper' => 1,
        'lower' => 1,
        'digit' => 1,
        'special' => 0,
        'hasUser' => true,
        'hasEmail' => true
    ],
    PasswordValidator::FAIR => [
        'min' => 10,
        'upper' => 1,
        'lower' => 1,
        'digit' => 1,
        'special' => 1,
        'hasUser' => true,
        'hasEmail' => true
    ],
    PasswordValidator::MEDIUM => [
        'min' => 10,
        'upper' => 1,
        'lower' => 1,
        'digit' => 2,
        'special' => 1,
        'hasUser' => true,
        'hasEmail' => true
    ],
    PasswordValidator::STRONG => [
        'min' => 12,
        'upper' => 2,
        'lower' => 2,
        'digit' => 2,
        'special' => 2,
        'hasUser' => true,
        'hasEmail' => true
    ],
];
