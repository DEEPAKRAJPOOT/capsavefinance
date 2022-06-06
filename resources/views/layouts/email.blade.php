<table class="wrapper" width="100%;" cellpadding="0" cellspacing="0" style="font-family: Calibri !important; font-size: 0.917rem; box-sizing: border-box; background-color: #efefef; margin: 0 auto; padding: 20px; width:100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
    <tr>
        <td class="header" style=" text-align: center;">
            <a href="#">
                <img src="{{ asset('frontend/assets/images/logo.png') }}"  alt="Capsave" width="200px;" />
            </a>
        </td>
    </tr>
    <tr>
        <td class="header">
            @yield('email_content')
        </td>
    </tr>
    <tr>
        <td style="font-family: Calibri !important; font-size: 0.917rem; box-sizing: border-box;">
            <table class="footer" align="center" width="100%" cellpadding="0" cellspacing="0" style="font-family: Calibri !important; font-size: 0.917rem; box-sizing: border-box; margin: 0 auto; padding: 0; text-align: center; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                <tr>
                    <td class="content-cell" align="center" style="font-family: Calibri !important; font-size: 0.917rem; box-sizing: border-box; padding: 25px;">
                        <p style="font-family: Calibri !important; font-size: 0.917rem; box-sizing: border-box; line-height: 1.5em; margin-top: 0; color: #082629; font-size: 12px; text-align: center;">© {{ now()->year }} Capsave. All rights reserved.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>


