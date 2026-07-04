<?php

namespace common\commands\handler;

use Yii;
use trntv\tactician\base\BaseHandler;
use yii\swiftmailer\Message;
//use yii\swiftmailer\Attachment;
require_once(Yii::getAlias("@vendor/swiftmailer/swiftmailer/lib/classes/Swift/Attachment.php"));

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class SendEmailHandler extends BaseHandler
{
    /**
     * @param \common\commands\command\SendEmailCommand $command
     * @return bool
     */
    public function handle($command)
    {
        if (!$command->body) {
            $message = \Yii::$app->mailer->compose($command->view, $command->params);
        } else {
            $message = new Message();
            if ($command->isHtml()) {
                $message->setHtmlBody($command->body);
            } else {
                $message->setTextBody($command->body);
            }
        }
        $message->setFrom($command->from);
        $message->setTo($command->to ?: \Yii::$app->params['robotEmail']);
        $message->setSubject($command->subject);
        
        if($command->attach){
            if(!is_array($command->attach)){
                $command->attach = [$command->attach];
            }
            
            if($command->attach){
                foreach($command->attach as $attach){
                    //для старых версий
                    /*$file = \Swift_Attachment::fromPath($attach);
                    $message->attach($file);*/
                    $message->attach($attach);
                }
            }
        }
        
        return $message->send();
    }
}
