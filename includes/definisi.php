<?php
$defs = [
 'barang'=>['title'=>'Data Barang','table'=>'barang','pk'=>'idbarang','fields'=>[
  'kodebarang'=>['Kode Barang','text',true], 'namabarang'=>['Nama Barang','text',true], 'idkategori'=>['Kategori','select',true], 'idpenyuplai'=>['Penyuplai','select',true], 'idmerek'=>['Merek','select',true], 'stok'=>['Stok Awal','number',true], 'satuan'=>['Satuan','text',true]],
  'headers'=>['idbarang'=>'ID','kodebarang'=>'Kode Barang','namabarang'=>'Nama Barang','namakategori'=>'Kategori','namapenyuplai'=>'Penyuplai','namamerek'=>'Merek','stok'=>'Stok','satuan'=>'Satuan']],
 'barangmasuk'=>['title'=>'Barang Masuk','table'=>'barangmasuk','pk'=>'idbarangmasuk','fields'=>[
  'idbarang'=>['Barang','select',true], 'iduser'=>['Petugas','select',true], 'tanggalmasuk'=>['Tanggal Masuk','date',true], 'jumlah'=>['Jumlah','number',true], 'keterangan'=>['Keterangan','text',true]],
  'headers'=>['idbarangmasuk'=>'ID','namabarang'=>'Barang','namauser'=>'Petugas','tanggalmasuk'=>'Tanggal','jumlah'=>'Jumlah','keterangan'=>'Keterangan']],
 'kategori'=>['title'=>'Kategori','table'=>'kategori','pk'=>'idkategori','fields'=>['namakategori'=>['Nama Kategori','text',true],'keterangan'=>['Keterangan','text',true]],'headers'=>['idkategori'=>'ID','namakategori'=>'Nama Kategori','keterangan'=>'Keterangan']],
 'merek'=>['title'=>'Merek','table'=>'merek','pk'=>'idmerek','fields'=>['namamerek'=>['Nama Merek','text',true],'keterangan'=>['Keterangan','text',true]],'headers'=>['idmerek'=>'ID','namamerek'=>'Nama Merek','keterangan'=>'Keterangan']],
 'penyuplai'=>['title'=>'Penyuplai','table'=>'penyuplai','pk'=>'idpenyuplai','fields'=>['namapenyuplai'=>['Nama Penyuplai','text',true],'nohp'=>['Nomor HP','text',true],'alamat'=>['Alamat','text',true]],'headers'=>['idpenyuplai'=>'ID','namapenyuplai'=>'Nama Penyuplai','nohp'=>'Nomor HP','alamat'=>'Alamat']],
 'user'=>['title'=>'Pengguna','table'=>'user','pk'=>'iduser','fields'=>['namauser'=>['Nama Lengkap','text',true],'username'=>['Username','text',true],'password'=>['Password','password',false],'nohp'=>['Nomor HP','text',true],'Alamat'=>['Alamat','text',true]],'headers'=>['iduser'=>'ID','namauser'=>'Nama','username'=>'Username','nohp'=>'Nomor HP','Alamat'=>'Alamat']]
];
