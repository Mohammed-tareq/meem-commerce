<?php

namespace App\Services\General;

use Marvel\Database\Models\Faqs;

class FaqService
{

    public function getfaqs()
    {
        return Faqs::active()->get();
    }
}
