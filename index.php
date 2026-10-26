
<?php
$hasil = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member = $_POST["member"] ?? "tidak";
    $subtotal = 0;
    $totalDiskonBarang = 0;
    $daftarBarang = [];

    for ($i = 1; $i <= 2; $i++) {
        $nama = trim($_POST["nama$i"] ?? "");
        $harga = (float) ($_POST["harga$i"] ?? 0);
        $jumlah = (int) ($_POST["jumlah$i"] ?? 0);
        $diskon = (float) ($_POST["diskon$i"] ?? 0);

        // Validasi data barang
        if ($nama === "" || $harga < 0 || $jumlah < 0
            || $diskon < 0 || $diskon > 100) {
            die("Data barang tidak valid. Periksa kembali input.");
        }

        // Hitung harga barang sebelum dan sesudah diskon
        $totalAwal = $harga * $jumlah;
        $nilaiDiskon = $totalAwal * $diskon / 100;
        $totalSetelahDiskon = $totalAwal - $nilaiDiskon;

        $subtotal += $totalSetelahDiskon;
        $totalDiskonBarang += $nilaiDiskon;

        $daftarBarang[] = [
            "nama" => $nama,
            "harga" => $harga,
            "jumlah" => $jumlah,
            "diskon" => $diskon,
            "total_awal" => $totalAwal,
            "nilai_diskon" => $nilaiDiskon,
            "total" => $totalSetelahDiskon
        ];
    }

    // Diskon member tambahan 5% dari subtotal
    $diskonMember = ($member == "ya") ? 0.05 : 0;
    $nilaiDiskonMember = $subtotal * $diskonMember;
    $totalBayar = $subtotal - $nilaiDiskonMember;

    $hasil = [
        "member" => $member,
        "barang" => $daftarBarang,
        "subtotal" => $subtotal,
        "diskon_barang" => $totalDiskonBarang,
        "diskon_member" => $nilaiDiskonMember,
        "total_bayar" => $totalBayar
    ];
}

function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ",", ".");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Minimarket Online</title>
</head>
<body>
    <h2>Program Minimarket Online</h2>

    <form method="post">
        <label>Apakah pembeli member?</label>
        <select name="member">
            <option value="tidak">Tidak</option>
            <option value="ya">Ya</option>
        </select>

        <?php for ($i = 1; $i <= 2; $i++): ?>
            <h3>Barang <?= $i ?></h3>

            <label>Nama barang:</label><br>
            <input type="text" name="nama<?= $i ?>"
                   value="<?= $i == 1 ? 'Sabun Cuci Rinso' : 'Sikat Gigi Pepsodent' ?>"
                   required><br><br>

            <label>Harga satuan (Rp):</label><br>
            <input type="number" name="harga<?= $i ?>"
                   value="<?= $i == 1 ? 9000 : 7000 ?>"
                   min="0" required><br><br>

            <label>Jumlah dibeli:</label><br>
            <input type="number" name="jumlah<?= $i ?>"
                   value="<?= $i == 1 ? 3 : 2 ?>"
                   min="0" required><br><br>

            <label>Diskon barang (%):</label><br>
            <input type="number" name="diskon<?= $i ?>"
                   value="<?= $i == 1 ? 5 : 7 ?>"
                   min="0" max="100" step="0.01" required><br><br>
        <?php endfor; ?>

        <button type="submit">Hitung Total</button>
    </form>

    <?php if ($hasil !== null): ?>
        <hr>
        <h2>Hasil Perhitungan</h2>

        <p>Member: <?= $hasil["member"] == "ya" ? "Ya" : "Tidak" ?></p>

        <?php foreach ($hasil["barang"] as $barang): ?>
            <h3><?= htmlspecialchars($barang["nama"]) ?></h3>
            <p>Harga satuan: <?= rupiah($barang["harga"]) ?></p>
            <p>Jumlah: <?= $barang["jumlah"] ?></p>
            <p>Total sebelum diskon: <?= rupiah($barang["total_awal"]) ?></p>
            <p>Diskon barang: <?= $barang["diskon"] ?>%</p>
            <p>Nilai diskon: <?= rupiah($barang["nilai_diskon"]) ?></p>
            <p>Total setelah diskon: <?= rupiah($barang["total"]) ?></p>
        <?php endforeach; ?>

        <hr>
        <p>Subtotal setelah diskon barang:
            <?= rupiah($hasil["subtotal"]) ?></p>
        <p>Total diskon barang:
            <?= rupiah($hasil["diskon_barang"]) ?></p>
        <p>Diskon tambahan member:
            <?= rupiah($hasil["diskon_member"]) ?></p>
        <h3>Total yang harus dibayar:
            <?= rupiah($hasil["total_bayar"]) ?></h3>
    <?php endif; ?>
</body>
</html>
