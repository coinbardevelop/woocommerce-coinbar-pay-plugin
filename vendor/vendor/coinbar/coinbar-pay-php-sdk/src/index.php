<?php
/*
 * Copyright (c) Coinbar Spa 2023.
 *
 * This file is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Nome-Programma is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Nome-Programma.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

require_once(__DIR__ . '/../vendor/autoload.php');
use Dotenv\Dotenv;
use CoinbarPay\Sdk\CoinbarPaymentGatewayEnvConfig;

Dotenv::createImmutable(__DIR__)->load();
$cfg = new CoinbarPaymentGatewayEnvConfig();
?>

<html>
    <body>
    Welcome, PHP version is <?= phpversion(); ?>
    </body>
</html>
