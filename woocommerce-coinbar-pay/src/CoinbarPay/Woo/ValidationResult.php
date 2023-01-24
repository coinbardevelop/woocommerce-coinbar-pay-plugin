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

namespace CoinbarPay\Woo;

class ValidationResult {

	/**
	 * @var string[]
	 */
	private array $errors   = array();

	/**
	 * @var string[]
	 */
	private array $warnings = array();

	/**
	 * @param string $error the error message to record
	 * @return void
	 */
	public function addError(string $error) {
		$this->errors[] = $error;
	}

	/**
	 * @param string $warning the warning message to record
	 * @return void
	 */
	public function addWarning(string $warning) {
		$this->warnings[] = $warning;
	}

	/**
	 * @return string[] a list of the error messages
	 */
	public function getErrors(): array {
		return $this->errors;
	}

	/**
	 * @return string[] a list of the warning messages
	 */
	public function getWarnings(): array {
		return $this->warnings;
	}

	/**
	 * @return bool `true` if there are no errors nor warnings
	 */
	public function isOk(): bool {
		return ! ($this->isError() || $this->isWarning());
	}

	/**
	 * @return bool `true` if there is any error
	 */
	public function isError(): bool {
		return !empty($this->errors);
	}

	/**
	 * @return bool `true` if there is any warning
	 */
	public function isWarning(): bool {
		return !empty($this->warnings);
	}

}
