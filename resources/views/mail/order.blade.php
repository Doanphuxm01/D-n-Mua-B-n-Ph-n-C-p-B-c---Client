<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org=/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body style="height: 100%;     background-color: #eaebed;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" data-mobile="true" dir="ltr" align="center"  style="text-align: justify;color:#353535;background-color: #eaebed;font-family: arial,sans-serif;padding: 30px 0px;line-height: 24px; font-size: 14px">
    <tbody>
    <tr>
        <td align="center" valign="" style="padding:0;margin:0;">
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="640" style="border: 1px solid #DDDDDD;background: #FFF;padding:20px; border-bottom: 3px solid #ff9600">
                <tbody>
                <tr>
                    <td colspan="2" >
                        Xin chào, {{@$order['fullname']}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="color: #53a11b; font-size: 20px; padding: 10px 0">Đặt hàng thành công</td>
                </tr>
                <tr>
                    <td colspan="2">MinhPhucGroup rất vui lòng xin thông báo đơn hàng <b style="color: #10b710">#{{$order['code']}}</b> của quý khách đã được tiếp nhận và đang trong quá trình xử lý</td>
                </tr>
                <tr>
                    <td style="width: 50%; padding-right: 15px; height: 100%; font-size: 12px; padding-top: 15px">
                        <div style="border: 1px solid #d1d1d1; border-radius: 5px; padding: 10px; height: 210px;">
                            <div style="font-weight: bold; padding: 10px 0; border-bottom: 1px solid #d1d1d1;">Địa chỉ nhận hàng</div>
                            <div>
                                <span style="font-weight: bold">Họ và tên: </span> {{$order['fullname']}}
                            </div>
                            <div>
                                <span style="font-weight: bold">Địa chỉ: </span> {{ $order['street'] ? $order['street']. ', ' : ''}} {{$order['town']['name']}} - {{$order['district']['name'].' - '.$order['city']['name']}}
                            </div>
                            <div>
                                <span style="font-weight: bold">Điện thoại: </span> {{$order['phone']}}
                            </div>
                            <div>
                                <span style="font-weight: bold">Ghi chú: </span> {{$order['note']}}
                            </div>
                        </div>
                    </td>
                    <td style="width: 50%; padding-left: 15px; height: 100%; font-size: 12px; padding-top: 15px">
                        <div style="border: 1px solid #d1d1d1; border-radius: 5px; padding: 10px; height: 210px">
                            <div style="font-weight: bold; padding: 10px 0; border-bottom: 1px solid #d1d1d1;">Hình thức thanh toán</div>

                            <b style="color: #10b710">Thanh toán bằng ví tiêu dùng</b><br>
                            <div>
                                <b>Trạng thái:</b> <span style="color: #10b710">Đơn mới</span>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center"><a href="{{route('FeCart',['action_name' => 'success', 'token' => @$order['token_tracking']])}}" style="color: #fff; background-color: #008aff; border-radius: 18px; line-height: 36px ; display: inline-block; margin: 25px auto 50px; padding: 0 25px; text-decoration: none">Xem chi tiết đơn hàng</a></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr class="copyright">
        <td align="center">
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="640" style="text-align: center">
                <tr style="color: #464646;text-align: center; font-weight: bold">
                    <td style="padding-top: 10px">
                        Copyright © {{ date('Y') }} - MinhPhucGroup
                    </td>
                </tr>
                <tr style="color: #8b8b8b">
                    <td>
                        BT2 O3, KĐT Văn Khê, Phường La Khê, QUận Hà Đông, TP. Hà Nội.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</td>
</tr>
</tbody>
</table>

<style>
    *{
        padding: 0;
        margin: 0;
    }
</style>



</body>
</html>