<?php

declare(strict_types=1);

/**
 * Passbolt ~ Open source password manager for teams
 * Copyright (c) Passbolt SA (https://www.passbolt.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Passbolt SA (https://www.passbolt.com)
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link          https://www.passbolt.com Passbolt(tm)
 * @since         5.14.0
 */

namespace App\Form\Users;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

class UsersEditForm extends Form
{
    /**
     * User edition schema.
     *
     * @param \Cake\Form\Schema $schema schema
     * @return \Cake\Form\Schema
     */
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema
            ->addField('id', ['type' => 'string'])
            ->addField('role_id', ['type' => 'string'])
            ->addField('disabled', ['type' => 'string'])
            ->addField('profile', ['type' => 'array']);
    }

    /**
     * @inheritDoc
     */
    public function validationDefault(Validator $validator): Validator
    {
        $avatarValidator = new Validator();
        $avatarValidator
            ->allowEmptyFile('file')
            ->uploadedFile('file', ['types' => ['image/jpg', 'image/jpeg', 'image/png', 'image/gif']]);

        $profileValidator = new Validator();
        $profileValidator
            ->utf8('first_name', 'First name should be a valid utf8 string.')
            ->maxLength('first_name', 255)
            ->allowEmptyString('first_name');

        $profileValidator
            ->utf8('last_name', 'Last name should be a valid utf8 string.')
            ->maxLength('last_name', 255)
            ->allowEmptyString('last_name');

        $profileValidator
            ->addNested('avatar', $avatarValidator)
            ->allowEmptyArray('avatar');

        $validator
            ->addNested('profile', $profileValidator)
            ->allowEmptyArray('profile');

        $validator
            ->requirePresence('id')
            ->uuid('id', 'The user identifier should be a valid UUID.');

        $validator
            ->allowEmptyString('role_id')
            ->uuid('role_id', 'The user role id should be a valid UUID.');

        $validator
            ->allowEmptyString('disabled')
            ->datetime('disabled');


        return $validator;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $data, array $options = []): bool
    {
        $data = $this->sanitizeData($data);

        return parent::execute($data, $options);
    }

    /**
     * @param array $data Data to sanitize
     * @return array
     */
    protected function sanitizeData(array $data): array
    {
        return array_filter([
            'id' => Hash::get($data, 'id'),
            'role_id' => Hash::get($data, 'role_id'),
            'disabled' => Hash::get($data, 'disabled'),
            'profile' => array_filter([
                'first_name' => Hash::get($data, 'profile.first_name'),
                'last_name' => Hash::get($data, 'profile.last_name'),
                'avatar' => Hash::get($data, 'profile.avatar'),
            ]),
        ]);
    }
}
