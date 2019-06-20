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

/**
 * PdoTransaction
 *
 * @author James Buncle <jbuncle@hotmail.com>
 */
class PdoDbTransaction extends PdoDbConnect {

    public function connect(callable $callback): void {
        parent::connect(function(\PDO $pdo) use ($callback) {
            $pdo->beginTransaction();
            try {
                $callback($pdo);
            } catch (Exception $ex) {
                $pdo->rollBack();
                throw $ex;
            }
            $pdo->commit();
        });
    }

}
