## Start project
Project có sẵn vendor của composer nên có thể bạn không cần chạy lệnh composer install
<br/>1: phân quyền đọc, ghi cho thư mục: storage và bootstrap/cache
<br/>2: cài virtual host: backend.com trỏ đến public (xem file config nginx tại  _data/webconfig/htaccess.macos.conf)
<b3/>: Đăng nhập với tài khoản: sakura/123456 Tài khoản root

## Định nghĩa quyền trong code
trong file: app/Http/Models/Member.php
<br/> Ví dụ : const mng_car="mng_car"
định nghĩa thêm phần config quyên thì xem function Member::getListRole()
<br/>
Gọi check quyền như sau: if(Member:haveRole(Member::mng_car)){}
<br/>

##code 
nằm trong app/Http/Controllers 
