<?php
/**
 * Validator Class
 * Handles input validation and sanitization
 */

class Validator {
    private $errors = [];
    private $data = [];
    private $rules = [];

    public function __construct($data = []) {
        $this->data = $data;
    }

    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    public function setRules($rules) {
        $this->rules = $rules;
        return $this;
    }

    public function validate() {
        $this->errors = [];

        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);

            foreach ($rules as $rule) {
                $this->validateField($field, $rule);
            }
        }

        return empty($this->errors);
    }

    private function validateField($field, $rule) {
        $value = $this->getValue($field);

        // Parse rule parameters
        $ruleParts = explode(':', $rule);
        $ruleName = $ruleParts[0];
        $parameters = isset($ruleParts[1]) ? explode(',', $ruleParts[1]) : [];

        $method = 'validate' . ucfirst($ruleName);

        if (method_exists($this, $method)) {
            if (!$this->$method($field, $value, $parameters)) {
                $this->addError($field, $ruleName, $parameters);
            }
        }
    }

    private function getValue($field) {
        return $this->data[$field] ?? null;
    }

    private function validateRequired($field, $value, $parameters) {
        return !is_null($value) && $value !== '' && $value !== [];
    }

    private function validateEmail($field, $value, $parameters) {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function validateMin($field, $value, $parameters) {
        $min = $parameters[0] ?? 0;
        if (is_string($value)) {
            return strlen($value) >= $min;
        } elseif (is_numeric($value)) {
            return $value >= $min;
        }
        return false;
    }

    private function validateMax($field, $value, $parameters) {
        $max = $parameters[0] ?? 0;
        if (is_string($value)) {
            return strlen($value) <= $max;
        } elseif (is_numeric($value)) {
            return $value <= $max;
        }
        return false;
    }

    private function validateNumeric($field, $value, $parameters) {
        return is_numeric($value);
    }

    private function validateInteger($field, $value, $parameters) {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    private function validateAlpha($field, $value, $parameters) {
        return ctype_alpha($value);
    }

    private function validateAlphaNum($field, $value, $parameters) {
        return ctype_alnum($value);
    }

    private function validateDate($field, $value, $parameters) {
        $date = date_create($value);
        return $date !== false;
    }

    private function validateUrl($field, $value, $parameters) {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    private function validateIn($field, $value, $parameters) {
        return in_array($value, $parameters);
    }

    private function validateUnique($field, $value, $parameters) {
        if (empty($parameters)) return true;

        $table = $parameters[0];
        $column = $parameters[1] ?? $field;

        $db = Database::getInstance();
        $result = $db->query("SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?")
                    ->bind(1, $value)
                    ->single();

        return $result['count'] == 0;
    }

    private function validateExists($field, $value, $parameters) {
        if (empty($parameters)) return true;

        $table = $parameters[0];
        $column = $parameters[1] ?? $field;

        $db = Database::getInstance();
        $result = $db->query("SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?")
                    ->bind(1, $value)
                    ->single();

        return $result['count'] > 0;
    }

    private function validateMatches($field, $value, $parameters) {
        $otherField = $parameters[0] ?? '';
        $otherValue = $this->getValue($otherField);
        return $value === $otherValue;
    }

    private function addError($field, $rule, $parameters = []) {
        $message = $this->getErrorMessage($field, $rule, $parameters);
        $this->errors[$field][] = $message;
    }

    private function getErrorMessage($field, $rule, $parameters = []) {
        $messages = [
            'required' => 'The :field field is required.',
            'email' => 'The :field must be a valid email address.',
            'min' => 'The :field must be at least :param characters.',
            'max' => 'The :field may not be greater than :param characters.',
            'numeric' => 'The :field must be a number.',
            'integer' => 'The :field must be an integer.',
            'alpha' => 'The :field may only contain letters.',
            'alpha_num' => 'The :field may only contain letters and numbers.',
            'date' => 'The :field is not a valid date.',
            'url' => 'The :field format is invalid.',
            'in' => 'The selected :field is invalid.',
            'unique' => 'The :field has already been taken.',
            'exists' => 'The selected :field is invalid.',
            'matches' => 'The :field confirmation does not match.'
        ];

        $message = $messages[$rule] ?? 'The :field field is invalid.';

        // Replace placeholders
        $message = str_replace(':field', ucfirst(str_replace('_', ' ', $field)), $message);
        if (!empty($parameters)) {
            $message = str_replace(':param', $parameters[0], $message);
        }

        return $message;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getFirstError($field) {
        return $this->errors[$field][0] ?? null;
    }

    public function hasErrors() {
        return !empty($this->errors);
    }

    public function sanitize($data) {
        $security = Security::getInstance();

        if (is_array($data)) {
            return array_map([$security, 'sanitize'], $data);
        }

        return $security->sanitize($data);
    }

    public function getValidatedData() {
        $validated = [];
        foreach ($this->rules as $field => $rule) {
            if (isset($this->data[$field])) {
                $validated[$field] = $this->sanitize($this->data[$field]);
            }
        }
        return $validated;
    }
}
?>