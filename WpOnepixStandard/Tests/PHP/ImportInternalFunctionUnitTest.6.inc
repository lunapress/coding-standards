<?php

namespace Test;

use WP_Screen;

use function functionName;

class Screen {
    public static function test(): void {
        $screen = get_current_screen();
    }

    public function get_expected_delivery_date2()
    {
        $this->get_expected_delivery_date()?->format( 'Y-m-d' );
    }

    public function get_expected_delivery_date()
    {

    }
}
