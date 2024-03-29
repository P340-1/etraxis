<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2017-2022 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis. If not, see <https://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace App\Message\UserSettings;

use App\Entity\Enums\LocaleEnum;
use App\Entity\Enums\ThemeEnum;

/**
 * Updates profile info of the current user.
 */
final class UpdateSettingsCommand
{
    /**
     * @codeCoverageIgnore Dependency Injection constructor
     */
    public function __construct(private readonly LocaleEnum $locale, private readonly ThemeEnum $theme, private readonly string $timezone)
    {
    }

    /**
     * @return LocaleEnum New locale
     */
    public function getLocale(): LocaleEnum
    {
        return $this->locale;
    }

    /**
     * @return ThemeEnum New theme
     */
    public function getTheme(): ThemeEnum
    {
        return $this->theme;
    }

    /**
     * @return string New timezone
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }
}
