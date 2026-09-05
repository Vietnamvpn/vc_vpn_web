<?php

namespace VcApp\VcHelpers;

class Validator
{
    protected $errors = [];

    public function validate(array $data, array $rules)
    {
        foreach ($rules as $field => $ruleList) {
            $rulesArray = explode('|', $ruleList);
            $value = $data[$field] ?? null;

            foreach ($rulesArray as $rule) {
                if ($rule === 'required' && (empty($value) && $value !== '0')) {
                    $this->errors[$field] = "Trường {$field} là bắt buộc.";
                    break;
                }
                if ($rule === 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field] = "Trường {$field} không phải là email hợp lệ.";
                    break;
                }
                if (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if (!empty($value) && mb_strlen($value) < $min) {
                        $this->errors[$field] = "Trường {$field} phải có ít nhất {$min} ký tự.";
                        break;
                    }
                }
            }
        }
        return empty($this->errors);
    }

    public function errors()
    {
        return $this->errors;
    }
}