<?php
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
 * @since         2.0.0
 *
 * @var array $body
 */
use App\Utility\Purifier;
use App\View\Helper\AvatarHelper;
use Cake\I18n\DateTime;
use Cake\Routing\Router;
if (PHP_SAPI === 'cli') {
    Router::fullBaseUrl($body['fullBaseUrl']);
}
$admin = $body['admin'];
$group = $body['group'];
$isAdmin = $body['isAdmin'];

echo $this->element('Email/module/avatar',[
    'url' => AvatarHelper::getAvatarUrl($admin['profile']['avatar']),
    'text' => $this->element('Email/module/avatar_text', [
        'user' => $admin,
        'datetime' => DateTime::now(),
        'text' => __('{0} updated your membership in the group {1}',
            Purifier::clean($admin['profile']['first_name']),
            Purifier::clean($group['name'])
        )
    ])
]);

if ($isAdmin) {
    $text = __('You are now a group manager of this group.');
    $text .= ' ' . __('As group manager you are now authorized to edit the members of this group.');
    $text .= ' ' . __('As member of the group you still have access to all the passwords that are shared with this group.');
} else {
    $text = __('You are no longer a group manager of this group.');
    $text .= ' ' . __('You are no longer authorized to edit the members of this group.');
    $text .= ' ' . __('As member of the group you still have access to all the passwords that are shared with this group.');
}

echo $this->element('Email/module/text', [
    'text' => $text
]);

echo $this->element('Email/module/button', [
    'url' => Router::url('/', true),
    'text' => __('Log in passbolt')
]);
