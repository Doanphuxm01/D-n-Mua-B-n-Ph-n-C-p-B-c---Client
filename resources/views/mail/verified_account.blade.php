<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width"/>
    @include('mail.includes._style')
</head>
<body>
<table class="body-wrap">
    <tr>
        <td class="container">

            <!-- Message start -->
            <table>
                <tr>
                    <td align="center" class="masthead">

                        <h1>Ứng dụng MinhPhucGroup</h1>

                    </td>
                </tr>
                <tr>
                    <td class="content">

                        <h2>Xin chào </h2>

                        <p>Email này được gửi tự động từ hệ thống phần mềm MinhPhucGroup, gửi cho bạn về việc xác thực tài
                            khoản trên hệ thống MinhPhucGroup</p>
                        <p>
                            Đây là mã xác nhận của bạn: <b>{{$code}}</b>
                        </p>
                        <div>Nếu bạn cảm thấy phiền toái về những gì nhận được từ email này. Hãy gửi phản hồi đến chúng
                            tôi. Hoặc tắt chức năng thông báo qua email trong thông tin nhân sự của bạn.
                        </div>

                        <p><em>– MinhPhucGroup -</em></p>

                    </td>
                </tr>
            </table>

        </td>
    </tr>
    @include('mail.includes._footer')
</table>
</body>
</html>