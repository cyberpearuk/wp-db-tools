<?php

/*
 * Copyright (C) 2019 CyberPear (https://www.cyberpear.co.uk) - All Rights Reserved
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace CyberPear\WpDbTools;

/**
 * FindReplaceUtility
 *
 * @author James Buncle <jbuncle@hotmail.com>
 */
class FindReplaceUtility {

    public static function findReplaceSerialised(string $needle, string $replace, string $value): string {
        if (StringUtils::isSerialised($value)) {
            $unserialised = unserialize($value);
            // Assume serialised array
            self::searchReplaceArray($needle, $replace, $unserialised);

            $newValue = serialize($unserialised);
            return $newValue;
        } else {
            // Replace non-serialised
            return str_replace($needle, $replace, $value);
        }
    }

    private static function searchReplaceArray(string $needle, string $replace, array &$array): void {
        foreach ($array as &$value) {
            if (is_array($value)) {
                self::searchReplaceArray($needle, $replace, $value);
                continue;
            }

            if (is_string($value)) {
                $value = str_replace($needle, $replace, $value);
                continue;
            }
        }
    }

}
