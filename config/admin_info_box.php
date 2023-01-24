<?php
/*
 * Copyright (c) Coinbar Spa 2023.
 * This file is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * along with the software.  If not, see <http://www.gnu.org/licenses/>.
 */

 /*
  * This file contains an informational box that is shown in the admin
  * configuration section when the user is configuring the integration
  * with the CoinbarPay gateway.
  * Plugin implementors: modify its content to reflect
  * the content you want to convey to your (potential) users, or delete
  * this file if you don't want the box to be shown to the user.
  */
?>
<div class="postbox">
    <div class="inside">
        <a href="https://coinbar.io"><img src="https://coinbar.io/static/media/logo-coinbar-hor.f21a28f5c91c26c050be7456c3fc6cb2.svg" width="200px"/></a>
        <div>    
            <h1>How to activate <b>Coinbar</b><span style="color:lightblue">Pay</span></h1>
                <ol>
                    <li>Sign up to <a target='_blank' href="https://pay.coinbar.io/sign-up/">Coinbar website</a> by filling out the required information and creating an account.</li>
                    <li>Apply for Know Your Customer (KYC) verification as Business Account. This is a standard process for businesses that handle financial transactions and is used to ensure compliance with regulations and prevent fraud.</li>
                    <li>Once your account is activated, log in to the Coinbar business panel and activate CoinbarPay by clicking on the appropriate button or link.</li>
                    <li>Set up the configuration by following the instructions provided on the activation panel. This will include setting up service keys and preferences.</li>
                    <li>After completing these steps, your CoinbarPay plugin will be ready to accept payments. </li>
                </ol>

                <p>Please note that you may need to follow additional steps as per the regulatory requirement in your region, and it\'s always a good idea to consult <a href="https://pay.coinbar.io">customer support</a> for any additional information.</p>
        </div>
    </div> <!-- .inside -->        
</div> <!-- .postbox -->
