
<table style="width:100%; align-content:center; align:center;">
    <table align="center" width="600" style="align:center; text-align:center;  background-color:#e5e7eb; margin:auto; padding:20px;">
        <tr>
            <td>
                <img src="https://mijn.rietpanel.nl/public/storage/images/rietpanel_logo.png" height="70"  alt="Rietpanel logo"/>
            </td>
        </tr>
        <tr>
            <td>
                <h2>Your account has been updated!</h2><br/>
                @if($status)
                    Your account on my.rietpanel.com has been activated.<br/>
                    U can now login using https://my.rietpanel.com
                @else
                    “Your account on my.rietpanel.com has been deactivated.<br/>
                    You will no longer be able to log in. <br/>
                    If you believe this is a mistake, please contact us.
                @endif
                <br/><br/>
                Kind regards,<br/><br/>
                Team Rietpanel<br/><br/>
                <a href="mailto:info@rietpanel.nl">info@rietpanel.nl</a><br/>
                <a href="tel:0850290840">085-029 08 40</a>
            </td>
        </tr>

    </table>
</table>

</body>
</html>
