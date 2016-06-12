Author: Parama Fadli Kurnia

Cart API:
spesifikasi:
- ada addItem  ke cart
- ada removeItem dari cart
- ada checkout untuk membayar item yang dibeli beserta penerapan kupon diskon
- fitur mengurangi stok barang saat add item ke cart
- fitur mengembalikan stok barang saat remove item ke cart
- notifikasi item apa saja yang baru di-add dan di-remove
- notifikasi berapa jumlah item yang sudah ada di cart dan total harganya
- penerapan API Key
- pengecekan transaksi sudah dibayar atau belum
- terdeploy di cloud infra, di server Centos 6.7 x64 di domain url rest.labanian.com

Code API
folder project ini berisi semua code untuk API cart yang dikerjakan dengan Codeigniter, 
yang controllernya berisi:

1. Add item
untuk menambahkan item pada cart dengan parameter:
'apikey' => "DKpsPzAYK5utMJcaIwlMWTYx|1659864696117076"
$data = array(
    'id_transaction' => 'AXBD123',
    'id_item' => '2',
    'total_item' => '1'
);

cara debug:
- online
http://rest.labanian.com/cart/addItem/AXBD123/2/2?apikey=DKpsPzAYK5utMJcaIwlMWTYx|1659864696117076
- local
http://localhost/cart-api/cart/addItem/AXBD123/2/2?apikey=DKpsPzAYK5utMJcaIwlMWTYx|1659864696117076

2. Remove Item
untuk menghapus item pada cart dengan parameter:
'apikey' => "DKpsPzAYK5utMJcaIwlMWTYx|1659864696117076"
$data = array(
    'id_transaction' => 'AXBD123',
    'id_item' => '2'
);
cara debug:
- online
http://rest.labanian.com/cart/removeItem/AXBD123/2?apikey=DKpsPzAYK5utMJcaIwlMWTYx|1659864696117076
- local
http://localhost/cart-api/cart/removeItem/AXBD123/2?apikey=DKpsPzAYK5utMJcaIwlMWTYx|1659864696117076

3. checkout
untuk membayar semua item yang telah dibeli beseta menerapkan kupon diskon dengan parameter:
'apikey' => "DKpsPzAYK5utMJcaIwlMWTYx|1659864696117076"
$data = array(
    'id_transaction' => 'AXBD123',
    'coupon' => 'PROMO01'
);
cara debug:
- online
http://rest.labanian.com/cart/checkout/AXBD123/PROMOX1?apikey=DKpsPzAYK5utMJcaIwlMWTYx|1659864696117076
- local
http://localhost/cart-api/cart/checkout/AXBD123/PROMOX1?apikey=DKpsPzAYK5utMJcaIwlMWTYx|1659864696117076

Note:
cara debug bisa pakai url, curl, atau dengan php command
