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
use Cake\Validation\Validator;

class UsersEditForm extends Form
{
    /**
     * @inheritDoc
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->requirePresence('id')
            ->uuid('id', 'The user identifier should be a valid UUID.');

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
        $sanitizedData = [];
        $allowedKeys = [
            'id',
            'role_id',
            'disabled',
            'profile' => [
                'first_name',
                'last_name',
                'avatar',
            ],
        ];

        foreach ($allowedKeys as $allowedMainKey => $allowedKey) {
            if (!is_array($allowedKey)) {
                if (array_key_exists($allowedKey, $data)) {
                    $sanitizedData[$allowedKey] = $data[$allowedKey];
                }
            } else {
                foreach ($allowedKey as $allowedNestedKey) {
                    if (!array_key_exists($allowedMainKey, $data)) {
                        break;
                    }

                    if (array_key_exists($allowedNestedKey, $data[$allowedMainKey])) {
                        $sanitizedData[$allowedMainKey][$allowedNestedKey] = $data[$allowedMainKey][$allowedNestedKey];
                    }
                }
            }
        }

        return $sanitizedData;
    }
}
