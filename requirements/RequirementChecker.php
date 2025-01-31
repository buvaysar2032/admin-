<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

if (PHP_VERSION_ID < 70400) {
    echo 'At least PHP 7.4 is required to run this script!';
    exit(1);
}

/**
 * YiiRequirementChecker allows checking, if current system meets the requirements for running the Yii application.
 * This class allows rendering of the check report for the web and console application interface.
 *
 * Example:
 *
 * ```php
 * require_once 'path/to/YiiRequirementChecker.php';
 * $requirementsChecker = new YiiRequirementChecker();
 * $requirements = array(
 *     array(
 *         'name' => 'PHP Some Extension',
 *         'mandatory' => true,
 *         'condition' => extension_loaded('some_extension'),
 *         'by' => 'Some application feature',
 *         'memo' => 'PHP extension "some_extension" required',
 *     ),
 * );
 * $requirementsChecker->checkYii()->check($requirements)->render();
 * ```
 *
 * If you wish to render the report with your own representation, use [[getResult()]] instead of [[render()]]
 *
 * Requirement condition could be in format "eval:PHP expression".
 * In this case specified PHP expression will be evaluated in the context of this class instance.
 * For example:
 *
 * ```php
 * $requirements = array(
 *     array(
 *         'name' => 'Upload max file size',
 *         'condition' => 'eval:$this->checkUploadMaxFileSize("5M")',
 *     ),
 * );
 * ```
 *
 * Note: this class definition does not match ordinary Yii style, because it should match PHP 4.3
 * and should not use features from newer PHP versions!
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
class RequirementChecker
{
    /**
     * The check results, this property is for internal usage only.
     */
    public ?array $result;

    /**
     * Check the given requirements, collecting results into internal field.
     * This method can be invoked several times checking different requirement sets.
     * Use [[getResult()]] or [[render()]] to get the results.
     * @param array|string $requirements requirements to be checked.
     * If an array, it is treated as the set of requirements;
     * If a string, it is treated as the path of the file, which contains the requirements;
     * @return $this self instance.
     */
    public function check($requirements): RequirementChecker
    {
        if (is_string($requirements)) {
            $requirements = require $requirements;
        }
        if (!is_array($requirements)) {
            $this->usageError('Requirements must be an array, "' . gettype($requirements) . '" has been given!');
        }
        if (!isset($this->result) || !is_array($this->result)) {
            $this->result = [
                'summary' => [
                    'total' => 0,
                    'errors' => 0,
                    'warnings' => 0,
                ],
                'requirements' => [],
            ];
        }
        foreach ($requirements as $key => $rawRequirement) {
            $requirement = $this->normalizeRequirement($rawRequirement, $key);
            $this->result['summary']['total']++;
            if (!$requirement['condition']) {
                if ($requirement['mandatory']) {
                    $requirement['error'] = true;
                    $requirement['warning'] = true;
                    $this->result['summary']['errors']++;
                } else {
                    $requirement['error'] = false;
                    $requirement['warning'] = true;
                    $this->result['summary']['warnings']++;
                }
            } else {
                $requirement['error'] = false;
                $requirement['warning'] = false;
            }
            $this->result['requirements'][] = $requirement;
        }

        return $this;
    }

    /**
     * Performs the check for the Yii core requirements.
     * @return RequirementChecker self instance.
     */
    public function checkYii(): RequirementChecker
    {
        return $this->check(__DIR__ . '/requirements.php');
    }

    /**
     * Return the check results.
     * @return array|null check results in format:
     *
     * ```php
     * array(
     *     'summary' => array(
     *         'total' => total number of checks,
     *         'errors' => number of errors,
     *         'warnings' => number of warnings,
     *     ),
     *     'requirements' => array(
     *         array(
     *             ...
     *             'error' => is there an error,
     *             'warning' => is there a warning,
     *         ),
     *         ...
     *     ),
     * )
     * ```
     */
    public function getResult(): ?array
    {
        return $this->result ?? null;
    }

    /**
     * Renders the requirements check result.
     * The output will vary depending is a script running from web or from console.
     */
    public function render(): void
    {
        if (!isset($this->result)) {
            $this->usageError('Nothing to render!');
        }
        $baseViewFilePath = __DIR__ . '/views';
        if (!empty($_SERVER['argv'])) {
            $viewFileName = $baseViewFilePath . '/console/index.php';
        } else {
            $viewFileName = $baseViewFilePath . '/web/index.php';
        }
        $this->renderViewFile($viewFileName, $this->result);
    }

    /**
     * Checks if the given PHP extension is available and its version matches the given one.
     * @param string $extensionName PHP extension name.
     * @param string $version required PHP extension version.
     * @param string $compare comparison operator, by default '>='
     * @return bool if PHP extension version matches.
     */
    public function checkPhpExtensionVersion(string $extensionName, string $version, string $compare = '>='): bool
    {
        if (!extension_loaded($extensionName)) {
            return false;
        }
        $extensionVersion = phpversion($extensionName);
        if (empty($extensionVersion)) {
            return false;
        }
        if (strncasecmp($extensionVersion, 'PECL-', 5) === 0) {
            $extensionVersion = substr($extensionVersion, 5);
        }

        return version_compare($extensionVersion, $version, $compare);
    }

    /**
     * Checks if PHP configuration option (from php.ini) is on.
     * @param string $name configuration option name.
     * @return bool option is on.
     */
    public function checkPhpIniOn(string $name): bool
    {
        $value = ini_get($name);
        if (empty($value)) {
            return false;
        }

        return ((int)$value === 1 || strtolower($value) === 'on');
    }

    /**
     * Checks if PHP configuration option (from php.ini) is off.
     * @param string $name configuration option name.
     * @return bool option is off.
     */
    public function checkPhpIniOff(string $name): bool
    {
        $value = ini_get($name);
        if (empty($value)) {
            return true;
        }

        return (strtolower($value) === 'off');
    }

    /**
     * Compare byte sizes of values given in the verbose representation,
     * like '5M', '15K' etc.
     * @param string $a first value.
     * @param string $b second value.
     * @param string $compare comparison operator, by default '>='.
     * @return bool comparison result.
     */
    public function compareByteSize(string $a, string $b, string $compare = '>='): bool
    {
        $compareExpression = '(' . $this->getByteSize($a) . $compare . $this->getByteSize($b) . ')';

        return $this->evaluateExpression($compareExpression);
    }

    /**
     * Gets the size in bytes from verbose size representation.
     * For example: '5K' => 5*1024
     * @param string $verboseSize verbose size representation.
     * @return int actual size in bytes.
     */
    public function getByteSize(string $verboseSize): int
    {
        if (empty($verboseSize)) {
            return 0;
        }
        if (is_numeric($verboseSize)) {
            return (int)$verboseSize;
        }
        $sizeUnit = trim($verboseSize, '0123456789');
        $size = trim(str_replace($sizeUnit, '', $verboseSize));
        if (!is_numeric($size)) {
            $size = 0;
        }
        switch (strtolower($sizeUnit)) {
            case 'kb':
            case 'k':
                $result = $size * 1024;
                break;
            case 'mb':
            case 'm':
                $result = $size * 1024 * 1024;
                break;
            case 'gb':
            case 'g':
                $result = $size * 1024 * 1024 * 1024;
                break;
            default:
                $result = 0;
                break;
        }
        return $result;
    }

    /**
     * Checks if upload max file size matches the given range.
     * @param string|null $min verbose file size minimum required value, pass null to skip minimum check.
     * @param string|null $max verbose file size maximum required value, pass null to skip maximum check.
     * @return bool success.
     */
    public function checkUploadMaxFileSize(string $min = null, string $max = null): bool
    {
        $postMaxSize = ini_get('post_max_size');
        $uploadMaxFileSize = ini_get('upload_max_filesize');
        if ($min !== null) {
            $minCheckResult = $this->compareByteSize($postMaxSize, $min, '>=') &&
                $this->compareByteSize($uploadMaxFileSize, $min);
        } else {
            $minCheckResult = true;
        }
        if ($max !== null) {
            $maxCheckResult = $this->compareByteSize($postMaxSize, $max, '<=') &&
                $this->compareByteSize($uploadMaxFileSize, $max, '<=');
        } else {
            $maxCheckResult = true;
        }

        return ($minCheckResult && $maxCheckResult);
    }

    /**
     * Renders a view file.
     * This method includes the view file as a PHP script
     * and captures the display result if required.
     * @param string $_viewFile_ view file
     * @param array|null $_data_ data to be extracted and made available to the view file
     * @param bool $_return_ whether the rendering result should be returned as a string
     * @return string|false the rendering result. Null if the rendering result is not required.
     */
    public function renderViewFile(string $_viewFile_, array $_data_ = null, bool $_return_ = false)
    {
        // we use special variable names here to avoid conflict when extracting data
        if (is_array($_data_)) {
            extract($_data_, EXTR_PREFIX_SAME, 'data');
        } else {
            $data = $_data_;
        }
        if ($_return_) {
            ob_start();
            ob_implicit_flush(false);
            require $_viewFile_;

            return ob_get_clean();
        }

        require $_viewFile_;
    }

    /**
     * Normalizes requirement ensuring it has correct format.
     * @param array $requirement raw requirement.
     * @param int|string $requirementKey requirement key in the list.
     * @return array normalized requirement.
     */
    public function normalizeRequirement(array $requirement, $requirementKey = 0): array
    {
        if (!is_array($requirement)) {
            $this->usageError('Requirement must be an array!');
        }
        if (!array_key_exists('condition', $requirement)) {
            $this->usageError("Requirement '$requirementKey' has no condition!");
        } else {
            $evalPrefix = 'eval:';
            if (is_string($requirement['condition']) && strpos($requirement['condition'], $evalPrefix) === 0) {
                $expression = substr($requirement['condition'], strlen($evalPrefix));
                $requirement['condition'] = $this->evaluateExpression($expression);
            }
        }
        if (!array_key_exists('name', $requirement)) {
            $requirement['name'] = is_numeric($requirementKey) ? 'Requirement #' . $requirementKey : $requirementKey;
        }
        if (!array_key_exists('mandatory', $requirement)) {
            if (array_key_exists('required', $requirement)) {
                $requirement['mandatory'] = $requirement['required'];
            } else {
                $requirement['mandatory'] = false;
            }
        }
        if (!array_key_exists('by', $requirement)) {
            $requirement['by'] = 'Unknown';
        }
        if (!array_key_exists('memo', $requirement)) {
            $requirement['memo'] = '';
        }

        return $requirement;
    }

    /**
     * Displays a usage error.
     * This method will then terminate the execution of the current application.
     * @param string $message the error message
     */
    public function usageError(string $message): void
    {
        echo "Error: $message\n\n";
        exit(1);
    }

    /**
     * Evaluates a PHP expression under the context of this class.
     * @param string $expression a PHP expression to be evaluated.
     * @return mixed the expression result.
     */
    public function evaluateExpression(string $expression)
    {
        return eval('return ' . $expression . ';');
    }

    /**
     * Returns the server information.
     * @return string server information.
     */
    public function getServerInfo(): string
    {
        return $_SERVER['SERVER_SOFTWARE'] ?? '';
    }

    /**
     * Returns the now date if possible in string representation.
     * @return string now date.
     */
    public function getNowDate(): string
    {
        return @strftime('%Y-%m-%d %H:%M', time());
    }
}
