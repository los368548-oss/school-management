<?php
/**
 * Validator Class
 *
 * Handles input validation and sanitization
 */

class Validator {
    private $errors = [];
    private $data = [];

    public function __construct($data = []) {
        $this->data = $data;
    }

    public function validate($rules) {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $this->data[$field] ?? null;
            $fieldRules = is_array($fieldRules) ? $fieldRules : explode('|', $fieldRules);

            foreach ($fieldRules as $rule) {
                $this->validateRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    private function validateRule($field, $value, $rule) {
        $ruleParts = explode(':', $rule);
        $ruleName = $ruleParts[0];
        $ruleParam = $ruleParts[1] ?? null;

        $method = 'validate' . ucfirst($ruleName);

        if (method_exists($this, $method)) {
            if (!$this->$method($value, $ruleParam)) {
                $this->addError($field, $ruleName, $ruleParam);
            }
        }
    }

    private function validateRequired($value) {
        return !is_null($value) && $value !== '' && (!is_array($value) || !empty($value));
    }

    private function validateEmail($value) {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function validateMin($value, $param) {
        if (is_numeric($value)) {
            return $value >= $param;
        }
        return strlen($value) >= $param;
    }

    private function validateMax($value, $param) {
        if (is_numeric($value)) {
            return $value <= $param;
        }
        return strlen($value) <= $param;
    }

    private function validateNumeric($value) {
        return is_numeric($value);
    }

    private function validateInteger($value) {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    private function validateAlpha($value) {
        return ctype_alpha($value);
    }

    private function validateAlphaNum($value) {
        return ctype_alnum($value);
    }

    private function validateDate($value) {
        return strtotime($value) !== false;
    }

    private function validateUrl($value) {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    private function validateIn($value, $param) {
        $options = explode(',', $param);
        return in_array($value, $options);
    }

    private function validateRegex($value, $param) {
        return preg_match($param, $value);
    }

    private function addError($field, $rule, $param = null) {
        $message = $this->getErrorMessage($field, $rule, $param);
        $this->errors[$field][] = $message;
    }

    private function getErrorMessage($field, $rule, $param = null) {
        $messages = [
            'required' => 'The :field field is required.',
            'email' => 'The :field field must be a valid email address.',
            'min' => 'The :field field must be at least :param characters.',
            'max' => 'The :field field may not be greater than :param characters.',
            'numeric' => 'The :field field must be a number.',
            'integer' => 'The :field field must be an integer.',
            'alpha' => 'The :field field may only contain letters.',
            'alpha_num' => 'The :field field may only contain letters and numbers.',
            'date' => 'The :field field is not a valid date.',
            'url' => 'The :field field is not a valid URL.',
            'in' => 'The selected :field is invalid.',
            'regex' => 'The :field field format is invalid.'
        ];

        $message = $messages[$rule] ?? 'The :field field is invalid.';

        return str_replace([':field', ':param'], [$field, $param], $message);
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getFirstError($field = null) {
        if ($field) {
            return $this->errors[$field][0] ?? null;
        }

        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0];
        }

        return null;
    }

    public function hasErrors() {
        return !empty($this->errors);
    }

    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }

        return Security::sanitizeInput($data);
    }

    public static function validateData($data, $rules) {
        $validator = new self($data);
        return $validator->validate($rules) ? null : $validator->getErrors();
    }
}
?>