<?php

class newApi
{
    private $ip;
    private $password;
    const BLOCK_SIZE = 16;

    public function __construct(string $ip, string $password)
    {
            $this->ip = $ip;
            $this->password = $password;
    }

    public function addPadding(string $data): string
    {
        $dataLength = strlen($data);
        $charsToAdd = ($dataLength + self::BLOCK_SIZE) - ($dataLength % self::BLOCK_SIZE) - $dataLength;
        $pad_string = str_pad("", $charsToAdd, "\x10");

        return implode("", [$data, $pad_string]);
    }

    public function decrypt(string $data, string $password) {
        $test = unpack($data);
        $iv = substr($data, 74, 32);
//        $passwordHash = crypto.createHash('sha256').update(toBytes(password)).digest().slice(0, 32),
//        randomBytes = data.slice(32, 48),
//        encryptedBody = data.slice(48, data.length),
//        aesDecryptor = new aesjs.ModeOfOperation.cbc(passwordHash, randomBytes);
//    return new TextDecoder().decode(aesDecryptor.decrypt(encryptedBody));
    }

    /**
     * @throws Exception
     */
    public function encrypt(string $data, string $password){
        $tocodedata = implode("", [$data, str_pad("",2,"\x00\x10")]);
        $str = mb_convert_encoding($this->addPadding($tocodedata), "UTF-8");
        $pop = json_encode($this->addPadding($tocodedata));
        $iv = random_bytes(16);
        $ret = json_decode($pop);
        $c = utf8_encode($this->addPadding($tocodedata));
//        $c = mb_convert_encoding("\u0010\u0010", "UTF-8", "UNICODE");
//        $utf8string = html_entity_decode(preg_replace("/U\+([0-9A-F]{4})/", "&#x\\1;", $this->addPadding($tocodedata)), ENT_NOQUOTES, 'UTF-8');

//        $test = str_replace("\\u0000\\u0010\u0010\u0010\u0010\u0010\u0010\u0010\u0010\u0010\u0010\u0010\u0010\u0010","\x00\x10",$c);
        $lol = hash('sha256', $password, false);


        $test2 = bin2hex(hash_hmac('sha256',$lol,"",true));

//        $String = "Text";
        $Bytes = "";

        $Length = strlen($c);
        for ($i=0; $i<$Length; $i++) {
            $Bytes .= "\x".(string)dechex(mb_ord($c[$i]));
        }

        echo $Bytes;


//        body = JSON.stringify(body);
//        let passwordHash = crypto.createHash('sha256').update(toBytes(rb.password)).digest(),
//        randomBytes = crypto.randomBytes(16),
//        packedBody = toBytes(addPadding(body + "\x00\x10")),
//        hashedBody = crypto.createHash('sha256').update(toBytes(body)).digest(),
//        easEncryptor = new aesjs.ModeOfOperation.cbc(passwordHash, randomBytes),
//        encryptedBody = Buffer.from(easEncryptor.encrypt(packedBody));
//    return Buffer.concat([hashedBody, randomBytes, encryptedBody]);
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }



}