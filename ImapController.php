<?php
/**
 * Created by PhpStorm.
 * User: River
 * Date: 2019/3/28
 * Time: 17:38
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client as HttpClient;
use Webklex\IMAP\Client as ImapClient;

class ImapController
{
    private $current_account;
    private $current_password;
    private $container = [];
    private static $account_number = 10;

    public function __construct()
    {
        set_time_limit(0);
    }

    public function readEmail(HttpClient $httpClient)
    {
        while (true) {
            foreach ($this->createImapConnect() as $imapClient) {
                $Folder = $imapClient->getFolder('INBOX');
                $message_collection = $Folder->query()->from('noreply@processon.io')->get();
                foreach($message_collection as $message){
                    $body = $message->getHTMLBody();
                    Log::debug('email-body', [$body]);
                    if (!$body) {
                        continue;
                    }
                    echo 'Current-Account ：'.$this->current_account.'<br />';
                    echo $message->getSubject().'<br />';
                    //echo 'Attachments: '.$message->getAttachments()->count().'<br />';
                    //echo $message->getHTMLBody();
                    //preg_match('/http:\/\/sctrack\.sc\.gg\/track\/click\/[0-9a-zA-Z]+\.html/', $body, $matches);
                    preg_match('/https:\/\/www\.processon\.com\/signup\/verification\/[0-9a-z]+_?[0-9a-z]*/', $body, $matches);
                    $processon_register_url = $matches[0];

                    $response = $httpClient->request('GET', $processon_register_url, ['verify'=>false]);
                    echo $processon_register_url.' 激活 ：'.$response->getStatusCode().'<br />'; # 200
                    if ($response->getStatusCode() == 200) {
                        $this->container[] = $this->current_password;
                    }
                    //echo $response->getBody();
                }
            }
            if (count($this->container) == self::$account_number) {
                exit('账户全部激活');
            }
        }

        /* Alternative by using the Facade
        $oClient = Webklex\IMAP\Facades\Client::account('default');
        */
    }

    /** 创建 Imap 连接
     *
     * @return \Generator
     */
    private function createImapConnect()
    {
        $options = [
            'host'          => 'imap.mxhichina.com',
            'port'          => 993,
            'encryption'    => 'ssl',
            'validate_cert' => true,
            'username'      => 'xxx@xxx.com',
            'password'      => 'xxx',
            'protocol'      => 'imap'
        ];
        for ($i=1; $i <= self::$account_number; $i++) {
            $i = str_pad($i, 3, 0, STR_PAD_LEFT);
            $username = 'river_'.$i.'@hsf88.cn';
            $password = 'River'.$i;
            $this->current_account = $username;
            $this->current_password = $password;
            if (in_array($this->current_password, $this->container)) {
                continue;
            }
            $options['username'] = $username;
            $options['password'] = $password;

            $oClient = new ImapClient($options);
            yield $oClient->connect();
        }
    }
}