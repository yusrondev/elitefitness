<?php

require_once base_path('vendor/setasign/fpdf/fpdf.php');
require_once base_path('vendor/setasign/fpdi/src/autoload.php');

use setasign\Fpdi\Fpdi;

class Pdf extends Fpdi
{
    public function __construct()
    {
        parent::__construct();
    }
}
