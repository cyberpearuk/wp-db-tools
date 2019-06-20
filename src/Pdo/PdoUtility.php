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

use PDO;

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

    public function doQuery(string $query, array $params, callable $callback): void {
        $stmt = $this->pdo->prepare($query);

        $stmt->execute($params);
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $callback($row);
            }
        }
    }

    public function doUpdate(string $query, array $params): bool {
        return $this->pdo->prepare($query)->execute($params);
    }

}
