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

use App\Utility\UuidFactory;
use Cake\TestSuite\TestCase;
use App\Form\Users\UsersEditForm;
use Cake\I18n\DateTime;
use App\Test\Lib\Model\AvatarsIntegrationTestTrait;
use Laminas\Diactoros\UploadedFile;

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
        $uploadFile = FIXTURES . 'Avatar' . DS . 'ada.png';

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
                    'profile' => [
                        'first_name' => 1
                    ],
                ],
                false,
            ],
            [
                [
                    'id' => UuidFactory::uuid(),
                    'profile' => [
                        'first_name' => 'addddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd'
                    ],
                ],
                false,
            ],
            [
                [
                    'id' => UuidFactory::uuid(),
                    'profile' => [
                        'last_name' => 1
                    ],
                ],
                false,
            ],
            [
                [
                    'id' => UuidFactory::uuid(),
                    'profile' => [
                        'last_name' => 'addddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd'
                    ],
                ],
                false,
            ],
            [
                [
                    'id' => UuidFactory::uuid(),
                    'disabled' => '2006-12-27',
                ],
                false,
            ],
            [
                [
                    'id' => UuidFactory::uuid(),
                    'profile' => [
                        'avatar' => [
                            'file' => 'not a file'
                        ],
                    ],
                ],
                false,
            ],
            [
                [
                    'id' => UuidFactory::uuid(),
                    'profile' => [
                        'avatar' => [
                            'file' => new UploadedFile(
                                $uploadFile,
                                filesize($uploadFile),
                                UPLOAD_ERR_OK,
                                $uploadFile,
                                'image/png'
                            )
                        ],
                    ],
                ],
                true,
            ],
            [
                [
                    'id' => UuidFactory::uuid(),
                    'profile' => [
                        'first_name' => 'ddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd'
                    ],
                ],
                true,
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
                    'disabled' => DateTime::yesterday(),
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
