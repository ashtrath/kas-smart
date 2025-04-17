<?php

return [
    'user' => [
        'title' => 'Karyawan',
        'search_placeholder' => 'Cari (nama lengkap, email)',

        'name' => 'Nama Lengkap',
        'role' => 'Role',
        'email' => 'Email',
        'password' => 'Kata Sandi',
        'password_confirmation' => 'Konfirmasi Kata Sandi',
    ],

    'category' => [
        'title' => 'Kategori',
        'search_placeholder' => 'Cari (nama kategori)',

        'name' => 'Nama Kategori',
        'description' => 'Deskripsi',
    ],

    'product' => [
        'title' => 'Produk',
        'search_placeholder' => 'Cari (nama produk, kategori)',

        'name' => 'Nama Produk',
        'description' => 'Deskripsi',
        'price' => 'Harga',
        'stock' => 'Stok',
        'image' => 'Gambar',
        'category_id' => 'Kategori',
        'is_visible' => [
            'label' => 'Tampilkan Produk?',
            'true' => 'Produk ini akan ditampilkan pada halaman kasir.',
            'false' => 'Produk ini tidak akan ditampilkan pada halaman kasir.',
        ],

        'section' => [
            'image' => 'Gambar',
            'inventory' => 'Inventaris',
        ],

        'widget' => [
            'total_products' => 'Total Produk',
            'total_inventory' => 'Total Inventaris',
            'average_price' => 'Rata-Rata Harga',
        ]
    ],

    'payment_method' => [
        'title' => 'Metode Pembayaran',
        'search_placeholder' => 'Cari (nama metode pembayaran)',

        'name' => 'Nama Metode Pembayaran',
        'type' => 'Tipe',
    ],

    'created_at' => 'Dibuat pada',
    'updated_at' => 'Terakhir diubah pada',
];
