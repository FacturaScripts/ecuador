<?php
class validador {

    public function valida_id($id) {
        if (strlen($id) == 13) {
            $type = substr($id, 2, 1);
            $last_digits = substr($id, 10, 3);
            if ($last_digits != "001")
                return FALSE;
            if ($type === '9') {
                return $this->validatePrivateCompanyId($id);
            } elseif ($type === '6') {
                return $this->validatePublicCompanyId($id);
            } else {
                return $this->validatePersonId($id);
            }
        } else {
            if (strlen($id) == 10) {
                return $this->validatePersonId($id);
            } else {
                return FALSE;
            }
        }
    }

    private function validatePrivateCompanyId($id) {
        $coefficients = array(4, 3, 2, 7, 6, 5, 4, 3, 2);
        $chars_id = str_split($id);
        $s = 0;
        for ($index = 0; $index < count($coefficients); $index++) {
			$r = (int)$chars_id[$index] * $coefficients[$index];
			$s = $s + $r;
        }
        $r = $s % 11;
        if ($r > 0)
            $r = 11 - $r;
        if ($r == (int)$chars_id[9])
            return TRUE;
        else
            return FALSE;
    }

    private function validatePublicCompanyId($id) {
        $coefficients = array(3, 2, 7, 6, 5, 4, 3, 2);
        $chars_id = str_split($id);
        $s = 0;
        for ($index = 0; $index < count($coefficients); $index++) {
            $r = (int)$chars_id[$index] * $coefficients[$index];
            $s = $s + $r;
        }
        $r = $s % 11;
        if ($r > 0)
            $r = 11 - $r;
        if ($r == (int)$chars_id[8])
            return TRUE;
        else
            return FALSE;
    }

    private function validatePersonId($id) {
        $coefficients = array(2, 1, 2, 1, 2, 1, 2, 1, 2);
        $chars_id = str_split($id);
        $s = 0;
        for ($index = 0; $index < count($coefficients); $index++) {
            $r = (int)$chars_id[$index] * $coefficients[$index];
            if ($r > 9)
                $s = $s + ($r - 9);
            else
                $s = $s + $r;
        }
        $r = $s % 10;
        if ($r > 0)
            $r = 10 - $r;
        if ($r == (int)$chars_id[9])
            return TRUE;
        else
            return FALSE;
    }
}
