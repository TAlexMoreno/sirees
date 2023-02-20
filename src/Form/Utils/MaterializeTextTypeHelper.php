<?php

namespace App\Form\Utils;

class MaterializeTextTypeHelper {
    public function __construct(private String $helperText, private String $errorText = "", private String $successText = "")
    {
        return;
    }
    public function getHelperText(): String
    {
        return $this->helperText;
    }
    public function getErrorText(): String
    {
        return $this->errorText;
    }
    public function getSuccessText(): String
    {
        return $this->successText;
    }
}