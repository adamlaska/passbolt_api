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
 * @since         3.4.0
 */

namespace Passbolt\Folders\Test\TestCase\Controller\Folders;

use App\Test\Lib\Utility\PaginationTestTrait;
use Cake\Utility\Hash;
use Passbolt\Folders\Test\Factory\FolderFactory;
use Passbolt\Folders\Test\Lib\FoldersIntegrationTestCase;

class FoldersIndexControllerPaginationTest extends FoldersIntegrationTestCase
{
    use PaginationTestTrait;

    public static function dataProviderForSortingDirection(): array
    {
        return [
            [],
            ['Folders.name', 'asc', 'name'],
            ['Folders.name', 'desc', 'name'],
            ['Folders.created', 'asc', 'created'],
            ['Folders.created', 'desc', 'created'],
            ['Folders.modified', 'asc', 'modified'],
            ['Folders.modified', 'desc', 'modified'],
        ];
    }

    /**
     * Test the expected pagination information for the component's default
     * config.
     *
     * @Given I have 19 folders
     * @When I paginate on page 2 with 10 folders by page sorting by folder name
     * @Then I should see 9 folders sorted according to $direction 'asc' resp. 'desc'.
     * @dataProvider dataProviderForSortingDirection
     * @param string|null $sortedField Sorted field.
     * @param string $direction Sorting direction.
     * @param string|null $path Path where to find the sorted field in the response data.
     * @return void
     * @throws \Exception
     */
    public function testFoldersIndexPagination(?string $sortedField = null, string $direction = 'asc', ?string $path = null)
    {
        $numberOfFolders = 19;
        $limit = 10;
        $page = 2;
        $expectedCurrent = 9;

        $user = $this->logInAsUser();
        $data = Hash::merge(
            $this->getArrayOfDistinctRandomStrings($numberOfFolders, 'name'),
            $this->getArrayOfDistinctRandomPastDates($numberOfFolders, 'created'),
            $this->getArrayOfDistinctRandomPastDates($numberOfFolders, 'modified'),
        );
        FolderFactory::make($data)->withPermissionsFor([$user])->withFoldersRelationsFor([$user])->persist();

        $paginationParameter = [
            'limit=' . $limit,
            'direction=' . $direction,
            'page=' . $page,
        ];

        // If the option sorted is defined and set to empty, no sorting will apply
        if ($sortedField) {
            $paginationParameter[] = 'sort=' . $sortedField;
        }

        $paginationParameter = implode('&', $paginationParameter);
        $this->getJson("/folders.json?$paginationParameter&api-version=2");

        $this->assertSuccess();
        $this->assertCountPaginatedEntitiesEquals($expectedCurrent);
        $this->assertBodyContentIsSorted($path ?? 'name', $direction);
    }
}
