<?php

/*
 * Copyright (C) 2019 CyberPear (https://www.cyberpear.co.uk)
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

use CyberPear\WpDbTools\Pdo\PdoDbTransaction;
use CyberPear\WpDbTools\Pdo\PdoUtility;
use CyberPear\WpDbTools\Pdo\PdoUtilityConnection;
use PDO;

/**
 * Description of WpDbRelocate
 *
 * @author James Buncle <jbuncle@hotmail.com>
 */
class WpDbRelocate {

    public function run($argv): void {
        $servername = $argv[1];
        $username = $argv[2];
        $password = $argv[3];

        $needle = $argv[4];
        $replacement = $argv[5];
        $dbname = "wordpress";

        echo "Replacing '$needle' with '$replacement'\n";

        $pdoDbConnect = new PdoDbTransaction($servername, $username, $password, $dbname);
        $pdoUtilityConnection = new PdoUtilityConnection($pdoDbConnect);

        $this->updateDatabase($pdoUtilityConnection, $needle, $replacement);

        echo "Update complete\n";
    }

    /**
     * 
     * @param PDO $pdoUtility
     * @param string $needle      The value to search for.
     * @param string $replacement The value to replace with.
     * @param string $idField     The field (db column name) to use for entry ID.
     * @param string $valueField  The field (db column name) to search and replace in.
     * @param string $table       The database table name
     * @return void
     */
    private function updateTable(
            PdoUtility $pdoUtility,
            string $needle,
            string $replacement,
            string $idField,
            string $valueField,
            string $table
    ): void {
        $query = "SELECT `$idField`, `$valueField` FROM `$table` WHERE `$valueField` LIKE '%:needle%'";
        $updateCount = 0;
        $params = [
            ':needle' => $needle,
        ];
        
        $pdoUtility->doQuery($query, $params, function(array $row) use ($pdoUtility, $needle, $replacement, &$updateCount, $idField, $valueField, $table) {
            $value = $row["$valueField"];

            if (StringUtils::contains($value, $needle)) {
                $query = "UPDATE `$table` SET `$valueField`=:value WHERE `$idField`=:id";

                $newValue = FindReplaceUtility::findReplaceSerialised($needle, $replacement, $value);
                $params = [
                    ':id' => $row["$idField"],
                    ':value' => $newValue,
                ];

                $pdoUtility->doQuery($query, $params);
                $updateCount++;
            }
        });
        echo "Updated '$updateCount' rows in '$table' for value '$valueField'\n";
    }

    private function updateDatabase(\PdoUtilityConnection $pdoUtilityConnection, string $needle, string $replacement) {
        $pdoUtilityConnection->connect(function(PdoUtility $pdoUtility) use ($needle, $replacement) {
            $this->updateTable($pdoUtility, $needle, $replacement, 'option_id', 'option_value', 'wp_options');
            $this->updateTable($pdoUtility, $needle, $replacement, 'ID', 'post_content', 'wp_posts');
            $this->updateTable($pdoUtility, $needle, $replacement, 'ID', 'post_content', 'wp_posts');
            $this->updateTable($pdoUtility, $needle, $replacement, 'guid', 'post_content', 'wp_posts');
            $this->updateTable($pdoUtility, $needle, $replacement, 'meta_id', 'meta_value', 'wp_postmeta');
        });
    }

}
