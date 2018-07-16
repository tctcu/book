<?php
/**
 * Prpcrypt class
 *
 * �ṩ���պ����͸�����ƽ̨��Ϣ�ļӽ��ܽӿ�.
 */
class Wechat_Prpcrypt
{
    public $key;

    function Wechat_Prpcrypt($k)
    {
        $this->key = base64_decode($k . "=");
    }

    /**
     * �����Ľ��м���
     * @param string $text ��Ҫ���ܵ�����
     * @return string ���ܺ������
     */
    public function encrypt($text, $appid)
    {

        try {
            //���16λ����ַ�������䵽����֮ǰ
            $random = $this->getRandomStr();
            $text = $random . pack("N", strlen($text)) . $text . $appid;
            // �����ֽ���
            $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            $iv = substr($this->key, 0, 16);
            //ʹ���Զ������䷽ʽ�����Ľ��в�λ���
            $pkc_encoder = new Wechat_Pkcs7Encoder;
            $text = $pkc_encoder->encode($text);
            mcrypt_generic_init($module, $this->key, $iv);
            //����
            $encrypted = mcrypt_generic($module, $text);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);

            //print(base64_encode($encrypted));
            //ʹ��BASE64�Լ��ܺ���ַ������б���
            return array(Wechat_ErrorCode::$OK, base64_encode($encrypted));
        } catch (Exception $e) {
            //print $e;
            return array(Wechat_ErrorCode::$EncryptAESError, null);
        }
    }

    /**
     * �����Ľ��н���
     * @param string $encrypted ��Ҫ���ܵ�����
     * @return string ���ܵõ�������
     */
    public function decrypt($encrypted, $appid)
    {

        try {
            //ʹ��BASE64����Ҫ���ܵ��ַ������н���
            $ciphertext_dec = base64_decode($encrypted);
            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            $iv = substr($this->key, 0, 16);
            mcrypt_generic_init($module, $this->key, $iv);

            //����
            $decrypted = mdecrypt_generic($module, $ciphertext_dec);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);
        } catch (Exception $e) {
            return array(Wechat_ErrorCode::$DecryptAESError, null);
        }


        try {
            //ȥ����λ�ַ�
            $pkc_encoder = new Wechat_Pkcs7Encoder;
            $result = $pkc_encoder->decode($decrypted);
            //ȥ��16λ����ַ���,�����ֽ����AppId
            if (strlen($result) < 16)
                return "";
            $content = substr($result, 16, strlen($result));
            $len_list = unpack("N", substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $from_appid = substr($content, $xml_len + 4);
        } catch (Exception $e) {
            //print $e;
            return array(Wechat_ErrorCode::$IllegalBuffer, null);
        }
        if ($from_appid != $appid)
            return array(Wechat_ErrorCode::$ValidateAppidError, null);
        return array(0, $xml_content);

    }


    /**
     * �������16λ�ַ���
     * @return string ���ɵ��ַ���
     */
    function getRandomStr()
    {

        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }

}

?>