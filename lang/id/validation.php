<?php

return [
    'required' => ':attribute wajib diisi.',
    'string' => ':attribute harus berupa teks.',
    'max' => [
        'string' => ':attribute tidak boleh lebih dari :max karakter.',
        'file' => ':attribute tidak boleh lebih dari :max kilobyte.',
    ],
    'min' => [
        'string' => ':attribute harus memiliki minimal :min karakter.',
    ],
    'unique' => ':attribute sudah terdaftar.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'in' => ':attribute yang dipilih tidak valid.',
    'image' => ':attribute harus berupa gambar.',
    'mimes' => ':attribute harus berformat: :values.',
    'nullable' => ':attribute boleh kosong.',
    'exists' => ':attribute yang dipilih tidak ditemukan.',
    'boolean' => ':attribute harus bernilai benar atau salah.',
    'date' => ':attribute harus berupa tanggal yang valid.',
    'integer' => ':attribute harus berupa angka bulat.',
    'numeric' => ':attribute harus berupa angka.',

    'attributes' => [
        'equipment_code' => 'Kode Peralatan',
        'equipment_name' => 'Nama Peralatan',
        'status' => 'Status',
        'description' => 'Deskripsi',
        'photo' => 'Foto',
        'email' => 'Alamat Email',
        'password' => 'Kata Sandi',
        'name' => 'Nama',
        'username' => 'Nama Pengguna',
        'full_name' => 'Nama Lengkap',
    ],
];
