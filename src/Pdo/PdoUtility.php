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

namespace CyberPear\WpDbTools\Pdo;

use Exception;
use PDO;
use PDOStatement;

/**
 * PdoUtility
 *
 * @author James Buncle <jbuncle@hotmail.com>
 */
class PdoUtility {

    /**
     *
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function doQuery(string $query, array $params, ?callable $callback = null): bool {
        $stmt = $this->prepare($query);
        $stmt->execute($params);
        if (!$stmt->execute()) {
            return false;
        }
        if ($callback !== null) {
            /** @var array $row */
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $callback($row);
            }
        }

        return true;
    }

    public function doUpdate(string $query, array $params): bool {
        $stmt = $this->prepare($query);

        return $stmt->execute($params);
    }

    private function prepare(string $query): PDOStatement {
        $stmt = $this->pdo->prepare($query);
        if ($stmt === false) {
            throw new Exception("Failed to prepare statement.");
        }
        return $stmt;
    }

}
