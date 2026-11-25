<?php

if (! function_exists('rupiah')) {
    function rupiah(int|float|null $value): string
    {
        return 'Rp' . number_format((float) $value, 0, ',', '.');
    }
}
