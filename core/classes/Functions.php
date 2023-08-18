<?php

namespace core\classes;

class Functions
{

    public function VerifyEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }

}

