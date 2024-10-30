<?php
function generate_verification_code(): int
{
    return rand(100000,999999);
}
