<?php
  $post = $_POST;
  header('Content-type: application/json');

  if(!empty($post))
  {
    $name    = $post['name'];
    $kana    = $post['kana'];
    $email   = $post['email'];
    $tel     = $post['tel'];
    $contexts = $post['contexts'];

    $error = array();
    if(empty($name))
    {
      $error += array('name' => '名前を入力してください。');
    }

    if(empty($kana))
    {
      $error += array('kana' => 'フリガナを入力してください。');
    }

    if(empty($email))
    {
      $error += array('email' => 'メールアドレスを入力してください。');
    }
    else if(false == filter_var($email, FILTER_VALIDATE_EMAIL) && ']' !== substr($email, -1))
    {
      $error += array('email' => 'メールアドレスを正しい形式で入力してください。');
    }

    if(empty($tel))
    {
      $error += array('tel' => '電話番号を入力してください。');
    }
    else if(is_string($tel) && preg_match('/\A\d{2,4}+-\d{2,4}+-\d{4}\z/', $tel))
    {
      $error += array('tel' => '電話番号を正しい形式ハイフン無しで入力してください。');
    }

    if(empty($contexts))
    {
      $error += array('contexts' => 'お問い合わせ内容を入力してください。');
    }

    if(empty($error))
    {
      mb_language("japanese");
      mb_internal_encoding("UTF-8");

      require_once ( './lib/PHPMailer/PHPMailerAutoload.php' );

      $mail = new PHPMailer;
      $mail->SMTPOptions = array(
        'ssl' => array(
          'verify_peer' => false,
          'verify_peer_name' => false,
          'allow_self_signed' => true
        )
      );
      $mail -> isSMTP();
      $mail -> Host       = "";
      $mail -> Username   = "";
      $mail -> Password   = "";
      $mail -> SMTPSecure = "tls";
      $mail -> Port       = 587;
      $mail -> SMTPAuth   = true;
      // $mail -> SMTPDebug  = 3;
      $mail -> SMTPDebug  = false;
      $mail -> do_debug   = 0;
      $mail -> From       = ""; // 送信元

      $mail -> addAddress("", "");
      $mail -> isHTML(false);

      $body = '';
      $body .= 'HPからお問い合わせがありました。'. "\n\n";
      $body .= '【お問い合わせ内容】'. "\n";
      $body .= '名前： '. $name. "\n";
      $body .= 'フリガナ:  '. $kana. "\n";
      $body .= 'メールアドレス： '. $email. "\n";
      $body .= '電話番号： '. $tel. "\n";
      $body .= 'お問い合わせ内容： '. $contexts. "\n\n";

      $mail -> Subject = mb_encode_mimeheader("HPからのお問い合わせ", "ISO-2022-JP", "UTF-8");
      $mail -> Body = mb_convert_encoding($body, "UTF-8", "auto");

      if(!$mail -> send())
      {
          $res['status'] = 'sendError';
          $res['errors'] = $mail->ErrorInfo;
          echo json_encode($res);
      }
      else
      {
          $res['status'] = 'success';
          echo json_encode($res);
      }
    }
    else
    {
      $res['status'] = 'error';
      $res['errors'] = $error;
      echo json_encode($res);
    }
  }
?>
