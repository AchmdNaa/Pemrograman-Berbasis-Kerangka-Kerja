<?php $warna = ['baru' => 'primary', 'diproses' => 'info', 'dikirim' => 'warning', 'selesai' => 'success', 'batal' => 'danger']; ?>
<span class="badge bg-<?= $warna[$status] ?? 'secondary' ?>"><?= esc($status) ?></span>
