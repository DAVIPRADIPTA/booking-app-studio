# Studio Booking System

Sistem manajemen reservasi studio yang dirancang untuk mengelola ketersediaan slot waktu secara real-time dan mencegah tumpang tindih (double-booking).

## Fitur Utama

* **Automated Scheduling:** Manajemen slot waktu otomatis berdasarkan jam operasional studio.
* **Conflict Prevention:** Logika validasi ketat untuk memastikan tidak ada jadwal bentrok pada jam yang sama.
* **User Dashboard:** Antarmuka bagi pelanggan untuk memantau status pesanan mereka secara transparan.
* **Admin Management:** Kendali penuh bagi pengelola studio untuk mengatur resource dan jadwal secara efisien.

## Tech Stack

* **Framework:** Laravel 10
* **Frontend:** Blade Templating, Tailwind CSS
* **Database:** MySQL / MariaDB
* **Validation:** Custom Request Validation untuk logika booking yang kompleks.

## Highlight Teknis

Dalam proyek ini, saya memecahkan masalah kompleks dalam sinkronisasi waktu antara zona waktu server dan input user. Selain itu, saya mengimplementasikan database indexing pada kolom waktu untuk memastikan pencarian ketersediaan jadwal tetap cepat meskipun data dalam jumlah besar.
