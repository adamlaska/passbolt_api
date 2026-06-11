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

namespace App\Test\TestCase\Form\Users;

use App\Form\Users\UsersEditForm;
use App\Test\Lib\Model\AvatarsIntegrationTestTrait;
use App\Utility\UuidFactory;
use Cake\TestSuite\TestCase;

class UsersEditFormTest extends TestCase
{
    use AvatarsIntegrationTestTrait;

    private UsersEditForm $form;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->form = new UsersEditForm();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->form);

        parent::tearDown();
    }

    public static function usersEditFormDataProvider(): array
    {
        return [
            [
                [], //input data
                false, //expected result
            ],
            [
                [
                    'id' => 1,
                ],
                false,
            ],
            [
                [
                    'role_id' => UuidFactory::uuid(),
                ],
                false,
            ],
            [
                [
                    'id' => UuidFactory::uuid(),
                ],
                true,
            ],
            [
                [
                    'id' => UuidFactory::uuid(),
                    'role_id' => UuidFactory::uuid(),
                ],
                true,
            ],
        ];
    }

    /**
     * @dataProvider usersEditFormDataProvider
     */
    public function testUsersEditForm(array $data, bool $expectedResult)
    {
        $this->assertSame($expectedResult, $this->form->validate($data));
    }
}
