<?php
/****************************************************
*         Author: ShenLu  
*          Email: lusknight@gmail.com
*  Last modified: 2012-05-17 14:16
*       Filename: mail.php
*    Description: 用户找回密码发送邮件的类
****************************************************/

class SendSmtpMail {

    /**
     * 发送邮件的地址
     */
    private $sendFrom;

    /**
     * smtp服务器端口
     */
    private $smtpPort;

    /**
     * smtp 服务器地址
     */
    private $hostName;

    /**
     * 邮箱发送服务密码
     */
    private $password;

    /**
     * smtp 服务器是否需要验证
     */
    private $authLogin;

    /**
     * socket过期时间
     */
    private $timeOut;

    /**
     * 邮件发送的header
     */
    private $header;

    /**
     * 日志文件的名称
     */
    private $logFile;

    /**
     * PHP中socket套接字的资源标示符
     */
    private $socket;

    public function __construct($user, $password, $smtpName = '', $port = 25, $time = 30, $auth = true) {

        /**
         * e.g $smtpName = 'smtp.163.com'
         */
        $this->sendFrom = $user;
        $this->smtpPort = $port;
        $this->hostName = $smtpName;
        $this->password = $password; 
        $this->authLogin = $auth;
        $this->timeOut = $time;
        $this->logFile = __DIR__ . DIRECTORY_SEPARATOR . "mailLog.txt";
    }

    /**
     * 发送邮件的函数
     * 给其他用户调用的发邮件的接口
     */
    public function sendMail($from, $to, $subject = '', $body = 'hello world!') {

        $this->header = "MIME-Version:1.0\r\n";
        $this->header .= "Content-Type:text/plain;charset=utf-8\r\n";
        $this->header .= "To: " . $to . "\r\n";
        $this->header .= "From: $from<" . $from . ">\r\n";
        $this->header .= "Subject: " . $subject . "\r\n";

        try {
            $this->buildSocket();
            $this->smtpSend($this->hostName, $from, $to, $body);
        } catch (Exception $e) {
            $this->mailLog($e->getMessage());
        }
    }

    /**
     * 用于和SMTP协议的命令行交互
     * 即发送命令
     * 接收返回的消息
     */
    private function smtpSend($hello, $from, $to, $body) {

        // 接收连接时的字符串传输
        fgets($this->socket, 512);
        // 发送命令开始
        try {
            $this->smtpCommand('HELO', $hello);
            if($this->authLogin) {
                $this->smtpCommand('auth login');
                $this->smtpCommand('', base64_encode($this->sendFrom));
                $this->smtpCommand('', base64_encode($this->password));
            }
            $this->smtpCommand('MAIL', "FROM:<" . $from . ">");
            $this->smtpCommand('RCPT', "TO:<" . $to . ">");
            $this->smtpCommand('DATA');
            $this->smtpMessage($body);
            //file_put_contents('.\maillogs', $body);
            $this->smtpCommand('QUIT');

        } catch (Exception $e) {
            $this->mailLog($e->getMessage());
        }

        return TRUE;
    } 

    /**
     * 利用telnet的smtp服务的命令进行交互
     * 一次调用，即一次命令
     * HELO、auth login、 user、 password、 RCPT、 DATA
     */
    private function smtpCommand($command, $params = '') {

        if($params) {
            if($command) {
                $command = $command . ' '  . $params;
            } else {
                $command = $params;
            }
        }
        if(!fputs($this->socket, $command . "\r\n")) {
            $error = $command . " command is wrong";
            throw new Exception($error);
        }
        try {
            $this->smtpResponse();
        } catch (Exception $e) {
            $this->mailLog($e->getMessage());
        }
    }

    /**
     * 处理和smtp的交互过程中smtp的响应结果
     */
    private function smtpResponse() {

        $response = str_replace("\r\n", '',fgets($this->socket, 512));
        if(!preg_match('/^[2,3]/', $response)) {
            fputs($this->socket, "QUIT\r\n");
            fgets($this->socket, 512);
            $error = "Error:the receive response is wrong" . $response;
            throw new Exception($error);
        }

        return true;
    }

    /**
     * 发送消息内容
     */
    private function smtpMessage($body){

        fputs($this->socket, $this->header . "\r\n" . $body);
        // 结束符
        fputs($this->socket, "\r\n.\r\n");
        return TRUE;
    }
    

    /**
     * 建立socket连接
     */
    private function buildSocket() {

        $this->socket = @fsockopen($this->hostName, $this->smtpPort, $errno, $errstr, $this->timeOut);
        if (!$this->socket) {
            // have something wrong in the socket
            $error = "can't connect the host" . $this->hostName . " " . $errno . " " . $errstr;
            throw new Exception($error);
        }
        return TRUE;
    }

    /**
     * 邮件发送错误日志
     */
    private function mailLog($exception) {

        $message = date("M d H:i:s ") . get_current_user() . "[" . getmypid() . "]: " . $exception . "\n";
        file_put_contents($this->logFile, $message, FILE_APPEND);
        return TRUE;
    }
}
