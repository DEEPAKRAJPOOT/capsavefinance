<?php

$crifUAT = [
    'CRIF_URL' => env('CRIF_URL', 'https://test.crifhighmark.com/Inquiry/Inquiry/CPUAction.action'),   
    'CRIF_REQ_MBR' => env('CRIF_REQ_MBR', 'NBF0002966'),   
    'CRIF_MBR_ID' => env('CRIF_MBR_ID', 'BOROCTSAN005'),   
    'CRIF_SUB_MBR_ID' => env('CRIF_SUB_MBR_ID', 'CAPSAVE FINANCE PRIVATE LIMITED'),   
    'CRIF_USRID' => env('CRIF_USRID', 'crif1_cpu_uat@capsavefinance.com'),
    'CRIF_PASSCODE' => env('CRIF_PASSCODE', '55DE689372D33C9876D1E09CFFF8BBBFF74B9445'),
];

$crifPROD = [
    'CRIF_URL' => env('CRIF_URL', 'https://hub.crifhighmark.com/Inquiry/Inquiry/CPUAction.action'),   
    'CRIF_REQ_MBR' => env('CRIF_REQ_MBR', 'NBF0000834'),   
    'CRIF_MBR_ID' => env('CRIF_MBR_ID', 'BOROCTSAN005'),   
    'CRIF_SUB_MBR_ID' => env('CRIF_SUB_MBR_ID', 'CAPSAVE FINANCE PRIVATE LTD'),   
    'CRIF_USRID' => env('CRIF_USRID', 'crif1_cpu_prd@capsavefinance.com'),
    'CRIF_PASSCODE' => env('CRIF_PASSCODE', 'D261F46CFA7E1C0DB5A80FB02269668E1A3F05B9'),
];

$uatEnable = TRUE;

return ($uatEnable ? $crifUAT : $crifPROD);
