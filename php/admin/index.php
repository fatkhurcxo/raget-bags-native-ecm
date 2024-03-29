<?php
session_start();
require '../function-final.php';

if (isset($_POST["logoutAdmin"])) {
    # code...   
    session_destroy();
    echo "<script> alert('Anda keluar!');
                    document.location.href = '../user-login.php';
                        window.history.replaceState( null, null, window.location.href );
            </script>";
    exit();
}

// MENGAMBIL SELURUH JUMLAH PESANAN
$jumlahPesanan = getAllTabelData("SELECT * FROM pesanan");

// JUMLAH PRODUK
$jumlahProduk = getAllTabelData("SELECT * FROM produk");

// PESANAN DIPROSES
$pesananDalamProses = getAllTabelData("SELECT * FROM pesanan WHERE status_barang='Belum Diterima'");

// 5 DATA PESANAN TERBARU
$_5pesananTerbaru = showData("SELECT id_pesanan, nama_penerima, no_pesanan, status, bukti, FORMAT(total_pesanan, 2), tgl_pemesanan
                                FROM pesanan
                                    ORDER BY tgl_pemesanan
                                        DESC LIMIT 5");

// 5 DATA PRODUK TERBARU
$_5produkTerbaru = showData("SELECT id_produk, nama, FORMAT(harga, 2) FROM produk ORDER BY id_produk DESC LIMIT 5");

// URL GET
if (isset($_GET["id_pesanan"])) {
    # code...
    $id_pesanan = $_GET["id_pesanan"];
    $pesananTerbaru = showData("SELECT * FROM pesanan WHERE id_pesanan=$id_pesanan");
    $pesananDetail = showDataTable("SELECT * FROM pesanan WHERE id_pesanan=$id_pesanan");
    $ordered = showData("SELECT id_ordered, nama_produk, hero_img, FORMAT(total, 2), jumlah, varian, harga, id_akun, id_produk, id_pesanan
                            FROM ordered
                                WHERE id_pesanan=$id_pesanan");

    $subtotal = showDataTable("SELECT FORMAT(SUM(total), 2) Subtotal
                                FROM ordered
                                    WHERE id_pesanan=$id_pesanan");
    $totalPesan = showDataTable("SELECT FORMAT(total_pesanan, 2) FROM pesanan WHERE id_pesanan=$id_pesanan");

    // 
} elseif (isset($_GET["id_produk"])) {
    # code...
    $id_produk = $_GET["id_produk"];
    $produkTerbaru = showDataTable("SELECT id_produk, nama, FORMAT(harga, 2) FROM produk WHERE id_produk=$id_produk");
    $produkVarianTerbaru = showDataTable("SELECT * FROM produk_varian WHERE id_produk=$id_produk");
    $imgProduk = showDataTable("SELECT * FROM produk_image WHERE id_produk=$id_produk");
    // 
} else {
    # code...
    echo "Tidak ada ID pesanan di URL";
}

// DELETE PRODUK
if (isset($_GET["delete"])) {
    # code...
    $delete_produk = $_GET["delete"];
    mysqli_query($dconn, "DELETE FROM produk WHERE id_produk=$delete_produk");
} else {
    # code...
    echo "Tidak ada barang yang diinginkan untuk dihapus";
}

// DETAIL PESANAN

// if ($_GET["id"] != NULL) {
//     # code...
//     echo $_GET["id"];
//     $id_terpilih = $_GET["id"];
//     $hasil = mysqli_fetch_assoc(mysqli_query($database, "SELECT * FROM produk WHERE id_produk=$id_terpilih"));
// }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>

    <!-- LINK BOOTSTRAP 5.2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body class="bg-light">

    <!-- MODAL ADD PRODUK -->
    <div class="modal fade" id="modalDetail" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="modal-body">
                    <div class="row row-cols-1">
                        <div class="col border-bottom container-fluid">
                            <div class="float-start">
                                <h6 class="fw-bold">Tambah Produk</h6 class="fw-bold">
                            </div>
                            <div class="float-end">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="col mt-2 container-fluid">
                            <form action="tambah-barang/" method="post">
                                <div class="mb-3">
                                    <label for="produk" class="form-label">Nama Produk</label>
                                    <input type="text" class="form-control" id="produk" autocomplete="off">
                                </div>
                                <div class="mb-3">
                                    <label for="produkHarga" class="form-label">Harga Produk</label>
                                    <input type="text" class="form-control" id="produkHarga" autocomplete="off">
                                </div>
                                <div>
                                    <button type="submit" name="tambahProduk" class="container pt-1 pb-1">Lanjut</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- OFFCANVAS -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header text-white" style="background-color: #453C41;">
            <svg width="169" height="50" viewBox="0 0 169 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15 5C15 2.23858 17.2386 0 20 0H45V50H20C17.2386 50 15 47.7614 15 45V5Z" fill="#7895B2" />
                <path d="M27.9219 20.5586V27.2852H31.2852C31.7461 27.2852 32.1797 27.1992 32.5859 27.0273C32.9922 26.8477 33.3477 26.6055 33.6523 26.3008C33.957 25.9961 34.1953 25.6406 34.3672 25.2344C34.5469 24.8203 34.6367 24.3828 34.6367 23.9219C34.6367 23.4609 34.5469 23.0273 34.3672 22.6211C34.1953 22.207 33.957 21.8477 33.6523 21.543C33.3477 21.2383 32.9922 21 32.5859 20.8281C32.1797 20.6484 31.7461 20.5586 31.2852 20.5586H27.9219ZM27.9219 34H24.5586V17.1953H31.2852C31.9023 17.1953 32.4961 17.2773 33.0664 17.4414C33.6367 17.5977 34.168 17.8242 34.6602 18.1211C35.1602 18.4102 35.6133 18.7617 36.0195 19.1758C36.4336 19.582 36.7852 20.0352 37.0742 20.5352C37.3711 21.0352 37.5977 21.5703 37.7539 22.1406C37.918 22.7109 38 23.3047 38 23.9219C38 24.5 37.9258 25.0625 37.7773 25.6094C37.6367 26.1562 37.4297 26.6758 37.1562 27.168C36.8906 27.6602 36.5625 28.1133 36.1719 28.5273C35.7812 28.9414 35.3438 29.3008 34.8594 29.6055L36.7227 34H33.1484L31.6836 30.6133L27.9219 30.6367V34ZM50.1055 27.2852V23.9219C50.1055 23.4609 50.0156 23.0273 49.8359 22.6211C49.6641 22.207 49.4258 21.8477 49.1211 21.543C48.8164 21.2383 48.457 21 48.043 20.8281C47.6367 20.6484 47.2031 20.5586 46.7422 20.5586C46.2812 20.5586 45.8438 20.6484 45.4297 20.8281C45.0234 21 44.668 21.2383 44.3633 21.543C44.0586 21.8477 43.8164 22.207 43.6367 22.6211C43.4648 23.0273 43.3789 23.4609 43.3789 23.9219V27.2852H50.1055ZM53.4688 34H50.1055V30.6367H43.3789V34H40.0273V23.9219C40.0273 22.9922 40.2031 22.1211 40.5547 21.3086C40.9062 20.4883 41.3828 19.7734 41.9844 19.1641C42.5938 18.5547 43.3047 18.0742 44.1172 17.7227C44.9375 17.3711 45.8125 17.1953 46.7422 17.1953C47.6719 17.1953 48.543 17.3711 49.3555 17.7227C50.1758 18.0742 50.8906 18.5547 51.5 19.1641C52.1094 19.7734 52.5898 20.4883 52.9414 21.3086C53.293 22.1211 53.4688 22.9922 53.4688 23.9219V34ZM69.7578 32.3008C68.9766 32.9648 68.1055 33.4766 67.1445 33.8359C66.1836 34.1875 65.1836 34.3633 64.1445 34.3633C63.3477 34.3633 62.5781 34.2578 61.8359 34.0469C61.1016 33.8438 60.4141 33.5547 59.7734 33.1797C59.1328 32.7969 58.5469 32.3438 58.0156 31.8203C57.4844 31.2891 57.0312 30.7031 56.6562 30.0625C56.2812 29.4141 55.9883 28.7188 55.7773 27.9766C55.5742 27.2344 55.4727 26.4648 55.4727 25.668C55.4727 24.8711 55.5742 24.1055 55.7773 23.3711C55.9883 22.6367 56.2812 21.9492 56.6562 21.3086C57.0312 20.6602 57.4844 20.0742 58.0156 19.5508C58.5469 19.0195 59.1328 18.5664 59.7734 18.1914C60.4141 17.8164 61.1016 17.5273 61.8359 17.3242C62.5781 17.1133 63.3477 17.0078 64.1445 17.0078C65.1836 17.0078 66.1836 17.1875 67.1445 17.5469C68.1055 17.8984 68.9766 18.4062 69.7578 19.0703L68 22C67.4922 21.4844 66.9062 21.082 66.2422 20.793C65.5781 20.4961 64.8789 20.3477 64.1445 20.3477C63.4102 20.3477 62.7188 20.4883 62.0703 20.7695C61.4297 21.0508 60.8672 21.4336 60.3828 21.918C59.8984 22.3945 59.5156 22.957 59.2344 23.6055C58.9531 24.2461 58.8125 24.9336 58.8125 25.668C58.8125 26.4102 58.9531 27.1055 59.2344 27.7539C59.5156 28.4023 59.8984 28.9688 60.3828 29.4531C60.8672 29.9375 61.4297 30.3203 62.0703 30.6016C62.7188 30.8828 63.4102 31.0234 64.1445 31.0234C64.5664 31.0234 64.9766 30.9727 65.375 30.8711C65.7734 30.7695 66.1523 30.6289 66.5117 30.4492V25.668H69.7578V32.3008ZM83.8672 34H72.3477V17.1953H83.8672V20.5586H75.7109V23.9219H81.2305V27.2852H75.7109V30.6367H83.8672V34ZM93.2773 34H89.9258V20.5586H84.875V17.1953H98.3164V20.5586H93.2773V34ZM120.266 28.9609C120.266 29.6562 120.133 30.3086 119.867 30.918C119.602 31.5273 119.238 32.0625 118.777 32.5234C118.324 32.9766 117.793 33.3359 117.184 33.6016C116.574 33.8672 115.922 34 115.227 34H106.824V17.1953H115.227C115.922 17.1953 116.574 17.3281 117.184 17.5938C117.793 17.8594 118.324 18.2227 118.777 18.6836C119.238 19.1367 119.602 19.668 119.867 20.2773C120.133 20.8867 120.266 21.5391 120.266 22.2344C120.266 22.5469 120.223 22.8672 120.137 23.1953C120.051 23.5234 119.93 23.8398 119.773 24.1445C119.617 24.4492 119.43 24.7266 119.211 24.9766C118.992 25.2266 118.75 25.4336 118.484 25.5977C118.758 25.7461 119.004 25.9492 119.223 26.207C119.441 26.457 119.629 26.7344 119.785 27.0391C119.941 27.3438 120.059 27.6641 120.137 28C120.223 28.3281 120.266 28.6484 120.266 28.9609ZM110.188 30.6367H115.227C115.461 30.6367 115.68 30.5938 115.883 30.5078C116.086 30.4219 116.262 30.3047 116.41 30.1562C116.566 30 116.688 29.8203 116.773 29.6172C116.859 29.4141 116.902 29.1953 116.902 28.9609C116.902 28.7266 116.859 28.5078 116.773 28.3047C116.688 28.1016 116.566 27.9258 116.41 27.7773C116.262 27.6211 116.086 27.5 115.883 27.4141C115.68 27.3281 115.461 27.2852 115.227 27.2852H110.188V30.6367ZM110.188 23.9219H115.227C115.461 23.9219 115.68 23.8789 115.883 23.793C116.086 23.707 116.262 23.5898 116.41 23.4414C116.566 23.2852 116.688 23.1055 116.773 22.9023C116.859 22.6914 116.902 22.4688 116.902 22.2344C116.902 22 116.859 21.7812 116.773 21.5781C116.688 21.375 116.566 21.1992 116.41 21.0508C116.262 20.8945 116.086 20.7734 115.883 20.6875C115.68 20.6016 115.461 20.5586 115.227 20.5586H110.188V23.9219ZM132.699 27.2852V23.9219C132.699 23.4609 132.609 23.0273 132.43 22.6211C132.258 22.207 132.02 21.8477 131.715 21.543C131.41 21.2383 131.051 21 130.637 20.8281C130.23 20.6484 129.797 20.5586 129.336 20.5586C128.875 20.5586 128.438 20.6484 128.023 20.8281C127.617 21 127.262 21.2383 126.957 21.543C126.652 21.8477 126.41 22.207 126.23 22.6211C126.059 23.0273 125.973 23.4609 125.973 23.9219V27.2852H132.699ZM136.062 34H132.699V30.6367H125.973V34H122.621V23.9219C122.621 22.9922 122.797 22.1211 123.148 21.3086C123.5 20.4883 123.977 19.7734 124.578 19.1641C125.188 18.5547 125.898 18.0742 126.711 17.7227C127.531 17.3711 128.406 17.1953 129.336 17.1953C130.266 17.1953 131.137 17.3711 131.949 17.7227C132.77 18.0742 133.484 18.5547 134.094 19.1641C134.703 19.7734 135.184 20.4883 135.535 21.3086C135.887 22.1211 136.062 22.9922 136.062 23.9219V34ZM152.352 32.3008C151.57 32.9648 150.699 33.4766 149.738 33.8359C148.777 34.1875 147.777 34.3633 146.738 34.3633C145.941 34.3633 145.172 34.2578 144.43 34.0469C143.695 33.8438 143.008 33.5547 142.367 33.1797C141.727 32.7969 141.141 32.3438 140.609 31.8203C140.078 31.2891 139.625 30.7031 139.25 30.0625C138.875 29.4141 138.582 28.7188 138.371 27.9766C138.168 27.2344 138.066 26.4648 138.066 25.668C138.066 24.8711 138.168 24.1055 138.371 23.3711C138.582 22.6367 138.875 21.9492 139.25 21.3086C139.625 20.6602 140.078 20.0742 140.609 19.5508C141.141 19.0195 141.727 18.5664 142.367 18.1914C143.008 17.8164 143.695 17.5273 144.43 17.3242C145.172 17.1133 145.941 17.0078 146.738 17.0078C147.777 17.0078 148.777 17.1875 149.738 17.5469C150.699 17.8984 151.57 18.4062 152.352 19.0703L150.594 22C150.086 21.4844 149.5 21.082 148.836 20.793C148.172 20.4961 147.473 20.3477 146.738 20.3477C146.004 20.3477 145.312 20.4883 144.664 20.7695C144.023 21.0508 143.461 21.4336 142.977 21.918C142.492 22.3945 142.109 22.957 141.828 23.6055C141.547 24.2461 141.406 24.9336 141.406 25.668C141.406 26.4102 141.547 27.1055 141.828 27.7539C142.109 28.4023 142.492 28.9688 142.977 29.4531C143.461 29.9375 144.023 30.3203 144.664 30.6016C145.312 30.8828 146.004 31.0234 146.738 31.0234C147.16 31.0234 147.57 30.9727 147.969 30.8711C148.367 30.7695 148.746 30.6289 149.105 30.4492V25.668H152.352V32.3008ZM153.957 22.2344C153.957 21.5391 154.09 20.8867 154.355 20.2773C154.621 19.668 154.98 19.1367 155.434 18.6836C155.895 18.2227 156.43 17.8594 157.039 17.5938C157.648 17.3281 158.301 17.1953 158.996 17.1953H166.707V20.5586H158.996C158.762 20.5586 158.543 20.6016 158.34 20.6875C158.137 20.7734 157.957 20.8945 157.801 21.0508C157.652 21.1992 157.535 21.375 157.449 21.5781C157.363 21.7812 157.32 22 157.32 22.2344C157.32 22.4688 157.363 22.6914 157.449 22.9023C157.535 23.1055 157.652 23.2852 157.801 23.4414C157.957 23.5898 158.137 23.707 158.34 23.793C158.543 23.8789 158.762 23.9219 158.996 23.9219H162.359C163.055 23.9219 163.707 24.0547 164.316 24.3203C164.934 24.5781 165.469 24.9375 165.922 25.3984C166.383 25.8516 166.742 26.3867 167 27.0039C167.266 27.6133 167.398 28.2656 167.398 28.9609C167.398 29.6562 167.266 30.3086 167 30.918C166.742 31.5273 166.383 32.0625 165.922 32.5234C165.469 32.9766 164.934 33.3359 164.316 33.6016C163.707 33.8672 163.055 34 162.359 34H154.895V30.6367H162.359C162.594 30.6367 162.812 30.5938 163.016 30.5078C163.219 30.4219 163.395 30.3047 163.543 30.1562C163.699 30 163.82 29.8203 163.906 29.6172C163.992 29.4141 164.035 29.1953 164.035 28.9609C164.035 28.7266 163.992 28.5078 163.906 28.3047C163.82 28.1016 163.699 27.9258 163.543 27.7773C163.395 27.6211 163.219 27.5 163.016 27.4141C162.812 27.3281 162.594 27.2852 162.359 27.2852H158.996C158.301 27.2852 157.648 27.1523 157.039 26.8867C156.43 26.6211 155.895 26.2617 155.434 25.8086C154.98 25.3477 154.621 24.8125 154.355 24.2031C154.09 23.5859 153.957 22.9297 153.957 22.2344Z" fill="white" />
                <rect y="10" width="5" height="30" fill="#7895B2" />
                <path d="M0 10C0 7.79086 1.79086 6 4 6H13V10H0Z" fill="#7895B2" />
                <path d="M0 40C0 42.2091 1.79086 44 4 44H13V40H0Z" fill="#7895B2" />
                <circle cx="20.5" cy="24.5" r="2.5" fill="#127681" />
                <circle cx="20.5" cy="24.5" r="1.5" fill="white" />
            </svg>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="row row-cols-1">
                <a href="" class="text-decoration-none text-dark">
                    <div class="col pt-2 pb-2 border-bottom ps-3">
                        <div class="row row-cols-2 align-items-center">
                            <div class="col-2">
                                <img src="img-admin/home.png" alt="">
                            </div>
                            <div class="col pt-2">
                                <h5>Home</h6>
                            </div>
                        </div>
                    </div>
                </a>
                <a href="kelola-pesanan/" class="text-decoration-none text-dark">
                    <div class="col pt-2 pb-2 mt-2 border-bottom ps-3">
                        <div class="row row-cols-2 align-items-center">
                            <div class="col-2">
                                <img src="img-admin/inventory.png" alt="">
                            </div>
                            <div class="col pt-2">
                                <h5>Kelola Pesanan</h6>
                            </div>
                        </div>
                    </div>
                </a>
                <a href="barang/" class="text-decoration-none text-dark">
                    <div class="col pt-2 pb-2 mt-2 border-bottom ps-3">
                        <div class="row row-cols-2 align-items-center">
                            <div class="col-2">
                                <img src="img-admin/product.png" alt="">
                            </div>
                            <div class="col pt-2">
                                <h5>Produk</h6>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- NAVIGASI -->
    <nav class="navbar navbar-expand container-fluid text-white" style="background-color: #453C41;">
        <div class="navigasi d-flex flex-fill justify-content-between align-items-center ps-5 pe-5">
            <div class="logo-trigger">
                <button class="btn border-white btn-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                    <img src="img-admin/menu.png" alt="" width="30px" height="30px">
                </button>
            </div>
            <div class="">

            </div>
            <div class="admin-profile d-flex align-items-center">
                <div class="admin-name ms-2">
                    <div class="dropdown dropstart">
                        <button class="btn border-0 text-white dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Fatkhur Rozak
                        </button>
                        <ul class="dropdown-menu rounded-0">
                            <li><button class="dropdown-item" type="button">Profile</button></li>
                            <form action="" method="POST">
                                <li><button class="dropdown-item text-danger" type="submit" name="logoutAdmin">Log Out</button></li>
                            </form>
                        </ul>
                    </div>
                </div>
                <div class="profile-place bg-success rounded-circle border" style="width: 30px;height: 30px;">

                </div>
            </div>
        </div>
    </nav>

    <section class="section-1">
        <div class="container-fluid bg-light">
            <div class="ms-5 ps-5 row row-cols-1 me-5 pe-5 mt-3 rounded pt-1">
                <div class="col border pt-3 pb-2" style="background-color: #D4DBE2;">
                    <h5 style="font-weight: 400;">Informasi Utama</h3>
                </div>
                <div class="col border mt-3">
                    <div class="row row-cols-3 text-center">
                        <div class="col">
                            <div class="row row-cols-1 mt-3 mb-3 ms-3 me-3 text-white pt-3 pb-2" style="background-color: #7B7C81;">
                                <div class="col">
                                    <span>Banyak Produk</span>
                                </div>
                                <div class="col">
                                    <span style="font-size: 42px;font-weight: 600;"><?= $jumlahProduk; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row row-cols-1 mt-3 mb-3 ms-3 me-3 text-white pt-3 pb-2" style="background-color: #7B7C81;">
                                <div class="col">
                                    <span>Total Pesanan</span>
                                </div>
                                <div class="col">
                                    <span style="font-size: 42px;font-weight: 600;"><?= $jumlahPesanan; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row row-cols-1 mt-3 mb-3 ms-3 me-3 text-white pt-3 pb-2" style="background-color: #7B7C81;">
                                <div class="col">
                                    <span>Pesanan Dalam Proses</span>
                                </div>
                                <div class="col">
                                    <span style="font-size: 42px;font-weight: 600;"><?= $pesananDalamProses; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="overview-pesanan">
        <div class="row row-cols-1 ms-5 ps-5 pe-5 me-5">
            <div class="col text-center pt-3 pb-2">
                <h4>Overview Pesanan</h4>
            </div>
            <div class="col">
                <div class="row me-2 ms-2">
                    <div class="col">
                        <div class="row container row-cols-1 border bg-white border-1 rounded">
                            <div class="col border-bottom pb-2 pt-2">
                                <span style="font-weight: 500;">Daftar Pesanan</span></span>
                            </div>
                            <div class="col">
                                <?php
                                if (is_array(@$ordered)) {
                                    # code...
                                    foreach ($ordered as $listOrder) {
                                        # code...
                                        $total = $listOrder["FORMAT(total, 2)"];
                                        echo "<div class='row border-bottom pt-2 pb-3'>
                                    <div class='col-2 bg-light d-flex justify-content-center'>
                                        <img class='' src='../../img-assets/product/$listOrder[hero_img]' alt='' height='110px'>
                                    </div>
                                    <div class='col'>
                                        <div class='row row-cols-1 align-items-center'>
                                            <div class='col'>
                                                <h5>$listOrder[nama_produk]</h5>
                                            </div>
                                            <div class='col'>
                                                <span>Varian: $listOrder[varian]</span>
                                            </div>
                                            <div class='col mt-4'>
                                                <div class='float-start'>
                                                    <h6>$total</h6>
                                                </div>
                                                <div class='float-end'>
                                                    x $listOrder[jumlah]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>";
                                    }
                                }
                                ?>

                            </div>
                            <div class="col border-bottom pb-2 pt-2">
                                <div class="row row-cols-1" style="font-weight: 500;">
                                    <div class="col">
                                        <div class="float-start">
                                            <span>Subtotal</span>
                                        </div>
                                        <div class="float-end">
                                            Rp<?= @$subtotal["Subtotal"]; ?>
                                        </div>
                                    </div>
                                    <div class="col mt-2">
                                        <div class="float-start">
                                            <span>Biaya pengiriman</span>
                                        </div>
                                        <div class="float-end">
                                            COD (Dibayar saat barang tiba)
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col border-bottom pb-3 pt-3">
                                <div class="float-start">
                                    <h4>Total</h4>
                                </div>
                                <div class="float-end">
                                    <h4>Rp<?= @$totalPesan["FORMAT(total_pesanan, 2)"]; ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- LIST PESANAN -->
                    <div class="col ms-4">
                        <div class="row row-cols-1 border rounded">
                            <div class="col">
                                <h5>Kode Pesanan : <?= @$pesananDetail["no_pesanan"]; ?></h5>
                            </div>
                            <div class="col">
                                <div class="row row-cols-2">
                                    <div class="col">
                                        <span>Nama Penerima</span>
                                    </div>
                                    <div class="col">
                                        <h6><?= @$pesananDetail["nama_penerima"]; ?></h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="row row-cols-2">
                                    <div class="col">
                                        <span>Nomor Handphone</span>
                                    </div>
                                    <div class="col">
                                        <h6><?= @$pesananDetail["nomor_hp"]; ?></h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="row row-cols-2">
                                    <div class="col">
                                        <span>Catatan</span>
                                    </div>
                                    <div class="col">
                                        <p style="font-weight: 500;">"<?= @$pesananDetail["catatan"]; ?>"</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="row row-cols-2">
                                    <div class="col">
                                        <span>Alamat Pengiriman</span>
                                    </div>
                                    <div class="col">
                                        <p style="font-weight: 500;"><?= @$pesananDetail["alamat"]; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="row row-cols-2">
                                    <div class="col">
                                        <span>Jasa Pengiriman</span>
                                    </div>
                                    <div class="col">
                                        <p style="font-weight: 500;"><?= @$pesananDetail["jasa_pengiriman"]; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="row row-cols-2">
                                    <div class="col">
                                        <span>Status Pembayaran</span>
                                    </div>
                                    <div class="col">
                                        <p style="font-weight: 500;"><?= @$pesananDetail["status"]; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="row row-cols-2">
                                    <div class="col">
                                        <span>Status Barang</span>
                                    </div>
                                    <div class="col">
                                        <p style="font-weight: 500;"><?= @$pesananDetail["status_barang"]; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="row row-cols-2">
                                    <div class="col">

                                    </div>
                                    <div class="col">
                                        <button class="container">Konfirmasi Pembayaran</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-2">
        <div class="container-fluid bg-light">
            <div class="row row-cols-1 me-5 pe-5 mt-3 rounded pt-1 ms-5 ps-5">
                <div class="col pt-3 pb-2 mb-2" style=" background-color: #D4DBE2;">
                    <h5 style="font-weight: 400;">Pesanan Terbaru</h4>
                </div>
                <!-- TABLE PESANAN TERBARU -->
                <table class="table table-hover table-bordered">
                    <thead class="" style="background-color:  #778899; color: #f5f5f5;">
                        <tr class="text-center">
                            <th>No.</th>
                            <th>Kode Pesanan</th>
                            <th>Nama Penerima</th>
                            <th>Tanggal Pemesanan</th>
                            <th>Total Pesanan</th>
                            <th class="text-center">Bukti Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $number = 1;
                        foreach ($_5pesananTerbaru as $order) : ?>
                            <tr class="text-center">
                                <td><?= $number++; ?></td>
                                <td><?= $order["no_pesanan"]; ?></td>
                                <td><?= $order["nama_penerima"]; ?></td>
                                <td><?= $order["tgl_pemesanan"]; ?></td>
                                <td>Rp<?= $order["FORMAT(total_pesanan, 2)"]; ?></td>
                                <td class="text-center"><?php if ($order["bukti"] == NULL) {
                                                            echo "<span style='color: red;'>Not yet uploaded <img class='img-fluid' src='img-admin/cancel.png'></span>";
                                                        } else {
                                                            # code...
                                                            echo "<span style='color: green;font-weight:500;'>Uploaded <img class='img-fluid' src='img-admin/correct.png'></span>       <a class='' target='_blank' href='../buktiPembayaran.php?bukti=$order[bukti]'>Lihat bukti</a>";
                                                        } ?></td>
                                <td>
                                    <!-- BUTTON PESANAN -->
                                    <button onclick="
                                    var id_pesanan = <?= $order['id_pesanan']; ?>;
                                    window.location = '?id_pesanan='+id_pesanan;">Lihat detail <img src="img-admin/info.png" alt=""></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="display-overview container bg-white rounded-3 border">
        <div class="ms-5 ps-5 pe-5 me-5 text-center pt-3 pb-2">
            <h4>Overview Produk</h4>
        </div>
        <!-- OVERVIEW PRODUK -->
        <div class="row row-cols-1 ms-5 me-5 ps-5 pe-5">
            <div class="col d-flex justify-content-center">
                <div id="carouselExampleInterval" class="carousel slide" data-bs-ride="carousel">
                    <div class="me-5 pe-5">
                        <button class="carousel-control-prev me-5 pe-5" type="button" data-bs-target="#carouselExampleInterval" data-bs-slide="prev">
                            <img src="img-admin/previous.png" alt="">
                        </button>
                    </div>
                    <div class="carousel-inner" style="width: 300px;height: 400px;">
                        <div class=" carousel-item active" data-bs-interval="2000">
                            <img class="img-fluid" src="../../img-assets/product/<?= $imgProduk["hero_img"]; ?>" alt="...">
                        </div>
                        <div class="carousel-item" data-bs-interval="2500">
                            <img class="img-fluid" src="../../img-assets/product/<?= $imgProduk["second_img"]; ?>" alt="...">
                        </div>
                        <div class="carousel-item" data-bs-interval="2500">
                            <img class="img-fluid" src="../../img-assets/product/<?= $imgProduk["third_img"]; ?>" alt="...">
                        </div>
                        <div class="carousel-item" data-bs-interval="2500">
                            <img class="img-fluid" src="../../img-assets/product/<?= $imgProduk["features_1_img"]; ?>" alt="...">
                        </div>
                        <div class="carousel-item" data-bs-interval="2500">
                            <img class="img-fluid" src="../../img-assets/product/<?= $imgProduk["features_2_img"]; ?>" alt="...">
                        </div>
                        <div class="carousel-item" data-bs-interval="2500">
                            <img class="img-fluid" src="../../img-assets/product/<?= $imgProduk["features_3_img"]; ?>" alt="...">
                        </div>
                    </div>
                    <div class="ms-5 ps-5">
                        <button class="carousel-control-next ms-5 ps-5" type="button" data-bs-target="#carouselExampleInterval" data-bs-slide="next">
                            <img src="img-admin/next.png" alt="">
                        </button>
                    </div>
                </div>
            </div>
            <div class="col mt-3">
                <div class="row row-cols-2 text-center mb-3 bg-light">
                    <div class="col border pt-3">
                        <h5><?= @$produkTerbaru["nama"]; ?></h5>
                        <h5 class="mt-3">Varian Tersedia</h5>
                        <p><?= @$produkVarianTerbaru["varian_1"]; ?>, <?= @$produkVarianTerbaru["varian_2"]; ?>, <?= @$produkVarianTerbaru["varian_3"]; ?></p>
                    </div>
                    <div class="col border pt-3">
                        <h5>Harga</h5>
                        <h4 class="mt-4">Rp<?= @$produkTerbaru["FORMAT(harga, 2)"]; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-3 mb-5">
        <div class="container-fluid bg-light">
            <div class="row row-cols-1 me-5 pe-5 mt-3 rounded pt-1 ms-5 ps-5">
                <div class="col pt-3 pb-2 mb-2" style=" background-color: #D4DBE2;">
                    <h5 style="font-weight: 400;">Produk Terbaru</h4>
                </div>
                <!-- TABLE PRODUK TERBARU -->
                <table class="table table-hover table-bordered">
                    <thead class="text-white" style="background-color: dimgrey;">
                        <tr class="text-center">
                            <th>No.</th>
                            <th>Preview</th>
                            <th>Nama Produk</th>
                            <th>Varian</th>
                            <th>Harga</th>
                            <th>Detail Produk</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $number = 1;
                        foreach ($_5produkTerbaru as $product) : ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center" style="height: 300px;">
                                        <?= $number++; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php $gambar = showDataTable("SELECT * FROM produk_image WHERE id_produk=$product[id_produk]");
                                    // echo $gambar["hero_img"];
                                    ?>
                                    <img width="275px" height="300px" src="../../img-assets/product/<?= $gambar["hero_img"]; ?>" alt="">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center" style="height: 300px;">
                                        <span><?= $product["nama"]; ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php $varianProduk = showDataTable("SELECT * FROM produk_varian WHERE id_produk=$product[id_produk]");
                                    ?>
                                    <div class="d-flex align-items-center justify-content-center" style="height: 300px;">
                                        <?php echo @$varianProduk['varian_1'] . ", " . @$varianProduk['varian_2'] . ", " . @$varianProduk['varian_3']; ?>
                                    </div>
                                </td>

                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center" style="height: 300px;">
                                        Rp<?= $product["FORMAT(harga, 2)"]; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center" style="height: 300px;">
                                        <!-- BUTTON PRODUK -->
                                        <button onclick="
                                        var id_produk = <?= $product['id_produk']; ?>;
                                        window.location = '?id_produk='+id_produk;
                                        ">Detail <img src="img-admin/info.png" alt=""></button>
                                        ||
                                        <button onclick="
                                        var id_produk2 = <?= $product['id_produk']; ?>;
                                        window.location = '?delete='+id_produk2;
                                        alert('Produk berhasil dihapus!');
                                        window.location.reload();
                                        ">Delete <img src="img-admin/trash (1).png" alt=""></button>
                                        ||
                                        <button>Ubah <img src="img-admin/exchange.png" alt=""></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <footer class="fixed-bottom">
        <div class="miniFooter container-fluid text-center text-white pt-2 pb-2" style="background-color: #7895B2;">
            <span>© 2022 Raget bags. All Rights reserved. Ecommerce software by Kaftapus2 </span>
        </div>
    </footer>
    <!-- LINK JS AND POPPER -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous">
    </script>
</body>

</html>