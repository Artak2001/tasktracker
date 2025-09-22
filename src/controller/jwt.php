<?php
function b64url($d){ return rtrim(strtr(base64_encode($d), '+/', '-_'), '='); }
function b64urld($d){ $m=strlen($d)%4; if($m) $d.=str_repeat('=',4-$m); return base64_decode(strtr($d,'-_','+/')); }

function jwt_sign(array $payload, string $secret, int $ttl=604800): string {
    $header=['alg'=>'HS256','typ'=>'JWT'];
    $payload['iat']=time(); 
    $payload['exp']=time()+$ttl;
    $h=b64url(json_encode($header));
    $p=b64url(json_encode($payload));
    $s=b64url(hash_hmac('sha256', "$h.$p", $secret, true));
    return "$h.$p.$s";
}

function jwt_verify(string $jwt, string $secret): ?array {
    $parts=explode('.',$jwt);
    if(count($parts)!==3) return null;
    [$h,$p,$s]=$parts;
    $chk=b64url(hash_hmac('sha256', "$h.$p", $secret, true));
    if(!hash_equals($chk,$s)) return null;
    $payload=json_decode(b64urld($p)?:'', true);
    if(!$payload || ($payload['exp']??0)<time()) return null;
    return $payload;
}
