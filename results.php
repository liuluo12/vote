<?php
header('Content-Type: text/html; charset=utf-8');
$file = __DIR__ . '/votes.json';
$votes = json_decode(@file_get_contents($file), true) ?: [];
$c = array_count_values($votes);
arsort($c);
$total = count($votes);
echo "<!doctype html><meta charset=utf-8><meta name=viewport content='width=device-width'><title>投票结果</title>";
echo "<style>body{font:15px/1.6 -apple-system,'PingFang SC',sans-serif;margin:0;padding:18px;background:#f2f4f8;color:#16202e}";
echo "h1{font-size:19px;margin:4px 0 12px}.row{background:#fff;border-radius:12px;padding:10px 13px;margin-bottom:8px}";
echo ".l{display:flex;justify-content:space-between}.n{font-weight:600}.v{color:#5b6b84;font-size:13px}";
echo ".b{height:7px;background:#eef1f7;border-radius:5px;margin-top:6px;overflow:hidden}";
echo ".b i{display:block;height:100%;background:linear-gradient(90deg,#2f6bff,#00c2a8)}";
echo ".e{text-align:center;color:#93a1b7;padding:26px}</style>";
echo "<h1>投票结果 · 共 " . $total . " 票 / 59 人</h1>";
if (!$c) { echo "<div class=e>还没有人投票</div>"; exit; }
$max = max($c);
foreach ($c as $n => $v) {
  $pct = $total ? number_format($v / $total * 100, 1) : '0.0';
  echo "<div class=row><div class=l><span class=n>" . htmlspecialchars($n) . "</span><span class=v>{$v} 票 · {$pct}%</span></div>";
  echo "<div class=b><i style='width:" . round($v / $max * 100) . "%'></i></div></div>";
}
