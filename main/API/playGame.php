<?php

include "../../function/connect.php";
include "config.php";
include "functions.php";

$id = $_GET['id'];

$v = mysqli_query($koneksi, "SELECT * FROM `games` WHERE id = '$id'") or die(mysqli_error($koneksi));
$user_datad = mysqli_fetch_array($v, MYSQLI_ASSOC);

$getGamesid = $user_datad['game_code'];
$getSession = $_GET['p'];

$q = mysqli_query($koneksi, "SELECT * FROM `tb_user` WHERE extplayer = '$getSession'") or die(mysqli_error($koneksi));
$user_data = mysqli_fetch_array($q, MYSQLI_ASSOC);
$extplayer = $user_data['extplayer'];
$usersID = $user_data['extplayer'];

$cekUser = getUserBalance($extplayer);
$datas = json_decode($cekUser, true);

if ($datas === null) {
    echo "Failed to decode JSON string";
} else {
    $status = $datas['status'];
    $playerLogin = $datas['content']['playerLogin'];

    if ($status === 'success') {
        $sql_3 = mysqli_query($koneksi, "SELECT * FROM `tb_trxgame` ORDER BY id DESC LIMIT 1") or die(mysqli_error($koneksi));
        $s33 = mysqli_num_rows($sql_3);
        $unikID = ($s33 == 0) ? 0 : mysqli_fetch_array($sql_3)['id'];

        $no_invoice = 'INV/' . date('y/m/s') . $unikID;
        $unik = date('Hs');
        $kode_unik = substr(str_shuffle(1234567890), 0, 3);
        $orderid = $kode_unik . date('dis');
        $created_date = date('Y-m-d H:i:s');

        $validation = openGame($getSession, $getGamesid);

        if ($validation && isset($validation['url'])) {
            $url = $validation['url'];
            
            // Jika berhasil membuka game, transfer saldo ke dalam game
            $getBalance = mysqli_query($koneksi, "SELECT * FROM `tb_saldo` WHERE id_user = '$usersID'") or die(mysqli_error($koneksi));
            $userbalance = mysqli_fetch_array($getBalance, MYSQLI_ASSOC);
            $balances = $userbalance['active'];

            if ($balances > 0) {
                $pushCash = $balances;
                $addBalanceAPI = transactionIN($extplayer, $pushCash);
                $decode = json_decode($addBalanceAPI, true);

                if ($decode['status'] == "success") {
                    $insert_transaksi = mysqli_query($koneksi, "INSERT INTO `tb_trxgame` 
                    (`kd_transaksi`, `date`, `transaksi`, `total`, `saldo`, `note`, `gameid`, `provider`, `id_user`,`status`) VALUES ('$orderid','$created_date','Transaction IN Wallet Provider','$pushCash','$getGamesid','$getSession','Success')");

                    $getBalanceUser = mysqli_query($koneksi, "UPDATE tb_saldo SET active='0' WHERE id_user='$usersID'");
                    
                    header('Location: ' . $url);
                    exit(); // Pastikan tidak ada eksekusi kode lebih lanjut setelah redirect
                } else {
                    // Jika gagal transfer saldo, kembalikan ke index.php
                    header('Location:../../index.php?page=slot');
                }
            } else {
                header('Location: ' . $url);
                exit(); // Pastikan tidak ada eksekusi kode lebih lanjut setelah redirect
            }
        } else {
            // Jika gagal membuka game, kembalikan ke index.php
           header('Location:../../index.php?page=slot');
        }
    } else {
        header('Location:../../index.php?page=slot');
    }
}
ob_end_flush();
?>
