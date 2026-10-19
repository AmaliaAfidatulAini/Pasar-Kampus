<?php

function debug_wrapper(callable $callback, mixed ...$args): void
{
    $id = 'dbg_' . uniqid();
    ob_start();
    $callback(...$args);
    $out = htmlspecialchars(ob_get_clean(), ENT_QUOTES, 'UTF-8');

    echo '<style>.debug-pre{background:#1e1e1e;color:#d4d4d4;font-family:Consolas,monospace;font-size:13px;line-height:1.5;padding:16px;border-radius:8px;border:1px solid #333;overflow-x:auto;white-space:pre-wrap;word-wrap:break-word;margin:12px 0}.dbg-bracket{color:#fff}.dbg-key{color:#ce9178;font-weight:bold}.dbg-arrow{color:#f44747;font-weight:bold}.dbg-array{color:#4ec9b0;font-style:italic}.dbg-type{color:#f44747;font-style:italic}.dbg-num{color:#b5cea8}.dbg-str{color:#9cdcfe}.dbg-struct{color:#569cd6;font-weight:bold}</style>';
    echo '<pre id="' . $id . '" class="debug-pre">' . $out . '</pre>';
    echo '<script>(function(){var e=document.getElementById("' . $id . '");if(!e)return;var c=e.innerHTML;c=c.replace(/(\[)([^\]]+)(\])\s*(&gt;|=&gt;|=>)/g,\'<span class="dbg-bracket">[</span><span class="dbg-key">$2</span><span class="dbg-bracket">]</span> <span class="dbg-arrow">=&gt;</span>\').replace(/\barray\b(?=\s*\()/gi,\'<span class="dbg-array">array</span>\').replace(/\b(string|int|float|double|bool|object|resource|NULL|null)\b(?=\s*\()/gi,\'<span class="dbg-type">$1</span>\').replace(/(\()(\d+)(\))/g,\'$1<span class="dbg-num">$2</span>$3\').replace(/&quot;(.*?)&quot;/g,\'&quot;<span class="dbg-str">$1</span>&quot;\').replace(/\b(Array|Object)\b(?=\s*\n|\s*\()/g,\'<span class="dbg-struct">$1</span>\');e.innerHTML=c;})();</script>';
}

function d(mixed $data): void
{
    debug_wrapper("print_r", $data);
}

function dd(mixed $data): void
{
    debug_wrapper("print_r", $data);
    die;
}

function v(mixed $data): void
{
    debug_wrapper("var_dump", $data);
}
function vd(mixed $data): void
{
    debug_wrapper("var_dump", $data);
    die;
}
